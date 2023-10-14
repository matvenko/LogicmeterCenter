<?php
$user_class->permission_end($module, 'admin');
global $html_out, $block_types, $setting_fields;

$result_per = $query->select_sql("permissions", "*", "module_id = ".get_int('module_id')."");
while($row_per = mysql_fetch_object($result_per)){
	$out .= "		<input name=\"".get_int('module_id')."_".$row_per->name."\" type=\"checkbox\" value=\"1\" ".$if_admin.permission(get_int('module_id'), get('group_id'), get('custom_id'), $row_per->name)."> \n";
	$out .= "<b>".$row_per->name."</b>";
	$out .= " (".$row_per->description.")<BR>\n";
}

echo $out;
?>