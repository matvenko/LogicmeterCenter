<?php
echo "<body><style>\n";
@include("style/style.css");
echo "</style>\n";
@include("index.php");
@include("../../functions/functions.php");
@include("../../conf.php");

$lang = 'geo';
$var = set_var(get('var'), '');
$div = set_var(get('div'), '');
$now_date = set_var(get('now_date'), '');
$year_b = set_var(get('year_b'), 15);
$year_f = set_var(get('year_f'), 15);

//echo get('now_date')."mm";


calendar($var, $now_date, $year_b, $year_f);

?>