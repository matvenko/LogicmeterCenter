<?php
$user_class->permission_end($module, 'admin');

if(get('action') == 'chng_pass'){
	echo "".generatePassword(8, 2)."";
	exit;
}


$div_style = "padding: 10px; float: left";

$replace_fields['module'] = $module;
$replace_fields['user_id'] = get_int('user_id');

echo $templates->gen_module_html($replace_fields, "admin_chng_pass");