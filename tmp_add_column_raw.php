<?php
$mysqli = new mysqli("localhost", "root", "", "custom_new");
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}
if ($mysqli->query("ALTER TABLE listings ADD COLUMN price_type VARCHAR(20) DEFAULT 'monthly' AFTER price")) {
    echo "Column added successfully";
} else {
    echo "Error: " . $mysqli->error;
}
$mysqli->close();
