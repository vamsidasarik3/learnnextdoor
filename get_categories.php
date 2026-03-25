<?php
$c = mysqli_connect('localhost', 'root', '', 'custom_new');
$res = mysqli_query($c, "SELECT * FROM categories");
while($row = mysqli_fetch_assoc($res)) {
    echo $row['id'] . " | " . $row['name'] . "\n";
}
