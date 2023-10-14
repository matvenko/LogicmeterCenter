<?php

if (is_post('send_message')) {
	
	if (post('name') == false) {
		echo request_callback("error", _NO_NAME);
		exit;
	}
	if(!preg_match("/^[_A-Za-z0-9-]+(\.[_A-Za-z0-9-]+)*@[A-Za-z0-9-]+(\.[A-Za-z0-9-]+)*(\.[A-Za-z]{2,3})$/", post('email'))){
		echo request_callback("error", _NO_EMAIL);
		exit;
	}
	if (post('message') == false) {
		echo request_callback("error", _NO_MESSAGE);
		exit;
	}
	
	$global_conf['reply_to'] = post('email');
	$subject = _MAIL_SUBJECT." (".post('name')." - Logiccenter)";
	$mail_text = "<b>" . _NAME . ":</b> " . post('name') . "\r\n<BR><BR>
				  <b>" . _EMAIL . ":</b> " . post('email') . "\r\n<BR><BR>
    			  <b>" . _MESSAGE . ":</b><br /><br /> " . nl2br(post('message')) . "\r\n<BR><BR>";
	send_mail($global_conf['contac_email'], $subject, $mail_text);
	
	echo request_callback("ok_message", _MESSAGE_SENT);
	exit;
}

if (is_post('send_meeting')) {

	if (post('name') == false) {
		echo request_callback("error", _NO_NAME);
		exit;
	}
	if(!preg_match("/^[_A-Za-z0-9-]+(\.[_A-Za-z0-9-]+)*@[A-Za-z0-9-]+(\.[A-Za-z0-9-]+)*(\.[A-Za-z]{2,3})$/", post('email'))){
		echo request_callback("error", _NO_EMAIL);
		exit;
	}
	if (post('tel') == false) {
		echo request_callback("error", _NO_TEL);
		exit;
	}
	if (post('message') == false) {
		echo request_callback("error", _NO_MESSAGE);
		exit;
	}

	$global_conf['reply_to'] = post('email');
	$subject = _MAIL_SUBJECT_MEETING." (".post('name')." - Logiccenter)";
	$mail_text = "<b>" . _NAME . ":</b> " . post('name') . "\r\n<BR><BR>
				  <b>" . _EMAIL . ":</b> " . post('email') . "\r\n<BR><BR>
				  <b>" . _TEL . ":</b> " . post('tel') . "\r\n<BR><BR>
				  <b>" . _MESSAGE . ":</b><br /><br /> " . nl2br(post('message')) . "\r\n<BR><BR>";
	send_mail($global_conf['contac_email'], $subject, $mail_text);

	echo request_callback("ok_message", _MESSAGE_SENT);
	exit;
}

$pages = array (
		'form',
		'ok',
		'meeting' 
);
load_page(get('page'), $pages, 'form');

?>