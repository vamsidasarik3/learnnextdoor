<?php
require 'app/Config/Paths.php';
$paths = new Config\Paths();
require $paths->systemDirectory . '/bootstrap.php';

$db = \Config\Database::connect();
$query = $db->query("SHOW COLUMNS FROM users");
$fields = $query->getResult();
echo "Column | Type | Null | Key | Default | Extra\n";
echo "-------|------|------|-----|---------|------\n";
foreach ($fields as $field) {
    echo "{$field->Field} | {$field->Type} | {$field->Null} | {$field->Key} | " . ($field->Default ?: 'NULL') . " | {$field->Extra}\n";
}
