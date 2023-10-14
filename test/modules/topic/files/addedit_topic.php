<?php
global $html_out, $topic_conf;
$user_class->permission_end($module, 'edit_topic');
//include('blocks/ajax_calendar/index.php');
include("modules/".$module."/files/show_calendar.php");
if(get('action') == "show_calendar"){
	$var = set_var(get('var'), '');
	$div = set_var(get('div'), '');
	$now_date = set_var(get('now_date'), '');
	$year_b = set_var(get('year_b'), 15);
	$year_f = set_var(get('year_f'), 15);
	echo calendar_view($var, $now_date, $year_b, $year_f);
	exit;
}

$row_temp = $query->select_obj_sql("topic_text", "topic_id", "id = '".get_int('id')."'");
$row_head = $query->select_obj_sql("topic_pages", "name", "id = '".$row_temp->topic_id."'");

$edit_value['date'] = date("Y-m-d", time());
if(get_int('id') !== 0){
	$edit_value = $query->select_ar_sql("topic_text", "*", "id = ".get_int('id')."");
    $edit_value['category'] = $row_edit['cat_id'];

    $row_pic = $query->select_obj_sql("topic_gallery", "pic", "topic_text_id = ".get_int('id')." AND `default` = 1");
    $edit_value['pic'] = $row_pic->pic;
    
    if((int)$topic_conf['multi_dates'] == 1){
    	$start_time = explode(":", $edit_value['start_time']);
    	$edit_value['start_time_h'] = $start_time[0];
    	$edit_value['start_time_m'] = $start_time[1];
    	
    	$multi_dates = select_items("topic_multi_dates", "id", "date", "topic_text_id = ".get_int('id'));
    	$edit_value['multi_dates'] = implode("\n", $multi_dates)."\n";
    }
}

$field_style = "width: 550px; height: 25px";
$form_style = "width: 540px";
$text_style = "width: 150px; height: 25px";

$out .= form_start("topic_id=".get_int('topic_id')."&id=".get_int('id'), "admin", 1, "post", "topic");

$out .= "<table class=\"admin_table\" style=\"margin-top: 30px\">\n";
$out .= "<tr>\n";
$out .= "	<td class=\"table_td2\">"._TITLE."</td>\n";
$out .= "	<td class=\"table_td2\">\n";
$out .= 	input_form("title", "textbox", $edit_value, "", $form_style)."</td>\n";
$out .= "</tr>\n";
$out .= "<tr>\n";
$out .= "<td class=\"table_td1\">"._CATEGORY."</td>\n";
$result_category = $query->select_sql("topic_categories", "*", "topic_id = ".get_int('topic_id')."", "id DESC");
while($row_category = $query->obj($result_category)){
	$categories[$row_category->name] = $row_category->id;
}
$out .= "<td class=\"table_td1\">".
			input_form("category", "select", $edit_value, $categories)."</td>\n";
$out .= "</tr>\n";
if($topic_conf['multi_dates'] == 1){
	$edit_value['multi_dates'] = $edit_value['multi_dates'] == "" ? current_date()."\n" : $edit_value['multi_dates']; 
	
	$out .= "<tr>\n";
	$out .= "<td class=\"table_td2\">"._START_TIME."</td>\n";
	$out .= "<td class=\"table_td2\">\n";
	$out .= 	input_form("start_time_h","textbox", $edit_value, "", "width: 20px").": ".input_form("start_time_m","textbox", $edit_value, "", "width: 20px");
	$out .= "</td>\n";
	$out .= "</tr>\n";
	$out .= "<tr>\n";
	$out .= "<td class=\"table_td2\">"._DATE."</td>\n";
	$out .= "<td class=\"table_td2\">\n";
	$out .= 	"<span style=\"float: left\">".input_form("multi_dates","textarea", $edit_value, "", "width: 120px; height: 122px")."</span>";
	$out .= 	"<span id=\"calendar_block\" style=\"padding-left: 10px; float: left\">".calendar_view($var, 'multi_dates_block')."</span>";
	$out .= "</td>\n";
	$out .= "</tr>\n";
	
	$out .= "<script type=\"text/javascript\">\n";
	$out .= "	$(\".add_multi_date\").click(function(){
					$(\"#multi_dates\").val($(\"#multi_dates\").val() + this.id + '\\n');
				})\n";
	$out .= "</script>\n";
}
else{
	$out .= "<tr>\n";
	$out .= "<td class=\"table_td2\">"._DATE."</td>\n";
	$out .= "<td class=\"table_td2\">\n";
	$out .= 	input_form("date","calendar", $edit_value)."</td>\n";
	$out .= "</tr>\n";
}
$out .= "<tr>\n";
$out .= "<td class=\"table_td1\">"._IMAGE."</td>\n";
$out .= "<td class=\"table_td1\">\n";
$main_picture = $query->select_ar_sql("topic_gallery", "*", "topic_text_id = ".get_int('id')." AND `default` = 1");
if(get_int('id') !== 0){
	if(is_file("upload/".$module."/gallery/thumb/".$main_picture['pic'])){
		$out .= "<img src=\"upload/".$module."/gallery/thumb/".$main_picture['pic']."\" height=\"40\">";
	}
	$out .= " "._PICTURE.": <input type=\"file\" name=\"pic\" size=\"20\"> \n";
}
else{
       $out .= " "._PICTURE.": <input type=\"file\" name=\"pic\" size=\"20\"> \n";
}
$out .= "</td>\n";
$out .= "</tr>\n";
$out .= "<tr>\n";
$html_out['editorarea'] .= editorarea(2, 700, 250);
$out .= "<td class=\"table_td2\" colspan=2 align=\"center\">"._TEXT."<BR>\n";
$out .= 	input_form("text", "textarea_editor", $edit_value, 1)."</td>\n";
$out .= "</tr>\n";
$out .= "<tr>\n";
$out .= "<td class=\"table_td1\" colspan=2 align=\"center\">"._SHORT_TEXT."<BR>\n";
$out .= 	input_form("short_text", "textarea_editor", $edit_value, 2)."</td>\n";
$out .= "</tr>\n";
$out .= "<tr>\n";
$out .= "<td class=\"table_td1\" colspan=2>
			<input type=\"submit\" value=\""._ADD_TOPIC."\" name=\"add_topic_text\"></td>\n";
$out .= form_end();
$out .= "</tr>\n";
$out .= "</table>\n";

//************ attachment ************************
if(get_int('id') !== 0){
$out .= "<div style=\"width: 750px\">\n";
$out .= "<a name=\"gallery\"></a>\n";
$out .= form_start("topic_id=".get_int('topic_id')."&id=".get_int('id'), "admin", 1, "post");

$out .= space(5, 20)."<br><div class=\"admin_head\" style=\"clear: both; width: 750px;\">"._ATTACHMENT."</div>\n";
$result = $query->select_sql("topic_files", "*", "topic_text_id = '".get('id')."'", "id ASC");
while($row = $query->obj($result)){
	$div_style = $templates->change_style();
	$out .= " <div class=\"".$div_style."\" style=\"clear: both; padding-top: 10px; width: 700px;\"> \n";
    $out .= "<a href=\"upload/".$module."/".$row->file."\">\n";
    $out .= "<img src=\"images/attachment.gif\" border=\"0\" align=\"middle\">\n";
    $out .= "".$row->title."</a>".space(20,5)."\n";
    $out .= " 	<a href=\"javascript: yes_no('post.php?module=".$module."&action=attach_delete&id=".$row->id."&sc_id=".$row->link_id."', '"._YES_NO_DELETE."')\"><font class=\"edit\">"._DELETE."</font></a>";

    $out .= " </div>\n";
}

$out .= "<div class=\"table_td1\" style=\"clear: both; width: 750px;\">\n";
$out .= 	" "._TITLE.": <input type=\"text\" name=\"attach_title\" style=\"width: 250px\"><br>\n";
$out .= 	" "._FILE.": <input type=\"file\" name=\"attach_file\" style=\"width: 250px\"></div>\n";

$out .= "<div style=\"clear: left; \">
			<input type=\"submit\" value=\""._ADD_FILE."\" name=\"add_topic_attach\"></div>\n";

$out .= form_end();
$out .= " </div>\n";
}
//*********************************************

//************ gallery ************************
if(get_int('id') !== 0){
$out .= "<div style=\"width: 750px\">\n";
$out .= "<a name=\"gallery\"></a>\n";
$out .= form_start("topic_id=".get_int('topic_id')."&id=".get_int('id'), "admin", 1, "post");

$out .= space(5, 20)."<br><div class=\"admin_head\" style=\"clear: both; width: 750px;\">"._GALLERY."</div>\n";
$result = $query->select_sql("topic_gallery", "*", "topic_text_id = '".get('id')."'", "id ASC");
$n=0;
$out .= "<div class=\"table_td1\" style=\"clear: both; width: 750px;\">\n";
while($row = $query->obj($result)){
	$n++;
	$out .= " <div class=\"table_td2\" style=\"float: left; padding: 5px;\"> \n";
	$out .= " 	<a href=\"".DOC_ROOT."upload/".$module."/gallery/".$row->pic."\"><img src=\"".DOC_ROOT."upload/".$module."/gallery/".$row->pic."\" ".image_size("upload/".$module."/gallery/thumb/".rawurldecode($row->pic), 50, 50)." border=0></a><br />";
	$out .= " 	<a href=\"javascript: yes_no('post.php?module=".$module."&action=pic_delete&id=".$row->id."&sc_id=".$row->link_id."', '"._YES_NO_DELETE."')\"><font class=\"edit\">"._DELETE."</font></a>";
	$out .= " </div>\n";
	if($n == 5){
		$n=0;
		$out .= " <div style=\"clear:both;\"></div> \n";
	}
}
$out .= " </div> \n";

$out .= "<div class=\"table_td1\" style=\"clear: both; width: 750px;\">\n";
$out .= 	" "._PICTURE.": <input type=\"file\" name=\"gallery_pic\" size=\"20\"><BR>
			"._MAIN_PICTURE." <input name=\"main_pictupe\" type=\"checkbox\" value=\"1\">
		</div>\n";

$out .= "<div style=\"clear: left; \">
			<input type=\"submit\" value=\""._ADD_PIC."\" name=\"add_topic_gallery\"></div>\n";

$out .= form_end();
$out .= " </div> \n";
}
//*********************************************

$html_out['module'] = $out;
unset($out);
?>