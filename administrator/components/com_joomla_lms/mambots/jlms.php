<?php
defined( '_JEXEC' ) or die( 'Restricted access' );

if ( !defined( '_JLMS_EXEC' ) ) { define( '_JLMS_EXEC', 1 ); }

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

if (file_exists(JPATH_SITE.DS.'components'.DS.'com_joomla_lms'.DS.'includes'.DS.'forums'.DS.'smf'.DS.'smf.php')) {
	require_once( JPATH_SITE.DS.'components'.DS.'com_joomla_lms'.DS.'includes'.DS.'forums'.DS.'smf'.DS.'smf.php');
}

class plgSystemjlms extends JPlugin
{
	function plgSystemjlms(& $subject, $config) {
		parent::__construct($subject, $config);
	}	

	function onAfterRoute() {		
		$database = JFactory::getDBO();	
					
		$task = JRequest::getVar('task', '');
		$option = JRequest::getVar('option', '');
		$id = JRequest::getInt('id');
				
		$version = new JVersion();
		$app = & JFactory::getApplication();		
		
		$loginTasks = array('login', 'user.login');
		$logoutTasks = array('logout', 'user.logout');
		$editTasks = array('saveUserEdit', 'saveregisters', 'profile.save', 'registration.register', 'register_save', 'user.save', 'user.apply', 'save', 'apply'  );
		
		if( strnatcasecmp( $version->RELEASE, '1.7' ) >= 0 ) 
		{
			$jVersion = 17;
		} else if( strnatcasecmp( $version->RELEASE, '1.6' ) >= 0 ) 
		{
			$jVersion = 16;
		} else {
			$jVersion = 15;
		}
		
		if (file_exists(JPATH_SITE.DS.'components'.DS.'com_joomla_lms'.DS."includes".DS."classes".DS."lms.factory.php")) {
			require_once(JPATH_SITE.DS.'components'.DS.'com_joomla_lms'.DS."includes".DS."classes".DS."lms.factory.php");
		} else {
			//JoomlaLMS system files not found
			return;
		}
		if (!file_exists(JPATH_SITE.DS.'components'.DS.'com_joomla_lms'.DS.'includes'.DS.'forums'.DS.'smf'.DS.'smf.php')) {
			//JoomlaLMS system files not found
			return;	
		}
		if (!class_exists('JLMSFactory')) {
			//JoomlaLMS system class not found
			return;
		}
		
		jimport('joomla.user.helper');
	
		$JLMS_CONFIG = JLMSFactory::getConfig();		
					
		/* admin language for JoomlaLMS BackEnd menu */
		$app = & JFactory::getApplication();
		if ($app->isAdmin()) {		
			$language = $JLMS_CONFIG->get('default_language');
			$lang_path = JPATH_SITE . DS . "administrator" . DS . "components" . DS . "com_joomla_lms";	
			$lang = & JFactory::getLanguage();
						
			$oldLang = $lang->setDefault('english');
			if ( $jVersion >= 16) 
			{				
				$lang->load( 'com_joomla_lms.sys', $lang_path, $language );			
			} else {
				$lang->load( 'admin.menu.lang', $lang_path, $language );
			}			
			$lang->setDefault( $oldLang );
		}
		/* end of admin language */
		
		$user_id = 0;
		
		if( in_array($task, $loginTasks ) )	
		{				
			$username = JRequest::getVar('username', '', 'post', 'username');
			$password = JRequest::getVar('password', '', 'post', 'string', JREQUEST_ALLOWRAW);	
			
			if( !$username ) return false;
			
			if( !$password )
					$password = JRequest::getVar('passwd', '', 'post', 'string', JREQUEST_ALLOWRAW);
							
			$query = 'SELECT * '
				. ' FROM `#__users`'
				. ' WHERE username=' . $database->Quote( $username )
				;
			$database->setQuery( $query );
			$result = $database->loadObject();
			
			if( $result )
			{
				$parts	= explode( ':', $result->password );
				$crypt	= $parts[0];
				$salt	= @$parts[1];
				$testcrypt = JUserHelper::getCryptedPassword($password, $salt);
	
				if ($crypt == $testcrypt) {
					$juser = $result;				
				} 
			}
			
			if( isset($juser->id) ){
				$user_id = $juser->id;			
			}
		} else {
			$user = & JFactory::getUser();
			$user_id = ($id)?$id:$user->get('id');
		}
		
		//echo '<pre>';
		//print_r($juser);
		//echo '</pre>';
		//die;
		
		$do_synchronize = false;			
		
		if ($this->params->get('synch_all_users', 1)) {
			$do_synchronize = true;
		} else {
			if( !$user_id ) return false;
			
			$query = "SELECT user_id FROM #__lms_users WHERE user_id = ".$user_id;
			$database->setQuery($query);
			$lms_user_id = $database->LoadResult();
			if (!$lms_user_id) {
				$query = "SELECT user_id FROM #__lms_user_courses WHERE user_id = ".$user_id;
				$database->setQuery($query);
				$lms_user_id = $database->LoadResult();
				if (!$lms_user_id) {
					$query = "SELECT user_id FROM #__lms_users_in_groups WHERE user_id = ".$user_id;
					$database->setQuery($query);
					$lms_user_id = $database->LoadResult();
				}
			}
			
			if (!$lms_user_id) {
				$do_synchronize = false;
			} else {
				$do_synchronize = true;
			}
		}	
					
		if ( $JLMS_CONFIG->get('plugin_forum') && $option && $do_synchronize ) {					
						
			if( in_array($option, array('com_users', 'com_user', 'com_comprofiler')) && in_array($task, $loginTasks) ){					
				$forum = & JLMS_SMF::getInstance();				
						
				if ( is_object( $forum ) ){									
					//require_once ( JPATH_SITE.'/components/com_joomla_lms/includes/jlms_reg_forum.php');					
					if ( isset($juser) && is_object( $juser ) ) {
						if (isset($juser->id)){
							//----> CB section
							$query = "SELECT lms_config_var, lms_config_value FROM `#__lms_config` WHERE lms_config_var LIKE '%_cb_%' ";
							$database->setQuery($query);
							$configs = $database->loadObjectList();
	
							$cb_values = array();
							foreach($configs as $cb_value){
								$cb_values[$cb_value->lms_config_var] = $cb_value->lms_config_value;
							}
							$cb_info = array();
							if ($cb_values['is_cb_installed']) {
								$fields = array ('website', 'ICQ', 'AIM', 'YIM','MSN', 'location');
								$fields_isset = array();
								foreach ($fields as $field) {
									if (isset($cb_values['jlms_cb_'.$field]) && $cb_values['jlms_cb_'.$field]) {
										$fields_isset[] = $cb_values['jlms_cb_'.$field];
									}
								}
								if (!empty($fields_isset)) {
									$fields_str = implode(',', $fields_isset );
									$query = "SELECT name FROM `#__comprofiler_fields` WHERE fieldid IN ($fields_str) ";
									$database->setQuery($query);
									$field_name = $database->loadResultArray();
									$field_names = implode(',', $field_name);
	
									$query = "SELECT ".$field_names." FROM `#__comprofiler` WHERE user_id=".$juser->id;
									$database->setQuery($query);
									$cb_user = $database->loadResultArray();
									if ( isset($cb_user[0]) ) {
										$cb_info = array_values( $cb_user );
									}
								}
							}
	
							$groups = '';												
							$smf_user = $forum->loadMemberByName( $username );
																																			
							if (is_object($smf_user) && isset($smf_user->id_member)){							
								$mem_id = $smf_user->id_member;							
							} else {								
								$mem_id = $forum->registerOnForum( $juser, $password, $groups, $cb_info);
							}							 
													
							$forum->setLoginCookie15( $mem_id, $password );							
						}
					}
				}
			}
			elseif ( in_array($option, array('com_users', 'com_user', 'com_comprofiler')) && in_array($task, $logoutTasks ) )
			{
				$forum = & JLMS_SMF::getInstance();
				if ( is_object( $forum ) )
				{
					$cookiename = $forum->getCookieName();	
					$parts = $forum->urlParts();
																						
					if( $parts[0]  == 'localhost' ) 
					{						
						setcookie( $cookiename, 0, time()-3600, $parts[1] );
					} else {					
						setcookie( $cookiename, 0 , time()-3600, $parts[1], $parts[0] );
					}
				}
			}
			elseif ( 
				( in_array($option, array('com_users', 'com_user', 'com_comprofiler')) && in_array($task, $editTasks ) )
			) {										
					if( !$id ) return false;
													
					$forum = & JLMS_SMF::getInstance();
					if  ( is_object( $forum ) ) {								
						
						$loginForm = JRequest::getVar( 'jform', array(), 'default', 'array');
						
						$verifyPass = JRequest::getVar('password__verify', '', 'post', 'string', JREQUEST_ALLOWRAW);
						$isCB = (!$verifyPass)?false:true;						
						
						if( $isCB ) 
						{
							$email = JRequest::getVar('email');						
							$username = JRequest::getVar('username');
							$name = JRequest::getVar('name');												
							$postPass = JRequest::getVar('password', '', 'post', 'string', JREQUEST_ALLOWRAW);							
						} else if( $jVersion > 15  ) {													
							$username = $loginForm['username'];
							$name = $loginForm['name'];						
							
							if( $app->isAdmin() ) 
							{
								$email = $loginForm['email'];
								$postPass = $loginForm['password'];
							} else {
								$email = $loginForm['email1'];
								$postPass = $loginForm['password1'];
							}
							
							$verifyPass = $loginForm['password2']; 
						} else {													
							$email = JRequest::getVar('email');						
							$username = JRequest::getVar('username');
							$name = JRequest::getVar('name');
							$postPass = JRequest::getVar('password', '', 'post', 'string', JREQUEST_ALLOWRAW);
							$verifyPass = JRequest::getVar('password2', '', 'post', 'string', JREQUEST_ALLOWRAW);							
						}																										
						
						//$pass=',';
						$password = '';
																	
						if( !preg_match( "/.+@.+\..+/", $email ) ) {							
							return false;
						}										
						
						if( $username ) 
						{
							if( $verifyPass ) 
							{
								if( $verifyPass == $postPass ) {								
									$password = $forum->password( $username, $postPass );
									//$pass = ", passwd='".$password."', ";
								} else {								
									return false;
								}
							}
						} 				
											
						if( $id ) 
						{
							$query = "SELECT username FROM `#__users` WHERE id='".$id."'";
							$database->setQuery ($query);
							$database->query();
							$usernameOld = $database->loadResult();										
							$smf_user = $forum->loadMemberByName( $usernameOld );																	
							$userid_forum = $smf_user->id_member;
							
							$storeData = get_object_vars($smf_user);						
						}
						
						$storeData['id_member'] = $userid_forum;
						$storeData['member_name'] = $username;
						$storeData['email_address'] = $email;
						$storeData['real_name'] = $name;
						if( $password )
							$storeData['passwd'] = $password;												
	
						$forum->storeMember( $storeData );
	
						if ($password)
						{
							$forum->setLoginCookie15( $userid_forum, $post_password );
						}								
					}			
			} 
		}
	}
}
?>