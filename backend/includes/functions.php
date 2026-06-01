<?php
require_once __DIR__ . '/db.php';

// Vérifier si l'utilisateur est connecté
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

// Obtenir l'utilisateur connecté
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("SELECT id, nom, prenom, email, role, telephone FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}

// Hash du mot de passe
function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT);
}

// Vérifier le mot de passe
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

// Générer un token
function generateToken() {
    return bin2hex(random_bytes(32));
}

// Valider un email
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL)!== false;
}

// Répondre en JSON
function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

// Vérifier les données POST
function getPost($key, $default = null) {
    $data = json_decode(file_get_contents('php://input'), true);
    if (is_array($data) && isset($data[$key])) {
        return $data[$key];
    }
    return $_POST[$key] ?? $default;
}

// Vérifier les données GET
function getGet($key, $default = null) {
    return $_GET[$key] ?? $default;
}

// Vérifier si l'utilisateur est admin
function isAdmin() {
    return isLoggedIn() && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

function requireAdmin() {
    if (!isAdmin()) {
        jsonResponse(['success' => false, 'error' => 'Accès refusé'], 403);
    }
}

// Loguer les actions
function logAction($userId, $action, $details = null) {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("INSERT INTO logs (user_id, action, details, date_action) VALUES (?, ?, ?, NOW())");
    $stmt->execute([$userId, $action, json_encode($details)]);
}

// Vérifier l'authentification pour les API
function requireAuth() {
    if (!isLoggedIn()) {
        jsonResponse(['error' => 'Authentification requise', 'code' => 401], 401);
    }
}
?>
