<?php
/**
* admin.maintenance.php
* (c) JoomaLMS eLearning Software http://www.joomlalms.com/
**/
// no direct access
//defined( '_VALID_MOS' ) or die( 'Restricted access' );
if (!defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) { die( 'Restricted access' ); }

require_once(_JOOMLMS_ADMIN_HOME.'/files/admin.maintenance.html.php');

$task 	= strval(mosGetParam( $_REQUEST, 'task', 'lms_maintenance' ));
$page 	= strval(mosGetParam( $_REQUEST, 'page', '' ));

$id 	= intval( mosGetParam( $_REQUEST, 'id', 0 ) );
$cid 	= mosGetParam( $_POST, 'cid', mosGetParam( $_GET, 'cid', array(0) ) );

switch ($page) {
	case 'check_database':
		JLMS_check_database( $option );
	break;	
	case 'check_tables':
		JLMS_check_table_data( $option );		
	break;	
	case 'maintenance_log':
		JLMS_Maintenance_log ( $option );
	break;	
	default:
		JLMS_check_database_interface( $option );
	break;
}

function JLMS_Maintenance_log ( $option ) {
	$db = & JFactory::getDbo();
	
			$query = "SELECT * FROM `#__lms_maintenance_log`";
			$db->setQuery( $query );
			$rows = $db->loadObjectList();
			
			$str = '';
			//$str .= '"Log time.","Log action","Log result"'."\n";

			for($i=0, $n = count($rows); $i < $n; $i++) {
				$str .= $rows[$i]->ID.'","'.$rows[$i]->log_time.'","';
				$str .= $rows[$i]->log_action.'","';
				$str .= $rows[$i]->log_result.'"';
				$str .= "\"\n";
			}

			//echo $str; die;
			
			$UserBrowser = '';
			if (preg_match('/MSIE ([0-9].[0-9]{1,2})/', $_SERVER['HTTP_USER_AGENT'])) $UserBrowser = "IE";
			header("Content-Type:application/txt");
			header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
			if ($UserBrowser == 'IE') {
				header("Content-Disposition: inline; filename=jlms_maintenance_log.txt ");
				header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
				header('Pragma: public');
			} else {
				header("Content-Disposition: inline; filename=jlms_maintenance_log.txt ");
				header('Pragma: no-cache');
			}
			echo $str;
			exit();		
}

function JLMS_check_table_data( $option ) {
	global $JLMS_CONFIG;
	$db = & JFactory::getDbo();
	$out = array();
	$y= 0;
	
	$out[]['#__lms_config data'] = JLMS_check_table_is_empty('#__lms_config', _JLMS_MAIN_CFG_IS_EMPTY );
	$out[]['#__lms_course_cats data'] = JLMS_check_table_is_empty('#__lms_course_cats', _JLMS_MAIN_CAT_IS_EMPTY );
	$out[]['#__lms_file_types data'] = JLMS_check_table_is_empty('#__lms_file_types', _JLMS_MAIN_TYPES_IS_EMPTY );
	
	if($out[count($out)-1]['#__lms_file_types data'][1]) {
			// insert 'file types'
		$query = "INSERT INTO `#__lms_file_types` VALUES ('3gp'), ('avi'), ('bmp'), ('csv'), ('doc'), ('docx'), ('flv'), ('gif'), ('htm'), ('html'), ('jpe'), ('jpeg'), ('jpg'), ('mov'), ('mp3'), ('mp4'), ('mpe'), ('mpeg'), ('mpg'), ('pdf'), ('png'), ('ppt'), ('pptx'), ('qt'), ('ra'), ('ram'), ('rar'), ('rm'), ('rtf'), ('swf'), ('swfl'), ('tif'), ('tiff'), ('txt'), ('wma'), ('wmv'), ('xls'), ('xlsx'), ('xml'), ('zip')";
		$db->SetQuery($query);
		$db->query();
		$error = $db->getErrorMsg();
		
		if($error) {
			$out[count($out)-1]['#__lms_file_types data'][] = '';
			$out[count($out)-1]['#__lms_file_types data'][] = $error;
			
			$status = 'ERROR: '.$error;
			JLMS_insert_log( '#__lms_file_types data', $status );
		}
		else {
			$out[count($out)-1]['#__lms_file_types data'][] = _JLMS_MAIN_TYPES_LIST_GENER;
			$out[count($out)-1]['#__lms_file_types data'][] = '';
			
			$status = $query;
			JLMS_insert_log( '#__lms_file_types data', $status );
		}
	}

	$out[]['#__lms_gradebook_cats data'] = JLMS_check_table_is_empty('#__lms_gradebook_cats', _JLMS_MAIN_GRADE_LIST_IS_EMPTY );
	
	if($out[count($out)-1]['#__lms_gradebook_cats data'][1]) {
			$query = "INSERT INTO `#__lms_gradebook_cats` VALUES (1, 'Assignment'),"
			. "\n (2, 'Attendance'),"
			. "\n (3, 'Essay'),"
			. "\n (4, 'Exam'),"
			. "\n (5, 'Extra Credit'),"
			. "\n (6, 'Final Exam'),"
			. "\n (7, 'Group Project'),"
			. "\n (8, 'Homework'),"
			. "\n (9, 'Journal'),"
			. "\n (10, 'Lab'),"
			. "\n (11, 'Midterm Exam'),"
			. "\n (12, 'Other'),"
			. "\n (13, 'Paper'),"
			. "\n (14, 'Presentation'),"
			. "\n (15, 'Problem Set'),"
			. "\n (16, 'Quiz'),"
			. "\n (17, 'Survey')";		
		$db->SetQuery($query);
		$db->query();
		$error = $db->getErrorMsg();
		
		if($error) {
			$out[count($out)-1]['#__lms_gradebook_cats data'][] = '';
			$out[count($out)-1]['#__lms_gradebook_cats data'][] = $error;
			
			$status = 'ERROR: '.$error;
			JLMS_insert_log( '#__lms_gradebook_cats data', $status );
		}
		else {
			$out[count($out)-1]['#__lms_gradebook_cats data'][] = _JLMS_MAIN_GRADE_LIST_GENER;
			$out[count($out)-1]['#__lms_gradebook_cats data'][] = '';
			
			$status = $query;
			JLMS_insert_log( '#__lms_gradebook_cats data', $status );
		}
	}	
	
	$out[]['#__lms_languages data'] = JLMS_check_table_is_empty('#__lms_languages', _JLMS_MAIN_LANG_LIST_IS_EMPTY );
	
	if($out[count($out)-1]['#__lms_languages data'][1]) {
		$query = "INSERT INTO `#__lms_languages` VALUES (1, 'english', 1),"
		. "\n (2, 'danish', 1),"
		. "\n (3, 'german', 1),"
		. "\n (4, 'spanish', 1),"
		. "\n (5, 'french', 1),"
		. "\n (6, 'norwegian', 1),"
		. "\n (7, 'italian', 1),"
		. "\n (8, 'brazilian', 1),"
		. "\n (9, 'bulgarian', 0),"
		. "\n (10, 'chinese', 0),"
		. "\n (11, 'czech', 0),"
		. "\n (12, 'japanese', 0),"
		. "\n (13, 'russian', 0),"
		. "\n (14, 'dutch', 1)";
		$db->setQuery($query);
		$db->query();
		$error = $db->getErrorMsg();
		
		if($error) {
			$out[count($out)-1]['#__lms_languages data'][] = '';
			$out[count($out)-1]['#__lms_languages data'][] = $error;
			
			$status = 'ERROR: '.$error;
			JLMS_insert_log( '#__lms_languages data', $status );
		}
		else {
			$out[count($out)-1]['#__lms_languages data'][] = _JLMS_MAIN_LANG_LIST_GENER;
			$out[count($out)-1]['#__lms_languages data'][] = '';
			
			$status = $query;
			JLMS_insert_log( '#__lms_languages data', $status );
		}
	}
	
	$out[]['#__lms_menu data'] = JLMS_check_table_is_empty('#__lms_menu', _JLMS_MAIN_MENU_LIST_IS_EMPTY );

	if($out[count($out)-1]['#__lms_menu data'][1]) {
			// insert 'file types'
		$error = JLMS_insert_menu_items_if_no();
		
		if($error) {
			$out[count($out)-1]['#__lms_menu data'][] = '';
			$out[count($out)-1]['#__lms_menu data'][] = $error;
			
			$status = 'ERROR: '.$error;
			JLMS_insert_log( '#__lms_menu data', $status );
		}
		else {
			$out[count($out)-1]['#__lms_menu data'][] = _JLMS_MAIN_MENU_LIST_GENER;
			$out[count($out)-1]['#__lms_menu data'][] = '';
			
			$status = $query;
			JLMS_insert_log( '#__lms_menu data', $status );
		}
	}
	
	if($JLMS_CONFIG->get('global_quest_pool', '')) {
		$query = "SELECT id FROM `#__lms_menu` WHERE lang_var = '_JLMS_TOOLBAR_GQP_PARENT'";
		$db->setQuery( $query );
		$gqp = $db->loadResult();
		
		if(!$gqp) {
			
			$query = "SELECT ordering FROM `#__lms_menu` WHERE lang_var = '_JLMS_TOOLBAR_SUBSCRIPTIONS' AND user_access = 1";
			$db->setQuery( $query );
			$ordering = $db->loadResult();
			
			$query = "INSERT INTO `#__lms_menu` VALUES ('', '_JLMS_TOOLBAR_GQP_PARENT', 'tlb_pool.png', 'quizzes', ".$ordering.", 1, 0, 1)";
			$db->SetQuery($query);
			$db->query();
			$error = $db->getErrorMsg();
			
			$query = "SELECT ordering FROM `#__lms_menu` WHERE lang_var = '_JLMS_TOOLBAR_SUBSCRIPTIONS' AND user_access = 0";
			$db->setQuery( $query );
			$ordering = $db->loadResult();

			$query = "INSERT INTO `#__lms_menu` VALUES ('', '_JLMS_TOOLBAR_GQP_PARENT', 'tlb_pool.png', 'quizzes', ".$ordering.", 1, 0, 0);";
			$db->SetQuery($query);
			$db->query();
			$error .= $db->getErrorMsg();
			
			if($error) {
				$out[count($out)-1]['#__lms_menu data'][] = '';
				$out[count($out)-1]['#__lms_menu data'][] = $error;
				
				$status = 'ERROR: '.$error;
				JLMS_insert_log( '#__lms_menu data', $status );
			}
			else {
				$out[count($out)-1]['#__lms_menu data'][] = _JLMS_MAIN_MENU_LIST_GENER;
				$out[count($out)-1]['#__lms_menu data'][] = '';
				
				$status = $query;
				JLMS_insert_log( '#__lms_menu data', $status );
			}
		}
	}
	
	if($JLMS_CONFIG->get('flms_integration', '')) {
		$query = "SELECT id FROM `#__lms_menu` WHERE lang_var = '_JLMS_TOOLBAR_VIEW_ALL_NOTICES'";
		$db->setQuery( $query );
		$flms = $db->loadResult();
		
		if(!$flms) {
			
			$query = "SELECT ordering FROM `#__lms_menu` WHERE lang_var = '_JLMS_TOOLBAR_SUBSCRIPTIONS' AND user_access = 2";
			$db->setQuery( $query );
			$ordering = $db->loadResult();
			
			$query = "INSERT INTO `#__lms_menu` VALUES ('', '_JLMS_TOOLBAR_VIEW_ALL_NOTICES', 'btn_notice.png', 'view_all_notices', ".$ordering.", 1, 0, 2)";
			$db->SetQuery($query);
			$db->query();
			$error = $db->getErrorMsg();
						
			$query = "SELECT ordering FROM `#__lms_menu` WHERE lang_var = '_JLMS_TOOLBAR_SUBSCRIPTIONS' AND user_access = 1";
			$db->setQuery( $query );
			$ordering = $db->loadResult();
			
			$query = "INSERT INTO `#__lms_menu` VALUES ('', '_JLMS_TOOLBAR_VIEW_ALL_NOTICES', 'btn_notice.png', 'view_all_notices', ".$ordering.", 1, 0, 1)";
			$db->SetQuery($query);
			$db->query();
			$error .= $db->getErrorMsg();
			
			$query = "SELECT ordering FROM `#__lms_menu` WHERE lang_var = '_JLMS_TOOLBAR_SUBSCRIPTIONS' AND user_access = 0";
			$db->setQuery( $query );
			$ordering = $db->loadResult();
			
			$query = "INSERT INTO `#__lms_menu` VALUES ('', '_JLMS_TOOLBAR_VIEW_ALL_NOTICES', 'btn_notice.png', 'view_all_notices', ".$ordering.", 1, 0, 0)";
			$db->SetQuery($query);
			$db->query();
			$error .= $db->getErrorMsg();

			
			if($error) {
				$out[count($out)-1]['#__lms_menu data'][] = '';
				$out[count($out)-1]['#__lms_menu data'][] = $error;
				
				$status = 'ERROR: '.$error;
				JLMS_insert_log( '#__lms_menu data', $status );
			}
			else {
				$out[count($out)-1]['#__lms_menu data'][] = _JLMS_MAIN_MENU_LIST_GENER;
				$out[count($out)-1]['#__lms_menu data'][] = '';
				
				$status = $query;
				JLMS_insert_log( '#__lms_menu data', $status );
			}
		}
	}
	
	
	
	
	$out[]['#__lms_plugins data'] = JLMS_check_table_is_empty('#__lms_plugins', _JLMS_MAIN_PLUGINS_LIST_IS_EMPTY );
	
	$out[]['#__lms_quiz_t_qtypes data'] = JLMS_check_table_is_empty('#__lms_quiz_t_qtypes', _JLMS_MAIN_QUEST_TS_LIST_IS_EMPTY );
	
	if($out[count($out)-1]['#__lms_quiz_t_qtypes data'][1]) {
		$query = "INSERT INTO `#__lms_quiz_t_qtypes` VALUES (1, 'Multiple Choice'),"
			. " \n (2, 'Multiple Response'),"
			. " \n (3, 'True/False'),"
			. " \n (4, 'Matching Drag and Drop'),"
			. " \n (5, 'Matching Drop-Down'),"
			. " \n (6, 'Fill in the blank'),"
			. " \n (7, 'Hotspot'),"
			. " \n (8, 'Surveys'),"
			. " \n (9, 'Likert Scale'),"
			. " \n (10, 'Boilerplate'),"
			. " \n (11, 'Matching Drag and Drop Images'),"
			. " \n (12, 'Multiple Images Choice'),"
			. " \n (13, 'Multiple Images Response')";
		$db->SetQuery($query);
		$db->query();
		$error = $db->getErrorMsg();
		
		if($error) {
			$out[count($out)-1]['#__lms_quiz_t_qtypes data'][] = '';
			$out[count($out)-1]['#__lms_quiz_t_qtypes data'][] = $error;
			
			$status = 'ERROR: '.$error;
			JLMS_insert_log( '#__lms_quiz_t_qtypes data', $status );
		}
		else {
			$out[count($out)-1]['#__lms_quiz_t_qtypes data'][] = _JLMS_MAIN_QUEST_TS_LIST_GENER;
			$out[count($out)-1]['#__lms_quiz_t_qtypes data'][] = '';
			
			$status = $query;
			JLMS_insert_log( '#__lms_quiz_t_qtypes data', $status );
		}
	}
	
	$out[]['#__lms_usertypes data'] = JLMS_check_table_is_empty('#__lms_usertypes', _JLMS_MAIN_ROLES_LIST_IS_EMPTY );
	
	if($out[count($out)-1]['#__lms_usertypes data'][1]) {
		$query = "INSERT INTO `#__lms_usertypes` (id, roletype_id, lms_usertype, default_role) VALUES (1, 2, 'Teacher', 1),"
			. "\n (2, 1, 'Student', 1),"
			. "\n (3, 0, 'lms_admin', 0),"
			. "\n (4, 5, 'Assistant', 0),"
			. "\n (5, 4, 'LMS administrator', 0),"
			. "\n (6, 3, 'Parent/CEO', 0)";
		$db->SetQuery($query);
		$db->query();
		$error = $db->getErrorMsg();
		
		if($error) {
			$out[count($out)-1]['#__lms_usertypes data'][] = '';
			$out[count($out)-1]['#__lms_usertypes data'][] = $error;
			
			$status = 'ERROR: '.$error;
			JLMS_insert_log( '#__lms_usertypes data', $status );
		}
		else {
			$out[count($out)-1]['#__lms_usertypes data'][] = _JLMS_MAIN_ROLES_LIST_GENER;
			$out[count($out)-1]['#__lms_usertypes data'][] = '';
			
			$status = $query;
			JLMS_insert_log( '#__lms_usertypes data', $status );
		}
	}	
	
	$query = "SELECT id FROM `#__lms_usertypes` WHERE roletype_id = 2 AND default_role = 1";
	$db->setQuery( $query );
	$roletype_id_2_2 = $db->loadResult();
	
	if(!$roletype_id_2_2) {
		
		$out[count($out)-1]['#__lms_usertypes data'][] = '';
		$out[count($out)-1]['#__lms_usertypes data'][] = _JLMS_MAIN_T_ROLE_NOT_CFG;
		
		$status = 'ERROR: Default teacher role is not configured';
		JLMS_insert_log( '#__lms_usertypes data', $status );
		
		$query = "SELECT id FROM `#__lms_usertypes` WHERE roletype_id = 2";
		$db->setQuery( $query );
		$roletype_id_2_1 = $db->loadResult();
		
		if($roletype_id_2_1) {
			$query = "UPDATE `#__lms_usertypes` SET `default_role` = 1 WHERE id = '".$roletype_id_2_1."'";
			$db->setQuery($query);
			$db->query();
		}
		
		$error = $db->getErrorMsg();
		if($error) {
			$out[count($out)-1]['#__lms_usertypes data'][] = '';
			$out[count($out)-1]['#__lms_usertypes data'][] = $error;

			$status = 'ERROR: '.$error;
			JLMS_insert_log( '#__lms_usertypes data', $status );
		}
		else {
			$out[count($out)-1]['#__lms_usertypes data'][] = _JLMS_MAIN_T_ROLE_CFG;
			$out[count($out)-1]['#__lms_usertypes data'][] = '';
			
			$status = $query;
			JLMS_insert_log( '#__lms_usertypes data', $status );
		}
	}
	
	$query = "SELECT id FROM `#__lms_usertypes` WHERE roletype_id = 1 AND default_role = 1";
	$db->setQuery( $query );
	$roletype_id_1_1 = $db->loadResult();
	
	if(!$roletype_id_1_1) {
		
		$out[count($out)-1]['#__lms_usertypes data'][] = '';
		$out[count($out)-1]['#__lms_usertypes data'][] = _JLMS_MAIN_L_ROLE_NOT_CFG;
		
		$status = 'ERROR: Default learner role is not configured';
		JLMS_insert_log( '#__lms_usertypes data', $status );
		
		$query = "SELECT id FROM `#__lms_usertypes` WHERE roletype_id = 1";
		$db->setQuery( $query );
		$roletype_id_1_0 = $db->loadResult();
		
		if($roletype_id_1_0) {
			$query = "UPDATE `#__lms_usertypes` SET `default_role` = 1 WHERE id = '".$roletype_id_1_0."'";
			$db->setQuery($query);
			$db->query();
		}
		
		$error = $db->getErrorMsg();
		if($error) {
			$out[count($out)-1]['#__lms_usertypes data'][] = '';
			$out[count($out)-1]['#__lms_usertypes data'][] = $error;

			$status = 'ERROR: '.$error;
			JLMS_insert_log( '#__lms_usertypes data', $status );
		}
		else {
			$out[count($out)-1]['#__lms_usertypes data'][] = _JLMS_MAIN_L_ROLE_CFG;
			$out[count($out)-1]['#__lms_usertypes data'][] = '';
			
			$status = $query;
			JLMS_insert_log( '#__lms_usertypes data', $status );
		}
	}
	
	//lms_n_scorm_lib
	$scorm_packages = array();
	
	$query = "SELECT scorm_package FROM `#__lms_n_scorm` WHERE course_id = 0 group by scorm_package";
	$db->setQuery( $query );
	$scorm_package = $db->loadResultArray();

	if(count($scorm_package)) {
		$scorm_package_ids = implode(',', $scorm_package);

		$query = "SELECT a.id FROM `#__lms_n_scorm` as a, `#__lms_learn_paths` as b WHERE a.course_id > 0 AND a.scorm_package IN (".$scorm_package_ids.") AND a.id = b.item_id";
		$db->setQuery( $query );
		$scorm_package = $db->loadResultArray();

		if(count($scorm_package)) {
			$scorm_package_ids = implode(',', $scorm_package);
			
			$query = "SELECT c.*, b.scorm_package FROM `#__lms_n_scorm` as b, `#__lms_learn_paths` as c"
			. " \n WHERE b.course_id > 0"
			. " \n AND c.item_id = b.id"
			. " \n AND b.id IN (".$scorm_package_ids.")"
			. " \n AND c.lp_type = 2"
			. " \n GROUP BY c.id"
			;

			$db->setQuery( $query );
			$scorm_packages = $db->loadObjectList();

			for($i=0;$i<count($scorm_packages);$i++) {
				unset($outer_id);
				$query = "SELECT b.id as learn_path_id, a.id as library_id FROM `#__lms_n_scorm` AS a, `#__lms_outer_documents` AS b"
					." \n WHERE a.course_id = 0 AND a.scorm_package = '".$scorm_packages[$i]->scorm_package."'"
					." \n AND b.file_id = a.id AND b.folder_flag = 3";
				$db->setQuery( $query );
				$outer_id = $db->loadObject();

				if(isset($outer_id->learn_path_id) && isset($outer_id->library_id)) {
					$scorm_packages[$i]->lib_id = $outer_id->learn_path_id;
					$scorm_packages[$i]->lib_n_scorm_id = $outer_id->library_id;
				}
			}
			
			$query = "SELECT lib_id FROM `#__lms_n_scorm_lib`";
			$db->setQuery( $query );
			$scorm_lib = $db->loadResultArray();
		}
	}

	$out[]['#__lms_n_scorm_lib data'] = array();

	$count = 0;
	for($i=0;$i<count($scorm_packages);$i++) {
		if(isset($scorm_packages[$i]->lib_id) && isset($scorm_packages[$i]->lib_n_scorm_id) && !in_array($scorm_packages[$i]->lib_n_scorm_id, $scorm_lib)) {

			$lib_id = $scorm_packages[$i]->lib_id;
			$lpath_id = $scorm_packages[$i]->id;
			$course_id = $scorm_packages[$i]->course_id;
			$lib_n_scorm_id = $scorm_packages[$i]->lib_n_scorm_id;
			$lpath_n_scorm_id = $scorm_packages[$i]->item_id;
			$scorm_package = $scorm_packages[$i]->scorm_package;

			$query = "SELECT count(*) FROM #__lms_n_scorm_lib WHERE lib_id = $lib_id AND lpath_id = $lpath_id";
			$db->setQuery($query);
			$is_rec_exists = $db->LoadResult();
			if ($is_rec_exists) {
				// record is already exists
			} else {
				$query = "INSERT INTO `#__lms_n_scorm_lib` (`lib_id`, `lpath_id`, `course_id`, `lib_n_scorm_id`, `lpath_n_scorm_id`, `scorm_package`)"
				."\n VALUES ('".$lib_id."', '".$lpath_id."', '".$course_id."', '".$lib_n_scorm_id."', '".$lpath_n_scorm_id."', '".$scorm_package."')";
				$db->setQuery($query);
				$db->query();		
				
				$error = $db->getErrorMsg();
	
				if($error) {
					$out[count($out)-1]['#__lms_n_scorm_lib data'][] = '';
					$out[count($out)-1]['#__lms_n_scorm_lib data'][] = $error;
		
					$status = 'ERROR: '.$error;
					JLMS_insert_log( '#__lms_n_scorm_lib data', $status );
				}
				else {
					/*$out[count($out)-1]['#__lms_n_scorm_lib data'][] = 'lms_n_scorm_lib has been successfully configured';
					$out[count($out)-1]['#__lms_n_scorm_lib data'][] = '';*/
					
					$status = $query;
					JLMS_insert_log( '#__lms_n_scorm_lib data', $status );
				}
				$count++;
			}
		}
	}
	
	if($count) {
		$out[count($out)-1]['#__lms_n_scorm_lib data'][] = $count.' '._JLMS_MAIN_RCS_FIXED;
		$out[count($out)-1]['#__lms_n_scorm_lib data'][] = '';
	}
	else {
		$out[count($out)-1]['#__lms_n_scorm_lib data'][] = '';
		$out[count($out)-1]['#__lms_n_scorm_lib data'][] = '';
	}
	
	
	$out[]['#__lms_courses data'] = array();
	
	$query = "SELECT count(*) FROM #__lms_course_level";
	$db->setQuery($query);
	if($db->loadResult()){
		$query = "SELECT * FROM #__lms_courses";
		$db->setQuery($query);
		$all_courses = $db->loadObjectList();
		$query = "SELECT * FROM #__lms_course_level";
		$db->setQuery($query);
		$all_courses_level = $db->loadObjectList();
		$tmp = array();
		$last_course = 0;
		foreach($all_courses_level as $cl){
			if($last_course == 0 || $last_course != $cl->course_id){
				$last_course = $cl->course_id;
			}	
			$tmp[$last_course][] = $cl;
		}
		$check_update = 0;
		$x_row = 0;
		foreach($all_courses as $course){
			if(isset($tmp[$course->id]) && count($tmp[$course->id])){
				$tmp_level = $tmp[$course->id];
				$max = 0;
				$catid = 0;
				foreach($tmp_level as $key=>$v){
					if($v->level > $max){
						$max = $v->level;
						$catid = $v->cat_id;	
					}
				}
				$query = "UPDATE #__lms_courses SET cat_id = '".$catid."' WHERE id = '".$course->id."'";
				$db->setQuery($query);
				if($db->query()){
					$check_update = 1;
				} 
				$error = $db->getErrorMsg();
				if($error){
					break;	
				}
				$x_row++;
			}	
		}
		
		if($error) {
			$out[count($out)-1]['#__lms_courses data'][] = '';
			$out[count($out)-1]['#__lms_courses data'][] = $error;
			$status = 'ERROR: '.$error;
			JLMS_insert_log( '#__lms_courses data', $status );
		} else {
			$out[count($out)-1]['#__lms_courses data'][] = $x_row.' '._JLMS_MAIN_RCS_FIXED;
			$out[count($out)-1]['#__lms_courses data'][] = '';
			$status = $query;
			JLMS_insert_log( '#__lms_courses data', $status );
		}
		if($check_update){
			$query = "DELETE FROM #__lms_course_level";
			$db->setQuery($query);
			$db->query();
		}
	}
	
	$out[]['#__lms_forums data'] = array();
	$count = 0;
	$query = "SELECT count(*) FROM #__lms_forums";
	$db->setQuery($query);
	if(!$db->loadResult()){
		
       $course_forum_name = '{course_name}';
       $course_forum_desc = '';
       $group_forum_name = '{course_name} - {group_name}';
       $group_forum_desc = 'Private group discussions';
       $module_forum_name = '{course_name} - {lpath_name}';
       $module_forum_desc = 'Module discussions';
       $private_forum_name = '{course_name} - Teachers board';
       $private_forum_desc = 'Private teachers discussions';
       $private_module_forum_name = '{course_name} - {lpath_name} - Teachers board';
       $private_module_forum_desc = 'Private discussions';

       $query = "INSERT INTO `#__lms_forums` VALUES (1, 0, 1, 0, 0, 1, 0, 0, '', '', ".$db->Quote($course_forum_name).", ".$db->Quote($course_forum_desc).");";
       $db->SetQuery($query);$db->query();
       
       			$error = $db->getErrorMsg();
				if($error) {
					$out[count($out)-1]['#__lms_forums data'][] = '';
					$out[count($out)-1]['#__lms_forums data'][] = $error;
		
					$status = 'ERROR: '.$error;
					JLMS_insert_log( '#__lms_forums data', $status );
				}
				else {
					$status = $query;
					JLMS_insert_log( '#__lms_forums data', $status );
					$count++;
				}
       
   		$query = "SELECT id FROM #__lms_forum_details where board_type = 2";
		$db->setQuery($query);
		$published = ($db->loadResult())? 1 : 0;
		
	       $query = "INSERT INTO `#__lms_forums` VALUES (2, 0, ".$published.", 1, 0, 1, 0, 0, '', '', ".$db->Quote($module_forum_name).", ".$db->Quote($module_forum_desc).");";
	       $db->SetQuery($query);$db->query();
	           	
	       		$error = $db->getErrorMsg();
				if($error) {
					$out[count($out)-1]['#__lms_forums data'][] = '';
					$out[count($out)-1]['#__lms_forums data'][] = $error;
		
					$status = 'ERROR: '.$error;
					JLMS_insert_log( '#__lms_forums data', $status );
				}
				else {
					$status = $query;
					JLMS_insert_log( '#__lms_forums data', $status );
					$count++;
				}

   		$query = "SELECT id FROM #__lms_forum_details where board_type = 3";
		$db->setQuery($query);
		$published = ($db->loadResult())? 1 : 0;
	       
       $query = "INSERT INTO `#__lms_forums` VALUES (3, 0, ".$published.", 0, 2, 1, 0, 0, '', '', ".$db->Quote($group_forum_name).", ".$db->Quote($group_forum_desc).");";
       $db->SetQuery($query);$db->query();
       
	       		$error = $db->getErrorMsg();
				if($error) {
					$out[count($out)-1]['#__lms_forums data'][] = '';
					$out[count($out)-1]['#__lms_forums data'][] = $error;
		
					$status = 'ERROR: '.$error;
					JLMS_insert_log( '#__lms_forums data', $status );
				}
				else {
					$status = $query;
					JLMS_insert_log( '#__lms_forums data', $status );
					$count++;
				}
       
   		$query = "SELECT id FROM #__lms_forum_details where board_type = 4";
		$db->setQuery($query);
		$published = ($db->loadResult())? 1 : 0;
       
       $query = "INSERT INTO `#__lms_forums` VALUES (4, 1, ".$published.", 0, 0, 1, 0, 1, '1,4,5', '', ".$db->Quote($private_forum_name).", ".$db->Quote($private_forum_desc).");";
       $db->SetQuery($query);$db->query();
       
	       		$error = $db->getErrorMsg();
				if($error) {
					$out[count($out)-1]['#__lms_forums data'][] = '';
					$out[count($out)-1]['#__lms_forums data'][] = $error;
		
					$status = 'ERROR: '.$error;
					JLMS_insert_log( '#__lms_forums data', $status );
				}
				else {
					$status = $query;
					JLMS_insert_log( '#__lms_forums data', $status );
					$count++;
				}       
       
   		$query = "SELECT id FROM #__lms_forum_details where board_type = 5";
		$db->setQuery($query);
		$published = ($db->loadResult())? 1 : 0;
       
       $query = "INSERT INTO `#__lms_forums` VALUES (5, 2, ".$published.", 1, 0, 1, 0, 1, '1,4,5', '', ".$db->Quote($private_module_forum_name).", ".$db->Quote($private_module_forum_desc).");";
       $db->SetQuery($query);$db->query();
       
	       		$error = $db->getErrorMsg();
				if($error) {
					$out[count($out)-1]['#__lms_forums data'][] = '';
					$out[count($out)-1]['#__lms_forums data'][] = $error;
		
					$status = 'ERROR: '.$error;
					JLMS_insert_log( '#__lms_forums data', $status );
				}
				else {
					$status = $query;
					JLMS_insert_log( '#__lms_forums data', $status );
					$count++;
				}       
       
   		$query = "SELECT id FROM #__lms_forum_details where board_type = 6";
		$db->setQuery($query);
		$published = ($db->loadResult())? 1 : 0;
       
       $query = "INSERT INTO `#__lms_forums` VALUES (6, 0, ".$published.", 1, 0, 1, 0, 0, '', '', ".$db->Quote($group_forum_name).", ".$db->Quote($group_forum_name).");";
       $db->SetQuery($query);$db->query();   
	       		
       			$error = $db->getErrorMsg();
				if($error) {
					$out[count($out)-1]['#__lms_forums data'][] = '';
					$out[count($out)-1]['#__lms_forums data'][] = $error;
		
					$status = 'ERROR: '.$error;
					JLMS_insert_log( '#__lms_forums data', $status );
				}
				else {
					$status = $query;
					JLMS_insert_log( '#__lms_forums data', $status );
					$count++;
				}
		
	}
	
	if($count) {
		$out[count($out)-1]['#__lms_forums data'][] = $count.' '._JLMS_MAIN_RCS_FIXED;
		$out[count($out)-1]['#__lms_forums data'][] = '';
	}
	else {
		$out[count($out)-1]['#__lms_forums data'][] = '';
		$out[count($out)-1]['#__lms_forums data'][] = '';
	}

	$out[]['Extension integrity'] = array();
	$version = new JVersion();
	$metadata_file = JPATH_SITE.DS.'components'.DS.'com_joomla_lms'.DS.'metadata.xml';
	$metadata_file_renamed = JPATH_SITE.DS.'components'.DS.'com_joomla_lms'.DS.'metadata.xml_';
	//TODO: add additional check for Joomal 1.7 and test this operation under Joomla 1.7.x
	if ($version->RELEASE >= '1.6') 
	{
		jimport('joomla.filesystem.file');
		if (JFile::exists($metadata_file)) {
			if (JFile::move($metadata_file, $metadata_file_renamed)) {
				$out[count($out)-1]['Extension integrity'][] = 'File "components/com_joomla_lms/metadata.xml" successfully renamed to "metadata.xml_"';
				$out[count($out)-1]['Extension integrity'][] = '';
			} else {
				$out[count($out)-1]['Extension integrity'][] = '';
				$out[count($out)-1]['Extension integrity'][] = 'ERROR: Failed to rename components/com_joomla_lms/metadata.xml to "metadata.xml_"';
			}
		} else {
				$out[count($out)-1]['Extension integrity'][] = 'OK';
				$out[count($out)-1]['Extension integrity'][] = '';
		}
	} else {
		//Joomla 1.5
		jimport('joomla.filesystem.file');
		if (JFile::exists($metadata_file)) {
			$out[count($out)-1]['Extension integrity'][] = 'OK';
			$out[count($out)-1]['Extension integrity'][] = '';
		} else {
			if (JFile::exists($metadata_file_renamed)) {
				if (JFile::move($metadata_file_renamed, $metadata_file)) {
					$out[count($out)-1]['Extension integrity'][] = 'File "metadata.xml_" successfully renamed to components/com_joomla_lms/metadata.xml';
					$out[count($out)-1]['Extension integrity'][] = '';
				} else {
					$out[count($out)-1]['Extension integrity'][] = '';
					$out[count($out)-1]['Extension integrity'][] = 'ERROR: Failed to rename file "metadata.xml_" to components/com_joomla_lms/metadata.xml';
				}
			} else {
				$out[count($out)-1]['Extension integrity'][] = '';
				$out[count($out)-1]['Extension integrity'][] = 'ERROR: File components/com_joomla_lms/metadata.xml not found. Please extract it form installation package manually.';
			}
		}
	}

	ALM_html::JLMS_results_check_database( $option, $out, 0, 2 );
}

function JLMS_check_table_is_empty( $table_name, $msg ) {

	$out = array();
	$out[] = '';
	if(!table_data($table_name)) {
		$out[] = $msg;
		$status = 'ERROR: '.$msg;
		JLMS_insert_log( $table_name." data", $status );
	}
	else {
		$out[] = '';
	}
	return $out;
}

function table_data($table_name) {
	$db = & JFactory::getDbo();

	$query = "SELECT count(1) FROM `".$table_name."`";
	$db->setQuery( $query );
	$isset = $db->loadResult();

	return $isset;
}


function JLMS_check_database_interface( $option ) {
	ALM_html::JLMS_check_database_interface( $option );
}

function JLMS_check_database( $option ) {
	$db = & JFactory::getDbo();

	$query = "CREATE TABLE IF NOT EXISTS `#__lms_maintenance_log` (
`ID` INT( 11 ) NOT NULL AUTO_INCREMENT ,
`log_time` DATETIME NOT NULL ,
`log_action` VARCHAR( 100 ) NOT NULL ,
`log_result` TEXT NOT NULL ,
PRIMARY KEY ( `ID` )
);";
	$db->setQuery( $query );
	$db->query();

	$querys = JLMS_all_query();	
	$out = array();

	$start = mosGetParam($_REQUEST,'table_num',0);
	$end = mosGetParam($_REQUEST,'table_num',0) + 20;

	if($end >= count($querys)) {
		$end = count($querys);
	}

	if(count($querys)) {
		for($i=$start;$i<$end;$i++) {
			if(substr($querys[$i],0,6) == 'CREATE') {
//				preg_match_all('|\`(.*)\`|U', $querys[$i],$row_fields);
//				preg_match_all('~\`([^\`]+)(\);|\,)~', $querys[$i],$row_types);
//				preg_match_all('~PRIMARY KEY.*\(\`(.*)\`\)~', $querys[$i],$row_primary_key);
//				preg_match_all('|UNIQUE KEY(.*\))|U', $querys[$i],$row_unique_key);
//				preg_match_all('|KEY(.*\))|U', $querys[$i],$row_key);

				preg_match('~\`([^\`]+)\`~m', $querys[$i],$table_name);
				preg_match_all('~^\s*`([^`]+)`\s+(.+)~m', $querys[$i],$row_fields);
				preg_match_all('~\sPRIMARY\s+KEY\s+(?:`([^`]+)`)?\s+(\([^)]+)~i', $querys[$i],$row_primary_key);
				preg_match_all('~\sUNIQUE\s+KEY\s+(?:`([^`]+)`)?\s+(\([^)]+)~i', $querys[$i],$row_unique_key);
				preg_match_all('~(?<!\sPRIMARY)(?<!\sUNIQUE)\s+KEY\s+(?:`([^`]+)`)?\s+(\([^)]+)~i', $querys[$i],$row_key);
				if (isset($row_fields[2])) {
					$ii = 0;
					while ($ii < count($row_fields[2])) {
						if (isset($row_fields[2][$ii])) {
							if (substr($row_fields[2][$ii],-1) == ',') {
								$row_fields[2][$ii] = substr($row_fields[2][$ii], 0, -1);
							}
						}
						
						$ii ++;
					}
				}
				$out[][$table_name[1]] = JLMS_check_tables($table_name[1], $row_fields, $querys[$i], $row_primary_key, $row_unique_key, $row_key);
			}
		}
	}
	
	$flag_stop = 0;
	$flag_check_tables = 0;

	if($end >= count($querys)) {
		$end = 0;
		$flag_stop = 1;
		$flag_check_tables = 1;
		$out[]['JoomlaLMS version'] = JLMS_check_version ();
	}

	ALM_html::JLMS_results_check_database( $option, $out, $end, $flag_stop, $flag_check_tables );
}

function JLMS_check_version () {
	$db = & JFactory::getDbo();

	$version = '';
	$file = JPATH_SITE .'/administrator/components/com_joomla_lms/joomla_lms.xml';
	$xml_file_contents = file_get_contents($file);
	preg_match('/<version>(.*)<\/version>/',$xml_file_contents, $matches);
	if (isset($matches[1])) {
		$version = trim($matches[1]);
	}

	$query = "SELECT lms_config_value FROM `#__lms_config` WHERE lms_config_var = 'jlms_version'";
	$db->setQuery( $query );
	$jlms_version = $db->loadResult();
	$query = '';
	if($jlms_version && $jlms_version != $version) {
		$query = "UPDATE `#__lms_config` SET `lms_config_value` = '".$version."' WHERE lms_config_var = 'jlms_version'";
		$db->setQuery($query);
		$db->query();
	}
	elseif(!$jlms_version) {
		$query = "INSERT INTO `#__lms_config` ( `lms_config_var` , `lms_config_value` )"
		."\n VALUES ('jlms_version', '".$version."')";
		$db->setQuery($query);
		$db->query();
	}
	else {}

	$output = array();
	if($query) {
		$error = $db->getErrorMsg();
		if($error) {
			JLMS_insert_log( 'JoomlaLMS version', $query );
			$status = 'ERROR: '.$error;
			$output[]= '';
			$output[]= $error;
		}
		else {
			$status = $query;
			$output[]= _JLMS_MAIN_DB_V_UPDATED.' '.$version;
			$output[]= '';
		}
		JLMS_insert_log('JoomlaLMS version', $status );
	} elseif ($jlms_version) {
		// no query - JoomlaLMS version is actual
		$output[]= _JLMS_MAIN_DB_V.' '.$jlms_version;
		$output[]= '';
	}
	return $output;
}

function JLMS_check_tables($table_name, $row_fields, $querys, $row_primary_key, $row_unique_key, $row_key) {
	$db = & JFactory::getDbo();
	$app = & JFactory::getApplication();
	$dbprefix = $app->getCfg('dbprefix');
	//$query = "SELECT count(1) FROM `".$table_name."`";
	$query = "SHOW TABLES like '".str_replace('#__', $dbprefix, $table_name)."'";
	$db->setQuery( $query );
	$isset = $db->loadResult();

	$y = 0;
	$output = array();

	if($isset) {

		$query = "SHOW COLUMNS FROM `".$table_name."`";
		$db->setQuery( $query );
		$colums = $db->loadObjectList();

		$arr_field = array();
		for($i=0;$i<count($colums);$i++) {
			$arr_field[] = $colums[$i]->Field;
		}

		foreach ($row_fields[1] as $i=>$v) {
			if(!in_array($row_fields[1][$i],$arr_field)) {
				if($i==0) {
					$query_after = 'FIRST';
				}
				else {
					$query_after = 'AFTER `'.$row_fields[1][$i-1].'`';
				}
	
				$query = "ALTER TABLE `".$table_name."` ADD `".$row_fields[1][$i]."` ".rtrim(trim($row_fields[2][$i]), ',')." ".$query_after."";
				$db->setQuery( $query );
				$db->query();
				
				$output[$y] = $query;
				$y++;

				$error = $db->getErrorMsg();
				$output[$y] = $error;
				$y++;

				if($db->getErrorMsg()) {
					JLMS_insert_log( $table_name, $query );
					$status = 'ERROR: '.$error;
				}
				else {
					$status = $query;
				}
				JLMS_insert_log( $table_name, $status );			
			}
		}

		$query = "SHOW KEYS FROM `".$table_name."`";
		$db->setQuery( $query );
		$keys1 = $db->loadObjectList();
		
		
		$keys = array();
		$key_name = '';
		for($i=0;$i<count($keys1);$i++) {
			$key_name_cur = $keys1[$i]->Key_name;
			if ($key_name_cur == $key_name && count($keys) > 0) {
				$keys[count($keys)-1]->Column_name = $keys[count($keys)-1]->Column_name.',`'.$keys1[$i]->Column_name.'`';
			} else {
				$keys[] = $keys1[$i];
				$keys[count($keys)-1]->Column_name = '`'.$keys[count($keys)-1]->Column_name.'`';
				$key_name = $key_name_cur;
			}
		}
		
		
		$arr_pri = array();
		$arr_uni = array();
		$arr_key = array();
		
		foreach($keys as $k=>$v) {
		
		//for($i=0;$i<count($keys);$i++) {
			if($keys[$k]->Key_name == 'PRIMARY') {
				$arr_pri[] = $keys[$k]->Column_name;
			}
			if($keys[$k]->Key_name != 'PRIMARY' && $keys[$k]->Non_unique == 0) {
				$arr_uni[][$keys[$k]->Key_name] = $keys[$k]->Column_name;
			}
			if($keys[$k]->Key_name != 'PRIMARY' && $keys[$k]->Non_unique == 1) {
				$arr_key[][$keys[$k]->Key_name] = $keys[$k]->Column_name;
			}
		}
		
		if(isset($row_primary_key[2][0])) {
			//preg_match('~\`([^\`]+)\`~m', $row_primary_key[2][0],$primary_key);		
			
			$primary_key = substr($row_primary_key[2][0],1,strlen($row_primary_key[2][0]));
			
			if(is_array($arr_pri)) {
				$arr_pri = implode(',',$arr_pri);
			}
			
			if(!isset($arr_pri) || $primary_key !=$arr_pri) {
				$query = "ALTER TABLE ".$table_name." DROP PRIMARY KEY";
				$db->setQuery( $query );
				$db->query();

				if($db->getErrorMsg()) {
					$output[$y] = $query;
					$y++;

					$error = $db->getErrorMsg();
					$output[$y] = $error;
					$y++;

					if($db->getErrorMsg()) {
						JLMS_insert_log( $table_name, $query );
						$status = 'ERROR: '.$error;
					}
					else {
						$status = $query;
					}
					JLMS_insert_log( $table_name, $status );
				}

				if($primary_key) {
					$query = "ALTER TABLE ".$table_name." ADD PRIMARY KEY ($primary_key) ";
					$db->setQuery( $query );
					$db->query();

					$output[$y] = $query;
					$y++;

					$error = $db->getErrorMsg();
					$output[$y] = $error;
					$y++;

					if($db->getErrorMsg()) {
						JLMS_insert_log( $table_name, $query );
						$status = 'ERROR: '.$error;
					}
					else {
						$status = $query;
					}
					JLMS_insert_log( $table_name, $status );
				}
			}
		}

		$arr_uni_xml = array();
		if(isset($row_unique_key[1])) {
			for($i=0;$i<count($row_unique_key[2]);$i++) {
				//preg_match_all('~\`([^\`]+)\`~m', $row_unique_key[2][$i],$value);		

				//for($j=0;$j<count($value[1]);$j++) {
					//$arr_uni_xml[][$row_unique_key[1][$i]] = $value[1][$j];		
					$arr_uni_xml[][$row_unique_key[1][$i]] = substr($row_unique_key[2][$i],1,strlen($row_unique_key[2][$i]));			
				//}
			}
		}
		
		$not_in_xml = array_diff_assoc($arr_uni, $arr_uni_xml);
		$not_in_bd = array_diff_assoc($arr_uni_xml, $arr_uni);
		
		$last_drop = '';
		foreach ($not_in_xml as $k=>$v) {
			foreach ( $v as $n=>$m) {
				if ($last_drop && $n == $last_drop) {
					//this index is already removed
				} else {
					if ($table_name == '#__lms_n_scorm_scoes_track') {
						// do not remove indexes configured by users for tracking table
					} else {
						$query = "ALTER TABLE ".$table_name." DROP INDEX ".$m."";
						$db->setQuery( $query );
						$db->query();
						$last_drop = $n;
		
						$output[$y] = $query;
						$y++;
		
						$error = $db->getErrorMsg();
						$output[$y] = $error;
						$y++;
		
						if($db->getErrorMsg()) {
							JLMS_insert_log( $table_name, $query );
							$status = 'ERROR: '.$error;
						}
						else {
							$status = $query;
						}
						JLMS_insert_log( $table_name, $status );
					}
				}
			}	
		}
		
		foreach ($not_in_bd as $k=>$v) {
			foreach ( $v as $n=>$m) {
				
				$query = "ALTER TABLE ".$table_name." ADD UNIQUE ($m)";
				$db->setQuery( $query );
				$db->query();
				
				$output[$y] = $query;
				$y++;

				$error = $db->getErrorMsg();
				$output[$y] = $error;
				$y++;

				if($db->getErrorMsg()) {
					JLMS_insert_log( $table_name, $query );
					$status = 'ERROR: '.$error;
				}
				else {
					$status = $query;
				}
				JLMS_insert_log( $table_name, $status );
			}	
		}
		
		$arr_key_xml = array();
		if(isset($row_key[1])) {
			for($i=0;$i<count($row_key[1]);$i++) {
				//preg_match_all('~\`([^\`]+)\`~m', $row_key[2][$i],$value);		
				
				//for($j=0;$j<count($value[1]);$j++) {
					$arr_key_xml[][$row_key[1][$i]] = substr($row_key[2][$i],1,strlen($row_key[2][$i]));					
				//}
			}
		}
		
		$not_in_xml = array_diff_assoc($arr_key, $arr_key_xml);
		$not_in_bd = array_diff_assoc($arr_key_xml, $arr_key);
		
		foreach ($not_in_xml as $k=>$v) {
			foreach ( $v as $n=>$m) {
				if ($last_drop && $n == $last_drop) {
					//this index is already removed
				} else {
					if ($table_name == '#__lms_n_scorm_scoes_track') {
						// do not remove indexes configured by users for tracking table
					} else {
						$query = "ALTER TABLE ".$table_name." DROP INDEX ".$m."";
						$db->setQuery( $query );
						$db->query();
		
						$output[$y] = $query;
						$y++;
		
						$error = $db->getErrorMsg();
						$output[$y] = $error;
						$y++;
		
						if($db->getErrorMsg()) {
							JLMS_insert_log( $table_name, $query );
							$status = 'ERROR: '.$error;
						}
						else {
							$status = $query;
						}
						JLMS_insert_log( $table_name, $status );
					}
				}
			}	
		}
		
		foreach ($not_in_bd as $k=>$v) {
			
			foreach ( $v as $n=>$m) {
				
				$query = "ALTER TABLE ".$table_name." ADD INDEX ($m)";
				
				$db->setQuery( $query );
				$db->query();

				$output[$y] = $query;
				$y++;

				$error = $db->getErrorMsg();
				$output[$y] = $error;
				$y++;

				if($db->getErrorMsg()) {
					JLMS_insert_log( $table_name, $query );
					$status = 'ERROR: '.$error;
				}
				else {
					$status = $query;
				}
				JLMS_insert_log( $table_name, $status );
			}	
		}
		
		
	}
	else {
		$query = $querys;
		$db->setQuery( $query );
		$db->query();

		$output[$y] = $query;
		$y++;

		$error = $db->getErrorMsg();
		$output[$y] = $error;
		$y++;

		if($db->getErrorMsg()) {
			JLMS_insert_log( $table_name, $query );
			$status = 'ERROR: '.$error;
		}
		else {
			$status = $query;
		}
		JLMS_insert_log( $table_name, $status );
	}
	return $output;
}

function JLMS_all_query() {
	$queries_array = array();
	$file = JPATH_SITE .'/administrator/components/com_joomla_lms/install.sql';
	$xml_file_contents = file_get_contents($file);
	preg_match_all('/--\s<query>(.*)--\s<\/query>/isU',$xml_file_contents, $matches);
	if (isset($matches[1][0])) {
		foreach ($matches[1] as $single_query_match) {
			if (trim($single_query_match)) {
				$queries_array[] = trim($single_query_match);
			}
		}
	}

	if(count($queries_array)) {
		return $queries_array;
	} else {
		return false;
	}
}

function JLMS_insert_log( $table_name, $status ) {
	$db = & JFactory::getDbo();
	$query = "INSERT INTO `#__lms_maintenance_log` ( `ID` , `log_time` , `log_action` , `log_result` )"
			."\n VALUES ('', NOW(), '".$table_name."', ".$db->quote($status).")";
	$db->setQuery( $query );
	$db->query();
}

function JLMS_insert_menu_items_if_no() {
	$db = & JFactory::getDbo();
		$error = '';

		$query = "INSERT INTO `#__lms_menu` (`lang_var`, `image`, `task`, `ordering`, `published`, `is_separator`, `user_access`)"
		. "\n VALUES ('_JLMS_TOOLBAR_HOME', 'tlb_home.png', '', 0, 1, 0, 2),"
		. "\n ('_JLMS_TOOLBAR_HOME', 'tlb_home.png', '', 1, 1, 0, 1),"
		. "\n ('_JLMS_TOOLBAR_HOME', 'tlb_home.png', '', 1, 1, 0, 6),"
		. "\n ('_JLMS_TOOLBAR_COURSES', 'tlb_courses.png', 'courses', 1, 1, 0, 2),"
		. "\n ('_JLMS_TOOLBAR_COURSES', 'tlb_courses.png', 'courses', 2, 1, 0, 1),"
		. "\n ('_JLMS_TOOLBAR_COURSES', 'tlb_courses.png', 'courses', 2, 1, 0, 6),"
		. "\n ('', '', '', 2, 1, 1, 2),"
		. "\n ('', '', '', 3, 1, 1, 1),"
		. "\n ('', '', '', 4, 1, 1, 6)";
		$db->setQuery($query);
		$db->query();

		if($db->getErrorMsg()) {
			$error .= $db->getErrorMsg();
		}

		$query = "INSERT INTO `#__lms_menu` (`lang_var`, `image`, `task`, `ordering`, `published`, `is_separator`, `user_access`)"
		. "\n VALUES ('_JLMS_PATHWAY_COURSE_HOME', 'tlb_course_home.png', 'details_course', 3, 1, 0, 2),"
		. "\n ('_JLMS_PATHWAY_COURSE_HOME', 'tlb_course_home.png', 'details_course', 4, 1, 0, 1),"
		. "\n ('_JLMS_PATHWAY_COURSE_HOME', 'tlb_course_home.png', 'details_course', 5, 1, 0, 6),"
		. "\n ('_JLMS_TOOLBAR_AGENDA', 'tlb_agenda.png', 'agenda', 4, 1, 0, 2),"
		. "\n ('_JLMS_TOOLBAR_AGENDA', 'tlb_agenda.png', 'agenda', 5, 1, 0, 1),"
		. "\n ('_JLMS_TOOLBAR_AGENDA', 'tlb_agenda.png', 'agenda', 6, 1, 0, 6),"
		. "\n ('_JLMS_TOOLBAR_DOCS', 'tlb_docs.png', 'documents', 5, 1, 0, 2),"
		. "\n ('_JLMS_TOOLBAR_DOCS', 'tlb_docs.png', 'documents', 6, 1, 0, 1),"
		. "\n ('_JLMS_TOOLBAR_LPATH', 'tlb_lpath.png', 'learnpaths', 6, 1, 0, 2),"
		. "\n ('_JLMS_TOOLBAR_LPATH', 'tlb_lpath.png', 'learnpaths', 7, 1, 0, 1),"
		. "\n ('_JLMS_TOOLBAR_LINKS', 'tlb_links.png', 'links', 8, 1, 0, 2),"
		. "\n ('_JLMS_TOOLBAR_LINKS', 'tlb_links.png', 'links', 8, 1, 0, 1)";
		$db->setQuery($query);
		$db->query();

		if($db->getErrorMsg()) {
			$error .= $db->getErrorMsg();
		}

		$query = "INSERT INTO `#__lms_menu` (`lang_var`, `image`, `task`, `ordering`, `published`, `is_separator`, `user_access`)"
		. "\n VALUES ('_JLMS_TOOLBAR_QUIZZES', 'tlb_quiz.png', 'quizzes', 9, 1, 0, 2),"
		. "\n ('_JLMS_TOOLBAR_QUIZZES', 'tlb_quiz.png', 'quizzes', 9, 1, 0, 1),"
		. "\n ('_JLMS_TOOLBAR_DROP', 'tlb_dropbox.png', 'dropbox', 10, 1, 0, 2),"
		. "\n ('_JLMS_TOOLBAR_DROP', 'tlb_dropbox.png', 'dropbox', 10, 1, 0, 1),"
		. "\n ('_JLMS_TOOLBAR_HOMEWORK', 'tlb_homework.png', 'homework', 11, 1, 0, 2),"
		. "\n ('_JLMS_TOOLBAR_HOMEWORK', 'tlb_homework.png', 'homework', 11, 1, 0, 1),"
		. "\n ('_JLMS_TOOLBAR_HOMEWORK', 'tlb_homework.png', 'homework', 7, 1, 0, 6),"
		. "\n ('_JLMS_TOOLBAR_ATTEND', 'tlb_attendance.png', 'attendance', 12, 1, 0, 2),"
		. "\n ('_JLMS_TOOLBAR_ATTEND', 'tlb_attendance.png', 'attendance', 12, 1, 0, 1),"
		. "\n ('_JLMS_TOOLBAR_ATTEND', 'tlb_attendance.png', 'attendance', 8, 1, 0, 6),"
		. "\n ('_JLMS_TOOLBAR_FORUM', 'tlb_forum.png', 'course_forum', 14, 1, 0, 2),"
		. "\n ('_JLMS_TOOLBAR_FORUM', 'tlb_forum.png', 'course_forum', 14, 1, 0, 1),"
		. "\n ('_JLMS_TOOLBAR_CHAT', 'tlb_chat.png', 'chat', 15, 1, 0, 2),"
		. "\n ('_JLMS_TOOLBAR_CHAT', 'tlb_chat.png', 'chat', 15, 1, 0, 1)";
		$db->setQuery($query);
		$db->query();

		if($db->getErrorMsg()) {
			$error .= $db->getErrorMsg();
		}

		$query = "INSERT INTO `#__lms_menu` (`lang_var`, `image`, `task`, `ordering`, `published`, `is_separator`, `user_access`)"
		. "\n VALUES ('', '', '', 20, 1, 1, 2),"
		. "\n ('_JLMS_TOOLBAR_TRACK', 'tlb_tracking.png', 'tracking', 19, 1, 0, 1),"
		. "\n ('_JLMS_TOOLBAR_MAILBOX', 'tlb_mailbox.png', 'mailbox', 19, 1, 0, 2),"
		. "\n ('_JLMS_TOOLBAR_MAILBOX', 'tlb_mailbox.png', 'mailbox', 20, 1, 0, 1),"
		. "\n ('_JLMS_TOOLBAR_USER_OPTIONS', 'tlb_switch.png', '', 21, 1, 0, 2),"
		. "\n ('_JLMS_TOOLBAR_USERS', 'tlb_users.png', 'course_users', 21, 1, 0, 1),"
		. "\n ('_JLMS_TOOLBAR_GRADEBOOK', 'tlb_gradebook.png', 'gradebook', 9, 1, 0, 6),"
		. "\n ('_JLMS_TOOLBAR_USER_OPTIONS', 'tlb_switch.png', '', 24, 1, 0, 1),"
		. "\n ('_JLMS_TOOLBAR_HELP', 'tlb_help.png', 'view_by_task', 22, 1, 0, 2),"
		. "\n ('_JLMS_TOOLBAR_HELP', 'tlb_help.png', '', 25, 1, 0, 1),"
		. "\n ('_JLMS_TOOLBAR_CONF', 'btn_cam.png', 'conference', 16, 1, 0, 2),"
		. "\n ('_JLMS_TOOLBAR_HELP', 'tlb_help.png', 'view_by_task', 12, 1, 0, 6),"
		. "\n ('_JLMS_TOOLBAR_GRADEBOOK', 'tlb_gradebook.png', 'gradebook', 18, 1, 0, 2)";
		$db->setQuery($query);
		$db->query();

		if($db->getErrorMsg()) {
			$error .= $db->getErrorMsg();
		}

		$query = "INSERT INTO `#__lms_menu` (`lang_var`, `image`, `task`, `ordering`, `published`, `is_separator`, `user_access`)"
		. "\n VALUES ('', '', '', 17, 1, 1, 2),"
		. "\n ('', '', '', 13, 1, 1, 2),"
		. "\n ('', '', '', 10, 1, 1, 6),"
		. "\n ('_JLMS_TOOLBAR_GRADEBOOK', 'tlb_gradebook.png', 'gradebook', 18, 1, 0, 1),"
		. "\n ('_JLMS_TOOLBAR_USER_OPTIONS', 'tlb_switch.png', '', 11, 1, 0, 6),"
		. "\n ('_JLMS_TOOLBAR_CONF', 'btn_cam.png', 'conference', 16, 1, 0, 1),"
		. "\n ('', '', '', 17, 1, 1, 1),"
		. "\n ('', '', '', 23, 1, 1, 1),"
		. "\n ('', '', '', 13, 1, 1, 1),"
		. "\n ('_JLMS_TOOLBAR_HOME', 'tlb_home.png', '', 0, 1, 0, 0),"
		. "\n ('_JLMS_TOOLBAR_COURSES', 'tlb_courses.png', 'courses', 1, 1, 0, 0),"
		. "\n ('_JLMS_TOOLBAR_CEO_PARENT', 'tlb_ceo.png', 'ceo_page', 2, 1, 0, 0)";
		$db->setQuery($query);
		$db->query();

		if($db->getErrorMsg()) {
			$error .= $db->getErrorMsg();
		}		

		$query = "INSERT INTO `#__lms_menu` (`lang_var`, `image`, `task`, `ordering`, `published`, `is_separator`, `user_access`)"
		. "\n VALUES ('_JLMS_TOOLBAR_SUBSCRIPTIONS', 'tlb_subscriptions.png', 'subscription', 3, 1, 0, 1),"
		. "\n ('_JLMS_TOOLBAR_SUBSCRIPTIONS', 'tlb_subscriptions.png', 'subscription', 2, 1, 0, 2),"
		. "\n ('_JLMS_TOOLBAR_SUBSCRIPTIONS', 'tlb_subscriptions.png', 'subscription', 2, 1, 0, 0),"
		. "\n ('_JLMS_TOOLBAR_SUBSCRIPTIONS', 'tlb_subscriptions.png', 'subscription', 2, 1, 0, 6)";
		$db->setQuery($query);
		$db->query();

		if($db->getErrorMsg()) {
			$error .= $db->getErrorMsg();
		}

		$query = "INSERT INTO `#__lms_menu` (`lang_var`, `image`, `task`, `ordering`, `published`, `is_separator`, `user_access`)"
		. "\n VALUES ('_JLMS_TOOLBAR_HOME', 'tlb_home.png', '', 0, 1, 0, -1),"
		. "\n ('_JLMS_TOOLBAR_SUBSCRIPTIONS', 'tlb_subscriptions.png', 'subscription', 1, 1, 0, -1)";
		$db->setQuery($query);
		$db->query();

		if($db->getErrorMsg()) {
			$error .= $db->getErrorMsg();
		}		

		$query = "INSERT INTO `#__lms_menu` (`lang_var`, `image`, `task`, `ordering`, `published`, `is_separator`, `user_access`)"
		. "\n VALUES ('_JLMS_TOOLBAR_LIBRARY', 'tlb_library.png', 'outdocs', 3, 1, 0, 1),"
		. "\n ('_JLMS_TOOLBAR_LIBRARY', 'tlb_library.png', 'outdocs', 2, 1, 0, 2),"
		. "\n ('_JLMS_TOOLBAR_LIBRARY', 'tlb_library.png', 'outdocs', 2, 1, 0, 0)";
		$db->setQuery($query);
		$db->query();

		if($db->getErrorMsg()) {
			$error .= $db->getErrorMsg();
		}	

		return $error;

	}
?>