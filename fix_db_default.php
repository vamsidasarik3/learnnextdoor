<?php
require 'app/Config/Paths.php';
$paths = new Config\Paths();
require $paths->systemDirectory . '/bootstrap.php';

$db = \Config\Database::connect();
$db->query("ALTER TABLE users MODIFY provider_verification_status ENUM('pending', 'approved', 'rejected') DEFAULT NULL");
echo "DB updated successfully!";
