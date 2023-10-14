<?php
global $out, $topic_conf, $topic_permission, $template_comment;

if(get_int('pg') !== 0){
	$page_start = get_int('pg');
}
else $page_start = 1;

$row_tmpl_comment = $query->select_obj_sql("topic_template", "*", "topic_id = ".get_int('topic_id')." AND topic_comment = 2", "priority ASC", "0,1");

$template_comment = htmlspecialchars_decode($row_tmpl_comment->design);

//****************** permissions ********************************
$evaluate_comment_permission = $query->group_perm($module, 'evaluate_comment');
$add_comment_permission = $query->group_perm($module, 'add_comment');
$edit_comment_permission = $query->group_perm($module, 'edit_comment');
//***************************************************************
if(!function_exists('view_comments')){
function view_comments($where_comment, $comment_discuss = 0, $order_by = "", $limit = ""){
	global $query, $comment_out,  $topic_conf, $module, $topic_permission, $template_comment;

	//***********************************************
	if(get_int('pg') !== 0){
		$page_start = get_int('pg');
	}
	else $page_start = 1;
    //***********************************************
    if($order_by == ""){		$order_by = "add_date ASC";	}
	else{
		$order_ar = explode("|", $order_by);
		$accept_ar = array("add_date", "asc", "desc");
		if(in_array($order_ar[0], $accept_ar) && in_array($order_ar[1], $accept_ar)){
			$order_by = $order_ar[0]." ".$order_ar[1];
		}
	}
	//*********************************************
	if($limit == ""){
		$limit = "".(($page_start-1)*$topic_conf['comment_per_page']).", ".(int)$topic_conf['comment_per_page']."";
	}
	else{
		$limit_ar = explode("|", $limit);
		$limit = $limit_ar[0].",".$limit_ar[1];
	}
	//***********************************************
	$accept_img[0] = "accept.gif";
	$accept_img[1] = "decline.gif";
	$result_comment = $query->select_sql("topic_comments", "*", $where_comment, $order_by, $limit);
	if($comment_discuss == 0){		$comment_out="";
	}
	while($row_comment = $query->obj($result_comment)){
 		$comment = $template_comment;
       	if($topic_permission['edit_comment']){
			$edit_button = "<span id=\"accept_comment_".$row_comment->id."\">\n";
			$edit_button .= ajax_get("accept_comment_".$row_comment->id, "<img src=\"images/".$accept_img[$row_comment->active]."\" border=\"0\">\n", $module, "", "id=".get_int('id')."&action=accept_comment&comment_id=".$row_comment->id);
			$edit_button .= "</span>\n";
			$edit_button .= space(15,5);
			$edit_button .= popup_wndow("<img src=\"images/edit.png\" alt=\""._EDIT."\" border=\"0\">", $module, "add_comment", "topic_id=".get_int('topic_id')."&id=".get_int('id')."&comment_id=".$row_comment->id, "clear_post");
			$edit_button .= space(15,5);
			$edit_button .= delete_button("delete_comment", "topic_id=".get_int('topic_id')."&id=".(int)$row_comment->topic_text_id."&comment_id=".$row_comment->id."")."<BR>";
			$comment = str_replace("{{comment_admin}}", $edit_button, $comment);
		}
 		if((int)$row_comment->user_id !== 0){
			$user_info = $query->user_info($row_comment->user_id);
			//$comment = str_replace("{{comment_user_image}}", user_info_box($row_comment->user_id, pic_resize($user_info['pic_small'], "", 40, 40, "bottom")), $comment);
			//$comment = str_replace("{{comment_user_name_surname}}", user_info_box($row_comment->user_id, $user_info['name_surname']), $comment);
			$comment = str_replace("{{comment_add_date}}", _ADDED." ".date("Y-m-d H:i:s", (int)$row_comment->add_date), $comment);
			$comment = str_replace("{{comment_id}}", $row_comment->id, $comment);
			if($user_info['color'] !== ""){				$comment = str_replace("{{user_color}}", "style=\"background-image: url('functions/opacity.php?color=".ltrim($user_info['color'], "#")."')\"", $comment);			}
		}
		$comment_text_out = "";
		//********************* comment answer **************************************
		if((int)$row_comment->parent_id !== 0){
			$comment_text_out .= "<div style=\"clear: both; text-align: left;\">\n";
			$comment_text_out .= "<div class=\"comment_answer\">\n";
			$comment_text_out .= "<div><b>"._ANSWER_TO_COMMENT."</b></div>\n";
			$row_answer = $query->select_obj_sql("topic_comments", "*", "id = ".(int)$row_comment->parent_id."");
    		$row_answer_user = $query->select_obj_sql("users", "*", "id = ".(int)$row_answer->user_id."");
			if((int)$row_comment->user_id !== 0){
				$comment_text = str_replace("{{comment_user_name_surname}}", "<a href=\"\">".$row_answer_user->name." ".$row_answer_user->surname."</a>", $template_comment);
			}
			$comment_text = str_replace("{{comment_add_date}}", _ADDED." ".date("Y-m-d H:i:s", $row_answer->add_date), $comment_text);
			$comment_text = str_replace("{{comment_text}}", nl2br($row_answer->comment), $comment_text);
			$comment_text = eregi_replace("{{([[:print:]]+)}}", "", $comment_text);
			$comment_text_out .= $comment_text;
			$comment_text_out .= "</div>\n";
			$comment_text_out .= "</div>\n";
		}
		//****************************************************************************
	   	$comment_text_out .= nl2br($row_comment->comment);
	   	$comment = str_replace("{{comment_text}}", $comment_text_out, $comment);
       	//if($evaluate_comment_permission){
		 //  	$comment = str_replace("{{comment_evaluate}}", view_karma($row_comment->id, $row_comment->plus, $row_comment->minus, $row_comment->user_id), $comment);
		//}
		if($topic_permission['add_comment']){
		   	$comment = str_replace("{{comment_answer}}", popup_wndow("<span class=\"answer\">"._ANSWER."</span>", $module, "add_comment", "topic_id=".get_int('topic_id')."&id=".get_int('id')."&parent_comment_id=".$row_comment->id, "clear_post"), $comment);
		}
		$comment = str_replace("{{comment_discuss}}", "<a href=\"index.php?module=".$module."&page=view_comments&topic_id=".get_int('topic_id')."&id=".get_int('id')."&comment_discuss=".$row_comment->id."\">"._COMMENT_DISCUSS."</a>", $comment);
		$comment_out .= $comment;

		if($comment_discuss == 1){
			if($query->amount_fields("topic_comments", "parent_id = ".(int)$row_comment->id."") !== 0){
				view_comments("parent_id = ".$row_comment->id, 1);			}
		}
    }
    return $comment_out;}
}

if($topic_conf['comment_per_page'] !== 0){
	$where_comment = "topic_text_id = ".get_int('id')."";
    $comment_discuss = 0;
	if(get_int('comment_discuss') !== 0){		$where_comment = "id = ".get_int('comment_discuss');
		$comment_discuss =1;	}
	if(!$topic_permission['edit_comment']){
		$where_comment .= " AND active = 1";
	}
	$comment_out = view_comments($where_comment, $comment_discuss, get('order_by'), get('limit'));
}
$topic_out = str_replace("{{comment}}", $comment_out, $topic_out);
$split_page = "<div class=\"split_page\">\n";
$split_page .= split_page("topic_comments", $where_comment, $topic_conf['comment_per_page']);
$split_page .= "</div>\n";

$topic_out = str_replace("{{split_comments}}", $split_page, $topic_out);
if($m !== 0){
	$m--;
}

//************* add comments ******************
if($topic_permission['add_comment']){
	$add_comments = form_start("topic_id=".get_int('topic_id')."&id=".get_int('id')."", "user");

	$max_comment_text = $topic_conf['max_comment_text'];
	if($query->group_perm($module, 'delete_topic')){
		$max_comment_text = 0;
	}
	$add_comments .= "<div style=\"clear: both\">\n";
	if((int)$max_comment_text !== 0){
		$add_comments .= "<font class=\"error\">"._TEXT_MUST." ".$max_comment_text.""._S."</font><br>\n";
	}
	$add_comments .= "<textarea name=\"comment_text\" id=\"comment_text\" class=\"add_comment_text\" onKeyup=\"textCounter('comment_text',".$max_comment_text.", '"._OVER_TEXT_LIMIT."');\" onKeydown=\"textCounter('comment_text',".$max_comment_text.", '"._OVER_TEXT_LIMIT."');\"></textarea>\n";
    $add_comments .= "</div>\n";

	if((int)$max_comment_text !== 0){		$add_comment_button = "<div style=\"float: left\"><input readonly type=text id=\"comment_text_counter\" size=3 maxlength=3 value=\"".$max_comment_text."\"></div>\n";
	}
	$add_comment_button .= "<div style=\"float: right\"><input type=\"submit\" value=\""._SEND."\" name=\"add_comment\" class=\"button\"></div>\n";
	$add_comment_button .= form_end();
	$topic_out = str_replace("{{add_comment_area}}", $add_comments, $topic_out);
	$topic_out = str_replace("{{add_comment_button}}", $add_comment_button, $topic_out);
}
//*********************************************
if(get('echo') == "yes"){
	$max_limit = explode("|", get('limit'));
	$comment_out .= "<div id=\"more_comments_".get_int('id')."".($max_limit[0] + $max_limit[1])."\"></div>\n";
	$comment_out = eregi_replace("{{([[:print:]]+)}}", "", $comment_out);
	echo $comment_out;
}

?>