<?php
/*	Project:	EQdkp-Plus
 *	Package:	Siggenerator Plugin
 *	Link:		http://eqdkp-plus.eu
 *
 *	Copyright (C) 2006-2015 EQdkp-Plus Developer Team
 *
 *	This program is free software: you can redistribute it and/or modify
 *	it under the terms of the GNU Affero General Public License as published
 *	by the Free Software Foundation, either version 3 of the License, or
 *	(at your option) any later version.
 *
 *	This program is distributed in the hope that it will be useful,
 *	but WITHOUT ANY WARRANTY; without even the implied warranty of
 *	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *	GNU Affero General Public License for more details.
 *
 *	You should have received a copy of the GNU Affero General Public License
 *	along with this program.  If not, see <http://www.gnu.org/licenses/>.
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
				
			$objQuery = $this->db->prepare("UPDATE __siggenerator_signatures :p WHERE id=?")->set($arrQuery)->execute($intSigID);
				
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