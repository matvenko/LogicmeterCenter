<?php
$user_class->permission_end($module, 'user');
global $out;

if((int)$user_class->current_child_id == 0){
	$templates->module_ignore_fields[] = "parent";
}

$birthdate_years = range(date("Y") - 3, date("Y") - 20);
$birthdate_years = array_combine($birthdate_years, $birthdate_years);
$birthdate_monthes = array(1 => _JANUARY, 2 => _FABRUARY, 3 => _MARCH, 4 => _APRIL, 5 => _MAY, 6 => _JUNE, 7 => _JULY, 8 => _AUGUST, 9 => _SEPTEMBER, 10 => _OCTOMBER, 11 => _NOVEMBER, 12 => _DECEMBER);
$birthdate_monthes[0] = _MONTH;
$replace_fields['parent_birthdate_month'] = input_form("parent_birthdate_month", "select", $edit_value, $birthdate_monthes);
$birthdate_dayes = range(0, 31);
$birthdate_dayes[0] = _DAY;
$replace_fields['_NAME'] = _NAME;
$replace_fields['_SURNAME'] = _SURNAME;
$replace_fields['_BIRTHDATE'] = _BIRTHDATE;
$replace_fields['_STATUS'] = _STATUS;
$replace_fields['_ADD_CHILD'] = _ADD_CHILD;
$replace_fields['_START_PAYMENT'] = _START_PAYMENT;
$replace_fields['_PACKAGE'] = _PACKAGE;
$replace_fields['_CHANGE_PACKAGE'] = _CHANGE_PACKAGE;


$children_tmpl = $templates->split_template("children", "children");
$result = $query->select_sql("math_children", "*", "user_id = ".(int)$user_class->current_user_id, "id ASC");
$n= 0;
while($row = $query->assoc($result)){
	$n ++;
	$children_fields = $row;
	$children_fields['module'] = $module;
	$children_fields['child_n'] = $n;
	
	$child_image = "upload/".$module."/thumb/".$row['image'];
	$children_fields['child_image'] = is_file($child_image) ? $child_image : "images/user_no_image.jpg";
	
	$children_fields['edit'] = icon('edit', 'title="'._EDIT_LONG.'"');
	$children_fields['deactivate'] = (int)$row['disabled'] == 0 ? icon('decline', 'title="'._DECLINE.'"') : icon('accept', 'title="'._ACTIVATE.'"');
	$children_fields['deactivate'] = $row['paid_to'] >= current_date() ? "" : $children_fields['deactivate'];
	$children_fields['child_status'] = (int)$row['disabled'] == 0 ? "" : "disabled_child";
	$children_fields['paid'] = $row['paid_to'] >= current_date() ? _PAID." ".$row['paid_to']."_"._TO : _UNPAID;
	$children_fields['paid_color'] = $row['paid_to'] >= current_date() ? "green" : "red";
	
	//**** packages
	$packages = array();
	if((int)$row['math'] == 1) {
		$packages[] = _MATH;
	}
	if((int)$row['literacy'] == 1) {
		$packages[] = _LITERACY;
	}
	$children_fields['package'] = implode(", ", (array)$packages);
	
	$templates->gen_loop_html($children_fields, $children_tmpl);
}

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

if((int)$package_payment_no_discount !== 0){
	$templates->module_ignore_fields[] = "payment_amount";
}

$out = $templates->gen_module_html($replace_fields, "children");
