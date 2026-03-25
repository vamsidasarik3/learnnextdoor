<?php
$conn = mysqli_connect("localhost", "root", "", "custom_new");
$res = mysqli_query($conn, "SELECT id, title, status, review_status FROM listings LIMIT 10");
while($row = mysqli_fetch_assoc($res)) {
    print_r($row);
}
mysqli_close($conn);
