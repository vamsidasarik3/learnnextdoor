<?php
require 'app/Config/Paths.php';
$paths = new Config\Paths();
require $paths->systemDirectory . '/bootstrap.php';

$db = \Config\Database::connect();
$fields = $db->getFieldNames('whatsapp_logs');
echo implode("\n", $fields);
