<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/helpers.php';

// Vérifier que c'est un pharmacien
requireRole('pharmacie');

// Rediriger vers le dashboard
header('Location: dashboard.php');
exit;
?>
