<?php
global $tmpl;

function menu($parent_id, $position, $cycle = 0){
	global $query, $items, $id,$module;
	$menu_conf_file = "modules/".$module."/files/conf_".$position.".php";
    $menu_conf_file = is_file($menu_conf_file) ? $menu_conf_file : "modules/".$module."/files/conf_horizontal.php";
	include($menu_conf_file);
	$result_items = $query->select_sql("menu_items", "*", "parent_id = '".$parent_id."'", "priority ASC");

	if($cycle == 0) $items = '';
	$items .= "<ul class=\"menu_inner\">\n";
	while($row_items = $query->obj($result_items)){
        if((int)$row_items->parent == 1){
			//$li_style = "class=\"topline\"";
		}
		$items .= "	<li ".$li_style.">
						<a href=\"".$row_items->link."\">".$row_items->name."</a>\n";
		if((int)$row_items->parent == 1){
			$cycle++;
			menu($row_items->id, $position, $cycle);
		}
		$items .= "	</li>\n";
	}
	$items .= "</ul>\n";
	return $items;
}

function show_menu($menu_id){
	global $query, $lang, $tmpl, $module;
	$position_type[1] = "horizontal";
	$position_type[2] = "vertically";
	$row_menu = $query->select_obj_sql("menu", "*", "menu_n = ".(int)$menu_id." AND lang = '".$lang."'");
	$position = $position_type[$row_menu->position];
    $menu_conf_file = "modules/".$module."/files/conf_".$position.".php";
    $menu_conf_file = is_file($menu_conf_file) ? $menu_conf_file : "modules/".$module."/files/conf_horizontal.php";
    include($menu_conf_file);

	$result_items = $query->select_sql("menu_items", "*", "menu_id = '".$row_menu->id."' AND parent_id = '0'", "priority ASC");
	$item_amount = $query->amount_fields("menu_items", "menu_id = '".$row_menu->id."' AND parent_id = '0'");

	$cur_url = explode('/', $_SERVER['REQUEST_URI']);
	$where = "link = '".$cur_url[count($cur_url) - 1]."'";
	$row_cur = $query->select_obj_sql("menu_items", "*", $where);
	if(strlen($row_cur->link) > 0){
		if((int)$row_cur->parent_id !== 0){
			$row_cur_sub = $query->select_obj_sql("menu_items", "*", "id = '".$row_cur->parent_id."'");
			$color_item_id = $row_cur_sub->id;
		}
		else{
			$color_item_id = $row_cur->id;
		}
	}
	$n=0;
	$img[$color_item_id] = "_hover";
	$class[$color_item_id] = "_hover";

    $out .= "  <div class=\"menu_div_".$position."\">\n";
	$out .= "<ul class=\"menu_".$position."\" id=\"menu_".$position."\">\n";
	while($row_items = $query->obj($result_items)){
		$n++;
	    $li_style = "";
		if((int)$row_items->parent == 1){
			//$li_style = "class=\"accessible\"";
			$li_style = "class=\"main_items_".$position."\"";
		}
		else{
			$li_style = "class=\"main_items_".$position."\"";
		}
		/*if($n !== 1){
			$out .= $menu_conf['split'];
		}*/

		$out .= "	<li ".$li_style."".($n == 1 ? "  style=\"background: none\"" : "").">\n";
		if($row_items->image == ""){
			$out .= $left;
			$out .= "	<div class=\"menu_item_inside_".$position.$class[$row_items->id]."\">\n";
			//*********** momrgvaleba ***************
			if($row_items->id == $color_item_id){
				if($n !== 1) $color_left = "_blue";
				//$out .= "<div style=\"left: 0px; top: 0px; position: absolute\"><img src=\"images/menu_round_border".$color_left."_left.png\" border=0></div>\n";
			}
			//***************************************
			$out .= "		<a href=\"".$row_items->link."\" class=\"menulink\">".$row_items->name."</a>\n";
			//*********** momrgvaleba ***************
			if($row_items->id == $color_item_id && $n !== $item_amount){
				if($n !== $item_amount) $color_right = "_blue";
				//$out .= "<div style=\"right: 0px; top: 0px; position: absolute\"><img src=\"images/menu_round_border".$color_right."_right.png\" border=0></div>\n";
			}
			//***************************************
			$out .= "	</div>\n";
		}
		else{
			$image_size = getimagesize("upload/menu/".$row_items->image);
			$out .= "	<div class=\"menu_item_inside_".$position." menu_item_inside_".$position.$class[$row_items->id]."\" style=\"background-image: url('upload/menu/".$row_items->image."')\">
							<a href=\"".$row_items->link."\" class=\"menulink\">".space()."</a></div>\n";
		}
		if((int)$row_items->parent == 1){
			$out .= menu($row_items->id, $position);
		}
		$out .= "	</li>\n";

	}
	$out .= "</ul>\n";
	$out .= " </div> ";

	$out .= "<script type=\"text/javascript\">\n";
	$out .= "if(parseInt($( window ).width()) > 700){\n";
	$out .= "	var menu_".$position."=new menu_".$position.".dd(\"menu_".$position."\");\n";
	$out .= "	menu_".$position.".init(\"menu_".$position."\",\"menuhover\");\n";
	$out .= "}\n";
	$out .= "</script>\n";
	return $out;
}

?>