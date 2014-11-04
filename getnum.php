<?php
require("config.php");
header('Content-type: text/html; charset=utf-8');
$_SESSION['customerid'] = $_GET['customerid'];
if ( isset($_GET['line_id']) ){
	switch($_GET['line_id']){
		case 'softbank':
			$line_id = "1";
			break;
		case "ncom":
			$line_id = "2";
			break;
	}
}

mysql_connect($mysql_host,$mysql_user,$mysql_pass);
mysql_query("set names utf8") or die("Could not change character set!");
mysql_query("use ".$database) or die("Could not select database!");

// Construct the query to get numbers according to customer id, line id.
$query = "select * from ".$numtable." where `user_id` = '".$_SESSION['customerid']."'";
$query .= (isset($_GET['line_id'])) ? " and `line_id` = '".$line_id."'" : "" ;
$query .= "  order by notice_num";
$result = mysql_query($query);
$i = 0;
while($number = mysql_fetch_assoc($result)){
	$numbers[$i] = $number;
	$i++;
}
$numbers_json = json_encode($numbers);
echo json_encode($numbers);
mysql_close();
?>