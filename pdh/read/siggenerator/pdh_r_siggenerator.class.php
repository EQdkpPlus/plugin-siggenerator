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

if ( !class_exists( "pdh_r_siggenerator" ) ) {
	class pdh_r_siggenerator extends pdh_r_generic{
		public static function __shortcuts() {
		$shortcuts = array('crypt' => 'encrypt');
		return array_merge(parent::$shortcuts, $shortcuts);
	}

	public $default_lang = 'english';
	public $sigs = null;

	public $hooks = array(
		'siggenerator_signatures_update',
	);

	public $presets = array(
			'siggen_editicon'	=> array('editicon', array('%intSigID%'), array()),
			'siggen_name'		=> array('name', array('%intSigID%'), array()),
			'siggen_date'		=> array('date', array('%intSigID%'), array()),
			'siggen_preview'	=> array('preview', array('%intSigID%'), array()),
	);

	public function reset(){
			$this->pdc->del('pdh_siggenerator_table');
			
			$this->sigs = NULL;
	}

	public function init(){
			$this->sigs	= $this->pdc->get('pdh_siggenerator_table');

			if($this->sigs !== NULL){
				return true;
			}

			$objQuery = $this->db->query('SELECT * FROM __siggenerator_signatures');
			if($objQuery){
				while($drow = $objQuery->fetchAssoc()){
					$this->sigs[(int)$drow['id']] = array(
						'id'				=> (int)$drow['id'],
						'name'				=> $drow['name'],
						'background'		=> $drow['background'],
						'font'				=> $drow['font'],
						'font_color'		=> $drow['font_color'],
						'font_border_color'	=> $drow['font_border_color'],
						'font_border_size'	=> $drow['font_border_size'],
						'picture_preset'	=> $drow['picture_preset'],
						'objects'			=> unserialize($drow['objects']),
						'date'				=> (int)$drow['date'],
					);
				}
				
				$this->pdc->put('pdh_siggenerator_table', $this->sigs, null);
			}

		}	//end init function

		/**
		 * @return multitype: List of all IDs
		 */				
		public function get_id_list(){
			if ($this->sigs === null) return array();
			return array_keys($this->sigs);
		}
		
		public function get_data($intSigID){
			return $this->sigs[$intSigID];
		}
		
		public function get_name($intSigID){
			return $this->sigs[$intSigID]['name'];
		}
		
		public function get_editicon($intSigID){
			return '<a href="signatures.php'.$this->SID.'&sig='.$intSigID.'"><i class="fa fa-pencil fa-lg" title="'.$this->user->lang('edit').'"></i></a>';
		}
		
		public function get_preview($intSigID){
			$arrMembers = $this->pdh->get('member', 'id_list');
			if (isset($arrMembers[0])){
				$src = $this->env->buildlink().$this->controller_path_plain.'CharSignature/Char-'.$arrMembers[0].'/?sig='.$intSigID.'&key='.$this->crypt->encrypt($arrMembers[0]);

				return '<img src="'.$src.'" height="60" />';
			}
			return '';
		}
		
		public function get_date($intSigID){
			if (isset($this->sigs[$intSigID])){
				return $this->sigs[$intSigID]['date'];
			}
			return false;
		}
		
		public function get_html_date($intSigID){
			return $this->time->user_date($this->get_date($intSigID), true);
		}

	}//end class
}//end if
?>