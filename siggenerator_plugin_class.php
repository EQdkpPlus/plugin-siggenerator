<?php
/*
 * Project:     EQdkp siggenerator
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:        http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2008
 * Date:        $Date: 2012-11-11 13:32:45 +0100 (So, 11. Nov 2012) $
 * -----------------------------------------------------------------------
 * @author      $Author: godmod $
 * @copyright   2008-2011 Aderyn
 * @link        http://eqdkp-plus.com
 * @package     siggenerator
 * @version     $Rev: 12426 $
 *
 * $Id: siggenerator_plugin_class.php 12426 2012-11-11 12:32:45Z godmod $
 */

if (!defined('EQDKP_INC'))
{
  header('HTTP/1.0 404 Not Found');
  exit;
}


/*+----------------------------------------------------------------------------
  | siggenerator
  +--------------------------------------------------------------------------*/
class siggenerator extends plugin_generic
{

  public $version    = '0.1.0';
  public $build      = '1';
  public $copyright  = 'GodMod';
  public $vstatus    = 'Beta';
  
  protected static $apiLevel = 20;

  /**
    * Constructor
    * Initialize all informations for installing/uninstalling plugin
    */
  public function __construct()
  {
    parent::__construct();

    $this->add_data(array (
      'name'              => 'SignatureGenerator',
      'code'              => 'siggenerator',
      'path'              => 'siggenerator',
      'template_path'     => 'plugins/siggenerator/templates/',
      'icon'              => 'fa-pencil',
      'version'           => $this->version,
      'author'            => $this->copyright,
      'description'       => $this->user->lang('siggenerator_short_desc'),
      'long_description'  => $this->user->lang('siggenerator_long_desc'),
      'homepage'          => EQDKP_PROJECT_URL,
      'manuallink'        => false,
      'plus_version'      => '2.0',
      'build'             => $this->build,
    ));

    $this->add_dependency(array(
      'plus_version'      => '2.0'
    ));

    // -- Register our permissions ------------------------
    // permissions: 'a'=admins, 'u'=user
    // ('a'/'u', Permission-Name, Enable? 'Y'/'N', Language string, array of user-group-ids that should have this permission)
    // Groups: 2 = Super-Admin, 3 = Admin, 4 = Member
    $this->add_permission('a', 'manage', 'N', $this->user->lang('manage'), array(2,3));
	$this->add_permission('u', 'view',    'Y', $this->user->lang('view'),    array(1,2,3,4));

	// Routes
	$this->routing->addRoute('CharSignature', 'charsignatures', 'plugins/siggenerator/pageobjects');
	$this->routing->addRoute('Signatures', 'signatures', 'plugins/siggenerator/pageobjects');
	
	
	
    // -- Menu --------------------------------------------
    $this->add_menu('admin', $this->gen_admin_menu());
    $this->add_menu('main', $this->gen_main_menu());

    // -- PDH Modules -------------------------------------
    $this->add_pdh_read_module('siggenerator');
    $this->add_pdh_write_module('siggenerator');

  }

  /**
    * pre_install
    * Define Installation
    */
   public function pre_install()
  {
    // include SQL and default configuration data for installation
    include($this->root_path.'plugins/siggenerator/includes/sql.php');

    // define installation
    for ($i = 1; $i <= count($siggeneratorSQL['install']); $i++)
      $this->add_sql(SQL_INSTALL, $siggeneratorSQL['install'][$i]);
	  
  }

  
  /**
    * pre_uninstall
    * Define uninstallation
    */
  public function pre_uninstall()
  {
    // include SQL data for uninstallation
    include($this->root_path.'plugins/siggenerator/includes/sql.php');

    for ($i = 1; $i <= count($siggeneratorSQL['uninstall']); $i++)
      $this->add_sql(SQL_UNINSTALL, $siggeneratorSQL['uninstall'][$i]);
  }

  /**
    * post_uninstall
    * Define Post Uninstall
    */
  public function post_uninstall()
  {
    // clear cache
    $this->pdc->del('pdh_siggenerator_table');
  }

  
  /**
   * gen_admin_menu
   * Generate the Admin Menu
   */
  private function gen_main_menu()
  {
  	if ($this->user->is_signedin()){
  	
	  	$main_menu = array(
	  			1 => array (
	  					'link'  => $this->routing->build('Signatures', false, false, true, true),
	  					'text'  => $this->user->lang('sg_signatur_link'),
	  					'check' => 'u_siggenerator_view',
	  			),
	  	);
  	} else return array();
  
  	return $main_menu;
  }
  
  /**
    * gen_admin_menu
    * Generate the Admin Menu
    */
  private function gen_admin_menu()
  {
    $admin_menu = array (array(
        'name' => $this->user->lang('siggenerator'),
        'icon' => 'fa-pencil',
        1 => array (
          'link'  => 'plugins/siggenerator/admin/signatures.php'.$this->SID,
          'text'  => $this->user->lang('sg_manage_signatures'),
          'check' => 'a_siggenerator_manage',
          'icon'  => 'fa-pencil'
        ),
        2 => array (
          'link'  => 'plugins/siggenerator/admin/fonts.php'.$this->SID,
          'text'  => $this->user->lang('sg_manage_fonts'),
          'check' => 'a_siggenerator_manage',
          'icon'  => 'fa-font'
        ),
    	3 => array (
    		'link'  => 'plugins/siggenerator/admin/backgrounds.php'.$this->SID,
    		'text'  => $this->user->lang('sg_manage_backgrounds'),
    		'check' => 'a_siggenerator_manage',
    		'icon'  => 'fa-image'
    	)

    ));

    return $admin_menu;
  }

}

?>