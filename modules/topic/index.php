<?php
global $topic_conf, $topic_permission;
$topic_conf = $query->select_ar_sql("topic_config", "*", "topic_id = ".get_int('topic_id')."");
unset($out);

if(!function_exists('topic_head')){
function topic_head($text){	$head_style['new'] = "topic_head_passive";
	$head_style['popular'] = "topic_head_passive";
	$head_style['my'] = "topic_head_passive";
	$head_style['follow'] = "topic_head_passive";

	if(in_array(get('search_type'), array_keys($head_style))){
		$head_style[get('search_type')] = "topic_head_active";
	}
	else{
		$head_style['new'] = "topic_head_active";
	}

	foreach($head_style as $key => $value){
		$text = str_replace("{{active_".$key."}}", $value, $text);
	}
	return $text;
}
}
if (!function_exists("tmpl_id")){
function tmpl_id($topic_id, $tmpl_conf = "template"){
	global $query;
	if($query->amount_fields("topic_".$tmpl_conf, "topic_id = ".(int)$topic_id."") == 0){
		$row = $query->select_obj_sql("topic_pages", "id", "default_topic = 1");
		return $row->id;
	}
	else{
		return $topic_id;
	}
}
}

if(is_post('add_comment') && get_int('comment_id') == 0){	$query->permission_end($module, 'add_comment');
	if($query->amount_fields("topic_comments", "user_id = ".(int)$_SESSION['login']['user_id']." AND add_date > '".(time() - 5)."'") == 0
		&& post("comment_text") !== false){

		$row_topic = $query->select_obj_sql("topic_text", "cat_id", "id = ".get_int('id')."");
		$field['cat_id'] = $row_topic->cat_id;
		$field['topic_id'] = get_int('topic_id');
		$field['topic_text_id'] = get_int('id');
		$field['user_id'] = $_SESSION['login']['user_id'];
		$field['add_date'] = time();
		$field['comment'] = post('comment_text');
		if(!$query->group_perm($module, 'delete_topic')){
			$field['comment'] = mb_substr(post('comment_text'), 0, $topic_conf['max_comment_text'],'UTF-8');
		}
		$field['parent_id'] = get_int('parent_comment_id');

		$row_user = $query->select_obj_sql("topic_comments", "user_id", "id = ".get_int('parent_comment_id')."");
		$field['parent_user_id'] = $row_user->user_id;

		if((int)$topic_conf['comment_need_accept'] == 0){			$field['active'] = 1;
			$query->update_sql("topic_text", array("comment_amount" => "INCREASE"), "id = ".get_int('id')."");		}

		$comment_id = $query->insert_sql("topic_comments", $field);
		if(get_int('parent_comment_id') == 0){
			comment_add_karma($comment_id);
		}
		else{
			comment_answer_karma($comment_id);
		}
	}
	$where = " AND active = 1";
	if($query->group_perm($module, 'edit_comment')){
		$where = "";
	}
	$page = ceil($query->amount_fields("topic_comments", "topic_text_id = ".get_int('id')."".$where) / $topic_conf['comment_per_page']);

	header("Location: index.php?module=".$module."&page=detals&topic_id=".get_int('topic_id')."&id=".get_int('id')."&pg=".$page);
	exit;
}

if(is_post('add_comment') && get_int('comment_id') !== 0){
	$query->permission_end($module, 'add_comment');
	if($query->amount_fields("topic_comments", "user_id = ".(int)$_SESSION['login']['user_id']." AND add_date > '".date("Y-m-d H:i:s", (time() - 5))."'") == 0
		&& post("comment_text") !== false){

		$field['comment'] = post('comment_text');
		if(!$query->group_perm($module, 'delete_topic')){
			$field['comment'] = mb_substr(post('comment_text'), 0, $topic_conf['max_comment_text'],'UTF-8');
		}

		$query->update_sql("topic_comments", $field, "id = ".get_int('comment_id')."");
	}
	header("Location: index.php?module=".$module."&page=detals&topic_id=".get_int('topic_id')."&id=".get_int('id')."");
	exit;
}

if(get('action') == "evaluate"){
	$query->permission_end($module, 'evaluate_comment');
	if($query->amount_fields("topic_comments_evaluates", "user_id = ".(int)$_SESSION['login']['user_id']." AND comment_id = ".get_int('comment_id')."") == 0
		&& in_array(get('type'), array("plus", "minus"))){
		$query->update_sql("topic_comments", array(get('type') => "INCREASE"), "id = ".get_int('comment_id')."");

		$fields['user_id'] = $_SESSION['login']['user_id'];
		$fields['comment_id'] = get_int('comment_id');
		$plus_minus['plus'] = 1;
		$plus_minus['minus'] = 0;
		$fields['plus_minus'] = $plus_minus[get('type')];
		$query->insert_sql("topic_comments_evaluates", $fields);
		comment_evaluate_karma(get_int('comment_id'), get('type'));
	}
	$row_karma = $query->select_obj_sql("topic_comments", "plus, minus", "id = ".get_int('comment_id') . "");
	echo view_karma(get_int('comment_id'), $row_karma->plus, $row_karma->minus, $_SESSION ['login'] ['user_id']);
	exit();
}

//****************** permissions ********************************
$topic_permission['edit_topic'] = $user_class->group_perm($module, 'edit_topic');
$topic_permission['accept_topic'] = $user_class->group_perm($module, 'accept_topic');
$topic_permission['evaluate_comment'] = $user_class->group_perm($module, 'evaluate_comment');
$topic_permission['add_comment'] = $user_class->group_perm($module, 'add_comment');
$topic_permission['edit_comment'] = $user_class->group_perm($module, 'edit_comment');
//***************************************************************

$pages = array (
		'main',
		'detals',
		'pic_view',
		'view_categories',
		'add_comment',
		'view_comments',
		'comments',
		'search',
		'search_form',
		'complaints_cat',
		'topic',
		'slider',
		'events'
);
load_page(get('page'), $pages);
?>