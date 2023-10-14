<?php
$user_class->permission_end($module, 'admin');
global $html_out;
$out .= " <a href=\"admin.php?module=".$module."\"><b>"._MENU_ADMIN."</b></a> <p> \n";
$out .= " <form action=\"post.php?module=".$module."\" method=\"post\"> ";
$out .= " <center> ";
$out .= " <a href=\"admin.php?module=".$module."\">"._MENU_ADMIN."</a> <br /><br />";
$out .= " <input name=\"lang\" type=\"hidden\" value=\"".$lang."\"> ";

$out .= " <table border=\"0\" width=\"500\" id=\"table1\"> ";
$out .= "         <tr> ";
$out .= "                 <td class=\"menu_title\" align=\"center\" width=\"250\">"._NAME."</td> ";
$out .= "                 <td class=\"menu_title\" align=\"center\" width=\"150\">"._POSITION."</td> ";
$out .= "         </tr> ";
$out .= "         <tr> ";
$out .= "                 <td class=\"menu_td\" width=\"250\" align=\"center\"> ";
$out .= "                      <input type=\"text\" name=\"menu_name\" size=\"20\"> ";
$out .= "                 </td> ";
$out .= "                 <td class=\"menu_td\" width=\"150\" align=\"center\"> ";
$out .= "                      <select size=\"1\" name=\"menu_pos\"> ";
$out .= "                                 <option value=\"1\">Horizontal</option> ";
$out .= "                                 <option value=\"2\">vertical</option> ";
$out .= "                                 </select></td> ";
$out .= "         </tr> ";
$out .= " </table> <br />";

$out .= " <input type=\"submit\" value=\"Send\" name=\"add_menu\"> ";

$html_out['module'] = $out;
unset($out);
?>