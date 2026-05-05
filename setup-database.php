<?php
$hostname = 'localhost';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$hostname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Lire le fichier SQL
    $sqlFile = __DIR__ . '/backend/database.sql';
    if (!file_exists($sqlFile)) {
        echo "Fichier SQL non trouvé: $sqlFile";
        exit;
    }
    
    $sql = file_get_contents($sqlFile);
    
    // Exécuter les requêtes
    $conn->exec($sql);
    
    echo "✅ Base de données recréée avec succès!<br>";
    
    // Vérifier les données
    $conn = new PDO("mysql:host=$hostname;dbname=pharmalocal", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $counts = [
        'users' => 0,
        'pharmacies' => 0,
        'medicaments' => 0,
        'stocks' => 0
    ];
    
    foreach ($counts as $table => &$count) {
        $stmt = $conn->query("SELECT COUNT(*) FROM $table");
        $count = $stmt->fetchColumn();
    }
    
    echo "📊 Données insérées:<br>";
    echo "  - Users: " . $counts['users'] . "<br>";
    echo "  - Pharmacies: " . $counts['pharmacies'] . "<br>";
    echo "  - Médicaments: " . $counts['medicaments'] . "<br>";
    echo "  - Stocks: " . $counts['stocks'] . "<br><br>";
    
    // Afficher les utilisateurs
    $stmt = $conn->query("SELECT id, email, role FROM users");
    echo "👤 Utilisateurs:<br>";
    while ($user = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "  - " . $user['email'] . " (" . $user['role'] . ") - ID: " . $user['id'] . "<br>";
    }
    
    echo "<br>💡 Identifiants:<br>";
    echo "  Admin: admin@pharmalokal.com / Admin123!<br>";
    echo "  Pharmacien: pharmacien@pharmalokal.com / Pharmacien123!<br>";
    
} catch (PDOException $e) {
    echo "❌ Erreur: " . $e->getMessage();
}
?>
