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