<?php
/**
* joomlaquiz.class.php
* JoomlaQuiz plugin fo LMS
* * * ElearningForce DK
**/

// no direct access
defined( '_JLMS_EXEC' ) or die( 'Restricted access' );

class mos_Joomla_LMS_GQPCategories extends JLMSDBTable {
	var $id 					= null;
	var $c_category				= null;
	var $parent					= null;
	var $restricted				= null;
	var $groups					= null;
	var $lesson_type			= null;

	function mos_Joomla_LMS_GQPCategories( &$db ) {
		$this->JLMSDBTable( '#__lms_gqp_cats', 'id', $db );
	}

	function check() {
		return true;
	}
}

class mos_JoomQuiz_Cat extends JLMSDBTable {
	var $c_id 				= null;
	var $course_id			= null;
	var $c_category	 		= null;
	var $c_instruction 		= null;
	var $is_quiz_cat		= null;

	function mos_JoomQuiz_Cat( &$db ) {
		$this->JLMSDBTable( '#__lms_quiz_t_category', 'c_id', $db );
	}

	function check() {
		return true;
	}
} 
class mos_JoomQuiz_Quiz extends JLMSDBTable {
	var $c_id 				= null;
	var $course_id			= null;
	var $c_category_id 		= null;
	var $c_user_id			= null;
	var $c_author			= null;
	var $c_full_score		= null;
	var $c_title			= null;
	var $c_description		= null;
	var $c_image			= null;
	var $c_time_limit		= null;
	var $c_min_after		= null;
	var $c_passing_score	= null;
	var $c_created_time		= null;
	var $c_published		= null;
	var $c_right_message	= null;
	var $c_wrong_message	= null;
	var $c_pass_message		= null;
	var $c_unpass_message	= null;
	var $c_enable_review	= null;
	var $c_email_to			= null;
	var $c_enable_print		= null;
	var $c_enable_sertif	= null;
	var $c_skin				= null;
	var $c_random			= null;
	var $c_guest			= null;
	var $published			= null;
	var $c_slide			= null;
	var $c_language			= null;
	var $c_certificate		= null;
	var $c_gradebook		= null;
	var $params				= null;
	var $c_resume			= null;
	var $c_max_numb_attempts= null;
	var $is_time_related	= null;
	var $show_period		= null;

	
	function mos_JoomQuiz_Quiz( &$db ) {
		$this->JLMSDBTable( '#__lms_quiz_t_quiz', 'c_id', $db );
	}

	function check() {
		return true;
	}
} 
class mos_JoomQuiz_Question extends JLMSDBTable {
	var $c_id 				= null;
	var $course_id			= null;
	var $c_quiz_id	 		= null;
	var $c_point			= null;
	var $c_attempts			= null;
	var $c_question			= null;
	var $c_image			= null;
	var $c_type				= null;
	var $published			= null;
	var $ordering			= null;
	var $c_pool				= null;
	var $c_qcat				= null;
	var $params				= null;
	var $c_explanation		= null;
	var $c_pool_gqp			= null;

	function mos_JoomQuiz_Question( &$db ) {
		$this->JLMSDBTable( '#__lms_quiz_t_question', 'c_id', $db );
	}

	function check() {
		return true;
	}
} 
class mos_JoomQuiz_ChoiceField extends JLMSDBTable {
	var $c_id 				= null;
	var $c_choice	 		= null;
	var $c_right			= null;
	var $c_question_id		= null;
	var $ordering			= null;

	function mos_JoomQuiz_ChoiceField( &$db ) {
		$this->JLMSDBTable( '#__lms_quiz_t_choice', 'c_id', $db );
	}

	function check() {
		return true;
	}
} 
class mos_JoomQuiz_MatchField extends JLMSDBTable {
	var $c_id 				= null;
	var $c_question_id		= null;
	var $c_left_text		= null;
	var $c_right_text		= null;
	var $ordering			= null;

	function mos_JoomQuiz_MatchField( &$db ) {
		$this->JLMSDBTable( '#__lms_quiz_t_matching', 'c_id', $db );
	}

	function check() {
		return true;
	}
}

class mos_JoomQuiz_BlankTextField extends JLMSDBTable {
	var $c_id 				= null;
	var $c_blank_id			= null;
	var $c_text				= null;
	var $ordering			= null;

	function mos_JoomQuiz_BlankTextField( &$db ) {
		$this->JLMSDBTable( '#__lms_quiz_t_text', 'c_id', $db );
	}

	function check() {
		return true;
	}
}
class mos_JoomQuiz_HotSpotField extends JLMSDBTable {
	var $c_id 				= null;
	var $c_question_id		= null;
	var $c_start_x			= null;
	var $c_start_y			= null;
	var $c_width			= null;
	var $c_height			= null;

	function mos_JoomQuiz_HotSpotField( &$db ) {
		$this->JLMSDBTable( '#__lms_quiz_t_hotspot', 'c_id', $db );
	}

	function check() {
		return true;
	}
}
class mos_JoomQuiz_ScaleField extends JLMSDBTable {
	var $c_id 				= null;
	var $c_field	 		= null;
	var $c_type				= null;
	var $c_question_id		= null;
	var $ordering			= null;

	function mos_JoomQuiz_ScaleField( &$db ) {
		$this->JLMSDBTable( '#__lms_quiz_t_scale', 'c_id', $db );
	}

	function check() {
		return true;
	}
} 
class mos_JoomQuiz_Template extends JLMSDBTable {
	var $id 				= null;
	var $template_name		= null;

	function mos_JoomQuiz_Template( &$db ) {
		$this->JLMSDBTable( '#__lms_quiz_templates', 'id', $db );
	}

	function check() {
		return true;
	}
}
class mos_JoomQuiz_Language extends JLMSDBTable {
	var $id 				= null;
	var $lang_file			= null;

	function mos_JoomQuiz_Language( &$db ) {
		$this->JLMSDBTable( '#__lms_quiz_languages', 'id', $db );
	}

	function check() {
		global $JLMS_DB;
		$query = "SELECT lang_file FROM #__lms_quiz_languages WHERE id = '".$this->id."'";
		$JLMS_DB->SetQuery( $query );
		$old_name = $JLMS_DB->LoadResult();
		if (isset($old_name) && $old_name == 'default') {
			$this->_error = 'Could not modify DEFAULT Language';
			return false;
		} 
		$query = "SELECT count(*) FROM #__lms_quiz_languages WHERE id <> '".$this->id."' and lang_file = '".$this->lang_file."'";
		$JLMS_DB->SetQuery( $query );
		$items_count = $JLMS_DB->LoadResult();
		if ($items_count > 0) {
			$this->_error = 'This name for Language is already exist';
			return false;
		} 
		if ((trim($this->lang_file == '')) || (preg_match("/[0-9a-z]/", $this->lang_file ) == false)) {
			$this->_error = 'Please enter valid Language name';
			return false;
		} 
		return true;
	}
}
?>