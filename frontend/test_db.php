<?php
// Supprime les headers pour le test CLI
if (php_sapi_name() === 'cli') {
    // En mode CLI, on ignore les headers
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

// Test de connexion à la base de données
try {
    // Config DB
    $host = 'localhost';
    $dbname = 'pharmalocal';
    $user = 'root';
    $password = '';
    
    echo "Tentative de connexion...\n";
    echo "Host: $host, DB: $dbname, User: $user\n";
    
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
    echo "✅ Database connected!\n";
    
    $result = $conn->query('SELECT COUNT(*) FROM users');
    $count = $result->fetchColumn();
    echo "✅ Users found: " . $count . "\n";
    
    echo "\n🟢 TOUT FONCTIONNE CORRECTEMENT!\n";
    echo "✅ La plateforme est prête à être utilisée.\n";
    
} catch (PDOException $e) {
    echo "❌ Erreur de connexion DB: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}
?>
