<?php
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR);
$pathsConfig = 'app/Config/Paths.php';
require realpath($pathsConfig) ?: $pathsConfig;
$paths = new Config\Paths();
require $paths->systemDirectory . '/bootstrap.php';

$db = \Config\Database::connect();
$db->query("ALTER TABLE users ADD COLUMN google_id VARCHAR(255) NULL AFTER password");

echo "Added google_id to users table.\n";
