<?php
$user_class->permission_end($module, 'admin');
include("language/".$lang."/schools_stats.php");

$region = select_items("region", "name", "id");
$district = select_items("district", "name", "id");

$out .= " <center> \n";
$out .= "<div style=\"width: 500px\">\n";

$out .= "<h3>"._SCHOOLS."</h3>";
$out .= "<table border=\"0\" class=\"result\" style=\"font-size: 12px;\"> \n";
$out .= " <tr>";
$out .= "     <td align=\"center\" width=\"100\" height=\"20\" class=\"reg_table_title\"> <b>"._DISTRICT."</b></td> \n";
$out .= "     <td align=\"center\" width=\"300\" height=\"20\" class=\"reg_table_title\"> <b>"._SCHOOL_NAME."</b></td> \n";
$out .= "   <td align=\"center\" width=\"80\" height=\"20\" class=\"reg_table_title\"><b>"._DELETE."</b> </td> \n";
$out .= " </tr>";
$result_schools = $query->select_sql("users_schools", "*", "user_id = ".get_int('user_id'));
while($row_schools = mysql_fetch_object($result_schools)){
	$school_info = $query->select_ar_sql("school_stats_info", "id, district_name, school_name", "id = ".(int)$row_schools->school_id);
	$out .= " <tr>";
    $out .= "   <td class=\"reg_table_td\">&nbsp;".$school_info['district_name']." </td>";
    $out .= "   <td class=\"reg_table_td\">&nbsp;".$school_info['school_name']." </td>";
    $out .= "   <td class=\"reg_table_td\" align=\"center\" width=\"80\">
    			<a href=\"javascript: yes_no('post.php?module=".$module."&action=user_school_delete&school_id=".$row_schools->school_id."&user_id=".get_int('user_id')."')\"><img src=\"images/drop.png\" border=0></a> </td> \n";
    $out .= " </tr>";
}
$out .= " </table> <br /> \n";

$out .= " <form action=\"post.php?module=".$module."&user_id=".get_int('user_id')."\" method=\"post\"> \n";
$out .= "	<div style=\"float: left\">"._REGION." 
				".input_form("region", "select", $edit_value, $region, "width: 130px", "", "onchange=\"showform(this.name, this.value, 'clear_post.php?module=schools_stats&page=district', 'district_div')\"")."
			</div>\n";
$out .= "	<div style=\"float: left; padding-left: 10px\">"._DISTRICT." 
				<span id=\"district_div\">
				".input_form("district", "select", $edit_value, $district, "width: 130px", "", "onchange=\"showform(this.name, this.value, 'clear_post.php?module=schools_stats&page=district&type=school', 'school_div')\"")."
				</span>
			</div>\n";
$out .= "	<div style=\"clear: both\">"._SCHOOL." 
				<span id=\"school_div\">
				".input_form("school", "select", $edit_value, "", "width: 430px", "", "onchange=\"showform(this.name, this.value, 'clear_post.php?module=schools_stats&page=district&type=school', 'school_div')\"")."
				</span>
			</div>\n";
$out .= "</div>\n";

$out .= " <input type=\"submit\" value=\""._ADD."\" name=\"add_school\"> \n";
$out .= " </form> \n";

?>
