<?php
global $out;
$out = "";

$link_id = get_int('link_id');
$row = $query->select_obj_sql("text", "*", "link_id = '".$link_id."' AND lang = '".$lang."'");
$result_files = $query->select_sql("text_files", "*", "link_id = '".$link_id."' AND lang = '".$lang."'");

if($user_class->group_perm($module, 'admin')){
	$replace_fields['admin'] = " <div style=\"background: #FFF; position: absolute; width: 50px; height: 20px; top: 0px; left: 0px\">
        				<a href=\"".MANAGE_DIR."admin.php?module=".$module."&link_id=".$link_id."&action=edit&page=edit\" style=\"color: #8C0000; font-size: 13px\"><b>"._EDIT."</b></a>
        			</div> \n";
}
$replace_fields['text_head'] = $row->name;
$replace_fields['text_body'] = htmlspecialchars_decode($row->text);

$shareurl = rawurlencode($global_conf['location']."/text-".get_int('link_id').".html");
$replace_fields['share_url'] = $shareurl;

$at= $templates->split_template("attachments", "main");
$attachments = $templates->split_template("attachment_items", "main");
while($row_files = $query->obj($result_files)){	$attach['text_attachment_item'] = "<a href=\"upload/".$module."/".$row_files->file."\">
       										<img src=\"images/attachment.gif\" border=0 align=\"left\">".space(3)."
                  								".$row_files->title."
                  							</a>";
	$templates->gen_loop_html($attach, $attachments);
}

//************************ gallery *******************
$result = $query->select_sql("text_gallery", "*", "link_id = '".get_int('link_id')."'", "id ASC");
$n=0;
while($row = $query->obj($result)){
    $n++;
    @$size = getimagesize("upload/".$module."/gallery/thumb/".rawurldecode($row->pic));
	if($size[0] >= $size[1]){
		$width = 800;
		@$s = (int)$size[1]/(int)$size[0];
		@$height = $width * $s;
	}
	else{
		$height = 600;
		@$s = $height/$size[1];
		@$width = (int)($size[0]*$s);
	}
    $gallery .= " <div style=\"float: left; padding: 10px;\"> \n";
    $gallery .= popup_wndow("<img src=\"upload/".$module."/gallery/thumb/".($row->pic)."\" ".image_size("upload/".$module."/gallery/thumb/".rawurldecode($row->pic), 100, 75)." border=0>",
    			$module, "pic_view", "link_id=".get_int('link_id')."&img_id=".$row->id, "clear_post");
    $gallery .= " </div>\n";
    if($n == 5){
    	$n=0;
      	$gallery .= " <div style=\"clear: both;\"></div> \n";
	}
}
$replace_fields['gallery'] = $gallery;
//*****************************************************

$out .= $templates->gen_module_html($replace_fields, "main");

unset($GLOBALS['module_content']);
?>