<?php
$user_class->permission_end($module, 'admin');
global $html_out;
$result = $query->select_sql("text", "*", "lang='".$lang."'", "name ASC");
$selected[$lang] = 'selected';
if(get_int('id') !== 0){
	$result_edit =  $query->select_sql("text", "*", "id = '".get_int('id')."'");
	$row_edit = $query->obj($result_edit);
	$selected[$row_edit->lang] = 'selected';
}

$out .= " <form method=\"post\" action=\"post.php?module=".$module."&id=".$row_edit->id."\"> ";
$out .= " <center><br /><br /> ";
$out .= " <table border=\"0\" width=\"800\" class=\"text_items\"> \n";
$out .= "         <tr> \n";
$out .= "                 <td width=\"250\" align=\"center\" class=\"table_head\">"._NAME."</td> \n";
$out .= "                 <td width=\"180\" align=\"center\" class=\"table_head\">"._DESCRIPTION."</td> \n";
$out .= "                 <td width=\"100\" align=\"center\" class=\"table_head\">".LINK."</td> \n";
$out .= "                 <td width=\"60\" align=\"center\" class=\"table_head\">"._LANG."</td> \n";
$out .= "                 <td width=\"60\" align=\"center\" class=\"table_head\">"._EDIT."</td> \n";
$out .= "                 <td width=\"60\" align=\"center\" class=\"table_head\">"._DELETE."</td> \n";
$out .= "         </tr> \n";
while($row = $query->obj($result)){
	$td_style = $templates->change_style();
	$out .= " <tr> \n";
    $out .= "   <td width=\"250\" class=\"".$td_style."\">&nbsp; <a href=\"".DOC_ROOT."index.php?module=".$module."&page=main&link_id=".$row->link_id."&lang=".$row->lang."\">".$row->name."</a></td> \n";
    $out .= "   <td width=\"180\" class=\"".$td_style."\" align=\"center\">".$row->description."</td> \n";
    $out .= "   <td width=\"100\" class=\"".$td_style."\" align=\"center\">text-".$row->link_id.".html</td> \n";
    $out .= "   <td width=\"60\" class=\"".$td_style."\" align=\"center\">".$row->lang."</td> \n";
    $out .= "   <td width=\"60\" class=\"".$td_style."\" align=\"center\">
                       <a href=\"admin.php?module=".$module."&action_type=edit&id=".$row->id."\">
                        <img src=\"images/edit.png\" border=0></a></td> \n";
    $out .= "    <td width=\"60\" class=\"".$td_style."\" align=\"center\">
                       <a href=\"javascript: yes_no('post.php?module=".$module."&action=delete&id=".$row->id."')\">
                         <img src=\"images/drop.png\" border=0></a></td> \n";
    $out .= " </tr> \n";
}
$out .= " </table> <br /><br /> \n";

if(get('action_type') !== false){
        $action_type = get('action_type');
}
else{
        $action_type = "add";
}
$out .= "<div style=\"text-align: left; width: 400px\">\n";
$out .= " <input name=\"text_name\" type=\"text\" value=\"".$row_edit->name."\" style=\"width: 300px\"> "._NAME."<BR>";
$out .= " <input name=\"description\" type=\"text\" value=\"".$row_edit->description."\" style=\"width: 300px\"> "._DESCRIPTION."<BR>";

$out .= "<select size=\"1\" name=\"text_lang\"> ";
$out .= "   <option value=\"geo\" ".$selected['geo'].">"._GEORGIAN."</option> ";
$out .= "   <option value=\"eng\" ".$selected['eng'].">"._ENGLISH."</option> ";
$out .= " </select> "._LANG."<BR>";
$out .= " <input type=\"submit\" value=\"Send\" name=\"".$action_type."\"> ";
$out .= "</div>\n";

$html_out['module'] = $out;
?>