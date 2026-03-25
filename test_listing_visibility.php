<?php
require 'app/Config/Paths.php';
$paths = new Config\Paths();
require $paths->systemDirectory . '/bootstrap.php';

$model = new \App\Models\ListingModel();
// Simulate a request from a specific location (Hyderabad) to see if they match
$results = $model->getByLocation('regular', 17.3850, 78.4867, 25);
echo "Found " . count($results) . " listings.\n";

$logFile = WRITEPATH . 'logs/listing_debug.log';
if (file_exists($logFile)) {
    echo "Log Content:\n" . file_get_contents($logFile);
} else {
    echo "Log file not found.\n";
}
