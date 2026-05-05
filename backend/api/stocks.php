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
     * GET - Récupérer les stocks du pharmacien actuel
     */
    if ($action === 'current' && $_SERVER['REQUEST_METHOD'] === 'GET') {
        // Vérifier l'authentification
        if (empty($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'error' => 'Non authentifié']);
            exit;
        }

        // Récupérer la pharmacie du pharmacien actuel
        $query = 'SELECT id FROM pharmacies WHERE pharmacien_id = ? LIMIT 1';
        $stmt = $conn->prepare($query);
        $stmt->execute([$_SESSION['user_id']]);
        $pharmacie = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$pharmacie) {
            echo json_encode([
                'success' => true,
                'data' => [],
                'message' => 'Aucune pharmacie associée'
            ]);
            exit;
        }

        $query = 'SELECT s.*, m.nom, m.dosage, m.prix, m.categorie 
                  FROM stocks s
                  JOIN medicaments m ON s.medicament_id = m.id
                  WHERE s.pharmacie_id = ?
                  ORDER BY m.nom ASC';
        $stmt = $conn->prepare($query);
        $stmt->execute([$pharmacie['id']]);
        $stocks = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'success' => true,
            'data' => $stocks,
            'pharmacie_id' => $pharmacie['id']
        ]);
        exit;
    }

    /**
     * GET - Récupérer les stocks d'une pharmacie
     */
    if ($action === 'list' && $_SERVER['REQUEST_METHOD'] === 'GET') {
        // Vérifier l'authentification
        if (empty($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'error' => 'Non authentifié']);
            exit;
        }

        $pharmacie_id = intval($_GET['pharmacie_id'] ?? 0);

        if ($pharmacie_id <= 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'ID pharmacie invalide']);
            exit;
        }

        $query = 'SELECT s.*, m.nom, m.dosage, m.prix, m.categorie 
                  FROM stocks s
                  JOIN medicaments m ON s.medicament_id = m.id
                  WHERE s.pharmacie_id = ?
                  ORDER BY m.nom ASC';
        $stmt = $conn->prepare($query);
        $stmt->execute([$pharmacie_id]);
        $stocks = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'success' => true,
            'data' => $stocks
        ]);
        exit;
    }

    /**
     * GET - Récupérer stock d'un médicament pour une pharmacie
     */
    if ($action === 'get' && $_SERVER['REQUEST_METHOD'] === 'GET') {
        $pharmacie_id = intval($_GET['pharmacie_id'] ?? 0);
        $medicament_id = intval($_GET['medicament_id'] ?? 0);

        if ($pharmacie_id <= 0 || $medicament_id <= 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'IDs invalides']);
            exit;
        }

        $query = 'SELECT * FROM stocks WHERE pharmacie_id = ? AND medicament_id = ?';
        $stmt = $conn->prepare($query);
        $stmt->execute([$pharmacie_id, $medicament_id]);
        $stock = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$stock) {
            echo json_encode([
                'success' => true,
                'data' => ['pharmacie_id' => $pharmacie_id, 'medicament_id' => $medicament_id, 'quantite' => 0]
            ]);
            exit;
        }

        echo json_encode([
            'success' => true,
            'data' => $stock
        ]);
        exit;
    }

    /**
     * POST - Ajouter ou mettre à jour un stock
     */
    if ($action === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        // Vérifier l'authentification
        if (empty($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'error' => 'Non authentifié']);
            exit;
        }

        if (($_SESSION['user_role'] ?? '') !== 'pharmacie') {
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'Accès réservé aux pharmaciens']);
            exit;
        }

        $requestedPharmacieId = intval($_POST['pharmacie_id'] ?? 0);
        $query = 'SELECT id FROM pharmacies WHERE pharmacien_id = ? LIMIT 1';
        $stmt = $conn->prepare($query);
        $stmt->execute([$_SESSION['user_id']]);
        $pharmacie = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$pharmacie) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Pharmacie non trouvée pour l\'utilisateur']);
            exit;
        }

        $pharmacie_id = $pharmacie['id'];
        if ($requestedPharmacieId > 0 && $requestedPharmacieId !== $pharmacie_id) {
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'ID de pharmacie invalide']);
            exit;
        }

        $medicament_id = intval($_POST['medicament_id'] ?? 0);
        $quantite = intval($_POST['quantite'] ?? 0);

        if ($medicament_id <= 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'IDs invalides']);
            exit;
        }

        if ($quantite < 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'La quantité doit être positive']);
            exit;
        }

        // Insérer ou mettre à jour
        $query = 'INSERT INTO stocks (pharmacie_id, medicament_id, quantite) VALUES (?, ?, ?) 
                  ON DUPLICATE KEY UPDATE quantite = ?';
        $stmt = $conn->prepare($query);
        $stmt->execute([$pharmacie_id, $medicament_id, $quantite, $quantite]);

        echo json_encode([
            'success' => true,
            'message' => 'Stock mis à jour avec succès'
        ]);
        exit;
    }

    /**
     * POST - Augmenter le stock
     */
    if ($action === 'increase' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        // Vérifier l'authentification
        if (empty($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'error' => 'Non authentifié']);
            exit;
        }

        if (($_SESSION['user_role'] ?? '') !== 'pharmacie') {
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'Accès réservé aux pharmaciens']);
            exit;
        }

        $requestedPharmacieId = intval($_POST['pharmacie_id'] ?? 0);
        $query = 'SELECT id FROM pharmacies WHERE pharmacien_id = ? LIMIT 1';
        $stmt = $conn->prepare($query);
        $stmt->execute([$_SESSION['user_id']]);
        $pharmacie = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$pharmacie) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Pharmacie non trouvée pour l\'utilisateur']);
            exit;
        }

        $pharmacie_id = $pharmacie['id'];
        if ($requestedPharmacieId > 0 && $requestedPharmacieId !== $pharmacie_id) {
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'ID de pharmacie invalide']);
            exit;
        }

        $medicament_id = intval($_POST['medicament_id'] ?? 0);
        $quantite = intval($_POST['quantite'] ?? 0);

        if ($medicament_id <= 0 || $quantite <= 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Données invalides']);
            exit;
        }

        $query = 'UPDATE stocks SET quantite = quantite + ? WHERE pharmacie_id = ? AND medicament_id = ?';
        $stmt = $conn->prepare($query);
        $stmt->execute([$quantite, $pharmacie_id, $medicament_id]);

        echo json_encode([
            'success' => true,
            'message' => 'Stock augmenté avec succès'
        ]);
        exit;
    }

    /**
     * POST - Réduire le stock
     */
    if ($action === 'decrease' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        // Vérifier l'authentification
        if (empty($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'error' => 'Non authentifié']);
            exit;
        }

        if (($_SESSION['user_role'] ?? '') !== 'pharmacie') {
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'Accès réservé aux pharmaciens']);
            exit;
        }

        $requestedPharmacieId = intval($_POST['pharmacie_id'] ?? 0);
        $query = 'SELECT id FROM pharmacies WHERE pharmacien_id = ? LIMIT 1';
        $stmt = $conn->prepare($query);
        $stmt->execute([$_SESSION['user_id']]);
        $pharmacie = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$pharmacie) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Pharmacie non trouvée pour l\'utilisateur']);
            exit;
        }

        $pharmacie_id = $pharmacie['id'];
        if ($requestedPharmacieId > 0 && $requestedPharmacieId !== $pharmacie_id) {
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'ID de pharmacie invalide']);
            exit;
        }

        $medicament_id = intval($_POST['medicament_id'] ?? 0);
        $quantite = intval($_POST['quantite'] ?? 0);

        if ($medicament_id <= 0 || $quantite <= 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Données invalides']);
            exit;
        }

        $query = 'UPDATE stocks SET quantite = GREATEST(0, quantite - ?) WHERE pharmacie_id = ? AND medicament_id = ?';
        $stmt = $conn->prepare($query);
        $stmt->execute([$quantite, $pharmacie_id, $medicament_id]);

        echo json_encode([
            'success' => true,
            'message' => 'Stock réduit avec succès'
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
}
