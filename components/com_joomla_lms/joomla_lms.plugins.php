<?php
if (!defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) { die( 'Restricted access' ); }

if ( !defined( '_JLMS_EXEC' ) ) { define( '_JLMS_EXEC', 1 ); }

function JLMS_pluginaction(){

	$folder = JRequest::getVar('folder', '');
	$plugin = JRequest::getVar('plugin', '');
	
	$db = & JFactory::getDBO();
	$_JLMS_PLUGINS = & JLMSFactory::getPlugins();
	
	$query = "SELECT element, folder"
	. "\n FROM #__lms_plugins"
	. "\n WHERE 1"
	. "\n AND folder = '".$folder."'"
	. "\n AND element = '".$plugin."'"
	. "\n AND published = '1'"
	;
	$db->setQuery($query);
	$plugin_in_db = $db->loadObject();
	
	$exist_in_db = isset($plugin_in_db->element) ? 1 : 0;
	
	$exist_in_dir = 0;
	if($exist_in_db && 
		file_exists(JPATH_SITE . DS . 'components' . DS . 'com_joomla_lms' . DS . 'includes' . DS . 'plugins' . DS . $plugin_in_db->folder . DS . $plugin_in_db->element.'.php') &&
		file_exists(JPATH_SITE . DS . 'components' . DS . 'com_joomla_lms' . DS . 'includes' . DS . 'plugins' . DS . $plugin_in_db->folder . DS . $plugin_in_db->element.'.xml')
	){
		$exist_in_dir = 1;
	}
	
	require_once(JPATH_SITE . DS . 'components' . DS . 'com_joomla_lms' . DS . 'includes' . DS . 'plugins' . DS . $plugin_in_db->folder . DS . $plugin_in_db->element.'.php');
	$_JLMS_PLUGINS->trigger('onPluginAction');
	
	exit();
}
JLMS_pluginaction();
