<?php
/*
* Project:		EQdkp-Plus
* License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
* Link:			http://creativecommons.org/licenses/by-nc-sa/3.0/
* -----------------------------------------------------------------------
* Began:		2010
* Date:			$Date: 2013-01-29 17:35:08 +0100 (Di, 29 Jan 2013) $
* -----------------------------------------------------------------------
* @author		$Author: wallenium $
* @copyright	2006-2014 EQdkp-Plus Developer Team
* @link			http://eqdkp-plus.eu
* @package		eqdkpplus
* @version		$Rev: 12937 $
*
* $Id: pdh_r_articles.class.php 12937 2013-01-29 16:35:08Z wallenium $
*/

if ( !defined('EQDKP_INC') ){
	die('Do not access this file directly.');
}
				
if ( !class_exists( "pdh_w_siggenerator" ) ) {
	class pdh_w_siggenerator extends pdh_w_generic{
	
		public function insert($arrSigData){
			
			$arrObjects = array(
				'left' 		=> $arrSigData['left'],
				'right'		=> $arrSigData['right'],
				'title'		=> $arrSigData['title'],
				'subtitle'	=> $arrSigData['subtitle'],
			);
			
			
			$arrQuery = array(
				'name' 				=> $arrSigData['name'],
				'background' 		=> $arrSigData['background'],
				'font'				=> $arrSigData['font'],
				'font_border_color' => $arrSigData['font_border_color'],
				'font_border_size'	=> $arrSigData['font_border_size'],
				'font_color' 		=> $arrSigData['font_color'],
				'picture_preset'	=> $arrSigData['picture_preset'],
				'objects'			=> serialize($arrObjects),
				'date'				=> $this->time->time,
			);
			
			$objQuery = $this->db->prepare("INSERT INTO __siggenerator_signatures :p")->set($arrQuery)->execute();
			
			if ($objQuery){
				$id = $objQuery->insertId;
			
				$this->pdh->enqueue_hook('siggenerator_signatures_update');
				return $id;
			}
				
			return false;
		}
		
		public function update($intSigID, $arrSigData){
				
			$arrObjects = array(
					'left' 		=> $arrSigData['left'],
					'right'		=> $arrSigData['right'],
					'title'		=> $arrSigData['title'],
					'subtitle'	=> $arrSigData['subtitle'],
			);
				
				
			$arrQuery = array(
					'name' 				=> $arrSigData['name'],
					'background' 		=> $arrSigData['background'],
					'font'				=> $arrSigData['font'],
					'font_border_color' => $arrSigData['font_border_color'],
					'font_border_size'	=> $arrSigData['font_border_size'],
					'font_color' 		=> $arrSigData['font_color'],
					'picture_preset'	=> $arrSigData['picture_preset'],
					'objects'			=> serialize($arrObjects),
					'date'				=> $this->time->time,
			);
				
			$objQuery = $this->db->prepare("UPDATE __siggenerator_signatures :p")->set($arrQuery)->execute($intSigID);
				
			if ($objQuery){					
				$this->pdh->enqueue_hook('siggenerator_signatures_update');
				return $intSigID;
			}
		
			return false;
		}
		
		public function delete($intSigID){
			$objQuery = $this->db->prepare("DELETE FROM __siggenerator_signatures WHERE id =?")->execute($intSigID);
			$this->pdh->enqueue_hook('siggenerator_signatures_update');
			return true;
		}

	}//end class
}//end if
?>