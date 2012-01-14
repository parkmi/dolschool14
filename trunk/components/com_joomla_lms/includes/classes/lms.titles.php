<?php 
/**
* includes/classes/lms.titles.php
* (c) JoomaLMS eLearning Software http://www.joomlalms.com/
**/

defined( '_JEXEC' ) or die( 'Restricted access' );

class JLMSTitles {//}extends JObject {
			
	var $_cache;
	
	var $_db;
	
	function __construct()
	{
		$this->_db = JFactory::getDBO();	
	}
	
	static function &getInstance()
	{
		static $instance;
		
		if (!is_object($instance))
		{						
			$instance = new JLMSTitles(); 
		}

		return $instance;
	} 
	
	function get( $entity, $id ) 
	{
		if( !$id || !$entity ) return '';
		
		$title = '';
		
		if( isset($this->_cache[$entity][$id]) ) {
			$title = $this->_cache[$entity][$id];			
		} else {	
			switch( $entity ) 
			{
				case 'courses':													
					$this->_db->setQuery( "SELECT course_name FROM #__lms_courses WHERE id = ".$id );
					$title = $this->_db->loadResult();					 										
					$this->set( $entity, $id, $title );					
				break;
				case 'categories':													
					$this->_db->setQuery( "SELECT c_category FROM #__lms_course_cats WHERE id = ".$id );
					$title = $this->_db->loadResult();					 										
					$this->set( $entity, $id, $title );					
				break;
				case 'learn_paths':
					$this->_db->setQuery( "SELECT lpath_name FROM #__lms_learn_paths WHERE id = ".$id );
					$title = $this->_db->loadResult(); 
					$this->set( $entity, $id, $title );
				break;
				case 'learn_path_steps':
					$this->_db->setQuery( "SELECT step_name FROM #__lms_learn_path_steps WHERE id = ".$id );
					$title = $this->_db->loadResult(); 
					$this->set( $entity, $id, $title );
				break;
				case 'documents':
					$this->_db->setQuery( "SELECT doc_name FROM #__lms_documents WHERE id = ".$id );
					$title = $this->_db->loadResult(); 
					$this->set( $entity, $id, $title );
				break;
				case 'agenda':
					$this->_db->setQuery( "SELECT title FROM #__lms_agenda WHERE agenda_id = ".$id );
					$title = $this->_db->loadResult(); 
					$this->set( $entity, $id, $title );
				break;
				case 'quiz_t_quiz':
					$this->_db->setQuery( "SELECT c_title FROM #__lms_quiz_t_quiz WHERE c_id = ".$id );
					$title = $this->_db->loadResult(); 
					$this->set( $entity, $id, $title );
				break;
				case 'homework':
					$this->_db->setQuery( "SELECT hw_name FROM #__lms_homework WHERE id = ".$id );
					$title = $this->_db->loadResult();
					$this->set( $entity, $id, $title ); 
				break;
				case 'dropbox':
					$this->_db->setQuery( "SELECT drp_name FROM #__lms_dropbox WHERE id = ".$id );
					$title = $this->_db->loadResult(); 
					$this->set( $entity, $id, $title );
				break;
				case 'messages':
					$this->_db->setQuery("SELECT subject FROM #__lms_messages WHERE id = ".$id);
					$title = $this->_db->loadResult(); 
					$this->set( $entity, $id, $title );
				break;
				case 'usergroups':
					$this->_db->setQuery( "SELECT ug_name FROM #__lms_usergroups WHERE id = ".$id );
					$title = $this->_db->loadResult(); 
					$this->set( $entity, $id, $title );
				break;
				case 'topics':
					$this->_db->setQuery( "SELECT name FROM #__lms_topics WHERE id = ".$id );
					$title = $this->_db->loadResult();
					$this->set( $entity, $id, $title ); 
				break;
				case 'users':
					$this->_db->setQuery( "SELECT username FROM #__users WHERE id = ".$id );
					$title = $this->_db->loadResult(); 
					$this->set( $entity, $id, $title );
				break;
				case 'outer_documents':
					$this->_db->setQuery( "SELECT doc_name FROM #__lms_outer_documents WHERE id = ".$id );
					$title = $this->_db->loadResult(); 
					$this->set( $entity, $id, $title );
				break;
				case 'gradebook_scale':
					$this->_db->setQuery( "SELECT scale_name FROM #__lms_gradebook_scale WHERE id = ".$id );
					$title = $this->_db->loadResult(); 
					$this->set( $entity, $id, $title );
				break;
				case 'subscriptions_procs':
					$this->_db->setQuery( "SELECT name FROM #__lms_subscriptions_procs WHERE id = ".$id );
					$title = $this->_db->loadResult(); 
					$this->set( $entity, $id, $title );
				break;
				case 'quiz_t_question':
					$this->_db->setQuery( "SELECT c_question FROM #__lms_quiz_t_question WHERE c_id = ".$id );
					$title = $this->_db->loadResult(); 
					$this->set( $entity, $id, $title );
				break;
				case 'links':
					$this->_db->setQuery( "SELECT link_name FROM #__lms_links WHERE id = ".$id );
					$title = $this->_db->loadResult(); 
					$this->set( $entity, $id, $title );					
				break;				
				case 'gqp_cats':
					$this->_db->setQuery( "SELECT c_category FROM #__lms_gqp_cats WHERE id = ".$id );
					$title = $this->_db->loadResult(); 
					$this->set( $entity, $id, $title );					
				break;
				case 'quiz_t_category':
					$this->_db->setQuery( "SELECT c_category FROM #__lms_quiz_t_category WHERE c_id = ".$id );
					$title = $this->_db->loadResult(); 
					$this->set( $entity, $id, $title );					
				break;
				case 'files':
					$this->_db->setQuery( "SELECT file_name FROM #__lms_files WHERE id = ".$id );
					$title = $this->_db->loadResult(); 
					$this->set( $entity, $id, $title );					
				break;
				case 'homeworks':
					$this->_db->setQuery( "SELECT hw_name FROM #__lms_homework WHERE id = ".$id );
					$title = $this->_db->loadResult(); 
					$this->set( $entity, $id, $title );					
				break;
				case 'quiz_images':
					$this->_db->setQuery( "SELECT imgs_name FROM #__lms_quiz_images WHERE c_id = ".$id );
					$title = $this->_db->loadResult(); 
					$this->set( $entity, $id, $title );					
				break;
				case 'students':
					$this->_db->setQuery( "SELECT u.username FROM #__lms_quiz_r_student_quiz AS s, #__users AS u WHERE s.c_student_id = u.id AND s.c_id = ".$id );
					$title = $this->_db->loadResult(); 
					$this->set( $entity, $id, $title );					
				break;							
			}		
		}
		
		return $title;	
	}
	
	function set( $entity, $id, $title )   
	{
		$this->_cache[$entity][$id] = $title;			  	
	}
	
	function setArray( $entity, & $rows, $key, $value )   
	{
		if( count($rows) ) 
		{
			foreach( $rows AS $row ) 
			{				
				$this->_cache[$entity][$row->{$key}] = $row->{$value};	
			}
			
			return true;
		}	
		
		return false;			  	
	}
}
?>