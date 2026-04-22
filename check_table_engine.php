<?php
$db = mysqli_connect('localhost', 'root', '', 'custom_new');
$res = mysqli_query($db, "SHOW TABLE STATUS WHERE Name='bookings'");
$row = mysqli_fetch_assoc($res);
print_r($row);
mysqli_close($db);
