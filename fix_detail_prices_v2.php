<?php
$file = "g:/xampp/htdocs/class/public_html/app/Views/frontend/detail.php";
$content = file_get_contents($file);

// Use hex to avoid encoding issues with the Rupee symbol and PHP evaluation
$rupee = hex2bin('e282b9');

// Replace badge price
$target1 = '<span class="badge bg-pink text-white">' . $rupee . '<?php echo number_format($batch[\'price\'] ?? 0) ?></span>';
// Wait, the file has <?=
$target1 = '<span class="badge bg-pink text-white">' . $rupee . '<?=' . ' number_format($batch[\'price\'] ?? 0) ?></span>';

$content = str_replace($target1, '<span class="badge bg-pink text-white">' . $rupee . '<?=' . ' number_format($batch[\'price\'] ?? 0) ?> / <?=' . ' ($batch[\'price_type\'] ?? "monthly") === "quarterly" ? "Quarter" : "Month" ?></span>', $content);

// Replace modal price
$target2 = '(' . $rupee . '<?=' . ' number_format($batch[\'price\'] ?? 0) ?>)';
$content = str_replace($target2, '(' . $rupee . '<?=' . ' number_format($batch[\'price\'] ?? 0) ?> / <?=' . ' ($batch[\'price_type\'] ?? "monthly") === "quarterly" ? "Quarter" : "Month" ?>)', $content);

file_put_contents($file, $content);
echo "Done";
