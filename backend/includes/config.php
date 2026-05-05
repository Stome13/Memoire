<?php
// Configuration de la base de données
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'pharmalocal');
define('DB_PORT', 3306);

// Configuration générale
define('SITE_URL', 'http://localhost/PharmaLocal');
define('API_URL', 'http://localhost/PharmaLocal/backend/api');

// N'envoyer les headers JSON/CORS que pour les scripts API
$isApi = (strpos($_SERVER['SCRIPT_NAME'], '/api/') !== false);
if ($isApi) {
    header('Content-Type: application/json; charset=utf-8');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
    header('Access-Control-Allow-Headers: Content-Type');
    // Session pour API
    if (session_status() === PHP_SESSION_NONE) {
        ini_set('session.gc_maxlifetime', 1800); // 30 minutes
        session_start();
    }
}
// Pour les pages HTML, la session doit être démarrée dans le frontend (session.php)

// Gestion des erreurs
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/errors.log');
?>
