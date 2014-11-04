//////////////////////////////
// Number Maintenance Button//
//////////////////////////////
$("#b3").click(function(){
	var func;
	var selectedcustb3;
	var selectednum;
	var canceledflag;
	
	if ( $("#customerselectb3").val() == 0 && $("#numberselectb3").val() == 0 ){
		$("#editb3, #deleteb3").attr("disabled", "disabled");
	}
	
	$(".datab3").prop("disabled", true);	
	if (bpressed != 3){
		$("#saveb3, #cancelb3").prop("disabled", true);
		$("#b" + bpressed).css("color","black");
		$("#b3").css("color","blue");
		$("#fadewlb" + bpressed + ", #fadewrb" + bpressed).fadeOut(timeout);
		bpressed = 3;

		window.setTimeout(function(){
			$("#fadewlb3, #fadewrb3").fadeIn(timeout);
		},timeout);
	}
});

$("input:radio[name=lineidb3]").change(function(){$("#errortextb31").css("color", "black");});
$("#back_numb3").click(function(){$("#errortextb32").css("color", "black");});
$("#notice_numb3").click(function(){$("#errortextb33").css("color", "black");});

//FUNCTION WHEN CUSTOMER IS CHANGED================================================================================================
$("#customerselectb3").change(function(){
	$("#testarea").stop().html("");
	selectedcustb3 = $("#customerselectb3").val();
	$("#customerselectb3").css("color","black");
	
	document.newnumbername.reset();
	if (selectedcustb3 != 0){
		var ajax = $.ajax({ //GET NUMBERS OF SELECTED CUSTOMER
			type: "GET",
			url: "getnum.php?customerid="+selectedcustb3
		}).done(function(){
				numinfo = jQuery.parseJSON(ajax.responseText);
				$("#numberselectb3").children('option:not(:first)').remove();
				var num_opt_text = "";
				for(i = 0; i < numinfo.length; i++){
					num_opt_text = "<option val='"+numinfo[i].phonenumber_id+"' id='" + i + "'>"+ numinfo[i].notice_num + " | " + numinfo[i].back_num;
					if ( numinfo[i].location_name ){ num_opt_text += " | " + numinfo[i].location_name; }
					num_opt_text += "</option>";					
					//$("#numberselectb3").append("<option val='" + numinfo[i].phonenumber_id + "' id='" + i + "'>"+ numinfo[i].notice_num + " | " + numinfo[i].back_num + "</option>"); 
					$("#numberselectb3").append(num_opt_text); 
				}
		});
	}
	$(".datab3").prop("disabled", true);
});

//FUNCTION WHEN NUMBER IS CHANGED===================================================================================================
$("#numberselectb3").change(function(){
	selectednum = $("#numberselectb3 option:selected").attr("id");
	if ( numinfo[selectednum] == undefined ){$("#editb3, #deleteb3").attr("disabled", "disabled");document.getElementById("newnumberform").reset();return false;}
	$("#numberselectb3").css("color", "black");
	$(".datab3").prop("disabled", true);
	switch(numinfo[selectednum].line_id){
		case "1":
			$("input:radio[value=1]").prop("checked",true);
			break;
		case "2":
			$("input:radio[value=2]").prop("checked",true);
			break;
	}
	
	//fill fields with number information
	$("input:text[id=location_nameb3]").val(numinfo[selectednum].location_name);
	$("input:text[id=back_numb3]").val(numinfo[selectednum].back_num);
	$("input:text[id=notice_numb3]").val(numinfo[selectednum].notice_num);
	$("input:text[id=gw_nameb3]").val(numinfo[selectednum].gw_name);
	$("input:text[id=gw_ex_numb3]").val(numinfo[selectednum].gw_ex_num);
	$("input:text[id=gw_prfix_numb3]").val(numinfo[selectednum].gw_prfix_num);
	$("input:text[id=ch_numb3]").val(numinfo[selectednum].ch_num);
	$("input:text[id=out_rate_landlineb3]").val(numinfo[selectednum].out_rate_landline);
	$("input:text[id=out_time_landlineb3]").val(numinfo[selectednum].out_time_landline);
	$("input:text[id=out_rate_mobileb3]").val(numinfo[selectednum].out_rate_mobile);
	$("input:text[id=out_time_mobileb3]").val(numinfo[selectednum].out_time_mobile);
	$("input:text[id=in_rate_landlineb3]").val(numinfo[selectednum].in_rate_landline);
	$("input:text[id=in_time_landlineb3]").val(numinfo[selectednum].in_time_landline);
	$("input:text[id=in_rate_mobileb3]").val(numinfo[selectednum].in_rate_mobile);
	$("input:text[id=in_time_mobileb3]").val(numinfo[selectednum].in_time_mobile);
	$("input:text[id=servicein_dateb3]").val(numinfo[selectednum].servicein_date);
	$("input:text[id=memob3]").val(numinfo[selectednum].memo);
	if ( numinfo[selectednum].use == "No" ){
		$("input:radio[id=no]").prop("checked","checked");
	}	
	$("#editb3, #deleteb3").prop("disabled", false);
});

//WHEN NEW NUMBER BUTTON IS CLICKED==============================================================================================
$("#newnumberb3").click(function(){
	document.newnumbername.reset();
	func = "newnumber";
	$("#errortextb31, #errortextb32, #errortextb33").css("color", "black");
	
	if ( $("#customerselectb3").val() == 0 ){
		$("#testarea").html("Please select a customer!");
		errorAnimate("testarea");
		setTimeout(function(){
			$("#testarea").fadeOut(500);
			setTimeout(function(){
				$("#testarea").css("color", "black").html("").fadeIn(10);
			},500);
		},3000);
		return false;
	}
	$(".datab3").prop("disabled", false);
	$("#numberselectb3").val(0);
	$("#editb3").prop("disabled", true);
	$("#deleteb3").prop("disabled", true);
	$("#saveb3, #cancelb3").prop("disabled", false);
	cancelflag = 0;
});

//WHEN EDIT BUTTON IS CLICKED=======================================================================================================
$("#editb3").click(function(){
	func = "editnumber";
	if ( $("#customerselectb3").val() == 0 ){
		errorAnimate("customerselectb3");
		return false;
	}
	if ( $("#numberselectb3").val() == 0 ){
		errorAnimate("numberselectb3");
		return false;
	}
	$(".datab3").prop("disabled", false);
	$("#saveb3, #cancelb3").prop("disabled", false);
	cancelflag = 0;
});

//WHEN DELETE BUTTON IS CLICKED===================================================================================================
$("#deleteb3").click(function(){
	phonenumber_id = $("#numberselectb3 option:selected").attr("val");
	var ajax = $.ajax({
		type: "GET",
		url: "number.php?func=delete&phonenumber_id=" + phonenumber_id
	}).done(function(){
		var refresh = $.ajax({ //REFRESH THE NUMBERS OF SELECTED CUSTOMER
			type: "GET",
			url: "getnum.php?customerid="+selectedcustb3
		}).done(function(){
				$("#numberselectb3").children("option:not(:first)").remove();
				numinfo = jQuery.parseJSON(refresh.responseText);
				for(i = 0; i < numinfo.length; i++){
					$("#numberselectb3").append("<option val='"+numinfo[i].phonenumber_id+"' id='" + i + "'>"+ numinfo[i].phonenumber_id + " " +numinfo[i].notice_num+"</option>"); 
				}
		});
		document.getElementById("newnumberform").reset();
		$("#editb3, #deleteb3").attr("disabled", "disabled");
	});
});


//WHEN SAVE BUTTON IS CLICKED=================================================================================================
$("#saveb3").click(function(){
	if ( $("input:radio[name=lineidb3]:checked").val() == undefined ){
		errorAnimate("errortextb31");
		return false;
	}
	if( $.trim( $("input:text[id=back_numb3]").val() ).length == 0 ){
		errorAnimate("errortextb32");
		return false;
	} 
	if( $.trim( $("input:text[id=notice_numb3]").val() ).length == 0 ){
		errorAnimate("errortextb33");
		return false;
	} 
	
	var url;
	url = "number.php?";
	url += "func=" + func;
	url += "&phonenumber_id=" + $("#numberselectb3 option:selected").attr("val");
	url += "&user_id=" + $("#customerselectb3 option:selected").attr("value");
	url += "&line_id=" + $("input:radio[name=lineidb3]:checked").attr("value");
	url += "&location_name=" + $("#location_nameb3").attr("value");
	url += "&back_num=" + $("#back_numb3").attr("value");
	url += "&notice_num=" + $("#notice_numb3").attr("value");
	url += "&gw_name=" + $("#gw_nameb3").attr("value");
	url += "&gw_ex_num=" + $("#gw_ex_numb3").attr("value");
	url += "&gw_prfix_num=" + $("#gw_prfix_numb3").attr("value");
	url += "&ch_num=" + $("#ch_numb3").attr("value");
	url += "&out_time_landline=" + $("#out_time_landlineb3").attr("value");
	url += "&out_rate_landline=" + $("#out_rate_landlineb3").attr("value");
	url += "&out_time_mobile=" + $("#out_time_mobileb3").attr("value");
	url += "&out_rate_mobile=" + $("#out_rate_mobileb3").attr("value");
	url += "&in_time_landline=" + $("#in_time_landlineb3").attr("value");
	url += "&in_rate_landline=" + $("#in_rate_landlineb3").attr("value");
	url += "&in_time_mobile=" + $("#in_time_mobileb3").attr("value");
	url += "&in_rate_mobile=" + $("#in_rate_mobileb3").attr("value");
	url += "&servicein_date=" + $("#servicein_dateb3").attr("value");
	url += "&memo=" + $("#memob3").attr("value");
	url += "&use=" + $("input:radio[name=useb3]:checked").attr("value");
	
	var ajax = $.ajax({//ENTER NUMBER INFO INTO DATABASE
		type: "GET",
		url: url
	}).done(function(){
		var refresh = $.ajax({ //REFRESH THE NUMBERS OF SELECTED CUSTOMER
			type: "GET",
			url: "getnum.php?customerid="+selectedcustb3
		}).done(function(){
			$("#numberselectb3").children("option:not(:first)").remove();
			numinfo = jQuery.parseJSON(refresh.responseText);
			for(i = 0; i < numinfo.length; i++){
				$("#numberselectb3").append("<option val='"+numinfo[i].phonenumber_id+"' id='" + i + "'>"+ numinfo[i].phonenumber_id + " " +numinfo[i].notice_num+"</option>"); 
			}
			if(func == "editnumber"){$("select[name=numberselectb3] option[id=" + selectednum + "]").attr("selected", "selected");}
		});
	});
	
	$(".datab3").prop("disabled", true);
	$("#saveb3, #cancelb3").prop("disabled", true);
	cancelflag = 0;
	document.getElementById("buttonsb1").reset();
	$(".data").html("");
	$("#numberselectb1").children("option:not(:first)").remove();
});

//WHEN CANCEL BUTTON IS CLICKED================================================================================================
$("#cancelb3").click(function(){
	$("#testarea").stop();
	document.getElementById("newnumberform").reset();
	$("form[name=newnumbername] input").attr("disabled", "disabled");
	$("#numberselectb3").val(0);
	$("#editb3, #deleteb3, #saveb3, #cancelb3").attr("disabled", "disabled");
	$("#errortextb31, #errortextb32, #errortextb33").stop(true,true).css("color","black");
	cancelflag = 1;
});
