<?php
$host = 'localhost';
$db   = 'custom_new';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
try {
     $pdo = new PDO($dsn, $user, $pass);
     $res = $pdo->query("SHOW TABLES LIKE 'listing_availabilities'");
     if ($res->fetch()) {
         echo "Table 'listing_availabilities' EXISTS locally.\n";
     } else {
         echo "Table 'listing_availabilities' MISSING locally.\n";
     }
     
     $res = $pdo->query("SHOW TABLES LIKE 'listing_subcategories'");
     if ($res->fetch()) {
         echo "Table 'listing_subcategories' EXISTS locally.\n";
     } else {
         echo "Table 'listing_subcategories' MISSING locally.\n";
     }
} catch (\PDOException $e) {
     echo "Connection failed: " . $e->getMessage();
}
