<?php
/**
* joomla_lms.chat.php
* (c) JoomaLMS eLearning Software http://www.joomlalms.com/
**/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
require_once(_JOOMLMS_FRONT_HOME . "/joomla_lms.chat.html.php");
$id 	= intval( mosGetParam( $_REQUEST, 'id', 0 ) );
$task 	= mosGetParam( $_REQUEST, 'task', '' );
$group_id = intval( mosGetParam( $_REQUEST, 'group_id', 0));
switch ($task) {
	case 'chat':			JLMS_showChat( $id, $option );				break;
	case 'get_chat_xml':	JLMS_getChatXML( $id, $group_id, $option );	break;
	case 'chat_post':		JLMS_postChatMsg( $id, $option );			break;
}
// to do: insert course checks
function JLMS_showChat( $course_id, $option ) {
	global $JLMS_DB, $my, $Itemid, $JLMS_CONFIG;
	$JLMS_ACL = & JLMSFactory::getACL();
	if ($JLMS_ACL->CheckPermissions('chat', 'view')) {
		global $JLMS_CONFIG;
		$pathway = array();
		$pathway[] = array('name' => _JLMS_PATHWAY_HOME, 'link' => sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid"), 'is_home' => true);
		$pathway[] = array('name' => $JLMS_CONFIG->get('course_name'), 'link' => sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid&amp;task=details_course&amp;id=$course_id"), 'is_course' => true);
		$pathway[] = array('name' => _JLMS_TOOLBAR_CHAT, 'link' => sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid&amp;task=chat&amp;id=$course_id"));
		JLMSAppendPathWay($pathway);

		JLMS_ShowHeading();
		$group_id = intval( mosGetParam( $_REQUEST, 'group_id', 0));
		if ($JLMS_ACL->CheckPermissions('chat', 'manage')) {
			if ($JLMS_CONFIG->get('use_global_groups', 1)) {
				$query = "SELECT group_chat FROM #__lms_usergroups WHERE course_id = 0 AND id = $group_id";
				$JLMS_DB->SetQuery( $query );
				$gr_c = $JLMS_DB->LoadResult();
				if (!$gr_c) { $group_id = 0; }
			}
			else {
				$query = "SELECT group_chat FROM #__lms_usergroups WHERE course_id = $course_id AND id = $group_id";
				$JLMS_DB->SetQuery( $query );
				$gr_c = $JLMS_DB->LoadResult();
				if (!$gr_c) { $group_id = 0; }
			}
		} else {
			if ($JLMS_CONFIG->get('use_global_groups', 1)) {
				if ($group_id) {
					$query = "SELECT group_chat FROM #__lms_users_in_global_groups AS uigg, #__lms_usergroups AS ug WHERE uigg.user_id = $my->id AND uigg.group_id = $group_id AND ug.id = uigg.group_id";
					$JLMS_DB->setQuery($query);
					if (!$JLMS_DB->loadResult()) $group_id = 0;
				}
			}
			else {
				if ($group_id) {
					$query = "SELECT a.group_id FROM #__lms_users_in_groups as a, #__lms_usergroups as b WHERE a.course_id = $course_id AND a.user_id = '".$my->id."' AND a.group_id = b.id AND b.course_id = $course_id AND b.group_chat = 1";
					$JLMS_DB->SetQuery( $query );
					$group_id = $JLMS_DB->LoadResult();
					if (!$group_id) { $group_id = 0; }
				}
			}
		}

		$query = "SHOW COLUMNS FROM `#__lms_chat_history` WHERE Field = 'user_message'";
		$JLMS_DB->SetQuery( $query );
		$chat_table = $JLMS_DB->LoadObject();
		if (is_object($chat_table) && isset($chat_table->Type)) {
			if ($chat_table->Type != 'text') {
				$query = "ALTER TABLE `#__lms_chat_history` CHANGE `user_message` `user_message` TEXT";
				$JLMS_DB->SetQuery( $query );
				$JLMS_DB->query();
			}
		}

		//query (Drop users)
		$query = "DELETE FROM #__lms_chat_users WHERE user_id = '".$my->id."'";
		$JLMS_DB->SetQuery( $query );
		$JLMS_DB->query();
		$tim_minus_15 =  time() - date('Z') - 15*60;
		$query = "DELETE FROM #__lms_chat_users WHERE time_post < '". date( 'Y-m-d H:i:s', $tim_minus_15 ) ."'";
		$JLMS_DB->SetQuery( $query );
		$JLMS_DB->query();
		//query (Entering chat)
		$query = "INSERT INTO #__lms_chat_users (course_id, group_id, user_id, time_enter, time_post, chat_option)"
		. "\n VALUES ('".$course_id."', '".$group_id."', '".$my->id."', '". gmdate( 'Y-m-d H:i:s' ) ."', '". gmdate( 'Y-m-d H:i:s' ) ."', '0')";
		$JLMS_DB->SetQuery( $query );
		$JLMS_DB->query();

		//tracking
		global $Track_Object;
		$Track_Object->UserEnterChat( $my->id, $course_id );

		$query = "SELECT a.username FROM #__users as a, #__lms_chat_users as b"
		. "\n WHERE a.id = b.user_id AND b.course_id = '".$course_id."' AND b.group_id = '".$group_id."'"
		. "\n ORDER BY a.username";
		$JLMS_DB->SetQuery( $query );
		$chat_users = $JLMS_DB->LoadObjectList();
		$course_chats = array();
		$course_chats[] = mosHTML::makeOption( 0, _JLMS_CHAT_COURSE_CHAT );
		if ($JLMS_ACL->CheckPermissions('chat', 'manage')) {
			if ($JLMS_CONFIG->get('use_global_groups', 1)) {
				$query = "SELECT ug.id AS value, ug.ug_name AS text"
				. "\n FROM #__lms_usergroups AS ug, #__lms_users_in_groups AS uig, #__lms_users_in_global_groups AS uigg"
				. "\n WHERE ug.group_chat = 1 AND uig.course_id = $course_id AND uigg.user_id = uig.user_id AND ug.id = uigg.group_id";
				$JLMS_DB->setQuery($query);
				$group_chats = $JLMS_DB->loadObjectList();
				for ($i = 0; $i < count($group_chats); $i ++) {
					$group_chats[$i]->text = $group_chats[$i]->text . " ("._JLMS_CHAT_GROUP_CHAT.")";
				}
				$course_chats = array_merge($course_chats, $group_chats );
			}
			else {
				$query = "SELECT id as value, ug_name as text FROM #__lms_usergroups WHERE course_id = $course_id AND group_chat = 1";
				$JLMS_DB->SetQuery( $query );
				$group_chats = $JLMS_DB->loadObjectList();
				for ($i = 0; $i < count($group_chats); $i ++) {
					$group_chats[$i]->text = $group_chats[$i]->text . " ("._JLMS_CHAT_GROUP_CHAT.")";
				}
				$course_chats = array_merge($course_chats, $group_chats );
			}
		} else {
			if ($JLMS_CONFIG->get('use_global_groups', 1)) {
				$query = "SELECT ug.id AS value, ug.ug_name AS text"
				. "\n FROM #__lms_usergroups AS ug, #__lms_users_in_groups AS uig, #__lms_users_in_global_groups AS uigg"
				. "\n WHERE uig.course_id = $course_id AND uigg.user_id = uig.user_id AND ug.id = uigg.group_id AND ug.group_chat = 1 AND uig.user_id = $my->id";
				$JLMS_DB->setQuery($query);
				$group_chats = $JLMS_DB->loadObjectList();
				for ($i = 0; $i < count($group_chats); $i ++) {
					$group_chats[$i]->text = $group_chats[$i]->text . " ("._JLMS_CHAT_GROUP_CHAT.")";
				}
				$course_chats = array_merge($course_chats, $group_chats );
			}
			else {
				$query = "SELECT b.id as value, b.ug_name as text FROM #__lms_users_in_groups as a, #__lms_usergroups as b WHERE a.course_id = $course_id AND a.user_id = '".$my->id."' AND a.group_id = b.id AND b.course_id = $course_id AND b.group_chat = 1";
				$JLMS_DB->SetQuery( $query );
				$group_chats = $JLMS_DB->loadObjectList();
				for ($i = 0; $i < count($group_chats); $i ++) {
					$group_chats[$i]->text = _JLMS_CHAT_GROUP_CHAT . " (" . $group_chats[$i]->text . ")";
				}
				$course_chats = array_merge($course_chats, $group_chats );
			}
		}
		/*if (JLMS_GetUserType_simple($my->id) == 1) {
		$query = "SELECT distinct b.* FROM #__lms_courses as b, #__lms_user_courses as a"
		. "\n WHERE a.course_id = b.id AND a.user_id = '".$my->id."'";
		} elseif (JLMS_GetUserType_simple($my->id) == 2) {
		$query = "SELECT distinct a.* FROM #__lms_courses as a, #__lms_usergroups as b, #__lms_users_in_groups as c"
		. "\n WHERE a.id = b.course_id AND b.id = c.group_id AND c.user_id = '".$my->id."'";
		}
		$JLMS_DB->SetQuery( $query );
		$rows = $JLMS_DB->LoadObjectList();*/
		$lists = array();
		$javascript = 'onchange="document.chatForm.submit();"';
		$lists['course_chats'] = mosHTML::selectList($course_chats, 'group_id', 'class="inputbox" size="1" '.$javascript, 'value', 'text', $group_id );

		JLMS_chat_html::showChat( $course_id, $group_id, $option, $lists, $chat_users );
	} else {
		JLMSRedirect(sefRelToAbs("index.php?option=$option&Itemid=$Itemid"));
	}
}
function JLMS_postChatMsg( $course_id, $option ) {
	global $JLMS_DB, $my, $JLMS_CONFIG;
	$JLMS_ACL = & JLMSFactory::getACL();
	if ($JLMS_ACL->CheckPermissions('chat', 'view')) {
		$XML_data = '';
		$group_id = intval( mosGetParam( $_REQUEST, 'group_id', 0 ) );
		$do_chat = false;
		if ($JLMS_ACL->CheckPermissions('chat', 'manage')) {
			if ($group_id) {
				if ($JLMS_CONFIG->get('use_global_groups', 1)) {
					$query = "SELECT group_chat FROM #__lms_usergroups WHERE course_id = 0 AND id = $group_id";
					$JLMS_DB->SetQuery( $query );
					if ($JLMS_DB->LoadResult()) {
						$do_chat = true;
					}
				}
				else {
					$query = "SELECT group_chat FROM #__lms_usergroups WHERE course_id = $course_id AND id = $group_id";
					$JLMS_DB->SetQuery( $query );
					if ($JLMS_DB->LoadResult()) {
						$do_chat = true;
					}
				}
			} else {
				$do_chat = true;
			}
		} else {
			if ($group_id) {
				if ($JLMS_CONFIG->get('use_global_groups', 1)) {
					$query = "SELECT group_chat FROM #__lms_users_in_global_groups AS uigg, #__lms_usergroups AS ug WHERE uigg.user_id = $my->id AND uigg.group_id = $group_id AND ug.id = uigg.group_id";
					$JLMS_DB->setQuery($query);
					if ($JLMS_DB->LoadResult()) {
						$do_chat = true;
					}
				}
				else {
					$query = "SELECT b.group_chat FROM #__lms_users_in_groups as a, #__lms_usergroups as b"
					. "\n WHERE a.course_id = $course_id AND a.group_id = $group_id AND a.user_id = '".$my->id."' AND a.group_id = b.id AND b.id = $group_id AND b.course_id = $course_id";
					$JLMS_DB->SetQuery( $query );
					if ($JLMS_DB->LoadResult()) {
						$do_chat = true;
					}
				}
			} else {
				$do_chat = true;
			}
		}
		if ($do_chat) {
			$query = "SELECT time_enter FROM #__lms_chat_users"
			. "\n WHERE user_id = '".$my->id."' AND course_id = '".$course_id."' AND group_id = '".$group_id."'";
			$JLMS_DB->SetQuery( $query );
			$time_online = $JLMS_DB->LoadResult();
			$user_msg = '';
			/*print_r($_REQUEST);*/
			if (isset($_REQUEST['message'])) {
				$user_msg = trim(strval($_REQUEST['message']));
				$user_msg = (get_magic_quotes_gpc()) ? stripslashes( $user_msg ) : $user_msg;
			}
			if ($time_online && ($user_msg || $user_msg === '0' || $user_msg === 0)) {
				//query (Update time)
				$query = "UPDATE #__lms_chat_users SET time_post = '". gmdate( 'Y-m-d H:i:s' ) ."'"
				. "\n WHERE user_id = '".$my->id."' AND course_id = '".$course_id."' AND group_id = '".$group_id."'";
				$JLMS_DB->SetQuery( $query );
				$JLMS_DB->query();
				//query (post Message)
				$user_msg = $JLMS_DB->GetEscaped(unicode_decode($user_msg));
				/*				print_r($user_msg);
				die;
				*/				$query = "INSERT INTO #__lms_chat_history (course_id, group_id, user_id, recv_id, user_message, mes_time)"
				. "\n VALUES ('".$course_id."', '".$group_id."', '".$my->id."', '0', '".$user_msg."', '". gmdate( 'Y-m-d H:i:s' ) ."')";
				$JLMS_DB->SetQuery( $query );
				$JLMS_DB->query();
				JLMS_getChatXML( $course_id, $group_id, $option, true );
			}
		}
	}
	JLMS_getChatXML( $course_id, $group_id, $option, false );
}
function JLMS_getChatXML( $course_id, $group_id, $option, $checked = false ) {
	global $JLMS_DB, $my, $JLMS_CONFIG;
	$do_chat = $checked;
	if (!$do_chat) {
		$JLMS_ACL = & JLMSFactory::getACL();
		if ($JLMS_ACL->CheckPermissions('chat', 'view')) {
			if ($JLMS_ACL->CheckPermissions('chat', 'manage')) {
				if ($group_id) {
					if ($JLMS_CONFIG->get('use_global_groups', 1)) {
						$query = "SELECT group_chat FROM #__lms_usergroups WHERE course_id = 0 AND id = $group_id";
						$JLMS_DB->SetQuery( $query );
						if ($JLMS_DB->LoadResult()) {
							$do_chat = true;
						}
					}
					else {
						$query = "SELECT group_chat FROM #__lms_usergroups WHERE course_id = $course_id AND id = $group_id";
						$JLMS_DB->SetQuery( $query );
						if ($JLMS_DB->LoadResult()) {
							$do_chat = true;
						}
					}
				} else {
					$do_chat = true;
				}
			} else {
				if ($group_id) {
					if ($JLMS_CONFIG->get('use_global_groups', 1)) {
						$query = "SELECT group_chat FROM #__lms_users_in_global_groups AS uigg, #__lms_usergroups AS ug WHERE uigg.user_id = $my->id AND uigg.group_id = $group_id AND ug.id = uigg.group_id";
						$JLMS_DB->setQuery($query);
						if ($JLMS_DB->LoadResult()) {
							$do_chat = true;
						}
					}
					else {
						$query = "SELECT b.group_chat FROM #__lms_users_in_groups as a, #__lms_usergroups as b"
						. "\n WHERE a.course_id = $course_id AND a.group_id = $group_id AND a.user_id = '".$my->id."' AND a.group_id = b.id AND b.id = $group_id AND b.course_id = $course_id";
						$JLMS_DB->SetQuery( $query );
						if ($JLMS_DB->LoadResult()) {
							$do_chat = true;
						}
					}
				} else {
					$do_chat = true;
				}
			}
		}
	}
	if ($do_chat) {
		$last_msg = intval( mosGetParam( $_REQUEST, 'last_msg', 0 ) );
		//query (Drop users)
		$tim_minus_15 =  time() - date('Z') - 15*60;
		$query = "DELETE FROM #__lms_chat_users WHERE time_post < '". date( 'Y-m-d H:i:s', $tim_minus_15 ) ."'";
		$JLMS_DB->SetQuery( $query );
		$JLMS_DB->query();
		$query = "SELECT time_enter, time_post FROM #__lms_chat_users"
		. "\n WHERE user_id = '".$my->id."' AND course_id = '".$course_id."' AND group_id = '".$group_id."'";
		$JLMS_DB->SetQuery( $query );
		$XML_data = '';
		$user_time_obj = $JLMS_DB->LoadObject();
		if (is_object($user_time_obj) && isset($user_time_obj->time_enter)) {
			$time_online = $user_time_obj->time_enter;
			$time_prev_post = $user_time_obj->time_post;
			if ($time_online) {
				//query (Update time)
				$query = "UPDATE #__lms_chat_users SET time_post = '". gmdate( 'Y-m-d H:i:s' ) ."'"
				. "\n WHERE user_id = '".$my->id."' AND course_id = '".$course_id."' AND group_id = '".$group_id."'";
				$JLMS_DB->SetQuery( $query );
				$JLMS_DB->query();
	
				$query = "SELECT a.username FROM #__users as a, #__lms_chat_users as b"
				. "\n WHERE a.id = b.user_id AND b.course_id = '".$course_id."' AND b.group_id = '".$group_id."'"
				. "\n ORDER BY a.username";
				$JLMS_DB->SetQuery( $query );
				$chat_users = $JLMS_DB->LoadObjectList();
	
				$query = "SELECT a.username, b.user_message, b.mes_time, b.id FROM #__users as a, #__lms_chat_history as b"
				. "\n WHERE a.id = b.user_id AND b.course_id = '".$course_id."' AND b.mes_time > '".$time_online."'"
				. "\n AND b.group_id = '".$group_id."' AND (b.recv_id = '0' OR b.recv_id = '".$my->id."')"
				. "\n ORDER BY b.mes_time DESC, b.id DESC LIMIT 0, 100";
				$JLMS_DB->SetQuery( $query );
				$chat_history = $JLMS_DB->LoadObjectList();
				$new_chat_history = array();
				$time_prev_post_time = strtotime($time_prev_post);
				$do_scroll_chat = false;
				$new_last_id = 0;
				$i = count($chat_history) - 1;
				if (isset($chat_history[0]->mes_time)) {
					$time_last_post_time = strtotime($chat_history[0]->mes_time);
					if ($time_last_post_time >= $time_prev_post_time) {
						$do_scroll_chat = true;
					}
				}
				while ($i >= 0) {
					$new_chat_history[] = $chat_history[$i];
					$i --;
				}

				/* 29.05.2008 - changes by DEN */
				$show_chat_history = array();
				for ($i=0;$i < count($new_chat_history); $i ++) {
					if ($new_chat_history[$i]->id > $last_msg) {
						$show_chat_history[] = $new_chat_history[$i];
						if ($new_chat_history[$i]->id > $new_last_id) {
							$new_last_id = $new_chat_history[$i]->id;
						}
					}
				}
				$count_mes = count($show_chat_history);
				$XML_data .= "\t" . '<task>chat_xml</task>' . "\n";
				$XML_data .= "\t" . '<count_new_msgs>'.$count_mes.'</count_new_msgs>' . "\n";
				$XML_data .= "\t" . '<chat_last_id>'.$new_last_id.'</chat_last_id>' . "\n";
				$XML_data .= "\t" . '<chat_users><![CDATA['.JLMS_chat_html::prepareUserList($chat_users).']]></chat_users>' . "\n";
				//$XML_data .= "\t" . '<chat_history><![CDATA['.JLMS_chat_html::prepareChatHistory($new_chat_history).']]></chat_history>' . "\n";
				$i=1;
				foreach($show_chat_history as $data){
					$CDATA = '<b>'.$data->username.': </b><br />';
					$CDATA .= JLMS_nl2br($data->user_message);
					$XML_data .= "\t" . '<chat_message_'.$i.'><![CDATA['.$CDATA.']]></chat_message_'.$i.'>' . "\n";
					$i++;
				}
			}
		}
		$iso = explode( '=', _ISO );
		echo "\n"."some notices :)";
		$debug_str = ob_get_contents();
		$debug_str = "no debug info";
		ob_end_clean();
			header ('Expires: Fri, 14 Mar 1980 20:53:00 GMT');
			header ('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
			header ('Cache-Control: no-cache, must-revalidate');
			header ('Pragma: no-cache');
			if (class_exists('JFactory')) {
				$document=& JFactory::getDocument();
				$charset_xml = $document->getCharset();
				header ('Content-Type: text/xml; charset='.$charset_xml);
			} else {
				header ('Content-Type: text/xml');
			}
		if ($XML_data != "") {
			echo '<?xml version="1.0" encoding="'.$iso[1].'" standalone="yes"?>';
			echo '<response>' . "\n";
			echo $XML_data;
			echo "\t" . '<debug><![CDATA['.$debug_str.']]></debug>' . "\n";
			echo '</response>' . "\n";
		}
		else {
			echo '<?xml version="1.0" encoding="'.$iso[1].'" standalone="yes"?>';
			echo '<response>' . "\n";
			echo "\t" . '<task>failed</task>' . "\n";
			echo "\t" . '<info>boom</info>' . "\n";
			echo "\t" . '<debug><![CDATA['.$debug_str.']]></debug>' . "\n";
			echo '</response>' . "\n";
		}
	}
	exit();
}

/*function unicode_decode($txt) {
	// !!! ereg_replace() is depreceted in PHP5.3 - do not use it
	$txt = ereg_replace('%u0([[:alnum:]]{3})', '&#x\1;',$txt);
	$txt = ereg_replace('%([[:alnum:]]{2})', '&#x\1;',$txt);
	return urldecode($txt);
}*/

function unicode_decode($url) {

	preg_match_all('/%u([[:alnum:]]{4})/', $url, $a);

	foreach ($a[1] as $uniord) {
		$utf = '&#x' . $uniord . ';';
		$url = str_replace('%u'.$uniord, $utf, $url);
	}
	preg_match_all('/%([[:alnum:]]{2})/', $url, $a);

	foreach ($a[1] as $uniord) {
		$utf = '&#x' . $uniord . ';';
		$url = str_replace('%'.$uniord, $utf, $url);
	}

	preg_match_all('/%([[ABCDEF1234567890]]{2})/', $url, $a);

	foreach ($a[1] as $uniord) {
		$utf = '&#' .  hexdec($uniord) . ';';
		$url = str_replace('%'.$uniord, $utf, $url);
	}

	foreach ($a[1] as $uniord) {
		$utf = '&#' .  hexdec($uniord) . ';';
		$url = str_replace('%'.$uniord, $utf, $url);
	}

	$url = htmlentities(urldecode($url));
	$url = str_replace('&amp;#','&#', $url);

	return $url;
}
?>