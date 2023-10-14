<?php
$user_class->permission_end($module, 'user');
global $out;
$first_child = $query->select_ar_sql("math_children", "id", "user_id = ".(int)$user_class->current_user_id, "id ASC", "0,1");
$child_id = set_var(post_int('child_id'), $first_child['id']);

$common_where = "user_id = ".(int)$user_class->current_user_id." AND child_id = ".(int)$child_id;
$date_from = set_var(post('date_from'), current_date(strtotime("-1 month")));
$date_to =  set_var(post('date_to'), current_date());
$children = select_items("math_children", "id", array("name", "surname"), "user_id = ".(int)$user_class->current_user_id." ORDER BY id ASC");

$ar_month[1] = _JANUARY;
$ar_month[2] = _FABRUARY;
$ar_month[3] = _MARCH;
$ar_month[4] = _APRIL;
$ar_month[5] = _MAY;
$ar_month[6] = _JUNE;
$ar_month[7] = _JULY;
$ar_month[8] = _AUGUST;
$ar_month[9] = _SEPTEMBER;
$ar_month[10] = _OCTOMBER;
$ar_month[11] = _NOVEMBER;
$ar_month[12] = _DECEMBER;

if(get('page') == "statistics" || get('page') == "profile"){
	include("modules/".$module."/files/statistics_search.php");
}
