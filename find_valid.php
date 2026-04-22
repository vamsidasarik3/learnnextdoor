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
     $valid = $pdo->query("SELECT id, title, status, review_status, payment FROM listings 
                          WHERE status = 'active' AND review_status = 'approved' AND payment = 'success' 
                          ORDER BY id DESC LIMIT 5")->fetchAll();
     echo "Valid Listings (should NOT be 404):\n";
     print_r($valid);
     
     $max_id = $pdo->query("SELECT MAX(id) as max FROM listings")->fetch();
     echo "\nAbsolute Max ID: " . $max_id['max'] . "\n";

} catch (\PDOException $e) {
     echo "Connection failed: " . $e->getMessage();
}
