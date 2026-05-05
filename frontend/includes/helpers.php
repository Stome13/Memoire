<?php
/**
 * Fonctions utilitaires pour les pages PHP
 */

/**
 * Escaper une chaîne pour HTML
 */
function escape($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

/**
 * Obtenir les données GET/POST
 */
function getInput($key, $default = null) {
    return $_GET[$key] ?? $_POST[$key] ?? $default;
}

/**
 * Valider un email
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Afficher une alerte Bootstrap
 */
function showAlert($type, $message) {
    echo '<div class="alert alert-' . escape($type) . ' alert-dismissible fade show" role="alert">';
    echo escape($message);
    echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
    echo '</div>';
}

/**
 * Afficher les données du formulaire (pour remplir les inputs)
 */
function old($key, $default = '') {
    return isset($_POST[$key]) ? escape($_POST[$key]) : escape($default);
}
?>
