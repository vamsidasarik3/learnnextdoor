<?php
$c = mysqli_connect('localhost', 'root', '', 'custom_new');
$res = mysqli_query($c, "SHOW COLUMNS FROM users LIKE 'is_verified'");
if (mysqli_num_rows($res) == 0) {
    mysqli_query($c, 'ALTER TABLE users ADD COLUMN is_verified TINYINT(1) DEFAULT 0 AFTER role');
    echo 'Added column is_verified';
} else {
    echo 'Column already exists';
}
