<?php
/**
 * Gestion de session et authentification
 */

// Session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Gérer les actions GET (logout)
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    logout();
}

/**
 * Vérifier si l'utilisateur est connecté
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Vérifier le rôle de l'utilisateur
 */
function getUserRole() {
    return $_SESSION['user_role'] ?? null;
}

/**
 * Obtenir les données de l'utilisateur
 */
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    return [
        'id' => $_SESSION['user_id'],
        'email' => $_SESSION['user_email'],
        'nom' => $_SESSION['user_nom'] ?? '',
        'prenom' => $_SESSION['user_prenom'] ?? '',
        'role' => $_SESSION['user_role']
    ];
}

/**
 * Redirection selon le rôle
 */
function redirectByRole() {
    $role = getUserRole();
    
    if ($role === 'admin') {
        header('Location: /PharmaLocal/frontend/users/Admin/dashboard.php');
    } elseif ($role === 'pharmacie') {
        header('Location: /PharmaLocal/frontend/users/pharmacien/dashboard.php');
    } else {
        header('Location: /PharmaLocal/frontend/users/clients/profil.php');
    }
    exit;
}

/**
 * Redirection vers login si pas connecté
 */
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: /PharmaLocal/frontend/users/clients/connexion.php');
        exit;
    }
}

/**
 * Redirection si déjà connecté
 */
function requireGuest() {
    if (isLoggedIn()) {
        redirectByRole();
    }
}

/**
 * Vérifier un rôle spécifique
 */
function requireRole($role) {
    requireLogin();
    
    if (getUserRole() !== $role) {
        header('Location: /PharmaLocal/frontend/users/clients/connexion.php');
        exit;
    }
}

/**
 * Redirection vers login avec message
 */
function loginRequired($message = 'Vous devez être connecté pour accéder à cette page') {
    $_SESSION['login_required_message'] = $message;
    header('Location: /PharmaLocal/frontend/users/clients/connexion.php');
    exit;
}

/**
 * Logout
 */
function logout() {
    session_destroy();
    setcookie(session_name(), '', time() - 3600);
    header('Location: /PharmaLocal/frontend/users/clients/connexion.php');
    exit;
}

/**
 * Appel API au backend
 */
function callAPI($endpoint, $method = 'GET', $data = null, $checkSession = true) {
    $url = 'http://localhost/PharmaLocal/backend/api/' . $endpoint;
    
    $options = [
        'http' => [
            'method' => $method,
            'header' => 'Content-Type: application/x-www-form-urlencoded',
            'timeout' => 5
        ]
    ];
    
    if ($method === 'POST' && $data) {
        $options['http']['content'] = http_build_query($data);
    } elseif ($method === 'GET' && $data) {
        $url .= '?' . http_build_query($data);
    }
    
    $context = stream_context_create($options);
    $response = @file_get_contents($url, false, $context);
    
    if ($response === false) {
        return ['success' => false, 'error' => 'Erreur de connexion à l\'API'];
    }
    
    return json_decode($response, true) ?? ['success' => false, 'error' => 'Erreur lors du décodage de la réponse'];
}

/**
 * Obtenir le message de login si présent
 */
function getLoginMessage() {
    $message = $_SESSION['login_required_message'] ?? null;
    unset($_SESSION['login_required_message']);
    return $message;
}
?>
