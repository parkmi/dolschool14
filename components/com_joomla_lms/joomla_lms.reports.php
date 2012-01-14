<?php
/**
* joomla_lms.reports.php
* (c) JoomaLMS eLearning Software http://www.joomlalms.com/
**/

// no direct access
defined( '_JLMS_EXEC' ) or die( 'Restricted access' );
require_once(_JOOMLMS_FRONT_HOME . "/joomla_lms.reports.html.php");

if(!function_exists('JLMS_GB_getUsersGrades')){
	require_once(_JOOMLMS_FRONT_HOME . "/includes/lms_grades.lib.php");
}
require_once(_JOOMLMS_FRONT_HOME . "/includes/libraries/lms.lib.reporting.php");

$is_full 	= mosGetParam( $_REQUEST, 'is_full', 0);

$task 	= mosGetParam( $_REQUEST, 'task', '' );
//if ($task == 'mailbox_new' || $task == 'mailbox' || $task == 'mail_sendbox' || $task == 'mail_send' || $task == 'mail_view' ) {
	global $JLMS_CONFIG;
	$course_id = $JLMS_CONFIG->get('course_id',0);
	$pathway = array();
	$pathway[] = array('name' => _JLMS_PATHWAY_HOME, 'link' => sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid"), 'is_home' => true);
	//$pathway[] = array('name' => $JLMS_CONFIG->get('course_name'), 'link' => sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid&amp;task=details_course&amp;id=$course_id"), 'is_course' => true);
	if($task == 'report_access'){
		$pth_head = _JLMS_REPORTS_ACCESS;
	}else if($task == 'report_certif'){
		$pth_head = _JLMS_REPORTS_CONCLUSION;
	}else if($task == 'report_grade'){
		$pth_head = _JLMS_REPORTS_USER;
	}else if($task == 'report_scorm'){
		$pth_head = _JLMS_REPORTS_SCORM;
	}
	$pathway[] = array('name' => $pth_head, 'link' => sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid&amp;task=$task"));
	JLMSAppendPathWay($pathway);
	if(!$is_full && mosGetParam($_REQUEST,'view') != 'csv' && mosGetParam($_REQUEST,'view') != 'xls') {
		JLMS_ShowHeading();
	}
	if ($is_full && mosGetParam($_REQUEST,'view') != 'csv' && mosGetParam($_REQUEST,'view') != 'xls') {
		/*echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">'."\n";
		echo '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-gb" lang="en-gb">'."\n";
		echo '<head>'."\n";
		echo '<title>'.($JLMS_CONFIG->get('jlms_title') ? ($JLMS_CONFIG->get('jlms_title').' - ') : '').$pth_head.'</title>'."\n";
		echo '<style type="text/css">'."\n";
		echo 'table.jlms_report_fullview_table td, table.jlms_report_fullview_table th {'."\n";
		echo '	border-top:1px solid grey;'."\n";
		echo '	border-right:1px solid grey;'."\n";
		echo '}'."\n";
		echo 'table.jlms_report_fullview_table {'."\n";
		echo '	border-bottom:1px solid grey;'."\n";
		echo '	border-left:1px solid grey;'."\n";
		echo '}'."\n";
		echo 'tr.jlms_report_fullview_row td, tr.jlms_report_fullview_row_bottom td {'."\n";
		echo '	border-top:1px solid grey;'."\n";
		echo '	border-right:1px solid grey;'."\n";
		echo '}'."\n";
		echo 'tr.jlms_report_fullview_row td.first_td, tr.jlms_report_fullview_row_bottom td.first_td {'."\n";
		echo '	border-left:1px solid grey;'."\n";
		echo '}'."\n";
		echo 'tr.jlms_report_fullview_row_bottom td {'."\n";
		echo '	border-bottom:1px solid grey;'."\n";
		echo '}'."\n";
		echo '</style>'."\n";
		echo '</head>'."\n";
		echo '<body>'."\n";*/
		echo '<link rel="stylesheet" href="'.JLMSCSS::link().'" type="text/css" />';

	}
//}

$id 	= intval( mosGetParam( $_REQUEST, 'id', 0 ) ); 
$cid = mosGetParam( $_POST, 'cid', array(0) );
if (!is_array( $cid )) { $cid = array(0); }

//echo $task; die;

switch ($task) {
	case 'report_access':	JLMS_sreportAccess( $option, $is_full ); 	break;
	case 'report_certif':	JLMS_sreportCertif( $option, $is_full );  	break;
	case 'report_grade':	JLMS_sreportGrade( $option, $is_full );  	break;
	case 'report_scorm':	JLMS_sreportScorm( $option, $is_full );  	break;
}
if ($is_full && mosGetParam($_REQUEST,'view') != 'csv' && mosGetParam($_REQUEST,'view') != 'xls') {
	//$JLMS_CONFIG = & JLMSFactory::getConfig();
	//$JLMS_CONFIG->set('add_html_at_the_end', '</body></html>');
}

function JLMS_sreportScorm($option, $is_full=0){
	global $JLMS_DB, $JLMS_CONFIG, $JLMS_SESSION, $task, $option, $my, $Itemid;
	
	$JLMS_ACL = & JLMSFactory::getACL();
	
	$view = mosGetParam($_REQUEST, 'view', '');

	if($view == 'csv' || $view == 'xls') {
		$is_full = 1;
	}
	
	$limit		= intval( mosGetParam($_GET, 'limit', $JLMS_SESSION->get('list_limit', $JLMS_CONFIG->get('list_limit')) ) );
	$JLMS_SESSION->set('list_limit', $limit);
	$limitstart = intval( mosGetParam($_GET, 'limitstart', 0 ) );
	
	$filt_group = intval(mosGetParam($_REQUEST, 'filt_group', $JLMS_SESSION->get('filt_group', 0)));
	
	$filt_cat = intval( mosGetParam( $_REQUEST, 'filt_cat', 0 ) );
	
	$filt_course_id = intval( mosGetParam( $_REQUEST, 'filt_course_id', 0 ) );
	
	$start_date	= (mosGetParam( $_REQUEST, 'start_date', "" ));
	$end_date 	= (mosGetParam( $_REQUEST, 'end_date', "" ));
	
	$s_date_db = '';
	$start_date = ($start_date == "-")?"":$start_date;
	if($start_date){
		$start_date = JLMS_dateToDB($start_date);
		$s_date = explode('-',$start_date);
		$s_date_db = date("Y-m-d",mktime(0,0,0,$s_date[1],$s_date[2],$s_date[0]));
	}	
	$e_date_db = '';
	$end_date = ($end_date == "-")?"":$end_date;
	if($end_date){
		$end_date = JLMS_dateToDB($end_date);
		$e_date = explode('-',$end_date);
		$e_date_db = date("Y-m-d",mktime(23,59,0,$e_date[1],$e_date[2],$e_date[0]));	
	}	
	
	$lists = array();
	
	//FLMS multicat
	$levels = array();
	if ($JLMS_CONFIG->get('multicat_use', 0)){
		$query = "SELECT * FROM #__lms_course_cats_config ORDER BY id";
		$JLMS_DB->setQuery($query);
		$levels = $JLMS_DB->loadObjectList();
		if(count($levels) == 0){
			for($i=0;$i<5;$i++){
				if($i>0){
					$levels[$i]->cat_name = _JLMS_COURSES_COURSES_GROUPS;	
				} else {
					$levels[$i]->cat_name = _JLMS_COURSES_COURSES_GROUPS;
				}
			}
		}

		$level_id = array();
		for($i=0;$i<count($levels);$i++){
			if($i == 0){
				$level_id[$i] = intval( mosGetParam( $_REQUEST, 'filter_id_'.$i.'', $JLMS_SESSION->get('FLMS_filter_id_'.$i.'', 0) ) );
				$_REQUEST['filter_id_'.$i] = $level_id[$i];
				$JLMS_SESSION->set('FLMS_filter_id_'.$i.'', $level_id[$i]);
			} else {
				$level_id[$i] = intval( mosGetParam( $_REQUEST, 'filter_id_'.$i.'', $JLMS_SESSION->get('FLMS_filter_id_'.$i.'', 0) ) );
				$_REQUEST['filter_id_'.$i] = $level_id[$i];
				$JLMS_SESSION->set('FLMS_filter_id_'.$i.'', $level_id[$i]);
			}
			if($i == 0){
				$parent_id[$i] = 0;
			} else {
				$parent_id[$i] = $level_id[$i-1];
			}
			if($i == 0 || $parent_id[$i]){ //(Max): extra requests
				$query = "SELECT count(id) FROM `#__lms_course_cats` WHERE parent = '".$parent_id[$i]."' ORDER BY c_category";
				$JLMS_DB->setQuery($query);
				$groups = $JLMS_DB->loadResult();
				if($groups==0){
					$level_id[$i] = 0;	
					$JLMS_SESSION->set('FLMS_filter_id_'.$i.'', $level_id[$i]);
				}
			}
		}
		
		for($i=0;$i<count($levels);$i++){
			if($i > 0 && $level_id[$i - 1] == 0){
				$level_id[$i] = 0;
				$_REQUEST['filter_id_'.$i] = $level_id[$i];
				$JLMS_SESSION->set('FLMS_filter_id_'.$i.'', $level_id[$i]);
				$parent_id[$i] = 0;
			} elseif($i == 0 && $level_id[$i] == 0) {
				$level_id[$i] = 0;
				$_REQUEST['filter_id_'.$i] = $level_id[$i];
				$JLMS_SESSION->set('FLMS_filter_id_'.$i.'', $level_id[$i]);
				$parent_id[$i] = 0;
			}
		}
		
		$javascript = 'onclick="javascript:read_filter();" onchange="javascript:write_filter();submitFormView(\'\');"';
		
		if(class_exists('JFactory')){
			$user = JLMSFactory::getUser();
			$my->id = $user->id;
		}
		$lists['user_id'] = $my->id;
		
		$query1 = "SELECT group_id FROM `#__lms_users_in_global_groups` WHERE user_id = '".$my->id."'";
		$JLMS_DB->setQuery($query1);
		$user_group_ids = $JLMS_DB->loadResultArray();
		
		$categories_reporting = array();
		$name_categories_reporting = array();
		
		for($i=0;$i<count($levels);$i++) {
			if($i == 0 || $parent_id[$i]){ //(Max): extra requests
				if( $parent_id[$i] == 0 && (!$JLMS_ACL->CheckPermissions('lms', 'create_course'))) {
					$query = "SELECT * FROM `#__lms_course_cats` WHERE `parent` = '0'";
	
					$query .= "\n AND (";
					if (count($user_group_ids)) {
						$query .= "( `restricted` = 1 AND ( `groups` LIKE '%|$user_group_ids[0]|%'";
						for($i1=1;$i1<count($user_group_ids);$i1++) {
							$query .= "\n OR `groups` like '%|$user_group_ids[$i1]|%'";
						}
						$query .=  "\n ) ) \n OR ";
					}
					$query .= "(`restricted` = 0 )) ";
					$query .= "\n ORDER BY `c_category`";
				}
				else {
					$query = "SELECT * FROM `#__lms_course_cats` WHERE parent = '".$parent_id[$i]."' ORDER BY c_category";
				}
				
				$JLMS_DB->setQuery($query);
				$groups = $JLMS_DB->loadObjectList();
				
				if($parent_id[$i] && $i > 0 && count($groups)) {
					$type_level[$i][] = mosHTML::makeOption( 0, ' &nbsp; ' );
					foreach ($groups as $group){
						$type_level[$i][] = mosHTML::makeOption( $group->id, $group->c_category );
						if($group->id == $level_id[$i]){
							$name_categories_reporting[] = $levels[$i]->cat_name;
							$categories_reporting[] = $group->c_category;
						}
					}
					$lists['filter_'.$i.''] = mosHTML::selectList($type_level[$i], 'filter_id_'.$i.'', 'class="inputbox" size="1" style="width: 100%;" '.$javascript, 'value', 'text', $level_id[$i] ); //onchange="document.location.href=\''. $link_multi .'\';"
				} elseif($i == 0) {
					$type_level[$i][] = mosHTML::makeOption( 0, ' &nbsp; ' );
					foreach ($groups as $group){
						$type_level[$i][] = mosHTML::makeOption( $group->id, $group->c_category );
						if($group->id == $level_id[$i]){
							$name_categories_reporting[] = $levels[$i]->cat_name;
							$categories_reporting[] = $group->c_category;
						}
					}
					$lists['filter_'.$i.''] = mosHTML::selectList($type_level[$i], 'filter_id_'.$i.'', 'class="inputbox" size="1" style="width: 100%;" '.$javascript, 'value', 'text', $level_id[$i] ); //onchange="document.location.href=\''. $link_multi .'\';"
				}
			}
		}
		$reporting_header['name_categories'] = $name_categories_reporting;
		$reporting_header['categories'] = $categories_reporting;	
	}
	//FLMS multicat
	global $JLMS_DB, $my;
	if(class_exists('Jfactory')){
		$user = JLMSFactory::getUser();
		$my->id = $user->id;	
	}
	
	$is_ceo = $JLMS_ACL->isStaff();
	
	$courses = array();
	if($JLMS_ACL->isTeacher()){
		$courses = $JLMS_CONFIG->get('teacher_in_courses', array() );
	} else 
	if($is_ceo){
		$query = "SELECT user_id FROM #__lms_user_parents WHERE parent_id = '".$my->id."'";
		$JLMS_DB->setQuery($query);
		$users = $JLMS_DB->loadResultArray();
		if(count($users)){
			$query = "SELECT course_id FROM #__lms_users_in_groups WHERE user_id IN (".implode(",", $users).")";
			$JLMS_DB->setQuery($query);
			$courses = $JLMS_DB->loadResultArray();
		}
	}
	
	if(count($courses)){
		$where = "";
		if ($JLMS_CONFIG->get('multicat_use', 0)){
			//NEW MUSLTICATS
//			$tmp_level = array();
			$last_catid = 0;
			
			$tmp_cats_filter = JLMS_getFilterMulticategories($last_catid);
			/*
			$i=0;
			foreach($_REQUEST as $key=>$item){
				if(preg_match('#filter_id_(\d+)#', $key, $result)){
					if($item){
						$tmp_level[$i] = $result;
						$last_catid = $item;
						$i++;
					}	
				}	
			}
			$filt_cat = $last_catid;
			$query = "SELECT * FROM #__lms_course_cats ORDER BY id";
			$JLMS_DB->setQuery($query);
			$all_cats = $JLMS_DB->loadObjectList();
			
			$tmp_cats_filter = array();
			$children = array();
			foreach($all_cats as $cat){
				$pt = $cat->parent;
				$list = @$children[$pt] ? $children[$pt] : array();
				array_push($list, $cat->id);
				$children[$pt] = $list;
			}
			$tmp_cats_filter[0] = $last_catid;
			$i=1;
			foreach($children as $key=>$childs){
				if($last_catid == $key){
					foreach($children[$key] as $v){
						if(!in_array($v, $tmp_cats_filter)){
							$tmp_cats_filter[$i] = $v;
							$i++;
						}
					}
				}
			}
			foreach($children as $key=>$childs){
				if(in_array($key, $tmp_cats_filter)){
					foreach($children[$key] as $v){
						if(!in_array($v, $tmp_cats_filter)){
							$tmp_cats_filter[$i] = $v;
							$i++;
						}
					}
				}
			}
			$tmp_cats_filter = array_unique($tmp_cats_filter);
			*/
			$catids = implode(",", $tmp_cats_filter);
			
			if($last_catid && count($tmp_cats_filter)){
				$where .= "\n AND ( cat_id IN (".$catids.")";
				if($JLMS_CONFIG->get('sec_cat_use', 0)){
					foreach ($tmp_cats_filter as $tmp_cats_filter_one) {
						$where .= "\n OR sec_cat LIKE '%|".$tmp_cats_filter_one."|%'";
					}
				}
				$where .= "\n )";
			}
			//NEW MUSLTICATS
		}
		
		$courses_str = implode(",", $courses);
		$query = "SELECT id FROM #__lms_courses WHERE id IN(".$courses_str.")"
		. $where
		. "\n ORDER BY id"
		;
		$JLMS_DB->setQuery($query);
		$courses = $JLMS_DB->loadResultArray();
		
		$javascript2 = 'onchange="javascript:submitFormView();"';
		$query = "SELECT id as value, course_name as text FROM #__lms_courses WHERE id IN (".$courses_str.")"
		. $where
		. "\n ORDER BY "
		.($JLMS_CONFIG->get('lms_courses_sortby') ? "ordering" : "course_name")
		;	
		$JLMS_DB->setQuery($query);
		$list_courses = $JLMS_DB->loadObjectList();
		
		$f_courses = array();
		$f_courses[] = mosHTML::makeOption(0, '&nbsp;');
		$f_courses = array_merge($f_courses, $list_courses);
		$lists['filt_course'] = mosHTML::selectList($f_courses, 'filt_course_id', 'class="inputbox" size="1" style="width: 100%;" '.$javascript2, 'value', 'text', $filt_course_id );

		$g_items = array();
		$g_items[] = mosHTML::makeOption(0, _JLMS_ATT_FILTER_ALL_GROUPS);
		if ($JLMS_CONFIG->get('use_global_groups', 1)) {
			if($is_ceo){
				$cid = $users;
			} else {
				if (!count($courses)) {
					$courses = array(0);
				}
				$query = "SELECT user_id FROM #__lms_users_in_groups WHERE course_id IN (".implode(',',$courses).")";
				$JLMS_DB->setQuery($query);
				$cid = $JLMS_DB->loadResultArray();	
			}
			if (!$cid) $cid = array(-1);
			$query = "SELECT group_id FROM #__lms_users_in_global_groups WHERE user_id IN (".implode(',', $cid).")";
			$JLMS_DB->setQuery($query);
			$gid = $JLMS_DB->loadResultArray();
			if (!$gid) $gid = array(-1);
			$query = "SELECT distinct id AS value, ug_name AS text FROM #__lms_usergroups WHERE id IN (".implode(',', $gid).") AND course_id = 0 ORDER BY text";//course id check just in case))
			$JLMS_DB->setQuery($query);
			$groups = $JLMS_DB->loadObjectList();
		} else {
			if (!count($courses)) {
				$courses = array(0);
			}
			$query = "SELECT distinct a.id as value, a.ug_name as text FROM #__lms_usergroups as a, #__lms_users_in_groups as b"
			. "\n WHERE a.course_id IN (".implode(',',$courses).") AND b.group_id = a.id ORDER BY a.ug_name";
			$JLMS_DB->SetQuery( $query );
			$groups = $JLMS_DB->LoadObjectList();
		}
		$g_items = array_merge($g_items, $groups);
		
		$lists['filt_group'] = mosHTML::selectList($g_items, 'filt_group', 'class="inputbox" size="1" style="width: 100%;" '.$javascript2, 'value', 'text', $filt_group );
		
		
		if($filt_group){
			if ($JLMS_CONFIG->get('use_global_groups', 1)) {
				$query = "SELECT user_id"
				. "\n FROM #__lms_users_in_global_groups ugg"
				. "\n WHERE 1"
				. "\n AND group_id = '".$filt_group."'"
				;
				$JLMS_DB->setQuery($query);
				$filt_grp_users = $JLMS_DB->loadResultArray();
			} else {
				$query = "SELECT user_id"
				. "\n FROM #__lms_user_in_groups"
				. "\n WHERE 1"
				. "\n AND group_id = '".$filt_group."'"
				;	
				$JLMS_DB->setQuery($query);
				$filt_grp_users = $JLMS_DB->loadResultArray();
			}
		}
		
		$query = "SELECT COUNT(*)"
		. "\n FROM #__lms_courses as c, #__lms_users_in_groups as ug"
		. "\n LEFT JOIN #__lms_certificate_users as cu ON cu.course_id = ug.course_id AND cu.user_id = ug.user_id"
		. "\n, #__lms_learn_paths as lp, #__users as u"
		. "\n WHERE 1"
		. "\n AND c.id = ug.course_id"
		. "\n AND c.id = lp.course_id"
		. "\n AND lp.item_id <> 0"
		. "\n AND lp.lp_type = 1"
		. "\n AND lp.published = 1"
		. "\n AND ug.user_id = u.id"
		. "\n AND c.id IN (".implode(",", $courses).")"
		.($filt_course_id ? "\n AND c.id = '".$filt_course_id."'" : "")
		.($filt_group ? "\n AND ug.user_id IN (".implode(",", $filt_grp_users).")" : "")
		.($is_ceo ? "\n AND ug.user_id IN (".implode(",", $users).")" : "")
		;
		$JLMS_DB->setQuery($query);
		$total = $JLMS_DB->loadResult();
		
		require_once(_JOOMLMS_FRONT_HOME . DS . "includes" . DS . "classes" . DS . "lms.pagination.php");
		$pageNav = new JLMSPageNav( $total, $limitstart, $limit );
		
		$query = "SELECT"
		. "\n c.id as course_id, c.course_name"
		. "\n, lp.id, lp.lpath_name, lp.item_id"
		. "\n, u.id as user_id, u.username, u.name, u.email"
		. "\n, cu.crt_option, cu.crt_date"
		. "\n FROM #__lms_courses as c, #__lms_users_in_groups as ug"
		. "\n LEFT JOIN #__lms_certificate_users as cu ON cu.course_id = ug.course_id AND cu.user_id = ug.user_id"
		. "\n, #__lms_learn_paths as lp, #__users as u"
		. "\n WHERE 1"
		. "\n AND c.id = ug.course_id"
		. "\n AND c.id = lp.course_id"
		. "\n AND lp.item_id <> 0"
		. "\n AND lp.lp_type = 1"
		. "\n AND lp.published = 1"
		. "\n AND ug.user_id = u.id"
		. "\n AND c.id IN (".implode(",", $courses).")"
		.($filt_course_id ? "\n AND c.id = '".$filt_course_id."'" : "")
		.($filt_group ? "\n AND ug.user_id IN (".implode(",", $filt_grp_users).")" : "")
		.($is_ceo ? "\n AND ug.user_id IN (".implode(",", $users).")" : "")
		. "\n ORDER BY "
		.($JLMS_CONFIG->get('lms_courses_sortby') ? "c.ordering" : "c.course_name")
		. "\n, u.username, lp.lpath_name" 
		;
		if(strlen($s_date_db) || strlen($e_date_db)){
			$JLMS_DB->setQuery($query);
		} else {
			if($is_full){
				$JLMS_DB->setQuery($query);
			} else {
				$JLMS_DB->setQuery($query, $limitstart, $limit);
			}
		}
		$scorm_list = $JLMS_DB->loadObjectList();
		
		$user_scorms = array();
		foreach($scorm_list as $scorm){
			$user_scorms[$scorm->user_id][] = $scorm->item_id;	
		}
		
		require_once(_JOOMLMS_FRONT_HOME . "/includes/lms_scorm.lib.php");
		
		$result = array();
		foreach($user_scorms as $user_id=>$scorms){
			$uids = array();
			$uids[] = $user_id;
			
			$scn_ids = array();
			$scn_ids = $scorms;
			
			$result[$user_id]->scorm_data = & JLMS_Get_N_SCORM_userResults($uids, $scn_ids, 0);
		}
		
		$new_scorm_list = array();
		foreach($scorm_list as $n=>$scorm){
			$new_scorm_list[$n] = $scorm;
			
			$new_scorm_list[$n]->course_status = 0;	
			if(isset($scorm->crt_option) && $scorm->crt_option){
				$new_scorm_list[$n]->course_status = 1;	
			}
			
			if(isset($result[$scorm->user_id]->scorm_data)){
				$tmp = $result[$scorm->user_id]->scorm_data;
				foreach($tmp as $t){
					if($t->content_id == $scorm->item_id){
						$scorm_data = new stdClass();
						$scorm_data = $t;
						$scorm_data->start = $t->at_start;
						$scorm_data->end = $t->scn_timemodified;
						
						$new_scorm_list[$n]->scorm_data = $scorm_data;	
					}	
				}	
			}	
		}
		
		if(strlen($s_date_db) || strlen($e_date_db)){
			$start = strtotime($s_date_db);	
			$end = strtotime($e_date_db);
			
			$tmp = array();
			foreach($new_scorm_list as $scorm){
				$add = false;
				if($start){
					$add = false;
					if(isset($scorm->scorm_data->end) && $scorm->scorm_data->end && $start <= $scorm->scorm_data->end){
						$add = true;	
					}	
				}	
				if($end){
					$add = false;
					if(isset($scorm->scorm_data->end) && $scorm->scorm_data->end && $end >= $scorm->scorm_data->end){
						$add = true;	
					}	
				}
				if($add){
					$tmp[] = $scorm;	
				}
			}
			if(count($tmp)){
				$new_scorm_list = array();
				$new_scorm_list = $tmp;	
			}
			
			$total = count($new_scorm_list);
			
			$pageNav = new JLMSPageNav( $total, $limitstart, $limit );
			
			if(!$is_full){
				$tmp = array();
				for($i=($limitstart - 1);$i<($limitstart + $limit);$i++){
					if(isset($new_scorm_list[$i])){
						$tmp[] = $new_scorm_list[$i];
					}
				}
				if(count($tmp)){
					$new_scorm_list = array();
					$new_scorm_list = $tmp;	
				}
			}
		}
		if(count($new_scorm_list)){
			$rowz = $new_scorm_list;	
		}

		if($view == 'csv') {
			JLMS_REP_exportCsv(array(), array(), array(), $rowz, $pageNav, $lists, $levels, $filt_cat, $filt_group, $option, 1);
		} else {
			JLMS_reports_html::JLMS_sreportScorm($option, $rowz, $start_date, $end_date, $pageNav, $lists, $levels, $filt_cat, $filt_group, $is_full);
		}
	}
}

function JLMS_switchType($option){
	$JLMS_CONFIG = & JLMSFactory::getConfig();
	global $task;
	$type = strval( mosGetParam( $_REQUEST, 'type', $task ) );
	$javascript = 'onchange="javascript:document.adminForm.task.value=document.adminForm.type.options[document.adminForm.type.selectedIndex].value;document.adminForm.view.value=\'\';document.adminForm.submit();"';
	$types = array();
	$types[] = mosHTML::makeOption('report_access', _JLMS_REPORTS_ACCESS);
	$types[] = mosHTML::makeOption('report_certif', _JLMS_REPORTS_CONCLUSION);
	$types[] = mosHTML::makeOption('report_grade', _JLMS_REPORTS_USER);
	if ($JLMS_CONFIG->get('show_scorm_report_link', false)) {
		$types[] = mosHTML::makeOption('report_scorm', _JLMS_REPORTS_SCORM);
	}
	$type = mosHTML::selectList( $types, 'type', 'class="inputbox" size="1" style="width: 100%;" '.$javascript, 'value', 'text', $type);
	return $type;
}

function JLMS_sreportAccess( $option, $is_full ){
	global $JLMS_DB, $JLMS_CONFIG, $JLMS_SESSION, $my, $Itemid;
	
	$JLMS_ACL = & JLMSFactory::getACL();
	
	$view = mosGetParam($_REQUEST,'view', '');

	if($view == 'csv' || $view == 'xls') {
		$is_full = 1;
	}
	
	$start_date	= (mosGetParam( $_REQUEST, 'start_date', "" ));
	$end_date 	= (mosGetParam( $_REQUEST, 'end_date', "" ));
	
	$limit = intval( mosGetParam( $_GET, 'limit', $JLMS_SESSION->get('list_limit', $JLMS_CONFIG->get('list_limit')) ) );
	$JLMS_SESSION->set('list_limit', $limit);
	$limitstart = intval( mosGetParam( $_GET, 'limitstart', 0 ) );
	
	$filt_group = intval( mosGetParam( $_REQUEST, 'filt_group', 0 ) );
	
	$lists = array();
	
	$reporting_header = array();
	
	$filt_cat = intval( mosGetParam( $_REQUEST, 'filt_cat', 0 ) );
	//FLMS multicat
	$levels = array();
	if ($JLMS_CONFIG->get('multicat_use', 0)){
		$query = "SELECT * FROM #__lms_course_cats_config ORDER BY id";
		$JLMS_DB->setQuery($query);
		$levels = $JLMS_DB->loadObjectList();
		if(count($levels) == 0){
			for($i=0;$i<5;$i++){
				if($i>0){
					$levels[$i]->cat_name = _JLMS_COURSES_COURSES_GROUPS;	
				} else {
					$levels[$i]->cat_name = _JLMS_COURSES_COURSES_GROUPS;
				}
			}
		}
		
		$level_id = array();
		for($i=0;$i<count($levels);$i++){
			if($i == 0){
				$level_id[$i] = intval( mosGetParam( $_REQUEST, 'filter_id_'.$i.'', $JLMS_SESSION->get('FLMS_filter_id_'.$i.'', 0) ) );
				$_REQUEST['filter_id_'.$i] = $level_id[$i];
				$JLMS_SESSION->set('FLMS_filter_id_'.$i.'', $level_id[$i]);
			} else {
				$level_id[$i] = intval( mosGetParam( $_REQUEST, 'filter_id_'.$i.'', $JLMS_SESSION->get('FLMS_filter_id_'.$i.'', 0) ) );
				$_REQUEST['filter_id_'.$i] = $level_id[$i];
				$JLMS_SESSION->set('FLMS_filter_id_'.$i.'', $level_id[$i]);
			}
			if($i == 0){
				$parent_id[$i] = 0;
			} else {
				$parent_id[$i] = $level_id[$i-1];
			}
			if($i == 0 || $parent_id[$i]){ //(Max): extra requests
				$query = "SELECT count(id) FROM `#__lms_course_cats` WHERE parent = '".$parent_id[$i]."' ORDER BY c_category";
				$JLMS_DB->setQuery($query);
				$groups = $JLMS_DB->loadResult();
				if($groups==0){
					$level_id[$i] = 0;	
					$JLMS_SESSION->set('FLMS_filter_id_'.$i.'', $level_id[$i]);
				}
			}
		}
		
		for($i=0;$i<count($levels);$i++){
			if($i > 0 && $level_id[$i - 1] == 0){
				$level_id[$i] = 0;
				$_REQUEST['filter_id_'.$i] = $level_id[$i];
				$JLMS_SESSION->set('FLMS_filter_id_'.$i.'', $level_id[$i]);
				$parent_id[$i] = 0;
			} elseif($i == 0 && $level_id[$i] == 0) {
				$level_id[$i] = 0;
				$_REQUEST['filter_id_'.$i] = $level_id[$i];
				$JLMS_SESSION->set('FLMS_filter_id_'.$i.'', $level_id[$i]);
				$parent_id[$i] = 0;
			}
		}
		
		$javascript = 'onclick="javascript:read_filter();" onchange="javascript:write_filter();submitFormView(\'\');"';
		$query1 = "SELECT group_id FROM `#__lms_users_in_global_groups` WHERE user_id = '".$my->id."'";
		$JLMS_DB->setQuery($query1);
		$user_group_ids = $JLMS_DB->loadResultArray();
		
		$categories_reporting = array();
		$name_categories_reporting = array();
		
		for($i=0;$i<count($levels);$i++) {
			if($i == 0 || $parent_id[$i]){ //(Max): extra requests
				if( $parent_id[$i] == 0 && (!$JLMS_ACL->CheckPermissions('lms', 'create_course'))) {
					$query = "SELECT * FROM `#__lms_course_cats` WHERE `parent` = '0'";
	
					$query .= "\n AND (";
					if (count($user_group_ids)) {
						$query .= "( `restricted` = 1 AND ( `groups` LIKE '%|$user_group_ids[0]|%'";
						for($i1=1;$i1<count($user_group_ids);$i1++) {
							$query .= "\n OR `groups` like '%|$user_group_ids[$i1]|%'";
						}
						$query .=  "\n ) ) \n OR ";
					}
					$query .= "(`restricted` = 0 )) ";
					$query .= "\n ORDER BY `c_category`";
				}
				else {
					$query = "SELECT * FROM `#__lms_course_cats` WHERE parent = '".$parent_id[$i]."' ORDER BY c_category";
				}
				
				$JLMS_DB->setQuery($query);
				$groups = $JLMS_DB->loadObjectList();
				
				if($parent_id[$i] && $i > 0 && count($groups)) {
					$type_level[$i][] = mosHTML::makeOption( 0, ' &nbsp; ' );
					foreach ($groups as $group){
						$type_level[$i][] = mosHTML::makeOption( $group->id, $group->c_category );
						if($group->id == $level_id[$i]){
							$name_categories_reporting[] = $levels[$i]->cat_name;
							$categories_reporting[] = $group->c_category;
						}
					}
					$lists['filter_'.$i.''] = mosHTML::selectList($type_level[$i], 'filter_id_'.$i.'', 'class="inputbox" size="1" style="width: 100%;" '.$javascript, 'value', 'text', $level_id[$i] ); //onchange="document.location.href=\''. $link_multi .'\';"
				} elseif($i == 0) {
					$type_level[$i][] = mosHTML::makeOption( 0, ' &nbsp; ' );
					foreach ($groups as $group){
						$type_level[$i][] = mosHTML::makeOption( $group->id, $group->c_category );
						if($group->id == $level_id[$i]){
							$name_categories_reporting[] = $levels[$i]->cat_name;
							$categories_reporting[] = $group->c_category;
						}
					}
					$lists['filter_'.$i.''] = mosHTML::selectList($type_level[$i], 'filter_id_'.$i.'', 'class="inputbox" size="1" style="width: 100%;" '.$javascript, 'value', 'text', $level_id[$i] ); //onchange="document.location.href=\''. $link_multi .'\';"
				}
			}
		}
		$reporting_header['name_categories'] = $name_categories_reporting;
		$reporting_header['categories'] = $categories_reporting;		
	}
	//FLMS multicat
	
	$s_date_db = '';
	$start_date = ($start_date == "-")?"":$start_date;
	$end_date = ($end_date == "-")?"":$end_date;
	if($start_date){
		$start_date = JLMS_dateToDB($start_date);
		$s_date = explode('-',$start_date);
		$s_date_db = date("Y-m-d H:i:s",mktime(0,0,0,$s_date[1],$s_date[2],$s_date[0]));
	}	
	$e_date_db = '';
	if($end_date){
		$end_date = JLMS_dateToDB($end_date);
		$e_date = explode('-',$end_date);
		$e_date_db = date("Y-m-d H:i:s",mktime(23,59,0,$e_date[1],$e_date[2],$e_date[0]));	
	}	
	
	$teacher_in_courses = $JLMS_CONFIG->get('teacher_in_courses', array() );
	$parent_in_courses = array();
	$parent_in_courses = $JLMS_CONFIG->get('parent_in_courses', array() );
	
	$courses = array_merge($teacher_in_courses, $parent_in_courses);
	
	//var_dump($courses);
	if(count($courses)){
		$courses_str = implode(',',$courses);
		$JLMS_DB->setQuery('SELECT id FROM #__lms_courses WHERE id IN('.$courses_str.')');
		$courses = $JLMS_DB->loadResultArray();
		
		$g_items = array();
		$g_items[] = mosHTML::makeOption(0, _JLMS_ATT_FILTER_ALL_GROUPS);
		if ($JLMS_CONFIG->get('use_global_groups', 1)) {
			if (!count($courses)) {
				$courses = array(0);
			}
			$query = "SELECT user_id FROM #__lms_users_in_groups WHERE course_id IN (".implode(',',$courses).")";
			$JLMS_DB->setQuery($query);
			$cid = $JLMS_DB->loadResultArray();
			if (!$cid) $cid = array(-1);
			$query = "SELECT group_id FROM #__lms_users_in_global_groups WHERE user_id IN (".implode(',', $cid).")";
			$JLMS_DB->setQuery($query);
			$gid = $JLMS_DB->loadResultArray();
			if (!$gid) $gid = array(-1);
			$query = "SELECT distinct id AS value, ug_name AS text FROM #__lms_usergroups WHERE id IN (".implode(',', $gid).") AND course_id = 0 ORDER BY text";//course id check just in case))
			$JLMS_DB->setQuery($query);
			$groups = $JLMS_DB->loadObjectList();
		} else {
			if (!count($courses)) {
				$courses = array(0);
			}
			$query = "SELECT distinct a.id as value, a.ug_name as text FROM #__lms_usergroups as a, #__lms_users_in_groups as b"
			. "\n WHERE a.course_id IN (".implode(',',$courses).") AND b.group_id = a.id ORDER BY a.ug_name";
			$JLMS_DB->SetQuery( $query );
			$groups = $JLMS_DB->LoadObjectList();
		}
		$g_items = array_merge($g_items, $groups);
		
		$link = "javascript:document.adminForm.submit();";
		$lists['filter'] = mosHTML::selectList($g_items, 'filt_group', 'class="inputbox" size="1" style="width: 100%;" onchange="'. $link .'"', 'value', 'text', $filt_group );
		
		$groups_reporting = array();
		foreach($groups as $grp){
			if($filt_group && $grp->value == $filt_group){
				$groups_reporting[] = $grp->text;
			}
		}
		$name_groups_reporting[] = 'Usergroup';
		$reporting_header['name_groups'] = $name_groups_reporting;	
		$reporting_header['groups'] = $groups_reporting;	
		
//---	
		$where = '';
		if ($JLMS_CONFIG->get('multicat_use', 0)){
			
			//NEW MUSLTICATS
//			$tmp_level = array();
			$last_catid = 0;
			
			$tmp_cats_filter = JLMS_getFilterMulticategories($last_catid);
			
			$catids = implode(",", $tmp_cats_filter);
			
			if($last_catid && count($tmp_cats_filter)){
				$where .= "\n AND ( cat_id IN (".$catids.")";
				if($JLMS_CONFIG->get('sec_cat_use', 0)){
					foreach ($tmp_cats_filter as $tmp_cats_filter_one) {
						$where .= "\n OR sec_cat LIKE '%|".$tmp_cats_filter_one."|%'";
					}
				}
				$where .= "\n )";
			}
			//NEW MUSLTICATS
		}
		if (!$courses_str) {
			$courses_str = '0';
		}
		$query = "SELECT id FROM #__lms_courses WHERE id IN (".$courses_str.")"
//		.($filt_cat?(" AND (cat_id=$filt_cat OR sec_cat LIKE '%|$filt_cat|%')"):"")
		.$where
		." ORDER BY course_name, id";
		$JLMS_DB->setQuery($query);
		$courses2 = $JLMS_DB->loadResultArray();
		
		$courses = $courses2;

//---
		if ($JLMS_CONFIG->get('use_global_groups', 1) && $filt_group) {
			$query = "SELECT DISTINCT(a.id)"
			. "\n FROM #__users as a, #__lms_users_in_global_groups as c"
			. "\n WHERE a.id = c.user_id "
			. ($filt_group ? ("\n AND c.group_id = '".$filt_group."'") : '' );
		}
		else{
		$query = "SELECT DISTINCT(a.id)"
			. "\n FROM #__users as a, #__lms_users_in_groups as c"
			. "\n WHERE a.id = c.user_id AND c.course_id IN (".$courses_str.")"
			. ($filt_group ? ("\n AND c.group_id = '".$filt_group."'") : '' );
		}	
		$JLMS_DB->SetQuery( $query );
		$users2 = $JLMS_DB->LoadResultArray();
		require_once(_JOOMLMS_FRONT_HOME . DS . "includes" . DS . "classes" . DS . "lms.pagination.php");
		$pageNav = new JLMSPageNav( count($users2), $limitstart, $limit );
		if ($JLMS_CONFIG->get('use_global_groups', 1) && $filt_group) {
			$query = "SELECT DISTINCT(a.id)"
			. "\n FROM #__users as a, #__lms_users_in_global_groups as c"
			. "\n WHERE a.id = c.user_id "
			. ($filt_group ? ("\n AND c.group_id = '".$filt_group."'") : '' )
			. "\n ORDER BY a.name ".(!$is_full?"LIMIT $pageNav->limitstart, $pageNav->limit":"");
		}
		else{
			$query = "SELECT DISTINCT(a.id)"
			. "\n FROM #__users as a, #__lms_users_in_groups as c"
			. "\n WHERE a.id = c.user_id AND c.course_id IN (".$courses_str.")"
			. ($filt_group ? ("\n AND c.group_id = '".$filt_group."'") : '' )
			. "\n ORDER BY a.name ".(!$is_full?"LIMIT $pageNav->limitstart, $pageNav->limit":"");
		}	
		$JLMS_DB->SetQuery( $query );
		$users = $JLMS_DB->LoadResultArray();
		
		//var_dump($users);
		if(count($users)){
			
			// 21 May 2007
			// prepare tracking images
	
			// 18 August 2007 - changes (DEN) - added check for GD and FreeType support
			if($JLMS_CONFIG->get('show_reports_images',0))
			$generate_images = true;
			else
			$generate_images = false;
			$msg = '';
			if (!function_exists('imageftbbox') || !function_exists('imagecreatetruecolor')) {
				$generate_images = false;
				$sec = false;
				if (!function_exists('imagecreatetruecolor')) {
					$msg = 'This function requires GD 2.0.1 or later (2.0.28 or later is recommended).';
					$sec = true;
				}
				if (!function_exists('imageftbbox')) {
					$msg .= ($sec?'<br />':'').'This function is only available if PHP is compiled with freetype support.';
				}
			} // end of GD and FreeType support check
			$users_str = implode(',',$users);
			$new_image_ym_stats = new stdClass();
			if ($JLMS_CONFIG->get('temp_folder', '') && $generate_images){ // temp folder setup is ready.
				$img_gl_width = $JLMS_CONFIG->get('visual_set_tracking_image_base_width', 400); //parameter added 09.06.2007
				$img_gl_height = $JLMS_CONFIG->get('visual_set_tracking_image_base_height', 260); //parameter added 09.06.2007
				require_once(_JOOMLMS_FRONT_HOME . "/includes/libchart/libchart.php");
				JLMS_cleanLibChartCache();
	
				// Year/Month statistic
				
				$chart =  new PieChart($img_gl_width*2, $img_gl_height+40, false, 'no_title');//(700, 70 + count($img_names)*30);
				$query = "SELECT COUNT(*) as hits,c.course_name FROM #__lms_track_hits as h LEFT JOIN #__users as u ON h.user_id=u.id LEFT JOIN #__lms_courses as c ON h.course_id = c.id WHERE h.course_id IN (".$courses_str.") AND h.user_id IN  (".implode(',',$users2).") ".($s_date_db?"AND h.track_time > '".$s_date_db."'":"")." ".($e_date_db?"AND h.track_time < '".$e_date_db."'":"")."  group by h.course_id ORDER BY hits desc LIMIT 10";
				$JLMS_DB->SetQuery( $query );
				$img_names = $JLMS_DB->LoadObjectLIST();
				//var_dump($img_names);
				for ($i = 0, $n = count($img_names); $i < $n; $i ++) {
					$chart->addPoint(new Point($img_names[$i]->course_name, $img_names[$i]->hits));
				}
				$title = "Top 10 courses";
				//$title = JLMS_TR_temp_fix($title);
	
				$mas[0]=$title;
				
				$title = '';
				
				$chart->setTitle($title);
				$filename = time() . '_' . md5(uniqid(rand(), true)) . ".png";
				
				$new_image_ym_stats->filename = $filename; $new_image_ym_stats->width = $img_gl_width; $new_image_ym_stats->height = $img_gl_height; $new_image_ym_stats->alt = $title;
				$new_image_ym_stats->title = $mas[0];
				$chart->render($JLMS_CONFIG->get('absolute_path') . "/".$JLMS_CONFIG->get('temp_folder', '')."/$filename");
			}
			
			$query = "SELECT COUNT(*) as hits,h.course_id as c_id FROM #__lms_track_hits as h LEFT JOIN #__users as u ON h.user_id=u.id LEFT JOIN #__lms_courses as c ON h.course_id = c.id WHERE h.course_id IN (".$courses_str.") AND h.user_id IN  (".implode(',',$users2).") ".($s_date_db?"AND h.track_time > '".$s_date_db."'":"")." ".($e_date_db?"AND h.track_time < '".$e_date_db."'":"")."   group by h.course_id ";
			$JLMS_DB->SetQuery( $query );
			$tot_hits = $JLMS_DB->LoadObjectLIST();
			
			$query = "SELECT COUNT(*) as hits,h.user_id as usr_id,h.course_id as c_id FROM #__lms_track_hits as h LEFT JOIN #__users as u ON h.user_id=u.id LEFT JOIN #__lms_courses as c ON h.course_id = c.id WHERE h.course_id IN (".$courses_str.") AND h.user_id IN  (".$users_str.") ".($s_date_db?"AND h.track_time > '".$s_date_db."'":"")." ".($e_date_db?"AND h.track_time < '".$e_date_db."'":"")."   group by h.course_id,h.user_id ORDER BY h.course_id,h.user_id";
			$JLMS_DB->SetQuery( $query );
			$hits = $JLMS_DB->LoadObjectLIST();
			//var_dump($hits);
			
			if($view == 'csv') {
				JLMS_REP_exportCsv($hits, $tot_hits, $users, $courses, $pageNav, $lists, $levels, $filt_cat, $filt_group, $option, 1);
			} else
			if($view == 'xls') {
				JLMS_REP_exportXLS($hits, $tot_hits,  $users, $courses, $reporting_header);
			} else {
				JLMS_reports_html::JLMS_sreportAccess($tot_hits, $new_image_ym_stats, $hits, $users, $courses, $pageNav, $start_date, $end_date, $lists, $levels, $filt_cat, $filt_group, $option, $is_full);
			}
		}	
	}
	
}

function JLMS_sreportCertif( $option, $is_full ){
	global $JLMS_DB, $JLMS_CONFIG, $JLMS_SESSION, $my, $Itemid;

	$view = mosGetParam($_REQUEST,'view', '');

	if($view == 'csv' || $view == 'xls') {
		$is_full = 1;
	}
	
	$limit = intval( mosGetParam( $_GET, 'limit', $JLMS_SESSION->get('list_limit', $JLMS_CONFIG->get('list_limit')) ) );
	$JLMS_SESSION->set('list_limit', $limit);
	$limitstart = intval( mosGetParam( $_GET, 'limitstart', 0 ) );
	
	$filt_group = intval( mosGetParam( $_REQUEST, 'filt_group', 0 ) );
	$filt_cat = intval( mosGetParam( $_REQUEST, 'filt_cat', 0 ) );
	
	$lists = array();
	
	$reporting_header = array();
	
	//FLMS multicat
	$JLMS_ACL = & JLMSFactory::getACL();
	$levels = array();
	if ($JLMS_CONFIG->get('multicat_use', 0)){
		$query = "SELECT * FROM #__lms_course_cats_config ORDER BY id";
		$JLMS_DB->setQuery($query);
		$levels = $JLMS_DB->loadObjectList();
		if(count($levels) == 0){
			for($i=0;$i<5;$i++){
				if($i>0){
					$levels[$i]->cat_name = _JLMS_COURSES_COURSES_GROUPS;	
				} else {
					$levels[$i]->cat_name = _JLMS_COURSES_COURSES_GROUPS;
				}
			}
		}

		$level_id = array();
		for($i=0;$i<count($levels);$i++){
			if($i == 0){
				$level_id[$i] = intval( mosGetParam( $_REQUEST, 'filter_id_'.$i.'', $JLMS_SESSION->get('FLMS_filter_id_'.$i.'', 0) ) );
				$_REQUEST['filter_id_'.$i] = $level_id[$i];
				$JLMS_SESSION->set('FLMS_filter_id_'.$i.'', $level_id[$i]);
			} else {
				$level_id[$i] = intval( mosGetParam( $_REQUEST, 'filter_id_'.$i.'', $JLMS_SESSION->get('FLMS_filter_id_'.$i.'', 0) ) );
				$_REQUEST['filter_id_'.$i] = $level_id[$i];
				$JLMS_SESSION->set('FLMS_filter_id_'.$i.'', $level_id[$i]);
			}
			if($i == 0){
				$parent_id[$i] = 0;
			} else {
				$parent_id[$i] = $level_id[$i-1];
			}
			if($i == 0 || $parent_id[$i]){ //(Max): extra requests
				$query = "SELECT count(id) FROM `#__lms_course_cats` WHERE parent = '".$parent_id[$i]."' ORDER BY c_category";
				$JLMS_DB->setQuery($query);
				$groups = $JLMS_DB->loadResult();
				if($groups==0){
					$level_id[$i] = 0;	
					$JLMS_SESSION->set('FLMS_filter_id_'.$i.'', $level_id[$i]);
				}
			}
		}
		
		for($i=0;$i<count($levels);$i++){
			if($i > 0 && $level_id[$i - 1] == 0){
				$level_id[$i] = 0;
				$_REQUEST['filter_id_'.$i] = $level_id[$i];
				$JLMS_SESSION->set('FLMS_filter_id_'.$i.'', $level_id[$i]);
				$parent_id[$i] = 0;
			} elseif($i == 0 && $level_id[$i] == 0) {
				$level_id[$i] = 0;
				$_REQUEST['filter_id_'.$i] = $level_id[$i];
				$JLMS_SESSION->set('FLMS_filter_id_'.$i.'', $level_id[$i]);
				$parent_id[$i] = 0;
			}
		}
		
		$javascript = 'onclick="javascript:read_filter();" onchange="javascript:write_filter();submitFormView(\'\');"';
		
		$query1 = "SELECT group_id FROM `#__lms_users_in_global_groups` WHERE user_id = '".$my->id."'";
		$JLMS_DB->setQuery($query1);
		$user_group_ids = $JLMS_DB->loadResultArray();
		
		$categories_reporting = array();
		$name_categories_reporting = array();
		
		for($i=0;$i<count($levels);$i++) {
			if($i == 0 || $parent_id[$i]){ //(Max): extra requests
				if( $parent_id[$i] == 0 && (!$JLMS_ACL->CheckPermissions('lms', 'create_course'))) {
					$query = "SELECT * FROM `#__lms_course_cats` WHERE `parent` = '0'";
	
					$query .= "\n AND (";
					if (count($user_group_ids)) {
						$query .= "( `restricted` = 1 AND ( `groups` LIKE '%|$user_group_ids[0]|%'";
						for($i1=1;$i1<count($user_group_ids);$i1++) {
							$query .= "\n OR `groups` like '%|$user_group_ids[$i1]|%'";
						}
						$query .=  "\n ) ) \n OR ";
					}
					$query .= "(`restricted` = 0 )) ";
					$query .= "\n ORDER BY `c_category`";
				}
				else {
					$query = "SELECT * FROM `#__lms_course_cats` WHERE parent = '".$parent_id[$i]."' ORDER BY c_category";
				}
				$JLMS_DB->setQuery($query);
				$groups = $JLMS_DB->loadObjectList();
				
				if($parent_id[$i] && $i > 0 && count($groups)) {
					$type_level[$i][] = mosHTML::makeOption( 0, ' &nbsp; ' );
					foreach ($groups as $group){
						$type_level[$i][] = mosHTML::makeOption( $group->id, $group->c_category );
						if($group->id == $level_id[$i]){
							$name_categories_reporting[] = $levels[$i]->cat_name;
							$categories_reporting[] = $group->c_category;
						}
					}
					$lists['filter_'.$i.''] = mosHTML::selectList($type_level[$i], 'filter_id_'.$i.'', 'class="inputbox" size="1" style="width: 100%;" '.$javascript, 'value', 'text', $level_id[$i] ); //onchange="document.location.href=\''. $link_multi .'\';"
					
				} elseif($i == 0) {
					$type_level[$i][] = mosHTML::makeOption( 0, ' &nbsp; ' );
					foreach ($groups as $group){
						$type_level[$i][] = mosHTML::makeOption( $group->id, $group->c_category );
						if($group->id == $level_id[$i]){
							$name_categories_reporting[] = $levels[$i]->cat_name;
							$categories_reporting[] = $group->c_category;
						}
					}
					$lists['filter_'.$i.''] = mosHTML::selectList($type_level[$i], 'filter_id_'.$i.'', 'class="inputbox" size="1" style="width: 100%;" '.$javascript, 'value', 'text', $level_id[$i] ); //onchange="document.location.href=\''. $link_multi .'\';"
				}
			}
		}
		$reporting_header['name_categories'] = $name_categories_reporting;
		$reporting_header['categories'] = $categories_reporting;	
	}
	//FLMS multicat
		
	$teacher_in_courses = $JLMS_CONFIG->get('teacher_in_courses', array() );
	$parent_in_courses = $JLMS_CONFIG->get('parent_in_courses', array() );
	
	$courses = array_merge($teacher_in_courses, $parent_in_courses);

	//var_dump($courses);
	if(count($courses)){
		$courses_str = implode(',',$courses);
		$JLMS_DB->setQuery('SELECT id FROM #__lms_courses WHERE id IN('.$courses_str.')');
		$courses = $JLMS_DB->loadResultArray();
		
		$g_items = array();
		$g_items[] = mosHTML::makeOption(0, _JLMS_ATT_FILTER_ALL_GROUPS);
		if ($JLMS_CONFIG->get('use_global_groups', 1)) {
			if (!count($courses)) {
				$courses = array(0);
			}
			$query = "SELECT user_id FROM #__lms_users_in_groups WHERE course_id IN (".implode(',',$courses).")";
			$JLMS_DB->setQuery($query);
			$cid = $JLMS_DB->loadResultArray();
			if (!$cid) $cid = array(-1);
			$query = "SELECT group_id FROM #__lms_users_in_global_groups WHERE user_id IN (".implode(',', $cid).")";
			$JLMS_DB->setQuery($query);
			$gid = $JLMS_DB->loadResultArray();
			if (!$gid) $gid = array(-1);
			$query = "SELECT distinct id AS value, ug_name AS text FROM #__lms_usergroups WHERE id IN (".implode(',', $gid).") AND course_id = 0 ORDER BY text";//course id check just in case))
			$JLMS_DB->setQuery($query);
			$groups = $JLMS_DB->loadObjectList();
		} else {
			if (!count($courses)) {
				$courses = array(0);
			}
			$query = "SELECT distinct a.id as value, a.ug_name as text FROM #__lms_usergroups as a, #__lms_users_in_groups as b"
			. "\n WHERE a.course_id IN (".implode(',',$courses).") AND b.group_id = a.id ORDER BY a.ug_name";
			$JLMS_DB->SetQuery( $query );
			$groups = $JLMS_DB->LoadObjectList();
		}
		$g_items = array_merge($g_items, $groups);
		$link = "javascript:submitFormView('');";
		//$link = $link ."&amp;filt_group=' + this.options[selectedIndex].value + '";
		$lists['filter'] = mosHTML::selectList($g_items, 'filt_group', 'class="inputbox" size="1" style="width: 100%;" onchange="'. $link .'"', 'value', 'text', $filt_group );
		
		$groups_reporting = array();
		foreach($groups as $grp){
			if($filt_group && $grp->value == $filt_group){
				$groups_reporting[] = $grp->text;
			}
		}
		$name_groups_reporting[] = 'Usergroup';
		$reporting_header['name_groups'] = $name_groups_reporting;	
		$reporting_header['groups'] = $groups_reporting;	
		
//---	
		$where = '';
		if ($JLMS_CONFIG->get('multicat_use', 0)){
			//NEW MUSLTICATS
//			$tmp_level = array();
			$last_catid = 0;
			
			$tmp_cats_filter = JLMS_getFilterMulticategories($last_catid);
			/*
			$i=0;
			foreach($_REQUEST as $key=>$item){
				if(preg_match('#filter_id_(\d+)#', $key, $result)){
					if($item){
						$tmp_level[$i] = $result;
						$last_catid = $item;
						$i++;
					}	
				}	
			}
			$query = "SELECT * FROM #__lms_course_cats ORDER BY id";
			$JLMS_DB->setQuery($query);
			$all_cats = $JLMS_DB->loadObjectList();
			
			$tmp_cats_filter = array();
			$children = array();
			foreach($all_cats as $cat){
				$pt = $cat->parent;
				$list = @$children[$pt] ? $children[$pt] : array();
				array_push($list, $cat->id);
				$children[$pt] = $list;
			}
			$tmp_cats_filter[0] = $last_catid;
			$i=1;
			foreach($children as $key=>$childs){
				if($last_catid == $key){
					foreach($children[$key] as $v){
						if(!in_array($v, $tmp_cats_filter)){
							$tmp_cats_filter[$i] = $v;
							$i++;
						}
					}
				}
			}
			foreach($children as $key=>$childs){
				if(in_array($key, $tmp_cats_filter)){
					foreach($children[$key] as $v){
						if(!in_array($v, $tmp_cats_filter)){
							$tmp_cats_filter[$i] = $v;
							$i++;
						}
					}
				}
			}
			$tmp_cats_filter = array_unique($tmp_cats_filter);
			*/
			$catids = implode(",", $tmp_cats_filter);
			
			if($last_catid && count($tmp_cats_filter)){
				$where .= "\n AND ( cat_id IN (".$catids.")";
				if($JLMS_CONFIG->get('sec_cat_use', 0)){
					foreach ($tmp_cats_filter as $tmp_cats_filter_one) {
						$where .= "\n OR sec_cat LIKE '%|".$tmp_cats_filter_one."|%'";
					}
				}
				$where .= "\n )";
			}
			//NEW MUSLTICATS
		}
		if (!$courses_str) {
			$courses_str = '0';
		}
		$query = "SELECT id FROM #__lms_courses WHERE id IN (".$courses_str.")"
//		.($filt_cat?" AND (cat_id=$filt_cat OR sec_cat LIKE '%|$filt_cat|%')":"")
		.$where
		." ORDER BY course_name"		
		;
		$JLMS_DB->setQuery($query);
		$courses2 = $JLMS_DB->loadResultArray();
		/*
		Old filter
		$query = "SELECT distinct a.id as value, a.c_category as text FROM #__lms_course_cats as a, #__lms_courses as c WHERE c.cat_id=a.id AND c.id IN (".implode(',',$courses).")  order by a.c_category";
		$JLMS_DB->setQuery( $query );
		$sf_cats = array();
		$sf_cats[] = mosHTML::makeOption( '0', '- Select Category -' );
		$sf_cats = array_merge( $sf_cats, $JLMS_DB->loadObjectList() );
		$lists['jlms_course_cats'] = mosHTML::selectList( $sf_cats, 'filt_cat', 'class="text_area" size="1" onchange="document.adminForm.submit();"', 'value', 'text', $filt_cat );
		*/
		
		$courses = $courses2;
//---
		
		if ($JLMS_CONFIG->get('use_global_groups', 1) && $filt_group) {
			$query = "SELECT DISTINCT(a.id)"
			. "\n FROM #__users as a, #__lms_users_in_global_groups as c"
			. "\n WHERE a.id = c.user_id "
			. ($filt_group ? ("\n AND c.group_id = '".$filt_group."'") : '' );
		}
		else{
		$query = "SELECT DISTINCT(a.id)"
			. "\n FROM #__users as a, #__lms_users_in_groups as c"
			. "\n WHERE a.id = c.user_id AND c.course_id IN (".$courses_str.")"
			. ($filt_group ? ("\n AND c.group_id = '".$filt_group."'") : '' );
		}	
		$JLMS_DB->SetQuery( $query );
		$users2 = $JLMS_DB->LoadResultArray();
		require_once(_JOOMLMS_FRONT_HOME . DS . "includes" . DS . "classes" . DS . "lms.pagination.php");
		$pageNav = new JLMSPageNav( count($users2), $limitstart, $limit );
		if ($JLMS_CONFIG->get('use_global_groups', 1) && $filt_group) {
			$query = "SELECT DISTINCT(a.id)"
			. "\n FROM #__users as a, #__lms_users_in_global_groups as c"
			. "\n WHERE a.id = c.user_id "
			. ($filt_group ? ("\n AND c.group_id = '".$filt_group."'") : '' )
			. "\n ORDER BY a.name ".(!$is_full?"LIMIT $pageNav->limitstart, $pageNav->limit":"");
		}
		else{
			$query = "SELECT DISTINCT(a.id)"
			. "\n FROM #__users as a, #__lms_users_in_groups as c"
			. "\n WHERE a.id = c.user_id AND c.course_id IN (".$courses_str.")"
			. ($filt_group ? ("\n AND c.group_id = '".$filt_group."'") : '' )
			. "\n ORDER BY a.name ".(!$is_full?"LIMIT $pageNav->limitstart, $pageNav->limit":"");
		}	
		$JLMS_DB->SetQuery( $query );
		$users = $JLMS_DB->LoadResultArray();
		
		//var_dump($users);
		if(count($users)){
			$users_str = implode(',',$users);
			$query = "SELECT h.user_id as usr_id,h.course_id as c_id FROM #__lms_certificate_users as h LEFT JOIN #__users as u ON h.user_id=u.id LEFT JOIN #__lms_courses as c ON h.course_id = c.id WHERE h.course_id IN (".$courses_str.") AND h.user_id IN  (".$users_str.")  ORDER BY h.course_id,h.user_id";
			$JLMS_DB->SetQuery( $query );
			$hits = $JLMS_DB->LoadObjectLIST();
			//var_dump($hits);
			
			if($view == 'csv') {
				JLMS_REP_exportCsv($hits, array(), $users, $courses, $pageNav, $lists, $levels, $filt_cat, $filt_group, $option, 1);
			} else
			if($view == 'xls') {
				JLMS_REP_exportXLS($hits, array(), $users, $courses, $reporting_header);
			} else {
				JLMS_reports_html::JLMS_sreportCertif($hits, $users, $courses, $pageNav, $lists, $levels, $filt_cat, $filt_group, $option, $is_full);
			}	
		}	
	}
}

function JLMS_sreportGrade( $option, $is_full ){
	global $JLMS_DB, $JLMS_CONFIG, $JLMS_SESSION, $my, $Itemid;
	
	$view = mosGetParam($_REQUEST, 'view', '');

	if($view == 'csv' || $view == 'xls') {
		$is_full = 0;
	}
	
	$limit = intval( mosGetParam( $_GET, 'limit', $JLMS_SESSION->get('list_limit', $JLMS_CONFIG->get('list_limit')) ) );
	$JLMS_SESSION->set('list_limit', $limit);
	$limitstart = intval( mosGetParam( $_GET, 'limitstart', 0 ) );
	
	$filt_group = intval( mosGetParam( $_REQUEST, 'filt_group', 0 ) );
	$filt_cat = intval( mosGetParam( $_REQUEST, 'filt_cat', 0 ) );
	$user_id = intval( mosGetParam( $_REQUEST, 'filt_user', 0 ) );
	$lists = array();
	
	$reporting_header = array();
	
	//FLMS multicat
	$JLMS_ACL = & JLMSFactory::getACL();
	$levels = array();
	if ($JLMS_CONFIG->get('multicat_use', 0)){
		$query = "SELECT * FROM #__lms_course_cats_config ORDER BY id";
		$JLMS_DB->setQuery($query);
		$levels = $JLMS_DB->loadObjectList();
		if(count($levels) == 0){
			for($i=0;$i<5;$i++){
				if($i>0){
					$levels[$i]->cat_name = _JLMS_COURSES_COURSES_GROUPS;	
				} else {
					$levels[$i]->cat_name = _JLMS_COURSES_COURSES_GROUPS;
				}
			}
		}

		$level_id = array();
		for($i=0;$i<count($levels);$i++){
			if($i == 0){
				$level_id[$i] = intval( mosGetParam( $_REQUEST, 'filter_id_'.$i.'', $JLMS_SESSION->get('FLMS_filter_id_'.$i.'', 0) ) );
				$_REQUEST['filter_id_'.$i] = $level_id[$i];
				$JLMS_SESSION->set('FLMS_filter_id_'.$i.'', $level_id[$i]);
			} else {
				$level_id[$i] = intval( mosGetParam( $_REQUEST, 'filter_id_'.$i.'', $JLMS_SESSION->get('FLMS_filter_id_'.$i.'', 0) ) );
				$_REQUEST['filter_id_'.$i] = $level_id[$i];
				$JLMS_SESSION->set('FLMS_filter_id_'.$i.'', $level_id[$i]);
			}
			if($i == 0){
				$parent_id[$i] = 0;
			} else {
				$parent_id[$i] = $level_id[$i-1];
			}
			if($i == 0 || $parent_id[$i]){ //(Max): extra requests
				$query = "SELECT count(id) FROM `#__lms_course_cats` WHERE parent = '".$parent_id[$i]."' ORDER BY c_category";
				$JLMS_DB->setQuery($query);
				$groups = $JLMS_DB->loadResult();
				if($groups==0){
					$level_id[$i] = 0;	
					$JLMS_SESSION->set('FLMS_filter_id_'.$i.'', $level_id[$i]);
				}
			}
		}
		
		for($i=0;$i<count($levels);$i++){
			if($i > 0 && $level_id[$i - 1] == 0){
				$level_id[$i] = 0;
				$_REQUEST['filter_id_'.$i] = $level_id[$i];
				$JLMS_SESSION->set('FLMS_filter_id_'.$i.'', $level_id[$i]);
				$parent_id[$i] = 0;
			} elseif($i == 0 && $level_id[$i] == 0) {
				$level_id[$i] = 0;
				$_REQUEST['filter_id_'.$i] = $level_id[$i];
				$JLMS_SESSION->set('FLMS_filter_id_'.$i.'', $level_id[$i]);
				$parent_id[$i] = 0;
			}
		}
		
		$javascript = 'onclick="javascript:read_filter();" onchange="javascript:write_filter();submitFormView();"';
		
		$query1 = "SELECT group_id FROM `#__lms_users_in_global_groups` WHERE user_id = '".$my->id."'";
		$JLMS_DB->setQuery($query1);
		$user_group_ids = $JLMS_DB->loadResultArray();
		
		$categories_reporting = array();
		$name_categories_reporting = array();
		
		for($i=0;$i<count($levels);$i++) {
			if($i == 0 || $parent_id[$i]){ //(Max): extra requests
				if( $parent_id[$i] == 0 && (!$JLMS_ACL->CheckPermissions('lms', 'create_course'))) {
					$query = "SELECT * FROM `#__lms_course_cats` WHERE `parent` = '0'";
	
					$query .= "\n AND (";
					if (count($user_group_ids)) {
						$query .= "( `restricted` = 1 AND ( `groups` LIKE '%|$user_group_ids[0]|%'";
						for($i1=1;$i1<count($user_group_ids);$i1++) {
							$query .= "\n OR `groups` like '%|$user_group_ids[$i1]|%'";
						}
						$query .=  "\n ) ) \n OR ";
					}
					$query .= "(`restricted` = 0 )) ";
					$query .= "\n ORDER BY `c_category`";
				}
				else {
					$query = "SELECT * FROM `#__lms_course_cats` WHERE parent = '".$parent_id[$i]."' ORDER BY c_category";
				}
				
				$JLMS_DB->setQuery($query);
				$groups = $JLMS_DB->loadObjectList();
				
				if($parent_id[$i] && $i > 0 && count($groups)) {
					$type_level[$i][] = mosHTML::makeOption( 0, ' &nbsp; ' );
					foreach ($groups as $group){
						$type_level[$i][] = mosHTML::makeOption( $group->id, $group->c_category );
						if($group->id == $level_id[$i]){
							$name_categories_reporting[] = $levels[$i]->cat_name;
							$categories_reporting[] = $group->c_category;
						}
					}
					$lists['filter_'.$i.''] = mosHTML::selectList($type_level[$i], 'filter_id_'.$i.'', 'class="inputbox" size="1" style="width: 100%;" '.$javascript, 'value', 'text', $level_id[$i] ); //onchange="document.location.href=\''. $link_multi .'\';"
				} elseif($i == 0) {
					$type_level[$i][] = mosHTML::makeOption( 0, ' &nbsp; ' );
					foreach ($groups as $group){
						$type_level[$i][] = mosHTML::makeOption( $group->id, $group->c_category );
						if($group->id == $level_id[$i]){
							$name_categories_reporting[] = $levels[$i]->cat_name;
							$categories_reporting[] = $group->c_category;
						}
					}
					$lists['filter_'.$i.''] = mosHTML::selectList($type_level[$i], 'filter_id_'.$i.'', 'class="inputbox" size="1" style="width: 100%;" '.$javascript, 'value', 'text', $level_id[$i] ); //onchange="document.location.href=\''. $link_multi .'\';"
				}
			}
		}
		$reporting_header['name_categories'] = $name_categories_reporting;
		$reporting_header['categories'] = $categories_reporting;	
	}
	//FLMS multicat
	
	$teacher_in_courses = $JLMS_CONFIG->get('teacher_in_courses', array() );
	$parent_in_courses = $JLMS_CONFIG->get('parent_in_courses', array() );
	
	$courses = array_merge($teacher_in_courses, $parent_in_courses);
	
	//var_dump($courses);
	if(count($courses)){
		$courses_str = implode(',',$courses);
		$JLMS_DB->setQuery('SELECT id FROM #__lms_courses WHERE id IN('.$courses_str.')');
		$courses = $JLMS_DB->loadResultArray();
		
		$g_items = array();
		$g_items[] = mosHTML::makeOption(0, _JLMS_ATT_FILTER_ALL_GROUPS);
		if ($JLMS_CONFIG->get('use_global_groups', 1)) {
			if (!count($courses)) {
				$courses = array(0);
			}
			$query = "SELECT user_id FROM #__lms_users_in_groups WHERE course_id IN (".implode(',',$courses).")";
			$JLMS_DB->setQuery($query);
			$cid = $JLMS_DB->loadResultArray();
			if (!$cid) $cid = array(-1);
			$query = "SELECT group_id FROM #__lms_users_in_global_groups WHERE user_id IN (".implode(',', $cid).")";
			$JLMS_DB->setQuery($query);
			$gid = $JLMS_DB->loadResultArray();
			if (!$gid) $gid = array(-1);
			$query = "SELECT distinct id AS value, ug_name AS text FROM #__lms_usergroups WHERE id IN (".implode(',', $gid).") AND course_id = 0 ORDER BY text";//course id check just in case))
			$JLMS_DB->setQuery($query);
			$groups = $JLMS_DB->loadObjectList();
		} else {
			if (!count($courses)) {
				$courses = array(0);
			}
			$query = "SELECT distinct a.id as value, a.ug_name as text FROM #__lms_usergroups as a, #__lms_users_in_groups as b"
			. "\n WHERE a.course_id IN (".implode(',',$courses).") AND b.group_id = a.id ORDER BY a.ug_name";
			$JLMS_DB->SetQuery( $query );
			$groups = $JLMS_DB->LoadObjectList();
		}
		$g_items = array_merge($g_items, $groups);
		$link = "javascript:submitFormView();";
		//$link = $link ."&amp;filt_group=' + this.options[selectedIndex].value + '";
		$lists['filter'] = mosHTML::selectList($g_items, 'filt_group', 'class="inputbox" size="1" style="width: 100%;" onchange="'. $link .'"', 'value', 'text', $filt_group );
		
		$name_groups_reporting = array();
		$groups_reporting = array();
		foreach($groups as $grp){
			if($filt_group && $grp->value == $filt_group){
				$name_groups_reporting[] = _JLMS_REPORTING_USERGROUP;
				$groups_reporting[] = $grp->text;
			}
		}
		$reporting_header['name_groups'] = $name_groups_reporting;	
		$reporting_header['groups'] = $groups_reporting;
		
		//---
		if ($JLMS_CONFIG->get('use_global_groups', 1) && $filt_group) {
			$query = "SELECT DISTINCT a.id as value, a.name as text"
			. "\n FROM #__users as a, #__lms_users_in_global_groups as c"
			. "\n WHERE a.id = c.user_id "
			. ($filt_group ? ("\n AND c.group_id = '".$filt_group."'") : '' )
			. "\n ORDER BY a.name";
		}else{
			if (!count($courses)) {
				$courses = array(0);
			}
			if($view == 'xls'){
				$query = "SELECT DISTINCT a.id as value, a.name as text"
				. "\n FROM #__users as a, #__lms_users_in_groups as c"
				. "\n WHERE a.id = c.user_id AND c.course_id IN (".implode(',',$courses).")"
				. ($filt_group ? ("\n AND c.group_id = '".$filt_group."'") : '' )
				. "\n ORDER BY a.name";
			} else {
				$query = "SELECT DISTINCT a.id as value, CONCAT(a.name,' (',a.username,')') as text"
				. "\n FROM #__users as a, #__lms_users_in_groups as c"
				. "\n WHERE a.id = c.user_id AND c.course_id IN (".implode(',',$courses).")"
				. ($filt_group ? ("\n AND c.group_id = '".$filt_group."'") : '' )
				. "\n ORDER BY a.name";
			}
		}
		
		$JLMS_DB->SetQuery( $query );
		$usr_cats[] = mosHTML::makeOption( '0', _JLMS_SB_SELECT_USER );
		$usr_cats = array_merge( $usr_cats, $JLMS_DB->loadObjectList() );
		$lists['jlms_filt_user'] = mosHTML::selectList( $usr_cats, 'filt_user', 'class="text_area" size="1" style="width: 100%;" onchange="javascript:submitFormView();"', 'value', 'text', $user_id );
		
		$name_users_reporting = array();
		$users_reporting = array();
		foreach($usr_cats as $usr){
			if($user_id && $usr->value == $user_id){
				$name_users_reporting[] = _JLMS_USERS_NAME;
				$users_reporting[] = $usr->text;
			}
		}
		$reporting_header['name_users'] = $name_users_reporting;	
		$reporting_header['users'] = $users_reporting;

//---
//---
//$user_id = 64;
		$where = '';
		if ($JLMS_CONFIG->get('multicat_use', 0)){
			
			//NEW MUSLTICATS
//			$tmp_level = array();
			$last_catid = 0;
			
			$tmp_cats_filter = JLMS_getFilterMulticategories($last_catid);
			/*
			$i=0;
			foreach($_REQUEST as $key=>$item){
				if(preg_match('#filter_id_(\d+)#', $key, $result)){
					if($item){
						$tmp_level[$i] = $result;
						$last_catid = $item;
						$i++;
					}	
				}	
			}
			$query = "SELECT * FROM #__lms_course_cats ORDER BY id";
			$JLMS_DB->setQuery($query);
			$all_cats = $JLMS_DB->loadObjectList();
			
			$tmp_cats_filter = array();
			$children = array();
			foreach($all_cats as $cat){
				$pt = $cat->parent;
				$list = @$children[$pt] ? $children[$pt] : array();
				array_push($list, $cat->id);
				$children[$pt] = $list;
			}
			$tmp_cats_filter[0] = $last_catid;
			$i=1;
			foreach($children as $key=>$childs){
				if($last_catid == $key){
					foreach($children[$key] as $v){
						if(!in_array($v, $tmp_cats_filter)){
							$tmp_cats_filter[$i] = $v;
							$i++;
						}
					}
				}
			}
			foreach($children as $key=>$childs){
				if(in_array($key, $tmp_cats_filter)){
					foreach($children[$key] as $v){
						if(!in_array($v, $tmp_cats_filter)){
							$tmp_cats_filter[$i] = $v;
							$i++;
						}
					}
				}
			}
			$tmp_cats_filter = array_unique($tmp_cats_filter);
			*/
			$catids = implode(",", $tmp_cats_filter);
			
			if($last_catid && count($tmp_cats_filter)){
				$where .= "\n AND ( cat_id IN (".$catids.")";
				if($JLMS_CONFIG->get('sec_cat_use', 0)){
					foreach ($tmp_cats_filter as $tmp_cats_filter_one) {
						$where .= "\n OR sec_cat LIKE '%|".$tmp_cats_filter_one."|%'";
					}
				}
				$where .= "\n )";
			}
			//NEW MUSLTICATS
			
			
			/*
			if($level_id[0]){
				$where .= " AND cat_id = '".$level_id[0]."' ";
			}
			$other_cat_ids = array();
			for($i=1;$i<count($level_id);$i++){
				if($level_id[$i]){
					$other_cat_ids[] = $level_id[$i];	
				}
			}
			$other_cat_id = '';
			if(count($other_cat_ids) > 0){
				$other_cat_id = implode(",", $other_cat_ids);	
			
				$query = "SELECT course_id, cat_id, level FROM #__lms_course_level WHERE cat_id IN (".$other_cat_id.")";
				$JLMS_DB->setQuery($query);
				$c_list = $JLMS_DB->loadObjectList();
				
				$c_result = array();
				foreach($c_list as $data){
					if($data->level == count($other_cat_ids)){
						$c_result[] = $data->course_id;
					}	
				}
				
				$course_subs = implode(",", $c_result);
				$where .= " AND id IN (".$course_subs.") ";
			}
			*/
		}	
		$query = "SELECT id FROM #__lms_courses WHERE id IN(".$courses_str.")"
//		.($filt_cat?" AND (cat_id=$filt_cat OR sec_cat LIKE '%|$filt_cat|%')":"")
		.$where
		." ORDER BY id"
		;
		$JLMS_DB->setQuery($query);
		$courses2 = $JLMS_DB->loadResultArray();
		
		//Test Course Only (FLMS) (Max - 18.04.2011)
		if($JLMS_CONFIG->get('flms_integration')){
			$test_lesson = JRequest::getVar('test_lesson', 0);
			$checked = $test_lesson ? 'checked="checked"' : '';
			$lists['test_lesson'] = '<input type="checkbox" name="test_lesson" value="1" class="inputbox" '.$checked.' onclick="javascript:submitFormView();" />';
			$lists['test_lesson_value'] = $test_lesson;
		
			if($test_lesson){
				$query = "SELECT b.course_id"
				. "\n FROM"
				. "\n #__lms_courses as a"
				. "\n, #__lmsf_courses as b"
				. "\n WHERE 1"
				. "\n AND a.id = b.course_id"
				. "\n AND b.test_lesson = 1"
				;
				$JLMS_DB->setQuery($query);
				$test_course_ids = $JLMS_DB->loadResultArray();
				
				$tmp = array();
				foreach($courses2 as $c){
					foreach($test_course_ids as $t){
						if($c == $t){
							$tmp[] = $c;
						}
					}
				}
				if(count($tmp)){
					$courses2 = array();			
					$courses2 = $tmp;
				}
			}
		}
		//Test Course Only (FLMS) (Max - 18.04.2011)
		
		/*
		OLd Filter
		$query = "SELECT distinct a.id as value, a.c_category as text FROM #__lms_course_cats as a, #__lms_courses as c WHERE c.cat_id=a.id AND c.id IN (".implode(',',$courses).")  order by a.c_category";
		$JLMS_DB->setQuery( $query );
		$sf_cats = array();
		$sf_cats[] = mosHTML::makeOption( '0', '- Select Category -' );
		$sf_cats = array_merge( $sf_cats, $JLMS_DB->loadObjectList() );
		$lists['jlms_course_cats'] = mosHTML::selectList( $sf_cats, 'filt_cat', 'class="text_area" size="1" onchange="document.adminForm.submit();"', 'value', 'text', $filt_cat );
		*/
		$courses = $courses2;
		//$JLMS_DB->setQuery('SELECT COUNT(*) FROM #__lms_courses as c, #__lms_user_courses as u WHERE c.id=u.course_id AND c.id IN('.$courses_str.') AND u.user_id = '.$user_id.' '.($filt_cat?" AND cat_id=$filt_cat":""));

		require_once(_JOOMLMS_FRONT_HOME . DS . "includes" . DS . "classes" . DS . "lms.pagination.php");
		if (!count($courses)) {
			$courses = array(0);
		}
		$query = "SELECT COUNT(*)"
			. "\n FROM #__lms_users_in_groups as b"
			. "\n LEFT JOIN #__lms_usergroups as c ON b.group_id = c.id AND c.course_id IN (".implode(',',$courses)."),"
			. "\n #__users as u, #__lms_courses as lc"
			. "\n WHERE b.course_id IN (".implode(',',$courses).") AND b.user_id = u.id AND b.course_id = lc.id"
			. "\n AND b.user_id = '".$user_id."'";
		$JLMS_DB->SetQuery( $query );

		$pageNav = new JLMSPageNav( $JLMS_DB->loadResult(), $limitstart, $limit );
		$query = "SELECT distinct b.course_id"
			. "\n FROM #__lms_users_in_groups as b"
			. "\n LEFT JOIN #__lms_usergroups as c ON b.group_id = c.id AND c.course_id IN (".implode(',',$courses)."),"
			. "\n #__users as u, #__lms_courses as lc"
			. "\n WHERE b.course_id IN (".implode(',',$courses).") AND b.user_id = u.id AND b.course_id = lc.id"
			. "\n AND b.user_id = '".$user_id."'"
			. "\n  ORDER BY lc.course_name ".(!$is_full?"LIMIT $pageNav->limitstart, $pageNav->limit":"");
			$JLMS_DB->SetQuery( $query );
			$courses = $JLMS_DB->loadResultArray();
			
		//$JLMS_DB->setQuery('SELECT id FROM #__lms_courses as c, #__lms_user_courses as u WHERE c.id=u.course_id AND c.id IN('.$courses_str.') AND u.user_id = '.$user_id.' '.($filt_cat?" AND cat_id=$filt_cat":"")." ORDER BY id LIMIT $pageNav->limitstart, $pageNav->limit");
		//$courses = $JLMS_DB->loadResultArray();
		
		$hits = array();
		$rowz = array();
		if($user_id){			
			//--start grade
			$rowz = array();
			//$lists = array();
			$lists['user_id'] = $user_id;
			$lists['hits'] = array();
			foreach($courses as $course_id){
				//$course_id = 177;
				$query = "SELECT b.*, u.username, u.name, u.email, c.ug_name, lc.course_name"
				. "\n FROM #__lms_users_in_groups as b"
				. "\n LEFT JOIN #__lms_usergroups as c ON b.group_id = c.id AND c.course_id = '".$course_id."',"
				. "\n #__users as u, #__lms_courses as lc"
				. "\n WHERE b.course_id = '".$course_id."' AND b.user_id = u.id AND b.course_id = lc.id"
				. "\n AND b.user_id = '".$user_id."'";
				$JLMS_DB->SetQuery( $query );
				$rows = $JLMS_DB->LoadObjectList();
				
				$uids = array();
				foreach ($rows as $row) {
					$uids[] = $row->user_id;
				}
				//var_dump($rows);die();
		
				if (count($uids)) {
					JLMS_GB_getUsersGrades($course_id, $uids, $rows, $lists, 1);
					
					if(count($rows))
					$rowz[] = $rows[0];
				}
				
				$query = "SELECT COUNT(*) FROM #__lms_track_hits  WHERE course_id = ".$course_id." AND user_id = ".$user_id;
				$JLMS_DB->SetQuery( $query );
				$lists['hits'][] = $JLMS_DB->LoadResult();
			}
			$hits = $lists['hits'];	
				
			//JLMS_gradebook_html::showUserGradebook( $course_id, $option, $rows, $lists );
			//---end grade	
			
			//var_dump($users);
			/*if(count($users)){
				$users_str = implode(',',$users);
				$query = "SELECT h.user_id as usr_id,h.course_id as c_id FROM #__lms_certificate_users as h LEFT JOIN #__users as u ON h.user_id=u.id LEFT JOIN #__lms_courses as c ON h.course_id = c.id WHERE h.course_id IN (".$courses_str.") AND h.user_id IN  (".$users_str.") AND h.crt_date > '".$s_date_db."' AND h.crt_date < '".$e_date_db."' ORDER BY h.course_id,h.user_id";
				$JLMS_DB->SetQuery( $query );
				$hits = $JLMS_DB->LoadObjectLIST();
				//var_dump($hits);
				
			}*/
		}
	}
	
	if($is_full){
		JLMS_reports_html::JLMS_sreportGradeFV($option, $rowz, $pageNav, $lists, $levels, $filt_group, $filt_cat, $user_id, $is_full);
	} else {
		$users = array();
		$users[] = $user_id;
		
		$reporting_header['data_grade']['rowz'] = $rowz;
		$reporting_header['data_grade']['lists'] = $lists;
		
//		if($view == 'csv') {
//			JLMS_REP_exportCsv($hits, array(), $users, $courses, $pageNav, $lists, $levels, $filt_cat, $filt_group, $option, 1);
//		} else
		if($view == 'xls'){
			JLMS_REP_exportXLS($hits, array(), $users, $courses, $reporting_header);
		} else {
			JLMS_reports_html::JLMS_sreportGrade($option, $rowz, $pageNav, $lists, $levels, $filt_group, $filt_cat, $user_id, $is_full);
		}
	}
	
}

function JLMS_REP_exportXLS($hits, $tot_hits=array(), $users, $courses, $reporting_header){
	global $JLMS_DB, $JLMS_CONFIG, $task, $option;
	
	$results = array();
	
	switch($task){
		case 'report_access':
			
			$users_str = implode(',', $users);
			$courses_str = implode(',', $courses);
			
			$JLMS_DB->setQuery('SELECT course_name FROM #__lms_courses WHERE id IN('.$courses_str.') ORDER BY course_name');
			$crs_name = $JLMS_DB->loadResultArray();
																			
			$JLMS_DB->setQuery('SELECT * FROM #__lms_courses WHERE id IN('.$courses_str.') ORDER BY course_name');
			$crs_options = $JLMS_DB->loadObjectList();
			
			$results['title_courses'] = array();
			
			$i=0;
			foreach($crs_name as $key=>$c_name){
				$view_is_course = 1;
				if($JLMS_CONFIG->get('flms_integration', 1)){
					$params = new JLMSParameters($crs_options[$key]->params);
					$view_is_course = $params->get('show_in_report', 1);	
				}
				if($view_is_course){
					$results['title_courses'][$i]['course_name'] = $c_name; 
					$i++;
				}
			}
			
			$results['results'] = array();
			$results['results_hit'] = array();
			
			$j = 0;
			foreach($users as $usr_id){
				$JLMS_DB->setQuery('SELECT username, name, email FROM #__users WHERE id ='.$usr_id);
				$usrname = $JLMS_DB->LoadObject();
				
				$results['results'][$j]['username'] = $usrname->username;
				$results['results'][$j]['name'] = $usrname->name;
				//$results['results'][$j]['email'] = $usrname->email;
				
				$count = 0;
				$k=0;
				foreach($courses as $key=>$course_id){
					$count++;
					$hit_num = 0;
					for($i=0;$i<count($hits);$i++){
						if($hits[$i]->c_id == $course_id && $hits[$i]->usr_id == $usr_id){
							$hit_num = $hits[$i]->hits;
							break;// by DEN
						} else if($hits[$i]->c_id == $course_id && $usr_id == 'total'){
							$hit_num = $hits[$i]->hits;
							break;
						}
					}
					
					$view_is_course = 1;
					if($JLMS_CONFIG->get('flms_integration', 1)){
						$params = new JLMSParameters($crs_options[$key]->params);
						$view_is_course = $params->get('show_in_report', 1);	
					}
					if($view_is_course){
						$results['results_hit'][$j][$k] = $hit_num;
					}
					$k++;
				}
				$j++;
			}
			
			$results_total_hits = array();
			$k=0;
			foreach($courses as $course_id){
				$results_total_hits[$k]['hits'] = 0;
				foreach($tot_hits as $hit){
					if($course_id == $hit->c_id){
						$results_total_hits[$k]['hits'] = $hit->hits;
					} 
				}
				$k++;
			}
			$results['results_total_hits'] = $results_total_hits;
		break;	
		
		case 'report_certif':
			$users_str = implode(',', $users);
			$courses_str = implode(',', $courses);
			if (!$courses_str) {
				$courses_str = '0';
			}
			if (!$users_str) {
				$users_str = '0';
			}
			
			$query = "SELECT h.user_id as usr_id,h.course_id as c_id FROM #__lms_certificate_users as h LEFT JOIN #__users as u ON h.user_id=u.id LEFT JOIN #__lms_courses as c ON h.course_id = c.id WHERE h.course_id IN (".$courses_str.") AND h.user_id IN  (".$users_str.")  ORDER BY h.course_id,h.user_id";
			$JLMS_DB->SetQuery( $query );
			$hits = $JLMS_DB->LoadObjectLIST();
			
			$JLMS_DB->setQuery('SELECT course_name FROM #__lms_courses WHERE id IN('.$courses_str.') ORDER BY course_name');
			$crs_name = $JLMS_DB->loadResultArray();
																			
			$JLMS_DB->setQuery('SELECT * FROM #__lms_courses WHERE id IN('.$courses_str.') ORDER BY course_name');
			$crs_options = $JLMS_DB->loadObjectList();
			
			$results['title_courses'] = array();
			
			$i=0;
			foreach($crs_name as $key=>$c_name){
				$view_is_course = 1;
				if($JLMS_CONFIG->get('flms_integration', 1)){
					$params = new JLMSParameters($crs_options[$key]->params);
					$view_is_course = $params->get('show_in_report', 1);	
				}
				if($view_is_course){
					$results['title_courses'][$i]['course_name'] = $c_name; 
					$i++;
				}
			}
			
			$results['results'] = array();
			$results['results_hit'] = array();
			
			$j = 0;
			foreach($users as $usr_id) {
				$JLMS_DB->setQuery('SELECT username,name, email FROM #__users WHERE id ='.$usr_id);
				$usrname = $JLMS_DB->LoadObject();
				
				$course_hits = 0;
				
				$results['results'][$j]['username'] = $usrname->username;
				$results['results'][$j]['name'] = $usrname->name;
				//$results['results'][$j]['email'] = $usrname->email;
				
				$count = 0;
				$k=0;
				foreach($courses as $key=>$course_id){
					$count++;
					$hit_num = _JLMS_NO_ALT_TITLE;
					for($i=0;$i<count($hits);$i++){
						if($hits[$i]->c_id == $course_id && $hits[$i]->usr_id == $usr_id){
							$hit_num = _JLMS_YES_ALT_TITLE;
							break;// by DEN
						}
					}
					$view_is_course = 1;
					if($JLMS_CONFIG->get('flms_integration', 1)){
						$params = new JLMSParameters($crs_options[$key]->params);
						$view_is_course = $params->get('show_in_report', 1);	
					}
					if($view_is_course){
						$results['results_hit'][$j][$k] = $hit_num;
						$k++;
					}
				}
				$j++;
			}
		break;
		
		case 'report_grade':
			$course_info = array();
			$course_info_hits = array();
			$title_headers = array();
			$data_grade = array();
			
			$rows = array();
			if(isset($reporting_header['data_grade']['rowz']) && isset($reporting_header['data_grade']['lists']) && count($reporting_header['data_grade']['rowz']) && count($reporting_header['data_grade']['lists'])){
				$rows = $reporting_header['data_grade']['rowz'];
				$lists = $reporting_header['data_grade']['lists'];
				
				for($i=0;$i<count($rows);$i++){
					$row = $rows[$i];
					
					$course_info[$i]['course_name'] = $row->course_name;
					$course_info[$i]['hits'] = $lists['hits'][$i];
					
					/*
					$course_info[$i][] = $row->course_name;
					$course_info[$i][] = $lists['hits'][$i];
					$course_info_hits[$i][] = $lists['hits'][$i];
					*/
					
					$title_headers[$i][] = _JLMS_REPORTS_CONCLUSION_ROW;
					$data_grade[$i][] = $row->user_certificate ? _CMN_YES : _CMN_NO;
					
					$sc_num = 0;
					$sc_num2 = 0;
					foreach($lists['sc_rows'][$i] as $sc_row){
						if($sc_row->show_in_gradebook){
							$title_headers[$i][] = $sc_row->lpath_name;	
							$sc_num++;
							$j = 0;
							while ($j < count($row->scorm_info)){
								if ($row->scorm_info[$j]->gbi_id == $sc_row->item_id) {
									if ($sc_num2 < $sc_num) {
										if ($row->scorm_info[$j]->user_status == -1) {
											$data_grade[$i][] = '-';
										} else {
											$user_status = '';
											$user_status .= $row->scorm_info[$j]->user_status ? _CMN_YES : _CMN_NO;
											$user_status .= isset($row->scorm_info[$j]->user_grade) ? ' '.$row->scorm_info[$j]->user_grade : '';
											$user_status .= isset($row->scorm_info[$j]->user_pts) ? ' ('.$row->scorm_info[$j]->user_pts.')' : '';
											
											$data_grade[$i][] = $user_status;
										}
										$sc_num2++;
									}
								}
								$j++;
							}
						}
					}
					foreach($lists['quiz_rows'][$i] as $quiz_row){
						$title_headers[$i][] = $quiz_row->c_title;
						$j = 0;
						while ($j < count($row->quiz_info)) {
							if ($row->quiz_info[$j]->gbi_id == $quiz_row->c_id) {
								if ($row->quiz_info[$j]->user_status == -1) {
									$data_grade[$i][] = '-';
								} else {
									$user_status = '';
//									$user_status .= $row->quiz_info[$j]->user_status ? _CMN_YES : _CMN_NO;
//									$user_status .= ' '.$row->quiz_info[$j]->user_grade.' ';
//									$user_status .= '('.$row->quiz_info[$j]->user_pts_full.')';
									
									$user_status = JLMS_showQuizStatus($row->quiz_info[$j], '', 1);
									
									$data_grade[$i][] = $user_status;
								}
							}
							$j ++;
						}	
					}
					foreach($lists['gb_rows'][$i] as $gb_row){
						$title_headers[$i][] = $gb_row->gbi_name;	
					}
					$j = 0;
					while ($j < count($row->grade_info)) {
						$data_grade[$i][] = $row->grade_info[$j]->user_grade;
						$j ++;
					} 
					
					
				}	
			}
			
			$results['course_info'] = $course_info;
			$results['title_headers'] = $title_headers;
			$results['data_grade'] = $data_grade;
			
			$results['data'] = array();
			foreach($course_info as $n=>$ci){
				$results['data'][$n][] = $title_headers[$n];
				$results['data'][$n][] = $data_grade[$n];
			}
		break;
	}
	
	//echo '<pre>';
	//print_r($results);
	//print_r($reporting_header);
	//echo '</pre>';
	//die;
	
	global $task;
	if($task == 'report_access'){
		$tmpl_name = 'access_report';
		$prefix_title = str_replace("_", " ", $tmpl_name);
	} else 
	if($task == 'report_certif'){
		$tmpl_name = 'completion_report';
		$prefix_title = str_replace("_", " ", $tmpl_name);
	} else 
	if($task == 'report_grade'){
		$tmpl_name = 'user_report';
		$prefix_title = str_replace("_", " ", $tmpl_name);
	}

	JLMS_reporting::exportXLS($results, $reporting_header, $tmpl_name, $prefix_title);
}
function JLMS_REP_exportCsv($hits, $tot_hits=array(), $users, $courses, $pageNav, $lists, $levels, $filt_cat, $filt_group, $option, $is_full) {
	global $JLMS_DB, $JLMS_CONFIG, $JLMS_SESSION, $task;
	
	switch($task){
		case 'report_access':
			$users_str = implode(',', $users);
			$courses_str = implode(',', $courses);
			
			$JLMS_DB->setQuery('SELECT course_name FROM #__lms_courses WHERE id IN('.$courses_str.') ORDER BY course_name');
			$crs_name = $JLMS_DB->loadResultArray();
																			
			$JLMS_DB->setQuery('SELECT * FROM #__lms_courses WHERE id IN('.$courses_str.') ORDER BY course_name');
			$crs_options = $JLMS_DB->loadObjectList();
		
			$text_to_csv = 'username,name,email,';
			
			$array_zagolovk = array();
			
			foreach($crs_name as $key=>$c_name){
				$view_is_course = 1;
				if($JLMS_CONFIG->get('flms_integration', 1)){
					$params = new JLMSParameters($crs_options[$key]->params);
					$view_is_course = $params->get('show_in_report', 1);	
				}
				if($view_is_course){
						$array_zagolovk[] = JLMS_processCSVField($c_name); 
					//echo '<th class="sectiontableheader" style="text-align:center;">'.$c_name.'</th>';
				}
			}
		
			$zagolovk = implode(',',$array_zagolovk);
			
			if($zagolovk) {
				$text_to_csv .= $zagolovk."\n";
			}
			else {
				$text_to_csv .= "\n";
			}
			
			$zzz = 0;
			foreach($users as $usr_id) {
				$JLMS_DB->setQuery('SELECT username,name, email FROM #__users WHERE id ='.$usr_id);
				$usrname = $JLMS_DB->LoadObject();
				
				$course_hits = 0;
				
				$text_to_csv .= $usrname->username.','.JLMS_processCSVField($usrname->name).','.$usrname->email.',';
				$count = 0;
				foreach($courses as $key=>$course_id){
					$count++;
					$hit_num = 0;
					for($i=0;$i<count($hits);$i++){
						if($hits[$i]->c_id == $course_id && $hits[$i]->usr_id == $usr_id){
							$hit_num = $hits[$i]->hits;
							break;// by DEN
						}
					}
					$view_is_course = 1;
					if($JLMS_CONFIG->get('flms_integration', 1)){
						$params = new JLMSParameters($crs_options[$key]->params);
						$view_is_course = $params->get('show_in_report', 1);	
					}
					if($view_is_course){
						$text_to_csv .= $hit_num;
						if($count != count($courses)) {
							$text_to_csv .= ',';
						}
					}
				}
				$text_to_csv .= "\n";
				$zzz++;
			}
			$text_to_csv .= 'Total,,,';
			
			$results_total_hits = array();
			$k=0;
			foreach($courses as $i=>$course_id){
				foreach($tot_hits as $j=>$hit){
					if($course_id == $hit->c_id){
						foreach($hit as $key=>$tmp_hit){
							if($key == 'hits'){
								$results_total_hits[] = $tmp_hit;
								$k++;
							}
						}
					}
				}
			}
			$text_to_csv .= implode(",", $results_total_hits);
			$text_to_csv .= "\n";
		break;
		
		case 'report_certif':
			$users_str = implode(',', $users);
			$courses_str = implode(',', $courses);
			
			$query = "SELECT h.user_id as usr_id,h.course_id as c_id FROM #__lms_certificate_users as h LEFT JOIN #__users as u ON h.user_id=u.id LEFT JOIN #__lms_courses as c ON h.course_id = c.id WHERE h.course_id IN (".$courses_str.") AND h.user_id IN  (".$users_str.")  ORDER BY h.course_id,h.user_id";
			$JLMS_DB->SetQuery( $query );
			$hits = $JLMS_DB->LoadObjectLIST();
			
			$JLMS_DB->setQuery('SELECT course_name FROM #__lms_courses WHERE id IN('.$courses_str.') ORDER BY course_name');
			$crs_name = $JLMS_DB->loadResultArray();
																			
			$JLMS_DB->setQuery('SELECT * FROM #__lms_courses WHERE id IN('.$courses_str.') ORDER BY course_name');
			$crs_options = $JLMS_DB->loadObjectList();
		
			$text_to_csv = 'username,name,email,';
			
			$array_zagolovk = array();
			
			foreach($crs_name as $key=>$c_name){
				$view_is_course = 1;
				if($JLMS_CONFIG->get('flms_integration', 1)){
					$params = new JLMSParameters($crs_options[$key]->params);
					$view_is_course = $params->get('show_in_report', 1);	
				}
				if($view_is_course){
						$array_zagolovk[] = JLMS_processCSVField($c_name); 
					//echo '<th class="sectiontableheader" style="text-align:center;">'.$c_name.'</th>';
				}
			}
		
			$zagolovk = implode(',',$array_zagolovk);
			
			if($zagolovk) {
				$text_to_csv .= $zagolovk."\n";
			}
			else {
				$text_to_csv .= "\n";
			}
			
			$zzz = 0;
			foreach($users as $usr_id) {
				$JLMS_DB->setQuery('SELECT username,name, email FROM #__users WHERE id ='.$usr_id);
				$usrname = $JLMS_DB->LoadObject();
				
				$course_hits = 0;
				
				$text_to_csv .= $usrname->username.','.JLMS_processCSVField($usrname->name).','.$usrname->email.',';
				$count = 0;
				foreach($courses as $key=>$course_id){
					$count++;
					$hit_num = _JLMS_NO_ALT_TITLE;
					for($i=0;$i<count($hits);$i++){
						if($hits[$i]->c_id == $course_id && $hits[$i]->usr_id == $usr_id){
							$hit_num = _JLMS_YES_ALT_TITLE;
							break;// by DEN
						}
					}
					$view_is_course = 1;
					if($JLMS_CONFIG->get('flms_integration', 1)){
						$params = new JLMSParameters($crs_options[$key]->params);
						$view_is_course = $params->get('show_in_report', 1);	
					}
					if($view_is_course){
						$text_to_csv .= $hit_num;
						if($count != count($courses)) {
							$text_to_csv .= ',';
						}
					}
				}
				
				$text_to_csv .= "\n";
				$zzz++;
			}
		break;
		
		case 'report_scorm':
			$rows = $courses;
			
			$array_zagolovk = array();
			$array_zagolovk[] = 'Username';
			$array_zagolovk[] = 'Name';
			$array_zagolovk[] = 'Email';
			$array_zagolovk[] = 'Course Name';
			$array_zagolovk[] = 'Course ID';
			$array_zagolovk[] = 'Date';
			$array_zagolovk[] = 'Score';
			$array_zagolovk[] = 'Course Status';
			
			$zagolovk = implode(',',$array_zagolovk);
			
			$text_to_csv = '';
			
			if($zagolovk) {
				$text_to_csv .= $zagolovk."\n";
			}
			else {
				$text_to_csv .= "\n";
			}
			
			foreach($rows as $row){
				$text = array();
				$text[] = $row->username;
				$text[] = JLMS_processCSVField($row->name);
				$text[] = $row->email;
				$text[] = JLMS_processCSVField($row->course_name);
				$text[] = JLMS_processCSVField($row->lpath_name);
				if(isset($row->scorm_data) && $row->scorm_data->status){
					if($row->scorm_data->end){
						$date_end = date("Y-m-d H:i:s", $row->scorm_data->end);
						$text[] = JLMS_dateToDisplay($date_end);
					} else {
						$text[] = '';	
					}
				} else {
					$text[] = '';	
				}
				if(isset($row->scorm_data)){
					$text[] = $row->scorm_data->score;
				} else {
					$text[] = '';		
				}
				if($row->course_status){
					$text[] = 'Completed';
				} else {
					$text[] = 'Incompleted';
				}
				
				$text_to_csv .= implode(",", $text);
				
				$text_to_csv .= "\n"; 	
			}
				
		break;	
	}

	global $task;
	if($task == 'report_access'){
		$tmpl_name = 'access_report';
		$prefix_title = str_replace("_", " ", $tmpl_name);
	} else 
	if($task == 'report_certif'){
		$tmpl_name = 'completion_report';
		$prefix_title = str_replace("_", " ", $tmpl_name);
	} else 
	if($task == 'report_grade'){
		$tmpl_name = 'user_report';
		$prefix_title = str_replace("_", " ", $tmpl_name);
	}  else 
	if($task == 'report_scorm'){
		$tmpl_name = 'scorm_report';
		$prefix_title = str_replace("_", " ", $tmpl_name);
	}

	$ug_name = $tmpl_name.'_'.date('dMY');
	if (preg_match('/Opera(\/| )([0-9].[0-9]{1,2})/', $_SERVER['HTTP_USER_AGENT'])) {
		$UserBrowser = "Opera";
	}
	elseif (preg_match('/MSIE ([0-9].[0-9]{1,2})/', $_SERVER['HTTP_USER_AGENT'])) {
		$UserBrowser = "IE";
	} else {
		$UserBrowser = '';
	}
	header("Content-type: application/csv");
	header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
	header("Content-Length: ".strlen(trim($text_to_csv)));
	header('Content-Disposition: attachment; filename="'.$ug_name.'.csv"');
	if ($UserBrowser == 'IE') {
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
	} else {
		header('Pragma: no-cache');
	}
	echo $text_to_csv;
	exit();
}
?>