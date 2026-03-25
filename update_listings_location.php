<?php
require 'app/Config/Paths.php';
$paths = new Config\Paths();
require $paths->systemDirectory . '/bootstrap.php';

$db = \Config\Database::connect();
$forge = \Config\Database::forge();

// Add new columns if they don't exist
$fields = [
    'city' => [
        'type' => 'VARCHAR',
        'constraint' => 100,
        'null' => true,
        'after' => 'longitude'
    ],
    'locality' => [
        'type' => 'VARCHAR',
        'constraint' => 255,
        'null' => true,
        'after' => 'city'
    ],
    'pincode' => [
        'type' => 'VARCHAR',
        'constraint' => 20,
        'null' => true,
        'after' => 'locality'
    ],
    'formatted_address' => [
        'type' => 'TEXT',
        'null' => true,
        'after' => 'pincode'
    ],
];

echo "Adding location columns to listings table...\n";
$forge->addColumn('listings', $fields);

// Modify latitude and longitude to specific precision as requested
$modifyFields = [
    'latitude' => [
        'type' => 'DECIMAL',
        'constraint' => '10,8',
        'null' => true
    ],
    'longitude' => [
        'type' => 'DECIMAL',
        'constraint' => '11,8',
        'null' => true
    ],
];
$forge->modifyColumn('listings', $modifyFields);

echo "Database updated successfully!\n";
