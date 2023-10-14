<?php
$user_class->permission_end($module, 'admin');
global $out, $html_out;
$out = "";

$row_head = $query->select_obj_sql("topic_pages", "*", "id = ".get_int('topic_id')."");



$edit_value = $query->select_ar_sql("topic_config", "*", "topic_id = ".get_int('topic_id')."");

$field_style = "width: 140px; height: 25px";
$form_style = "width: 100px";
$text_style = "width: 300px; height: 25px";
$out .= "<div style=\"clear: both\">".space(5, 10)."</div>\n";

$out .= form_start("config_id=".$edit_value['id']."&topic_id=".get_int('topic_id'));

$out .= "<table>\n";
$out .= "<tr>\n";
$out .= "<td class=\"table_head\" colspan=2>
			<a href=\"admin.php?module=".$module."&page=admin_main\" style=\"color: #FFF\">".$row_head->name."</a></td>\n";
$out .= "</tr>\n";
$out .= "<tr>\n";
$out .= "<td class=\"table_td1\">"._TOPICS_IN_PAGE."</td>\n";
$out .= "<td class=\"table_td1\">".
			input_form("topics_on_page", "textbox", $edit_value, "", $form_style)."</td>\n";
$out .= "</tr>\n";
/*
$out .= "<div class=\"clear_div\">\n";
$out .= "<div class=\"table_td2\" style=\"clear: left; ".$text_style."\">"._COMMENT_PER_PAGE."</div>\n";
$out .= "<div class=\"table_td2\" style=\"".$field_style."\">".
			input_form("comment_per_page", "textbox", $edit_value, "", $form_style)."</div>\n";
$out .= "</div>\n";

$out .= "<div class=\"clear_div\">\n";
$out .= "<div class=\"table_td1\" style=\"clear: left; ".$text_style."\">"._MORE_COMMENTS_AMOUNT."</div>\n";
$out .= "<div class=\"table_td1\" style=\"".$field_style."\">".
			input_form("more_comments_amount", "textbox", $edit_value, "", $form_style)."</div>\n";
$out .= "</div>\n";*/

$out .= "<tr>\n";
$out .= "<td class=\"table_td2\">"._BIG_PIC_WIDTH."</td>\n";
$out .= "<td class=\"table_td2\">".
			input_form("big_img_width", "textbox", $edit_value, "", $form_style)."px</td>\n";
$out .= "</tr>\n";

$out .= "<tr>\n";
$out .= "<td class=\"table_td1\">"._BIG_PIC_HEIGHT."</td>\n";
$out .= "<td class=\"table_td1\">".
			input_form("big_img_height", "textbox", $edit_value, "", $form_style)."px</td>\n";
$out .= "</tr>\n";

$out .= "<tr>\n";
$out .= "<td class=\"table_td2\">"._SMALL_PIC_WIDTH."</td>\n";
$out .= "<td class=\"table_td2\">".
			input_form("small_img_width", "textbox", $edit_value, "", $form_style)."px</td>\n";
$out .= "</tr>\n";

$out .= "<tr>\n";
$out .= "<td class=\"table_td1\">"._SMALL_PIC_HEIGHT."</td>\n";
$out .= "<td class=\"table_td1\">".
			input_form("small_img_height", "textbox", $edit_value, "", $form_style)."px</td>\n";
$out .= "</tr>\n";

$out .= "<tr>\n";
$out .= "<td class=\"table_td2\">"._TOPIC_NEED_ACCEPT."</td>\n";
$out .= "<td class=\"table_td2\">".
			input_form("topic_need_accept", "checkbox", $edit_value, "", $form_style)."</td>\n";
$out .= "</tr>\n";

$out .= "<tr>\n";
$out .= "<td class=\"table_td2\">"._MULTI_DATES."</td>\n";
$out .= "<td class=\"table_td2\">".
		input_form("multi_dates", "checkbox", $edit_value, "", $form_style)."</td>\n";
$out .= "</tr>\n";
/*
$out .= "<div class=\"clear_div\">\n";
$out .= "<div class=\"table_td1\" style=\"clear: left; ".$text_style."\">"._COMMENT_NEED_ACCEPT."</div>\n";
$out .= "<div class=\"table_td1\" style=\"".$field_style."\">".
			input_form("comment_need_accept", "checkbox", $edit_value, "", $form_style)."</div>\n";
$out .= "</div>\n";

$out .= "<div class=\"clear_div\">\n";
$out .= "<div class=\"table_td2\" style=\"clear: left; ".$text_style."\">"._MAX_TOPIC_TEXT."</div>\n";
$out .= "<div class=\"table_td2\" style=\"".$field_style."\">".
			input_form("max_topic_text", "textbox", $edit_value, "", $form_style)."</div>\n";
$out .= "</div>\n";

$out .= "<div class=\"clear_div\">\n";
$out .= "<div class=\"table_td1\" style=\"clear: left; ".$text_style."\">"._MAX_COMMENT_TEXT."</div>\n";
$out .= "<div class=\"table_td1\" style=\"".$field_style."\">".
			input_form("max_comment_text", "textbox", $edit_value, "", $form_style)."</div>\n";
$out .= "</div>\n";*/

$out .= "<tr>\n";
$out .= "<td class=\"table_td1\" colspan=2>
			<input type=\"submit\" value=\""._EDIT."\" name=\"edit_config\"></div>\n";
$out .= "</td>\n";
$out .= "</table>\n";
$out .= form_end();

$html_out['module'] = $out;
?>