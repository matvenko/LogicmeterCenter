<?php
$user_class->permission_end($module, 'admin');
global $html_out;

$replace_fields['module'] = $module;

$templates_tmpl = $templates->split_template("templates", "admin_main");
$result = $query->select_sql("templates");
while($row = mysql_fetch_assoc($result)){
	$templates_fields['td_style'] = $templates->change_style();
	$templates_fields['module'] = $module;
	
	$templates_fields['tmpl_id'] = $row['id'];
	$templates_fields['name'] = $row['name'];
	$templates_fields['active'] = $row['active'] == 1 ? icon('accept') : icon('decline');
	$templates_fields['edit'] = edit_button($edit_object);
	$templates_fields['delete'] = delete_button($action, $delete_obj);
	
	$templates->gen_loop_html($templates_fields, $templates_tmpl);
}

$out .= $templates->gen_module_html($replace_fields, "admin_main");
$html_out['module'] = $out;