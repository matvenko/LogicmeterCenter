<?php
global $sql_db_l, $sql_db;

//***** calculate price
if(get('action') == "calculate_price"){
	$query->sql_db = $sql_db_l;
	$schedule = explode('-', get('schedule'));
	$_GET['dayes_id'] = $schedule[0];
	$payment_amount = $math->package_payment_center($_GET, get_int('child_id'));
	//$query->sql_db = $sql_db;

	//**** check class places
	$children_amount = $query_l->amount_fields("center_class_children", "grade = ".get_int('grade')." AND dayes_id = ".(int)$schedule[0]." AND hours_id = ".(int)$schedule[1]." AND paid_to < '".current_date(time() - 10 * 24 * 3600)."'");

	if($children_amount >= 12){
		echo "class_full";
	}
	else{
		$remaining_lessons = $query_l->amount_fields("center_lessons_schedule", "dayes_id = ".(int)$schedule[0]." AND `year` = ".(int)$math->current_lesson_year()." AND `date` > '".current_date()."'");
		$payment_bh_lessons = get_int('period') * 8 > $remaining_lessons ? $remaining_lessons * $math->config['center_1_monthly_paiment'] / 8 : $math->config['center_1_monthly_paiment'] * get_int('period');
		echo get_int('period') > 1 ? "<span class=\"payment_price_no_discount\">".($payment_bh_lessons)."</span>" : "";
		
		echo (int)$payment_amount;
		echo "<sup><u>".round(($payment_amount - (int)$payment_amount) * 100)."</u></sup></span>".icon('gel');
	}
	exit;
}

//*** class block dayes
if(get('action') == "class_block_dayes"){
	$class[0] = array('3-4');
	$class[1] = array('1-4', '2-4', '3-4');
	$class[2] = array('1-4', '2-4', '3-4');
	$class[3] = array('1-4', '2-4', '3-4');
	$class[4] = array('1-1', '1-2', '1-4', '2-4', '3-4');
	
	$data['dayes'] = (array)$class[get_int('grade')];
	$data['grade'] = get_int('grade');
	echo json_encode($data);
	exit;
}

//*** class_places
if(get('action') == "class_places"){
	$schedule = explode('-', get('schedule'));
	$children_amount = $query_l->amount_fields("center_class_children", "grade = ".get_int('grade')." AND dayes_id = ".(int)$schedule[0]." AND hours_id = ".(int)$schedule[1]." AND paid_to < '".current_date(time() - 10 * 24 * 3600)."'");
	$data['class_full'] = $children_amount >= 12 ? 1 : 0;
	//$new_class_places = $children_amount % 12;
	$new_class_places = $children_amount;
	
	for($i = 1; $i <= 12; $i++){
		//$place_type[$i] = $i <= $new_class_places ? 0 : -70;
		//$place_type[$i] = $i == $new_class_places + 1 ? -35 : $place_type[$i];
		$place_type[$i] = -35;
	}
	
	$data['place_type'] = $place_type;
	$data['schedule'] = get('schedule');
	
	//*** monthes
	$limit = "0, 0";
	$limit = get('payment_period') == "1m" ? "0, 8" : $limit;
	$limit = get('payment_period') == "3m" ? "0, 24" : $limit;
	$limit = get('payment_period') == "9m" ? "" : $limit;
	$last_year = $query_l->max_value("center_lessons_schedule", "year");
	$result_schedule = $query_l->select_sql("center_lessons_schedule", "MONTH(`date`) AS `month`, DAY(`date`) AS `day`", "`year` = ".(int)$last_year." AND dayes_id = ".(int)$schedule[0]." AND `date` > '".current_date()."'", "", $limit);
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
	
	echo json_encode($data);
	exit;
}

$monthes = array(1 => _JANUARY, 2 => _FABRUARY, 3 => _MARCH, 4 => _APRIL, 5 => _MAY, 6 => _JUNE, 7 => _JULY, 8 => _AUGUST, 9 => _SEPTEMBER, 10 => _OCTOMBER, 11 => _NOVEMBER, 12 => _DECEMBER);

$replace_fields['module'] = $module;
$replace_fields['child_id'] = get_int('child_id');
$replace_fields['_CHOOSE_CLASS'] = _CHOOSE_CLASS;
$replace_fields['_CHOOSE_SCHEDULE'] = _CHOOSE_SCHEDULE;
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
$replace_fields['_CLASS_IS_FULL'] = _CLASS_IS_FULL;
$replace_fields['_CLASS_IS_EMPTY'] = _CLASS_IS_EMPTY;
$replace_fields['_PRICE'] = _PRICE;

$replace_fields['grade_input'] = input_form("grade", "hidden", $edit_value);
$replace_fields['schedule_input'] = input_form("schedule", "hidden", $edit_value);
$replace_fields['payment_period_input'] = input_form("payment_period", "hidden", $edit_value);

//***** schedule
$query->sql_db = $sql_db_l;
$dayes = select_items("center_lesson_dayes", "id", "dayes");
$query->sql_db = $sql_db;
$dayes_id = array_keys($dayes);
$dayes_values = array_values($dayes);
$replace_fields['dayes_1'] = $dayes_values[0];
$replace_fields['dayes_2'] = $dayes_values[1];
$replace_fields['dayes_3'] = $dayes_values[2];
$hours_tmpl = $templates->split_template("hours", "choose_class");
$result_hour = $query_l->select_sql("center_lesson_hours", "*", "`show` = 1");

while($row_hour = mysql_fetch_assoc($result_hour)){
	$hour_fields['time'] = substr($row_hour['lesson_time'], 0, 5);

	$hour_fields['dayes_id_1'] = $dayes_id[0];
	$hour_fields['dayes_id_2'] = $dayes_id[1];
	$hour_fields['dayes_id_3'] = $dayes_id[2];

	$hour_fields['hour_id'] = $row_hour['id'];

	$templates->gen_loop_html($hour_fields, $hours_tmpl);
}

//*** class places
$place_tmpl = $templates->split_template("place", "choose_class");
for($i = 1; $i <= 12; $i ++){
	$place_fields['n'] = $i;
	$place_fields['place_type'] = -35;
	$place_fields['class_place_image'] = "left";
	$place_fields['class_place_image'] = $i > 5 ? "top" : $place_fields['class_place_image'];
	$place_fields['class_place_image'] = $i > 10 ? "right" : $place_fields['class_place_image'];

	$replace_fields['place_'.$i] = $templates->gen_html($place_fields, $place_tmpl['place'], 0);
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
$replace_fields['choosed_monthes_1'] = $monthes_1;
$replace_fields['choosed_monthes_2'] = $monthes_2;

$out = $templates->gen_module_html($replace_fields, "choose_class");
