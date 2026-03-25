<?php
// Script to scan for broken image paths in class listings

$db_host = 'localhost';
$db_name = 'custom_new';
$db_user = 'root';
$db_pass = '';

$public_path = __DIR__ . '/public';
$upload_dir = $public_path . '/uploads/listings';
$placeholder_path = $public_path . '/assets/frontend/img/class-placeholder.jpg';

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

function listing_img_url_local($path, $upload_dir) {
    $path = trim($path);
    if ($path === '') return null;

    // Strip any leading "uploads/listings/" prefix
    $path = preg_replace('#^uploads[/\\\\]listings[/\\\\]#i', '', $path);
    // Also strip a bare "uploads/" prefix
    $path = preg_replace('#^uploads[/\\\\]#i', '', $path);

    // Normalise directory separators
    $path = str_replace('\\', '/', $path);

    return $upload_dir . '/' . $path;
}

$results = [
    'broken_images' => [],
    'summary' => [
        'total_listings_checked' => 0,
        'total_images_checked' => 0,
        'missing_files' => 0,
        'placeholder_missing' => !file_exists($placeholder_path)
    ]
];

// 1. Check listings and their primary images
$stmt = $pdo->query("
    SELECT l.id, l.title, l.status, l.review_status, li.image_path as cover_image
    FROM listings l
    LEFT JOIN listing_images li ON li.listing_id = l.id AND li.position = 0
");

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $results['summary']['total_listings_checked']++;
    
    if ($row['cover_image']) {
        $results['summary']['total_images_checked']++;
        $full_path = listing_img_url_local($row['cover_image'], $upload_dir);
        
        if ($full_path && !file_exists($full_path)) {
            $results['broken_images'][] = [
                'type' => 'Listing Cover',
                'listing_id' => $row['id'],
                'listing_title' => $row['title'],
                'db_path' => $row['cover_image'],
                'expected_file' => str_replace(__DIR__, '', $full_path),
                'page' => 'Classes Listing / Detail'
            ];
            $results['summary']['missing_files']++;
        }
    }
}

// 2. Check all images in listing_images table
$stmt = $pdo->query("
    SELECT li.listing_id, li.image_path, l.title
    FROM listing_images li
    JOIN listings l ON l.id = li.listing_id
    WHERE li.position > 0
");

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $results['summary']['total_images_checked']++;
    $full_path = listing_img_url_local($row['image_path'], $upload_dir);
    
    if ($full_path && !file_exists($full_path)) {
        $results['broken_images'][] = [
            'type' => 'Additional Image',
            'listing_id' => $row['listing_id'],
            'listing_title' => $row['title'],
            'db_path' => $row['image_path'],
            'expected_file' => str_replace(__DIR__, '', $full_path),
            'page' => 'Listing Detail'
        ];
        $results['summary']['missing_files']++;
    }
}

header('Content-Type: application/json');
echo json_encode($results, JSON_PRETTY_PRINT);
