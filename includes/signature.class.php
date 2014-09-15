<?php
/*
 * Project:     EQdkp guildrequest
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:        http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2008
 * Date:        $Date: 2012-10-13 22:48:23 +0200 (Sa, 13. Okt 2012) $
 * -----------------------------------------------------------------------
 * @author      $Author: godmod $
 * @copyright   2008-2011 Aderyn
 * @link        http://eqdkp-plus.com
 * @package     guildrequest
 * @version     $Rev: 12273 $
 *
 * $Id: archive.php 12273 2012-10-13 20:48:23Z godmod $
 */

	class signature extends gen_class {
		
		
		public function createSignature($intCharID, $arrSignatureData, $strFilename){
			
			//Background
			$strBackground = $this->root_path.$arrSignatureData['background'];
			
			//Create Background Image
			$strExtension =  strtolower(pathinfo($strBackground, PATHINFO_EXTENSION));
			switch($strExtension)
			{
				case "jpg":
					$img = imagecreatefromjpeg($strBackground);
					break;
				case "gif":
					$img = imagecreatefromgif($strBackground);
					break;
				case "png":
					$img = imagecreatefrompng($strBackground);
					break;
			}
			
			//Colors:
			$fontColor = $this->hex2rgb($arrSignatureData['font_color']);
			$fontColorRes = ImageColorAllocate($img, $fontColor[0], $fontColor[1], $fontColor[2]);
			
			//Margin
			$intMargin = 4;
			$marginRechtsLinks = 10;
			$intImageMargin = 10;
			
			//Charicon
			$arrPresets = $this->pdh->get_preset($arrSignatureData['picture_preset']);
			$subArray = array('%member_id%' => $intCharID);

			$strCharicon = $this->pdh->get($arrPresets[0], $arrPresets[1], $arrPresets[2], $subArray);
			$strCharicon = str_replace($this->server_path, $this->root_path, $strCharicon);
			if (strlen($strCharicon)){
			
				$strChariconExtension = strtolower(pathinfo($strCharicon, PATHINFO_EXTENSION));
				switch($strChariconExtension){
					case "jpg":
						$charicon = imagecreatefromjpeg($strCharicon);
						break;
					case "gif":
						$charicon = imagecreatefromgif($strCharicon);
						break;
					case "png":
						$charicon = imagecreatefrompng($strCharicon);
						break;
				}
	
				
				$intImageSize = 48;
				imagefilledrectangle($img, $marginRechtsLinks, $intMargin, $intImageSize+$marginRechtsLinks+1, $intImageSize+$intMargin+1, $fontColorRes);
				imagecopyresampled($img, $charicon, $marginRechtsLinks+1, $intMargin+1,0,0,$intImageSize,$intImageSize,ImageSX($charicon), ImageSY($charicon));
				
				$intImageMargin = 70;
			}
			//Load Font
			$font = $this->root_path.$arrSignatureData['font'];
			
			
			$strokeColor = $this->hex2rgb($arrSignatureData['font_border_color']);
			$strokeColorRes = imagecolorallocate($img,  $strokeColor[0], $strokeColor[1], $strokeColor[2]);
			
			$intStrokeWidth = (int)$arrSignatureData['font_border_size'];
			
			$arrTitle = $arrSubtitle = $arrRight = $arrLeft = array();
			
			//Title
			if (isset($arrSignatureData['title'])){
				foreach($arrSignatureData['title'] as $key => $arrData){
					$myPreset = $arrData[0];
					$intShowLabel = $arrData[1];
					
					$myStrPos = strpos($myPreset, '_mdkp');
					if ($myStrPos !== false){
						$realPreset = substr($myPreset, 0, $myStrPos);
						$dkp_id = substr($myPreset, $myStrPos+5);
							
						$arrPresets = $this->pdh->get_preset($realPreset);
						$subArray = array('%dkp_id%' => $dkp_id, '%member_id%' => $intCharID, '%with_twink%' =>!intval($this->config->get('show_twinks')));
						$value = $this->pdh->get($arrPresets[0], $arrPresets[1], $arrPresets[2], $subArray);
						$strDKPName = $this->pdh->get('multidkp', 'name', array($dkp_id));	
						$strPresetname = $this->pdh->get_caption($arrPresets[0], $arrPresets[1], $this->pdh->post_process_preset($arrPresets[3], $subArray)).' ['.$strDKPName.']';
						if (is_float($value)) $value = runden($value);
					} else {
						$arrPresets = $this->pdh->get_preset($myPreset);
						$subArray = array('%member_id%' => $intCharID, '%with_twink%' =>!intval($this->config->get('show_twinks')));
						$value = $this->pdh->get($arrPresets[0], $arrPresets[1], $arrPresets[2], $subArray);
						$strPresetname = $this->pdh->get_caption($arrPresets[0], $arrPresets[1], $this->pdh->post_process_preset($arrPresets[3], $subArray));
					}
					
					$arrTitle[] = ($intShowLabel) ? $strPresetname.' '.$value : $value;
				}
			}
			
			$strTitle = implode(', ', $arrTitle);		
			$this->imagettfstroketext( $img , 20 , 0, $intImageMargin, 26 , $fontColorRes , $strokeColorRes, $font , $strTitle, $intStrokeWidth);
			
			//Subtitle
			if (isset($arrSignatureData['subtitle'])){
				foreach($arrSignatureData['subtitle'] as $key => $arrData){
					$myPreset = $arrData[0];
					$intShowLabel = $arrData[1];
					
					$myStrPos = strpos($myPreset, '_mdkp');
					if ($myStrPos !== false){
						$realPreset = substr($myPreset, 0, $myStrPos);
						$dkp_id = substr($myPreset, $myStrPos+5);
				
						$arrPresets = $this->pdh->get_preset($realPreset);
						$subArray = array('%dkp_id%' => $dkp_id, '%member_id%' => $intCharID, '%with_twink%' =>!intval($this->config->get('show_twinks')));
						$value = $this->pdh->get($arrPresets[0], $arrPresets[1], $arrPresets[2], $subArray);
						$strDKPName = $this->pdh->get('multidkp', 'name', array($dkp_id));	
						$strPresetname = $this->pdh->get_caption($arrPresets[0], $arrPresets[1], $this->pdh->post_process_preset($arrPresets[3], $subArray)).' ['.$strDKPName.']';
						if (is_float($value)) $value = runden($value);
					} else {
						$arrPresets = $this->pdh->get_preset($myPreset);
						$subArray = array('%member_id%' => $intCharID, '%with_twink%' =>!intval($this->config->get('show_twinks')));
						$value = $this->pdh->get($arrPresets[0], $arrPresets[1], $arrPresets[2], $subArray);
						$strPresetname = $this->pdh->get_caption($arrPresets[0], $arrPresets[1], $this->pdh->post_process_preset($arrPresets[3], $subArray));
					}
				
					$arrSubtitle[] = ($intShowLabel) ? $strPresetname.' '.$value : $value;
				}
			}
				
			$strSubtitle = implode(', ', $arrSubtitle);
			$this->imagettfstroketext( $img , 12 , 0, $intImageMargin, 50 , $fontColorRes , $strokeColorRes, $font , $strSubtitle, $intStrokeWidth);

			
			//Left
			if (isset($arrSignatureData['left'])){
				foreach($arrSignatureData['left'] as $key => $arrData){
					$myPreset = $arrData[0];
					$intShowLabel = $arrData[1];
					
					if ($myPreset == '-') continue;
					
					$myStrPos = strpos($myPreset, '_mdkp');
					if ($myStrPos !== false){
						$realPreset = substr($myPreset, 0, $myStrPos);
						$dkp_id = substr($myPreset, $myStrPos+5);
							
						$arrPresets = $this->pdh->get_preset($realPreset);
						$subArray = array('%dkp_id%' => $dkp_id, '%member_id%' => $intCharID, '%with_twink%' =>!intval($this->config->get('show_twinks')));
						$value = $this->pdh->get($arrPresets[0], $arrPresets[1], $arrPresets[2], $subArray);
						$strDKPName = $this->pdh->get('multidkp', 'name', array($dkp_id));	
						$strPresetname = $this->pdh->get_caption($arrPresets[0], $arrPresets[1], $this->pdh->post_process_preset($arrPresets[3], $subArray)).' ['.$strDKPName.']';
						
						if (is_float($value)) $value = runden($value);
					} else {
						$arrPresets = $this->pdh->get_preset($myPreset);
						$subArray = array('%member_id%' => $intCharID, '%with_twink%' =>!intval($this->config->get('show_twinks')));
						$value = $this->pdh->get($arrPresets[0], $arrPresets[1], $arrPresets[2], $subArray);
						$strPresetname = $this->pdh->get_caption($arrPresets[0], $arrPresets[1], $this->pdh->post_process_preset($arrPresets[3], $subArray));
					}
	
					$value = ($intShowLabel) ? $strPresetname.': '.$value : $value;
					$this->imagettfstroketext ( $img , 11 , 0, $marginRechtsLinks, 52+(15*($key+1)) , $fontColorRes , $strokeColorRes, $font , $value, $intStrokeWidth);
				}
			}
			
			//Right
			if (isset($arrSignatureData['right'])){
				foreach($arrSignatureData['right'] as $key => $arrData){
					if ($myPreset == '-') continue;
					
					$myPreset = $arrData[0];
					$intShowLabel = $arrData[1];
					
					$myStrPos = strpos($myPreset, '_mdkp');
					if ($myStrPos !== false){
						$realPreset = substr($myPreset, 0, $myStrPos);
						$dkp_id = substr($myPreset, $myStrPos+5);
				
						$arrPresets = $this->pdh->get_preset($realPreset);
						$subArray = array('%dkp_id%' => $dkp_id, '%member_id%' => $intCharID, '%with_twink%' =>!intval($this->config->get('show_twinks')));
						$value = $this->pdh->get($arrPresets[0], $arrPresets[1], $arrPresets[2], $subArray);
						$strDKPName = $this->pdh->get('multidkp', 'name', array($dkp_id));	
						
						if (is_float($value)) $value = runden($value);
						
						$strPresetname = $this->pdh->get_caption($arrPresets[0], $arrPresets[1], $this->pdh->post_process_preset($arrPresets[3], $subArray)).' ['.$strDKPName.']';
						
					} else {
						$arrPresets = $this->pdh->get_preset($myPreset);
						$subArray = array('%member_id%' => $intCharID, '%with_twink%' =>!intval($this->config->get('show_twinks')));
						$value = $this->pdh->get($arrPresets[0], $arrPresets[1], $arrPresets[2], $subArray);
						$strPresetname = $this->pdh->get_caption($arrPresets[0], $arrPresets[1], $this->pdh->post_process_preset($arrPresets[3], $subArray));
					}
					
					$value = ($intShowLabel) ? $strPresetname.': '.$value : $value;
					
					$dimensions = imagettfbbox(11, 0, $font, $value);
					$textWidth = abs($dimensions[4] - $dimensions[0]);
					$x = imagesx($img) - $textWidth;

					$this->imagettfstroketext ( $img , 11 , 0, $x-($marginRechtsLinks), 5+(15*($key+1)) , $fontColorRes , $strokeColorRes, $font , $value, $intStrokeWidth);
				}
			}
			
			//URL
			$strURL = str_replace(array('http://', 'https://'), '', $this->env->buildlink());
			
			$dimensions = imagettfbbox(7, 0, $font, $strURL);
			$textWidth = abs($dimensions[4] - $dimensions[0]);
			$x = imagesx($img) - $textWidth;
			imagettftext ( $img , 7 , 0, $x-$marginRechtsLinks, 100-$intMargin , $fontColorRes , $font ,$strURL);
			
			
			
			//Save that thing
			$strFolder = $this->pfh->FolderPath('sigs', 'signatures');

			imagejpeg($img,$strFolder.$strFilename,100);
			
			return $strFolder.$strFilename;
		}
		
		public function errorimage($strText){
			$image = imagecreate ( 500 , 100 );	
			$arrColor = $this->hex2rgb('2c73a9');
			$backgroundColor = imagecolorallocate($image, $arrColor[0], $arrColor[1], $arrColor[2]);
			imagefill($image, 0, 0, $backgroundColor);
			
			//Colors:
			$fontColor = $this->hex2rgb('fff');
			$fontColorRes = ImageColorAllocate($image, $fontColor[0], $fontColor[1], $fontColor[2]);
			
			$strokeColor = $this->hex2rgb('000');
			$strokeColorRes = ImageColorAllocate($image, $strokeColor[0], $strokeColor[1], $strokeColor[2]);
			
			$font = $this->root_path.'plugins/siggenerator/includes/fonts/OpenSans-Regular.ttf';
			
			$this->imagettfstroketext ( $image , 16 , 0, 10, 60 , $fontColorRes , $strokeColorRes, $font , $strText, 1);
			
			//URL
			$strURL = str_replace(array('http://', 'https://'), '', $this->env->buildlink());
				
			$dimensions = imagettfbbox(7, 0, $font, $strURL);
			$textWidth = abs($dimensions[4] - $dimensions[0]);
			$x = imagesx($image) - $textWidth;
			imagettftext ( $image , 7 , 0, $x-10, 100-4 , $fontColorRes , $font ,$strURL);
			
			header("Content-disposition: inline; filename=signature.png");
			header("content-type: image/png");
			imagepng($image, null, 0);
			exit();
		}

		public function hex2rgb($hex) {
			$hex = str_replace("#", "", $hex);
		
			if(strlen($hex) == 3) {
				$r = hexdec(substr($hex,0,1).substr($hex,0,1));
				$g = hexdec(substr($hex,1,1).substr($hex,1,1));
				$b = hexdec(substr($hex,2,1).substr($hex,2,1));
			} else {
				$r = hexdec(substr($hex,0,2));
				$g = hexdec(substr($hex,2,2));
				$b = hexdec(substr($hex,4,2));
			}
			$rgb = array($r, $g, $b);
			//return implode(",", $rgb); // returns the rgb values separated by commas
			return $rgb; // returns an array with the rgb values
		}
		
		public function imagettfstroketext(&$image, $size, $angle, $x, $y, &$textcolor, &$strokecolor, $fontfile, $text, $px) {
			for($c1 = ($x-abs($px)); $c1 <= ($x+abs($px)); $c1++)
				for($c2 = ($y-abs($px)); $c2 <= ($y+abs($px)); $c2++)
					$bg = imagettftext($image, $size, $angle, $c1, $c2, $strokecolor, $fontfile, $text);
		
					return imagettftext($image, $size, $angle, $x, $y, $textcolor, $fontfile, $text);
		}
		
		public function scan_fonts(){
			$arrOut = array();
			$strFolder = $this->root_path.'plugins/siggenerator/includes/fonts/';
			$arrFiles = scandir($strFolder);
			foreach($arrFiles as $filename){
				if (valid_folder($filename) && pathinfo($filename, PATHINFO_EXTENSION) == 'ttf') $arrOut[str_replace($this->root_path, '', $strFolder.$filename)] = $filename;
			}
			
			$strFolder = $this->pfh->FolderPath('fonts', 'signatures');
			$arrFiles = scandir($strFolder);
			foreach($arrFiles as $filename){
				if (valid_folder($filename) && pathinfo($filename, PATHINFO_EXTENSION) == 'ttf') $arrOut[str_replace($this->root_path, '', $strFolder.$filename)] = $filename;
			}
	
			return $arrOut;
		}
		
		public function scan_backgrounds(){
			$arrOut = array();
			$strFolder = $this->root_path.'plugins/siggenerator/includes/backgrounds/';
			$arrFiles = scandir($strFolder);
			$arrExtensions = array('jpg', 'gif', 'png');

			foreach($arrFiles as $filename){
				if (valid_folder($filename)) {
					$strExtension = pathinfo($filename, PATHINFO_EXTENSION);
					if (in_array($strExtension, $arrExtensions)) $arrOut[str_replace($this->root_path, '', $strFolder.$filename)] = $filename;
				}
			}
			
			$strFolder = $this->pfh->FolderPath('backgrounds', 'signatures');
			$arrFiles = scandir($strFolder);
			$arrExtensions = array('jpg', 'gif', 'png');
			foreach($arrFiles as $filename){
				if (valid_folder($filename)) {
					$strExtension = pathinfo($filename, PATHINFO_EXTENSION);
					if (in_array($strExtension, $arrExtensions)) $arrOut[str_replace($this->root_path, '', $strFolder.$filename)] = $filename;
				}
			}

			return $arrOut;
		}

	}
?>