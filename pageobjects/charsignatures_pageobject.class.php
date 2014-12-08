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


class charsignatures_pageobject extends pageobject {
  /**
   * __dependencies
   * Get module dependencies
   */
  public static function __shortcuts()
  {
    $shortcuts = array();
   	return array_merge(parent::__shortcuts(), $shortcuts);
  }


  /**
   * Constructor
   */
  public function __construct()
  {
    // plugin installed?
    if (!$this->pm->check('siggenerator', PLUGIN_INSTALLED))
      message_die($this->user->lang('sg_plugin_not_installed'));
    
    $handler = array();
    parent::__construct(false, $handler);

    $this->process();
  }
  
  public function display(){
	$intCharacterID = $this->url_id;
	
	$crypt = register('encrypt');
	$strDecrypted = $crypt->decrypt(str_replace(' ', '+', $this->in->get('key')));
	
	if ($strDecrypted != $intCharacterID){
		include_once $this->root_path.'plugins/siggenerator/includes/signature.class.php';
		$this->signature = register('signature');
		$this->signature->errorimage("#403 - Access denied");
	}

	$intSigID = $this->in->get('sig', 0);
	if ($intSigID == 0) {
		include_once $this->root_path.'plugins/siggenerator/includes/signature.class.php';
		$this->signature = register('signature');
		$this->signature->errorimage("#404 - Signature not Found");
	}

	//Check Cached Signature
	$intCacheHours = 4;
	$cache_time_browser = $intCacheHours * 60 * 60;
	
	$blnRenew = false;
	$strSignatureImage = md5($intCharacterID.'_'.$intSigID.'_'.$this->user->data['user_lang']).'.jpg';
	$strSignaturFolder = $this->pfh->FolderPath('sigs', 'signatures');
	if (is_file($strSignaturFolder.$strSignatureImage)){
		//Check Filedate
		$mytime = date();
		$filetime = filemtime($strSignaturFolder.$strSignatureImage);
		if ($filetime+($intCacheHours* 60 * 60) < $mytime){
			$blnRenew = true;
		}
		
		if(!$blnRenew){
			header("cache-control: max-age=".$cache_time_browser);
			header("Content-disposition: inline; filename=signature.jpg");
			header("content-type: image/jpg");
				
			echo file_get_contents($strSignaturFolder.$strSignatureImage);
			exit();
		}
	} else {
		$blnRenew = true;	
	}

	include_once $this->root_path.'plugins/siggenerator/includes/signature.class.php';
	$this->signature = register('signature');

	if ($blnRenew){
		//Create Signature
		$arrSigData = $this->pdh->get('siggenerator', 'data', array($intSigID));

		if (!$arrSigData){
			$this->signature->errorimage("#404 - Signature not Found");
		}
					
		$arrSigData['title'] = $arrSigData['objects']['title'];
		$arrSigData['subtitle'] = $arrSigData['objects']['subtitle'];
		$arrSigData['right'] = $arrSigData['objects']['right'];
		$arrSigData['left'] = $arrSigData['objects']['left'];
		
		$blnResult = $this->signature->createSignature($intCharacterID, $arrSigData, $strSignatureImage);

		if (is_file($strSignaturFolder.$strSignatureImage)){
			//Output File
			header("Content-disposition: inline; filename=signature.jpg");
			header("content-type: image/jpg");
			header("cache-control: max-age=".$cache_time_browser);
				
			echo file_get_contents($strSignaturFolder.$strSignatureImage);
			exit();
		}
	}

	$this->signature->errorimage("#404 - Signature not Found");
  }
}
?>