<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=pharmalocal', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->prepare('UPDATE users SET password = ? WHERE email = ?')->execute(['$2y$10$yacFOEtDrS0FpDg1xnlr8.VgUTmz5I2LUcInuFTcARUbZ7GDwQLWG', 'admin@pharmalokal.com']);
    $pdo->prepare('UPDATE users SET password = ? WHERE email = ?')->execute(['$2y$10$7vCQxt5DNBM4XLedYy.sWOljx3gpYJj7ffv979GVkVE/1xFYyL0vy', 'pharmacien@pharmalokal.com']);
    echo 'Passwords updated\n';
} catch (Exception $e) {
    echo 'ERROR: ' . $e->getMessage() . "\n";
}
