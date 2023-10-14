<?php
global $tmpl, $out, $sql_db_l, $sql_db;

if($user_class->login_action()){
	header("Location: index.php");
	exit;
}

$templates->module_ignore_fields[] = "choose_group";

//**** register head
$_GET['link_id'] = 13;
include("modules/text/files/text.php");
$replace_fields['registration_head'] = $out;

include("modules/".$module."/files/choose_class.php");
$replace_fields['choose_class'] = $out;
unset($templates->module_content);

$replace_fields['module'] = $module;
$replace_fields['star'] = star();
$replace_fields['_BE_MEMBER'] = _BE_MEMBER;
$replace_fields['_MEMBERSHIP'] = _MEMBERSHIP;
$replace_fields['_CHOOSE_PERIOD'] = _CHOOSE_PERIOD;
$replace_fields['_CHILDREN_AMOUNT'] = _CHILDREN_AMOUNT;
$replace_fields['_PARENT_INFO'] = _PARENT_INFO;
$replace_fields['_NAME'] = _NAME;
$replace_fields['_SURNAME'] = _SURNAME;
$replace_fields['_EMAIL_PARENTS'] = _EMAIL_PARENTS;
$replace_fields['_RETYPE_EMAIL_PARENTS'] = _RETYPE_EMAIL;
$replace_fields['_ADDRESS'] = _ADDRESS;
$replace_fields['_MOBILE'] = _MOBILE;
$replace_fields['_PASSWORD'] = _PASSWORD;
$replace_fields['_RE_PASSWORD'] = _RE_PASSWORD;
$replace_fields['_CHILDREN_INFO'] = _CHILDREN_INFO;
$replace_fields['_REGISTRATION'] = _REGISTRATION;
$replace_fields['_NO_CHILDREN_AMOUNT'] = _NO_CHILDREN_AMOUNT;
$replace_fields['_ADDRESS_INFO'] = _ADDRESS_INFO;
$replace_fields['_ZIP_CODE'] = _ZIP_CODE;
$replace_fields['_SCHOOL'] = _SCHOOL;
$replace_fields['_CHOOSE_GROUP'] = _CHOOSE_GROUP;
$replace_fields['_GRADE'] = _GRADE;
$replace_fields['_TERMS'] = _TERMS;

$replace_fields['math'] = input_form("math", "checkbox", 1);
$replace_fields['literacy'] = input_form("literacy", "checkbox", $edit_value);

$periods = array(1 => "1 "._MONTH, 6 => "6 "._MONTH, 12 => "1 "._YEAR);
$edit_value['period'] = 1;
$periods[0] = "hide";
$replace_fields['period'] = input_form("period", "select", $edit_value, $periods, "", "month");
$children_amounts = range(0, 12);
$edit_value['children_amount'] = 1;
$children_amounts[0] = "hide";
$replace_fields['children_amount'] = input_form("children_amount", "select", $edit_value, $children_amounts, "", "month");
$parameters['period'] = 1;
$parameters['children_amount'] = 1;
$parameters['math'] = 1;

//**** parent info
$replace_fields['parent_name'] = input_form("parent_name", "text", $edit_value);
$replace_fields['parent_surname'] = input_form("parent_surname", "text", $edit_value);
$parent_birthdate_years = range(date("Y") - 18, date("Y") - 90);
$parent_birthdate_years = array_combine($parent_birthdate_years, $parent_birthdate_years);
$parent_birthdate_years[0] = _YEAR;
$replace_fields['parent_birthdate_year'] = input_form("parent_birthdate_year", "select", $edit_value, $parent_birthdate_years);
$birthdate_monthes = array(1 => _JANUARY, 2 => _FABRUARY, 3 => _MARCH, 4 => _APRIL, 5 => _MAY, 6 => _JUNE, 7 => _JULY, 8 => _AUGUST, 9 => _SEPTEMBER, 10 => _OCTOMBER, 11 => _NOVEMBER, 12 => _DECEMBER);
$birthdate_monthes[0] = _MONTH;
$replace_fields['parent_birthdate_month'] = input_form("parent_birthdate_month", "select", $edit_value, $birthdate_monthes);
$birthdate_dayes = range(0, 31);
$birthdate_dayes[0] = _DAY;
$replace_fields['parent_birthdate_day'] = input_form("parent_birthdate_day", "select", $edit_value, $birthdate_dayes);
$replace_fields['parent_mail'] = input_form("parent_mail", "text", $edit_value);
$replace_fields['retype_parent_mail'] = input_form("retype_parent_mail", "text", $edit_value);
$replace_fields['parent_password'] = input_form("parent_password", "password", $edit_value);
$replace_fields['parent_re_passowrd'] = input_form("parent_re_passowrd", "password", $edit_value);
$replace_fields['parent_mobile'] = input_form("parent_mobile", "text", $edit_value);
$replace_fields['address'] = input_form("address", "text", $edit_value);
$replace_fields['info_icon'] = icon('info');
$replace_fields['zip_code'] = input_form("zip_code", "text", $edit_value);
$replace_fields['school'] = input_form("school", "text", $edit_value);

//**** child info
$replace_fields['_NAME'] = _NAME;
$replace_fields['_SURNAME'] = _SURNAME;
$replace_fields['_BIRTHDATE'] = _BIRTHDATE;
$replace_fields['star'] = star();
$replace_fields['child_n'] = $i;
$replace_fields['child_name'] = input_form("child_name", "text");
$replace_fields['child_surname'] = input_form("child_surname", "text");
$children_birthdate_years = range(date("Y") - 3, date("Y") - 20);
$children_birthdate_years = array_combine($children_birthdate_years, $children_birthdate_years);
$children_birthdate_years[0] = _YEAR;
$replace_fields['child_birthdate_year'] = input_form("child_birthdate_year", "select", "", $children_birthdate_years, "width: 65px", "year_month year", "data-num=\"".$i."\"");
$replace_fields['child_birthdate_month'] = input_form("child_birthdate_month", "select", "", $birthdate_monthes, "width: 110px", "year_month month", "data-num=\"".$i."\"");
$replace_fields['child_birthdate_day'] = input_form("child_birthdate_day", "select", "", $birthdate_dayes, "width: 60px", "day");



$replace_fields['terms_and_rules'] = input_form("terms_and_rules", "checkbox");
$replace_fields['_I_AGREE'] = _I_AGREE;
$replace_fields['_TERMS_AND_RULES'] = _TERMS_AND_RULES;

$out = $templates->gen_module_html($replace_fields, "register");