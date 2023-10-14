<?php
$user_class->permission_end($module, 'user');
global $out;
$message_type = "inbox";
if(get('message_type') == "sent") $message_type = "sent";
$where = "id = ".get_int('message_id')." AND to_user_id = ".(int)$_SESSION['login']['user_id']." AND sent = 0";
if($message_type == "sent"){
	$where = "id = ".get_int('message_id')." AND from_user_id = ".(int)$_SESSION['login']['user_id']." AND sent = 1";
}
$row_message = $query->select_obj_sql("users_messages", "*", $where);

if($message_type == "inbox"){
	$user_info = $query->user_info($row_message->from_user_id);
}
elseif($message_type == "sent"){
	$user_info = $query->user_info($_SESSION['login']['user_id']);
}

$out_body .= "<div class=\"inbox_body\" id=\"inbox_body\">\n";
$out_body .= "	<div class=\"inbox_body_body1\">\n";
$out_body .= "		<div style=\"width: 180px; float: left; border-right: 2px solid #FFF\">\n";
$out_body .= "			".user_info_box($row_message->from_user_id, $user_info['name_surname'])."\n";
$out_body .= "			".date("Y-m-d H:i:s", $row_message->add_time)."\n";
$out_body .= "		</div>\n";
$out_body .= "		<div class=\"message_title\" style=\"padding-left: 5px; float: left;\">\n";
$out_body .= "				".$row_message->title."\n";
$out_body .= "		</div>\n";
$out_body .= "	</div>\n";
$out_body .= "	<div class=\"inbox_body_body2\">\n";
$out_body .= "			<div class=\"message_text\">\n";
$out_body .= "				".explode_text($row_message->message, 5)."\n";
$out_body .= "			</div>\n";
$out_body .= "	</div>\n";

$out_body .= "</div>\n";

$query->update_sql("users_messages", array("read" => 1), "id = ".get_int('message_id')."");


if(get('echo') == "yes"){
	echo $out_body;
	exit;
}
?>