<?php
require 'app/Config/Paths.php';
$paths = new Config\Paths();
require $paths->systemDirectory . '/bootstrap.php';

$db = \Config\Database::connect();
$fields = $db->getFieldNames('bookings');
echo implode("\n", $fields);
