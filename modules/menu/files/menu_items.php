<?php
$user_class->permission_end($module, 'admin');
global $html_out;
$result = $query->select_sql("menu_items", "*", "menu_id = '".get('menu_id')."' AND parent_id = '0'", "priority ASC");
$row_menu = $query->select_obj_sql("menu", "name", "id = '".get('menu_id')."'");
$row_edit = $query->select_obj_sql("menu_items", "*", "id = '".get('item_id')."'");

$result_textmod = $query->select_sql("text", "*", "lang = '".$lang."'", "name ASC");
$result_topicmod = $query->select_sql("topic_pages", "*", "lang = '".$lang."'", "name ASC");
$result_module = $query->select_sql("modules", "*", "", "module");

$out .= " <form action=\"post.php?module=menu&menu_id=".get('menu_id')."&item_id=".get('item_id')."\" method=\"post\" enctype='multipart/form-data'> \n";
$out .= " <center> \n";
$out .= " <a href=\"admin.php?module=".$module."\"><b>"._MENU_ADMIN."</b></a> <p></p> \n";
$out .= " <a href=\"admin.php?module=".$module."&action=menu_items&menu_id=".get('menu_id')."\">".$row_menu->name."</a> <br /> \n";

$out .= " <table border=\"0\" width=\"650\" class=\"menu_items\"> \n";
$out .= "         <tr> \n";
$out .= "                 <td class=\"admin_table_head\" align=\"center\" width=\"300\"><b>"._NAME."</b></td> \n";
$out .= "                 <td class=\"admin_table_head\" align=\"center\" width=\"150\"><b>"._URL."</b></td> \n";
$out .= "                 <td class=\"admin_table_head\" align=\"center\" width=\"100\"><b>"._EDIT."</b></td> \n";
$out .= "                 <td class=\"admin_table_head\" align=\"center\" width=\"100\"><b>"._DELETE."</b></td> \n";
$out .= "         </tr> \n";

function into_items($parent_id, $type = 'item', $space = 8){
	global $query, $module, $into_out, $templates;
	$result = $query->select_sql("menu_items", "*", "parent_id = '".$parent_id."'", "priority ASC");

	while($row = $query->obj($result)){
		if($type == 'item'){
			$td_style = $templates->change_style();
			$into_out .= "<tr> \n";
			$into_out .= "  <td class=\"".$td_style."\" width=\"300\"><img src=\"images/space.gif\" width=\"".$space."\" height=\"2\" border=0>--".$row->name."</td> \n";
			$into_out .= "  <td class=\"".$td_style."\" width=\"150\">&nbsp;".$row->link."</td> \n";
			$into_out .= "  <td class=\"".$td_style."\" align=\"center\" width=\"100\">
								<a href=\"admin.php?module=".$module."&page=menu_items&menu_id=".get('menu_id')."&item_type=edit&item_id=".$row->id."#edit\"><img src=\"images/edit.png\" border=0></a></td> \n";
			$into_out .= "  <td class=\"".$td_style."\" align=\"center\" width=\"100\">
							<a href=\"javascript: yes_no('post.php?module=".$module."&action=item_delete&id=".$row->id."&priority=".$row->priority."&parent_id=".$row->parent_id."&menu_id=".get('menu_id')."', '"._YES_NO_DELETE.":&nbsp;&quot;".$row->name."&quot;')\">
							<img src=\"images/drop.png\" border=0></a></td> \n";
			$into_out .= "</tr> \n";
	  	}
	   	elseif($type == 'option'){
	   		for($i = 0; $i < ($space/4); $i++) $dot .= '.';
	     	$into_out .= " <option value=\"".$row->id."\">".$dot.$row->name."</option> \n";
	      	$dot = '';
	    }
	    if($row->parent == '1'){
	    	into_items($row->id, $type, $space+8);
	    }
	}
	return $into_out;
}

while($row = $query->obj($result)){
        if($row->parent == '1'){
                $parents_img = "<img src=\"images/plus.gif\" width=\"8\" height=\"10\" border=0>";
        }
        else $parents_img = '&nbsp;';
        $td_style = $templates->change_style();
        
        $out .= "         <tr> ";
        $out .= "                 <td class=\"".$td_style."\" width=\"300\">".$parents_img."".$row->name."</td> \n";
        $out .= "                 <td class=\"".$td_style."\" width=\"150\">&nbsp;".$row->link."</td> \n";
        $out .= "                 <td class=\"".$td_style."\" align=\"center\" width=\"100\">
                                <a href=\"admin.php?module=".$module."&page=menu_items&menu_id=".get('menu_id')."&item_type=edit&item_id=".$row->id."#edit\"><img src=\"images/edit.png\" border=0></a></td> \n";
        $out .= "                 <td class=\"".$td_style."\" align=\"center\" width=\"100\">
                                <a href=\"javascript: yes_no('post.php?module=".$module."&action=item_delete&id=".$row->id."&priority=".$row->priority."&parent_id=".$row->parent_id."&menu_id=".get('menu_id')."', '"._YES_NO_DELETE.":&nbsp;&quot;".$row->name."&quot;')\">
                                <img src=\"images/drop.png\" border=0></a></td> \n";
        $out .= "         </tr> \n";
        if($row->parent == '1'){
        	unset($GLOBALS['into_out']);
        	$out .= into_items($row->id);
        }
}

$out .= " </table> <br><br>";

if(get('item_type') == ''){
        $item_type = 'add';
}
else{
        $item_type = get('item_type');
}
$blank_opt[$row_edit->blank] = 'selected';
$locat[$item_type] = 'checked';

$out .= " <a name=edit></a><table border=\"0\" width=\"600\" class=\"add_edit_menu\" cellpadding=\"3\"> \n";
$out .= "         <tr> \n";
$out .= "                 <td width=\"300\" class=\"admin_table_td2\"> \n";
$out .= "                      &nbsp;<input type=\"text\" name=\"item_name\" size=\"50\" value=\"".$row_edit->name."\"></td> \n";
$out .= "                 <td width=\"300\" class=\"admin_table_td2\"> \n";
$out .= "                      &nbsp;<select size=\"1\" name=\"blank\"> \n";
$out .= "                                 <option value=\"0\" ".$blank_opt[0].">"._SAME_WINDOW."</option> \n";
$out .= "                                 <option value=\"1\" ".$blank_opt[1].">"._NEW_WINDOW."</option> \n";
$out .= "                                 </select></td> \n";
$out .= "         </tr> \n";
$out .= "         <tr> \n";
$out .= "                 <td colspan=2 class=\"admin_table_td2\"> \n";
if(get('item_type') == 'edit' && $row_edit->image !== ""){
	$out .= "<img src=\"".DOC_ROOT."upload/".$module."/".$row_edit->image."\">\n";
	$out .= "<input type=\"checkbox\" name=\"del_image\" value=\"1\">Delete\n";
}
$out .= "                      &nbsp;<input type=\"file\" name=\"item_image\"></td> \n";
$out .= "         </tr> \n";
$out .= "         <tr> \n";
$out .= "                 <td width=\"300\" class=\"admin_table_td2\"> \n";
if(get('item_type') == 'edit'){
        $out .= "              &nbsp;<input type=\"radio\" value=\"same\" name=\"item_loc\" ".$locat['edit']."> "._SAME."&nbsp;&nbsp; \n";
}
$out .= "                      &nbsp;<input type=\"radio\" value=\"after\" name=\"item_loc\" ".$locat['add']."> "._AFTER."&nbsp;&nbsp; \n";
$out .= "                      <input type=\"radio\" value=\"into\" name=\"item_loc\">"._INTO."&nbsp; \n";
$out .= "                      <input type=\"radio\" value=\"before\" name=\"item_loc\">"._BEFORE."</td> \n";
$out .= "                 <td width=\"300\" class=\"admin_table_td2\"> \n";
$out .= "                      &nbsp;<select size=\"1\" name=\"items\"> \n";
$result = $query->select_sql("menu_items", "*", "menu_id = '".get('menu_id')."' AND parent_id = '0'", "priority ASC");
while($row = $query->obj($result)){
	$out .= "<option value=\"".$row->id."\">".$row->name."</option> ";
	if($row->parent == '1'){
		unset($GLOBALS['into_out']);
		$out .= into_items($row->id, $type = 'option');
	}
}
$out .= "                 </select></td> \n";
$out .= "         </tr> \n";
$out .= "         <tr> \n";
$out .= "                 <td width=\"300\" class=\"admin_table_td2\">&nbsp;<input type=\"radio\" value=\"text\" name=\"module\" ".$locat['add'].">"._TEXT_MODULE."</td> \n";
$out .= "                 <td width=\"300\" class=\"admin_table_td2\">&nbsp;<select size=\"1\" name=\"text_modules\" style=\"width: 290px\"> \n";
while($row = $query->obj($result_textmod)){
        $out .= "                         <option value=\"".$row->link_id."\">\n";
        $out .= $row->name;
        if($row->description !== ""){        	$out .= " - (".$row->description.")";        }
        $out .= "</option> \n";
}
$out .= "                 </select></td> \n";
$out .= "         </tr> \n";
$out .= "         <tr> \n";
$out .= "                 <td width=\"300\" class=\"admin_table_td2\">&nbsp;<input type=\"radio\" value=\"topic\" name=\"module\" >"._TOPIC_MODULE."</td> \n";
$out .= "                 <td width=\"300\" class=\"admin_table_td2\">&nbsp;<select size=\"1\" name=\"topic_modules\"> \n";
while($row = $query->obj($result_topicmod)){
        $out .= "                         <option value=\"".$row->id."\">".$row->name." (".$row->description.")</option> \n";
}
$out .= "                 </select></td> \n";
$out .= "         </tr> \n";
$out .= "         <tr> \n";
$out .= "                 <td width=\"300\" class=\"admin_table_td2\">&nbsp;<input type=\"radio\" value=\"module\" name=\"module\">"._MODULE."</td> \n";
$out .= "                 <td width=\"300\" class=\"admin_table_td2\">&nbsp;<select size=\"1\" name=\"modules\"> \n";
while($row = $query->obj($result_module)){
        $out .= "                         <option value=\"".$row->module."\">".$row->module."</option> \n";
}
$out .= "                 </select></td> \n";
$out .= "         </tr> \n";
$out .= "         <tr> \n";
$out .= "                 <td width=\"300\" class=\"admin_table_td2\">&nbsp;<input type=\"radio\" value=\"url\" name=\"module\" ".$locat['edit'].">"._URL."</td> \n";
$out .= "                 <td width=\"300\" class=\"admin_table_td2\">&nbsp;<input type=\"text\" name=\"item_url\" size=\"35\" value=\"".$row_edit->link."\"></td> \n";
$out .= "         </tr> \n";
$out .= " </table> <br /> \n";

$out .= " <input type=\"submit\" value=\"Send\" name=\"".$item_type."_item\"> \n";

$html_out['module'] = $out;
unset($out);
?>