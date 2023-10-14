<?php
global $out;

$menu_tree_tmpl = $templates->split_template("menu_tree_links", "menu_tree");
$menu_tree_sublinks = $templates->split_template("menu_tree_sublinks", "menu_tree");

$result = $query->select_sql("menu_items", "*", "parent_id = 0 AND parent = 1 AND menu_id = ".get_int('menu_id'));

while($row = $query->assoc($result)){
	$menu_tree_fields['menu_link'] = $row['link'];
	$menu_tree_fields['link_title'] = $row['name'];
	
	$result_sub = $query->select_sql("menu_items", "*", "parent_id = ".(int)$row['id']);	
	$menu_tree_fields['sublinks'] = "";
	while($row_sub = $query->assoc($result_sub)){
		$menu_tree_sub_fields['menu_link'] = $row_sub['link'];
		$menu_tree_sub_fields['link_title'] = $row_sub['name'];
		
		$menu_tree_fields['sublinks'] .= $templates->gen_html($menu_tree_sub_fields, $menu_tree_sublinks['menu_tree_sublinks'], 0);
	}
	
	$templates->gen_loop_html($menu_tree_fields, $menu_tree_tmpl);
}

$out = $templates->gen_module_html($replace_fields, "menu_tree");
unset($templates->module_content);