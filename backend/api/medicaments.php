<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

// Permettre l'accès CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

try {
    $conn = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME,
        DB_USER,
        DB_PASSWORD
    );
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $action = $_GET['action'] ?? $_POST['action'] ?? null;

    /**
     * POST - Importer des médicaments depuis un fichier
     */
    if ($action === 'import' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Aucun fichier fourni']);
            exit;
        }

        $file = $_FILES['file'];
        $fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $tmpPath = $file['tmp_name'];
        $extractedData = [];

        try {
            // Traiter les fichiers CSV
            if ($fileExt === 'csv') {
                if (!file_exists($tmpPath)) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'error' => 'Fichier introuvable']);
                    exit;
                }

                $handle = fopen($tmpPath, 'r');
                if (!$handle) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'error' => 'Impossible de lire le fichier CSV']);
                    exit;
                }

                $header = null;
                $rowCount = 0;
                
                while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                    if ($header === null) {
                        $header = $row;
                        continue;
                    }
                    
                    $rowCount++;
                    if (!empty($row[0])) {
                        $extractedData[] = [
                            'nom' => trim($row[0] ?? ''),
                            'dosage' => trim($row[1] ?? ''),
                            'categorie' => trim($row[2] ?? ''),
                            'prix' => floatval($row[3] ?? 0),
                            'quantite' => intval($row[4] ?? 0)
                        ];
                    }
                }
                fclose($handle);

                if (!empty($extractedData)) {
                    http_response_code(200);
                    echo json_encode([
                        'success' => true,
                        'message' => count($extractedData) . ' médicament(s) trouvé(s)',
                        'data' => $extractedData[0],
                        'total' => count($extractedData)
                    ]);
                } else {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'error' => 'Aucune donnée valide trouvée dans le fichier CSV']);
                }
                exit;
            }
            // Traiter les fichiers Excel (.xlsx)
            else if ($fileExt === 'xlsx') {
                if (!class_exists('ZipArchive')) {
                    http_response_code(500);
                    echo json_encode(['success' => false, 'error' => 'Extension ZipArchive non disponible']);
                    exit;
                }

                $zip = new ZipArchive();
                $zipResult = $zip->open($tmpPath);
                
                if ($zipResult !== true) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'error' => 'Fichier Excel invalide']);
                    exit;
                }

                // Lire les chaînes partagées
                $sharedStrings = [];
                $stringsXml = $zip->getFromName('xl/sharedStrings.xml');
                
                if ($stringsXml !== false) {
                    libxml_use_internal_errors(true);
                    $stringsDoc = simplexml_load_string($stringsXml);
                    libxml_use_internal_errors(false);
                    
                    if ($stringsDoc !== false) {
                        // Récupérer tous les éléments 'si' sans namespace
                        foreach ($stringsDoc->children() as $si) {
                            // Chercher l'élément 't' dans les enfants
                            foreach ($si->children() as $t) {
                                if (strpos((string)$t->getName(), 't') !== false) {
                                    $sharedStrings[] = (string)$t;
                                    break;
                                }
                            }
                        }
                    }
                }

                // Lire la feuille Excel
                $xml = $zip->getFromName('xl/worksheets/sheet1.xml');
                $zip->close();
                
                if ($xml === false) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'error' => 'Impossible de lire les données Excel']);
                    exit;
                }

                libxml_use_internal_errors(true);
                $doc = simplexml_load_string($xml);
                libxml_use_internal_errors(false);
                
                if ($doc === false) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'error' => 'Format Excel invalide']);
                    exit;
                }

                $isFirstRow = true;
                foreach ($doc->children() as $sheetData) {
                    if (strpos((string)$sheetData->getName(), 'sheetData') === false) continue;
                    
                    foreach ($sheetData->children() as $row) {
                        if (strpos((string)$row->getName(), 'row') === false) continue;
                        
                        if ($isFirstRow) {
                            $isFirstRow = false;
                            continue; // Ignorer les en-têtes
                        }

                        $rowData = [];
                        
                        foreach ($row->children() as $cell) {
                            if (strpos((string)$cell->getName(), 'c') === false) continue;
                            
                            $cellType = (string)$cell['t'];
                            $cellValue = '';
                            
                            // Récupérer la valeur
                            foreach ($cell->children() as $v) {
                                if (strpos((string)$v->getName(), 'v') !== false) {
                                    $cellValue = (string)$v;
                                    break;
                                }
                            }
                            
                            // Si c'est une string partagée, la récupérer
                            if ($cellType === 's' && isset($sharedStrings[(int)$cellValue])) {
                                $rowData[] = $sharedStrings[(int)$cellValue];
                            } else {
                                $rowData[] = $cellValue;
                            }
                        }

                        if (!empty($rowData[0])) {
                            $extractedData[] = [
                                'nom' => trim($rowData[0] ?? ''),
                                'dosage' => trim($rowData[1] ?? ''),
                                'categorie' => trim($rowData[2] ?? ''),
                                'prix' => floatval($rowData[3] ?? 0),
                                'quantite' => intval($rowData[4] ?? 0)
                            ];
                        }
                    }
                }

                if (!empty($extractedData)) {
                    http_response_code(200);
                    echo json_encode([
                        'success' => true,
                        'message' => count($extractedData) . ' médicament(s) trouvé(s)',
                        'data' => $extractedData[0],
                        'total' => count($extractedData)
                    ]);
                } else {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'error' => 'Aucune donnée valide trouvée dans le fichier Excel. Le fichier doit avoir au moins 2 lignes (en-têtes + données).']);
                }
                exit;
            }
            // Formats non supportés
            else {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Format .' . $fileExt . ' non supporté. Utilisez Excel (.xlsx) ou CSV.']);
                exit;
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Erreur serveur: ' . $e->getMessage()]);
            exit;
        }
    }

    /**
     * GET - Récupérer tous les médicaments
     */
    if ($action === 'list' && $_SERVER['REQUEST_METHOD'] === 'GET') {
        $requestedPharmacieId = intval($_GET['pharmacy_id'] ?? 0);
        $pharmacie_id = null;

        if (!empty($_SESSION['user_id']) && ($_SESSION['user_role'] ?? '') === 'pharmacie') {
            $query = 'SELECT id FROM pharmacies WHERE pharmacien_id = ? LIMIT 1';
            $stmt = $conn->prepare($query);
            $stmt->execute([$_SESSION['user_id']]);
            $pharmacie = $stmt->fetch(PDO::FETCH_ASSOC);
            $pharmacie_id = $pharmacie['id'] ?? null;
        }

        if ($requestedPharmacieId > 0) {
            $query = 'SELECT m.*, COALESCE(s.quantite, 0) AS quantite
                      FROM medicaments m
                      INNER JOIN stocks s ON m.id = s.medicament_id
                      WHERE s.pharmacie_id = ?
                      ORDER BY m.nom ASC';
            $stmt = $conn->prepare($query);
            $stmt->execute([$requestedPharmacieId]);
            $medicaments = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode([
                'success' => true,
                'data' => $medicaments,
                'pharmacie_id' => $requestedPharmacieId
            ]);
            exit;
        }

        if ($pharmacie_id) {
            $query = 'SELECT m.*, COALESCE(s.quantite, 0) AS quantite
                      FROM medicaments m
                      INNER JOIN stocks s ON m.id = s.medicament_id
                      WHERE s.pharmacie_id = ?
                      ORDER BY m.nom ASC';
            $stmt = $conn->prepare($query);
            $stmt->execute([$pharmacie_id]);
        } else {
            // Admin - Afficher la somme des stocks de toutes les pharmacies
            $query = 'SELECT m.*, COALESCE(SUM(s.quantite), 0) AS quantite
                      FROM medicaments m
                      LEFT JOIN stocks s ON m.id = s.medicament_id
                      GROUP BY m.id
                      ORDER BY m.nom ASC';
            $stmt = $conn->prepare($query);
            $stmt->execute();
        }

        $medicaments = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'success' => true,
            'data' => $medicaments,
            'pharmacie_id' => $pharmacie_id
        ]);
        exit;
    }

    /**
     * GET - Récupérer un médicament spécifique
     */
    if ($action === 'get' && $_SERVER['REQUEST_METHOD'] === 'GET') {
        $id = intval($_GET['id'] ?? 0);
        
        if ($id <= 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'ID invalide']);
            exit;
        }

        $query = 'SELECT * FROM medicaments WHERE id = ?';
        $stmt = $conn->prepare($query);
        $stmt->execute([$id]);
        $medicament = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$medicament) {
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => 'Médicament non trouvé']);
            exit;
        }

        echo json_encode([
            'success' => true,
            'data' => $medicament
        ]);
        exit;
    }

    /**
     * POST - Ajouter un médicament
     */
    if ($action === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        // Vérifier l'authentification
        if (empty($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'error' => 'Non authentifié']);
            exit;
        }

        // Récupérer les données
        $nom = trim($_POST['nom'] ?? '');
        $dosage = trim($_POST['dosage'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $prix = floatval($_POST['prix'] ?? 0);
        $categorie = trim($_POST['categorie'] ?? '');
        $quantite = intval($_POST['quantite'] ?? 0);

        // Valider
        if (empty($nom)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Le nom est requis']);
            exit;
        }

        if ($prix < 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Le prix doit être positif']);
            exit;
        }

        try {
            // Insérer le médicament
            $query = 'INSERT INTO medicaments (nom, dosage, description, prix, categorie) VALUES (?, ?, ?, ?, ?)';
            $stmt = $conn->prepare($query);
            $stmt->execute([$nom, $dosage, $description, $prix, $categorie]);

            $medicamentId = $conn->lastInsertId();

            // Si quantité > 0 et l'utilisateur est pharmacien, créer une entrée stock
            if ($quantite > 0 && ($_SESSION['user_role'] ?? '') === 'pharmacie') {
                // Récupérer la pharmacie associée à ce pharmacien
                $query = 'SELECT id FROM pharmacies WHERE pharmacien_id = ? LIMIT 1';
                $stmt = $conn->prepare($query);
                $stmt->execute([$_SESSION['user_id']]);
                $pharmacie = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($pharmacie) {
                    // Insérer ou mettre à jour le stock
                    $query = 'INSERT INTO stocks (pharmacie_id, medicament_id, quantite) VALUES (?, ?, ?) 
                              ON DUPLICATE KEY UPDATE quantite = ?';
                    $stmt = $conn->prepare($query);
                    $stmt->execute([$pharmacie['id'], $medicamentId, $quantite, $quantite]);
                }
            }

            echo json_encode([
                'success' => true,
                'message' => 'Médicament ajouté avec succès',
                'data' => [
                    'id' => $medicamentId,
                    'nom' => $nom,
                    'dosage' => $dosage,
                    'description' => $description,
                    'prix' => $prix,
                    'categorie' => $categorie,
                    'quantite' => $quantite
                ]
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Erreur lors de l\'ajout: ' . $e->getMessage()]);
        }
        exit;
    }

    /**
     * PUT/POST - Modifier un médicament
     */
    if ($action === 'update' && ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'PUT')) {
        // Vérifier l'authentification
        if (empty($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'error' => 'Non authentifié']);
            exit;
        }

        $id = intval($_POST['id'] ?? $_GET['id'] ?? 0);
        
        if ($id <= 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'ID invalide']);
            exit;
        }

        $nom = trim($_POST['nom'] ?? '');
        $dosage = trim($_POST['dosage'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $prix = floatval($_POST['prix'] ?? 0);
        $categorie = trim($_POST['categorie'] ?? '');

        if (empty($nom)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Le nom est requis']);
            exit;
        }

        $query = 'UPDATE medicaments SET nom = ?, dosage = ?, description = ?, prix = ?, categorie = ? WHERE id = ?';
        $stmt = $conn->prepare($query);
        $stmt->execute([$nom, $dosage, $description, $prix, $categorie, $id]);

        echo json_encode([
            'success' => true,
            'message' => 'Médicament modifié avec succès'
        ]);
        exit;
    }

    /**
     * DELETE/POST - Supprimer un médicament
     */
    if ($action === 'delete' && ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'DELETE')) {
        // Vérifier l'authentification
        if (empty($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'error' => 'Non authentifié']);
            exit;
        }

        $id = intval($_POST['id'] ?? $_GET['id'] ?? 0);
        
        if ($id <= 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'ID invalide']);
            exit;
        }

        // Supprimer d'abord les stocks et réservations
        $query = 'DELETE FROM stocks WHERE medicament_id = ?';
        $stmt = $conn->prepare($query);
        $stmt->execute([$id]);

        $query = 'DELETE FROM reservations WHERE medicament_id = ?';
        $stmt = $conn->prepare($query);
        $stmt->execute([$id]);

        // Supprimer le médicament
        $query = 'DELETE FROM medicaments WHERE id = ?';
        $stmt = $conn->prepare($query);
        $stmt->execute([$id]);

        echo json_encode([
            'success' => true,
            'message' => 'Médicament supprimé avec succès'
        ]);
        exit;
    }

    /**
     * GET - Chercher des médicaments
     */
    if ($action === 'search' && $_SERVER['REQUEST_METHOD'] === 'GET') {
        $query_str = trim($_GET['q'] ?? '');
        
        if (empty($query_str)) {
            echo json_encode(['success' => false, 'error' => 'Terme de recherche requis']);
            exit;
        }

        $query = 'SELECT * FROM medicaments WHERE nom LIKE ? OR categorie LIKE ? ORDER BY nom ASC';
        $stmt = $conn->prepare($query);
        $search_term = '%' . $query_str . '%';
        $stmt->execute([$search_term, $search_term]);
        $medicaments = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'success' => true,
            'data' => $medicaments
        ]);
        exit;
    }

    /**
     * GET - Chercher des médicaments avec stocks et pharmacies
     */
    if ($action === 'search_with_stocks' && $_SERVER['REQUEST_METHOD'] === 'GET') {
        $query_str = trim($_GET['q'] ?? '');
        
        if (empty($query_str)) {
            echo json_encode(['success' => false, 'error' => 'Terme de recherche requis']);
            exit;
        }

        // Chercher les médicaments
        $query = 'SELECT m.* FROM medicaments m WHERE m.nom LIKE ? OR m.categorie LIKE ? ORDER BY m.nom ASC';
        $stmt = $conn->prepare($query);
        $search_term = '%' . $query_str . '%';
        $stmt->execute([$search_term, $search_term]);
        $medicaments = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Pour chaque médicament, récupérer les pharmacies et les stocks
        $results = [];
        foreach ($medicaments as $med) {
            // Récupérer les stocks avec les infos de pharmacie
            $stockQuery = 'SELECT 
                            p.id as pharmacie_id,
                            p.nom as pharmacie_nom,
                            p.adresse as pharmacie_adresse,
                            p.telephone as pharmacie_telephone,
                            p.email as pharmacie_email,
                            s.quantite,
                            CASE 
                                WHEN g.id IS NOT NULL AND g.date_garde = CURDATE() THEN 1 
                                ELSE 0 
                            END as is_garde,
                            g.heure_debut,
                            g.heure_fin
                        FROM stocks s
                        INNER JOIN pharmacies p ON s.pharmacie_id = p.id
                        LEFT JOIN gardes g ON p.id = g.pharmacie_id AND g.date_garde = CURDATE()
                        WHERE s.medicament_id = ?
                        ORDER BY p.nom ASC';
            $stockStmt = $conn->prepare($stockQuery);
            $stockStmt->execute([$med['id']]);
            $stocks = $stockStmt->fetchAll(PDO::FETCH_ASSOC);

            $med['pharmacies'] = $stocks;
            $results[] = $med;
        }

        echo json_encode([
            'success' => true,
            'data' => $results
        ]);
        exit;
    }

    // Action non reconnue
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Action non reconnue']);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Erreur serveur: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Erreur: ' . $e->getMessage()
    ]);
}
?>
