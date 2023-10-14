<?php
global $html_out, $out;
$out = '';
$multi_id = get_int('multi_id');
$module = "multi";

if($query->group_perm($module, 'admin')){
	$out .= " <div> <a href=\"admin.php?module=".$module."&page=addedit_multi&multi_id=".$multi_id."&action=add\"> <font class=\"edit\">"._ADD."</a></font></div> \n";
}
$out .= " <div class=\"multi_title\">".$row_head->name."</div> \n";

if(get_int('pg') !== 0){
        $page_start = get_int('pg');
}
else $page_start = 1;
$result = $query->select_sql("multi_text", "*", "multi_id = '".$multi_id."' AND lang = '".$lang."'", "date DESC, id DESC", "".(($page_start-1)*10).", 10");
while($row = mysql_fetch_object($result)){
        $out .= "<div style=\"float:center\">\n";
        $out .= " <div><a href=\"index.php?module=".$module."&page=detals&multi_id=".$multi_id."&id=".$row->id."\"><b>".$row->title."</b></a></div> \n";
        if($query->group_perm($module, 'admin')){
                $out .= " &nbsp;&nbsp; <a href=\"admin.php?module=".$module."&page=addedit_multi&action=edit&id=".$row->id."\"><font class=\"edit\">"._EDIT." </a></font> \n";
                $out .= " &nbsp;&nbsp; <a href=\"javascript: yes_no('post.php?module=".$module."&action=delete_text&multi_id=".$multi_id."&id=".$row->id."&lang=".$lang."')\"><font class=\"edit\">"._DELETE."</font> </a> \n";
        }
		$out .= " <div style=\"float:left;width: 100%;\"><a href=\"index.php?module=".$module."&page=detals&multi_id=".$multi_id."&id=".$row->id."\">
						".pic_resize("misc/".$module."/gallery/thumb/","".rawurldecode($row->pic)."", 240, 180)."</a> \n";
        $out .= " 	</div>\n";
        $out .= "<div style=\"width: 240px; padding: 5px;\">".space(5, 5)."<BR>\n";
        if($row->short_text !== ''){
	       $out .= "         ".strip_tags(htmlspecialchars_decode($row->short_text), '<br>, <a>, <font>')." \n";
		}
		else{
			$out .= "         ".strip_tags(explode_text(htmlspecialchars_decode($row->text), 50), '<br>, <a>, <font>')." \n";
		}
        $out .= " 	<a href=\"index.php?module=".$module."&page=detals&multi_id=".$multi_id."&id=".$row->id."\"><font class=\"edit\">&gt;&gt;&gt;</font></a>\n";
        $out .= " 	</div>\n";
        $out .= " 	</div>\n";

}

?>