<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

// Vérifier l'authentification - OBLIGATOIRE pour les réservations
requireAuth();

$action = getPost('action') ?? getGet('action');

// CRÉER UNE RÉSERVATION
if ($action === 'create') {
    $pharmacie_id = getPost('pharmacie_id');
    $medicament_id = getPost('medicament_id');
    $quantite = getPost('quantite');

    if (empty($pharmacie_id) || empty($medicament_id) || empty($quantite)) {
        jsonResponse(['success' => false, 'error' => 'Données manquantes'], 400);
    }

    $db = Database::getInstance()->getConnection();
    
    // Vérifier la disponibilité
    $stmt = $db->prepare("SELECT quantite FROM stocks WHERE pharmacie_id = ? AND medicament_id = ?");
    $stmt->execute([$pharmacie_id, $medicament_id]);
    $stock = $stmt->fetch();

    if (!$stock || $stock['quantite'] < $quantite) {
        jsonResponse(['success' => false, 'error' => 'Quantité insuffisante'], 400);
    }

    // Créer la réservation
    try {
        $stmt = $db->prepare("INSERT INTO reservations (user_id, medicament_id, pharmacie_id, quantite, date_reservation, statut) 
                            VALUES (?, ?, ?, ?, NOW(), 'en attente')");
        $stmt->execute([$_SESSION['user_id'], $medicament_id, $pharmacie_id, $quantite]);
        $reservationId = $db->lastInsertId();

        logAction($_SESSION['user_id'], 'reservation_created', ['reservation_id' => $reservationId]);

        jsonResponse([
            'success' => true,
            'message' => 'Réservation créée avec succès',
            'reservation_id' => $reservationId
        ]);
    } catch (Exception $e) {
        jsonResponse(['success' => false, 'error' => 'Erreur lors de la réservation'], 500);
    }
}

// OBTENIR LES RÉSERVATIONS DE L'UTILISATEUR CONNECTÉ
else if ($action === 'getByUser') {
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

    jsonResponse([
        'success' => true,
        'data' => $reservations
    ]);
}

// METTRE À JOUR UNE RÉSERVATION
else if ($action === 'update') {
    $reservation_id = getPost('id');
    $new_quantite = getPost('quantite');

    if (empty($reservation_id) || empty($new_quantite)) {
        jsonResponse(['success' => false, 'error' => 'Données manquantes'], 400);
    }

    $db = Database::getInstance()->getConnection();
    
    // Vérifier que la réservation appartient à l'utilisateur et est modifiable
    $stmt = $db->prepare("SELECT id, statut, medicament_id, pharmacie_id FROM reservations WHERE id = ? AND user_id = ?");
    $stmt->execute([$reservation_id, $_SESSION['user_id']]);
    $reservation = $stmt->fetch();
    
    if (!$reservation) {
        jsonResponse(['success' => false, 'error' => 'Réservation non trouvée'], 404);
    }

    // Vérifier que le statut permet la modification
    if (strtolower($reservation['statut']) !== 'en attente') {
        jsonResponse(['success' => false, 'error' => 'Seules les réservations en attente peuvent être modifiées'], 400);
    }

    // Vérifier la disponibilité du stock
    $stmt = $db->prepare("SELECT quantite FROM stocks WHERE pharmacie_id = ? AND medicament_id = ?");
    $stmt->execute([$reservation['pharmacie_id'], $reservation['medicament_id']]);
    $stock = $stmt->fetch();

    if (!$stock || $stock['quantite'] < $new_quantite) {
        jsonResponse(['success' => false, 'error' => 'Quantité insuffisante en stock'], 400);
    }

    // Mettre à jour la quantité
    $stmt = $db->prepare("UPDATE reservations SET quantite = ? WHERE id = ?");
    $stmt->execute([$new_quantite, $reservation_id]);

    logAction($_SESSION['user_id'], 'reservation_updated', ['reservation_id' => $reservation_id, 'new_quantite' => $new_quantite]);

    jsonResponse(['success' => true, 'message' => 'Réservation mise à jour avec succès']);
}

// SUPPRIMER/ANNULER UNE RÉSERVATION
else if ($action === 'delete') {
    $reservation_id = getPost('id');

    if (empty($reservation_id)) {
        jsonResponse(['success' => false, 'error' => 'ID manquant'], 400);
    }

    $db = Database::getInstance()->getConnection();
    
    // Vérifier que la réservation appartient à l'utilisateur
    $stmt = $db->prepare("SELECT id, statut FROM reservations WHERE id = ? AND user_id = ?");
    $stmt->execute([$reservation_id, $_SESSION['user_id']]);
    $reservation = $stmt->fetch();
    
    if (!$reservation) {
        jsonResponse(['success' => false, 'error' => 'Réservation non trouvée'], 404);
    }

    // Vérifier que le statut permet l'annulation
    if (strtolower($reservation['statut']) !== 'en attente') {
        jsonResponse(['success' => false, 'error' => 'Seules les réservations en attente peuvent être annulées'], 400);
    }

    // Annuler la réservation
    $stmt = $db->prepare("UPDATE reservations SET statut = 'annulée' WHERE id = ?");
    $stmt->execute([$reservation_id]);

    logAction($_SESSION['user_id'], 'reservation_deleted', ['reservation_id' => $reservation_id]);

    jsonResponse(['success' => true, 'message' => 'Réservation annulée avec succès']);
}

// OBTENIR MES RÉSERVATIONS
else if ($action === 'getMyReservations') {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("
        SELECT r.id, r.quantite, r.date_reservation, r.statut,
               m.nom as medicament, m.dosage, p.nom as pharmacie, p.telephone
        FROM reservations r
        JOIN medicaments m ON r.medicament_id = m.id
        JOIN pharmacies p ON r.pharmacie_id = p.id
        WHERE r.user_id = ?
        ORDER BY r.date_reservation DESC
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $reservations = $stmt->fetchAll();

    jsonResponse([
        'success' => true,
        'reservations' => $reservations
    ]);
}

// LISTER TOUTES LES RÉSERVATIONS (ADMIN)
else if ($action === 'listAll') {
    requireAdmin();
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("
        SELECT r.id, u.email AS client_email, p.nom AS pharmacie, m.nom AS medicament, r.quantite, r.statut, r.date_reservation
        FROM reservations r
        JOIN users u ON r.user_id = u.id
        JOIN medicaments m ON r.medicament_id = m.id
        JOIN pharmacies p ON r.pharmacie_id = p.id
        ORDER BY r.date_reservation DESC
    ");
    $stmt->execute();
    $reservations = $stmt->fetchAll();

    jsonResponse([
        'success' => true,
        'reservations' => $reservations
    ]);
}

// LISTER LES RÉSERVATIONS POUR UNE PHARMACIE (PHARMACIEN)
else if ($action === 'listByPharmacy') {
    if (($_SESSION['user_role'] ?? '') !== 'pharmacie') {
        jsonResponse(['success' => false, 'error' => 'Accès réservé aux pharmaciens'], 403);
    }

    $db = Database::getInstance()->getConnection();
    
    $query = 'SELECT id FROM pharmacies WHERE pharmacien_id = ? LIMIT 1';
    $stmt = $db->prepare($query);
    $stmt->execute([$_SESSION['user_id']]);
    $pharmacie = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$pharmacie) {
        jsonResponse(['success' => true, 'reservations' => []]);
        exit;
    }

    $stmt = $db->prepare("
        SELECT r.id, u.email AS client_email, u.nom AS client_nom, u.prenom AS client_prenom, 
               m.nom AS medicament, m.dosage, r.quantite, r.statut, r.date_reservation, r.date_retrait
        FROM reservations r
        JOIN users u ON r.user_id = u.id
        JOIN medicaments m ON r.medicament_id = m.id
        WHERE r.pharmacie_id = ?
        ORDER BY r.date_reservation DESC
    ");
    $stmt->execute([$pharmacie['id']]);
    $reservations = $stmt->fetchAll();

    jsonResponse([
        'success' => true,
        'reservations' => $reservations
    ]);
}

// METTRE À JOUR LE STATUT D'UNE RÉSERVATION (PHARMACIEN)
else if ($action === 'updateStatus') {
    if (($_SESSION['user_role'] ?? '') !== 'pharmacie') {
        jsonResponse(['success' => false, 'error' => 'Accès réservé aux pharmaciens'], 403);
    }

    $reservation_id = getPost('id');
    $new_statut = getPost('statut');

    if (empty($reservation_id) || empty($new_statut)) {
        jsonResponse(['success' => false, 'error' => 'Données manquantes'], 400);
    }

    // Valider le statut
    $valid_statuts = ['en attente', 'confirmée', 'prête', 'retirée', 'annulée'];
    if (!in_array($new_statut, $valid_statuts)) {
        jsonResponse(['success' => false, 'error' => 'Statut invalide'], 400);
    }

    $db = Database::getInstance()->getConnection();
    
    // Vérifier que la réservation appartient à la pharmacie du pharmacien connecté
    $stmt = $db->prepare("
        SELECT r.id FROM reservations r
        JOIN pharmacies p ON r.pharmacie_id = p.id
        WHERE r.id = ? AND p.pharmacien_id = ?
    ");
    $stmt->execute([$reservation_id, $_SESSION['user_id']]);
    
    if (!$stmt->fetch()) {
        jsonResponse(['success' => false, 'error' => 'Réservation non trouvée'], 404);
    }

    // Mettre à jour le statut
    $date_retrait = ($new_statut === 'retirée') ? date('Y-m-d H:i:s') : null;
    
    if ($date_retrait) {
        $stmt = $db->prepare("UPDATE reservations SET statut = ?, date_retrait = ? WHERE id = ?");
        $stmt->execute([$new_statut, $date_retrait, $reservation_id]);
    } else {
        $stmt = $db->prepare("UPDATE reservations SET statut = ? WHERE id = ?");
        $stmt->execute([$new_statut, $reservation_id]);
    }

    logAction($_SESSION['user_id'], 'reservation_status_updated', ['reservation_id' => $reservation_id, 'new_statut' => $new_statut]);

    jsonResponse(['success' => true, 'message' => 'Statut mis à jour']);
}

// ANNULER UNE RÉSERVATION
else if ($action === 'cancel') {
    $reservation_id = getPost('reservation_id');

    $db = Database::getInstance()->getConnection();
    
    // Vérifier que la réservation appartient à l'utilisateur
    $stmt = $db->prepare("SELECT id FROM reservations WHERE id = ? AND user_id = ?");
    $stmt->execute([$reservation_id, $_SESSION['user_id']]);
    
    if (!$stmt->fetch()) {
        jsonResponse(['success' => false, 'error' => 'Réservation non trouvée'], 404);
    }

    // Annuler la réservation
    $stmt = $db->prepare("UPDATE reservations SET statut = 'annulée' WHERE id = ?");
    $stmt->execute([$reservation_id]);

    logAction($_SESSION['user_id'], 'reservation_cancelled', ['reservation_id' => $reservation_id]);

    jsonResponse(['success' => true, 'message' => 'Réservation annulée']);
}

else {
    jsonResponse(['success' => false, 'error' => 'Action non reconnue'], 400);
}
?>
