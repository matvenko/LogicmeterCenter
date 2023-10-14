<?php
$user_class->permission_end($module, 'user');
global $out;
//*** search fields
$replace_fields['statistic_page'] = "statistics_skill_level_literacy";
$templates->module_ignore_fields[] = "grade_theme";
$templates->module_ignore_fields[] = "grade";

if(get('page') == "statistics"){
	$templates->module_ignore_fields[] = "default";
}
include("modules/".$module."/files/statistics_init.php");

$replace_fields['_GRADE'] = _GRADE;
$replace_fields['_SKILLS'] = _SKILLS;
$replace_fields['_AVARAGE_POINTS'] = _AVARAGE_POINTS;
$replace_fields['_DONE_TESTS'] = _DONE_TESTS;
$replace_fields['_TRUE_ANSWERS'] = _TRUE_ANSWERS;
$replace_fields['_DONE_SKILLS'] = _DONE_SKILLS;
$replace_fields['_SUM_POINTS'] = _SUM_POINTS;
$replace_fields['_PROGRESS'] = _PROGRESS;

//**** search ****
if(post_int('grade_id') !== 0){
	$common_where .= " AND grade_id = ".post_int('grade_id');
}

//***** skills *****
$skills_tmpl = $templates->split_template("skills", "statistics_skill_level_literacy");

$result = $query->select_sql("math_user_tests", "literacy_skill_id,
												SUM(answer_point) AS answer_points,
												COUNT(id) AS done_tests,
												SUM(true_answer) AS true_answers", $common_where." AND text_id != 0", "", "", "user_id, child_id, literacy_skill_id");

$literacy_skills = select_items("math_literacy_skills", "id", "name", "del = 0");
$n = 0;
while($row = mysql_fetch_assoc($result)){
	if(!array_key_exists($row['literacy_skill_id'], $literacy_skills)) continue;
	$n++;
	$skills_fields['skill_number'] = $n;
	$skills_fields['skill_name'] = $literacy_skills[$row['literacy_skill_id']];
	$skills_fields['avarage_point'] = round($row['answer_points'] / $row['done_tests'], 2);
	$skills_fields['done_tests'] = $row['done_tests'];
	$skills_fields['true_answers'] = round($row['true_answers'] / $row['done_tests'] * 100);
	$skills_fields['progress_color'] = progress_color($skills_fields['true_answers']);
	
	$skill_progress[$row['literacy_skill_id']] = $skills_fields['true_answers'];
	
	$templates->gen_loop_html($skills_fields, $skills_tmpl);
}


//*** chart labels
$chart_tmpl = $templates->split_template("chart_data", "statistics_skill_level_literacy");
foreach($literacy_skills as $skill_id => $skill_name){
	$labels .= ",\"".$skill_name."\"";
	$chart_data[] = (int)$skill_progress[$skill_id];
}
$replace_fields['chart_data'] = implode(",", $chart_data);
$replace_fields['labels'] = ltrim($labels, ',');

$out .= $templates->gen_module_html($replace_fields, "statistics_skill_level_literacy");
unset($templates->module_content);
