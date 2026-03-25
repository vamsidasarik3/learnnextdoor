<?php
$content = file_get_contents('app/Views/frontend/provider/create_listing.php');
$open = substr_count($content, '{');
$close = substr_count($content, '}');
echo "Braces Count - Open: $open, Close: $close\n";

$openJS = 0; $closeJS = 0; $inJS = false;
$lines = explode("\n", $content);
foreach($lines as $line) {
   if (strpos($line, '<script>') !== false) $inJS = true;
   if (strpos($line, '</script>') !== false) $inJS = false;
   if ($inJS) {
      $openJS += substr_count($line, '{');
      $closeJS += substr_count($line, '}');
   }
}
echo "JS Braces Count - Open: $openJS, Close: $closeJS\n";
