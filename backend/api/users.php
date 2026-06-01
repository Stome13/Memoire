<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

requireAuth();

$action = getPost('action') ?? getGet('action');

// OBTENIR LE PROFIL
if ($action === 'getProfile') {
    $user = getCurrentUser();
    if ($user) {
        jsonResponse(['success' => true, 'user' => $user]);
    } else {
        jsonResponse(['success' => false, 'error' => 'Utilisateur non trouvé'], 404);
    }
}

// LISTER LES UTILISATEURS
else if ($action === 'list') {
    requireAdmin();
    $db = Database::getInstance()->getConnection();
    $role = getGet('role');
    
    if ($role) {
        $stmt = $db->prepare('SELECT id, nom, prenom, email, telephone, adresse, role, date_inscription FROM users WHERE role = ? ORDER BY date_inscription DESC');
        $stmt->execute([$role]);
    } else {
        $stmt = $db->prepare('SELECT id, nom, prenom, email, telephone, adresse, role, date_inscription FROM users ORDER BY date_inscription DESC');
        $stmt->execute();
    }
    
    $users = $stmt->fetchAll();
    jsonResponse(['success' => true, 'users' => $users]);
}

// STATISTIQUES GÉNÉRALES
else if ($action === 'stats') {
    requireAdmin();
    $db = Database::getInstance()->getConnection();
    $stats = [];
    $stmt = $db->prepare('SELECT COUNT(*) as total FROM users');
    $stmt->execute();
    $stats['totalUsers'] = (int) $stmt->fetchColumn();

    $stmt = $db->prepare('SELECT COUNT(*) as total FROM users WHERE role = ?');
    $stmt->execute(['client']);
    $stats['totalClients'] = (int) $stmt->fetchColumn();

    $stmt->execute(['pharmacie']);
    $stats['totalPharmacies'] = (int) $stmt->fetchColumn();

    jsonResponse(['success' => true, 'stats' => $stats]);
}

// METTRE À JOUR LE PROFIL
else if ($action === 'updateProfile') {
    $nom = trim(getPost('nom'));
    $prenom = trim(getPost('prenom'));
    $telephone = trim(getPost('telephone'));
    $adresse = trim(getPost('adresse'));

    if (empty($nom) || empty($prenom)) {
        jsonResponse(['success' => false, 'error' => 'Nom et prénom requis'], 400);
    }

    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("UPDATE users SET nom = ?, prenom = ?, telephone = ?, adresse = ? WHERE id = ?");
    $stmt->execute([$nom, $prenom, $telephone, $adresse, $_SESSION['user_id']]);

    logAction($_SESSION['user_id'], 'profile_updated');

    jsonResponse([
        'success' => true,
        'message' => 'Profil mis à jour',
        'user' => [
            'id' => $_SESSION['user_id'],
            'nom' => $nom,
            'prenom' => $prenom,
            'telephone' => $telephone,
            'adresse' => $adresse
        ]
    ]);
}

// CHANGER LE MOT DE PASSE
else if ($action === 'changePassword') {
    $oldPassword = getPost('oldPassword');
    $newPassword = getPost('newPassword');
    $confirmPassword = getPost('confirmPassword');

    if (empty($oldPassword) || empty($newPassword) || empty($confirmPassword)) {
        jsonResponse(['success' => false, 'error' => 'Tous les champs sont requis'], 400);
    }

    if ($newPassword !== $confirmPassword) {
        jsonResponse(['success' => false, 'error' => 'Les nouveaux mots de passe ne correspondent pas'], 400);
    }

    if (strlen($newPassword) < 8) {
        jsonResponse(['success' => false, 'error' => 'Le mot de passe doit contenir au moins 8 caractères'], 400);
    }

    $user = getCurrentUser();
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $result = $stmt->fetch();

    if (!verifyPassword($oldPassword, $result['password'])) {
        jsonResponse(['success' => false, 'error' => 'Ancien mot de passe incorrect'], 401);
    }

    $hashedPassword = hashPassword($newPassword);
    $stmt = $db->prepare("UPDATE users SET password = ? WHERE id = ?");
    $stmt->execute([$hashedPassword, $_SESSION['user_id']]);

    logAction($_SESSION['user_id'], 'password_changed');

    jsonResponse(['success' => true, 'message' => 'Mot de passe changé avec succès']);
}

else {
    jsonResponse(['success' => false, 'error' => 'Action non reconnue'], 400);
}
?>
