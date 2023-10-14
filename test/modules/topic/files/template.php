<?php
$user_class->permission_end($module, 'admin');
global $html_out;
$out = "";
$row_head = $query->select_obj_sql("topic_pages", "*", "id = ".get_int('topic_id')."");

$out .= "<div class=\"clear_div\">\n";
$out .= "<div class=\"table_head\" style=\"width: 550px\">
			<a href=\"admin.php?module=".$module."&page=admin_main\" style=\"color: #FFF\">".$row_head->name."</a></div>\n";
$out .= "</div>\n";

$topic_comment[1] = _TOPIC;
$topic_comment[2] = _COMMENT;
$topic_comment[3] = _CATEGORY;
$topic_comment[4] = _DETALS;
$topic_comment[5] = _HEAD;
$topic_comment[6] = _SUB_CATEGORY;
$topic_comment[7] = _FOOTER;
$topic_comment[8] = _VIEV_COMMENTS;
$result = $query->select_sql("topic_template", "*", "topic_id = ".get_int('topic_id')."", "topic_comment ASC, priority ASC");
$out .= "<table class=\"admin_table\">\n";
while($row = $query->obj($result)){
	$td_style = $templates->change_style();
	$out .= "<tr>\n";
	$out .= "<td class=\"".$td_style."\" style=\"width: 50px; height: 25px;\">
				<a href=\"post.php?module=".$module."&tmpl_id=".$row->id."&updown=up\">
					<img src=\"images/arrow_up_green.png\" alt=\"Up\" border=\"0\"></a>\n
    			<a href=\"post.php?module=".$module."&tmpl_id=".$row->id."&updown=down\">
    				<img src=\"images/arrow_down_blue.png\" alt=\"Down\" border=\"0\"></a></td>\n";
	$out .= "<td class=\"".$td_style."\" style=\"width: 300px; height: 25px; text-align: left\">&nbsp;".$row->tmpl_title."</td>\n";
	$out .= "<td class=\"".$td_style."\" style=\"width: 80px; height: 25px;\">".$topic_comment[$row->topic_comment]."</td>\n";
	$out .= "<td class=\"".$td_style."\" style=\"width: 55px; height: 25px\">
				".edit_button("topic_id=".get_int('topic_id')."&tmpl_id=".$row->id."")."</td>\n";
	$out .= "</td>\n";
	$out .= "<td class=\"".$td_style."\" style=\"width: 55px; height: 25px\">
				".delete_button("delete_template", "topic_id=".get_int('topic_id')."&tmpl_id=".$row->id."")."</td>\n";
	$out .= "</td>\n";
	$out .= "</tr>\n";
}
$out .= "</table>\n";

if(get_int('tmpl_id') !== 0){
	$row_edit = $query->select_obj_sql("topic_template", "*", "id = ".get_int('tmpl_id')."");
	$edit_value['tmpl_title'] = $row_edit->tmpl_title;
	$edit_value['design'] = $row_edit->design;
	$edit_value['repeat'] = $row_edit->repeat;
	$edit_value['comments_amount'] = $row_edit->comments_amount;
	$edit_value['topic_comment'] = $row_edit->topic_comment;
}

$out .= form_start("tmpl_id=".get_int('tmpl_id')."&topic_id=".get_int('topic_id'));

$out .= "<table class=\"admin_table\" style=\"margin-top: 30px\">\n";
$out .= "<tr>\n";
$out .= "	<td class=\"table_head\" colspan=2>"._ADD_TEMPLATE."</td>\n";
$out .= "</tr>\n";

$out .= "<tr>\n";
$out .= "<td class=\"table_td2\">"._TMPL_TITLE."</td>\n";
$out .= "<td class=\"table_td2\">
			".input_form("tmpl_title", "textbox", $edit_value, "", "width: 300px")."</td>\n";
$out .= "</tr>\n";

$out .= "<tr>\n";
$out .= "<td class=\"table_td1\" colspan=2>"._TEMPLATE."<BR>
			".input_form("design", "textarea", $edit_value, "", "width: 550px; height: 250px")."</td>\n";
$out .= "</tr>\n";

$out .= "<tr>\n";
$out .= "<td class=\"table_td2\">"._TEMPLATE_REPEATE."</td>\n";
$out .= "<td class=\"table_td2\">
			".input_form("repeat", "textbox", $edit_value, "", "width: 30px")."</td>\n";
$out .= "</tr>\n";

$out .= "<tr>\n";
$out .= "<td class=\"table_td1\">"._COMMENTS_AMOUNT."</td>\n";
$out .= "<td class=\"table_td1\">
			".input_form("comments_amount", "textbox", $edit_value, "", "width: 30px")."</td>\n";
$out .= "</tr>\n";

$out .= "<tr>\n";
$out .= "<td class=\"table_td2\">"._TOPIC." / "._COMMENT."</td>\n";
$out .= "<td class=\"table_td2\">
			".input_form("topic_comment", "select", $edit_value, array_flip($topic_comment))."</td>\n";
$out .= "</tr>\n";

$out .= "<tr>\n";
$out .= "<td class=\"table_td2\" colspan=2>
			<input type=\"submit\" value=\""._ADD."\" name=\"add_template\"></td>\n";
$out .= "</tr>\n";
$out .= "</table>\n";
$out .= form_end();

$html_out['module'] = $out;
?>