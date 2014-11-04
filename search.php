<?php require("config.php"); ?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>検索!!</title>
		<link rel="StyleSheet" href="jquery/jquery-ui.css" type="text/css"/>
		<link rel="StyleSheet" href="style.css" type="text/css">
		
		<style type="text/css">
			.style2 {font-size: 12px; }
        </style>	

		<script src="jquery/jquery.js"></script>
		<script src="jquery/jquery-ui.js"></script>
		
		<script type="text/javascript">
		
		<!--window.resizeTo(1000, 300);-->
		
<!--
function openwin() {	
	window.open("result.php","","width=1000,height=100,scrollbars=yes,resizable=yes,status=yes,toolbar=yes");
}
// -->
				// populate customer drop down menus
				populateCust = function(b4){ var newjax = $.ajax({
					type:"GET",
					url: "customer.php?func=getcust"
					}).done(function() {
						custinfo = jQuery.parseJSON(newjax.responseText);
						
						for(i = 0; i < custinfo.length; i++){
							$("#"+b4).append("<option value='" + custinfo[i].user_id + "' index='" + i + "'>" + custinfo[i].user_name + "</option>");
						}
						
					});
				}
				populateCust("customerselectb4");
				
				// For dates
				for(i = 1; i <= 31; i++){
					$("#dateselb2").append("<option value='"+i+"'>"+i+"</option>");
				}
		</script>
</head>

 
<body>
	<p>各項目ごとに検索してください。</p>

	<form action="result.php" method="post">
	  <table class="searchtbl" border="1">
		<tr>
		  <th  class="stbth">お客様</th>
		  <td align="left"><select name="customerselectb" id="customerselectb4">
		  <option value="0">お客様...</option></select>
		  </td>
		  <td class="stbtd"><input type="submit" name = "no1" value="検索"></td>
		</tr>
		</table></form>
		<br>
		<form action="result.php" method="post">
		  <table class="searchtbl" border="1">
		<tr>
		  <th class="stbth">電話番号
		  </th>
		  <td><input type="text" name="message"></td>
		  <td class="stbtd"><input type="submit" name = "no2" value="検索"></td>
		</tr>
	  </table>
	</form>
</body>
</html>
