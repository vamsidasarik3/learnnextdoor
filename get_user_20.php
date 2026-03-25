<?php
$host = 'localhost';
$db   = 'custom_new';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $stmt = $pdo->prepare("SELECT id, email, phone FROM users WHERE id = ?");
    $stmt->execute([20]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user) {
        print_r($user);
    } else {
        echo "User ID 20 not found.";
    }
} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage();
}
