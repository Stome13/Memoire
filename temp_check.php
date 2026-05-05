<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=pharmalocal', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $pdo->query('SELECT email,password,role FROM users');
    foreach ($stmt as $row) {
        echo $row['email'] . ' => ' . $row['password'] . ' (' . $row['role'] . ')\n';
    }
} catch (Exception $e) {
    echo 'ERROR: ' . $e->getMessage() . "\n";
}
