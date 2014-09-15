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


class signatures_pageobject extends pageobject {
  /**
   * __dependencies
   * Get module dependencies
   */
  public static function __shortcuts()
  {
    $shortcuts = array('crypt' => 'encrypt');
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
    
   	//Check Permissions
    $this->user->check_auth('u_siggenerator_view');
    
    $handler = array();
    parent::__construct(false, $handler);

    $this->process();
  }

  
  public function display(){
	
  	$arrCharachters = $this->pdh->get('member', 'connection_id', array($this->user->data['user_id']));
  	
  	$arrDD = array();
  	foreach($arrCharachters as $intCharID){
  		$strMembername =  $this->pdh->get('member', 'name', array($intCharID));
  		$cleaned = $this->routing->clean($strMembername);
  		$arrDD[$cleaned.'-'.$intCharID.'|'.$this->crypt->encrypt($intCharID)] = $strMembername;
  	}
  	
  	$myFirstChar = $arrCharachters[0];
  	$strMembername =  $this->pdh->get('member', 'name', array($myFirstChar));
  	
  	$this->tpl->assign_vars(array(
  		'CHAR_DD'	=> new hdropdown('chars', array('options' => $arrDD, 'js' => 'onchange="change_char(this.value)"')),
  		'BBCODE'	=> '[img]'.$this->env->buildlink().$this->controller_path_plain.'CharSignature/ID/?sig=SIG&key=KEY[/img]',
  		'HTML'		=> '<img src="'.$this->env->buildlink().$this->controller_path_plain.'CharSignature/ID/?sig=SIG&key=KEY" />',
  		'DIRECT'	=> $this->env->buildlink().$this->controller_path_plain.'CharSignature/ID/?sig=SIG&key=KEY',
  	));
  	
  	$arrSignatures = $this->pdh->get('siggenerator', 'id_list');
  	foreach($arrSignatures as $intSigID){
  		
  		$this->tpl->assign_block_vars('signatur_row', array(
  			'NAME' => $this->pdh->get('siggenerator', 'name', array($intSigID)),
  			'ID'	=> $intSigID,
  			
  			'BBCODE' => '[img]'.$this->env->buildlink().$this->controller_path_plain.'CharSignature/'.$this->routing->clean($strMembername).'-'.$myFirstChar.'/?sig='.$intSigID.'&key='.$this->crypt->encrypt($myFirstChar).'[/img]',
  			'HTML'	 => htmlentities('<img src="'.$this->env->buildlink().$this->controller_path_plain.'CharSignature/'.$this->routing->clean($strMembername).'-'.$myFirstChar.'/?sig='.$intSigID.'&key='.$this->crypt->encrypt($myFirstChar).'" />'),
  			'DIRECT' => $this->env->buildlink().$this->controller_path_plain.'CharSignature/'.$this->routing->clean($strMembername).'-'.$myFirstChar.'/?sig='.$intSigID.'&key='.$this->crypt->encrypt($myFirstChar),
  		));
  		
  	}

	
    // -- EQDKP ---------------------------------------------------------------
    $this->core->set_vars(array (
      'page_title'    => $this->user->lang('sg_signatur_link'),
      'template_path' => $this->pm->get_data('siggenerator', 'template_path'),
      'template_file' => 'signatures.html',
      'display'       => true
    ));	
  }
  
}
?>