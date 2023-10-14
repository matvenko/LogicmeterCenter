<?php
$_GET['link_id'] = 4;
$module = 'text';
include("modules/text/files/text.php");

echo "<div id=\"custom-content\" class=\"popup_block\" style=\"padding: 50px; width: 950px\">\n";
echo "<div style=\"height: 600px; overflow: auto; padding-right: 10px\">\n";
echo $out;
echo "</div>\n";
echo "</div>\n";