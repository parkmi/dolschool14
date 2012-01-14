<?php
/**
* jlms_course_export.php
* (c) JoomaLMS eLearning Software http://www.joomlalms.com/
**/

defined( '_JLMS_EXEC' ) or die( 'Restricted access' );

function JLMS_courseExport ($option, $course_id, $type, $cid = array(1,2,3,4,5,6,7,8), $selected_resources = null, $related_to_selected_resources = false ){
	global $JLMS_DB, $JLMS_CONFIG;
	/*
	1 => 'Documents'
	2 => 'Learning Paths'
	3 => 'SCORMs'
	4 => 'Links'
	5 => 'Quizzes'
	6 => 'Announcements'
	7 => 'Homework'
	8 => 'Gradebook (settings only)'
	*/
	if (count($cid) == 1) {
		if (in_array(2,$cid)) {
			if (is_null($selected_resources)) {
			} elseif (isset($selected_resources[2]) && is_array($selected_resources[2]) && count($selected_resources[2]) ) {
				$all_exp_lpath_ids = implode(',', $selected_resources[2]);
				$query = "SELECT distinct item_id FROM #__lms_learn_path_steps WHERE course_id = $course_id AND lpath_id IN ($all_exp_lpath_ids) AND step_type = 2";
				$JLMS_DB->SetQuery($query);
				$all_docs_in_lps = $JLMS_DB->LoadresultArray();
				if (count($all_docs_in_lps)) {
					if (in_array(1,$cid)) {
					} else {
						$cid[] = 1;
					}
					if (isset($selected_resources[1])) {
					} else {
						$selected_resources[1] = array();
					}
					$selected_resources[1] = array_merge($selected_resources[1],$all_docs_in_lps);
					$selected_resources[1] = array_unique($selected_resources[1]);
				}
				$query = "SELECT distinct item_id FROM #__lms_learn_path_steps WHERE course_id = $course_id AND lpath_id IN ($all_exp_lpath_ids) AND step_type = 3";
				$JLMS_DB->SetQuery($query);
				$all_links_in_lps = $JLMS_DB->LoadresultArray();
				if (count($all_links_in_lps)) {
					if (in_array(4,$cid)) {
					} else {
						$cid[] = 4;
					}
					if (isset($selected_resources[4])) {
					} else {
						$selected_resources[4] = array();
					}
					$selected_resources[4] = array_merge($selected_resources[4],$all_links_in_lps);
					$selected_resources[4] = array_unique($selected_resources[4]);
				}
				$query = "SELECT distinct item_id FROM #__lms_learn_path_steps WHERE course_id = $course_id AND lpath_id IN ($all_exp_lpath_ids) AND step_type = 5";
				$JLMS_DB->SetQuery($query);
				$all_qz_in_lps = $JLMS_DB->LoadresultArray();
				if (count($all_qz_in_lps)) {
					if (in_array(5,$cid)) {
					} else {
						$cid[] = 5;
					}
					if (isset($selected_resources[5])) {
					} else {
						$selected_resources[5] = array();
					}
					$selected_resources[5] = array_merge($selected_resources[5],$all_qz_in_lps);
					$selected_resources[5] = array_unique($selected_resources[5]);
				}
				/*$query = "SELECT distinct item_id FROM #__lms_learn_path_steps WHERE course_id = $course_id AND lpath_id IN ($all_exp_lpath_ids) AND step_type = 6";
				$JLMS_DB->SetQuery($query);
				$all_scorms_in_lps = $JLMS_DB->LoadresultArray();
				if (count($all_scorms_in_lps)) {
					if (in_array(3,$cid)) {
					} else {
						$cid[] = 3;
					}
					if (isset($selected_resources[3])) {
					} else {
						$selected_resources[3] = array();
					}
					$selected_resources[3] = array_merge($selected_resources[3],$all_scorms_in_lps);
					$selected_resources[3] = array_unique($selected_resources[3]);
				}*/
			}
		}
	}

	if (!empty($cid)) {
	require_once ($JLMS_CONFIG->getCfg('absolute_path').'/components/com_joomla_lms/joomla_lms.main.php');
	@set_time_limit('3000'); // 50 minutes? ;)
	require_once(_JOOMLMS_FRONT_HOME . "/includes/libraries/lms.lib.zip.php");

	$query = "SELECT a.*, b.lang_name, c.c_category, '' as course_question FROM `#__lms_courses` as a"
	. "\n LEFT JOIN `#__lms_languages` as b ON a.language = b.id"
	. "\n LEFT JOIN `#__lms_course_cats` as c ON a.cat_id = c.id"
#	. "\n LEFT JOIN `#__lms_spec_reg_questions` as q ON q.course_id = a.id AND a.spec_reg = 1"
	. "\n WHERE a.id = '$course_id'";
	$JLMS_DB->setQuery($query);
	$course = $JLMS_DB->loadObject();
	if (is_object($course) && isset($course->id)) {
		//ok
	} else {
		exit;
	}

	$course_questions = array();
	$query = "SELECT * FROM #__lms_spec_reg_questions WHERE course_id = $course_id AND role_id = 0";
	$JLMS_DB->setQuery($query);
	$course_questions = $JLMS_DB->loadObjectList();

	$documents1 = array();
	$docs_collapsed = array();
	if (in_array(1,$cid)) {
		if (is_null($selected_resources)) {
			$query = "SELECT a.* FROM `#__lms_documents` as a WHERE a.course_id = $course_id AND a.folder_flag IN (0,1,2)";
			$JLMS_DB->setQuery($query);
			$documents1 = $JLMS_DB->loadObjectList();
			$documents1 = JLMS_GetTreeStructure($documents1);
		} else {
			if ( isset($selected_resources[1]) && is_array($selected_resources[1]) &&  count($selected_resources[1]) ) {
				$p_str_docs = implode(',', $selected_resources[1]);
			} else {
				$p_str_docs = '0';
			}
			$query = "SELECT a.* FROM `#__lms_documents` as a WHERE a.id IN ($p_str_docs) AND a.course_id = $course_id AND a.folder_flag IN (0,1,2)";
			$JLMS_DB->setQuery($query);
			$new_documents_pre1 = $JLMS_DB->loadObjectList();
			$counter_to_avoid_loop = 0;
			$documents1 = array();
			while (count($new_documents_pre1) && $counter_to_avoid_loop < 10) { // 10 - hope, nobody will not create more than 10 sub-levels of directories
				$parents_docspre = array();
				foreach ($new_documents_pre1 as $document_pre1) {
					if ($document_pre1->parent_id) {
						$parents_docspre[] = $document_pre1->parent_id;
						$documents1[] = $document_pre1;
					} else {
						$documents1[] = $document_pre1;
					}
				}
				if (count($parents_docspre)) {
					$parents_docspre_str = implode(',', $parents_docspre);
					$query = "SELECT a.* FROM `#__lms_documents` as a WHERE a.id IN ($parents_docspre_str) AND a.course_id = $course_id AND a.folder_flag IN (0,1,2)";
					$JLMS_DB->setQuery($query);
					$new_documents_pre1 = $JLMS_DB->loadObjectList();
				} else {
					$new_documents_pre1 = array();
				}
				$counter_to_avoid_loop ++;
			}
			$documents1 = JLMS_GetTreeStructure($documents1);
		}
		// 27.0.3.2008 (DEN) - " AND a.folder_flag IN (0,1,2)" - Documents from Library are not exported !

		$zip_docs_ids = array();
		$folder_ids = array();
		foreach ($documents1 as $doc1) {
			if ($doc1->folder_flag == 2) {
				$zip_docs_ids[] = $doc1->file_id;
			} elseif ($doc1->folder_flag == 1 && $doc1->file_id == 0) {
				$folder_ids[] = $doc1->id;
			}
		}
		if (!empty($folder_ids)) {
			$fids = implode(',',$folder_ids);
			$query = "SELECT doc_id FROM #__lms_documents_view WHERE course_id = $course_id AND doc_id IN ($fids)";
			$JLMS_DB->SetQuery($query);
			$docs_collapsed = $JLMS_DB->LoadResultArray();
		}
		$zipped_docs = array();
		if (count($zip_docs_ids)) {
			$zdis = implode(',', $zip_docs_ids);
			$query = "SELECT * FROM `#__lms_documents_zip` WHERE id IN ($zdis) AND course_id = $course_id";
			$JLMS_DB->setQuery($query);
			$zipped_docs = $JLMS_DB->loadObjectList();
		}
	}

	$links = array();
	if (in_array(4,$cid)) {
		if (is_null($selected_resources)) {
			$query = "SELECT * FROM `#__lms_links` WHERE course_id = $course_id";
			$JLMS_DB->setQuery($query);
			$links = $JLMS_DB->loadObjectList();
		} else {
			if ( isset($selected_resources[4]) && is_array($selected_resources[4]) &&  count($selected_resources[4]) ) {
				$p_str_links = implode(',', $selected_resources[4]);
			} else {
				$p_str_links = '0';
			}
			$query = "SELECT * FROM `#__lms_links` WHERE id IN ($p_str_links) AND course_id = $course_id";
			$JLMS_DB->setQuery($query);
			$links = $JLMS_DB->loadObjectList();
		}
	}


	// 27.03.2008 (DEN) - Course topics processing
	$processed_docs = array();	
	$processed_links = array();
	$processed_lpaths = array();
	$processed_quizzes = array();
	if (is_null($selected_resources)) {
		$query = "SELECT * FROM `#__lms_topics` WHERE course_id = $course_id";
		$JLMS_DB->setQuery($query);
		$topics = $JLMS_DB->loadObjectList();
		$query = "SELECT * FROM `#__lms_topic_items` WHERE course_id = $course_id AND item_type IN (2,3,5,7)";
		$JLMS_DB->setQuery($query);
		$topic_items = $JLMS_DB->loadObjectList();
	} else {
		//only selcted resources - e.g. export of specific lpath, we don't need topics here
		$topics = array();
		$topic_items = array();
	}

	$homeworks = array();
	if (in_array(7,$cid)) {
		$query = "SELECT * FROM `#__lms_homework` WHERE course_id = $course_id";
		$JLMS_DB->setQuery($query);
		$homeworks = $JLMS_DB->loadObjectList();
	}

	$announcements = array();
	if (in_array(6,$cid)) {
		$query = "SELECT * FROM `#__lms_agenda` WHERE course_id = $course_id";
		$JLMS_DB->setQuery($query);
		$announcements = $JLMS_DB->loadObjectList();
	}
	
	if (is_null($selected_resources)) {
		$query = "SELECT * FROM `#__lms_local_menu` WHERE course_id = $course_id";
		$JLMS_DB->setQuery($query);
		$hidden_menus = $JLMS_DB->loadObjectList();
	} else {
		$hidden_menus = array();
	}

	// TIP (28 April 2007) We've export only SCORM package.
	// Instance in table 'lms_n_scorm' will be added automatically on first activation of SCORM.
	if (in_array(3,$cid)) {
		$query = "SELECT * FROM `#__lms_scorm_packages` WHERE course_id = $course_id";
		$JLMS_DB->setQuery($query);
		$scorms = $JLMS_DB->loadObjectList();
	}

	$lpaths = array();
	if (in_array(2,$cid) || in_array(3,$cid)) {
		if (in_array(2,$cid)) {
			if (is_null($selected_resources)) {
				$query = "SELECT * FROM `#__lms_learn_paths` WHERE course_id = $course_id" . ( (in_array(3,$cid)) ? '' : " AND item_id = 0");
			} else {
				if ( is_array($selected_resources[2]) &&  count($selected_resources[2]) ) {
					$p_str_lpaths = implode(',', $selected_resources[2]);
				} else {
					$p_str_lpaths = '0';
				}
				$query = "SELECT * FROM `#__lms_learn_paths` WHERE id IN ($p_str_lpaths) AND course_id = $course_id" . ( (in_array(3,$cid)) ? '' : " AND item_id = 0");
			}
		} else {
			$query = "SELECT * FROM `#__lms_learn_paths` WHERE course_id = $course_id AND item_id <> 0";
		}
		$JLMS_DB->setQuery($query);
		$lpaths = $JLMS_DB->loadObjectList();
	}

	if (in_array(8,$cid)) {
		$query = "SELECT a.*, b.gb_category FROM #__lms_gradebook_items as a LEFT JOIN #__lms_gradebook_cats as b ON a.gbc_id = b.id WHERE a.course_id = $course_id";
		$JLMS_DB->setQuery($query);
		$gb_items = $JLMS_DB->loadObjectList();
	
		$query = "SELECT * FROM #__lms_gradebook_scale WHERE course_id = $course_id";
		$JLMS_DB->setQuery($query);
		$gb_scale = $JLMS_DB->loadObjectList();

		// 27.03.2008 (DEN) List of Lpaths for automated course completion
		$query = "SELECT * FROM #__lms_gradebook_lpaths WHERE course_id = $course_id";
		$JLMS_DB->setQuery($query);
		$gb_lpaths = $JLMS_DB->loadObjectList();
	}

	$lpath_all_contents = array();
	$lpath_all_conds = array();
	$lpath_all_prerequisites = array();
	$lp_ids = array();
	if (in_array(2,$cid)) {
		foreach ($lpaths as $lp) {
			$lp_ids[] = $lp->id;
		}
		if (count($lp_ids)) {
			$lpi_str = implode(',',$lp_ids);

			$query = "SELECT *, step_name as doc_name FROM `#__lms_learn_path_steps` "
			. "\n  WHERE lpath_id IN ($lpi_str) AND course_id = '".$course_id."'"
			. "\n ORDER BY lpath_id, parent_id, ordering";
			$JLMS_DB->SetQuery( $query );

			$lpath_all_contents = $JLMS_DB->LoadObjectList();
			$query = "SELECT * FROM `#__lms_learn_path_conds` "
			. "\n  WHERE lpath_id IN ($lpi_str) AND course_id = '".$course_id."' ORDER BY lpath_id";
			$JLMS_DB->setQuery( $query );
			$lpath_all_conds = $JLMS_DB->loadObjectList();

			$query = "SELECT * FROM `#__lms_learn_path_prerequisites` "
			. "\n  WHERE lpath_id IN ($lpi_str) AND req_id IN ($lpi_str) ORDER BY lpath_id";
			$JLMS_DB->setQuery( $query );
			$lpath_all_prerequisites = $JLMS_DB->loadObjectList();
		}
	}

	if (in_array(5,$cid)) {
		if (is_null($selected_resources)) {
			$query = "SELECT * FROM `#__lms_certificates` WHERE course_id = '$course_id' AND crtf_type IN ('1','2','-2')";
			$JLMS_DB->setQuery($query);
			$certs = $JLMS_DB->loadObjectList();
		} else {
			$certs = array();
			if ( isset($selected_resources[5]) && is_array($selected_resources[5]) &&  count($selected_resources[5]) ) {
				$p_str_qzs = implode(',', $selected_resources[5]);
				$query = "SELECT distinct c_certificate FROM #__lms_quiz_t_quiz WHERE c_id IN ($p_str_qzs) AND course_id = $course_id";
				$JLMS_DB->setQuery($query);
				$cert_ids = $JLMS_DB->LoadResultArray();
				if (count($cert_ids)) {
					$cert_ids_str = implode(',', $cert_ids);
					$query = "SELECT * FROM `#__lms_certificates` WHERE course_id = '$course_id' AND crtf_type IN ('1','2','-2') AND id IN ($cert_ids_str)";
					$JLMS_DB->setQuery($query);
					$certs = $JLMS_DB->loadObjectList();
				}
			}
		}
	} else {
		if (is_null($selected_resources)) {
			$query = "SELECT * FROM `#__lms_certificates` WHERE course_id = '$course_id' AND crtf_type IN ('1','-2')";
			$JLMS_DB->setQuery($query);
			$certs = $JLMS_DB->loadObjectList();
		} else {
			//we don't need certificates if we export specific lpath withotu quizzes
			$certs = array();
		}
	}


	// 26.03.2008 - DEN - export of files for quiz images 
	$quiz_images_files = array();
	if (in_array(5,$cid)) {
		$query = "SELECT c_id, imgs_id FROM `#__lms_quiz_images` WHERE course_id = '$course_id'";
		$JLMS_DB->setQuery($query);
		$quiz_images_files = $JLMS_DB->loadObjectList();
	}

	$file_ids = array();
	foreach ($certs as $cert) {
		if ($cert->file_id) {
			$file_ids[] = $cert->file_id;
		}
	}
	foreach ($quiz_images_files as $qif) {
		if ($qif->imgs_id) {
			$file_ids[] = $qif->imgs_id;
		}
	}
	if (in_array(1,$cid)) {
		foreach ($documents1 as $doc1) {
			if ($doc1->file_id) {
				$file_ids[] = $doc1->file_id;
			}
		}
	}
	$files = array();
	if (!empty($file_ids)) {
		$file_ids = array_unique($file_ids);
		$f_str = implode(',',$file_ids);
		$query = "SELECT * FROM `#__lms_files` WHERE id IN ($f_str)";
		$JLMS_DB->setQuery($query);
		$files = $JLMS_DB->loadObjectList();
	}
	//get config values
	$query = "SELECT * FROM `#__lms_config`";
	$JLMS_DB->SetQuery( $query );
	$lms_cfg = $JLMS_DB->LoadObjectList();
	$lms_cfg_doc_folder = '';
	$lms_cfg_scorm = '';
	$lms_cfg_backup_folder = '';
	$lms_cfg_quiz_enabled = 0;
	foreach ($lms_cfg as $lcf) {
		if ($lcf->lms_config_var == 'plugin_quiz') {
			$lms_cfg_quiz_enabled = $lcf->lms_config_value;
		} elseif ($lcf->lms_config_var == 'jlms_doc_folder') {
			$lms_cfg_doc_folder = $lcf->lms_config_value;
		} elseif ($lcf->lms_config_var == 'scorm_folder') {
			$lms_cfg_scorm = $lcf->lms_config_value;
		} elseif ($lcf->lms_config_var == 'jlms_backup_folder') {
			$lms_cfg_backup_folder = $lcf->lms_config_value;
		}
	}
	$lms_cfg_doc_folder = str_replace("\\", "/", $lms_cfg_doc_folder);
	$lms_cfg_backup_folder = str_replace("\\", "/", $lms_cfg_backup_folder);
	//end of config
	////////////////////////////////////////////////////////////////////////////////
	//get quizzes data
	$quizzes_data = array();
	$quest_cats_data = array();
	$quiz_quest_data = array();
	$quiz_blank_data = array();
	$quiz_choice_data = array();
	$quiz_hotspot_data = array();
	$quiz_match_data = array();
	$quiz_scale_data = array();
	$quiz_fb_data = array();
	$quizzes_files = array();
	if ($lms_cfg_quiz_enabled && in_array(5,$cid)) {
		if (is_null($selected_resources)) {
			$query = "SELECT a.*, b.c_category as category_name FROM #__lms_quiz_t_quiz as a LEFT JOIN #__lms_quiz_t_category as b ON a.c_category_id = b.c_id WHERE a.course_id = '".$course_id."'";
			$JLMS_DB->SetQuery( $query );
			$quizzes_data = $JLMS_DB->LoadObjectList();
			$query = "SELECT * FROM #__lms_quiz_t_category WHERE course_id = $course_id AND is_quiz_cat = 0";
			$JLMS_DB->SetQuery( $query );
			$quest_cats_data = $JLMS_DB->LoadObjectList();
			$quizzes_ids = array();
			$quizzes_ids[] = 0;
			foreach ($quizzes_data as $qd) {
				$quizzes_ids[] = $qd->c_id;
			}
		} else {
			//export specific resources only
			if ( isset($selected_resources[5]) && is_array($selected_resources[5]) &&  count($selected_resources[5]) ) {
				$p_str_qzs = implode(',', $selected_resources[5]);
			} else {
				$p_str_qzs = '0';
			}
			$query = "SELECT a.*, b.c_category as category_name FROM #__lms_quiz_t_quiz as a LEFT JOIN #__lms_quiz_t_category as b ON a.c_category_id = b.c_id WHERE a.course_id = '".$course_id."' AND a.c_id IN ($p_str_qzs)";
			$JLMS_DB->SetQuery( $query );
			$quizzes_data = $JLMS_DB->LoadObjectList();
			$quizzes_ids = array();
			//$quizzes_ids[] = 0;//no pool compatibility
			foreach ($quizzes_data as $qd) {
				$quizzes_ids[] = $qd->c_id;
			}
		}
		if (count($quizzes_ids)) {
			$q_str = implode(',',$quizzes_ids);
			$query = "SELECT * FROM #__lms_quiz_t_question WHERE course_id = '".$course_id."' AND c_quiz_id IN ($q_str) ORDER BY ordering, c_id";
			$JLMS_DB->SetQuery( $query );
			$quiz_quest_data = $JLMS_DB->LoadObjectList();
			$quests_ids = array();
			foreach ($quiz_quest_data as $qqd) {
				$quests_ids[] = $qqd->c_id;
				if ($qqd->c_image && !in_array($qqd->c_image, $quizzes_files)) {
				// Changed 20.08.2007 by DEN - from:
				//if ($qqd->c_image) {
					$quizzes_files[] = $qqd->c_image;
				}
			}
			if (count($quests_ids)) {
				$qq_str = implode(',',$quests_ids);

				// 18 June 2007 (Questions Feedbacks export)
				$query = "SELECT * FROM #__lms_quiz_t_question_fb WHERE quest_id IN ($qq_str)";
				$JLMS_DB->SetQuery( $query );
				$quiz_fb_data = $JLMS_DB->LoadObjectList();

				$query = "SELECT a.*, b.c_text, b.ordering FROM #__lms_quiz_t_blank as a, #__lms_quiz_t_text as b WHERE a.c_question_id IN ($qq_str) AND a.c_id = b.c_blank_id ORDER BY b.c_blank_id, b.ordering";
				$JLMS_DB->SetQuery( $query );
				$quiz_blank_data = $JLMS_DB->LoadObjectList();
				$query = "SELECT * FROM #__lms_quiz_t_choice WHERE c_question_id IN ($qq_str) ORDER BY c_question_id, ordering";
				$JLMS_DB->SetQuery( $query );
				$quiz_choice_data = $JLMS_DB->LoadObjectList();
				$query = "SELECT * FROM #__lms_quiz_t_hotspot WHERE c_question_id IN ($qq_str)";
				$JLMS_DB->SetQuery( $query );
				$quiz_hotspot_data = $JLMS_DB->LoadObjectList();
				$query = "SELECT * FROM #__lms_quiz_t_matching WHERE c_question_id IN ($qq_str) ORDER BY c_question_id, ordering";
				$JLMS_DB->SetQuery( $query );
				$quiz_match_data = $JLMS_DB->LoadObjectList();

				// Likert scale - aded 27.0.3.2008 (DEN)
				$query = "SELECT * FROM #__lms_quiz_t_scale WHERE c_question_id IN ($qq_str) ORDER BY c_question_id, ordering";
				$JLMS_DB->SetQuery( $query );
				$quiz_scale_data = $JLMS_DB->LoadObjectList();
			}
		}

		// 26.03.2008 - DEN - quiz images export
		$query = "SELECT * FROM #__lms_quiz_images WHERE course_id = '".$course_id."'";
		$JLMS_DB->SetQuery( $query );
		$quizzes_images_data = $JLMS_DB->LoadObjectList();
	}
	//end of quizzes
	////////////////////////////////////////////////////////////////////////////////
	//create XML file
	$xml_encoding = 'iso-8859-1';
	if (defined('_ISO')) {
		$iso = explode( '=', _ISO );
		if (isset($iso[1]) && $iso[1]) {
			$xml_encoding = $iso[1];
		}
	}
	$export_xml = "";
	$export_xml .= "<?xml version=\"1.0\" encoding=\"".$xml_encoding."\" ?>\r\n";
	$export_xml .= "\t<course_backup lms_version=\"1.0.0\">\r\n";
	$export_xml .= "\t\t<name><![CDATA[".$course->course_name."]]></name>\r\n";
	$export_xml .= "\t\t<description><![CDATA[".$course->course_description."]]></description>\r\n";
	$export_xml .= "\t\t<course_category><![CDATA[".$course->c_category."]]></course_category>\r\n";
	$export_xml .= "\t\t<metadesc><![CDATA[".$course->metadesc."]]></metadesc>\r\n";
	$export_xml .= "\t\t<metakeys><![CDATA[".$course->metakeys."]]></metakeys>\r\n";
	$export_xml .= "\t\t<language_name><![CDATA[".$course->lang_name."]]></language_name>\r\n";
	$export_xml .= "\t\t<course_paid><![CDATA[".$course->paid."]]></course_paid>\r\n";
	$export_xml .= "\t\t<self_registration>".$course->self_reg."</self_registration>\r\n";
	$export_xml .= "\t\t<forum_enabled>".$course->add_forum."</forum_enabled>\r\n";
	$export_xml .= "\t\t<chat_enabled>".$course->add_chat."</chat_enabled>\r\n";
	$export_xml .= "\t\t<publish_start>".$course->publish_start."</publish_start>\r\n";
	$export_xml .= "\t\t<publish_start_date><![CDATA[".$course->start_date."]]></publish_start_date>\r\n";
	$export_xml .= "\t\t<publish_end>".$course->publish_end."</publish_end>\r\n";
	$export_xml .= "\t\t<publish_end_date><![CDATA[".$course->end_date."]]></publish_end_date>\r\n";
	$export_xml .= "\t\t<spec_reg>".$course->spec_reg."</spec_reg>\r\n";
	$export_xml .= "\t\t<course_question><![CDATA[]]></course_question>\r\n"; // 27.03.2008 (DEN) enrollment qeustions are moved into another section - tag is left here for compatibility only
	$export_xml .= "\t\t<course_params><![CDATA[".$course->params."]]></course_params>\r\n";

	// 27.03.2008 (DEN) Enrollment questions export
	$export_xml .= "\t\t<course_questions>\n";
	foreach ($course_questions as $course_quest){
		$export_xml .= "\t\t\t<course_quest is_optional=\"".$course_quest->is_optional."\" ordering=\"".$course_quest->ordering."\" >\r\n";
		$export_xml .= "\t\t\t\t<question_text><![CDATA[".$course_quest->course_question."]]></question_text>\n";
		$export_xml .= "\t\t\t\t<default_answer><![CDATA[".$course_quest->default_answer."]]></default_answer>\n";
		$export_xml .= "\t\t\t</course_quest>\n";
	}	
	$export_xml .= "\t\t</course_questions>\n";

	//hidden menus
	$export_xml .= "\t\t<hidden_menu_items>\n";
	foreach ($hidden_menus as $hdmn){
		$export_xml .= "\t\t\t<menu_item menu_id=\"".$hdmn->menu_id."\" user_access=\"".$hdmn->user_access."\" >\r\n";
		$export_xml .= "\t\t\t</menu_item>\n";
	}	
	$export_xml .= "\t\t</hidden_menu_items>\n";

	//documents section		
	if (in_array(1,$cid)) {
		$export_xml .= "\t\t<documents>\n";
		foreach ($documents1 as $document){
			$export_xml .= "\t\t\t<document id=\"".$document->id."\" file_id=\"".$document->file_id."\" folder_flag=\"".$document->folder_flag."\" collapsed_folder=\"".(($document->folder_flag == 1 && !$document->file_id && !empty($docs_collapsed) && in_array($document->id,$docs_collapsed) )?1:0)."\" parent_id=\"".$document->parent_id."\" ordering=\"".$document->ordering."\" published=\"".$document->published."\" publish_start=\"".$document->publish_start."\" start_date=\"".$document->start_date."\" publish_end=\"".$document->publish_end."\" end_date=\"".$document->end_date."\" is_time_related=\"".$document->is_time_related."\" show_period=\"".$document->show_period."\" >\r\n";
			$export_xml .= "\t\t\t\t<doc_name><![CDATA[".$document->doc_name."]]></doc_name>\n";
			$export_xml .= "\t\t\t\t<doc_description><![CDATA[".$document->doc_description."]]></doc_description>\n";
			$export_xml .= "\t\t\t</document>\n";
			$processed_docs[] = $document->id;
		}
		$export_xml .= "\t\t</documents>\n";
	}

	//links section
	if (in_array(4,$cid)) {
		$export_xml .= "\t\t<links>\n";
		foreach ($links as $link){
			$export_xml .= "\t\t\t<link id=\"".$link->id."\" link_type=\"".$link->link_type."\" ordering=\"".$link->ordering."\" published=\"".$link->published."\" is_time_related=\"".$link->is_time_related."\" show_period=\"".$link->show_period."\">\n";
			$export_xml .= "\t\t\t\t<linkname><![CDATA[".$link->link_name."]]></linkname>\r\n";
			$export_xml .= "\t\t\t\t<linkhref><![CDATA[".$link->link_href."]]></linkhref>\r\n";
			$export_xml .= "\t\t\t\t<description><![CDATA[".$link->link_description."]]></description>\r\n";
            $export_xml .= "\t\t\t\t<link_params><![CDATA[".$link->params."]]></link_params>\n";
			$export_xml .= "\t\t\t</link>\n";
			$processed_links[] = $link->id;
		}
		$export_xml .= "\t\t</links>\n";
	}

	//homework section
	if (in_array(7,$cid)) {
		$export_xml .= "\t\t<homework_tool>\n";
		foreach ($homeworks as $homework){
			$export_xml .= "\t\t\t<homework id=\"".$homework->id."\" post_date=\"".$homework->post_date."\" end_date=\"".$homework->end_date."\" is_time_related=\"".$homework->is_time_related."\" show_period=\"".$homework->show_period."\">\n ";
			$export_xml .= "\t\t\t\t<hw_name><![CDATA[".$homework->hw_name."]]></hw_name>\r\n";
			$export_xml .= "\t\t\t\t<description><![CDATA[".$homework->hw_description."]]></description>\r\n";
			$export_xml .= "\t\t\t\t<short_description><![CDATA[".$homework->hw_shortdescription."]]></short_description>\r\n";
			$export_xml .= "\t\t\t</homework>\n";
		}
		$export_xml .= "\t\t</homework_tool>\n";
	}

	//announcement section
	if (in_array(6,$cid)) {
		$export_xml .= "\t\t<announcement_tool>\n";
		foreach ($announcements as $announc){
			$export_xml .= "\t\t\t<announcement id=\"".$announc->agenda_id."\" start_date=\"".$announc->start_date."\" end_date=\"".$announc->end_date."\" is_time_related=\"".$announc->is_time_related."\" show_period=\"".$announc->show_period."\">\n";
			$export_xml .= "\t\t\t\t<announcement_title><![CDATA[".$announc->title."]]></announcement_title>\r\n";
			$export_xml .= "\t\t\t\t<announcement_content><![CDATA[".$announc->content."]]></announcement_content>\r\n";
			$export_xml .= "\t\t\t</announcement>\n";
		}
		$export_xml .= "\t\t</announcement_tool>\n";
	}

	//quizzes section
	if (in_array(5,$cid)) {
		$export_xml .= "\t\t<quizzes_quest_categories>\n";
		if ($lms_cfg_quiz_enabled) {
			foreach ($quest_cats_data as $qcat){
				$export_xml .= "\t\t\t<quest_category c_id=\"".$qcat->c_id."\">\r\n";
				$export_xml .= "\t\t\t\t<quest_category_name><![CDATA[".$qcat->c_category."]]></quest_category_name>\r\n";
				$export_xml .= "\t\t\t\t<quest_category_instr><![CDATA[".$qcat->c_instruction."]]></quest_category_instr>\r\n";
				$export_xml .= "\t\t\t</quest_category>\r\n";
			}
		}
		$export_xml .= "\t\t</quizzes_quest_categories>\n";

		// 26.03.2008 - DEN - quiz images export
		$export_xml .= "\t\t<quizzes_images>\n";
		if ($lms_cfg_quiz_enabled) {
			foreach ($quizzes_images_data as $qimage){
				$export_xml .= "\t\t\t<quiz_image c_id=\"".$qimage->c_id."\" file_id=\"".$qimage->imgs_id."\">\r\n";
				$export_xml .= "\t\t\t\t<quiz_image_name><![CDATA[".$qimage->imgs_name."]]></quiz_image_name>\r\n";
				$export_xml .= "\t\t\t</quiz_image>\r\n";
			}
		}
		$export_xml .= "\t\t</quizzes_images>\n";

		/* (28 April 2007) Questions Pool export
		 *
		 */
		$q_quests = array();
		$qq_ids = array();
		foreach ($quiz_quest_data as $qqdo) {
			if ($qqdo->c_quiz_id == 0) {
				$q_quests[] = $qqdo;
				$qq_ids[] = $qqdo->c_id;
			}
		}

		$export_xml .= "\t\t<quizzes_question_pool>\n";
		if ($lms_cfg_quiz_enabled) {
			$export_xml .= "\t\t<question_pool_data>\n";
			$export_xml .= "\t\t\t<pool_questions>\n";
			if (count($q_quests)) {
				foreach ($q_quests as $quest){
					$export_xml .= "\t\t\t<quiz_question c_id=\"".$quest->c_id."\" c_point=\"".$quest->c_point."\" c_attempts=\"".$quest->c_attempts."\" c_type=\"".$quest->c_type."\" c_pool=\"".$quest->c_pool."\" c_qcat=\"".$quest->c_qcat."\" ordering=\"".$quest->ordering."\">\n";
					$export_xml .= "\t\t\t\t<question_text><![CDATA[".$quest->c_question."]]></question_text>\r\n";
					$export_xml .= "\t\t\t\t<question_image><![CDATA[".$quest->c_image."]]></question_image>\r\n";
					$export_xml .= "\t\t\t\t<question_params><![CDATA[".$quest->params."]]></question_params>\r\n";
					$export_xml .= "\t\t\t\t<question_explanation><![CDATA[".$quest->c_explanation."]]></question_explanation>\r\n"; // added 27.03.2008 (DEN)
					$export_xml .= "\t\t\t</quiz_question>\n";
				}
			}
			$export_xml .= "\t\t\t</pool_questions>\n";

			// 18 June 2007 - questions feedbacks
			$q_fback = array();
			foreach ($quiz_fb_data as $qfb) {
				if (in_array($qfb->quest_id, $qq_ids)) {
					$q_fback[] = $qfb;
				}
			}
			$export_xml .= "\t\t\t<question_feedbacks>\n";
			if (count($q_fback)) {
				foreach ($q_fback as $qfb){
					if ($qfb->fb_text) {
						$export_xml .= "\t\t\t<question_fb quest_id=\"".$qfb->quest_id."\" choice_id=\"".$qfb->choice_id."\">\n";
						$export_xml .= "\t\t\t\t<fb_text><![CDATA[".$qfb->fb_text."]]></fb_text>\r\n";
						$export_xml .= "\t\t\t</question_fb>\n";
					}
				}
			}
			$export_xml .= "\t\t\t</question_feedbacks>\n";

			$export_xml .= "\t\t\t<choice_data>\n";
			$q_choice = array();
			foreach ($quiz_choice_data as $qcd) {
				if (in_array($qcd->c_question_id, $qq_ids)) {
					$q_choice[] = $qcd;
				}
			}
			if (count($q_choice)) {
				foreach ($q_choice as $qc_one) {
					$export_xml .= "\t\t\t\t<quest_choice c_question_id=\"".$qc_one->c_question_id."\" c_right=\"".$qc_one->c_right."\" ordering=\"".$qc_one->ordering."\">\r\n";
					$export_xml .= "\t\t\t\t\t<choice_text><![CDATA[".$qc_one->c_choice."]]></choice_text>\r\n";
					$export_xml .= "\t\t\t\t</quest_choice>\n";
				}
			}
			$export_xml .= "\t\t\t</choice_data>\n";
			$export_xml .= "\t\t\t<match_data>\n";
			$q_match = array();
			foreach ($quiz_match_data as $qmd) {
				if (in_array($qmd->c_question_id, $qq_ids)) {
					$q_match[] = $qmd;
				}
			}
			if (count($q_match)) {
				foreach ($q_match as $qm_one) {
					$export_xml .= "\t\t\t\t<quest_match c_question_id=\"".$qm_one->c_question_id."\" ordering=\"".$qm_one->ordering."\">\r\n";
					$export_xml .= "\t\t\t\t\t<match_text_left><![CDATA[".$qm_one->c_left_text."]]></match_text_left>\r\n";
					$export_xml .= "\t\t\t\t\t<match_text_right><![CDATA[".$qm_one->c_right_text."]]></match_text_right>\r\n";
					$export_xml .= "\t\t\t\t</quest_match>\n";
				}
			}
			$export_xml .= "\t\t\t</match_data>\n";

			// Likert scale - 27.03.2008 (DEN)
			$export_xml .= "\t\t\t<scale_data>\n";
			$q_scale = array();
			foreach ($quiz_scale_data as $qsd) {
				if (in_array($qsd->c_question_id, $qq_ids)) {
					$q_scale[] = $qsd;
				}
			}
			if (count($q_scale)) {
				foreach ($q_scale as $qs_one) {
					$export_xml .= "\t\t\t\t<quest_scale c_question_id=\"".$qs_one->c_question_id."\" c_type=\"".$qs_one->c_type."\" ordering=\"".$qs_one->ordering."\">\r\n";
					$export_xml .= "\t\t\t\t\t<scale_field><![CDATA[".$qs_one->c_field."]]></scale_field>\r\n";
					$export_xml .= "\t\t\t\t</quest_scale>\n";
				}
			}
			$export_xml .= "\t\t\t</scale_data>\n";

			$export_xml .= "\t\t\t<blank_data>\n";
			$q_blank = array();
			foreach ($quiz_blank_data as $qbd) {
				if (in_array($qbd->c_question_id, $qq_ids)) {
					$q_blank[] = $qbd;
				}
			}
			if (count($q_blank)) {
				foreach ($q_blank as $qb_one) {
					$export_xml .= "\t\t\t\t<quest_blank c_question_id=\"".$qb_one->c_question_id."\" ordering=\"".$qb_one->ordering."\">\r\n";
					$export_xml .= "\t\t\t\t\t<blank_text><![CDATA[".$qb_one->c_text."]]></blank_text>\r\n";
					$export_xml .= "\t\t\t\t\t<default_answer><![CDATA[".$qb_one->c_default ."]]></default_answer>\r\n";
					$export_xml .= "\t\t\t\t</quest_blank>\n";
				}
			}
			$export_xml .= "\t\t\t</blank_data>\n";
			$export_xml .= "\t\t\t<hotspot_data>\n";
			$q_hotspot = array();
			foreach ($quiz_hotspot_data as $qhd) {
				if (in_array($qhd->c_question_id, $qq_ids)) {
					$q_hotspot[] = $qhd;
				}
			}
			if (count($q_hotspot)) {
				foreach ($q_hotspot as $qh_one) {
					$export_xml .= "\t\t\t\t<quest_hotspot c_question_id=\"".$qh_one->c_question_id."\">\r\n";
					$export_xml .= "\t\t\t\t\t<hs_start_x><![CDATA[".$qh_one->c_start_x."]]></hs_start_x>\r\n";
					$export_xml .= "\t\t\t\t\t\t<hs_start_y><![CDATA[".$qh_one->c_start_y."]]></hs_start_y>\r\n";
					$export_xml .= "\t\t\t\t\t<hs_width><![CDATA[".$qh_one->c_width."]]></hs_width>\r\n";
					$export_xml .= "\t\t\t\t\t<hs_height><![CDATA[".$qh_one->c_height."]]></hs_height>\r\n";
					$export_xml .= "\t\t\t\t</quest_hotspot>\n";
				}
			}
			$export_xml .= "\t\t\t</hotspot_data>\n";
			$export_xml .= "\t\t</question_pool_data>\n";
		}
		$export_xml .= "\t\t</quizzes_question_pool>\n";

		/* (28 April 2007) END OF Question Pool export
		 *
		 */
		 
		$export_xml .= "\t\t<quizzes>\n";
		if ($lms_cfg_quiz_enabled) {
			foreach ($quizzes_data as $quiz){				
				$export_xml .= "\t\t\t<quiz c_id=\"".$quiz->c_id."\" published=\"".$quiz->published."\">\r\n";
				$export_xml .= "\t\t\t\t<quiz_title><![CDATA[".$quiz->c_title."]]></quiz_title>\r\n";
				$export_xml .= "\t\t\t\t<quiz_description><![CDATA[".$quiz->c_description."]]></quiz_description>\r\n";
				$export_xml .= "\t\t\t\t<quiz_category><![CDATA[".$quiz->category_name."]]></quiz_category>\r\n";
				$export_xml .= "\t\t\t\t<quiz_full_score><![CDATA[".$quiz->c_full_score."]]></quiz_full_score>\r\n";
				$export_xml .= "\t\t\t\t<quiz_time_limit><![CDATA[".$quiz->c_time_limit."]]></quiz_time_limit>\r\n";
				$export_xml .= "\t\t\t\t<quiz_min_after><![CDATA[".$quiz->c_min_after."]]></quiz_min_after>\r\n";
				$export_xml .= "\t\t\t\t<quiz_passing_score><![CDATA[".$quiz->c_passing_score."]]></quiz_passing_score>\r\n";
				$export_xml .= "\t\t\t\t<quiz_right_message><![CDATA[".$quiz->c_right_message."]]></quiz_right_message>\r\n";
				$export_xml .= "\t\t\t\t<quiz_wrong_message><![CDATA[".$quiz->c_wrong_message."]]></quiz_wrong_message>\r\n";
				$export_xml .= "\t\t\t\t<quiz_pass_message><![CDATA[".$quiz->c_pass_message."]]></quiz_pass_message>\r\n";
				$export_xml .= "\t\t\t\t<quiz_unpass_message><![CDATA[".$quiz->c_unpass_message."]]></quiz_unpass_message>\r\n";
				$export_xml .= "\t\t\t\t<quiz_review>".$quiz->c_enable_review."</quiz_review>\r\n";
				$export_xml .= "\t\t\t\t<quiz_email>".$quiz->c_email_to."</quiz_email>\r\n";
				$export_xml .= "\t\t\t\t<quiz_print>".$quiz->c_enable_print."</quiz_print>\r\n";
				$export_xml .= "\t\t\t\t<quiz_certif>".$quiz->c_enable_sertif."</quiz_certif>\r\n";
				$export_xml .= "\t\t\t\t<quiz_skin>".$quiz->c_skin."</quiz_skin>\r\n";
				$export_xml .= "\t\t\t\t<quiz_random>".$quiz->c_random."</quiz_random>\r\n";
				$export_xml .= "\t\t\t\t<quiz_guest>".$quiz->c_guest."</quiz_guest>\r\n";
				$export_xml .= "\t\t\t\t<quiz_slide>".$quiz->c_slide."</quiz_slide>\r\n";
				$export_xml .= "\t\t\t\t<quiz_language>".$quiz->c_language."</quiz_language>\r\n";
				$export_xml .= "\t\t\t\t<quiz_certificate>".$quiz->c_certificate."</quiz_certificate>\r\n";
				$export_xml .= "\t\t\t\t<quiz_gradebook>".$quiz->c_gradebook."</quiz_gradebook>\r\n";
				$export_xml .= "\t\t\t\t<quiz_params><![CDATA[".$quiz->params."]]></quiz_params>\r\n";
				$export_xml .= "\t\t\t\t<is_time_related><![CDATA[".$quiz->is_time_related."]]></is_time_related>\r\n";
				$export_xml .= "\t\t\t\t<show_period><![CDATA[".$quiz->show_period."]]></show_period>\r\n";
				
				$query = "SELECT * FROM #__lms_quiz_t_quiz_pool WHERE quiz_id = $quiz->c_id";
				$JLMS_DB->SetQuery( $query );
				$quiz_pool = $JLMS_DB->LoadObjectList();
				$export_xml .= "\t\t\t\t<quiz_pool_assoc>\n";
				foreach ($quiz_pool as $qup) {
					$export_xml .= "\t\t\t\t\t<quiz_pool_item qcat_id=\"".$qup->qcat_id."\" items_number=\"".$qup->items_number."\" />\n";
				}
				$export_xml .= "\t\t\t\t</quiz_pool_assoc>\n";
	
				$q_quests = array();
				$qq_ids = array();
				foreach ($quiz_quest_data as $qqdo) {
					if ($qqdo->c_quiz_id == $quiz->c_id) {
						$q_quests[] = $qqdo;
						$qq_ids[] = $qqdo->c_id;
					}
				}
				$export_xml .= "\t\t\t\t<quiz_questions>\n";
				if (count($q_quests)) {
					foreach ($q_quests as $quest){
						$export_xml .= "\t\t\t\t\t<quiz_question c_id=\"".$quest->c_id."\" c_point=\"".$quest->c_point."\" c_attempts=\"".$quest->c_attempts."\" c_type=\"".$quest->c_type."\" c_pool=\"".$quest->c_pool."\" c_qcat=\"".$quest->c_qcat."\" ordering=\"".$quest->ordering."\">\n";
						$export_xml .= "\t\t\t\t\t\t<question_text><![CDATA[".$quest->c_question."]]></question_text>\r\n";
						$export_xml .= "\t\t\t\t\t\t<question_image><![CDATA[".$quest->c_image."]]></question_image>\r\n";
						$export_xml .= "\t\t\t\t\t\t<question_params><![CDATA[".$quest->params."]]></question_params>\r\n";
						$export_xml .= "\t\t\t\t\t\t<question_explanation><![CDATA[".$quest->c_explanation."]]></question_explanation>\r\n"; // added 27.03.2008 (DEN)
						$export_xml .= "\t\t\t\t\t</quiz_question>\n";
					}
				}
				$export_xml .= "\t\t\t\t</quiz_questions>\n";


				// 18 June 2007 - questions feedbacks
				$q_fback = array();
				foreach ($quiz_fb_data as $qfb) {
					if (in_array($qfb->quest_id, $qq_ids)) {
						$q_fback[] = $qfb;
					}
				}
				$export_xml .= "\t\t\t<question_feedbacks>\n";
				if (count($q_fback)) {
					foreach ($q_fback as $qfb){
						if ($qfb->fb_text) {
							$export_xml .= "\t\t\t<question_fb quest_id=\"".$qfb->quest_id."\" choice_id=\"".$qfb->choice_id."\">\n";
							$export_xml .= "\t\t\t\t<fb_text><![CDATA[".$qfb->fb_text."]]></fb_text>\r\n";
							$export_xml .= "\t\t\t</question_fb>\n";
						}
					}
				}
				$export_xml .= "\t\t\t</question_feedbacks>\n";

				$export_xml .= "\t\t\t\t<choice_data>\n";
				$q_choice = array();
				foreach ($quiz_choice_data as $qcd) {
					if (in_array($qcd->c_question_id, $qq_ids)) {
						$q_choice[] = $qcd;
					}
				}
				if (count($q_choice)) {
					foreach ($q_choice as $qc_one) {
						$export_xml .= "\t\t\t\t\t<quest_choice c_question_id=\"".$qc_one->c_question_id."\" c_right=\"".$qc_one->c_right."\" ordering=\"".$qc_one->ordering."\">\r\n";
						$export_xml .= "\t\t\t\t\t\t<choice_text><![CDATA[".$qc_one->c_choice."]]></choice_text>\r\n";
						$export_xml .= "\t\t\t\t\t</quest_choice>\n";
					}
				}
				$export_xml .= "\t\t\t\t</choice_data>\n";
				$export_xml .= "\t\t\t\t<match_data>\n";
				$q_match = array();
				foreach ($quiz_match_data as $qmd) {
					if (in_array($qmd->c_question_id, $qq_ids)) {
						$q_match[] = $qmd;
					}
				}
				if (count($q_match)) {
					foreach ($q_match as $qm_one) {
						$export_xml .= "\t\t\t\t\t<quest_match c_question_id=\"".$qm_one->c_question_id."\" ordering=\"".$qm_one->ordering."\">\r\n";
						$export_xml .= "\t\t\t\t\t\t<match_text_left><![CDATA[".$qm_one->c_left_text."]]></match_text_left>\r\n";
						$export_xml .= "\t\t\t\t\t\t<match_text_right><![CDATA[".$qm_one->c_right_text."]]></match_text_right>\r\n";
						$export_xml .= "\t\t\t\t\t</quest_match>\n";
					}
				}
				$export_xml .= "\t\t\t\t</match_data>\n";

				// Likert scale - 27.03.2008 (DEN)
				$export_xml .= "\t\t\t<scale_data>\n";
				$q_scale = array();
				foreach ($quiz_scale_data as $qsd) {
					if (in_array($qsd->c_question_id, $qq_ids)) {
						$q_scale[] = $qsd;
					}
				}
				if (count($q_scale)) {
					foreach ($q_scale as $qs_one) {
						$export_xml .= "\t\t\t\t<quest_scale c_question_id=\"".$qs_one->c_question_id."\" c_type=\"".$qs_one->c_type."\" ordering=\"".$qs_one->ordering."\">\r\n";
						$export_xml .= "\t\t\t\t\t<scale_field><![CDATA[".$qs_one->c_field."]]></scale_field>\r\n";
						$export_xml .= "\t\t\t\t</quest_scale>\n";
					}
				}
				$export_xml .= "\t\t\t</scale_data>\n";

				$export_xml .= "\t\t\t\t<blank_data>\n";
				$q_blank = array();
				foreach ($quiz_blank_data as $qbd) {
					if (in_array($qbd->c_question_id, $qq_ids)) {
						$q_blank[] = $qbd;
					}
				}
				if (count($q_blank)) {
					foreach ($q_blank as $qb_one) {
						$export_xml .= "\t\t\t\t\t<quest_blank c_question_id=\"".$qb_one->c_question_id."\" ordering=\"".$qb_one->ordering."\">\r\n";
						$export_xml .= "\t\t\t\t\t\t<blank_text><![CDATA[".$qb_one->c_text."]]></blank_text>\r\n";
						$export_xml .= "\t\t\t\t\t<default_answer><![CDATA[".$qb_one->c_default ."]]></default_answer>\r\n";
						$export_xml .= "\t\t\t\t\t</quest_blank>\n";
					}
				}
				$export_xml .= "\t\t\t\t</blank_data>\n";
				$export_xml .= "\t\t\t\t<hotspot_data>\n";
				$q_hotspot = array();
				foreach ($quiz_hotspot_data as $qhd) {
					if (in_array($qhd->c_question_id, $qq_ids)) {
						$q_hotspot[] = $qhd;
					}
				}
				if (count($q_hotspot)) {
					foreach ($q_hotspot as $qh_one) {
						$export_xml .= "\t\t\t\t\t<quest_hotspot c_question_id=\"".$qh_one->c_question_id."\">\r\n";
						$export_xml .= "\t\t\t\t\t\t<hs_start_x><![CDATA[".$qh_one->c_start_x."]]></hs_start_x>\r\n";
						$export_xml .= "\t\t\t\t\t\t\t<hs_start_y><![CDATA[".$qh_one->c_start_y."]]></hs_start_y>\r\n";
						$export_xml .= "\t\t\t\t\t\t<hs_width><![CDATA[".$qh_one->c_width."]]></hs_width>\r\n";
						$export_xml .= "\t\t\t\t\t\t<hs_height><![CDATA[".$qh_one->c_height."]]></hs_height>\r\n";
						$export_xml .= "\t\t\t\t\t</quest_hotspot>\n";
					}
				}
				$export_xml .= "\t\t\t\t</hotspot_data>\n";
				$export_xml .= "\t\t\t</quiz>\n";
				$processed_quizzes[] = $quiz->c_id; 
			}
		}
		$export_xml .= "\t\t</quizzes>\n";
	}
	//end of quizzez
	///////////////////////////////////////////////////////////////////////////////
	//certificates section

	// Certificates section (was changed at 27.03.2008 (DEN) - processing of fonts, additional texts; different roles are not processed)
	$cert_ids = array();
	$export_xml .= "\t\t<certificates>\n";
	foreach ($certs as $cert){
		if ($cert->crtf_type == 1 || $cert->crtf_type == 2) {
			$export_xml .= "\t\t\t<certificate id=\"".$cert->id."\" file_id=\"".$cert->file_id."\" crtf_type=\"".$cert->crtf_type."\" text_x=\"".$cert->text_x."\" text_y=\"".$cert->text_y."\" text_size=\"".$cert->text_size."\">\n";
			$export_xml .= "\t\t\t\t<certificate_text><![CDATA[".$cert->crtf_text."]]></certificate_text>\r\n";
			$export_xml .= "\t\t\t\t<certificate_font><![CDATA[".$cert->crtf_font."]]></certificate_font>\r\n";
			$export_xml .= "\t\t\t</certificate>\n";
			$cert_ids[] = $cert->id;
		}
	}
	$export_xml .= "\t\t</certificates>\n";
	$export_xml .= "\t\t<certificate_texts>\n";
	if (!empty($cert_ids)) {
		foreach ($certs as $cert){
			if ($cert->crtf_type == -2 && in_array($cert->parent_id,$cert_ids)) {
				$export_xml .= "\t\t\t<certificate_add_text parent_id=\"".$cert->parent_id."\" crtf_type=\"".$cert->crtf_type."\" text_x=\"".$cert->text_x."\" text_y=\"".$cert->text_y."\" text_size=\"".$cert->text_size."\">\n";
				$export_xml .= "\t\t\t\t<add_certificate_text><![CDATA[".$cert->crtf_text."]]></add_certificate_text>\r\n";
				$export_xml .= "\t\t\t\t<certificate_font><![CDATA[".$cert->crtf_font."]]></certificate_font>\r\n";
				$export_xml .= "\t\t\t</certificate_add_text>\n";
			}
		}	
	}
	$export_xml .= "\t\t</certificate_texts>\n";

	//files section
	$export_xml .= "\t\t<files>\n";
	foreach ($files as $ff){
		$export_xml .= "\t\t\t<file id=\"".$ff->id."\">\n";
		$export_xml .= "\t\t\t\t<filename><![CDATA[".$ff->file_name."]]></filename>\r\n";
		$export_xml .= "\t\t\t\t<servername><![CDATA[".$ff->file_srv_name."]]></servername>\r\n";
		$export_xml .= "\t\t\t</file>\n";
	}
	$export_xml .= "\t\t</files>\n";
	//SCORM's section
	if (in_array(3,$cid)) {
		$export_xml .= "\t\t<scorms>\n";
		foreach ($scorms as $scorm){
			$export_xml .= "\t\t\t<scorm id=\"".$scorm->id."\" uploadtime=\"".$scorm->upload_time."\" >\n";
			$export_xml .= "\t\t\t\t<foldersrvname><![CDATA[".$scorm->folder_srv_name."]]></foldersrvname>\r\n";
			$export_xml .= "\t\t\t\t<packagesrvname><![CDATA[".$scorm->package_srv_name."]]></packagesrvname>\r\n";
			$export_xml .= "\t\t\t\t<packageusername><![CDATA[".$scorm->package_user_name."]]></packageusername>\r\n";
			$export_xml .= "\t\t\t</scorm>\n";
		}
		$export_xml .= "\t\t</scorms>\n";
	}
	//ZIPPACK's section
	if (in_array(1,$cid)) {
		$export_xml .= "\t\t<zipped_documents>\n";
		foreach ($zipped_docs as $zipdoc){
			$export_xml .= "\t\t\t<zipdoc id=\"".$zipdoc->id."\" uploadtime=\"".$zipdoc->upload_time."\" count_files=\"".$zipdoc->count_files."\" zip_size=\"".$zipdoc->zip_size."\" zipfile_size=\"".$zipdoc->zipfile_size."\" >\n";
			$export_xml .= "\t\t\t\t<zip_folder><![CDATA[".$zipdoc->zip_folder."]]></zip_folder>\r\n";
			$export_xml .= "\t\t\t\t<zip_srv_name><![CDATA[".$zipdoc->zip_srv_name."]]></zip_srv_name>\r\n";
			$export_xml .= "\t\t\t\t<zip_name><![CDATA[".$zipdoc->zip_name."]]></zip_name>\r\n";
			$export_xml .= "\t\t\t\t<startup_file><![CDATA[".$zipdoc->startup_file."]]></startup_file>\r\n";
			$export_xml .= "\t\t\t</zipdoc>\n";
		}
		$export_xml .= "\t\t</zipped_documents>\n";
	}
	//LearningPaths section
	if (!empty($lpaths)) {
		$export_xml .= "\t\t<learn_paths>\n";
		foreach ($lpaths as $lpath ){
			// we need to replace item_id to Scorm_package id.
			$item_id = $lpath->item_id;
			if ($item_id) {
				if ($lpath->lp_type == 1) {
					$query = "SELECT scorm_package FROM #__lms_n_scorm WHERE id = ".$lpath->item_id;
					$JLMS_DB->SetQuery($query);
					$item_id = $JLMS_DB->loadResult();
				}
			}
			$export_xml .= "\t\t\t<learn_path id=\"".$lpath->id."\" item_id=\"".$item_id."\" ordering=\"".$lpath->ordering."\" published=\"".$lpath->published."\" is_time_related=\"".$lpath->is_time_related."\" show_period=\"".$lpath->show_period."\" >\n";
			$export_xml .= "\t\t\t\t<lp_name><![CDATA[".$lpath->lpath_name."]]></lp_name>\n";
			$export_xml .= "\t\t\t\t<lp_shortdescription><![CDATA[".$lpath->lpath_shortdescription."]]></lp_shortdescription>\n";
			$export_xml .= "\t\t\t\t<lp_description><![CDATA[".$lpath->lpath_description."]]></lp_description>\n";
			$export_xml .= "\t\t\t\t<lp_params><![CDATA[".$lpath->lp_params."]]></lp_params>\n";
			////////////////////////////////////////////////////////////////////////////////
			$lpath_contents = array();
			foreach ($lpath_all_contents as $lp_ac) {
				if ($lp_ac->lpath_id == $lpath->id) {
					$lpath_contents[] = $lp_ac;
				}
			}
			///////////////////////////////////////////////////////////////////////////////
			// Prerequisites export - 18 August 2007 - (DEN)
			$lpath_prerequisites = array();
			foreach ($lpath_all_prerequisites as $lp_prs) {
				if ($lp_prs->lpath_id == $lpath->id) {
					$lpath_prerequisites[] = $lp_prs;
				}
			}
			///////////////////////////////////////////////////////////////////////////////
			// Prerequisites export - 18 August 2007 - (DEN)
			$export_xml .= "\t\t\t\t<prerequisites>\n";
			if (count($lpath_prerequisites)) {
				foreach ($lpath_prerequisites as $prereq){
					$export_xml .= "\t\t\t\t\t<prerequisite lpath_id=\"".$prereq->lpath_id."\" req_id=\"".$prereq->req_id."\" time_minutes=\"".$prereq->time_minutes."\" >\n";
					$export_xml .= "\t\t\t\t\t</prerequisite>\n";
				}
			}
			$export_xml .= "\t\t\t\t</prerequisites>\n";
			///////////////////////////////////////////////////////////////////////////////
			$export_xml .= "\t\t\t\t<steps>\n";
			if (count($lpath_contents)) {
				//tree sorting of LP contents
				$lpath_contents = JLMS_GetLPathTreeStructure($lpath_contents);
				foreach ($lpath_contents as $step){
					$export_xml .= "\t\t\t\t\t<step id=\"".$step->id."\" item_id=\"".$step->item_id."\" lpath_id=\"".$step->lpath_id."\" step_type=\"".$step->step_type."\" parent_id=\"".$step->parent_id."\" ordering=\"".$step->ordering."\">\n";
					$export_xml .= "\t\t\t\t\t\t<step_name><![CDATA[".$step->step_name."]]></step_name>\r\n";
					$export_xml .= "\t\t\t\t\t\t<step_shortdescription><![CDATA[".$step->step_shortdescription."]]></step_shortdescription>\r\n";
					$export_xml .= "\t\t\t\t\t\t<step_description><![CDATA[".$step->step_description."]]></step_description>\r\n";
					$export_xml .= "\t\t\t\t\t</step>\n";
				}
			}
			$export_xml .= "\t\t\t\t</steps>\n";
			//////////////////////////////////////////////////////////////////////////////
			$lpath_conds = array();
			foreach ($lpath_all_conds as $lp_co) {
				if ($lp_co->lpath_id == $lpath->id) {
					$lpath_conds[] = $lp_co;
				}
			}
			$export_xml .= "\t\t\t\t<conds>\n";
			foreach ($lpath_conds as $cond){
				$export_xml .= "\t\t\t\t\t<cond lpath_id=\"".$cond->lpath_id."\" step_id=\"".$cond->step_id."\" ref_step=\"".$cond->ref_step."\" cond_type=\"".$cond->cond_type."\" cond_value=\"".$cond->cond_value."\" >\n";
				$export_xml .= "\t\t\t\t\t</cond>\n";
			}
			$export_xml .= "\t\t\t\t</conds>\n";
			$export_xml .= "\t\t\t</learn_path>\n";
			$processed_lpaths[] = $lpath->id;
		}
		$export_xml .= "\t\t</learn_paths>\n";
	}




	//TOPICS - 27.03.2008 (DEN)
	$topic_ids = array();
	$export_xml .= "\t\t<course_topics>\n";
	foreach ($topics as $c_topic){
		$export_xml .= "\t\t\t<c_topic topic_id=\"".$c_topic->id."\" ordering=\"".$c_topic->ordering."\" published=\"".$c_topic->published."\" publish_start=\"".$c_topic->publish_start."\" start_date=\"".$c_topic->start_date."\" publish_end=\"".$c_topic->publish_end."\" end_date=\"".$c_topic->end_date."\" is_time_related=\"".$c_topic->is_time_related."\" show_period=\"".$c_topic->show_period."\">\n";
		$export_xml .= "\t\t\t\t<topic_name><![CDATA[".$c_topic->name."]]></topic_name>\r\n";
		$export_xml .= "\t\t\t\t<topic_description><![CDATA[".$c_topic->description."]]></topic_description>\r\n";
		$export_xml .= "\t\t\t</c_topic>\n";
		$topic_ids[] = $c_topic->id;
	}
	$export_xml .= "\t\t</course_topics>\n";

	/* define('_CHAPTER_ID', 1);
	define('_DOCUMENT_ID', 2);
	define('_LINK_ID', 3);
	define('_CONTENT_ID', 4);
	define('_QUIZ_ID', 5);
	define('_SCORM_ID', 6);
	define('_LPATH_ID', 7); */ 

	$export_xml .= "\t\t<course_topic_items>\n";
	foreach ($topic_items as $ct_item){
		if (in_array($ct_item->topic_id,$topic_ids)) {
			$proceed_add = false;
			if ($ct_item->item_type == 2) { // documents
				if (in_array(1,$cid) && in_array($ct_item->item_id,$processed_docs)) {
					$proceed_add = true;
				}
			} elseif ($ct_item->item_type == 3) { // links
				if (in_array(4,$cid) && in_array($ct_item->item_id,$processed_links)) {
					$proceed_add = true;
				}
			} elseif ($ct_item->item_type == 5) { // quizzes
				if (in_array(5,$cid) && in_array($ct_item->item_id,$processed_quizzes)) {
					$proceed_add = true;
				}
			} elseif ($ct_item->item_type == 7) { // lpaths
				if (in_array(2,$cid) && in_array($ct_item->item_id,$processed_lpaths)) {
					$proceed_add = true;
				}
			}
			if ($proceed_add) {
				$export_xml .= "\t\t\t<ct_item topic_id=\"".$ct_item->topic_id."\" item_id=\"".$ct_item->item_id."\" item_type=\"".$ct_item->item_type."\" ordering=\"".$ct_item->ordering."\" is_shown=\"".($ct_item->show?'1':'0')."\" >\n";
				$export_xml .= "\t\t\t</ct_item>\n";
			}
		}
	}
	$export_xml .= "\t\t</course_topic_items>\n";



	//GradeBook Items:
	if (in_array(8,$cid)) {
		$export_xml .= "\t\t<gradebook_items>\n";
		foreach ($gb_items as $gb_item){
			$export_xml .= "\t\t\t<gb_item gbi_option=\"".$gb_item->gbi_option."\" ordering=\"".$gb_item->ordering."\" >\n";
			$export_xml .= "\t\t\t\t<gbi_name><![CDATA[".$gb_item->gbi_name."]]></gbi_name>\r\n";
			$export_xml .= "\t\t\t\t<gbi_description><![CDATA[".$gb_item->gbi_description."]]></gbi_description>\r\n";
			$export_xml .= "\t\t\t\t<gb_category><![CDATA[".$gb_item->gb_category."]]></gb_category>\r\n";
			$export_xml .= "\t\t\t</gb_item>\n";
		}
		$export_xml .= "\t\t</gradebook_items>\n";

		//GradeBook Scale:
		$export_xml .= "\t\t<gradebook_scale>\n";
		foreach ($gb_scale as $gb_sc){
			$export_xml .= "\t\t\t<gb_scale min_val=\"".$gb_sc->min_val."\" max_val=\"".$gb_sc->max_val."\" ordering=\"".$gb_sc->ordering."\" >\n";
			$export_xml .= "\t\t\t\t<scale_name><![CDATA[".$gb_sc->scale_name."]]></scale_name>\r\n";
			$export_xml .= "\t\t\t</gb_scale>\n";
		}
		$export_xml .= "\t\t</gradebook_scale>\n";

		// 27.03.2008 (DEN) GradeBook LPaths:
		$export_xml .= "\t\t<gradebook_lpaths>\n";
		foreach ($gb_lpaths as $gb_lp){
			$export_xml .= "\t\t\t<gb_lpath learn_path_id=\"".$gb_lp->learn_path_id."\" >\n";
			$export_xml .= "\t\t\t</gb_lpath>\n";
		}
		$export_xml .= "\t\t</gradebook_lpaths>\n";
	}

	//end of course backup
	$export_xml .= "\t</course_backup>";
	//end xml file

	$filename_xml = $JLMS_CONFIG->getCfg('absolute_path').'/media/export.xml';
	//$handle = fopen($filename_xml, 'w');
	if(($handle = @fopen($filename_xml,'w')) === FALSE){
        die("Failed to open file 'media/export.xml' for writing! Check that folder 'media' is writable and there is no read-only 'export.xml' file in it.");
    }

	// try to write in XML file our xml-contents.
    if (@fwrite($handle, $export_xml) === FALSE) {
		echo "Could not create writable XML file!  Check that folder 'media' is writable and there is no read-only 'export.xml' file in it.";
		exit;
    }
	fclose($handle);

	$uniq = mktime();
	if ($type == 'gen'){
		$dir = $lms_cfg_backup_folder."/";
		//chmod($dir, '777');
		$backup_zip = $dir.'course_backup_'.$course_id.'_'.$uniq.'.zip';
		$backup_zip_file = 'course_backup_'.$course_id.'_'.$uniq.'.zip';
		$temp_backup_dir = $dir.'course_backup_'.$course_id.'_'.$uniq;
	} elseif ($type == 'exp'){
		$dir = $JLMS_CONFIG->getCfg('absolute_path')."/media/";
		$backup_zip = $dir.'course_export_'.$course_id.'_'.$uniq.'.zip';
		$backup_zip_file = 'course_export_'.$course_id.'_'.$uniq.'.zip';
		$temp_backup_dir = $dir.'course_export_'.$course_id.'_'.$uniq;
	}
	elseif ($type == 'tpl')
	{
		$dir = $lms_cfg_backup_folder."/";
		//chmod($dir, '777');
		$backup_zip = $dir.'template_'.$course_id.'_'.$uniq.'.zip';
		$backup_zip_file = 'template_'.$course_id.'_'.$uniq.'.zip';
		$temp_backup_dir = $dir.'template_'.$course_id.'_'.$uniq;
	}
	if (@mkdir($temp_backup_dir)) {
		@mkdir($temp_backup_dir.'/export');
		$temp_backup_dir2 = $temp_backup_dir.'/export';
		$pcl_files = array();

		$filename_xml = str_replace("\\", "/", $filename_xml);
		@rename($filename_xml, $temp_backup_dir2.'/export.xml');
		$pcl_files[] = $temp_backup_dir2.'/export.xml';
		if (@mkdir($temp_backup_dir2.'/files')) {
			foreach($files as $file){
				$filename = $lms_cfg_doc_folder.'/'.$file->file_srv_name;
				if (@copy($filename, $temp_backup_dir2.'/files/'.$file->file_srv_name )) {
					$pcl_files[] = $temp_backup_dir2.'/files/'.$file->file_srv_name;
				}
			}
		}
		if (in_array(3,$cid)) {
			if (@mkdir($temp_backup_dir2.'/scorm')) {
				foreach($scorms as $scorm){
					$filename = $JLMS_CONFIG->getCfg('absolute_path') . '/'.$lms_cfg_scorm.'/'.$scorm->package_srv_name;
					$filename = str_replace("\\", "/", $filename);
					if (@copy($filename, $temp_backup_dir2.'/scorm/'.$scorm->package_srv_name )) {
						$pcl_files[] = $temp_backup_dir2.'/scorm/'.$scorm->package_srv_name;
					}
				}
			}
		}
		if (in_array(1,$cid)) {
			if (@mkdir($temp_backup_dir2.'/zippacks')) {
				foreach($zipped_docs as $zipdoc){
					$filename = $JLMS_CONFIG->getCfg('absolute_path') . '/'.$lms_cfg_scorm.'/'.$zipdoc->zip_srv_name;
					$filename = str_replace("\\", "/", $filename);
					if (@copy($filename, $temp_backup_dir2.'/zippacks/'.$zipdoc->zip_srv_name )) {
						$pcl_files[] = $temp_backup_dir2.'/zippacks/'.$zipdoc->zip_srv_name;
					}
				}
			}
		}
		if ($lms_cfg_quiz_enabled && in_array(5,$cid)) {
			if (@mkdir($temp_backup_dir2.'/quiz_images')) {
				foreach($quizzes_files as $quiz_image){
					$filename = $JLMS_CONFIG->getCfg('absolute_path') . '/images/joomlaquiz/images/'.$quiz_image;
					$filename = str_replace("\\", "/", $filename);
					if (@copy($filename, $temp_backup_dir2.'/quiz_images/'.$quiz_image )) {
						$pcl_files[] = $temp_backup_dir2.'/quiz_images/'.$quiz_image;
					}
				}
			}
		}

		$pz = new PclZip($temp_backup_dir.'/'.$backup_zip_file);

		$rrr= $pz->create($pcl_files, PCLZIP_OPT_REMOVE_PATH, $temp_backup_dir2);//, '', $temp_backup_dir.'/');
		@rename($temp_backup_dir.'/'.$backup_zip_file, $dir.$backup_zip_file);
		require_once(_JOOMLMS_FRONT_HOME . "/includes/jlms_dir_operation.php");
		deldir( $temp_backup_dir.'/' );
		$backup_zip = $dir.$backup_zip_file;
	} else {
		$pz = new PclZip($backup_zip);
	
		$filename_xml = str_replace("\\", "/", $filename_xml);
		$xm_path = $JLMS_CONFIG->getCfg('absolute_path').'/media/';
		$xm_path =  str_replace("\\", "/", $xm_path);
		$pz->create($filename_xml, '', $filename_xml = $xm_path);
	
		//add _lms_course_files_ catalog
		foreach($files as $file){
			$filename = $lms_cfg_doc_folder.'/'.$file->file_srv_name;
			$pz->add($filename,'files', $lms_cfg_doc_folder.'/');
		}
	
		//add lms_scorm archive catalog
		if (in_array(3,$cid)) {
			foreach($scorms as $scorm){
				$filename = $JLMS_CONFIG->getCfg('absolute_path') . '/'.$lms_cfg_scorm.'/'.$scorm->package_srv_name;
				$filename = str_replace("\\", "/", $filename);
				$xm_path = $JLMS_CONFIG->getCfg('absolute_path') . '/'.$lms_cfg_scorm.'/';
				$xm_path =  str_replace("\\", "/", $xm_path);
				$pz->add($filename,'scorm', $xm_path);
			}
		}
	
		//add lms_documents_zip archive catalog
		if (in_array(1,$cid)) {
			foreach($zipped_docs as $zipdoc){
				$filename = $JLMS_CONFIG->getCfg('absolute_path') . '/'.$lms_cfg_scorm.'/'.$zipdoc->zip_srv_name;
				$filename = str_replace("\\", "/", $filename);
				$xm_path = $JLMS_CONFIG->getCfg('absolute_path') . '/'.$lms_cfg_scorm.'/';
				$xm_path =  str_replace("\\", "/", $xm_path);
				$pz->add($filename,'zippacks', $xm_path);
			}
		}
	
		//add quiz images
		if ($lms_cfg_quiz_enabled && in_array(5,$cid)) {
			foreach($quizzes_files as $quiz_image){
				$filename = $JLMS_CONFIG->getCfg('absolute_path') . '/images/joomlaquiz/images/'.$quiz_image;
				$filename = str_replace("\\", "/", $filename);
				$xm_path = $JLMS_CONFIG->getCfg('absolute_path') . '/images/joomlaquiz/images/';
				$xm_path =  str_replace("\\", "/", $xm_path);
				$pz->add($filename,'quiz_images', $xm_path);
			}
		}
	}

	//add sql file
	if ($type == 'gen'){
		$query = "INSERT INTO `#__lms_courses_backups` ( course_id, name, backupdate) VALUES ('$course_id', 'course_backup_".$course_id."_".$uniq.".zip','".date('Y-m-d H:i:s',$uniq)."')";
		$JLMS_DB -> setQuery($query);
		$JLMS_DB -> query();
	} elseif ($type == 'exp'){
		require_once ($JLMS_CONFIG->getCfg('absolute_path')."/components/com_joomla_lms/includes/jlms_download.php");
		JLMS_download ('course_export_'.$course_id.'__'.str_replace('-','_',date('Y-m-d')).'.zip', $backup_zip, false);
		@unlink($backup_zip);
		exit;
	} elseif ($type == 'tpl') {
		return "template_".$course_id."_".$uniq.".zip";
	}
	} // end of if (!empty($cid)) {
}
?>