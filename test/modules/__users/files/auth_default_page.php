<?php
global $out;

$user_info = $user_class->user_info($user_class->current_user_id);

$replace_fields['name_surname'] = $user_info['full_name'];
$replace_fields['parent_name_surname'] = $user_info['name'];
$replace_fields['_GO_TO_PROFILE'] = _GO_TO_PROFILE;
$replace_fields['_REPORTS'] = _REPORTS;
$replace_fields['_LOGOUT'] = _LOGOUT;
$replace_fields['module'] = $module;
$replace_fields['logicmeter_link'] = $global_conf['location_logicmeter'];
$replace_fields['session_id'] = session_id();

$replace_fields['user_image'] = $user_info['profile_image'];

$replace_fields['parent_image'] = $user_info['user_image'];

$out = $templates->gen_module_html($replace_fields, "auth_default_page");
unset($templates->module_content);