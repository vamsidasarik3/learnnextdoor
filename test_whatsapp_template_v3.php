<?php
/**
 * Test WhatsApp OTP functionality using Templates
 */
require_once __DIR__ . '/vendor/autoload.php';

echo "--- WhatsApp Template OTP Test ---\n";

function get_env_var($key, $default = '') {
    $content = file_get_contents(__DIR__ . '/.env');
    if (preg_match('/^' . $key . '=(.*)$/m', $content, $m)) {
        return trim($m[1], " \t\n\r\0\x0B\"'");
    }
    return $default;
}

$waToken = get_env_var('WHATSAPP_TOKEN');
$waPhoneId = get_env_var('WHATSAPP_PHONE_ID');
$waTemplate = get_env_var('WHATSAPP_TEMPLATE_BOOK', 'learn_next_door_otp');

echo "Token Start: " . substr($waToken, 0, 15) . "...\n";
echo "Phone ID: " . $waPhoneId . "\n";
echo "Template: " . $waTemplate . "\n";

if (!$waToken || !$waPhoneId) {
    die("Error: WHATSAPP_TOKEN or WHATSAPP_PHONE_ID not found in .env\n");
}

// Check for command line argument for phone number
$testPhone = isset($argv[1]) ? $argv[1] : "919010427382"; 
echo "Target Phone: " . $testPhone . "\n";

$otp = rand(100000, 999999);
$url = "https://graph.facebook.com/v19.0/{$waPhoneId}/messages";

$payload = [
    'messaging_product' => 'whatsapp',
    'to'                => $testPhone,
    'type'              => 'template',
    'template'          => [
        'name'     => $waTemplate,
        'language' => ['code' => 'en'],
        'components' => [
            [
                'type'       => 'body',
                'parameters' => [
                    [
                        'type' => 'text',
                        'text' => $otp
                    ]
                ]
            ],
            [
                'type'       => 'button',
                'sub_type'   => 'url',
                'index'      => '0',
                'parameters' => [
                    [
                        'type' => 'text',
                        'text' => $otp
                    ]
                ]
            ]
        ]
    ]
];

// Many OTP templates also use the button parameter for "Copy Code"
// If the above fails, we might need to adjust based on the exact template structure.

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
