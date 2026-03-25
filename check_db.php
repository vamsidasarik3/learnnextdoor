<?php
require 'vendor/autoload.php';
$config = new \Config\Database();
$db = \Config\Database::connect();
$query = $db->query('DESCRIBE listings');
foreach($query->getResultArray() as $row) {
    echo $row['Field'] . " - " . $row['Type'] . "\n";
}
