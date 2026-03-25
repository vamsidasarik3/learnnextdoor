<?php
$c = mysqli_connect('localhost', 'root', '', 'custom_new');
$res = mysqli_query($c, "SHOW COLUMNS FROM users LIKE 'mailing_list'");
if (mysqli_num_rows($res) == 0) {
    mysqli_query($c, 'ALTER TABLE users ADD COLUMN mailing_list TINYINT(1) DEFAULT 0');
    echo 'Added column mailing_list';
} else {
    echo 'Column already exists';
}
