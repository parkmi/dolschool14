<?php
/**
* joomla_lms.course_forum.php
* (c) JoomaLMS eLearning Software http://www.joomlalms.com/
**/

// no direct access
defined( '_JLMS_EXEC' ) or die( 'Restricted access' );

require_once(_JOOMLMS_FRONT_HOME . "/joomla_lms.course_forum.html.php");
$task 	= mosGetParam( $_REQUEST, 'task', '' );

if ($task != 'login_to_forum') {
	global $JLMS_CONFIG;
	$course_id = $JLMS_CONFIG->get('course_id',0);
	$pathway = array();
	$pathway[] = array('name' => _JLMS_PATHWAY_HOME, 'link' => sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid"), 'is_home' => true);
	$pathway[] = array('name' => $JLMS_CONFIG->get('course_name'), 'link' => sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid&amp;task=details_course&amp;id=$course_id"), 'is_course' => true);
	$pathway[] = array('name' => _JLMS_TOOLBAR_FORUM, 'link' => sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid&amp;task=course_forum&amp;id=$course_id"));
	JLMSAppendPathWay($pathway);
	JLMS_ShowHeading();
}

$id 	= intval( mosGetParam( $_REQUEST, 'id', 0 ) );

switch ($task) {
	case 'course_forum':	
		if( !JLMS_show_course_forum( $id, $option ) ) 
		{
			JLMSRedirect(sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=details_course&id=$id"));
		}
	break;			
	case 'login_to_forum':	JLMS_forum_SetSMFCookie( $id, $option );break;
}
function JLMS_forum_SetSMFCookie( $id, $option ) {
	global $my, $JLMS_DB, $Itemid, $JLMS_CONFIG;
		
	if ($JLMS_CONFIG->get('plugin_forum')) {
		$query = "SELECT add_forum FROM #__lms_courses WHERE id = $id";
		$JLMS_DB->SetQuery( $query );
		$is_c_forum = $JLMS_DB->LoadResult();
		
		if ($is_c_forum) {			
			$username = strval(mosGetParam ($_REQUEST,'username',''));
			$password = strval(mosGetParam ($_REQUEST,'passwd',''));

			$query = "SELECT id, name, username, password, usertype, block"
			. "\n FROM #__users"
			. "\n WHERE username = ". $JLMS_DB->Quote( $username ) . " AND id = $my->id";
			;
			$JLMS_DB->setQuery( $query );
			$row = $JLMS_DB->loadObject();
			$is_loaded_user = false;

			if (is_object($row)) {
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
					$query = "SELECT * FROM `#__users` WHERE id = $row->id";
					$JLMS_DB->setQuery($query);
					$user = $JLMS_DB->loadObject();
					$is_loaded_user = true;
				}
			}
							
			if ($is_loaded_user && isset($user->id)){												
				JLMS_createForumUser( $user, $password );				
			}

			JLMSRedirect(sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=course_forum&id=$id"));
		} else {
			JLMSRedirect(sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=details_course&id=$id"));
		}
	} else {
		JLMSRedirect(sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=details_course&id=$id"));
	}
}

function JLMS_show_course_forum( $id, $option ) {
	/*
	board_type = 1 - Public/Group course board
	board_type = 2 - LearningPath public course board
	board_type = 3 - Board for users from 'Global group'
	board_type = 4 - Private (teachers-only) boards
	board_type = 5 - Private (teachers-only) LearningPath boards
	*/
	global $my, $JLMS_DB, $Itemid, $JLMS_CONFIG;
	
	$doc = & JFactory::getDocument();

	$usertype = JLMS_GetUserType($my->id, $id);
	if (!$usertype) {
		JLMSRedirect(sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=details_course&id=$id"));
	}

	$JLMS_ACL = & JLMSFactory::getACL();
	$forum = & JLMS_SMF::getInstance();
	
	if( !$forum )
		return false;
		
	$id_cat = 0;
	$course_name = '';
	$owner_name = '';
	$private_forums_tobe_created = array(); // list of private forums to be created
	$msg = '';//error message - prompt to login if not blank

	$query = "SELECT * FROM #__lms_forums WHERE published = 1";
	$JLMS_DB->setQuery($query);
	$lms_forum_types = $JLMS_DB->loadObjectList('id');
	//[TODO] - check all types of the forums and create/remove necessary forum boards in the SMF - i.e. if they wasn't be created/removed previously
	// Note, autocreation is necessary only for 'global groups' forums and private forums (`access` is 1)
	$give_course_forums = array();
	$need_to_create = false;
	$need_to_update = false;
	if (count($lms_forum_types)) {
		// check all forum types for access.
		$allowed_forum_types = array(); // allowed forum types
		$lms_forum_types_ids = array();
		foreach ($lms_forum_types as $lft) {
			$do_proceeed = true;

			// firstly - check `access` and `permissions`
			switch(intval($lft->forum_access)) {
				case 0:
					// any user
				break;
				case 1:
					// check user role
					if ($lft->forum_permissions) {
						$allowed_roles = explode(',', $lft->forum_permissions);
						if ( $JLMS_ACL->GetRole() && in_array($JLMS_ACL->GetRole(), $allowed_roles) ) {
							// allow access
						} else { // restrict access if this forum is not permissioned.
							$do_proceeed = false;
						}
					} else { // if this is a restricted area but there is no roles configured
						$do_proceeed = false;
					}
				break;
			}
			if (!$do_proceeed) { continue; }
			// secondly - check `user_level` permissions
			switch(intval($lft->user_level)) {
				case 0: // any user
				break;

				case 1: // local usergroup forum
				case 2: // global usergroup forum
					if ($lft->user_level == 1 && $JLMS_CONFIG->get('use_global_groups', 1)) {
						$do_proceeed = false; // restrict access to the local-group forum, if we are in the global mode
					} elseif ($lft->user_level == 2 && !$JLMS_CONFIG->get('use_global_groups', 1)) {
						$do_proceeed = false; // restrict access to the global-group forum, if we are in the local mode
					}
				break;
			}
			if (!$do_proceeed) { continue; }
			$allowed_forum_types[$lft->id] = $lft; // we have a list of allowed forum types here.
			$lms_forum_types_ids[] = $lft->id;
		}
		if (count($lms_forum_types_ids)) {
			$query = "SELECT id, course_id, board_type, group_id, ID_GROUP AS id_group, ID_CAT AS id_cat, ID_BOARD AS id_board, is_active, need_update FROM #__lms_forum_details"
			 . "\n WHERE course_id = $id"
			 //. "\n  AND is_active = 1"
			 . "\n AND board_type IN (".implode(',',$lms_forum_types_ids).")";
			$JLMS_DB->setQuery($query);
			$active_course_forums = $JLMS_DB->loadObjectList();
			for ($i = 0; $i < count($active_course_forums); $i++) {				
				$active_course_forums[$i]->forum_level = $lms_forum_types[$active_course_forums[$i]->board_type]->forum_level;
				$active_course_forums[$i]->user_level = $lms_forum_types[$active_course_forums[$i]->board_type]->user_level;
				$active_course_forums[$i]->forum_access = $lms_forum_types[$active_course_forums[$i]->board_type]->forum_access;
				$active_course_forums[$i]->forum_permissions = $lms_forum_types[$active_course_forums[$i]->board_type]->forum_permissions;
				$active_course_forums[$i]->parent_forum = $lms_forum_types[$active_course_forums[$i]->board_type]->parent_forum;
				$active_course_forums[$i]->forum_moderators = $lms_forum_types[$active_course_forums[$i]->board_type]->forum_moderators;
				$active_course_forums[$i]->forum_name = $lms_forum_types[$active_course_forums[$i]->board_type]->forum_name;
				$active_course_forums[$i]->forum_desc = $lms_forum_types[$active_course_forums[$i]->board_type]->forum_desc;
				$active_course_forums[$i]->moderated = $lms_forum_types[$active_course_forums[$i]->board_type]->moderated;
				// !!!! ATTENTION, `need_update` field was moved to the `lms_forum_details` table //$active_course_forums[$i]->need_update = $lms_forum_types[$active_course_forums[$i]->board_type]->need_update;
			}
			// [TODO] - inspect each forum type and populate list of necessary forums (to be created or already created)
			$give_course_forums = array();			
			foreach ($allowed_forum_types as $aft) {
				switch(intval($aft->forum_level)) {
					case 0:
						switch(intval($aft->user_level)) {
							case 0:
								//one forum should be created for course.								
								$forum->populateCourseForums( $id, $give_course_forums, $active_course_forums, $aft );		
							break;
							case 1:
								$forum->populateLgroupForums( $id, $give_course_forums, $active_course_forums, $aft );
							break;
							case 2:
								$forum->populateGgroupForums( $id, $give_course_forums, $active_course_forums, $aft );
							break;
						}
					break;
					case 1:
						if (intval($aft->user_level) == 0) {
							$forum->populateLpathForums( $id, $give_course_forums, $active_course_forums, $aft );			
						} else {
							//ignore any other forums - currently there is no posibility to have such difficult structure
						}
					break;
				}
			}
														
			if (count($give_course_forums)) {
				$need_to_create = false;
				for ($i = 0; $i < count($give_course_forums); $i++) {
					if ($give_course_forums[$i]->id == 0) {
						$need_to_create = true;
					}
					if (isset($give_course_forums[$i]->need_update) && $give_course_forums[$i]->need_update) {
						$need_to_update = true;
					}
					if ($need_to_create && $need_to_update) {
						break;
					}
				}
				
			}
		}
	}
	$was_created = array();
	$was_updated = array();
	$newcat_wascreated = false;

	if (count($give_course_forums)) {
		//echo '<pre>';var_dump($give_course_forums);die;
		if ($need_to_create || $need_to_update) {
			$query = "SELECT a.course_name, a.owner_id, b.username as owner_name FROM #__lms_courses as a LEFT JOIN #__users as b ON a.owner_id = b.id WHERE a.id = $id";
			$JLMS_DB->setQuery($query);
			$course_info = $JLMS_DB->loadObject();
			if (is_object($course_info)) {
				$course_name = $course_info->course_name;// - we need it to create boards
				$owner_name = $course_info->owner_name;
			}
		}
		$query = "SELECT ID_CAT AS id_cat FROM #__lms_forum_details WHERE course_id = $id LIMIT 0,1";
		$JLMS_DB->setQuery($query);
		$id_cat = $JLMS_DB->loadResult();// ID of the Course CATEGORY (in which all course boards are placed) - we need it to create boards
		// TODO:
		// check if SMF boards was removed and create new ones if necessary!
		$query = "SELECT distinct ID_GROUP AS id_group FROM `#__lms_forum_details` ";
		$JLMS_DB->setQuery($query);
		$all_lms_groups = $JLMS_DB->loadResultArray();
		$all_moderator_ids = array();
		$all_moderators = array();
		for ($i = 0; $i < count($give_course_forums); $i++) {
			if ((($need_to_create && $give_course_forums[$i]->id == 0) || $give_course_forums[$i]->need_update) && $give_course_forums[$i]->forum_moderators) {
				$new_moderators = explode(',', $give_course_forums[$i]->forum_moderators);
				$all_moderator_ids = array_merge($all_moderator_ids, $new_moderators);
			}
		}
		if (count($all_moderator_ids)) {
			for ($i = 0; $i < count($all_moderator_ids); $i++) {
				$all_moderator_ids[$i] = intval($all_moderator_ids[$i]);
			}
			$all_moderator_ids = array_unique($all_moderator_ids);
			if (!empty($all_moderator_ids)) {
				$all_moderator_ids_str = implode(',',$all_moderator_ids);
				$query = "SELECT id, username FROM #__users WHERE id IN ($all_moderator_ids_str)";
				$JLMS_DB->setQuery($query);
				$all_moderators = $JLMS_DB->LoadObjectList('id');
			}
		}		
		if (!$id_cat) {
			// CATEGORY for this course doesn't exists - we need to create new one
			$storeData = array();			
			$storeData['name'] = $course_name;
			$storeData['can_collapse'] = 1; 			
			$id_cat = $forum->storeCategory( $storeData );
			 
			$newcat_wascreated = true;
			// TODO:
			// if instead CAT_ID #__forum_details was populated with 0 - we need to update #__forum_details table and boards in SMF db
			// all updates to Joola db are 100 - 150 lines below
		}
				
		$user_exists = false;
		$is_forum_category = 0;
		$boardurl = $forum->getBoardURL();
		$link = $boardurl.'/index.php#'.$id_cat;
		$smf_user_details = $forum->loadMemberByName( $my->username );
		if ( $smf_user_details ) {
			$user_exists = true;
			$all_current_smf_groups = array();
			// create all parent forums
			for ($i = 0; $i < count($give_course_forums); $i++) {				
				if ($need_to_create && $give_course_forums[$i]->id == 0 && !$give_course_forums[$i]->parent_forum) {
					if ( ($give_course_forums[$i]->forum_level == 1 && $give_course_forums[$i]->user_level == 0) ||
					($give_course_forums[$i]->forum_level == 0 && $give_course_forums[$i]->user_level == 2) || ($give_course_forums[$i]->forum_level == 0 && $give_course_forums[$i]->user_level == 0)	) {

						$tmp = $forum->create( $give_course_forums, $i, $course_name, $owner_name, $all_moderators, $id_cat);
						$was_created[] = $tmp;
						$all_current_smf_groups[] = $tmp->id_group;

					}
				} elseif ($give_course_forums[$i]->id && $give_course_forums[$i]->need_update && !$give_course_forums[$i]->parent_forum) {
					$tmp = $forum->update( $give_course_forums, $i, $course_name, $owner_name, $all_moderators, $id_cat);
					$was_updated[] = $tmp;
					$all_current_smf_groups[] = $give_course_forums[$i]->id_group;
				} elseif ($give_course_forums[$i]->id && !$give_course_forums[$i]->need_update) {
					$all_current_smf_groups[] = $give_course_forums[$i]->id_group;
				}
			}
			//crate all nested forums
			for ($i = 0; $i < count($give_course_forums); $i++) {
				if ($need_to_create && $give_course_forums[$i]->id == 0 && $give_course_forums[$i]->parent_forum) {
					if ( ($give_course_forums[$i]->forum_level == 1 && $give_course_forums[$i]->user_level == 0) ||
					($give_course_forums[$i]->forum_level == 0 && $give_course_forums[$i]->user_level == 2)	|| ($give_course_forums[$i]->forum_level == 0 && $give_course_forums[$i]->user_level == 0)) {

						$tmp = $forum->create( $give_course_forums, $i, $course_name, $owner_name, $all_moderators, $id_cat);
						$was_created[] = $tmp;
						$all_current_smf_groups[] = $tmp->id_group;

					}
				} elseif ($give_course_forums[$i]->id && $give_course_forums[$i]->need_update && $give_course_forums[$i]->parent_forum) {
					$tmp = $forum->update( $give_course_forums, $i, $course_name, $owner_name, $all_moderators, $id_cat);
					$was_updated[] = $tmp;
					$all_current_smf_groups[] = $give_course_forums[$i]->id_group;
				}
			}
			$mem_id = $smf_user_details->id_member;
			$mem_real_name = $smf_user_details->real_name;
			$primary_group = $smf_user_details->id_group;
			$old_groups = explode(',', $smf_user_details->additional_groups);
			$old_groups_save = array();
			foreach($old_groups as $group){
				if (!in_array($group, $all_lms_groups)){
					$old_groups_save[] = $group;
				}
			}
			$forum_groups = array_unique(array_merge($all_current_smf_groups, $old_groups_save));

			$new_forum_groups = array();
			foreach ($forum_groups as $fg) {
				if ($fg) {
					$new_forum_groups[] = $fg;
				}
			}

			$groups = implode(',', $new_forum_groups);
			
			if (!$mem_real_name && isset($my->name) && $my->name) {
				// update real_name of user, if it is missed
				$storeData = array();		
				$storeData['id_member'] = $mem_id;		
				$storeData['id_group'] = $primary_group;
				$storeData['additional_groups'] = $groups;
				$storeData['real_name'] = $my->name;				
			} else {
				$storeData = array();
				$storeData['id_member'] = $mem_id;
				$storeData['id_group'] = $primary_group;
				$storeData['additional_groups'] = $groups;
			}
			
			$forum->storeMember( $storeData );

			if (count($give_course_forums) == 1) {
				$is_forum_category = 0;
				$link = $boardurl.'/index.php?board='.$give_course_forums[0]->id_board.'.0';
			} elseif (count($give_course_forums) > 1) {
				$is_forum_category = 1;
				$link = $boardurl.'/index.php#'.$id_cat;
			}
			
			$topic_id = JRequest::getVar('topic_id', 0);
			$message_id = JRequest::getVar('message_id', 0);
			if($topic_id && $message_id){
				$link = $boardurl.'/index.php';
				$link .= '?topic='.$topic_id;
				$link .= '.msg'.$message_id;
				$link .= '#msg'.$message_id;
			}

			if (true) {//($is_forum_category) {				
				$forum_tree = $forum->selectBoards();
				$user_cats = array();
				foreach ($forum_tree as $ft) {
					$ar = $ft->member_groups;
					$ar = explode(',',$ar);
					$is_cat = false;
					foreach ($new_forum_groups as $gia) {
						if (in_array($gia, $ar)) { $is_cat = true; break; }
					}
					if ($is_cat) {
						$user_cats[] = $ft->id_cat;
					}
				}
				$user_cats = array_unique($user_cats);
				if (count($user_cats)) {									
						
					$forum->deleteCollapsedCategories( $mem_id, $user_cats );
															
					$new_ar = array($id_cat);
					$rrr = array_diff($user_cats, $new_ar);
					$forum->insertCollapsedCategories( $mem_id, $rrr );
				}
			}
		}
		$mem_id_cookies = 0;
		if ($user_exists) {
			$mem_id_cookies = JLMS_checkSMF_cookies(false);
		}
				
		if ($need_to_create) {
			if (count($was_created)) {
				$query = "INSERT INTO #__lms_forum_details (course_id, board_type, group_id, ID_GROUP, ID_CAT, ID_BOARD, is_active) VALUES";
				$first = 1;
				foreach ($was_created as $obj) {
					$query .= ($first) ? '' : ',';
					$query .= "\n($id, $obj->board_type, $obj->group_id, $obj->id_group, $obj->id_cat, $obj->id_board, 1)";
					$first = 0;
				}
				$JLMS_DB->setQuery($query);
				$JLMS_DB->query();
			}
		}
		if (isset($was_updated) && count($was_updated)) {
			$was_updated = array_unique($was_updated);
			if (!empty($was_updated)) {
				$was_updated_str = implode(',',$was_updated);
				$query = "UPDATE #__lms_forum_details SET need_update = 0 WHERE id IN ($was_updated_str) AND need_update = 1";
				$JLMS_DB->setQuery($query);
				$JLMS_DB->query();
			}
		}

		if ($user_exists) {			
			if ($mem_id && $mem_id_cookies && $mem_id_cookies == $mem_id) {				
				$mbname = $forum->getMbname();				
				$doc->setTitle( $mbname );
				//[TODO] - what is it - $mbname and $boardurl - from the Settings.php?
			} else { $msg = JLMS_FORUM_NOT_MEMBER; }
		} else {
			$msg = JLMS_FORUM_NOT_MEMBER;
		}
	} else {
		JLMSRedirect(sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=details_course&id=$id"));
	}
	JLMS_course_forum_html::wrapper_course_forum( $link, $option, $id, $msg );
	
	return true; 
}

function JLMS_createForumUser( $user, $password ) {
	global $my, $JLMS_DB, $JLMS_CONFIG;
	
	$forum = & JLMS_SMF::getInstance();
	
	$error = '';
	
	$query = "SELECT distinct a.ID_GROUP AS id_group FROM `#__lms_forum_details` as a, `#__lms_users_in_groups` as b WHERE a.course_id = b.course_id AND (b.group_id = a.group_id OR a.group_id = '0') AND b.user_id='".$my->id."' AND a.is_active = 1";
	$JLMS_DB->setQuery($query);
	$forum_groups_stu = $JLMS_DB->loadResultArray();
	$error = $JLMS_DB->getErrorMsg()."\n";

	$query = "SELECT distinct a.ID_GROUP AS id_group FROM `#__lms_forum_details` as a, `#__lms_user_courses` as b WHERE a.course_id = b.course_id AND b.user_id='".$my->id."' AND a.is_active = 1";
	$JLMS_DB->setQuery($query);
	$forum_groups_tea = $JLMS_DB->loadResultArray();
	$error .= $JLMS_DB->getErrorMsg()."\n";

	$query = "SELECT distinct a.ID_BOARD AS id_board FROM `#__lms_forum_details` as a, `#__lms_user_courses` as b WHERE a.course_id = b.course_id AND b.user_id='".$my->id."' AND a.is_active = 1";
	$JLMS_DB->setQuery($query);
	$forum_boards_tea = $JLMS_DB->loadResultArray();
	$error .= $JLMS_DB->getErrorMsg()."\n";

	$forum_groups = array_unique(array_merge($forum_groups_stu, $forum_groups_tea));
	$cb_info = array();	
	if ($JLMS_CONFIG->get('is_cb_installed')) {
		$fields = array ('website', 'ICQ', 'AIM', 'YIM','MSN', 'location');
		$fields_isset = array();
		foreach ($fields as $field) {
			if ($JLMS_CONFIG->get('jlms_cb_'.$field)) {
				$fields_isset[] = $JLMS_CONFIG->get('jlms_cb_'.$field);
			}
		}
		if (!empty($fields_isset)) {
			$fields_str = implode(',', $fields_isset );
			$query = "SELECT name FROM `#__comprofiler_fields` WHERE fieldid IN ($fields_str) ";
			$JLMS_DB->setQuery($query);
			$field_name = $JLMS_DB->loadResultArray();
			$error .= $JLMS_DB->getErrorMsg()."\n";
			$field_names = implode(',', $field_name);

			$query = "SELECT ".$field_names." FROM `#__comprofiler` WHERE user_id=".$user->id;
			$JLMS_DB->setQuery($query);
			$cb_user = $database->loadResultArray();
			if ( isset($cb_user[0]) ) {
				$cb_info = array_values( $cb_user );
			}
			$error .= $JLMS_DB->getErrorMsg()."\n";
		}
	}

	$forum_groups = array_unique(array_merge($forum_groups_stu, $forum_groups_tea));

	if (count($forum_groups)){
		$groups = implode(',', $forum_groups);
	} else {
		$groups = '';
		$forum_groups = array();
	}
	$query = "SELECT distinct ID_GROUP AS id_group FROM `#__lms_forum_details` ";
	$JLMS_DB->setQuery($query);
	$all_lms_groups = $JLMS_DB->loadResultArray();
	$error .= $JLMS_DB->getErrorMsg()."\n";
	
	$smf_user = $forum->loadMemberByName( $my->username );
		
	$primary_group = 0;	
		
	if (is_object( $smf_user )){
						
		$mem_id = $smf_user->id_member;
		$primary_group = $smf_user->id_group;
		$old_groups = explode(',', $smf_user->additional_groups);
		$old_group_s = array();
		foreach($old_groups as $group){
			if (!in_array($group, $all_lms_groups)){
				$old_group_s[] = $group;
			}
		}
		$forum_groups = array_unique(array_merge($forum_groups, $old_group_s));
		$groups = implode(',', $forum_groups);				
		
		$smf_password = sha1(strtolower($my->username) .$password);
		$storeData = array();
		$storeData['id_member'] = $mem_id;
		$storeData['passwd'] = $smf_password;
		$storeData['id_group'] = $primary_group;
		$storeData['additional_groups'] = $groups;
		$storeData = array_merge( $storeData, $cb_info );
				 		
		$forum->storeMember( $storeData );				 	
				
	} else {						
		$mem_id = $forum->registerOnForum( $user, $password, $groups, $cb_info);
	}

	$forum->setLoginCookie15( $mem_id, $password );
}

function JLMS_checkSMF_cookies( $reconnect = true ) {
	global $JLMS_CONFIG, $JLMS_DB;
	$id_member = 0;
	$password = '';
		
	$forum = & JLMS_SMF::getInstance();
	$cookiename = $forum->getCookieName();
	
	if (isset($_COOKIE[$cookiename])) {
		
		$cookie_forum = stripslashes($_COOKIE[$cookiename]);
		if (preg_match('~^a:[34]:\{i:0;(i:\d{1,6}|s:[1-8]:"\d{1,8}");i:1;s:(0|40):"([a-fA-F0-9]{40})?";i:2;[id]:\d{1,14};(i:3;i:\d;)?\}$~', $cookie_forum) == 1) {
			list ($id_member, $password) = @unserialize($cookie_forum);
			$id_member = !empty($id_member) && strlen($password) > 0 ? (int) $id_member : 0;
		} else {
			$id_member = 0;
		}
	}
	if ( $id_member ) {	
		$user_data = $forum->selectMembers( array($id_member) );
		
		$user_settings = array();
		if ( $user_data ) $user_settings = $user_data[0];		
		if (!empty($user_settings)) {
			// SHA-1 passwords should be 40 characters long.
			if (strlen($password) == 40) {
				$check = sha1($user_settings->passwd . $user_settings->password_salt) == $password;
			} else {
				$check = false;
			}
			// Wrong password or not activated - either way, you're going nowhere.
			//$id_member = $check && ($user_settings['is_activated'] == 1 || $user_settings['is_activated'] == 11) ? $user_settings['id_member'] : 0;
			$id_member = $check ? $user_settings->id_member : 0;
		} else {
			$id_member = 0;
		}
	}
	return $id_member;
}

?>