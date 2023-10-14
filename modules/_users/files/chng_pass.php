<?php
$user_class->permission_end($module, 'user');
global $out;

$out .= "<div class=\"profile_mian\">\n";
$out .= "	<div class=\"profile_mian_sub\">\n";

//include("modules/".$module."/files/profile_head.php");

//************* error messages **************************
$error = array("old_pass_error" => _OLD_PASSWORD_ERROR, "password" => _PASSWORD_ERROR,
				"re_password" => _RE_PASSWORD_ERROR, "chng_ok" => "<font color=\"#008000\">"._PASSWORD_CHNG_OK."</font>",
				"email" => _ERROR_EMAIL, "mail_exist" => _MAIL_EXIST, "mail_chng_ok" => "<font color=\"#008000\">"._MAIL_CHNG_OK."</font>");

if(in_array($error[get('error')], $error)){
	$out .= "<div class=\"error\">".$error[get('error')]."</div>\n";
	$out .= "<style>#".get('error')."{background:#FF8C8C;}</style>";
}
//*******************************************************

//********************* body ***************************
//*********************** chng password ****************
$out .= form_start("", "user");
$out .= "<div style=\"width: 300px\">\n";
$out .= "<div class=\"table_head\" style=\"width: 300px\">"._CHNG_PASSWORD."</div>\n";

$out .= "<div style=\"clear:both\">\n";
$out .= "<div class=\"table_td1\">"._OLD_PASSWORD."</div>\n";
$out .= "<div class=\"table_td1\">".
			input_form("old_password", "password")."</div>\n";
$out .= "</div>\n";

$out .= "<div style=\"clear:both\">\n";
$out .= "<div class=\"table_td2\">"._NEW_PASSWORD."</div>\n";
$out .= "<div class=\"table_td2\">".
			input_form("new_password", "password")."</div>\n";
$out .= "</div>\n";

$out .= "<div style=\"clear:both\">\n";
$out .= "<div class=\"table_td1\">"._RE_PASSWORD."</div>\n";
$out .= "<div class=\"table_td1\">".
			input_form("re_password", "password")."</div>\n";
$out .= "</div>\n";

$out .= "<div style=\"clear:both\">\n";
$out .= "<div class=\"registration_left\">&nbsp;</div>\n";
$out .= "<div class=\"registration_right registration_right2\" style=\"padding-top: 10px;\">\n";
$out .= "	<input type=\"submit\" value=\""._CHANGE."\" name=\"chng_password\" class=\"buttong\"></div>\n";
$out .= "</div>\n";
$out .= "</div>\n";
$out .= form_end();
//******************************************************
/*
//*********************** chng email ****************
$out .= form_start("", "user");
$out .= "<div class=\"chng_password_head\">"._CHNG_EMAIL."</div>\n";

$out .= "<div style=\"clear:both; padding-left: 10px;\">"._CURENT_EMAIL." <b>".$_SESSION['login']['username']."</b></div>\n";

$out .= "<div class=\"registration_left\">"._NEW_MAIL."</div>\n";
$out .= "<div class=\"registration_right\">".
			input_form("new_mail", "textbox", get('mail'))."</div>\n";
$out .= "</div>\n";

$out .= "<div style=\"clear:both\">\n";
$out .= "<div class=\"registration_left\">&nbsp;</div>\n";
$out .= "<div class=\"registration_right registration_right2\" style=\"padding-top: 10px;\">\n";
$out .= "	<input type=\"submit\" value=\""._CHANGE."\" name=\"chng_email\" class=\"buttong\"></div>\n";
$out .= "</div>\n";
$out .= form_end();
//******************************************************
 
 */
//******************************************************

$out .= "	</div>\n";
$out .= "</div>\n";
?>