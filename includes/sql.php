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

if (!defined('EQDKP_INC')){
	header('HTTP/1.0 404 Not Found');exit;
}

$siggeneratorSQL = array(

	'uninstall' => array(
		1	=> 'DROP TABLE IF EXISTS `__siggenerator_signatures`',
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
	) DEFAULT CHARSET=utf8 COLLATE=utf8_bin;",
	));

?>