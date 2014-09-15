<?php
/*
* Project:		EQdkp-Plus
* License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
* Link:			http://creativecommons.org/licenses/by-nc-sa/3.0/
* -----------------------------------------------------------------------
* Began:		2009
* Date:			$Date: 2013-03-23 18:01:39 +0100 (Sa, 23 Mrz 2013) $
* -----------------------------------------------------------------------
* @author		$Author: godmod $
* @copyright	2006-2011 EQdkp-Plus Developer Team
* @link			http://eqdkp-plus.com
* @package		eqdkpplus
* @version		$Rev: 13242 $
*
* $Id: Manage_Article_Categories.php 13242 2013-03-23 17:01:39Z godmod $
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
				'display'			=> true)
		);
	}
	
}
registry::register('Fonts');
?>