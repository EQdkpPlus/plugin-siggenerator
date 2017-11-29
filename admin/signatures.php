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

define('EQDKP_INC', true);
define('IN_ADMIN', true);
$eqdkp_root_path = './../../../';
include_once($eqdkp_root_path . 'common.php');

class Signatures extends page_generic {

	private $signature;
	
	public function __construct(){
		// plugin installed?
		if (!$this->pm->check('siggenerator', PLUGIN_INSTALLED))
			message_die($this->user->lang('mc_plugin_not_installed'));
		
		$this->user->check_auth('a_siggenerator_manage');
		
		$handler = array(
			'save' 				=> array('process' => 'save', 'csrf' => true),
			'sig'				=> array('process' => 'edit'),
			'livepreview'		=> array('process' => 'livepreview'),
		);
		parent::__construct(false, $handler, array('siggenerator', 'name'), null, 'selected_ids[]', 'sig');

		include_once $this->root_path.'plugins/siggenerator/includes/signature.class.php';
		$this->signature = register('signature');

		$this->process();
	}
	
	public function livepreview(){
		
		$arrCharIDs = $this->pdh->get('member', 'id_list', array());
		if (isset($arrCharIDs[0])) $intCharID = $arrCharIDs[0];
		
		$arrSigData = array(
			'background' 		=> str_replace($this->root_path, '', $this->in->get('background')),
			'font'		 		=> str_replace($this->root_path, '', $this->in->get('font')),
			'font_border_color'	=> $this->in->get('font_border_color'),
			'font_border_size'	=> $this->in->get('font_border_size'),
			'font_color'		=> $this->in->get('font_color'),
			'picture_preset'	=> $this->in->get('picture_preset'),
		);
		
		$arrPos = $this->in->getArray('pos', 'string');
		$arrPreset = $this->in->getArray('preset', 'string');
		$arrLabels = $this->in->getArray('label', 'int');
		$arrOrder = $this->in->getArray('order', 'int');
		
		
		foreach($arrPos as $key => $val){
			$id = $arrOrder[$key];
			$label = ($arrLabels[$id]) ? 1 : 0;
			
			$arrSigData[$val][] = array($arrPreset[$key], $label);
		}
		
		$this->signature->createSignature($intCharID, $arrSigData, 'livepreview.jpg');
		
		echo $this->pfh->FolderPath('sigs', 'signatures', 'relative').'livepreview.jpg?_='.$this->time->time;
		exit();
	}
	
	
	public function save(){

		$arrSigData = array(
				'background' 		=> str_replace($this->root_path, '', $this->in->get('background')),
				'font'		 		=> str_replace($this->root_path, '', $this->in->get('font')),
				'font_border_color'	=> $this->in->get('font_border_color'),
				'font_border_size'	=> $this->in->get('font_border_size'),
				'font_color'		=> $this->in->get('font_color'),
				'picture_preset'	=> $this->in->get('picture_preset'),
				'name'				=> $this->in->get('name'),
		);
		
		$arrPos = $this->in->getArray('pos', 'string');
		$arrPreset = $this->in->getArray('preset', 'string');
		$arrLabels = $this->in->getArray('label', 'int');
		$arrOrder = $this->in->getArray('order', 'int');
		
		foreach($arrPos as $key => $val){
			$id = $arrOrder[$key];
			$label = ($arrLabels[$id]) ? 1 : 0;
				
			$arrSigData[$val][] = array($arrPreset[$key], $label);
		}
		
		if($this->url_id){
			$intResult = $this->pdh->put('siggenerator', 'update', array($this->url_id, $arrSigData));	
		} else {
			$intResult = $this->pdh->put('siggenerator', 'insert', array($arrSigData));
		}
		$this->core->message($this->user->lang('pk_succ_saved'), $this->user->lang('success'), 'green');
		$this->pdh->process_hook_queue();
		
		//Delte all existings signatures
		$this->pfh->Delete($this->pfh->FolderPath('sigs', 'signatures'));
		
		$this->display();
	}
	
	public function delete(){
		$retu = array();
		
		if(count($this->in->getArray('selected_ids', 'int')) > 0) {
			foreach($this->in->getArray('selected_ids','int') as $id) {
	
				$pos[] = stripslashes($this->pdh->get('siggenerator', 'name', array($id)));
				$retu[$id] = $this->pdh->put('siggenerator', 'delete', array($id));
			}
		}
	
		if(!empty($pos)) {
			$messages[] = array('title' => $this->user->lang('del_suc'), 'text' => implode(', ', $pos), 'color' => 'green');
			$this->core->messages($messages);
		}
	
		$this->pdh->process_hook_queue();
	}
	
	public function edit(){
		$intSignatureID = $this->in->get('sig', 0);
		
		register('game');
		$potential_presets = $this->pdh->get_preset_list('%member_id%', array('%member_id%', '%with_twink%', '%dkp_id%'), array());
		$potential_keys = array_keys($potential_presets);
		$dkp_list = $this->pdh->get('multidkp', 'id_list');
		
		$pps = array('-' => '');
		
		foreach($potential_keys as $id => $pset){
			if (strpos($pset, '_all') || strpos($pset, 'all_') !== false) continue;
			
			$isDKP = false;
			foreach($potential_presets[$pset][2] as $bla){
				if ($bla === '%dkp_id%'){
					$isDKP = true;
					break;
				}
			}

			if ($isDKP){
				foreach($dkp_list as $intDKPID){
					$pps[$pset.'_mdkp'.$intDKPID] = (($this->pdh->get_preset_description($pset)) ? $this->pdh->get_preset_description($pset) : $pset).' ['.$this->pdh->get('multidkp', 'name', array($intDKPID)).']';
				}
			} else {
				$pps[$pset] = ($this->pdh->get_preset_description($pset)) ? $this->pdh->get_preset_description($pset) : $pset;
			}
		}
		
		//Picture Presets
		$arrPicturePresets  = array();
		foreach($potential_keys as $id => $pset){
			if (strpos($pset, 'charicon') !== false || strpos($pset, 'picture') !== false ){
				$arrPicturePresets[$pset] =  ($this->pdh->get_preset_description($pset)) ? $this->pdh->get_preset_description($pset) : $pset;
			}
		}
		
		natcasesort($pps);
		
		$arrPosOptions = array('title' => 'Titel', 'subtitle' => 'Untertitel','left' => 'Links', 'right' => 'Rechts');
		
		$arrFonts = $this->signature->scan_fonts();
		$arrBackgrounds = $this->signature->scan_backgrounds();
		
		
		if ($this->url_id){
			$arrSigData = $this->pdh->get('siggenerator', 'data', array($this->url_id));
			
			$key = 1;
			if (isset($arrSigData['objects']['title'])){
				foreach($arrSigData['objects']['title'] as $arrData){
					$this->tpl->assign_block_vars('field_row', array(
						'KEY' => $key,
						'POS_DD'			=> (new hdropdown('pos[]', array('options' => $arrPosOptions, 'js' => 'onchange="handle_pos(this)"', 'value' => 'title')))->output(),
						'PRESET_DD'			=> (new hdropdown('preset[]', array('options' => $pps, 'js' => 'onchange="handle_preset(this)"', 'value' => $arrData[0])))->output(),
						'LABEL'				=> ($arrData[1]) ? " checked='checked'" : "",
					));
					$key++;
				}	
			}
			
			if (isset($arrSigData['objects']['subtitle'])){
				foreach($arrSigData['objects']['subtitle'] as $arrData){
					$this->tpl->assign_block_vars('field_row', array(
							'KEY' => $key,
							'POS_DD'			=> (new hdropdown('pos[]', array('options' => $arrPosOptions, 'js' => 'onchange="handle_pos(this)"', 'value' => 'subtitle')))->output(),
							'PRESET_DD'			=> (new hdropdown('preset[]', array('options' => $pps, 'js' => 'onchange="handle_preset(this)"', 'value' => $arrData[0])))->output(),
							'LABEL'				=> ($arrData[1]) ? " checked='checked'" : "",
					));
					$key++;
				}
			}
			
			if (isset($arrSigData['objects']['right'])){
				foreach($arrSigData['objects']['right'] as $arrData){
					$this->tpl->assign_block_vars('field_row', array(
							'KEY' => $key,
							'POS_DD'			=> (new hdropdown('pos[]', array('options' => $arrPosOptions, 'js' => 'onchange="handle_pos(this)"', 'value' => 'right')))->output(),
							'PRESET_DD'			=> (new hdropdown('preset[]', array('options' => $pps, 'js' => 'onchange="handle_preset(this)"', 'value' => $arrData[0])))->output(),
							'LABEL'				=> ($arrData[1]) ? " checked='checked'" : "",
					));
					$key++;
				}
			}
			
			if (isset($arrSigData['objects']['left'])){
				foreach($arrSigData['objects']['left'] as $arrData){
					$this->tpl->assign_block_vars('field_row', array(
							'KEY' => $key,
							'POS_DD'			=> (new hdropdown('pos[]', array('options' => $arrPosOptions, 'js' => 'onchange="handle_pos(this)"', 'value' => 'left')))->output(),
							'PRESET_DD'			=> (new hdropdown('preset[]', array('options' => $pps, 'js' => 'onchange="handle_preset(this)"', 'value' => $arrData[0])))->output(),
							'LABEL'				=> ($arrData[1]) ? " checked='checked'" : "",
					));
					$key++;
				}
			}
			
			
			
			$this->tpl->assign_vars(array(
					'POS_DD'			=> (new hdropdown('pos[]', array('options' => $arrPosOptions, 'js' => 'onchange="handle_pos(this)"')))->output(),
					'PRESET_DD'			=> (new hdropdown('preset[]', array('options' => $pps, 'js' => 'onchange="handle_preset(this)"')))->output(),
					
					'NAME'				=> $arrSigData['name'],
					'SIG'				=> $this->url_id,
					'FONT_COLOR'		=> (new hcolorpicker('font_color', array('value' => $arrSigData['font_color'])))->output(),
					'FONT_BORDER_COLOR' => (new hcolorpicker('font_border_color', array('value' => $arrSigData['font_border_color'])))->output(),
					'FONT_BORDER_SIZE'	=> (new hspinner('font_border_size', array('value' => $arrSigData['font_border_size'], 'min' => 0, 'max' => 10)))->output(),
					'KEY'				=> $key,
					'BACKGROUND_DD'		=> (new hdropdown('background', array('options' => $arrBackgrounds, 'value' => $arrSigData['background'])))->output(),
					'FONT_DD'			=> (new hdropdown('font', array('options' => $arrFonts, 'value' => $arrSigData['font'])))->output(),
					'PICTURE_PRESET_DD' => (new hdropdown('picture_preset', array('options' => $arrPicturePresets, 'value' => $arrSigData['picture_preset'])))->output(),
			));
			
			
		} else {
		
			$this->tpl->assign_vars(array(
				'POS_DD'			=> (new hdropdown('pos[]', array('options' => $arrPosOptions, 'js' => 'onchange="handle_pos(this)"')))->output(),
				'PRESET_DD'			=> (new hdropdown('preset[]', array('options' => $pps, 'js' => 'onchange="handle_preset(this)"')))->output(),
				'FONT_COLOR'		=> (new hcolorpicker('font_color', array('value' => 'fff')))->output(),
				'FONT_BORDER_COLOR' => (new hcolorpicker('font_border_color', array('value' => '000')))->output(),
				'FONT_BORDER_SIZE'	=> (new hspinner('font_border_size', array('value' => 1, 'min' => 0, 'max' => 10)))->output(),
				'KEY'				=> 1,
				'BACKGROUND_DD'		=> (new hdropdown('background', array('options' => $arrBackgrounds)))->output(),
				'FONT_DD'			=> (new hdropdown('font', array('options' => $arrFonts, 'value' => 'plugins/siggenerator/includes/fonts/OpenSans-Regular.ttf')))->output(),
				'PICTURE_PRESET_DD' => (new hdropdown('picture_preset', array('options' => $arrPicturePresets)))->output(),
			));
		}
		
		$this->tpl->add_js("
			$(\"#gr_form_table tbody\").sortable({
				cancel: '.not-sortable, input, tr th.footer, th, select',
				cursor: 'pointer',
			});
		", "docready");

		$strName = ($this->url_id) ? $arrSigData['name'] : $this->user->lang('sg_add_signature');
		
		$this->core->set_vars(array(
				'page_title'		=> $this->user->lang('sg_manage_signatures').': '.$this->pdh->get('siggenerator', 'name', array($intSignatureID)),
				'template_path'		=> $this->pm->get_data('siggenerator', 'template_path'),
				'template_file'		=> 'admin/signatures_edit.html',
				'page_path'			=> [
						['title'=>$this->user->lang('menu_admin_panel'), 'url'=>$this->root_path.'admin/'.$this->SID],
						['title'=>$this->user->lang('siggenerator').': '.$this->user->lang('sg_manage_signatures'), 'url'=> $this->root_path.'plugins/siggenerator/admin/signatures.php'.$this->SID],
						['title'=>$strName, 'url'=>' '],
				],
				'display'			=> true)
		);
	}


	
	public function display(){
		
		$hptt_page_settings = array(
				'name'				=> 'hptt_mc_admin_signatures',
				'table_main_sub'	=> '%intSigID%',
				'table_subs'		=> array('%intSigID%'),
				'page_ref'			=> 'signatures.php',
				'show_numbers'		=> false,
				'show_select_boxes'	=> true,
				'selectboxes_checkall'=>true,
				'show_detail_twink'	=> false,
				'table_sort_dir'	=> 'asc',
				'table_sort_col'	=> 1,
				'table_presets'		=> array(
						array('name' => 'siggen_editicon',	'sort' => false, 'th_add' => 'width="20"', 'td_add' => ''),
						array('name' => 'siggen_name',		'sort' => true, 'th_add' => '', 'td_add' => ''),
						array('name' => 'siggen_preview',	'sort' => true, 'th_add' => '', 'td_add' => ''),
						array('name' => 'siggen_date',		'sort' => true, 'th_add' => 'width="20"', 'td_add' => 'nowrap="nowrap"'),
				),
		);
		
		$page_suffix = '&amp;start='.$this->in->get('start', 0);
		$sort_suffix = '?sort='.$this->in->get('sort');
		
		$view_list = $this->pdh->get('siggenerator', 'id_list');
		
		$hptt = $this->get_hptt($hptt_page_settings, $view_list, $view_list, array('%link_url%' => 'signatures.php', '%link_url_suffix%' => '&amp;upd=true'));
		
		$this->tpl->assign_vars(array(
			'HPTT_COLUMN_COUNT'		=> $hptt->get_column_count(),
			'SIGNATURES_LIST'		=> $hptt->get_html_table($this->in->get('sort'), $page_suffix,null,1,null,false),
			'ACTION'				=> 'signatures.php'.$this->SID,	
		));
		
		$this->core->set_vars(array(
				'page_title'		=> $this->user->lang('sg_manage_signatures'),
				'template_path'		=> $this->pm->get_data('siggenerator', 'template_path'),
				'template_file'		=> 'admin/signatures.html',
				'page_path'			=> [
						['title'=>$this->user->lang('menu_admin_panel'), 'url'=>$this->root_path.'admin/'.$this->SID],
						['title'=>$this->user->lang('siggenerator').': '.$this->user->lang('sg_manage_signatures'), 'url'=>' '],
				],
				'display'			=> true)
		);
	}
	
}
registry::register('Signatures');
?>