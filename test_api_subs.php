<?php
require 'public/index.php';
$provider = new \App\Controllers\Provider();
// We can't easily call the controller method directly without HTTP request in CI4 sometimes, 
// but we can simulate the request object.
$_GET['category_id'] = 5;
$res = $provider->getSubcategories();
echo "Status Code: " . $res->getStatusCode() . "\n";
echo "Body: " . $res->getBody() . "\n";
