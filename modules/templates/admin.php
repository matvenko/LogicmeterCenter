<?php
$user_class->permission_end($module, 'admin');

//******* add page **********
if(is_post('add_page')){
	if(post('lang') == false){
		echo "No lang";
		exit;
	}
	if(post('page_type') == false){
		echo "No page_type";
		exit;
	}
	
	$fields['lang'] = post('lang');
	$fields['page_type'] = post('page_type');
	$fields['module'] = post('module');
	$fields['custom_page'] = post('custom_page');
	$fields['template_file'] = post('template_file');
	
	if(get_int('edit_id') == 0){
		$fields['template_id'] = get_int('template_id');
		$query->insert_sql("templates_sources", $fields);
	}
	else{
		$query->update_sql("templates_sources", $fields, "id = ".get_int('edit_id'));
	}
	
	
	echo "ok";
	exit;
}

//*********** generate tmpl ******
if(is_post('generate_tmpl')){
	
	$page_info = $query->select_ar_sql("templates_sources", "template_file", "id = ".post_int('tmpl_page_id'));
	$content = read_file("templates/pages/".$page_info['template_file']);
	$content = strip_tags($content);
	$content = str_replace(array ("\n","\r", "\t"), "", $content);
	
	foreach ($global_conf['lang_ar'] as $system_lang){
		$content_lang = str_replace("[:".$system_lang.":]", "", $content);
		$content_lang = preg_replace("@(\[:(.*?):\])(.*?)(\[:(.*?):\])@", "", $content_lang);
		$content_lang = str_replace(" ", "", $content_lang);
		$content_lang = str_replace("{{:", "", $content_lang);
		$tmpl_blocks[$system_lang] = explode(":}}", $content_lang);
		
		$n = 0;
		foreach($tmpl_blocks[$system_lang] as $block_url){
			if($block_url == '')continue;
			$n++;
			$mod = explode('?', $block_url);
			if(strpos($mod [0], ".php") > 0){
				$request_ar [$system_lang][$n]['include_page'] = $mod [0]; 
		
				$mod2 = explode('&', $mod [1]);
				for($i = 0; $i < (count($mod2)); $i ++) {
					if($mod2[$i] !== ''){
						$temp = explode('=', $mod2 [$i]);
						$request_ar[$system_lang][$n]['requests'][$temp [0]] = $temp [1];
					}
				}
			}
		}
	}
	
	$fields['last_cache_time'] = date('Y-m-d H:i:s');
	$fields['cache'] = json_encode($request_ar);
	$query->update_sql("templates_sources", $fields, "id = ".post_int('tmpl_page_id'));

	echo OK;
	exit;
}

$pages = array('admin_main', 'template_details', 'add_page');
load_page(get('page'), $pages, 'admin_main');