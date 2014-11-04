////////////////////
//View Bill //
////////////////////
var selectednum = 0;
$("#b1").click(function(){
	var selectedcustb1;
	var selectedlineb1;
	
	var selectedcontext;
	var selectedtype;
	var from;
	var to;
	var from_month;
	var to_month;
	
	//disallow selection of line if customer is not selected
	if ( $("#customerselectb1").val() == 0 ){
		$("input:radio[name=cdrline]").each(function(){
			$(this).attr("disabled", "disabled");
		});
	}
	
	//only do the following if coming from another component page
	if ( bpressed != 1 ){ 
		$("#b" + bpressed).css("color","black");
		$("#b1").css("color","blue");
		$("#fadewlb" + bpressed + ", #fadewrb" + bpressed).fadeOut(timeout);
		bpressed = 1;
		setTimeout(function(){$("#fadewlb1, #fadewrb1").fadeIn(timeout);},timeout);
	}
});

$("input:radio[name=cdrline]").change(function(){getLine();});
$("#numberselectb1").change(function(){
	$("#numberselectb1text").css("color","black");
	//$(".calldata").html("");
});
$("input:radio[name=in_out]").click(function(){$("#in_outtext").css("color","black");}).change(function(){$(".calldata").html("");});
$("input:radio[name=land_mobile]").click(function(){$("#land_mobiletext").css("color","black");}).change(function(){$(".calldata").html("");});

//GET SELECTED CUSTOMER AND WRITE NUMBERS===================================================================================
$("#customerselectb1").change(function(){
	$("#customerselectb1text").css("color","black");
	$("#numberselectb1").children("option:not(:first)").remove();
	selectedcustb1 = $("#customerselectb1 option:selected").attr("index");
	
	if ( $("#customerselectb1 option:selected").attr("value") == 0 ){ 
		$("input:button[name=cdrline]").each(function(){ $(this).prop("checked", false).attr("disabled", "disabled"); });
		return false;
		$("#softbank, #ncom").attr("disabled", true);
	}
	
	$("input:radio[name=cdrline]").each(function(){$(this).removeAttr("disabled");});
	$(".calldata").html("");
	switch(custinfo[selectedcustb1].line_used){
		case "1":
			$("input:radio[name=cdrline][id=ncom]").attr("disabled", true).prop("checked", false);
			$("input:radio[name=cdrline][id=softbank]").prop("checked", true);
			$("#cdrlinetext").css("color","black");
			getLine();
			break;
		case "2":
			$("input:radio[name=cdrline][id=softbank]").attr("disabled", true).prop("checked", false);
			$("input:radio[name=cdrline][id=ncom]").prop("checked", true);
			$("#cdrlinetext").css("color","black");
			getLine();
			break;
		case "1,2":
			$("input:radio[name=cdrline]").each(function(){$(this).prop("checked", false);});
			break;
	}
	loading = 0;
});



//SUBMIT THE INFO TO PHP AND GET THE CALLS==================================================================================
$("#submitb1").click(function(){
  // Go!した時#to日付が28,30,31以外なら背景を赤くする
  var hiduke = $("#to").val().split("-")[2];
  if(hiduke == 31 ||
    hiduke == 28 ||
    hiduke == 30 
  ){
     $("#to").css("background-color","white");
  }else{
     $("#to").css("background-color","pink");
  }

  // Go!した時#from日付が1以外なら背景を赤くする
  var hidukefrom = $("#from").val().split("-")[2];
  if(hidukefrom == "01" ){
     $("#from").css("background-color","white");
  }else{
     $("#from").css("background-color","pink");
  }

	// check for missing info
	error = 0;
	if ( $("#customerselectb1").val() == 0 ){
		errorAnimate("customerselectb1text");
		error = 1;
	}
	if ( $("input:radio[name=cdrline]:checked").attr("id") == undefined ){
		errorAnimate("cdrlinetext");
		error = 1;
	}
	if ( $("#numberselectb1").attr("value") == 0){
		errorAnimate("numberselectb1text");
		error = 1;
	}
	if ( $("input:radio[name=in_out]:checked").attr("id") == undefined ){
		errorAnimate("in_outtext");
		error = 1;
	}
	if ( $("input:radio[name=land_mobile]:checked").attr("id") == undefined) {
		errorAnimate("land_mobiletext");
		error = 1;
	}
	if ( error == 1 ){return false;}
	
	// declare and set vars
	var url;
	loading = 1;
	selectednum = $("#numberselectb1 option:selected").attr("id");
	selectedcontext = $("input:radio[name=in_out]:checked").attr("id");
	selectedtype = $("input:radio[name=land_mobile]:checked").attr("id");
	selectedlineb1 = $("input:radio[name=cdrline]").attr("id");
	from = $.datepicker.parseDate('yy-mm-dd',$("#from").val());
	to = $.datepicker.parseDate('yy-mm-dd',$("#to").val());
	var calltables = new Array();
	switch(selectedcontext){
		case "external":
			rateyen = "in_rate_";
			ratetime = "in_time_";
			break;
		case "internal":
			rateyen = "out_rate_";
			ratetime = "out_time_";
			break;
	}
	switch(selectedtype){
		case "home":
			rateyen += "landline";
			ratetime += "landline";
			break;
		case "mobile":
			rateyen += "mobile";
			ratetime += "mobile";
			break;
	}
	
	//set call table(s)
	var multipletables = 0;
	var numberofcalltables;
	var table_month;
	year = from.getFullYear();
	if ( from.getYear() == to.getYear() ) { numberofcalltables = to.getMonth()-from.getMonth() + 1; }
	else { numberofcalltables = (to.getMonth()+12)-from.getMonth() + 1; }
	if ( numberofcalltables > 1 ){
		multipletables = 1;
	}
	for(i = 0; i < numberofcalltables; i++){
		table_month = parseInt(from.getMonth() + 1) + i;
		if ( table_month == 13 ) {  
			year++;
			table_month = 1;
		}
		if ( table_month < 10 ) { table_month = "0" + String(table_month); }
		calltables[i] = calltableprefix + "_" + year + "_" + table_month;
	}
	
	url = "bill.php?cust=" + selectedcustb1;
	url += "&line=" + selectedlineb1;
	url += "&back_num=" + numinfo[selectednum].back_num;
	url += "&context=" + selectedcontext;
	url += "&type=" + selectedtype;
	url += "&gwprefix=" + numinfo[selectednum].gw_prfix_num;
	url += "&notice_num=" + numinfo[selectednum].notice_num;
	url += "&gw_ext_num=" + numinfo[selectednum].gw_ex_num;
	url += "&ratetime=" + numinfo[selectednum][ratetime];
	url += "&multipletables=" + multipletables;
	url += "&numberofcalltables=" + numberofcalltables;
	for( i = 0; i < calltables.length; i++){
		url += "&calltable" + i + "=" + calltables[i]; 
	}
		
	// get the calls
	var getbill = $.ajax({
		type: "GET",
		url: url
	}).done(function(){
			$("#testarea").html("");
			if ( getbill.responseText == "duplicate" ){ alert("There was a duplicate."); return false; }
			if ( getbill.responseText == "mysql error" ){ alert("mysqlエラーが発生しました。."); return false; }
			if ( getbill.responseText ){
				//alert(getbill.responseText);
				calls = JSON.parse(getbill.responseText);
        //if( calls.dup ){
        //    alert(calls.datas);
        //}
			}
			else{
				calls = "";
			}
			writeCalls();
	});
	
	// reset and enable stuff
	$("#customerselectb1text, #cdrlinetext, #numberselectb1text, #in_outtext, #land_mobiletext").css("color","black");
	loading = 0;
	$("#from, #to").attr("disabled", false);
});


//FUNCTION FOR SHOW CALLS BUTTON================================================================================================
$("#showcalls").click(function(){
	$("#viewcallswindow").fadeIn(300);
});
$("#tableclosebutton").click(function(){
	$("#viewcallswindow").fadeOut(300);
});
$(".dlcsv").click(function(){
	var filefrom;
	filefrom = from.replace("-","");
	filefrom = filefrom.replace("-","");
	var fileto;
	fileto = to.replace("-","");
	fileto = fileto.replace("-","");
	
	$("#calltableform").attr("action", "download.php?filename=" + numinfo[selectednum].notice_num + "_"+ selectedcontext + "_" + selectedtype+ "_cdr_" + filefrom + "_" + fileto + "_SJIS.csv");
	$("#calltableb1").attr("disabled", false);
	$("#calltableform").submit();
	$("#calltableb1").attr("disabled", true);
});

$("#numberselectb1, #customerselectb1, input:radio[name=land_mobile], input:radio[name=in_out], input:radio[name=cdrline], #from, #to").change(function(){
	$("#testarea").html("");
});
