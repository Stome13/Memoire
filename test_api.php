<?php
session_start();

// Simulation d'une session utilisateur connecté
$_SESSION['user_id'] = 1;
$_SESSION['user_role'] = 'client';

require_once __DIR__ . '/backend/includes/config.php';
require_once __DIR__ . '/backend/includes/db.php';
require_once __DIR__ . '/backend/includes/functions.php';

echo "=== TEST API RESERVATIONS ===\n";
echo "Session user_id: " . ($_SESSION['user_id'] ?? 'NOT SET') . "\n";
echo "Is Logged In: " . (isLoggedIn() ? 'YES' : 'NO') . "\n\n";

// Test 1: Vérifier la connexion DB
echo "=== TEST 1: Connexion DB ===\n";
try {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("SELECT 1 as test");
    $stmt->execute();
    echo "✓ Connexion DB OK\n\n";
} catch (Exception $e) {
    echo "✗ Erreur DB: " . $e->getMessage() . "\n\n";
}

// Test 2: Vérifier les réservations existantes
echo "=== TEST 2: Réservations existantes ===\n";
try {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM reservations");
    $stmt->execute();
    $result = $stmt->fetch();
    echo "✓ Nombre de réservations: " . $result['count'] . "\n\n";
} catch (Exception $e) {
    echo "✗ Erreur: " . $e->getMessage() . "\n\n";
}

// Test 3: Vérifier l'endpoint getByUser
echo "=== TEST 3: getByUser (Action API) ===\n";
try {
    $_GET['action'] = 'getByUser';
    
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("
        SELECT r.id, r.quantite, r.date_reservation, r.statut,
               m.nom as medicament_nom, m.dosage, m.categorie, p.nom as pharmacie_nom
        FROM reservations r
        JOIN medicaments m ON r.medicament_id = m.id
        JOIN pharmacies p ON r.pharmacie_id = p.id
        WHERE r.user_id = ?
        ORDER BY r.date_reservation DESC
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $reservations = $stmt->fetchAll();
    
    echo "✓ Requête OK. Réservations trouvées: " . count($reservations) . "\n";
    foreach ($reservations as $res) {
        echo "  - " . $res['medicament_nom'] . " @ " . $res['pharmacie_nom'] . " (" . $res['statut'] . ")\n";
    }
    echo "\n";
} catch (Exception $e) {
    echo "✗ Erreur: " . $e->getMessage() . "\n\n";
}

// Test 4: Vérifier les stocks
echo "=== TEST 4: Stocks disponibles ===\n";
try {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("
        SELECT m.nom, p.nom as pharmacie, s.quantite
        FROM stocks s
        JOIN medicaments m ON s.medicament_id = m.id
        JOIN pharmacies p ON s.pharmacie_id = p.id
        LIMIT 5
    ");
    $stmt->execute();
    $stocks = $stmt->fetchAll();
    
    echo "✓ Stocks trouvés: " . count($stocks) . "\n";
    foreach ($stocks as $stock) {
        echo "  - " . $stock['nom'] . " @ " . $stock['pharmacie'] . ": " . $stock['quantite'] . "\n";
    }
    echo "\n";
} catch (Exception $e) {
    echo "✗ Erreur: " . $e->getMessage() . "\n\n";
}

echo "=== TEST TERMINÉ ===\n";
?>
