<?php
/**
 * populate_images.php
 * Standalone script to populate listings with high-quality Unsplash images.
 */

// Database config (from .env)
$host = 'localhost';
$db   = 'custom_new';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    die("DB Connection failed: " . $e->getMessage());
}

// Category keywords for Unsplash
$imageMap = [
    'music'     => 'https://images.unsplash.com/photo-1514119412350-e174d90d280e?q=80&w=800',
    'dance'     => 'https://images.unsplash.com/photo-1508700115892-45ecd05ae2ad?q=80&w=800',
    'sports'    => 'https://images.unsplash.com/photo-1471295253337-3ceaaedca402?q=80&w=800',
    'coding'    => 'https://images.unsplash.com/photo-1587620962725-abab7fe55159?q=80&w=800',
    'art'       => 'https://images.unsplash.com/photo-1460661419201-fd4cecdf8a8b?q=80&w=800',
    'tuitions'  => 'https://images.unsplash.com/photo-1434030216411-0b793f4b4173?q=80&w=800',
    'yoga'      => 'https://images.unsplash.com/photo-1544367567-0f2fcb009e0b?q=80&w=800',
    'language'  => 'https://images.unsplash.com/photo-1456513080510-7bf3a84b82f8?q=80&w=800',
    'swimming'  => 'https://images.unsplash.com/photo-1519783301027-440182c578a0?q=80&w=800',
    'chess'     => 'https://images.unsplash.com/photo-1528819622765-d6bcf132f793?q=80&w=800',
    'drawing'   => 'https://images.unsplash.com/photo-1513364776144-60967b0f800f?q=80&w=800',
    'default'   => 'https://images.unsplash.com/photo-1485546246426-74dc88dec4d9?q=80&w=800'
];

$uploadDir = __DIR__ . '/public/uploads/listings/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Fetch all listings and their categories
$sql = "SELECT l.id, l.title, c.name as category_name 
        FROM listings l 
        LEFT JOIN categories c ON c.id = l.category_id";
$listings = $pdo->query($sql)->fetchAll();

echo "Found " . count($listings) . " listings.\n";

foreach ($listings as $row) {
    $listingId = $row['id'];
    $catName   = strtolower($row['category_name'] ?? '');
    
    // Check if listing already has images
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM listing_images WHERE listing_id = ?");
    $stmt->execute([$listingId]);
    if ($stmt->fetchColumn() > 0) {
        echo "Listing $listingId already has images. Skipping.\n";
        continue;
    }

    // Determine keyword
    $keyword = 'default';
    foreach (array_keys($imageMap) as $key) {
        if (strpos($catName, $key) !== false) {
            $keyword = $key;
            break;
        }
    }

    $imageUrl = $imageMap[$keyword];
    $filename = 'listing_' . $listingId . '_cover.jpg';
    $targetPath = $uploadDir . $filename;

    echo "Downloading image for listing $listingId ($keyword)... ";
    
    // Download image
    $imgData = @file_get_contents($imageUrl);
    if ($imgData === false) {
        echo "FAILED to download from $imageUrl\n";
        continue;
    }

    if (file_put_contents($targetPath, $imgData)) {
        // Insert into database
        $stmt = $pdo->prepare("INSERT INTO listing_images (listing_id, image_path, position, created_at) VALUES (?, ?, 0, NOW())");
        $stmt->execute([$listingId, $filename]);
        echo "DONE: $filename\n";
    } else {
        echo "FAILED to save to $targetPath\n";
    }
}

echo "Cleanup: delete script? No, keeping it for now.\n";
