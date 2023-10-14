<?

	//
	// Verification Image.
	// v0.1
	//
	// An antispam image, generates a code, creates an image out of it, and
	// registers this code in a session. User input will be checked against
	// this session value. If it is valid no spambot is the one that is submitting
	// this form.
	//
	// Install instructions:
	// Put the file of your ttf in the same directory as this script. Also note that
	// you call session_start() before using this class in any script.
	//
	//
	// As always I appreciate feedback. So don't hesitate to contact me.
	//
	// author: Jaap van der Meer (jaap@web-radiation.nl)
	//



	class text_image {
		// the image that will be outputted
		var $image;

		// the width of the image thats outputted
		var $_w;

		// the height of the image that's outputted
		var $_h;

		// the color used for the text
		var $text_color;

		// the background used in the the text
		var $bg_color;

		// the font to be user
		var $ttf_font;

		var $text;


		// constructor to setup the image properties
		// width - the width of the image
		// height - the height of the image
		// font - the font to be used, must be in same directory
		function text_image($width = 120, $height = 40, $font = "", $text = "") {
			$this->_w = $width;
			$this->_h = $height;

			$this->ttf_font = $font;
			$this->text = $text;
		}


		// initializes the image
		function init() {

			$this->image = imagecreate($this->_w, $this->_h);
			//$background_color = imagecolorallocate($this->image, 255, 255, 255);
			$bgcolor = $this->html2rgb($this->bgcolor);
			$this->set_bgcolor($bgcolor[0], $bgcolor[1], $bgcolor[2]);
			$textcolor = $this->html2rgb($this->textcolor);
			$this->set_textcolor($textcolor[0], $textcolor[1], $textcolor[2]);

		}

		// sets the bgcolor
		function set_bgcolor($r,$g,$b) {
			$background_color = imagecolorallocate($this->image, $r, $g, $b);

		}

		// sets the textcolor
		function set_textcolor($r,$g,$b) {
			$this->text_color = imagecolorallocate($this->image, $r, $g, $b);
		}

		// draws the string
		function draw() {
			$code = $this->text;
			$this->write_string(1, 22, $code);

		}


		function write_string($x_offset, $y_offset, $string) {

				// check if a font is set
				if($this->ttf_font != "") {
					// does the file font exist on the server
					if(file_exists($this->ttf_font)) {
						putenv('GDFONTPATH=' . realpath('.'));
						$font_size = $this->font_size;
						$textcolor2 = $this->html2rgb($this->textcolor2);
						$grey = imagecolorallocate($this->image, $textcolor2[0], $textcolor2[1], $textcolor2[2]);
						// draw a shadow
						imagettftext($this->image, $font_size, 0, $x_offset + 0.8, $y_offset + 0.8, $grey, $this->ttf_font, $string);
						// draw the text
						imagettftext($this->image, $font_size, 0, $x_offset, $y_offset, $this->text_color, $this->ttf_font, $string);

					} else {
						die("Font doesn't exist, or not in same directory as a .ttf");
					}
				} else {
					die("No font set.");
				}
		}


		function _output() {
			// initialize the image
			$this->init();
			// draw the image
			$this->draw();

			header("Content-type: image/png");
			imagepng($this->image);

			// destroy the image to free resources
			imagedestroy($this->image);
		}

		function html2rgb($color){
    		if ($color[0] == '#')
        	$color = substr($color, 1);

		    if (strlen($color) == 6)
		        list($r, $g, $b) = array($color[0].$color[1],
        		                         $color[2].$color[3],
		                                 $color[4].$color[5]);
		    elseif (strlen($color) == 3)
		        list($r, $g, $b) = array($color[0].$color[0], $color[1].$color[1], $color[2].$color[2]);
		    else
		        return false;

		    $r = hexdec($r); $g = hexdec($g); $b = hexdec($b);

    		return array($r, $g, $b);
		}

}




?>