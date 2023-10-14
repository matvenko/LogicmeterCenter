<?php
$user_class->group_perm($module, 'add_comment');
global $out, $topic_conf;
$out = '';

if(get_int('comment_id') !== 0 && $user_class->group_perm($module, 'edit_comment')){
	$row_edit = $query->select_obj_sql("topic_comments", "comment", "id = ".get_int('comment_id')."");
	$edit_value['comment_text'] = $row_edit->comment;
}

$out .= open_popup();

$out .= "<div class=\"login1\" onclick=\"document.getElementById('respodents_area').innerHTML = '';\">\n";
$out .= "	<form action=\"clear_post.php?module=".$module."\" method=\"post\" onsubmit=\"return check_message('q_addressee', 'respodents_area', '"._NO_USER_SELECTED."', 'message_title', '"._NO_TITLE."')\">\n";
//******** adresat ****************
if($user_class->group_perm("school_support", "admin")){
	$event = "showform('respodent_name', this.value, 'clear_post.php?module=".$module."&page=ajax_users&echo=yes', 'respodents_area');show_block('respodents_area')";
	$out .= "<div style=\"padding-top: 10px;\">
				<div style=\"float: left; padding-left: 10px; padding-right: 5px;\">"._ADDRESSEE."</div>\n
				<div style=\"float: left\">\n
					<div>
						<input name=\"q_addressee_id\" id=\"q_addressee_id\" type=\"hidden\">
						<input name=\"q_addressee_string\" id=\"q_addressee_string\" type=\"hidden\">
						<input name=\"q_addressee\" id=\"q_addressee\" type=\"text\" autocomplete=\"off\" value=\"\" style=\"width: 200px;\"
							onkeyup=\"".$event."\" onmouseup=\"".$event."\">
					</div>
					<div id=\"respodents_area\"></div>
				</div>
			</div>\n";
}
//*********************************

$out .= "	<div style=\"clear: both;text-align: left; padding-left: 10px; padding-top: 10px;\">"._TITLE."\n";
$out .= input_form("message_title", "textbox", $edit_value, "", "width: 350px");
$out .= "	</div>\n";

$out .= "	<div style=\"clear: both; padding-top: 15px;\">\n";
$out .= input_form("message_text", "textarea", $edit_value, "", "", "add_message_text");
$out .= "	</div>\n";

$out .= "	<div style=\"clear: both; padding: 10px;\">\n";
$out .= "		<input type=\"submit\" value=\""._SEND."\" name=\"send_message\" class=\"button\">\n";
$out .= "	</div>\n";

$out .= "</div>\n";

$out .= "</div>\n";
$out .= form_end();

$out .= close_popup();

echo $out;

?>