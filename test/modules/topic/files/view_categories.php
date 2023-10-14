<?php
global $out;
$out = '';

$row_tmpl = $query->select_obj_sql("topic_template", "*", "topic_id = ".get_int('topic_id')." AND topic_comment = 3", "priority ASC", "0,1");
$row_tmpl_sub = $query->select_obj_sql("topic_template", "*", "topic_id = ".get_int('topic_id')." AND topic_comment = 6", "priority ASC", "0,1");

$template['design'] = htmlspecialchars_decode($row_tmpl->design);
$template_sub['design'] = htmlspecialchars_decode($row_tmpl_sub->design);

$result = $query->select_sql("topic_categories", "*", "topic_id = '".get_int('topic_id')."' AND parent_id = 0");


while($row = $query->obj($result)){
	$topic_out = $template['design'];
	$category = $row->name;
	$topic_out = str_replace("{{cat_id}}", $row->id, $topic_out);
	$topic_out = str_replace("{{category}}", $category, $topic_out);
    $out .= $topic_out;

	$result_sub = $query->select_sql("topic_categories", "*", "topic_id = '".get_int('topic_id')."' AND parent_id = ".(int)$row->id."");

	if($template_sub['design'] !== ""){
		$out .= "<div class=\"sub_categories\" id=\"sub_cat_".$row->id."\">\n";
		while($row_sub = $query->obj($result_sub)){
			$topic_sub_out = $template_sub['design'];
			$category_sub = $row_sub->name;
			$topic_sub_out = str_replace("{{main_cat_id}}", $row->id, $topic_sub_out);
			$topic_sub_out = str_replace("{{cat_id}}", $row_sub->id, $topic_sub_out);
			$topic_sub_out = str_replace("{{sub_category}}", $category_sub, $topic_sub_out);
		    $out .= $topic_sub_out;
		}
		$out .= "</div>\n";
	}
	$out = str_replace("{{lang}}", $lang, $out);
	$out = str_replace("{{global_main_cat_id}}", get_int('topic_id'), $out);
	$out = str_replace("{{module}}", $module, $out);


}
//$out = eregi_replace("{{([[:print:]]+)}}", "", $out);

?>