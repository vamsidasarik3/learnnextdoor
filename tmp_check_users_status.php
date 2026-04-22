<?php
require 'app/Config/Paths.php';
$paths = new Config\Paths();
require $paths->systemDirectory . '/bootstrap.php';

$db = \Config\Database::connect();
$query = $db->query("SELECT id, name, email, status FROM users LIMIT 10");
$users = $query->getResult();
foreach ($users as $user) {
    echo "ID: {$user->id} | Name: {$user->name} | Email: {$user->email} | Status: '{$user->status}' (Type: " . gettype($user->status) . ")\n";
}
