<?php
/**
* includes/libraries/lms.lib.errorlog.php
* (c) JoomaLMS eLearning Software http://www.joomlalms.com/
**/

// no direct access
defined( '_JLMS_EXEC' ) or die( 'Restricted access' );

class JLMSErrorLog {

	function writeMessagesToLog($msgs) {
		global $JLMS_CONFIG;
		if($JLMS_CONFIG->get('enable_errorlog', true) && count($msgs)){
			$log_array = array();
			foreach ($msgs as $msg) {
				if (strlen($msg)) {
					$log_array[] = $msg;
				}
			}
			if (count($log_array)) {
				JLMSErrorLog::writeEntry($log_array);
			}
		}
	}

	function writeSCORMLog($error='', $course_id=0, $scorm_id=0){
		global $JLMS_CONFIG;

		if($JLMS_CONFIG->get('enable_errorlog', true) && strlen($error)){
			$db = & JFactory::getDbo();
			$user = & JFactory::getUser();

			$course_name = '';
			if($course_id){
				$query = "SELECT course_name"
				. "\n FROM #__lms_courses"
				. "\n WHERE 1"
				. "\n AND id = '".$course_id."'"
				;
				$db->setQuery($query);
				$course_name = $db->loadResult();
			}

			$scorm_name = '';
			if($scorm_id){
				$query = "SELECT lpath_name"
				. "\n FROM"
				. "\n #__lms_learn_paths"
				. "\n WHERE 1"
				. "\n AND item_id = '".$scorm_id."'"
				;
				$db->setQuery($query);
				$scorm_name = $db->loadResult();
			}

			$log_array = array();

			$log_array[] = 'Error: '.$error;

			if(isset($user->id) && $user->id){
				$log_array[] = 'User informaton: '.$user->username.' (UserID='.$user->id.')';
			} else {
				$log_array[] = 'User informaton: not logged in';
			}

			if($course_id){
				$log_array[] = 'Course information: '.$course_name.' (CourseID='.$course_id.')';
			}

			if($scorm_id){
				$log_array[] = 'SCORM information: '.$scorm_name.' (SCORMID='.$scorm_id.')';
			}

			JLMSErrorLog::writeLog($log_array);
		}
	}

	function writeLog($log_array=array(), $filename='joomlalms_error_log.txt', $format='Date: {DATE} {TIME} {COMMENT}'){
		jimport('joomla.error.log');
		$log = & JLog::getInstance($filename, array('format'=>$format));

		$log_array[] = 'Browser and OS information: '.$_SERVER['HTTP_USER_AGENT'];
		$log_array[] = 'IP address: '.$_SERVER['REMOTE_ADDR'];

		$log_array[] = 'GET:' . "\n" . '--------------------------------' . "\n" . JLMSErrorLog::array_to_str($_GET) . '--------------------------------';
		$log_array[] = 'POST:' . "\n" . '--------------------------------' . "\n" . JLMSErrorLog::array_to_str($_POST) . '--------------------------------';
		$log_array[] = 'COOKIE:' . "\n" . '--------------------------------' . "\n" . JLMSErrorLog::array_to_str($_COOKIE) . '--------------------------------';

		$log_array[] = '###########################################################################';

		$log_text = "\n".implode("\n", $log_array). "\n";

		$status = $log->addEntry(array('COMMENT'=>$log_text));
		return $status;
	}

	function writeEntry($log_array=array(), $filename='joomlalms_error_log.txt', $format='Date: {DATE} {TIME} {COMMENT}'){
		jimport('joomla.error.log');
		$log = & JLog::getInstance($filename, array('format'=>$format));

		$log_array[] = '###########################################################################';

		$log_text = "\n".implode("\n", $log_array). "\n";

		$status = $log->addEntry(array('COMMENT'=>$log_text));
		return $status;
	}

	function array_to_str($array=array()){
		$str = '';
		if(count($array)){
			foreach($array as $key=>$value){
				$str .= '['.$key.'] => '.$value . "\n";
			}
		}
		return $str;
	}
}