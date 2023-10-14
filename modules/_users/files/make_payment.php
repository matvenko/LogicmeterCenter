<?php
$user_class->permission_end($module, 'user');
global $out;

$user_class->check_user_activation_redirect();

$replace_fields['module'] = $module;
$replace_fields['_PACKAGE'] = _PACKAGE;
$replace_fields['_CHILDREN'] = _CHILDREN;
$replace_fields['_PERIOD'] = _PERIOD;
$replace_fields['_PAYMENT_AMOUNT'] = _PAYMENT_AMOUNT;
$replace_fields['_PAYMENT'] = _PAYMENT;

$package_info = $query->select_ar_sql("math_user_packages", "*", "user_id = ".(int)$user_class->current_user_id." AND paid = 0 AND del = 0");

$replace_fields['period'] = $package_info['period'] == 12 ? 1 : $package_info['period'];
$replace_fields['_MONTH'] = $package_info['period'] == 12 ? _YEAR : _MONTH;
$replace_fields['package_id'] = $package_info['id'];

//**** payment amount
$package_payment_no_discount = $math->package_payment();
$package_payment_with_discount = $math->package_payment(0, $user_class->current_user_email);
if((int)$package_payment_no_discount !== (int)$package_payment_with_discount){
	$replace_fields['payment_amount'] = "";
	$replace_fields['payment_amount_no_discount'] = $package_payment_no_discount;
	$replace_fields['payment_amount_with_discount'] = $package_payment_with_discount;
}
else{
	$replace_fields['payment_amount'] = $package_payment_no_discount;
	$replace_fields['payment_amount_no_discount'] = "";
	$replace_fields['payment_amount_with_discount'] = "";
}

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

$out = $templates->gen_module_html($replace_fields, "make_payment");