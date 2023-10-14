<?php
$user_class->permission_end($module, 'admin');

$row_user = $query->select_obj_sql("users", "mail", "id = ".get_int('user_id')."");

$div_style = "padding: 10px; float: left";

echo open_popup();


echo "<div id=\"action\" class=\"ok\"></div>\n";
//echo "<form action=\"post.php?module=".$module."&user_id=".get_int('user_id')."\" method=\"post\">\n";
echo " <table border=\"0\" width=\"400\" height=\"150\" style=\"background: #FFF;\"> \n";
echo " 	<tr> \n";
echo " 		<td align=\"right\"><b>New Email</b> </td> \n";
echo " 		<td><input name=\"new_email\" id=\"new_email\" type=\"text\" style=\"width: 300px;\" value=\"".$row_user->mail."\"></td> \n";
echo " 	</tr> \n";
echo " 	<tr> \n";
echo " 		<td align=\"right\">&nbsp;</td> \n";
echo " 		<td><input type=\"submit\" name=\"chng_user_email\" value=\"Change\"
					onclick=\"showform('action', 'chng_user_email', 'post.php?module=".$module."&new_email='+document.getElementById('new_email').value+'&user_id=".get_int('user_id')."', 'action');\"></td> \n";
echo " 	</tr> \n";
echo " </table> \n";

//echo "</form>\n";
echo close_popup();

?>