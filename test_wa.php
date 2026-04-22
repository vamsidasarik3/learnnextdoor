<?php
// Bootstrap CI4
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR);
require __DIR__ . '/app/Config/Paths.php';
$paths = new Config\Paths();
$bootstrap = $paths->systemDirectory . '/bootstrap.php';
require $bootstrap;

// Ensure header functions are available if needed (NotificationService uses helper('basic'))
helper('basic');

$service = new \App\Services\NotificationService();
$testPhone = '9989284804'; // Recipient from recent logs
echo "--------------------------------------------------\n";
echo "WhatsApp OTP Test Script\n";
echo "Recipient: $testPhone\n";
echo "--------------------------------------------------\n";

$result = $service->sendOtp($testPhone);

if ($result['sent']) {
    echo "SUCCESS: OTP sent via WhatsApp.\n";
} else {
    echo "FAILED: " . $service->getLastError() . "\n";
}
echo "--------------------------------------------------\n";
