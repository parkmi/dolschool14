<?php
/**
* lms.lib.sef.php
* JoomlaLMS Component
**/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

define( 'JLMS_SEF_ID_SEP', '-' );

require_once( JPATH_SITE.DS.'components'.DS.'com_joomla_lms'.DS.'includes'.DS.'classes'.DS.'lms.titles.php' );
require_once( JPATH_SITE.DS.'components'.DS.'com_joomla_lms'.DS.'includes'.DS.'libraries'.DS.'lms.lib.sef.config.php' );

class JLMS_SEF {
	var $_db;
	
	var $_data;	
	
	var $_nonSefVars;
	
	var $_res;
	
	var $_menuTitle;
		
	var $_isRouterPHP;
		
	var $_positions;
	
	var $_tObj;  
		
	function JLMS_SEF( &$uri ) 
	{		
		$this->_db = JFactory::getDBO();
		$this->_tObj = JLMSTitles::getInstance();
		$this->_cfg = JLMS_SEF_CONFIG::getInstance();			
		
		if( is_object($uri) )
			$this->_data =$uri->getQuery(true);	
		/*
		if( $this->getVar( 'tmpl', 'word' ) == 'component' ) 
		{	
			JLMS_SEF_VarsPositions::isTmplComponent( true );
		} 
		*/												
	}
	
  /**
   * JLMS_SEF::calledFromRouterPHP()
   * It is called only from a router.php 	
   * @return void
   */
   
	function calledFromRouterPHP()
	{		
		$this->_isRouterPHP = true; 
	} 
		
	function stringURLSafe( $title ) 
	{ 	
		if( $this->_isRouterPHP )						
			$title = $this->_titleToLocation( $title );		
				
		return $title;
	}
	
	function _titleToLocation(&$title)
    {
		// remove accented characters        
        $title = strtr($title, $this->getReplacements());
        
        // remove quotes, spaces, and other illegal characters        
        $title = preg_replace(array('/\'/', '/[^a-zA-Z0-9\,!+]+/', '/(^_|_$)/'), array('', $this->_cfg->get('replacement'), ''), $title);                
                
        $title = rtrim( $title, '-' );
        $title = rtrim( $title, '.' );        
        
        // Handling lower case
        $title = strtolower($title); 
		     

        return $title;
    }   
	
	function getReplacements()
    {
        static $replacements;
        
        if( isset($replacements) ) {
            return $replacements;
        }
        
        $replacements = array();
               
        $str = trim( $this->_cfg->get('replacements') );
		        
        if( $str != '' ) {
            $items = explode(',', $str);
            foreach ($items as $item) {
                @list ($src, $dst) = explode('|', trim($item));
                
                // $dst can be empty, so the character can be removed
                if( trim($src) == '' ) {
                    continue;
                }
                
                $replacements[trim($src)] = trim($dst);
            }
        }
                
        return $replacements;
    }
	
	function keySefUrlPart( $id, $title ) 
	{		
		$safeTitle = $this->stringURLSafe( $title );		
		$fId = '';		
		
		if( $id && $this->_isRouterPHP ) 
		{
			if( $safeTitle )
				$fId = $id.JLMS_SEF_ID_SEP;
			else
				$fId = $id;
		}		
		
		return $fId.$safeTitle;	
	}
		
	function setData( $data ) 
	{		
		$this->_data = $data;		
	}		
	
	function getVar( $name, $type = 'id', $default = '' ) 
	{	
		//check if name like cid[0]	
		if( preg_match( "/([a-zA-Z0-9_]+)\[([0-9]+)\]/", $name, $matches ) ) 
		{
			$name = $matches[1];
			$index = $matches[2];
		}				
		
		$filter = JFilterInput::getInstance();
															
		if( isset( $this->_data[$name] ) ) 
		{
			switch ( $type ) 
			{				
				case 'id':
				case 'int':	
					if( isset( $index ) )										
						$res = $filter->clean( $this->_data[$name][$index], 'int' );						
					else
						$res = $filter->clean( $this->_data[$name], 'int' );
				break;
				case 'word': 
					if( isset( $index ) )										
						$res = $filter->clean( $this->_data[$name][$index], 'word' );
					else
						$res = $filter->clean( $this->_data[$name], 'word' );									
				break;
				case 'string': 
					if( isset( $index ) )										
						$res = $filter->clean( $this->_data[$name][$index], 'string' );
					else
						$res = $filter->clean( $this->_data[$name], 'string' );									
				break;
				default:
					$res = $default;				
			}
		} else {
			switch ( $type ) 
			{
				case 'id':
				case 'int':									
					$res = 0;				 
				break;
				case 'string':									
					$res = '';				 
				break;				
				default:
					$res = $default; 
			}	
		}	
			
		return $res;	
	}
	
	function hw_downloadfile_build() 
	{	
		$course_name= $this->_tObj->get( 'courses', $this->getVar('course_id') );
		$hw_name 	= $this->_tObj->get( 'homeworks', $this->getVar('hw_id') );
		$user_name	= $this->_tObj->get( 'users', $this->getVar('user_id') );
		$file_name	= $this->_tObj->get( 'files', $this->getVar('file_id') );		
		
		$this->_res[] = $this->keySefUrlPart( $this->getVar('course_id'), $course_name );	
		$this->_res[] = 'homework';
		$this->_res[] = $this->keySefUrlPart( $this->getVar('hw_id'), $hw_name );
		$this->_res[] = 'files';
		$this->_res[] = $this->keySefUrlPart( $this->getVar('user_id'), $user_name );
		$this->_res[] = $this->keySefUrlPart( $this->getVar('file_id'), $file_name );									
	}
	
	function hw_downloadfile_init() 
	{	
		$pos = new JLMS_SEF_VarsPositions( 'hw_downloadfile' );
		$pos->addVar( 'course_id', '', '', 'id' );	
		$pos->addVar( '', 'homework' );
		$pos->addVar( 'hw_id', '', '', 'id' );
		$pos->addVar( '', 'files' );																 	
		$pos->addVar( 'user_id', '', '', 'id' );
		$pos->addVar( 'file_id', '', '', 'id' );	
		$this->_positions[] = $pos;
	}
		
	function new_notice_build() 
	{	
		$course_name = $this->_tObj->get( 'courses', $this->getVar('course_id') );		
		
		$this->_res[] = $this->keySefUrlPart( $this->getVar('course_id'), $course_name );	
		$this->_res[] = 'new_notice';	
		
		$this->_nonSefVars[] = 'doc_id';
		$this->_nonSefVars[] = 'ntask';														
	}
	
	function new_notice_init() 
	{	
		$pos = new JLMS_SEF_VarsPositions( 'new_notice' );
		$pos->addVar( 'course_id', '', '', 'id' );	
		$pos->addVar( '', 'new_notice' );																 		
		$this->_positions[] = $pos;
	}
	
	function get_notice_count_build() 
	{	
		$course_name = $this->_tObj->get( 'courses', $this->getVar('course_id') );		
		
		$this->_res[] = $this->keySefUrlPart( $this->getVar('course_id'), $course_name );	
		$this->_res[] = 'get_notice_count';	
		
		$this->_nonSefVars[] = 'doc_id';
		$this->_nonSefVars[] = 'ntask';														
	}
	
	function get_notice_count_init() 
	{	
		$pos = new JLMS_SEF_VarsPositions( 'get_notice_count' );
		$pos->addVar( 'course_id', '', '', 'id' );	
		$pos->addVar( '', 'get_notice_count' );																 		
		$this->_positions[] = $pos;
	}
		
	function save_notice_build() 
	{	
		$course_name = $this->_tObj->get( 'courses', $this->getVar('course_id') );		
		
		$this->_res[] = $this->keySefUrlPart( $this->getVar('course_id'), $course_name );	
		$this->_res[] = 'save_notice';	
		
		$this->_nonSefVars[] = 'doc_id';
		$this->_nonSefVars[] = 'ntask';
		$this->_nonSefVars[] = 'v_id';												
	}
	
	function save_notice_init() 
	{	
		$pos = new JLMS_SEF_VarsPositions( 'save_notice' );
		$pos->addVar( 'course_id', '', '', 'id' );	
		$pos->addVar( '', 'save_notice' );																 		
		$this->_positions[] = $pos;
	}
		
	function datamodel_scorm_build() 
	{	
		$course_name = $this->_tObj->get( 'courses', $this->getVar('course_id') );		
		
		$this->_res[] = $this->keySefUrlPart( $this->getVar('course_id'), $course_name );	
		$this->_res[] = 'datamodel_scorm';	
		
		$this->_nonSefVars[] = 'skip_resume';
		$this->_nonSefVars[] = 'ssid';										
	}	
	
	function datamodel_scorm_init() 
	{	
		$pos = new JLMS_SEF_VarsPositions( 'datamodel_scorm' );
		$pos->addVar( 'course_id', '', '', 'id' );	
		$pos->addVar( '', 'datamodel_scorm' );																 		
		$this->_positions[] = $pos;
	}
		
	function api_scorm_build() 
	{	
		$course_name = $this->_tObj->get( 'courses', $this->getVar('course_id') );		
		
		$this->_res[] = $this->keySefUrlPart( $this->getVar('course_id'), $course_name );	
		$this->_res[] = 'api_scorm';	
		
		$this->_nonSefVars[] = 'skip_resume';
		$this->_nonSefVars[] = 'id';								
	}
	
	function api_scorm_init() 
	{	
		$pos = new JLMS_SEF_VarsPositions( 'api_scorm' );
		$pos->addVar( 'course_id', '', '', 'id' );	
		$pos->addVar( '', 'api_scorm' );																 		
		$this->_positions[] = $pos;
	}
		
	function keep_a_live_build() 
	{	
		$this->_res[] = 'keep_a_live';									
	}
	
	function keep_a_live_init() 
	{
		$pos = new JLMS_SEF_VarsPositions( 'keep_a_live' );	
		$pos->addVar( '', 'keep_a_live' );																 		
		$this->_positions[] = $pos;
	}
	
	function setup_quest_build() 
	{	
		$this->_res[] = 'setup_quest';									
	}
	
	function setup_quest_init() 
	{
		$pos = new JLMS_SEF_VarsPositions( 'setup_quest' );	
		$pos->addVar( '', 'setup_quest' );																 		
		$this->_positions[] = $pos;
	}
		
	function history_build() 
	{	
		$this->_res[] = 'history';									
	}
	
	function history_init() 
	{
		$pos = new JLMS_SEF_VarsPositions( 'history' );
	
		$pos->addVar( '', 'history' );				
												 		
		$this->_positions[] = $pos;
	}
	
	function export_lang_xml_build() 
	{	
		$this->_res[] = 'export_lang_xml';		
				
		$this->_nonSefVars[] = 'lang_id';								
	}
	
	function export_lang_xml_init() 
	{
		$pos = new JLMS_SEF_VarsPositions( 'export_lang_xml' );
	
		$pos->addVar( '', 'export_lang_xml' );				
												 		
		$this->_positions[] = $pos;
	}
	
	function mail_iframe_build() 
	{	
		$this->_res[] = 'mail_iframe';		
				
		$this->_nonSefVars[] = 'assigned';
		$this->_nonSefVars[] = 'redirect';						
	}
	
	function mail_iframe_init() 
	{
		$pos = new JLMS_SEF_VarsPositions( 'mail_iframe' );
	
		$pos->addVar( '', 'mail_iframe' );				
												 		
		$this->_positions[] = $pos;
	}
	
	function scorm_closesco_build() 
	{
		$course_name = $this->_tObj->get( 'courses', $this->getVar('course_id') );					
		
		$this->_res[] = $this->keySefUrlPart( $this->getVar('course_id'), $course_name );													
		$this->_res[] = 'scorm_contents';		
				
		$this->_nonSefVars[] = 'id';
		$this->_nonSefVars[] = 'href_item';
		$this->_nonSefVars[] = 'sco_identifier';
	}
	
	function scorm_closesco_init() 
	{
		$pos = new JLMS_SEF_VarsPositions( 'scorm_closesco' );	
		
		$pos->addVar( 'course_id', '', '', 'id' );			
		$pos->addVar( '', 'scorm_closesco' );				
												 		
		$this->_positions[] = $pos;
	}
	
	function scorm_opensco_build() 
	{
		$course_name = $this->_tObj->get( 'courses', $this->getVar('course_id') );					
		
		$this->_res[] = $this->keySefUrlPart( $this->getVar('course_id'), $course_name );													
		$this->_res[] = 'scorm_contents';		
				
		$this->_nonSefVars[] = 'id';
		$this->_nonSefVars[] = 'href_item';
		$this->_nonSefVars[] = 'sco_identifier';
	}
	
	function scorm_opensco_init() 
	{
		$pos = new JLMS_SEF_VarsPositions( 'scorm_opensco' );	
		
		$pos->addVar( 'course_id', '', '', 'id' );			
		$pos->addVar( '', 'scorm_opensco' );				
												 		
		$this->_positions[] = $pos;
	}
	
	function scorm_contents_build() 
	{
		$course_name = $this->_tObj->get( 'courses', $this->getVar('course_id') );					
		
		$this->_res[] = $this->keySefUrlPart( $this->getVar('course_id'), $course_name );													
		$this->_res[] = 'scorm_contents';		
				
		$this->_nonSefVars[] = 'id';
		$this->_nonSefVars[] = 'time';						
	}
	
	function scorm_contents_init() 
	{
		$pos = new JLMS_SEF_VarsPositions( 'scorm_contents' );	
		
		$pos->addVar( 'course_id', '', '', 'id' );			
		$pos->addVar( '', 'scorm_contents' );				
												 		
		$this->_positions[] = $pos;
	}
	
	function scorm_funcs_build() 
	{
		$course_name = $this->_tObj->get( 'courses', $this->getVar('course_id') );					
		
		$this->_res[] = $this->keySefUrlPart( $this->getVar('course_id'), $course_name );													
		$this->_res[] = 'scorm_funcs';		
				
		$this->_nonSefVars[] = 'id';						
	}
	
	function scorm_funcs_init() 
	{
		$pos = new JLMS_SEF_VarsPositions( 'scorm_funcs' );	
		
		$pos->addVar( 'course_id', '', '', 'id' );			
		$pos->addVar( '', 'scorm_funcs' );				
												 		
		$this->_positions[] = $pos;
	}
		
	function quiz_ajax_action_build() 
	{
		$course_name = $this->_tObj->get( 'courses', $this->getVar('id') );					
		
		$this->_res[] = $this->keySefUrlPart( $this->getVar('id'), $course_name );													
		$this->_res[] = 'quizajaxaction';		
				
		$this->_nonSefVars[] = 'inside_lp';
		$this->_nonSefVars[] = 'jlms';				
	}
	
	function quiz_ajax_action_init() 
	{
		$pos = new JLMS_SEF_VarsPositions( 'quiz_ajax_action' );	
		
		$pos->addVar( 'id', '', '', 'id' );			
		$pos->addVar( '', 'quizajaxaction' );				
												 		
		$this->_positions[] = $pos;
	}
		
	function mail_main_build() 
	{									
		$this->_res[] = 'mailmain';		
				
		$this->_nonSefVars[] = 'assigned';				
	}
	
	function mail_main_init() 
	{
		$pos = new JLMS_SEF_VarsPositions( 'mail_main' );	
					
		$pos->addVar( '', 'mailmain' );				
												 		
		$this->_positions[] = $pos;
	}
		
	function print_quiz_cert_build() 
	{
		$course_name = $this->_tObj->get( 'courses', $this->getVar('course_id') );						
		
		$this->_res[] = $this->keySefUrlPart( $this->getVar('course_id'), $course_name );								
		$this->_res[] = 'printquizcert';		
				
		$this->_nonSefVars[] = 'user_unique_id';
		$this->_nonSefVars[] = 'stu_quiz_id';		
	}
	
	function print_quiz_cert_init() 
	{
		$pos = new JLMS_SEF_VarsPositions( 'print_quiz_cert' );	
		
		$pos->addVar( 'course_id', '', '', 'id' );	
		$pos->addVar( '', 'printquizcert' );				
												 		
		$this->_positions[] = $pos;
	}
	
	function delete_notice_build() 
	{
		$course_name = $this->_tObj->get( 'courses', $this->getVar('course_id') );		
		
		$this->_res[] = $this->keySefUrlPart( $this->getVar('course_id'), $course_name );				
		$this->_res[] = 'deletenotice';		
		
		$this->_nonSefVars[] = 'ntask';
		$this->_nonSefVars[] = 'doc_id';
	}
	
	function delete_notice_init() 
	{
		$pos = new JLMS_SEF_VarsPositions( 'delete_notice' );	
		
		$pos->addVar( 'course_id', '', '', 'id' );	
		$pos->addVar( '', 'deletenotice' );				
												 		
		$this->_positions[] = $pos;
	}
	
	function edit_notice_build() 
	{
		$course_name = $this->_tObj->get( 'courses', $this->getVar('course_id') );		
		
		$this->_res[] = $this->keySefUrlPart( $this->getVar('course_id'), $course_name );				
		$this->_res[] = 'editnotice';		
		
		$this->_nonSefVars[] = 'ntask';
		$this->_nonSefVars[] = 'doc_id';
	}
	
	function edit_notice_init() 
	{
		$pos = new JLMS_SEF_VarsPositions( 'edit_notice' );	
		
		$pos->addVar( 'course_id', '', '', 'id' );	
		$pos->addVar( '', 'editnotice' );				
												 		
		$this->_positions[] = $pos;
	}
	
	function crt_preview_build() 
	{	
		$course_name = $this->_tObj->get( 'courses', $this->getVar('id') );		
		
		$this->_res[] = $this->keySefUrlPart( $this->getVar('id'), $course_name );				
		$this->_res[] = 'crtpreview';		
		
		$this->_nonSefVars[] = 'crtf_id';
		$this->_nonSefVars[] = 'crtf_role';
		$this->_nonSefVars[] = 'no_html';							
	}
	
	function crt_preview_init() 
	{
		$pos = new JLMS_SEF_VarsPositions( 'crt_preview' );	
		
		$pos->addVar( 'id', '', '', 'id' );	
		$pos->addVar( '', 'crtpreview' );				
												 		
		$this->_positions[] = $pos;		
	}
	
	function mfile_load_build() 
	{	
		$course_name = $this->_tObj->get( 'courses', $this->getVar('id') );
		$subject = $this->_tObj->get( 'messages', $this->getVar('view_id') );
										
		$this->_res[] = $this->keySefUrlPart( $this->getVar('id'), $course_name );		
		$this->_res[] = 'mailbox';
		$this->_res[] = $this->keySefUrlPart( $this->getVar('view_id'), $subject );		
		$this->_res[] = 'attachment';							
	}
	
	function mfile_load_init() 
	{
		$pos = new JLMS_SEF_VarsPositions( 'mfile_load' );	
		
		$pos->addVar( 'id', '', '', 'id' );	
		$pos->addVar( '', 'mailbox' );
		$pos->addVar( 'view_id', '', '', 'id' );
		$pos->addVar( '', 'attachment' );		
												 		
		$this->_positions[] = $pos;		
	}
	
	
	function hw_view_result_build() 
	{	
		$course_name = $this->_tObj->get( 'courses', $this->getVar('course_id') );
		$hw_name = $this->_tObj->get( 'homework', $this->getVar('hw_id') );
		$user_name = $this->_tObj->get( 'users', $this->getVar('user_id') );
												
		$this->_res[] = $this->keySefUrlPart( $this->getVar('course_id'), $course_name );		
		$this->_res[] = 'homework';
		$this->_res[] = $this->keySefUrlPart( $this->getVar('hw_id'), $hw_name );
		$this->_res[] = 'results';
		$this->_res[] = $this->keySefUrlPart( $this->getVar('user_id'), $user_name );					
	}
	
	function hw_view_result_init() 
	{			
		$pos = new JLMS_SEF_VarsPositions( 'hw_view_result' );	
		
		$pos->addVar( 'course_id', '', '', 'id' );	
		$pos->addVar( '', 'homework' );
		$pos->addVar( 'hw_id', '', '', 'id' );
		$pos->addVar( '', 'results' );
		$pos->addVar( 'user_id', '', '', 'id' );								 		
		$this->_positions[] = $pos;  		 
	}
	
	function gb_user_pdf_build() 
	{	
		$course_name = $this->_tObj->get( 'courses', $this->getVar('course_id') );								
		$this->_res[] = $this->keySefUrlPart( $this->getVar('course_id'), $course_name );		
		$this->_res[] = 'gradebook';
		$this->_res[] = $this->keySefUrlPart( $this->getVar('id'), 'grade' );
		$this->_res[] = 'pdf';			
	}
	
	function gb_user_pdf_init() 
	{			
		$pos = new JLMS_SEF_VarsPositions( 'gb_user_pdf' );	
		
		$pos->addVar( 'course_id', '', '', 'id' );	
		$pos->addVar( '', 'gradebook' );
		$pos->addVar( 'id', '', '', 'id' );
		$pos->addVar( '', 'pdf' );								 		
		$this->_positions[] = $pos;  		 
	} 
		
	function course_import_build() 
	{			
		$this->_res[] = 'courses';
		$this->_res[] = 'import';	
	}
	
	function course_import_init() 
	{			
		$pos = new JLMS_SEF_VarsPositions( 'course_import' );					
		$pos->addVar( '', 'courses' );
		$pos->addVar( '', 'import' );								 		
		$this->_positions[] = $pos;  		 
	}
		
	function view_inline_link_build() 
	{	
		$course_name = $this->_tObj->get( 'courses', $this->getVar('course_id') );
		$link_name = $this->_tObj->get( 'links', $this->getVar('id') );
										
		$this->_res[] = $this->keySefUrlPart( $this->getVar('course_id'), $course_name );	
		$this->_res[] = 'links';									
		$this->_res[] = $this->keySefUrlPart( $this->getVar('id'), $link_name );
	}
	
	function view_inline_link_init() 
	{			
		$pos = new JLMS_SEF_VarsPositions( 'view_inline_link' );	
		$pos->addVar( 'course_id', '', '', 'id' );				
		$pos->addVar( '', 'links' );			
		$pos->addVar( 'id', '', '', 'id' );						 		
		$this->_positions[] = $pos;  		 
	}
	
	function new_lpath_scorm_build() 
	{	
		$course_name = $this->_tObj->get( 'courses', $this->getVar('id') );								
		$this->_res[] = $this->keySefUrlPart( $this->getVar('id'), $course_name );			
		$this->_res[] = 'learn_paths';
		$this->_res[] = 'new_lpath_scorm';	  		 
	}
	
	function new_lpath_scorm_init() 
	{			
		$pos = new JLMS_SEF_VarsPositions( 'new_lpath_scorm' );	
		$pos->addVar( 'id', '', '', 'id' );		
		$pos->addVar( '', 'learn_paths' );		
		$pos->addVar( '', 'new_lpath_scorm' );									 		
		$this->_positions[] = $pos;  		 
	}
	
	function new_scorm_from_library_build() 
	{	
		$course_name = $this->_tObj->get( 'courses', $this->getVar('id') );								
		$this->_res[] = $this->keySefUrlPart( $this->getVar('id'), $course_name );
		$this->_res[] = 'learn_paths';		
		$this->_res[] = 'add_scorm_from_library';	  		 
	}
	
	function new_scorm_from_library_init() 
	{			
		$pos = new JLMS_SEF_VarsPositions( 'new_scorm_from_library' );	
		$pos->addVar( 'id', '', '', 'id' );	
		$pos->addVar( '', 'learn_paths' );					
		$pos->addVar( '', 'add_scorm_from_library' );									 		
		$this->_positions[] = $pos;  		 
	}
	
	function new_scorm_build() 
	{	
		$this->_res[] = 'library';		
		$this->_res[] = 'upload_scorm';	  		 
	}
	
	function new_scorm_init() 
	{			
		$pos = new JLMS_SEF_VarsPositions( 'new_scorm' );			
		$pos->addVar( '', 'library' );					
		$pos->addVar( '', 'upload_scorm' );									 		
		$this->_positions[] = $pos;  		 
	}
	
	function report_access_build() 
	{	
		$this->_res[] = 'reports';	
		$this->_res[] = 'access_report';						  		 
	}
	
	function report_access_init() 
	{			
		$pos = new JLMS_SEF_VarsPositions( 'report_access' );
		$pos->addVar( '', 'reports' );					
		$pos->addVar( '', 'access_report' );									 		
		$this->_positions[] = $pos;  		 
	}
	
	function report_certif_build() 
	{	
		$this->_res[] = 'reports';	
		$this->_res[] = 'completion_report';	  		 
	}
	
	function report_certif_init() 
	{			
		$pos = new JLMS_SEF_VarsPositions( 'report_certif' );
		$pos->addVar( '', 'reports' );					
		$pos->addVar( '', 'completion_report' );									 		
		$this->_positions[] = $pos;  		 
	}
	
	function report_grade_build() 
	{	
		$this->_res[] = 'reports';	
		$this->_res[] = 'grades_report';
		$this->_nonSefVars[] = 'filt_user';			
	}
	
	function report_grade_init() 
	{			
		$pos = new JLMS_SEF_VarsPositions( 'report_grade' );
		$pos->addVar( '', 'reports' );					
		$pos->addVar( '', 'grades_report' );				 		
		$this->_positions[] = $pos;  		 
	}
	
	function report_scorm_build() 
	{	
		$this->_res[] = 'reports';	
		$this->_res[] = 'scorm_report';				 
	}
	
	function report_scorm_init() 
	{			
		$pos = new JLMS_SEF_VarsPositions( 'report_scorm' );
		$pos->addVar( '', 'reports' );					
		$pos->addVar( '', 'scorm_report' );									 		
		$this->_positions[] = $pos;  		 
	}
								
	function player_scorm_build() 
	{	
		$course_name = $this->_tObj->get( 'courses', $this->getVar('course_id') );		
		$lpath_name = $this->_tObj->get( 'learn_paths', $this->getVar('lpath_id') );
								
		$this->_res[] = $this->keySefUrlPart( $this->getVar('course_id'), $course_name );				
		$this->_res[] = $this->keySefUrlPart( $this->getVar('lpath_id'), $lpath_name );
		$this->_res[] = 'scorms';
		$this->_res[] = $this->keySefUrlPart( $this->getVar('id'), 'scorm' );
		
		$this->_nonSefVars[] = 'height';
		$this->_nonSefVars[] = 'int_skip_resume';
		$this->_nonSefVars[] = 'doc_id';
		$this->_nonSefVars[] = 'scoid';
		$this->_nonSefVars[] = 'currentorg';
		$this->_nonSefVars[] = 'newattempt';
		$this->_nonSefVars[] = 'mode';
		$this->_nonSefVars[] = 'srw';						  		 
	}
	
	function player_scorm_init() 
	{			
		$pos = new JLMS_SEF_VarsPositions( 'player_scorm' );
					
		$pos->addVar( 'course_id', '', '', 'id' );		
		$pos->addVar( 'lpath_id', '', '', 'id'  );
		$pos->addVar( '', 'scorms'  );
		$pos->addVar( 'id', '', '', 'id'  );							 		
		$this->_positions[] = $pos;  		 
	}
	
	function playerSCORMFiles_build() 
	{	
		$doc_name = $this->_tObj->get( 'outer_documents', $this->getVar('doc_id'));
			
		$this->_res[] = 'library';				
		$this->_res[] = $this->keySefUrlPart( $this->getVar('doc_id'), $doc_name );
		$this->_res[] = $this->keySefUrlPart( $this->getVar('id'), 'out' );	
		
		$this->_nonSefVars[] = 'scoid';								
	}
	
	function playerSCORMFiles_init() 
	{			
		$pos = new JLMS_SEF_VarsPositions( 'playerSCORMFiles' );
		$pos->addVar( '', 'library'  );
		$pos->addVar( 'doc_id', '', '', 'id' );		
		$pos->addVar( 'id', '', '', 'id' );							 		
		$this->_positions[] = $pos;  		 
	}
	
	function compose_lpath_build() 
	{		
		$course_name = $this->_tObj->get( 'courses', $this->getVar('course_id'));
		$lpath_name = $this->_tObj->get( 'learn_paths', $this->getVar('id') );
				
		$this->_res[] = $this->keySefUrlPart( $this->getVar('course_id'), $course_name );		
		$this->_res[] = 'learn_paths';						
		$this->_res[] = $this->keySefUrlPart( $this->getVar('id'), $lpath_name );
		$this->_res[] = 'compose';		
	}  
	
	function compose_lpath_init() 
	{			
		$pos = new JLMS_SEF_VarsPositions( 'compose_lpath' );
				
		$pos->addVar( 'course_id', '', '', 'id'  );
		$pos->addVar( '', 'learn_paths' );
		$pos->addVar( 'id', '', '', 'id'  );
		$pos->addVar( '', 'compose' );							 		
		$this->_positions[] = $pos;  		 
	}  
	
	function show_lpath_build() 
	{
		$course_name = $this->_tObj->get( 'courses', $this->getVar('course_id') );
		$lpath_name = $this->_tObj->get( 'learn_paths', $this->getVar('id') );
		
		$this->_res[] = $this->keySefUrlPart( $this->getVar('course_id'), $course_name );
		$this->_res[] = 'learn_paths';		
		$this->_res[] = $this->keySefUrlPart( $this->getVar('id'), $lpath_name );	
		
		$this->_nonSefVars[] = 'action';
		$this->_nonSefVars[] = 'doc_id';
		$this->_nonSefVars[] = 'step_id';
		$this->_nonSefVars[] = 'user_start_id';
		$this->_nonSefVars[] = 'force';						
	}
	
	function  show_lpath_init() 
	{			
		$pos = new JLMS_SEF_VarsPositions( 'show_lpath' );
				
		$pos->addVar( 'course_id', '', '', 'id'  );
		$pos->addVar( '', 'learn_paths' );
		$pos->addVar( 'id', '', '', 'id'  );							 		
		$this->_positions[] = $pos;  		 
	}  
	
	function new_lpath_chapter_build() {
		$course_name = $this->_tObj->get( 'courses', $this->getVar('course_id') );
		$lpath_name = $this->_tObj->get( 'learn_paths', $this->getVar('id') );
		
		$this->_res[] = $this->keySefUrlPart( $this->getVar('course_id'), $course_name );
		$this->_res[] = 'learn_paths';				
		$this->_res[] = $this->keySefUrlPart( $this->getVar('id'), $lpath_name );    
		$this->_res[] = 'new_lpath_chapter';	
	}
	
	function new_lpath_chapter_init() 
	{			
		$pos = new JLMS_SEF_VarsPositions( 'new_lpath_chapter' );
				
		$pos->addVar( 'course_id', '', '', 'id'  );
		$pos->addVar( '', 'learn_paths' );
		$pos->addVar( 'id', '', '', 'id'  );
		$pos->addVar( '', 'new_lpath_chapter' );							 		
		$this->_positions[] = $pos;  		 
	}
	
	function lpath_step_cond_build() {
		$course_name = $this->_tObj->get( 'courses', $this->getVar('course_id') );
		$lpath_name = $this->_tObj->get( 'learn_paths', $this->getVar('lpath_id') );
		$step_name = $this->_tObj->get( 'learn_path_steps', $this->getVar('id') );
		
		$this->_res[] = $this->keySefUrlPart( $this->getVar('course_id'), $course_name );		
		$this->_res[] = $this->keySefUrlPart( $this->getVar('lpath_id'), $lpath_name );							
		$this->_res[] = $this->keySefUrlPart( $this->getVar('id'), $step_name );   		
		$this->_res[] = 'conditions';		
	}
	
	function lpath_step_cond_init() 
	{			
		$pos = new JLMS_SEF_VarsPositions( 'lpath_step_cond' );
						
		$pos->addVar( 'course_id', '', '', 'id'  );
		$pos->addVar( 'lpath_id', '', '', 'id'  );
		$pos->addVar( 'id', '', '', 'id'  );		
		$pos->addVar( '', 'conditions' );							 		
		$this->_positions[] = $pos;  		 
	} 
	
	function show_lp_content_build() 
	{		
		$course_name = $this->_tObj->get( 'courses', $this->getVar('course_id') );
		$lpath_name = $this->_tObj->get( 'learn_paths', $this->getVar('lpath_id') );
		$step_name = $this->_tObj->get( 'learn_path_steps', $this->getVar('id') );
		
		$this->_res[] = $this->keySefUrlPart( $this->getVar('course_id'), $course_name );		
		$this->_res[] = $this->keySefUrlPart( $this->getVar('lpath_id'), $lpath_name ); 	
		$this->_res[] = 'step';					
		$this->_res[] = $this->keySefUrlPart( $this->getVar('id'), $step_name );	
	} 
	
	function show_lp_content_init() 
	{			
		$pos = new JLMS_SEF_VarsPositions( 'show_lp_content' );
						
		$pos->addVar( 'course_id', '', '', 'id'  );
		$pos->addVar( 'lpath_id', '', '', 'id'  );
		$pos->addVar( '', 'step' );
		$pos->addVar( 'id', '', '', 'id'  );							 		
		$this->_positions[] = $pos;  		 
	}   
	
	function get_document_build() {
		$course_name = $this->_tObj->get( 'courses', $this->getVar('course_id') );
		$doc_name = $this->_tObj->get( 'documents', $this->getVar('id') );
		
		$this->_res[] = $this->keySefUrlPart( $this->getVar('course_id'), $course_name );
		$this->_res[] = 'documents';
		$this->_res[] = 'files';				 	
		$this->_res[] = $this->keySefUrlPart( $this->getVar('id'), $doc_name );
		
		$this->_nonSefVars[] = 'force';
	}
	
	function get_document_init() 
	{			
		 $pos = new JLMS_SEF_VarsPositions( 'get_document' );
						
		$pos->addVar( 'course_id', '', '', 'id'  );
		$pos->addVar( '', 'documents' );
		$pos->addVar( '', 'files' );
		$pos->addVar( 'id', '', '', 'id'  );						 		
		$this->_positions[] = $pos;	  		 
	}
	
	function docs_view_content_build() 
	{
		$course_name = $this->_tObj->get( 'courses', $this->getVar('course_id') );
		$doc_name = $this->_tObj->get( 'documents', $this->getVar('id') );
		
		$this->_res[] = $this->keySefUrlPart( $this->getVar('course_id'), $course_name );
		$this->_res[] = 'documents';				 	
		$this->_res[] = $this->keySefUrlPart( $this->getVar('id'), $doc_name );	 	 
	}
	
	function docs_view_content_init() 
	{
		$pos = new JLMS_SEF_VarsPositions( 'docs_view_content' );
						
		$pos->addVar( 'course_id', '', '', 'id'  );
		$pos->addVar( '', 'documents' );
		$pos->addVar( 'id', '', '', 'id'  );						 		
		$this->_positions[] = $pos;	
	}
	
	function links_build() 
	{
		$course_name = $this->_tObj->get( 'courses', $this->getVar('id') );		
		$this->_res[] = $this->keySefUrlPart( $this->getVar('id'), $course_name );
		$this->_res[] = 'links';		
	}
	
	function links_init() 
	{
		$pos = new JLMS_SEF_VarsPositions( 'links' );
						
		$pos->addVar( 'id', '', '', 'id'  );
		$pos->addVar( '', 'links' );							 		
		$this->_positions[] = $pos;	 
	}
	
	function pre_create_link_build() 
	{
		$course_name = $this->_tObj->get( 'courses', $this->getVar('id') );		
		$this->_res[] = $this->keySefUrlPart( $this->getVar('id'), $course_name );
		$this->_res[] = 'links';
		$this->_res[] = 'new';		
	}
	
	function pre_create_links_init() 
	{
		$pos = new JLMS_SEF_VarsPositions( 'pre_create_link' );
						
		$pos->addVar( 'id', '', '', 'id'  );
		$pos->addVar( '', 'links' );
		$pos->addVar( '', 'new' );								
							 		
		$this->_positions[] = $pos;	 
	}
		
	function change_link_build() 
	{
		/*
		$course_name = $this->_tObj->get( 'courses', $this->getVar('id') );		
		$this->_res[] = $this->keySefUrlPart( $this->getVar('id'), $course_name );
		$this->_res[] = 'links';
		$this->_res[] = 'change';
				
		$this->_nonSefVars[] = 'state';
		*/			
	}
	
	function change_link_init() 
	{
		/*
		$pos = new JLMS_SEF_VarsPositions( 'change_link' );
						
		$pos->addVar( 'id', '', '', 'id'  );
		$pos->addVar( '', 'links', '' );
		$pos->addVar( '', 'change', '' );										
							 		
		$this->_positions[] = $pos;
		*/		 
	}
	
	function learnpaths_build() 
	{
		$course_name = $this->_tObj->get( 'courses', $this->getVar('id') );		
				
		$this->_res[] = $this->keySefUrlPart( $this->getVar('id'), $course_name );
		$this->_res[] = 'learn_paths';				                   
	}
	
	function learnpaths_init() 
	{
		$pos = new JLMS_SEF_VarsPositions( 'learnpaths' );
						
		$pos->addVar( 'id', '', '', 'id'  );		
		$pos->addVar( '', 'learn_paths' );								
							 		
		$this->_positions[] = $pos;	
	}
	
	function new_lpath_build() 
	{
		$course_name = $this->_tObj->get( 'courses', $this->getVar('id') );	
			
		$this->_res[] = $this->keySefUrlPart( $this->getVar('id'), $course_name );
		$this->_res[] = 'learn_paths';
		$this->_res[] = 'new';	
	}
	
	function new_lpath_init() 
	{
		$pos = new JLMS_SEF_VarsPositions( 'new_lpath' );
						
		$pos->addVar( 'id', '', '', 'id'  );
		$pos->addVar( '', 'learn_paths' );		
		$pos->addVar( '', 'new' );								
							 		
		$this->_positions[] = $pos;	
	}
	
	function change_lpath_build() 
	{		
		$course_name = $this->_tObj->get( 'courses', $this->getVar('id') );
		$lpath_name = $this->_tObj->get( 'learn_paths', $this->getVar('cid[0]') );
		
		$this->_res[] = $this->keySefUrlPart( $this->getVar('id'), $course_name );	
		$this->_res[] = 'learn_paths';		
		$this->_res[] = $this->keySefUrlPart( $this->getVar('cid[0]'), $lpath_name );
		
		$this->_nonSefVars[] = 'state';		
	}
	
	function change_lpath_init() 
	{
		$pos = new JLMS_SEF_VarsPositions( 'change_lpath' );
						
		$pos->addVar( 'id', '', '', 'id'  );
		$pos->addVar( '', 'learn_paths'  );
		$pos->addVar( 'cid[0]', '', '', 'id'  );										
							 		
		$this->_positions[] = $pos;	
	}
	
	function documents_build() 
	{
		$course_name = $this->_tObj->get( 'courses', $this->getVar('id') );	
			
		$this->_res[] = $this->keySefUrlPart( $this->getVar('id'), $course_name );
		$this->_res[] = 'documents';	
	}
	
	function documents_init() 
	{
		$pos = new JLMS_SEF_VarsPositions( 'documents' );
						
		$pos->addVar( 'id', '', '', 'id'  );
		$pos->addVar( '', 'documents' );										
							 		
		$this->_positions[] = $pos;	
	}
	
	function agenda_build() 
	{
		$course_name = $this->_tObj->get( 'courses', $this->getVar('id') );
				
		$this->_res[] = $this->keySefUrlPart( $this->getVar('id'), $course_name );
		$this->_res[] = 'announcements';
		
		$mode = $this->getVar('mode', 'string');
					
		switch ($mode) {
			case 'add_event':
				$this->_res[] = 'add';
			break;  
			case 'view_month':
				$this->_res[] = 'monthly';
				if( $this->getVar('date', 'string') )
					$this->_res[] = $this->getVar('date', 'string');
			break;	
			case 'view_week':
				$this->_res[] = 'weekly';
				if( $this->getVar('date', 'string') ) 
					$this->_res[] = $this->getVar('date', 'string');
			break;	
			case 'view_day':
				$this->_res[] = 'daily';
				if( $this->getVar('date', 'string') )
					$this->_res[] = $this->getVar('date', 'string');
			break;
			case 'edit':				
				$agenda_title = $this->_tObj->get( 'agenda', $this->getVar('agenda_id') );
				$this->_res[] = $this->keySefUrlPart( $this->getVar('agenda_id'), $agenda_title );				
				$this->_res[] = 'edit';
			break;
			case 'delete':
				$agenda_title = $this->_tObj->get( 'agenda', $this->getVar('agenda_id') );				
				$this->_res[] = $this->keySefUrlPart( $this->getVar('agenda_id'), $agenda_title );
				$this->_res[] = 'delete';
			break;
			default:
				if( $this->getVar('agenda_id') ) {
					$agenda_title = $this->_tObj->get( 'agenda', $this->getVar('agenda_id') );			   		
			   		$this->_res[] = $this->keySefUrlPart( $this->getVar('agenda_id'), $agenda_title );
				}			
		}		
		
		$this->_nonSefVars[] = 'date';
	}
	
	function agenda_init() 
	{
		$pos = new JLMS_SEF_VarsPositions( 'agenda' );						
		$pos->addVar( 'id', '', '', 'id'  );
		$pos->addVar( '', 'announcements' );
		$pos->addVar( 'mode', 'add_event', 'add' );		
		$this->_positions[] = $pos;
		
		$pos = new JLMS_SEF_VarsPositions( 'agenda' );						
		$pos->addVar( 'id', '', '', 'id'  );
		$pos->addVar( '', 'announcements' );
		$pos->addVar( 'mode', 'view_month', 'monthly' );				
		$this->_positions[] = $pos;
		
		$pos = new JLMS_SEF_VarsPositions( 'agenda' );						
		$pos->addVar( 'id', '', '', 'id'  );
		$pos->addVar( '', 'announcements' );
		$pos->addVar( 'mode', 'view_month', 'monthly' );
		$pos->addVar( 'date'  );		
		$this->_positions[] = $pos;
		
		$pos = new JLMS_SEF_VarsPositions( 'agenda' );						
		$pos->addVar( 'id', '', '', 'id'  );
		$pos->addVar( '', 'announcements' );
		$pos->addVar( 'mode', 'view_week', 'weekly' );				
		$this->_positions[] = $pos;
		
		$pos = new JLMS_SEF_VarsPositions( 'agenda' );						
		$pos->addVar( 'id', '', '', 'id'  );
		$pos->addVar( '', 'announcements' );
		$pos->addVar( 'mode', 'view_week', 'weekly' );
		$pos->addVar( 'date'  );		
		$this->_positions[] = $pos;
		
		$pos = new JLMS_SEF_VarsPositions( 'agenda' );						
		$pos->addVar( 'id', '', '', 'id'  );
		$pos->addVar( '', 'announcements' );
		$pos->addVar( 'mode', 'view_day', 'daily' );
				
		$this->_positions[] = $pos;
		
		$pos = new JLMS_SEF_VarsPositions( 'agenda' );						
		$pos->addVar( 'id', '', '', 'id'  );
		$pos->addVar( '', 'announcements' );
		$pos->addVar( 'mode', 'view_day', 'daily' );
		$pos->addVar( 'date'  );		
		$this->_positions[] = $pos;
		
		$pos = new JLMS_SEF_VarsPositions( 'agenda' );						
		$pos->addVar( 'id', '', '', 'id'  );
		$pos->addVar( '', 'announcements' );
		$pos->addVar( 'agenda_id', '', '', 'id' );
		$pos->addVar( 'mode', 'edit'  );		
		$this->_positions[] = $pos;
		
		$pos = new JLMS_SEF_VarsPositions( 'agenda' );						
		$pos->addVar( 'id', '', '', 'id'  );
		$pos->addVar( '', 'announcements' );
		$pos->addVar( 'agenda_id', '', '', 'id' );
		$pos->addVar( 'mode', 'delete'  );		
		$this->_positions[] = $pos;
		
		$pos = new JLMS_SEF_VarsPositions( 'agenda' );						
		$pos->addVar( 'id', '', '', 'id'  );
		$pos->addVar( '', 'announcements' );
		$pos->addVar( 'agenda_id', '', '', 'id' );				
		$this->_positions[] = $pos;	
		
		$pos = new JLMS_SEF_VarsPositions( 'agenda' );						
		$pos->addVar( 'id', '', '', 'id'  );
		$pos->addVar( '', 'announcements' );						
		$this->_positions[] = $pos;	
	}
	
	function quizzes_build() 
	{
		$page = $this->getVar('page', 'word');
		
		if( $page == 'imgs_v' ) 
		{
			$course_name = $this->_tObj->get( 'courses', $this->getVar('course_id') );				
			$this->_res[] = $this->keySefUrlPart( $this->getVar('course_id'), $course_name );	
		} else {
			$course_name = $this->_tObj->get( 'courses', $this->getVar('id') );				
			$this->_res[] = $this->keySefUrlPart( $this->getVar('id'), $course_name );
		}		
							
		
		$page = $this->getVar('page', 'word');
		
		if( !in_array( $page, array('setup_gqp', 'editA_quest_gqp', 'category_gqp', 'edit_category_gqp', 'preview_crtf' ) ) )
			$this->_res[] = 'quizzes';						
						
		switch ($page) {
			case 'view_answ_survey':
				$this->_res[] = 'survey_reports';
				$this->_nonSefVars[] = 'quiz_id';		 		
	   			$this->_nonSefVars[] = 'quest_id';
				$this->_nonSefVars[] = 'group_id';
			break;
			case 'cats':
				$this->_res[] = 'categories';
			break;	
			case 'add_cat':
				$this->_res[] = 'categories';
				$this->_res[] = 'new';
			break;
			case 'editA_cat':
				$cat_name = $this->_tObj->get( 'quiz_t_category', $this->getVar('c_id') );
				$this->_res[] = 'categories';
				$this->_res[] = 'edit';
				$this->_res[] = $this->keySefUrlPart( $this->getVar('c_id'), $cat_name );
			break;			
			case 'quizzes':
				$this->_res[] = 'quiz_management';				
			break;					 
			case 'reports':
				$this->_res[] = 'reports';
				$this->_nonSefVars[] = 'user_id';
			break;	
			case 'certificates':				
				$this->_res[] = 'certificates';	
			break;	
			case 'imgs':
				$this->_res[] = 'images';
			break;	
			case 'setup_quest':
				$quiz_id = $this->getVar('quiz_id');
				
				$this->_res[] = 'setup_quest';
				if($quiz_id == -1)
					$this->_res[] = 'question_pool';					
				else {			 		
					$quiz_title = $this->_tObj->get( 'quiz_t_quiz', $quiz_id );		   			
		   			$this->_res[] = $this->keySefUrlPart( $quiz_id, $quiz_title );
				}	   		
			break;	
			case 'setup_quiz':
				$this->_res[] = 'setup_quiz';
			break;
			case 'setup_gqp':
				$this->_res[] = 'global_pool';
			break;
			case 'editA_quest_gqp':
				$quest_name = $this->_tObj->get( 'quiz_t_question', $this->getVar('c_id') );
				
				$this->_res[] = 'global_pool';
				$this->_res[] = 'edita_quest';				
				$this->_res[] = $this->keySefUrlPart( $this->getVar('c_id'), $quest_name );
			break;
			case 'category_gqp':				
				$this->_res[] = 'global_pool';
				$this->_res[] = 'categories';				
			break;
			case 'edit_category_gqp':
				$cat_name = $this->_tObj->get( 'gqp_cats', $this->getVar('c_id') );
				
				$this->_res[] = 'global_pool';
				$this->_res[] = 'categories';
				$this->_res[] = 'edit';								
				$this->_res[] = $this->keySefUrlPart( $this->getVar('c_id'), $cat_name );
			break;				
			case 'stu_reportA':
				$this->_res[] = 'reporta';			 		
		   		$this->_res[] = $this->getVar('c_id');
		   	break;
		   	case 'quest_reportA':
		   		$stu_name = $this->_tObj->get( 'students', $this->getVar('stu_id') );
		   	
				$this->_res[] = 'reporta';							
				$this->_res[] = $this->keySefUrlPart( $this->getVar('stu_id'), $stu_name );					 	
				$this->_res[] = $this->getVar('c_id');		   		
		   	break;
			case 'stu_report':
					$this->_res[] = 'report';			 		
		   			$this->_res[] = $this->getVar('c_id');					
			break;			
			case 'publish_quest':
					$this->_res[] = 'quest';
					$this->_res[] = 'publish';	
					$this->_nonSefVars[] = 'state';		 		
		   			$this->_nonSefVars[] = 'cidoff';
					$this->_nonSefVars[] = 'gqp';					
			break;
			case 'unpublish_quest':
					$this->_res[] = 'quest';
					$this->_res[] = 'unpublish';
					$this->_nonSefVars[] = 'state';			 		
		   			$this->_nonSefVars[] = 'cidoff';
					$this->_nonSefVars[] = 'gqp';					
			break;
			case 'publish_quiz':								 		
		   			$this->_res[] = 'publish';		
					$this->_nonSefVars[] = 'state';			
					$this->_nonSefVars[] = 'cidoff';
					$this->_nonSefVars[] = 'gqp';					
			break;
			case 'unpublish_quiz':					
					$this->_res[] = 'unpublish';
					$this->_nonSefVars[] = 'state';					
					$this->_nonSefVars[] = 'cidoff';
					$this->_nonSefVars[] = 'gqp';		   								
			break;			
			case 'editA_quest':
					$this->_res[] = 'edita';			 		
		   			$this->_res[] = $this->getVar('c_id');					
			break;
			case 'add_imgs':
					$this->_res[] = 'images';			 		
		   			$this->_res[] = 'new';;					
			break;
			case 'editA_imgs':
					$this->_res[] = 'images';			 		
		   			$this->_res[] = 'edit';					   				
					$img_name = $this->_tObj->get( 'quiz_images', $this->getVar('c_id') );				
					$this->_res[] = $this->keySefUrlPart( $this->getVar('c_id'), $img_name );						
			break;			
			case 'add_crtf':
					$this->_res[] = 'certificates';								 		
		   			$this->_res[] = 'new';;					
			break;
			case 'editA_crtf':							
				$this->_res[] = 'certificates';
				$this->_res[] = 'edit';	
				$this->_res[] = $this->getVar('c_id');				   								
			break;
			case 'quiz_bars':
					$this->_res[] = 'answers_statistics';	   								
			break;
			case 'preview_crtf':
					$this->_res[] = 'preview_crtf';
					$this->_nonSefVars[] = 'crtf_id';
					$this->_nonSefVars[] = 'crtf_role';
					$this->_nonSefVars[] = 'no_html';	   								
			break;
			case 'uploadimage':
					$this->_res[] = 'uploadimage';						   								
			break;	
			case 'uploadimage_gqp':
					$this->_res[] = 'uploadimage_gqp';						   								
			break;
			case 'imgs_v':
					$this->_res[] = 'imgs_v';		
					$this->_nonSefVars[] = 'crtf_id';
					$this->_nonSefVars[] = 'file_id';
					$this->_nonSefVars[] = 'imgs_name';
					$this->_nonSefVars[] = 'pic_width';
					$this->_nonSefVars[] = 'pic_height';
					$this->_nonSefVars[] = 'bg_color';				   								
			break;
			case 'createhotspot':
					$this->_res[] = 'createhotspot';		
					$this->_nonSefVars[] = 'hotspot';												   								
			break;
			case 'createhotspot_gqp':
					$this->_res[] = 'createhotspot_gqp';		
					$this->_nonSefVars[] = 'hotspot';
			break;
			case 'add_quiz':							
				$this->_res[] = 'new';					   								
			break;				
		}	
	}
	
	function quizzes_init() 
	{
		$pos = new JLMS_SEF_VarsPositions( 'quizzes' );						
		$pos->addVar( 'id', '', '', 'id'  );
		$pos->addVar( '', 'quizzes' );							 		
		$this->_positions[] = $pos;
		
		$pos = new JLMS_SEF_VarsPositions( 'quizzes' );						
		$pos->addVar( 'id', '', '', 'id'  );
		$pos->addVar( '', 'quizzes' );
		$pos->addVar( 'page', 'cats', 'categories' );					 		
		$this->_positions[] = $pos;
		
		$pos = new JLMS_SEF_VarsPositions( 'quizzes' );						
		$pos->addVar( 'id', '', '', 'id'  );
		$pos->addVar( '', 'quizzes' );
		$pos->addVar( '', 'categories'  );
		$pos->addVar( 'page', 'add_cat', 'new' );					 		
		$this->_positions[] = $pos;
		
		$pos = new JLMS_SEF_VarsPositions( 'quizzes' );						
		$pos->addVar( 'id', '', '', 'id'  );
		$pos->addVar( '', 'quizzes' );
		$pos->addVar( '', 'categories'  );		
		$pos->addVar( 'page', 'editA_cat', 'edit' );
		$pos->addVar( 'c_id', '', '', 'id' );					 		
		$this->_positions[] = $pos;
		
		$pos = new JLMS_SEF_VarsPositions( 'quizzes' );						
		$pos->addVar( 'id', '', '', 'id'  );
		$pos->addVar( '', 'quizzes' );
		$pos->addVar( 'page', 'quizzes', 'quiz_management' );					 		
		$this->_positions[] = $pos;
		
		$pos = new JLMS_SEF_VarsPositions( 'quizzes' );						
		$pos->addVar( 'id', '', '', 'id'  );
		$pos->addVar( '', 'quizzes' );
		$pos->addVar( 'page', 'reports' );					 		
		$this->_positions[] = $pos;
		
		$pos = new JLMS_SEF_VarsPositions( 'quizzes' );						
		$pos->addVar( 'id', '', '', 'id'  );
		$pos->addVar( '', 'quizzes' );
		$pos->addVar( 'page', 'certificates' );					 		
		$this->_positions[] = $pos;
		
		$pos = new JLMS_SEF_VarsPositions( 'quizzes' );						
		$pos->addVar( 'id', '', '', 'id'  );
		$pos->addVar( '', 'quizzes' );
		$pos->addVar( 'page' ,'imgs', 'images' );					 		
		$this->_positions[] = $pos;	
		
		$pos = new JLMS_SEF_VarsPositions( 'quizzes' );						
		$pos->addVar( 'id', '', '', 'id'  );
		$pos->addVar( '', 'quizzes' );
		$pos->addVar( 'page', 'setup_quest' );
		$pos->addVar( 'quiz_id', '-1', 'question_pool' );							 		
		$this->_positions[] = $pos;		
		
		$pos = new JLMS_SEF_VarsPositions( 'quizzes' );						
		$pos->addVar( 'id', '', '', 'id'  );
		$pos->addVar( '', 'quizzes' );
		$pos->addVar( 'page', 'setup_quest' );
		$pos->addVar( 'quiz_id', '', '', 'id' );							 		
		$this->_positions[] = $pos;
		
		$pos = new JLMS_SEF_VarsPositions( 'quizzes' );						
		$pos->addVar( 'id', '', '', 'id'  );
		$pos->addVar( '', 'quizzes' );
		$pos->addVar( 'page', 'setup_quest' );									 		
		$this->_positions[] = $pos;
		
		$pos = new JLMS_SEF_VarsPositions( 'quizzes' );						
		$pos->addVar( 'id', '', '', 'id'  );
		$pos->addVar( '', 'quizzes' );
		$pos->addVar( 'page', 'setup_quiz' );									 		
		$this->_positions[] = $pos;
		
		$pos = new JLMS_SEF_VarsPositions( 'quizzes' );		
		$pos->addVar( 'page', 'setup_gqp', 'global_pool' );									 		
		$this->_positions[] = $pos;
		
		$pos = new JLMS_SEF_VarsPositions( 'quizzes' );		
		$pos->addVar( '', 'global_pool' );
		$pos->addVar( 'page', 'editA_quest_gqp', 'edita_quest' );		
		$pos->addVar( 'c_id', '', '', 'id' );											 		
		$this->_positions[] = $pos;
		
		$pos = new JLMS_SEF_VarsPositions( 'quizzes' );		
		$pos->addVar( '', 'global_pool' );
		$pos->addVar( 'page', 'category_gq', 'categories' );												 		
		$this->_positions[] = $pos;			
		
		$pos = new JLMS_SEF_VarsPositions( 'quizzes' );		
		$pos->addVar( '', 'global_pool' );
		$pos->addVar( '', 'categories' );
		$pos->addVar( 'page', 'edit_category_gqp', 'edit' );		
		$pos->addVar( 'c_id', '', '', 'id' );											 		
		$this->_positions[] = $pos;
		
		$pos = new JLMS_SEF_VarsPositions( 'quizzes' );						
		$pos->addVar( 'id', '', '', 'id'  );
		$pos->addVar( '', 'quizzes' );
		$pos->addVar( 'page', 'stu_reportA', 'reporta' );
		$pos->addVar( 'c_id', '', '', 'id' );										 		
		$this->_positions[] = $pos;
		
		$pos = new JLMS_SEF_VarsPositions( 'quizzes' );						
		$pos->addVar( 'id', '', '', 'id'  );
		$pos->addVar( '', 'quizzes' );		
		$pos->addVar( 'page', 'quest_reportA', 'reporta' );
		$pos->addVar( 'stu_id', '', '', 'id' );
		$pos->addVar( 'c_id', '', '', 'id' );										 		
		$this->_positions[] = $pos;	
		
		$pos = new JLMS_SEF_VarsPositions( 'quizzes' );						
		$pos->addVar( 'id', '', '', 'id'  );
		$pos->addVar( '', 'quizzes' );
		$pos->addVar( 'page', 'stu_report', 'report' );
		$pos->addVar( 'c_id', '', '', 'id' );									 		
		$this->_positions[] = $pos;
		
		$pos = new JLMS_SEF_VarsPositions( 'quizzes' );						
		$pos->addVar( 'id', '', '', 'id'  );
		$pos->addVar( '', 'quizzes' );
		$pos->addVar( '', 'quest' );
		$pos->addVar( 'page', 'publish_quest', 'publish' );											 		
		$this->_positions[] = $pos;
		
		$pos = new JLMS_SEF_VarsPositions( 'quizzes' );						
		$pos->addVar( 'id', '', '', 'id'  );
		$pos->addVar( '', 'quizzes' );
		$pos->addVar( '', 'quest' );
		$pos->addVar( 'page', 'unpublish_quest', 'unpublish' );											 		
		$this->_positions[] = $pos;
		
		$pos = new JLMS_SEF_VarsPositions( 'quizzes' );						
		$pos->addVar( 'id', '', '', 'id'  );
		$pos->addVar( '', 'quizzes' );		
		$pos->addVar( 'page', 'publish_quiz', 'publish' );											 		
		$this->_positions[] = $pos;
		
		$pos = new JLMS_SEF_VarsPositions( 'quizzes' );						
		$pos->addVar( 'id', '', '', 'id'  );
		$pos->addVar( '', 'quizzes' );		
		$pos->addVar( 'page', 'unpublish_quiz', 'unpublish' );											 		
		$this->_positions[] = $pos;
		
		$pos = new JLMS_SEF_VarsPositions( 'quizzes' );						
		$pos->addVar( 'id', '', '', 'id'  );
		$pos->addVar( '', 'quizzes' );
		$pos->addVar( 'page', 'editA_quest', 'edita' );
		$pos->addVar( 'c_id', '', '', 'id' );									 		
		$this->_positions[] = $pos;
		
		$pos = new JLMS_SEF_VarsPositions( 'quizzes' );						
		$pos->addVar( 'id', '', '', 'id'  );
		$pos->addVar( '', 'quizzes' );		
		$pos->addVar( '', 'images' );	
		$pos->addVar( 'page', 'add_imgs', 'new' );								 		
		$this->_positions[] = $pos;
		
		$pos = new JLMS_SEF_VarsPositions( 'quizzes' );						
		$pos->addVar( 'id', '', '', 'id'  );
		$pos->addVar( '', 'quizzes' );		
		$pos->addVar( '', 'images' );			
		$pos->addVar( 'page', 'editA_imgs', 'edit' );
		$pos->addVar( 'c_id', '', '', 'id'  );								 		
		$this->_positions[] = $pos;
		
		$pos = new JLMS_SEF_VarsPositions( 'quizzes' );						
		$pos->addVar( 'id', '', '', 'id'  );
		$pos->addVar( '', 'quizzes' );				
		$pos->addVar( '', 'certificates' );	
		$pos->addVar( 'page', 'add_crtf', 'new' );								 		
		$this->_positions[] = $pos;
		
		$pos = new JLMS_SEF_VarsPositions( 'quizzes' );						
		$pos->addVar( 'id', '', '', 'id'  );
		$pos->addVar( '', 'quizzes' );				
		$pos->addVar( '', 'certificates' );	
		$pos->addVar( 'page', 'editA_crtf', 'edit' );
		$pos->addVar( 'c_id', '', '', 'id'  );								 		
		$this->_positions[] = $pos;
		
		$pos = new JLMS_SEF_VarsPositions( 'quizzes' );						
		$pos->addVar( 'id', '', '', 'id'  );
		$pos->addVar( '', 'quizzes' );			
		$pos->addVar( 'page', 'quiz_bars', 'answers_statistics' );								 		
		$this->_positions[] = $pos;		
		
		$pos = new JLMS_SEF_VarsPositions( 'quizzes' );						
		$pos->addVar( 'id', '', '', 'id'  );							
		$pos->addVar( 'page', 'preview_crtf' );								 		
		$this->_positions[] = $pos;
		
		$pos = new JLMS_SEF_VarsPositions( 'quizzes' );						
		$pos->addVar( 'id', '', '', 'id'  );
		$pos->addVar( '', 'quizzes' );							
		$pos->addVar( 'page', 'uploadimage' );								 		
		$this->_positions[] = $pos;
		
		$pos = new JLMS_SEF_VarsPositions( 'quizzes' );						
		$pos->addVar( 'id', '', '', 'id'  );
		$pos->addVar( '', 'quizzes' );							
		$pos->addVar( 'page', 'uploadimage_gqp' );								 		
		$this->_positions[] = $pos;	
		
		$pos = new JLMS_SEF_VarsPositions( 'quizzes' );						
		$pos->addVar( 'course_id', '', '', 'id'  );
		$pos->addVar( '', 'quizzes' );							
		$pos->addVar( 'page', 'imgs_v' );								 		
		$this->_positions[] = $pos;

		$pos = new JLMS_SEF_VarsPositions( 'quizzes' );						
		$pos->addVar( 'id', '', '', 'id'  );
		$pos->addVar( '', 'quizzes' );							
		$pos->addVar( 'page', 'view_answ_survey', 'survey_reports');								 		
		$this->_positions[] = $pos;	
		
		$pos = new JLMS_SEF_VarsPositions( 'quizzes' );						
		$pos->addVar( 'id', '', '', 'id'  );
		$pos->addVar( '', 'quizzes' );							
		$pos->addVar( 'page', 'add_quiz', 'new');								 		
		$this->_positions[] = $pos;	
	}			
	
	function quiz_action_build() 
	{		        		
		$course_name = $this->_tObj->get( 'courses', $this->getVar('id') );
		$quiz_title = $this->_tObj->get( 'quiz_t_quiz', $this->getVar('quiz') );
		
		$this->_res[] = $this->keySefUrlPart( $this->getVar('id'), $course_name );				
		$this->_res[] = $this->keySefUrlPart( $this->getVar('quiz'), $quiz_title );
		
		$atask = $this->getVar('atask', 'word');
		
		switch ( $atask ) {
			case 'start':
				$this->_res[] = 'start';
			break;	
			case 'contents':
				$this->_res[] = 'contents';
				$this->_res[] = $this->getVar('quest_id');
			break;	
			case 'review_stop':
				$this->_res[] = 'review_end';
			break;	
			case 'review_start':
				$this->_res[] = 'review';
			break;	
			case 'goto_quest':
				$this->_res[] = 'change_question';
				$this->_nonSefVars[] = 'seek_quest_id';
				$this->_nonSefVars[] = 'stu_quiz_id';				
			break;
			case 'email_results':
				$this->_res[] = 'email_results';
				$this->_nonSefVars[] = 'user_unique_id';
				$this->_nonSefVars[] = 'stu_quiz_id';				
			break;			
		}		
	}
	
	function quiz_action_init() 
	{
		$pos = new JLMS_SEF_VarsPositions( 'quiz_action' );						
		$pos->addVar( 'id', '', '', 'id'  );
		$pos->addVar( 'quiz', '', '', 'id'  );		
		$pos->addVar( 'atask', 'start' );							 		
		$this->_positions[] = $pos;
		
		$pos = new JLMS_SEF_VarsPositions( 'quiz_action' );						
		$pos->addVar( 'id', '', '', 'id'  );
		$pos->addVar( 'quiz', '', '', 'id'  );		
		$pos->addVar( 'atask', 'contents' );
		$pos->addVar( 'quest_id', '', '', 'id' );							 		
		$this->_positions[] = $pos;
		
		$pos = new JLMS_SEF_VarsPositions( 'quiz_action' );						
		$pos->addVar( 'id', '', '', 'id'  );
		$pos->addVar( 'quiz', '', '', 'id'  );		
		$pos->addVar( 'atask', 'review_stop', 'review_end' );									 		
		$this->_positions[] = $pos;
		
		$pos = new JLMS_SEF_VarsPositions( 'quiz_action' );						
		$pos->addVar( 'id', '', '', 'id'  );
		$pos->addVar( 'quiz', '', '', 'id'  );		
		$pos->addVar( 'atask', 'review_start', 'review' );									 		
		$this->_positions[] = $pos;
		
		$pos = new JLMS_SEF_VarsPositions( 'quiz_action' );						
		$pos->addVar( 'id', '', '', 'id'  );
		$pos->addVar( 'quiz', '', '', 'id'  );		
		$pos->addVar( 'atask', 'goto_quest', 'change_question' );													 		
		$this->_positions[] = $pos;	
		
		$pos = new JLMS_SEF_VarsPositions( 'quiz_action' );						
		$pos->addVar( 'id', '', '', 'id'  );
		$pos->addVar( 'quiz', '', '', 'id'  );		
		$pos->addVar( 'atask', 'email_results' );									 		
		$this->_positions[] = $pos;
	}	
	
	function print_quiz_result_build() 
	{
		$course_name = $this->_tObj->get( 'courses', $this->getVar('course_id') );		
		$this->_db->setQuery("SELECT a.c_title FROM #__lms_quiz_t_quiz as a, #__lms_quiz_r_student_quiz as b WHERE b.c_id = ".$this->getVar('stu_quiz_id')." and b.c_quiz_id = a.c_id");
		$quiz_title = $this->_db->loadResult();			
		
		$this->_res[] = $this->keySefUrlPart( $this->getVar('course_id'), $course_name );
		$this->_res[] = $this->keySefUrlPart( $this->getVar('stu_quiz_id'), $quiz_title );		
		$this->_res[] = 'print_result';		        		
	}
	
	function print_quiz_result_init() 
	{
		$pos = new JLMS_SEF_VarsPositions( 'print_quiz_result' );						
		$pos->addVar( 'course_id', '', '', 'id'  );
		$pos->addVar( 'stu_quiz_id', '', '', 'id'  );		
		$pos->addVar( '', 'print_result' );							 		
		$this->_positions[] = $pos;	
	}	
	
	function show_quiz_build() {		
		$course_title = $this->_tObj->get( 'courses', $this->getVar('id') );
		$quiz_title = $this->_tObj->get( 'quiz_t_quiz', $this->getVar('quiz_id') );
		
		$this->_res[] = $this->keySefUrlPart( $this->getVar('id'), $course_title );
		$this->_res[] = 'quizzes';				
		$this->_res[] = $this->keySefUrlPart( $this->getVar('quiz_id'), $quiz_title );	
	}
	
	function show_quiz_init() 
	{
		$pos = new JLMS_SEF_VarsPositions( 'show_quiz' );
		$pos->addVar( 'id', '', '', 'id'  );					
		$pos->addVar( '', 'quizzes' );							 
		$pos->addVar( 'quiz_id', '', '', 'id'  );		
		$this->_positions[] = $pos;	
	}	
	
	function show_cart_build() {
		$this->_res[] = 'shopping_cart';			
	}
	
	function show_cart_init() 
	{
		$pos = new JLMS_SEF_VarsPositions( 'show_cart' );					
		$pos->addVar( '', 'shopping_cart' );			
		$this->_positions[] = $pos;	
	}
	
	function subscription_build() {		
		$this->_res[] = 'subscriptions';	
				
		$this->_nonSefVars[] = 'category_filter';		
		$this->_nonSefVars[] = 'course_filter';		
		$this->_nonSefVars[] = 'id';
		$this->_nonSefVars[] = 'after_reg';
		$this->_nonSefVars[] = 'after_login';
	}
	
	function subscription_init() 
	{
		$pos = new JLMS_SEF_VarsPositions( 'subscription' );					
		$pos->addVar( '', 'subscriptions' );			
		$this->_positions[] = $pos;	
	}
	
	function details_course_build() {
		$course_name = $this->_tObj->get( 'courses', $this->getVar('id') );
		
		$this->_res[] = 'courses';					
		$this->_res[] = $this->keySefUrlPart( $this->getVar('id'), $course_name );
		
		$this->_nonSefVars[] = 'short';		
	}
	
	function details_course_init() 
	{
		$pos = new JLMS_SEF_VarsPositions( 'details_course' );
		$pos->addVar( '', 'courses' );					
		$pos->addVar( 'id', '', '', 'id' );			
		$this->_positions[] = $pos;	
	}
	
	function courses_build() {			
		$this->_res[] = 'courses';
		
		$this->_nonSefVars[] = 'groups_course';
		
		for( $i=0; $i<11; $i++ ) 
		{
			$this->_nonSefVars[] = 'filter_id_'.$i;
		}								
	}
	
	function courses_init() 
	{
		$pos = new JLMS_SEF_VarsPositions( 'courses' );					
		$pos->addVar( '', 'courses' );			
		$this->_positions[] = $pos;	
	}
		
	function add_course_build() {		
		$this->_res[] = 'courses';
		$this->_res[] = 'add';
	}
	
	function add_course_init() 
	{
		$pos = new JLMS_SEF_VarsPositions( 'add_course' );
		$pos->addVar( '', 'courses' );
		$pos->addVar( '', 'add' );				
		$this->_positions[] = $pos;	
	}
	
	function change_course_build() {
		$course_name = $this->_tObj->get( 'courses', $this->getVar('id') );	
			
		$this->_res[] = $this->keySefUrlPart( $this->getVar('id'), $course_name );
		$this->_res[] = 'change';
		
		$this->_nonSefVars[] = 'state';		
	}
	
	function change_course_init() 
	{
		$pos = new JLMS_SEF_VarsPositions( 'change_course' );
		$pos->addVar( 'id', '', '', 'id' );
		$pos->addVar( '', 'change' );								
		$this->_positions[] = $pos;		
	}
	
	function new_folder_build() {
		$course_name = $this->_tObj->get( 'courses', $this->getVar('id') );	
			
		$this->_res[] = $this->keySefUrlPart( $this->getVar('id'), $course_name );
		$this->_res[] = 'documents';
		$this->_res[] = 'new_folder';		
	}
	
	function new_folder_init() 
	{
		$pos = new JLMS_SEF_VarsPositions( 'new_folder' );
		$pos->addVar( 'id', '', '', 'id' );
		$pos->addVar( '', 'documents' );
		$pos->addVar( '', 'new_folder' );						
		$this->_positions[] = $pos;		
	}
	
	function new_document_build() {
		$course_name = $this->_tObj->get( 'courses', $this->getVar('id') );	
			
		$this->_res[] = $this->keySefUrlPart( $this->getVar('id'), $course_name );
		$this->_res[] = 'documents';
		$this->_res[] = 'new_document';		
	}
	
	function new_document_init() 
	{
		$pos = new JLMS_SEF_VarsPositions( 'new_document' );
		$pos->addVar( 'id', '', '', 'id' );
		$pos->addVar( '', 'documents' );
		$pos->addVar( '', 'new_document' );						
		$this->_positions[] = $pos;		
	}
	
	function change_doc_build() 
	{
		$course_name = $this->_tObj->get( 'courses', $this->getVar('id') );
		$doc_name = $this->_tObj->get( 'documents', $this->getVar('cid[0]') );
		
		$this->_res[] = $course_name;
		$this->_res[] = 'documents';				
		$this->_res[] = $this->keySefUrlPart( $this->getVar('cid[0]'), $doc_name );
		
		$this->_nonSefVars[] = 'state';		
	}
	
	function change_doc_init() 
	{
		$pos = new JLMS_SEF_VarsPositions( 'change_doc' );
		$pos->addVar( 'id', '', '', 'id' );
		$pos->addVar( '', 'documents' );
		$pos->addVar( 'cid[0]', '', '', 'id' );												
		$this->_positions[] = $pos;			
	}
	
	function gradebook_build() {
		$course_name = $this->_tObj->get( 'courses', $this->getVar('id') );	
			
		$this->_res[] = $this->keySefUrlPart( $this->getVar('id'), $course_name );
		$this->_res[] = 'gradebook';		
	}
	
	function gradebook_init() 
	{
		$pos = new JLMS_SEF_VarsPositions( 'gradebook' );
		$pos->addVar( 'id', '', '', 'id' );
		$pos->addVar( '', 'gradebook' );											
		$this->_positions[] = $pos;					
	}
		
 	function gb_crt_build() {
		$course_name = $this->_tObj->get( 'courses', $this->getVar('id') );
		
		$this->_res[] = $this->keySefUrlPart( $this->getVar('id'), $course_name );
		$this->_res[] = 'gradebook';
		$this->_res[] = 'completions';				
		$this->_res[] = $this->getVar('cid[0]');
		
		$this->_nonSefVars[] = 'state';		
	}	
	
	function gb_crt_init() 
	{
		$pos = new JLMS_SEF_VarsPositions( 'gb_crt' );
		$pos->addVar( 'id', '', '', 'id' );
		$pos->addVar( '', 'gradebook' );
		$pos->addVar( '', 'completions' );		
		$pos->addVar( 'cid[0]', '' ,'', 'id' );												
		$this->_positions[] = $pos;				
	}
		        	
	function gb_scale_build() {
		$course_name = $this->_tObj->get( 'courses', $this->getVar('course_id') );		
		
		$this->_res[] = $this->keySefUrlPart( $this->getVar('course_id'), $course_name );		
		$this->_res[] = 'gradebook';
		$this->_res[] = 'scales';		
	}
	
	function gb_scale_init() 
	{
		$pos = new JLMS_SEF_VarsPositions( 'gb_scale' );
		$pos->addVar( 'course_id', '', '', 'id' );
		$pos->addVar( '', 'gradebook' );
		$pos->addVar( '', 'scales' );														
		$this->_positions[] = $pos;					
	}
	
	function gb_items_build() {
		$course_name = $this->_tObj->get( 'courses', $this->getVar('id') );	
			
		$this->_res[] = $this->keySefUrlPart( $this->getVar('id'), $course_name );
		$this->_res[] = 'gradebook';
		$this->_res[] = 'items';		
	}
	
	function gb_items_init() 
	{
		$pos = new JLMS_SEF_VarsPositions( 'gb_items' );
		$pos->addVar( 'id', '', '', 'id' );
		$pos->addVar( '', 'gradebook' );
		$pos->addVar( '', 'items' );														
		$this->_positions[] = $pos;					
	}
	
	function gb_certificates_build() {
		$course_name = $this->_tObj->get( 'courses', $this->getVar('id') );
				
		$this->_res[] = $this->keySefUrlPart( $this->getVar('id'), $course_name );
		$this->_res[] = 'gradebook';
		$this->_res[] = 'certificate';		
	}
	
	function gb_certificates_init() 
	{
		$pos = new JLMS_SEF_VarsPositions( 'gb_certificates' );
		$pos->addVar( 'id', '', '', 'id' );
		$pos->addVar( '', 'gradebook' );
		$pos->addVar( '', 'certificate' );														
		$this->_positions[] = $pos;					
	}
	
	function gb_setup_path_build() {
		$course_name = $this->_tObj->get( 'courses', $this->getVar('id') );	
			
		$this->_res[] = $this->keySefUrlPart( $this->getVar('id'), $course_name );
		$this->_res[] = 'gradebook';
		$this->_res[] = 'settings';		
	}
	
	function gb_setup_path_init() 
	{
		$pos = new JLMS_SEF_VarsPositions( 'gb_setup_path' );
		$pos->addVar( 'id', '', '', 'id' );
		$pos->addVar( '', 'gradebook' );
		$pos->addVar( '', 'settings' );														
		$this->_positions[] = $pos;					
	}
		        	
	function gb_usergrade_build() {
		$course_name = $this->_tObj->get( 'courses', $this->getVar('course_id') );
		$username = $this->_tObj->get( 'users', $this->getVar('id') );	
			
		$this->_res[] = $this->keySefUrlPart( $this->getVar('course_id'), $course_name );
		$this->_res[] = 'gradebook';							
		$this->_res[] = $this->keySefUrlPart( $this->getVar('id'), $username );
	}
	
	function gb_usergrade_init() 
	{
		$pos = new JLMS_SEF_VarsPositions( 'gb_usergrade' );
		$pos->addVar( 'course_id', '', '', 'id' );
		$pos->addVar( '', 'gradebook' );		
		$pos->addVar( 'id', '', '', 'id' );														
		$this->_positions[] = $pos;					
	}
	
	function homework_build() {
		$course_name = $this->_tObj->get( 'courses', $this->getVar('id') );	
			
		$this->_res[] = $this->keySefUrlPart( $this->getVar('id'), $course_name );
		$this->_res[] = 'homework';
		
		$this->_nonSefVars[] = 'filt_hw';
	}
	
	function homework_init() 
	{
		$pos = new JLMS_SEF_VarsPositions( 'homework' );
		$pos->addVar( 'id', '', '', 'id' );
		$pos->addVar( '', 'homework' );																
		$this->_positions[] = $pos;					
	}
	
	function hw_create_build() {
		$course_name = $this->_tObj->get( 'courses', $this->getVar('id') );	
			
		$this->_res[] = $this->keySefUrlPart( $this->getVar('id'), $course_name );
		$this->_res[] = 'homework';
		$this->_res[] = 'new';	
	}
	
	function hw_create_init() 
	{
		$pos = new JLMS_SEF_VarsPositions( 'hw_create' );
		$pos->addVar( 'id', '', '', 'id' );
		$pos->addVar( '', 'homework' );
		$pos->addVar( '', 'new' );																
		$this->_positions[] = $pos;					
	}
	
	function hw_stats_build() {
		$course_name = $this->_tObj->get( 'courses', $this->getVar('course_id') );
		$hw_name = $this->_tObj->get( 'homework', $this->getVar('id') );
		
		$this->_res[] = $this->keySefUrlPart( $this->getVar('course_id'), $course_name );
		$this->_res[] = 'homework';				
		$this->_res[] = $this->keySefUrlPart( $this->getVar('id'), $hw_name );
				   
		$this->_nonSefVars[] = 'filt_hw';
		$this->_nonSefVars[] = 'filt_group';
		$this->_nonSefVars[] = 'filter_stu';
		$this->_nonSefVars[] = 'filt_subgroup';	
	}
	
	function hw_stats_init() 
	{
		$pos = new JLMS_SEF_VarsPositions( 'hw_stats' );
		$pos->addVar( 'course_id', '', '', 'id' );
		$pos->addVar( '', 'homework' );
		$pos->addVar( 'id', '', '', 'id' );																
		$this->_positions[] = $pos;					
	}
	
	function attendance_build() {
		$course_name = $this->_tObj->get( 'courses', $this->getVar('id') );	
			
		$this->_res[] = $this->keySefUrlPart( $this->getVar('id'), $course_name );
		$this->_res[] = 'attendance';
		
		$this->_nonSefVars[] = 'filt_group';
		$this->_nonSefVars[] = 'filt_subgroup';						
	}
	
	function attendance_init() 
	{
		$pos = new JLMS_SEF_VarsPositions( 'attendance' );
		$pos->addVar( 'id', '', '', 'id' );
		$pos->addVar( '', 'attendance' );																		
		$this->_positions[] = $pos;					
	}
	
	function at_userattend_build() {
		$course_name = $this->_tObj->get( 'courses', $this->getVar('course_id') );
		$username = $this->_tObj->get( 'users', $this->getVar('id') );
		
		$this->_res[] = $this->keySefUrlPart( $this->getVar('course_id'), $course_name );
		$this->_res[] = 'attendance';		
		$this->_res[] = $this->getVar('at_date', 'word');		
		$this->_res[] = $this->keySefUrlPart( $this->getVar('id'), $username );	
	}
	
	function at_userattend_init() 
	{
		$pos = new JLMS_SEF_VarsPositions( 'at_userattend' );
		$pos->addVar( 'course_id', '', '', 'id' );
		$pos->addVar( '', 'attendance' );
		$pos->addVar( 'at_date' );
		$pos->addVar( 'id', '', '', 'id' );																				
		$this->_positions[] = $pos;
		
		$pos = new JLMS_SEF_VarsPositions( 'at_userattend' );
		$pos->addVar( 'course_id', '', '', 'id' );
		$pos->addVar( '', 'attendance' );		
		$pos->addVar( 'id', '', '', 'id' );																				
		$this->_positions[] = $pos;					
	}
	
	function at_change_build() {
		$course_name = $this->_tObj->get( 'courses', $this->getVar('id') );
		$username = $this->_tObj->get( 'users', $this->getVar('cid[0]'));
		
		$this->_res[] = $this->keySefUrlPart( $this->getVar('id'), $course_name );
		$this->_res[] = 'attendance';
		$this->_res[] = $this->getVar('at_date', 'word');		
		$this->_res[] = $this->keySefUrlPart( $this->getVar('period_id'), 'period' );	
		$this->_res[] = $this->keySefUrlPart( $this->getVar('cid[0]'), $username );
		
		$this->_nonSefVars[] = 'state';	
	}
	
	function at_change_init() 
	{
		$pos = new JLMS_SEF_VarsPositions( 'at_change' );
		$pos->addVar( 'id', '', '', 'id' );
		$pos->addVar( '', 'attendance' );
		$pos->addVar( 'at_date' );		
		$pos->addVar( 'period_id', '', '', 'id' );
		$pos->addVar( 'cid[0]', '', '', 'id' );																					
		$this->_positions[] = $pos;				
	}
	
	function dropbox_build() {
		$course_name = $this->_tObj->get( 'courses', $this->getVar('id') );	
			
		$this->_res[] = $this->keySefUrlPart( $this->getVar('id'), $course_name );
		$this->_res[] = 'dropbox';		
	}
	
	function dropbox_init() 
	{
		$pos = new JLMS_SEF_VarsPositions( 'dropbox' );
		$pos->addVar( 'id', '', '', 'id' );
		$pos->addVar( '', 'dropbox' );
																					
		$this->_positions[] = $pos;						
	}
	
	function new_dropbox_build() {
		$course_name = $this->_tObj->get( 'courses', $this->getVar('id') );	
			
		$this->_res[] = $this->keySefUrlPart( $this->getVar('id'), $course_name );
		$this->_res[] = 'new_dropbox';	
	}
	
	function new_dropbox_init() 
	{
		$pos = new JLMS_SEF_VarsPositions( 'new_dropbox' );
		$pos->addVar( 'id', '', '', 'id' );
		$pos->addVar( '', 'new_dropbox' );
																					
		$this->_positions[] = $pos;						
	}
	
	function get_frombox_build() {
		$course_name = $this->_tObj->get( 'courses', $this->getVar('course_id') );
		$drp_name = $this->_tObj->get( 'dropbox', $this->getVar('id') );
		
		$this->_res[] = $this->keySefUrlPart( $this->getVar('course_id'), $course_name );
		$this->_res[] = 'dropbox';				
		$this->_res[] = $this->keySefUrlPart( $this->getVar('id'), $drp_name );	
	}
	
	function get_frombox_init() 
	{
		$pos = new JLMS_SEF_VarsPositions( 'get_frombox' );
		$pos->addVar( 'course_id', '', '', 'id' );
		$pos->addVar( '', 'dropbox' );
		$pos->addVar( 'id', '' );																					
		$this->_positions[] = $pos;						
	}
	
	function mailbox_build() {
		$course_name = $this->_tObj->get( 'courses', $this->getVar('id') );	
			
		$this->_res[] = $this->keySefUrlPart( $this->getVar('id'), $course_name );
		$this->_res[] = 'mailbox';		
	}
	
	function mailbox_init() 
	{
		$pos = new JLMS_SEF_VarsPositions( 'mailbox' );
		$pos->addVar( 'id', '', '', 'id' );
		$pos->addVar( '', 'mailbox' );																					
		$this->_positions[] = $pos;						
	}
	
	function mail_view_build() {
		$course_name = $this->_tObj->get( 'courses', $this->getVar('id') );
		$subject = $this->_tObj->get( 'messages', $this->getVar('view_id') );
		
		$this->_res[] = $this->keySefUrlPart( $this->getVar('id'), $course_name );
		$this->_res[] = 'mailbox';								
		$this->_res[] = $this->keySefUrlPart( $this->getVar('view_id'), $subject );
		
		$this->_nonSefVars[] = 'inb';
	}
	
	function mail_view_init() 
	{
		$pos = new JLMS_SEF_VarsPositions( 'mail_view' );
		$pos->addVar( 'id', '', '', 'id' );
		$pos->addVar( '', 'mailbox' );
		$pos->addVar( 'view_id', '', '', 'id' );																		
		$this->_positions[] = $pos;			
	}
	
	function mail_sendbox_build() {
		$course_name = $this->_tObj->get( 'courses', $this->getVar('id') );		
		$this->_res[] = $this->keySefUrlPart( $this->getVar('id'), $course_name );
		$this->_res[] = 'mailbox';
		$this->_res[] = 'outbox';	
	}
	
	function mail_sendbox_init() 
	{
		$pos = new JLMS_SEF_VarsPositions( 'mail_sendbox' );
		$pos->addVar( 'id', '', '', 'id' );
		$pos->addVar( '', 'mailbox' );
		$pos->addVar( '', 'outbox' );																				
		$this->_positions[] = $pos;				
	}
	
	function view_assistants_build() {
		$course_name = $this->_tObj->get( 'courses', $this->getVar('id') );	
			
		$this->_res[] = $this->keySefUrlPart( $this->getVar('id'), $course_name );
		$this->_res[] = 'users';
		$this->_res[] = 'managers';		
	}
	
	function view_assistants_init() 
	{
		$pos = new JLMS_SEF_VarsPositions( 'view_assistants' );
		$pos->addVar( 'id', '', '', 'id' );
		$pos->addVar( '', 'users' );
		$pos->addVar( '', 'managers' );																				
		$this->_positions[] = $pos;				
	}
	
	function view_users_build() {
		$course_name = $this->_tObj->get( 'courses', $this->getVar('course_id') );	
			
		$this->_res[] = $this->keySefUrlPart( $this->getVar('course_id'), $course_name );
		$this->_res[] = 'users';
		
		if($this->getVar('id') == 0)
			$this->_res[] = 'nogroup_users';
		else {			
			$group_name = $this->_tObj->get( 'usergroups', $this->getVar('id') );			
			$this->_res[] = $this->keySefUrlPart( $this->getVar('id'), $group_name );
		}		
	}
	
	function view_users_init() 
	{
		$pos = new JLMS_SEF_VarsPositions( 'view_users' );
		$pos->addVar( 'course_id', '', '', 'id' );
		$pos->addVar( '', 'users' );
		$pos->addVar( '', 'nogroup_users' );																			
		$this->_positions[] = $pos;
		
		$pos = new JLMS_SEF_VarsPositions( 'view_users' );
		$pos->addVar( 'course_id', '', '', 'id' );
		$pos->addVar( '', 'users' );
		$pos->addVar( 'id', '', '', 'id' );																			
		$this->_positions[] = $pos;				
	}
	
	function chat_build() {
		$course_name = $this->_tObj->get( 'courses', $this->getVar('id') );	
			
		$this->_res[] = $this->keySefUrlPart( $this->getVar('id'), $course_name );
		$this->_res[] = 'chat';		
	}
	
	function chat_init() 
	{
		$pos = new JLMS_SEF_VarsPositions( 'chat' );
		$pos->addVar( 'id', '', '', 'id' );		
		$pos->addVar( '', 'chat' );																			
		$this->_positions[] = $pos;				
	}
	
	function show_lpath_nojs_build() {
		$course_name = $this->_tObj->get( 'courses', $this->getVar('course_id') );
		$lpath_name = $this->_tObj->get( 'learn_paths', $this->getVar('id') );
		
		$this->_res[] = $this->keySefUrlPart( $this->getVar('course_id'), $course_name );			
		$this->_res[] = $this->keySefUrlPart( $this->getVar('id'), $lpath_name );
				
		switch ($this->getVar('action', 'word')) {
			case 'start_lpath':
				$this->_res[] = 'start';
			break;
			case 'seek_lpathstep':
				$this->_res[] = 'seek';
				$this->_res[] = $this->getVar('step_id');
				$this->_res[] = $this->getVar('user_start_id');
			break;	
		}		
	}
	
	function show_lpath_nojs_init() 
	{
		$pos = new JLMS_SEF_VarsPositions( 'show_lpath_nojs' );
		$pos->addVar( 'course_id', '', '', 'id' );
		$pos->addVar( 'id', '', '', 'id' );		
		$pos->addVar( 'action', 'start_lpath', 'start' );																
		$this->_positions[] = $pos;
		
		$pos = new JLMS_SEF_VarsPositions( 'show_lpath_nojs' );
		$pos->addVar( 'course_id', '', '', 'id' );
		$pos->addVar( 'id', '', '', 'id' );		
		$pos->addVar( 'action', 'seek_lpathstep', 'seek' );
		$pos->addVar( 'step_id', '', '', 'id' );
		$pos->addVar( 'user_start_id', '', '', 'id' );																	
		$this->_positions[] = $pos;				
	}       		
	
	function tracking_build() {
		$course_name = $this->_tObj->get( 'courses', $this->getVar('id') );	
			
		$this->_res[] = $this->keySefUrlPart( $this->getVar('id'), $course_name );
		$this->_res[] = 'tracking';
		
		$page = $this->getVar('page', 'int');
				
		switch ($page) {
		   	case 1:
		   		$this->_res[] = 'documents';
		   	break;
		   	case 2:
		   		$this->_res[] = 'links';	
		   	break;	
		   	case 3:
		   		$this->_res[] = 'dropbox';	
		   	break;	
		   	case 4:
		   		$this->_res[] = 'learn_paths';	
		   	break;	
		   	case 5:
		   		$this->_res[] = 'homework';	
		   	break;	
		   	case 6:
		   		$this->_res[] = 'announcements';	
		   	break;	
		   	case 7:
		   		$this->_res[] = 'conference';	
		   	break;	
		   	case 8:
		   		$this->_res[] = 'chat';	
		   	break;	
		   	case 9:
		   		$this->_res[] = 'lpath_player';	
		   	break;	
		   	case 10:
		   		$this->_res[] = 'forum';	
		   	break;	
		   	case 11:
		   		$this->_res[] = 'quizzes';	
		   	break;
			case 12:
		   		$this->_res[] = 'downloads_statistics';	
		   	break;
			case 13:
		   		$this->_res[] = 'learnpaths_statistics';	
		   	break;
			case 14:
		   		$this->_res[] = 'latest_activities_report';	
		   	break;	
		}		
		
						
		$this->_nonSefVars[] = 'filter_stu';
		$this->_nonSefVars[] = 'filter_lpath';
		$this->_nonSefVars[] = 'filter_year';
		$this->_nonSefVars[] = 'filter_month';
		$this->_nonSefVars[] = 'filter_day';
		$this->_nonSefVars[] = 'filt_group';
		$this->_nonSefVars[] = 'filt_subgroup';			
	}
	
	function tracking_init() 
	{
		$pos = new JLMS_SEF_VarsPositions( 'tracking' );
		$pos->addVar( 'id', '', '', 'id' );					
		$pos->addVar( '', 'tracking', '' );																			
		$this->_positions[] = $pos;
		
		$pos = new JLMS_SEF_VarsPositions( 'tracking' );
		$pos->addVar( 'id', '', '', 'id' );					
		$pos->addVar( '', 'tracking', '' );
		$pos->addVar( 'page', '1', 'documents' );																	
		$this->_positions[] = $pos;
		
		$pos = new JLMS_SEF_VarsPositions( 'tracking' );
		$pos->addVar( 'id', '', '', 'id' );					
		$pos->addVar( '', 'tracking', '' );
		$pos->addVar( 'page', '2', 'links' );																	
		$this->_positions[] = $pos;
		
		$pos = new JLMS_SEF_VarsPositions( 'tracking' );
		$pos->addVar( 'id', '', '', 'id' );					
		$pos->addVar( '', 'tracking', '' );
		$pos->addVar( 'page', '3', 'dropbox' );																	
		$this->_positions[] = $pos;
		
		$pos = new JLMS_SEF_VarsPositions( 'tracking' );
		$pos->addVar( 'id', '', '', 'id' );					
		$pos->addVar( '', 'tracking', '' );
		$pos->addVar( 'page', '4', 'learn_paths' );																	
		$this->_positions[] = $pos;
		
		$pos = new JLMS_SEF_VarsPositions( 'tracking' );
		$pos->addVar( 'id', '', '', 'id' );					
		$pos->addVar( '', 'tracking', '' );
		$pos->addVar( 'page', '5', 'homework' );																	
		$this->_positions[] = $pos;
		
		$pos = new JLMS_SEF_VarsPositions( 'tracking' );
		$pos->addVar( 'id', '', '', 'id' );					
		$pos->addVar( '', 'tracking', '' );
		$pos->addVar( 'page', '6', 'announcements' );															
		$this->_positions[] = $pos;
		
		$pos = new JLMS_SEF_VarsPositions( 'tracking' );
		$pos->addVar( 'id', '', '', 'id' );					
		$pos->addVar( '', 'tracking', '' );
		$pos->addVar( 'page', '7', 'conference' );																	
		$this->_positions[] = $pos;
		
		$pos = new JLMS_SEF_VarsPositions( 'tracking' );
		$pos->addVar( 'id', '', '', 'id' );					
		$pos->addVar( '', 'tracking', '' );
		$pos->addVar( 'page', '8', 'chat' );																	
		$this->_positions[] = $pos;
		
		$pos = new JLMS_SEF_VarsPositions( 'tracking' );
		$pos->addVar( 'id', '', '', 'id' );					
		$pos->addVar( '', 'tracking', '' );
		$pos->addVar( 'page', '9', 'lpath_player' );																
		$this->_positions[] = $pos;
		
		$pos = new JLMS_SEF_VarsPositions( 'tracking' );
		$pos->addVar( 'id', '', '', 'id' );					
		$pos->addVar( '', 'tracking', '' );
		$pos->addVar( 'page', '10', 'forum' );																	
		$this->_positions[] = $pos;
		
		$pos = new JLMS_SEF_VarsPositions( 'tracking' );
		$pos->addVar( 'id', '', '', 'id' );					
		$pos->addVar( '', 'tracking', '' );
		$pos->addVar( 'page', '11', 'quizzes' );																	
		$this->_positions[] = $pos;
		
		$pos = new JLMS_SEF_VarsPositions( 'tracking' );
		$pos->addVar( 'id', '', '', 'id' );					
		$pos->addVar( '', 'tracking', '' );
		$pos->addVar( 'page', '12', 'downloads_statistics' );																	
		$this->_positions[] = $pos;
		
		$pos = new JLMS_SEF_VarsPositions( 'tracking' );
		$pos->addVar( 'id', '', '', 'id' );					
		$pos->addVar( '', 'tracking', '' );
		$pos->addVar( 'page', '13', 'learnpaths_statistics' );																	
		$this->_positions[] = $pos;
		
		$pos = new JLMS_SEF_VarsPositions( 'tracking' );
		$pos->addVar( 'id', '', '', 'id' );					
		$pos->addVar( '', 'tracking', '' );
		$pos->addVar( 'page', '14', 'latest_activities_report' );																
		$this->_positions[] = $pos;						
	}    
		
	
	function track_clear_build() {
		$course_name = $this->_tObj->get( 'courses', $this->getVar('id') );	
			
		$this->_res[] = $this->keySefUrlPart( $this->getVar('id'), $course_name );
		$this->_res[] = 'clear_statistics';		
	}
	
	function track_clear_init() 
	{
		$pos = new JLMS_SEF_VarsPositions( 'track_clear' );
		$pos->addVar( 'id', '', '', 'id' );					
		$pos->addVar( '', 'clear_statistics', '' );																		
		$this->_positions[] = $pos;			
	}
	
	function course_users_build() {
		$course_name = $this->_tObj->get( 'courses', $this->getVar('id') );	
			
		$this->_res[] = $this->keySefUrlPart( $this->getVar('id'), $course_name );
		$this->_res[] = 'course_users';
		
		$group_id = $this->getVar('group_id'); 
		if( $group_id ) {
			$ug_name = $this->_tObj->get( 'usergroups', $group_id );			
			$this->_res[] = $this->keySefUrlPart( $group_id, $ug_name );
		}		
	}
	
	function course_users_init() 
	{
		$pos = new JLMS_SEF_VarsPositions( 'course_users' );
		$pos->addVar( 'id', '', '', 'id' );					
		$pos->addVar( '', 'course_users', '' );																		
		$this->_positions[] = $pos;
				
		$pos = new JLMS_SEF_VarsPositions( 'course_users' );
		$pos->addVar( 'id', '', '', 'id' );					
		$pos->addVar( '', 'course_users', '' );
		$pos->addVar( 'group_id', '', '', 'id' );	
																			
		$this->_positions[] = $pos;			
	}
	
	function settings_build() {
		$course_name = $this->_tObj->get( 'courses', $this->getVar('id') );	
			
		$this->_res[] = $this->keySefUrlPart( $this->getVar('id'), $course_name );
		$this->_res[] = 'settings';		
	}
	
	function settings_init() 
	{
		$pos = new JLMS_SEF_VarsPositions( 'settings' );
		$pos->addVar( 'id', '', '', 'id' );					
		$pos->addVar( '', 'settings', '' );																		
		$this->_positions[] = $pos;			
	}
	
	function edit_course_build() {
		$course_name = $this->_tObj->get( 'courses', $this->getVar('id') );		
		
		$this->_res[] = $this->keySefUrlPart( $this->getVar('id'), $course_name );
		$this->_res[] = 'edit';
														
		$this->_nonSefVars[] = 'is_inside';					
	}
	
	function edit_course_init() 
	{
		$pos = new JLMS_SEF_VarsPositions( 'edit_course' );
		$pos->addVar( 'id', '', '', 'id' );					
		$pos->addVar( '', 'edit', '' );																		
		$this->_positions[] = $pos;			
	}
	
	function course_forum_build() {
		$course_name = $this->_tObj->get( 'courses', $this->getVar('id') );	
			
		$this->_res[] = $this->keySefUrlPart( $this->getVar('id'), $course_name );
		$this->_res[] = 'forum';		
		
		$this->_nonSefVars[] = 'topic_id';
		$this->_nonSefVars[] = 'message_id';
	}
	
	function course_forum_init() 
	{
		$pos = new JLMS_SEF_VarsPositions( 'course_forum' );
		$pos->addVar( 'id', '', '', 'id' );					
		$pos->addVar( '', 'forum', '' );																		
		$this->_positions[] = $pos;			
	}
	
	function conference_build() 
	{	
		$mode = $this->getVar('mode', 'word');
				
		if( $mode && $mode != 'archive' )	{
			$course_name = $this->_tObj->get( 'courses', $this->getVar('course_id') );
			$this->_res[] = $this->keySefUrlPart( $this->getVar('course_id'), $course_name );
		} else {		
			$course_name = $this->_tObj->get( 'courses', $this->getVar('id') );
			$this->_res[] = $this->keySefUrlPart( $this->getVar('id'), $course_name );
		}
		
		if( $mode != 'conference_room' ) 
		{		
			$this->_res[] = 'conference'; 
		}		
		
		switch( $mode ) 
		{
			case 'archive':
				$this->_res[] = 'archive';
			break;
			case 'conference_room':
				$this->_res[] = 'conference_room';
			break;
			case 'conference_playback':
				$this->_res[] = 'conference_playback';
			break;
			case 'profile':
				$this->_res[] = 'profile';
			break;
			case 'param_request':
				$this->_res[] = 'param_request';
			break;
			case 'upload_popup':
				$this->_res[] = 'upload_popup';
			break; 
		}
		
		$this->_nonSefVars[] = 'name';
		$this->_nonSefVars[] = 'username';
		$this->_nonSefVars[] = 'recorded_session';				
	}
	
	function conference_init() 
	{
		$pos = new JLMS_SEF_VarsPositions( 'conference' );
		$pos->addVar( 'id', '', '', 'id' );					
		$pos->addVar( '', 'conference', '' );																		
		$this->_positions[] = $pos;
		
		$pos = new JLMS_SEF_VarsPositions( 'conference' );
		$pos->addVar( 'id', '', '', 'id' );					
		$pos->addVar( '', 'conference', '' );
		$pos->addVar( 'mode', 'archive' );																		
		$this->_positions[] = $pos;
		
		$pos = new JLMS_SEF_VarsPositions( 'conference' );
		$pos->addVar( 'course_id', '', '', 'id' );					
		$pos->addVar( 'mode', 'conference_room' );																				
		$this->_positions[] = $pos;
		
		$pos = new JLMS_SEF_VarsPositions( 'conference' );
		$pos->addVar( 'course_id', '', '', 'id' );					
		$pos->addVar( '', 'conference', '' );
		$pos->addVar( 'mode', 'conference_playback' );																		
		$this->_positions[] = $pos;
		
		$pos = new JLMS_SEF_VarsPositions( 'conference' );
		$pos->addVar( 'course_id', '', '', 'id' );					
		$pos->addVar( '', 'conference', '' );
		$pos->addVar( 'mode', 'profile' );																		
		$this->_positions[] = $pos;
		
		$pos = new JLMS_SEF_VarsPositions( 'conference' );
		$pos->addVar( 'course_id', '', '', 'id' );					
		$pos->addVar( '', 'conference', '' );
		$pos->addVar( 'mode', 'param_request' );																		
		$this->_positions[] = $pos;
		
		$pos = new JLMS_SEF_VarsPositions( 'conference' );
		$pos->addVar( 'course_id', '', '', 'id' );					
		$pos->addVar( '', 'conference', '' );
		$pos->addVar( 'mode', 'upload_popup' );																		
		$this->_positions[] = $pos;		
	}
	
	function at_pre_export_build() {
		$course_name = $this->_tObj->get( 'courses', $this->getVar('id') );	
			
		$this->_res[] = $this->keySefUrlPart( $this->getVar('id'), $course_name );
		$this->_res[] = 'at_pre_export';		
	}
	
	function at_pre_export_init() 
	{
		$pos = new JLMS_SEF_VarsPositions( 'at_pre_export' );
		$pos->addVar( 'id', '', '', 'id' );					
		$pos->addVar( '', 'at_pre_export', '' );																		
		$this->_positions[] = $pos;	
	}
	
	function ceo_page_build() {
		$this->_res[] = 'ceo_page';	
	}
	
	function ceo_page_init() 
	{
		$pos = new JLMS_SEF_VarsPositions( 'ceo_page' );							
		$pos->addVar( '', 'ceo_page', '' );																		
		$this->_positions[] = $pos;	
	}
	
	function new_outfolder_build() {
		$this->_res[] = 'library';
		$this->_res[] = 'new_folder';
	}
	
	function new_outfolder_init() 
	{
		$pos = new JLMS_SEF_VarsPositions( 'new_outfolder' );
		$pos->addVar( '', 'library', '' );							
		$pos->addVar( '', 'new_folder', '' );																		
		$this->_positions[] = $pos;	
	}
	
	function new_outdocs_build() {
		$this->_res[] = 'library';
		$this->_res[] = 'new_document';	
	}
	
	function new_outdocs_init() 
	{
		$pos = new JLMS_SEF_VarsPositions( 'new_outdocs' );
		$pos->addVar( '', 'library', '' );							
		$pos->addVar( '', 'new_document', '' );																		
		$this->_positions[] = $pos;	
	}
		
	function outdocs_build() {
		$this->_res[] = 'library';
		
		$this->_nonSefVars[] = 'folder';
		$this->_nonSefVars[] = 'element';	
	}
	
	function outdocs_init() 
	{
		$pos = new JLMS_SEF_VarsPositions( 'outdocs' );							
		$pos->addVar( '', 'library', '' );																		
		$this->_positions[] = $pos;	
	}
	
	function outdocs_view_content_build() {
		$doc_name = $this->_tObj->get( 'outer_documents', $this->getVar('id') );
		
		$this->_res[] = 'library';
		$this->_res[] = 'view_content';		
		$this->_res[] = $this->keySefUrlPart( $this->getVar('id'), $doc_name );
			
		$this->_nonSefVars[] = 'force';
	}
	
	function outdocs_view_content_init() 
	{
		$pos = new JLMS_SEF_VarsPositions( 'outdocs_view_content' );							
		$pos->addVar( '', 'library', '' );
		$pos->addVar( '', 'view_content', '' );
		$pos->addVar( 'id', '', '', 'id' );																		
		$this->_positions[] = $pos;	
	}
	
	function get_outdoc_build() {
		$doc_name = $this->_tObj->get( 'outer_documents', $this->getVar('id') );
		
		$this->_res[] = 'library';		
		$this->_res[] = $this->keySefUrlPart( $this->getVar('id'), $doc_name );
			
		$this->_nonSefVars[] = 'force';
	}
	
	function get_outdoc_init() 
	{
		$pos = new JLMS_SEF_VarsPositions( 'get_outdoc' );							
		$pos->addVar( '', 'library', '' );
		$pos->addVar( 'id', '', '', 'id' );																		
		$this->_positions[] = $pos;		
	}
	
	function new_usergroup_build() {
		$course_name = $this->_tObj->get( 'courses', $this->getVar('id') );	
			
		$this->_res[] = $this->keySefUrlPart( $this->getVar('id'), $course_name );
		$this->_res[] = 'new_usergroup';		
	}
	
	function new_usergroup_init() 
	{
		$pos = new JLMS_SEF_VarsPositions( 'new_usergroup' );		
		$pos->addVar( 'id', '', '', 'id' );					
		$pos->addVar( '', 'new_usergroup', '' );													
		$this->_positions[] = $pos;		
	}
	
	function user_csv_delete_build() {
		$course_name = $this->_tObj->get( 'courses', $this->getVar('id') );	
			
		$this->_res[] = $this->keySefUrlPart( $this->getVar('id'), $course_name );
		$this->_res[] = 'user_csv_delete';		
	}
	
	function user_csv_delete_init() 
	{
		$pos = new JLMS_SEF_VarsPositions( 'user_csv_delete' );		
		$pos->addVar( 'id', '', '', 'id' );					
		$pos->addVar( '', 'user_csv_delete', '' );													
		$this->_positions[] = $pos;		
	}
	
	function add_user_build() {
		$course_name = $this->_tObj->get( 'courses', $this->getVar('course_id') );
				
		$this->_res[] = $this->keySefUrlPart( $this->getVar('course_id'), $course_name );
		$this->_res[] = 'users';
		$this->_res[] = 'add_user';		
	}
	
	function add_user_init() 
	{
		$pos = new JLMS_SEF_VarsPositions( 'add_user' );		
		$pos->addVar( 'course_id', '', '', 'id' );					
		$pos->addVar( '', 'users', '' );
		$pos->addVar( '', 'add_user', '' );													
		$this->_positions[] = $pos;		
	}
	
	function course_guest_build() {
		$course_name = $this->_tObj->get( 'courses', $this->getVar('id') );
										
		$this->_res[] = 'preview';					
		$this->_res[] = $this->keySefUrlPart( $this->getVar('id'), $course_name );						
	}
	
	function course_guest_init() 
	{
		$pos = new JLMS_SEF_VarsPositions( 'course_guest' );
		$pos->addVar( '', 'preview', '' );		
		$pos->addVar( 'id', '', '', 'id' );															
		$this->_positions[] = $pos;		
	}
	
	function topic_build() {
		$course_name = $this->_tObj->get( 'courses', $this->getVar('course_id') );
		$course_name = $this->_tObj->get( 'topics', $this->getVar('topic_id') );
				
		$this->_res[] = $this->keySefUrlPart( $this->getVar('course_id'), $course_name );
		$this->_res[] = 'topics';				
		$this->_res[] = $this->keySefUrlPart( $this->getVar('topic_id'), $course_name );
		
		$this->_nonSefVars[] = 'short';		
	}
	
	function topic_init() 
	{
		$pos = new JLMS_SEF_VarsPositions( 'topic' );
		$pos->addVar( 'id', '', '', 'id' );
		$pos->addVar( '', 'topics', '' );
		$pos->addVar( 'topic_id', '', '', 'id' );																	
		$this->_positions[] = $pos;		
	}
		
	function add_topic_build() {
		$course_name = $this->_tObj->get( 'courses', $this->getVar('id') );	
			
		$this->_res[] = $this->keySefUrlPart( $this->getVar('id'), $course_name );
		$this->_res[] = 'topics';
		$this->_res[] = 'new';		
	}
	
	function add_topic_init() 
	{
		$pos = new JLMS_SEF_VarsPositions( 'add_topic' );
		$pos->addVar( 'id', '', '', 'id' );
		$pos->addVar( '', 'topics', '' );
		$pos->addVar( '', 'new', '' );																	
		$this->_positions[] = $pos;		
	}
	
	function gbs_new_build() {
		$course_name = $this->_tObj->get( 'courses', $this->getVar('course_id') );	
			
		$this->_res[] = $this->keySefUrlPart( $this->getVar('course_id'), $course_name );
		$this->_res[] = 'scales';
		$this->_res[] = 'new';	
	}
	
	function gbs_new_init() 
	{
		$pos = new JLMS_SEF_VarsPositions( 'gbs_new' );
		$pos->addVar( 'course_id', '', '', 'id' );
		$pos->addVar( '', 'scales', '' );
		$pos->addVar( '', 'new', '' );																	
		$this->_positions[] = $pos;		
	}
	
	function gbs_editA_build() {
		$course_name = $this->_tObj->get( 'courses', $this->getVar('course_id') );
		$scale_name = $this->_tObj->get( 'gradebook_scale', $this->getVar('id') );
		
		$this->_res[] = $this->keySefUrlPart( $this->getVar('course_id'), $course_name );
		$this->_res[] = 'gradebook';
		$this->_res[] = 'scales';				
		$this->_res[] = $this->keySefUrlPart( $this->getVar('id'), $scale_name );		
		$this->_res[] = 'edit';		
	}
	
	function gbs_editA_init() 
	{
		$pos = new JLMS_SEF_VarsPositions( 'gbs_editA' );
		$pos->addVar( 'course_id', '', '', 'id' );
		$pos->addVar( '', 'gradebook', '' );
		$pos->addVar( '', 'scales', '' );
		$pos->addVar( 'id', '', '', 'id' );
		$pos->addVar( '', 'edit' );																		
		$this->_positions[] = $pos;		
	}
	
	function gbi_new_build() {
		$course_name = $this->_tObj->get( 'courses', $this->getVar('id') );	
			
		$this->_res[] = $this->keySefUrlPart( $this->getVar('id'), $course_name );
		$this->_res[] = 'gradebook';
		$this->_res[] = 'items';
		$this->_res[] = 'new';
	}
	
	function gbi_new_init() 
	{
		$pos = new JLMS_SEF_VarsPositions( 'gbi_new' );
		$pos->addVar( 'id', '', '', 'id' );
		$pos->addVar( '', 'gradebook', '' );
		$pos->addVar( '', 'items', '' );
		$pos->addVar( '', 'new' );																				
		$this->_positions[] = $pos;		
	}
	
	function hw_view_build() {
		$course_name = $this->_tObj->get( 'courses', $this->getVar('course_id') );
		$hw_name = $this->_tObj->get( 'homework', $this->getVar('id') );
		
		$this->_res[] = $this->keySefUrlPart( $this->getVar('course_id'), $course_name );
		$this->_res[] = 'homework';				
		$this->_res[] = $this->keySefUrlPart( $this->getVar('id'), $hw_name );		
		$this->_res[] = 'preview';		
	}
	
	function hw_view_init() 
	{
		$pos = new JLMS_SEF_VarsPositions( 'hw_view' );
		$pos->addVar( 'course_id', '', '', 'id' );
		$pos->addVar( '', 'homework', '' );
		$pos->addVar( 'id', '', '', 'id' );
		$pos->addVar( '', 'preview', '' );																				
		$this->_positions[] = $pos;		
	}
	
	function loadsco_scorm_build() 
	{
		$course_name = $this->_tObj->get( 'courses', $this->getVar('course_id') );
		
		$this->_res[] = $this->keySefUrlPart( $this->getVar('course_id'), $course_name );
		$this->_res[] = 'scorm_api';					
		$this->_res[] = $this->getVar('id');		
		$this->_res[] = $this->keySefUrlPart( $this->getVar('scoid'), 'loadsco' );	
	}
	
	function loadsco_scorm_init() 
	{
		$pos = new JLMS_SEF_VarsPositions( 'loadsco_scorm' );
		$pos->addVar( 'course_id', '', '', 'id' );
		$pos->addVar( '', 'scorm_api', '' );
		$pos->addVar( 'id', '', '', 'id' );		
		$pos->addVar( 'scoid', '', '', 'id' );																				
		$this->_positions[] = $pos;
		
		$pos = new JLMS_SEF_VarsPositions( 'loadsco_scorm' );		
		$pos->addVar( '', 'scorm_api', '' );
		$pos->addVar( 'id', '', '', 'id' );		
		$pos->addVar( 'scoid', '', '', 'id' );																				
		$this->_positions[] = $pos;		
	}
	
	function preview_scorm_build() 
	{
		$course_name = $this->_tObj->get( 'courses', $this->getVar('course_id') );
		
		$this->_res[] = $this->keySefUrlPart( $this->getVar('course_id'), $course_name );
		$this->_res[] = $this->getVar('id');
		$this->_res[] = $this->getVar('lpath_id');
	}
	
	function preview_scorm_init() 
	{
		$pos = new JLMS_SEF_VarsPositions( 'preview_scorm' );
		$pos->addVar( 'course_id', '', '', 'id' );		
		$pos->addVar( 'id', '', '', 'id' );
		$pos->addVar( 'lpath_id', '', '' );																			
		$this->_positions[] = $pos;		
	}
	
	function gb_get_cert_build() 
	{
		$course_name = $this->_tObj->get( 'courses', $this->getVar('id') );
		$user_name = $this->_tObj->get( 'users', $this->getVar('user_id') );	
			
		$this->_res[] = $this->keySefUrlPart( $this->getVar('id'), $course_name );
		$this->_res[] = 'gradebook';
		$this->_res[] = $this->keySefUrlPart( $this->getVar('user_id'), $user_name );
		$this->_res[] = 'get_certificate';			
	}
	
	function gb_get_cert_init() 
	{
		$pos = new JLMS_SEF_VarsPositions( 'gb_get_cert' );
		$pos->addVar( 'id', '', '', 'id' );
		$pos->addVar( '', 'gradebook' );	
		$pos->addVar( '', 'get_certificate', '' );																		
		$this->_positions[] = $pos;		
		
		$pos = new JLMS_SEF_VarsPositions( 'gb_get_cert' );
		$pos->addVar( 'id', '', '', 'id' );
		$pos->addVar( '', 'gradebook' );
		$pos->addVar( 'user_id', '', '', 'id' );	
		$pos->addVar( '', 'get_certificate', '' );																		
		$this->_positions[] = $pos;		
	}
	
	function download_scorm_build() 
	{
		$course_name = $this->_tObj->get( 'courses', $this->getVar('course_id') );
		$lpath_name = $this->_tObj->get( 'learn_paths', $this->getVar('id') );
		
        $this->_res[] = $this->keySefUrlPart( $this->getVar('course_id'), $course_name );
		$this->_res[] = 'learn-paths';				
		$this->_res[] = $this->keySefUrlPart( $this->getVar('id'), $lpath_name );        
		$this->_res[] = 'download';		
	}
	
	function download_scorm_init() 
	{
		$pos = new JLMS_SEF_VarsPositions( 'download_scorm' );
		$pos->addVar( 'course_id', '', '', 'id' );
		$pos->addVar( '', 'learn-paths' );	
		$pos->addVar( 'id', '', '', 'id' );
		$pos->addVar( '', 'download' );																		
		$this->_positions[] = $pos;		
	}
	
	function publish_topic_build() {
		$course_name = $this->_tObj->get( 'courses', $this->getVar('id') );
		$name = $this->_tObj->get( 'topics', $this->getVar('topic_id') );
		
		$this->_res[] = $this->keySefUrlPart( $this->getVar('id'), $course_name );		
		$this->_res[] = 'topics';				
		$this->_res[] = $this->keySefUrlPart( $this->getVar('topic_id'), $name );
		$this->_res[] = 'change';		
		
		$this->_nonSefVars[] = 'state';			
	}
	
	function publish_topic_init() 
	{
		$pos = new JLMS_SEF_VarsPositions( 'publish_topic' );
		$pos->addVar( 'id', '', '', 'id' );
		$pos->addVar( '', 'topics' );	
		$pos->addVar( 'topic_id', '', '', 'id' );
		$pos->addVar( '', 'change', '' );																		
		$this->_positions[] = $pos;		
	}
	
	function edit_topic_build() {					
		$course_name = $this->_tObj->get( 'courses', $this->getVar('id') );
		$name = $this->_tObj->get( 'topics', $this->getVar('topic_id') );
		
		$this->_res[] = $this->keySefUrlPart( $this->getVar('id'), $course_name );		
		$this->_res[] = 'topics';		
		$this->_res[] = $this->keySefUrlPart( $this->getVar('topic_id'), $name );		
		$this->_res[] = 'edit';	
	}
	
	function edit_topic_init() 
	{
		$pos = new JLMS_SEF_VarsPositions( 'edit_topic' );
		$pos->addVar( 'id', '', '', 'id' );
		$pos->addVar( '', 'topics' );	
		$pos->addVar( 'topic_id', '', '', 'id' );
		$pos->addVar( '', 'edit' );																		
		$this->_positions[] = $pos;	
	}
	
	function spec_reg_build() {
		$course_name = $this->_tObj->get( 'courses', $this->getVar('id') );		
		
		$this->_res[] = $this->keySefUrlPart( $this->getVar('id'), $course_name );		
		$this->_res[] = 'registration';
	}
	
	function spec_reg_init() 
	{
		$pos = new JLMS_SEF_VarsPositions( 'spec_reg' );
		$pos->addVar( 'id', '', '', 'id' );		
		$pos->addVar( '', 'registration' );																		
		$this->_positions[] = $pos;	
	}
	
	function docs_choose_startup_build() {
		$course_name = $this->_tObj->get( 'courses', $this->getVar('id') );
		$doc_name = $this->_tObj->get( 'documents', $this->getVar('doc_id') );
		
		$this->_res[] = $this->keySefUrlPart( $this->getVar('id'), $course_name );		
		$this->_res[] = 'documents';				
		$this->_res[] = $this->keySefUrlPart( $this->getVar('doc_id'), $doc_name );		
		$this->_res[] = 'select_index';		
	}
	
	function docs_choose_startup_init() 
	{
		$pos = new JLMS_SEF_VarsPositions( 'docs_choose_startup' );
		$pos->addVar( 'id', '', '', 'id' );		
		$pos->addVar( '', 'documents' );
		$pos->addVar( 'doc_id', '', '', 'id' );
		$pos->addVar( '', 'select_index' );																				
		$this->_positions[] = $pos;	
	}
	
	function docs_view_zip_build() {
		$course_name = $this->_tObj->get( 'courses', $this->getVar('course_id') );
		$doc_name = $this->_tObj->get( 'documents', $this->getVar('id') );
		
		$this->_res[] = $this->keySefUrlPart( $this->getVar('course_id'), $course_name );		
		$this->_res[] = 'documents';
		$this->_res[] = 'zips';					
		$this->_res[] = $this->keySefUrlPart( $this->getVar('id'), $doc_name );
	}
	
	function docs_view_zip_init() 
	{
		$pos = new JLMS_SEF_VarsPositions( 'docs_view_zip' );
		$pos->addVar( 'course_id', '', '', 'id' );		
		$pos->addVar( '', 'documents' );
		$pos->addVar( '', 'zips' );
		$pos->addVar( 'id', '', '', 'id' );																				
		$this->_positions[] = $pos;	
	}
	
	function callback_build() {
		
		$name = $this->_tObj->get( 'subscriptions_procs', $this->getVar('proc') );
		
		$this->_res[] = 'payments';		 
		$this->_res[] = $this->keySefUrlPart( $this->getVar('proc'), $name );		
		$this->_res[] = 'callback';	
	}
	
	function callback_init() 
	{
		$pos = new JLMS_SEF_VarsPositions( 'callback' );
		$pos->addVar( '', 'payments' );
		$pos->addVar( 'proc', '', '', 'id' );		
		$pos->addVar( '', 'callback' );																					
		$this->_positions[] = $pos;	
	}
	
	function change_element_build() {
		$course_name = $this->_tObj->get( 'courses', $this->getVar('id') );
		$name = $this->_tObj->get( 'topics', $this->getVar('topic_id') );
		
		$this->_res[] = $this->keySefUrlPart( $this->getVar('id'), $course_name );
		$this->_res[] = 'topics';		
		$this->_res[] = $this->keySefUrlPart( $this->getVar('topic_id'), $name );
		$this->_res[] = 'element';				
		$this->_res[] = $this->keySefUrlPart( $this->getVar('eid'), 'element' );
		$this->_res[] = 'change';	
		
		$this->_nonSefVars[] = 'state';				
	}
	
	function change_element_init() 
	{
		$pos = new JLMS_SEF_VarsPositions( 'change_element' );		
		$pos->addVar( 'id', '', '', 'id' );		
		$pos->addVar( '', 'topics' );
		$pos->addVar( 'topic_id', '', '', 'id' );
		$pos->addVar( '', 'element' );				
		$pos->addVar( 'eid', '', '', 'id' );
		$pos->addVar( '', 'change' );																				
		$this->_positions[] = $pos;		
	}
	
	function import_succesfull_build() {
		$this->_res[] = 'import_completed';
	}
	
	function import_succesfull_init() 
	{
		$pos = new JLMS_SEF_VarsPositions( 'import_succesfull' );				
		$pos->addVar( '', 'import_completed' );																		
		$this->_positions[] = $pos;	
	}
	
	function orderdown_element_build() {
		$course_name = $this->_tObj->get( 'courses', $this->getVar('id') );
		$name = $this->_tObj->get( 'topics', $this->getVar('topic_id') );
		
		$this->_res[] = $this->keySefUrlPart( $this->getVar('id'), $course_name );		
		$this->_res[] = 'topics';				
		$this->_res[] = $this->keySefUrlPart( $this->getVar('topic_id'), $name );						
		$this->_res[] = $this->keySefUrlPart( $this->getVar('element_ordering'), 'element_ordering' );		
		$this->_res[] = 'orderdown';	
	}
	
	function orderdown_element_init() 
	{
		$pos = new JLMS_SEF_VarsPositions( 'orderdown_element' );
		$pos->addVar( 'id', '', '', 'id' );				
		$pos->addVar( '', 'topics' );	
		$pos->addVar( 'topic_id', '', '', 'id' );		
		$pos->addVar( 'element_ordering', '', '', 'id' );
		$pos->addVar( '', 'orderdown' );																				
		$this->_positions[] = $pos;	
	}
	
	function orderup_element_build() {
		$course_name = $this->_tObj->get( 'courses', $this->getVar('id') );
		$name = $this->_tObj->get( 'topics', $this->getVar('topic_id') );
		
		$this->_res[] = $this->keySefUrlPart( $this->getVar('id'), $course_name );		
		$this->_res[] = 'topics';			
		$this->_res[] = $this->keySefUrlPart( $this->getVar('topic_id'), $name );
		$this->_res[] = $this->keySefUrlPart( $this->getVar('element_ordering'), 'element_ordering' );		
		$this->_res[] = 'orderup';		
	}
	
	function orderup_element_init() 
	{
		$pos = new JLMS_SEF_VarsPositions( 'orderup_element' );
		$pos->addVar( 'id', '', '', 'id' );				
		$pos->addVar( '', 'topics' );	
		$pos->addVar( 'topic_id', '', '', 'id' );		
		$pos->addVar( 'element_ordering', '', '', 'id' );
		$pos->addVar( '', 'orderup' );																				
		$this->_positions[] = $pos;	
	}
	
	function drp_view_descr_build() {
		$course_name = $this->_tObj->get( 'courses', $this->getVar('course_id') );
		$drp_name = $this->_tObj->get( 'dropbox', $this->getVar('id') );
		
		$this->_res[] = $this->keySefUrlPart( $this->getVar('course_id'), $course_name );
		$this->_res[] = 'dropbox';			
		$this->_res[] = $this->keySefUrlPart( $this->getVar('id'), $drp_name );
		$this->_res[] = 'read_more';	
	}
	
	function drp_view_descr_init() 
	{
		$pos = new JLMS_SEF_VarsPositions( 'drp_view_descr' );
		$pos->addVar( 'course_id', '', '', 'id' );				
		$pos->addVar( '', 'dropbox' );	
		$pos->addVar( 'id', '', '', 'id' );		
		$pos->addVar( '', 'read_more' );																				
		$this->_positions[] = $pos;	
	}
	
	function mk_read_build() {
		$course_name = $this->_tObj->get( 'courses', $this->getVar('id') );
		$subject = $this->_tObj->get( 'messages', $this->getVar('view_id') );
		
		$this->_res[] = $this->keySefUrlPart( $this->getVar('id'), $course_name );		
		$this->_res[] = 'mailbox';			
		$this->_res[] = $this->keySefUrlPart( $this->getVar('view_id'), $subject );
		$this->_res[] = 'mark_as_read';		
	}
	
	function mk_read_init() 
	{
		$pos = new JLMS_SEF_VarsPositions( 'mk_read' );
		$pos->addVar( 'id', '', '', 'id' );				
		$pos->addVar( '', 'mailbox' );	
		$pos->addVar( 'view_id', '', '', 'id' );		
		$pos->addVar( '', 'mark_as_read' );																				
		$this->_positions[] = $pos;	
	}
	
	function mk_unread_build() {
		$course_name = $this->_tObj->get( 'courses', $this->getVar('id') );
		$subject = $this->_tObj->get( 'messages', $this->getVar('view_id') );
		
		$this->_res[] = $this->keySefUrlPart( $this->getVar('id'), $course_name );		
		$this->_res[] = 'mailbox';			
		$this->_res[] = $this->keySefUrlPart( $this->getVar('view_id'), $subject );
		$this->_res[] = 'mark_as_unread';		 
	}
	
	function mk_unread_init() 
	{
		$pos = new JLMS_SEF_VarsPositions( 'mk_unread' );
		$pos->addVar( 'id', '', '', 'id' );				
		$pos->addVar( '', 'mailbox' );	
		$pos->addVar( 'view_id', '', '', 'id' );		
		$pos->addVar( '', 'mark_as_unread' );																				
		$this->_positions[] = $pos;	
	}
	
	function default_build() {		
		if (isset($this->_data['groups_course']) && $this->_data['groups_course'] && intval($this->_data['groups_course']) == $this->_data['groups_course']) {
			$this->_res[] = $this->_data['groups_course'];
		} else 
		{
			if( $this->_isRouterPHP ) {						
				foreach( $this->_data AS $key=>$value ) 
				{
					if( $key != 'option' && $key != 'Itemid' && !in_array( $key, $this->getNonSefVars() ) )
						$this->_res[] = $key.','.$value;
				}
			}			
			
		}	
    }
    
    function default_parse() {
    	if( isset($this->_data[0]) ) {
			foreach( $this->_data AS $rVar ) 
			{
				$arr = explode( ',', $rVar );
				if( isset($arr[1]) )								
					$this->_res[$arr[0]] = $arr[1];
			}			 		            	
		}		
    }
    
    function buildSefSegments() {
									
		if( $this->_menuTitle )	
			$this->_res[] = $this->_menuTitle;
		
		/*	
		if( $this->getVar('tmpl', 'word') == 'component' ) 
		{
			$this->_res[] = 'tmplcomponent';			
		}	
		*/	
			
		if( isset($this->_data['task']) ) {
			$task = $this->_data['task'];			
			$method = $task.'_build';		
			
			if( method_exists( $this, $method ) ) 
			{						
				call_user_func( array( & $this, $method ) );																
			} else
				$this->default_build();			 
			
		} else {
			$this->default_build();
		}
				
		if( $this->_res )
			return $this->_res;
		else
			return array();		
	}
	
	function parseSefSegments() {		
		
		$methods = get_class_methods( 'JLMS_SEF' );		
				
		foreach( $methods AS $meth ) 
		{			
			if( strpos( $meth, '_init' ) !== false ) 
			{								
				call_user_func( array( & $this, $meth ) );				
			}
		}
						
		$dLength = count($this->_data);
				
		$possWithOutKey = array();
		
		$matches = array();
		
		$commaMathesCount = 0;
		foreach( $this->_data AS $var ) 
		{
			if( strpos( $var, ',' ) !== false ) 
			{
				$arr = explode(',', $var);				
				$this->_res[$arr[0]] = $arr[1];
				$commaMathesCount++;								
			}	
		}
		
		if( $commaMathesCount != $dLength) 
		{
			$this->_res = false;
		}
		
		if( !$this->_res ) {						
			foreach( $this->_positions AS $pos ) 
			{		 
				if( !$pos->isContainsKey() ) 
				{					
					$possWithOutKey[] = $pos;
					continue;  	
				} 				
				
				if( $pos->length() == $dLength ) 
				{						
					if( $pos->isIdentified( $this->_data ) ) 
					{										
						$matches[] = $pos;
					}
				}			
			}
								 		
			if( !count($matches) ) 
			{			
				foreach( $possWithOutKey AS $posWOK ) 
				{
					if( $posWOK->length() == $dLength ) 
					{				
						$this->_res = $posWOK->getVars( $this->_data );				
					}
				}	
			} else {
				$maxMatchesPosId = 0;
				$maxMatchesCount = 0;
				$matchesCount = 0;
				
				for( $i = 0;  $i< count($matches); $i++ ) 
				{
					$match = $matches[$i];
					$matchesCount = $match->getMatchesCount();
					
					if( $maxMatchesCount < $matchesCount ) 
					{
						$maxMatchesCount = $matchesCount;
						$maxMatchesPosId = $i; 
					}
				}
				
				$this->_res = $matches[$maxMatchesPosId]->getVars( $this->_data );			 
			}
		}
											  		  										
		if( $this->_res ) 
		{			
			return $this->_res;
		} else {
			$this->_res = $this->default_parse();
			if( $this->_res )
				return $this->_res;
			else
				return array();
		}	
	}	
	
	function setMenuTitle( $title ) {		
		$this->_menuTitle = $title;
	}
	
	function getNonSefVars() 
	{	
		$this->_nonSefVars[] = 'no_html';
		$this->_nonSefVars[] = 'tmpl';
		$this->_nonSefVars[] = 'ntask';
		$this->_nonSefVars[] = 'is_full';
		$this->_nonSefVars[] = 'start_date';
		$this->_nonSefVars[] = 'end_date';
		$this->_nonSefVars[] = 'filt_group';
		$this->_nonSefVars[] = 'filt_cat';	
		$this->_nonSefVars[] = 'user_unique_id';
		$this->_nonSefVars[] = 'stu_quiz_id';			
					
		if($this->_nonSefVars)			
			return $this->_nonSefVars;
		else
			return array();		
	}

	function _decodeSegments($segments)
	{
		$total = count($segments);
		for($i=0; $i<$total; $i++)  {
			$segments[$i] = preg_replace('/-/', ':', $segments[$i], 1);
		}

		return $segments;
	}
	
	
}

class JLMS_SEF_VarsPositions 
{
	var $_vars;
	
	var $_length = 0;
	
	var $_task;
	
	var $_isContainsKey = false;
	
	var $_matchCount = 0;	
	
	function JLMS_SEF_VarsPositions( $task ) 
	{
		$this->_task = $task;
		
		/*
		if( JLMS_SEF_VarsPositions::isTmplComponent() ) 
		{
			$this->addVar( 'tmpl', 'tmplcomponent', 'component' );			
		}
		*/		
	}
	/*
	function isTmplComponent( $value = false ) 
	{
		static $val; 
		
		if( !isset($val) ) 
		{		
			$val = $value;
		}	
		
		return $val;	
	}
	*/
	
	function getMatchesCount() 
	{
		return $this->_matchCount;
	}	
	
	function addVar( $name, $realVal = '', $sefVal = '', $valType = false ) 
	{
		
		$var = new stdClass();		
		$var->name = $name;
		$var->realVal = $realVal;
		$var->sefVal = $sefVal;		
		$var->valType = $valType;
				
		if( $var->realVal )
			$this->_isContainsKey = true;
										
		$this->_length++;	
						
		$this->_vars[] =  $var;					    
	}
	
	function getVars( $segments ) 
	{
		$res['task'] = $this->_task;
																									
		for( $i = 0; $i< count($segments); $i++ ) 
		{	
			$var = $this->_vars[$i];
											
			if( $var->name ) {				
				if( $var->realVal ) {																
					$res[ $var->name ] = $var->realVal;
				} else {					
					switch( $var->valType ) 
					{
						case 'id':													
							$arr = explode( '-', $segments[$i] );							
							$res[ $var->name ] = $arr[0];
						break;
						default:
							$res[ $var->name ] = $segments[$i];
					}					
				}
			}
		}		
																													
		return $res;  
	}
	
	function length() 
	{
		return $this->_length;
	}
	
	function isIdentified( $segments ) 
	{													
		for( $i = 0; $i< count($segments); $i++ ) 
		{		
			if( $this->_vars[$i]->sefVal ) 
			{
				if( $this->_vars[$i]->sefVal != $segments[$i] )				
					return false;
				else 
					$this->_matchCount++;		
			} else if ( $this->_vars[$i]->realVal ) 
			{
				if( $this->_vars[$i]->realVal != $segments[$i] )					
					return false;
				else 
					$this->_matchCount++;
			}											
		}	
				
		return true;		
	}
	
	function isContainsKey() 
	{
		return $this->_isContainsKey;
	}
} 

function getJLMSSefObject( $uri = '' ) {
	$res = new JLMS_SEF( $uri );
	
	return $res; 
}

class JLMS_SEFHelper {
	function buildJSRedirectfunction($link, $def_link, $vars, $fname = 'jlms_redirect') {

$js = "
		function ".$fname."(sel_element) {
			var id = sel_element.options[sel_element.selectedIndex].value;
			var redirect_url = '';
			switch (id) {";
	
	if( is_array( $vars ) && !empty( $vars ) ) {		
		foreach ($vars as $var) {
			$js .= "
						case '$var':
							redirect_url = '".JRoute::_(str_replace('{var}', $var, $link))."';
						break;
			";
		}
	}

$js .= "
				default:
					redirect_url = '".JRoute::_($def_link)."';
				break;
			}
			if (redirect_url) {
				top.location.href = redirect_url;
			}
		}
		";
		return $js;
	}
}

?>