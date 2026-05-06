<?php
session_start();

echo "<pre>";
echo "=== DEBUG PHARMACIEN DASHBOARD ===\n\n";

// Vérifier la session
if (!isset($_SESSION['user_id'])) {
    die("❌ Pas connecté. Veuillez vous connecter d'abord.\n");
}

echo "✓ Connecté en tant que user_id: " . $_SESSION['user_id'] . "\n\n";

// Charger la base de données
require_once __DIR__ . '/backend/includes/db.php';

try {
    $db = Database::getInstance()->getConnection();
    echo "✓ DB connected\n\n";
    
    // 1. Vérifier la pharmacie du pharmacien
    echo "TEST 1: Récupérer la pharmacie du pharmacien\n";
    $stmt = $db->prepare("SELECT * FROM pharmacies WHERE pharmacien_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $pharmacy = $stmt->fetch();
    
    if ($pharmacy) {
        echo "  ✓ Pharmacie trouvée:\n";
        echo "    ID: " . $pharmacy['id'] . "\n";
        echo "    Nom: " . $pharmacy['nom'] . "\n";
    } else {
        echo "  ✗ Aucune pharmacie assignée\n\n";
    }
    
    if (!$pharmacy) {
        echo "\n⚠️ Le pharmacien n'a pas de pharmacie assignée! C'est le problème.\n";
        die();
    }
    
    $pharmacyId = $pharmacy['id'];
    
    // 2. Compter les réservations en attente
    echo "\nTEST 2: Réservations en attente\n";
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM reservations WHERE pharmacie_id = ? AND statut = 'en attente'");
    $stmt->execute([$pharmacyId]);
    $result = $stmt->fetch();
    echo "  ✓ Réservations en attente: " . $result['count'] . "\n";
    
    // 3. Compter les stocks faibles
    echo "\nTEST 3: Stocks faibles (< 5)\n";
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM stocks WHERE pharmacie_id = ? AND quantite < 5");
    $stmt->execute([$pharmacyId]);
    $result = $stmt->fetch();
    echo "  ✓ Stocks faibles: " . $result['count'] . "\n";
    
    // 4. Compter les médicaments
    echo "\nTEST 4: Médicaments disponibles\n";
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM stocks WHERE pharmacie_id = ? AND quantite > 0");
    $stmt->execute([$pharmacyId]);
    $result = $stmt->fetch();
    echo "  ✓ Médicaments en stock: " . $result['count'] . "\n";
    
    // 5. Afficher les réservations récentes
    echo "\nTEST 5: Réservations récentes\n";
    $stmt = $db->prepare("
        SELECT r.*, u.nom, u.prenom, m.nom as med_nom 
        FROM reservations r
        JOIN users u ON r.user_id = u.id
        JOIN medicaments m ON r.medicament_id = m.id
        WHERE r.pharmacie_id = ?
        ORDER BY r.date_reservation DESC
        LIMIT 5
    ");
    $stmt->execute([$pharmacyId]);
    $reservations = $stmt->fetchAll();
    
    if (count($reservations) > 0) {
        echo "  ✓ " . count($reservations) . " réservations trouvées:\n";
        foreach ($reservations as $res) {
            echo "    - ID " . $res['id'] . ": " . $res['prenom'] . " " . $res['nom'] . " | " . $res['med_nom'] . " (" . $res['quantite'] . "x) | Statut: " . $res['statut'] . "\n";
        }
    } else {
        echo "  ⚠️ Aucune réservation trouvée\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}

echo "\n=== END DEBUG ===\n";
echo "</pre>";
?>
