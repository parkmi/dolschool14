<?php
defined( '_VALID_MOS' ) or die( 'Restricted access' );

class mos_FLMS_Course_save extends mosDBTable {

	var $f_id							= null;
	var $course_id						= null;
	var $type_lesson					= null;
	var $theory_duration_time			= null;
	var $stu_1_briefing_time			= null;
	var $stu_1_additional_time			= null;
	var $stu_2_briefing_time			= null;
	var $stu_2_additional_time			= null;
	var $stu_3_briefing_time			= null;
	var $stu_3_additional_time			= null;
	var $stu_4_briefing_time			= null;
	var $stu_4_additional_time			= null;
	
	var $operation						= null;
	var $pf_time 						= null;
	var $pm_time						= null;
	var $debriefing_time				= null;
	
	var $no_instructor					= null;
	var $no_room						= null;
	
	var $like_theory					= null;
	
	var $test_lesson					= null;
	var $solo_flight_lesson				= null;
/**
* @param database A database connector object
*/
	function mos_FLMS_Course_save( &$db ) {
		$this->mosDBTable( '#__lmsf_courses', 'f_id', $db );
	}
	
	function check() {
		return true;
	}
}

class mos_FLMS_Course_load extends mosDBTable {

	var $f_id							= null;
	var $course_id						= null;
	var $type_lesson					= null;
	var $theory_duration_time			= null;
	var $stu_1_briefing_time			= null;
	var $stu_1_additional_time			= null;
	var $stu_2_briefing_time			= null;
	var $stu_2_additional_time			= null;
	var $stu_3_briefing_time			= null;
	var $stu_3_additional_time			= null;
	var $stu_4_briefing_time			= null;
	var $stu_4_additional_time			= null;
	
	var $operation						= null;
	var $pf_time 						= null;
	var $pm_time						= null;
	var $debriefing_time				= null;
	
	var $no_instructor					= null;
	var $no_room						= null;
	
	var $like_theory					= null;
	
	var $test_lesson					= null;
	var $solo_flight_lesson				= null;
/**
* @param database A database connector object
*/
	function mos_FLMS_Course_load( &$db ) {
		$this->mosDBTable( '#__lmsf_courses', 'course_id', $db );
	}
	
	function check() {
		return true;
	}
}

?>