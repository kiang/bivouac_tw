<?php
$fh = fopen(__DIR__ . '/points.csv', 'w');
$headerDone = false;
for($i = 1; $i < 200; $i++) {
  $result = getPage($i);
  foreach($result AS $line) {
    if(false === $headerDone) {
      fputcsv($fh, array_keys($line));
      $headerDone = true;
    }
    fputcsv($fh, $line);
  }
}

function getPage($pageNum) {
  $tmpFile = __DIR__ . '/tmp/page_' . $pageNum;
  if(!file_exists($tmpFile)) {
    file_put_contents($tmpFile, file_get_contents('http://61.60.100.23/TTE/index.jsp?PNO01=' . $pageNum));
  }
  $html = file_get_contents($tmpFile);
  $pos = strpos($html, '<tr class="table_7">');
  $html = substr($html, $pos);
  $lines = explode('</tr>', $html);
  $header = false;
  $result = array();
  foreach($lines AS $line) {
    $cols = explode('</td>', $line);
    if(count($cols) === 9) {
      array_pop($cols);
      foreach($cols AS $k => $v) {
        $cols[$k] = trim(strip_tags($v));
      }
      if($cols[0] !== '露營區名稱') {
        $result[] = array_combine($header, $cols);
      } elseif($header === false) {
        $header = $cols;
      }
    }
  }
  return $result;
}
