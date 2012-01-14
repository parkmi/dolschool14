<?php
/**
* joomla_lms.homework.php
* (c) JoomaLMS eLearning Software http://www.joomlalms.com/
**/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
require_once(_JOOMLMS_FRONT_HOME . "/joomla_lms.homework.html.php");

define( '_ACTIVITY_TYPE_OFFLINE', 1 );
define( '_ACTIVITY_TYPE_WRITE', 2 );
define( '_ACTIVITY_TYPE_UPLOAD', 3 );

define( '_STATUS_NOT_SELECT', 'not_select' );
define( '_STATUS_INCOMPLETE', 'incomplete' );
define( '_STATUS_NOT_PASSED', 'not_passed' );
define( '_STATUS_PASSED', 'passed' );

	global $JLMS_CONFIG;
	$course_id = $JLMS_CONFIG->get('course_id',0);
	$pathway = array();
	$pathway[] = array('name' => _JLMS_PATHWAY_HOME, 'link' => sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid"), 'is_home' => true);
	$pathway[] = array('name' => $JLMS_CONFIG->get('course_name'), 'link' => sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid&amp;task=details_course&amp;id=$course_id"), 'is_course' => true);
	$pathway[] = array('name' => _JLMS_TOOLBAR_HOMEWORK, 'link' => sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid&amp;task=homework&amp;id=$course_id"));
	JLMSAppendPathWay($pathway);

JLMS_ShowHeading();
$id 	= intval( mosGetParam( $_REQUEST, 'id', 0 ) ); 
$task 	= mosGetParam( $_REQUEST, 'task', '' );

switch ($task) {
	case 'homework':			JLMS_showHomeWork( $id, $option );		break;
	case 'hw_create':			JLMS_editHW( 0, $id, $option );			break;
	case 'hw_edit':		$cid = mosGetParam( $_POST, 'cid', array(0) );
				if (!is_array( $cid )) { $cid = array(0); }
				JLMS_editHW( $cid[0], $id, $option );					break;
	case 'hw_save':				JLMS_saveHW( $option );					break;
	case 'hw_delete':			JLMS_deleteHW( $id, $option );			break;
	case 'hw_cancel':			JLMS_cancelHW( $option );				break;
	case 'hw_change':			JLMS_changeHW( $id, $option );			break;
	case 'hw_tchange':			JLMS_tchangeHW( $id, $option );			break;
	case 'hw_view':				JLMS_viewHW( $id, $option );			break;
	case 'hw_stats':			JLMS_statsHW( $id, $option );			break;
	case 'hw_publish':			JLMS_publishHW( $id, $option );			break;
	case 'hw_uploadfile':		JLMS_uploadFileHW( $option );			break;
	case 'hw_downloadfile':		JLMS_downloadFileHW( $option );			break;
	case 'hw_view_result':		JLMS_viewHWResult( $option );			break;
	case 'hw_save_result':		JLMS_saveHWResult( $option );			break;	
	
}
function JLMS_statsHW( $id, $option ) {
	$JLMS_CONFIG = & JLMSFactory::getConfig();
	$JLMS_SESSION = & JLMSFactory::getSession();
	$Itemid = $JLMS_CONFIG->get('Itemid');
	$db = & JFactory::getDbo();
	$user = JLMSFactory::getUser();
	$my_id = $user->get('id');
	$JLMS_ACL = & JLMSFactory::getACL();

	$course_id = intval(mosGetParam($_REQUEST, 'course_id', 0));
	$hw_id = intval(mosGetParam($_REQUEST, 'hw_id', 0));

	$id = ( $hw_id )?$hw_id:$id;

	$usertype = JLMS_GetUserType($my_id, $course_id);

//	if ( $course_id && ($usertype == 1 || $usertype == 6) && ($id && (JLMS_GetHWCourse($id) == $course_id)) ) {
	if ( $course_id && ( ($JLMS_ACL->CheckPermissions('homework', 'view_stats') && !$JLMS_ACL->isStaff()) || ($JLMS_ACL->CheckPermissions('homework', 'view_stats') && $JLMS_ACL->isStaff())) && ($id && (JLMS_GetHWCourse($id) == $course_id)) ) {
		$limit		= intval( mosGetParam( $_GET, 'limit', $JLMS_SESSION->get('list_limit', $JLMS_CONFIG->get('list_limit')) ) );
		$JLMS_SESSION->set('list_limit', $limit);
		if ($usertype == 1 || $JLMS_ACL->isStaff()) {
			$filt_hw = intval( mosGetParam( $_GET, 'filt_hw', $JLMS_SESSION->get('filt_hw', 2) ) );
			$filt_group = intval( mosGetParam( $_GET, 'filt_group', $JLMS_SESSION->get('filt_group', 0) ) );
			$filt_subgroup = intval( mosGetParam( $_GET, 'filt_subgroup', $JLMS_SESSION->get('filt_subgroup', 0) ) );
			$filter_stu = intval( mosGetParam( $_REQUEST, 'filter_stu', $JLMS_SESSION->get('filter_stu_h', 0) ) );
			if ($JLMS_ACL->isStaff()) {
				$filt_group = 0;
				$filt_subgroup = 0;
				$filter_stu = 0;
			}
		} else {
			$filt_hw = 0;
			$filt_group = 0;
			$filt_subgroup = 0;
			$filter_stu = 0;
		}
		$JLMS_SESSION->set('filt_hw', $filt_hw);
		if ($filt_group != $JLMS_SESSION->get('filt_group', 0)) {
			$filter_stu = 0;
		}
		
		if(!$filt_group) {
			$filt_subgroup = 0;
		}
		
		$JLMS_SESSION->set('filt_group', $filt_group);
		$JLMS_SESSION->set('filt_subgroup', $filt_subgroup);
		
		$JLMS_SESSION->set('filter_stu_h', $filter_stu);
		$limitstart = intval( mosGetParam( $_GET, 'limitstart', $JLMS_SESSION->get('limitstart_hw', 0) ) );
		$JLMS_SESSION->set('limitstart_hw', $limitstart);
		$query = "SELECT * FROM #__lms_homework WHERE id = '".$id."'"
				. (($JLMS_ACL->CheckPermissions('homework', 'view_all')) ? '' : "\n AND published = 1");
		$db->SetQuery( $query );
		$hw_info = $db->LoadObjectList();
		
		$members = "'0'";
		$groups_where_admin_manager = "'0'";

		//TODO: where is _role_type == 5 for assistants ???
		//TODO: where is CEO? they are limited to assigned groups !!!
		//if($JLMS_ACL->_role_type == 2 || $JLMS_ACL->_role_type == 4 || $JLMS_ACL->_role_type == 5) { // teacher or admin
		if($JLMS_ACL->CheckPermissions('advanced', 'assigned_groups_only')) { // limited stats for organization admins/teachers
			$groups_where_admin_manager = JLMS_ACL_HELPER::GetAssignedGroups($my_id, $course_id);	
			if(count($groups_where_admin_manager)) {
				if(JLMS_ACL_HELPER::GetCountAssignedGroups($my_id, $course_id) == 1) {	
					$filt_group = $groups_where_admin_manager[0];
					if ($JLMS_ACL->isStaff()) {
						$filt_group = 0;
					}
				}
			}
			if(count($groups_where_admin_manager)) {
				$groups_where_admin_manager = implode(',', $groups_where_admin_manager);
				if($groups_where_admin_manager != '') {
					$query = "SELECT user_id FROM #__lms_users_in_global_groups WHERE (group_id IN ($groups_where_admin_manager) OR subgroup1_id IN ($groups_where_admin_manager)) AND user_id > 0"
						. ($filt_group ? ("\n AND group_id = '".$filt_group."'") : '')
						. ($filt_subgroup ? ("\n AND subgroup1_id = '".$filt_subgroup."'") : '')
					;
					$db->setQuery($query);
					$members = $db->loadResultArray();
					//$members = implode(',', $members);
					//if($members == '') {
					//	$members = "'0'";
					//}
				}
				$users_where_ceo_parent = array();
				if($JLMS_ACL->_role_type == 3) {
					$query = "SELECT user_id FROM `#__lms_user_parents` WHERE parent_id = '".$my_id."'"
					;
					$db->setQuery($query);
					$users_where_ceo_parent = $db->loadResultArray();
					
					//$members = array_merge($members, $users_where_ceo_parent);
				}
				if($members != "'0'" && count($users_where_ceo_parent)) {
					$members = array_merge($members, $users_where_ceo_parent);
				}
				elseif(count($users_where_ceo_parent)) {
					$members = $users_where_ceo_parent;
				}
					
				$members = implode(',', $members);
				if($members == '') {
					$members = "'0'";
				}		
			} else {
				$groups_where_admin_manager = "'0'";
			}
		}
		//}

		if($JLMS_ACL->isStaff() && $JLMS_ACL->CheckPermissions('homework', 'view_stats')){//ceo

			$staff_learners = ($JLMS_ACL->_role_type == 3 && isset($JLMS_ACL->_staff_learners))?$JLMS_ACL->_staff_learners:array();
			$str_staff_learners = implode(",", $staff_learners);	

			//NOTE: $filt_group is always == 0 for Staffs, also they don't have a filter by group - therefore we can use the same query for both 'global' and 'local' groups modes
			//if($JLMS_CONFIG->get('use_global_groups', 1)){				
				$query = "SELECT count(u.id)"
				. "\n FROM (#__users as u, #__lms_homework as d) LEFT JOIN #__lms_homework_results as c ON c.hw_id = d.id AND c.user_id = u.id, #__lms_users_in_groups as a"
				. ($filt_group ? ("\n, #__lms_users_in_global_groups as uigg, #__lms_usergroups AS b") : '')	
				. "\n WHERE d.id = '".$id."' AND a.user_id = u.id AND a.course_id = ".$course_id
				. "\n AND u.id IN (".$str_staff_learners.")"				
				. ($filt_group ? ("\n AND uigg.group_id = '".$filt_group."' AND a.group_id = uigg.group_id AND b.id = uigg.group_id AND b.course_id = 0") : '')				
				. ($filter_stu ? ("\n AND u.id = '".$filter_stu."'") : '')
				. ($filt_hw ? ( ($filt_hw == 2) ? "\n AND c.hw_status = 1" : (($filt_hw == 1)?" AND (c.hw_status IS NULL OR c.hw_status <> 1)":"")) : '')				
				. (($JLMS_ACL->CheckPermissions('homework', 'view_all')) ? '' : "\n AND d.published = 1")				
				;					
			/*} else {
				$query = "SELECT count(u.id)"
				. "\n FROM #__lms_homework as d, #__users as u, #__lms_user_parents as p, #__lms_homework_results as c, #__lms_users_in_groups as a"											
				. "\n WHERE d.id = '".$id."' AND c.hw_id = d.id AND c.user_id = u.id AND u.id = p.user_id AND p.parent_id = '".$my_id."' AND a.user_id = u.id AND a.course_id = ".$course_id	
				. ($filt_group ? ("\n AND a.group_id = '".$filt_group."'") : '')				
				. ($filter_stu ? ("\n AND u.id = '".$filter_stu."'") : '')
				. ($filt_hw ? ( ($filt_hw == 2) ? "\n AND c.hw_status = 1" : (($filt_hw == 1)?" AND (c.hw_status IS NULL OR c.hw_status <> 1)":"")) : '')
				. (($JLMS_ACL->CheckPermissions('homework', 'view_all')) ? '' : "\n AND d.published = 1")				
				;	
			}*/
		} elseif($JLMS_ACL->CheckPermissions('homework', 'view_stats')){ //teacher or admin
			if($JLMS_CONFIG->get('use_global_groups', 1)){
				$query = "SELECT count(u.id)"
				. "\n FROM (#__users as u, #__lms_homework as d) LEFT JOIN #__lms_homework_results as c ON c.hw_id = d.id AND c.user_id = u.id, #__lms_users_in_groups as a "
				. ($filt_group ? ("\n, #__lms_users_in_global_groups as uigg, #__lms_usergroups AS b") : '')	
				. "\n WHERE d.id = '".$id."' AND a.user_id = u.id AND a.course_id = ".$course_id				
				. ($filt_group ? ("\n AND uigg.group_id = '".$filt_group."' AND a.group_id = uigg.group_id AND b.id = uigg.group_id AND b.course_id = 0") : '')				
				. ($filter_stu ? ("\n AND u.id = '".$filter_stu."'") : '')
				. ($filt_hw ? ( ($filt_hw == 2) ? "\n AND c.hw_status = 1" : (($filt_hw == 1)?" AND (c.hw_status IS NULL OR c.hw_status <> 1)":"")) : '')
				. ($JLMS_ACL->CheckPermissions('advanced', 'assigned_groups_only') ? ("\n AND u.id IN ($members)") :'')
				. (($JLMS_ACL->CheckPermissions('homework', 'view_all')) ? '' : "\n AND d.published = 1")				
				;				
			} else {
				//TODO: fix this query !
				$query = "SELECT count(u.id)"
				. "\n FROM #__lms_homework as d, #__users as u, #__lms_homework_results as c, #__lms_users_in_groups as a"				
				. "\n WHERE d.id = '".$id."' AND c.hw_id = d.id AND c.user_id = u.id AND a.user_id = u.id AND a.course_id = ".$course_id				
				. ($filt_group ? ("\n AND a.group_id = '".$filt_group."'") : '')				
				. ($filter_stu ? ("\n AND u.id = '".$filter_stu."'") : '')
				. ($filt_hw ? ( ($filt_hw == 2) ? "\n AND c.hw_status = 1" : (($filt_hw == 1)?" AND (c.hw_status IS NULL OR c.hw_status <> 1)":"")) : '')
				. (($JLMS_ACL->CheckPermissions('homework', 'view_all')) ? '' : "\n AND d.published = 1")				
				;
			}
		} else { $query = ''; }
		$db->SetQuery( $query );
		$total = $db->LoadResult();
						
		require_once(_JOOMLMS_FRONT_HOME . DS . "includes" . DS . "classes" . DS . "lms.pagination.php");
		$pageNav = new JLMSPageNav( $total, $limitstart, $limit );
		if($JLMS_ACL->isStaff() && $JLMS_ACL->CheckPermissions('homework', 'view_stats')){ // ceo
			//NOTE: $filt_group is always == 0 for Staffs, also they don't have a filter by group - therefore we can use the same query for both 'global' and 'local' groups modes
			//if($JLMS_CONFIG->get('use_global_groups', 1)){

				$staff_learners = ($JLMS_ACL->_role_type == 3 && isset($JLMS_ACL->_staff_learners))?$JLMS_ACL->_staff_learners:array();
				$str_staff_learners = implode(",", $staff_learners);			

				$query = "SELECT u.id AS user_id, u.username, u.name, u.email, c.hw_status, c.hw_date, c.grade, d.graded_activity"
				. ($filt_group ? (", b.ug_name") : '')
				. "\n FROM (#__lms_homework as d, #__users as u) LEFT JOIN #__lms_homework_results as c ON c.hw_id = d.id AND c.user_id = u.id, #__lms_users_in_groups as a"
				. ($filt_group ? ("\n, #__lms_users_in_global_groups as uigg, #__lms_usergroups AS b") : '')	
				. "\n WHERE d.id = '".$id."' AND a.user_id = u.id AND a.course_id = ".$course_id
				. "\n AND u.id IN (".$str_staff_learners.")"				
				. ($filt_group ? ("\n AND uigg.group_id = '".$filt_group."' AND a.group_id = uigg.group_id AND b.id = uigg.group_id AND b.course_id = 0") : '')				
				. ($filter_stu ? ("\n AND u.id = '".$filter_stu."'") : '')
				. ($filt_hw ? ( ($filt_hw == 2) ? "\n AND c.hw_status = 1" : (($filt_hw == 1)?" AND (c.hw_status IS NULL OR c.hw_status <> 1)":"")) : '')				
				. (($JLMS_ACL->CheckPermissions('homework', 'view_all')) ? '' : "\n AND d.published = 1")
				. "\n LIMIT $pageNav->limitstart, $pageNav->limit";
				;								
				
			/*} else {
				$query = "SELECT u.id AS user_id, u.username, u.name, u.email, c.hw_status, c.hw_date, c.grade, d.graded_activity"
				. ($filt_group ? (", b.ug_name") : '')
				. "\n FROM #__lms_homework as d, #__users as u, #__lms_user_parents as p, #__lms_homework_results as c, #__lms_users_in_groups as a, #__lms_usergroups AS b"											
				. "\n WHERE d.id = '".$id."' AND c.hw_id = d.id AND c.user_id = u.id AND u.id = p.user_id AND p.parent_id = '".$my_id."' AND a.user_id = u.id AND b.id = a.group_id AND a.course_id = ".$course_id	
				. ($filt_group ? ("\n AND a.group_id = '".$filt_group."'") : '')				
				. ($filter_stu ? ("\n AND u.id = '".$filter_stu."'") : '')
				. ($filt_hw ? ( ($filt_hw == 2) ? "\n AND c.hw_status = 1" : (($filt_hw == 1)?" AND (c.hw_status IS NULL OR c.hw_status <> 1)":"")) : '')
				. (($JLMS_ACL->CheckPermissions('homework', 'view_all')) ? '' : "\n AND d.published = 1")
				. "\n LIMIT $pageNav->limitstart, $pageNav->limit";
				;				
			}*/
		} elseif($JLMS_ACL->CheckPermissions('homework', 'view_stats')){ //teacher or admin or assistant
			if($JLMS_CONFIG->get('use_global_groups', 1)){
				$query = "SELECT u.id AS user_id, u.username, u.name, u.email, c.hw_status, c.hw_date, c.grade, d.graded_activity"
				. ($filt_group ? (", b.ug_name") : '')
				. "\n FROM (#__lms_homework as d, #__users as u) LEFT JOIN #__lms_homework_results as c ON c.hw_id = d.id AND c.user_id = u.id, #__lms_users_in_groups as a"
				. ($filt_group ? ("\n, #__lms_users_in_global_groups as uigg, #__lms_usergroups AS b") : '')	
				. "\n WHERE d.id = '".$id."' AND a.user_id = u.id AND a.course_id=".$course_id				
				. ($filt_group ? ("\n AND uigg.group_id = '".$filt_group."' AND a.group_id = uigg.group_id AND b.id = uigg.group_id AND b.course_id = 0") : '')				
				. ($filter_stu ? ("\n AND u.id = '".$filter_stu."'") : '')
				. ($filt_hw ? ( ($filt_hw == 2) ? "\n AND c.hw_status = 1" : (($filt_hw == 1)?" AND (c.hw_status IS NULL OR c.hw_status <> 1)":"")) : '')
				. ($JLMS_ACL->CheckPermissions('advanced', 'assigned_groups_only') ? ("\n AND u.id IN ($members)") :'')
				. (($JLMS_ACL->CheckPermissions('homework', 'view_all')) ? '' : "\n AND d.published = 1")
				. "\n LIMIT $pageNav->limitstart, $pageNav->limit";
				;				
			} else {
				$query = "SELECT u.id AS user_id, u.username, u.name, u.email, c.hw_status, c.hw_date, c.grade, d.graded_activity"
				. ($filt_group ? (", b.ug_name") : '')
				. "\n FROM #__lms_homework as d, #__users as u, #__lms_homework_results as c, #__lms_users_in_groups as a"
				. ($filt_group ? (", "."\n #__lms_usergroups AS b") : '')										
				. "\n WHERE d.id = '".$id."' AND c.hw_id = d.id AND c.user_id = u.id AND a.user_id = u.id "
				. ($filt_group ? ("\n AND b.id = a.group_id ") : '')
				. "\n AND a.course_id = ".$course_id				
				. ($filt_group ? ("\n AND a.group_id = '".$filt_group."'") : '')
				. ($filter_stu ? ("\n AND u.id = '".$filter_stu."'") : '')
				. ($filt_hw ? ( ($filt_hw == 2) ? "\n AND c.hw_status = 1" : (($filt_hw == 1)?" AND (c.hw_status IS NULL OR c.hw_status <> 1)":"")) : '')
				. (($JLMS_ACL->CheckPermissions('homework', 'view_all')) ? '' : "\n AND d.published = 1")
				. "\n LIMIT $pageNav->limitstart, $pageNav->limit";
				;
			}
		} else { $query = ''; }
		$db->SetQuery( $query );
		$hw_stats = $db->LoadObjectList();
				
		if( !$filt_group ) {			
				if( $JLMS_CONFIG->get('use_global_groups', 1) ) 
				{					
					$query = "SELECT ug.ug_name, uig.user_id 
								FROM #__lms_users_in_global_groups AS uigg, #__lms_users_in_groups AS uig, #__lms_usergroups AS ug 
								WHERE uigg.group_id = ug.id AND uig.group_id = uigg.group_id AND uig.course_id = ".$course_id
								.( $JLMS_ACL->CheckPermissions('advanced', 'assigned_groups_only') ? ("\n AND uig.group_id IN ($groups_where_admin_manager)") :'');									
				} else {
					$query = "SELECT ug.ug_name, uig.user_id 
								FROM #__lms_users_in_groups AS uig, #__lms_usergroups AS ug 
								WHERE uig.group_id = ug.id AND uig.course_id = ".$course_id;												
				}	
				
				
				$db->setQuery( $query );
				$ug_names = $db->LoadObjectList();
																				
				for( $i = 0; $i < count($hw_stats); $i++ ) 
				{
					$hw_stats[$i]->ug_name = '';
					foreach( $ug_names AS $ug_name ) 
					{	
						if( $ug_name->user_id == $hw_stats[$i]->user_id ) 
						{
							if( $hw_stats[$i]->ug_name )
								$hw_stats[$i]->ug_name .= ', '.$ug_name->ug_name;
							else
								$hw_stats[$i]->ug_name = $ug_name->ug_name;
						}					
					}											
				}			
		}		
						
		$lists = array();
		$f_items = array();
		$f_items[] = mosHTML::makeOption(0, _JLMS_HW_FILTER_ALL_RESULTS);
		$f_items[] = mosHTML::makeOption(2, _JLMS_HW_STATUS_COMPLETED);
		$f_items[] = mosHTML::makeOption(1, _JLMS_HW_STATUS_INCOMPLETE);
		$link = "index.php?option=$option&amp;Itemid=$Itemid&task=hw_stats&course_id=$course_id&amp;id=$id";
		$link = $link ."&amp;filt_hw='+this.options[selectedIndex].value+'";
		$link = sefRelToAbs( $link );
		$link = str_replace('%5C%27',"'", $link);$link = str_replace('%5B',"[", $link);$link = str_replace('%5D',"]", $link);$link = str_replace('%20',"+", $link);$link = str_replace("\\\\\\","", $link);$link = str_replace('%27',"'", $link);
		$lists['filter'] = mosHTML::selectList($f_items, 'filt_hw', 'class="inputbox" size="1" onchange="document.location.href=\''. $link .'\';"', 'value', 'text', $filt_hw );
		
		$g_items = array();
		$g_items[] = mosHTML::makeOption(0, _JLMS_HW_FILTER_ALL_GROUPS);
		if ($JLMS_CONFIG->get('use_global_groups', 1)){
			if($JLMS_ACL->_role_type == 2 || $JLMS_ACL->_role_type == 4) {
				$query = "SELECT distinct a.id as value, a.ug_name as text"
				. "\n FROM #__lms_usergroups as a"
				. "\n WHERE a.course_id = 0"
				. "\n AND a.parent_id = 0"
				. ($JLMS_ACL->CheckPermissions('advanced', 'assigned_groups_only') ? ("\n AND a.id IN ($groups_where_admin_manager)") :'')
				. "\n ORDER BY a.ug_name"
				;
			}
			else {
				//TODO: bug! CEO cann't see all the groups available in the system
				$query = "SELECT distinct a.id as value, a.ug_name as text"
				. "\n FROM #__lms_usergroups as a"
				. "\n WHERE a.course_id = 0"
				. "\n AND a.parent_id = 0"
				. "\n ORDER BY a.ug_name"
				;
			}
		} else {
			$query = "SELECT distinct a.id as value, a.ug_name as text FROM #__lms_usergroups as a, #__lms_users_in_groups as b"
			. "\n WHERE a.course_id = '".$course_id."' AND b.group_id = a.id ORDER BY a.ug_name";
		}
		$db->SetQuery( $query );
		$groups = $db->LoadObjectList();
		
		$g_items = array_merge($g_items, $groups);
		$link = "index.php?option=$option&amp;Itemid=$Itemid&task=hw_stats&course_id=$course_id&amp;id=$id";
		$link = $link ."&amp;filt_group='+this.options[selectedIndex].value+'";
		$link = sefRelToAbs( $link );
		$link = str_replace('%5C%27',"'", $link);$link = str_replace('%5B',"[", $link);$link = str_replace('%5D',"]", $link);$link = str_replace('%20',"+", $link);$link = str_replace("\\\\\\","", $link);$link = str_replace('%27',"'", $link);
		$lists['filter2'] = mosHTML::selectList($g_items, 'filt_group', 'class="inputbox" style="width:250px;" size="1" onchange="document.location.href=\''. $link .'\';"', 'value', 'text', $filt_group );

		if($filt_group) {
			$g_items = array();
			$g_items[] = mosHTML::makeOption(0, _JLMS_FILTER_ALL_SUBGROUPS);

			if( ($JLMS_ACL->_role_type == 2 || $JLMS_ACL->_role_type == 4) && $JLMS_CONFIG->get('use_global_groups', 1)) {
				$query = "SELECT distinct a.id as value, a.ug_name as text"
				. "\n FROM #__lms_usergroups as a"
				. "\n WHERE a.course_id = 0"
				. "\n AND a.parent_id = $filt_group"
				. ($JLMS_ACL->CheckPermissions('advanced', 'assigned_groups_only') ? ("\n AND a.parent_id IN ($groups_where_admin_manager)") :'')
				. "\n ORDER BY a.ug_name"
				;
				$db->SetQuery( $query );
				$sbugroups = $db->LoadObjectList();
				
				if(count($sbugroups)) {
					$g_items = array_merge($g_items, $sbugroups);
					$link = "index.php?option=$option&amp;Itemid=$Itemid&task=hw_stats&course_id=$course_id&amp;id=$id";
					$link = $link ."&amp;filt_group=".$filt_group."&amp;filt_subgroup='+this.options[selectedIndex].value+'";
					$link = sefRelToAbs( $link );
					$link = str_replace('%5C%27',"'", $link);$link = str_replace('%5B',"[", $link);$link = str_replace('%5D',"]", $link);$link = str_replace('%20',"+", $link);$link = str_replace("\\\\\\","", $link);$link = str_replace('%27',"'", $link);
					$lists['filter3'] = mosHTML::selectList($g_items, 'filt_subgroup', 'class="inputbox" style="width:250px;" size="1" onchange="document.location.href=\''. $link .'\';"', 'value', 'text', $filt_subgroup );
				}
			}
		}
		
		$r = new stdClass();
		$r->id = 0;$r->username = _JLMS_SB_ALL_USERS;$r->name = '';$r->email = '';$r->ug_name = '';
		$students[] = $r;
		$students = array_merge($students, JLMS_getCourseStudentsList2($course_id, $filt_group));
//		$students = array_merge($students, JLMS_getCourseStudentsList($course_id, $filt_group));
		$link = "index.php?option=$option&amp;Itemid=$Itemid&amp;task=hw_stats&amp;course_id=$course_id&amp;id=$id";
		$link = $link ."&amp;filter_stu='+this.options[selectedIndex].value+'";
		$link = sefRelToAbs( $link );
		$link = str_replace('%5C%27',"'", $link);$link = str_replace('%5B',"[", $link);$link = str_replace('%5D',"]", $link);$link = str_replace('%20',"+", $link);$link = str_replace("\\\\\\","", $link);$link = str_replace('%27',"'", $link);
		$lists['filter_stu'] = mosHTML::selectList($students, 'filter_stu', 'class="inputbox" style="width:250px;" size="1" onchange="document.location.href=\''. $link .'\';"', 'id', 'username', $filter_stu );

		$lms_titles_cache = & JLMSFactory::getTitles();
		$lms_titles_cache->setArray('users', $hw_stats, 'user_id', 'username');

		JLMS_homework_html::statsHW( $hw_stats, $hw_info[0], $option, $course_id, $pageNav, $lists, ($JLMS_ACL->CheckPermissions('homework', 'view_stats')) );
	} else {
		JLMSRedirect( sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=homework&id=$course_id") );
	}
}

function JLMS_publishHW( $id, $option ) {
	$JLMS_CONFIG = & JLMSFactory::getConfig();
	$Itemid = $JLMS_CONFIG->get('Itemid');
	$db = & JFactory::getDbo();
	$JLMS_ACL = & JLMSFactory::getACL();
	if ( $JLMS_ACL->CheckPermissions('homework', 'publish') ) {	
		$state = intval(mosGetParam($_REQUEST, 'state', 0));				
		$cid2 = intval(mosGetParam( $_REQUEST, 'cid2', 0 ));
		$row = new mos_Joomla_LMS_HomeWork( $db );		
		$row->load( $cid2 );
		$row->published = $state;
		$row->store();			
	}
	JLMSRedirect( sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=homework&id=$id") );	
}

function JLMS_viewHW( $id, $option ) {
	global $my, $JLMS_DB, $Itemid;
	$course_id = intval(mosGetParam($_REQUEST, 'course_id', 0));
	$JLMS_ACL = & JLMSFactory::getACL();
	
	if ( $JLMS_ACL->CheckPermissions('homework', 'view') && ($id && (JLMS_GetHWCourse($id) == $course_id)) ) {
		$row = new mos_Joomla_LMS_HomeWork( $JLMS_DB );
		$row->load( $id );
				
		$lists = array();
		$lists['completed'] = _JLMS_HW_STATUS_INCOMPLETE;
		$query = "SELECT id, hw_date, hw_status, file_id, write_text, comments, grade, user_id FROM #__lms_homework_results WHERE hw_id = '".$id."' AND user_id = '".$my->id."' AND course_id = '".$course_id."'";
		$JLMS_DB->setQuery( $query );		 
					
		$status = 0;
		$row->file = false;
		$row->write_text = '';
		$row->comments = '';
		$row->hw_id = $row->id;
		$row->id = 0;
		$row->grade_text = '';	

		$hw_result = $JLMS_DB->LoadObject();
		if ( is_object( $hw_result ) ) {			
			$status = $hw_result->hw_status;
			if ($hw_result->hw_status == 1) {
				$lists['completed'] = $hw_result->hw_date;
			}
			
			$query = "SELECT * FROM #__lms_files WHERE id = ".$hw_result->file_id;		
			$JLMS_DB->setQuery( $query );
			$file = $JLMS_DB->loadObject();
			if ( is_object( $file ) ) 
			{
				$file->date = $hw_result->hw_date;
				$row->file = $file;					
			}			
			
			$row->write_text = $hw_result->write_text;
			$row->id = $hw_result->id;
			$row->comments = $hw_result->comments;	
			
			$row->file_id = $hw_result->file_id;
			$row->user_id = $hw_result->user_id;		
			
			switch( $hw_result->grade ) 
			{
				case _STATUS_NOT_SELECT:
					$row->grade_text = _JLMS_HW_STATUS_COMPLETED;
				break;
				case _STATUS_INCOMPLETE:
					$row->grade_text = _JLMS_HW_STATUS_INCOMPLETE;
				break;
				case _STATUS_NOT_PASSED:
					$row->grade_text = _JLMS_HW_STATUS_NOT_PASSED;
				break;
				case _STATUS_PASSED:
					$row->grade_text = _JLMS_HW_STATUS_PASSED;
				break;
				default:
					if( $hw_result->grade && $hw_result->grade != _STATUS_NOT_SELECT )
						$row->grade_text = $hw_result->grade;
			}			
		}		
										
		JLMS_homework_html::viewHW( $row, $option, $course_id, $lists, $status );
	} else {
		JLMSRedirect( sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=homework&id=$course_id") );
	}
}

function JLMS_viewHWResult( $option ) {
	global $my, $JLMS_DB, $Itemid, $JLMS_CONFIG;
	
	$course_id = intval(mosGetParam($_REQUEST, 'course_id', 0));
	$user_id = intval(mosGetParam($_REQUEST, 'user_id', 0));
	$hw_id = intval(mosGetParam($_REQUEST, 'hw_id', 0));
	
	$JLMS_ACL = & JLMSFactory::getACL();
	if ( $JLMS_ACL->CheckPermissions('homework', 'manage') && ( $hw_id && ( JLMS_GetHWCourse( $hw_id ) == $course_id )) ) {
					
		$query = "SELECT * 
					FROM #__lms_homework_results 
					WHERE hw_id = '".$hw_id."' AND user_id = '".$user_id."' AND course_id = '".$course_id."'";
		$JLMS_DB->setQuery( $query );	
		$row = $JLMS_DB->LoadObject();
		if ( is_object( $row ) ) 
		{												
			$hw = new mos_Joomla_LMS_HomeWork( $JLMS_DB );
			$hw->load( $row->hw_id );		
			
			$row->hw = $hw;	
									
			if( $JLMS_CONFIG->get('use_global_groups', 1) ) 				
				$user_group_ids = JLMS_ACL_HELPER::GetUserGlobalGroup( $row->user_id, $row->course_id );
			else 
				$user_group_ids = JLMS_ACL_HELPER::GetUserGroup( $row->user_id, $row->course_id );
								
						
			if($JLMS_ACL->CheckPermissions('advanced', 'assigned_groups_only')) { 
				$assigned_group_ids = JLMS_ACL_HELPER::GetAssignedGroups( $row->user_id, $row->course_id );
				$user_group_ids = array_intersect( $user_group_ids, $assigned_group_ids );
			}	
			
			$user = JUser::getInstance( $user_id );			
			$user->group = '';
			if( isset( $user_group_ids[0] ) ) {			
				$query = "SELECT ug_name FROM #__lms_usergroups WHERE id IN (".implode(',', $user_group_ids).")";
				$JLMS_DB->setQuery( $query );
				$user_groups = $JLMS_DB->loadResultArray();			
				
				if( isset($user_groups[0]) ) 
					$user->group = implode( ', ', $user_groups );
			}						
			$row->user = $user;
									
			$row->file = false;		
								
			switch( $hw->activity_type ) { 
			case _ACTIVITY_TYPE_UPLOAD: 
				$query = "SELECT * FROM #__lms_files WHERE id = ".$row->file_id;		
				$JLMS_DB->setQuery( $query );
				$file = $JLMS_DB->loadObject();
				if ( is_object( $file ) ) 
				{
					
					$base_path = str_replace( '\\', '/', JPATH_BASE);
					$doc_path = str_replace( '\\', '/', _JOOMLMS_DOC_FOLDER);
										
					$doc_folder = str_replace( $base_path, '', $doc_path );					
					$doc_folder = ltrim( $doc_folder, '/' );				
					
					$file->path = JURI::root().$doc_folder.$file->file_srv_name;										 
					
					$row->file = $file;					
				}
			break;					
			}
			
			if( $hw->graded_activity  ) {
				$query = "SELECT scale_name AS value, scale_name AS text 
						FROM #__lms_gradebook_scale 
						WHERE course_id = ".$row->course_id."
						ORDER BY ordering"
						;
				$JLMS_DB->setQuery( $query );
				$grade_list = $JLMS_DB->loadObjectList();
				
				if( isset($grade_list[0]) ) 		
					$grade_list = array_merge( array( mosHTML::makeOption( 'not_selected' , '&nbsp;' ) ), $grade_list  ) ;
				else	
					$grade_list[] = mosHTML::makeOption( 'not_selected' , '&nbsp;' );
				
				$grade_list[] = mosHTML::makeOption( _STATUS_INCOMPLETE, _JLMS_HW_STATUS_INCOMPLETE );	
				
			} else {
								 
				$grade_list[] = mosHTML::makeOption( _STATUS_NOT_SELECT, '&nbsp;' );			
				$grade_list[] = mosHTML::makeOption( _STATUS_NOT_PASSED, _JLMS_HW_STATUS_NOT_PASSED );
				$grade_list[] = mosHTML::makeOption( _STATUS_PASSED, _JLMS_HW_STATUS_PASSED );
				$grade_list[] = mosHTML::makeOption( _STATUS_INCOMPLETE, _JLMS_HW_STATUS_INCOMPLETE );
			}
			
			if( !$row->grade ) $row->grade = _STATUS_NOT_SELECT;
															
			$lists['grade'] = mosHTML::selectList( $grade_list , 'grade', 'class="inputbox" size="1"', 'value', 'text', $row->grade );
										
			JLMS_homework_html::viewHWResult( $row, $option, $lists );		
		} else {			
			JLMSRedirect( sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=homework&id=$course_id") );
		}		
	} else {
		JLMSRedirect( sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=homework&id=$course_id") );
	}
}

function JLMS_editHW( $id, $course_id, $option ) {
	global $my, $JLMS_DB, $Itemid;
	$JLMS_ACL = & JLMSFactory::getACL();
	
	$assigned_groups_only = $JLMS_ACL->CheckPermissions('advanced', 'assigned_groups_only');
			
//	if ( $course_id && (JLMS_GetUserType($my->id, $course_id) == 1) && ( ($id && (JLMS_GetHWCourse($id) == $course_id)) || !$id ) ) {
	if ( $course_id && $JLMS_ACL->CheckPermissions('homework', 'manage') && ( ($id && (JLMS_GetHWCourse($id) == $course_id)) || !$id ) ) {
		$AND_ST = "";
		/*
		if( false !== ( $enroll_period = JLMS_getEnrolPeriod( $my->id, $course_id )) ) 
		{
			$AND_ST = " AND IF(is_time_related, (show_period < '".$enroll_period."' ), 1) ";	
		}
		*/
		
		if($assigned_groups_only) 
		{
			$groups_where_admin_manager = JLMS_ACL_HELPER::GetAssignedGroups($my->id, $id);
			$groups_where_isset_user = JLMS_ACL_HELPER::GetUserGlobalGroup($my->id, $id);
			
			$groups_where_admin_manager = array_merge($groups_where_admin_manager,$groups_where_isset_user);
			
			if (count($groups_where_admin_manager)) {
				$AND_ST .= "\n AND (is_limited = 0 OR groups LIKE '%|$groups_where_admin_manager[0]|%'";
				for($i=1;$i<count($groups_where_admin_manager);$i++) {
					$AND_ST .= "\n OR groups like '%|$groups_where_admin_manager[$i]|%'";
				}
				$AND_ST .=  "\n OR owner_id = '".$my->id."')";
			}
			else {
				$AND_ST .= "\n AND (is_limited = 0 OR owner_id = '".$my->id."' OR id = 0) AND groups = ''";
			}
		}
		
		$row = new mos_Joomla_LMS_HomeWork( $JLMS_DB );
		$row->addCond( $AND_ST );
		$row->load( $id );
		
		$lists = array();
		
		$groups_arr = array();
		
		$groups_where_admin_manager = array(59,69,71);
						
		if( $row->groups ) {
			$groups = substr($row->groups,  1 ,strlen($row->groups)-2);
			$groups_arr = explode('|',$groups);
		} else if( !$row->groups && $groups_where_admin_manager )  
		{
			$groups_arr = $groups_where_admin_manager;
		}
				
		$lists['groups'] = JLMSmultiselect( $groups_arr, true, $course_id );				
			
		if($assigned_groups_only) {						
			//$lists['is_limited'] = mosHTML::yesnoRadioList( 'is_limited', 'disabled="disabled" class="inputbox" ', ($id?$row->is_limited:1));
			$lists['is_limited'] = '';			
		}
		else {
			$lists['is_limited'] = mosHTML::yesnoRadioList( 'is_limited', 'class="inputbox" ', $row->is_limited);
		}
		
		$lists['published'] = mosHTML::yesnoRadioList( 'published', 'class="inputbox" ', $row->published);
		
		$activity_type[] = mosHTML::makeOption(1, _JLMS_HW_OFFLINE_ACTIVITY);
		$activity_type[] = mosHTML::makeOption(2, _JLMS_HW_WRITE_TEXT);
		$activity_type[] = mosHTML::makeOption(3, _JLMS_HW_UPLOAD_FILE);
				
		$lists['activity_type'] = mosHTML::selectList($activity_type, 'activity_type', 'class="inputbox" size="1"', 'value', 'text', $row->activity_type );		
		$lists['graded_activity'] = mosHTML::yesnoRadioList( 'graded_activity', 'class="inputbox" ', $row->graded_activity);
		
		if( $assigned_groups_only )
			$params['hidden_is_time_related'] = '<input type="hidden" name="is_limited" value="'.($id?$row->is_limited:1).'">';
		
		JLMS_homework_html::showEditHW( $row, $lists, $option, $course_id, $params );
	} else {
		JLMSRedirect( sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=homework&id=$course_id") );
	}
}

function JLMS_cancelHW( $option ) {
	$JLMS_CONFIG = & JLMSFactory::getConfig();
	$Itemid = $JLMS_CONFIG->get('Itemid');
	$course_id = intval(mosGetParam($_REQUEST, 'course_id', 0));
	JLMSRedirect(sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=homework&id=$course_id"));
}
function JLMS_saveHW( $option ) {
	global $my, $JLMS_DB, $Itemid;
	
	$course_id = intval(mosGetParam($_REQUEST, 'course_id', 0));
	$id = intval(mosGetParam($_REQUEST, 'id', 0));
	$groups 	= mosGetParam( $_REQUEST, 'groups', array(0) );
	$is_limited	= intval(mosGetParam( $_REQUEST,'is_limited',0));

	$JLMS_ACL = & JLMSFactory::getACL();
	$assigned_groups_only = $JLMS_ACL->CheckPermissions('advanced', 'assigned_groups_only');
	
//	if ( (JLMS_GetUserType($my->id, $course_id) == 1) && ( ($id && (JLMS_GetHWCourse($id) == $course_id)) || !$id ) ) {
	if ( $JLMS_ACL->CheckPermissions('homework', 'manage') && ( ($id && (JLMS_GetHWCourse($id) == $course_id)) || !$id ) ) {
		
		if( $id ) 
		{
			$AND_ST = "";
			$oldH= new mos_Joomla_LMS_HomeWork( $JLMS_DB );
			if($assigned_groups_only) 
			{
				$groups_where_admin_manager = JLMS_ACL_HELPER::GetAssignedGroups($my->id, $id);
				$groups_where_isset_user = JLMS_ACL_HELPER::GetUserGlobalGroup($my->id, $id);
				
				$groups_where_admin_manager = array_merge($groups_where_admin_manager,$groups_where_isset_user);
				
				if (count($groups_where_admin_manager)) {
					$AND_ST .= "\n AND (is_limited = 0 OR groups LIKE '%|$groups_where_admin_manager[0]|%'";
					for($i=1;$i<count($groups_where_admin_manager);$i++) {
						$AND_ST .= "\n OR groups like '%|$groups_where_admin_manager[$i]|%'";
					}
					$AND_ST .=  "\n OR owner_id = '".$my->id."')";
				}
				else {
					$AND_ST .= "\n AND (is_limited = 0 OR owner_id = '".$my->id."' OR id = 0) AND groups = ''";
				}
			}
			
			$oldH->addCond( $AND_ST );
			$oldH->load( $id );
			
			if( !$oldH->id )
				JLMSRedirect( sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=homework&id=$course_id") );
		}
				
		$row = new mos_Joomla_LMS_HomeWork( $JLMS_DB );
		if (!$row->bind( $_POST )) {
			echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
			exit();
		}
		
		$row->post_date = JLMS_dateToDB($row->post_date);
		$row->end_date = JLMS_dateToDB($row->end_date);
		$hw_name = isset($_REQUEST['hw_name'])?strval($_REQUEST['hw_name']):'homework';
		$hw_name = (get_magic_quotes_gpc()) ? stripslashes( $hw_name ) : $hw_name; 
		$row->hw_name	= ampReplace(strip_tags($hw_name));
		
		$days = intval(mosGetParam($_POST, 'days', ''));
		$hours = intval(mosGetParam($_POST, 'hours', ''));
		$mins = intval(mosGetParam($_POST, 'mins', ''));
		
		if( $row->is_time_related ) {
			$row->show_period = JLMS_HTML::_('showperiod.getminsvalue', $days, $hours, $mins );
		}

		$row->hw_description = strval(JLMS_getParam_LowFilter($_POST, 'hw_description', ''));
		$row->hw_description = JLMS_ProcessText_LowFilter($row->hw_description);
		$row->hw_shortdescription = strval(JLMS_getParam_LowFilter($_POST, 'hw_shortdescription', ''));
		$row->hw_shortdescription = JLMS_ProcessText_HardFilter($row->hw_shortdescription);
		
		if($assigned_groups_only) {
			$row->is_limited = 1;
			$groups_where_admin_manager = JLMS_ACL_HELPER::GetAssignedGroups($my->id, $course_id);
			for($i=0;$i<count($groups);$i++) {
				if(!in_array($groups[$i], $groups_where_admin_manager)) {
					unset($groups[$i]);
				}
			}
			sort($groups);
		}
	
		$groups_in_db_arr = array();
	
		if($row->id){
			$query = "SELECT groups FROM #__lms_homework WHERE id = '".$row->id."' AND course_id = '".$course_id."'";
			$JLMS_DB->setQuery( $query );
			$groups_in_db = $JLMS_DB->LoadResult();
			
			if($groups_in_db){
				$groups_in_db = substr($groups_in_db,  1, (strlen($groups_in_db) - 2));
				$groups_in_db_arr = explode('|',$groups_in_db);
				if(isset($groups_where_admin_manager) && count($groups_where_admin_manager)) {
					$groups_in_db_arr = array_diff($groups_in_db_arr, $groups_where_admin_manager);
				}
				$groups_in_db_arr = array_unique($groups_in_db_arr);
			}
		}
		
		$groups_str = '';
		if($row->is_limited && ((count($groups) && $groups[0] != 0) || count($groups_in_db_arr))) {
			//$groups = array_merge($groups, $groups_in_db_arr);
			$groups = array_unique($groups);
			$razd = '|';
			for($i=0;$i<count($groups);$i++) {
				$groups_str .= $razd.$groups[$i];
			}	
			$groups_str .= '|';
		}
		else {
			$groups_str = '';
		}
		
		$row->groups = $groups_str;
		
		if(!$row->id) {
			$row->owner_id = $my->id;
		}
		
		
		if (!$row->check()) {
			echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
			exit();
		}
		
		if (!$row->store()) {
			echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
			exit();
		}
	}
	JLMSRedirect( sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=homework&id=$course_id") );
}

function JLMS_saveHWResult( $option ) {
	$JLMS_CONFIG = & JLMSFactory::getConfig();
	$Itemid = $JLMS_CONFIG->get('Itemid');
	$_JLMS_PLUGINS = & JLMSFactory::getPlugins();
	$db = & JFactory::getDbo();
	$user = JLMSFactory::getUser();
	
	$course_id = intval(mosGetParam($_REQUEST, 'course_id', 0));
	$user_id = intval(mosGetParam($_REQUEST, 'user_id', 0));
	$hw_id = intval(mosGetParam($_REQUEST, 'hw_id', 0));

	$JLMS_ACL = & JLMSFactory::getACL();
	$assigned_groups_only = $JLMS_ACL->CheckPermissions('advanced', 'assigned_groups_only');
		
	if ( $JLMS_ACL->CheckPermissions('homework', 'manage') && ( $hw_id && ( JLMS_GetHWCourse( $hw_id ) == $course_id )) ) 
	{
		$oldH= new mos_Joomla_LMS_HomeWork( $db );
		$AND_ST = "";
		if($assigned_groups_only) 
		{
			$groups_where_admin_manager = JLMS_ACL_HELPER::GetAssignedGroups($user->get('id'), $hw_id);
			$groups_where_isset_user = JLMS_ACL_HELPER::GetUserGlobalGroup($user->get('id'), $hw_id);
			
			$groups_where_admin_manager = array_merge($groups_where_admin_manager,$groups_where_isset_user);
			
			if (count($groups_where_admin_manager)) {
				$AND_ST .= "\n AND (is_limited = 0 OR groups LIKE '%|$groups_where_admin_manager[0]|%'";
				for($i=1;$i<count($groups_where_admin_manager);$i++) {
					$AND_ST .= "\n OR groups like '%|$groups_where_admin_manager[$i]|%'";
				}
				$AND_ST .=  "\n OR owner_id = '".$user->get('id')."')";
			}
			else {
				$AND_ST .= "\n AND (is_limited = 0 OR owner_id = '".$user->get('id')."' OR a.id = 0) AND groups = ''";
			}
		}
		
		$oldH->addCond( $AND_ST );
		$oldH->load( $hw_id );
		
		if( !$oldH->id ) {
			JLMSRedirect( sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=hw_stats&course_id=$course_id&id=".$hw_id) );
		}

		$row = new mos_JLMS_HomeWork_Result( $db );
		$row->bind( JRequest::get('POST') );
		$row->comments = strval(JLMS_getParam_LowFilter($_POST, 'comments', ''));
		$row->comments = JLMS_ProcessText_LowFilter($row->comments);

		if( $row->grade == _STATUS_INCOMPLETE ) 
		{
			$row->hw_status = '0';
		}	
		$row->store();

		$do_notify = false;
		if (isset($row->id) && $row->id) {
			$query = "SELECT hw_status FROM #__lms_homework_results WHERE id = $row->id";
			$db->setQuery( $query );
			$do_notify = $db->loadResult();
		}
		if ($do_notify) {
			//*** email notification about new homework submission
			$e_course = new stdClass();
			$e_course->course_alias = '';
			$e_course->course_name = '';			

			$query = "SELECT course_name, name_alias FROM #__lms_courses WHERE id = '".$course_id."'";
			$db->setQuery( $query );
			$e_course = $db->loadObject();

			$query = "SELECT hw_name FROM #__lms_homework WHERE id = '".$hw_id."'";
			$db->setQuery( $query );
			$e_hw_name = $db->loadResult();

			$e_user = new stdClass();
			$e_user->name = '';
			$e_user->email = '';
			$e_user->username = '';

			$query = "SELECT email, name, username FROM #__users WHERE id = '".$user_id."'";
			$db->setQuery( $query );
			$e_user = $db->loadObject();

			$e_params['user_id'] = $user_id;
			$e_params['course_id'] = $course_id;					
			$e_params['markers']['{email}'] = $e_user->email;	
			$e_params['markers']['{name}'] = $e_user->name;										
			$e_params['markers']['{username}'] = $e_user->username;
			$e_params['markers']['{coursename}'] = $e_course->course_name;//( $e_course->course_alias )?$e_course->course_alias:$e_course->course_name;
			$e_params['markers']['{homeworkname}'] = $e_hw_name;

			$e_params['markers']['{courselink}'] = JLMSEmailRoute("index.php?option=com_joomla_lms&Itemid=$Itemid&task=details_course&id=$course_id");

			$e_params['markers_nohtml']['{courselink}'] = $e_params['markers']['{courselink}'];
			$e_params['markers']['{courselink}'] = '<a href="'.$e_params['markers']['{courselink}'].'">'.$e_params['markers']['{courselink}'].'</a>';

			$e_params['markers']['{lmslink}'] = JLMSEmailRoute("index.php?option=com_joomla_lms&Itemid=$Itemid");

			$e_params['markers_nohtml']['{lmslink}'] = $e_params['markers']['{lmslink}'];
			$e_params['markers']['{lmslink}'] = '<a href="'.$e_params['markers']['{lmslink}'].'">'.$e_params['markers']['{lmslink}'].'</a>';

			$e_params['action_name'] = 'OnHomeworkReview';

			$_JLMS_PLUGINS->loadBotGroup('emails');
			$plugin_result_array = $_JLMS_PLUGINS->trigger('OnHomeworkReview', array (& $e_params));
			//*** end of emails
		}
	}
				
	JLMSRedirect( sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=hw_stats&course_id=$course_id&id=".$hw_id) );
}

//to do: proverku na teacher, student,. ... (TODO: vstavit' else - redirect)
function JLMS_showHomeWork( $id, $option) {
	global $my, $JLMS_DB, $Itemid, $JLMS_SESSION, $JLMS_CONFIG;
	
	$course_id = JRequest::getInt( 'course_id' );
	
	$id = ($course_id)?$course_id:$id;
	
	$usertype = JLMS_GetUserType($my->id, $id);	
	
	$JLMS_ACL = & JLMSFactory::getACL();
		
	$assigned_groups_only = $JLMS_ACL->CheckPermissions('advanced', 'assigned_groups_only');
	
		
	$limit		= intval( mosGetParam( $_GET, 'limit', $JLMS_SESSION->get('list_limit', $JLMS_CONFIG->get('list_limit')) ) );
	$JLMS_SESSION->set('list_limit', $limit);
	$limitstart = intval( mosGetParam( $_GET, 'limitstart', 0 ) );
	$where = '';
	
//	if ($id && ($usertype == 1 || $usertype == 6)) {
//	if ($id && ($JLMS_ACL->CheckPermissions('homework', 'manage') || $usertype == 6)) {
	if ($id && ($JLMS_ACL->CheckPermissions('homework', 'manage') || $JLMS_ACL->CheckPermissions('homework', 'view_stats'))) {				
		$members = "'0'";	
		$AND_ST = "";	
		$fields = ',0 AS checkedout';
		if($JLMS_ACL->_role_type == 2 || $JLMS_ACL->_role_type == 3 || $JLMS_ACL->_role_type == 4) {
			if($assigned_groups_only) 
			{				
				$fields = ",IF(a.owner_id != '".$my->id."',1 ,0) AS checkedout";

				$groups_where_admin_manager = JLMS_ACL_HELPER::GetAssignedGroups($my->id, $id);
				$groups_where_isset_user = JLMS_ACL_HELPER::GetUserGlobalGroup($my->id, $id);
				
				$groups_where_admin_manager = array_merge($groups_where_admin_manager,$groups_where_isset_user);
				
				if (count($groups_where_admin_manager)) {
					$where .= "\n AND (a.is_limited = 0 OR a.groups LIKE '%|$groups_where_admin_manager[0]|%'";
					for($i=1;$i<count($groups_where_admin_manager);$i++) {
						$where .= "\n OR a.groups like '%|$groups_where_admin_manager[$i]|%'";
					}
					$where .=  "\n OR a.owner_id = '".$my->id."')";
				}
				else {
					$where .= "\n AND (a.is_limited = 0 OR a.owner_id = '".$my->id."' OR a.id = 0) AND a.groups = ''";
				}
			}
		} else
		if($JLMS_ACL->_role_type < 2){
			$query = "select a.group_id FROM #__lms_users_in_global_groups as a WHERE a.user_id = '".$my->id."' AND a.subgroup1_id = 0 AND a.group_id > 0";
			$JLMS_DB->setQuery($query);
			$temp1 = $JLMS_DB->loadResultArray();

			$query = "select subgroup1_id FROM #__lms_users_in_global_groups WHERE user_id = '".$my->id."' AND subgroup1_id > 0";
			$JLMS_DB->setQuery($query);
			$temp2 = $JLMS_DB->loadResultArray();

			$group_where_isset_user = array_merge($temp1, $temp2);
			
			if (count($group_where_isset_user)) {
				$where .= "\n AND (( a.groups <> '' AND a.groups IS NOT NULL AND (groups LIKE '%|$group_where_isset_user[0]|%'";
				for($i=1;$i<count($group_where_isset_user);$i++) {
					$where .= "\n OR groups like '%|$group_where_isset_user[$i]|%'";
				}
				$where .=  "\n )) OR (a.is_limited = 0 AND (a.groups = '' OR a.groups IS NULL)))";
			}	
							
			if( false !== ( $enroll_period = JLMS_getEnrolPeriod( $my->id, $id )) ) 
			{
				$AND_ST = " AND IF(a.is_time_related, (a.show_period < '".$enroll_period."' ), 1) ";	
			}			
		}
		
		$query = "SELECT count(*) FROM #__lms_homework AS a" 
				 ."\n WHERE a.course_id = '".$id."'".$AND_ST
				 . (($JLMS_ACL->CheckPermissions('homework', 'view_all')) ? '' : "\n AND a.published = 1")
				.$where 
		;
		$JLMS_DB->SetQuery( $query );
		$total = $JLMS_DB->LoadResult();
		
		require_once(_JOOMLMS_FRONT_HOME . DS . "includes" . DS . "classes" . DS . "lms.pagination.php");
		$pageNav = new JLMSPageNav( $total, $limitstart, $limit );
		
		$query = "SELECT a.*".$fields
		. "\n FROM #__lms_homework as a"
		. "\n WHERE a.course_id = '".$id."'".$AND_ST
		. (($JLMS_ACL->CheckPermissions('homework', 'view_all')) ? '' : "\n AND a.published = 1")
		.$where 
		. "\n ORDER BY a.post_date DESC, a.hw_name"
		. "\n LIMIT $pageNav->limitstart, $pageNav->limit";
		$JLMS_DB->SetQuery( $query );
		$rows = $JLMS_DB->LoadObjectList();
		
		$lms_titles_cache = & JLMSFactory::getTitles();
		$lms_titles_cache->setArray('homework', $rows, 'id', 'hw_name');

		JLMS_homework_html::showHomeWorks( $id, $option, $rows, $pageNav, $usertype );
	} elseif ($id && $JLMS_ACL->CheckPermissions('homework', 'view')) {
		$filt_hw = intval( mosGetParam( $_GET, 'filt_hw', $JLMS_SESSION->get('filt_hw', 0) ) );
		$JLMS_SESSION->set('filt_hw', $filt_hw);
		
		$query = "select a.group_id FROM #__lms_users_in_global_groups as a WHERE a.user_id = '".$my->id."' AND a.subgroup1_id = 0 AND a.group_id > 0";
		$JLMS_DB->setQuery($query);
		$temp1 = $JLMS_DB->loadResultArray();

		$query = "select subgroup1_id FROM #__lms_users_in_global_groups WHERE user_id = '".$my->id."' AND subgroup1_id > 0";
		$JLMS_DB->setQuery($query);
		$temp2 = $JLMS_DB->loadResultArray();

		$group_where_isset_user = array_merge($temp1, $temp2);
		
		$AND_ST = "";
		if( false !== ( $enroll_period = JLMS_getEnrolPeriod( $my->id, $id )) ) 
		{
			$AND_ST = " AND IF(a.is_time_related, (a.show_period < '".$enroll_period."' ), 1) ";	
		}
				
		$query = "SELECT count(*) FROM #__lms_homework as a"
		. ($filt_hw ? ("\n LEFT JOIN #__lms_homework_results as b ON a.id = b.hw_id AND b.user_id = '".$my->id."' AND b.course_id = '".$id."'") : '')
		. "\n WHERE a.course_id = '".$id."'".$AND_ST
		. (($JLMS_ACL->CheckPermissions('homework', 'view_all')) ? '' : "\n AND a.published = 1")
		. ($filt_hw ? ( ($filt_hw == 2) ? "\n AND b.hw_status = 1" : (($filt_hw == 1)?" AND (b.hw_status IS NULL OR b.hw_status <> 1)":"")) : '')
		;
		$query .= "\n AND a.is_limited = 0";
		if(isset($group_where_isset_user) && count($group_where_isset_user)){
			$query .= "\n OR (a.is_limited = 1"
			. "\n AND (a.groups LIKE '%|$group_where_isset_user[0]|%'"
			;
			for($i=1;$i<count($group_where_isset_user);$i++){
				$query .= "\n OR a.groups like '%|$group_where_isset_user[$i]|%'";
			}
			$query .= "\n ))";
		}
		$JLMS_DB->SetQuery( $query );
		$total = $JLMS_DB->LoadResult();
		
		require_once(_JOOMLMS_FRONT_HOME . DS . "includes" . DS . "classes" . DS . "lms.pagination.php");
		$pageNav = new JLMSPageNav( $total, $limitstart, $limit );
		
		$query = "SELECT a.*, b.id as result_id, b.hw_status, b.hw_date, b.grade"
		. "\n FROM #__lms_homework as a LEFT JOIN #__lms_homework_results as b ON a.id = b.hw_id AND b.user_id = '".$my->id."' AND b.course_id = '".$id."'"
		. "\n WHERE a.course_id = '".$id."'".$AND_ST
		. ($filt_hw ? ( ($filt_hw == 2) ? "\n AND b.hw_status = 1" : (($filt_hw == 1)?" AND (b.hw_status IS NULL OR b.hw_status <> 1)":"")) : '')
		. (($JLMS_ACL->CheckPermissions('homework', 'view_all')) ? '' : "\n AND a.published = 1")
		;
		$query .= "\n AND (a.is_limited = 0";
		if(isset($group_where_isset_user) && count($group_where_isset_user)){
			$query .= "\n OR (a.is_limited = 1"
			. "\n AND (a.groups LIKE '%|$group_where_isset_user[0]|%'"
			;
			for($i=1;$i<count($group_where_isset_user);$i++){
				$query .= "\n OR a.groups like '%|$group_where_isset_user[$i]|%'";
			}
			$query .= "\n )))";
		} else {
			$query .= "\n )";
		}
		$query .= "\n ORDER BY a.post_date DESC, a.hw_name"
		. "\n LIMIT $pageNav->limitstart, $pageNav->limit";
		$JLMS_DB->SetQuery( $query );
		$rows = $JLMS_DB->LoadObjectList();
		
		$lists = array();
		$f_items = array();
		$f_items[] = mosHTML::makeOption(0, _JLMS_HW_FILTER_ALL_RESULTS);
		$f_items[] = mosHTML::makeOption(2, _JLMS_HW_STATUS_COMPLETED);
		$f_items[] = mosHTML::makeOption(1, _JLMS_HW_STATUS_INCOMPLETE);
		$link = "index.php?option=$option&amp;Itemid=$Itemid&task=homework&id=$id";
		$link = $link ."&amp;filt_hw='+this.options[selectedIndex].value+'";
		$link = sefRelToAbs( $link );
		$link = str_replace('%5C%27',"'", $link);$link = str_replace('%5B',"[", $link);$link = str_replace('%5D',"]", $link);$link = str_replace('%20',"+", $link);$link = str_replace("\\\\\\","", $link);$link = str_replace('%27',"'", $link); 
		$lists['filter'] = mosHTML::selectList($f_items, 'filt_hw', 'class="inputbox" size="1" onchange="document.location.href=\''. $link .'\';"', 'value', 'text', $filt_hw );
		$lists['used_filter'] = $filt_hw;

		$lms_titles_cache = & JLMSFactory::getTitles();
		$lms_titles_cache->setArray('homework', $rows, 'id', 'hw_name');

		JLMS_homework_html::showHomeWorks_stu( $id, $option, $rows, $pageNav, $lists );
	} else {
		JLMSRedirect(sefRelToAbs("index.php?option=$option&Itemid=$Itemid"));
	}
}
function JLMS_deleteHW( $course_id, $option ) {
	global $my, $JLMS_DB, $Itemid;
//	if ( $course_id && (JLMS_GetUserType($my->id, $course_id) == 1) ) {
	$JLMS_ACL = & JLMSFactory::getACL();

	if ($course_id && $JLMS_ACL->CheckPermissions('homework', 'manage')) {
		$cid = mosGetParam( $_POST, 'cid', array(0) );
		if (!is_array( $cid )) {
			$cid = array(0);
		}
		$i = 0;
		while ($i < count($cid)) {
			$cid[$i] = intval($cid[$i]);
			$i ++;
		}
		$cids = implode(',',$cid);

		//new proverka
		$assigned_groups_only = $JLMS_ACL->CheckPermissions('advanced', 'assigned_groups_only');

		$query = "SELECT id, is_limited, owner_id FROM #__lms_homework WHERE id IN ($cids)";
		$JLMS_DB -> setQuery($query);
		$rows = $JLMS_DB->loadObjectList();

		$flag = 0;
		$array_not_delete = array();
		for($i=0;$i<count($rows);$i++) {
			if( $rows[$i]->is_limited == 0 && $rows[$i]->owner_id != $my->id ) {
				$array_not_delete[] = $rows[$i]->id;
			}
		}
		$flag = 0;
		$where = '';
		if($assigned_groups_only) {
			$groups_where_admin_manager = JLMS_ACL_HELPER::GetAssignedGroups($my->id, $course_id);

			$groups_in_db_arr = array();
			$query = "SELECT * FROM #__lms_homework WHERE id IN ($cids)";
			$JLMS_DB->setQuery( $query );
			$rows = $JLMS_DB->LoadObjectList();

			for($i=0;$i<count($rows);$i++) {
				if($rows[$i]->is_limited && $rows[$i]->groups) {
					$groups_in_db = substr($rows[$i]->groups,  1 ,strlen($rows[$i]->groups)-2);
					$groups_in_db_arr = explode('|',$groups_in_db);
					
					$groups_in_db_arr_str = implode(',',$groups_in_db_arr);
					
					$query = "SELECT id FROM #__lms_usergroups WHERE id IN ($groups_in_db_arr_str)";
					$JLMS_DB->setQuery( $query );
					$groups_in_db_arr = $JLMS_DB->LoadResultArray();

					for($j=0;$j<count($groups_in_db_arr);$j++) {
						if(!in_array($groups_in_db_arr[$j],$groups_where_admin_manager)) {
							$array_not_delete[]=$rows[$i]->id;
							break;
						}
					}
				}
			}
		}
		// ---

		$cid = array_diff($cid, $array_not_delete);
		$cids = implode(',',$cid);

		$query = "SELECT id FROM #__lms_homework WHERE id IN ($cids) AND course_id = '".$course_id."'";
		$JLMS_DB->SetQuery( $query );
		$cid = $JLMS_DB->LoadResultArray();
		if (count($cid)) {
			$cids = implode(',',$cid);
			$query = "DELETE FROM #__lms_homework WHERE id IN ($cids) AND course_id = '".$course_id."'";
			$JLMS_DB->SetQuery( $query );
			$JLMS_DB->query();
			$query = "DELETE FROM #__lms_homework_results WHERE hw_id IN ($cids) AND course_id = '".$course_id."'";
			$JLMS_DB->SetQuery( $query );
			$JLMS_DB->query();
		}
	}
	JLMSRedirect(sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=homework&id=$course_id"));
}

function JLMS_changeHW( $course_id, $option ) {	
	$JLMS_CONFIG = & JLMSFactory::getConfig();
	$Itemid = $JLMS_CONFIG->get('Itemid');
	$_JLMS_PLUGINS = & JLMSFactory::getPlugins();
	$db = & JFactory::getDbo();
	$user = JLMSFactory::getUser();

	$course_id = intval(mosGetParam($_REQUEST, 'course_id', 0));
	$hw_id = intval(mosGetParam($_REQUEST, 'hw_id', 0));
	$id = intval(mosGetParam($_REQUEST, 'id', 0));		

	$JLMS_ACL = & JLMSFactory::getACL();
	if ( $course_id && $JLMS_ACL->CheckPermissions('homework', 'view') && ( $hw_id && JLMS_GetHWCourse( $hw_id ) == $course_id) ) {
					
		$write_text = '';		
		$hw = new mos_Joomla_LMS_HomeWork( $db );
		$hw->load( $hw_id );				
			
		$res_id = 0;		
		if( $hw->activity_type == _ACTIVITY_TYPE_UPLOAD ) {
			if( !($res_id = JLMS_uploadFileHW( $option, true )) && !$id ) 
			{
				JLMSRedirect( sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=hw_view&course_id=$course_id&id=$hw_id"));
			}
		}
					
		if( !$id )
			$id = $res_id;	

		$post_date = strtotime($hw->post_date);
		$end_date = strtotime($hw->end_date);
		$now = strtotime( date('Y-m-d') );	

		if( $post_date <= $now && $end_date >= $now ) {							
			$row = new mos_JLMS_HomeWork_Result( $db );

			if( !$row->loadExt( $course_id, $hw_id, $user->get('id') ) ) {
				$row->course_id = $course_id;
				$row->hw_id = $hw_id;
				$row->user_id = $user->get('id');
			}					

			if( $hw->activity_type == _ACTIVITY_TYPE_WRITE ) { 
				$write_text_no_html = JRequest::getVar('write_text');				
				if( !$write_text_no_html ) 
				{
					//TODO: pridumat soobschenie pri pustom pole write text
					echo "<script> alert('Please input your answer in \'Write text\' field'); window.history.go(-1);</script>\n";
					exit();
				}		
				$write_text = $_REQUEST['write_text'];				
				$row->write_text = $write_text;
			}

			$row->hw_date = date('Y-m-d H:i:s');
			$row->hw_status = 1;		
			$row->grade = '';

			if( $row->store() ) 
			{							
				//*** email notification about newe homework submission
				$e_course = new stdClass();
				$e_course->course_alias = '';
				$e_course->course_name = '';			

				$query = "SELECT course_name, name_alias FROM #__lms_courses WHERE id = '".$course_id."'";
				$db->setQuery( $query );
				$e_course = $db->loadObject();

				$query = "SELECT hw_name FROM #__lms_homework WHERE id = '".$hw_id."'";
				$db->setQuery( $query );
				$e_hw_name = $db->loadResult();

				$e_user = new stdClass();
				$e_user->name = '';
				$e_user->email = '';
				$e_user->username = '';

				$query = "SELECT email, name, username FROM #__users WHERE id = '".$user->get('id')."'";
				$db->setQuery( $query );
				$e_user = $db->loadObject();

				$e_params['user_id'] = $user->get('id');
				$e_params['course_id'] = $course_id;					
				$e_params['markers']['{email}'] = $e_user->email;	
				$e_params['markers']['{name}'] = $e_user->name;										
				$e_params['markers']['{username}'] = $e_user->username;
				$e_params['markers']['{coursename}'] = $e_course->course_name;//( $e_course->course_alias )?$e_course->course_alias:$e_course->course_name;
				$e_params['markers']['{homeworkname}'] = $e_hw_name;

				$e_params['markers']['{courselink}'] = JLMSEmailRoute("index.php?option=com_joomla_lms&Itemid=$Itemid&task=details_course&id=$course_id");;
				$e_params['markers_nohtml']['{courselink}'] = $e_params['markers']['{courselink}'];
				$e_params['markers']['{courselink}'] = '<a href="'.$e_params['markers']['{courselink}'].'">'.$e_params['markers']['{courselink}'].'</a>';

				$e_params['markers']['{lmslink}'] = 'OnCSVImportUser';	
				$e_params['markers']['{lmslink}'] = JLMSEmailRoute("index.php?option=com_joomla_lms&Itemid=$Itemid");
				$e_params['markers_nohtml']['{lmslink}'] = $e_params['markers']['{lmslink}'];
				$e_params['markers']['{lmslink}'] = '<a href="'.$e_params['markers']['{lmslink}'].'">'.$e_params['markers']['{lmslink}'].'</a>';

				$e_params['action_name'] = 'OnHomeworkSubmission';

				$_JLMS_PLUGINS->loadBotGroup('emails');
				$plugin_result_array = $_JLMS_PLUGINS->trigger('OnHomeworkSubmission', array (& $e_params));			
				//*** end of emails
			}										
		}				
	}	

	JLMSRedirect( sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=homework&id=$course_id") );
}

function JLMS_downloadFileHW( $option ) {
	$JLMS_CONFIG = & JLMSFactory::getConfig();
	$Itemid = $JLMS_CONFIG->get('Itemid');

	$course_id = intval( mosGetParam($_REQUEST, 'course_id', 0) );
	$hw_id = intval( mosGetParam($_REQUEST, 'hw_id', 0) );
	$file_id = intval( mosGetParam($_REQUEST, 'file_id', 0) );
	$user_id = intval( mosGetParam($_REQUEST, 'user_id', 0) );
			
	$JLMS_ACL = & JLMSFactory::getACL();
	$user = JLMSFactory::getUser(); 

	if ($course_id  && ( $JLMS_ACL->CheckPermissions('homework', 'manage') || $user_id == $user->get('id') ) && ( $hw_id && JLMS_GetHWCourse( $hw_id ) == $course_id ) ) 
	{															
		JLMS_downloadFile( $file_id, $option );							
	}

	JLMSRedirect( sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=hw_view_result&course_id=$course_id&hw_id=$hw_id&user_id=$user_id") );
}

function JLMS_uploadFileHW( $option, $callFromCode = false ) 
{
	global $JLMS_DB, $my, $Itemid;
	
	$id = JRequest::getInt( 'id' );
	$hw_id = JRequest::getInt( 'hw_id' );
	$course_id = JRequest::getInt( 'course_id' );
	$userfile = JRequest::getVar( 'userfile', '', 'FILES' );
	
	$JLMS_ACL = & JLMSFactory::getACL();	
	
	if( !isset($userfile['name']) || (isset($userfile['name']) && empty($userfile['name'])) )
		return false;
		
	$res_id = 0;	
	
	if ( $JLMS_ACL->CheckPermissions('homework', 'view') && ( $hw_id && ( JLMS_GetHWCourse($hw_id) == $course_id )) ) {	
		
		$row = new mos_JLMS_HomeWork_Result( $JLMS_DB );
		if ( !$row->loadExt( $course_id, $hw_id, $my->id ) ) 
		{
			$row->course_id = $course_id;
			$row->user_id = $my->id;		
			$row->hw_id = $hw_id;
			$row->file_id = 0;			
		} 		
		$row->hw_date = date('Y-m-d H:i:s');					
							
		$file_id = JLMS_uploadFile( $course_id );											
		if ($file_id) {			
			if ( $row->file_id ) {
				JLMS_deleteFiles( $row->file_id );
			}
		
			$row->file_id = $file_id;			
			$row->store();																							
		} 
		
		$res_id = $row->id;
	}
	
	if( $callFromCode ) 
	{
		return $res_id;
	} else {			
		JLMSRedirect( sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=hw_view&course_id=$course_id&id=$hw_id"));
	}	
}

function JLMS_tchangeHW( $course_id, $option ) {
	$JLMS_CONFIG = & JLMSFactory::getConfig();
	$Itemid = $JLMS_CONFIG->get('Itemid');
	$_JLMS_PLUGINS = & JLMSFactory::getPlugins();
	$db = & JFactory::getDbo();

	$user_id = intval(mosGetParam($_REQUEST, 'user_id', 0));
	$JLMS_ACL = & JLMSFactory::getACL();
	if ($course_id && $user_id && $JLMS_ACL->CheckPermissions('homework', 'manage') ) {
		$state = intval(mosGetParam($_REQUEST, 'state', 0));
		if ($state != 1) { $state = 0; }
		//$state = 1;
		$cid = mosGetParam( $_REQUEST, 'cid', array(0) );
		$cid2 = intval(mosGetParam( $_REQUEST, 'cid2', 0 ));
		if ($cid2) {
			$cid = array();
			$cid[] = $cid2;
		}
		if (!is_array( $cid )) {
			$cid = array(0);
		} 
		if (!is_array( $cid ) || count( $cid ) < 1) {
			$action = 1 ? 'Publish' : 'Unpublish';
			echo "<script> alert('Select an item to $action'); window.history.go(-1);</script>\n";
			exit();
		}
		$cid1 = array();
		$cid1[] = $cid[0];
		$cids = implode( ',', $cid1 );
		//$now = date( 'Y-m-d', time() ); 
		//check rights to change id's
		$query = "SELECT id FROM #__lms_homework WHERE course_id = '".$course_id."'"
		. "\n AND id IN ( $cids )";
		$db->SetQuery( $query );
		$cid = $db->LoadResultArray();
		if (!is_array( $cid )) {
			$cid = array(0);
		}
		$cids = implode( ',', $cid );

		$query = "SELECT hw_id FROM #__lms_homework_results WHERE course_id = '".$course_id."' AND user_id = '".$user_id."' AND hw_id IN ( $cids )";
		$db->SetQuery( $query );
		$pre_cids = $db->LoadResultArray();
		if (!count($pre_cids)) {
			$pre_cids = array(0);
		}
		$upd_cid = array_intersect($cid, $pre_cids);
		$ins_cid = array_diff($cid, $pre_cids);
		$now = date('Y-m-d H:i:s');
		if (!$state) {
			$now = '';
		}
		$hw_id_notify = 0;
		if (count($upd_cid)) {
			$cids = implode( ',', $upd_cid );
			if (isset($upd_cid[0]) && $upd_cid[0]) {
				$hw_id_notify = $upd_cid[0];
			}
			$query = "UPDATE #__lms_homework_results"
			. "\n SET hw_status = $state, hw_date = '".$now."'"
			. "\n WHERE hw_id IN ( $cids ) AND course_id = $course_id AND user_id = '".$user_id."'"
			;
			$db->setQuery( $query );
			if (!$db->query()) {
				echo "<script> alert('".$db->getErrorMsg()."'); window.history.go(-1); </script>\n";
				exit();
			}
		}
		if (count($ins_cid)) {
			if (isset($ins_cid[0]) && $ins_cid[0]) {
				$hw_id_notify = $ins_cid[0];
			}
			$query = "INSERT INTO #__lms_homework_results (course_id, user_id, hw_id, hw_status, hw_date) VALUES ";
			$t = 0;
			foreach($ins_cid as $ins_id) {
				$query .= "\n ($course_id, ".$user_id.", ".$ins_id.", $state, '".$now."')".(($t < (count($ins_cid) - 1))?',':'');
				$t ++;
			}
			$db->setQuery( $query );
			if (!$db->query()) {
				echo "<script> alert('".$db->getErrorMsg()."'); window.history.go(-1); </script>\n";
				exit();
			}
		}
		if ($state && $hw_id_notify) {
			//*** email notification about new homework submission
			$e_course = new stdClass();
			$e_course->course_alias = '';
			$e_course->course_name = '';			

			$query = "SELECT course_name, name_alias FROM #__lms_courses WHERE id = '".$course_id."'";
			$db->setQuery( $query );
			$e_course = $db->loadObject();

			$query = "SELECT hw_name FROM #__lms_homework WHERE id = '".$hw_id_notify."'";
			$db->setQuery( $query );
			$e_hw_name = $db->loadResult();

			$e_user = new stdClass();
			$e_user->name = '';
			$e_user->email = '';
			$e_user->username = '';

			$query = "SELECT email, name, username FROM #__users WHERE id = '".$user_id."'";
			$db->setQuery( $query );
			$e_user = $db->loadObject();

			$e_params['user_id'] = $user_id;
			$e_params['course_id'] = $course_id;					
			$e_params['markers']['{email}'] = $e_user->email;	
			$e_params['markers']['{name}'] = $e_user->name;										
			$e_params['markers']['{username}'] = $e_user->username;
			$e_params['markers']['{coursename}'] = $e_course->course_name;//( $e_course->course_alias )?$e_course->course_alias:$e_course->course_name;
			$e_params['markers']['{homeworkname}'] = $e_hw_name;

			$e_params['markers']['{courselink}'] = JLMSEmailRoute("index.php?option=com_joomla_lms&Itemid=$Itemid&task=details_course&id=$course_id");
			$e_params['markers_nohtml']['{courselink}'] = $e_params['markers']['{courselink}'];
			$e_params['markers']['{courselink}'] = '<a href="'.$e_params['markers']['{courselink}'].'">'.$e_params['markers']['{courselink}'].'</a>';

			$e_params['markers']['{lmslink}'] = 'OnCSVImportUser';	
			$e_params['markers']['{lmslink}'] = JLMSEmailRoute("index.php?option=com_joomla_lms&Itemid=$Itemid");
			$e_params['markers_nohtml']['{lmslink}'] = $e_params['markers']['{lmslink}'];
			$e_params['markers']['{lmslink}'] = '<a href="'.$e_params['markers']['{lmslink}'].'">'.$e_params['markers']['{lmslink}'].'</a>';

			$e_params['action_name'] = 'OnHomeworkReview';

			$_JLMS_PLUGINS->loadBotGroup('emails');
			$plugin_result_array = $_JLMS_PLUGINS->trigger('OnHomeworkReview', array (& $e_params));
			//*** end of emails
		}
	}
	JLMSRedirect( sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=hw_stats&course_id=$course_id&id={$cid[0]}") );
}
function JLMS_GetHWCourse($hw_id) {
	$db = & JFactory::getDbo();
	$JLMS_ACL = & JLMSFactory::getACL();

	$query = "SELECT course_id FROM #__lms_homework WHERE id = '".$hw_id."'". (($JLMS_ACL->CheckPermissions('homework', 'view_all')) ? '' : "\n AND published = 1");
	$db->SetQuery( $query );
	return $db->LoadResult();
}
?>