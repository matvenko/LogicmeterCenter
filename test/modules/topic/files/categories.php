<?php
$query->permission_end($module, 'admin');
global $html_out;

if(get_int('cat_id') !== 0){
	$row_edit = $query->select_obj_sql("topic_categories", "*", "id = ".get_int('cat_id')."");
	$edit_value['cat_name'] = $row_edit->name;
	$edit_value['categories'] = (int)$row_edit->parent_id;
}

$row_head = $query->select_obj_sql("topic_pages", "*", "id = ".get_int('topic_id')."");

$out .= "<div class=\"clear_div\">\n";
$out .= "<div class=\"admin_head\" style=\"width: 550px\">
			<a href=\"admin.php?module=".$module."&page=admin_main\" style=\"color: #FFF\">".$row_head->name."</a></div>\n";
$out .= "</div>\n";

$out .= "<div style=\"clear: both\">".space(5, 10)."</div>\n";
$out .= "<div class=\"admin_head\" style=\"width: 400px;\">"._CATEGORIES."</div>\n";
$out .= "<div class=\"admin_head\" style=\"width: 60px;\">&nbsp;</div>\n";

$result = $query->select_sql("topic_categories", "*", "topic_id = ".get_int('topic_id')." AND parent_id = 0", "id DESC");

while($row = $query->obj($result)){
	$div_style = $templates->change_style();
	$out .= "<div style=\"clear: both\">\n";
	$out .= "<div class=\"".$div_style."\" style=\"text-align: left; width: 400px; height: 25px\">&nbsp;".$row->name."</div>\n";
	$out .= "<div class=\"".$div_style."\" style=\"width: 30px; height: 25px;\">
				".edit_button("topic_id=".get_int('topic_id')."&cat_id=".$row->id)."</div>\n";
	$out .= "<div class=\"".$div_style."\" style=\"width: 30px; height: 25px;\">
				".delete_button("delete_topic_cat", "topic_id=".get_int('topic_id')."&cat_id=".$row->id)."</div>\n";
	$out .= "</div>\n";
	if((int)$row->parent_id == 0){
		$result_sub = $query->select_sql("topic_categories", "*", "parent_id = ".$row->id."", "id DESC");
		while($row_sub = $query->obj($result_sub)){
			$out .= "<div style=\"clear: both\">\n";
			$out .= "<div class=\"".$div_style."\" style=\"text-align: left; width: 400px; height: 25px;\">
						".space(15, 5).".....".$row_sub->name."</div>\n";
			$out .= "<div class=\"".$div_style."\" style=\"width: 30px; height: 25px;\">
						".edit_button("topic_id=".get_int('topic_id')."&cat_id=".$row_sub->id)."</div>\n";
			$out .= "<div class=\"".$div_style."\" style=\"width: 30px; height: 25px;\">
						".delete_button("delete_topic_cat", "topic_id=".get_int('topic_id')."&cat_id=".$row_sub->id)."</div>\n";
			$out .= "</div>\n";
		}	}
	$categories[$row->name] = $row->id;
}

$out .= "<div style=\"clear: both\">".space(5, 20)."</div>\n";
$out .= form_start("topic_id=".get_int('topic_id')."&cat_id=".get_int('cat_id'));
$out .= "<div class=\"admin_head\" style=\"width: 350;\">"._ADD_CATEGORY."</div>\n";
$out .= "<div class=\"admin_td1\" style=\"clear: left; width: 350;\">GEO".
			input_form("cat_name", "textbox", $edit_value, "", "width: 310px")."</div>\n";

$out .= "<div class=\"admin_td1\" style=\"clear: left; width: 350;\">Cat".
			input_form("categories", "select", $edit_value, $categories, "width: 310px")."</div>\n";

$out .= "<div style=\"clear: left; \">
			<input type=\"submit\" value=\""._ADD."\" name=\"add_topic_cat\"></div>\n";
$out .= form_end();

$html_out['module'] = $out;
unset($out);
?>