<?php
$user_class->permission_end($module, 'admin');

if(get('custom_value') == 'true'){
	if($query->amount_fields("group_custom_permission", "group_id = ".get_int('group_id')." AND module_id = ".get_int('module_id')." AND custom_id = ".get_int('custom_id')."") == 0){		$fields['group_id'] = get_int('group_id');
		$fields['module_id'] = get_int('module_id');
		$fields['custom_id'] = get_int('custom_id');
		$query->insert_sql("group_custom_permission", $fields);	}
}
else{	
	$query->delete_sql("group_custom_permission", "group_id = ".get_int('group_id')." AND module_id = ".get_int('module_id')." AND custom_id = ".get_int('custom_id')."");
}

?>