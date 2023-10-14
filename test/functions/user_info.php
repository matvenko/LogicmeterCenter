<?php
class user_class {
	var $current_user_id = 0;
	var $current_user_email = 0;
	var $current_child_id = 0;
	var $choosed_profile = 0;

	function __construct() {
		$this->login_info = $_SESSION ['login'];
		$this->current_user_id = (int)$_SESSION['login']['user_id'];
		$this->current_user_email = $_SESSION['login']['username'];
		$this->current_child_id = (int)$_SESSION['login']['child_id'];
		$this->choosed_profile = (int)$_SESSION['login']['choosed_profile'];
	}
	
	function set_login_sessions($parameters){
		$_SESSION['login']['user_id'] = $parameters['id'];
		$_SESSION['login']['name'] = $parameters['name'];
		$_SESSION['login']['surname'] = $parameters['surname'];
		$_SESSION['login']['username'] = $parameters['mail'];
		$_SESSION['login']['password'] = $parameters['password'];
		$_SESSION['login']['name_surname'] = $parameters['name']." ".$parameters['surname'];
		$_SESSION['login']['image'] = $parameters['image'];
	}

	function login_action() {
		if (isset($_SESSION ['login'])) {
			return true;
		} else{
			return false;
		}
	}

	function login_action_end() {
		if (! $this->login_action()) {
			@header("Location: index.php?module=users&page=login&referer=".base64_encode($_SERVER['REQUEST_URI']));
			echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0; URL=index.php?module=users&page=login&referer=".base64_encode($_SERVER['REQUEST_URI'])."\"> ";
			exit();
		}
	}

	function user_permission_end() {
		if ($this->login_action()) {
			return true;
		} else {
			@header("Location: index.php?module=users&page=login&referer=".base64_encode($_SERVER['REQUEST_URI']));
			echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0; URL=index.php?module=users&page=login&referer=".base64_encode($_SERVER['REQUEST_URI'])."\"> ";
			exit();
		}
	}

	function group_name($id) {
		global $query_l;
		$row = $query_l->select_obj_sql("group", "name", "id = '" . (int)$id . "'");
		if ($row->name !== false) {
			return $row->name;
		}
	}

	function user_groups($id = 0) {
		global $query_l;
		/* if (! $this->login_action()) {
			$groups [] = 2;
			return $groups;
		} */
		if ($id == 0) {
			$id = $this->current_user_id;
		}
		$result = $query_l->select_sql("user_groups", "group_id", "user_id = '" . $id . "'");
		while ( $row = $query_l->obj($result) ) {
			$groups [] = $row->group_id;
		}
		if (! is_array($groups)) {
			$groups [] = 2;
		}
		return $groups;
	}

	function user_admin() {
		global $query_l;
		$amount = $query_l->amount_fields("user_groups", "user_id = " . (int)$_SESSION ['login'] ['user_id'] . " AND group_id = 1");
		if ($amount == 1) {
			return 'ADMIN';
		} else {
			return 'NO_ADMIN';
		}
	}

	function group_perm($module, $type, $check_var = 0) {
		global $query_l;
		$this->get_user_permission_cache();
		$row_module = $query_l->select_obj_sql("modules", "*", "module = '" . $module . "'");
		//********** custom ************
		if ($row_module->get_check_var == "") {
			$check_var_value = $row_module->check_var;
		} else {
			$check_var_value = $row_module->get_check_var;
		}
		
		if ($check_var == 0) {
			$check_var_value = get_int($check_var);
		} else {
			$check_var_value = $check_var;
		}
		
		//******************************
		if ($type == 'all' && is_array($_SESSION ['permission'] [$module])) {
			return true;
		} elseif ($_SESSION ['permission'] [$module] [$type] == "OK" || $_SESSION ['permission'] [$module] [$check_var_value] [$type] == 'OK' || $_SESSION ['permission'] [$module] ['all'] [$type] == 'OK' || $this->user_admin() == 'ADMIN') {
			return true;
		} else {
			return false;
		}
	}

	function get_user_permission_cache() {
		global $query_l;
		
		$table_info = $query_l->table_info("group_permissions");
		$table_info_user_group = $query_l->table_info("user_groups"); 
		$table_custom_info = $query_l->table_info("group_custom_permission");
		
		$user_info = array ();
		if ((int)$_SESSION ['login'] ['user_id'] !== 0) {
			$user_info = $query_l->select_ar_sql("users_permissions", "id, permissions, add_time", "user_id = " . (int)$_SESSION ['login'] ['user_id'] . "");
		}
		//**** set time to server time
		$dateTime = new DateTime($user_info['add_time']);
		$dateTime->setTimeZone(new DateTimeZone(ini_get('date.timezone')));
		
		$user_info['add_time'] = $dateTime->format('Y-m-d H:i:s');
		if ((int)$_SESSION ['login'] ['user_id'] !== 0 && ((int)$user_info ['id'] == 0 || $table_info_user_group ['Update_time'] > $user_info ['add_time'] || $table_info ['Update_time'] > $user_info ['add_time'] || $table_custom_info ['Update_time'] > $user_info ['add_time'])) {
			//************* set user permission cach **************
			unset($_SESSION ['permission']);
			$result_module = $query_l->select_sql("modules");
			$user_grougs = $this->user_groups();
			while ( $row_module = $query_l->obj($result_module) ) {
				$result_type = $query_l->select_sql("permissions", "name", "module_id = " . (int)$row_module->id . "");
				while ( $row_type = $query_l->obj($result_type) ) {
					$where = '';
					$amount = 0;
					$permition_type = " AND permition_type = '" . $row_type->name . "'";
					
					foreach ( $user_grougs as $group ) {
						$where .= "group_id = " . (int)$group . " OR ";
					}
					$where = rtrim($where, 'OR ');
					$amount = $query_l->amount_fields("group_permissions", "(" . $where . ") AND module_id = " . (int)$row_module->id . " " . $permition_type);
					if ($row_module->custom_permission == 1) {
						$amount = $query_l->amount_fields("group_custom_permission", "(" . $where . ") " . $permition_type . " AND module_id = " . (int)$row_module->id . " AND custom_id = " . get_int($row_module->check_var) . "");
						$amount_all = $query_l->amount_fields("group_custom_permission", "(" . $where . ") " . $permition_type . " AND module_id = " . (int)$row_module->id . " AND custom_id = 'all'");
						if ($amount >= 1 || $this->user_admin() == 'ADMIN') {
							$result_custom = $query_l->select_sql("group_custom_permission", "*", "module_id = " . (int)$row_module->id . " AND (" . $where . ")");
							while ( $row_custom = $query_l->obj($result_custom) ) {
								$_SESSION ['permission'] [$row_module->module] [$row_custom->custom_id] [$row_custom->permition_type] = "OK";
							}
						}
						if ($amount_all >= 1 || $this->user_admin() == 'ADMIN') {
							$_SESSION ['permission'] [$row_module->module] ['all'] [$row_type->name] = "OK";
						}
					} else {
						$amount = $query_l->amount_fields("group_permissions", "(" . $where . ") AND module_id = " . (int)$row_module->id . " " . $permition_type);
						if ($amount >= 1 || $this->user_admin() == 'ADMIN') {
							$_SESSION ['permission'] [$row_module->module] [$row_type->name] = "OK";
						}
					}
				}
			}
			//die(''.$_SESSION['permission'].'');
			$fields ['user_id'] = $_SESSION ['login'] ['user_id'];
			$fields ['permissions'] = serialize($_SESSION ['permission']);
			$fields ['add_time'] = date("Y-m-d H:i:s");
			if ((int)$user_info ['id'] == 0) {
				$query_l->insert_sql("users_permissions", $fields);
			} else {
				$query_l->update_sql("users_permissions", $fields, "id = " . (int)$user_info ['id'] . "");
			}
			//*****************************************************
		} else {
			//************ get user permissions cach **************
			//if(!isset($_SESSION['permission']))
			$_SESSION ['permission'] = unserialize($user_info ['permissions']);
			//*****************************************************
		}
	}

	function permission_end($module, $type) {
		if ($this->group_perm($module, $type) || $this->user_admin() == 'ADMIN') {
			return true;
		} else {
			@header("Location: index.php?module=users&page=login&referer=".base64_encode($_SERVER['REQUEST_URI']));
			echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0; URL=index.php?module=users&page=login&referer=".base64_encode($_SERVER['REQUEST_URI'])."\"> ";
			exit();
		}
	}

	function user_info($user_id) {
		global $query_l;
		$user_info = $query_l->select_ar_sql("users_info", "*", "id = " . (int)$user_id . "");
		$user_info['user_id'] = $user_info ['id'];
		$user_info['full_name'] = $user_info['name']." ".$user_info['surname'];
		$user_info['profile_name'] = $_SESSION['login']['name'];
		$user_info['profile_surname'] = $_SESSION['login']['surname'];
		$user_info['profile_full_name'] = $_SESSION['login']['name']." ".$_SESSION['login']['surname'];
		
		$user_info['profile_image_src'] = $_SESSION['login']['image'];
		$profile_image = "upload/users/thumb/".$_SESSION['login']['image'];
		$user_info['profile_image'] = is_file($profile_image) ? $profile_image : "images/user_no_image.jpg";
		$user_image = "upload/users/thumb/".$user_info['image'];
		$user_info['user_image'] = is_file($user_image) ? $user_image : "images/user_no_image.jpg";
		return $user_info;
	}
	
	function user_info_by_mail($mail){
		global $query_l;
		
		$query_l->where_vars['mail'] = $mail;
		$user_info = $query_l->select_ar_sql("users", "*", "mail = '{{mail}}'");
		
		return $user_info;
	}
	
	function check_restore_code($code){
		global $query_l;
		
		$query_l->where_vars['restore_password_code'] = $code;
		$code_info = $query_l->select_ar_sql("users_other_info", "user_id, restore_password_code_time", "restore_password_code = '{{restore_password_code}}'");
		
		return (int)$code_info['restore_password_code_time'] < time() - 3600 ? false : $code_info['user_id'];
	}
	
	function check_user_activation(){
		global $query_l;
		
		return $query_l->record_exist("users", "id = ".(int)$this->current_user_id." AND status = 1");
	}
	
	function check_user_activation_redirect(){
		global $query_l;
	
		if($this->check_user_activation() == false && !strpos($_SERVER['REQUEST_URI'], "module=users&page=activation")){
			header("Location: index.php?module=users&page=activation");
			exit;
		}
	}
}
