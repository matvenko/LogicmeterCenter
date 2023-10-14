<?php
$user_class->permission_end($module, 'user');
global $out;

//**** last package
$edit_value = $query->select_ar_sql("math_user_packages", "*", "user_id = ".(int)$user_class->current_user_id." AND del = 0", "id DESC", "0, 1");
$children_paid_to = $query->max_value("math_children", "paid_to", "user_id = ".(int)$user_class->current_user_id." AND disabled = 0");

//***** calculate price
if(get('action') == "calculate_price"){
	$parameters = $_GET;
	
	if((int)$edit_value['paid'] == 1 && $children_paid_to >= current_date()){
		$parameters['parent_id'] = (int)$edit_value['parent_id'] == 0 ? $edit_value['id'] : $edit_value['parent_id'];
		$package_math = (int)$last_package['math'] == 1 ? 1 : get_int('math');
		$package_literacy = (int)$last_package['literacy'] == 1 ? 1 : get_int('literacy');
	}
	else{
		$package_math = get_int('math');
		$package_literacy = get_int('literacy');
	}
	
	$parameters['math'] = $package_math;
	$parameters['literacy'] = $package_literacy;
	$parameters['children_amount'] = $query->amount_fields("math_children", "user_id = ".(int)$user_class->current_user_id." AND `disabled` = 0");
	
	
	if((int)$edit_value['paid'] == 1 && get_int('period') == 0 && (int)$edit_value['math'] == $package_math && (int)$edit_value['literacy'] == $package_literacy){
		$payment_amount = 0;
	}
	else{
		$payment_amount = $math->package_payment($parameters);
		$package_payment_with_discount = $math->package_payment($parameters, $user_class->current_user_email);
	}
	
	//**** payment amount
	
	if((int)$payment_amount !== (int)$package_payment_with_discount){
		echo "</strike> <span style=\"color: #E8E8E8\">";
		echo "<strike>";
		echo (int)$payment_amount;
		echo "<sup><u>".round(($payment_amount - (int)$payment_amount) * 100)."</u></sup></span>";
		echo "</strike> ";
		echo (int)$package_payment_with_discount;
		echo "<sup><u>".round(($package_payment_with_discount - (int)$package_payment_with_discount) * 100)."</u></sup></span>";		
	}
	else{
		echo (int)$payment_amount;
		echo "<sup><u>".round(($payment_amount - (int)$payment_amount) * 100)."</u></sup></span>";
	}
	exit;
}

$replace_fields['module'] = $module;
$replace_fields['_PAYMENT_AMOUNT'] = _PAYMENT_AMOUNT;
$replace_fields['_CHOOSE_PERIOD'] = _CHOOSE_PERIOD;
$replace_fields['_CHOOSE_PACKAGE'] = _CHOOSE_PACKAGE;
$replace_fields['_MATH'] = _MATH;
$replace_fields['_LITERACY'] = _LITERACY;
$replace_fields['_SAVE'] = _SAVE;

if((int)$edit_value['paid'] == 1){
	$disable['math'] = (int)$edit_value['math'] == 1 ? "disabled" : "";
	$disable['literacy'] = (int)$edit_value['literacy'] == 1 ? "disabled" : "";
}

$replace_fields['math'] = input_form("math", "checkbox", $edit_value, "", "", "", $disable['math']);
$replace_fields['literacy'] = input_form("literacy", "checkbox", $edit_value, "", "", "", $disable['literacy']);

$periods = array(1 => "1 "._MONTH, 6 => "6 "._MONTH, 12 => "1 "._YEAR);
$periods[0] = "hide";
if((int)$edit_value['paid'] == 1 && $children_paid_to >= current_date()){
	$periods[0] = _SAME_PERIOD;
	$edit_value['period'] = 0;	
}
$replace_fields['period'] = input_form("period", "select", $edit_value, $periods, "", "month");


//**** children
$replace_fields['_NAME'] = _NAME;
$replace_fields['_SURNAME'] = _SURNAME;
$replace_fields['_PACKAGE'] = _PACKAGE;
$replace_fields['_STATUS'] = _STATUS;
$replace_fields['_CHILDREN'] = _CHILDREN;
$replace_fields['_PACKAGE_CANT_CHANGE_WHILE_UNPAID'] = _PACKAGE_CANT_CHANGE_WHILE_UNPAID;

$children_tmpl = $templates->split_template("children", "change_package");
$result = $query->select_sql("math_children", "*", "user_id = ".(int)$user_class->current_user_id." AND `disabled` = 0", "id ASC");
while($row = $query->assoc($result)){
	$children_fields = $row;
	$children_fields['module'] = $module;
	$children_fields['child_n'] = $n;

	$child_image = "upload/".$module."/thumb/".$row['image'];
	$children_fields['child_image'] = is_file($child_image) ? $child_image : "images/user_no_image.jpg";

	if($row['paid_to'] >= current_date()){
		$children_fields['paid'] = _PAID."<br>".$row['paid_to']."_"._TO;
		$children_fields['paid_color'] = "green";
		$paid_children = 1;
	}
	else{
		$children_fields['paid'] = _UNPAID;
		$children_fields['paid_color'] = "red";
		$unpaid_children = 1;
	}

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

//**** tu zogi gadaxdilia da zogi gadauxdeli
$templates->module_ignore_fields[] = (int)$paid_children * (int)$unpaid_children == 1 ? "caution" : "content";

	
$out = $templates->gen_module_html($replace_fields, "change_package");
