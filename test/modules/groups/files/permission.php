<?php
$user_class->permission_end($module, 'admin');
global $html_out, $base_dn;
$row_group = $query->select_obj_sql("group", "*", "id = '".get_int('id')."'", "id ASC");

if(get_int('id') == 1){
        $if_admin = 'checked disabled';
}
$out .= "<script type=\"text/javascript\">\n";
$out .= " function custom_perm(check_name){\n";
$out .= " 	if(document.getElementById(check_name).checked == true){;\n";
$out .= "		SetCookie(check_name, 1);";
$out .= " 	}\n";
$out .= " 	else{;\n";
$out .= "		SetCookie(check_name, 0);";
$out .= " 	}\n";
$out .= " }\n";
$out .= "</script>\n";

$out .= " <form action=\"post.php?module=".$module."&id=".get_int('id')."\" method=\"post\"> \n";
$out .= " <center> \n";
$out .= "<table border=\"0\" width=\"800\"> \n";
$out .= " <tr>";
$out .= "     <td align=\"center\" colspan=3 class=\"admin_table_head\">".$row_group->name." (".$row_group->description.") </td> \n";
$out .= " </tr>";
$out .= " <tr>";
$out .= "     <td align=\"center\" width=\"200\" class=\"admin_table_td3\">Module Name</td> \n";
$out .= "     <td align=\"center\" width=\"600\" class=\"admin_table_td3\">Permission</td> \n";
$out .= " </tr>";
$result = $query->select_sql("modules", "*", 1, "id ASC");
while($row = mysql_fetch_object($result)){
	$td_style = $templates->change_style();
	$out .= " <tr>";
	$out .= "   <td class=\"".$td_style."\">\n";
	if($row->custom_permission == 1){		$result_custom = $query->select_sql($row->custom_table);
		$custom_pages = array("&laquo; All &raquo;" => "all");

		while($row_custom = mysql_fetch_object($result_custom)){
			if($row->custom_field == ""){
				$custom_pages[$row_custom->name] = $row_custom->id;
			}
			else{				
				$custom_pages[$row_custom->{$row->custom_field}] = $row_custom->id;			
			}
		}
		$out .= "<b>".$row->module."</b>\n";
		$out .= input_form("custom_".$row->id, "select", get_int('custom_'.$row->id), $custom_pages, "width: 200px", "",
							"onchange=\"showform('custom_id', this.value, 'post.php?module=".$module."&page=custom_permission&module_id=".$row->id."&group_id=".get_int('id')."', 'custom_permissions".$row->id."'); loadinggif('custom_permissions".$row->id."')\"");	}
	else{
		$out .= "<b>".$row->module."</b>\n";
	}
	$out .= "</td>\n";
	$out .= "   <td class=\"".$td_style."\" id=\"custom_permissions".$row->id."\">";
	$result_per = $query->select_sql("permissions", "*", "module_id = ".$row->id."");
	while($row_per = mysql_fetch_object($result_per)){
		$out .= "		<input name=\"".$row->id."_".$row_per->name."\" type=\"checkbox\" value=\"1\" ".$if_admin.permission($row->id, get('id'), "", $row_per->name)."> \n";
		$out .= "<b>".$row_per->name."</b>";
		$out .= " (".$row_per->description.")<BR>\n";
	}
	$out .= "   </td>";
	$out .= " </tr>";
}
$out .= " </table>";
$out .= " <input type=\"submit\" value=\"Send\" name=\"edit_group_per\"> \n";
$out .= " </form> \n";

$user_group_result = $query->select_sql("users g, gt_user_groups ug", "*", "ug.group_id = ".get_int('id')." AND g.id = ug.user_id");
$user_amount = $query->amount_fields("users g, gt_user_groups ug", "ug.group_id = ".get_int('id')." AND g.id = ug.user_id");
$out .= "<BR><table border=\"0\" width=\"540\">\n";
$out .= "	<tr>\n";
$out .= "		<td colspan=6 style=\"color: #008000\">Founded <b>".$user_amount."</b> Users</td>\n";
$out .= "	</tr> \n";
$out .= "	<tr>\n";
$out .= "		<td align=\"center\" width=\"250\" class=\"reg_table_title\"><b>"._NAME.", "._SURNAME."</b></td>\n";
$out .= "		<td align=\"center\" width=\"250\" class=\"reg_table_title\"><b>"._MAIL."</b></td>\n";
$out .= "		<td align=\"center\" width=\"40\" class=\"reg_table_title\">&nbsp;</td>\n";
$out .= "	</tr> \n";

$out .= "	<tr>\n";
$out .= "		<td colspan=6>\n";
$out .= "<div id=\"search_cat\" style=\"overflow : auto; width: 100%; height: 350px;\">\n";
$out .= "<table border=\"0\" width=\"100%\">\n";
while($row = mysql_fetch_object($user_group_result)){
	$out .= "	<tr>\n";
	$out .= "		<td align=\"center\" width=\"250\" class=\"reg_table_td\">".$row->name." ".$row->surname."</td>\n";
	$out .= "		<td align=\"center\" width=\"250\" class=\"reg_table_td\">".$row->mail."</td>\n";
	$out .= "		<td align=\"center\" width=\"40\" class=\"reg_table_td\">
						<a href=\"javascript: yes_no('post.php?module=".$module."&action=user_group_delete&user_id=".$row->user_id."&group_id=".get_int('id')."', '"._YES_NO_DELETE."')\">
                                  <img src=\"images/drop.png\" border=0></a></td>\n";
	$out .= "	</tr>\n";
}
$out .= "</table>\n";
$out .= "</div>";
$out .= "		</td>\n";
$out .= "	</tr> \n";
$out .= "</table>\n";

$html_out['module'] = $out;
unset($out);
?>
