<?php 
/**
* includes/lms_tracking.class.php
* (c) JoomaLMS eLearning Software http://www.joomlalms.com/
**/

defined( '_JLMS_EXEC' ) or die( 'Restricted access' );

class JLMS_Tracking extends JLMSObject {

	var $_trackEnabled = null;
	var $_db;
	
	var $_timeinterval;
	var $_show_online;
	var $_show_online_pulse;
	var $_show_status;

	/**	Class constructor	*/
	function __construct( $trackEnabled = 0, &$db ) {
		$this->_trackEnabled = $trackEnabled;
		$this->_db = & $db;
		$this->set( 'trackTime', date('Y-m-d H:i:s', time() - date('Z') ) ); // the same as gmdate('Y-m-d H:i:s'
	}
	
	function UserDownloadFile( $user_id, $doc_id ) {
		if ($this->_trackEnabled) {
			$query = "INSERT INTO #__lms_track_downloads (doc_id, user_id, track_time)"
			. "\n VALUES('$doc_id', '$user_id', '".$this->trackTime."')";
			$this->_db->SetQuery( $query );
			$this->_db->query();
		}
	}
	function UserEnterChat( $user_id, $course_id ) {
		if ($this->_trackEnabled) {
			$query = "INSERT INTO #__lms_track_chat (user_id, course_id, track_time)"
			. "\n VALUES('$user_id', '$course_id', '".$this->trackTime."')";
			$this->_db->SetQuery( $query );
			$this->_db->query();
		}
	}
	function UserHit( $user_id, $course_id, $page_id ) {
		if ($this->_trackEnabled) {
			$query = "INSERT INTO #__lms_track_hits (user_id, page_id, course_id, track_time)"
			. "\n VALUES('$user_id', '$page_id', '$course_id', '".$this->trackTime."')";
			$this->_db->SetQuery( $query );
			$this->_db->query();
		}
	}
	
	function TimeTrakingRequireJS(){
		$this->_timeinterval = 5;
		$this->_show_online = 1;
		$this->_show_online_pulse = 0;
		$this->_show_status = 0;
		
		if( JLMS_mootools12() ) {
			$fileTimeTracker = 'timeTracker16.js';
		} else {
			$fileTimeTracker = 'timeTracker.js';
		}
		
		if(file_exists(_JOOMLMS_FRONT_HOME . DS . 'includes' . DS . 'js' . DS . $fileTimeTracker)){
			JHTML::_('behavior.mootools');
			$document = & JFactory::getDocument();
			$document->addScript(JURI::base().'components/com_joomla_lms/includes/js/'.$fileTimeTracker);
			return true;
		} else {
			return false;
		}
	}
	
	function TimeTrakingOnJS(&$course_id, &$user_id){
		global $JLMS_CONFIG;
		$script = "
			var TTracker_".$course_id."_".$user_id." = new TimeTracker({
				interval: ".$this->_timeinterval.",
				url_handler: '".JURI::base()."index.php?option=com_joomla_lms&task=timetracking',
				method: 'post',
				course_id: ".$course_id.",
				user_id: ".$user_id.",
				show_online: ".$this->_show_online.",
				show_online_pulse: ".$this->_show_online_pulse.",
				show_status: ".$this->_show_status."
			});
			TTracker_".$course_id."_".$user_id.".start();
		";
		$JLMS_CONFIG->set('web20_domready_code', $JLMS_CONFIG->get('web20_domready_code','').$script);
	}
	
	function TimeTrakingOnJS_Resource(&$course_id, &$user_id, $resourse_type=9, $resourse_id){
		global $JLMS_CONFIG;
		
		$script = '';
		$script .= '
			var TTracker_'.$course_id.'_'.$user_id.'_'.$resourse_type.'_'.$resourse_id.' = new TimeTracker({
				interval: '.$this->_timeinterval.',
				url_handler: "'.JURI::base().'index.php?option=com_joomla_lms&task=timetracking",
				method: "post",
				course_id: '.$course_id.',
				user_id: '.$user_id.',
				resource_type: '.$resourse_type.',
				resource_id: '.$resourse_id.',
				show_online: '.$this->_show_online.',
				show_online_pulse: '.$this->_show_online_pulse.',
				show_status: '.$this->_show_status.'
			});
		';
		$document = &JFactory::getDocument();
		$document->addScriptDeclaration($script);
	}
	
	function TimeTracking_currentTime(){
		return time() - date('Z');
	}
	
	function TimeTrackingClear(&$course_id, &$user_id, $resource_type=0, &$resource_id=0){
		if($resource_id){
			$query = "DELETE FROM #__lms_time_tracking_resources"
			. "\n WHERE 1"
			. "\n AND course_id = '".$course_id."'"
			. "\n AND user_id = '".$user_id."'"
			. "\n AND resource_type = '".$resource_type."'"
			. "\n AND resource_id = '".$resource_id."'"
			;
		} else {
			$query = "DELETE FROM #__lms_time_tracking"
			. "\n WHERE 1"
			. "\n AND course_id = '".$course_id."'"
			. "\n AND user_id = '".$user_id."'"
			;
		}
		$this->_db->setQuery($query);
		$this->_db->query();
	}
	
	function TimeTrackingActivate(&$course_id, &$user_id, $resource_type=0, &$resource_id=0, &$item_id=0){
		if($course_id && $user_id){
			$current_time = $this->TimeTracking_currentTime();
			if($resource_id){
				$query = "SELECT *"
				. "\n FROM #__lms_time_tracking_resources"
				. "\n WHERE 1"
				. "\n AND course_id = '".$course_id."'"
				. "\n AND user_id = '".$user_id."'"
				. "\n AND resource_type = '".$resource_type."'"
				. "\n AND resource_id = '".$resource_id."'"
				. "\n AND item_id = '".$item_id."'"
				;
			} else {
				$query = "SELECT *"
				. "\n FROM #__lms_time_tracking"
				. "\n WHERE 1"
				. "\n AND course_id = '".$course_id."'"
				. "\n AND user_id = '".$user_id."'"
				;
			}
			$this->_db->setQuery($query);
			$time_tracking = $this->_db->loadObject();
			
			if(!isset($time_tracking->id) || !$time_tracking->id){
				if($resource_id){
					$query = "INSERT INTO #__lms_time_tracking_resources"
					. "\n (id, course_id, user_id, resource_type, resource_id, item_id, time_spent, time_last_activity)"
					. "\n VALUES"
					. "\n ('', ".$course_id.", ".$user_id.", ".$resource_type.", ".$resource_id.", ".$item_id.", 0, ".$current_time.")"
					;
				} else {
					$query = "INSERT INTO #__lms_time_tracking"
					. "\n (id, course_id, user_id, time_spent, time_last_activity)"
					. "\n VALUES"
					. "\n ('', ".$course_id.", ".$user_id.", 0, ".$current_time.")"
					;
				}
				$this->_db->setQuery($query);
				$this->_db->query();
			}
			
			if($resource_id){
				$query = "SELECT *"
				. "\n FROM #__lms_time_tracking_resources"
				. "\n WHERE 1"
				. "\n AND course_id = '".$course_id."'"
				. "\n AND user_id = '".$user_id."'"
				. "\n AND resource_type = '".$resource_type."'"
				. "\n AND resource_id = '".$resource_id."'"
				. "\n AND item_id = '".$item_id."'"
				;
			} else {
				$query = "SELECT *"
				. "\n FROM #__lms_time_tracking"
				. "\n WHERE 1"
				. "\n AND course_id = '".$course_id."'"
				. "\n AND user_id = '".$user_id."'"
				;
			}
			$this->_db->setQuery($query);
			$time_tracking = $this->_db->loadObject();
			
			$start_time = isset($time_tracking->time_last_activity) && $time_tracking->time_last_activity ? $time_tracking->time_last_activity : $current_time;
			$time_spent = isset($time_tracking->time_spent) && $time_tracking->time_spent ? $time_tracking->time_spent : 0;
			$time_spent_before = $time_spent;
			
			if(isset($time_tracking->id) && $time_tracking->id){
				if(($current_time - $start_time) < $this->_timeinterval){
					$time_spent = $time_spent + ($current_time - $start_time);
				}
			 	$time_spent_after = $time_spent;
				
				if($resource_id){
			 		$query = "UPDATE #__lms_time_tracking_resources"
				 	. "\n SET"
				 	. "\n time_last_activity = '".$current_time."'"
				 	.($time_spent_after && $time_spent_before <= $time_spent_after ? "\n, time_spent = '".$time_spent."'" : '')
				 	. "\n WHERE 1"
				 	. "\n AND course_id = '".$course_id."'"
					. "\n AND user_id = '".$user_id."'"
					. "\n AND resource_type = '".$resource_type."'"
					. "\n AND resource_id = '".$resource_id."'"
					. "\n AND item_id = '".$item_id."'"
					;
			 	} else {
				 	$query = "UPDATE #__lms_time_tracking"
				 	. "\n SET"
				 	. "\n time_last_activity = '".$current_time."'"
				 	.($time_spent_after && $time_spent_before <= $time_spent_after ? "\n, time_spent = '".$time_spent."'" : '')
				 	. "\n WHERE 1"
				 	. "\n AND course_id = '".$course_id."'"
					. "\n AND user_id = '".$user_id."'"
					;
			 	}
				$this->_db->setQuery($query);
				$this->_db->query();
			}
		}
	}
	
	function TimeTracking(){
		$course_id = JRequest::getVar('course_id', 0);
		$user_id = JRequest::getVar('user_id', 0);
		$resource_type = JRequest::getVar('resource_type', 0);
		$resource_id = JRequest::getVar('resource_id', 0);
		$item_id = JRequest::getVar('item_id', 0);
		
		$start = JRequest::getVar('start', 0);
		$is_active = JRequest::getVar('is_active', 0);
		
		if(isset($resource_id) && $resource_id){
			$this->TimeTrackingData($course_id, $user_id, $is_active, $start, $resource_type, $resource_id, $item_id);
		} else {
			$this->TimeTrackingData($course_id, $user_id, $is_active, $start);
		}
	}
	
	function TimeTrackingData(&$course_id, &$user_id, &$is_active, &$start, &$resource_type=0, &$resource_id=0, &$item_id=0){
		if($course_id && $user_id){
			$current_time = $this->TimeTracking_currentTime();
			
			if($resource_id){
				$query = "SELECT *"
				. "\n FROM #__lms_time_tracking_resources"
				. "\n WHERE 1"
				. "\n AND course_id = '".$course_id."'"
				. "\n AND user_id = '".$user_id."'"
				. "\n AND resource_type = '".$resource_type."'"
				. "\n AND resource_id = '".$resource_id."'"
				. "\n AND item_id = '".$item_id."'"
				;
			} else {
				$query = "SELECT *"
				. "\n FROM #__lms_time_tracking"
				. "\n WHERE 1"
				. "\n AND course_id = '".$course_id."'"
				. "\n AND user_id = '".$user_id."'"
				;
			}
			$this->_db->setQuery($query);
			$time_tracking = $this->_db->loadObject();
			
			if(isset($start) && $start && (!isset($time_tracking->id) || !$time_tracking->id)){
				if($resource_id){
					$query = "INSERT INTO #__lms_time_tracking_resources"
					. "\n (id, course_id, user_id, resource_type, resource_id, item_id, time_spent, time_last_activity)"
					. "\n VALUES"
					. "\n ('', ".$course_id.", ".$user_id.", ".$resource_type.", ".$resource_id.", ".$item_id.", 0, ".$current_time.")"
					;
				} else {
					$query = "INSERT INTO #__lms_time_tracking"
					. "\n (id, course_id, user_id, time_spent, time_last_activity)"
					. "\n VALUES"
					. "\n ('', ".$course_id.", ".$user_id.", 0, ".$current_time.")"
					;
				}
				$this->_db->setQuery($query);
				$this->_db->query();
			}
			
			$start_time = isset($time_tracking->time_last_activity) && $time_tracking->time_last_activity ? $time_tracking->time_last_activity : $current_time;
			$time_spent = isset($time_tracking->time_spent) && $time_tracking->time_spent ? $time_tracking->time_spent : 0;
			$time_spent_before = $time_spent;
			
			if(isset($is_active)){
				$time_spent_after = 0;
			 	if($is_active && !$start){
			 		$time_spent = $time_spent + ($current_time - $start_time);
			 		$time_spent_after = $time_spent;
			 	}
			 	
			 	if($resource_id){
			 		$query = "UPDATE #__lms_time_tracking_resources"
				 	. "\n SET"
				 	. "\n time_last_activity = '".$current_time."'"
				 	.($time_spent_after && $time_spent_before <= $time_spent_after ? "\n, time_spent = '".$time_spent."'" : '')
				 	. "\n WHERE 1"
				 	. "\n AND course_id = '".$course_id."'"
					. "\n AND user_id = '".$user_id."'"
					. "\n AND resource_type = '".$resource_type."'"
					. "\n AND resource_id = '".$resource_id."'"
					. "\n AND item_id = '".$item_id."'"
					;
			 	} else {
				 	$query = "UPDATE #__lms_time_tracking"
				 	. "\n SET"
				 	. "\n time_last_activity = '".$current_time."'"
				 	.($time_spent_after && $time_spent_before <= $time_spent_after ? "\n, time_spent = '".$time_spent."'" : '')
				 	. "\n WHERE 1"
				 	. "\n AND course_id = '".$course_id."'"
					. "\n AND user_id = '".$user_id."'"
					;
			 	}
				$this->_db->setQuery($query);
				$this->_db->query();
			}
		}
		
		$this->TimeTrakingViewStatistics($course_id, $user_id, $resource_type, $resource_id, $item_id);
		die;
	}
	
	function TimeTrakingViewStatistics(&$course_id, &$user_id, &$resource_type=0, &$resource_id=0, &$item_id=0){
		if($resource_id){
			$query = "SELECT time_spent"
			. "\n FROM #__lms_time_tracking_resources"
			. "\n WHERE 1"
			. "\n AND course_id = '".$course_id."'"
			. "\n AND user_id = '".$user_id."'"
			. "\n AND resource_type = '".$resource_type."'"
			. "\n AND resource_id = '".$resource_id."'"
			. "\n AND item_id = '".$item_id."'"
			;
		} else {
			$query = "SELECT time_spent"
			. "\n FROM #__lms_time_tracking"
			. "\n WHERE 1"
			. "\n AND course_id = '".$course_id."'"
			. "\n AND user_id = '".$user_id."'"
			;
		}
		$this->_db->setQuery($query);
		$time_tracking = $this->_db->loadObject();
		if(!isset($time_tracking->time_spent)){
			echo 'Refresh Page';
		} else {
			echo '<b>'.$this->TT_getTimeNormal($time_tracking->time_spent).'</b>'; //Online time:
		}
	}
	
	function TT_getTimeNormal($sec){
		$d = $sec >= (24 * 60 * 60) ? floor($sec / (24 * 60 * 60)) : 0;
		$sec = $sec - $d * 24 * 60 * 60;
		$h = $sec >= (60 * 60) ? floor($sec / (60 * 60)) : 0;
		$sec = $sec - $h * 60 * 60;
		$m = $sec >= 60 ? floor($sec/60) : 0;
		$sec = $sec - $m * 60;
		$s = $sec;
		
		$time = '';
		$time .= $d ? str_pad($d, strlen($d), '0', STR_PAD_LEFT).' day ' : '';
		$time .= (($d && $h) || ($d && !$h) || (!$d && $h)) ? str_pad($h, 2, '0', STR_PAD_LEFT).':' : '';
		$time .= str_pad($m, 2, '0', STR_PAD_LEFT).':';
		$time .= str_pad($s, 2, '0', STR_PAD_LEFT);
		
		return $time;
	}
}
?>