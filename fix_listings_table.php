<?php
// G:\xampp\htdocs\class\public_html\fix_listings_table.php

// This is a crude way to boot CI4 for a standalone script
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR);
chdir(__DIR__);
require 'vendor/autoload.php';

// Mocking some system environment
$config = new \Config\Database();
$db = \Config\Database::connect();

$fields = $db->getFieldNames('listings');

$missing = [];
$toAdd = [
    'max_students' => 'INT NULL',
    'batches'      => 'TEXT NULL',
    'free_trial'   => 'TINYINT(1) DEFAULT 0',
    'review_status' => "ENUM('pending', 'approved', 'rejected') DEFAULT 'pending'",
    'admin_remarks' => 'TEXT NULL',
    'total_students' => 'INT DEFAULT 0'
];

foreach ($toAdd as $col => $def) {
    if (!in_array($col, $fields)) {
        echo "Adding column: $col" . PHP_EOL;
        $db->query("ALTER TABLE listings ADD COLUMN $col $def");
    } else {
        echo "Column already exists: $col" . PHP_EOL;
    }
}

echo "Done." . PHP_EOL;
