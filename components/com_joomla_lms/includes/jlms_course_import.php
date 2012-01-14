<?php
/**
* jlms_course_import.php
* (c) JoomaLMS eLearning Software http://www.joomlalms.com/
**/

defined( '_JLMS_EXEC' ) or die( 'Restricted access' );

/*
------------------------------------------------------------------------
		Using $cid variable
------------------------------------------------------------------------
		$rows = array();
		$rows[] = array('id' => 1, 'name' => _JLMS_TOOLBAR_DOCS);
		$rows[] = array('id' => 2, 'name' => _JLMS_TOOLBAR_LPATH);
		$rows[] = array('id' => 3, 'name' => 'SCORMs');
		$rows[] = array('id' => 4, 'name' => _JLMS_TOOLBAR_LINKS);
		$rows[] = array('id' => 5, 'name' => _JLMS_TOOLBAR_QUIZZES);
		$rows[] = array('id' => 6, 'name' => _JLMS_TOOLBAR_AGENDA);
		$rows[] = array('id' => 7, 'name' => _JLMS_TOOLBAR_HOMEWORK);
		$rows[] = array('id' => 8, 'name' => _JLMS_TOOLBAR_GRADEBOOK.' (settings only)');*/

function JLMS_courseImport( $option, $course_id = 0, $imp_tools = array(1,2,3,4,5,6,7,8) ) {
	global $JLMS_DB, $JLMS_CONFIG, $my;
	require_once($JLMS_CONFIG->get('absolute_path')."/components/com_joomla_lms/includes/libraries/lms.lib.zip.php");
	require_once($JLMS_CONFIG->get('absolute_path')."/components/com_joomla_lms/includes/jlms_dir_operation.php");
	require_once($JLMS_CONFIG->get('absolute_path')."/components/com_joomla_lms/includes/jlms_zip_operation.php");

	$is_new_course = false;
	@set_time_limit('3000');
	// Check that the zlib is available
	if(!extension_loaded('zlib')) {
		mosErrorAlert ("Error! zlib library unavailable");
	}
	$backupfile = mosGetParam( $_FILES, 'jlms_ifile', null );
	$archive = JLMS_courseImport_main($backupfile,$option,$course_id,$imp_tools);
	@unlink($archive);
}

function JLMS_templateImport( $option, $course_id = 0, $imp_tools = array(1,2,3,4,5,6,7,8) ) {
	global $JLMS_DB, $JLMS_CONFIG, $my;
	require_once($JLMS_CONFIG->get('absolute_path')."/components/com_joomla_lms/includes/libraries/lms.lib.zip.php");
	require_once($JLMS_CONFIG->get('absolute_path')."/components/com_joomla_lms/includes/jlms_dir_operation.php");
	require_once($JLMS_CONFIG->get('absolute_path')."/components/com_joomla_lms/includes/jlms_zip_operation.php");

	$is_new_course = false;
	@set_time_limit('3000');
	// Check that the zlib is available
	if(!extension_loaded('zlib')) {
		mosErrorAlert ("Error! zlib library unavailable");
	}
	
	$tpl_id = intval(mosGetParam( $_POST, 'tpl_id', 0 ));
	$course_zname = mosGetParam( $_POST, 'course_zname', '' );
	$JLMS_DB->setQuery("SELECT filename FROM #__lms_courses_template WHERE id=".$tpl_id);
	$backupfile['name'] = $JLMS_DB->loadResult();
	$query = "SELECT lms_config_value FROM `#__lms_config` WHERE lms_config_var = 'jlms_backup_folder'";
		$JLMS_DB->setQuery($query);
		$filepath = $JLMS_DB->loadResult();
	$backupfile['tmp_name']	= $filepath.'/'.$backupfile['name'];
	JLMS_courseImport_main($backupfile,$option,$course_id,$imp_tools,$course_zname,true);
}

function JLMS_courseImport_main($backupfile,$option,$course_id,$imp_tools,$course_zname='',$is_template=false){
	global $JLMS_DB, $JLMS_CONFIG, $my;
		
	if (!$backupfile) {
		mosErrorAlert (_JLMS_EM_SELECT_FILE);
	}
	$backupfile_name = $backupfile['name'];
	$filename = explode(".", $backupfile_name);
	if (empty($backupfile_name)) {
		mosErrorAlert(_JLMS_EM_SELECT_FILE);
	}
	//commented (12.01.2007 Bjarne request forum post #331)
	#if (eregi("[^0-9a-zA-Z_]", $filename[0])) {
	#	mosErrorAlert("File must only contain alphanumeric characters and no special symbols and spaces please.");
	#}
	if (strcmp(substr($backupfile_name,-4,1),".")) {
		mosErrorAlert(_JLMS_EM_BAD_FILEEXT);
	}
	if (strcmp(substr($backupfile_name,-4),".zip")) {
		mosErrorAlert(_JLMS_EM_BAD_FILEEXT);
	}
		
	$tmp_name = $backupfile['tmp_name'];
	if (!file_exists($tmp_name)) {
		mosErrorAlert (_JLMS_EM_UPLOAD_SIZE_ERROR);
	}
	if (preg_match("/.zip$/", strtolower($backupfile_name))) {
		
		$zipFile = new pclZip($tmp_name);
		$zipContentArray = $zipFile->listContent();
		$exp_xml_file = false;
		if (!empty($zipContentArray)) {
			foreach($zipContentArray as $thisContent) {
				if ( preg_match('~.(php.*|phtml)$~i', $thisContent['filename']) ) {
					mosErrorAlert(_JLMS_EM_READ_PACKAGE_ERROR);
				}
				if ($thisContent['filename'] == 'export.xml'){
					$exp_xml_file = true;
				}
			}
		}
		if ($exp_xml_file == false){
			mosErrorAlert("Could not find a Course XML setup file in the package.");
		}
	} else {
		mosErrorAlert(_JLMS_EM_BAD_FILEEXT);
	}

	$config		= & JFactory::getConfig();
	$tmp_dest	= $config->getValue('config.tmp_path').DS.$backupfile['name'];
	$tmp_src	= $backupfile['tmp_name'];

	// Move uploaded file
	jimport('joomla.filesystem.file');
	
	if($is_template){
		$uploaded = JFile::copy($tmp_src, $tmp_dest);
	} else {
		$uploaded = JFile::upload($tmp_src, $tmp_dest);
	}

	$extract_dir = $config->getValue('config.tmp_path').DS."course_backup_".uniqid(rand(), true).DS;
	$archive = $tmp_dest;//$JLMS_CONFIG->getCfg('absolute_path')."/media/".$backupfile['name'];
	if(!is_file($archive)) $archive = $backupfile['tmp_name'];
	//exstract archive in uniqfolder media
	
	JLMS_Zip::extractFile( $archive, $extract_dir);
	
	$xmlFile = $extract_dir."export.xml";

	$xmlDoc = &JLMSFactory::getXMLParser();
	//$xmlDoc->resolveErrors( true );
	if (!$xmlDoc->loadFile( $xmlFile )) {
		echo "<script> alert('Error during reading xml file'); window.history.go(-1); </script>\n";
		exit();
	}

	$root = &$xmlDoc->document;

	if ($root->name() != 'course_backup') {
		echo "<script> alert('Not a Course installation file'); window.history.go(-1); </script>\n";
		exit();
	}
	$course = new stdClass();

	// ****************************************************************************************************
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

	// ****************************************************************************************************
	// Get course DATA and insert it into the 'lms_courses' table 

	if (!$course_id) {
		$element 					= &$root->getElementByPath('name');
		if(!$course_zname) {
			$course->course_name	= $element ? $element->data() : '';
		} else {
			$course->course_name	= $course_zname;
		}
		if ($course->course_name) {
			$element 					= &$root->getElementByPath('description');
			$course->course_description = $element ? $element->data() : '';
		//	echo $xmlDoc->getErrorString();
		//	var_dump($course->course_description); die;
			
			
			$element 					= &$root->getElementByPath('metadesc');
			$course->metadesc			= $element ? $element->data() : '';
			$element 					= &$root->getElementByPath('metakeys');
			$course->metakeys			= $element ? $element->data() : '';
			$element 					= &$root->getElementByPath('self_registration');
			$course->self_reg			= $element ? $element->data() : '';
			$element 					= &$root->getElementByPath('course_paid');
			$course->paid				= $element ? $element->data() : 0;
			$course->add_forum 			= 0;
			$course->add_chat 			= 0;
			$course->owner_id 			= $my->id;
			$course->published 			= 0;
			$element 					= &$root->getElementByPath('publish_start');
			$course->publish_start		= $element ? $element->data() : 0;
			$element 					= &$root->getElementByPath('publish_end');
			$course->publish_end		= $element ? $element->data() : 0;
			$element					= &$root->getElementByPath('publish_start_date');
			$course->start_date			= $element ? $element->data() : '0000-00-00';
			$element 					= &$root->getElementByPath('publish_end_date');
			$course->end_date			= $element ? $element->data() : '0000-00-00';
			$element 					= &$root->getElementByPath('spec_reg');
			$course->spec_reg			= $element ? $element->data() : 0;

			$course->cat_id = 0;
			$element 					= &$root->getElementByPath('course_category');
			$course_category_txt		= $element ? $element->data() : '';

			$element 					= &$root->getElementByPath('course_question');
			$course_question_txt		= $element ? $element->data() : '';

			// 02.03.2007 1.0.1 support
			$element 					= &$root->getElementByPath('course_params');
			$params_txt					= $element ? $element->data() : '';
			$course->params = '';
			$params = new JLMSParameters($params_txt);
			$params->def('lpath_redirect', 0);
			$params->def('agenda_view', 0);
			$params->def('dropbox_view', 0);
			$params->def('homework_view', 0);
			$params->def('learn_path', 0);
			$course_redirect_lp = $params->get('learn_path');
			$params->set('learn_path', 0);
			$params_ar = $params->toArray();
			if (is_array($params_ar)) {
				foreach ( $params_ar as $k=>$v) {
					$txt[] = "$k=$v";
				}
				$course->params = implode( "\n", $txt );
			}
			// to do:
			// check lpath_redirect parameter !!!!!!!!!

			$course->gid = 0;

			if ($course_category_txt) {
				$query = "SELECT id FROM #__lms_course_cats WHERE c_category = '".$course_category_txt."'";
				$JLMS_DB->SetQuery( $query );
				$course_cat_id = $JLMS_DB->LoadResult();
				if ($course_cat_id) {
					$course->cat_id		= $course_cat_id;
				}
			}

			$course->language 			= 0;
			$element 					= &$root->getElementByPath('language_name');
			$course_lang_txt			= $element ? $element->data() : '';
			if ($course_lang_txt) {
				$query = "SELECT id FROM #__lms_languages WHERE lang_name = '".$course_lang_txt."'";
				$JLMS_DB->SetQuery( $query );
				$course_lang_id = $JLMS_DB->LoadResult();
				if ($course_lang_id) {
					$course->language	= $course_lang_id;
				}
			}

			$JLMS_DB -> insertObject("#__lms_courses" , $course , "id");
			//get new Course_id
			$course_id = $JLMS_DB->insertid();
			$is_new_course = true;

			$default_teacher_role = 0;
			$query = "SELECT id FROM #__lms_usertypes WHERE roletype_id = 2 AND default_role = 1 LIMIT 0,1";
			$JLMS_DB ->setQuery($query);
			$default_teacher_role = intval($JLMS_DB->LoadResult());
			if (!$default_teacher_role) {
				$query = "SELECT id FROM #__lms_usertypes WHERE roletype_id = 2 LIMIT 0,1";
				$JLMS_DB ->setQuery($query);
				$default_teacher_role = intval($JLMS_DB->LoadResult());
				if (!$default_teacher_role) {
					$default_teacher_role = 1;
				}
			}
			// create teacher for imported course
			$query = "INSERT INTO `#__lms_user_courses` (user_id, course_id, role_id) VALUES ('".$my->id."','".$course_id."','".$default_teacher_role."')";
			$JLMS_DB ->setQuery($query);
			$JLMS_DB->query();

			// commented by DEN - 27.03.2008 - enrollment questions are moved into another section
			/*if ($course->spec_reg && $course_question_txt) {
				$query = "INSERT INTO #__lms_spec_reg_questions (course_id, course_question) VALUES ($course_id, ".$JLMS_DB->Quote($course_question_txt).")";
				$JLMS_DB->SetQuery( $query );
				$JLMS_DB->query();
			}*/
			
			// ****************************************************************************************************
			// Get hidden menu items DATA, insert it into 'lms_local_menu' table
		
			//get hidden menu items array from xml
			$element = &$root->getElementByPath('hidden_menu_items');
			$hidden_menus = JLMS_parse_XML_elements($element->children(), array('menu_id', 'user_access'), array());
			$i = 0;
			while( $i < count($hidden_menus) ){
				$hdmn = new stdClass();
				$hdmn->course_id	= $course_id;
				$hdmn->menu_id		= $hidden_menus[$i]->menu_id;
				$hdmn->user_access	= $hidden_menus[$i]->user_access;
				$query = "INSERT INTO #__lms_local_menu (course_id, menu_id, user_access) "
				. "\n VALUES (".$course_id.", ".intval($hdmn->menu_id).", ".intval($hdmn->user_access).")";
				$JLMS_DB->SetQuery( $query );
				$JLMS_DB->query();
				$i++;
			}
		}
	}

	if ($course_id) {

		//get hidden menu items array from xml
		$element = &$root->getElementByPath('course_questions');
		$course_questions = JLMS_parse_XML_elements($element->children(), array('is_optional', 'ordering'), array('question_text', 'default_answer'));
		if (isset($course_question_txt) && $course_question_txt) {
			$add = new stdClass();
			$add->is_optional		= 0;
			$add->ordering			= 0;
			$add->question_text		= $course_question_txt;
			$add->default_answer	= '';
			$course_questions[] = $add;
		}
		$i = 0;
		while( $i < count($course_questions) ){
			$insert = new stdClass();
			$insert->course_id		= $course_id;
			$insert->is_optional	= $course_questions[$i]->is_optional;
			$insert->ordering		= $course_questions[$i]->ordering;
			$insert->question_text	= $course_questions[$i]->question_text;
			$insert->default_answer	= $course_questions[$i]->default_answer;
			$insert->role_id		= 0;
			$JLMS_DB->insertObject("#__lms_spec_reg_questions" , $insert , "id");
			$i++;
		}


		/*$element 					= &$root->getElementsByPath('course_price', 1);
		$course_price				= $element ? $element->data() : 0;
		$query = "INSERT INTO #__lms_course_price (course_id, price ) VALUES('".$course_id."', '".$course_price."')";
		$JLMS_DB->SetQuery( $query );
		$JLMS_DB->query();*/
	
		// ****************************************************************************************************
		// Get files DATA, insert it into 'lms_files' table and copy files to the 'lms_files' folder
	
		//get files array from xml
		$element = &$root->getElementByPath('files');
		$files_pre = JLMS_parse_XML_elements($element->children(), array('id'), array('filename','servername'));

		// 05 June 2007. Nugno sformirovat' spisok of files - tol'ko tex? kotoryi deistvitel'no budut importirovat'sya
		//					(t.e. s uchetom vybrannyx tools pri 'import').
		$documents = array();
		if (in_array(1, $imp_tools)) {
			$element = &$root->getElementByPath('documents');
			$documents = JLMS_parse_XML_elements($element->children(), array('id', 'file_id', 'folder_flag', 'collapsed_folder', 'parent_id', 'ordering', 'published', 'publish_start', 'start_date', 'publish_start', 'publish_end', 'end_date', 'is_time_related', 'show_period'), array('doc_name', 'doc_description'));
		}

		$element = &$root->getElementByPath('certificates');
		$certificates = JLMS_parse_XML_elements($element->children(), array('id', 'file_id', 'crtf_type', 'text_x', 'text_y', 'text_size'), array('certificate_text', 'certificate_font'));

		$element = &$root->getElementByPath('certificate_texts');
		$certificate_texts = JLMS_parse_XML_elements($element->children(), array('parent_id', 'crtf_type', 'text_x', 'text_y', 'text_size'), array('add_certificate_text', 'certificate_font'));

		$quiz_images2 = array(); // for fields 'c_image' of the question (e.g. hostspot)
		$quiz_images = array();
		if (in_array(5, $imp_tools)) {
			$element_qimg = &$root->getElementByPath('quizzes_images');
			$quiz_images = JLMS_parse_XML_elements($element_qimg->children(), array('c_id', 'file_id'), array('quiz_image_name'));
		}

		/*if (in_array(5,$cid)) {
			$query = "SELECT * FROM `#__lms_certificates` WHERE course_id = '$course_id'";
		} else {
			$query = "SELECT * FROM `#__lms_certificates` WHERE course_id = '$course_id' AND crtf_type <> 2";
		}*/
		$files = array();
		foreach ($documents as $doc) {
			foreach ($files_pre as $file_pre) {
				if ($doc->file_id == $file_pre->id) {
					$is_exists = false;
					foreach ($files as $file_ex) {
						if ($file_ex->id == $file_pre->id) {
							$is_exists = true;
							break;
						}
					}
					if (!$is_exists) {
						$files[] = $file_pre;
					}
					break;
				}
			}
		}
		foreach ($certificates as $crtf) {
			foreach ($files_pre as $file_pre) {
				if ($crtf->file_id == $file_pre->id) {
					$do_add = false;
					if ($crtf->crtf_type == 2) {
						if (in_array(5, $imp_tools)) {
							$do_add = true;
						}
					} else { $do_add = true; }
					if ($do_add) {
						$is_exists = false;
						foreach ($files as $file_ex) {
							if ($file_ex->id == $file_pre->id) {
								$is_exists = true;
								break;
							}
						}
						if (!$is_exists) {
							$files[] = $file_pre;
						}
						break;
					}
				}
			}
		}
		foreach ($quiz_images as $qimg) {
			foreach ($files_pre as $file_pre) {
				if ($qimg->file_id == $file_pre->id) {
					$is_exists = false;
					foreach ($files as $file_ex) {
						if ($file_ex->id == $file_pre->id) {
							$is_exists = true;
							break;
						}
					}
					if (!$is_exists) {
						$files[] = $file_pre;
					}
					break;
				}
			}
		}

		$i = 0;
		$fromDir = $extract_dir."files/";
		$toDir   = $lms_cfg_doc_folder."/";
		while( $i < count($files) ){
			$insert_file = new stdClass();
			$insert_file->file_name = $files[$i]->filename;
			$file_unique_name = str_pad($course_id,4,'0',STR_PAD_LEFT) . '_' . md5(uniqid(rand(), true)) . '.' . substr($files[$i]->servername,-3);
			$insert_file->file_srv_name = $file_unique_name;
			$insert_file->owner_id = $my->id;
			$JLMS_DB->insertObject("#__lms_files", $insert_file, "id");
			$files[$i]->new_file_id = $JLMS_DB->insertid();
			rename ($fromDir.$files[$i]->servername, $toDir.$file_unique_name);
			$i++;
		}

		$zip_docs = array();
		if (in_array(1, $imp_tools)) {
			// ****************************************************************************************************
			// Get ZIPPACK's DATA, insert it into 'lms_documents_zip' table and copy zippack files to the 'lms_scorm' folder
	
			$fromDir = $extract_dir."zippacks/";
			$toDir = $JLMS_CONFIG->getCfg('absolute_path')."/".$lms_cfg_scorm."/";
		
			$element = &$root->getElementByPath('zipped_documents');
			$zip_docs = JLMS_parse_XML_elements($element->children(), array('id', 'upload_time', 'count_files', 'zip_size', 'zipfile_size', 'is_time_related', 'show_period' ), array('zip_folder', 'zip_srv_name', 'zip_name', 'startup_file'));
			$i = 0;
			while ( $i< count($zip_docs) ){
				$insert = new stdClass();
				$insert->owner_id			= $my->id;
				$insert->course_id			= $course_id;
				$folder_unique_name			= str_pad($my->id,4,'0',STR_PAD_LEFT) . '_zip_' . md5(uniqid(rand(), true));
				$insert->zip_folder			= $folder_unique_name;
				$file_unique_name			= $folder_unique_name.".zip";
				$insert->zip_srv_name		= $file_unique_name;
				$insert->zip_name			= $zip_docs[$i]->zip_name;
				$insert->startup_file		= $zip_docs[$i]->startup_file;
				$insert->count_files		= $zip_docs[$i]->count_files;
				$insert->zip_size			= $zip_docs[$i]->zip_size;
				$insert->zipfile_size		= $zip_docs[$i]->zipfile_size;
				$insert->upload_time		= date('Y-m-d H:i:s');//$scorms[$i]->upload_time;				
				//insert into DB
				$JLMS_DB -> insertObject("#__lms_documents_zip" , $insert , "id");
				$zip_docs[$i]->new_zip_id	= $JLMS_DB->insertid();
				//move scrom package
				rename($fromDir.$zip_docs[$i]->zip_srv_name, $toDir.$file_unique_name);
				//extract SCORM package archive
				extractBackupArchive($toDir.$file_unique_name, $toDir.$folder_unique_name);
				$i++;
			}
		}

		if (in_array(1, $imp_tools)) {
			// ****************************************************************************************************
			// Get documents DATA and insert it into the 'lms_documents' table

			//$element = &$root->getElementsByPath('documents', 1);
			//$documents = JLMS_parse_XML_elements($element->children(), array('id', 'file_id', 'folder_flag', 'parent_id', 'ordering', 'published', 'publish_start', 'start_date', 'publish_start', 'publish_end', 'end_date'), array('doc_name', 'doc_description'));
			// 05 June 2007 - this array is generated above (~~ line 270)
			$j = 0;
			$collapsed_folders = array();
			while ( $j < count($documents) ){
				$insert = new stdClass();
				$insert->course_id = $course_id;
				$insert->owner_id = $my->id;
				$insert->file_id = 0;
				if ($documents[$j]->file_id) {
					if ($documents[$j]->folder_flag == 2) {
						// search $zip_docs for new file_id
						for ($i=0; $i<count($zip_docs); $i++){
							if($zip_docs[$i]->id == $documents[$j]->file_id){
								$insert->file_id = $zip_docs[$i]->new_zip_id; break;
							}	
						}
					} else {
						// search $files for new file_id
						for ($i=0; $i<count($files); $i++){
							if($files[$i]->id == $documents[$j]->file_id){
								$insert->file_id = $files[$i]->new_file_id; break;
							}	
						}
					}
				}
				$insert->folder_flag		= $documents[$j]->folder_flag;
				$insert->doc_name			= $documents[$j]->doc_name;
				$insert->doc_description	= $documents[$j]->doc_description;
				$insert->ordering			= $documents[$j]->ordering;
				$insert->published			= $documents[$j]->published;
				$insert->publish_start		= $documents[$j]->publish_start;
				$insert->start_date			= $documents[$j]->start_date;
				$insert->publish_end		= $documents[$j]->publish_end;
				$insert->end_date			= $documents[$j]->end_date;
				$insert->is_time_related	= $documents[$i]->is_time_related;
				$insert->show_period			= $documents[$i]->show_period;
				// search processed $documents for parent_id
				$parent = $documents[$j]->parent_id;
				if ($parent){
					$a = 0;
					while ($a < $j){
						if ($documents[$a]->id == $parent){
							$parent = $documents[$a]->new_doc_id; break;
						}
						$a ++;
					}
				}
				$insert->parent_id = $parent;
				$do_ins_object = true;
				$ins_object_folder_already_exists_id = 0;
				if ($insert->folder_flag == 1 && $course_id) {
					//if we are merging imported course with already existeten course, then some folders can be already exists 
					$query = "SELECT id FROM #__lms_documents WHERE doc_name = ".$JLMS_DB->quote($insert->doc_name)." AND parent_id = ".intval($insert->parent_id)." AND folder_flag = 1 AND file_id = 0 AND course_id = $course_id";
					$JLMS_DB->SetQuery($query);
					$ins_object_folder_already_exists_id = $JLMS_DB->LoadResult();
					if ($ins_object_folder_already_exists_id) {
						$do_ins_object = false;
					}
				}
				if ($do_ins_object) {
					$JLMS_DB -> insertObject("#__lms_documents" , $insert , "id");
					$documents[$j]->new_doc_id = $JLMS_DB->insertid();
				} else {
					$documents[$j]->new_doc_id = $ins_object_folder_already_exists_id;
				}

				if ($do_ins_object) {
					if ($documents[$j]->folder_flag == 1 && !$documents[$j]->file_id && $documents[$j]->collapsed_folder) {
						$collapsed_folders[] = $documents[$j]->new_doc_id;
					}
				}

				$j++;
			}
			if (!empty($collapsed_folders)) {
				$query = "INSERT INTO #__lms_documents_view (course_id, doc_id) VALUES";
				$s = '';$is_add = false;
				foreach ($collapsed_folders as $cf) {
					if ($cf) {
						$is_add = true;
						$query .= $s."\n ($course_id, $cf)";
						$s = ',';
					}
				}
				if ($is_add) {
					$JLMS_DB->SetQuery($query);
					$JLMS_DB->query();
				}
			}
		}

		$scorms = array();
		if (in_array(3, $imp_tools)) {
			// ****************************************************************************************************
			// Get SCORM's DATA, insert it into 'lms_scorm_packages' table and copy scorm files to the 'lms_scorm' folder

			$fromDir = $extract_dir."scorm/";
			$toDir = $JLMS_CONFIG->getCfg('absolute_path')."/".$lms_cfg_scorm."/";

			$element = &$root->getElementByPath('scorms');

			$scorms = JLMS_parse_XML_elements($element->children(), array('id', 'upload_time'), array('foldersrvname', 'packagesrvname', 'packageusername'));

			$i = 0;
			while ( $i< count($scorms) ){
				$insert = new stdClass();
				$insert->owner_id			= $my->id;
				$insert->course_id			= $course_id;
				$folder_unique_name			= str_pad($my->id,4,'0',STR_PAD_LEFT) . '_' . md5(uniqid(rand(), true));
				$insert->folder_srv_name	= $folder_unique_name;
				$file_unique_name			= $folder_unique_name.".zip";
				$insert->package_srv_name	= $file_unique_name;
				$insert->package_user_name	= $scorms[$i]->packageusername;
				$insert->upload_time		= date('Y-m-d H:i:s');//$scorms[$i]->upload_time;
				//insert into DB
				$JLMS_DB -> insertObject("#__lms_scorm_packages" , $insert , "id");
				$scorms[$i]->new_sco_id		= $JLMS_DB->insertid();
				//move scrom package
				rename($fromDir.$scorms[$i]->packagesrvname, $toDir.$file_unique_name);
				//extract SCORM package archive
				extractBackupArchive($toDir.$file_unique_name, $toDir.$folder_unique_name);
				$i++;
			}
		}

		$links = array();
		if (in_array(4, $imp_tools)) {
			// ****************************************************************************************************
			// Get links DATA and insert it into the 'lms_links' table

			$element = &$root->getElementByPath('links');
			$links = JLMS_parse_XML_elements($element->children(), array('id', 'link_type', 'ordering', 'published', 'is_time_related', 'show_period'), array('linkname', 'linkhref', 'description', 'link_params'));
			$i = 0;			
			while ( $i < count ($links) ){
				$insert = new stdClass();
				$insert->course_id			= $course_id;
				$insert->owner_id			= $my->id;
				$insert->link_name			= $links[$i]->linkname;
				$insert->link_href			= $links[$i]->linkhref;
				$insert->link_description	= $links[$i]->description;
				$insert->link_type			= $links[$i]->link_type;
				$insert->ordering			= $links[$i]->ordering;
				$insert->published			= $links[$i]->published;
				$insert->is_time_related	= $links[$i]->is_time_related;
				$insert->show_period		= $links[$i]->show_period;
                $insert->params     		= $links[$i]->link_params;
				
				$JLMS_DB->insertObject("#__lms_links", $insert, "id");
				$links[$i]->new_link_id = $JLMS_DB->insertid();
				$i++;
			}
		}

		$homeworks = array();
		if (in_array(7, $imp_tools)) {
			// ****************************************************************************************************
			// Get homeworks DATA and insert it into the 'lms_homework' table

			$element = &$root->getElementByPath('homework_tool');
			$homeworks = JLMS_parse_XML_elements($element->children(), array('id', 'post_date', 'end_date', 'is_time_related', 'show_period'), array('hw_name', 'description', 'short_description'));
			$i = 0;
			while ( $i < count ($homeworks) ){
				$insert = new stdClass();
				$insert->course_id				= $course_id;
				$insert->hw_name				= $homeworks[$i]->hw_name;
				$insert->hw_description			= $homeworks[$i]->description;
				$insert->hw_shortdescription	= $homeworks[$i]->short_description;
				$insert->post_date				= $homeworks[$i]->post_date;
				$insert->end_date				= $homeworks[$i]->end_date;
				$insert->is_time_related		= $homeworks[$i]->is_time_related;
				$insert->show_period				= $homeworks[$i]->show_period;
				
				$JLMS_DB->insertObject("#__lms_homework", $insert, "id");
				$homeworks[$i]->new_hw_id = $JLMS_DB->insertid();
				$i++;
			}
		}

		$announcements = array();
		if (in_array(6, $imp_tools)) {
			// ****************************************************************************************************
			// Get announcements DATA and insert it into the 'lms_agenda' table

			$element = &$root->getElementByPath('announcement_tool');
			$announcements = JLMS_parse_XML_elements($element->children(), array('id', 'start_date', 'end_date', 'is_time_related', 'show_period'), array('announcement_title', 'announcement_content'));
			$i = 0;
			while ( $i < count ($announcements) ){
				$insert = new stdClass();
				$insert->course_id			= $course_id;
				$insert->owner_id			= $my->id;
				$insert->title				= $announcements[$i]->announcement_title;
				$insert->content			= $announcements[$i]->announcement_content;
				$insert->start_date			= $announcements[$i]->start_date;
				$insert->end_date			= $announcements[$i]->end_date;
				$insert->is_time_related	= $announcements[$i]->is_time_related;
				$insert->show_period		= $announcements[$i]->show_period;
				
				$JLMS_DB->insertObject("#__lms_agenda", $insert, "agenda_id");
				$announcements[$i]->new_ag_id = $JLMS_DB->insertid();
				$i++;
			}
		}

		// ****************************************************************************************************
		// Get certificates DATA and insert it into the 'lms_certificates' table
	
		//$element = &$root->getElementsByPath('certificates', 1);
		//$certificates = JLMS_parse_XML_elements($element->children(), array('id', 'file_id', 'crtf_type', 'text_x', 'text_y', 'text_size'), array('certificate_text'));
		// 05 June 2007 - this array is generated above (~~ line 270)
		$i = 0;
		$crtf_ids_exp = array();
		while ( $i < count ($certificates) ){
			$do_add = false;
			if ($certificates[$i]->crtf_type == 2) {
				if (in_array(5, $imp_tools)) {
					$do_add = true;
				}
			} else { $do_add = true; }
			if ($do_add) {
				$insert = new stdClass();
				$insert->course_id = $course_id;
				$insert->file_id = 0;
				$insert->parent_id = 0;
				if ($certificates[$i]->file_id) {
					for ($k=0; $k<count($files); $k++){
						if($files[$k]->id == $certificates[$i]->file_id){
							$insert->file_id = $files[$k]->new_file_id; break;
						}	
					}
				}
				$insert->crtf_text	= $certificates[$i]->certificate_text;
				$insert->crtf_font	= $certificates[$i]->certificate_font;
				$insert->text_x		= $certificates[$i]->text_x;
				$insert->text_y		= $certificates[$i]->text_y;
				$insert->text_size	= $certificates[$i]->text_size;
				$insert->crtf_type	= $certificates[$i]->crtf_type;
				if ($insert->crtf_type == 1) {
					$insert->published = 0;
				}
				$JLMS_DB->insertObject("#__lms_certificates", $insert, "id");
				$certificates[$i]->new_id = $JLMS_DB->insertid();
				$crtf_ids_exp[] = $certificates[$i]->id;
			}
			$i++;
		}

		// 27.03.2008 (DEN) - Additional certificate texts
		$i = 0;
		while ( $i < count ($certificate_texts) ){
			$do_add = false;
			if ($certificates[$i]->crtf_type == -2 && $certificates[$i]->parent_id) {
				if (in_array($certificates[$i]->parent_id, $crtf_ids_exp)) {
					$do_add = true;
				}
			}
			if ($do_add) {
				$insert = new stdClass();
				$insert->course_id = $course_id;
				$insert->file_id = 0;
				$insert->parent_id = 0;
				for ($k=0; $k<count($certificates); $k++){
					if($certificates[$k]->id == $certificate_texts[$i]->parent_id){
						$insert->parent_id = $certificates[$k]->new_id; break;
					}	
				}
				if ($insert->parent_id) {
					$insert->crtf_text	= $certificate_texts[$i]->add_certificate_text;
					$insert->crtf_font	= $certificate_texts[$i]->certificate_font;
					$insert->text_x		= $certificate_texts[$i]->text_x;
					$insert->text_y		= $certificate_texts[$i]->text_y;
					$insert->text_size	= $certificate_texts[$i]->text_size;
					$insert->crtf_type	= $certificate_texts[$i]->crtf_type;
					$JLMS_DB->insertObject("#__lms_certificates", $insert, "id");
					$certificate_texts[$i]->new_id = $JLMS_DB->insertid();
				}
			}
			$i++;
		}

		$quizzes = array();
		if (in_array(5, $imp_tools)) {
			// ****************************************************************************************************
			// Get Quizzes DATA and insert it into the DB
		
		//if ($lms_cfg_quiz_enabled) { // commented 05.03.2007 (to put exported quizzes into LP anywhere) (else we've got errors in LP)

			
			/* 28 April 2007 (DEN) - Question categories processing
			 *
			 */
			$element_qcat = &$root->getElementByPath('quizzes_quest_categories');

			$quest_cats = JLMS_parse_XML_elements($element_qcat->children(), array('c_id'), array('quest_category_name', 'quest_category_instr'));

			$i = 0;
			while ( $i < count ($quest_cats) ){
				$insert = new stdClass();
				$insert->course_id			= $course_id;
				$insert->c_category			= $quest_cats[$i]->quest_category_name;
				$insert->c_instruction		= $quest_cats[$i]->quest_category_instr;
				$insert->is_quiz_cat		= 0;
				$JLMS_DB->insertObject("#__lms_quiz_t_category", $insert, "c_id");
				$quest_cats[$i]->new_qcat_id = $JLMS_DB->insertid();
				$i++;
			}

			/* 27 March 2008 (DEN) - Quizzes images processing
			 * (variable $quiz_images defined above)
			 */

			$i = 0;
			while ( $i < count ($quiz_images) ){
				$insert = new stdClass();
				$insert->course_id			= $course_id;
				$insert->imgs_name			= $quiz_images[$i]->quiz_image_name;
				$insert->imgs_id			= 0;
				if ($quiz_images[$i]->file_id) {
					for ($k=0; $k<count($files); $k++){
						if($files[$k]->id == $quiz_images[$i]->file_id){
							$insert->imgs_id = $files[$k]->new_file_id; break;
						}	
					}
				}
				if ($insert->imgs_id) {
					$JLMS_DB->insertObject("#__lms_quiz_images", $insert, "c_id");
					$quiz_images[$i]->new_img_id = $JLMS_DB->insertid();
					$quiz_images[$i]->imgs_new_id = $insert->imgs_id;
					// Axtung!: vse tablicy nabora question options (like 't_choice', 't_matching') are stores imgs_id instead of id from #__lms_quiz_images 
				}

				$i++;
			}

			/* 28 April 2007 (DEN) - Questions Pool processing
			 *
			 */
			$element_pool = &$root->getElementByPath('quizzes_question_pool');

			$q_pool = JLMS_parse_XML_elements($element_pool->children(), array(), array(), true,
			array('pool_questions', 'question_feedbacks', 'choice_data', 'match_data', 'scale_data', 'blank_data', 'hotspot_data'),
			array(array('c_id', 'c_point', 'c_attempts', 'c_type', 'c_pool', 'c_qcat', 'ordering'), array('quest_id', 'choice_id'), array('c_question_id', 'c_right', 'ordering'), array('c_question_id', 'ordering'), array('c_question_id', 'c_type', 'ordering'), array('c_question_id', 'ordering'), array('c_question_id')),
			array(array('question_text', 'question_image', 'question_params', 'question_explanation'), array('fb_text'), array('choice_text'), array('match_text_left', 'match_text_right'), array('scale_field'), array('blank_text', 'default_answer'), array('hs_start_x', 'hs_start_y', 'hs_width', 'hs_height')));

			$i = 0;

			while ( $i < count($q_pool) ){
				//questions processing
				$j = 0;
				while ($j < count($q_pool[$i]->pool_questions)) {
					$quest = new stdClass();
					$quest->course_id 	= $course_id;
					$quest->c_quiz_id	= 0;//$new_quiz_id;
					$quest->c_point		= $q_pool[$i]->pool_questions[$j]->c_point;
					$quest->c_attempts	= $q_pool[$i]->pool_questions[$j]->c_attempts;
					$quest->c_question	= $q_pool[$i]->pool_questions[$j]->question_text;
					$quest->c_image		= $q_pool[$i]->pool_questions[$j]->question_image;
					$quest->params		= $q_pool[$i]->pool_questions[$j]->question_params;
					$quest->c_explanation = $q_pool[$i]->pool_questions[$j]->question_explanation; // added 27.03.2008 (DEN)
					$quest->c_type		= $q_pool[$i]->pool_questions[$j]->c_type;
					$quest->c_pool		= 0;//$q_pool[$i]->pool_questions[$j]->c_pool;
					$quest->c_qcat		= 0;
					if ($q_pool[$i]->pool_questions[$j]->c_qcat) {
						for ($ij=0; $ij<count($quest_cats); $ij++){
							if($quest_cats[$ij]->c_id == $q_pool[$i]->pool_questions[$j]->c_qcat){
								$quest->c_qcat = $quest_cats[$ij]->new_qcat_id; break;
							}	
						}
					}
					$quest->ordering	= $q_pool[$i]->pool_questions[$j]->ordering;

					if ($q_pool[$i]->pool_questions[$j]->question_image && !in_array($q_pool[$i]->pool_questions[$j]->question_image, $quiz_images2)) {
					// Changed 20.08.2007 by DEN - from:
					//if ($q_pool[$i]->pool_questions[$j]->question_image) {
						$quiz_images2[] = $q_pool[$i]->pool_questions[$j]->question_image;
					}

					$JLMS_DB->insertObject("#__lms_quiz_t_question", $quest, "c_id");
					$q_pool[$i]->pool_questions[$j]->new_id = $JLMS_DB->insertid();
					$j ++;
				}

				//feedbacks processing
				$j = 0;
				while ($j < count($q_pool[$i]->question_feedbacks)) {
					$q_fb = new stdClass();
					$q_fb->choice_id	= $q_pool[$i]->question_feedbacks[$j]->choice_id;
					$q_fb->fb_text		= $q_pool[$i]->question_feedbacks[$j]->fb_text;
					$q_fb->quest_id		= 0;
					for ($k=0; $k<count($q_pool[$i]->pool_questions); $k++){
						if ($q_pool[$i]->pool_questions[$k]->c_id == $q_pool[$i]->question_feedbacks[$j]->quest_id){
							$q_fb->quest_id = isset($q_pool[$i]->pool_questions[$k]->new_id)?intval($q_pool[$i]->pool_questions[$k]->new_id):0; break;
						}	
					}
					if ($q_fb->quest_id && $q_fb->fb_text && ($q_fb->choice_id == -1 || $q_fb->choice_id == 0 )) {
						$query = "INSERT INTO #__lms_quiz_t_question_fb (quest_id, choice_id, fb_text) VALUES ($q_fb->quest_id, $q_fb->choice_id, ".$JLMS_DB->Quote($q_fb->fb_text).")";
						$JLMS_DB->SetQuery($query);
						$JLMS_DB->query();
					}
					$j ++;
				}

				//choices processing
				$j = 0;
				while ($j < count($q_pool[$i]->choice_data)) {
					$q_choice = new stdClass();
					$q_choice->c_choice		= $q_pool[$i]->choice_data[$j]->choice_text;
					$q_choice->c_right		= $q_pool[$i]->choice_data[$j]->c_right;
					$q_choice->ordering		= $q_pool[$i]->choice_data[$j]->ordering;
					$q_choice->c_question_id = 0;
					for ($k=0; $k<count($q_pool[$i]->pool_questions); $k++){
						if ($q_pool[$i]->pool_questions[$k]->c_id == $q_pool[$i]->choice_data[$j]->c_question_id){
							$q_choice->c_question_id = isset($q_pool[$i]->pool_questions[$k]->new_id)?$q_pool[$i]->pool_questions[$k]->new_id:0;
							if ($q_pool[$i]->pool_questions[$k]->c_type == 12 || $q_pool[$i]->pool_questions[$k]->c_type == 13) {
								$q_choice->c_choice = intval($q_choice->c_choice);
								for ($kk=0; $kk<count($quiz_images); $kk++){
									if ($q_choice->c_choice == $quiz_images[$kk]->file_id){
										$q_choice->c_choice = isset($quiz_images[$kk]->imgs_new_id)?intval($quiz_images[$kk]->imgs_new_id):0; break;
									}	
								}
							}
							break;
						}	
					}
					if ($q_choice->c_question_id) {
						$JLMS_DB->insertObject("#__lms_quiz_t_choice", $q_choice, "c_id");
					}
					$j ++;
				}

				//matching processing
				$j = 0;
				while ($j < count($q_pool[$i]->match_data)) {
					$q_match = new stdClass();
					$q_match->c_left_text	= $q_pool[$i]->match_data[$j]->match_text_left;
					$q_match->c_right_text	= $q_pool[$i]->match_data[$j]->match_text_right;
					$q_match->ordering		= $q_pool[$i]->match_data[$j]->ordering;
					$q_match->c_question_id = 0;
					for ($k=0; $k<count($q_pool[$i]->pool_questions); $k++){
						if ($q_pool[$i]->pool_questions[$k]->c_id == $q_pool[$i]->match_data[$j]->c_question_id){
							$q_match->c_question_id = isset($q_pool[$i]->pool_questions[$k]->new_id)?$q_pool[$i]->pool_questions[$k]->new_id:0;
							if ($q_pool[$i]->pool_questions[$k]->c_type == 11) {
								$q_match->c_left_text = intval($q_match->c_left_text);
								$q_match->c_right_text = intval($q_match->c_right_text);
								$is_changed_match_images = 0;
								for ($kk=0; $kk<count($quiz_images); $kk++){
									if ($q_match->c_left_text == $quiz_images[$kk]->file_id){
										$q_match->c_left_text = isset($quiz_images[$kk]->imgs_new_id)?intval($quiz_images[$kk]->imgs_new_id):0;
										$is_changed_match_images ++;
										if ($is_changed_match_images == 2) { break; }
									}
									if ($q_match->c_right_text == $quiz_images[$kk]->file_id){
										$q_match->c_right_text = isset($quiz_images[$kk]->imgs_new_id)?intval($quiz_images[$kk]->imgs_new_id):0;
										$is_changed_match_images ++;
										if ($is_changed_match_images == 2) { break; }
									}	
								}
							}
							break;
						}	
					}
					if ($q_match->c_question_id) {
						$JLMS_DB->insertObject("#__lms_quiz_t_matching", $q_match, "c_id");
					}
					$j ++;
				}

				//likert scale processing (27.03.2008 - DEN)
				$j = 0;
				while ($j < count($q_pool[$i]->scale_data)) {
					$q_scale = new stdClass();
					$q_scale->c_field		= $q_pool[$i]->scale_data[$j]->scale_field;
					$q_scale->c_type		= $q_pool[$i]->scale_data[$j]->c_type;
					$q_scale->ordering		= $q_pool[$i]->scale_data[$j]->ordering;
					$q_scale->c_question_id = 0;
					for ($k=0; $k<count($q_pool[$i]->pool_questions); $k++){
						if ($q_pool[$i]->pool_questions[$k]->c_id == $q_pool[$i]->scale_data[$j]->c_question_id){
							$q_scale->c_question_id = isset($q_pool[$i]->pool_questions[$k]->new_id)?$q_pool[$i]->pool_questions[$k]->new_id:0; break;
						}	
					}
					if ($q_scale->c_question_id) {
						$JLMS_DB->insertObject("#__lms_quiz_t_scale", $q_scale, "c_id");
					}
					$j ++;
				}

				//hotspot processing
				$j = 0;
				while ($j < count($q_pool[$i]->hotspot_data)) {
					$q_hotspot = new stdClass();
					$q_hotspot->c_start_x	= $q_pool[$i]->hotspot_data[$j]->hs_start_x;
					$q_hotspot->c_start_y	= $q_pool[$i]->hotspot_data[$j]->hs_start_y;
					$q_hotspot->c_width		= $q_pool[$i]->hotspot_data[$j]->hs_width;
					$q_hotspot->c_height	= $q_pool[$i]->hotspot_data[$j]->hs_height;
					$q_hotspot->c_question_id = 0;
					for ($k=0; $k<count($q_pool[$i]->pool_questions); $k++){
						if ($q_pool[$i]->pool_questions[$k]->c_id == $q_pool[$i]->hotspot_data[$j]->c_question_id){
							$q_hotspot->c_question_id = isset($q_pool[$i]->pool_questions[$k]->new_id)?$q_pool[$i]->pool_questions[$k]->new_id:0; break;
						}	
					}
					if ($q_hotspot->c_question_id) {
						$JLMS_DB->insertObject("#__lms_quiz_t_hotspot", $q_hotspot, "c_id");
					}
					$j ++;
				}

				//blank quests processing
				$j = 0;
				$blank_inserted = array();
				while ($j < count($q_pool[$i]->blank_data)) {
					$q_blank = new stdClass();
					$q_blank->c_question_id = 0;
					$q_blank->c_default = $q_pool[$i]->blank_data[$j]->default_answer;
					for ($k=0; $k<count($q_pool[$i]->pool_questions); $k++){
						if ($q_pool[$i]->pool_questions[$k]->c_id == $q_pool[$i]->blank_data[$j]->c_question_id){
							$q_blank->c_question_id = isset($q_pool[$i]->pool_questions[$k]->new_id)?$q_pool[$i]->pool_questions[$k]->new_id:0; break;
						}	
					}
					if ($q_blank->c_question_id) {
						$proceed_insert = true;
						foreach ($blank_inserted as $bains) {
							if ($bains->quest_id == $q_blank->c_question_id) {
								$proceed_insert = false;
								$new_blank_id = $bains->blank_id;
								break;
							}
						}
						if ($proceed_insert) {
							$JLMS_DB->insertObject("#__lms_quiz_t_blank", $q_blank, "c_id");
							$new_blank_id = $JLMS_DB->insertid();
							$blankins = new stdClass();
							$blankins->quest_id = $q_blank->c_question_id;
							$blankins->blank_id = $new_blank_id;
							$blank_inserted[] = $blankins;
						}
						$q_blank_text = new stdClass();
						$q_blank_text->c_blank_id	= $new_blank_id;
						$q_blank_text->c_text		= $q_pool[$i]->blank_data[$j]->blank_text;
						$q_blank_text->ordering		= $q_pool[$i]->blank_data[$j]->ordering;
						if ($q_blank_text->c_blank_id) {
							$JLMS_DB->insertObject("#__lms_quiz_t_text", $q_blank_text, "c_id");
						}
					}
					$j ++;
				}
				$i ++;
			}


			$element = &$root->getElementByPath('quizzes');

			$quizzes = JLMS_parse_XML_elements($element->children(), array('c_id', 'published'),
			array('quiz_title', 'quiz_description', 'quiz_category', 'quiz_full_score', 'quiz_time_limit', 'quiz_min_after', 'quiz_passing_score', 'quiz_right_message', 'quiz_wrong_message', 'quiz_pass_message', 'quiz_unpass_message', 'quiz_review', 'quiz_email', 'quiz_print', 'quiz_certif', 'quiz_skin', 'quiz_random', 'quiz_guest', 'quiz_slide', 'quiz_language', 'quiz_certificate', 'quiz_gradebook', 'quiz_params','is_time_related', 'show_period' ),
			true, array('quiz_pool_assoc', 'quiz_questions', 'question_feedbacks', 'choice_data', 'match_data', 'scale_data', 'blank_data', 'hotspot_data'),
			array(array('qcat_id', 'items_number'),array('c_id', 'c_point', 'c_attempts', 'c_type', 'c_pool', 'c_qcat', 'ordering'), array('quest_id', 'choice_id'), array('c_question_id', 'c_right', 'ordering'), array('c_question_id', 'ordering'), array('c_question_id', 'c_type', 'ordering'), array('c_question_id', 'ordering'), array('c_question_id')),
			array(array(), array('question_text', 'question_image', 'question_params', 'question_explanation'), array('fb_text'), array('choice_text'), array('match_text_left', 'match_text_right'), array('scale_field'), array('blank_text', 'default_answer'), array('hs_start_x', 'hs_start_y', 'hs_width', 'hs_height')));
			
			$i = 0;

			while ( $i < count($quizzes) ){
				$insert = new stdClass();
				$insert->course_id				= $course_id;
				$insert->c_category_id			= 0;
				$quiz_cat_name = $quizzes[$i]->quiz_category;
				$query = "SELECT c_id FROM #__lms_quiz_t_category WHERE c_category = '".$quiz_cat_name."' AND is_quiz_cat = 1";
				$JLMS_DB->SetQuery( $query );
				$quiz_cat_id = $JLMS_DB->LoadResult();
				if ($quiz_cat_id) {
					$insert->c_category_id		= $quiz_cat_id;
				} elseif ($quiz_cat_name) {
					$query = "INSERT INTO #__lms_quiz_t_category (course_id, c_category, c_instruction, is_quiz_cat) VALUES ($course_id, ".$JLMS_DB->Quote($quiz_cat_name).", '', 1)";
					$JLMS_DB->SetQuery( $query );
					$JLMS_DB->query();
					$insert->c_category_id = $JLMS_DB->insertid();
				}
				$insert->c_user_id				= $my->id;
				$insert->c_full_score			= $quizzes[$i]->quiz_full_score;
				$insert->c_title				= $quizzes[$i]->quiz_title;
				$insert->c_description			= $quizzes[$i]->quiz_description;
				$insert->c_time_limit			= $quizzes[$i]->quiz_time_limit;
				$insert->c_min_after			= $quizzes[$i]->quiz_min_after;
				$insert->c_passing_score		= $quizzes[$i]->quiz_passing_score;
				$insert->c_created_time			= date('Y-m-d');
				$insert->published				= $quizzes[$i]->published;//!!!
				$insert->c_right_message		= $quizzes[$i]->quiz_right_message;
				$insert->c_wrong_message		= $quizzes[$i]->quiz_wrong_message;
				$insert->c_pass_message			= $quizzes[$i]->quiz_pass_message;
				$insert->c_unpass_message		= $quizzes[$i]->quiz_unpass_message;
				$insert->c_enable_review		= $quizzes[$i]->quiz_review;
				$insert->c_email_to				= $quizzes[$i]->quiz_email;
				$insert->c_enable_print			= $quizzes[$i]->quiz_print;
				$insert->c_enable_sertif		= $quizzes[$i]->quiz_certif;
				$insert->c_skin					= $quizzes[$i]->quiz_skin;
				$insert->c_random				= $quizzes[$i]->quiz_random;
				$insert->c_guest				= $quizzes[$i]->quiz_guest;
				$insert->c_skin					= $quizzes[$i]->quiz_skin;
				$insert->c_slide				= $quizzes[$i]->quiz_slide;
				$insert->c_language				= $quizzes[$i]->quiz_language;
				$insert->params					= $quizzes[$i]->quiz_params;
				$insert->is_time_related		= $quizzes[$i]->is_time_related;
				$insert->show_period			= $quizzes[$i]->show_period;
				
				$insert->c_certificate = 0;
				if ($quizzes[$i]->quiz_certificate) {
					for ($r=0; $r<count($certificates); $r++){
						if($certificates[$r]->id == $quizzes[$i]->quiz_certificate){
							$insert->c_certificate = $certificates[$r]->new_id; break;
						}	
					}
				}
				$insert->c_gradebook			= $quizzes[$i]->quiz_gradebook;

				$JLMS_DB->insertObject("#__lms_quiz_t_quiz", $insert, "c_id");
				$new_quiz_id = $JLMS_DB->insertid();
				$quizzes[$i]->new_quiz_id = $new_quiz_id;	
				
				
				if (!empty($quizzes[$i]->quiz_pool_assoc)) {
					$j = 0;
					while ($j < count($quizzes[$i]->quiz_pool_assoc)) {
						$ins_qp = new stdClass();
						$ins_qp->quiz_id = $new_quiz_id;
						$ins_qp->qcat_id = 0;//$quizzes[$i]->quiz_pool_assoc[$j]->qcat_id;
						if ($quizzes[$i]->quiz_pool_assoc[$j]->qcat_id) {
							for ($ij=0; $ij<count($quest_cats); $ij++){
								if($quest_cats[$ij]->c_id == $quizzes[$i]->quiz_pool_assoc[$j]->qcat_id){
									$ins_qp->qcat_id = $quest_cats[$ij]->new_qcat_id; break;
								}	
							}
						}
						$ins_qp->items_number = $quizzes[$i]->quiz_pool_assoc[$j]->items_number;
						$query = "INSERT INTO #__lms_quiz_t_quiz_pool (quiz_id, qcat_id, items_number) VALUES ($ins_qp->quiz_id, $ins_qp->qcat_id, $ins_qp->items_number)";
						$JLMS_DB->SetQuery( $query );
						$JLMS_DB->query();
						$j++;
					}
				}

				//questions processing
				$j = 0;
				while ($j < count($quizzes[$i]->quiz_questions)) {
					$quest = new stdClass();
					$quest->course_id 	= $course_id;
					$quest->c_quiz_id	= $new_quiz_id;
					$quest->c_point		= $quizzes[$i]->quiz_questions[$j]->c_point;
					$quest->c_attempts	= $quizzes[$i]->quiz_questions[$j]->c_attempts;
					$quest->c_question	= $quizzes[$i]->quiz_questions[$j]->question_text;
					$quest->c_image		= $quizzes[$i]->quiz_questions[$j]->question_image;
					$quest->c_type		= $quizzes[$i]->quiz_questions[$j]->c_type;
					$quest->params		= $quizzes[$i]->quiz_questions[$j]->question_params;
					$quest->c_explanation = $quizzes[$i]->quiz_questions[$j]->question_explanation; // added 27.03.2008 (DEN)
					$quest->c_pool		= 0;//$q_pool[$i]->pool_questions[$j]->c_pool;
					$quest->c_qcat		= 0;
					if ($quizzes[$i]->quiz_questions[$j]->c_qcat) {
						for ($ij=0; $ij<count($quest_cats); $ij++){
							if($quest_cats[$ij]->c_id == $quizzes[$i]->quiz_questions[$j]->c_qcat){
								$quest->c_qcat = $quest_cats[$ij]->new_qcat_id; break;
							}	
						}
					}
					if ( $quizzes[$i]->quiz_questions[$j]->c_pool && ($quest->c_type == 20) ) {
						if (!empty($q_pool[0]->pool_questions)) {
							for ($ij=0; $ij<count($q_pool[0]->pool_questions); $ij++){
								if($q_pool[0]->pool_questions[$ij]->c_id == $quizzes[$i]->quiz_questions[$j]->c_pool){
									$quest->c_pool = $q_pool[0]->pool_questions[$ij]->new_id; break;
								}
							}
						}
					}
					$quest->ordering	= $quizzes[$i]->quiz_questions[$j]->ordering;

					if ($quizzes[$i]->quiz_questions[$j]->question_image && !in_array($quizzes[$i]->quiz_questions[$j]->question_image, $quiz_images2)) {
					// Changed 20.08.2007 by DEN - from:
					//if ($q_pool[$i]->pool_questions[$j]->question_image) {
						$quiz_images2[] = $quizzes[$i]->quiz_questions[$j]->question_image;
					}

					$JLMS_DB->insertObject("#__lms_quiz_t_question", $quest, "c_id");
					$quizzes[$i]->quiz_questions[$j]->new_id = $JLMS_DB->insertid();
					$j ++;
				}

				//feedbacks processing
				$j = 0;
				while ($j < count($quizzes[$i]->question_feedbacks)) {
					$q_fb = new stdClass();
					$q_fb->choice_id	= $quizzes[$i]->question_feedbacks[$j]->choice_id;
					$q_fb->fb_text		= $quizzes[$i]->question_feedbacks[$j]->fb_text;
					$q_fb->quest_id		= 0;
					for ($k=0; $k<count($quizzes[$i]->quiz_questions); $k++){
						if ($quizzes[$i]->quiz_questions[$k]->c_id == $quizzes[$i]->question_feedbacks[$j]->quest_id){
							$q_fb->quest_id = isset($quizzes[$i]->quiz_questions[$k]->new_id)?intval($quizzes[$i]->quiz_questions[$k]->new_id):0; break;
						}	
					}
					if ($q_fb->quest_id && $q_fb->fb_text && ($q_fb->choice_id == -1 || $q_fb->choice_id == 0 )) {
						$query = "INSERT INTO #__lms_quiz_t_question_fb (quest_id, choice_id, fb_text) VALUES ($q_fb->quest_id, $q_fb->choice_id, ".$JLMS_DB->Quote($q_fb->fb_text).")";
						$JLMS_DB->SetQuery($query);
						$JLMS_DB->query();
					}
					$j ++;
				}

				//choices processing
				$j = 0;
				while ($j < count($quizzes[$i]->choice_data)) {
					$q_choice = new stdClass();
					$q_choice->c_choice		= $quizzes[$i]->choice_data[$j]->choice_text;
					$q_choice->c_right		= $quizzes[$i]->choice_data[$j]->c_right;
					$q_choice->ordering		= $quizzes[$i]->choice_data[$j]->ordering;
					$q_choice->c_question_id = 0;
					for ($k=0; $k<count($quizzes[$i]->quiz_questions); $k++){
						if ($quizzes[$i]->quiz_questions[$k]->c_id == $quizzes[$i]->choice_data[$j]->c_question_id){
							$q_choice->c_question_id = isset($quizzes[$i]->quiz_questions[$k]->new_id)?$quizzes[$i]->quiz_questions[$k]->new_id:0;
							
							if ($quizzes[$i]->quiz_questions[$k]->c_type == 12 || $quizzes[$i]->quiz_questions[$k]->c_type == 13) {
								$q_choice->c_choice = intval($q_choice->c_choice);
								for ($kk=0; $kk<count($quiz_images); $kk++){
									if ($q_choice->c_choice == $quiz_images[$kk]->file_id){
										$q_choice->c_choice = isset($quiz_images[$kk]->imgs_new_id)?intval($quiz_images[$kk]->imgs_new_id):0; 
										break;
									}	
								}
							}							
							break;
						}	
					}
					if ($q_choice->c_question_id) {
						$JLMS_DB->insertObject("#__lms_quiz_t_choice", $q_choice, "c_id");
					}
					$j ++;
				}
				
				//matching processing
				$j = 0;
				while ($j < count($quizzes[$i]->match_data)) {
					$q_match = new stdClass();
					$q_match->c_left_text	= $quizzes[$i]->match_data[$j]->match_text_left;
					$q_match->c_right_text	= $quizzes[$i]->match_data[$j]->match_text_right;
					$q_match->ordering		= $quizzes[$i]->match_data[$j]->ordering;
					$q_match->c_question_id = 0;
					for ($k=0; $k<count($quizzes[$i]->quiz_questions); $k++){
						if ($quizzes[$i]->quiz_questions[$k]->c_id == $quizzes[$i]->match_data[$j]->c_question_id){
							$q_match->c_question_id = isset($quizzes[$i]->quiz_questions[$k]->new_id)?$quizzes[$i]->quiz_questions[$k]->new_id:0; break;
							if ($quizzes[$i]->quiz_questions[$k]->c_type == 11) {
								$q_match->c_left_text = intval($q_match->c_left_text);
								$q_match->c_right_text = intval($q_match->c_right_text);
								$is_changed_match_images = 0;
								for ($kk=0; $kk<count($quiz_images); $kk++){
									if ($q_match->c_left_text == $quiz_images[$kk]->file_id){
										$q_match->c_left_text = isset($quiz_images[$kk]->imgs_new_id)?intval($quiz_images[$kk]->imgs_new_id):0;
										$is_changed_match_images ++;
										if ($is_changed_match_images == 2) { break; }
									}
									if ($q_match->c_right_text == $quiz_images[$kk]->file_id){
										$q_match->c_right_text = isset($quiz_images[$kk]->imgs_new_id)?intval($quiz_images[$kk]->imgs_new_id):0;
										$is_changed_match_images ++;
										if ($is_changed_match_images == 2) { break; }
									}	
								}
							}
						}	
					}
					if ($q_match->c_question_id) {
						$JLMS_DB->insertObject("#__lms_quiz_t_matching", $q_match, "c_id");
					}
					$j ++;
				}

				//likert scale processing (27.03.2008 - DEN)
				$j = 0;
				while ($j < count($quizzes[$i]->scale_data)) {
					$q_scale = new stdClass();
					$q_scale->c_field		= $quizzes[$i]->scale_data[$j]->scale_field;
					$q_scale->c_type		= $quizzes[$i]->scale_data[$j]->c_type;
					$q_scale->ordering		= $quizzes[$i]->scale_data[$j]->ordering;
					$q_scale->c_question_id = 0;
					for ($k=0; $k<count($quizzes[$i]->quiz_questions); $k++){
						if ($quizzes[$i]->quiz_questions[$k]->c_id == $quizzes[$i]->scale_data[$j]->c_question_id){
							$q_scale->c_question_id = isset($quizzes[$i]->quiz_questions[$k]->new_id)?$quizzes[$i]->quiz_questions[$k]->new_id:0; break;
						}	
					}
					if ($q_scale->c_question_id) {
						$JLMS_DB->insertObject("#__lms_quiz_t_scale", $q_scale, "c_id");
					}
					$j ++;
				}

				//hotspot processing
				$j = 0;
				while ($j < count($quizzes[$i]->hotspot_data)) {
					$q_hotspot = new stdClass();
					$q_hotspot->c_start_x	= $quizzes[$i]->hotspot_data[$j]->hs_start_x;
					$q_hotspot->c_start_y	= $quizzes[$i]->hotspot_data[$j]->hs_start_y;
					$q_hotspot->c_width		= $quizzes[$i]->hotspot_data[$j]->hs_width;
					$q_hotspot->c_height	= $quizzes[$i]->hotspot_data[$j]->hs_height;
					$q_hotspot->c_question_id = 0;
					for ($k=0; $k<count($quizzes[$i]->quiz_questions); $k++){
						if ($quizzes[$i]->quiz_questions[$k]->c_id == $quizzes[$i]->hotspot_data[$j]->c_question_id){
							$q_hotspot->c_question_id = isset($quizzes[$i]->quiz_questions[$k]->new_id)?$quizzes[$i]->quiz_questions[$k]->new_id:0; break;
						}	
					}
					if ($q_hotspot->c_question_id) {
						$JLMS_DB->insertObject("#__lms_quiz_t_hotspot", $q_hotspot, "c_id");
					}
					$j ++;
				}

				//blank quests processing
				$j = 0;
				$blank_inserted = array();
				while ($j < count($quizzes[$i]->blank_data)) {
					$q_blank = new stdClass();
					$q_blank->c_question_id = 0;
					$q_blank->c_default = $quizzes[$i]->blank_data[$j]->default_answer;
					for ($k=0; $k<count($quizzes[$i]->quiz_questions); $k++){
						if ($quizzes[$i]->quiz_questions[$k]->c_id == $quizzes[$i]->blank_data[$j]->c_question_id){
							$q_blank->c_question_id = isset($quizzes[$i]->quiz_questions[$k]->new_id)?$quizzes[$i]->quiz_questions[$k]->new_id:0; break;
						}	
					}
					if ($q_blank->c_question_id) {
						$proceed_insert = true;
						foreach ($blank_inserted as $bains) {
							if ($bains->quest_id == $q_blank->c_question_id) {
								$proceed_insert = false;
								$new_blank_id = $bains->blank_id;
								break;
							}
						}
						if ($proceed_insert) {
							$JLMS_DB->insertObject("#__lms_quiz_t_blank", $q_blank, "c_id");
							$new_blank_id = $JLMS_DB->insertid();
							$blankins = new stdClass();
							$blankins->quest_id = $q_blank->c_question_id;
							$blankins->blank_id = $new_blank_id;
							$blank_inserted[] = $blankins;
						}
						$q_blank_text = new stdClass();
						$q_blank_text->c_blank_id	= $new_blank_id;
						$q_blank_text->c_text		= $quizzes[$i]->blank_data[$j]->blank_text;
						$q_blank_text->ordering		= $quizzes[$i]->blank_data[$j]->ordering;
						if ($q_blank_text->c_blank_id) {
							$JLMS_DB->insertObject("#__lms_quiz_t_text", $q_blank_text, "c_id");
						}
					}
					$j ++;
				}
				$i ++;
			}

			// ****************************************************************************************************
			// Copy quiz images
			if (count($quiz_images2)) {
				$fromDir = $extract_dir."quiz_images/";
				$toDir   = $JLMS_CONFIG->getCfg('absolute_path')."/images/joomlaquiz/images/";
				$i = 0;
				while( $i < count($quiz_images2) ){
					if (file_exists($fromDir.$quiz_images2[$i])) {
						@rename ($fromDir.$quiz_images2[$i], $toDir.$quiz_images2[$i]);
					}
					$i ++;
				}

			}
		//} // end if ($lms_cfg_quiz_enabled)
		}

		$l_paths = array();
		$lpath_prerequisites = array();
		if (in_array(2, $imp_tools)) {
			// ****************************************************************************************************
			// Get LearningPaths DATA and insert it into the DB
		
			$element = &$root->getElementByPath('learn_paths');
			$l_paths = JLMS_parse_XML_elements($element->children(), array('id', 'item_id', 'ordering', 'published', 'is_time_related', 'show_period'), array('lp_name', 'lp_shortdescription', 'lp_description', 'lp_params'), true, array('prerequisites', 'steps','conds'), array(array('lpath_id', 'req_id', 'time_minutes'), array('id', 'item_id', 'lpath_id', 'step_type', 'parent_id', 'ordering'), array('lpath_id', 'step_id', 'ref_step', 'cond_type', 'cond_value')), array(array(), array('step_name', 'step_shortdescription', 'step_description'),array()));
			$i = 0;

			$process_steps_on_exit = array();

			while ( $i < count($l_paths) ){
				$insert = new stdClass();
				$insert->course_id = $course_id;
				$insert->owner_id = $my->id;
				$insert->lpath_name = $l_paths[$i]->lp_name;
				if ($l_paths[$i]->item_id) {
					$n = 0;
					$scorm_found = false;
					while ($n < count($scorms)) {
						if ($scorms[$n]->id == $l_paths[$i]->item_id){
							$insert->item_id = $scorms[$n]->new_sco_id;
							$scorm_found = true;
							break;
						}
						$n ++;	
					}
					if (!$scorm_found) {
						$i++;
						continue;
					}
				} else {
					$insert->item_id = 0;
				}
				$insert->lpath_shortdescription	= $l_paths[$i]->lp_shortdescription;
				$insert->lp_params				= $l_paths[$i]->lp_params;
				$insert->lpath_description		= $l_paths[$i]->lp_description;
				$insert->ordering				= $l_paths[$i]->ordering;
				$insert->published				= $l_paths[$i]->published;
				$insert->lp_type				= 0; // for compatibility with SCORM import/export (if lp_type == 0, then new instance in 'lms_n_scorm' will be added automatically)
				$insert->is_time_related		= $l_paths[$i]->is_time_related;
				$insert->show_period			= $l_paths[$i]->show_period;
				
				$JLMS_DB->insertObject("#__lms_learn_paths", $insert, "id");
				$l_paths[$i]->new_step_id		= $JLMS_DB->insertid();

				// 18 August 2007 - DEN - prerequisites import
				$j = 0;
				while ($j < count($l_paths[$i]->prerequisites)) {
					$new_prereq = new stdClass();
					$new_prereq->old_lpath_id = $l_paths[$i]->prerequisites[$j]->lpath_id;
					$new_prereq->old_req_id = $l_paths[$i]->prerequisites[$j]->req_id;
					$new_prereq->lpath_id = 0;
					$new_prereq->req_id = 0;
					$new_prereq->time_minutes = 0;
					$lpath_prerequisites[] = $new_prereq;
					$j ++;
				}

				if ($l_paths[$i]->item_id) {

				} else {
					$j = 0;
					while ($j < count($l_paths[$i]->steps)) {
						$step = new stdClass();
						$step->course_id		= $course_id;
						$step->lpath_id			= $l_paths[$i]->new_step_id;
						$step->step_type		= $l_paths[$i]->steps[$j]->step_type;

						$do_process_on_exit = false;
						$proc_exit_item = 0;

						if ($step->step_type == 2){ //document
							$is_doc_found = false;
							for ($k=0; $k<count($documents); $k++){
								if ($documents[$k]->id == $l_paths[$i]->steps[$j]->item_id){
									$step->item_id = $documents[$k]->new_doc_id;
									$is_doc_found = true;
									break;
								}	
							}
							if (!$is_doc_found) {
								$step->step_type = 4;
							}
						}
						elseif ($step->step_type == 3){ //link
							$is_link_found = false;
							for ($k=0; $k<count($links); $k++){
								if ($links[$k]->id == $l_paths[$i]->steps[$j]->item_id){
									$step->item_id = $links[$k]->new_link_id;
									$is_link_found = true;
									break;
								}	
							}
							if (!$is_link_found) {
								$step->step_type = 4;
							}
						}
						elseif  ($step->step_type == 5){ //quiz
							$is_quiz_found = false;
							for ($k=0; $k<count($quizzes); $k++){
								if ($quizzes[$k]->c_id == $l_paths[$i]->steps[$j]->item_id){
									$step->item_id = $quizzes[$k]->new_quiz_id;
									$is_quiz_found = true;
									break;
								}	
							}
							if (!$is_quiz_found) {
								$step->step_type = 4;
							}
						}
						elseif ($step->step_type == 6) { //scorm
							// Axtung - we should make all scorm steps - as 4 (content) and after processing all LPaths -> upgrade them to type 6 (scorm) and link to scorm LPath
							// (because) at this step not all Lpaths are processed !!!
							$step->step_type = 4;
							$do_process_on_exit = true;
							$proc_exit_item = $l_paths[$i]->steps[$j]->item_id;
						}
						else { //other
							$step->item_id = $l_paths[$i]->steps[$j]->item_id;
						}
						$parent = $l_paths[$i]->steps[$j]->parent_id;
						// search processed steps for parent_id
						if ($parent){
							$a = 0;
							while ($a < $j){
								if ($l_paths[$i]->steps[$a]->id == $parent){
									$parent = $l_paths[$i]->steps[$a]->new_id; break;
								}
								$a ++;
							}
						}
						$step->parent_id				= $parent;
						$step->step_name 	 			= $l_paths[$i]->steps[$j]->step_name;
						$step->step_shortdescription 	= $l_paths[$i]->steps[$j]->step_shortdescription;
						$step->step_description 		= $l_paths[$i]->steps[$j]->step_description;
						$step->ordering 		 		= $l_paths[$i]->steps[$j]->ordering;
						$JLMS_DB->insertObject("#__lms_learn_path_steps", $step, "id");
						$l_paths[$i]->steps[$j]->new_id = $JLMS_DB->insertid();

						if ($do_process_on_exit) {
							$pr_step = new stdClass();
							$pr_step->step_id = $l_paths[$i]->steps[$j]->new_id;
							$pr_step->item_id = $proc_exit_item;
							$pr_step->new_item_id = 0;
							$process_steps_on_exit[] = $pr_step;
						}

						$j ++;
					}
					$j = 0;
					while ( $j < count( $l_paths[$i]->conds ) ){
						$cond = new stdClass();
						$cond->course_id		= $course_id;
						$cond->lpath_id			= $l_paths[$i]->new_step_id;
						$cond->cond_type		= $l_paths[$i]->conds[$j]->cond_type;
						$cond->cond_value		= $l_paths[$i]->conds[$j]->cond_value;
						$st1 = $l_paths[$i]->conds[$j]->step_id;
						if ($st1){
							$a = 0;
							while ($a < count($l_paths[$i]->steps)){
								if ($l_paths[$i]->steps[$a]->id == $st1){
									$st1 = $l_paths[$i]->steps[$a]->new_id; break;
								}
								$a ++;
							}
						}
						$cond->step_id = $st1;
						$st2 = $l_paths[$i]->conds[$j]->ref_step;
						if ($st2){
							$a = 0;
							while ($a < count($l_paths[$i]->steps)){
								if ($l_paths[$i]->steps[$a]->id == $st2){
									$st2 = $l_paths[$i]->steps[$a]->new_id; break;
								}
								$a ++;
							}
						}
						$cond->ref_step = $st2;
						$JLMS_DB->insertObject("#__lms_learn_path_conds", $cond, "id");
						$j ++;
					}
				}
				$i ++;
			}

			// 18 August 2007 - DEN - import scorm steps
			if (!empty($process_steps_on_exit)) {
				$a = 0;
				while ($a < count($process_steps_on_exit)) {
					foreach ($l_paths as $lp) {
						if ($lp->id == $process_steps_on_exit[$a]->item_id) {
							$process_steps_on_exit[$a]->new_item_id = $lp->new_step_id; // xm.. why 'step' ?? (bad name)
						}
					}
					$a ++;
				}
				foreach ($process_steps_on_exit as $lp_pse) {
					if ($lp_pse->new_item_id && $lp_pse->step_id) {
						$query = "UPDATE #__lms_learn_path_steps SET step_type = 6, item_id = ".intval($lp_pse->new_item_id)." WHERE id = ".intval($lp_pse->step_id);
						$JLMS_DB->SetQuery($query);
						$JLMS_DB->query();
					}
				}
			}

			// 18 August 2007 - DEN - prerequisites import
			if (!empty($lpath_prerequisites)) {
				$a = 0;
				while ($a < count($lpath_prerequisites)) {
					foreach ($l_paths as $lp) {
						if ($lp->id == $lpath_prerequisites[$a]->old_lpath_id) {
							$lpath_prerequisites[$a]->lpath_id = $lp->new_step_id; // xm.. why 'step' ?? (bad name)
						}
						if ($lp->id == $lpath_prerequisites[$a]->old_req_id) {
							$lpath_prerequisites[$a]->req_id = $lp->new_step_id; // xm.. why 'step' ?? (bad name)
						}

					}
					$a ++;
				}
				foreach ($lpath_prerequisites as $lp_pre) {
					if ($lp_pre->lpath_id && $lp_pre->req_id && ($lp_pre->lpath_id != $lp_pre->req_id) ) {
						$query = "INSERT INTO #__lms_learn_path_prerequisites (lpath_id, req_id, time_minutes) VALUES (".intval($lp_pre->lpath_id).", ".intval($lp_pre->req_id).", ".intval($lp_pre->time_minutes).")";
						$JLMS_DB->SetQuery($query);
						$JLMS_DB->query();
					}
				}
			}

			if ($course_redirect_lp) {
				$lp_id = 0;
				foreach ($l_paths as $lp) {
					if ($lp->id == $course_redirect_lp) {
						$lp_id = $lp->new_step_id; // xm.. why 'step' ?? (bad name)
						break;
					}
				}
				if ($lp_id) {
					$params->set('learn_path', $lp_id);
					$params_ar = $params->toArray();
					if (is_array($params_ar)) {
						foreach ( $params_ar as $k=>$v) {
							$txt[] = "$k=$v";
						}
						$new_params = implode( "\n", $txt );
						if ($new_params) {
							$query = "UPDATE #__lms_courses SET ".$JLMS_DB->NameQuote('params')." = ".$JLMS_DB->Quote($new_params)." WHERE id = $course_id";
							$JLMS_DB->SetQuery($query);
							$JLMS_DB->query();
						}
					}
				}
			}
		}


		$course_topics = array();
		// ****************************************************************************************************
		// Get TOPICS Items DATA and insert it into the 'lms_topics', 'lms_topic_items' tables - 27.03.2008 (DEN)

		$element = &$root->getElementByPath('course_topics');
		$course_topics = JLMS_parse_XML_elements($element->children(), array('topic_id', 'ordering', 'published', 'publish_start', 'start_date', 'publish_end', 'end_date', 'is_time_related', 'show_period'), array('topic_name', 'topic_description'));
		$i = 0;
		while ( $i < count ($course_topics) ){
			$insert = new stdClass();
			$insert->course_id			= $course_id;
			$insert->ordering			= $course_topics[$i]->ordering;
			$insert->name				= $course_topics[$i]->topic_name;
			$insert->description		= $course_topics[$i]->topic_description;
			$insert->published			= $course_topics[$i]->published;
			$insert->start_date			= $course_topics[$i]->start_date;
			$insert->publish_start		= $course_topics[$i]->publish_start;
			$insert->end_date			= $course_topics[$i]->end_date;
			$insert->publish_end		= $course_topics[$i]->publish_end;
			$insert->is_time_related	= $course_topics[$i]->is_time_related;
			$insert->show_period		= $course_topics[$i]->show_period;
			
			$JLMS_DB->insertObject("#__lms_topics", $insert, "id");
			$course_topics[$i]->new_topic_id = $JLMS_DB->insertid();
			$i++;
		}

		$element = &$root->getElementByPath('course_topic_items');
		$course_topic_items = JLMS_parse_XML_elements($element->children(), array('topic_id', 'item_id', 'item_type', 'ordering', 'is_shown'), array());
		$i = 0;
		while ( $i < count ($course_topic_items) ){
			$insert = new stdClass();
			$insert->course_id			= $course_id;
			$insert->topic_id			= 0;
			foreach ($course_topics as $ct) {
				if ($course_topic_items[$i]->topic_id == $ct->topic_id) {
					$insert->topic_id	= $ct->new_topic_id;
				}
			}
			if ($insert->topic_id) {
				$insert->ordering		= $course_topic_items[$i]->ordering;
				$insert->item_type		= $course_topic_items[$i]->item_type;
				$insert->show			= ($course_topic_items[$i]->is_shown ? 1 : 0);
				$insert->item_id		= 0;
				$do_add_topic_item = false;
				if ($insert->item_type == 2) { // documents
					if (in_array(1, $imp_tools)) {
						for ($k=0; $k<count($documents); $k++){
							if ($documents[$k]->id == $course_topic_items[$i]->item_id){
								$insert->item_id = $documents[$k]->new_doc_id;
								$do_add_topic_item = true;
								break;
							}	
						}
					}
				} elseif ($insert->item_type == 3) { // links
					if (in_array(4, $imp_tools)) {
						for ($k=0; $k<count($links); $k++){
							if ($links[$k]->id == $course_topic_items[$i]->item_id){
								$insert->item_id = $links[$k]->new_link_id;
								$do_add_topic_item = true;
								break;
							}	
						}
					}
				} elseif ($insert->item_type == 5) { // quizzes
					if (in_array(5, $imp_tools)) {
						for ($k=0; $k<count($quizzes); $k++){
							if ($quizzes[$k]->c_id == $course_topic_items[$i]->item_id){
								$insert->item_id = $quizzes[$k]->new_quiz_id;
								$do_add_topic_item = true;
								break;
							}	
						}
					}
				} elseif ($insert->item_type == 7) { // learning paths
					if (in_array(2, $imp_tools)) {
						for ($k=0; $k<count($l_paths); $k++){
							if ($l_paths[$k]->id == $course_topic_items[$i]->item_id){
								$insert->item_id = $l_paths[$k]->new_step_id;
								$do_add_topic_item = true;
								break;
							}	
						}
					}
				}
				if ($do_add_topic_item && $insert->item_id) {
					$JLMS_DB->insertObject("#__lms_topic_items", $insert, "id");
					$course_topic_items[$i]->new_topic_item_id = $JLMS_DB->insertid();
				}
			}
			$i++;
		}



		if (in_array(8, $imp_tools)) {
			// ****************************************************************************************************
			// Get GradeBook Items DATA and insert it into the 'lms_gradebook_items' table

			$element = &$root->getElementByPath('gradebook_items');
			$gb_items = JLMS_parse_XML_elements($element->children(), array('gbi_option', 'ordering'), array('gbi_name', 'gbi_description', 'gb_category'));
			$i = 0;
			while ( $i < count ($gb_items) ){
				$insert = new stdClass();
				$query = "SELECT id FROM #__lms_gradebook_cats WHERE gb_category = '".$gb_items[$i]->gb_category."'";
				$JLMS_DB->SetQuery( $query );
				$cat_id = $JLMS_DB->LoadResult();
				if (!$cat_id) { $cat_id = 0; }
				$insert->course_id			= $course_id;
				$insert->gbc_id				= $cat_id;
				$insert->gbi_name			= $gb_items[$i]->gbi_name;
				$insert->gbi_description	= $gb_items[$i]->gbi_description;
				$insert->gbi_points			= 0;
				$insert->gbi_option			= $gb_items[$i]->gbi_option;
				$insert->ordering			= $gb_items[$i]->ordering;
				$JLMS_DB->insertObject("#__lms_gradebook_items", $insert, "id");
				$gb_items[$i]->new_gbi_id = $JLMS_DB->insertid();
				$i++;
			}

			// ****************************************************************************************************
			// Get GradeBook Scale DATA and insert it into the 'lms_gradebook_scale' table

			$element = &$root->getElementByPath('gradebook_scale');
			$gb_scale = JLMS_parse_XML_elements($element->children(), array('min_val', 'max_val', 'ordering'), array('scale_name'));
			$i = 0;
			while ( $i < count ($gb_scale) ){
				$insert = new stdClass();
				$insert->course_id			= $course_id;
				$insert->scale_name			= $gb_scale[$i]->scale_name;
				$insert->min_val			= $gb_scale[$i]->min_val;
				$insert->max_val			= $gb_scale[$i]->max_val;
				$insert->ordering			= $gb_scale[$i]->ordering;
				$JLMS_DB->insertObject("#__lms_gradebook_scale", $insert, "id");
				$gb_scale[$i]->new_gbs_id = $JLMS_DB->insertid();
				$i++;
			}

			// ****************************************************************************************************
			// Get GradeBook Scale DATA and insert it into the 'lms_gradebook_scale' table

			$element = &$root->getElementByPath('gradebook_lpaths');
			$gb_lpaths = JLMS_parse_XML_elements($element->children(), array('learn_path_id'), array());
			$i = 0;
			while ( $i < count ($gb_lpaths) ){
				$insert = new stdClass();
				$insert->course_id		= $course_id;
				$insert->learn_path_id	= 0;
				foreach ($l_paths as $lp) {
					if ($gb_lpaths[$i]->learn_path_id == $lp->id) {
						$insert->learn_path_id	= $lp->new_step_id;
					}
				}
				if ($insert->learn_path_id) {
					$JLMS_DB->insertObject("#__lms_gradebook_lpaths", $insert, "id");
				}
				$i++;
			}
		}
	}

	// delete temporary files
	deldir($extract_dir);
	return $archive;

}

function JLMS_parse_XML_elements(&$elements, $arr_attrib, $arr_paths, $is_recurse = false, $rname = array(), $arr_attribr = array(), $arr_pathsr = array()) {
	$ret_array = array();
	if (!empty($elements) && is_array($elements)) {
		foreach ($elements as $element) {
			$tmp = new StdClass();
			foreach ($arr_attrib as $attrib) {
				$tmp->$attrib = $element->attributes($attrib);
			}
			foreach ($arr_paths as $path) {
				$test = &$element->getElementByPath ($path);
				if ($test === null) {
					$tmp->$path = '';//if course was exported on the old version and some fields doesn't exists
				} else {
					$tmp->$path = $test->data();
				}
			}
			if ( $is_recurse ) {
				$ii = 0;
				foreach ($rname as $rn) {
					$rn_elements = & $element->getElementByPath($rn);
					#$rn_childs = & $rn_elements->children();
					$tmp->$rn = JLMS_parse_XML_elements( $rn_elements->children(), $arr_attribr[$ii], $arr_pathsr[$ii]);
					$ii ++;
				}
			}
			$ret_array[] = $tmp;
		}
	}
	return $ret_array;
}
?>