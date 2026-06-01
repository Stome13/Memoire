<?php
/**
 * Debug - Test de l'API et de la configuration
 */

echo "=== DEBUG API ===\n\n";

// Test 1: Inclusion des fichiers
echo "1. TEST D'INCLUSION:\n";
try {
    require_once __DIR__ . '/../includes/config.php';
    echo "   ✓ config.php chargé\n";
} catch (Exception $e) {
    echo "   ✗ config.php: " . $e->getMessage() . "\n";
}

try {
    require_once __DIR__ . '/../includes/db.php';
    echo "   ✓ db.php chargé\n";
} catch (Exception $e) {
    echo "   ✗ db.php: " . $e->getMessage() . "\n";
}

try {
    require_once __DIR__ . '/../includes/functions.php';
    echo "   ✓ functions.php chargé\n";
} catch (Exception $e) {
    echo "   ✗ functions.php: " . $e->getMessage() . "\n";
}

// Test 2: Obtenir l'action
echo "\n2. TEST ACTION:\n";
$action = getPost('action') ?? getGet('action');
echo "   Action reçue: " . ($action ?? 'aucune') . "\n";

// Test 3: Test de checkSession
echo "\n3. TEST SESSION:\n";
if (session_status() === PHP_SESSION_ACTIVE) {
    echo "   ✓ Session active\n";
    echo "   user_id: " . ($_SESSION['user_id'] ?? 'non défini') . "\n";
} else {
    echo "   ✗ Session non active\n";
}

// Test 4: Test base de données
echo "\n4. TEST BASE DE DONNÉES:\n";
try {
    $db = Database::getInstance()->getConnection();
    echo "   ✓ Connexion BD établie\n";
    
    // Vérifier les tables
    $result = $db->query("SHOW TABLES");
    $tables = [];
    while ($row = $result->fetch()) {
        $tables[] = current($row);
    }
    echo "   Tables: " . implode(', ', $tables) . "\n";
} catch (Exception $e) {
    echo "   ✗ Erreur BD: " . $e->getMessage() . "\n";
}

echo "\n=== FIN DEBUG ===\n";
?>
