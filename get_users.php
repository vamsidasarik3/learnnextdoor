<?php
require 'vendor/autoload.php';
// Define required constants
if (!defined('FCPATH')) define('FCPATH', __DIR__ . '/public/');
if (!defined('APPPATH')) define('APPPATH', __DIR__ . '/app/');

// Bootstrap CodeIgniter
require APPPATH . 'Config/Constants.php';

$db = \Config\Database::connect();
$users = $db->table('users')->where('role', 2)->get()->getResultArray();
foreach($users as $user) {
    echo "ID: " . $user['id'] . " | Name: " . $user['name'] . " | Email: " . $user['email'] . " | Phone: " . $user['phone'] . "\n";
}
