<?php
global $out, $topic_conf, $topic_permission;
$out = '';

if(get_int('pg') !== 0){
	$page_start = get_int('pg');
}
else $page_start = 1;

$topic_amount = $topic_conf['topics_on_page'];

$topic_id = get_int('topic_id');

$row_head = $query->select_obj_sql("topic_pages", "name, id", "id = '".$topic_id."'");

$row_tmpl = $query->select_obj_sql("topic_template", "*", "topic_id = ".get_int('topic_id')." AND topic_comment = 8", "priority ASC", "0,1");
$row_tmpl_head = $query->select_obj_sql("topic_template", "*", "topic_id = ".get_int('topic_id')." AND topic_comment = 5", "priority ASC", "0,1");
$row_tmpl_footer = $query->select_obj_sql("topic_template", "*", "topic_id = ".get_int('topic_id')." AND topic_comment = 7", "priority ASC", "0,1");
$template['design'] = htmlspecialchars_decode($row_tmpl->design);

$out .= topic_head(htmlspecialchars_decode($row_tmpl_head->design));


$out .= "<div class=\"topic_main\">\n";
if($query->group_perm($module, 'edit_topic')){
	$out .= "<div><a href=\"admin.php?module=".$module."&page=&page=addedit_topic&topic_id=".get_int('topic_id')."\">
				<font class=\"edit\">"._ADD_TOPIC."</font></a></div>";
}

$row = $query->select_obj_sql("topic_text", "*", "id = ".get_int('id')."");
$row_user = $query->select_obj_sql("users", "*", "id = ".(int)$row->user_id."");

$topic_out = $template['design'];

if($topic_permission['edit']){
	$edit_button = edit_button("page=addedit_topic&topic_id=".$topic_id."&id=".$row->id);
	$edit_button .= space(15,5);
	$edit_button .= delete_button("delete_topic_text", "topic_id=".$topic_id."&id=".(int)$row->id."")."<BR>";
	$topic_out = str_replace("{{admin}}", $edit_button, $topic_out);
}
$topic_out = str_replace("{{topic_title}}", $row->title, $topic_out);
$topic_out = str_replace("{{topic_date}}", substr($row->date, 0, 10), $topic_out);
$topic_out = str_replace("{{topic_short_date}}", substr($row->date, 5, 5), $topic_out);
$topic_out = str_replace("{{topic_time}}", substr($row->date, 11, 5), $topic_out);
$topic_out = str_replace("{{topic_text}}", htmlspecialchars_decode($row->text), $topic_out);
$topic_out = str_replace("{{topic_pic}}", pic_resize("upload/".$module."/gallery/thumb/","".rawurldecode($row->pic)."", $topic_conf['big_img_width'], $topic_conf['big_img_height']), $topic_out);
$topic_out = str_replace("{{topic_short_text}}", $row->short_text, $topic_out);
$topic_out = str_replace("{{user_name_surname}}", "<a href=\"\">".$row_user->name." ".$row_user->surname."</a>", $topic_out);
$comments_amount = $query->amount_fields("topic_comments", "topic_text_id = ".get_int('id')." AND active =1");
$topic_out = str_replace("{{topic_comments}}", $comments_amount." "._COMMENTS, $topic_out);

$shareurl = rawurlencode($global_conf['location']."/index.php?module=".$module."&page=detals&topic_id=".get_int('topic_id')."&id=".$row->id);
$topic_out = str_replace("{{share_url}}", $shareurl, $topic_out);
$topic_out = str_replace("{{share_title}}", rawurlencode($row->title), $topic_out);

if((int)$row->cat_id !== 0){
	$row_cat = $query->select_obj_sql("topic_categories", "*", "id = ".$row->cat_id."");
	$topic_out = str_replace("{{topic_category}}",
							"<a href=\"index.php?module=".$module."&cat_id=".$row_cat->id."\">".$row_cat->name."</a>", $topic_out);
}
if((int)$row->user_id !== 0){
	$topic_out = str_replace("{{user_image}}",
							"<a href=\"index.php?module=users&page=profile&user_id=".$row_user->id."\">
								".pic_resize("upload/users/thumb/", $row_user->pic, 40, 40, "bottom")."</a>", $topic_out);
}

//***************** comments ************************************
if($topic_conf['comment_per_page'] !== 0){
	include("modules/".$module."/files/comments.php");
}
//***************************************************************
$out .= $topic_out;

$out .= htmlspecialchars_decode($row_tmpl_footer->design);

if(strpos($out, "categories") !== false){
	$out_temp = $out;
	include("modules/".$module."/files/view_categories.php");
	$out_temp = str_replace("{{categories}}", $out, $out_temp);
	$out = $out_temp;
}

//********** global replace ******************
$out = str_replace("{{topic_head}}", $row_head->name, $out);
$out = str_replace("{{topic_id}}", get_int('topic_id'), $out);
$out = str_replace("{{global_cat_id}}", get_int('cat_id'), $out);
$out = str_replace("{{global_main_cat_id}}", get_int('main_cat_id'), $out);
$out = str_replace("{{lang}}", $lang, $out);
$out = str_replace("{{module}}", $module, $out);

$out = str_replace("[text_".$lang."]", "&nbsp;}}\n", $out);
$out = str_replace("[/text_".$lang."]", "\n{{&nbsp;", $out);

$out = eregi_replace("{{([[:print:]]+)}}", "", $out);
//********************************************

$out .= "</div>\n";

?>