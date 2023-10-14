<?php
$user_info = $user_class->user_info(get_int('user_id'));

$img_size = image_size($user_info['pic_big'], 70, 70);

$out = "<div class=\"user_info_box\" style=\"position: relative\" onmouseover=\"show_block('".get_int('div_id')."')\"
				onmouseout=\"hide_block('".get_int('div_id')."');\">\n";
$out .= "	<div class=\"user_info_box_sub\">\n";
$out .= "		<div style=\"clear:both; padding: 10px\">\n";
$out .= "			<div style=\"float:left;width: 200px\">\n";
$out .= "				<div style=\"clear:both\">
							<a style=\"color: #000; font-size: 13px;\" href=\"index.php?module=users&page=user_info&user_id=".get_int('user_id')."\"><b>".$user_info['name']." ".$user_info['surname']."</b></a></div>\n";
if(strlen($user_info['org']) !== 0){
	$out .= "	<div>".$user_info['org']."</div>\n";
}
if(strlen($user_info['pers_type']) !== 0){
	$out .= "		<div style=\"color: #616161;\"><i>".$user_info['pers_type']."</i></div>\n";
}
$out .= "			</div>\n";
$out .= "		</div>\n";

$out .= "		<div style=\"clear:both; padding: 10px\">\n";
$user_info = $query->user_info(get_int('user_id'));
$out .= "<div class=\"most_readed_comments_comments_sub\">
		 	<b>"._LAST_LOGIN.":</b> ".date("Y-m-d H:i:s", (int)$user_info['last_login'])."
		 </div>\n";

$out .= "		</div>\n";

$out .= "	</div>\n";
$out .= "</div>\n";

echo $out;
?>