<?php
$query->group_perm($module, 'add_comment');
global $out, $topic_conf;
$out = '';

if(get_int('comment_id') !== 0 && $query->group_perm($module, 'edit_comment')){	$row_edit = $query->select_obj_sql("topic_comments", "comment", "id = ".get_int('comment_id')."");
	$edit_value['comment_text'] = $row_edit->comment;}

$out .= "<center><div style=\"width: 550px;\">\n";
$out .= "<div style=\"float:right;\">\n";
$out .= popup_close("<img src=\"images/close_popup.png\" border=\"0\" style=\"position: absolute\">\n".space(25,10))."\n";
$out .="</div>\n";

$max_comment_text = $topic_conf['max_comment_text'];
if($query->group_perm($module, 'delete_topic')){
	$max_comment_text = 0;
}
$out .= "<div class=\"login1\">\n";
$out .= "	<form action=\"clear_post.php?module=".$module."&topic_id=".get_int('topic_id')."&comment_id=".get_int('comment_id')."&parent_comment_id=".get_int('parent_comment_id')."&id=".get_int('id')."\" method=\"post\">\n";
$out .= "	<div style=\"clear: both; padding-top: 15px;\">\n";
$out .= "<font class=\"error\">"._TEXT_MUST." ".$max_comment_text.""._S."</font>\n";
$out .= input_form("comment_text", "textarea", $edit_value, "", "", "add_answer_text", "onKeyup=\"textCounter('comment_text',".$max_comment_text.", '"._OVER_TEXT_LIMIT."');\" onKeydown=\"textCounter('comment_text',".$max_comment_text.", '"._OVER_TEXT_LIMIT."');\"");
$out .= "<input readonly type=text id=\"comment_text_counter\" size=3 maxlength=3 value=\"".$max_comment_text."\">\n";
$out .= "	</div>\n";

$out .= "	<div style=\"clear: both; padding: 10px;\">\n";
$out .= "<input type=\"submit\" value=\""._SEND."\" name=\"add_comment\" class=\"button\">\n";
$out .= "	</div>\n";

if(get_int('parent_comment_id') !== 0){	$out .= "<div style=\"clear: both; padding: 10px; text-align: left;\">\n";
	$out .= "<div><b>"._ANSWER_TO_COMMENT."</b></div>\n";
	$out .= "<div class=\"comment_answer\">\n";
	$row_tmpl_comment = $query->select_obj_sql("topic_template", "*", "topic_id = ".get_int('topic_id')." AND topic_comment = 2", "priority ASC", "0,1");
    $template_comment = htmlspecialchars_decode($row_tmpl_comment->design);
    $comment = $template_comment;
    $row_comment = $query->select_obj_sql("topic_comments", "*", "id = ".get_int('parent_comment_id')."");
    $row_user = $query->select_obj_sql("users", "*", "id = ".(int)$row_comment->user_id."");
	if((int)$row_comment->user_id !== 0){
		$comment = str_replace("{{comment_user_name_surname}}", "<a href=\"\">".$row_user->name." ".$row_user->surname."</a>", $comment);
	}
	$comment = str_replace("{{comment_text}}", $row_comment->comment, $comment);
	$comment = eregi_replace("{{([[:print:]]+)}}", "", $comment);
	$out .= $comment;
	$out .= "</div>\n";
	$out .= "</div>\n";}

$out .= "</div>\n";

$out .= "</div>\n";
$out .= form_end();

echo $out;

?>