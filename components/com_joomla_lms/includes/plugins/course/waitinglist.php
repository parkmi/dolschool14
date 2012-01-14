<?php
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

$_JLMS_PLUGINS = & JLMSFactory::getPlugins();
$_JLMS_PLUGINS->registerFunction( 'onCourseJoinAttempt', 'addToWaitingList' );
$_JLMS_PLUGINS->registerFunction( 'onCourseAdd', 'removeFromWaitingList' );
$_JLMS_PLUGINS->registerFunction( 'onCourseExpulsion', 'moveToCourseFromWaitingList' );

function addToWaitingList ( $user_info, $course_info ) {
	$_JLMS_PLUGINS = & JLMSFactory::getPlugins();

	$dbo = & JFactory::GetDbo();
	//---->get bot info
	if ( !isset($_JLMS_PLUGINS->_course_bot_params['waitinglist']) ) {
		// load mambot params info
		$query = "SELECT params"
		. "\n FROM #__lms_plugins"
		. "\n WHERE element = 'waitinglist'"
		. "\n AND folder = 'course'"
		;
		$dbo->setQuery( $query );
		$bot = $dbo->loadObject();

		// save query to class variable
		$_JLMS_PLUGINS->_course_bot_params['waitinglist'] = $bot;
	}

	// pull query data from class variable
	$bot = $_JLMS_PLUGINS->_course_bot_params['waitinglist'];

	$botParams = new jlmsPluginParameters( $bot->params );

	//count users in course
	$query = "SELECT COUNT(DISTINCT(user_id)) FROM `#__lms_users_in_groups` WHERE course_id = $course_info->course_id";// AND role_id = 2";
	$dbo->setQuery($query);
	$current_attendees = $dbo->loadResult();
	$max_attendees = $course_info->max_attendees;

	//course not full - return true
	if (!$max_attendees || $max_attendees > $current_attendees) return true;

	//check if user is already in waiting list (just in case)
	$query = "SELECT COUNT(*) FROM #__lms_waiting_lists WHERE course_id = $course_info->course_id AND user_id = $user_info->user_id";
	$dbo->setQuery($query);
	if ($dbo->loadResult()) return false;

	//get max ordering of needed list
	$query = "SELECT MAX(ordering) FROM #__lms_waiting_lists WHERE course_id = $course_info->course_id";
	$dbo->setQuery($query);
	$ordering = $dbo->loadResult();
	$ordering++;

	//add user to waiting list
	$query = "INSERT INTO #__lms_waiting_lists (course_id, user_id, ordering) VALUES ($course_info->course_id, $user_info->user_id, $ordering)";
	$dbo->setQuery($query);
	$dbo->query();

	return false;
}

/**
 * Deletes users from waiting list
 *
 * @param object/objectArray $users_info 
 * @param object $course_info
 * @return bool
 */
function removeFromWaitingList ( $users_info, $course_info ) {

	$user_ids = array();
	if (is_array($users_info)) {
		foreach ($users_info as $user_info) {
			$user_ids[] = $user_info->user_id;
		}
	} else {
		$user_ids = array($users_info->user_id);
	}
	$dbo = & JFactory::GetDbo();
	//delete user from waiting list
	$query = "DELETE FROM #__lms_waiting_lists WHERE course_id=$course_info->course_id AND user_id IN (".implode(',', $user_ids).")";
	$dbo->setQuery($query);
	$dbo->query();

	return true;
}

function moveToCourseFromWaitingList ( $course_id ) {
	$dbo = & JFactory::getDbo();
	$query = "SELECT * FROM `#__lms_courses` WHERE id = '$course_id' AND self_reg != '0'  ";
	$dbo->setQuery($query);
	$course = $dbo->loadObject();
	$params = new JLMSParameters($course->params);
	$max_attendees = $params->get('max_attendees', 0);
	
	//count users in course
	$query = "SELECT COUNT(DISTINCT(user_id)) FROM `#__lms_users_in_groups` WHERE course_id = $course_id";// AND role_id = 2";
	$dbo->setQuery($query);
	$current_attendees = $dbo->loadResult();
	
	$limit = $max_attendees - $current_attendees;
	//course full - return true
	if ($limit <= 0) return false;

	$query = "SELECT user_id FROM #__lms_waiting_lists WHERE course_id=$course_id ORDER BY ordering ASC";
	$dbo->setQuery($query, 0, $limit);
	$ids = $dbo->loadResultArray();
	
	$query = "DELETE FROM #__lms_waiting_lists WHERE course_id=$course_id AND user_id IN (".implode(',',$ids).")";
	$dbo->setQuery($query);
	$dbo->query();
	
	$result = array();
	$query = "INSERT INTO `#__lms_users_in_groups` ( course_id , user_id, enrol_time ) VALUES ";	
	$first = true;
	foreach ($ids as $id) {
		$query .= (!$first) ? ',' : '';
		$query .= "\n($course_id, $id, '".JLMS_gmdate()."')";
		$first = false;
		
		$tmp = new stdClass();
		$tmp->user_id = $id;
		$tmp->course_id = $course_id;
		$result[] = $tmp;
	}
	$dbo->setQuery($query);
	$dbo->query();
	
	if (!count($result)) return false;
	
	return $result;
}
?>