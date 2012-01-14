<?php
/**
* ajax_quiz.class.php
* (c) JoomaLMS eLearning Software http://www.joomlalms.com/
**/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class JLMS_quiz_API extends JObject {
	var $quiz_id;
	var $inside_lp;
	var $quiz_time;
	var $quiz_data;
	var $question_list;
	var $is_valid = false;
	var $is_start_valid = false;
	var $is_time_out = false;
	var $user_time = 0;
	var $user_unique_id;
	var $stu_quiz_id;
	var $lang_name;
	// Max (31.01.08)
	var $mode_self;
	var $cats_id;
	var $pool_num;
	// Max (24.03.08)
	var $lpath_stu_quiz_id;
	var $lpath_start_id;
	var $lpath_unique_id;
	var $lpath_result_id;
	var $lpath_step_id;

	function __construct($quiz_id = 0, $inside_lp = 0) {
		if ($quiz_id) {
			global $JLMS_DB, $my;
			$this->quiz_id = intval($quiz_id);
			$this->inside_lp = $inside_lp;
			$this->quiz_time = time() - date('Z');
			$query = "SELECT * FROM #__lms_quiz_t_quiz WHERE c_id = ".$this->quiz_id;
			$JLMS_DB->SetQuery ($query );
			$quiz = $JLMS_DB->LoadObject();
			if (is_object($quiz)) {
				$this->quiz_data = $quiz;
				if ($quiz->published) {
					if ( ($my->id) ) {
						$this->is_valid = true;
					} elseif ($quiz->c_guest) {
						$this->is_valid = true;
					}
				} else {
					$JLMS_ACL = & JLMSFactory::getACL();
					if ($JLMS_ACL->CheckPermissions('quizzes', 'view_all')) {
						$this->is_valid = true;
					}
				}
			}
		}
	}

	function get_qvar($var, $default = '') {
		if ($var) {
			if (isset($this->quiz_data->$var)) {
				return $this->quiz_data->$var;
			}
		}
		return $default;
	}

	function quiz_valid() {
		return $this->is_valid;
	}

	function start_valid() {
		return $this->is_start_valid;
	}

	function time_is_up() {
		return $this->is_time_out;
	}

	function get_user_time() {
		return $this->user_time;
	}

	function quiz_Gen_UID() {
		$this->user_unique_id = md5(uniqid(rand(), true));
		//return $this->user_unique_id;
	}
	
	function mode_self_verification_data($mode_self, $cats_id, $pool_num){
		$this->mode_self = $mode_self;
		$this->cats_id = $cats_id;
		$this->pool_num = $pool_num;
	}

	function quiz_New_Start() {
		global $JLMS_DB, $my, $JLMS_CONFIG;
		$query = "INSERT INTO #__lms_quiz_r_student_quiz (c_quiz_id, c_student_id, c_total_score, c_total_time, c_date_time, c_passed, unique_id)"
		. "\n VALUES('".$this->quiz_id."', '".$my->id."', '0', '0', '".date('Y-m-d H:i:s', $this->quiz_time)."', '0', '".$this->user_unique_id."')";
		$JLMS_DB->SetQuery($query);
		$JLMS_DB->query();
		$this->stu_quiz_id = $JLMS_DB->insertid();

		/* 24 April 2007 (DEN) - Question pool MOD
		 * Here we should generate list of quiz questions and save it to DB (observe ordering and randomize if necessary)
		 * If quiz contains questions from pool - extract them and save too.
		 */
		$query = "SELECT c_id, c_pool, c_pool_gqp FROM #__lms_quiz_t_question WHERE c_quiz_id = ".$this->quiz_id." ORDER BY ordering, c_id";
		$JLMS_DB->SetQuery($query);
		$quiz_quests_list = $JLMS_DB->LoadObjectList();
		
		$quiz_quests = array(); // List of all Quiz questions (will be populated here)
		$quiz_pool_restrict = array(); // contains questions, which are already added to quiz from Pool (we couldn't add them again)
		foreach ($quiz_quests_list as $qql) {
			$quiz_quests[] = $qql->c_id;
			if ($qql->c_pool) {
				$quiz_pool_restrict[] = $qql->c_pool;
			}
		}
		if($this->mode_self == 0) {
			$query = "SELECT * FROM #__lms_quiz_t_quiz_pool WHERE quiz_id = ".$this->quiz_id;
			$JLMS_DB->SetQuery($query);
			$quiz_pool = $JLMS_DB->LoadObjectList();
			$quiz_pool_cats = array();
			$quiz_pool_free_num = 0;
			foreach ($quiz_pool as $qp) {
				if ($qp->qcat_id) {
					$qc = new stdClass();
					$qc->qcat_id = $qp->qcat_id;
					$qc->items_number = $qp->items_number;
					$quiz_pool_cats[] = $qc;
				} else {
					$quiz_pool_free_num = intval($qp->items_number);
				}
			}
		} else {
			$quiz_pool_free_num = 0;
			if($this->mode_self == 1){
				$quiz_pool_free_num = intval($this->pool_num);
			} else if($this->mode_self == 2){
				$arr_cats_id = array();
				$arr_cats_id = explode(",", $this->cats_id);
				$arr_pool_num = array();
				$arr_pool_num = explode(",", $this->pool_num);
				
				for($i=0; $i<count($arr_cats_id);$i++){
					$qc = new stdClass();
					$qc->qcat_id = $arr_cats_id[$i];
					$qc->items_number = $arr_pool_num[$i];
					$quiz_pool_cats[] = $qc;
				}
			} else if($this->mode_self == 3){
				$qc = new stdClass();
				$qc->qcat_id = $arr_cats_id;
				$qc->items_number = $arr_pool_num;
				$quiz_pool_cats[] = $qc;
			}
		}	
		
		if (!empty($quiz_pool_cats)) { // Adding questions from Pool by categories
			$q_cats = array();
			foreach ($quiz_pool_cats as $qpc) {
				if (!in_array($qpc->qcat_id, $q_cats)) {
					$q_cats[] = $qpc->qcat_id;
				}
			}
			$restrict_query = '';
			if (!empty($quiz_pool_restrict)) {
				$qpr = implode(',',$quiz_pool_restrict);
				$restrict_query = " AND c_id NOT IN ($qpr)";
			}
			$rrr = implode(',',$q_cats);
			$query = "SELECT * FROM #__lms_quiz_t_question WHERE course_id = ".$this->get_qvar('course_id')." AND c_quiz_id = 0 AND c_qcat IN ($rrr)".$restrict_query;
			$JLMS_DB->SetQuery($query);
			$qp_xm = $JLMS_DB->LoadObjectList();
			if (!empty($qp_xm)) {
				srand((float)microtime() * 1000000);
				shuffle($qp_xm);
				foreach ($quiz_pool_cats as $qpc) {
					$cur_cat_num = 0;
					$cur_cat_need = $qpc->items_number;
					foreach ($qp_xm as $qpxm) {
						if ($qpxm->c_qcat == $qpc->qcat_id) {
							if ($cur_cat_num >= $cur_cat_need) {
								break;
							} else {
								if (!in_array($qpxm->c_id, $quiz_quests)) {
									$quiz_quests[] = $qpxm->c_id;// Add to the List of questions
									if (!in_array($qpxm->c_id, $quiz_pool_restrict)) {
										$quiz_pool_restrict[] = $qpxm->c_id;// Add to the list of restrict questions
									}
									$cur_cat_num ++;
								}
							}
						}
					}
				}
			}
		}
		if ($quiz_pool_free_num) { // Adding questions from Pool by number of questions
			$restrict_query = '';
			if (!empty($quiz_pool_restrict)) {
				$qpr = implode(',',$quiz_pool_restrict);
				$restrict_query = " AND c_id NOT IN ($qpr)";
			}
			$query = "SELECT * FROM #__lms_quiz_t_question WHERE course_id = ".$this->get_qvar('course_id')." AND c_quiz_id = 0".$restrict_query;
			$JLMS_DB->SetQuery($query);
			$qp_xm = $JLMS_DB->LoadObjectList();
			if (!empty($qp_xm)) {
				srand((float)microtime() * 1000000);
				shuffle($qp_xm);
				$cur_cat_num = 0;
				foreach ($qp_xm as $qpxm) {
					if ($cur_cat_num >= $quiz_pool_free_num) {
						break;
					} else {
						if (!in_array($qpxm->c_id, $quiz_quests)) {
							$quiz_quests[] = $qpxm->c_id;// Add to the List of questions
							if (!in_array($qpxm->c_id, $quiz_pool_restrict)) {
								$quiz_pool_restrict[] = $qpxm->c_id;// Add to the list of restrict questions
							}
							$cur_cat_num ++;
						}
					}
				}
			}
		}
//		 Now we have a list of all quiz questions in array $quiz_quests;
//		 If quiz have randomize option - we must to randomize this list.
//		if ($this->get_qvar('c_random')) {
//			srand((float)microtime() * 1000000);
//			shuffle($quiz_quests);
//		}
//		 And now save order of questions into DB
//		
//		if (!empty($quiz_quests)) {
//			$query = "INSERT INTO #__lms_quiz_r_student_quiz_pool (start_id, quest_id, ordering) VALUES ";
//			$i = 0;
//			$divider = '';
//			foreach ($quiz_quests as $qq) {
//				$query .= $divider . "\n ($this->stu_quiz_id, $qq, $i)";
//				$divider = ',';
//				$i ++;
//			}
//			
//			$JLMS_DB->SetQuery($query);
//			$JLMS_DB->query();
//		}
		/* END of Question Pool MOD */

		$quiz_quests_temp = $quiz_quests;

		//------------------------global question pool by kosmos
		if ($JLMS_CONFIG->get('global_quest_pool')) {
			unset($quiz_quests);
//			$query = "SELECT a.c_id, a.c_pool, a.c_pool_gqp"
//			. "\n FROM #__lms_quiz_t_question AS a, #__lms_gqp_levels d"
//			. "\n WHERE a.c_quiz_id = ".$this->quiz_id." AND (d.quest_id = a.c_id OR d.quest_id = a.c_pool_gqp) AND a.published = 1 ORDER BY a.ordering, a.c_id";
			//New categories for GQP (Max) - 18.12.2009
			$query = "SELECT a.c_id, a.c_pool, a.c_pool_gqp"
			. "\n FROM #__lms_quiz_t_question AS a" //, #__lms_gqp_levels d" //by Max
			. "\n WHERE a.c_quiz_id = ".$this->quiz_id
//			. "\n AND (d.quest_id = a.c_id OR d.quest_id = a.c_pool_gqp)" //by Max
			. "\n AND a.c_type = 21"
			. "\n AND a.c_pool_gqp > 0"
			. "\n AND a.published = 1"
			. "\n ORDER BY a.ordering, a.c_id";
			$JLMS_DB->SetQuery($query);
			$quiz_quests_list = $JLMS_DB->LoadObjectList();
			
//			$quiz_quests = array(); // List of all Quiz questions (will be populated here)
//			$quiz_pool_restrict = array(); // contains questions, which are already added to quiz from Pool (we couldn't add them again)
			$quiz_pool_restrict_gqp = array();
			$quiz_quests = array();
			//$quiz_quests_list = array();
			
			foreach ($quiz_quests_list as $qql) {
				//$quiz_quests[] = $qql->c_id;
				if ($qql->c_pool_gqp) {
					$quiz_pool_restrict_gqp[] = $qql->c_pool_gqp;
				}
			}
			
			if($this->mode_self == 0){
				$query = "SELECT *"
				. "\n FROM #__lms_quiz_t_quiz_gqp"
				. "\n WHERE 1"
				. "\n AND quiz_id = ".$this->quiz_id
				. "\n AND items_number > 0"
				//. "\n ORDER BY orderin DESC"
				;
				$JLMS_DB->SetQuery($query);
				$quiz_pool_gqp = $JLMS_DB->LoadObjectList();
				
				$quiz_pool_cats_gqp = array();
				$quiz_pool_free_num_gqp = 0;
				foreach ($quiz_pool_gqp as $qp) {
					if ($qp->qcat_id) {
						$qc = new stdClass();
						$qc->qcat_id = $qp->qcat_id;
						$qc->items_number = $qp->items_number;
						$quiz_pool_cats_gqp[] = $qc;
					} else {
						$quiz_pool_free_num_gqp = intval($qp->items_number);
					}
				}
				
			} else {
				$quiz_pool_free_num_gqp = 0;
				if($this->mode_self == 1){
					$quiz_pool_free_num_gqp = intval($this->pool_num_gqp);
				} else if($this->mode_self == 2){
					$arr_cats_id_gqp = array();
					$arr_cats_id_gqp = explode(",", $this->cats_id_gqp);
					$arr_pool_num_gqp = array();
					$arr_pool_num_gqp = explode(",", $this->pool_num_gqp);
					
					for($i=0; $i<count($arr_cats_id_gqp);$i++){
						$qc = new stdClass();
						$qc->qcat_id = $arr_cats_id_gqp[$i];
						$qc->items_number = $arr_pool_num_gqp[$i];
						$quiz_pool_cats_gqp[] = $qc;
					}
				} else if($this->mode_self == 3){
					$qc = new stdClass();
					$qc->qcat_id = $arr_cats_id_gqp;
					$qc->items_number = $arr_pool_num_gqp;
					$quiz_pool_cats_gqp[] = $qc;
				}
			}
			
			
			///////////////////////FIX GQP Questions///////////////////////
			$query = "SELECT *"
			. "\n FROM #__lms_gqp_cats"
			. "\n WHERE 1"
			;
			$JLMS_DB->setQuery($query);
			$gqp_cats = $JLMS_DB->loadObjectList();
			
			$childs = array();
			for($i=0;$i<count($quiz_pool_cats_gqp);$i++){
				$a = $quiz_pool_cats_gqp[$i];
			
				$childs_ids = array();
				$childs_ids[] = $a->qcat_id;
				$temp_childs = $this->AllChilds($a->qcat_id, $gqp_cats, $childs_ids);
				
				//$a->child_qcat_ids = $temp_childs;
				
				$childs = array_merge($childs, $temp_childs);
			}
			$childs = array_unique($childs);
			//////////////////////////////////////////////
			
			if (!empty($quiz_pool_cats_gqp)) { // Adding questions from Pool by categories
					
				$q_cats_gqp = array();
				foreach ($quiz_pool_cats_gqp as $qpc) {
					if (!in_array($qpc->qcat_id, $q_cats_gqp)) {
						$q_cats_gqp[] = $qpc->qcat_id;
					}
				}
				
				$restrict_query = '';
				if (!empty($quiz_pool_restrict_gqp)) {
					$qpr = implode(',',$quiz_pool_restrict_gqp);
					$restrict_query = " AND c_id NOT IN ($qpr)";
				}
				$rrr = implode(',', $q_cats_gqp);
				
				$rrr = implode(',', $childs); //FIX GQP Questions
				
				$query = "SELECT a.*"
				. "\n FROM #__lms_quiz_t_question AS a"
				. "\n WHERE 1"
				. "\n AND a.course_id = 0"
				. "\n AND a.c_quiz_id = 0"
				. "\n AND a.published = 1"
				. "\n AND a.c_qcat IN ($rrr)"
				. $restrict_query
				;
				$JLMS_DB->SetQuery($query);
				$qp_xm = $JLMS_DB->LoadObjectList();
				
				if (!empty($qp_xm)) {
					
					srand((float)microtime() * 1000000);
					shuffle($qp_xm);
					
					foreach ($quiz_pool_cats_gqp as $qpc) {
						$cur_cat_num_gqp = 0;
						$cur_cat_need_gqp = $qpc->items_number;
						foreach ($qp_xm as $qpxm) {
//							if ($qpxm->c_qcat == $qpc->qcat_id) {
								if ($cur_cat_num_gqp >= $cur_cat_need_gqp) {
									break;
								} else {
									if (!in_array($qpxm->c_id, $quiz_quests)) {
										$quiz_quests[] = $qpxm->c_id;// Add to the List of questions
										if (!in_array($qpxm->c_id, $quiz_pool_restrict_gqp)) {
											$quiz_pool_restrict_gqp[] = $qpxm->c_id;// Add to the list of restrict questions
										}
										$cur_cat_num_gqp ++;
									}
								}
//							}
						}
					}
				}
			}
			
			if ($quiz_pool_free_num_gqp) { // Adding questions from Pool by number of questions
				$restrict_query = '';
				if (!empty($quiz_pool_restrict_gqp)) {
					$qpr = implode(',',$quiz_pool_restrict_gqp);
					$restrict_query = " AND a.c_id NOT IN ($qpr)";
				}
//				$query = "SELECT a.*"
//				. "\n FROM #__lms_quiz_t_question AS a, #__lms_gqp_levels d"
//				 ."\n WHERE a.course_id = 0 AND d.quest_id = a.c_id AND a.published = 1 AND a.c_quiz_id = 0".$restrict_query;
				$query = "SELECT a.*"
				. "\n FROM #__lms_quiz_t_question AS a"
				. "\n WHERE 1"
				. "\n AND a.course_id = 0"
				. "\n AND a.c_quiz_id = 0"
				. "\n AND a.published = 1"
				. $restrict_query
				;
				$JLMS_DB->SetQuery($query);
				$qp_xm = $JLMS_DB->LoadObjectList();
				
				if (!empty($qp_xm)) {
					srand((float)microtime() * 1000000);
					shuffle($qp_xm);
					$cur_cat_num_gqp = 0;
					foreach ($qp_xm as $qpxm) {
						if ($cur_cat_num_gqp >= $quiz_pool_free_num_gqp) {
							break;
						} else {
							if (!in_array($qpxm->c_id, $quiz_quests)) {
								$quiz_quests[] = $qpxm->c_id;// Add to the List of questions
								if (!in_array($qpxm->c_id, $quiz_pool_restrict_gqp)) {
									$quiz_pool_restrict_gqp[] = $qpxm->c_id;// Add to the list of restrict questions
								}
								$cur_cat_num_gqp++;
							}
						}
					}
				}
			}
			
			// Now we have a list of all quiz questions in array $quiz_quests;
			// If quiz have randomize option - we must to randomize this list.
//			if ($this->get_qvar('c_random')) {
//				srand((float)microtime() * 1000000);
//				shuffle($quiz_quests);
//			}
			
			// And now save order of questions into DB
//			if (!empty($quiz_quests)) {
//				$query = "INSERT INTO #__lms_quiz_r_student_quiz_gqp (start_id, quest_id, ordering) VALUES ";
//				$i = 0;
//				$divider = '';
//				foreach ($quiz_quests as $qq) {
//					$query .= $divider . "\n ($this->stu_quiz_id, $qq, $i)";
//					$divider = ',';
//					$i ++;
//				}
//				
//				$JLMS_DB->SetQuery($query);
//				$JLMS_DB->query();
//			}
			

			$quiz_quests = array_merge ($quiz_quests, $quiz_quests_temp);
		}
		
//		 Now we have a list of all quiz questions in array $quiz_quests;
//		 If quiz have randomize option - we must to randomize this list.
		if ($this->get_qvar('c_random')) {
			srand((float)microtime() * 1000000);
			shuffle($quiz_quests);
		}
//		 And now save order of questions into DB

		if (!empty($quiz_quests)) {
			$query = "INSERT INTO #__lms_quiz_r_student_quiz_pool (start_id, quest_id, ordering) VALUES ";
			$i = 0;
			$divider = '';
			foreach ($quiz_quests as $qq) {
				$query .= $divider . "\n ($this->stu_quiz_id, $qq, $i)";
				$divider = ',';
				$i ++;
			}
			
			$JLMS_DB->SetQuery($query);
			$JLMS_DB->query();
		}
		
		//------------------------end global quetion pool
		if ($this->inside_lp) {
			$lpath_id = intval( mosGetParam( $_REQUEST, 'lpath_id', 0 ) );
			$step_id = intval( mosGetParam( $_REQUEST, 'step_id', 0 ) );
			$result_id = intval( mosGetParam( $_REQUEST, 'user_start_id', 0 ) );
			$result_uniq = strval( mosGetParam( $_REQUEST, 'lp_user_unique_id', '' ) );
			// TO DO: ! check validity of these vars
			if ($lpath_id && $result_id && $result_uniq) {
				$query = "SELECT id FROM #__lms_learn_path_results WHERE user_id = '".$my->id."'"
				. "\n AND course_id = '".$this->get_qvar('course_id')."' AND lpath_id = '".$lpath_id."'";
				$JLMS_DB->SetQuery( $query );
				$res_id = $JLMS_DB->LoadResult();

				$query = "DELETE FROM #__lms_learn_path_step_quiz_results"
				. "\n WHERE result_id = $res_id AND step_id = $step_id";// AND stu_quiz_id = ".$this->stu_quiz_id;
				$JLMS_DB->SetQuery($query);
				$JLMS_DB->query();

				$query = "INSERT INTO #__lms_learn_path_step_quiz_results (result_id, step_id, stu_quiz_id, start_id, unique_id)"
				. "\n VALUES('".$res_id."', '".$step_id."', '".$this->stu_quiz_id."', '".$result_id."', '".$result_uniq."')";
				$JLMS_DB->SetQuery($query);
				$JLMS_DB->query();
			}
		}
		//return $this->stu_quiz_id;
	}
	
	//FIX GQP Questions
	function AllChilds($id, &$cats, $child_ids){
		if(count($cats)){
			foreach($cats as $cat){
				if($id == $cat->parent){
					$child_ids[] = $cat->id;
					
					$child_ids = $this->AllChilds($cat->id, $cats, $child_ids);
				} 
			}
		}
		return $child_ids;
	}

	function quiz_Fill_QuestionList() {
		if (empty($this->question_list)) {
			global $JLMS_DB, $JLMS_CONFIG;
		
			$query = "SELECT c_pool_gqp"
			. "\n FROM #__lms_quiz_t_question"
			. "\n WHERE 1"
			. "\n AND c_pool_gqp > '0'"
			. "\n AND published = 1"
			. "\n AND course_id = '".$this->get_qvar('course_id')."'"
			. "\n AND c_quiz_id = '".$this->quiz_id."'" //Quizzes and Pool fix (Max) - 17.01.2011
			;
			$JLMS_DB->setQuery( $query );
			$result_array = $JLMS_DB->loadResultArray();
		
			if(count($result_array)){	
				$sql_use_ids = "\n AND a.c_id NOT IN (".implode(',', $result_array).")";
			} else {
				$sql_use_ids = '';	
			}
			
			if ($JLMS_CONFIG->get('global_quest_pool')){

//				$query = "(SELECT a.*, c.c_correct FROM #__lms_quiz_t_question as a, #__lms_quiz_r_student_quiz_pool as b"
//				. "\n LEFT JOIN #__lms_quiz_r_student_question as c ON (c.c_stu_quiz_id = b.start_id AND c.c_question_id = b.quest_id)" //, #__lms_quiz_r_student_question as c
//				. "\n WHERE b.start_id = $this->stu_quiz_id AND b.quest_id = a.c_id AND a.course_id = ".$this->get_qvar('course_id').""
//				. "\n AND a.published = '1'"
//				. ( $sql_use_ids ? $sql_use_ids : ' ')
//				. "\n ORDER BY b.ordering)"
//				. "\n UNION"
//				. "\n (SELECT a.*, c.c_correct FROM #__lms_quiz_t_question as a, #__lms_quiz_r_student_quiz_gqp as b"
//				. "\n LEFT JOIN #__lms_quiz_r_student_question as c ON (c.c_stu_quiz_id = b.start_id AND c.c_question_id = b.quest_id)" //, #__lms_quiz_r_student_question as c
//				. "\n WHERE b.start_id = $this->stu_quiz_id AND b.quest_id = a.c_id AND a.course_id = 0"
//				. "\n AND a.published = '1'"
//				. ( $sql_use_ids ? $sql_use_ids : ' ')
//				. "\n ORDER BY b.ordering)"
//				. "\n ORDER BY ordering"
//				;
				
				$query = "SELECT a.*, c.c_correct FROM #__lms_quiz_t_question as a, #__lms_quiz_r_student_quiz_pool as b"
				. "\n LEFT JOIN #__lms_quiz_r_student_question as c ON (c.c_stu_quiz_id = b.start_id AND c.c_question_id = b.quest_id)" //, #__lms_quiz_r_student_question as c
				. "\n WHERE b.start_id = ".intval($this->stu_quiz_id)." AND b.quest_id = a.c_id"
				. "\n AND (a.course_id = '0' OR a.course_id = '".$this->get_qvar('course_id')."')" //Quizzes and Pool fix (Max) - 10.01.2011
				. "\n AND a.published = '1'"
				. ( $sql_use_ids ? $sql_use_ids : ' ')
				. "\n ORDER BY b.ordering"
				;
				
			} else {			
				$query = "SELECT a.*, c.c_correct FROM #__lms_quiz_t_question as a, #__lms_quiz_r_student_quiz_pool as b"
				. "\n LEFT JOIN #__lms_quiz_r_student_question as c ON (c.c_stu_quiz_id = b.start_id AND c.c_question_id = b.quest_id)" //, #__lms_quiz_r_student_question as c
				. "\n WHERE b.start_id = $this->stu_quiz_id AND b.quest_id = a.c_id AND a.course_id = ".$this->get_qvar('course_id').""
				. "\n AND a.published = '1'"
				. "\n ORDER BY b.ordering"
				;
			}
			$JLMS_DB->SetQuery($query);
			$this->question_list = $JLMS_DB->LoadObjectList();
				
			if ($JLMS_DB->geterrormsg()) {
				// fix for 1.0.5 installation problems
				$query_fix1 = "ALTER TABLE `#__lms_quiz_r_student_question` ADD `c_correct` INT DEFAULT '0' NOT NULL";
				$JLMS_DB->SetQuery($query_fix1);
				$JLMS_DB->query();
	
				$query_fix2 = "ALTER TABLE `#__lms_learn_path_step_quiz_results` ADD `start_id` INT NOT NULL DEFAULT '0', ADD `unique_id` VARCHAR( 32 ) NOT NULL DEFAULT '0';";
				$JLMS_DB->SetQuery($query_fix2);
				$JLMS_DB->query();

				$JLMS_DB->SetQuery($query);
				$this->question_list = $JLMS_DB->LoadObjectList();
			}
			
			
			/* --------------QP-----------------*/
			$q_from_pool = array();
			foreach ($this->question_list as $row) {
				if ($row->c_type == 20) {
					$q_from_pool[] = $row->c_pool;
				}
			}
			if (count($q_from_pool)) {
				$qp_ids =implode(',',$q_from_pool);
				$query = "SELECT a.* FROM #__lms_quiz_t_question as a"
				. "\n WHERE a.course_id = ".$this->get_qvar('course_id')
				. "\n AND a.published = '1'"
				;
				$JLMS_DB->setQuery( $query );
				$rows2 = $JLMS_DB->loadObjectList();
				for ($i=0, $n=count( $this->question_list ); $i < $n; $i++) {
					if ($this->question_list[$i]->c_type == 20) {
						for ($j=0, $m=count( $rows2 ); $j < $m; $j++) {
							if ($this->question_list[$i]->c_pool == $rows2[$j]->c_id) {
								$this->question_list[$i]->c_question = $rows2[$j]->c_question;
								$this->question_list[$i]->c_point = $rows2[$j]->c_point;
								$this->question_list[$i]->c_attempts = $rows2[$j]->c_attempts;
								$this->question_list[$i]->c_type = $rows2[$j]->c_type;
								$this->question_list[$i]->c_image = $rows2[$j]->c_image;

								// added 18.05.2007 - params of Pool question. (get 'Disable feedback' option of pool question).
								$this->question_list[$i]->params = $rows2[$j]->params;

								$this->question_list[$i]->c_explanation = $rows2[$j]->c_explanation;

								break;
							}
						}
					}
				}
			}
			
			/*---------------GQP-----------------*/
			$q_from_pool_gqp = array();
			foreach ($this->question_list as $row) {
				if ($row->c_type == 21) {
					$q_from_pool_gqp[] = $row->c_pool_gqp;
				}
			}
			if (count($q_from_pool_gqp)) {
				$qp_ids_gqp =implode(',',$q_from_pool_gqp);
				$query = "SELECT a.* FROM #__lms_quiz_t_question as a"
				. "\n WHERE a.course_id = 0"
				. "\n AND a.published = '1'"
				;
				$JLMS_DB->setQuery( $query );
				$rows2 = $JLMS_DB->loadObjectList();
				for ($i=0, $n=count( $this->question_list ); $i < $n; $i++) {
					if ($this->question_list[$i]->c_type == 21) {
						for ($j=0, $m=count( $rows2 ); $j < $m; $j++) {
							if ($this->question_list[$i]->c_pool_gqp == $rows2[$j]->c_id) {
								$this->question_list[$i]->c_question = $rows2[$j]->c_question;
								$this->question_list[$i]->c_point = $rows2[$j]->c_point;
								$this->question_list[$i]->c_attempts = $rows2[$j]->c_attempts;
								$this->question_list[$i]->c_type = $rows2[$j]->c_type;
								$this->question_list[$i]->c_image = $rows2[$j]->c_image;

								// added 18.05.2007 - params of Pool question. (get 'Disable feedback' option of pool question).
								$this->question_list[$i]->params = $rows2[$j]->params;

								$this->question_list[$i]->c_explanation = $rows2[$j]->c_explanation;

								break;
							}
						}
					}
				}
			}
			/*-------------------------------------*/
		}
	}

	function quiz_Get_QuestionList() {
		if (empty($this->question_list)) {
			$this->quiz_Fill_QuestionList();
		}
		return $this->question_list;
	}

	function quiz_Get_MaxScore() {
		if (empty($this->question_list)) {
			$this->quiz_Fill_QuestionList();
		}
		$c_points = 0;
		foreach ($this->question_list as $tcl) {
			$c_points = $c_points + intval($tcl->c_point);
		}
		return $c_points;
	}

	function quiz_ProcessStartData() {
		if ($this->user_unique_id && $this->stu_quiz_id) {
			global $JLMS_DB, $my;
			$query = "SELECT c_quiz_id, c_student_id, unique_id, c_date_time FROM #__lms_quiz_r_student_quiz WHERE c_id = '".$this->stu_quiz_id."'";
			$JLMS_DB->SetQuery($query);
			$st_quiz_data = $JLMS_DB->LoadObject();
			if (!empty($st_quiz_data)) {
				$start_quiz = $st_quiz_data->c_quiz_id;
				if ( ($this->user_unique_id == $st_quiz_data->unique_id) && ($my->id == $st_quiz_data->c_student_id) && ($this->quiz_id == $st_quiz_data->c_quiz_id) ) {
					$this->is_start_valid = true;
					$user_time = $this->quiz_time - strtotime($st_quiz_data->c_date_time);
					$this->user_time = $user_time;
					if ($this->get_qvar('c_time_limit')) {
						if ($user_time > ($this->get_qvar('c_time_limit') * 60)) {
							$this->is_time_out = true;
						}
					}
				}
			}
			$query = "SELECT * FROM #__lms_learn_path_step_quiz_results WHERE stu_quiz_id = '".$this->stu_quiz_id."'";
			$JLMS_DB->setQuery($query);
			$lpath_on = $JLMS_DB->LoadObject();
			
			if(is_object($lpath_on)){
				$this->lpath_stu_quiz_id = $lpath_on->stu_quiz_id;
				$this->lpath_start_id = $lpath_on->start_id;
				$this->lpath_unique_id = $lpath_on->unique_id;
				$this->lpath_result_id = $lpath_on->result_id;
				$this->lpath_step_id = $lpath_on->step_id;
			}
		}
	}

	function quiz_Get_StartToolbar($quest_type=0, $skip_quest=0, $next_quest=0){
		$toolbar = array();
		if($quest_type && $quest_type == 10){
			$toolbar[] = array('btn_type' => 'next', 'btn_js' => "javascript:jq_QuizNextOn();void(0);");
		} else {
			$toolbar[] = array('btn_type' => 'quiz_ok', 'btn_js' => "javascript:jq_QuizNextOn();void(0);");
		}
		if($skip_quest && $next_quest){
			$toolbar[] = array('btn_type' => 'skip', 'btn_js' => "javascript:JQ_gotoQuestion(".$next_quest.");void(0);");
		}
		if ($this->inside_lp) {
			$toolbar[] = array('btn_type' => 'contents', 'btn_js' => "javascript:ajax_action('contents_lpath');");
		} else {
			if ($this->get_qvar('c_slide')) {
				$toolbar[] = array('btn_type' => 'contents', 'btn_js' => "javascript:jq_ShowPanel();");
			}
		}
		return $toolbar;
	}

	function quiz_Get_NoAtToolbar() {
		$toolbar = array();
		$toolbar[] = array('btn_type' => 'next', 'btn_js' => "javascript:jq_QuizContinue();void(0);");
		if ($this->inside_lp) {
			$toolbar[] = array('btn_type' => 'contents', 'btn_js' => "javascript:ajax_action('contents_lpath');");
		} else {
			if ($this->get_qvar('c_slide')) {
				$toolbar[] = array('btn_type' => 'contents', 'btn_js' => "javascript:jq_ShowPanel();");
			}
		}
		return $toolbar;
	}
}

class JLMS_quiz_ajax_class {
	function JQ_ajax_main( $jq_task ) {
		$jq_ret_str = '';
		
//		if($_SERVER['REMOTE_ADDR'] == '86.57.158.98' || $_SERVER['REMOTE_ADDR'] == '127.0.0.1'){
//			echo $jq_task; die;
//		}
		

		switch ($jq_task) {
			case 'start':			$jq_ret_str = JLMS_quiz_ajax_class::JQ_StartQuiz();		break;
			case 'resume_quiz':
			case 'next':			$jq_ret_str = JLMS_quiz_ajax_class::JQ_NextQuestion();	break;
			case 'finish_stop':		$jq_ret_str = JLMS_quiz_ajax_class::JQ_FinishQuiz();	break;
			case 'email_results':	$jq_ret_str = JLMS_quiz_ajax_class::JQ_emailResults();	break;
			case 'review_start':	$jq_ret_str = JLMS_quiz_ajax_class::JQ_StartReview();	break;
			case 'review_next':		$jq_ret_str = JLMS_quiz_ajax_class::JQ_NextReview();	break;
			case 'preview_quest':	$jq_ret_str = JLMS_quiz_ajax_class::JQ_QuestPreview();	break;
			case 'next_preview':	$jq_ret_str = JLMS_quiz_ajax_class::JQ_NextPreview();	break;
			case 'goto_quest':		$jq_ret_str = JLMS_quiz_ajax_class::JQ_SeekQuestion();	break;
			default:	break;
		}
		
		$iso = explode( '=', _ISO );
		echo "\n"."some notices :)";
		$debug_str = ob_get_contents();
		
		@ob_end_clean();
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
		if ($jq_ret_str != "") {
			echo '<?xml version="1.0" encoding="'.$iso[1].'" standalone="yes"?>';
			echo '<response>' . "\n";
			echo $jq_ret_str;
			echo "\t" . '<debug><![CDATA['.$debug_str.']]></debug>' . "\n";
			echo '</response>' . "\n";
		} else {
			echo '<?xml version="1.0" encoding="'.$iso[1].'" standalone="yes"?>';
			echo '<response>' . "\n";
			echo "\t" . '<task>failed</task>' . "\n";
			echo "\t" . '<info>boom</info>' . "\n";
			echo "\t" . '<debug><![CDATA['.$debug_str.']]></debug>' . "\n";
			echo '</response>' . "\n";
		}
		exit;
	}
function JQ_emailResults() {
	global $JLMS_DB, $my;

	// preloading QUIZ languge (28.02.2007 new method) (all quizzes messages now in global quiz language)
	global $JLMS_LANGUAGE, $JLMS_CONFIG;
	JLMS_require_lang($JLMS_LANGUAGE, 'quiz.lang', $JLMS_CONFIG->get('default_language'));
	//require(_JOOMLMS_FRONT_HOME . "/languages/".$JLMS_CONFIG->get('default_language').'/quiz.lang.php');
	require(_JOOMLMS_FRONT_HOME . '/includes/quiz/quiz_language.php');
	global $jq_language;

	$ret_str = '';
	$result = false;
	$stu_quiz_id = intval( mosGetParam( $_REQUEST, 'stu_quiz_id', 0 ) );
	$quiz_id = intval( mosGetParam( $_REQUEST, 'quiz', 0 ) );
	$user_unique_id = strval( mosGetParam( $_REQUEST, 'user_unique_id', '') );
	$query = "SELECT * FROM #__lms_quiz_r_student_quiz WHERE c_id = '".$stu_quiz_id."'";
	$JLMS_DB->SetQuery( $query );
	$stu_info = $JLMS_DB->LoadObjectList();
	if (count($stu_info)) {
		$stu_info = $stu_info[0];
		if ( ($user_unique_id == $stu_info->unique_id) && ($quiz_id == $stu_info->c_quiz_id) && ($my->id == $stu_info->c_student_id) ) {
			$query = "SELECT u.email, u.username, q.c_email_to, q.c_language"
			. "\n FROM #__lms_quiz_r_student_quiz sq, #__lms_quiz_t_quiz q LEFT JOIN #__users u ON  q.c_user_id = u.id"
			. "\n WHERE sq.c_id = '".$stu_quiz_id."' AND sq.c_quiz_id = q.c_id";
			$JLMS_DB->setQuery( $query );
			$rows = $JLMS_DB->loadObjectList();
			// u.email - author email
			if (count($rows)) {
/*				if ( ($rows[0]->c_language) && ($rows[0]->c_language != 1) ) {
					$query = "SELECT lang_file FROM #__lms_quiz_languages WHERE id = '".intval($rows[0]->c_language)."'";
					$JLMS_DB->SetQuery( $query );
					$req_lang = $JLMS_DB->LoadResult();
					if ($req_lang && file_exists( dirname(__FILE__) . "/language/".$req_lang.".php")) {
						include( dirname(__FILE__) . "/language/".$req_lang.".php");
					}
				}
*/				if ($rows[0]->c_email_to) {
					$email_address = '';
					if ($rows[0]->c_email_to == 2) {
						$query = "SELECT email FROM #__users WHERE id = '".$my->id."'";
						$JLMS_DB->SetQuery( $query );
						$email_address = $JLMS_DB->LoadResult();//strval( mosGetParam( $_REQUEST, 'email_address', '') );
					} else {
						$email_address = $rows[0]->email;
					}
					require_once(dirname(__FILE__)."/joomlaquiz.manageresults.php");
					$result = JQ_Email($stu_quiz_id, $email_address);
				}
			}
		}
	}
	$ret_str .= "\t" . '<task>email_results</task>' . "\n";
	if ($result) $ret_str .= "\t" . '<email_msg>'.$jq_language['quiz_mes_email_ok'].'</email_msg>' . "\n";
	else $ret_str .= "\t" . '<email_msg>'.$jq_language['quiz_mes_email_fail'].'</email_msg>' . "\n";
	return $ret_str;
}

function JQ_params($quiz_id){
	global $JLMS_DB;

	$query = "SELECT * FROM #__lms_quiz_t_quiz WHERE c_id = '".$quiz_id."'";
	$JLMS_DB->SetQuery ($query );
	$quiz = $JLMS_DB->LoadObjectList();
	$quiz_params = new JLMSParameters($quiz->params);
	
	$obj_return = new stdClass();
	$obj_return->quiz_params = $quiz_params;
	
	return $obj_return;
}

function JQ_StartQuiz() {
	global $JLMS_DB, $my;
	$JLMS_CONFIG = & JLMSFactory::getConfig();
	$ret_str = '';
	
	$quiz_id = intval( mosGetParam( $_REQUEST, 'quiz', 0 ) );
	$inside_lp = intval( mosGetParam( $_REQUEST, 'inside_lp', 0 ) );
	
	$mode_self = intval( mosGetParam( $_REQUEST, 'mode_self', 0 ) );
	$cats_id = mosGetParam( $_REQUEST, 'cats_id', 0 );
	$pool_num = mosGetParam( $_REQUEST, 'pool_num', 0 );
	
	$skip_question = 0;
	
	$QA = new JLMS_quiz_API($quiz_id, $inside_lp);
	if (!$QA->quiz_valid()) {
		return '';
	}
	$QA->quiz_Gen_UID();
	$QA->mode_self_verification_data($mode_self, $cats_id, $pool_num);
	$QA->quiz_New_Start();
	$q_data = $QA->quiz_Get_QuestionList();
	
	// preloading QUIZ languge (28.02.2007 new method) (all quizzes messages now in global quiz language)
	global $JLMS_LANGUAGE, $JLMS_CONFIG;
	JLMS_require_lang($JLMS_LANGUAGE, 'quiz.lang', $JLMS_CONFIG->get('default_language'));
	//require(_JOOMLMS_FRONT_HOME . "/languages/".$JLMS_CONFIG->get('default_language').'/quiz.lang.php');
	require(_JOOMLMS_FRONT_HOME . '/includes/quiz/quiz_language.php');
	global $jq_language;
	
	$kol_quests = count($q_data);
	
	if ($kol_quests > 0) {
		//Max: modign skip question
		$quiz_params = new JLMSParameters($QA->quiz_data->params);
		$skip_quest = $quiz_params->get('sh_skip_quest', 0);
		$num_quest = 0;
		$next_num_quest = $num_quest + 1;
		$next_quest = isset($q_data[$next_num_quest]->c_id)?$q_data[$next_num_quest]->c_id:0;
	
		$toolbar = $QA->quiz_Get_StartToolbar($q_data[0]->c_type, $skip_quest, $next_quest);
		$toolbar_code = JLMS_ShowToolbar($toolbar);
		$toolbar_code = str_replace('"components/com_joomla_lms','"'.$JLMS_CONFIG->get('live_site').'/components/com_joomla_lms',$toolbar_code);
	
		$ret_str .= "\t" . '<task>start</task>' . "\n";
		$ret_str .= "\t" . '<quiz_menu><![CDATA['.$toolbar_code.']]></quiz_menu>' . "\n";
		$ret_str .= "\t" . '<stu_quiz_id>'.$QA->get('stu_quiz_id',0).'</stu_quiz_id>' . "\n";
		$ret_str .= "\t" . '<user_unique_id>'.$QA->get('user_unique_id','').'</user_unique_id>' . "\n";

		$quest_num = 0;
		# commented 25 April 2007 (DEN)
		# we've already randomized auestions in the sequence
		/*if ($QA->get_qvar('c_random')) {
			$quest_num = rand(0, ($kol_quests - 1) );
		}*/
		$ret_str .= "\t" . '<quiz_count_quests>'.$kol_quests.'</quiz_count_quests>' . "\n";
		$ret_str .= "\t" . '<quiz_quest_num>1</quiz_quest_num>' . "\n";

		$quiz_time_limit = intval($QA->get_qvar('c_time_limit')) * 60;
		$exec_start_script = 'max_quiz_time = '.$quiz_time_limit.';';

		$ret_str .= JLMS_quiz_ajax_class::JQ_GetQuestData($q_data[$quest_num], $jq_language, $QA->get('stu_quiz_id',0), $exec_start_script);

		$ret_str .= JLMS_quiz_ajax_class::JQ_GetPanelData($quiz_id, $q_data);
		if ($inside_lp) {
			$course_id = $QA->get_qvar('course_id', 0);
			$ret_str .= JLMS_quiz_ajax_class::JQ_GetPanelData_LP($quiz_id, $course_id, $q_data);
		}
	} else { $ret_str = ''; }

	return $ret_str;
}
function JQ_StartReview() {
	global $JLMS_DB, $my;
	$JLMS_CONFIG = & JLMSFactory::getConfig();
	$ret_str = '';

	$quiz_id = intval( mosGetParam( $_REQUEST, 'quiz', 0 ) );
	$query = "SELECT * FROM #__lms_quiz_t_quiz WHERE c_id = '".$quiz_id."'";
	$JLMS_DB->SetQuery ($query );
	$quiz = $JLMS_DB->LoadObjectList();
	if (count($quiz)) {
		$quiz = $quiz[0];
	} else { return $ret_str; }
	$quiz_params = new JLMSParameters($quiz->params);
	$now = date( 'Y-m-d H:i:s', time() - date('Z') );
	if ( ($quiz->published) ) {
		if ( ($my->id) ) {
		} elseif ($quiz->c_guest) {
		} else { return $ret_str; }
	} else {
		$JLMS_ACL = & JLMSFactory::getACL();
		if (!$JLMS_ACL->CheckPermissions('quizzes', 'view_all')) {
			return $ret_str;
		}
	}

	if ($quiz_id) {
		$stu_quiz_id = intval( mosGetParam( $_REQUEST, 'stu_quiz_id', 0 ) );
		$user_unique_id = strval( mosGetParam( $_REQUEST, 'user_unique_id', '') );
		if ($stu_quiz_id) {
			$query = "SELECT c_quiz_id, c_student_id, unique_id, allow_review, c_passed FROM #__lms_quiz_r_student_quiz WHERE c_id = '".$stu_quiz_id."'";
			$JLMS_DB->SetQuery($query);
			$st_quiz_data = $JLMS_DB->LoadObjectList();

			$start_quiz = 0;
			if (count($st_quiz_data)) {
				$start_quiz = $st_quiz_data[0]->c_quiz_id;
			} else { return ''; }
			if ($user_unique_id != $st_quiz_data[0]->unique_id) { return ''; }
			if ($my->id != $st_quiz_data[0]->c_student_id) { return ''; }
			if ($start_quiz != $quiz_id) { return '';}
			if (!$st_quiz_data[0]->allow_review) { return ''; }

			/*$query = "SELECT * FROM #__lms_quiz_t_question WHERE c_quiz_id = '".$quiz_id."' ORDER BY ordering, c_id";
			$JLMS_DB->SetQuery($query);
			$q_data = $JLMS_DB->LoadObjectList();*/
			$q_data = array();
			// 22.04.2008 Bugfix - support for Questions pool
			$inside_lp = intval(mosGetParam($_REQUEST, 'inside_lp', 0));
			$QA = new JLMS_quiz_API($quiz_id, $inside_lp);
			if (!$QA->quiz_valid()) {
				return '';
			}
			$QA->set('stu_quiz_id', $stu_quiz_id);
			$QA->set('user_unique_id', $user_unique_id);
			$QA->quiz_ProcessStartData();
		
			if ( $QA->start_valid()) {
				$q_data = $QA->quiz_Get_QuestionList();
			}

			$cur_tmpl = 'joomlaquiz_lms_template';
			require_once(dirname(__FILE__) . '/templates/'.$cur_tmpl.'/jq_template.php');
			//require( dirname(__FILE__) . "/language/english.php");
			// preloading QUIZ languge (28.02.2007 new method) (all quizzes messages now in global quiz language)
			global $JLMS_LANGUAGE, $JLMS_CONFIG;
			JLMS_require_lang($JLMS_LANGUAGE, 'quiz.lang', $JLMS_CONFIG->get('default_language'));
			//require(_JOOMLMS_FRONT_HOME . "/languages/".$JLMS_CONFIG->get('default_language').'/quiz.lang.php');
			require(_JOOMLMS_FRONT_HOME . '/includes/quiz/quiz_language.php');
			global $jq_language;

			/*$query = "SELECT lang_file FROM #__lms_quiz_languages WHERE is_default = 1 and lang_file <> 'default'";
			$JLMS_DB->SetQuery( $query );
			$req_lang = $JLMS_DB->LoadResult();
			if ($req_lang && file_exists( dirname(__FILE__) . "/language/".$req_lang.".php")) {
				include( dirname(__FILE__) . "/language/".$req_lang.".php");
			}*/
			/*if ( ($quiz->c_language) && ($quiz->c_language != 1) ) {
				$query = "SELECT lang_file FROM #__lms_quiz_languages WHERE id = '".intval($quiz->c_language)."'";
				$JLMS_DB->SetQuery( $query );
				$req_lang = $JLMS_DB->LoadResult();
				if ($req_lang && file_exists( dirname(__FILE__) . "/language/".$req_lang.".php")) {
					include( dirname(__FILE__) . "/language/".$req_lang.".php");
				}
			}*/
			$toolbar = array();
			if($q_data[0]->c_type == 10){
				$toolbar[] = array('btn_type' => 'next', 'btn_js' => "javascript:jq_QuizReviewNext();void(0);");
			} else {
				$toolbar[] = array('btn_type' => 'next', 'btn_js' => "javascript:jq_QuizReviewNext();void(0);");
			}
			$inside_lp = intval(mosGetParam($_REQUEST, 'inside_lp', 0));
			if ($inside_lp) {
				$toolbar[] = array('btn_type' => 'contents', 'btn_js' => "javascript:ajax_action('contents_lpath');");
			} else {
				if ($quiz->c_slide) {
					$toolbar[] = array('btn_type' => 'contents', 'btn_js' => "javascript:jq_ShowPanel();");
				}
			}
			if ($quiz->c_wrong_message) $jq_language['quiz_answer_incorrect'] = htmlspecialchars(nl2br($quiz->c_wrong_message));
			if ($quiz->c_right_message) $jq_language['quiz_answer_correct'] = htmlspecialchars(nl2br($quiz->c_right_message));
			$quest_params = new JLMSParameters($q_data[0]->params);	
			if($quest_params->get('survey_question') == 1) {
				$is_survey = 1;
			} else {
				$is_survey = 0;
			}				
			$is_correct = 0;
			
			//---test for right quest
			$proc_quest_id = $q_data[0]->c_id;
			if (isset($q_data[0]->c_pool) && $q_data[0]->c_pool) {
				/*$query = "SELECT a.* FROM #__lms_quiz_t_question as a"
				. "\n WHERE a.c_id = ".$q_data[0]->c_pool;
				$JLMS_DB->setQuery( $query ); 
			   $pool_quest = $JLMS_DB->loadObject();
				if (is_object($pool_quest)) {
					$q_data[0]->old_c_id = $q_data[0]->c_id;
					$q_data[0]->c_id = $pool_quest->c_id;
					$q_data[0]->c_question = $pool_quest->c_question;
					$q_data[0]->c_point = $pool_quest->c_point;
					$q_data[0]->c_attempts = $pool_quest->c_attempts;
					$q_data[0]->c_type = $pool_quest->c_type;
					$proc_quest_id_pool = $pool_quest->c_id;
			
				$q_data[0]->params = $pool_quest->params;
				}*/
				$q_data[0]->old_c_id = $q_data[0]->c_id;
				$q_data[0]->c_id = $q_data[0]->c_pool;
				$proc_quest_id_pool = $q_data[0]->c_pool;
			} elseif (isset($q_data[0]->c_pool_gqp) && $q_data[0]->c_pool_gqp) {
				$q_data[0]->old_c_id = $q_data[0]->c_id;
				$q_data[0]->c_id = $q_data[0]->c_pool_gqp;
				$proc_quest_id_pool = $q_data[0]->c_pool_gqp;
			} else {
				$proc_quest_id_pool = $q_data[0]->c_id;
				$q_data[0]->old_c_id = $q_data[0]->c_id;
			}

			switch ($q_data[0]->c_type) {
				case 1:
				case 3:
				case 12:	
					$query = "SELECT qst.c_id FROM #__lms_quiz_r_student_quiz as qz, #__lms_quiz_r_student_question as qst WHERE qz.c_id = qst.c_stu_quiz_id AND qz.unique_id = '".$user_unique_id."' AND c_question_id='".$proc_quest_id."'";
					$JLMS_DB->SetQuery( $query );
					$sqtq_id = $JLMS_DB->LoadResult();

					$query = "SELECT c_choice_id FROM #__lms_quiz_r_student_choice WHERE c_sq_id = '".$sqtq_id."'";
					$JLMS_DB->SetQuery( $query );
					$answer = $JLMS_DB->LoadResult();

					$query = "SELECT a.c_point, b.c_id, a.c_attempts FROM #__lms_quiz_t_question as a, #__lms_quiz_t_choice as b WHERE a.c_id = '".$proc_quest_id_pool."' and b.c_question_id = a.c_id and b.c_right = '1'";
					$JLMS_DB->SetQuery( $query );
					$ddd = $JLMS_DB->LoadObjectList();
					if ($answer)
					if (count($ddd)) {
						if ($ddd[0]->c_id == $answer) {
							$is_correct = 1;
						}
					}
				break;
				case 2:
				case 13:
					$query = "SELECT qst.c_id FROM #__lms_quiz_r_student_quiz as qz, #__lms_quiz_r_student_question as qst WHERE qz.c_id = qst.c_stu_quiz_id AND qz.unique_id = '".$user_unique_id."' AND c_question_id='".$proc_quest_id."'";
					$JLMS_DB->SetQuery( $query );
					$sqtq_id = $JLMS_DB->LoadResult();
					$query = "SELECT c_choice_id FROM #__lms_quiz_r_student_choice WHERE c_sq_id = '".$sqtq_id."'";
					$JLMS_DB->SetQuery( $query );
					$answers = $JLMS_DB->LoadObjectList();
					$answer = array();
					if(count($answers))
						foreach($answers as $answ)
							$answer[] = $answ->c_choice_id;
					$query = "SELECT a.c_point, a.c_attempts FROM #__lms_quiz_t_question as a WHERE a.c_id = '".$proc_quest_id_pool."'";
					$JLMS_DB->SetQuery( $query );
					$ddd = $JLMS_DB->LoadObjectList();
					$query = "SELECT b.c_id FROM #__lms_quiz_t_question as a, #__lms_quiz_t_choice as b WHERE a.c_id = '".$proc_quest_id_pool."' and b.c_question_id = a.c_id and b.c_right = '1'";
					$JLMS_DB->SetQuery( $query );
					$ddd2 = $JLMS_DB->LoadObjectList();
					$query = "SELECT b.c_id FROM #__lms_quiz_t_question as a, #__lms_quiz_t_choice as b WHERE a.c_id = '".$proc_quest_id_pool."' and b.c_question_id = a.c_id and b.c_right <> '1'";
					$JLMS_DB->SetQuery( $query );
					$ddd3 = $JLMS_DB->LoadObjectList();
					
					$ans_array = $answer;
					if (count($ddd2) && count($ddd)) {
						$c_quest_score = $ddd[0]->c_point;
						$is_correct = 1;
						foreach ($ddd2 as $right_row) {
							if (!in_array($right_row->c_id, $ans_array)) {
								$c_quest_score = 0;
								$is_correct = 0; }
						}
						foreach ($ddd3 as $not_right_row) {
							if (in_array($not_right_row->c_id, $ans_array)) {
								$c_quest_score = 0;
								$is_correct = 0; }
						}
						
					}
					
				break;
				case 4:
				case 5:
				case 11:
					$query = "SELECT qst.c_id FROM #__lms_quiz_r_student_quiz as qz, #__lms_quiz_r_student_question as qst WHERE qz.c_id = qst.c_stu_quiz_id AND qz.unique_id = '".$user_unique_id."' AND c_question_id='".$proc_quest_id."'";
					$JLMS_DB->SetQuery( $query );
					$sqtq_id = $JLMS_DB->LoadResult();
					$query = "SELECT a.c_sel_text as c_sel_text FROM #__lms_quiz_r_student_matching as a, #__lms_quiz_t_matching as b WHERE a.c_sq_id = '".$sqtq_id."' AND a.c_matching_id = b.c_id ORDER BY b.ordering";
					$JLMS_DB->SetQuery( $query );
					$answers = $JLMS_DB->LoadObjectList();
					if(count($answers))
						foreach($answers as $answ)
							$answer[] = $answ->c_sel_text;
					$query = "SELECT a.c_point, a.c_attempts FROM #__lms_quiz_t_question as a WHERE a.c_id = '".$proc_quest_id_pool."'";
					$JLMS_DB->SetQuery( $query );
					$ddd = $JLMS_DB->LoadObjectList();
					$query = "SELECT b.c_id, b.c_left_text, b.c_right_text FROM #__lms_quiz_t_question as a, #__lms_quiz_t_matching as b WHERE a.c_id = '".$proc_quest_id_pool."' and b.c_question_id = a.c_id ORDER BY b.ordering";
					$JLMS_DB->SetQuery( $query );
					$ddd2 = $JLMS_DB->LoadObjectList();

					$ans_array = $answer;
					if (count($ddd2) && count($ddd)) {

						$is_correct = 1; $rr_num = 0;
						foreach ($ddd2 as $right_row) {
							if ($right_row->c_right_text != $ans_array[$rr_num]) {
								$is_correct = 0;
							}
							$rr_num ++;
						}
					}
				break;
				case 6:
					$query = "SELECT qst.c_id FROM #__lms_quiz_r_student_quiz as qz, #__lms_quiz_r_student_question as qst WHERE qz.c_id = qst.c_stu_quiz_id AND qz.unique_id = '".$user_unique_id."' AND c_question_id='".$proc_quest_id."'";
					$JLMS_DB->SetQuery( $query );
					$sqtq_id = $JLMS_DB->LoadResult();
					$query = "SELECT c_answer FROM #__lms_quiz_r_student_blank WHERE c_sq_id = '".$sqtq_id."'";
					$JLMS_DB->SetQuery( $query );
					$answer = $JLMS_DB->LoadResult();
					$query = "SELECT a.c_point, a.c_attempts FROM #__lms_quiz_t_question as a WHERE a.c_id = '".$proc_quest_id_pool."'";
					$JLMS_DB->SetQuery( $query );
					$ddd = $JLMS_DB->LoadObjectList();
					$query = "SELECT c.c_text FROM #__lms_quiz_t_question as a, #__lms_quiz_t_blank as b, #__lms_quiz_t_text as c WHERE a.c_id = '".$proc_quest_id_pool."' and b.c_question_id = a.c_id and c.c_blank_id = b.c_id";
					$JLMS_DB->SetQuery( $query );
					$ddd2 = $JLMS_DB->LoadObjectList();

					$answer = trim(urldecode($answer));
					if (count($ddd2) && count($ddd)) {
						foreach ($ddd2 as $right_row) {
							if (strtolower($right_row->c_text) === strtolower($answer)) {
								$is_correct = 1;
							}
						}
						
					}
				break;
				case 7:
					$query = "SELECT qst.c_id FROM #__lms_quiz_r_student_quiz as qz, #__lms_quiz_r_student_question as qst WHERE qz.c_id = qst.c_stu_quiz_id AND qz.unique_id = '".$user_unique_id."' AND c_question_id='".$proc_quest_id."'";
					$JLMS_DB->SetQuery( $query );
					$sqtq_id = $JLMS_DB->LoadResult();
					$query = "SELECT * FROM #__lms_quiz_r_student_hotspot WHERE c_sq_id = '".$sqtq_id."'";
					$JLMS_DB->SetQuery( $query );
					$answers = $JLMS_DB->LoadObjectList();
					$answer = array();
					if(count($answers))
					{
						$answer[0] = $answers[0]->c_select_x;
						$answer[1] = $answers[0]->c_select_y;
					}
					if(count($answer))
					{
						$query = "SELECT a.c_point, a.c_attempts, b.c_start_x, b.c_start_y, b.c_width, b.c_height FROM #__lms_quiz_t_question as a, #__lms_quiz_t_hotspot as b WHERE a.c_id = '".$proc_quest_id_pool."' and b.c_question_id = a.c_id";
						$JLMS_DB->SetQuery( $query );
						$ddd = $JLMS_DB->LoadObjectList();
						if (count($ddd)) {
							$ans_array = $answer;
							if ((count($ans_array) == 2) && ($ans_array[0] >= $ddd[0]->c_start_x) && ($ans_array[0] <= ($ddd[0]->c_start_x+$ddd[0]->c_width)) && ($ans_array[1] >= $ddd[0]->c_start_y) && ($ans_array[1] <= ($ddd[0]->c_start_y+$ddd[0]->c_height)) ) {
								$is_correct = 1;
							}
						}	
					}	
				break;
				case 8:
					$query = "SELECT qst.c_id FROM #__lms_quiz_r_student_quiz as qz, #__lms_quiz_r_student_question as qst WHERE qz.c_id = qst.c_stu_quiz_id AND qz.unique_id = '".$user_unique_id."' AND c_question_id='".$proc_quest_id."'";
					$JLMS_DB->SetQuery( $query );
					$sqtq_id = $JLMS_DB->LoadResult();

					$query = "SELECT c_answer FROM #__lms_quiz_r_student_survey WHERE c_sq_id = '".$sqtq_id."'";
					$JLMS_DB->SetQuery( $query );
					$survey_data = $JLMS_DB->LoadResult();
					
					$is_correct = 1;
					$is_survey = 1;
					$answer = $survey_data;
				break;
				case 9:
					$is_correct = 1;
					$is_survey = 1;
					$query = "SELECT qst.c_id FROM #__lms_quiz_r_student_quiz as qz, #__lms_quiz_r_student_question as qst WHERE qz.c_id = qst.c_stu_quiz_id AND qz.unique_id = '".$user_unique_id."' AND c_question_id='".$proc_quest_id."'";
					$JLMS_DB->SetQuery( $query );
					$sqtq_id = $JLMS_DB->LoadResult();
					$query = "SELECT * FROM #__lms_quiz_r_student_scale WHERE c_sq_id = '".$sqtq_id."'";
					$JLMS_DB->SetQuery( $query );
					$answers = $JLMS_DB->LoadObjectList();
					$answer = array();
					for($p=0;$p<count($answers);$p++)
					{
						$answer[$p][0] = $answers[$p]->q_scale_id;
						$answer[$p][1] = $answers[$p]->scale_id;
					}
				break;
				case 10:
					$is_correct = 1;
					$is_survey = 1;
					$answer = array();
				break;
			}
			
			if (!substr_count($quiz->params,'disable_quest_feedback=1') && !substr_count($q_data[0]->params,'disable_quest_feedback=1')) {		
				//---
				if($is_survey)
				{
					$msg_cor = '';
				}
				else 
				{
					if($is_correct)
					{
//							$msg_cor = $jq_language['quiz_answer_correct'];
						$msg_cor = JoomlaQuiz_template_class::JQ_show_messagebox('', $jq_language['quiz_answer_correct'], $is_correct);
						
					}	
					else 
//							$msg_cor = $jq_language['quiz_answer_incorrect'];
						$msg_cor = JoomlaQuiz_template_class::JQ_show_messagebox('', $jq_language['quiz_answer_incorrect'], $is_correct);
				}
				
			}
			else {
				$msg_cor = '';
			}
			$ret_str .= "\t" . '<quiz_review_correct><![CDATA['.$msg_cor.']]></quiz_review_correct>' . "\n";
			
			//--explanation
			$explans = '';
			if(!$is_survey)
			switch ($quiz_params->get('sh_explanation'))
			{	
				case '1':
				case '12':		
						if($q_data[0]->c_explanation)
							$explans = JoomlaQuiz_template_class::JQ_show_messagebox('', $q_data[0]->c_explanation, 3);
						break;
				case '2':	
				case '13':
						if($st_quiz_data[0]->c_passed)
						if($q_data[0]->c_explanation)
							$explans = JoomlaQuiz_template_class::JQ_show_messagebox('', $q_data[0]->c_explanation, 3);
						break;	
				case '3':
						if(!$st_quiz_data[0]->c_passed)
						if($q_data[0]->c_explanation)
							$explans = JoomlaQuiz_template_class::JQ_show_messagebox('', $q_data[0]->c_explanation, 3);
						break;				
			}
			$ret_str .= "\t" . '<quiz_review_explanation><![CDATA['.($explans?$explans:' ').']]></quiz_review_explanation>' . "\n";
			
			$ret_str .= "\t" . '<task>review_start</task>' . "\n";
			$toolbar_code = JLMS_ShowToolbar($toolbar);
			$toolbar_code = str_replace('"components/com_joomla_lms','"'.$JLMS_CONFIG->get('live_site').'/components/com_joomla_lms',$toolbar_code);
			$ret_str .= "\t" . '<quiz_menu><![CDATA['.$toolbar_code.']]></quiz_menu>' . "\n";
			if (count($q_data) > 0) {
				$ret_str .= "\t" . '<quiz_count_quests>'.count($q_data).'</quiz_count_quests>' . "\n";
				$ret_str .= "\t" . '<quiz_quest_num>1</quiz_quest_num>' . "\n";
				$ret_str .= JLMS_quiz_ajax_class::JQ_GetQuestData_review($q_data[0], $jq_language, $answer, $quiz_params->get('sh_user_answer'), $quiz_params->get('sh_correct_answer', 1), $is_survey);
			} else { $ret_str = ''; }
		}
	}
	return $ret_str;
}

function JQ_NextReview() {
	global $JLMS_DB, $my;
	$JLMS_CONFIG = & JLMSFactory::getConfig();
	$ret_str = '';
	$quiz_id = intval( mosGetParam( $_REQUEST, 'quiz', 0 ) );
	
	$prev_mode = intval( mosGetParam( $_REQUEST, 'prev', 0 ) );

	$query = "SELECT * FROM #__lms_quiz_t_quiz WHERE c_id = '".$quiz_id."'";
	$JLMS_DB->SetQuery ($query );
	$quiz = $JLMS_DB->LoadObjectList();
	if (count($quiz)) {
		$quiz = $quiz[0];
	} else { return $ret_str; }
	$quiz_params = new JLMSParameters($quiz->params);

	$now = date( 'Y-m-d H:i:s', time() - date('Z') );
	if ( ($quiz->published) ) {
		if ( ($my->id) ) {
		} elseif ($quiz->c_guest) {
		} else { return $ret_str; }
	} else {
		$JLMS_ACL = & JLMSFactory::getACL();
		if (!$JLMS_ACL->CheckPermissions('quizzes', 'view_all')) {
			return $ret_str;
		}
	}

	$stu_quiz_id = intval( mosGetParam( $_REQUEST, 'stu_quiz_id', 0 ) );
	$quest_id = intval( mosGetParam( $_REQUEST, 'quest_id', 0 ) );
	$user_unique_id = strval( mosGetParam( $_REQUEST, 'user_unique_id', '') );

	if (($quiz_id) && ($stu_quiz_id) && ($quest_id)) {
		$query = "SELECT c_quiz_id, c_student_id, unique_id, allow_review, c_passed FROM #__lms_quiz_r_student_quiz WHERE c_id = '".$stu_quiz_id."'";
		$JLMS_DB->SetQuery($query);
		$st_quiz_data = $JLMS_DB->LoadObjectList();
		$start_quiz = 0;
		if (count($st_quiz_data)) {
			$start_quiz = $st_quiz_data[0]->c_quiz_id;
		} else { return $ret_str; }
		if ($user_unique_id != $st_quiz_data[0]->unique_id) { return ''; }
		if ($my->id != $st_quiz_data[0]->c_student_id) { return ''; }
		if ($start_quiz != $quiz_id) { return '';}
		if (!$st_quiz_data[0]->allow_review) { return ''; }

		//$query = "SELECT ordering from #__lms_quiz_t_question WHERE c_id = '".$quest_id."'";
		//$JLMS_DB->SetQuery( $query );
		$qorder = 0;//$JLMS_DB->LoadResult();

		/*$query = "SELECT * FROM #__lms_quiz_t_question WHERE c_quiz_id = '".$quiz_id."' ORDER BY ordering, c_id";
		$JLMS_DB->SetQuery($query);
		$q_data = $JLMS_DB->LoadObjectList();*/


		// 22.04.2008 Bugfix - support for Questions pool
		$inside_lp = intval(mosGetParam($_REQUEST, 'inside_lp', 0));
		$QA = new JLMS_quiz_API($quiz_id, $inside_lp);
		if (!$QA->quiz_valid()) {
			return '';
		}
		$QA->set('stu_quiz_id', $stu_quiz_id);
		$QA->set('user_unique_id', $user_unique_id);
		$QA->quiz_ProcessStartData();
	
		if ( $QA->start_valid()) {
			$q_data = $QA->quiz_Get_QuestionList();
		}
		$i = 0;
		while ($i < count($q_data)) {
			$q_data[$i]->ordering = $i;
			if ($q_data[$i]->c_id == $quest_id) { $qorder = $i;}
			
			if(isset($q_data[$i - 1]->c_id) && $q_data[$i - 1]->c_id){
				$q_data[$i]->prev_c_id = $q_data[$i - 1]->c_id;
			} else {
				$q_data[$i]->prev_c_id = 0;
			}
			
			$i++;
		}
		
		if($prev_mode){
			$j = $qorder;	
		} else {
			$i = 0;
			$j = 0;
			while($i < count($q_data)) {
				if ($q_data[$i]->ordering < $qorder) { $j ++;
				} elseif (($q_data[$i]->ordering == $qorder) && ($q_data[$i]->c_id < $quest_id)) { $j ++;
				} elseif (($q_data[$i]->ordering == $qorder) && ($q_data[$i]->c_id == $quest_id)) { $j ++;
				} else { }
				$i ++;
			}
		}

		// 12.03.2007 (bug.. xm template name not inserted in table )
		#$query = "SELECT template_name FROM #__lms_quiz_templates WHERE id = '".$quiz->c_skin."'";
		#$JLMS_DB->SetQuery( $query );
		#$cur_tmpl = $JLMS_DB->LoadResult();
		$cur_tmpl = 'joomlaquiz_lms_template';

		//require( dirname(__FILE__) . "/language/english.php");
		// preloading QUIZ languge (28.02.2007 new method) (all quizzes messages now in global quiz language)
		global $JLMS_LANGUAGE, $JLMS_CONFIG;
		JLMS_require_lang($JLMS_LANGUAGE, 'quiz.lang', $JLMS_CONFIG->get('default_language'));
		//require(_JOOMLMS_FRONT_HOME . "/languages/".$JLMS_CONFIG->get('default_language').'/quiz.lang.php');
		require(_JOOMLMS_FRONT_HOME . '/includes/quiz/quiz_language.php');
		global $jq_language;

		/*$query = "SELECT lang_file FROM #__lms_quiz_languages WHERE is_default = 1 and lang_file <> 'default'";
		$JLMS_DB->SetQuery( $query );
		$req_lang = $JLMS_DB->LoadResult();
		if ($req_lang && file_exists( dirname(__FILE__) . "/language/".$req_lang.".php")) {
			include( dirname(__FILE__) . "/language/".$req_lang.".php");
		}*/
		/*if ( ($quiz->c_language) && ($quiz->c_language != 1) ) {
			$query = "SELECT lang_file FROM #__lms_quiz_languages WHERE id = '".intval($quiz->c_language)."'";
			$JLMS_DB->SetQuery( $query );
			$req_lang = $JLMS_DB->LoadResult();
			if ($req_lang && file_exists( dirname(__FILE__) . "/language/".$req_lang.".php")) {
				include( dirname(__FILE__) . "/language/".$req_lang.".php");
			}
		}*/
		if ($quiz->c_wrong_message) $jq_language['quiz_answer_incorrect'] = htmlspecialchars(nl2br($quiz->c_wrong_message));
		if ($quiz->c_right_message) $jq_language['quiz_answer_correct'] = htmlspecialchars(nl2br($quiz->c_right_message));
		
		if ($cur_tmpl) {
			require_once(dirname(__FILE__) . '/templates/'.$cur_tmpl.'/jq_template.php');
			if (isset($q_data[$j])) {

				$toolbar = array();
				if($q_data[$j]->c_type == 10){
					$toolbar[] = array('btn_type' => 'next', 'btn_js' => "javascript:jq_QuizReviewNext();void(0);");
				} else {
					if(isset($q_data[($j - 1)]->prev_c_id) && $q_data[($j - 1)]->prev_c_id){
						$toolbar[] = array('btn_type' => 'prev', 'btn_js' => "javascript:jq_QuizReviewPrev();void(0);");
					} else {
						$toolbar[] = array('btn_type' => 'prev', 'btn_js' => "javascript:jq_startReview();void(0);");
					}
					$toolbar[] = array('btn_type' => 'next', 'btn_js' => "javascript:jq_QuizReviewNext();void(0);");
				}
				$inside_lp = intval(mosGetParam($_REQUEST, 'inside_lp', 0));
				if ($inside_lp) {
					$toolbar[] = array('btn_type' => 'contents', 'btn_js' => "javascript:ajax_action('contents_lpath');");
				} else {
					if ($quiz->c_slide) {
						$toolbar[] = array('btn_type' => 'contents', 'btn_js' => "javascript:jq_ShowPanel();");
					}
				}
				$is_correct = 0;
				$quest_params = new JLMSParameters($q_data[$j]->params);	
				if($quest_params->get('survey_question')){
					$is_survey = 1;
				} else {
					$is_survey = 0;
				}
					
				$is_correct = 0;

				//---test for right quest
				
				$proc_quest_id = $q_data[$j]->c_id;
				if (isset($q_data[$j]->c_pool) && $q_data[$j]->c_pool) {
					/*$query = "SELECT a.* FROM #__lms_quiz_t_question as a"
					. "\n WHERE a.c_id = ".$q_data[$j]->c_pool;
					$JLMS_DB->setQuery( $query ); 
				   $pool_quest = $JLMS_DB->loadObject();
					if (is_object($pool_quest)) {
						$q_data[$j]->old_c_id = $q_data[$j]->c_id;
						$q_data[$j]->c_id = $pool_quest->c_id;
						$q_data[$j]->c_question = $pool_quest->c_question;
						$q_data[$j]->c_point = $pool_quest->c_point;
						$q_data[$j]->c_attempts = $pool_quest->c_attempts;
						$q_data[$j]->c_type = $pool_quest->c_type;
						$proc_quest_id_pool = $pool_quest->c_id;
				
					$q_data[$j]->params = $pool_quest->params;
					}*/
					$q_data[$j]->old_c_id = $q_data[$j]->c_id;
					$q_data[$j]->c_id = $q_data[$j]->c_pool;
					$proc_quest_id_pool = $q_data[$j]->c_pool;
					
				} elseif (isset($q_data[$j]->c_pool_gqp) && $q_data[$j]->c_pool_gqp) {
					$q_data[$j]->old_c_id = $q_data[$j]->c_id;
					$q_data[$j]->c_id = $q_data[$j]->c_pool_gqp;
					$proc_quest_id_pool = $q_data[$j]->c_pool_gqp;
				} else {
					$proc_quest_id_pool = $q_data[$j]->c_id;
					$q_data[$j]->old_c_id = $q_data[$j]->c_id;
				}
				
					switch ($q_data[$j]->c_type) {
						case 1:
						case 3:
						case 12:
							$query = "SELECT qst.c_id FROM #__lms_quiz_r_student_quiz as qz, #__lms_quiz_r_student_question as qst WHERE qz.c_id = qst.c_stu_quiz_id AND qz.unique_id = '".$user_unique_id."' AND c_question_id='".$proc_quest_id."'";
							$JLMS_DB->SetQuery( $query );
							$sqtq_id = $JLMS_DB->LoadResult();

							$query = "SELECT c_choice_id FROM #__lms_quiz_r_student_choice WHERE c_sq_id = '".$sqtq_id."'";
							$JLMS_DB->SetQuery( $query );
							$answer = $JLMS_DB->LoadResult();
							
							$query = "SELECT a.c_point, b.c_id, a.c_attempts FROM #__lms_quiz_t_question as a, #__lms_quiz_t_choice as b WHERE a.c_id = '".$proc_quest_id_pool."' and b.c_question_id = a.c_id and b.c_right = '1'";
							$JLMS_DB->SetQuery( $query );
							$ddd = $JLMS_DB->LoadObjectList();
							if ($answer)
							if (count($ddd)) {
								if ($ddd[0]->c_id == $answer) {
									$is_correct = 1;
								}
								
							}
							
						break;
						case 2:
						case 13:
							$query = "SELECT qst.c_id FROM #__lms_quiz_r_student_quiz as qz, #__lms_quiz_r_student_question as qst WHERE qz.c_id = qst.c_stu_quiz_id AND qz.unique_id = '".$user_unique_id."' AND c_question_id='".$proc_quest_id."'";
							$JLMS_DB->SetQuery( $query );
							$sqtq_id = $JLMS_DB->LoadResult();
							$query = "SELECT c_choice_id FROM #__lms_quiz_r_student_choice WHERE c_sq_id = '".$sqtq_id."'";
							$JLMS_DB->SetQuery( $query );
							$answers = $JLMS_DB->LoadObjectList();
							$answer = array();
							if(count($answers))
								foreach($answers as $answ)
									$answer[] = $answ->c_choice_id;
							$query = "SELECT a.c_point, a.c_attempts FROM #__lms_quiz_t_question as a WHERE a.c_id = '".$proc_quest_id."'";
							$JLMS_DB->SetQuery( $query );
							$ddd = $JLMS_DB->LoadObjectList();
							$query = "SELECT b.c_id FROM #__lms_quiz_t_question as a, #__lms_quiz_t_choice as b WHERE a.c_id = '".$proc_quest_id_pool."' and b.c_question_id = a.c_id and b.c_right = '1'";
							$JLMS_DB->SetQuery( $query );
							$ddd2 = $JLMS_DB->LoadObjectList();
							$query = "SELECT b.c_id FROM #__lms_quiz_t_question as a, #__lms_quiz_t_choice as b WHERE a.c_id = '".$proc_quest_id_pool."' and b.c_question_id = a.c_id and b.c_right <> '1'";
							$JLMS_DB->SetQuery( $query );
							$ddd3 = $JLMS_DB->LoadObjectList();
							
							$ans_array = $answer;
							if (count($ddd2) && count($ddd)) {
								$c_quest_score = $ddd[0]->c_point;
								$is_correct = 1;
								foreach ($ddd2 as $right_row) {
									if (!in_array($right_row->c_id, $ans_array)) {
										$c_quest_score = 0;
										$is_correct = 0; }
								}
								foreach ($ddd3 as $not_right_row) {
									if (in_array($not_right_row->c_id, $ans_array)) {
										$c_quest_score = 0;
										$is_correct = 0; }
								}
								
							}
							
						break;
						case 4:
						case 5:
						case 11:
							$query = "SELECT qst.c_id FROM #__lms_quiz_r_student_quiz as qz, #__lms_quiz_r_student_question as qst WHERE qz.c_id = qst.c_stu_quiz_id AND qz.unique_id = '".$user_unique_id."' AND c_question_id='".$proc_quest_id."'";
							$JLMS_DB->SetQuery( $query );
							$sqtq_id = $JLMS_DB->LoadResult();
							$query = "SELECT a.c_sel_text as c_sel_text FROM #__lms_quiz_r_student_matching as a, #__lms_quiz_t_matching as b WHERE a.c_sq_id = '".$sqtq_id."' AND a.c_matching_id = b.c_id ORDER BY b.ordering";
							$JLMS_DB->SetQuery( $query );
							$answers = $JLMS_DB->LoadObjectList();
							if(count($answers))
								foreach($answers as $answ)
									$answer[] = $answ->c_sel_text;
							$query = "SELECT a.c_point, a.c_attempts FROM #__lms_quiz_t_question as a WHERE a.c_id = '".$proc_quest_id_pool."'";
							$JLMS_DB->SetQuery( $query );
							$ddd = $JLMS_DB->LoadObjectList();
							$query = "SELECT b.c_id, b.c_left_text, b.c_right_text FROM #__lms_quiz_t_question as a, #__lms_quiz_t_matching as b WHERE a.c_id = '".$proc_quest_id_pool."' and b.c_question_id = a.c_id ORDER BY b.ordering";
							$JLMS_DB->SetQuery( $query );
							$ddd2 = $JLMS_DB->LoadObjectList();

							$ans_array = $answer;
							if (count($ddd2) && count($ddd)) {

								$is_correct = 1; $rr_num = 0;
								foreach ($ddd2 as $right_row) {
									if ($right_row->c_right_text != $ans_array[$rr_num]) {
										$is_correct = 0;
									}
									$rr_num ++;
							
								}
								
							}
						break;
						case 6:
							$query = "SELECT qst.c_id FROM #__lms_quiz_r_student_quiz as qz, #__lms_quiz_r_student_question as qst WHERE qz.c_id = qst.c_stu_quiz_id AND qz.unique_id = '".$user_unique_id."' AND c_question_id='".$proc_quest_id."'";
							$JLMS_DB->SetQuery( $query );
							$sqtq_id = $JLMS_DB->LoadResult();
							$query = "SELECT c_answer FROM #__lms_quiz_r_student_blank WHERE c_sq_id = '".$sqtq_id."'";
							$JLMS_DB->SetQuery( $query );
							$answer = $JLMS_DB->LoadResult();
							$query = "SELECT a.c_point, a.c_attempts FROM #__lms_quiz_t_question as a WHERE a.c_id = '".$proc_quest_id_pool."'";
							$JLMS_DB->SetQuery( $query );
							$ddd = $JLMS_DB->LoadObjectList();
							$query = "SELECT c.c_text FROM #__lms_quiz_t_question as a, #__lms_quiz_t_blank as b, #__lms_quiz_t_text as c WHERE a.c_id = '".$proc_quest_id_pool."' and b.c_question_id = a.c_id and c.c_blank_id = b.c_id";
							$JLMS_DB->SetQuery( $query );
							$ddd2 = $JLMS_DB->LoadObjectList();

							$answer = trim(urldecode($answer));
							if (count($ddd2) && count($ddd)) {
								foreach ($ddd2 as $right_row) {
									if (strtolower($right_row->c_text) === strtolower($answer)) {
										$is_correct = 1;
									}
								}
								
							}
						break;
						case 7:
							$query = "SELECT qst.c_id FROM #__lms_quiz_r_student_quiz as qz, #__lms_quiz_r_student_question as qst WHERE qz.c_id = qst.c_stu_quiz_id AND qz.unique_id = '".$user_unique_id."' AND c_question_id='".$proc_quest_id."'";
							$JLMS_DB->SetQuery( $query );
							$sqtq_id = $JLMS_DB->LoadResult();
							$query = "SELECT * FROM #__lms_quiz_r_student_hotspot WHERE c_sq_id = '".$sqtq_id."'";
							$JLMS_DB->SetQuery( $query );
							$answers = $JLMS_DB->LoadObjectList();
							$answer = array();
							if(count($answers))
							{
								$answer[0] = $answers[0]->c_select_x;
								$answer[1] = $answers[0]->c_select_y;
							}
							if(count($answer))
							{
								$query = "SELECT a.c_point, a.c_attempts, b.c_start_x, b.c_start_y, b.c_width, b.c_height FROM #__lms_quiz_t_question as a, #__lms_quiz_t_hotspot as b WHERE a.c_id = '".$proc_quest_id_pool."' and b.c_question_id = a.c_id";
								$JLMS_DB->SetQuery( $query );
								$ddd = $JLMS_DB->LoadObjectList();

								if (count($ddd)) {
									$ans_array = $answer;
									if ((count($ans_array) == 2) && ($ans_array[0] >= $ddd[0]->c_start_x) && ($ans_array[0] <= ($ddd[0]->c_start_x+$ddd[0]->c_width)) && ($ans_array[1] >= $ddd[0]->c_start_y) && ($ans_array[1] <= ($ddd[0]->c_start_y+$ddd[0]->c_height)) ) {
										$is_correct = 1;
									}
								}	
							}	
						break;
						case 8:
							$query = "SELECT qst.c_id FROM #__lms_quiz_r_student_quiz as qz, #__lms_quiz_r_student_question as qst WHERE qz.c_id = qst.c_stu_quiz_id AND qz.unique_id = '".$user_unique_id."' AND c_question_id='".$proc_quest_id."'";
							$JLMS_DB->SetQuery( $query );
							$sqtq_id = $JLMS_DB->LoadResult();

							$query = "SELECT c_answer FROM #__lms_quiz_r_student_survey WHERE c_sq_id = '".$sqtq_id."'";
							$JLMS_DB->SetQuery( $query );
							$survey_data = $JLMS_DB->LoadResult();
							
							$is_correct = 1;
							$is_survey = 1;
							$answer = $survey_data;
						break;
						case 9:
							$is_correct = 1;
							$is_survey = 1;
							$query = "SELECT qst.c_id FROM #__lms_quiz_r_student_quiz as qz, #__lms_quiz_r_student_question as qst WHERE qz.c_id = qst.c_stu_quiz_id AND qz.unique_id = '".$user_unique_id."' AND c_question_id='".$proc_quest_id."'";
							$JLMS_DB->SetQuery( $query );
							$sqtq_id = $JLMS_DB->LoadResult();
							$query = "SELECT * FROM #__lms_quiz_r_student_scale WHERE c_sq_id = '".$sqtq_id."'";
							$JLMS_DB->SetQuery( $query );
							$answers = $JLMS_DB->LoadObjectList();
							$answer = array();
							for($p=0;$p<count($answers);$p++)
							{
								$answer[$p][0] = $answers[$p]->q_scale_id;
								$answer[$p][1] = $answers[$p]->scale_id;
							}
						break;
						case 10:
							$is_correct = 1;
							$is_survey = 1;
							$answer = array();
						break;
					}

					if ($quiz_params->get('disable_quest_feedback')!=1 && !substr_count($q_data[$j]->params,'disable_quest_feedback=1')) {
					//---
					if($is_survey)
					{
						$msg_cor = '';
					}
					else 
					{
						if($is_correct)
						{
//							$msg_cor = $jq_language['quiz_answer_correct'];
							$msg_cor = JoomlaQuiz_template_class::JQ_show_messagebox('', $jq_language['quiz_answer_correct'], $is_correct);
							
						}	
						else 
//							$msg_cor = $jq_language['quiz_answer_incorrect'];
							$msg_cor = JoomlaQuiz_template_class::JQ_show_messagebox('', $jq_language['quiz_answer_incorrect'], $is_correct);
					}
					
				}
				else {
					$msg_cor = '';
				}
				$ret_str .= "\t" . '<quiz_review_correct><![CDATA['.$msg_cor.']]></quiz_review_correct>' . "\n";
				
				//--explanation
				$explans = '';
				if(!$is_survey)
				switch ($quiz_params->get('sh_explanation'))
				{	
					case '1':
					case '12':	
							if($q_data[$j]->c_explanation)
								$explans = JoomlaQuiz_template_class::JQ_show_messagebox('', $q_data[$j]->c_explanation, 3);
							break;
					case '2':
					case '13':	
							if($st_quiz_data[0]->c_passed)
							if($q_data[$j]->c_explanation)
								$explans = JoomlaQuiz_template_class::JQ_show_messagebox('', $q_data[$j]->c_explanation, 3);
							break;	
					case '3':	
							if(!$st_quiz_data[0]->c_passed)
							if($q_data[$j]->c_explanation)
								$explans = JoomlaQuiz_template_class::JQ_show_messagebox('', $q_data[$j]->c_explanation, 3);
							break;				
				}

				$ret_str .= "\t" . '<quiz_review_explanation><![CDATA['.$explans.']]></quiz_review_explanation>' . "\n";

				$ret_str .= "\t" . '<task>review_next</task>' . "\n";
				$toolbar_code = JLMS_ShowToolbar($toolbar);
				$toolbar_code = str_replace('"components/com_joomla_lms','"'.$JLMS_CONFIG->get('live_site').'/components/com_joomla_lms',$toolbar_code);
				$ret_str .= "\t" . '<quiz_menu><![CDATA['.$toolbar_code.']]></quiz_menu>' . "\n";
				$ret_str .= "\t" . '<quiz_quest_num>'.($j + 1).'</quiz_quest_num>' . "\n";
				$ret_str .= JLMS_quiz_ajax_class::JQ_GetQuestData_review($q_data[$j], $jq_language, $answer, $quiz_params->get('sh_user_answer'), $quiz_params->get('sh_correct_answer', 1), $is_survey);
			} else {
				$ret_str .= "\t" . '<task>review_finish</task>' . "\n";
			}
		}
	}
	return $ret_str;
}

function JQ_NextReview_nojs() {
	global $JLMS_DB, $my, $option, $Itemid, $JLMS_CONFIG;
	
	$ret_str = '';
	$id = intval( mosGetParam( $_REQUEST, 'id', 0 ) );
	$quiz_id = intval( mosGetParam( $_REQUEST, 'quiz', 0 ) );
	
	$doc = & JFactory::getDocument();

	$query = "SELECT * FROM #__lms_quiz_t_quiz WHERE c_id = '".$quiz_id."'";
	$JLMS_DB->SetQuery ($query );
	$quiz = $JLMS_DB->LoadObjectList();
	if (count($quiz)) {
		$quiz = $quiz[0];
	} else { return $ret_str; }
$quiz_params = new JLMSParameters($quiz->params);

	$now = date( 'Y-m-d H:i:s', time() - date('Z') );
	if ( ($quiz->published) ) {
		if ( ($my->id) ) {
		} elseif ($quiz->c_guest) {
		} else { return $ret_str; }
	} else {
		$JLMS_ACL = & JLMSFactory::getACL();
		if (!$JLMS_ACL->CheckPermissions('quizzes', 'view_all')) {
			return $ret_str;
		}
	}

	$stu_quiz_id = intval( mosGetParam( $_REQUEST, 'stu_quiz_id', 0 ) );
	$quest_id = intval( mosGetParam( $_REQUEST, 'quest_id', 0 ) );
	$user_unique_id = strval( mosGetParam( $_REQUEST, 'user_unique_id', '') );

	if (($quiz_id) && ($stu_quiz_id) && ($quest_id)) {
		$query = "SELECT c_quiz_id, c_student_id, unique_id, allow_review, c_passed FROM #__lms_quiz_r_student_quiz WHERE c_id = '".$stu_quiz_id."'";
		$JLMS_DB->SetQuery($query);
		$st_quiz_data = $JLMS_DB->LoadObjectList();
		$start_quiz = 0;
		if (count($st_quiz_data)) {
			$start_quiz = $st_quiz_data[0]->c_quiz_id;
		} else { return $ret_str; }
		if ($user_unique_id != $st_quiz_data[0]->unique_id) { return ''; }
		if ($my->id != $st_quiz_data[0]->c_student_id) { return ''; }
		if ($start_quiz != $quiz_id) { return '';}
		if (!$st_quiz_data[0]->allow_review) { return ''; }

		$query = "SELECT ordering from #__lms_quiz_t_question WHERE c_id = '".$quest_id."'";
		$JLMS_DB->SetQuery( $query );
		$qorder = $JLMS_DB->LoadResult();

		$query = "SELECT * FROM #__lms_quiz_t_question WHERE c_quiz_id = '".$quiz_id."' ORDER BY ordering, c_id";
		$JLMS_DB->SetQuery($query);
		$q_data = $JLMS_DB->LoadObjectList();


		// 22.04.2008 Bugfix - support for Questions pool
		$inside_lp = intval(mosGetParam($_REQUEST, 'inside_lp', 0));
		$QA = new JLMS_quiz_API($quiz_id, $inside_lp);
		if (!$QA->quiz_valid()) {
			return '';
		}
		$QA->set('stu_quiz_id', $stu_quiz_id);
		$QA->set('user_unique_id', $user_unique_id);
		$QA->quiz_ProcessStartData();
	
		if ( $QA->start_valid()) {
			$q_data = $QA->quiz_Get_QuestionList();
		}
		$i = 0;
		while ($i < count($q_data)) {
			$q_data[$i]->ordering = $i;
			if ($q_data[$i]->c_id == $quest_id) { $qorder = $i;}
			$i++;
		}


		$i = 0;$j = 0;
		while($i < count($q_data)) {
			if ($q_data[$i]->ordering < $qorder) { $j ++;
			} elseif (($q_data[$i]->ordering == $qorder) && ($q_data[$i]->c_id < $quest_id)) { $j ++;
			} elseif (($q_data[$i]->ordering == $qorder) && ($q_data[$i]->c_id == $quest_id)) { $j ++;
			} else { }
			$i ++;
		}

		// 12.03.2007 (bug.. xm template name not inserted in table )
		#$query = "SELECT template_name FROM #__lms_quiz_templates WHERE id = '".$quiz->c_skin."'";
		#$JLMS_DB->SetQuery( $query );
		#$cur_tmpl = $JLMS_DB->LoadResult();
		$cur_tmpl = 'joomlaquiz_lms_template';

		//require( dirname(__FILE__) . "/language/english.php");
		// preloading QUIZ languge (28.02.2007 new method) (all quizzes messages now in global quiz language)
		global $JLMS_LANGUAGE, $JLMS_CONFIG;
		JLMS_require_lang($JLMS_LANGUAGE, 'quiz.lang', $JLMS_CONFIG->get('default_language'));
		//require(_JOOMLMS_FRONT_HOME . "/languages/".$JLMS_CONFIG->get('default_language').'/quiz.lang.php');
		require(_JOOMLMS_FRONT_HOME . '/includes/quiz/quiz_language.php');
		global $jq_language;

		/*$query = "SELECT lang_file FROM #__lms_quiz_languages WHERE is_default = 1 and lang_file <> 'default'";
		$JLMS_DB->SetQuery( $query );
		$req_lang = $JLMS_DB->LoadResult();
		if ($req_lang && file_exists( dirname(__FILE__) . "/language/".$req_lang.".php")) {
			include( dirname(__FILE__) . "/language/".$req_lang.".php");
		}*/
		/*if ( ($quiz->c_language) && ($quiz->c_language != 1) ) {
			$query = "SELECT lang_file FROM #__lms_quiz_languages WHERE id = '".intval($quiz->c_language)."'";
			$JLMS_DB->SetQuery( $query );
			$req_lang = $JLMS_DB->LoadResult();
			if ($req_lang && file_exists( dirname(__FILE__) . "/language/".$req_lang.".php")) {
				include( dirname(__FILE__) . "/language/".$req_lang.".php");
			}
		}*/
		if ($quiz->c_wrong_message) $jq_language['quiz_answer_incorrect'] = htmlspecialchars(nl2br($quiz->c_wrong_message));
		if ($quiz->c_right_message) $jq_language['quiz_answer_correct'] = htmlspecialchars(nl2br($quiz->c_right_message));
		
		if ($cur_tmpl) {
			require_once(dirname(__FILE__) . '/templates/'.$cur_tmpl.'/jq_template.php');
			if (isset($q_data[$j])) {

//				$toolbar = array();
//				$toolbar[] = array('btn_type' => 'quiz_ok', 'btn_js' => "javascript:jq_QuizReviewNext();void(0);");
//				$inside_lp = intval(mosGetParam($_REQUEST, 'inside_lp', 0));
//				if ($inside_lp) {
//					$toolbar[] = array('btn_type' => 'contents', 'btn_js' => "javascript:ajax_action('contents_lpath');");
//				} else {
//					if ($quiz->c_slide) {
//						$toolbar[] = array('btn_type' => 'contents', 'btn_js' => "javascript:jq_ShowPanel();");
//					}
//				}
				$is_correct = 0;
				$quest_params = new JLMSParameters($q_data[$j]->params);	
				if($quest_params->get('survey_question'))
					$is_survey = 1;
				else 
					$is_survey = 0;

				$is_correct = 0;

					//---test for right quest
					
					
					$proc_quest_id = $q_data[$j]->c_id;
					if (isset($q_data[$j]->c_pool) && $q_data[$j]->c_pool) {
						$q_data[$j]->old_c_id = $q_data[$j]->c_id;
						$q_data[$j]->c_id = $q_data[$j]->c_pool;
						$proc_quest_id_pool = $q_data[$j]->c_pool;
					} elseif (isset($q_data[$j]->c_pool_gqp) && $q_data[$j]->c_pool_gqp) {
						$q_data[$j]->old_c_id = $q_data[$j]->c_id;
						$q_data[$j]->c_id = $q_data[$j]->c_pool_gqp;
						$proc_quest_id_pool = $q_data[$j]->c_pool_gqp;
					} else {
						$proc_quest_id_pool = $q_data[$j]->c_id;
						$q_data[$j]->old_c_id = $q_data[$j]->c_id;
					}
	
						switch ($q_data[$j]->c_type) {
							case 1:
							case 3:
							case 12:
								$query = "SELECT qst.c_id FROM #__lms_quiz_r_student_quiz as qz, #__lms_quiz_r_student_question as qst WHERE qz.c_id = qst.c_stu_quiz_id AND qz.unique_id = '".$user_unique_id."' AND c_question_id='".$proc_quest_id."'";
								$JLMS_DB->SetQuery( $query );
								$sqtq_id = $JLMS_DB->LoadResult();
								$query = "SELECT c_choice_id FROM #__lms_quiz_r_student_choice WHERE c_sq_id = '".$sqtq_id."'";
								$JLMS_DB->SetQuery( $query );
								$answer = $JLMS_DB->LoadResult();
								
								$query = "SELECT a.c_point, b.c_id, a.c_attempts FROM #__lms_quiz_t_question as a, #__lms_quiz_t_choice as b WHERE a.c_id = '".$proc_quest_id_pool."' and b.c_question_id = a.c_id and b.c_right = '1'";
								$JLMS_DB->SetQuery( $query );
								$ddd = $JLMS_DB->LoadObjectList();
								if ($answer)
								if (count($ddd)) {
									if ($ddd[0]->c_id == $answer) {
										$is_correct = 1;
									}
									
								}
								
							break;
							case 2:
							case 13:
								$query = "SELECT qst.c_id FROM #__lms_quiz_r_student_quiz as qz, #__lms_quiz_r_student_question as qst WHERE qz.c_id = qst.c_stu_quiz_id AND qz.unique_id = '".$user_unique_id."' AND c_question_id='".$proc_quest_id."'";
								$JLMS_DB->SetQuery( $query );
								$sqtq_id = $JLMS_DB->LoadResult();
								$query = "SELECT c_choice_id FROM #__lms_quiz_r_student_choice WHERE c_sq_id = '".$sqtq_id."'";
								$JLMS_DB->SetQuery( $query );
								$answers = $JLMS_DB->LoadObjectList();
								$answer = array();
								if(count($answers))
									foreach($answers as $answ)
										$answer[] = $answ->c_choice_id;
								$query = "SELECT a.c_point, a.c_attempts FROM #__lms_quiz_t_question as a WHERE a.c_id = '".$proc_quest_id."'";
								$JLMS_DB->SetQuery( $query );
								$ddd = $JLMS_DB->LoadObjectList();
								$query = "SELECT b.c_id FROM #__lms_quiz_t_question as a, #__lms_quiz_t_choice as b WHERE a.c_id = '".$proc_quest_id_pool."' and b.c_question_id = a.c_id and b.c_right = '1'";
								$JLMS_DB->SetQuery( $query );
								$ddd2 = $JLMS_DB->LoadObjectList();
								$query = "SELECT b.c_id FROM #__lms_quiz_t_question as a, #__lms_quiz_t_choice as b WHERE a.c_id = '".$proc_quest_id_pool."' and b.c_question_id = a.c_id and b.c_right <> '1'";
								$JLMS_DB->SetQuery( $query );
								$ddd3 = $JLMS_DB->LoadObjectList();
								
								$ans_array = $answer;
								if (count($ddd2) && count($ddd)) {
									$c_quest_score = $ddd[0]->c_point;
									$is_correct = 1;
									foreach ($ddd2 as $right_row) {
										if (!in_array($right_row->c_id, $ans_array)) {
											$c_quest_score = 0;
											$is_correct = 0; }
									}
									foreach ($ddd3 as $not_right_row) {
										if (in_array($not_right_row->c_id, $ans_array)) {
											$c_quest_score = 0;
											$is_correct = 0; }
									}
									
								}
								
							break;
							case 4:
							case 5:
							case 11:
								$query = "SELECT qst.c_id FROM #__lms_quiz_r_student_quiz as qz, #__lms_quiz_r_student_question as qst WHERE qz.c_id = qst.c_stu_quiz_id AND qz.unique_id = '".$user_unique_id."' AND c_question_id='".$proc_quest_id."'";
								$JLMS_DB->SetQuery( $query );
								$sqtq_id = $JLMS_DB->LoadResult();
								$query = "SELECT a.c_sel_text as c_sel_text FROM #__lms_quiz_r_student_matching as a, #__lms_quiz_t_matching as b WHERE a.c_sq_id = '".$sqtq_id."' AND a.c_matching_id = b.c_id ORDER BY b.ordering";
								$JLMS_DB->SetQuery( $query );
								$answers = $JLMS_DB->LoadObjectList();
								if(count($answers))
									foreach($answers as $answ)
										$answer[] = $answ->c_sel_text;
								$query = "SELECT a.c_point, a.c_attempts FROM #__lms_quiz_t_question as a WHERE a.c_id = '".$proc_quest_id_pool."'";
								$JLMS_DB->SetQuery( $query );
								$ddd = $JLMS_DB->LoadObjectList();
								$query = "SELECT b.c_id, b.c_left_text, b.c_right_text FROM #__lms_quiz_t_question as a, #__lms_quiz_t_matching as b WHERE a.c_id = '".$proc_quest_id_pool."' and b.c_question_id = a.c_id ORDER BY b.ordering";
								$JLMS_DB->SetQuery( $query );
								$ddd2 = $JLMS_DB->LoadObjectList();

								$ans_array = $answer;
								if (count($ddd2) && count($ddd)) {
									$is_correct = 1; $rr_num = 0;
									foreach ($ddd2 as $right_row) {
										if ($right_row->c_right_text != $ans_array[$rr_num]) {
											$is_correct = 0;
										}
										$rr_num ++;
									}
								}
							break;
							case 6:
								$query = "SELECT qst.c_id FROM #__lms_quiz_r_student_quiz as qz, #__lms_quiz_r_student_question as qst WHERE qz.c_id = qst.c_stu_quiz_id AND qz.unique_id = '".$user_unique_id."' AND c_question_id='".$proc_quest_id."'";
								$JLMS_DB->SetQuery( $query );
								$sqtq_id = $JLMS_DB->LoadResult();
								$query = "SELECT c_answer FROM #__lms_quiz_r_student_blank WHERE c_sq_id = '".$sqtq_id."'";
								$JLMS_DB->SetQuery( $query );
								$answer = $JLMS_DB->LoadResult();
								$query = "SELECT a.c_point, a.c_attempts FROM #__lms_quiz_t_question as a WHERE a.c_id = '".$proc_quest_id_pool."'";
								$JLMS_DB->SetQuery( $query );
								$ddd = $JLMS_DB->LoadObjectList();
								$query = "SELECT c.c_text FROM #__lms_quiz_t_question as a, #__lms_quiz_t_blank as b, #__lms_quiz_t_text as c WHERE a.c_id = '".$proc_quest_id_pool."' and b.c_question_id = a.c_id and c.c_blank_id = b.c_id";
								$JLMS_DB->SetQuery( $query );
								$ddd2 = $JLMS_DB->LoadObjectList();

								$answer = trim(urldecode($answer));
								if (count($ddd2) && count($ddd)) {
									foreach ($ddd2 as $right_row) {
										if (strtolower($right_row->c_text) === strtolower($answer)) {
											$is_correct = 1;
										}
									}
									
								}
							break;
							case 7:
								$query = "SELECT qst.c_id FROM #__lms_quiz_r_student_quiz as qz, #__lms_quiz_r_student_question as qst WHERE qz.c_id = qst.c_stu_quiz_id AND qz.unique_id = '".$user_unique_id."' AND c_question_id='".$proc_quest_id."'";
								$JLMS_DB->SetQuery( $query );
								$sqtq_id = $JLMS_DB->LoadResult();
								$query = "SELECT * FROM #__lms_quiz_r_student_hotspot WHERE c_sq_id = '".$sqtq_id."'";
								$JLMS_DB->SetQuery( $query );
								$answers = $JLMS_DB->LoadObjectList();
								$answer = array();
								if(count($answers))
								{
									$answer[0] = $answers[0]->c_select_x;
									$answer[1] = $answers[0]->c_select_y;
								}
								if(count($answer))
								{
									$query = "SELECT a.c_point, a.c_attempts, b.c_start_x, b.c_start_y, b.c_width, b.c_height FROM #__lms_quiz_t_question as a, #__lms_quiz_t_hotspot as b WHERE a.c_id = '".$proc_quest_id_pool."' and b.c_question_id = a.c_id";
									$JLMS_DB->SetQuery( $query );
									$ddd = $JLMS_DB->LoadObjectList();

									if (count($ddd)) {
										$ans_array = $answer;
										if ((count($ans_array) == 2) && ($ans_array[0] >= $ddd[0]->c_start_x) && ($ans_array[0] <= ($ddd[0]->c_start_x+$ddd[0]->c_width)) && ($ans_array[1] >= $ddd[0]->c_start_y) && ($ans_array[1] <= ($ddd[0]->c_start_y+$ddd[0]->c_height)) ) {
											$is_correct = 1;
										}
									}	
								}	
							break;
							case 8:
								$query = "SELECT qst.c_id FROM #__lms_quiz_r_student_quiz as qz, #__lms_quiz_r_student_question as qst WHERE qz.c_id = qst.c_stu_quiz_id AND qz.unique_id = '".$user_unique_id."' AND c_question_id='".$proc_quest_id."'";
								$JLMS_DB->SetQuery( $query );
								$sqtq_id = $JLMS_DB->LoadResult();

								$query = "SELECT c_answer FROM #__lms_quiz_r_student_survey WHERE c_sq_id = '".$sqtq_id."'";
								$JLMS_DB->SetQuery( $query );
								$survey_data = $JLMS_DB->LoadResult();
								
								$is_correct = 1;
								$is_survey = 1;
								$answer = $survey_data;
							break;
							case 9:
								$is_correct = 1;
								$is_survey = 1;
								$query = "SELECT qst.c_id FROM #__lms_quiz_r_student_quiz as qz, #__lms_quiz_r_student_question as qst WHERE qz.c_id = qst.c_stu_quiz_id AND qz.unique_id = '".$user_unique_id."' AND c_question_id='".$proc_quest_id."'";
								$JLMS_DB->SetQuery( $query );
								$sqtq_id = $JLMS_DB->LoadResult();
								$query = "SELECT * FROM #__lms_quiz_r_student_scale WHERE c_sq_id = '".$sqtq_id."'";
								$JLMS_DB->SetQuery( $query );
								$answers = $JLMS_DB->LoadObjectList();
								$answer = array();
								for($p=0;$p<count($answers);$p++)
								{
									$answer[$p][0] = $answers[$p]->q_scale_id;
									$answer[$p][1] = $answers[$p]->scale_id;
								}
							break;
							case 10:
								$is_correct = 1;
								$is_survey = 1;
								$answer = array();
							break;
						}
				if ($quiz_params->get('disable_quest_feedback')!=1 && !substr_count($q_data[$j]->params,'disable_quest_feedback=1')) {
					//---
					if($is_survey)
					{
						$msg_cor = '';
					}
					else 
					{
						if($is_correct)
						{
							$msg_cor = $jq_language['quiz_answer_correct'];
							
						}	
						else 
							$msg_cor = $jq_language['quiz_answer_incorrect'];
					}
					
				}
				else {
					$msg_cor = '';
				}
//				$ret_str .= "\t" . '<quiz_review_correct><![CDATA['.$msg_cor.']]></quiz_review_correct>' . "\n";
				
				//--explanation
				$explans = '';
				if(!$is_survey)
				switch ($quiz_params->get('sh_explanation'))
				{	
					case '1':
					case '12':	
							if($q_data[$j]->c_explanation)
								$explans = $q_data[$j]->c_explanation;
							break;
					case '2':
					case '13':	
							if($st_quiz_data[0]->c_passed)
							if($q_data[$j]->c_explanation)
								$explans = $q_data[$j]->c_explanation;
							break;	
					case '3':	
							if(!$st_quiz_data[0]->c_passed)
							if($q_data[$j]->c_explanation)
								$explans = $q_data[$j]->c_explanation;
							break;				
				}

//				$ret_str .= "\t" . '<quiz_review_explanation><![CDATA['.$explans.']]></quiz_review_explanation>' . "\n";
				$kol_quests = count($q_data);
				$quest_score = $q_data[$j]->c_point;
				$qtype = $q_data[$j]->c_type;
				$quest_id = $q_data[$j]->c_id;
							
				$quest_num = $j + 1;
				
				$query = "SELECT a.*, b.lpath_id FROM #__lms_learn_path_step_quiz_results as a, #__lms_learn_path_steps as b WHERE a.stu_quiz_id = '".$stu_quiz_id."' AND a.step_id = b.id";
				$JLMS_DB->setQuery($query);
				$this_lpath = $JLMS_DB->LoadObject();
				
				$toolbar = array();
				if(isset($this_lpath->stu_quiz_id) && $this_lpath->stu_quiz_id == $stu_quiz_id){
					if($qtype == 10){
						if(isset($q_data[$j]->c_slide) && $q_data[$j]->c_slide){
							$toolbar[] = array('kol_quests' => $kol_quests, 'num_quest' => $quest_num, 'quest_score' => $quest_score, 'img_btn'=>'btn_start.png', 'text_btn'=>_JLMS_NEXT_ALT_TITLE, 'link_cont'=>'&task=show_lpath_nojs&action=contents_lpath&course_id='.$id.'&id='.$this_lpath->lpath_id.'&stu_quiz_id='.$stu_quiz_id.'&user_unique_id='.$this_lpath->unique_id.'&step_id='.$this_lpath->step_id.'&user_start_id='.$this_lpath->start_id.'&quest_id='.$quest_id.'&quiz_id='.$quiz_id.'&stu_quiz_id='.$stu_quiz_id.'');						
						} else {
							$toolbar[] = array('kol_quests' => $kol_quests, 'num_quest' => $quest_num, 'quest_score' => $quest_score, 'img_btn'=>'btn_start.png', 'text_btn'=>_JLMS_NEXT_ALT_TITLE);							
						}
					} else {
						if(isset($q_data[$j]->c_slide) && $q_data[$j]->c_slide){
							$toolbar[] = array('kol_quests' => $kol_quests, 'num_quest' => $quest_num, 'quest_score' => $quest_score, 'img_btn'=>'btn_complete.png', 'text_btn'=>_JLMS_OK_ALT_TITLE, 'link_cont'=>'&task=show_lpath_nojs&action=contents_lpath&course_id='.$id.'&id='.$this_lpath->lpath_id.'&stu_quiz_id='.$stu_quiz_id.'&user_unique_id='.$this_lpath->unique_id.'&step_id='.$this_lpath->step_id.'&user_start_id='.$this_lpath->start_id.'&quest_id='.$quest_id.'&quiz_id='.$quiz_id.'&stu_quiz_id='.$stu_quiz_id.'');
						} else {
							$toolbar[] = array('kol_quests' => $kol_quests, 'num_quest' => $quest_num, 'quest_score' => $quest_score, 'img_btn'=>'btn_complete.png', 'text_btn'=>_JLMS_OK_ALT_TITLE);	
						}
					}
				} else {
					if($qtype == 10){
						if(isset($q_data[$j]->c_slide) && $q_data[$j]->c_slide){
							$toolbar[] = array('kol_quests' => $kol_quests, 'num_quest' => $quest_num, 'quest_score' => $quest_score, 'img_btn'=>'btn_start.png', 'text_btn'=>_JLMS_NEXT_ALT_TITLE, 'link_cont'=>'&task=quiz_action&atask=contents&id='.$id.'&quiz='.$quiz_id.'&stu_quiz_id='.$stu_quiz_id.'&user_unique_id='.$user_unique_id.'&quest_id='.$quest_id.'');
						} else {
							$toolbar[] = array('kol_quests' => $kol_quests, 'num_quest' => $quest_num, 'quest_score' => $quest_score, 'img_btn'=>'btn_start.png', 'text_btn'=>_JLMS_NEXT_ALT_TITLE);	
						}
					} else {
						if(isset($q_data[$j]->c_slide) && $q_data[$j]->c_slide){
							$toolbar[] = array('kol_quests' => $kol_quests, 'num_quest' => $quest_num, 'quest_score' => $quest_score, 'img_btn'=>'btn_complete.png', 'text_btn'=>_JLMS_OK_ALT_TITLE, 'link_cont'=>'&task=quiz_action&atask=contents&id='.$id.'&quiz='.$quiz_id.'&stu_quiz_id='.$stu_quiz_id.'&user_unique_id='.$user_unique_id.'&quest_id='.$quest_id.'');
						} else {
							$toolbar[] = array('kol_quests' => $kol_quests, 'num_quest' => $quest_num, 'quest_score' => $quest_score, 'img_btn'=>'btn_complete.png', 'text_btn'=>_JLMS_OK_ALT_TITLE);	
						}
					}
				}
				
				$doc->addStyleSheet( $JLMS_CONFIG->get('live_site').'/components/com_joomla_lms/includes/quiz/templates/joomlaquiz_lms_template/jq_template.css' );
				
				?>
				<form name="quest_form" action="<?php echo ampReplace($JLMS_CONFIG->get('live_site')."/index.php?option=$option&Itemid=$Itemid");?>" method="post">
					<table border="0" width="100%" align="center" class="jlms_table_no_borders">
						<tr>
							<td>
								<?php echo JLMS_quiz_ajax_class::JQ_toolbar_nojs($toolbar, $qtype, 1);?>
					
								<input type="hidden" name="stu_quiz_id" value="<?php echo $stu_quiz_id;?>"/>
								<input type="hidden" name="user_unique_id" value="<?php echo $user_unique_id;?>"/>
							</td>
						</tr>
						<tr>
							<td align="center" style="text-align:center;">
								<?php
								
								if ($kol_quests > 0) {
									$quest_num = 0;
									# commented 25 April 2007 (DEN)
									# we've already randomized auestions in the sequence
									/*if ($QA->get_qvar('c_random')) {
										$quest_num = rand(0, ($kol_quests - 1) );
									}*/
									?>
									<input type="hidden" name="quiz_count_quests" value="<?php echo $kol_quests;?>"/>
									<input type="hidden" name="quiz_quest_num" value="1"/>
									<?php echo JLMS_quiz_ajax_class::JQ_GetQuestData_review_nojs($q_data[$j], $jq_language, $answer, $quiz_params->get('sh_user_answer'), $is_survey, $msg_cor, $is_correct);
						//			$ret_str .= JLMS_quiz_ajax_class::JQ_GetPanelData_nojs($quiz_id, $q_data); ?>
									<?php
								}
								if($explans != ''){
									 echo JoomlaQuiz_template_class::JQ_show_messagebox('', $explans, 3);									
								}
								?>
							</td>
						</tr>
					</table>
					
					<input type="hidden" name="option" value="<?php echo $option;?>"/>
					<input type="hidden" name="task" value="quiz_action"/>
					<input type="hidden" name="id" value="<?php echo $id;?>"/>
					<input type="hidden" name="quiz" value="<?php echo $quiz_id;?>"/>
					<input type="hidden" name="atask" value="review_next"/>
				</form>
				<?php
			} else {
//				$ret_str .= "\t" . 'review_finish' . "\n";
//				echo 'review_finish';
				JLMSRedirect( sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=quiz_action&id=$id&stu_quiz_id=$stu_quiz_id&user_unique_id=$user_unique_id&quiz=$quiz_id&atask=review_stop") );
			}
		}
	}
//	return $ret_str;
}

function JQ_FinishQuiz() {
	global $JLMS_DB, $my, $Itemid, $option;
	$JLMS_CONFIG = & JLMSFactory::getConfig();
	$ret_str = '';
	$quiz_id = intval( mosGetParam( $_REQUEST, 'quiz', 0 ) );
	$id = intval( mosGetParam( $_REQUEST, 'id', 0 ) );
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

	$stu_quiz_id = intval( mosGetParam( $_REQUEST, 'stu_quiz_id', 0 ) );
	$user_unique_id = strval( mosGetParam( $_REQUEST, 'user_unique_id', '') );

	$QA->set('stu_quiz_id', $stu_quiz_id);
	$QA->set('user_unique_id', $user_unique_id);
	$QA->quiz_ProcessStartData();
	
	if ( $QA->start_valid() && $quiz_id ) {

		// temporary fo compatibility
		// (25 April 2007 commented) $quiz = $QA->quiz_data;

		//print_r($stu_quiz_id);
		$query = "SELECT SUM(c_score) FROM #__lms_quiz_r_student_question WHERE c_stu_quiz_id = '".$stu_quiz_id."'";
		$JLMS_DB->SetQuery( $query );
		$user_score = $JLMS_DB->LoadResult();
		if (!$user_score) $user_score = 0;

		/*$query = "SELECT SUM(c_point) FROM #__lms_quiz_t_question WHERE c_quiz_id = '".$quiz_id."'";
		$JLMS_DB->SetQuery( $query );
		$max_score = $JLMS_DB->LoadResult();*/
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

		$query = "UPDATE #__lms_quiz_r_student_quiz SET c_total_score = '".$user_score."', c_passed = '".$user_passed."', c_total_time = '".$user_time."'"
		. "\n WHERE c_id = '".$stu_quiz_id."' and c_quiz_id = '".$quiz_id."' and c_student_id = '".$my->id."'";
		$JLMS_DB->SetQuery( $query );
		$JLMS_DB->query();

		// update lms results
		$lms_course = $QA->get_qvar('course_id', 0);
		$lms_quiz = $quiz_id;
		$lms_user = $my->id;
		$lms_score = $user_score;
		$lms_time = $user_time;
		$lms_date = date( 'Y-m-d H:i:s', time() - date('Z') );
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

				if ($lms_passed) {
					$db = & JFactory::getDbo();
					$_JLMS_PLUGINS = & JLMSFactory::getPlugins();
					//*** send email notifications
					$JLMS_CONFIG = & JLMSFactory::getConfig();
					$Itemid = $JLMS_CONFIG->get('Itemid');
					$e_course = new stdClass();
					$e_course->course_alias = '';
					$e_course->course_name = '';			
	
					$query = "SELECT course_name, name_alias FROM #__lms_courses WHERE id = '".$lms_course."'";
					$db->setQuery( $query );
					$e_course = $db->loadObject();
	
					$query = "SELECT c_title FROM #__lms_quiz_t_quiz WHERE c_id = '".$lms_quiz."'";
					$db->setQuery( $query );
					$e_quiz_name = $db->loadResult();

					$e_user = new stdClass();
					$e_user->name = '';
					$e_user->email = '';
					$e_user->username = '';

					$query = "SELECT email, name, username FROM #__users WHERE id = '".$lms_user."'";
					$db->setQuery( $query );
					$e_user = $db->loadObject();

					$e_params['user_id'] = $lms_user;
					$e_params['course_id'] = $lms_course;					
					$e_params['markers']['{email}'] = $e_user->email;	
					$e_params['markers']['{name}'] = $e_user->name;										
					$e_params['markers']['{username}'] = $e_user->username;
					$e_params['markers']['{coursename}'] = $e_course->course_name;
					$e_params['markers']['{quizname}'] = $e_quiz_name;

					$e_params['markers']['{courselink}'] = JLMSEmailRoute("index.php?option=com_joomla_lms&Itemid=$Itemid&task=details_course&id=$lms_course");
					$e_params['markers_nohtml']['{courselink}'] = $e_params['markers']['{courselink}'];
					$e_params['markers']['{courselink}'] = '<a href="'.$e_params['markers']['{courselink}'].'">'.$e_params['markers']['{courselink}'].'</a>';
	
					$e_params['markers']['{lmslink}'] = JLMSEmailRoute("index.php?option=com_joomla_lms&Itemid=$Itemid");
					$e_params['markers_nohtml']['{lmslink}'] = $e_params['markers']['{lmslink}'];
					$e_params['markers']['{lmslink}'] = '<a href="'.$e_params['markers']['{lmslink}'].'">'.$e_params['markers']['{lmslink}'].'</a>';

					$e_params['action_name'] = 'OnQuizCompletion';

					$_JLMS_PLUGINS->loadBotGroup('emails');
					$plugin_result_array = $_JLMS_PLUGINS->trigger('OnQuizCompletion', array (& $e_params));
					//*** end of emails
				}
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

			$ret_str .= "\t" . '<task>results</task>' . "\n";
			$eee = $jq_language['quiz_header_fin_message'];
			$ret_str .= "\t" . '<finish_msg><![CDATA[';
			if ($user_passed) {
				if ($QA->get_qvar('c_pass_message', '')) {
					$jq_language['quiz_user_passes'] = nl2br($QA->get_qvar('c_pass_message', ''));
				}
			} else {
				if ($QA->get_qvar('c_unpass_message', '')) {
					$jq_language['quiz_user_fails'] = nl2br($QA->get_qvar('c_unpass_message', ''));
				}
			}
			
			
			if($quiz_params->get('sh_final_page_fdbck', 1) == 1){
				$ret_str .= JoomlaQuiz_template_class::JQ_show_results_msg($eee, ($user_passed?$jq_language['quiz_user_passes']:$jq_language['quiz_user_fails']), $user_passed);
				$ret_str .= '<br />';
			} else {
				$ret_str .= '<br />';	
			}
			
			$ret_str .= ']]></finish_msg>' . "\n";
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
			
			/*Integration Plugin Percentiles*/
			$_JLMS_PLUGINS = & JLMSFactory::getPlugins();
			$_JLMS_PLUGINS->loadBotGroup('system');
			
			$data = new stdClass();
			$data->course_id = $id;
			$data->quiz_id = $quiz_id;
			$data->user_id = $my->id;
			
			if($out_plugin = $_JLMS_PLUGINS->trigger('onQuizFinish', array($data))){
				if(count($out_plugin)){
					$percentiles = $out_plugin[0];
					$percent = $percentiles->percent.'%';
					$t_ar[] = mosHTML::makeOption($percent, $jq_language['quiz_res_mes_percentiles']);
				}	
			}
			/*Integration Plugin Percentiles*/
			
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
				$link_inside_1 = ampReplace($JLMS_CONFIG->get('live_site').'/index.php?tmpl=component&option=com_joomla_lms&Itemid='.$Itemid.'&no_html=1&task=print_quiz_cert&course_id='.$lms_course.'&stu_quiz_id='.$stu_quiz_id.'&user_unique_id=');
				$btn_certificate = 'window.open(\''.$link_inside_1.'\'+user_unique_id,\'blank\');';
				$footer_ar[2]->text = "<div class='back_button'><a href='javascript:void(0)' onclick=\"window.open ('".$JLMS_CONFIG->get('live_site')."/index.php?tmpl=component&option=com_joomla_lms&Itemid=".$Itemid."&no_html=1&task=print_quiz_cert&course_id=".$lms_course."&stu_quiz_id=".$stu_quiz_id."&user_unique_id=' + user_unique_id,'blank');\">".$jq_language['quiz_fin_btn_certificate']."</a></div>";
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
			
//Max 27.03.08			
//			if($quiz_params->get('sh_final_page') == 2)
//			{
//				$footer_html = JoomlaQuiz_template_class::JQ_show_results_footer_content(stripslashes($quiz_params->get('sh_final_content')));
//				$ret_str = "\t" . '<task>results</task>' . "\n";
//				$ret_str .= "\t" . '<finish_msg><![CDATA[]]></finish_msg>' . "\n";
//				$ret_str .= "\t" . '<quiz_results><![CDATA['.$footer_html.']]></quiz_results>' . "\n";
//				
//			}
			

			$footer_html_graf = '';
			if($quiz_params->get('sh_final_page_grafic', 0) == 1)
			{
				////----barss----////
				$is_pool = 0;
				//$showtype_id = intval( mosGetParam( $_POST, 'showtype_id', 0 ) );
				//if(!$quiz_id)
				//$quiz_id = intval(mosGetParam($_POST,'quiz_id',-1));
				if($quiz_id == -1 || $quiz_id == 0) {$is_pool = 1; $quiz_id = 0;}
			
				/*$query = "SELECT a.*, b.c_qtype as qtype_full, c.c_title as quiz_name, qc.c_category"
				. "\n FROM #__lms_quiz_t_question a LEFT JOIN #__lms_quiz_t_qtypes b ON b.c_id = a.c_type LEFT JOIN #__lms_quiz_t_quiz c ON a.c_quiz_id = c.c_id AND c.course_id = '".$id."'"
				. "\n LEFT JOIN #__lms_quiz_t_category as qc ON a.c_qcat = qc.c_id AND qc.course_id = '".$id."' AND qc.is_quiz_cat = 0"
				. "\n WHERE a.course_id = '".$id."'"
				. "\n AND c_quiz_id = '".$quiz_id."'" 
				. "\n ORDER BY a.ordering, a.c_id"
				;
				$JLMS_DB->setQuery( $query );
				$rows = $JLMS_DB->loadObjectList();*/
				$rows = $QA->quiz_Get_QuestionList();
			
				/*$q_from_pool = array();
				foreach ($rows as $row) {
					if ($row->c_type == 20) {
						$q_from_pool[] = $row->c_pool;
					}
				}
				if (count($q_from_pool)) {
					$qp_ids =implode(',',$q_from_pool);
					$query = "SELECT a.*, b.c_qtype as qtype_full FROM #__lms_quiz_t_question as a LEFT JOIN #__lms_quiz_t_qtypes b ON b.c_id = a.c_type"
					. "\n WHERE a.course_id = '".$id."' AND a.c_id IN ($qp_ids)";
					$JLMS_DB->setQuery( $query );
					$rows2 = $JLMS_DB->loadObjectList();
					for ($i=0, $n=count( $rows ); $i < $n; $i++) {
						if ($rows[$i]->c_type == 20) {
							for ($j=0, $m=count( $rows2 ); $j < $m; $j++) {
								if ($rows[$i]->c_pool == $rows2[$j]->c_id) {
									$rows[$i]->c_question = $rows2[$j]->c_question;
									$rows[$i]->qtype_full = _JLMS_QUIZ_QUEST_POOL_SHORT . ' - ' . $rows2[$j]->qtype_full;
									break;
								}
							}
						}
					}
			
				}*/
				//var_dump($rows);
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
						$c_question_id = $row->c_pool_id;
						$group_id = 0;
						$str_user_in_groups = '';
						$obj_GraphStat = JLMS_GraphStatistics($option, $id, $quiz_id, $i, $z, $row, $c_question_id, $group_id, $str_user_in_groups, 1);

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
			
			$ret_str .= "\t" . '<quiz_results><![CDATA['.$results_txt.']]></quiz_results>' . "\n";
//			$footer_html = JoomlaQuiz_template_class::JQ_show_results_footer($footer_ar);
			if(isset($toolbar_footer) && count($toolbar_footer) > 0){
				ksort($toolbar_footer);
				$footer_html = JLMS_ShowToolbar($toolbar_footer, false, 'center');
				$footer_html = str_replace('"components/com_joomla_lms','"'.$JLMS_CONFIG->get('live_site').'/components/com_joomla_lms',$footer_html);
			} else {
				$footer_html = '';
			}
			$entire_footer_data = $footer_html . $footer_html_graf;
			$ret_str .= "\t" . '<quiz_footer><![CDATA['. ($entire_footer_data ? $entire_footer_data : ' ') .']]></quiz_footer>' . "\n";
			// this filed shouldn't be a null - null caused errors in Safari
			
			
		}
	}
	return $ret_str;
}
function JQ_TimeIsUp($quiz) {
	global $JLMS_DB;
	$JLMS_CONFIG = & JLMSFactory::getConfig();
	$ret_str = '';
	//require( dirname(__FILE__) . "/language/english.php");
	// preloading QUIZ languge (28.02.2007 new method) (all quizzes messages now in global quiz language)
	global $JLMS_LANGUAGE, $JLMS_CONFIG;
	JLMS_require_lang($JLMS_LANGUAGE, 'quiz.lang', $JLMS_CONFIG->get('default_language'));
	//require(_JOOMLMS_FRONT_HOME . "/languages/".$JLMS_CONFIG->get('default_language').'/quiz.lang.php');
	require(_JOOMLMS_FRONT_HOME . '/includes/quiz/quiz_language.php');
	global $jq_language;

	/*$query = "SELECT lang_file FROM #__lms_quiz_languages WHERE is_default = 1 and lang_file <> 'default'";
	$JLMS_DB->SetQuery( $query );
	$req_lang = $JLMS_DB->LoadResult();
	if ($req_lang && file_exists( dirname(__FILE__) . "/language/".$req_lang.".php")) {
		include( dirname(__FILE__) . "/language/".$req_lang.".php");
	}*/
	/*if ( ($quiz->c_language) && ($quiz->c_language != 1) ) {
		$query = "SELECT lang_file FROM #__lms_quiz_languages WHERE id = '".intval($quiz->c_language)."'";
		$JLMS_DB->SetQuery( $query );
		$req_lang = $JLMS_DB->LoadResult();
		if ($req_lang && file_exists( dirname(__FILE__) . "/language/".$req_lang.".php")) {
			include( dirname(__FILE__) . "/language/".$req_lang.".php");
		}
	}*/

	$resume_id = intval( mosGetParam ( $_REQUEST, 'resume_id', 0) );
		if($resume_id)
			$stu_quiz_id = $resume_id;
		else 
			$stu_quiz_id = intval( mosGetParam( $_REQUEST, 'stu_quiz_id', 0 ) );

	$unique_id =  mosGetParam ( $_REQUEST, 'unique_id', '') ;
		if($unique_id)
			$user_unique_id = $unique_id;
		else 	
			$user_unique_id = strval( mosGetParam( $_REQUEST, 'user_unique_id', '') );
			
	// 12.03.2007
	#$query = "SELECT template_name FROM #__lms_quiz_templates WHERE id = '".$quiz->c_skin."'";
	#$JLMS_DB->SetQuery( $query );
	#$cur_tmpl = $JLMS_DB->LoadResult();
	$cur_tmpl = 'joomlaquiz_lms_template';
	if ($cur_tmpl) {
		require_once(dirname(__FILE__) . '/templates/'.$cur_tmpl.'/jq_template.php');
		$msg_html = JoomlaQuiz_template_class::JQ_show_messagebox('', $jq_language['quiz_mes_timeout'], 2);
		$ret_str .= "\t" . '<task>time_is_up</task>' . "\n";
		$toolbar = array();
		$toolbar[] = array('btn_type' => 'next', 'btn_js' => "javascript:jq_QuizContinueFinish();void(0);");
		$inside_lp = intval(mosGetParam($_REQUEST, 'inside_lp', 0));
		if ($inside_lp) {
			$toolbar[] = array('btn_type' => 'contents', 'btn_js' => "javascript:ajax_action('contents_lpath');");
		} else {
			if ($quiz->c_slide) {
				//$toolbar[] = array('btn_type' => 'contents', 'btn_js' => "javascript:jq_ShowPanel();");
			}
		}
		$toolbar_code = JLMS_ShowToolbar($toolbar);
		$toolbar_code = str_replace('"components/com_joomla_lms','"'.$JLMS_CONFIG->get('live_site').'/components/com_joomla_lms',$toolbar_code);
		$ret_str .= "\t" . '<quiz_menu><![CDATA['.$toolbar_code.']]></quiz_menu>' . "\n";
		$ret_str .= "\t" . '<quiz_message_box><![CDATA['.$msg_html.']]></quiz_message_box>' . "\n";
		
		$ret_str .= "\t" . '<stu_quiz_id>'.$stu_quiz_id.'</stu_quiz_id>';
		$ret_str .= "\t" . '<user_unique_id>'.$user_unique_id.'</user_unique_id>';
		
		
		
		/*
			echo $stu_quiz_id."<hr>";
			echo $user_unique_id."<hr>";
			echo $quiz_id."<hr>";
		*/
	}
	return $ret_str;
}

function JQ_TimeIsUp_nojs($quiz) {
	global $JLMS_DB;
	$ret_str = '';
	//require( dirname(__FILE__) . "/language/english.php");
	// preloading QUIZ languge (28.02.2007 new method) (all quizzes messages now in global quiz language)
	global $JLMS_LANGUAGE, $JLMS_CONFIG;
	JLMS_require_lang($JLMS_LANGUAGE, 'quiz.lang', $JLMS_CONFIG->get('default_language'));
	//require(_JOOMLMS_FRONT_HOME . "/languages/".$JLMS_CONFIG->get('default_language').'/quiz.lang.php');
	require(_JOOMLMS_FRONT_HOME . '/includes/quiz/quiz_language.php');
	global $jq_language;

	// 12.03.2007
	#$query = "SELECT template_name FROM #__lms_quiz_templates WHERE id = '".$quiz->c_skin."'";
	#$JLMS_DB->SetQuery( $query );
	#$cur_tmpl = $JLMS_DB->LoadResult();
	
	$id 	= intval( mosGetParam( $_REQUEST, 'id', 0 ) );
	$quiz_id 	= intval( mosGetParam( $_REQUEST, 'quiz', 0 ) );
	$stu_quiz_id 	= intval( mosGetParam( $_REQUEST, 'stu_quiz_id', 0 ) );
	$user_unique_id 	= strval( mosGetParam( $_REQUEST, 'user_unique_id', '' ) );
	$quest_id			= intval( mosGetParam( $_REQUEST, 'quest_id', 0 ) );
	
	$cur_tmpl = 'joomlaquiz_lms_template';
	if ($cur_tmpl) {
		require_once(dirname(__FILE__) . '/templates/'.$cur_tmpl.'/jq_template.php');
		$msg_html = JoomlaQuiz_template_class::JQ_show_messagebox('', $jq_language['quiz_mes_timeout'], 2);
//		$ret_str .= "\t" . '<task>time_is_up</task>' . "\n";

		$query = "SELECT a.*, b.lpath_id FROM #__lms_learn_path_step_quiz_results as a, #__lms_learn_path_steps as b WHERE a.stu_quiz_id = '".$stu_quiz_id."' AND a.step_id = b.id";
		$JLMS_DB->setQuery($query);
		$this_lpath = $JLMS_DB->LoadObject();
		
		$query = "SELECT * FROM #__lms_quiz_t_quiz WHERE c_id = '".$quiz_id."'";
		$JLMS_DB->setQuery($query);
		$q_data = $JLMS_DB->loadObject();

		$toolbar = array();
		if(isset($this_lpath->stu_quiz_id) && $this_lpath->stu_quiz_id == $stu_quiz_id){
			if(isset($q_data->c_slide) && $q_data->c_slide){
				$toolbar[] = array('kol_quests' => 0, 'num_quest' => 0, 'quest_score' => 0, 'img_btn'=>'btn_start.png', 'text_btn'=>_JLMS_NEXT_ALT_TITLE, 'link_cont'=>'&task=show_lpath_nojs&action=contents_lpath&course_id='.$id.'&id='.$this_lpath->lpath_id.'&stu_quiz_id='.$stu_quiz_id.'&user_unique_id='.$this_lpath->unique_id.'&step_id='.$this_lpath->step_id.'&user_start_id='.$this_lpath->start_id.'&quest_id='.$quest_id.'&quiz_id='.$quiz_id.'');
			} else {
				$toolbar[] = array('kol_quests' => 0, 'num_quest' => 0, 'quest_score' => 0, 'img_btn'=>'btn_start.png', 'text_btn'=>_JLMS_NEXT_ALT_TITLE);	
			}
		} else {
			if(isset($q_data->c_slide) && $q_data->c_slide){
				$toolbar[] = array('kol_quests' => 0, 'num_quest' => 0, 'quest_score' => 0, 'img_btn'=>'btn_start.png', 'text_btn'=>_JLMS_NEXT_ALT_TITLE, 'link_cont'=>'&task=quiz_action&atask=contents&id='.$id.'&quiz='.$quiz_id.'&stu_quiz_id='.$stu_quiz_id.'&user_unique_id='.$user_unique_id.'&quest_id='.$quest_id.'');
			} else {
				$toolbar[] = array('kol_quests' => 0, 'num_quest' => 0, 'quest_score' => 0, 'img_btn'=>'btn_start.png', 'text_btn'=>_JLMS_NEXT_ALT_TITLE);	
			}
		}
		
		echo JLMS_quiz_ajax_class::JQ_feedback_nojs('finish_stop', $id, $quiz_id, $msg_html, $toolbar, $stu_quiz_id, $user_unique_id, $quest_id);
//		$ret_str .= "\t" . '<div style="text-align:center;">'.$msg_html.'</div>' . "\n";
	}
//	echo $ret_str;
}

function JQ_NextQuestion() {
	global $JLMS_DB, $my;
	$JLMS_CONFIG = & JLMSFactory::getConfig();
	$ret_str = '';
	$quiz_id = intval( mosGetParam( $_REQUEST, 'quiz', 0 ) );
	$inside_lp = intval( mosGetParam( $_REQUEST, 'inside_lp', 0 ) );
	$QA = new JLMS_quiz_API($quiz_id, $inside_lp);
	if (!$QA->quiz_valid()) {
		return '';
	}

	$toolbar_no_a = $QA->quiz_Get_NoAtToolbar();

	//-----------------
		$resume_id = intval( mosGetParam ( $_REQUEST, 'resume_id', 0) );
		
		if($resume_id)
			$stu_quiz_id = $resume_id;
		else 
			$stu_quiz_id = intval( mosGetParam( $_REQUEST, 'stu_quiz_id', 0 ) );

	//-----------------		
		$last_question = intval( mosGetParam( $_REQUEST, 'last_question', 0 ) );

		//echo $last_question; die;
		
		if($last_question || $last_question == -1) {
			
			if($last_question > 0) {
				$quest_id = $last_question;
				
				$query = "SELECT c_score FROM #__lms_quiz_r_student_question WHERE c_question_id = '".$quest_id."' AND c_stu_quiz_id = '".$stu_quiz_id."'";
				$JLMS_DB->setQuery($query);
				$resume_quest_score = $JLMS_DB->LoadResult();
			}	
			else {
				// TODO: resume will not work for entirely GQP/QP quizzes; last quetion should be found in student_quiz_pool instead of quiz_question table !!!
				// 03Nov2009: this section is executed if learner have started the quiz but haven't answered any question. So, we need to gat 1st quiz question here and show it.
				//				but we can't get it from quiz_t_question table, because our quizzes are QP\GQP-based
				// 03Nov2009: commented by DEN. will make $quest_id = -1; here and will extract 1st question after generating QP questions list
				//
				// TODO: if quiz resume -> resume all learner questions from student_quiz_pool
				/*$query = "SELECT c_id FROM #__lms_quiz_t_question WHERE c_quiz_id = '".$quiz_id."' and published = 1 ORDER BY ordering desc LIMIT 1";
				$JLMS_DB->setQuery($query);
				$quest_id = $JLMS_DB->LoadResult();*/
				$quest_id = -1;
			}
		}	
		else {	
			$quest_id = intval( mosGetParam( $_REQUEST, 'quest_id', 0 ) );
		}
			
			
	//-----------------
		$unique_id =  mosGetParam ( $_REQUEST, 'unique_id', '') ;
		if($unique_id)
			$user_unique_id = $unique_id;
		else 	
			$user_unique_id = strval( mosGetParam( $_REQUEST, 'user_unique_id', '') );

			
		$temp_quest_id = $quest_id; // deprecated ????
		
	//-----------------
	//$answer = strval( mosGetParam( $_REQUEST, 'answer', '' ) );
	$answer = strval( isset($_REQUEST['answer']) ? $_REQUEST['answer'] : '' );
	$answer = (get_magic_quotes_gpc()) ? stripslashes( $answer ) : $answer;
	
	$QA->set('stu_quiz_id', $stu_quiz_id);
	$QA->set('user_unique_id', $user_unique_id);
	$QA->quiz_ProcessStartData();
	
	if ( $QA->start_valid() && $quest_id ) {
		
		$quiz = $QA->quiz_data;// temporary for compatibility
		$quiz_params = new JLMSParameters($QA->get_qvar('params'));

		if ($QA->time_is_up()) {
			return JLMS_quiz_ajax_class::JQ_TimeIsUp($quiz);
		}

		# commented 25 April 2007 by DEN
		# We could remove this unnecesary query and find all neede information about question in $q_data;
		/* // get question type
		/$query = "SELECT c_type from #__lms_quiz_t_question WHERE c_id = '".$quest_id."'";
		$JLMS_DB->SetQuery( $query );
		$qtype = $JLMS_DB->LoadResult();*/

		/* insert results to the Database */

		$q_data = $QA->quiz_Get_QuestionList(); // 25 April 2007 (DEN) We need this var here (Early it was declared after 'switch')

		/* * * * * * * (TIP) 25 April 2007 (DEN)
		 * In $q_data array question_type NEVER will be 20 (pool question)
		 * because in function 'quiz_Get_QuestionList()' of 'JLMS_quiz_API' class
		 * we've changed 20 type to the actual type of pool question
		 */
		
		if ($quest_id == -1) {
			if (isset($q_data[0]) && isset($q_data[0]->c_id) && $q_data[0]->c_id ) {
				$quest_id = $q_data[0]->c_id;
			} else {
				return '';
			}
		}
		
		$is_quest_exists = false;
		$qtype = 0;
		$c_pool_quest = 0;
		
		foreach ($q_data as $qd) {
			if ($qd->c_id == $quest_id) {
				$is_quest_exists = true;
				$qtype = $qd->c_type;
				$c_pool_quest = $qd->c_pool;
				$c_pool_quest_gqp = $qd->c_pool_gqp;
				$quest_params = new JLMSParameters($qd->params);
				break;
			}
		}
		if (!$is_quest_exists) {
			return '';
		}

		/* 25 April 2007 (DEN)
		 * These vars are using for compatibility with Question Pool
		 * (We should get answer-data for pool question, but record to DB answers for current question)
		 *
		 * - If current question type is 20 (question is added from pool), then we should process answers
		 *   for question from pool; but id for answers we should use current
		 */
		$proc_qtype = $qtype;
		$proc_quest_id = $quest_id;

		//if ($qtype == 20) {
		if ($c_pool_quest) {
			/* 24 April 2007 (pool question)
			 * We must change vars $qtype and $quest_id to the actual vars (hmmm...? )
			 */
			/*$query = "SELECT * FROM #__lms_quiz_t_question WHERE c_id = $c_pool_quest";
			$JLMS_DB->setQuery($query);
			$pool_quest = $JLMS_DB->LoadObject();
			if (is_object($pool_quest)) {
				$proc_qtype = $pool_quest->c_type;//$qtype;
				$proc_quest_id = $pool_quest->c_id;//$quest_id;
			} else {
				return '';
			}*/
			$proc_quest_id = $c_pool_quest;
		}
		if ($c_pool_quest_gqp) {
			
			$proc_quest_id = $c_pool_quest_gqp;
		}
		
		$is_correct = 0;
		$is_no_attempts = 0;
			
		if(!$resume_id) {
			
			
			//echo $proc_qtype; die;
			
			switch ($proc_qtype) {
				case 1:
				case 3:
				case 12:
					$query = "SELECT a.c_point, b.c_id, a.c_attempts FROM #__lms_quiz_t_question as a, #__lms_quiz_t_choice as b WHERE a.c_id = '".$proc_quest_id."' and b.c_question_id = a.c_id and b.c_right = '1'";
					$JLMS_DB->SetQuery( $query );
					$ddd = $JLMS_DB->LoadObjectList();
					$c_quest_score = 0;
					$c_all_attempts = 1;
					$is_avail = 1;
					
					if (count($ddd)) {
						if ($ddd[0]->c_id == $answer) {
							$c_quest_score = $ddd[0]->c_point;
							$is_correct = 1;
						}
						if (isset($ddd[0]->c_attempts)) {
							$c_all_attempts = $ddd[0]->c_attempts;
						}
					}
					
					$c_quest_cur_attempt = 0;
					$query = "SELECT c_id, c_attempts FROM #__lms_quiz_r_student_question WHERE c_stu_quiz_id = '".$stu_quiz_id."' and c_question_id = '".$quest_id."'";
					$JLMS_DB->SetQuery( $query );
					$c_tmp = $JLMS_DB->LoadObjectList();
					if (count($c_tmp)) {
						$c_quest_cur_attempt = $c_tmp[0]->c_attempts;
						if (($ddd[0]->c_attempts > 0) && $c_quest_cur_attempt >= $c_all_attempts && $c_all_attempts) {
							$ret_str .= "\t" . '<task>no_attempts</task>' . "\n";
							$toolbar_code = JLMS_ShowToolbar($toolbar_no_a);
							$toolbar_code = str_replace('"components/com_joomla_lms','"'.$JLMS_CONFIG->get('live_site').'/components/com_joomla_lms',$toolbar_code);
							$ret_str .= "\t" . '<quiz_menu><![CDATA['.$toolbar_code.']]></quiz_menu>' . "\n";
							$is_avail = 0;
							$is_no_attempts = 1;
						}
						if ($is_avail) {
							$query = "DELETE FROM #__lms_quiz_r_student_question WHERE c_stu_quiz_id = '".$stu_quiz_id."' and c_question_id = '".$quest_id."'";
							$JLMS_DB->SetQuery( $query );
							$JLMS_DB->query();
							$query = "DELETE FROM #__lms_quiz_r_student_choice WHERE c_sq_id = '".$c_tmp[0]->c_id."'";
							$JLMS_DB->SetQuery( $query );
							$JLMS_DB->query();
						}
					}
					if ($quest_params->get('survey_question')) {
						$is_correct = 1;
					}
					if ($is_avail) {
						$correct = JLMS_quiz_ajax_class::correct_answer($is_correct);
						
						if(isset($resume_quest_score) && $resume_quest_score) {
							$c_quest_score = $resume_quest_score;
						}
							
						$query = "INSERT INTO #__lms_quiz_r_student_question (c_stu_quiz_id, c_question_id, c_score, c_attempts, c_correct)"
						. "\n VALUES('".$stu_quiz_id."', '".$quest_id."', '".$c_quest_score."', '".($c_quest_cur_attempt + 1)."', '".$correct."')";
						$JLMS_DB->SetQuery($query);
						$JLMS_DB->query();
						$c_sq_id = $JLMS_DB->insertid();
						
						$query = "INSERT INTO #__lms_quiz_r_student_choice (c_sq_id, c_choice_id)"
						. "\n VALUES('".$c_sq_id."', '".$answer."')";
						$JLMS_DB->SetQuery($query);
						$JLMS_DB->query();
					}
				break;
				case 2:
				case 13:
					$query = "SELECT a.c_point, a.c_attempts FROM #__lms_quiz_t_question as a WHERE a.c_id = '".$proc_quest_id."'";
					$JLMS_DB->SetQuery( $query );
					$ddd = $JLMS_DB->LoadObjectList();
					$query = "SELECT b.c_id FROM #__lms_quiz_t_question as a, #__lms_quiz_t_choice as b WHERE a.c_id = '".$proc_quest_id."' and b.c_question_id = a.c_id and b.c_right = '1'";
					$JLMS_DB->SetQuery( $query );
					$ddd2 = $JLMS_DB->LoadObjectList();
					$query = "SELECT b.c_id FROM #__lms_quiz_t_question as a, #__lms_quiz_t_choice as b WHERE a.c_id = '".$proc_quest_id."' and b.c_question_id = a.c_id and b.c_right <> '1'";
					$JLMS_DB->SetQuery( $query );
					$ddd3 = $JLMS_DB->LoadObjectList();
					$c_quest_score = 0;
					$c_all_attempts = 1;
					$is_avail = 1;
					$ans_array = explode(',',$answer);
					if (count($ddd2) && count($ddd)) {
						$c_quest_score = $ddd[0]->c_point;
						$is_correct = 1;
						foreach ($ddd2 as $right_row) {
							if (!in_array($right_row->c_id, $ans_array)) {
								$c_quest_score = 0;
								$is_correct = 0; }
						}
						foreach ($ddd3 as $not_right_row) {
							if (in_array($not_right_row->c_id, $ans_array)) {
								$c_quest_score = 0;
								$is_correct = 0; }
						}
					}
					if (count($ddd) && isset($ddd[0]->c_attempts) && $ddd[0]->c_attempts) {
						$c_all_attempts = $ddd[0]->c_attempts;
					}
					$c_quest_cur_attempt = 0;
					$query = "SELECT c_id, c_attempts FROM #__lms_quiz_r_student_question WHERE c_stu_quiz_id = '".$stu_quiz_id."' and c_question_id = '".$quest_id."'";
					$JLMS_DB->SetQuery( $query );
					$c_tmp = $JLMS_DB->LoadObjectList();
					if (count($c_tmp)) {
						$c_quest_cur_attempt = $c_tmp[0]->c_attempts;
						if (($ddd[0]->c_attempts > 0) && $c_quest_cur_attempt >= $c_all_attempts) {
							$ret_str .= "\t" . '<task>no_attempts</task>' . "\n";
							$toolbar_code = JLMS_ShowToolbar($toolbar_no_a);
							$toolbar_code = str_replace('"components/com_joomla_lms','"'.$JLMS_CONFIG->get('live_site').'/components/com_joomla_lms',$toolbar_code);
							$ret_str .= "\t" . '<quiz_menu><![CDATA['.$toolbar_code.']]></quiz_menu>' . "\n";
							$is_avail = 0;
							$is_no_attempts = 1;
						}
						if ($is_avail) {
							$query = "DELETE FROM #__lms_quiz_r_student_question WHERE c_stu_quiz_id = '".$stu_quiz_id."' and c_question_id = '".$quest_id."'";
							$JLMS_DB->SetQuery( $query );
							$JLMS_DB->query();
							$query = "DELETE FROM #__lms_quiz_r_student_choice WHERE c_sq_id = '".$c_tmp[0]->c_id."'";
							$JLMS_DB->SetQuery( $query );
							$JLMS_DB->query();
						}
					}
					if ($quest_params->get('survey_question')) {
						$is_correct = 1;
					}
					if ($is_avail) {
						$correct = JLMS_quiz_ajax_class::correct_answer($is_correct);
						
						if(isset($resume_quest_score) && $resume_quest_score)
							$c_quest_score = $resume_quest_score;
						
						$query = "INSERT INTO #__lms_quiz_r_student_question (c_stu_quiz_id, c_question_id, c_score, c_attempts, c_correct)"
						. "\n VALUES('".$stu_quiz_id."', '".$quest_id."', '".$c_quest_score."', '".($c_quest_cur_attempt + 1)."', '".$correct."')";
						$JLMS_DB->SetQuery($query);
						$JLMS_DB->query();
						$c_sq_id = $JLMS_DB->insertid();
						$i = 0;
						while ($i < count($ans_array)) {
							$query = "INSERT INTO #__lms_quiz_r_student_choice (c_sq_id, c_choice_id)"
							. "\n VALUES('".$c_sq_id."', '".$ans_array[$i]."')";
							$JLMS_DB->SetQuery($query);
							$JLMS_DB->query();
							$i ++;
						}
					}
				break;
				case 4:
				case 5:
				case 11:
					$query = "SELECT a.c_point, a.c_attempts FROM #__lms_quiz_t_question as a WHERE a.c_id = '".$proc_quest_id."'";
					$JLMS_DB->SetQuery( $query );
					$ddd = $JLMS_DB->LoadObjectList();
					$query = "SELECT b.c_id, b.c_left_text, b.c_right_text FROM #__lms_quiz_t_question as a, #__lms_quiz_t_matching as b WHERE a.c_id = '".$proc_quest_id."' and b.c_question_id = a.c_id ORDER BY b.ordering";
					$JLMS_DB->SetQuery( $query );
					$ddd2 = $JLMS_DB->LoadObjectList();
					$c_quest_score = 0;
					$c_all_attempts = 1;
					$is_avail = 1;
					$answer = urldecode($answer);
					$ans_array = explode('```',$answer);
					$ans_array_values = array();
					if (count($ddd2) && count($ddd)) {
						$c_quest_score = $ddd[0]->c_point;
						$is_correct = 1; $rr_num = 0;

						for ($di = 0, $dn = count($ddd2); $di < $dn; $di ++) {
							$ddd2[$di]->c_right_text_md5 = md5($ddd2[$di]->c_right_text);
						}
						foreach ($ans_array as $ans_array_one) {
							foreach ($ddd2 as $right_row) {
								if ($ans_array_one == $right_row->c_right_text_md5) {
									$ans_array_values[$ans_array_one] = $right_row->c_right_text;
									break;
								}
							}
						}

						foreach ($ddd2 as $right_row) {
							/**
							 * TODO (started): remove strings comparison.
							 * 		TIPS:	1. we can not compare by id's, because in this case PRO-user with JS debugger can cheat
							 * 				2. may create comparison of md5-hashes (send hashes to the html page instead of answer options)?
							 */
							if (trim($right_row->c_right_text_md5) != trim($ans_array[$rr_num])) {
								$c_quest_score = 0;
								$is_correct = 0;
							}
							$rr_num ++;
						}

						if ($ddd[0]->c_attempts) {
							$c_all_attempts = $ddd[0]->c_attempts;
						}
					}
					$c_quest_cur_attempt = 0;
					$query = "SELECT c_id, c_attempts FROM #__lms_quiz_r_student_question WHERE c_stu_quiz_id = '".$stu_quiz_id."' and c_question_id = '".$quest_id."'";
					$JLMS_DB->SetQuery( $query );
					$c_tmp = $JLMS_DB->LoadObjectList();
					if (count($c_tmp)) {
						$c_quest_cur_attempt = $c_tmp[0]->c_attempts;
						if (($ddd[0]->c_attempts > 0) && $c_quest_cur_attempt >= $c_all_attempts) {
							$ret_str .= "\t" . '<task>no_attempts</task>' . "\n";
							$toolbar_code = JLMS_ShowToolbar($toolbar_no_a);
							$toolbar_code = str_replace('"components/com_joomla_lms','"'.$JLMS_CONFIG->get('live_site').'/components/com_joomla_lms',$toolbar_code);
							$ret_str .= "\t" . '<quiz_menu><![CDATA['.$toolbar_code.']]></quiz_menu>' . "\n";
							$is_avail = 0;
							$is_no_attempts = 1;
						}
						if ($is_avail) {
							$query = "DELETE FROM #__lms_quiz_r_student_question WHERE c_stu_quiz_id = '".$stu_quiz_id."' and c_question_id = '".$quest_id."'";
							$JLMS_DB->SetQuery( $query );
							$JLMS_DB->query();
							$query = "DELETE FROM #__lms_quiz_r_student_matching WHERE c_sq_id = '".$c_tmp[0]->c_id."'";
							$JLMS_DB->SetQuery( $query );
							$JLMS_DB->query();
						}
					}
					if ($quest_params->get('survey_question')) {
						$is_correct = 1;
					}
					if ($is_avail) {
						$correct = JLMS_quiz_ajax_class::correct_answer($is_correct);
						
						if(isset($resume_quest_score) && $resume_quest_score) {
							$c_quest_score = $resume_quest_score;
						}	
						
						$query = "INSERT INTO #__lms_quiz_r_student_question (c_stu_quiz_id, c_question_id, c_score, c_attempts, c_correct)"
						. "\n VALUES('".$stu_quiz_id."', '".$quest_id."', '".$c_quest_score."', '".($c_quest_cur_attempt + 1)."', '".$correct."')";
						$JLMS_DB->SetQuery($query);
						$JLMS_DB->query();
						$c_sq_id = $JLMS_DB->insertid();
						$i = 0;
						while ($i < count($ddd2)) {
							$cur_quest_answer_value = isset($ans_array_values[$ans_array[$i]]) ? $ans_array_values[$ans_array[$i]] : $ans_array[$i]; 
							$query = "INSERT INTO #__lms_quiz_r_student_matching (c_sq_id, c_matching_id, c_sel_text)"
							. "\n VALUES('".$c_sq_id."', '".$ddd2[$i]->c_id."', ".$JLMS_DB->Quote($cur_quest_answer_value).")";
							$JLMS_DB->SetQuery($query);
							$JLMS_DB->query();
							$i ++;
						}
					}
				break;
				case 6:
					$query = "SELECT a.c_point, a.c_attempts FROM #__lms_quiz_t_question as a WHERE a.c_id = '".$proc_quest_id."'";
					$JLMS_DB->SetQuery( $query );
					$ddd = $JLMS_DB->LoadObjectList();
					$query = "SELECT c.c_text FROM #__lms_quiz_t_question as a, #__lms_quiz_t_blank as b, #__lms_quiz_t_text as c WHERE a.c_id = '".$proc_quest_id."' and b.c_question_id = a.c_id and c.c_blank_id = b.c_id";
					$JLMS_DB->SetQuery( $query );
					$ddd2 = $JLMS_DB->LoadObjectList();
					$c_quest_score = 0;
					$c_all_attempts = 1;
					$is_avail = 1;
					$answer = trim($answer);
					if (PHP_VERSION < 5) {
						$answer = php4_utf8_urldecode($answer);
					} else {
						$answer = php5_utf8_urldecode($answer);
					}
					if (count($ddd2) && count($ddd)) {
						/*foreach ($ddd2 as $right_row) {
							if($quest_params->get('case_sensivity', 0)){
								if ($right_row->c_text === $answer) {
									$c_quest_score = $ddd[0]->c_point;
									$is_correct = 1;
								}
							} else {
								if (strtolower($right_row->c_text) === strtolower($answer)) {
									$c_quest_score = $ddd[0]->c_point;
									$is_correct = 1;
								}	
							}
						}*/
						foreach ($ddd2 as $right_row) {
							if($quest_params->get('case_sensivity', 0)){
								if ($right_row->c_text === $answer) {
									$c_quest_score = $ddd[0]->c_point;
									$is_correct = 1;
								}
							} else {
								if (strtolower($right_row->c_text) === strtolower($answer)) {
									$c_quest_score = $ddd[0]->c_point;
									$is_correct = 1;
								}	
							}	
							if (!$is_correct) {
								/**
								 * 01 November 2007 - DEN - bugfix - checking different character encodings
								 * I.e. if browser sent data in UTF, but DB collation is ISO
								 * or another case, if DB collation is UTF, but browser sent response in ISO.
								 * TODO: code is not tested fully. - need testing with ISO (danish, german), cp and UTF
								 */
								if (function_exists('utf8_encode')) {
									$a_u = utf8_encode($right_row->c_text);
									$b_u = utf8_encode($answer);
								} else {
									$a_u = $right_row->c_text;
									$b_u = $answer;
								}
								if($quest_params->get('case_sensivity', 0)){
									if ($a_u === $answer) {
										$c_quest_score = $ddd[0]->c_point;
										$is_correct = 1;
									} else {
										if ($right_row->c_text === $b_u) {
											$c_quest_score = $ddd[0]->c_point;
											$is_correct = 1;
										}
									}
								} else {
									if (strtolower($a_u) === strtolower($answer)) {
										$c_quest_score = $ddd[0]->c_point;
										$is_correct = 1;
									} else {
										if (strtolower($right_row->c_text) === strtolower($b_u)) {
											$c_quest_score = $ddd[0]->c_point;
											$is_correct = 1;
										}
									}
								}
							}
						}

						if ($ddd[0]->c_attempts) {
							$c_all_attempts = $ddd[0]->c_attempts; }
					}
					$c_quest_cur_attempt = 0;
					$query = "SELECT c_id, c_attempts FROM #__lms_quiz_r_student_question WHERE c_stu_quiz_id = '".$stu_quiz_id."' and c_question_id = '".$quest_id."'";
					$JLMS_DB->SetQuery( $query );
					$c_tmp = $JLMS_DB->LoadObjectList();
					if (count($c_tmp)) {
						$c_quest_cur_attempt = $c_tmp[0]->c_attempts;
						if (($ddd[0]->c_attempts > 0) && $c_quest_cur_attempt >= $c_all_attempts) {
							$ret_str .= "\t" . '<task>no_attempts</task>' . "\n";
							$toolbar_code = JLMS_ShowToolbar($toolbar_no_a);
							$toolbar_code = str_replace('"components/com_joomla_lms','"'.$JLMS_CONFIG->get('live_site').'/components/com_joomla_lms',$toolbar_code);
							$ret_str .= "\t" . '<quiz_menu><![CDATA['.$toolbar_code.']]></quiz_menu>' . "\n";
							$is_avail = 0;
							$is_no_attempts = 1;
						}
						if ($is_avail) {
							$query = "DELETE FROM #__lms_quiz_r_student_question WHERE c_stu_quiz_id = '".$stu_quiz_id."' and c_question_id = '".$quest_id."'";
							$JLMS_DB->SetQuery( $query );
							$JLMS_DB->query();
							$query = "DELETE FROM #__lms_quiz_r_student_blank WHERE c_sq_id = '".$c_tmp[0]->c_id."'";
							$JLMS_DB->SetQuery( $query );
							$JLMS_DB->query();
						}
					}
					if ($quest_params->get('survey_question')) {
						$is_correct = 1;
					}
					if ($is_avail) {
						$correct = JLMS_quiz_ajax_class::correct_answer($is_correct);
						
						if(isset($resume_quest_score) && $resume_quest_score)
							$c_quest_score = $resume_quest_score;
						
						$query = "INSERT INTO #__lms_quiz_r_student_question (c_stu_quiz_id, c_question_id, c_score, c_attempts, c_correct)"
						. "\n VALUES('".$stu_quiz_id."', '".$quest_id."', '".$c_quest_score."', '".($c_quest_cur_attempt + 1)."', '".$correct."')";
						$JLMS_DB->SetQuery($query);
						$JLMS_DB->query();
						$c_sq_id = $JLMS_DB->insertid();
						$query = "INSERT INTO #__lms_quiz_r_student_blank (c_sq_id, c_answer)"
						. "\n VALUES('".$c_sq_id."', ". $JLMS_DB->Quote( $answer ) .")";
						$JLMS_DB->SetQuery($query);
						$JLMS_DB->query();
						
					}
				break;
				case 7:
					$query = "SELECT a.c_point, a.c_attempts, b.c_start_x, b.c_start_y, b.c_width, b.c_height FROM #__lms_quiz_t_question as a, #__lms_quiz_t_hotspot as b WHERE a.c_id = '".$proc_quest_id."' and b.c_question_id = a.c_id";
					$JLMS_DB->SetQuery( $query );
					$ddd = $JLMS_DB->LoadObjectList();
					$c_quest_score = 0;
					$c_all_attempts = 1;
					$is_avail = 1;
					if (count($ddd)) {
						$ans_array = explode(',',$answer);
						if ((count($ans_array) == 2) && ($ans_array[0] >= $ddd[0]->c_start_x) && ($ans_array[0] <= ($ddd[0]->c_start_x+$ddd[0]->c_width)) && ($ans_array[1] >= $ddd[0]->c_start_y) && ($ans_array[1] <= ($ddd[0]->c_start_y+$ddd[0]->c_height)) ) {
							$is_correct = 1;
							$c_quest_score = $ddd[0]->c_point;
						}
						if ($ddd[0]->c_attempts) {
							$c_all_attempts = $ddd[0]->c_attempts; }
					}
					$c_quest_cur_attempt = 0;
					$query = "SELECT c_id, c_attempts FROM #__lms_quiz_r_student_question WHERE c_stu_quiz_id = '".$stu_quiz_id."' and c_question_id = '".$quest_id."'";
					$JLMS_DB->SetQuery( $query );
					$c_tmp = $JLMS_DB->LoadObjectList();
					if (count($c_tmp)) {
						$c_quest_cur_attempt = $c_tmp[0]->c_attempts;
						if (($ddd[0]->c_attempts > 0) && $c_quest_cur_attempt >= $c_all_attempts && $c_all_attempts) {
							$ret_str .= "\t" . '<task>no_attempts</task>' . "\n";
							$toolbar_code = JLMS_ShowToolbar($toolbar_no_a);
							$toolbar_code = str_replace('"components/com_joomla_lms','"'.$JLMS_CONFIG->get('live_site').'/components/com_joomla_lms',$toolbar_code);
							$ret_str .= "\t" . '<quiz_menu><![CDATA['.$toolbar_code.']]></quiz_menu>' . "\n";
							$is_avail = 0;
							$is_no_attempts = 1;
						}
						if ($is_avail) {
							$query = "DELETE FROM #__lms_quiz_r_student_question WHERE c_stu_quiz_id = '".$stu_quiz_id."' and c_question_id = '".$quest_id."'";
							$JLMS_DB->SetQuery( $query );
							$JLMS_DB->query();
							$query = "DELETE FROM #__lms_quiz_r_student_hotspot WHERE c_sq_id = '".$c_tmp[0]->c_id."'";
							$JLMS_DB->SetQuery( $query );
							$JLMS_DB->query();
						}
					}
					if ($quest_params->get('survey_question')) {
						$is_correct = 1;
					}
					if ($is_avail) {
						$correct = JLMS_quiz_ajax_class::correct_answer($is_correct);

						if($resume_quest_score)
							$c_quest_score = $resume_quest_score;
						
						$query = "INSERT INTO #__lms_quiz_r_student_question (c_stu_quiz_id, c_question_id, c_score, c_attempts, c_correct)"
						. "\n VALUES('".$stu_quiz_id."', '".$quest_id."', '".$c_quest_score."', '".($c_quest_cur_attempt + 1)."', '".$correct."')";
						$JLMS_DB->SetQuery($query);
						$JLMS_DB->query();
						$c_sq_id = $JLMS_DB->insertid();
						$query = "INSERT INTO #__lms_quiz_r_student_hotspot (c_sq_id, c_select_x, c_select_y)"
						. "\n VALUES('".$c_sq_id."', '".(isset($ans_array[0])?$ans_array[0]:0)."', '".(isset($ans_array[1])?$ans_array[1]:0)."')";
						$JLMS_DB->SetQuery($query);
						$JLMS_DB->query();
					}
				break;
				case 8:
					$query = "SELECT a.c_point, a.c_attempts FROM #__lms_quiz_t_question as a WHERE a.c_id = '".$proc_quest_id."'";
					$JLMS_DB->SetQuery( $query );
					$ddd = $JLMS_DB->LoadObjectList();
					
					$c_quest_score = 0;
					$c_all_attempts = 1;
					$is_avail = 1;
					$answer = trim(urldecode($answer));
					if (count($ddd)) {
						if ($answer) {
							$is_correct = 1;
							$c_quest_score = $ddd[0]->c_point;
						}
						if ($ddd[0]->c_attempts) {
							$c_all_attempts = $ddd[0]->c_attempts; }
					}
					$c_quest_cur_attempt = 0;
					$query = "SELECT c_id, c_attempts FROM #__lms_quiz_r_student_question WHERE c_stu_quiz_id = '".$stu_quiz_id."' and c_question_id = '".$quest_id."'";
					$JLMS_DB->SetQuery( $query );
					$c_tmp = $JLMS_DB->LoadObjectList();
					if (count($c_tmp)) {
						$c_quest_cur_attempt = $c_tmp[0]->c_attempts;
						if (($ddd[0]->c_attempts > 0) && ($c_quest_cur_attempt >= $c_all_attempts) && $c_all_attempts) {
							$ret_str .= "\t" . '<task>no_attempts</task>' . "\n";
							$toolbar_code = JLMS_ShowToolbar($toolbar_no_a);
							$toolbar_code = str_replace('"components/com_joomla_lms','"'.$JLMS_CONFIG->get('live_site').'/components/com_joomla_lms',$toolbar_code);
							$ret_str .= "\t" . '<quiz_menu><![CDATA['.$toolbar_code.']]></quiz_menu>' . "\n";
							$is_avail = 0;
							$is_no_attempts = 1;
						}
						if ($is_avail) {
							$query = "DELETE FROM #__lms_quiz_r_student_question WHERE c_stu_quiz_id = '".$stu_quiz_id."' and c_question_id = '".$quest_id."'";
							$JLMS_DB->SetQuery( $query );
							$JLMS_DB->query();
							$query = "DELETE FROM #__lms_quiz_r_student_survey WHERE c_sq_id = '".$c_tmp[0]->c_id."'";
							$JLMS_DB->SetQuery( $query );
							$JLMS_DB->query();
						}
					}
					if ($quest_params->get('survey_question')) {
						$is_correct = 1;
					}
					if ($is_avail) {
						$correct = JLMS_quiz_ajax_class::correct_answer($is_correct);

						if($resume_quest_score)
							$c_quest_score = $resume_quest_score;
						
						$query = "INSERT INTO #__lms_quiz_r_student_question (c_stu_quiz_id, c_question_id, c_score, c_attempts, c_correct)"
						. "\n VALUES('".$stu_quiz_id."', '".$quest_id."', '".$c_quest_score."', '".($c_quest_cur_attempt + 1)."', '".$correct."')";
						$JLMS_DB->SetQuery($query);
						$JLMS_DB->query();
						$c_sq_id = $JLMS_DB->insertid();
						$query = "INSERT INTO #__lms_quiz_r_student_survey (c_sq_id, c_answer)"
						. "\n VALUES('".$c_sq_id."', ". $JLMS_DB->Quote( $answer ) .")";
						$JLMS_DB->SetQuery($query);
						$JLMS_DB->query();
					}
				break;
				case 9:
					$query = "SELECT a.c_point, a.c_attempts FROM #__lms_quiz_t_question as a WHERE a.c_id = '".$proc_quest_id."'";
					$JLMS_DB->SetQuery( $query );
					$ddd = $JLMS_DB->LoadObjectList();
					$is_correct = 1;
					$c_quest_score = 0;
					$c_all_attempts = 1;
					$is_avail = 1;

					if ($ddd[0]->c_attempts) {
						$c_all_attempts = $ddd[0]->c_attempts; }
					
					$c_quest_cur_attempt = 0;
					$query = "SELECT c_id, c_attempts FROM #__lms_quiz_r_student_question WHERE c_stu_quiz_id = '".$stu_quiz_id."' and c_question_id = '".$quest_id."'";
					$JLMS_DB->SetQuery( $query );
					$c_tmp = $JLMS_DB->LoadObjectList();
					if (count($c_tmp)) {
						$c_quest_cur_attempt = $c_tmp[0]->c_attempts;
						if (($ddd[0]->c_attempts > 0) && $c_quest_cur_attempt >= $c_all_attempts) {
							$ret_str .= "\t" . '<task>no_attempts</task>' . "\n";
							$toolbar_code = JLMS_ShowToolbar($toolbar_no_a);
							$toolbar_code = str_replace('"components/com_joomla_lms','"'.$JLMS_CONFIG->get('live_site').'/components/com_joomla_lms',$toolbar_code);
							$ret_str .= "\t" . '<quiz_menu><![CDATA['.$toolbar_code.']]></quiz_menu>' . "\n";
							$is_avail = 0;
							$is_no_attempts = 1;
						}
						if ($is_avail) {
							$query = "DELETE FROM #__lms_quiz_r_student_question WHERE c_stu_quiz_id = '".$stu_quiz_id."' and c_question_id = '".$quest_id."'";
							$JLMS_DB->SetQuery( $query );
							$JLMS_DB->query();
							$query = "DELETE FROM #__lms_quiz_r_student_scale WHERE c_sq_id = '".$c_tmp[0]->c_id."'";
							$JLMS_DB->SetQuery( $query );
							$JLMS_DB->query();
						}
					}
					if ($quest_params->get('survey_question')) { // not necessary.... this questino is always 'survey' - just for future
						$is_correct = 1;
					}
					if ($is_avail) {
						$correct = JLMS_quiz_ajax_class::correct_answer($is_correct);
						if(isset($resume_quest_score) && $resume_quest_score) {
							$c_quest_score = $resume_quest_score;
						}	
						$query = "INSERT INTO #__lms_quiz_r_student_question (c_stu_quiz_id, c_question_id, c_score, c_attempts, c_correct)"
						. "\n VALUES('".$stu_quiz_id."', '".$quest_id."', '".$c_quest_score."', '".($c_quest_cur_attempt + 1)."', '".$correct."')";
						$JLMS_DB->SetQuery($query);
						$JLMS_DB->query();
						$c_sq_id = $JLMS_DB->insertid();
						$query = "SELECT * FROM #__lms_quiz_t_scale WHERE c_question_id = '".$proc_quest_id."' AND c_type!='1'  ORDER BY ordering";
						$JLMS_DB->SetQuery( $query );
						$c_scal = $JLMS_DB->LoadObjectList();
						$ans_array = explode(',',$answer);
						for($p=0;$p<count($ans_array);$p++)
						{
							$query = "INSERT INTO #__lms_quiz_r_student_scale (c_sq_id, q_scale_id, scale_id)"
							. "\n VALUES('".$c_sq_id."', '". $c_scal[$p]->c_id ."', '". $ans_array[$p] ."')";
							$JLMS_DB->SetQuery($query);
							$JLMS_DB->query();
						}
						
					}
				break;
				case 10:
					$query = "SELECT a.c_attempts FROM #__lms_quiz_t_question as a WHERE a.c_id = '".$proc_quest_id."'";
					$JLMS_DB->SetQuery( $query );
					$ddd = $JLMS_DB->LoadObjectList();
					$c_quest_score = 0;
					$c_all_attempts = 1;
					$is_avail = 1;
					$is_correct = 1;
					$answer = trim(urldecode($answer));
					if (count($ddd)) {
						if ($ddd[0]->c_attempts) {
							$c_all_attempts = $ddd[0]->c_attempts; }
					}
					$c_quest_cur_attempt = 0;
					$query = "SELECT c_id, c_attempts FROM #__lms_quiz_r_student_question WHERE c_stu_quiz_id = '".$stu_quiz_id."' and c_question_id = '".$quest_id."'";
					$JLMS_DB->SetQuery( $query );
					$c_tmp = $JLMS_DB->LoadObjectList();
					if (count($c_tmp)) {
						$c_quest_cur_attempt = $c_tmp[0]->c_attempts;
						if (($ddd[0]->c_attempts > 0) && $c_quest_cur_attempt >= $c_all_attempts) {
							$ret_str .= "\t" . '<task>no_attempts</task>' . "\n";
							$is_avail = 0;
							$is_no_attempts = 1;
						}
						if ($is_avail) {
							$query = "DELETE FROM #__lms_quiz_r_student_question WHERE c_stu_quiz_id = '".$stu_quiz_id."' and c_question_id = '".$quest_id."'";
							$JLMS_DB->SetQuery( $query );
							$JLMS_DB->query();
						}
					}if ($quest_params->get('survey_question')) { // not necessary too.... this questin is just a content page
						$is_correct = 1;
					}
					if ($is_avail) {
						$correct = JLMS_quiz_ajax_class::correct_answer($is_correct);
						if($resume_quest_score)
							$c_quest_score = $resume_quest_score;
						$query = "INSERT INTO #__lms_quiz_r_student_question (c_stu_quiz_id, c_question_id, c_score, c_attempts, c_correct)"
						. "\n VALUES('".$stu_quiz_id."', '".$quest_id."', '".$c_quest_score."', '".($c_quest_cur_attempt + 1)."', '".$correct."')";
						$JLMS_DB->SetQuery($query);
						$JLMS_DB->query();
					}
				break;
				}

			}

			$j = -1;
			$quest_num = 1;

			/* 24 April 2007 (DEN)
			 * Get next question from all quiz quests
			 */
			$query = "SELECT c_question_id FROM #__lms_quiz_r_student_question WHERE c_stu_quiz_id = '".$stu_quiz_id."'";
			$JLMS_DB->SetQuery( $query );
			$q_ids = $JLMS_DB->LoadResultArray();
			$q_num = 0;
			$q_num_ar = 0;
			$qqn = 0;

			// get number of answered questions (to estimate value for progressbar if enabled)
			$progress_quests_answered = count($q_ids);

			foreach ($q_data as $qd) {
				if ($qd->c_id == $quest_id) {
					$q_num = $qqn + 1;
					$q_num_ar = $qqn;
					break;
				}
				$qqn++;
			}
			
/*		
			echo $quest_id;
			echo '<pre>';
			print_r($q_data);
			echo '</pre>';
			echo 'qqn='.$qqn;
			echo 'q_num='.$q_num;
			//(Max): ja tut testil pohodu tut gdeto kosiak
			die;
*/		
			if (!$q_num) {
				return '';
			} else {
				$q_num = 0;
				$q_num_ar = 0;
				if ($last_question == -1) {
					//resume from first question - we need to show first question!
					if ($qqn) {
						$qqn = $qqn - 1;
					}
				}
				//echo $last_question; echo $qqn;die;
				// here we are using $q_num and $q_num_ar for other needs (don't warry :) )
				// find next not answered question
				for ($i = $qqn, $n = count($q_data); $i < $n; $i ++ ) {
					
					if (!in_array($q_data[$i]->c_id, $q_ids) && $q_data[$i]->c_id != $quest_id ) {
						$q_num = $i + 1;
						$q_num_ar = $i;
						break;
					}
				}
				if ($last_question == -1) {
					//resume from first question - we need to show first question!
					if (!$qqn) {
						if (isset($q_data[0]->c_id) && $q_data[0]->c_id == $quest_id) {
							$q_num = 1;
							$q_num_ar = 0;
						}
					}
				}
				

				if (!$q_num) {
					// find not answered question from prev questions
					for ($i = 0; $i < $qqn; $i ++ ) {
						if (!in_array($q_data[$i]->c_id, $q_ids) && $q_data[$i]->c_id != $quest_id ) {
							$q_num = $i + 1;
							$q_num_ar = $i;
							break;
						}
					}
				}
				if ($q_num) {
					$quest_num = $q_num;
					$j = $q_num_ar;
				}
			}
			//
//			echo 'j='.$j;
			//Max: skip mod next question skip
		
		$jx = -1;	
				
		if(isset($q_data[$j]->c_id)) {	
			
			$q_ids[] = $q_data[$j]->c_id;
			$q_num = 0;
			$q_num_ar = 0;
			$qqn = 0;
			foreach ($q_data as $qd) {
				if ($qd->c_id == $quest_id) {
					$q_num = $qqn + 1;
					$q_num_ar = $qqn;
					break;
				}
				$qqn ++;
			}
			if (!$q_num) {
				return '';
			} else {
				$q_num = 0;
				$q_num_ar = 0;
				// here we are using $q_num and $q_num_ar for other needs (don't warry :) )
				// find next not answered question
				for ($i = $qqn, $n = count($q_data); $i < $n; $i ++ ) {
					if (!in_array($q_data[$i]->c_id, $q_ids) && $q_data[$i]->c_id != $quest_id ) {
						$q_num = $i + 1;
						$q_num_ar = $i;
						break;
					}
				}
				if (!$q_num) {
					// find not answered question from prev questions
					for ($i = 0; $i < $qqn; $i ++ ) {
						if (!in_array($q_data[$i]->c_id, $q_ids) && $q_data[$i]->c_id != $quest_id ) {
							$q_num = $i + 1;
							$q_num_ar = $i;
							break;
						}
					}
				}
				if ($q_num) {
					$quest_num_x = $q_num;
					$jx = $q_num_ar;
				}
			}
		}	
		
			/*
			if ($quiz->c_random) {
				$query = "SELECT c_question_id FROM #__lms_quiz_r_student_question WHERE c_stu_quiz_id = '".$stu_quiz_id."'";
				$JLMS_DB->SetQuery( $query );
				$q_ids = $JLMS_DB->LoadResultArray();
				$q_kol_already = count($q_ids);
				if (!count($q_ids)) {
					$q_ids = array(0);
				}
				$qids = implode(',',$q_ids);
				$query = "SELECT * FROM #__lms_quiz_t_question WHERE c_quiz_id = '".$quiz_id."' AND c_id NOT IN (".$qids.") ORDER BY ordering, c_id";
				$JLMS_DB->SetQuery( $query );
				$q_data = $JLMS_DB->LoadObjectList();
				$kol_q = count($q_data);
				if ($kol_q) {
					$j = rand(0, ($kol_q - 1) );
				} else {
					$j = -1;
				}
				$query = "SELECT count(*) FROM #__lms_quiz_t_question WHERE c_quiz_id = '".$quiz_id."'";
				$JLMS_DB->SetQuery( $query );
				$q_total = $JLMS_DB->LoadResult();
				$quest_num = $q_kol_already + 1;
			} else {
				$query = "SELECT ordering from #__lms_quiz_t_question WHERE c_id = '".$quest_id."'";
				$JLMS_DB->SetQuery( $query );
				$qorder = $JLMS_DB->LoadResult();
	
				$query = "SELECT * FROM #__lms_quiz_t_question WHERE c_quiz_id = '".$quiz_id."' ORDER BY ordering, c_id";
				$JLMS_DB->SetQuery($query);
				$q_data = $JLMS_DB->LoadObjectList();
				$i = 0;$j = 0;
	
				while($i < count($q_data)) {
					if ($q_data[$i]->ordering < $qorder) {
						$j ++;
					} elseif (($q_data[$i]->ordering == $qorder) && ($q_data[$i]->c_id < $quest_id)) {
						$j ++;
					} elseif (($q_data[$i]->ordering == $qorder) && ($q_data[$i]->c_id == $quest_id)) {
						$j ++;
					} else {
					}
					$i ++;
				}
				$quest_num = $j + 1;
				if (!isset($q_data[$j])) {
					$query = "SELECT count(*) FROM #__lms_quiz_t_question WHERE c_quiz_id = '".$quiz_id."'";
					$JLMS_DB->SetQuery( $query );
					$qk = $JLMS_DB->LoadResult();
					$query = "SELECT c_question_id FROM #__lms_quiz_r_student_question WHERE c_stu_quiz_id = '".$stu_quiz_id."'";
					$JLMS_DB->SetQuery( $query );
					$qq_d = $JLMS_DB->LoadResultArray();
					$qq = count($qq_d);
					if ( $qk && $qq && ($qk != $qq)) {
						if (!count($qq_d)) { $qq_d = array(0); }
						$qids = implode(',',$qq_d);
						$query = "SELECT * FROM #__lms_quiz_t_question WHERE c_quiz_id = '".$quiz_id."' AND c_id NOT IN (".$qids.") ORDER BY ordering, c_id";
						$JLMS_DB->SetQuery( $query );
						$q_data = $JLMS_DB->LoadObjectList();
						$kol_q = count($q_data);
						if ($kol_q) { $j = 0; } else { $j = -1; }
						$quest_num = $qq + 1;
					}
				}
			}*/
			$is_avail = 1;
			
			if(isset($c_quest_cur_attempt) && isset($c_all_attempts)) {
				if (($c_quest_cur_attempt + 1) >= $c_all_attempts) { $is_avail = 0; }
			}
			

			//require( dirname(__FILE__) . "/language/english.php");
			// preloading QUIZ languge (28.02.2007 new method) (all quizzes messages now in global quiz language)
			global $JLMS_LANGUAGE, $JLMS_CONFIG;
			JLMS_require_lang($JLMS_LANGUAGE, 'quiz.lang', $JLMS_CONFIG->get('default_language'));
			//require(_JOOMLMS_FRONT_HOME . "/languages/".$JLMS_CONFIG->get('default_language').'/quiz.lang.php');
			require(_JOOMLMS_FRONT_HOME . '/includes/quiz/quiz_language.php');
			global $jq_language;

			/*$query = "SELECT lang_file FROM #__lms_quiz_languages WHERE is_default = 1 and lang_file <> 'default'";
			$JLMS_DB->SetQuery( $query );
			$req_lang = $JLMS_DB->LoadResult();
			if ($req_lang && file_exists( dirname(__FILE__) . "/language/".$req_lang.".php")) {
				include( dirname(__FILE__) . "/language/".$req_lang.".php");
			}*/
			/*if ( ($quiz->c_language) && ($quiz->c_language != 1) ) {
				$query = "SELECT lang_file FROM #__lms_quiz_languages WHERE id = '".intval($quiz->c_language)."'";
				$JLMS_DB->SetQuery( $query );
				$req_lang = $JLMS_DB->LoadResult();
				if ($req_lang && file_exists( dirname(__FILE__) . "/language/".$req_lang.".php")) {
					include( dirname(__FILE__) . "/language/".$req_lang.".php");
				}
			}*/
			
			if ($quiz->c_wrong_message) $jq_language['quiz_answer_incorrect'] = htmlspecialchars(nl2br($quiz->c_wrong_message));
			if ($quiz->c_right_message) $jq_language['quiz_answer_correct'] = htmlspecialchars(nl2br($quiz->c_right_message));
			//$q_t_params = $QA->get_qvar('params');
			if ($quiz_params->get('disable_quest_feedback') || $quest_params->get('disable_quest_feedback')) {
				
			} else {
				$query = "SELECT * FROM #__lms_quiz_t_question_fb WHERE quest_id = $quest_id";
				$JLMS_DB->SetQuery($query);
				$q_fbs = $JLMS_DB->LoadObjectList();
				foreach ($q_fbs as $qfb) {
					if ($qfb->choice_id == -1) {
						if ($qfb->fb_text) {
							$jq_language['quiz_answer_incorrect'] = $qfb->fb_text;
						}
					} elseif(!$qfb->choice_id) {
						if ($qfb->fb_text) {
							$jq_language['quiz_answer_correct'] = $qfb->fb_text;
							$jq_language['quiz_answer_accepted'] = $qfb->fb_text;
						}
					}
				}
			}
			
			//Max: modign skip question
//			$quiz_params = new JLMSParameters($QA->quiz_data->params);
			$skip_quest = $quiz_params->get('sh_skip_quest', 0);
			$next_quest = 0;
			if($skip_quest){
				$next_quest = isset($q_data[$jx]->c_id)?$q_data[$jx]->c_id:0;
			} else {
				$next_quest = 0;
			}
			
			// 12.03.2007
			#$query = "SELECT template_name FROM #__lms_quiz_templates WHERE id = '".$quiz->c_skin."'";
			#$JLMS_DB->SetQuery( $query );
			#$cur_tmpl = $JLMS_DB->LoadResult();
			$cur_tmpl = 'joomlaquiz_lms_template';
			if ($cur_tmpl) {
				require_once(dirname(__FILE__) . '/templates/'.$cur_tmpl.'/jq_template.php');
				$is_correct_report = $is_correct;
				if ($is_no_attempts == 1) {
					//get information about correct/incorrect answer from previous attempt.
					//to avoid corruption of statistics at the frontend (in the list of quiz questions - slide panel)
					$query = "SELECT c_correct FROM #__lms_quiz_r_student_question WHERE c_stu_quiz_id = '".$stu_quiz_id."' AND c_question_id = '".$quest_id."'";
					$JLMS_DB->SetQuery($query);
					$is_correct_db = intval($JLMS_DB->LoadResult());
					if ($is_correct_db == 2) {
						$is_correct_report = 1;
					} else {
						$is_correct_report = 0;
					}
				}

				$kol_quests = count($q_data);

				if (isset($q_data[$j])){
					$toolbar = array();
					if ($is_correct) {
						$toolbar[] = array('btn_type' => 'next', 'btn_js' => "javascript:jq_QuizContinue();void(0);");
					} else {
						if ($is_avail) {
							$toolbar[] = array('btn_type' => 'prev', 'btn_js' => "javascript:jq_QuizBack();void(0);");
							$toolbar[] = array('btn_type' => 'next', 'btn_js' => "javascript:jq_QuizContinue();void(0);");
						} else {
							$toolbar[] = array('btn_type' => 'next', 'btn_js' => "javascript:jq_QuizContinue();void(0);");
						}
					}
					$inside_lp = intval(mosGetParam($_REQUEST, 'inside_lp', 0));
					if ($inside_lp) {
						$toolbar[] = array('btn_type' => 'contents', 'btn_js' => "javascript:ajax_action('contents_lpath');");
					} else {
						if ($quiz->c_slide) {
							$toolbar[] = array('btn_type' => 'contents', 'btn_js' => "javascript:jq_ShowPanel();");
						}
					}

					$exec_resume_script = '';
					$quiz->resume_timer_value = 0;

					if($resume_id) {
						$ret_str .= "\t" . '<task>resume</task>' . "\n";
						$ret_str .= "\t" . '<user_unique_id>'.$unique_id.'</user_unique_id>' . "\n";
						$ret_str .= "\t" . '<stu_quiz_id>'.$resume_id.'</stu_quiz_id>' . "\n";

						$ret_str .= "\t" . '<quiz_count_quests>'.$kol_quests.'</quiz_count_quests>' . "\n";

						$quiz->resume_timer_value = $QA->get_user_time();
						$quiz_time_limit = intval($QA->get_qvar('c_time_limit')) * 60;
						$exec_resume_script = 'timer_sec = '.$quiz->resume_timer_value.'; max_quiz_time = '.$quiz_time_limit.';';
					} else {
						$ret_str .= "\t" . '<task>next</task>' . "\n";
					}

					$progressbar_value = intval((100/$kol_quests)*$progress_quests_answered);
					$ret_str .= "\t" . '<progress_quests_done>'.$progressbar_value.'</progress_quests_done>' . "\n";

					$ret_str .= "\t" . '<quiz_menu><![CDATA['.JLMS_ShowToolbar($toolbar).']]></quiz_menu>' . "\n";
					$ret_str .= "\t" . '<quiz_quest_num>'.$quest_num.'</quiz_quest_num>' . "\n";
					$ret_str .= "\t" . '<quiz_prev_correct>'.$is_correct_report.'</quiz_prev_correct>' . "\n";
					$ret_str .= "\t" . '<quiz_skip_next_quest>'.$next_quest.'</quiz_skip_next_quest>' . "\n";
					if ($quiz_params->get('disable_quest_feedback') || $quest_params->get('disable_quest_feedback') || $quest_params->get('survey_question') || $qtype == 10) {
						$ret_str .= "\t" . '<quest_feedback>0</quest_feedback>' . "\n";
						$ret_str .= "\t" . '<quest_feedback_repl_func><![CDATA[jq_QuizContinue();]]></quest_feedback_repl_func>' . "\n";
						$msg_html = ' ';
					} else {
						$msg_html = JoomlaQuiz_template_class::JQ_show_messagebox('', (($is_correct)?(($qtype == 8)?$jq_language['quiz_answer_accepted']:$jq_language['quiz_answer_correct']):$jq_language['quiz_answer_incorrect']), $is_correct);
						$ret_str .= "\t" . '<quest_feedback>1</quest_feedback>' . "\n";
						$ret_str .= "\t" . '<quest_feedback_repl_func>0</quest_feedback_repl_func>' . "\n";
					}
					if ($is_no_attempts == 1) {
						$msg_html = JoomlaQuiz_template_class::JQ_show_messagebox('', $jq_language['mes_no_attempts'], 2);
					}
					$ret_str .= "\t" . '<quiz_message_box><![CDATA['.$msg_html.']]></quiz_message_box>' . "\n";
					$ret_str .= "\t" . '<quiz_allow_attempt>'.$is_avail.'</quiz_allow_attempt>' . "\n";
					
					$ret_str .= JLMS_quiz_ajax_class::JQ_GetQuestData($q_data[$j], $jq_language, $QA->get('stu_quiz_id',0),$exec_resume_script);
					
					if($resume_id) {
						$ret_str .= JLMS_quiz_ajax_class::JQ_GetPanelData_resume($quiz->c_id, $q_data);
						//echo JLMS_quiz_ajax_class::JQ_GetPanelData($quiz->c_id, $q_data); die;
					}	

					
				} else {
					$ret_str .= "\t" . '<task>finish</task>' . "\n";
					if ($is_no_attempts == 1) {
						$ret_str = "\t" . '<task>finish</task>' . "\n";
					}

					$progressbar_value = intval((100/$kol_quests)*$progress_quests_answered);
					$ret_str .= "\t" . '<progress_quests_done>'.$progressbar_value.'</progress_quests_done>' . "\n";

					$toolbar = array();
					if ($is_correct) {
						$toolbar[] = array('btn_type' => 'next', 'btn_js' => "javascript:jq_QuizContinueFinish();void(0);");
					} else {
						if ($is_avail) {
							$toolbar[] = array('btn_type' => 'prev', 'btn_js' => "javascript:jq_QuizBack();void(0);");
							$toolbar[] = array('btn_type' => 'next', 'btn_js' => "javascript:jq_QuizContinueFinish();void(0);");
						} else {
							$toolbar[] = array('btn_type' => 'next', 'btn_js' => "javascript:jq_QuizContinueFinish();void(0);");
						}
					}
					if($skip_quest && $next_quest){
						$toolbar[] = array('btn_type' => 'skip', 'btn_js' => "javascript:JQ_gotoQuestion(".$next_quest.");void(0);");	
					}
					$inside_lp = intval(mosGetParam($_REQUEST, 'inside_lp', 0));
					if ($inside_lp) {
						$toolbar[] = array('btn_type' => 'contents', 'btn_js' => "javascript:ajax_action('contents_lpath');");
					} else {
						if ($quiz->c_slide) {
							$toolbar[] = array('btn_type' => 'contents', 'btn_js' => "javascript:jq_ShowPanel();");
						}
					}
					$ret_str .= "\t" . '<quiz_menu><![CDATA['.JLMS_ShowToolbar($toolbar).']]></quiz_menu>' . "\n";

					$ret_str .= "\t" . '<quiz_prev_correct>'.$is_correct_report.'</quiz_prev_correct>' . "\n";
					
					if ($quiz_params->get('disable_quest_feedback') || $quest_params->get('disable_quest_feedback') || $quest_params->get('survey_question') || $qtype == 10) {
						$ret_str .= "\t" . '<quest_feedback>0</quest_feedback>' . "\n";
						$ret_str .= "\t" . '<quest_feedback_repl_func><![CDATA[jq_QuizContinueFinish();]]></quest_feedback_repl_func>' . "\n";
						$msg_html = ' ';
					} else {
						$msg_html = JoomlaQuiz_template_class::JQ_show_messagebox('', (($is_correct)?(($qtype == 8)?$jq_language['quiz_answer_accepted']:$jq_language['quiz_answer_correct']):$jq_language['quiz_answer_incorrect']), $is_correct);
						$ret_str .= "\t" . '<quest_feedback>1</quest_feedback>' . "\n";
						$ret_str .= "\t" . '<quest_feedback_repl_func>0</quest_feedback_repl_func>' . "\n";
					}
					if ($is_no_attempts == 1) {
						$msg_html = JoomlaQuiz_template_class::JQ_show_messagebox('', $jq_language['mes_no_attempts'], 2);
					}
					$ret_str .= "\t" . '<quiz_message_box><![CDATA['.$msg_html.']]></quiz_message_box>' . "\n";
					
					
					/*$msg_html = JoomlaQuiz_template_class::JQ_show_messagebox('', (($is_correct)?(($qtype == 8)?$jq_language['quiz_answer_accepted']:$jq_language['quiz_answer_correct']):$jq_language['quiz_answer_incorrect']));
					if ($is_no_attempts == 1) {
						$msg_html = JoomlaQuiz_template_class::JQ_show_messagebox('', $jq_language['mes_no_attempts']);
					}
					$ret_str .= "\t" . '<quiz_message_box><![CDATA['.$msg_html.']]></quiz_message_box>' . "\n";*/
					$ret_str .= "\t" . '<quiz_allow_attempt>'.$is_avail.'</quiz_allow_attempt>' . "\n";

					$query = "SELECT sum(c_score) FROM #__lms_quiz_r_student_question WHERE c_stu_quiz_id = '".$stu_quiz_id."'";
					$JLMS_DB->SetQuery( $query );
					$q_total_score = $JLMS_DB->LoadResult();

					$query = "SELECT c_date_time FROM #__lms_quiz_r_student_quiz WHERE c_id = '".$stu_quiz_id."'";
					$JLMS_DB->SetQuery( $query );
					$q_beg_time = $JLMS_DB->LoadResult();

					$q_time_total = time() - date('Z') - strtotime($q_beg_time);
		
					if(!$resume_id) {
					$query = "UPDATE #__lms_quiz_r_student_quiz SET c_total_score = '".$q_total_score."', c_total_time = '".$q_time_total."' WHERE c_id = '".$stu_quiz_id."'";
					$JLMS_DB->SetQuery($query);
					$JLMS_DB->query();
					}
				}
			}
	}
	return $ret_str;
}
function JQ_SeekQuestion() {
	global $JLMS_DB, $my;
	

	$ret_str = '';
	$quiz_id = intval( mosGetParam( $_REQUEST, 'quiz', 0 ) );
	$inside_lp = intval( mosGetParam( $_REQUEST, 'inside_lp', 0 ) );
	$QA = new JLMS_quiz_API($quiz_id, $inside_lp);
	if (!$QA->quiz_valid()) {
		return '';
	}

	$stu_quiz_id = intval( mosGetParam( $_REQUEST, 'stu_quiz_id', 0 ) );
	$seek_quest_id = intval( mosGetParam( $_REQUEST, 'seek_quest_id', 0 ) );
	$user_unique_id = strval( mosGetParam( $_REQUEST, 'user_unique_id', '') );

	$QA->set('stu_quiz_id', $stu_quiz_id);
	$QA->set('user_unique_id', $user_unique_id);
	$QA->quiz_ProcessStartData(); // fill in start_valid private variable

	if ( $QA->start_valid() && $seek_quest_id ) {

		$quiz = $QA->quiz_data;// temporary for compatibility

		if ($QA->time_is_up()) {
			return JLMS_quiz_ajax_class::JQ_TimeIsUp($quiz);
		}

		$q_data = $QA->quiz_Get_QuestionList();
		$seek_avail = false;
		$i = 0;
		foreach ($q_data as $qd) {
			if ($qd->c_id == $seek_quest_id) {
				$seek_avail = true;
				break;
			}
			$i ++;
		}

		if ($seek_avail) { // if Seek question from the current quiz

			$quest_num = $i + 1; // number of question in the quiz sequence

			global $JLMS_LANGUAGE, $JLMS_CONFIG;
			JLMS_require_lang($JLMS_LANGUAGE, 'quiz.lang', $JLMS_CONFIG->get('default_language'));
			//require(_JOOMLMS_FRONT_HOME . "/languages/".$JLMS_CONFIG->get('default_language').'/quiz.lang.php');
			require(_JOOMLMS_FRONT_HOME . '/includes/quiz/quiz_language.php');
			global $jq_language;
			if ($quiz->c_wrong_message) $jq_language['quiz_answer_incorrect'] = htmlspecialchars(nl2br($quiz->c_wrong_message));
			if ($quiz->c_right_message) $jq_language['quiz_answer_correct'] = htmlspecialchars(nl2br($quiz->c_right_message));

			$cur_tmpl = 'joomlaquiz_lms_template';
			if ($cur_tmpl) {
				require_once(dirname(__FILE__) . '/templates/'.$cur_tmpl.'/jq_template.php');
				if (isset($q_data[$i])) {
					$query = "SELECT c_question_id FROM #__lms_quiz_r_student_question WHERE c_stu_quiz_id = '".$stu_quiz_id."'";
					$JLMS_DB->SetQuery( $query );
					$q_ids = $JLMS_DB->LoadResultArray();
					$progress_quests_answered = count($q_ids);

					$quiz_params = new JLMSParameters($QA->quiz_data->params);
					$next_quest = 0;
					$skip_quest = $quiz_params->get('sh_skip_quest', 0);
					if($skip_quest){
						$q_num = 0;
						$q_num_ar = 0;
						$qqn = 0;
						foreach ($q_data as $qd) {
							if ($qd->c_id == $seek_quest_id) {
								$q_num = $qqn + 1;
								$q_num_ar = $qqn;
								break;
							}
							$qqn ++;
						}
						if (!$q_num) {
							return '';
						} else {
							$q_num = 0;
							$q_num_ar = 0;
							// here we are using $q_num and $q_num_ar for other needs (don't warry :) )
							// find next not answered question
							for ($j = $qqn, $n = count($q_data); $j < $n; $j ++ ) {
								if (!in_array($q_data[$j]->c_id, $q_ids) && $q_data[$j]->c_id != $seek_quest_id ) {
									$q_num = $j + 1;
									$q_num_ar = $j;
									break;
								}
							}
							if (!$q_num) {
								// find not answered question from prev questions
								for ($j = 0; $j < $qqn; $j ++ ) {
									if (!in_array($q_data[$j]->c_id, $q_ids) && $q_data[$j]->c_id != $seek_quest_id ) {
										$q_num = $j + 1;
										$q_num_ar = $j;
										break;
									}
								}
							}
							if ($q_num) {
//								$quest_num = $q_num;
								$jj = $q_num_ar;
							}
						}
						$next_quest = $q_data[$jj]->c_id;
					}

					$toolbar = $QA->quiz_Get_StartToolbar($q_data[$i]->c_type, $skip_quest, $next_quest); //toolbar with 'Next' buttond and (if available)'Contents' button (from LP or QUIZ)

					$ret_str .= "\t" . '<task>seek_quest</task>' . "\n";
					$ret_str .= "\t" . '<quiz_menu><![CDATA['.JLMS_ShowToolbar($toolbar).']]></quiz_menu>' . "\n";
					$ret_str .= "\t" . '<quiz_quest_num>'.$quest_num.'</quiz_quest_num>' . "\n";

					$kol_quests = count($q_data);
					$progressbar_value = intval((100/$kol_quests)*$progress_quests_answered);
					$ret_str .= "\t" . '<progress_quests_done>'.$progressbar_value.'</progress_quests_done>' . "\n";

					$ret_str .= JLMS_quiz_ajax_class::JQ_GetQuestData($q_data[$i], $jq_language, $QA->get('stu_quiz_id',0));
				}
			}
		}
	}
	return $ret_str;
}

function JQ_QuestPreview() {
	global $JLMS_DB, $my;
	
	$ret_str = '';
	$quiz_id = intval( mosGetParam( $_REQUEST, 'quiz', 0 ) );
	$quest_id = intval( mosGetParam( $_REQUEST, 'quest_id', 0 ) );
	$preview_id = '111';//strval( mosGetParam( $_REQUEST, 'preview_id', '' ) );
	$id = intval( mosGetParam( $_REQUEST, 'id', 0 ) );
	$usertype = JLMS_GetUserType($my->id, $id);

	$query = "SELECT * FROM #__lms_quiz_t_quiz WHERE c_id = '".$quiz_id."' AND course_id = '".$id."'";
	$JLMS_DB->SetQuery ($query );
	$quiz = $JLMS_DB->LoadObjectList();
	if (count($quiz)) {
		$quiz = $quiz[0];
	} else {
		$quiz = new stdClass();
		$quiz->c_wrong_message = '';
		$quiz->c_right_message = '';
	}
	//$query = "SELECT c_par_value FROM #__lms_quiz_setup WHERE c_par_name = 'admin_preview'";
	//$JLMS_DB->SetQuery( $query );
	$preview_code = '111';//$JLMS_DB->LoadResult();
	$query = "SELECT c_quiz_id FROM #__lms_quiz_t_question WHERE c_id = '".$quest_id."'";
	$JLMS_DB->SetQuery( $query );
	$q_quiz = $JLMS_DB->LoadResult();
	
	if(!$id){ //UPZX-2572 - movingedu GQP bug
		$query = "SELECT COUNT(*) FROM #__lms_users as a, #__lms_usertypes as b WHERE a.lms_usertype_id = b.id AND a.user_id = '".$my->id."'";
		$JLMS_DB->setQuery($query);
		$exist_in_table = $JLMS_DB->loadResult();
		
		if($exist_in_table){
			$usertype = 1;
		}
	}
	
	if (($quiz_id == $q_quiz) && ($usertype == 1) && ($preview_id == $preview_code) && ($quest_id)) {

		$query = "SELECT * FROM #__lms_quiz_t_question WHERE c_quiz_id = '".$quiz_id."' AND c_id = '".$quest_id."'";
		$JLMS_DB->SetQuery($query);
		$q_data = $JLMS_DB->LoadObjectList();
		
		if (isset($q_data[0]->c_type) && $q_data[0]->c_type == 20) {
			$query = "SELECT a.* FROM #__lms_quiz_t_question as a"
			. "\n WHERE a.c_id = ".$q_data[0]->c_pool;
			$JLMS_DB->setQuery( $query );
			$pool_quest = $JLMS_DB->loadObject();
			if (is_object($pool_quest)) {
				// dont' change the ID !!!'
				//$q_data[0]->old_c_id = $q_data[0]->c_id;
				//$q_data[0]->c_id = $pool_quest->c_id;
				$q_data[0]->c_question = $pool_quest->c_question;
				$q_data[0]->c_point = $pool_quest->c_point;
				$q_data[0]->c_attempts = $pool_quest->c_attempts;
				$q_data[0]->c_type = $pool_quest->c_type;
				$q_data[0]->params = $pool_quest->params;
			}
		}
		
		if (isset($q_data[0]->c_type) && $q_data[0]->c_type == 21) {
			$query = "SELECT a.* FROM #__lms_quiz_t_question as a"
			. "\n WHERE a.c_id = ".$q_data[0]->c_pool_gqp;
			$JLMS_DB->setQuery( $query );
			$pool_quest = $JLMS_DB->loadObject();
			if (is_object($pool_quest)) {
				// dont' change the ID !!!'
				//$q_data[0]->old_c_id = $q_data[0]->c_id;
				//$q_data[0]->c_id = $pool_quest->c_id;
				$q_data[0]->c_question = $pool_quest->c_question;
				$q_data[0]->c_point = $pool_quest->c_point;
				$q_data[0]->c_attempts = $pool_quest->c_attempts;
				$q_data[0]->c_type = $pool_quest->c_type;
				$q_data[0]->params = $pool_quest->params;
			}
		}
		
		if ($quiz_id) {
			$query = "SELECT count(*) FROM #__lms_quiz_t_question WHERE c_quiz_id = '".$quiz_id."'";
			$JLMS_DB->SetQuery($query);
			$q_data_count = $JLMS_DB->LoadResult();
		} else {
			$q_data_count = 0;
		}

		//require( dirname(__FILE__) . "/language/english.php");
		// preloading QUIZ languge (28.02.2007 new method) (all quizzes messages now in global quiz language)
		global $JLMS_LANGUAGE, $JLMS_CONFIG;
		JLMS_require_lang($JLMS_LANGUAGE, 'quiz.lang', $JLMS_CONFIG->get('default_language'));
		//require(_JOOMLMS_FRONT_HOME . "/languages/".$JLMS_CONFIG->get('default_language').'/quiz.lang.php');
		require(_JOOMLMS_FRONT_HOME . '/includes/quiz/quiz_language.php');
		global $jq_language;

		/*$query = "SELECT lang_file FROM #__lms_quiz_languages WHERE is_default = 1 and lang_file <> 'default'";
		$JLMS_DB->SetQuery( $query );
		$req_lang = $JLMS_DB->LoadResult();
		if ($req_lang && file_exists( dirname(__FILE__) . "/language/".$req_lang.".php")) {
			include( dirname(__FILE__) . "/language/".$req_lang.".php");
		}*/
		/*if ( ($quiz->c_language) && ($quiz->c_language != 1) ) {
			$query = "SELECT lang_file FROM #__lms_quiz_languages WHERE id = '".intval($quiz->c_language)."'";
			$JLMS_DB->SetQuery( $query );
			$req_lang = $JLMS_DB->LoadResult();
			if ($req_lang && file_exists( dirname(__FILE__) . "/language/".$req_lang.".php")) {
				include( dirname(__FILE__) . "/language/".$req_lang.".php");
			}
		}*/
		if ($quiz->c_wrong_message) $jq_language['quiz_answer_incorrect'] = htmlspecialchars(nl2br($quiz->c_wrong_message));
		if ($quiz->c_right_message) $jq_language['quiz_answer_correct'] = htmlspecialchars(nl2br($quiz->c_right_message));

		$query = "SELECT c_type from #__lms_quiz_t_question WHERE c_id = '".$quest_id."'";
		$JLMS_DB->SetQuery( $query );
		$qtype = $JLMS_DB->LoadResult();

		// 12.03.2007
		#$query = "SELECT template_name FROM #__lms_quiz_templates WHERE id = '".$quiz->c_skin."'";
		#$JLMS_DB->SetQuery( $query );
		#$cur_tmpl = $JLMS_DB->LoadResult();
		$cur_tmpl = 'joomlaquiz_lms_template';
		if ($cur_tmpl) {
			$is_correct = 1;//first preview - previous question not defined (always correct)
			require_once(dirname(__FILE__) . '/templates/'.$cur_tmpl.'/jq_template.php');
			if (count($q_data) > 0) {

				$toolbar = array();
				$toolbar[] = array('btn_type' => 'quiz_ok', 'btn_js' => "javascript:jq_QuizNextOn();void(0);");
				#if ($quiz->c_slide) {
				#	$toolbar[] = array('btn_type' => 'contents', 'btn_js' => "javascript:jq_ShowPanel();");
				#}

				$ret_str .= "\t" . '<task>quest_preview</task>' . "\n";
				$ret_str .= "\t" . '<quiz_menu><![CDATA['.JLMS_ShowToolbar($toolbar, false).']]></quiz_menu>' . "\n";
				$ret_str .= "\t" . '<quiz_count_quests>'.$q_data_count.'</quiz_count_quests>' . "\n";
				$ret_str .= "\t" . '<quiz_quest_num>X</quiz_quest_num>' . "\n";
				$ret_str .= "\t" . '<quiz_prev_correct>'.$is_correct.'</quiz_prev_correct>' . "\n";
				$msg_html = JoomlaQuiz_template_class::JQ_show_messagebox('', (($is_correct)?(($qtype == 8)?$jq_language['quiz_answer_accepted']:$jq_language['quiz_answer_correct']):$jq_language['quiz_answer_incorrect']), $is_correct);
				$ret_str .= "\t" . '<quiz_message_box><![CDATA['.$msg_html.']]></quiz_message_box>' . "\n";
				$ret_str .= JLMS_quiz_ajax_class::JQ_GetQuestData($q_data[0], $jq_language);
			}
		}
	}
	return $ret_str;
}

function JQ_NextPreview() {
	global $JLMS_DB, $my;
	$ret_str = '';
	$quiz_id = intval( mosGetParam( $_REQUEST, 'quiz', 0 ) );
	$quest_id = intval( mosGetParam( $_REQUEST, 'quest_id', 0 ) );
	$preview_id = '111';//strval( mosGetParam( $_REQUEST, 'preview_id', '' ) );
	$id = intval( mosGetParam( $_REQUEST, 'id', 0 ) );
	$usertype = JLMS_GetUserType($my->id, $id);
	$query = "SELECT * FROM #__lms_quiz_t_quiz WHERE c_id = '".$quiz_id."' AND course_id = '".$id."'";
	$JLMS_DB->SetQuery ($query );
	$quiz = $JLMS_DB->LoadObjectList();
	if (count($quiz)) {
		$quiz = $quiz[0];
	} else {
		$quiz = new stdClass();
		$quiz->c_wrong_message = '';
		$quiz->c_right_message = '';
	}
	//$query = "SELECT c_par_value FROM #__lms_quiz_setup WHERE c_par_name = 'admin_preview'";
	//$JLMS_DB->SetQuery( $query );
	$preview_code = '111';//$JLMS_DB->LoadResult();
	$query = "SELECT c_quiz_id FROM #__lms_quiz_t_question WHERE c_id = '".$quest_id."'";
	$JLMS_DB->SetQuery( $query );
	$q_quiz = $JLMS_DB->LoadResult();
	if ( ($quiz_id == $q_quiz) && ($usertype == 1) && ($preview_id == $preview_code) && ($quest_id)) {
		$answer = strval( isset($_REQUEST['answer']) ? $_REQUEST['answer'] : '' );
		$answer = (get_magic_quotes_gpc()) ? stripslashes( $answer ) : $answer;
		$quest_id_pre = $quest_id;
		$query = "SELECT c_type from #__lms_quiz_t_question WHERE c_id = '".$quest_id."'";
		$JLMS_DB->SetQuery( $query );
		$qtype = $JLMS_DB->LoadResult();
		$query = "SELECT params from #__lms_quiz_t_question WHERE c_id = '".$quest_id."'";
		$JLMS_DB->SetQuery( $query );
		$qparams = $JLMS_DB->LoadResult();
		$quest_params = new JLMSParameters($qparams);
		if ($qtype == 20) {
			$query = "SELECT c_pool from #__lms_quiz_t_question WHERE c_id = '".$quest_id."'";
			$JLMS_DB->SetQuery( $query );
			$qpool = $JLMS_DB->LoadResult();
			if ($qpool) {
				$query = "SELECT a.* FROM #__lms_quiz_t_question as a"
				. "\n WHERE a.c_id = ".$qpool;
				$JLMS_DB->setQuery( $query );
				$pool_quest = $JLMS_DB->loadObject();
				if (is_object($pool_quest)) {
					$qtype = $pool_quest->c_type;
					$quest_id = $pool_quest->c_id;
					$quest_params = new JLMSParameters($pool_quest->params);
				}
			}
		}
		if ($qtype == 21) {
			$query = "SELECT c_pool_gqp from #__lms_quiz_t_question WHERE c_id = '".$quest_id."'";
			$JLMS_DB->SetQuery( $query );
			$qpool_gqp = $JLMS_DB->LoadResult();
			if ($qpool_gqp) {
				$query = "SELECT a.* FROM #__lms_quiz_t_question as a"
				. "\n WHERE a.c_id = ".$qpool_gqp;
				$JLMS_DB->setQuery( $query );
				$pool_quest_gqp = $JLMS_DB->loadObject();
				if (is_object($pool_quest_gqp)) {
					$qtype = $pool_quest_gqp->c_type;
					$quest_id = $pool_quest_gqp->c_id;
					$quest_params = new JLMSParameters($pool_quest_gqp->params);
				}
			}
		}

		$is_correct = 0;
		
		switch ($qtype) {
			case 1:
			case 3:
			case 12:
				$query = "SELECT a.c_point, b.c_id, a.c_attempts FROM #__lms_quiz_t_question as a, #__lms_quiz_t_choice as b WHERE a.c_id = '".$quest_id."' and b.c_question_id = a.c_id and b.c_right = '1'";
				$JLMS_DB->SetQuery( $query );
				$ddd = $JLMS_DB->LoadObjectList();
				if (count($ddd)) { if ($ddd[0]->c_id == $answer) { $is_correct = 1; }}
			break;
			case 2:
			case 13:
				$query = "SELECT a.c_point, a.c_attempts FROM #__lms_quiz_t_question as a WHERE a.c_id = '".$quest_id."'";
				$JLMS_DB->SetQuery( $query );
				$ddd = $JLMS_DB->LoadObjectList();
				$query = "SELECT b.c_id FROM #__lms_quiz_t_question as a, #__lms_quiz_t_choice as b WHERE a.c_id = '".$quest_id."' and b.c_question_id = a.c_id and b.c_right = '1'";
				$JLMS_DB->SetQuery( $query );
				$ddd2 = $JLMS_DB->LoadObjectList();
				$query = "SELECT b.c_id FROM #__lms_quiz_t_question as a, #__lms_quiz_t_choice as b WHERE a.c_id = '".$quest_id."' and b.c_question_id = a.c_id and b.c_right <> '1'";
				$JLMS_DB->SetQuery( $query );
				$ddd3 = $JLMS_DB->LoadObjectList();
				$ans_array = explode(',',$answer);
				if (count($ddd2) && count($ddd)) {
					$is_correct = 1;
					foreach ($ddd2 as $right_row) {
						if (!in_array($right_row->c_id, $ans_array)) { $is_correct = 0; }
					}
					foreach ($ddd3 as $not_right_row) {
						if (in_array($not_right_row->c_id, $ans_array)) { $is_correct = 0; }
					}
				}
			break;
			case 4:
			case 5:
			case 11:
				$query = "SELECT a.c_point, a.c_attempts FROM #__lms_quiz_t_question as a WHERE a.c_id = '".$quest_id."'";
				$JLMS_DB->SetQuery( $query );
				$ddd = $JLMS_DB->LoadObjectList();
				$query = "SELECT b.c_id, b.c_left_text, b.c_right_text FROM #__lms_quiz_t_question as a, #__lms_quiz_t_matching as b WHERE a.c_id = '".$quest_id."' and b.c_question_id = a.c_id ORDER BY b.ordering";
				$JLMS_DB->SetQuery( $query );
				$ddd2 = $JLMS_DB->LoadObjectList();
				$answer = urldecode($answer);
				$ans_array = explode('```',$answer);
				if (count($ddd2) && count($ddd)) {
					$is_correct = 1; $rr_num = 0;
					for ($di = 0, $dn = count($ddd2); $di < $dn; $di ++) {
						$ddd2[$di]->c_right_text_md5 = md5($ddd2[$di]->c_right_text);
					}
					foreach ($ddd2 as $right_row) {
						if ($right_row->c_right_text_md5 != $ans_array[$rr_num]) { $is_correct = 0; }
						$rr_num ++;
					}
				}
			break;
			case 6:
				$query = "SELECT a.c_point, a.c_attempts FROM #__lms_quiz_t_question as a WHERE a.c_id = '".$quest_id."'";
				$JLMS_DB->SetQuery( $query );
				$ddd = $JLMS_DB->LoadObjectList();
				$query = "SELECT c.c_text FROM #__lms_quiz_t_question as a, #__lms_quiz_t_blank as b, #__lms_quiz_t_text as c WHERE a.c_id = '".$quest_id."' and b.c_question_id = a.c_id and c.c_blank_id = b.c_id";
				$JLMS_DB->SetQuery( $query );
				$ddd2 = $JLMS_DB->LoadObjectList();
				$answer = trim(urldecode($answer));
				if (count($ddd2) && count($ddd)) {
					/*foreach ($ddd2 as $right_row) {
						if ($right_row->c_text == $answer) { $is_correct = 1; }
					}*/
					foreach ($ddd2 as $right_row) {
						if($quest_params->get('case_sensivity', 0)){
							if ($right_row->c_text === $answer) {
								$is_correct = 1;
							}
						} else {
							if (strtolower($right_row->c_text) === strtolower($answer)) {
								$is_correct = 1;
							}	
						}	
						if (!$is_correct) {
							/**
							 * 01 November 2007 - DEN - bugfix - checking different character encodings
							 * I.e. if browser sent data in UTF, but DB collation is ISO
							 * or another case, if DB collation is UTF, but browser sent response in ISO.
							 * TODO: code is not tested fully. - need testing with ISO (danish, german), cp and UTF
							 */
							if (function_exists('utf8_encode')) {
								$a_u = utf8_encode($right_row->c_text);
								$b_u = utf8_encode($answer);
							} else {
								$a_u = $right_row->c_text;
								$b_u = $answer;
							}
							if($quest_params->get('case_sensivity', 0)){
								if ($a_u === $answer) {
									$is_correct = 1;
								} else {
									if ($right_row->c_text === $b_u) {
										$is_correct = 1;
									}
								}
							} else {
								if (strtolower($a_u) === strtolower($answer)) {
									$is_correct = 1;
								} else {
									if (strtolower($right_row->c_text) === strtolower($b_u)) {
										$is_correct = 1;
									}
								}
							}
						}
					}
				}
			break;
			case 7:
				$query = "SELECT a.c_point, a.c_attempts, b.c_start_x, b.c_start_y, b.c_width, b.c_height FROM #__lms_quiz_t_question as a, #__lms_quiz_t_hotspot as b WHERE a.c_id = '".$quest_id."' and b.c_question_id = a.c_id";
				$JLMS_DB->SetQuery( $query );
				$ddd = $JLMS_DB->LoadObjectList();
				if (count($ddd)) {
					$ans_array = explode(',',$answer);
					if ((count($ans_array) == 2) && ($ans_array[0] >= $ddd[0]->c_start_x) && ($ans_array[0] <= ($ddd[0]->c_start_x+$ddd[0]->c_width)) && ($ans_array[1] >= $ddd[0]->c_start_y) && ($ans_array[1] <= ($ddd[0]->c_start_y+$ddd[0]->c_height)) ) {
						$is_correct = 1;
					}
				}
			break;
			case 8:
				$query = "SELECT a.c_point, a.c_attempts FROM #__lms_quiz_t_question as a WHERE a.c_id = '".$quest_id."'";
				$JLMS_DB->SetQuery( $query );
				$ddd = $JLMS_DB->LoadObjectList();
				$answer = trim(urldecode($answer));
				if (count($ddd)) { if ($answer) { $is_correct = 1; }}
			break;
			case 9:
				$is_correct = 1;
			break;
			case 10:
				$is_correct = 1;
			break;	
		}
		$quest_id = $quest_id_pre;
		$query = "SELECT * FROM #__lms_quiz_t_question WHERE c_quiz_id = '".$quiz_id."' AND c_id = '".$quest_id."'";
		$JLMS_DB->SetQuery($query);
		$q_data = $JLMS_DB->LoadObjectList();
		$query = "SELECT count(*) FROM #__lms_quiz_t_question WHERE c_quiz_id = '".$quiz_id."'";
		$JLMS_DB->SetQuery($query);
		$q_data_count = $JLMS_DB->LoadResult();

		//require( dirname(__FILE__) . "/language/english.php");
		// preloading QUIZ languge (28.02.2007 new method) (all quizzes messages now in global quiz language)
		global $JLMS_LANGUAGE, $JLMS_CONFIG;
		JLMS_require_lang($JLMS_LANGUAGE, 'quiz.lang', $JLMS_CONFIG->get('default_language'));
		//require(_JOOMLMS_FRONT_HOME . "/languages/".$JLMS_CONFIG->get('default_language').'/quiz.lang.php');
		require(_JOOMLMS_FRONT_HOME . '/includes/quiz/quiz_language.php');
		global $jq_language;

		/*$query = "SELECT lang_file FROM #__lms_quiz_languages WHERE is_default = 1 and lang_file <> 'default'";
		$JLMS_DB->SetQuery( $query );
		$req_lang = $JLMS_DB->LoadResult();
		if ($req_lang && file_exists( dirname(__FILE__) . "/language/".$req_lang.".php")) {
			include( dirname(__FILE__) . "/language/".$req_lang.".php");
		}*/
		/*if ( ($quiz->c_language) && ($quiz->c_language != 1) ) {
			$query = "SELECT lang_file FROM #__lms_quiz_languages WHERE id = '".intval($quiz->c_language)."'";
			$JLMS_DB->SetQuery( $query );
			$req_lang = $JLMS_DB->LoadResult();
			if ($req_lang && file_exists( dirname(__FILE__) . "/language/".$req_lang.".php")) {
				include( dirname(__FILE__) . "/language/".$req_lang.".php");
			}
		}*/
		if ($quiz->c_wrong_message) $jq_language['quiz_answer_incorrect'] = htmlspecialchars(nl2br($quiz->c_wrong_message));
		if ($quiz->c_right_message) $jq_language['quiz_answer_correct'] = htmlspecialchars(nl2br($quiz->c_right_message));

		$query = "SELECT * FROM #__lms_quiz_t_question_fb WHERE quest_id = $quest_id";
		$JLMS_DB->SetQuery($query);
		$q_fbs = $JLMS_DB->LoadObjectList();
		foreach ($q_fbs as $qfb) {
			if ($qfb->choice_id == -1) {
				if ($qfb->fb_text) {
					$jq_language['quiz_answer_incorrect'] = $qfb->fb_text;
				}
			} elseif(!$qfb->choice_id) {
				if ($qfb->fb_text) {
					$jq_language['quiz_answer_correct'] = $qfb->fb_text;
				}
			}
		}
		// 12.03.2007
		#$query = "SELECT template_name FROM #__lms_quiz_templates WHERE id = '".$quiz->c_skin."'";
		#$JLMS_DB->SetQuery( $query );
		#$cur_tmpl = $JLMS_DB->LoadResult();
		$cur_tmpl = 'joomlaquiz_lms_template';
		if ($cur_tmpl) {
			require_once(dirname(__FILE__) . '/templates/'.$cur_tmpl.'/jq_template.php');
			if (count($q_data) > 0) {

				$toolbar = array();
				$toolbar[] = array('btn_type' => 'prev', 'btn_js' => "javascript:JQ_previewQuest();void(0);");
				#if ($quiz->c_slide) {
				#	$toolbar[] = array('btn_type' => 'contents', 'btn_js' => "javascript:jq_ShowPanel();");
				#}

				$ret_str .= "\t" . '<task>preview_finish</task>' . "\n";
				$ret_str .= "\t" . '<quiz_menu><![CDATA['.JLMS_ShowToolbar($toolbar, false).']]></quiz_menu>' . "\n";
				$ret_str .= "\t" . '<quiz_quest_num>X</quiz_quest_num>' . "\n";
				$ret_str .= "\t" . '<quiz_prev_correct>'.$is_correct.'</quiz_prev_correct>' . "\n";
				$msg_html = JoomlaQuiz_template_class::JQ_show_messagebox('', (($is_correct)?(($qtype == 8)?$jq_language['quiz_answer_accepted']:$jq_language['quiz_answer_correct']):$jq_language['quiz_answer_incorrect']), $is_correct);
				$ret_str .= "\t" . '<quiz_message_box><![CDATA['.$msg_html.']]></quiz_message_box>' . "\n";
			}
		}
	}
	return $ret_str;
}

function JQ_GetQuestData($q_data, $jq_language, $stu_quiz_id = 0, $exec_system_script = '') {
	global $JLMS_DB, $my, $option, $JLMS_CONFIG, $Itemid;
	$ret_str = '';

	$cur_template = 'joomlaquiz_lms_template';
	if ($cur_template) {
		require_once(dirname(__FILE__) . '/templates/'.$cur_template.'/jq_template.php');
		$ret_add = '';
		$ret_add_script = '';
		if ($exec_system_script) {
			$ret_add_script = $exec_system_script.'';
		}

		$test_text = $q_data->c_question;
		require_once(dirname(__FILE__) . '/../lms_certificates.php');
		$a = new stdClass();
		$a->quiz_id = $q_data->c_quiz_id;
		$a->stu_quiz_id = $stu_quiz_id;
		$test_text = JLMS_Certificates::ReplaceQuizAnswers($test_text, $a, $my->id, $q_data->course_id);
		$test_text = JLMS_Certificates::ReplaceEventOptions($test_text, $a, $my->id, $q_data->course_id);
		$test_text = str_replace('#date#', date('m-d-Y', time() - date('Z')), $test_text);
		$q_data->c_question = $test_text;
		
		$tmp = JLMS_ShowText_WithFeatures($q_data->c_question, true, true);
		
		if ($q_data->c_type == 7) { //temporary for 'hotspot' support
			$ret_add = '<div>'.$q_data->c_question.'</div><form name=\'quest_form\'><input type=\'hidden\' name=\'hotspot_x\' value=\'0\' /><input type=\'hidden\' name=\'hotspot_y\' value=\'0\' /></form><div id=\'quiz_hs_container_add\'>&nbsp;</div>';
		} else {
			//$q_description = JLMS_ShowText_WithFeatures($q_data->c_question, true);
			$q_description = $tmp['new_text']; //fix bug for AVReloaded (Max - 24.08.2011)
			$ret_add = '<div>'.$q_description.'</div>';
		}
		
		$ret_add_script .= $tmp['js'];

		$ret_str = "\t" . '<quest_data><![CDATA['.$ret_add.']]></quest_data>' . "\n";

		$ret_str .= "\t" . '<quest_type>'.$q_data->c_type.'</quest_type>' . "\n";
		$ret_str .= "\t" . '<quest_id>'.$q_data->c_id.'</quest_id>' . "\n";
		$ret_str .= "\t" . '<quest_score>'.$q_data->c_point.'</quest_score>' . "\n";
		
		if($JLMS_CONFIG->get('quizzes_show_quest_id')) {

			$flag_gqp = 0;
			$flag_pool = 0;
			
			$query = "SELECT a.c_type FROM #__lms_quiz_t_question as a WHERE a.c_id = '".$q_data->c_id."'";
				$JLMS_DB->SetQuery( $query );
				$c_type = $JLMS_DB->LoadResult();
			
			$query = "SELECT a.c_quiz_id FROM #__lms_quiz_t_question as a WHERE a.c_id = '".$q_data->c_id."'";
			$JLMS_DB->SetQuery( $query );
			$c_quiz_id = $JLMS_DB->LoadResult();	
			
			if($c_type == 21) {
				$question_id = $q_data->c_pool_gqp;
				
//				$query = "SELECT a.c_id"
//				. "\n FROM #__lms_quiz_t_question as a, #__lms_gqp_levels as b"
//				. "\n WHERE 1"
//				. "\n AND a.c_id = '".$question_id."'"
//				. "\n AND a.c_quiz_id = 0"
//				. "\n AND b.quest_id = a.c_id"
//				;
				$query = "SELECT a.c_id"
				. "\n FROM #__lms_quiz_t_question as a"
				. "\n WHERE 1"
				. "\n AND a.c_id = '".$question_id."'"
				. "\n AND a.course_id = 0"
				. "\n AND a.c_quiz_id = 0"
				;
				$JLMS_DB->SetQuery( $query );
				$quest_info = $JLMS_DB->LoadObject();
				
							
				if($quest_info->c_id) {
					$ret_str .= "\t" . '<quest_id_gqp>'.$quest_info->c_id.'</quest_id_gqp>' . "\n";	
					$flag_gqp = 1;
				}
			}
			
			elseif($c_type == 20 || $c_quiz_id==0) {
				
				if($c_type != 20) {
					$question_id = $q_data->c_id;					
				}
				else {	
					$question_id = $q_data->c_pool;
				}
				
				$query = "SELECT a.c_id FROM #__lms_quiz_t_question as a WHERE a.c_id = '".$question_id."' AND a.c_quiz_id = 0";
				$JLMS_DB->SetQuery( $query );
				$quest_info = $JLMS_DB->LoadObject();
				
				if($quest_info->c_id && !$flag_gqp) {
				$ret_str .= "\t" . '<quest_id_pool>'.$quest_info->c_id.'</quest_id_pool>' . "\n";	
				$flag_pool = 1;
				}
			}
			
			if(!$flag_gqp) {
				$ret_str .= "\t" . '<quest_id_gqp>0</quest_id_gqp>' . "\n";	
			}
			
			if(!$flag_pool) {
				$ret_str .= "\t" . '<quest_id_pool>0</quest_id_pool>' . "\n";	
			}
			
		}
		
		$qtype = $q_data->c_type;
		$qu_id = $q_data->c_id;
		if ($q_data->c_pool) {
			$qu_id = $q_data->c_pool;
		}
		
		if ($q_data->c_pool_gqp) {
			$qu_id = $q_data->c_pool_gqp;
		}
		
		$quest_params = new JLMSParameters($q_data->params);
		switch ($qtype) {
			case 1:
			case 12:
				if($quest_params->get('mandatory') == 1 && $quest_params->get('survey_question') == 1)
					$is_mandatory = 1;
				else 
					$is_mandatory = 0;
				$sql_var = '';
				$sql_table = '';
				$sql_where = '';
				if($qtype == 12){
					$sql_var .= ', b.imgs_name';
					$sql_table = ', #__lms_quiz_images as b';
					$sql_where .= ' AND a.c_choice = b.imgs_id';	
				}				
				$query = "SELECT a.c_id as value, a.c_choice as text, '0' as c_right, '0' as c_review".$sql_var." FROM #__lms_quiz_t_choice as a".$sql_table." WHERE a.c_question_id = '".$qu_id."'".$sql_where." ORDER BY a.ordering";
				$JLMS_DB->SetQuery( $query );
				$choice_data = $JLMS_DB->LoadObjectList();
				
				/*07.06.2010 - Max - randomize answers*/
				if($qtype == 1 && $quest_params->get('random_answers')){
					shuffle($choice_data);
				}
				/*07.06.2010 - Max - randomize answers*/
				
				$qhtml = JoomlaQuiz_template_class::JQ_createMChoice($choice_data, $qtype);
				$ret_str .= "\t" . '<quest_data_user><![CDATA[<div><form name=\'quest_form\'>'.$qhtml.'<input type="hidden" name="ismandatory" value="'.$is_mandatory.'" /></form></div>]]></quest_data_user>' . "\n";
			break;
			case 3:
				$query = "SELECT c_id as value, c_choice as text, '0' as c_right, '0' as c_review FROM #__lms_quiz_t_choice WHERE c_question_id = '".$qu_id."' ORDER BY ordering";
				$JLMS_DB->SetQuery( $query );
				$choice_data = $JLMS_DB->LoadObjectList();
				$choice_data_new = array();
				if ( ($choice_data[0]->text == 'true') || ($choice_data[0]->text == 'True') ) {
					// ordering is correct
					$choice_data_new = $choice_data;
				} else {
					//change ordering of true/false answers
					$choice_data_new[] = $choice_data[1];
					$choice_data_new[] = $choice_data[0];
				}
				$i = 0;
				while ($i < count($choice_data_new)) {
					if ( ($choice_data_new[$i]->text == 'true') || ($choice_data_new[$i]->text == 'True') ) {
						$choice_data_new[$i]->text = $jq_language['quiz_simple_true'];
					} elseif ( ($choice_data_new[$i]->text == 'false') || ($choice_data_new[$i]->text == 'False') ) {
						$choice_data_new[$i]->text = $jq_language['quiz_simple_false'];
					}
					$i ++;
				}
				$qhtml = JoomlaQuiz_template_class::JQ_createMChoice($choice_data_new, $qtype);
				$ret_str .= "\t" . '<quest_data_user><![CDATA[<div><form name=\'quest_form\'>'.$qhtml.'</form></div>]]></quest_data_user>' . "\n";
			break;
			case 2:
			case 13:
				$sql_var = '';
				$sql_table = '';
				$sql_where = '';
				if($qtype == 13){
					$sql_var .= ', b.imgs_name';
					$sql_table = ', #__lms_quiz_images as b';	
					$sql_where .= ' AND a.c_choice = b.imgs_id';	
				}
				$query = "SELECT a.c_id as value, a.c_choice as text, '0' as c_right, '0' as c_review".$sql_var." FROM #__lms_quiz_t_choice as a".$sql_table." WHERE a.c_question_id = '".$qu_id."'".$sql_where." ORDER BY a.ordering";
				$JLMS_DB->SetQuery( $query );
				$choice_data = $JLMS_DB->LoadObjectList();
				
				/*07.06.2010 - Max - randomize answers*/
				if($qtype == 2 && $quest_params->get('random_answers')){
					shuffle($choice_data);
				}
				/*07.06.2010 - Max - randomize answers*/
				
				$qhtml = JoomlaQuiz_template_class::JQ_createMResponse($choice_data, $qtype);
				$ret_str .= "\t" . '<quest_data_user><![CDATA[<div><form name=\'quest_form\'>'. $qhtml .'</form></div>]]></quest_data_user>' . "\n";
			break;
			case 4:
				$query = "SELECT *, c_right_text as c_val FROM #__lms_quiz_t_matching WHERE c_question_id = '".$qu_id."'"
				. "\n ORDER BY ordering";
				$JLMS_DB->SetQuery( $query );
				$match_data = $JLMS_DB->LoadObjectList();
				$shuffle_match = $match_data;
				shuffle($shuffle_match);
				$ret_str .= "\t" . '<quest_data_user><![CDATA[<div>';
				$ret_add_script = ''
				. 'kol_drag_elems = '.count( $match_data ).';'
				. 'drag_array = new Array(kol_drag_elems);coord_left = new Array(kol_drag_elems);coord_top = new Array(kol_drag_elems);'
				. 'ids_in_cont = new Array(kol_drag_elems);cont_for_ids = new Array(kol_drag_elems);answ_ids = new Array(kol_drag_elems);'
				. 'cont_index = 0;last_drag_id = \'\';last_drag_id_drag = \'\';';
				$ret_str .= ''
				. '<div id="cont" class="d_cont">'
				. '<table width="100%" cellpadding="10" cellspacing="0" border="0" class="jlms_table_no_borders">'
				;
//				. '<div id="jq_drops">';
				for ($i=0, $n = count( $match_data ); $i < $n; $i++ ) {
					
					$ret_str .= '<tr><td width="50%" align="right" style="padding:10px">'
					. '<div id=\'cdiv_'.($i+1).'\' class=\'jq_drop\'>'
					. $match_data[$i]->c_left_text . '</div>'
					. '</td><td width="50%" align="left" style="padding:10px">'
					;
					$ret_str .= '<div id=\'ddiv_'.($i+1).'\' class=\'jq_drag\'>' //onmousedown=\'startDrag()\' onmouseup=\'stopDrag()\'
					. $shuffle_match[$i]->c_right_text . '</div>';
					$ret_str .= '</td></tr>';
				}
//				$ret_str .= '</div>'
//				. '</td><td width="50%" align="left">'
//				. '<div id="jq_drags">';
//				for ($i=0, $n = count( $shuffle_match ); $i < $n; $i++ ) {
//					$ret_str .= '<div id=\'ddiv_'.($i+1).'\' class=\'jq_drag\'>' //onmousedown=\'startDrag()\' onmouseup=\'stopDrag()\'
//					. $shuffle_match[$i]->c_right_text . '</div>';
//				}
				
//				$ret_str .= '</div>'
				$ret_str .= '</table>'
//				. '</td></tr></table>'
				. '<div style="clear:both;"><!-- --></div></div>';
				$ret_add_script .= ''
				. 'setTimeout(\'excute_draggable()\', 500);';
				for ($i=0, $n = count( $shuffle_match ); $i < $n; $i++ ) {
					//$ret_add_script .= 'answ_ids['.($i + 1).'] = \''.$shuffle_match[$i]->c_right_text.'\';';
					//$ret_add_script .= 'answ_ids['.($i + 1).'] = \''.str_replace("'", "\'",$shuffle_match[$i]->c_right_text).'\';';
					$ret_add_script .= 'answ_ids['.($i + 1).'] = \''.md5($shuffle_match[$i]->c_right_text).'\';';
				}
				$ret_add_script .= 'var ijq = 1;for (ijq=1; ijq<=kol_drag_elems; ijq++) { var ddiv_xxx = getObj(\'ddiv_\'+ijq);'
				. '}'; //if (ddiv_xxx) {ddiv_xxx.onmousedown=startDrag; ddiv_xxx.onmouseup=stopDrag;}
				$ret_str .= '<form name=\'quest_form\'></form></div>]]></quest_data_user>' . "\n";
			break;
			case 5:
				$query = "SELECT *, c_right_text as c_val FROM #__lms_quiz_t_matching WHERE c_question_id = '".$qu_id."'"
				. "\n ORDER BY ordering";
				$JLMS_DB->SetQuery( $query );
				$match_data = $JLMS_DB->LoadObjectList();
				$shuffle_match = $match_data;
				shuffle($shuffle_match);
				for ($di = 0, $dn = count($shuffle_match); $di < $dn; $di ++) {
					$shuffle_match[$di]->c_val = md5($shuffle_match[$di]->c_val);
				}
				$shuffle_match1 = array();
				$shuffle_match1[0]->c_right_text = _JLMS_QUIZ_SHOW_SELECT_YOUR_ANSWER;
				$shuffle_match1[0]->c_val = '{0}';
				$shuffle_match1 = array_merge( $shuffle_match1, $shuffle_match );
				$qdata = array();
				for ($i=0, $n = count( $match_data ); $i < $n; $i++ ) {
					$qdata[$i]->c_left_text = $match_data[$i]->c_left_text;
					$qdata[$i]->c_right_text = mosHTML::selectList( $shuffle_match1, 'quest_match', 'class="inputbox" size="1" ', 'c_val', 'c_right_text', null );
				}
				$qhtml = JoomlaQuiz_template_class::JQ_createMDropDown($qdata);
				$ret_str .= "\t" . '<quest_data_user><![CDATA[<div><form name=\'quest_form\'>'. $qhtml .'</form></div>]]></quest_data_user>' . "\n";
			break;
			case 6:
				//$quest_params = new JLMSParameters($q_data->params);
				if($quest_params->get('mandatory') == 1 && $quest_params->get('survey_question') == 1) {
					$is_mandatory = 1;
				} else {
					$is_mandatory = 0;
				}
				$def_cb = '';
				if($quest_params->get('survey_question')) {
					$query = "SELECT c_default FROM #__lms_quiz_t_blank WHERE c_question_id = '".$qu_id."'";
					$JLMS_DB->SetQuery( $query );
					$blank_data = $JLMS_DB->LoadObjectList();
					if(isset($blank_data[0]->c_default) && $blank_data[0]->c_default ) {
						$def_cb = $blank_data[0]->c_default;
					}
				}
				if($def_cb) {	
					$def_cb = str_replace('{','',$def_cb);
					$def_cb = str_replace('}','',$def_cb);
					require(_JOOMLMS_FRONT_HOME . '/includes/classes/lms.cb_join.php');

					$def_cb2 = JLMSCBJoin::getASSOC($def_cb);
					if ($def_cb2) {
						$def_cb = $def_cb2;
					}
					$def_cb = str_replace('#date#', date('m-d-Y', time() - date('Z')), $def_cb);
				}
				$ret_str .= "\t" . '<quest_data_user><![CDATA[<div><form name=\'quest_form\'>' . "\n";
				$qhtml = JoomlaQuiz_template_class::JQ_createBlank('', $def_cb);
				$ret_str .= $qhtml . "\n" . "\t" . '<input type="hidden" name="ismandatory" value="'.$is_mandatory.'" /></form></div>]]></quest_data_user>' . "\n";
			break;
			case 7:
				global $JLMS_CONFIG;
				$is_hotspot_manual_correct = $JLMS_CONFIG->get('quiz_hs_offset_manual_correction');
				$hs_div_offset_class = $JLMS_CONFIG->get('quiz_hs_offset_div_class');
				$hs_manual_offset = $JLMS_CONFIG->get('quiz_hs_ofset_manual_value');
				$ret_str .= "\t" . '<quest_data_user><![CDATA[ ]]></quest_data_user>' . "\n";
				$ret_add_script .= ''
#				. '<form name=\'quest_form\'><div><img id=\'img_hotspot\' style=\'position:relative\' src=\'images/joomlaquiz/images/'.$q_data->c_image.'\' onclick=\'ggg()\'>'
#				. '<div id=\'hs_label_div\' style=\'visibility:hidden; display:none; position:absolute;\'>'
#				. '<img src=\'components/com_joomlaquiz/templates/'.$cur_template.'/images/hs_round.png\' width=\'12\' height=\'12\'></div><input type=\'hidden\' name=\'hotspot_x\' value=\'0\'>'
#				. '<input type=\'hidden\' name=\'hotspot_y\' value=\'0\'></form></div>' ."\n"
#				. '<SCRIPT DEFER language="javascript" type="text/javascript">'
#				. 'alert("sdfsdfsdfd");'
				. 'var quiz_cont_add = getObj(\'quest_div_hs\');'
				. 'var quiz_cont = getObj(\'jq_quiz_container\'); var hs_gg_div1 = document.createElement("div");var hs_gg_div2 = document.createElement("div");'
				. 'var hs_gg_img1 = document.createElement("img");'."\n".'hs_gg_img1.src = \''.$JLMS_CONFIG->get('live_site').'/images/joomlaquiz/images/'.$q_data->c_image.'\';'."\n".'hs_gg_img1.style.position = \'relative\';'
				. 'hs_gg_img1.id = \'img_hotspot\';hs_gg_img1.alt = \'img_hotspot\';'
				. 'hs_gg_div1.appendChild(hs_gg_img1);'
				. 'var hs_gg_img2 = document.createElement("img"); hs_gg_img2.src = \''.$JLMS_CONFIG->get('live_site').'/components/com_joomla_lms/includes/quiz/hs_round.png\';hs_gg_img2.style.width = \'12px\';hs_gg_img2.style.height = \'12px\';'
				. 'hs_gg_div2.id = \'hs_label_div\';hs_gg_div2.style.visibility = \'hidden\';hs_gg_div2.style.display = \'none\';hs_gg_div2.style.position = \'absolute\';'
				. 'hs_gg_div2.appendChild(hs_gg_img2);'
				. 'var hs_gg_form = document.createElement("form"); hs_gg_form.name = \'quest_form\';'
				. 'var hs_gg_input1 = document.createElement(\'input\');hs_gg_input1.setAttribute(\'type\', \'hidden\');hs_gg_input1.setAttribute(\'name\', \'hotspot_x\');hs_gg_input1.setAttribute(\'value\', \'0\');'
				. 'var hs_gg_input2 = document.createElement(\'input\');hs_gg_input2.setAttribute(\'type\', \'hidden\');hs_gg_input2.setAttribute(\'name\', \'hotspot_y\');hs_gg_input2.setAttribute(\'value\', \'0\');'
				. 'hs_gg_form.appendChild(hs_gg_input1);hs_gg_form.appendChild(hs_gg_input2);'
				. 'var quiz_cont_uu = document.createElement("div");'
				. 'quiz_cont_uu.appendChild(hs_gg_div1);quiz_cont_uu.appendChild(hs_gg_div2);'
#				. 'quiz_cont_add.appendChild(quiz_cont_uu);'
#				. 'quiz_cont.appendChild(hs_gg_form);'
				. 'quiz_cont_add.appendChild(quiz_cont_uu);'

				. 'quiz_cont_add.style.textAlign=\'left\';' //quiz_cont_add
				/* DEN: don't change these textAligns - it is for compatibility with IE6 !!!! */
				. 'quiz_cont_add.style.width=\'100%\';'
				
	#			. 'hs_gg_div2.style.textAlign=\'left\';'
				. 'hs_gg_div1.style.textAlign=\'center\';'
	#			. 'quiz_cont_uu.style.textAlign=\'left\';'			
	

#				. 'quiz_cont.appendChild(hs_gg_form);'
# (form moved to top of function)
#				. 'quiz_cont.appendChild(hs_gg_div1);'
#				. 'quiz_cont.appendChild(hs_gg_div2);hs_gg_img1 = undefined;'
#				. 'var quest_div_yy = getObj(\'quest_div\');'
#				. 'quest_div_yy.innerHTML = "&nbsp;";'
				. 'function JQ_img_click_handler(e) {if (!e) { e = window.event;}'
				. 'var targ=e.target?e.target:e.srcElement;'
				. 'var hs_img = getObj(\'img_hotspot\');var hs_label_div = getObj(\'hs_label_div\');'
#				. 'alert(document.body.offsetLeft);'
#				. 'alert("body width = " + document.body.offsetWidth);'
#				. 'alert(targ.scrollLeft || 0);'
#				. 'alert(window.pageXOffset || document.documentElement.scrollLeft || document.body.scrollLeft || 0);'
//				. 'var qqq1 = e.pageY || (e.clientY + (document.documentElement.scrollTop || document.body.scrollTop));'
//				. 'var qqq2 = e.pageX || (e.clientX + (document.documentElement.scrollLeft || document.body.scrollLeft));'
			. 'var qqq1 = e.pageY || (e.clientY + (document.documentElement.scrollTop || document.body.scrollTop));'
			. 'var qqq2 = e.pageX || (e.clientX + (document.documentElement.scrollLeft || document.body.scrollLeft));'
				. 'var valueL2 = 0;'
			. 'var base_y = $(\'quest_div_hs\').getPosition().y;'
			. 'var base_x = $(\'quest_div_hs\').getPosition().x;'
			. 'var img_y = $(\'img_hotspot\').getPosition().y;'
			. 'var img_x = $(\'img_hotspot\').getPosition().x;'
			
			
			//. 'alert(\'Y=\'+qqq1+\' X=\'+qqq2+\' base_Y=\'+base_y+\' base_X=\'+base_x);'
//			. '$(\'test1\').setText(\'X=\'+parseInt(qqq2 )+\' Y=\'+parseInt(qqq1 )+\' base_Y=\'+base_y+\' base_X=\'+base_x+\' img_y=\'+img_y+\' img_x=\'+img_x+\' value_Y=\'+(qqq1 - img_y)+\' value_X=\'+(qqq2 - img_x)+\' \'+ $(\'quest_div_hs\').getCoordinates().left+\' \'+document.documentElement.scrollLeft );'	
				;
#				. 'var valueT = 0; var valueL = 0;var valueL2 = 0; var kol2 = 0; var element1 = hs_img; do {'
#				. 'valueT += element1.scrollTop || 0; valueL += element1.scrollLeft || 0; kol2++; valueL2 += element1.offsetLeft || 0; element1 = element1.parentNode; } while (element1);'
			if ($is_hotspot_manual_correct) {
				$ret_add_script .= 'var valueT2 = 0; var element2 = hs_label_div; do {';
				if ($hs_div_offset_class) {
					$ret_add_script .= 'if (element2.className == "'.$hs_div_offset_class.'") {'
					. 'valueT2 += element2.offsetTop || 0; valueL2 += element2.offsetLeft || 0;} element2 = element2.parentNode; } while (element2);'
					;
				}
				if ($hs_manual_offset) {
					$ret_add_script .= 'valueL2 -= '.$hs_manual_offset.';';
				}
			}
#				. 'alert(valueT + " - " + valueL);alert(valueT2 + " - " + valueL2); alert("Y = " + parseInt(qqq1 - hs_img.offsetTop));'
#				. 'alert("X = " + parseInt(qqq2 - hs_img.offsetLeft));'
				$ret_add_script .= 'hs_label_div.style.top=qqq1 - base_y -6 +\'px\';'
				. 'hs_label_div.style.left=qqq2 - base_x -6 +\'px\';' //-valueL2
				. 'hs_label_div.style.visibility=\'visible\';hs_label_div.style.display=\'block\';hs_label_div.style.position=\'absolute\';'
				. 'document.quest_form.hotspot_x.value = parseInt(qqq2 - img_x);' //- valueL2 - hs_img.offsetLeft
				. 'document.quest_form.hotspot_y.value = parseInt(qqq1 - img_y );}' //- hs_img.offsetTop
				. 'var img = getObj(\'img_hotspot\');img.onclick = null;if (window.addEventListener) { img.addEventListener(\'click\', JQ_img_click_handler, false);}else { img.attachEvent(\'onclick\', JQ_img_click_handler);}'
				;
			break;
			case 8:
				if($quest_params->get('mandatory') == 1)
					$is_mandatory = 1;
				else 
					$is_mandatory = 0;	
				$ret_str .= "\t" . '<quest_data_user><![CDATA[<form name=\'quest_form\'>' . "\n";
				$qhtml = JoomlaQuiz_template_class::JQ_createSurvey();// 'Survey' - question type
				$ret_str .= $qhtml . "\n" . "\t" . '<input type="hidden" name="ismandatory" value="'.$is_mandatory.'" /></form>]]></quest_data_user>' . "\n";
			break;
			case 9:
				if($quest_params->get('mandatory') == 1)
					$is_mandatory = 1;
				else 
					$is_mandatory = 0;
				$query = "SELECT * FROM #__lms_quiz_t_scale WHERE c_question_id = '".$qu_id."'"
				. "\n ORDER BY ordering";
				$JLMS_DB->SetQuery( $query );
				$scale_data = $JLMS_DB->LoadObjectList();
				$qhtml = JoomlaQuiz_template_class::JQ_createScale($scale_data,0);
				$ret_str .= "\t" . '<quest_data_user><![CDATA[<div><form name=\'quest_form\'>'. $qhtml .'<input type="hidden" name="ismandatory" value="'.$is_mandatory.'" /></form></div>]]></quest_data_user>' . "\n";
			break;
			case 10:
				$ret_str .= "\t" . '<quest_data_user><![CDATA[<form name=\'quest_form\'>' . "\n";
				$ret_str .= "\n" . "\t" . '</form>]]></quest_data_user>' . "\n";
			break;
			case 11:
				$query = "SELECT a.*, a.c_right_text as c_val, b.imgs_name as left_name, c.imgs_name as right_name FROM #__lms_quiz_t_matching as a, #__lms_quiz_images as b, #__lms_quiz_images as c WHERE c_question_id = '".$qu_id."' AND a.c_left_text = b.imgs_id AND a.c_right_text = c.imgs_id"
				. "\n ORDER BY a.ordering";
				$JLMS_DB->SetQuery( $query );
				$match_data = $JLMS_DB->LoadObjectList();
				$shuffle_match = $match_data;
				shuffle($shuffle_match);
				$ret_str .= "\t" . '<quest_data_user><![CDATA[<div>';
				$ret_add_script .= ''
				. 'kol_drag_elems = '.count( $match_data ).';'
				. 'drag_array = new Array(kol_drag_elems);coord_left = new Array(kol_drag_elems);coord_top = new Array(kol_drag_elems);'
				. 'ids_in_cont = new Array(kol_drag_elems);cont_for_ids = new Array(kol_drag_elems);answ_ids = new Array(kol_drag_elems);'
				. 'cont_index = 0;last_drag_id = \'\';last_drag_id_drag = \'\';';
				$ret_str .= ''
				. '<div id="cont" class="d_cont">'
				. '<table width="100%" cellpadding="10" cellspacing="0" border="0" class="jlms_table_no_borders">'
//				. '<div id="jq_drops">'
				;
				for ($i=0, $n = count( $match_data ); $i < $n; $i++ ) {
					$link = 'index.php?tmpl=component&option='.$option.'&task=quizzes&page=imgs_v&file_id='.$match_data[$i]->c_left_text.'&imgs_name='.$match_data[$i]->left_name;
					$ret_str .= '<tr><td width="50%" align="right" style="padding:10px">';
					$ret_str .= '<div id=\'cdiv_'.($i+1).'\' class=\'jq_drop\' style=\'width: '.$JLMS_CONFIG->get('quiz_match_max_width', 250).'px; height: '.$JLMS_CONFIG->get('quiz_match_max_height', 30).'px;\'>
					<img src="'.ampReplace($JLMS_CONFIG->get('live_site').'/index.php?tmpl=component&option='.$option.'&task=quizzes&page=imgs_v&id='.$q_data->course_id.'&file_id='.$match_data[$i]->c_left_text.'&Itemid='.$Itemid.'&imgs_name='.$match_data[$i]->left_name.'&pic_width='.$JLMS_CONFIG->get('quiz_match_max_width', 250).'&pic_height='.$JLMS_CONFIG->get('quiz_match_max_height', 250).'&bg_color=dddddd').'" border="0" alt="'.$match_data[$i]->left_name.'"/>
					</div>'
					. '</td>'
					. '<td width="50%" align="left" style="padding:10px">'
					;
					$ret_str .= '<div id=\'ddiv_'.($i+1).'\' class=\'jq_drag\' style=\'width: '.$JLMS_CONFIG->get('quiz_match_max_width', 250).'px; height: '.$JLMS_CONFIG->get('quiz_match_max_height', 30).'px;\'>
					<img src="'.ampReplace($JLMS_CONFIG->get('live_site').'/index.php?tmpl=component&option='.$option.'&task=quizzes&page=imgs_v&id='.$q_data->course_id.'&file_id='.$shuffle_match[$i]->c_right_text.'&Itemid='.$Itemid.'&imgs_name='.$shuffle_match[$i]->right_name.'&pic_width='.$JLMS_CONFIG->get('quiz_match_max_width', 250).'&pic_height='.$JLMS_CONFIG->get('quiz_match_max_height', 250)).'" border="0" alt="'.$shuffle_match[$i]->right_name.'"/>
					</div>'
					. '</td></tr>'
					;
				}
				
				$ret_str .= '</table>'
				. '<div style="clear:both;"><!-- --></div></div>';
				$ret_add_script .= 'setTimeout(\'excute_draggable()\', 500);';
				for ($i=0, $n = count( $shuffle_match ); $i < $n; $i++ ) {
					$ret_add_script .= 'answ_ids['.($i + 1).'] = \''.md5($shuffle_match[$i]->c_right_text).'\';';
				}
				$ret_add_script .= 'var ijq = 1;for (ijq=1; ijq<=kol_drag_elems; ijq++) { var ddiv_xxx = getObj(\'ddiv_\'+ijq);'
				. '}'; //if (ddiv_xxx) {ddiv_xxx.onmousedown=startDrag; ddiv_xxx.onmouseup=stopDrag;}
				$ret_str .= '<form name=\'quest_form\'></form></div>]]></quest_data_user>' . "\n";
			break;
		}
		if ($ret_add_script) {
				$ret_str .= "\t" . '<exec_quiz_script>1</exec_quiz_script>' . "\n";
				$ret_str .= "\t" . '<quiz_script_data><![CDATA['.$ret_add_script.']]></quiz_script_data>' . "\n";
		} else {
				$ret_str .= "\t" . '<exec_quiz_script>0</exec_quiz_script>' . "\n";
				$ret_str .= "\t" . '<quiz_script_data><![CDATA[ ]]></quiz_script_data>' . "\n";
		}
	}
	return $ret_str;
}

function JQ_GetQuestData_nojs($q_data, $jq_language, $stu_quiz_id = 0) {
	global $JLMS_DB, $my, $option, $JLMS_CONFIG;
	$ret_str = '';
	
	$cur_template = 'joomlaquiz_lms_template';
	if ($cur_template) {
		require_once(dirname(__FILE__) . '/templates/'.$cur_template.'/jq_template.php');
		$ret_add = '';

		$test_text = $q_data->c_question;
		require_once(dirname(__FILE__) . '/../lms_certificates.php');
		$a = new stdClass();
		$a->quiz_id = $q_data->c_quiz_id;
		$a->stu_quiz_id = $stu_quiz_id;
		$test_text = JLMS_Certificates::ReplaceQuizAnswers($test_text, $a, $my->id, $q_data->course_id);
		$test_text = JLMS_Certificates::ReplaceEventOptions($test_text, $a, $my->id, $q_data->course_id);
		$test_text = str_replace('#date#', date('m-d-Y', time() - date('Z')), $test_text);
		$q_data->c_question = $test_text;

		if ($q_data->c_type == 7) { //temporary for 'hotspot' support
			$ret_add = '<div style="text-align:center;">'.$q_data->c_question.'</div>';
		} else {
			$q_description = JLMS_ShowText_WithFeatures($q_data->c_question, true);
			$ret_add = '<div style="text-align:center;">'.$q_description.'</div>';
		}

//		$ret_str = "\t" . '<quest_data>'.$ret_add.'</quest_data>' . "\n";
		$ret_str = "\t" . $ret_add . "\n";

		$ret_str .= "\t" . '<input type="hidden" name="quest_type" value="'.$q_data->c_type.'"/>' . "\n";
		$ret_str .= "\t" . '<input type="hidden" name="quest_id" value="'.$q_data->c_id.'"/>' . "\n";
		$ret_str .= "\t" . '<input type="hidden" name="quest_score" value="'.$q_data->c_point.'"/>' . "\n";
		$qtype = $q_data->c_type;
		$qu_id = $q_data->c_id;
		if ($q_data->c_pool) {
			$qu_id = $q_data->c_pool;
		}
		$quest_params = new JLMSParameters($q_data->params);
		switch ($qtype) {
			case 1:
			case 12:
				if($quest_params->get('mandatory') == 1 && $quest_params->get('survey_question') == 1)
					$is_mandatory = 1;
				else 
					$is_mandatory = 0;
				$sql_var = '';
				$sql_table = '';
				$sql_where = '';
				if($qtype == 12){
					$sql_var .= ', b.imgs_name';
					$sql_table = ', #__lms_quiz_images as b';
					$sql_where .= ' AND a.c_choice = b.imgs_id';	
				}				
				$query = "SELECT a.c_id as value, a.c_choice as text, '0' as c_right, '0' as c_review".$sql_var." FROM #__lms_quiz_t_choice as a".$sql_table." WHERE a.c_question_id = '".$qu_id."'".$sql_where." ORDER BY a.ordering";
				$JLMS_DB->SetQuery( $query );
				$choice_data = $JLMS_DB->LoadObjectList();
				
				$qhtml = JoomlaQuiz_template_class::JQ_createMChoice($choice_data, $qtype);
//				$ret_str .= "\t" . '<quest_data_user><div>'.$qhtml.'<input type="hidden" name="ismandatory" value="'.$is_mandatory.'" /></div></quest_data_user>' . "\n";
				$ret_str .= "\t" . '<div>'.$qhtml.'<input type="hidden" name="ismandatory" value="'.$is_mandatory.'" /></div>' . "\n";
			break;
			case 3:
				$query = "SELECT c_id as value, c_choice as text, '0' as c_right, '0' as c_review FROM #__lms_quiz_t_choice WHERE c_question_id = '".$qu_id."' ORDER BY ordering";
				$JLMS_DB->SetQuery( $query );
				$choice_data = $JLMS_DB->LoadObjectList();
				$i = 0;
				while ($i < count($choice_data)) {
					if ( ($choice_data[$i]->text == 'true') || ($choice_data[$i]->text == 'True') ) {
						$choice_data[$i]->text = $jq_language['quiz_simple_true'];
					} elseif ( ($choice_data[$i]->text == 'false') || ($choice_data[$i]->text == 'False') ) {
						$choice_data[$i]->text = $jq_language['quiz_simple_false'];
					}
					$i ++;
				}
				$qhtml = JoomlaQuiz_template_class::JQ_createMChoice($choice_data, $qtype);
				$ret_str .= "\t" . '<div style="text-align:center;">'.$qhtml.'</div>' . "\n";
			break;
			case 2:
			case 13:
				$sql_var = '';
				$sql_table = '';
				$sql_where = '';
				if($qtype == 13){
					$sql_var .= ', b.imgs_name';
					$sql_table = ', #__lms_quiz_images as b';	
					$sql_where .= ' AND a.c_choice = b.imgs_id';	
				}
				$query = "SELECT a.c_id as value, a.c_choice as text, '0' as c_right, '0' as c_review".$sql_var." FROM #__lms_quiz_t_choice as a".$sql_table." WHERE a.c_question_id = '".$qu_id."'".$sql_where." ORDER BY a.ordering";
				$JLMS_DB->SetQuery( $query );
				$choice_data = $JLMS_DB->LoadObjectList();
				
				$qhtml = JoomlaQuiz_template_class::JQ_createMResponse($choice_data, $qtype, 1);
				$ret_str .= "\t" . '<div>'. $qhtml .'</div>' . "\n";
			break;
			case 4:
				$query = "SELECT *, c_right_text as c_val FROM #__lms_quiz_t_matching WHERE c_question_id = '".$qu_id."'"
				. "\n ORDER BY ordering";
				$JLMS_DB->SetQuery( $query );
				$match_data = $JLMS_DB->LoadObjectList();
				$shuffle_match = $match_data;
				shuffle($shuffle_match);
				for ($di = 0, $dn = count($shuffle_match); $di < $dn; $di ++) {
					$shuffle_match[$di]->c_val = md5($shuffle_match[$di]->c_val);
				}
//				$ret_str .= "\t" . '<div>'
//				. '<div id="cont" class="d_cont"><div id="col_1" style="float:left; width:49%;">';
//				for ($i=0, $n = count( $match_data ); $i < $n; $i++ ) {
//					$ret_str .= '<div id=\'cdiv_'.($i+1).'\' class=\'jq_drop\'>'
//					. $match_data[$i]->c_left_text . '</div>';
//				}
//				$ret_str .= '</div><div id="col_2" style="float:left; width:49%;">';
//				for ($i=0, $n = count( $shuffle_match ); $i < $n; $i++ ) {
//					$ret_str .= '<div id=\'ddiv_'.($i+1).'\' class=\'jq_drag\'>' //onmousedown=\'startDrag()\' onmouseup=\'stopDrag()\'
//					. $shuffle_match[$i]->c_right_text . '</div>';
//				}
//				$ret_str .= '</div><div style="clear:both;"><!-- --></div></div>';
//				$ret_str .= '</div>' . "\n";
				
				
				$shuffle_match1 = array();
				$shuffle_match1[0]->c_right_text = _JLMS_QUIZ_SHOW_SELECT_YOUR_ANSWER;
				$shuffle_match1[0]->c_val = '{0}';
				$shuffle_match1 = array_merge( $shuffle_match1, $shuffle_match );
				$qdata = array();
				for ($i=0, $n = count( $match_data ); $i < $n; $i++ ) {
					$qdata[$i]->c_left_text = '<div class=\'jq_drop\'>'.$match_data[$i]->c_left_text.'</div>';
					$qdata[$i]->c_right_text = mosHTML::selectList( $shuffle_match1, 'quest_match['.$i.']', 'class="inputbox" size="1" ', 'c_val', 'c_right_text', null );
				}
				$qhtml = JoomlaQuiz_template_class::JQ_createMDropDown($qdata);
				$ret_str .= "\t" . '<div>'. $qhtml .'</div>' . "\n";
				
			break;
			case 5:
				$query = "SELECT *, c_right_text as c_val FROM #__lms_quiz_t_matching WHERE c_question_id = '".$qu_id."'"
				. "\n ORDER BY ordering";
				$JLMS_DB->SetQuery( $query );
				$match_data = $JLMS_DB->LoadObjectList();
				$shuffle_match = $match_data;
				shuffle($shuffle_match);
				for ($di = 0, $dn = count($shuffle_match); $di < $dn; $di ++) {
					$shuffle_match[$di]->c_val = md5($shuffle_match[$di]->c_val);
				}
				$shuffle_match1 = array();
				$shuffle_match1[0]->c_right_text = _JLMS_QUIZ_SHOW_SELECT_YOUR_ANSWER;
				$shuffle_match1[0]->c_val = '{0}';
				$shuffle_match1 = array_merge( $shuffle_match1, $shuffle_match );
				$qdata = array();
				for ($i=0, $n = count( $match_data ); $i < $n; $i++ ) {
					$qdata[$i]->c_left_text = $match_data[$i]->c_left_text;
					$qdata[$i]->c_right_text = mosHTML::selectList( $shuffle_match1, 'quest_match['.$i.']', 'class="inputbox" size="1" ', 'c_val', 'c_right_text', null );
				}
				$qhtml = JoomlaQuiz_template_class::JQ_createMDropDown($qdata);
				$ret_str .= "\t" . '<div>'. $qhtml .'</div>' . "\n";
			break;
			case 6:
				//$quest_params = new JLMSParameters($q_data->params);
				if($quest_params->get('mandatory') == 1 && $quest_params->get('survey_question') == 1)
					$is_mandatory = 1;
				else 
					$is_mandatory = 0;
					
				$def_cb = '';
				
				$def_cb = '';
				if($quest_params->get('survey_question')) {
					$query = "SELECT c_default FROM #__lms_quiz_t_blank WHERE c_question_id = '".$qu_id."'";
					$JLMS_DB->SetQuery( $query );
					$blank_data = $JLMS_DB->LoadObjectList();
					if(isset($blank_data[0]->c_default) && $blank_data[0]->c_default ) {
						$def_cb = $blank_data[0]->c_default;
					}
				}
				if($def_cb) {	
					$def_cb = str_replace('{','',$def_cb);
					$def_cb = str_replace('}','',$def_cb);
					require(_JOOMLMS_FRONT_HOME . '/includes/classes/lms.cb_join.php');

					$def_cb2 = JLMSCBJoin::getASSOC($def_cb);
					if ($def_cb2) {
						$def_cb = $def_cb2;
					}
					$def_cb = str_replace('#date#', date('m-d-Y', time() - date('Z')), $def_cb);
				}
				$qhtml = JoomlaQuiz_template_class::JQ_createBlank('', $def_cb);
				$ret_str .= '<div style="text-align:center;">'.$qhtml .'</div>'. "\n" . "\t" . '<input type="hidden" name="ismandatory" value="'.$is_mandatory.'" />' . "\n";
			break;
			case 7:
				global $JLMS_CONFIG;
				$is_hotspot_manual_correct = $JLMS_CONFIG->get('quiz_hs_offset_manual_correction');
				$hs_div_offset_class = $JLMS_CONFIG->get('quiz_hs_offset_div_class');
				$hs_manual_offset = $JLMS_CONFIG->get('quiz_hs_ofset_manual_value');
				
				$src = 'images/joomlaquiz/images/'.$q_data->c_image.'';
				$qhtml = '<input type="image" name="hotspot" value="hs" src="'.$src.'"/>';
				$ret_str .= "\t" . '<div style="text-align:center;">'. $qhtml .'</div>' . "\n";
			break;
			case 8:
				if($quest_params->get('mandatory') == 1)
					$is_mandatory = 1;
				else 
					$is_mandatory = 0;	
				$qhtml = JoomlaQuiz_template_class::JQ_createSurvey();// 'Survey' - question type
				$ret_str .= '<div style="text-align:center;">' . $qhtml . "</div>\n" . "\t" . '<input type="hidden" name="ismandatory" value="'.$is_mandatory.'" />' . "\n";
			break;
			case 9:
				if($quest_params->get('mandatory') == 1)
					$is_mandatory = 1;
				else 
					$is_mandatory = 0;
				$query = "SELECT * FROM #__lms_quiz_t_scale WHERE c_question_id = '".$qu_id."'"
				. "\n ORDER BY ordering";
				$JLMS_DB->SetQuery( $query );
				$scale_data = $JLMS_DB->LoadObjectList();
				$qhtml = JoomlaQuiz_template_class::JQ_createScale($scale_data,0);
				$ret_str .= "\t" . '<div>'. $qhtml .'<input type="hidden" name="ismandatory" value="'.$is_mandatory.'" /></div>' . "\n";
			break;
			case 10:
				$ret_str .= "\n" . "\t" . '' . "\n";
			break;
			case 11:
				$query = "SELECT a.*, a.c_right_text as c_val, b.imgs_name as left_name, c.imgs_name as right_name FROM #__lms_quiz_t_matching as a, #__lms_quiz_images as b, #__lms_quiz_images as c WHERE c_question_id = '".$qu_id."' AND a.c_left_text = b.imgs_id AND a.c_right_text = c.imgs_id"
				. "\n ORDER BY a.ordering";
				$JLMS_DB->SetQuery( $query );
				$match_data = $JLMS_DB->LoadObjectList();
				
				$shuffle_match = $match_data;
				shuffle($shuffle_match);
				for ($di = 0, $dn = count($shuffle_match); $di < $dn; $di ++) {
					$shuffle_match[$di]->c_val = md5($shuffle_match[$di]->c_val);
				}
				
				$shuffle_match1 = array();
				$shuffle_match1[0]->right_name = _JLMS_QUIZ_SHOW_SELECT_YOUR_ANSWER;
				$shuffle_match1[0]->c_val = '{0}';
				$shuffle_match1 = array_merge( $shuffle_match1, $shuffle_match );
				$qdata = array();
				for ($i=0, $n = count( $match_data ); $i < $n; $i++ ) {
					$link = 'index.php?tmpl=component&option='.$option.'&task=quizzes&page=imgs_v&file_id='.$match_data[$i]->c_left_text.'&imgs_name='.$match_data[$i]->left_name;
					$qdata[$i]->c_left_text = '<div class=\'jq_drop\' style=\'width: '.$JLMS_CONFIG->get('quiz_match_max_width', 250).'px; height: '.$JLMS_CONFIG->get('quiz_match_max_height', 30).'px;\'>
					<img src="'.ampReplace($JLMS_CONFIG->get('live_site').'/index.php?tmpl=component&option='.$option.'&task=quizzes&page=imgs_v&id='.$q_data->course_id.'&file_id='.$match_data[$i]->c_left_text.'&imgs_name='.$match_data[$i]->left_name.'&pic_width='.$JLMS_CONFIG->get('quiz_match_max_width', 250).'&pic_height='.$JLMS_CONFIG->get('quiz_match_max_height', 250).'&bg_color=dddddd" border="0" alt="'.$match_data[$i]->left_name).'" title="'.$match_data[$i]->left_name.'"/>
					</div>';
					$qdata[$i]->c_right_text = mosHTML::selectList( $shuffle_match1, 'quest_match['.$i.']', 'class="inputbox" size="1" ', 'c_val', 'right_name', null );
				}
				$qhtml = JoomlaQuiz_template_class::JQ_createMDropDown($qdata);
				$ret_str .= "\t" . '<div>'. $qhtml .'</div>' . "\n";
				
			break;
		}
	}
	return $ret_str;
}

function JQ_GetQuestData_review($q_data, $jq_language, $user_answer, $show_correct, $show_correct_answer, $q_survey) {
	global $JLMS_DB, $JLMS_CONFIG, $option, $Itemid;
	$ret_str = '';
	$ret_add_script = '';

	$cur_template = 'joomlaquiz_lms_template';
	if ($cur_template) {
		require_once(dirname(__FILE__) . '/templates/'.$cur_template.'/jq_template.php');
		
		$tmp = JLMS_ShowText_WithFeatures($q_data->c_question, true, true);
		$ret_add_script .= $tmp['js'];
		$q_description = JLMS_ShowText_WithFeatures($q_data->c_question, true);
		
		$ret_str = "\t" . '<quest_data><![CDATA[<div>'.$q_description.'</div>]]></quest_data>' . "\n";
		$ret_str .= "\t" . '<quest_type>'.$q_data->c_type.'</quest_type>' . "\n";
		$ret_str .= "\t" . '<quest_id>'.$q_data->old_c_id.'</quest_id>' . "\n";  //$q_data->c_id
		$ret_str .= "\t" . '<prev_quest_id>'.(isset($q_data->prev_c_id) && $q_data->prev_c_id ? $q_data->prev_c_id : 0).'</prev_quest_id>' . "\n";  //$q_data->c_id
		$ret_str .= "\t" . '<quest_score>'.$q_data->c_point.'</quest_score>' . "\n";
		switch ($q_data->c_type) {
			case 1:
			case 12:
				$sql_var = '';
				$sql_table = '';
				$sql_where = '';
				if($q_data->c_type == 12){
					$sql_var .= ', b.imgs_name';
					$sql_table = ', #__lms_quiz_images as b';
					$sql_where .= ' AND a.c_choice = b.imgs_id';	
				}
				$query = "SELECT a.c_id as value, a.c_choice as text, a.c_right, '1' as c_review".$sql_var." FROM #__lms_quiz_t_choice as a".$sql_table." WHERE a.c_question_id = '".$q_data->c_id."'".$sql_where." ORDER BY a.ordering";
				$JLMS_DB->SetQuery( $query );
				$choice_data = $JLMS_DB->LoadObjectList();
				$qhtml = '';
				if($show_correct_answer){
					$qhtml = JoomlaQuiz_template_class::JQ_createMChoice($choice_data, $q_data->c_type);
				}
				$quhtml = '';
				if($show_correct || $q_survey)
				{
					for ($i=0;$i<count($choice_data);$i++)
					{
						if($choice_data[$i]->value == $user_answer){
							$choice_data[$i]->c_right = 1;
						} else {
							$choice_data[$i]->c_right = 0;	
						}
					}
					
					$quhtml .= '<div style="width:100%;"><div class="contentheading">'._JLMS_QUIZ_REVIEW_YOUR_ANSWER.'</div><table width="100%" align="left" cellpadding="0" cellspacing="0" class="jlms_table_no_borders"><tr><td>';
					$quhtml .= JoomlaQuiz_template_class::JQ_createMChoice($choice_data, $q_data->c_type);
					$quhtml .= '</td></tr></table><div style="clear: both;"><!-- --></div></div>';
				}
				if($q_survey) $qhtml = '';
				$ret_str .= "\t" . '<quest_data_user><![CDATA[<div style=\'width:100%;\'><table width="100%" align="left" cellpadding="0" cellspacing="0" class="jlms_table_no_borders"><tr><td><form name=\'quest_form\'>'.$qhtml.'</form></td></tr></table><div style="clear: both;"><!-- --></div></div>'.$quhtml.']]></quest_data_user>' . "\n";
			break;
			case 3:
				$query = "SELECT c_id as value, c_choice as text, c_right, '1' as c_review FROM #__lms_quiz_t_choice WHERE c_question_id = '".$q_data->c_id."' ORDER BY ordering";
				$JLMS_DB->SetQuery( $query );
				$choice_data = $JLMS_DB->LoadObjectList();
				
				$i = 0;
				while ($i < count($choice_data)) {
					if ( ($choice_data[$i]->text == 'true') || ($choice_data[$i]->text == 'True') ) {
						$choice_data[$i]->text = $jq_language['quiz_simple_true'];
					} elseif ( ($choice_data[$i]->text == 'false') || ($choice_data[$i]->text == 'False') ) {
						$choice_data[$i]->text = $jq_language['quiz_simple_false'];
					}
					$i ++;
				}
				
				$qhtml = '';
				if($show_correct_answer){	
					$qhtml = JoomlaQuiz_template_class::JQ_createMChoice($choice_data, $q_data->c_type);
				}
				$quhtml = '';
				if($show_correct)
				{
					for ($i=0;$i<count($choice_data);$i++)
					{
						if($choice_data[$i]->value == $user_answer){
							$choice_data[$i]->c_right = 1;
						} else {
							$choice_data[$i]->c_right = 0;
						}
					}
					$quhtml .= '<div style="width:100%;"><div class="contentheading">'._JLMS_QUIZ_REVIEW_YOUR_ANSWER.'</div><table align="left" width="100%" cellpadding="0" cellspacing="0" class="jlms_table_no_borders"><tr><td>';
					$quhtml .= JoomlaQuiz_template_class::JQ_createMChoice($choice_data, $q_data->c_type);
					$quhtml .= '</td></tr></table><div style="clear: both;"><!-- --></div></div>';
				}
				$ret_str .= "\t" . '<quest_data_user><![CDATA[<div style=\'width:100%;\'><table width="100%" align="left" cellpadding="0" cellspacing="0" class="jlms_table_no_borders"><tr><td><form name=\'quest_form\'>'.$qhtml.'</form></td></tr></table></div>'.$quhtml.']]></quest_data_user>' . "\n";
			break;
			case 2:
			case 13:
				$sql_var = '';
				$sql_table = '';
				$sql_where = '';
				if($q_data->c_type == 13){
					$sql_var .= ', b.imgs_name';
					$sql_table = ', #__lms_quiz_images as b';
					$sql_where .= ' AND a.c_choice = b.imgs_id';	
				}
				
				$query = "SELECT a.c_id as value, a.c_choice as text, a.c_right, '1' as c_review".$sql_var." FROM #__lms_quiz_t_choice as a".$sql_table." WHERE a.c_question_id = '".$q_data->c_id."'".$sql_where." ORDER BY a.ordering";
				$JLMS_DB->SetQuery( $query );
				$choice_data = $JLMS_DB->LoadObjectList();
				
				$qhtml = '';
				if($show_correct_answer){
					$qhtml = JoomlaQuiz_template_class::JQ_createMResponse($choice_data, $q_data->c_type);
				}
				if($show_correct || $q_survey)
				{
					for ($i=0;$i<count($choice_data);$i++)
					{
						if(in_array($choice_data[$i]->value, $user_answer))
							$choice_data[$i]->c_right = 1;
						else
							$choice_data[$i]->c_right = 0;	
					}
					$quhtml = '';
					$quhtml .= '<div style="width:100%;"><div class="contentheading">'._JLMS_QUIZ_REVIEW_YOUR_ANSWER.'</div><table width="100%" align="left" cellpadding="0" cellspacing="0" class="jlms_table_no_borders"><tr><td>';
					$quhtml .= JoomlaQuiz_template_class::JQ_createMResponse($choice_data, $q_data->c_type);
					$quhtml .= '</td></tr></table></div>';
				}
				if($q_survey){ $qhtml = ''; }
				$ret_str .= "\t" . '<quest_data_user><![CDATA[<div style=\'width:100%;\'><table width="100%" align="left" cellpadding="0" cellspacing="0" class="jlms_table_no_borders"><tr><td><form name=\'quest_form\'>'.$qhtml.'</form></td></tr></table></div>'.$quhtml.']]></quest_data_user>' . "\n";
			break;
			case 4:
				$query = "SELECT *, c_right_text as c_val FROM #__lms_quiz_t_matching WHERE c_question_id = '".$q_data->c_id."'"
				. "\n ORDER BY ordering";
				$JLMS_DB->SetQuery( $query );
				$match_data = $JLMS_DB->LoadObjectList();
				$shuffle_match = $match_data;
				$ret_str .= "\t" . '<quest_data_user><![CDATA[<div>' . "\n";
				if($show_correct_answer){
					$ret_str .= '<div style=\'width:100%;text-align:center\'><table id=\'quest_table\' align=\'center\' cellpadding="10" cellspacing="0" border="0" class="jlms_table_no_borders">';
					for ($i=0, $n = count( $match_data ); $i < $n; $i++ ) {
						$ret_str .= '<tr><td style="padding:10px"><div id=\'cdiv_'.($i+1).'\' class=\'jq_drop_pre\' align="center">'
						. $match_data[$i]->c_left_text . '</div></td><td style="padding:10px"><div id=\'ddiv_'.($i+1).'\' class=\'jq_drag_pre\'>'
						. $shuffle_match[$i]->c_right_text . '</div></td></tr>';
					}
					$ret_str .= '</table></div>' . "\n";
				}
				if($show_correct)
				{
					$ret_str .= '<div style=\'width:100%;text-align:center\'><div class="contentheading">'._JLMS_QUIZ_REVIEW_YOUR_ANSWER.'</div><table id=\'quest_table2\' align="center" cellpadding="10" cellspacing="0" border="0" class="jlms_table_no_borders">';
					for ($i=0, $n = count( $match_data ); $i < $n; $i++ ) {
						$ret_str .= '<tr><td style="padding:10px"><div id=\'cdiv_'.($i+1).'\' class=\'jq_drop_pre\'>'
						. $match_data[$i]->c_left_text . '</div></td><td style="padding:10px"><div id=\'ddiv_'.($i+1).'\' class=\'jq_drag_pre\'>'
						. $user_answer[$i] . '</div></td></tr>';
					}
					$ret_str .= '</table></div>' . "\n";
				}
				$ret_str .= '<form name=\'quest_form\'></form></div>]]></quest_data_user>' . "\n";
			break;
			case 5:
				$query = "SELECT *, c_right_text as c_val FROM #__lms_quiz_t_matching WHERE c_question_id = '".$q_data->c_id."'"
				. "\n ORDER BY ordering";
				$JLMS_DB->SetQuery( $query );
				$match_data = $JLMS_DB->LoadObjectList();
				$shuffle_match = $match_data;
				$shuffle_match1 = array();
				$shuffle_match1[0]->c_right_text = _JLMS_QUIZ_SHOW_SELECT_YOUR_ANSWER;
				$shuffle_match1[0]->c_val = '{0}';
				$shuffle_match1 = array_merge( $shuffle_match1, $shuffle_match );
				$qdata = array();
				for ($i=0, $n = count( $match_data ); $i < $n; $i++ ) {
					$qdata[$i]->c_left_text = $match_data[$i]->c_left_text;
					$qdata[$i]->c_right_text = mosHTML::selectList( $shuffle_match1, 'quest_match', 'class="inputbox" size="1" disabled', 'c_val', 'c_right_text', $shuffle_match1[$i+1]->c_right_text );
				}
				
				$qhtml = '';
				if($show_correct_answer){
					$qhtml = JoomlaQuiz_template_class::JQ_createMDropDown($qdata);
				}
				$quhtml = '';
				if($show_correct)
				{
					$quhtml .= '<div class="contentheading">'._JLMS_QUIZ_REVIEW_YOUR_ANSWER.'</div><table align="center" class="jlms_table_no_borders"><tr><td>';
					for ($i=0, $n = count( $qdata ); $i < $n; $i++ ) {
						$qdata[$i]->c_left_text = $match_data[$i]->c_left_text;
					    $qdata[$i]->c_right_text = mosHTML::selectList( $shuffle_match1, 'quest_match_u', 'class="inputbox" size="1" disabled', 'c_val', 'c_right_text', $user_answer[$i] );
					}
					$quhtml .= JoomlaQuiz_template_class::JQ_createMDropDown($qdata);
					$quhtml .= '</td></tr></table>';
				}
				$ret_str .= "\t" . '<quest_data_user><![CDATA[<div><form name=\'quest_form\'>'. $qhtml .$quhtml.'</form></div>]]></quest_data_user>' . "\n";
			break;
			case 6:
				$query = "SELECT t.c_text FROM #__lms_quiz_t_blank as b, #__lms_quiz_t_text as t"
				. "\n WHERE b.c_question_id = '".$q_data->c_id."' AND t.c_blank_id = b.c_id"
				. "\n ORDER BY t.ordering";
				$JLMS_DB->SetQuery( $query );
				$blank_data = $JLMS_DB->LoadObjectList();
				$ret_str .= "\t" . '<quest_data_user><![CDATA[<div><form name=\'quest_form\'>' . "\n";
				$qhtml = '';
				if($show_correct_answer){
					foreach ($blank_data as $bl_one){
						$qhtml .= JoomlaQuiz_template_class::JQ_createBlankReview($bl_one->c_text) . "<br />";
					}
				}
				$quhtml = '';
				if($show_correct || $q_survey)
				{
					$quhtml .= '<div><div class="contentheading">'._JLMS_QUIZ_REVIEW_YOUR_ANSWER.'</div><table align="center" cellpadding="0" cellspacing="0" class="jlms_table_no_borders"><tr><td>';
					$quhtml .= JoomlaQuiz_template_class::JQ_createBlankReview($user_answer);
					$quhtml .= '</td></tr></table></div>';
				}
				if($q_survey) $qhtml = '';
				$ret_str .= $qhtml . "\n" . "\t" . '</form>'.$quhtml.'</div>]]></quest_data_user>' . "\n";
			break;
			case 7:
				$query = "SELECT * FROM #__lms_quiz_t_hotspot WHERE c_question_id = '".$q_data->c_id."'";
				$JLMS_DB->SetQuery( $query );
				$hotspot_data = $JLMS_DB->LoadObjectList();
				$hs_lefttop_x = 0;
				$hs_lefttop_y = 0;
				$hs_rightbottom_x = 0;
				$hs_rightbottom_y = 0;
				if (isset($hotspot_data[0])) {
					$hs_lefttop_x = $hotspot_data[0]->c_start_x;
					$hs_lefttop_y = $hotspot_data[0]->c_start_y;
					$hs_rightbottom_x = /*$hotspot_data[0]->c_start_x + */$hotspot_data[0]->c_width;
					$hs_rightbottom_y = /*$hotspot_data[0]->c_start_y + */$hotspot_data[0]->c_height;
				}
				$ret_str .= "\t" . '<quest_data_user><![CDATA[<div>';
				$ret_str .= '<div class="contentheading">'._JLMS_QUIZ_REVIEW_YOUR_ANSWER.'</div>';
				$ret_str .= '<center><table class="jlms_table_no_borders"><tr><td align="center"><div style="text-align:left; position:relative;">';
				if($show_correct)
				{
					$ret_str .= '<div id="div_hotspot_rec2" style="background:url(\''.$JLMS_CONFIG->get('live_site').'/components/com_joomla_lms/includes/quiz/templates/joomlaquiz_lms_template/images/hs_round.png\'); z-index:1002; width:12px; height:12px;  position:absolute; left:'.($user_answer[0]-6).'px; top:'.($user_answer[1]+6).'px;"></div>';
				}
				if($show_correct_answer){
					$ret_str .= '<div id="div_hotspot_rec" style="background-color:#FFFFFF; z-index:1001; ' . (preg_match('/MSIE ([0-9].[0-9]{1,2})/', $_SERVER['HTTP_USER_AGENT'])?"filter:alpha(opacity=50);":'') . ' -moz-opacity:.50; opacity:.50; border:1px solid #000000; position:absolute; left:'.$hs_lefttop_x.'px; top:'.$hs_lefttop_y.'px; width:'.$hs_rightbottom_x.'px; height:'.$hs_rightbottom_y.'px; "><img src="'.$JLMS_CONFIG->get('live_site').'/images/blank.png" border="0" width="1" height="1"></div>';
				}
				$ret_str .= '<form name=\'quest_form\'><img id=\'img_hotspot\' style=\'position:relative; z-index:999;\' src=\''.$JLMS_CONFIG->get('live_site').'/images/joomlaquiz/images/'.$q_data->c_image.'\' />'
				. '<input type=\'hidden\' name=\'hotspot_x\' value=\'0\' />'
				. '<input type=\'hidden\' name=\'hotspot_y\' value=\'0\' /></form></div></td></tr></table></center></div>]]></quest_data_user>' . "\n";
			break;
			case 8:
				$ret_str .= "\t" . '<quest_data_user><![CDATA[<form name=\'quest_form\'>' . "\n";
				$qhtml = JoomlaQuiz_template_class::JQ_createSurvey(1, $user_answer);
				$ret_str .= $qhtml . "\n" . "\t" . '</form>]]></quest_data_user>' . "\n";
			break;
			case 9:
				$query = "SELECT * FROM #__lms_quiz_t_scale WHERE c_question_id = '".$q_data->c_id."'"
				. "\n ORDER BY ordering";
				$JLMS_DB->SetQuery( $query );
				$scale_data = $JLMS_DB->LoadObjectList();
				for($i=0;$i<count($scale_data);$i++)
				{
					$scale_data[$i]->inchek = '';
					if($show_correct_answer){
						foreach ($user_answer as $uansw)
						{
							if($uansw[0] == $scale_data[$i]->c_id){
								$scale_data[$i]->inchek = $uansw[1];
							}
						}
					}
				}
				$qhtml = JoomlaQuiz_template_class::JQ_createScale($scale_data,1);
				$ret_str .= "\t" . '<quest_data_user><![CDATA[<div><form name=\'quest_form\'>'. $qhtml .'</form></div>]]></quest_data_user>' . "\n";
			break;
			case 10:
				$ret_str .= "\t" . '<quest_data_user><![CDATA[<form name=\'quest_form\'>' . "\n";
				$ret_str .= "\n" . "\t" . '</form>]]></quest_data_user>' . "\n";
			break;
			case 11:
				$query = "SELECT a.*, a.c_right_text as c_val, b.imgs_name as left_name, c.imgs_name as right_name FROM #__lms_quiz_t_matching as a, #__lms_quiz_images as b, #__lms_quiz_images as c WHERE c_question_id = '".$q_data->c_id."' AND a.c_left_text = b.imgs_id AND a.c_right_text = c.imgs_id"
				. "\n ORDER BY a.ordering";
				$JLMS_DB->SetQuery( $query );
				$match_data = $JLMS_DB->LoadObjectList();
				$shuffle_match = $match_data;
				
				$ret_str .= "\t" . '<quest_data_user><![CDATA[<div>' . "\n\r";
				if($show_correct_answer){
					$ret_str .= '<div style=\'width:100%;text-align:center\'><table id=\'quest_table\' align="center" cellpadding="10" cellspacing="0" border="0" class="jlms_table_no_borders">';
					for ($i=0, $n = count( $match_data ); $i < $n; $i++ ) {
						$ret_str .= '<tr><td style="padding:10px"><div id=\'cdiv_'.($i+1).'\' class=\'jq_drop_pre\' style=\'width: '.$JLMS_CONFIG->get('quiz_match_max_width', 250).'px; height: '.$JLMS_CONFIG->get('quiz_match_max_height', 30).'px;\'>
						<img src="'.ampReplace($JLMS_CONFIG->get('live_site').'/index.php?tmpl=component&option='.$option.'&task=quizzes&page=imgs_v&id='.$q_data->course_id.'&file_id='.$match_data[$i]->c_left_text.'&Itemid='.$Itemid.'&imgs_name='.$match_data[$i]->left_name.'&pic_width='.$JLMS_CONFIG->get('quiz_match_max_width', 250).'&pic_height='.$JLMS_CONFIG->get('quiz_match_max_height', 250).'&bg_color=dddddd').'" border="0" alt="'.$match_data[$i]->left_name.'"/></div></td><td style="padding:10px"><div id=\'ddiv_'.($i+1).'\' class=\'jq_drag_pre\' style=\'width: '.$JLMS_CONFIG->get('quiz_match_max_width', 250).'px; height: '.$JLMS_CONFIG->get('quiz_match_max_height', 30).'px;\'>
						<img src="'.ampReplace($JLMS_CONFIG->get('live_site').'/index.php?tmpl=component&option='.$option.'&task=quizzes&page=imgs_v&id='.$q_data->course_id.'&file_id='.$shuffle_match[$i]->c_right_text.'&Itemid='.$Itemid.'&imgs_name='.$shuffle_match[$i]->right_name.'&pic_width='.$JLMS_CONFIG->get('quiz_match_max_width', 250).'&pic_height='.$JLMS_CONFIG->get('quiz_match_max_height', 250)).'" border="0" alt="'.$shuffle_match[$i]->right_name.'"/></div></td></tr>';
					}
					$ret_str .= '</table></div>' . "\n";
				}
				if($show_correct)
				{
					$ret_str .= '<div style=\'width:100%;text-align:center\'><div class="contentheading">'._JLMS_QUIZ_REVIEW_YOUR_ANSWER.'</div><table id=\'quest_table\' align="center" cellpadding="10" cellspacing="0" border="0" class="jlms_table_no_borders">';
					for ($i=0, $n = count( $match_data ); $i < $n; $i++ ) {
						$ret_str .= '<tr><td style="padding:10px"><div id=\'cdiv_'.($i+1).'\' class=\'jq_drop_pre\' style=\'width: '.$JLMS_CONFIG->get('quiz_match_max_width', 250).'px; height: '.$JLMS_CONFIG->get('quiz_match_max_height', 30).'px;\'>
						<img src="'.ampReplace($JLMS_CONFIG->get('live_site').'/index.php?tmpl=component&option='.$option.'&task=quizzes&page=imgs_v&id='.$q_data->course_id.'&file_id='.$match_data[$i]->c_left_text.'&Itemid='.$Itemid.'&imgs_name='.$match_data[$i]->left_name.'&pic_width='.$JLMS_CONFIG->get('quiz_match_max_width', 250).'&pic_height='.$JLMS_CONFIG->get('quiz_match_max_height', 250)).'&bg_color=dddddd" border="0" alt="'.$match_data[$i]->left_name.'"/></div></td><td style="padding:10px"><div id=\'ddiv_'.($i+1).'\' class=\'jq_drag_pre\' style=\'width: '.$JLMS_CONFIG->get('quiz_match_max_width', 250).'px; height: '.$JLMS_CONFIG->get('quiz_match_max_height', 30).'px;\'>
						<img src="'.ampreplace($JLMS_CONFIG->get('live_site').'/index.php?tmpl=component&option='.$option.'&task=quizzes&page=imgs_v&id='.$q_data->course_id.'&file_id='.$user_answer[$i].'&Itemid='.$Itemid.'&pic_width='.$JLMS_CONFIG->get('quiz_match_max_width', 250).'&pic_height='.$JLMS_CONFIG->get('quiz_match_max_height', 250)).'" border="0" alt=""/></div></td></tr>';
					}
					$ret_str .= '</table></div>' . "\n";		
				}
				$ret_str .= '<form name=\'quest_form\'></form></div>]]></quest_data_user>' . "\n";
			break;
		}
		
		$ret_str .= "\t" . '<exec_quiz_script>1</exec_quiz_script>' . "\n";
		$ret_str .= "\t" . '<quiz_script_data><![CDATA['.$ret_add_script.']]></quiz_script_data>' . "\n";
	}
	return $ret_str;
}

function JQ_GetQuestData_review_nojs($q_data, $jq_language, $user_answer, $show_correct, $q_survey, $msg_cor, $correct=0) {
	global $JLMS_DB, $JLMS_CONFIG, $option, $Itemid;
	$ret_str = '';

	$cur_template = 'joomlaquiz_lms_template';
	if ($cur_template) {
		require_once(dirname(__FILE__) . '/templates/'.$cur_template.'/jq_template.php');
		if($msg_cor != ''){
		$ret_str .= "\t" . '<div style="text-align:center; font-size:14px; padding:5px 20px 5px 10px;">'.JoomlaQuiz_template_class::JQ_show_messagebox('', $msg_cor, $correct).'</div>' . "\n";
		}
		$ret_str .= "\t" . '<div style="text-align:center;">'.$q_data->c_question.'</div>' . "\n";
		$ret_str .= "\t" . '<input type="hidden" name="quest_type" value="'.$q_data->c_type.'"/>' . "\n";
		$ret_str .= "\t" . '<input type="hidden" name="quest_id" value="'.$q_data->old_c_id.'"/>' . "\n";  //$q_data->c_id
		$ret_str .= "\t" . '<input type="hidden" name="quest_score" value="'.$q_data->c_point.'"/>' . "\n";
		switch ($q_data->c_type) {
			case 1:
			case 12:
				$sql_var = '';
				$sql_table = '';
				$sql_where = '';
				if($q_data->c_type == 12){
					$sql_var .= ', b.imgs_name';
					$sql_table = ', #__lms_quiz_images as b';
					$sql_where .= ' AND a.c_choice = b.imgs_id';	
				}				
//				$query = "SELECT a.c_id as value, a.c_choice as text, '0' as c_right, '0' as c_review".$sql_var." FROM #__lms_quiz_t_choice as a".$sql_table." WHERE a.c_question_id = '".$qu_id."'".$sql_where." ORDER BY a.ordering";
				$query = "SELECT a.c_id as value, a.c_choice as text, a.c_right, '1' as c_review".$sql_var." FROM #__lms_quiz_t_choice as a".$sql_table." WHERE a.c_question_id = '".$q_data->c_id."'".$sql_where." ORDER BY a.ordering";
				$JLMS_DB->SetQuery( $query );
				$choice_data = $JLMS_DB->LoadObjectList();
				$qhtml = JoomlaQuiz_template_class::JQ_createMChoice($choice_data, $q_data->c_type);
				$quhtml = '';
				if($show_correct || $q_survey)
				{

					for ($i=0;$i<count($choice_data);$i++)
					{
						if($choice_data[$i]->value == $user_answer)
							$choice_data[$i]->c_right = 1;
						else
							$choice_data[$i]->c_right = 0;	
					}
//					print_r($choice_data); print_r($user_answer);
					$quhtml .= '<div><div class="contentheading">'._JLMS_QUIZ_REVIEW_YOUR_ANSWER.'</div><table cellpadding="0" cellspacing="0" class="jlms_table_no_borders"><tr><td>';
					$quhtml .= JoomlaQuiz_template_class::JQ_createMChoice($choice_data, $q_data->c_type, '_answr');
					$quhtml .= '</td></tr></table></div>';
				}
				if($q_survey) $qhtml = '';
				$ret_str .= "\t" . '<div style="width:100%;"><table cellpadding="0" cellspacing="0" class="jlms_table_no_borders"><tr><td>'.$qhtml.'</td></tr></table></div>'.$quhtml.'' . "\n";
			break;
			case 3:
				$query = "SELECT c_id as value, c_choice as text, c_right, '1' as c_review FROM #__lms_quiz_t_choice WHERE c_question_id = '".$q_data->c_id."' ORDER BY ordering";
				$JLMS_DB->SetQuery( $query );
				$choice_data = $JLMS_DB->LoadObjectList();
				$i = 0;
				while ($i < count($choice_data)) {
					if ( ($choice_data[$i]->text == 'true') || ($choice_data[$i]->text == 'True') ) {
						$choice_data[$i]->text = $jq_language['quiz_simple_true'];
					} elseif ( ($choice_data[$i]->text == 'false') || ($choice_data[$i]->text == 'False') ) {
						$choice_data[$i]->text = $jq_language['quiz_simple_false'];
					}
					$i ++;
				}
				$qhtml = JoomlaQuiz_template_class::JQ_createMChoice($choice_data, $q_data->c_type);
				$quhtml = '';
				if($show_correct)
				{

					for ($i=0;$i<count($choice_data);$i++)
					{
						if($choice_data[$i]->value == $user_answer)
							$choice_data[$i]->c_right = 1;
						else
							$choice_data[$i]->c_right = 0;	
					}
					$quhtml .= '<div><div class="contentheading">'._JLMS_QUIZ_REVIEW_YOUR_ANSWER.'</div><table cellpadding="0" cellspacing="0" class="jlms_table_no_borders"><tr><td>';
					$quhtml .= JoomlaQuiz_template_class::JQ_createMChoice($choice_data, $q_data->c_type, '_answr');
					$quhtml .= '</td></tr></table></div>';
				}
				$ret_str .= "\t" . '<div style="width:100%;"><table cellpadding="0" cellspacing="0" class="jlms_table_no_borders"><tr><td>'.$qhtml.'</td></tr></table></div>'.$quhtml.'' . "\n";
			break;
			case 2:
			case 13:
				$sql_var = '';
				$sql_table = '';
				$sql_where = '';
				if($q_data->c_type == 13){
					$sql_var .= ', b.imgs_name';
					$sql_table = ', #__lms_quiz_images as b';
					$sql_where .= ' AND a.c_choice = b.imgs_id';	
				}				
//				$query = "SELECT a.c_id as value, a.c_choice as text, '0' as c_right, '0' as c_review".$sql_var." FROM #__lms_quiz_t_choice as a".$sql_table." WHERE a.c_question_id = '".$qu_id."'".$sql_where." ORDER BY a.ordering";
				$query = "SELECT a.c_id as value, a.c_choice as text, a.c_right, '1' as c_review".$sql_var." FROM #__lms_quiz_t_choice as a".$sql_table." WHERE a.c_question_id = '".$q_data->c_id."'".$sql_where." ORDER BY a.ordering";
				$JLMS_DB->SetQuery( $query );
				$choice_data = $JLMS_DB->LoadObjectList();
				$qhtml = JoomlaQuiz_template_class::JQ_createMResponse($choice_data, $q_data->c_type);
				$quhtml = '';
				if($show_correct || $q_survey)
				{

					for ($i=0;$i<count($choice_data);$i++)
					{
						if(in_array($choice_data[$i]->value,$user_answer))
							$choice_data[$i]->c_right = 1;
						else
							$choice_data[$i]->c_right = 0;	
					}
					$quhtml .= '<div><div class="contentheading">'._JLMS_QUIZ_REVIEW_YOUR_ANSWER.'</div><table cellpadding="0" cellspacing="0" class="jlms_table_no_borders"><tr><td>';
					$quhtml .= JoomlaQuiz_template_class::JQ_createMResponse($choice_data, $q_data->c_type, 0, '_r');
					$quhtml .= '</td></tr></table></div>';
				}
				if($q_survey) $qhtml = '';
				$ret_str .= "\t" . '<div style=\'width:100%;\'><table cellpadding="0" cellspacing="0" class="jlms_table_no_borders"><tr><td>'.$qhtml.'</td></tr></table></div>'.$quhtml.'' . "\n";
			break;
			case 4:
				$query = "SELECT *, c_right_text as c_val FROM #__lms_quiz_t_matching WHERE c_question_id = '".$q_data->c_id."'"
				. "\n ORDER BY ordering";
				$JLMS_DB->SetQuery( $query );
				$match_data = $JLMS_DB->LoadObjectList();
				$shuffle_match = $match_data;
				$ret_str .= "\t" . '<div>' . "\n"
				. '<div style=\'width:100%;text-align:center\'><table id=\'quest_table\' align="center" class="jlms_table_no_borders">';
				for ($i=0, $n = count( $match_data ); $i < $n; $i++ ) {
					$ret_str .= '<tr><td><div id=\'cdiv_'.($i+1).'\' class=\'jq_drop_pre\'>'
					. $match_data[$i]->c_left_text . '</div></td><td><div id=\'ddiv_'.($i+1).'\' class=\'jq_drag_pre\'>'
					. $shuffle_match[$i]->c_right_text . '</div></td></tr>';
				}
				$ret_str .= '</table></div>' . "\n";
				if($show_correct)
				{
					$ret_str .= '<div style=\'width:100%;text-align:center\'><div class="contentheading">'._JLMS_QUIZ_REVIEW_YOUR_ANSWER.'</div><table id=\'r_quest_table\' align="center" class="jlms_table_no_borders">';
					for ($i=0, $n = count( $match_data ); $i < $n; $i++ ) {
						$ret_str .= '<tr><td><div id=\'r_cdiv_'.($i+1).'\' class=\'jq_drop_pre\'>'
						. $match_data[$i]->c_left_text . '</div></td><td><div id=\'r_ddiv_'.($i+1).'\' class=\'jq_drag_pre\'>'
						. $user_answer[$i] . '</div></td></tr>';
					}
					$ret_str .= '</table></div>' . "\n";
				}
				$ret_str .= '</div>' . "\n";
			break;
			case 5:
				$query = "SELECT *, c_right_text as c_val FROM #__lms_quiz_t_matching WHERE c_question_id = '".$q_data->c_id."'"
				. "\n ORDER BY ordering";
				$JLMS_DB->SetQuery( $query );
				$match_data = $JLMS_DB->LoadObjectList();
				$shuffle_match = $match_data;
				$shuffle_match1 = array();
				$shuffle_match1[0]->c_right_text = _JLMS_QUIZ_SHOW_SELECT_YOUR_ANSWER;
				$shuffle_match1[0]->c_val = '{0}';
				$shuffle_match1 = array_merge( $shuffle_match1, $shuffle_match );
				$qdata = array();
				for ($i=0, $n = count( $match_data ); $i < $n; $i++ ) {
					$qdata[$i]->c_left_text = $match_data[$i]->c_left_text;
					$qdata[$i]->c_right_text = mosHTML::selectList( $shuffle_match1, 'quest_match', 'class="inputbox" size="1" disabled="disabled"', 'c_val', 'c_right_text', $shuffle_match1[$i+1]->c_right_text );
				}
				$qhtml = JoomlaQuiz_template_class::JQ_createMDropDown($qdata);
				$quhtml = '';
				if($show_correct)
				{
					$quhtml .= '<div class="contentheading">'._JLMS_QUIZ_REVIEW_YOUR_ANSWER.'</div><table align="center" class="jlms_table_no_borders"><tr><td>';
					for ($i=0, $n = count( $qdata ); $i < $n; $i++ ) {
						$qdata[$i]->c_left_text = $match_data[$i]->c_left_text;
					    $qdata[$i]->c_right_text = mosHTML::selectList( $shuffle_match1, 'quest_match_u', 'class="inputbox" size="1" disabled="disabled"', 'c_val', 'c_right_text', $user_answer[$i] );
					}
					$quhtml .= JoomlaQuiz_template_class::JQ_createMDropDown($qdata);
					$quhtml .= '</td></tr></table>';
				}
				$ret_str .= "\t" . '<div>'. $qhtml .$quhtml.'</div>' . "\n";
			break;
			case 6:
				$query = "SELECT t.c_text FROM #__lms_quiz_t_blank as b, #__lms_quiz_t_text as t"
				. "\n WHERE b.c_question_id = '".$q_data->c_id."' AND t.c_blank_id = b.c_id"
				. "\n ORDER BY t.ordering";
				$JLMS_DB->SetQuery( $query );
				$blank_data = $JLMS_DB->LoadObjectList();
				$ret_str .= "\t" . '<div style="text-align:center;">' . "\n";
				$qhtml = '';
				foreach ($blank_data as $bl_one) {
					$qhtml .= JoomlaQuiz_template_class::JQ_createBlank($bl_one->c_text) . "<br />";
				}
				$quhtml = '';
				if($show_correct || $q_survey)
				{
					$quhtml .= '<div style="text-align:center;"><div class="contentheading">'._JLMS_QUIZ_REVIEW_YOUR_ANSWER.'</div><table align="center" cellpadding="0" cellspacing="0" class="jlms_table_no_borders"><tr><td>';
					$quhtml .= JoomlaQuiz_template_class::JQ_createBlank($user_answer);
					$quhtml .= '</td></tr></table></div>';
				}
				if($q_survey) $qhtml = '';
				$ret_str .= $qhtml . "\n" . "\t" . ''.$quhtml.'</div>' . "\n";
			break;
			case 7:
				$query = "SELECT * FROM #__lms_quiz_t_hotspot WHERE c_question_id = '".$q_data->c_id."'";
				$JLMS_DB->SetQuery( $query );
				$hotspot_data = $JLMS_DB->LoadObjectList();
				
				$hs_lefttop_x = 0;
				$hs_lefttop_y = 0;
				$hs_rightbottom_x = 0;
				$hs_rightbottom_y = 0;
				if (isset($hotspot_data[0])) {
					$hs_lefttop_x = $hotspot_data[0]->c_start_x;
					$hs_lefttop_y = $hotspot_data[0]->c_start_y;
					$hs_rightbottom_x = /*$hotspot_data[0]->c_start_x + */$hotspot_data[0]->c_width;
					$hs_rightbottom_y = /*$hotspot_data[0]->c_start_y + */$hotspot_data[0]->c_height;
				}
				$ret_str .= "\t" . '<center><table class="jlms_table_no_borders"><tr><td align="center"><div style="text-align:left; position:relative" >';
				if($show_correct)
				{
				$ret_str .= '<div id="div_hotspot_rec2" style="background:url(\''.$JLMS_CONFIG->get('live_site').'/components/com_joomla_lms/includes/quiz/templates/joomlaquiz_lms_template/images/hs_round.png\'); z-index:1002; width:12px; height:12px;  position:absolute; left:'.($user_answer[0]-6).'px; top:'.($user_answer[1]+6).'px;"></div>';
				}
				$ret_str .= '<div id="div_hotspot_rec" style="background-color:#FFFFFF; z-index:1001; ' . (preg_match('/MSIE ([0-9].[0-9]{1,2})/', $_SERVER['HTTP_USER_AGENT'])?"filter:alpha(opacity=50);":'') . ' -moz-opacity:.50; opacity:.50; border:1px solid #000000; position:absolute; left:'.$hs_lefttop_x.'px; top:'.$hs_lefttop_y.'px; width:'.($hs_rightbottom_x).'px; height:'.($hs_rightbottom_y).'px; "><img src="'.$JLMS_CONFIG->get('live_site').'/images/blank.png" border="0" width="1" height="1" alt="" /></div>'
				. '<img id=\'img_hotspot\' src=\'images/joomlaquiz/images/'.$q_data->c_image.'\' alt="" />'
				. '<input type=\'hidden\' name=\'hotspot_x\' value=\'0\' />'
				. '<input type=\'hidden\' name=\'hotspot_y\' value=\'0\' /></div></td></tr></table></center>' . "\n";
			break;
			case 8:
				$ret_str .= "\t" . '' . "\n";
				$qhtml = JoomlaQuiz_template_class::JQ_createSurvey(1, $user_answer);
				$ret_str .= $qhtml . "\n" . "\t" . '' . "\n";
			break;
			case 9:
				$query = "SELECT * FROM #__lms_quiz_t_scale WHERE c_question_id = '".$q_data->c_id."'"
				. "\n ORDER BY ordering";
				$JLMS_DB->SetQuery( $query );
				$scale_data = $JLMS_DB->LoadObjectList();
				for($i=0;$i<count($scale_data);$i++)
				{
					$scale_data[$i]->inchek = '';
					foreach ($user_answer as $uansw)
					{
						if($uansw[0] == $scale_data[$i]->c_id)
							$scale_data[$i]->inchek = $uansw[1];
					}
				}
				$qhtml = JoomlaQuiz_template_class::JQ_createScale($scale_data,1);
				$ret_str .= "\t" . '<div>'. $qhtml .'</div>' . "\n";
			break;
			case 10:
				$ret_str .= "\t" . '' . "\n";
				$ret_str .= "\n" . "\t" . '' . "\n";
			break;
			case 11:
				$query = "SELECT a.*, a.c_right_text as c_val, b.imgs_name as left_name, c.imgs_name as right_name FROM #__lms_quiz_t_matching as a, #__lms_quiz_images as b, #__lms_quiz_images as c WHERE c_question_id = '".$q_data->c_id."' AND a.c_left_text = b.imgs_id AND a.c_right_text = c.imgs_id"
				. "\n ORDER BY a.ordering";
				$JLMS_DB->SetQuery( $query );
				$match_data = $JLMS_DB->LoadObjectList();
				$shuffle_match = $match_data;
//				shuffle($shuffle_match);
				$ret_str .= "\t" . '<div>'
				. '<div style=\'width:100%;text-align:center\'><table id=\'quest_table\' align="center" class="jlms_table_no_borders">';
				for ($i=0, $n = count( $match_data ); $i < $n; $i++ ) {
					$ret_str .= '<tr><td><div id=\'cdiv_'.($i+1).'\' class=\'jq_drop_pre\' style=\'width: '.$JLMS_CONFIG->get('quiz_match_max_width', 250).'px; height: '.$JLMS_CONFIG->get('quiz_match_max_height', 30).'px;\'>
					<img src="'.ampReplace($JLMS_CONFIG->get('live_site').'/index.php?tmpl=component&option='.$option.'&task=quizzes&page=imgs_v&id='.$q_data->course_id.'&file_id='.$match_data[$i]->c_left_text.'&Itemid='.$Itemid.'&imgs_name='.$match_data[$i]->left_name.'&pic_width='.$JLMS_CONFIG->get('quiz_match_max_width', 250).'&pic_height='.$JLMS_CONFIG->get('quiz_match_max_height', 250).'&bg_color=dddddd').'" border="0" alt="'.$match_data[$i]->left_name.'"/></div></td><td><div id=\'ddiv_'.($i+1).'\' class=\'jq_drag_pre\' style=\'width: '.$JLMS_CONFIG->get('quiz_match_max_width', 250).'px; height: '.$JLMS_CONFIG->get('quiz_match_max_height', 30).'px;\'>
					<img src="'.ampReplace($JLMS_CONFIG->get('live_site').'/index.php?tmpl=component&option='.$option.'&task=quizzes&page=imgs_v&id='.$q_data->course_id.'&file_id='.$shuffle_match[$i]->c_right_text.'&Itemid='.$Itemid.'&imgs_name='.$shuffle_match[$i]->right_name.'&pic_width='.$JLMS_CONFIG->get('quiz_match_max_width', 250).'&pic_height='.$JLMS_CONFIG->get('quiz_match_max_height', 250)).'" border="0" alt="'.$shuffle_match[$i]->right_name.'"/></div></td></tr>';
				}
				$ret_str .= '</table></div>' . "\n";
				if($show_correct)
				{
					$ret_str .= '<div style=\'width:100%;text-align:center\'><div class="contentheading">'._JLMS_QUIZ_REVIEW_YOUR_ANSWER.'</div><table id=\'r_quest_table\' align="center" class="jlms_table_no_borders">';
					for ($i=0, $n = count( $match_data ); $i < $n; $i++ ) {
						$ret_str .= '<tr><td><div id=\'r_cdiv_'.($i+1).'\' class=\'jq_drop_pre\' style=\'width: '.$JLMS_CONFIG->get('quiz_match_max_width', 250).'px; height: '.$JLMS_CONFIG->get('quiz_match_max_height', 30).'px;\'>
						<img src="'.ampReplace($JLMS_CONFIG->get('live_site').'/index.php?tmpl=component&option='.$option.'&task=quizzes&page=imgs_v&id='.$q_data->course_id.'&file_id='.$match_data[$i]->c_left_text.'&Itemid='.$Itemid.'&imgs_name='.$match_data[$i]->left_name.'&pic_width='.$JLMS_CONFIG->get('quiz_match_max_width', 250).'&pic_height='.$JLMS_CONFIG->get('quiz_match_max_height', 250).'&bg_color=dddddd').'" border="0" alt="'.$match_data[$i]->left_name.'"/></div></td><td><div id=\'r_ddiv_'.($i+1).'\' class=\'jq_drag_pre\' style=\'width: '.$JLMS_CONFIG->get('quiz_match_max_width', 250).'px; height: '.$JLMS_CONFIG->get('quiz_match_max_height', 30).'px;\'>
						<img src="'.ampReplace($JLMS_CONFIG->get('live_site').'/index.php?tmpl=component&option='.$option.'&task=quizzes&page=imgs_v&id='.$q_data->course_id.'&file_id='.$user_answer[$i].'&Itemid='.$Itemid.'&pic_width='.$JLMS_CONFIG->get('quiz_match_max_width', 250).'&pic_height='.$JLMS_CONFIG->get('quiz_match_max_height', 250)).'" border="0" alt=""/></div></td></tr>';
					}
					$ret_str .= '</table></div>' . "\n";		
				}
				$ret_str .= '</div>' . "\n";
			break;
		}
	}
	return $ret_str;
}

function JQ_GetPanelData($quiz_id, $panel_data = array()) {
	global $JLMS_DB;
	$panel_str = "\t" . '<quiz_panel_data><![CDATA[';
	if (empty($panel_data)) {
		$query = "SELECT * FROM #__lms_quiz_t_question WHERE c_quiz_id = '".$quiz_id."' ORDER BY ordering, c_id";
		$JLMS_DB->SetQuery( $query );
		$panel_data = $JLMS_DB->LoadObjectList();
	}
	$panel_str .= '<table id="jq_results_panel_table" width="100%" style="padding: 0px 20px 0px 20px" cellpadding="0" cellspacing="0" class="jlms_table_no_borders">';
	$k = 1;
	foreach ($panel_data as $panel_row) {
		$panel_str .= '<tr class="sectiontableentry'.$k.'"><td><a href="javascript:void(0)" onclick="javascript:JQ_gotoQuestionOn('.$panel_row->c_id.')">'.jlms_string_substr(strip_tags($panel_row->c_question),0,50).'</a></td><td width="40px" align="center">'.$panel_row->c_point.'</td><td width="25px" align="center"><div id="quest_result_'.$panel_row->c_id.'">-</div></td></tr>';
		$k = 3 - $k;
	}
	$panel_str .= '</table>]]></quiz_panel_data>' . "\n";
	return $panel_str;
}

function JQ_GetPanelData_resume($quiz_id, $panel_data = array()) {
	global $JLMS_DB;
	$panel_str = "\t" . '<quiz_panel_data><![CDATA[';

if (empty($panel_data)) {
	$query = "SELECT a.c_id, a.c_question, a.c_point
    			FROM #__lms_quiz_t_question AS a
    			WHERE a.c_quiz_id = '".$quiz_id."' ORDER BY a.ordering, a.c_id";
			$JLMS_DB->SetQuery( $query );
		$panel_data = $JLMS_DB->LoadObjectList();
}
	for($i=0;$i<count($panel_data);$i++) {
		
		$query = "SELECT b.c_correct, b.c_score FROM #__lms_quiz_r_student_question AS b WHERE b.c_question_id = '".$panel_data[$i]->c_id."' AND b.c_stu_quiz_id = '".mosGetParam($_REQUEST,'resume_id',0)."' ORDER BY b.c_id desc";
		$JLMS_DB->SetQuery( $query );
		$rows = $JLMS_DB->LoadObjectList();
		 
		if(isset($rows[0]->c_correct)) {
			$panel_data[$i]->c_correct = $rows[0]->c_correct;
			$panel_data[$i]->c_score = $rows[0]->c_score;
		}
		unset($row);
	}

	//$query = "SELECT a.c_id, a.c_question, a.c_point, b.c_correct, b.c_score FROM #__lms_quiz_t_question AS a, #__lms_quiz_r_student_question AS b WHERE b.c_stu_quiz_id = '".MosGetParam($_REQUEST,'resume_id',0)."' AND a.c_quiz_id = '".$quiz_id."' ORDER BY a.ordering, a.c_id";
		
		/*
	$query = "SELECT a.*, b.c_score, b.c_correct FROM  #__lms_quiz_t_question as a, #__lms_quiz_r_student_question as b"
	. "\n WHERE b.c_stu_quiz_id = ".mosGetParam($_REQUEST,'resume_id',0)." AND b.c_question_id = a.c_id";
	//. "\n WHERE a.course_id = ".mosGetParam($_REQUEST,'course_id',0)."";// (a.c_quiz_id = ".$this->quiz_id." OR (a.c_quiz_id = 0 AND )) ORDER BY b.ordering";
	$JLMS_DB->SetQuery( $query );
	$panel_data = $JLMS_DB->LoadObjectList();	*/
		
	$panel_str .= '<table id="jq_results_panel_table" width="100%" style="padding: 0px 20px 0px 20px" cellpadding="0" cellspacing="0" class="jlms_table_no_borders">';
	$k = 1;
	
	foreach ($panel_data as $panel_row) {
		$panel_str .= '<tr class="sectiontableentry'.$k.'"><td><a href="javascript:void(0)" onclick="javascript:JQ_gotoQuestionOn('.$panel_row->c_id.')">'.jlms_string_substr(strip_tags($panel_row->c_question),0,50).'</a></td><td width="40px" align="center">'.$panel_row->c_point.'</td><td width="25px" align="center"><div id="quest_result_'.$panel_row->c_id.'">'.JLMS_quiz_ajax_class::btn_picture(isset($panel_row->c_correct)?$panel_row->c_correct:0,isset($panel_row->c_score)?$panel_row->c_score:0, $panel_row->c_point).'</div></td></tr>';
		$k = 3 - $k;
	}
	$panel_str .= '</table>]]></quiz_panel_data>' . "\n";
	return $panel_str;
}

function btn_picture ($c_correct, $c_score, $c_point ) {
	$JLMS_CONFIG = & JLMSFactory::getConfig();
	
	if(isset($c_correct) && $c_correct) {
		if($c_correct == 1) {
			return '<img border="0" src="'.$JLMS_CONFIG->get('live_site').'/components/com_joomla_lms/lms_images/toolbar/btn_cancel.png" alt=""/>';
		} elseif($c_correct == 2) {
			return '<img border="0" src="'.$JLMS_CONFIG->get('live_site').'/components/com_joomla_lms/lms_images/toolbar/btn_accept.png" alt=""/>';
		}
	} else {
		if(isset($c_score) && $c_score > 0 && $stu_score == $c_point) {
			return '<img border="0" src="'.$JLMS_CONFIG->get('live_site').'/components/com_joomla_lms/lms_images/toolbar/btn_accept.png" alt=""/>';
		} elseif(isset($c_score) && $c_score > 0) {
			return '<img border="0" src="'.$JLMS_CONFIG->get('live_site').'/components/com_joomla_lms/lms_images/toolbar/btn_cancel.png" alt=""/>';
		} else {
			return '-';
		}									
	}
}

function JQ_GetPanelData_nojs($quiz_id, $panel_data = array()) {
	global $JLMS_DB;
	$panel_str = "\t" . '<quiz_panel_data>';
	if (empty($panel_data)) {
		$query = "SELECT * FROM #__lms_quiz_t_question WHERE c_quiz_id = '".$quiz_id."' ORDER BY ordering, c_id";
		$JLMS_DB->SetQuery( $query );
		$panel_data = $JLMS_DB->LoadObjectList();
	}
	$panel_str .= '<table id="jq_results_panel_table" width="100%" style="padding: 0px 20px 0px 20px" cellpadding="0" cellspacing="0" class="jlms_table_no_borders">';
	$k = 1;
	foreach ($panel_data as $panel_row) {
		$panel_str .= '<tr class="sectiontableentry'.$k.'"><td><a href="javascript:void(0)" onclick="javascript:JQ_gotoQuestionOn('.$panel_row->c_id.')">'.jlms_string_substr(strip_tags($panel_row->c_question),0,50).'</a></td><td width="40px" align="center">'.$panel_row->c_point.'</td><td width="25px" align="center"><div id="quest_result_'.$panel_row->c_id.'">-</div></td></tr>';
		$k = 3 - $k;
	}
	$panel_str .= '</table></quiz_panel_data>' . "\n";
	return $panel_str;
}

function JQ_GetPanelData_LP($quiz_id, $course_id, $panel_data = array()) {
	global $JLMS_DB;
	$JLMS_CONFIG = & JLMSFactory::getConfig();

	$lpath_id = intval( mosGetParam( $_REQUEST, 'lpath_id', 0 ) );
	$step_id = intval( mosGetParam( $_REQUEST, 'step_id', 0 ) );
	$result_id = intval( mosGetParam( $_REQUEST, 'user_start_id', 0 ) );
	$result_uniq = strval( mosGetParam( $_REQUEST, 'lp_user_unique_id', '' ) );
	// return id of this step, of next step and return quiz contents...... (to create 'contentents' at the F.E.)
	$lpath_contents = JLMS_GetLPath_Data($lpath_id, $course_id);
	$panel_str = '';
	
	$tree_modes = array();
	$prev_tds = array();
	$colspan = 0;
	for ($i=0, $n=count($lpath_contents); $i < $n; $i++) {
		$row_path = $lpath_contents[$i];
		$max_tree_width = $row_path->tree_max_width;
		
		if ($row_path->tree_mode_num) {
			$g = 0;
			$tree_modes[$row_path->tree_mode_num - 1] = $row_path->tree_mode;
			while ($g < ($row_path->tree_mode_num - 1)) {
				$pref = '';
				if (isset($tree_modes[$g]) && ($tree_modes[$g] == 2) ) { $pref = 'empty_'; }
				if ($row_path->id == $step_id) {
					$prev_tds[] = "<img src='".$JLMS_CONFIG->get('live_site')."/components/com_joomla_lms/lms_images/treeview/".$pref."line.png' width='16' height='16' alt='line' border='0' />";
				}
				$g ++;
			}
			if ($row_path->id == $step_id) {
				$pref = '';
				if ($row_path->tree_mode == 2) {
					$pref = 'empty_';
				}
				$prev_tds[] = "<img src='".$JLMS_CONFIG->get('live_site')."/components/com_joomla_lms/lms_images/treeview/".$pref."line.png' width='16' height='16' border='0' alt='line' />";
			}
			$max_tree_width = $max_tree_width - $g - 1;
		}
		
		if ($row_path->id == $step_id) {
			$colspan = $max_tree_width + 1;
		}
	}
	
	/*options Quiz*/
	$query = "SELECT * FROM #__lms_quiz_t_quiz WHERE c_id = '".$quiz_id."'";
	$JLMS_DB->setQuery($query);
	$option_quiz = $JLMS_DB->loadObject();
	
	/*Fix contents quiz in LPath (Max)*/
	if(isset($option_quiz->c_slide) && $option_quiz->c_slide){
	/*Fix contents quiz in LPath (Max)*/	
		$panel_str .= "\t" . '<prev_tds_count>' . count($prev_tds) . '</prev_tds_count>' . "\n";
		for ($i = 0, $n = count($prev_tds); $i < $n; $i ++) {
			$panel_str .= "\t" . '<prev_td_'.($i+1).'><![CDATA[' . $prev_tds[$i] . ']]></prev_td_'.($i+1).'>' . "\n";
		}
		$panel_str .= "\t" . '<quest_colspan>' . $colspan . '</quest_colspan>' . "\n";
	
	
		if (empty($panel_data)) {
			$query = "SELECT * FROM #__lms_quiz_t_question WHERE c_quiz_id = '".$quiz_id."' ORDER BY ordering, c_id";
			$JLMS_DB->SetQuery( $query );
			$panel_data = $JLMS_DB->LoadObjectList();
		}
		$panel_str .= "\t" . '<quest_count_c_gen>' . count($panel_data) . '</quest_count_c_gen>' . "\n";
	/*Fix contents quiz in LPath (Max)*/	
	} else {
		$panel_str .= "\t" . '<prev_tds_count>0</prev_tds_count>' . "\n";	
		$panel_str .= "\t" . '<quest_colspan>' . $colspan . '</quest_colspan>' . "\n";
		$panel_str .= "\t" . '<quest_count_c_gen>0</quest_count_c_gen>' . "\n";
	}
	/*Fix contents quiz in LPath (Max)*/
	if (!empty($panel_data)) {
		#$panel_str .= "\t" . '<questions>' . "\n";
		$i = 0;
		foreach ($panel_data as $panel_row) {
			$panel_str .= "\t" . '<question_'.($i+1).'_id>' . $panel_row->c_id . '</question_'.($i+1).'_id>' . "\n";
			$panel_str .= "\t" . '<question_'.($i+1).'_points>' . $panel_row->c_point . '</question_'.($i+1).'_points>' . "\n";
			$panel_str .= "\t" . '<question_'.($i+1).'_text><![CDATA[' . jlms_string_substr(strip_tags($panel_row->c_question),0,50) . ']]></question_'.($i+1).'_text>' . "\n";
			$i ++;
		}
		#$panel_str .= "\t" . '</questions>' . "\n";
	}



	//$panel_str .= "\t" . '</quiz_panel_data_gen>' . "\n";
	return $panel_str;	
	
	
	
	
	
	
	
	/*$panel_str = "\t" . '<quiz_panel_data><![CDATA[';
	if (empty($panel_data)) {
		$query = "SELECT * FROM #__lms_quiz_t_question WHERE c_quiz_id = '".$quiz_id."' ORDER BY ordering, c_id";
		$JLMS_DB->SetQuery( $query );
		$panel_data = $JLMS_DB->LoadObjectList();
	}
	$panel_str .= '<table id="jq_results_panel_table" width="100%" style="padding: 0px 20px 0px 20px" class="">';
	$k = 1;
	foreach ($panel_data as $panel_row) {
		$panel_str .= '<tr class="sectiontableentry'.$k.'"><td><a href="javascript:void(0)" onclick="javascript:JQ_gotoQuestionOn('.$panel_row->c_id.')">'.substr(strip_tags($panel_row->c_question),0,50).'</a></td><td width="40px" align="center">'.$panel_row->c_point.'</td><td width="25px" align="center"><div id="quest_result_'.$panel_row->c_id.'">-</div></td></tr>';
		$k = 3 - $k;
	}
	$panel_str .= '</table>]]></quiz_panel_data>' . "\n";
	return $panel_str;*/
}

/*no script javascript*/

	function JQ_main_nojs($jq_task){
		global $JLMS_DB;
		
		switch($jq_task){
			case 'start':			JLMS_quiz_ajax_class::JQ_StartQuiz_nojs();			break;
			case 'next':			JLMS_quiz_ajax_class::JQ_NextQuestion_nojs();		break;
			case 'next_load':		JLMS_quiz_ajax_class::JQ_LoadNextData_nojs();		break;
			case 'finish_stop':		JLMS_quiz_ajax_class::JQ_FinishQuiz_nojs();			break;
			case 'email_results':	JLMS_quiz_ajax_class::JQ_emailResults_nojs();		break;
			case 'review_start':	JLMS_quiz_ajax_class::JQ_StartReview_nojs();		break;
			case 'review_next':		JLMS_quiz_ajax_class::JQ_NextReview_nojs();			break;
			case 'review_stop':		JLMS_quiz_ajax_class::JQ_ResultsQuiz_nojs();		break;
			case 'preview_quest':	JLMS_quiz_ajax_class::JQ_QuestPreview_nojs();		break;
			case 'next_preview':	JLMS_quiz_ajax_class::JQ_NextPreview_nojs();		break;
			case 'goto_quest':		JLMS_quiz_ajax_class::JQ_SeekQuestion_nojs();		break;
			case 'contents':		JLMS_quiz_ajax_class::JQ_Content_nojs();			break;
			default:	break;
		}
	}
	
	function JQ_StartQuiz_nojs(){
		global $JLMS_DB, $my, $option, $Itemid, $JLMS_CONFIG;
		$ret_str = '';
		
		$doc = & JFactory::getDocument();
		
		$id = intval( mosGetParam( $_REQUEST, 'id', 0 ) );
//		$this_lpath = intval( mosGetParam( $_REQUEST, 'this_lpath', 0 ) );
		
		$quiz_id = intval( mosGetParam( $_REQUEST, 'quiz', 0 ) );
		$inside_lp = intval( mosGetParam( $_REQUEST, 'inside_lp', 0 ) );
		
		$pool_mode = intval( mosGetParam( $_REQUEST, 'c_pool_type', 0 ) );
		$cats_id = implode(",", mosGetParam( $_REQUEST, 'pool_cat_id', array()) );
		$pool_num = implode(",", mosGetParam( $_REQUEST, 'pool_cat_number', array()) );
		
		//Max 21.03.08
		$mode_self = 0;	
		if($pool_mode == 1){
			$mode_self = 1;	
		}
		if($pool_mode == 2){
			if(gettype($cats_id) == 'string'){
				$mode_self = 2;	
			} elseif(gettype($cats_id) == 'integer'){
				$mode_self = 3;	
			}
		}
		
		$QA = new JLMS_quiz_API($quiz_id, $inside_lp);
		if (!$QA->quiz_valid()) {
			return '';
		}
		$QA->quiz_Gen_UID();
		$QA->mode_self_verification_data($mode_self, $cats_id, $pool_num);
		$QA->quiz_New_Start();
		$q_data = $QA->quiz_Get_QuestionList();
		
		$kol_quests = count($q_data);
		$quest_score = $q_data[0]->c_point;
		$qtype = $q_data[0]->c_type;
		$quest_id = $q_data[0]->c_id;
		
		// preloading QUIZ languge (28.02.2007 new method) (all quizzes messages now in global quiz language)
		global $JLMS_LANGUAGE, $JLMS_CONFIG;
		JLMS_require_lang($JLMS_LANGUAGE, 'quiz.lang', $JLMS_CONFIG->get('default_language'));
		//require(_JOOMLMS_FRONT_HOME . "/languages/".$JLMS_CONFIG->get('default_language').'/quiz.lang.php');
		require(_JOOMLMS_FRONT_HOME . '/includes/quiz/quiz_language.php');
		global $jq_language;
		
		$stu_quiz_id = $QA->get('stu_quiz_id',0);
		$user_unique_id = $QA->get('user_unique_id','');
		
		$query = "SELECT a.*, b.lpath_id FROM #__lms_learn_path_step_quiz_results as a, #__lms_learn_path_steps as b WHERE a.stu_quiz_id = '".$stu_quiz_id."' AND a.step_id = b.id";
		$JLMS_DB->setQuery($query);
		$this_lpath = $JLMS_DB->LoadObject();
		
		$toolbar = array();
		if(isset($this_lpath->stu_quiz_id) && $this_lpath->stu_quiz_id == $stu_quiz_id){
			if($qtype == 10){
				if(isset($q_data[0]->c_slide) && $q_data[0]->c_slide){
					$toolbar[] = array('kol_quests' => $kol_quests, 'num_quest' => 1, 'quest_score' => $quest_score, 'img_btn'=>'btn_start.png', 'text_btn'=>_JLMS_NEXT_ALT_TITLE, 'link_cont'=>'&task=show_lpath_nojs&action=contents_lpath&course_id='.$id.'&id='.$this_lpath->lpath_id.'&stu_quiz_id='.$stu_quiz_id.'&user_unique_id='.$this_lpath->unique_id.'&step_id='.$this_lpath->step_id.'&user_start_id='.$this_lpath->start_id.'&quest_id='.$quest_id.'&quiz_id='.$quiz_id.'&stu_quiz_id='.$stu_quiz_id.'');
				} else {
					$toolbar[] = array('kol_quests' => $kol_quests, 'num_quest' => 1, 'quest_score' => $quest_score, 'img_btn'=>'btn_start.png', 'text_btn'=>_JLMS_NEXT_ALT_TITLE);	
				}
			} else {
				if(isset($q_data[0]->c_slide) && $q_data[0]->c_slide){
					$toolbar[] = array('kol_quests' => $kol_quests, 'num_quest' => 1, 'quest_score' => $quest_score, 'img_btn'=>'btn_complete.png', 'text_btn'=>_JLMS_OK_ALT_TITLE, 'link_cont'=>'&task=show_lpath_nojs&action=contents_lpath&course_id='.$id.'&id='.$this_lpath->lpath_id.'&stu_quiz_id='.$stu_quiz_id.'&user_unique_id='.$this_lpath->unique_id.'&step_id='.$this_lpath->step_id.'&user_start_id='.$this_lpath->start_id.'&quest_id='.$quest_id.'&quiz_id='.$quiz_id.'&stu_quiz_id='.$stu_quiz_id.'');
				} else {
					$toolbar[] = array('kol_quests' => $kol_quests, 'num_quest' => 1, 'quest_score' => $quest_score, 'img_btn'=>'btn_complete.png', 'text_btn'=>_JLMS_OK_ALT_TITLE);	
				}
			}
		} else {
			if($qtype == 10){
				if(isset($q_data[0]->c_slide) && $q_data[0]->c_slide){
					$toolbar[] = array('kol_quests' => $kol_quests, 'num_quest' => 1, 'quest_score' => $quest_score, 'img_btn'=>'btn_start.png', 'text_btn'=>_JLMS_NEXT_ALT_TITLE, 'link_cont'=>'&task=quiz_action&atask=contents&id='.$id.'&quiz='.$quiz_id.'&stu_quiz_id='.$stu_quiz_id.'&user_unique_id='.$user_unique_id.'&quest_id='.$quest_id.'');				
				} else {
					$toolbar[] = array('kol_quests' => $kol_quests, 'num_quest' => 1, 'quest_score' => $quest_score, 'img_btn'=>'btn_start.png', 'text_btn'=>_JLMS_NEXT_ALT_TITLE);
				}
			} else {
				if(isset($q_data[0]->c_slide) && $q_data[0]->c_slide){
					$toolbar[] = array('kol_quests' => $kol_quests, 'num_quest' => 1, 'quest_score' => $quest_score, 'img_btn'=>'btn_complete.png', 'text_btn'=>_JLMS_OK_ALT_TITLE, 'link_cont'=>'&task=quiz_action&atask=contents&id='.$id.'&quiz='.$quiz_id.'&stu_quiz_id='.$stu_quiz_id.'&user_unique_id='.$user_unique_id.'&quest_id='.$quest_id.'');
				} else {
					$toolbar[] = array('kol_quests' => $kol_quests, 'num_quest' => 1, 'quest_score' => $quest_score, 'img_btn'=>'btn_complete.png', 'text_btn'=>_JLMS_OK_ALT_TITLE);	
				}
			}
		}
				
		$doc->addStyleSheet( $JLMS_CONFIG->get('live_site').'/components/com_joomla_lms/includes/quiz/templates/joomlaquiz_lms_template/jq_template.css' );
		?>
		<form name="quest_form" action="<?php echo ampReplace($JLMS_CONFIG->get('live_site')."/index.php?option=$option&Itemid=$Itemid");?>" method="post">
			<table border="0" width="100%" align="center" cellpadding="0" cellspacing="0" class="jlms_table_no_borders">
				<tr>
					<td>
						<?php echo JLMS_quiz_ajax_class::JQ_toolbar_nojs($toolbar, $qtype, 1, $this_lpath);?>
			
						<input type="hidden" name="stu_quiz_id" value="<?php echo $stu_quiz_id;?>"/>
						<input type="hidden" name="user_unique_id" value="<?php echo $user_unique_id;?>"/>
					</td>
				</tr>
				<tr>
					<td>
						<?php
						
						if ($kol_quests > 0) {
							$quest_num = 0;
							# commented 25 April 2007 (DEN)
							# we've already randomized auestions in the sequence
							/*if ($QA->get_qvar('c_random')) {
								$quest_num = rand(0, ($kol_quests - 1) );
							}*/
							?>
							<input type="hidden" name="quiz_count_quests" value="<?php echo $kol_quests;?>"/>
							<input type="hidden" name="quiz_quest_num" value="1"/>
							<?php echo JLMS_quiz_ajax_class::JQ_GetQuestData_nojs($q_data[$quest_num], $jq_language, $QA->get('stu_quiz_id',0));
				//			$ret_str .= JLMS_quiz_ajax_class::JQ_GetPanelData_nojs($quiz_id, $q_data); ?>
							<?php
						} else {  }
						?>
					</td>
				</tr>
			</table>
			
			<input type="hidden" name="option" value="<?php echo $option;?>"/>
			<input type="hidden" name="task" value="quiz_action"/>
			<input type="hidden" name="id" value="<?php echo $id;?>"/>
			<input type="hidden" name="quiz" value="<?php echo $quiz_id;?>"/>
			<input type="hidden" name="atask" value="next"/>
		</form>
		<?php
//		echo $ret_str;
//		return $ret_str;	
	}
	
	function JQ_NextQuestion_nojs() {
		global $JLMS_DB, $my, $option, $Itemid;
		
		$ret_str = '';
		$id = intval( mosGetParam( $_REQUEST, 'id', 0 ) );
		$quiz_id = intval( mosGetParam( $_REQUEST, 'quiz', 0 ) );
		$inside_lp = intval( mosGetParam( $_REQUEST, 'inside_lp', 0 ) );
		$QA = new JLMS_quiz_API($quiz_id, $inside_lp);
		if (!$QA->quiz_valid()) {
			return '';
		}
		$toolbar_no_a = $QA->quiz_Get_NoAtToolbar();
	
		$stu_quiz_id = intval( mosGetParam( $_REQUEST, 'stu_quiz_id', 0 ) );
		$quest_id = intval( mosGetParam( $_REQUEST, 'quest_id', 0 ) );
		$quiz_quest_num = intval( mosGetParam( $_REQUEST, 'quiz_quest_num', 0 ) );
		$user_unique_id = strval( mosGetParam( $_REQUEST, 'user_unique_id', '') );
		
		$quest_score = intval( mosGetParam( $_REQUEST, 'quest_score', 0 ) );
//		$answer = strval( mosGetParam( $_REQUEST, 'answer', '' ) );
		$id = $QA->get_qvar('course_id', 0);		

		$QA->set('stu_quiz_id', $stu_quiz_id);
		$QA->set('user_unique_id', $user_unique_id);
		$QA->quiz_ProcessStartData();
		$q_data = $QA->quiz_Get_QuestionList();
		$kol_quests = count($q_data);
		
	
		if ( $QA->start_valid() && $quest_id ) {
	
			$quiz = $QA->quiz_data;// temporary for compatibility
			$quiz_params = new JLMSParameters($QA->get_qvar('params'));
	
			if ($QA->time_is_up()) {
				return JLMS_quiz_ajax_class::JQ_TimeIsUp_nojs($quiz);
			}
	
			# commented 25 April 2007 by DEN
			# We could remove this unnecesary query and find all neede information about question in $q_data;
			/* // get question type
			/$query = "SELECT c_type from #__lms_quiz_t_question WHERE c_id = '".$quest_id."'";
			$JLMS_DB->SetQuery( $query );
			$qtype = $JLMS_DB->LoadResult();*/
	
			/* insert results to the Database */
	
			$q_data = $QA->quiz_Get_QuestionList(); // 25 April 2007 (DEN) We need this var here (Early it was declared after 'switch')
	
			/* * * * * * * (TIP) 25 April 2007 (DEN)
			 * In $q_data array question_type NEVER will be 20 (pool question)
			 * because in function 'quiz_Get_QuestionList()' of 'JLMS_quiz_API' class
			 * we've changed 20 type to the actual type of pool question
			 */
	
			$is_quest_exists = false;
			$qtype = 0;
			$c_pool_quest = 0;
			
			foreach ($q_data as $qd) {
				if ($qd->c_id == $quest_id) {
					$is_quest_exists = true;
					$qtype = $qd->c_type;
					$c_pool_quest = $qd->c_pool;
					$quest_params = new JLMSParameters($qd->params);
					break;
				}
			}
			if (!$is_quest_exists) {
				return '';
			}
	
			/* 25 April 2007 (DEN)
			 * These vars are using for compatibility with Question Pool
			 * (We should get answer-data for pool question, but record to DB answers for current question)
			 *
			 * - If current question type is 20 (question is added from pool), then we should process answers
			 *   for question from pool; but id for answers we should use current
			 */
			$proc_qtype = $qtype;
			$proc_quest_id = $quest_id;
	
			//if ($qtype == 20) {
			if ($c_pool_quest) {
				/* 24 April 2007 (pool question)
				 * We must change vars $qtype and $quest_id to the actual vars (hmmm...? )
				 */
				/*$query = "SELECT * FROM #__lms_quiz_t_question WHERE c_id = $c_pool_quest";
				$JLMS_DB->setQuery($query);
				$pool_quest = $JLMS_DB->LoadObject();
				if (is_object($pool_quest)) {
					$proc_qtype = $pool_quest->c_type;//$qtype;
					$proc_quest_id = $pool_quest->c_id;//$quest_id;
				} else {
					return '';
				}*/
				$proc_quest_id = $c_pool_quest;
			}
			$is_correct = 0;
			$is_no_attempts = 0;
				switch ($proc_qtype) {
					case 1:
					case 3:
					case 12:
						if(isset($_REQUEST['quest_choice']) && $_REQUEST['quest_choice']){
							$answer = strval( mosGetParam( $_REQUEST, 'quest_choice', '' ) );
							$query = "SELECT a.c_point, b.c_id, a.c_attempts FROM #__lms_quiz_t_question as a, #__lms_quiz_t_choice as b WHERE a.c_id = '".$proc_quest_id."' and b.c_question_id = a.c_id and b.c_right = '1'";
							$JLMS_DB->SetQuery( $query );
							$ddd = $JLMS_DB->LoadObjectList();
							$c_quest_score = 0;
							$c_all_attempts = 1;
							$is_avail = 1;
							if (count($ddd)) {
								if ($ddd[0]->c_id == $answer) {
									$c_quest_score = $ddd[0]->c_point;
									$is_correct = 1;
								}
								if ($ddd[0]->c_attempts) {
									$c_all_attempts = $ddd[0]->c_attempts;
								}
							}
							$c_quest_cur_attempt = 0;
							$query = "SELECT c_id, c_attempts FROM #__lms_quiz_r_student_question WHERE c_stu_quiz_id = '".$stu_quiz_id."' and c_question_id = '".$quest_id."'";
							$JLMS_DB->SetQuery( $query );
							$c_tmp = $JLMS_DB->LoadObjectList();
							if (count($c_tmp)) {
								$c_quest_cur_attempt = $c_tmp[0]->c_attempts;
								if ($c_quest_cur_attempt >= $c_all_attempts) {
									$ret_str .= "\t" . '<task>no_attempts</task>' . "\n";
									$ret_str .= "\t" . '<quiz_menu><![CDATA['.JLMS_ShowToolbar($toolbar_no_a).']]></quiz_menu>' . "\n";
									$is_avail = 0;
									$is_no_attempts = 1;
								}
								if ($is_avail) {
									$query = "DELETE FROM #__lms_quiz_r_student_question WHERE c_stu_quiz_id = '".$stu_quiz_id."' and c_question_id = '".$quest_id."'";
									$JLMS_DB->SetQuery( $query );
									$JLMS_DB->query();
									$query = "DELETE FROM #__lms_quiz_r_student_choice WHERE c_sq_id = '".$c_tmp[0]->c_id."'";
									$JLMS_DB->SetQuery( $query );
									$JLMS_DB->query();
								}
							}
							if ($is_avail) {
								$correct = JLMS_quiz_ajax_class::correct_answer($is_correct);
								
								$query = "INSERT INTO #__lms_quiz_r_student_question (c_stu_quiz_id, c_question_id, c_score, c_attempts, c_correct)"
								. "\n VALUES('".$stu_quiz_id."', '".$quest_id."', '".$c_quest_score."', '".($c_quest_cur_attempt + 1)."', '".$correct."')";
								$JLMS_DB->SetQuery($query);
								$JLMS_DB->query();
								$c_sq_id = $JLMS_DB->insertid();
								
								$query = "INSERT INTO #__lms_quiz_r_student_choice (c_sq_id, c_choice_id)"
								. "\n VALUES('".$c_sq_id."', '".$answer."')";
								$JLMS_DB->SetQuery($query);
								$JLMS_DB->query();
							}
						} else {
							JLMSRedirect( sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=quiz_action&id=".$id."&quiz=".$quiz_id."&atask=goto_quest&seek_quest_id=".$quest_id."&quest_num=".$quiz_quest_num."&stu_quiz_id=".$stu_quiz_id."&user_unique_id=".$user_unique_id."") );
						}
					break;
					case 2:
					case 13:
						if(isset($_REQUEST['quest_choice']) && $_REQUEST['quest_choice']){
							$answer = implode(',', $_REQUEST['quest_choice']);
							$query = "SELECT a.c_point, a.c_attempts FROM #__lms_quiz_t_question as a WHERE a.c_id = '".$proc_quest_id."'";
							$JLMS_DB->SetQuery( $query );
							$ddd = $JLMS_DB->LoadObjectList();
							$query = "SELECT b.c_id FROM #__lms_quiz_t_question as a, #__lms_quiz_t_choice as b WHERE a.c_id = '".$proc_quest_id."' and b.c_question_id = a.c_id and b.c_right = '1'";
							$JLMS_DB->SetQuery( $query );
							$ddd2 = $JLMS_DB->LoadObjectList();
							$query = "SELECT b.c_id FROM #__lms_quiz_t_question as a, #__lms_quiz_t_choice as b WHERE a.c_id = '".$proc_quest_id."' and b.c_question_id = a.c_id and b.c_right <> '1'";
							$JLMS_DB->SetQuery( $query );
							$ddd3 = $JLMS_DB->LoadObjectList();
							$c_quest_score = 0;
							$c_all_attempts = 1;
							$is_avail = 1;
							$ans_array = explode(',',$answer);
							if (count($ddd2) && count($ddd)) {
								$c_quest_score = $ddd[0]->c_point;
								$is_correct = 1;
								foreach ($ddd2 as $right_row) {
									if (!in_array($right_row->c_id, $ans_array)) {
										$c_quest_score = 0;
										$is_correct = 0; }
								}
								foreach ($ddd3 as $not_right_row) {
									if (in_array($not_right_row->c_id, $ans_array)) {
										$c_quest_score = 0;
										$is_correct = 0; }
								}
								if ($ddd[0]->c_attempts) {
									$c_all_attempts = $ddd[0]->c_attempts; }
							}
							$c_quest_cur_attempt = 0;
							$query = "SELECT c_id, c_attempts FROM #__lms_quiz_r_student_question WHERE c_stu_quiz_id = '".$stu_quiz_id."' and c_question_id = '".$quest_id."'";
							$JLMS_DB->SetQuery( $query );
							$c_tmp = $JLMS_DB->LoadObjectList();
							if (count($c_tmp)) {
								$c_quest_cur_attempt = $c_tmp[0]->c_attempts;
								if ($c_quest_cur_attempt >= $c_all_attempts) {
									$ret_str .= "\t" . '<task>no_attempts</task>' . "\n";
									$ret_str .= "\t" . '<quiz_menu><![CDATA['.JLMS_ShowToolbar($toolbar_no_a).']]></quiz_menu>' . "\n";
									$is_avail = 0;
									$is_no_attempts = 1;
								}
								if ($is_avail) {
									$query = "DELETE FROM #__lms_quiz_r_student_question WHERE c_stu_quiz_id = '".$stu_quiz_id."' and c_question_id = '".$quest_id."'";
									$JLMS_DB->SetQuery( $query );
									$JLMS_DB->query();
									$query = "DELETE FROM #__lms_quiz_r_student_choice WHERE c_sq_id = '".$c_tmp[0]->c_id."'";
									$JLMS_DB->SetQuery( $query );
									$JLMS_DB->query();
								}
							}
							if ($is_avail) {
								$correct = JLMS_quiz_ajax_class::correct_answer($is_correct);
								$query = "INSERT INTO #__lms_quiz_r_student_question (c_stu_quiz_id, c_question_id, c_score, c_attempts, c_correct)"
								. "\n VALUES('".$stu_quiz_id."', '".$quest_id."', '".$c_quest_score."', '".($c_quest_cur_attempt + 1)."', '".$correct."')";
								$JLMS_DB->SetQuery($query);
								$JLMS_DB->query();
								$c_sq_id = $JLMS_DB->insertid();
								$i = 0;
								while ($i < count($ans_array)) {
									$query = "INSERT INTO #__lms_quiz_r_student_choice (c_sq_id, c_choice_id)"
									. "\n VALUES('".$c_sq_id."', '".$ans_array[$i]."')";
									$JLMS_DB->SetQuery($query);
									$JLMS_DB->query();
									$i ++;
								}
							}
						} else {
							JLMSRedirect( sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=quiz_action&id=".$id."&quiz=".$quiz_id."&atask=goto_quest&seek_quest_id=".$quest_id."&quest_num=".$quiz_quest_num."&stu_quiz_id=".$stu_quiz_id."&user_unique_id=".$user_unique_id."") );
						}
					break;
					case 4:
					case 5:
					case 11:
						$count_match = count($_REQUEST['quest_match']);
						$arr_error = 1;
						for($ji=0;$ji<$count_match;$ji++){
							if($_REQUEST['quest_match'][$ji] == '{0}'){
								$arr_error = 0;	
							}
						}
						
						if(isset($_REQUEST['quest_match']) && $_REQUEST['quest_match'] && $arr_error){
							$answer = implode('```', $_REQUEST['quest_match']);
							$query = "SELECT a.c_point, a.c_attempts FROM #__lms_quiz_t_question as a WHERE a.c_id = '".$proc_quest_id."'";
							$JLMS_DB->SetQuery( $query );
							$ddd = $JLMS_DB->LoadObjectList();
							$query = "SELECT b.c_id, b.c_left_text, b.c_right_text FROM #__lms_quiz_t_question as a, #__lms_quiz_t_matching as b WHERE a.c_id = '".$proc_quest_id."' and b.c_question_id = a.c_id ORDER BY b.ordering";
							$JLMS_DB->SetQuery( $query );
							$ddd2 = $JLMS_DB->LoadObjectList();
							$c_quest_score = 0;
							$c_all_attempts = 1;
							$is_avail = 1;
							$ans_array = explode('```',$answer);
							$ans_array_values = array();
							if (count($ddd2) && count($ddd)) {
								$c_quest_score = $ddd[0]->c_point;
								$is_correct = 1; $rr_num = 0;

								for ($di = 0, $dn = count($ddd2); $di < $dn; $di ++) {
									$ddd2[$di]->c_right_text_md5 = md5($ddd2[$di]->c_right_text);
								}
								foreach ($ans_array as $ans_array_one) {
									foreach ($ddd2 as $right_row) {
										if ($ans_array_one == $right_row->c_right_text_md5) {
											$ans_array_values[$ans_array_one] = $right_row->c_right_text;
											break;
										}
									}
								}

								foreach ($ddd2 as $right_row) {
									/**
									 * 01 November 2007 - DEN - bugfix - checking different character encodings
									 * I.e. if browser sent data in UTF, but DB collation is ISO
									 * or another case, if DB collation is UTF, but browser sent response in ISO.
									 * TODO: remove strings comparison.
									 * 		TIPS:	1. we can not compare by id's, because in this case PRO-user with JS debugger can cheat
									 * 				2. may create comparison of md5-hashes (send hashes to the html page instead of answer options)?
									 */
									if (trim($right_row->c_right_text_md5) != trim($ans_array[$rr_num])) {
										$c_quest_score = 0;
										$is_correct = 0;
									}
									$rr_num ++;
								}
								if ($ddd[0]->c_attempts) {
									$c_all_attempts = $ddd[0]->c_attempts; }
							}
							$c_quest_cur_attempt = 0;
							$query = "SELECT c_id, c_attempts FROM #__lms_quiz_r_student_question WHERE c_stu_quiz_id = '".$stu_quiz_id."' and c_question_id = '".$quest_id."'";
							$JLMS_DB->SetQuery( $query );
							$c_tmp = $JLMS_DB->LoadObjectList();
							if (count($c_tmp)) {
								$c_quest_cur_attempt = $c_tmp[0]->c_attempts;
								if ($c_quest_cur_attempt >= $c_all_attempts) {
									$ret_str .= "\t" . '<task>no_attempts</task>' . "\n";
									$ret_str .= "\t" . '<quiz_menu><![CDATA['.JLMS_ShowToolbar($toolbar_no_a).']]></quiz_menu>' . "\n";
									$is_avail = 0;
									$is_no_attempts = 1;
								}
								if ($is_avail) {
									$query = "DELETE FROM #__lms_quiz_r_student_question WHERE c_stu_quiz_id = '".$stu_quiz_id."' and c_question_id = '".$quest_id."'";
									$JLMS_DB->SetQuery( $query );
									$JLMS_DB->query();
									$query = "DELETE FROM #__lms_quiz_r_student_matching WHERE c_sq_id = '".$c_tmp[0]->c_id."'";
									$JLMS_DB->SetQuery( $query );
									$JLMS_DB->query();
								}
							}
							if ($is_avail) {
								$correct = JLMS_quiz_ajax_class::correct_answer($is_correct);
								$query = "INSERT INTO #__lms_quiz_r_student_question (c_stu_quiz_id, c_question_id, c_score, c_attempts, c_correct)"
								. "\n VALUES('".$stu_quiz_id."', '".$quest_id."', '".$c_quest_score."', '".($c_quest_cur_attempt + 1)."', '".$correct."')";
								$JLMS_DB->SetQuery($query);
								$JLMS_DB->query();
								$c_sq_id = $JLMS_DB->insertid();
								$i = 0;
								while ($i < count($ddd2)) {
									$cur_quest_answer_value = isset($ans_array_values[$ans_array[$i]]) ? $ans_array_values[$ans_array[$i]] : $ans_array[$i];
									$query = "INSERT INTO #__lms_quiz_r_student_matching (c_sq_id, c_matching_id, c_sel_text)"
									. "\n VALUES('".$c_sq_id."', '".$ddd2[$i]->c_id."', ".$JLMS_DB->Quote($cur_quest_answer_value).")";
									$JLMS_DB->SetQuery($query);
									$JLMS_DB->query();
									$i ++;
								}
							}
						} else {
							JLMSRedirect( sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=quiz_action&id=".$id."&quiz=".$quiz_id."&atask=goto_quest&seek_quest_id=".$quest_id."&quest_num=".$quiz_quest_num."&stu_quiz_id=".$stu_quiz_id."&user_unique_id=".$user_unique_id."") );
						}
					break;
					case 6:
						if(isset($_REQUEST['quest_blank']) && $_REQUEST['quest_blank'] !=''){
							$answer = strval( mosGetParam( $_REQUEST, 'quest_blank', '' ) );
							$query = "SELECT a.c_point, a.c_attempts FROM #__lms_quiz_t_question as a WHERE a.c_id = '".$proc_quest_id."'";
							$JLMS_DB->SetQuery( $query );
							$ddd = $JLMS_DB->LoadObjectList();
							$query = "SELECT c.c_text FROM #__lms_quiz_t_question as a, #__lms_quiz_t_blank as b, #__lms_quiz_t_text as c WHERE a.c_id = '".$proc_quest_id."' and b.c_question_id = a.c_id and c.c_blank_id = b.c_id";
							$JLMS_DB->SetQuery( $query );
							$ddd2 = $JLMS_DB->LoadObjectList();
							$c_quest_score = 0;
							$c_all_attempts = 1;
							$is_avail = 1;
							$answer = trim(urldecode($answer));
							if (count($ddd2) && count($ddd)) {
								/*foreach ($ddd2 as $right_row) {
									if($quest_params->get('case_sensivity', 0)){
										if ($right_row->c_text === $answer) {
											$c_quest_score = $ddd[0]->c_point;
											$is_correct = 1;
										}
									} else {	
										if (strtolower($right_row->c_text) === strtolower($answer)) {
											$c_quest_score = $ddd[0]->c_point;
											$is_correct = 1;
										}
									}
								}*/
								foreach ($ddd2 as $right_row) {
									if($quest_params->get('case_sensivity', 0)){
										if ($right_row->c_text === $answer) {
											$c_quest_score = $ddd[0]->c_point;
											$is_correct = 1;
										}
									} else {
										if (strtolower($right_row->c_text) === strtolower($answer)) {
											$c_quest_score = $ddd[0]->c_point;
											$is_correct = 1;
										}	
									}	
									if (!$is_correct) {
										/**
										 * 01 November 2007 - DEN - bugfix - checking different character encodings
										 * I.e. if browser sent data in UTF, but DB collation is ISO
										 * or another case, if DB collation is UTF, but browser sent response in ISO.
										 * TODO: code is not tested fully. - need testing with ISO (danish, german), cp and UTF
										 */
										if (function_exists('utf8_encode')) {
											$a_u = utf8_encode($right_row->c_text);
											$b_u = utf8_encode($answer);
										} else {
											$a_u = $right_row->c_text;
											$b_u = $answer;
										}
										if($quest_params->get('case_sensivity', 0)){
											if ($a_u === $answer) {
												$c_quest_score = $ddd[0]->c_point;
												$is_correct = 1;
											} else {
												if ($right_row->c_text === $b_u) {
													$c_quest_score = $ddd[0]->c_point;
													$is_correct = 1;
												}
											}
										} else {
											if (strtolower($a_u) === strtolower($answer)) {
												$c_quest_score = $ddd[0]->c_point;
												$is_correct = 1;
											} else {
												if (strtolower($right_row->c_text) === strtolower($b_u)) {
													$c_quest_score = $ddd[0]->c_point;
													$is_correct = 1;
												}
											}
										}
									}
								}
								if ($ddd[0]->c_attempts) {
									$c_all_attempts = $ddd[0]->c_attempts; }
							}
							$c_quest_cur_attempt = 0;
							$query = "SELECT c_id, c_attempts FROM #__lms_quiz_r_student_question WHERE c_stu_quiz_id = '".$stu_quiz_id."' and c_question_id = '".$quest_id."'";
							$JLMS_DB->SetQuery( $query );
							$c_tmp = $JLMS_DB->LoadObjectList();
							if (count($c_tmp)) {
								$c_quest_cur_attempt = $c_tmp[0]->c_attempts;
								if ($c_quest_cur_attempt >= $c_all_attempts) {
									$ret_str .= "\t" . '<task>no_attempts</task>' . "\n";
									$ret_str .= "\t" . '<quiz_menu><![CDATA['.JLMS_ShowToolbar($toolbar_no_a).']]></quiz_menu>' . "\n";
									$is_avail = 0;
									$is_no_attempts = 1;
								}
								if ($is_avail) {
									$query = "DELETE FROM #__lms_quiz_r_student_question WHERE c_stu_quiz_id = '".$stu_quiz_id."' and c_question_id = '".$quest_id."'";
									$JLMS_DB->SetQuery( $query );
									$JLMS_DB->query();
									$query = "DELETE FROM #__lms_quiz_r_student_blank WHERE c_sq_id = '".$c_tmp[0]->c_id."'";
									$JLMS_DB->SetQuery( $query );
									$JLMS_DB->query();
								}
							}
							if ($is_avail) {
								$correct = JLMS_quiz_ajax_class::correct_answer($is_correct);
								$query = "INSERT INTO #__lms_quiz_r_student_question (c_stu_quiz_id, c_question_id, c_score, c_attempts, c_correct)"
								. "\n VALUES('".$stu_quiz_id."', '".$quest_id."', '".$c_quest_score."', '".($c_quest_cur_attempt + 1)."', '".$correct."')";
								$JLMS_DB->SetQuery($query);
								$JLMS_DB->query();
								$c_sq_id = $JLMS_DB->insertid();
								$query = "INSERT INTO #__lms_quiz_r_student_blank (c_sq_id, c_answer)"
								. "\n VALUES('".$c_sq_id."', ". $JLMS_DB->Quote( $answer ) .")";
								$JLMS_DB->SetQuery($query);
								$JLMS_DB->query();
								
							}
						} else {
							JLMSRedirect( sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=quiz_action&id=".$id."&quiz=".$quiz_id."&atask=goto_quest&seek_quest_id=".$quest_id."&quest_num=".$quiz_quest_num."&stu_quiz_id=".$stu_quiz_id."&user_unique_id=".$user_unique_id."") );
						}
					break;
					case 7:
						$answer = $_REQUEST['hotspot_x'].','.$_REQUEST['hotspot_y'];
						$query = "SELECT a.c_point, a.c_attempts, b.c_start_x, b.c_start_y, b.c_width, b.c_height FROM #__lms_quiz_t_question as a, #__lms_quiz_t_hotspot as b WHERE a.c_id = '".$proc_quest_id."' and b.c_question_id = a.c_id";
						$JLMS_DB->SetQuery( $query );
						$ddd = $JLMS_DB->LoadObjectList();
						$c_quest_score = 0;
						$c_all_attempts = 1;
						$is_avail = 1;
						if (count($ddd)) {
							$ans_array = explode(',',$answer);
							if ((count($ans_array) == 2) && ($ans_array[0] >= $ddd[0]->c_start_x) && ($ans_array[0] <= ($ddd[0]->c_start_x+$ddd[0]->c_width)) && ($ans_array[1] >= $ddd[0]->c_start_y) && ($ans_array[1] <= ($ddd[0]->c_start_y+$ddd[0]->c_height)) ) {
								$is_correct = 1;
								$c_quest_score = $ddd[0]->c_point;
							}
							if ($ddd[0]->c_attempts) {
								$c_all_attempts = $ddd[0]->c_attempts; }
						}
						$c_quest_cur_attempt = 0;
						$query = "SELECT c_id, c_attempts FROM #__lms_quiz_r_student_question WHERE c_stu_quiz_id = '".$stu_quiz_id."' and c_question_id = '".$quest_id."'";
						$JLMS_DB->SetQuery( $query );
						$c_tmp = $JLMS_DB->LoadObjectList();
						if (count($c_tmp)) {
							$c_quest_cur_attempt = $c_tmp[0]->c_attempts;
							if ($c_quest_cur_attempt >= $c_all_attempts) {
								$ret_str .= "\t" . '<task>no_attempts</task>' . "\n";
								$ret_str .= "\t" . '<quiz_menu><![CDATA['.JLMS_ShowToolbar($toolbar_no_a).']]></quiz_menu>' . "\n";
								$is_avail = 0;
								$is_no_attempts = 1;
							}
							if ($is_avail) {
								$query = "DELETE FROM #__lms_quiz_r_student_question WHERE c_stu_quiz_id = '".$stu_quiz_id."' and c_question_id = '".$quest_id."'";
								$JLMS_DB->SetQuery( $query );
								$JLMS_DB->query();
								$query = "DELETE FROM #__lms_quiz_r_student_hotspot WHERE c_sq_id = '".$c_tmp[0]->c_id."'";
								$JLMS_DB->SetQuery( $query );
								$JLMS_DB->query();
							}
						}
						if ($is_avail) {
							$correct = JLMS_quiz_ajax_class::correct_answer($is_correct);
							$query = "INSERT INTO #__lms_quiz_r_student_question (c_stu_quiz_id, c_question_id, c_score, c_attempts, c_correct)"
							. "\n VALUES('".$stu_quiz_id."', '".$quest_id."', '".$c_quest_score."', '".($c_quest_cur_attempt + 1)."', '".$correct."')";
							$JLMS_DB->SetQuery($query);
							$JLMS_DB->query();
							$c_sq_id = $JLMS_DB->insertid();
							$query = "INSERT INTO #__lms_quiz_r_student_hotspot (c_sq_id, c_select_x, c_select_y)"
							. "\n VALUES('".$c_sq_id."', '".(isset($ans_array[0])?$ans_array[0]:0)."', '".(isset($ans_array[1])?$ans_array[1]:0)."')";
							$JLMS_DB->SetQuery($query);
							$JLMS_DB->query();
						}
					break;
					case 8:
						if(isset($_REQUEST['survey_box']) && $_REQUEST['survey_box'] != ''){
							$answer = strval( mosGetParam( $_REQUEST, 'survey_box', '' ) );
							$query = "SELECT a.c_point, a.c_attempts FROM #__lms_quiz_t_question as a WHERE a.c_id = '".$proc_quest_id."'";
							$JLMS_DB->SetQuery( $query );
							$ddd = $JLMS_DB->LoadObjectList();
							$c_quest_score = 0;
							$c_all_attempts = 1;
							$is_avail = 1;
							$answer = trim(urldecode($answer));
							if (count($ddd)) {
								if ($answer) {
									$is_correct = 1;
									$c_quest_score = $ddd[0]->c_point;
								}
								if ($ddd[0]->c_attempts) {
									$c_all_attempts = $ddd[0]->c_attempts; }
							}
							$c_quest_cur_attempt = 0;
							$query = "SELECT c_id, c_attempts FROM #__lms_quiz_r_student_question WHERE c_stu_quiz_id = '".$stu_quiz_id."' and c_question_id = '".$quest_id."'";
							$JLMS_DB->SetQuery( $query );
							$c_tmp = $JLMS_DB->LoadObjectList();
							if (count($c_tmp)) {
								$c_quest_cur_attempt = $c_tmp[0]->c_attempts;
								if ($c_quest_cur_attempt >= $c_all_attempts) {
									$ret_str .= "\t" . '<task>no_attempts</task>' . "\n";
									$ret_str .= "\t" . '<quiz_menu><![CDATA['.JLMS_ShowToolbar($toolbar_no_a).']]></quiz_menu>' . "\n";
									$is_avail = 0;
									$is_no_attempts = 1;
								}
								if ($is_avail) {
									$query = "DELETE FROM #__lms_quiz_r_student_question WHERE c_stu_quiz_id = '".$stu_quiz_id."' and c_question_id = '".$quest_id."'";
									$JLMS_DB->SetQuery( $query );
									$JLMS_DB->query();
									$query = "DELETE FROM #__lms_quiz_r_student_survey WHERE c_sq_id = '".$c_tmp[0]->c_id."'";
									$JLMS_DB->SetQuery( $query );
									$JLMS_DB->query();
								}
							}
							if ($is_avail) {
								$correct = JLMS_quiz_ajax_class::correct_answer($is_correct);
								$query = "INSERT INTO #__lms_quiz_r_student_question (c_stu_quiz_id, c_question_id, c_score, c_attempts, c_correct)"
								. "\n VALUES('".$stu_quiz_id."', '".$quest_id."', '".$c_quest_score."', '".($c_quest_cur_attempt + 1)."', '".$correct."')";
								$JLMS_DB->SetQuery($query);
								$JLMS_DB->query();
								$c_sq_id = $JLMS_DB->insertid();
								$query = "INSERT INTO #__lms_quiz_r_student_survey (c_sq_id, c_answer)"
								. "\n VALUES('".$c_sq_id."', ". $JLMS_DB->Quote( $answer ) .")";
								$JLMS_DB->SetQuery($query);
								$JLMS_DB->query();
							}
						} else {
							JLMSRedirect( sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=quiz_action&id=".$id."&quiz=".$quiz_id."&atask=goto_quest&seek_quest_id=".$quest_id."&quest_num=".$quiz_quest_num."&stu_quiz_id=".$stu_quiz_id."&user_unique_id=".$user_unique_id."") );
						}
					break;
					case 9:
						for($ji=0;$ji<$_REQUEST['scale_count'];$ji++){
							if(isset($_REQUEST['ch_scale_'.$ji]) && $_REQUEST['ch_scale_'.$ji]){
								$arr[] = $_REQUEST['ch_scale_'.$ji];
							}
						}
						$is_correct = 1;
						if(count($arr) == $_REQUEST['scale_count']){
							$answer = implode(",", $arr);
							$query = "SELECT a.c_point, a.c_attempts FROM #__lms_quiz_t_question as a WHERE a.c_id = '".$proc_quest_id."'";
							$JLMS_DB->SetQuery( $query );
							$ddd = $JLMS_DB->LoadObjectList();
							
							$c_quest_score = 0;
							$c_all_attempts = 1;
							$is_avail = 1;
		
							if ($ddd[0]->c_attempts) {
								$c_all_attempts = $ddd[0]->c_attempts; }
							
							$c_quest_cur_attempt = 0;
							$query = "SELECT c_id, c_attempts FROM #__lms_quiz_r_student_question WHERE c_stu_quiz_id = '".$stu_quiz_id."' and c_question_id = '".$quest_id."'";
							$JLMS_DB->SetQuery( $query );
							$c_tmp = $JLMS_DB->LoadObjectList();
							if (count($c_tmp)) {
								$c_quest_cur_attempt = $c_tmp[0]->c_attempts;
								if ($c_quest_cur_attempt >= $c_all_attempts) {
									$ret_str .= "\t" . '<task>no_attempts</task>' . "\n";
									$ret_str .= "\t" . '<quiz_menu><![CDATA['.JLMS_ShowToolbar($toolbar_no_a).']]></quiz_menu>' . "\n";
									$is_avail = 0;
									$is_no_attempts = 1;
								}
								if ($is_avail) {
									$query = "DELETE FROM #__lms_quiz_r_student_question WHERE c_stu_quiz_id = '".$stu_quiz_id."' and c_question_id = '".$quest_id."'";
									$JLMS_DB->SetQuery( $query );
									$JLMS_DB->query();
									$query = "DELETE FROM #__lms_quiz_r_student_scale WHERE c_sq_id = '".$c_tmp[0]->c_id."'";
									$JLMS_DB->SetQuery( $query );
									$JLMS_DB->query();
								}
							}
							
							if ($is_avail) {
								$correct = JLMS_quiz_ajax_class::correct_answer($is_correct);
								$query = "INSERT INTO #__lms_quiz_r_student_question (c_stu_quiz_id, c_question_id, c_score, c_attempts, c_correct)"
								. "\n VALUES('".$stu_quiz_id."', '".$quest_id."', '".$c_quest_score."', '".($c_quest_cur_attempt + 1)."', '".$correct."')";
								$JLMS_DB->SetQuery($query);
								$JLMS_DB->query();
								$c_sq_id = $JLMS_DB->insertid();
								$query = "SELECT * FROM #__lms_quiz_t_scale WHERE c_question_id = '".$quest_id."' AND c_type!='1'  ORDER BY ordering";
								$JLMS_DB->SetQuery( $query );
								$c_scal = $JLMS_DB->LoadObjectList();
								
								$ans_array = explode(',',$answer);
								for($p=0;$p<count($ans_array);$p++)
								{
									$query = "INSERT INTO #__lms_quiz_r_student_scale (c_sq_id, q_scale_id, scale_id)"
									. "\n VALUES('".$c_sq_id."', '". $c_scal[$p]->c_id ."', '". $ans_array[$p] ."')";
									$JLMS_DB->SetQuery($query);
									$JLMS_DB->query();
								}
								
							}
						} else {
							JLMSRedirect( sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=quiz_action&id=".$id."&quiz=".$quiz_id."&atask=goto_quest&seek_quest_id=".$quest_id."&quest_num=".$quiz_quest_num."&stu_quiz_id=".$stu_quiz_id."&user_unique_id=".$user_unique_id."") );
						}
					break;
					case 10:
						$query = "SELECT a.c_attempts FROM #__lms_quiz_t_question as a WHERE a.c_id = '".$proc_quest_id."'";
						$JLMS_DB->SetQuery( $query );
						$ddd = $JLMS_DB->LoadObjectList();
						$c_quest_score = 0;
						$c_all_attempts = 1;
						$is_avail = 1;
						$is_correct = 1;
						$answer = trim(urldecode($answer));
						if (count($ddd)) {
							if ($ddd[0]->c_attempts) {
								$c_all_attempts = $ddd[0]->c_attempts; }
						}
						$c_quest_cur_attempt = 0;
						$query = "SELECT c_id, c_attempts FROM #__lms_quiz_r_student_question WHERE c_stu_quiz_id = '".$stu_quiz_id."' and c_question_id = '".$quest_id."'";
						$JLMS_DB->SetQuery( $query );
						$c_tmp = $JLMS_DB->LoadObjectList();
						if (count($c_tmp)) {
							$c_quest_cur_attempt = $c_tmp[0]->c_attempts;
							if ($c_quest_cur_attempt >= $c_all_attempts) {
								$ret_str .= "\t" . '<task>no_attempts</task>' . "\n";
								$is_avail = 0;
								$is_no_attempts = 1;
							}
							if ($is_avail) {
								$query = "DELETE FROM #__lms_quiz_r_student_question WHERE c_stu_quiz_id = '".$stu_quiz_id."' and c_question_id = '".$quest_id."'";
								$JLMS_DB->SetQuery( $query );
								$JLMS_DB->query();
							}
						}
						if ($is_avail) {
							$correct = JLMS_quiz_ajax_class::correct_answer($is_correct);
							$query = "INSERT INTO #__lms_quiz_r_student_question (c_stu_quiz_id, c_question_id, c_score, c_attempts, c_correct)"
							. "\n VALUES('".$stu_quiz_id."', '".$quest_id."', '".$c_quest_score."', '".($c_quest_cur_attempt + 1)."', '".$correct."')";
							$JLMS_DB->SetQuery($query);
							$JLMS_DB->query();
						}
					break;
				}
				
				
				$j = -1;
				$quest_num = 1;
//				$quest_num = intval( mosGetParam( $_REQUEST, 'quiz_quest_num', 1 ) );
				
				
				/*function to show feedback for the answer to question*/
//				JLMS_quiz_ajax_class::feedback($is_correct, $qtype, $quiz, $quest_id);
				/* 24 April 2007 (DEN)
				 * Get next question from all quiz quests
				 */
	/*now it will the function of showing next question */
				$query = "SELECT c_question_id FROM #__lms_quiz_r_student_question WHERE c_stu_quiz_id = '".$stu_quiz_id."'";
				$JLMS_DB->SetQuery( $query );
				$q_ids = $JLMS_DB->LoadResultArray();
				$q_num = 0;
				$q_num_ar = 0;
				$qqn = 0;
				foreach ($q_data as $qd) {
					if ($qd->c_id == $quest_id) {
						$q_num = $qqn + 1;
						$q_num_ar = $qqn;
						break;
					}
					$qqn ++;
				}
				if (!$q_num) {
					return '';
				} else {
					$q_num = 0;
					$q_num_ar = 0;
					// here we are using $q_num and $q_num_ar for other needs (don't warry :) )
					// find next not answered question
					for ($i = $qqn, $n = count($q_data); $i < $n; $i ++ ) {
						if (!in_array($q_data[$i]->c_id, $q_ids) && $q_data[$i]->c_id != $quest_id ) {
							$q_num = $i + 1;
							$q_num_ar = $i;
							break;
						}
					}
					if (!$q_num) {
						// find not answered question from prev questions
						for ($i = 0; $i < $qqn; $i ++ ) {
							if (!in_array($q_data[$i]->c_id, $q_ids) && $q_data[$i]->c_id != $quest_id ) {
								$q_num = $i + 1;
								$q_num_ar = $i;
								break;
							}
						}
					}
					if ($q_num) {
						$quest_num = $q_num;
						$j = $q_num_ar;
					}
				}
				//
				
//				echo $j;
//			echo 'q_num='.$q_num;
				$is_avail = 1;
				if (($c_quest_cur_attempt + 1) >= $c_all_attempts) { $is_avail = 0; }
	
				//require( dirname(__FILE__) . "/language/english.php");
				// preloading QUIZ languge (28.02.2007 new method) (all quizzes messages now in global quiz language)
				global $JLMS_LANGUAGE, $JLMS_CONFIG;
				JLMS_require_lang($JLMS_LANGUAGE, 'quiz.lang', $JLMS_CONFIG->get('default_language'));
				//require(_JOOMLMS_FRONT_HOME . "/languages/".$JLMS_CONFIG->get('default_language').'/quiz.lang.php');
				require(_JOOMLMS_FRONT_HOME . '/includes/quiz/quiz_language.php');
				global $jq_language;
				
				
				if ($quiz->c_wrong_message) $jq_language['quiz_answer_incorrect'] = htmlspecialchars(nl2br($quiz->c_wrong_message));
				if ($quiz->c_right_message) $jq_language['quiz_answer_correct'] = htmlspecialchars(nl2br($quiz->c_right_message));
				//$q_t_params = $QA->get_qvar('params');
				if ($quiz_params->get('disable_quest_feedback') || $quest_params->get('disable_quest_feedback')) {
					
				} else {
					$query = "SELECT * FROM #__lms_quiz_t_question_fb WHERE quest_id = $quest_id";
					$JLMS_DB->SetQuery($query);
					$q_fbs = $JLMS_DB->LoadObjectList();
					foreach ($q_fbs as $qfb) {
						if ($qfb->choice_id == -1) {
							if ($qfb->fb_text) {
								$jq_language['quiz_answer_incorrect'] = $qfb->fb_text;
							}
						} elseif(!$qfb->choice_id) {
							if ($qfb->fb_text) {
								$jq_language['quiz_answer_correct'] = $qfb->fb_text;
							}
						}
					}
				}
				
//				echo '<pre>';
//				print_r($q_data);
//				print_r($jq_language);
//				echo '</pre>';

				$query = "SELECT a.*, b.lpath_id FROM #__lms_learn_path_step_quiz_results as a, #__lms_learn_path_steps as b WHERE a.stu_quiz_id = '".$stu_quiz_id."' AND a.step_id = b.id";
				$JLMS_DB->setQuery($query);
				$this_lpath = $JLMS_DB->LoadObject();
				
				$cur_tmpl = 'joomlaquiz_lms_template';
				if ($cur_tmpl) {
					require_once(dirname(__FILE__) . '/templates/'.$cur_tmpl.'/jq_template.php');
					if (isset($q_data[$j])) {
						$atask = 'next_load';
						if ($quiz_params->get('disable_quest_feedback') || $quest_params->get('disable_quest_feedback') || $quest_params->get('survey_question') || $qtype == 10) {
							$msg_html = '';
						} else {
							$msg_html = JoomlaQuiz_template_class::JQ_show_messagebox('', (($is_correct)?(($qtype == 8)?$jq_language['quiz_answer_accepted']:$jq_language['quiz_answer_correct']):$jq_language['quiz_answer_incorrect']), $is_correct);
						}
						if ($is_no_attempts == 1) {
							$msg_html = JoomlaQuiz_template_class::JQ_show_messagebox('', $jq_language['mes_no_attempts'], 2);
						}
						
						$toolbar = array();
						if(isset($this_lpath->stu_quiz_id) && $this_lpath->stu_quiz_id == $stu_quiz_id){
							if(isset($quiz->c_slide) && $quiz->c_slide){
								$toolbar[] = array('kol_quests' => $kol_quests, 'num_quest' => $quest_num, 'quest_score' => $quest_score, 'img_btn'=>'btn_start.png', 'text_btn'=>_JLMS_NEXT_ALT_TITLE, 'link_cont'=>'&task=show_lpath_nojs&action=contents_lpath&course_id='.$id.'&id='.$this_lpath->lpath_id.'&stu_quiz_id='.$stu_quiz_id.'&user_unique_id='.$this_lpath->unique_id.'&step_id='.$this_lpath->step_id.'&user_start_id='.$this_lpath->start_id.'&quest_id='.$quest_id.'&quiz_id='.$quiz_id.'&stu_quiz_id='.$stu_quiz_id.'');
							} else {
								$toolbar[] = array('kol_quests' => $kol_quests, 'num_quest' => $quest_num, 'quest_score' => $quest_score, 'img_btn'=>'btn_start.png', 'text_btn'=>_JLMS_NEXT_ALT_TITLE);	
							}
						} else {
							if(isset($quiz->c_slide) && $quiz->c_slide){
								$toolbar[] = array('kol_quests' => $kol_quests, 'num_quest' => $quest_num, 'quest_score' => $quest_score, 'img_btn'=>'btn_start.png', 'text_btn'=>_JLMS_NEXT_ALT_TITLE, 'link_cont'=>'&task=quiz_action&atask=contents&id='.$id.'&quiz='.$quiz_id.'&stu_quiz_id='.$stu_quiz_id.'&user_unique_id='.$user_unique_id.'&quest_id='.$quest_id.'');
							} else {
								$toolbar[] = array('kol_quests' => $kol_quests, 'num_quest' => $quest_num, 'quest_score' => $quest_score, 'img_btn'=>'btn_start.png', 'text_btn'=>_JLMS_NEXT_ALT_TITLE);
							}
						}
						
						if ($quiz_params->get('disable_quest_feedback') || $quest_params->get('disable_quest_feedback') || $quest_params->get('survey_question') || $qtype == 10) {
							JLMSRedirect( sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=quiz_action&id=$id&quiz=$quiz_id&quest_id=$quest_id&stu_quiz_id=$stu_quiz_id&user_unique_id=$user_unique_id&atask=$atask") );
						} else {
							JLMS_quiz_ajax_class::JQ_feedback_nojs($atask, $id, $quiz_id, $msg_html, $toolbar, $stu_quiz_id, $user_unique_id, $quest_id);
						}
					} else {
						$atask = 'finish_stop';
						if ($quiz_params->get('disable_quest_feedback') || $quest_params->get('disable_quest_feedback') || $quest_params->get('survey_question') || $qtype == 10) {
							$msg_html = ' ';
						} else {
							$msg_html = JoomlaQuiz_template_class::JQ_show_messagebox('', (($is_correct)?(($qtype == 8)?$jq_language['quiz_answer_accepted']:$jq_language['quiz_answer_correct']):$jq_language['quiz_answer_incorrect']), $is_correct);
						}
						if ($is_no_attempts == 1) {
							$msg_html = JoomlaQuiz_template_class::JQ_show_messagebox('', $jq_language['mes_no_attempts'], 2);
						}
						
						$toolbar = array();
						if(isset($this_lpath->stu_quiz_id) && $this_lpath->stu_quiz_id == $stu_quiz_id){
							if(isset($quiz->c_slide) && $quiz->c_slide){	
								$toolbar[] = array('kol_quests' => $kol_quests, 'num_quest' => $quest_num, 'quest_score' => $quest_score, 'img_btn'=>'btn_start.png', 'text_btn'=>_JLMS_NEXT_ALT_TITLE, 'link_cont'=>'&task=show_lpath_nojs&action=contents_lpath&course_id='.$id.'&id='.$this_lpath->lpath_id.'&stu_quiz_id='.$stu_quiz_id.'&user_unique_id='.$this_lpath->unique_id.'&step_id='.$this_lpath->step_id.'&user_start_id='.$this_lpath->start_id.'&quest_id='.$quest_id.'&quiz_id='.$quiz_id.'&stu_quiz_id='.$stu_quiz_id.'');
							} else {
								$toolbar[] = array('kol_quests' => $kol_quests, 'num_quest' => $quest_num, 'quest_score' => $quest_score, 'img_btn'=>'btn_start.png', 'text_btn'=>_JLMS_NEXT_ALT_TITLE);	
							}
						} else {
							if(isset($quiz->c_slide) && $quiz->c_slide){	
								$toolbar[] = array('kol_quests' => $kol_quests, 'num_quest' => $quest_num, 'quest_score' => $quest_score, 'img_btn'=>'btn_start.png', 'text_btn'=>_JLMS_NEXT_ALT_TITLE, 'link_cont'=>'&task=quiz_action&atask=contents&id='.$id.'&quiz='.$quiz_id.'&stu_quiz_id='.$stu_quiz_id.'&user_unique_id='.$user_unique_id.'&quest_id='.$quest_id.'');
							} else {
								$toolbar[] = array('kol_quests' => $kol_quests, 'num_quest' => $quest_num, 'quest_score' => $quest_score, 'img_btn'=>'btn_start.png', 'text_btn'=>_JLMS_NEXT_ALT_TITLE);
							}
						}
						
						if ($quiz_params->get('disable_quest_feedback') || $quest_params->get('disable_quest_feedback') || $quest_params->get('survey_question') || $qtype == 10) {
							JLMSRedirect( sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=quiz_action&id=$id&quiz=$quiz_id&quest_id=$quest_id&stu_quiz_id=$stu_quiz_id&user_unique_id=$user_unique_id&atask=$atask") );
						} else {
							JLMS_quiz_ajax_class::JQ_feedback_nojs($atask, $id, $quiz_id, $msg_html, $toolbar, $stu_quiz_id, $user_unique_id, $quest_id);
						}
						
						$query = "SELECT sum(c_score) FROM #__lms_quiz_r_student_question WHERE c_stu_quiz_id = '".$stu_quiz_id."'";
						$JLMS_DB->SetQuery( $query );
						$q_total_score = $JLMS_DB->LoadResult();
	
						$query = "SELECT c_date_time FROM #__lms_quiz_r_student_quiz WHERE c_id = '".$stu_quiz_id."'";
						$JLMS_DB->SetQuery( $query );
						$q_beg_time = $JLMS_DB->LoadResult();
	
						$q_time_total = time() - date('Z') - strtotime($q_beg_time);
	
						$query = "UPDATE #__lms_quiz_r_student_quiz SET c_total_score = '".$q_total_score."', c_total_time = '".$q_time_total."' WHERE c_id = '".$stu_quiz_id."'";
						$JLMS_DB->SetQuery($query);
						$JLMS_DB->query();	
					}
				}
		}
		
//		echo $ret_str;
//		return $ret_str;
	}
	
	function JQ_FinishQuiz_nojs() {
		global $JLMS_DB, $my, $Itemid, $option, $Itemid;
		
		$ret_str = '';
		$quiz_id = intval( mosGetParam( $_REQUEST, 'quiz', 0 ) );
		$id = intval( mosGetParam( $_REQUEST, 'id', 0 ) );
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
//		$toolbar_no_a = $QA->quiz_Get_NoAtToolbar();
	
		$stu_quiz_id = intval( mosGetParam( $_REQUEST, 'stu_quiz_id', 0 ) );
		$user_unique_id = strval( mosGetParam( $_REQUEST, 'user_unique_id', '') );
	
		$QA->set('stu_quiz_id', $stu_quiz_id);
		$QA->set('user_unique_id', $user_unique_id);
		$QA->quiz_ProcessStartData();
		
		if ( $QA->start_valid() && $quiz_id ) {
	
			// temporary fo compatibility
			// (25 April 2007 commented) $quiz = $QA->quiz_data;
	
			//print_r($stu_quiz_id);
			$query = "SELECT SUM(c_score) FROM #__lms_quiz_r_student_question WHERE c_stu_quiz_id = '".$stu_quiz_id."'";
			$JLMS_DB->SetQuery( $query );
			$user_score = $JLMS_DB->LoadResult();
			if (!$user_score) $user_score = 0;
	
			/*$query = "SELECT SUM(c_point) FROM #__lms_quiz_t_question WHERE c_quiz_id = '".$quiz_id."'";
			$JLMS_DB->SetQuery( $query );
			$max_score = $JLMS_DB->LoadResult();*/
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
	
			$query = "UPDATE #__lms_quiz_r_student_quiz SET c_total_score = '".$user_score."', c_passed = '".$user_passed."', c_total_time = '".$user_time."'"
			. "\n WHERE c_id = '".$stu_quiz_id."' and c_quiz_id = '".$quiz_id."' and c_student_id = '".$my->id."'";
			$JLMS_DB->SetQuery( $query );
			$JLMS_DB->query();
	
			// update lms results
			$lms_course = $QA->get_qvar('course_id', 0);
			$lms_quiz = $quiz_id;
			$lms_user = $my->id;
			$lms_score = $user_score;
			$lms_time = $user_time;
			$lms_date = date( 'Y-m-d H:i:s', time() - date('Z') );
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

					if ($lms_passed) {
						$db = & JFactory::getDbo();
						$_JLMS_PLUGINS = & JLMSFactory::getPlugins();
						//*** send email notifications
						$JLMS_CONFIG = & JLMSFactory::getConfig();
						$Itemid = $JLMS_CONFIG->get('Itemid');
						$e_course = new stdClass();
						$e_course->course_alias = '';
						$e_course->course_name = '';			

						$query = "SELECT course_name, name_alias FROM #__lms_courses WHERE id = '".$lms_course."'";
						$db->setQuery( $query );
						$e_course = $db->loadObject();

						$query = "SELECT c_title FROM #__lms_quiz_t_quiz WHERE c_id = '".$lms_quiz."'";
						$db->setQuery( $query );
						$e_quiz_name = $db->loadResult();

						$e_user = new stdClass();
						$e_user->name = '';
						$e_user->email = '';
						$e_user->username = '';

						$query = "SELECT email, name, username FROM #__users WHERE id = '".$lms_user."'";
						$db->setQuery( $query );
						$e_user = $db->loadObject();

						$e_params['user_id'] = $lms_user;
						$e_params['course_id'] = $lms_course;					
						$e_params['markers']['{email}'] = $e_user->email;	
						$e_params['markers']['{name}'] = $e_user->name;										
						$e_params['markers']['{username}'] = $e_user->username;
						$e_params['markers']['{coursename}'] = $e_course->course_name;
						$e_params['markers']['{quizname}'] = $e_quiz_name;

						$e_params['markers']['{courselink}'] = JLMSEmailRoute("index.php?option=com_joomla_lms&Itemid=$Itemid&task=details_course&id=$lms_course");
						$e_params['markers_nohtml']['{courselink}'] = $e_params['markers']['{courselink}'];
						$e_params['markers']['{courselink}'] = '<a href="'.$e_params['markers']['{courselink}'].'">'.$e_params['markers']['{courselink}'].'</a>';

						$e_params['markers']['{lmslink}'] = JLMSEmailRoute("index.php?option=com_joomla_lms&Itemid=$Itemid");
						$e_params['markers_nohtml']['{lmslink}'] = $e_params['markers']['{lmslink}'];
						$e_params['markers']['{lmslink}'] = '<a href="'.$e_params['markers']['{lmslink}'].'">'.$e_params['markers']['{lmslink}'].'</a>';

						$e_params['action_name'] = 'OnQuizCompletion';

						$_JLMS_PLUGINS->loadBotGroup('emails');
						$plugin_result_array = $_JLMS_PLUGINS->trigger('OnQuizCompletion', array (& $e_params));
						//*** end of emails
					}
				}
			}
			// end of lms results section
		}
		//redirect
		JLMSRedirect( sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=quiz_action&id=$id&quiz=$quiz_id&stu_quiz_id=$stu_quiz_id&user_unique_id=$user_unique_id&atask=review_stop") );
	}
	///////
	
	function JQ_ResultsQuiz_nojs() {
		global $JLMS_DB, $my, $Itemid, $option, $Itemid, $JLMS_CONFIG;
		
		$doc = & JFactory::getDocument();
		
		$ret_str = '';
		$quiz_id = intval( mosGetParam( $_REQUEST, 'quiz', 0 ) );
		$id = intval( mosGetParam( $_REQUEST, 'id', 0 ) );
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
//		$toolbar_no_a = $QA->quiz_Get_NoAtToolbar();
		
	
		$stu_quiz_id = intval( mosGetParam( $_REQUEST, 'stu_quiz_id', 0 ) );
		$user_unique_id = strval( mosGetParam( $_REQUEST, 'user_unique_id', '') );
	
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
			
			$query = "SELECT c_total_score, c_passed, c_total_time FROM #__lms_quiz_r_student_quiz WHERE c_id = '".$stu_quiz_id."' and c_quiz_id = '".$quiz_id."' and c_student_id = '".$my->id."'";
			$JLMS_DB->setQuery($query);
			$results_data = $JLMS_DB->LoadObjectList();
			
			$lms_course = $QA->get_qvar('course_id', 0);
			$lms_quiz = $quiz_id;
			$lms_user = $my->id;
			$lms_score = $user_score;
			$lms_time = $user_time = $results_data[0]->c_total_time;
			$lms_date = date( 'Y-m-d H:i:s', time() - date('Z') );
			$lms_passed = $user_passed = $results_data[0]->c_passed;
			
			$cur_tmpl = 'joomlaquiz_lms_template';
			if ($cur_tmpl) {

				require_once(dirname(__FILE__) .'/templates/'.$cur_tmpl.'/jq_template.php');

				global $JLMS_LANGUAGE, $JLMS_CONFIG;
				JLMS_require_lang($JLMS_LANGUAGE, 'quiz.lang', $JLMS_CONFIG->get('default_language'));

				require(_JOOMLMS_FRONT_HOME . '/includes/quiz/quiz_language.php');
				global $jq_language;
	
//				$ret_str .= "\t" . '<task>results</task>' . "\n";
				$eee = $jq_language['quiz_header_fin_message'];				
				
				$doc->addStyleSheet( $JLMS_CONFIG->get('live_site').'/components/com_joomla_lms/includes/quiz/templates/joomlaquiz_lms_template/jq_template.css' );
				
				if($QA->lpath_stu_quiz_id == $stu_quiz_id){
					
					$query = "SELECT lpath_id FROM #__lms_learn_path_steps WHERE id = '".$QA->lpath_step_id."'";
					$JLMS_DB->setQuery($query);
					$lpath_id = $JLMS_DB->loadResult();
					
					$link_pre = sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=show_lpath_nojs&id=$lpath_id&course_id=$id&step_id=".$QA->lpath_step_id."&user_start_id=".$QA->lpath_start_id."&user_unique_id=".$QA->lpath_unique_id."&action=prev_lpathstep");
					$link_next = sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=show_lpath_nojs&id=$lpath_id&course_id=$id&step_id=".$QA->lpath_step_id."&user_start_id=".$QA->lpath_start_id."&user_unique_id=".$QA->lpath_unique_id."&action=next_lpathstep");
					$link_contents = sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=show_lpath_nojs&id=$lpath_id&course_id=$id&step_id=".$QA->lpath_step_id."&user_start_id=".$QA->lpath_start_id."&user_unique_id=".$QA->lpath_unique_id."&action=contents_lpath");
					
					$ret_str .= "<table width='100%' cellpadding='0' cellspacing='3' border='0' class='jlms_table_no_borders'>
					<tr>
						<td width='150'>
							&nbsp;
						</td>
						<td width='100%'>
						</td>
						<td width='150' align='right'>
							<table cellpadding='0' cellspacing='3' border='0' class='jlms_table_no_borders'>
								<tr>
									<td>
										<a href='".$link_pre."' title='"._JLMS_PREV_ALT_TITLE."'>
										<img src='".$JLMS_CONFIG->get('live_site')."/components/com_joomla_lms/lms_images/buttons/btn_back.png' border='0' alt='"._JLMS_PREV_ALT_TITLE."'/>
										</a>
									</td>
									<td>
										<a href='".$link_pre."' title='"._JLMS_PREV_ALT_TITLE."'>
										"._JLMS_PREV_ALT_TITLE."
										</a>
									</td>
									<td>
										<a href='".$link_next."' title='"._JLMS_NEXT_ALT_TITLE."'>
										<img src='".$JLMS_CONFIG->get('live_site')."/components/com_joomla_lms/lms_images/buttons/btn_start.png' border='0' alt='"._JLMS_NEXT_ALT_TITLE."'/>
										</a>
									</td>
									<td>
										<a href='".$link_next."' title='"._JLMS_NEXT_ALT_TITLE."'>
										"._JLMS_NEXT_ALT_TITLE."
										</a>
									</td>
									<td>
										<a href='".$link_contents."' title='"._JLMS_CONTENTS_ALT_TITLE."'>
										<img src='".$JLMS_CONFIG->get('live_site')."/components/com_joomla_lms/lms_images/buttons/btn_contents.png' border='0' alt='"._JLMS_CONTENTS_ALT_TITLE."'/>
										</a>
									</td>
									<td>
										<a href='".$link_contents."' title='"._JLMS_CONTENTS_ALT_TITLE."'>
										"._JLMS_CONTENTS_ALT_TITLE."
										</a>
									</td>
								</tr>
							</table>
						</td>
					</tr>
					</table>";	
				}
				
				if ($user_passed) {
					if ($QA->get_qvar('c_pass_message', '')) {
						$jq_language['quiz_user_passes'] = nl2br($QA->get_qvar('c_pass_message', ''));
					}
				} else {
					if ($QA->get_qvar('c_unpass_message', '')) {
						$jq_language['quiz_user_fails'] = nl2br($QA->get_qvar('c_unpass_message', ''));
					}
				}
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
				//21.03.08 Max
				if($quiz_params->get('sh_final_page_text', 1) == 1){
				$results_txt = JoomlaQuiz_template_class::JQ_show_results($jq_language['quiz_header_fin_results'], $t_ar);
				$ret_str .= "\t" . '<div>'.$results_txt.'</div>' . "\n";
				}
				//21.03.08 Max
				if($quiz_params->get('sh_final_page_fdbck', 1) == 1){
				$ret_str .= JoomlaQuiz_template_class::JQ_show_results_msg($eee, ($user_passed?$jq_language['quiz_user_passes']:$jq_language['quiz_user_fails']), $user_passed);
				$ret_str .= '<br />';
				} else {
				$ret_str .= '<br />';	
				}
				$footer_ar = array();
				$footer_ar[] = mosHTML::makeOption(0,$jq_language['quiz_fin_btn_review']);
				$footer_ar[] = mosHTML::makeOption(1,$jq_language['quiz_fin_btn_print']);
				$footer_ar[] = mosHTML::makeOption(2,$jq_language['quiz_fin_btn_certificate']);
				$footer_ar[] = mosHTML::makeOption(3,$jq_language['quiz_fin_btn_email']);
				
				$toolbar_footer = array();
				if ($QA->get_qvar('c_certificate', 0) && $user_passed) {
					$link_certificate = sefRelToAbs("index.php?option=com_joomla_lms&Itemid=".$Itemid."&no_html=1&task=print_quiz_cert&course_id=".$lms_course."&stu_quiz_id=".$stu_quiz_id."&user_unique_id=".$user_unique_id."");
					$footer_ar[2]->text = "<div class='back_button'><a target='_blank' href='".$link_certificate."'\">".$jq_language['quiz_fin_btn_certificate']."</a></div>";
					$toolbar_footer[2] = array('btn_type'=>'certificate_fbar', 'btn_js'=>$link_certificate);
				}
				if ($QA->get_qvar('c_enable_print', 0)) {
					$link_print = sefRelToAbs("index.php?option=com_joomla_lms&Itemid=".$Itemid."&no_html=1&task=print_quiz_result&course_id=".$lms_course."&stu_quiz_id=".$stu_quiz_id."&user_unique_id=".$user_unique_id."");
					$footer_ar[1]->text = "<div class='back_button'><a target='_blank' href='".$link_print."' \">".$jq_language['quiz_fin_btn_print']."</a></div>";
					$toolbar_footer[1] = array('btn_type'=>'print_fbar', 'btn_js'=>$link_print);
				}
				
				if ($QA->get_qvar('c_email_to', 0)) {
					$link_to_mail = ampReplace($JLMS_CONFIG->get('live_site')."/index.php?tmpl=component&option=$option&Itemid=$Itemid&task=quiz_action&id=$id&user_unique_id=$user_unique_id&atask=email_results&quiz=$quiz_id&stu_quiz_id=$stu_quiz_id");
					$footer_ar[3]->text = "<div class='back_button'><a target='_blank' href='".$link_to_mail."' onclick=\"jq_emailResults();\">".$jq_language['quiz_fin_btn_email']."</a></div>";
					$toolbar_footer[3] = array('btn_type'=>'email_to_fbar', 'btn_js'=>$link_to_mail);
				}
				if ($QA->get_qvar('c_enable_review', 0)) {
					$query = "UPDATE #__lms_quiz_r_student_quiz SET allow_review = 1 WHERE c_id = '".$stu_quiz_id."' and c_quiz_id = '".$quiz_id."' and c_student_id = '".$my->id."'";
					$JLMS_DB->SetQuery( $query );
					$JLMS_DB->query();
					$link_review = sefRelToAbs("index.php?option=$option&Itemid=$Itemid&id=$id&task=quiz_action&quiz=$quiz_id&stu_quiz_id=$stu_quiz_id&user_unique_id=$user_unique_id&atask=review_start");
					$footer_ar[0]->text = "<div class='back_button'><a href='".$link_review."'>".$jq_language['quiz_fin_btn_review']."</a></div>";
					$toolbar_footer[0] = array('btn_type'=>'review_fbar', 'btn_js'=>$link_review);
				}

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
					for($i=0,$n=count($rows);$i<$n;$i++){
						$row = $rows[$i];
						$quest_params = new JLMSParameters($row->params);
						
						$z = 1;
						$show_case = true;
//						if($shotype_id && !$quest_params->get('survey_question'))
						if(false && !$quest_params->get('survey_question')){
							$show_case = false;
						}
						if($show_case){
							require_once(_JOOMLMS_FRONT_HOME . "/includes/libraries/lms.lib.graph.php");
							$c_question_id = $row->c_id;
							$group_id = 0;
							$str_user_in_groups = '';
							$obj_GraphStat = JLMS_GraphStatistics($option, $id, $quiz_id, $i, $z, $row, $c_question_id, $group_id, $str_user_in_groups, 1);

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
					
					$footer_html = JoomlaQuiz_template_class::JQ_show_results_footer_content_bars($img_arr, $title_arr, $count_graph, $id);
					$ret_str .= "\t" . $footer_html . "\n";
					
				}
				
//				$footer_html = JoomlaQuiz_template_class::JQ_show_results_footer($footer_ar);
				if(isset($toolbar_footer) && count($toolbar_footer) > 0){
					ksort($toolbar_footer);
					$footer_html = JLMS_ShowToolbar($toolbar_footer, true, 'center');
				} else {
					$footer_html = '';
				}
				$ret_str .= "\t" . $footer_html . "\n";
				
				
			}
		}
		echo $ret_str;
	}
	
	function JQ_StartReview_nojs() {
		global $JLMS_DB, $my, $option, $Itemid, $JLMS_CONFIG;
		$ret_str = '';
		
		$doc = & JFactory::getDocument();
		
		$id = intval( mosGetParam( $_REQUEST, 'id', 0 ) );
		$quiz_id = intval( mosGetParam( $_REQUEST, 'quiz', 0 ) );
		$query = "SELECT * FROM #__lms_quiz_t_quiz WHERE c_id = '".$quiz_id."'";
		$JLMS_DB->SetQuery ($query );
		$quiz = $JLMS_DB->LoadObjectList();
		if (count($quiz)) {
			$quiz = $quiz[0];
		} else { return $ret_str; }
		$quiz_params = new JLMSParameters($quiz->params);
		$now = date( 'Y-m-d H:i:s', time() - date('Z') );
		if ( ($quiz->published) ) {
			if ( ($my->id) ) {
			} elseif ($quiz->c_guest) {
			} else { return $ret_str; }
		} else {
			$JLMS_ACL = & JLMSFactory::getACL();
			if (!$JLMS_ACL->CheckPermissions('quizzes', 'view_all')) {
				return $ret_str;
			}
		}
	
		if ($quiz_id) {
			$stu_quiz_id = intval( mosGetParam( $_REQUEST, 'stu_quiz_id', 0 ) );
			$user_unique_id = strval( mosGetParam( $_REQUEST, 'user_unique_id', '') );
			if ($stu_quiz_id) {
				$query = "SELECT c_quiz_id, c_student_id, unique_id, allow_review, c_passed FROM #__lms_quiz_r_student_quiz WHERE c_id = '".$stu_quiz_id."'";
				$JLMS_DB->SetQuery($query);
				$st_quiz_data = $JLMS_DB->LoadObjectList();
				
				$start_quiz = 0;
				if (count($st_quiz_data)) {
					$start_quiz = $st_quiz_data[0]->c_quiz_id;
				} else { return ''; }
				if ($user_unique_id != $st_quiz_data[0]->unique_id) { return ''; }
				if ($my->id != $st_quiz_data[0]->c_student_id) { return ''; }
				if ($start_quiz != $quiz_id) { return '';}
				if (!$st_quiz_data[0]->allow_review) { return ''; }
	
				$query = "SELECT * FROM #__lms_quiz_t_question WHERE c_quiz_id = '".$quiz_id."' ORDER BY ordering, c_id";
				$JLMS_DB->SetQuery($query);
				$q_data = $JLMS_DB->LoadObjectList();


				// 22.04.2008 Bugfix - support for Questions pool
				$inside_lp = intval(mosGetParam($_REQUEST, 'inside_lp', 0));
				$QA = new JLMS_quiz_API($quiz_id, $inside_lp);
				if (!$QA->quiz_valid()) {
					return '';
				}
				$QA->set('stu_quiz_id', $stu_quiz_id);
				$QA->set('user_unique_id', $user_unique_id);
				$QA->quiz_ProcessStartData();
			
				if ( $QA->start_valid()) {
					$q_data = $QA->quiz_Get_QuestionList();
				}

				global $JLMS_LANGUAGE, $JLMS_CONFIG;
				JLMS_require_lang($JLMS_LANGUAGE, 'quiz.lang', $JLMS_CONFIG->get('default_language'));

				require(_JOOMLMS_FRONT_HOME . '/includes/quiz/quiz_language.php');
				global $jq_language;

				if ($quiz->c_wrong_message) $jq_language['quiz_answer_incorrect'] = htmlspecialchars(nl2br($quiz->c_wrong_message));
				if ($quiz->c_right_message) $jq_language['quiz_answer_correct'] = htmlspecialchars(nl2br($quiz->c_right_message));
				$quest_params = new JLMSParameters($q_data[0]->params);	
				if($quest_params->get('survey_question') == 1){
					$is_survey = 1;
				} else {
					$is_survey = 0;				
				}
				$is_correct = 0;

						//---test for right quest
						$proc_quest_id = $q_data[0]->c_id;
						if (isset($q_data[0]->c_pool) && $q_data[0]->c_pool) {
							$q_data[0]->old_c_id = $q_data[0]->c_id;
							$q_data[0]->c_id = $q_data[0]->c_pool;
							$proc_quest_id_pool = $q_data[0]->c_pool;
						} elseif (isset($q_data[0]->c_pool_gqp) && $q_data[0]->c_pool_gqp) {
							$q_data[0]->old_c_id = $q_data[0]->c_id;
							$q_data[0]->c_id = $q_data[0]->c_pool_gqp;
							$proc_quest_id_pool = $q_data[0]->c_pool_gqp;
						} else {
							$proc_quest_id_pool = $q_data[0]->c_id;
							$q_data[0]->old_c_id = $q_data[0]->c_id;
						}

						$proc_quest_id = $q_data[0]->c_id;	
							switch ($q_data[0]->c_type) {
								case 1:
								case 3:
								case 12:	
									$query = "SELECT qst.c_id FROM #__lms_quiz_r_student_quiz as qz, #__lms_quiz_r_student_question as qst WHERE qz.c_id = qst.c_stu_quiz_id AND qz.unique_id = '".$user_unique_id."' AND c_question_id='".$proc_quest_id."'";
									$JLMS_DB->SetQuery( $query );
									$sqtq_id = $JLMS_DB->LoadResult();
									$query = "SELECT c_choice_id FROM #__lms_quiz_r_student_choice WHERE c_sq_id = '".$sqtq_id."'";
									$JLMS_DB->SetQuery( $query );
									$answer = $JLMS_DB->LoadResult();
									
									$query = "SELECT a.c_point, b.c_id, a.c_attempts FROM #__lms_quiz_t_question as a, #__lms_quiz_t_choice as b WHERE a.c_id = '".$proc_quest_id_pool."' and b.c_question_id = a.c_id and b.c_right = '1'";
									$JLMS_DB->SetQuery( $query );
									$ddd = $JLMS_DB->LoadObjectList();
									if ($answer)
									if (count($ddd)) {
										if ($ddd[0]->c_id == $answer) {
											$is_correct = 1;
										}
										
									}
								break;
								case 2:
								case 13:
									$query = "SELECT qst.c_id FROM #__lms_quiz_r_student_quiz as qz, #__lms_quiz_r_student_question as qst WHERE qz.c_id = qst.c_stu_quiz_id AND qz.unique_id = '".$user_unique_id."' AND c_question_id='".$proc_quest_id."'";
									$JLMS_DB->SetQuery( $query );
									$sqtq_id = $JLMS_DB->LoadResult();
									$query = "SELECT c_choice_id FROM #__lms_quiz_r_student_choice WHERE c_sq_id = '".$sqtq_id."'";
									$JLMS_DB->SetQuery( $query );
									$answers = $JLMS_DB->LoadObjectList();
									$answer = array();
									if(count($answers))
										foreach($answers as $answ)
											$answer[] = $answ->c_choice_id;
									$query = "SELECT a.c_point, a.c_attempts FROM #__lms_quiz_t_question as a WHERE a.c_id = '".$proc_quest_id_pool."'";
									$JLMS_DB->SetQuery( $query );
									$ddd = $JLMS_DB->LoadObjectList();
									$query = "SELECT b.c_id FROM #__lms_quiz_t_question as a, #__lms_quiz_t_choice as b WHERE a.c_id = '".$proc_quest_id_pool."' and b.c_question_id = a.c_id and b.c_right = '1'";
									$JLMS_DB->SetQuery( $query );
									$ddd2 = $JLMS_DB->LoadObjectList();
									$query = "SELECT b.c_id FROM #__lms_quiz_t_question as a, #__lms_quiz_t_choice as b WHERE a.c_id = '".$proc_quest_id_pool."' and b.c_question_id = a.c_id and b.c_right <> '1'";
									$JLMS_DB->SetQuery( $query );
									$ddd3 = $JLMS_DB->LoadObjectList();
									
									$ans_array = $answer;
									if (count($ddd2) && count($ddd)) {
										$c_quest_score = $ddd[0]->c_point;
										$is_correct = 1;
										foreach ($ddd2 as $right_row) {
											if (!in_array($right_row->c_id, $ans_array)) {
												$c_quest_score = 0;
												$is_correct = 0; }
										}
										foreach ($ddd3 as $not_right_row) {
											if (in_array($not_right_row->c_id, $ans_array)) {
												$c_quest_score = 0;
												$is_correct = 0; }
										}
										
									}
									
								break;
								case 4:
								case 5:
								case 11:
									$query = "SELECT qst.c_id FROM #__lms_quiz_r_student_quiz as qz, #__lms_quiz_r_student_question as qst WHERE qz.c_id = qst.c_stu_quiz_id AND qz.unique_id = '".$user_unique_id."' AND c_question_id='".$proc_quest_id."'";
									$JLMS_DB->SetQuery( $query );
									$sqtq_id = $JLMS_DB->LoadResult();
									$query = "SELECT a.c_sel_text as c_sel_text FROM #__lms_quiz_r_student_matching as a, #__lms_quiz_t_matching as b WHERE a.c_sq_id = '".$sqtq_id."' AND a.c_matching_id = b.c_id ORDER BY b.ordering";
									$JLMS_DB->SetQuery( $query );
									$answers = $JLMS_DB->LoadObjectList();
									if(count($answers))
										foreach($answers as $answ)
											$answer[] = $answ->c_sel_text;
									$query = "SELECT a.c_point, a.c_attempts FROM #__lms_quiz_t_question as a WHERE a.c_id = '".$proc_quest_id_pool."'";
									$JLMS_DB->SetQuery( $query );
									$ddd = $JLMS_DB->LoadObjectList();
									$query = "SELECT b.c_id, b.c_left_text, b.c_right_text FROM #__lms_quiz_t_question as a, #__lms_quiz_t_matching as b WHERE a.c_id = '".$proc_quest_id_pool."' and b.c_question_id = a.c_id ORDER BY b.ordering";
									$JLMS_DB->SetQuery( $query );
									$ddd2 = $JLMS_DB->LoadObjectList();
	
									$ans_array = $answer;
									if (count($ddd2) && count($ddd)) {
	
										$is_correct = 1; $rr_num = 0;
										foreach ($ddd2 as $right_row) {
											if ($right_row->c_right_text != $ans_array[$rr_num]) {
												$is_correct = 0;
											}
											$rr_num ++;
									
										}
										
									}
								break;
								case 6:
									$query = "SELECT qst.c_id FROM #__lms_quiz_r_student_quiz as qz, #__lms_quiz_r_student_question as qst WHERE qz.c_id = qst.c_stu_quiz_id AND qz.unique_id = '".$user_unique_id."' AND c_question_id='".$proc_quest_id."'";
									$JLMS_DB->SetQuery( $query );
									$sqtq_id = $JLMS_DB->LoadResult();
									$query = "SELECT c_answer FROM #__lms_quiz_r_student_blank WHERE c_sq_id = '".$sqtq_id."'";
									$JLMS_DB->SetQuery( $query );
									$answer = $JLMS_DB->LoadResult();
									$query = "SELECT a.c_point, a.c_attempts FROM #__lms_quiz_t_question as a WHERE a.c_id = '".$proc_quest_id_pool."'";
									$JLMS_DB->SetQuery( $query );
									$ddd = $JLMS_DB->LoadObjectList();
									$query = "SELECT c.c_text FROM #__lms_quiz_t_question as a, #__lms_quiz_t_blank as b, #__lms_quiz_t_text as c WHERE a.c_id = '".$proc_quest_id_pool."' and b.c_question_id = a.c_id and c.c_blank_id = b.c_id";
									$JLMS_DB->SetQuery( $query );
									$ddd2 = $JLMS_DB->LoadObjectList();
	
									$answer = trim(urldecode($answer));
									if (count($ddd2) && count($ddd)) {
										foreach ($ddd2 as $right_row) {
											if (strtolower($right_row->c_text) === strtolower($answer)) {
												$is_correct = 1;
											}
										}
										
									}
								break;
								case 7:
									$query = "SELECT qst.c_id FROM #__lms_quiz_r_student_quiz as qz, #__lms_quiz_r_student_question as qst WHERE qz.c_id = qst.c_stu_quiz_id AND qz.unique_id = '".$user_unique_id."' AND c_question_id='".$proc_quest_id."'";
									$JLMS_DB->SetQuery( $query );
									$sqtq_id = $JLMS_DB->LoadResult();
									$query = "SELECT * FROM #__lms_quiz_r_student_hotspot WHERE c_sq_id = '".$sqtq_id."'";
									$JLMS_DB->SetQuery( $query );
									$answers = $JLMS_DB->LoadObjectList();
									$answer = array();
									if(count($answers))
									{
										$answer[0] = $answers[0]->c_select_x;
										$answer[1] = $answers[0]->c_select_y;
									}
									if(count($answer))
									{
										$query = "SELECT a.c_point, a.c_attempts, b.c_start_x, b.c_start_y, b.c_width, b.c_height FROM #__lms_quiz_t_question as a, #__lms_quiz_t_hotspot as b WHERE a.c_id = '".$proc_quest_id_pool."' and b.c_question_id = a.c_id";
										$JLMS_DB->SetQuery( $query );
										$ddd = $JLMS_DB->LoadObjectList();
										if (count($ddd)) {
											$ans_array = $answer;
											if ((count($ans_array) == 2) && ($ans_array[0] >= $ddd[0]->c_start_x) && ($ans_array[0] <= ($ddd[0]->c_start_x+$ddd[0]->c_width)) && ($ans_array[1] >= $ddd[0]->c_start_y) && ($ans_array[1] <= ($ddd[0]->c_start_y+$ddd[0]->c_height)) ) {
												$is_correct = 1;
											}
										}	
									}	
								break;
								case 8:
									$is_correct = 1;
									$is_survey = 1;
									$answer = array();
								break;
								case 9:
									$is_correct = 1;
									$is_survey = 1;
									$query = "SELECT qst.c_id FROM #__lms_quiz_r_student_quiz as qz, #__lms_quiz_r_student_question as qst WHERE qz.c_id = qst.c_stu_quiz_id AND qz.unique_id = '".$user_unique_id."' AND c_question_id='".$proc_quest_id."'";
									$JLMS_DB->SetQuery( $query );
									$sqtq_id = $JLMS_DB->LoadResult();
									$query = "SELECT * FROM #__lms_quiz_r_student_scale WHERE c_sq_id = '".$sqtq_id."'";
									$JLMS_DB->SetQuery( $query );
									$answers = $JLMS_DB->LoadObjectList();
									$answer = array();
									for($p=0;$p<count($answers);$p++)
									{
										$answer[$p][0] = $answers[$p]->q_scale_id;
										$answer[$p][1] = $answers[$p]->scale_id;
									}
								break;
								case 10:
									$is_correct = 1;
									$is_survey = 1;
									$answer = array();
								break;
							}
					if (!substr_count($quiz->params,'disable_quest_feedback=1') && !substr_count($q_data[0]->params,'disable_quest_feedback=1')) {		
						//---
						if($is_survey)
						{
							$msg_cor = '';
						}
						else 
						{
							if($is_correct)
							{
								$msg_cor = $jq_language['quiz_answer_correct'];
								
							}	
							else 
								$msg_cor = $jq_language['quiz_answer_incorrect'];
						}
						
					}
					else {
						$msg_cor = '';
					}
//					$ret_str .= "\t" . '<quiz_review_correct><![CDATA['.$msg_cor.']]></quiz_review_correct>' . "\n";
				
				//--explanation
				$explans = '';
				if(!$is_survey)
				switch ($quiz_params->get('sh_explanation'))
				{	
					case '1':
					case '12':		
							if($q_data[0]->c_explanation)
								$explans = $q_data[0]->c_explanation;
							break;
					case '2':	
					case '13':
							if($st_quiz_data[0]->c_passed)
							if($q_data[0]->c_explanation)
								$explans = $q_data[0]->c_explanation;
							break;	
					case '3':
							if(!$st_quiz_data[0]->c_passed)
							if($q_data[0]->c_explanation)
								$explans = $q_data[0]->c_explanation;
							break;				
				}
//				$ret_str .= "\t" . '<quiz_review_explanation><![CDATA['.$explans.']]></quiz_review_explanation>' . "\n";

				$kol_quests = count($q_data);
				$quest_score = $q_data[0]->c_point;
				$qtype = $q_data[0]->c_type;
				$quest_id = $q_data[0]->c_id;
				
				$query = "SELECT a.*, b.lpath_id FROM #__lms_learn_path_step_quiz_results as a, #__lms_learn_path_steps as b WHERE a.stu_quiz_id = '".$stu_quiz_id."' AND a.step_id = b.id";
				$JLMS_DB->setQuery($query);
				$this_lpath = $JLMS_DB->LoadObject();
				
				$toolbar = array();
				if(isset($this_lpath->stu_quiz_id) && $this_lpath->stu_quiz_id == $stu_quiz_id){
					if($qtype == 10){
						if(isset($q_data[$j]->c_slide) && $q_data[$j]->c_slide){
							$toolbar[] = array('kol_quests' => $kol_quests, 'num_quest' => 1, 'quest_score' => $quest_score, 'img_btn'=>'btn_start.png', 'text_btn'=>_JLMS_NEXT_ALT_TITLE, 'link_cont'=>'&task=show_lpath_nojs&action=contents_lpath&course_id='.$id.'&id='.$this_lpath->lpath_id.'&stu_quiz_id='.$stu_quiz_id.'&user_unique_id='.$this_lpath->unique_id.'&step_id='.$this_lpath->step_id.'&user_start_id='.$this_lpath->start_id.'&quest_id='.$quest_id.'&quiz_id='.$quiz_id.'&stu_quiz_id='.$stu_quiz_id.'');						
						} else {
							$toolbar[] = array('kol_quests' => $kol_quests, 'num_quest' => 1, 'quest_score' => $quest_score, 'img_btn'=>'btn_start.png', 'text_btn'=>_JLMS_NEXT_ALT_TITLE);							
						}
					} else {
						if(isset($q_data[$j]->c_slide) && $q_data[$j]->c_slide){
							$toolbar[] = array('kol_quests' => $kol_quests, 'num_quest' => 1, 'quest_score' => $quest_score, 'img_btn'=>'btn_complete.png', 'text_btn'=>_JLMS_OK_ALT_TITLE, 'link_cont'=>'&task=show_lpath_nojs&action=contents_lpath&course_id='.$id.'&id='.$this_lpath->lpath_id.'&stu_quiz_id='.$stu_quiz_id.'&user_unique_id='.$this_lpath->unique_id.'&step_id='.$this_lpath->step_id.'&user_start_id='.$this_lpath->start_id.'&quest_id='.$quest_id.'&quiz_id='.$quiz_id.'&stu_quiz_id='.$stu_quiz_id.'');
						} else {
							$toolbar[] = array('kol_quests' => $kol_quests, 'num_quest' => 1, 'quest_score' => $quest_score, 'img_btn'=>'btn_complete.png', 'text_btn'=>_JLMS_OK_ALT_TITLE);	
						}
					}
				} else {
					if($qtype == 10){
						if(isset($q_data[$j]->c_slide) && $q_data[$j]->c_slide){
							$toolbar[] = array('kol_quests' => $kol_quests, 'num_quest' => 1, 'quest_score' => $quest_score, 'img_btn'=>'btn_start.png', 'text_btn'=>_JLMS_NEXT_ALT_TITLE, 'link_cont'=>'&task=quiz_action&atask=contents&id='.$id.'&quiz='.$quiz_id.'&stu_quiz_id='.$stu_quiz_id.'&user_unique_id='.$user_unique_id.'&quest_id='.$quest_id.'');
						} else {
							$toolbar[] = array('kol_quests' => $kol_quests, 'num_quest' => 1, 'quest_score' => $quest_score, 'img_btn'=>'btn_start.png', 'text_btn'=>_JLMS_NEXT_ALT_TITLE);
						}
					} else {
						if(isset($q_data[$j]->c_slide) && $q_data[$j]->c_slide){
							$toolbar[] = array('kol_quests' => $kol_quests, 'num_quest' => 1, 'quest_score' => $quest_score, 'img_btn'=>'btn_complete.png', 'text_btn'=>_JLMS_OK_ALT_TITLE, 'link_cont'=>'&task=quiz_action&atask=contents&id='.$id.'&quiz='.$quiz_id.'&stu_quiz_id='.$stu_quiz_id.'&user_unique_id='.$user_unique_id.'&quest_id='.$quest_id.'');
						} else {
							$toolbar[] = array('kol_quests' => $kol_quests, 'num_quest' => 1, 'quest_score' => $quest_score, 'img_btn'=>'btn_complete.png', 'text_btn'=>_JLMS_OK_ALT_TITLE);							
						}
					}
				}
				
				$doc->addStyleSheet( $JLMS_CONFIG->get('live_site').'/components/com_joomla_lms/includes/quiz/templates/joomlaquiz_lms_template/jq_template.css' );
				
				?>
				<form name="quest_form" action="<?php echo ampReplace($JLMS_CONFIG->get('live_site')."/index.php?option=$option&Itemid=$Itemid");?>" method="post">
					<table border="0" width="100%" align="center" cellpadding="0" cellspacing="0" class="jlms_table_no_borders">
						<tr>
							<td>
								<?php echo JLMS_quiz_ajax_class::JQ_toolbar_nojs($toolbar, $qtype, 1);?>
					
								<input type="hidden" name="stu_quiz_id" value="<?php echo $stu_quiz_id;?>"/>
								<input type="hidden" name="user_unique_id" value="<?php echo $user_unique_id;?>"/>
							</td>
						</tr>
						<tr>
							<td>
								<?php
								
								if ($kol_quests > 0) {
									$quest_num = 0;
									# commented 25 April 2007 (DEN)
									# we've already randomized auestions in the sequence
									/*if ($QA->get_qvar('c_random')) {
										$quest_num = rand(0, ($kol_quests - 1) );
									}*/
									?>
									<input type="hidden" name="quiz_count_quests" value="<?php echo $kol_quests;?>"/>
									<input type="hidden" name="quiz_quest_num" value="1"/>
									<?php echo JLMS_quiz_ajax_class::JQ_GetQuestData_review_nojs($q_data[0], $jq_language, $answer, $quiz_params->get('sh_user_answer'), $is_survey, $msg_cor, $is_correct);
						//			$ret_str .= JLMS_quiz_ajax_class::JQ_GetPanelData_nojs($quiz_id, $q_data); ?>
									<?php
								}
								if($explans != ''){
									echo JoomlaQuiz_template_class::JQ_show_messagebox('', $explans, 3);
								}
								?>
							</td>
						</tr>
					</table>
					
					<input type="hidden" name="option" value="<?php echo $option;?>"/>
					<input type="hidden" name="task" value="quiz_action"/>
					<input type="hidden" name="id" value="<?php echo $id;?>"/>
					<input type="hidden" name="quiz" value="<?php echo $quiz_id;?>"/>
					<input type="hidden" name="atask" value="review_next"/>
				</form>
				<?php
				
			}
		}
//		return $ret_str;
	}
	
	function JQ_emailResults_nojs() {
		global $JLMS_DB, $my;
	
		// preloading QUIZ languge (28.02.2007 new method) (all quizzes messages now in global quiz language)
		global $JLMS_LANGUAGE, $JLMS_CONFIG;
		JLMS_require_lang($JLMS_LANGUAGE, 'quiz.lang', $JLMS_CONFIG->get('default_language'));
		//require(_JOOMLMS_FRONT_HOME . "/languages/".$JLMS_CONFIG->get('default_language').'/quiz.lang.php');
		require(_JOOMLMS_FRONT_HOME . '/includes/quiz/quiz_language.php');
		global $jq_language;
	
		$ret_str = '';
		$result = false;
		$stu_quiz_id = intval( mosGetParam( $_REQUEST, 'stu_quiz_id', 0 ) );
		$quiz_id = intval( mosGetParam( $_REQUEST, 'quiz', 0 ) );
		$user_unique_id = strval( mosGetParam( $_REQUEST, 'user_unique_id', '') );
		$query = "SELECT * FROM #__lms_quiz_r_student_quiz WHERE c_id = '".$stu_quiz_id."'";
		$JLMS_DB->SetQuery( $query );
		$stu_info = $JLMS_DB->LoadObjectList();
		if (count($stu_info)) {
			$stu_info = $stu_info[0];
			if ( ($user_unique_id == $stu_info->unique_id) && ($quiz_id == $stu_info->c_quiz_id) && ($my->id == $stu_info->c_student_id) ) {
				$query = "SELECT u.email, u.username, q.c_email_to, q.c_language"
				. "\n FROM #__lms_quiz_r_student_quiz sq, #__lms_quiz_t_quiz q LEFT JOIN #__users u ON  q.c_user_id = u.id"
				. "\n WHERE sq.c_id = '".$stu_quiz_id."' AND sq.c_quiz_id = q.c_id";
				$JLMS_DB->setQuery( $query );
				$rows = $JLMS_DB->loadObjectList();
				// u.email - author email
				if (count($rows)) {
	/*				if ( ($rows[0]->c_language) && ($rows[0]->c_language != 1) ) {
						$query = "SELECT lang_file FROM #__lms_quiz_languages WHERE id = '".intval($rows[0]->c_language)."'";
						$JLMS_DB->SetQuery( $query );
						$req_lang = $JLMS_DB->LoadResult();
						if ($req_lang && file_exists( dirname(__FILE__) . "/language/".$req_lang.".php")) {
							include( dirname(__FILE__) . "/language/".$req_lang.".php");
						}
					}
	*/				if ($rows[0]->c_email_to) {
						$email_address = '';
						if ($rows[0]->c_email_to == 2) {
							$query = "SELECT email FROM #__users WHERE id = '".$my->id."'";
							$JLMS_DB->SetQuery( $query );
							$email_address = $JLMS_DB->LoadResult();//strval( mosGetParam( $_REQUEST, 'email_address', '') );
						} else {
							$email_address = $rows[0]->email;
						}
						require_once(dirname(__FILE__)."/joomlaquiz.manageresults.php");
						$result = JQ_Email($stu_quiz_id, $email_address);
					}
				}
			}
		}
//		$ret_str .= "\t" . '<task>email_results</task>' . "\n";
		if ($result) $ret_str .= "\t" . '<div style="text-align:center; vertical-align:middle; width:100%; height:50px; color:#c80000;">'.$jq_language['quiz_mes_email_ok'].'</div>' . "\n";
		else $ret_str .= "\t" . '<div style="text-align:center; vertical-align:middle; width:100%; height:50px; color:#c80000;">'.$jq_language['quiz_mes_email_fail'].'</div>' . "\n";
		echo $ret_str;
		die;
	}
	
	function JQ_LoadNextData_nojs(){
		global $JLMS_DB, $option, $Itemid, $JLMS_CONFIG;
		
		$id = intval( mosGetParam( $_REQUEST, 'id', 0 ) );
		$quest_id = intval( mosGetParam( $_REQUEST, 'quest_id', 0 ) );
		$quiz_id = intval( mosGetParam( $_REQUEST, 'quiz', 0 ) );
		$inside_lp = intval( mosGetParam( $_REQUEST, 'inside_lp', 0 ) );
		$QA = new JLMS_quiz_API($quiz_id, $inside_lp);
		if (!$QA->quiz_valid()) {
			return '';
		}
//		$toolbar_no_a = $QA->quiz_Get_NoAtToolbar();
	
		$stu_quiz_id = intval( mosGetParam( $_REQUEST, 'stu_quiz_id', 0 ) );
		$quest_id = intval( mosGetParam( $_REQUEST, 'quest_id', 0 ) );
		$user_unique_id = strval( mosGetParam( $_REQUEST, 'user_unique_id', '') );
		
		$quest_score = intval( mosGetParam( $_REQUEST, 'quest_score', 0 ) );
//		$answer = strval( mosGetParam( $_REQUEST, 'answer', '' ) );
	
		$QA->set('stu_quiz_id', $stu_quiz_id);
		$QA->set('user_unique_id', $user_unique_id);
		$QA->quiz_ProcessStartData();
		$q_data = $QA->quiz_Get_QuestionList();
		
			$is_quest_exists = false;
			$qtype = 0;
			$c_pool_quest = 0;
			
			foreach ($q_data as $qd) {
				if ($qd->c_id == $quest_id) {
					$is_quest_exists = true;
					$qtype = $qd->c_type;
					$c_pool_quest = $qd->c_pool;
					$quest_params = new JLMSParameters($qd->params);
					break;
				}
			}
			if (!$is_quest_exists) {
				return '';
			}
		
		$kol_quests = count($q_data);
		
		$quiz = $QA->quiz_data;// temporary for compatibility
		$quiz_params = new JLMSParameters($QA->get_qvar('params'));
		
			global $JLMS_LANGUAGE, $JLMS_CONFIG;
			JLMS_require_lang($JLMS_LANGUAGE, 'quiz.lang', $JLMS_CONFIG->get('default_language'));
			//require(_JOOMLMS_FRONT_HOME . "/languages/".$JLMS_CONFIG->get('default_language').'/quiz.lang.php');
			require(_JOOMLMS_FRONT_HOME . '/includes/quiz/quiz_language.php');
			global $jq_language;
			
			
			if ($quiz->c_wrong_message) $jq_language['quiz_answer_incorrect'] = htmlspecialchars(nl2br($quiz->c_wrong_message));
			if ($quiz->c_right_message) $jq_language['quiz_answer_correct'] = htmlspecialchars(nl2br($quiz->c_right_message));
			//$q_t_params = $QA->get_qvar('params');
			if ($quiz_params->get('disable_quest_feedback') || $quest_params->get('disable_quest_feedback')) {
				
			} else {
				$query = "SELECT * FROM #__lms_quiz_t_question_fb WHERE quest_id = $quest_id";
				$JLMS_DB->SetQuery($query);
				$q_fbs = $JLMS_DB->LoadObjectList();
				foreach ($q_fbs as $qfb) {
					if ($qfb->choice_id == -1) {
						if ($qfb->fb_text) {
							$jq_language['quiz_answer_incorrect'] = $qfb->fb_text;
						}
					} elseif(!$qfb->choice_id) {
						if ($qfb->fb_text) {
							$jq_language['quiz_answer_correct'] = $qfb->fb_text;
						}
					}
				}
			}

			$quest_num = 1;

			$query = "SELECT c_question_id FROM #__lms_quiz_r_student_question WHERE c_stu_quiz_id = '".$stu_quiz_id."'";
			$JLMS_DB->SetQuery( $query );
			$q_ids = $JLMS_DB->LoadResultArray();
			$q_num = 0;
			$q_num_ar = 0;
			$qqn = 0;
			foreach ($q_data as $qd) {
				if ($qd->c_id == $quest_id) {
					$q_num = $qqn + 1;
					$q_num_ar = $qqn;
					break;
				}
				$qqn ++;
			}
			if (!$q_num) {
				return '';
			} else {
				$q_num = 0;
				$q_num_ar = 0;
				// here we are using $q_num and $q_num_ar for other needs (don't warry :) )
				// find next not answered question
				for ($i = $qqn, $n = count($q_data); $i < $n; $i ++ ) {
					if (!in_array($q_data[$i]->c_id, $q_ids) && $q_data[$i]->c_id != $quest_id ) {
						$q_num = $i + 1;
						$q_num_ar = $i;
						break;
					}
				}
				if (!$q_num) {
					// find not answered question from prev questions
					for ($i = 0; $i < $qqn; $i ++ ) {
						if (!in_array($q_data[$i]->c_id, $q_ids) && $q_data[$i]->c_id != $quest_id ) {
							$q_num = $i + 1;
							$q_num_ar = $i;
							break;
						}
					}
				}
				if ($q_num) {
					$quest_num = $q_num;
					$j = $q_num_ar;
				}
			}
		$quest_score = $q_data[$j]->c_point;
		$quest_id = $q_data[$j]->c_id;

		$query = "SELECT a.*, b.lpath_id FROM #__lms_learn_path_step_quiz_results as a, #__lms_learn_path_steps as b WHERE a.stu_quiz_id = '".$stu_quiz_id."' AND a.step_id = b.id";
		$JLMS_DB->setQuery($query);
		$this_lpath = $JLMS_DB->LoadObject();

		$toolbar = array();
		
		if(isset($QA->lpath_stu_quiz_id) && $QA->lpath_stu_quiz_id == $stu_quiz_id){
			if($q_data[$j]->c_type == 10){
				if(isset($q_data[$j]->c_slide) && $q_data[$j]->c_slide){
					$toolbar[] = array('kol_quests' => $kol_quests, 'num_quest' => $quest_num, 'quest_score' => $quest_score, 'img_btn'=>'btn_start.png', 'text_btn'=>_JLMS_NEXT_ALT_TITLE, 'link_cont'=>'&task=show_lpath_nojs&action=contents_lpath&course_id='.$id.'&id='.$this_lpath->lpath_id.'&stu_quiz_id='.$stu_quiz_id.'&user_unique_id='.$this_lpath->unique_id.'&step_id='.$this_lpath->step_id.'&user_start_id='.$this_lpath->start_id.'&quest_id='.$quest_id.'&quiz_id='.$quiz_id.'&stu_quiz_id='.$stu_quiz_id.'');
				} else {
					$toolbar[] = array('kol_quests' => $kol_quests, 'num_quest' => $quest_num, 'quest_score' => $quest_score, 'img_btn'=>'btn_start.png', 'text_btn'=>_JLMS_NEXT_ALT_TITLE);					
				}
			} else {
				if(isset($q_data[$j]->c_slide) && $q_data[$j]->c_slide){
					$toolbar[] = array('kol_quests' => $kol_quests, 'num_quest' => $quest_num, 'quest_score' => $quest_score, 'img_btn'=>'btn_complete.png', 'text_btn'=>_JLMS_OK_ALT_TITLE, 'link_cont'=>'&task=show_lpath_nojs&action=contents_lpath&course_id='.$id.'&id='.$this_lpath->lpath_id.'&stu_quiz_id='.$stu_quiz_id.'&user_unique_id='.$this_lpath->unique_id.'&step_id='.$this_lpath->step_id.'&user_start_id='.$this_lpath->start_id.'&quest_id='.$quest_id.'&quiz_id='.$quiz_id.'&stu_quiz_id='.$stu_quiz_id.'');
				} else {
					$toolbar[] = array('kol_quests' => $kol_quests, 'num_quest' => $quest_num, 'quest_score' => $quest_score, 'img_btn'=>'btn_complete.png', 'text_btn'=>_JLMS_OK_ALT_TITLE);	
				}	
			}
		} else {
			if($q_data[$j]->c_type == 10){
				if(isset($q_data[$j]->c_slide) && $q_data[$j]->c_slide){
					$toolbar[] = array('kol_quests' => $kol_quests, 'num_quest' => $quest_num, 'quest_score' => $quest_score, 'img_btn'=>'btn_start.png', 'text_btn'=>_JLMS_NEXT_ALT_TITLE, 'link_cont'=>'&task=quiz_action&atask=contents&id='.$id.'&quiz='.$quiz_id.'&stu_quiz_id='.$stu_quiz_id.'&user_unique_id='.$user_unique_id.'&quest_id='.$quest_id.'');				
				} else {
					$toolbar[] = array('kol_quests' => $kol_quests, 'num_quest' => $quest_num, 'quest_score' => $quest_score, 'img_btn'=>'btn_start.png', 'text_btn'=>_JLMS_NEXT_ALT_TITLE);				
				}
			} else {
				if(isset($q_data[$j]->c_slide) && $q_data[$j]->c_slide){
					$toolbar[] = array('kol_quests' => $kol_quests, 'num_quest' => $quest_num, 'quest_score' => $quest_score, 'img_btn'=>'btn_complete.png', 'text_btn'=>_JLMS_OK_ALT_TITLE, 'link_cont'=>'&task=quiz_action&atask=contents&id='.$id.'&quiz='.$quiz_id.'&stu_quiz_id='.$stu_quiz_id.'&user_unique_id='.$user_unique_id.'&quest_id='.$quest_id.'');
				} else {
					$toolbar[] = array('kol_quests' => $kol_quests, 'num_quest' => $quest_num, 'quest_score' => $quest_score, 'img_btn'=>'btn_complete.png', 'text_btn'=>_JLMS_OK_ALT_TITLE);					
				}
			}
		}
		?>
		<form action="<?php echo ampReplace($JLMS_CONFIG->get('live_site')."/index.php?option=$option&Itemid=$Itemid");?>" method="post" name="quest_form">
			<table width="100%" cellpadding="0" cellspacing="0" border="0" class="jlms_table_no_borders">
				<tr>
					<td>
					<?php
						JLMS_quiz_ajax_class::JQ_toolbar_nojs($toolbar, $q_data[$j]->c_type, 1);
					?>
					</td>
				</tr>
				<tr>
					<td>
						<?php
						echo JLMS_quiz_ajax_class::JQ_GetQuestData_nojs($q_data[$j], $jq_language, $QA->get('stu_quiz_id',0));
						?>
					</td>
				</tr>
			</table>
			
			<input type="hidden" name="atask" value="next"/>
			<input type="hidden" name="user_unique_id" value="<?php echo $user_unique_id;?>"/>
			<input type="hidden" name="stu_quiz_id" value="<?php echo $stu_quiz_id;?>"/>
			<input type="hidden" name="quiz" value="<?php echo $quiz_id;?>"/>
			
			<input type="hidden" name="option" value="<?php echo $option;?>"/>
			<input type="hidden" name="task" value="quiz_action"/>
			<input type="hidden" name="id" value="<?php echo $id;?>"/>
		</form>
		<?php
	}
	
	function JQ_SeekQuestion_nojs() {
		global $JLMS_DB, $my, $option, $Itemid, $JLMS_CONFIG;
	
		$ret_str = '';
		$quiz_id = intval( mosGetParam( $_REQUEST, 'quiz', 0 ) );
		$inside_lp = intval( mosGetParam( $_REQUEST, 'inside_lp', 0 ) );
		$QA = new JLMS_quiz_API($quiz_id, $inside_lp);
		if (!$QA->quiz_valid()) {
			return '';
		}

		$id = intval( mosGetParam( $_REQUEST, 'id', 0 ) );
		$quest_num = intval( mosGetParam( $_REQUEST, 'quest_num', 0 ) );
		$stu_quiz_id = intval( mosGetParam( $_REQUEST, 'stu_quiz_id', 0 ) );
		$seek_quest_id = intval( mosGetParam( $_REQUEST, 'seek_quest_id', 0 ) );
		$user_unique_id = strval( mosGetParam( $_REQUEST, 'user_unique_id', '') );
	
		$QA->set('stu_quiz_id', $stu_quiz_id);
		$QA->set('user_unique_id', $user_unique_id);
		$QA->quiz_ProcessStartData(); // fill in start_valid private variable
		
		?>
		<form action='<?php echo ampReplace($JLMS_CONFIG->get('live_site')."/index.php?option=$option&Itemid=$Itemid");?>' method='post' name='quest_form'>
		<?php
		if ( $QA->start_valid() && $seek_quest_id ) {
	
			$quiz = $QA->quiz_data;// temporary for compatibility
	
			if ($QA->time_is_up()) {
				return JLMS_quiz_ajax_class::JQ_TimeIsUp_nojs($quiz);
			}
	
			$q_data = $QA->quiz_Get_QuestionList();
			$kol_quests = count($q_data);
			$seek_avail = false;
			$i = 0;
			foreach ($q_data as $qd) {
				if ($qd->c_id == $seek_quest_id) {
					$seek_avail = true;
					break;
				}
				$i ++;
			}
	
			if ($seek_avail) { // if Seek question from the current quiz
	
				$quest_num = $i + 1; // number of question in the quiz sequence
	
				global $JLMS_LANGUAGE, $JLMS_CONFIG;
				JLMS_require_lang($JLMS_LANGUAGE, 'quiz.lang', $JLMS_CONFIG->get('default_language'));
				//require(_JOOMLMS_FRONT_HOME . "/languages/".$JLMS_CONFIG->get('default_language').'/quiz.lang.php');
				require(_JOOMLMS_FRONT_HOME . '/includes/quiz/quiz_language.php');
				global $jq_language;
				if ($quiz->c_wrong_message) $jq_language['quiz_answer_incorrect'] = htmlspecialchars(nl2br($quiz->c_wrong_message));
				if ($quiz->c_right_message) $jq_language['quiz_answer_correct'] = htmlspecialchars(nl2br($quiz->c_right_message));
				
				$cur_tmpl = 'joomlaquiz_lms_template';
				if ($cur_tmpl) {
					require_once(dirname(__FILE__) . '/templates/'.$cur_tmpl.'/jq_template.php');
					if (isset($q_data[$i])) {
						
						$query = "SELECT a.*, b.lpath_id FROM #__lms_learn_path_step_quiz_results as a, #__lms_learn_path_steps as b WHERE a.stu_quiz_id = '".$stu_quiz_id."' AND a.step_id = b.id";
						$JLMS_DB->setQuery($query);
						$this_lpath = $JLMS_DB->LoadObject();
						
						$toolbar = array();
						if(isset($this_lpath->stu_quiz_id) && $this_lpath->stu_quiz_id == $stu_quiz_id){
							if($q_data[$i]->c_type == 10){
								if(isset($q_data[$j]->c_slide) && $q_data[$j]->c_slide){
									$toolbar[] = array('kol_quests' => $kol_quests, 'num_quest' => $quest_num, 'quest_score' => $q_data[$i]->c_point, 'img_btn'=>'btn_start.png', 'text_btn'=>_JLMS_NEXT_ALT_TITLE, 'link_cont'=>'&task=show_lpath_nojs&action=contents_lpath&course_id='.$id.'&id='.$this_lpath->lpath_id.'&stu_quiz_id='.$stu_quiz_id.'&user_unique_id='.$this_lpath->unique_id.'&step_id='.$this_lpath->step_id.'&user_start_id='.$this_lpath->start_id.'&quest_id='.$seek_quest_id.'&quiz_id='.$quiz_id.'&stu_quiz_id='.$stu_quiz_id.'');								
								} else {
									$toolbar[] = array('kol_quests' => $kol_quests, 'num_quest' => $quest_num, 'quest_score' => $q_data[$i]->c_point, 'img_btn'=>'btn_start.png', 'text_btn'=>_JLMS_NEXT_ALT_TITLE);
								}	
							} else {
								if(isset($q_data[$j]->c_slide) && $q_data[$j]->c_slide){
									$toolbar[] = array('kol_quests' => $kol_quests, 'num_quest' => $quest_num, 'quest_score' => $q_data[$i]->c_point, 'img_btn'=>'btn_complete.png', 'text_btn'=>_JLMS_OK_ALT_TITLE, 'link_cont'=>'&task=show_lpath_nojs&action=contents_lpath&course_id='.$id.'&id='.$this_lpath->lpath_id.'&stu_quiz_id='.$stu_quiz_id.'&user_unique_id='.$this_lpath->unique_id.'&step_id='.$this_lpath->step_id.'&user_start_id='.$this_lpath->start_id.'&quest_id='.$seek_quest_id.'&quiz_id='.$quiz_id.'&stu_quiz_id='.$stu_quiz_id.'');
								} else {
									$toolbar[] = array('kol_quests' => $kol_quests, 'num_quest' => $quest_num, 'quest_score' => $q_data[$i]->c_point, 'img_btn'=>'btn_complete.png', 'text_btn'=>_JLMS_OK_ALT_TITLE);									
								}
							}
						} else {
							if($q_data[$i]->c_type == 10){
								if(isset($q_data[$j]->c_slide) && $q_data[$j]->c_slide){
									$toolbar[] = array('kol_quests' => $kol_quests, 'num_quest' => $quest_num, 'quest_score' => $q_data[$i]->c_point, 'img_btn'=>'btn_start.png', 'text_btn'=>_JLMS_NEXT_ALT_TITLE, 'link_cont'=>'&task=quiz_action&atask=contents&id='.$id.'&quiz='.$quiz_id.'&stu_quiz_id='.$stu_quiz_id.'&user_unique_id='.$user_unique_id.'&quest_id='.$seek_quest_id.'');
								} else {
									$toolbar[] = array('kol_quests' => $kol_quests, 'num_quest' => $quest_num, 'quest_score' => $q_data[$i]->c_point, 'img_btn'=>'btn_start.png', 'text_btn'=>_JLMS_NEXT_ALT_TITLE);	
								}
							} else {
								if(isset($q_data[$j]->c_slide) && $q_data[$j]->c_slide){
									$toolbar[] = array('kol_quests' => $kol_quests, 'num_quest' => $quest_num, 'quest_score' => $q_data[$i]->c_point, 'img_btn'=>'btn_complete.png', 'text_btn'=>_JLMS_OK_ALT_TITLE, 'link_cont'=>'&task=quiz_action&atask=contents&id='.$id.'&quiz='.$quiz_id.'&stu_quiz_id='.$stu_quiz_id.'&user_unique_id='.$user_unique_id.'&quest_id='.$seek_quest_id.'');
								} else {
									$toolbar[] = array('kol_quests' => $kol_quests, 'num_quest' => $quest_num, 'quest_score' => $q_data[$i]->c_point, 'img_btn'=>'btn_complete.png', 'text_btn'=>_JLMS_OK_ALT_TITLE);	
								}
							}
						}
						
						echo JLMS_quiz_ajax_class::JQ_toolbar_nojs($toolbar, $q_data[$i]->c_type, 1);
						echo JLMS_quiz_ajax_class::JQ_GetQuestData_nojs($q_data[$i], $jq_language, $QA->get('stu_quiz_id',0));
					}
				}
				?>
				<input type='hidden' name='stu_quiz_id' value='<?php echo $stu_quiz_id;?>'/>
				<input type='hidden' name='user_unique_id' value='<?php echo $user_unique_id;?>'/>
				
				<input type='hidden' name='atask' value='next'/>
				<input type='hidden' name='quiz' value='<?php echo $quiz_id;?>'/>
				<input type='hidden' name='task' value='quiz_action'/>
				<input type='hidden' name='id' value='<?php echo $id;?>'/>
				<input type='hidden' name='option' value='<?php echo $option;?>'/>
				<?php
			}
		}
		?>
		</form>
		
		<?php
//		return $ret_str;
	}

	function JQ_feedback_nojs($atask, $id, $quiz_id, $msg_html, $toolbar, $stu_quiz_id, $user_unique_id, $quest_id){
		global $option, $Itemid, $JLMS_CONFIG;
		?>
		<form action="<?php echo ampReplace($JLMS_CONFIG->get('live_site')."/index.php?option=$option&Itemid=$Itemid");?>" name="quest_form" method="post">
			<table width="100%" cellpadding="0" cellspacing="0" border="0" class="jlms_table_no_borders">
				<tr>
					<td>
						<?php
						JLMS_quiz_ajax_class::JQ_toolbar_nojs($toolbar);
						?>
					</td>
				</tr>
				<tr>
					<td>
						<?php
						echo $msg_html;
						?>
					</td>
				</tr>
			</table>
			
			<input type="hidden" name="user_unique_id" value="<?php echo $user_unique_id;?>"/>
			<input type="hidden" name="stu_quiz_id" value="<?php echo $stu_quiz_id;?>"/>
			<?php 
				/*
				<input type="hidden" name="quiz_quest_num" value="<?php echo $quest_num;?>"/>
				*/
			?>
			<input type="hidden" name="atask" value="<?php echo $atask;?>"/>
			<input type="hidden" name="quest_id" value="<?php echo $quest_id;?>"/>
			
			
			<input type="hidden" name="id" value="<?php echo $id;?>"/>
			<input type="hidden" name="quiz" value="<?php echo $quiz_id;?>"/>
			<input type="hidden" name="task" value="quiz_action"/>
			<input type="hidden" name="option" value="<?php echo $option;?>"/>
			
		</form>
		<?php
	}
	
	function JQ_toolbar_nojs($toolbar, $q_type=0, $no_left_info=0, $this_lpath=0){
		global $option, $Itemid, $JLMS_CONFIG;
		$atask 	= mosGetParam( $_REQUEST, 'atask', '' );
		
		$doc = & JFactory::getDocument();
		$doc->addStyleSheet( $JLMS_CONFIG->get('live_site').'/components/com_joomla_lms/includes/quiz/templates/joomlaquiz_lms_template/jq_template.css' );
		
		?>
		
		<table width="100%" cellpadding="0" cellspacing="0" border="0" class="jlms_table_no_borders">
			<tr>
				<td width="150" nowrap="nowrap">
					<?php
					if($no_left_info){
					?>
					Question <?php echo $toolbar[0]['num_quest'];?> of <?php echo $toolbar[0]['kol_quests'];?><br />
					Point value <?php echo $toolbar[0]['quest_score'];?>
					<?php
					}
					?>
				</td>
				<td width="100%" align="center">&nbsp;
					
				</td>
				<td width="150" align="right">
				<?php
				if($atask == 'review_next'){
					?>
						<table width="100%" cellpadding="0" cellspacing="3" border="0" class="jlms_table_no_borders">
							<tr>
								<td>
									<input type="image" name="btn" value="<?php echo $toolbar[0]['text_btn'];?>" src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/buttons/<?php echo $toolbar[0]['img_btn'];?>"/>							
								</td>
								<td>
									<?php echo $toolbar[0]['text_btn'];?>
								</td>
								<?php
								if(isset($toolbar[0]['link_cont'])){
									$link_cont = sefRelToAbs("index.php?option=$option&Itemid=$Itemid".$toolbar[0]['link_cont']."");
								?>
								<td>
									<a href="<?php echo $link_cont;?>">
										<img class="JLMS_png" width="32" height="32" border="0" title="<?php echo _JLMS_CONTENTS_ALT_TITLE;?>" alt="<?php echo _JLMS_CONTENTS_ALT_TITLE;?>" src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/buttons/btn_contents.png"/>
									</a>
								</td>
								<td>
									<a href="<?php echo $link_cont;?>">
										<?php echo _JLMS_CONTENTS_ALT_TITLE;?>
									</a>
								</td>
								<?php
								}
								?>
							</tr>
						</table>
					<?php
				} else {
					if($q_type != 7){
						
					?>
						<table width="100%" cellpadding="0" cellspacing="3" border="0" class="jlms_table_no_borders">
							<tr>
								<td>
									<input type="image" name="btn" value="<?php echo $toolbar[0]['text_btn'];?>" src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/buttons/<?php echo $toolbar[0]['img_btn'];?>"/>							
								</td>
								<td>
									<?php echo $toolbar[0]['text_btn'];?>
								</td>
								<?php
								if(isset($toolbar[0]['link_cont'])){
									$link_cont = sefRelToAbs("index.php?option=$option&Itemid=$Itemid".$toolbar[0]['link_cont']."");
								?>
								<td>
									<a href="<?php echo $link_cont;?>">
										<img class="JLMS_png" width="32" height="32" border="0" title="<?php echo _JLMS_CONTENTS_ALT_TITLE;?>" alt="<?php echo _JLMS_CONTENTS_ALT_TITLE;?>" src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/buttons/btn_contents.png"/>
									</a>
								</td>
								<td>
									<a href="<?php echo $link_cont;?>">
										<?php echo _JLMS_CONTENTS_ALT_TITLE;?>
									</a>
								</td>
								<?php
								}
								?>
							</tr>
						</table>
					<?php
					}
				}
				?>	
				</td>
			</tr>
		</table>
		<?php	
	}
	
	function JQ_Content_nojs(){
		global $option, $Itemid, $JLMS_DB;
		$JLMS_CONFIG = & JLMSFactory::getConfig();

		$id = intval( mosGetParam( $_REQUEST, 'id', 0 ) );
		$quiz_id = intval( mosGetParam( $_REQUEST, 'quiz', 0 ) );
		$stu_quiz_id = intval( mosGetParam( $_REQUEST, 'stu_quiz_id', 0 ) );
		$user_unique_id = strval( mosGetParam( $_REQUEST, 'user_unique_id', '' ) );
		$quest_id = intval( mosGetParam( $_REQUEST, 'quest_id', 0 ) );
		
		$inside_lp = intval( mosGetParam( $_REQUEST, 'inside_lp', 0 ) );
		$QA = new JLMS_quiz_API($quiz_id, $inside_lp);
		if (!$QA->quiz_valid()) {
			return '';
		}
		
		$QA->set('stu_quiz_id', $stu_quiz_id);
		$QA->set('user_unique_id', $user_unique_id);
		$QA->quiz_ProcessStartData();
		
		$quiz = $QA->quiz_data;// temporary for compatibility
		$quiz_params = new JLMSParameters($QA->get_qvar('params'));
		$q_data = $QA->quiz_Get_QuestionList();
		
		$link_back = sefRelToAbs("index.php?option=$option&Itemid=$Itemid&id=$id&task=quiz_action&quiz=$quiz_id&atask=goto_quest&stu_quiz_id=$stu_quiz_id&user_unique_id=$user_unique_id&seek_quest_id=$quest_id");
		?>
		<table border="0" width="100%" class="jlms_table_no_borders">
			<tr>
				<td align="right">
					<table class="jlms_table_no_borders">
						<tr>
							<td>
								<a href="<?php echo $link_back;?>">
									<img src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/buttons/btn_back.png" border="0" alt=""/>
								</a>
							</td>
							<td>
								<a href="<?php echo $link_back;?>">
									<?php echo _JLMS_q_quiz_back;?>
								</a>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td align="center">
					<table width="100%" class="jlms_table_no_borders" style="padding: 0px 20px;" id="jq_results_panel_table">
					<?php
						$i = 1;
						foreach($q_data as $data){
							$link_goto_quest = sefRelToAbs("index.php?option=$option&Itemid=$Itemid&id=$id&task=quiz_action&atask=goto_quest&quiz=$quiz_id&seek_quest_id=$data->c_id&stu_quiz_id=$stu_quiz_id&user_unique_id=$user_unique_id");
					?>
						<tr class="sectiontableentry<?php echo $i;?>">
							<td>
								<a href="<?php echo $link_goto_quest;?>">
									<?php echo $data->c_question;?>
								</a>
							</td>
							<td width="40" align="center">
								<?php echo $data->c_point;?>
							</td>
							<td width="25" align="center">
								<div id="quest_result_<?php echo $data->c_id;?>">
									<?php
									if(isset($data->c_correct) && $data->c_correct){
										if($data->c_correct == 1){
										?>
										<img border="0" src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_cancel.png" alt=""/>
										<?php
										} elseif($data->c_correct == 2){
										?>
										<img border="0" src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_accept.png" alt=""/>
										<?php
										}
									} else {
										if(isset($data->c_score) && $data->c_score > 0){
										?>
										<img border="0" src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_cancel.png" alt=""/>
										<?php
										} elseif(isset($data->c_score) && $data->c_score > 0){
										?>
										<img border="0" src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_accept.png" alt=""/>
										<?php
										} else {
										?>
										-
										<?php	
										}									
									}
									?>
								</div>
							</td>
						</tr>
					<?php
							if($i == 2){
								$i = $i - 1;
							} else {
								$i++;	
							}	
						}
					?>
					</table>
				</td>
			</tr>
		</table>
		<?php
		
	}
	
	/*
	2 - correct answer
	1 - incorect answer
	0 - neproyden
	*/
	function correct_answer($is_c){
		$correct=0;
		if($is_c){
			$correct = 2;
		} else {
			$correct = 1;
		}
		return $correct;		
	}
	

}
?>