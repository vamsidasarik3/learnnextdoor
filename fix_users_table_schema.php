<?php
require 'app/Config/Paths.php';
$paths = new Config\Paths();
require $paths->systemDirectory . '/bootstrap.php';

$db = \Config\Database::connect();
// 1. Alter users table to allow NULL for email and username
$db->query("ALTER TABLE users MODIFY email VARCHAR(255) NULL");
$db->query("ALTER TABLE users MODIFY username VARCHAR(255) NULL");
// 2. Fix phone to be VARCHAR too for better indexing
$db->query("ALTER TABLE users MODIFY phone VARCHAR(20) NULL");

echo "Table 'users' updated: email, username, and phone are now nullable VARCHARs.\n";
