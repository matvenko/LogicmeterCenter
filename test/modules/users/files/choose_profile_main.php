<?php
//$user_class->permission_end($module, "user");
global $out;

$user_info = $user_class->user_info($user_class->current_user_id);

$replace_fields['module'] = $module;
$replace_fields['name_surname'] = $user_info['profile_name'];
$replace_fields['name_surname'] .= (int)$user_class->current_child_id == 0 ? " ("._PARENT.")" : "";
$replace_fields['_LOGOUT'] = _LOGOUT;
$replace_fields['_CHOOSE_PROFILE'] = _CHOOSE_PROFILE;
$user_image = "upload/".$module."/".$user_info['image'];
$replace_fields['user_image'] = is_file($user_image) ? $user_image : "images/user_no_image.jpg";

$profiles_tmpl = $templates->split_template("profiles", "choose_profile_main");
$result = $query->select_sql("math_children", "*", "user_id = ".(int)$user_class->current_user_id." AND `disabled` = 0");
while($row = $query->assoc($result)){
	$profiles_fields['name_surname'] = $row['name'];
	$profiles_fields['child_id'] = $row['id'];
	$user_image = "upload/users/thumb/".$row['image'];
	$profiles_fields['user_image'] = is_file($user_image) ? $user_image : "images/user_no_image.jpg";

	$templates->gen_loop_html($profiles_fields, $profiles_tmpl);
}

$out = $templates->gen_module_html($replace_fields, "choose_profile_main");