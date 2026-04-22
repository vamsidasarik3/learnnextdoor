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
     $row = $pdo->query("SELECT id, title, status, review_status, payment FROM listings WHERE id = 146")->fetch();
     if ($row) {
         echo "Listing 146 data:\n";
         print_r($row);
     } else {
         echo "Listing 146 NOT FOUND locally.\n";
     }
} catch (\PDOException $e) {
     echo "Connection failed: " . $e->getMessage();
}
