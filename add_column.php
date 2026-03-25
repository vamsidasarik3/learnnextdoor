<?php
require 'vendor/autoload.php';
// Need to bootstrap CodeIgniter
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR);
$loader = require 'vendor/autoload.php';

// This might be tricky to bootstrap CI4 from a script without the full index.php logic.
// Let's try to just use mysqli if I can find the env.
?>
