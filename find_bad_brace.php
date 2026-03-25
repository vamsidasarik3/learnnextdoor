<?php
$content = file_get_contents('app/Views/frontend/provider/create_listing.php');
$lines = explode("\n", $content);
$balance = 0;
$inJS = false;
foreach($lines as $idx => $line) {
   $lNum = $idx + 1;
   if (strpos($line, '<script>') !== false) $inJS = true;
   if (strpos($line, '</script>') !== false) $inJS = false;
   if ($inJS) {
      $open = substr_count($line, '{');
      $close = substr_count($line, '}');
      $balance += ($open - $close);
      if ($balance < 0) {
         echo "Negative balance at line $lNum: $balance\n";
         echo "Line content: $line\n";
      }
   }
}
echo "Final JS balance: $balance\n";
