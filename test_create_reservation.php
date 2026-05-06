<?php
session_start();

// Simulation d'une session utilisateur connecté
$_SESSION['user_id'] = 1;
$_SESSION['user_role'] = 'client';

require_once __DIR__ . '/backend/includes/config.php';
require_once __DIR__ . '/backend/includes/db.php';
require_once __DIR__ . '/backend/includes/functions.php';

echo "=== TEST CRÉATION RÉSERVATION ===\n\n";

// Simuler une requête POST
$_SERVER['REQUEST_METHOD'] = 'POST';

// Simuler les données POST comme le ferait FormData
$_POST['action'] = 'create';
$_POST['medicament_id'] = '1';
$_POST['pharmacie_id'] = '1';
$_POST['quantite'] = '2';

echo "Données POST:\n";
echo "  action: " . $_POST['action'] . "\n";
echo "  medicament_id: " . $_POST['medicament_id'] . "\n";
echo "  pharmacie_id: " . $_POST['pharmacie_id'] . "\n";
echo "  quantite: " . $_POST['quantite'] . "\n";
echo "  user_id (session): " . $_SESSION['user_id'] . "\n\n";

try {
    $db = Database::getInstance()->getConnection();
    
    // Vérifier la disponibilité du stock
    echo "1. Vérification du stock...\n";
    $stmt = $db->prepare("SELECT quantite FROM stocks WHERE pharmacie_id = ? AND medicament_id = ?");
    $stmt->execute([$_POST['pharmacie_id'], $_POST['medicament_id']]);
    $stock = $stmt->fetch();
    
    if ($stock) {
        echo "   ✓ Stock trouvé: " . $stock['quantite'] . "\n";
        
        if ($stock['quantite'] >= $_POST['quantite']) {
            echo "   ✓ Quantité suffisante\n\n";
            
            // Créer la réservation
            echo "2. Création de la réservation...\n";
            $stmt = $db->prepare("INSERT INTO reservations (user_id, medicament_id, pharmacie_id, quantite, date_reservation, statut) 
                                VALUES (?, ?, ?, ?, NOW(), 'en attente')");
            $stmt->execute([$_SESSION['user_id'], $_POST['medicament_id'], $_POST['pharmacie_id'], $_POST['quantite']]);
            $reservationId = $db->lastInsertId();
            
            echo "   ✓ Réservation créée avec ID: " . $reservationId . "\n\n";
            
            // Vérifier la création
            echo "3. Vérification de la réservation créée...\n";
            $stmt = $db->prepare("
                SELECT r.id, r.quantite, r.statut,
                       m.nom as medicament_nom, 
                       p.nom as pharmacie_nom
                FROM reservations r
                JOIN medicaments m ON r.medicament_id = m.id
                JOIN pharmacies p ON r.pharmacie_id = p.id
                WHERE r.id = ?
            ");
            $stmt->execute([$reservationId]);
            $reservation = $stmt->fetch();
            
            if ($reservation) {
                echo "   ✓ Réservation vérifiée:\n";
                echo "     - ID: " . $reservation['id'] . "\n";
                echo "     - Médicament: " . $reservation['medicament_nom'] . "\n";
                echo "     - Pharmacie: " . $reservation['pharmacie_nom'] . "\n";
                echo "     - Quantité: " . $reservation['quantite'] . "\n";
                echo "     - Statut: " . $reservation['statut'] . "\n\n";
            }
            
        } else {
            echo "   ✗ Quantité insuffisante\n\n";
        }
    } else {
        echo "   ✗ Stock non trouvé pour medicament_id=" . $_POST['medicament_id'] . " et pharmacie_id=" . $_POST['pharmacie_id'] . "\n\n";
    }
    
    // Vérifier les réservations de l'utilisateur
    echo "4. Récupération des réservations de l'utilisateur...\n";
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
    
    echo "   ✓ Réservations trouvées: " . count($reservations) . "\n";
    foreach ($reservations as $res) {
        echo "     - " . $res['medicament_nom'] . " @ " . $res['pharmacie_nom'] . " (Qté: " . $res['quantite'] . ", Statut: " . $res['statut'] . ")\n";
    }
    
} catch (Exception $e) {
    echo "✗ ERREUR: " . $e->getMessage() . "\n";
}

echo "\n=== TEST TERMINÉ ===\n";
?>
