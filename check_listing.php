<?php
// Simple script to check listing 145 status
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
     $stmt = $pdo->prepare("SELECT id, status, review_status, payment FROM listings WHERE id = ?");
     $stmt->execute([145]);
     $row = $stmt->fetch();

     if ($row) {
         echo "Listing 145 data:\n";
         print_r($row);
         
         if ($row['status'] !== 'active') echo "FAIL: status is " . $row['status'] . " (expected 'active')\n";
         if ($row['review_status'] !== 'approved') echo "FAIL: review_status is " . $row['review_status'] . " (expected 'approved')\n";
         if ($row['payment'] !== 'success') echo "FAIL: payment is " . $row['payment'] . " (expected 'success')\n";
     } else {
         echo "Listing 145 NOT FOUND in database.\n";
     }
} catch (\PDOException $e) {
     echo "Connection failed: " . $e->getMessage();
}
