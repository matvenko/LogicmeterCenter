<?php
function which_day($month, $year){
	$day_1 = date("d", time());
	settype($day_1, "integer");
	$day_1 = ($day_1-1)*60*60*24;
	if(time() >= mktime(0, 0, 0, $month, $day, $year)){
		$d = floor((time() - $day_1 - mktime(0, 0, 0, $month, 1, $year)) / (60 * 60 * 24));
		$back = 1;  // die("$d");
	}
	else{
		$d = ceil((mktime(0, 0, 0, $month, 1, $year) - time() + $day_1) / (60 * 60 * 24));
		$back = 0;
	}
	//$out .= $d."-";
	$q = round(($d/7 - floor($d/7))*7);
	$w = date("w", (time() - $day_1));
	settype($w, "integer");
	$w--;
	if($w < 0) $w = 6;

	for($i = 0; $i < $q; $i++){
		 if($back == 1){
			  $w--;
			  if($w < 0){
				  $w = 6;
			  }
		 }
		 elseif($back == 0){
			  $w++;
			  if($w > 6){
				  $w = 0;
			  }
		 }
	}

	return $w;
}

function month_dayes($month){	
	$month_31 = array(1, 3, 5, 7, 8, 10, 12);
	$month_30 = array(4, 6, 9, 11);

	if(in_array($month, $month_31)){
		$days = 31;
	}
	elseif(in_array($month, $month_30)){
		$days = 30;
	}
	elseif($month == 2 and gettype(gmdate("Y", time())/4) == "integer"){
		$days = 29;
	}
	else{
		$days = 28;
	}
	return $days;}

function month_first_day($month, $year){	$first_day = date("w", date_to_unix($year."-".$month."-1"));
	if($first_day == 0) $first_day = 7;
	return $first_day - 2;}

function calendar_view($var, $now_date = '', $year_b = 15, $year_f = 15){  
	global $lang, $query, $module;
	$year_b = (date("Y", time()) - 2006);
	@include("language/".$lang."/calendar.php");
$now_date = set_var($now_date, date("Y-m-d", time()), 0);
$now_date = explode("-", $now_date);

$day = date("d", time());

$month = set_var(get_int('month'), sprintf("%01u", $now_date[1]), 0);
$year = set_var(get_int('year'), $now_date[0], 0);

$days = month_dayes($month);

settype($day, "integer");
settype($month, "integer");
settype($year, "integer");
// ramdenit mtavrdea tve



//$tt= date("w", time());   $out .= gettype($tt);
$ar_month[1] = _JANUARY;
$ar_month[2] = _FABRUARY;
$ar_month[3] = _MARCH;
$ar_month[4] = _APRIL;
$ar_month[5] = _MAY;
$ar_month[6] = _JUNE;
$ar_month[7] = _JULY;
$ar_month[8] = _AUGUST;
$ar_month[9] = _SEPTEMBER;
$ar_month[10] = _OCTOMBER;
$ar_month[11] = _NOVEMBER;
$ar_month[12] = _DECEMBER;

$month_back = $month - 1;
if($month_back < 1){
	$month_back = 12;
	$year_back = $year - 1;
}
else $year_back = $year;

$month_for = $month + 1;
if($month_for > 12){
	$month_for = 1;
	$year_for = $year + 1;
}
else $year_for = $year;

if($month_back < 10) $mb_zero = "0";
else $mb_zero = "";

if($month_for < 10) $mf_zero = "0";
else $mf_zero = "";

if($month < 10) $m_zero = "0";
else $m_zero = "";

//$out .= "<script src=\"../../functions/ajax.js\"></script>\n";
//$out .= "<script src=\"functions/ajax.js\"></script>\n";

$out .= "<div>";
$out .= " <table border=\"0\" width=\"180\" cellspacing=\"0\" cellpadding=\"0\"> \n";
$out .= "   <tr> \n";
$out .= "		<td height=\"20\" align=\"left\" valign=\"top\">
					<span class=\"calendar_control\" data-url=\"post.php?module=".$module."&page=addedit_topic&action=show_calendar&lang=".$lang."&month=".$mb_zero.$month_back."&year=".$year_back."&var=".$var."&div=".$var."2&now_date=".$now_date."&year_b=".$year_b."&year_f=".$year_f."\">
						<img src=\"images/calendar_arrow_left.png\" style=\"cursor:pointer\" border=0>
					</span>
				</td>\n";
$out .= "		<td colspan=5 align=\"center\" style=\"color: #1b1b1b\" valign=\"top\">
					".$ar_month[$month]." ".$year."
				</td>\n";
$out .= "		<td align=\"right\" valign=\"top\">
					<span class=\"calendar_control\" data-url=\"post.php?module=".$module."&page=addedit_topic&action=show_calendar&lang=".$lang."&div=".$var."2&month=".$mf_zero.$month_for."&year=".$year_for."&var=".$var."&now_date=".$now_date."\">
						<img src=\"images/calendar_arrow_right.png\" style=\"cursor:pointer\" border=0>
					</span>
				</td>\n";
//			onchange=\"showform('', '', 'blocks/calendar/show_calendar.php?lang=".$lang."&div=".$div."&month=".$m_zero.$month."&year='+this.value+'&var=".$var."&now_date=".$now_date."&year_b=".$year_b."&year_f=".$year_f."', '".$div."_cal')\">\n";
$out .= "   </tr> \n";
$out .= "   <tr> \n";
$out .= "	   <td align=\"center\" class=\"topic_calendar_td_head\">"._W_OR."</td> \n";
$out .= "	   <td align=\"center\" class=\"topic_calendar_td_head\">"._W_SAM."</td> \n";
$out .= "	   <td align=\"center\" class=\"topic_calendar_td_head\">"._W_OTX."</td> \n";
$out .= "	   <td align=\"center\" class=\"topic_calendar_td_head\">"._W_XUT."</td> \n";
$out .= "	   <td align=\"center\" class=\"topic_calendar_td_head\">"._W_PAR."</td> \n";
$out .= "	   <td align=\"center\" class=\"topic_calendar_td_head\">"._W_SHAB."</td> \n";
$out .= "	   <td align=\"center\" class=\"topic_calendar_td_head\">"._W_KV."</td> \n";
$out .= "   </tr> \n";
$out .= "   <tr> \n";

//**************** events ****************
$result_events = $query->select_sql("topic_text", "date", "topic_id = 1 AND date >= ".date_to_unix($year."-".$month."-0")." AND date < ".date_to_unix($year."-".($month+1)."-0")."");
$events = array();
while($row_eventes = $query->obj($result_events)){	
	$events[] = date("Y-m-d", $row_eventes->date);
}
$events = array_unique($events);
//****************************************

//****** if admin calendar_link **********
if($query->group_perm("topic", "admin")){	$topic_admin = 1;}
//****************************************
$j = 0;
$after_days = 0;
$before_dayes = month_dayes($month_back);
$first_day = month_first_day($month, $year);
for($i = 0; $i < 42; $i++){
	if(gettype($i/7) == "integer" and $i !== 0){
		$out .= " </tr> \n";
		$out .= " <tr> \n";
	}
	if($i >= which_day($month, $year) and $j < $days){
	if(gettype((($i+1)/7)) == "integer") $w_color = "color=\"#FF0000\"";
	else $w_color = "";
	$j++;
	if(get('search_day') == "$j"){
		$b[1] = "<b>[";
		$b[2] = "]</b>";
	}
	else{
		$b[1] = "";
		$b[2] = "";
	}
	if($j < 10) $d_zero = "0";
	else $d_zero = "";

	/* if("".date("Y-m-d", time())."" == "".$year."-".sprintf("%02s", $month)."-".sprintf("%02s", $j)."")
		 $w_color = 'style="color: #D50000; font-weight: bold;"';
	else
	     $w_color = 'color="#000000"'; */

	//****** link **************
	$cur_day = $year."-".sprintf("%02s", $month)."-".sprintf("%02s", $j);
	//**************************
	$cur_day_style = current_date() == $cur_day ? " current_day" : ""; 
	$out .= " <td class=\"topic_calendar_td".$cur_day_style."\" align=\"center\">
				<span class=\"add_multi_date\" id=\"".$cur_day."\">".$j."</span></td> \n";
	}
	elseif(($j == 0 and which_day($month, $year) <= 6) or $j == $days){
		if($j == $days and  gettype(($i/7)) == "integer"){
			//$out .= "    </tr> \n";
			//$out .= " </table> \n";
			break;

		}
		
		if($j == $days){
			$after_days++;
			$other_days = $after_days;
		}
		else{			
			$other_days = $before_dayes - $first_day;
			$first_day --;		
		}
		$other_month = $month == 12 ? 1 : $month + 1;
		$other_year = $month == 1 ? $year + 1 : $year;
		
		$cur_day = $other_year."-".sprintf("%02s", $other_month)."-".sprintf("%02s", $other_days);
		$out .= " <td align=\"center\" class=\"topic_calendar_td\">
					<span class=\"add_multi_date\" id=\"".$cur_day."\">".$other_days."</span></td> \n";
	}


}
$out .= "    </tr> \n";
$out .= " </table> \n";
$out .= "</div>\n";
return $out;

}
?>