<?php
global $sql_db_l, $sql_db, $out;

//**** subjects ******
$query_l->where_vars['date'] = current_date();
$where_subjects = $user_class->login_action() ? "" : " AND only_local_reg <= '{{date}} 23:59:59'";
$result_subjects = $query_l->select_sql("center_subjects", "*", "reg_start_date <= '{{date}}' AND reg_end_date >= '{{date}}'".$where_subjects);
$subjects_tmpl = $templates->split_template("subjects", "choose_class");
$subjects_ar = array();
while($row_subject = $query->assoc($result_subjects)){
    $subjects_fields['subject'] = input_form("subject", "single_radio", $edit_value, $row_subject['alias']);
    $subjects_fields['subject_name'] = $row_subject['subject_name'];
    $subjects_fields['description'] = $row_subject['description'];
    $subjects_fields['subject_alias'] = $row_subject['alias'];
    $subjects_ar[$row_subject['alias']] = $row_subject['subject_name'];
    $subjects_info[$row_subject['alias']] = $row_subject;

    $templates->gen_loop_html($subjects_fields, $subjects_tmpl);
}
//********************

$subject = strlen(get('subject')) > 0 && key_exists(get('subject'), $subjects_ar) ? get('subject') : "math";

//***** calculate price
if(get('action') == "calculate_price"){
	$query = new sql_func(array("db" => $sql_db_l));
	$query->sql_db = $sql_db_l;
	//$dayes_id = get_int('math_dayes_id') == 0 ? get_int('literacy_dayes_id') : get_int('math_dayes_id');
	$subject = get('subject');

	$dayes_id = get_int('dayes_id');
	$payment_amount = $math->package_payment_center($_GET, get_int('child_id'));
	
	//$query->sql_db = $sql_db;

	//** saatebis gadakveta
	if(get_int('math_hours_id') !== 0 && get_int('math_dayes_id') == get_int('literacy_dayes_id') && get_int('math_hours_id') == get_int('literacy_hours_id')){
		$data['status'] = "error";
		$data['message'] = _LESSON_SAME_TIME;
		echo json_encode($data, JSON_UNESCAPED_UNICODE);
		exit;
	}

	//**** check class places
	//$children_amount = $query_l->amount_fields("center_class_children", "disabled = 0 AND year = '".$teach_current_year."' AND grade = ".get_int('grade')." AND dayes_id = ".(int)$schedule[0]." AND hours_id = ".(int)$schedule[1]." AND paid_to < '".current_date(time() - 10 * 24 * 3600)."'");
	//$children_amount['math'] = $query_l->amount_fields("center_class_children", "disabled = 0 AND subject = 'm' AND year = '".$math->current_lesson_year()."' AND grade = ".get_int('math_grade')." AND dayes_id = ".get_int('math_dayes_id')." AND hours_id = ".get_int('math_hours_id'));
	//$children_amount['literacy'] = $query_l->amount_fields("center_class_children", "disabled = 0 AND subject = 'l' AND year = '".$math->current_lesson_year()."' AND grade = ".get_int('literacy_grade')." AND dayes_id = ".get_int('literacy_dayes_id')." AND hours_id = ".get_int('literacy_hours_id'));
	$children_amount = $query_l->amount_fields("center_class_children", "disabled = 0 AND subject = '".substr($subject, 0, 1)."' AND year = '".$math->current_lesson_year()."' AND grade = ".get_int('grade')." AND dayes_id = ".get_int('dayes_id')." AND hours_id = ".get_int('hours_id'));

    //die($subjects_info[$subject]['max_place'].'-');
	if($children_amount >= $subjects_info[$subject]['max_place']){
		$data['status'] = "error";
		$data['message'] = "class_full";
		$data['subjects'][] = $subject;
		echo json_encode($data, JSON_UNESCAPED_UNICODE);
	}
	else{
		$remaining_lessons = $query_l->amount_fields("center_lessons_schedule", "dayes_id = ".(int)$dayes_id." AND `year` = ".(int)$math->current_lesson_year()." AND `date` > '".current_date()."'");
		$month_payment = $subjects_info[$subject]['1_month_price'];
		$period = get_int('period') == 9 ? 9.5 : get_int('period');
		$payment_bh_lessons = $period * 8 > $remaining_lessons ? $remaining_lessons * $month_payment / 8 : $month_payment * $period;
		//$both_package_discount = get_int('math') + get_int('literacy') ? 1 - $math->config['center_both_packages_discount'] / 200 : 1;
		//$payment_bh_lessons = $payment_bh_lessons * (get_int('math') + get_int('literacy'));

		$data['status'] = "ok";
		$data['message'] = $payment_bh_lessons > $payment_amount ? "<span class=\"payment_price_no_discount\">".($payment_bh_lessons)."</span>" : "";

		$data['message'] .= (int)$payment_amount;
		$data['message'] .= "</span>".icon('gel');

		echo json_encode($data, JSON_UNESCAPED_UNICODE);
	}
	exit;
}

//**** generate hours
if(get('action') == "generate_hours"){
	$query = $query_l;
	$hours = select_items("center_lesson_hours", "id", "lesson_time");

	$subject = get('subject') !== false && key_exists(get('subject'), $subjects_ar) ? get('subject') : "math";
	$hours_tmpl = $templates->split_template("hours", "choose_class_".$subject);
	$query->where_vars['subject'] = substr($subject, 0, 1);
	$where = get_int('dayes_id') > 0 ?  : "";
	$result = $query_l->select_sql("center_lesson_hours_to_dayes", "*", "subject = '{{subject}}' AND grade = ".get_int('grade')." AND dayes_id = ".get_int('dayes_id')." AND closed = 0", "sort ASC, id ASC");
	$dayes = array();
	while($row = $query->assoc($result)){
		$hours_fields['hours_id'] = $row['hours_id'];
		$hours_fields['hours'] = $hours[$row['hours_id']];
		$hours_fields['subject'] = $subject;
		$hours_fields['active_hour'] = get_int('edit_value') == (int)$row['hours_id'] ? "choose_button_active" : "";

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

//*** months
if(get('action') == "monthes"){
	$dayes[] = get_int('dayes_id');
	//*** months
	$limit = "0, 0";
	$limit = get('_payment_period') == "1m" ? "0, 8" : $limit;
	$limit = get('_payment_period') == "3m" ? "0, 24" : $limit;
	$limit = get('_payment_period') == "9m" ? "" : $limit;
	$last_year = $query_l->max_value("center_lessons_schedule", "year");
	$result_schedule = $query_l->select_sql("center_lessons_schedule", "MONTH(`date`) AS `month`, DAY(`date`) AS `day`", "`year` = ".(int)$last_year." AND dayes_id IN (".implode(",", $dayes).") AND `date` > '".current_date()."'", "`date` ASC");
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

$subject_body_tmpl = $templates->split_template("subject_body", "choose_class_".$subject);

//**** special_price
if($subjects_info[$subject]['pre_deadline'] >= current_date()){
    $templates->module_ignore_fields[] = "special_price";
}

$replace_fields['_CHOOSE_CLASS'] = _CHOOSE_CLASS;
$replace_fields['_MATH_LITERACY'] = _MATH_LITERACY;
$replace_fields['_LEGO'] = _LEGO;
$replace_fields['_INDIVIDUAL'] = _INDIVIDUAL;
$replace_fields['_BUSINESS'] = _BUSINESS;
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
$replace_fields['_CHOOSE_DIRECTION'] = _CHOOSE_DIRECTION;
$replace_fields['_CLASS_IS_FULL'] = _CLASS_IS_FULL;
$replace_fields['_CLASS_IS_EMPTY'] = _CLASS_IS_EMPTY;
$replace_fields['_PRICE'] = _PRICE;
$replace_fields['_SPECIAL_PRICE'] = _SPECIAL_PRICE;
$replace_fields['_LESSONS_PERIOD'] =_LESSONS_PERIOD;
$replace_fields['_LESSON_DAYES'] = _LESSON_DAYES;
$replace_fields['_PAYMENT_PERIOD'] = _PAYMENT_PERIOD;
$replace_fields['_SUMMER_SCHOOL_SPECIAL_DEAL'] = _SUMMER_SCHOOL_SPECIAL_DEAL;

$replace_fields['rlego_ages1'] = _LEGO_AGES_1;
$replace_fields['rlego_ages2'] = _LEGO_AGES_2;
$replace_fields['rlego_ages3'] = _LEGO_AGES_3;
$replace_fields['rlego_ages4'] = _LEGO_AGES_4;
$replace_fields['rlego_ages5'] = _LEGO_AGES_5;
$replace_fields['rlego_ages6'] = _LEGO_AGES_6;

$replace_fields['subject'] = $subject;
$replace_fields['subject_name'] = $subjects_ar[$subject];
$replace_fields['subject_checkbox'] = input_form($subject . "_check", "checkbox", $edit_value, "", "", "", "data-subject=\"" . $subject . "\"");
$replace_fields['module'] = $module;
$replace_fields['user_id'] = get_int('user_id');
$replace_fields['child_id'] = get_int('child_id');

$replace_fields['grade_input'] = input_form("grade", "hidden", $edit_value);
$replace_fields['dayes_input'] = input_form("dayes", "hidden");
$replace_fields['hours_input'] = input_form("hours", "hidden");
$replace_fields['payment_period_input'] = input_form("_payment_period", "hidden", "1");

//***** dayes
$replace_fields['dayes'] = "";
$dayes_tmpl = $templates->split_template("dayes", "choose_class_".$subject);
$result_dayes = $query_l->select_sql("center_lesson_dayes", "*", $subject . " = 1", "sort ASC");
while ($row_dayes = $query->assoc($result_dayes)) {
    $dayes_fields['dayes_id'] = $row_dayes['id'];
    $dayes_fields['dayes'] = $row_dayes['dayes'];
    $dayes_fields['subject'] = $subject;
    $dayes_fields['_LESSON_SESSION'] = _LESSON_SESSION;
    $dayes_fields['lessons_amount'] = $lessons_amount[$subject];

    $templates->gen_loop_html($dayes_fields, $dayes_tmpl);
}


//**** choosed monthes
$monthes_tmpl = $templates->split_template("choosed_monthes", "choose_class_".$subject);
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

$replace_fields['choose_class_functions'] = $templates->gen_html($replace_fields, "modules/".$module."/templates/choose_class.js");

$replace_fields['subject_body'] = $templates->gen_module_html($replace_fields, "choose_class_".$subject);

$out = $templates->gen_module_html($replace_fields, "choose_class");
