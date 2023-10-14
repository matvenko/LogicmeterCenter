<?php
$user_class->permission_end($module, 'admin');
global $html_out;
$row_item = $query->select_obj_sql("menu_items", "id, name", "id = '".$_GET['id']."'");
$row_menu = $query->select_obj_sql("menu", "name", "id = '".$_GET['menu_id']."'");
$result_group = $query->select_sql("group");
$result_access = $query->select_sql("menu_item_access", "*", "item_id = '".$_GET['id']."'");
while($row_access = $query->obj($result_access)){
        $chek_access[$row_access->group_id] = 'checked';
}

$out .= " <a href=\"admin.php?module=".$module."\"><b>"._MENU_ADMIN."</b></a> <p> \n";
$out .= " <form action=\"post.php?module=".$module."&menu_id=".$_GET['menu_id']."&id=".$_GET['id']."\" method=\"post\"> \n";
$out .= " <center> \n";
$out .= " <a href=\"admin.php?module=".$module."&page=menu_items&menu_id=".$_GET['menu_id']."\">".$row_menu->name."</a> <br /> \n";

$out .= " <table border=\"0\" width=\"400\" class=\"common_table\"> \n";
$out .= "         <tr> \n";
$out .= "                 <td align=\"center\" class=\"menu_title\">".$row_item->name."</td> \n";
$out .= "         </tr> \n";
while($row_group = $query->obj($result_group)){
	$out .= "         <tr> \n";
    $out .= "                 <td class=\"menu_td\">&nbsp;
                                  <input name=\"".$row_group->id."\" type=\"checkbox\" value=\"1\" ".$chek_access[$row_group->id].">
                                  &nbsp; ".$row_group->name."</td> \n";
    $out .= "         </tr> \n";
}
$out .= "         <tr> \n";
$out .= "                 <td align=\"center\"><input name=\"item_access\" type=\"submit\" value=\"Send\"></td> \n";
$out .= "         </tr> \n";
$out .= " </table> \n";
$out .= " </form> \n";

$html_out['module'] = $out;
unset($out);

?>