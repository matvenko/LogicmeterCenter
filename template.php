<?php

function file_url(&$get_value, $get_name){
	$get_value = $get_name."=".$get_value;
}

$url = "index.php?";
foreach ( $_GET as $key => $value ) {
	$url .= $key . "=" . $value . "&";
}

if ((int)strpos($url, "module=") !== 0) {
	$query->where_vars['module'] = $module;
	$result_custom_tmpl = $query->select_sql("templates_sources", "template_file, custom_page, module", "page_type = 3 AND module= '{{module}}'", "template_file DESC");
	while ( $row_custom_tmpl = $query->obj($result_custom_tmpl) ) {
		
		if ((int)strpos(" ".$_SERVER['QUERY_STRING']." ", rtrim("module=".$row_custom_tmpl->module."&".$row_custom_tmpl->custom_page, '&')) !== 0) {
			if(is_file("templates/pages/".$row_custom_tmpl->template_file)) {
				$tmpl_file = $row_custom_tmpl->template_file;
				break;
			}
		}
	}
	if (!is_file("templates/pages/".$tmpl_file)) {
		$tmpl_file = "default.html";
	}
} else {
	if (is_file("templates/pages/main.html")) {
		$tmpl_file = "main.html";
	} else {
		$tmpl_file = "default.html";
	}
}

$html_out ['module'] = $module;
$query->where_vars['template_file'] = $tmpl_file;
$tmpl_info_json = $query->select_ar_sql("templates_sources", "cache", "template_file = '{{template_file}}'");

$tmpl_info_obj = json_decode($tmpl_info_json['cache'], true);

$tmpl_info_ar = (array)$tmpl_info_obj[$lang];
 
$include_module_styles = $include_block_styles = array ();
$global_get = $_GET;

foreach ($tmpl_info_ar as $tmpl_info){
	
	if (is_array($tmpl_info)) {
		$_GET = $global_get;
		foreach ( (array)$tmpl_info['requests'] as $key => $value ) {
			if (ltrim($value, '$') !== $value) {
				$_GET [$key] = $global_get [$key];
			} else {
				$_GET [$key] = $value;
			}
		}
	}
	
	if (strlen($tmpl_info['requests']['module']) !== 0) {
		//echo $_GET['module']."<BR>";
		$module = load_module(get('module'));
		if (is_file("language/" . $lang . "/" . $module . ".php")) {
			include_once ("language/" . $lang . "/" . $module . ".php");
		}
		include ("modules/" . $module . "/index.php");
		if (! in_array($module, $include_module_styles)) {
			$tmpl ['module_style'] .= "<link href=\"{{:doc_root_url:}}modules/" . $module . "/style/style.css?v=8\" rel=\"stylesheet\" type=\"text/css\">\n";
		}
		$include_module_styles [] = $module;
	} else {
		if (is_file($tmpl_info ['include_page'])) {
			include ($tmpl_info ['include_page']);
		}		
	}
	if(is_array($tmpl_info['requests'])){
		array_walk($tmpl_info['requests'], "file_url");
	}
	$file_url = $tmpl_info ['include_page']."?".implode("&", (array)$tmpl_info['requests']);
	$html_out [$file_url] = $out;
	unset($out);
	
}
$html_out ['edit_menu'] = "display: none;";
if ($user_class->user_admin() == "ADMIN") {
	$templates->module_ignore_fields[] = "admin";
	$tmpl ['admin'] = " 		<div style=\"position: absolute; line-height: 20px;background: #FFF; text-align: left; top: 0px\">
			<a href=\"admin.php\" style=\"color: #000;\">" . _ADMIN . "</a>&nbsp; | &nbsp;
			<a href=\"admin.php?module=users&page=main&username=&ou=0&search=search&al=all\" style=\"color: #000;\">" . _CHANGE_PASSWORD . "</a>&nbsp; | &nbsp;
			<a href=\"logout.php\" style=\"color: #000;\">" . _LOGOUT . "</a></font></div> \n";
	$html_out ['admin'] = $tmpl ['admin'];
	$html_out ['edit_menu'] = "";
}
elseif ($user_class->group_perm("text", "all")) {
	$templates->module_ignore_fields[] = "admin";
	$tmpl ['admin'] = " 		<div style=\"position: absolute; line-height: 20px;background: #FFF; text-align: left; top: 0px\">
			<a href=\"admin.php?module=math\" style=\"color: #000;\">" . _ADMIN . "</a>&nbsp; | &nbsp;
			<a href=\"logout.php\" style=\"color: #000;\">" . _LOGOUT . "</a></font></div> \n";
	$html_out ['admin'] = $tmpl ['admin'];
}


if(!$user_class->login_action()){
	$html_out ['_JOIN'] = _JOIN;
	$html_out ['_FIRST_TIME_HERE'] = _FIRST_TIME_HERE;
}

$html_out ['child_img_n'] = $user_class->login_action() ? 2 : 1;
$html_out ['load_section'] = get('section') == "literacy" ? "literacy" : "math";
$html_out ['lang'] = $lang;
$html_out ['news_id'] = $lang == "eng" ? 10 : 3;
$html_out ['lang_flag'] = $lang == "eng" ? "geo" : "eng";
$html_out ['keyword'] = get('keyword') == false ? "" : get('keyword');
$html_out ['_AUTHORIZATION'] = _AUTHORIZATION;
$html_out ['_REGISTRATION'] = _REGISTRATION;
$html_out ['_LEARNING'] = _LEARNING;
$html_out ['_IN_LOGICCENTER'] = _IN_LOGICCENTER;
$html_out ['_MATH'] = _MATH;
$html_out ['_LOGIC'] = _LOGIC;
$html_out ['_LITERACY'] = _LITERACY;
$html_out ['_CLASSES'] = _CLASSES;
$html_out ['_METHODOLOGY'] = _METHODOLOGY;
$html_out ['_VIDEOS'] = _VIDEOS;
$html_out ['_LERNING_PLANS_ALL'] = _LERNING_PLANS_ALL;

$tmpl['session_id'] = session_id();
$tmpl ['_PORTAL'] = _PORTAL;
$tmpl ['_CHAMP'] = _CHAMP;

$tmpl ['viewpoint'] = $tmpl_file == "main.html" ? "<meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">" : "";
$tmpl ['body'] = $templates->gen_html($html_out, "templates/pages/".$tmpl_file);

if($tmpl_file == "default.html"){
	$tmpl ['module_style'] .= "<link href=\"modules/menu/style/style_default.css\" rel=\"stylesheet\" type=\"text/css\">\n";
}

//require_once ("blocks/menu/menu.php");
if((int)$templates->only_body == 1){
	echo $tmpl ['body'];
}
else{
    $tmpl ['yes_no_delete'] = _YES_NO_DELETE;
    //$tmpl['module'] = $module;
    $tmpl ['doc_root_url'] = DOC_ROOT;
    $tmpl ['lang'] = $lang;
    $tmpl ['page_title'] = _WEB_TITLE;
    $full_out = $templates->gen_html($tmpl, "templates/html.html");
    
    $full_out = preg_replace(array("/{{:([[:print:]]+):}}/", "/\[:([[:print:]]+):\]/"), "", $full_out);
    echo $full_out;
}
