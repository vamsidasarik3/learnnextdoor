<?php
require 'public/index.php';
$db = \Config\Database::connect();
$subs = $db->table('subcategories')->get()->getResultArray();
foreach($subs as $s) {
    echo "ID: {$s['id']}, CatID: {$s['category_id']}, Name: {$s['name']}, Status: {$s['status']}\n";
}
