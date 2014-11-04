<?php
require("config.php");
mysql_connect($mysql_host,$mysql_user,$mysql_pass);
mysql_query("use ".$database) or die("Could not select database!");
mysql_query("set names utf8");

// If there are no values for the following set to corresponding value for MySQL query.
if ($_GET['ch_num'] == ""){$_GET['ch_num'] = "null";}
if ($_GET['out_time_landline'] == ""){$_GET['out_time_landline'] = "null";}
if ($_GET['out_rate_landline'] == ""){$_GET['out_rate_landline'] = "null";}
if ($_GET['out_time_mobile'] == ""){$_GET['out_time_mobile'] = "null";}
if ($_GET['out_rate_mobile'] == ""){$_GET['out_rate_mobile'] = "null";}
if ($_GET['in_time_landline'] == ""){$_GET['in_time_landline'] = "null";}
if ($_GET['in_rate_landline'] == ""){$_GET['in_rate_landline'] = "null";}
if ($_GET['in_time_mobile'] == ""){$_GET['in_time_mobile'] = "null";}
if ($_GET['in_rate_mobile'] == ""){$_GET['in_rate_mobile'] = "null";}
if ($_GET['servicein_date'] == ""){$_GET['servicein_date'] = "0000-00-00";}

// Check if user wants to add a new number or edit an existing one.
switch($_GET['func']){
	case "newnumber":
		$query = "show table status like 'phonenumber_information'";
		$result = mysql_query($query);
		$status = mysql_fetch_assoc($result);
		$newid = $status['Auto_increment'];
		
		$query = "insert into phonenumber_information values (";
		$query .= $newid.",";
		$query .= $_GET['user_id'].",";
		$query .= $_GET['line_id'].",";
		$query .= "'".$_GET['location_name']."',"; 
		$query .= "'".$_GET['back_num']."',";
		$query .= "'".$_GET['notice_num']."',";
		$query .= "'".$_GET['gw_name']."',";
		$query .= "'".$_GET['gw_ex_num']."',";
		$query .= "'".$_GET['gw_prfix_num']."',";
		$query .= $_GET['ch_num'].",";
		$query .= $_GET['out_time_landline'].",";
		$query .= $_GET['out_rate_landline'].",";
		$query .= $_GET['out_time_mobile'].",";
		$query .= $_GET['out_rate_mobile'].",";
		$query .= $_GET['in_time_landline'].",";
		$query .= $_GET['in_rate_landline'].",";
		$query .= $_GET['in_time_mobile'].",";
		$query .= $_GET['in_rate_mobile'].",";
		$query .= "'".$_GET['servicein_date']."',";
		$query .= "'".$_GET['memo']."',";
		$query .= "'".$_GET['use']."')";
		break;
	case "editnumber":
		$query = "update phonenumber_information set ";
		$query .= "`line_id` = '".$_GET['line_id']."',";
		$query .= "`location_name` = '".$_GET['location_name']."',"; 
		$query .= "`back_num` = '".$_GET['back_num']."',";
		$query .= "`notice_num` = '".$_GET['notice_num']."',";
		$query .= "`gw_name` = '".$_GET['gw_name']."',";
		$query .= "`gw_ex_num` = '".$_GET['gw_ex_num']."',";
		$query .= "`gw_prfix_num` = '".$_GET['gw_prfix_num']."',";
		$query .= "`ch_num` = '".$_GET['ch_num']."',";
		$query .= "`out_time_landline` = '".$_GET['out_time_landline']."',";
		$query .= "`out_rate_landline` = '".$_GET['out_rate_landline']."',";
		$query .= "`out_time_mobile` = '".$_GET['out_time_mobile']."',";
		$query .= "`out_rate_mobile` = '".$_GET['out_rate_mobile']."',";
		$query .= "`in_time_landline` = '".$_GET['in_time_landline']."',";
		$query .= "`in_rate_landline` = '".$_GET['in_rate_landline']."',";
		$query .= "`in_time_mobile` = '".$_GET['in_time_mobile']."',";
		$query .= "`in_rate_mobile` = '".$_GET['in_rate_mobile']."',";
		$query .= "`servicein_date` = '".$_GET['servicein_date']."',";
		$query .= "`memo` = '".$_GET['memo']."',";
		$query .= "`use` = '".$_GET['use']."' ";
		$query .= "where `phonenumber_id` = '".$_GET['phonenumber_id']."'";
		break;
	case "delete":
		$query = "delete from phonenumber_information where phonenumber_id = '".$_GET['phonenumber_id']."'";
		break;
}	

echo $query;
echo mysql_query($query) or die("There was a problem with the query!");

mysql_close();
?>