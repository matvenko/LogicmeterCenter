<?php
$user_class->permission_end("users", 'all');
global $out;

$child_id = get_int('child_id') == 0 ? $user_class->current_child_id : get_int('child_id');
$child_info = $math->child_info($child_id);

$child_award_id = (int)$child_award_id == 0 ? get_int('child_award_id') : $child_award_id;
$award_info = $query->select_ar_sql("math_children_awards", "*", "id = ".(int)$child_award_id." AND child_id = ".(int)$child_info['id']);

$grade_info = $math->grade_info($award_info['grade_id']);
$skill_info = $math->skill_info($award_info['skill_id']);
$text_title = $literacy->text_title($award_info['text_id']);

$award_template = $query->select_ar_sql("math_awards_templates", "*", "id= ".(int)$award_info['award_id']);

$awars_text_replace = array("{{subject}}" => (int)$award_info['text_id'] == 0 ? _MATH : _LITERACY,
							"{{child_name}}" => $child_info['name'],
							"{{child_surname}}" => $child_info['surname'],
							"{{grade_number}}" => $grade_info['number'],
							"{{grade_name}}" => $grade_info['name'],
							"{{skill_number}}" => (int)$award_info['text_id'] == 0 ? $skill_info['number'] : "",
							"{{skill_name}}" => (int)$award_info['text_id'] ==0 ? $skill_info['name'] : $text_title,
							"{{text_title}}" => $text_title);

$award_text = str_replace(array_keys($awars_text_replace), $awars_text_replace, $award_template['template']);

$out = $award_text;