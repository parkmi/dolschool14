<?php 

function jlmssmf_register_on_forum( &$JLMS_DB, $user, $pass, $groups, $db_prefix, $cb_info = NULL ){
	global $my;//,$JLMS_DB;

	//$md5_hmac_pass = jlmssmf_md5_hmac($pass , strtolower($user->username));
	$email = $user->email;
	$name  = $user->name;
	$ip = $_SERVER['REMOTE_ADDR'];
	//Adding new user into forum
	$password = sha1(strtolower($user->username) .$pass);

	$str_keys = '';
	$str_value = '';
	if (is_object($cb_info)){
		foreach ($cb_info as $key=>$value){
			if ($key == 'website'){
				$key = 'websiteUrl';
				if (!ereg('/(http://)/', $value)){
					$value = 'http://'.$value;
				}
				$str_keys .= ", ".$key;
				$str_value .= ", ".$JLMS_DB->quote($value)."";
			}/* elseif (blablabla) {
			}*/
		}
	}
	
	$query_insert = "INSERT INTO ".$db_prefix."members (member_name, date_registered, id_group, real_name, passwd, email_address, pm_email_notify, show_online, notify_announcements, notify_types, member_ip, member_ip2, is_activated, additional_groups, id_post_group, password_salt".$str_keys.")
					 VALUES (".$JLMS_DB->quote( $user->username ).", '".time()."', '0', ".$JLMS_DB->quote($name).", '".$password."', ".$JLMS_DB->quote($email).", '1', '1', '1', '2','".$ip."', '".$ip."', '1', '".$groups."', '4', '".substr(md5(rand()), 0, 4)."'".$str_value.")";
	$JLMS_DB->setQuery($query_insert);
	$JLMS_DB->query();
	
	return $JLMS_DB->insertid();
}


function jlmssmf_md5_hmac($data, $key)
{
	$key = str_pad(strlen($key) <= 64 ? $key : pack('H*', md5($key)), 64, chr(0x00));
	return md5(($key ^ str_repeat(chr(0x5c), 64)) . pack('H*', md5(($key ^ str_repeat(chr(0x36), 64)) . $data)));
}

function jlmssmf_setLoginCookie( &$JLMS_DB, $id, $password = '', $cookiename,  $db_prefix )
{
	setcookie( $cookiename, 0 ,0 , '/' );
	
	$query = "SELECT member_name, password_salt FROM `".$db_prefix."members` WHERE id_member  = '$id' ";
	$JLMS_DB->setQuery($query);
	$userdata = $JLMS_DB->loadObject();
	if (is_object($userdata)) {
		jlmssmf_setLoginCookie_UN($id, $userdata, $cookiename, $password);
	}
}
function jlmssmf_setLoginCookie15( &$JLMS_DB, $id, $password = '', $cookiename,  $db_prefix )
{
	setcookie( $cookiename, 0 ,0 , '/' );
	
	$query = "SELECT member_name, password_salt FROM `".$db_prefix."members` WHERE id_member  = '$id' ";
	$JLMS_DB->setQuery($query);
	$userdata = $JLMS_DB->loadObject();
	if (isset($userdata->member_name)) {
		jlmssmf_setLoginCookie_UN($id, $userdata, $cookiename, $password);
	}
}

function jlmssmf_setLoginCookie_UN($id, $userdata, $cookiename, $password = '') {
	$password = sha1(sha1(strtolower($userdata->member_name) . $password) . $userdata->password_salt );
		
	$data = serialize(empty($id) ? array(0, '', 0) : array($id, $password, time() , 0));
	$cookie_url = jlmssmf_url_parts();
	
	// Set the cookie, $_COOKIE, and session variable.
	setcookie($cookiename, $data, time() + (60*60*24*365), $cookie_url[1], $cookie_url[0], 0);
	// cookies are for 1 year... to force 'remember me' to avoid double-login

	$_COOKIE[$cookiename] = $data;
	$_SESSION['login_' . $cookiename] = $data;
}

function jlmssmf_url_parts()
{
	global $boardurl, $modSettings;

	// Parse the URL with PHP to make life easier.
	$parsed_url = parse_url($boardurl);
	if (isset($parsed_url['port']))
		$parsed_url['host'] .= ':' . $parsed_url['port'];

	// Is local cookies off?
	if (empty($parsed_url['path']) || empty($modSettings['localCookies']))
		$parsed_url['path'] = '';

	// Globalize cookies across domains (filter out IP-addresses)?
	if (!empty($modSettings['globalCookies']) && !preg_match('~^\d{1,3}(\.\d{1,3}){3}$~', $parsed_url['host']))
	{
		// If we can't figure it out, just skip it.
		if (preg_match('~(?:[^\.]+\.)?([^\.]{2,}\..+)\z~i', $parsed_url['host'], $parts) == 1)
			$parsed_url['host'] = '.' . $parts[1];
	}
	// We shouldn't use a host at all if both options are off.
	elseif (empty($modSettings['localCookies']))
		$parsed_url['host'] = '';

	return array($parsed_url['host'], $parsed_url['path'] . '/');
}

function jlms_get_something() {
	static $instance;
	static $is_already_populated;
	if ($is_already_populated === true) {
		return $instance;
	} else {
		//populate !!!!!!!!!!!!!!
		$is_already_populated = true;
		return $instance;
	}
}
function JLMS_Populate_course_forums( $course_id, &$user_forums, &$all_forums, &$type){
	$is_ex = false;
	foreach ($all_forums as $af) {
		if ($af->board_type == $type->id) {
			$is_ex = true;
			if ($af->is_active) {
				$user_forums[] = clone($af);
			}
		}
	}
	/* course forums are created in the course new/edit interface  - there is no need to create them here*/
	if (!$is_ex) { 
		$new_forum = clone($type);
		$new_forum->id = 0;
		$new_forum->group_id = 0;
		$new_forum->course_id = $course_id;
		$new_forum->board_type = $type->id;
		$new_forum->id_cat = 0;
		$new_forum->id_group = 0;
		$new_forum->id_board = 0;
		$user_forums[] = $new_forum;
	}
}
function JLMS_Populate_lgroup_forums( $course_id, &$user_forums, &$all_forums, &$type){
	global $my, $JLMS_DB;
		
	$JLMS_ACL = & JLMSFactory::getACL();
	if ($JLMS_ACL->isTeacher()) {
		$query = "SELECT distinct id, ug_name FROM #__lms_usergroups WHERE course_id = '".$course_id."' ORDER BY ug_name";
	} else {
		$query = "SELECT distinct a.id, a.ug_name FROM #__lms_usergroups as a, #__lms_users_in_groups as b WHERE a.course_id = '".$course_id."' AND a.course_id = b.course_id AND a.id = b.group_id AND b.user_id = ".$my->id." ORDER BY a.ug_name";
	}
	$JLMS_DB->SetQuery( $query );
	$user_groups = $JLMS_DB->LoadObjectList('id');
	$user_groups_ids = array();
	foreach ($user_groups as $ug) {
		$user_groups_ids[] = $ug->id;
	}	
	$is_ex = false;
	if (count($user_groups)) {
		foreach ($all_forums as $af) {
			if ($af->board_type == $type->id) {
				$is_ex = true;
				if ($af->is_active && in_array($af->group_id, $user_groups_ids)) {
					$af->item_title = isset($user_groups[$af->group_id]->ug_name) ? $user_groups[$af->group_id]->ug_name : '';
					$user_forums[] = clone($af);
				}
			}
		}
	}
	/* local groups forums are created in the course new/edit interface  - there is no need to create them here*/
	/*if (!$is_ex) {
		$new_forum = clone($type);
		$new_forum->id = 0;
		$new_forum->board_type = $type->id;
		$user_forums[] = $new_forum;
	}*/
}
function JLMS_Populate_ggroup_forums( $course_id, &$user_forums, &$all_forums, &$type){
	global $my, $JLMS_DB;
	$JLMS_ACL = & JLMSFactory::getACL();
	
	if ($JLMS_ACL->isTeacher()) {
		
		$groups_where_admin_manager = "'0'";
		if($JLMS_ACL->CheckPermissions('advanced', 'assigned_groups_only')) {
			$query = "SELECT a.group_id FROM `#__lms_user_assign_groups` as a WHERE a.user_id = '".$my->id."' group by a.group_id"
			;
			$JLMS_DB->setQuery($query);
			$groups_where_admin_manager = $JLMS_DB->loadResultArray();
			
			if(count($groups_where_admin_manager) == 1) {
				$filt_group = $groups_where_admin_manager[0];
			}
			
			$groups_where_admin_manager = implode(',', $groups_where_admin_manager);
			
			if($groups_where_admin_manager == '') {
				$groups_where_admin_manager = "'0'";
			}
		}
		
		$query = "SELECT distinct ug.id, ug.ug_name"
		."\n FROM #__lms_users_in_groups AS uig, #__lms_users_in_global_groups AS uigg, #__lms_usergroups AS ug"
		."\n WHERE uig.course_id = $course_id"
		."\n AND ug.group_forum = 1"
		."\n AND uig.user_id = uigg.user_id"
		."\n AND ug.id = uigg.group_id AND ug.course_id = 0"
		. ($JLMS_ACL->CheckPermissions('advanced', 'assigned_groups_only') ? ("\n AND ug.id IN ($groups_where_admin_manager)") :'')
		;			

	} else {
		$query = "SELECT distinct ug.id, ug.ug_name FROM #__lms_usergroups AS ug, #__lms_users_in_global_groups as a WHERE ug.id = a.group_id AND a.user_id = $my->id AND ug.course_id = 0 AND ug.group_forum = 1";
	}
	$JLMS_DB->SetQuery( $query );
	$user_groups = $JLMS_DB->LoadObjectList('id');
	
	
	$user_groups_ids = array();
	foreach ($user_groups as $ug) {
		$user_groups_ids[] = $ug->id;
	}
	$groups_ex = array();
	if (count($user_groups)) {
		foreach ($all_forums as $af) {
			if ($af->board_type == $type->id) {
				if ($af->is_active && in_array($af->group_id, $user_groups_ids)) {
					$af->item_title = isset($user_groups[$af->group_id]->ug_name) ? $user_groups[$af->group_id]->ug_name : '';
					$user_forums[] = clone($af);
					$groups_ex[] = $af->group_id;
				}
			}
		}
	}
	// we need to create missing global groups forums
	if (count($groups_ex) < count($user_groups)) {
		foreach ($user_groups as $ug) {
			if (!in_array($ug->id,$groups_ex)) {
				$new_forum = clone($type);
				$new_forum->id = 0;
				$new_forum->group_id = $ug->id;
				$new_forum->board_type = $type->id;
				$new_forum->item_title = $ug->ug_name;
				$new_forum->id_cat = 0;
				$new_forum->id_group = 0;
				$new_forum->id_board = 0;
				$user_forums[] = $new_forum;
			}
		}
	}
}
function JLMS_Populate_lpath_forums( $course_id, &$user_forums, &$all_forums, &$type){
	global $my, $JLMS_DB;
	$JLMS_ACL = & JLMSFactory::getACL();
	if ($JLMS_ACL->isTeacher()) {
		$query = "SELECT a.*"
		. "\n FROM #__lms_learn_paths as a"
		. "\n WHERE a.course_id = '".$course_id."' AND a.lp_params like '%add_forum=1%'"
		. "\n ORDER BY a.ordering";
		$JLMS_DB->SetQuery( $query );
		$user_lpaths = $JLMS_DB->LoadObjectList();
		$user_lpaths_ids = array();
		foreach ($user_lpaths as $ul) {
			$user_lpaths_ids[] = $ul->id;
		}
	} else {
		/* Get list of Published LPaths and check access rights to them (i.e. access is restricted by prerequisites) */
		$query = "SELECT a.*, '' as r_status, '' as r_start, '' as r_end"
		. "\n FROM #__lms_learn_paths as a"
		. "\n WHERE a.course_id = '".$course_id."'"
		//. "\n AND a.published = 1"
		. "\n ORDER BY a.ordering";
		$JLMS_DB->SetQuery( $query );
		$lpaths = $JLMS_DB->LoadObjectList();

		require_once(_JOOMLMS_FRONT_HOME . "/includes/lms_grades.lib.php");
		$user_ids = array();
		$user_ids[] = $my->id;
		JLMS_LP_populate_results($course_id, $lpaths, $user_ids);

		// 13 August 2007 (DEN) Check for prerequisites.
		// 1. get the list of lpath_ids.
		$lpath_ids = array();
		foreach ($lpaths as $lpath) {
			$lpath_ids[] = $lpath->id;
		}
		if (!empty($lpath_ids)) {
			$lpath_ids_str = implode(',', $lpath_ids);
			// 2. get the list of prerequisites
			// SELECT from two tables (+ #__lms_learn_paths) - because the prereq lpath could be deleted...
			$query = "SELECT a.* FROM #__lms_learn_path_prerequisites as a, #__lms_learn_paths as b"
			. "\n WHERE a.lpath_id IN ($lpath_ids_str) AND a.req_id = b.id";
			$JLMS_DB->SetQuery($query);
			$prereqs = $JLMS_DB->LoadObjectList();
			if (!empty($prereqs)) {
				// 3. compare lists of prereqs to the lists of lpaths.
				$i = 0;
				while ($i < count($lpaths)) {
					$is_hidden = false;
					$o = 0;
					while ($o < count($prereqs)) {
						if ($prereqs[$o]->lpath_id == $lpaths[$i]->id) {
							$j = 0;
							while ($j < count($lpaths)) {
								if ($lpaths[$j]->id == $prereqs[$o]->req_id) {
									if (!$lpaths[$j]->item_id) {
										if (empty($lpaths[$j]->r_status)) {
											$is_hidden = true;
											break;
										} else {
											$end_time = strtotime($lpaths[$j]->r_end);
											$current_time = strtotime(date("Y-m-d H:i:s"));
											if($current_time > $end_time && (($current_time - $end_time) < ($prereqs[$o]->time_minutes*60))){
												$is_hidden = true;
												break;	
											}
										}
									} else {
										if (empty($lpaths[$j]->s_status)) {
											$is_hidden = true;
											break;
										} else {
											$end_time = strtotime($lpaths[$j]->r_end);
											$current_time = strtotime(date("Y-m-d H:i:s"));
											if($current_time > $end_time && (($current_time - $end_time) < ($prereqs[$o]->time_minutes*60))){
												$is_hidden = true;
												break;	
											}
										}
									}
								}
								$j ++;
							}
						}
						$o ++;
					}
					if ($is_hidden) {
						$lpaths[$i]->published = 0;
					}
					$i ++;
				}
			}
		}
		$user_lpaths = array();
		$user_lpaths_ids = array();
		foreach ($lpaths as $lp) {
			if ($lp->published) {
				$pos = strpos($lp->lp_params, 'add_forum=1');
				if ($pos === false) {
				} else { // forum is allowed for this lpath
					$rrr = new stdClass();
					$rrr = clone($lp);
					$user_lpaths[] = $rrr;
					$user_lpaths_ids[] = $rrr->id;
				}
			}
		}
	}

	$groups_ex = array();
	if (count($user_lpaths)) {
		foreach ($all_forums as $af) {
			if ($af->board_type == $type->id) {
				if ($af->is_active && in_array($af->group_id, $user_lpaths_ids)) {
					$user_forums[] = clone($af);
					$groups_ex[] = $af->group_id;
				}
			}
		}
	}
	// we need to create missing lpaths forums
	if (count($groups_ex) < count($user_lpaths)) {
		foreach ($user_lpaths as $ul) {
			if (!in_array($ul->id,$groups_ex)) {
				$new_forum = clone($type);
				$new_forum->id = 0;
				$new_forum->group_id = $ul->id;
				$new_forum->board_type = $type->id;
				$new_forum->item_title = $ul->lpath_name;
				$new_forum->id_cat = 0;
				$new_forum->id_group = 0;
				$new_forum->id_board = 0;
				$user_forums[] = $new_forum;
			}
		}
	}
}
/**
 !!!! AXTUNG: all `parent` froums should be created before creating the child one !!! 
 */
function JLMS_createForum( &$give_course_forums, $i, $course_name, $owner_name, &$all_moderators, $db_prefix, $id_cat) {
	global $JLMS_DB; //should be connected to the SMF !!!!	
	$forum_name = $give_course_forums[$i]->forum_name;
	$forum_desc = $give_course_forums[$i]->forum_desc;
	$forum_name = str_replace('{course_name}', $course_name, $forum_name);
	$forum_name = str_replace('{group_name}', $give_course_forums[$i]->item_title, $forum_name);
	$forum_name = str_replace('{lpath_name}', $give_course_forums[$i]->item_title, $forum_name);
	$forum_desc = str_replace('{course_name}', $course_name, $forum_desc);
	$forum_desc = str_replace('{group_name}', $give_course_forums[$i]->item_title, $forum_desc);
	$forum_desc = str_replace('{lpath_name}', $give_course_forums[$i]->item_title, $forum_desc);

	$query = "INSERT INTO `".$db_prefix."membergroups` (group_name, minPosts, maxMessages, stars) VALUES (".$JLMS_DB->Quote($forum_name).", '-1' , '0', '1#star.gif' )";
	$JLMS_DB->setQuery($query);
	$JLMS_DB->query();
	$id_group = $JLMS_DB->insertid();

	$parent_id = 0;
	if ($give_course_forums[$i]->parent_forum) {
		for ($j = 0; $j < count($give_course_forums); $j++) {
			if ($give_course_forums[$j]->board_type == $give_course_forums[$i]->parent_forum) {
				if (!$give_course_forums[$i]->group_id || ($give_course_forums[$i]->group_id == $give_course_forums[$j]->group_id)) {
					if (isset($give_course_forums[$j]->id_board) && $give_course_forums[$j]->id_board) {
						$parent_id = $give_course_forums[$j]->id_board;
					}
				}
			}
		}
	}
	$query = "INSERT INTO `".$db_prefix."boards` (id_cat, ID_PARENT, memberGroups, name, description ) VALUES ('".$id_cat."', '".$parent_id."', '".$id_group."', ".$JLMS_DB->Quote($forum_name).", ".$JLMS_DB->Quote($forum_desc).")";
	$JLMS_DB->setQuery($query);
	$JLMS_DB->query();
	$id_board = $JLMS_DB->insertid();

	$keys = array( 'post_new', 'poll_view' ,'post_reply_own', 'post_reply_any', 'delete_own', 'modify_own', 'mark_any_notify', 'mark_notify','moderate_board', 'report_any', 'send_topic', 'poll_vote', 'poll_edit_own',	'poll_post', 'poll_add_own', 'post_attachment', 'lock_own', 'remove_own', 'view_attachments' );

	$query = "INSERT INTO `".$db_prefix."board_permissions` (id_group , id_board, permission, addDeny ) VALUES ";
	for ($j = 0; $j<count($keys); $j++){
		$query .= "\n ('".$id_group."', '0' , '".$keys[$j]."', '1')".(($j < (count($keys) - 1))?',':'');
	}
	$JLMS_DB->setQuery($query);
	$JLMS_DB->query();

	if ($give_course_forums[$i]->moderated && $owner_name) {
		$query = "SELECT id_member FROM `".$db_prefix."members` WHERE LOWER(member_name) = ".$JLMS_DB->Quote(strtolower($owner_name));
		$JLMS_DB->SetQuery( $query );
		$mid = $JLMS_DB->LoadResult();
		if ($mid) {
			$query = "INSERT INTO `".$db_prefix."moderators` (id_board, id_member) VALUES ('".$id_board."', '".$mid."')";
			$JLMS_DB->setQuery($query);
			$JLMS_DB->query();
		}
	}

	if ($give_course_forums[$i]->forum_moderators) {
		$curr_forum_moders = explode(',', $give_course_forums[$i]->forum_moderators);
		for ($i = 0; $i < count($curr_forum_moders); $i++) {
			$curr_forum_moders[$i] = intval($curr_forum_moders[$i]);
		}
		$curr_forum_moders = array_unique($curr_forum_moders);
		if (!empty($curr_forum_moders)) {
			foreach ($curr_forum_moders as $cfm) {
				if (isset($all_moderators[$cfm]) && $all_moderators[$cfm]->username) {
					$query = "SELECT id_member FROM `".$db_prefix."members` WHERE LOWER(member_name) = ".$JLMS_DB->Quote(strtolower($all_moderators[$cfm]->username));
					$JLMS_DB->SetQuery( $query );
					$mid = $JLMS_DB->LoadResult();
					if ($mid) {
						$query = "INSERT INTO `".$db_prefix."moderators` (id_board, id_member) VALUES ('".$id_board."', '".$mid."')";
						$JLMS_DB->setQuery($query);
						$JLMS_DB->query();
					}
				}
			}
			
		}
	}

	$give_course_forums[$i]->id_cat = $id_cat;
	$give_course_forums[$i]->id_group = $id_group;
	$give_course_forums[$i]->id_board = $id_board;
	$tmp = new stdClass();
	$tmp->group_id = $give_course_forums[$i]->group_id;
	$tmp->id_cat = $id_cat;
	$tmp->id_group = $id_group;
	$tmp->id_board = $id_board;
	$tmp->board_type = $give_course_forums[$i]->board_type;
	return $tmp;
}
function JLMS_updateForum( &$give_course_forums, $i, $course_name, $owner_name, &$all_moderators, $db_prefix, $id_cat) {
	global $JLMS_DB; //should be connected to the SMF !!!!
	$forum_name = $give_course_forums[$i]->forum_name;
	$forum_desc = $give_course_forums[$i]->forum_desc;
	$forum_name = str_replace('{course_name}', $course_name, $forum_name);
	if (isset($give_course_forums[$i]->item_title)) {
		$forum_name = str_replace('{group_name}', $give_course_forums[$i]->item_title, $forum_name);
		$forum_name = str_replace('{lpath_name}', $give_course_forums[$i]->item_title, $forum_name);
	}
	$forum_desc = str_replace('{course_name}', $course_name, $forum_desc);
	if (isset($give_course_forums[$i]->item_title)) {
		$forum_desc = str_replace('{group_name}', $give_course_forums[$i]->item_title, $forum_desc);
		$forum_desc = str_replace('{lpath_name}', $give_course_forums[$i]->item_title, $forum_desc);
	}

	if (isset($give_course_forums[$i]->id_group) && $give_course_forums[$i]->id_group) {
		$query = "UPDATE `".$db_prefix."membergroups` SET group_name = ".$JLMS_DB->Quote($forum_name)." WHERE id_group = ".$give_course_forums[$i]->id_group;
		$JLMS_DB->setQuery($query);
		$JLMS_DB->query();
	}

	$parent_id = 0;
	if ($give_course_forums[$i]->parent_forum) {
		for ($j = 0; $j < count($give_course_forums); $j++) {
			if ($give_course_forums[$j]->board_type == $give_course_forums[$i]->parent_forum) {
				if (!$give_course_forums[$i]->group_id || ($give_course_forums[$i]->group_id == $give_course_forums[$j]->group_id)) {
					if (isset($give_course_forums[$j]->id_board) && $give_course_forums[$j]->id_board) {
						$parent_id = $give_course_forums[$j]->id_board;
					}
				}
			}
		}
	}
	if (isset($give_course_forums[$i]->id_board) && $give_course_forums[$i]->id_board) {
		$query = "UPDATE `".$db_prefix."boards` SET ID_PARENT = '".$parent_id."', name = ".$JLMS_DB->Quote($forum_name).", description = ".$JLMS_DB->Quote($forum_desc)." WHERE id_board = ".$give_course_forums[$i]->id_board;
		$JLMS_DB->setQuery($query);
		$JLMS_DB->query();
		// TODO: this operation can remove moderators who was configured by SMF admin.
		$query = "DELETE FROM `".$db_prefix."moderators` WHERE id_board = ".$give_course_forums[$i]->id_board;
		$JLMS_DB->setQuery($query);
		$JLMS_DB->query();

		$owner_was_added = false;
		if ($give_course_forums[$i]->moderated && $owner_name) {
			$query = "SELECT id_member FROM `".$db_prefix."members` WHERE LOWER(member_name) = ".$JLMS_DB->Quote(strtolower($owner_name));
			$JLMS_DB->SetQuery( $query );
			$mid = $JLMS_DB->LoadResult();
			if ($mid) {
				$query = "INSERT INTO `".$db_prefix."moderators` (id_board, id_member) VALUES ('".$give_course_forums[$i]->id_board."', '".$mid."')";
				$JLMS_DB->setQuery($query);
				$JLMS_DB->query();
			}
			$owner_was_added = true;
		}
	
		if ($give_course_forums[$i]->forum_moderators) {
			$curr_forum_moders = explode(',', $give_course_forums[$i]->forum_moderators);
			for ($j = 0; $j < count($curr_forum_moders); $j++) {
				$curr_forum_moders[$j] = intval($curr_forum_moders[$j]);
			}
			$curr_forum_moders = array_unique($curr_forum_moders);
			if (!empty($curr_forum_moders)) {
				foreach ($curr_forum_moders as $cfm) {
					if (isset($all_moderators[$cfm]) && $all_moderators[$cfm]->username && (($owner_was_added && $all_moderators[$cfm]->username != $owner_name) || !$owner_was_added)) {
						$query = "SELECT id_member FROM `".$db_prefix."members` WHERE LOWER(member_name) = ".$JLMS_DB->Quote(strtolower($all_moderators[$cfm]->username));
						$JLMS_DB->SetQuery( $query );
						$mid = $JLMS_DB->LoadResult();
						if ($mid) {
							$query = "INSERT INTO `".$db_prefix."moderators` (id_board, id_member) VALUES ('".$give_course_forums[$i]->id_board."', '".$mid."')";
							$JLMS_DB->setQuery($query);
							$JLMS_DB->query();
						}
					}
				}
				
			}
		}
	}

	$give_course_forums[$i]->was_updated = 1;
	return $give_course_forums[$i]->id;
}
?>