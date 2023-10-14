<?php
$user_class->permission_end($module, 'admin');
$link_id = get_int('link_id');
if(is_post(get_int('link_id'))) {
	$query->update_sql("text", array("text" => htmlspecialchars(post_admin('text'))), "link_id = ".get_int('link_id')." AND lang = '".$lang."'");
	header("Location: ".DOC_ROOT."index.php?module=text&link_id=".get_int('link_id')."");
	exit;
}

if(is_post('add')){
        $link_id = $query->max_value("text", "link_id") + 1;
        $query->insert_sql("text", array("link_id" => $link_id, "description" => post('description'), "lang" => post('text_lang'), "name" => post('text_name')));
        header("Location: admin.php?module=".$module."");
        exit;
}

if(is_post('edit')){
        $query->update_sql("text", array("lang" => post('text_lang'), "description" => post('description'), "name" => post('text_name')), "id = '".get_int('id')."'");
        header("Location: admin.php?module=".$module."");
        exit;
}

if(get('action') == 'delete'){
        $query->delete_sql("text", "id = '".get_int('id')."'");
        header("Location: admin.php?module=".$module."");
        exit;
}

if(is_post('add_file')){
        $uploadFile = file_upload("text_file", time(), "upload/".$module);
        if($uploadFile !== false){
                move_uploaded_file($_FILES['text_file']['tmp_name'], $uploadDir.$uploadFile);
                $query->insert_sql("text_files", array("lang" => $lang,
                                                      "link_id" => get_int('link_id'),
                                                      "title" => post('title'),
                                                      "file" => RawUrlEncode($uploadFile)));
        }
        header("Location: admin.php?module=".$module."&page=edit&action=edit&link_id=".get_int('link_id')."");
        exit;
}

if(is_post('edit_title') or is_post('title_edit')){
        $query->update_sql("text_files", array("title" => post('title_edit')), "id = '".post_int('title_id')."'");
        header("Location: admin.php?module=text&page=edit&action=edit&link_id=".get_int('link_id')."");
        exit;
}

if(get('action') == 'file_delete'){
        $result = $query->select_sql("text_files", "lang, file", "id = '".get_int('id')."'");
        $row = $query->obj($result);
        @unlink("upload/".$module."/".$row->file);
        $query->delete_sql("text_files", "id = '".get_int('id')."'");
        header("Location: ".getenv("HTTP_REFERER")."");
        exit;
}

//***************** gallery *******************************
if(is_post('add_text_gallery')){
	$fields['link_id'] = get_int('link_id');
	$fields['pic'] = upload_image("upload/".$module."/gallery", "gallery_pic", time(), 'none', 800, 600, 100, 75);
	if($fields['pic'] !== false)
		$query->insert_sql("text_gallery", $fields);
	header("Location: ".$_SERVER['HTTP_REFERER']);
	exit;
}

if(get('action') == 'pic_delete'){
	$row = $query->select_obj_sql("text_gallery", "pic", "id = '".get('id')."'");

	@unlink("upload/".$module."/gallery/".rawurldecode($row->pic));
	@unlink("upload/".$module."/gallery/thumb/".rawurldecode($row->pic));
	$query->delete_sql("text_gallery", "id = '".get('id')."'");
	header("Location: ".getenv("HTTP_REFERER")."#gallery");
	exit;
}
//*********************************************************

$pages = array('edit', 'main', 'text');
load_page(get('page'), $pages, 'admin_main');
?>