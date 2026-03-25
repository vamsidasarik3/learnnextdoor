<?php
$conn = mysqli_connect("localhost", "root", "", "custom_new");
$res = mysqli_query($conn, "SELECT id, title, latitude, longitude, status, review_status, payment FROM listings WHERE id = 1");
print_r(mysqli_fetch_assoc($res));
mysqli_close($conn);
