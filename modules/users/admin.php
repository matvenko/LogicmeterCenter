<?php
$user_class->permission_end($module, 'manager');

if(is_post('user_register')){
	$_SESSION['form']['name'] = post('name');
	$_SESSION['form']['surname'] = post('surname');
	$_SESSION['form']['new_password'] = post('new_password');
	$_SESSION['form']['mail'] = post('mail');
	if($query->amount_fields("users", "mail = '".post('mail')."' AND id != ".get_int('user_id'))){
        header("Location: admin.php?module=".$module."&page=add_user&error=mail_exist");
		exit;
    }
	if(get_int('user_id') == 0 && post('new_password') == false){
        header("Location: admin.php?module=".$module."&page=add_user&error=new_password");
		exit;
    }
    
    $fields['name'] = post('name');
	$fields['surname'] = post('surname');
	$fields['mail'] = post('mail');
	$fields_other['tel'] = post('tel');
	$fields_other['position'] = post_int('position');
	
	if(get_int('user_id') == 0){
        $fields['password'] = md5(post('new_password'));
        $user_id = $query->insert_sql("users", $fields);        
        
        $fields_other['user_id'] = $user_id;
        $query->insert_sql("users_other_info", $fields_other);
	}
	else{
		$user_id = get_int('user_id');
		$query->update_sql("users", $fields, "id = ".get_int('user_id'));
		
		$query->update_sql("users_other_info", $fields_other, "user_id = ".get_int('user_id'));
	}    
    
    unset($_SESSION['form']);
    header("Location: admin.php?module=".$module."&page=add_user&user_id=".$user_id);
	exit;
}

if(is_post('chng_pass')){
	$query->update_sql("users", array("password" => md5(post('new_password'))), "id = ".get_int('id')."");
	header("Location: ".$_SERVER['HTTP_REFERER']."");
	exit;
}

if(get('action') == 'delete_user'){
	$query->delete_sql("users", "id = ".get_int('id')."");
    $query->delete_sql("user_groups", "user_id = ".get_int('id')."");
	header("Location: ".$_SERVER['HTTP_REFERER']."");
	exit;
}

if(is_post('add_group')){
	if(post_int('user_group') !== 0){
		$query->insert_sql("user_groups", array("group_id" => post_int('user_group'), "user_id" => get_int('user_id')));
		header("Location: admin.php?module=".$module."&page=add_user&user_id=".get_int('user_id')."");
		exit;
	}
	header("Location: admin.php?module=".$module."&page=add_user&user_id=".get_int('user_id')."");
	exit;
}

if(get('action') == 'user_group_delete'){
	$query->delete_sql("user_groups", "group_id = ".get_int('group_id')." AND user_id = ".get_int('user_id')."");
	header("Location: admin.php?module=".$module."&page=add_user&user_id=".get_int('user_id')."");
	exit;
}

//************* add school *********************
if(is_post('add_school')){
	if(post_int('school') !== 0){
		$query->insert_sql("users_schools", array("school_id" => post_int('school'), "user_id" => get_int('user_id')));
		header("Location: admin.php?module=".$module."&page=add_user&user_id=".get_int('user_id')."");
		exit;
	}
	header("Location: admin.php?module=".$module."&page=add_user&user_id=".get_int('user_id')."");
	exit;
}

if(get('action') == 'user_school_delete'){
	$query->delete_sql("users_schools", "school_id = ".get_int('school_id')." AND user_id = ".get_int('user_id')."");
	header("Location: admin.php?module=".$module."&page=add_user&user_id=".get_int('user_id')."");
	exit;
}
//**********************************************

if(get('action') == 'chng_user_pass'){
	$user_class->permission_end($module, 'admin');
	if(get('new_password') == false){
		echo "<div class=\"error\">"._PASSWORD_EMPTY."</div>";
		exit;
	}
	$fields['password'] = md5(get('new_password'));
	$query->update_sql("users", $fields, "id = ".get_int('user_id')."");
	//***** change chat password *****
	//$query->table_pref = "";
	//$query->update_sql("c_chatoperator", array('vcpassword' => $fields['password']), "operatorid = ".get_int('user_id')."");
	//********************************
	echo _CHNG_PASSWORD_OK;
	exit;
}

if(get('action') == 'chng_user_email'){
	$user_class->permission_end($module, 'admin');
	if(!preg_match("/([[:print:]]+)@([[:print:]]+)\.([[:print:]]+)/", get('new_email'))){
	    echo "<div class=\"error\">"._INCORRECT_EMAIL."</div>";
    	exit;
	}
	if($query->amount_fields("users", "mail = '".get('new_email')."' AND id != ".get_int('user_id')."") !== 0){
		echo "<div class=\"error\">"._EMAIL_EXIST."</div>";
    	exit;
	}
	$query->update_sql("users ", array("mail" => get('new_email')), "id = ".get_int('user_id')."");
	echo _CHNG_EMAIL_OK;
	exit;
}

function user_head(){
	global $module, $user_class;
	$out .= "<table border=\"0\" width=\"100%\">\n";
	$out .= "	<tr>\n";
	$out .= "		<td align=\"center\"><a href=\"admin.php?module=".$module."&page=main\"><b>Main</b></a></td>\n";
	$out .= "		<td align=\"center\"><a href=\"admin.php?module=".$module."&page=transactions\"><b>transactions</b></a></td>\n";
	if($user_class->group_perm($module, "admin")){
		$out .= "		<td align=\"center\"><a href=\"admin.php?module=".$module."&page=add_user\"><b>Add User</b></a></td>\n";
	}
	$out .= "	</tr>\n";
	$out .= "</table>\n";
	return $out;
}
$pages = array (
		'add_user',
		'main',
		'ubani',
		'admin_chng_pass',
		'profile',
		'admin_chng_email',
		'chng_pass',
		'transactions'
);
include("modules/".$module."/files/".load_page(get('page'), $pages, 'main', 0).".php");
?>