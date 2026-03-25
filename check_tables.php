<?php
define('FCPATH', __DIR__ . '/public/');
require 'vendor/autoload.php';
$db = \Config\Database::connect();
$tables = $db->listTables();
print_r($tables);
