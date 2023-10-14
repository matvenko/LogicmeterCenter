<?php
$user_class->permission_end($module, 'all');
$topic_conf = $query->select_ar_sql("topic_config", "*", "topic_id = ".get_int('topic_id')."");

//*************** add topic page ********************
if(is_post('add_topic') && get_int('topic_id') == 0){
	$user_class->permission_end($module, 'admin');
	$fields['name'] = post('topic_name');
	$fields['description'] = post('topic_description');
	$fields['lang'] = post('topic_lang');
	$query->insert_sql("topic_pages", $fields);
	header("Location: admin.php?module=".$module."&page=admin_main");
	exit;
}

if(is_post('add_topic') && get_int('topic_id') !== 0){
	$user_class->permission_end($module, 'admin');
	$fields['name'] = post('topic_name');
	$fields['description'] = post('topic_description');
	$fields['lang'] = post('topic_lang');
	$query->update_sql("topic_pages", $fields, "id = ".get_int('topic_id')."");
	header("Location: admin.php?module=".$module."&page=admin_main");
	exit;
}

if(get('action') == 'delete_topic'){	
	$user_class->permission_end($module, 'admin');
	$query->delete_sql("topic_pages", "id = ".get_int('topic_id')."");
	header("Location: admin.php?module=".$module."&page=admin_main");
	exit;
}
//******************************************************

//********************* add template ************************
if(is_post('add_template') && get_int('tmpl_id') == 0){
	$user_class->permission_end($module, 'admin');
	$fields['topic_id'] = get_int('topic_id');
	$fields['tmpl_title'] = post('tmpl_title');
	$fields['design'] = post_admin('design');
	$fields['repeat'] = post_int('repeat');
	$fields['comments_amount'] = post_int('comments_amount');
	$fields['topic_comment'] = post_int('topic_comment');

	$priority = $query->max_value("topic_template", "priority", "topic_id = ".get_int('topic_id')."");
	$fields['priority'] = ($priority + 1);

	$query->insert_sql("topic_template", $fields);
	header("Location: admin.php?module=".$module."&page=template&topic_id=".get_int('topic_id')."");
	exit;
}

if(is_post('add_template') && get_int('tmpl_id') !== 0){
	$user_class->permission_end($module, 'admin');
	$fields['design'] = post_admin('design');
	$fields['tmpl_title'] = post('tmpl_title');
	$fields['repeat'] = post_int('repeat');
	$fields['comments_amount'] = post_int('comments_amount');
	$fields['topic_comment'] = post_int('topic_comment');

	$query->update_sql("topic_template", $fields, "id = ".get_int('tmpl_id')."");
	header("Location: admin.php?module=".$module."&page=template&topic_id=".get_int('topic_id')."");
	exit;
}

if(get('action') == 'delete_template'){
	$user_class->permission_end($module, 'admin');
	$query->delete_sql("topic_template", "id = ".get_int('tmpl_id')."");
	header("Location: ".$_SERVER['HTTP_REFERER']);
	exit;
}

if(get('updown') !== false){
	$user_class->permission_end($module, 'admin');
	$posotion = $query->select_obj_sql("topic_template", "*", "id = ".get_int("tmpl_id")."");
	$where = "topic_id = '".$posotion->topic_id."' AND topic_comment = '".$posotion->topic_comment."'";
	if(get('updown') == "up"){
		if($query->amount_fields("topic_template", $where." AND priority < ".$posotion->priority."")){
			$query->update_sql("topic_template", array("priority" => "DECREASE"), "id = ".get_int('tmpl_id')."");
			$query->update_sql("topic_template", array("priority" => "INCREASE"), $where." AND priority = ".($posotion->priority-1)." AND id != ".get_int('tmpl_id')."");
		}
	}
	if(get('updown') == "down"){
		if($query->amount_fields("topic_template", $where." AND priority > ".$posotion->priority."")){
			$query->update_sql("topic_template", array("priority" => "INCREASE"), "id = ".get_int('tmpl_id')."");
			$query->update_sql("topic_template", array("priority" => "DECREASE"), $where." AND priority = ".($posotion->priority+1)." AND id != ".get_int('tmpl_id')."");
		}
	}
	header("Location: ".$_SERVER['HTTP_REFERER']);
	exit;
}
//***********************************************************

//*************** add topic category ********************
if(is_post('add_topic_cat') && get_int('cat_id') == 0){
	$user_class->permission_end($module, 'admin');
	$fields['name'] = post('cat_name');
	$fields['topic_id'] = get_int('topic_id');
	$fields['parent_id'] = post_int('categories');
	$query->insert_sql("topic_categories", $fields);
	header("Location: admin.php?module=".$module."&page=categories&topic_id=".get_int('topic_id'));
	exit;
}

if(is_post('add_topic_cat') && get_int('cat_id') !== 0){
	$user_class->permission_end($module, 'admin');
	$fields['name'] = post('cat_name');
	$fields['parent_id'] = post_int('categories');
	$query->update_sql("topic_categories", $fields, "id = ".get_int('cat_id')."");
	header("Location: admin.php?module=".$module."&page=categories&topic_id=".get_int('topic_id'));
	exit;
}

if(get('action') == 'delete_topic_cat'){
	$user_class->permission_end($module, 'admin');
	$query->delete_sql("topic_categories", "id = ".get_int('cat_id')."");
	$query->delete_sql("topic_categories", "parent_id = ".get_int('cat_id')."");
	header("Location: admin.php?module=".$module."&page=categories&topic_id=".get_int('topic_id'));
	exit;
}
//******************************************************

//*************** add topic text ***********************
if(is_post('add_topic_text')){
	$user_class->permission_end($module, 'edit_topic');
	if(post_int('date') !== 0 || post_int('multi_dates') !== 0){
	$fields['lang'] = $lang;
	$fields['topic_id'] = get_int('topic_id');
	$fields['cat_id'] = post_int('category');
	$fields['user_id'] = $_SESSION['login']['user_id'];
	//$add_date = explode("-", post('add_date'));
	//$add_time = explode(":", post('add_time'));
	//$fields['date'] = mktime($add_time[0], $add_time[1], $add_time[2], $add_date[1], $add_date[2], $add_date[0]);
	$fields['date'] = post('date');
	$fields['title'] = post('title');

	$fields['text'] = post_admin('text');
	$fields['short_text'] = post_admin('short_text');
	$fields['start_time'] = post('start_time_h').":".post('start_time_m');
	

	if((int)$topic_conf['topic_need_accept'] == 0){
		$fields['active'] = 1;
	}

	if(get_int('id') == 0){
		$insert_id = $query->insert_sql("topic_text", $fields);
	}
	else{
		$query->update_sql("topic_text", $fields, "id = ".get_int('id')."");
		$insert_id = get_int('id');
	}
	
	//***** multi dates *****
	if((int)$topic_conf['multi_dates'] == 1){
		$multi_dates = explode("\n", "'".str_replace("\n", "'\n'", post('multi_dates'))."'");
		foreach($multi_dates as $date){
			if($date == "") continue;
			$fields_dates['date'] = trim(trim($date, "'"));
			if($query->amount_fields("topic_multi_dates", "topic_text_id = ".(int)$insert_id." AND date = ".$date."") == 0){
				$fields_dates['topic_id'] = get_int('topic_id');
				$fields_dates['topic_text_id'] = $insert_id;
				$query->insert_sql("topic_multi_dates", $fields_dates);
			}
		}
		$query->delete_sql("topic_multi_dates", "topic_text_id = ".(int)$insert_id." AND date NOT IN (".implode(',', $multi_dates).")");
	}
	//***********************
	
	$pic = upload_image("upload/".$module."/gallery", "pic", time(), 'none', $topic_conf['big_img_width'], $topic_conf['big_img_height'], $topic_conf['small_img_width'], $topic_conf['small_img_height']);
	if($pic !== false){
		$pic_fields['pic'] = $pic;
		if(get_int('id') == 0){
			$pic_fields['topic_text_id'] = $insert_id;
       		$pic_fields['default'] = 1;       		
       		$query->insert_sql("topic_gallery", $pic_fields);
		}
       	else{
       		$row = $query->select_obj_sql("topic_gallery", "pic", "topic_text_id = '".get_int('id')."' AND `default` = 1");
       		
       		@unlink("upload/".$module."/gallery/".rawurldecode($row->pic));
       		@unlink("upload/".$module."/gallery/thumb/".rawurldecode($row->pic));
       		$pic_fields['pic'] = $pic;
       		if($query->amount_fields("topic_gallery", "topic_text_id = '".get_int('id')."' AND `default` = 1") == 0){
       			$pic_fields['topic_text_id'] = get_int('id');
       			$pic_fields['default'] = 1;
       			$query->insert_sql("topic_gallery", $pic_fields);
       		}
       		else{
       			$query->update_sql("topic_gallery", $pic_fields, "topic_text_id = '".get_int('id')."' AND `default` = 1");
       		}
       	}
       	
	}
	}
	header("Location: ".DOC_ROOT."index.php?module=".$module."&page=detals&topic_id=".get_int('topic_id')."&cat_id=".post_int('category')."&id=".$insert_id);
	exit;
}

if(get('action') == "accept_topic"){
    $user_class->permission_end($module, 'accept_topic');
	$query->update_sql("topic_text", array("active" => "INVERSE"), "id = ".get_int('topic_text_id')."");
	$row_accept = $query->select_obj_sql("topic_text", "active", "id = ".get_int('topic_text_id')."");
	$accept_img[0] = "accept.gif";
	$accept_img[1] = "decline.gif";
	echo ajax_get("accept_topic_".get_int('topic_text_id'), "<img src=\"images/".$accept_img[$row_accept->active]."\" border=\"0\">\n", $module, "", "action=accept_topic&topic_text_id=".get_int('topic_text_id'));
	exit;
}

if(get('action') == "choice"){
    $user_class->permission_end($module, 'admin');
	$query->update_sql("topic_text", array("rcheuli" => "INVERSE"), "id = ".get_int('topic_text_id')."");
	$row_accept = $query->select_obj_sql("topic_text", "rcheuli", "id = ".get_int('topic_text_id')."");
	$choice_img[0] = "good_red.png";
	$choice_img[1] = "good_green.png";
	echo ajax_get("choice_".get_int('topic_text_id'), "<img src=\"images/".$choice_img[$row_accept->rcheuli]."\" border=\"0\">\n", $module, "", "action=choice&topic_text_id=".get_int('topic_text_id'));
	exit;
}

if(get('action') == 'delete_topic_text'){
	$user_class->permission_end($module, 'admin');
	$query->delete_sql("topic_text", "id = ".get_int('id')."");
	header("Location: ".$_SERVER['HTTP_REFERER']);
	exit;
}
//******************************************************

//******************* configure ************************
if(is_post('edit_config') && get_int('config_id') == 0){
	$user_class->permission_end($module, 'admin');
	$fields['big_img_width'] = post_int('big_img_width');
	$fields['big_img_height'] = post_int('big_img_height');
	$fields['small_img_width'] = post_int('small_img_width');
	$fields['small_img_height'] = post_int('small_img_height');
	$fields['topics_on_page'] = post('topics_on_page');
	$fields['topic_id'] = get_int('topic_id');
	$fields['comment_per_page'] = post_int('comment_per_page');
	$fields['comment_need_accept'] = post_int('comment_need_accept');
	$fields['topic_need_accept'] = post_int('topic_need_accept');
	$fields['more_comments_amount'] = post_int('more_comments_amount');
	$fields['max_topic_text'] = post_int('max_topic_text');
	$fields['max_comment_text'] = post_int('max_comment_text');
	$fields['multi_dates'] = post_int('multi_dates');
	
	$query->insert_sql("topic_config", $fields);
	header("Location: ".$_SERVER['HTTP_REFERER']);
	exit;
}

if(is_post('edit_config') && get_int('config_id') !== 0){
	$user_class->permission_end($module, 'admin');
	$fields['big_img_width'] = post_int('big_img_width');
	$fields['big_img_height'] = post_int('big_img_height');
	$fields['small_img_width'] = post_int('small_img_width');
	$fields['small_img_height'] = post_int('small_img_height');
	$fields['topics_on_page'] = post('topics_on_page');
	$fields['comment_per_page'] = post_int('comment_per_page');
	$fields['comment_need_accept'] = post_int('comment_need_accept');
	$fields['topic_need_accept'] = post_int('topic_need_accept');
	$fields['more_comments_amount'] = post_int('more_comments_amount');
	$fields['max_topic_text'] = post_int('max_topic_text');
	$fields['max_comment_text'] = post_int('max_comment_text');
	$fields['multi_dates'] = post_int('multi_dates');

	$query->update_sql("topic_config", $fields, "id = ".get_int('config_id')."");
	header("Location: ".$_SERVER['HTTP_REFERER']);
	exit;
}
//*********************************************************

//**************** edit comments **************************
if(get('action') == "delete_comment"){
	$user_class->group_perm($module, 'edit_comment');
	comment_delete_karma(get_int('comment_id'));
	$query->delete_sql("topic_comments", "id = ".get_int('comment_id')."");
	$query->update_sql("topic_text", array("comment_amount" => "DECREASE"), "id = ".get_int('id')."");
	header("Location: index.php?module=".$module."&page=detals&topic_id=".get_int('topic_id')."&id=".get_int('id')."");
	exit;
}

if(get('action') == "accept_comment"){    
	$user_class->permission_end($module, 'admin');
	$query->update_sql("topic_comments", array("active" => "INVERSE"), "id = ".get_int('comment_id')."");
	$comment_amount = $query->amount_fields("topic_comments", "topic_text_id = ".get_int('id')." AND active = 1");
	$query->update_sql("topic_text", array("comment_amount" => $comment_amount), "id = ".get_int('id')."");
	$row_accept = $query->select_obj_sql("topic_comments", "active", "id = ".get_int('comment_id')."");
	$accept_img[0] = "accept.gif";
	$accept_img[1] = "decline.gif";
	echo ajax_get("accept_comment_".get_int('comment_id'), "<img src=\"images/".$accept_img[$row_accept->active]."\" border=\"0\">\n", $module, "", "action=accept_comment&comment_id=".get_int('comment_id'));
	exit;
}
//*********************************************************

//***************** attachment *******************************
if(is_post('add_topic_attach')){
	$fields['topic_text_id'] = get_int('id');
	$fields['title'] = post('attach_title');
	$fields['file'] = file_upload("attach_file", time(), "upload/".$module."", "gallery_pic");
	if($fields['file'] !== '')
		$query->insert_sql("topic_files", $fields);
	header("Location: ".$_SERVER['HTTP_REFERER']);
	exit;
}

if(get('action') == 'attach_delete'){
	$row = $query->select_obj_sql("topic_files", "file", "id = '".get('id')."'");

	@unlink("upload/".$module."/".rawurldecode($row->pic));
	$query->delete_sql("topic_files", "id = '".get('id')."'");
	header("Location: ".getenv("HTTP_REFERER")."#gallery");
	exit;
}
//*********************************************************

//***************** gallery *******************************
if(is_post('add_topic_gallery')){
	$fields['topic_text_id'] = get_int('id');
	if(post_int('main_pictupe') == 1){
		$fields['default'] = post_int('main_pictupe');
		$query->update_sql("topic_gallery", array("default" => 0), "topic_text_id = ".get_int('id'));
	}
	$fields['pic'] = upload_image("upload/".$module."/gallery", "gallery_pic", time(), 'none', $topic_conf['big_img_width'], $topic_conf['big_img_height'], $topic_conf['small_img_width'], $topic_conf['small_img_height']);
	if($fields['pic'] !== false)
		$query->insert_sql("topic_gallery", $fields);
	header("Location: ".$_SERVER['HTTP_REFERER']);
	exit;
}

if(get('action') == 'pic_delete'){
	$row = $query->select_obj_sql("topic_gallery", "pic", "id = '".get('id')."'");

	@unlink("upload/".$module."/gallery/".rawurldecode($row->pic));
	@unlink("upload/".$module."/gallery/thumb/".rawurldecode($row->pic));
	$query->delete_sql("topic_gallery", "id = '".get('id')."'");
	header("Location: ".getenv("HTTP_REFERER")."#gallery");
	exit;
}
//*********************************************************

$pages = array('admin_main', 'main', 'template', 'categories', 'pic_view', 'addedit_topic', 'config');
load_page(get('page'), $pages, 'admin_main');
?>