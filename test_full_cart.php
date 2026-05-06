<?php
session_start();

// Simuler un utilisateur connecté
$_SESSION['user_id'] = 1;
$_SESSION['user_role'] = 'client';

require_once __DIR__ . '/backend/includes/config.php';
require_once __DIR__ . '/backend/includes/db.php';
require_once __DIR__ . '/backend/includes/functions.php';

echo "=== TEST COMPLET - SIMULATION PANIER ===\n\n";

$db = Database::getInstance()->getConnection();

// Items du panier (comme ils seraient envoyés)
$cartItems = [
    [
        'medicineId' => '7',     // Flagyl
        'pharmacyId' => '1',     // Pharmacie Centrale
        'medicineName' => 'Flagyl',
        'pharmacyName' => 'Pharmacie Centrale',
        'quantity' => 2
    ],
    [
        'medicineId' => '10',    // para
        'pharmacyId' => '7',     // PHARMACIE JEANPAUL
        'medicineName' => 'para',
        'pharmacyName' => 'PHARMACIE JEANPAUL',
        'quantity' => 1
    ]
];

echo "Items du panier: " . count($cartItems) . "\n";
foreach ($cartItems as $i => $item) {
    echo "  [$i] " . $item['medicineName'] . " @ " . $item['pharmacyName'] . " (Qté: " . $item['quantity'] . ")\n";
}
echo "\n";

echo "Création des réservations...\n";
$successCount = 0;
$failCount = 0;

foreach ($cartItems as $item) {
    echo "\n--- " . $item['medicineName'] . " ---\n";
    
    // Vérifier la connexion
    if (!isLoggedIn()) {
        echo "✗ Utilisateur non connecté\n";
        $failCount++;
        continue;
    }
    echo "✓ Utilisateur connecté (ID: " . $_SESSION['user_id'] . ")\n";
    
    // Vérifier le stock
    $stmt = $db->prepare("SELECT quantite FROM stocks WHERE pharmacie_id = ? AND medicament_id = ?");
    $stmt->execute([$item['pharmacyId'], $item['medicineId']]);
    $stock = $stmt->fetch();
    
    if (!$stock) {
        echo "✗ Stock non trouvé\n";
        $failCount++;
        continue;
    }
    
    echo "✓ Stock trouvé: " . $stock['quantite'] . " unités\n";
    
    if ($stock['quantite'] < $item['quantity']) {
        echo "✗ Quantité insuffisante (demandé: " . $item['quantity'] . ")\n";
        $failCount++;
        continue;
    }
    
    echo "✓ Quantité suffisante\n";
    
    // Créer la réservation
    try {
        $stmt = $db->prepare("
            INSERT INTO reservations (user_id, medicament_id, pharmacie_id, quantite, date_reservation, statut) 
            VALUES (?, ?, ?, ?, NOW(), 'en attente')
        ");
        $stmt->execute([$_SESSION['user_id'], $item['medicineId'], $item['pharmacyId'], $item['quantity']]);
        $resId = $db->lastInsertId();
        
        echo "✓ Réservation créée (ID: " . $resId . ")\n";
        $successCount++;
        
    } catch (Exception $e) {
        echo "✗ Erreur création: " . $e->getMessage() . "\n";
        $failCount++;
    }
}

echo "\n\n=== RÉSULTATS ===\n";
echo "Réussies: " . $successCount . "\n";
echo "Échouées: " . $failCount . "\n\n";

echo "=== VÉRIFICATION FINALE ===\n";
echo "Réservations de l'utilisateur " . $_SESSION['user_id'] . ":\n";
$stmt = $db->prepare("
    SELECT r.id, r.quantite, r.date_reservation, r.statut,
           m.nom as medicament_nom, p.nom as pharmacie_nom
    FROM reservations r
    JOIN medicaments m ON r.medicament_id = m.id
    JOIN pharmacies p ON r.pharmacie_id = p.id
    WHERE r.user_id = ?
    ORDER BY r.date_reservation DESC
    LIMIT 10
");
$stmt->execute([$_SESSION['user_id']]);
$reservations = $stmt->fetchAll();

if (count($reservations) > 0) {
    foreach ($reservations as $res) {
        echo "  [" . $res['id'] . "] " . $res['medicament_nom'] . " @ " . $res['pharmacie_nom'];
        echo " | Qté: " . $res['quantite'] . " | Statut: " . $res['statut'];
        echo " | Date: " . $res['date_reservation'] . "\n";
    }
} else {
    echo "  (aucune)\n";
}
?>
