<?php
/**
* joomla_lms.agenda.php
* (c) JoomaLMS eLearning Software http://www.joomlalms.com/
**/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

	$id 	= intval( mosGetParam( $_REQUEST, 'id', 0 ) ); 
	$task 	= mosGetParam( $_REQUEST, 'task', '' );

	require_once(_JOOMLMS_FRONT_HOME . "/joomla_lms.agenda.html.php");
	$JLMS_CONFIG = & JLMSFactory::getConfig();

	$course_id = $JLMS_CONFIG->get('course_id',0);
	$pathway = array();
	$pathway[] = array('name' => _JLMS_PATHWAY_HOME, 'link' => sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid"), 'is_home' => true);
	$pathway[] = array('name' => $JLMS_CONFIG->get('course_name'), 'link' => sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid&amp;task=details_course&amp;id=$course_id"), 'is_course' => true);
	$pathway[] = array('name' => _JLMS_TOOLBAR_AGENDA, 'link' => sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid&amp;task=agenda&amp;id=$course_id"));
	JLMSAppendPathWay($pathway);
	JLMS_ShowHeading();
	
	switch ($task) {
	###############################		AGENDA		##############################
		case 'my_agenda':
		case 'agenda':				JLMS_show_calendar( $id, $option );		break;
	##############################################################################
	}

//sdelat vivod po datam
function JLMS_show_calendar( $id, $option) {
	$JLMS_CONFIG = & JLMSFactory::getConfig();
	$Itemid = $JLMS_CONFIG->get('Itemid');
	$db = & JFactory::getDbo();
	$user = JLMSFactory::getUser();
	$my_id = $user->get('id');
	$JLMS_ACL = & JLMSFactory::getACL();

	$assigned_groups_only = $JLMS_ACL->CheckPermissions('advanced', 'assigned_groups_only');

	$view_all_course_categories = $JLMS_ACL->CheckPermissions('advanced', 'view_all_course_categories');

	//show top menu 
	JLMS_agenda_html::show_head_menu($id, $option );

	//select all events
	if ($id){
		$course_id = array($id);
	}
	else{
		$course_id = JLMS_GetUserCourses_IDs( $my_id );
		if (!is_array($course_id) || empty($course_id)){
			$course_id = array(0);
		}
	}
	//opredeliaem type usera

	$mode 	= mosGetParam($_REQUEST, 'mode', '' );
	$sort   = mosGetParam($_REQUEST, 'jlms_agenda_order' , 'desc');
	$filter = mosGetParam($_REQUEST, 'jlms_agenda_filter' , '');
	$where = '';
	
	switch ($filter){
		case 'current':
			$where .= "\n AND end_date >= '".date('Y-m-d')."' AND start_date <= '".date('Y-m-d')."'" ;	break;
		case 'upcoming':
			$where .= "\n AND start_date > '".date('Y-m-d')."'" ;	break;
	}
	$course_id = implode(',',$course_id);
	if ($mode != 'add_avent' || $mode != 'edit_event'){

		$members = "'0'";

		if($JLMS_ACL->_role_type == 2 || $JLMS_ACL->_role_type == 3 || $JLMS_ACL->_role_type == 4) {
			if($assigned_groups_only) {

				$groups_where_admin_manager = JLMS_ACL_HELPER::GetAssignedGroups($my_id, $id);
				$groups_where_isset_user = JLMS_ACL_HELPER::GetUserGlobalGroup($my_id, $id);
				
				$groups_where_admin_manager = array_merge($groups_where_admin_manager,$groups_where_isset_user);
				
				if (count($groups_where_admin_manager)) {
					$where .= "\n AND (is_limited = 0 OR `groups` LIKE '%|$groups_where_admin_manager[0]|%'";
					for($i=1;$i<count($groups_where_admin_manager);$i++) {
						$where .= "\n OR `groups` like '%|$groups_where_admin_manager[$i]|%'";
					}
					$where .=  "\n OR owner_id = '".$my_id."')";
				}
				else {
					$where .= "\n AND (is_limited = 0 OR owner_id = '".$my_id."' OR agenda_id = 0) AND groups = ''";
				}
			}
		}
		elseif($JLMS_ACL->_role_type < 2) {				
				$query = "select a.group_id FROM #__lms_users_in_global_groups as a WHERE a.user_id = '".$my_id."' AND a.subgroup1_id = 0 AND a.group_id > 0";
				$db->setQuery($query);
				$temp1 = $db->loadResultArray();

				$query = "select subgroup1_id FROM #__lms_users_in_global_groups WHERE user_id = '".$my_id."' AND subgroup1_id > 0";
				$db->setQuery($query);
				$temp2 = $db->loadResultArray();

				$group_where_isset_user = array_merge($temp1,$temp2);

				if (count($group_where_isset_user)) {
					$where .= "\n AND (( `groups` <> '' AND `groups` IS NOT NULL AND (`groups` LIKE '%|$group_where_isset_user[0]|%'";
					for($i=1;$i<count($group_where_isset_user);$i++) {
						$where .= "\n OR `groups` like '%|$group_where_isset_user[$i]|%'";
					}
					$where .=  "\n )) OR (is_limited = 0 AND (`groups` = '' OR `groups` IS NULL)))";
				}				
		}		

		$query	= "SELECT * FROM #__lms_agenda WHERE course_id IN ($course_id)"
				.$where 
				."\n ORDER BY start_date ";
		$db->setQuery($query);
		$rows	= $db->LoadObjectList();

		$lms_titles_cache = & JLMSFactory::getTitles();
		$lms_titles_cache->setArray('agenda', $rows, 'agenda_id', 'title');

		if( $JLMS_ACL->_role_type < 2 ) 
		{
			 $rows = filterByShowPeriod( $rows );
		}
	}

	//select date (if no, select current date)
	$cal_date = false;
	if( isset($_REQUEST['cal_date']) && ($_REQUEST['cal_date'] != '') && (strtotime($_REQUEST['cal_date']) != -1) ) 
	{
		$cal_date = $_REQUEST['cal_date'];
	} else if (isset($_REQUEST['date']) && ($_REQUEST['date'] != '') && (strtotime($_REQUEST['date']) != -1)) 
	{
		$cal_date = $_REQUEST['date'];
	}
	
	if ( $cal_date ) {				
		$now_date = JLMS_dateToDB($cal_date);
		
		$strDate = $now_date;
		$isValid = false;
		//proverka pravilnosti date 
		$dateArr = getdate(strtotime($now_date));				
		$y=$dateArr['year']; $m=$dateArr['mon']; $d=$dateArr['mday'];
		$isValid = checkdate($m, $d, $y);		
		
		if (!$isValid) {		
			$now_date = date( 'Y-m-d');
		}
	}
	else {		
		$now_date = date( 'Y-m-d');
	}
	
	$date= strtotime ($now_date);

	if ( $id && $JLMS_ACL->CheckPermissions('announce', 'view') ){
	
		switch ($mode){
			case 'view_month':
				JLMS_agenda_html::show_calendar_month( $id, $option, $rows, $date );	break;

			case 'view_week':
				JLMS_agenda_html::show_calendar_week( $id, $option, $rows, $date );		break;	

			case 'view_day':
				JLMS_agenda_html::show_calendar_day( $id, $option, $rows, $date) ;		break;

			case 'add_event':
				$JLMS_ACL = & JLMSFactory::getACL();
				global $JLMS_CONFIG;
				$id = $JLMS_CONFIG->get('course_id');
				if ($id && $JLMS_ACL->CheckPermissions('announce', 'manage')) {
					
					$lists['groups'] = JLMSmultiselect( array(), true, $id );
					
					if($assigned_groups_only) {
						$lists['is_limited'] = mosHTML::yesnoRadioList( 'is_limited', 'disabled="disabled" class="inputbox" ', 1);
					}
					else {
						$lists['is_limited'] = mosHTML::yesnoRadioList( 'is_limited', 'class="inputbox" ', 0);
					}
					
					JLMS_agenda_html::show_add_event( $id, $option, $agenda_item = '', $lists );
				} else {
					JLMSRedirect(sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=agenda&id=$id") );
				}
			break;

			case 'cancel_agenda':
				JLMSRedirect(sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=agenda&id=$id") ); break;

			case 'event_save':
				JLMS_save_event( $option );		break;

			case 'edit':
				JLMS_edit_event ( $option , $id );	break;

			case 'delete':
				JLMS_delete_event ( $option , $id ); 	break;

			default:
				show_agenda_items( $id, $option, $rows, $date, $sort );
			break;
		}
	}
	else{
		JLMSRedirect( sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=details_course&id=$id") );
	}	
}

function JLMS_save_event ( $option ) {
	$JLMS_CONFIG = & JLMSFactory::getConfig();
	$Itemid = $JLMS_CONFIG->get('Itemid');
	global $my, $JLMS_DB;

	//$course_id     = intval(mosGetParam($_REQUEST, 'id', 0));
	$course_id = $JLMS_CONFIG->get('course_id');
	$start_date    = JLMS_dateToDB(mosGetParam($_REQUEST, 'start_date', date('Y-m-d')));
	$end_date      = JLMS_dateToDB(mosGetParam($_REQUEST, 'end_date', date('Y-m-d')));

	$agenda_detail = isset($_REQUEST['jlms_agenda_detail']) ? strval($_REQUEST['jlms_agenda_detail']) : '';
	$agenda_detail = (get_magic_quotes_gpc()) ? stripslashes( $agenda_detail ) : $agenda_detail; 
	$agenda_detail = JLMS_ProcessText_HardFilter($agenda_detail);
	$edit          = mosGetParam($_REQUEST, 'edit', '');
	$agenda_id     = intval(mosGetParam($_REQUEST, 'agenda_id', 0));

	$agenda_title  = isset($_REQUEST['jlms_agenda_title'])?strval($_REQUEST['jlms_agenda_title']):'';
	$agenda_title  = (get_magic_quotes_gpc()) ? stripslashes( $agenda_title ) : $agenda_title; 
	$agenda_title  = ampReplace(strip_tags($agenda_title));

	$groups 	= mosGetParam( $_REQUEST, 'groups', array(0) );
	$is_limited	= intval(mosGetParam( $_REQUEST,'is_limited',0));

	$is_time_related = intval(mosGetParam($_POST, 'is_time_related', ''));	
	$days = intval(mosGetParam($_POST, 'days', ''));
	$hours = intval(mosGetParam($_POST, 'hours', ''));
	$mins = intval(mosGetParam($_POST, 'mins', ''));
	if( $is_time_related ) {			
		$show_period = JLMS_HTML::_('showperiod.getminsvalue', $days, $hours, $mins );
	}

	$JLMS_ACL = & JLMSFactory::getACL();
	$assigned_groups_only = $JLMS_ACL->CheckPermissions('advanced', 'assigned_groups_only');

	if($assigned_groups_only) {
		$is_limited = 1;

		$groups_where_admin_manager = JLMS_ACL_HELPER::GetAssignedGroups($my->id, $course_id);

		for($i=0;$i<count($groups);$i++) {
			if(!in_array($groups[$i],$groups_where_admin_manager)) {
				unset($groups[$i]);
			}
		}
		sort($groups);
	}

	$groups_in_db_arr = array();
	$query = "SELECT groups FROM #__lms_agenda WHERE agenda_id = $agenda_id AND course_id = $course_id";
	$JLMS_DB->setQuery( $query );
	$groups_in_db = $JLMS_DB->LoadResult();

	if($groups_in_db) {

		$groups_in_db = substr($groups_in_db,  1 ,strlen($groups_in_db)-2);
		$groups_in_db_arr = explode('|',$groups_in_db);

		if(count($groups_where_admin_manager)) {
			$groups_in_db_arr = array_diff($groups_in_db_arr,$groups_where_admin_manager);
		}
	}

	$groups_str = '';
	if($is_limited && ( (count($groups) && $groups[0] != 0) || count($groups_in_db_arr) ) ) {
		$groups = array_merge($groups,$groups_in_db_arr);
		$razd = '|';
		for($i=0;$i<count($groups);$i++) {
			$groups_str .= $razd.$groups[$i];
		}	
		$groups_str .= '|';
	}
	else {
		$groups_str = '';
	}
	//echo $groups_str; die;

	$ag_id = 0;
	if ( $course_id && $JLMS_ACL->CheckPermissions('announce', 'manage') ){
		//proverka na korrektnost' end_date
		if ( strtotime($end_date) < strtotime($start_date) ){
			$end_date = $start_date;
		}
		if (isset($edit) && $edit == 'yes' && $agenda_id) {
			$query = "SELECT owner_id FROM #__lms_agenda WHERE agenda_id = $agenda_id AND course_id = $course_id";
			$JLMS_DB->setQuery( $query );
			$agenda_owner = $JLMS_DB->LoadResult();
			$proceed_with_edit = true;
			if ($agenda_owner) {
				if ($JLMS_ACL->CheckPermissions('announce', 'only_own') && $agenda_owner != $my->id) {
					$proceed_with_edit = false;
				} elseif ($JLMS_ACL->CheckPermissions('announce', 'only_own_role') && $JLMS_ACL->GetRole() != $JLMS_ACL->UserSystemRole($JLMS_DB, $agenda_owner)) {
					$proceed_with_edit = false;
				}
			}
			if ($proceed_with_edit) {
				$set = '';
				if($is_time_related)
					$set = ",  show_period = '".$show_period."'";

				$query = "UPDATE `#__lms_agenda` "
						." SET title = ".$JLMS_DB->Quote($agenda_title).", is_limited = '".$is_limited."', groups = '".$groups_str."', content = ".$JLMS_DB->Quote($agenda_detail).", start_date = '".$start_date."', end_date = '".$end_date."', is_time_related = '".$is_time_related."'".$set
						." WHERE agenda_id = '".$agenda_id."' AND course_id = '".$course_id."' ";
				$JLMS_DB -> setQuery( $query );
				$JLMS_DB -> query();
				$ag_id = $agenda_id;
			}
		} else {
			$row = new StdClass();
			#$row->id = 0;
			$row->course_id = $course_id;
			$row->owner_id = $my->id;
			$row->is_limited = $is_limited;
			$row->title = $agenda_title;
			$row->groups = $groups_str;
			$row->content = $agenda_detail;
			$row->start_date = $start_date;
			$row->end_date = $end_date;
			$row->is_time_related = $is_time_related;		
			$row->show_period = $show_period;
									
			$JLMS_DB->insertobject('#__lms_agenda', $row );
			$insert_id = $JLMS_DB->insertid();
			
			$ag_id = $insert_id;
		}
	}
	if ($ag_id) {
		JLMSRedirect( sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid&amp;task=agenda&amp;id=$course_id&amp;agenda_id=".$ag_id) );
	} else {
		JLMSRedirect( sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid&amp;task=agenda&amp;id=$course_id") );
	}
}

function JLMS_edit_event( $option , $id ){
	global $JLMS_DB, $Itemid, $my, $JLMS_CONFIG;
	$id = $JLMS_CONFIG->get('course_id');
	
	$agenda_id = intval(mosGetParam ($_REQUEST, 'agenda_id', 0));
	
	$JLMS_ACL = & JLMSFactory::getACL();
	
	$assigned_groups_only = $JLMS_ACL->CheckPermissions('advanced', 'assigned_groups_only');
	
	$AND_ST = "";
	if( false !== ( $enroll_period = JLMS_getEnrolPeriod( $my->id, $id )) ) 
	{		
		$AND_ST = " AND IF(is_time_related, (show_period < '".$enroll_period."' ), 1) ";	
	}
	
	$do_redirect = false;	
	
	$query = "SELECT is_limited, owner_id FROM `#__lms_agenda` WHERE agenda_id = $agenda_id AND course_id = $id".$AND_ST;
	$JLMS_DB->setQuery($query);
	$row = $JLMS_DB->loadObject();
	if ( is_object($row) && isset($row->is_limited) ) {
		$flag = 0;
		if( $row->is_limited == 0 && $row->owner_id != $my->id ) {		
			$flag = 1;
		}	
	} else {
		$do_redirect = true;
	}		
	
	if ( $id && $JLMS_ACL->CheckPermissions('announce', 'manage') && !$flag) {
		
		$where = '';
		if($assigned_groups_only && $row->is_limited) { 
			
			$groups_where_admin_manager = JLMS_ACL_HELPER::GetAssignedGroups($my->id, $id);
			$groups_where_isset_user = JLMS_ACL_HELPER::GetUserGlobalGroup($my->id, $id);
			
			$groups_where_admin_manager = array_merge($groups_where_admin_manager,$groups_where_isset_user);
			
			if (count($groups_where_admin_manager)) {
				$where .= "\n AND is_limited = 1 AND ( `groups` LIKE '%|$groups_where_admin_manager[0]|%'";
				for($i=1;$i<count($groups_where_admin_manager);$i++) {
					$where .= "\n OR `groups` like '%|$groups_where_admin_manager[$i]|%'";
				}
				$where .=  "\n OR owner_id = '".$my->id."')";
			}
			else {
				$where .= "\n AND (owner_id = '".$my->id."' OR agenda_id = 0) AND groups = ''";
			}
		}
		
		$query = "SELECT * FROM `#__lms_agenda` WHERE agenda_id = $agenda_id AND course_id = $id"
				.$where
		;
		$JLMS_DB -> setQuery($query);
		$agenda_item = $JLMS_DB->loadObject();
		if (is_object($agenda_item) && isset($agenda_item->agenda_id)) {
			if ($JLMS_ACL->CheckPermissions('announce', 'only_own') && $agenda_item->owner_id != $my->id) {
				$do_redirect = true;
			} elseif ($JLMS_ACL->CheckPermissions('announce', 'only_own_role') && $JLMS_ACL->GetRole() != $JLMS_ACL->UserSystemRole($JLMS_DB, $agenda_item->owner_id)) {
				$do_redirect = true;
			} else {
				$groups_arr = array();
				
				if($agenda_item->groups) {
					$groups = substr($agenda_item->groups,  1 ,strlen($agenda_item->groups)-2);
					$groups_arr = explode('|',$groups);
				}
				
				if($assigned_groups_only) {
					$lists['is_limited'] = mosHTML::yesnoRadioList( 'is_limited', 'disabled="disabled" class="inputbox" ', $agenda_item->is_limited);
				}
				else {
					$lists['is_limited'] = mosHTML::yesnoRadioList( 'is_limited', 'class="inputbox" ', $agenda_item->is_limited);
				}
				
				$lists['groups'] = JLMSmultiselect( $groups_arr, true, $id );
												
				JLMS_agenda_html::show_add_event( $id, $option, $agenda_item, $lists );
			}
		} else { $do_redirect = true; }
	} else { $do_redirect = true; }
	
	if ($do_redirect) {
		JLMSRedirect( sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=agenda&id=$id") );
	}
}


function JLMS_delete_event ($option , $id ){
	global $JLMS_DB, $my, $Itemid, $JLMS_CONFIG;
	
	$agenda_id = intval(mosGetParam ($_REQUEST, 'agenda_id', 0));
	$id = $JLMS_CONFIG->get('course_id');
	$JLMS_ACL = & JLMSFactory::getACL();
	
	$assigned_groups_only = $JLMS_ACL->CheckPermissions('advanced', 'assigned_groups_only');
		
	$query = "SELECT is_limited, owner_id FROM `#__lms_agenda` WHERE agenda_id = $agenda_id AND course_id = $id";
	$JLMS_DB -> setQuery($query);
	$row = $JLMS_DB->loadObject();
	if( is_object($row) && isset($row->is_limited)) 
	{	
		$flag = 0;
		$flag1 = 0;
		if( $row->is_limited == 0 && $row->owner_id != $my->id ) {
			$flag1 = 1;
		}
		
		
		$flag = 0;
		if ($JLMS_ACL->CheckPermissions('announce', 'manage') && !$flag1) {
	
			$where = '';
			if($assigned_groups_only) {
				$groups_where_admin_manager = JLMS_ACL_HELPER::GetAssignedGroups($my->id, $id);
				//$groups_where_isset_user = JLMS_ACL_HELPER::GetUserGlobalGroup($my->id, $id);
				//$groups_where_admin_manager = array_merge($groups_where_admin_manager,$groups_where_isset_user);
				
				if (count($groups_where_admin_manager)) {
	//				$where .= "\n AND ( `groups` LIKE '%|$groups_where_admin_manager[0]|%'";
	//				for($i=1;$i<count($groups_where_admin_manager);$i++) {
	//					$where .= "\n OR `groups` like '%|$groups_where_admin_manager[$i]|%'";
	//				}
	//				$where .=  "\n )";
				}
				else {
					$where .= "\n AND agenda_id = 0 ";
				}
				
				$groups_in_db_arr = array();
				$query = "SELECT groups FROM #__lms_agenda WHERE agenda_id = $agenda_id AND course_id = $id";
				$JLMS_DB->setQuery( $query );
				$groups_in_db = $JLMS_DB->LoadResult();
				
				$query = "SELECT is_limited FROM #__lms_agenda WHERE agenda_id = $agenda_id AND course_id = $id";
				$JLMS_DB->SetQuery($query);
				$is_limited = $JLMS_DB->LoadResult();
	
				if($is_limited) {
					if($groups_in_db) {
						$groups_in_db = substr($groups_in_db,  1 ,strlen($groups_in_db)-2);
						$groups_in_db_arr = explode('|',$groups_in_db);
						
						$groups_in_db_arr_str = implode(',',$groups_in_db_arr);
						
						$query = "SELECT id FROM #__lms_usergroups WHERE id IN ($groups_in_db_arr_str)";
						$JLMS_DB->setQuery( $query );
						$groups_in_db_arr = $JLMS_DB->LoadResultArray();
						
						for($i=0;$i<count($groups_in_db_arr);$i++) {
							if(!in_array($groups_in_db_arr[$i],$groups_where_admin_manager)) {
								$flag = 1;
								break;
							}
						}
					}
				}
			}
			
			$query = "SELECT owner_id FROM #__lms_agenda WHERE agenda_id = $agenda_id AND course_id = $id";
			$JLMS_DB->SetQuery($query);
			$agenda_owner = $JLMS_DB->LoadResult();
			
			$proceed_with_removal = true;
			if ($agenda_owner) { // if owner there
				if ($JLMS_ACL->CheckPermissions('announce', 'only_own') && $agenda_owner != $my->id && $flag) {
					$proceed_with_removal = false;
				} elseif ($JLMS_ACL->CheckPermissions('announce', 'only_own_role') && $JLMS_ACL->GetRole() != $JLMS_ACL->UserSystemRole($JLMS_DB, $agenda_owner)) {
					$proceed_with_removal = false;
				}
			}
			
			if ($proceed_with_removal && !$flag) {
				$query = "DELETE FROM #__lms_agenda WHERE agenda_id = $agenda_id AND course_id = $id"
						.$where
				;
				$JLMS_DB->setQuery($query);
				$JLMS_DB->query();
			}
		}
	}
	
	JLMSRedirect (sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid&amp;task=agenda&amp;id=$id"));
}

function show_agenda_items( $id, $option, $rows, $date, $sort ){
	global $Itemid;
		
	$selected 	     = strval( mosGetParam( $_REQUEST, 'jlms_agenda_order', '' ) );
	$selected_filter = strval( mosGetParam( $_REQUEST, 'jlms_agenda_filter', '' ) );
	
	$JLMS_ACL = & JLMSFactory::getACL();
	if ($JLMS_ACL->CheckPermissions('announce', 'view')){	
		$lists = array();
		$order = array();
		//formiruem spisok Ordering
		$order[] = mosHTML::makeOption( 'desc', _JLMS_AGENDA_DESC );
		$order[] = mosHTML::makeOption( 'asc', _JLMS_AGENDA_ASC );
		$lists['order'] = mosHTML::selectList( $order, 'jlms_agenda_order', 'class="inputbox" size="1" ', 'value', 'text', $selected );
		//formiruem spisok Filter
		$filter[] = mosHTML::makeOption( '0', _JLMS_SB_FILTER_NONE );
		$filter[] = mosHTML::makeOption( 'current', _JLMS_AGENDA_TODAY );
		$filter[] = mosHTML::makeOption( 'upcoming', _JLMS_AGENDA_UPCOMING );
		$lists['filter'] = mosHTML::selectList( $filter, 'jlms_agenda_filter', 'class="inputbox" size="1" ', 'value', 'text', $selected_filter );
		
		function add_to_agenda( &$agenda, $d) {
			$do_add = true;
			for ($i = 0,$n=count($agenda);$i<$n;$i++) {
				if (($agenda[$i]->a_y.'-'.$agenda[$i]->a_m) == substr($d,0,7)) {
					$do_add = false;
				}
			}
			if ($do_add) {
				$ff = new stdClass();
				$ff->a_y = substr($d,0,4);
				$ff->a_m = substr($d,5,2);
				$ff->items = array();
				$agenda[] = $ff;
			}
		}
		function fill_my(&$agenda, &$st, &$en) {
			$i = 0;
			$do = true;
			while($do) {
				$d = date("Y-m-1",strtotime("+$i month",strtotime($st)));
				if (strtotime($d) <= strtotime($en)) {
					add_to_agenda($agenda, $d);
				} else $do = false;
				$i ++;
			}
		}
		$agenda =array();
		foreach ($rows as $row) {
			$tmp_date = $row->start_date;
			fill_my($agenda, $row->start_date, $row->end_date);
		}
		for ($i=0,$n=(count($agenda)-1);$i<$n;$i++) {
			for($j=$i,$m=count($agenda);$j<$m;$j++) {
				if ($sort == 'asc') {
					if (($agenda[$j]->a_y < $agenda[$i]->a_y) || ($agenda[$j]->a_y == $agenda[$i]->a_y && $agenda[$j]->a_m < $agenda[$i]->a_m)) {
						$k = $agenda[$j];
						$agenda[$j] = $agenda[$i];
						$agenda[$i] = $k;
					}
				} else {
					if (($agenda[$j]->a_y > $agenda[$i]->a_y) || ($agenda[$j]->a_y == $agenda[$i]->a_y && $agenda[$j]->a_m > $agenda[$i]->a_m)) {
						$k = $agenda[$j];
						$agenda[$j] = $agenda[$i];
						$agenda[$i] = $k;
					}
				}
			}
		}
//		print_r($agenda);die;
		
		foreach ($rows as $row){
			for($i = 0,$n=count($agenda);$i<$n;$i++) {
				if ( ($agenda[$i]->a_y.'-'.$agenda[$i]->a_m == substr($row->start_date,0,7)) || ($agenda[$i]->a_y.'-'.$agenda[$i]->a_m == substr($row->end_date,0,7)) || (strtotime($agenda[$i]->a_y.'-'.$agenda[$i]->a_m.'-1') >= strtotime($row->start_date) && strtotime($agenda[$i]->a_y.'-'.$agenda[$i]->a_m.'-1') <= strtotime($row->end_date)) ) {
					$agenda[$i]->items[] = $row;
				}
			}
		}
		
		JLMS_agenda_html::show_agenda_items( $id, $option, $rows, $date, $lists, $agenda );
	}
	else{
		JLMSRedirect (sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid"));
	}	
}

//to do: proverku na teacher, owner,. ...

?>