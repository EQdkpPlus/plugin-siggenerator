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

class Fonts extends page_generic {

	private $signature;
	
	public function __construct(){
		// plugin installed?
		if (!$this->pm->check('siggenerator', PLUGIN_INSTALLED))
			message_die($this->user->lang('sg_plugin_not_installed'));
		
		$this->user->check_auth('a_siggenerator_manage');
		
		$handler = array(
			'upload'	=> array('process' => 'upload', 'csrf' => true),
			'addbg' 	=> array('process' => 'add'),
		);
		parent::__construct(false, $handler);
		
		include_once $this->root_path.'plugins/siggenerator/includes/signature.class.php';
		$this->signature = register('signature');
		
		$this->process();
	}

	public function delete(){
		$arrFiles = $this->in->getArray('selected_ids', 'string');
		if (count($arrFiles)){
			$strFolder = $this->pfh->FolderPath('backgrounds', 'signatures');
			foreach($arrFiles as $filename){
				$this->pfh->Delete($strFolder.$filename);
			}
		}
	}
	
	public function upload(){
		$arrFields = array(
				'background' => array(
					'type'			=> 'file',
					'lang'			=> 'sg_font',
					'extensions'	=> array('jpg', 'png', 'gif'),
					'folder'		=> $this->pfh->FolderPath('backgrounds', 'signatures'),
					'numerate'		=> true,
			),
		);
		
		$objForm = register('form', array('addbg'));
		$objForm->langPrefix = 'mc_';
		$objForm->validate = true;
		$objForm->add_fields($arrFields);
		
		$this->tpl->assign_vars(array(
				'S_UPLOAD' => true,
		));
		
		$arrValues = $objForm->return_values();
		
		if ($arrValues['background'] != ""){
			$this->tpl->add_js('$.FrameDialog.closeDialog();', 'docready');
		} else {
			$this->add();
		}
	}
	
	public function add(){
		$arrFields = array(
			'background' => array(
					'type'			=> 'file',
					'lang'			=> 'sg_font',
					'extensions'	=> array('jpg', 'png', 'gif'),
					'folder'		=> $this->pfh->FolderPath('backgrounds', 'signatures'),
					'numerate'		=> true,
			),
		);
		
		$objForm = register('form', array('addbg'));
		$objForm->reset_fields();
		$objForm->langPrefix = 'mc_';
		$objForm->validate = true;
		$objForm->add_fields($arrFields);
		
		$this->tpl->assign_vars(array(
			'S_UPLOAD' => true,
		));
		
		$objForm->output();
		
		$this->core->set_vars(array(
				'page_title'		=> $this->user->lang('sg_manage_backgrounds'),
				'template_path'		=> $this->pm->get_data('siggenerator', 'template_path'),
				'template_file'		=> 'admin/backgrounds.html',
				'page_path'			=> [
						['title'=>$this->user->lang('menu_admin_panel'), 'url'=>$this->root_path.'admin/'.$this->SID],
						['title'=>$this->user->lang('siggenerator').': '.$this->user->lang('sg_manage_backgrounds'), 'url'=>' '],
				],
				'display'			=> true)
		);
	}
	
	public function display(){
		$arrBackgrounds = $this->signature->scan_backgrounds();
		
		foreach($arrBackgrounds as $folder => $bgname){
			$this->tpl->assign_block_vars('bg_row', array(
				'FOLDER'	=> $folder,
				'FONTNAME'	=> $bgname,
				'S_DATA_DIR'=> (strpos($folder, 'data/') === 0) ? true : false,
			));	
		}
		
		$this->jquery->Dialog('addbg', $this->user->lang('sg_add_background'), array('url'=> 'backgrounds.php'.$this->SID.'&simple_head=1&addbg=1', 'width'=>'640', 'height'=>'520', 'onclose' => $this->root_path.'plugins/siggenerator/admin/backgrounds.php'.$this->SID));

		$this->core->set_vars(array(
				'page_title'		=> $this->user->lang('sg_manage_backgrounds'),
				'template_path'		=> $this->pm->get_data('siggenerator', 'template_path'),
				'template_file'		=> 'admin/backgrounds.html',
				'page_path'			=> [
						['title'=>$this->user->lang('menu_admin_panel'), 'url'=>$this->root_path.'admin/'.$this->SID],
						['title'=>$this->user->lang('siggenerator').': '.$this->user->lang('sg_manage_backgrounds'), 'url'=>' '],
				],
				'display'			=> true)
		);
	}
	
}
registry::register('Fonts');
?>