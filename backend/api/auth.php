<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

$action = getPost('action') ?? getGet('action');

// INSCRIPTION
if ($action === 'register') {
    $nom = trim(getPost('nom'));
    $prenom = trim(getPost('prenom'));
    $email = trim(getPost('email'));
    $telephone = trim(getPost('telephone'));
    $password = getPost('password');
    $passwordConfirm = getPost('passwordConfirm');

    // Validations
    if (empty($nom) || empty($prenom) || empty($email) || empty($password)) {
        jsonResponse(['success' => false, 'error' => 'Champs obligatoires manquants'], 400);
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

    // Vérifier si email existe déjà
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        jsonResponse(['success' => false, 'error' => 'Cet email est déjà utilisé'], 400);
    }

    // Insérer l'utilisateur
    try {
        $hashedPassword = hashPassword($password);
        $stmt = $db->prepare("INSERT INTO users (nom, prenom, email, telephone, password, role, date_inscription) VALUES (?, ?, ?, ?, ?, 'client', NOW())");
        $stmt->execute([$nom, $prenom, $email, $telephone, $hashedPassword]);
        $userId = $db->lastInsertId();

        // Note: Pas de création de session ici pour obliger l'utilisateur à se connecter
        jsonResponse([
            'success' => true,
            'message' => 'Inscription réussie',
            'user' => [
                'id' => $userId,
                'email' => $email,
                'nom' => $nom,
                'prenom' => $prenom
            ]
        ]);
    } catch (Exception $e) {
        error_log("Erreur inscription: " . $e->getMessage());
        jsonResponse(['success' => false, 'error' => 'Erreur lors de l\'inscription: ' . $e->getMessage()], 500);
    }
}

// CONNEXION
else if ($action === 'login') {
    $email = trim(getPost('email'));
    $password = getPost('password');

    if (empty($email) || empty($password)) {
        jsonResponse(['success' => false, 'error' => 'Email et mot de passe requis'], 400);
    }

    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("SELECT id, nom, prenom, email, password, role FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user || !verifyPassword($password, $user['password'])) {
        logAction(null, 'login_failed', ['email' => $email]);
        jsonResponse(['success' => false, 'error' => 'Email ou mot de passe incorrect'], 401);
    }

    // Connexion réussie
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_role'] = $user['role'];
    $_SESSION['user_nom'] = $user['nom'];
    $_SESSION['user_prenom'] = $user['prenom'];

    logAction($user['id'], 'login_success', ['email' => $email]);

    jsonResponse([
        'success' => true,
        'message' => 'Connexion réussie',
        'user' => [
            'id' => $user['id'],
            'email' => $user['email'],
            'nom' => $user['nom'],
            'prenom' => $user['prenom'],
            'role' => $user['role']
        ]
    ]);
}

// DÉCONNEXION
else if ($action === 'logout') {
    session_destroy();
    jsonResponse(['success' => true, 'message' => 'Déconnexion réussie']);
}

// VÉRIFIER SI L'EMAIL EXISTE
else if ($action === 'checkEmail') {
    $email = trim(getPost('email') ?? '');
    
    if (empty($email)) {
        jsonResponse(['available' => false, 'error' => 'Email vide'], 400);
    }

    if (!isValidEmail($email)) {
        jsonResponse(['available' => false, 'error' => 'Email invalide'], 400);
    }

    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $exists = $stmt->fetch();

    if ($exists) {
        jsonResponse(['available' => false, 'message' => 'Cet email est déjà utilisé']);
    } else {
        jsonResponse(['available' => true, 'message' => 'Email disponible']);
    }
}

// VÉRIFIER LA SESSION
else if ($action === 'check') {
    if (isLoggedIn()) {
        $user = getCurrentUser();
        jsonResponse(['loggedIn' => true, 'user' => $user]);
    } else {
        jsonResponse(['loggedIn' => false]);
    }
}

// ACTION NON RECONNUE
else {
    jsonResponse(['success' => false, 'error' => 'Action non reconnue'], 400);
}

// Gestion globale des erreurs non détectées
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    error_log("Erreur PHP [$errno]: $errstr dans $errfile:$errline");
    jsonResponse(['success' => false, 'error' => 'Erreur serveur: ' . $errstr], 500);
});
?>
