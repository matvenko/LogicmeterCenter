<?php
require_once("text_image.php");
require_once("functions.php");
$font['geo'] = "agremyn_web.ttf";
$font['eng'] = "DECOR.TTF";

define('EMPTY_STRING', '');

$sylfaen = array("ქ", "წ", "ჭ", "ე", "რ", "ღ", "ტ", "თ", "ყ", "უ", "ი", "ო", "პ", "ა", "ს", "შ", "დ", "ფ", "გ", "ჰ", "ჯ", "ჟ", "კ", "ლ", "ზ", "ძ", "ხ", "ც", "ჩ", "ვ", "ბ", "ნ", "მ");
$lat = array("q", "w", "W", "e", "r", "R", "t", "T", "y", "u", "i", "o", "p", "a", "s", "S", "d", "f", "g", "h", "j", "J", "k", "l", "z", "Z", "x", "c", "C", "v", "b", "n", "m");

$text = mb_convert_encoding($_GET['img_text'], 'HTML-ENTITIES',"UTF-8");
// Convert HTML entities into ISO-8859-1
$text = html_entity_decode($text,ENT_NOQUOTES, "ISO-8859-1");
// Convert characters > 127 into their hexidecimal equivalents
for($i = 0; $i < strlen($text); $i++) {
    $letter = $text[$i];
    $num = ord($letter);
    if($num>127) {
      $img_text .= "&#$num;";
    } else {
      $img_text .=  $letter;
    }
}
      // die($out);

$img_text = str_replace($sylfaen, $lat, $_GET['img_text']);
$text = $img_text;
$image = new text_image($_GET['img_width'], $_GET['img_height'], $font[$_GET['lang']], $text);

if(!($image->font_size = get('font_size')))
	$image->font_size = 17;

if(!($image->bgcolor = get('bgcolor')))
	$image->bgcolor = "dae7f4";

if(!($image->textcolor = get('textcolor')))
	$image->textcolor = "005085";

if(!($image->textcolor2 = get('textcolor2')))
	$image->textcolor2 = "000000";

$image->_output();
?>