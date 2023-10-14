<?php
$user_class->permission_end($module, 'admin');
global $html_out, $dc, $ou_select, $base_dn;

$out = user_head();

$out .= "<br /><div class=\"title_div\">"._LAST_COMP."</div>";
$lastcomp = $query->max_value("gov_users", "CAST(SUBSTRING(compname, 10,4) AS SIGNED)");
$out .= "<div class=\"common_div\">mes-comp-".$lastcomp."</div>";

$out .= "<br /><div class=\"title_div\">"._UNUSER_COMP."</div>";
$out .= "<div class=\"common_div\">\n";
for($i = 11; $i <= $lastcomp; $i++){
	if($query->amount_fields("gov_users", "compname = 'mes-comp-".$i."'") == 0 && $dc->dc_search("name=mes-comp-".$i."") !== false){
		$out .= "mes-comp-".$i."<br>";
	}
}
$out .= "</div>\n";

$out .= "<br /><div class=\"title_div\">"._FREE_COMP."</div>";
$out .= "<div class=\"common_div\">\n";
for($i = 11; $i <= $lastcomp; $i++){
	if($dc->dc_search("name=mes-comp-".$i."") == false){
		$out .= "mes-comp-".$i."<br>";
	}
}
$out .= "</div>\n";


$html_out['module'] = $out;
unset($out);

?>