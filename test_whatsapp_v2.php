<?php
/**
 * Test WhatsApp OTP functionality
 */

echo "--- WhatsApp OTP Test ---\n";

function get_env_var($key, $default = '') {
    $content = file_get_contents(__DIR__ . '/.env');
    if (preg_match('/^' . $key . '=(.*)$/m', $content, $m)) {
        return trim($m[1], " \t\n\r\0\x0B\"'");
    }
    return $default;
}

$waToken = get_env_var('WHATSAPP_TOKEN');
$waPhoneId = get_env_var('WHATSAPP_PHONE_ID');

echo "Token Length: " . strlen($waToken) . "\n";
echo "Token Start: " . substr($waToken, 0, 15) . "...\n";
echo "Phone ID: " . $waPhoneId . "\n";

if (!$waToken || !$waPhoneId) {
    die("Error: WHATSAPP_TOKEN or WHATSAPP_PHONE_ID not found in .env\n");
}

$testPhone = "919010427382"; 
if (preg_match('/^\+?91[6-9][0-9]{9}$/', $waPhoneId)) {
    $testPhone = ltrim($waPhoneId, '+');
}

echo "Target Phone: " . $testPhone . "\n";

$otp = rand(100000, 999999);
$url = "https://graph.facebook.com/v19.0/{$waPhoneId}/messages";
$payload = [
    'messaging_product' => 'whatsapp',
    'to'                => $testPhone,
    'type'              => 'text',
    'text'              => [
        'body' => "Your Class Next Door OTP is: *{$otp}*\nValid for 5 minutes. Do not share this with anyone.",
    ],
];

echo "Sending request to: $url\n";

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $waToken,
    'Content-Type: application/json',
]);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$result = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "HTTP Code: $code\n";
echo "Response: $result\n";
if ($error) {
    echo "CURL Error: $error\n";
}
