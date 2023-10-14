<?php
global $out, $facebook_loginUrl;

if($user_class->login_action()){
	include("modules/".$module."/files/auth_default_page.php");
}
else{
	$replace_fields['module'] = $module;
	$replace_fields['_REGISTRATION'] = _REGISTRATION;
	$replace_fields['_LOG_IN'] = _LOG_IN;
	
	$out = $templates->gen_module_html($replace_fields, "login_default_page");
}