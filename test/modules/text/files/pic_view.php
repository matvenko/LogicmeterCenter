<?php
$img_id = get_int('img_id');

if($img_id == 0){	
	$img_id = $query->min_value("text_gallery", "id", "link_id = ".get_int('link_id'));
}
$row = $query->select_obj_sql("text_gallery", "*", "id = '".$img_id."'");

$row_next = $query->select_obj_sql("text_gallery", "*", "link_id = ".get_int('link_id')." AND id > ".$img_id."", "id ASC", "0,1");
$row_prev = $query->select_obj_sql("text_gallery", "*", "link_id = ".get_int('link_id')." AND id < ".$img_id."", "id DESC", "0,1");

$img = "upload/".$module."/gallery/".rawurldecode($row->pic);

echo "<script language=javascript>\n";
echo "function csrollposition() {\n";
echo "  return document.body.scrollTop;\n";
echo "}\n";
echo "</script>\n";

echo open_popup(820);

echo "<center>";
echo "<div style=\"cursor: pointer; padding-top: 15px\" onclick=\"showform('coursor', csrollposition(), 'clear_post.php?module=".$module."&page=pic_view&link_id=".get_int('link_id')."&img_id=".$row_next->id."', 'start_div')\">
					<img src=\"".$img."\" ".image_size($img, 800, 600)." border=\"0\"></div>\n";

echo "<div style=\"height: 20px\">\n";
if((int)$row_prev->id !== 0)
	echo "		<a style=\"font-size: 14px; color: #6C6C6C; cursor:pointer\" onclick=\"showform('coursor', csrollposition(), 'clear_post.php?module=".$module."&page=pic_view&link_id=".get_int('link_id')."&img_id=".$row_prev->id."', 'start_div')\"><b>&lt;&lt;&lt; </b></a>&nbsp;&nbsp; &nbsp;&nbsp;\n";
if((int)$row_next->id !== 0)
	echo "		<a style=\"font-size: 14px; color: #6C6C6C; cursor:pointer\" onclick=\"showform('coursor', csrollposition(), 'clear_post.php?module=".$module."&page=pic_view&link_id=".get_int('link_id')."&img_id=".$row_next->id."', 'start_div')\"><b> &gt;&gt&gt;</b></a>\n";

echo "</div>\n";
echo "</center>";
echo close_popup();
//exit;
?>
