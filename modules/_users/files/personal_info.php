<?php
$user_class->permission_end($module, 'user');
global $tmpl, $out, $math;

if((int)$user_class->current_child_id !== 0){
	header("Location: index.php");
	exit;
}

$edit_value = $user_class->user_info((int)$user_class->current_user_id);

$replace_fields['module'] = $module;
$replace_fields['star'] = star();

//**** parent info
$replace_fields['_PARENT_INFO'] = _PARENT_INFO;
$replace_fields['_NAME'] = _NAME;
$replace_fields['_SURNAME'] = _SURNAME;
$replace_fields['_EMAIL'] = _EMAIL;
$replace_fields['_MOBILE'] = _MOBILE;
$replace_fields['_SAVE'] = _SAVE;
$replace_fields['_UPLOAD_IMAGE'] = _UPLOAD_IMAGE;

$replace_fields['user_image'] = $edit_value['profile_image'];
$replace_fields['name'] = input_form("name", "text", $edit_value);
$replace_fields['surname'] = input_form("surname", "text", $edit_value);
$replace_fields['mail'] = input_form("mail", "text", $edit_value);
$replace_fields['tel'] = input_form("tel", "text", $edit_value);

$replace_fields['_CHANGE_PASSWORD'] = _CHANGE_PASSWORD;
$replace_fields['_CURRENT_PASSWORD'] = _CURRENT_PASSWORD;
$replace_fields['_NEW_PASSWORD'] = _NEW_PASSWORD;
$replace_fields['_RETYPE_PASSWORD'] = _RETYPE_PASSWORD;
$replace_fields['_CHANGE'] = _CHANGE;

$replace_fields['currnet_password'] = input_form("currnet_password", "password");
$replace_fields['new_password'] = input_form("new_password", "password");
$replace_fields['re_password'] = input_form("re_password", "password");

$out = $templates->gen_module_html($replace_fields, "personal_info");