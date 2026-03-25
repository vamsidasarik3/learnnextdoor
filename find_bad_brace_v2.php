<?php
$content = file_get_contents('app/Views/frontend/provider/create_listing.php');
$lines = explode("\n", $content);
$balance = 0;
$inJS = false;
foreach($lines as $idx => $line) {
   $lNum = $idx + 1;
   if (strpos($line, '<script>') !== false) $inJS = true;
   if ($inJS) {
      $open = substr_count($line, '{');
      $close = substr_count($line, '}');
      $balance += ($open - $close);
      if ($balance < 0) {
         echo "Balance first negative at line $lNum: $balance\n";
         echo "Line content: $line\n";
         exit;
      }
   }
   if (strpos($line, '</script>') !== false) $inJS = false;
}
echo "Final JS balance: $balance\n";
