<?php
$user_class->permission_end($module, 'user');
global $out;

$out .= "<script type=\"text/javascript\">
function change_message_head_style(type){	if(type == 'inbox'){
		document.getElementById('inbox').className = 'inbox_title inbox_title_active';
		document.getElementById('sent').className = 'inbox_title';
	}
	else if(type == 'sent'){
		document.getElementById('sent').className = 'inbox_title inbox_title_active';
		document.getElementById('inbox').className = 'inbox_title';
	}}
function del_messages(field){	var del_items = document.getElementsByName(field);
	var del_url = '';
	for(i = 0; i < del_items.length; i++){		if(del_items[i].checked == true){			del_url += del_items[i].value + '-';		}	}
	if(del_url !== ''){		window.location = 'index.php?module=".$module."&action=delete_messages&message_type=".get('message_type')."&items=' + del_url;	}}

function check_all_messages(field){	var del_items = document.getElementsByName(field);
	for(i = 0; i < del_items.length; i++){
		if(document.getElementById('check_all').checked == true){			del_items[i].checked = true;
		}
		else{			del_items[i].checked = false;		}
	}}
</script>\n";

$out .= "<div class=\"profile_mian\">\n";
$out .= "	<div class=\"profile_mian_sub\">\n";

include("modules/".$module."/files/profile_head.php");

$message_type = "inbox";
if(get('message_type') == "sent") $message_type = "sent";

//********************* body ***************************
$out .= "<div class=\"inbox_main\">\n";
//******** head ******
$unread_inbox = $query->amount_fields("users_messages", "to_user_id = ".(int)$_SESSION['login']['user_id']." AND sent = 0 AND `read` = 0");
$head_style[$message_type] = " inbox_title_active";
$out .= "<div class=\"inbox_head\">\n";
$out .= "	<div class=\"inbox_title".$head_style['inbox']."\" id=\"inbox\"
				onclick=\"change_message_head_style('inbox'); showform('', '', 'clear_post.php?module=".$module."&page=messages&message_type=inbox&echo=yes', 'inbox_body')\">
				"._INBOX." (".$unread_inbox.")</div>\n";
$out .= "	<div class=\"inbox_title".$head_style['sent']."\" id=\"sent\"
				onclick=\"change_message_head_style('sent'); showform('', '', 'clear_post.php?module=".$module."&page=messages&message_type=sent&echo=yes', 'inbox_body')\">
				"._SENT."</div>\n";
$out .= "	<div class=\"inbox_new_message\">
				".popup_wndow("<img src=\"images/inbox_new_message.gif\" alt=\"new_message\" border=\"0\">&nbsp;"._NEW_MESSAGE."",
								 $module, "new_message", "", 'clear_post')."

			</div>\n";
$out .= "</div>\n";
//********************
//******* body *******
$out .= "<div class=\"inbox_body\" id=\"inbox_body\">\n";
//* body head *
$out_body .= "	<div class=\"inbox_body_head\">\n";
$out_body .= "		<div style=\"float: left; padding-left: 5px; padding-top: 5px\">\n";
$out_body .= "			<input id=\"check_all\" type=\"checkbox\" value=\"1\" onclick=\"check_all_messages('del_message')\">\n";
$out_body .= "			"._CHECK_ALL."\n";
$out_body .= "		</div>\n";
$out_body .= "		<div style=\"float: right; padding-right: 5px; padding-top: 5px\">\n";
$out_body .= "			"._CHECKED."\n";
$out_body .= "			<input type=\"button\" class=\"button\" value=\""._DELETE."\" onclick=\"del_messages('del_message')\">\n";
$out_body .= "		</div>\n";
$out_body .= "	</div>\n";
//*************
//* body body *
$where = "to_user_id = ".(int)$_SESSION['login']['user_id']." AND sent = 0";
if($message_type == "sent"){
	$where = "from_user_id = ".(int)$_SESSION['login']['user_id']." AND sent = 1";
}
$result_messages = $query->select_sql("users_messages", "*", $where, "id DESC");
while($row_message = mysql_fetch_object($result_messages)){
	$user_info = $query->user_info($row_message->from_user_id);
	$div_style = chng_style($div_style, 'inbox_body_body1', 'inbox_body_body2');
	$out_body .= "	<div class=\"".$div_style."\">\n";
	$out_body .= "		<div style=\"float: left;\">\n";
	$out_body .= "			<input name=\"del_message\" type=\"checkbox\" value=\"".$row_message->id."\">\n";
	$out_body .= "		</div>\n";
	$out_body .= "		<div class=\"user_from\">\n";
	$out_body .= "			".user_info_box($row_message->from_user_id, $user_info['name']." ".$user_info['surname'])."\n";
	$out_body .= "			".date("Y-m-d H:i:s", $row_message->add_time)."\n";
	$out_body .= "		</div>\n";
	$bold[0] = "font-weight: bold";
	$out_body .= "		<div class=\"message\">\n";
	$out_body .= "			<div class=\"message_title\" style=\"".$bold[$row_message->read]."\" onclick=\"showform('', '', 'clear_post.php?module=".$module."&page=message_detals&message_type=".get('message_type')."&&message_id=".$row_message->id."&echo=yes', 'inbox_body')\">\n";
	$out_body .= "				".$row_message->title."\n";
	$out_body .= "			</div>\n";
	$out_body .= "			<div class=\"message_text\">\n";
	$out_body .= "				".explode_text($row_message->message, 5)."\n";
	$out_body .= "			</div>\n";
	$out_body .= "		</div>\n";
	$out_body .= "	</div>\n";
}
//*************
//********************
$out .= $out_body;
$out .= "</div>\n";
$out .= "</div>\n";
//******************************************************

$out .= "	</div>\n";
$out .= "</div>\n";

if(get('echo') == "yes"){	echo $out_body;
	exit;}
?>