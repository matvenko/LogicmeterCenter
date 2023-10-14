<?php
global $topic_conf;
$img_id = get_int('img_id');

if($img_id == 0){	$img_id = $query->min_value("topic_gallery", "id", "topic_text_id = ".get_int('topic_text_id'));}
$row = $query->select_obj_sql("topic_gallery", "*", "id = '".$img_id."'");

$row_next = $query->select_obj_sql("topic_gallery", "*", "topic_text_id = ".get_int('topic_text_id')." AND id > ".$img_id."", "id ASC", "0,1");
$row_prev = $query->select_obj_sql("topic_gallery", "*", "topic_text_id = ".get_int('topic_text_id')." AND id < ".$img_id."", "id DESC", "0,1");

$img = "upload/".$module."/gallery/".rawurldecode($row->pic);
if($query->amount_fields("topic_gallery", "id = ".$img_id."") == 0){
	$img = "images/topic_default_picture.jpg";
}

echo open_popup(820);
echo "<center>";
echo "<div class=\"popup\" href=\"clear_post.php?module=".$module."&page=pic_view&topic_id=".get_int('topic_id')."&topic_text_id=".get_int('topic_text_id')."&img_id=".$row_next->id.")\">
					<img src=\"".$img."\" ".image_size($img, $topic_conf['big_img_width'], $topic_conf['big_img_height'])." border=\"0\"></div>\n";

echo "<div style=\"height: 20px\">\n";
if((int)$row_prev->id !== 0)
	echo "		<a class=\"popup\" href=\"clear_post.php?module=".$module."&page=pic_view&topic_id=".get_int('topic_id')."&topic_text_id=".get_int('topic_text_id')."&img_id=".$row_prev->id."\"><b>&lt;&lt;&lt; </b></a>&nbsp;&nbsp; &nbsp;&nbsp;\n";
if((int)$row_next->id !== 0)
	echo "		<a class=\"popup\" href=\"clear_post.php?module=".$module."&page=pic_view&topic_id=".get_int('topic_id')."&topic_text_id=".get_int('topic_text_id')."&img_id=".$row_next->id."\"><b> &gt;&gt&gt;</b></a>\n";

echo "</div>\n";
echo "</center>";
echo close_popup();
//exit;
?>
<script type="text/javascript">
$(document).ready(function() {
	$('.popup').magnificPopup({
		type: 'ajax',
		alignTop: true
	});	
});
</script>