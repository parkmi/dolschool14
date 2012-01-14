<?php

defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

$_MAMBOTS->registerFunction( 'onAfterStart', 'jlmsplugin' );

function jlmsplugin() {
	global $database, $my, $mosConfig_live_site, $mosConfig_absolute_path, $mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db, $mosConfig_dbprefix;
	//---->config section
	$query_path = "SELECT lms_config_value AS value, lms_config_var FROM `#__lms_config` WHERE lms_config_var = 'plugin_forum' OR lms_config_var = 'forum_path' OR lms_config_var = 'plugin_lpath_forum'";
	$database -> setQuery($query_path);
	if (!$config = $database->loadObjectList('lms_config_var')){
		$config['forum_path']->value = '';
		$config['plugin_forum']->value = 0;
	}
	//<----end

	if ($config['plugin_forum']->value && $config['forum_path']->value && isset($_REQUEST['option'])) {

		if( ($_REQUEST['option'] == 'login') || ($_REQUEST['option'] == 'com_comprofiler' && isset($_REQUEST['task']) && $_REQUEST['task'] == 'login')  ){

			if ($config['forum_path']->value && file_exists($config['forum_path']->value.'/Settings.php')){

				$username = mosGetParam ($_REQUEST,'username','');
				$password = mosGetParam ($_REQUEST,'passwd','');

				require_once ($mosConfig_absolute_path.'/components/com_joomla_lms/includes/jlms_reg_forum.php');

				require($config['forum_path']->value.'/Settings.php');

				$query = "SELECT * FROM `#__users` WHERE LOWER(username)='".strtolower($username)."' ";
				$database->setQuery($query);
				if ($database -> loadObject($user)) {

					if (isset($user->id)){
						//----> CB section
						$query = "SELECT lms_config_var, lms_config_value FROM `#__lms_config` WHERE lms_config_var LIKE '%_cb_%' ";
						$database->setQuery($query);
						$configs = $database->loadObjectList();

						$JLMS_CONFIG = array();
						foreach($configs as $config_cb){
							$JLMS_CONFIG[$config_cb->lms_config_var] = $config_cb->lms_config_value;
						}
						$cb_info = null;
						if ($JLMS_CONFIG['is_cb_installed']) {
							$fields = array ('website', 'ICQ', 'AIM', 'YIM','MSN', 'location');
							$fields_isset = array();
							foreach ($fields as $field) {
								if (isset($JLMS_CONFIG['jlms_cb_'.$field]) && $JLMS_CONFIG['jlms_cb_'.$field]) {
									$fields_isset[] = $JLMS_CONFIG['jlms_cb_'.$field];
								}
							}
							if (!empty($fields_isset)) {
								$fields_str = implode(',', $fields_isset );
								$query = "SELECT name FROM `#__comprofiler_fields` WHERE fieldid IN ($fields_str) ";
								$database->setQuery($query);
								$field_name = $database->loadResultArray();
								$field_names = implode(',', $field_name);

								$query = "SELECT ".$field_names." FROM `#__comprofiler` WHERE user_id=".$user->id;
								$database->setQuery($query);
								$database->loadObject($cb_info);
							}
						}
						$str = '';
						if (isset($cb_info) && is_object($cb_info)){
							foreach ($cb_info as $key=>$value){
								if ($key == 'website'){
									$key = 'websiteUrl';
									if (!ereg('/(http://)/', $value)){
										$value = 'http://'.$value;
									}
								}
								$str .= $key." = '".$value."' ,";
							}
						}
						//<----

						$groups = '';
						$forum_groups = array();

						$database->database( $db_server, $db_user, $db_passwd , $db_name, '', false);
						$query = "SELECT ID_MEMBER, ID_GROUP, additionalGroups FROM `".$db_prefix."members` WHERE LOWER(memberName) = '".strtolower($username)."' ";
						$database->setQuery($query);
						$database->loadObject($smf_user);
						$primary_group = 0;

						if (is_object($smf_user) && isset($smf_user->ID_MEMBER)){
							$mem_id = $smf_user->ID_MEMBER;
							$primary_group = $smf_user->ID_GROUP;
						} else {
							$mem_id = jlmssmf_register_on_forum( $database, $user, $password, $groups, $db_prefix, $cb_info);
						}
						jlmssmf_setLoginCookie ( $database, $mem_id, $password, $cookiename, $db_prefix);
						//conect to joomla DB
						$database->database( $mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db, $mosConfig_dbprefix );
					}
				}
			}
		}
		elseif (($_REQUEST['option'] == 'logout') || ($_REQUEST['option'] == 'com_comprofiler' && isset($_REQUEST['task']) && $_REQUEST['task'] == 'logout')  ) {
			if ($config['forum_path']->value && file_exists($config['forum_path']->value.'/Settings.php')){
				require($config['forum_path']->value.'/Settings.php');

				setcookie( $cookiename, 0 ,0 , '/' );
			}
		}
		elseif ( ($_REQUEST['option'] == 'com_user' ) || ($_REQUEST['option'] == 'com_comprofiler')) {
			if (isset($_REQUEST['task']) && $_REQUEST['task'] == 'saveUserEdit'){
				if ($config['forum_path']->value && file_exists($config['forum_path']->value.'/Settings.php')){

					require_once ($mosConfig_absolute_path.'/components/com_joomla_lms/includes/jlms_reg_forum.php');

					/* SECURITY CHECK begin */
					//if (function_exists('josSpoofCheck')) josSpoofCheck();
					global $mainframe;
					$tmp_my = $mainframe->getUser();
					if (!isset($tmp_my->id)) {
						echo "<script> alert('You are not authorised to view this resource.'); window.history.go(-1); </script>\n";
						exit();
					}
					if (!$tmp_my->id) {
						echo "<script> alert('You are not authorised to view this resource.'); window.history.go(-1); </script>\n";
						exit();
					}

					$id = 0;
					if(isset($_POST["id"]) && $_POST["id"]) {
						$id = intval($_POST["id"]);
					}
					if (!$id) {
						echo "<script> alert('You are not authorised to view this resource.'); window.history.go(-1); </script>\n";
						exit();
					}
					if ($id != $tmp_my->id){
						echo "<script> alert('You are not authorised to view this resource.'); window.history.go(-1); </script>\n";
						exit();
					}
					/* SECURITY CHECK end */

					require($config['forum_path']->value.'/Settings.php');
					//check to correctness of the entering data
					$pass=',';
					$password = '';
					if(ereg(".+@.+\..+", $_POST["email"])) {
						$email = $_POST["email"];
					}
					else {
						echo "<script> alert(''); window.history.go(-1); </script>\n";
						exit();
					}
					if(isset($_POST["username"]) && $_POST["username"] != "") {
						$username = $_POST["username"];
					}
					else {
						echo "<script> alert('Username is empty!'); window.history.go(-1); </script>\n";
						exit();
					}
					/*if(isset($_POST["id"]) && $_POST["id"] != "") {
					$id = $_POST["id"];
					}*/
					if(isset($_POST["password"]) && $_POST["password"] != "") {
						if(isset($_POST["verifyPass"]) && ($_POST["verifyPass"] == $_POST["password"])) {
							$password = sha1(strtolower(mosGetParam ($_POST , 'username', '' )) .mosGetParam($_POST , 'password', '' ));
							$pass = ", passwd='".$password."', ";

						} elseif(isset($_POST["password__verify"]) && ($_POST["password__verify"] == $_POST["password"])) {
							$password = sha1(strtolower(mosGetParam ($_POST , 'username', '' )) .mosGetParam($_POST , 'password', '' ));
							$pass = ", passwd='".$password."', ";

						} else {
							echo "<script> alert('Wrong password!'); window.history.go(-1); </script>\n";
							exit();
						}
					}
					//SELECT old username for change
					$query = "SELECT username FROM `#__users` WHERE id='".$id."'";
					$database -> setQuery ($query);
					$database -> query();
					$username_old = $database -> loadResult();
					//connect to forum database
					$database->database( $db_server, $db_user, $db_passwd , $db_name, '', false);

					$query = "SELECT ID_MEMBER FROM `".$db_prefix."members` WHERE memberName='".$username_old."'";
					$database -> setQuery ($query);
					$database -> query();
					$userid_forum = $database -> loadResult();

					//UPDATE Forum DATA
					$query = "UPDATE `".$db_prefix."members`
							  SET memberName='".$username."' ".$pass." emailAddress='".$email."', realName= '".mosGetParam($_REQUEST, 'name', '')."'  
							  WHERE memberName='".$username_old."'";
					$database -> setQuery($query);
					$database -> query();
					if ($password){
						jlmssmf_setLoginCookie ( $database, $userid_forum, mosGetParam($_POST , 'password', '' ), $cookiename, $db_prefix);
					}

					//connect Joomla database
					$database->database( $mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db, $mosConfig_dbprefix );
				}
			}
		}
	}
}
?>