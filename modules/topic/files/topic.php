<?php
global $out, $topic_conf, $topic_permission, $topic_ids;
$out = '';

if(get_int('pg') !== 0){
	$page_start = get_int('pg');
}
else $page_start = 1;

$topic_amount = $topic_conf['topics_on_page'];

$topic_id = set_var(get_int('topic_id'), 1);

$result_tmpl = $query->select_sql("topic_template", "*", "topic_id = ".$topic_id."", "priority ASC");

while($row_tmpl = $query->obj($result_tmpl)){
	if($row_tmpl->topic_comment == 1){
		$template[$row_tmpl->priority]['design'] = htmlspecialchars_decode($row_tmpl->design);
		$template[$row_tmpl->priority]['repeat'] = $row_tmpl->repeat;
		$template[$row_tmpl->priority]['comments_amount'] = $row_tmpl->comments_amount;
		if((int)$row_tmpl->repeat == 0){
			$template[0] = $template[$row_tmpl->priority];
		}
		$template_n[] = $row_tmpl->priority;
	}
	elseif($row_tmpl->topic_comment == 2){
		$template_comment = htmlspecialchars_decode($row_tmpl->design);
	}
	elseif($row_tmpl->topic_comment == 5){
		$template_head = topic_head(htmlspecialchars_decode($row_tmpl->design));
	}
	elseif($row_tmpl->topic_comment == 7){
		$template_footer = htmlspecialchars_decode($row_tmpl->design);
	}
}

$cat_id = get_int('cat_id');
//****************** search *******************************
$order_by = "";
$where = "topic_id = ".$topic_id;
if(!$topic_permission['edit_topic']){
	$where .= " AND active = 1";
}
if(get_int('cat_id') !== 0){
	$where .= " AND cat_id = ".get_int('cat_id')."";
}
if(get_int('user_id') !== 0){
	$where .= " AND user_id = ".get_int('user_id')."";
}
if(get('cat') == "last" && get_int('cat_id') == 0){
	$max_cat = $query->max_value("topic_text", "cat_id", $where);
	$where .= " AND cat_id = ".$max_cat."";
	$cat_id = $max_cat;
}
if(get('search_type') == "popular"){
	$order_by .= "comment_amount DESC, ";
}

//***********************************************
if(get('topic_order_by') == false){
	$order_by = "date DESC";
}
else{
	$order_ar = explode("|", get('topic_order_by'));
	$accept_ar = array("cat_id", "add_date", "asc", "desc");
	if(in_array($order_ar[0], $accept_ar) && in_array($order_ar[1], $accept_ar)){
		$order_by = $order_ar[0]." ".$order_ar[1];
	}
}
//*********************************************************
//************** add button *******************************
if($topic_permission['edit_topic']){
	$add_topic = "<div><a href=\"admin.php?module=".$module."&page=addedit_topic&cat_id=".$max_cat."&topic_id=".$topic_id."\">
				<font class=\"edit\">"._ADD_TOPIC."</font></a></div>";
}
//*********************************************************
$out .= "<div class=\"topic_main\">\n";

$result = $query->select_sql("topic_text", "*", $where, $order_by, "".(($page_start-1)*$topic_amount).", ".(int)$topic_amount."");

$n=($page_start-1)*$topic_amount; $m=0;

$accept_img[0] = "accept.gif";
$accept_img[1] = "decline.gif";
$choice_img[0] = "good_red.png";
$choice_img[1] = "good_green.png";
while($row = $query->obj($result)){
	if($m == 0){
		$n++;
		$m = $template[$n]['repeat'];
        if(!in_array($n, $template_n)){
        	$n = 0;
        	$m = $topic_amount;
        }
	}
	$topic_out = $template[$n]['design'];
	if($topic_permission['edit_topic']){
		$edit_button = "<span id=\"accept_topic_".$row->id."\">\n";
		$edit_button .= ajax_get("accept_topic_".$row->id, "<img src=\"images/".$accept_img[$row->active]."\" border=\"0\">\n", $module, "", "action=accept_topic&topic_text_id=".$row->id);
		$edit_button .= "</span>\n";
		$edit_button .= space(15,5);
		if($topic_ids['topic'] == get_int('topic_id')){
			$edit_button .= "<span id=\"choice_".$row->id."\">\n";
			$edit_button .= ajax_get("choice_".$row->id, "<img src=\"images/".$choice_img[$row->rcheuli]."\" border=\"0\">\n", $module, "", "action=choice&topic_text_id=".$row->id);
			$edit_button .= "</span>\n";
			$edit_button .= space(15,5);
		}
		$edit_button .= edit_button("page=addedit_topic&topic_id=".$topic_id."&id=".$row->id);
		$edit_button .= space(15,5);
		$edit_button .= delete_button("delete_topic_text", "topic_id=".$topic_id."&id=".(int)$row->id."")."<BR>";
		$topic_out = str_replace("{{admin}}", $edit_button, $topic_out);
	}
	if($topic_permission['add_comment']){
		$topic_out = str_replace("{{add_comment}}", popup_wndow("<span class=\"add_comment\">"._ADD_COMMENT."</span>", $module, "add_comment", "topic_id=".get_int('topic_id')."&id=".$row->id, "clear_post"), $topic_out);
	}
	$topic_out = str_replace("{{module}}", $module, $topic_out);
	$topic_out = str_replace("{{topic_title}}", $row->title, $topic_out);
	$topic_out = str_replace("{{topic_date}}", date("Y-m-d", $row->date), $topic_out);
	$topic_out = str_replace("{{topic_short_date}}", date("m-d", $row->date), $topic_out);
	$topic_out = str_replace("{{topic_time}}", date("H:i", $row->date), $topic_out);
	$topic_out = str_replace("{{topic_text}}", htmlspecialchars_decode($row->text), $topic_out);
	$topic_out = str_replace("{{topic_sub_text}}", htmlspecialchars_decode(explode_text($row->text, 30)), $topic_out);
	$topic_out = str_replace("{{topic_pic}}", pic_resize("upload/".$module."/gallery/thumb/","".rawurldecode($row->pic)."", $topic_conf['small_img_width'], $topic_conf['small_img_height']), $topic_out);
	$topic_out = str_replace("{{topic_id}}", $row->topic_id, $topic_out);
	$topic_out = str_replace("{{id}}", $row->id, $topic_out);
	$topic_out = str_replace("{{topic_comments}}", $row->comment_amount." "._COMMENTS, $topic_out);

	$shareurl = rawurlencode($global_conf['location']."/index.php?module=".$module."&page=detals&topic_id=".get_int('topic_id')."&id=".$row->id);
 	$topic_out = str_replace("{{share_url}}", $shareurl, $topic_out);
 	$topic_out = str_replace("{{share_title}}", rawurlencode($row->title), $topic_out);

	if(eregi("^<p>", $short_text)){
		$short_text = ltrim($short_text, "<p>");
		$short_text = rtrim($short_text, "</p>");
	}
	$short_text = $row->short_text;
	if($short_text == ""){
		$short_text = strip_tags(explode_text(htmlspecialchars_decode($row->text), 50), '<br>, <a>, <font>');
	}
	$topic_out = str_replace("{{topic_short_text}}", $short_text, $topic_out);
	$topic_out = str_replace("{{topic_clear_short_text}}", $row->short_text, $topic_out);
	if((int)$row->cat_id !== 0){
		$row_cat = $query->select_obj_sql("topic_categories", "*", "id = ".$row->cat_id."");
		$topic_out = str_replace("{{topic_category}}",
								"<a href=\"index.php?module=".$module."&topic_id=".get_int('topic_id')."&cat_id=".$row_cat->id."\">".$row_cat->name."</a>", $topic_out);
	}
	if((int)$row->user_id !== 0){
		$user_info = $query->user_info($row->user_id);
		$topic_out = str_replace("{{user_image}}", user_info_box($row->user_id, pic_resize($user_info['pic_small'], "", 40, 40, "bottom")), $topic_out);
		$topic_out = str_replace("{{user_name_surname}}", user_info_box($row->user_id, $user_info['name_surname']), $topic_out);
		$topic_out = str_replace("{{user_org}}", $user_info['org'], $topic_out);
	}

	//************ split_text ***********************
	$split_text = "<span onclick=\"show_block_relative('split_text".$row->id."')\"> &gt;&gt;&gt;</span>\n";
 	$split_text .= "</span><span id=\"split_text".$row->id."\" style=\"position: absolute; visibility: hidden\">\n";
 	$topic_out = str_replace("{{split_text}}", $split_text, $topic_out);
 	//***********************************************

	//******************* custom ***********************
	$complaint_style = chng_style($complaint_style, 'complaint_main1', 'complaint_main2');
	$topic_out = str_replace("{{complaint_main_class}}", $complaint_style, $topic_out);
	//**************************************************

	//***************** comments ************************************
	if($topic_conf['comment_per_page'] !== 0){
		$_GET['id'] = $row->id;
		$_GET['limit'] = "0|".$template[$n]['comments_amount'];
		include("modules/".$module."/files/comments.php");
		$_GET['id'] = 0;
		$_GET['limit'] = "";
	}
	//***************************************************************

	$comment_where = "topic_text_id = ".$row->id."";
	if(!$topic_permission['edit_comment']){
		$comment_where .= " AND active = 1";
	}
	$comment_amount = $query->amount_fields("topic_comments", $comment_where);
	$topic_out = str_replace("{{comments_amount}}", $comment_amount, $topic_out);
	$topic_out = str_replace("{{comment_limit_from}}", $template[$n]['comments_amount'], $topic_out);
	$topic_out = str_replace("{{comment_limit_to}}", $topic_conf['more_comments_amount'], $topic_out);
	if($comment_amount > $template[$n]['comments_amount']){
		$topic_out = str_replace("{{view_more_comments}}", _VIEW_MORE_COMMENTS, $topic_out);
	}
	$topic_out = str_replace("{{view_all_comments}}", _VIEW_ALL_COMMENTS, $topic_out);

	if($m !== 0){
		$m--;
	}

    $out .= $topic_out;
}

if(strpos($out, "categories") !== false){
	$out_temp = $out;
	include("modules/".$module."/files/view_categories.php");
	$out_temp = str_replace("{{categories}}", $out, $out_temp);
	$out = $out_temp;
}
//

//********** global replace ******************
$out = str_replace("{{topic_head}}", $row_head->name, $out);
$out = str_replace("{{topic_id}}", get_int('topic_id'), $out);
$out = str_replace("{{global_cat_id}}", get_int('cat_id'), $out);
$out = str_replace("{{global_main_cat_id}}", get_int('main_cat_id'), $out);
$out = str_replace("{{lang}}", $lang, $out);
$out = str_replace("{{module}}", $module, $out);

$out = str_replace("[text_".$lang."]", "&nbsp;}}\n", $out);
$out = str_replace("[/text_".$lang."]", "\n{{&nbsp;", $out);

$split_page = "<div class=\"split_page\">\n";
$split_page .= split_page("topic_text", $where, $topic_amount);
$split_page .= "</div>\n";

$out = str_replace("{{split_page}}", $split_page, $out);

$out = eregi_replace("{{([[:print:]]+)}}", "", $out);
//********************************************

$out .= "</div>\n";


?>