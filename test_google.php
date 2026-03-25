<?php
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR);
$pathsConfig = 'app/Config/Paths.php';
require realpath($pathsConfig) ?: $pathsConfig;
$paths = new Config\Paths();
require $paths->systemDirectory . '/bootstrap.php';

if (file_exists(ROOTPATH . 'vendor/autoload.php')) {
    echo "Found vendor/autoload.php at " . ROOTPATH . "vendor/autoload.php\n";
    require_once ROOTPATH . 'vendor/autoload.php';
    if (class_exists('Google\Client')) {
        echo "Google\Client successfully loaded.\n";
    } else {
        echo "Failed to load Google\Client.\n";
    }
} else {
    echo "vendor/autoload.php not found at " . ROOTPATH . "vendor/autoload.php\n";
}
