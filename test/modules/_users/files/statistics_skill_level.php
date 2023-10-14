<?php
$user_class->permission_end($module, 'user');
global $out;
//*** search fields
$replace_fields['statistic_page'] = "statistics_skill_level";
$templates->module_ignore_fields[] = "grade_theme";
$templates->module_ignore_fields[] = "grade";

if(get('page') == "profile"){
	$templates->module_ignore_fields[] = "default";
}
include("modules/".$module."/files/statistics_init.php");

$replace_fields['_GRADE'] = _GRADE;
$replace_fields['_SKILL'] = _SKILL;
$replace_fields['_DONE_SKILLS'] = _DONE_SKILLS;
$replace_fields['_SUM_POINTS'] = _SUM_POINTS;
$replace_fields['_PROGRESS'] = _PROGRESS;

//***** skills *****
$grade_tmpl = $templates->split_template("grades", "statistics_skill_level");
$pie_grade_tmpl = $templates->split_template("pie_grades", "statistics_skill_level");
$parent_skills_tmpl = $templates->split_template("parent_skills", "statistics_skill_level");
$skills_tmpl = $templates->split_template("skills", "statistics_skill_level");
$grades = $math->grades_get(1);
$user_grade_progress_info = select_items("math_user_grade_info", "grade_id", "smart_point", $common_where);

//**** search
if(post_int('grade') !== 0){
	$common_where .= " AND grade_id = ".post_int('grade');
	$grade_info = $math->grade_info(post_int('grade'));
	$grades = array($grade_info['number'] => $grade_info);
}

foreach ($grades as $grade_number => $grade_info){
	$grade_fields['grade_color'] = $math->grade_colors_get($grade_number);
	$grade_fields['grade_number'] = $grade_number;
	$grade_fields['grade_name'] = $grade_info['name'];
	$grade_fields['grade_id'] = $grade_info['id'];
	
	$skills_body = "";
	$skills_parents = $math->skills_get($grade_info['id'], 0);
	$user_skill_progress_info = select_items("math_user_skill_info", "skill_id", "smart_point", $common_where." AND grade_id = ".(int)$grade_info['id']);
	$n = 0; $all_done_skills = 0; $all_skills_amount = 0;
	$grade_fields['parent_skills'] = "";
	foreach ($skills_parents as $parent_id => $skill_parent_info){
		$n++;
		$skill_title_fields['id'] = $parent_id;
		$skill_title_fields['skill_title'] = $skill_parent_info['name'];
		$skill_title_fields['grade_id'] = $skill_parent_info['grade_id'];
		$skill_title_fields['priority'] = $skill_parent_info['priority'];
		$skill_title_fields['parent_id'] = $parent_id;
	
		$skills = $math->skills_get($grade_info['id'], $parent_id);
		$skill_title_fields['skills'] = "";
		$parent_done_skills = 0; $parent_skills_amount = 0; $skill_point_sum = 0;
		foreach((array)$skills as $skill_id => $skill_info){
			//$n++;
			$skill_fields['module'] = $module;
			$skill_fields['parent_id'] = $parent_id;
			$skill_fields['skill_id'] = $skill_id;
			$skill_fields['skill_number'] = $skill_info['number'];
			$skill_fields['skill_name'] = $skill_info['name'];
			
			$skill_fields['done_skills'] = "";
			$skill_fields['sum_point'] = $user_skill_progress_info[$skill_id];;
			$skill_fields['progress'] = (int)$user_skill_progress_info[$skill_id];
			$skill_point_sum += $user_skill_progress_info[$skill_id];
					
			$skill_title_fields['skills'] .= $templates->gen_html($skill_fields, $skills_tmpl['skills'], 0);
			
			if(array_key_exists($skill_id, (array)$user_skill_progress_info)){
				$all_done_skills ++;
				$parent_done_skills ++;
			}
			$all_skills_amount ++;
			$parent_skills_amount ++;
		}
	
		$skill_title_fields['done_skills'] = $parent_done_skills;
		$skill_title_fields['sum_point'] = $parent_done_skills == 0 ? 0 : round($skill_point_sum / $parent_done_skills, 2);
		$skill_title_fields['progress'] = $parent_skills_amount == 0 ? 0 : round($skill_point_sum / $parent_skills_amount, 2);
		
		$grade_fields['parent_skills'] .= $templates->gen_html($skill_title_fields, $parent_skills_tmpl['parent_skills'], 0);
	}
	
	$grade_fields['done_skills'] = $all_done_skills;
	$grade_fields['sum_point'] = $all_done_skills == 0 ? 0 : round($user_grade_progress_info[$grade_info['id']] / $all_done_skills, 2);
	$grade_fields['progress'] = $all_skills_amount == 0 ? 0 : round($user_grade_progress_info[$grade_info['id']] / $all_skills_amount, 2);
	
	$templates->gen_loop_html($grade_fields, $grade_tmpl);
	$templates->gen_loop_html($grade_fields, $pie_grade_tmpl);
}
$replace_fields['undone_progress'] = 70;
$replace_fields['_UNDONE_SKILLS'] = _UNDONE_SKILLS;
//$skills_fields['skills_body'] = $skills_body;

//$templates->gen_loop_html($skills_fields, $skills_tmpl);

$out .= $templates->gen_module_html($replace_fields, "statistics_skill_level");
unset($templates->module_content);
