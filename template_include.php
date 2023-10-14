<?php
require_once("functions/functions.php");
$templates_ar = array("search.html", "footer.html");

if(in_array(get('page'), $templates_ar)){
	$out .= read_file("templates/".get('page'));
}
