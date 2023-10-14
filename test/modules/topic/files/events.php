<?php
global $out, $topic_conf, $location, $topic_permission, $topic_ids;
$out = '';

$topic_amount = $topic_conf['topics_on_page'];

$topic_id = get_int('topic_id');
if(get_int('limit') < $topic_amount && get_int('limit') !== 0){
	$topic_amount = get_int('limit');
}

$replace_fields['_EVENTS'] = _EVENTS;
$replace_fields['topic_id'] = $topic_id;

$topic_tmpl = $templates->split_template("topics", "events");

$result = $query->select_sql("topic_text", "*", "topic_id = ".$topic_id, "date DESC", "0, 4");
$n = 0;
while($row = $query->obj($result)){
	$n++;
	$topic_fields['topic_id'] = get_int('topic_id');
	$topic_fields['id'] = $row->id;
	$topic_fields['topic_title'] = $row->title;
	$row_cat = $query->select_obj_sql("topic_categories", "*", "id = ".$row->cat_id."");
	$topic_fields['category'] = $row_cat->name;
	
	//*************** main picture ******************
	$main_picture = $query->select_obj_sql("topic_gallery", "*", "topic_text_id = ".$row->id." AND `default` = 1");
	if(is_file("upload/".$module."/gallery/".$main_picture->pic)){
		$topic_fields["topic_gallery_main_image"] = "upload/".$module."/gallery/".$main_picture->pic;
		
		$topic_fields["image_id"] = $main_picture->id;
	
		$topic_fields["image"] = "<img src=\"upload/".$module."/gallery/".$main_picture->pic."\" width=\"".$topic_conf['small_img_width']."\" height=\"".$topic_conf['small_img_height']."\" border=\"0\">";
	}
	else{
		$topic_fields["topic_gallery_main_image"] = "images/topic_default_picture.jpg";
		$topic_fields["image"] = "<img src=\"images/topic_default_picture.jpg\" width=\"".$topic_conf['small_img_width']."\" height=\"".$topic_conf['small_img_height']."\" border=\"0\">";
	}
	//***********************************************
	
	$topic_fields['topic_slider_date'] = date("d.m.Y", date_to_unix($row->date));
	$topic_fields["topic_short_text"] = strip_tags($row->short_text, "<br>, <b>, <a>");
	
	$templates->gen_loop_html($topic_fields, $topic_tmpl);
}

$replace_fields['margin_left'] = 130 - $n * 15;

$out .= $templates->gen_module_html($replace_fields, "events");
$templates->module_content = "";
