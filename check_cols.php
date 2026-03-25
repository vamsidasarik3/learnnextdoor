<?php
$mysqli = new mysqli('localhost', 'root', '', 'custom_new');
$res = $mysqli->query('DESCRIBE listings');
while($row = $res->fetch_assoc()) echo $row['Field'] . PHP_EOL;
