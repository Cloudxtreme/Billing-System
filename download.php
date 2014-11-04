<?php
//require("config.php");
$header = 'Content-Disposition: attachment; charset=utf-8; filename="'.$_GET['filename'].'"';
header($header);
header('Content-type: text/plain');
$tempname = uniqid().".csv";

$fh = fopen('/var/www/html/billing_project/csv/'.$tempname.'', 'w' );
fputs($fh, $_POST["calltableb1"]);
$exec = "iconv -f UTF-8 -t SHIFT-JIS /var/www/html/billing_project/csv/".$tempname." > /var/www/html/billing_project/csv/".$tempname."1";
shell_exec($exec);
readfile("/var/www/html/billing_project/csv/".$tempname."1");
fclose($fh);
shell_exec("rm -f /var/www/html/billing_project/csv/".$tempname);
shell_exec("rm -f /var/www/html/billing_project/csv/".$tempname."1");

?>