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
     $status = $pdo->query("SHOW TABLE STATUS LIKE 'listings'")->fetch();
     echo "Table Status for 'listings':\n";
     print_r($status);
     
     // Check if there are ANY listings where the conditions fail
     $failed = $pdo->query("SELECT id, title, status, review_status, payment FROM listings 
                           WHERE status != 'active' OR review_status != 'approved' OR payment != 'success' 
                           ORDER BY id DESC LIMIT 10")->fetchAll();
     echo "\nListings that would return 404 (if checked):\n";
     print_r($failed);

} catch (\PDOException $e) {
     echo "Connection failed: " . $e->getMessage();
}
