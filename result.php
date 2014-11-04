<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>検索</title>
	<link rel="StyleSheet" href="jquery/jquery-ui.css" type="text/css"/>
	<link rel="StyleSheet" href="style.css" type="text/css">
		
	<style type="text/css">
		.style2 {font-size: 10px; }
    </style>
    
	<script type="text/javascript">

　　　</script>	
</head>

 
<body>
<Form><Input type=button value="戻る" onClick="javascript:history.go(-1)"></Form>

<?php


try {
	require("config.php");
	header('Content-type: text/html; charset=utf-8');

	$link = mysql_connect($mysql_host,$mysql_user,$mysql_pass);
	if (!$link) {
		die('接続失敗です。'.mysql_error());
	}

	mysql_query("set names utf8") or die("Could not change character set!");
	mysql_query("use ".$database) or die("Could not select database!");

	$db_selected = mysql_select_db($database, $link);
	if (!$db_selected){
		die('データベース選択失敗です。'.mysql_error());
	}


	echo mysql_result($result, 0);

	 $phonenumber = htmlspecialchars($_POST['message']);
	 $customerid = htmlspecialchars($_POST['customerselectb']);
	  
	if(isset($_POST['no2'])){
		//ボタン2の時の処理
		$query = "select * from ".$numtable." where `back_num` LIKE '%".$phonenumber."%'";
		$query .= " OR `notice_num` LIKE '%".$phonenumber."%'";
		echo $phonenumber."の検索結果です。";
	}
	elseif(isset($_POST['no1'])){
		$cstnamesql = "select user_name from ".$custtable." where user_id = '".$customerid."'";
		$cstnameresult = mysql_query($cstnamesql);
		if (!$cstnameresult) {
			die('クエリーが失敗しました。'.mysql_error());
		}
		$cstnamerecord = mysql_fetch_assoc($cstnameresult);
		echo $cstnamerecord['user_name']."様の検索結果です。";
		$query = "select * from ".$numtable." where `user_id` = '".$customerid."'";
	}else{
		echo "全然ちがいます";
	}

	$result = mysql_query($query);
	if (!$result) {
		die('クエリーが失敗しました。'.mysql_error());
	}



	echo "<table  border='1' class='infotbl'>";
		echo "<tr>";
			echo "<th class='wtd1'>番号ID";
			echo "</th>";
			echo "<th class='wtd2'>顧客名";
			echo "</th>";
			echo "<th>回線の種類";
			echo "</th>";
			echo "<th>拠点名";
			echo "</th>";
			echo "<th class='wtd3'>裏番号";
			echo "</th>";
			echo "<th class='wtd3'>通知番号";
			echo "</th>";
			echo "<th>GATEWAY名";
			echo "</th>";
			echo "<th>着信番号";
			echo "</th>";
			echo "<th>発信特番";
			echo "</th>";
			echo "<th class='wtd1'>回線数";
			echo "</th>";
			echo "<th class='wtd1'>発信固定時間";
			echo "</th>";
			echo "<th class='wtd1'>発信固定金額";
			echo "</th>";
			echo "<th class='wtd1'>発信携帯時間";
			echo "</th>";
			echo "<th class='wtd1'>発信携帯金額";
			echo "</th>";
			echo "<th class='wtd1'>着信固定時間";
			echo "</th>";
			echo "<th class='wtd1'>着信固定金額";
			echo "</th>";
			echo "<th class='wtd1'>着信携帯時間";
			echo "</th>";
			echo "<th class='wtd1'>着信携帯金額";
			echo "</th>";
			echo "<th>提供開始日";
			echo "</th>";
			echo "<th class='wtd2'>備考";
			echo "</th>";
			echo "<th class='wtd1'>利用可能";
			echo "</th>";
		echo "</tr>";


	//データをテーブルに表示する
	while ($record = mysql_fetch_assoc($result)){
		$cstnamesql = "select user_name from ".$custtable." where user_id = '".$record['user_id']."'";
		$cstnameresult = mysql_query($cstnamesql);
		if (!$cstnameresult) {
			die('クエリーが失敗しました。'.mysql_error());
		}
		$cstnamerecord = mysql_fetch_assoc($cstnameresult);
		if((strcmp($record['line_id'],'2')) == 0){
			$line = "NCom";
		}elseif((strcmp($record['line_id'],'1')) == 0){
			$line = "SB";
		}elseif((strcmp($record['line_id'],'3')) == 0){
			$line = "FUSION";
		}
		echo "<tr>";
			echo "<td>".$record['phonenumber_id']."</td>";
			echo "<td>".$cstnamerecord['user_name']."</td>";
			echo "<td>".$line."</td>";
			echo "<td>".$record['location_name']."</td>";
			echo "<td>".$record['back_num']."</td>";
			echo "<td>".$record['notice_num']."</td>";
			echo "<td>".$record['gw_name']."</td>";
			echo "<td>".$record['gw_ex_num']."</td>";
			echo "<td>".$record['gw_prfix_num']."</td>";
			echo "<td>".$record['ch_num']."</td>";
			echo "<td>".$record['out_time_landline']."</td>";
			echo "<td>".$record['out_rate_landline']."</td>";
			echo "<td>".$record['out_time_mobile']."</td>";
			echo "<td>".$record['out_rate_mobile']."</td>";
			echo "<td>".$record['in_time_landline']."</td>";
			echo "<td>".$record['in_rate_landline']."</td>";
			echo "<td>".$record['in_time_mobile']."</td>";
			echo "<td>".$record['in_rate_mobile']."</td>";
			echo "<td>".$record['servicein_date']."</td>";
			echo "<td>".$record['memo']."</td>";
			echo "<td>".$record['use']."</td>";
		echo "</tr>";
	}

}catch(Exception $e){
    echo 'システムエラーが発生しました';
	echo $e->getMessage();
}

echo "</table>";

mysql_close($link);

?>

</body>
</html>