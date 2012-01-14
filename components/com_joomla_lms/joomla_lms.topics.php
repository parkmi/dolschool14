<?php
/**
* joomla_lms.topics.php
* (c) JoomaLMS eLearning Software http://www.joomlalms.com/
**/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

if (!defined('_CHAPTER_ID')) {
	define('_CHAPTER_ID', 1);
	define('_DOCUMENT_ID', 2);
	define('_LINK_ID', 3);
	define('_CONTENT_ID', 4);
	define('_QUIZ_ID', 5);
	define('_SCORM_ID', 6);
	define('_LPATH_ID', 7);
}

global $JLMS_LANGUAGE, $JLMS_CONFIG;
//adding non-topic language files
JLMS_require_lang($JLMS_LANGUAGE, array('course_docs.lang', 'course_links', 'course_lpath'), $JLMS_CONFIG->get('default_language'));
JLMS_processLanguage( $JLMS_LANGUAGE );

$task 				= mosGetParam( $_REQUEST, 'task', '' );
$course_id			= intval( mosGetParam( $_REQUEST, 'id', 0 ) );
$course_id			= intval( mosGetParam( $_REQUEST, 'course_id', $course_id));
$topic_id			= intval( mosGetParam( $_REQUEST, 'topic_id', 0 ) );
$topic_ordering		= intval( mosGetParam( $_REQUEST, 'topic_ordering', 0 ) );
$element_ordering	= intval( mosGetParam( $_REQUEST, 'element_ordering', 0 ) );
$state				= intval( mosGetParam( $_REQUEST, 'state', 0 ));
$t_id				= intval( mosGetParam( $_REQUEST, 't_id', 0 ));
require_once(_JOOMLMS_FRONT_HOME . "/joomla_lms.topics.html.php");
require_once(_JOOMLMS_FRONT_HOME . "/joomla_lms.topics.class.php");
require_once(_JOOMLMS_FRONT_HOME . "/joomla_lms.docs.hlpr.php");


switch ($task) {
	//	case 'details_course':				showCourseDetails( $option );					break;
	case 'orderup_topic':				orderTopic($course_id, $topic_ordering, -1);	break;
	case 'orderdown_topic':				orderTopic($course_id, $topic_ordering, 1);		break;
	case 'delete_topic':				deleteTopic($course_id, $topic_id);				break;
	case 'add_topic_element':
	$course = new JLMS_Course_HomePage($course_id, true);
	$course->listElements($topic_id);
	break;
	case 'add_submit_topic_element':	addElement($course_id, $topic_id, $t_id);				break;
	case 'publish_topic':				publishTopic($course_id, $topic_id, mosGetParam($_REQUEST, 'state', 0));	break;
	case 'add_topic':
	case 'edit_topic':					editTopic($course_id, $topic_id);				break;
	case 'cancel_topic':				JLMSRedirect( sefRelToAbs("index.php?option=$option&task=details_course&id=$course_id&Itemid=$Itemid#topic_".intval( mosGetParam( $_REQUEST, 'id', 0 ) )) ); break;
	case 'save_topic':					saveTopic($course_id);							break;
	case 'orderup_element':				JLMS_orderElement($topic_id, $course_id, -1, $element_ordering, $t_id);	break;
	case 'orderdown_element':			JLMS_orderElement($topic_id, $course_id, 1, $element_ordering, $t_id);		break;
	case 'delete_topic_element':		JLMS_deleteElement($topic_id, $course_id, $t_id);		break;
	case 'change_element':				changeElement($course_id, $state, $topic_id);	break;
	
	case 'topic':						viewTopic($course_id, $topic_id);											break;
}

function viewTopic($course_id, $topic_id){
	global $JLMS_DB, $Itemid, $JLMS_CONFIG;
	
	$lists = array();
	
	$usertype = $JLMS_CONFIG->get('current_usertype', 0);
	if ( ($course_id == $JLMS_CONFIG->get('course_id')) ) {
		$row = new JLMS_Topic($JLMS_DB);
		if ($topic_id) $row->load($topic_id);
		
		JLMS_topic_html::showTopic($course_id, $topic_id, $row, $lists);
	} else {
		$msg = '';//_JLMS_TOPIC_HACK;
		JLMSRedirect( sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=details_course&id=$course_id#topic_$topic_id"), $msg);
	}
}

/**
 * Changes topic ordering
 *
 * @param int $course_id
 * @param int $ordering (current ordering of the topic)
 * @param int $def ('-1' to orderUp and '1' to orderDown)
 */

function orderTopic ($course_id, $ordering, $def) {
	global $JLMS_DB, $option, $Itemid, $JLMS_CONFIG;
	$usertype = $JLMS_CONFIG->get('current_usertype', 0);
	if ( ($course_id == $JLMS_CONFIG->get('course_id')) && ($usertype == 1) ) {
		$query = "UPDATE #__lms_topics SET ordering=ordering+($def)+99999 WHERE course_id=$course_id and ordering=$ordering";
		$JLMS_DB->setQuery($query);
		$JLMS_DB->query();
		$query = "UPDATE #__lms_topics SET ordering=ordering-($def) WHERE course_id=$course_id and ordering=$ordering+($def)";
		$JLMS_DB->setQuery($query);
		$JLMS_DB->query();
		$query = "UPDATE #__lms_topics SET ordering=ordering-99999 WHERE course_id=$course_id and ordering>9999";
		$JLMS_DB->setQuery($query);
		$JLMS_DB->query();
		$msg = _JLMS_TOPIC_T_REORDERED;
		$topic_id = mosGetParam($_REQUEST, 'topic_id');
	} else {
		$msg = '';//_JLMS_TOPIC_HACK;
	}
	JLMSRedirect( sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=details_course&id=$course_id#topic_$topic_id"), $msg );
}
function deleteTopic ($course_id, $topic_id) {
	global $JLMS_DB, $Itemid, $option, $JLMS_CONFIG;
	$usertype = $JLMS_CONFIG->get('current_usertype', 0);
	if ( ($course_id == $JLMS_CONFIG->get('course_id')) && ($usertype == 1) ) {
		$query = "DELETE FROM #__lms_topics WHERE id=$topic_id";
		$JLMS_DB->setQuery($query);
		$JLMS_DB->query();
		fixTopicOrder($course_id);
		$msg = _JLMS_TOPIC_T_DELETED;
	} else {
		$msg = '';//_JLMS_TOPIC_HACK;
	}
	JLMSRedirect( sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=details_course&id=$course_id"), $msg );
}
function fixTopicOrder ($course_id) {
	global $JLMS_DB;
	$query = "SELECT COUNT(id) FROM #__lms_topics WHERE course_id=$course_id";
	$JLMS_DB->setQuery($query);
	$count = $JLMS_DB->loadResult();
	for ($i=0; $i<$count; $i++) {
		$query = "UPDATE #__lms_topics SET ordering=$i WHERE ordering>=$i AND course_id=$course_id ORDER BY ordering,id LIMIT 1";
		$JLMS_DB->setQuery($query);
		$JLMS_DB->query();
	}
}
function fixElementOrder ($topic_id) {
	global $JLMS_DB;
	$query = "SELECT COUNT(id) FROM #__lms_topic_items WHERE topic_id=$topic_id";
	$JLMS_DB->setQuery($query);
	$count = $JLMS_DB->loadResult();
	for ($i=0; $i<$count; $i++) {
		$query = "UPDATE #__lms_topic_items SET ordering=$i WHERE ordering>=$i AND topic_id=$topic_id ORDER BY ordering LIMIT 1";
		$JLMS_DB->setQuery($query);
		$JLMS_DB->query();
	}
}
function addElement ($course_id, $topic_id, $t_id) {
	global $JLMS_DB, $Itemid, $option, $JLMS_CONFIG;
	$usertype = $JLMS_CONFIG->get('current_usertype', 0);
	if ( ($course_id == $JLMS_CONFIG->get('course_id')) && ($usertype == 1) ) {
		$cid = mosGetParam( $_REQUEST, 'cid', array(0) );
		$do_proceed = true;
		if (empty($cid)) {
			$msg = '';//There are no selected items
			$do_proceed = false;
		} elseif (count($cid) == 1) {
			if (isset($cid[0]) && $cid[0] == 0) {
				$msg = '';//There are no selected items
				$do_proceed = false;
			}
		}
		if ($do_proceed) {
			foreach ($cid as $foo) {
				$foo = explode('_', $foo);
				$type = $foo[0];
				$id = $foo[1];
				$query = "INSERT INTO #__lms_topic_items SET course_id=$course_id, topic_id=$topic_id, item_type=$type, item_id=$id, ordering=9999";
				$JLMS_DB->setQuery($query);
				$JLMS_DB->query();
			}
			fixElementOrder($topic_id);
			$msg = _JLMS_TOPIC_I_ADDED;
		}
	} else {
		$msg = '';//_JLMS_TOPIC_HACK;
	}
	JLMSRedirect( sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=details_course&id=$course_id".($t_id?"&t_id=".$t_id:"")."#topic_$topic_id"), $msg );
}
function publishTopic ($course_id, $topic_id, $state) {
	global $JLMS_DB, $Itemid, $option, $JLMS_CONFIG;
	$usertype = $JLMS_CONFIG->get('current_usertype', 0);
	if ( ($course_id == $JLMS_CONFIG->get('course_id')) && ($usertype == 1) ) {
		$query = "UPDATE #__lms_topics SET published=$state WHERE id=$topic_id";
		$JLMS_DB->setQuery($query);
		$JLMS_DB->query();
		if ($state == 1) $msg = _JLMS_TOPIC_T_PUBLISHED;
		else $msg = _JLMS_TOPIC_T_UNPUBLISHED;
	} else {
		$msg = '';//_JLMS_TOPIC_HACK;
	}
	JLMSRedirect( sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=details_course&id=$course_id#topic_$topic_id"), $msg);
}
function editTopic ($course_id, $topic_id = 0) {
	global $JLMS_DB, $Itemid, $JLMS_CONFIG, $my;
	$usertype = $JLMS_CONFIG->get('current_usertype', 0);
	if ( ($course_id == $JLMS_CONFIG->get('course_id')) && ($usertype == 1) ) {
		
		$AND_ST = "";
		if( false !== ( $enroll_period = JLMS_getEnrolPeriod( $my->id, $course_id )) ) 
		{
			$AND_ST = " AND IF(is_time_related, (show_period < '".$enroll_period."' ), 1) ";	
		}
		
		$row = new JLMS_Topic($JLMS_DB);
		$row->addCond( $AND_ST );
		if ($topic_id) $row->load($topic_id);
		$lists['publishing'] = mosHTML::yesnoRadioList( 'published', 'class="inputbox" ', ($topic_id != 0) ? $row->published : 0);
		$query = "SELECT name, ordering FROM #__lms_topics WHERE course_id=$course_id ORDER BY ordering";
		$JLMS_DB->setQuery($query);
		$topics = $JLMS_DB->loadObjectList();
		array_unshift($topics, mosHTML::makeOption(0, _JLMS_SB_FIRST_ITEM, 'ordering', 'name'));
		array_push($topics, mosHTML::makeOption(9999, _JLMS_SB_LAST_ITEM, 'ordering', 'name'));
		$lists['ordering'] = mosHTML::selectList($topics, 'ordering', '', 'ordering', 'name', $row->ordering);		
		unset($tmp);
		$lists['names'] = '';
		for ($i=1; $i<=10; $i++) {
			$tmp[] = mosHTML::makeOption($i, $i);
			$lists['names'] .= '<div id="name_'.$i.'" style="display:none;"><input class="inputbox" size="40" type="text" name="name_'.$i.'" /></div>'."\n";
		}
		$javascript = 'onclick="jlms_Change_weekly();"';
		$lists['number'] = mosHTML::selectList($tmp, 'number', $javascript, 'value', 'text', 1);
		JLMS_topic_html::editTopic($course_id, $topic_id, $row, $lists);
	} else {
		$msg = '';//_JLMS_TOPIC_HACK;
		JLMSRedirect( sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=details_course&id=$course_id#topic_$topic_id"), $msg);
	}
}
function saveTopic ($course_id) {
	global $option, $Itemid, $JLMS_DB, $JLMS_CONFIG;
	$usertype = $JLMS_CONFIG->get('current_usertype', 0);
	if ( ($course_id == $JLMS_CONFIG->get('course_id')) && ($usertype == 1) ) {
		$topic_description = strval(JLMS_getParam_LowFilter($_POST, 'description', ''));
		$topic_description = JLMS_ProcessText_LowFilter($topic_description);
		$topic_name_post = isset($_REQUEST['name'])?strval($_REQUEST['name']):'';
		$topic_name_post = (get_magic_quotes_gpc()) ? stripslashes( $topic_name_post ) : $topic_name_post; 
		$topic_name_post = ampReplace(strip_tags($topic_name_post));
		$row = new JLMS_Topic($JLMS_DB);
		$row->bind($_POST);
		$row->name = $topic_name_post;
		$row->description = $topic_description;
		$row->start_date = mosGetParam($_REQUEST, 'start_date', '0000-00-00');
		$row->start_date = JLMS_dateToDB($row->start_date);
		$row->end_date = mosGetParam($_REQUEST, 'end_date', '0000-00-00');
		$row->end_date = JLMS_dateToDB($row->end_date);
		
		$days = intval(mosGetParam($_POST, 'days', ''));
		$hours = intval(mosGetParam($_POST, 'hours', ''));
		$mins = intval(mosGetParam($_POST, 'mins', ''));
		
		if( $row->is_time_related ) {
			$row->show_period = JLMS_HTML::_('showperiod.getminsvalue', $days, $hours, $mins );
		}
		
		//----> ordering implementation
		if (mosGetParam($_POST, 'weekly', 0)) {
			//----> 06.12.2007 - DEN - 14.12.2007 - Replaced by TPETb
			$number = intval(mosGetParam($_POST, 'number', 0));
			if ($number > 50) {
				$number = 50;
			}
			//<----
			if ($number <= 0) $number = 1;//fool-check
		} else {
			$number = 1;//fool-check
		}
		$ordering = $row->ordering;
		//moveup topics with higher ordering
		$query = "UPDATE #__lms_topics SET ordering=ordering+$number WHERE course_id=$course_id AND ordering>=$ordering";
		$JLMS_DB->setQuery($query);
		$JLMS_DB->query();
		//<----

		if (mosGetParam($_POST, 'weekly', 0)) {
			$fix = 2 - $JLMS_CONFIG->get('date_format_fdow', 1);
			$first_date = strtotime($row->start_date);
			$first_day = date("w", $first_date);
			$next_date = date("Y-m-d", mktime(0, 0, 0, date("m",$first_date), (date("d",$first_date)+7-1-$first_day+$fix), date("Y",$first_date)));
			$row->publish_end = 1;
			$row->end_date = $next_date;
			if (empty($_POST['name'])) {
				$date_1 = intval(strftime("%d",$first_date)).' '.month_lang(strftime("%m",$first_date),0,2);
				$date_2 = intval(strftime("%d",strtotime($next_date))).' '.month_lang(strftime("%m",strtotime($next_date)),0,2);
				$name = $date_1.' - '.$date_2;
				$row->name = $name;
			} else {
				$row->name = $topic_name_post;
			}
		}
		$row->store();
		if (mosGetParam($_POST, 'weekly', 0)) {
			for ($i=2; $i<=$number; $i++) {
				$row = new JLMS_Topic($JLMS_DB);
				$first_date = strtotime($next_date);
				$row->start_date = date("Y-m-d", mktime(0, 0, 0, date("m",$first_date), date("d",$first_date)+1, date("Y",$first_date)));
				$next_date = date("Y-m-d", mktime(0, 0, 0, date("m",$first_date), (date("d",$first_date)+7), date("Y",$first_date)));
				$row->course_id = $course_id;
				$row->published = 1;
				$row->publish_start = 1;
				$row->publish_end = 1;
				$row->end_date = $next_date;
				if (empty($_POST['name_'.$i])) {
					$date_1 = intval(strftime("%d",strtotime($row->start_date))).' '.month_lang(strftime("%B",strtotime($row->start_date)),0,2);
					$date_2 = intval(strftime("%d",strtotime($row->end_date))).' '.month_lang(strftime("%B",strtotime($row->end_date)),0,2);
					$name = $date_1.' - '.$date_2;
					$row->name = $name;
				} else {
					$row->name = isset($_POST['name_'.$i])?strval($_POST['name_'.$i]):'';
					$row->name = (get_magic_quotes_gpc()) ? stripslashes( $row->name ) : $row->name; 
					$row->name = ampReplace(strip_tags($row->name));
				}
				$row->description = $topic_description;
				$ordering++;
				$row->ordering = $ordering;
				$row->store();
			}
		}
		fixTopicOrder($course_id);
		if ($_POST['id']) $msg = _JLMS_TOPIC_T_EDITED;
		else $msg = _JLMS_TOPIC_T_CREATED;
		if (mosGetParam($_POST, 'weekly', 0)) $msg = _JLMS_TOPIC_T_SERIES_CREATED;
	} else {
		$msg = '';//_JLMS_TOPIC_HACK;
	}
	JLMSRedirect( sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=details_course&id=$course_id#topic_$row->id"), $msg);
}
function changeElement ($course_id, $state, $topic_id) {
	global $option, $Itemid, $JLMS_DB, $JLMS_CONFIG;
	$usertype = $JLMS_CONFIG->get('current_usertype', 0);
	if ( ($course_id == $JLMS_CONFIG->get('course_id')) && ($usertype == 1) ) {
		$state = 1-$state;		
		if ($eid = mosGetParam( $_REQUEST, 'eid', 0 )) {
			$query = "UPDATE #__lms_topic_items SET `show`=$state WHERE id = $eid";
		} else {
			$cid = mosGetParam( $_REQUEST, 'cid', array(0) );
			$query = "UPDATE #__lms_topic_items SET `show`=$state WHERE id IN (".implode(',', $cid).')';
		}
		$JLMS_DB->setQuery($query);
		$JLMS_DB->query();
		if ($state) $msg = _JLMS_TOPIC_I_SHOWN;
		else $msg = _JLMS_TOPIC_I_HIDDEN;
	} else {
		$msg = '';//_JLMS_TOPIC_HACK;
	}
	JLMSRedirect( sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=details_course&id=$course_id#topic_$topic_id"), $msg);
}
function showFolderWithContent (&$k, $folder, $i, $topic_id, $checked, $span, $in_folder=null) {
	global $JLMS_DB, $max_lvl;
	$manage = ($in_folder) ? 0 : 1;
	JLMS_topic_html::showDocumentRow($k, $folder, $i, $topic_id, $checked, $manage, $in_folder, $span);
	$query = "SELECT a.*, b.file_name FROM #__lms_documents as a LEFT JOIN #__lms_files as b ON a.file_id = b.id AND a.folder_flag = 0 WHERE a.parent_id=$folder->id ORDER BY ordering";
	$JLMS_DB->setQuery($query);
	$rows = $JLMS_DB->loadObjectList();
	$new_rows = array();
	for($j=0;$j<count($rows);$j++){
		if($rows[$j]->folder_flag == 3){
			$query = "SELECT a.*, b.file_name FROM #__lms_outer_documents as a LEFT JOIN #__lms_files as b ON a.file_id = b.id AND a.folder_flag = 0 "
			. "\n WHERE a.folder_flag = 0 AND a.id = ".$rows[$j]->file_id." AND a.allow_link = 1";
			$JLMS_DB->SetQuery( $query );
			$out_row = $JLMS_DB->LoadObjectList();
			if(count($out_row)){
				$rows[$j]->doc_name = $out_row[0]->doc_name;
				$rows[$j]->file_name = $out_row[0]->file_name;
				$rows[$j]->doc_description = $out_row[0]->doc_description;
				$rows[$j]->file_id = $out_row[0]->file_id;
				$new_rows[] = $rows[$j];
			} else {
				$rows[$j]->doc_name = _JLMS_LP_RESOURSE_ISUNAV;
			}
		} else {
			$new_rows[] = $rows[$j];
		}
	}
	unset($rows);
	$rows = $new_rows;
	$rows = AppendFileIcons_toList($rows);
	if (@$in_folder[$max_lvl - $span - 1] == 2) $in_folder[$max_lvl - $span - 1] = 0;
	$in_folder[$max_lvl - $span] = 1;
	for ($j=0; $j<count($rows)-1; $j++) {
		$rows[$j]->allow_up = 0;
		$rows[$j]->allow_down = 0;
		if ($rows[$j]->folder_flag == 1) {
			showFolderWithContent ($k, $rows[$j], $i, $topic_id, '&nbsp;', $span-1, $in_folder);
		} else {
			JLMS_topic_html::showDocumentRow($k, $rows[$j], $i, '', '&nbsp;', 0, $in_folder, $span-1);
		}
	}
	$in_folder[$max_lvl - $span] = 2;
	if (isset($rows[$j])) {
		$rows[$j]->allow_up = 0;
		$rows[$j]->allow_down = 0;
		if ($rows[$j]->folder_flag == 1) {
			showFolderWithContent ($k, $rows[$j], $i, $topic_id, '&nbsp;', $span-1, $in_folder);
		} else {
			JLMS_topic_html::showDocumentRow($k, $rows[$j], $i, '', '&nbsp;', 0, $in_folder, $span-1);
		}
	}
}
function getDirNesting($rows, $id) {
	$nesting = 0;
	foreach ($rows as $key=>$row) {
		if ($row->parent_id == $id && $row->folder_flag == 1 && ($id != $key)) $curr_nesting = getDirNesting($rows, $key);
		elseif ($row->parent_id == $id) $curr_nesting = 0;
		else $curr_nesting = 0;
		if ($curr_nesting>$nesting) $nesting = $curr_nesting;
	}
	return $nesting+1;
}
function publishUtility ($published, $show=-1, $start_flag=0, $end_flag=0, $start_date="0000-00-00", $end_date="0000-00-00") {
	$ret = new publishOptionClass();
	$date = date("Y-m-d");
	$in_date = (($start_flag == 0 || $start_date <= $date) && ($end_flag == 0 || $end_date >= $date));
	$expired = ($date > $end_date) ? 1 : 0; //only shows if element expired, doesn't show pending option
	if ($show != -1) {
		if ($published == 1) {
			if ($in_date && $show == 1) { $ret->state = 1; $ret->show = 1; $ret->pending_expired = 0; $ret->alt = _JLMS_TOPIC_PUBLISHED.'/'._JLMS_TOPIC_SHOWN; $ret->image = 'components/com_joomla_lms/lms_images/toolbar/btn_accept.png'; }
			elseif ($in_date && $show == 0) { $ret->state = 1; $ret->show = 0; $ret->pending_expired = 0; $ret->alt = _JLMS_TOPIC_PUBLISHED.'/'._JLMS_TOPIC_HIDDEN; $ret->image =  'components/com_joomla_lms/lms_images/toolbar/btn_cancel.png'; }
			elseif (!$in_date && $show == 1 && $expired == 1) { $ret->state = 0; $ret->show = 1; $ret->pending_expired =  1; $ret->alt = _JLMS_TOPIC_EXPIRED.'/'._JLMS_TOPIC_SHOWN; $ret->image = 'components/com_joomla_lms/lms_images/toolbar/btn_publish_hidden.png'; }
			elseif (!$in_date && $show == 1 && $expired == 0) { $ret->state = 0; $ret->show = 1; $ret->pending_expired = -1; $ret->alt = _JLMS_TOPIC_PENDING.'/'._JLMS_TOPIC_SHOWN; $ret->image = 'components/com_joomla_lms/lms_images/toolbar/btn_publish_hidden.png'; }
			elseif (!$in_date && $show == 0 && $expired == 1) { $ret->state = 0; $ret->show = 0; $ret->pending_expired =  1; $ret->alt = _JLMS_TOPIC_EXPIRED.'/'._JLMS_TOPIC_HIDDEN; $ret->image = 'components/com_joomla_lms/lms_images/toolbar/btn_cancel.png'; }
			elseif (!$in_date && $show == 0 && $expired == 0) { $ret->state = 0; $ret->show = 0; $ret->pending_expired = -1; $ret->alt = _JLMS_TOPIC_PENDING.'/'._JLMS_TOPIC_HIDDEN; $ret->image = 'components/com_joomla_lms/lms_images/toolbar/btn_cancel.png'; }
		} else {
			if ($show == 1) { $ret->state = 0; $ret->show = 1; $ret->alt = _JLMS_TOPIC_UNPUBLISHED.'/'._JLMS_TOPIC_SHOWN; $ret->image = 'components/com_joomla_lms/lms_images/toolbar/btn_publish_hidden.png'; }
			elseif ($show == 0) { $ret->state = 0; $ret->show = 0;  $ret->alt = _JLMS_TOPIC_UNPUBLISHED.'/'._JLMS_TOPIC_HIDDEN; $ret->image = 'components/com_joomla_lms/lms_images/toolbar/btn_cancel.png'; }
		}
	} else {
		if ($published == 1) {
			if ($in_date) { $ret->state = 1; $ret->show = 1; $ret->pending_expired = 0; $ret->alt = _JLMS_TOPIC_PUBLISHED; $ret->image = 'components/com_joomla_lms/lms_images/toolbar/btn_accept.png'; }
			elseif (!$in_date && $expired == 1) { $ret->state = 0; $ret->show = 1; $ret->pending_expired =  1; $ret->alt = _JLMS_TOPIC_EXPIRED; $ret->image = 'components/com_joomla_lms/lms_images/toolbar/btn_expired.png'; }
			elseif (!$in_date && $expired == 0) { $ret->state = 0; $ret->show = 1; $ret->pending_expired = -1; $ret->alt = _JLMS_TOPIC_PENDING; $ret->image = 'components/com_joomla_lms/lms_images/toolbar/btn_expired.png'; }
		} else { $ret->state = 0; $ret->show = 1; $ret->pending_expired = 0; $ret->alt = _JLMS_TOPIC_UNPUBLISHED; $ret->image = 'components/com_joomla_lms/lms_images/toolbar/btn_cancel.png';	}
	}
	return $ret;
}
function publishUtility_topic ($published, $start_flag=0, $end_flag=0, $start_date="0000-00-00", $end_date="0000-00-00") {
	$ret = new publishOptionClass();
	$date = date("Y-m-d");
	$in_date = (($start_flag == 0 || $start_date <= $date) && ($end_flag == 0 || $end_date >= $date));
	$expired = ($date > $end_date) ? 1 : 0; //only shows if element expired, doesn't show pending option
	if ($published == 1) {
		if ($in_date) { $ret->state = 1; $ret->pending_expired = 0; $ret->alt = _JLMS_TOPIC_PUBLISHED; $ret->image = 'components/com_joomla_lms/lms_images/toolbar/btn_accept.png'; }
		elseif (!$in_date && $expired == 1) { $ret->state = 1; $ret->pending_expired =  1; $ret->alt = _JLMS_TOPIC_EXPIRED; $ret->image = 'components/com_joomla_lms/lms_images/toolbar/btn_expired.png'; }
		elseif (!$in_date && $expired == 0) { $ret->state = 1; $ret->pending_expired = -1; $ret->alt = _JLMS_TOPIC_PENDING; $ret->image = 'components/com_joomla_lms/lms_images/toolbar/btn_expired.png'; }
	} else { $ret->state = 0; $ret->pending_expired = 0; $ret->alt = _JLMS_TOPIC_UNPUBLISHED; $ret->image = 'components/com_joomla_lms/lms_images/toolbar/btn_cancel.png';	}
	return $ret;
}



function JLMS_orderUpIcon_topic( $i, $id, $condition=true, $task='orderup_topic', $alt='' ) {
	global $JLMS_CONFIG;
	$ret_str = '';
	if (!$alt) $alt = _JLMS_TOPIC_T_MOVEUP;
	if (($i > 0) && $condition) {
		$ret_str = '<a class="jlms_img_link" href="javascript:topicSubmit(\''.$id.'\',\''.$task.'\');" title="'.$alt.'">';
		$ret_str .= '<img class="JLMS_png" src="'.$JLMS_CONFIG->get('live_site').'/components/com_joomla_lms/lms_images/toolbar/btn_uparrow.png" width="16" height="16" border="0" alt="'.$alt.'" />';
		$ret_str .= '</a>';
	} else {
		$ret_str = '&nbsp;';
	}
	return $ret_str;
}
function JLMS_orderDownIcon_topic( $i, $n, $id, $condition=true, $task='orderdown_topic', $alt='' ) {
	global $JLMS_CONFIG;
	$ret_str = '';
	if (!$alt) $alt = _JLMS_TOPIC_T_MOVEDOWN;
	if (($i < $n-1) && $condition) {
		$ret_str = '<a class="jlms_img_link" href="javascript:topicSubmit(\''.$id.'\',\''.$task.'\');" title="'.$alt.'">';
		$ret_str .= '<img class="JLMS_png" src="'.$JLMS_CONFIG->get('live_site').'/components/com_joomla_lms/lms_images/toolbar/btn_downarrow.png" width="16" height="16" border="0" alt="'.$alt.'" />';
		$ret_str .= '</a>';
	} else {
		$ret_str = '&nbsp;';
	}
	return $ret_str;
}
function JLMS_deleteIcon_topic ($topic_id, $alt='') {
	global $JLMS_CONFIG;
	if (!$alt) $alt = _JLMS_TOPIC_T_DELETE;
	$ret_str = '<a class="jlms_img_link" href="javascript:topicDelete(\''.$topic_id.'\');" title="'.$alt.'">';
	$ret_str .= '<img class="JLMS_png" src="'.$JLMS_CONFIG->get('live_site').'/components/com_joomla_lms/lms_images/buttons_22/btn_delete_22.png" width="16" height="16" border="0" alt="'.$alt.'" />';
	$ret_str .= '</a>';
	return $ret_str;
}
function JLMS_addElementIcon_topic ($topic_id, $alt='') {
	global $JLMS_CONFIG;
	if (!$alt) $alt = _JLMS_TOPIC_T_LINK_ELEMENT;
	$ret_str = '<a class="jlms_img_link" href="javascript:topicAdd(\''.$topic_id.'\');" title="'.$alt.'">';
	$ret_str .= '<img class="JLMS_png" src="'.$JLMS_CONFIG->get('live_site').'/components/com_joomla_lms/lms_images/buttons_22/btn_add_22.png" width="16" height="16" border="0" alt="'.$alt.'" />';
	$ret_str .= '</a>';
	return $ret_str;
}
function JLMS_editTopicIcon ($course_id, $topic_id) {
	global $option, $Itemid, $JLMS_CONFIG;
	//	$link = sefRelToAbs("index.php?option=$option&amp;task=edit_topic&amp;id=$course_id&amp;topic_id=$topic_id&amp;Itemid=$Itemid");
	$link = sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=edit_topic&id=$course_id&topic_id=$topic_id");
	$link = "index.php?option=$option&amp;Itemid=$Itemid&amp;task=edit_topic&amp;id=$course_id&amp;topic_id=$topic_id";
	$ret_str = '<a class="jlms_img_link" href="'.$link.'" title="'._JLMS_TOPIC_T_EDIT.'">';
	$ret_str .= '<img class="JLMS_png" src="'.$JLMS_CONFIG->get('live_site').'/components/com_joomla_lms/lms_images/buttons_22/btn_add_22.png" width="16" height="16" border="0" alt="'._JLMS_TOPIC_T_EDIT.'" />';
	$ret_str .= '</a>';
	return $ret_str;
}
function JLMS_publishIcon_topic($topic_id, $course_id, $publish_options) {
	global $Itemid, $option, $JLMS_CONFIG;
	$link = "index.php?option=".$option."&amp;Itemid=".$Itemid."&amp;task=publish_topic&amp;state=".(1-$publish_options->state)."&amp;id=".$course_id."&amp;topic_id=$topic_id";
	$link = sefRelToAbs($link);
	$ret_str = '<a class="jlms_img_link" href="'.$link.'" title="'.$publish_options->alt.'">';
	$ret_str .= '<img class="JLMS_png" src="'.$JLMS_CONFIG->get('live_site').'/'.$publish_options->image.'" width="16" height="16" border="0" alt="'.$publish_options->alt.'" />';
	$ret_str .= '</a>';
	return $ret_str;
}
function JLMS_showHideIcon( $id, $course_id, $topic_id, $p_options, $task, $option, $add_options = '' ) {
	global $Itemid, $JLMS_CONFIG;
	$t_id				= intval( mosGetParam( $_REQUEST, 't_id', 0 ));
	$link = "index.php?option=$option&amp;Itemid=$Itemid&amp;task=$task&amp;state=$p_options->show&amp;id=$course_id&amp;topic_id=$topic_id&amp;eid=$id".($t_id?"&amp;t_id=".$t_id:"");
	$ret_str = '<a class="jlms_img_link" '.$add_options.'href="'.JLMSRoute::_($link).'" title="'.$p_options->alt.'">';
	$ret_str .= '<img class="JLMS_png" src="'.$JLMS_CONFIG->get('live_site').'/'.$p_options->image.'" width="16" height="16" border="0" alt="'.$p_options->alt.'" />';
	$ret_str .= '</a>';
	return $ret_str;
}
function JLMS_orderIcon_element ($topic_id, $course_id, $task, $alt, $ordering) {
	global $option, $Itemid, $JLMS_CONFIG;
	$t_id = intval( mosGetParam( $_REQUEST, 't_id', 0 ));
	$link = JLMSRoute::_("index.php?option=$option&amp;Itemid=$Itemid&amp;task=$task&amp;id=$course_id&amp;topic_id=$topic_id&amp;element_ordering=$ordering".($t_id?"&amp;t_id=".$t_id:""));
	$ret_str = '';
	$ret_str = '<a class="jlms_img_link" href="'.$link.'" title="'.$alt.'">';
	$img = ($task == 'orderdown_element') ? 'btn_downarrow.png' : 'btn_uparrow.png';
	$ret_str .= '<img class="JLMS_png" src="'.$JLMS_CONFIG->get('live_site').'/components/com_joomla_lms/lms_images/toolbar/'.$img.'" width="16" height="16" border="0" alt="'.$alt.'" />';
	$ret_str .= '</a>';
	return $ret_str;
}
function JLMS_orderElement ($topic_id, $course_id, $def, $ordering, $t_id) {
	global $JLMS_DB, $option, $Itemid;
	$query = "UPDATE #__lms_topic_items SET ordering=ordering+($def)+99999 WHERE course_id=$course_id AND topic_id=$topic_id AND ordering=$ordering";
	$JLMS_DB->setQuery($query);
	$JLMS_DB->query();
	$query = "UPDATE #__lms_topic_items SET ordering=ordering-($def) WHERE course_id=$course_id AND topic_id=$topic_id AND ordering=$ordering+($def)";
	$JLMS_DB->setQuery($query);
	$JLMS_DB->query();
	$query = "UPDATE #__lms_topic_items SET ordering=ordering-99999 WHERE course_id=$course_id AND topic_id=$topic_id AND ordering>9999";
	$JLMS_DB->setQuery($query);
	$JLMS_DB->query();
	$msg = _JLMS_TOPIC_I_REORDERED;
	JLMSRedirect( sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=details_course&id=$course_id".($t_id?"&t_id=".$t_id:"")."#topic_$topic_id"), $msg);
}
function JLMS_deleteElement ($topic_id, $course_id, $t_id) {
	global $JLMS_DB, $Itemid, $option, $JLMS_CONFIG;
	$usertype = $JLMS_CONFIG->get('current_usertype', 0);
	if ( ($course_id == $JLMS_CONFIG->get('course_id')) && ($usertype == 1) ) {
		$cid = mosGetParam( $_REQUEST, 'cid', array(0) );
		$query = "DELETE FROM #__lms_topic_items WHERE id IN (".implode(',', $cid).")";
		$JLMS_DB->setQuery($query);
		$JLMS_DB->query();
		fixElementOrder($topic_id);
		$msg = _JLMS_TOPIC_I_DELETED;
	}
	JLMSRedirect( sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=details_course&id=$course_id".($t_id?"&t_id=".$t_id:"")."#topic_$topic_id"), $msg);
}
?>