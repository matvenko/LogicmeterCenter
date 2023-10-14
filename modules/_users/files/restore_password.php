<?php
global $out;

if($user_class->login_action()){
	header("Location: index.php");
	exit;
}

$replace_fields['module'] = $module;
$replace_fields['_RESTORE_PASSWORD'] = _RESTORE_PASSWORD;
$replace_fields['_EMAIL'] = _EMAIL;
$replace_fields['mail'] = input_form("mail", "textbox");
$replace_fields['_CONTINUE'] = _CONTINUE;
$replace_fields['_BIRTHDATE'] = _BIRTHDATE;
$replace_fields['_ENTER'] = _ENTER;
$children_birthdate_years = range(date("Y") - 3, date("Y") - 20);
$children_birthdate_years = array_combine($children_birthdate_years, $children_birthdate_years);
$children_birthdate_years[0] = _YEAR;
$birthdate_monthes = array(1 => _JANUARY, 2 => _FABRUARY, 3 => _MARCH, 4 => _APRIL, 5 => _MAY, 6 => _JUNE, 7 => _JULY, 8 => _AUGUST, 9 => _SEPTEMBER, 10 => _OCTOMBER, 11 => _NOVEMBER, 12 => _DECEMBER);
$birthdate_monthes[0] = _MONTH;
$birthdate_dayes = range(0, 31);
$birthdate_dayes[0] = _DAY;
$replace_fields['birthdate_year'] = input_form("birthdate_year", "select", "", $children_birthdate_years);
$replace_fields['birthdate_month'] = input_form("birthdate_month", "select", "", $birthdate_monthes);
$replace_fields['birthdate_day'] = input_form("birthdate_day", "select", "", $birthdate_dayes);

$out = $templates->gen_module_html($replace_fields, "restore_password");