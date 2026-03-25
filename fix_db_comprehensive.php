<?php
// fix_db_comprehensive.php
$mysqli = new mysqli("localhost", "root", "", "custom_new");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// 1. Listings table
echo "Checking 'listings' table..." . PHP_EOL;
$res = $mysqli->query("SHOW TABLES LIKE 'listings'");
if ($res->num_rows == 0) {
    echo "Creating 'listings' table..." . PHP_EOL;
    $mysqli->query("CREATE TABLE listings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        provider_id INT NOT NULL,
        category_id INT NOT NULL,
        title VARCHAR(255) NOT NULL,
        description TEXT NOT NULL,
        type ENUM('regular', 'workshop', 'course') NOT NULL,
        address TEXT,
        state VARCHAR(100),
        latitude DECIMAL(10, 8),
        longitude DECIMAL(11, 8),
        price DECIMAL(10, 2) DEFAULT 0,
        price_breakdown TEXT,
        free_trial TINYINT(1) DEFAULT 0,
        registration_end_date DATE,
        early_bird_date DATE,
        early_bird_slots INT,
        early_bird_price DECIMAL(10, 2),
        experience_details TEXT,
        linkedin_url VARCHAR(255),
        portfolio_url VARCHAR(255),
        start_date DATE,
        end_date DATE,
        class_time TIME,
        class_end_time TIME,
        status ENUM('active', 'inactive', 'paused') DEFAULT 'inactive',
        review_status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
        admin_remarks TEXT,
        total_students INT DEFAULT 0,
        max_students INT,
        batches LONGTEXT,
        created_at DATETIME,
        updated_at DATETIME
    )");
} else {
    $toAdd = [
        'max_students' => 'INT NULL',
        'batches'      => 'LONGTEXT NULL',
        'free_trial'   => 'TINYINT(1) DEFAULT 0',
        'review_status' => "ENUM('pending', 'approved', 'rejected') DEFAULT 'pending'",
        'admin_remarks' => 'TEXT NULL',
        'total_students' => 'INT DEFAULT 0'
    ];
    $result = $mysqli->query("SHOW COLUMNS FROM listings");
    $existing = [];
    while ($row = $result->fetch_assoc()) { $existing[] = $row['Field']; }
    foreach ($toAdd as $col => $def) {
        if (!in_array($col, $existing)) {
            echo "Adding column to listings: $col" . PHP_EOL;
            $mysqli->query("ALTER TABLE listings ADD COLUMN $col $def");
        }
    }
}

// 2. Listing Images table
echo "Checking 'listing_images' table..." . PHP_EOL;
$res = $mysqli->query("SHOW TABLES LIKE 'listing_images'");
if ($res->num_rows == 0) {
    echo "Creating 'listing_images' table..." . PHP_EOL;
    $mysqli->query("CREATE TABLE listing_images (
        id INT AUTO_INCREMENT PRIMARY KEY,
        listing_id INT NOT NULL,
        image_path VARCHAR(255) NOT NULL,
        position INT DEFAULT 0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");
}

// 3. Listing Availabilities table
echo "Checking 'listing_availabilities' table..." . PHP_EOL;
$res = $mysqli->query("SHOW TABLES LIKE 'listing_availabilities'");
if ($res->num_rows == 0) {
    echo "Creating 'listing_availabilities' table..." . PHP_EOL;
    $mysqli->query("CREATE TABLE listing_availabilities (
        id INT AUTO_INCREMENT PRIMARY KEY,
        listing_id INT NOT NULL,
        available_date DATE NOT NULL,
        available_time TIME NOT NULL,
        is_disabled TINYINT(1) DEFAULT 0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");
}

$mysqli->close();
echo "Comprehensive fix complete." . PHP_EOL;
