<?
global $html_out;
$user_class->permission_end($module, 'admin');
$link_id = get_int('link_id');

$row = $query->select_obj_sql("text", "*", "link_id = '".$link_id."' AND lang = '".$lang."'");

$out .= "<center>\n";

if(get('actiontype') == "title"){
        $out .= " <input name=\"title_id\" type=\"hidden\" value=\"".get_int('title_id')."\"> ";
        $result = $query->select_sql("text_files", "*", "id = '".get_int('title_id')."'");
        $row = $query->obj($result);
        $out .= " <h1>"._EDIT_TITLE."</h1> ";
        $out .= " <table border=\"0\" width=\"100%\"> ";
        $out .= "   <tr> ";
        $out .= "     <td align=\"center\"> ";
        $out .= "         <input name=\"title_edit\" type=\"text\" value=\"".htmlspecialchars_decode($row->title)."\"> <br><br>";
        $out .= "         <input type=\"submit\" value=\""._SAVE."\" name=\"edit_title\"> ";
        $out .= "     </td> ";
        $out .= "   </tr> ";
        $out .= " </table> ";
        $out .= " </form> \n";
        $html_out['module'] = $out;
}
else{
$out .= "<form action=\"post.php?module=".$module."&link_id=".$link_id."\" method=\"post\">\n";

$out .= " <table border=\"0\" width=\"650\"> \n";
$out .= "         <tr> ";
$out .= "                 <td colspan=\"2\" height=\"20\" valign=\"top\"> <font class=\"head_text\">".$row->name."</font></td> \n";
$out .= "         </tr> ";
$out .= "         <tr> \n";
$out .= "           <td class=\"text_top\" align=\"center\"> \n";
$html_out['editorarea'] = editorarea(1, 800, 400);
$out .= input_form("text", "textarea_editor", htmlspecialchars_decode($row->text), 1);
$out .= "              <br><br> \n";
$out .= "              <input type=\"submit\" value=\"Submit\" name=\"".get_int('link_id')."\"> \n";
$out .= "           </td> \n";
$out .= "         </tr> \n";
$out .= "     </table> \n";
$out .= " <br /><br /> \n";

$out .= " </form> \n";

//************ attachment ************************
$out .= " <form action=\"post.php?module=".$module."&link_id=".$link_id."\" method=\"post\" enctype='multipart/form-data'> \n";
$out .= " <table border=\"0\" width=\"400\" id=\"table2\" cellpadding=\"5\" cellspacing=\"0\"> \n";
$out .= "         <tr> \n";
$out .= "                 <td class=\"admin_table_head\" align=\"center\">"._ATACHED_FILES."</td> \n";
$out .= "         </tr> \n";

$result = $query->select_sql("text_files", "*", "link_id = '".get_int('link_id')."' AND lang = '".$lang."'");
while($row = $query->obj($result)){
	$td_style = $templates->change_style();
	$out .= "         <tr> \n";
	$out .= "                 <td class=\"".$td_style."\"> \n";
	$out .= "<a href=\"".DOC_ROOT."upload/text/".$row->file."\"><img src=\"".file_icon($row->file)."\" width=\"15\" height=\"15\" border=0> ".$row->title."</a>&nbsp;&nbsp;";
    $out .= "<a href=\"admin.php?module=".$module."&action=edit&page=edit&actiontype=title&link_id=".$link_id."&title_id=".$row->id."\"><font class=\"edit\">"._EDIT."</font></a> &nbsp; ";
    $out .= "<a href=\"javascript: yes_no('post.php?module=".$module."&action=file_delete&id=".$row->id."&sc_id=".$row->link_id."', '"._YES_NO_DELETE."')\"><font class=\"edit\">"._DELETE."</font></a><br><br>";
    $out .= "        </td>\n";
    $out .= "        </tr>\n";
}

$out .= "        <tr>\n";
$out .= "                <td class=\"text_top\">\n";
$out .= "                      "._TITLE.": <input type=\"text\" name=\"title\" size=\"35\"><br /><br />\n";
$out .= "                      "._FILE.": <input type=\"file\" name=\"text_file\" size=\"20\">\n";
$out .= "                      <input type=\"submit\" value=\"Submit\" name=\"add_file\">\n";
$out .= "              </td>\n";
$out .= "        </tr>\n";
$out .= "</table>\n";
$out .= " </form> \n";
//*********************************************

//************ gallery ************************
if(get_int('link_id') !== 0){
$out .= "<a name=\"gallery\"></a>\n";
$out .= form_start("link_id=".get_int('link_id'), "admin", 1, "post");

$out .= space(5, 20)."<br>";
$out .= "<div style=\"clear: both; width: 700px;\">";
$out .= "<div class=\"admin_head\" style=\"clear: both; width: 700px;\">"._GALLERY."</div>\n";
$result = $query->select_sql("text_gallery", "*", "link_id = '".get('link_id')."'", "id ASC");
$n=0;
$out .= "<div class=\"admin_td1\" style=\"clear: both; width: 700px;\">\n";
while($row = $query->obj($result)){
	$n++;
	$out .= " <div class=\"admin_td2\" style=\"padding: 5px;\"> \n";
	$out .= " 	<a href=\"".DOC_ROOT."upload/".$module."/gallery/".$row->pic."\"><img src=\"".DOC_ROOT."upload/".$module."/gallery/".$row->pic."\" ".image_size("upload/".$module."/gallery/thumb/".rawurldecode($row->pic), 50, 50)." border=0></a><br />";
	$out .= " 	<a href=\"javascript: yes_no('post.php?module=".$module."&action=pic_delete&id=".$row->id."&sc_id=".$row->link_id."', '"._YES_NO_DELETE."')\"><font class=\"edit\">"._DELETE."</font></a>";
	$out .= " </div>\n";
	if($n == 5){
		$n=0;
		$out .= " <div style=\"clear:both;\"></div> \n";
	}
}
$out .= " </div> \n";

$out .= "<div class=\"admin_td1\" style=\"clear: both; width: 700px;\">\n";
$out .= 	" "._PICTURE.": <input type=\"file\" name=\"gallery_pic\" size=\"20\">
		</div>\n";

$out .= "<div style=\"clear: left; \">
			<input type=\"submit\" value=\""._ADD_PIC."\" name=\"add_text_gallery\"></div>\n";

$out .= form_end();
$out .= " </div> \n";
}
//*********************************************

$html_out['module'] = $out;
}
?>

