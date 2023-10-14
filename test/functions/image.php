<?php
class thumbnail
{
    var $img;
    var $crop = 0;

    function thumbnail($imgfile)
    {
        //detect image format
        $this->img["format"]=preg_replace("/.*\.(.*)$/","\\1",$imgfile);
        $this->img["format"]=strtoupper($this->img["format"]);
        if ($this->img["format"]=="JPG" || $this->img["format"]=="JPEG") {
            //JPEG
            $this->img["format"]="JPEG";
            $this->img["src"] = ImageCreateFromJPEG ($imgfile);
        } elseif ($this->img["format"]=="PNG") {
            //PNG
            $this->img["format"]="PNG";
            $this->img["src"] = ImageCreateFromPNG ($imgfile);
        } elseif ($this->img["format"]=="GIF") {
            //GIF
            $this->img["format"]="GIF";
            $this->img["src"] = ImageCreateFromGIF ($imgfile);
        } elseif ($this->img["format"]=="WBMP") {
            //WBMP
            $this->img["format"]="WBMP";
            $this->img["src"] = ImageCreateFromWBMP ($imgfile);
        } else {
            //DEFAULT
            echo "Not Supported File";
            exit();
        }
        @$this->img["lebar"] = imagesx($this->img["src"]);
        @$this->img["tinggi"] = imagesy($this->img["src"]);
        //default quality jpeg
        $this->img["quality"]=100;
    }

    function size_height($size=100)
    {
        //height
        $this->img["tinggi_thumb"]=$size;
        @$this->img["lebar_thumb"] = ($this->img["tinggi_thumb"]/$this->img["tinggi"])*$this->img["lebar"];
    }

    function size_width($size=100)
    {
        //width
        $this->img["lebar_thumb"]=$size;
        @$this->img["tinggi_thumb"] = ($this->img["lebar_thumb"]/$this->img["lebar"])*$this->img["tinggi"];
    }

    function size_auto($size=100)
    {
        //size
        if ($this->img["lebar"]>=$this->img["tinggi"]) {
            $this->img["lebar_thumb"]=$size;
            @$this->img["tinggi_thumb"] = ($this->img["lebar_thumb"]/$this->img["lebar"])*$this->img["tinggi"];
        } else {
            $this->img["tinggi_thumb"]=$size;
            @$this->img["lebar_thumb"] = ($this->img["tinggi_thumb"]/$this->img["tinggi"])*$this->img["lebar"];
        }
    }

    function jpeg_quality($quality=100)
    {
        //jpeg quality
        $this->img["quality"]=$quality;
    }

    function show()
    {
        //show thumb
        @Header("Content-Type: image/".$this->img["format"]);

        /* change ImageCreateTrueColor to ImageCreate if your GD not supported ImageCreateTrueColor function*/
        $this->img["des"] = ImageCreateTrueColor($this->img["lebar_thumb"],$this->img["tinggi_thumb"]);
            @imagecopyresampled ($this->img["des"], $this->img["src"], 0, 0, 0, 0, $this->img["lebar_thumb"], $this->img["tinggi_thumb"], $this->img["lebar"], $this->img["tinggi"]);
         
        if ($this->img["format"]=="JPG" || $this->img["format"]=="JPEG") {
            //JPEG
            imageJPEG($this->img["des"],"",$this->img["quality"]);
        } elseif ($this->img["format"]=="PNG") {
            //PNG
        	imagealphablending($this->img["des"], false);
        	imagesavealpha($this->img["des"], true);
        	
        	$trans_layer_overlay = imagecolorallocatealpha($this->img["des"], 220, 220, 220, 127);
        	imagefill($this->img["des"], 0, 0, $trans_layer_overlay);
            imagePNG($this->img["des"]);
        } elseif ($this->img["format"]=="GIF") {
            //GIF
            imageGIF($this->img["des"]);
        } elseif ($this->img["format"]=="WBMP") {
            //WBMP
            imageWBMP($this->img["des"]);
        }
    }
    
    function save($save="")
    {
        //save thumb
        if (empty($save)) $save=strtolower("./thumb.".$this->img["format"]);
        /* change ImageCreateTrueColor to ImageCreate if your GD not supported ImageCreateTrueColor function*/
        $this->img["des"] = ImageCreateTrueColor($this->img["lebar_thumb"],$this->img["tinggi_thumb"]);
        	if ($this->img["format"]=="PNG") {
        		//tranparent
	        	imagealphablending($this->img["des"], false);
		        imagesavealpha($this->img["des"],true);
		        $transparent = imagecolorallocatealpha($this->img["des"], 255, 255, 255, 127);
		        imagefilledrectangle($this->img["des"], 0, 0, $this->img["lebar_thumb"], $this->img["tinggi_thumb"], $transparent);
        	}
		    
		if($this->crop == 1){
			if($this->img["lebar_thumb"] > $this->img["tinggi_thumb"]){
				$x = ($this->img["lebar_thumb"] - $this->img["tinggi_thumb"]) / 2;
				$y = 0;
				$crop_measure = $this->img["tinggi_thumb"];				
			}
			else{
				$x = 0;
				$y = ($this->img["tinggi_thumb"] - $this->img["lebar_thumb"]) / 2;
				$crop_measure = $this->img["lebar_thumb"];
			}
			$to_crop_array = array('x' => $x , 'y' =>  $y, 'width' => $crop_measure, 'height'=> $crop_measure);
			
			@imagecopyresampled ($this->img["des"], $this->img["src"], 0, 0, $x, $y, $crop_measure, $crop_measure, $this->img["lebar"], $this->img["tinggi"]);
		}
		else{
			@imagecopyresampled ($this->img["des"], $this->img["src"], 0, 0, 0, 0, $this->img["lebar_thumb"], $this->img["tinggi_thumb"], $this->img["lebar"], $this->img["tinggi"]);
		}
		
		    
        if ($this->img["format"]=="JPG" || $this->img["format"]=="JPEG") {
            //JPEG
            imageJPEG($this->img["des"],"$save",$this->img["quality"]);
        } elseif ($this->img["format"]=="PNG") {
            //PNG 	
            imagePNG($this->img["des"],"$save");
        } elseif ($this->img["format"]=="GIF") {
            //GIF
            imageGIF($this->img["des"],"$save");
        } elseif ($this->img["format"]=="WBMP") {
            //WBMP
            imageWBMP($this->img["des"],"$save");
        }
    }
}
?>
