<?php
require 'app/Config/Paths.php';
$paths = new Config\Paths();
require $paths->systemDirectory . '/bootstrap.php';

$db = \Config\Database::connect();
$forge = \Config\Database::forge();

$fields = [
    'provider_verification_status' => [
        'type'       => 'ENUM',
        'constraint' => ['pending', 'approved', 'rejected'],
        'default'    => 'pending',
        'null'       => true,
    ],
    'provider_verification_message' => [
        'type' => 'TEXT',
        'null' => true,
    ],
    'provider_submitted_at' => [
        'type' => 'TIMESTAMP',
        'null' => true,
    ],
];

echo "Adding columns to users table...\n";
if ($forge->addColumn('users', $fields)) {
    echo "Columns added successfully!\n";
} else {
    echo "Error adding columns.\n";
}
