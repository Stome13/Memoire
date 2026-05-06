<?php
session_start();
$_SESSION['user_id'] = 1;
$_SESSION['user_role'] = 'client';

require_once __DIR__ . '/backend/includes/config.php';
require_once __DIR__ . '/backend/includes/db.php';

$db = Database::getInstance()->getConnection();

echo "=== DONNÉES ACTUELLES EN BASE ===\n\n";

// Médicaments
echo "MÉDICAMENTS:\n";
$stmt = $db->prepare("SELECT id, nom, dosage FROM medicaments LIMIT 10");
$stmt->execute();
foreach ($stmt->fetchAll() as $med) {
    echo "  [" . $med['id'] . "] " . $med['nom'] . " (" . $med['dosage'] . ")\n";
}

echo "\nPHARMACIES:\n";
$stmt = $db->prepare("SELECT id, nom FROM pharmacies LIMIT 10");
$stmt->execute();
foreach ($stmt->fetchAll() as $pharm) {
    echo "  [" . $pharm['id'] . "] " . $pharm['nom'] . "\n";
}

echo "\nSTOCKS:\n";
$stmt = $db->prepare("SELECT s.id, m.nom as med, p.nom as pharm, s.quantite FROM stocks s JOIN medicaments m ON s.medicament_id = m.id JOIN pharmacies p ON s.pharmacie_id = p.id");
$stmt->execute();
foreach ($stmt->fetchAll() as $stock) {
    echo "  Stock #" . $stock['id'] . ": " . $stock['med'] . " @ " . $stock['pharm'] . " = " . $stock['quantite'] . " unités\n";
}

echo "\nRÉSERVATIONS:\n";
$stmt = $db->prepare("SELECT r.id, u.email, m.nom as med, p.nom as pharm, r.quantite, r.statut FROM reservations r JOIN users u ON r.user_id = u.id JOIN medicaments m ON r.medicament_id = m.id JOIN pharmacies p ON r.pharmacie_id = p.id");
$stmt->execute();
$reservations = $stmt->fetchAll();
if (count($reservations) > 0) {
    foreach ($reservations as $res) {
        echo "  Res #" . $res['id'] . ": " . $res['email'] . " → " . $res['med'] . " @ " . $res['pharm'] . " (Qté: " . $res['quantite'] . ", Statut: " . $res['statut'] . ")\n";
    }
} else {
    echo "  (aucune)\n";
}
?>
