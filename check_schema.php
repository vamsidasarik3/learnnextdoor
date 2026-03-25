<?php
$c = mysqli_connect('localhost', 'root', '', 'custom_new');

echo "--- CATEGORIES TABLE ---\n";
$res = mysqli_query($c, "DESCRIBE categories");
while($row = mysqli_fetch_assoc($res)) print_r($row);

echo "\n--- LISTINGS TABLE ---\n";
$res = mysqli_query($c, "DESCRIBE listings");
while($row = mysqli_fetch_assoc($res)) print_r($row);
