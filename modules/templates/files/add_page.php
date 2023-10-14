<?php
$user_class->permission_end($module, 'admin');
global $html_out;

$replace_fields['module'] = $module;
$replace_fields['tmpl_id'] = get_int('tmpl_id');
$replace_fields['lang'] = input_form("lang", "select", $edit_value, array_combine($global_conf['lang_ar'], $global_conf['lang_ar']));
$page_types = select_items("templates_page_types", "id", "page_type");
$replace_fields['page_type'] = input_form("page_type", "select", $edit_value, $page_types);
$modules = select_items("modules", "id", "module");
$replace_fields['module_list'] = input_form("module_list", "select", $edit_value, $modules);
$replace_fields['custom_page'] = input_form("custom_page", "textbox", $edit_value);

if ($dh = opendir("templates/pages")) {
	while (($file = readdir($dh)) !== false) {
		if(is_file("templates/pages/".$file))$template_files[$file] = $file;
	}
	closedir($dh);
}
$replace_fields['template_file'] = input_form("template_file", "select", $edit_value, $template_files);




$out .= $templates->gen_module_html($replace_fields, "add_page");
echo $out;