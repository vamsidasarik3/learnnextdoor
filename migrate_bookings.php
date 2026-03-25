<?php
$host = 'localhost';
$db   = 'custom_new';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
     $pdo->exec("ALTER TABLE bookings ADD COLUMN batch_name VARCHAR(100) DEFAULT NULL AFTER booking_type");
     echo "Column batch_name added successfully!";
} catch (\PDOException $e) {
     if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
         echo "Column already exists.";
     } else {
         echo "Error: " . $e->getMessage();
     }
}
