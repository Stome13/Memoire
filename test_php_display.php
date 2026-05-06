<?php
session_start();
$_SESSION['user_id'] = 1;
$_SESSION['user_role'] = 'client';

require_once __DIR__ . '/backend/includes/db.php';

echo "=== AFFICHAGE DES RÉSERVATIONS EN PHP ===\n\n";

try {
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
    $stmt->execute([1]);
    $reservations = $stmt->fetchAll();
    
    echo "✓ Connexion DB OK\n";
    echo "✓ Nombre de réservations: " . count($reservations) . "\n";
    echo "✓ Badge du compteur: " . count($reservations) . "\n\n";
    
    echo "=== CARTES DE RÉSERVATION ===\n\n";
    
    if (empty($reservations)) {
        echo "(Aucune réservation)\n";
    } else {
        foreach ($reservations as $i => $res) {
            echo "["  . ($i + 1) . "] ";
            echo $res['medicament_nom'] . " @ " . $res['pharmacie_nom'] . " | ";
            echo "Qté: " . $res['quantite'] . " | ";
            echo "Statut: " . $res['statut'];
            echo "\n";
        }
    }
    
} catch (Exception $e) {
    echo "✗ ERREUR: " . $e->getMessage() . "\n";
}
?>
