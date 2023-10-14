<?php
$user_class->permission_end($module, 'user');
global $out, $pages;

$replace_fields['module'] = $module;
$replace_fields['_PERSONAL_INFO'] = _PERSONAL_INFO;
$replace_fields['_CHILDREN'] = _CHILDREN;
$replace_fields['_PAYMENT_HISTORY'] = _PAYMENT_HISTORY;
$replace_fields['_STATISTICS'] = _STATISTICS;
$replace_fields['_OVERVIEW'] = _OVERVIEW;
$replace_fields['_SKILL_LEVEL'] = _SKILL_LEVEL;
$replace_fields['_MATH'] = _MATH;
$replace_fields['_LITERACY'] = _LITERACY;

include("modules/".$module."/files/".load_page(get('type'), $pages, 'children', 0).".php");
unset($templates->module_content);

$replace_fields['statistics_content'] = $out;
$replace_fields[get('type')] = "statistics_menu_active";

$out = $templates->gen_module_html($replace_fields, "statistics");