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

$topic_id = set_var(get_int('topic_id'), 1);

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

$cat_id = get_int('cat_id');
//****************** search *******************************
$order_by = "";
$where = "topic_id = ".$topic_id;
if(!$topic_permission['edit_topic'] && (int)$topic_conf['topic_need_accept'] == 1){
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

if(get_int('date') !== 0){
	$where .= " AND `date` >= '".get('date')."' AND `date` <= '".get('date')."'";
}

if(get('date') == "today"){
	$query->where_vars['curent_date'] = current_date();
	$where .= " AND `date` = '{{curent_date}}'";
}

if(get('date') == "future"){
	$query->where_vars['curent_date'] = current_date();
	$where .= " AND `date` >= '{{curent_date}}'";
}

if(get_int('limit') < $topic_amount && get_int('limit') !== 0){
	$topic_amount = get_int('limit');
}

//***********************************************
if(get('topic_order_by') == false){
	$order_by = "`date` DESC";
}
else{
	$order_ar = explode("|", get('topic_order_by'));
	$accept_ar = array("cat_id", "date", "asc", "desc");
	if(in_array($order_ar[0], $accept_ar) && in_array($order_ar[1], $accept_ar)){
		$order_by = $order_ar[0]." ".$order_ar[1];
	}
}
//*********************************************************
//************** add button *******************************
if($topic_permission['edit_topic']){
	$add_topic = "<div><a href=\"".MANAGE_DIR."admin.php?module=".$module."&page=addedit_topic&cat_id=".$max_cat."&topic_id=".$topic_id."\">
				<font class=\"edit\">"._ADD_TOPIC."</font></a></div>";
	$template_head = str_replace("{{add_topic_button}}", $add_topic, $template_head);
}
//*********************************************************
$out .= $template_head;

$topic_table = (int)$topic_conf['multi_dates'] == 1 ? "topic_multi_date_text" : "topic_text";
$result = $query->select_sql($topic_table, "*", $where, $order_by, "".(($page_start-1)*$topic_amount).", ".(int)$topic_amount."");

$n=($page_start-1)*$topic_amount; $m=0;

$accept_img[0] = "accept.gif";
$accept_img[1] = "decline.gif";
$choice_img[0] = "good_red.png";
$choice_img[1] = "good_green.png";
$cur_template=0;
while($row = $query->obj($result)){
     $n++;
     foreach($template_n as $temp_n){
       	if((int)$template[$temp_n]['repeat'] == 0){
       		$cur_template=0;
       		$m = $topic_amount;
       		break;
       	}
       	elseif(($n - $template[$temp_n]['repeat']) <= 0){
       		$cur_template = $temp_n;
       		$m = $n - $template[$temp_n]['repeate'];
       		break;
       	}
	}
	if($topic_permission['edit_topic']){
		/*$edit_button = "<span id=\"accept_topic_".$row->id."\">\n";
		$edit_button .= "<a href=\"admin.php?module=".$module."&page=addedit_topic&cat_id=".$max_cat."&topic_id=".$topic_id."\">
							<font class=\"edit\"><img src=\"images/add.gif\"></font></a>\n";
		$edit_button .= "</span>\n";*/
		if($topic_permission['accept_topic'] && (int)$topic_conf['topic_need_accept'] == 1){
			$edit_button .= "<span id=\"accept_topic_".$row->id."\">\n";
			$edit_button .= ajax_get("accept_topic_".$row->id, "<img src=\"images/".$accept_img[$row->active]."\" border=\"0\">\n", $module, "", "action=accept_topic&topic_id=".get_int('topic_id')."&topic_text_id=".$row->id);
			$edit_button .= "</span>\n";
			$edit_button .= space(15,5);			
		}
		if((int)$topic_conf['choose_main_page'] == 1){
			$edit_button .= "<span id=\"choice_".$row->id."\">\n";
			$edit_button .= ajax_get("choice_".$row->id, "<img src=\"images/".$choice_img[$row->rcheuli]."\" border=\"0\">\n", $module, "", "action=choice&topic_text_id=".$row->id);
			$edit_button .= "</span>\n";
			$edit_button .= space(15,5);
		}
		$edit_button .= edit_button("page=addedit_topic&topic_id=".$topic_id."&id=".$row->id);
		$edit_button .= space(15,5);
		$edit_button .= delete_button("delete_topic_text", "topic_id=".$topic_id."&id=".(int)$row->id."")."<BR>";
		$edit_button = "<div id=\"topic_admin_buttons_".$row->id."\" class=\"topic_admin_buttons\">".$edit_button."</div>";   
		$topic_out["{{admin}}"] = $edit_button;
		$show_admin = "onmouseover=\"show_admin('show', 'topic_admin_buttons_".$row->id."')\" onmouseout=\"show_admin('hide', 'topic_admin_buttons_".$row->id."')\"";
		//$topic_out["{{show_admin}}"] = $show_admin;
	}
	if($topic_permission['add_comment']){
		$topic_out["{{add_comment}}"] = popup_wndow("<span class=\"add_comment\">"._ADD_COMMENT."</span>", $module, "add_comment", "topic_id=".get_int('topic_id')."&id=".$row->id, "clear_post");
	}
	$topic_out["{{n}}"] = $n;
	$topic_out["{{module}}"] = $module;
	$topic_out["{{_MORE}}"] = _MORE;
	$topic_out["{{_START_TIME}}"] = _START_TIME;
	$topic_out["{{start_time}}"] = substr($row->start_time, 0, 5);
	$topic_out["{{topic_title}}"] = $row->title;
	$topic_out["{{cat_id}}"] = $row->cat_id;
	$topic_out["{{topic_date}}"] = date("d.m.Y", date_to_unix($row->date));
	$topic_out["{{topic_standart_date}}"] = $row->date;
	$v_date = date("d {{v}} Y", date_to_unix($row->date));
	$topic_out["{{topic_v_date}}"] = str_replace("{{v}}", $ar_month[(int)date("m", date_to_unix($row->date))], $v_date);
	//$topic_out["{{topic_short_date}}"] = date("m-d", $row->date);
	//$topic_out["{{topic_time}}"] = date("H:i", $row->date);
	$topic_out["{{topic_text}}"] = htmlspecialchars_decode($row->text);
	$topic_out["{{topic_sub_text}}"] = htmlspecialchars_decode(explode_text($row->text, 30));
	$topic_out["{{topic_id}}"] = $row->topic_id;
	$topic_out["{{id}}"] = $row->id;
	$topic_out["{{topic_comments}}"] = $row->comment_amount." "._COMMENTS;

	$shareurl = rawurlencode($global_conf['location']."/topicdetals-".get_int('topic_id').".".$row->id.".html");
 	$topic_out["{{share_url}}"] = $shareurl;
 	$topic_out["{{share_title}}"] = rawurlencode($row->title);

	/*if(preg_match("/^<p>/", $short_text)){
		$short_text = ltrim($short_text, "<p>");
		$short_text = rtrim($short_text, "</p>");
	}
	$short_text = $row->short_text;
	if($short_text == ""){
		$short_text = strip_tags(explode_text(htmlspecialchars_decode($row->text), 50), '<br>, <a>, <font>');
	}*/
	$topic_out["{{topic_short_text}}"] = strip_tags($row->short_text, "<br>, <b>, <a>, <p>");
	$topic_out["{{topic_clear_short_text}}"] = strip_tags($row->short_text);
	//$topic_out["{{topic_clear_short_text}}"] = $row->short_text;
	if((int)$row->cat_id !== 0){
		$row_cat = $query->select_obj_sql("topic_categories", "*", "id = ".$row->cat_id."");
		$topic_out["{{topic_category}}"] =
								"<a href=\"index.php?module=".$module."&topic_id=".get_int('topic_id')."&cat_id=".$row_cat->id."\">".$row_cat->name."</a>";
	}
	else{
		$topic_out["{{topic_category}}"] = "";
	}
	if((int)$row->user_id !== 0){
		$user_info = $user_class->user_info($row->user_id);
		//$topic_out["{{user_image}}"] = user_info_box($row->user_id, pic_resize($user_info['pic_small'], "", 40, 40, "bottom"));
		//$topic_out["{{user_name_surname}}"] = user_info_box($row->user_id, $user_info['name_surname']);
		$topic_out["{{user_org}}"] = $user_info['org'];
	}

	//*************** main picture ******************
	$main_picture = $query->select_obj_sql("topic_gallery", "*", "topic_text_id = ".$row->id." AND `default` = 1");
	if(is_file("upload/".$module."/gallery/".$main_picture->pic)){
		$topic_out["{{topic_gallery_main_image}}"] = "upload/".$module."/gallery/".$main_picture->pic;
		$topic_out["{{image_id}}"] = $main_picture->id;

		$topic_out["{{topic_pic}}"] = pic_resize("upload/".$module."/gallery/thumb/", $main_picture->pic, $topic_conf['small_img_width'], $topic_conf['small_img_height']);
		$topic_out["{{image}}"] = "upload/".$module."/gallery/thumb/".$main_picture->pic;
	}
	else{
		$topic_out["{{topic_gallery_main_image}}"] = "images/topic_default_picture.jpg";
		$topic_out["{{topic_pic}}"] = pic_resize("images/topic_default_picture.jpg","".rawurldecode($row->pic)."", $topic_conf['small_img_width'], $topic_conf['small_img_height']);
		$topic_out["{{image}}"] = "images/topic_default_picture.jpg";
	}
	//***********************************************
	//************ split_text ***********************
	$split_text = "<span id=\"split_text_cursor".$row->id."\" onclick=\"show_block_relative('split_text".$row->id."');hide_block_relative('split_text_cursor".$row->id."')\" class=\"split_text\"> &gt;&gt;&gt;</span>\n";
 	$split_text .= "</span><span id=\"split_text".$row->id."\" style=\"position: absolute; visibility: hidden\">\n";
 	$topic_out["{{split_text}}"] = $split_text;
 	//***********************************************

	//******************* custom ***********************
	$complaint_style = $templates->change_style('complaint_main1', 'complaint_main2');
	$topic_out["{{complaint_main_class}}"] = $complaint_style;
	//**************************************************

	//***************** comments ************************************
	if((int)$topic_conf['comment_per_page'] !== 0){
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
	$topic_out["{{comments_amount}}"] = $comment_amount;
	$topic_out["{{comment_limit_from}}"] = $template[$n]['comments_amount'];
	$topic_out["{{comment_limit_to}}"] = $topic_conf['more_comments_amount'];
	if($comment_amount > $template[$n]['comments_amount']){
		$topic_out["{{view_more_comments}}"] = _VIEW_MORE_COMMENTS;
	}
	$topic_out["{{view_all_comments}}"] = _VIEW_ALL_COMMENTS;

	//************ bolo atvirtuli faili ***********
	$row_last_file = $query->select_obj_sql("topic_files", "*", "topic_text_id = ".(int)$row->id);
	$topic_out['{{last_file_title}}'] = $row_last_file->title;
	$topic_out['{{last_file_link}}'] = "upload/".$module."/".$row_last_file->file;
	//*********************************************

	if($m !== 0){
		$m--;
	}

    $out .= str_replace(array_keys($topic_out), array_values($topic_out), $template[$cur_template]['design']);
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
$global_values["{{add_topic_button}}"] = $add_topic;
$global_values["{{topic_head}}"] = $row_head->name;
$global_values["{{topic_id}}"] = get_int('topic_id');
$global_values["{{global_cat_id}}"] = get_int('cat_id');
$global_values["{{global_main_cat_id}}"] = get_int('main_cat_id');
$global_values["{{lang}}"] = $lang;
$global_values["{{module}}"] = $module;
$global_values["{{page}}"] = $page_start;
$global_values["{{topic_amount}}"] = $query->amount_fields("topic_text", $where);

$global_values["[text_".$lang."]"] = "&nbsp;}}\n";
$global_values["[/text_".$lang."]"] = "\n{{&nbsp;";

$split_page = "<div class=\"split_page\">\n";
$split_page .= split_page("topic_text", $where, $topic_amount);
$split_page .= "</div>\n";

$global_values["{{split_page}}"] = $split_page;

$out = str_replace(array_keys($global_values), array_values($global_values), $out);

$out = preg_replace("/{{([[:print:]]+)}}/", "", $out);

$out .= "<script type=\"text/javascript\">\n";
$out .= " $(\".topic_admin\").mouseover(function(){
			$(\"#topic_admin_buttons_\"+$(this).attr('data-id')).css('display', 'block');
		})\n";
$out .= " $(\".topic_admin\").mouseout(function(){
			$(\"#topic_admin_buttons_\"+$(this).attr('data-id')).css('display', 'none');
		})\n";
$out .= "</script>\n";
//********************************************

?>