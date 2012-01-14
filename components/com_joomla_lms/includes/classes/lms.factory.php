<?php 

defined( '_JEXEC' ) or die( 'Restricted access' );

class JLMSFactory {
	function &getDB() {
		static $instance;
		if (!is_object($instance)) {
			$instance = JLMSFactory::_createDB();
		}
		return $instance;
	}

	function &getConfig() {
		static $instance;
		if (!is_object($instance)) {
			$instance = JLMSFactory::_createConfig();
		}
		return $instance;
	}

	function &getPlugins() {
		static $instance;
		if (!is_object($instance)) {
			$instance = JLMSFactory::_createPlugins();
		}
		return $instance;
	}

	function &getSession() {
		static $instance;
		if (!is_object($instance)) {
			$instance = JLMSFactory::_createSession();
		}
		return $instance;
	}

	function &getACL($user_id = 0) {
		static $instances = array();
		if (!$user_id) {
			$user = & JFactory::getUser();
			$user_id = $user->get('id');
		}
		if (isset($instances[$user_id]) && is_object($instances[$user_id])) {
		} else {
			$instances[$user_id] = JLMSFactory::_createACL($user_id);
		}
		return $instances[$user_id];
	}
	
	function &getJoomlaACL() 
	{	
		static $instance;
		
		if( !is_object($instance) ) 
		{
			if( JLMS_J16version() ) 
			{
				require_once(JPATH_SITE.DS.'components'.DS.'com_joomla_lms'.DS."includes".DS.'classes'.DS .'lms.access.16.php');
				
				$instance = new JLMSAccess();				
			} else {
				$instance = & JFactory::getACL(); 
			}
		}
		
		return $instance;
	}

	function &_createDB() {
		require_once(JPATH_SITE . DS . 'components' . DS . 'com_joomla_lms' . DS . 'includes' . DS . 'classes' . DS . 'lms.table.new.php');
		$JLMS_DB = & JFactory::getDBO();
		return $JLMS_DB;
	}

	function &_createConfig() {
		require_once(JPATH_SITE . DS . 'components' . DS . 'com_joomla_lms' . DS . "includes" . DS . "classes" . DS . "lms.config.php");
		$JLMS_CONFIG = new JLMS_Config();
		require(JPATH_SITE . DS . 'components' . DS . 'com_joomla_lms' . DS . "includes" . DS . "config.inc.php");
		return $JLMS_CONFIG;
	}

	function &_createPlugins() {
		require_once(JPATH_SITE . DS . 'components' . DS . 'com_joomla_lms' . DS . "includes" . DS . "classes" . DS . "lms.plugins.php");
		$_JLMS_PLUGINS = new jlmsPluginHandler();
		return $_JLMS_PLUGINS;
	}

	function &_createSession() {
		// SESSION class
		//TODO: implemet native Joomla 1.5 sessions)
		require_once(JPATH_SITE . DS . 'components' . DS . 'com_joomla_lms' . DS . "includes" . DS . "classes" . DS . "lms.session.php");
		$JLMS_SESSION = new JLMS_Session();
		return $JLMS_SESSION;
	}

	function &_createACL($user_id) {
		require_once(JPATH_SITE . DS . 'components' . DS . 'com_joomla_lms' . DS . 'includes' . DS . 'classes' . DS . 'lms.acl.php');
		$JLMS_DB = JLMSFactory::getDB();
		$JLMS_ACL = new JLMS_ACL( $user_id, $JLMS_DB );
		return $JLMS_ACL;
	}
	
	function &getTitles() {
		require_once(JPATH_SITE . DS . 'components' . DS . 'com_joomla_lms' . DS . 'includes' . DS . 'classes' . DS . 'lms.titles.php');
		$titles = & JLMSTitles::getInstance();
		return $titles;
	}
	
	function &getXMLParser()
	{
		$doc = null;		
		require_once(JPATH_SITE . DS . 'components' . DS . 'com_joomla_lms' . DS . 'includes' . DS . 'classes' . DS . 'lms.xml.php');	
		$doc = new JLMSXML();		
		return $doc;
	}
	
	function &getDispatcher() 
	{
		require_once(JPATH_SITE . DS . 'components' . DS . 'com_joomla_lms' . DS . 'includes' . DS . 'classes' . DS . 'lms.dispatcher.php');
		$res = & JLMSDispatcher::getChildInstance();
		return $res;
	}
	
	function getUser() 
	{
		$version = new JVersion();	
	
		if( strnatcasecmp( $version->RELEASE, '1.6' ) >= 0 ) 
		{
			$user = JFactory::getUser(); 
		} else {
			$user = & JFactory::getUser();
		}	
		
		return $user;
	}	
}
class JLMSRoute {
	function _($path) {
		return JRoute::_($path);
	}
}
?>