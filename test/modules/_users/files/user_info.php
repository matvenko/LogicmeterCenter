<?php
global $out;

$user_info = $query->user_info(get_int('user_id'));

$img_size = image_size($user_info['pic_big'], 200, 240);

$out .= "<div style=\"clear: both; width: 540px; overflow: hidden\">\n";
$out .= "	<div style=\"float: left;\">\n";
$out .= "		<div style=\"height: ".($img_size['height']+40)."px; width: ".($img_size['width']+10)."px; background: #FFF;\">\n";
$out .= "			<div style=\"padding: 5px\">".pic_resize($user_info['pic_big'], "", 200, 240)."</div>\n";
$out .= "			<div style=\"clear: both;padding-top: 5px;\">
						<img src=\"images/send_message.gif\" alt=\"send_message\" border=\"0\">
						".space(7)."\n";
if($user_class->login_action() && get_int('user_id') !== (int)$_SESSION['login']['user_id'] && $query->amount_fields("users_friends", "friend_id = ".(int)$_SESSION['login']['user_id']." AND user_id = ".get_int('user_id')."") == 0){
	$out .= " 			<span id=\"follow_button\">
							".ajax_get("follow_button", "<img src=\"images/follow.gif\" alt=\"follow\" border=\"0\">", $module, "", "action=follow&user_id=".get_int('user_id')."", "clear_post")."</span>\n";
}
else{
	$out .= "  			<span id=\"follow_button\">
							<img src=\"images/follow_disable.png\" alt=\"follow\" border=\"0\"></span>\n";
}
$out .= "			</div>\n";
$out .= "		</div>\n";
$follows_amount = $query->amount_fields("users_friends", "user_id = ".get_int('user_id')."");
$out .= "		<div style=\"clear:both;\">
					<div style=\"border-top: 1px solid #d6d6d6; border-bottom: 1px solid #d6d6d6; border-left: 1px solid #d6d6d6;
								 float: left; padding: 5px; background: #f8f8f8\">
						"._FOLLOWS." ".$follows_amount." "._PEOPLE."
					</div>
					<div style=\"border: 1px solid #d6d6d6;
								 float: left; padding: 5px; background: #f8f8f8\">
						"._KARMA." ".$user_info['karma']."
					</div>
				</div>\n";
$out .= "	</div>\n";
$out .= "	<div style=\"float: left; width: 320px;\">\n";
$out .= "		<div style=\"clear: both;\" class=\"user_info_name\">".$user_info['name']." ".$user_info['surname']."</div>\n";
$out .= "		<div style=\"clear: both;\" class=\"user_info\"><b>"._BITHDAY.":</b> ".$user_info['birthday']."</div>\n";
if(strlen($user_info['org']) !== 0){
	$out .= "		<div style=\"clear: both;\" class=\"user_info\"><b>"._ORG.":</b> ".$user_info['org']."</div>\n";
}
if(strlen($user_info['pers_type']) !== 0){
	$out .= "		<div style=\"clear: both;\" class=\"user_info\"><b>"._PERS_TYPE.":</b> ".$user_info['pers_type']."</div>\n";
}
$out .= "		<div style=\"clear: both;\" class=\"user_info\"><b>"._EMAIL.":</b> ".$user_info['mail']."</div>\n";
if(strlen($user_info['skype']) !== 0){
	$out .= "		<div style=\"clear: both;\" class=\"user_info\">".$user_info['skype']."</div>\n";
}
if(strlen($user_info['facebook_link']) !== 0){
	$out .= "		<div style=\"clear: both;\" class=\"user_info\">
						<a target=\"blank\" href=\"".$user_info['facebook_link']."\"><img src=\"images/facebook.gif\" alt=\"facebook\" border=\"0\"></a></div>\n";
}
$out .= "	<div>\n";
$out .= "</div>\n";

?>