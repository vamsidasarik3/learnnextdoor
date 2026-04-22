<?php
// Simple script to test WhatsApp API directly from .env
function get_env_var($key, $default = '') {
    $content = file_get_contents(__DIR__ . '/.env');
    if (preg_match('/^' . $key . '=(.*)$/m', $content, $m)) {
        return trim($m[1], " \t\n\r\0\x0B\"'");
    }
    return $default;
}

$token = get_env_var('WHATSAPP_TOKEN');
$phoneId = get_env_var('WHATSAPP_PHONE_NUMBER_ID');
$template = get_env_var('WHATSAPP_TEMPLATE_OTP');
$testPhone = '9989284804'; // From logs

echo "Testing WhatsApp API...\n";
echo "Phone ID: $phoneId\n";
echo "Template: $template\n";
echo "Recipient: $testPhone\n";

$url = "https://graph.facebook.com/v19.0/$phoneId/messages";
$otp = rand(100000, 999999);

$payload = [
    'messaging_product' => 'whatsapp',
    'to'                => '91' . $testPhone,
    'type'              => 'template',
    'template'          => [
        'name'     => $template,
        'language' => ['code' => 'en'],
        'components' => [
            [
                'type'       => 'body',
                'parameters' => [['type' => 'text', 'text' => $otp]]
            ],
            [
                'type'       => 'button',
                'sub_type'   => 'url',
                'index'      => '0',
                'parameters' => [['type' => 'text', 'text' => $otp]]
            ]
        ]
    ]
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $token,
    'Content-Type: application/json',
]);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$result = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $code\n";
echo "Response: $result\n";
