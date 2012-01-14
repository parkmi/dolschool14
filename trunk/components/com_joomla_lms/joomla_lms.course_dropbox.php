<?php
/**
* joomla_lms.course_dropbox.html.php
* (c) JoomaLMS eLearning Software http://www.joomlalms.com/
**/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
require_once(_JOOMLMS_FRONT_HOME . "/joomla_lms.course_dropbox.html.php");

	global $JLMS_CONFIG;
	$course_id = $JLMS_CONFIG->get('course_id',0);
	$pathway = array();
	$pathway[] = array('name' => _JLMS_PATHWAY_HOME, 'link' => sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid"), 'is_home' => true);
	$pathway[] = array('name' => $JLMS_CONFIG->get('course_name'), 'link' => sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid&amp;task=details_course&amp;id=$course_id"), 'is_course' => true);
	$pathway[] = array('name' => _JLMS_TOOLBAR_DROP, 'link' => sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid&amp;task=dropbox&amp;id=$course_id"));
	JLMSAppendPathWay($pathway);

JLMS_ShowHeading();
$id 	= intval( mosGetParam( $_REQUEST, 'id', 0 ) ); 
$task 	= mosGetParam( $_REQUEST, 'task', '' );


switch ($task) {
###############################		DROPBOX			##############################
	case 'dropbox':				JLMS_showCourseDropBox( $id, $option );	break;
	case 'new_dropbox':			JLMS_editDropBox( 0, $id, $option );	break;
	case 'del_dropbox':			JLMS_deleteDropBox( $id, $option );		break;
	case 'save_dropbox':		JLMS_saveDropBox( $option );			break;
	case 'change_dropbox':		JLMS_changeDropBox( $id, $option );		break;
	case 'get_frombox':			JLMS_downloadFromBox( $id, $option );	break;
	case 'cancel_dropbox':		JLMS_cancelDropBox( $option );			break;
	case 'drp_view_descr':		JLMS_viewDRPDescription( $id, $option);	break;

}

function JLMS_cancelDropBox( $option ) {
	global $Itemid;
	$course_id = intval(mosGetParam($_REQUEST, 'course_id', 0));
	JLMSRedirect(sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=dropbox&id=$course_id"));
}
function JLMS_downloadFromBox( $id, $option ) {
	global $JLMS_DB, $my, $Itemid;
	$course_id = intval(mosGetParam($_REQUEST, 'course_id', 0));
//	$usertype = JLMS_GetUserType($my->id, $course_id);
	$JLMS_ACL = & JLMSFactory::getACL();
	
	$flag = false;
	if ($course_id && $JLMS_ACL->CheckPermissions('dropbox', 'view')  && (JLMS_GetDropItemCourse($id) == $course_id) ) {
		
		$query = "SELECT file_id, drp_type, drp_name, drp_description  FROM #__lms_dropbox"
		. "\n WHERE id = '".$id."' AND course_id = '".$course_id."'"
		. "\n AND (owner_id = '".$my->id."' OR recv_id = '".$my->id."')";
		$JLMS_DB->SetQuery( $query );
		$file_data = $JLMS_DB->LoadObjectList();
		
		if (count($file_data) == 1) {
			$query = "UPDATE #__lms_dropbox"
			. "\n SET drp_mark = 0"
			. "\n WHERE id = '".$id."' AND course_id = '".$course_id."' AND recv_id = '".$my->id."'";
			$JLMS_DB->SetQuery( $query );
			$JLMS_DB->query();
		
			if($file_data[0]->drp_type == 1){
				if($file_data[0]->file_id == 0) {
					$flag = true;
				}
				else {
					JLMS_downloadFile( $file_data[0]->file_id, $option, $file_data[0]->drp_name);
				}
			} else if($file_data[0]->drp_type == 2){
				if($file_data[0]->file_id == 0){
					$flag = true;
				} else {
					$path_detect = JPATH_SITE . DS . 'components' . DS . 'com_jlms_profile' . DS . 'jlms_profile_detect.php';
					if(file_exists($path_detect)){
						include_once($path_detect);
						$detect = COMPONENT_Profile_Detect();
						if($detect){
							$TabMyFiles = new TabMyFiles($my->id);
							$TabMyFiles->DownloadFile($file_data[0]->file_id, $my->id, $my->id, $file_data[0]->drp_name, true, array(), array('view'=>1));
						}
					
					}
				}
			}
		}
	}

	if($flag) {
		JLMS_course_dropbox_html::viewCourseDropBox( $file_data[0]->drp_description, $option, $file_data[0]->drp_name );
	}
	else {
		JLMSRedirect(sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=dropbox&id=$course_id"));
	}
}
// to do: proverki na teachera, ownera (30.10 - OK)
function JLMS_editDropBox( $id, $course_id, $option ) {
	global $my, $JLMS_DB;
//	if ($my->id && $course_id && JLMS_GetUserType($my->id, $course_id) ) {
	$cid = mosGetParam( $_REQUEST, 'cid', array(0) );
	$JLMS_ACL = & JLMSFactory::getACL();
	if ($my->id && $course_id && $JLMS_ACL->CheckPermissions('dropbox', 'view') ) {
		if(count($cid) == 1){
			$tmpl_id = $cid[0];	
		} else {
			$tmpl_id = 0;	
		}
		
		$id = 0; // only new records
		$row = new mos_Joomla_LMS_DropBox( $JLMS_DB );
		if($tmpl_id == 0){
			$row->load( $id );
		} else {
			$row->load( $tmpl_id );
		}
		$lists = array();
		$users = array();
		$users1 = array();
		
		if ($JLMS_ACL->CheckPermissions('dropbox', 'send_to_teachers')) {
			$query = "SELECT a.* FROM #__users as a, #__lms_user_courses as c"
			. "\n WHERE a.id = c.user_id AND c.course_id = '".$course_id."' AND a.id <> '".$my->id."'"
			. "\n ORDER BY a.username";
			$JLMS_DB->SetQuery( $query );
			$users1 = $JLMS_DB->LoadObjectList();
			$i = 0;
			while ($i < count($users1)) {
				$users1[$i]->username = _JLMS_ROLE_TEACHER . ' - '.$users1[$i]->name . ' ('.$users1[$i]->username.')';
				$i ++;
			}
		}
		if ($JLMS_ACL->CheckPermissions('dropbox', 'send_to_learners')) {
			$users = JLMS_getCourseStudentsList($course_id);
			
			$tmp = array();
			for($i=0;$i<count($users);$i++){
				if($my->id != $users[$i]->id){
					$tmp[] = $users[$i];
				}
			}
			if(count($tmp)){
				$users = array();
				$users = $tmp;
			}
		}
		$users = array_merge($users1, $users);
		/*
		Old part (Max)
		*/
		/*else {
			$query = "SELECT a.* FROM #__users as a, #__lms_user_courses as c"
			. "\n WHERE a.id = c.user_id AND c.course_id = '".$course_id."'"
			. "\n ORDER BY a.username";
			$JLMS_DB->SetQuery( $query );
			$users = $JLMS_DB->LoadObjectList();
			$i = 0;
			while ($i < count($users)) {
				$users[$i]->username = $users[$i]->username . ' ('.$users[$i]->name.')';
				$i ++;
			}
		}*/
		$recv_id = $row->recv_id;
		if($my->id == $row->recv_id){
			$recv_id = $row->owner_id;	
		} else 
		if(isset($users[0]) && count($users) == 1){ //fix one user (ticket  [QDHZ-1096])
			$recv_id = $users[0]->id;
		}
		$lists['course_users'] = mosHTML::selectList($users, 'recv_id[]', 'class="inputbox" style=\'width:340px\' size="7" multiple', 'id', 'username', $recv_id );
		JLMS_course_dropbox_html::editDropBox( $row, $lists, $option, $course_id );
	} else {
		JLMSRedirect( sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=dropbox&id=$course_id") );
	}
}
//23.11.2006 - todo: pri udalenii files proveryat' moget oni eshe ispol'zuyutsa gde-nit'
function JLMS_deleteDropBox( $course_id, $option ) {
	global $my, $JLMS_DB, $Itemid;
	$usertype = JLMS_GetUserType($my->id, $course_id);
	if ($course_id && ($usertype == 1)) {
		$cid = mosGetParam( $_REQUEST, 'cid', array(0) );
		if (is_array( $cid ) && count( $cid ) > 0) {
			$cids = implode( ',', $cid );
			$query = "SELECT distinct file_id FROM #__lms_dropbox WHERE id IN ($cids) AND course_id = '".$course_id."'";
			$JLMS_DB->SetQuery( $query );
			$files = $JLMS_DB->LoadResultArray();
			if (count($files)) {
				$query = "DELETE FROM #__lms_dropbox WHERE id IN ($cids) AND course_id = '".$course_id."'";
				$JLMS_DB->SetQuery( $query );
				$JLMS_DB->query();
				$files_del = JLMS_checkFiles( $course_id, $files );
				if (count($files_del)) {
					JLMS_deleteFiles($files_del);
				}
			}
		}
	} elseif ($course_id && ($usertype == 2)) {
		$cid = mosGetParam( $_REQUEST, 'cid', array(0) );
		if (is_array( $cid ) && count( $cid ) > 0) {
			$cids = implode( ',', $cid );
			$query = "SELECT distinct file_id FROM #__lms_dropbox WHERE id IN ($cids) AND course_id = '".$course_id."' AND owner_id = '".$my->id."'";
			$JLMS_DB->SetQuery( $query );
			$files = $JLMS_DB->LoadResultArray();
			if (count($files)) {
				$query = "DELETE FROM #__lms_dropbox WHERE id IN ($cids) AND course_id = '".$course_id."' AND owner_id = '".$my->id."'";
				$JLMS_DB->SetQuery( $query );
				$JLMS_DB->query();
				$files_del = JLMS_checkFiles( $course_id, $files );
				if (count($files_del)) {
					JLMS_deleteFiles($files_del);
				}
			}
		}
	}
	JLMSRedirect( sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=dropbox&id=$course_id") );
}
// 'drp_mark' - sostoyanie faila ( 0 - unread, 1- read )
// 'drp_corrected' - 1 - esli teacher uplodit 'corrected' version of file
// todo: function tol'ko dlya teachera (30.10 - NO! dlya studenta toge)
// todo: proverki shto receiver tot kto nado ..... (30.10 - OK)
// (30.10)
// TODO: sdelat' normal'nyi 'drp_corrected' (30.10 - vrode uge norm. TEST IT!)
/*
$cid = mosGetParam( $_POST, 'cid', array(0) );
				if (!is_array( $cid )) { $cid = array(0); }
*/
// (30.10) TODO:
// recv_id - teper' array(). peredelat' vse dlya array().
function JLMS_saveDropBox( $option ) {
	$JLMS_CONFIG = & JLMSFactory::getConfig();
	$Itemid = $JLMS_CONFIG->get('Itemid');
	$_JLMS_PLUGINS = & JLMSFactory::getPlugins();
	$db = & JFactory::getDbo();
	$user = JLMSFactory::getUser();

	$course_id = intval(mosGetParam($_REQUEST, 'course_id', 0));
	
//	if ($user->get('id') && $course_id && JLMS_GetUserType($user->get('id'), $course_id) ) {
	$JLMS_ACL = & JLMSFactory::getACL();
	if ($user->get('id') && $course_id && $JLMS_ACL->CheckPermissions('dropbox', 'view') ) {
		
		$recv_id = intval(mosGetParam($_REQUEST, 'recv_id', 0));
		$recv_id = mosGetParam( $_POST, 'recv_id', array(0) );
		if (!is_array( $recv_id )) { $recv_id = array(0); }
		
		if((isset($recv_id[0]) && !$recv_id[0]) || !count($recv_id)){ //fix one user (ticket  [QDHZ-1096])
			$msg = _JLMS_DROP_ERROR_NO_SEND_TO;
			JLMSRedirect( sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=dropbox&id=$course_id"), $msg );
		}
		
		//convert array of receiver's to numeric values
		$i = 0;
		while ($i < count($recv_id)) {
			$recv_id[$i] = intval($recv_id[$i]);
			$i ++;
		}
		$recv_ids = implode(',',$recv_id);
		$do_continue = false;
		
		/*
		New permissions (Max)
		*/
		$query = "SELECT count(user_id) FROM #__lms_user_courses"
		. "\n WHERE course_id = '".$course_id."' AND user_id IN ( $recv_ids )";
		$db->setQuery($query);
		$count_users = $db->LoadResult();
		
		if ($JLMS_ACL->CheckPermissions('dropbox', 'send_to_teachers')) {
			if ($count_users) {
				$do_continue = true;
			} else {
				$query = "SELECT count(c.user_id) FROM #__lms_users_in_groups as c"
				. "\n WHERE c.course_id = '".$course_id."' AND c.user_id IN ( $recv_ids )";
				$db->setQuery($query);
				$count_users = $db->LoadResult();
				if ($count_users) {
					$do_continue = true;
				}
			}
		}
		if ($JLMS_ACL->CheckPermissions('dropbox', 'send_to_learners')) {
			if ($count_users) {
				$do_continue = true;
			}	
		}
		
		/*
		Old part
		*/
		/*
		if (JLMS_GetUserType($user->get('id'), $course_id) == 1) {
			$query = "SELECT count(user_id) FROM #__lms_user_courses"
			. "\n WHERE course_id = '".$course_id."' AND (role_id = 1 OR role_id = 4) AND user_id IN ( $recv_ids )";
			$db->setQuery($query);
			$count_users = $db->LoadResult();
			
			if ($count_users) {
				$do_continue = true;
			} else {
				$query = "SELECT count(c.user_id) FROM #__lms_users_in_groups as c"
				. "\n WHERE c.course_id = '".$course_id."' AND c.user_id IN ( $recv_ids )";
				$db->setQuery($query);
				$count_users = $db->LoadResult();
				if ($count_users) {
					$do_continue = true;
				}
			}
		} elseif (JLMS_GetUserType($user->get('id'), $course_id) == 2) {
			$query = "SELECT count(user_id) FROM #__lms_user_courses"
			. "\n WHERE course_id = '".$course_id."' AND (role_id = 1 OR role_id = 4) AND user_id IN ( $recv_ids )";
			$db->setQuery($query);
			$count_users = $db->LoadResult();
			if ($count_users) {
				$do_continue = true;
			}
		}
		*/
		
		// (TIPS)
		// sender: teacher - RECEIVER must be teacher of this course or student of this course
		// sender: student - RECEIVER must be teacher of this course
		$flag = false;
		if ($do_continue) {
			
			if($_FILES['userfile']['name']=='') {
				$file_id = 0;			
				$flag = true;
			}	
			else {		
				$file_id = JLMS_uploadFile( $course_id );
				if($file_id) {
					$flag = true;
				}
			}
			$_POST['drp_type'] = 1;
			if(intval(mosGetParam($_REQUEST, 'file_id', 0))){
				$file_id = intval(mosGetParam($_REQUEST, 'file_id', 0));
				$_POST['drp_type'] = 2;
			}
			
			if ($flag) {
				$row = new mos_Joomla_LMS_DropBox( $db );
				if (!$row->bind( $_POST )) {
					echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
					exit();
				}
				$row->file_id = $file_id;

				$row->owner_id = $user->get('id');
				$row->drp_mark = 1;
				/*$query = "SELECT file_name FROM #__lms_files WHERE id = '".$file_id."'";
				$db->SetQuery( $query );

				$row->drp_name = $db->LoadResult();*/
				//$row->drp_name = strval(mosGetParam($_FILES['userfile'], 'name', 'dropbox_file'));
				if($file_id > 0) {
					if($row->drp_type == 1){
						$drp_name = isset($_FILES['userfile']['name'])?strval($_FILES['userfile']['name']):'dropbox_file';
					} else if($row->drp_type == 2){
						$drp_name = strval(mosGetParam($_REQUEST, 'dropbox_name', 'dropbox_file_('. time().')'));
					}
				}
				else {
					$drp_name = mosGetParam($_REQUEST, 'dropbox_name');
				}
				$drp_name = (get_magic_quotes_gpc()) ? stripslashes( $drp_name ) : $drp_name; 
				$row->drp_name	= ampReplace(strip_tags($drp_name));

				$row->drp_description = strval(JLMS_getParam_LowFilter($_POST, 'drp_description', ''));
				//$row->drp_description = JLMS_ProcessText_LowFilter($row->drp_description);

//				if (JLMS_GetUserType($user->get('id'), $course_id ) == 1) {
				if ($JLMS_ACL->CheckPermissions('dropbox', 'mark_as_corrected')) {
					$drp_corr = intval(mosGetParam($_REQUEST, 'drp_corrected', 0));
					if ($drp_corr != 1) { $drp_corr = 0; }
					$row->drp_corrected = $drp_corr;
				} else {
					$row->drp_corrected = 0;
				}
				$row->drp_time = date( 'Y-m-d H:i:s' );
				
				
				//Replace old function JLMS_GetUserType //tmp
				$users_teachers = array();
				$users_learners = array();
				if ($JLMS_ACL->CheckPermissions('dropbox', 'send_to_teachers')) {
					$query = "SELECT a.* FROM #__users as a, #__lms_user_courses as c"
					. "\n WHERE a.id = c.user_id AND c.course_id = '".$course_id."' AND a.id <> '".$user->id."'"
					. "\n ORDER BY a.username";
					$db->SetQuery( $query );
					$users_teachers = $db->LoadObjectList();
					$i = 0;
					while ($i < count($users_teachers)) {
						$users_teachers[$i]->username = _JLMS_ROLE_TEACHER . ' - '.$users_teachers[$i]->name . ' ('.$users_teachers[$i]->username.')';
						$i ++;
					}
				}
				if ($JLMS_ACL->CheckPermissions('dropbox', 'send_to_learners')) {
					$users_learners = JLMS_getCourseStudentsList($course_id);
				}
				
				$tmp = array();
				foreach($users_teachers as $n=>$ut){
					$tmp[$n] = $ut->id;
				}
				if(count($tmp)){
					$users_teachers = $tmp;
				}
				
				$tmp = array();
				foreach($users_learners as $n=>$ul){
					$tmp[$n] = $ul->id;
				}
				if(count($tmp)){
					$users_learners = $tmp;
				}
				//Replace old function JLMS_GetUserType //tmp
				
				foreach ($recv_id as $recv) {
					$check_recv = false;

					//Replace old function JLMS_GetUserType //tmp
					if ($JLMS_ACL->CheckPermissions('dropbox', 'send_to_teachers') && in_array($recv, $users_teachers) ){
						$check_recv = true;
					}
					if ($JLMS_ACL->CheckPermissions('dropbox', 'send_to_learners') && in_array($recv, $users_learners) ){
						$check_recv = true;
					}
					//Replace old function JLMS_GetUserType //tmp

//					if ($recv && ($recv != $user->get('id')) && ((JLMS_GetUserType($user->get('id'), $course_id) == 1 && JLMS_GetUserType($recv, $course_id)) || ((JLMS_GetUserType($user->get('id'), $course_id) == 2) && (JLMS_GetUserType($recv, $course_id, true) == 1))) || ((JLMS_GetUserType($user->get('id'), $course_id) == 2) && (JLMS_GetUserType($recv, $course_id, true) == 2)) ) { //old
					if ($recv && ($recv != $user->get('id')) && $check_recv) {
						$row->id = 0;
						$row->recv_id = $recv;
						if (!$row->check()) {
							echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
							exit();
						}
						if (!$row->store()) {
							echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
							exit();
						}

						//*** send email notification
						$e_course = new stdClass();
						$e_course->course_alias = '';
						$e_course->course_name = '';			

						$query = "SELECT course_name, name_alias FROM #__lms_courses WHERE id = '".$course_id."'";
						$db->setQuery( $query );
						$e_course = $db->loadObject();

						$e_user = new stdClass();
						$e_user->name = '';
						$e_user->email = '';
						$e_user->username = '';

						$query = "SELECT email, name, username FROM #__users WHERE id = '".$recv."'";
						$db->setQuery( $query );
						$e_user = $db->loadObject();

						$e_params['user_id'] = $recv;
						$e_params['course_id'] = $course_id;					
						$e_params['markers']['{email}'] = $e_user->email;	
						$e_params['markers']['{name}'] = $e_user->name;										
						$e_params['markers']['{username}'] = $e_user->username;
						$e_params['markers']['{coursename}'] = $e_course->course_name;//( $e_course->course_alias )?$e_course->course_alias:$e_course->course_name;
						$e_params['markers']['{filename}'] = $row->drp_name;

						$e_params['markers']['{courselink}'] = JLMSEmailRoute("index.php?option=com_joomla_lms&Itemid=$Itemid&task=details_course&id=$course_id");;
						$e_params['markers_nohtml']['{courselink}'] = $e_params['markers']['{courselink}'];
						$e_params['markers']['{courselink}'] = '<a href="'.$e_params['markers']['{courselink}'].'">'.$e_params['markers']['{courselink}'].'</a>';

						$e_params['markers']['{lmslink}'] = JLMSEmailRoute("index.php?option=com_joomla_lms&Itemid=$Itemid");
						$e_params['markers_nohtml']['{lmslink}'] = $e_params['markers']['{lmslink}'];
						$e_params['markers']['{lmslink}'] = '<a href="'.$e_params['markers']['{lmslink}'].'">'.$e_params['markers']['{lmslink}'].'</a>';						

						$e_params['action_name'] = 'OnNewDropboxFile';

						$_JLMS_PLUGINS->loadBotGroup('emails');
						$plugin_result_array = $_JLMS_PLUGINS->trigger('OnNewDropboxFile', array (& $e_params));
						//*** end of emails
					}
				}
				JLMSRedirect( sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=dropbox&id=$course_id") );
			} else {
				mosErrorAlert("Upload of ".$userfile_name." failed");
			}
		} else {
			JLMSRedirect( sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=dropbox&id=$course_id") );
		}
	} else {
		JLMSRedirect( sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=dropbox&id=$course_id") );
	}
}
function JLMS_changeDropBox( $course_id, $option ) {
	global $JLMS_DB, $my, $Itemid;
//	if ( $course_id && JLMS_GetUserType($my->id, $course_id) ) {
	$JLMS_ACL = & JLMSFactory::getACL();
	if ( $course_id && $JLMS_ACL->CheckPermissions('dropbox', 'mark_as_corrected') ) {
		$state = intval(mosGetParam($_REQUEST, 'state', 0));
		if ($state != 1) { $state = 0; }
		$state = 0; // 23.11.2006 - Bjarne request
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
			$action = $state ? 'Read' : 'Unread';
			echo "<script> alert('Select an item to $action'); window.history.go(-1);</script>\n";
			exit();
		}
		$cids = implode( ',', $cid );
		$query = "UPDATE #__lms_dropbox"
		. "\n SET drp_mark = $state"
		. "\n WHERE id IN ( $cids ) AND course_id = $course_id AND recv_id = '".$my->id."'"
		;
		$JLMS_DB->setQuery( $query );
		if (!$JLMS_DB->query()) {
			echo "<script> alert('".$JLMS_DB->getErrorMsg()."'); window.history.go(-1); </script>\n";
			exit();
		}
	}
	JLMSRedirect( sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=dropbox&id=$course_id") );
}
function JLMS_showCourseDropBox( $course_id, $option ) {
	global $my, $JLMS_DB, $Itemid;
	$JLMS_ACL = & JLMSFactory::getACL();
	if ( $course_id && $JLMS_ACL->CheckPermissions('dropbox', 'view') ) {
//	if ( $course_id && JLMS_GetUserType($my->id, $course_id) ) {
		$lists = array();
		$query = "SELECT count(*) FROM #__lms_dropbox"
		. "\n WHERE course_id = '".$course_id."'"
		. "\n AND recv_id = '".$my->id."'";
		$JLMS_DB->SetQuery( $query );
		$lists['dropbox_in_total'] = intval($JLMS_DB->LoadResult());
		
		$query = "SELECT count(*) FROM #__lms_dropbox"
		. "\n WHERE course_id = '".$course_id."'"
		. "\n AND recv_id = '".$my->id."' AND drp_mark = 1";
		$JLMS_DB->SetQuery( $query );
		$lists['dropbox_in_new'] = intval($JLMS_DB->LoadResult());
		
		$query = "SELECT count(*) FROM #__lms_dropbox"
		. "\n WHERE course_id = '".$course_id."'"
		. "\n AND owner_id = '".$my->id."'";
		$JLMS_DB->SetQuery( $query );
		$lists['dropbox_out_total'] = intval($JLMS_DB->LoadResult());
		
		$query = "SELECT a.*"
		. "\n, b.name as owner_username"
		. "\n, c.name as recv_username"
		. "\n FROM #__lms_dropbox as a"
		. "\n, #__users as b"
		. "\n, #__users as c"
		. "\n WHERE a.course_id = '".$course_id."'"
		. "\n AND a.owner_id = b.id AND a.recv_id = c.id AND (a.owner_id = '".$my->id."' OR a.recv_id = '".$my->id."')"
		. "\n ORDER BY a.drp_time DESC";
		$JLMS_DB->SetQuery( $query );
		$dropbox = $JLMS_DB->LoadObjectList();

		$lms_titles_cache = & JLMSFactory::getTitles();
		$lms_titles_cache->setArray('dropbox', $dropbox, 'id', 'drp_name');

		JLMS_course_dropbox_html::showCourseDropBox( $course_id, $option, $dropbox, $lists );
	} elseif ($course_id) {
		JLMSRedirect( sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=details_course&id=$course_id") );
	} else {
		JLMSRedirect( sefRelToAbs("index.php?option=$option&Itemid=$Itemid") );
	}
}
function JLMS_viewDRPDescription( $id, $option ) {
	global $my, $JLMS_DB, $Itemid;
	$course_id = intval(mosGetParam($_REQUEST, 'course_id', 0));
//	if ($my->id && $course_id && JLMS_GetUserType($my->id, $course_id) ) {
	$JLMS_ACL = & JLMSFactory::getACL();
	if ($my->id && $course_id && $JLMS_ACL->CheckPermissions('dropbox', 'view') ) {
		$query = "SELECT a.*, b.name as owner_username, c.name as recv_username FROM #__lms_dropbox as a, #__users as b, #__users as c"
		. "\n WHERE a.id = '".$id."'"
		. "\n AND a.course_id = '".$course_id."'"
		. "\n AND a.owner_id = b.id AND a.recv_id = c.id AND (a.owner_id = '".$my->id."' OR a.recv_id = '".$my->id."')"
		;
		$JLMS_DB->SetQuery( $query );
		$dropbox = $JLMS_DB->LoadObjectList();
		if (count($dropbox)) {
			$drop = $dropbox[0];
			$lists = array();
			JLMS_course_dropbox_html::viewDRPDescription( $course_id, $option, $drop, $lists );
		} else {
			JLMSRedirect( sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=dropbox&id=$course_id") );
		}
	} elseif ($course_id) {
		JLMSRedirect( sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=details_course&id=$course_id") );
	} else {
		JLMSRedirect( sefRelToAbs("index.php?option=$option&Itemid=$Itemid") );
	}
}
?>