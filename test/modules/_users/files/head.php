<?php
global $out;
require_once("modules/math/files/functions.php");
$math = new math();

$skill_info = $math->skill_info(get_int('skill_id'));
$replace_fields['skill_name'] = $skill_info['number']." ".$skill_info['name'];

$grade_id = (int)$skill_info['grade_id'] == 0 ? get_int('grade_id') : $skill_info['grade_id'];
$grade_info = $math->grade_info($grade_id);
$replace_fields['grade_name'] = $grade_info['name'];
$replace_fields['grade_id'] = $grade_id;
$replace_fields['module'] = $module;

$out .= $templates->gen_module_html($replace_fields, "head");