<?php
// Autoload
require 'vendor/autoload.php';

// Mock CI environment if needed, or just use mysqli
$c = mysqli_connect('localhost', 'root', '', 'custom_new');
if (!$c) {
    die("Connection failed: " . mysqli_connect_error());
}

echo "--- LISTINGS TABLE ---\n";
$res = mysqli_query($c, "DESCRIBE listings");
if (!$res) {
    die("Query failed: " . mysqli_error($c));
}
while($row = mysqli_fetch_assoc($res)) {
    echo $row['Field'] . " - " . $row['Type'] . "\n";
}
