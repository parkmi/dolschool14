<?php
/**
* includes/lms_bb2lms_converter.php
* Joomla LMS Component
* * * ElearningForce DK
**/
defined( '_JLMS_EXEC' ) or die( 'Restricted access' );

require_once(_JOOMLMS_FRONT_HOME . "/includes/libraries/lms.lib.zip.php");

class JLMS_bb2lms_converter extends JLMSObject {
	var $options = array('documents', 'links', 'quizzes', 'homework', 'gradebook_items', 'announcements');
	var $bb_version = '7.1';
	var $is_prepared = 0;
	var $bb_file_media_name = '';
	var $bb_file_tmp_name = '';
	var $charset_lang = 'ISO-8859-1';

/**
 * constructor
 *
 * @param  Array - array with items to convert
 *   default - array('documents'=>true, 'links'=>true, 'quizzes'=>true, 'homework'=>true, 'gradebook_items'=>true, 'announcements'=>true)	
 */
	function __construct($options = null) {
		$this->is_prepared = 0;
		if (!empty($options)) {
			$this->options = $options;
		}
	}
	
	function prepare($field_name) {
		$this->is_prepared = 0;
		$bbfile = mosGetParam( $_FILES, $field_name, null );
		if(!extension_loaded('zlib')) {
			return "Error! zlib library unavailable";
		}
		if (!$bbfile) {
			return _JLMS_EM_SELECT_FILE;
		}

		$bbfile_name = $bbfile['name'];
		if (empty($bbfile_name)) {
			return _JLMS_EM_SELECT_FILE;
		}

		if (strcmp(substr($bbfile_name,-4,1),".")) {
			return _JLMS_EM_BAD_FILEEXT;
		}

		if (strcmp(substr($bbfile_name,-4),".zip")) {
			return _JLMS_EM_BAD_FILEEXT;
		}

		$tmp_name = $bbfile['tmp_name'];
		if (!file_exists($tmp_name)) {
			return _JLMS_EM_UPLOAD_SIZE_ERROR;
		} else {
			#$this->$bb_filelocation = $tmp_name;
		}
		if (preg_match("/.zip$/", strtolower($bbfile_name))) {
			
			$zipFile = new pclZip($tmp_name);
			$zipContentArray = $zipFile->listContent();
			$exp_xml_file = false;
			foreach($zipContentArray as $thisContent) {
				if ( preg_match('~.(php.*|phtml)$~i', $thisContent['filename']) ) {
					return _JLMS_EM_READ_PACKAGE_ERROR;
				}
				if ($thisContent['filename'] == 'imsmanifest.xml'){
					$exp_xml_file = true;
				}
			}
			if ($exp_xml_file == false){
				return "Could not find a Course XML setup file in the package.";
			}
		} else {
			return _JLMS_EM_BAD_FILEEXT;
		}
		
		$config		= & JFactory::getConfig();
		$tmp_dest	= $config->getValue('config.tmp_path').DS.$bbfile['name'];
		$tmp_src	= $bbfile['tmp_name'];
	
		// Move uploaded file
		jimport('joomla.filesystem.file');
		$uploaded = JFile::upload($tmp_src, $tmp_dest);
		if ($uploaded) {
			$this->bb_file_media_name = '';
			$this->bb_file_tmp_name = $tmp_dest;
			$this->is_prepared = 1;
			return '';
		} else {
			return 'File not found';
		}
	}

	function prepareFTP($filename) {

		$this->is_prepared = 0;
		
		if(!extension_loaded('zlib')) {
			return "Error! zlib library unavailable";
		}
		if (!$filename) {
			return _JLMS_EM_SELECT_FILE;
		}

		if (strcmp(substr($filename,-4,1),".")) {
			return _JLMS_EM_BAD_FILEEXT;
		}

		if (strcmp(substr($filename,-4),".zip")) {
			return _JLMS_EM_BAD_FILEEXT;
		}

		$baseDir = mosPathName( JPATH_SITE . '/media' );

		$tmp_name = $baseDir . $filename;
		if (!file_exists($tmp_name)) {
			return _JLMS_EM_UPLOAD_SIZE_ERROR;
		}
		if (preg_match("/.zip$/", strtolower($filename))) {
			
			$zipFile = new pclZip($tmp_name);
			$zipContentArray = $zipFile->listContent();
			$exp_xml_file = false;
			foreach($zipContentArray as $thisContent) {
				if ( preg_match('~.(php.*|phtml)$~i', $thisContent['filename']) ) {
					return _JLMS_EM_READ_PACKAGE_ERROR;
				}
				if ($thisContent['filename'] == 'imsmanifest.xml'){
					$exp_xml_file = true;
				}
			}
			if ($exp_xml_file == false){
				return "Could not find a Course XML setup file in the package.";
			}
		} else {
			return _JLMS_EM_BAD_FILEEXT;
		}
		
		$this->bb_file_media_name = $filename;
		$this->is_prepared = 1;
		return '';
	}

/**
	* Convert BB export course file to JLMS export course file and upload it to database
	* @param String Name of form field with bb filename
	* @return Array  ['lmsfile'] - Name of JLMS export course file (file placed in \media) (null if error)
					 ['msg']	 - Message
*/ 
	function processBBfile( $course_id = 0, $def_course_name = 'BB course', $do_lms_archive = true, $upload_to_db = true ) {
		global $JLMS_DB, $JLMS_CONFIG, $my;

		if (!$this->is_prepared) {
			return array('lmsfile'=>null, 'msg'=>"Error! Invalid parameters passed");
		}

		if ((!$do_lms_archive && !$upload_to_db) || (!$this->bb_file_media_name && !$this->bb_file_tmp_name))
			return array('lmsfile'=>null, 'msg'=>"Error! Invalid parameters passed");
			
		
		require_once($JLMS_CONFIG->get('absolute_path')."/components/com_joomla_lms/includes/jlms_dir_operation.php");

		require_once($JLMS_CONFIG->get('absolute_path')."/components/com_joomla_lms/includes/jlms_zip_operation.php");

		// for function "JLMS_parse_XML_elements()"
		require_once($JLMS_CONFIG->get('absolute_path')."/components/com_joomla_lms/includes/jlms_course_import.php");

		@set_time_limit('3000');
		// Check that the zlib is available
		if(!extension_loaded('zlib')) {
			return array('lmsfile'=>null, 'msg'=>"Error! zlib library unavailable");
		}

		if (!$this->bb_file_media_name && !$this->bb_file_tmp_name) {
			return array('lmsfile'=>null, 'msg'=>_JLMS_EM_SELECT_FILE);	
		}
		$config			= & JFactory::getConfig();
		$extract_dir	= $config->get('tmp_path').DS."bb_course_export_".uniqid(rand()).DS;
		if ($this->bb_file_tmp_name) {
			$archive = $this->bb_file_tmp_name;
		} else {
			$archive = $JLMS_CONFIG->get('absolute_path')."/media/".$this->bb_file_media_name;
		}
		
		//exstract archive in uniqfolder media
		extractBackupArchive( $archive, $extract_dir);
			
		$xmlFile = $extract_dir."imsmanifest.xml";		
		
		if ($fp1 = fopen($xmlFile, "r")) {
   			$fline = fgets($fp1);
  			$match = array();
   			preg_match('/encoding="([0-9a-zA-Z-]*)"/i', $fline, $match); //find quoted encoding
   			if (isset($match[1]) && $match[1] != "ISO-8859-1") {
   				$this->charset_lang = $match[1];
   			}
			fclose($fp1);
 		}
		
		$xmlDoc = &JLMSFactory::getXMLParser();		
		if (!$xmlDoc->loadFile( $xmlFile )) {
			return array('lmsfile'=>null, 'msg'=>'Error during reading xml file');
		}
	
		$root = &$xmlDoc->document;
		
		if ($root->name() != 'manifest') {
			return array('lmsfile'=>null, 'msg'=>'Not a Course installation file');
		}
		
		
		
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
		// ***************************************************************************************************
		$course = new stdClass();

		$course->course_name 		= $def_course_name;
		$course->course_description = '';
		$course->metadesc			= '';
		$course->metakeys			= '';
		$course->self_reg			= '';
		$course->paid				=  0;
		$course->add_forum 			= 0;
		$course->add_chat 			= 0;
		$course->owner_id 			= $my->id;
		$course->published 			= 0;
		$course->publish_start		= 0;
		$course->publish_end		= 0;
		$course->start_date			= '0000-00-00';
		$course->end_date			= '0000-00-00';
		$course->spec_reg			= 0;
		$course->cat_id = 0;
		// 02.03.2007 1.0.1 support
		$course->params				= '';
		$course->gid = 0;
		$course->language 			= 0;

		//get resources
		$element = &$root->getElementByPath( 'resources' );	
		
		$resources = JLMS_parse_XML_elements($element->children(), array('identifier', 'type'), array());
		foreach($resources as $resource){
			if ( $resource->type == 'course/x-bb-coursesetting' ) {
				$resourceFile = $extract_dir.$resource->identifier.".dat"; 
				$resourceXmlDoc = & JLMSFactory::getXMLParser();				
				////$resourceXmlDoc->resolveErrors( true );		
				if ($resourceXmlDoc->loadFile( $resourceFile )) {
					$resourceRoot = &$resourceXmlDoc->document;							
					$resourceElement = &$resourceRoot->getElementByPath('title');															
					$course->course_name = $this->attributes($resourceElement, 'value');					
					if ($def_course_name != 'BB course') {
						$course->course_name = $def_course_name;
					}
					$resourceElement = &$resourceRoot->getElementByPath('description');
					$course->course_description = $this->data($resourceElement);
				}
			}
		}
		//save course
		if ($upload_to_db) {
			if (!$course_id) {
				$JLMS_DB -> insertObject("#__lms_courses" , $course , "id");
				//get new Course_id
				$course_id = $JLMS_DB->insertid();
				// create teacher for imported course
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
				$query = "INSERT INTO `#__lms_user_courses` (user_id, course_id, role_id) VALUES ('".$my->id."','".$course_id."','".$default_teacher_role."')";
				$JLMS_DB->setQuery($query);
				$JLMS_DB->query();
			}
		}
		
	// ******************************************************************************************************************
	// process resourses	
		$i = 0;
		//get data from resource files
		while( $i < count($resources) ){
			//$resources[$i]->identifier .= ".dat"; 
			$resourceFile = $extract_dir.$resources[$i]->identifier.".dat"; 
			//echo $resourceFile.'<br />';
			$resourceXmlDoc = & JLMSFactory::getXMLParser();
			//$resourceXmlDoc->resolveErrors( true );			
			if ($resourceXmlDoc->loadFile( $resourceFile )) {
				$resourceRoot = &$resourceXmlDoc->document;
				//file, folder, link or content
				$resources[$i]->resource_type = '';
				if ( $resources[$i]->type == 'resource/x-bb-document') {
					$resourceElement = &$resourceRoot->getElementByPath( 'contenthandler' );
					// resource/x-bb-document - content may be with file
					// resource/x-bb-folder - foder
					// resource/x-bb-externallink - link
					$resources[$i]->resource_type = $this->attributes($resourceElement, 'value');
					if ( $resources[$i]->resource_type == 'resource/x-bb-document' ||
						 $resources[$i]->resource_type == 'resource/x-bb-folder' ||
						 $resources[$i]->resource_type == 'resource/x-bb-externallink'  ) {
						
						//save files
						$resourceElement = &$resourceRoot->getElementByPath( 'files' );
						$files = JLMS_parse_XML_elements($resourceElement->children(), array('id'), array('name'));
						if ( count($files) > 0 ) {
							$resources[$i]->files = $files; 
						}
							
						//$resourceElement = &$resourceRoot->getElementByPath('CONTENT', 1);						
						$resources[$i]->id = $this->attributes($resourceRoot, 'id');
						
						//save URLs
						$resourceElement = &$resourceRoot->getElementByPath('url');
						if ( $this->attributes($resourceElement, 'value') )
							$resources[$i]->url = $this->attributes($resourceElement, 'value');
					
					
						//save name and description
						$resourceElement = &$resourceRoot->getElementByPath('title');
						if ( $this->attributes($resourceElement, 'value') )
							$resources[$i]->title = $this->attributes($resourceElement, 'value');
						else
							$resources[$i]->title = '';
					
						$resourceElement = &$resourceRoot->getElementByPath('body');
						$text = &$resourceElement->getElementByPath ('text');
						if ( $text )
							$resources[$i]->text = $this->data($text);
						else
							$resources[$i]->text = '';
							
						//save START and END date
						$resourceElement = &$resourceRoot->getElementByPath('dates');
						$tmp = $resourceElement->getElementByPath('start');
						if ( $this->attributes($tmp, 'value') )
							$resources[$i]->start = $this->attributes($tmp, 'value');
					
						$tmp = $resourceElement->getElementByPath('end');
						if ( $this->attributes($tmp, 'value') )
							$resources[$i]->end = $this->attributes($tmp, 'value');
					}
				}## if ( $resources[$i]->type == 'resource/x-bb-document') ....
			
				//announcement
				elseif ( $resources[$i]->type == 'resource/x-bb-announcement' ) {
					//save name and description		
					$resourceElement = &$resourceRoot->getElementByPath('title');
					if ( $this->attributes($resourceElement, 'value') )
						$resources[$i]->title = $this->attributes($resourceElement, 'value');
					
					$resourceElement = &$resourceRoot->getElementByPath('description');
					$text = &$resourceElement->getElementByPath ('text');
					if ( $text )
						$resources[$i]->text = $this->data($text);
					else
						$resources[$i]->text = '';	
						
					//save START and END date
					$resourceElement = &$resourceRoot->getElementByPath('dates');
					$tmp = $resourceElement->getElementByPath('RESTRICTSTART');
					if ( $this->attributes($tmp, 'value') )
						$resources[$i]->restrictstart = $this->attributes($tmp, 'value');	
					
					$tmp = $resourceElement->getElementByPath('RESTRICTEND');
					if ( $this->attributes($tmp, 'value') )
						$resources[$i]->restrictend = $this->attributes($tmp, 'value');	
				}## elseif ( $resources[$i]->type == 'resource/x-bb-announcement' ) ....
				
				//homework
				elseif ( $resources[$i]->type == 'resource/x-bb-task' ) {
					//save name and description
					$resourceElement = &$resourceRoot->getElementByPath('title');
					if ( $this->attributes($resourceElement, 'value') )
						$resources[$i]->title = $this->attributes($resourceElement, 'value');
					
					$resourceElement = &$resourceRoot->getElementByPath('description');
					$text = &$resourceElement->getElementByPath ('text');
					if ( $text )
						$resources[$i]->text = $this->data($text);
					else 
						$resources[$i]->text = '';
						
					//save DUE date
					$resourceElement = &$resourceRoot->getElementByPath('dates');
					$tmp = $resourceElement->getElementByPath('due');
					if ( $this->attributes($tmp, 'value') )
						$resources[$i]->due = $this->attributes($tmp, 'value');	
	
				}## elseif ( $resources[$i]->type == 'resource/x-bb-announcement' ) ....
				
				//quizzes
				elseif ($resources[$i]->type == 'assessment/x-bb-qti-test' || 
						$resources[$i]->type == 'assessment/x-bb-qti-pool' || 
						$resources[$i]->type == 'assessment/x-bb-qti-survey') {
					//save name and description
					
					$resourceElement = &$resourceRoot->getElementByPath('assessment');
					$resourceElement = &$resourceElement->getElementByPath('section');
					$resourceElement = &$resourceElement->getElementByPath ('sectionmetadata');
					$resourceElement = &$resourceElement->getElementByPath ('bbmd_assessmenttype');
					$assess_type = $this->data($resourceElement);
					$resources[$i]->assess_type = $assess_type;
					if ($assess_type != 'Survey'){
					
						$resourceElement = &$resourceRoot->getElementByPath('assessment');
						if ( $this->attributes($resourceElement, 'title') )
							$resources[$i]->title = $this->attributes($resourceElement, 'title');
						
						$resourceElement = &$resourceElement->getElementByPath('presentation_material');
						$resourceElement = &$resourceElement->getElementByPath('flow_mat');
						$resourceElement = &$resourceElement->getElementByPath('material');
						$resourceElement = &$resourceElement->getElementByPath('mat_extension');
						$resourceElement = &$resourceElement->getElementByPath('mat_formattedtext');
						$resources[$i]->text = $this->data($resourceElement);
					
						//save max score
						$resourceElement = &$resourceRoot->getElementByPath('assessment');
						$resourceElement = &$resourceElement->getElementByPath('section');
						$resourceElement = &$resourceElement->getElementByPath('sectionmetadata');
						$tmp = &$resourceElement->getElementByPath('bbmd_asi_object_id');
						$resources[$i]->q_id = $this->data($tmp);
						$resourceElement = &$resourceElement->getElementByPath('qmd_absolutescore_max');
						$resources[$i]->score_max = $this->data($resourceElement);
					
						//save questions
						$resourceElement = &$resourceRoot->getElementByPath('assessment');
						$resourceElement = &$resourceElement->getElementByPath('section');
			
					
						$resources[$i]->questions = $this->getQuestions($resourceElement->children(), $resources[$i]->q_id, $resources[$i]->identifier);
						$resources[$i]->answers = $this->getAnswers($resourceElement->children(), $resources[$i]->q_id);
					}
				}##elseif ($resources[$i]->type == 'assessment/x-bb-qti-test' ....
	
				//gradebook
				elseif ( $resources[$i]->type == 'course/x-bb-gradebook' ) {
					//get name and descriptoin
					$resourceElement = &$resourceRoot->getElementByPath('outcomedefinitions');
					$gb = array();
					foreach($resourceElement->children() as $item) {
						$title = $item->getElementByPath('title');
						$description = $item->getElementByPath('description');
						if ($this->attributes($title, 'value') == 'OutcomeDefinition.Total.title' || $this->attributes($title, 'value') == 'OutcomeDefinition.WeightedTotal.title')
							continue;
							
						$identifier = $item->getElementByPath('asidataid');
						$identifier = $this->attributes($identifier, 'value');
						if (!$identifier) {
							$insert = new stdClass();
							$insert->title = $this->attributes($title, 'value');
							$insert->description = $this->data($description);
							$gb[] = $insert;
						}					
					}
					$resources[$i]->gradebook = $gb; 
				}##elseif ( $resources[$i]->type == 'course/x-bb-gradebook' ) ....
				
				elseif ( $resources[$i]->type == 'course/x-bb-coursesetting' ) {
					$resourceElement = &$resourceRoot->getElementByPath('title');				
					$course->course_name = $this->attributes($resourceElement, 'value');
					$resourceElement = &$resourceRoot->getElementByPath('description');
					$course->course_description = $this->data($resourceElement);
				}##elseif ( $resources[$i]->type == 'course/x-bb-coursesetting' ) ....
				
			}## if ($resourceXmlDoc->loadXML( $resourceFile, false, true )) ....
	
		$i++;	
		}## while( $i < count($resources) ) ....
	
	// *******************************************************************************************************************
		
		$i = 0;
		$announcements = array();
		$homeworks = array();
		$links = array();	
		$files = array();
		$gradebook = array();
		$quizzes_no = array();
		$quizzes = array();
		$questions = array();
		$answers = array();
		//prepare data to save
		while ( $i < count($resources) ){
		
			if (($resources[$i]->type == 'assessment/x-bb-qti-test' || 
			$resources[$i]->type == 'assessment/x-bb-qti-pool' || 
			$resources[$i]->type == 'assessment/x-bb-qti-survey') && $resources[$i]->assess_type != 'Survey' ) {
				
				$insert = new stdClass();
				
				$insert->_c_id 					= $resources[$i]->q_id;
				$insert->c_category_id			= null;
				$insert->c_user_id				= $my->id;
				$insert->c_full_score			= abs($resources[$i]->score_max); // 17.12.2007 (DEN) - negative values bug - needed another solution - BB courses have negative points values !
				$insert->c_title				= $resources[$i]->title;
				$insert->c_description			= $resources[$i]->text;
				$insert->c_time_limit			= 5;
				$insert->c_min_after			= 5;
				$insert->c_passing_score		= 1;
				$insert->c_created_time			= date('Y-m-d');
				$insert->published				= 0;
				$insert->c_right_message		= 'correct';
				$insert->c_wrong_message		= 'incorrect';
				$insert->c_pass_message			= 'pass';
				$insert->c_unpass_message		= 'fail';
				$insert->c_enable_review		= null;
				$insert->c_email_to				= 0;
				$insert->c_enable_print			= null;
				$insert->c_enable_sertif		= null;
				$insert->c_skin					= 3;
				$insert->c_random				= 0;
				$insert->c_guest				= 0;
				$insert->c_slide				= 1;
				$insert->c_language				= 1;
				$insert->c_certificate 			= 0;
				$insert->c_gradebook			= 1;
				$insert->course_id 				= $course_id;
				$quizzes[] = $insert;
				$quizzes_no[] = $i;
				$questions[] = $resources[$i]->questions;
				$answers[] = $resources[$i]->answers;
			}
						
			if ( isset($resources[$i]->url)) {
				$insert = new stdClass();
				$insert->course_id			= $course_id;
				$insert->owner_id			= $my->id;
				$insert->link_name			= $resources[$i]->title;	
				$insert->link_href			= $resources[$i]->url;
				$insert->link_description	= $resources[$i]->text;
				$insert->link_type			= 0;
				$insert->ordering			= 0;
				$insert->published			= 0;
				$insert->_id 				= $resources[$i]->id;
				$links[] = $insert; 
			}
			
			if ($resources[$i]->type == 'resource/x-bb-announcement') {
				$insert = new stdClass();
				$insert->course_id			= $course_id;
				$insert->owner_id			= $my->id;
				$insert->title				= $resources[$i]->title;	
				$insert->content			= $resources[$i]->text;
				$insert->start_date			= $resources[$i]->restrictstart;
				$insert->end_date			= (isset($resources[$i]->restrictend) ? $resources[$i]->restrictend : $resources[$i]->restrictstart);
				$announcements[] = $insert;
			}
			
			if ($resources[$i]->type == 'resource/x-bb-task') {
				$insert = new stdClass();
				$insert->course_id			= $course_id;
				$insert->hw_name			= $resources[$i]->title;	
				$insert->hw_description		= $resources[$i]->text;
				$insert->hw_shortdescription= $resources[$i]->text;
				$insert->post_date			= date("Y-m-d");
				$insert->end_date			= $resources[$i]->due;
				$homeworks[] = $insert;
			}
			
			if ( $resources[$i]->type == 'course/x-bb-gradebook' ) {
				foreach($resources[$i]->gradebook as $item) {
					$insert = new stdClass();
					$insert->course_id			= $course_id;
					$insert->gbc_id				= 0;
					$insert->gbi_name			= $item->title;
					$insert->gbi_description	= $item->description;
					$insert->gbi_points			= 0;
					$insert->gbi_option			= 0;
					$insert->ordering			= 0;
					$gradebook[] = $insert;
				}
			}
			
			if ( isset($resources[$i]->files)) {
				$file = $resources[$i]->files[0];
				if ($this->charset_lang != 'ISO-8859-1') {
					$file->name = htmlentities($file->name, ENT_QUOTES, $this->charset_lang);
					$file->name = str_replace('&lt;', '<', $file->name);
					$file->name = str_replace('&gt;', '>', $file->name);
					$file->name = str_replace('&amp;', '&', $file->name);
				}
				
				$fromDir = $extract_dir.$resources[$i]->identifier."/";
				$toDir   = $lms_cfg_doc_folder."/";
				
				$insert_file = new stdClass();
				$insert_file->_fromDir = $extract_dir.$resources[$i]->identifier."/";
				$insert_file->_toDir = $lms_cfg_doc_folder."/";
				$insert_file->file_name = $file->name;
				$file_unique_name = str_pad($course_id,4,'0',STR_PAD_LEFT) . '_' . md5(uniqid(rand(), true)) . '.' . substr($file->name,-3);
				$insert_file->file_srv_name = $file_unique_name;
				$insert_file->owner_id = $my->id;
				
				if (!file_exists($insert_file->_fromDir.$insert_file->file_name)) {
					$res_dir = opendir( $insert_file->_fromDir );
					while (false !== ($res_file = readdir($res_dir))) { 
	        			if ($res_file != "." && $res_file != "..") { 
	           				 rename ($insert_file->_fromDir.$res_file, $insert_file->_fromDir.$insert_file->file_srv_name);
							 break;
	        			} 
	    			}
	    			closedir($res_dir);				
				}
				else {
					rename ($insert_file->_fromDir.$insert_file->file_name, $insert_file->_fromDir.$insert_file->file_srv_name);
				}			
				$insert_file->_id = $file->id;			
				$file->file_srv_name = $insert_file->file_srv_name;
				$resources[$i]->files[0] = $file;
				$files[] = $insert_file;
			}
			
			$i++;
		}
		
		$element = &$root->getElementByPath('organizations');	
		$element = $element->getElementByPath('organization');
		//$items = JLMS_parse_XML_elements($element->children(), array('identifierref'), array());
		
		$i = 0;
		$documents1 = array();
		while( $i < count($element->children()) ){
			$nodes = $element->children();
			$node = $nodes[$i];
			$title = $node->getElementByPath('title');
			$title = substr($this->data($title), 15);
			$title = substr($title, 0, strpos($title, "."));
			foreach($node->children() as $item) {
				if ($item->name() == 'item') {
					$this->Process_Item($item, $documents1, $resources);
				}
			}
			$i++;
		}
		$documents1 = array_reverse($documents1);
		
		// insert data to database
		if ( $upload_to_db ) {
			
			if (in_array('links',$this->options)) {
				for ( $i = 0, $n = count($links); $i < $n; $i++) {
					$JLMS_DB->insertObject("#__lms_links", $links[$i], "id");
					$links[$i]->_id = $JLMS_DB->insertid();
				}
			}
			
			if (in_array('announcements',$this->options)) {
				for ( $i = 0, $n = count($announcements); $i < $n; $i++) {
					$JLMS_DB->insertObject("#__lms_agenda", $announcements[$i], "id");
				}
			}
			
			if (in_array('homework',$this->options)) {
				for ( $i = 0, $n = count($homeworks); $i < $n; $i++) {
					$JLMS_DB->insertObject("#__lms_homework", $homeworks[$i], "id");
				}
			}
	
			if (in_array('gradebook_items',$this->options)) {
				for ( $i = 0, $n = count($gradebook); $i < $n; $i++) {
					$JLMS_DB->insertObject("#__lms_gradebook_items", $gradebook[$i], "id");
				}
			}
			
			if (in_array('documents',$this->options)) {
				for ( $i = 0, $n = count($files); $i < $n; $i++) {
					$JLMS_DB->insertObject("#__lms_files", $files[$i], "id");
			  		$files[$i]->new_file_id = $JLMS_DB->insertid();
				}
			}
			
			if (in_array('quizzes',$this->options)) {
				$i = 0;
				while ( $i < count($quizzes) ){
					$JLMS_DB->insertObject("#__lms_quiz_t_quiz", $quizzes[$i], "c_id");
					$new_quiz_id = $JLMS_DB->insertid();
					$quizzes[$i]->new_quiz_id = $new_quiz_id;
					$quizzes[$i]->_c_id = $new_quiz_id;
					
					//questions processing
					$j = 0;
					while ($j < count($questions[$i])) {
						$questions[$i][$j]->course_id 	= $course_id;
						$questions[$i][$j]->c_quiz_id	= $new_quiz_id;
						$JLMS_DB->insertObject("#__lms_quiz_t_question", $questions[$i][$j], "c_id");
						$questions[$i][$j]->new_id = $JLMS_DB->insertid();
						$j ++;
					}
		
					//choices processing
					$j = 0;
					while ($j < count($answers[$i]['choice_data'])) {
						for ($k=0; $k<count($questions[$i]); $k++){
							if ($questions[$i][$k]->_c_id == $answers[$i]['choice_data'][$j]->c_question_id){
								$answers[$i]['choice_data'][$j]->c_question_id = isset($questions[$i][$k]->new_id) ? $questions[$i][$k]->new_id : 0; break;
							}	
						}
						if ($answers[$i]['choice_data'][$j]->c_question_id > 0 ) {
							$JLMS_DB->insertObject("#__lms_quiz_t_choice", $answers[$i]['choice_data'][$j], "c_id");
						}
						$j ++;
					}
		
					//matching processing
					$j = 0;
					while ($j < count($answers[$i]['match_data'])) {
						for ($k=0; $k<count($questions[$i]); $k++){
							if ($questions[$i][$k]->_c_id == $answers[$i]['match_data'][$j]->c_question_id){
								$answers[$i]['match_data'][$j]->c_question_id = isset($questions[$i][$k]->new_id) ? $questions[$i][$k]->new_id : 0; break;
							}	
						}
						if ($answers[$i]['match_data'][$j]->c_question_id > 0 ) {
							$JLMS_DB->insertObject("#__lms_quiz_t_matching", $answers[$i]['match_data'][$j], "c_id");
						}
						$j ++;
					}
		
					//hotspot processing
					$j = 0;
					while ($j < count($answers[$i]['hotspot_data'])) {
						for ($k=0; $k<count($questions[$i]); $k++){
							if ($questions[$i][$k]->_c_id == $answers[$i]['hotspot_data'][$j]->c_question_id){
								$answers[$i]['hotspot_data'][$j]->c_question_id = isset($questions[$i][$k]->new_id) ? $questions[$i][$k]->new_id : 0; break;
							}	
						}
						if ($answers[$i]['hotspot_data'][$j]->c_question_id > 0) {
							$JLMS_DB->insertObject("#__lms_quiz_t_hotspot", $answers[$i]['hotspot_data'][$j], "c_id");
						}
						$j ++;
					}
		
					//blank quests processing
					$j = 0;
					while ($j < count($answers[$i]['blank_data'])) {
						for ($k=0; $k<count($questions[$i]); $k++){
							if ($questions[$i][$k]->_c_id == $answers[$i]['blank_data'][$j]['obj']->c_question_id){
								$answers[$i]['blank_data'][$j]['obj']->c_question_id = isset($questions[$i][$k]->new_id) ? $questions[$i][$k]->new_id : 0; break;
							}	
						}
						if ($answers[$i]['blank_data'][$j]['obj']->c_question_id > 0) {
							$JLMS_DB->insertObject("#__lms_quiz_t_blank", $answers[$i]['blank_data'][$j]['obj'], "c_id");
							$new_blank_id = $JLMS_DB->insertid();
							foreach( $answers[$i]['blank_data'][$j]['ans'] as $ans ) {
								$q_blank_text = new stdClass();
								$q_blank_text->c_blank_id	= $new_blank_id;
								$q_blank_text->c_text		= $ans;
								$q_blank_text->ordering		= 0;
								if ($q_blank_text->c_blank_id > 0 ) {
									$JLMS_DB->insertObject("#__lms_quiz_t_text", $q_blank_text, "c_id");
								}
							}
						}
						$j ++;
					}	
					$i ++;
				}
			}	
			
			if (in_array('documents',$this->options)) {	
				$i = 0;
				while ( $i < count($documents1) ) {
					$insert = new stdClass();
					$insert->course_id = $course_id;
					$insert->owner_id = $my->id;
					$insert->file_id = 0;
					
					if ($documents1[$i]->file_id) {
						// search $files for new file_id
						for ($j=0; $j<count($files); $j++){
							if($files[$j]->_id == $documents1[$i]->file_id){
								$insert->file_id = $files[$j]->new_file_id; break;
							}	
						}
					}
					$insert->folder_flag		= $documents1[$i]->folder_flag;
					$insert->doc_name			= $documents1[$i]->doc_name;
					$insert->doc_description	= $documents1[$i]->doc_description;
					$insert->ordering			= $documents1[$i]->ordering;
					$insert->published			= $documents1[$i]->published;
					$insert->publish_start		= $documents1[$i]->publish_start;
					$insert->start_date			= $documents1[$i]->start_date;
					$insert->publish_end		= $documents1[$i]->publish_end;
					$insert->end_date			= $documents1[$i]->end_date;
					// search processed $documents for parent_id
					$parent = $documents1[$i]->parent_id;
					if ($parent){
						$a = 0;
						while ($a < $i){
							if ($documents1[$a]->id == $parent){
							$parent = $documents1[$a]->new_doc_id; break;
							}
							$a ++;
						}
					}
					$insert->parent_id = $parent;
					$JLMS_DB -> insertObject("#__lms_documents" , $insert , "id");
					$documents1[$i]->new_doc_id = $JLMS_DB->insertid();
					$i++;
				}
			}
			
			if (in_array('quizzes',$this->options)) {
				$toDir   = $JLMS_CONFIG->get('absolute_path')."/images/joomlaquiz/images/";
				foreach ($questions as $question) {
					foreach($question as $quest){
						if (isset($quest->_c_image)) {
							$filename = $extract_dir.$quest->_identifier."/".$quest->_c_image;
							//$pz->add($filename,'quiz_images', $extract_dir.$quest->identifier."/".substr($quest->_c_image, 0, strpos($quest->_c_image, '\\'))."/");
							@copy ($filename, $toDir.$quest->c_image);
						}
					}
				}
			}
			if (in_array('documents',$this->options)) {
				$toDir   = $lms_cfg_doc_folder."/";
				foreach($files as $file){
					$filename = $file->_fromDir.$file->file_srv_name;
					//$pz->add($filename,'files', $file->_fromDir);
					copy ($filename, $toDir.$file->file_srv_name);	
				}
			}
		}
		
		if ($do_lms_archive) {
			
			$export_xml = "";
			$export_xml .= "<?xml version=\"1.0\" ?>\r\n";
			$export_xml .= "\t<course_backup lms_version=\"1.0.0\">\r\n";
			$export_xml .= "\t\t<name><![CDATA[".$course->course_name."]]></name>\r\n";
			$export_xml .= "\t\t<description><![CDATA[".$course->course_description."]]></description>\r\n";
			$export_xml .= "\t\t<course_category><![CDATA[]]></course_category>\r\n";
			$export_xml .= "\t\t<metadesc><![CDATA[".$course->metadesc."]]></metadesc>\r\n";
			$export_xml .= "\t\t<metakeys><![CDATA[".$course->metakeys."]]></metakeys>\r\n";
			$export_xml .= "\t\t<language_name><![CDATA[english]]></language_name>\r\n";
			$export_xml .= "\t\t<course_paid><![CDATA[".$course->paid."]]></course_paid>\r\n";
			$export_xml .= "\t\t<self_registration>".$course->self_reg."</self_registration>\r\n";
			$export_xml .= "\t\t<forum_enabled>".$course->add_forum."</forum_enabled>\r\n";
			$export_xml .= "\t\t<chat_enabled>".$course->add_chat."</chat_enabled>\r\n";
			$export_xml .= "\t\t<publish_start>".$course->publish_start."</publish_start>\r\n";
			$export_xml .= "\t\t<publish_start_date><![CDATA[".$course->start_date."]]></publish_start_date>\r\n";
			$export_xml .= "\t\t<publish_end>".$course->publish_end."</publish_end>\r\n";
			$export_xml .= "\t\t<publish_end_date><![CDATA[".$course->end_date."]]></publish_end_date>\r\n";
			$export_xml .= "\t\t<spec_reg>".$course->spec_reg."</spec_reg>\r\n";
			$export_xml .= "\t\t<course_question><![CDATA[]]></course_question>\r\n";
			$export_xml .= "\t\t<course_params><![CDATA[".$course->params."]]></course_params>\r\n";
			$export_xml .= "\t\t<hidden_menu_items></hidden_menu_items>\n";
			$export_xml .= "\t\t<certificates></certificates>\n";
			$export_xml .= "\t\t<scorms></scorms>\n";
			$export_xml .= "\t\t<zipped_documents></zipped_documents>\n";
			$export_xml .= "\t\t<learn_paths></learn_paths>\n";
			$export_xml .= "\t\t<gradebook_scale></gradebook_scale>\n";
		
			//GradeBook Items:
			$export_xml .= "\t\t<gradebook_items>\n";
			if (in_array('gradebook_items',$this->options)) {
				foreach ($gradebook as $gb_item){
					$export_xml .= "\t\t\t<gb_item gbi_option=\"".$gb_item->gbi_option."\" ordering=\"".$gb_item->ordering."\" >\n";
					$export_xml .= "\t\t\t\t<gbi_name><![CDATA[".$gb_item->gbi_name."]]></gbi_name>\r\n";
					$export_xml .= "\t\t\t\t<gbi_description><![CDATA[".$gb_item->gbi_description."]]></gbi_description>\r\n";
					$export_xml .= "\t\t\t\t<gb_category><![CDATA[]]></gb_category>\r\n";
					$export_xml .= "\t\t\t</gb_item>\n";
				}
			}
			$export_xml .= "\t\t</gradebook_items>\n";
			
			//links section
			$export_xml .= "\t\t<links>\n";
			if (in_array('links',$this->options)) {
				foreach ($links as $link){
					$export_xml .= "\t\t\t<link id=\"".$link->_id."\" link_type=\"".$link->link_type."\" ordering=\"".$link->ordering."\" published=\"".$link->published."\">\n";
					$export_xml .= "\t\t\t\t<linkname><![CDATA[".$link->link_name."]]></linkname>\r\n";
					$export_xml .= "\t\t\t\t<linkhref><![CDATA[".$link->link_href."]]></linkhref>\r\n";
					$export_xml .= "\t\t\t\t<description><![CDATA[".$link->link_description."]]></description>\r\n";
					$export_xml .= "\t\t\t</link>\n";
				}	
			}	
			$export_xml .= "\t\t</links>\n";
	
			//files section
			$export_xml .= "\t\t<files>\n";
			if (in_array('documents',$this->options)) {
				foreach ($files as $ff){
					$export_xml .= "\t\t\t<file id=\"".$ff->_id."\">\n";
					$export_xml .= "\t\t\t\t<filename><![CDATA[".$ff->file_name."]]></filename>\r\n";
					$export_xml .= "\t\t\t\t<servername><![CDATA[".$ff->file_srv_name."]]></servername>\r\n";
					$export_xml .= "\t\t\t</file>\n";
				}
			}
			$export_xml .= "\t\t</files>\n";
		
			//documents section
			$export_xml .= "\t\t<documents>\n";
			if (in_array('documents',$this->options)) {
				foreach ($documents1 as $document){
					$export_xml .= "\t\t\t<document id=\"".$document->id."\" file_id=\"".$document->file_id."\" folder_flag=\"".$document->folder_flag."\" parent_id=\"".$document->parent_id."\" ordering=\"".$document->ordering."\" published=\"".$document->published."\" publish_start=\"".$document->publish_start."\" start_date=\"".$document->start_date."\" publish_end=\"".$document->publish_end."\" end_date=\"".$document->end_date."\" >\r\n";
					$export_xml .= "\t\t\t\t<doc_name><![CDATA[".$document->doc_name."]]></doc_name>\n";
					$export_xml .= "\t\t\t\t<doc_description><![CDATA[".$document->doc_description."]]></doc_description>\n";
					$export_xml .= "\t\t\t</document>\n";
				}	
			}
			$export_xml .= "\t\t</documents>\n";
		
			$export_xml .= "\t\t<quizzes>\n";
			if (in_array('quizzes',$this->options)) {
				for ($i = 0, $n = count($quizzes); $i < $n; $i++){
					$quiz = $quizzes[$i];
					$question = $questions[$i];
					$answer = $answers[$i];
				
					$export_xml .= "\t\t\t<quiz c_id=\"".$quiz->_c_id."\" published=\"".$quiz->published."\" >\r\n";
					$export_xml .= "\t\t\t\t<quiz_title><![CDATA[".$quiz->c_title."]]></quiz_title>\r\n";
					$export_xml .= "\t\t\t\t<quiz_description><![CDATA[".$quiz->c_description."]]></quiz_description>\r\n";
					$export_xml .= "\t\t\t\t<quiz_category><![CDATA[".$quiz->c_category_id."]]></quiz_category>\r\n";
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
				
					$export_xml .= "\t\t\t\t<quiz_questions>\n";
					if (count($question)) {
						foreach ($question as $quest){
							$export_xml .= "\t\t\t\t\t<quiz_question c_id=\"".$quest->_c_id."\" c_point=\"".$quest->c_point."\" c_attempts=\"".$quest->c_attempts."\" c_type=\"".$quest->c_type."\" ordering=\"".$quest->ordering."\">\n";
							$export_xml .= "\t\t\t\t\t\t<question_text><![CDATA[".$quest->c_question."]]></question_text>\r\n";
							$export_xml .= "\t\t\t\t\t\t<question_image><![CDATA[".$quest->c_image."]]></question_image>\r\n";
							$export_xml .= "\t\t\t\t\t</quiz_question>\n";
						}
					}
					$export_xml .= "\t\t\t\t</quiz_questions>\n";
					
					$export_xml .= "\t\t\t\t<choice_data>\n";
					if (count($answer['choice_data'])) {
						foreach ($answer['choice_data'] as $qc_one) {
							$export_xml .= "\t\t\t\t\t<quest_choice c_question_id=\"".$qc_one->c_question_id."\" c_right=\"".$qc_one->c_right."\" ordering=\"".$qc_one->ordering."\">\r\n";
							$export_xml .= "\t\t\t\t\t\t<choice_text><![CDATA[".$qc_one->c_choice."]]></choice_text>\r\n";
							$export_xml .= "\t\t\t\t\t</quest_choice>\n";
						}
					}
					$export_xml .= "\t\t\t\t</choice_data>\n";
					
					$export_xml .= "\t\t\t\t<match_data>\n";
					if (count($answer['match_data'])) {
						foreach ($answer['match_data'] as $qm_one) {
							$export_xml .= "\t\t\t\t\t<quest_match c_question_id=\"".$qm_one->c_question_id."\" ordering=\"".$qm_one->ordering."\">\r\n";
							$export_xml .= "\t\t\t\t\t\t<match_text_left><![CDATA[".$qm_one->c_left_text."]]></match_text_left>\r\n";
							$export_xml .= "\t\t\t\t\t\t<match_text_right><![CDATA[".$qm_one->c_right_text."]]></match_text_right>\r\n";
							$export_xml .= "\t\t\t\t\t</quest_match>\n";
						}
					}
					$export_xml .= "\t\t\t\t</match_data>\n";
					
					$export_xml .= "\t\t\t\t<blank_data>\n";
					if (count($answer['blank_data'])) {
						foreach ($answer['blank_data'] as $qb_one) {
							$export_xml .= "\t\t\t\t\t<quest_blank c_question_id=\"".$qb_one['obj']->c_question_id."\" ordering=\"".$qb_one['obj']->_ordering."\">\r\n";
							foreach($qb_one['ans'] as $ans) {
								$export_xml .= "\t\t\t\t\t\t<blank_text><![CDATA[".$ans."]]></blank_text>\r\n";
							}
							$export_xml .= "\t\t\t\t\t</quest_blank>\n";
						}
					}
					$export_xml .= "\t\t\t\t</blank_data>\n";
					
					$export_xml .= "\t\t\t\t<hotspot_data>\n";
					if (count($answer['hotspot_data'])) {
						foreach ($answer['hotspot_data'] as $qh_one) {
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
				}
			}	
			$export_xml .= "\t\t</quizzes>\n";
			
			//end of course backup
			$export_xml .= "\t</course_backup>";
			//end xml file
			$filename_xml = $JLMS_CONFIG->get('absolute_path').'/media/export.xml';
			$handle = fopen($filename_xml, 'w');
	
			// try to write in XML file our xml-contents.
	    	if (fwrite($handle, $export_xml) === FALSE) {
				return	array('lmsfile'=>null, 'msg'=>"Could not create writable XML file");
		    }
			fclose($handle);
	
			$uniq = mktime();
			$dir = $lms_cfg_backup_folder."/";
			//chmod($dir, '777');
			$backup_zip = $dir.'course_backup_'.$course_id.'_'.$uniq.'.zip';
	
			$pz = new PclZip($backup_zip);
	
			//add _lms_course_files_ catalog
			$pz->create($filename_xml, '', $filename_xml = $JLMS_CONFIG->get('absolute_path').'/media/');
			
			if (in_array('documents',$this->options)) {
				foreach($files as $file){
					$filename = $file->_fromDir.$file->file_srv_name;
					$pz->add($filename,'files', $file->_fromDir);
				}	
			}
			if (in_array('quizzes',$this->options)) {
				foreach ($questions as $question) {
					foreach ($question as $quest){
						if (isset($quest->_c_image)) {
							$filename = $extract_dir.$quest->_identifier."/".$quest->_c_image;
							$pz->add($filename,'quiz_images', $extract_dir.$quest->_identifier."/".substr($quest->_c_image, 0, strpos($quest->_c_image, '\\'))."/");
						}
					}
				}
			}
		}
	
		// delete temporary files
		deldir($extract_dir);
		@unlink($archive);

		if (!$do_lms_archive)
			return	array('lmsfile'=>'none', 'msg'=>_JLMS_COURSE_IMPORT_SUCCESS);
			
		return	array('lmsfile'=>'course_backup_'.$course_id.'_'.$uniq.'.zip', 'msg'=>_JLMS_COURSE_IMPORT_SUCCESS);
	}
		
	function data(&$resourceElement) {
		$default = $resourceElement->data(); //'ISO-8859-1'
		//return (($this->charset_lang == 'UTF-8') ? utf8_decode ($default) : $default);
		$default = (($this->charset_lang != 'ISO-8859-1') ? htmlentities($default, ENT_QUOTES, $this->charset_lang) : $default);
		$default = str_replace('&lt;', '<', $default);
		$default = str_replace('&gt;', '>', $default);
		$default = str_replace('&amp;', '&', $default);	
		return $default;
	}

	function attributes(&$resourceElement, $attribute) {
		//echo $attribute;
		if (!is_object($resourceElement)) {
//var_dump($resourceElement);
//$rr = debug_backtrace();
//var_dump($rr);
		}
		$default = $resourceElement->attributes($attribute);
		//return (($this->charset_lang == 'UTF-8') ? utf8_decode ($default) : $default);
		$default = (($this->charset_lang != 'ISO-8859-1') ? htmlentities($default, ENT_QUOTES, $this->charset_lang) : $default);
		$default = str_replace('&lt;', '<', $default);
		$default = str_replace('&gt;', '>', $default);
		$default = str_replace('&amp;', '&', $default);	
		return $default; 		
	}
	
	function Process_Item(&$item, &$documents, &$resources, $parent_id = 0) {
		global $my;
	
		$m = $this->getResource($resources, $this->attributes($item, 'identifierref'));
		if ($m > 0 && (isset($resources[$m]->files) || $resources[$m]->resource_type == 'resource/x-bb-folder' || $resources[$m]->resource_type == 'resource/x-bb-document')) {
			$insert = new stdClass();
			$insert->course_id			= 1;//$course_id;
			$insert->owner_id			= $my->id;
			$insert->parent_id			= $parent_id;
			$insert->doc_description	= $resources[$m]->text;
			$insert->ordering			= 0;
			$insert->published			= 0;
			$insert->publish_start		= 0;
			$insert->start_date			= '0000-00-00';
			$insert->publish_end		= 0;
			$insert->end_date			= '0000-00-00';
			$insert->id 				= $resources[$m]->id;
			
			// "doc without attachment"
			if ( $resources[$m]->resource_type == 'resource/x-bb-document' ) {
				$insert->folder_flag = 0;	
				$insert->file_id 	 = 0;	
				$insert->doc_name	 = $resources[$m]->title;	
			}
			
			//file
			if ( isset($resources[$m]->files) ) {
				//echo $resources[$m]->id." ".$item->getAttribute('identifierref')."*";	
				$insert->folder_flag = 0;	
				$insert->file_id 	 = $resources[$m]->files[0]->id;	
				$insert->doc_name	 = isset($resources[$m]->files[0]->NAME) ? $resources[$m]->files[0]->NAME : ( isset($resources[$m]->files[0]->name) ? $resources[$m]->files[0]->name : '' ) ;
			}
			
			//folder
			if ( $resources[$m]->resource_type == 'resource/x-bb-folder' ) {
				//echo $resources[$m]->id." ".$item->getAttribute('identifierref')."*";
				$insert->folder_flag = 1;	
				$insert->file_id	 = 0;	
				$insert->doc_name 	 = $resources[$m]->title;
				$childCount = 0;
				if (isset($item->childCount)) {
					$childCount = $item->childCount;
				} elseif (isset($item->_children) && is_array($item->_children)) {
					$childCount = count($item->_children);
				}
				if ( $childCount > 1 ) {
					foreach($item->children() as $item_r) {
						if ($item_r->name() == 'item') {
							$this->Process_Item($item_r, $documents, $resources, $resources[$m]->id);
						}
					}
				}
			}
			if (isset($insert->doc_name)) {
				$insert->doc_name	 = trim($insert->doc_name);
			}
			$documents[] = $insert;
		}
		return true;
	}
	
	
	function getResource(&$resources, $identifier) {
		$i = 0;
		while ( $i < count($resources) ){
			if ($resources[$i]->identifier == $identifier)
				return $i;
			$i++;
		}
		return null;
	}
	
	function getQuestions(&$nodes, $q_id, $identifier) {
	
		$ret_arr = array();
		foreach($nodes as $node){
			if ($node->name() == 'item') {
				$ret_obj = new stdClass();
				$ret_obj->c_attempts = $this->attributes($node, 'maxattempts');
				$ret_obj->ordering = 0;
				$ret_obj->c_image = null;
				$element = $node->getElementByPath ('itemmetadata');
				$id = $element->getElementByPath ('bbmd_asi_object_id');
				$ret_obj->_c_id = $this->data($id);
				$ret_obj->c_quiz_id = $q_id;
				$point = $element->getElementByPath ('qmd_absolutescore_max');
				$ret_obj->c_point = abs($this->data($point)); // 17.12.2007 (DEN) - negative values bug - needed another solution - BB courses have negative points values !
				$type = $element->getElementByPath ('bbmd_questiontype');
				$type = $this->data($type);
				$assess_type = $element->getElementByPath ('bbmd_assessmenttype');
				$assess_type = $this->data($assess_type);
				
				if ($assess_type == 'Survey')// && $type == 'Fill in the Blank')
					$type = '';
					
				$element = $node->getElementByPath ('presentation');
				$element = $element->getElementByPath ('flow');
				$items = $element->children();
				foreach($items as $item) {
					if ($this->attributes($item, 'class') != 'QUESTION_BLOCK')
						continue;	
					$element = $item->getElementByPath ('flow');
					$element = $element->getElementByPath ('material');
					$element = $element->getElementByPath ('mat_extension');
					$element = $element->getElementByPath ('mat_formattedtext');
					$ret_obj->c_question = $this->data($element);
				}
				
				switch ($type) {
					
					case 'True/False':
						$ret_obj->c_image = null;
						$ret_obj->c_type = 3;
						break;
					case 'Essay':
					case 'Short Response':
						$ret_obj->c_image = null;
						$ret_obj->c_type = 8;
						break;
					case 'Fill in the Blank':
						$ret_obj->c_image = null;
						$ret_obj->c_type = 6;
						break;
					case 'Hot Spot':
						$element = $node->getElementByPath ('presentation');
						$element = $element->getElementByPath ('flow');
						$items = $element->children();
						foreach($items as $item) {
							if ($this->attributes($item, 'class') != 'RESPONSE_BLOCK')
								continue;
							$element = $item->getElementByPath ('flow');
							$element = $element->getElementByPath ('material');
							$element = $element->getElementByPath ('matapplication');
							break;
						}
						$ret_obj->_identifier = $identifier;
						$ret_obj->_c_image = $this->attributes($element, 'uri');
						$ret_obj->c_image = substr($ret_obj->_c_image, strpos($ret_obj->_c_image, '\\') + 1);
						$ret_obj->c_type = 7;
						break;
					case 'Matching':
						$ret_obj->c_image = null;
						$ret_obj->c_type = 4;
						break;
					case 'Multiple Answer':
						$ret_obj->c_image = null;
						$ret_obj->c_type = 2;
						break;
					case 'Either/Or':
					case 'Multiple Choice':
					//case 'Opinion Scale':
						$ret_obj->c_image = null;
						$ret_obj->c_type = 1;
						break;
					case 'Ordering':
						$ret_obj->c_image = null;
						$ret_obj->c_type = 5;
						break;				
					default: $ret_obj = null;continue;
				}
				if ($ret_obj)
					$ret_arr[] = $ret_obj;
			}
		}
		return $ret_arr;
	}
	
	function getAnswers(&$nodes, $q_id) {
		
		$ret_arr = array('choice_data' => array(), 'match_data' => array(), 'blank_data' => array(), 'hotspot_data' => array());
		foreach($nodes as $node){
			if ($node->name() == 'item') {
				$element = $node->getElementByPath ('itemmetadata');
				$id = $element->getElementByPath ('bbmd_asi_object_id');
				$assess_type = $element->getElementByPath ('bbmd_assessmenttype');
				//$fff = $element->getElementByPath ('bbmd_asi_object_id', 1);
				//$fff1 = $this->data($fff);
				//echo $fff1.'<br />';
				$assess_type = $this->data($assess_type);
				$question_id = $this->data($id);
				$element = $element->getElementByPath ('bbmd_questiontype');
				$type = $this->data($element);
					
				if ($assess_type == 'Survey' )// && $type == 'Fill in the Blank')
					$type = '';
					
				switch ($type) {				
					case 'True/False':
						$element = $node->getElementByPath ('resprocessing');
						$element1 = null;
						foreach($element->children() as $item){
							if ($item->name() != 'outcomes') {
								if (($this->attributes($item, 'title') && $this->attributes($item, 'title') != 'incorrect') || (!$this->attributes($item, 'title') && !$element1)) {
									$element1 = $item;
								}
							}
						}
						//echo $element1->name();
						//$element = $element->getElementByPath ('respcondition', 1);
						$element = $element1->getElementByPath ('conditionvar');
						$element = $element->getElementByPath ('varequal');
						$correct = $this->data($element);
						$tf_obj = new stdClass();
						$tf_obj->c_question_id = $question_id;
						$tf_obj->c_right = (strtolower($correct) == 'true' ? 1 : 0);
						$tf_obj->ordering = 0; 
						$tf_obj->c_choice = 'true';
						$ret_arr['choice_data'][] = $tf_obj;
						
						$tf_obj = new stdClass();
						$tf_obj->c_question_id = $question_id;
						$tf_obj->c_right = (strtolower($correct) == 'false' ? 1 : 0);
						$tf_obj->ordering = 0; 
						$tf_obj->c_choice = 'false';
						$ret_arr['choice_data'][] = $tf_obj;
						//print_r($ret_arr['choice_data']);
						break;
					case 'Essay':
					case 'Short Response':
						break;
					case 'Fill in the Blank':
						$element = $node->getElementByPath ('resprocessing');
						$correct = array();
						foreach($element->children() as $item){
							if ($this->attributes($item, 'title') && $this->attributes($item, 'title') != 'incorrect' && $item->name() != 'outcomes') {
								//$element = $item;
								$element = $item->getElementByPath ('conditionvar');
								$element = $element->getElementByPath ('varequal');
								$correct[] = $this->data($element);		
							}
						}
						//$element = $element->getElementByPath ('respcondition', 1);
						$fb_obj = new stdClass();
						$fb_obj->c_question_id = $question_id;
						$fb_obj->_ordering = 0; 		
						$ret_arr['blank_data'][] = array('obj' => $fb_obj, 'ans' => $correct);
						break;
					case 'Hot Spot':
						$element = $node->getElementByPath ('resprocessing');
						foreach($element->children() as $item){
							if ($this->attributes($item, 'title') && $this->attributes($item, 'title') != 'incorrect' && $item->name() != 'outcomes')
								$element = $item;
						}
						//$element = $element->getElementByPath ('respcondition', 1);
						$element = $element->getElementByPath ('conditionvar');
						$element = $element->getElementByPath ('varinside');
						$coordinates = $this->data($element);
						if ($coordinates) {
							$coords = explode(',', $coordinates);
							$hotspot_obj = new stdClass();
							$hotspot_obj->c_question_id = $question_id;
							$hotspot_obj->c_start_x =	abs($coords[0]);
							$hotspot_obj->c_start_y =	abs($coords[1]);
							$hotspot_obj->c_width = abs($coords[2] - $coords[0]);
							$hotspot_obj->c_height = abs($coords[3] - $coords[1]);
							$ret_arr['hotspot_data'][] = $hotspot_obj;
						}
						break;
					case 'Matching':
						$element = $node->getElementByPath ('presentation');
						$element = $element->getElementByPath ('flow');
						$items = $element->children();
						foreach($items as $item) {
							if ($this->attributes($item, 'class') == 'RESPONSE_BLOCK')
								$response_block = $item;
							if ($this->attributes($item, 'class') == 'RIGHT_MATCH_BLOCK')
								$right_match_block = $item;
						}
						
						$quest_text = array();
						$ans_ids = array();
						$quest_id = array();
						$ans_text = array();
						foreach($response_block->children() as $item) {
							$element = $item->getElementByPath ('flow');
							$element = $element->getElementByPath ('material');
							$element = $element->getElementByPath ('mat_extension');
							$element = $element->getElementByPath ('mat_formattedtext');
							$quest_text[] = $this->data($element);
							
							$element = $item->getElementByPath ('response_lid');
							$quest_id[] = $this->attributes($element, 'ident');
							
							$a_ids = array();
							$element = $element->getElementByPath ('render_choice');
							$element = $element->getElementByPath ('flow_label');
							foreach($element->children() as $a_id) {
								$a_ids[] = $this->attributes($a_id, 'ident');
							}
							$ans_ids[] = $a_ids;						
						}
						foreach($right_match_block->children() as $item) {
							$element = $item->getElementByPath ('flow');
							$element = $element->getElementByPath ('material');
							$element = $element->getElementByPath ('mat_extension');
							$element = $element->getElementByPath ('mat_formattedtext');
							$ans_text[] = $this->data($element);
						}
						//get right id
						$q_id = array();
						$a_id = array();
						$element = $node->getElementByPath ('resprocessing');
						foreach($element->children() as $item){
							if (($this->attributes($item, 'title') && $this->attributes($item, 'title') == 'incorrect') || $item->name() == 'outcomes')
								continue;
							$element = $item->getElementByPath ('conditionvar');
							$element = $element->getElementByPath ('varequal');
							
							$q_id[] = $this->attributes($element, 'respident');
							$a_id[] = $this->data($element);						
						}
						
						for($i = 0, $n = count($quest_id); $i < $n; $i++) {
							$mat_obj = new stdClass();
							$mat_obj->c_question_id = $question_id;
							$mat_obj->c_left_text = $quest_text[$i];						
							$text_ind = array_search($quest_id[$i], $q_id);
							$a_ind = array_search($a_id[$text_ind], $ans_ids[$i]);
							$mat_obj->c_right_text = $ans_text[$a_ind];
							$mat_obj->ordering = 0; 
							
							$ret_arr['match_data'][] = $mat_obj;					
						}
						break;
					case 'Multiple Answer':
						$element = $node->getElementByPath ('presentation');
						$element = $element->getElementByPath ('flow');
						$items = $element->children();
						foreach($items as $item) {
							if ($this->attributes($item, 'class') != 'RESPONSE_BLOCK')
								continue;
							$element = $item->getElementByPath ('response_lid');
							$element = $element->getElementByPath ('render_choice');
							break;
						}
						$items = $element->children();
						$ans_id = array();
						$ans_text = array();
						$ans_right = array();
						foreach($items as $item) {
							$element = $item->getElementByPath ('response_label');
							$ans_id[] = $this->attributes($element, 'ident');
							$element = $element->getElementByPath ('flow_mat');
							$element = $element->getElementByPath ('material');
							$element = $element->getElementByPath ('mat_extension');
							$element = $element->getElementByPath ('mat_formattedtext');
							$ans_text[] = $this->data($element);
							$ans_right[] = 0;
						}
						//get right answers
						$element = $node->getElementByPath ('resprocessing');
						foreach($element->children() as $item){
							if ( $this->attributes($item, 'title') && $this->attributes($item, 'title') != 'incorrect' && $item->name() != 'outcomes')
								$element = $item;
						}
						//$element = $element->getElementByPath ('respcondition', 1);
						$element = $element->getElementByPath ('conditionvar');
						$element = $element->getElementByPath ('and');
						foreach($element->children() as $item){
							if ($item->name() == 'varequal') {
								$right_id = $this->data($item);
								$ans_right[array_search($right_id, $ans_id)] = 1;
							}															
						}					
						
						for($i = 0, $n = count($ans_id); $i < $n; $i++) {
							$ma_obj = new stdClass();
							$ma_obj->c_question_id = $question_id;
							$ma_obj->c_right = $ans_right[$i];
							$ma_obj->ordering = 0; 
							$ma_obj->c_choice = $ans_text[$i];
							
							$ret_arr['choice_data'][] = $ma_obj;					
						}
						break;
					case 'Either/Or':
						$element = $node->getElementByPath ('resprocessing');
						foreach($element->children() as $item){
							if ($this->attributes($item, 'title') && $this->attributes($item, 'title') != 'incorrect' && $item->name() != 'outcomes')
								$element = $item;
						}
						//$element = $element->getElementByPath ('respcondition', 1);
						$element = $element->getElementByPath ('conditionvar');
						$element = $element->getElementByPath ('varequal');
						$correct = $this->data($element);
						
						$q_type = substr($correct, 0, strpos($correct, '.'));
						
						switch ($q_type) {
							case 'yes_no':
								$ans1 = 'Yes';
								$ans2 = 'No';
								break;
							case 'right_wrong':
								$ans1 = 'Right';
								$ans2 = 'Wrong';
								break;
							case 'true_false':
								$ans1 = 'True';
								$ans2 = 'False';
								break;
							case 'agree_disagree':
								$ans1 = 'Agree';
								$ans2 = 'Disagree';
								break;
						}	
						
						$eo_obj = new stdClass();
						$eo_obj->c_question_id = $question_id;
						$eo_obj->c_right = ($correct == $q_type.'.true' ? 1 : 0);
						$eo_obj->ordering = 0; 
						$eo_obj->c_choice = $ans1;
						$ret_arr['choice_data'][] = $eo_obj;
						
						$eo_obj = new stdClass();
						$eo_obj->c_question_id = $question_id;
						$eo_obj->c_right = ($correct == $q_type.'.false' ? 1 : 0);
						$eo_obj->ordering = 0; 
						$eo_obj->c_choice = $ans2;
						$ret_arr['choice_data'][] = $eo_obj;
						break;
					case 'Multiple Choice':
					//case 'Opinion Scale':
						$element = $node->getElementByPath ('presentation');
						$element = $element->getElementByPath ('flow');
						$items = $element->children();
						foreach($items as $item) {
							if ($this->attributes($item, 'class') != 'RESPONSE_BLOCK')
								continue;
							$element = $item->getElementByPath ('response_lid');
							$element = $element->getElementByPath ('render_choice');
							break;
						}
						$items = $element->children();
						$ans_id = array();
						$ans_text = array();
						foreach($items as $item) {
							$element = $item->getElementByPath ('response_label');
							$ans_id[] = $this->attributes($element, 'ident');
							$element = $element->getElementByPath ('flow_mat');
							$element = $element->getElementByPath ('material');
							$element = $element->getElementByPath ('mat_extension');
							$element = $element->getElementByPath ('mat_formattedtext');
							$ans_text[] = $this->data($element);
						}
						$element = $node->getElementByPath ('resprocessing');
						foreach($element->children() as $item){
							if ($this->attributes($item, 'title') && $this->attributes($item, 'title') != 'incorrect' && $item->name() != 'outcomes')
								$element = $item;
						}
						//$element = $element->getElementByPath ('respcondition', 1);
						$element = $element->getElementByPath ('conditionvar');
						$element = $element->getElementByPath ('varequal');
						$correct_id = $this->data($element);
						for($i = 0, $n = count($ans_id); $i < $n; $i++) {
							$mc_obj = new stdClass();
							$mc_obj->c_question_id = $question_id;
							$mc_obj->c_right = ($ans_id[$i] == $correct_id ? 1 : 0);
							$mc_obj->ordering = 0; 
							$mc_obj->c_choice = $ans_text[$i];
							
							$ret_arr['choice_data'][] = $mc_obj;					
						}
						break;
					case 'Ordering':
						$element = $node->getElementByPath ('presentation');
						$element = $element->getElementByPath ('flow');
						$items = $element->children();
						foreach($items as $item) {
							if ($this->attributes($item, 'class') != 'RESPONSE_BLOCK')
								continue;
							$element = $item->getElementByPath ('response_lid');
							$element = $element->getElementByPath ('render_choice');
							break;
						}
						$items = $element->children();
						$ans_id = array();
						$ans_text = array();
						$ans_order = array();
						foreach($items as $item) {
							$element = $item->getElementByPath ('response_label');
							$ans_id[] = $this->attributes($element, 'ident');
							$element = $element->getElementByPath ('flow_mat');
							$element = $element->getElementByPath ('material');
							$element = $element->getElementByPath ('mat_extension');
							$element = $element->getElementByPath ('mat_formattedtext');
							$ans_text[] = $this->data($element);
						}
						//get order
						$element = $node->getElementByPath ('resprocessing');
						foreach($element->children() as $item){
							if ($this->attributes($item, 'title') && $this->attributes($item, 'title') != 'incorrect' && $item->name() != 'outcomes')
								$element = $item;
						}
						//$element = $element->getElementByPath ('respcondition', 1);
						$element = $element->getElementByPath ('conditionvar');
						$element = $element->getElementByPath ('and');
						
						foreach($element->children() as $item){
							if ($item->name() == 'varequal') {
								$ans_order[] = $this->data($item);
							}
						}
						
						for($i = 0, $n = count($ans_order); $i < $n; $i++) {
							$ord_obj = new stdClass();
							$ord_obj->c_question_id = $question_id;
							$ord_obj->c_left_text = strval( $i + 1 );
							$text_ind = array_search($ans_order[$i], $ans_id);
							$ord_obj->c_right_text = $ans_text[$text_ind];
							$ord_obj->ordering = 0; 
							
							$ret_arr['match_data'][] = $ord_obj;					
						}
						break;				
					//default: $ret_obj = null;continue;
				}			
			}
		}
		return $ret_arr;
	}
}
?>