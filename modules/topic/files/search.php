<?php
global $out, $topic_conf;
$out = '';

if(get_int('pg') !== 0){
	$page_start = get_int('pg');
}
else $page_start = 1;

$topic_amount = 15;

$topic_id = set_var(get_int('topic_id'), 18);

$tmp_id = tmpl_id($topic_id);

$row_head = $query->select_obj_sql("topic_pages", "name, id", "id = '".$topic_id."'");

if(get_int('tmpl_id') !== 0){
	$tmpl_where = " AND id = ".get_int('tmpl_id')."";
}
$result_tmpl = $query->select_sql("topic_template", "*", "topic_id = ".(int)$tmp_id."".$tmpl_where, "priority ASC");
$template_n = array();
while($row_tmpl = $query->obj($result_tmpl)){
	if($row_tmpl->topic_comment == 1){
		$template[$row_tmpl->priority]['design'] = htmlspecialchars_decode($row_tmpl->design);
		$template[$row_tmpl->priority]['repeat'] = $row_tmpl->repeat;
		$template[$row_tmpl->priority]['comments_amount'] = $row_tmpl->comments_amount;
		if((int)$row_tmpl->repeat == 0 && (int)$template[0] == 0){
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
if($query->group_perm($module, 'edit_topic')){
	$add_topic = "<div><a href=\"admin.php?module=".$module."&page=&page=addedit_topic&topic_id=".$topic_id."\">
				<font class=\"edit\">"._ADD_TOPIC."</font></a></div>";
	$template_head = str_replace("{{add_topic_button}}", $add_topic, $template_head);
}
$out .= $template_head;

$out .= "<div class=\"topic_main\">\n";

//****************** search *******************************
$where = "active >= 1";
if(get('search_type') == "my"){	
	$where .= " AND user_id = ".(int)$_SESSION['login']['user_id']." AND topic_id = ".get_int('topic_id')."";
	$result_search = $query->select_sql("topic_comments", "topic_text_id", $where, "", "".(($page_start-1)*$topic_amount).", ".(int)$topic_amount."", "topic_text_id DESC");
	$split_page_table = "topic_comments";
}

if(get('search_type') == "follow"){
	$topic_comment = "topic";
	$where .= " AND u.user_id = ".(int)$_SESSION['login']['user_id']." AND topic_id = ".get_int('topic_id')."";
	$result_search = $query->select_sql("topic_text t INNER JOIN ".$global_conf['table_pref']."users_friends u ON (u.friend_id = t.user_id)", "t.*", $where, "", "".(($page_start-1)*$topic_amount).", ".(int)$topic_amount."");
	$split_page_table = "topic_text t INNER JOIN ".$global_conf['table_pref']."users_friends u ON (u.friend_id = t.user_id)";
}

if(get('keyword') !== false){
	if(get('topic_comment') == "comment"){
		$topic_comment = "comment";
		$where .= " AND comment LIKE '%".get('keyword')."%'";
		$result_search = $query->select_sql("topic_comments", "topic_text_id", $where, "", "".(($page_start-1)*$topic_amount).", ".(int)$topic_amount."", "topic_text_id DESC");
		$split_page_table = "topic_comments";
	}
	else{
		$topic_comment = "topic";
		if(get_int('topic_id') !== 0){
			$where .= " AND topic_id = ".get_int('topic_id');
		}
		$query->where_vars['keyword'] = get('keyword');
		$where .= " AND (text LIKE '%{{keyword}}%' OR title LIKE '%{{keyword}}%')";
		$result_search = $query->select_sql("topic_text", "*", $where, "date DESC", "".(($page_start-1)*$topic_amount).", ".(int)$topic_amount."");
		$split_page_table = "topic_text";
	}

}
if($where == "active = 1"){	
	$where = 0;
	$result_search = $query->select_sql("topic_text", "*", 0);
	$split_page_table = "topic_text";
}
//*********************************************************

$n=($page_start-1)*$topic_amount; $m=0;
//****************** permissions ********************************
$edit_topic_permission = $query->group_perm($module, 'edit_topic');
$evaluate_comment_permission = $query->group_perm($module, 'evaluate_comment');
$add_comment_permission = $query->group_perm($module, 'add_comment');
//***************************************************************
while($row_search = $query->obj($result_search)){
	if($topic_comment == "topic"){		
		$row = $row_search;
	}
	else{		
		$row = $query->select_obj_sql("topic_text", "*", "id = ".(int)$row_search->topic_text_id." AND active = 1");	
	}
	if($m == 0){
		$n++;
		$m = $template[$n]['repeat'];
        if(!in_array($n, (array)$template_n)){
        	$n = 0;
        	$m = $topic_amount;
        }
	}
	$topic_out = $template[2]['design'];
	if($edit_topic_permission){
		$edit_button = edit_button("page=addedit_topic&topic_id=".$topic_id."&id=".$row->id);
		$edit_button .= space(15,5);
		$edit_button .= delete_button("delete_topic_text", "topic_id=".$topic_id."&id=".(int)$row->id."")."<BR>";
		$topic_out = str_replace("{{admin}}", $edit_button, $topic_out);
	}
	$topic_out = str_replace("{{module}}", $module, $topic_out);
	$topic_out = str_replace("{{topic_title}}", $row->title, $topic_out);
	$topic_out = str_replace("{{topic_date}}", substr($row->date, 0, 10), $topic_out);
	$topic_out = str_replace("{{topic_short_date}}", substr($row->date, 5, 5), $topic_out);
	$topic_out = str_replace("{{topic_time}}", substr($row->date, 11, 5), $topic_out);
	$topic_out = str_replace("{{topic_text}}", htmlspecialchars_decode($row->text), $topic_out);
	$topic_out = str_replace("{{topic_pic}}", pic_resize("upload/".$module."/gallery/thumb/","".rawurldecode($row->pic)."", $topic_conf['small_img_width'], $topic_conf['small_img_height']), $topic_out);
	$topic_out = str_replace("{{topic_id}}", $row->topic_id, $topic_out);
	$topic_out = str_replace("{{id}}", $row->id, $topic_out);
	$topic_out = str_replace("{{topic_comments}}", $row->comment_amount." "._COMMENTS, $topic_out);

	$shareurl = rawurlencode($global_conf['location']."/index.php?module=".$module."&page=detals&topic_id=".get_int('topic_id')."&id=".$row->id);
 	$topic_out = str_replace("{{share_url}}", $shareurl, $topic_out);
 	$topic_out = str_replace("{{share_title}}", rawurlencode($row->title), $topic_out);

	if(strpos($short_text, "^<p>")){
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
	/* if((int)$row->user_id !== 0){
		$user_info = $query->user_info($row->user_id);
		$topic_out = str_replace("{{user_image}}", user_info_box($row->user_id, pic_resize($user_info['pic_small'], "", 40, 40, "bottom")), $topic_out);
		$topic_out = str_replace("{{user_name_surname}}", user_info_box($row->user_id, $user_info['name_surname']), $topic_out);
		$topic_out = str_replace("{{user_org}}", $user_info['org'], $topic_out);
	} */

	//******************* custom ***********************
	//$complaint_style = chng_style($complaint_style, 'complaint_main1', 'complaint_main2');
	$topic_out = str_replace("{{complaint_main_class}}", $complaint_style, $topic_out);
	//**************************************************
    //***************** comments ************************************
	/* if($topic_conf['comment_per_page'] !== 0){
		$_GET['id'] = $row->id;
		$_GET['limit'] = "0|".$template[$n]['comments_amount'];
		include("modules/".$module."/files/comments.php");
		$_GET['id'] = 0;
		$_GET['limit'] = "";
	} */
	//***************************************************************

	/* $comment_where = "topic_text_id = ".(int)$row->id."";
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
 */
	if($m !== 0){
		$m--;
	}

    $out .= $topic_out;
}

$out .= $template_footer;

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
$split_page .= split_page($split_page_table, $where, $topic_amount);
$split_page .= "</div>\n";

$out = str_replace("{{split_page}}", $split_page, $out);

$out = preg_replace("/{{([[:print:]]+)}}/", "", $out);
//********************************************

$out .= "</div>\n";


?>