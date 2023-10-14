<?php
global $out;

//*** head text
$_GET['link_id'] = 7;
include("modules/text/files/text.php");

$replace_fields['head_text'] = $out;

$replace_fields['module'] = $module;
$replace_fields['_MESSAGE_TITLE'] = _MESSAGE_TITLE;
$replace_fields['_SEND'] = _SEND;

$replace_fields['_YOURNAME'] = _YOURNAME;
$replace_fields['name'] = input_form("name", "textbox", $_SESSION['send']);
$replace_fields['_YOUREMAIL'] = _YOUREMAIL;
$replace_fields['mail'] = input_form("email", "textbox", $_SESSION['send']);
$replace_fields['_MESSAGE_TEXT'] = _MESSAGE_TEXT;
$replace_fields['message'] = input_form("message", "textarea", $_SESSION['send']);

$error = array(1 => _NO_NAME, 2 => _NO_EMAIL, 3 => _NO_MESSAGE);

$out = $templates->gen_module_html($replace_fields, "form");


?>