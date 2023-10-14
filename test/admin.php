<?php
include("init.php");

$user_class->permission_end($module, 'all');

include("post.php");

if($user_class->user_admin() == "ADMIN"){
	$tmpl['admin'] = " 		<div style=\"position: absolute; width: 350px; line-height: 20px;background: #FFF\">
			<a href=\"".DOC_ROOT."index.php\" style=\"color: #000;\">"._MAIN."</a>&nbsp; | &nbsp;
			<a href=\"admin.php\" style=\"color: #000;\">"._ADMIN."</a>&nbsp; | &nbsp;
			<a href=\"logout.php\" style=\"color: #000;\">"._LOGOUT."</a></font></div> \n";
	$html_out['admin'] = $tmpl['admin'];
}
elseif($user_class->login_action()){
	$tmpl ['admin'] = " 		<div style=\"position: absolute; width: 450px; line-height: 20px;background: #FFF; text-align: left\">
			<a href=\"index.php?module=users&page=chng_pass\" style=\"color: #000;\">" . $_SESSION['login']['name_surname'] . "</a>&nbsp; | &nbsp;
			<a href=\"index.php?module=users&page=chng_pass\" style=\"color: #000;\">" . _CHNG_PASSWORD . "</a>&nbsp; | &nbsp;
			<a href=\"logout.php\" style=\"color: #000;\">" . _LOGOUT . "</a></font></div> \n";
	$html_out ['admin'] = $tmpl ['admin'];
}

$html_out['yes_no_delete'] = _YES_NO_DELETE;
$html_out['module_style'] = $module;

$full_out = $templates->gen_html($html_out, "templates/admin_html.html");

$full_out = preg_replace("/{{:([[:print:]]+):}}/", "", $full_out);
echo $full_out;

?>