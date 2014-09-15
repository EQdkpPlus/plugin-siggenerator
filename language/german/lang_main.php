<?php
/*
 * Project:     EQdkp guildrequest
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:        http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2008
 * Date:        $Date: 2011-09-02 10:09:49 +0200 (Fr, 02. Sep 2011) $
 * -----------------------------------------------------------------------
 * @author      $Author: Aderyn $
 * @copyright   2008-2011 Aderyn
 * @link        http://eqdkp-plus.com
 * @package     guildrequest
 * @version     $Rev: 11183 $
 *
 * $Id: lang_main.php 11183 2011-09-02 08:09:49Z Aderyn $
 */

if (!defined('EQDKP_INC'))
{
    header('HTTP/1.0 404 Not Found');exit;
}

$lang = array(
  'siggenerator'                    => 'Signaturgenerator',

  // Description
  'siggenerator_short_desc'         => 'Signaturgenerator',
  'siggenerator_long_desc'          => 'Erstelle Signaturen mit Punkteständen und Charakter-Daten',
  
  'sg_plugin_not_installed'	=> 'Das Signaturgenerator-Plugin ist nicht installiert.',
		
  'sg_manage_signatures'	=> 'Signaturen verwalten',
  'sg_manage_fonts'			=> 'Schriften verwalten',
  'sg_manage_backgrounds'	=> 'Hintergründe verwalten',
  'sg_add_signature'		=> 'Signatur hinzufügen',
		
	'sg_background'			=> 'Hintergrund',
	'sg_font'				=> 'Schriftart',
	'sg_font_color'			=> 'Schriftfarbe',
	'sg_font_border_color'	=> 'Farbe Schriftkontur',
	'sg_font_border_size'	=> 'Breite Schriftkontur',
	'sg_position'			=> 'Position',
	'sg_preset'				=> 'Wert',
	'sg_add_field'			=> 'Feld hinzufügen',
	'sg_values'				=> 'Felder',
	'sg_delete_field'		=> 'Feld löschen',
	'sg_live_preview'		=> 'Live-Vorschau',
	'sg_show_label'			=> 'Label anzeigen',
	'sg_picture_preset'		=> 'Charakterbild Modul',
	'sg_signatur_link'		=> 'Charaktersignaturen',
	'sg_select_char'		=> 'Charakter auswählen',
	'sg_bbcode'				=> 'BB-Code für Foren',
	'sg_htmlcode'			=> 'HTML-Code für Webseiten',
	'sg_direktlink'			=> 'Direktlink',
	'sg_add_font'			=> 'Schriftart hinzufügen',
	'sg_folder'				=> 'Ordner',
	'sg_add_background'		=> 'Hintergrund hinzufügen',
	'sg_font_help'			=> 'Du hast hier die Möglichkeit, eigene Schriftarten hochzuladen. Diese müssten True Type Fonts im .ttf-Format sein. Viele kostenlose Schriftarten kannst du bei Google Fonts herunterladen.',
	'sg_background_help'	=> 'Hier hast du die Möglichkeit, eigene Hintergründe hochzuladen. Am besten eignen sich Bilder mit einer Auflösung von 500 Pixel in der Breite und 100 Pixel in der Höhe. Andere Auflösungen sind möglich, können aber zu Problemen bei der Darstellung führen.',
 );

?>