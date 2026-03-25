<?php
require 'app/Config/Paths.php';
$paths = new Config\Paths();
require $paths->systemDirectory . '/bootstrap.php';

$model = new \App\Models\ListingModel();
// DELHI coordinates
$results = $model->getByLocation('regular', 28.6139, 77.2090, 25);
echo "Found " . count($results) . " listings in Delhi.\n";

$logFile = WRITEPATH . 'logs/listing_debug.log';
if (file_exists($logFile)) {
    echo "Log Content:\n" . file_get_contents($logFile);
}
