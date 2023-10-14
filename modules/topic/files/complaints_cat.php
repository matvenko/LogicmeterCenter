<?php
global $out, $topic_conf, $topic_permission;
$out = '';

$row_head = $query->select_obj_sql("topic_pages", "name, id", "id = '".get_int('topic_id')."'");
$row_tmpl_head = $query->select_obj_sql("topic_template", "*", "topic_id = ".get_int('topic_id')." AND topic_comment = 5", "priority ASC", "0,1");
$row_tmpl_footer = $query->select_obj_sql("topic_template", "*", "topic_id = ".get_int('topic_id')." AND topic_comment = 7", "priority ASC", "0,1");

$out .= topic_head(htmlspecialchars_decode($row_tmpl_head->design));

$out .= "<table border=\"0\" width=\"615\" cellspacing=\"0\" cellpadding=\"0\">\n";
$out .= "	<tr>\n";
$result = $query->select_sql("topic_categories", "*", "topic_id = '".get_int('topic_id')."' AND parent_id = 0");
$n=0;
while($row = $query->obj($result)){
	$n++;
	$out .= "<td valign=\"top\">\n";
	$out .= "	<div style=\"padding-bottom: 15px;\">\n";
	$out .= "		<div class=\"complaint_category\">".$row->name."</div>\n";
	$result_sub = $query->select_sql("topic_categories", "*", "topic_id = '".get_int('topic_id')."' AND parent_id = ".(int)$row->id."");
    $out .= "		<div class=\"complaint_sub_category2\">\n";
    $sub_cats = "";
    while($row_sub = $query->obj($result_sub)){    	$sub_cats .= ", <a href=\"index.php?module=".$module."&page=main&topic_id=".get_int('topic_id')."&main_cat_id=".$row->id."&cat_id=".$row_sub->id."\">
    					".$row_sub->name."</a>\n";
    }
    $out .= substr($sub_cats, 2);
    $out .= "		</div>\n";
    $out .= "	</div>\n";
	$out .= "</td>\n";
	if($n == 3){		$out .= "</tr>\n";
		$out .= "<tr>\n";
		$n=0;	}
}
$out .= "	</tr>\n";
$out .= "</table>\n";

$out .= htmlspecialchars_decode($row_tmpl_footer->design);

//********** global replace ******************
$out = str_replace("{{topic_head}}", $row_head->name, $out);
$out = str_replace("{{topic_id}}", get_int('topic_id'), $out);
$out = str_replace("{{global_cat_id}}", get_int('cat_id'), $out);
$out = str_replace("{{global_main_cat_id}}", get_int('main_cat_id'), $out);
$out = str_replace("{{lang}}", $lang, $out);
$out = str_replace("{{module}}", $module, $out);

$out = str_replace("[text_".$lang."]", "&nbsp;}}\n", $out);
$out = str_replace("[/text_".$lang."]", "\n{{&nbsp;", $out);

$out = eregi_replace("{{([[:print:]]+)}}", "", $out);
//********************************************
?>