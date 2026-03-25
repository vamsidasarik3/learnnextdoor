<?php
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR);
chdir(__DIR__ . '/public');
$pathsConfig = FCPATH . '../app/Config/Paths.php';
require realpath($pathsConfig) ?: $pathsConfig;
$paths = new Config\Paths();
$bootstrap = rtrim($paths->systemDirectory, '\\/ ') . DIRECTORY_SEPARATOR . 'bootstrap.php';
$app = require realpath($bootstrap) ?: $bootstrap;

$db = \Config\Database::connect();
echo "--- Categories ---\n";
print_r($db->getFieldData('categories'));
echo "--- Subcategories ---\n";
print_r($db->getFieldData('subcategories'));
