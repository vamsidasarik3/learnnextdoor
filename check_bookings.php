<?php
$host = 'localhost';
$db   = 'custom_new';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $stmt = $pdo->query("SELECT * FROM bookings WHERE booking_status='confirmed' AND payment_status='paid' LIMIT 5");
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (empty($bookings)) {
        echo "No confirmed/paid bookings found.";
    } else {
        foreach ($bookings as $b) {
            echo "ID: {$b['id']}, Parent: {$b['parent_id']}, Phone: {$b['parent_phone']}, Status: {$b['booking_status']}\n";
        }
    }
} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage();
}
