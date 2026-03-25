<?php
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR);
chdir(__DIR__ . '/public');
$pathsConfig = FCPATH . '../app/Config/Paths.php';
require realpath($pathsConfig) ?: $pathsConfig;
$paths = new Config\Paths();
$bootstrap = rtrim($paths->systemDirectory, '\\/ ') . DIRECTORY_SEPARATOR . 'bootstrap.php';
$app = require realpath($bootstrap) ?: $bootstrap;

$db = \Config\Database::connect();
$db->query("ALTER TABLE categories ADD COLUMN description TEXT AFTER name, ADD COLUMN status ENUM('active', 'inactive') DEFAULT 'active' AFTER description");
echo "Categories table updated.\n";
