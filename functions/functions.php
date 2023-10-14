<?php
@include ("conf.php");

function load_lang($lang) {
	global $query, $query_l, $module, $lang, $templates, $global_conf, $user_class, $math, $literacy, $literacy_user;
	if ($lang == false and $_SESSION ['ses_lang'] == '') {
		$_SESSION ['ses_lang'] = $global_conf['default_lang'];
	} elseif ($lang !== '' and isset($lang) and in_array($lang, $global_conf['lang_ar'])) {
		$_SESSION ['ses_lang'] = $lang;
	}
	$lang = $_SESSION ['ses_lang'];
	
	@include ("language/" . $lang . "/global.php");
	@include ("language/" . $lang . "/" . load_module(get('module')) . ".php");
	return $lang;
}

function load_page($load_page, $pages, $default_page = 'main', $inc = 1) {
	global $query, $query_l, $module, $lang, $templates, $global_conf, $user_class, $math, $literacy, $literacy_user;
	if (isset($load_page) and in_array($load_page, $pages)) {
		$load_page = $load_page;
	} else {
		$load_page = $default_page;
	}
	
	if ($inc == 1)
		include ("modules/" . $module . "/files/" . $load_page . ".php");
	elseif ($inc == 0)
		return $load_page;
}

function load_module($module, $default_module = 'main') {
	global $query;
	
	if (isset($module) && $query->amount_fields("modules", "module = '" . $module . "'") > 0) {
		$module = $module;
	} else {
		$module = $default_module;
	}
	return $module;
}

function read_file($file_src) {
	if (is_file($file_src)) {
		$handle = fopen($file_src, "r");
		$contents = fread($handle, filesize($file_src));
		fclose($handle);
		return $contents;
	} else {
		return "No file";
	}
}

function is_post($name) {
	if (isset($_POST [$name]))
		return true;
	else {
		return false;
	}
}

function clear_request($request){
	$return_value = strip_tags(trim($request));
	$return_value = str_replace('script', 'sc_ript', $return_value);
	
	if (! get_magic_quotes_gpc()) {
		return addslashes($return_value);
	} else {
		return $return_value;
	}
}

function post($name) {
	if (is_post($name) == true && $_POST [$name] !== '') {
		if(is_array($_POST[$name])){
			foreach($_POST[$name] as $key => $value){
				$result[$key] = clear_request($value);
			}
		}
		else{
			$result = clear_request($_POST[$name]);
		}
		return $result;
	} else {
		return false;
	}
}

function post_value($name) {
	$return_value = stripslashes(htmlspecialchars(post($name)));
	
	return $return_value;
}

function get_value($name) {
	$return_value = stripcslashes(htmlspecialchars(get($name)));
	
	return $return_value;
}

function post_admin($name) {
	if (is_post($name) == true && $_POST [$name] !== '') {
		$return_value = str_replace('javascript', 'java_script', trim($_POST[$name]));
		$return_value = str_replace('script', 'sc_ript', $return_value);
		if (! get_magic_quotes_gpc()) {
			return addslashes($return_value);
		} else {
			return $return_value;
		}
	} else {
		return false;
	}
}

function post_int($name) {
	if (post($name) !== false) {
		return (int)post($name);
	} else {
		return 0;
	}
}

function get_int($name) {
	return (int)get($name);
}

function get($name) {
	settype($name, 'string');
	if (strlen($_GET [$name]) !== 0) {
		$return_value = strip_tags(htmlspecialchars(rawurldecode(trim($_GET [$name]))));
		$return_value = str_replace('javascript', 'java_script', $return_value);
		$return_value = str_replace('script', 'sc_ript', $return_value);
		if (! get_magic_quotes_gpc()) {
			return addslashes($return_value);
		} else {
			return $return_value;
		}
	} else {
		return false;
	}
}

function set_var($var, $default_value, $equal = '', $else_value = '') {
	if ($var == false || $var == $equal) {
		return $default_value;
	} else {
		if ($else_value == '')
			return $var;
		else
			return $else_value;
	}
}

function image_size($img, $width = 100, $height = 100) {
	@$size = getimagesize(urldecode($img));
	if ((int)$size [0] < (int)$width && (int)$size [1] < (int)$height) {
		$width = $size [0];
		$height = $size [1];
	} elseif (($size [0] / $width) >= ($size [1] / $height)) {
		@$height = $size [1] * ($width / $size [0]);
	} elseif (($size [0] / $width) < ($size [1] / $height)) {
		@$width = $size [0] * ($height / $size [1]);
	}
	$size = "width=" . $width . " height=" . $height . "";
	return $size;
}

function image_dimention($img, $width = 100, $height = 100) {
	@$size = getimagesize(urldecode($img));
	if ((int)$size [0] < (int)$width && (int)$size [1] < (int)$height) {
		$width = $size [0];
		$height = $size [1];
	} elseif (($size [0] / $width) >= ($size [1] / $height)) {
		@$height = $size [1] * ($width / $size [0]);
	} elseif (($size [0] / $width) < ($size [1] / $height)) {
		@$width = $size [0] * ($height / $size [1]);
	}
	$size['width'] = $width;
	$size['height'] = $height;
	return $size;
}

function body_head($title, $link = '') {
	$out = "<div height=\"50\" align=\"left\" style=\"width: 96%\">\n";
	if ($link !== '') {
		$out .= "<a href=\"" . $link . "\">" . $title . "</a>";
	} else {
		$out .= "<span class=\"head_text\">" . $title . "</span>";
	}
	$out .= "</div>";
	return $out;
}

function file_icon($filename) {
	if (preg_match("/\.rar$/", $filename) or preg_match("/\.zip$/", $filename)) {
		$icon = "images/archive.gif";
	} elseif (preg_match("/\.doc$/", $filename)) {
		$icon = "images/word.gif";
	} elseif (preg_match("/\.xls/", $filename)) {
		$icon = "images/excel.gif";
	} elseif (preg_match("/\.ppt/", $filename)) {
		$icon = "images/powerpoint.gif";
	} elseif (preg_match("/\.mdb$/", $filename)) {
		$icon = "images/access.gif";
	} elseif (preg_match("/\.pdf$/", $filename)) {
		$icon = "images/pdf.gif";
	} elseif (preg_match("/\.avi/", $filename)) {
		$icon = "images/video.png";
	} elseif (preg_match("/\.wmv/", $filename)) {
		$icon = "images/video.png";
	} elseif (preg_match("/\.mov/", $filename)) {
		$icon = "images/video.png";
	} else {
		$icon = "images/otherfile.gif";
	}
	return $icon;
}

function pic_resize($source, $img, $et_width = 100, $et_height = 100, $align = 'left') {
	$div_style = "float: left; padding-right: 3px;";
	if (! is_file($source . $img)) {
		return "<img src=\"images/no_image.jpg\" width=\"150\" height=\"100\" border=\"0\">";
		return "";
	}
	$space = 2;
	if (_get_browser('browser') == 'OPERA')
		$space = 5;
	@$size = getimagesize($source . $img);
	if (($size [0] / $et_width) >= ($size [1] / $et_height)) {
		$width = $et_width;
		@$s = $width / $size [0];
		@$height = $size [1] * $s;
		$pic = "<img src=\"" . $source . rawurlencode($img) . "\" border=0 width=\"" . $width . "\" height=\"" . $height . "\" align=\"" . $align . "\">";
		//$pic .= space($space, $height, "", "left");
	} else {
		$height = $et_height;
		@$s = $height / $size [1];
		@$width = $size [0] * $s;
		$pic = "<img src=\"" . $source . rawurlencode($img) . "\" border=0 width=\"" . $width . "\" height=\"" . $height . "\" align=\"" . $align . "\">";
		//$pic .= space($space, $height, "", "left");
	}
	return $pic;
}

function explode_text($text, $world = 20, $stip_tags = '<b>, <p>, <a>, <font>, <br>') {
	$out_text = explode(" ", strip_tags(htmlspecialchars_decode($text), $stip_tags));
	if (count($out_text) > $world) {
		$cut_text = "";
		$dot = "...";
		for($i = 0; $i < $world; $i ++) {
			$cut_text .= $out_text [$i] . " ";
		}
	} else {
		$cut_text = strip_tags(htmlspecialchars_decode($text), $stip_tags);
		$dot = "";
	}
	return $cut_text . $dot;
}

function print_url() {
	$curent_url = explode('.php?', $_SERVER ['REQUEST_URI']);
	return "print.php?" . $curent_url [1];
}

function editorarea($ed_num = 1, $width = 670, $height = 300, $toolbar_type = "gt_toolbar", $p_br = 1, $ed_num_start = 1, $skin = "kama") {
	global $html_out, $global_conf, $user_class, $query;
	$out .= " <script type=\"text/javascript\"> \n";
	//$image_browse_url = $toolbar_type == "literacy" ? "filebrowserImageBrowseUrl: 'post.php?module=literacy&page=insert_images&skill_id=".get_int('skill_id')."'," : "";
	//$image_browse_url = $toolbar_type == "literacy" ? "filebrowserImageBrowseUrl: '/editor/kcfinder/temp.html'," : "";
	if($toolbar_type == "literacy"){
		$text_info = $query->select_ar_sql("math_literacy_texts", "book_id", "id = ".get_int('text_id'));
		$book_info = $query->select_ar_sql("math_literacy_books", "title", "id = ".(int)$text_info['book_id']);
		$dir_name = file_name_fix($book_info['title']);
    	$_SESSION['KCFINDER'] = array();
    	$_SESSION['KCFINDER']['uploadURL'] = is_dir("upload/literacy/".$dir_name) ? $global_conf['literacy_upload_directory']."/".$dir_name : $global_conf['literacy_upload_directory'];
    	//$_SESSION['KCFINDER']['sidebarWidth'] = "0";
    	$_SESSION['fold_type'] = "";
	}
	else{
		$_SESSION['KCFINDER'] = array();
		$_SESSION['KCFINDER']['uploadURL'] = $global_conf['editor_upload_directory'];
		$_SESSION['KCFINDER']['types'] = array('file' => "", 'media' => "swf flv avi mpg mpeg qt mov wmv asf rm", 'images' => "*img");
		$_SESSION['fold_type'] = "";
	}
	for($i = $ed_num_start; $i < $ed_num_start + $ed_num; $i ++) {
		$out .= "var instance = CKEDITOR.instances.editor".$i.";
    			if(instance){
        			CKEDITOR.remove(instance);
    			}
				CKEDITOR.replace( 'editor" . $i . "',
				{
					fullPage : false,
					skin : '".$skin."',
					sidebarWidth  : '0',
					".$image_browse_url."
					enterMode : Number(".$p_br."),
     				toolbar: '" . $toolbar_type . "',
     				height: '".$height."', width: '".$width."'
				});\n";
		
	}
	$out .= " </script> \n";
	return $out;
	// $out .=o " <textarea name=\"editorarea".$ed_num."\" id=\"editorarea".$ed_num."\">".$value."</textarea> \n";
}

function file_name_fix($name){
	$name = convert_text($name, "geo", "lat");
	$name = str_replace(" ", "_", $name);
	$name = str_replace("#", "", $name);
	$name = str_replace("\"", "", $name);
	$name = str_replace("'", "", $name);
	$name = str_replace("(", "", $name);
	$name = str_replace(")", "", $name);
	$name = str_replace(",", "_", $name);
	$name = str_replace(".", "_", $name);
	
	return ($name);
}

function upload_image($uploadDir, $name, $pre, $type = 'none', $big_width = 600, $big_height = 400, $width = 150, $height = 150) {
	$file_ext = explode('.', $_FILES [$name] ['name']);
	$uploadFile = $pre . "." . strtolower($file_ext [(count($file_ext) - 1)]);
	if ($_FILES [$name] ['name']) {
		move_uploaded_file($_FILES [$name] ['tmp_name'], "upload/temp/" . rawurldecode($uploadFile));
		$pic = $uploadFile;
		@$size = getimagesize("upload/temp/" . rawurldecode($uploadFile));
		$thumb = new thumbnail("upload/temp/" . rawurldecode($uploadFile));
		$O = $type == "inverse" ? 1 : 0;
		$I = $type == "inverse" ? 0 : 1;
		if(!is_dir($uploadDir)){
			mkdir($uploadDir);
		}
		if ($type == 'logo') {
			if ($size [$O] >= $size [$I]) {
				$thumb->size_width($big_width);
			} else {
				$thumb->size_height($big_height);
			}
			$thumb->save($uploadDir . "/" . rawurldecode($uploadFile));
		} else {
			if ($size [$O] <= $big_width && $size [$I] <= $big_height) {
				$thumb->size_width($size [$O]);
			} elseif ($size [$O] >= $size [$I]) {
				$thumb->size_width($big_width);
			} else {
				$thumb->size_height($big_height);
			}
			
			if($big_width == "original"){
				copy("upload/temp/" . rawurldecode($uploadFile), $uploadDir . "/" . rawurldecode($uploadFile));
			}
			else{
				$thumb->save($uploadDir . "/" . rawurldecode($uploadFile));
			}
			$thumb = new thumbnail("upload/temp/" . rawurldecode($uploadFile));
			if ($size [$O] >= $size [$I]) {
				$thumb->size_width($width);
			} else {
				$thumb->size_height($height);
			}
			if(!is_dir($uploadDir."/thumb")){
				mkdir($uploadDir."/thumb");
			}
			$thumb_dir = $type == "editor" ? ".thumbs" : "thumb";
			//$thumb->crop = $type == "inverse" ? 1 : 0;
			$thumb->save($uploadDir . "/".$thumb_dir."/" . rawurldecode($uploadFile));
		}
		@unlink("upload/temp/" . rawurldecode($uploadFile));
		return $uploadFile;
	} else {
		return false;
	}
}

function upload_image_percent($uploadDir, $name, $pre, $size_percent = 100) {
	$file_ext = explode('.', $_FILES [$name] ['name']);
	$uploadFile = $pre . "." . strtolower($file_ext [(count($file_ext) - 1)]);
	if ($_FILES [$name] ['name']) {
		move_uploaded_file($_FILES [$name] ['tmp_name'], "upload/temp/" . rawurldecode($uploadFile));
		$pic = $uploadFile;
		@$size = getimagesize("upload/temp/" . rawurldecode($uploadFile));
		$thumb = new thumbnail("upload/temp/" . rawurldecode($uploadFile));
		
		$thumb->size_width(round($size[0] * $size_percent / 100));
		$thumb->save($uploadDir."/".rawurldecode($uploadFile));
		
		@unlink("upload/temp/" . rawurldecode($uploadFile));
		return $uploadFile;
	} else {
		return false;
	}
}

function copy_image_percent($source, $destination, $size_percent = 100) {
	$file_ext = explode('.', $_FILES [$name] ['name']);
	$uploadFile = $destination;
	if (is_file($source)) {
		@$size = getimagesize($source);
		$thumb = new thumbnail($source);

		$thumb->size_width(round($size[0] * $size_percent / 100));
		$thumb->save(rawurldecode($uploadFile));

		return $uploadFile;
	} else {
		return false;
	}
}

function crop_image($image_src, $width){
	if(is_file($image_src)){
		@$size = getimagesize($image_src);
		$x = $size[0] > $size[1] ? ($size[0] - $width) / 2 : 0;
		$y = $size[0] < $size[1] ? ($size[1] - $width) / 2 : 0;
		try{
			$image = new Imagick(realpath($image_src));
			$image->cropImage($width, $width, $x, $y);
			$image->writeImage($image_src);
		}
		catch (Exception $e){
			return false;
		}		
	}
}

function file_name($name) {
	if ($_FILES [$name] ['name'] !== '') {
		return $_FILES [$name] ['name'];
	} else {
		return false;
	}
}

function file_extention($file_name, $exploder = '.'){
	$file_ext = explode($exploder, $file_name);
	return strtolower($file_ext[count($file_ext) - 1]);
}

function file_upload($name, $pref, $uploadDir, $error = "") {
	if (file_name($name) !== false) {
		$file_ext = explode('.', file_name($name));
		move_uploaded_file($_FILES [$name] ['tmp_name'], $uploadDir . "/" . $pref . "." . $file_ext [count($file_ext) - 1]);
		return $pref . "." . $file_ext [count($file_ext) - 1];
	} else {
		return false;
	}
}

function image_swf($filename, $dir, $width = '', $height = '', $class = '') {
	$file_ext_ar = explode('.', $filename);
	$file_ext = strtolower($file_ext_ar [count($file_ext_ar) - 1]);
	$img_ext = array (
			'jpg',
			'gif' 
	);
	if ($height !== '')
		$height = "height=\"" . $height . "\"";
	if ($width !== '')
		$width = "$width=\"" . $height . "\"";
	if (in_array($file_ext, $img_ext)) {
		return "<img src=\"" . $dir . $filename . "\" " . $width . " " . $height . " border=\"0\" class=\"" . $class . "\">";
	} elseif ($file_ext == 'swf') {
		return "<embed src=\"" . $dir . $filename . "\" quality=\"high\" wmode=\"transparent\"
			    width=\"" . $width . "\" height=\"" . $height . "\" name=\"superpimper\" align=\"middle\" allowScriptAccess=\"sameDomain\"
			    type=\"application/x-shockwave-flash\" pluginspage=\"http://www.macromedia.com/go/getflashplayer\" />
		      </embed>";
	} else {
		return "";
	}
}

function split_page($table, $where, $limit = 10, $search_field = '*') {
	global $query;
	$amount = $query->amount_fields($table, $where, $search_field);
	$url_temp = explode('?', $_SERVER ['REQUEST_URI']);
	$url = explode('&pg=', $url_temp [1]);
	
	if ($limit == 0)
		$limit = 10;
	if (($amount / $limit) > 1) {
		if (get_int('pg') == 0) {
			$page_start = 1;
		} elseif (get_int('pg') <= ceil($amount / $limit)) {
			$page_start = get_int('pg');
		}
		$b_s [$page_start] = "<b>";
		$b_f [$page_start] = "</b>";
		//$out['module_body'] .= "<a href=\"index.php?module=".$module."&multi_id=".$multi_id."\">".$b_s[1]._FIRST_PAGE.$b_f[1]."</a>";
		$pages = '';
		$page_am = ceil($amount / $limit);
		if ($page_am > 7) {
			$pages .= " &gt; <a href=\"" . $url_temp [0] . "?" . $url [0] . "&pg=1\">" . $b_s [1] . "[1]" . $b_f [1] . "</a>";
			if ($page_start > 6) {
				$pages .= "...";
				if ($page_start < ($page_am - 3)) {
					$page_from = $page_start - 4;
					$page_to = $page_start + 2;
				} else {
					$page_from = $page_am - 6;
					$page_to = $page_am - 2;
				}
				for($i = $page_from; $i <= $page_to; $i ++) {
					$pages .= " &gt; <a href=\"" . $url_temp [0] . "?" . $url [0] . "&pg=" . ($i + 1) . "\">" . $b_s [($i + 1)] . "[" . ($i + 1) . "]" . $b_f [($i + 1)] . "</a> \n";
				}
			} else {
				for($i = 1; $i <= 6; $i ++) {
					$pages .= " &gt; <a href=\"" . $url_temp [0] . "?" . $url [0] . "&pg=" . ($i + 1) . "\">" . $b_s [($i + 1)] . "[" . ($i + 1) . "]" . $b_f [($i + 1)] . "</a> \n";
				}
			}
			if ($page_start < ($page_am - 3)) {
				$pages .= "...";
			}
			$pages .= " &gt; <a href=\"" . $url_temp [0] . "?" . $url [0] . "&pg=" . $page_am . "\">" . $b_s [($page_am)] . "[" . $page_am . "]" . $b_f [$page_am] . "</a>";
		} else {
			for($i = 0; $i < $amount / $limit; $i ++) {
				if (($i + 1) == get_int('pg')) {
					$pages .= " &gt; <font color=\"#000000\"><b>" . $b_s [($i + 1)] . "[" . ($i + 1) . "]" . $b_f [($i + 1)] . "</b></font>";
				} else {
					$pages .= " &gt; <a href=\"" . $url_temp [0] . "?" . $url [0] . "&pg=" . ($i + 1) . "\">" . $b_s [($i + 1)] . "[" . ($i + 1) . "]" . $b_f [($i + 1)] . "</a> \n";
				}
			}
		}
		return ltrim($pages, ' &gt; ');
	}
}

function split_page_ajax($div, $table, $where, $limit = 10, $search_field = '*') {
	global $query;
	$amount = $query->amount_fields($table, $where, $search_field);
	
	$url = "body.php?view=ajax_view";
	
	foreach ( $_GET as $key => $value ) {
		if (! in_array($key, array (
				'pg',
				'include_page' 
		)) && $value !== "") {
			$url .= "&" . $key . "=" . $value;
		}
	}
	
	if ($limit == 0)
		$limit = 10;
	if (($amount / $limit) > 1) {
		if (get_int('pg') == 0) {
			$page_start = 1;
		} elseif (get_int('pg') <= ceil($amount / $limit)) {
			$page_start = get_int('pg');
		}
		$b_s [$page_start] = "<b>";
		$b_f [$page_start] = "</b>";
		//$out['module_body'] .= "<a href=\"index.php?module=".$module."&multi_id=".$multi_id."\">".$b_s[1]._FIRST_PAGE.$b_f[1]."</a>";
		$pages = '';
		$page_am = ceil($amount / $limit);
		if ($page_am > 7) {
			$pages .= " &gt; <span style=\"cursor:pointer\" onclick=\"showform('', '', '" . $url . "&pg=1', '" . $div . "')\">" . $b_s [1] . "[1]" . $b_f [1] . "</span>";
			if ($page_start > 6) {
				$pages .= "...";
				if ($page_start < ($page_am - 3)) {
					$page_from = $page_start - 4;
					$page_to = $page_start + 2;
				} else {
					$page_from = $page_am - 6;
					$page_to = $page_am - 2;
				}
				for($i = $page_from; $i <= $page_to; $i ++) {
					$pages .= " &gt; <span style=\"cursor:pointer\" onclick=\"showform('', '', '" . $url . "&pg=" . ($i + 1) . "', '" . $div . "')\">" . $b_s [($i + 1)] . "[" . ($i + 1) . "]" . $b_f [($i + 1)] . "</span> \n";
				}
			} else {
				for($i = 1; $i <= 6; $i ++) {
					$pages .= " &gt; <span style=\"cursor:pointer\" onclick=\"showform('', '', '" . $url . "&pg=" . ($i + 1) . "', '" . $div . "')\">" . $b_s [($i + 1)] . "[" . ($i + 1) . "]" . $b_f [($i + 1)] . "</span> \n";
				}
			}
			if ($page_start < ($page_am - 3)) {
				$pages .= "...";
			}
			$pages .= " &gt; <span style=\"cursor:pointer\" onclick=\"showform('', '', '" . $url . "&pg=" . $page_am . "', '" . $div . "')\">" . $b_s [($page_am)] . "[" . $page_am . "]" . $b_f [$page_am] . "</span>";
		} else {
			for($i = 0; $i < $amount / $limit; $i ++) {
				if (($i + 1) == get_int('pg')) {
					$pages .= " &gt; <font color=\"#000000\"><b>" . $b_s [($i + 1)] . "[" . ($i + 1) . "]" . $b_f [($i + 1)] . "</b></font>";
				} else {
					$pages .= " &gt; <span style=\"cursor:pointer\" onclick=\"showform('', '', '" . $url . "&pg=" . ($i + 1) . "', '" . $div . "')\">" . $b_s [($i + 1)] . "[" . ($i + 1) . "]" . $b_f [($i + 1)] . "</span> \n";
				}
			}
		}
		return ltrim($pages, ' &gt; ');
	}
}

function module_head($title = " d") {
}

function _get_browser($data_type) {
	$browser = array (
			"MSIE", // parent
			"OPERA",
			"MOZILLA", // parent
			"NETSCAPE",
			"FIREFOX",
			"SAFARI" 
	);
	
	$info [browser] = "OTHER";
	
	foreach ( $browser as $parent ) {
		if (($s = strpos(strtoupper($_SERVER ['HTTP_USER_AGENT']), $parent)) !== FALSE) {
			$f = $s + strlen($parent);
			$version = substr($_SERVER ['HTTP_USER_AGENT'], $f, 5);
			$version = preg_replace('/[^0-9,.]/', '', $version);
			
			$info [browser] = $parent;
			$info [version] = $version;
			break;
		}
	}
	
	return $info [$data_type];
}

function open_popup($width = "500px", $height = "") {
	if ($height !== "")
		$height = "height: " . $height . "px";
	$out .= "<center><div style=\"text-align: left; position: relative; width: " . ((int)$width + 17) . "px;\">\n";
	$out .= "<div style=\"position: absolute; right: 0px; top: 0px\">\n";
	$out .= popup_close("<img src=\"images/close_popup.png\" border=\"0\" style=\"position: absolute\">\n" . space(25, 10)) . "\n";
	$out .= "</div>\n";
	$out .= "<div style=\"padding-top: 12px; padding-right: 12px\">";
	$out .= "<div class=\"popup\">\n";
	$out .= "	<div style=\"border: 2px solid #d9d9d9;\">\n";
	$out .= "	<div style=\"width: " . $width . "; overflow: hidden; " . $height . "\">\n";
	$out .= "	<div style=\"padding: 10px\">\n";
	$out .= "		<div id=\"check\" class=\"error\"></div>\n";
	return $out;
}

function close_popup() {
	$out .= "	</div>\n";
	$out .= "	</div>\n";
	$out .= "	</div>\n";
	$out .= "	</div>\n";
	$out .= "</div>\n";
	$out .= "</div>\n";
	$out .= "<img src=\"images/space.gif\" onload=\"getPageSizeWithScroll()\">\n";
	return $out;
}

function input_form($name, $type, $value = '', $items = '', $style = '', $class = '', $other = '') {
	global $input2view;
	$input2view = set_var($input2view, "input");
	if (is_array($value))
		$edit_value = htmlspecialchars((string)$value [$name]);
	else
		$edit_value = htmlspecialchars((string)$value);
	switch ($type) {
		case "view" :
			$out ['input'] = "<span id=\"" . $name . "\">" . htmlspecialchars_decode($edit_value) . "</span>";
			$out ['view'] = "<span id=\"" . $name . "\">" . htmlspecialchars_decode($edit_value) . "</span>";
			break;
		case "text":
		case "textbox":
			$out ['input'] = "<input name=\"" . $name . "\" id=\"" . $name . "\" type=\"text\" value=\"" . $edit_value . "\" class=\"" . $class . "\" style=\"" . $style . "\" " . $other . ">";
			$out ['view'] = "<span id=\"" . $name . "\">" . htmlspecialchars_decode($edit_value) . "</span>";
			break;
		case "hidden" :
			$out ['input'] = "<input name=\"" . $name . "\" id=\"" . $name . "\" type=\"hidden\" value=\"" . $edit_value . "\">\n";
			break;
		case "password" :
			$out ['input'] = "<input name=\"" . $name . "\" id=\"" . $name . "\" type=\"password\" class=\"" . $class . "\" style=\"" . $style . "\">\n";
			break;
		case "select" :
			settype($items, "array");
			$out['input'] = "<select size=\"1\" id=\"" . $name . "\" name=\"" . $name . "\" style=\"" . $style . "\" class=\"" . $class . "\" " . $other . ">\n";
			$multi_select_name = str_replace(array('[',	']' ), '', $name, $multi_select);
			if ($multi_select == 2 && is_array($value[$multi_select_name])) {
				foreach ( $value[$multi_select_name] as $selected_items ) {
					$select[$selected_items] = " selected";
				}
			} else {
				$select[$edit_value] = " selected";
			}
			if($items[0] !== "hide"){
				$out['input'] .= "<option value=\"\">".($items[0] == "" ? "------" : $items[0])."</option>\n";
			}
			unset($items[0]);
			if (is_array($items))
				foreach ( $items as $key => $value ) {
					if (! is_numeric($key)) {
						list($key, $value) = array($value, $key);
					}
					$out['input'] .= "<option value=\"" . $key . "\"" . $select[$key] . ">" . $value . "</option>\n";
				}
			$out['input'] .= "</select>\n";
			//$items = @array_flip($items);
			$out['view'] = $items[$edit_value];
			break;
		case "select_group" :
				$out['input'] = "<select size=\"1\" id=\"" . $name . "\" name=\"" . $name . "\" style=\"" . $style . "\" class=\"" . $class . "\" " . $other . ">\n";
				$multi_select_name = str_replace(array('[', ']'), '', $name, $multi_select);
				if ($multi_select == 2 && is_array($value[$multi_select_name])) {
					foreach ( $value[$multi_select_name] as $selected_items ) {
						$select[$selected_items] = " selected";
					}
				} else {
					$select[$edit_value] = " selected";
				}
				$out['input'] .= "<option value=\"\">------</option>\n";
				if (is_array($items))
					foreach ( $items['groups'] as $group_key => $group_value ) {
						$out['input'] .= "<optgroup label=\"".$group_value."\">\n";
						foreach ((array)$items['options'][$group_key] as $key => $value){
							if (! is_numeric($key)) {
								list($key, $value) = array($value, $key);
							}
							$out['input'] .= "<option value=\"" . $key . "\"" . $select[$key] . ">" . $value . "</option>\n";
						}
						$out['input'] .= "</optgroup>\n";
					}
				$out['input'] .= "</select>\n";
				//$items = @array_flip($items);
				$out['view'] = $items[$edit_value];
				break;
		case "radio" :
			$select[$edit_value] = " checked";
			if (is_array($items)) {
				foreach ( $items as $key => $value ) {
					if (! is_numeric($key)) {
						list($key, $value) = array($value, $key);
					}
					$out['input'] = "<input name=\"" . $name . "\" id=\"".$name."_".$key."\" type=\"radio\" value=\"" . $key . "\" " . $select[$key] . " style=\"" . $style . "\" class=\"" . $class . "\" " . $other . "> " . $value . "\n";
				}
				//$items = array_flip($items);
				$out['view'] = $items[$edit_value];
			}
			break;
		case "single_radio" :
		    settype($select, "array");
		    $select [$edit_value] = " checked";
			$out ['input'] = "<input name=\"".$name."\" id=\"".$name."_".$items."\" type=\"radio\" value=\"" . $items . "\" " . $select [$items] . " style=\"" . $style . "\" class=\"" . $class . "\" " . $other . ">\n";
			settype($items, "array");
			$out ['view'] = "<span id=\"".$name."\">".$items[$edit_value]."</span>";
			break;
		case "checkbox" :
			$checked = "";
			if ((int)$edit_value == 1) {
				$checked = " checked";
			}
			$out ['input'] .= "<input name=\"" . $name . "\" id=\"" . $name . "\" type=\"checkbox\" value=\"1\"" . $checked . " style=\"" . $style . "\" class=\"" . $class . "\" " . $other . ">\n";
			;
			break;
		case "textarea" :
			$edit_value = str_replace("\r\n", "\n", $edit_value);
			$out ['input'] = "<textarea name=\"" . $name . "\" id=\"" . $name . "\" style=\"" . $style . "\" class=\"" . $class . "\">" . $edit_value . "</textarea>\n";
			$out ['view'] = "<span id=\"" . $name . "\">" . nl2br($edit_value) . "</span>";
			break;
		case "textarea_editor" :
			$out ['input'] = "<textarea name=\"".$name."\" style=\"".$style."\" id=\"editor".$items."\">".$edit_value."</textarea>\n";
			$out ['view'] = "<span id=\"" . $name . "\">" . htmlspecialchars_decode($edit_value) . "</span>";
			break;
		case "calendar" :
			settype($items, "array");
			$out ['input'] = show_calendar($name, $edit_value, $items ['from'], $items ['to'], $other);
			$out ['view'] = "<span id=\"" . $name . "\">" . $edit_value . "</span>";
			break;
		case "number":
			settype($items, "array");
			$min_max = (int)$items['min'] !== 0 ? "min=\"".$items['min']."\" " : "";
			$min_max .= (int)$items['max'] !== 0 ? "max=\"".$items['max']."\" " : "";
			$out ['input'] = "<input name=\"" . $name . "\" id=\"" . $name . "\" type=\"number\" value=\"" . $edit_value . "\" ".$min_max." class=\"" . $class . "\" style=\"" . $style . "\" " . $other . ">\n";
			$out ['view'] = "<span id=\"" . $name . "\">" . htmlspecialchars_decode($edit_value) . "</span>";
			break;
	}
	return $out [$input2view];
}

function space($width = 5, $height = 5, $id = '', $align = '') {
	return "<img src=\"images/space.gif\" width=\"" . $width . "\" height=\"" . $height . "\" border=\"0\" align=\"" . $align . "\" id=\"" . $id . "\">";
}

function popup_wndow($text, $module, $page, $url = '', $index_type = 'post') {
	$out = "<a style=\"cursor:pointer\" onclick=\"showform('coursor', csrollposition(), '" . $index_type . ".php?module=" . $module . "&page=" . $page . "&" . $url . "', 'start_div');getPageSizeWithScroll();\">
    " . $text . "</a>\n";
	return $out;
}

function ajax_get($div, $text, $module, $page, $url = '', $admin = "post") {
	$out = "<span style=\"cursor:pointer\" onclick=\"showform('', '', '" . $admin . ".php?module=" . $module . "&page=" . $page . "&" . $url . "', '" . $div . "');\">
    " . $text . "</span>\n";
	return $out;
}

function popup_close($text = "X", $align = "right") {
	$out = "<div style=\"text-align: " . $align . "; text-decoration: none; cursor: pointer; color: #000\" onclick=\"showform('','','clear.php?', 'start_div');hide_table();\"><b>" . $text . "</b>&nbsp;&nbsp;</div>";
	return $out;
}

function ajax_from($function_name, $items) {
	$out = "<script language=\"javascript\" type=\"text/javascript\">\n";
	$out .= "   function " . $function_name . "(){\n";
	$out .= "   var out='';\n";
	foreach ( $items as $field ) {
		$out .= " out = out + ajax_form('" . $field . "');\n";
	}
	$out .= "   return out;\n";
	$out .= "}\n";
	$out .= "</script>\n";
	
	return $out;
}

function javascript_echo($text) {
	$out = "<script language=\"javascript\" type=\"text/javascript\">";
	$out .= $text;
	$out .= "</script>";
	
	return $out;
}

function select_color($amount = 1) {
	$out .= "<link rel=\"STYLESHEET\" type=\"text/css\" href=\"functions/select_color/dhtmlxcolorpicker.css\">\n";
	$out .= " <script src=\"" . DOC_ROOT . "functions/select_color/dhtmlxcommon.js\"></script>\n";
	$out .= " <script src=\"" . DOC_ROOT . "functions/select_color/dhtmlxcolorpicker.js\"></script>\n";
	$out .= "<script>\n";
	$out .= "function initColorPicker(){\n";
	for($i = 1; $i <= $amount; $i ++) {
		$out .= "   var myCP" . $i . " = new dhtmlXColorPickerInput('select_color" . $i . "');\n";
		$out .= "    myCP" . $i . ".setImagePath(\"functions/select_color/imgs/\");\n";
		$out .= "    myCP" . $i . ".init();\n";
	}
	$out .= "}\n";
	$out .= "</script>\n";
	return $out;
}

function select_color_form($name, $n, $value) {
	if (is_array($value))
		$edit_value = $value [$name];
	else
		$edit_value = $value;
	$out .= "<input type=\"text\" name=\"" . $name . "\" imagepath=\"" . DOC_ROOT . "functions/select_color/imgs/\" customcolors=\"true\" selectonclick=\"true\" id=\"select_color" . $n . "\" value=\"" . $edit_value . "\" style=\" z-index:999; background: " . $edit_value . "; width:60px; color: #525252; font-style: italic;\">\n";
	$out .= "<img src=\"images/space.gif\" onload=\"initColorPicker()\" border=\"0\">\n";
	return $out;
}

function write_file($filename, $text, $type = 'replace') {
	$open_type ['replace'] = 'w+';
	$open_type ['add'] = 'a+';
	if (! $handle = fopen($filename, $open_type [$type])) {
		return "Cannot open file ($filename)";
		exit();
	}
	if (is_writable($filename)) {
		if (fwrite($handle, $text) === FALSE) {
			return "Cannot write to file ($filename)";
			exit();
		}
		return true;
		fclose($handle);
	} else {
		echo "The file $filename is not writable";
	}
}

function clear_file($filename) {
	if (! $handle = fopen($filename, 'w')) {
		return false;
	} else {
		ftruncate($handle, 0);
	}
}

function star() {
	return "<span style=\"color: #CE0000; font-size: 16px;\">*</span>";
}

function form_start($post_action = "", $action = "admin", $file = "", $get_post = "post", $name = "") {
	global $module;
	if ((int)$file == 1) {
		$file = " enctype='multipart/form-data'";
	}
	if ($action == "admin") {
		$action = "post.php?module=" . $module;
	} elseif ($action == "user") {
		$action = "clear_post.php?module=" . $module;
	}
	return "<div style=\"position: absolute;\"><form name=\"" . $name . "\" action=\"" . $action . "&" . $post_action . "\" method=\"" . $get_post . "\" " . $file . "></div>\n";
}

function form_end() {
	return "<div style=\"position: absolute;\"></form></div>\n";
}

function edit_button($edit_object, $type = "admin", $link = "", $edit_text = "") {
	global $module;
	if ($link == "") {
		$link = $type . ".php?module=" . $module . "&page=" . get('page') . "";
	}
	if ($edit_text == '') {
		$edit_text = "<img src=\"images/edit.png\" alt=\"" . _EDIT . "\" border=\"0\">\n";
	}
	return "<a href=\"" . MANAGE_DIR . $link . "&" . $edit_object . "\">" . $edit_text . "</a>\n";
}

function delete_button($action, $delete_obj, $type = "admin", $link = "", $delete_text = "") {
	global $module;
	if ($type == "admin") {
		$link = "post.php?module=" . $module . "&action=" . $action . "";
	} elseif ($type == "user") {
		$link = "clear_post.php?module=" . $module . "&action=" . $action . "";
	}else{
		$link .= "&action=" . $action;
	}
	if ($delete_text == '') {
		$delete_text = "<img src=\"images/drop.png\" alt=\"" . _EDIT . "\" border=\"0\">\n";
	}
	return "<a href=\"javascript: yes_no('" . $link . "&" . $delete_obj . "', '" . _YES_NO_DELETE . "')\">" . $delete_text . "</a>\n";
}

function convert_text($text, $from = "lat", $to = "geo") {
	$convert['geo'] =     array("ქ", "წ", "ჭ", "ე", "რ", "ღ", "ტ", "თ", "ყ", "უ", "ი", "ო", "პ", "ა", "ს", "შ", "დ", "ფ", "გ", "ჰ", "ჯ", "ჟ", "კ", "ლ", "ზ", "ძ", "ხ", "ც", "ჩ", "ვ", "ბ", "ნ", "მ");
	$convert['lat'] =     array("q", "w", "W", "e", "r", "R", "t", "T", "y", "u", "i", "o", "p", "a", "s", "S", "d", "f", "g", "h", "j", "J", "k", "l", "z", "Z", "x", "c", "C", "v", "b", "n", "m");
	$convert['geo_lat'] = array("q", "w", "ch", "e", "r", "g", "t", "t", "y", "u", "i", "o", "p", "a", "s", "sh", "d", "f", "g", "h", "j", "j", "k", "l", "z", "Z", "x", "c", "ch", "v", "b", "n", "m");
	
	return trim(str_replace($convert[$from], $convert[$to], $text));
}

function select_items($table, $key, $value, $where = 1, $new_line = "") {
	global $query;
	$query_value = ", " . $value;
	if (is_array($value)) {
		$query_value = "";
		foreach ( $value as $v ) {
			$query_value .= ", " . $v;
		}
	}
	$result = $query->select_sql($table, $key . $query_value, $where);
	while ( $row = $query->obj($result) ) {
		$out_ar [$row->{$key}] = $row->{$value} . $new_line;
		if (is_array($value)) {
			foreach ( $value as $v ) {
				$out_ar [$row->{$key}] .= " - ". $row->{$v} ;
			}
			$out_ar [$row->{$key}] = substr($out_ar [$row->{$key}], 3) . $new_line;
		}
	}
	return (array)$out_ar;
}

function ajax_dropdown($field_name, $edit_value, $module, $url) {
	$event = "showform('" . $field_name . "', this.value, 'clear_post.php?module=" . $module . "&" . $url . "&echo=yes', 'loading_area');show_block('loading_area')";
	$out .= "<div class=\"send_q_body_left\">
				<div style=\"\">
					<input name=\"" . $field_name . "_id\" id=\"" . $field_name . "_id\" type=\"hidden\" value=\"" . $edit_value [$field_name . '_id'] . "\">
					<input name=\"" . $field_name . "_string\" id=\"" . $field_name . "_string\" type=\"hidden\" value=\"" . $edit_value [$field_name . '_string'] . "\">
					<input name=\"" . $field_name . "\" id=\"" . $field_name . "\" type=\"text\" value=\"" . $edit_value [$field_name] . "\" style=\"width: 220px\" autocomplete=\"off\"
						onkeyup=\"" . $event . "\" onmouseup=\"" . $event . "\"\">

			</div>\n";
	$out .= "</div><div id=\"loading_area\"></div>\n";
	return $out;
}

function ajax_dropdown_close() {
	$out = "<div style=\"float: right; cursor: pointer\" onclick=\"hide_block('loading_area')\">close X</div>\n";
	return $out;
}

function date_to_unix($date) {
	$date_parts = explode("-", $date);
	$unix_time = mktime(0, 0, 0, (int)$date_parts[1], (int)$date_parts[2], (int)$date_parts[0]);
	return (int)$unix_time;
}

function check_pid($check_type = "", $page = "register") {
	global $query, $module;
	$user_id = $_SESSION ['login'] ['user_id'];
	if ($query->group_perm("users", "admin") && get_int('user_id') !== 0) {
		$user_id = get_int('user_id');
	}
	
	if (! is_numeric(post('pid')) || strlen(post('pid')) !== 11) {
		header("Location: index.php?module=" . $module . "&page=" . $page . "&error=pid");
		exit();
	}
	$where = "";
	if ($check_type == "registered") {
		$where = " AND user_id != " . (int)$user_id;
	}
	if ($query->amount_fields("users_other_info", "pid = '" . post('pid') . "'" . $where) > 0) {
		header("Location: index.php?module=" . $module . "&page=" . $page . "&error=pid_exist");
		exit();
	}
}

function check_mail($mail, $check_type = "") {
	global $query, $query_l, $module, $user_class;
	$user_id = $user_class->current_user_id;
	if ($user_class->group_perm("users", "admin") && get_int('user_id') !== 0) {
		$user_id = get_int('user_id');
	}
	
	if (!preg_match("/^[_A-Za-z0-9-]+(\.[_A-Za-z0-9-]+)*@[A-Za-z0-9-]+(\.[A-Za-z0-9-]+)*(\.[A-Za-z]{2,3})$/", $mail)) {
		return _MAIL_SINTAX_ERROR;
	}
	$mail_parts = explode("@", $mail);
	if(!checkdnsrr(array_pop($mail_parts), "MX")){
		return _MAIL_SINTAX_ERROR;
	}
	
	$where = "";
	if ($check_type == "registered") {
		$where = " AND id != " . (int)$user_id;
	}
	$query_l->where_vars['mail'] = $mail;
	if ($query_l->amount_fields("users", "mail = '{{mail}}'" . $where) > 0) {
		return _MAIL_EXIST;
	}
	if (strpos($mail, "@") !== strrpos($mail, "@")) {
		return _MAIL_SINTAX_ERROR;
	}
	return 'ok';
}

function weight($weight, $length, $width, $height, $custom_price = 0, $nl = '-') {
	global $query, $conf;
	if ($custom_price > 0) {
		$shiping_price = $custom_price;
	} else {
		$shiping_price = $conf ['price'];
	}
	$weight = str_replace(',', '.', $weight);
	$length = str_replace(',', '.', $length);
	$width = str_replace(',', '.', $width);
	$height = str_replace(',', '.', $height);
	if (! is_array($conf)) {
		$conf = $query->select_ar_sql("gov_tracing_config", "price, rate", "id = 1");
	}
	if ($weight >= ($length * $width * $height) / 6000) {
		$weight = round($weight, 2);
		$weight_type = _OWN;
	} else {
		$weight = round($length * $width * $height / 6000, 2);
		$weight_type = _MOCULOBITI;
	}
	$moc_weight = round($length * $width * $height / 6000, 2);
	$price = round($weight * $shiping_price, 2) . " USD " . $nl . " " . round($weight * $shiping_price * $conf ['rate'], 2) . " GEL";
	$price_usd = round($weight * $shiping_price, 2) . " USD ";
	$price_gel = round($weight * $shiping_price * $conf ['rate'], 2) . " GEL";
	$price_gel_clear = trim($price_gel, ' GEL');
	$price_usd_clear = trim($price_usd, ' USD');
	$full_weight = $weight;
	if ($weight < $moc_weight)
		$full_weight = $moc_weight;
	return array (
			'full_weight' => $full_weight,
			'weight' => $weight,
			'type' => $weight_type,
			'price' => $price,
			'price_usd' => $price_usd,
			'price_gel' => $price_gel,
			'price_gel_clear' => $price_gel_clear,
			'price_usd_clear' => $price_usd_clear,
			'moc_weight' => $moc_weight 
	);
}

function shipping_price($price_usd) {
	global $conf;
	return round($price_usd * $conf ['rate'], 2);
}

function sub_trecing($trecing, $type = 0) {
	if (strlen($trecing) > 10 && $type == 0) {
		return "<a title=\"" . $trecing . "\">" . substr($trecing, (strlen($trecing) - 10)) . "</a>";
	} elseif ($type == 0) {
		return "<a title=\"" . $trecing . "\">" . $trecing . "</a>";
	} elseif ($type == 1) {
		return substr($trecing, (strlen($trecing) - 10));
	}
}

function generatePassword($length = 9, $strength = 1) {
	$consonants[1] = range(2, 9);
	$consonants[2] = range('a', 'z');
	$consonants[3] = range('A', 'Z');
    $bad_symbols = array('i', 'j', 'o', 'l', 'I', 'O');;
    $consonants[2] = array_diff($consonants[2], $bad_symbols);
    $consonants[3] = array_diff($consonants[3], $bad_symbols);
    
	$password = '';
	for($i = 0; $i < $length; $i ++) {
		$ar1 = rand(1, $strength);
		$ar2 = rand(0, (count($consonants [$ar1]) - 1));
		$password .= $consonants [$ar1] [$ar2];
	}
	return $password;
}

function microtime_float() {
	list ($usec, $sec) = explode(" ", microtime());
	return ((float)$usec + (float)$sec);
}

function time_dif() {
	global $time_start;
	$time_end = microtime_float();
	$time = $time_end - $time_start;
	return $time;
}

function send_mail($to, $title, $body) {
	global $send_mail, $query, $admiin_email, $global_conf;
	/* $mail_domain = explode('@', $admiin_email);
	$headers = array (
			'From' => $send_mail['from'],
			'To' => " <" . $to . ">",
			'Subject' => $title,
			'Message-Id' => "<" . md5(uniqid(microtime())) . "@".$mail_domain[1].">",
			'Date' => date("r"),
			'Content-type' => 'text/html; charset=utf-8' 
	);
	
	if($global_conf['reply_to'] !== ''){
		$headers['Reply-To'] = $global_conf['reply_to'];
	}
	
	$smtp = Mail::factory('smtp', array (
			'host' => $send_mail ['host'],
			'port' => $send_mail ['port'],
			'auth' => true,
			'username' => $send_mail ['username'],
			'password' => $send_mail ['password'],
			'debug' => true 
	));
	
	$mail = $smtp->send($to, $headers, $body);
	if (PEAR::isError($mail)) {
		$fields ['error_message'] = $mail->getMessage();
	} else {
		$fields ['error_message'] = $GLOBALS ['debug_message'];
	}
	$fields ['email'] = $to;
	$fields ['title'] = $title;
	$fields ['text'] = $body;
	$fields ['user_id'] = $_SESSION ['login'] ['user_id'];
	$query->insert_sql("mail_log", $fields); */
	
	require_once("functions/sendgrid/sendgrid-php.php");
	
	$sendgrid = new SendGrid($send_mail['sendgrid_key']);
	$email = new SendGrid\Email();
	$email
	->addTo($to)
	->setFrom($send_mail['from'])
	->setFromName($send_mail['from_name'])
	->setSubject($title)
	->setHtml($body);
	
	$report = $sendgrid->send($email);
	
	$fields ['error_message'] = $report->body['message'];
	
	$fields ['email'] = $to;
	$fields ['title'] = $title;
	$fields ['text'] = $body;
	$fields ['user_id'] = $_SESSION ['login'] ['user_id'];
	$query->insert_sql("mail_log", $fields);
}

function icon($icon_type, $other = ""){
	$icons['add'] = "add2.png";
	$icons['add2'] = "add_new.gif";
	$icons['accept'] = "accept.gif";
	$icons['decline'] = "decline.png";
	$icons['edit'] = "edit.png";
	$icons['copy'] = "copy.gif";
	$icons['save'] = "save.png";
	$icons['drop'] = "drop.png";
	$icons['drop_blue'] = "drop_blue.png";
	$icons['word'] = "word.gif";
	$icons['excel'] = "excel.gif";
	$icons['good_yellow'] = "good_yellow.png";
	$icons['good_red'] = "good_red.png";
	$icons['good_green'] = "good_green.png";
	$icons['bad'] = "good_0.png";
	$icons['arrow_up'] = "arrow_up_green.png";
	$icons['arrow_down'] = "arrow_down_blue.png";
	$icons['exams'] = "exams.png";
	$icons['archive'] = "archive.png";
	$icons['show'] = "show.png";
	$icons['hide'] = "hide.png";
	$icons['coution'] = "coution.gif";
	$icons['no_status'] = "no_status.png";
	$icons['error_sign'] = "error_sign.png";
	$icons['error_sign_green'] = "error_sign_green.png";
	$icons['book'] = "book.png";
	$icons['info'] = "info.png";
	$icons['gel'] = "gel.png";

	return "<img src=\"images/".$icons[$icon_type]."\" width=\"16\" border=\"0\" ".$other.">";
}

function request_callback($success, $message, $exec_function = "", $exec_function_parameters = ""){
	$data['success'] = strtolower($success);
	if($data['success'] == "ok"){
		$data['redirect_url'] = $message;
	}
	if($data['success'] == "ok_message"){
		$data['message'] = $message;
	}
	else{
		$data['error_message'] = $message;
	}
	
	if($exec_function !== ""){
		$data['exec_function'] = $exec_function;
		$data['exec_function_parameters'] = json_encode($exec_function_parameters);
	}
	return json_encode($data);
}

function current_time($time = ""){
	$time = $time == "" ? time() : $time;
	return date("Y-m-d H:i:s", $time);
}

function current_date($time = ""){
	$time = $time == "" ? time() : $time;
	return date("Y-m-d", $time);
}

function date_week_first_day($date, $date_from, $week){
	$week_first_day = strtotime((int)$date."W".sprintf("%02s", $week)."1");
	$week_first_day = current_date($week_first_day) < $date_from ? date_to_unix($date_from) : $week_first_day;
	
	return $week_first_day;
}

function date_week_last_day($date, $date_to, $week){
	$week_last_day = strtotime((int)$date."W".sprintf("%02s", $week)."7");
	$week_last_day = current_date($week_last_day) > $date_to ? date_to_unix($date_to) : $week_last_day;

	return $week_last_day;
}

function date_month_first_day($date, $date_from, $month){
	$month_first_day = (int)$date."-".sprintf("%02s", $month)."-01";
	$month_first_day = $month_first_day < $date_from ? date_to_unix($date_from) : date_to_unix($month_first_day);

	return $month_first_day;
}

function date_month_last_day($date, $date_to, $month){
	$month_last_day = (int)$date."-".sprintf("%02s", $month)."-".date("t", date_to_unix($date));
	$month_last_day = $month_last_day > $date_to ? date_to_unix($date_to) : date_to_unix($month_last_day);

	return $month_last_day;
}

function only_numbers($string){
	$output = preg_replace( '/[^0-9]/', '', $string );

	return $output;
}

function mobile_formated_number($number){
	$number = only_numbers($number);
	
	return $number[0].$number[1].$number[2]." ".$number[3].$number[4]."-".$number[5].$number[6]."-".$number[7].$number[8];
}

function progress_color($point){
	global $math;

	if((int)$point > 0 && (int)$point < 70){
		$color = "#ed9393";
	}
	if((int)$point > 70){
		$color = "#66c5f1";
	}
	if((int)$point > 90){
		$color = "#63d663";
	}

	return $color;
}

function email_formated_text($text, $width = 600){
	global $query, $query_l;

	$mail_template = $query_l->select_ar_sql("mail_templates", "*", "name = 'DEFAULT'");
	$mail_template['tamplate'] = str_replace("{{:body_width:}}", $width, $mail_template['tamplate']);
	return str_replace("{{:body:}}", $text, $mail_template['tamplate']);
}