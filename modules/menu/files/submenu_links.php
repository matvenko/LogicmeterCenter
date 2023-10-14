<?php
global $out;

$module = "menu";
$submenu_tmpl = $templates->split_template("submenu_links", "submenu_links");

$cur_url = explode('/', $_SERVER['REQUEST_URI']);
$where = "link = '".$cur_url[count($cur_url) - 1]."'";
$row_cur = $query->select_obj_sql("menu_items", "*", $where);

$result = $query->select_sql("menu_items", "*", "parent_id!= 0 AND parent_id = ".(int)$row_cur->parent_id);

$submenu_amount = 0;
while($row = $query->assoc($result)){
	$submenu_amount ++;
	$submenu_fields['menu_link'] = $row['link'];
	$submenu_fields['link_title'] = $row['name'];
	
	$submenu_fields['active'] = $row['link'] == $cur_url[count($cur_url) - 1] ? " submenu_active" : "";
	
	$templates->gen_loop_html($submenu_fields, $submenu_tmpl);
}

if($submenu_amount > 0){
    $templates->module_ignore_fields[] = "submenu";
}

$out = $templates->gen_module_html($replace_fields, "submenu_links");
unset($templates->module_content);
