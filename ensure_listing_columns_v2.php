<?php
// g:\xampp\htdocs\class\public_html\ensure_listing_columns_v2.php

$mysqli = new mysqli("localhost", "root", "", "custom_new");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

echo "Checking 'listings' table columns..." . PHP_EOL;

$toAdd = [
    'course_duration'      => 'INT NULL',
    'course_duration_type' => "ENUM('weeks', 'months') DEFAULT 'weeks'",
    'instructor_name'      => 'VARCHAR(255) NULL',
    'instructor_kyc_status' => "ENUM('pending', 'verified', 'rejected') DEFAULT 'pending'",
    'instructor_kyc_doc'   => 'VARCHAR(255) NULL',
    'institute_name'       => 'VARCHAR(255) NULL',
    'manual_address'       => 'TEXT NULL',
    'pincode'              => 'VARCHAR(10) NULL',
    'city'                 => 'VARCHAR(100) NULL',
    'locality'             => 'VARCHAR(100) NULL',
    'formatted_address'    => 'TEXT NULL',
    'registration_end_date' => 'DATE NULL',
    'early_bird_date'      => 'DATE NULL',
    'early_bird_slots'     => 'INT NULL',
    'early_bird_price'     => 'DECIMAL(10, 2) NULL',
];

$result = $mysqli->query("SHOW COLUMNS FROM listings");
$existing = [];
while ($row = $result->fetch_assoc()) {
    $existing[] = $row['Field'];
}

foreach ($toAdd as $col => $def) {
    if (!in_array($col, $existing)) {
        echo "Adding column: $col" . PHP_EOL;
        $mysqli->query("ALTER TABLE listings ADD COLUMN $col $def");
    } else {
        echo "Column '$col' already exists." . PHP_EOL;
    }
}

// 2. Listing Images table
$res = $mysqli->query("SHOW TABLES LIKE 'listing_images'");
if ($res->num_rows == 0) {
    echo "Creating 'listing_images' table..." . PHP_EOL;
    $mysqli->query("CREATE TABLE listing_images (
        id INT AUTO_INCREMENT PRIMARY KEY,
        listing_id INT NOT NULL,
        image_path VARCHAR(255) NOT NULL,
        position INT DEFAULT 0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");
} else {
    echo "Table 'listing_images' already exists." . PHP_EOL;
}

$mysqli->close();
echo "Done." . PHP_EOL;
