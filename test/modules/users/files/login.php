<?php
global $out;
//**** login ******
if(is_post('login')){
	//$query = new sql_func();
	if($query_l->amount_fields("users", "mail = '".post('username')."' AND password = '".md5(post('password'))."' AND `status` != 2") == 1){
		$user_info = $query_l->select_ar_sql("users", "*", "mail = '".post('username')."' AND password = '".md5(post('password'))."' AND `status` != 2");
		@session_start();
		session_unset();
		$user_class->set_login_sessions($user_info);
		
		$query_l->update_sql("users", array("last_login" => time(), 'last_login_ip' => $_SERVER['REMOTE_ADDR']), "id = ".(int)$user_info['id']);
		
		/* if($user_class->group_perm("math", "all")){
			header("Location: admin.php?module=math");
		}
		elseif($user_class->group_perm("literacy", "all")){
			header("Location: admin.php?module=literacy");
		}
		else{
			header("Location: index.php");
		} */
		if(post('login_type') == 'ajax'){
			echo 'ok';
		}
		else{
			header("Location: index.php");
		}
        exit;
	}
	else{
		if(post('login_type') == 'ajax'){
			echo _LOGIN_ERROR;
		}
		else{
			header("Location: index.php?module=".$module."&page=login&error=login_error");
		}
        exit;
	}
}

//****** choose profile ******
if(is_post('choose_profile')){
	if(post_int('child_id') !== 0){
		$child_info = $math->child_info(post_int('child_id'));
		if($child_info !== false){
			unset($_SESSION['math']);
			unset($_SESSION['literacy']);
			$_SESSION['login']['child_id'] = post_int('child_id');
			$_SESSION['login']['name'] = $child_info['name'];
			$_SESSION['login']['surname'] = $child_info['surname'];
			$_SESSION['login']['image'] = $child_info['image_src'];
			$_SESSION['login']['choosed_profile'] = 1;
		}
	}
	else{
		$user_info = $user_class->user_info($user_class->current_user_id);
		unset($_SESSION['math']);
		unset($_SESSION['literacy']);
		$_SESSION['login']['name'] = $user_info['name'];
		$_SESSION['login']['surname'] = $user_info['surname'];
		$_SESSION['login']['image'] = $user_info['image'];
		$_SESSION['login']['child_id'] = 0;
		$_SESSION['login']['choosed_profile'] = 1;
	}
	echo "ok";
	exit;
}

if($user_class->login_action()){
	header("Location: ".(get('referer') == false ? "index.php" : str_replace("/", "", base64_decode(get('referer')))));
	exit;
}

if(get('error') !== false){
	$replace_fields['error_message'] = _LOGIN_ERROR;
}

$replace_fields['_LOGIN_TO_SYSTEM'] = _LOGIN_TO_SYSTEM;
$replace_fields['_REGISTRATION'] = _REGISTRATION;
$replace_fields['_USERNAME'] = _USERNAME;
$replace_fields['_PASSWORD'] = _PASSWORD;
$replace_fields['_RESTORE_PASSWORD'] = _RESTORE_PASSWORD;
$replace_fields['location_logicmeter'] = $global_conf['location_logicmeter'];
$replace_fields['_LOGIN'] = _LOGIN;
$replace_fields['lang'] = $lang;
$replace_fields['module'] = $module;

if($user_class->user_admin() == "ADMIN"){
	$login_out .= "	<div style=\"line-height: 20px; width: 190px; text-align: left;\">
						<a href=\"admin.php\" style=\"color: #000;\">"._ADMIN."</a><BR>
						<a href=\"index.php?module=users&page=messages\">"._PROFILE."</a><BR>
						<a href=\"logout.php\" style=\"color: #000;\">"._LOGOUT."</a>
					</div> \n";
	$login_out .= $tmpl['admin'];
}
elseif($user_class->login_action()){
	$login_out .= " <div style=\"line-height: 20px; width: 190px; text-align: left; color: #000;font-size: 12px\">
						<a href=\"index.php?module=users&page=profile\">".$_SESSION['login']['username']."</a><BR>
						<a href=\"index.php?module=users&page=messages\">"._PROFILE."</a><BR>
						<a href=\"logout.php\" style=\"color: #000;\">"._LOGOUT."</a></div> \n";
	$login_out .= $tmpl['admin'];
}
else{
	$login_out .= "<form method=\"post\" action=\"index.php?module=users&page=login\">
					<table border=\"0\" width=\"100%\" id=\"table1\" height=\"100%\">
						<tr>
							<td align=\"center\"><div class=\"error\">".$error."</div>
								<table border=\"0\" width=\"300\" id=\"table2\">
									<tr>
										<td align=\"left\" class=\"table_head\" colspan=\"2\"><b>Sign in</b></td>
									</tr>
									<tr>
										<td align=\"right\" class=\"table_td1\" width=\"42%\"><b>Username:</b></td>
										<td width=\"56%\" class=\"admin_table_td1\">
											<input type=\"text\" name=\"username\" size=\"20\"></td>
									</tr>
									<tr>
										<td align=\"right\" class=\"table_td1\" width=\"42%\"><b>Password:</b></td>
										<td width=\"56%\" class=\"admin_table_td1\">
											<input type=\"password\" name=\"password\" size=\"20\"></td>
									</tr>
									<tr>
										<td align=\"center\" class=\"table_head\" colspan=\"2\"><input
											type=\"submit\" value=\"Login\" name=\"login\"></td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</form>\n";
}
$login_out .="</div>
	</div>	
</div>\n";

$replace_fields['module'] = $module;


$out .= $templates->gen_module_html($replace_fields, "login");



?>
