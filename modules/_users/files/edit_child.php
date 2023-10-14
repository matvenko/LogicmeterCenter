<?php
$user_class->permission_end($module, 'user');
global $out;

$birthdate_years = range(date("Y") - 3, date("Y") - 20);
$birthdate_years = array_combine($birthdate_years, $birthdate_years);
$birthdate_monthes = array(1 => _JANUARY, 2 => _FABRUARY, 3 => _MARCH, 4 => _APRIL, 5 => _MAY, 6 => _JUNE, 7 => _JULY, 8 => _AUGUST, 9 => _SEPTEMBER, 10 => _OCTOMBER, 11 => _NOVEMBER, 12 => _DECEMBER);
$birthdate_monthes[0] = _MONTH;
$replace_fields['parent_birthdate_month'] = input_form("parent_birthdate_month", "select", $edit_value, $birthdate_monthes);
$birthdate_dayes = range(0, 31);
$birthdate_dayes[0] = _DAY;


$children_tmpl = $templates->split_template("children", "children");
$edit_value = $math->child_info(get_int('child_id'));

$replace_fields['star'] = star();
$replace_fields['_NAME'] = _NAME;
$replace_fields['_SURNAME'] = _SURNAME;
$replace_fields['_BIRTHDATE'] = _BIRTHDATE;
	
$replace_fields['name'] = input_form("name", "text", $edit_value);
$replace_fields['surname'] = input_form("surname", "text", $edit_value);
$children_birthdate_years[0] = _YEAR;
$child_date = explode('-', $edit_value['birthdate']);
$replace_fields['birthdate_year'] = input_form("birthdate_year", "select", $child_date[0], $birthdate_years);
$replace_fields['birthdate_month'] = input_form("birthdate_month", "select", (int)$child_date[1], $birthdate_monthes);
$replace_fields['birthdate_day'] = input_form("birthdate_day", "select", (int)$child_date[2], $birthdate_dayes);
	
$out = $templates->gen_module_html($replace_fields, "edit_child");
