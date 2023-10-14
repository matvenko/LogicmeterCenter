<?php
$user_class->permission_end($module, 'admin');
global $html_out;

$error = array("mail" => _ERROR_EMAIL, "name" => _FILL_FIELDS, "surname" => _FILL_FIELDS,
				 "new_password" => _NO_PASSWORD, "mail_exist" => _MAIL_EXIST);

$edit_value = $_SESSION['form'];
if(get_int('user_id') !== 0){
	$edit_value = $query_l->select_ar_sql("users_info", "*", "id = ".get_int('user_id')."");
}

$out = user_head();
$out .= space(15,15);
if(in_array($error[get('error')], $error)){
	$out .= "<div class=\"error\">".$error[get('error')]."</div>\n";
	$out .= "<style>#".get('error')."{background:#FF8C8C;}</style>";
}
$out .= "<form  action=\"post.php?module=".$module."&user_id=".get_int('user_id')."\" method=\"post\">\n";
$out .= "<div class=\"register_main\">\n";

$out .= "<div class=\"register_left\"> "._NAME.":</div>\n";
$out .= "<div class=\"register_right\">\n";
$out .= input_form("name", "textbox", $edit_value);
$out .= "</div>\n";

$out .= "<div class=\"register_left\"> "._SURNAME.":</div>\n";
$out .= "<div class=\"register_right\">\n";
$out .= input_form("surname", "textbox", $edit_value);
$out .= "</div>\n";

$out .= "<div class=\"register_left\"> "._TEL.":</div>\n";
$out .= "<div class=\"register_right\">\n";
$out .= input_form("tel", "textbox", $edit_value);
$out .= "</div>\n";

$positions = select_items("user_positions", "id", "name");
$out .= "<div class=\"register_left\"> "._POSITION.":</div>\n";
$out .= "<div class=\"register_right\">\n";
$out .= input_form("position", "select", $edit_value, $positions);
$out .= "</div>\n";

$out .= "<div class=\"register_left\">".star()." "._EMAIL.":</div>\n";
$out .= "<div class=\"register_right\">\n";
$out .= input_form("mail", "textbox", $edit_value);
$out .= "</div>\n";

if(get_int('user_id') == 0){
	$out .= "<div class=\"register_left\">".star()." "._PASSWORD.":</div>\n";
	$out .= "<div class=\"register_right\">\n";
	$out .= input_form("new_password", "textbox", $edit_value);
	$out .= "</div>\n";
	$out .= "<div style=\"clear: both; float: right\">\n";
	$out .= "<div style=\"float: right; padding-left: 5px\">\n";
	$out .= "<input type=\"button\" id=\"copy\" value=\"Copy\">
			<input type=\"button\" value=\"Generate\" id=\"generate\" style=\"cursor:pointer\">\n";
	$out .= "</div>\n";
	$out .= "<div id=\"gernerate_password\" style=\"float: right; font-weight: bold; font-size: 16px\"></div>";
	$out .= "</div>\n";
}
$out .= "<div style=\"".$left_div."; height: 30px; clear: both\">\n";
$out .= "<input type=\"submit\" value=\""._SAVE."\" name=\"user_register\">\n";	$out .= "</div>\n";
$out .= "</form>\n";

$out .= "</div>\n";

$out .= "<script type=\"text/javascript\">
$(\"#generate\").click(function(){
	$.get('post.php?module=".$module."&page=admin_chng_pass', {action: 'chng_pass'})
	.done(function(data){
		$(\"#gernerate_password\").html(data);
	})
})
$(\"#copy\").click(function(){
	$(\"#new_password\").val($(\"#gernerate_password\").html());
})
</script>\n";

if(get_int('user_id') !== 0){
	
	
	$add_group_result = $query->select_sql("group", "*", "id != 1 AND (SELECT COUNT(*) FROM ".$global_conf['table_pref']."user_groups WHERE user_id = ".get_int('user_id')." AND group_id = ".$global_conf['table_pref']."group.id) = 0");
	$user_group_result = $query->select_sql("group g, ".$global_conf['table_pref']."user_groups ug", "*", "ug.user_id = ".get_int('user_id')." AND g.id = ug.group_id");
	$out .= " <center> \n";
	$out .= "<h3>Member Of</h3>";
	$out .= "<table border=\"0\" width=\"400\" class=\"result\" style=\"font-size: 12px;\"> \n";
	$out .= " <tr>";
	$out .= "     <td align=\"center\" width=\"340\" height=\"20\" class=\"reg_table_title\"> <b>"._GROUP_NAME."</b></td> \n";
	$out .= "   <td align=\"center\" width=\"80\" height=\"20\" class=\"reg_table_title\"><b>"._DELETE."</b> </td> \n";
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
	    $out .= "   <td class=\"reg_table_td\" width=\"340\"> &nbsp;".$if_admin."&nbsp;".$row_user_group->name." </td>";
	    $out .= "   <td class=\"reg_table_td\" align=\"center\" width=\"80\">
	    			<a href=\"javascript: yes_no('post.php?module=".$module."&action=user_group_delete&group_id=".$row_user_group->group_id."&user_id=".get_int('user_id')."')\">".$if_admin_link."<img src=\"images/drop.png\" border=0></a> </td> \n";
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

$html_out['module'] = $out;
unset($out);
?>