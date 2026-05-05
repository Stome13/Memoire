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
