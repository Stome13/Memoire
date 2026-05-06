<?php
// Simulation de la session
session_start();
$_SESSION['user_id'] = 1;
$_SESSION['user_role'] = 'client';

// Incluons le fichier profil en simulant l'environnement
ob_start();

// Créer les fonctions de helpers nécessaires
if (!function_exists('escape')) {
    function escape($text) {
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }
}

require_once __DIR__ . '/backend/includes/db.php';

// Charger les réservations
$currentUser = ['id' => 1, 'prenom' => 'Test', 'nom' => 'User', 'email' => 'test@test.com'];
$reservations = [];

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
    $stmt->execute([$currentUser['id']]);
    $reservations = $stmt->fetchAll();
} catch (Exception $e) {
    error_log("Erreur: " . $e->getMessage());
}

echo "=== AFFICHAGE DES RÉSERVATIONS (PHP) ===\n\n";
echo "Nombre de réservations: " . count($reservations) . "\n";
echo "Badge compteur: " . count($reservations) . "\n\n";

echo "=== CARTES DE RÉSERVATION ===\n\n";

if (empty($reservations)) {
    echo "❌ Aucune réservation\n";
} else {
    foreach ($reservations as $i => $reservation) {
        echo "RÉSERVATION #" . ($i + 1) . "\n";
        echo "─────────────────────────────────\n";
        
        $statusClass = [
            'en attente' => 'status-pending (🟡)',
            'confirmée' => 'status-confirmed (🟢)',
            'prête' => 'status-picked (🔵)',
            'retirée' => 'status-picked (⚪)',
            'annulée' => 'status-cancelled (🔴)'
        ][strtolower($reservation['statut'])] ?? 'unknown';
        
        $canModify = strtolower($reservation['statut']) === 'en attente';
        
        echo "  Médicament: " . $reservation['medicament_nom'] . "\n";
        echo "  Pharmacie:  " . $reservation['pharmacie_nom'] . "\n";
        echo "  Statut:     " . $reservation['statut'] . " " . $statusClass . "\n";
        echo "  Quantité:   " . $reservation['quantite'] . "\n";
        echo "  Date:       " . date('d/m/Y', strtotime($reservation['date_reservation'])) . "\n";
        echo "  Dosage:     " . ($reservation['dosage'] ?? '-') . "\n";
        echo "  Catégorie:  " . ($reservation['categorie'] ?? '-') . "\n";
        
        if ($canModify) {
            echo "  Actions:    ✏️ Modifier | ❌ Annuler\n";
        } else {
            echo "  Actions:    🔒 Non modifiable\n";
        }
        echo "\n";
    }
}

ob_end_clean();
?>
