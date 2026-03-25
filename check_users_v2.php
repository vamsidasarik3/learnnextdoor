<?php
$host = 'localhost';
$db   = 'custom_new';
$user = 'root';
$pass = '';
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $stmt = $pdo->query("DESCRIBE users");
    $cols = $stmt->fetchAll(PDO::FETCH_ASSOC);
    print_r($cols);
} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage();
}
