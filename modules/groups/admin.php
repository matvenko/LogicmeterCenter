<?php
$user_class->permission_end($module, 'admin');

function permission($id, $group_id, $custom_id = "", $type){
	global $query;
	if($custom_id == "" && $query->amount_fields("group_permissions", "module_id = '".$id."' AND group_id = '".$group_id."' AND permition_type = '".$type."'") !== 0){
		$checked = 'checked';
	}
	elseif($custom_id !== "" && $query->amount_fields("group_custom_permission", "custom_id = '".$custom_id."' AND module_id = '".$id."' AND group_id = '".$group_id."' AND permition_type = '".$type."'") !== 0){
		$checked = 'checked';
	}
	else $checked = '';
	return $checked;
}

if(is_post('add_group')){
	if(post('group_name') !== false){
		$query->insert_sql("group", array("name" => post('group_name'), "description" => post('group_desc'), "karma" => post('karma')));
        header("Location: admin.php?module=".$module."");
        exit;
	}
    header("Location: admin.php?module=".$module."");
    exit;
}

if(is_post('edit_group')){
	if(post('group_name') !== false){
		$query->update_sql("group", array("name" => post('group_name'), "description" => post('group_desc'), "karma" => post('karma')), "id = ".get_int('id')."");
        header("Location: admin.php?module=".$module."");
        exit;
	}
    header("Location: admin.php?module=".$module."");
    exit;
}

if(get('action') == 'delete_group'){
        $query->delete_sql("group", "id = '".get_int('id')."'");
        header("Location: admin.php?module=".$module."");
        exit;
}

if(is_post('edit_group_per')){
	$result_module = $query->select_sql("modules");
	while($row = mysql_fetch_object($result_module)){
		$result_per = $query->select_sql("permissions", "*", 1, "id ASC");
		unset($fields);
		while($row_per = mysql_fetch_object($result_per)){
			$where = "module_id = ".$row->id." AND group_id = ".get_int('id')." AND permition_type = '".$row_per->name."'";
        	$fields['group_id'] = get_int('id');
        	$fields['module_id'] = $row->id;
        	$fields['permition_type'] = $row_per->name;
        	if((int)$row->custom_permission == 0){        		
        		$table = "group_permissions";        	
        	}
        	elseif((int)$row->custom_permission == 1 && post('custom_'.$row->id) !== "0"){        		
        		$table = "group_custom_permission";
        		$where .= " AND custom_id = '".post('custom_'.$row->id)."'";
        		$fields['custom_id'] = post('custom_'.$row->id);        	
        	}
        	else{        		
        		continue;        	
        	}

        	if(post($row->id."_".$row_per->name) == 1){
        		if($query->amount_fields($table, $where) == 0){
        			$query->insert_sql($table, $fields);
        		}
        	}
        	else{
        		$query->delete_sql($table, $where);
        	}
		}
	}
    header("Location: admin.php?module=".$module."&page=permission&id=".get_int('id')."");
    exit;
}

if(get('action') == 'user_group_delete'){
	$query->delete_sql("user_groups", "group_id = ".get_int('group_id')." AND user_id = ".get_int('user_id')."");
	header("Location: admin.php?module=".$module."&page=permission&id=".get_int('group_id')."");
	exit;
}


$pages = array('permission', 'main', 'custom_permission', 'set_custom');
load_page($_GET['page'], $pages);

?>

