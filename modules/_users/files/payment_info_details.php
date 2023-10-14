<?php
$user_class->permission_end($module, 'user');
global $out;

$replace_fields['module'] = $module;
$replace_fields['_PACKAGE'] = _PACKAGE;
$replace_fields['_CHILDREN'] = _CHILDREN;
$replace_fields['_PERIOD'] = _PERIOD;
$replace_fields['_PAID_AMOUNT'] = _PAID_AMOUNT;
$replace_fields['_PAYMENT_TIME'] = _PAYMENT_TIME;
$replace_fields['_ORDER_N'] = _ORDER_N;
$replace_fields['_PAYMENT'] = _PAYMENT;

$query->where_vars['order_n'] = get('order_n');
$package_info = $query->select_ar_sql("math_user_packages", "*", "user_id = ".(int)$user_class->current_user_id." AND order_n = '{{order_n}}'");

$replace_fields['period'] = $package_info['period'];
$replace_fields['_MONTH'] = $package_info['period'] == 12 ? _YEAR : _MONTH;
$replace_fields['payment_amount'] = $package_info['payment_amount'];
$replace_fields['package_id'] = $package_info['id'];
$replace_fields['order_n'] = $package_info['order_n'];
$replace_fields['add_time'] = $package_info['add_time'];

//**** packages
if((int)$package_info['math'] == 1) {
	$packages[] = _MATH;
}
if((int)$package_info['literacy'] == 1) {
	$packages[] = _LITERACY;
}
$replace_fields['package'] = implode(", ", (array)$packages);

$replace_fields['paid_to'] = current_date(strtotime("+".$package_info['period']." month", $math->package_payment_time_period($package_info['parent_id'], $package_info['id'])));

//**** children
$result_children = $query->select_sql("math_user_packages_children", "child_id", "del = 0 AND package_id = ".(int)$package_info['id']);
while($row_children = mysql_fetch_assoc($result_children)){
	$child_info = $math->child_info($row_children['child_id']);
	$children[] = $child_info['name']." ".$child_info['surname'];
}
$replace_fields['children'] = implode(", ", (array)$children);

$out = $templates->gen_module_html($replace_fields, "payment_info_details");