<?php
// Script de mise à jour du hash admin

require_once 'backend/includes/config.php';
require_once 'backend/includes/db.php';

try {
    $db = Database::getInstance()->getConnection();
    
    // Mettre à jour le password du compte admin
    $stmt = $db->prepare("UPDATE users SET password = ? WHERE email = ?");
    $stmt->execute([
        '$2y$10$4.J5IzDpgxQ1Yd8ZltVkeeQxyBv4hN7qfGHhkX5D1ChoKpC5xvROe',
        'admin@pharmalokal.com'
    ]);
    
    echo "✅ Hash admin mis à jour avec succès!\n";
    echo "Email: admin@pharmalokal.com\n";
    echo "Password: Admin123!\n";
    echo "Rows affected: " . $stmt->rowCount() . "\n";
    
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}
?>
