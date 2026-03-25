<?php
require 'app/Config/Paths.php';
require 'system/bootstrap.php';
$db = \Config\Database::connect();
try {
    $db->query("ALTER TABLE listings ADD COLUMN price_type VARCHAR(20) DEFAULT 'monthly' AFTER price");
    echo "Done";
} catch (\Exception $e) {
    echo $e->getMessage();
}
