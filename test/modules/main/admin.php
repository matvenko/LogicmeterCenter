<?php
$user_class->permission_end('main', 'all');
global $html_out;
$result = $query->select_sql("modules", "*", "hide = 0");
$out .= " <table border=\"0\" width=\"100%\" class=\"common_table\">";
$out .= "         <tr>";
$td = 0;
while($row = mysql_fetch_object($result)){
	if($user_class->group_perm($row->module, 'all')){
		$td++;
		$out .= " <td align=\"center\" width=\"20%\">";
		$out .= "     <a href=\"admin.php?module=".$row->module."\">";
		$out .= "     <img src=\"modules/".$row->module."/icon.gif\" width=\"50\" height=\"50\" alt=\"".$row->module."\" border=0></a>";
		$out .= "     <br><a href=\"admin.php?module=".$row->module."\"><font style=\"color: #000; font-size: 14px; font-weight: bold;\">".$row->module."</font></a></td>";
		if(gettype($td/5) == "integer"){
			$out .= " </tr>";
			$out .= " <td> &nbsp; </td>";
			$out .= " <tr>";
			$out .= " </tr>";
			$out .= " <tr>";
		}
	}
}


$out .= "        </tr>\n";
$out .= "</table>\n";

$html_out['module'] = $out;
unset($out);
?>