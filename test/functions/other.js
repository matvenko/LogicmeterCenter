function yes_no(loc, message){
	if(message == null){
        message = '{{:yes_no_delete:}}';
	}
	if(confirm(message)){
        location.href = loc;
	}
}

function redirect(url){
	window.location = url;
}

function reload(){
	location.reload();
}

function error_message(message){
	try{
		data = jQuery.parseJSON(message);
	}
	catch (e) {
		return false;
	}
	if(data.success == 'ok'){
		if(data.redirect_url !== ''){
			redirect(data.redirect_url);
		}
	}
	else if(data.success == 'ok_message'){
		if(data.message !== undefined){
			window.scrollTo(0, 0);
			$("#check").css('display', 'none');
			if(data.message !== ''){
				$("#ok_message").css('opacity', '1');
				$("#ok_message").css('display', 'block');
				$("#ok_message").addClass('ok_message');
				$("#ok_message").html('<img src="images/success.png" border=0>' + data.message);
				setTimeout(function(){
					$("#ok_message").fadeTo('slow', 0, function(){$("#ok_message").css('display', 'none')});
				}, 3000);
			}
			
		}
	}
	else{
		window.scrollTo(0, 0);
		$("#check").css('display', 'block');
		$("#check").html(data.error_message);
		$("#submit").attr('disabled', false);
	}
	if(data.exec_function !== undefined){
		window[data.exec_function](jQuery.parseJSON(data.exec_function_parameters));
	}
}

function enable_field(parameters){
	$("#"+parameters.field_id).attr('disabled', false);
}

function obj_value(field_id){
	return $('#'+field_id).is(':checkbox') || $("input[name='"+field_id+"']").is(':radio') ? $("input[name='"+field_id+"']:checked").val() : $('#'+field_id).val();
}

function post_call(form_name, ev){
	$("#"+form_name).val(1);
	fields = $("#"+form_name+"_form");
	var request = $.ajax({
	    //dataType: "json",
	    url: fields.attr('action'),
	    method: "POST",
	    data: fields.serializeArray(),
	    success: function(data) {
	    	error_message(data);
	    	message = jQuery.parseJSON(data);
	    	if(message.success == "error"){
	    		$(ev).attr('disabled', false);
	    	}
	    }
	})	
}

function change_img_src(parameters){
	img_info = parameters;
	$("#"+img_info.img_id).attr('src', img_info.img_src);
}

function cool_selecxbox(){
	var config = {
			'.chosen-select'           : {},
			'.chosen-select-deselect'  : {allow_single_deselect:true},
		    '.chosen-select-no-single' : {disable_search_threshold:10},
		    '.chosen-select-no-results': {no_results_text:'Oops, nothing found!'},
		    '.chosen-select-width'     : {width:"95%"}
	}
	for (var selector in config) {
		$(selector).chosen(config[selector]);
	}
}

function form_field_error(parameters){
	for (i = 0; i < parameters.good_fields.length; i++) { 
		$("#"+parameters.good_fields[i]).css('border', '1px solid green');
	}
	$("#"+parameters.field_id).css('border', '1px solid red');
}

function roundNumber(num) {
	var result = Math.round(num*Math.pow(10,2))/Math.pow(10,2);
	return result;
}

function loading_icon(id){
	$("#"+id).html("<img src=\"images/loading2.gif\" border=0>");
}
function loading_big_icon(id){
	$("#"+id).html("<img src=\"images/loading.gif\" border=0>");
}


//***************** calendar ***********************
function cal_image(op, div_value, var_value, year_b, year_f){
	if(op == 0){
		eval_text = 'document.getElementById(\''+var_value+'\').value';
		document.getElementById('img_'+div_value).innerHTML='<img onclick="showform(\'\', \'\', \'blocks/ajax_calendar/show_calendar.php?div='+div_value+'&var='+var_value+'&now_date='+eval(eval_text)+'&year_b='+year_b+'&year_f='+year_f+'\', \''+div_value+'_cal\')" src="images/btn_date1_up.gif" border="0">';
	}
	else if(op == 1){
		document.getElementById('cal_img_'+div_value).innerHTML='<img onclick="cal_image(3, \''+div_value+'\', \''+var_value+'\')" src="images/btn_date1_down.gif" border="0">';
	}
	else if(op == 3){
		eval_text = 'document.getElementById(\''+var_value+'\').value';
		document.getElementById('cal_img_'+div_value).innerHTML='<div id="img" onclick="cal_image(1, \''+div_value+'\', \''+var_value+'\');"><img onclick="showform(\'\', \'\', \'blocks/ajax_calendar/show_calendar.php?div='+div_value+'&var='+var_value+'&now_date='+eval(eval_text)+'&year_b='+year_b+'&year_f='+year_f+'\', \''+div_value+'_cal\')" src="images/btn_date1_up.gif" border="0"></div>';
		document.getElementById(''+div_value+'_cal').innerHTML='';
	}
}
function add_date(date_value, div_value, var_value, year_b, year_f){
	eval('document.getElementById(\''+var_value+'\').value = \''+date_value+'\'');
	eval_text = 'document.getElementById(\''+var_value+'\').value';
	document.getElementById('cal_img_'+div_value).innerHTML='<div id="img" onclick="cal_image(1, \''+div_value+'\', \''+var_value+'\');"><img onclick="showform(\'\', \'\', \'blocks/ajax_calendar/show_calendar.php?div='+div_value+'&var='+var_value+'&now_date='+eval(eval_text)+'&year_b='+year_b+'&year_f='+year_f+'\', \''+div_value+'_cal\')" src="images/btn_date1_up.gif" border="0"></div>';
	document.getElementById(''+div_value+'_cal').innerHTML='';
}
//**************************************************