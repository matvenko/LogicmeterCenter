<?php
$user_class->permission_end($module, 'user');
global $out;

//***** search
if(post_int('search') == 0){
	$templates->module_ignore_fields[] = "search";
}
$replace_fields['module'] = $module;
$replace_fields['_UPDATE'] = _UPDATE;
$replace_fields['_DATE'] = _DATE;
$replace_fields['_FROM'] = _FROM;
$replace_fields['_TO'] = _TO;
$replace_fields['_CHILD'] = _CHILD;
$replace_fields['_GRADE'] = _GRADE;
$replace_fields['_SUBJECT'] = _SUBJECT_NAME;

$replace_fields['_JANUARY'] = _JANUARY;
$replace_fields['_FABRUARY'] = _FABRUARY;
$replace_fields['_MARCH'] = _MARCH;
$replace_fields['_APRIL'] = _APRIL;
$replace_fields['_MAY'] = _MAY;
$replace_fields['_JUNE'] = _JUNE;
$replace_fields['_JULY'] = _JULY;
$replace_fields['_AUGUST'] = _AUGUST;
$replace_fields['_SEPTEMBER'] = _SEPTEMBER;
$replace_fields['_OCTOMBER'] = _OCTOMBER;
$replace_fields['_NOVEMBER'] = _NOVEMBER;
$replace_fields['_DECEMBER']= _DECEMBER;

$replace_fields['_W_OR'] = _W_OR;
$replace_fields['_W_SAM'] = _W_SAM;
$replace_fields['_W_OTX'] = _W_OTX;
$replace_fields['_W_XUT'] = _W_XUT;
$replace_fields['_W_PAR'] = _W_PAR;
$replace_fields['_W_SHAB'] = _W_SHAB;
$replace_fields['_W_KV'] = _W_KV;

$date_from = set_var(post('date_from'), current_date(strtotime("-1 month")));
$date_to =  set_var(post('date_to'), current_date());

$date_types = array(0 => _ALL, 1 => _LAST_WEEK, 2 => _LAST_MONTH, 3 => _LAST_3_MONTH, 4 => _LAST_6_MONTH, 5 => _LAST_YEAR);
$edit_value['date_types'] = post('date_types') === false ? 2 : post_int('date_types');
$replace_fields['date_types'] = input_form("date_types", "select", $edit_value, $date_types);
$replace_fields['date_from'] = input_form("date_from", "textbox", $date_from, "", "", "calendar");
$replace_fields['date_to'] = input_form("date_to", "textbox", $date_to, "", "", "calendar");
$children = select_items("math_children", "id", "name", "user_id = ".(int)$user_class->current_user_id." ORDER BY id ASC");
$children[0] = "hide";
$replace_fields['children'] = input_form("child_id", "select", $_POST, $children, "width: 120px");
$grade_type = $math->statistic_subject() == "literacy" ? 2 : 1;
$grades_get = $math->grades_get($grade_type);
foreach($grades_get as $grade_number => $grade_info){
	$grades[$grade_info['id']] = $grade_number;
}
$replace_fields['grades'] = input_form("grade", "select", $_POST, $grades);

$subject =  set_var(post('subject'), 1);
$replace_fields['subject'] = input_form("subject", "select", $subject, array(1 => _MATH, 2 => _LITERACY), "width: 105px");

//**** awards
$where = post_int('subject') == 2 ? " AND text_id != 0" : " AND text_id = 0";
if(post_int('grade_id') !== 0){
	$where .= " AND grade_id = ".post_int('grade_id');
}

$awards = select_items("math_awards", "id", "name");
$award_tmpl = $templates->split_template("awards", "awards");

$first_child = $query->select_ar_sql("math_children", "id", "user_id = ".(int)$user_class->current_user_id, "id ASC", "0,1");
$child_id = set_var(post_int('child_id'), $first_child['id']);

$query->where_vars['date_from'] = $date_from;
$query->where_vars['date_to'] = $date_to;
$result = $query->select_sql("math_children_awards", "*", "user_id = ".(int)$user_class->current_user_id." AND child_id = ".(int)$child_id."
															 AND `add_date` >= '{{date_from}}' AND `add_date` <= '{{date_to}}'".$where, "id DESC");

while($row = mysql_fetch_assoc($result)){
	$award_fields['module'] = $module;
	$award_fields['id'] = $row['id'];
	$award_fields['child_id'] = $row['child_id'];
	$award_fields['award_name'] = $awards[$row['award_id']];
	$grade_info = $math->grade_info($row['grade_id']);
	$skill_info = $math->skill_info($row['skill_id']);
	$award_fields['skill_number'] = $skill_info['number'];
	$award_fields['grade_number'] = $grade_info['number'];
	
	$templates->gen_loop_html($award_fields, $award_tmpl);
}


$out .= $templates->gen_module_html($replace_fields, "awards");