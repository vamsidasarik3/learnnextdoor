<?php
require 'app/Config/Paths.php';
$paths = new Config\Paths();
require $paths->systemDirectory . '/bootstrap.php';

$db = \Config\Database::connect();
$query = $db->query("SHOW COLUMNS FROM listings LIKE 'status'");
$row = $query->getRow();
echo "Status Column Definition: " . $row->Type . "\n";
