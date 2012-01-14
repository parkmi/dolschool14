<?php
/**
*
* JoomlaQuiz plugin fo LMS
* (c) JoomaLMS eLearning Software http://www.joomlalms.com/
**/

// no direct access
defined( '_JLMS_EXEC' ) or die( 'Restricted access' );
define( '_JLMS_QUIZ_STU_QUESTS_FROM_SELF', 'Enter number of questions:' );
define( '_JLMS_QUIZ_SELF_VERIFICATION', 'Self-verification options' );

class JLMS_quiz_front_class {

function JQ_printResults() {
	global $JLMS_DB, $my, $JLMS_CONFIG, $JLMS_LANGUAGE;
	JLMS_require_lang($JLMS_LANGUAGE, 'quiz.lang', $JLMS_CONFIG->get('default_language'));
	require(_JOOMLMS_FRONT_HOME . '/includes/quiz/quiz_language.php');
	global $jq_language;

	$stu_quiz_id = intval( mosGetParam( $_GET, 'stu_quiz_id', 0 ) );
	$user_unique_id = strval( mosGetParam( $_GET, 'user_unique_id', '') );
	$course_id = $JLMS_CONFIG->get('course_id',0);
	$query = "SELECT c_quiz_id, c_student_id, unique_id FROM #__lms_quiz_r_student_quiz WHERE c_id = '".$stu_quiz_id."'";
	$JLMS_DB->SetQuery($query);
	$st_quiz_data = $JLMS_DB->LoadObjectList();
	if (count($st_quiz_data) && $course_id) {
		$st_quiz_data = $st_quiz_data[0];
		$query = "SELECT course_id FROM #__lms_quiz_t_quiz WHERE c_id = '".$st_quiz_data->c_quiz_id."'";
		$JLMS_DB->SetQuery( $query );
		$quiz_course = $JLMS_DB->LoadResult();
		if ( ($user_unique_id == $st_quiz_data->unique_id) && $quiz_course == $course_id) {
			$i_can_view_these_results = false;
			$JLMS_ACL = & JLMSFactory::getACL();
			if (($my->id == $st_quiz_data->c_student_id)) {
				// user who passed the quiz
				$i_can_view_these_results = true;
			} elseif ($JLMS_ACL->isCourseTeacher()) {
				// course teacher
				$i_can_view_these_results = true;
			} elseif ($JLMS_ACL->isStaff() && isset($JLMS_ACL->_staff_learners) && is_array($JLMS_ACL->_staff_learners) && in_array($st_quiz_data->c_student_id, $JLMS_ACL->_staff_learners)) {
				//users CEO
				$i_can_view_these_results = true;
			}
			if ($i_can_view_these_results) {
				require_once(dirname(__FILE__)."/joomlaquiz.manageresults.php");
				$str = JQ_PrintResultForPDF($stu_quiz_id);
				rel_dofreePDF($str);
				die();
			}
		}
	}
	echo $jq_language['quiz_mes_notavail'];
}

function JQ_printCertificate() {
	global $JLMS_DB, $my, $JLMS_CONFIG;

	global $JLMS_LANGUAGE, $JLMS_CONFIG;
	JLMS_require_lang($JLMS_LANGUAGE, 'quiz.lang', $JLMS_CONFIG->get('default_language'));

	require(_JOOMLMS_FRONT_HOME . '/includes/quiz/quiz_language.php');
	global $jq_language;

	$stu_quiz_id = intval( mosGetParam( $_GET, 'stu_quiz_id', 0 ) );
	$user_unique_id = strval( mosGetParam( $_GET, 'user_unique_id', '') );
	$course_id = $JLMS_CONFIG->get('course_id',0);
	$query = "SELECT sq.c_passed, sq.c_student_id, sq.c_total_score, sq.unique_id, sq.c_date_time as completion_datetime,"
	. "\n qtq.c_full_score, qtq.c_title, qtq.c_certificate, qtq.course_id, qtq.c_id as quiz_id, qtq.course_id"
	. "\n FROM #__lms_quiz_r_student_quiz AS sq, #__lms_quiz_t_quiz AS qtq"
	. "\n WHERE sq.c_id = '".$stu_quiz_id."' and qtq.c_id = sq.c_quiz_id";
	$JLMS_DB->SetQuery( $query );
	$stu_quiz = $JLMS_DB->LoadObjectList();
	if (count($stu_quiz) && $course_id) {
		$stu_quiz = $stu_quiz[0];
		$query = "SELECT course_id FROM #__lms_quiz_t_quiz WHERE c_id = '".$stu_quiz->quiz_id."'";
		$JLMS_DB->SetQuery( $query );
		$quiz_course = $JLMS_DB->LoadResult();

		if (($user_unique_id == $stu_quiz->unique_id) && $quiz_course == $course_id ) {
			$JLMS_ACL = & JLMSFactory::getACL();
			$i_can_view_these_results = false;
			if (($my->id == $stu_quiz->c_student_id)) {
				// user who passed the quiz
				$i_can_view_these_results = true;
			} elseif ($JLMS_ACL->isCourseTeacher()) {
				// course teacher
				$i_can_view_these_results = true;
			} elseif ($JLMS_ACL->isStaff() && isset($JLMS_ACL->_staff_learners) && is_array($JLMS_ACL->_staff_learners) && in_array($stu_quiz->c_student_id, $JLMS_ACL->_staff_learners)) {
				//users CEO
				$i_can_view_these_results = true;
			}

			if ( $i_can_view_these_results ) {
				$user_id = $stu_quiz->c_student_id;
				if ($stu_quiz->c_passed != 1) {
					echo $jq_language['quiz_mes_notpassed']; die();
				}
				if (!$stu_quiz->c_certificate) {
					echo $jq_language['quiz_mes_notavail']; die();
				}

				require_once(_JOOMLMS_FRONT_HOME . "/includes/lms_certificates.php");
				$query = "SELECT * FROM #__users WHERE id = '".$user_id."'";
				$JLMS_DB->SetQuery( $query );
				$u_data = $JLMS_DB->LoadObjectList();
				$tm_obj = new stdClass();
				$tm_obj->username = isset($u_data[0]->username)?$u_data[0]->username:'';
				$tm_obj->name = isset($u_data[0]->name)?$u_data[0]->name:'';
				$tm_obj->crtf_spec_answer = '';
				$course_id = $stu_quiz->course_id;

				$tm_obj->is_preview = false;
				$tm_obj->quiz_id = $stu_quiz->quiz_id;
				$tm_obj->quiz_name = $stu_quiz->c_title;
				$tm_obj->stu_quiz_id = $stu_quiz_id;
				$tm_obj->crtf_date = strtotime($stu_quiz->completion_datetime);
				$user = new stdClass();
				$user->id = isset($u_data[0]->id) ? $u_data[0]->id : 0;
				$user->username = isset($u_data[0]->username) ? $u_data[0]->username : '';
				$user->name = isset($u_data[0]->name) ? $u_data[0]->name : '';
				$user->email = isset($u_data[0]->email) ? $u_data[0]->email : '';
				JLMS_Certificates::JLMS_outputCertificate( $stu_quiz->c_certificate, $stu_quiz->course_id, $tm_obj, $user );
			}
		}
	}
	echo $jq_language['quiz_mes_notavail'];
}
function JQ_doQuiz( $option, $quiz_id, $course_id ) {
	global  $Itemid, $JLMS_DB, $my, $JLMS_LANGUAGE, $JLMS_CONFIG;

	JLMS_require_lang($JLMS_LANGUAGE, 'quiz.lang', $JLMS_CONFIG->get('default_language'));
	require(_JOOMLMS_FRONT_HOME . '/includes/quiz/quiz_language.php');
	global $jq_language;

	$JLMS_ACL = & JLMSFactory::getACL();

	$query = "SELECT a.*, 'joomlaquiz_lms_template' as template_name FROM #__lms_quiz_t_quiz as a WHERE a.c_id = ".$quiz_id." and a.course_id = ".$course_id;
	$JLMS_DB->SetQuery($query);
	$quiz_params = $JLMS_DB->LoadObjectList();

	if($quiz_params[0]->c_resume) {

		$query = "SELECT c_id, unique_id, c_total_time, c_passed FROM #__lms_quiz_r_student_quiz WHERE c_quiz_id = ".$quiz_id." AND c_student_id = '".$my->id."' ORDER BY `c_id` DESC LIMIT 1";
		$JLMS_DB->SetQuery($query);
		$resume_quiz = $JLMS_DB->LoadObject();

		if(isset($resume_quiz->c_id) && $resume_quiz->c_passed == 0 && !$resume_quiz->c_total_time) {
			$query = "SELECT c_question_id FROM #__lms_quiz_r_student_question WHERE c_stu_quiz_id = ".$resume_quiz->c_id." ORDER BY c_id desc LIMIT 1";
			$JLMS_DB->SetQuery($query);
			$last_question = $JLMS_DB->LoadResult();

			if(!$last_question)
				$last_question = -1;

			$quiz_params[0]->resume_quiz = $resume_quiz->c_id;
			$quiz_params[0]->unique_id = $resume_quiz->unique_id;
			$quiz_params[0]->last_question = $last_question;
			$quiz_params[0]->last_question = $last_question;
			$quiz_params[0]->c_total_time = $resume_quiz->c_total_time;
		}
	}

	$query = "SELECT count(c_id) FROM #__lms_quiz_r_student_quiz WHERE c_quiz_id = ".$quiz_id." AND c_student_id = '".$my->id."'";
	$JLMS_DB->SetQuery($query);
	$count = $JLMS_DB->LoadResult();

	$quiz_params[0]->attempts_of_this_quiz = $count;

	$quiz_params[0]->attempts_of_this_quiz = intval($quiz_params[0]->attempts_of_this_quiz);
	$quiz_params[0]->c_max_numb_attempts = intval($quiz_params[0]->c_max_numb_attempts);

	$doing_quiz = 1;
	if ((count($quiz_params) == 1) && $quiz_params[0]->published == 1) {
		$q_skin = $quiz_params[0]->c_skin;
		$q_random = $quiz_params[0]->c_random;
		$q_time_limit = ($quiz_params[0]->c_min_after)*60;
		$q_allow_guest = $quiz_params[0]->c_guest;
		if ($my->id) {
			$q_user_id = $my->id;
			if ($q_time_limit) {
//				$query = "SELECT max(c_date_time) as time_last_access FROM #__lms_quiz_r_student_quiz WHERE c_quiz_id = '".$quiz_id."' and c_student_id = '".$q_user_id."'";
				$query = "SELECT MAX(FROM_UNIXTIME(UNIX_TIMESTAMP(c_date_time) + c_total_time)) as time_last_access"
				. "\n FROM #__lms_quiz_r_student_quiz"
				. "\n WHERE 1"
				. "\n AND c_quiz_id = '".$quiz_id."'"
				. "\n AND c_student_id = '".$q_user_id."'"
				. "\n ORDER BY (UNIX_TIMESTAMP(c_date_time) + c_total_time)"
				;
				$JLMS_DB->SetQuery( $query );
				$q_last_access = $JLMS_DB->LoadResult();
				if ($q_last_access) { //'STRTOTIME with an empty parameter' bug fixed (02.10.2006)
					$q_last_access_t = strtotime($q_last_access);
					$time_goes = time() - date('Z') - $q_last_access_t;
					if ($time_goes > 1) { //esli proshla 1 minuta (changed to 10 secs)
						if ($time_goes < $q_time_limit) {
							if(isset($quiz_params[0]->resume_quiz) && $quiz_params[0]->resume_quiz && !$quiz_params[0]->c_total_time) {
								//resume is possible and latest attempt is nor finished (c_total_time == 0)
								// we need to restrict 'start button' here.... only resume is possible !!!!
								$quiz_params[0]->attempts_of_this_quiz = $quiz_params[0]->c_max_numb_attempts;
							} elseif( ($quiz_params[0]->attempts_of_this_quiz >= $quiz_params[0]->c_max_numb_attempts) && (!isset($quiz_params[0]->resume_quiz) && !isset($quiz_params[0]->last_question)) && $quiz_params[0]->c_max_numb_attempts > 0) {
								// all attempast are finished.... user will scree 'final quiz page' screen.... there is no necessity in delay here
							} else {
								$message = str_replace("{text}", (($q_time_limit - $time_goes) >60)?(floor(($q_time_limit - $time_goes)/60).' minute(s)'):(($q_time_limit - $time_goes). ' seconds'), $jq_language['quiz_comeback_later']);
								echo '<div class="joomlalms_sys_message">'.$message.'</div>';
								$doing_quiz = -2;
							}
						}
					}
				}
				if ($doing_quiz == 1) {
					/*$query = "INSERT INTO #__quiz_r_student_quiz SET c_quiz_id = '".$quiz_id."', c_student_id = '".$q_user_id."', c_date_time = now()";
					$JLMS_DB->SetQuery($query);
					$JLMS_DB->query();
					$sid = $JLMS_DB->insertid();*/
				}
			}
		} else { //if guest
			$doing_quiz = -3;
			if($q_allow_guest == 1) {
				//$sid = '0';
				$doing_quiz = 1;
			} else {
				echo '<p align="left">'.$jq_language['quiz_reg_only'].'</p>';
			}
		}
		if (!$q_random) { $q_random = 0; }

	} elseif ($JLMS_ACL->CheckPermissions('quizzes', 'view_all') && (count($quiz_params) == 1)) {

	} else { $doing_quiz = -1; }

	// 30.01.08 Max Self-verification
	$lists = array();
	$query = "SELECT a.*, b.items_number FROM #__lms_quiz_t_category as a LEFT JOIN #__lms_quiz_t_quiz_pool as b"
	. "\n ON a.c_id = b.qcat_id AND b.quiz_id = $quiz_id"
	. "\n WHERE a.course_id = '".$course_id."' AND a.is_quiz_cat = 0 order by a.c_category";
	$JLMS_DB->setQuery( $query );
	$pool_cats = $JLMS_DB->loadObjectList();
	$lists['jq_pool_categories'] = $pool_cats;

	$query= "SELECT items_number FROM #__lms_quiz_t_quiz_pool WHERE quiz_id = $quiz_id AND qcat_id = 0";
	$JLMS_DB->setQuery( $query );
	$pool_quest_num = intval($JLMS_DB->loadResult());
	$lists['pool_quest_num'] = $pool_quest_num;

	$query = "SELECT count(*) FROM #__lms_quiz_t_quiz_pool WHERE quiz_id = $quiz_id AND qcat_id <> 0";
	$JLMS_DB->setQuery( $query );
	$pool_is_quest_mode = intval($JLMS_DB->loadResult());
	$lists['pool_quest_mode'] = $pool_is_quest_mode ? false : true;
	if (!$lists['pool_quest_mode']) {
		$lists['pool_quest_num'] = 0;
	}
	
	$self_verification = '';
	$quiz_params_self = new JLMSParameters($quiz_params[0]->params);

	if($quiz_params_self->get('sh_self_verification', 0) == 1){
		$pool_quest_mode = $lists['pool_quest_mode']?1:0;
		$pool_quest_mode2 = !$lists['pool_quest_mode']?2:0;
		$pool_quest_num = $lists['pool_quest_num'] ? $lists['pool_quest_num'] : 0;

		$self_verification = "<fieldset style='border: 1px solid #dddddd;'><legend align='left'>"._JLMS_QUIZ_STU_QUESTS_FROM_SELF."</legend>";
		$self_verification .= "<div style='padding: 5px;'><table width='100%' cellpadding='0' cellspacing='0' border='0'><tr>"; 
		$self_verification .="<td>";
		$self_verification .="<table cellpadding='0' cellspacing='0' border='0' width='100%'>";
		if($pool_quest_mode){
			$self_verification .="<tr class='sectiontableentry1'>";
			$self_verification .="<td><input type='hidden' id='pool_quest_type' name='c_pool_type' value='".$pool_quest_mode."'><input type='text' name='pool_qtype_number' size='3' value='".$pool_quest_num."' />";
			$self_verification .="</td>";
			$self_verification .="</tr>";
		}
		if($pool_quest_mode2){
			$self_verification .="<tr class='sectiontableentry1'>";
			$self_verification .="<td colspan='2' align='left' style='text-align:left'>";
			$self_verification .="<input type='hidden' id='pool_cat_type' name='c_pool_type' value='".$pool_quest_mode2."' />"; //<label for='pool_cat_type'>"._JLMS_QUIZ_ADD_POOL_MODE_CAT.":</label>
			$self_verification .="</td>";
			$self_verification .="</tr>";

			$k = 1;
			for ($i=0, $n=count($lists['jq_pool_categories']); $i < $n; $i++) {
				$plc = $lists['jq_pool_categories'][$i];
				$self_verification .=  "<tr class='sectiontableentry$k'>";
				$self_verification .=  "<td width='30%' align='left'>".$plc->c_category."</td>";
				$self_verification .=  "<td>";
				$self_verification .=  "<input type='hidden' name='pool_cat_id[]' value='".$plc->c_id."' />";
				$self_verification .=  "<input type='text' name='pool_cat_number[]' size='3' value='".($plc->items_number?$plc->items_number:0)."' />";
				$self_verification .=  "</td></tr>";
			}
		}
		$self_verification .= "</table>";
		$self_verification .= "</td>";
		$self_verification .= "</tr>";
		$self_verification .= "</table></div>";
		$self_verification .= "</fieldset>";
	}

	if ($doing_quiz == 1) {

		// !!! 14 June 2007 - we should always load D'n'D code - beacuase D'n'D questions could became randomly from the pool.

		$quiz_params[0]->if_dragdrop_exist = true;

		if( ($quiz_params[0]->attempts_of_this_quiz >= $quiz_params[0]->c_max_numb_attempts) && (!isset($quiz_params[0]->resume_quiz) && !isset($quiz_params[0]->last_question)) && $quiz_params[0]->c_max_numb_attempts > 0) {

			$query = "SELECT a.c_id, a.unique_id, b.course_id FROM #__lms_quiz_r_student_quiz AS a, #__lms_quiz_t_quiz AS b WHERE a.c_quiz_id = b.c_id AND a.c_quiz_id = ".$quiz_id." AND a.c_student_id = ".$my->id." ORDER BY a.c_id desc LIMIT 1";
			$JLMS_DB->SetQuery($query);
			$resume_quiz1 = $JLMS_DB->LoadObject();

			$quiz_params[0]->stu_quiz_id = $resume_quiz1->c_id;
			$quiz_params[0]->user_uniquie_id = $resume_quiz1->unique_id;
			$quiz_params[0]->course_id = $resume_quiz1->course_id;
			echo JLMS_quiz_front_class::JQ_FinishQuiz($quiz_params[0]->c_id, $quiz_params[0]->stu_quiz_id, $quiz_params[0]->user_uniquie_id, $quiz_params[0]->course_id );
		}
		else {
			JLMS_quiz_front_html_class::JQ_ShowQuiz( $option, $course_id, $quiz_params[0], $jq_language, $self_verification);
		}
		//JLMS_quiz_front_html_class::JQ_ShowQuiz( $option, $course_id, $quiz_params[0], $jq_language, $self_verification);
	} elseif ($doing_quiz == -1) {
		echo '<div class="joomlalms_sys_message">'.$jq_language['quiz_not_available'].'</div>';
	}
}
//administrator preview function
function JQ_previewQuestion( $option, $course_id, $is_pool = false ) {
	global  $JLMS_DB;

	global $JLMS_LANGUAGE, $JLMS_CONFIG;
	JLMS_require_lang($JLMS_LANGUAGE, 'quiz.lang', $JLMS_CONFIG->get('default_language'));

	require(_JOOMLMS_FRONT_HOME . '/includes/quiz/quiz_language.php');
	global $jq_language;
	
	$self_verification = '';

	$quest_id = intval( mosGetParam($_REQUEST, 'c_id', 0));
	$preview_id = '111';//strval( mosGetParam($_REQUEST, 'preview_id', ''));
	//$query = "SELECT c_par_value FROM #__quiz_setup WHERE c_par_name = 'admin_preview'";
	//$JLMS_DB->SetQuery( $query );
	$preview_code = '111';//$JLMS_DB->LoadResult();
	if ($quest_id && ($preview_id == $preview_code)) {
		$query = "SELECT c_quiz_id FROM #__lms_quiz_t_question WHERE c_id = '".$quest_id."'";
		$JLMS_DB->SetQuery( $query );
		$quiz_id = $JLMS_DB->LoadResult();
		if ($quiz_id) {
			$query = "SELECT a.*, 'joomlaquiz_lms_template' as template_name FROM #__lms_quiz_t_quiz as a WHERE a.c_id = ".$quiz_id;
			$JLMS_DB->SetQuery($query);
			$quiz_params = $JLMS_DB->LoadObjectList();
			if (count($quiz_params)) {
				$query = "SELECT count(*) FROM #__lms_quiz_t_question WHERE c_id = '".$quest_id."' AND (c_type = 4 OR c_type = 11)";
				$JLMS_DB->SetQuery( $query );
				$quiz_params[0]->if_dragdrop_exist = $JLMS_DB->LoadResult();
				JLMS_quiz_front_html_class::JQ_ShowQuiz($option, $course_id, $quiz_params[0], $jq_language, $self_verification, true, $quest_id, $preview_id);
			} else {
				echo '<div class="joomlalms_sys_message">'.$jq_language['quiz_not_available'].'<br />(Error code: 0001 - Template for quiz not found.)</div>';
			}
		} elseif ($is_pool) {
			$quiz_params = new stdClass();
			$quiz_params->if_dragdrop_exist = true;
			$quiz_params->c_id = 0;
			$quiz_params->c_time_limit = 0;
			$quiz_params->c_title = '';
			$quiz_params->c_slide = 0;
			$quiz_params->c_email_to = 0;
			JLMS_quiz_front_html_class::JQ_ShowQuiz($option, $course_id, $quiz_params, $jq_language, $self_verification, true, $quest_id, $preview_id);
		} else {
			echo '<div class="joomlalms_sys_message">'.$jq_language['quiz_not_available'].'<br />(Error code: 0002 - Quiz not found.)</div>';
		}
	} else {
		echo '<div class="joomlalms_sys_message">'.$jq_language['quiz_not_available'].'<br />(Error code: 0003 - You have no permissions to preview this question.)</divp>';
	}
}


function JQ_FinishQuiz($quiz_id, $stu_quiz_id, $user_unique_id, $id ) {
	global $JLMS_DB, $my, $Itemid, $JLMS_CONFIG;
	$ret_str = '';
	
	require_once(dirname(__FILE__) .'/ajax_quiz.class.php');
	
	$query = "SELECT * FROM #__lms_quiz_t_quiz WHERE c_id = '".$quiz_id."'";
	$JLMS_DB->SetQuery ($query );
	$quiz = $JLMS_DB->LoadObjectList();
	if (count($quiz)) {
		$quiz = $quiz[0];
	} else { return $ret_str; }
	$quiz_params = new JLMSParameters($quiz->params);
	$inside_lp = intval( mosGetParam( $_REQUEST, 'inside_lp', 0 ) );
	$QA = new JLMS_quiz_API($quiz_id, $inside_lp);
	if (!$QA->quiz_valid()) {
		return '';
	}
	$toolbar_no_a = $QA->quiz_Get_NoAtToolbar();
	
	$QA->set('stu_quiz_id', $stu_quiz_id);
	$QA->set('user_unique_id', $user_unique_id);
	$QA->quiz_ProcessStartData();
	
	if ( $QA->start_valid() && $quiz_id ) {

		$query = "SELECT SUM(c_score) FROM #__lms_quiz_r_student_question WHERE c_stu_quiz_id = '".$stu_quiz_id."'";
		$JLMS_DB->SetQuery( $query );
		$user_score = $JLMS_DB->LoadResult();
		if (!$user_score) $user_score = 0;

		$max_score = $QA->quiz_Get_MaxScore();

		$nugno_score = ($QA->get_qvar('c_passing_score', 0) * $max_score) / 100;

		$user_passed = 0;
		if ($user_score >= $nugno_score) { $user_passed = 1; }

		$user_time = 0;
		$quiz_time1 = time() - date('Z');
		$query = "SELECT c_date_time FROM #__lms_quiz_r_student_quiz WHERE c_id = '".$stu_quiz_id."'";
		$JLMS_DB->SetQuery( $query );
		$quiz_time2 = $JLMS_DB->LoadResult();
		$quiz_time2a = strtotime($quiz_time2);
		$user_time = $quiz_time1 - $quiz_time2a;

		$query = "SELECT c_total_score, c_passed, c_total_time FROM #__lms_quiz_r_student_quiz WHERE c_id = '".$stu_quiz_id."' and c_quiz_id = '".$quiz_id."' and c_student_id = '".$my->id."'";
		$JLMS_DB->SetQuery( $query );
		$user_quiz_results_obj = $JLMS_DB->LoadObject();
		if (is_object($user_quiz_results_obj)) {
			$user_score = $user_quiz_results_obj->c_total_score;
			$user_passed = $user_quiz_results_obj->c_passed;
			$user_time = $user_quiz_results_obj->c_total_time;
		}
		// update lms results
		$lms_course = $QA->get_qvar('course_id', 0);
		$lms_quiz = $quiz_id;
		$lms_user = $my->id;
		$lms_score = $user_score;
		$lms_time = $user_time;
		$lms_date = date( 'Y-m-d H:i:s', time() - date('Z') );//the same as gmdate
		$lms_passed = $user_passed;
		global $JLMS_CONFIG;
		if ($lms_course && $JLMS_CONFIG->get('course_id') == $lms_course) {
			$course_params = $JLMS_CONFIG->get('course_params');
			$params = new JLMSParameters($course_params);
			$do_insert_new_res = false;
			if ($params->get('track_type', 0) == 1) {
				$query = "SELECT * FROM #__lms_quiz_results WHERE course_id = '".$lms_course."' AND quiz_id = '".$lms_quiz."' AND user_id = '".$lms_user."'";
				$JLMS_DB->SetQuery( $query );
				$old_user_results = $JLMS_DB->LoadObject();
				if (is_object($old_user_results)) {
					if (!$lms_passed && !$old_user_results->user_passed &&  $lms_score > $old_user_results->user_score) {
						$do_insert_new_res = true;
					} elseif ($lms_passed && !$old_user_results->user_passed) {
						$do_insert_new_res = true;
					} elseif ($lms_passed && $old_user_results->user_passed && $lms_score > $old_user_results->user_score) {
						$do_insert_new_res = true;
					} elseif ($lms_passed && $old_user_results->user_passed && $lms_score == $old_user_results->user_score && $lms_time < $old_user_results->user_time ) {
						$do_insert_new_res = true;
					}
				} else {
					$do_insert_new_res = true;
				}
			} else {
				$do_insert_new_res = true;
			}
			if ($do_insert_new_res) {
				$query = "DELETE FROM #__lms_quiz_results WHERE course_id = '".$lms_course."' AND quiz_id = '".$lms_quiz."' AND user_id = '".$lms_user."'";
				$JLMS_DB->SetQuery( $query );
				$JLMS_DB->query();
				$query = "INSERT INTO #__lms_quiz_results (course_id, quiz_id, user_id, user_score, quiz_max_score, user_time, quiz_date, user_passed)"
				. "\n VALUES ('".$lms_course."', '".$lms_quiz."', '".$lms_user."', '".$lms_score."', ".intval($max_score).", '".$lms_time."', '".$lms_date."', '".$lms_passed."')";
				$JLMS_DB->SetQuery( $query );
				$JLMS_DB->query();
			}
		}
		// end of lms results section

		$cur_tmpl = 'joomlaquiz_lms_template';
		if ($cur_tmpl) {

			require_once(dirname(__FILE__) .'/templates/'.$cur_tmpl.'/jq_template.php');
			global $JLMS_LANGUAGE, $JLMS_CONFIG;
			JLMS_require_lang($JLMS_LANGUAGE, 'quiz.lang', $JLMS_CONFIG->get('default_language'));
			require(_JOOMLMS_FRONT_HOME . '/includes/quiz/quiz_language.php');
			global $jq_language;

			#$ret_str .= "\t" . '<task>results</task>' . "\n";
			$eee = $jq_language['quiz_header_fin_message'];
			#$ret_str .= "\t" . '<finish_msg><![CDATA[';
			if ($user_passed) {
				if ($QA->get_qvar('c_pass_message', '')) {
					$jq_language['quiz_user_passes'] = nl2br($QA->get_qvar('c_pass_message', ''));
				}
			} else {
				if ($QA->get_qvar('c_unpass_message', '')) {
					$jq_language['quiz_user_fails'] = nl2br($QA->get_qvar('c_unpass_message', ''));
				}
			}
			
			#$ret_str .= ']]></finish_msg>' . "\n";
			$t_ar = array();
			$t_ar[] = mosHTML::makeOption($user_score." of ".$max_score, $jq_language['quiz_res_mes_score']);
			$t_ar[] = mosHTML::makeOption(($nugno_score?($nugno_score." (".$QA->get_qvar('c_passing_score', 0)."%)"):''), $jq_language['quiz_res_mes_pas_score']);
			$tot_hour = floor($user_time / 3600);
			if ($tot_hour) {
				$tot_min = floor( ($user_time - $tot_hour*3600) / 60);
				$tot_sec = $user_time - $tot_hour*3600 - $tot_min*60;
				$tot_time = str_pad($tot_hour,2, "0", STR_PAD_LEFT).":".str_pad($tot_min,2, "0", STR_PAD_LEFT).":".str_pad($tot_sec,2, "0", STR_PAD_LEFT);
			} else {
				$tot_min = floor($user_time / 60);
				$tot_sec = $user_time - $tot_min*60;
				$tot_time = str_pad($tot_min,2, "0", STR_PAD_LEFT).":".str_pad($tot_sec,2, "0", STR_PAD_LEFT);
			}
			$t_ar[] = mosHTML::makeOption($tot_time, $jq_language['quiz_res_mes_time']);
			
			if($quiz_params->get('sh_final_page_text', 1) == 1){
				$results_txt = JoomlaQuiz_template_class::JQ_show_results($jq_language['quiz_header_fin_results'], $t_ar);
			} else {
				$results_txt = '';	
			}
			
			$footer_ar = array();
			$footer_ar[] = mosHTML::makeOption(0,$jq_language['quiz_fin_btn_review']);
			$footer_ar[] = mosHTML::makeOption(1,$jq_language['quiz_fin_btn_print']);
			$footer_ar[] = mosHTML::makeOption(2,$jq_language['quiz_fin_btn_certificate']);
			$footer_ar[] = mosHTML::makeOption(3,$jq_language['quiz_fin_btn_email']);
			$toolbar_fotter = array();
			if ($QA->get_qvar('c_certificate', 0) && $user_passed) {
				$link_inside_1 = ampReplace($JLMS_CONFIG->get('live_site').'/index.php?tmpl=component&option=com_joomla_lms&Itemid='.$Itemid.'&no_html=1&task=print_quiz_cert&course_id='.$lms_course.'&stu_quiz_id='.$stu_quiz_id.'&user_unique_id='.$user_unique_id);
				$btn_certificate = 'window.open(\''.$link_inside_1.'\',\'blank\');';
				$footer_ar[2]->text = "<div class='back_button'><a href='javascript:void(0)' onclick=\"window.open ('".$JLMS_CONFIG->get('live_site')."/index.php?tmpl=component&option=com_joomla_lms&Itemid=".$Itemid."&no_html=1&task=print_quiz_cert&course_id=".$lms_course."&stu_quiz_id=".$stu_quiz_id."&user_unique_id=".$user_unique_id."','blank');\">".$jq_language['quiz_fin_btn_certificate']."</a></div>";
				$toolbar_footer[2] = array('btn_type'=>'certificate_fbar', 'btn_js'=>$btn_certificate);
			}
			if ($QA->get_qvar('c_enable_print', 0)) {
				$link_inside_2 = ampReplace($JLMS_CONFIG->get('live_site').'/index.php?tmpl=component&option=com_joomla_lms&Itemid='.$Itemid.'&no_html=1&task=print_quiz_result&course_id='.$lms_course.'&stu_quiz_id='.$stu_quiz_id.'&user_unique_id=');
				$btn_print = 'window.open(\''.$link_inside_2.'\'+user_unique_id,\'blank\');';
				$footer_ar[1]->text = "<div class='back_button'><a href='javascript:void(0)' onclick=\"window.open ('".$JLMS_CONFIG->get('live_site')."/index.php?tmpl=component&option=com_joomla_lms&Itemid=".$Itemid."&no_html=1&task=print_quiz_result&course_id=".$lms_course."&stu_quiz_id=".$stu_quiz_id."&user_unique_id=' + user_unique_id,'blank');\">".$jq_language['quiz_fin_btn_print']."</a></div>";
				$toolbar_footer[1] = array('btn_type'=>'print_fbar', 'btn_js'=>$btn_print);
			}
			if ($QA->get_qvar('c_email_to', 0)) {
				$btn_email_to = 'jq_emailResults();';
				$footer_ar[3]->text = "<div class='back_button'><a href='javascript:void(0)' onclick=\"jq_emailResults();\">".$jq_language['quiz_fin_btn_email']."</a></div>";
				$toolbar_footer[3] = array('btn_type'=>'email_to_fbar', 'btn_js'=>$btn_email_to);				
			}
			if ($QA->get_qvar('c_enable_review', 0)) {
				$btn_review = 'jq_startReview();';
				$query = "UPDATE #__lms_quiz_r_student_quiz SET allow_review = 1 WHERE c_id = '".$stu_quiz_id."' and c_quiz_id = '".$quiz_id."' and c_student_id = '".$my->id."'";
				$JLMS_DB->SetQuery( $query );
				$JLMS_DB->query();
				$footer_ar[0]->text = "<div class='back_button'><a href='javascript:void(0)' onclick=\"jq_startReview();\">".$jq_language['quiz_fin_btn_review']."</a></div>";
				$toolbar_footer[0] = array('btn_type'=>'review_fbar', 'btn_js'=>$btn_review);
			}

			$footer_html_graf = '';
			if($quiz_params->get('sh_final_page_grafic', 0) == 1)
			{
				////----barss----////
				$is_pool = 0;

				if($quiz_id == -1 || $quiz_id == 0) {$is_pool = 1; $quiz_id = 0;}
			
				$rows = $QA->quiz_Get_QuestionList();

				// 18 August 2007 - changes (DEN) - added check for GD and FreeType support
					$generate_images = true;
					$msg = '';
					if (!function_exists('imageftbbox') || !function_exists('imagecreatetruecolor')) {
						$generate_images = false;
						$sec = false;
						if (!function_exists('imagecreatetruecolor')) {
							$msg = 'This function requires GD 2.0.1 or later (2.0.28 or later is recommended).';
							$sec = true;
						}
						if (!function_exists('imageftbbox')) {
							$msg .= ($sec?'<br />':'').'This function is only available if PHP is compiled with freetype support.';
						}
					} // end of GD and FreeType support check
				if ($JLMS_CONFIG->get('temp_folder', '') && $generate_images) { // temp folder setup is ready.
				
			//--------- array of bar-images
				$img_arr = array();
				$title_arr = array();
				$count_graph =array();
				global $option;
				for($i=0,$n=count($rows);$i<$n;$i++){
					$row = $rows[$i];
					$quest_params = new JLMSParameters($row->params);
					$z = 1;
					if (isset($row->c_pool) && $row->c_pool) {
						$row->c_pool_id = $row->c_pool;
					} else {
						$row->c_pool_id = $row->c_id;
					}
					$show_case = true;
//					if($showtype_id && !$quest_params->get('survey_question'))
					if(false && !$quest_params->get('survey_question')){
						$show_case = false;
					}
					if($show_case){
						require_once(_JOOMLMS_FRONT_HOME . "/includes/libraries/lms.lib.graph.php");
						$group_id = 0;
						$str_user_in_groups = '';
						$c_question_id = $row->c_pool_id;
						$obj_GraphStat = JLMS_GraphStatistics($option, $id, $quiz_id, $i, $z, $row, $c_question_id, $group_id, $str_user_in_groups);
							
						foreach($obj_GraphStat as $key=>$item){
							if(preg_match_all('#([a-z]+)_(\w+)#', $key, $out, PREG_PATTERN_ORDER)){
								if($out[1][0] == 'img'){
									$img_arr[$i]->$out[2][0] = $item;	
								} else 
								if($out[1][0] == 'title'){
									$title_arr[$i]->$out[2][0] = $item;	
								} else 
								if($out[1][0] == 'count'){
									$count_graph[$i]->$out[2][0] = $item;	
								}
							}	
						}
					}
				}
				}
				$footer_html_graf = JoomlaQuiz_template_class::JQ_show_results_footer_content_bars($img_arr, $title_arr, $count_graph, $id);
			}
			
			$ret_str .= $results_txt;
			// this filed shouldn't be a null - null caused errors in Safari
			if($quiz_params->get('sh_final_page_fdbck', 1) == 1){
				$ret_str .= '<br />';
				$ret_str .= JoomlaQuiz_template_class::JQ_show_results_msg($eee, ($user_passed?$jq_language['quiz_user_passes']:$jq_language['quiz_user_fails']), $user_passed);
				$ret_str .= '<br />';
			} else {
				$ret_str .= '<br />';	
			}

			if(isset($toolbar_footer) && count($toolbar_footer) > 0) {
				ksort($toolbar_footer);
				$footer_html = JLMS_ShowToolbar($toolbar_footer, false, 'center');
				$footer_html = str_replace('"components/com_joomla_lms','"'.$JLMS_CONFIG->get('live_site').'/components/com_joomla_lms',$footer_html);
			} else {
				$footer_html = '';
			}
			$entire_footer_data = $footer_html . ($footer_html_graf ? ('<br />'.$footer_html_graf) : '');
			
			$ret_str .=($entire_footer_data ? $entire_footer_data : ' ');
			
		}
	}
	return $ret_str;
}

}
?>