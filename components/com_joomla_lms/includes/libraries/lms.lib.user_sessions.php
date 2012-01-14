<?php
/**
* lms.lib.user_sessions.php
* Joomla LMS Component
* * * ElearningForce Inc.
**/

// no direct access
defined( '_JLMS_EXEC' ) or die( 'Restricted access' );

class JLMS_UserSessions_html {
	function login_form($option, $task = 'jlms_login', $course_id = 0){
		global $Itemid;
		
		$lang =& JFactory::getLanguage();
		
		$lang->load("mod_login");				
		$validate 	= josSpoofValue(1);		
		?>
		<form method="post" name="JLMS_loginForm" id="form-login" action="index.php?option=com_joomla_lms">
		<?php
		if( JLMS_J16version() ) {
		?>	
			<div class="login-fields">		
					<label for="mod_login_username">
						<?php echo JText::_('MOD_LOGIN_VALUE_USERNAME'); ?>
					</label>
					<input name="username" id="mod_login_username" type="text" class="inputbox" alt="username" size="10" />
			</div>
			<div class="login-fields">				
					<label for="mod_login_password">
						<?php echo JText::_('JGLOBAL_PASSWORD'); ?>
					</label>					
					<input type="password" id="mod_login_password" name="passwd" class="inputbox" size="10" alt="password" />
			</div>
			<div class="login-fields">
			<input type="checkbox" name="remember" id="mod_login_remember" class="inputbox" value="yes" alt="Remember Me" />
			<label for="mod_login_remember">
				<?php echo JText::_('MOD_LOGIN_REMEMBER_ME'); ?>
			</label>
			</div>
			<div class="login-fields">
			<a href="<?php echo sefRelToAbs( JLMS_UserSessions_html::getLostPasswordLink() ); ?>">
				<?php echo JText::_('MOD_LOGIN_FORGOT_YOUR_PASSWORD'); ?></a>
			</div>	
			<input type="submit" name="login_to_joomla" class="button" onclick="jlms_submitlogin('<?php echo $task;?>')" value="<?php echo JText::_('JLOGIN'); ?>" />
		<?php } else { ?>
			<fieldset class="input jlmsloginform">
			<p id="form-login-username">					
					<label for="mod_login_username">
						<?php echo JText::_('Username'); ?>
					</label>
					<br />
					<input name="username" id="mod_login_username" type="text" class="inputbox" alt="username" size="18" />
			</p>
			<p id="form-login-password">				
					<label for="mod_login_password">
						<?php echo JText::_('Password'); ?>
					</label>
					<br />					
					<input type="password" id="mod_login_password" name="passwd" class="inputbox" size="18" alt="password" />
			</p>
			<p id="form-login-remember">
			<label for="mod_login_remember">
				<?php echo  JText::_('Remember me'); ?>
			</label>			
			<input type="checkbox" name="remember" id="mod_login_remember" class="inputbox" value="yes" alt="Remember Me" />
			</p>
			<input type="submit" name="login_to_joomla" class="button" onclick="jlms_submitlogin('<?php echo $task;?>')" value="<?php echo JText::_('LOGIN'); ?>" />
			</fieldset>	
			<ul>
				<li>	
				<a href="<?php echo sefRelToAbs( JLMS_UserSessions_html::getLostPasswordLink() ); ?>">
					<?php echo JText::_('FORGOT_YOUR_PASSWORD'); ?></a>
				</li>
			</ul>			
		<?php } ?>	
		<input type="hidden" name="from_course_enrollment" value="<?php echo $course_id; ?>" />
		<input type="hidden" name="option" value="<?php echo $option?>" />
		<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
		<input type="hidden" name="task" value="" />	
		<input type="hidden" name="op2" value="login" />		
		<input type="hidden" name="force_session" value="1" />
		<input type="hidden" name="<?php echo $validate; ?>" value="1" />
		</form>
		
		<?php
	}
	
	function getLostPasswordLink() 
	{	
		global $JLMS_CONFIG;
		
		$db = & JFactory::getDBO();	
				
		if( JLMS_J16version() ) 
		{				
			if( $JLMS_CONFIG->get('is_cb_installed') ) 
			{
				$link = 'index.php?option=com_comprofiler&task=lostpassword';
			} else {				
				$link = 'index.php?option=com_users&view=reset';
			}
				
			$user = & JFactory::getUser();
			$groups = $user->getAuthorisedViewLevels();			
			$groups[] = 0;					  			 
			
			$query = 'SELECT id FROM #__menu WHERE type = \'component\' AND LCASE(link) LIKE LCASE(\'%'.$link.'\') AND published = 1 AND access IN ('.implode( ',',$groups ).')';
			$db->setQuery( $query );			
			$Itemid = $db->loadResult();						 	
			
			if( !$Itemid ) 
			{			
				if( strpos( $link, 'option=com_comprofiler' ) ) 
				{
					$query = 'SELECT id FROM #__menu WHERE type = \'component\' AND LCASE(link) LIKE LCASE(\'%option=com_comprofiler&task=registers\') AND published = 1 AND access IN ('.implode( ',',$groups ).')';
					$db->setQuery( $query );			
					$Itemid = $db->loadResult();
					
					if( !$Itemid ) 
					{
						$query = 'SELECT id FROM #__menu WHERE type = \'component\' AND LCASE(link) LIKE LCASE(\'%option=com_comprofiler&task=usersList\') AND published = 1 AND access IN ('.implode( ',',$groups ).')';
						$db->setQuery( $query );			
						$Itemid = $db->loadResult();
					}					
				} else {
					$query = 'SELECT id FROM #__menu WHERE type = \'component\' AND LCASE(link) LIKE LCASE(\'%option=com_users&view=profile\') AND published = 1 AND access IN ('.implode( ',',$groups ).')';
					$db->setQuery( $query );			
					$Itemid = $db->loadResult();
					
					if( !$Itemid ) 
					{
						$query = 'SELECT id FROM #__menu WHERE type = \'component\' AND LCASE(link) LIKE LCASE(\'option=com_users&view=registration\') AND published = 1 AND access IN ('.implode( ',',$groups ).')';
						$db->setQuery( $query );			
						$Itemid = $db->loadResult();
					}
				}
			}	
					
		} else {
			
			if( $JLMS_CONFIG->get('is_cb_installed') ) 
			{
				$link = 'index.php?option=com_comprofiler&task=lostPassword';
			} else {				
				$link = 'index.php?option=com_user&view=reset';
			}
			
			$user = & JFactory::getUser();
			$gid = $user->get('gid');
						
			$query = 'SELECT id FROM #__menu WHERE LCASE(link) LIKE LCASE(\'%'.$link.'\') AND published = 1 AND access <= '.$gid;
			$db->setQuery( $query );			
			$Itemid = $db->loadResult();
			
			if( !$Itemid ) 
			{			
				if( strpos( $link, 'option=com_comprofiler' ) ) 
				{
					$query = 'SELECT id FROM #__menu WHERE LCASE(link) LIKE LCASE(\'%option=com_comprofiler&task=registers\') AND published = 1 AND access <= '.$gid;
					$db->setQuery( $query );			
					$Itemid = $db->loadResult();
					
					if( !$Itemid ) 
					{
						$query = 'SELECT id FROM #__menu WHERE LCASE(link) LIKE LCASE(\'%option=com_comprofiler&task=usersList\') AND published = 1 AND access <= '.$gid;
						$db->setQuery( $query );			
						$Itemid = $db->loadResult();
					}					
				} else {
					$query = 'SELECT id FROM #__menu WHERE LCASE(link) LIKE LCASE(\'%option=com_users&view=profile\') AND published = 1 AND access <= '.$gid;
					$db->setQuery( $query );			
					$Itemid = $db->loadResult();
					
					if( !$Itemid ) 
					{
						$query = 'SELECT id FROM #__menu WHERE LCASE(link) LIKE LCASE(\'option=com_users&view=registration\') AND published = 1 AND access <= '.$gid;
						$db->setQuery( $query );			
						$Itemid = $db->loadResult();
					}
				}
			}					
		}
		
		if( $Itemid )		
			return $link.'&Itemid='.$Itemid;
		else
			return $link;		
	}

	function register_form($option, $custom_task = ''){
		global $Itemid, $JLMS_CONFIG;
		global $ueConfig;
				
		$usersConfig	=	&JComponentHelper::getParams( 'com_users' );
		$allowUserRegistration	=	$usersConfig->get('allowUserRegistration');
		
		if( !$allowUserRegistration && !$ueConfig['reg_admin_allowcbregistration'] ) 
		{
			return '';	
		}
		
		$doc = & JFactory::getDocument();

		$page_title = $doc->getTitle();
		
		$is_cb = $JLMS_CONFIG->get('is_cb_installed');		

		$validate 	= josSpoofValue(1);

$one_more_string_to_replace = 
'    <tr>
      <td colspan="2" width="100%"><div class="componentheading">Registration</div></td>
    </tr>';
$one_more_string_to_replace2 = 
'		<div class="componentheading">
			Registration		</div>';

		if ($is_cb) {			
			//ob_start();
			require_once($JLMS_CONFIG->get('absolute_path').'/components/com_comprofiler/comprofiler.html.php');
			require_once($JLMS_CONFIG->get('absolute_path').'/components/com_comprofiler/comprofiler.php');
			//@ob_end_clean();

			ob_start();
			registerForm( 'com_joomla_lms', isset( $ueConfig['emailpass'] ) ? $ueConfig['emailpass'] : '0', '&nbsp;');
			$form = ob_get_contents();
			ob_end_clean();
			
/*SoulPowerUniversity_MOD*/			
			$form = str_replace("index.php?option=com_comprofiler", "index.php?option=com_comprofiler&vc=1", $form);
/*SoulPowerUniversity_MOD*/
			
			$form = str_replace('saveregisters', $custom_task.'_cb', $form);			
			$form = str_replace('"com_comprofiler"', '"com_joomla_lms"', $form);
			$form = str_replace('</form>', '<input type="hidden" value="'.$Itemid.'" name="Itemid"></form>', $form);			
			$form = str_replace($one_more_string_to_replace, '', $form);
			
			echo $form;
			if ($ueConfig['reg_admin_approval'] || $ueConfig['reg_confirmation']){
			} else {
			?>
			<script type="text/javascript" language="javascript">
			<!--
			var cb_form = document.adminForm;
			cb_form.option.value = 'com_joomla_lms';
			-->	
			</script>
			<?php
			}
		} else if( $allowUserRegistration ) {
			if (JLMS_Jversion() == 2) {				
				if( JLMS_J16version() ) 
				{					
					$lang =& JFactory::getLanguage();
					$lang->load( 'com_users');
				
					require_once($JLMS_CONFIG->get('absolute_path').'/components/com_users/controller.php');
					jimport('joomla.form.form');
															 
					JForm::addFormPath( JPATH_SITE.DS.'components'.DS.'com_users'.DS.'models'.DS.'forms' );
					JForm::addFieldPath( JPATH_SITE.DS.'components'.DS.'com_users'.DS.'models'.DS.'fields' );
					
					$usersConfig = array( 
						'base_path' => $JLMS_CONFIG->get('absolute_path').DS.'components'.DS.'com_users'						 
					);
					
					// Create the controller
					$usersController = UsersController::getInstance( 'Users', $usersConfig );				
					$usersView = $usersController->getView( 'registration', $doc->getType() );					
					$usersView->addTemplatePath( JPATH_SITE.DS.'components'.DS.'com_users'.DS.'views'.DS.'registration'.DS.'tmpl' );				
							
					$oldView =  JRequest::getCmd('view');
					JRequest::setVar( 'view', 'registration' );					
					ob_start();						
					$usersController->display();									
					$form = ob_get_contents();
					ob_end_clean();	
					JRequest::setVar( 'view', $oldView );					
					
					$form = str_replace('"com_users"', 'com_joomla_lms', $form);					
					$form = str_replace('registration.register', $custom_task, $form);
					$form = str_replace(JRoute::_( 'index.php?option=com_users' ), $JLMS_CONFIG->get('live_site')."/index.php?option=com_joomla_lms&Itemid=".$Itemid, $form);					
					
				} else {	
					$lang =& JFactory::getLanguage();
					$lang->load( 'com_user');
				
					require_once($JLMS_CONFIG->get('absolute_path').'/components/com_user/controller.php');
					require_once($JLMS_CONFIG->get('absolute_path').'/components/com_user/views/register/view.html.php');
					// Create the controller
					$controller = new UserController();
					// Perform the Request task
					$controller->_basePath = $JLMS_CONFIG->get('absolute_path').'/components/com_user';
					ob_start();
					$controller->execute( 'register');
					$form = ob_get_contents();
					ob_end_clean();
					
					$form = str_replace('"com_user"', 'com_joomla_lms', $form);
					$form = str_replace('register_save', $custom_task, $form);	
					$form = str_replace(JRoute::_( 'index.php?option=com_user' ), $JLMS_CONFIG->get('live_site')."/index.php?option=com_joomla_lms&Itemid=".$Itemid, $form);					
				}				
				
				$form = str_replace($one_more_string_to_replace, '', $form);
				echo $form;
			} 
		}
		$doc->setTitle($page_title);
	}
	
	function loginPanel( $course_id = 0, $calledFrom = 'cart' ) 
	{	
		global $JLMS_CONFIG; 	
		$usersConfig = &JComponentHelper::getParams( 'com_users' );
		$allowUserRegistration = $usersConfig->get('allowUserRegistration');		
		?>
		<script type="text/javascript" language="javascript">
			<!--
			function form_viewer(task){
				var show_reg_form = document.getElementById('show_reg_form');
				var show_login_form = document.getElementById('show_login_form');
				var show_log = document.getElementById('show_log');
				var show = document.getElementById('show');
				if (task == 'reg'){
					show_login_form.style.display = 'none';
					show_reg_form.style.display = '';
					show_log.style.display = '';
					show.style.display = 'none';
				}
				if (task == 'log'){
					show_login_form.style.display = '';
					show_reg_form.style.display = 'none';
					show_log.style.display = 'none';
					show.style.display = '';
				} 
			}
			function jlms_submitlogin( pressbutton ) {
				var form = document.JLMS_loginForm;
				form.task.value = pressbutton;			
				form.submit();			
			}
			-->
		</script>		
		<?php	 		
		
		if( JLMS_J16version() ) {
			?>												
				<h3><a style="cursor:pointer" onclick="javascript:form_viewer('log')"><?php echo _JLMS_PLEASE_LOGIN; ?></a></h3>											
				<h4><a onclick="javascript:form_viewer('log')" id="show_log" style="cursor:pointer;display:none;"><?php echo _JLMS_SHOW_LOGIN;?></a></h4>							
				<div id="show_login_form">			
						<?php JLMS_UserSessions_html::login_form( 'com_joomla_lms', $calledFrom.'_login', $course_id); ?>
				</div>				
				<?php if ( $allowUserRegistration ) { ?>
				
												
				<h3><a style="cursor:pointer" onclick="javascript:form_viewer('reg')" ><?php echo _JLMS_PLEASE_REGISTER; ?></a></h3>					
				<h4><a style="cursor:pointer" onclick="javascript:form_viewer('reg')" id="show"><?php echo _JLMS_SHOW_REGISTRATION;?></a></h4>						
				<div id="show_reg_form"  style="display:none;">				
							<?php
								if ( $allowUserRegistration ) {
									ob_start();
									JLMS_UserSessions_html::register_form( 'com_joomla_lms', $calledFrom.'_register' );
									$cont = ob_get_contents();
									$cont = str_replace("componentheading","contentheading", $cont);
									ob_end_clean();
									echo $cont;
								} else {
									echo _JLMS_REGISTRATION_DISABLED;
								}
							?>				
				</div>							
			<?php
			}
		} else {
			?>
			<table width="100%" style="width:100%" cellpadding="0" cellspacing="0" border="0" class="contentpane">		
			<tr>
					<<?php echo $JLMS_CONFIG->get('tableheader_tag', 'td');?> class="sectiontableheader">			
					<a style="cursor:pointer" onclick="javascript:form_viewer('log')"><?php echo _JLMS_PLEASE_LOGIN; ?></a>
					</<?php echo $JLMS_CONFIG->get('tableheader_tag', 'td');?>>
					<<?php echo $JLMS_CONFIG->get('tableheader_tag', 'td');?> align="right" class="sectiontableheader">
					<a onclick="javascript:form_viewer('log')" id="show_log" style="cursor:pointer;display:none;"><?php echo _JLMS_SHOW_LOGIN;?></a>
					</<?php echo $JLMS_CONFIG->get('tableheader_tag', 'td');?>>
				</tr>
				<tr id="show_login_form">
					<td colspan="2">
						<table>
							<tr>
								<td>
								<?php JLMS_UserSessions_html::login_form( 'com_joomla_lms', $calledFrom.'_login', $course_id ); ?>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		<?php if ( $allowUserRegistration ) { ?>
		<table width="100%" style="width:100%" cellpadding="0" cellspacing="0" border="0" class="contentpane">		
			<tr>
				<<?php echo $JLMS_CONFIG->get('tableheader_tag', 'td');?> class="sectiontableheader">
				<a style="cursor:pointer" onclick="javascript:form_viewer('reg')" ><?php echo _JLMS_PLEASE_REGISTER; ?></a>
				</<?php echo $JLMS_CONFIG->get('tableheader_tag', 'td');?>>
				<<?php echo $JLMS_CONFIG->get('tableheader_tag', 'td');?> align="right" class="sectiontableheader">
				<a style="cursor:pointer" onclick="javascript:form_viewer('reg')" id="show"><?php echo _JLMS_SHOW_REGISTRATION;?></a>
				</<?php echo $JLMS_CONFIG->get('tableheader_tag', 'td');?>>
			</tr>
			<tr style="display:none;" id="show_reg_form">
				<td colspan="2">
					<?php
						if ( $allowUserRegistration ) {
							ob_start();
							JLMS_UserSessions_html::register_form( 'com_joomla_lms', $calledFrom.'_register' );
							$cont = ob_get_contents();
							$cont = str_replace("componentheading","contentheading", $cont);
							ob_end_clean();
							echo $cont;
						}else{
							echo _JLMS_REGISTRATION_DISABLED;
						}
					?>
				</td>
			</tr>
		</table>
			<?php
			}
		}
	}
}

class JLMS_UserSessions {
	function doLogin($username,  $password) {
		global $JLMS_CONFIG, $JLMS_DB;
		if ($JLMS_CONFIG->get('is_cb_installed', 0)) {
			$query = "SELECT * "
			. "\n FROM #__users u, "
			. "\n #__comprofiler ue "
			. "\n WHERE u.username=".$JLMS_DB->Quote($username)." AND u.id = ue.id";
		} else {
			$query = "SELECT *, 1 as approved, 1 as confirmed "
			. "\n FROM #__users u "
			. "\n WHERE u.username=".$JLMS_DB->Quote($username);
		}
		$is_success = false;
		$is_error = false;
		$JLMS_DB->setQuery( $query );
		$row = $JLMS_DB->loadObject();
		
		$app = & JFactory::getApplication(); 
								
		if ( is_object( $row ) && JLMS_HashPassword( $password, $row ) ) {
			if ( ($row->approved == 2) || ($row->approved == 0) || ($row->block == 1) ){
				$is_error = true;
			} elseif ($row->confirmed != 1){
				$is_error = true;
			}
			$is_loaded_user = false;
			if (JLMS_Jversion() == 1 || JLMS_Jversion() == 2) {
				$parts	= explode( ':', $row->password );
				$crypt	= $parts[0];
				$salt	= @$parts[1];
			} else {
				$crypt = $row->password;
				$salt = '';
			}
			$testcrypt = JLMS_getCryptedPassword($password, $salt, 'md5-hex');
			if ($crypt == $testcrypt) {
				$is_loaded_user = true;
			}
			if (!$is_loaded_user) {
				$is_error = true;
			}
		}
		if (!$is_error) {		
			if ( $app->login( array( 'username' => $username, 'password' => $password ), array() ) === true ) 
			{				
				$is_success = true;	
			} else {
				$is_success = false;
			}			
		}
		return $is_success;
	}
	
	function register( $from_cb = 0, $freeCourse = false ) 
	{
		global $Itemid, $JLMS_DB,$JLMS_CONFIG,$JLMS_SESSION, $version, $task, $my;	

		$reg_success = false;
		$msg = '';
		
		$username = JRequest::getVar('username');
		$password = JRequest::getVar('password');
		
		$usersConfig = &JComponentHelper::getParams( 'com_users' );
		$app	= &JFactory::getApplication();
	
		if ($from_cb) {		
			global $task;
			$task = 'saveregisters';
			$_REQUEST['task'] = 'saveregisters';
			$_GET['task'] = 'saveregisters';
			$_POST['task'] = 'saveregisters';
			JRequest::setVar('task', $task);
			
			ob_start();
			
			global $ueConfig;
			$_CB_joomla_adminpath = $JLMS_CONFIG->get('absolute_path'). "/administrator";
			$_CB_adminpath = $_CB_joomla_adminpath. "/components/com_comprofiler";
			include_once($_CB_adminpath."/ue_config.php" );
				
			if ($usersConfig->get('allowUserRegistration')) {
				$allowUserRegistration = true;
			} else {
				$allowUserRegistration = false;
			}
			
			// check if CB registration is allowed
			if ( ( ( !$allowUserRegistration )
				   && ( ( ! isset($ueConfig['reg_admin_allowcbregistration']) ) || $ueConfig['reg_admin_allowcbregistration'] != '1' ) )
				 || $my->id ) {
				$msg = _JLMS_REGISTRATION_DISABLED;
				$reg_success = false;
			} else {
				$existingUser = null;
				$query = "SELECT * "
				. "\n FROM #__users u "
				. "\n WHERE u.username = '" . $JLMS_DB->getEscaped( $username ) . "'"
				;
				$JLMS_DB->setQuery( $query );
				$existingUser = $JLMS_DB->loadObjectList();
				// new registration will be failed if user with such username is already exists.
				if ( isset($existingUser[0]) ) {
					$reg_success = false;
				} 
						
				require_once(JPATH_SITE.'/components/com_comprofiler/comprofiler.html.php');
				require_once(JPATH_SITE.'/components/com_comprofiler/comprofiler.php');			
							
				$msg = @ob_get_contents();
				$msg = str_replace('<br />', '**br**', $msg);
				$msg = str_replace('</div><div', '**br**', $msg);
				$msg = strip_tags($msg);
				$msg = str_replace('**br**', '<br />', $msg);
				$msg = trim($msg);
				if (substr($msg,0,6) == 'alert(') {
					preg_match('`alert\(\'(.*)\'\);(.*)`isU', $msg, $matches2);# <script...>(#our_content#)</script> areas
					if (isset($matches2[1])) {
						$msg = $matches2[1];
					}
				}
				@ob_end_clean();			
				
				$filter = & JFilterInput::getInstance();
										
				if( $filter->clean( $msg ) == $filter->clean( _UE_REG_COMPLETE ) ) {
					$msg = '';
					$reg_success = true;
				}						
			}
		} else {
			if (JLMS_Jversion() == 2) {
				if( JLMS_J16version() ) 
				{
					$lang =& JFactory::getLanguage();
					$lang->load( 'com_users');
							
									
					require_once(JPATH_SITE.DS.'components'.DS.'com_users'.DS.'models'.DS.'registration.php');				
					// Create the controller
					$model = new UsersModelRegistration();
					
					$requestData = JRequest::getVar('jform', array(), 'post', 'array');
					
					$username = $requestData['username'];
					$password = $requestData['password1'];
					
					JForm::addFormPath(JPATH_SITE.'/components/com_users/models/forms');
					JForm::addFieldPath(JPATH_SITE.'/components/com_users/models/fields');				
					
					$form	= $model->getForm();						
					$return	= $model->validate($form, $requestData);	
									
					// Save the data in the session.
					if( $return === false ) {
						
						$errors	= $model->getErrors();
	
						// Push up to three validation messages out to the user.
						for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++) 
						{							
							$msg .= '<br />'.$errors[$i];						
						}
																
						$app->setUserState('users.registration.form.data', $requestData);				 
					} else {
						$return =  $model->register($requestData);
						
						if( $return === false ) 
						{
							$msg .= '<br />'.JText::sprintf('COM_USERS_REGISTRATION_SAVE_FAILED', $model->getError());						
							$app->setUserState('users.registration.form.data', $requestData);						
						}	
					}	
					
					if ($return === 'adminactivate'){
						$reg_complete_activate_found = true;
					} else if ($return === 'useractivate') {
						$reg_complete_activate_found = true;
					} else if ( $return !== false ) {
						$reg_success = true;
						$reg_complete_found = true;
					}					
				} else {
					$lang =& JFactory::getLanguage();
					$lang->load( 'com_user');
		
					require_once(JPATH_SITE.'/components/com_user/controller.php');
					require_once(JPATH_SITE.'/components/com_user/views/register/view.html.php');
					// Create the controller
					$controller = new UserController();
					$controller->_basePath = JPATH_SITE.'/components/com_user';
					ob_start();
					$controller->execute( 'register_save');	
					ob_end_clean();
					
					$t = JError::getError(true);				
									
					$reg_complete_found = false;
					$reg_complete_activate_found = false;// this variable is not used yet... for future
					if (isset($t->message) && $t->message == JText::_( 'REG_COMPLETE' )) {
						$reg_success = true;
						$reg_complete_found = true;
					} elseif (isset($t->message) && $t->message == JText::_( 'REG_COMPLETE_ACTIVATE' )) {
						$reg_complete_activate_found = true;
					} elseif (isset($controller->_message) && $controller->_message == JText::_( 'REG_COMPLETE' )) {
						$reg_success = true;
						$reg_complete_found = true;
					} elseif (isset($controller->_message) && $controller->_message == JText::_( 'REG_COMPLETE_ACTIVATE' )) {
						$reg_complete_activate_found = true;
					}				
				}		
			} 
		}
	
		
		$login_success = false;
		if ( $reg_success ) {			
			$login_success = JLMS_UserSessions::doLogin($username, $password);
		}
		
		$app->set('_messageQueue', null);
			
		if ($reg_success && $login_success) 
		{
			if( $freeCourse )
				$msg .= '<br />'._JLMS_REGISTRATION_COMPLETE."<br />"._JLMS_LOGIN_SUCCESS."<br />"._JLMS_SUBSCRIBE_CONTINUE;
			else 
				$msg .= '<br />'._JLMS_REGISTRATION_COMPLETE."<br />"._JLMS_LOGIN_SUCCESS."<br />";
		} elseif ($reg_success) {
			$msg .= '<br />'._JLMS_REGISTRATION_COMPLETE;
		} else {				
			if ($usersConfig->get('useractivation') && !$reg_success) {
				$msg .= '<br />'._JLMS_REGISTRATION_ACTIVATION;
			}
		}	
		
		return $msg;
	}

	function processLogin() 
	{		
		$username = stripslashes( strval( mosGetParam( $_POST, 'username', '' ) ) );
		$password = stripslashes( strval( mosGetParam( $_POST, 'passwd', '' ) ) );		
		//$is_error = false;
				
		$app = & JFactory::getApplication();
		
		if ( !$username || !$password ) {
			return false;
		} else {			
			$login_success = JLMS_UserSessions::doLogin($username, $password);			
			$app->set('_messageQueue', null);
			if( $login_success ) return true;		
		}
		
		return false;
	}
}
?>