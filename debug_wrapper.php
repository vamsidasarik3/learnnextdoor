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
         if ($line[$i] == '{') {
            $stack[] = $lNum;
         }
         if ($line[$i] == '}') {
            if (empty($stack)) {
               echo "Extra close brace at line $lNum\n";
               echo "Line content: $line\n";
            } else {
               $start = array_pop($stack);
               if ($start == 616) {
                  echo "Wrapper from line 616 was closed by brace at line $lNum\n";
                  echo "Closing line content: $line\n";
               }
            }
         }
      }
   }
   if (strpos($line, '</script>') !== false) {
      $inJS = false;
      $stack = [];
   }
}
