<?php
$user_class->permission_end($module, 'user');
global $out;

if(is_post('statistic_search')){
	$_POST['statistic_search'] = false;
	echo request_callback("ok_message", "", "update_statistic", $_POST);
	exit;
}

if(get('action') == "show_grade_themes"){
	$themes = $math->skills_get(get_int('grade_id'), 0);
	foreach($themes as $theme_id => $theme_info){
		$theme_select[$theme_id] = $theme_info['name'];
	}
	echo input_form("theme", "select", "", $theme_select);
	exit;
}

$replace_fields['_DATE'] = _DATE;
$replace_fields['_FROM'] = _FROM;
$replace_fields['_TO'] = _TO;
$replace_fields['_CHILD'] = _CHILD;
$replace_fields['_GRADE'] = _GRADE;
$replace_fields['_THEME'] = _THEME;
$replace_fields['_UPDATE'] = _UPDATE;
$replace_fields['module'] = $module;

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
$replace_fields['themes'] = input_form("theme", "select", $_POST);

$out = $templates->gen_module_html($replace_fields, "statistics_search");