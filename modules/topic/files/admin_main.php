<?php
global $html_out;
$user_class->permission_end($module, "admin");
$out = "";

if(get_int('topic_id') !== 0){	$row_edit = $query->select_obj_sql("topic_pages", "*", "id = ".get_int('topic_id')."");
	$edit_value['topic_name'] = $row_edit->name;
	$edit_value['topic_lang'] = $row_edit->lang;}

$out .= "<table class=\"admin_table\">\n";
$out .= "	<tr>\n";
$out .= "		<td class=\"admin_head\" style=\"width: 250px\">"._NAME."</td>\n";
$out .= "		<td class=\"admin_head\" style=\"width: 250px\">Description</td>\n";
$out .= "		<td class=\"admin_head\" style=\"width: 70px; overflow: hidden\">"._CONFIG."</td>\n";
$out .= "		<td class=\"admin_head\" style=\"width: 70px\">"._TEMPLATE."</td>\n";
$out .= "		<td class=\"admin_head\" style=\"width: 70px\">"._CATEGORIES."</td>\n";
$out .= "		<td class=\"admin_head\" style=\"width: 60px\">"._LANG."</td>\n";
$out .= "		<td class=\"admin_head\" style=\"width: 60px; overflow: hidden\">"._EDIT."</td>\n";
$out .= "		<td class=\"admin_head\" style=\"width: 60px\">"._DELETE."</td>\n";
$out .= "	</tr>\n";

$result = $query->select_sql("topic_pages");

while($row = $query->obj($result)){	
	$div_style = $templates->change_style();
	$td_style = $templates->change_style();
	$out .= "<tr>\n";
	$out .= "<td class=\"".$td_style."\" style=\"height: 25px; text-align: left;\">&nbsp;
				<a href=\"".DOC_ROOT."index.php?module=".$module."&topic_id=".$row->id."\">".$row->name."</a>
			</td>\n";
	$out .= "<td class=\"".$td_style."\" style=\"height: 25px; text-align: left;\">&nbsp;".$row->description."</td>\n";
	$out .= "<td class=\"".$td_style."\" style=\"height: 25px;\">
				<a href=\"admin.php?module=".$module."&page=config&topic_id=".$row->id."\">
					<img src=\"images/settings.gif\" alt=\""._SETTINGS."\" title=\""._SETTINGS."\" border=\"0\"></a></td>\n";
	$out .= "<td class=\"".$td_style."\" style=\"height: 25px;\">
				<a href=\"admin.php?module=".$module."&page=template&topic_id=".$row->id."\">TMpL</a></td>\n";
	$out .= "<td class=\"".$td_style."\" style=\"height: 25px;\">
				<a href=\"admin.php?module=".$module."&page=categories&topic_id=".$row->id."\">
					<img src=\"images/folder.gif\" border=\"0\"></a></td>\n";
	$out .= "<td class=\"".$td_style."\" style=\"height: 25px;\">".$row->lang."</td>\n";
	$out .= "<td class=\"".$td_style."\" style=\"height: 25px;\">
				".edit_button("topic_id=".$row->id)."</td>\n";
	$out .= "<td class=\"".$td_style."\" style=\"width: 60px; height: 25px;\">
				".delete_button("delete_topic", "topic_id=".$row->id)."</td>\n";
	$out .= "</td>\n";

	$out .= "</tr>\n";
}
$out .= "</table>\n";

$out .= form_start("topic_id=".get_int('topic_id'));
$out .= "<div class=\"clear_div\">".space(5,35)."<BR>\n";
$out .= "<div class=\"admin_td1\" style=\"width: 250px\">
			Name: ".input_form("topic_name", "textbox", $edit_value, "", "width: 180px")."</div>\n";
$out .= "<div class=\"admin_td1\" style=\"width: 250px\">
			Desc: ".input_form("topic_description", "textbox", $edit_value, "", "width: 200px")."</div>\n";
$select_lang['geo'] = 'geo';
$select_lang['eng'] = 'eng';
$out .= "<div class=\"admin_td1\" style=\"width: 70px\">
			".input_form("topic_lang", "select", $edit_value, $select_lang)."</div>\n";
$out .= "<div class=\"admin_td1\" style=\"width: 70px\">
			<input type=\"submit\" value=\""._ADD."\" name=\"add_topic\"></div>\n";
$out .= "</div>\n";
$out .= form_end();


$html_out['module'] = $out;
?>