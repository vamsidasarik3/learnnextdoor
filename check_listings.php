<?php
$conn = mysqli_connect("localhost", "root", "", "custom_new");
if (!$conn) die("Connection failed: " . mysqli_connect_error());
$res = mysqli_query($conn, "DESCRIBE listings");
while($row = mysqli_fetch_assoc($res)) {
    echo $row['Field'] . "\n";
}
mysqli_close($conn);
