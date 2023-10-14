<?php
$user_class->permission_end($module, 'user');
global $out;
//*** search fields
$replace_fields['statistic_page'] = "statistics_common";
$templates->module_ignore_fields[] = "date";
$templates->module_ignore_fields[] = "grade_theme";
$templates->module_ignore_fields[] = "grade";
$templates->module_ignore_fields[] = "theme";

if(get('page') == "profile"){
	$templates->module_ignore_fields[] = "default";
}
include("modules/".$module."/files/statistics_init.php");

$replace_fields['_STATISTICS_FOR_PERIOD'] = _STATISTICS_FOR_PERIOD;
$replace_fields['_SKILLS_AMOUNT'] = _SKILLS_AMOUNT;
$replace_fields['_TOTAL_POINT'] = _TOTAL_POINT;
$replace_fields['_SKILL'] = _SKILL;
$replace_fields['_TESTS_AMOUNT'] = _TESTS_AMOUNT;
$replace_fields['_SPENT_TIME'] = _SPENT_TIME;
$replace_fields['_TRUE_ANSWERS'] = _TRUE_ANSWERS;
$replace_fields['_WORNG_ANSWERS'] = _WORNG_ANSWERS;
$replace_fields['_TOTAL'] = _TOTAL;
$replace_fields['_DATE'] = _DATE;
$replace_fields['_ONE_AVARAGE_TEST_SPENT_TIME'] = _ONE_AVARAGE_TEST_SPENT_TIME;
$replace_fields['_TIME_TABLE_BY_SKILLS'] = _TIME_TABLE_BY_SKILLS;
$replace_fields['date_from'] = $date_from;
$replace_fields['date_to'] = $date_to;

//***** result type **
$result_type = "day";
if(strtotime("+14 week", date_to_unix($date_from)) < date_to_unix($date_to)){
	$result_type = "month";
}
elseif(strtotime("+2 week", date_to_unix($date_from)) < date_to_unix($date_to)){
	$result_type = "week";
}

//***** list by date ***
$list_tmpl = $templates->split_template("list_by_date", "statistics_common");

//**** search ****
if(post_int('grade_id') !== 0){
	$common_where .= " AND grade_id = ".post_int('grade_id');
}
if(post_int('theme') !== 0){
	$common_where .= " AND parent_skill_id = ".post_int('theme');
}

$query->where_vars['date_from'] = $date_from;
$query->where_vars['date_to'] = $date_to;
$result = $query->select_sql("math_user_skill_info_by_day", "COUNT(id) AS skills_amount,
															`date`,
															`week`,
															`month`,
															SUM(tests_amount) AS tests_amount,
															SUM(true_answers) AS true_answers,
															SUM(spent_time) AS spent_time", $common_where." AND `date` >= '{{date_from}}' AND `date` <= '{{date_to}}'", 
							"`date` DESC", "", "user_id, child_id, ".($result_type == "day" ? "date" : $result_type));


while($row = mysql_fetch_assoc($result)){
	$list_fields = $row;
	$list_fields['wrong_answers'] = $row['tests_amount'] - $row['true_answers'];
	$list_fields['_HR'] = _HR;
	$list_fields['_MNT'] = _MNT;
	$list_fields['_SC'] = _SC;
	$list_fields['spent_time_hour'] = (int)($row['spent_time'] / 3600);
	$list_fields['spent_time_minute'] = (int)(($row['spent_time'] - $list_fields['spent_time_hour'] * 3600) / 60) ;
	$avarage_test_spent_time = round($row['spent_time'] / $row['tests_amount']);
	$list_fields['avarage_test_spent_time_minute'] = (int)($avarage_test_spent_time / 60);
	$list_fields['avarage_test_spent_time_second'] = (int)($avarage_test_spent_time % 60);
		
	if($result_type == "week"){
		$week_first_day = date_week_first_day($row['date'], $date_from, $row['week']);
		$week_last_day = date_week_last_day($row['date'], $date_to, $row['week']);
		$list_fields['date'] = date("m-d", $week_first_day)." - ".date("m-d", $week_last_day);
		$chart_data_ar[(int)$row['date']."-".$row['week']] = round($row['spent_time'] / 60);
	}
 	elseif($result_type == "month"){
		$month_first_day = date_month_first_day($row['date'], $date_from, $row['month']);
		$month_last_day = date_month_last_day($row['date'], $date_to, $row['month']);
		$list_fields['date'] = date("m-d", $month_first_day)." - ".date("m-d", $month_last_day);
		$chart_data_ar[(int)$row['date']."-".$row['month']] = round($row['spent_time'] / 60);
	}
	else{
		$chart_data_ar[$row['date']] = round($row['spent_time'] / 60);
	}
	
	$templates->gen_loop_html($list_fields, $list_tmpl);
}

//***** list by skill ***
$list_tmpl = $templates->split_template("list_by_skill", "statistics_common");

$query->where_vars['date_from'] = $date_from;
$query->where_vars['date_to'] = $date_to;
$result = $query->select_sql("math_user_skill_info_by_day", "COUNT(id) AS skills_amount,
															`date`,
															`week`,
															`month`,
															grade_id,
															skill_id,
															SUM(tests_amount) AS tests_amount,
															SUM(true_answers) AS true_answers,
															SUM(spent_time) AS spent_time", $common_where." AND `date` >= '{{date_from}}' AND `date` <= '{{date_to}}'",
		"`date` DESC", "", "user_id, child_id, skill_id");


while($row = mysql_fetch_assoc($result)){
	$list_fields = $row;
	$grade_info = $math->grade_info($row['grade_id']);
	$skill_info = $math->skill_info($row['skill_id']);
	$skill_sum_info = $query->select_ar_sql("math_user_skill_info", "smart_point", $common_where." AND skill_id = ".(int)$row['skill_id']);
	
	$list_fields['smart_point'] = $skill_sum_info['smart_point'];
	$list_fields['skill'] = $grade_info['number']."-".$skill_info['number']." ".$skill_info['skill_name'];
	$list_fields['wrong_answers'] = $row['tests_amount'] - $row['true_answers'];
	$list_fields['_HR'] = _HR;
	$list_fields['_MNT'] = _MNT;
	$list_fields['_SC'] = _SC;
	$list_fields['spent_time_hour'] = (int)($row['spent_time'] / 3600);
	$list_fields['spent_time_minute'] = (int)(($row['spent_time'] - $list_fields['spent_time_hour'] * 3600) / 60) ;
	$avarage_test_spent_time = round($row['spent_time'] / $row['tests_amount']);
	$list_fields['avarage_test_spent_time_minute'] = (int)($avarage_test_spent_time / 60);
	$list_fields['avarage_test_spent_time_second'] = (int)($avarage_test_spent_time % 60);

	$templates->gen_loop_html($list_fields, $list_tmpl);
}

//***** chart *****
$replace_fields['_CHART_TITLE'] = _CHART_TITLE_COMMON;
$replace_fields['_LEFT_LABEL'] = _LEFT_LABEL_COMMON;
$replace_fields['_BOTTOM_LABEL'] = _BOTTOM_LABEL_COMMON;

$chart_unix_time = date_to_unix($date_from);
//$chart_data[] = array_key_exists(current_date($chart_unix_time), (array)$chart_data_ar) ? $chart_data_ar[current_date($chart_unix_time)] : 0;
while($last_day < date_to_unix($date_to)){	
	$chart_date = current_date($chart_unix_time);
	if($result_type == "week"){
		$chart_week = date("W", $chart_unix_time);
		$chart_data_key = (int)$chart_date."-".$chart_week;
		$first_day = date_week_first_day($chart_date, $date_from, $chart_week);
		$last_day = date_week_last_day($chart_date, $date_to, $chart_week);
		$labels .= ",\"".$ar_month[date("n", $chart_unix_time)]." ".date("d", $first_day).($last_day == $first_day ? "" : "-".date("d", $last_day))."\"";
	}
 	elseif($result_type == "month"){
 		$chart_month = date("n", $chart_unix_time);
		$chart_data_key = (int)$chart_date."-".$chart_month;
		$first_day = date_month_first_day($chart_date, $date_from, $chart_month);
		$last_day = date_month_last_day($chart_date, $date_to, $chart_month);
		$labels .= ",\"".$ar_month[$chart_month]." ".date("d", $first_day).($last_day == $first_day ? "" : " - ".date("d", $last_day))."\"";
	}
	else{
		$chart_data_key = current_date($chart_unix_time);
		$last_day = $chart_unix_time;
		$labels .= ",\"".$ar_month[date("n", $chart_unix_time)]."-".date("d", $chart_unix_time)."\"";
	}
	
	$chart_data[] = array_key_exists($chart_data_key, (array)$chart_data_ar) ? $chart_data_ar[$chart_data_key] : 0;

	$chart_unix_time = strtotime("+1 ".$result_type, $chart_unix_time);
}

$replace_fields['chart_data'] = implode(",", $chart_data);
$replace_fields['labels'] = ltrim($labels, ',');

$out .= $templates->gen_module_html($replace_fields, "statistics_common");
unset($templates->module_content);
