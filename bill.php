<?php
require("config.php");
header("Content-Type: application/json; charset=utf-8");
$cust = $_GET["cust"];
$line = $_GET["line"];
$dcontext = $_GET["context"];
$type = $_GET["type"];
if ( !is_null($_GET["gwprefix"]) && trim($_GET["gwprefix"]) != '' && $_GET["gwprefix"] != "null"){
    $gwprefix = explode(";",$gwprefix);
}else{
    $gwprefix = null;
}
$notice_num = $_GET["notice_num"];
$back_num = $_GET['back_num'];
$gw_ext_num = $_GET["gw_ext_num"];
$ratetime = $_GET["ratetime"];
$multipletables = $_GET['multipletables'];
$numberoftables = 0;

// Check if the user selected a date range spanning multiple months.
if ( $_GET['multipletables'] == 0 ){
    $calltable[0] = $_GET['calltable0'];
}
else{
    for( $i = 0; $i < $_GET['numberofcalltables']; $i++){
        $calltable[$i] = $_GET['calltable'.$i];
        $numberoftables++;
    }
}

// Set up the part of the mysql query that checks for dst code.
$ifdst = "";
if ( $gwprefix == '' || is_null($gwprefix) ){
    $ifdst = "184)?";
}
else{
    for ( $i = 0; $i < count($gwprefix); $i++ ){
        $ifdst .= $gwprefix[$i]."|".$gwprefix[$i]."184";
        if ( $i < (count($gwprefix) - 1) ){
            $ifdst .= "|";
        }
    }
    $ifdst .= ")";
}
$notlike = (is_null($gwprefix)) ? "" :$gwprefix;

// Connect to MySQL
mysql_connect($mysql_host,$mysql_user,$mysql_pass) or die("mysql error");
mysql_query("set names utf8") or die("mysql error");
mysql_query("use ".$database) or die("mysql error");

// Load the test numbers from database into $qnum array.
$query = "select * from ".$testnum_table;
$res = mysql_query($query);
for( $i = 0; $row = mysql_fetch_array($res); $i++ ){
    $qnum[$i] = $row[0];
}

// Construct query.
$multquery1 = "(";
$query = "";
for($i = 0; $i < $_GET['numberofcalltables']; $i++){
    $query .= "SELECT ";
    $query .= "`calldate`,";
    $query .= "`end`,";
    $query .= "`src`,";
    $query .= "`clid`,";
    $query .= "`duration`,";
    $query .= "`billsec`,";
    $query .= "`dcontext`,";
    $query .= "`disposition`, ";
    $query .= "`dst`,";
  
    if ($dcontext == 'internal'){
        $query .= "IF(`dst` REGEXP '^(".$ifdst."0(5|7|8|9)0[^0]+', 'mobile', 'home') as `kind`, ";
        $query .= "ceil( `billsec` /".$ratetime." ) AS `billunits` ";
        $query .= "FROM `".$calltable[$i]."` ";
        $query .= "HAVING `src`='".$notice_num."' ";
        $query .= "AND SUBSTR(`clid`,3) = '".$back_num."' ";
        if($notlike == ""){
            $query .= "AND `dst` NOT LIKE '".$notlike."0120%' ";
            $query .= "AND `dst` NOT LIKE '".$notlike."0800%' ";
            $query .= "AND `dst` NOT LIKE '".$notlike."1840120%' ";
            $query .= "AND `dst` NOT LIKE '".$notlike."1840800%' ";
        }else{
            for($k=0;$k < count($notlike);$k++){
                $query .= "AND `dst` NOT LIKE '".$notlike[$k]."0120%' ";
                $query .= "AND `dst` NOT LIKE '".$notlike[$k]."0800%' ";
                $query .= "AND `dst` NOT LIKE '".$notlike[$k]."1840120%' ";
                $query .= "AND `dst` NOT LIKE '".$notlike[$k]."1840800%' ";
            }
        }
        
		//EXCLUDE TEST NUMBERS
        for($j = 0; $j < count($qnum); $j++){
            $query .= "AND `dst` NOT LIKE '%".$qnum[$j]."' ";
        }
    }
    elseif ($dcontext == 'external'){
        $query .= "'$notice_num' as 'pdst',"; //笠井さん依頼分
        $query .= "IF(`src` REGEXP '^0(5|7|8|9)0[^0]+', 'mobile', 'home') as `kind`, ";
        $query .= "ceil( `billsec` /".$ratetime." ) AS `billunits`";
        $query .= "FROM `" . $calltable[$i] . "` ";
        $query .= "having `dst`='".$gw_ext_num."' ";
    }
    $query .= "AND `dcontext` = '".$dcontext."' ";
    $query .= "AND `disposition` = 'ANSWERED' ";
    $query .= "AND `billsec` > 0 ";
    $query .= "AND `kind`='".$type."' ";
    $query .= "ORDER BY `".$calltable[$i]."`.`calldate` ASC";
    
	// Add 'union' to the query if there are multiple tables and it is not the last iteration of the loop.
	if ( $multipletables == 1 && $i != ($_GET['numberofcalltables']-1)){
        $query .= ") union (";
    }
}

try{
    $_fp = fopen("./db.log","a");
    flock($_fp,LOCK_EX);
    fputs($_fp, $query . "\n");
    fclose($_fp);
    $_fp = null;
}
catch(Exception $ex){
}

// Query the database.
$multquery2 = ")";
if ( $multipletables == 0 ){
    $result = mysql_query($query) or die(mysql_error());
}
else{
    $result = mysql_query($multquery1.$query.$multquery2) or die(mysql_error());
}

// Check for duplicates.
$dupes = 0;
$dup = array();
$call_before = array();
$calls_json = array();
for ( $i = 0; $call = mysql_fetch_assoc($result); $i++ ){ 
    $call_now = $call;
    if ( $i != 0 ){
        if ( $i != 0
             && $call_before['calldate'] == $call_now['calldate']
             && $call_before['billsec'] == $call_now['billsec']
             && $call_before['dst'] == $call_now['dst']
             && $call_before['src'] == $call_now['src']
             && $call_before['end'] == $call_now['end']
        ){
            $dupes++;
            $dup[] = $dupes . '|' . $call_before['calldate'] . '|' . $call_before['billsec'] . '|'. $call_before['dst'] . '|' . $call_before['src'];
        }
    }
    $calls_json[$i] = json_encode($call_now);
    $call_before = $call;
}

// If there are duplicates, let AJAX know.
if ( count($calls_json) > 0 && $dupes > 0){
    $response = json_encode($dup);
}
else {
    $response = "[".join(",",$calls_json)."]";
}
echo $response;
mysql_close();

?>
