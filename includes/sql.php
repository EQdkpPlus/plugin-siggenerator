<?php
/*
 * Project:     EQdkp Shoutbox
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:        http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2008
 * Date:        $Date: 2011-08-09 10:00:07 +0200 (Di, 09. Aug 2011) $
 * -----------------------------------------------------------------------
 * @author      $Author: Aderyn $
 * @copyright   2008-2011 Aderyn
 * @link        http://eqdkp-plus.com
 * @package     shoutbox
 * @version     $Rev: 10949 $
 *
 * $Id: sql.php 10949 2011-08-09 08:00:07Z Aderyn $
 */

if (!defined('EQDKP_INC'))
{
  header('HTTP/1.0 404 Not Found');exit;
}

$siggeneratorSQL = array(

  'uninstall' => array(
    1     => 'DROP TABLE IF EXISTS `__siggenerator_signatures`',
  ),

  'install'   => array(
	1 => "CREATE TABLE `__siggenerator_signatures` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(255) NOT NULL DEFAULT '' COLLATE 'utf8_bin',
  	`background` TEXT NOT NULL COLLATE 'utf8_bin',
  	`font`  VARCHAR(255) NOT NULL DEFAULT '' COLLATE 'utf8_bin',	
  	`font_color`  VARCHAR(7) NOT NULL DEFAULT '' COLLATE 'utf8_bin',
  	`font_border_color`  VARCHAR(7) NOT NULL DEFAULT '' COLLATE 'utf8_bin',
  	`font_border_size` INT(3) UNSIGNED NOT NULL DEFAULT '1',
  	`picture_preset`  VARCHAR(50) NOT NULL DEFAULT '' COLLATE 'utf8_bin',		
	`objects` TEXT NOT NULL COLLATE 'utf8_bin',
  	`date` INT(10) UNSIGNED NOT NULL,
	PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
",
  ));

?>