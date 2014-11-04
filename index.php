<?php require("config.php"); ?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>ビリングシステム V3</title>
		<link rel="StyleSheet" href="jquery/jquery-ui.css" type="text/css"/>
		<link rel="StyleSheet" href="style.css" type="text/css">
		
		<style type="text/css">
		</style>	

		<script src="jquery/jquery.js"></script>
		<script src="jquery/jquery-ui.js"></script>
		
		<script type="text/javascript">
			var custinfo;
			var populateCust;
			var selectedcustb2 = "";
			var func = "";
			$(document).ready(function(){
				$("#viewcallswindow").hide();
				$("#viewcallswindow").draggable();
				$("#from").datepicker({
					onSelect: function(){
						if ( $(this).val() > $("#to").val() ){$("#to").val($(this).val());}
						$(".data").html("");
					}
				});
				$("#to").datepicker({
					onSelect: function(){
						if ( $(this).val() < $("#from").val() ){$("#from").val($(this).val());}
						$(".data").html("");
					}
				});
				
				// setup dates for datepicker text
				$("#from, #to").datepicker("option", "dateFormat", "yy-mm-dd");
				var today = new Date;
				var date = new Date(today - 1000*60*60*24);
				year = date.getFullYear();
				month = date.getMonth() + 1;
				day = date.getDate();
				$("#from").datepicker("setDate", year + "-" + month + "-01");
				$("#to").datepicker("setDate", year + "-" + month + "-" + day);
				$("#fromtext").html($("#from").val() + " 00:00:00");
				$("#totext").html($("#to").val() + " 23:59:59");
				
				var error = 0;
				$("#fadewlb1, #fadewlb2, #fadewlb3, #fadewlb4, #fadewrb1, #fadewrb2, #fadewrb3, #fadewrb4").hide();
			
				// populate customer drop down menus
				populateCust = function(b1, b2, b3){ var newjax = $.ajax({
					type:"GET",
					url: "customer.php?func=getcust"
					}).done(function() {
						custinfo = jQuery.parseJSON(newjax.responseText);
						
						for(i = 0; i < custinfo.length; i++){
							$("#"+b1+", #"+b2+", #"+b3).append("<option value='" + custinfo[i].user_id + "' index='" + i + "'>" + custinfo[i].user_name + "</option>");
						}
						if ( func == "editcust" ) { $("#customerselectb2 option[value='" + selectedcustb2 + "']").attr("selected", "selected");}
					});
				}
				populateCust("customerselectb1", "customerselectb2", "customerselectb3");
				for(i = 1; i <= 31; i++){
					$("#dateselb2").append("<option value='"+i+"'>"+i+"</option>");
				}
				
				// LOADING ANIMATION FOR BILL FUNCTION
				$("#head").on({
					ajaxStart: function() { 
						if ( loading == 1 ){$(this).addClass("loading");}
					},
					ajaxStop: function() { 
						$(this).removeClass("loading");
					}    
				});
			});
			function OpenWin(){
　　　　　　			win=window.open("./search.php","new","width=1000,height=400,scrollbars=yes,resizable=yes,status=yes,toolbar=yes");
			}
		</script>
	</head>
	<body>
		<div class="shadow" id="head"> <!-- HEAD -->
			<b1>ビリングシステム！</b1>
			<div id="viewcallswindow">
				<div id="callswindowtopbar">
					<button id="tableclosebutton">Close</button>
					<button class="dlcsv">Download</button>
				</div><br>
					<form id="calltableform" name="calltableform" method="post" ><textarea disabled="disabled" id="calltableb1" name="calltableb1" rows="38" cols="72"></textarea><div></div></form>
					</form>
			</div>
			<div class="modal" id="modal2"></div> <!-- END DIV ID 'MODAL' -->
			<div id="logo">
				Calls updated <?php echo file_get_contents("/var/www/html/billing_project/new_v3/cdr_import/updatetime.txt"); ?>.<br>
				Current DB: <?php echo $database; ?>
			</div>
			
			<div id="ver">Ver. 3.4.4</div>
			<div id="toolbar">
				<div class="btn-group">
				<button id="b1"class="btn btn-small"/>明細</button>
				<button id="b2"class="btn btn-small"/>顧客情報</button> 
				<button id="b3"class="btn btn-small"/>電話番号情報</button>
                <button id="b4"class="btn btn-small"/ onClick="OpenWin()">顧客情報検索</button>
				</div>
			</div>
		</div>
		<div class="shadow" id="toolbar2"> <!-- TOOLBAR2 -->
			<div id="headerinfo">
				<table><tr><td width=30></td><td>INFO:&#160;<text id="testarea"></text></td></tr></table>
			</div>
		</div>
		<div class="shadow" id="workspace"> <!-- WORKSPACE -->
				<div id="fadewlb1" class="left"><!--////////// 1 L //////////-->
					<form id="buttonsb1">
					<table border=0 cellPadding=5 id="tablebuttons">
						<tr><td id="customerselectb1text" style="text-align: right;">お客さん: </td><td><select id="customerselectb1"><option value="0">お客様...</option></select><br></td></tr>
						<tr><td id="cdrlinetext" style="text-align: right;">回線: </td><td>
						<div class="btn-group"><input type="radio" name="cdrline" id="softbank"/><label for="softbank">Softbank</label>&#160;&#160;<input type="radio" name="cdrline" id="ncom"/><label for="ncom">Ncom</label></td></div></tr>
						<tr><td id="numberselectb1text" style="text-align: right;">電話番号: </td><td><select id="numberselectb1"><option value="0">電話番号...</option></select></td></tr>
						<tr><td id="in_outtext" style="text-align: right;">Context: </td><td><input type="radio" name="in_out" id="external"/><label for="external">着信</label>&#160;&#160;<input type="radio" name="in_out" id="internal"/><label for="internal">発信</label></td></tr>
						<tr><td id="land_mobiletext" style="text-align: right;">Type: </td><td><input type="radio" name="land_mobile" id="home"/><label for="home">固定</label>&#160;&#160;<input type="radio" name="land_mobile" id="mobile"/><label for="mobile">携帯</label></td></tr>
						<tr><td style="text-align: right;">From: </td><td><b><input type="text" id="from" size="12">&#160;00:00:00</td></tr>
						<tr><td style="text-align: right;">To: </td><td><b><input type="text" id="to" size="12">&#160;23:59:59</td></tr>
					</table>
					</form>
					&#160;&#160;<button id="submitb1">Go!</button>
				</div>
				<div id="fadewrb1" class="right"><!--////////// 1 R //////////-->
					<table>
					<tr><td style="text-align: right;">電話番号: </td><td><b><text id="data1" class="calldata"></text></b></td></tr>
					<tr><td style="text-align: right;">メモ: </td><td><b><text id="data10" class="calldata"></text></b></td></tr>
					<tr><td style="text-align: right;">着信又は発信: </td><td><b><text id="data2" class="calldata"></text></b></td></tr>
					<tr><td style="text-align: right;">顧客名: </td><td><b><text id="data3" class="calldata"></text></b></td></tr>
					<tr><td style="text-align: right;">種類: </td><td><b><text id="data4" class="calldata"></text></b></td></tr>
					<tr><td style="text-align: right;">タリフ: </td><td><b>¥<text id="data5" class="calldata"></text>&#160;/&#160;<text class="calldata" id="data5_2"></text>秒</b></td></tr>
					<tr><td></td></tr>
					<tr><td style="text-align: right;">総通話数: </td><td><b><text id="data6" class="calldata"></text></b></td></tr>
					<tr><td style="text-align: right;">総度数: </td><td><b><text id="data7" class="calldata"></text></b></td></tr>
					<tr><td style="text-align: right;">総通話数: </td><td><b><text id="data8" class="calldata"></text></b></td></tr>
					<tr><td style="text-align: right;"></td><td></td></tr>
					<tr><td style="text-align: right;">通話料合計: </td><td><b>¥<text id="data9" class="calldata"></text> </b></td></tr>
					</table><br>
					CSVをプレビュー! <button id="showcalls">見る!!</button><br>
					CSVをダウンロード！<button id="downloadb1" class="dlcsv" />ダウンロード!</button><br>
				</div>
				<div id="fadewlb2" class="left"><!--////////// 2 L //////////-->
					<table border=0 cellPadding=5 id="tablebuttons">
						<tr><td>お客様: </td><td><select id="customerselectb2" name="customerselectb2" style="width: 200px;"><option value="-1" index="-1">お客さん…</option></select></td></tr>
						<tr><td>Action: </td><td><button class="btn btn-small" id="editb2">Edit</button>&#160;&#160;<button class="btn btn-small" id="deleteb2">Delete</button></td></tr>
						<tr><td></td></tr>
					</table>
					<button class="btn btn-small" id="newcustomer">新しいお客様</button>
				</div>
				<div id="fadewrb2" class="right"><!--////////// 2 R //////////-->
					<form id="custform" name="custformname">
					<table>
						<!--<tr><td style="text-align: right;">User Id:</td> <td><div id="userid" style="display:inline;"></div></td><br></tr>-->
						<tr><td style="text-align: right;"><text class="datab2" id="customernameb2text">Customer (顧客名):</td><td></text><input type="text" id="customernameb2" size=35/></td></tr>
						<tr><td style="text-align: right;">Customer (フリガナ):</td> <td><input type="text" id="customerfurib2" size=35/></td></tr>
						<tr><td style="text-align: right;"><text class="datab2" id="lineb2text">回線種類:</td><td></text> <input type="checkbox" name="lineb2" value="1" id="softbank"><label for="softbank">Softbank</label>&#160;&#160;<input type="checkbox" name="lineb2" value="2" id="ncom"/><label for="ncom">Ncom</label></td></tr>
						<tr><td style="text-align: right;">ビル日:</td> <td><select id="dateselb2"><option id="default" value="0">ビル日...</option></select></td></tr>
					</table>
					</form><br><br>
					<button class="btn btn-small btn-primary" id="submitb2">Submit</button>&#160;&#160;&#160;&#160;&#160;<button class="btn btn-small" id="cancelb2">キャンセル</button>
				</div>
				<div id="fadewlb3" class="left"><!--////////// 3 L //////////-->
					<table border=0 cellPadding=5 id="tablebuttons">
						<tr><td>お客様: </td><td><select id="customerselectb3"><option value="0" userid="">お客さん...</option></select></td></tr><tr><td>電話番語: </td><td><select id="numberselectb3" name="numberselectb3"><option value="0" >電話番語…</option></select></td></tr>
						<tr><td>Action: </td><td><button class="btn btn-small" class="edit" id="editb3">Edit</button>&#160;&#160;<button class="btn btn-small"class="delete" id="deleteb3">Delete</button></td>							</tr>
						<tr></tr>
					</table>
					<button class="btn btn-small"id="newnumberb3">新しい電話番号</button>
					
				</div>
				<div id="fadewrb3" class="right"><!--////////// 3 R //////////-->
					<form id="newnumberform" method="post" name="newnumbername">
					<table id="newnumbertable">
						<tr><td class="tdl" id="errortextb31" style="text-align: right;">Line name<r class="redast">*</r></td><td><input class="datab3" id="softbank" type="radio" name="lineidb3" value="1"><label for="1">Softbank</label><br><input class="datab3" id="ncom" type="radio" name="lineidb3" value="2"><label for="2">Ncom</label><br></tr>
						<tr><td class="tdl"><div id="test" style="text-align: right;">location_name (拠点名): </div></td><td><input class="datab3" id="location_nameb3" name="locationb3" type="text"></td></tr>
						<tr><td class="tdl" id="errortextb32" style="text-align: right;">back_num (裏番号):<r class="redast">*</r> </td><td><input class="datab3" id="back_numb3" name="backnumb3" type="text"></td></tr>
						<tr><td class="tdl" id="errortextb33" style="text-align: right;">notice_num (通知番号):<r class="redast">*</r> </td><td><input class="datab3" id="notice_numb3" name="noticeb3" type="text"></td></tr>
						<tr><td class="tdl" style="text-align: right;">gw_name (Gateway名): </td><td><input class="datab3" id="gw_nameb3" name="gwnameb3" type="text"></td></tr>
						<tr><td class="tdl" style="text-align: right;">gw_ex_num (Gateway内線番号): </td><td><input class="datab3" id="gw_ex_numb3" name="gwexnumb3" type="text" size="5" maxlength="7"></td></tr>
						<tr><td class="tdl" style="text-align: right;">gw_prfix_num (Gateway発信特番): </td><td><input class="datab3" id="gw_prfix_numb3" name="gwprenumb3" type="text" size="5" maxlength="5"></td></tr>
						<tr><td class="tdl" style="text-align: right;">ch_num (契約ch数)</td><td><input class="datab3" id="ch_numb3" name="chnumb3" type="text" size="3" maxlength="3"></td></tr>
						<tr><td class="tdl" style="text-align: right;">Outgoing home rate (発信固定率): </td><td><input class="datab3" id="out_rate_landlineb3" name="olrb3" type="text" maxlength="5" size="5"> ¥ /<input class="datab3" id="out_time_landlineb3" name="olr2" type="text" maxlength="5" size="5"> sec</td></tr>
						<tr><td class="tdl" style="text-align: right;">Outgoing mobile rate (発信携帯率): </td><td><input class="datab3" id="out_rate_mobileb3" name="omrb3" type="text" maxlength="5" size="5"> ¥ /<input class="datab3" id="out_time_mobileb3" name="omr2" type="text" maxlength="5" size="5"> sec</td></tr>
						<tr><td class="tdl" style="text-align: right;">Incoming home rate (着信固定率): </td><td><input class="datab3" id="in_rate_landlineb3" name="ilrb3" type="text" maxlength="5" size="5"> ¥ /<input class="datab3" id="in_time_landlineb3" name="ilr2" type="text" maxlength="5" size="5"> sec</td></tr>
						<tr><td class="tdl" style="text-align: right;">Incoming mobile rate (着信携帯率): </td><td><input class="datab3" id="in_rate_mobileb3" name="imrb3" type="text" maxlength="5" size="5"> ¥ /<input class="datab3" id="in_time_mobileb3" name="imr2" type="text" maxlength="5" size="5"> sec</td></tr>
						<tr><td class="tdl" style="text-align: right;">servicein_date (提供日): </td><td><input class="datab3" id="servicein_dateb3" name="servicedateb3" type="text"></td></tr>
						<tr><td class="tdl" style="text-align: right;">memo (備考): </td><td><input class="datab3" id="memob3" name="memob3" type="text"></td></tr>
						<tr><td class="tdl" style="text-align: right;">use (利用可能): </td><td><input class="datab3" id="yes" type="radio" name="useb3" value="Yes" checked><label for="yes">Yes</label><input class="datab3" id="no" type="radio" name="useb3" value="No"><label for="no">No</label></td></tr>
					</table>
					</form>
					<button class="btn btn-small btn-primary"id="saveb3">Save Changes</button><button class="btn btn-small"id="cancelb3">キャンセル</button>
				</div>
		</div> <!-- END DIV ID 'WORKSPACE'-->
		
		<div id="textbox"> <!-- TEXTBOX -->
		</div>
	</body>
	
	<script src="bs.js"></script>
	<script src="b1.js"></script>
	<script src="b2.js"></script>
	<script src="b3.js"></script>
</html>
