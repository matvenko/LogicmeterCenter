<?php
$user_class->permission_end($module, 'admin');
if(is_post('add_menu')){
	if($_POST['menu_name']){
		$query->insert_sql("menu", array("lang" => $_POST['lang'],
											 "position" => $_POST['menu_pos'],
											 "name" => $_POST['menu_name']));
	}
	header("Location: admin.php?module=".$module."");
	exit;
}

if(is_post('edit_menu')){
	if($_POST['menu_name']){
		$query->update_sql("menu", array("lang" => $_POST['lang'],
											 "position" => $_POST['menu_pos'],
											 "name" => $_POST['menu_name']), "id = '".get('id')."'");
	}
	header("Location: admin.php?module=".$module."");
	exit;
}

if(get('action') == 'menu_delete'){
	$query->delete_sql("menu", "id = '".get('id')."'");
	header("Location: admin.php?module=".$module."");
	exit;
}

if(is_post('add_item')){
	$row = $query->select_obj_sql("menu_items", "*", "id = '".post_int('items')."'");
	$link['text'] = "text-".post_int('text_modules').".html";
	$link['topic'] = "topic-".post_int('topic_modules').".html";
	$link['module'] = post('modules');
	$link['url'] = post('item_url');

	$normal_link['text'] = "index.php?module=text&link_id=".post_int('text_modules');
	$normal_link['topic'] = "index.php?module=topic&topic_id=".post_int('topic_modules');
	$normal_link['module'] = "index.php?module=".post('modules');
	$normal_link['url'] = post('item_url');

	if(post('item_loc') == "after"){
		$query->update_sql("menu_items", array("priority" => "INCREASE"), "parent_id = ".(int)$row->parent_id." AND priority > ".(int)$row->priority);
		$parent_id = $row->parent_id;
		$priority = $row->priority + 1;
	}
	elseif(post('item_loc') == "before"){
		$query->update_sql("menu_items", array("priority" => "INCREASE"), "parent_id = ".(int)$row->parent_id." AND priority >= ".(int)$row->priority);
		$parent_id = $row->parent_id;
		$priority = $row->priority;
	}
	elseif(post('item_loc') == "into"){
		$priority = $query->max_value("menu_items", "priority", "parent_id = ".post_int('items'));
		$priority++;
		$parent_id = post_int('items');
		$query->update_sql("menu_items", array("parent" => 1), "id = '".post_int('items')."'");
	}

	$image = file_upload("item_image", time(), "upload/".$module);

	$fields = array("menu_id" => get('menu_id'),
					"parent_id" => $parent_id,
					"name" => post('item_name'),
					"module" => $module,
					"link" => $link[post('module')],
					"normal_link" => $normal_link[post('module')],
					"blank" => post('blank'),
					"priority" => $priority,
					"image" => $image);

	$query->insert_sql("menu_items", $fields);
	header("Location: admin.php?module=menu&page=menu_items&menu_id=".get('menu_id')."");
	exit;
}

if(is_post('edit_item')){
	function stop_edit($id, $item_id){
		 global $query;
		 $row = $query->select_obj_sql("menu_items", "*", "id = '".$item_id."'");
		 if($row->parent_id == $id){
			  header("Location: admin.php?module=menu&page=menu_items&menu_id=".get('menu_id')."");
			  exit;
		 }
		 elseif($row->parent_id !== '0'){
			  stop_edit($id, $row->parent_id);
		 }
	}
	stop_edit(get_int('item_id'), post_int('items'));
	$row = $query->select_obj_sql("menu_items", "*", "id = '".post_int('items')."'");
	$row_edit = $query->select_obj_sql("menu_items", "*", "id = '".get_int('item_id')."'");
	$link['text'] = "text-".post_int('text_modules').".html";
	$link['topic'] = "topic-".post_int('topic_modules').".html";
	$link['module'] = post('modules');
	$link['url'] = post('item_url');

	$normal_link['text'] = "index.php?module=text&link_id=".post_int('text_modules');
	$normal_link['topic'] = "index.php?module=topic&topic_id=".post_int('topic_modules');
	$normal_link['module'] = "index.php?module=".post('modules');
	$normal_link['url'] = post('item_url');

	if(post('item_loc') == "after"){
		$query->update_sql("menu_items", array("priority" => "INCREASE"), "parent_id = ".(int)$row->parent_id." AND priority > ".(int)$row->priority);
		$priority = $row->priority + 1;
		$parent_id = $row->parent_id;
	}
	elseif(post('item_loc') == "before"){
		$query->update_sql("menu_items", array("priority" => "INCREASE"), "parent_id = ".(int)$row->parent_id." AND priority >= ".(int)$row->priority);
		$priority = $row->priority;
		$parent_id = $row->parent_id;
	}
	elseif(post('item_loc') == "into"){
		$priority = $query->max_value("menu_items", "priority", "parent_id = ".post_int('items'));
		$priority++;
		$parent_id = post_int('items');
		$query->update_sql("menu_items", array("parent" => 1), "id = '".post_int('items')."'");
	}
	else{
		$priority = $row_edit->priority;
		$parent_id = $row_edit->parent_id;
	}

	if(post('item_loc') !== "same"){
		$query->update_sql("menu_items", array("priority" => "DECREASE"), "parent_id = ".(int)$row_edit->parent_id." AND priority > ".(int)$row_edit->priority);
	}

	$fields = array("menu_id" => get('menu_id'),
					"parent_id" => $parent_id,
					"name" => post('item_name'),
					"module" => $module,
					"link" => $link[post('module')],
					"normal_link" => $normal_link[post('module')],
					"blank" => post('blank'),
					"priority" => $priority);

	$image = file_upload("item_image", time(), "upload/".$module);
	if(post_int('del_image') == 1){
		$fields['image'] = "";
		@unlink("upload/".$module."/".$row_edit->image);
	}
	elseif($image !== false){
		@unlink("upload/".$module."/".$row_edit->image);
		$fields['image'] = $image;
	}

	$query->update_sql("menu_items", $fields, "id = ".get_int('item_id'));
	if($query->amount_fields("menu_items", "parent_id = '".(int)$row_edit->parent_id."'") == 0 ){
	   $query->update_sql("menu_items", array("parent" => 0), "id = '".(int)$row_edit->parent_id."'");
	}

	header("Location: admin.php?module=menu&page=menu_items&menu_id=".get('menu_id')."");
	exit;
}

if(get('action') == 'item_delete'){
    $item_info = $query->select_ar_sql("menu_items", "*", "id= ".get_int('id'));
	$query->delete_sql("menu_items", "id = '".get_int('id')."' OR parent_id = '".get_int('id')."'");
	$query->update_sql("menu_items", array("priority" => "DECREASE"), "parent_id = ".(int)$item_info['parent_id']." AND priority < ".(int)$item_info['priority']);
	if($query->amount_fields("menu_items", "parent_id = '".get('parent_id')."'") == 0 ){
	   $query->update_sql("menu_items", array("parent" => 0), "id = '".get('parent_id')."'");
	}
	header("Location: admin.php?module=menu&page=menu_items&menu_id=".get('menu_id')."");
	exit;
}

if(isset($_POST['item_access'])){
	$result = $query->select_sql("group");
	while($row = $query->obj($result)){
		if($_POST[$row->id] == '1'){
			if($query->amount_fields("menu_item_access", "group_id = '".$row->id."' AND item_id = '".$_GET['id']."'") == '0'){
				$query->insert_sql("menu_item_access", array("item_id" => get_int('id'),
																 "group_id" => $row->id));
			}
		}
		else{
			if($query->amount_fields("menu_item_access", "group_id = '".$row->id."' AND item_id = '".$_GET['id']."'") !== '0'){
				$query->delete_sql("menu_item_access", "group_id = '".$row->id."' AND item_id = '".$_GET['id']."'");
			}
		}
	}
	header("Location: admin.php?module=".$module."&page=item_access&menu_id=".$_GET['menu_id']."&id=".$_GET['id']."");
	exit;
}

$pages = array('add_menu', 'item_access', 'menu_items');
load_page(get('page'), $pages, 'admin_main');
?>