<?php
$content = file_get_contents('app/Views/frontend/provider/create_listing.php');
$lines = explode("\n", $content);
$stack = [];
$inJS = false;
foreach($lines as $idx => $line) {
   $lNum = $idx + 1;
   if (strpos($line, '<script>') !== false) $inJS = true;
   if ($inJS) {
      for ($i = 0; $i < strlen($line); $i++) {
         if ($line[$i] == '{') $stack[] = $lNum;
         if ($line[$i] == '}') {
            if (empty($stack)) {
               echo "Extra close brace at line $lNum\n";
               echo "Line content: $line\n";
            } else {
               array_pop($stack);
            }
         }
      }
   }
   if (strpos($line, '</script>') !== false) {
      $inJS = false;
      foreach($stack as $s) echo "Unclosed brace from line $s\n";
      $stack = [];
   }
}
