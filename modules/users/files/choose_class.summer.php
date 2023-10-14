<?php
global $sql_db_l, $sql_db;

//$subjects_ar = array('math' => _MATH, 'literacy' => _LITERACY);
$subjects_ar = array('math' => _MATH." + "._LITERACY, 'rlego' => _LEGO);

$subject = key_exists($subject, $subjects_ar) ? $subject : "math";

//***** calculate price
if(get('action') == "calculate_price"){
	$query = new sql_func(array("db" => $sql_db_l));
	$dayes_id = get_int('math_dayes_id') == 0 ? get_int('literacy_dayes_id') : get_int('math_dayes_id');
	$dayes_id = $dayes_id == 0 ? get_int('rlego_dayes_id') : $dayes_id;
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
		$data['status'] = "error";
		$data['message'] = "class_full";

		echo json_encode($data, JSON_UNESCAPED_UNICODE);
	}
	else{
		$remaining_lessons = $query_l->amount_fields("center_lessons_schedule", "dayes_id = ".(int)$dayes_id." AND `year` = ".(int)$math->current_lesson_year()." AND `date` > '".current_date()."'");
		$payment_bh_lessons = get_int('period') * 8 > $remaining_lessons ? $remaining_lessons * $math->config['center_1_monthly_paiment'] / 8 : $math->config['center_1_monthly_paiment'] * get_int('period');
		$both_package_discount = get_int('math') + get_int('literacy') ? 1 - $math->config['center_both_packages_discount'] / 200 : 1;
		$payment_bh_lessons = $payment_bh_lessons * (get_int('math') + get_int('literacy'));

		$payment_bh_lessons = get_int('rlego') == 1 ? 155 : 220; // 15-is mere 220 egireba

		$data['status'] = "ok";
		$data['message'] = $payment_bh_lessons > $payment_amount ? "<span class=\"payment_price_no_discount\">".($payment_bh_lessons)."</span>" : "";

		$data['message'] .= (int)$payment_amount;
		$data['message'] .= "<sup><u>".round(($payment_amount - (int)$payment_amount) * 100)."</u></sup></span>".icon('gel');

		echo json_encode($data, JSON_UNESCAPED_UNICODE);
	}
	exit;
}

//**** generate hours
if(get('action') == "generate_hours"){
	$query = $query_l;
	$hours = select_items("center_lesson_hours", "id", "lesson_time");

	$subject = get('subject') !== false && key_exists(get('subject'), $subjects_ar) ? get('subject') : "math";
	$hours_tmpl = $templates->split_template("hours", "choose_class");
	$query->where_vars['subject'] = substr($subject, 0, 1);
	$where = get_int('dayes_id') > 0 ?  : "";
	$dayes = select_items("center_lesson_hours_to_dayes", "hours_id", "hours_id", "subject = '{{subject}}' AND grade = ".get_int('grade')." AND dayes_id = ".get_int('dayes_id')." AND closed = 0");
	$hours = count($dayes) > 0 ? select_items("center_lesson_hours", "id", "lesson_time", "id IN (".implode(",", $dayes).") ORDER BY lesson_time ASC") : array();
	foreach($hours as $hours_id => $lesson_time){
		$hours_fields['hours_id'] = $hours_id;
		$hours_fields['hours'] = $lesson_time;
		$hours_fields['subject'] = $subject;
		$hours_fields['active_hour'] = get_int('edit_value') == (int)$hours_id ? "choose_button_active" : "";

		$templates->gen_loop_html($hours_fields, $hours_tmpl);
	}
	$data['source'] = get_int('dayes_id') > 0 && isset($templates->module_content['hours']) ? $templates->module_content['hours'] : "";
	$days = select_items("center_lesson_hours_to_dayes", "dayes_id", "dayes_id", "subject = '{{subject}}' AND grade = ".get_int('grade')." AND closed = 0");
	$data['days'] = count($days) > 0 ? array_values($days) : array();
	$data['subject'] = $subject;
	echo json_encode($data);
	exit;
}

$monthes = array(1 => _JANUARY, 2 => _FABRUARY, 3 => _MARCH, 4 => _APRIL, 5 => _MAY, 6 => _JUNE, 7 => _JULY, 8 => _AUGUST, 9 => _SEPTEMBER, 10 => _OCTOMBER, 11 => _NOVEMBER, 12 => _DECEMBER);

//*** monthes
if(get('action') == "monthes"){
	$dayes[] = get_int('math_dayes_id') == 0 ? get_int('rlego_dayes_id') : get_int('math_dayes_id');
	//*** monthes
	$last_year = $query_l->max_value("center_lessons_schedule", "year");
	$result_schedule = $query_l->select_sql("center_lessons_schedule", "MONTH(`date`) AS `month`, DAY(`date`) AS `day`", "`year` = ".(int)$last_year." AND dayes_id IN (".implode(",", $dayes).") AND `date` > '".current_date()."' AND lesson_n BETWEEN 1 AND 18", "lesson_n ASC");
	$n = 1;
	while($row_schedule = $query->assoc($result_schedule)){
		if($n == 1){
			$schedule_first_day = $row_schedule['day'];
			$schedule_first_month = $row_schedule['month'];
			$n++;
		}
		$schedule_last_day = $row_schedule['day'];
		$schedule_last_month = $row_schedule['month'];
	}

	$data['lesson_period'] = $schedule_first_day." ".$monthes[$schedule_first_month]." - ".$schedule_last_day." ".$monthes[$schedule_last_month];

	echo json_encode((array)$data);
	exit;
}

$replace_fields['direction_1'] = input_form("direction", "single_radio", $edit_value, 1);
$replace_fields['direction_2'] = input_form("direction", "single_radio", $edit_value, 2);
$replace_fields['direction_3'] = input_form("direction", "single_radio", $edit_value, 3);

$subject_body_tmpl = $templates->split_template("subject_body", "choose_class.summer");

$replace_fields['_CHOOSE_CLASS'] = _CHOOSE_CLASS;
$replace_fields['_MATH_LITERACY'] = _MATH_LITERACY;
$replace_fields['_LEGO'] = _LEGO;
$replace_fields['_GENERAL_SKILLS'] = _GENERAL_SKILLS;
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
$replace_fields['_CHOOSE_GROUP'] = _CHOOSE_GROUP;
$replace_fields['_SUMMER_SCHOOL'] = _SUMMER_SCHOOL;
$replace_fields['_CLASS_IS_FULL'] = _CLASS_IS_FULL;
$replace_fields['_CLASS_IS_EMPTY'] = _CLASS_IS_EMPTY;
$replace_fields['_PRICE'] = _PRICE;
$replace_fields['_LESSONS_PERIOD'] =_LESSONS_PERIOD;
$replace_fields['_LESSON_DAYES'] = _LESSON_DAYES;
$replace_fields['_PAYMENT_PERIOD'] = _PAYMENT_PERIOD;
$replace_fields['_SUMMER_SCHOOL_SPECIAL_DEAL'] = _SUMMER_SCHOOL_SPECIAL_DEAL;

$replace_fields['rlego_ages1'] = _LEGO_AGES_1;
$replace_fields['rlego_ages2'] = _LEGO_AGES_2;

$lessons_amount = ['math' => 3, 'rlego' => 2];
foreach($subjects_ar as $subject => $subject_title){
	$replace_fields['subject'] = $subject;
	$replace_fields['subject_name'] = $subjects_ar[$subject];
	$replace_fields['subject_checkbox'] = input_form($subject."_check", "checkbox", $edit_value, "", "", "", "data-subject=\"".$subject."\"");
	$replace_fields['module'] = $module;
	$replace_fields['child_id'] = get_int('child_id');

	$replace_fields['grade_input'] = input_form($subject."_grade", "hidden", $edit_value);
	$replace_fields['dayes_input'] = input_form($subject."_dayes", "hidden");
	$replace_fields['hours_input'] = input_form($subject."_hours", "hidden", 11);
	$replace_fields['payment_period_input'] = input_form("_payment_period", "hidden", "2.25m");

	//***** dayes
	$replace_fields['dayes'] = "";
	$dayes_tmpl = $templates->split_template("dayes", "choose_class.summer");
	$result_dayes = $query_l->select_sql("center_lesson_dayes", "*", $subject." = 1 AND summer = 1", "sort ASC");
	while($row_dayes = $query->assoc($result_dayes)){
		$dayes_fields['dayes_id'] = $row_dayes['id'];
		$dayes_fields['dayes'] = $row_dayes['dayes'];
		$dayes_fields['subject'] = $subject;
		$dayes_fields['_LESSON_SESSION'] = _LESSON_SESSION;
		$dayes_fields['lessons_amount'] = $lessons_amount[$subject];

		$templates->gen_loop_html($dayes_fields, $dayes_tmpl);
		$replace_fields['dayes'] .= $templates->gen_module_custom_html($dayes_fields, "choose_class.summer", $dayes_tmpl['dayes']);
	}
	
	$replace_fields['subject_body'] .= $templates->gen_module_custom_html($replace_fields, "choose_class.summer", $subject_body_tmpl['subject_body']);
	unset($templates->module_content);
}



//**** choosed monthes
$monthes_tmpl = $templates->split_template("choosed_monthes", "choose_class.summer");
$last_year = $query_l->max_value("center_lessons_schedule", "year");
$result_monthes = $query_l->select_sql("center_lessons_schedule", "DISTINCT MONTH(`date`) AS `month`", "`year` = ".(int)$last_year);
$monthes_amount = mysqli_num_rows($result_monthes);
$n = 0;
while($row_monthes = $query->assoc($result_monthes)){
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

$replace_fields['_LESSON_SAME_TIME'] = _LESSON_SAME_TIME;
$replace_fields['choosed_monthes_1'] = $monthes_1;
$replace_fields['choosed_monthes_2'] = $monthes_2;

$out = $templates->gen_module_html($replace_fields, "choose_class.summer");
