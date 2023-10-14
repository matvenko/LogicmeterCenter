<?php
global $out;

$replace_fields['_JANUARY'] = _JANUARY;
$replace_fields['_FABRUARY'] = _FABRUARY;
$replace_fields['_MARCH'] = _MARCH;
$replace_fields['_APRIL'] = _APRIL;
$replace_fields['_MAY'] = _MAY;
$replace_fields['_JUNE'] = _JUNE;
$replace_fields['_JULY'] = _JULY;
$replace_fields['_AUGUST'] = _AUGUST;
$replace_fields['_SEPTEMBER'] = _SEPTEMBER;
$replace_fields['_OCTOMBER'] = _OCTOMBER;
$replace_fields['_NOVEMBER'] = _NOVEMBER;
$replace_fields['_DECEMBER']= _DECEMBER;

$replace_fields['_W_OR'] = _W_OR;
$replace_fields['_W_SAM'] = _W_SAM;
$replace_fields['_W_OTX'] = _W_OTX;
$replace_fields['_W_XUT'] = _W_XUT;
$replace_fields['_W_PAR'] = _W_PAR;
$replace_fields['_W_SHAB'] = _W_SHAB;
$replace_fields['_W_KV'] = _W_KV;

//*** head text
$_GET['link_id'] = 38;
include("modules/text/files/text.php");

$replace_fields['head_text'] = $out;

$replace_fields['module'] = $module;
$replace_fields['_MESSAGE_TITLE'] = _MESSAGE_TITLE;
$replace_fields['_SEND'] = _SEND;

$replace_fields['_YOURNAME'] = _YOURNAME;
$replace_fields['name'] = input_form("name", "textbox", $_SESSION['send']);
$replace_fields['_YOUREMAIL'] = _YOUREMAIL;
$replace_fields['mail'] = input_form("email", "textbox", $_SESSION['send']);
$replace_fields['_TEL'] = _TEL;
$replace_fields['tel'] = input_form("tel", "textbox", $_SESSION['send']);
$replace_fields['_MESSAGE_TEXT'] = _MESSAGE_TEXT;
$replace_fields['message'] = input_form("message", "textarea", $_SESSION['send'], "", "height: 135px");

$error = array(1 => _NO_NAME, 2 => _NO_EMAIL, 3 => _NO_MESSAGE);

$out = $templates->gen_module_html($replace_fields, "meeting");


?>