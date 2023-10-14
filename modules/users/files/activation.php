<?php
global $out;

if(get('action') == "send_activation"){
	if($user_class->login_action()){
		$user_info = $query_l->select_ar_sql("users", "activation_code", "id = ".(int)$user_class->current_user_id);
		if($user_info['activation_code'] == ''){
			$activation_code = md5(generatePassword(6, 2));
			$query->update_sql("users", array("activation_code" => $activation_code), "id = ".(int)$user_class->current_user_id);
		}
		else{
			$activation_code = $user_info['activation_code'];
		}
		
		$mail_text = $query_l->select_ar_sql("mail_templates", "*", "name = 'ACTIVATION_LINK'");
		
		$activation_link = $global_conf['location']."/index.php?module=users&page=activation&activation_code=".$activation_code;
		
		$mail_text['tamplate'] = str_replace("{{activation_link}}", $activation_link, $mail_text['tamplate']);
    
    	$global_conf['reply_to'] = 'no-reply@logicmeter.com';
    	$mail_text['tamplate'] = str_replace("{{activation_code}}", $activation_code, $mail_text['tamplate']);
    	send_mail($_SESSION['login']['username'], $mail_text['title'], $mail_text['tamplate']);
    	
    	$out_data['success'] = "ok_message";
    	$out_data['message'] = _ACTIVATION_CODE_SENT." ".$_SESSION['login']['username'];
    	echo json_encode($out_data);
    	exit;
	}
}

$replace_fields['module'] = $module;
$replace_fields['_ACTIVATION_CODE_INCORRECT'] = _ACTIVATION_CODE_INCORRECT;
$replace_fields['_ACOUNT_ACTIVATED'] = _ACOUNT_ACTIVATED;
$replace_fields['_NEED_ACTIVATION'] = _NEED_ACTIVATION;
$replace_fields['_SEND_ACTIVATION_LINK'] = _SEND_ACTIVATION_LINK;
$replace_fields['_ACTIVATION_CODE_SENT'] = _ACTIVATION_CODE_SENT;
$replace_fields['_GO_TO_PROFILE'] = _GO_TO_PROFILE;
$replace_fields['logicmeter_link'] = $global_conf['location_logicmeter'];
$replace_fields['_AND_PAY'] = _AND_PAY;

if(get('activation_code') !== false){
	$query_l->where_vars['activation_code'] = get('activation_code');
	if($query_l->record_exist("users", "activation_code = '{{activation_code}}'")){
		$query_l->update_sql("users", array("status" => 1), "activation_code = '{{activation_code}}'");
		header("Location: index.php?module=".$module."&page=activation");
		exit;
	}
	else{
		$templates->module_ignore_fields[] = "activation_code_incorrect";
	}
}
elseif($user_class->login_action()){
	$user_info = $query_l->select_ar_sql("users", "`status`", "id = ".(int)$user_class->current_user_id);
	if((int)$user_info['status'] !== 1){
		$templates->module_ignore_fields[] = "need_activation";
	}
	else{
		$templates->module_ignore_fields[] = "account_activated";
	}
}
else{
	header("Location: index.php");
	exit;
}

$out = $templates->gen_module_html($replace_fields, "activation");