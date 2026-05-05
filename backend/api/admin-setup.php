<?php
// Admin setup endpoint
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $db = Database::getInstance()->getConnection();
    
    // Check if admin user exists
    $stmt = $db->prepare("SELECT id, email, password, role FROM users WHERE email = ?");
    $stmt->execute(['admin@pharmalokal.com']);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $password = 'Admin123!';
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    
    if ($admin) {
        // Update existing admin with valid hash
        $updateStmt = $db->prepare("UPDATE users SET password = ? WHERE email = ?");
        $updateStmt->execute([$hashedPassword, 'admin@pharmalokal.com']);
        
        echo json_encode([
            'success' => true,
            'message' => 'Admin account updated',
            'email' => 'admin@pharmalokal.com',
            'role' => 'admin',
            'password' => $password,
            'hash_length' => strlen($hashedPassword)
        ]);
    } else {
        // Create new admin user
        $createStmt = $db->prepare("INSERT INTO users (nom, prenom, email, telephone, adresse, password, role) 
                                    VALUES (?, ?, ?, ?, ?, ?, ?)");
        $createStmt->execute([
            'Admin',
            'Système',
            'admin@pharmalokal.com',
            '',
            '',
            $hashedPassword,
            'admin'
        ]);
        
        echo json_encode([
            'success' => true,
            'message' => 'Admin account created',
            'email' => 'admin@pharmalokal.com',
            'role' => 'admin',
            'password' => $password,
            'hash_length' => strlen($hashedPassword)
        ]);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
