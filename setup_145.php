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
     
     // 1. Check if 145 exists
     $row = $pdo->query("SELECT id FROM listings WHERE id = 145")->fetch();
     
     if ($row) {
         echo "Listing 145 already exists. Updating it to be active/approved/success.\n";
         $pdo->query("UPDATE listings SET status = 'active', review_status = 'approved', payment = 'success' WHERE id = 145");
     } else {
         echo "Listing 145 does not exist. Creating it as a shell.\n";
         // Need a valid category_id
         $cat = $pdo->query("SELECT id FROM categories LIMIT 1")->fetch();
         $catId = $cat['id'] ?? 1;
         
         // Insert with ID 145
         $pdo->query("INSERT INTO listings (id, provider_id, category_id, title, description, type, status, review_status, payment, address, city, locality, state, latitude, longitude) 
                      VALUES (145, 1, $catId, 'Test Class 145', 'Self-generated test class for ID 145', 'regular', 'active', 'approved', 'success', '123 Test St', 'Mumbai', 'Andheri', 'Maharashtra', 19.076, 72.877)");
     }
     echo "DONE. Listing 145 should be accessible now locally.\n";

} catch (\PDOException $e) {
     echo "Connection failed: " . $e->getMessage();
}
