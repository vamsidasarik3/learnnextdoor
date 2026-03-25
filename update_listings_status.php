<?php
require 'app/Config/Paths.php';
$paths = new Config\Paths();
require $paths->systemDirectory . '/bootstrap.php';

$db = \Config\Database::connect();
// Keep existing ones for safety if needed, or strictly follow user request.
// "pending, active, rejected"
$db->query("ALTER TABLE listings MODIFY status ENUM('active', 'inactive', 'suspended', 'draft', 'pending', 'rejected') DEFAULT 'pending'");
echo "Listings status updated!";
