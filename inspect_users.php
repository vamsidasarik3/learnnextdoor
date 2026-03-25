<?php
require 'app/Config/Paths.php';
$paths = new Config\Paths();
require $paths->systemDirectory . '/bootstrap.php';

$db = \Config\Database::connect();
$fields = $db->getFieldData('users');
foreach ($fields as $field) {
    echo $field->name . " (" . $field->type . ")\n";
}
