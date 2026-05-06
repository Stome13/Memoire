<?php
// Test du chargement du profil et des réservations
session_start();

echo "<pre>";
echo "=== DEBUG PROFIL ===\n\n";

// Test 1: Vérifier le chemin
echo "Test 1: Chemins relatifs\n";
echo "  __DIR__: " . __DIR__ . "\n";
echo "  Session.php path: " . __DIR__ . '/frontend/includes/session.php' . "\n";
echo "  db.php path: " . __DIR__ . '/backend/includes/db.php' . "\n\n";

// Test 2: Vérifier l'inclusion
echo "Test 2: Inclusion des fichiers\n";
if (file_exists(__DIR__ . '/frontend/includes/session.php')) {
    echo "  ✓ session.php exists\n";
    require_once __DIR__ . '/frontend/includes/session.php';
    echo "  ✓ session.php included\n";
} else {
    echo "  ✗ session.php NOT FOUND\n";
}

if (file_exists(__DIR__ . '/backend/includes/db.php')) {
    echo "  ✓ db.php exists\n";
    require_once __DIR__ . '/backend/includes/db.php';
    echo "  ✓ db.php included\n";
} else {
    echo "  ✗ db.php NOT FOUND\n";
}

// Test 3: Vérifier l'utilisateur
echo "\nTest 3: Session utilisateur\n";
if (isset($_SESSION['user_id'])) {
    echo "  ✓ user_id in session: " . $_SESSION['user_id'] . "\n";
} else {
    echo "  ✗ No user in session\n";
    die("Please login first\n");
}

// Test 4: Vérifier la base de données
echo "\nTest 4: Base de données\n";
try {
    $db = Database::getInstance()->getConnection();
    echo "  ✓ Database connection OK\n";
    
    // Compter les réservations
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM reservations WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $result = $stmt->fetch();
    echo "  ✓ Reservations count for user: " . $result['count'] . "\n";
    
    // Afficher les réservations
    echo "\n  Details:\n";
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
    
    foreach ($reservations as $res) {
        echo "    - ID " . $res['id'] . ": " . $res['medicament_nom'] . " @ " . $res['pharmacie_nom'] . " (Qty: " . $res['quantite'] . ")\n";
    }
    
} catch (Exception $e) {
    echo "  ✗ Database error: " . $e->getMessage() . "\n";
}

echo "\n=== END DEBUG ===\n";
echo "</pre>";
?>
