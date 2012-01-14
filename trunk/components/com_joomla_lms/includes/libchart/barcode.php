<?php
/**
* includes\libchart\barcode.php
* Joomla LMS Component
* * * ElearningForce Inc.
*/
defined( '_VALID_MOS' ) or die( 'Restricted access' );
/*===========================================================================*/
/*		PHP Barcode Image Generator (based on http://www.sid6581.net/cs/php-scripts/barcode/)
		The only barcode type currently supported is 'Code 3 of 9'.

		PARAMETERS:
		-----------
		$barcode        = [required] The barcode you want to generate
		
		$type           = (default=0) It's 0 for Code 3 of 9 (the only one supported)
		
		$width          = (default=160) Width of image in pixels. The image MUST be wide
									enough to handle the length of the given value. The default
									value will probably be able to display about 6 digits. If you
									get an error message, make it wider!
		
		$height         = (default=80) Height of image in pixels
		
		$format         = (default=jpeg) Can be "jpeg", "png", or "gif"
		
		$quality        = (default=100) For JPEG only: ranges from 0-100
		
		$text           = (default=1) 0 to disable text below barcode, >=1 to enable

		NOTE: You must have GD-1.8 or higher compiled into PHP
		in order to use PNG and JPEG. GIF images only work with
		GD-1.5 and lower. (http://www.boutell.com)

		ANOTHER NOTE: If you actually intend to print the barcodes 
		and scan them with a scanner, I highly recommend choosing 
		JPEG with a quality of 100. Most browsers can't seem to print 
		a PNG without mangling it beyond recognition. 

*/
/*=============================================================================*/

class JLMS_barcode extends JLMSObject {
	var $text = true; // (default='true') 'false' to disable text below barcode
	var $bar = true;
	var $bar_format = 'PNG';// don't change it! This library is not completed fully. (Can be "jpeg", "png", or "gif")
	var $bar_quality = 100;//  For JPEG only: ranges from 0-100
	var $width = 220;/* Width of image in pixels. The image MUST be wide
							enough to handle the length of the given value. The default
							value will probably be able to display about 6 digits. If you
							get an error message, make it wider! */
	var $height = 50;// Height of image in pixels
	var $bar_type = 1;//It's 1 for Code 3 of 9 (the only one supported)
	var $barcode = '';
	var $w_offset = 20;
	var $h_offset = 10;
	
	
	function __construct($barcode, $params = array()) {
		global $JLMS_CONFIG;
		$this->w_offset = $JLMS_CONFIG->get('crtf_option_barcode_right_offset', 20);
		$this->h_offset = $JLMS_CONFIG->get('crtf_option_barcode_bottom_offset', 10);
		$this->barcode = substr($barcode,0,10); // only 10 digits is supported

		if (in_array('text', $params)) {
			$this->text = true;
		} else { $this->text = false; }
		if (in_array('bar', $params)) {
			$this->bar = true;
		} else { $this->bar = false; }
	}

	function generate(&$im, $w_full, $h_full) {
		if ($this->text || $this->bar) {
		$White = ImageColorAllocate ($im, 255, 255, 255);
		$Black = ImageColorAllocate ($im, 0, 0, 0);

		$w_start = $w_full - $this->width - $this->w_offset;
		$h_start = $h_full - $this->height - $this->h_offset;

		$NarrowRatio = 20;
		$WideRatio = 55;
		$QuietRatio = 35;


		$nChars = (strlen($this->barcode)+2) * ((6 * $NarrowRatio) + (3 * $WideRatio) + ($QuietRatio));
		$Pixels = $this->width / $nChars;
		$NarrowBar = (int)(20 * $Pixels);
		$WideBar = (int)(55 * $Pixels);
		$QuietBar = (int)(35 * $Pixels);


		$ActualWidth = (($NarrowBar * 6) + ($WideBar*3) + $QuietBar) * (strlen ($this->barcode)+2);

		if (($NarrowBar == 0) || ($NarrowBar == $WideBar) || ($NarrowBar == $QuietBar) || ($WideBar == 0) || ($WideBar == $QuietBar) || ($QuietBar == 0)) {
			//ImageString ($im, 1, 0, 0, "Image is too small!", $Black);
		} else {
			$CurrentBarX = (int)(($this->width - $ActualWidth) / 2);
			$Color = $White;
			$BarcodeFull = "*".strtoupper ($this->barcode)."*";
			settype ($BarcodeFull, "string");
        
			$FontNum = 3;
			$FontHeight = ImageFontHeight ($FontNum);
			$FontWidth = ImageFontWidth ($FontNum);

			$ho = 0;
			if (!$this->bar) {
				$ho = $this->height - $FontHeight;
			}
			if ($this->bar) {
				ImageFilledRectangle ($im, $w_start, $h_start + $ho, $w_full - $this->w_offset, $h_full - $this->h_offset, $White);
			}

			if ($this->text) {
				$CenterLoc = (int)(($this->width-1) / 2) - (int)(($FontWidth * strlen($BarcodeFull)) / 2);
				ImageString ($im, $FontNum, $w_start + $CenterLoc, $h_start + $this->height-$FontHeight, "$BarcodeFull", $Black);
			} else {
				$FontHeight=-2;
			}
			if ($this->bar) {
				for ($i=0; $i<strlen($BarcodeFull); $i++) {
					$StripeCode = $this->Code39 ($BarcodeFull[$i]);
				
					for ($n=0; $n < 9; $n++) {
						if ($Color == $White) $Color = $Black;
						else $Color = $White;
	
						switch ($StripeCode[$n]) {
							case '0':
								ImageFilledRectangle ($im, $w_start + $CurrentBarX, $h_start, $w_start + $CurrentBarX+$NarrowBar,  $h_start + $this->height-1-$FontHeight-2, $Color);
								$CurrentBarX += $NarrowBar;
							break;
							case '1':
								ImageFilledRectangle ($im, $w_start + $CurrentBarX, $h_start, $w_start + $CurrentBarX+$WideBar, $h_start + $this->height-1-$FontHeight-2, $Color);
								$CurrentBarX += $WideBar;
							break;
						}
					}
	
					$Color = $White;
					ImageFilledRectangle ($im, $w_start + $CurrentBarX, $h_start, $w_start + $CurrentBarX+$QuietBar, $h_start + $this->height-1-$FontHeight-2, $Color);
					$CurrentBarX += $QuietBar;
				}
			}
		}
		} // end if ($this->text && $this->bar) {
	}
	function Code39 ($Asc) {
		switch ($Asc)
		{
			case ' ':
				return "011000100";     
			case '$':
				return "010101000";             
			case '%':
				return "000101010"; 
			case '*':
				return "010010100"; // * Start/Stop
			case '+':
				return "010001010"; 
			case '|':
				return "010000101"; 
			case '.':
				return "110000100"; 
			case '/':
				return "010100010"; 
			case '-':
				return "010000101";
			case '0':
				return "000110100"; 
			case '1':
				return "100100001"; 
			case '2':
				return "001100001"; 
			case '3':
				return "101100000"; 
			case '4':
				return "000110001"; 
			case '5':
				return "100110000"; 
			case '6':
				return "001110000"; 
			case '7':
				return "000100101"; 
			case '8':
				return "100100100"; 
			case '9':
				return "001100100"; 
			case 'A':
				return "100001001"; 
			case 'B':
				return "001001001"; 
			case 'C':
				return "101001000";
			case 'D':
				return "000011001";
			case 'E':
				return "100011000";
			case 'F':
				return "001011000";
			case 'G':
				return "000001101";
			case 'H':
				return "100001100";
			case 'I':
				return "001001100";
			case 'J':
				return "000011100";
			case 'K':
				return "100000011";
			case 'L':
				return "001000011";
			case 'M':
				return "101000010";
			case 'N':
				return "000010011";
			case 'O':
				return "100010010";
			case 'P':
				return "001010010";
			case 'Q':
				return "000000111";
			case 'R':
				return "100000110";
			case 'S':
				return "001000110";
			case 'T':
				return "000010110";
			case 'U':
				return "110000001";
			case 'V':
				return "011000001";
			case 'W':
				return "111000000";
			case 'X':
				return "010010001";
			case 'Y':
				return "110010000";
			case 'Z':
				return "011010000";
			default:
				return "011000100"; 
		}
	}
}
?>