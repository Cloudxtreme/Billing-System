<?php
require("config.php");
header('Content-type: text/html; charset=utf-8');
mysql_connect($mysql_host,$mysql_user,$mysql_pass) or die("Could not establish a connection to mysql!");
mysql_query("set names utf8") or die("Could not change the characterset!");
mysql_query("use ".$database) or die("Could not change the database!");

// Check what the user wants to do.
switch( $_GET['func'] ){
	case "getcust":
		$result = mysql_query("select * from ".$custtable." ORDER BY  `".$custtable."`.`user_name_rubi` ");
		$i = 0;
		while($row = mysql_fetch_assoc($result)){
			$custinfo[$i] = $row;
			$i++;
		}
		$custinfojson = json_encode($custinfo);
		echo $custinfojson;
		break;
	case "getid":
		$result = mysql_query("show table status like '".$custtable."'");
		$row = mysql_fetch_assoc($result);
		echo $row['Auto_increment'];
		break;
	case "newcust":
		$query = "insert into ".$custtable." values ('','";
		$query .= $_GET['customername']."', '";
		$query .= $_GET['customernamefuri']."', '";
		$query .= $_GET['line']."', '";
		$query .= $_GET['billdate']."')";
		mysql_query($query);
		break;
	case "editcust":
		$query = "update ".$custtable." set ";
		$query .= "user_name = '".$_GET['customername']."', ";
		$query .= "user_name_rubi = '".$_GET['customernamefuri']."', ";
		$query .= "line_used = '".$_GET['line']."', ";
		$query .= "bill_date = '".$_GET['billdate']."'";
		$query .= " where user_id = ".$_GET['userid'];
		echo $query;
		mysql_query($query) or die("Could not update the customer information!");
		
		break;
	case "deletecust":
		$query = "delete from ".$custtable." where user_id = ".$_GET['user_id'];
		echo mysql_query($query);
		break;
}

mysql_close();
?>