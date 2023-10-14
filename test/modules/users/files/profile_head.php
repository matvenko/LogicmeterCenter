<?php

$out .= "<div class=\"profile_head\">\n";
$out .= "	<div class=\"profile_head_links\">\n
				<a href=\"index.php?module=".$module."&page=messages\">"._MY_MESSAGES."</a>\n
			</div>\n";
$out .= "	<div class=\"profile_head_links\">|</div>\n";
$out .= "	<div class=\"profile_head_links\">\n
				<a href=\"index.php?module=".$module."&page=profile\">"._MY_PROFILE."</a>\n
			</div>\n";
$out .= "	<div class=\"profile_head_links\">|</div>\n";
$out .= "	<div class=\"profile_head_links\">\n
				<a href=\"index.php?module=".$module."&page=chng_pass\">"._CHNG_PASS."</a>\n
			</div>\n";
$out .= "</div>\n";

?>