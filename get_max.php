<?php
$mysqli = new mysqli('localhost', 'root', '', 'custom_new');
$row = $mysqli->query('SELECT MAX(id) as max FROM listings')->fetch_assoc();
echo 'Max ID: ' . $row['max'] . PHP_EOL;
$mysqli->close();
