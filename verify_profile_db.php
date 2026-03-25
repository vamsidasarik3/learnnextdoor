<?php
$host = 'localhost';
$db   = 'custom_new';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute(['testparent_updated@example.com']);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user) {
        echo "USER UPDATED SUCCESSFULLY:\n";
        print_r($user);
    } else {
        echo "USER WITH UPDATED EMAIL NOT FOUND.\n";
        // Check current email if not changed
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute(['testparent@example.com']);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user) {
            echo "USER WITH ORIGINAL EMAIL FOUND:\n";
            print_r($user);
        }
    }
} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage();
}
