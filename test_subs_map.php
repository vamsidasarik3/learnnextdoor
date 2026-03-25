<?php
require 'public/index.php';
$db = \Config\Database::connect();
$cats = $db->table('categories')->get()->getResultArray();
foreach($cats as $c) {
    $subs = $db->table('subcategories')->where('category_id', $c['id'])->get()->getResultArray();
    echo "Category: {$c['name']} (ID: {$c['id']}) - Subcategories: " . count($subs) . "\n";
    foreach($subs as $s) {
        echo "  - {$s['name']} (ID: {$s['id']})\n";
    }
}
