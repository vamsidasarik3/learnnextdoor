<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Mocking required CI4 environment to run code
require 'vendor/autoload.php';
// We just want to check the DB query
$mysqli = new mysqli('localhost', 'root', '', 'custom_new');
if ($mysqli->connect_error) {
    die('Connect Error: ' . $mysqli->connect_error);
}

$userId = 1; // Assuming a user ID

$sql = "SELECT instructor_name, instructor_experience, instructor_social_link 
        FROM listings 
        WHERE provider_id = $userId 
        AND instructor_kyc_status = 'verified' 
        GROUP BY instructor_name";

$res = $mysqli->query($sql);
if (!$res) {
    echo "SQL Error: " . $mysqli->error . PHP_EOL;
} else {
    echo "Query OK. Found " . $res->num_rows . " instructors." . PHP_EOL;
    while($row = $res->fetch_assoc()) {
        print_r($row);
    }
}
