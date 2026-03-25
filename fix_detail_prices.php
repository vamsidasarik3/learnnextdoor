<?php
$file = "g:/xampp/htdocs/class/public_html/app/Views/frontend/detail.php";
$content = file_get_contents($file);

// Target 1: <span class="badge bg-pink text-white">₹<?= number_format($batch['price'] ?? 0) ?></span>
$target1 = '<span class="badge bg-pink text-white">₹<?= number_format($batch['price'] ?? 0) ?></span>';
$replace1 = '<span class="badge bg-pink text-white">₹<?= number_format($batch['price'] ?? 0) ?> / <?= ($batch['price_type'] ?? "monthly") === "quarterly" ? "Quarter" : "Month" ?></span>';

// Target 2: (₹<?= number_format($batch['price'] ?? 0) ?>)
$target2 = '(₹<?= number_format($batch['price'] ?? 0) ?>)';
$replace2 = '(₹<?= number_format($batch['price'] ?? 0) ?> / <?= ($batch['price_type'] ?? "monthly") === "quarterly" ? "Quarter" : "Month" ?>)';

$newContent = str_replace($target1, $replace1, $content);
$newContent = str_replace($target2, $replace2, $newContent);

if ($newContent !== $content) {
    file_put_contents($file, $newContent);
    echo "Files updated successfully";
} else {
    echo "Replace failed. Targets not found.";
}
