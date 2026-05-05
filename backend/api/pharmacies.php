<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

requireAuth();
requireAdmin();

$action = getPost('action') ?? getGet('action');

if ($action === 'register') {
    $name = trim(getPost('name'));
    $address = trim(getPost('address'));
    $telephone = trim(getPost('telephone'));
    $email = trim(getPost('email'));
    $openingHours = trim(getPost('opening_hours'));
    $closingHours = trim(getPost('closing_hours'));
    $manager = trim(getPost('manager'));
    $password = getPost('password');
    $passwordConfirm = getPost('passwordConfirm');

    if (empty($name) || empty($email) || empty($password)) {
        jsonResponse(['success' => false, 'error' => 'Nom, email et mot de passe sont requis'], 400);
    }

    if (!isValidEmail($email)) {
        jsonResponse(['success' => false, 'error' => 'Email invalide'], 400);
    }

    if ($password !== $passwordConfirm) {
        jsonResponse(['success' => false, 'error' => 'Les mots de passe ne correspondent pas'], 400);
    }

    if (strlen($password) < 8) {
        jsonResponse(['success' => false, 'error' => 'Le mot de passe doit contenir au moins 8 caractères'], 400);
    }

    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare('SELECT id FROM users WHERE email = ?');
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        jsonResponse(['success' => false, 'error' => 'Cet email est déjà utilisé'], 400);
    }

    try {
        $hashedPassword = hashPassword($password);
        $stmt = $db->prepare('INSERT INTO users (nom, prenom, email, telephone, adresse, password, role, date_inscription) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())');
        $stmt->execute([
            $name,
            $manager,
            $email,
            $telephone,
            $address,
            $hashedPassword,
            'pharmacie'
        ]);

        $userId = $db->lastInsertId();

        $stmt = $db->prepare('INSERT INTO pharmacies (nom, adresse, telephone, email, horaire_ouverture, horaire_fermeture, pharmacien_id, date_inscription) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())');
        $stmt->execute([
            $name,
            $address,
            $telephone,
            $email,
            $openingHours ?: null,
            $closingHours ?: null,
            $userId
        ]);

        $pharmacyId = $db->lastInsertId();

        logAction($_SESSION['user_id'], 'pharmacy_registered', ['pharmacy_id' => $pharmacyId, 'user_id' => $userId]);

        jsonResponse([
            'success' => true,
            'message' => 'Pharmacie enregistrée avec succès',
            'pharmacy' => [
                'id' => $pharmacyId,
                'nom' => $name,
                'adresse' => $address,
                'telephone' => $telephone,
                'email' => $email,
                'horaire_ouverture' => $openingHours,
                'horaire_fermeture' => $closingHours,
                'manager' => $manager
            ]
        ]);
    } catch (Exception $e) {
        error_log('Erreur enregistrement pharmacie: ' . $e->getMessage());
        jsonResponse(['success' => false, 'error' => 'Erreur lors de l\'enregistrement de la pharmacie'], 500);
    }
}

else if ($action === 'list') {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare('SELECT p.id, p.nom, p.adresse, p.ville, p.telephone, p.email, p.horaire_ouverture, p.horaire_fermeture, p.pharmacien_id, 
                         CONCAT(u.prenom, " ", u.nom) AS pharmacien_nom, u.id AS user_id 
                         FROM pharmacies p 
                         LEFT JOIN users u ON p.pharmacien_id = u.id 
                         ORDER BY p.date_inscription DESC');
    $stmt->execute();
    $pharmacies = $stmt->fetchAll();

    jsonResponse(['success' => true, 'pharmacies' => $pharmacies]);
}

else if ($action === 'create') {
    requireAuth();
    requireAdmin();

    $nom = trim(getPost('nom'));
    $adresse = trim(getPost('adresse'));
    $ville = trim(getPost('ville'));
    $telephone = trim(getPost('telephone'));
    $email = trim(getPost('email'));
    $horaire_ouverture = trim(getPost('horaire_ouverture'));
    $horaire_fermeture = trim(getPost('horaire_fermeture'));

    $pharmacien_nom = trim(getPost('pharmacien_nom'));
    $pharmacien_prenom = trim(getPost('pharmacien_prenom'));
    $pharmacien_email = trim(getPost('pharmacien_email'));
    $pharmacien_telephone = trim(getPost('pharmacien_telephone'));
    $pharmacien_password = getPost('pharmacien_password');
    $pharmacien_password_confirm = getPost('pharmacien_password_confirm');

    if (empty($nom) || empty($adresse) || empty($pharmacien_nom) || empty($pharmacien_prenom) || empty($pharmacien_email) || empty($pharmacien_password) || empty($pharmacien_password_confirm)) {
        jsonResponse(['success' => false, 'error' => 'Tous les champs du pharmacien sont requis'], 400);
    }

    if (!isValidEmail($pharmacien_email)) {
        jsonResponse(['success' => false, 'error' => 'Email pharmacien invalide'], 400);
    }

    if ($pharmacien_password !== $pharmacien_password_confirm) {
        jsonResponse(['success' => false, 'error' => 'Les mots de passe du pharmacien ne correspondent pas'], 400);
    }

    if (strlen($pharmacien_password) < 8) {
        jsonResponse(['success' => false, 'error' => 'Le mot de passe du pharmacien doit contenir au moins 8 caractères'], 400);
    }

    try {
        $db = Database::getInstance()->getConnection();

        $stmt = $db->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$pharmacien_email]);
        if ($stmt->fetch()) {
            jsonResponse(['success' => false, 'error' => 'Cet email pharmacien existe déjà'], 400);
        }

        $hashedPassword = hashPassword($pharmacien_password);
        $stmt = $db->prepare('INSERT INTO users (nom, prenom, email, telephone, adresse, password, role, date_inscription) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())');
        $stmt->execute([
            $pharmacien_nom,
            $pharmacien_prenom,
            $pharmacien_email,
            $pharmacien_telephone ?: null,
            $adresse,
            $hashedPassword,
            'pharmacie'
        ]);

        $pharmacien_id = $db->lastInsertId();

        $stmt = $db->prepare('INSERT INTO pharmacies (nom, adresse, ville, telephone, email, horaire_ouverture, horaire_fermeture, pharmacien_id, date_inscription) 
                             VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())');
        $stmt->execute([
            $nom,
            $adresse,
            $ville ?: null,
            $telephone ?: null,
            $email ?: null,
            $horaire_ouverture ?: null,
            $horaire_fermeture ?: null,
            $pharmacien_id
        ]);

        $pharmacyId = $db->lastInsertId();

        logAction($_SESSION['user_id'], 'pharmacy_created', ['pharmacy_id' => $pharmacyId, 'pharmacien_id' => $pharmacien_id]);

        jsonResponse([
            'success' => true,
            'message' => 'Pharmacie et compte pharmacien ajoutés avec succès',
            'pharmacy' => [
                'id' => $pharmacyId,
                'nom' => $nom,
                'adresse' => $adresse,
                'ville' => $ville,
                'telephone' => $telephone,
                'email' => $email,
                'horaire_ouverture' => $horaire_ouverture,
                'horaire_fermeture' => $horaire_fermeture,
                'pharmacien_id' => $pharmacien_id
            ]
        ]);
    } catch (Exception $e) {
        error_log('Erreur création pharmacie: ' . $e->getMessage());
        jsonResponse(['success' => false, 'error' => 'Erreur lors de la création de la pharmacie et du compte pharmacien'], 500);
    }
}

else {
    jsonResponse(['success' => false, 'error' => 'Action non reconnue'], 400);
}
?>