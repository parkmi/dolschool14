<?php
/**
* admin.roles.php
* JoomlaLMS Component
*/
// no direct access
//defined( '_VALID_MOS' ) or die( 'Restricted access' );
if (!defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) { die( 'Restricted access' ); }

require_once(_JOOMLMS_ADMIN_HOME.'/files/admin.group_managers.html.php');

$task 	= JRequest::getCmd('task', 'group_managers' );
$page 	= JRequest::getCmd('page', 'list' );
$cid	= JRequest::getVar('cid', array(), '', 'array');
if (!is_array( $cid )) {
	$cid = array(0);
	//TODO: check ALU_editItem function, probably it handles parameters like 'X_Y' as one request variable and after that explodes it....
	/*for ( $i = 0, $n = count($cid); $i < $n; $i++ ) {
		$cid[$i] = intval($cid[$i]);
	}*/
}
switch ($page) {
	case 'edit':
		ALU_editItem( intval( $cid[0] ), $option );
	break;
	case 'new':
	case 'assign_user_group_manager':
		ALU_editItem( 0, $option );
	break;
	case 'save':
		ALU_saveItem( $option, $page );
	break;
	case 'delete':
		ALU_deleteItem( $cid, $option, $page );
	break;
	case 'cancel':
		$app = & JFactory::getApplication('administrator');
		$app->redirect("index.php?option=$option&task=group_managers");
	break;
	case 'list':
	default:
		ALU_showList( $option );
	break;
}

function ALU_showList( $option ) {
	$db = & JFactory::GetDbo();
	$app = & JFactory::getApplication('administrator');

	$limit 		= intval( $app->getUserStateFromRequest( "viewlistlimit{$option}_group_managers", 'limit', $app->getCfg('list_limit') ) );
	$limitstart = intval( $app->getUserStateFromRequest( "view{$option}_lms_group_managers_limitstart", 'limitstart', 0 ) );
	$filt_groups = intval( $app->getUserStateFromRequest( "filt_groups{$option}", 'filt_groups', 0 ) );
	$filt_users = intval( $app->getUserStateFromRequest( "filt_users{$option}", 'filt_users', 0 ) );
	
	$query = "SELECT COUNT(a.user_id)"
	. "\n FROM #__lms_user_assign_groups as a, #__lms_users as b, #__lms_usergroups as c, #__users as d"
	. "\n WHERE a.user_id = b.user_id"
	. "\n AND a.group_id = c.id"
	. "\n AND b.user_id = d.id"
	. ($filt_groups ? "\n AND a.group_id = '$filt_groups'" : '' )
	. ($filt_users ? "\n AND b.user_id = '$filt_users'" : '' )
	;
	$db->setQuery( $query );
	$total = $db->loadResult();

	require_once( JPATH_SITE . DS . 'components'.DS.'com_joomla_lms'.DS.'includes'.DS.'classes'.DS.'lms.pagination.new.php');
	$pageNav = new JLMSPagination( $total, $limitstart, $limit  );

	// get the subset (based on limits) of required records
	$query = "SELECT d.name, d.username, c.ug_name, a.user_id, a.group_id"
	. "\n FROM #__lms_user_assign_groups as a, #__lms_users as b, #__lms_usergroups as c, #__users as d"
	. "\n WHERE a.user_id = b.user_id"
	. "\n AND a.group_id = c.id"
	. "\n AND b.user_id = d.id"
	. ($filt_groups ? "\n AND a.group_id = '$filt_groups'" : '' )
	. ($filt_users ? "\n AND b.user_id = '$filt_users'" : '' )
	. "\n ORDER BY d.name, d.username, c.ug_name"
	. "\n LIMIT $pageNav->limitstart, $pageNav->limit"
	;
	$db->setQuery( $query );
	$rows = $db->loadObjectList();

	$lists = array();

	$query = "SELECT id as value, ug_name as text FROM #__lms_usergroups WHERE course_id = 0 AND parent_id = 0 ORDER by ug_name";
	$db->setQuery( $query );
	$sf_groups = array();
	$sf_groups[] = mosHTML::makeOption( '0', _JLMS_USERS_SLCT_GR_ );
	$sf_groups = array_merge( $sf_groups, $db->loadObjectList() );

	$lists['jlms_groups'] = mosHTML::selectList( $sf_groups, 'filt_groups', 'class="text_area" size="1" style="width: 266px;" onchange="document.adminForm.submit();"', 'value', 'text', $filt_groups );

	$query = "SELECT c.id as value, c.name as text FROM #__lms_user_assign_groups as a, #__lms_users as b, #__users as c WHERE a.user_id = b.user_id AND c.id = b.user_id group by c.id order by c.name";
	$db->setQuery( $query );
	$sf_users = array();
	$sf_users[] = mosHTML::makeOption( '0', _JLMS_USERS_SLCT_USER_ );
	$sf_users = array_merge( $sf_users, $db->loadObjectList() );

	$lists['jlms_users'] = mosHTML::selectList( $sf_users, 'filt_users', 'class="text_area" size="1" style="width: 266px;" onchange="document.adminForm.submit();"', 'value', 'text', $filt_users );

	ALU_html::JLMS_showGroup_managers( $rows, $pageNav, $lists, $option);
}

function ALU_editItem($id, $option) {
	$db = & JFactory::GetDbo();
	
	$cid 	= mosGetParam( $_POST, 'cid', mosGetParam( $_GET, 'cid', array(0) ) );
	if (!is_array( $cid )) {
		$cid = array(0);
	}

	$group_id = intval( $cid[0] );

	if(!$group_id) {
		$group_id = mosGetParam($_REQUEST,'filt_groups');
	}
	
	$redirect = mosGetParam($_REQUEST,'page');
	
	$id = mosGetParam($_REQUEST,'cid', array(0));

	$rows_groups = array();
	
	$row->user_id = 0;

	if ($id[0]) {
		$one_elem = explode('_',$id[0]);
	
		$X = isset($one_elem[0]) ? intval($one_elem[0]) : 0;
		$Y = isset($one_elem[1]) ? intval($one_elem[1]) : 0;
		if ($X && $Y) {
			$query = "SELECT a.*,b.* FROM #__lms_user_assign_groups as a, #__users as b WHERE a.user_id = $X AND a.group_id = $Y AND b.id = a.user_id";
			$db->setQuery( $query );
			$row = $db->LoadObject();
			$group_id = $row->group_id;
			
			$query = "SELECT b.ug_name FROM #__lms_user_assign_groups as a, #__lms_usergroups as b WHERE a.user_id = $X AND a.group_id = b.id ORDER BY b.ug_name";
			$db->setQuery( $query );
			$rows_groups = $db->LoadObjectList();
		}
	}

	$lists = array();
	
	$query = "SELECT id as value, ug_name as text FROM #__lms_usergroups WHERE course_id = 0 AND parent_id = 0 ORDER by ug_name";
	$db->setQuery( $query );
	$ug_names = $db->loadObjectList();
	
	$list_ug_names = array();
	$list_ug_names[] = mosHTML::makeOption( '0', _JLMS_USERS_SLCT_USR_GR_ );
	$list_ug_names = array_merge( $list_ug_names, $ug_names );
	$lists['ug_names'] = mosHTML::selectList( $list_ug_names, 'group_id', 'class="text_area" size="1" style="width:266px"', 'value', 'text', $group_id );
	
	$query = "SELECT a.id as value, a.name as text FROM #__users AS a, #__lms_users AS b WHERE a.id = b.user_id ORDER BY name";
	$db->SetQuery($query);
	$list_users = array();
	$list_users[] = mosHTML::makeOption( '0', _JLMS_USERS_NAME_ );
	$pr = $db->loadObjectList();
	$list_users = array_merge( $list_users, $pr );
	$lists['users_names'] = mosHTML::selectList( $list_users, 'user_id', 'class="text_area" style="width:266px" size="1"', 'value', 'text', $row->user_id );

	ALU_html::editItem( $row, $lists, $option, $redirect, $rows_groups );
}

function ALU_deleteItem($cid, $option) {
	$db = & JFactory::GetDbo();

	$where = array();
	for($i=0;$i<count($cid); $i++) {
		$one_elem = explode('_',$cid[$i]);

		$X = isset($one_elem[0]) ? intval($one_elem[0]) : 0;
		$Y = isset($one_elem[1]) ? intval($one_elem[1]) : 0;

		if ($X && $Y) {
			$where[] = "(user_id = $X AND group_id = $Y)";
		}
		if (count($where)) {
			$Query_where = implode($where, ' OR ');
		}
	}
	$query = "DELETE FROM #__lms_user_assign_groups"
	. "\n WHERE $Query_where";

	$db->setQuery( $query );
	if (!$db->query()) {
		echo "<script> alert('".$db->getErrorMsg()."'); window.history.go(-1); </script>\n";
	}
	if (count($cid) == 1) {
		$msg = _JLMS_USERS_MSG_USR_REMOVED_F_MBRS;
	} else {
		$msg = _JLMS_USERS_MSG_USRS_REMOVED_F_MBRS;
	}

	$app = & JFactory::getApplication('administrator');
	$app->redirect("index.php?option=$option&task=group_managers", $msg);
}

function ALU_saveItem( $option, $page) {
	$db = & JFactory::GetDbo();

	$redirect = JRequest::getCmd('redirect');
	$edit_manager = JRequest::getCmd('edit_manager');
	$old_group_id = JRequest::getCmd('old_group_id');
	$user_id = JRequest::getInt('user_id');
	$group_id = JRequest::getInt('group_id');

	$query = "SELECT count(*) FROM #__lms_user_assign_groups WHERE user_id = $user_id AND group_id = $group_id";
	$db->setQuery( $query );
	$count_yet = $db->LoadResult();

	if ($count_yet) {
		$msg = _JLMS_USERS_USR_EXISTS;
	} else {
		if ($edit_manager) {
			$query = "UPDATE #__lms_user_assign_groups SET group_id = $group_id WHERE user_id = $user_id AND group_id = $old_group_id";
		} else {	
			$query = "INSERT INTO #__lms_user_assign_groups (user_id, group_id) VALUES ($user_id, $group_id)";
		}	
		$db->setQuery( $query );
		if (!$db->query()) {
			echo "<script> alert('".$db->getErrorMsg()."'); window.history.go(-1); </script>\n";
		}
		$msg = _JLMS_USERS_MSG_USR_ADDED;
	}
	$app = & JFactory::getApplication('administrator');
	if($redirect) {
		$app->redirect("index.php?option=$option&task=classes", $msg);
	} else {
		$app->redirect("index.php?option=$option&task=group_managers", $msg);
	}
}
?>