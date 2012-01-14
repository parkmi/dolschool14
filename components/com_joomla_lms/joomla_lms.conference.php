<?php
/**
* joomla_lms.conference.php
* (c) JoomaLMS eLearning Software http://www.joomlalms.com/
**/

// no direct access
defined( '_JLMS_EXEC' ) or die( 'Restricted access' );

	 
	$course_id 	= intval( mosGetParam( $_REQUEST, 'course_id', 0 ) );	
	
	$task 	= mosGetParam( $_REQUEST, 'task', '' );
	$mode 	= mosGetParam( $_REQUEST, 'mode', '' );
	$cid = josGetArrayInts('cid', $_POST);

	if (!is_array( $cid )) { $cid = array(0); }
	
	require_once(_JOOMLMS_FRONT_HOME . "/joomla_lms.conference.html.php");
	//otrisovivaem head menu
	if (($mode != 'conference_room') && ($mode != 'upload_popup') && ($mode != 'conference_playback') && ($mode != 'params') && ($mode != 'save_period') && ($mode != 'display_file')){
		global $JLMS_CONFIG;
		$course_id = $JLMS_CONFIG->get('course_id',0);
		$pathway = array();
		$pathway[] = array('name' => _JLMS_PATHWAY_HOME, 'link' => sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid"), 'is_home' => true);
		$pathway[] = array('name' => $JLMS_CONFIG->get('course_name'), 'link' => sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid&amp;task=details_course&amp;id=$course_id"), 'is_course' => true);
		$pathway[] = array('name' => _JLMS_TOOLBAR_CONF, 'link' => sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid&amp;task=conference&amp;id=$course_id"));
		JLMSAppendPathWay($pathway);
		JLMS_ShowHeading();
	}
	
	switch ($task) {
		case 'conference':	JLMS_show_conference( $course_id, $option, $mode);		break;
	}


//sdelat vivod po datam
function JLMS_show_conference( $course_id, $option, $mode) {
	global $my, $JLMS_DB, $JLMS_CONFIG;
	
	//select date (if no, select current date)
	$user_id = $my->id;
	$cid = mosGetParam( $_POST, 'cid', array(0) );
	if (!is_array( $cid )) { $cid = array(0); }
	//opredelenie tipa polzovatelia
	//$usertype = $JLMS_CONFIG->get('current_usertype');
	$JLMS_ACL = & JLMSFactory::getACL();
	if ($JLMS_ACL->CheckPermissions('conference', 'manage')) { //TODO: - implement full ACL permissions
		$usertype = 1;
	} else {
		$usertype = 2;
	}
	
	switch ($mode){
		case 'conference_room':
			jlms_conference_room($usertype, $option, $course_id);
			break;
		case 'conference_playback':
			jlms_conference_playback($usertype, $option, $course_id);	break;	
		case 'cb_profile':
			jlms_conference_cb_profile($option);	break;
		break;
		case 'profile':
			jlms_conference_profile($option);	break;
		case 'upload':
			jlms_conference_upload ( $option, $course_id );	break;	
		case 'upload_popup':
			JLMS_conference_html::jlms_conference_upload_popup( $option, $course_id );	break;
		case 'deleteFile':
			jlms_delete_file($course_id);	break;
		case 'params'	:
			jlms_generate_xml_settings($course_id);	break;
		case 'edit_record':
			JLMS_editDetails($option);	break;
		case 'change_record':
			JLMS_recordChange( $option ); break;
		case 'save_record':	
			JLMS_saveRecord( $course_id, $option ); break;
		case 'record_delete':
			JLMS_doDeleteRecords($option); break;
		case 'archive' :
			jlms_conference_archive($course_id, $option);	break;
		case 'start_record':
			jlms_record_write($course_id, $option);	break;
		case 'period_cancel':
			JLMSRedirect(sefRelToAbs('index.php?option='.$option.'&amp;task=conference&amp;mode=booking&amp;id='.$course_id)); break;
		case 'booking':
			jlms_booking_list($course_id, $option);	break;
		case 'user_access':
			jlms_booking_users($course_id, $option);	break;	
		case 'save_users':
			jlms_booking_users_save($course_id, $option);	break;	
		case 'delete_users_from_conference':
			jlms_booking_users_delete($cid, $course_id, $option);	break;	
		case 'new_period':
			jlms_booking_edit(0, $course_id, $option);	break;
		case 'edit_period':
			jlms_booking_edit(intval($cid[0]), $course_id, $option);	break;
		case 'period_delete':
			jlms_booking_delete($cid, $option, $course_id);	break;		
		case 'save_period':
			jlms_booking_save($course_id, $option); break;
		case 'param_request':
			get_parambook($course_id, 1); break;
		case 'display_file':
			jlms_displayFile($course_id, $option); break;			
		default:
			jlms_room_selecting( $course_id, $option, $user_id , $usertype);
		break;
	}
}
function get_parambook($course_id, $is_resp) {
	global $JLMS_DB, $my, $Itemid, $JLMS_CONFIG;

	$param_book = 1;
	$course_params = $JLMS_CONFIG->get('course_params');
	$params_cb = new JLMSParameters($course_params);
	$en_book = $params_cb->get('conf_book', 0);
	if($en_book) {	
		$cur_timestamp = ( time() + $JLMS_CONFIG->get('offset') * 60 * 60 );
		$query = "SELECT * FROM #__lms_conference_period WHERE course_id='".$course_id."' AND from_time <= ".$cur_timestamp." AND to_time >= ".$cur_timestamp;
		$JLMS_DB->setQuery( $query );
		$pids = $JLMS_DB->loadObjectList();
		if (count($pids))
		{
			if (!$pids[0]->public)
			{
				$query = "SELECT user_id FROM #__lms_conference_usr WHERE p_id='".$pids[0]->p_id."'";
				$JLMS_DB->setQuery( $query );
				$acesusrs = $JLMS_DB->loadResultArray();
				if(!in_array($my->id,$acesusrs))
				{
					$param_book = 0;
				}
			}
		}
		else 
		{
			$param_book = 0;
		}
	}
	if(!$is_resp)
	{
		return $param_book;
	}
	else 
	{
		@ob_end_clean();
		$iso = explode( '=', _ISO );
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
		echo '<?xml version="1.0" encoding="'.$iso[1].'" standalone="yes"?>';
		echo '<response>' . "\n";
		echo '<parambook>'.$param_book.'</parambook>';
		echo '</response>' . "\n";
		exit();
	}
}
function jlms_conference_room($usertype, $option, $course_id)
{
	global $JLMS_DB, $my, $Itemid, $JLMS_CONFIG;
	$param_book = get_parambook($course_id, 0);
	if($param_book || $usertype == 1)
	{
		JLMS_conference_html::jlms_conference_room($usertype, $option);
	}
	else 
	{
		echo '<script language="javascript" type="text/javascript">alert("You haven\'t access");</script>';
	}
}
function jlms_room_selecting( $course_id, $option, $user_id , $usertype) {
	global $JLMS_DB, $my, $Itemid, $JLMS_CONFIG, $JLMS_SESSION;

	$recorded_session = $course_id."_".date("Y-m-d_H-i")."_".substr(md5(uniqid(rand(), true)),0,10);
	$param_book = 1;
	$msg_name = '';
	$msg_descr = '';
	$msg_access = '';
	$course_params = $JLMS_CONFIG->get('course_params');
	$params_cb = new JLMSParameters($course_params);
	$en_book = $params_cb->get('conf_book', 0);
	
	if($en_book)
	{	

		$cur_timestamp = ( time() + $JLMS_CONFIG->get('offset') * 60 * 60 );
		$query = "SELECT * FROM #__lms_conference_period WHERE course_id='".$course_id."' AND from_time <= ".$cur_timestamp." AND to_time >= ".$cur_timestamp;
		$JLMS_DB->setQuery( $query );
		$pids = $JLMS_DB->loadObjectList();
		
		if (count($pids))
		{
			$msg_name = $pids[0]->p_name;
			$msg_descr = $pids[0]->p_description;
			if ($pids[0]->public) {
				$msg_access = 'Access: Public';
			}
			if ($usertype == 1) {
				if ($pids[0]->teacher_id && $pids[0]->teacher_id != $my->id) {
					$param_book = 0;
					$msg_name = "You have no access to the conference '".$msg_name."'";
				}
			} elseif ($usertype == 2) {
				if ($pids[0]->public)
				{
					//$msg_name = $pids[0]->p_name;
					//$msg_descr = $pids[0]->p_description;
					//$msg_access = 'Access: Public';
				}
				else 
				{
					$query = "SELECT user_id FROM #__lms_conference_usr WHERE p_id='".$pids[0]->p_id."'";
					$JLMS_DB->setQuery( $query );
					$acesusrs = $JLMS_DB->loadResultArray();
					
					if(in_array($user_id, $acesusrs))
					{
						//$msg_name = $pids[0]->p_name;
						//$msg_descr = $pids[0]->p_description;
						
					}
					else 
					{
						$param_book = 0;
						$msg_name = "You have no access to the conference '".$msg_name."'";
					}
				}
			} else {
				$param_book = 0;
				$msg_name = "You have no access to the conference '".$msg_name."'";
			}
		}
		else 
		{
			if ($usertype != 1) {
				$param_book = 0;
				$msg_name = "There are no sheduled conferences at the moment.";
			}
		}
	}

	JLMS_conference_html::jlms_room_selecting( $course_id, $option, $user_id , $usertype, $recorded_session, $param_book, $en_book, $msg_name, $msg_descr, $msg_access );
}

function JLMS_saveRecord($course_id, $option){
	global $JLMS_DB, $my, $Itemid, $JLMS_CONFIG, $JLMS_SESSION;
	//$usertype = $JLMS_CONFIG->get('current_usertype', 0);
	$JLMS_ACL = & JLMSFactory::getACL();
	if ( $course_id && $JLMS_ACL->CheckPermissions('conference', 'manage') ) {

		$cid = mosGetParam( $_POST, 'cid', 0);
		$published = mosGetParam( $_POST, 'published', 0);
		$record_name = mosGetParam( $_POST, 'record_name', '');
		$description = mosGetParam( $_POST, 'description', '');
		
		$query = "UPDATE #__lms_conference_records"
		. "\n SET record_name = '".$record_name."', description = '".$description."', published = $published  "
		. "\n WHERE id = $cid AND course_id = $course_id "
		;
		$JLMS_DB->setQuery( $query );
		if (!$JLMS_DB->query()) {
			echo "<script> alert('".$JLMS_DB->getErrorMsg()."'); window.history.go(-1); </script>\n";
			exit();
		}
		$JLMS_SESSION->set('msg', _JLMS_CONFERENCE_CHANGED);
	}else{
		$JLMS_SESSION->set('msg', _NOT_AUTH);
	}
	
	JLMSRedirect( sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=conference&mode=archive&id=$course_id") );
}
function JLMS_editDetails( $option){
	global $JLMS_DB, $my, $Itemid, $JLMS_CONFIG, $JLMS_SESSION;
	$course_id = $JLMS_CONFIG->get('course_id');
	//$usertype = $JLMS_CONFIG->get('current_usertype', 0);
	$JLMS_ACL = & JLMSFactory::getACL();
	if ( $course_id && $JLMS_ACL->CheckPermissions('conference', 'manage') ) {	
	//if ( $course_id && ($usertype == 1)) {
		$cid = mosGetParam( $_REQUEST, 'cid', 0 );
		if (!is_array( $cid )) {
			$cid = array(0);
		}
		$query = "SELECT * FROM `#__lms_conference_records` "
		. "\n WHERE id = ".$cid[0]." AND course_id = ".$course_id." ";
		$JLMS_DB->setQuery( $query );
		$record = $JLMS_DB->loadObject();
		if (is_object($record)) {
			$lists = array();
			$lists['published'] = mosHTML::yesnoRadioList( 'published', 'class="inputbox" ', $record->published);
			JLMS_conference_html::JLMS_editRecordDetails( $course_id, $option, $record, $lists  );
		} else {
			JLMSRedirect( sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=conference&mode=archive&id=$course_id") );
		}
	} else {
		$JLMS_SESSION->set('msg',_NOT_AUTH);
		JLMSRedirect( sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=conference&mode=archive&id=$course_id") );
	}
}

function jlms_conference_playback($usertype, $option, $course_id ){
	global $JLMS_CONFIG;
	$state = intval(mosGetParam($_REQUEST, 'state', 0));
	
	JLMS_conference_html::jlms_conference_playback($usertype, $option, $course_id );
}
function JLMS_recordChange( $option ) {
	global $JLMS_DB, $my, $Itemid, $JLMS_CONFIG;
	$course_id = $JLMS_CONFIG->get('course_id');
	//$usertype = $JLMS_CONFIG->get('current_usertype', 0);
	//if ( $course_id && ($usertype == 1) ) {
	$JLMS_ACL = & JLMSFactory::getACL();
	if ( $course_id && $JLMS_ACL->CheckPermissions('conference', 'manage') ) {
		$state = intval(mosGetParam($_REQUEST, 'state', 0));
		if ($state != 1) { $state = 0; }
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
		$cids = implode( ',', $cid );
		$query = "UPDATE #__lms_conference_records"
		. "\n SET published = $state"
		. "\n WHERE id IN ( $cids ) AND course_id = $course_id "
		;
		$JLMS_DB->setQuery( $query );
		if (!$JLMS_DB->query()) {
			echo "<script> alert('".$JLMS_DB->getErrorMsg()."'); window.history.go(-1); </script>\n";
			exit();
		}
	}
	jlmsRedirect( sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=conference&mode=archive&id=$course_id") );
}

function JLMS_doDeleteRecords( $option ) {
	global $my, $JLMS_DB, $Itemid, $JLMS_CONFIG;
	$course_id = $JLMS_CONFIG->get('course_id');
	//$usertype = $JLMS_CONFIG->get('current_usertype', 0);
	//if ( $course_id && ($usertype == 1) ) {
	$JLMS_ACL = & JLMSFactory::getACL();
	if ( $course_id && $JLMS_ACL->CheckPermissions('conference', 'manage') ) {
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
		require_once(_JOOMLMS_FRONT_HOME . "/includes/lms_del_operations.php");
		
		JLMS_deleteFromFMS($cids, $course_id, $option, true);
	}
}


function jlms_conference_archive($course_id ,$option){
	global $my, $JLMS_DB, $Itemid, $JLMS_SESSION, $JLMS_CONFIG;
	
	$limit		= intval( mosGetParam( $_GET, 'limit', $JLMS_SESSION->get('list_limit',$JLMS_CONFIG->get('list_limit')) ) );
	$JLMS_SESSION->set('list_limit', $limit);
	$limitstart = intval( mosGetParam( $_GET, 'limitstart', 0 ) );
	$where = '';
	$JLMS_ACL = & JLMSFactory::getACL();
	if (!$JLMS_ACL->CheckPermissions('conference', 'manage')){
		$where = " AND a.published = 1 ";
	}

	$query = "SELECT count(a.published) FROM `#__lms_conference_records` as a LEFT JOIN `#__users` as b ON a.user_id = b.id WHERE a.course_id = $course_id "
	. "\n $where "
	. "\n ORDER BY a.start_date DESC "
	;
	$JLMS_DB->setQuery($query);
	$total = $JLMS_DB->loadResult();
	
	require_once(_JOOMLMS_FRONT_HOME . DS . "includes" . DS . "classes" . DS . "lms.pagination.php");
	$pageNav = new JLMSPageNav( $total, $limitstart, $limit );

	$query = "SELECT a.*, b.username FROM `#__lms_conference_records` as a LEFT JOIN `#__users` as b ON a.user_id = b.id WHERE a.course_id = $course_id "
	. "\n $where "
	. "\n ORDER BY a.start_date DESC "
	. "\n LIMIT $pageNav->limitstart, $pageNav->limit";
	
	$JLMS_DB->setQuery($query);
	$records = $JLMS_DB->loadObjectList();
	
	JLMS_conference_html::jlms_conference_archive( $course_id, $option, $records, $pageNav );
}

function jlms_record_write($course_id, $option){
	global $my, $JLMS_DB;
	$session_name = mosGetParam($_REQUEST, 'session_name','');
	if ($session_name){
		$query = "SELECT id FROM `#__lms_conference_records` WHERE session_name = '".$session_name."' ";
		$JLMS_DB->setQuery($query);
		$data = $JLMS_DB->loadResult();
		
		if (!$data){
			$query = "INSERT INTO `#__lms_conference_records` (course_id, session_name, start_date, user_id, published ) VALUES ( ".$course_id." ,'".$session_name."', '".date("Y-m-d H:i:s")."', ".$my->id.", 1)";
			$JLMS_DB->setQuery($query);
			$JLMS_DB->query();
		}
	}
}

function jlms_generate_xml_settings($course_id){
	global $JLMS_DB, $JLMS_CONFIG, $my;
		$flashcomroot = $JLMS_CONFIG->get('flascommRoot');
		$pseudo = $my->username;
		$webRoot =  $JLMS_CONFIG->get('live_site');
		$maxclients = $JLMS_CONFIG->get('maxConfClients');
		//$usertype = $JLMS_CONFIG->get('current_usertype');
		$JLMS_ACL = & JLMSFactory::getACL();

/*		$query = "ALTER TABLE `#__lms_conference_doc` ADD `upload_type` TINYINT DEFAULT '0' NOT NULL AFTER `owner_id` , ADD `file_id` INT NOT NULL AFTER `upload_type`";
		$JLMS_DB->setQuery($query);
		$JLMS_DB->query();*/

		$query = "SELECT a.*, b.file_srv_name FROM `#__lms_conference_doc` AS a"
		." LEFT JOIN #__lms_files AS b ON a.file_id=b.id"
		." WHERE a.course_id = $course_id ORDER BY a.doc_id";
		$JLMS_DB -> setQuery($query);
		$files = $JLMS_DB->loadObjectList();

		$iso = explode( '=', _ISO );

		header ('Expires: Fri, 14 Mar 1980 20:53:00 GMT');
		header ('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
		header ('Cache-Control: no-cache, must-revalidate');
		header ('Pragma: no-cache');
		if (class_exists('JFactory')) {
			$document=& JFactory::getDocument();
			$charset_xml = $document->getCharset();
			header ('Content-Type: text/xml; charset='.$charset_xml);
		} else {
			$iso[1] = "utf-8";
			header ('Content-Type: text/xml');
		}
		$feed = '<?xml version="1.0" encoding="'.$iso[1].'" standalone="yes"?>';
		$feed.= "<conference>\n";
		$feed.= "	<version>".$JLMS_CONFIG->get('jlms_version')."</version>\n";
		$feed.= "	<flashcomroot>".$flashcomroot."</flashcomroot>\n";
		$feed.= "	<pseudo>".$pseudo."</pseudo>\n";
		$feed.= "	<maxclients>".$maxclients."</maxclients>\n";
		$feed.= "	<webRoot>".$webRoot."</webRoot>\n";
		$feed.= "	<master>".(($JLMS_ACL->CheckPermissions('conference', 'manage')) ? "yes" : "no" )."</master>\n";
		$feed.= "	<language>\n";
		$feed .= "		<lang num='0'>".$JLMS_CONFIG->get('default_language')."</lang>\n";				//0
		$feed .= "		<title num='1'>"._JLMS_CONFERENCE_TITLE."</title>\n";							//1
		//-----Conference room (Enter)
		$feed .= "		<total_clients num='2'>"._JLMS_CONFERENCE_TOTAL_CLIENTS."</total_clients>\n";
		$feed .= "		<teacher num='3'>"._JLMS_CONFERENCE_TEACHER."</teacher>\n";						//3
		$feed .= "		<clients num='4'>"._JLMS_CONFERENCE_CLIENTS."</clients>\n";
		$feed .= "		<open  num='5'>"._JLMS_CONFERENCE_OPEN."</open>\n";								//5
		$feed .= "		<state num='6'>"._JLMS_CONFERENCE_SERVER_STATE."</state>\n";
		//-----Conference room (LeftBar)
		$feed .= "		<start_rec num='7'>"._JLMS_CONFERENCE_START_RECORD."</start_rec>\n";			//7
		$feed .= "		<stop_rec  num='8'>"._JLMS_CONFERENCE_STOP_RECORD."</stop_rec>\n";
		$feed .= "		<teach_nc  num='9'>"._JLMS_CONFERENCE_TEACH_NC."</teach_nc>\n";					//9
		$feed .= "		<attach_list num='10'>"._JLMS_CONFERENCE_ATTACH_LIST."</attach_list>\n";
		$feed .= "		<my_status num='11'>"._JLMS_CONFERENCE_MY_STATUS."</my_status>\n";				//11
		$feed .= "		<students num='12'>"._JLMS_CONFERENCE_STUDENTS."</students>\n";
		$feed .= "		<stud_nc num='13'>"._JLMS_CONFERENCE_STUD_NC."</stud_nc>\n";						//13
		$feed .= "		<chat num='14'>"._JLMS_CONFERENCE_CHAT."</chat>\n";
		$feed .= "		<cam_voice num='15'>"._JLMS_CONFERENCE_CAM_VOICE."</cam_voice>\n";				//15
		$feed .= "		<cam_switch num='16'>"._JLMS_CONFERENCE_CAM_SWITCH."</cam_switch>\n";
		$feed .= "		<mic_switch num='17'>"._JLMS_CONFERENCE_MIC_SWITCH."</mic_switch>\n";			//17
		$feed .= "		<activate num='18'>"._JLMS_CONFERENCE_ACTIVATE."</activate>\n";
		$feed .= "		<request num='19'>"._JLMS_CONFERENCE_REQUEST_SWITCH."</request>\n";				//19
		$feed .= "		<null num='20'>Null</null>\n";
		$feed .= "		<audiorequest num='21'>"._JLMS_CONFERENCE_AUDIOREQUEST."</audiorequest>\n";		//21
		$feed .= "		<accept num='22'>"._JLMS_CONFERENCE_ACCEPT."</accept>\n";
		$feed .= "		<refuse num='23'>"._JLMS_CONFERENCE_REFUSE."</refuse>\n";						//23
		$feed .= "		<refused num='24'>"._JLMS_CONFERENCE_REFUSED."</refused>\n";
		$feed .= "		<ok num='25'>"._JLMS_CONFERENCE_OK."</ok>\n";									//25
		//-----Conference room (Whiteboard)
		$feed .= "		<clear num='26'>"._JLMS_CONFERENCE_CLEAR_ALL."</clear>\n";							
		$feed .= "		<selection num='27'>"._JLMS_CONFERENCE_SELECTION."</selection>\n";				//27
		$feed .= "		<pencil num='28'>"._JLMS_CONFERENCE_PENCIL."</pencil>\n";
		$feed .= "		<line num='29'>"._JLMS_CONFERENCE_LINE."</line>\n";								//29
		$feed .= "		<circle num='30'>"._JLMS_CONFERENCE_CIRCLE."</circle>\n";
		$feed .= "		<circle_filled num='31'>"._JLMS_CONFERENCE_CIRCLE_FILLED."</circle_filled>\n";	//31
		$feed .= "		<rectan num='32'>"._JLMS_CONFERENCE_RECTAN."</rectan>\n";
		$feed .= "		<rectan_filled num='33'>"._JLMS_CONFERENCE_RECTAN_FILLED."</rectan_filled>\n";	//33
		$feed .= "		<back num='34'>"._JLMS_CONFERENCE_BACK."</back>\n";
		$feed .= "		<translucent num='35'>"._JLMS_CONFERENCE_LINE_TRANS."</translucent>\n";			//35
		$feed .= "		<text num='36'>"._JLMS_CONFERENCE_TEXT."</text>\n";
		$feed .= "		<arrow num='37'>"._JLMS_CONFERENCE_ARROW."</arrow>\n";							//37
		$feed .= "		<delete num='38'>"._JLMS_CONFERENCE_DELETE."</delete>\n";
		//-----Conference room (Top bar)
		$feed .= "		<filelist num='39'>"._JLMS_CONFERENCE_FILELIST."</filelist>\n";					//39
		$feed .= "		<single num='40'>"._JLMS_CONFERENCE_SINGLE."</single>\n";
		$feed .= "		<multi num='41'>"._JLMS_CONFERENCE_MULTIPLE."</multi>\n";						//41
		$feed .= "		<upload num='42'>"._JLMS_CONFERENCE_UPLOAD."</upload>\n";
		$feed .= "		<web num='43'>"._JLMS_CONFERENCE_WEB."</web>\n";									//43
		$feed .= "		<quit num='44'>"._JLMS_CONFERENCE_QUIT."</quit>\n";
		//----------------------------------errors
		$feed .= "		<err1 num='45'>"._JLMS_CONFERENCE_ERR1."</err1>\n";								//45
		$feed .= "		<err2 num='46'>"._JLMS_CONFERENCE_ERR2."</err2>\n";
		//----------------------------------Set color style --------------------------------------//
		$feed .= "		<globBg num='47'>".str_replace('#', '0x', $JLMS_CONFIG->get('conf_background'))."</globBg>\n";										//47
		$feed .= "		<mainBg num='48'>".str_replace('#', '0x', $JLMS_CONFIG->get('conf_main_color'))."</mainBg>\n";
		$feed .= "		<borderCl num='49'>".str_replace('#', '0x', $JLMS_CONFIG->get('conf_border_color'))."</borderCl>\n";									//49
		$feed .= "		<borderClInside num='50'>".str_replace('#', '0x', $JLMS_CONFIG->get('conf_border_color'))."</borderClInside>\n";
		$feed .= "		<titleBg num='51'>".str_replace('#', '0x', $JLMS_CONFIG->get('conf_title_color'))."</titleBg>\n";									//51
		$feed .= "		<textCl num='52'>".str_replace('#', '0x', $JLMS_CONFIG->get('conf_title_font_color'))."</textCl>\n";
		$feed .= "		<toolbarBg num='53'>".str_replace('#', '0x', $JLMS_CONFIG->get('conf_toolbar_color'))."</toolbarBg>\n";
		$feed .= "		<buttonTextCl num='54'>".str_replace('#', '0x', $JLMS_CONFIG->get('conf_files_font_color'))."</buttonTextCl>\n";
		//---------------------------------add langs params----------------------------------------//
		$feed .= "		<deactivate num='55'>"._JLMS_CONFERENCE_DEACTIVATE."</deactivate>\n";
		$feed .= "		<but_deactivate num='56'>"._JLMS_CONFERENCE_BUTTON_DEACTIVATE."</but_deactivate>\n";
		$feed .= "		<record num='57'>"._JLMS_CONFERENCE_RECORD."</record>\n";
		$feed .= "		<playback num='58'>"._JLMS_CONFERENCE_RECORD_PB."</playback>\n";
		$feed .= "		<pause num='59'>"._JLMS_CONFERENCE_PAUSE_RECORD."</pause>\n";
		//---------------------------------alerts langs params----------------------------------------//
		$feed .= "		<alert num='60'>"._JLMS_CONFERENCE_DISK_SPACE."</alert>\n";		
		//---------------------------------added on 15.01.2009---------------------------------------//
		$feed .= "		<quality num='61'>"._JLMS_CONFERENCE_MAXIMIZE."</quality>\n";
		$feed .= "		<quality num='62'>"._JLMS_CONFERENCE_MINIMIZE."</quality>\n";
		$feed .= "		<quality num='63'>"._JLMS_CONFERENCE_VIDEO_QUALITY."</quality>\n";
		$feed .= "		<quality num='64'>"._JLMS_CONFERENCE_MIC_QUALITY."</quality>\n";
		$feed .= "		<quality num='65'>"._JLMS_CONFERENCE_SHOW_QUALITY."</quality>\n";
		$feed .= "		<quality num='66'>"._JLMS_CONFERENCE_HIDE_QUALITY."</quality>\n";
		$feed .= "		<quality num='67'>"._JLMS_CONFERENCE_DRAG."</quality>\n";
		$feed .= "		<quality num='68'>"._JLMS_CONFERENCE_CLOSE."</quality>\n";
		$feed .= "	</language>\n";
		$feed .= "	<files>\n";
		foreach ($files as $file){
			if($file->upload_type==1){
				$srv_name = _JOOMLMS_DOC_FOLDER . $file->file_srv_name;
				//if (file_exists(_JOOMLMS_DOC_FOLDER.$file->filename)){	
				if ( file_exists( $srv_name ) && is_readable( $srv_name ) ){			
					$feed .= "<file>\n";
					$feed .= "<filename>".$file->filename."</filename>\n";
					$feed .= "<fileid>".$file->doc_id."</fileid>\n";
					$feed .= "<filetype>".$file->upload_type."</filetype>\n";
					$feed .= "</file>\n";
				}
			}
			else{
				if (file_exists(_JOOMLMS_FRONT_HOME."/upload/".$file->filename)){
					$feed .= "<file>\n";
					$feed .= "<filename>".$file->filename."</filename>\n";
					$feed .= "<fileid>".$file->doc_id."</fileid>\n";
					$feed .= "<filetype>".$file->upload_type."</filetype>\n";
					$feed .= "</file>\n";
				}
			}
		}
		$feed .= "	</files>\n";
		$feed.= "</conference>\n";
		echo $feed; die;
}

function jlms_getFiles($course_id ){
	global $Itemid, $my, $JLMS_DB;
	$JLMS_CONFIG = & JLMSFactory::getConfig();
	//$pseudo = strtoupper($login);
/*	$query = "ALTER TABLE `#__lms_conference_doc` ADD `upload_type` TINYINT DEFAULT '0' NOT NULL AFTER `owner_id` , ADD `file_id` INT NOT NULL AFTER `upload_type`";
	$JLMS_DB->setQuery($query);
	$JLMS_DB->query();*/

	$query = "SELECT filename FROM `#__lms_conference_doc` WHERE course_id = $course_id ORDER BY doc_id";
	$JLMS_DB -> setQuery($query);
	$files = $JLMS_DB->loadObjectList();
	
	$i = 0;
	$files_list = '';
	foreach ($files as $file){
		if (file_exists($JLMS_CONFIG->get('absolute_path')."/components/com_joomla_lms/upload/".$file->filename)){
			$files_list .= "&amp;arg".$i."=". urlencode($file->filename);
			$i++;
		}
	}
	return $files_list;
}

function jlms_displayFile($course_id, $option){
	global $Itemid, $my, $JLMS_DB;
	$JLMS_CONFIG = & JLMSFactory::getConfig();
	
	$id = intval( mosGetParam( $_REQUEST, 'id', 0 ) );
	$filename = mosGetParam( $_REQUEST, 'filename', '' );
	if($id==-2){
		JLMSRedirect($filename);
	}

	if($id==-1){
		$file = new stdClass();
		$file->filename = "JLMS_intro.swf";
		$file->upload_type = 0;
	} else {
/*		$query = "ALTER TABLE `#__lms_conference_doc` ADD `upload_type` TINYINT DEFAULT '0' NOT NULL AFTER `owner_id` , ADD `file_id` INT NOT NULL AFTER `upload_type`";
		$JLMS_DB->setQuery($query);
		$JLMS_DB->query();*/

		$query = "SELECT * FROM `#__lms_conference_doc` WHERE course_id = $course_id AND doc_id=$id ORDER BY doc_id";
		$JLMS_DB -> setQuery($query);
		$file = $JLMS_DB->loadObject();
	}
	//print_r($file);
	if(is_object($file)){
		if($file->upload_type==1){
			$headers = array();
			$headers['Content-Disposition'] = 'inline';
			JLMS_downloadFile( $file->file_id, $option, '', true, $headers);
		}
		else{
			$file_name = $file->filename;
			$srv_name = $JLMS_CONFIG->get('absolute_path')."/components/com_joomla_lms/upload/".$file_name;
			if ( file_exists( $srv_name ) && is_readable( $srv_name ) ){
				$v_date = date("Y-m-d H:i:s");
				if (preg_match('/Opera(\/| )([0-9].[0-9]{1,2})/', $_SERVER['HTTP_USER_AGENT'])) {
					$UserBrowser = "Opera";
				}
				elseif (preg_match('/MSIE ([0-9].[0-9]{1,2})/', $_SERVER['HTTP_USER_AGENT'])) {
					$UserBrowser = "IE";
				} else {
					$UserBrowser = '';
				}
				$mime_type = ($UserBrowser == 'IE' || $UserBrowser == 'Opera') ? 'application/octetstream' : 'application/octet-stream';
				header('Content-Type: ' . $mime_type );
				header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
				if ($UserBrowser == 'IE') {
					header('Content-Disposition: inline; filename="' . $file_name . '";');
					header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
					header('Content-Length: '. filesize($srv_name)); 
					header('Pragma: public');
				} else {
					header('Content-Disposition: inline; filename="' . $file_name . '";');
					header('Content-Length: '. filesize($srv_name)); 
					header('Pragma: no-cache');
				}
				@ob_end_clean();
				readfile( $srv_name );
				exit();
			}
		}
	}
}

function jlms_conference_cb_profile( $option ){
	global $my, $Itemid, $JLMS_DB;
	$username = strval(mosGetParam($_REQUEST,'username',''));

	$query = "SELECT id FROM `#__users` WHERE username=".$JLMS_DB->quote($username);
	$JLMS_DB->setQuery($query);
	$user_id = $JLMS_DB->loadResult();

	JLMSRedirect(sefRelToAbs('index.php?option=com_comprofiler&amp;task=userProfile&amp;user='.$user_id));
}


function jlms_conference_profile($option){
	global $my, $Itemid, $JLMS_DB;
	$username = mosGetParam($_REQUEST,'username','');
	
	$query = "SELECT username, email FROM `#__users` WHERE username='$username'";
	$JLMS_DB -> setQuery($query);
	$user_data = $JLMS_DB->loadObject();
	echo "<div class='componentheading'>"._JLMS_CONFERENCE_PROFILE."</div>";
	echo "<div style='padding:10px'>"._JLMS_CONFERENCE_USERNAME.(isset($user_data->username) ? $user_data->username : '')."<br /><br />";
	echo _JLMS_CONFERENCE_EMAIL."<a href='mailto:".(isset($user_data->email) ? $user_data->email : '')."'>".(isset($user_data->email) ? $user_data->email : '')."</a></div>";
}

function getExtension($chaine)
{	
	$taille = strlen($chaine)-1;
	for ($i = $taille; $i >= 0; $i--)
		if ($chaine["$i"] == '.') break;
		
	return substr($chaine, $i+1, strlen($chaine)-($i+1));
}


function jlms_conference_upload($option, $id){
	global $my, $Itemid, $JLMS_DB, $JLMS_CONFIG; ?>
	<script type="text/javascript" src="<?php echo $JLMS_CONFIG->get('live_site');?>'/components/com_joomla_lms/includes/js/swfobject.js"></script>');
	<script type="text/javascript">
	function test(){
		//alert('test');
	}
	</script>
	<?php	

		$file_id = JLMS_uploadFile($course_id, 'fichier');
		if(!$file_id){
			echo "<script> alert('Upload failed or file extension is not supported.'); window.history.go(-1); </script>\n";
			exit();
		}
		else{
		
			$query = "INSERT INTO `#__lms_conference_doc` (course_id, owner_id, upload_type, filename, file_id) VALUES (".intval($id).", ".(int)$my->id.", 1, '".$_FILES['fichier']['name']."', ".intval($file_id)." )";
			$JLMS_DB -> setQuery($query);
			//$JLMS_DB->getErrorMsg();
			if ($JLMS_DB -> query()){
				$file_id = $JLMS_DB->insertid();				
			?>		
				<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,29,0" width="300" height="300" id="upload" align="middle">
				<param name="allowScriptAccess" value="sameDomain" />
				<param name="movie" value="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/includes/conference/upload_106.swf?fileName=<?php echo urlencode(utf8_encode($_FILES['fichier']['name']))."&course_id=".$id."&file_id=".$file_id; ?>" />
				<param name="quality" value="high" />
				<param name="bgcolor" value="#ffffff" />
				<embed src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/includes/conference/upload_106.swf?fileName=<?php echo urlencode(utf8_encode($_FILES['fichier']['name']))."&course_id=".$id."&file_id=".$file_id; ?>" quality="high" bgcolor="#ffffff" width="300" height="300" name="upload" align="middle" allowScriptAccess="sameDomain" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />
				</object>
			<?php			
			}	
		}
}

function jlms_delete_file( $course_id ){
	global $my, $Itemid, $JLMS_DB;

	$doc_id  = intval(mosGetParam ($_REQUEST, 'file_id', 0));	


	$query = "SELECT a.*, b.file_srv_name FROM `#__lms_conference_doc` AS a"
	." LEFT JOIN #__lms_files AS b ON a.file_id=b.id"
	." WHERE a.course_id = $course_id AND doc_id = $doc_id";
	$JLMS_DB -> setQuery($query);
	$file = $JLMS_DB->loadObject();
	if (is_object($file)) {
		if(isset($file->upload_type) && ( $file->upload_type == 1 ) && $file->file_srv_name && $file->file_id){
			if (file_exists(_JOOMLMS_DOC_FOLDER.$file->file_srv_name)){
				unlink (_JOOMLMS_DOC_FOLDER.$file->file_srv_name);
			}
			$query = "DELETE FROM #__lms_files WHERE id = ".$file->file_id;
			$JLMS_DB -> setQuery($query);
			$JLMS_DB -> query();
		} elseif(isset($file->filename) && $file->filename) {
			if (file_exists(_JOOMLMS_FRONT_HOME."/upload/".$file->filename)){
				unlink (_JOOMLMS_FRONT_HOME."/upload/".$file->filename);
			}			
		}		
	}
	$query = "DELETE FROM #__lms_conference_doc WHERE course_id = '$course_id' AND doc_id = $doc_id";
	$JLMS_DB -> setQuery($query);
	$JLMS_DB -> query();
}

//-----------Booking (for teacher only)--------///
function jlms_booking_list($course_id, $option) {
	global $my, $JLMS_DB, $Itemid, $JLMS_SESSION, $JLMS_CONFIG, $JLMS_CONFIG;

	$limit		= intval( mosGetParam( $_GET, 'limit', $JLMS_SESSION->get('list_limit',$JLMS_CONFIG->get('list_limit')) ) );
	$JLMS_SESSION->set('list_limit', $limit);
	$limitstart = intval( mosGetParam( $_GET, 'limitstart', $JLMS_SESSION->get('limitstart_confbook',-99) ) );
	$do_requery = false;
	if ($limitstart == -99) {
		$limitstart = 0;
		$do_requery = true;
	}
	$JLMS_SESSION->set('limitstart_confbook', $limitstart);
	$filter_stu	= intval( mosGetParam( $_REQUEST, 'filter_stu', $JLMS_SESSION->get('filter_stu',0) ) );
	$filter_teach = intval( mosGetParam( $_REQUEST, 'filter_teach', $JLMS_SESSION->get('filter_teach',$my->id) ) );
	$JLMS_SESSION->set('filter_stu', $filter_stu);
	$JLMS_SESSION->set('filter_teach', $filter_teach);
	//$usertype = $JLMS_CONFIG->get('current_usertype', 0);
	$JLMS_ACL = & JLMSFactory::getACL();
	if ( $course_id && ($JLMS_ACL->CheckPermissions('conference', 'manage'))) {
		if ($filter_stu) {
			$query = "SELECT COUNT(distinct a.p_id) FROM #__lms_conference_period as a, #__lms_conference_usr as b WHERE course_id='".$course_id."'"
			. ($filter_teach ? ("\n AND a.teacher_id = $filter_teach ") : '')
			. "\n AND a.p_id = b.p_id AND b.user_id = $filter_stu";
		} else {
			$query = "SELECT COUNT(*) FROM #__lms_conference_period WHERE course_id='".$course_id."'"
			. ($filter_teach ? ("\n AND teacher_id = $filter_teach ") : '');
		}
		$JLMS_DB -> setQuery($query);
		$total = $JLMS_DB->LoadResult();
		require_once(_JOOMLMS_FRONT_HOME . DS . "includes" . DS . "classes" . DS . "lms.pagination.php");
		$pageNav = new JLMSPageNav( $total, $limitstart, $limit );
		if ($filter_stu) {
			$query = "SELECT a.*, b.name, b.id as user_teacher_id, b.username FROM #__lms_conference_period as a LEFT JOIN #__users as b ON a.teacher_id = b.id, #__lms_conference_usr as c WHERE a.course_id='".$course_id."'"
			. ($filter_teach ? ("\n AND a.teacher_id = $filter_teach ") : '')
			. "\n AND a.p_id = c.p_id AND c.user_id = $filter_stu";
			$query .= "\n ORDER BY a.from_time";
			$query .= "\n LIMIT $pageNav->limitstart, $pageNav->limit";
		} else {
			$query = "SELECT a.*, b.name, b.id as user_teacher_id, b.username FROM #__lms_conference_period as a LEFT JOIN #__users as b ON a.teacher_id = b.id WHERE a.course_id='".$course_id."'"
			. ($filter_teach ? ("\n AND a.teacher_id = $filter_teach ") : '');
			$query .= "\n ORDER BY a.from_time";
			$query .= "\n LIMIT $pageNav->limitstart, $pageNav->limit";
		}
		$JLMS_DB -> setQuery($query);
		$rows = $JLMS_DB->LoadObjectList();

		if ($do_requery) {
			$is_found = false;
			foreach ($rows as $row) {
				if ($row->from_time >= ( time() + $JLMS_CONFIG->get('offset') * 60 * 60 )) {
					$is_found = true;
					break;
				}
			}
			if (!$is_found) {
				$do_search = true;
				$lim_s = $pageNav->limitstart;
				$lim = $pageNav->limit;
				if (($lim_s + $lim) > $total) {
					$do_search = false;
				}
				while ($do_search) {
					$lim_s = $lim_s + $lim;
					if ($filter_stu) {
						$query = "SELECT a.*, b.name, b.id as user_teacher_id, b.username FROM #__lms_conference_period as a LEFT JOIN #__users as b ON a.teacher_id = b.id, #__lms_conference_usr as c WHERE a.course_id='".$course_id."'"
						. ($filter_teach ? ("\n AND a.teacher_id = $filter_teach ") : '')
						. "\n AND a.p_id = c.p_id AND c.user_id = $filter_stu";
						$query .= "\n ORDER BY a.from_time";
						$query .= "\n LIMIT $lim_s, $lim";
					} else {
						$query = "SELECT a.*, b.name, b.id as user_teacher_id, b.username FROM #__lms_conference_period as a LEFT JOIN #__users as b ON a.teacher_id = b.id WHERE a.course_id='".$course_id."'"
						. ($filter_teach ? ("\n AND a.teacher_id = $filter_teach ") : '');
						$query .= "\n ORDER BY a.from_time";
						$query .= "\n LIMIT $lim_s, $lim";
					}
					$JLMS_DB->setQuery($query);
					$rows1 = $JLMS_DB->loadObjectList();
					if (($lim_s + $lim) > $total) {
						$do_search = false;
					}
					foreach ($rows1 as $row_tmp) {
						if ($row_tmp->from_time >= ( time() + $JLMS_CONFIG->get('offset') * 60 * 60 )) {
							$is_found = true;
							$do_search = false;
							break;
						}
					}
				}
				if ($is_found) {
					$rows = $rows1;
					$pageNav->limitstart = $lim_s;
					$pageNav->limit = $lim;
					$JLMS_SESSION->set('limitstart_confbook', $lim_s);
				}
			}
		}
		$lists = array();

		$query = "SELECT a.id as value, a.name as text FROM #__users as a, #__lms_user_courses as c"
		. "\n WHERE a.id = c.user_id AND c.course_id = '".$course_id."'"
		. "\n ORDER BY a.name";
		$JLMS_DB->SetQuery( $query );
		$teachers = $JLMS_DB->LoadObjectList();
		$teachers_s = array();
		$teachers_s[] = mosHTML::makeOption( 0, ' - Select a teacher - ');
		$i = 1;
		foreach ($teachers as $teacher){
			$teachers_s[] = mosHTML::makeOption( $teacher->value, $teacher->text );
			$i++;
		}
		$javascript = 'onchange="document.adminForm.submit();"';
		$lists['filter_teach'] = mosHTML::selectList($teachers_s, 'filter_teach', 'class="inputbox" size="1" '.$javascript, 'value', 'text', $filter_teach );

		$query = "SELECT a.id, a.name, a.username, a.email, b.ug_name"
		. "\n FROM #__users as a, #__lms_users_in_groups as c LEFT JOIN #__lms_usergroups as b ON c.group_id = b.id AND b.course_id = '".$course_id."'"
		. "\n WHERE a.id = c.user_id AND c.course_id = '".$course_id."'"
		. "\n ORDER BY a.username";
		$JLMS_DB->SetQuery( $query );
		$users = $JLMS_DB->LoadObjectList();
		$users_s = array();
		$users_s[] = mosHTML::makeOption( 0, ' - Select a student - ');
		$i = 1;
		foreach ($users as $user){
			$users_s[] = mosHTML::makeOption( $user->id, $user->username . ' ('.$user->name.')' );
			$i++;
		}
		$javascript = 'onchange="document.adminForm.submit();"';
		$lists['filter_stu'] = mosHTML::selectList($users_s, 'filter_stu', 'class="inputbox" size="1" '.$javascript, 'value', 'text', $filter_stu );

		JLMS_conference_html::jlms_booking_list( $course_id, $option, $rows, $pageNav, $lists );
	} else {
		jlmsRedirect( sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=conference&id=$course_id") );
	}
}
function jlms_booking_edit($cid, $course_id, $option)
{
global $my, $JLMS_DB, $Itemid, $JLMS_SESSION, $JLMS_CONFIG;
	
	$JLMS_ACL = & JLMSFactory::getACL();
	if ( $course_id && ($JLMS_ACL->CheckPermissions('conference', 'manage'))) {
	//$usertype = $JLMS_CONFIG->get('current_usertype', 0);
	//if ( $course_id && ($usertype == 1)) {

		$course_params = $JLMS_CONFIG->get('course_params');
		$params_cb = new JLMSParameters($course_params);
		$en_book = $params_cb->get('conf_book', 0);

		if($cid)
		{
			$query = "SELECT * FROM #__lms_conference_period WHERE p_id = '".$cid."'";
			$JLMS_DB -> setQuery($query);
			$row = $JLMS_DB->LoadObjectList();
			$from_hour = date("H",$row[0]->from_time);
			$from_minutes = date("i",$row[0]->from_time);
			$to_hour = date("H",$row[0]->to_time);
			$to_minutes = date("i",$row[0]->to_time);
			$row[0]->cur_date = date("Y-m-d",$row[0]->from_time);
			
		}
		else 
		{
			$row[0] = new stdClass();
			$row[0]->p_id = 0;
			$row[0]->public = 0;
			$from_hour = 0;
			$from_minutes = 0;
			$to_hour = 0;
			$to_minutes = 0;
			$row[0]->cur_date = date("Y-m-d");
			$row[0]->p_name = '';
			$row[0]->p_description = '';
			$row[0]->teacher_id = $my->id;
			
		}
		$lists = array();
		$times = array();
		for($i=0;$i<24;$i++)
		{
			$times[$i]->value = $i;
			if($i<10)
				$times[$i]->text = '0'.$i;
			else
				$times[$i]->text = $i;
		}
		$minutes = array();
		for($i=0;$i<4;$i++)
		{
			$minutes[$i]->value = $i*15;
			if($i==0)
				$minutes[$i]->text = '00';	
			else 
				$minutes[$i]->text = $i*15;
		}

		$lists['from_time'] = mosHTML::selectList($times, 'from_time', 'class="inputbox" size="1" ', 'value', 'text', $from_hour );
		$lists['to_time'] = mosHTML::selectList($times, 'to_time', 'class="inputbox" size="1" ', 'value', 'text', $to_hour );
		$lists['from_minutes'] = mosHTML::selectList($minutes, 'from_min', 'class="inputbox" size="1" ', 'value', 'text', $from_minutes );
		$lists['to_minutes'] = mosHTML::selectList($minutes, 'to_min', 'class="inputbox" size="1" ', 'value', 'text', $to_minutes );

		$query = "SELECT a.id as value, a.name as text FROM #__users as a, #__lms_user_courses as c"
		. "\n WHERE a.id = c.user_id AND c.course_id = '".$course_id."'"
		. "\n ORDER BY a.name";
		$JLMS_DB->SetQuery( $query );
		$teachers = $JLMS_DB->LoadObjectList();
		$teachers_s = array();
		$teachers_s[] = mosHTML::makeOption( 0, ' - Select a teacher - ');
		$i = 1;
		foreach ($teachers as $teacher){
			$teachers_s[] = mosHTML::makeOption( $teacher->value, $teacher->text );
			$i++;
		}
		$lists['teacher_id'] = mosHTML::selectList($teachers_s, 'teacher_id', 'class="inputbox" size="1" ', 'value', 'text', $row[0]->teacher_id );

		JLMS_conference_html::jlms_booking_edit( $course_id, $option, $row, $lists, $en_book );
		
	}

	
}
function jlms_booking_save($course_id, $option)
{
	global $my, $JLMS_DB, $Itemid, $JLMS_SESSION, $JLMS_CONFIG;
	$cid = intval(mosgetparam($_POST,'p_id',0));
	//$usertype = $JLMS_CONFIG->get('current_usertype', 0);
	$sel_option = intval(mosgetparam($_POST,"sel_option",0));
	$from_time = mosgetparam($_POST,'from_time',0);
	$to_time = mosgetparam($_POST,'to_time',0);
	$from_minutes = mosgetparam($_POST,'from_min',0);
	$to_minutes = mosgetparam($_POST,'to_min',0);
	$teacher_id = intval(mosgetparam($_POST,'teacher_id',0));
	$c_public = intval(mosgetparam($_POST,'c_public',0));
	$p_name = strval(mosgetparam($_POST,'p_name',''));
	$p_description = strval(mosgetparam($_POST,'p_description',''));
	$JLMS_ACL = & JLMSFactory::getACL();
	if ( $course_id && ($JLMS_ACL->CheckPermissions('conference', 'manage'))) {
//	if ( $course_id && ($usertype == 1)) {

		if (!defined('_JLMS_CONF_BOOK_EXISTS')) {
			define('_JLMS_CONF_BOOK_EXISTS','Conference with the same time period is already exists');
		}

		if($cid)
		{
			$save_date = JLMS_dateToDB(mosGetParam($_REQUEST,'start_date',date('Y-m-d')));

				$daytime_from = mktime($from_time,$from_minutes,0,substr($save_date,5,2),substr($save_date,8,2),substr($save_date,0,4));
				$daytime_to = mktime($to_time,$to_minutes,0,substr($save_date,5,2),substr($save_date,8,2),substr($save_date,0,4));
				if ($teacher_id) {
					$query = "SELECT COUNT(*) FROM #__lms_conference_period WHERE teacher_id = $teacher_id AND ((from_time > ".$daytime_from." AND from_time < ".$daytime_to.") OR (to_time > ".$daytime_from." AND to_time < ".$daytime_to.") OR (from_time < ".$daytime_from." AND to_time > ".$daytime_from.") OR (from_time < ".$daytime_to." AND to_time > ".$daytime_to.") OR (from_time = ".$daytime_from." AND to_time = ".$daytime_to.")) AND p_id <> $cid";
				} else {
					$query = "SELECT COUNT(*) FROM #__lms_conference_period WHERE course_id='".$course_id."' AND teacher_id = $teacher_id AND ((from_time > ".$daytime_from." AND from_time < ".$daytime_to.") OR (to_time > ".$daytime_from." AND to_time < ".$daytime_to.") OR (from_time < ".$daytime_from." AND to_time > ".$daytime_from.") OR (from_time < ".$daytime_to." AND to_time > ".$daytime_to.") OR (from_time = ".$daytime_from." AND to_time = ".$daytime_to.")) AND p_id <> $cid";
				}
				$JLMS_DB -> setQuery($query);
				if ($JLMS_DB->LoadResult()) {
					echo "<script> alert('"._JLMS_CONF_BOOK_EXISTS."'); window.history.go(-1); </script>\n";
					exit();
				}
				if ($c_public) {
					$query = "SELECT count(*) FROM #__lms_conference_usr WHERE p_id = $cid";
					$JLMS_DB->setQuery($query);
					$is_subscribers = $JLMS_DB->LoadResult();
					if ($is_subscribers) {
						$c_public = 0;
					}
				}
				$query = "UPDATE #__lms_conference_period SET teacher_id = $teacher_id, from_time = '".$daytime_from."',to_time= '".$daytime_to."',public = '".$c_public."',p_name = ".$JLMS_DB->Quote($p_name).", p_description = ".$JLMS_DB->Quote($p_description);
				$query .= "\n WHERE p_id = $cid";
				$JLMS_DB->setQuery($query);
				$JLMS_DB->query();
		}
		else 
		{
			if($sel_option)
			{
				$all_dates = array();
				if (count($_POST['weekday']) && count($_POST['monthday']))
				{
					foreach ($_POST['monthday'] as $month_year)
					{
						$month = substr($month_year,0,2);
						$year = substr($month_year,3,4);
						$all_dates = Daysforweeks($_POST['weekday'],$month,$year);
						if(count($all_dates))
						{
							foreach ($all_dates as $adata)
							{
								$daytime_from = mktime($from_time,$from_minutes,0,substr($adata,5,2),substr($adata,8,2),substr($adata,0,4));
								$daytime_to = mktime($to_time,$to_minutes,0,substr($adata,5,2),substr($adata,8,2),substr($adata,0,4));
								//$query = "SELECT COUNT(*) FROM #__lms_conference_period WHERE course_id='".$course_id."' AND teacher_id = $teacher_id AND ((from_time < ".$daytime_from." AND to_time > ".$daytime_from.") OR (from_time < ".$daytime_to." AND to_time > ".$daytime_to."))";
								if ($teacher_id) {
									$query = "SELECT COUNT(*) FROM #__lms_conference_period WHERE teacher_id = $teacher_id AND ((from_time > ".$daytime_from." AND from_time < ".$daytime_to.") OR (to_time > ".$daytime_from." AND to_time < ".$daytime_to.") OR (from_time < ".$daytime_from." AND to_time > ".$daytime_from.") OR (from_time < ".$daytime_to." AND to_time > ".$daytime_to.") OR (from_time = ".$daytime_from." AND to_time = ".$daytime_to."))";
								} else {
									$query = "SELECT COUNT(*) FROM #__lms_conference_period WHERE course_id='".$course_id."' AND teacher_id = $teacher_id AND ((from_time > ".$daytime_from." AND from_time < ".$daytime_to.") OR (to_time > ".$daytime_from." AND to_time < ".$daytime_to.") OR (from_time < ".$daytime_from." AND to_time > ".$daytime_from.") OR (from_time < ".$daytime_to." AND to_time > ".$daytime_to.") OR (from_time = ".$daytime_from." AND to_time = ".$daytime_to."))";
								}
								$JLMS_DB -> setQuery($query);
								if ($JLMS_DB->LoadResult()) {
									echo "<script> alert('"._JLMS_CONF_BOOK_EXISTS."'); window.history.go(-1); </script>\n";
									exit();
								}
								$query = "INSERT INTO #__lms_conference_period(course_id, teacher_id, from_time, to_time,public, p_name, p_description)";
								$query .= "\n VALUES ('".$course_id."', $teacher_id, '".$daytime_from."','".$daytime_to."', $c_public, ".$JLMS_DB->Quote($p_name).", ".$JLMS_DB->Quote($p_description).")";
								$JLMS_DB->setQuery($query);
								$JLMS_DB->query();
							}
						}
					} 
				}
			}
			else 
			{
				$save_date = JLMS_dateToDB(mosGetParam($_REQUEST,'start_date',date('Y-m-d')));

				$daytime_from = mktime($from_time,$from_minutes,0,substr($save_date,5,2),substr($save_date,8,2),substr($save_date,0,4));
				$daytime_to = mktime($to_time,$to_minutes,0,substr($save_date,5,2),substr($save_date,8,2),substr($save_date,0,4));
				
				//$query = "SELECT COUNT(*) FROM #__lms_conference_period WHERE course_id='".$course_id."' AND teacher_id = $teacher_id AND ((from_time < ".$daytime_from." AND to_time > ".$daytime_from.") OR (from_time < ".$daytime_to." AND to_time > ".$daytime_to."))";
				if ($teacher_id) {
					$query = "SELECT COUNT(*) FROM #__lms_conference_period WHERE teacher_id = $teacher_id AND ((from_time > ".$daytime_from." AND from_time < ".$daytime_to.") OR (to_time > ".$daytime_from." AND to_time < ".$daytime_to.") OR (from_time < ".$daytime_from." AND to_time > ".$daytime_from.") OR (from_time < ".$daytime_to." AND to_time > ".$daytime_to.") OR (from_time = ".$daytime_from." AND to_time = ".$daytime_to."))";
				} else {
					$query = "SELECT COUNT(*) FROM #__lms_conference_period WHERE course_id='".$course_id."' AND teacher_id = $teacher_id AND ((from_time > ".$daytime_from." AND from_time < ".$daytime_to.") OR (to_time > ".$daytime_from." AND to_time < ".$daytime_to.") OR (from_time < ".$daytime_from." AND to_time > ".$daytime_from.") OR (from_time < ".$daytime_to." AND to_time > ".$daytime_to.") OR (from_time = ".$daytime_from." AND to_time = ".$daytime_to."))";
				}
				$JLMS_DB->setQuery($query);
				if ($JLMS_DB->LoadResult()) {
					echo "<script> alert('"._JLMS_CONF_BOOK_EXISTS."'); window.history.go(-1); </script>\n";
					exit();
				}
				$query = "INSERT INTO #__lms_conference_period(course_id, teacher_id, from_time,to_time,public,p_name,p_description)";
				$query .= "\n VALUES ('".$course_id."', $teacher_id, '".$daytime_from."','".$daytime_to."', $c_public, ".$JLMS_DB->Quote($p_name).", ".$JLMS_DB->Quote($p_description).")";
				$JLMS_DB->setQuery($query);
				$JLMS_DB->query();
			}
		}
	}
	JLMSRedirect(sefRelToAbs('index.php?option='.$option.'&amp;task=conference&amp;mode=booking&amp;id='.$course_id));
}
function jlms_booking_delete($cid, $option, $course_id)
{
	global $JLMS_DB;
	if (count($cid))
	{
		$cids = implode( ',', $cid );
		$query = "DELETE FROM #__lms_conference_period WHERE p_id IN (".$cids.")";
		$JLMS_DB->setQuery($query);
		if (!$JLMS_DB->query()) {
			echo "<script> alert('".$JLMS_DB->getErrorMsg()."'); window.history.go(-1); </script>\n";
			exit();
		}
		$query = "DELETE FROM #__lms_conference_usr WHERE p_id IN (".$cids.")";
		$JLMS_DB->setQuery($query);
		if (!$JLMS_DB->query()) {
			echo "<script> alert('".$JLMS_DB->getErrorMsg()."'); window.history.go(-1); </script>\n";
			exit();
		}
	}
	JLMSRedirect(sefRelToAbs('index.php?option='.$option.'&amp;task=conference&amp;mode=booking&amp;id='.$course_id));
}

function jlms_booking_users($course_id, $option) {
	global $JLMS_DB,$JLMS_CONFIG;
	$pid = intval(mosgetparam($_GET,"pid",0));	
	$user_array = array();
	$lists = array();
	if($pid) {
		
		$query = "SELECT user_id FROM #__lms_conference_usr WHERE p_id = '".$pid."'";
		$JLMS_DB->setQuery($query);
		$ids = $JLMS_DB->LoadResultArray();
		
		if(count($ids)) {
			$ids_users = implode(',', $ids);
		}
		else {
			$ids_users = '';
		}
		
		$query = "SELECT a.user_id, b.name, b.username FROM #__lms_conference_usr as a, #__users as b WHERE a.p_id = '".$pid."' AND b.id = a.user_id";
		$JLMS_DB->setQuery($query);
		$rows = $JLMS_DB->LoadObjectList();
		

		$query = "SELECT a.*"
		. "\n FROM #__users as a, "
		. "\n #__lms_users_in_groups as b, #__lms_usertypes as c "
		. "\n WHERE a.id = b.user_id AND b.role_id = c.id AND b.course_id = '".$course_id."'" 
		. ($ids_users ? "\n AND a.id NOT IN (".$ids_users.")" : '')
		. "\n group by a.id ORDER BY a.username, a.name";
		$JLMS_DB->setQuery($query);
		$users = $JLMS_DB->LoadObjectList();
		
		$i = 0;
		while ($i < count($users)) {
			if ($JLMS_CONFIG->get('use_global_groups', 1)) $users[$i]->username = $users[$i]->name . ' ('.$users[$i]->username.')';
			else $users[$i]->username = ($users[$i]->ug_name?($users[$i]->ug_name.' - '):'').$users[$i]->name . ' ('.$users[$i]->username.')';
			$i ++;
		}
		
		$lists['usrs'] = mosHTML::selectList($users, 'sel_user', 'class="inputbox" size="1" ', 'id', 'username', '' );
		JLMS_conference_html::jlms_booking_users( $course_id, $option, $lists, $pid, $rows );
	}
}

function JLMS_showUsersGlobal_ ( $course_id, $group_id  ) {
	global $my, $JLMS_DB, $Itemid, $JLMS_SESSION, $JLMS_CONFIG;
	$u_search = mosGetParam($_REQUEST, 'u_search', '');
	
	
		return $rows;
}


function jlms_booking_users_save($course_id, $option)
{
	global $JLMS_DB;
	$pid = intval(mosgetparam($_POST,"p_id",0));
	$userid = intval(mosgetparam($_POST,"sel_user",0));
	if($pid && $userid)
	{
		$query = "INSERT INTO #__lms_conference_usr(user_id,p_id) VALUES('".$userid."','".$pid."') ";
		$JLMS_DB->setQuery($query);
		$JLMS_DB->query(); 
	}
	JLMSRedirect(sefRelToAbs('index.php?option='.$option.'&amp;task=conference&amp;mode=user_access&amp;id='.$course_id.'&amp;pid='.$pid));
}

function jlms_booking_users_delete($cid, $course_id, $option)
{
	global $JLMS_DB;
	
	if (count($cid))
	{
	$pid = intval(mosgetparam($_POST,"p_id",0));
	$cids = implode( ',', $cid );
		
	$query = "DELETE FROM #__lms_conference_usr WHERE p_id = '".$pid."' AND user_id IN (".$cids.")";
		$JLMS_DB->setQuery($query);
		if (!$JLMS_DB->query()) {
			echo "<script> alert('".$JLMS_DB->getErrorMsg()."'); window.history.go(-1); </script>\n";
			exit();
		}
	}
	JLMSRedirect(sefRelToAbs('index.php?option='.$option.'&amp;task=conference&amp;mode=user_access&amp;id='.$course_id.'&amp;pid='.$pid));
}

function Daysforweeks($weeks, $month, $year)
{
	$cur_date = date("Y-m-d");
	$cur_weekday = date("w");
	$fir_day = date("w",mktime(0,0,0,$month,1,$year));
	$dates = array();
	for($i=0;$i<7;$i++)
	{
		if(in_array($i,$weeks))
		{
			for($n=0;$n<5;$n++)
			{
				$add_week = ($i<$fir_day)?($i-$fir_day+7+1):($i-$fir_day+1);
				$dataz = date("Y-m-d", mktime(0,0,0,$month,$add_week+7*$n,$year));
				//echo "-".$dataz.'<br/>';
				if(substr($dataz,5,2) == $month && $dataz>=$cur_date)
				{
					$dates[] = $dataz;
				}
			}
		}
	}
	return $dates;
}
?>