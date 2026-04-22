<?php
$db = mysqli_connect('localhost', 'root', '', 'custom_new');
if (!$db) {
    die("Connection failed: " . mysqli_connect_error());
}
$res = mysqli_query($db, "DESCRIBE bookings");
while ($row = mysqli_fetch_assoc($res)) {
    print_r($row);
}
mysqli_close($db);
