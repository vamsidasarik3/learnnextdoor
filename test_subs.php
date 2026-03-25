<?php
require 'app/Config/Constants.php';
require 'system/bootstrap.php';
$db = \Config\Database::connect();
$subs = $db->table('subcategories')->get()->getResultArray();
echo "Subcategories Count: " . count($subs) . "\n";
print_r($subs);
