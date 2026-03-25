<?php
$c = mysqli_connect('localhost', 'root', '', 'custom_new');

if (!$c) {
    die("Connection failed: " . mysqli_connect_error());
}

echo "Creating subcategories table...\n";
$sql = "CREATE TABLE IF NOT EXISTS subcategories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL,
    description TEXT,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
)";

if (mysqli_query($c, $sql)) {
    echo "Subcategories table created successfully.\n";
} else {
    echo "Error creating table: " . mysqli_error($c) . "\n";
}

echo "Adding subcategory_id to listings table...\n";
$checkCol = mysqli_query($c, "SHOW COLUMNS FROM listings LIKE 'subcategory_id'");
if (mysqli_num_rows($checkCol) == 0) {
    $sql = "ALTER TABLE listings ADD COLUMN subcategory_id INT NULL AFTER category_id";
    if (mysqli_query($c, $sql)) {
        echo "subcategory_id column added to listings.\n";
    } else {
        echo "Error adding column: " . mysqli_error($c) . "\n";
    }
} else {
    echo "subcategory_id column already exists.\n";
}

echo "Populating default subcategories...\n";
$res = mysqli_query($c, "SELECT id, name FROM categories");
while ($cat = mysqli_fetch_assoc($res)) {
    $catId = $cat['id'];
    $catName = mysqli_real_escape_string($c, $cat['name']);
    $subName = "General " . $cat['name'];
    $slug = strtolower(str_replace(' ', '-', $subName));
    
    // Check if subcategory already exists
    $checkSub = mysqli_query($c, "SELECT id FROM subcategories WHERE category_id = $catId AND name = '$subName'");
    if (mysqli_num_rows($checkSub) == 0) {
        mysqli_query($c, "INSERT INTO subcategories (category_id, name, slug) VALUES ($catId, '$subName', '$slug')");
        echo "Created default subcategory '$subName' for category '$catName'.\n";
    }
}

echo "Linking existing listings to default subcategories...\n";
$sql = "UPDATE listings l 
        JOIN subcategories s ON s.category_id = l.category_id AND s.name LIKE 'General%'
        SET l.subcategory_id = s.id 
        WHERE l.subcategory_id IS NULL";
if (mysqli_query($c, $sql)) {
    echo "Existing listings updated with default subcategories.\n";
} else {
    echo "Error updating listings: " . mysqli_error($c) . "\n";
}

echo "Migration complete.\n";
mysqli_close($c);
