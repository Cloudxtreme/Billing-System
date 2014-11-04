////////////////////////////////
// Customer Maintenance Button//
////////////////////////////////
$("#b2").click(function(){
	var customername;
	var customernamefuri;
	var line;
	var billdate;
	var newCust;
	var func;
	
	
	if ( bpressed != 2 ){
		$("#b" + bpressed).css("color","black");
		$("#b2").css("color","blue");
		$("#fadewlb" + bpressed + ", #fadewrb" + bpressed).fadeOut(timeout);
		bpressed = 2;
		setTimeout(function(){$("#fadewrb2").fadeIn(timeout);},timeout);
		$("#custform input, #dateselb2, #submitb2, #cancelb2").attr("disabled","disabled");
	}
		
	if ( $("#customerselectb2 option:selected").attr("index") < 0 ){
		$("#editb2").attr("disabled","disabled");
		$("#deleteb2").attr("disabled","disabled");
	}
	setTimeout(function(){$("#fadewlb2").fadeIn(timeout);},timeout);
});

$("#customernameb2").focus(function(){$("#customernameb2text").css("color","black");});
$("input:checkbox[name=lineb2]").click(function(){$("#lineb2text").css("color","black");});


//FUNCTION WHEN CUSTOMER IS CHANGED==========================================================================================
$("#customerselectb2").change(custwrite = function(){
	var selected = $("#customerselectb2 option:selected").attr("index");
	$("#custform input, #dateselb2, #submitb2, #cancelb2").attr("disabled","disabled");
	func = "editcust";
	$(".datab2").css("color","black");
	
	
	if ( selected >= 0 ){
		$("#userid").html(custinfo[selected].user_id);
		$("#customernameb2").val(custinfo[selected].user_name);
		$("#customerfurib2").val(custinfo[selected].user_name_rubi);
		
		$("#fadewrb2 input[name=lineb2][type=checkbox]").each(function(){
			$(this).removeAttr("checked");
		});
		$("#dateselb2 option[value='0']").attr("selected", "selected");
		switch(custinfo[selected].line_used){
			case "1":
				$("#custform input[id=softbank]").attr("checked", "checked");
				break;
			case "2":
				$("#custform input[id=ncom]").attr("checked", "checked");
				break;
			case "1,2":
				$("#custform input[id=softbank]").attr("checked", "checked");
				$("#custform input[id=ncom]").attr("checked", "checked");
				break;
		}
		$("#dateselb2 option[value='"+custinfo[selected].bill_date+"']").attr("selected", "selected");	
		
		document.getElementById("deleteb2").disabled = false;
		document.getElementById("editb2").disabled = false;
	}
	else{
		$("#editb2, #deleteb2").attr("disabled","disabled");
		document.getElementById("custform").reset();
		$("#userid").html("");
		$("#dateselb2").val(0);
		$("input:checkbox[name=lineb2]").each(function(){$(this).prop("checked",false);});
	}
});

//WHEN EDIT BUTTON IS CLICKED=====================================================================================================
$("#editb2").click(function(){
	func = "editcust";
	$("#custform input, #dateselb2, #submitb2, #cancelb2").attr("disabled", false);
});

//WHEN DELETE BUTTON IS CLICKED==================================================================================================
$("#deleteb2").click(function(){
	func = "deletecust";
	
	var ajax = $.ajax({
		type: "GET",
		url: "customer.php?func=" + func + "&user_id=" + $("#customerselectb2 option:selected").attr("value")
	}).done(function(){
		$("#customerselectb2").children("option:not(:first)").remove();
		
		populateCust("customerselectb2");
		$("#editb2, #deleteb2").attr("disabled", "disabled");
	});
	
	document.getElementById("custform").reset();
	$("#userid").html("");
	$("#dateselb2").val(0);
	$("#custform input:checkbox").prop("checked", false);
});

//WHEN NEW CUSTOMER BUTTON IS CLICKED==========================================================================================
$("#newcustomer").click(newCust = function(){
	func = "newcust";
	$("#editb2, #deleteb2").attr("disabled",true);
	$("#customerselectb2").val("0");
	$("#custform input, #dateselb2, #submitb2, #cancelb2").attr("disabled", false);
	
	var ajax = $.ajax({
		type: "GET",
		url: "customer.php?func=getid"
	}).done(function(){
		userid = ajax.responseText;
		//$("#userid").html(parseInt(userid) + 1);
		$("#userid").html(userid);
	});
	document.getElementById("custform").reset();
	$("#dateselb2").val(0); 
	$("input:checkbox[name=lineb2]").each(function(){$(this).prop("checked",false);});
	$(".datab2").css("color","black");
});


//WHEN SUBMIT BUTTON IS CLICKED===============================================================================================
$("#submitb2").click(function(){
	if ( func == "editcust"){selectedcustb2 = $("#customerselectb2 option:selected").attr("value");}
	else { selectedcustb2 = userid; }
	error = 0;
	if ( $("input:text[id=customernameb2]").val() == "" && func == "newcust"){
		errorAnimate("customernameb2text");
		error = 1;
	}
	if ( $("input:checkbox[name=lineb2]:checked").val() == undefined  && func == "newcust"){
		errorAnimate("lineb2text");
		error = 1;
	}
	if ( error === 1 ){ return false; }
	
	$(".datab2").css("color","black");
	customername = $("input:text[id=customernameb2]").val();
	customernamefuri = $("input:text[id=customerfurib2]").val();
	var i = 0;
	$("input:checkbox[name=lineb2]:checked").each(function(){
		i += parseInt( $(this).val() );
		switch (i){
			case 1:
				line = "1"
				break;
			case 2:
				line = "2"
				break;
			case 3:
				line = "1\\,2"
				break;
		}
	});
	billdate = $("#dateselb2").val();
	var newcustajax = $.ajax({
		type: "GET",
		url: "customer.php?func=" + func + "&userid=" + selectedcustb2 + "&customername=" + customername + "&customernamefuri=" + customernamefuri + "&line=" + line + "&billdate=" + billdate})
		.done(function(){
			$("#custform input, #dateselb2, #submitb2, #cancelb2").attr("disabled",true);
			if ( func == "newcust" ){
				$("input:checkbox[name=lineb2]").each(function(){$(this).attr("checked",false);});
				$("#customerselectb2").val("0");
				$("#dateselb2").val(0);
				document.getElementById("custform").reset();
				$("#userid").html("");
			}
			$("#customerselectb2").children("option:not(:first)").remove();
			$.when(populateCust("customerselectb2")).then(function(){
				
			});
		});
}); 

//WHEN CANCEL BUTTON IS CLICKED=================================================================================================
$("#cancelb2").click(function(){
	$("#custform input, #dateselb2, #submitb2, #cancelb2").attr("disabled", "disabled");
	if ( func == "newcust" ){
		document.getElementById("custform").reset();
		$("#userid").html("");
		$("#custform select").val(0);
	}
	if ( func == "editcust" ){
		custwrite();
	}
});
