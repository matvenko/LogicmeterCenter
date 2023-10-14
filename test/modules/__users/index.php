<?php
$query->change_db($sql_db_l);
require_once($global_conf['logicmeter_src']."modules/math_user/files/functions.php");
$math = new math();
$query->change_db($sql_db);

//****** registration ****************
if(is_post('register')){
    $check_fields = array (
			'parent_name',
			'parent_surname',
			'parent_mail',
			'retype_parent_mail',
			'parent_password',
			'parent_re_passowrd',
			'child_birthdate_year',
			'child_birthdate_month',
			'child_birthdate_day',
    		'child_name',
    		'child_surname' 
	);
	foreach($check_fields as $field_name){
		if(post($field_name) == false){
			echo request_callback("error", _FILL_ALL_FIELDS, "form_field_error", array("field_id" => $field_name, "good_fields" => (array)$good_fields));
			exit;
		}
		$good_fields[] = $field_name;
	}
	
	//**** check mail
	$check_mail = check_mail(post('parent_mail'));
	if($check_mail !== 'ok'){
		echo request_callback("error", $check_mail, "form_field_error", array("field_id" => "parent_mail", "good_fields" => (array)$good_fields));
		exit;
	}
	if(post('parent_mail') !== post('retype_parent_mail')){
		echo request_callback("error", _RETYPE_EMAIL_ERROR, "form_field_error", array("field_id" => "retype_parent_mail", "good_fields" => (array)$good_fields));
		exit;
	}
	$good_fields[] = "parent_mail";
	$good_fields[] = "retype_parent_mail";
	
	//***** check password
	if(mb_strlen(post('parent_password'), 'utf-8') < 6){
		echo request_callback("error", _PASSWORD_ERROR, "form_field_error", array("field_id" => "parent_password", "good_fields" => (array)$good_fields));
		exit;
	}
	$good_fields[] = "parent_password";
	if(post('parent_password') !== post('parent_re_passowrd')){
		echo request_callback("error", _RE_PASSWORD_ERROR, "form_field_error", array("field_id" => "parent_re_passowrd", "good_fields" => (array)$good_fields));
		exit;
	}
	$good_fields[] = "parent_re_passowrd";
	
	//***** check packages ***
	$query->change_db($sql_db_l);
	$children_amount['math'] = $query_l->amount_fields("center_class_children", "disabled = 0 AND subject = 'm' AND year = '".$math->current_lesson_year()."' AND grade = ".post_int('math_grade')." AND dayes_id = ".post_int('math_dayes')." AND hours_id = ".post_int('math_hours'));
	$children_amount['literacy'] = $query_l->amount_fields("center_class_children", "disabled = 0 AND subject = 'l' AND year = '".$math->current_lesson_year()."' AND grade = ".post_int('literacy_grade')." AND dayes_id = ".post_int('literacy_dayes')." AND hours_id = ".post_int('literacy_hours'));
	$query->change_db($sql_db);
	$subjects_ar = array('math', 'literacy');
	foreach ($subjects_ar as $subject){
		if(post_int($subject."_check") == 1 && (post($subject.'_grade') === false ||
										post_int($subject.'_dayes') == 0 || 
										post_int($subject.'_hours') == 0 || 
										post_int('_payment_period') == 0)){
			echo request_callback("error", _CHOOSE_PACKAGE, "form_field_error", array("field_id" => "", "good_fields" => (array)$good_fields));
			exit;
		}
		//**** check class places
		if($children_amount[$subject] >= $global_conf['class_max_children']){
			echo request_callback("error", _CLASS_IS_FULL);
			exit;
		}
	}
	
	

	//** saatebis gadakveta
	if(post_int('math_dayes') == post_int('literacy_dayes') && post_int('math_hours') == post_int('literacy_hours')){
		echo request_callback("error", _LESSON_SAME_TIME, "form_field_error", array("field_id" => "", "good_fields" => (array)$good_fields));
		exit;
	}
	//*************************
		
	//****** terms and rules
	if(post_int('terms_and_rules') == 0){
		echo request_callback("error", _MUST_AGREE_TERMS, "form_field_error", array("field_id" => "terms_and_rules_block", "good_fields" => (array)$good_fields));
		exit;
	}
	
	//***** registration
	$fields_user['name'] = post('parent_name');
	$fields_user['surname'] = post('parent_surname');
	$fields_user['mail'] = post('parent_mail');
	$fields_user['password'] = md5(post('parent_password'));
	$fields_user['add_time'] = current_time();
	$fields_user['register_ip'] = $_SERVER['REMOTE_ADDR'];
	$fields_user['activation_code'] = md5(generatePassword(6, 2));
	$user_id = $query_l->insert_sql("users", $fields_user);
		
	//***** user other info
	$fields_user_other_info['user_id'] = $user_id;
	$fields_user_other_info['tel'] = only_numbers(post('parent_mobile'));
	$fields_user['address'] = post('address');
	$fields_user['zip_code'] = post('zip_code');
	$query_l->insert_sql("users_other_info", $fields_user_other_info);
	
	//**** insert to parents group
	$fields_group['user_id'] = $user_id;
	$fields_group['group_id'] = 9;
	$query_l->insert_sql("user_groups", $fields_group);
	
	//**** insert child
	$fields_child['name'] = post('child_name');
	$fields_child['surname'] = post('child_surname');
	$fields_child['school'] = post('school');
	$fields_child['birthdate'] = post('child_birthdate_year').'-'.post('child_birthdate_month').'-'.post('child_birthdate_day');
	$fields_child['user_id'] = $user_id;
	$fields_child['math'] = 1;
	$child_id = $query_l->insert_sql("math_children", $fields_child);
	
	//**** insert child to class
	foreach ($subjects_ar as $subject){
		$last_year = $query_l->max_value("center_lessons_schedule", "year");
		$fields_class['user_id'] = $user_id;
		$fields_class['year'] = $last_year;
		$fields_class['child_id'] = $child_id;
		$fields_class['subject'] = substr($subject, 0, 1);
		$fields_class['grade'] = post_int($subject.'_grade');
		$fields_class['dayes_id'] = post_int($subject.'_dayes');
		$fields_class['hours_id'] = post_int($subject.'_hours');
		$fields_class['add_time'] = current_time();
		if(post_int($subject."_check") !== 0){
			$query_l->insert_sql("center_class_children", $fields_class);
		}
	}
	
	
	//***** login *******
	$user_info = $user_class->user_info($user_id);
	$user_class->set_login_sessions($user_info);
	
	//**** insert package
	$user_class->current_user_id = $user_id;
	$query->change_db($sql_db_l);
	$math->generate_package_center($child_id, (float)post('_payment_period'), post_int('math_check'), post_int('literacy_check'));
	$query->change_db($sql_db);
    
    $mail_text = $query_l->select_ar_sql("mail_templates", "*", "name = 'NEW_REGISTRANT'");
    
    $global_conf['reply_to'] = 'no-reply@logicmeter.com';
    $activation_link = $global_conf['location']."/index.php?module=users&page=activation&activation_code=".$fields_user['activation_code'];    
    $mail_text['tamplate'] = str_replace("{{activation_link}}", $activation_link, $mail_text['tamplate']);
    $mail_text['tamplate'] = str_replace("{{user_code}}", sprintf("%06s", $user_id), $mail_text['tamplate']);
    send_mail(post('parent_mail'), $mail_text['title'], email_formated_text($mail_text['tamplate']));
	
    echo request_callback("ok", $global_conf['location_logicmeter']."index.php?module=".$module."&page=profile&type=make_payment");
	//echo request_callback("ok_message", "", "auth_to_profile");
	exit;
}
//************************************

//***** change password **************
if(is_post('change_password')){
	if(!$user_class->login_action()) exit;
	if(md5(post('currnet_password')) !== $_SESSION['login']['password']){
		echo request_callback("error", _OLD_PASSWORD_ERROR);
		exit;
	}
	elseif(strlen(post('new_password')) < 6){
		echo request_callback("error", _PASSWORD_ERROR);
		exit;
	}
	elseif(post('new_password') !== post('re_password')){
        echo request_callback("error", _RE_PASSWORD_ERROR);
		exit;
	}
	else{
		$query->update_sql("users", array("password" => md5(post('new_password'))), "id = ".(int)$_SESSION['login']['user_id']."");
		$_SESSION['login']['password'] = md5(post('new_password'));
		echo request_callback("ok_message", _PASSWORD_CHNG_OK);
		exit;
	}
}

//***** set new password **************
if(is_post('set_new_password')){
	$user_id = $user_class->check_restore_code(get('code'));
	if($user_id === false){
		echo request_callback("error", _RESTORE_CODE_ERROR);
		exit;
	}
	elseif(strlen(post('new_password')) < 6){
		echo request_callback("error", _PASSWORD_ERROR);
		exit;
	}
	elseif(post('new_password') !== post('re_password')){
		echo request_callback("error", _RE_PASSWORD_ERROR);
		exit;
	}
	else{
		$query->update_sql("users", array("password" => md5(post('new_password'))), "id = ".(int)$user_id."");
		echo request_callback("ok_message", _PASSWORD_CHNG_OK);
		exit;
	}
}

//**** change personale info ****
if(is_post('change_personal_info')){
	if(!$user_class->login_action()) exit;
	$check_fields = array('name', 'surname', 'mail');
	foreach($check_fields as $field_name){
		if(post($field_name) == false){
			echo request_callback("error", _FILL_ALL_FIELDS, "form_field_error", array("field_id" => $field_name, "good_fields" => (array)$good_fields));
			exit;
		}
		$good_fields[] = $field_name;
	}
	
	//**** check mail
	$check_mail = check_mail(post('mail'), "registered");
	if($check_mail !== 'ok'){
		echo request_callback("error", $check_mail, "form_field_error", array("field_id" => "parent_mail", "good_fields" => (array)$good_fields));
		exit;
	}
	$good_fields[] = "parent_mail";
	
	//***** registration
	$fields_user['name'] = post('name');
	$fields_user['surname'] = post('surname');
	$fields_user['mail'] = post('mail');
	$query->update_sql("users", $fields_user, "id = ".(int)$user_class->current_user_id);
	
	$fields_user_other_info['tel'] = only_numbers(post('tel'));
	$query->update_sql("users_other_info", $fields_user_other_info, "user_id = ".(int)$user_class->current_user_id);
	
	echo request_callback("ok_message", _CHNG_INFO_OK);
	exit;
}

//***** add user image
if(is_post('add_user_image')){
	if(!$user_class->login_action()) exit;

	if(file_name('user_image') == false){
		echo request_callback("error", _CHOOSE_IMAGE);
		exit;
	}
	if(!in_array(file_extention(file_name('user_image')), array('jpg', 'png', 'jpeg'))){
		echo request_callback("error", _INCORRECT_EXTENTION);
		exit;
	}

	$user_info = $user_class->user_info((int)$user_class->current_user_id);

	$upload_dir = "upload/".$module;
	$img_src = upload_image($upload_dir, 'user_image', $user_class->current_user_id."_".time(), "inverse", 800, 600, 90, 90);
	$_SESSION['login']['image'] = $img_src;
	$query->update_sql("users_other_info", array("image" => $img_src), "user_id = ".(int)$user_class->current_user_id);

	crop_image($upload_dir."/thumb/".$img_src, 90);

	@unlink($upload_dir."/".$user_info['image']);
	@unlink($upload_dir."/thumb/".$user_info['image']);

	echo request_callback("ok_message", "", "change_img_src", array("img_id" => "image", "img_src" => $upload_dir."/thumb/".$img_src));
	exit;
}

//***** make payment *******
if(is_post('make_payment')){
	if(!$user_class->login_action()) exit;
	$package_info = $query->select_ar_sql("math_user_packages", "*", "user_id = ".(int)$user_class->current_user_id." AND paid = 0 AND del = 0 AND id = ".get_int('p_id'));
	
	$get_fields['merch_id'] = $global_conf['bog_merchant_id'];
	$get_fields['page_id'] = $global_conf['bog_page_id'];
	$get_fields['back_url_s'] = rawurlencode($global_conf['bog_back_url_success']);
	$get_fields['back_url_f'] = rawurlencode($global_conf['bog_back_url_fail']);
	$get_fields['lang'] = 'KA';
	$get_fields['o.order_n'] = $math->gen_order_n();
	foreach($get_fields as $key => $value){
		$url .= "&".$key."=".$value;
	}
	
	$package_info['o.order_n'] = $get_fields['o.order_n'];
	$math->update_payment_amount($package_info);
	
	header("Location: ".$global_conf['bog_url']."?".$url);
	exit;
}

//***** add/change child ******
if(is_post('add_child')){
	if(!$user_class->login_action()) exit;
	$check_fields = array('name', 'surname', 'birthdate_year', 'birthdate_month', 'birthdate_day');
	foreach($check_fields as $field_name){
		if(post($field_name) == false){
			echo request_callback("error", _FILL_ALL_FIELDS, "form_field_error", array("field_id" => $field_name, "good_fields" => (array)$good_fields));
			exit;
		}
		$good_fields[] = $field_name;
	}
	
	$fields_children['name'] = post('name');
	$fields_children['surname'] = post('surname');
	$fields_children['birthdate'] = post('birthdate_year').'-'.post('birthdate_month').'-'.post('birthdate_day');
	
	//****** if allready exists
	$query->where_vars['name'] = $fields_children['name'];
	$query->where_vars['surname'] = $fields_children['surname'];
	$query->where_vars['birthdate'] = $fields_children['birthdate'];
	$where = "user_id = ".(int)$user_class->current_user_id." AND name = '{{name}}' AND surname = '{{surname}}' AND birthdate = '{{birthdate}}'";
	$where .= get_int('child_id') == 0 ? "" : " AND id != ".get_int('child_id');
	if($query->amount_fields("math_children", $where) !== 0){
		echo request_callback("error_message", _CHILD_ALLREADY_EXISTS, "enable_field", array("field_id" => "submit"));
		exit;
	}
	
	//**** add child
	if(get_int('child_id') == 0){
		$fields_children['user_id'] = $user_class->current_user_id;
		$child_id = $query->insert_sql("math_children", $fields_children);

		//*** package info
		$last_package_info = $query->select_ar_sql("math_user_packages", "*", "user_id = ".(int)$user_class->current_user_id." AND del = 0", "id DESC", "0, 1");
		$period = (int)$last_package_info['paid'] == 1 ? 0 : $last_package_info['period'];	
		$math->generate_package($period, $last_package_info['math'], $last_package_info['literacy']);
		//*********
	}
	else{
		$query->update_sql("math_children", $fields_children, "id = ".get_int('child_id')." AND user_id = ".(int)$user_class->current_user_id);
	}
	echo request_callback("ok", "index.php?module=".$module."&page=profile&type=children");
	exit;
}

//****** change package
if(is_post('change_package')){
	if(!$user_class->login_action()) exit;

	$last_package = $query->select_ar_sql("math_user_packages", "*", "user_id = ".(int)$user_class->current_user_id." AND del = 0", "id DESC", "0, 1");
	
	if((int)$last_package['paid'] == 1){
		$package_math = (int)$last_package['math'] == 1 ? 1 : post_int('math');
		$package_literacy = (int)$last_package['literacy'] == 1 ? 1 : post_int('literacy');
	}
	else{
		$package_math = post_int('math');
		$package_literacy = post_int('literacy');
	}
	
	//*** tu araferi shecvlila
	if((int)$last_package['paid'] == 1 && post_int('period') == 0 && (int)$last_package['math'] == $package_math && (int)$last_package['literacy'] == $package_literacy){
		echo request_callback("error", _NOTHING_CHANGED);
		exit;
	}
	if((int)$last_package['paid'] == 0 && post_int('period') == (int)$last_package['period'] && (int)$last_package['math'] == $package_math && (int)$last_package['literacy'] == $package_literacy){
		echo request_callback("error", _NOTHING_CHANGED);
		exit;
	}
	
	//**** gadauxdelis shecvlisas periodi tu 0_ia
	if((int)$last_package['paid'] == 0 && post_int('period') == 0){
		echo request_callback("error", _CHOOSE_PERIOD);
		exit;
	}
		
	$math->generate_package(post_int('period'), $package_math, $package_literacy);
	
	echo request_callback("ok", "index.php?module=".$module."&page=profile&type=children");
	exit;
}

//***** add child image
if(is_post('add_child_image')){
	if(!$user_class->login_action()) exit;
	
	if(file_name('child_image') == false){
		echo request_callback("error", _CHOOSE_IMAGE);
		exit;
	}
	if(!in_array(file_extention(file_name('child_image')), array('jpg', 'png', 'jpeg'))){
		echo request_callback("error", _INCORRECT_EXTENTION);
		exit;
	}
	
	$child_info = $math->child_info(get_int('child_id'));
	
	$upload_dir = "upload/".$module;
	$img_src = upload_image($upload_dir, 'child_image', $user_class->current_user_id."_".time(), "inverse", 800, 600, 90, 90);
	
	$query->update_sql("math_children", array("image" => $img_src), "user_id = ".(int)$user_class->current_user_id." AND id = ".get_int('child_id'));
	
	crop_image($upload_dir."/thumb/".$img_src, 90);
	
	@unlink($upload_dir."/".$child_info['image_src']);
	@unlink($upload_dir."/thumb/".$child_info['image_src']);
	
	echo request_callback("ok_message", "", "change_img_src", array("img_id" => "image", "img_src" => $upload_dir."/thumb/".$img_src));
	exit;
}

//***** deactivate child
if(get('action') == "deactivate_child"){
	if(!$user_class->login_action()) exit;
	
	$child_info = $math->child_info(get_int('child_id'));

	//**** decline last child disable
	if((int)$child_info['disabled'] == 0 && $query->amount_fields("math_children", "user_id = ".(int)$user_class->current_user_id." AND disabled = 0") == 1){
		echo _CANNOT_DISABLE_LAST_CHILD;
		exit;
	}
	
	//**** decline paid child disable
	if($child_info['paid_to'] >= current_date()){
		echo _CANNOT_DISABLE_PAID_CHILD;
		exit;
	}
	
	if($child_info !== false){
		$disable = (int)$child_info['disabled'] == 0 ? 1 : 0;
		$aff_rows = $query->update_sql("math_children", array("disabled" => $disable), "user_id = ".(int)$user_class->current_user_id." AND id = ".get_int('child_id'));
		
		if((int)$aff_rows !== 0){
			//**** update package
			$last_package_info = $query->select_ar_sql("math_user_packages", "*", "user_id = ".(int)$user_class->current_user_id." AND del = 0", "paid ASC, id DESC", "0, 1");
			$period = (int)$last_package_info['paid'] == 1 ? 0 : $last_package_info['period'];
    		$math->generate_package($period, $last_package_info['math'], $last_package_info['literacy']);
		}
	}
	echo "ok";
	exit;
}

//***** restore password
if(is_post('restore_password')){
	$user_info = $user_class->user_info_by_mail(post('mail'));
	
	if((int)$user_info['id'] == 0){
		$data_out['success'] = "error";
		$data_out['error_message'] = _USER_NOT_FOUND;
	}
	elseif(post_int('step') == 1){
		$child = $query->select_ar_sql("math_children", "id, name, surname", "user_id = ".(int)$user_info['id']." AND disabled = 0", "id ASC", "0,1");
		$data_out['success'] = "ok_message";
		$data_out['child_id'] = $child['id'];
		$data_out['child_name'] = $child['name']." ".$child['surname']._S;
	}
	elseif(post_int('step') == 2){
		$child = $query->select_ar_sql("math_children", "id, birthdate", "user_id = ".(int)$user_info['id']." AND disabled = 0", "id ASC", "0,1");
		if(post('birthdate_year')."-".sprintf("% 02s", post('birthdate_month'))."-".sprintf("% 02s", post('birthdate_day')) !== $child['birthdate']){
			$data_out['success'] = "error";
			$data_out['error_message'] = _CHILD_BIRTHDATE_INCORRECT;
		}
		else{
			$restore_code = md5($user_info['id'].rand().time());
			$fields['restore_password_code'] = $restore_code;
			$fields['restore_password_code_time'] = time();
			$query->update_sql("users_other_info", $fields, "user_id = ".(int)$user_info['id']);
			
			$restore_link = $global_conf['location']."/index.php?module=users&page=set_new_password&code=".$restore_code;
			$send_text = $query->select_ar_sql("mail_templates", "*", "name = 'RESTORE_PASSWORD'");
			
			$send_text['tamplate'] = str_replace("{{restore_link}}", $restore_link, $send_text['tamplate']);
			
			send_mail(post('mail'), $send_text['title'], $send_text['tamplate']);
			
			$data_out['success'] = "ok_message";
			$data_out['message'] = _RESTORE_INSTRUCTION_SENT;
		}
	}

	echo json_encode($data_out);
	exit;
}

$pages = array (
		'register',
		'profile',
		'login',
		'logout',
		'chng_pass',
		'restore_password',
		'set_new_password',
		'login_main_page',
		'login_default_page',
		'auth_main_page',
		'change_package',
		'activation',
		'choose_class'
);
load_page(get('page'), $pages);
?>