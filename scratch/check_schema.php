<?php
$host = 'localhost';
$db   = 'custom_new';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
try {
     $pdo = new PDO($dsn, $user, $pass);
     $res = $pdo->query("DESCRIBE email_verifications");
     while($row = $res->fetch(PDO::FETCH_ASSOC)) {
         print_r($row);
     }
} catch (\PDOException $e) {
     echo "Connection failed: " . $e->getMessage();
}
