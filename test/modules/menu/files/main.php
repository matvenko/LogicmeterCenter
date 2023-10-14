<?php
global $out;

require_once("functions/functions.php");
require_once("functions/db.php");
require_once("modules/".$module."/files/menu.php");
$query = new sql_func();

$out = show_menu(get_int('menu_id'));

?>