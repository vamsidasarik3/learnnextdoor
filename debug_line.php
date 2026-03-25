<?php
$content = file_get_contents("g:/xampp/htdocs/class/public_html/app/Views/frontend/detail.php");
$lines = explode("\n", $content);
foreach($lines as $i => $line) {
    if(strpos($line, "number_format(\$batch['price'] ?? 0)") !== false) {
        echo ($i + 1) . ": " . bin2hex($line) . "\n";
    }
}
