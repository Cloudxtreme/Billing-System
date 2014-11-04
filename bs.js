var bpressed = "0";
var timeout = 80;
var anitime = 100;
var database = "line_cdr";
var custtable = "customer_information";
var calltable;
var rateyen;
var ratetime;
var numinfo;
var loading;
var custwrite;
var calls;
var date = new Date();
var year;
var calltableprefix;

//FUNCTION TO CALCULATE TOTALS========================================================================================================
var writeCalls = function(){
	if ( calls ){
		var totalbillsec = 0;
		var billunits = 0;
		var totalcalls = 0;
		var callstext;
		var totalbill2 = 0;
		var thisbillunits = 0;
		var thisbill = 0;
		var prfixs;
                if(numinfo[selectednum].gw_prfix_num != null){
                  prfixs = numinfo[selectednum].gw_prfix_num.split(";");
                }else{
                  prfixs = '';
                }
		var regex = "^(";
		for ( i = 0; i < prfixs.length; i++ ){
			regex += prfixs[i];
			if ( i < (prfixs.length -1) ){
				regex += "|";
			}
		}
		regex += ")";
		var replacetext = new RegExp(regex);
		//var replacetext = new RegExp("^(" + numinfo[selectednum].gw_prfix_num + ")");
		from = $("#from").val();
		to = $("#to").val();
		callstext = "";
		calltabletext = "";
		calltabletext = "利用期間\n" + from + " 〜 " + to + "\n\n";
		calltabletext += "通話回数,通話時間,通話料金\n";
		
		// tally stats
		for (i = 0; i < calls.length; i++){
			if (calls[i].calldate < from + " 00:00:00" || calls[i].calldate > to + " 23:59:59"){continue;}
			totalbillsec += parseInt(calls[i].billsec);
			thisbillunits = Math.ceil(calls[i].billsec/numinfo[selectednum][ratetime]);
			billunits += thisbillunits;
			totalcalls++;
			thisbill = parseFloat((numinfo[selectednum][rateyen]*thisbillunits).toFixed(1));
			totalbill2 += thisbill;
			if ( calls[i].dst ) { calls[i].dst = calls[i].dst.replace(replacetext,""); }
      if (calls[i].pdst){
			    callstext += calls[i].calldate + "," + calls[i].src + "," + calls[i].pdst + "," + calls[i].billsec + "," + thisbillunits + "," + thisbill + "\n";
      }else{
			    callstext += calls[i].calldate + "," + calls[i].src + "," + calls[i].dst + "," + calls[i].billsec + "," + thisbillunits + "," + thisbill + "\n";
      }
		}
		calltabletext += totalcalls + "," + totalbillsec + "," + round(totalbill2,1) + "\n\n";
		calltabletext += "架電日時,発信元,着信先,通話時間,請求度数,通話料\n";
		
		// write stats to page
		$("#data1").html(numinfo[selectednum].notice_num);
		$("#data2").html(selectedcontext);
		$("#data3").html(custinfo[selectedcustb1].user_name);
		$("#data4").html(selectedtype);
		$("#data5").html(numinfo[selectednum][rateyen]);
		$("#data5_2").html(numinfo[selectednum][ratetime]);
		$("#data6, #totalcalls").html(totalcalls);
		$("#data7").html(billunits);
		$("#data8, #totalbillsec").html(totalbillsec);
		$("#data9, #totalbill").html(totalbill2.toFixed(1));
		$("#data10").html(numinfo[selectednum].memo);
		$("#calltableb1").val(callstext);
//		$("#calltableb1").val(calltabletext + callstext);
	}
	else {
		$(".calldata").html(""); 
		$("#testarea").html("There are no calls for this setup!").stop().animate({color:"red"},0).animate({color:"black"},4000); 
	}	
}

//FUNCTION TO GET SELECTED LINE, SET CALL TABLE, AND WRITE NUMBERS=================================================================
function getLine(){
	var num_opt_text = "";
	$("#cdrlinetext").css("color","black");
	selectedlineb1 = $("input:radio[name=cdrline]:checked").attr("id");
	switch (selectedlineb1){
		case 'softbank':
			calltableprefix = "cdr";
			break;
		case 'ncom':
			calltableprefix = "Ncom";
			break;
	}
	
	var ajax = $.ajax({
		type: "GET",
		url: "getnum.php?customerid=" + custinfo[selectedcustb1].user_id + "&line_id=" + selectedlineb1
	}).done(function(){
		numinfo = jQuery.parseJSON(ajax.responseText);
		$("#numberselectb1").children("option:not(:first)").remove();
		for(i = 0; i < numinfo.length; i++){
			if ( numinfo[i].use == 'No' ){ continue; }
			num_opt_text = "<option val='"+numinfo[i].phonenumber_id+"' id='" + i + "'>"+ numinfo[i].notice_num + " | " + numinfo[i].back_num;
			if ( numinfo[i].location_name ){ num_opt_text += " | " + numinfo[i].location_name; }
			num_opt_text += "</option>";
			$("#numberselectb1").append(num_opt_text);
		}
	});
}

//FUNCTION TO ANIMATE ERRORS======================================================================================================
function errorAnimate(id){
	$("#" + id)
		.animate({color: "black"},anitime)
		.animate({color: "red"},anitime)
		.animate({color: "black"},anitime)
		.animate({color: "red"},anitime)
		.animate({color: "black"},anitime)
		.animate({color: "red"},anitime)
		.animate({color: "black"},anitime)
		.animate({color: "red"},anitime)
		.animate({color: "black"},anitime)
		.animate({color: "red"},anitime)
		.animate({color: "black"},anitime)
		.animate({color: "red"},anitime);
}

//FUNCTION TO ROUND NUMBERS=====================================================================================================
function round(num, place){
	place = parseInt(place, 10);
	num = String(num); //to use array features
	
	var decimal = num.length;
	for (i=0; i<num.length; i++){
		decimal = (num[i] == ".") ? i : decimal;//find the decimal index
	}
	var changespot = decimal + place;
	var digitmove = (num[changespot+1] == ".") ? 2 : 1;
	
	num = num.split(""); //num is now an array
	if (num[changespot + digitmove] >= "5"){
		
		for (i=0; i<num.length; i++){
			if (i == decimal){continue;}
			num[i] = parseInt(num[i],10); //for digit manipulation
		}
		for ( i = changespot; i >= 0; i--){
			if (num[i] == 9){
				num[i] = 0;
				}
				else {
					num[i] += 1;
					break;
				}
		}
	}
	
	if (changespot > decimal){
		num.splice(changespot+digitmove);
	}
	else if (changespot < decimal || changespot+1 == decimal){
		for (i=changespot; i<=decimal; i++){
			num[i+digitmove] = 0;
			num.splice(decimal);
		}
	}
	var newnum = "";
	for (i=0; i<num.length; i++){newnum += num[i];}
	return newnum;
}