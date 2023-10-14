<?php

if(get_int('pg') !== 0){
	$page_start = get_int('pg');
}
else $page_start = 1;

$respodent_amount = 10;

$name_surname = explode(" ", get('respodent_name'));
$where = "(u.name LIKE '%".$name_surname[0]."%' AND u.surname LIKE '%".$name_surname[1]."%') OR
			(u.name LIKE '%".$name_surname[1]."%' AND u.surname LIKE '%".$name_surname[0]."%')";
if(get('respodent_name') === false) $where = 0;
$where .= " AND ot.faq_enable=1";
$table = "users u INNER JOIN ".$global_conf['table_pref']."db_org_type ot ON (u.pers_type = ot.id)";
$result_respodent = $query->select_sql($table,  "u.*, u.id as user_id", $where, "u.id DESC", "".(($page_start-1)*$respodent_amount).", ".(int)$respodent_amount."");


$n=0;
$out_respodent_body = "<div id=\"respodents_body\"
							style=\"width: 260px; height: 150px; overflow: auto;\">\n";

$no_data = "<div class=\"no_data\">"._YOU_MUST_CHOOSE_USER."</div>\n";
while($row_respodent = mysql_fetch_object($result_respodent)){
	$n++; $no_data = "";
	$row_org = $query->select_obj_sql("db_org", "name", "id = ".(int)$row_respodent->org."");
	$row_pers_type = $query->select_obj_sql("db_org_type", "name", "id = ".(int)$row_respodent->pers_type."");
	$div_style = chng_style($div_style, 'admin_td1', 'admin_td2');
	$out_respodent_body .= "<div style=\"clear: both; width: 235px; cursor: pointer\" class=\"".$div_style."\"
								 onclick=\"select_selecxbox('".$row_respodent->user_id."', '".$row_respodent->name." ".$row_respodent->surname."', 'q_addressee')\">\n";
	$out_respodent_body .= "<div style=\"float: left; width: 50px;\">
						".pic_resize("upload/users/thumb/", $row_respodent->pic, 40, 40)."</div>\n";
	$out_respodent_body .= "<div style=\"float: left; width: 180px; text-align: left\">
				<b>".$row_respodent->name." ".$row_respodent->surname."</b><BR>
				<font class=\"respodent_org\"><i>".$row_pers_type->name."</i></font>
				</div>\n";
	$out_respodent_body .= "</div>\n";
}
$out_respodent_body .= $no_data;

$out_respodent_body .= "</div>\n";

if(get('echo') == "yes"){
	echo $out_respodent_body;
	exit;
}
else{
	$out .= $out_respodent;
}
?>