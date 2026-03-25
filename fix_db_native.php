<?php
// fix_db_native.php
$mysqli = new mysqli("localhost", "root", "", "custom_new");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$toAdd = [
    'max_students' => 'INT NULL',
    'batches'      => 'LONGTEXT NULL',
    'free_trial'   => 'TINYINT(1) DEFAULT 0',
    'review_status' => "ENUM('pending', 'approved', 'rejected') DEFAULT 'pending'",
    'admin_remarks' => 'TEXT NULL',
    'total_students' => 'INT DEFAULT 0'
];

// Get existing columns
$result = $mysqli->query("SHOW COLUMNS FROM listings");
$existing = [];
while ($row = $result->fetch_assoc()) {
    $existing[] = $row['Field'];
}

foreach ($toAdd as $col => $def) {
    if (!in_array($col, $existing)) {
        echo "Adding column: $col" . PHP_EOL;
        if (!$mysqli->query("ALTER TABLE listings ADD COLUMN $col $def")) {
            echo "Error adding $col: " . $mysqli->error . PHP_EOL;
        }
    } else {
        echo "Column already exists: $col" . PHP_EOL;
    }
}

$mysqli->close();
echo "Done." . PHP_EOL;
