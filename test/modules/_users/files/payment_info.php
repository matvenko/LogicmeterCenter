<?php
$user_class->permission_end($module, 'user');
global $out;

if((int)$user_class->current_child_id !== 0){
	header("Location: index.php");
	exit;
}

$replace_fields['module'] = $module;
$replace_fields['_MONEY'] = _MONEY;
$replace_fields['_PAYMENT_TIME'] = _PAYMENT_TIME;
$replace_fields['_INVOICE'] = _DETALS;

$transactions_tmpl = $templates->split_template("list", "payment_info");
$result = $query->select_sql("payment_transactions", "*", "user_id = ".(int)$user_class->current_user_id, "add_time DESC");
while($row = mysql_fetch_assoc($result)){

	$transactions_fields = $row;
	$transactions_fields['_INVOICE'] = _DETALS;
	
	$templates->gen_loop_html($transactions_fields, $transactions_tmpl);
}

$out = $templates->gen_module_html($replace_fields, "payment_info");
