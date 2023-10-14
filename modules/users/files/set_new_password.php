<?php
global $out;

if($user_class->login_action()){
	header("Location: index.php");
	exit;
}

//*** check restore code
if($user_class->check_restore_code(get('code')) == false){
	$templates->module_ignore_fields[] = "error";
	$replace_fields['code_error'] = _RESTORE_CODE_ERROR;
}
else{
	$templates->module_ignore_fields[] = "code_ok";
}

$replace_fields['module'] = $module;
$replace_fields['code'] = get('code');
$replace_fields['_CHANGE_PASSWORD'] = _CHANGE_PASSWORD;
$replace_fields['_NEW_PASSWORD'] = _NEW_PASSWORD;
$replace_fields['_RETYPE_PASSWORD'] = _RETYPE_PASSWORD;
$replace_fields['_PASSWORD_CHANGED'] = _PASSWORD_CHANGED;
$replace_fields['_CHANGE'] = _CHANGE;

$replace_fields['new_password'] = input_form("new_password", "password");
$replace_fields['re_password'] = input_form("re_password", "password");

$out = $templates->gen_module_html($replace_fields, "set_new_password");
