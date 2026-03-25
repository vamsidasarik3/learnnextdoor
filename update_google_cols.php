<?php
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR);
$pathsConfig = 'app/Config/Paths.php';
require realpath($pathsConfig) ?: $pathsConfig;
$paths = new Config\Paths();
require $paths->systemDirectory . '/bootstrap.php';

$db = \Config\Database::connect();
// Add provider and email_verified_at
$db->query("ALTER TABLE users ADD COLUMN provider VARCHAR(50) NULL AFTER google_id");
$db->query("ALTER TABLE users ADD COLUMN email_verified_at DATETIME NULL AFTER email_verified");
// Rename google_id to provider_id (optionally, or just use google_id as requested but user said provider_id = google_id so I will rename or add)
$db->query("ALTER TABLE users CHANGE google_id provider_id VARCHAR(255) NULL");

echo "Added provider, provider_id, and email_verified_at to users table.\n";
