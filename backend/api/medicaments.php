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
            $query = 'SELECT m.*, 0 AS quantite FROM medicaments m ORDER BY m.nom ASC';
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
