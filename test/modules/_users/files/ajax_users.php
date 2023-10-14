<?php
if(get_int('pg') !== 0){
	$page_start = get_int('pg');
}
else $page_start = 1;

$respodent_amount = 10;

$name_surname = explode(" ", get('respodent_name'));
$where = "(name LIKE '%".$name_surname[0]."%' AND surname LIKE '%".$name_surname[1]."%') OR
			(name LIKE '%".$name_surname[1]."%' AND surname LIKE '%".$name_surname[0]."%')";
if(get('respodent_name') === false) $where = 0;

$table = "users";
$result_respodent = $query->select_sql($table,  "*", $where, "id DESC", "".(($page_start-1)*$respodent_amount).", ".(int)$respodent_amount."");


$n=0;
$out_respodent_body = "<div id=\"respodents_body\"
							style=\"width: 260px; height: 150px; overflow: auto;\">\n";

$no_data = "<div class=\"no_data\">"._YOU_MUST_CHOOSE_USER."</div>\n";
while($row_respodent = mysql_fetch_object($result_respodent)){
	$n++; $no_data = "";
	$div_style = chng_style($div_style, 'admin_td1', 'admin_td2');
	$out_respodent_body .= "<div style=\"clear: both; width: 235px; cursor: pointer\" class=\"".$div_style."\"
								 onclick=\"select_selecxbox('".$row_respodent->id."', '".$row_respodent->name." ".$row_respodent->surname."', 'q_addressee')\">\n";
	$out_respodent_body .= "<div style=\"float: left; width: 180px; height: 20px; text-align: left\">
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