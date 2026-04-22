<?php
$mysqli = new mysqli('localhost', 'root', '', 'custom_new');
$res = $mysqli->query('SELECT id, locality, city, address, formatted_address FROM listings WHERE id IN (135, 145, 146)');
while($row = $res->fetch_assoc()) print_r($row);
$mysqli->close();
