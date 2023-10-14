<?php
global $out;
$out = '';
$out .= "<div style=\"float: left; width: 350px\">\n";

$result_s_news = $query->select_sql("multi_text", "*", "multi_id = ".get_int('multi_id')."", "date DESC, id DESC", "3,3");
$n=0;
$out .= "<div style=\"padding-bottom: 15px\">\n";
while($row_s_news = mysql_fetch_object($result_s_news)){
	$n++;
	if($n == 2) $out .= "<div style=\"padding-top: 15px; clear: both\"></div>\n";
	$out .= "<div style=\"padding-bottom: 5px; clear: both\">\n";
	$out .= "  <a href=\"index.php?module=multi&multi_id=".get_int('multi_id')."&page=detals&id=".$row_s_news->id."\"><font class=\"multi_title\">".$row_s_news->title."</font></a>";
	$out .= "</div>\n";
	$out .= "<div>\n";
	$out .= "  ".pic_resize("misc/multi/gallery/thumb/","".rawurldecode($row_s_news->pic)."", 145, 110)."";
	if($row_s_news->short_text !== ''){
	      $out .= "         ".strip_tags(htmlspecialchars_decode($row_s_news->short_text), '<br>, <a>, <font>')." \n";
	}
	else{
		$out .= "         ".strip_tags(explode_text(htmlspecialchars_decode($row_s_news->text), 50), '<br>, <a>, <font>')." \n";
	}
	$out .= " 	<a href=\"index.php?module=multi&page=detals&multi_id=".get_int('multi_id')."&id=".$row_s_news->id."\"><font class=\"edit\">&gt;&gt;&gt;</font></a>\n";
	$out .= "</div>\n";

	if($n == 2){
		$out .= "</div>\n";
		$out .= "<div style=\"float: left; width: 350px;>\n";	}
}

$out .= "</div>\n";
$out .= "</div>\n";


?>
