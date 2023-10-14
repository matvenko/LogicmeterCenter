<?php
global $out;

if(!$user_class->login_action()){
	include("modules/".$module."/files/login_main_page.php");
}
else{
	$user_info = $user_class->user_info($user_class->current_user_id);
	
	$replace_fields['name_surname'] = $user_info['profile_full_name'];
	$replace_fields['_GO_TO_PROFILE'] = _GO_TO_PROFILE;
	$replace_fields['_LOGOUT'] = _LOGOUT;
	$replace_fields['logicmeter_link'] = $global_conf['location_logicmeter'];
	$replace_fields['session_id'] = session_id();
	
	$replace_fields['user_image'] = $user_info['profile_image'];
	
	$out = $templates->gen_module_html($replace_fields, "auth_main_page");
}