<?php
defined( '_JLMS_EXEC' ) or die( 'Restricted access' );


$task 		= mosGetParam( $_REQUEST, 'task', '' );
$id 		= mosGetParam( $_REQUEST, 'id', '' );
$option 	= mosGetParam( $_REQUEST, 'option', '' );


require_once( _JOOMLMS_FRONT_HOME.'/joomla_lms.user_notices.html.php' );

switch ($task){
	case 'new_notice_no_ajax':	
	case 'new_notice': 				JLMS_new_notice($option); 			break;
	case 'edit_notice_no_ajax':		JLMS_edit_notice_no_ajax($option); 	break;
	case 'edit_notice': 			JLMS_edit_notice($option); 			break;
	case 'save_notice_no_ajax':	
	case 'save_notice':				JLMS_save_notice($option); 			break;
	case 'view_notice':	 			JLMS_view_notice($option);			break;
	case 'delete_notice':			JLMS_del_notice($option);			break;
	case 'delete_notice_no_ajax':	JLMS_del_notice_no_ajax($option);	break;
	case 'get_notice_count':		JLMS_get_notice_count($option);		break;
	
	case 'view_all_notices':		JLMS_view_all_notices($option);		break;
}

function JLMS_get_notice_count($option)
{
	global $JLMS_CONFIG,$my,$JLMS_DB;
	$ntask 		= strval(mosGetParam( $_REQUEST, 'ntask', '' ));
	$doc_id 	= intval(mosGetParam( $_REQUEST, 'doc_id', 0 ));
	$course_id 	= intval(mosGetParam( $_REQUEST, 'course_id', 0 ));
	$query = "SELECT COUNT(*) FROM #__lms_page_notices WHERE usr_id=".$my->id." AND course_id=$course_id AND task=".$JLMS_DB->quote($ntask)." AND doc_id=$doc_id";
	$JLMS_DB->setQuery($query);
	echo $JLMS_DB->loadResult();
	die;
}
function JLMS_new_notice($option)
{
	global $JLMS_CONFIG, $JLMS_SESSION, $my, $JLMS_DB, $task;
	$ntask 		= strval(mosGetParam( $_REQUEST, 'ntask', '' ));
	$doc_id 	= intval(mosGetParam( $_REQUEST, 'doc_id', 0 ));
	$course_id 	= intval(mosGetParam( $_REQUEST, 'course_id', 0 ));
	$query = "SELECT * FROM #__lms_page_notices WHERE usr_id=".$my->id." AND course_id=$course_id AND task=".$JLMS_DB->quote($ntask)." AND doc_id=$doc_id ORDER BY data desc";
	$JLMS_DB->setQuery($query);
	$notices = $JLMS_DB->loadObjectList();
	if($task == 'new_notice_no_ajax'){
		JLMS_ShowHeading($JLMS_CONFIG->get('jlms_heading'), true);
		
		/*FLMS Categories*/
		$lists = array();
		if ($JLMS_CONFIG->get('multicat_use', 0)){
			$query = "SELECT * FROM #__lms_course_cats_config ORDER BY id";
			$JLMS_DB->setQuery($query);
			$lists['levels'] = $JLMS_DB->loadObjectList();
			if(count($lists['levels']) == 0){
				for($i=0;$i<5;$i++){
					if($i>0){
						$lists['levels'][$i]->cat_name = _JLMS_COURSES_COURSES_GROUPS;	
					} else {
						$lists['levels'][$i]->cat_name = _JLMS_COURSES_COURSES_GROUPS;
					}
				}
			}
	
			$level_id = array();
			for($i=0;$i<count($lists['levels']);$i++){
				if($i == 0){
					$level_id[$i] = intval( mosGetParam( $_REQUEST, 'filter_id_'.$i.'', $JLMS_SESSION->get('FLMS_filter_id_'.$i.'', 0) ) );
					$JLMS_SESSION->set('FLMS_filter_id_'.$i.'', $level_id[$i]);
				} else {
					$level_id[$i] = intval( mosGetParam( $_REQUEST, 'filter_id_'.$i.'', $JLMS_SESSION->get('FLMS_filter_id_'.$i.'', 0) ) );
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
			
			for($i=0;$i<count($lists['levels']);$i++){
				if($i > 0 && $level_id[$i - 1] == 0){
					$level_id[$i] = 0;
					$JLMS_SESSION->set('FLMS_filter_id_'.$i.'', $level_id[$i]);
					$parent_id[$i] = 0;
				} elseif($i == 0 && $level_id[$i] == 0) {
					$level_id[$i] = 0;
					$JLMS_SESSION->set('FLMS_filter_id_'.$i.'', $level_id[$i]);
					$parent_id[$i] = 0;
				}
			}
			
			$javascript = 'onclick="javascript:read_filter();" onchange="javascript:write_filter();document.form_pgnotice.task.value=\'new_notice_no_ajax\';document.form_pgnotice.submit();"';
			
			$query1 = "SELECT group_id FROM `#__lms_users_in_global_groups` WHERE user_id = '".$my->id."'";
			$JLMS_DB->setQuery($query1);
			$user_group_ids = $JLMS_DB->loadResultArray();
	
			for($i=0;$i<count($lists['levels']);$i++) {
				if($i == 0 || $parent_id[$i]){ //(Max): extra requests
					if( $parent_id[$i] == 0) {
						$query = "SELECT * FROM `#__lms_course_cats` WHERE `parent` = '0'";
						/*
						$query .= "\n AND (";
						if (count($user_group_ids)) {
							$query .= "( `restricted` = 1 AND ( `groups` LIKE '%|$user_group_ids[0]|%'";
							for($i1=1;$i1<count($user_group_ids);$i1++) {
								$query .= "\n OR `groups` like '%|$user_group_ids[$i1]|%'";
							}
							$query .=  "\n ) ) \n OR ";
						}
						$query .= "(`restricted` = 0 )) ";
						*/
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
						}
						$lists['filter_'.$i.''] = mosHTML::selectList($type_level[$i], 'filter_id_'.$i.'', 'class="inputbox" size="1" '.$javascript, 'value', 'text', $level_id[$i] ); //onchange="document.location.href=\''. $link_multi .'\';"
					} elseif($i == 0) {
						$type_level[$i][] = mosHTML::makeOption( 0, ' &nbsp; ' );
						foreach ($groups as $group){
							$type_level[$i][] = mosHTML::makeOption( $group->id, $group->c_category );
						}
						$lists['filter_'.$i.''] = mosHTML::selectList($type_level[$i], 'filter_id_'.$i.'', 'class="inputbox" size="1" '.$javascript, 'value', 'text', $level_id[$i] ); //onchange="document.location.href=\''. $link_multi .'\';"
					}
				}
			}	
		}
		//FLMS multicat
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
				$where .= "\n AND ( a.cat_id IN (".$catids.")";
				if($JLMS_CONFIG->get('sec_cat_use', 0)){
					foreach ($tmp_cats_filter as $tmp_cats_filter_one) {
						$where .= "\n OR a.sec_cat LIKE '%|".$tmp_cats_filter_one."|%'";
					}
				}
				$where .= "\n )";
			}
			//NEW MUSLTICATS
			
		}
		
		$query = "SELECT a.id as value, a.course_name as text"
		. "\n FROM #__lms_courses as a"
		. "\n WHERE a.id > 0"
		. "\n $where"
		. "\n ORDER BY ".($JLMS_CONFIG->get('lms_courses_sortby',0)?"ordering, ":"")."course_name"
		;
		$JLMS_DB->setQuery($query);
		$courses = $JLMS_DB->loadObjectList();
		
		$courses_ids = array();
		foreach($courses as $course){
			$courses_ids[] = $course->value;	
		}
		$str_courses_ids = implode(",", $courses_ids);
		
		$f_courses = array();
		$f_courses[] = mosHTML::makeOption(0, '&nbsp;');
		$f_courses = array_merge($f_courses, $courses);
		$lists['f_course'] = mosHTML::selectList( $f_courses, 'course_id', 'class="inputbox" size="1" '/*.$javascript*/, 'value', 'text', $course_id); 
		/*FLMS Categories*/
		
		FLMS_page_notice::new_notice_no_ajax($option, $notices, $ntask, $doc_id, $course_id, $lists);
	} else {
		FLMS_page_notice::new_notice($option, $notices, $ntask, $doc_id, $course_id);
	}
}
function JLMS_edit_notice($option)
{
	global $JLMS_CONFIG,$my,$JLMS_DB;
	$ntask 		= strval(mosGetParam( $_REQUEST, 'ntask', '' ));
	$doc_id 	= intval(mosGetParam( $_REQUEST, 'doc_id', 0 ));
	$course_id 	= intval(mosGetParam( $_REQUEST, 'course_id', 0 ));
	$query = "SELECT * FROM #__lms_page_notices WHERE usr_id=".$my->id." AND course_id=$course_id AND task=".$JLMS_DB->quote($ntask)." AND doc_id=$doc_id ORDER BY data desc";
	$JLMS_DB->setQuery($query);
	$notices = $JLMS_DB->loadObjectList();
	$v_id		= intval(mosGetParam( $_REQUEST, 'v_id', 0 ));

	$query = "SELECT * FROM #__lms_page_notices WHERE id=$v_id AND usr_id=".$my->id;
	$JLMS_DB->setQuery($query);
	$noticez = $JLMS_DB->loadObjectList();
	if (isset($noticez[0])) {
		$row = $noticez[0];
		$row->notice = str_replace("<br />", "\n", $row->notice);
	} else {
		$row = null;
	}
	FLMS_page_notice::new_notice($option, $notices, $ntask, $doc_id, $course_id, $row);
}
function JLMS_edit_notice_no_ajax($option)
{
	global $JLMS_CONFIG,$my,$JLMS_DB,$JLMS_SESSION;
	
	JLMS_ShowHeading($JLMS_CONFIG->get('jlms_heading'), true);
	
	$cid 		= mosGetParam( $_REQUEST, 'cid', array(0) );
	$ntask 		= strval(mosGetParam( $_REQUEST, 'ntask', '' ));
	$doc_id 	= intval(mosGetParam( $_REQUEST, 'doc_id', 0 ));
	$course_id 	= intval(mosGetParam( $_REQUEST, 'course_id', 0 ));
	$v_id		= intval(mosGetParam( $_REQUEST, 'v_id', 0 ));
	
	$notices = array();
	$query = "SELECT * FROM #__lms_page_notices WHERE id = '".$cid[0]."' AND usr_id=".$my->id;
	$JLMS_DB->setQuery($query);
	$noticez = $JLMS_DB->loadObjectList();
	if (isset($noticez[0])) {
		$row = $noticez[0];
	} else {
		$row = null;
	}
	
	$course_id = isset($row->course_id) ? $row->course_id : $course_id;
	$course_available = 0;
	if(isset($row->doc_id) && !$row->doc_id){
		$course_available = 1;	
	}
	
	$lists = array();
	if ($JLMS_CONFIG->get('multicat_use', 0)){
		$query = "SELECT * FROM #__lms_course_cats_config ORDER BY id";
		$JLMS_DB->setQuery($query);
		$lists['levels'] = $JLMS_DB->loadObjectList();
		if(count($lists['levels']) == 0){
			for($i=0;$i<5;$i++){
				if($i>0){
					$lists['levels'][$i]->cat_name = _JLMS_COURSES_COURSES_GROUPS;	
				} else {
					$lists['levels'][$i]->cat_name = _JLMS_COURSES_COURSES_GROUPS;
				}
			}
		}

		$level_id = array();
		for($i=0;$i<count($lists['levels']);$i++){
			if($i == 0){
				$level_id[$i] = intval( mosGetParam( $_REQUEST, 'filter_id_'.$i.'', $JLMS_SESSION->get('FLMS_filter_id_'.$i.'', 0) ) );
				$JLMS_SESSION->set('FLMS_filter_id_'.$i.'', $level_id[$i]);
			} else {
				$level_id[$i] = intval( mosGetParam( $_REQUEST, 'filter_id_'.$i.'', $JLMS_SESSION->get('FLMS_filter_id_'.$i.'', 0) ) );
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
		
		for($i=0;$i<count($lists['levels']);$i++){
			if($i > 0 && $level_id[$i - 1] == 0){
				$level_id[$i] = 0;
				$JLMS_SESSION->set('FLMS_filter_id_'.$i.'', $level_id[$i]);
				$parent_id[$i] = 0;
			} elseif($i == 0 && $level_id[$i] == 0) {
				$level_id[$i] = 0;
				$JLMS_SESSION->set('FLMS_filter_id_'.$i.'', $level_id[$i]);
				$parent_id[$i] = 0;
			}
		}
		
		$javascript = 'onclick="javascript:read_filter();" onchange="javascript:write_filter();document.form_pgnotice.task.value=\'edit_notice_no_ajax\';document.form_pgnotice.submit();"';
		
		$query1 = "SELECT group_id FROM `#__lms_users_in_global_groups` WHERE user_id = '".$my->id."'";
		$JLMS_DB->setQuery($query1);
		$user_group_ids = $JLMS_DB->loadResultArray();

		for($i=0;$i<count($lists['levels']);$i++) {
			if($i == 0 || $parent_id[$i]){ //(Max): extra requests
				if( $parent_id[$i] == 0) {
					$query = "SELECT * FROM `#__lms_course_cats` WHERE `parent` = '0'";
					/*
					$query .= "\n AND (";
					if (count($user_group_ids)) {
						$query .= "( `restricted` = 1 AND ( `groups` LIKE '%|$user_group_ids[0]|%'";
						for($i1=1;$i1<count($user_group_ids);$i1++) {
							$query .= "\n OR `groups` like '%|$user_group_ids[$i1]|%'";
						}
						$query .=  "\n ) ) \n OR ";
					}
					$query .= "(`restricted` = 0 )) ";
					*/
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
					}
					$lists['filter_'.$i.''] = mosHTML::selectList($type_level[$i], 'filter_id_'.$i.'', 'class="inputbox" size="1" '.$javascript, 'value', 'text', $level_id[$i] ); //onchange="document.location.href=\''. $link_multi .'\';"
				} elseif($i == 0) {
					$type_level[$i][] = mosHTML::makeOption( 0, ' &nbsp; ' );
					foreach ($groups as $group){
						$type_level[$i][] = mosHTML::makeOption( $group->id, $group->c_category );
					}
					$lists['filter_'.$i.''] = mosHTML::selectList($type_level[$i], 'filter_id_'.$i.'', 'class="inputbox" size="1" '.$javascript, 'value', 'text', $level_id[$i] ); //onchange="document.location.href=\''. $link_multi .'\';"
				}
			}
		}	
	}
	//FLMS multicat
	$where = '';
	if ($JLMS_CONFIG->get('multicat_use', 0)){
		
		//NEW MUSLTICATS
//		$tmp_level = array();
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
			$where .= "\n AND ( a.cat_id IN (".$catids.")";
			if($JLMS_CONFIG->get('sec_cat_use', 0)){
				foreach ($tmp_cats_filter as $tmp_cats_filter_one) {
					$where .= "\n OR a.sec_cat LIKE '%|".$tmp_cats_filter_one."|%'";
				}
			}
			$where .= "\n )";
		}
		//NEW MUSLTICATS
		
	}
	
	$query = "SELECT a.id as value, a.course_name as text"
	. "\n FROM #__lms_courses as a"
	. "\n WHERE a.id > 0"
	. "\n $where"
	. "\n ORDER BY ".($JLMS_CONFIG->get('lms_courses_sortby',0)?"ordering, ":"")."course_name"
	;
	$JLMS_DB->setQuery($query);
	$courses = $JLMS_DB->loadObjectList();
	
	$courses_ids = array();
	foreach($courses as $course){
		$courses_ids[] = $course->value;	
	}
	$str_courses_ids = implode(",", $courses_ids);
	
	$f_courses = array();
	$f_courses[] = mosHTML::makeOption(0, '&nbsp;');
	$f_courses = array_merge($f_courses, $courses);
	$lists['f_course'] = mosHTML::selectList( $f_courses, 'course_id', 'class="inputbox" size="1" '/*.$javascript*/ .(!$course_available ? 'disabled="disabled"':''), 'value', 'text', $course_id); 
	$lists['course_available'] = $course_available;	
	if(!$course_available){
		$lists['course_id'] = $course_id;	
	}
	
	FLMS_page_notice::new_notice_no_ajax($option, $notices, $ntask, $doc_id, $course_id, $lists, $row);
}
function JLMS_save_notice($option)
{
	global $JLMS_CONFIG, $my ,$JLMS_DB, $Itemid, $task;
	$ntask 		= strval(mosGetParam( $_REQUEST, 'ntask', '' ));
	$doc_id 	= intval(mosGetParam( $_REQUEST, 'doc_id', 0 ));
	$course_id 	= intval(mosGetParam( $_REQUEST, 'course_id', 0 ));
	$v_id 		= intval(mosGetParam( $_REQUEST, 'v_id', 0 ));

	$notice = strval(JLMS_getParam_LowFilter($_REQUEST, 'p_notice', ''));
	$notice = JLMS_ProcessText_LowFilter($notice);
	
	if($ntask == 'save_notice'){
		$notice = str_replace("\n", "<br />", $notice);
	}
	
	if(!$course_id){
		$ntask = 'new_notice_no_ajax';
	}
	if($course_id && $ntask == 'new_notice_no_ajax'){
		$ntask = 'details_course';	
	}
	
	if(!$v_id)
	{
		$query = "INSERT INTO #__lms_page_notices(id,usr_id,course_id,task,doc_id,notice,data)";
		$query .= " VALUES('',".$my->id.",".$course_id.",".$JLMS_DB->quote($ntask).",".$doc_id.",".$JLMS_DB->quote($notice).",'".date("Y-m-d H:i:s")."')";
		$JLMS_DB->setQuery($query);
		$JLMS_DB->query();
		/*$query = "SELECT COUNT(*) FROM #__lms_page_notices WHERE usr_id=".$my->id." AND course_id=$course_id AND task='".$ntask."' AND doc_id=$doc_id";
		$JLMS_DB->setQuery($query);
		$JLMS_DB->loadResult();*/
		if($task == 'save_notice_no_ajax'){
			JLMSRedirect( sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=view_all_notices") );
		} else {
			$query = "SELECT * FROM #__lms_page_notices WHERE usr_id=".$my->id." AND course_id=$course_id AND task=".$JLMS_DB->quote($ntask)." AND doc_id=$doc_id ORDER BY data desc";
			$JLMS_DB->setQuery($query);
			$notices = $JLMS_DB->loadObjectList();
			FLMS_page_notice::new_notice($option, $notices, $ntask, $doc_id, $course_id);
		}
	} else {	
		$query = "UPDATE #__lms_page_notices SET notice=".$JLMS_DB->quote($notice).""
		.(isset($course_id) ? "\n, course_id = '".$course_id."'" : '')
		.($ntask != '' ? "\n, task = '".$ntask."'" : '')
		. "\n WHERE id=".$v_id." AND usr_id = ".$my->id;	
		$JLMS_DB->setQuery($query);
		$JLMS_DB->query();
		if($task == 'save_notice_no_ajax'){
			JLMSRedirect( sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=view_all_notices") );
		} else {
			$query = "SELECT * FROM #__lms_page_notices WHERE usr_id=".$my->id." AND course_id=$course_id AND task=".$JLMS_DB->quote($ntask)." AND doc_id=$doc_id ORDER BY data desc";
			$JLMS_DB->setQuery($query);
			$notices = $JLMS_DB->loadObjectList();
			FLMS_page_notice::new_notice($option, $notices, $ntask, $doc_id, $course_id);
		}
	}

}
function JLMS_view_notice($option)
{
	global $JLMS_CONFIG,$my,$JLMS_DB;
	$ntask 		= strval(mosGetParam( $_REQUEST, 'ntask', '' ));
	$doc_id 	= intval(mosGetParam( $_REQUEST, 'doc_id', 0 ));
	$course_id 	= intval(mosGetParam( $_REQUEST, 'course_id', 0 ));
	$query = "SELECT * FROM #__lms_page_notices WHERE usr_id=".$my->id." AND course_id=$course_id AND task=".$JLMS_DB->quote($ntask)." AND doc_id=$doc_id ORDER BY data desc";
	$JLMS_DB->setQuery($query);
	$notices = $JLMS_DB->loadObjectList();
	FLMS_page_notice::view_notice($notices, $option, $ntask, $doc_id, $course_id);
}
function JLMS_del_notice($option)
{
	global $JLMS_CONFIG,$my,$JLMS_DB;
	$ntask 		= strval(mosGetParam( $_REQUEST, 'ntask', '' ));
	$doc_id 	= intval(mosGetParam( $_REQUEST, 'doc_id', 0 ));
	$course_id 	= intval(mosGetParam( $_REQUEST, 'course_id', 0 ));
	$v_id 	= intval(mosGetParam( $_REQUEST, 'v_id', 0 ));
	$query = "DELETE FROM #__lms_page_notices WHERE id=$v_id AND usr_id = ".$my->id;
	$JLMS_DB->setQuery($query);
	$JLMS_DB->query();
	$query = "SELECT * FROM #__lms_page_notices WHERE usr_id=".$my->id." AND course_id=$course_id AND task=".$JLMS_DB->quote($ntask)." AND doc_id=$doc_id ORDER BY data desc";
	$JLMS_DB->setQuery($query);
	$notices = $JLMS_DB->loadObjectList();
	FLMS_page_notice::new_notice( $option, $notices, $ntask, $doc_id, $course_id);	
}

function JLMS_del_notice_no_ajax($option)
{
	global $JLMS_CONFIG, $my, $JLMS_DB, $Itemid;
	$cid 		= mosGetParam( $_REQUEST, 'cid', array(0) );
	$ntask 		= strval(mosGetParam( $_REQUEST, 'ntask', '' ));
	$doc_id 	= intval(mosGetParam( $_REQUEST, 'doc_id', 0 ));
	$course_id 	= intval(mosGetParam( $_REQUEST, 'course_id', 0 ));
	$v_id 	= intval(mosGetParam( $_REQUEST, 'v_id', 0 ));
	$query = "DELETE FROM #__lms_page_notices WHERE id IN (".implode(",", $cid).") AND usr_id = ".$my->id;
	$JLMS_DB->setQuery($query);
	$JLMS_DB->query();
	
	JLMSRedirect( sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=view_all_notices") );	
}

function JLMS_view_all_notices($option){
	global $JLMS_DB, $my, $JLMS_CONFIG, $JLMS_SESSION;
	
	JLMS_ShowHeading($JLMS_CONFIG->get('jlms_heading'), true);
	
	$limit = intval( mosGetParam( $_GET, 'limit', $JLMS_SESSION->get('list_limit', $JLMS_CONFIG->get('list_limit')) ) );
	$JLMS_SESSION->set('list_limit', $limit);
	$limitstart	= intval( mosGetParam( $_REQUEST, 'limitstart', 0 ) );
	
	$course_id 	= intval(mosGetParam( $_REQUEST, 'course_id', 0 ));
	
	$lists = array();
	if ($JLMS_CONFIG->get('multicat_use', 0)){
		$query = "SELECT * FROM #__lms_course_cats_config ORDER BY id";
		$JLMS_DB->setQuery($query);
		$lists['levels'] = $JLMS_DB->loadObjectList();
		if(count($lists['levels']) == 0){
			for($i=0;$i<5;$i++){
				if($i>0){
					$lists['levels'][$i]->cat_name = _JLMS_COURSES_COURSES_GROUPS;	
				} else {
					$lists['levels'][$i]->cat_name = _JLMS_COURSES_COURSES_GROUPS;
				}
			}
		}

		$level_id = array();
		for($i=0;$i<count($lists['levels']);$i++){
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
			$query = "SELECT count(id) FROM `#__lms_course_cats` WHERE parent = '".$parent_id[$i]."' ORDER BY c_category";
			$JLMS_DB->setQuery($query);
			$groups = $JLMS_DB->loadResult();
			if($groups==0){
				$level_id[$i] = 0;
				$JLMS_SESSION->set('FLMS_filter_id_'.$i.'', $level_id[$i]);
			}
		}
		
		for($i=0;$i<count($lists['levels']);$i++){
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
		
		$javascript = 'onclick="javascript:read_filter();" onchange="javascript:write_filter();document.adminForm.task.value=\'view_all_notices\';document.adminForm.submit();"';
		
		$query1 = "SELECT group_id FROM `#__lms_users_in_global_groups` WHERE user_id = '".$my->id."'";
		$JLMS_DB->setQuery($query1);
		$user_group_ids = $JLMS_DB->loadResultArray();

		for($i=0;$i<count($lists['levels']);$i++) {
			
			if( $parent_id[$i] == 0) {
				$query = "SELECT * FROM `#__lms_course_cats` WHERE `parent` = '0'";
				/*
				$query .= "\n AND (";
				if (count($user_group_ids)) {
					$query .= "( `restricted` = 1 AND ( `groups` LIKE '%|$user_group_ids[0]|%'";
					for($i1=1;$i1<count($user_group_ids);$i1++) {
						$query .= "\n OR `groups` like '%|$user_group_ids[$i1]|%'";
					}
					$query .=  "\n ) ) \n OR ";
				}
				$query .= "(`restricted` = 0 )) ";
				*/
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
				}
				$lists['filter_'.$i.''] = mosHTML::selectList($type_level[$i], 'filter_id_'.$i.'', 'class="inputbox" size="1" '.$javascript, 'value', 'text', $level_id[$i] ); //onchange="document.location.href=\''. $link_multi .'\';"
			} elseif($i == 0) {
				$type_level[$i][] = mosHTML::makeOption( 0, ' &nbsp; ' );
				foreach ($groups as $group){
					$type_level[$i][] = mosHTML::makeOption( $group->id, $group->c_category );
				}
				$lists['filter_'.$i.''] = mosHTML::selectList($type_level[$i], 'filter_id_'.$i.'', 'class="inputbox" size="1" '.$javascript, 'value', 'text', $level_id[$i] ); //onchange="document.location.href=\''. $link_multi .'\';"
			}
		}	
	}
	//FLMS multicat
	$where = '';
	if ($JLMS_CONFIG->get('multicat_use', 0)){
		
		//NEW MUSLTICATS
//		$tmp_level = array();
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
			$where .= "\n AND ( a.cat_id IN (".$catids.")";
			if($JLMS_CONFIG->get('sec_cat_use', 0)){
				foreach ($tmp_cats_filter as $tmp_cats_filter_one) {
					$where .= "\n OR a.sec_cat LIKE '%|".$tmp_cats_filter_one."|%'";
				}
			}
			$where .= "\n )";
		}
		//NEW MUSLTICATS
		
	}
	
	$query = "SELECT a.id as value, a.course_name as text"
	. "\n FROM #__lms_courses as a"
	. "\n WHERE a.id > 0"
	. "\n $where"
//	.($course_id ? "\n AND a.course_id = '".$course_id."'" : '')
	;
	$JLMS_DB->setQuery($query);
	$courses = $JLMS_DB->loadObjectList();
	
//	echo '<pre>';
//	print_r($courses);
//	echo '</pre>';
	
	$courses_ids = array();
	foreach($courses as $course){
		$courses_ids[] = $course->value;	
	}
	$str_courses_ids = implode(",", $courses_ids);
	
	$f_courses = array();
	$f_courses[] = mosHTML::makeOption(0, '&nbsp;');
	$f_courses = array_merge($f_courses, $courses);
	$lists['f_course'] = mosHTML::selectList( $f_courses, 'course_id', 'class="inputbox" size="1" style="width: 100%;" '. $javascript, 'value', 'text', $course_id); 
	
	$query = "SELECT a.*, b.course_name "
	. "\n FROM #__lms_page_notices as a"
	. "\n LEFT JOIN #__lms_courses as b ON a.course_id = b.id"
	. "\n WHERE a.usr_id = '".$my->id."'"
	.($level_id[0] ? "\n AND a.course_id IN (".$str_courses_ids.")" : '')
	.($course_id ? "\n AND a.course_id = '".$course_id."'" : '')
	. "\n ORDER BY a.data DESC"
	;
	$JLMS_DB->setQuery($query);
	$lists['my_notices'] = $JLMS_DB->loadObjectList();
	
	$total 		= count($lists['my_notices']);
	require_once(_JOOMLMS_FRONT_HOME . DS . "includes" . DS . "classes" . DS . "lms.pagination.php");
	$pageNav = new JLMSPageNav( $total, $limitstart, $limit );
	
	FLMS_page_notice::show_all_notices($option, $lists, $total, $pageNav, $limitstart, $limit);
}
?>