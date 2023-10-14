<?php
ini_set('max_execution_time', 8000);
include 'init.php';
$img_src['middle'] = "/middle";
$img_src['small'] = "/thumb";
$img_src['tiny'] = "/tiny";
$img_percent['middle'] = 60;
$img_percent['small'] = 33;
$img_percent['tiny'] = 20;

$img_size = "tiny";
$dir = "upload/math/".$img_src[$img_size]."/";



// Open a known directory, and proceed to read its contents
if (is_dir($dir)) {
	if ($dh = opendir($dir)) {
		while (($file = readdir($dh)) !== false) {
			//echo "filename: $file : filetype: " . filetype($dir . $file) . "\n";
			if(is_file($dir.$file)){
				copy_image_percent("upload/math/".$file, $dir.$file, $img_percent[$img_size]);
			}
		}
					closedir($dh);
	}
	}
