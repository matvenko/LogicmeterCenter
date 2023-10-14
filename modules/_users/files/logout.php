<?php
global $facebook_info, $facebook_logoutUrl;
session_start();
unset($_SESSION['login']);
unset($_SESSION['permission']);
if($facebook_info){
	echo '<meta http-equiv="refresh" content="0;url='.$facebook_logoutUrl.'" />';	//header("Location: ".$facebook_logoutUrl);
	exit;}
else{
	header("Location: index.php");
	exit;
}
?>
