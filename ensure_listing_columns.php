<?php
// g:\xampp\htdocs\class\public_html\ensure_listing_columns.php

define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR);
chdir(__DIR__);
require 'vendor/autoload.php';

$db = \Config\Database::connect();
$fields = $db->getFieldNames('listings');

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

foreach ($toAdd as $col => $def) {
    if (!in_array($col, $fields)) {
        echo "Adding column: $col" . PHP_EOL;
        $db->query("ALTER TABLE listings ADD COLUMN $col $def");
    } else {
        echo "Column already exists: $col" . PHP_EOL;
    }
}

// Ensure listing_images table exists
if (!$db->tableExists('listing_images')) {
    echo "Creating listing_images table..." . PHP_EOL;
    $db->query("CREATE TABLE listing_images (
        id INT AUTO_INCREMENT PRIMARY KEY,
        listing_id INT NOT NULL,
        image_path VARCHAR(255) NOT NULL,
        position INT DEFAULT 0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");
}

echo "Done." . PHP_EOL;
