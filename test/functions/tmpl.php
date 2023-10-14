<?php
class tmpl {
	var $module_ignore_fields = array();
	var $module_content = array();
	var $changeable_style = "";
	var $only_body = 0;
	
	function gen_html($array, $source, $file = 1) {
		$source = (int)$file == 1 ? read_file($source) : $source;
		if (is_array($array)) {
			foreach ( array_keys($array) as $key => $value ) {
				$array_keys[] = "{{:" . $value . ":}}";
			}
			$source = str_replace($array_keys, array_values($array), $source);	
		} else {
			$source = $source;
		}
		
		
		return $source;
	}
	
	function gen_loop_html($array, $source) {
		$source_key = @array_keys($source);
		if (is_array($array)) {
			foreach ( array_keys($array) as $key => $value ) {
				$array_keys [] = "{{:" . $value . ":}}";
			}
			$out = str_replace($array_keys, array_values($array), $source [$source_key [0]]);
			$this->module_content [$source_key [0]] .= $out;
		} else {
			$this->module_content [$source_key [0]] .= $source [$source_key [0]];
		}
	}
	
	function split_template($sector, $filename, $custom_src = 0) {
		global $module;
		if ($custom_src === 0) {
			$filename = "modules/" . $module . "/templates/" . $filename . ".html";
		} else {
			$filename = $custom_src . "/" . $filename.".html";
		}
		$content = read_file($filename);
		$split = explode("[:" . $sector . ":]", $content);
		return array (
				$sector => $split[1]
		);
	}
	
	function get_template_blocks($src, $filename, $content) {
		$cach_time = @filemtime($src . "/blocks/" . $filename . '.blocks');
		$tmpl_time = filemtime($src . $filename);
		if ($cach_time < $tmpl_time) {
			if(strpos($content, "[:") === false){
				return "";
			}
			$content = preg_replace(array(
					"@\n@",
					"@\r@",
					"@(:\](.*?)\[:)@",
					"@(.*?)\[:@",
					"@:\](.*?)$@"
			), array(
					"",
					"",
					",",
					"",
					""
			), $content, - 1, $count);
			$blocks = array();
			if ($count > 0) {
				$blocks = explode(',', $content);
				$blocks = array_unique($blocks);
			}
			if (! is_dir($src . "/blocks/")) {
				mkdir($src . "/blocks/");
			}
			$handle = fopen($src . "/blocks/" . $filename . '.blocks', 'w');
			fputcsv($handle, $blocks);
			fclose($handle);
		}
		$handle = fopen($src . "/blocks/" . $filename . '.blocks', 'r');
		$blocks = fgetcsv($handle);
		fclose($handle);
		return $blocks;
	}
	
    function gen_module_html($replace_fields, $filename, $custom_src = 0) {
		global $module;
		if ($custom_src === 0) {
			$src = "modules/" . $module . "/templates/";
		} else {
			$src = $custom_src;
		}
		$filename .= ".html";
		$content = read_file($src . $filename);
	
		$content = $this->gen_html($replace_fields, $content, 0);
	
		if (is_array($this->module_content)) {
			foreach ( $this->module_content as $key => $value ) {
				$out_temp = explode("[:" . $key . ":]", $content);
				$content = $out_temp[0] . $this->module_content[$key] . $out_temp[2];
			}
		}
		$blocks = $this->get_template_blocks($src, $filename, $content);
	
		//***** ignore fields **********
		if (is_array($this->module_ignore_fields)) {
			foreach ( $this->module_ignore_fields as $value ) {
				$ignore_fields_real [] = "[:" . $value . ":]";
			}
			$content = str_replace($ignore_fields_real, "", $content);
		}
		//******************************
		$content = preg_replace("/{{:([[:print:]]+):}}/", "", $content);
		$content = str_replace(array ("\n","\r"), "{|}", $content);
	
		//****** replace trash blocks ********
		$trash_blocks = @array_diff($blocks, $this->module_ignore_fields);
		if (count($trash_blocks) !== 0) {
			foreach ( $trash_blocks as $block_name ) {
				$trash_replace_block[] = "@(\[:" . $block_name . ":\])(.*?)(\[:" . $block_name . ":\])@";
			}
		}
		$trash_replace_block[] = "@(\[:(.*?):\])(.*?)(\[:(.*?):\])@";
		$content = preg_replace($trash_replace_block, "", $content);
		//************************************
		//$content = preg_replace("/(\|:\|(.*?)\|:\|)/", "", $content);
		$content = str_replace("{|}", "\n", $content);
		return $content;
	}
	
	function gen_module_custom_html($replace_fields, $filename, $content, $custom_src = 0) {
		global $module;
		if ($custom_src === 0) {
			$src = "modules/" . $module . "/templates/";
		} else {
			$src = $custom_src;
		}
		$filename = $filename.".html";
		
		$content = $this->gen_html($replace_fields, $content, 0);
	
		$blocks = $this->get_template_blocks($src, $filename, $content);
	
		//***** ignore fields **********
		if (is_array($this->module_ignore_fields)) {
			foreach ( $this->module_ignore_fields as $value ) {
				$ignore_fields_real [] = "[:" . $value . ":]";
			}
			$content = str_replace($ignore_fields_real, "", $content);
		}
		//******************************
		$content = preg_replace("/{{:([[:print:]]+):}}/", "", $content);
		$content = str_replace(array ("\n","\r"), "{|}", $content);
	
		//****** replace trash blocks ********
		$trash_blocks = @array_diff($blocks, $this->module_ignore_fields);
		if (count($trash_blocks) !== 0) {
			foreach ( $trash_blocks as $block_name ) {
				$trash_replace_block[] = "@(\[:" . $block_name . ":\])(.*?)(\[:" . $block_name . ":\])@";
			}
		}
		$trash_replace_block[] = "@(\[:(.*?):\])(.*?)(\[:(.*?):\])@";
		$content = preg_replace($trash_replace_block, "", $content);
		//************************************
		//$content = preg_replace("/(\|:\|(.*?)\|:\|)/", "", $content);
		$content = str_replace("{|}", "\n", $content);
		return $content;
	}
	
	function change_style($style1 = "table_td1", $style2 = "table_td2") {
		if ($this->changeable_style == $style2) {
			$this->changeable_style = $style1;
			return $style1;
		} else {
			$this->changeable_style = $style2;
			return $style2;
		}
	}
	
	function add_ignore_field($module, $permission, $field){
		global $user_class;
		if($user_class->group_perm($module, $permission)){
			if(!in_array($field, $this->module_ignore_fields)){
				$this->module_ignore_fields[] = $field;
			}
		}
	}
}