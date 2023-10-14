<?php
global $out;
//$out = "";

$link_id = get_int('link_id');
$row = $query->select_obj_sql("text", "*", "link_id = '".$link_id."' AND lang = '".$lang."'");

if($user_class->group_perm($module, 'admin')){
        $out .= " <div style=\"background: #FFF; position: absolute; width: 50px; height: 20px; top: -10px; left: 0px\">
        				<a href=\"".MANAGE_DIR."admin.php?module=".$module."&link_id=".$link_id."&action=edit&page=edit\" style=\"color: #8C0000; font-size: 13px\"><b>"._EDIT."</b></a>
        			</div> \n";
}
$out .= htmlspecialchars_decode($row->text);

?>