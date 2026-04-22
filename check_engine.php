<?php
$db = mysqli_connect('localhost', 'root', '', 'custom_new');
$res = mysqli_query($db, "SHOW VARIABLES LIKE 'default_storage_engine'");
$row = mysqli_fetch_assoc($res);
print_r($row);
mysqli_close($db);
