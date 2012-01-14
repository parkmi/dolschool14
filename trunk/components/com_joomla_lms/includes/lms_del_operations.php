<?php
/**
* /includes/lms_del_operations.php
* Joomla LMS Component
* * * ElearningForce DK
**/

// no direct access
defined( '_JLMS_EXEC' ) or die( 'Restricted access' );

function JLMS_DelOp_deleteUserGroup( $course_id, &$cid ) {
	global $JLMS_DB, $JLMS_CONFIG;
	$cids = implode(',',$cid);		
	$query = "DELETE FROM #__lms_usergroups WHERE id IN ($cids) AND course_id = '".$course_id."'";
	$JLMS_DB->SetQuery( $query );
	$JLMS_DB->query();
	$query = "SELECT user_id FROM #__lms_users_in_groups WHERE course_id = '".$course_id."' AND group_id IN ($cids)";
	$JLMS_DB->SetQuery( $query );
	$user_ids = array();
	$user_ids = $JLMS_DB->LoadResultArray();
	$query = "UPDATE #__lms_users_in_groups SET group_id = '0' WHERE course_id = '".$course_id."' AND group_id IN ($cids)";
	$JLMS_DB->SetQuery( $query );
	$JLMS_DB->query();
	if ($JLMS_CONFIG->get('plugin_forum')) {
		$forum = & JLMS_SMF::getInstance();		
		if ( !is_object( $forum ) ) {			
			$query = "SELECT id FROM #__lms_forums WHERE forum_level = 0 AND user_level IN (0,1)";
			$JLMS_DB->SetQuery( $query );
			$board_types = $JLMS_DB->LoadResultArray();
			if (count($board_types)) {
				$board_types_str = implode(',', $board_types);
				$query = "SELECT id, course_id, board_type, group_id, ID_GROUP AS id_group, ID_CAT AS id_cat, ID_BOARD AS id_board, is_active, need_update FROM #__lms_forum_details WHERE course_id = $course_id AND group_id IN ($cids) AND group_id <> 0 AND board_type IN ($board_types)";
				$JLMS_DB->SetQuery( $query );
				$forum_det = $JLMS_DB->LoadObjectList();
				if (count($forum_det)) {					
					$boards = array();
					$mem_groups = array();
					foreach ($forum_det as $fd) {
						$boards[] = $fd->id_board;
						if ($fd->id_group > 8) {
							$mem_groups[] = $fd->id_group;
						}
					}				
															
					$forum->deleteBoards( $boards );
					$forum->deleteTopics( $boards );
					$forum->deleteMessages( $boards );			
					$forum->deleteModerators( $boards );	
					$forum->deleteMembergroups( $mem_groups );
					$forum->deletePermissions( $mem_groups );			
				}
			}
		}
	}
	
	return $user_ids;
}

function JLMS_DelOp_deleteForums( &$db, &$cid ) {
	global $JLMS_CONFIG;
	$newcids = array();
	foreach ($cid as $one_cid) {
		$one_cid_int = intval($one_cid);
		if ($one_cid_int) {
			$newcids[] = $one_cid_int;
		}
	}
	$cids = implode(',',$newcids);
	$query = "SELECT id, course_id, board_type, group_id, ID_GROUP AS id_group, ID_CAT AS id_cat, ID_BOARD AS id_board, is_active, need_update FROM #__lms_forum_details WHERE id IN ($cids)";
	$db->SetQuery( $query );
	$forum_det = $db->LoadObjectList();
	if (count($forum_det)) {
		$query = "DELETE FROM #__lms_forum_details WHERE id IN ($cids)";
		$db->SetQuery( $query );
		$db->query();
		if ($JLMS_CONFIG->get('plugin_forum')) {			
			$forum = & JLMS_SMF::getInstance();		
			if ( !is_object( $forum ) ) {						
				$boards = array();
				$mem_groups = array();
				foreach ($forum_det as $fd) {
					$boards[] = $fd->id_board;
					if ($fd->id_group > 8) {
						$mem_groups[] = $fd->id_group;
					}
				}
				
				$forum->deleteBoards( $boards );
				$forum->deleteTopics( $boards );
				$forum->deleteMessages( $boards );			
				$forum->deleteModerators( $boards );	
				$forum->deleteMembergroups( $mem_groups );
				$forum->deletePermissions( $mem_groups );
			}
		}
	}
}

function JLMS_DelOp_deleteCourseStudents( $course_id, $group_id, &$del_ids ) {
	global $JLMS_DB;
	$del_ids_str = implode(',',$del_ids);
	//modified by TPETb
	//canceled group_id check
	$query = "DELETE FROM #__lms_users_in_groups WHERE course_id = '".$course_id."' AND user_id IN ($del_ids_str)";
	$JLMS_DB->SetQuery( $query );
	$JLMS_DB->query();
	$query = "DELETE FROM #__lms_certificate_users WHERE course_id = '".$course_id."' AND user_id IN ($del_ids_str)";
	$JLMS_DB->SetQuery( $query );
	$JLMS_DB->query();
	$query = "DELETE FROM #__lms_chat_history WHERE course_id = '".$course_id."' AND user_id IN ($del_ids_str)";
	$JLMS_DB->SetQuery( $query );
	$JLMS_DB->query();
	$query = "DELETE FROM #__lms_chat_users WHERE course_id = '".$course_id."' AND user_id IN ($del_ids_str)";
	$JLMS_DB->SetQuery( $query );
	$JLMS_DB->query();
	$query = "SELECT distinct file_id FROM #__lms_dropbox WHERE course_id = '".$course_id."' AND ( owner_id IN ($del_ids_str) OR recv_id IN ($del_ids_str) )";
	$JLMS_DB->SetQuery( $query );
	$del_files = $JLMS_DB->LoadResultArray();
	$query = "DELETE FROM #__lms_dropbox WHERE course_id = '".$course_id."' AND ( owner_id IN ($del_ids_str) OR recv_id IN ($del_ids_str) )";
	$JLMS_DB->SetQuery( $query );
	$JLMS_DB->query();
	$files_del = JLMS_checkFiles( $course_id, $del_files );
	if (count($files_del)) {
		JLMS_deleteFiles($files_del);
	}
	$query = "DELETE FROM #__lms_gradebook WHERE course_id = '".$course_id."' AND user_id IN ($del_ids_str)";
	$JLMS_DB->SetQuery( $query );
	$JLMS_DB->query();
	$query = "DELETE FROM #__lms_homework_results WHERE course_id = '".$course_id."' AND user_id IN ($del_ids_str)";
	$JLMS_DB->SetQuery( $query );
	$JLMS_DB->query();
	$query = "SELECT id FROM #__lms_learn_path_results WHERE course_id = '".$course_id."' AND user_id IN ($del_ids_str)";
	$JLMS_DB->SetQuery( $query );
	$lp_res_ids = $JLMS_DB->LoadResultArray();

	$query = "DELETE FROM #__lms_learn_path_grades WHERE course_id = '".$course_id."' AND user_id IN ($del_ids_str)";
	$JLMS_DB->SetQuery( $query );
	$JLMS_DB->query();

	if (count($lp_res_ids)) {
		$lpr_str = implode(',',$lp_res_ids);
		$query = "DELETE FROM #__lms_learn_path_results WHERE id IN ($lpr_str)";
		$JLMS_DB->SetQuery( $query );
		$JLMS_DB->query();
		$query = "DELETE FROM #__lms_learn_path_step_results WHERE result_id IN ($lpr_str)";
		$JLMS_DB->SetQuery( $query );
		$JLMS_DB->query();
		// 18.08.2007 (deleting of lp quiz results)
		$query = "DELETE FROM #__lms_learn_path_step_quiz_results WHERE result_id IN ($lpr_str)";
		$JLMS_DB->SetQuery( $query );
		$JLMS_DB->query();
	}

	//delete QUIZ results
	$query = "DELETE FROM #__lms_quiz_results WHERE course_id = '".$course_id."' AND user_id IN ($del_ids_str)";
	$JLMS_DB->SetQuery( $query );
	$JLMS_DB->query();
	$query = "SELECT c_id FROM #__lms_quiz_t_quiz WHERE course_id = '".$course_id."'";
	$JLMS_DB->SetQuery( $query );
	$q_ids = $JLMS_DB->LoadResultArray();
	if (count($q_ids)) {
		$q_str = implode(',',$q_ids);
		$query = "SELECT c_id FROM #__lms_quiz_r_student_quiz WHERE c_quiz_id IN ($q_str) AND c_student_id IN ($del_ids_str)";
		$JLMS_DB->SetQuery( $query );
		$rsq_ids = $JLMS_DB->LoadResultArray();
		if (count($rsq_ids)) {
			$rsq_str = implode(',',$rsq_ids);
			$query = "SELECT c_id FROM #__lms_quiz_r_student_question WHERE c_stu_quiz_id IN ( $rsq_str )";
			$JLMS_DB->SetQuery( $query );
			$rsqq_ids = $JLMS_DB->LoadResultArray();
			if (count($rsqq_ids)) {
				$stu_cids = implode(',',$rsqq_ids);
				$query = "DELETE FROM #__lms_quiz_r_student_blank WHERE c_sq_id IN ( $stu_cids )";
				$JLMS_DB->setQuery( $query );
				$JLMS_DB->query();
				$query = "DELETE FROM #__lms_quiz_r_student_choice WHERE c_sq_id IN ( $stu_cids )";
				$JLMS_DB->setQuery( $query );
				$JLMS_DB->query();
				$query = "DELETE FROM #__lms_quiz_r_student_hotspot WHERE c_sq_id IN ( $stu_cids )";
				$JLMS_DB->setQuery( $query );
				$JLMS_DB->query();
				$query = "DELETE FROM #__lms_quiz_r_student_matching WHERE c_sq_id IN ( $stu_cids )";
				$JLMS_DB->setQuery( $query );
				$JLMS_DB->query();
				$query = "DELETE FROM #__lms_quiz_r_student_survey WHERE c_sq_id IN ( $stu_cids )";
				$JLMS_DB->setQuery( $query );
				$JLMS_DB->query();
				$query = "DELETE FROM #__lms_quiz_r_student_question WHERE c_id IN ( $stu_cids )";
				$JLMS_DB->setQuery( $query );
				$JLMS_DB->query();
			}
			$query = "DELETE FROM #__lms_quiz_r_student_quiz WHERE c_id IN ( $rsq_str )";
			$JLMS_DB->setQuery( $query );
			$JLMS_DB->query();
		}
	}
	// end of QUIZ

    //delete SCORMs tracking
	$query = "SELECT id FROM #__lms_scorm_packages WHERE course_id = '".$course_id."'";
	$JLMS_DB->SetQuery( $query );
	$sc_ids = $JLMS_DB->LoadResultArray();
    //old scorms (before JoomlaLMS 1.0.5)
	if (count($sc_ids)) {
		$sc_str = implode(',',$sc_ids);
		$query = "DELETE FROM #__lms_scorm_sco WHERE content_id IN ($sc_str) AND user_id IN ($del_ids_str)";
		$JLMS_DB->SetQuery( $query );
		$JLMS_DB->query();
	}
    //new scorms
	$query = "SELECT id FROM #__lms_n_scorm WHERE course_id = '".$course_id."'";
	$JLMS_DB->SetQuery( $query );
	$scn_ids = $JLMS_DB->LoadResultArray();
	if (count($scn_ids)) {
		$scn_str = implode(',',$scn_ids);
		$query = "DELETE FROM #__lms_n_scorm_scoes_track WHERE scormid IN ($scn_str) AND userid IN ($del_ids_str)";
		$JLMS_DB->SetQuery( $query );
		$JLMS_DB->query();
	}
    //end of SCORMs part

	//delete TRACKING records
	$query = "DELETE FROM #__lms_track_chat WHERE course_id = '".$course_id."' AND user_id IN ($del_ids_str)";
	$JLMS_DB->SetQuery( $query );
	$JLMS_DB->query();
	$query = "DELETE FROM #__lms_track_hits WHERE course_id = '".$course_id."' AND user_id IN ($del_ids_str)";
	$JLMS_DB->SetQuery( $query );
	$JLMS_DB->query();
	$query = "DELETE FROM #__lms_track_learnpath_stats WHERE course_id = '".$course_id."' AND user_id IN ($del_ids_str)";
	$JLMS_DB->SetQuery( $query );
	$JLMS_DB->query();
	$query = "SELECT id FROM #__lms_documents WHERE course_id = '".$course_id."'";
	$JLMS_DB->SetQuery( $query );
	$doc_ids = $JLMS_DB->LoadResultArray();
	if (count($doc_ids)) {
		$d_str = implode(',',$doc_ids);
		$query = "DELETE FROM #__lms_track_downloads WHERE doc_id IN ($d_str) AND user_id IN ($del_ids_str)";
		$JLMS_DB->SetQuery( $query );
		$JLMS_DB->query();
	}
}
function JLMS_DelOp_deleteCourseAssistants( $course_id, &$cid ) {
	global $JLMS_DB, $JLMS_CONFIG;
	$JLMS_ACL = & JLMSFactory::getACL();
	$ass_roles = $JLMS_ACL->GetSystemRolesIds(5);
	if (!count($ass_roles)) {
		return false;
	}
	$ass_roles_str = implode(',',$ass_roles);
	$cids = implode(',',$cid);
	$query = "DELETE FROM #__lms_user_courses WHERE course_id = '".$course_id."' AND user_id IN ($cids) AND role_id In ($ass_roles_str)";
	$JLMS_DB->SetQuery( $query );
	$JLMS_DB->query();
	if ($JLMS_CONFIG->get('plugin_forum')) {		
		$forum = & JLMS_SMF::getInstance();		
		if ( !is_object( $forum ) ) {
			$query = "SELECT id, course_id, board_type, group_id, ID_GROUP AS id_group, ID_CAT AS id_cat, ID_BOARD AS id_board, is_active, need_update FROM #__lms_forum_details WHERE course_id = $course_id";
			$JLMS_DB->SetQuery( $query );
			$forum_det = $JLMS_DB->LoadObjectList();
			if (count($forum_det)) {
				$query = "SELECT username FROM #__users WHERE user_id IN ($cids)";
				$JLMS_DB->SetQuery( $query );
				$assist_usernames = $JLMS_DB->LoadResultArray();				
				$boards = array();
				foreach ($forum_det as $fd) {
					$boards[] = $fd->id_board;
				}				
				foreach ($assist_usernames as $au) {
					$forum->deleteModerators( $boards, $au );
				}				
			}
		}
	}
}
function JLMS_DelOp_deleteCourse( $id ) {
	global $JLMS_DB, $JLMS_CONFIG;
	$course_id = $id;
	$files_ids = array();
	@set_time_limit('3000');
	$query = "DELETE FROM #__lms_agenda WHERE course_id = '".$course_id."'";
	$JLMS_DB->SetQuery( $query );
	$JLMS_DB->query();

	$query = "DELETE FROM #__lms_attendance WHERE course_id = '".$course_id."'";
	$JLMS_DB->SetQuery( $query );
	$JLMS_DB->query();

	$query = "DELETE FROM #__lms_certificate_users WHERE course_id = '".$course_id."'";
	$JLMS_DB->SetQuery( $query );
	$JLMS_DB->query();

	$query = "SELECT distinct file_id FROM #__lms_certificates WHERE course_id = '".$course_id."'";
	$JLMS_DB->SetQuery( $query );
	$cert_files = $JLMS_DB->LoadResultArray();

	$query = "DELETE FROM #__lms_certificates WHERE course_id = '".$course_id."'";
	$JLMS_DB->SetQuery( $query );
	$JLMS_DB->query();

	$query = "DELETE FROM #__lms_chat_history WHERE course_id = '".$course_id."'";
	$JLMS_DB->SetQuery( $query );
	$JLMS_DB->query();

	$query = "DELETE FROM #__lms_chat_users WHERE course_id = '".$course_id."'";
	$JLMS_DB->SetQuery( $query );
	$JLMS_DB->query();

	$query = "DELETE FROM #__lms_conference_doc WHERE course_id = '".$course_id."'";
	$JLMS_DB->SetQuery( $query );
	$JLMS_DB->query();

	/* added 05.09.2007 by DEN */
	$query = "DELETE FROM #__lms_subscriptions_courses WHERE course_id = '".$course_id."'";
	$JLMS_DB->SetQuery( $query );
	$JLMS_DB->query();


	$doc_files = array();
	$zip_docs = array();
	$doc_ids = array();
	$query = "SELECT id, file_id, folder_flag FROM #__lms_documents WHERE course_id = '".$course_id."'";
	$JLMS_DB->SetQuery( $query );
	$docs_info = $JLMS_DB->LoadObjectList();
	foreach ($docs_info as $di) {
		if ($di->folder_flag == 2 && $di->file_id) {
			$zip_docs[] = $di->file_id;
			$doc_ids[] = $di->id;
		} elseif (!$di->folder_flag && $di->file_id) {
			$doc_files[] = $di->file_id;
			$doc_ids[] = $di->id;
		}
	}
	$doc_files = array_unique($doc_files);

	$query = "DELETE FROM #__lms_documents WHERE course_id = '".$course_id."'";
	$JLMS_DB->SetQuery( $query );
	$JLMS_DB->query();

	$query = "SELECT distinct file_id FROM #__lms_dropbox WHERE course_id = '".$course_id."'";
	$JLMS_DB->SetQuery( $query );
	$dropbox_files = $JLMS_DB->LoadResultArray();

	$query = "DELETE FROM #__lms_dropbox WHERE course_id = '".$course_id."'";
	$JLMS_DB->SetQuery( $query );
	$JLMS_DB->query();

	$files_ids = array_merge($doc_files, $dropbox_files);
	$files_ids = array_merge($files_ids, $cert_files);
	if (count($files_ids)) {
		JLMS_deleteFiles($files_ids);
	}

	$query = "DELETE FROM #__lms_forum_details WHERE course_id = '".$course_id."'";
	$JLMS_DB->SetQuery( $query );
	$JLMS_DB->query();

	$query = "DELETE FROM #__lms_gradebook WHERE course_id = '".$course_id."'";
	$JLMS_DB->SetQuery( $query );
	$JLMS_DB->query();

	$query = "DELETE FROM #__lms_gradebook_items WHERE course_id = '".$course_id."'";
	$JLMS_DB->SetQuery( $query );
	$JLMS_DB->query();

	$query = "DELETE FROM #__lms_homework WHERE course_id = '".$course_id."'";
	$JLMS_DB->SetQuery( $query );
	$JLMS_DB->query();

	$query = "DELETE FROM #__lms_homework_results WHERE course_id = '".$course_id."'";
	$JLMS_DB->SetQuery( $query );
	$JLMS_DB->query();

	$query = "DELETE FROM #__lms_learn_path_conds WHERE course_id = '".$course_id."'";
	$JLMS_DB->SetQuery( $query );
	$JLMS_DB->query();

	$query = "DELETE FROM #__lms_learn_path_grades WHERE course_id = '".$course_id."'";
	$JLMS_DB->SetQuery( $query );
	$JLMS_DB->query();

	$query = "SELECT id FROM #__lms_learn_path_results WHERE course_id = '".$course_id."'";
	$JLMS_DB->SetQuery( $query );
	$result_cid = $JLMS_DB->LoadResultArray();
	if (count($result_cid)) {
		$result_cids = implode(',', $result_cid);
		$query = "DELETE FROM #__lms_learn_path_results WHERE course_id = '".$course_id."'";
		$JLMS_DB->SetQuery( $query );
		$JLMS_DB->query();
		$query = "DELETE FROM #__lms_learn_path_step_results WHERE result_id IN ($result_cids)";
		$JLMS_DB->SetQuery( $query );
		$JLMS_DB->query();
		// 05.03.2007 (dleting of lp quiz results)
		$query = "DELETE FROM #__lms_learn_path_step_quiz_results WHERE result_id IN ($result_cids)";
		$JLMS_DB->SetQuery( $query );
		$JLMS_DB->query();
	}

	$query = "DELETE FROM #__lms_learn_path_steps WHERE course_id = '".$course_id."'";
	$JLMS_DB->SetQuery( $query );
	$JLMS_DB->query();

	$query = "SELECT id FROM #__lms_learn_paths WHERE course_id = '".$course_id."'";
	$JLMS_DB->SetQuery( $query );
	$lp_cid = $JLMS_DB->LoadResultArray();
	if (count($lp_cid)) {
		$lp_cids = implode(',', $lp_cid);
		$query = "DELETE FROM #__lms_learn_path_prerequisites WHERE lpath_id IN ($lp_cids) OR req_id IN ($lp_cids)";
		$JLMS_DB->SetQuery( $query );
		$JLMS_DB->query();
	}

	$query = "DELETE FROM #__lms_learn_paths WHERE course_id = '".$course_id."'";
	$JLMS_DB->SetQuery( $query );
	$JLMS_DB->query();

	$query = "DELETE FROM #__lms_links WHERE course_id = '".$course_id."'";
	$JLMS_DB->SetQuery( $query );
	$JLMS_DB->query();

	# !!!!!!!!! lms_payments !!!!!!!!! # ??????????????????

	// QUIZZES section
	$query = "SELECT c_id FROM #__lms_quiz_t_quiz WHERE course_id = '".$course_id."'";
	$JLMS_DB->setQuery( $query );
	$cid_quiz = $JLMS_DB->LoadResultArray();
	if (count($cid_quiz)) {
		$cids_quiz = implode(',', $cid_quiz);
		$query = "DELETE FROM #__lms_quiz_t_quiz WHERE c_id IN ( $cids_quiz )";
		$JLMS_DB->setQuery( $query );
		$JLMS_DB->query();
		$query = "SELECT c_id FROM #__lms_quiz_t_question WHERE c_quiz_id IN ( $cids_quiz ) AND course_id = '".$course_id."'";
		$JLMS_DB->SetQuery( $query );
		$cid_quest = $JLMS_DB->LoadResultArray();
		if (count($cid_quest)) {
			$cids_quest = implode(',', $cid_quest);
			$query = "DELETE FROM #__lms_quiz_t_question WHERE c_id IN ( $cids_quest )";
			$JLMS_DB->setQuery( $query );
			$JLMS_DB->query();
			$query = "DELETE FROM #__lms_quiz_t_choice WHERE c_question_id IN ( $cids_quest )";
			$JLMS_DB->setQuery( $query );
			$JLMS_DB->query();
			$query = "DELETE FROM #__lms_quiz_t_hotspot WHERE c_question_id IN ( $cids_quest )";
			$JLMS_DB->setQuery( $query );
			$JLMS_DB->query();
			$query = "DELETE FROM #__lms_quiz_t_matching WHERE c_question_id IN ( $cids_quest )";
			$JLMS_DB->setQuery( $query );
			$JLMS_DB->query();
			$query = "SELECT c_id FROM #__lms_quiz_t_blank WHERE c_question_id IN ( $cids_quest )";
			$JLMS_DB->SetQuery( $query );
			$blank_cid = $JLMS_DB->LoadResultArray();
			$query = "DELETE FROM #__lms_quiz_t_blank WHERE c_question_id IN ( $cids_quest )";
			$JLMS_DB->setQuery( $query );
			$JLMS_DB->query();
			if (count($blank_cid)) {
				$blank_cids = implode( ',', $blank_cid );
				$query = "DELETE FROM #__lms_quiz_t_text WHERE c_blank_id IN ( $blank_cids )";
				$JLMS_DB->setQuery( $query );
				$JLMS_DB->query();
			}
		}
	}

	$query = "SELECT * FROM #__lms_scorm_packages WHERE course_id = '".$course_id."'";
	$JLMS_DB->SetQuery( $query );
	$scorm_info = $JLMS_DB->LoadObjectList();
	if (count($scorm_info)) {
		$scorm_cid = array();
		foreach ($scorm_info as $si) {
			$scorm_cid[] = $si->id;
		}
		$scorm_cids = implode( ',', $scorm_cid );
		$query = "DELETE FROM #__lms_scorm_packages WHERE course_id = '".$course_id."'";
		$JLMS_DB->SetQuery( $query );
		$JLMS_DB->query();
		$query = "DELETE FROM #__lms_scorm_sco WHERE content_id IN ($scorm_cids)";
		$JLMS_DB->SetQuery( $query );
		$JLMS_DB->query();

		require_once(_JOOMLMS_FRONT_HOME . "/includes/jlms_dir_operation.php");
		$scorm_folder = $JLMS_CONFIG->getCfg('absolute_path') . "/" . _JOOMLMS_SCORM_FOLDER . "/";
		foreach ($scorm_info as $del_scorm) {
			deldir( $scorm_folder . $del_scorm->folder_srv_name . "/" );
			@unlink( $scorm_folder . $del_scorm->package_srv_name );
		}
	}

	if (count($zip_docs)) {
		$zds = implode(',', $zip_docs);
		$query = "SELECT * FROM #__lms_documents_zip WHERE id IN ($zds) AND course_id = '".$course_id."'";
		$JLMS_DB->SetQuery( $query );
		$zippacks_info = $JLMS_DB->LoadObjectList();
		if (count($zippacks_info)) {
			$zippack_cid = array();
			foreach ($zippacks_info as $zi) {
				$zippack_cid[] = $zi->id;
			}
			$zippack_cids = implode( ',', $zippack_cid );
			$query = "DELETE FROM #__lms_documents_zip WHERE id IN ($zippack_cids) AND course_id = '".$course_id."'";
			$JLMS_DB->SetQuery( $query );
			$JLMS_DB->query();
			require_once(_JOOMLMS_FRONT_HOME . "/includes/jlms_dir_operation.php");
			$zp_folder = $JLMS_CONFIG->getCfg('absolute_path') . "/" . _JOOMLMS_SCORM_FOLDER . "/";
			foreach ($zippacks_info as $del_zp) {
				deldir( $zp_folder . $del_zp->zip_folder . "/" );
				@unlink( $zp_folder . $del_zp->zip_srv_name );
			}
		}
	}

	$query = "DELETE FROM #__lms_track_chat WHERE course_id = '".$course_id."'";
	$JLMS_DB->SetQuery( $query );
	$JLMS_DB->query();

	if (count($doc_ids)) {
		$cid_docs = implode(',',$doc_ids);
		$query = "DELETE FROM #__lms_track_downloads WHERE doc_id IN ($cid_docs)";
		$JLMS_DB->SetQuery( $query );
		$JLMS_DB->query();
	}

	$query = "DELETE FROM #__lms_track_hits WHERE course_id = '".$course_id."'";
	$JLMS_DB->SetQuery( $query );
	$JLMS_DB->query();

	$query = "DELETE FROM #__lms_track_learnpath_stats WHERE course_id = '".$course_id."'";
	$JLMS_DB->SetQuery( $query );
	$JLMS_DB->query();

	$query = "DELETE FROM #__lms_usergroups WHERE course_id = '".$course_id."'";
	$JLMS_DB->SetQuery( $query );
	$JLMS_DB->query();

	$query = "DELETE FROM #__lms_users_in_groups WHERE course_id = '".$course_id."'";
	$JLMS_DB->SetQuery( $query );
	$JLMS_DB->query();

	$query = "DELETE FROM #__lms_user_courses WHERE course_id = '".$course_id."'";
	$JLMS_DB->SetQuery( $query );
	$JLMS_DB->query();

	$query = "DELETE FROM #__lms_courses WHERE id = '".$course_id."'";
	$JLMS_DB->SetQuery( $query );
	$JLMS_DB->query();

}

function JLMS_deleteFromFMS($cids, $course_id, $option, $from_archive = false ){
	global $Itemid, $my, $JLMS_DB, $JLMS_CONFIG, $JLMS_SESSION, $JLMS_LANGUAGE;

	JLMS_require_lang($JLMS_LANGUAGE, 'conference.lang', $JLMS_CONFIG->get('default_language'));
	JLMS_processLanguage( $JLMS_LANGUAGE );
	$where = '';
	if ($cids){
		$where = " id IN ($cids) AND ";
	}
	$query = "SELECT session_name FROM `#__lms_conference_records` WHERE $where course_id = '".$course_id."'";
	$JLMS_DB -> setQuery($query);
	$files = $JLMS_DB->loadObjectList();
	$i = 0;

	$files_list = '';
	foreach ($files as $file){
		$files_list .= "&amp;arg".$i."=".$file->session_name;
		$i++;
	}

	$query = "DELETE FROM #__lms_conference_records WHERE $where course_id = '".$course_id."' ";
	$JLMS_DB->SetQuery( $query );
	$JLMS_DB->query();

	if ($files_list){

		$recorded_session = mosGetParam($_REQUEST, 'recorded_session', '');
		$flashcomroot = $JLMS_CONFIG->get('flascommRoot');
		$JLMS_CONFIG->SetPageTitle('Conference | delete records');
		$master = "yes";
		?>
		<script type="text/javascript" src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/includes/js/swfobject.js"></script>
		<script type="text/javascript" language="javascript">
		<!--
		function jlms_redirect(){
			<?php if ($from_archive){?>
			top.location.href = '<?php echo sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=conference&mode=archive&id=$course_id")?>';
			<?php }else{?>
			top.location.href = '<?php echo sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=courses");?>';
			<?php }?>
		}
		//-->
		</script>
		<div class="contentheading"><?php echo _JLMS_CONFERENCE_DELETING;?></div>
		<div style="text-align:left " id="deleteRecord">
		<?php
		if ($from_archive){
			$JLMS_SESSION->set('joomlalms_sys_message', _JLMS_CONFERENCE_DELETING_INFO);
		}else{
			$JLMS_SESSION->set('joomlalms_sys_message', _JLMS_COURSE_DELETED);
		}

		$params = 'pseudo='.$my->username.'&amp;course_id='.$course_id.'&amp;flashcommRoot='.$flashcomroot.'&amp;master='.$master.$files_list;
		?>
		<script type="text/javascript">
		// <![CDATA[
		var so = new SWFObject("components/com_joomla_lms/includes/conference_playback/deleteRecord_106.swf?<?php echo $params;?>", "deleteRecord", "150", "80", "8", "#ffffff");
		so.addVariable("allowScriptAccess", "sameDomain"); // this line is optional, but this example uses the variable and displays this text inside the flash movie
		so.addVariable("wmode", "transparent");
		so.addVariable("flashvars", "hello there");
		so.addVariable("salign", "t");
		so.addVariable("menu", "false");
		so.write("deleteRecord");
		// ]]>
		</script>
		<?php
		return false;
		?></div><?php
	}else{
		if ($from_archive){
			jlmsRedirect( sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=conference&mode=archive&id=$course_id") );
		}
		return true;
	}
}

function JLMS_DelOp_deleteLpaths( $cid ) {
	global $JLMS_DB, $JLMS_CONFIG;
	$cids = implode(',',$cid);

	//patch by DEN - 10 march 2010 - ticket RQFC-2946
	$query = "DELETE FROM #__lms_gradebook_lpaths WHERE learn_path_id IN ($cids)";
	$JLMS_DB->setQuery($query);
	$JLMS_DB->query();

	$query = "SELECT id FROM #__lms_forums WHERE forum_level = 1";
	$JLMS_DB->SetQuery( $query );
	$board_types = $JLMS_DB->LoadResultArray();
	if (count($board_types)) {
		$board_types_str = implode(',',$board_types);
		$query = "SELECT id, course_id, board_type, group_id, ID_GROUP AS id_group, ID_CAT AS id_cat, ID_BOARD AS id_board, is_active, need_update FROM #__lms_forum_details WHERE board_type IN ($board_types_str) AND group_id IN ($cids)";
		$JLMS_DB->setQuery($query);
		$lpaths = $JLMS_DB->loadObjectList();
		$boards = array();
		$mem_groups = array();
		foreach ($lpaths as $lpath)	{
			$boards[] = $lpath->id_board;
			$mem_groups[] = $lpath->id_group;
		}
		$query = "DELETE FROM #__lms_forum_details WHERE board_type IN ($board_types_str) AND group_id IN ($cids)";
		$JLMS_DB->setQuery($query);
		$JLMS_DB->query();
		
		$forum = & JLMS_SMF::getInstance();
		if ( is_object( $forum ) ) {			
			$forum->deleteBoards( $boards );
			$forum->deleteTopics( $boards );
			$forum->deleteMessages( $boards );			
			$forum->deleteModerators( $boards );	
			$forum->deleteMembergroups( $mem_groups );
			$forum->deletePermissions( $mem_groups );		
		}
	}
}
?>