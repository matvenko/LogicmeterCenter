<?php
require_once("init.php");

include("modules/".$module."/index.php");

$out_left = $out_right = "";
if(get('type') == "popup"){
	$out_left = "<div id=\"custom-content\" class=\"popup_block ".get('custom_style')."\"><div>\n";
	$out_right = "</div></div>\n";
}

echo $out_left.$out.$out_right;
?>