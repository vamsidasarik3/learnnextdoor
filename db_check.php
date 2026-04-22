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
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
     $row = $pdo->query("SELECT MAX(id) as max_id, COUNT(*) as total FROM listings")->fetch();
     echo "Listing Stats:\n";
     print_r($row);
     
     $recent = $pdo->query("SELECT id, title, status, review_status, payment FROM listings ORDER BY id DESC LIMIT 5")->fetchAll();
     echo "\nRecent Listings:\n";
     print_r($recent);

} catch (\PDOException $e) {
     echo "Connection failed: " . $e->getMessage();
}
