<?php
/**
* includes/lms_grades.lib.php
* (c) JoomaLMS eLearning Software http://www.joomlalms.com/
**/

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

/**
 * Get native LPaths results results
 *
 * @param unknown_type $user_ids
 * @param unknown_type $lp_ids
 * @param int $ttype - tracking type (0 - by the last attempt; 1 - by the best attempt
 * @return list of objects {id: lpath_id; user_status: 1 or 0; start_time: time of start; end_time: time of end}
 */
// (15 August 2007 - DEN) !!!!!!!!!!!!!!!!!! very strange work with array of user_ids !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
// don't use this function for array of user_ids with more then one element (user_id)
function JLMS_Get_LP_userResults( &$user_ids, &$lp_ids, $course_id, $ttype = 0 ) {
	global $JLMS_DB;
	$lp_str = implode(',',$lp_ids);
	$uids_str = implode(',',$user_ids);
	$lp_res_pre = array();
	$query = "SELECT lpath_id, user_status, start_time, end_time, user_id FROM #__lms_learn_path_results WHERE course_id = $course_id AND lpath_id IN ( $lp_str ) AND user_id IN ( $uids_str )"
	. "\n ORDER BY user_id, lpath_id";
	$JLMS_DB->SetQuery( $query );
	$lp_res_pre = $JLMS_DB->LoadObjectList();
	
	// user_status; start_time; end_time
	if ($ttype == 1) {
		$query = "SELECT lpath_id, user_status, start_time, end_time FROM #__lms_learn_path_grades WHERE course_id = $course_id AND lpath_id IN ( $lp_str ) AND user_id IN ( $uids_str )"
		. "\n ORDER BY user_id, lpath_id";
		$JLMS_DB->SetQuery( $query );
		$lp_res_pre2 = $JLMS_DB->LoadObjectList();
		
		foreach ($lp_res_pre2 as $lpr2) {
			$h = 0;
			while ($h < count($lp_res_pre)) {
				if ($lpr2->lpath_id == $lp_res_pre[$h]->lpath_id) {
					$do_update = false;
					if ($lpr2->user_status > $lp_res_pre[$h]->user_status) {
						$do_update = true;
					} elseif ($lpr2->user_status == $lp_res_pre[$h]->user_status && $lp_res_pre[$h]->end_time != '000-00-00 00:00:00'  && $lpr2->end_time != '000-00-00 00:00:00') {
						$f_time = strtotime($lp_res_pre[$h]->end_time) - strtotime($lp_res_pre[$h]->start_time);
						$s_time = strtotime($lpr2->end_time) - strtotime($lpr2->start_time);
						if ($s_time < $f_time) {
							$do_update = true;
						}
					}
					if ($do_update) {
						$lp_res_pre[$h]->user_status = $lpr2->user_status;
						$lp_res_pre[$h]->start_time = $lpr2->start_time;
						$lp_res_pre[$h]->end_time = $lpr2->end_time;
					}
					break;
				}
				$h ++;
			}
		}
	}
	
	return $lp_res_pre;
}

// (15 August 2007 - DEN) !!!!!!!!!!!!!!!!!! very strange work with array of user_ids !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
// don't use this function for array of user_ids with more then one element (user_id)
function JLMS_LP_populate_results($course_id, &$lpaths, &$user_ids) {
	global $JLMS_CONFIG;
	$course_params = $JLMS_CONFIG->get('course_params');
	if(!$course_params){
		$db = & JFactory::getDBO();
		$query = "SELECT params FROM #__lms_courses WHERE id = '".$course_id."'";
		$db->setQuery($query);
		$course_params = $db->loadResult();
	}
	$params = new JLMSParameters($course_params);

	$sc_ids = array();
	$scn_ids = array();
	$lp_ids = array();
	foreach ($lpaths as $ls) {
		if ($ls->item_id && ($ls->lp_type == 0)) {
			$sc_ids[] = $ls->item_id;
		} elseif ($ls->item_id && ($ls->lp_type == 1 || $ls->lp_type == 2 ) ) {
			$scn_ids[] = $ls->item_id;
		} elseif (!$ls->item_id) {
			$lp_ids[] = $ls->id;
		}
	}
	if (count($lp_ids)) {
		//require_once(_JOOMLMS_FRONT_HOME . "/includes/lms_grades.lib.php");
		$lp_res = JLMS_Get_LP_userResults($user_ids, $lp_ids, $course_id, $params->get('track_type', 0));
		foreach ($lp_res as $lpr) {
			$i = 0;
			while ($i < count($lpaths)) {
				//if ((!$lpaths[$i]->item_id) && ($lpaths[$i]->id == $lpr->lpath_id) && isset($lpaths[$i]->user_id) && $lpaths[$i]->user_id == $lpr->user_id) {
				if ((!$lpaths[$i]->item_id) && ($lpaths[$i]->id == $lpr->lpath_id)) {//user_id checks commented
					$lpaths[$i]->r_status = $lpr->user_status;
					$lpaths[$i]->r_start = $lpr->start_time;
					$lpaths[$i]->r_end = $lpr->end_time;
				}
				$i ++;
			}
		}
	}
	if (count($sc_ids) || count($scn_ids)) {
		require_once(_JOOMLMS_FRONT_HOME . "/includes/lms_scorm.lib.php");
		if (count($sc_ids)) {
			$scorm_ans = & JLMS_GetSCORM_userResults($user_ids, $sc_ids);
			foreach ($scorm_ans as $sa) {
				$i = 0;
				while ($i < count($lpaths)) {
					if (($lpaths[$i]->lp_type == 0) && ($lpaths[$i]->item_id == $sa->content_id)) {
						$lpaths[$i]->s_status = $sa->status;
						$lpaths[$i]->s_score = $sa->score;
					}
					$i ++;
				}
			}
		}
		if (count($scn_ids)) {
			$scorm_n_ans = & JLMS_Get_N_SCORM_userResults($user_ids, $scn_ids, $params->get('track_type', 0));
			foreach ($scorm_n_ans as $n_sa) {
				$i = 0;
				while ($i < count($lpaths)) {
					if (($lpaths[$i]->lp_type == 1 || $lpaths[$i]->lp_type == 2) && ($lpaths[$i]->item_id == $n_sa->content_id)) {
						$lpaths[$i]->s_status = $n_sa->status;
						$lpaths[$i]->s_score = $n_sa->score;
						$lpaths[$i]->r_end = $n_sa->scn_timemodified;
						$lpaths[$i]->r_start = $n_sa->at_start;
						if (isset($n_sa->suspend_data)) {
							$lpaths[$i]->suspend_data = $n_sa->suspend_data;
						}
					}
					$i ++;
				}
			}
		}
	}
}
function JLMS_getLpathProgress($row, $user_id = null){
	$db = & JFactory::getDbo();
	$user = & JFactory::getUser();
	if (is_null($user_id)) {
		$user_id = $user->get('id');
	}

	$percent = 0;
	if(isset($row->id) && $row->id){
		if($row->item_id == 0){
			$query = "SELECT lps.*"
			. "\n FROM #__lms_learn_path_steps as lps"
			. "\n WHERE 1"
			. "\n AND lps.course_id = '".$row->course_id."'"
			. "\n AND lps.lpath_id = '".$row->id."'"
			;
			$db->setQuery($query);
			$all_steps = $db->loadObjectList();
			
			$query = "SELECT lpsr.*"
			. "\n FROM #__lms_learn_path_results as lpr, #__lms_learn_path_step_results as lpsr"
			. "\n WHERE 1"
			. "\n AND lpr.id = lpsr.result_id"
			. "\n AND lpr.course_id = '".$row->course_id."'"
			. "\n AND lpr.lpath_id = '".$row->id."'"
			. "\n AND lpr.user_id = '".$user_id."'"
			;
			$db->setQuery($query);
			$all_result_steps = $db->loadObjectList();
			
			$tmp_all_steps = array();
			foreach($all_steps as $n=>$step){
				$tmp_all_steps[$n] = $step;
				$tmp_all_steps[$n]->step_status = 0;
				
				foreach($all_result_steps as $result_step){
					if($step->id == $result_step->step_id){
						$tmp_all_steps[$n]->step_status = $result_step->step_status;
					}
				}
			}
			$all_steps = array();
			$all_steps = $tmp_all_steps;
			
			if(isset($all_steps) && count($all_steps)){
				$completed_step = 0;
				foreach($all_steps as $step){
					if(isset($step->step_status) && $step->step_status == 1){
						$completed_step++;
					}
				}
				
				if($completed_step){
					$percent = round(($completed_step/count($all_steps))*100); 
				}
			}
		} else 
		if($row->item_id){
			if (isset($row->suspend_data)) {
				$suspend_data = $row->suspend_data;
			} else {
				$query = "SELECT lnsst.value"
				. "\n FROM #__lms_n_scorm as lns, #__lms_n_scorm_scoes_track as lnsst"
				. "\n WHERE 1"
				. "\n AND lns.id = lnsst.scormid"
				. "\n AND lnsst.element = 'cmi.suspend_data'"
				. "\n AND lns.id = '".$row->item_id."'"
				. "\n AND lns.course_id = '".$row->course_id."'"
				. "\n AND lnsst.userid = '".$user_id."'"
				;
				$db->setQuery($query);
				$suspend_data = $db->loadResult();
			}
			$percent = checkSuspendDate($suspend_data);
		}
	}
	
//	echo '$percent= '.$percent;
	
	$hide_percent = 0;
	if(isset($row->s_status) && $row->s_status == 1 && $percent == -1 && isset($row->r_start) && $row->r_start){
		$hide_percent = 1;
		$percent = 100;
	} else
	if(isset($row->s_status) && $row->s_status == 0 && $percent == -1 && isset($row->r_start) && $row->r_start){
		$hide_percent = 1;
		$percent = 50;
	} else 
	if(!isset($row->s_status) && $percent == -1 && isset($row->r_start) && !$row->r_start){
		$hide_percent = 1;
		$percent = 0;
	}

	$class_completed = '';
	if(isset($row->r_status) && $row->r_status == 1){
		$class_completed .= ' ';
		$class_completed .= 'completed';
	} else 
	if(isset($row->s_status) && $row->s_status == 1){
		$class_completed .= ' ';
		$class_completed .= 'completed';
	} else {
		$class_completed .= ' ';
		$class_completed .= 'incompleted';
	}
	$suffix_class = '_lpath'.$class_completed;
	
	return JLMS_HTML::showProgressBar($percent, $row->id, $suffix_class, $hide_percent);
}

function checkSuspendDate($suspend_data){
	
	$percent = 0;
	$completed_step = 0;
	$all_steps = array();
	$is_ok = false;
	
	$tmp = array();
	$split = explode('@', $suspend_data);
	if(isset($split) && count($split) > 1){
		foreach($split as $s){
			$tmp = array_merge($tmp, explode(',', $s));
		}
		
		$is_ok = false;
		$all_steps = array();
		$completed_step = 0;
		$count_tmp = count($tmp) - 1;
		foreach($tmp as $n=>$t){
			if($n == $count_tmp && ($t == 'true' || $t == 'false')){
				$is_ok = true;
			}
			if($n < $count_tmp){
				if(strlen($t) != 1){
					$is_ok = false;
					break;
				} else {
					$all_steps[] = $t;
					if($t == 1){
						$completed_step++;
					}
				}
			} 
		}
	} else {
		$is_ok = false;
		$all_steps = array();
		
		$tmp = array();
		
		$alphabet = range('A', 'Z');
		if(in_array(substr($suspend_data, 0, 1), $alphabet)){
			$suspend_data = substr($suspend_data, 1);
			if(preg_match_all('#(\d+)$#', $suspend_data, $out)){
				$split = str_split($suspend_data);
				foreach($split as $n=>$s){
					if($n){
						$tmp = array_merge($tmp, array($s));
					}
				}
				$tmp = array_reverse($tmp);
				
				$is_ok = false;
				$all_steps = array();
				$completed_step = 0;
				foreach($tmp as $n=>$t){
					if(strlen($t) != 1 && !intval($t)){
						$is_ok = false;
						break;
					} else {
						$is_ok = true;
						$all_steps[] = $t;
						if($t == 1){
							$completed_step++;
						}
					}
				}
			}
		} else {
			$is_ok = false;
			$all_steps = array();
			
			$tmp = array();
			$split = explode(',', $suspend_data);
			
			if(isset($split) && count($split) > 1 && ($split[0] == 'true' || $split[0] == 'false')){
				$tmp = $split;
				
				$is_ok = false;
				$all_steps = array();
				$completed_step = 0;
				foreach($tmp as $n=>$t){
					if($t != 'true' && $t != 'false'){
						$is_ok = false;
						break;
					} else {
						$is_ok = true;
						$all_steps[] = $t;
						if($t == 'true'){
							$completed_step++;
						}
					}
				}
			} else {
				$is_ok = false;
				$all_steps = array();
			
				$tmp = array();
				if(substr($suspend_data, 0, 3) == 'toc'){
					if(preg_match_all('#toc\`([^:]+)#', $suspend_data, $out)){
						if(isset($out[0][0]) && isset($out[1][0])){
							$tmp_1 = array();
							
							$tmp_1 = explode(':', $out[1][0]);
							if(isset($tmp_1[0]) && count(explode(',', $tmp_1[0]))){
								$tmp = explode(',', $tmp_1[0]);
							}
						
							$is_ok = false;
							$all_steps = array();
							$completed_step = 0;
							foreach($tmp as $n=>$t){
								if(strlen($t) != 1 && !intval($t)){
									$is_ok = false;
									break;
								} else {
									$is_ok = true;
									$all_steps[] = $t;
									if($t == 1){
										$completed_step++;
									}
								}
							}
						}
					}
				} else {
					$is_ok = false;
					$all_steps = array();
					
					$tmp = array();
					if(preg_match_all('#[\w]{2}(\d+)$#', $suspend_data, $out)){
						if(isset($out[0][0]) && isset($out[1][0])){
							$tmp_1 = array();
							$tmp_1 = str_split($out[1][0]);
							
							$stop = count($tmp_1)/2;
							$n=1;
							foreach($tmp_1 as $t1){
								if($n <= $stop){
									$tmp[] = $t1;
								}
								$n++;
							}
							$tmp = array_reverse($tmp);
						
							$is_ok = false;
							$all_steps = array();
							$completed_step = 0;
							foreach($tmp as $n=>$t){
								if(strlen($t) != 1 && !intval($t)){
									$is_ok = false;
									break;
								} else {
									$is_ok = true;
									$all_steps[] = $t;
									if($t == 1){
										$completed_step++;
									}
								}
							}
						}
					} else {
						$is_ok = false;
						$all_steps = array();
						if(preg_match_all('#viewed=([^|]*)\|lastviewedslide=(\d+)\|#', $suspend_data, $out)){
							$is_ok = false;
							
							$tmp = array();
							
							if(isset($out[1][0])){
								$tmp_1 = array();
								$tmp_1 = explode(',', $out[1][0]);
								$completed_step = count($tmp_1);
							}

							if(preg_match_all('/viewed=([^|]*)\|lastviewedslide=\d+\|\d+\#\d+\#.*#\,([^#]*)\#/', $suspend_data, $out)){
								if(isset($out[0][0]) && isset($out[2][0])){
									$is_ok = true;
									
									$tmp_1 = array();
									$tmp_1 = explode(',', $out[1][0]);
									$completed_step = count($tmp_1);
								
									$tmp_2 = array();
									$tmp_2 = explode(',', $out[2][0]);
									
									$all_steps = $tmp_2;
								}
							} else 
							if(preg_match_all('/viewed=([^|]+)\|lastviewedslide=([^|]+)\|(\d+|)\#(\d+|)\#(\d+|)\#\,(\d+|[^#]*)\#(\d+|)\#(\d+|)\#/', $suspend_data, $out)){
								if(isset($out[0][0]) && isset($out[1][0]) && isset($out[6][0])){
									$is_ok = true;
									
									$tmp_1 = array();
									$tmp_1 = explode(',', $out[1][0]);
									
									$tmp_2 = array();
									$tmp_2 = explode(',', $out[6][0]);
									
									
									
									$all_steps = $tmp_2;
									$completed_step = count($tmp_1);
								}
							} else 
							if(preg_match_all('#viewed=([^|]*)\|lastviewedslide=(\d+)\|(.*)$#', $suspend_data, $out)){
								if(isset($out[0][0]) && isset($out[3][0])){
									$in = $out[3][0];
									if(preg_match_all('#(\w\d+\=\d+)#', $in, $out)){
										if(isset($out[0]) && count($out[0])){
											$is_ok = true;
											
											$all_steps = $out[0];
										}
									}
								}
							}
						}
					}
				}
			}
		}
	}
	if($is_ok && $completed_step && count($all_steps) && $completed_step <= count($all_steps)){
		$percent = round(($completed_step/count($all_steps))*100); 
	} else {
		$percent = -1;
	}
	
//	echo '<pre>';
//	var_dump($is_ok);
//	echo '</pre>';
//	echo $completed_step;
//	echo '<br />';
//	echo count($all_steps);
//	echo '<br />';
//	echo $percent;
//	echo '<br />';
//	echo '----------------';
//	echo '<br />';
	
	return $percent;
}

//places in $rows information about SCORM user progress + gradebook items
//place in $lists - SCORM's + gradebook items
function JLMS_GB_getUsersGrades($id, $uids, &$rows, &$lists, $cycle=0) {
	$db = & JFactory::getDbo();

	$JLMS_ACL = & JLMSFactory::getACL();
	$is_teacher = $JLMS_ACL->isTeacher();

	$uids_str = implode(',',$uids);
	if (count($uids)) {
		$query = "SELECT a.*, b.scale_name FROM #__lms_gradebook as a LEFT JOIN #__lms_gradebook_scale as b ON a.gb_points=b.id AND b.course_id = $id WHERE a.course_id = '".$id."' AND a.user_id IN ($uids_str)";
		$db->SetQuery( $query );
		$gb_rows = $db->LoadObjectList();
		$query = "SELECT * FROM #__lms_certificate_users WHERE course_id = '".$id."' AND user_id IN ($uids_str)";
		$db->SetQuery( $query );
		$crt_rows = $db->LoadObjectList();
	} else {
		$crt_rows = array();
		$gb_rows = array();
	}
	$query = "SELECT * FROM #__lms_gradebook_scale WHERE course_id = $id ORDER BY ordering, scale_name";
	$db->SetQuery( $query );
	$scale_rows = $db->LoadObjectList();
	if (count($uids) == 1) {
		if (count($crt_rows) > 1) {
			$del_ids_tmp = array();
			$del_ids = array();
			foreach ($crt_rows as $crt_row) {
				if ($crt_row->crt_date == '0000-00-00 00:00:00') {
					$del_ids_tmp[] = $crt_row->id;
				}
			}
			if (count($del_ids) == count($crt_rows)) {
				$del_ii = 1;
				while ($del_ii < count($del_ids_tmp)) {
					$del_ids[] = $del_ids_tmp[$del_ii];
					$del_ii++;
				}
			} else {
				$del_ids = $del_ids_tmp;
			}
			$del_ids_str = implode(',',$del_ids);
			$query = "DELETE FROM #__lms_certificate_users WHERE id IN ($del_ids_str) AND course_id = '".$id."' AND user_id IN ($uids_str) AND crt_date = '0000-00-00 00:00:00'";
			$db->SetQuery( $query );
			$db->query();
		}
	}

	$query = "SELECT * FROM #__lms_gradebook_items WHERE course_id = '".$id."' ORDER BY ordering, gbi_name";
	$db->SetQuery( $query );
	$irows = $db->LoadObjectList();
	$query = "SELECT * FROM #__lms_learn_paths WHERE course_id = '".$id."' AND item_id <> '0' AND item_id <> '' AND published = '1' ORDER BY ordering";
	$db->SetQuery( $query );
	$scorm_rows = $db->LoadObjectList();
	$scorm_ans = array();
	$scorm_n_ans = array();
	if (count($scorm_rows)) {
		$scids = array();
		$scn_ids = array();
		$scrows_i = 0;
		foreach ($scorm_rows as $scorm_row) {
			$tmp_params = new JLMSParameters($scorm_row->lp_params);
			if ($tmp_params->get('show_in_gradebook',1)) {
				if ($scorm_row->lp_type == 1 || $scorm_row->lp_type == 2) {
					$scn_ids[] = $scorm_row->item_id;
				} else {
					$scids[] = $scorm_row->item_id;
				}
			}
			$scorm_rows[$scrows_i]->show_in_gradebook = $tmp_params->get('show_in_gradebook',1);
			$scrows_i++;
		}
		
		if($cycle){
			$query = "SELECT params FROM #__lms_courses WHERE id = '".$id."'"; //Atention!
			$db->setQuery($query); //Atention!
			$course_params = $db->loadResult(); //Atention!
		} else {
			global $JLMS_CONFIG;
			$course_params = $JLMS_CONFIG->get('course_params');
		}
		$params = new JLMSParameters($course_params);

		if (count($scids) || count($scn_ids)) {
			require_once(_JOOMLMS_FRONT_HOME . "/includes/lms_scorm.lib.php");
			if (count($scids)) {
				//nothing here
				$scorm_ans = & JLMS_GetSCORM_userResults($uids, $scids);
			}
			if (count($scn_ids)) {
				$scorm_n_ans = array();
				$uids_groups = array();
				$uids_group = array();
				//group user IDs by 5
				$uid_i = 1;
				foreach ($uids as $uid) {
					$uids_group[] = $uid;
					$uid_i++;
					if ($uid_i > 5) {
						$uids_groups[] = $uids_group;
						$uids_group = array();
						$uid_i = 1;
					}
				}
				if (count($uids_group)) {
					$uids_groups[] = $uids_group;
				}
				foreach ($uids_groups as $uids5) {
					foreach ($scn_ids as $scn_id) {
						$scn_ids_new_array = array();
						$scn_ids_new_array[] = $scn_id;
						$scorm_n_ans1 = & JLMS_Get_N_SCORM_userResults($uids5, $scn_ids_new_array, $params->get('track_type', 0));
						$scorm_n_ans = array_merge($scorm_n_ans, $scorm_n_ans1);
					}
				}
			}
		}
	}

	$query = "SELECT * FROM #__lms_quiz_t_quiz WHERE course_id = '".$id."' AND c_gradebook = 1 ORDER BY c_title";
	$db->SetQuery( $query );
	$quiz_rows = $db->LoadObjectList();
	$quiz_ans = array();
	if (count($quiz_rows)) {
		if (count($uids)) {
			$query = "SELECT a.*, b.c_full_score FROM #__lms_quiz_results as a, #__lms_quiz_t_quiz as b WHERE a.course_id = '".$id."'"
			. "\n AND a.quiz_id = b.c_id AND a.user_id IN ( $uids_str ) ORDER BY a.user_id, a.quiz_id";
			$db->SetQuery( $query );
			$quiz_ans = $db->LoadObjectList();
		} else {
			$quiz_ans = array();
		}
	}
	$i = 0;
	while ($i < count($rows)) {
		$p = array();
		foreach ($irows as $irow) {
			$pp = new stdClass();
			$pp->gbi_id = $irow->id;
			$pp->gbi_type = 1; // 1 - gb_item, 2 - scorm; 3 - quiz;
			$pp->user_grade = '-';
			$pp->scale_id = 0;
			$p[] = $pp;
		}
		$j = 0;
		while ($j < count($gb_rows)) {
			if ($gb_rows[$j]->user_id == $rows[$i]->user_id) {
				$k = 0;
				while ($k < count($p)) {
					if ($p[$k]->gbi_id == $gb_rows[$j]->gbi_id && $p[$k]->gbi_type == 1) {
						$p[$k]->user_grade = $gb_rows[$j]->scale_name?$gb_rows[$j]->scale_name:'-';//$gb_rows[$j]->gb_points;
						$p[$k]->scale_id = $gb_rows[$j]->gb_points?$gb_rows[$j]->gb_points:'-';
					}
					$k ++;
				}
			}
			$j ++;
		}
		$rows[$i]->user_certificate = 0;
		$rows[$i]->user_certificate_date = '';
		$j = 0;
		while ($j < count($crt_rows)) {
			if ($crt_rows[$j]->user_id == $rows[$i]->user_id) {
				$rows[$i]->user_certificate = $crt_rows[$j]->crt_option;
				$rows[$i]->user_certificate_date = $crt_rows[$j]->crt_date;
			}
			$j ++;
		}
		$rows[$i]->grade_info = $p;
		//scorm
		$p = array();
		foreach ($scorm_rows as $srow) {
			$pp = new stdClass();
			$pp->gbi_id = $srow->item_id;
			$pp->lp_type = $srow->lp_type;
			$pp->gbi_type = 2; // 1 - gb_item, 2 - scorm; 3 - quiz;
			$pp->user_grade = '-';
			$pp->user_pts = 0;
			$pp->user_status = -1;
			$p[] = $pp;
		}
		$j = 0;
		while ($j < count($scorm_ans)) {
			if ($scorm_ans[$j]->user_id == $rows[$i]->user_id) {
				$k = 0;
				while ($k < count($p)) {
					if ($p[$k]->gbi_id == $scorm_ans[$j]->content_id && $p[$k]->gbi_type == 2 && !$p[$k]->lp_type) {
						/*$p[$k]->user_grade = $scorm_ans[$j]->score;
						$p[$k]->user_status = $scorm_ans[$j]->status;*/
						$user_per = ($scorm_ans[$j]->score > 100)?100:$scorm_ans[$j]->score;
						$sc_i = count($scale_rows) - 1;
						while ($sc_i >= 0) {
							if (($scale_rows[$sc_i]->min_val <= $user_per) && ($scale_rows[$sc_i]->max_val >= $user_per)) {
								$p[$k]->user_grade = $scale_rows[$sc_i]->scale_name;
								break;
							}
							$sc_i --;
						}
						$p[$k]->user_pts = $scorm_ans[$j]->score;
						$p[$k]->user_status = $scorm_ans[$j]->status;
					}
					$k ++;
				}
			}
			$j ++;
		}
		$j = 0;
		while ($j < count($scorm_n_ans)) {
			if ($scorm_n_ans[$j]->user_id == $rows[$i]->user_id) {
				$k = 0;
				while ($k < count($p)) {
					if ($p[$k]->gbi_id == $scorm_n_ans[$j]->content_id && $p[$k]->gbi_type == 2 && ( $p[$k]->lp_type == 1 || $p[$k]->lp_type == 2) ) {
						
						/*$p[$k]->user_grade = $scorm_n_ans[$j]->score;
						$p[$k]->user_status = $scorm_n_ans[$j]->status;*/
						$user_per = ($scorm_n_ans[$j]->score > 100)?100:$scorm_n_ans[$j]->score;
						//$user_per = intval($quiz_ans[$j]->user_score * 100 / $quiz_ans[$j]->c_full_score);
						$sc_i = count($scale_rows) - 1;
						while ($sc_i >= 0) {
							if (($scale_rows[$sc_i]->min_val <= $user_per) && ($scale_rows[$sc_i]->max_val >= $user_per)) {
								$p[$k]->user_grade = $scale_rows[$sc_i]->scale_name;
								break;
							}
							$sc_i --;
						}
						$p[$k]->user_pts = $scorm_n_ans[$j]->score;
						$p[$k]->user_status = $scorm_n_ans[$j]->status;
					}
					$k ++;
				}
			}
			$j ++;
		}
		$rows[$i]->scorm_info = $p;
		//quizzes
		$p = array();
		foreach ($quiz_rows as $qrow) {
			$pp = new stdClass();
			$pp->gbi_id = $qrow->c_id;
			$pp->allow_user_pdf_print = $qrow->c_enable_print;
			$pp->gbi_type = 3; // 1 - gb_item, 2 - scorm; 3 - quiz;
			$pp->user_grade = '-';
			$pp->user_pts = 0;
			$pp->user_pts_full = '0';
			$pp->user_status = -1;
			$pp->user_passed = -1;
			$pp->user_score = 0;
			$pp->quiz_max_score = 0;
			
			/*Integration Plugin Percentiles*/
			$_JLMS_PLUGINS = & JLMSFactory::getPlugins();
			$_JLMS_PLUGINS->loadBotGroup('system');
			
			$data_plugin = new stdClass();
			$data_plugin->course_id = $rows[$i]->course_id;
			$data_plugin->quiz_id = $qrow->c_id;
			$data_plugin->user_id = $rows[$i]->user_id;
			
			if($out_plugin = $_JLMS_PLUGINS->trigger('onQuizFinish', array($data_plugin))){
				if(count($out_plugin)){
					$percentiles = $out_plugin[0];
					if($percentiles->percent >= 0){
						$percent = $percentiles->percent.'%';
						$pp->user_percentile = $percent;
					}
				}	
			}
			/*Integration Plugin Percentiles*/
			
			$p[] = $pp;
		}
		$j = 0;
		while ($j < count($quiz_ans)) {
			if ($quiz_ans[$j]->user_id == $rows[$i]->user_id) {
				$k = 0;
				while ($k < count($p)) {
					if ($p[$k]->gbi_id == $quiz_ans[$j]->quiz_id && $p[$k]->gbi_type == 3) {
						if ($quiz_ans[$j]->quiz_max_score) {
							$user_per = intval($quiz_ans[$j]->user_score * 100 / $quiz_ans[$j]->quiz_max_score);
						} elseif ($quiz_ans[$j]->c_full_score) {
							$user_per = intval($quiz_ans[$j]->user_score * 100 / $quiz_ans[$j]->c_full_score);
						} else {
							if ($quiz_ans[$j]->user_score) {
								// ...strange ... HOW?
								$user_per = 100;
							} else {
								$user_per = 0;
							}
						}
						$sc_i = count($scale_rows) - 1;
						while ($sc_i >= 0) {
							if (($scale_rows[$sc_i]->min_val <= $user_per) && ($scale_rows[$sc_i]->max_val >= $user_per)) {
								$p[$k]->user_grade = $scale_rows[$sc_i]->scale_name;
								break;
							}
							$sc_i --;
						}
						$p[$k]->user_pts = $quiz_ans[$j]->user_score;
						$p[$k]->user_pts_full = $quiz_ans[$j]->user_score.'/'.$quiz_ans[$j]->quiz_max_score;
						$p[$k]->user_status = $quiz_ans[$j]->user_passed;
						$p[$k]->user_passed = $quiz_ans[$j]->user_passed;
						$p[$k]->user_score = $quiz_ans[$j]->user_score;
						$p[$k]->quiz_max_score = $quiz_ans[$j]->quiz_max_score;
					}
					$k ++;
				}
			}
			$j ++;
		}
		$rows[$i]->quiz_info = $p;
		$i++;
	}
	global $my;
		//--------------------------
		for($i=0;$i<count($scorm_rows);$i++) {
			if($scorm_rows[$i]->lp_type == 2) {
				$query = "SELECT scorm_package FROM #__lms_n_scorm WHERE id = '".$scorm_rows[$i]->item_id."'";
				$db->SetQuery( $query );
				$scorm_package = $db->LoadResult();	
				
				$query = "SELECT id FROM #__lms_n_scorm WHERE scorm_package = '".$scorm_package."' AND course_id = 0";
				$db->SetQuery( $query );
				$scorm_lib_id = $db->LoadResult();	

				$outer_doc = null;
				$query = "SELECT outdoc_share, owner_id, allow_link FROM #__lms_outer_documents WHERE file_id = '".$scorm_lib_id."' AND folder_flag = 3";
				$db->SetQuery( $query );
				$outer_doc = $db->LoadObject();	

				if(is_object($outer_doc) && isset($outer_doc->allow_link) && $outer_doc->allow_link == 1 ) {

				} else {
					unset($scorm_rows[$i]);	
				}
			}
		}

		$mas = array();
		foreach ($scorm_rows as $k=>$v) {
			$mas[] = $scorm_rows[$k];	
		}
		unset($scorm_rows);
		$scorm_rows = $mas;
		//---------------------------------
	if($cycle){
		$lists['gb_rows'][] = $irows;
		$lists['sc_rows'][] = $scorm_rows;
		$lists['quiz_rows'][] = $quiz_rows;
	} else {
		$lists['gb_rows'] = $irows;
		$lists['sc_rows'] = $scorm_rows;
		$lists['quiz_rows'] = $quiz_rows;
	}
}
?>