<?php
/**
* joomla_lms.func.php
* (c) JoomaLMS eLearning Software http://www.joomlalms.com/
**/

defined( '_JLMS_EXEC' ) or die( 'Restricted access' );

define('_JLMS_HOME_NOTICES', 'My Notices');

function JLMS_process_menu_for_guests($option){
	global $JLMS_DB, $JLMS_CONFIG, $Itemid;
	$query	= "SELECT * FROM `#__lms_menu` "
			." WHERE user_access = -1 AND published = 1 ORDER BY ordering ";
	$JLMS_DB->setQuery($query);
	$menus = $JLMS_DB->loadObjectList();
	$row = array();
	
	foreach ($menus as $menu){
		if ($menu->lang_var == '_JLMS_TOOLBAR_SUBSCRIPTIONS' && !$JLMS_CONFIG->get('guest_access_subscriptions', 1)) {
			continue;
		}
		$item = new stdClass();
		$item->id = $menu->id;
		$item->display_mod = 0;
		$item->task = $menu->task;
		$item->target = '';
		
		$item->is_separator = $menu->is_separator;
		$item->image = $menu->image;
		$item->lang_var = $menu->lang_var;
		
		$task = '';
		if ($menu->task){
			$task = "&amp;task=".$menu->task;
		}
		$ids = '';
		$item->menulink = sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid".$task.$ids);
		$row[] = $item;
	}
	if (count($row) == 1) {
		$row = array();
	}
	$JLMS_CONFIG->set('jlms_menu', $row);
	
}
function JLMS_showCourseGuest( $id, $option, $enrollment = false ) {
	$JLMS_CONFIG = & JLMSFactory::getConfig();
	$Itemid = $JLMS_CONFIG->get('Itemid');

	$db = & JFactory::getDbo();
	$doc = & JFactory::getDocument();

	$restricted_courses = JLMS_illegal_courses_guest();

	if(in_array($id,$restricted_courses)) {
		JLMSRedirect(sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=courses"));
	}

	$query = "SELECT a.* FROM `#__lms_courses` as a WHERE a.id = '".$id."'"
	. "\n AND ( a.published = 1"
	. ($JLMS_CONFIG->get('show_future_courses', false) ? '' : "\n AND ( ((a.publish_start = 1) AND (a.start_date <= '".date('Y-m-d')."')) OR (a.publish_start = 0) )" )
	. "\n AND ( ((a.publish_end = 1) AND (a.end_date >= '".date('Y-m-d')."')) OR (a.publish_end = 0) )"
	. "\n )";
	;
	$db->SetQuery( $query );
	$row = $db->LoadObject();
	if ( is_object($row) && isset($row->id) ) {		
		$doc->setMetaData( 'description', $row->metadesc );
		$doc->setMetaData( 'keywords', $row->metakeys );
		JLMS_ShowHeading($JLMS_CONFIG->get('jlms_heading'), false);
		joomla_lms_html::showCourseGuest( $id, $row, $option, $enrollment );
	} else {
		JLMSRedirect(sefRelToAbs("index.php?option=$option&Itemid=$Itemid"));
	}
}
//}
function JLMS_showCoursesForGuest( $option, $enrollment = false ) {
	global $JLMS_DB, $JLMS_CONFIG, $JLMS_SESSION, $Itemid;
	$app = & JFactory::getApplication();

	$filter_groups = intval( mosGetParam( $_REQUEST, 'groups_course', 0 ) );
	$limit		= intval( mosGetParam( $_GET, 'limit', $JLMS_SESSION->get('list_limit',$JLMS_CONFIG->get('list_limit')) ) );
	$JLMS_SESSION->set('list_limit', $limit);
	$limitstart = intval( mosGetParam( $_GET, 'limitstart', 0 ) );

	$lists = array();
	/*Courses Blog*/
	$menu_params = '';
	if($Itemid){
		$query = "SELECT params FROM #__menu WHERE id = '".$Itemid."'";
		$JLMS_DB->setQuery($query);
		$menu_params = $JLMS_DB->loadResult();		
	}	

	$menu_params = new JLMSParameters($menu_params);

	if($Itemid){
		$lists['menu_params'] = $menu_params;
	}		
	/*Courses Blog*/

	if ( $JLMS_CONFIG->get('meta_desc') ) {
		$doc = & JFactory::getDocument();
		$doc->setMetaData( 'description', $JLMS_CONFIG->get('meta_desc') );
	}
	if ( $JLMS_CONFIG->get('meta_keys') ) {
		$doc = & JFactory::getDocument();
		$doc->setMetaData( 'keywords', $JLMS_CONFIG->get('meta_keys') );
	}

	$query = "SELECT * FROM `#__lms_course_cats` WHERE `parent` = '0' AND `restricted` = 0 ORDER BY c_category";

	$JLMS_DB->setQuery($query);
	$groups = $JLMS_DB->loadObjectList();
	$type_g[] = mosHTML::makeOption( 0, _JLMS_COURSES_ALL_CATEGORIES );
	$i = 1;
	foreach ($groups as $group){
		$type_g[] = mosHTML::makeOption( $group->id, $group->c_category );
		$i ++;
	}
	$link = "index.php?option=$option&amp;Itemid=$Itemid&amp;task=courses";
	$link = $link ."&amp;groups_course='+this.options[selectedIndex].value+'";
	$link = sefRelToAbs($link);
	$link = str_replace('%5C%27',"'", $link);$link = str_replace('%5B',"[", $link);$link = str_replace('%5D',"]", $link);$link = str_replace('%20',"+", $link);$link = str_replace("\\\\\\","", $link);$link = str_replace('%27',"'", $link);
	$lists['groups_course'] = mosHTML::selectList($type_g, 'groups_course', 'class="inputbox" size="1" onchange="document.location.href=\''.$link.'\';"', 'value', 'text', $filter_groups );

	//FLMS multicat
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
		$lists['levels'] = $levels;

		$level_id = array();
		for($i=0;$i<count($levels);$i++){
			if(isset($_REQUEST['category_filter']) && $_REQUEST['category_filter']){
				if($i == 0){
					$level_id[$i] = $_REQUEST['category_filter'];
					$parent_id[$i] = 0;
				} else {
					$level_id[$i] = 0;	
					$parent_id[$i] = $level_id[$i-1];
				}
			} else {
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
		}

		for($i=0;$i<count($levels);$i++){
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

		$javascript = 'onclick="javascript:read_filter();" onchange="javascript:write_filter();document.adminForm.task.value=\'courses\';document.adminForm.submit();"';

		for($i=0;$i<count($levels);$i++) {
			if($i == 0 || $parent_id[$i]){ //(Max): extra requests
				if( $parent_id[$i] == 0) {
					$query = "SELECT * FROM `#__lms_course_cats` WHERE `parent` = '0' AND `restricted` = 0";
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
					$lists['filter_'.$i.''] = mosHTML::selectList($type_level[$i], 'filter_id_'.$i.'', 'class="inputbox" size="1" style="width: 266px;" '.$javascript, 'value', 'text', $level_id[$i] ); //onchange="document.location.href=\''. $link_multi .'\';"
				} elseif($i == 0) {
					$type_level[$i][] = mosHTML::makeOption( 0, ' &nbsp; ' );
					foreach ($groups as $group){
						$type_level[$i][] = mosHTML::makeOption( $group->id, $group->c_category );
					}
					$lists['filter_'.$i.''] = mosHTML::selectList($type_level[$i], 'filter_id_'.$i.'', 'class="inputbox" size="1" style="width: 266px;" '.$javascript, 'value', 'text', $level_id[$i] ); //onchange="document.location.href=\''. $link_multi .'\';"
				}
			}
		}	
	}
	//FLMS multicat

	//FILTER
	$where = '';
	if($JLMS_CONFIG->get('multicat_use', 0)){
		//NEW MUSLTICATS
//		$tmp_level = array();
		$last_catid = 0;

		$tmp_cats_filter = JLMS_getFilterMulticategories($last_catid);
		/*
		if(isset($_REQUEST['category_filter']) && $_REQUEST['category_filter']){
			$last_catid = $_REQUEST['category_filter'];
		} else {
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
		
	} else {
		if ($filter_groups){
			if ($JLMS_CONFIG->get('sec_cat_use', 0)) {
				$where .= " AND (a.cat_id = '$filter_groups' OR a.sec_cat LIKE '%|$filter_groups|%') ";
			} else {
				$where .= " AND a.cat_id = '$filter_groups' ";
			}
		}
	}

	$show_paid_courses = $JLMS_CONFIG->get('show_paid_courses', 1);
	$where .= $show_paid_courses ? '' : ' AND a.paid <> 1 ';

	$restricted_courses = JLMS_illegal_courses_guest();
	$restricted_courses = implode(',', $restricted_courses);
	if($restricted_courses == '') {
		$restricted_courses = "''";
	}

	$query = "SELECT a.*, b.username, b.email, b.name as user_fullname, a.id as course_id, d.c_category  FROM `#__lms_courses` as a "
	. "\n LEFT JOIN `#__lms_course_cats` as d ON d.id = a.cat_id "
	. "\n ,`#__users` as b "
	. "\n WHERE a.owner_id = b.id"
	. "\n AND ( a.published = 1"
	. ( $JLMS_CONFIG->get('show_future_courses', false) ? '' : "\n AND ( ((a.publish_start = 1) AND (a.start_date <= '".date('Y-m-d')."')) OR (a.publish_start = 0) )" )
	. "\n AND ( ((a.publish_end = 1) AND (a.end_date >= '".date('Y-m-d')."')) OR (a.publish_end = 0) )"
	. "\n )"
	. "\n AND a.id NOT IN ($restricted_courses)"
	. "\n AND a.gid = 0"
	. "\n $where "
	. "\n ORDER BY ".($JLMS_CONFIG->get('lms_courses_sortby',0)?"a.ordering, a.course_name, a.id":"a.course_name, a.ordering, a.id")
	;
	$JLMS_DB->setQuery( $query );
	$JLMS_DB->query();
	
	$total = $JLMS_DB->getNumRows();
	
	require_once(_JOOMLMS_FRONT_HOME . DS . "includes" . DS . "classes" . DS . "lms.pagination.php");
	$pageNav = new JLMSPageNav( $total, $limitstart, $limit );
	
//	$JLMS_DB->setQuery( $query, $pageNav->limitstart, $pageNav->limit );
	$JLMS_DB->setQuery( $query );
	$rows = $JLMS_DB->LoadObjectList();
	
	//Leading Courses - Blog parametrs
	if(strlen($menu_params->get('leading_courses', ''))){
		$leading_courses = array();
		
		$leading_courses = explode(',', $menu_params->get('leading_courses', ''));
		
		for($i=0;$i<count($rows);$i++){
			$rows[$i]->leading_course = 0;
			if(in_array($rows[$i]->id, $leading_courses)){
				$rows[$i]->leading_course = 1;
				$lists['leading_courses'] = 1;
			}
		}
		
		$ordering = 0;
		$i = 0;
		while ($i < count($rows)) {
			$j = $i + 1;
			while ($j < count($rows)) {
				if($rows[$j]->leading_course) {
					$temp = new stdClass();
					$rows[$i]->ordering = $j;
					$rows[$j]->ordering = $i;
					$temp = $rows[$j];
					$rows[$j] = $rows[$i];
					$rows[$i] = $temp;
					break;
				}
				$j ++;	
			}
			$i ++;	
		}
		
		$ordering = 0;
		$i = 0;
		while ($i < count($rows)) {
			if($rows[$i]->leading_course) {
				$j = $i + 1;
				while ($j < count($rows)) {
					if(isset($leading_courses[$i]) && $rows[$j]->id == $leading_courses[$i]) {
						$rows[$i]->ordering = $j;
						$rows[$j]->ordering = $i;
						$temp = new stdClass();
						$temp = $rows[$j];
						$rows[$j] = $rows[$i];
						$rows[$i] = $temp;
						break;
					}
					$j ++;	
				}
			}
			$i ++;	
		}
	}
	//Leading Courses - Blog parametrs
	
	$tmp_rows = $rows;
	$rows = array();
	for($i=$pageNav->limitstart;$i<($pageNav->limitstart + $pageNav->limit);$i++){
		if(isset($tmp_rows[$i]) && $tmp_rows[$i]->id){
			$rows[] = $tmp_rows[$i];
		}
	}
	
	//$lists = array();
	$lists['homepage_text'] = $JLMS_CONFIG->get('frontpage_text');
	JLMS_ShowHeading($JLMS_CONFIG->get('jlms_heading'), false);

	$lms_titles_cache = & JLMSFactory::getTitles();
	$lms_titles_cache->setArray('courses', $rows, 'id', 'course_name');

	if($menu_params->get('blog', 0)){
		joomla_lms_html::showCoursesForGuest_blog( $option, $lists, $rows, $pageNav, $enrollment );
	} else {
		joomla_lms_html::showCoursesForGuest( $option, $lists, $rows, $pageNav, $enrollment );
	}
}
function JLMS_illegal_courses_guest(){
	$db = & JFactory::getDbo();
	
	$info = JLMS_restricted_data();
	
	$illegal_array = array();
	for($i=0;$i<count($info);$i++) {
		if($info[$i]->restricted) {
			$is_resticted = true;
			if ($is_resticted) {
				$illegal_array[] = $info[$i]->course_id;
			}
		}
	}
	
	return $illegal_array;
}
function JLMS_disabledFunction($course_id) {
	global $option, $Itemid;
	$pathway = array();
	$pathway[] = array('name' => _JLMS_PATHWAY_HOME, 'link' => sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid"), 'is_home' => true);
	JLMSAppendPathWay($pathway);
	echo '<div class="joomlalms_sys_message">'._JLMS_EM_DISABLED_OPTION.'</div>';
}
function JLMS_SwitchUserType( $usertype, $option ) {
	global $my, $JLMS_SESSION, $JLMS_CONFIG;
	//if (JLMS_GetUserType_simple($my->id, true) == 1 || $usertype == 6) {
	if (($JLMS_CONFIG->get('main_usertype') == 1) || $usertype == 6) {
		$Itemid = $JLMS_CONFIG->get('Itemid');
		$course_id = $JLMS_CONFIG->get('course_id');
		$JLMS_SESSION->set('switch_usertype', $usertype);
		echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
		echo '<html xmlns="http://www.w3.org/1999/xhtml"><head>';
		echo '<title>'._JLMS_REDIRECTING.'</title>';
		$iso_site = 'charset=utf-8';
		echo '<meta http-equiv="Content-Type" content="text/html; '.$iso_site.'" />';
		echo '</head><body>';

		if ((isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'])) {
			if ($_SERVER['HTTP_REFERER'] == sefRelToAbs("index.php?option=".$option."&amp;Itemid=".$Itemid) ||
				$_SERVER['HTTP_REFERER'] == sefRelToAbs("index.php?option=".$option."&Itemid=".$Itemid) ||
				$_SERVER['HTTP_REFERER'] == ($JLMS_CONFIG->get('live_site') . "/index.php?option=".$option."&Itemid=".$Itemid) ||
				$_SERVER['HTTP_REFERER'] == str_replace('&amp;', '&', sefRelToAbs("index.php?option=".$option."&Itemid=".$Itemid))
					) {
				$jlms_task = $JLMS_SESSION->get('jlms_task', '');
				switch ($jlms_task) {
					case 'agenda':
					case 'documents':
					case 'learnpaths':
					case 'links':
					case 'quizzes':
					case 'dropbox':
					case 'attendance':
					case 'course_forum':
					case 'conference';
					case 'chat':
					case 'gradebook':
					case 'mailbox':
						$url_redirect_pre = str_replace('&amp;', '&', sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=$jlms_task&id=$course_id"));
					break;
					default:
						$url_redirect_pre = str_replace('&amp;', '&', sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=details_course&id=$course_id"));
					break;
				}
			} else {
				$url_redirect_pre = $_SERVER['HTTP_REFERER'];
			}
			$url_redirect = $url_redirect_pre;
			$js_redirect = "window.top.location.href = '".str_replace('&amp;', '&',$url_redirect_pre)."';";
		} else {
			$url_redirect = 'javascript:window.history.go(-1);';
			$js_redirect = 'window.history.go(-1);';
		}

		echo '<br /><br /><br /><br /><br /><center><table><tr><td align="left"><span style="alignment:left" id="r_span">'._JLMS_REDIRECTING.'</span></td></tr><tr><td align="center"><a href="'.$url_redirect.'">'._JLMS_CLICK_HERE_TO_REDIRECT.'</a></td></tr></table></center>';
		echo '
<script type="text/javascript">
<!--//--><![CDATA[//><!--
function jlms_redirect() {
window.status = "'._JLMS_REDIRECTING.'" + myvar;
document.getElementById("r_span").innerHTML = "'._JLMS_REDIRECTING.'" + myvar;
myvar = myvar + " .";
var timerID = setTimeout("jlms_redirect();", 100);
if (timeout > 0) { timeout -= 1; }
else {
	clearTimeout(timerID);
	window.status = "";
	'.$js_redirect.'
}
}
var myvar = "";
var timeout = 15;
jlms_redirect();
//--><!]]>
</script>';
		echo '</body></html>';
		JLMS_die();
	}
}
function JLMS_showTopPage( $option ) {
	JLMS_showMainPage_front( $option );
}
function JLMS_showSpecRegPage( $option, $course_id ) {
	global $my, $JLMS_DB, $JLMS_CONFIG, $Itemid;
	if ( $JLMS_CONFIG->get('course_spec_reg', 0) && ($JLMS_CONFIG->get('current_usertype') == 2) ) {

		$pathway = array();
		$pathway[] = array('name' => _JLMS_PATHWAY_HOME, 'link' => sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid"), 'is_home' => true);
		JLMSAppendPathWay($pathway);

		JLMS_ShowHeading($JLMS_CONFIG->get('jlms_heading'));
		$query = "SELECT tip_message FROM #__lms_page_tips WHERE tip_task = 'pre_enrollment'";
		$JLMS_DB->SetQuery($query);
		$tip = $JLMS_DB->LoadResult();
		JLMS_TMPL::RenderPageTip($tip);
		$JLMS_ACL = & JLMSFactory::getACL();
		$sr_role = intval($JLMS_ACL->GetRole(1));
		$query = "SELECT role_id, id, course_question, is_optional, default_answer FROM #__lms_spec_reg_questions WHERE course_id = $course_id AND (role_id = 0 OR role_id = $sr_role) ORDER BY role_id DESC, ordering";
		$JLMS_DB->SetQuery( $query );
		$sr_quests = $JLMS_DB->LoadObjectList();
		if (!empty($sr_quests)) {
			if ($JLMS_CONFIG->get('use_secure_enrollment', false) && $JLMS_CONFIG->get('secure_url') && !$JLMS_CONFIG->get('under_ssl', false)) {
				JLMSRedirect($JLMS_CONFIG->get('secure_url')."/index.php?option=com_joomla_lms&Itemid=$Itemid&task=spec_reg&id=$course_id");
			}
			require_once(_JOOMLMS_FRONT_HOME . '/includes/classes/lms.cb_join.php');
			$all_cb_f = JLMSCBJoin::get_Assocarray();
			foreach ($all_cb_f as $cbf) {
				$tstr = '#'.$cbf.'#';
				$ijk = 0;
				while ($ijk < count($sr_quests)) {
					$tmp = $sr_quests[$ijk]->default_answer;
					$first_pos = strpos( $tmp,$tstr);
					if ($first_pos !== false) {
						$c = JLMSCBJoin::getASSOC($cbf);
						$sr_quests[$ijk]->default_answer = str_replace($tstr, $c, $tmp);
					}
					$ijk ++;
				}
				
			}
			$sr_role = $sr_quests[0]->role_id;
			$sr_ids = array();
			$prepared_questions = array();
			foreach ($sr_quests as $srq) {
				if ($srq->role_id == $sr_role) {
					$sr_ids[] = $srq->id;
					$srq->is_answered = 0;
					$prepared_questions[] = $srq;
				}
			}
			if (!empty($sr_ids)) {
				$sr_idss = implode(',',$sr_ids);
				$query = "SELECT * FROM #__lms_spec_reg_answers WHERE course_id = $course_id AND user_id = $my->id AND role_id = $sr_role AND quest_id IN ($sr_idss)";
				$JLMS_DB->SetQuery( $query );
				$sr_answs = $JLMS_DB->LoadObjectList();
				foreach ($sr_answs as $sra) {
					$i = 0;
					while ($i < count($prepared_questions)) {
						if ($prepared_questions[$i]->id == $sra->quest_id && $prepared_questions[$i]->role_id == $sra->role_id) {
							$prepared_questions[$i]->default_answer = $sra->user_answer;
							if ($sra->user_answer) {
								$prepared_questions[$i]->is_answered = 1;
							}
						}
						$i ++;
					}
				}
			}
			joomla_lms_html::showSR_page( $option, $course_id, $prepared_questions);
		} else {
			JLMSRedirect(sefRelToAbs("index.php?option=$option&Itemid=$Itemid"));
		}
	} else {
		JLMSRedirect(sefRelToAbs("index.php?option=$option&Itemid=$Itemid"));
	}
}
function JLMS_SpecRegAnswer( $option, $course_id ) {
	global $my, $JLMS_DB, $JLMS_CONFIG, $Itemid;
	$can_access_course = true;
	if ( $JLMS_CONFIG->get('course_spec_reg', 0) ) {
		if ($JLMS_CONFIG->get('current_usertype') == 2) {
			/*$user_answer = strval(mosGetParam($_REQUEST, 'user_answer', ''));
			$user_answer = strip_tags($user_answer);
			if ($user_answer) {
				$query = "SELECT id, user_answer FROM #__lms_spec_reg_answers WHERE course_id = $course_id AND user_id = ".$my->id;
				$JLMS_DB->SetQuery( $query );
				// new query !
				$new_query = "INSERT INTO #__lms_spec_reg_answers (course_id, user_id, user_answer)"
				. "\n VALUES ( $course_id, ".$my->id.", ".$JLMS_DB->Quote($user_answer).")";
				$del_query = "DELETE FROM #__lms_spec_reg_answers WHERE course_id = $course_id AND user_id = ".$my->id;
				$old_answer = $JLMS_DB->LoadObject();
				if (is_object($old_answer)) {
					if (!$old_answer->user_answer) {
						$JLMS_DB->SetQuery( $del_query );
						$JLMS_DB->query();
						$JLMS_DB->SetQuery( $new_query );
						$JLMS_DB->query();
					}
				} else {
					$JLMS_DB->SetQuery( $new_query );
					$JLMS_DB->query();
				}
			}*/
			$JLMS_ACL = & JLMSFactory::getACL();
			$sr_role = intval($JLMS_ACL->GetRole(1));
			$query = "SELECT role_id, id, course_question, is_optional FROM #__lms_spec_reg_questions WHERE course_id = $course_id AND (role_id = 0 OR role_id = $sr_role) ORDER BY role_id DESC, ordering";
			$JLMS_DB->SetQuery( $query );
			$sr_quests = $JLMS_DB->LoadObjectList();
			if (!empty($sr_quests)) {
				$sr_role = intval($sr_quests[0]->role_id);
				$sr_ids = array();
				$prepared_questions = array();
				foreach ($sr_quests as $srq) {
					if ($srq->role_id == $sr_role) {
						$sr_ids[] = $srq->id;
						$prepared_questions[] = $srq;
					}
				}
				if (!empty($sr_ids)) {
					$ans_ids = array();
					$ua_ids = mosGetParam( $_REQUEST, 'user_answer_id', array() );
					$uas = mosGetParam( $_REQUEST, 'user_answer', array() );
					if (!empty($uas) && is_array($uas) && !empty($ua_ids) && is_array($ua_ids) && (count($ua_ids) == count($uas))) {
						$i = 0;
						while ($i < count($uas)) {
							if ($prepared_questions[$i]->id == intval($ua_ids[$i]) && intval($ua_ids[$i]) && ($prepared_questions[$i]->is_optional || (!$prepared_questions[$i]->is_optional && $uas[$i]))) {
								$query = "SELECT user_answer FROM #__lms_spec_reg_answers WHERE course_id = $course_id AND user_id = $my->id AND role_id = $sr_role AND quest_id = ".intval($ua_ids[$i]);
								$JLMS_DB->SetQuery( $query );
								$ur = $JLMS_DB->LoadResult();
								$gg = $JLMS_DB->query();
								$tt = $JLMS_DB->getNumRows($gg);
								if (!$ur) {
									if ($tt == 1) {
										$query = "DELETE FROM #__lms_spec_reg_answers WHERE course_id = $course_id AND user_id = $my->id AND role_id = $sr_role AND quest_id = ".intval($ua_ids[$i]);
										$JLMS_DB->SetQuery( $query );
										$JLMS_DB->query();
									}
									$query = "INSERT INTO #__lms_spec_reg_answers (course_id, user_id, role_id, quest_id, user_answer) VALUES ($course_id, $my->id, $sr_role, ".intval($ua_ids[$i]).", ".$JLMS_DB->quote($uas[$i]).")";
									$JLMS_DB->SetQuery( $query );
									$JLMS_DB->query();
								}
							}
							$i ++;
						}
					}
				}
			}
			
			
			$query = "SELECT a.* FROM #__lms_courses as a"
			. "\n WHERE a.id = $course_id";
			$JLMS_DB->SetQuery( $query );
			$course_settings = $JLMS_DB->LoadObject();
			if (isset($course_settings->id) && $course_settings->id) {
			//if require spec. registration - redirect to spec. registration.
				if ($course_settings->spec_reg) {
					if (!empty($sr_quests)) {
						$sr_role = $sr_quests[0]->role_id;
						$sr_ids = array();
						$sr_qq = array();
						foreach ($sr_quests as $srq) {
							if ($srq->role_id == $sr_role) {
								$sr_ids[] = $srq->id;
								$sr_qq[] = $srq;
							}
						}
						if (!empty($sr_ids)) {
							$sr_idss = implode(',',$sr_ids);
							$query = "SELECT * FROM #__lms_spec_reg_answers WHERE course_id = $course_id AND user_id = $my->id AND role_id = $sr_role AND quest_id IN ($sr_idss)";
							$JLMS_DB->SetQuery( $query );
							$sr_answs = $JLMS_DB->LoadObjectList();
							$do_redirect = false;
							foreach ($sr_qq as $srqq) {
								$is_found = false;
								foreach ($sr_answs as $sra) {
									if ($sra->quest_id == $srqq->id) {
										if (!$sra->user_answer) {
											if ($srqq->is_optional) {
												$is_found = true;
											}
										} else {
											$is_found = true;
										}
										break;
									}
								}
								if (!$is_found) {
									$do_redirect = true;
								}
							}
							if (count($sr_answs) != count($sr_ids) || ($do_redirect)) {
								$can_access_course = false;
							}
						}
					}
				}
			}
		}
	}
	if ($can_access_course && $JLMS_CONFIG->get('under_ssl') && $JLMS_CONFIG->get('real_live_site')) {
		$temp_href = $JLMS_CONFIG->get('real_live_site')."/index.php?option=$option&amp;Itemid=$Itemid&amp;task=details_course&id=$course_id";
	} elseif (!$can_access_course) {
		$temp_href = sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=spec_reg&id=$course_id");
	} else {
		$temp_href = sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=details_course&id=$course_id");
	}
	JLMSRedirect($temp_href);
}
function JLMS_showMainPage_front( $option ) {
	global $my, $JLMS_DB, $JLMS_CONFIG, $Itemid;

	$JLMS_ACL = & JLMSFactory::getACL();
	$assigned_groups_only = $JLMS_ACL->CheckPermissions('advanced', 'assigned_groups_only');

	$pathway = array();
	$pathway[] = array('name' => _JLMS_PATHWAY_HOME, 'link' => sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid"), 'is_home' => true);
	JLMSAppendPathWay($pathway);

	$hp_items = $JLMS_CONFIG->get('homepage_items');
	$lists = array();	
	//courses

	$cidsf = $JLMS_CONFIG->get('student_in_future_courses');

	$course_data_sf = array();
	$cidtxt = '0';
	if (!empty($cidsf)) {
		$cidsfxt = implode(',',$cidsf);
		$query = "SELECT a.*, '0' as user_course_role FROM #__lms_courses as a WHERE a.id IN ($cidsfxt) AND a.id NOT IN ($cidtxt)"
//		. "\n ORDER BY a.course_name"
		. "\n ORDER BY ".($JLMS_CONFIG->get('lms_courses_sortby',0)?"a.ordering, a.course_name, a.id":"a.course_name, a.ordering, a.id")
		;
		$JLMS_DB->SetQuery( $query );
		$course_data_sf = $JLMS_DB->LoadObjectList();
	}

	$my_courses_r = my_courses_r();

	if (count($my_courses_r) || count($cidsf)) {
	} else {
		JLMSRedirect(sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=courses"));
	}

	$c_ids = array();
	foreach($my_courses_r as $mcr) {
		$c_ids[] = $mcr->id;
	}
	$my_courses_r = array_merge($my_courses_r, $course_data_sf);
	if ($JLMS_CONFIG->get('frontpage_courses_tree', 0)) {
		$hp_items = 999999999;
	}
	$my_courses = array();
	$i = 0;
	while ($i < $hp_items && $i < count($my_courses_r)) {
		$my_courses[] = $my_courses_r[$i];
		$i ++;
	}

	if (!is_array( $c_ids )) { $c_ids = array(0); }
	$c_str = implode(',',$c_ids);

	if ($JLMS_CONFIG->get('frontpage_courses_tree', 0)) {
		//My_courses_tree
		$levellimit = 20;
		$query = "SELECT id, c_category as name, parent, 0 as is_course"
		. "\n FROM #__lms_course_cats ORDER BY c_category";
		$JLMS_DB->setQuery( $query );
		$cats = $JLMS_DB->loadObjectList();

		$query = "SELECT a.id as user_certificate, b.course_id"
		. "\n FROM #__lms_certificate_users as a, #__lms_users_in_groups as b"
		. "\n WHERE 1"
		. "\n AND a.user_id = '".$my->id."'"
		. "\n AND a.crt_option = '1'"
		. "\n AND b.user_id = a.user_id AND a.course_id = b.course_id"
		;
		$JLMS_DB->setQuery($query);
		$crtfs = $JLMS_DB->loadObjectList();

		$i=0;
		foreach($my_courses as $course){
			$my_courses[$i]->certificate = 0;
			foreach($crtfs as $crtf){
				if($course->id == $crtf->course_id && $crtf->user_certificate){
					$my_courses[$i]->certificate = 1;
					break;	
				}	
			}
			$i++;	
		}

		$children = array();
		foreach ($cats as $v ) {
			$pt = $v->parent;
			/** Joomla 1.6 compability { */						
			$v->parent_id = $v->parent;
			$v->title = $v->name;
			/** } Joomla 1.6 compability*/			
			$list = @$children[$pt] ? $children[$pt] : array();
			array_push( $list, $v );
			$children[$pt] = $list;
		}
		$cats = mosTreeRecurse( 0, '', array(), $children, max( 0, $levellimit-1 ) );

		$i = 0;
		$tmp_cats = array();
		foreach($cats as $data){
			$tmp_cats[$i] = $data;
			$i++;	
		}
		$cats = array();
		$cats = $tmp_cats;


		//NEW
		$tmp = array();
		$last_catid = 0;
		foreach($my_courses as $data){
			if($last_catid == 0 || $last_catid != $data->cat_id){
				$last_catid	= $data->cat_id;
			}
			$tmp[$last_catid][] = $data->id;
		}
		$for_not_empty = $tmp;
		//NEW

		$flms_course = array();
		if($JLMS_CONFIG->get('flms_integration', 0)){
			$query = "SELECT * FROM #__lmsf_courses";
			$JLMS_DB->setQuery($query);
			$flms_course = $JLMS_DB->loadObjectList();
		}

		$result_tree = array();
		$i = 0;
		while ($i < count($my_courses)) {
			$my_courses[$i]->c_id = $my_courses[$i]->id;
			$my_courses[$i]->id = -$my_courses[$i]->id;
			$i++;
		}

		foreach($my_courses as $course){
			$result_tree = new stdClass();
			$result_tree->c_id = $course->c_id;
			$result_tree->id = $course->id;
			$result_tree->parent = 0;
			$result_tree->name = $course->course_name;
			$result_tree->children = 0;
			$result_tree->is_course = 1;
			$result_tree->certificate = $course->certificate;
			$result_tree->ordering = $course->ordering;

			$position = 0;
			$i = 0;
			$level = 0;
			foreach($cats as $cat){
				if($course->cat_id == $cat->id && $cat->parent == 0){
					$position = $i;
					$result_tree->parent = $cat->id;
				}
//				if ($cat->parent && isset($tmp_courses_cats[$course->c_id]) && count($tmp_courses_cats[$course->c_id]) && in_array($cat->id, $tmp_courses_cats[$course->c_id])) {
				if ($cat->parent && isset($for_not_empty[$cat->id]) && count($for_not_empty[$cat->id]) && in_array($course->c_id, $for_not_empty[$cat->id])){
					$position = $i;
					$result_tree->parent = $cat->id;
				}
				$i++;
			}

			if ($position) {
				$new_cats = array();
				foreach($cats as $cat){
					$new_cats[] = $cat;
					if (count($new_cats) == $position + 1) { //+1
						$new_cats[] = $result_tree;
					}
				}
				$cats = $new_cats;
			} else {
				$cats[] = $result_tree;
			}
		}

		$i = 0;
		while ($i < count($cats)) {
			$j = $i + 1;
			if ($cats[$i]->is_course) {
				while ($j < count($cats)) {
					if(!$cats[$j]->is_course && $cats[$i]->parent == $cats[$j]->parent) {
						$temp = new stdClass();
						$temp = $cats[$j];
						$cats[$j] = $cats[$i];
						$cats[$i] = $temp;
						break;
					}
					$j ++;	
				}
			}
			$i ++;	
		}

		$i = 0;
		while ($i < count($cats)) {
			$j = $i + 1;
			if ($cats[$i]->is_course) {
				while ($j < count($cats)) {
					if($cats[$j]->is_course && $cats[$i]->parent == $cats[$j]->parent) {
						if($JLMS_CONFIG->get('lms_courses_sortby', 0)){
							if($cats[$j]->ordering < $cats[$i]->ordering){
								$temp = new stdClass();
								$temp = $cats[$j];
								$cats[$j] = $cats[$i];
								$cats[$i] = $temp;
							}
						} else {
							if(strcasecmp($cats[$j]->name, $cats[$i]->name) < 0){
								$temp = new stdClass();
								$temp = $cats[$j];
								$cats[$j] = $cats[$i];
								$cats[$i] = $temp;
							}
						} 
					}
					$j ++;	
				}
			}
			$i ++;	
		}

		$children = array();
		foreach ($cats as $v ) {
			$pt = $v->parent;
			/** Joomla 1.6 compability { */
			$v->parent_id = $v->parent;
			$v->title = $v->name;
			/** } Joomla 1.6 compability*/
			$list = @$children[$pt] ? $children[$pt] : array();
			array_push( $list, $v );
			$children[$pt] = $list;
		}
		$cats = mosTreeRecurse( 0, '', array(), $children, max( 0, $levellimit-1 ) );

		$tmp_cats_tmp = array();
		$i=0;
		foreach($cats as $cat){
			$tmp_cats_tmp[$i] = $cat;
			$i++;
		}
		$cats = $tmp_cats_tmp;

		foreach($cats as $key=>$data){
			$cats[$key]->exist_courses = 0;
			if($data->is_course){
				$catid = $data->parent;
				$i = $key;
				while( $i >= 0 ){
					if($cats[$i]->id == $catid){
						$cats[$i]->exist_courses = 1;
						$catid = $cats[$i]->parent;	
					}
					$i--;
				}
			}	
		}

		$tmp_cats = array();
		$i=0;
		$lists['keep_flesson'] = 0;
		$tmp_data = array();
		foreach($cats as $key=>$data){
			$tmp_data[$key] = $data;

			unset($tmp_data[$key]->treename);
			$tmp_data[$key]->folder_flag = 0;	
			if($data->children){
				$tmp_data[$key]->folder_flag = 1;	
			}
			$tmp_data[$key]->parent_id = $data->parent;
			$tmp_data[$key]->doc_name = $data->name;
			$tmp_data[$key]->ordering = $i;

			foreach($flms_course as $f_course){
				if($data->is_course && abs($data->id) == $f_course->course_id && $f_course->type_lesson == 2){
					if($f_course->like_theory){
						$f_course->pf_time = 0;
						$f_course->pm_time = $f_course->theory_duration_time;
					}
					$pf_time_h = str_pad(floor($f_course->pf_time/60), 2, "0", STR_PAD_LEFT);
					$pf_time_m = str_pad(($f_course->pf_time - $pf_time_h*60), 2, "0", STR_PAD_LEFT);
					$pf_time = $pf_time_h.':'.$pf_time_m;

					$pm_time_h = str_pad(floor($f_course->pm_time/60), 2, "0", STR_PAD_LEFT);
					$pm_time_m = str_pad(($f_course->pm_time - $pm_time_h*60), 2, "0", STR_PAD_LEFT);
					$pm_time = $pm_time_h.':'.$pm_time_m;

					$pre_total_time = $f_course->pf_time + $f_course->pm_time;

					$total_time_h = str_pad(floor($pre_total_time/60), 2, "0", STR_PAD_LEFT);
					$total_time_m = str_pad(($pre_total_time - $total_time_h*60), 2, "0", STR_PAD_LEFT);
					$total_time = $total_time_h.':'.$total_time_m;

					$tmp_data[$key]->lesson_type = $f_course->type_lesson;
					$tmp_data[$key]->pf_time = $pf_time;
					$tmp_data[$key]->pm_time = $pm_time;
					$tmp_data[$key]->total_time = $total_time;

					$lists['keep_flesson'] = 1;
				} else if($data->is_course && abs($data->id) == $f_course->course_id){
					$tmp_data[$key]->lesson_type = $f_course->type_lesson;
				}
			}

			if($JLMS_CONFIG->get('multicat_no_display_empty', 1)){
//				if($data->is_course == 0 && $data->children && isset($tmp_cats_courses[$data->id]) && count($tmp_cats_courses[$data->id]) ){
//				if($data->is_course == 0 && $data->children && $data->exist_courses ){
				if($data->is_course == 0 && $data->children && $data->exist_courses ){
					$tmp_cats[$i] = $tmp_data[$key];
					$i++;
				} else if($data->is_course && $data->children == 0) {
					$tmp_cats[$i] = $tmp_data[$key];
					$i++;	
				}
			} else {
				$tmp_cats[$i] = $tmp_data[$key];
				$i++;
			}
		}
		$cats = $tmp_cats;
		$cats = JLMS_GetTreeStructure($cats);

		$my_courses = $cats;
		$hp_items = $JLMS_CONFIG->get('homepage_items');
	}
	//notices teacher
	if($JLMS_CONFIG->get('frontpage_notices_teacher')){
		$lists['my_notices'] = array();

		$hp_items = $JLMS_CONFIG->get('homepage_items');
		$query = "SELECT * "
		. "\n FROM #__lms_page_notices"
		. "\n WHERE usr_id = '".$my->id."'"
		. "\n ORDER BY data DESC"
		. "\n LIMIT 0,$hp_items"
		;
		$JLMS_DB->setQuery($query);
		$lists['my_notices'] = $JLMS_DB->loadObjectList();
	}

	//dropbox	
	$my_dropbox = array();
	if ($JLMS_CONFIG->get('frontpage_dropbox')) {
		$my_dropbox = my_dropboxes($c_str, $hp_items, $my->id);		
	}

	$lists['dropbox_total'] = my_dropboxes_total($c_str, $my->id);
	$lists['dropbox_total_new'] = my_dropboxes_total_new($c_str, $my->id);

	//homework
	$my_hw = array();
	if ($JLMS_CONFIG->get('frontpage_homework')) {

		$my_hw = my_homeworks($c_str, $hp_items, $my->id);	

//		$members = "'0'";
//		$where = '';
//		if($JLMS_ACL->_role_type == 2 || $JLMS_ACL->_role_type == 3 || $JLMS_ACL->_role_type == 4) {
//			if($assigned_groups_only) {
//				$groups_where_admin_manager = JLMS_ACL_HELPER::GetAssignedGroups($my->id);
//				$groups_where_isset_user = JLMS_ACL_HELPER::GetUserGlobalGroup($my->id);
//				
//				$groups_where_admin_manager = array_merge($groups_where_admin_manager,$groups_where_isset_user);
//				
//				if (count($groups_where_admin_manager)) {
//					$where .= "\n AND ( `groups` LIKE '%|$groups_where_admin_manager[0]|%'";
//					for($i=1;$i<count($groups_where_admin_manager);$i++) {
//						$where .= "\n OR `groups` like '%|$groups_where_admin_manager[$i]|%'";
//					}
//					$where .=  "\n )";
//				}
//			}
//		}
//		elseif($JLMS_ACL->_role_type < 2) {
//				$query = "select a.group_id FROM #__lms_users_in_global_groups as a WHERE a.user_id = '".$my->id."' AND a.subgroup1_id = 0";
//				$JLMS_DB->setQuery($query);
//				$temp1 = $JLMS_DB->loadResultArray();
//	
//				$query = "select subgroup1_id FROM #__lms_users_in_global_groups WHERE user_id = '".$my->id."'";
//				$JLMS_DB->setQuery($query);
//				$temp2 = $JLMS_DB->loadResultArray();
//	
//				$group_where_isset_user = array_merge($temp1,$temp2);
//				
//				if (count($group_where_isset_user)) {
//					$where .= "\n AND (( `groups` <> '' AND `groups` IS NOT NULL AND (`groups` LIKE '%|$group_where_isset_user[0]|%'";
//					for($i=1;$i<count($group_where_isset_user);$i++) {
//						$where .= "\n OR `groups` like '%|$group_where_isset_user[$i]|%'";
//					}
//					$where .=  "\n )) OR (`groups` = '' OR `groups` IS NULL))";
//				}					
//		}	
//		
//		
//		$query = "SELECT a.*, b.course_name, d.hw_status, c.user_id as stu_id, e.user_id as teach_id"
//		. "\n FROM (#__lms_homework as a, #__lms_courses as b)"
//		. "\n LEFT JOIN #__lms_users_in_groups as c ON c.user_id = '".$my->id."' AND c.course_id = b.id"
//		. "\n LEFT JOIN #__lms_homework_results as d ON d.course_id = b.id AND d.user_id = c.user_id AND d.hw_id = a.id"
//		. "\n LEFT JOIN #__lms_user_courses as e ON e.course_id = b.id AND e.user_id = '".$my->id."'"
//		. "\n WHERE a.course_id IN ($c_str) AND a.course_id = b.id AND a.end_date >= '".date('Y-m-d',time())."'"
//		.$where
//		. "\n LIMIT 0, $hp_items"
//		;
//		$JLMS_DB->SetQuery( $query );
//		$my_hw = $JLMS_DB->LoadObjectList();
	}
	//announcements
	$my_announcements = array();
	if ($JLMS_CONFIG->get('frontpage_announcements')) {
		$my_announcements = my_announcements ($c_str, $hp_items, $my->id);	
	}
	
	//mailbox
	$my_mailbox = array();
	if ($JLMS_CONFIG->get('frontpage_mailbox')) {
		$my_mailbox = my_mailbox($c_str, $hp_items, $my->id);	
	}
	
	//certificates
	$my_certificates = array();
	if ($JLMS_CONFIG->get('frontpage_certificates')) {
		$my_certificates = my_certificates($c_str, $hp_items, $my->id);	
	}
	
	//latest forum posts
	$latest_forum_posts = array();
	if ($JLMS_CONFIG->get('frontpage_latest_forum_posts')) {
		$latest_forum_posts = latest_forum_posts($c_str, $hp_items, $my->id);	
	}
	
	$lists['homepage_text'] = $JLMS_CONFIG->get('frontpage_text');
	//end
	JLMS_ShowHeading($JLMS_CONFIG->get('jlms_heading'), false);
	joomla_lms_html::showMainPage_front( $option, $lists, $my_courses, $my_dropbox, $my_hw, $my_announcements, $my_mailbox, $my_certificates, $latest_forum_posts);
}


//FLMS multicat
function JLMS_ShowTree($tree, $pid=0){
	if($pid == 0){
		echo "<ul id='mycourses_tree' class='treeview'>";
	} else {
		echo "<ul>";
	}
	foreach($tree as $id=>$root){
		if($pid!=$id){continue;}
		if(count($root)){
			foreach($root as $key => $title){
				if(isset($tree[$key]) && count($tree[$key])){
					echo "<li><span class='cursor'>".$title."</span>";
				} else {
					echo "<li>".$title;
				}
				if(isset($tree[$key]) && count($tree[$key])){
					JLMS_ShowTree($tree, $key);
				}
				echo "</li>";
			}
		}
	}
	echo "</ul>";
}


function JLMS_showCEOPage( $option ) {
	$db = & JFactory::getDbo();
	$user = JLMSFactory::getUser();
	$my_id = $user->get('id');
	$JLMS_CONFIG = & JLMSFactory::getConfig();
	$JLMS_SESSION = & JLMSFactory::getSession();

	$my_learners = array();
	$my_learners_1 = array();
	$my_learners_2 = array();
	
	$query = "SELECT a.*"
	. "\n, b.username, b.name, b.email"
	. "\n, d.course_name, d.id as course_id"
	. "\n FROM #__users as b, #__lms_user_parents as a"
	. "\n, #__lms_users_in_groups as c"
	. "\n, #__lms_courses as d"
	. "\n WHERE 1"
	. "\n AND a.user_id = b.id"
	. "\n AND a.user_id = c.user_id"
	. "\n AND c.course_id = d.id"
	. "\n AND a.parent_id = '".$my_id."'"
	. "\n ORDER BY b.username, b.name, d.course_name"
	;
	$db->SetQuery( $query );
	$my_learners_1 = $db->LoadObjectList();
	
//	echo $query;
//	echo '<pre>';
//	print_r($my_learners_1);
//	echo '</pre>';
	
	$query = "SELECT a.*"
	. "\n, b.username, b.name, b.email"
	. "\n, d.course_name, d.id as course_id"
	. "\n FROM #__users as b, #__lms_users_in_global_groups as a"
	. "\n, #__lms_users_in_groups as c"
	. "\n, #__lms_courses as d"
	. "\n, #__lms_user_assign_groups as e"
	. "\n WHERE 1"
	. "\n AND a.user_id = b.id"
	. "\n AND a.user_id = c.user_id"
	. "\n AND c.course_id = d.id"
	. "\n AND e.group_id = a.group_id"
	. "\n AND e.user_id = '".$my_id."'"
	. "\n ORDER BY b.username, b.name, d.course_name"
	;
	$db->SetQuery( $query );
	$my_learners_2 = $db->LoadObjectList();
	
//	echo $query;
//	echo '<pre>';
//	print_r($my_learners_2);
//	echo '</pre>';
	
	$exist_lrn_ids = array();
	$i=0;
	foreach($my_learners_2 as $my_lrn_2){
		$my_learners[] = $my_lrn_2;
		$exist_lrn_ids[] = $my_lrn_2->user_id;
	}
	foreach($my_learners_1 as $my_lrn_1){
		if(!in_array($my_lrn_1->user_id, $exist_lrn_ids)){
			$my_learners[] = $my_lrn_1;
			$exist_lrn_ids[] = $my_lrn_1->user_id;
		}
	}
	
//	echo '<pre>';
//	print_r($my_learners);
//	echo '</pre>';
	
	$lists = array();
	JLMS_ShowHeading($JLMS_CONFIG->get('jlms_heading'), false);

	//get list of all courses IDs
	$courses_ids = array();
	$user_ids = array();
	foreach ($my_learners as $my_learner) {
		if ($my_learner->course_id) {
			if (!in_array($my_learner->course_id, $courses_ids)) {
				$courses_ids[] = $my_learner->course_id;
			}
		}
		if ($my_learner->user_id) {
			if (!in_array($my_learner->user_id, $user_ids)) {
				$user_ids[] = $my_learner->user_id;
			}
		}
	}
	if (count($courses_ids) && count($user_ids)) {
		//check if any user has completed any course
		$user_ids_str = implode(',', $user_ids);
		$query = "SELECT * FROM #__lms_certificate_users WHERE user_id IN ($user_ids_str) AND crt_option = 1";
		$db->SetQuery( $query );
		$certificate_users = $db->LoadObjectList();
		if (count($certificate_users)) {
			//check if courses have certificates enabled and configured
			$courses_ids_str = implode(',', $courses_ids);
			$query = "SELECT * FROM #__lms_certificates WHERE course_id IN ($courses_ids_str) AND crtf_type = 1";
			$db->SetQuery( $query );
			$courses_certificates = $db->LoadObjectList();

			//populate list of CEO users with course completion information
			for ($i = 0, $n = count($my_learners); $i < $n; $i ++) {
				$my_learners[$i]->course_completion = 0;
				foreach ($certificate_users as $certificate_user) {
					if ($certificate_user->user_id == $my_learners[$i]->user_id && $certificate_user->course_id == $my_learners[$i]->course_id) {
						$my_learners[$i]->course_completion = 1;
						foreach ($courses_certificates as $course_certificate) {
							if ($course_certificate->course_id == $certificate_user->course_id) {
								$my_learners[$i]->course_completion = 2;
								break;
							}
						}
						break;
					}
				}
			}
		}
	}

	joomla_lms_html::showCEO_page( $option, $lists, $my_learners );
}

function JLMSmultiselect( $groups_arr = array(), $show_root = true, $course_id){
	global $my, $JLMS_DB;
	
	$JLMS_ACL = & JLMSFactory::getACL();
	$assigned_groups_only = $JLMS_ACL->CheckPermissions('advanced', 'assigned_groups_only');
	
	$members = "'0'";
	$groups_where_admin_manager = "'0'";
	if($assigned_groups_only && $JLMS_ACL->_role_type > 1) {
		$groups_where_admin_manager = JLMS_ACL_HELPER::GetAssignedGroups($my->id, $course_id);
		$groups_where_admin_manager = implode(',', $groups_where_admin_manager);
	}
	
	if($groups_where_admin_manager == '') {
		$groups_where_admin_manager = "'0'";
	}	
	
	$where = "\n AND course_id = 0";
	
	if($assigned_groups_only) {
		$where .= "\n AND id IN ($groups_where_admin_manager)";
	}

	// get a list of the menu items
	// excluding the current menu item and its child elements
	$query = "SELECT id, ug_name as name, parent_id as parent"
	. "\n FROM #__lms_usergroups"
	. "\n WHERE 1"
	. $where
	. "\n ORDER BY ug_name, parent_id"
	;
	$JLMS_DB->setQuery( $query );
	$mitems = $JLMS_DB->loadObjectList();

	//echo $JLMS_DB->geterrormsg();
	
	// establish the hierarchy of the menu
	$children = array();

	if ( $mitems ) {
		// first pass - collect children
		foreach ( $mitems as $v ) {
			$pt 	= $v->parent;
			/** Joomla 1.6 compability { */
			$v->parent_id = $v->parent;
			$v->title = $v->name;
			/** } Joomla 1.6 compability*/
			$list 	= @$children[$pt] ? $children[$pt] : array();
			array_push( $list, $v );
			$children[$pt] = $list;
		}
	}
	// second pass - get an indent list of the items
	$list = mosTreeRecurse( 0, '', array(), $children, 20, 0, 0 );
	
	//$javascript = ' onchange="javascript:view_fields(this,1);"';
	$javascript = '';
	// assemble menu items to the array
	$mitems 	= array();
//	if ($show_root) {
//		$mitems[] 	= mosHTML::makeOption( '0', 'Top' );
//	}

	$query = "SELECT group_id, subgroup1_id FROM #__lms_users_in_global_groups WHERE user_id IN ( SELECT user_id FROM #__lms_users_in_groups WHERE course_id = $course_id )";
	$JLMS_DB->setQuery( $query );
	$all_groups_in_course = $JLMS_DB->LoadObjectList();
	
	$rows = array();
	foreach ( $list as $item ) {
		$rows[] = mosHTML::makeOption( $item->id, ($show_root ? '&nbsp;&nbsp;&nbsp;' : ''). $item->treename );
	}
	
	// assemble menu items to the array
	$select_list = '<select class="text_area" style="width: 272px;" size="12" multiple="multiple" name="groups[]" id="restricted_groups">';
	for($i=0;$i<count($rows);$i++) {
		$selected = '';
		for($j=0;$j<count($groups_arr);$j++) {
			if($groups_arr[$j] == $rows[$i]->value)  {
				$selected = 'selected="selected"';	
				break;
			}			
		}
		
		$flag = 0;
		for($k=0;$k<count($all_groups_in_course);$k++) {
			if( ($rows[$i]->value == $all_groups_in_course[$k]->group_id) || ($rows[$i]->value == $all_groups_in_course[$k]->subgroup1_id) ) {
				$flag = 1;
				break;
			}
		}
		if($flag == 0) {
			$style = 'style="color: grey"';
		}
		else {
			$style = '';
		}
		
		$select_list .= '<option value="'.$rows[$i]->value.'" '.$selected.' '.$style.'>'.$rows[$i]->text.'</option>';
	}
	$select_list .= '</select>';
	
	//$output = mosHTML::selectList( $mitems, 'groups', 'class="text_area" size="12"'.(is_array($selected) ? ' multiple="multiple"' : '').' style="width: 272px;"'. $javascript, 'value', 'text', $selected );
	
	return $select_list; 	
}

function my_announcements ($course_id, $hp_items, $user_id) {
	global $JLMS_DB;
		if (!$course_id) {
			return array();
		}		
		$my_announcements = array();
		$JLMS_ACL = & JLMSFactory::getACL();
		$my_course_role = $JLMS_ACL->getMyCourseRole($course_id);
		$my_course_roletype = $JLMS_ACL->GetTypeofRole($my_course_role);
		$assigned_groups_only = JLMS_ACL_HELPER::checkPermissionsByRole($my_course_role, 'advanced', 'assigned_groups_only');
		$where = '';
						
		if($my_course_roletype == 2 || $my_course_roletype == 3 || $my_course_roletype == 4) {			
			if($assigned_groups_only) {
				$groups_where_admin_manager = JLMS_ACL_HELPER::GetAssignedGroups($user_id);
				$groups_where_isset_user = JLMS_ACL_HELPER::GetUserGlobalGroup($user_id);
				
				$groups_where_admin_manager = array_merge($groups_where_admin_manager,$groups_where_isset_user);
								
				if (count($groups_where_admin_manager)) {
					$where .= "\n AND (is_limited = 0 OR `groups` LIKE '%|$groups_where_admin_manager[0]|%'";
					for($i=1;$i<count($groups_where_admin_manager);$i++) {
						$where .= "\n OR `groups` like '%|$groups_where_admin_manager[$i]|%'";
					}
					$where .=  "\n OR a.owner_id = '".$user_id."')";
				}
				else {
					$where .= "\n AND (is_limited = 0 OR a.owner_id = '".$user_id."' OR agenda_id = 0) AND groups = ''";
				}
			}
		}
		elseif($my_course_roletype < 2) {
				$query = "select a.group_id FROM #__lms_users_in_global_groups as a WHERE a.user_id = '".$user_id."' AND a.subgroup1_id = 0 AND a.group_id > 0";
				$JLMS_DB->setQuery($query);
				$temp1 = $JLMS_DB->loadResultArray();

				$query = "select subgroup1_id FROM #__lms_users_in_global_groups WHERE user_id = '".$user_id."' AND subgroup1_id > 0";
				$JLMS_DB->setQuery($query);
				$temp2 = $JLMS_DB->loadResultArray();

				$group_where_isset_user = array_merge($temp1,$temp2);
										
				if (count($group_where_isset_user)) {
					$where .= "\n AND (( `groups` <> '' AND `groups` IS NOT NULL AND (`groups` LIKE '%|$group_where_isset_user[0]|%'";
					for($i=1;$i<count($group_where_isset_user);$i++) {
						$where .= "\n OR `groups` like '%|$group_where_isset_user[$i]|%'";
					}
					$where .=  "\n )) OR (is_limited = 0 AND (`groups` = '' OR `groups` IS NULL)))";
				}					
		}
								
		$query = "SELECT a.* FROM `#__lms_agenda` as a, `#__lms_courses` as b"
		. "\n WHERE a.course_id = b.id AND a.start_date <= '".date('Y-m-d')."' AND a.end_date >='".date('Y-m-d')."'"
		.$where
		. "\n AND a.course_id IN ($course_id)" 
		. "\n ORDER BY a.start_date "
		."\n LIMIT 0, $hp_items";
		$JLMS_DB  -> setQuery($query);
		$my_announcements = $JLMS_DB->loadObjectList();
			
		if( $my_course_roletype < 2 ) 
		{	
			$my_announcements = filterByShowPeriod( $my_announcements );
		}	
							
		return $my_announcements;
}

function my_homeworks ($course_id, $hp_items, $user_id) {
		global $JLMS_DB; 	
		if (!$course_id) {
			return array();
		}
		$my_hw = array();
		$JLMS_ACL = & JLMSFactory::getACL();
		$assigned_groups_only = $JLMS_ACL->CheckPermissions('advanced', 'assigned_groups_only');

		$members = "'0'";
		$where = '';
			
		
		if($JLMS_ACL->_role_type == 2 || $JLMS_ACL->_role_type == 3 || $JLMS_ACL->_role_type == 4) {
			if($assigned_groups_only) {

				$groups_where_admin_manager = JLMS_ACL_HELPER::GetAssignedGroups($user_id);
				$groups_where_isset_user = JLMS_ACL_HELPER::GetUserGlobalGroup($user_id);
				
				$groups_where_admin_manager = array_merge($groups_where_admin_manager,$groups_where_isset_user);
				
				if (count($groups_where_admin_manager)) {
					$where .= "\n AND (is_limited = 0 OR `groups` LIKE '%|$groups_where_admin_manager[0]|%'";
					for($i=1;$i<count($groups_where_admin_manager);$i++) {
						$where .= "\n OR `groups` like '%|$groups_where_admin_manager[$i]|%'";
					}
					$where .=  "\n OR a.owner_id = '".$user_id."')";
				}
				else {
					$where .= "\n AND (is_limited = 0 OR a.owner_id = '".$user_id."' OR a.id = 0) AND groups = ''";
				}
			}
		} else
		if($JLMS_ACL->_role_type < 2) {
			$query = "select a.group_id FROM #__lms_users_in_global_groups as a WHERE a.user_id = '".$user_id."' AND a.subgroup1_id = 0 AND a.group_id > 0";
			$JLMS_DB->setQuery($query);
			$temp1 = $JLMS_DB->loadResultArray();

			$query = "select subgroup1_id FROM #__lms_users_in_global_groups WHERE user_id = '".$user_id."' AND subgroup1_id > 0";
			$JLMS_DB->setQuery($query);
			$temp2 = $JLMS_DB->loadResultArray();

			$group_where_isset_user = array_merge($temp1, $temp2);
			
			$where .= "\n AND (a.is_limited = 0";
			if(isset($group_where_isset_user) && count($group_where_isset_user)){
				$where .= "\n OR (a.is_limited = 1"
				. "\n AND (a.groups LIKE '%|$group_where_isset_user[0]|%'"
				;
				for($i=1;$i<count($group_where_isset_user);$i++){
					$where .= "\n OR a.groups like '%|$group_where_isset_user[$i]|%'";
				}
				$where .= "\n )))";
			} else {
				$where .= "\n )";
			}
		}	
				
		$query = "SELECT a.*, d.hw_status, c.user_id as stu_id, e.user_id as teach_id"
		. "\n FROM (#__lms_homework as a, #__lms_courses as b)"
		. "\n LEFT JOIN #__lms_users_in_groups as c ON c.user_id = '".$user_id."' AND c.course_id = b.id"
		. "\n LEFT JOIN #__lms_homework_results as d ON d.course_id = b.id AND d.user_id = ".$user_id." AND d.hw_id = a.id"
		. "\n LEFT JOIN #__lms_user_courses as e ON e.course_id = b.id AND e.user_id = '".$user_id."'"
		. "\n WHERE (ISNULL(d.hw_status) OR d.hw_status = 0)"
		. "\n AND a.course_id IN ($course_id)"
		. "\n AND a.course_id = b.id AND a.end_date >= '".date('Y-m-d',time())."'"
		.$where
		. "\n LIMIT 0, $hp_items"
		;
		$JLMS_DB->SetQuery( $query );
		$my_hw = $JLMS_DB->LoadObjectList();
				
		if( $JLMS_ACL->_role_type < 2 ) 
		{	
			$my_hw = filterByShowPeriod( $my_hw );
		}
		
		return $my_hw;	
}
 
function my_dropboxes($course_id, $hp_items, $user_id) 
{
	global $JLMS_DB;
	if (!$course_id) {
		return array();
	}
	$query = "SELECT a.*, b.course_name FROM #__lms_dropbox as a, #__lms_courses as b WHERE a.recv_id = '".$user_id."' AND a.drp_mark = 1 AND a.course_id = b.id AND b.id IN ($course_id) ORDER BY a.drp_time DESC LIMIT 0,$hp_items";
	$JLMS_DB->SetQuery( $query );
	$my_dropbox = $JLMS_DB->LoadObjectList();
	
	return $my_dropbox;	
}

function my_dropboxes_total($course_id, $user_id) 
{
	global $JLMS_DB;
	if (!$course_id) {
		return 0;
	}
	$query = "SELECT count(*) FROM #__lms_dropbox WHERE recv_id = '".$user_id."' AND course_id IN ($course_id)";
	$JLMS_DB->SetQuery( $query );
	$my_dropbox_total = $JLMS_DB->LoadResult();
	
	return $my_dropbox_total?$my_dropbox_total:0;	
}

function my_dropboxes_total_new($course_id, $user_id) 
{
	global $JLMS_DB;
	if (!$course_id) {
		return 0;
	}
	$query = "SELECT count(*) FROM #__lms_dropbox WHERE recv_id = '".$user_id."' AND drp_mark = 1 AND course_id IN ($course_id)";
	$JLMS_DB->SetQuery( $query );
	$my_dropbox_total_new = $JLMS_DB->LoadResult();
	
	return $my_dropbox_total_new?$my_dropbox_total_new:0;	
}

function my_courses_r() 
{
	global $JLMS_CONFIG, $JLMS_DB, $my;
		
	$cidt = $JLMS_CONFIG->get('teacher_in_courses');
	$cids = $JLMS_CONFIG->get('student_in_courses');
	$cidsf = $JLMS_CONFIG->get('student_in_future_courses');
	$cidtxt = '0';
	$course_data_t = array();
	$course_data_s = array();	
	if (!empty($cidt)) {
		$cidtxt = implode(',',$cidt);
		$query = "SELECT b.*, '1' as user_course_role FROM #__lms_courses as b WHERE b.id IN ($cidtxt)"
//		. "\n ORDER BY b.course_name"
		. "\n ORDER BY ".($JLMS_CONFIG->get('lms_courses_sortby',0)?"b.ordering, b.course_name, b.id":"b.course_name, b.ordering, b.id")
		;
		$JLMS_DB->SetQuery( $query );
		$course_data_t = $JLMS_DB->LoadObjectList();
	}
	if (!empty($cids)) {
		$cidsxt = implode(',',$cids);
		$query = "SELECT a.*, '0' as user_course_role FROM #__lms_courses as a WHERE a.id IN ($cidsxt) AND a.id NOT IN ($cidtxt)"
//		. "\n ORDER BY a.course_name"
		. "\n ORDER BY ".($JLMS_CONFIG->get('lms_courses_sortby',0)?"a.ordering, a.course_name, a.id":"a.course_name, a.ordering, a.id")
		;
		$JLMS_DB->SetQuery( $query );
		$course_data_s = $JLMS_DB->LoadObjectList();
	}	
	
	$my_courses_r = array_merge($course_data_t, $course_data_s);
	
	return $my_courses_r;	
}

function my_mailbox($course_id, $hp_items, $user_id){
	$db = & JFactory::getDbo();
	$user = JLMSFactory::getUser();	
	
	$query = "SELECT m.*, mt.*, u.username as from_username, u.name as from_name"
	. "\n FROM"
	. "\n #__lms_messages as m"
	. "\n, #__lms_messages_to as mt"
	. "\n, #__users as u"
	. "\n WHERE 1"
	. "\n AND m.id = mt.id"
	. "\n AND m.sender_id = u.id"
	.($course_id ? "\n AND m.course_id IN (".$course_id.")" : '')
	. "\n AND mt.user_id = '".$user_id."'"
	. "\n AND mt.del = 0"
	. "\n ORDER BY m.data DESC"
	;
	$db->setQuery($query, 0, $hp_items);
	$my_mailbox = $db->loadObjectList();
	
	return $my_mailbox;
}

function my_certificates($course_id, $hp_items, $user_id){
	$db = & JFactory::getDbo();
	$user = JLMSFactory::getUser();	
	
	$my_courses = array();
	$my_certificates = array();
	if($user_id){
		$query = "SELECT a.id, a.course_name"
		. "\n FROM #__lms_courses as a"
		. "\n, #__lms_users_in_groups as b"
		. "\n WHERE 1"
		. "\n AND a.id = b.course_id"
		. "\n AND b.user_id = '".$user_id."'"
		;
		$db->setQuery($query);
		$my_courses = $db->loadObjectList();
		
		$query = "SELECT cc.* FROM (
			SELECT a.*, c.course_name, d.uniq_id, d.crtf_id, d.quiz_id, d.role_id
			FROM #__lms_certificate_users as a
			LEFT JOIN #__lms_certificate_prints as d ON a.course_id = d.course_id AND a.user_id = d.user_id AND crtf_id 
			,#__lms_users_in_groups as b
			,#__lms_courses as c
			WHERE 1
			AND a.user_id = $user_id
			AND a.user_id = b.user_id
			AND a.course_id = b.course_id
			AND b.course_id = c.id
			ORDER BY d.role_id DESC
		) AS cc
		GROUP BY cc.user_id, cc.course_id, cc.crtf_id, cc.quiz_id
		ORDER BY cc.crt_date DESC
		";
		$db->setQuery($query);
		$my_certificates = $db->loadObjectList();
	}
	
	$lists = array();
	
	$JLMS_ACL = & JLMSFactory::getACL();
	
	if(count($my_courses)){
		foreach($my_courses as $course){
			//JLMS_GB_getUserCertificates($course->id, $user_id, $lists);
			
			/**
			 * Certificates MOD - 04.10.2007 (DEN)
			 * We will show the list of all achieved certificates in the User Gradebook
			 */
			$query = "SELECT * FROM #__lms_quiz_t_quiz WHERE course_id = '".$course->id."' AND c_certificate <> 0 ORDER BY c_title";
			$db->SetQuery( $query );
			$quiz_rows = $db->LoadObjectList();
		
			$p = array();
			foreach ($quiz_rows as $qrow) {
				$pp = new stdClass();
				$pp->gbi_id = $qrow->c_id;
				$pp->user_pts = 0;
				$pp->user_status = -1;
				$pp->quiz_name = '';
				$pp->crtf_id = '';
				$p[] = $pp;
			}
		
			$certificates = array();
			$quiz_ans = array();
			if (count($quiz_rows)) {
				$query = "SELECT a.*, b.c_full_score, b.c_title, b.c_certificate FROM #__lms_quiz_results as a, #__lms_quiz_t_quiz as b WHERE a.course_id = '".$course->id."'"
				. "\n AND a.quiz_id = b.c_id AND a.user_id = $user_id ORDER BY a.user_id, a.quiz_id";
				$db->SetQuery( $query );
				$quiz_ans = $db->LoadObjectList();
				
				$j = 0;
				while ($j < count($quiz_ans)) {
					if ($quiz_ans[$j]->user_id == $user_id) {
						$k = 0;
						while ($k < count($p)) {
							if ($p[$k]->gbi_id == $quiz_ans[$j]->quiz_id) {
								$p[$k]->user_pts = $quiz_ans[$j]->user_score;
								$p[$k]->user_status = $quiz_ans[$j]->user_passed;
								$p[$k]->quiz_name = $quiz_ans[$j]->c_title;
								$p[$k]->crtf_id = $quiz_ans[$j]->c_certificate;
							}
							$k ++;
						}
					}
					$j ++;
				}
		
				$certificates = array();
				foreach ($p as $pp) {
					if ($pp->user_status == 1) {
						$query = "SELECT * FROM #__lms_quiz_r_student_quiz WHERE c_quiz_id = $pp->gbi_id AND c_student_id = $user_id AND c_total_score = $pp->user_pts AND c_passed = 1 ORDER BY c_date_time DESC LIMIT 0,1";
						$db->SetQuery( $query );
						$u_res = $db->LoadObject();
						if (is_object($u_res)) {
							$role = $JLMS_ACL->UserRole($db, $user_id, 1);
							$query = "SELECT crtf_date as crt_date FROM #__lms_certificate_prints WHERE user_id = $user_id AND (role_id = $role OR role_id = 0)  AND course_id = $course->id AND quiz_id = $pp->gbi_id AND crtf_id = $pp->crtf_id"
							. "\n ORDER BY role_id DESC LIMIT 0,1";
							/* !!!!!!!! Bring from DB date of printing by user role or by default role (only if userrole not found) - imenno dlya etogo tut sidit ORDER i LIMIT*/
							$db->SetQuery( $query );
							$crtf_date = $db->LoadResult();
							if (!$crtf_date) {
								$crtf_date = $u_res->c_date_time;
							}
							$ppp = new stdClass();
							$ppp->user_id = $user_id;
							$ppp->stu_quiz_id = $u_res->c_id;
							$ppp->quiz_id = $u_res->c_quiz_id;
							$ppp->user_unique_id = $u_res->unique_id;
							$ppp->quiz_name = $pp->quiz_name;
							$ppp->crt_date = $crtf_date;
							$certificates[] = $ppp;
						}
					}
				}
			}
			$lists['user_quiz_certificates'] = & $certificates;
			/* END of Certificates MOD */

			if(isset($lists['user_quiz_certificates']) && count($lists['user_quiz_certificates'])){
				for($i=0;$i<count($lists['user_quiz_certificates']);$i++){
					$lists['user_quiz_certificates'][$i]->course_id = $course->id;
					$lists['user_quiz_certificates'][$i]->course_name = $course->course_name;
				}
			}
			$my_certificates = array_merge($my_certificates, $lists['user_quiz_certificates']);
		}
	}
	
	//sort
	$sort_date = array();
	for($i=0;$i<count($my_certificates); $i++){
		$sort_date[] = strtotime($my_certificates[$i]->crt_date);
	}
	arsort($sort_date);
	
	$tmp = array();
	foreach($sort_date as $n=>$item){
		foreach($my_certificates as $m=>$crt){
			if($n == $m){
				$tmp[] = $crt;
			}
		}
	}
	if(isset($tmp) && count($tmp)){
		$my_certificates = array();
		$my_certificates = $tmp;
	}
	
	return $my_certificates;
}

function latest_forum_posts($course_id, $hp_items, $user_id){
	
	$db = & JFactory::getDBO();
	
	$latest_forum_posts = array();
	
	if(strlen($course_id)){
		$query = "SELECT fd.*, c.course_name"
		. "\n FROM #__lms_forum_details as fd, #__lms_courses AS c"
		. "\n WHERE c.id = fd.course_id"
		. "\n AND fd.course_id IN (".$course_id.")"
		;
		$db->setQuery($query);
		$forum_details = $db->loadObjectList();
		
		$board_ids = array();
		foreach($forum_details as $fd){
			$board_ids[] = $fd->ID_BOARD;
		}
		
		$latest_forum_posts = array();
		$forum = & JLMS_SMF::getInstance();
		if(isset($forum->smf_db)){
			$latest_forum_posts = $forum->getLatestPosts($user_id, $hp_items, $board_ids);
		}
		
		if(count($latest_forum_posts)){
			for($i=0;$i<count($latest_forum_posts);$i++){
				foreach($forum_details as $fd){
					if($latest_forum_posts[$i]->id_board == $fd->ID_BOARD){
						$latest_forum_posts[$i]->course_id = $fd->course_id;
						$latest_forum_posts[$i]->course_name = $fd->course_name;
					}
				}
			}
		}
	}
			
	return $latest_forum_posts;
}
?>