<?php
function which_day_ajax($month, $year){
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
        //echo $d."-";
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

function show_calendar($var, $now_date = '', $year_b = 15, $year_f = 15){
	$out .= "<table border=\"0\" width=\"87\" cellspacing=\"0\" cellpadding=\"0\">\n";
	$out .= "	<tr>\n";
	$out .= "		<td>\n";
	$out .= "			<input type=\"text\" name=\"".$var."\" id=\"".$var."\" value=\"".$now_date."\" style=\"width: 75px; font-size: 12px;\">\n";
	$out .= "		</td>\n";
	$out .= "		<td id=\"cal_img_".$var."2\">
			<div id=\"img_".$var."2\" onclick=\"cal_image(1, '".$var."2', '".$var."', '".$year_b."', '".$year_f."');\">
				<img onclick=\"showform('', '', '".DOC_ROOT."blocks/ajax_calendar/show_calendar.php?div=".$var."2&var=".$var."&now_date='+document.getElementById('".$var."').value+'&year_b=".$year_b."&year_f=".$year_f."', '".$var."2_cal')\" src=\"images/btn_date1_up.gif\" border=\"0\">
			</div></td>\n";
	$out .= "	</tr>\n";
	$out .= "	<tr>\n";
	$out .= "		<td></td>\n";
	$out .= "		<td><div id=\"".$var."2_cal\" style=\"position: absolute; z-index: 999\"></div></td>\n";
	$out .= "	</tr>\n";
	$out .= "</table>\n";

	return $out;
}


function calendar($var, $now_date = '', $year_b = 15, $year_f = 15){
global $lang;
if(is_file("language/".$lang."/calendar.php")){
	@include("language/".$lang."/calendar.php");
	echo " <link href=\"".DOC_ROOT."blocks/ajax_calendar/style/style.css\" rel=\"stylesheet\" type=\"text/css\"> \n";
}
else{
	@include("../../language/".$lang."/calendar.php");
}


$now_date = set_var($now_date, date("Y-m-d", time()), 0);
$now_date = explode("-", $now_date);

$month_31 = array(1, 3, 5, 7, 8, 10, 12);
$month_30 = array(4, 6, 9, 11);

$day = date("d", time());

$month = set_var(get_int('month'), sprintf("%01u", $now_date[1]), 0);
$year = set_var(get_int('year'), $now_date[0], 0);


settype($day, "integer");
settype($month, "integer");
settype($year, "integer");
// ramdenit mtavrdea tve
if(in_array($month, $month_31)){
        $days = 31;
}
elseif(in_array($month, $month_30)){
        $days = 30;
}
elseif($month == 2 and gettype($year/4) == "integer"){
        $days = 29;
}
else{
        $days = 28;
}


//$tt= date("w", time());   echo gettype($tt);
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

echo "<script src=\"../../functions/ajax.js\"></script>\n";

echo " <table class=\"cal_days\" border=\"0\" width=\"250\" cellspacing=\"1\" cellpadding=\"0\"> \n";
echo "                 <tr> \n";
echo "                         <td align=\"center\" width=\"20\" height=\"10\" class=\"cal_td\">
		<div onclick=\"showform(this.name, this.value, '".DOC_ROOT."blocks/ajax_calendar/show_calendar.php?month=".$mb_zero.$month_back."&year=".$year_back."&var=".$var."&div=".$var."2&now_date=".$now_date."&year_b=".$year_b."&year_f=".$year_f."', '".$var."2_cal')\">
			&lt;</div></td> \n";
echo "                         <td colspan=\"5\" align=\"center\" width=\"150\" height=\"10\" class=\"cal_td\"> \n";
echo "<select size=\"1\" name=\"month\" onchange=\"showform('', '', '".DOC_ROOT."blocks/ajax_calendar/show_calendar.php?div=".$var."2&month=".$m_zero."'+this.value+'&year=".$year."&var=".$var."&now_date=".$now_date."&year_b=".$year_b."&year_f=".$year_f."', '".$var."2_cal')\">\n";
$cur_month[$month] = 'selected';
foreach($ar_month as $month_num => $month_name){
	echo "  <option value=\"".$month_num."\" ".$cur_month[$month_num].">".$month_name."</option>\n";
}
echo "</select>\n";

echo "<select size=\"1\" name=\"year\" onchange=\"showform('', '', '".DOC_ROOT."blocks/ajax_calendar/show_calendar.php?div=".$var."2&month=".$m_zero.$month."&year='+this.value+'&var=".$var."&now_date=".$now_date."&year_b=".$year_b."&year_f=".$year_f."', '".$var."2_cal')\">\n";
$cur_year[$year] = 'selected';
for($i = date("Y", time()) - $year_b; $i <= date("Y", time()) + $year_f; $i++){
	echo "  <option value=\"".$i."\" ".$cur_year[$i].">".$i."</option>\n";
}
echo "</select>\n";

echo "	</td> \n";
echo "                         <td align=\"center\" width=\"20\" height=\"10\" class=\"cal_td\">
		<div onclick=\"showform(this.name, this.value, '".DOC_ROOT."blocks/ajax_calendar/show_calendar.php?div=".$var."2&month=".$mf_zero.$month_for."&year=".$year_for."&var=".$var."&now_date=".$now_date."', '".$var."2_cal')\">
			&gt;</div></td> \n";
echo "                 </tr> \n";
echo "                 <tr> \n";
echo "                         <td align=\"center\" width=\"25\" height=\"10\" class=\"cal_td\">"._W_OR."</td> \n";
echo "                         <td align=\"center\" width=\"25\" height=\"10\" class=\"cal_td\">"._W_SAM."</td> \n";
echo "                         <td align=\"center\" width=\"25\" height=\"10\" class=\"cal_td\">"._W_OTX."</td> \n";
echo "                         <td align=\"center\" width=\"25\" height=\"10\" class=\"cal_td\">"._W_XUT."</td> \n";
echo "                         <td align=\"center\" width=\"25\" height=\"10\" class=\"cal_td\">"._W_PAR."</td> \n";
echo "                         <td align=\"center\" width=\"25\" height=\"10\" class=\"cal_td\">"._W_SHAB."</td> \n";
echo "                         <td align=\"center\" width=\"25\" height=\"10\" class=\"cal_td\"> \n";
echo "                         <font class=\"cal_days\" color=\"#FF0000\">"._W_KV."</font></td> \n";
echo "                 </tr> \n";
echo "                 <tr> \n";
$j = 0;  // echo which_day_ajax($month, $year);
for($i = 0; $i < 42; $i++){
        if(gettype($i/7) == "integer" and $i !== 0){
                echo " </tr> \n";
                echo " <tr> \n";
        }
        if($i >= which_day_ajax($month, $year) and $j < $days){
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

        if("".date("Y-m-d", time())."" == "".$year."-".sprintf("%02s", $month)."-".sprintf("%02s", $j)."")
        	 $w_color = 'style="color: #D50000; font-weight: bold;"';
        else
             $w_color = 'color="#000000"';


        echo " <td class=\"cal_td\" align=\"center\" width=\"25\" height=\"10\">
        		<a href=\"javascript:add_date('".$year."-".sprintf("%02s", $month)."-".sprintf("%02s", $j)."', '".$var."2', '".$var."', '".$year_b."', '".$year_f."')\">
        		<font ".$w_color.">".$b[1].$j.$b[2]."</font></a></td> \n";
        }
        elseif(($j == 0 and which_day_ajax($month, $year) <= 6) or $j == $days){
                if($j == $days and  gettype(($i/7)) == "integer"){
                        echo "    </tr> \n";
                        echo " </table> \n";

                        return;
                }
                echo " <td align=\"center\" width=\"25\" height=\"10\" class=\"cal_td\">&nbsp;</td> \n";
        }


}
echo "    </tr> \n";
echo " </table> \n";

}
?>