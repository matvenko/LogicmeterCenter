<?php
global $out;
if(is_post('restore_password')){
	if($query->amount_fields("users", "mail = '".post('email')."'") == 1){
		$password = substr(md5(rand()), 6, 6);
		$md_pass = md5($password);
		$query->update_sql("users", array("password" => $md_pass), "mail = '".post('email')."'");

		$subject = _MAIL_SUBJECT;
	    $headers = "Content-type: text/html; charset=utf-8" . "\r\n";
	    $headers .= "From: no-reply@biofood.ge" . "\r\n";
    	$mail_text = _YOUR_NEW_PASSWORD." <b>".$password."</b>";
		@mail(post('email'), $subject, $mail_text, $headers);
        $_SESSION['restore']['email'] = post('email');
		header("Location: index.php?module=messages&message=new_password&email=".post('email')."");
	    exit;
	}
	else{
		$_SESSION['restore']['email'] = post('email');
		$_SESSION['restore']['pid'] = post('tel');
		header("Location: index.php?module=".$module."&page=restore_pass&error=no_user");
		exit;
	}
}

$error = array('no_user' => _NO_USER);
$out .= "<br />";
$out .= "<table border=\"0\" width=\"500\" cellpadding=\"5\" class=\"proj_reg\">\n";
$out .= " <form name=\"proj_reg\" action=\"clear_post.php?module=".$module."&page=restore_pass\" method=\"post\" enctype='multipart/form-data'> \n";
if(in_array($error[get('error')], $error)){
	$error_back[get('error')] = "style=\"background: #FFA8A8;\"";
	$out .= " 	<tr> \n";
	$out .= " 		<td colspan=\"2\" style=\"background: #FFA8A8;font-size: 16px; font-weight: bold;\">".$error[get('error')]."<br> \n";
	$out .= " 	</tr> \n";
}
$out .= "	<tr>\n";
$out .= "		<td colspan=\"2\" align=\"center\" class=\"addproject_color\">"._RESTORE_PASSWORD."</td>\n";
$out .= "	</tr>\n";
$out .= "	<tr>\n";
$out .= "		<td width=\"50%\" align=\"right\">"._EMAIL."</td>\n";
$out .= "		<td width=\"50%\"><input name=\"email\" type=\"text\" value=\"".$_SESSION['restore']['email']."\"></td>\n";
$out .= "	</tr>\n";
$out .= "	<tr>\n";
$out .= "		<td colspan=\"2\" align=\"center\"><input type=\"submit\" name=\"restore_password\" value=\""._SEND."\"></td>\n";
$out .= "	</tr>\n";
$out .= "</table>\n";

?>