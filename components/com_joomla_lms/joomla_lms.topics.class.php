<?php
/**
* joomla_lms.topics.class.php
* (c) JoomaLMS eLearning Software http://www.joomlalms.com/
**/

class JLMS_Course_HomePage {
	var $course_id	= null; //stores id of shown course
	var $topics		= null; //srores array of topics in the course
	var $elements	= null; //stores all elements of course
	var $links		= array(); //stores links on elements of every topic

	function JLMS_Course_HomePage($course_id, $add_elements_page = false) {
		global $option, $my, $JLMS_DB, $Itemid, $JLMS_CONFIG, $JLMS_SESSION;
		//TODO: replace global option declaration
		$usertype = $JLMS_CONFIG->get('current_usertype', 0);
		$JLMS_ACL = & JLMSFactory::getACL();
		$is_teacher = $JLMS_ACL->isTeacher(); 
		
		$AND_ST = "";
		if( false !== ( $enroll_period = JLMS_getEnrolPeriod( $my->id, $course_id )) ) 
		{
			$AND_ST = " AND IF(is_time_related, (show_period < '".$enroll_period."' ), 1) ";	
		}

		$this->course_id = $course_id;
		$curr_date = date("Y-m-d");
		$is_curtopic = intval(mosgetparam($_REQUEST,'t_id',0));
		//create list of topics
		if ($usertype == 1) $published = '1';
		else $published = "published=1 AND (publish_start=0 OR start_date<='$curr_date') AND (publish_end=0 OR end_date>='$curr_date')";
		$query = "SELECT * FROM #__lms_topics WHERE course_id=$course_id ".$AND_ST." AND $published ".($is_curtopic?" AND id=".$is_curtopic:"")." ORDER BY ordering";
		$JLMS_DB->setQuery($query);
		$this->topics = $JLMS_DB->loadObjectList('id');
		
		//get items of the course
		//2 - documents
		if ($add_elements_page) {
			$rows = array();
			$possibilities = new stdClass();
			JLMSDocs::FillList($course_id, $rows, $possibilities, false, false);
			$this->elements[_DOCUMENT_ID] = JLMSDocs::GetItemsbyPermission($rows, 'manage');
		} else {
			$AND_ST_D = "";
			if( false !== ( $enroll_period = JLMS_getEnrolPeriod( $my->id, $course_id )) ) 
			{
				$AND_ST_D = " AND IF(a.is_time_related, (a.show_period < '".$enroll_period."' ), 1) ";	
			}
		
			$query = "SELECT a.*, b.file_name FROM #__lms_documents as a LEFT JOIN #__lms_files as b ON a.file_id = b.id AND a.folder_flag = 0 WHERE a.course_id=$course_id".$AND_ST_D;
			$JLMS_DB->setQuery($query);
			$this->elements[_DOCUMENT_ID] = $JLMS_DB->loadObjectList('id');
			foreach($this->elements[_DOCUMENT_ID] as $elem){
				if($elem->folder_flag == 3){
					$query = "SELECT a.*, b.file_name"
						. "\n FROM #__lms_outer_documents as a LEFT JOIN #__lms_files as b ON a.file_id = b.id AND a.folder_flag = 0 "
						. "\n WHERE a.folder_flag = 0 AND a.id = ".$elem->file_id;
					$JLMS_DB->SetQuery( $query );
					$out_row = $JLMS_DB->LoadObjectList();

					if(count($out_row) &&  isset($out_row[0]->allow_link) && $out_row[0]->allow_link == 1) {
						$this->elements[_DOCUMENT_ID][$elem->id]->doc_name = $out_row[0]->doc_name;
						$this->elements[_DOCUMENT_ID][$elem->id]->doc_description = $out_row[0]->doc_description;
						$this->elements[_DOCUMENT_ID][$elem->id]->file_id = $out_row[0]->file_id;
						$this->elements[_DOCUMENT_ID][$elem->id]->file_name = $out_row[0]->file_name;
					} else {
						unset($this->elements[_DOCUMENT_ID][$elem->id]);
					}
				}
			}
			$this->elements[_DOCUMENT_ID] = AppendFileIcons_toList($this->elements[_DOCUMENT_ID]);
		}
		//get max tree level
		$this->max_lvl = getDirNesting($this->elements[_DOCUMENT_ID], 0);
		global $max_lvl;
		$max_lvl = $this->max_lvl;

		//3 - links
		$query = "SELECT * FROM #__lms_links WHERE course_id=$course_id".$AND_ST;
		$JLMS_DB->setQuery($query);
		$this->elements[_LINK_ID] = $JLMS_DB->loadObjectList('id');
		//4 - contents ... er... TODO

		//5 - quizs
		$query = "SELECT *, c_id AS id FROM #__lms_quiz_t_quiz WHERE course_id=$course_id".$AND_ST;
		$JLMS_DB->setQuery($query);
		$this->elements[_QUIZ_ID] = $JLMS_DB->loadObjectList('c_id');
		$quizzes_i = array();
		foreach ($this->elements[_QUIZ_ID] as $row) {
			$quizzes_i[] = $row->c_id;
		}

		//add results for student
		if ($usertype == 2 && count($this->elements[_QUIZ_ID])) {
			$quizzes_r = array();
			foreach ($this->elements[_QUIZ_ID] as $row) {
				$quizzes_r[] = $row->c_id;
			}
			$q_items_num = array();
			$extra_info_cells = array();
			if (!empty($quizzes_r)) {
				$quizzes_r_cid = implode(',', $quizzes_r);
				$query = "SELECT * FROM #__lms_quiz_results WHERE quiz_id IN ($quizzes_r_cid) AND course_id=$course_id AND user_id={$my->id} GROUP BY quiz_id";
				$JLMS_DB->SetQuery($query);
				$extra_info_cells = $JLMS_DB->loadObjectList();
			}
			foreach ($this->elements[_QUIZ_ID] as $i => $value) {
				$this->elements[_QUIZ_ID][$i]->start_date = '-';
				$this->elements[_QUIZ_ID][$i]->end_date = '-';
				$this->elements[_QUIZ_ID][$i]->status = -1;
				$this->elements[_QUIZ_ID][$i]->user_passed = -1;
				$this->elements[_QUIZ_ID][$i]->points = -1;
				$this->elements[_QUIZ_ID][$i]->user_score = 0;
				$this->elements[_QUIZ_ID][$i]->quiz_max_score = 0;
				foreach ($extra_info_cells as $cell) {
					if ($cell->quiz_id == $this->elements[_QUIZ_ID][$i]->c_id) {
						$this->elements[_QUIZ_ID][$i]->start_date = JLMS_dateToDisplay($cell->quiz_date, false, ($JLMS_CONFIG->get('offset')*60*60 - $cell->user_time), '\<\b\\r \/>H:i:s');
						$this->elements[_QUIZ_ID][$i]->end_date = JLMS_dateToDisplay($cell->quiz_date, false, $JLMS_CONFIG->get('offset')*60*60, '\<\b\\r \/>H:i:s');
						$this->elements[_QUIZ_ID][$i]->points = $cell->user_score;
						$this->elements[_QUIZ_ID][$i]->status = $cell->user_passed;
						$this->elements[_QUIZ_ID][$i]->user_passed = $cell->user_passed;
						$this->elements[_QUIZ_ID][$i]->user_score = $cell->user_score;
						$this->elements[_QUIZ_ID][$i]->quiz_max_score = $cell->quiz_max_score;

					}
				}
			}
			unset($extra_info_cells);
			require_once(_JOOMLMS_FRONT_HOME . DS . "includes" . DS . "lms_certificates.php");
			$arr = array();
			JLMS_Certificates::JLMS_GB_getUserCertificates($course_id, $my->id, $arr);
			$arr1 = isset($arr['user_quiz_certificates']) ? $arr['user_quiz_certificates'] : array();
			foreach ($this->elements[_QUIZ_ID] as $i => $value) {
				for($j=0;$j<count($arr1);$j++) {
					if($arr1[$j]->c_quiz_id == $this->elements[_QUIZ_ID][$i]->c_id) {
						$this->elements[_QUIZ_ID][$i]->link_certificate = "<a target = \"_blank\" href = \"".$JLMS_CONFIG->get('live_site')."/index.php?tmpl=component&option=".$option."&amp;no_html=1&amp;task=print_quiz_cert&amp;course_id=".$course_id."&amp;stu_quiz_id=".$arr1[$j]->stu_quiz_id."&amp;user_unique_id=".$arr1[$j]->user_unique_id."\"><img src = \"".$JLMS_CONFIG->get('live_site')."/components/com_joomla_lms/lms_images/toolbar/btn_certificate.png\" border = \"0\" align=\"top\" alt=\"certificate\"/></a>";
					}
				}
			}
		}
		//6 - scorm ... er... TODO

		//7 - learning paths
		$lpaths = array();
		if ( $course_id && $usertype ) {
			//$JLMS_SESSION->clear('redirect_to_learnpath');
			if ($usertype == 1) {
				$query = "SELECT * FROM #__lms_learn_paths WHERE course_id = '".$course_id."'".$AND_ST
				."\n ORDER BY ordering";
			} elseif( $usertype == 2) {
				//$query = "SELECT a.*, b.user_status as r_status, b.start_time as r_start, b.end_time as r_end"
				//. "\n FROM #__lms_learn_paths as a LEFT JOIN #__lms_learn_path_results as b ON a.id = b.lpath_id AND b.course_id = '".$course_id."' AND b.user_id = '".$my->id."'"
				$query = "SELECT a.*, '' as r_status, '' as r_start, '' as r_end"
				. "\n FROM #__lms_learn_paths as a"
				. "\n WHERE a.course_id = '".$course_id."'"
				. "\n AND a.published = 1"
				."\n ORDER BY a.ordering";
			}
			$JLMS_DB->SetQuery( $query );
			$lpaths = $JLMS_DB->LoadObjectList();

			if ($usertype == 2) {
				$user_ids = array();
				$user_ids[] = $my->id;
				require_once(_JOOMLMS_FRONT_HOME . "/includes/lms_grades.lib.php");
				JLMS_LP_populate_results($course_id, $lpaths, $user_ids);

				// 13 August 2007 (DEN) Check for prerequisites.
				// 1. get the list of lpath_ids.
				$lpath_ids = array();
				foreach ($lpaths as $lpath) {
					$lpath_ids[] = $lpath->id;
				}
				if (!empty($lpath_ids)) {
					$lpath_ids_str = implode(',', $lpath_ids);
					// 2. get the list of prerequisites
					// SELECT from two tables (+ #__lms_learn_paths) - because the prereq lpath could be deleted...
					$query = "SELECT a.* FROM #__lms_learn_path_prerequisites as a, #__lms_learn_paths as b"
					. "\n WHERE a.lpath_id IN ($lpath_ids_str) AND a.req_id = b.id";
					$JLMS_DB->SetQuery($query);
					$prereqs = $JLMS_DB->LoadObjectList();
					if (!empty($prereqs)) {
						// 3. compare lists of prereqs to the lists of lpaths.
						$i = 0;
						while ($i < count($lpaths)) {
							$is_hidden = false;
							$o = 0;
							while ($o < count($prereqs)) {
								if ($prereqs[$o]->lpath_id == $lpaths[$i]->id) {
									$j = 0;
									while ($j < count($lpaths)) {
										if ($lpaths[$j]->id == $prereqs[$o]->req_id) {
											if (!$lpaths[$j]->item_id) {
												if (empty($lpaths[$j]->r_status)) {
													$is_hidden = true;
													break;
												} else {
													$end_time = strtotime($lpaths[$j]->r_end);
													$current_time = strtotime(date("Y-m-d H:i:s"));
													if($current_time > $end_time && (($current_time - $end_time) < ($prereqs[$o]->time_minutes*60))){
														$is_hidden = true;
														break;	
													}
												}
											} else {
												if (empty($lpaths[$j]->s_status)) {
													$is_hidden = true;
													break;
												} else {
													$end_time = strtotime($lpaths[$j]->r_end);
													$current_time = strtotime(date("Y-m-d H:i:s"));
													if($current_time > $end_time && (($current_time - $end_time) < ($prereqs[$o]->time_minutes*60))){
														$is_hidden = true;
														break;	
													}
												}
											}
										}
										$j ++;
									}
								}
								$o ++;
							}
							$lpaths[$i]->is_hidden = $is_hidden;
							$i ++;
						}
					}
				}
			}
		}
		foreach ($lpaths as $lpath) {
			$this->elements[_LPATH_ID][$lpath->id] = $lpath;
		}

		//populate topics with links to their items

		$query = "SELECT * FROM #__lms_topic_items WHERE course_id=$course_id ORDER BY ordering";
		$JLMS_DB->setQuery($query);
		$items = $JLMS_DB->loadObjectList();
						
		for($i=0;$i<count($items);$i++) {
			
			if($items[$i]->item_type == 7) {
				
				$query = "SELECT lp_type FROM #__lms_learn_paths WHERE id = '".$items[$i]->item_id."'".$AND_ST;
				$JLMS_DB->SetQuery( $query );
				$lp_type = $JLMS_DB->LoadResult();
				
				if($lp_type == 2) {
				
					$query = "SELECT item_id FROM #__lms_learn_paths WHERE id = '".$items[$i]->item_id."'";
					$JLMS_DB->SetQuery( $query );
					$learn_path_id = $JLMS_DB->LoadResult();
					
					$query = "SELECT scorm_package, params as scorm_params, width as scorm_width, height as scorm_height FROM #__lms_n_scorm WHERE id = '".$learn_path_id."'";
					$JLMS_DB->SetQuery( $query );
					$outer_doc = null;
					$scorm_info = null;
					$scorm_info = $JLMS_DB->LoadObject();
					if (is_object($scorm_info)) {
						$this->elements[_LPATH_ID][$items[$i]->item_id]->scorm_params = $scorm_info->scorm_params;
						$this->elements[_LPATH_ID][$items[$i]->item_id]->scorm_width = $scorm_info->scorm_width;
						$this->elements[_LPATH_ID][$items[$i]->item_id]->scorm_height = $scorm_info->scorm_height;
						$scorm_package = $scorm_info->scorm_package;
						
						$query = "SELECT id FROM #__lms_n_scorm WHERE scorm_package = '".$scorm_package."' AND course_id = 0";
						$JLMS_DB->SetQuery( $query );
						$scorm_lib_id = $JLMS_DB->LoadResult();	
						
						$query = "SELECT outdoc_share, owner_id, allow_link FROM #__lms_outer_documents WHERE file_id = '".$scorm_lib_id."' AND folder_flag = 3";
						$JLMS_DB->SetQuery( $query );
						$outer_doc = $JLMS_DB->LoadObject();	
					}
					if(is_object($outer_doc) && isset($outer_doc->allow_link) && $outer_doc->allow_link == 1 ) {
						// 01May2009: new library policy: if 'allow_link' is enabled - we can view already added resource !
					} else {
						unset($items[$i]);
					}
				} elseif ($lp_type == 1) {
					$query = "SELECT item_id FROM #__lms_learn_paths WHERE id = '".$items[$i]->item_id."'";
					$JLMS_DB->SetQuery( $query );
					$learn_path_id = $JLMS_DB->LoadResult();
					if ($learn_path_id) {
						$query = "SELECT params as scorm_params, width as scorm_width, height as scorm_height FROM #__lms_n_scorm WHERE id = '".$learn_path_id."'";
						$JLMS_DB->SetQuery( $query );
						$scorm_info = null;
						$scorm_info = $JLMS_DB->LoadObject();
						if (is_object($scorm_info)) {
							$this->elements[_LPATH_ID][$items[$i]->item_id]->scorm_params = $scorm_info->scorm_params;
							$this->elements[_LPATH_ID][$items[$i]->item_id]->scorm_width = $scorm_info->scorm_width;
							$this->elements[_LPATH_ID][$items[$i]->item_id]->scorm_height = $scorm_info->scorm_height;
						}
					}
				}
			}
		}
				
		if( !$is_teacher ) 
		{
			$this->_filterItemsByShowPeriod( $items, $course_id );
		}

		$mas = array();
		foreach ($items as $k=>$v) {
			$mas[] = $items[$k];	
		}
		unset($lpath);
		$items = $mas;
		
		$links = array();
		foreach ($items as $item) {
			$item_tmp->id = $item->item_id;
			$item_tmp->type = $item->item_type;
			$item_tmp->ordering = $item->ordering;
			$links[$item->topic_id][] = $item;
		}
		$this->links = $links;		
	}
	
	function _filterItemsByShowPeriod( & $items, $course_id ) 
	{
		global $my, $JLMS_DB;
		
		if( isset($this->elements[_DOCUMENT_ID]) )		
			$docIds		= array_keys($this->elements[_DOCUMENT_ID]);
		else
			$docIds = array();
			
		if( isset($this->elements[_LINK_ID]) )	
			$linkIds	= array_keys($this->elements[_LINK_ID]);
		else 
			$linkIds	= array();
		
		if( isset($this->elements[_QUIZ_ID]) )
			$quizIds	= array_keys($this->elements[_QUIZ_ID]);
		else
			$quizIds	= array();
		
		if( isset($this->elements[_LPATH_ID]) )
			$lpathIds	= array_keys($this->elements[_LPATH_ID]);
		else 
			$lpathIds = array();
						
		for( $i = 0; $i < count($items); $i++ ) 
		{
			$item = $items[$i];
			
			switch( $item->item_type ) 
			{
				case '2': 
					if( !in_array( $item->item_id, $docIds ) )
						unset($items[$i]);
					break;//document
				case '3': 
					if( !in_array( $item->item_id, $linkIds ) )
						unset($items[$i]);
					break;//link				
				case '5': 
					if( !in_array( $item->item_id, $quizIds ) )
						unset($items[$i]);
					break;//quiz
				case '7': 
					if( !in_array( $item->item_id, $lpathIds ) )
						unset($items[$i]);
					break;//lpath				 
			}
		}
		
		$items = array_values( $items );
	}

	function listTopics () {
		JLMS_topic_html::showTopicsList($this->course_id, $this->topics, $this->links, $this->elements, $this->max_lvl);
	}

	function listElements ($topic_id) {
		global $my, $JLMS_DB;

		$JLMS_ACL = & JLMSFactory::getACL();
		$is_teacher = $JLMS_ACL->isTeacher(); 

		//create list of elements already linked to topic
		if (@count($this->links[$topic_id])) {
			foreach ($this->links[$topic_id] as $topic_link) {
				$linked_elements[$topic_link->item_type][] = $topic_link->item_id;
			}
		} else {
			$linked_elements = array();
		}

		foreach($this->elements as $k=>$v) {
			if($k == 7) {
				foreach ($v as $n=>$m) {
					if($m->lp_type == 2) {
						$lib_link_is_found = false;
						if (isset($m->item_id) && $m->item_id) {
							$learn_path_id = $m->item_id;
							$query = "SELECT scorm_package FROM #__lms_n_scorm WHERE id = '".$learn_path_id."'";
							$JLMS_DB->SetQuery( $query );
							$scorm_package = $JLMS_DB->LoadResult();
							if ($scorm_package) {
								$query = "SELECT id FROM #__lms_n_scorm WHERE scorm_package = '".$scorm_package."' AND course_id = 0";
								$JLMS_DB->SetQuery( $query );
								$scorm_lib_id = $JLMS_DB->LoadResult();
								if ($scorm_lib_id) {
									$query = "SELECT outdoc_share, owner_id, allow_link FROM #__lms_outer_documents WHERE file_id = '".$scorm_lib_id."' AND folder_flag = 3";
									$JLMS_DB->SetQuery( $query );
									$outer_doc = $JLMS_DB->LoadObject();
									if (is_object($outer_doc)) {
										$lib_link_is_found = true;
										if(isset($outer_doc->allow_link) && $outer_doc->allow_link == 1 ) {
											// 01May2009: new library policy: if 'allow_link' is enabled - we can view already added resource !
										} else {
											unset($m);	
										}
									}
								}
							}
						}
						if (!$lib_link_is_found) {
							unset($m);
						}
					}
				}
			}
		}

		//call output method
		JLMS_topic_html::showElementsList($this->course_id, $topic_id, $this->elements, $linked_elements);
	}
}
class JLMS_Topic extends JLMSDBTable {
	var $id				= null;
	var $course_id		= null;
	var $ordering		= 9999;
	var $name			= null;
	var $description	= null;
	var $published		= null;
	var $publish_start	= 0;
	var $publish_end	= 0;
	var $start_date		= null;
	var $end_date		= null;
	var $is_time_related= null;
	var $show_period	= null;
	
	function JLMS_Topic(&$db) {
		$this->JLMSDBTable( '#__lms_topics', 'id', $db );
	}
}
class publishOptionClass {
	var $state = null;
	var $show = null;
	var $image = null;
	var $alt = null;
	var $pending_expired = null; //-1 for pending and 1 for expired
}
?>