<?php
$db = mysqli_connect('localhost', 'root', '', 'custom_new');
$res = mysqli_query($db, "SELECT @@character_set_database, @@collation_database;");
$row = mysqli_fetch_assoc($res);
print_r($row);
mysqli_close($db);
