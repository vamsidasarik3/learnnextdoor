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
     $buggy = $pdo->query("SELECT id, title, status, review_status, payment FROM listings 
                          WHERE status = 'active' AND (review_status != 'approved' OR payment != 'success')")->fetchAll();
     echo "Potentially BUGGY Listings (active but failed review or payment):\n";
     print_r($buggy);

} catch (\PDOException $e) {
     echo "Connection failed: " . $e->getMessage();
}
