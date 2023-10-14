<?php
global $out;
function parent_navigation_item($id){
	global $query, $navigation;
	$row_parent = $query->select_obj_sql("menu_items", "*", "id = ".(int)$id."");
	if((int)$row_parent->parent_id !== 0){
		parent_navigation_item($row_parent->parent_id);
		$navigation .= " / <a href=\"".$row_parent->link."\">".$row_parent->name."</a>";
	}
	else{
		$navigation .= " / <a href=\"".$row_parent->link."\">".$row_parent->name."</a>";
	}
	return $navigation;
}

$rewrite_url = $_SERVER['REQUEST_URI'];
$url1 = explode("/", $rewrite_url);
$url = $url1[(count($url1) - 1)];

if(strpos($url, "topicdetals") !== false){
	$real_url = explode("topicdetals-", $url);
	$url = "topic-".(int)$real_url[1].".html";
}
elseif(strpos($url, "artsearch") !== false){
	$real_url = explode("artsearch-", $url);
	$url = "topic-".(int)$real_url[1].".html";
}
$default_clip = $query->select_obj_sql("clips", "src", "`default` = 1");
$row_menu = $query->select_obj_sql("menu_items", "*", "link = '".$url."' AND link != ''", "", "0,1");
if((int)$row_menu->id !== 0){
	if((int)$row_menu->parent_id !== 0){
    	$out .= "<div class=\"navigation\">\n";
    	$out .= substr(parent_navigation_item($row_menu->parent_id), 3);
    	$out .= " / <a href=\"".$row_menu->link."\">".$row_menu->name."</a>";
    	$out .= "</div>\n";
    }
}

?>