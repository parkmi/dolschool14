<?php

// no direct access
defined( '_VALID_MOS' ) or defined( '_JEXEC' ) or die( 'Restricted access' );

if (defined('E_STRICT')) {
	//hide "Strict Standards:" PHP warnings
	$errorlevel_original=error_reporting();
	$error_bits = array();
	$errorlevel = $errorlevel_original;
	while ($errorlevel > 0) {
		for($i = 0, $n = 0; $i <= $errorlevel; $i = 1 * pow(2, $n), $n++) {
			$end = $i;
		}
		$error_bits[] = $end;
		$errorlevel = $errorlevel - $end;
	}
	if (defined('E_STRICT') && in_array(E_STRICT, $error_bits)) {
		error_reporting($errorlevel_original ^ E_STRICT);
	}
}

//if ( defined( '_VALID_MOS' ) or defined( '_JLEGACY' ) or defined( '_BITS_LEGACY' ) )  {
	//changed by DEN (27.09.2010) - compatibility with BlueFlame framework (and other extensions who define VALID_MOS
if ( defined( '_JLEGACY' ) or defined( '_BITS_LEGACY' ) )  {
	//
} else {	
	// Define the 1.0 legacy mode constant
	define('_BITS_LEGACY', '1.0');
	
	// Set global configuration var for legacy mode
	$config = &JFactory::getConfig();
	$config->setValue('config.bitslegacy', 1);
	
	require_once(dirname(__FILE__).DS.'component.lib.php');
	
	/**
	 * Legacy define, _ISO define not used anymore. All output is forced as utf-8.
	 * @deprecated	As of version 1.5
	 */
	if (!defined('_ISO'))
		define('_ISO','charset=utf-8');
	
	/**
	 * Legacy constant, use _JEXEC instead
	 * @deprecated	As of version 1.5
	 */
	if (!defined('_VALID_MOS'))
		define( '_VALID_MOS', 1 );
	
	/**
	 * Legacy constant, use _JEXEC instead
	 * @deprecated	As of version 1.5
	 */
	if (!defined('_MOS_MAMBO_INCLUDED'))
		define( '_MOS_MAMBO_INCLUDED', 1 );
	
	/**
	 * Legacy constant, use DATE_FORMAT_LC instead
	 * @deprecated	As of version 1.5
	 */
	if (!defined('_DATE_FORMAT_LC'))
		define('_DATE_FORMAT_LC', JText::_('DATE_FORMAT_LC1') ); //Uses PHP's strftime Command Format
	
	/**
	 * Legacy constant, use DATE_FORMAT_LC2 instead
	 * @deprecated	As of version 1.5
	 */
	if (!defined('_DATE_FORMAT_LC2'))
		define('_DATE_FORMAT_LC2', JText::_('DATE_FORMAT_LC2'));
	
	/**
	 * Legacy constant, use JFilterInput instead
	 * @deprecated	As of version 1.5
	 */
	if (!defined('_MOS_NOTRIM'))
		define( "_MOS_NOTRIM", 0x0001 );
	
	/**
	 * Legacy constant, use JFilterInput instead
	 * @deprecated	As of version 1.5
	 */
	if (!defined('_MOS_ALLOWHTML'))
		define( "_MOS_ALLOWHTML", 0x0002 );
	
	/**
	 * Legacy constant, use JFilterInput instead
	 * @deprecated	As of version 1.5
	 */
	if (!defined('_MOS_ALLOWRAW'))
		define( "_MOS_ALLOWRAW", 0x0004 );
	
	/**
	 * Legacy global, use JVersion->getLongVersion() instead
	 * @name $_VERSION
	 * @deprecated	As of version 1.5
	 */
	 $GLOBALS['_VERSION']	= new JVersion();
	 $version				= $GLOBALS['_VERSION']->getLongVersion();
	

	// surveyforce - define global $database object
	if (file_exists(dirname(__FILE__).DS.'..'.DS.'..'.DS.'com_surveyforce'.DS.'surveyforce.php')) {
		$conf =& JFactory::getConfig();
		$GLOBALS['database'] = new database($conf->getValue('config.host'), $conf->getValue('config.user'), $conf->getValue('config.password'), $conf->getValue('config.db'), $conf->getValue('config.dbprefix'));
		$GLOBALS['database']->debug($conf->getValue('config.debug'));
	}

	/**
	 * Legacy global, use JFactory::getUser() [JUser object] instead
	 * @name $my
	 * @deprecated	As of version 1.5
	 */
	
	
	if (!isset($GLOBALS['my'])) {
		$user_legacy =& JFactory::getUser();

		$mygid = $user_legacy->get('aid', 0);
		$myobject = $user_legacy->getProperties();
	
		$GLOBALS['my'] = new stdClass();
	
		$GLOBALS['my']->id = $myobject['id'];
		$GLOBALS['my']->name = $myobject['name'];
		$GLOBALS['my']->username = $myobject['username'];
		$GLOBALS['my']->email = $myobject['email'];
		$GLOBALS['my']->password = $myobject['password'];
		$GLOBALS['my']->password_clear = $myobject['password_clear'];
		$GLOBALS['my']->usertype = $myobject['usertype'];
		$GLOBALS['my']->block = $myobject['block'];
		$GLOBALS['my']->sendEmail = $myobject['sendEmail'];
		$GLOBALS['my']->registerDate = $myobject['registerDate'];
		$GLOBALS['my']->lastvisitDate = $myobject['lastvisitDate'];
		$GLOBALS['my']->activation = $myobject['activation'];
		$GLOBALS['my']->params = $myobject['params'];
		$GLOBALS['my']->aid = $myobject['aid'];
		$GLOBALS['my']->guest = $myobject['guest'];
		$GLOBALS['my']->gid	= $mygid;
	}

	/**
	* Fixes the $my global if the user was restored by the remember me plugin
	*/
	$user_legacy	=& JFactory::getUser();
	if ($user_legacy->id) {
		if (isset($GLOBALS['my']) && $GLOBALS['my']->id === 0) {
			$mygid = $user_legacy->get('aid', 0);
			$myobject = $user_legacy->getProperties();
		
			$GLOBALS['my'] = new stdClass();
		
			$GLOBALS['my']->id = $myobject['id'];
			$GLOBALS['my']->name = $myobject['name'];
			$GLOBALS['my']->username = $myobject['username'];
			$GLOBALS['my']->email = $myobject['email'];
			$GLOBALS['my']->password = $myobject['password'];
			$GLOBALS['my']->password_clear = $myobject['password_clear'];
			$GLOBALS['my']->usertype = $myobject['usertype'];
			$GLOBALS['my']->block = $myobject['block'];
			$GLOBALS['my']->sendEmail = $myobject['sendEmail'];
			$GLOBALS['my']->registerDate = $myobject['registerDate'];
			$GLOBALS['my']->lastvisitDate = $myobject['lastvisitDate'];
			$GLOBALS['my']->activation = $myobject['activation'];
			$GLOBALS['my']->params = $myobject['params'];
			$GLOBALS['my']->aid = $myobject['aid'];
			$GLOBALS['my']->guest = $myobject['guest'];
			$GLOBALS['my']->gid	= $mygid;
		}
	}


	
	/**
	 * Insert configuration values into global scope (for backwards compatibility)
	 * @deprecated	As of version 1.5
	 */
	
	$temp = new JConfig;
	foreach (get_object_vars($temp) as $k => $v) {
		$name = 'mosConfig_'.$k;
		$GLOBALS[$name] = $v;
	}
	
	$usersConfig = &JComponentHelper::getParams( 'com_users' ); 
	
	$GLOBALS['mosConfig_allowUserRegistration'] = $usersConfig->get('allowUserRegistration');
	$GLOBALS['mosConfig_useractivation'] = $usersConfig->get('useractivation');
	$GLOBALS['mosConfig_frontend_userparams'] =$usersConfig->get('frontend_userparams');
	
	$GLOBALS['mosConfig_live_site']		= substr_replace(JURI::root(), '', -1, 1);
	$GLOBALS['mosConfig_absolute_path']	= JPATH_SITE;
	$GLOBALS['mosConfig_cachepath']	= JPATH_BASE.DS.'cache';
	
	$GLOBALS['mosConfig_offset_user']	= 0;
	
	
	$config->setValue('config.live_site', 		$GLOBALS['mosConfig_live_site']);
	$config->setValue('config.absolute_path', 	$GLOBALS['mosConfig_absolute_path']);
	//$config->setValue('config.lang', 			$GLOBALS['mosConfig_lang']);
	
	/**
	 * Legacy global, use JFactory::getUser() instead
	 * @name $acl
	 * @deprecated	As of version 1.5
	 */
/*	$acl =& JFactory::getACL();
	
	// Legacy ACL's for backward compat
	$acl->addACL( 'administration', 'edit', 'users', 'super administrator', 'components', 'all' );
	$acl->addACL( 'administration', 'edit', 'users', 'administrator', 'components', 'all' );
	$acl->addACL( 'administration', 'edit', 'users', 'super administrator', 'user properties', 'block_user' );
	$acl->addACL( 'administration', 'manage', 'users', 'super administrator', 'components', 'com_users' );
	$acl->addACL( 'administration', 'manage', 'users', 'administrator', 'components', 'com_users' );
	$acl->addACL( 'administration', 'config', 'users', 'super administrator' );
	//$acl->addACL( 'administration', 'config', 'users', 'administrator' );
	
	$acl->addACL( 'action', 'add', 'users', 'author', 'content', 'all' );
	$acl->addACL( 'action', 'add', 'users', 'editor', 'content', 'all' );
	$acl->addACL( 'action', 'add', 'users', 'publisher', 'content', 'all' );
	$acl->addACL( 'action', 'edit', 'users', 'author', 'content', 'own' );
	$acl->addACL( 'action', 'edit', 'users', 'editor', 'content', 'all' );
	$acl->addACL( 'action', 'edit', 'users', 'publisher', 'content', 'all' );
	$acl->addACL( 'action', 'publish', 'users', 'publisher', 'content', 'all' );
	
	$acl->addACL( 'action', 'add', 'users', 'manager', 'content', 'all' );
	$acl->addACL( 'action', 'edit', 'users', 'manager', 'content', 'all' );
	$acl->addACL( 'action', 'publish', 'users', 'manager', 'content', 'all' );
	
	$acl->addACL( 'action', 'add', 'users', 'administrator', 'content', 'all' );
	$acl->addACL( 'action', 'edit', 'users', 'administrator', 'content', 'all' );
	$acl->addACL( 'action', 'publish', 'users', 'administrator', 'content', 'all' );
	
	$acl->addACL( 'action', 'add', 'users', 'super administrator', 'content', 'all' );
	$acl->addACL( 'action', 'edit', 'users', 'super administrator', 'content', 'all' );
	$acl->addACL( 'action', 'publish', 'users', 'super administrator', 'content', 'all' );
	
	$acl->addACL( 'com_syndicate', 'manage', 'users', 'super administrator' );
	$acl->addACL( 'com_syndicate', 'manage', 'users', 'administrator' );
	$acl->addACL( 'com_syndicate', 'manage', 'users', 'manager' );
	
	$GLOBALS['acl'] =& $acl;
	*/
	/**
	 * Legacy global
	 * @name $task
	 * @deprecated	As of version 1.5
	 */
	$task = JRequest::getString('task'); 
	if( !$task ) 
	{
		$task = JRequest::getString('view');
		
		if( $task )
			JRequest::setVar('task', $task );
	}	
		
	$GLOBALS['task'] = $task;
	
	
	/**
	 *  Legacy global
	 * 	use JApplicaiton->registerEvent and JApplication->triggerEvent for event handling
	 *  use JPlugingHelper::importPlugin to load bot code
	 *  @deprecated As of version 1.5
	 */
/*	$GLOBALS['_MAMBOTS'] = new mosMambotHandler();
	
	$mosmsg = JRequest::getVar( 'mosmsg' );
	$mainframe->enqueueMessage( $mosmsg );*/
}

 
if (class_exists('jeditor')) { null; } elseif (class_exists('JLoader')) { JLoader::register('JEditor' , JPATH_LIBRARIES.DS.'joomla'.DS.'html'.DS.'editor.php');JLoader::load('JEditor'); }
if (class_exists('jobject')) { null; }
if (class_exists('jrequest')) { null; }
if (class_exists('jresponse')) { null; }
if (class_exists('jfactory')) { null; }
if (class_exists('jversion')) { null; }
if (class_exists('jerror')) { null; }
if (class_exists('jexception')) { null; }
if (class_exists('jarrayhelper')) { null; }
if (class_exists('jfilterinput')) { null; }
if (class_exists('jfilteroutput')) { null; }
if (class_exists('jtext')) { null; }
if (class_exists('jroute')) { null; }
if (class_exists('jmenu')) { null; }
if (class_exists('juser')) { null; }
if (class_exists('juri')) { null; }
if (class_exists('jhtml')) { null; }
if (class_exists('jparameter')) { null; }
if (class_exists('jutility')) { null; }
if (class_exists('jevent')) { null; }
if (class_exists('jdispatcher')) { null; }
if (class_exists('jlanguage')) { null; }
if (class_exists('jstring')) { null; }
if (class_exists('jtoolbar')) { null; }
if (class_exists('japplication')) { null; }
if (class_exists('japplicationhelper')) { null; }
if (class_exists('jcomponenthelper')) { null; }
if (class_exists('jregistry')) { null; }
if (class_exists('jregistryformat')) { null; }
if (class_exists('jsession')) { null; }
if (class_exists('jsessionstorage')) { null; }
if (class_exists('jdatabase')) { null; }
if (class_exists('jtable')) { null; }
if (class_exists('jpath')) { null; }
if (class_exists('jelement')) { null; }
if (class_exists('jfolder')) { null; }
if (class_exists('jpluginhelper')) { null; }
if (class_exists('jplugin')) { null; }
if (class_exists('jobserver')) { null; }
if (class_exists('jobservable')) { null; }
if (class_exists('jsimplexml')) { null; }
if (class_exists('jpanetabs')) { null; }
if (class_exists('mosadminmenus')) { null; }
if (class_exists('moscache')) { null; }
if (class_exists('moscategory')) { null; }
if (class_exists('moscommonhtml')) { null; }

if (class_exists('moscontent')) { null; }
if (class_exists('mosdbtable')) { null; }
if (class_exists('moshtml')) { null; }
if (class_exists('mosinstaller')) { null; }
if (class_exists('mosmainframe')) { null; }
if (class_exists('mosmambothandler')) { null; }
if (class_exists('mosmenu')) { null; }
if (class_exists('mosmodule')) { null; }
if (class_exists('mosparameters')) { null; }
if (class_exists('patfactory')) { null; }
if (class_exists('mosprofiler')) { null; }
if (class_exists('mossection')) { null; }
if (class_exists('mossession')) { null; }
if (class_exists('mostoolbar')) { null; }
if (class_exists('mosuser')) { null; }
if (class_exists('database')) { null; }
if (class_exists('jdatabasemysql')) { null; }
if (class_exists('jauthorization')) { null; }
if (class_exists('jdocument')) { null; }
if (class_exists('jdocumentrenderer')) { null; }
if (class_exists('jmodulehelper')) { null; }
if (class_exists('jtablecategory')) { null; }
if (class_exists('jtablecontent')) { null; }
if (class_exists('jinstaller')) { null; }
if (class_exists('jfile')) { null; }
if (class_exists('jarchive')) { null; }
if (class_exists('jtablemenu')) { null; }
if (class_exists('jtoolbarhelper')) { null; }
if (class_exists('jtablemodule')) { null; }
if (class_exists('jprofiler')) { null; }
if (class_exists('jtablesection')) { null; }
if (class_exists('jtablesession')) { null; }
if (class_exists('jtableuser')) { null; }
if (class_exists('jdatabasemysqli')) { null; }

?>