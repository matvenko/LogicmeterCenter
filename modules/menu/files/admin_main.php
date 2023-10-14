<?php
$user_class->permission_end($module, 'admin');
global $html_out;
$result = $query->select_sql("menu");

$out .= " <a href=\"admin.php?module=".$module."\"><b>"._MENU_ADMIN."</b></a> <p> \n";
$out .= " <a href=\"admin.php?module=".$module."&page=add_menu\">"._ADD_MENU."</a> ";
$out .= " <table border=\"0\" width=\"600\" class=\"menu\"> ";
$out .= "         <tr> ";
$out .= "                 <td class=\"table_head\" align=\"center\" width=\"250\">"._NAME."</td> ";
$out .= "                 <td class=\"table_head\" align=\"center\" width=\"150\">"._POSITION."</td> ";
$out .= "                 <td class=\"table_head\" align=\"center\" width=\"100\">"._LANG."</td> ";
$out .= "                 <td class=\"table_head\" align=\"center\" width=\"50\">"._EDIT."</td> ";
$out .= "                 <td class=\"table_head\" align=\"center\" width=\"50\">"._DELETE."</td> ";
$out .= "         </tr> ";
while($row = $query->obj($result)){
        $position[1] = _HORISONTAL;
        $position[2] = _VERTICAL;
        $out .= "         <tr> ";
        $out .= "                 <td class=\"table_td2\" width=\"250\"><a href=\"admin.php?module=menu&page=menu_items&menu_id=".$row->id."\">&nbsp;".$row->name."</a></td> ";
        $out .= "                 <td class=\"table_td2\" width=\"150\" align=\"center\">".$position["".$row->position.""]."</td> ";
        $out .= "                 <td class=\"table_td2\" width=\"100\" align=\"center\">".$row->lang."</td> ";
        $out .= "                 <td class=\"table_td2\" width=\"50\" align=\"center\">
                                   <a href=\"admin.php?module=".$_GET['module']."&page=add_menu&action=edit&id=".$row->id."\"><img src=\"images/edit.png\" border=0></a></td> ";
        $out .= "                 <td class=\"table_td2\" width=\"50\" align=\"center\">
                                   <a href=\"javascript: yes_no('post.php?module=".$_GET['module']."&action=menu_delete&id=".$row->id."')\"><img src=\"images/drop.png\" border=0</a></td> ";
        $out .= "         </tr> ";
}
$out .= " </table> ";

$html_out['module'] = $out;
unset($out);
?>