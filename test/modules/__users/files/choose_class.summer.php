<?php
global $sql_db_l, $sql_db;

//$subjects_ar = array('math' => _MATH, 'literacy' => _LITERACY);
$subjects_ar = array('math' => _MATH." + "._LITERACY);

$subject = key_exists($subject, $subjects_ar) ? $subject : "math";

//***** calculate price
if(get('action') == "calculate_price"){
	$query->sql_db = $sql_db_l;
	$dayes_id = get_int('math_dayes_id') == 0 ? get_int('literacy_dayes_id') : get_int('math_dayes_id');
	$payment_amount = $math->package_payment_center($_GET, get_int('child_id'));
	//$query->sql_db = $sql_db;

	//** saatebis gadakveta
	if(get_int('math_hours_id') !== 0 && get_int('math_dayes_id') == get_int('literacy_dayes_id') && get_int('math_hours_id') == get_int('literacy_hours_id')){
		echo _LESSON_SAME_TIME;
		exit;
	}

	//**** check class places
	$children_amount = $query_l->amount_fields("center_class_children", "grade = ".get_int('grade')." AND dayes_id = ".(int)$schedule[0]." AND hours_id = ".(int)$schedule[1]." AND paid_to < '".current_date(time() - 10 * 24 * 3600)."'");

	if($children_amount >= 15){
		echo "class_full";
	}
	else{
		$remaining_lessons = $query_l->amount_fields("center_lessons_schedule", "dayes_id = ".(int)$dayes_id." AND `year` = ".(int)$math->current_lesson_year()." AND `date` > '".current_date()."'");
		$payment_bh_lessons = get_int('period') * 8 > $remaining_lessons ? $remaining_lessons * $math->config['center_1_monthly_paiment'] / 8 : $math->config['center_1_monthly_paiment'] * get_int('period');
		$both_package_discount = get_int('math') + get_int('literacy') ? 1 - $math->config['center_both_packages_discount'] / 200 : 1;
		$payment_bh_lessons = $payment_bh_lessons * (get_int('math') + get_int('literacy'));

		$payment_bh_lessons = 220; // 15-is mere 220 egireba
		echo $payment_bh_lessons > $payment_amount ? "<span class=\"payment_price_no_discount\">".($payment_bh_lessons)."</span>" : "";

		echo (int)$payment_amount;
		echo "<sup><u>".round(($payment_amount - (int)$payment_amount) * 100)."</u></sup></span>".icon('gel');
	}
	exit;
}

//**** generate hours
if(get('action') == "generate_hours"){
	$query = $query_l;
	$hours = select_items("center_lesson_hours", "id", "lesson_time");
	
	$subject = key_exists(get('subject'), $subjects_ar) ? get('subject') : "math";
	$hours_tmpl = $templates->split_template("hours", "choose_class");
	$query->where_vars['subject'] = substr($subject, 0, 1);
	$result = $query_l->select_sql("center_lesson_hours_to_dayes", "*", "subject = '{{subject}}' AND grade = ".get_int('grade')." AND dayes_id = ".get_int('dayes_id')." AND  closed = 0");
	while($row = mysql_fetch_assoc($result)){
		$hours_fields['hours_id'] = $row['hours_id'];
		$hours_fields['hours'] = $hours[$row['hours_id']];
		$hours_fields['subject'] = $subject;
		$hours_fields['active_hour'] = get_int('edit_value') == (int)$row['hours_id'] ? "choose_button_active" : "";
		
		$templates->gen_loop_html($hours_fields, $hours_tmpl);
	}
	$data['source'] = $templates->module_content['hours'];
	$data['subject'] = $subject;
	echo json_encode($data);
	exit;
	
}

//*** monthes
if(get('action') == "monthes"){
	$dayes[] = get_int('literacy_dayes_id');
	$dayes[] = get_int('math_dayes_id');
	//*** monthes
	$limit = "0, 0";
	$limit = get('payment_period') == "1m" ? "0, 8" : $limit;
	$limit = get('payment_period') == "3m" ? "0, 24" : $limit;
	$limit = get('payment_period') == "9m" ? "" : $limit;
	$last_year = $query_l->max_value("center_lessons_schedule", "year");
	$result_schedule = $query_l->select_sql("center_lessons_schedule", "MONTH(`date`) AS `month`, DAY(`date`) AS `day`", "`year` = ".(int)$last_year." AND dayes_id IN (".implode(",", $dayes).") AND `date` > '".current_date()."'", "", $limit);
	$n = 1;
	while($row_schedule = mysql_fetch_assoc($result_schedule)){
		if($n == 1){
			$data['schedule_first_day']['day'] = $row_schedule['day'];
			$data['schedule_first_day']['month'] = $row_schedule['month'];
			$n++;
		}
		$data['schedule_monthes'][$row_schedule['month']] = 1;
		$data['schedule_last_day']['day'] = $row_schedule['day'];
		$data['schedule_last_day']['month'] = $row_schedule['month'];
	}
	
	echo json_encode((array)$data);
	exit;
}

$monthes = array(1 => _JANUARY, 2 => _FABRUARY, 3 => _MARCH, 4 => _APRIL, 5 => _MAY, 6 => _JUNE, 7 => _JULY, 8 => _AUGUST, 9 => _SEPTEMBER, 10 => _OCTOMBER, 11 => _NOVEMBER, 12 => _DECEMBER);

$subject_body_tmpl = $templates->split_template("subject_body", "choose_class");

$replace_fields['_CHOOSE_CLASS'] = _CHOOSE_CLASS;
$replace_fields['_CHOOSE_SCHEDULE'] = _CHOOSE_SCHEDULE;
$replace_fields['_CHOOSE_TIME'] = _CHOOSE_TIME;
$replace_fields['_CLASS'] = _CLASS;
$replace_fields['_PLACES_IN_CLASS'] = _PLACES_IN_CLASS;
$replace_fields['_RESERVED'] = _RESERVED;
$replace_fields['_FREE'] = _FREE;
$replace_fields['_YOUR_PLACE'] = _YOUR_PLACE;
$replace_fields['_FULL_PROGRAM'] = _FULL_PROGRAM;
$replace_fields['_MONTH'] = _MONTH;
$replace_fields['_CHOOSED_MONTHES'] = _CHOOSED_MONTHES;
$replace_fields['_CHOOSE_PERIOD'] = _CHOOSE_PERIOD;
$replace_fields['_CHOOSE_GROUP'] = _SUMMER_SCHOOL;
$replace_fields['_CLASS_IS_FULL'] = _CLASS_IS_FULL;
$replace_fields['_CLASS_IS_EMPTY'] = _CLASS_IS_EMPTY;
$replace_fields['_PRICE'] = _PRICE;
$replace_fields['_LESSONS_PERIOD'] =_LESSONS_PERIOD;
$replace_fields['_LESSON_DAYES'] = _LESSON_DAYES;
$replace_fields['_PAYMENT_PERIOD'] = _PAYMENT_PERIOD;
$replace_fields['_SUMMER_SCHOOL_SPECIAL_DEAL'] = _SUMMER_SCHOOL_SPECIAL_DEAL;

foreach($subjects_ar as $subject => $subject_title){
	$replace_fields['subject'] = $subject;
	$replace_fields['subject_name'] = $subjects_ar[$subject];
	$replace_fields['subject_checkbox'] = input_form($subject."_check", "checkbox", $edit_value, "", "", "", "data-subject=\"".$subject."\"");
	$replace_fields['module'] = $module;
	$replace_fields['child_id'] = get_int('child_id');

	$replace_fields['grade_input'] = input_form($subject."_grade", "hidden", $edit_value);
	$replace_fields['dayes_input'] = input_form($subject."_dayes", "hidden", 5);
	$replace_fields['hours_input'] = input_form($subject."_hours", "hidden", "5");
	$replace_fields['payment_period_input'] = input_form("_payment_period", "hidden", "2.25m");
	
	//***** dayes
	$replace_fields['dayes'] = "";
	$dayes_tmpl = $templates->split_template("dayes", "choose_class");
	//$result_dayes = $query_l->select_sql("center_lesson_dayes", "*", $subject." = 1 AND id = 5");
	$dayes_array = array(_MONDAY => _MATH, _WEDNESDAY => _LITERACY, _FRIDAY => _MATH);
	foreach($dayes_array as $day_name => $day_subject){
		$dayes_fields['dayes_id'] = $row_dayes['id'];
		$dayes_fields['dayes'] = $day_name;
		$dayes_fields['subject'] = $subject;
		$dayes_fields['day_subject'] = $day_subject;
		$dayes_fields['_LESSON_SESSION'] = _LESSON_SESSION;
		
		$templates->gen_loop_html($dayes_fields, $dayes_tmpl);
		$replace_fields['dayes'] .= $templates->gen_module_custom_html($dayes_fields, "choose_class", $dayes_tmpl['dayes']);
	}
	
	
	$replace_fields['subject_body'] .= $templates->gen_module_custom_html($replace_fields, "choose_class", $subject_body_tmpl['subject_body']);
	unset($templates->module_content);
}



//**** choosed monthes
$monthes_tmpl = $templates->split_template("choosed_monthes", "choose_class");
$last_year = $query_l->max_value("center_lessons_schedule", "year");
$result_monthes = $query_l->select_sql("center_lessons_schedule", "DISTINCT MONTH(`date`) AS `month`", "`year` = ".(int)$last_year);
$monthes_amount = mysql_num_rows($result_monthes);
$n = 0;
while($row_monthes = mysql_fetch_assoc($result_monthes)){
	$n++;
	$monthes_fields['month'] = $monthes[$row_monthes['month']];
	$monthes_fields['month_n'] = $row_monthes['month'];

	if($n <= $monthes_amount / 2){
		$monthes_1 .= $templates->gen_html($monthes_fields, $monthes_tmpl['choosed_monthes'], 0);
	}
	else{
		$monthes_2 .= $templates->gen_html($monthes_fields, $monthes_tmpl['choosed_monthes'], 0);
	}
	//$templates->gen_loop_html($monthes_fields, $monthes_tmpl);
}

$summer_school_schedule_start = $query_l->min_value("center_lessons_schedule", "date", "year = 2015 AND dayes_id = 5");
$summer_school_schedule_start_info = explode("-", $summer_school_schedule_start);
$summer_school_schedule_end = $query_l->max_value("center_lessons_schedule", "date", "year = 2015 AND dayes_id = 5");
$summer_school_schedule_end_info = explode("-", $summer_school_schedule_end);
$replace_fields['lessons_start_day'] = $summer_school_schedule_start_info[2];
$replace_fields['lessons_start_month'] = $monthes[(int)$summer_school_schedule_start_info[1]];
$replace_fields['lessons_end_day'] = $summer_school_schedule_end_info[2];
$replace_fields['lessons_end_month'] = $monthes[(int)$summer_school_schedule_end_info[1]];

$replace_fields['_LESSON_SAME_TIME'] = _LESSON_SAME_TIME;
$replace_fields['choosed_monthes_1'] = $monthes_1;
$replace_fields['choosed_monthes_2'] = $monthes_2;

$out = $templates->gen_module_html($replace_fields, "choose_class");
