<?php
global $out, $topic_conf, $topic_permission, $topic_ids;
$out = '';

$ar_month[1] = _JANUARY;
$ar_month[2] = _FABRUARY;
$ar_month[3] = _MARCH;
$ar_month[4] = _APRIL;
$ar_month[5] = _MAY;
$ar_month[6] = _JUNE;
$ar_month[7] = _JULY;
$ar_month[8] = _AUGUST;
$ar_month[9] = _SEPTEMBER;
$ar_month[10] = _OCTOMBER;
$ar_month[11] = _NOVEMBER;
$ar_month[12] = _DECEMBER;

if(get_int('pg') !== 0){
	$page_start = get_int('pg');
}
else $page_start = 1;

$topic_amount = $topic_conf['topics_on_page'];

$tmp_id = tmpl_id(get_int('topic_id'));

$row_head = $query->select_obj_sql("topic_pages", "name, id", "id = '".get_int('topic_id')."'");

$row_tmpl = $query->select_obj_sql("topic_template", "*", "topic_id = ".$tmp_id." AND topic_comment = 4", "priority ASC", "0,1");
$row_tmpl_head = $query->select_obj_sql("topic_template", "*", "topic_id = ".$tmp_id." AND topic_comment = 5", "priority ASC", "0,1");
$row_tmpl_footer = $query->select_obj_sql("topic_template", "*", "topic_id = ".$tmp_id." AND topic_comment = 7", "priority ASC", "0,1");
$row_tmpl_comment = $query->select_obj_sql("topic_template", "*", "topic_id = ".$tmp_id." AND topic_comment = 2", "priority ASC", "0,1");

$template_comment = htmlspecialchars_decode($row_tmpl_comment->design);
$template['design'] = htmlspecialchars_decode($row_tmpl->design);

$out .= topic_head(htmlspecialchars_decode($row_tmpl_head->design));


//$out .= "<div class=\"topic_main\">\n";
if($topic_permission['edit_topic']){
	$out .= "<div><a href=\"".MANAGE_DIR."admin.php?module=".$module."&page=&page=addedit_topic&topic_id=".get_int('topic_id')."\">
				<font class=\"edit\">"._ADD_TOPIC."</font></a></div>";
}

$row = $query->select_obj_sql("topic_text", "*", "id = ".get_int('id')."");
//$row_user = $query->select_obj_sql("users", "*", "id = ".(int)$row->user_id."");

$accept_img[0] = "accept.gif";
$accept_img[1] = "decline.gif";
$choice_img[0] = "good_red.png";
$choice_img[1] = "good_green.png";
if($topic_permission['edit_topic']){
	if($topic_permission['accept_topic'] && (int)$topic_conf['topic_need_accept'] == 1){
		$edit_button = "<span id=\"accept_topic_".$row->id."\">\n";
		$edit_button .= ajax_get("accept_topic_".$row->id, "<img src=\"images/".$accept_img[$row->active]."\" border=\"0\">\n", $module, "", "action=accept_topic&topic_text_id=".$row->id);
		$edit_button .= "</span>\n";
		$edit_button .= space(15,5);
	}
	if((int)$topic_conf['choose_main_page'] == 1){
		$edit_button .= "<span id=\"choice_".$row->id."\">\n";
		$edit_button .= ajax_get("choice_".$row->id, "<img src=\"images/".$choice_img[$row->rcheuli]."\" border=\"0\">\n", $module, "", "action=choice&topic_text_id=".$row->id);
		$edit_button .= "</span>\n";
		$edit_button .= space(15,5);
	}
	$edit_button .= edit_button("page=addedit_topic&topic_id=".get_int('topic_id')."&id=".$row->id);
	$edit_button .= space(15,5);
	$edit_button .= delete_button("delete_topic_text", "topic_id=".get_int('topic_id')."&id=".(int)$row->id."")."<BR>";
	$edit_button = "<div id=\"topic_admin_buttons_".$row->id."\" class=\"topic_admin_buttons\">".$edit_button."</div>";
	$topic_out["{{admin}}"] = $edit_button;
	$show_admin = "onmouseover=\"show_admin('show', 'topic_admin_buttons_".$row->id."')\" onmouseout=\"show_admin('hide', 'topic_admin_buttons_".$row->id."')\"";
	$topic_out["{{show_admin}}"] = $show_admin;
}
if($topic_permission['add_comment']){
	$topic_out["{{add_comment}}"] = popup_wndow("<span class=\"add_comment\">"._ADD_COMMENT."</span>", $module, "add_comment", "topic_id=".get_int('topic_id')."&id=".$row->id, "clear_post");
}
$topic_out["{{topic_title}}"] = $row->title;
$topic_out["{{topic_date}}"] = date("d/m/Y", $row->date);
$v_date = date("d {{v}} Y", date_to_unix($row->date));
$topic_out["{{topic_v_date}}"] = str_replace("{{v}}", $ar_month[(int)date("m", date_to_unix($row->date))], $v_date);
//$topic_out["{{topic_short_date}}"] = date("m-d", $row->date);
//$topic_out["{{topic_time}}"] = date("H:i", $row->date);
$topic_out["{{topic_text}}"] = htmlspecialchars_decode($row->text);
$topic_out["{{topic_pic}}"] = pic_resize("upload/".$module."/gallery/thumb/","".rawurldecode($row->pic)."", $topic_conf['big_img_width'], $topic_conf['big_img_height']);
$topic_out["{{topic_short_text}}"] = $row->short_text;
$topic_out["{{user_name_surname}}"] = "<a href=\"\">".$row_user->name." ".$row_user->surname."</a>";
$topic_out["{{topic_comments}}"] = $row->comment_amount." "._COMMENTS;
$topic_out["{{comments_amount}}"] = $comments_amount;
$topic_out["{{topic_clear_short_text}}"] = $row->short_text;

$shareurl = rawurlencode($global_conf['location']."/topicdetals-".get_int('topic_id').".".$row->id.".html");
$topic_out["{{share_url}}"] = $shareurl;
$topic_out["{{share_title}}"] = rawurlencode($row->title);

if((int)$row->cat_id !== 0){
	$row_cat = $query->select_obj_sql("topic_categories", "*", "id = ".$row->cat_id."");
	$topic_out["{{topic_category}}"] =
							"<a href=\"index.php?module=".$module."&cat_id=".$row_cat->id."\">".$row_cat->name."</a>";
}
if((int)$row->user_id !== 0){
	$topic_out["{{user_image}}"] =
							"<a href=\"index.php?module=users&page=user_info&user_id=".$row_user->id."\">
								".pic_resize("upload/users/thumb/", $row_user->pic, 40, 40, "bottom")."</a>";
}

//*************** main picture ******************
$main_picture = $query->select_obj_sql("topic_gallery", "*", "topic_text_id = ".get_int('id')." AND `default` = 1");
if(is_file("upload/".$module."/gallery/".$main_picture->pic)){
	$topic_out["{{topic_gallery_main_image}}"] = pic_resize("upload/".$module."/gallery/", $main_picture->pic, $topic_conf['big_img_width'], $topic_conf['big_img_height']);
	$topic_out["{{topic_small_image}}"] = pic_resize("upload/".$module."/gallery/thumb/", $main_picture->pic, $topic_conf['small_img_width'], $topic_conf['small_img_height']);
	$topic_out["{{image_id}}"] = $main_picture->id;

	$topic_out["{{image}}"] = popup_wndow("<img src=\"upload/".$module."/gallery/".$main_picture->pic."\" width=\"".$topic_conf['small_img_width']."\" height=\"".$topic_conf['small_img_height']."\" border=\"0\">",
											$module, "pic_view", "topic_id=".get_int('topic_id')."&topic_text_id=".get_int('id')."&img_id=".$main_picture->id, "clear_post");
}
else{
	$topic_out["{{image_id}}"] = "";
	$topic_out["{{topic_gallery_main_image}}"] = "";//"images/topic_default_picture.jpg";
	$topic_out["{{image}}"] = "<img src=\"images/topic_default_picture.jpg\" width=\"".$topic_conf['small_img_width']."\" height=\"".$topic_conf['small_img_height']."\" border=\"0\">";
}
//***********************************************

//***************** comments ************************************
if((int)$topic_conf['comment_per_page'] !== 0){
	include("modules/".$module."/files/comments.php");
}
//***************************************************************

//********************** attachment ****************
$result = $query->select_sql("topic_files", "*", "topic_text_id = '".get_int('id')."'", "id ASC");
$n=0;
while($row = $query->obj($result)){
    $attachment .= " <div style=\"clear: both; padding: 4px;\"> \n";
    $attachment .= "<a href=\"upload/".$module."/".$row->file."\">\n";
    //$attachment .= "<img src=\"images/attachment.gif\" border=\"0\" align=\"middle\">\n";
    $attachment .= "<img src=\"images/attachment.gif\" width=\"15\" height=\"15\" border=0> ".$row->title."</a>\n";
    $attachment .= " </div>\n";
}
$topic_out["{{attachment}}"] = $attachment;
//****************************************************


//************************ gallery *******************
$result = $query->select_sql("topic_gallery", "*", "topic_text_id = '".get_int('id')."' AND `default` != 1", "id ASC");
$n=0;
$gallery = "<div style=\"width: 100%; overflow; hidden; padding-bottom: 15px;\">\n";
while($row = $query->obj($result)){
    $n++;
    @$size = getimagesize("upload/".$module."/gallery/thumb/".rawurldecode($row->pic));
	if($size[0] >= $size[1]){
		$width = 800;
		@$s = (int)$size[1]/(int)$size[0];
		@$height = $width * $s;
	}
	else{
		$height = 600;
		@$s = $height/$size[1];
		@$width = (int)($size[0]*$s);
	}
    $gallery .= " <div style=\"float: left; padding: 10px;\"> \n";
    $gallery .= "<a class=\"popup\" href=\"clear_post.php?module=".$module."&page=pic_view&topic_id=".get_int('topic_id')."&topic_text_id=".get_int('id')."&img_id=".$row->id."\">
    				<img src=\"upload/".$module."/gallery/thumb/".($row->pic)."\" ".image_size("upload/".$module."/gallery/thumb/".rawurldecode($row->pic), 100, 75)." border=0></a>";
    $gallery .= " </div>\n";
    if($n == 5){
    	$n=0;
      	$gallery .= " <div style=\"clear: both;\"></div> \n";
	}
}
$gallery .= "</div>\n";
$topic_out["{{gallery}}"] = $gallery;
//*****************************************************
$out .= str_replace(array_keys($topic_out), array_values($topic_out), $template['design']);

$out .= htmlspecialchars_decode($row_tmpl_footer->design);

if(strpos($out, "categories") !== false){
	$out_temp = $out;
	include("modules/".$module."/files/view_categories.php");
	$out_temp = str_replace("{{categories}}", $out, $out_temp);
	$out = $out_temp;
}

//********** global replace ******************
$global_values["{{topic_head}}"] = $row_head->name;
$global_values["{{topic_id}}"] = get_int('topic_id');
$global_values["{{id}}"] = get_int('id');
$global_values["{{global_cat_id}}"] = get_int('cat_id');
$global_values["{{global_main_cat_id}}"] = get_int('main_cat_id');
$global_values["{{lang}}"] = $lang;
$global_values["{{module}}"] = $module;

$global_values["[text_".$lang."]"] = "&nbsp;}}\n";
$global_values["[/text_".$lang."]"] = "\n{{&nbsp;";

$out = str_replace(array_keys($global_values), array_values($global_values), $out);

$out = preg_replace("/{{([[:print:]]+)}}/", "", $out);
$out .= "<script type=\"text/javascript\">\n";
$out .= " $(\".topic_body\").mouseover(function(){
			$(\"#topic_admin_buttons_\"+$(this).attr('data-id')).css('display', 'block');
		})\n";
$out .= " $(\".topic_body\").mouseout(function(){
			$(\"#topic_admin_buttons_\"+$(this).attr('data-id')).css('display', 'none');
		})\n";
$out .= "</script>\n";
//********************************************

//$out .= "</div>\n";

?>