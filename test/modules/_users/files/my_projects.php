<?php
global $out;
$user_class->user_permission_end();
include("language/geo/form_builder.php");
include("modules/".$module."/files/profile_head.php");

$out .= "<table border=\"0\" width=\"700\">\n";
$out .= "	<tr>\n";
$out .= "		<td width=\"100\" align=\"center\" class=\"admin_table_head\">"._PROJECT_NUMBER."</td>\n";
$out .= "		<td width=\"300\" align=\"center\" class=\"admin_table_head\">"._PROJECT_NAME."</td>\n";
$out .= "		<td width=\"200\" align=\"center\" class=\"admin_table_head\">"._PROJECT_TYPE."</td>\n";
$out .= "		<td width=\"100\" align=\"center\" class=\"admin_table_head\" colspan=2>&nbsp;</td>\n";
$out .= "	</tr>\n";
$result_projects = $query->select_sql("projects_projects", "*", "user_id = ".(int)$_SESSION['login']['user_id']."");
while($row_projects = mysql_fetch_object($result_projects)){
	$row_type = $query->select_obj_sql("projects_type", "*", "type = ".(int)$row_projects->type."");
	$out .= "	<tr>\n";
	$out .= "		<td align=\"center\" class=\"admin_table_td1\">".$row_projects->number."</td>\n";
	$out .= "		<td align=\"center\" class=\"admin_table_td1\">".$row_projects->name_geo."</td>\n";
	$out .= "		<td align=\"center\" class=\"admin_table_td1\">".$row_type->name."</td>\n";
	$out .= "		<td align=\"center\" class=\"admin_table_td1\">
						<a href=\"index.php?module=form_builder&page=proj_files&page_type=view_info&proj_id=".$row_projects->type."&edit_id=".$row_projects->id."\">"._VIEW."</a></td>\n";
	$out .= "		<td align=\"center\" class=\"admin_table_td1\">
						<a href=\"index.php?module=form_builder&proj_id=".$row_projects->type."&edit_id=".$row_projects->id."&step=1\">
							<img src=\"images/edit.png\" alt=\""._EDIT."\" border=\"0\">
						</a></td>\n";
	$out .= "	</tr>\n";
}
$out .= "</table>\n";



?>