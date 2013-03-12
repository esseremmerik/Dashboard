<?php
header('HTTP/1.1 503 Service Unavailable');
header('Content-Type: text/plain; charset=UTF-8');
echo "503 Service Unavailable: " . $_SERVER['REMOTE_ADDR']; 

// log ip address to file
$oFile = fopen("access-log.txt","a");
fwrite($oFile, date("Y-m-d H:i:s: ") . $_SERVER['REMOTE_ADDR'] . "\n");
fclose($oFile);

?>