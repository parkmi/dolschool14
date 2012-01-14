<?php
/**
* install.joomla_lms.php
* (c) JoomaLMS eLearning Software http://www.joomlalms.com/
**/

// Don't allow access
//defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
if ( !defined( '_JEXEC' ) ) { die( 'Restricted access' ); }
if ( !defined( '_JLMS_EXEC' ) ) { define( '_JLMS_EXEC', 1 ); }

require_once(JPATH_SITE . DS . 'components' . DS . 'com_joomla_lms' . DS . 'includes' . DS . 'classes' . DS . 'lms.factory.php');	
require_once(JPATH_SITE . DS . 'components' . DS . 'com_joomla_lms' . DS . "includes".DS."libraries".DS."lms.lib.language.php");

jimport('joomla.filesystem.folder');
$folders = JFolder::folders(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_joomla_lms'.DS.'language');

$lang = & JFactory::getLanguage();
$locale = $lang->getLocale();

if( $folders && $locale ) 
{
	$intersect = array_intersect( $folders, $locale );
}

if( isset($intersect[0]) )
	$languageName = $intersect[0];   

global $JLMS_LANGUAGE;
JLMS_require_lang( $JLMS_LANGUAGE, 'admin.install.lang', $languageName, 'backend' );	 
JLMS_processLanguage( $JLMS_LANGUAGE, false, 'backend' );
	
function com_install()
{
	$absolutePath = JPATH_SITE;
	$liveSite = substr_replace(JURI::root(), '', -1, 1);
	$database =& JFactory::getDBO();

	$JLMS_cfg_fms_url = '';
	$JLMS_cfg_fms_users = 0;
	$JLMS_cfg_fms_enabled = 0;
	$JLMS_default_language = 'english';
	$JLMS_help_link = 'http://www.joomlalms.com/index.php?option=com_lms_help&Itemid=40&task=view_by_task&key={toolname}';

	$version = new JVersion();

	function jlms_install_plugins() { // installation of JoomlaLMS plugins (plugins were added in 1.0.5)		
		$absolutePath = JPATH_SITE;
		$liveSite = substr_replace(JURI::root(), '', -1, 1);
		$database =& JFactory::getDBO();		

		$plugins_dir = $absolutePath.'/components/com_joomla_lms/includes/plugins';
		$files = array();
		$handle = opendir($plugins_dir);
		while (false !== ($dir_name = readdir($handle))) {
			if (is_dir($plugins_dir.'/'.$dir_name) && $dir_name != "." && $dir_name != "..") {
				$dir = opendir($plugins_dir.'/'.$dir_name);
				while (false !== ($file = readdir($dir))) {
					if ($file != "." && $file != ".." && preg_match('/\.xml$/',$file)) {
						$tmp = array('dir' => $dir_name, 'file' => $file);
						$files[]=$tmp;
					}
				}
			}
		}
		closedir($handle);
		//require_once($absolutePath.'/components/com_joomla_lms/joomla_lms.class.php');
		// TODO: replace class to the simple database INSERT
		for ($i=0; $i<count($files); $i++) {
			// check if new
			$query = "SELECT COUNT(*) FROM #__lms_plugins WHERE element = '".str_replace('.xml','',$files[$i]['file'])."' AND folder = '".$files[$i]['dir']."'";
			$database->setQuery( $query );
			$total = $database->loadResult();
			$phpfile = $absolutePath.'/components/com_joomla_lms/includes/plugins/'.$files[$i]['dir'].'/'.str_replace('.xml','.php',$files[$i]['file']);
			$xmlfile = $absolutePath.'/components/com_joomla_lms/includes/plugins/'.$files[$i]['dir'].'/'.$files[$i]['file'];
	
			if ($total == 0 && file_exists($xmlfile)) {
				$row = new stdClass();
				$row->name = '';
				$row->short_description = '';
				$row->element = '';
				$row->folder = '';
								
				// xml file for module
				$xmlDoc = &JLMSFactory::getXMLParser();				
				if ($xmlDoc->loadFile( $xmlfile )) {
					$root = &$xmlDoc->document;
					if ($root->name() == 'jlmsplugin' ) {
						$element = &$root->getElementByPath( 'name' );
						$row->name = $element ? trim( $element->data() ) : '';
						$element = &$root->getElementByPath( 'short_description' );
						$row->short_description = $element ? trim( $element->data() ) : '';
					}
				}
				$row->element = str_replace('.xml','',$files[$i]['file']);
				$row->folder = $files[$i]['dir'];

				$query = "INSERT INTO #__lms_plugins (`name`, `element`, `folder`, `short_description`) VALUES(".$database->Quote($row->name).", ".$database->Quote($row->element).", ".$database->Quote($row->folder).", ".$database->Quote($row->short_description).")";
				$database->setQuery( $query );
				$database->query();
			}
		}
	}


	function jlms_install_mambot() {	
		$absolutePath = JPATH_SITE;
		$liveSite = substr_replace(JURI::root(), '', -1, 1);
		$database =& JFactory::getDBO();		
		$version = new JVersion();
		//Set up icons for admin area			
		
		$adminDir = dirname(__FILE__);
		if ( strnatcasecmp( $version->RELEASE, '1.6' ) >= 0 ) 
		{
			$database->SetQuery("SELECT count(*) FROM #__extensions WHERE element = 'jlms' AND folder = 'system'");
			$is_mambot = $database->LoadResult();
			if (!$is_mambot) 
			{
				$database->setQuery( "INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES ('', 'System - JoomlaLMS', 'plugin', 'jlms', 'system', 0, 1, 1, 0, '{\"legacy\":false,\"name\":\"System - JoomlaLMS\",\"type\":\"plugin\",\"creationDate\":\"June 2009\",\"author\":\"ElearningForce inc.\",\"copyright\":\"(C) 2006 - 2011 JoomlaLMS\",\"authorEmail\":\"lms@elearningforce.biz\",\"authorUrl\":\"www.joomlalms.com\",\"version\":\"1.2.0\",\"description\":\"JoomlaLMS and SMF forum bridge\",\"group\":\"\"}', '{}', '', '', 0, '0000-00-00 00:00:00', 0, 0)");
				$database->query();
			} 
		
			if (file_exists("$absolutePath/plugins/system/jlms/jlms.php")) 
			{
				@unlink("$absolutePath/plugins/system/jlms/jlms.php");
				@unlink("$absolutePath/plugins/system/jlms/jlms.xml");				
			} else {
				@mkdir("$absolutePath/plugins/system/jlms");
			}
			
			@rename( $adminDir. "/mambots/jlms.php", "$absolutePath/plugins/system/jlms/jlms.php");
			@rename( $adminDir. "/mambots/jlms.xml_", "$absolutePath/plugins/system/jlms/jlms.xml");
		} else {
			$database->SetQuery("SELECT count(*) FROM #__plugins WHERE element = 'jlms' AND folder = 'system'");
			$is_mambot = $database->LoadResult();
			if (!$is_mambot) {
				$database->setQuery( "INSERT INTO `#__plugins` (`name`, `element`, `folder`, `access`, `ordering`, `published`, `iscore`, `client_id`, `checked_out`, `checked_out_time`, `params`) VALUES ('System - JoomlaLMS', 'jlms', 'system', 0, 99, 1, 0, 0, 0, '0000-00-00 00:00:00', '')");
				$database->query();
			} else {
				$database->setQuery( "UPDATE `#__plugins` SET `name` = 'System - JoomlaLMS' WHERE `element` = 'jlms' AND `folder` = 'system'");
				$database->query();
			}
		
			if (file_exists("$absolutePath/plugins/system/jlms.php")) 
			{
				@unlink("$absolutePath/plugins/system/jlms.php");
				@unlink("$absolutePath/plugins/system/jlms.xml");
			}
			
			@rename( $adminDir. "/mambots/jlms.php", "$absolutePath/plugins/system/jlms.php");
			@rename( $adminDir. "/mambots/jlms.xml_", "$absolutePath/plugins/system/jlms.xml");
		}	
		
	}

	function jlms_checkCB() {		
		$absolutePath = JPATH_SITE;
		$liveSite = substr_replace(JURI::root(), '', -1, 1);
		$database =& JFactory::getDBO();		

		if (file_exists($absolutePath . "/components/com_comprofiler/comprofiler.php")) {
			$database->SetQuery("UPDATE #__lms_config SET lms_config_value = 1 WHERE lms_config_var = 'is_cb_installed'");
			$database->query();
		}
	}

	function jlms_copyfonts() {		
		$absolutePath = JPATH_SITE;
		
		$component_fonts = $absolutePath . "/components/com_joomla_lms/fonts";
		if (!file_exists($absolutePath . "/media/arial.ttf")) {
			// copy ARIAL.ttf font to media folder (this file required by certificates)
			@copy($component_fonts . "/arial.ttf", $absolutePath . "/media/arial.ttf");
		}
		if (!file_exists($absolutePath . "/media/DejaVuSansCondensed_Bold.ttf")) {
			@copy($component_fonts . "/DejaVuSansCondensed_Bold.ttf", $absolutePath . "/media/DejaVuSansCondensed_Bold.ttf");
		}
		if (!file_exists($absolutePath . "/media/DejaVuSansCondensed.ttf")) {
			@copy($component_fonts . "/DejaVuSansCondensed.ttf", $absolutePath . "/media/DejaVuSansCondensed.ttf");
		}
		if (!file_exists($absolutePath . "/media/Helvetica.afm")) {
			@copy($component_fonts . "/Helvetica.afm", $absolutePath . "/media/Helvetica.afm");
		}
		if (!file_exists($absolutePath . "/media/Helvetica-Bold.afm")) {
			@copy($component_fonts . "/Helvetica-Bold.afm", $absolutePath . "/media/Helvetica-Bold.afm");
		}
		if (!file_exists($absolutePath . "/media/Helvetica-BoldOblique.afm")) {
			@copy($component_fonts . "/Helvetica-BoldOblique.afm", $absolutePath . "/media/Helvetica-BoldOblique.afm");
		}
		if (!file_exists($absolutePath . "/media/Helvetica-Oblique.afm")) {
			@copy($component_fonts . "/Helvetica-Oblique.afm", $absolutePath . "/media/Helvetica-Oblique.afm");
		}
	}

	function jlms_newCfgSettings() { // insert new cgf settings, which are added in 1.0.3		
		$absolutePath = JPATH_SITE;
		$liveSite = substr_replace(JURI::root(), '', -1, 1);
		$database =& JFactory::getDBO();		

		$query = "DELETE FROM `#__lms_config` WHERE lms_config_var IN "
		. "\n ('show_paid_courses', 'show_fee_column', 'show_course_fee_property', 'show_course_spec_property', 'show_course_meta_property', 'show_course_access_property', 'conf_description', 'conf_toolbar_color', 'conf_title_font_color', 'conf_title_color', 'conf_main_color', 'conf_files_font_color', 'conf_border_color', 'conf_background')";
		$database->SetQuery($query);
		$database->query();

		$query = "INSERT INTO `#__lms_config` VALUES ('show_paid_courses', '1'),"
		. "\n ('show_fee_column', '1'), ('show_course_fee_property', '1'),"
		. "\n ('show_course_spec_property', '1'), ('show_course_meta_property', '1'), ('show_course_access_property', '1')";
		$database->SetQuery($query);
		$database->query();

		$query = "INSERT INTO `#__lms_config` VALUES ('conf_toolbar_color', '#B1CEF9'),"
		. "\n ('conf_title_font_color', '#4679C5'),"
		. "\n ('conf_title_color', '#BFC5CE'),"
		. "\n ('conf_main_color', '#E4E4E4'),"
		. "\n ('conf_files_font_color', '#7E8795'),"
		. "\n ('conf_border_color', '#9D9D9D'),"
		. "\n ('conf_background', '#EFEFEF')";
		$database->SetQuery($query);
		$database->query();

		$conf_descr = $database->Quote("<table border='0' width='444' style='height:100%'><tbody><tr><td colspan='2' style='font-weight: 700'>Requirements to use Conferencing<br /></td></tr><tr><td colspan='2' height='20'><strong>PC : </strong>Pentium III or equivalent, 128 mb RAM</td></tr><tr><td colspan='2' height='20'><strong>OS : </strong>Windows 9x/ME/NT/2000/XP/Vista, Linux, Mac OS X </td></tr><tr><td colspan='2' height='20'><strong>Internet Connection : </strong>ADSL or + internet connections </td></tr><tr><td><img class='JLMS_png' src='".$liveSite."/components/com_joomla_lms/lms_images/conference/conf_voicesupport.png' border='0' alt='Voice support' title='Voice support' width='64' height='64' /></td><td><img class='JLMS_png' src='".$liveSite."/components/com_joomla_lms/lms_images/conference/conf_webcam.png' border='0' alt='Webcam' title='Webcam' width='64' height='64' /></td></tr><tr><td width='200'><strong>Headphones and Voice support</strong></td><td><strong>Webcam (not necessary)</strong></td></tr></tbody></table>");
		$query = "INSERT INTO `#__lms_config` VALUES ('conf_description', ".$conf_descr.")";
		$database->SetQuery($query);
		$database->query();

		$query = "SELECT count(*) FROM `#__lms_config` WHERE lms_config_var = 'enabletax'";
		$database->SetQuery($query);
		$et = $database->loadResult();
		if (!$et) {
			$database->SetQuery("INSERT INTO `#__lms_config` VALUES ('enabletax', '0')");
			$database->query();
		}
	}

	function jlms_insert_usertypes() {		
		$absolutePath = JPATH_SITE;
		$liveSite = substr_replace(JURI::root(), '', -1, 1);
		$database =& JFactory::getDBO();		

		$query = "SELECT count(*) FROM `#__lms_usertypes`";
		$database->SetQuery($query);
		$total = $database->LoadResult();
		if (!$total) {
			$query = "INSERT INTO `#__lms_usertypes` (id, roletype_id, lms_usertype, default_role) VALUES (1, 2, 'Teacher', 1),"
				. "\n (2, 1, 'Student', 1),"
				. "\n (3, 0, 'lms_admin', 0),"
				. "\n (4, 5, 'Assistant', 0),"
				. "\n (5, 4, 'LMS administrator', 0),"
				. "\n (6, 3, 'Parent/CEO', 0)";
			$database->SetQuery($query);
			$database->query();
		}
	}

	function jlms_install_languages() {		
		$absolutePath = JPATH_SITE;
		$liveSite = substr_replace(JURI::root(), '', -1, 1);
		$database =& JFactory::getDBO();
		

		$all_langs = array();
		$all_langs[] = 'english';$all_langs[] = 'danish';$all_langs[] = 'german';$all_langs[] = 'spanish';
		$all_langs[] = 'french';$all_langs[] = 'norwegian';$all_langs[] = 'italian';$all_langs[] = 'brazilian';
		$all_langs[] = 'bulgarian';$all_langs[] = 'chinese';$all_langs[] = 'czech';$all_langs[] = 'japanese';
		$all_langs[] = 'russian';$all_langs[] = 'dutch';
		$query = "SELECT lang_name FROM #__lms_languages";
		$database->SetQuery($query);
		$ex_langs = $database->LoadResultArray();
		if (count($ex_langs)) {
			foreach ($all_langs as $one_lang) {
				if (in_array($one_lang, $ex_langs)) {
					// language is already exists
				} else {
					$pstate = 1;
					if ($one_lang == 'chinese' || $one_lang == 'japanese') {
						$pstate = 0;
					}
					$query = "INSERT INTO `#__lms_languages` (lang_name, published) VALUES ('".$one_lang."', $pstate)";
					$database->SetQuery($query);
					$database->query();
				}
			}
		} else {
			$database->SetQuery("TRUNCATE TABLE `#__lms_languages`");$database->query();
			$query = "INSERT INTO `#__lms_languages` VALUES (1, 'english', 1),"
			. "\n (2, 'danish', 1),"
			. "\n (3, 'german', 1),"
			. "\n (4, 'spanish', 1),"
			. "\n (5, 'french', 1),"
			. "\n (6, 'norwegian', 1),"
			. "\n (7, 'italian', 1),"
			. "\n (8, 'brazilian', 1),"
			. "\n (9, 'bulgarian', 0),"
			. "\n (10, 'chinese', 0),"
			. "\n (11, 'czech', 0),"
			. "\n (12, 'japanese', 0),"
			. "\n (13, 'russian', 0)";
			$database->setQuery($query);
			$database->query();
		}
	}

	function jlms_create_forums($using_cfg = false) {
				
		$absolutePath = JPATH_SITE;
		$liveSite = substr_replace(JURI::root(), '', -1, 1);
		$database =& JFactory::getDBO();
		

		$forum_enabled = 0;
		$course_forum_name = '{course_name}';
		$course_forum_desc = '';
		$course_forum_enabled = 1;
		$group_forum_name = '{course_name} - {group_name}';
		$group_forum_desc = 'Private group discussions';
		$group_forum_enabled = 1;
		$module_forum_name = '{course_name} - {lpath_name}';
		$module_forum_desc = 'Module discussions';
		$module_forum_enabled = 0;
		$private_forum_name = '{course_name} - Teachers board';
		$private_forum_desc = 'Private teachers discussions';
		$private_forum_enabled = 0;
		$private_module_forum_name = '{course_name} - {lpath_name} - Teachers board';
		$private_module_forum_desc = 'Private discussions';
		$private_module_forum_enabled = 0;

		if ($using_cfg) {

			$query = "SELECT lms_config_var as var_name, lms_config_value as value FROM #__lms_config";
			$database->SetQuery($query);
			$lms_config = $database->LoadObjectList();

			foreach ($lms_config as $cfg) {
				if ($cfg->var_name == 'plugin_lpath_forum') {
					$module_forum_enabled = $cfg->value ? 1 : 0;
				} elseif ($cfg->var_name == 'plugin_lpath_forum_name' && $cfg->value) {
					$module_forum_name = $cfg->value;
				} elseif ($cfg->var_name == 'plugin_lpath_forum_desc' && $cfg->value) {
					$module_forum_desc = $cfg->value;
				} elseif ($cfg->var_name == 'plugin_private_forum') {
					$private_forum_enabled = $cfg->value ? 1 : 0;
				} elseif ($cfg->var_name == 'plugin_private_forum_name' && $cfg->value) {
					$private_forum_name = $cfg->value;
				} elseif ($cfg->var_name == 'plugin_private_forum_desc' && $cfg->value) {
					$private_forum_desc = $cfg->value;
				} elseif ($cfg->var_name == 'plugin_private_lpath_forum') {
					$private_module_forum_enabled = $cfg->value ? 1 : 0;
				} elseif ($cfg->var_name == 'plugin_private_lpath_forum_name' && $cfg->value) {
					$private_module_forum_name = $cfg->value;
				} elseif ($cfg->var_name == 'plugin_private_lpath_forum_desc' && $cfg->value) {
					$private_module_forum_desc = $cfg->value;
				}
			}
		}

		$query = "INSERT INTO `#__lms_forums` VALUES (1, 0, 1, 0, 0, 1, 0, 0, '', '', ".$database->Quote($course_forum_name).", ".$database->Quote($course_forum_desc).");";
		$database->SetQuery($query);$database->query();
		$query = "INSERT INTO `#__lms_forums` VALUES (2, 0, $module_forum_enabled, 1, 0, 1, 0, 0, '', '', ".$database->Quote($module_forum_name).", ".$database->Quote($module_forum_desc).");";
		$database->SetQuery($query);$database->query();
		$query = "INSERT INTO `#__lms_forums` VALUES (3, 0, 0, 0, 2, 1, 0, 0, '', '', ".$database->Quote($group_forum_name).", ".$database->Quote($group_forum_desc).");";
		$database->SetQuery($query);$database->query();
		$query = "INSERT INTO `#__lms_forums` VALUES (4, 1, $private_forum_enabled, 0, 0, 1, 0, 1, '1,4,5', '', ".$database->Quote($private_forum_name).", ".$database->Quote($private_forum_desc).");";
		$database->SetQuery($query);$database->query();
		$query = "INSERT INTO `#__lms_forums` VALUES (5, 2, $private_module_forum_enabled, 1, 0, 1, 0, 1, '1,4,5', '', ".$database->Quote($private_module_forum_name).", ".$database->Quote($private_module_forum_desc).");";
		$database->SetQuery($query);$database->query();
		$query = "INSERT INTO `#__lms_forums` VALUES (6, 0, 0, 1, 0, 1, 0, 0, '', '', ".$database->Quote($group_forum_name).", ".$database->Quote($group_forum_name).");";
		$database->SetQuery($query);$database->query();		
	}

	function jlms_create_temp_folder() {
		
		$absolutePath = JPATH_SITE;
		$liveSite = substr_replace(JURI::root(), '', -1, 1);
		$database =& JFactory::getDBO();		

		$query = "SELECT lms_config_value FROM `#__lms_config` WHERE lms_config_var = 'temp_folder'";
		$database->SetQuery($query);
		$cur_tmp = $database->LoadResult();
		if (!$cur_tmp) {
			@mkdir( $absolutePath . "/jlms");
			$adminDir = dirname(__FILE__);
			if (!is_writable($absolutePath . "/jlms" )) {
				@chmod($absolutePath . "/jlms", 0777);
			}
			@copy( $adminDir. "/index.html", $absolutePath . "/jlms/index.html");
			$database->SetQuery("DELETE FROM `#__lms_config` WHERE lms_config_var = 'temp_folder'");
			$database->query();
			$database->SetQuery("INSERT INTO `#__lms_config` VALUES ('temp_folder', 'jlms')");
			$database->query();
			$cur_tmp = 'jlms';
		}
		return $cur_tmp;
	}

	function jlms_create_invoices_folder() {
		
		$absolutePath = JPATH_SITE;
		$liveSite = substr_replace(JURI::root(), '', -1, 1);
		$database =& JFactory::getDBO();		

		$query = "SELECT lms_config_value FROM `#__lms_config` WHERE lms_config_var = 'jlms_subscr_invoice_path'";
		$database->SetQuery($query);
		$cur_inv = $database->LoadResult();
		if (!$cur_inv) {
			$abs_path = str_replace('\\\\', '\\', $absolutePath);
			$abs_path = str_replace('\\', '/', $abs_path);
			$adminDir = dirname(__FILE__);
			$invoices_folder = '';
			if (is_writable($abs_path . "/jlms" )) {
				/* certificates folder */
				@mkdir( $abs_path . "/jlms/invoices");
				if (!is_writable($abs_path . "/jlms/invoices" )) {
					@chmod($abs_path . "/jlms/invoices", 0777);
				}
				if (is_writable($abs_path . "/jlms/invoices" )) {
					$invoices_folder = $abs_path . "/jlms/invoices";
					@copy( $adminDir. "/index.html", $abs_path . "/jlms/invoices/index.html");
				}
			}
			if (!$invoices_folder) {
				@mkdir( $abs_path . "/media/jlms_invoices");
				$invoices_folder = $abs_path . "/media/jlms_invoices";
				if (is_writable($abs_path . "/media/jlms_invoices" )) {
					@copy( $adminDir. "/index.html", $abs_path . "/media/jlms_invoices/index.html");
				}
			}
			$database->SetQuery("DELETE FROM `#__lms_config` WHERE lms_config_var = 'jlms_subscr_invoice_path'");
			$database->query();
			$database->SetQuery("INSERT INTO `#__lms_config` VALUES ('jlms_subscr_invoice_path', '".$invoices_folder."')");
			$database->query();
		}
	}

	function jlms_create_jlms_folders() {
		
		$absolutePath = JPATH_SITE;
		$liveSite = substr_replace(JURI::root(), '', -1, 1);
		$database =& JFactory::getDBO();		

		$abs_path = str_replace('\\\\', '\\', $absolutePath);
		$abs_path = str_replace('\\', '/', $abs_path);
		$adminDir = dirname(__FILE__);
		$doc_folder = '';
		$backup_folder = '';
		$crtf_folder = '';
		$scorm_folder = '';
		$invoices_folder = '';
		if (is_writable($absolutePath . "/jlms" )) {

			/* docs folder */
			@mkdir( $abs_path . "/jlms/docs");
			if (!is_writable($abs_path . "/jlms/docs" )) {
				@chmod($abs_path . "/jlms/docs", 0777);
			}
			if (is_writable($abs_path . "/jlms/docs" )) {
				$doc_folder = $abs_path . "/jlms/docs";
			}

			/* backups folder */
			@mkdir( $abs_path . "/jlms/backups");
			if (!is_writable($abs_path . "/jlms/backups" )) {
				@chmod($abs_path . "/jlms/backups", 0777);
			}
			if (is_writable($abs_path . "/jlms/backups" )) {
				$backup_folder = $abs_path . "/jlms/backups";
			}

			/* scorm folder */
			@mkdir( $abs_path . "/jlms/scorms");
			if (!is_writable($abs_path . "/jlms/scorms" )) {
				@chmod($abs_path . "/jlms/scorms", 0777);
			}
			if (is_writable($abs_path . "/jlms/scorms" )) {
				$scorm_folder =  "jlms/scorms";
			}
			
			/* certificates folder */
			@mkdir( $abs_path . "/jlms/certificates");
			if (!is_writable($abs_path . "/jlms/certificates" )) {
				@chmod($abs_path . "/jlms/certificates", 0777);
			}
			if (is_writable($abs_path . "/jlms/certificates" )) {
				$crtf_folder = $abs_path . "/jlms/certificates";
			}

			/* invoices folder */
			@mkdir( $abs_path . "/jlms/invoices");
			if (!is_writable($abs_path . "/jlms/invoices" )) {
				@chmod($abs_path . "/jlms/invoices", 0777);
			}
			if (is_writable($abs_path . "/jlms/invoices" )) {
				$invoices_folder = $abs_path . "/jlms/invoices";
			}

		}
		if (!$doc_folder) {
			@mkdir( $abs_path . "/media/jlms_docs");
			$doc_folder = $abs_path . "/media/jlms_docs";
		}
		if (!$backup_folder) {
			@mkdir( $abs_path . "/media/jlms_backups");
			$backup_folder = $abs_path . "/media/jlms_backups";
		}
		if (!$scorm_folder) {
			@mkdir( $abs_path . "/media/jlms_scorms");
			$scorm_folder = "media/jlms_scorms";
		}
		if (!$crtf_folder) {
			@mkdir( $abs_path . "/media/jlms_certificates");
			$crtf_folder = $abs_path . "/media/jlms_certificates";
		}
		if (!$invoices_folder) {
			@mkdir( $abs_path . "/media/jlms_invoices");
			$invoices_folder = $abs_path . "/media/jlms_invoices";
		}
		
		$query = "INSERT INTO `#__lms_config` VALUES ('scorm_folder', '".$scorm_folder."'),"
			. "\n ('jlms_doc_folder', '".$doc_folder."'),"
			. "\n ('jlms_crtf_folder', '".$crtf_folder."'),"
			. "\n ('jlms_subscr_invoice_path', '".$invoices_folder."'),"
			. "\n ('jlms_backup_folder', '".$backup_folder."')"
			;
		$database->SetQuery($query);
		$database->query();
	}

	function jlms_insert_quiz_quest_types() {
				
		$absolutePath = JPATH_SITE;
		$liveSite = substr_replace(JURI::root(), '', -1, 1);
		$database =& JFactory::getDBO();		

		$query = "INSERT INTO `#__lms_quiz_t_qtypes` VALUES (1, 'Multiple Choice'),"
			. " \n (2, 'Multiple Response'),"
			. " \n (3, 'True/False'),"
			. " \n (4, 'Matching Drag and Drop'),"
			. " \n (5, 'Matching Drop-Down'),"
			. " \n (6, 'Fill in the blank'),"
			. " \n (7, 'Hotspot'),"
			. " \n (8, 'Surveys'),"
			. " \n (9, 'Likert Scale'),"
			. " \n (10, 'Boilerplate'),"
			. " \n (11, 'Matching Drag and Drop Images'),"
			. " \n (12, 'Multiple Images Choice'),"
			. " \n (13, 'Multiple Images Response')";
		$database->SetQuery($query);
		$database->query();
	}

	function jlms_insert_menu_items() {
				
		$absolutePath = JPATH_SITE;
		$liveSite = substr_replace(JURI::root(), '', -1, 1);
		$database =& JFactory::getDBO();
		

		$query = "INSERT INTO `#__lms_menu` (`lang_var`, `image`, `task`, `ordering`, `published`, `is_separator`, `user_access`)"
		. "\n VALUES ('_JLMS_TOOLBAR_HOME', 'tlb_home.png', '', 0, 1, 0, 2),"
		. "\n ('_JLMS_TOOLBAR_HOME', 'tlb_home.png', '', 1, 1, 0, 1),"
		. "\n ('_JLMS_TOOLBAR_HOME', 'tlb_home.png', '', 1, 1, 0, 6),"
		. "\n ('_JLMS_TOOLBAR_COURSES', 'tlb_courses.png', 'courses', 1, 1, 0, 2),"
		. "\n ('_JLMS_TOOLBAR_COURSES', 'tlb_courses.png', 'courses', 2, 1, 0, 1),"
		. "\n ('_JLMS_TOOLBAR_COURSES', 'tlb_courses.png', 'courses', 2, 1, 0, 6),"
		. "\n ('', '', '', 2, 1, 1, 2),"
		. "\n ('', '', '', 3, 1, 1, 1),"
		. "\n ('', '', '', 4, 1, 1, 6)";
		$database->setQuery($query);
		$database->query();

		$query = "INSERT INTO `#__lms_menu` (`lang_var`, `image`, `task`, `ordering`, `published`, `is_separator`, `user_access`)"
		. "\n VALUES ('_JLMS_PATHWAY_COURSE_HOME', 'tlb_course_home.png', 'details_course', 3, 1, 0, 2),"
		. "\n ('_JLMS_PATHWAY_COURSE_HOME', 'tlb_course_home.png', 'details_course', 4, 1, 0, 1),"
		. "\n ('_JLMS_PATHWAY_COURSE_HOME', 'tlb_course_home.png', 'details_course', 5, 1, 0, 6),"
		. "\n ('_JLMS_TOOLBAR_AGENDA', 'tlb_agenda.png', 'agenda', 4, 1, 0, 2),"
		. "\n ('_JLMS_TOOLBAR_AGENDA', 'tlb_agenda.png', 'agenda', 5, 1, 0, 1),"
		. "\n ('_JLMS_TOOLBAR_AGENDA', 'tlb_agenda.png', 'agenda', 6, 1, 0, 6),"
		. "\n ('_JLMS_TOOLBAR_DOCS', 'tlb_docs.png', 'documents', 5, 1, 0, 2),"
		. "\n ('_JLMS_TOOLBAR_DOCS', 'tlb_docs.png', 'documents', 6, 1, 0, 1),"
		. "\n ('_JLMS_TOOLBAR_LPATH', 'tlb_lpath.png', 'learnpaths', 6, 1, 0, 2),"
		. "\n ('_JLMS_TOOLBAR_LPATH', 'tlb_lpath.png', 'learnpaths', 7, 1, 0, 1),"
		. "\n ('_JLMS_TOOLBAR_LINKS', 'tlb_links.png', 'links', 8, 1, 0, 2),"
		. "\n ('_JLMS_TOOLBAR_LINKS', 'tlb_links.png', 'links', 8, 1, 0, 1)";
		$database->setQuery($query);
		$database->query();

		$query = "INSERT INTO `#__lms_menu` (`lang_var`, `image`, `task`, `ordering`, `published`, `is_separator`, `user_access`)"
		. "\n VALUES ('_JLMS_TOOLBAR_QUIZZES', 'tlb_quiz.png', 'quizzes', 9, 1, 0, 2),"
		. "\n ('_JLMS_TOOLBAR_QUIZZES', 'tlb_quiz.png', 'quizzes', 9, 1, 0, 1),"
		. "\n ('_JLMS_TOOLBAR_DROP', 'tlb_dropbox.png', 'dropbox', 10, 1, 0, 2),"
		. "\n ('_JLMS_TOOLBAR_DROP', 'tlb_dropbox.png', 'dropbox', 10, 1, 0, 1),"
		. "\n ('_JLMS_TOOLBAR_HOMEWORK', 'tlb_homework.png', 'homework', 11, 1, 0, 2),"
		. "\n ('_JLMS_TOOLBAR_HOMEWORK', 'tlb_homework.png', 'homework', 11, 1, 0, 1),"
		. "\n ('_JLMS_TOOLBAR_HOMEWORK', 'tlb_homework.png', 'homework', 7, 1, 0, 6),"
		. "\n ('_JLMS_TOOLBAR_ATTEND', 'tlb_attendance.png', 'attendance', 12, 1, 0, 2),"
		. "\n ('_JLMS_TOOLBAR_ATTEND', 'tlb_attendance.png', 'attendance', 12, 1, 0, 1),"
		. "\n ('_JLMS_TOOLBAR_ATTEND', 'tlb_attendance.png', 'attendance', 8, 1, 0, 6),"
		. "\n ('_JLMS_TOOLBAR_FORUM', 'tlb_forum.png', 'course_forum', 14, 1, 0, 2),"
		. "\n ('_JLMS_TOOLBAR_FORUM', 'tlb_forum.png', 'course_forum', 14, 1, 0, 1),"
		. "\n ('_JLMS_TOOLBAR_CHAT', 'tlb_chat.png', 'chat', 15, 1, 0, 2),"
		. "\n ('_JLMS_TOOLBAR_CHAT', 'tlb_chat.png', 'chat', 15, 1, 0, 1)";
		$database->setQuery($query);
		$database->query();

		$query = "INSERT INTO `#__lms_menu` (`lang_var`, `image`, `task`, `ordering`, `published`, `is_separator`, `user_access`)"
		. "\n VALUES ('', '', '', 20, 1, 1, 2),"
		. "\n ('_JLMS_TOOLBAR_TRACK', 'tlb_tracking.png', 'tracking', 19, 1, 0, 1),"
		. "\n ('_JLMS_TOOLBAR_MAILBOX', 'tlb_mailbox.png', 'mailbox', 19, 1, 0, 2),"
		. "\n ('_JLMS_TOOLBAR_MAILBOX', 'tlb_mailbox.png', 'mailbox', 20, 1, 0, 1),"
		. "\n ('_JLMS_TOOLBAR_USER_OPTIONS', 'tlb_switch.png', '', 21, 1, 0, 2),"
		. "\n ('_JLMS_TOOLBAR_USERS', 'tlb_users.png', 'course_users', 21, 1, 0, 1),"
		. "\n ('_JLMS_TOOLBAR_GRADEBOOK', 'tlb_gradebook.png', 'gradebook', 9, 1, 0, 6),"
		. "\n ('_JLMS_TOOLBAR_USER_OPTIONS', 'tlb_switch.png', '', 24, 1, 0, 1),"
		. "\n ('_JLMS_TOOLBAR_HELP', 'tlb_help.png', 'view_by_task', 22, 1, 0, 2),"
		. "\n ('_JLMS_TOOLBAR_HELP', 'tlb_help.png', '', 25, 1, 0, 1),"
		. "\n ('_JLMS_TOOLBAR_CONF', 'btn_cam.png', 'conference', 16, 1, 0, 2),"
		. "\n ('_JLMS_TOOLBAR_HELP', 'tlb_help.png', 'view_by_task', 12, 1, 0, 6),"
		. "\n ('_JLMS_TOOLBAR_GRADEBOOK', 'tlb_gradebook.png', 'gradebook', 18, 1, 0, 2)";
		$database->setQuery($query);
		$database->query();

		$query = "INSERT INTO `#__lms_menu` (`lang_var`, `image`, `task`, `ordering`, `published`, `is_separator`, `user_access`)"
		. "\n VALUES ('', '', '', 17, 1, 1, 2),"
		. "\n ('', '', '', 13, 1, 1, 2),"
		. "\n ('', '', '', 10, 1, 1, 6),"
		. "\n ('_JLMS_TOOLBAR_GRADEBOOK', 'tlb_gradebook.png', 'gradebook', 18, 1, 0, 1),"
		. "\n ('_JLMS_TOOLBAR_USER_OPTIONS', 'tlb_switch.png', '', 11, 1, 0, 6),"
		. "\n ('_JLMS_TOOLBAR_CONF', 'btn_cam.png', 'conference', 16, 1, 0, 1),"
		. "\n ('', '', '', 17, 1, 1, 1),"
		. "\n ('', '', '', 23, 1, 1, 1),"
		. "\n ('', '', '', 13, 1, 1, 1),"
		. "\n ('_JLMS_TOOLBAR_HOME', 'tlb_home.png', '', 0, 1, 0, 0),"
		. "\n ('_JLMS_TOOLBAR_COURSES', 'tlb_courses.png', 'courses', 1, 1, 0, 0),"
		. "\n ('_JLMS_TOOLBAR_CEO_PARENT', 'tlb_ceo.png', 'ceo_page', 2, 1, 0, 0)";
		$database->setQuery($query);
		$database->query();
		
		$query = "INSERT INTO `#__lms_menu` (`lang_var`, `image`, `task`, `ordering`, `published`, `is_separator`, `user_access`)"
		. "\n VALUES ('_JLMS_TOOLBAR_SUBSCRIPTIONS', 'tlb_subscriptions.png', 'subscription', 3, 1, 0, 1),"
		. "\n ('_JLMS_TOOLBAR_SUBSCRIPTIONS', 'tlb_subscriptions.png', 'subscription', 2, 1, 0, 2),"
		. "\n ('_JLMS_TOOLBAR_SUBSCRIPTIONS', 'tlb_subscriptions.png', 'subscription', 2, 1, 0, 0),"
		. "\n ('_JLMS_TOOLBAR_SUBSCRIPTIONS', 'tlb_subscriptions.png', 'subscription', 2, 1, 0, 6)";
		$database->setQuery($query);
		$database->query();

		$query = "INSERT INTO `#__lms_menu` (`lang_var`, `image`, `task`, `ordering`, `published`, `is_separator`, `user_access`)"
		. "\n VALUES ('_JLMS_TOOLBAR_HOME', 'tlb_home.png', '', 0, 1, 0, -1),"
		. "\n ('_JLMS_TOOLBAR_SUBSCRIPTIONS', 'tlb_subscriptions.png', 'subscription', 1, 1, 0, -1)";
		$database->setQuery($query);
		$database->query();
		
		$query = "INSERT INTO `#__lms_menu` (`lang_var`, `image`, `task`, `ordering`, `published`, `is_separator`, `user_access`)"
		. "\n VALUES ('_JLMS_TOOLBAR_LIBRARY', 'tlb_library.png', 'outdocs', 3, 1, 0, 1),"
		. "\n ('_JLMS_TOOLBAR_LIBRARY', 'tlb_library.png', 'outdocs', 2, 1, 0, 2),"
		. "\n ('_JLMS_TOOLBAR_LIBRARY', 'tlb_library.png', 'outdocs', 2, 1, 0, 0)";
		$database->setQuery($query);
		$database->query();
	}
	
	function jlms_upgrade_operation($jlms_this_version = '1.2.0', $prev_version = '1.0.0') {
		
		$absolutePath = JPATH_SITE;
		$liveSite = substr_replace(JURI::root(), '', -1, 1);
		$database =& JFactory::getDBO();		

		$database->SetQuery("UPDATE #__lms_config SET lms_config_value = '".$jlms_this_version."' WHERE lms_config_var = 'jlms_version'");
		$database->query();
		if ($prev_version == '1.0.0') { //update from version 1.0.0 to 1.0.0_SP1
			// add `spec_reg` field to #__lms_courses
			$database->SetQuery("ALTER TABLE `#__lms_courses` ADD `spec_reg` TINYINT DEFAULT '0' NOT NULL");
			$database->query();
			$prev_version = '1.0.0_SP1';
		}

		if ($prev_version == '1.0.0_SP1') {//update from version 1.0.0_SP1 to 1.0.0_SP2

			@chmod( $absolutePath . "/components/com_joomla_lms/upload", 0777);

			$database->SetQuery("ALTER TABLE `#__lms_languages` ADD `published` INT DEFAULT '1' NOT NULL");
			$database->query();

			$database->SetQuery("ALTER TABLE `#__lms_agenda` ADD INDEX `course_id` ( `course_id` )");
			$database->query();
			$database->SetQuery("ALTER TABLE `#__lms_attendance` ADD INDEX `attend_index` ( `course_id` , `user_id` , `at_period` )");
			$database->query();
			$database->SetQuery("ALTER TABLE `#__lms_certificate_users` ADD INDEX `cu_index` ( `course_id` , `user_id` ) ");
			$database->query();
			$database->SetQuery("ALTER TABLE `#__lms_certificates` ADD INDEX `course_id` ( `course_id` ) ");
			$database->query();
			$database->SetQuery("ALTER TABLE `#__lms_chat_history` ADD INDEX `history_index` ( `course_id` , `group_id` , `user_id` , `recv_id` ) ");
			$database->query();
			$database->SetQuery("ALTER TABLE `#__lms_chat_users` ADD INDEX `chat_users_index` ( `course_id` , `group_id` , `user_id` ) ");
			$database->query();
			$database->SetQuery("ALTER TABLE `#__lms_conference_doc` ADD INDEX `course_id` ( `course_id` ) ");
			$database->query();
			$database->SetQuery("ALTER TABLE `#__lms_courses_backups` ADD INDEX `course_id` ( `course_id` ) ");
			$database->query();
			$database->SetQuery("ALTER TABLE `#__lms_documents` ADD INDEX `course_id` ( `course_id` ) ");
			$database->query();
			$database->SetQuery("ALTER TABLE `#__lms_documents_zip` ADD INDEX `course_id` ( `course_id` ) ");
			$database->query();
			$database->SetQuery("ALTER TABLE `#__lms_dropbox` ADD INDEX `dropbox_index` ( `course_id` , `owner_id` , `recv_id` , `file_id` ) ");
			$database->query();
			$database->SetQuery("ALTER TABLE `#__lms_forum_details` ADD INDEX `forum_index` ( `course_id` , `group_id` , `is_active` ) ");
			$database->query();
			$database->SetQuery("ALTER TABLE `#__lms_gradebook` ADD INDEX `gb_index` ( `course_id` , `user_id` , `gbi_id` ) ");
			$database->query();
			$database->SetQuery("ALTER TABLE `#__lms_gradebook_items` ADD INDEX `gbi_index` ( `course_id` , `gbc_id` ) ");
			$database->query();
			$database->SetQuery("ALTER TABLE `#__lms_gradebook_scale` ADD INDEX `course_id` ( `course_id` ) ");
			$database->query();
			$database->SetQuery("ALTER TABLE `#__lms_homework` ADD INDEX `course_id` ( `course_id` ) ");
			$database->query();
			$database->SetQuery("ALTER TABLE `#__lms_homework_results` ADD INDEX `hwr_index` ( `course_id` , `user_id` , `hw_id` ) ");
			$database->query();
			$database->SetQuery("ALTER TABLE `#__lms_languages` ADD INDEX `lang_index` ( `published` , `lang_name` ) ");
			$database->query();
			$database->SetQuery("ALTER TABLE `#__lms_learn_path_conds` ADD INDEX `lpc_index` ( `course_id` , `lpath_id` , `step_id` ) ");
			$database->query();
			$database->SetQuery("ALTER TABLE `#__lms_learn_path_results` ADD INDEX `lpr_index` ( `course_id` , `lpath_id` , `user_id` ) ");
			$database->query();
			$database->SetQuery("ALTER TABLE `#__lms_learn_path_step_results` ADD INDEX `lpsr_index` ( `result_id` , `step_id` ) ");
			$database->query();
			$database->SetQuery("ALTER TABLE `#__lms_learn_path_steps` ADD INDEX `lps_index` ( `course_id` , `lpath_id` , `parent_id` ) ");
			$database->query();
			$database->SetQuery("ALTER TABLE `#__lms_learn_paths` ADD INDEX `course_id` ( `course_id` ) ");
			$database->query();
			$database->SetQuery("ALTER TABLE `#__lms_links` ADD INDEX `course_id` ( `course_id` ) ");
			$database->query();
			$database->SetQuery("ALTER TABLE `#__lms_scorm_packages` ADD INDEX `course_id` ( `course_id` ) ");
			$database->query();
			$database->SetQuery("ALTER TABLE `#__lms_scorm_sco` ADD INDEX `ss_index` ( `content_id` , `user_id` ) ");
			$database->query();
			$database->SetQuery("ALTER TABLE `#__lms_subscriptions_courses` ADD UNIQUE `sc_index` ( `sub_id` , `course_id` ) ");
			$database->query();
			$database->SetQuery("ALTER TABLE `#__lms_usergroups` ADD INDEX `course_id` ( `course_id` ) ");
			$database->query();

			$prev_version = '1.0.0_SP2';
		}

		if ($prev_version == '1.0.0_SP2') {//update from version 1.0.0_SP2 to 1.0.0_SP3
			$database->SetQuery("ALTER TABLE `#__lms_attendance` CHANGE `at_status` `at_status` TINYINT( 4 ) DEFAULT '0'");
			$database->query();
			$query = "ALTER TABLE `#__lms_courses` CHANGE `cat_id` `cat_id` INT( 11 ) ,"
			. "\n CHANGE `published` `published` TINYINT( 4 ) DEFAULT '0',"
			. "\n CHANGE `publish_start` `publish_start` TINYINT( 4 ) DEFAULT '0',"
			. "\n CHANGE `publish_end` `publish_end` TINYINT( 4 ) DEFAULT '0',"
			. "\n CHANGE `add_forum` `add_forum` TINYINT( 4 ) DEFAULT '0',"
			. "\n CHANGE `add_chat` `add_chat` TINYINT( 4 ) DEFAULT '0',"
			. "\n CHANGE `add_hw` `add_hw` TINYINT( 4 ) DEFAULT '1',"
			. "\n CHANGE `add_attend` `add_attend` TINYINT( 4 ) DEFAULT '1',"
			. "\n CHANGE `paid` `paid` TINYINT( 4 ) DEFAULT '0',"
			. "\n CHANGE `spec_reg` `spec_reg` TINYINT( 4 ) DEFAULT '0'"
			;
			$database->SetQuery($query);
			$database->query();
			$database->SetQuery("ALTER TABLE `#__lms_users` CHANGE `lms_usertype_id` `lms_usertype_id` TINYINT( 4 ) DEFAULT '0'");
			$database->query();
			$query = "ALTER TABLE `#__lms_users_in_groups` CHANGE `publish_start` `publish_start` TINYINT( 4 ) DEFAULT '0',"
			. "\n CHANGE `publish_end` `publish_end` TINYINT( 4 ) DEFAULT '0' ";
			$database->SetQuery($query);
			$database->query();

			$prev_version = '1.0.0_SP3';
		}

		if ($prev_version == '1.0.0_SP3') {//update from version 1.0.0_SP3 to 1.0.1
			$database->SetQuery("ALTER TABLE `#__lms_courses` ADD `gid` TINYTEXT NOT NULL");
			$database->query();
			$database->SetQuery("ALTER TABLE `#__lms_courses` ADD `params` TEXT NOT NULL");
			$database->query();

			// new tables _lms_local_menu, _lms_menu, _lms_learn_path_step_quiz_results - created in XML

			$query = "INSERT INTO `#__lms_config` VALUES ('lms_check_version', 1),"
			. "\n ('date_format', 'Y-m-d'),"
			. "\n ('date_format_fdow', 1),"
			. "\n ('jlms_title', 'Online Courses'),"
			. "\n ('meta_keys', 'online courses, elearning, lms, online education'),"
			. "\n ('meta_desc', 'Online courses catalog by JoomlaLMS')";
			$database->SetQuery($query);
			$database->query();

			jlms_insert_menu_items();
			$prev_version = '1.0.1';
		}

		if ($prev_version == '1.0.1') {//update from version 1.0.1 to 1.0.2
			$database->SetQuery("ALTER TABLE `#__lms_learn_paths` ADD `lp_type` INT NOT NULL DEFAULT '0' AFTER `item_id`");
			$database->query();
			$database->SetQuery("ALTER TABLE `#__lms_learn_paths` ADD `lp_params` TEXT NOT NULL");
			$database->query();
			$prev_version = '1.0.2';
		}

		/* upgrade from 1.0.2 to 1.0.2 QP */
		if ($prev_version == '1.0.2') {// ($prev_version == '1.0.1') {
			$database->SetQuery("ALTER TABLE `#__lms_quiz_t_category` ADD `is_quiz_cat` INT DEFAULT '1' NOT NULL");
			$database->query();
			// + CREATE TABLE `jos_lms_quiz_r_student_quiz_pool` (in XML)
			$database->SetQuery("ALTER TABLE `#__lms_quiz_t_question` ADD `c_pool` INT DEFAULT '0' NOT NULL");
			$database->query();
			$database->SetQuery("ALTER TABLE `#__lms_quiz_t_question` ADD `c_qcat` INT DEFAULT '0' NOT NULL");
			$database->query();
			$database->SetQuery("ALTER TABLE `#__lms_conference_doc` CHANGE `doc_id` `doc_id` INT( 11 ) DEFAULT NULL AUTO_INCREMENT");
			$database->query();
			$prev_version = '1.0.2 QP';
		}

		if ($prev_version == '1.0.2 QP') {// ($prev_version == '1.0.1') {
			$database->SetQuery("ALTER TABLE `#__lms_conference_doc` CHANGE `course_id` `course_id` INT( 11 ) NOT NULL");
			$database->query();
			$database->SetQuery("ALTER TABLE `#__lms_conference_doc` CHANGE `owner_id` `owner_id` INT( 11 ) NOT NULL");
			$database->query();
			$database->SetQuery("ALTER TABLE `#__lms_quiz_results` ADD `quiz_max_score` INT DEFAULT '0' NOT NULL AFTER `user_score`");
			$database->query();
			$database->SetQuery("ALTER TABLE `#__lms_quiz_t_category` ADD `is_quiz_cat` INT DEFAULT '1' NOT NULL");
			$database->query();
			$database->SetQuery("ALTER TABLE `#__lms_quiz_t_quiz` ADD `params` TEXT NOT NULL");
			$database->query(); 
			$database->SetQuery("ALTER TABLE `#__lms_quiz_t_question` ADD `params` TEXT NOT NULL");
			$database->query(); 
			$prev_version = '1.0.3';

			$query = "INSERT INTO `#__lms_menu` (`lang_var`, `image`, `task`, `ordering`, `published`, `is_separator`, `user_access`)"
			. "\n VALUES ('_JLMS_TOOLBAR_SUBSCRIPTIONS', 'tlb_subscriptions.png', 'subscription', 3, 1, 0, 1),"
			. "\n ('_JLMS_TOOLBAR_SUBSCRIPTIONS', 'tlb_subscriptions.png', 'subscription', 2, 1, 0, 2),"
			. "\n ('_JLMS_TOOLBAR_SUBSCRIPTIONS', 'tlb_subscriptions.png', 'subscription', 2, 1, 0, 0),"
			. "\n ('_JLMS_TOOLBAR_SUBSCRIPTIONS', 'tlb_subscriptions.png', 'subscription', 2, 1, 0, 6)";
			$database->setQuery($query);
			$database->query();

			$query = "UPDATE `#__lms_menu` SET `image` = 'tlb_course_home.png' WHERE `lang_var` = '_JLMS_PATHWAY_COURSE_HOME'";
			$database->setQuery($query);
			$database->query();

			jlms_newCfgSettings();

			$prev_version = '1.0.3';
		}

		if ($prev_version == '1.0.3') {
			// !!! don't remove these queries !!! (to fix some issues in pre-1.0.3 versions)
			$database->SetQuery("SELECT c_pool FROM #__lms_quiz_t_question LIMIT 0, 1");
			$r = $database->loadResult();
			if ($database->geterrormsg()) {
				$database->SetQuery("ALTER TABLE `#__lms_quiz_t_question` ADD `c_pool` INT DEFAULT '0' NOT NULL");
				$database->query();
				$database->SetQuery("ALTER TABLE `#__lms_quiz_t_question` ADD `c_qcat` INT DEFAULT '0' NOT NULL");
				$database->query();
			}
			$database->SetQuery("SELECT count(*) FROM #__lms_quiz_t_qtypes");
			if (!$database->loadResult()) {
				$query = "INSERT INTO `#__lms_quiz_t_qtypes` VALUES (1, 'Multiple Choice'),"
					. " \n (2, 'Multiple Response'),"
					. " \n (3, 'True/False'),"
					. " \n (4, 'Matching Drag and Drop'),"
					. " \n (5, 'Matching Drop-Down'),"
					. " \n (6, 'Fill in the blank'),"
					. " \n (7, 'Hotspot'),"
					. " \n (8, 'Surveys')";
				$database->SetQuery($query);
				$database->query();
			}
			$prev_version = '1.0.3_CE';
		}

		if ($prev_version == '1.0.3_CE') {

			$query = "INSERT INTO `#__lms_menu` (`lang_var`, `image`, `task`, `ordering`, `published`, `is_separator`, `user_access`)"
			. "\n VALUES ('_JLMS_TOOLBAR_HOME', 'tlb_home.png', '', 0, 1, 0, -1),"
			. "\n ('_JLMS_TOOLBAR_SUBSCRIPTIONS', 'tlb_subscriptions.png', 'subscription', 1, 1, 0, -1)";
			$database->setQuery($query);
			$database->query();

			// config
			$prev_version = '1.0.4';
		}

		if ($prev_version == '1.0.4') {
			/* UPDATE to 1.0.4a */

			/* Certificate Changes */
			$database->SetQuery("ALTER TABLE `#__lms_certificates` ADD `parent_id` INT DEFAULT '0' NOT NULL AFTER `id`");
			$database->query();
			$database->SetQuery("ALTER TABLE `#__lms_certificate_prints` ADD `role_id` INT DEFAULT '0' NOT NULL AFTER `user_id`");
			$database->query();
			$database->SetQuery("ALTER TABLE `#__lms_certificate_prints` DROP INDEX `user_id`");
			$database->query();
			$database->SetQuery("ALTER TABLE `#__lms_certificate_prints` ADD UNIQUE `user_id` ( `user_id` , `role_id` , `course_id` , `crtf_id` , `quiz_id` )");
			$database->query();

			/* Course Spec registration changes */
			$database->SetQuery("ALTER TABLE `#__lms_spec_reg_questions` ADD `role_id` INT DEFAULT '0' NOT NULL AFTER `course_id`");
			$database->query();
			$database->SetQuery("ALTER TABLE `#__lms_spec_reg_questions` ADD `ordering` INT DEFAULT '0' NOT NULL AFTER `role_id`");
			$database->query();
			$database->SetQuery("ALTER TABLE `#__lms_spec_reg_questions` DROP INDEX `course_id`");
			$database->query();
			$database->SetQuery("ALTER TABLE `#__lms_spec_reg_questions` ADD `is_optional` INT DEFAULT '0' NOT NULL AFTER `role_id`");
			$database->query();
			$database->SetQuery("ALTER TABLE `#__lms_spec_reg_questions` ADD `default_answer` VARCHAR( 255 ) NOT NULL AFTER `ordering`");
			$database->query();

			/* Course spec registration (answers section) changes */
			$database->SetQuery("ALTER TABLE `#__lms_spec_reg_answers` ADD `role_id` INT DEFAULT '0' NOT NULL AFTER `user_id`");
			$database->query();
			$database->SetQuery("ALTER TABLE `#__lms_spec_reg_answers` ADD `quest_id` INT DEFAULT '0' NOT NULL AFTER `role_id`");
			$database->query();
			$database->SetQuery("ALTER TABLE `#__lms_spec_reg_answers` DROP INDEX `course_user`");
			$database->query();
			$database->SetQuery("ALTER TABLE `#__lms_spec_reg_answers` ADD UNIQUE `course_user` ( `course_id` , `user_id` , `role_id` , `quest_id` )");
			$database->query();

			/* !!!!!!HERE WE SHOULD UPDATE SPEC_REG_ANSWERS TABLE !!!!!! */
			$query = "SELECT id, course_id FROM #__lms_spec_reg_questions";
			$database->SetQuery($query);
			$cq_info = $database->LoadObjectList();

			foreach ($cq_info as $cqi) {
				$query = "UPDATE #__lms_spec_reg_answers SET quest_id = $cqi->id WHERE course_id = $cqi->course_id";
				$database->SetQuery($query);
				$database->query();
			}

			/* Quiz tool changes */
			$database->SetQuery("ALTER TABLE `#__lms_quiz_t_question` ADD `c_explanation` TEXT NOT NULL");
			$database->query();
			$database->SetQuery("ALTER TABLE `#__lms_quiz_t_blank` ADD `c_default` VARCHAR(100) NOT NULL");
			$database->query();

			$query = "INSERT INTO `#__lms_quiz_t_qtypes` VALUES (9, 'Likert Scale'),"
				. " \n (10, 'Boilerplate')";
			$database->SetQuery($query);
			$database->query();

			/* User roles changes */
			$database->SetQuery("ALTER TABLE `#__lms_usertypes` ADD `roletype_id` INT DEFAULT '1' NOT NULL AFTER `id`");
			$database->query();
			$database->SetQuery("ALTER TABLE `#__lms_usertypes` CHANGE `lms_usertype` `lms_usertype` VARCHAR( 50 )");
			$database->query();
			$database->SetQuery("ALTER TABLE `#__lms_users_in_groups` ADD `role_id` INT DEFAULT '2' NOT NULL AFTER `user_id");
			$database->query();

			$database->SetQuery("UPDATE `#__lms_usertypes` SET `roletype_id` = '2' WHERE `id` = 1");
			$database->query();
			// for id = 2 - used default value - roletype_id - 1
			$database->SetQuery("UPDATE `#__lms_usertypes` SET `roletype_id` = '0' WHERE `id` = 3");
			$database->query();
			$database->SetQuery("UPDATE `#__lms_usertypes` SET `roletype_id` = '2' WHERE `id` = 4");
			$database->query();
			$database->SetQuery("UPDATE `#__lms_usertypes` SET `roletype_id` = '4' WHERE `id` = 5");
			$database->query();
			$database->SetQuery("UPDATE `#__lms_usertypes` SET `roletype_id` = '3' WHERE `id` = 6");
			$database->query();

			/* Create folder for certificates */			
			$abs_path = str_replace('\\\\', '\\', $absolutePath);
			$abs_path = str_replace('\\', '/', $abs_path);
			$temp_folder = jlms_create_temp_folder();
			$crtf_folder = '';
			if ($temp_folder && is_writable($absolutePath . "/" . $temp_folder )) {
				/* certificates folder */
				@mkdir( $abs_path . "/".$temp_folder."/certificates");
				if (!is_writable($abs_path . "/".$temp_folder."/certificates" )) {
					@chmod($abs_path . "/".$temp_folder."/certificates", 0777);
				}
				if (is_writable($abs_path . "/".$temp_folder."/certificates" )) {
					$crtf_folder = $abs_path . "/".$temp_folder."/certificates";
				}
			}
			if (!$crtf_folder) {
				@mkdir( $abs_path . "/media/jlms_certificates");
				$crtf_folder = $abs_path . "/media/jlms_certificates";
			}
			$query = "INSERT INTO `#__lms_config` VALUES ('jlms_crtf_folder', '".$crtf_folder."')";
			$database->SetQuery($query);
			$database->query();

			$prev_version = '1.0.4a';
		}
		// 1.0.4a (DEV version) - 'Certificates'; 'Enrollment questions'; 'Quiz/Survey'; 'Roles management' modifiations

		if ($prev_version == '1.0.4a' || $prev_version == '1.0.4.1') {
			/* UPDATE to 1.0.4b */

			/* Certificate Changes */
			$database->SetQuery("ALTER TABLE `#__lms_certificates` ADD `crtf_font` varchar(50) DEFAULT '' NOT NULL AFTER `crtf_shadow`");
			$database->query();

			$database->SetQuery("ALTER TABLE `#__lms_links` CHANGE `link_href` `link_href` VARCHAR( 255 ) DEFAULT NULL");
			$database->query();

			$database->SetQuery("ALTER TABLE `#__lms_quiz_t_quiz` CHANGE `c_full_score` `c_full_score` INT DEFAULT '0'");
			$database->query();

			$prev_version = '1.0.4b';
		}
		// 1.0.4b (DEV version) - a little fixes for previous modifications

		if ($prev_version == '1.0.4b') {
			$database->SetQuery("ALTER TABLE `#__lms_courses` ADD `sec_cat` TEXT");
			$database->query();
			$prev_version = '1.0.4c';
		}
		// 1.0.4c (DEV version) - 'Secondary categories' modification

		if ($prev_version == '1.0.4c') {
			$database->SetQuery("ALTER TABLE `#__lms_payments` ADD `payment_type` INT DEFAULT '0' NOT NULL AFTER `id`");
			$database->query();
			$prev_version = '1.0.4d';
		}
		// 1.0.4d (DEV version) - 'Custom subscriptions' modification

		if ($prev_version == '1.0.4d') {
			$database->SetQuery("ALTER TABLE `#__lms_forum_details` ADD `board_type` INT NOT NULL DEFAULT '1' AFTER `course_id`");
			$database->query();

			$database->SetQuery("ALTER TABLE `#__lms_links` CHANGE `link_href` `link_href` VARCHAR( 255 ) DEFAULT NULL");
			$database->query();

			$database->SetQuery("ALTER TABLE `#__lms_subscriptions` ADD `sub_descr` TEXT NOT NULL");
			$database->query();

			$query = "INSERT INTO `#__lms_quiz_t_qtypes` VALUES (11, 'Matching Drag and Drop Images'),"
				. " \n (12, 'Multiple Images Choice'), (13, 'Multiple Images Response')";
			$database->SetQuery($query);
			$database->query();

			// queries below was missed in the official 1.0.5 release - they should be checked/fixed somewhere in the install script everytime !!!
			$query = "ALTER TABLE `#__lms_quiz_r_student_question` ADD `c_correct` INT DEFAULT '0' NOT NULL";
			$database->SetQuery($query);
			$database->query();

			$query = "ALTER TABLE `#__lms_learn_path_step_quiz_results` ADD `start_id` INT NOT NULL DEFAULT '0', ADD `unique_id` VARCHAR( 32 ) NOT NULL DEFAULT '0';";
			$database->SetQuery($query);
			$database->query();

			$prev_version = '1.0.5';
		}
		// 1.0.5 - release version

		if ($prev_version == '1.0.5') {
			$query = "ALTER TABLE `#__lms_chat_history` CHANGE `user_message` `user_message` TEXT";
			$database->SetQuery($query);
			$database->query();
			$query = "ALTER TABLE `#__lms_users` ADD UNIQUE `user_id_2` ( `user_id` );";
			$database->SetQuery($query);
			$database->query();
			$query = "UPDATE `#__lms_usertypes` SET `roletype_id` = '5' WHERE `id` = 4 AND (`lms_usertype` = 'assistant' OR `lms_usertype` = 'Assistant') AND `roletype_id` = 2";
			$database->SetQuery($query);
			$database->query();
			$query = "UPDATE `#__lms_usertypes` SET `lms_usertype` = 'LMS administrator' WHERE `lms_usertype` = 'super_user'";
			$database->SetQuery($query);
			$database->query();
			$query = "ALTER TABLE `#__lms_course_cats` ADD `parent` INT NOT NULL DEFAULT '0', ADD `restricted` INT NOT NULL DEFAULT '0', ADD `groups` VARCHAR( 255 ) NOT NULL DEFAULT '';";
			$database->SetQuery($query);
			$database->query();
			$prev_version = '1.0.5 SP1';
		}
		if ($prev_version == '1.0.5 SP1') {
			$prev_version = '1.0.5 20080818';
		}
		if ($prev_version == '1.0.5 20080818') {
			$prev_version == '1.0.5 20080901';
			jlms_create_forums(true);
		}
		if ($prev_version == '1.0.5 20080901') {
			$query = "ALTER TABLE `#__lms_quiz_t_quiz` CHANGE `c_full_score` `c_full_score` INT DEFAULT '0';";
			$database->SetQuery($query);
			$database->query();
			$query = "ALTER TABLE `#__lms_forums` ADD `forum_moderators` VARCHAR( 255 ) NOT NULL AFTER `forum_permissions`;";
			$database->SetQuery($query);
			$database->query();
			$query = "ALTER TABLE `#__lms_forum_details` ADD `need_update` TINYINT DEFAULT '0' NOT NULL;";
			$database->SetQuery($query);
			$database->query();
			$prev_version == '1.0.5 20080922';
			//TODO: implement these changes to the XML file
		}
		if ($prev_version == '1.0.5 20080922') {
			$query = "ALTER TABLE `#__lms_courses` ADD `ordering` INT NOT NULL DEFAULT '0';";
			$database->SetQuery($query);
			$database->query();
			$prev_version == '1.0.5 20080930';
			//TODO: implement these changes to the XML file
		}
		if ($prev_version == '1.0.5 20080930') {
			$query = "ALTER TABLE `#__lms_quiz_t_question` ADD `published` INT NOT NULL DEFAULT '1' AFTER `c_type`;";
			$database->SetQuery($query);
			$database->query();
			$prev_version == '1.0.5 20081001';
			//TODO: implement these changes to the XML file
			//TODO: !!!! Populate `lms_forums` with the information about forums!!!
		}
			
		if ($prev_version == '1.0.5 20081001') {
			$database->SetQuery("ALTER TABLE `#__lms_quiz_t_quiz` ADD `c_resume` TINYINT NOT NULL DEFAULT '0'");
			$database->query();
			$database->SetQuery("ALTER TABLE `#__lms_quiz_t_quiz` ADD `c_max_numb_attempts` INT NOT NULL DEFAULT '0'");
			$database->query();
			$prev_version == '1.0.5 20081126';
		}
		if ($prev_version == '1.0.5 20081126') {
			$database->SetQuery("ALTER TABLE `#__lms_courses` ADD `name_alias` VARCHAR( 100 ) NOT NULL AFTER `course_name`");
			$database->query();
			$prev_version == '1.0.5 20090128';
		}
		if ($prev_version == '1.0.5 20090128') {
			$database->SetQuery("ALTER TABLE `#__lms_course_cats` ADD `lesson_type` INT DEFAULT '0' NOT NULL AFTER `groups`");
			$database->query();
			$prev_version == '1.0.5 20090305';
		}
		if ($prev_version == '1.0.5 20090305') {

			$database->SetQuery("ALTER TABLE `#__lms_course_cats` ADD `lesson_type` INT DEFAULT '0'");
			$database->query();
			$query = "ALTER TABLE `#__lms_conference_doc` ADD `upload_type` TINYINT DEFAULT '0' NOT NULL AFTER `owner_id` ,"
				. "\n ADD `file_id` INT NOT NULL AFTER `upload_type`";
			$database->SetQuery($query);
			$database->query();
			$database->SetQuery("ALTER TABLE `#__lms_courses` ADD `course_sh_description` TEXT NOT NULL AFTER `course_description`");
			$database->query();
			$database->SetQuery("ALTER TABLE `#__lms_forum_details` ADD `need_update` TINYINT DEFAULT '0' NOT NULL AFTER `is_active`");
			$database->query();
			$database->SetQuery("ALTER TABLE `#__lms_forums` CHANGE `id` `id` INT( 11 ) AUTO_INCREMENT ");
			$database->query();
			$database->SetQuery("ALTER TABLE `#__lms_quiz_t_question` ADD `published` INT DEFAULT '1' NOT NULL AFTER `c_type`");
			$database->query();
			$query = "ALTER TABLE `#__lms_usergroups` ADD `start_date` DATE NOT NULL ,"
				. "\n ADD `end_date` DATE NOT NULL ,"
				. "\n ADD `publish_start_date` INT DEFAULT '0' NOT NULL ,"
				. "\n ADD `publish_end_date` INT DEFAULT '0' NOT NULL ;";
			$database->SetQuery($query);
			$database->query();
			$database->SetQuery("ALTER TABLE `#__lms_usertypes` ADD `default_role` INT DEFAULT '1' NOT NULL");
			$database->query();
			$database->SetQuery("ALTER TABLE `#__lms_courses` ADD `ordering` INT DEFAULT '0' NOT NULL");
			$database->query();

			$prev_version == '1.0.6 beta';
		}
		if ($prev_version == '1.0.6 beta') {
			$database->SetQuery("ALTER TABLE `#__lms_quiz_t_question` ADD `c_pool_gqp` int(11) NOT NULL DEFAULT '0'");
			$database->query();
			$prev_version == '1.0.6 beta2';
		}
		if ($prev_version == '1.0.6 beta2') {
			$database->SetQuery("ALTER TABLE `#__lms_certificates` ADD `published` INT DEFAULT '1' NOT NULL AFTER `course_id`");
			$database->query();
			$prev_version == '1.0.6 beta3';
		}
		if ($prev_version == '1.0.6 beta3') {
			$prev_version == '1.0.6';
		}
		$database->SetQuery("ALTER TABLE `#__lms_quiz_t_question` ADD `published` INT(11) NOT NULL DEFAULT '1' AFTER `c_type`");
		$database->query();
		// if this field was added previously - change its default value from '0' to '1'
		$database->SetQuery("ALTER TABLE `#__lms_quiz_t_question` CHANGE `published` `published` INT(11) DEFAULT '1'");
		$database->query();
		if ($prev_version == '1.0.6') {
			$prev_version == '1.0.7';
		}
		if ($prev_version == '1.0.7') {
			$prev_version == '1.1.0 RC';
		}
		if ($prev_version == '1.1.0 RC') {
			$prev_version == '1.1.0';
		}
		if ($prev_version == '1.1.0') {
			$prev_version == '1.2.0';
		}
		jlms_create_temp_folder(); // for creating + and for checking if error.

		jlms_create_invoices_folder(); // for creating + and for checking if error.

		jlms_install_plugins();

		jlms_checkCB();
	}

	$query = "SELECT * FROM #__users LIMIT 0,1";
	$database->SetQuery( $query );
	$database->query();
	
	if ($database->geterrormsg()) { //it did the trick on GoDaddy's servers after losing connection to the DB (after cpu-load (unzipping) operations on the shared hosting)
		sleep(1);
		
		$conf =& JFactory::getConfig();

		$host 		= $conf->getValue('config.host');
		$user 		= $conf->getValue('config.user');
		$password 	= $conf->getValue('config.password');
		$database	= $conf->getValue('config.db');
		$prefix 	= $conf->getValue('config.dbprefix');
		$driver 	= $conf->getValue('config.dbtype');
		$debug 		= $conf->getValue('config.debug');
		
		$options	= array ('driver' => $driver, 'host' => $host, 'user' => $user, 'password' => $password, 'database' => $database, 'prefix' => $prefix, mt_rand() => mt_rand() );

		$database = JDatabase::getInstance( $options );	
	}
		//Set up icons for admin area
	if (strnatcasecmp( $version->RELEASE, '1.6' ) >= 0) 
	{
		//look joomla_lms.xml img="" menu param
		
	} else {
		$database->setQuery("UPDATE #__components SET admin_menu_img='../components/com_joomla_lms/lms_images/logo_small.png' WHERE admin_menu_link like 'option=com_joomla_lms'");
		$database->query();
	
		$icon = 'users';
		if (class_exists('JApplication')) { $icon = 'user'; }
		$database->setQuery("UPDATE #__components SET admin_menu_img='js/ThemeOffice/".$icon.".png' WHERE admin_menu_link='option=com_joomla_lms&task=users'");
		$database->query();
		$database->setQuery("UPDATE #__components SET admin_menu_img='js/ThemeOffice/content.png' WHERE admin_menu_link='option=com_joomla_lms&task=courses'");
		$database->query();
		$database->setQuery("UPDATE #__components SET admin_menu_img='js/ThemeOffice/config.png' WHERE admin_menu_link='option=com_joomla_lms&task=config'");
		$database->query();
		$icon = 'home';
		if (class_exists('JApplication')) { $icon = 'frontpage'; }
		$database->setQuery("UPDATE #__components SET admin_menu_img='js/ThemeOffice/".$icon.".png' WHERE admin_menu_link='option=com_joomla_lms&task=look_feel'");
		$database->query();
		$icon = 'menus';
		if (class_exists('JApplication')) { $icon = 'menu'; }
		$database->setQuery("UPDATE #__components SET admin_menu_img='js/ThemeOffice/".$icon.".png' WHERE admin_menu_link='option=com_joomla_lms&task=menu_manage'");
		$database->query();
		$database->setQuery("UPDATE #__components SET admin_menu_img='js/ThemeOffice/content.png' WHERE admin_menu_link='option=com_joomla_lms&task=subscriptions'");
		$database->query();
		$icon = 'content';
		if (class_exists('JApplication')) { $icon = 'checkin'; }
		$database->setQuery("UPDATE #__components SET admin_menu_img='js/ThemeOffice/content.png' WHERE admin_menu_link='option=com_joomla_lms&task=payments'");
		$database->query();
		$icon = 'install';
		if (class_exists('JApplication')) { $icon = 'plugin'; }
		$database->setQuery("UPDATE #__components SET admin_menu_img='js/ThemeOffice/".$icon.".png' WHERE admin_menu_link='option=com_joomla_lms&task=processorslist'");
		$database->query();
		$database->setQuery("UPDATE #__components SET admin_menu_img='js/ThemeOffice/".$icon.".png' WHERE admin_menu_link='option=com_joomla_lms&task=pluginslist'");
		$database->query();
		$database->setQuery("UPDATE #__components SET admin_menu_img='js/ThemeOffice/help.png' WHERE admin_menu_link='option=com_joomla_lms&task=support'");
		$database->query();
		$icon = 'document';
		if (class_exists('JApplication')) {
			$icon = 'info';
		}
		$database->setQuery("UPDATE #__components SET admin_menu_img='js/ThemeOffice/".$icon.".png' WHERE admin_menu_link='option=com_joomla_lms&task=about'");
		$database->query();	
			
	}

	//install mambot
	jlms_install_mambot();

	jlms_copyfonts();

	$jlms_this_version = '1.2.0';
	$database->SetQuery("SELECT lms_config_value FROM #__lms_config WHERE lms_config_var = 'jlms_version'");
	$prev_version = $database->LoadResult();
	if ($prev_version) {
		
		$version = new JVersion();		
		
		if ( strnatcasecmp( $version->RELEASE, '1.6' ) >= 0 ) 
		{
			$query = "SELECT id FROM #__extensions WHERE `type` = 'component' AND `element` = 'com_joomla_lms'";
			$database->SetQuery($query);
			$jlms_comp_id = $database->LoadResult();
		} else {
			/* Correcting of menu items */
			$query = "SELECT id FROM #__components WHERE `option` = 'com_joomla_lms' AND `link` = 'option=com_joomla_lms'";
			$database->SetQuery($query);
			$jlms_comp_id = intval($database->LoadResult());
		}
		
		if ($jlms_comp_id) {
			$query = "UPDATE #__menu SET `componentid` = $jlms_comp_id WHERE (`type` = 'components' OR `type` = 'component') AND (`link` = 'index.php?option=com_joomla_lms' OR `link` = 'index.php?option=com_joomla_lms&task=courses')";
			$database->SetQuery($query);
			$database->query();
		} else {
			// hmm... very strange
		}

		jlms_upgrade_operation($jlms_this_version, $prev_version);

		//install languages
		jlms_install_languages();

		if ($JLMS_cfg_fms_enabled) {
			$query = "UPDATE `#__lms_config` SET lms_config_value = '".$database->Quote($JLMS_cfg_fms_url)."' WHERE lms_config_var = 'flascommRoot'";
			$database->SetQuery($query);$database->query();
			$query = "UPDATE `#__lms_config` SET lms_config_value = '".$JLMS_cfg_fms_users."' WHERE lms_config_var = 'maxConfClients'";
			$database->SetQuery($query);$database->query();
			$query = "UPDATE `#__lms_config` SET lms_config_value = '".$JLMS_cfg_fms_enabled."' WHERE lms_config_var = 'conference_enable'";
			$database->SetQuery($query);$database->query();
		}

	} else { // new installation

		$is_real_new_installation = true;
		// We are receive error in SELECT query FROM #__lms_config (JoomLearn LMS is installed !!) he-he
		if ($database->getErrorMsg()) {
			$is_real_new_installation = false;
			$database->SetQuery("DROP TABLE `#__lms_config`");$database->query();
			$query = "CREATE TABLE IF NOT EXISTS `#__lms_config` ("
			. "\n `lms_config_var` varchar(50) NOT NULL default '',"
			. "\n `lms_config_value` text,"
			. "\n PRIMARY KEY (`lms_config_var`) )";
			$database->SetQuery($query);$database->query();

			$database->SetQuery("SELECT count(*) FROM #__lms_usertypes");
			if ($database->loadResult()) {
				$is_real_new_installation = true;
			}
		}
		if (!$is_real_new_installation) {
			$query = "SELECT count(*) FROM #__lms_learn_path_step_types";
			$database->SetQuery($query);
			$is_prev = $database->LoadResult();
			if ($is_prev) {
				$xm_version = '1.0.0';
				$query = "SELECT count(*) FROM #__lms_menu";
				$database->SetQuery($query);
				$is_prev = $database->LoadResult();
				if ($is_prev) {
					$xm_version = '1.0.1';
				}
				$query = "SELECT count(*) FROM #__lms_quiz_t_quiz_pool";
				$database->SetQuery($query);
				$r = $database->LoadResult();
				if (!$database->geterrormsg()) {
					$xm_version = '1.0.2';//for one point below, because tables in XML are already exists
				}
				jlms_upgrade_operation($jlms_this_version, $xm_version);
			}

		} else {
			$adminDir = dirname(__FILE__);
			// create images folder for QUIZ plugin
			@mkdir( $absolutePath . "/images/joomlaquiz");
			@chmod( $absolutePath . "/images/joomlaquiz", 0777);
			@mkdir( $absolutePath . "/images/joomlaquiz/images");
			@chmod( $absolutePath . "/images/joomlaquiz/images", 0777);
			@chmod( $absolutePath . "/components/com_joomla_lms/upload", 0777);
			@copy( $adminDir. "/index.html", $absolutePath . "/images/joomlaquiz/images/index.html");
			@copy( $adminDir. "/index.html", $absolutePath . "/images/joomlaquiz/index.html");

			// INSERT data to DB

			//install languages
			jlms_install_languages();

			jlms_insert_menu_items();

			// insert 'attendance periods'
			$query = "INSERT INTO `#__lms_attendance_periods` VALUES (1, '08:00:00', '08:45:00'),"
			. "\n (2, '09:00:00', '09:45:00'),"
			. "\n (3, '10:00:00', '10:45:00'),"
			. "\n (4, '11:00:00', '11:45:00')";
			$database->SetQuery($query);$database->query();

			// insert 'course categories'
			$query = "INSERT INTO `#__lms_course_cats` (id, c_category) VALUES (1, 'Dentistry education'),"
			. "\n (2, 'Joomla courses'),"
			. "\n (3, 'K-12'),"
			. "\n (4, 'Language courses'),"
			. "\n (5, 'Marketing'),"
			. "\n (6, 'Math courses'),"
			. "\n (7, 'Other'),"
			. "\n (8, 'Plumbing')";
			$database->SetQuery($query);$database->query();

			// insert 'file types'
			$query = "INSERT INTO `#__lms_file_types` VALUES ('3gp'), ('avi'), ('bmp'), ('csv'), ('doc'), ('docx'), ('flv'), ('gif'), ('htm'), ('html'), ('jpe')";
			$database->SetQuery($query);$database->query();
			$query = "INSERT INTO `#__lms_file_types` VALUES ('jpeg'), ('jpg'), ('mov'), ('mp3'), ('mp4'), ('mpe'), ('mpeg'), ('mpg'), ('pdf'), ('png')";
			$database->SetQuery($query);$database->query();
			$query = "INSERT INTO `#__lms_file_types` VALUES ('ppt'), ('pptx'), ('qt'), ('ra'), ('ram'), ('rar'), ('rm'), ('rtf'), ('swf'), ('swfl'), ('tif')";
			$database->SetQuery($query);$database->query();
			$query = "INSERT INTO `#__lms_file_types` VALUES ('tiff'), ('txt'), ('wma'), ('wmv'), ('xls'), ('xlsx'), ('xml'), ('zip')";
			$database->SetQuery($query);$database->query();

			// insert 'gradebook cats'
			$query = "INSERT INTO `#__lms_gradebook_cats` VALUES (1, 'Assignment'),"
			. "\n (2, 'Attendance'),"
			. "\n (3, 'Essay'),"
			. "\n (4, 'Exam'),"
			. "\n (5, 'Extra Credit'),"
			. "\n (6, 'Final Exam'),"
			. "\n (7, 'Group Project'),"
			. "\n (8, 'Homework'),"
			. "\n (9, 'Journal'),"
			. "\n (10, 'Lab'),"
			. "\n (11, 'Midterm Exam'),"
			. "\n (12, 'Other'),"
			. "\n (13, 'Paper'),"
			. "\n (14, 'Presentation'),"
			. "\n (15, 'Problem Set'),"
			. "\n (16, 'Quiz'),"
			. "\n (17, 'Survey')";
			$database->SetQuery($query);$database->query();

			// insert 'quiz languages'
			$database->SetQuery("INSERT INTO `#__lms_quiz_languages` VALUES (1, 'english', 0), (2, 'german', 0), (3, 'brazilian', 0)");$database->query();

			// insert 'step types'
			$database->SetQuery("INSERT INTO `#__lms_learn_path_step_types` VALUES (1, 'chapter'), (2, 'document'), (3, 'link'), (4, 'content')");$database->query();

			// insert 'quiz quest types'
			$query = "INSERT INTO `#__lms_quiz_t_qtypes` VALUES (1, 'Multiple Choice'),"
				. " \n (2, 'Multiple Response'),"
				. " \n (3, 'True/False'),"
				. " \n (4, 'Matching Drag and Drop'),"
				. " \n (5, 'Matching Drop-Down'),"
				. " \n (6, 'Fill in the blank'),"
				. " \n (7, 'Hotspot'),"
				. " \n (8, 'Surveys'),"
				. " \n (9, 'Likert Scale'),"
				. " \n (10, 'Boilerplate'),"
				. " \n (11, 'Matching Drag and Drop Images'),"
				. " \n (12, 'Multiple Images Choice'),"
				. " \n (13, 'Multiple Images Response')";
			$database->SetQuery($query);
			$database->query();

			// insert 'quiz templates'
			$database->SetQuery("INSERT INTO `#__lms_quiz_templates` VALUES (3, 'joomlaquiz_lms_template')");$database->query();

			jlms_create_forums(false);
		}

		// config
		$query = "INSERT INTO `#__lms_config` VALUES ('jlms_version', '".$jlms_this_version."'),"
			. "\n ('tracking_enable', '1'),"
			. "\n ('quiz_hs_ofset_manual_value', '0'),"
			. "\n ('quiz_hs_offset_manual_correction', '0'),"
			. "\n ('quiz_hs_offset_div_class', 'wrapper'),"
			. "\n ('plugin_quiz', '1'), ('plugin_forum', '0'),"
			. "\n ('offline_message', ".$database->Quote("<div class='joomlalms_sys_message'>LMS system is currently down for maintenance. Please check back again soon.</div>")."),"
			. "\n ('new_user_password', 'student')";
		$database->SetQuery($query);
		$database->query();

		$query = "INSERT INTO `#__lms_config` VALUES ('maxConfClients', '".$JLMS_cfg_fms_users."'),"
			. "\n ('flascommRoot', ".$database->Quote($JLMS_cfg_fms_url)."),"
			. "\n ('conference_enable', '".$JLMS_cfg_fms_enabled."')";
		$database->SetQuery($query);
		$database->query();

		$query = "INSERT INTO `#__lms_config` VALUES ('lms_isonline', '0'),"
			. "\n ('jlms_terms', 'Terms and conditions agreement on subscription checkout.'),"
			. "\n ('jlms_cur_sign', '$'),"
			. "\n ('jlms_cur_code', 'USD'),"
			. "\n ('jlms_ap_redirect', ''), ('jlms_admin_emails', ''), ('is_cb_installed', '0'),"
			. "\n ('homepage_items', '10'),"
			. "\n ('frontpage_text', 'Welcome to JoomlaLMS.'),"
			. "\n ('frontpage_homework', '1'),"
			. "\n ('frontpage_dropbox', '1'),"
			. "\n ('frontpage_courses', '1'),"
			. "\n ('frontpage_announcements', '1'),"
			. "\n ('forum_path', ''),"
			. "\n ('enableterms', '1'),"
			. "\n ('enabletax', '0'),"
			. "\n ('default_language', '".$JLMS_default_language."'),"
			. "\n ('jlms_help_link', '".$JLMS_help_link."')"
			;
		$database->SetQuery($query);
		$database->query();

		$query = "INSERT INTO `#__lms_config` VALUES ('chat_enable', '1'),"
			. "\n ('attendance_days', ".$database->Quote('a:5:{i:0;s:1:"1";i:1;s:1:"2";i:2;s:1:"3";i:3;s:1:"4";i:4;s:1:"5";}')."),"
			. "\n ('allow_import_users', '1'),"
			. "\n ('jlms_heading', 'JoomlaLMS'),"
			. "\n ('lms_check_version', 1), ('date_format', 'Y-m-d'), ('date_format_fdow', 1),"
			. "\n ('jlms_title', 'Online Courses'), ('meta_keys', 'online courses, elearning, lms, online education'), ('meta_desc', 'Online courses catalog by JoomlaLMS')"
			;
		$database->SetQuery($query);
		$database->query();

		jlms_newCfgSettings();
		
		jlms_create_temp_folder();
		jlms_create_jlms_folders();

		jlms_install_plugins();

		jlms_checkCB();
	}

	// insert 'usertypes'
	jlms_insert_usertypes();

	/* Check for Quiz questions types */
	$query = "SELECT count(*) FROM #__lms_quiz_t_qtypes";
	$database->SetQuery($query);
	$is_qtypes = $database->LoadResult();
	if (!$is_qtypes) {
		jlms_insert_quiz_quest_types();
	}


	/* Check quizzes database issues (1.0.5 bugfixes) */
	$database->SetQuery("SELECT c_correct FROM #__lms_quiz_r_student_question LIMIT 0, 1");
	$r = $database->loadResult();
	if ($database->geterrormsg()) {
		$query = "ALTER TABLE `#__lms_quiz_r_student_question` ADD `c_correct` INT DEFAULT '0' NOT NULL";
		$database->SetQuery($query);
		$database->query();
	}
	$database->SetQuery("SELECT start_id FROM #__lms_learn_path_step_quiz_results LIMIT 0, 1");
	$r = $database->loadResult();
	if ($database->geterrormsg()) {
		$query = "ALTER TABLE `#__lms_learn_path_step_quiz_results` ADD `start_id` INT NOT NULL DEFAULT '0', ADD `unique_id` VARCHAR( 32 ) NOT NULL DEFAULT '0';";
		$database->SetQuery($query);
		$database->query();
	}
	$database->SetQuery("UPDATE `#__lms_quiz_t_qtypes` SET c_qtype = 'Multiple Images Choice' WHERE c_id = 12 AND c_qtype = 'Multuiple Images Choice'"); // fix spelling error
	$database->query();

	// iisue with certificates table (was happened on jomlalms.com)
	$database->SetQuery("SELECT crtf_name FROM #__lms_certificates LIMIT 0, 1");
	$r = $database->loadResult();
	if ($database->geterrormsg()) {
		$database->SetQuery("ALTER TABLE `#__lms_certificates` ADD `crtf_name` VARCHAR( 100 ) NOT NULL AFTER `file_id`");
		$database->query();
	}

	/* Check for JoomlaLMS menu items */
	$query = "SELECT count(*) FROM #__lms_menu";
	$database->SetQuery($query);
	$is_menus = $database->LoadResult();
	if (!$is_menus) {
		jlms_insert_menu_items();
	} else {
		/* FIX (guest menu was not created correctly on some installations (DON'T remove this code !) */
		$query = "SELECT count(*) FROM #__lms_menu WHERE `user_access` = -1";
		$database->SetQuery($query);
		$is_menus_g = $database->LoadResult();
		if (!$is_menus_g) {
			$query = "INSERT INTO `#__lms_menu` (`lang_var`, `image`, `task`, `ordering`, `published`, `is_separator`, `user_access`)"
			. "\n VALUES ('_JLMS_TOOLBAR_HOME', 'tlb_home.png', '', 0, 1, 0, -1),"
			. "\n ('_JLMS_TOOLBAR_SUBSCRIPTIONS', 'tlb_subscriptions.png', 'subscription', 1, 1, 0, -1)";
			$database->setQuery($query);
			$database->query();
		}

		/* Insert FileLibrary menu items - Don't remove this code */
		$query = "SELECT count(*) FROM #__lms_menu WHERE `lang_var` = '_JLMS_TOOLBAR_LIBRARY'";
		$database->SetQuery($query);
		$is_menus_g = $database->LoadResult();
		if (!$is_menus_g) {
			$query = "INSERT INTO `#__lms_menu` (`lang_var`, `image`, `task`, `ordering`, `published`, `is_separator`, `user_access`)"
			. "\n VALUES ('_JLMS_TOOLBAR_LIBRARY', 'tlb_library.png', 'outdocs', 3, 1, 0, 1),"
			. "\n ('_JLMS_TOOLBAR_LIBRARY', 'tlb_library.png', 'outdocs', 2, 1, 0, 2),"
			. "\n ('_JLMS_TOOLBAR_LIBRARY', 'tlb_library.png', 'outdocs', 2, 1, 0, 0),"
			. "\n ('_JLMS_TOOLBAR_LIBRARY', 'tlb_library.png', 'outdocs', 2, 1, 0, 6)";
			$database->setQuery($query);
			$database->query();
		}
	}
	
	echo '<div style="text-align:left;">';
	echo _JLMS_THANK_YOU_FOR_INSTALL;	
	echo '</div>';
	
	if ( strnatcasecmp( $version->RELEASE, '1.6' ) >= 0 )	
	{
		$query = "SELECT count(1) FROM #__extensions WHERE element = 'com_joomla_lms'";
		$database->setQuery( $query );
		$compExists = $database->loadResult();


		if( !$compExists ) {
			$query = "SHOW TABLE STATUS LIKE '#__extensions'";
			$query = str_replace( '#__', $database->getPrefix(), $query );
			$database->setQuery( $query );
			$tableInf = $database->loadObject();
		
			$query = "UPDATE #__menu SET component_id = '".$tableInf->Auto_increment."' WHERE link LIKE '%com_joomla_lms%' AND client_id = 0";		
			$database->setQuery( $query );
			$database->query();		
		}
	}
	
	jimport('joomla.filesystem.file');
					
	$metadata_file = JPATH_SITE.DS.'components'.DS.'com_joomla_lms'.DS.'metadata.xml';
	$metadata_file_renamed = JPATH_SITE.DS.'components'.DS.'com_joomla_lms'.DS.'metadata.xml_';
			
	if( strnatcasecmp( $version->RELEASE, '1.6' ) >= 0 )
	{										
		if ( JFile::exists( $metadata_file ) ) 
		{
			JFile::move( $metadata_file, $metadata_file_renamed );
		}															
	} else {
		if (JFile::exists( $metadata_file_renamed )) 
		{
			JFile::move( $metadata_file_renamed, $metadata_file );						
		}
	} 
}
?>