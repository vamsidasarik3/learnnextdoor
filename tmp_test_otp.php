<?php
$token = 'EAAUN2EvFNiUBRHJizDtrAOBNPiVjWZAT1GwVz3jMj05HHiJkj7IZCQJCgytxVFWC8cu9evBXz42zJ4zLNvKakVfXAaBlBpilt6W23reFLjERfy6vG96zUL9gm6FhMf1LVOp4vddvYz95FIt4bhZAtxudY9YiRHluSHLlUrk6uoCJ5qZAfGrZAlwCylEhhR4xZCotns5RNZAFiiN31sawHLxRWKPpsWvcE2Yqn9QAxXpMENZAHldHbymZAUbINZB3aq7chhBMzBLgzxZAXPZCby2Q4MuE';
$phoneId = '1176671085525437'; // Updated ID from .env
$url = "https://graph.facebook.com/v19.0/$phoneId/messages";

$payload = [
    'messaging_product' => 'whatsapp',
    'to'                => '919989284804',
    'type'              => 'template',
    'template'          => [
        'name'     => 'learnnextdoorv1',
        'language' => ['code' => 'en_US'],
        'components' => [
            [
                'type'       => 'body',
                'parameters' => [
                    [
                        'type' => 'text',
                        'text' => '123456'
                    ]
                ]
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
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "HTTP Code: $code\n";
echo "Response: $response\n";
echo "Error: $error\n";
