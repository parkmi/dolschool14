<?php 
defined( '_JLMS_EXEC' ) or die( 'Direct Access to this location is not allowed.' );

// function is deprecated in 1.0.2 (it located here only for compatibility needs)
function JLMS_GetSCORM_userResults( &$user_ids, &$scorm_ids ) {
	global $JLMS_DB;
	if (count($user_ids) && count($scorm_ids)) {
		$sc_str = implode(',',$scorm_ids);
		$uids_str = implode(',',$user_ids);
		$query = "SELECT * FROM #__lms_scorm_sco WHERE content_id IN ( $sc_str ) AND user_id IN ( $uids_str ) ORDER BY user_id, content_id";
		$JLMS_DB->SetQuery( $query );
		$scorm_ans_pre = $JLMS_DB->LoadObjectList();
	} else {
		$scorm_ans = array();
		return $scorm_ans;
	}
	$scorm_ans = array();
	$h = 0;
	$first_step = true;
	$p = new stdClass();$p->status = 1;$p->score = 0;$p->user_id = 0;$p->content_id = 0;
	while ( $h < count($scorm_ans_pre) ) {
		if ( $first_step || ($p->user_id != $scorm_ans_pre[$h]->user_id) || ($p->content_id != $scorm_ans_pre[$h]->content_id) ) {
			if ($first_step) {
				$first_step = false;
			} else {
				$scorm_ans[] = $p;
			}
			$p = new stdClass();
			$p->status = 1;
			$p->score = 0;
			$p->user_id = $scorm_ans_pre[$h]->user_id;
			$p->content_id = $scorm_ans_pre[$h]->content_id;
		}
		$p->score = $p->score + intval($scorm_ans_pre[$h]->score);
		if ($scorm_ans_pre[$h]->status != 'completed' && $scorm_ans_pre[$h]->status != 'passed') { $p->status = 0; }
		if ($h == (count($scorm_ans_pre) - 1)) { $scorm_ans[] = $p; }
		$h ++;
	}	
	return $scorm_ans;
}

/**
 * Get SCORM results
 *
 * @param unknown_type $user_ids
 * @param unknown_type $scorm_ids
 * @param int $ttype - tracking type (0 - by the last attempt; 1 - by the best attempt
 * @return unknown
 */

function & JLMS_Get_N_SCORM_userResults( &$user_ids, &$scorm_ids, $ttype = 0 ) {
	$db = & JFactory::getDbo();
	$scorm_ans_pre = array();
	if (count($user_ids) && count($scorm_ids)) {
		$sc_str = implode(',',$scorm_ids);
		$uids_str = implode(',',$user_ids);
	/*if ($ttype) { //by the best attempt

	} else {
		$query = "SELECT max(attempt) as max_attempt WHERE scormid IN ( $sc_str ) AND userid IN ( $uids_str )";
		$JLMS_DB->SetQuery($query);
		$last_at = $JLMS_DB->LoadResult();

		if ($last_at) {
			$query = "SELECT * FROM #__lms_n_scorm_scoes_track WHERE scormid IN ( $sc_str ) AND userid IN ( $uids_str ) AND attempt = $last_at"
			#. "\n AND ".$JLMS_DB->NameQuote("element")." <> 'cmi.suspend_data'"
			#. "\n AND ".$JLMS_DB->NameQuote("element")." <> 'cmi.location' ORDER BY userid, scormid";
			. "\n ORDER BY userid, scormid";
			$JLMS_DB->SetQuery( $query );
			$scorm_ans_pre = $JLMS_DB->LoadObjectList();
		}
	}*/
	/*$query = "SELECT * FROM #__lms_n_scorm_scoes_track WHERE scormid IN ( $sc_str ) AND userid IN ( $uids_str )"
	#. "\n AND ".$JLMS_DB->NameQuote("element")." <> 'cmi.suspend_data'"
	#. "\n AND ".$JLMS_DB->NameQuote("element")." <> 'cmi.location' ORDER BY userid, scormid";
	. "\n ORDER BY userid, scormid, attempt";*/

		$query = "SELECT a3.* FROM #__lms_n_scorm as a1, #__lms_n_scorm_scoes as a2, #__lms_n_scorm_scoes_track as a3"
		. "\n WHERE a1.id IN ( $sc_str ) AND a2.scorm = a1.id AND a3.scormid = a1.id AND a2.id = a3.scoid AND a3.userid IN ( $uids_str )"
		. "\n ORDER BY a3.userid, a3.scormid, a3.attempt";
	
		$db->SetQuery( $query );
		$scorm_ans_pre = $db->LoadObjectList();
	}
	$scorm_ans = array();
	$h = 0;
	$first_step = true;
	$tmp_var = false;
	$p = new stdClass();$p->status = 1;$p->score = 0;$p->user_id = 0;$p->content_id = 0;$p->scn_timemodified = 0;$p->suspend_data = '';
	while ( $h < count($scorm_ans_pre) ) {
		if ( $first_step || ($p->user_id != $scorm_ans_pre[$h]->userid) || ($p->content_id != $scorm_ans_pre[$h]->scormid) || ($p->attempt != $scorm_ans_pre[$h]->attempt) ) {
			if ($first_step) {
				$first_step = false;
			} else {
				$p->at_start = $p->scn_timemodified ? ($p->scn_timemodified - JLMS_Estimate_SCORMTIME_seconds($p->at_start_temp_value)) : 0;
				$scorm_ans[] = $p;
			}
			$p = new stdClass();
			$p->status = 0;
			$p->score = 0;
			$p->attempt = 0;
			$p->user_id = $scorm_ans_pre[$h]->userid;
			$p->content_id = $scorm_ans_pre[$h]->scormid;
			$p->scn_timemodified = 0;
			$p->at_start = 0;
			$p->at_start_temp_value = '';
			$p->at_start_temp_value_priority = 0;
			$p->suspend_data = '';
			$tmp_var = false;
		}
		$p->attempt = $scorm_ans_pre[$h]->attempt;
		if ($scorm_ans_pre[$h]->timemodified > $p->scn_timemodified) {
			$p->scn_timemodified = $scorm_ans_pre[$h]->timemodified;
		}
		if ( ($scorm_ans_pre[$h]->element == 'cmi.score.raw') || ($scorm_ans_pre[$h]->element == 'cmi.core.score.raw') ) {
			$p->score = $scorm_ans_pre[$h]->value;//$p->score + intval($scorm_ans_pre[$h]->score);
		}

		//DEN (03Jan2011) : suspend data for progress analisys
		if ($scorm_ans_pre[$h]->element == 'cmi.suspend_data') {
			$p->suspend_data = $scorm_ans_pre[$h]->value;
		}

		/* time (03.10.2007) */
		if ( $scorm_ans_pre[$h]->element == 'cmi.core.session_time' && $p->at_start_temp_value_priority < 4 ) {
			$p->at_start_temp_value = $scorm_ans_pre[$h]->value;
			$p->at_start_temp_value_priority = 4;
		}
		if ( $scorm_ans_pre[$h]->element == 'cmi.session_time' && $p->at_start_temp_value_priority < 3 ) {
			$p->at_start_temp_value = $scorm_ans_pre[$h]->value;
			$p->at_start_temp_value_priority = 3;
		}
		if ( $scorm_ans_pre[$h]->element == 'cmi.core.total_time' && $p->at_start_temp_value_priority < 2 ) {
			$p->at_start_temp_value = $scorm_ans_pre[$h]->value;
			$p->at_start_temp_value_priority = 2;
		}
		if ( $scorm_ans_pre[$h]->element == 'cmi.total_time' && $p->at_start_temp_value_priority < 1 ) {
			$p->at_start_temp_value = $scorm_ans_pre[$h]->value;
			$p->at_start_temp_value_priority = 1;
		}


		if (($scorm_ans_pre[$h]->element == 'cmi.completion_status') || ($scorm_ans_pre[$h]->element == 'cmi.core.lesson_status') || ($scorm_ans_pre[$h]->element == 'cmi.core.completion_status') || ($scorm_ans_pre[$h]->element == 'cmi.lesson_status')) {
			if (($scorm_ans_pre[$h]->value == 'completed') || ($scorm_ans_pre[$h]->value == 'passed')) {
				if (!$tmp_var) {
					$p->status = 1;
				}
			} else {
				$tmp_var = true;
				$p->status = 0;
			}
		}
		//if ($scorm_ans_pre[$h]->status != 'completed' && $scorm_ans_pre[$h]->status != 'passed') { $p->status = 0; }
		if ($h == (count($scorm_ans_pre) - 1)) {
			$p->at_start = $p->scn_timemodified ? ($p->scn_timemodified - JLMS_Estimate_SCORMTIME_seconds($p->at_start_temp_value)) : 0;
			$scorm_ans[] = $p;
		}
		$h ++;
	}

	//tracking type handling - 26 July 2007 (DEN)
	$scorm_ans_ret = array();
	$first_step = true;
	$h = 0;
	while ( $h < count($scorm_ans) ) {
		if ( $first_step || ($p->user_id != $scorm_ans[$h]->user_id) || ($p->content_id != $scorm_ans[$h]->content_id)) {
			if ($first_step) {
				$first_step = false;
			} else {
				$scorm_ans_ret[] = $p;
			}
			$p = new stdClass();
			$p = $scorm_ans[$h];
		} else {
			if ($ttype) { // - by the best attempt
				if ($p->status) {
					if ($scorm_ans[$h]->status && ($scorm_ans[$h]->score > $p->score || $scorm_ans[$h]->score == $p->score)) {
						$p = new stdClass();
						$p = $scorm_ans[$h];
					}
				} else {
					if ($scorm_ans[$h]->status || $scorm_ans[$h]->score > $p->score || $scorm_ans[$h]->score == $p->score) {
						$p = new stdClass();
						$p = $scorm_ans[$h];
					}
				}
			} else {
				$p = new stdClass();
				$p = $scorm_ans[$h];
			}
		}
		if ($h == (count($scorm_ans) - 1)) { $scorm_ans_ret[] = $p; }
		$h ++;
	}
	return $scorm_ans_ret;
}

function JLMS_Estimate_SCORMTIME_seconds($value) {
	$ret = 0;
	if ($value) {
		$hours = 0;
		$mins = 0;
		$secs = 0;
		if (strlen($value) > 2 && substr($value,0,2) == 'PT') {
			preg_match('/PT([0-9\.]{1,6}H)?([0-9\.]{1,6}M)?([0-9\.]{1,6}S)/', $value, $a);
		} else {
			preg_match('/^([0-9]{1,2})\:([0-9]{1,2})\:([0-9]{1,2})\.([0-9]{1,2})$/', $value, $a);
		}
		if (isset($a[1]) && $a[1]) {
			$hours = intval($a[1]);
		}
		if (isset($a[2]) && $a[2]) {
			$mins = intval($a[2]);
		}
		if (isset($a[3]) && $a[3]) {
			$secs = intval($a[3]);
		}
		$ret = $secs + $mins*60 + $hours*3600;
	}
	return $ret;
}
// function is deprecated in 1.0.2 (it located here only for compatibility needs)
function JLMS_GetSCORM_SCO_userResults( &$user_ids, $scorm_id ) {
	global $JLMS_DB;
	$uids_str = implode(',',$user_ids);
	$query = "SELECT * FROM #__lms_scorm_sco WHERE content_id = $scorm_id AND user_id IN ( $uids_str ) ORDER BY user_id, sco_identifier";
	$JLMS_DB->SetQuery( $query );
	$scorm_ans_pre = $JLMS_DB->LoadObjectList();
	$query = "SELECT distinct sco_identifier FROM #__lms_scorm_sco WHERE content_id = $scorm_id AND user_id IN ( $uids_str )";
	$JLMS_DB->SetQuery( $query );
	$scorm_scos = $JLMS_DB->LoadObjectList();
	$scos_count = count($scorm_scos);
	$scorm_ans = array();
	if ($scos_count == 1) {
		$h = 0;
		$first_step = true;
		$p = new stdClass();$p->status = 1;$p->score = 0;$p->user_id = 0;$p->content_id = 0;
		while ( $h < count($scorm_ans_pre) ) {
			if ( $first_step || ($p->user_id != $scorm_ans_pre[$h]->user_id) || ($p->content_id != $scorm_ans_pre[$h]->content_id) ) {
				if ($first_step) {
					$first_step = false;
				} else {
					$scorm_ans[] = $p;
				}
				$p = new stdClass();
				$p->status = 1;
				$p->score = 0;
				$p->sco_count = $scos_count;
				$p->sco_identifier = '';
				$p->sco_title = '';
				$p->user_id = $scorm_ans_pre[$h]->user_id;
				$p->content_id = $scorm_ans_pre[$h]->content_id;
			}
			$p->score = $p->score + intval($scorm_ans_pre[$h]->score);
			if ($scorm_ans_pre[$h]->status != 'completed' && $scorm_ans_pre[$h]->status != 'passed') { $p->status = 0; }
			if ($h == (count($scorm_ans_pre) - 1)) { $scorm_ans[] = $p; }
			$h ++;
		}
	} else {
		$h = 0;
		$first_step = true;
		//$p = new stdClass();$p->status = 1;$p->score = 0;$p->user_id = 0;$p->content_id = 0;
		while ( $h < count($scorm_ans_pre) ) {
			$p = new stdClass();
			$p->status = 1;
			$p->score = 0;
			$p->sco_count = $scos_count;
			$p->sco_identifier = $scorm_ans_pre[$h]->sco_identifier;
			$p->sco_title = $scorm_ans_pre[$h]->sco_title;
			$p->user_id = $scorm_ans_pre[$h]->user_id;
			$p->content_id = $scorm_ans_pre[$h]->content_id;
			$p->score = intval($scorm_ans_pre[$h]->score);
			if ($scorm_ans_pre[$h]->status != 'completed' && $scorm_ans_pre[$h]->status != 'passed') { $p->status = 0; }
			$scorm_ans[] = $p;
			$h ++;
		}
	}
	return $scorm_ans;
}
function JLMS_Get_N_SCORM_SCO_userResults( &$user_ids, $scorm_id, $ttype = 0 ) {
	global $JLMS_DB;
	$uids_str = implode(',',$user_ids);
	$query = "SELECT id FROM #__lms_n_scorm_scoes WHERE scorm = $scorm_id";
	$JLMS_DB->SetQuery( $query );
	$scorm_scoes_ids = $JLMS_DB->LoadResultArray();
	if (empty($scorm_scoes_ids)) { $scorm_scoes_ids = array(0);}
	$scorm_scoes_ids_str = implode(',',$scorm_scoes_ids);
	$query = "SELECT * FROM #__lms_n_scorm_scoes_track WHERE scormid = $scorm_id AND userid IN ( $uids_str ) AND scoid IN ( $scorm_scoes_ids_str )"
	. "\n AND ".$JLMS_DB->NameQuote("element")." <> 'cmi.suspend_data'"
	. "\n AND ".$JLMS_DB->NameQuote("element")." <> 'cmi.location'"
 	. "\n ORDER BY userid, attempt";
	$JLMS_DB->SetQuery( $query );
	$scorm_ans_pre = $JLMS_DB->LoadObjectList();
	// tracking type checks
	$scorm_ids = array();
	$scorm_ids[] = $scorm_id;
	$ttt = & JLMS_Get_N_SCORM_userResults($user_ids, $scorm_ids, $ttype); // best results by attempts (in the case of $ttype)

	$scorm_ans_pre_real = array();
	for ($i=0; $i < count($scorm_ans_pre); $i ++) {
		$do_add = false;
		foreach ($ttt as $tt1) {
			if ($tt1->user_id == $scorm_ans_pre[$i]->userid && $tt1->content_id == $scorm_ans_pre[$i]->scormid && $tt1->attempt == $scorm_ans_pre[$i]->attempt) {
				$do_add = true;
				break;
			}
		}
		if ($do_add) {
			$p = new stdClass();
			$p = $scorm_ans_pre[$i];
			$scorm_ans_pre_real[] = $p;
		}
	}


	$query = "SELECT identifier as sco_identifier, scorm, id, title FROM #__lms_n_scorm_scoes WHERE scorm = $scorm_id AND scormtype <> '' ORDER BY id";
	$JLMS_DB->SetQuery( $query );
	$scorm_scos = $JLMS_DB->LoadObjectList();
	$scos_count = count($scorm_scos);
	for ($i=0; $i < $scos_count; $i ++) {
		$scorm_scos[$i]->track_data = array();
		foreach ($scorm_ans_pre_real as $sap) {
			if ($sap->scoid == $scorm_scos[$i]->id) {
				$p = new stdClass();
				$p->element = $sap->element;
				$p->value = $sap->value;
				$p->user_id = $sap->userid;
				$scorm_scos[$i]->track_data[] = $p;
			}
		}
	}
	return $scorm_scos;
	/*$scorm_ans = array();
	if ($scos_count == 1) {
		$h = 0;
		$first_step = true;
		$p = new stdClass();$p->status = 1;$p->score = 0;$p->user_id = 0;$p->content_id = 0;
		while ( $h < count($scorm_ans_pre) ) {
			if ( $first_step || ($p->user_id != $scorm_ans_pre[$h]->userid) || ($p->content_id != $scorm_ans_pre[$h]->scoid) ) {
				if ($first_step) {
					$first_step = false;
				} else {
					$scorm_ans[] = $p;
				}
				$p = new stdClass();
				$p->status = 1;
				$p->score = 0;
				$p->sco_count = $scos_count;
				$p->sco_identifier = '';
				$p->sco_title = '';
				$p->user_id = $scorm_ans_pre[$h]->userid;
				$p->content_id = $scorm_ans_pre[$h]->scoid;
			}
			$p->score = $p->score + intval($scorm_ans_pre[$h]->score);
			if ($scorm_ans_pre[$h]->status != 'completed' && $scorm_ans_pre[$h]->status != 'passed') { $p->status = 0; }
			if ($h == (count($scorm_ans_pre) - 1)) { $scorm_ans[] = $p; }
			$h ++;
		}
	} else {
		$h = 0;
		$first_step = true;
		//$p = new stdClass();$p->status = 1;$p->score = 0;$p->user_id = 0;$p->content_id = 0;
		while ( $h < count($scorm_ans_pre) ) {
			$p = new stdClass();
			$p->status = 1;
			$p->score = 0;
			$p->sco_count = $scos_count;
			$p->sco_identifier = $scorm_ans_pre[$h]->sco_identifier;
			$p->sco_title = $scorm_ans_pre[$h]->sco_title;
			$p->user_id = $scorm_ans_pre[$h]->user_id;
			$p->content_id = $scorm_ans_pre[$h]->content_id;
			$p->score = intval($scorm_ans_pre[$h]->score);
			if ($scorm_ans_pre[$h]->status != 'completed' && $scorm_ans_pre[$h]->status != 'passed') { $p->status = 0; }
			$scorm_ans[] = $p;
			$h ++;
		}
	}
	return $scorm_ans;*/
}
?>