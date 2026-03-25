<?php
require 'vendor/autoload.php';
// Need to manually load CI4 environment to run this if it's not a spark command
// But I'll just use spark to run a seeder that dumps it if needed.
// Actually, let's just use php spark to run a "migration" seeder.

// Or use a simple command to select from DB.
$db = mysqli_connect('localhost', 'root', '', 'class');
if (!$db) {
    die('Connect Error');
}
$res = mysqli_query($db, "SELECT * FROM categories");
while ($row = mysqli_fetch_assoc($res)) {
    echo "Category: " . $row['id'] . " - " . $row['name'] . "\n";
}
mysqli_close($db);
