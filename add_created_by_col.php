<?php
require 'app/Config/Paths.php';
$paths = new Config\Paths();
require $paths->systemDirectory . '/bootstrap.php';

$db = \Config\Database::connect();
$forge = \Config\Database::forge();

$fields = [
    'created_by_provider_id' => [
        'type'       => 'INT',
        'constraint' => 11,
        'null'       => true,
        'after'      => 'provider_id'
    ]
];

$existingFields = $db->getFieldNames('listings');
if (!in_array('created_by_provider_id', $existingFields)) {
    $forge->addColumn('listings', $fields);
    echo "Column 'created_by_provider_id' added successfully.\n";
} else {
    echo "Column 'created_by_provider_id' already exists.\n";
}
