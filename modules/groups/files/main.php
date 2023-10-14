<?php
$user_class->permission_end($module, 'admin');
global $html_out;
$edit_result = $query->select_sql("group");

$out .= "<table border=\"0\" width=\"580\" class=\"result\"> \n";
$out .= " <tr>";
$out .= "     <td align=\"center\" width=\"140\" class=\"reg_table_title\">Group Name</td> \n";
$out .= "     <td align=\"center\" width=\"200\" class=\"reg_table_title\">Description</td> \n";
$out .= "     <td align=\"center\" width=\"80\" class=\"reg_table_title\">Karma</td> \n";
$out .= "   <td align=\"center\" width=\"80\" class=\"reg_table_title\">"._EDIT." </td> \n";
$out .= "   <td align=\"center\" width=\"80\" class=\"reg_table_title\">"._DELETE." </td> \n";
$out .= " </tr>";
while($row_edit = mysql_fetch_object($edit_result)){
	if($row_edit->id == 1){
		$if_admin_link = '</a>';
		$if_admin = "<font color=\"#800000\"><b>root</b></font>";
	}
	else{
		$if_admin_link = '';
		$if_admin = '';
	}

    $karma_value = $row_edit->karma;
    if((int) $karma_value == 0) $karma_value = "No";

	$out .= " <tr>";
	$out .= "   <td class=\"reg_table_td\"> &nbsp;".$if_admin."&nbsp;<a href=\"admin.php?module=".$module."&page=permission&id=".$row_edit->id."\">".$row_edit->name."</a> </td>";
	$out .= "   <td class=\"reg_table_td\"> &nbsp;".$row_edit->description."</a> </td>";
	$out .= "   <td align=\"center\" class=\"reg_table_td\">".$karma_value."</td> \n";
	$out .= "   <td align=\"center\" class=\"reg_table_td\"> <a href=\"admin.php?module=".$module."&action=edit&id=".$row_edit->id."')\"><img src=\"images/edit.png\" border=0></a> </td> \n";
	$out .= "   <td align=\"center\" class=\"reg_table_td\"> <a href=\"javascript: yes_no('post.php?module=".$module."&action=delete_group&id=".$row_edit->id."')\">".$if_admin_link."<img src=\"images/drop.png\" border=0></a> </td> \n";
	$out .= " </tr>";
}
$out .= " </table>";


if(get('action') == 'edit'){
        $result = $query->select_sql("group", "*", "id = '".get_int('id')."'");
        $row = mysql_fetch_object($result);
        $group_value = $row->name;
        $group_desc = $row->description;
        $karma = $row->karma;
        $action = 'edit';
}
else{
        $action = 'add';
}

$out .= " <form action=\"post.php?module=".$module."&id=".get_int('id')."\" method=\"post\"> \n";
$out .= " <br /><br /> ";
$out .= " <table border=\"0\" width=\"580\" class=\"add_edit_menu\" cellspacing=\"0\" cellpadding=\"3\"> \n";
$out .= "         <tr> \n";
$out .= "                 <td> \n";
$out .= "                      Group name<input type=\"text\" name=\"group_name\" style=\"width: 100px\" value=\"".$group_value."\">&nbsp;&nbsp;&nbsp; \n";
$out .= "                      Description<input type=\"text\" name=\"group_desc\" style=\"width: 200px\" value=\"".$group_desc."\"> \n";
$out .= "                      Karma<input type=\"text\" name=\"karma\" style=\"width: 50px\" value=\"".$karma."\"></td> \n";
$out .= "         </tr> \n";
$out .= " </table> <br /> \n";
$out .= " <input type=\"submit\" value=\"Send\" name=\"".$action."_group\"> \n";
$out .= " </form> \n";

$html_out['module'] = $out;
unset($out);
?>
