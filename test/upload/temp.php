<?php
$dir = "math";

if (is_dir($dir)) {
    if ($dh = opendir($dir)) {
        while (($file = readdir($dh)) !== false) {
			if(strpos($file, ")")){
        		$new_file_name = str_replace(")", "", $file);
        		rename($dir."/".$file, $dir."/".$new_file_name);
        		rename($dir."/thumb/".$file, $dir."/thumb/".$new_file_name);
			}
        }
        closedir($dh);
    }
}