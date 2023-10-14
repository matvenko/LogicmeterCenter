<?php
$user_class->login_action_end();
global $out;

$user_id = $_SESSION['login']['user_id'];
if($user_class->group_perm($module, "admin") && get_int('user_id') !== 0){
	$user_id = get_int('user_id');
}
//**************** head ****************
include("modules/".$module."/files/profile_head.php");
//*************************************

if(get('error') == "chng_info_ok"){
	$out .= "<div class=\"ok\" style=\"font-size: 16px; padding: 10px\">"._CHNG_INFO_OK."</div>\n";
	$out .= "<div style=\"font-size: 16px;\">
				<a href=\"index.php?module=".$module."&page=profile\" style=\"font-size: 16px; font-weight: bold; padding: 10px\">&lt;&lt; "._GO_TO_PROFILE."</a>
			</div>\n";
}
else{
$error = array("city" => _FILL_FIELDS, "mail" => _ERROR_EMAIL, "name" => _FILL_FIELDS, "surname" => _FILL_FIELDS, "pid" => _PID_ERROR, "pid_exist" => _PID_EXIST,
				"tel" => _FILL_FIELDS, "address" => _FILL_FIELDS, "password" => _NO_PASSWORD, "re_password" => _NO_RE_PASSWORD, "mail_exist" => _MAIL_EXIST);

$edit_value = $query->select_ar_sql("users_info", "*", "id = ".(int)$user_id);

if(in_array($error[get('error')], $error)){	
	$out .= "<div class=\"error\">".$error[get('error')]."</div>\n";
	$out .= "<style>#".get('error')."{background:#FF8C8C;}</style>";
}
$td_style = "admin_table_td1 div_pad";
$out .= "<div class=\"register_main\" style=\"margin-top: 20px\">\n";
$out .= "<center>\n";
$out .= "<table class=\"admin_table\" cellspacing=0 style=\"width: 490px; text-align: left\">\n";
$out .= "<form  action=\"clear_post.php?module=".$module."&user_id=".$user_id."\" method=\"post\">\n";
$out .= "  <tr>\n";
$out .= "    <td class=\"admin_table_head\" align=\"center\" colspan=2>"._OWN_INFO."</td>\n";
$out .= "  </tr>\n";
$cities = select_items("cities", "name_".$lang, "id");
$out .= "  <tr>\n";
$out .= "    <td style=\"width: 150px\" class=\"".$td_style."\">".star()." "._CITY.":</td>\n";
$out .= "    <td style=\"width: 325px\" class=\"".$td_style."\">
				".input_form("city", "select", $edit_value, $cities)." "._CITY_DESCRIPTION."
			</td>\n";
$out .= "  </tr>\n";
$td_style = chng_style($td_style, "admin_table_td1 div_pad", "admin_table_td2 div_pad");
$out .= "  <tr>\n";
$out .= "    <td class=\"".$td_style."\">".star()." "._NAME.":</td>\n";
$out .= "    <td class=\"".$td_style."\">
				".input_form("name", "textbox", $edit_value)."
			</td>\n";
$out .= "  </tr>\n";
$td_style = chng_style($td_style, "admin_table_td1 div_pad", "admin_table_td2 div_pad");
$out .= "  <tr>\n";
$out .= "    <td class=\"".$td_style."\">".star()." "._SURNAME.":</td>\n";
$out .= "    <td class=\"".$td_style."\">
				".input_form("surname", "textbox", $edit_value)."
			</td>\n";
$out .= "  </tr>\n";
$td_style = chng_style($td_style, "admin_table_td1 div_pad", "admin_table_td2 div_pad");
$out .= "  <tr>\n";
$out .= "    <td class=\"".$td_style."\">".star()." "._EMAIL.":</td>\n";
$out .= "    <td class=\"".$td_style."\">
				".input_form("mail", "textbox", $edit_value)."
			</td>\n";
$out .= "  </tr>\n";
$td_style = chng_style($td_style, "admin_table_td1 div_pad", "admin_table_td2 div_pad");
$out .= "  <tr>\n";
$out .= "    <td class=\"".$td_style."\">".star()." "._PID.":</td>\n";
$out .= "    <td class=\"".$td_style."\">
				".input_form("pid", "textbox", $edit_value)."
			</td>\n";
$out .= "  </tr>\n";
$td_style = chng_style($td_style, "admin_table_td1 div_pad", "admin_table_td2 div_pad");
$out .= "  <tr>\n";
$out .= "    <td class=\"".$td_style."\">".star()." "._TEL.":</td>\n";
$out .= "    <td class=\"".$td_style."\">
				".input_form("tel", "textbox", $edit_value)."
			</td>\n";
$out .= "  </tr>\n";
$td_style = chng_style($td_style, "admin_table_td1 div_pad", "admin_table_td2 div_pad");
$out .= "  <tr>\n";
$out .= "    <td class=\"".$td_style."\">"._SHIPPING.":</td>\n";
$out .= "    <td class=\"".$td_style."\">
				".input_form("shipping", "checkbox", $edit_value)." "._SHIPPING_DESCRIPTION."
			</td>\n";
$out .= "  </tr>\n";
$td_style = chng_style($td_style, "admin_table_td1 div_pad", "admin_table_td2 div_pad");
$out .= "  <tr>\n";
$out .= "    <td class=\"".$td_style."\">".star()." "._ADDRESS.":</td>\n";
$out .= "    <td class=\"".$td_style."\">
				".input_form("address", "textbox", $edit_value, "", "width: 310px")."<br>"._ADDRESS_DESCRIPTION."
			</td>\n";
$out .= "  </tr>\n";
$out .= "  <tr>\n";
$out .= "    <td class=\"admin_table_head\" align=\"center\" colspan=2>"._FOR_ORGS."</td>\n";
$out .= "  </tr>\n";
$td_style = chng_style($td_style, "admin_table_td1 div_pad", "admin_table_td2 div_pad");
$out .= "  <tr>\n";
$out .= "    <td class=\"".$td_style."\">"._ORG_NAME.":</td>\n";
$out .= "    <td class=\"".$td_style."\">
				".input_form("org_name", "textbox", $edit_value)."
			</td>\n";
$out .= "  </tr>\n";
$td_style = chng_style($td_style, "admin_table_td1 div_pad", "admin_table_td2 div_pad");
$out .= "  <tr>\n";
$out .= "    <td class=\"".$td_style."\">"._ORG_CODE.":</td>\n";
$out .= "    <td class=\"".$td_style."\">
				".input_form("org_code", "textbox", $edit_value)."
			</td>\n";
$out .= "  </tr>\n";
$out .= "</table>\n";

$out .= "<div style=\"padding-top: 10px\"><input type=\"submit\" value=\""._CHANGE."\" name=\"change_info\"></div>";
$out .= "</div>\n";
$out .= "</form>\n";
}

//************* groups **************
if($user_class->group_perm($module, "admin") && get_int('user_id') !== 0){
	$add_group_result = $query->select_sql("group", "*", "id != 1 AND (SELECT COUNT(*) FROM ".$global_conf['table_pref']."user_groups WHERE user_id = ".get_int('user_id')." AND group_id = ".$global_conf['table_pref']."group.id) = 0");
	$user_group_result = $query->select_sql("group g, ".$global_conf['table_pref']."user_groups ug", "*", "ug.user_id = ".get_int('user_id')." AND g.id = ug.group_id");
	$out .= " <center> \n";
	$out .= "<h3>Member Of</h3>";
	$out .= "<table border=\"0\" width=\"400\" class=\"result\" style=\"font-size: 12px;\"> \n";
	$out .= " <tr>";
	$out .= "     <td align=\"center\" width=\"340\" height=\"20\" class=\"admin_table_head\"> <b>"._GROUP_NAME."</b></td> \n";
	$out .= "   <td align=\"center\" width=\"80\" height=\"20\" class=\"admin_table_head\"><b>"._DELETE."</b> </td> \n";
	$out .= " </tr>";
	while($row_user_group = mysql_fetch_object($user_group_result)){
        if($row_user_group->id == 1){
			$if_admin_link = '</a>';
		 	$if_admin = "<font color=\"#800000\"><b>root</b></font>";
	   	}
	    else{
	    	$if_admin_link = '';
	    	$if_admin = '';
	    }

		$out .= " <tr>";
	    $out .= "   <td class=\"admin_table_td1\" width=\"340\"> &nbsp;".$if_admin."&nbsp;".$row_user_group->name." </td>";
	    $out .= "   <td class=\"admin_table_td1\" align=\"center\" width=\"80\">
	    			<a href=\"javascript: yes_no('post.php?module=".$module."&action=user_group_delete&group_id=".$row_user_group->id."&user_id=".get_int('user_id')."')\">".$if_admin_link."<img src=\"images/drop.png\" border=0></a> </td> \n";
	    $out .= " </tr>";
	}
	$out .= " </table>";



	$out .= " <form action=\"post.php?module=".$module."&user_id=".get_int('user_id')."\" method=\"post\"> \n";
	$out .= " <br /> ";
	$out .= " <table border=\"0\" width=\"300\" cellspacing=\"0\" cellpadding=\"3\"> \n";
	$out .= "         <tr> \n";
	$out .= "                 <td align=\"center\"> \n";
	$out .= "		<select size=\"1\" name=\"user_group\">\n";
	$out .= "  		<option value=\"0\">----------</option>\n";
	while($row_group = mysql_fetch_object($add_group_result)){
		$out .= "  <option value=\"".$row_group->id."\" ".$select_group[$row_group->id].">".$row_group->name."</option>\n";
	}
	$out .= "		</select>\n";
	$out .= "         </tr> \n";
	$out .= " </table> <br /> \n";
	$out .= " <input type=\"submit\" value=\""._ADD."\" name=\"add_group\"> \n";
	$out .= " </form> \n";
}

//***********************************
?>
