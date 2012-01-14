<?php
/**
* admin.joomlaquiz.class.php
* (c) JoomaLMS eLearning Software http://www.joomlalms.com/
**/

defined( '_JLMS_EXEC' ) or die( 'Restricted access' );

define( 'SL_CATPREF', 'cat' );

define( 'MATCHING_DRAG_AND_DROP_IMAGES_ID', 11 );
define( 'MULTIPLE_IMAGES_CHOICE_ID', 12 );
define( 'MULTIPLE_IMAGES_RESPONSE_ID', 13 );

class JLMS_quiz_admin_class {
#######################################
###	--- --- CATEGORIES GQP	--- --- ###
function skippedQuestionIds() 
{
	return  array( MATCHING_DRAG_AND_DROP_IMAGES_ID, MULTIPLE_IMAGES_CHOICE_ID, MULTIPLE_IMAGES_RESPONSE_ID);
}

function JQ_ListCategoryGQP($option, $page, $id){
	global $JLMS_DB, $JLMS_SESSION, $JLMS_CONFIG;
	
	$rows = array();
	$lists = array();
	$pageNav = array();
	$is_pool = false;
	$gqp = true;
	
	$limit		= intval( mosGetParam( $_GET, 'limit', $JLMS_SESSION->get('list_limit', $JLMS_CONFIG->get('list_limit')) ) );
	$limitstart = intval( mosGetParam( $_GET, 'limitstart', 0 ) );
	$levellimit = intval( mosGetParam( $_REQUEST, 'levellimit', $JLMS_SESSION->get('GQP_levellimit', 10) ) );
	$JLMS_SESSION->set('GQP_levellimit', $levellimit);

	$query = "SELECT id, c_category as name, parent"
	. "\n FROM #__lms_gqp_cats"
	. "\n ORDER BY c_category"
	;
	$JLMS_DB->setQuery( $query );
	$rows = $JLMS_DB->loadObjectList();
	
	// establish the hierarchy of the menu
	$children = array();
	// first pass - collect children
	foreach ($rows as $v ) {
		$pt = $v->parent;
		/** Joomla 1.6 compability { */
		$v->parent_id = $v->parent;
		$v->title = $v->name;
		/** } Joomla 1.6 compability*/
		$list = @$children[$pt] ? $children[$pt] : array();
		array_push( $list, $v );
		$children[$pt] = $list;
	}
	
	// second pass - get an indent list of the items
	$list = mosTreeRecurse( 0, '', array(), $children, max( 0, $levellimit-1 ) );
	// eventually only pick out the searched items.

	$total = count( $list );

	require_once(_JOOMLMS_FRONT_HOME . DS . "includes" . DS . "classes" . DS . "lms.pagination.php");
	$pageNav = new JLMSPageNav( $total, $limitstart, $limit );

	$levellist = mosHTML::integerSelectList( 1, 20, 1, 'levellimit', 'size="1" onchange="document.adminForm.submit();"', $levellimit );
	
	// slice out elements based on limits
	$list = array_slice( $list, $pageNav->limitstart, $pageNav->limit );
	
	
	/*For update question delete gqp_cats_levels*/
	$query = "SELECT count(*) FROM #__lms_gqp_levels";
	$JLMS_DB->setQuery($query);
	$exist = $JLMS_DB->loadResult();
	
	$query = "SELECT * FROM #__lms_quiz_t_question WHERE course_id = 0 AND c_quiz_id = 0";
	$JLMS_DB->setQuery($query);
	$listQuestions = $JLMS_DB->loadObjectList();
	
	if($exist && isset($listQuestions) && count($listQuestions)){
		$status_check = true;
		foreach($listQuestions as $n=>$quest){
			$listLevels = array();
			
			$query = "SELECT * FROM #__lms_gqp_levels WHERE quest_id = '".$quest->c_id."'";	
			$JLMS_DB->setQuery($query);
			$listLevels = $JLMS_DB->loadObjectList();
			
			$tmp_last_cat_id = 0;
			$tmp_update = array();
			if(count($listLevels)){
				$tmp_level = 0;
				foreach($listLevels as $level){
					if($level->level > $tmp_level){
						$tmp_last_cat_id = $level->cat_id;
						$tmp_level = $level->level;
						$tmp_update[] = $level->quest_id;
					}
				}
			}
			
			$query = "UPDATE #__lms_quiz_t_question as qtq, #__lms_gqp_levels as l"
			. "\n SET qtq.c_qcat = '".$tmp_last_cat_id."'"
			. "\n WHERE 1"
			. "\n AND qtq.c_id = '".$quest->c_id."'"
			. "\n AND qtq.course_id = 0"
			. "\n AND qtq.c_quiz_id = 0"
			. "\n AND qtq.c_id = l.quest_id"
//			.(count($tmp_update) ? "\n AND qtq.c_id IN (".implode(",", $tmp_update).")" : '')
			;
			$JLMS_DB->setQuery($query);
			if($JLMS_DB->query()){
				$status_check = false;	
			}
		}
		if(!$status_check){
			$query = "DROP TABLE IF EXISTS #__lms_gqp_levels";	
			$JLMS_DB->setQuery($query);
			$JLMS_DB->query();
		}
	}
	/*For update question delete gqp_cats_levels*/
	
	JLMS_quiz_admin_html_class::JQ_showListCategoryGQP($list, $lists, $pageNav, $option, $page, $id, $is_pool, $gqp, $levellist);
}
function JQ_editCategoryGQP($uid, $option, $page, $course_id){
	global $JLMS_DB, $my;
	
	$menu = new mos_Joomla_LMS_GQPCategories( $JLMS_DB );
	$menu->load( (int)$uid );
	
	$menu->name = $menu->c_category;
	if(!isset($menu->lesson_type) || !$menu->lesson_type){
		$menu->lesson_type = 1;	
	}
	// build the html select list for paraent item
	$lists['parent'] = JLMS_quiz_admin_class::GQP_parent( $menu->id, $menu->parent );
	
	$list = array();
	
	$javascript = '';// onchange="javascript:view_fields(this,0);"';
	if($menu->parent){
		$disabled = 'disabled="disabled"';
	} else {
		$disabled = '';	
	}	
	
	$lists['restricted_category'] = mosHTML::yesnoRadioList( 'restricted', $disabled.'class="inputbox" id="restricted_radio"'. $javascript, $menu->restricted );

	$query = "SELECT groups FROM #__lms_gqp_cats WHERE id = '".$uid."'";
	$JLMS_DB->setQuery($query);
	$JLMS_DB->query();
	$groups = $JLMS_DB->loadResult();

	$groups = substr($groups,  1 ,strlen($groups)-2);

	$groups_arr = explode('|',$groups);
	
	$query = "SELECT * FROM #__lms_usergroups WHERE course_id = 0";
	$JLMS_DB->setQuery($query);
	$JLMS_DB->query();
	$rows = $JLMS_DB->loadObjectList();
	
	if($menu->restricted && !$menu->parent){
		$disabled = '';	
	} else {
		$disabled = 'disabled="disabled"';
	}
	
	// assemble menu items to the array
	$select_list = '<select '.$disabled.' class="inputbox" style="width: 272px;" size="12" multiple="multiple" name="groups[]" id="restricted_groups">';
	for($i=0;$i<count($rows);$i++) {
		$selected = '';
		for($j=0;$j<count($groups_arr);$j++) {
			if($groups_arr[$j] == $rows[$i]->id)  {
				$selected = 'selected="selected"';	
				break;
			}			
		}
		$select_list .= '<option value="'.$rows[$i]->id.'" '.$selected.'>'.$rows[$i]->ug_name.'</option>';
	}
	$select_list .= '</select>';

	$lists['restricted_groups'] = $select_list;

	JLMS_quiz_admin_html_class::JQ_showeditCategoryGQP($menu, $lists, $rows, $option);
}
function JQ_deleteCategoryGQP($cid=NULL, $option, $page, $course_id){
	global $JLMS_DB, $JLMS_CONFIG, $Itemid;
	
	$query = "SELECT * FROM #__lms_gqp_cats";
	$JLMS_DB->setQuery($query);
	$items = $JLMS_DB->loadObjectList();
	
	$deletes = array();
	
	$children = array();
	foreach ( $items as $item ) {
		if (isset($cid[0]) && $item->parent == $cid[0] ) {
			$children[] = $item->id;
		}		
	}
	$deletes = JLMS_quiz_admin_class::JLMS_ChildrenRecurse( $items, $children, $children );
	$deletes = array_merge( array($cid[0]), $deletes );
	
	$parent = 0;
	foreach($items as $item){
		if($item->id == $cid[0]){
			$parent = $item->parent;	
		}
	}
	
	if(count($deletes)){
		$deletes = implode(",", $deletes);
		$query = "UPDATE #__lms_quiz_t_question SET c_qcat = '".$parent."' WHERE c_qcat IN (".$deletes.") AND course_id = 0 AND c_quiz_id = 0";
		$JLMS_DB->setQuery($query);
		if($JLMS_DB->query()){
			$query = "DELETE FROM #__lms_gqp_cats WHERE id IN (".$deletes.")";
			$JLMS_DB->setQuery($query);
			$JLMS_DB->query();
		}
	}
	$msg = 'Category item Deleted';
	JLMSRedirect( sefRelToAbs('index.php?option='.$option.'&task=quizzes&page=category_gqp&Itemid='.$Itemid), $msg );

}
function JQ_saveCategoryGQP($option, $page, $course_id){
	global $JLMS_DB, $JLMS_CONFIG, $Itemid;
	
	$save_mass = array();
	$save_mass['id'] = intval(mosGetParam( $_REQUEST, 'c_id', 0));
	$save_mass['c_category'] = mosGetParam( $_REQUEST, 'name', '');
	$save_mass['parent'] = intval(mosGetParam( $_REQUEST, 'parent', 0));
	
	$save_mass['lesson_type'] = intval(mosGetParam( $_REQUEST, 'lesson_type', 0));
	$save_mass['restricted'] = intval(mosGetParam( $_REQUEST, 'restricted', 0));
	
	if($save_mass['id']){
		$query = "SELECT * FROM #__lms_gqp_cats ORDER BY parent";
		$JLMS_DB->setQuery($query);
		$all_cats = $JLMS_DB->loadObjectList();
		
		$last_catid = $save_mass['id'];
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
		$cids = implode(",", $tmp_cats_filter);
		
		$query = "UPDATE #__lms_gqp_cats SET lesson_type = '".$save_mass['lesson_type']."' WHERE id IN (".$cids.")";
		$JLMS_DB->setQuery($query);
		$JLMS_DB->query();
	}
	
	if($save_mass['parent'] && !$save_mass['lesson_type']){
		$query = "SELECT lesson_type FROM #__lms_gqp_cats WHERE id = '".$save_mass['parent']."'";
		$JLMS_DB->setQuery($query);
		$lesson_type_up_level = $JLMS_DB->loadResult();
		$save_mass['lesson_type'] = $lesson_type_up_level;
	}
	
	$groups 	= mosGetParam( $_REQUEST, 'groups', array(0) );
	$restricted_category = intval(mosGetParam( $_REQUEST, 'restricted', 0));
	
	$row = new mos_Joomla_LMS_GQPCategories( $JLMS_DB );

	if (!$row->bind( $save_mass )) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}

	$row->c_category = ampReplace( $row->c_category );
	
	if (!$row->check()) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}

	$groups_str = '';
	if ($restricted_category) {
		if(count($groups) && $groups[0] != 0) {
			$razd = '|';
			for($i=0;$i<count($groups);$i++) {
				$groups_str .= $razd.$groups[$i];
			}	
			$groups_str .= '|';
		}
	}

	$row->groups = $groups_str;
	
	if (!$JLMS_CONFIG->get('use_global_groups', 1) && $row->id){
		if($row->parent) {
			$row->restricted = '';
			$row->groups = '';
		}
		else {		
			unset($row->restricted);
			unset($row->groups);
		}
	}
	
	if (!$row->store()) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}
//	$row->checkin();
//	$row->updateOrder( 'menutype = ' . $JLMS_DB->Quote( $row->menutype ) . ' AND parent = ' . (int) $row->parent );

	$msg = 'Category item Saved';
	switch ( $page ) {
		case 'apply_category_gqp':
			mosRedirect( 'index.php?option='.$option.'&task=quizzes&page=edit_category_gqp&c_id='.$row->id.'&Itemid='.$Itemid , $msg );
			break;

		case 'save_category_gqp':
		default:
			mosRedirect( 'index.php?option='.$option.'&task=quizzes&page=category_gqp&Itemid='.$Itemid, $msg );
			break;
	}	
}

function JLMS_ChildrenRecurse( $mitems, $parents, $list, $maxlevel=20, $level=0 ) {
	// check to reduce recursive processing
	if ( $level <= $maxlevel && count( $parents ) ) {
		$children = array();
		foreach ( $parents as $id ) {			
			foreach ( $mitems as $item ) {
				if ( $item->parent == $id ) {
					$children[] = $item->id;
				}		
			}
		}	
		// check to reduce recursive processing
		if ( count( $children ) ) {
			$list = JLMS_quiz_admin_class::JLMS_ChildrenRecurse( $mitems, $children, $list, $maxlevel, $level+1 );
			$list = array_merge( $list, $children );
		}
	}
	return $list;
}
function GQP_parent($ex_id = 0, $selected = 0, $sb_name='parent', $show_root = true){
	global $JLMS_DB;

	$id = '';
	if ( $ex_id ) {
		$id = "\n AND id != " . (int) $ex_id;
	}

	// get a list of the menu items
	// excluding the current menu item and its child elements
	$query = "SELECT id, c_category as name, parent"
	. "\n FROM #__lms_gqp_cats"
	. "\n WHERE 1"
	. $id
	. "\n ORDER BY parent, c_category"
	;
	$JLMS_DB->setQuery( $query );
	$mitems = $JLMS_DB->loadObjectList();

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
	
	$javascript = '';// onchange="javascript:view_fields(this,1);"';
	// assemble menu items to the array
	$mitems 	= array();
	if ($show_root) {
		$mitems[] 	= mosHTML::makeOption( '0', 'Top' );
	}
	foreach ( $list as $item ) {
		$mitems[] = mosHTML::makeOption( $item->id, ($show_root ? '&nbsp;&nbsp;&nbsp;' : ''). $item->treename );
	}
	$output = mosHTML::selectList( $mitems, $sb_name, 'class="text_area" size="12"'.(is_array($selected) ? ' multiple="multiple"' : '').' style="width: 272px;"'. $javascript, 'value', 'text', $selected );

	return $output; 	
}
	
###################################
###	--- --- CATEGORIES	--- --- ###

function JQ_ListCategories( $option, $page, $id ) {
	global $JLMS_DB, $JLMS_SESSION, $JLMS_CONFIG;
	$limit		= intval( mosGetParam( $_GET, 'limit', $JLMS_SESSION->get('list_limit', $JLMS_CONFIG->get('list_limit')) ) );
	$limitstart = intval( mosGetParam( $_GET, 'limitstart', 0 ) );

	$query = "SELECT COUNT(*)"
	. "\n FROM #__lms_quiz_t_category WHERE course_id = '".$id."'";
	$JLMS_DB->setQuery( $query );
	$total = $JLMS_DB->loadResult();

	require_once(_JOOMLMS_FRONT_HOME . DS . "includes" . DS . "classes" . DS . "lms.pagination.php");
	$pageNav = new JLMSPageNav( $total, $limitstart, $limit );

	$query = "SELECT * "
	. "\n FROM #__lms_quiz_t_category WHERE course_id = '".$id."'"
	. "\n ORDER BY c_category"
	. "\n LIMIT $pageNav->limitstart, $pageNav->limit"
	;
	$JLMS_DB->setQuery( $query );
	$rows = $JLMS_DB->loadObjectList();

	JLMS_quiz_admin_html_class::JQ_showCatsList( $rows, $pageNav, $option, $page, $id );
}
function JQ_editCategory( $id, $option, $page, $course_id ) {	
	global $JLMS_DB, $my;
	$row = new mos_JoomQuiz_Cat( $JLMS_DB );
	$row->load( $id );	
	if (!$id) {
		$row->is_quiz_cat = 1;
	}
	$lists = array();
	JLMS_quiz_admin_html_class::JQ_editCategory( $row, $lists, $option, $page, $course_id );
}
function JQ_saveCategory( $option, $page, $course_id ) {
	global $JLMS_DB, $Itemid;
	$row = new mos_JoomQuiz_Cat( $JLMS_DB );
	if (!$row->bind( $_POST )) {
		exit();
	}
	$row->course_id = $course_id;
	$row->c_category = strval(JLMS_getParam_LowFilter($_POST, 'c_category', ''));
	$row->c_category = JLMS_Process_ContentNames($row->c_category);

	$row->c_instruction = strval(JLMS_getParam_LowFilter($_POST, 'c_instruction', ''));
	$row->c_instruction = JLMS_ProcessText_HardFilter($row->c_instruction);
	if (!$row->check()) {
		exit();
	}
	if (!$row->store()) {
		exit();
	}
	if ($page == 'apply_cat') {
		JLMSRedirect( sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=quizzes&id=$course_id&page=editA_cat&c_id=". $row->c_id) );
	} else {
		JLMSRedirect( sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=quizzes&id=$course_id&page=cats") );
	}
}
function JQ_removeCategory( &$cid, $option, $page, $id ) {
	global $JLMS_DB, $Itemid;
	if (count( $cid )) {
		$cids = implode( ',', $cid );
		$query = "DELETE FROM #__lms_quiz_t_category"
		. "\n WHERE c_id IN ( $cids ) AND course_id = '".$id."'";
		$JLMS_DB->setQuery( $query );
		if (!$JLMS_DB->query()) {
			echo "<script> alert('".$JLMS_DB->getErrorMsg()."'); window.history.go(-1); </script>\n";
		}
	}
	JLMSRedirect( sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=quizzes&id=$id&page=cats") );
}
function JQ_cancelCategory($option, $page, $id) {
	global $Itemid;
	JLMSRedirect(sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=quizzes&id=$id&page=cats"));
}
			#######################################
			###	--- --- 	QUIZZES 	--- --- ###
function JQ_Calculate_Quiz_totalScore($qid) {
	global $JLMS_DB;
	if ($qid) {
		$query = "SELECT SUM(c_point) FROM #__lms_quiz_t_question WHERE c_quiz_id = $qid";
		$JLMS_DB->SetQuery( $query );
		$total_score = $JLMS_DB->LoadResult();
		$query = "SELECT c_pool FROM #__lms_quiz_t_question WHERE c_quiz_id = $qid";
		$JLMS_DB->SetQuery($query);
		$ar = $JLMS_DB->LoadResultArray();
		if (!empty($ar)) {
			$arc = implode(',',$ar);
			$query = "SELECT SUM(c_point) FROM #__lms_quiz_t_question WHERE c_id IN ($arc)";
			$JLMS_DB->SetQuery( $query );
			$total_score_pool = $JLMS_DB->LoadResult();
			$total_score = $total_score + $total_score_pool;
		}
		$query = "UPDATE #__lms_quiz_t_quiz SET c_full_score = '".$total_score."' WHERE c_id = $qid";
		$JLMS_DB->SetQuery( $query );
		$JLMS_DB->query();
	}
}
function JQ_ListQuizzes( $option, $page, $course_id ) {
	global $JLMS_DB, $JLMS_SESSION, $JLMS_CONFIG;
	
	$cat_id = intval( mosGetParam( $_REQUEST, 'cat_id', $JLMS_SESSION->get('cat_id', 0 )) );
	$JLMS_SESSION->set('cat_id', $cat_id);
	 
	$limit = intval( mosGetParam( $_GET, 'limit', $JLMS_SESSION->get('list_limit', $JLMS_CONFIG->get('list_limit')) ) );
	$JLMS_SESSION->set('list_limit', $limit);
	$limitstart = intval( mosGetParam( $_REQUEST, 'limitstart', 0 ) );

	$query = "SELECT COUNT(*)"
	. "\n FROM #__lms_quiz_t_quiz"
	. "\n WHERE course_id = '".$course_id."'"
	. ( $cat_id ? "\n AND c_category_id = $cat_id" : '' )
	;
	$JLMS_DB->setQuery( $query );
	$total = $JLMS_DB->loadResult();

	require_once(_JOOMLMS_FRONT_HOME . DS . "includes" . DS . "classes" . DS . "lms.pagination.php");
	$pageNav = new JLMSPageNav( $total, $limitstart, $limit );

	$query = "SELECT a.*, b.c_category, u.name as author_name "
	. "\n FROM #__lms_quiz_t_quiz a LEFT JOIN #__lms_quiz_t_category b ON a.c_category_id = b.c_id AND b.course_id = '".$course_id."' AND b.is_quiz_cat = 1 LEFT JOIN #__users as u ON a.c_user_id = u.id"
	. "\n WHERE a.course_id = '".$course_id."'"
	. ( $cat_id ? "\n AND a.c_category_id = $cat_id" : '' )
	. "\n ORDER BY a.c_title, b.c_category"
	. "\n LIMIT $pageNav->limitstart, $pageNav->limit"
	;
	$JLMS_DB->setQuery( $query );
	$rows = $JLMS_DB->loadObjectList();

	$quizzes_i = array();
	foreach ($rows as $row) {
		$quizzes_i[] = $row->c_id;
	}
	$q_items_num = array();
	if (!empty($quizzes_i)) {
		$quizzes_i_cid = implode(',', $quizzes_i);
		$query = "SELECT sum(items_number) as items_count, quiz_id FROM #__lms_quiz_t_quiz_pool WHERE quiz_id IN ($quizzes_i_cid) GROUP BY quiz_id";
		$JLMS_DB->SetQuery($query);
		$q_items_num = $JLMS_DB->loadObjectList();
	}
	for ($i = 0, $n = count($rows); $i < $n; $i ++) {
		$rows[$i]->quests_from_pool = 0;
		foreach ($q_items_num as $qin) {
			if ($qin->quiz_id == $rows[$i]->c_id) {
				if ($qin->items_count) {
					$rows[$i]->quests_from_pool = $qin->items_count;
				}
				break;
			}
		}
	}

	$query = "SELECT count(*) FROM #__lms_quiz_t_question WHERE c_quiz_id = 0 and course_id = $course_id";
	$JLMS_DB->setQuery( $query );
	$pool_count = $JLMS_DB->loadResult();

	$javascript = 'onchange="document.adminForm.submit();"';
	$query = "SELECT c_id AS value, c_category AS text"
	. "\n FROM #__lms_quiz_t_category WHERE course_id = '".$course_id."' AND is_quiz_cat = 1"
	. "\n ORDER BY c_category"
	;
	$JLMS_DB->setQuery( $query );
	$categories[] = mosHTML::makeOption( '0', _JLMS_SB_SELECT_CATEGORY );
	$categories = array_merge( $categories, $JLMS_DB->loadObjectList() );
	$category = mosHTML::selectList( $categories, 'cat_id', 'class="inputbox" size="1" '. $javascript, 'value', 'text', $cat_id ); 

	$lists = array();
	$lists['pool_count'] = intval($pool_count);
	$lists['category'] = $category;

	$lms_titles_cache = & JLMSFactory::getTitles();
	$lms_titles_cache->setArray('quiz_t_quiz', $rows, 'c_id', 'c_title');

	JLMS_quiz_admin_html_class::JQ_showQuizList( $rows, $lists, $pageNav, $option, $page, $course_id );
}

function JQ_ListQuizzes_Stu( $option, $course_id ) {
	global $JLMS_DB, $JLMS_SESSION, $my, $JLMS_CONFIG;
	$JLMS_ACL = & JLMSFactory::getACL();

	$cat_id = intval( mosGetParam( $_REQUEST, 'cat_id', $JLMS_SESSION->get('cat_id', 0 )) );
	$JLMS_SESSION->set('cat_id', $cat_id);

	$limit = intval( mosGetParam( $_REQUEST, 'limit', $JLMS_SESSION->get('list_limit', $JLMS_CONFIG->get('list_limit')) ) );
	$JLMS_SESSION->set('list_limit', $limit);
	$limitstart = intval( mosGetParam( $_REQUEST, 'limitstart', 0 ) );
	
	$AND_ST = "";
	if( false !== ( $enroll_period = JLMS_getEnrolPeriod( $my->id, $course_id )) ) 
	{
		$AND_ST = " AND IF(a.is_time_related, (a.show_period < '".$enroll_period."' ), 1) ";	
	}

	$query = "SELECT COUNT(*)"
	. "\n FROM #__lms_quiz_t_quiz AS a"
	. "\n WHERE a. course_id = '".$course_id."'".$AND_ST
	. ( $JLMS_ACL->CheckPermissions('quizzes', 'view_all') ? '' : "\n AND a.published = 1" )
	. ( $cat_id ? "\n AND a.c_category_id = $cat_id" : '' )
	;
	$JLMS_DB->setQuery( $query );
	$total = $JLMS_DB->loadResult();

	require_once(_JOOMLMS_FRONT_HOME . DS . "includes" . DS . "classes" . DS . "lms.pagination.php");
	$pageNav = new JLMSPageNav( $total, $limitstart, $limit );

	$query = "SELECT a.*, b.c_category, u.name as author_name "
	. "\n FROM #__lms_quiz_t_quiz a LEFT JOIN #__lms_quiz_t_category b ON a.c_category_id = b.c_id AND b.course_id = '".$course_id."' AND b.is_quiz_cat = 1 LEFT JOIN #__users as u ON a.c_user_id = u.id"
	. "\n WHERE a.course_id = '".$course_id."'".$AND_ST
	. ( $JLMS_ACL->CheckPermissions('quizzes', 'view_all') ? '' : "\n AND a.published = 1" )
	. ( $cat_id ? "\n AND a.c_category_id = $cat_id" : '' )
	. "\n ORDER BY a.c_title, b.c_category"
	. "\n LIMIT $pageNav->limitstart, $pageNav->limit"
	;
	$JLMS_DB->setQuery( $query );
	$rows = $JLMS_DB->loadObjectList();	

	$quizzes_i = array();
	foreach ($rows as $row) {
		$quizzes_i[] = $row->c_id;
	}
	$q_items_num = array();
	if (!empty($quizzes_i)) {
		$quizzes_i_cid = implode(',', $quizzes_i);
		$query = "SELECT sum(items_number) as items_count, quiz_id FROM #__lms_quiz_t_quiz_pool WHERE quiz_id IN ($quizzes_i_cid) GROUP BY quiz_id";
		$JLMS_DB->SetQuery($query);
		$q_items_num = $JLMS_DB->loadObjectList();
	}
	for ($i = 0, $n = count($rows); $i < $n; $i ++) {
		$rows[$i]->quests_from_pool = 0;
		foreach ($q_items_num as $qin) {
			if ($qin->quiz_id == $rows[$i]->c_id) {
				if ($qin->items_count) {
					$rows[$i]->quests_from_pool = $qin->items_count;
				}
				break;
			}
		}
	}

	$javascript = 'onchange="document.adminForm.submit();"';
	$query = "SELECT c_id AS value, c_category AS text"
	. "\n FROM #__lms_quiz_t_category WHERE course_id = '".$course_id."' AND is_quiz_cat = 1"
	. "\n ORDER BY c_category"
	;
	$JLMS_DB->setQuery( $query );
	$categories[] = mosHTML::makeOption( '0', _JLMS_SB_SELECT_CATEGORY );
	$categories = array_merge( $categories, $JLMS_DB->loadObjectList() );
	$category = mosHTML::selectList( $categories, 'cat_id', 'class="inputbox" size="1" '. $javascript, 'value', 'text', $cat_id );
	$lists['category'] = $category;
	$lists['used_category_filter'] = $cat_id;

	$arr1 = array();
	if (!empty($rows)) {

		if (empty($rows)) {
			$quiz_ids = array(0);
		} else {
			$quiz_ids = array();
			foreach ($rows as $row1) {
				$quiz_ids[] = $row1->c_id;
			}
			$query = "SELECT * FROM #__lms_quiz_results WHERE quiz_id IN (".implode(',',$quiz_ids).") AND course_id='".$course_id."' AND user_id = '".$my->id."'";//  GROUP BY quiz_id";
			$JLMS_DB->SetQuery($query);
			$user_results = $JLMS_DB->loadObjectList();
			for($i=0;$i<count($rows);$i++) {
				$rows[$i]->user_passed = -1;
				$rows[$i]->quiz_max_score = 0;
				$rows[$i]->user_score = 0;
				foreach ($user_results as $user_result1) {
					if ($user_result1->course_id == $rows[$i]->course_id && $user_result1->quiz_id == $rows[$i]->c_id) {
						$rows[$i]->user_passed = $user_result1->user_passed;
						$rows[$i]->quiz_max_score = $user_result1->quiz_max_score;
						$rows[$i]->user_score = $user_result1->user_score;
					}
				}
			}

		}

		require_once(_JOOMLMS_FRONT_HOME . DS . "includes" . DS . "lms_certificates.php");
		$arr = array();
		JLMS_Certificates::JLMS_GB_getUserCertificates($course_id, $my->id, $arr);
		$arr1 = isset($arr['user_quiz_certificates']) ? $arr['user_quiz_certificates'] : array();
	}
	for($i=0; $i<count($rows);$i++) {
		for($j=0;$j<count($arr1);$j++) {
			if($arr1[$j]->c_quiz_id == $rows[$i]->c_id) {
				$rows[$i]->link_certificate = "<a class=\"jlms_img_link\" target = \"_blank\" href = \"".$JLMS_CONFIG->get('live_site')."/index.php?tmpl=component&option=".$option."&amp;no_html=1&amp;task=print_quiz_cert&amp;course_id=".$course_id."&amp;stu_quiz_id=".$arr1[$j]->stu_quiz_id."&amp;user_unique_id=".$arr1[$j]->user_unique_id."\"><img src = \"".$JLMS_CONFIG->get('live_site')."/components/com_joomla_lms/lms_images/toolbar/btn_certificate.png\" border = \"0\" align=\"top\" alt=\"certificate\"/></a>";
				$rows[$i]->user_score = $arr1[$j]->user_score;
				$rows[$i]->quiz_max_score = $arr1[$j]->quiz_max_score;
			}
		}
	}

	$lms_titles_cache = & JLMSFactory::getTitles();
	$lms_titles_cache->setArray('quiz_t_quiz', $rows, 'c_id', 'c_title');
	
//	echo '<pre>';
//	print_r($rows);
//	echo '</pre>';

	JLMS_quiz_admin_html_class::JQ_showQuizList_Stu( $rows, $lists, $pageNav, $option, $course_id );
}

function JQ_editQuiz( $id, $option, $page, $course_id ) {
	global $JLMS_DB, $my, $JLMS_CONFIG;
	
	$AND_ST = "";
	if( false !== ( $enroll_period = JLMS_getEnrolPeriod( $my->id, $course_id )) ) 
	{
		$AND_ST = " AND IF(is_time_related, (show_period < '".$enroll_period."' ), 1) ";	
	}
		
	$row = new mos_JoomQuiz_Quiz( $JLMS_DB );
	$row->addCond( $AND_ST );
	$row->load( $id );
	
	if ($id) {
		
	} 
	elseif(mosGetParam($_REQUEST,'flag')) {
		$row->bind( $_POST );
		$row->flag = mosGetParam($_REQUEST,'c_pool_type_gqp');
	}
	else {
		$row->c_user_id = $my->id;
		$JLMS_DB->SetQuery("Select name from #__users WHERE id = '".$my->id."'");
		$row->c_author = $JLMS_DB->LoadResult();
		$row->c_slide = 1;
		$row->c_gradebook = 1;
		$row->c_max_numb_attempts = 0;
		$row->c_resume = 1;
	}

	if(!isset($row->flag))
		$row->flag = 0;
	
	/*----------------------------------------------------*/
	$count_array = array();
	$new_val_array = array();
	if ($JLMS_CONFIG->get('global_quest_pool')) {
		if($id) {
			$query = "SELECT count(*) FROM #__lms_quiz_t_quiz_gqp WHERE quiz_id = $id AND qcat_id <> 0";
			$JLMS_DB->setQuery( $query );
			$pool_is_cat_mode_gqp = intval($JLMS_DB->loadResult());
			
			$query = "SELECT count(*) FROM #__lms_quiz_t_quiz_gqp WHERE quiz_id = $id AND qcat_id = 0";
			$JLMS_DB->setQuery( $query );
			$pool_is_quest_mode_gqp = intval($JLMS_DB->loadResult());

			if($pool_is_cat_mode_gqp) {
				$query = "SELECT * FROM #__lms_quiz_t_quiz_gqp WHERE quiz_id = $id AND qcat_id <> 0 ORDER BY orderin";
				$JLMS_DB->setQuery( $query );
				$db_cats = $JLMS_DB->loadObjectList();
				
				$row->flag = 2;
				
				for($i=0;$i<count($db_cats); $i++) {
					$_REQUEST['cat_id_gqp'][] = $db_cats[$i]->qcat_id;
					$_REQUEST['pool_cat_number_gqp'][] = $db_cats[$i]->items_number;					
				}
			}
			
//			$lists['pool_quest_mode_gqp'] = $pool_is_quest_mode_gqp ? false : true;
//			if (!$lists['pool_quest_mode_gqp']) {
//				$lists['pool_quest_num_gqp'] = 0;
//			}
		}
	}
	
	$levels = array();
	$javascript = 'onchange="document.adminForm.page.value=\'edit_quiz\';document.adminForm.submit();"';
	
	if($row->flag == 2) {
		
		$cat_id = mosGetParam($_REQUEST,'cat_id_gqp');
		$count_array = mosGetParam($_REQUEST,'pool_cat_number_gqp');

		if(isset($cat_id) && count($cat_id)) {
			foreach($cat_id as $k=>$v) {
				if($cat_id[$k] == 0)
					unset($cat_id[$k]);
					unset($_REQUEST['cat_id_gqp'][$k]);		
			}
	
			if(!isset($cat_id[0]))
				unset($cat_id);
			
			$lists = array();
			$level = 0;
			
			if(isset($cat_id) && count($cat_id)) {
				foreach($cat_id as $k=>$v) {
	
					$query = "SELECT parent"
					. "\n FROM #__lms_gqp_cats"
					. "\n WHERE id = '".$cat_id[$k]."'"
					;
					$JLMS_DB->setQuery( $query );
					$parent = intval($JLMS_DB->loadResult());
					
					if(!$parent) 
						$parent = 0; 
					
					/*-----------------category-----------------------*/	
						$query = "SELECT id AS value, c_category AS text"
						. "\n FROM #__lms_gqp_cats"
						. "\n WHERE parent = '".$parent."'"
						. "\n ORDER BY c_category"
						;
						
						$JLMS_DB->setQuery( $query );
						$categories[] = mosHTML::makeOption( '0', 'Select Category' );
						$categories = array_merge( $categories, $JLMS_DB->loadObjectList() );
						
						$level = JLMS_quiz_admin_class::check_level($cat_id[$k]);
						
						$categories = JLMS_quiz_admin_class::count_questions_in_category($categories, $level);
						
						$category = mosHTML::selectList( $categories, 'cat_id_gqp[]', 'class="text_area" size="1" style="width:100%;" '. $javascript, 'value', 'text', $cat_id[$k] ); 
						
						//echo JLMS_quiz_admin_class::check_level($cat_id[$k]); die;
						
						$levels[] = $level;
						
						$lists['category'][] = $category; 
						unset($category);
						unset($categories);
					
					/*----------------subcategory---------------------*/
					if(JLMS_quiz_admin_class::select_sub($k, $cat_id)) {
							
						$this_level = JLMS_quiz_admin_class::check_level($cat_id[$k]);
						
						$level1=$this_level;
						
						for($j=$this_level;$j>-1;$j--) {
							
							for($n=$k;$n>-1;$n--) {
								if(isset($cat_id[$n]) && isset($level1) && JLMS_quiz_admin_class::check_level($cat_id[$n]) == $level1 && JLMS_quiz_admin_class::this_level_on_stek($cat_id, $n)) {
							
										$query = "SELECT id AS value, c_category AS text"
										. "\n FROM #__lms_gqp_cats"
										. "\n WHERE parent = '".$cat_id[$n]."'"
										. "\n ORDER BY c_category"
										;
										$JLMS_DB->setQuery( $query );
										
										if($JLMS_DB->loadObjectList()) {
											$categories[] = mosHTML::makeOption( '0', 'Select SubCategory' );
											$categories = array_merge( $categories, $JLMS_DB->loadObjectList() );
											
											$categories = JLMS_quiz_admin_class::count_questions_in_category($categories, $this_level);
											
											$category = mosHTML::selectList( $categories, 'cat_id_gqp[]', 'class="text_area" size="1" style="width:100%;" '. $javascript, 'value', 'text', '' );
											$levels[] = $level1+1;
											$lists['category'][] = $category; 
											unset($category);
											unset($categories);
											
											$new_val_array[] = $cat_id[$k];
										}
									$level1--;
								}		
							}
						}
					}	
					/*------------------------------------------------*/	
				}
			}
		}
		
		$query = "SELECT id AS value, c_category AS text"
		. "\n FROM #__lms_gqp_cats"
		. "\n WHERE parent = 0"
		. "\n ORDER BY c_category"
		;
		
		$JLMS_DB->setQuery( $query );
		$categories[] = mosHTML::makeOption( '0', 'Select Category' );
		$categories = array_merge( $categories, $JLMS_DB->loadObjectList() );
		
		$categories = JLMS_quiz_admin_class::count_questions_in_category($categories, 0);
		
		$category = mosHTML::selectList( $categories, 'cat_id_gqp[]', 'class="text_area" size="1" style="width:100%;"'. $javascript, 'value', 'text', '' ); 
		$lists['new_category'] = $category; 
		unset($category);
		unset($categories);
	}	
	
	/*---------------------------------------------------*/
	
	if(is_array($row->params)){
		$row->params = implode("\n", $row->params);
	}
	
	$params = new JLMSParameters($row->params);
	$params->def('disable_quest_feedback', 0);
	$params->def('sh_user_answer', 0);
	$params->def('sh_correct_answer', 1);
	$params->def('sh_explanation', 0);
	$params->def('sh_final_page', 1);
	$params->def('sh_self_verification', 0);
	$params->def('sh_final_page_text', 1);
	$params->def('sh_final_page_grafic', 0);
	$params->def('sh_final_page_fdbck', 1);
	$params->def('sh_skip_quest', 0);
	if (!$row->c_user_id) $row->c_user_id = $my->id;
	

	$query = "SELECT a.*, b.items_number FROM #__lms_quiz_t_category as a LEFT JOIN #__lms_quiz_t_quiz_pool as b"
	. "\n ON a.c_id = b.qcat_id AND b.quiz_id = $id"
	. "\n WHERE a.course_id = '".$course_id."' AND a.is_quiz_cat = 0 order by a.c_category";
	$JLMS_DB->setQuery( $query );
	$pool_cats = $JLMS_DB->loadObjectList();
	$lists['jq_pool_categories'] = $pool_cats;
		
	if ($JLMS_CONFIG->get('global_quest_pool')) {
		$query = "SELECT a.*, b.items_number FROM #__lms_gqp_cats as a LEFT JOIN #__lms_quiz_t_quiz_gqp as b"
		. "\n ON a.id = b.qcat_id AND b.quiz_id = $id"
		. "\n order by a.c_category";
		$JLMS_DB->setQuery( $query );
		$pool_cats = $JLMS_DB->loadObjectList();
		$lists['jq_pool_categories_gqp'] = $pool_cats;
		
	}
	
	$query= "SELECT items_number FROM #__lms_quiz_t_quiz_pool WHERE quiz_id = $id AND qcat_id = 0";
	$JLMS_DB->setQuery( $query );
	$pool_quest_num = intval($JLMS_DB->loadResult());
	$lists['pool_quest_num'] = $pool_quest_num;

	if ($JLMS_CONFIG->get('global_quest_pool')) {
		$query= "SELECT items_number FROM #__lms_quiz_t_quiz_gqp WHERE quiz_id = $id AND qcat_id = 0";
		$JLMS_DB->setQuery( $query );
		$pool_quest_num_gqp = intval($JLMS_DB->loadResult());
		$lists['pool_quest_num_gqp'] = $pool_quest_num_gqp;
	}
	
	
	$query = "SELECT count(*) FROM #__lms_quiz_t_quiz_pool WHERE quiz_id = $id AND qcat_id <> 0";
	$JLMS_DB->setQuery( $query );
	$pool_is_quest_mode = intval($JLMS_DB->loadResult());
	$lists['pool_quest_mode'] = $pool_is_quest_mode ? false : true;
	if (!$lists['pool_quest_mode']) {
		$lists['pool_quest_num'] = 0;
	}
	
	$query = "SELECT * FROM #__lms_quiz_t_category WHERE course_id = '".$course_id."' AND is_quiz_cat = 1 order by c_category";
	$JLMS_DB->setQuery( $query );
	$jq_cats = $JLMS_DB->loadObjectList();
	$lists['jq_categories']	= mosHTML::selectList( $jq_cats, 'c_category_id', 'class="inputbox" size="1"', 'c_id', 'c_category', $row->c_category_id );
	$jq_temps = array();
	/*$query = "SELECT * FROM #__lms_quiz_templates order by id";
	$JLMS_DB->setQuery( $query );
	$jq_temps = $JLMS_DB->loadObjectList();
	$lists['jq_templates'] = mosHTML::selectList( $jq_temps, 'c_skin', 'class="inputbox" size="1"', 'id', 'template_name', $row->c_skin );
	$jq_langs = array();*/
/*	$query = "SELECT * FROM #__lms_quiz_languages order by id";
	$JLMS_DB->setQuery( $query );
	$jq_langs = $JLMS_DB->loadObjectList();
	$lists['jq_languages'] = mosHTML::selectList( $jq_langs, 'c_language', 'class="inputbox" size="1"', 'id', 'lang_file', $row->c_language );*/
	$jq_certs = array();
	$query = "SELECT id as value, crtf_text as text, crtf_name FROM #__lms_certificates WHERE course_id = '".$course_id."' AND crtf_type = 2 AND parent_id = 0 ORDER BY id";
	$JLMS_DB->setQuery( $query );
	$jq_certs[] = mosHTML::makeOption(0, _JLMS_SB_NO_CERTIFICATE);
	$jq_certs2 = $JLMS_DB->loadObjectList();
	$i = 0;
	while ($i < count($jq_certs2)) {
		$jq_certs2[$i]->text = $jq_certs2[$i]->crtf_name?$jq_certs2[$i]->crtf_name:((strlen($jq_certs2[$i]->text) > 50)?(substr($jq_certs2[$i]->text,0,50).'...'):$jq_certs2[$i]->text);
		$i ++;
	}
	
	$jq_certs = array_merge($jq_certs, $jq_certs2);
	$lists['jq_certificates'] = mosHTML::selectList( $jq_certs, 'c_certificate', 'class="inputbox" size="1" '.($params->get('sh_self_verification')?"disabled='disabled'":'').'', 'value', 'text', ($params->get('sh_self_verification') == 1)?0:$row->c_certificate );

	$user_emailto = array();
	$user_emailto[] = mosHTML::makeOption(0, _JLMS_DISABLE_OPTION);
	$user_emailto[] = mosHTML::makeOption(1, _JLMS_QUIZ_EMAIL_TO_AUTHOR);
	$user_emailto[] = mosHTML::makeOption(2, _JLMS_QUIZ_EMAIL_TO_LEARNER);
	$lists['user_email_to'] = mosHTML::selectList( $user_emailto, 'c_email_to', 'class="inputbox" size="1"', 'value', 'text', $row->c_email_to );
	
	$quiz_explanation = array();
	$quiz_explanation[] = mosHTML::makeOption(0, _JLMS_DISABLE_OPTION);
	$quiz_explanation[] = mosHTML::makeOption(1, _JLMS_QUIZ_REVIEW_FOR_ALL);
	$quiz_explanation[] = mosHTML::makeOption(2, _JLMS_QUIZ_REVIEW_FOR_PASSED);
	$quiz_explanation[] = mosHTML::makeOption(3, _JLMS_QUIZ_REVIEW_FOR_FAILED);
	$lists['quiz_explanation'] = mosHTML::selectList( $quiz_explanation, 'params[sh_explanation]', 'class="inputbox" size="1"', 'value', 'text', $params->get('sh_explanation') );

	
	$lists['published'] = mosHTML::yesnoradioList( 'published', '', $row->published );
	
	$lists['sh_final_page_text'] = mosHTML::yesnoradioList( 'params[sh_final_page_text]', 'class="inputbox"', $params->get('sh_final_page_text', 1) );
	$lists['sh_final_page_grafic'] = mosHTML::yesnoradioList( 'params[sh_final_page_grafic]', 'class="inputbox"', $params->get('sh_final_page_grafic', 0) );
	$lists['sh_final_page_fdbck'] = mosHTML::yesnoradioList( 'params[sh_final_page_fdbck]', 'class="inputbox"', $params->get('sh_final_page_fdbck', 1) );
	
	$property = $params->get('sh_self_verification') == 1 ? "disabled" : "";
	$property_1 = $property . ' onclick="javascript: this.form[\'c_enable_review\'].value = this.value;"';
	$lists['c_enable_review_chk'] = mosHTML::yesnoradioList( 'c_enable_review_chk', $property_1, $row->c_enable_review );
	
	$lists['sh_correct_answer'] = mosHTML::yesnoradioList( 'params[sh_correct_answer]', $property, $params->get('sh_correct_answer', 1) );
	$lists['sh_user_answer'] = mosHTML::yesnoradioList( 'params[sh_user_answer]', $property, $params->get('sh_user_answer', 0) );

	$lists['c_resume'] = mosHTML::yesnoradioList( 'c_resume', '', $row->c_resume );
	
	
	if(!mosGetParam($_REQUEST,'c_title') && !isset($row->c_title)) {
		if(!isset($cat_id))
			$cat_id = array();
		$count_array = JLMS_quiz_admin_class::insert_null($new_val_array, $count_array, $cat_id);
	}	
	
	JLMS_quiz_admin_html_class::JQ_editQuiz( $row, $lists, $option, $page, $course_id, $params, $levels, $count_array );
}

function count_questions_in_category($categories, $level) {
	global $JLMS_DB;
	
		for($i=0;$i<count($categories); $i++) {
			if($categories[$i]->value > 0 ) {
				$tmp_level = array();
				$last_catid = $categories[$i]->value;
				
				$all_cats = array();
				$query = "SELECT * FROM #__lms_gqp_cats"
				. "\n ORDER BY id";
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
				$j=1;
				foreach($children as $key=>$childs){
					if($last_catid == $key){
						foreach($children[$key] as $v){
							if(!in_array($v, $tmp_cats_filter)){
								$tmp_cats_filter[$j] = $v;
								$j++;
							}
						}
					}
				}
				foreach($children as $key=>$childs){
					if(in_array($key, $tmp_cats_filter)){
						foreach($children[$key] as $v){
							if(!in_array($v, $tmp_cats_filter)){
								$tmp_cats_filter[$j] = $v;
								$j++;
							}
						}
					}
				}
				$tmp_cats_filter = array_unique($tmp_cats_filter);
				
				$catids = '';
				$count = 0;
				if(isset($tmp_cats_filter) && count($tmp_cats_filter)){
					$catids = implode(",", $tmp_cats_filter);
					$query = "SELECT count(c_id) FROM #__lms_quiz_t_question"
					. "\n WHERE 1"
					. "\n AND c_qcat IN (".$catids.")"
					. "\n AND course_id = 0"
					. "\n AND c_quiz_id = 0"
					. "\n AND published = 1"
					;
					$JLMS_DB->SetQuery($query);
					$count = $JLMS_DB->LoadResult();
				}

				$categories[$i]->text = $categories[$i]->text." (".$count.")";
			}
		}
	return $categories;	
}


function insert_null($new_val_array, $count_array, $arr) {

	$new_count_array = array();
	for($j=0;$j<count($new_val_array);$j++) {
		$new_arr = array();
		$new_count_array = array();
		$flag = 0;
		$i=0;
		
		foreach ($arr as $k=>$v) {
			
			if($v == $new_val_array[$j])	{
				$flag = 1;			
			}
			$new_arr[] = $v;
			$new_count_array[] = $count_array[$i];
			$i++;
			
			if($flag) {
				$new_arr[] = 0;
				$new_count_array[] = ''; 
				$flag = 0;
			}
		}
		
		$arr = $new_arr;
		$count_array = $new_count_array;
	}
	
	return $new_count_array;
}

function this_level_on_stek($cat_id, $i) {
	global $JLMS_DB;	

	foreach ($cat_id as $k=>$v) {
		if($k > $i) {

			if((JLMS_quiz_admin_class::check_level($cat_id[$k]) < JLMS_quiz_admin_class::check_level($cat_id[$i])) ) {
				return 1;
			}
			else {	
				
				$query = "SELECT parent FROM #__lms_gqp_cats"
				. "\n WHERE id ='".$v."'";
				$JLMS_DB->setQuery( $query );
				$parent = $JLMS_DB->loadResult();
				
				foreach ($cat_id as $n=>$m) {
					if($n < $i) {
					
						if($cat_id[$n] == $parent)	
							return 0;
					}
				}
			}
		}
	}	
	return 1;
}

function select_sub($i, $cat_id) {
	global $JLMS_DB;
	//echo $cat_id[$i]; die;
	
	if(isset($cat_id[$i+1])) {
		
		$this_level = JLMS_quiz_admin_class::check_level($cat_id[$i]);
		$next_level = JLMS_quiz_admin_class::check_level($cat_id[$i+1]);
		
//		echo $this_level."<br>";
//		echo $next_level."<hr>";
		
		if($next_level < $this_level) {
			return true;
		}
	}
	else 
		return true;
	
	
		
//	if(isset($cat_id[$i+1])) {
//		$query = "SELECT parent FROM #__lms_gqp_cats"
//		. "\n WHERE id ='".$cat_id[$i+1]."'";
//		$JLMS_DB->setQuery( $query );
//		$parent = $JLMS_DB->loadResult();
//
//		echo $query."<br>";
//		echo $parent."<hr>";
//		
//		if($parent == $cat_id[$i]) {
//			return false;
//		}
//		else {
//			return true;
//		}
//	}
//	else 
//		return true;
}

function check_level($id) {
	global $JLMS_DB;
	
	$flag = 1;
	$level = 0;
	
	if($id) {
		while($flag > 0) {
			$query = "SELECT parent FROM #__lms_gqp_cats"
			. "\n WHERE id ='".$id."'";
			$JLMS_DB->setQuery( $query );
			$parent = $JLMS_DB->loadResult();

			if($parent == 0) {
				$flag = 0;
			} else {
				$level++;		
				$id = $parent;
			}
		}
	}
	return $level;
}


function JQ_saveQuiz( $option, $page, $course_id ) {
	global $JLMS_DB, $my, $Itemid, $JLMS_CONFIG;

	$row = new mos_JoomQuiz_Quiz( $JLMS_DB );
	if (!$row->bind( $_POST )) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}
	$JLMS_ACL = & JLMSFactory::getACL();
	if (!$JLMS_ACL->CheckPermissions('quizzes', 'publish')) {
		unset($row->published);
	}
	$row->course_id = $course_id;
	$row->c_user_id = $my->id;
	$row->c_skin = 3;
	$row->c_language = 1;
	$row->c_guest = 0;
	$params = mosGetParam( $_POST, 'params', '' );
	$quiz_params = '';
	if (is_array( $params )) {
		$txt = array();
		foreach ( $params as $k=>$v) {
			$txt[] = "$k=$v";
		}
		$quiz_params = implode( "\n", $txt );
	}
	$row->params = $quiz_params;

	if (!$row->c_id) {
		$date = time();
		$s_day = mktime(0,0,0,date('m',$date), date('d',$date), date('Y',$date));
		$row->c_created_time = date( 'Y-m-d', $s_day );
	} else {
		unset($row->c_created_time);
	}

	$row->c_title = strval(JLMS_getParam_LowFilter($_POST, 'c_title', ''));
	$row->c_title = JLMS_Process_ContentNames($row->c_title);

	$row->c_description = strval(JLMS_getParam_LowFilter($_POST, 'c_description', ''));
	$row->c_description = JLMS_ProcessText_LowFilter($row->c_description);

	$row->c_right_message = strval(JLMS_getParam_LowFilter($_POST, 'c_right_message', ''));
	$row->c_right_message = JLMS_ProcessText_LowFilter($row->c_right_message);
	$row->c_wrong_message = strval(JLMS_getParam_LowFilter($_POST, 'c_wrong_message', ''));
	$row->c_wrong_message = JLMS_ProcessText_LowFilter($row->c_wrong_message);
	$row->c_pass_message = strval(JLMS_getParam_LowFilter($_POST, 'c_pass_message', ''));
	$row->c_pass_message = JLMS_ProcessText_LowFilter($row->c_pass_message);
	$row->c_unpass_message = strval(JLMS_getParam_LowFilter($_POST, 'c_unpass_message', ''));
	$row->c_unpass_message = JLMS_ProcessText_LowFilter($row->c_unpass_message);
	
	$days = intval(mosGetParam($_POST, 'days', ''));
	$hours = intval(mosGetParam($_POST, 'hours', ''));
	$mins = intval(mosGetParam($_POST, 'mins', ''));
	
	if( $row->is_time_related ) {
		$row->show_period = JLMS_HTML::_('showperiod.getminsvalue', $days, $hours, $mins );
	}

	if (!$row->check()) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}
	if (!$row->store()) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}
	
	$query = "DELETE FROM #__lms_quiz_t_quiz_pool WHERE quiz_id = $row->c_id";
	$JLMS_DB->SetQuery($query);
	$JLMS_DB->query();
	$pool_type = intval(mosGetParam($_REQUEST, 'c_pool_type', 0));
	
	if ($pool_type == 1) {
		$pool_num = intval(mosGetParam($_REQUEST, 'pool_qtype_number', 0));
		if ($pool_num) {
			$query = "INSERT INTO #__lms_quiz_t_quiz_pool (quiz_id, qcat_id, items_number)"
			. "\n VALUES($row->c_id, 0, $pool_num)";
			$JLMS_DB->SetQuery($query);
			$JLMS_DB->query();
		}
	} elseif ($pool_type == 2) {
		if (!empty($_REQUEST['pool_cat_id'])) {
			for ($i = 0, $n = count($_REQUEST['pool_cat_id']); $i < $n; $i ++) {
				$row_cid = isset($_REQUEST['pool_cat_id'][$i]) ? intval($_REQUEST['pool_cat_id'][$i]) : 0;
				$row_num = isset($_REQUEST['pool_cat_number'][$i]) ? intval($_REQUEST['pool_cat_number'][$i]) : 0;
				if ($row_cid && $row_num) {
					$query = "SELECT c_id FROM #__lms_quiz_t_category WHERE course_id = $course_id AND is_quiz_cat = 0 AND c_id = $row_cid";
					$JLMS_DB->SetQuery($query);
					$row_check = $JLMS_DB->LoadResult();
					if ($row_check == $row_cid) {
						$query = "INSERT INTO #__lms_quiz_t_quiz_pool (quiz_id, qcat_id, items_number)"
						. "\n VALUES($row->c_id, $row_cid, $row_num)";
						$JLMS_DB->SetQuery($query);
						$JLMS_DB->query();
					}
				}
			}
		}
	}

	if ($JLMS_CONFIG->get('global_quest_pool')) {
		
		$query = "DELETE FROM #__lms_quiz_t_quiz_gqp WHERE quiz_id = $row->c_id";
		$JLMS_DB->SetQuery($query);
		$JLMS_DB->query();
		$pool_type = intval(mosGetParam($_REQUEST, 'c_pool_type_gqp', 0));

		if ($pool_type == 1) {
			$pool_num = intval(mosGetParam($_REQUEST, 'pool_qtype_number_gqp', 0));
			if ($pool_num) {
				$query = "INSERT INTO #__lms_quiz_t_quiz_gqp (quiz_id, qcat_id, items_number)"
				. "\n VALUES($row->c_id, 0, $pool_num)";
				$JLMS_DB->SetQuery($query);
				$JLMS_DB->query();
			}
		} elseif ($pool_type == 2) {
			
			if (!empty($_REQUEST['cat_id_gqp'])) {
				
				$order = 1;
				
				foreach ($_REQUEST['cat_id_gqp'] as $k=>$v) {
					
						$row_cid = isset($_REQUEST['cat_id_gqp'][$k]) ? intval($_REQUEST['cat_id_gqp'][$k]) : 0;
						$row_num = isset($_REQUEST['pool_cat_number_gqp'][$k]) ? intval($_REQUEST['pool_cat_number_gqp'][$k]) : 0;
						
						if($row_cid) {
							
//							$query = "SELECT cat_id FROM #__lms_gqp_levels WHERE cat_id = $row_cid";
//							$JLMS_DB->SetQuery($query);
//							$row_check = $JLMS_DB->LoadResult();
//					
//									
//							if ($row_check == $row_cid) {
								$query = "INSERT INTO #__lms_quiz_t_quiz_gqp (quiz_id, qcat_id, items_number, orderin)"
								. "\n VALUES($row->c_id, $row_cid, $row_num, $order)";
								$JLMS_DB->SetQuery($query);
								$JLMS_DB->query();
								
								$order++;
//							}
						}
				}
					
//				for ($i = 0, $n = count($_REQUEST['cat_id_gqp']); $i < $n; $i ++) {
//					$row_cid = isset($_REQUEST['cat_id_gqp'][$i]) ? intval($_REQUEST['cat_id_gqp'][$i]) : 0;
//					$row_num = isset($_REQUEST['pool_cat_number_gqp'][$i]) ? intval($_REQUEST['pool_cat_number_gqp'][$i]) : 0;
//					if ($row_cid && $row_num) {
//						
//						$query = "SELECT cat_id FROM #__lms_gqp_levels WHERE cat_id = $row_cid";
//						$JLMS_DB->SetQuery($query);
//						$row_check = $JLMS_DB->LoadResult();
//						
//						if ($row_check == $row_cid) {
//							
//							$query = "INSERT INTO #__lms_quiz_t_quiz_gqp (quiz_id, qcat_id, items_number)"
//							. "\n VALUES($row->c_id, $row_cid, $row_num)";
//							$JLMS_DB->SetQuery($query);
//							$JLMS_DB->query();
//							
//						}
//					}
//				}
			}
		}	
	}

	if ($page == 'apply_quiz') {
		JLMSRedirect( $JLMS_CONFIG->get('live_site')."/index.php?option=$option&Itemid=$Itemid&task=quizzes&id=$course_id&page=editA_quiz&c_id=". $row->c_id );
	} else {
		JLMSRedirect( sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=quizzes&id=$course_id&page=quizzes") );
	}
}
function JQ_removeQuiz( &$cid, $option, $page, $course_id ) {
	global $JLMS_DB, $Itemid;
	if (count( $cid )) {
		$cids = implode( ',', $cid );
		$query = "SELECT c_id FROM #__lms_quiz_t_quiz"
		. "\n WHERE c_id IN ( $cids ) AND course_id = '".$course_id."'";
		$JLMS_DB->setQuery( $query );
		$ccid = $JLMS_DB->loadResultArray();
		if (!empty($ccid)) {
			$cids = implode( ',', $ccid );
			
			//topics
			$query = "DELETE FROM #__lms_topic_items WHERE item_id IN ($cids) AND item_type = 5";
			$JLMS_DB->SetQuery( $query );
			$JLMS_DB->query();
			//-------------
			
			$query = "DELETE FROM #__lms_quiz_t_quiz"
			. "\n WHERE c_id IN ( $cids ) AND course_id = '".$course_id."'"
			;
			$JLMS_DB->setQuery( $query );
			if (!$JLMS_DB->query()) {
				echo "<script> alert('".$JLMS_DB->getErrorMsg()."'); window.history.go(-1); </script>\n";
				exit();
			}
			$query = "SELECT c_id FROM #__lms_quiz_r_student_quiz WHERE c_quiz_id IN ( $cids )";
			$JLMS_DB->setQuery( $query );
			$cid2 = $JLMS_DB->loadResultArray();
			if (is_array( $cid2 ) && !empty( $cid2 )) {
				JLMS_quiz_admin_class::JQ_delete_quizReportA($cid2);
			}

			$query = "SELECT c_id FROM #__lms_quiz_t_question WHERE c_quiz_id IN ( $cids ) AND course_id = '".$course_id."'";
			$JLMS_DB->SetQuery( $query );
			$cid = $JLMS_DB->LoadResultArray();
			if (is_array( $cid ) && !empty( $cid )) {
				JLMS_quiz_admin_class::JQ_removeQuestion( $cid, $option, $page, $course_id, 1);
			} 
		}
	}
	JLMSRedirect( sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=quizzes&id=$course_id&page=quizzes") );
}
function JQ_cancelQuiz($option, $page, $course_id) {
	global $Itemid;
	JLMSRedirect( sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=quizzes&id=$course_id&page=quizzes") );
}
function JQ_changeQuiz( $cid=null, $state=0, $option, $page, $course_id ) {
	global $JLMS_DB, $Itemid;
	
	$cidoff = intval(mosGetParam($_GET,'cidoff',0));
	if($cidoff) {$cid[0] = $cidoff;};
	if (!is_array( $cid ) || count( $cid ) < 1) {
		$action = $publish ? 'publish_quiz' : 'unpublish_quiz';
		echo "<script> alert('Select an item to $action'); window.history.go(-1);</script>\n";
		exit();
	}

	$cids = implode( ',', $cid );

	$query = "UPDATE #__lms_quiz_t_quiz"
	. "\n SET published = " . intval( $state )
	. "\n WHERE c_id IN ( $cids ) AND course_id = '".$course_id."'"
	;
	$JLMS_DB->setQuery( $query );
	if (!$JLMS_DB->query()) {
		echo "<script> alert('".$JLMS_DB->getErrorMsg()."'); window.history.go(-1); </script>\n";
		exit();
	}
	JLMSRedirect( sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=quizzes&id=$course_id&page=quizzes") );
}
function JQ_changeQuest( $cid=null, $state=0, $option, $page, $course_id, $gqp = 0 ) {
	
	global $JLMS_DB, $Itemid;
	
	$cidoff = intval(mosGetParam($_GET,'cidoff',0));
	if($cidoff) {$cid[0] = $cidoff;};
	if (!is_array( $cid ) || count( $cid ) < 1) {
		$action = $publish ? 'publish_quest' : 'unpublish_quest';
		echo "<script> alert('Select an item to $action'); window.history.go(-1);</script>\n";
		exit();
	}

	$cids = implode( ',', $cid );

	$query = "UPDATE #__lms_quiz_t_question"
	. "\n SET published = " . intval( $state )
	. "\n WHERE c_id IN ( $cids ) AND course_id = '".$course_id."'"
	;
	$JLMS_DB->setQuery( $query );
	if (!$JLMS_DB->query()) {
		echo "<script> alert('".$JLMS_DB->getErrorMsg()."'); window.history.go(-1); </script>\n";
		exit();
	}
	
	if($gqp) {
		JLMSRedirect( sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=quizzes&page=setup_gqp") );
	}
	else {
		JLMSRedirect( sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=quizzes&id=$course_id&page=setup_quest") );	
	}
}
function JQ_moveQuizSelect( $option, $page, $course_id, $cid ) {
	global $JLMS_DB;

	if (!is_array( $cid ) || count( $cid ) < 1) {
		echo "<script> alert('Select an item to move'); window.history.go(-1);</script>\n";
		exit;
	}

	$cids = implode( ',', $cid );
	$query = "SELECT a.c_title as quiz_name, b.c_category as category_name"
	. "\n FROM #__lms_quiz_t_quiz AS a LEFT JOIN #__lms_quiz_t_category AS b ON b.c_id = a.c_category_id AND b.course_id = '".$course_id."' AND b.is_quiz_cat = 1"
	. "\n WHERE a.c_id IN ( $cids ) AND a.course_id = '".$course_id."'"
	;
	$JLMS_DB->setQuery( $query );
	$items = $JLMS_DB->loadObjectList();

	$query = "SELECT a.c_category AS text, a.c_id AS value"
	. "\n FROM #__lms_quiz_t_category AS a"
	. "\n WHERE course_id = '".$course_id."' AND is_quiz_cat = 1"
	. "\n ORDER BY a.c_category"
	;
	$JLMS_DB->setQuery( $query );

	$categories = array();
	$categories[] = mosHTML::makeOption( '0', _JLMS_SB_SELECT_CATEGORY );
	$categories = array_merge( $categories, $JLMS_DB->loadObjectList() );
	$CategoryList = mosHTML::selectList( $categories, 'categorymove', 'class="inputbox" size="1"', 'value', 'text', null );

	JLMS_quiz_admin_html_class::JQ_moveQuiz_Select( $option, $page, $course_id, $cid, $CategoryList, $items );
}
function JQ_moveQuizSave( $option, $page, $course_id, $cid ) {
	global $JLMS_DB, $Itemid;

	$categoryMove = strval( mosGetParam( $_REQUEST, 'categorymove', '' ) );

	$cids = implode( ',', $cid );
	$total = count( $cid );

	$query = "UPDATE #__lms_quiz_t_quiz"
	. "\n SET c_category_id = '$categoryMove'"
	. "WHERE c_id IN ( $cids ) AND course_id = '".$course_id."'"
	;
	$JLMS_DB->setQuery( $query );
	if ( !$JLMS_DB->query() ) {
		echo "<script> alert('". $JLMS_DB->getErrorMsg() ."'); window.history.go(-1); </script>\n";
		exit();
	}
	$categoryNew = new mos_JoomQuiz_Cat ( $JLMS_DB );
	$categoryNew->load( $categoryMove );
	
	#$msg = $total ." Quizzes moved to ". $categoryNew->c_category;
	JLMSRedirect( sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=quizzes&id=$course_id&page=quizzes") );	
}
function JQ_copyQuizSave( $option, $page, $course_id, $cid ) {
	global $JLMS_DB, $Itemid;

	$categoryMove = strval( mosGetParam( $_REQUEST, 'categorymove', '' ) );

	$cids = implode( ',', $cid );
	$total = count( $cid );

	$query = "SELECT * FROM #__lms_quiz_t_quiz WHERE c_id IN ( $cids ) AND course_id = '".$course_id."'";
	$JLMS_DB->setQuery( $query );
	$quizzes_to_copy = $JLMS_DB->loadAssocList();
	foreach ($quizzes_to_copy as $quiz2copy) {
		$new_quiz = new mos_JoomQuiz_Quiz( $JLMS_DB );
		if (!$new_quiz->bind( $quiz2copy )) { echo "<script> alert('".$new_quiz->getError()."'); window.history.go(-1); </script>\n"; exit(); }
		$JLMS_ACL = & JLMSFactory::getACL();
		if (!$JLMS_ACL->CheckPermissions('quizzes', 'publish')) {
			$new_quiz->published = 0;
		}
		$new_quiz->c_id = 0; $new_quiz->c_category_id = $categoryMove; $new_quiz->c_title = 'Copy of ' . $new_quiz->c_title;
		if (!$new_quiz->check()) { echo "<script> alert('".$new_quiz->getError()."'); window.history.go(-1); </script>\n"; exit(); }
		if (!$new_quiz->store()) { echo "<script> alert('".$new_quiz->getError()."'); window.history.go(-1); </script>\n"; exit(); }
		$new_quiz_id = $new_quiz->c_id;
		$query = "SELECT c_id FROM #__lms_quiz_t_question WHERE c_quiz_id = '".$quiz2copy['c_id']."' AND course_id = '".$course_id."'";
		$JLMS_DB->SetQuery( $query );
		$cid = $JLMS_DB->LoadResultArray();
		if (!is_array( $cid )) {
			$cid = array(0);
		} 
		JLMS_quiz_admin_class::JQ_copyQuestionSave( $option, $page, $course_id, $cid, 1, $new_quiz_id );
	}
	$categoryNew = new mos_JoomQuiz_Cat ( $JLMS_DB );
	$categoryNew->load( $categoryMove );
	
	#$msg = $total ." Quizzes including all questions was copied to ". $categoryNew->c_category;
	JLMSRedirect( sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=quizzes&id=$course_id&page=quizzes") );	
}

			#######################################
			###	--- ---     QUESTIONS 	--- --- ###

function JQ_ListQuestions( $option, $page, $id, $gqp = false ) {
	global $JLMS_DB, $JLMS_SESSION, $my, $Itemid, $JLMS_CONFIG;

	$JLMS_ACL = & JLMSFactory::getACL();
	$usertype_simple = $JLMS_ACL->_role_type;
//	$usertype_simple = JLMS_GetUserType_simple($my->id, false, true);
	
	//-------------------------------------------------------------------
	$filt_quest = mosGetParam($_REQUEST, 'quest_filter', $JLMS_SESSION->get('LQ_quest_filter', ''));
	$JLMS_SESSION->set('LQ_quest_filter', $filt_quest);
	
	//FLMS multicat
	$levels = array();
	if ($gqp) {
		/*
		$query = "SELECT * FROM #__lms_gqp_cats_config ORDER BY id";
		$JLMS_DB->setQuery($query);
		$levels = $JLMS_DB->loadObjectList();
		*/
		if(count($levels) == 0){
			for($i=0;$i<15;$i++){
				$num = $i + 1;
				if($i>0){
//					$levels[$i]->cat_name = _JLMS_COURSES_COURSES_GROUPS;	
					$levels[$i]->cat_name = '';//'Level #'.$num;
				} else {
//					$levels[$i]->cat_name = _JLMS_COURSES_COURSES_GROUPS;
					$levels[$i]->cat_name = '';//'Level #'.$num;
				}
			}
		}

		$level_id = array();
		for($i=0;$i<count($levels);$i++) {
			if($i == 0){
				$level_id[$i] = intval( mosGetParam( $_REQUEST, 'filter_id_'.$i.'', $JLMS_SESSION->get('GQP_filter_id_'.$i.'', 0) ) );
				$JLMS_SESSION->set('GQP_filter_id_'.$i.'', $level_id[$i]);
			} else {
				$level_id[$i] = intval( mosGetParam( $_REQUEST, 'filter_id_'.$i.'', $JLMS_SESSION->get('GQP_filter_id_'.$i.'', 0) ) );
				$JLMS_SESSION->set('GQP_filter_id_'.$i.'', $level_id[$i]);
			}
			if($i == 0){
				$parent_id[$i] = 0;
			} else {
				$parent_id[$i] = $level_id[$i-1];
			}
			if($i == 0 || $parent_id[$i]){ //(Max): extra requests
				$query = "SELECT count(id) FROM `#__lms_gqp_cats` WHERE parent = '".$parent_id[$i]."' ORDER BY c_category";
				$JLMS_DB->setQuery($query);
				$groups = $JLMS_DB->loadResult();
				if($groups==0){
					$level_id[$i] = 0;	
					$JLMS_SESSION->set('GQP_filter_id_'.$i.'', $level_id[$i]);
				}
			}
		}
		
		for($i=0;$i<count($levels);$i++) {
			if($i > 0 && $level_id[$i - 1] == 0){
				$level_id[$i] = 0;
				$JLMS_SESSION->set('GQP_filter_id_'.$i.'', $level_id[$i]);
				$parent_id[$i] = 0;
			} elseif($i == 0 && $level_id[$i] == 0) {
				$level_id[$i] = 0;
				$JLMS_SESSION->set('GQP_filter_id_'.$i.'', $level_id[$i]);
				$parent_id[$i] = 0;
			}
		}
		
		$javascript = 'onclick="javascript:read_filter();" onchange="javascript:write_filter();document.adminFormQ.page.value=\'setup_gqp\';document.adminFormQ.submit();"';
		
		$query1 = "SELECT group_id FROM `#__lms_users_in_global_groups` WHERE user_id = '".$my->id."'";
		$JLMS_DB->setQuery($query1);
		$user_group_ids = $JLMS_DB->loadResultArray();
		
		for($i=0;$i<count($levels);$i++) {
			if($i == 0 || $parent_id[$i]){ //(Max): extra requests
				if( $parent_id[$i] == 0 && $usertype_simple == 1) { //(Max): roletype_id
					$query = "SELECT * FROM `#__lms_gqp_cats` WHERE `parent` = '0'";
					$query .= "\n ORDER BY `c_category`";
				}
				else {
					$query = "SELECT * FROM `#__lms_gqp_cats` WHERE parent = '".$parent_id[$i]."' ORDER BY c_category";
				}
				
				$JLMS_DB->setQuery($query);
				$groups = $JLMS_DB->loadObjectList();
				
				if($parent_id[$i] && $i > 0 && count($groups)) {
					$type_level[$i][] = mosHTML::makeOption( 0, _JLMS_SB_QUIZ_SELECT_QCATS);//' &nbsp; ' );
					foreach ($groups as $group){
						$type_level[$i][] = mosHTML::makeOption( $group->id, $group->c_category );
					}
					$lists['filter_'.$i.''] = mosHTML::selectList($type_level[$i], 'filter_id_'.$i.'', 'class="inputbox" style="width: 266px;" size="1" '.$javascript, 'value', 'text', $level_id[$i] ); //onchange="document.location.href=\''. $link_multi .'\';"
				} elseif($i == 0) {
					$type_level[$i][] = mosHTML::makeOption( 0, _JLMS_SB_QUIZ_SELECT_QCATS);//' &nbsp; ' );
					foreach ($groups as $group){
						$type_level[$i][] = mosHTML::makeOption( $group->id, $group->c_category );
					}
					$lists['filter_'.$i.''] = mosHTML::selectList($type_level[$i], 'filter_id_'.$i.'', 'class="inputbox" style="width: 266px;" size="1" '.$javascript, 'value', 'text', $level_id[$i] ); //onchange="document.location.href=\''. $link_multi .'\';"
				}
			}
		}	
	}

	//-------------------------------------------------------------------
	$is_pool = false;
	$quiz_id = intval( mosGetParam( $_REQUEST, 'quiz_id', $JLMS_SESSION->get('quiz_id', 0 )) );
	if ($quiz_id == -1) {
		$is_pool = true;
	}

	$JLMS_ACL = & JLMSFactory::getACL();
	if (!$JLMS_ACL->CheckPermissions('quizzes', 'manage')) {
		if ($JLMS_ACL->CheckPermissions('quizzes', 'manage_pool')) {
			$quiz_id = -1;
			$is_pool = true;
		}
	}
	
	$JLMS_SESSION->set('quiz_id', $quiz_id);

	$qtype_id = intval( mosGetParam( $_REQUEST, 'qtype_id', $JLMS_SESSION->get('qtype_id', 0 )) );
	$JLMS_SESSION->set('qtype_id', $qtype_id);
	
	$qcats_id = intval( mosGetParam( $_REQUEST, 'qcats_id', $JLMS_SESSION->get('qcats_id', 0 )) );
	$JLMS_SESSION->set('qcats_id', $qcats_id);
	
	$new_qtype_id = intval( mosGetParam( $_REQUEST, 'new_qtype_id', $JLMS_SESSION->get('new_qtype_id', 0 )) );
	$JLMS_SESSION->set('new_qtype_id', $new_qtype_id);
	$limit = intval( mosGetParam( $_GET, 'limit', $JLMS_SESSION->get('list_limit', $JLMS_CONFIG->get('list_limit')) ) );
	$JLMS_SESSION->set('list_limit', $limit);
	$limitstart = intval( mosGetParam( $_REQUEST, 'limitstart', 0 ) );

	
	if(!$gqp) {
		$query = "SELECT COUNT(*)"
		. "\n FROM #__lms_quiz_t_question as a"
		
		. "\n LEFT JOIN #__lms_quiz_t_question AS k ON k.c_id = a.c_pool_gqp"
		. "\n LEFT JOIN #__lms_quiz_t_question AS m ON m.c_id = a.c_pool"
		
		. "\n WHERE a.course_id = '".$id."'"
		. ( $quiz_id ? ("\n AND a.c_quiz_id = ".($is_pool ? 0 : $quiz_id)) : '' )
		. ( $qtype_id ? ("\n AND a.c_type = $qtype_id") : '' )
		. ( $qcats_id ? "\n AND a.c_qcat = $qcats_id" : '' )
		. ($filt_quest ? ("\n AND ("
		
		. "\n ( CASE"
			. "\n WHEN a.c_type = 21 THEN k.c_id"
			. "\n WHEN a.c_type = 20 THEN m.c_id"
			. "\n WHEN a.c_type < 20 THEN a.c_id"
					
		. "\n END )"
		
		." \n LIKE '$filt_quest' OR " 
		
		. "\n ( CASE"
			. "\n WHEN a.c_type = 21 THEN k.c_question"
			. "\n WHEN a.c_type = 20 THEN m.c_question"
			. "\n WHEN a.c_type < 20 THEN a.c_question"
					
		. "\n END )"

		. "\n LIKE '%$filt_quest%' OR a.c_id LIKE '$filt_quest')") : '')
		;
		
		$JLMS_DB->setQuery( $query );
		$total = $JLMS_DB->loadResult();
		
	} else {
		$str = '';
		
		//NEW MUSLTICATS
		$tmp_level = array();
		$last_catid = 0;
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
		if(!$i){
			foreach($_SESSION as $key=>$item){
				if(preg_match('#GQP_filter_id_(\d+)#', $key, $result)){
					if($item){
						$tmp_level[$i] = $result;
						$last_catid = $item;
						$i++;
					}	
				}	
			}	
		}
		
		$query = "SELECT * FROM #__lms_gqp_cats"
		. "\n ORDER BY id";
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
		$catids = implode(",", $tmp_cats_filter);
		
		if($last_catid && count($tmp_cats_filter)){
			$str .= "\n AND a.c_qcat IN (".$catids.")";
		}
		//NEW MUSLTICATS
		
		/*old kosmosa
		for ($i=count($level_id);$i>-1;$i--) {
			if(isset($level_id[$i]) && $level_id[$i]) {
				$str = "\n AND d.cat_id = ".$level_id[$i]." AND d.level = $i";
				break;
			}
		}
		*/
		
		$query = "SELECT COUNT(*)"
		. "\n FROM #__lms_quiz_t_question a "
		//. "\n LEFT JOIN #__lms_quiz_t_qtypes b ON b.c_id = a.c_type LEFT JOIN #__lms_quiz_t_quiz c ON a.c_quiz_id = c.c_id AND c.course_id = 0"
//		. ( $str ? "\n LEFT JOIN #__lms_gqp_levels AS d ON d.quest_id = a.c_id " : '')
		//. "\n LEFT JOIN #__lms_gqp_cats AS qc ON d.cat_id = qc.id"
		. "\n WHERE a.course_id = 0"
		. ( $qtype_id ? "\n AND a.c_type = $qtype_id" : '' )
		. ( $qcats_id ? "\n AND a.c_qcat = $qcats_id" : '' )
		. ($filt_quest ? ("\n AND (a.c_id LIKE '$filt_quest' OR a.c_question LIKE '%$filt_quest%')") : '')
//		. "\n AND d.quest_id = a.c_id AND d.cat_id =qc.id "
		. ( $str ? $str : ' ')
		;
		$JLMS_DB->setQuery( $query );
		$total = $JLMS_DB->loadResult();
	}
	
	require_once(_JOOMLMS_FRONT_HOME . DS . "includes" . DS . "classes" . DS . "lms.pagination.php");
	$pageNav = new JLMSPageNav( $total, $limitstart, $limit );
	
	if(!$gqp) {
		$query = "SELECT a.*,"
				
//				. "\n ( CASE"
//					. "\n WHEN a.c_type = 21 THEN k.c_question"
//					. "\n WHEN a.c_type = 20 THEN m.c_question"
//					. "\n WHEN a.c_type < 20 THEN a.c_question"
//				. "\n END ) AS c_question_search, "	
//					
//				. "\n ( CASE"
//					. "\n WHEN a.c_type = 21 THEN k.c_id"
//					. "\n WHEN a.c_type = 20 THEN m.c_id"
//					. "\n WHEN a.c_type < 20 THEN a.c_id"
//				. "\n END ) AS c_id_search, "	
 				
// 				. "\n a.c_id, a.c_question, a.course_id,a.c_quiz_id,a.c_point,a.c_attempts,a.c_image,a.c_type,a.published,a.ordering,a.c_pool,a.c_qcat,a.params,a.c_explanation,a.c_pool_gqp,"
//				. "\n a.c_type as type1, k.c_type as type2,"
				
		. "\n  b.c_qtype as qtype_full, c.c_title as quiz_name, qc.c_category"
		. "\n FROM #__lms_quiz_t_question a LEFT JOIN #__lms_quiz_t_qtypes b ON b.c_id = a.c_type LEFT JOIN #__lms_quiz_t_quiz c ON a.c_quiz_id = c.c_id AND c.course_id = '".$id."'"
		. "\n LEFT JOIN #__lms_quiz_t_category as qc ON a.c_qcat = qc.c_id AND qc.course_id = '".$id."' AND qc.is_quiz_cat = 0"
		
		. "\n LEFT JOIN #__lms_quiz_t_question AS k ON k.c_id = a.c_pool_gqp"
		. "\n LEFT JOIN #__lms_quiz_t_question AS m ON m.c_id = a.c_pool"
		
		. "\n WHERE a.course_id = '".$id."'"
		. ( $quiz_id ? ("\n AND a.c_quiz_id = ".($is_pool ? 0 : $quiz_id)) : '' )
		. ( $qtype_id ? "\n AND a.c_type = $qtype_id" : '' )
		. ( $qcats_id ? "\n AND a.c_qcat = $qcats_id" : '' )
		. ($filt_quest ? ("\n AND ("
		
		. "\n ( CASE"
			. "\n WHEN a.c_type = 21 THEN k.c_id"
			. "\n WHEN a.c_type = 20 THEN m.c_id"
			. "\n WHEN a.c_type < 20 THEN a.c_id"
					
		. "\n END )"
		
		." \n LIKE '$filt_quest' OR " 
		
		. "\n ( CASE"
			. "\n WHEN a.c_type = 21 THEN k.c_question"
			. "\n WHEN a.c_type = 20 THEN m.c_question"
			. "\n WHEN a.c_type < 20 THEN a.c_question"
					
		. "\n END )"

		. "\n LIKE '%$filt_quest%' OR a.c_id LIKE '$filt_quest')") : '')
		. "\n ORDER BY a.ordering, a.c_id"
		. "\n LIMIT $pageNav->limitstart, $pageNav->limit"
		;
		$JLMS_DB->setQuery( $query );
		$rows = $JLMS_DB->loadObjectList();
	}
	else {
		$query = "SELECT a.*,"
		. "\n b.c_qtype as qtype_full, '' as quiz_name, qc.c_category"
		
//		. "\n, qtc.c_choice as right_answer"
		
		. "\n FROM #__lms_quiz_t_question a LEFT JOIN #__lms_quiz_t_qtypes b ON b.c_id = a.c_type"
		. "\n LEFT JOIN #__lms_gqp_cats AS qc ON a.c_qcat = qc.id"
		
//		. "\n, #__lms_quiz_t_choice as qtc"
		
		. "\n WHERE a.course_id = 0"
		. ( $qtype_id ? "\n AND a.c_type = $qtype_id" : '' )
		. ( $qcats_id ? "\n AND a.c_qcat = $qcats_id" : '' )
		. ($filt_quest ? ("\n AND (a.c_id LIKE '$filt_quest' OR a.c_question LIKE '%$filt_quest%')") : '')
		
//		. "\n AND a.c_id = qtc.c_quiestion_id"
//		. "\n AND qtc.c_rigth = 1"
		
		. ( $str ? $str : ' ')
		. "\n ORDER BY a.ordering, a.c_qcat"
		;
		$JLMS_DB->setQuery( $query, $pageNav->limitstart, $pageNav->limit );
		$rows = $JLMS_DB->loadObjectList();

		/*if (count($rows)) {
			$diff_ids = array();
			foreach ($rows as $one_qrow) {
				$diff_ids[] = $one_qrow->c_id;
			}
			if (count($diff_ids)) {
				$diff_ids_str = implode(',',$diff_ids);
				$query = "SELECT a.quest_id, max(a.level) as level FROM #__lms_gqp_levels as a, #__lms_gqp_cats AS b"
				. "\n WHERE a.quest_id IN ($diff_ids_str) AND a.cat_id = b.id GROUP BY quest_id";
				$JLMS_DB->setQuery( $query );
				$rows_levels = $JLMS_DB->loadObjectList();
				var_dump($rows_levels);
				if (count($rows_levels)) {
					for ($ir = 0, $nr = count($rows); $ir < $nr; $ir ++) {
						foreach ($rows_levels as $row_level) {
							if ($row_level->quest_id == $rows[$ir]->c_id) {
								$rows[$ir]->level = $row_level->level;
								break;
							}
						}
					}
				}
			}
		}*/
	}
	
	for($i=0;$i<count($rows);$i++) 
	{	
		$str = '_JLMS_QUIZ_QTYPE_'.$rows[$i]->c_type;
		if (defined($str)) {
			$rows[$i]->qtype_full = constant($str);
		}
	}
	
	if($gqp) {
		/*
		$tmp_level = 0;
		$tmp_level2 = 0;
		if(mosGetParam($_REQUEST,'filter_id_0') || (isset($_SESSION['GQP_filter_id_0']) && $_SESSION['GQP_filter_id_0'] > 0)) {
			if (count($rows)) {
				$tmp_level = isset($rows[0]->level) ? $rows[0]->level : 0;
				$tmp_level2 = $tmp_level + 1;
			}
		}
		$diff_ids = array();
		foreach ($rows as $one_qrow) {
			$diff_ids[] = $one_qrow->c_id;
		}
		if (count($diff_ids)) {
			$diff_ids_str = implode(',',$diff_ids);
			$query = "SELECT a.quest_id, b.c_category FROM #__lms_gqp_levels as a, #__lms_gqp_cats AS b"
			. "\n WHERE a.quest_id IN ($diff_ids_str) AND a.cat_id = b.id AND a.level = $tmp_level";
			$JLMS_DB->setQuery( $query );
			$rows_level_cats = $JLMS_DB->loadObjectList();
			if (count($rows_level_cats)) {
				for ($ir = 0, $nr = count($rows); $ir < $nr; $ir ++) {
					foreach ($rows_level_cats as $row_level_cat) {
						if ($row_level_cat->quest_id == $rows[$ir]->c_id) {
							if ($row_level_cat->c_category) {
								$rows[$ir]->c_category = $row_level_cat->c_category;
							}
							break;
						}
					}
				}
			}
			if ($tmp_level2) {
				$query = "SELECT a.quest_id, b.c_category FROM #__lms_gqp_levels as a, #__lms_gqp_cats AS b"
				. "\n WHERE a.quest_id IN ($diff_ids_str) AND a.cat_id = b.id AND a.level = $tmp_level2";
				$JLMS_DB->setQuery( $query );
				$rows_level_cats = $JLMS_DB->loadObjectList();
				if (count($rows_level_cats)) {
					for ($ir = 0, $nr = count($rows); $ir < $nr; $ir ++) {
						foreach ($rows_level_cats as $row_level_cat) {
							if ($row_level_cat->quest_id == $rows[$ir]->c_id) {
								if ($row_level_cat->c_category) {
									$rows[$ir]->c_category = $row_level_cat->c_category;
								}
								break;
							}
						}
					}
				}
			}
		}
		*/
		/*for($i=0;$i<count($rows);$i++) {
			$new_level = $rows[$i]->level+1;
			$query = "SELECT b.c_category FROM #__lms_gqp_levels AS a, #__lms_gqp_cats AS b WHERE a.quest_id = '".$rows[$i]->c_id."' AND a.cat_id = b.id AND a.level = '".$new_level."'";
			$JLMS_DB->SetQuery($query);
			$cat_name = $JLMS_DB->LoadResult();
			if($cat_name) {
				$rows[$i]->c_category = $cat_name;
			}
		}*/
		/*} else {
			for($i=0;$i<count($rows);$i++) {
				$query = "SELECT b.c_category FROM #__lms_gqp_levels AS a, #__lms_gqp_cats AS b WHERE a.quest_id = '".$rows[$i]->c_id."' AND a.cat_id = b.id AND a.level = 0";
				$JLMS_DB->SetQuery($query);
				$cat_name = $JLMS_DB->LoadResult();
				if($cat_name) {
					$rows[$i]->c_category = $cat_name;
					$rows[$i]->level = 0;
				}
			}
		}*/
	}
	
	$q_from_pool = array();
	foreach ($rows as $row) {
		if ($row->c_type == 20) {
			$q_from_pool[] = $row->c_pool;
		}
	}
	if (count($q_from_pool)) {
		$qp_ids =implode(',',$q_from_pool);
		$query = "SELECT a.*, b.c_qtype as qtype_full FROM #__lms_quiz_t_question as a LEFT JOIN #__lms_quiz_t_qtypes b ON b.c_id = a.c_type"
		. "\n WHERE a.course_id = '".$id."' AND a.c_id IN ($qp_ids)";
		$JLMS_DB->setQuery( $query );
		$rows2 = $JLMS_DB->loadObjectList();
		
		for($i=0;$i<count($rows2);$i++) {
			$str = '_JLMS_QUIZ_QTYPE_'.$rows2[$i]->c_type;
			if (defined($str)) {
				$rows2[$i]->qtype_full = constant($str);
			}
		}
		
		for ($i=0, $n=count( $rows ); $i < $n; $i++) {
			if ($rows[$i]->c_type == 20) {
				for ($j=0, $m=count( $rows2 ); $j < $m; $j++) {
					if ($rows[$i]->c_pool == $rows2[$j]->c_id) {
						$rows[$i]->c_question = $rows2[$j]->c_question;
						$rows[$i]->qtype_full = _JLMS_QUIZ_QUEST_POOL_SHORT . ' - ' . $rows2[$j]->qtype_full;
						break;
					}
				}
			}
		}
	}
	
	//----------------GQP
	
	$q_from_pool_gqp = array();
	foreach ($rows as $row) {
		if ($row->c_type == 21) {
			$q_from_pool_gqp[] = $row->c_pool_gqp;
		}
	}
	
	if (count($q_from_pool_gqp)) {
		$qp_ids_gqp =implode(',',$q_from_pool_gqp);
		$query = "SELECT a.*, b.c_qtype as qtype_full FROM #__lms_quiz_t_question as a LEFT JOIN #__lms_quiz_t_qtypes b ON b.c_id = a.c_type"
		. "\n WHERE a.course_id = 0 AND a.c_id IN ($qp_ids_gqp)";
		
		$JLMS_DB->setQuery( $query );
		$rows2 = $JLMS_DB->loadObjectList();
		
		for($i=0;$i<count($rows2);$i++) {
			$str = '_JLMS_QUIZ_QTYPE_'.$rows2[$i]->c_type;
			if (defined($str)) {
				$rows2[$i]->qtype_full = constant($str);
			}
		}
		
		for ($i=0, $n=count( $rows ); $i < $n; $i++) {
			if ($rows[$i]->c_type == 21) {
				for ($j=0, $m=count( $rows2 ); $j < $m; $j++) {
					if ($rows[$i]->c_pool_gqp == $rows2[$j]->c_id) {
						$rows[$i]->c_question = $rows2[$j]->c_question;
						$rows[$i]->qtype_full = _JLMS_QUIZ_QUEST_POOL_GQP_SHORT . ' - ' . $rows2[$j]->qtype_full;
						break;
					}
				}
			}
		}
	}
	//--------------------
	
	//Tooltip Right Answer (Max - 15.04.2011)
	for($i=0;$i<count($rows);$i++){
		if(in_array($rows[$i]->c_type, array(1, 2, 3))){
			
			$right_answer = '';
			
			$query = "SELECT c_choice"
			. "\n FROM"
			. "\n #__lms_quiz_t_choice"
			. "\n WHERE 1"
			. "\n AND c_right = 1"
			. "\n AND c_question_id = '".$rows[$i]->c_id."'"
			;
			$JLMS_DB->setQuery($query);
			$right_answer = $JLMS_DB->loadResult();
			
			if(strlen($right_answer)){
				$rows[$i]->right_answer = $right_answer;
			}
		} else 
		if(in_array($rows[$i]->c_type, array(20, 21))){
			if($rows[$i]->c_type == 20){
				$select_field = "c_pool";
			} else 
			if($rows[$i]->c_type == 21){
				$select_field = "c_pool_gqp";
			}
			$query = "SELECT ".$select_field
			. "\n FROM"
			. "\n #__lms_quiz_t_question"
			. "\n WHERE 1"
			. "\n AND c_id = '".$rows[$i]->c_id."'"
			;
			$JLMS_DB->setQuery($query);
			$question_id = $JLMS_DB->loadResult();
			
			$right_answer = '';
			
			if(intval($question_id)){
				$query = "SELECT b.c_choice"
				. "\n FROM"
				. "\n #__lms_quiz_t_question as a"
				. "\n, #__lms_quiz_t_choice as b"
				. "\n WHERE 1"
				. "\n AND a.c_id = '".$question_id."'"
				. "\n AND b.c_question_id = '".$question_id."'"
				. "\n AND a.c_type IN (".implode(',', array(1, 2, 3)).")"
				. "\n AND b.c_right = 1"
				;
				$JLMS_DB->setQuery($query);
				$right_answer = $JLMS_DB->loadResult();
			}
			if(strlen($right_answer)){
				$rows[$i]->right_answer = $right_answer;
			}
		}
	}
	//Tooltip Right Answer (Max - 15.04.2011)
	
	$javascript = 'onchange="document.adminFormQ.submit();"';

	$quizzes = array();
	$quizzes[] = mosHTML::makeOption( '0', _JLMS_SB_SELECT_QUIZ );
	$quizzes[] = mosHTML::makeOption( '-1', _JLMS_QUIZ_QUEST_POOL );
	if ($JLMS_ACL->CheckPermissions('quizzes', 'manage')) {
		$query = "SELECT c_id AS value, c_title AS text"
		. "\n FROM #__lms_quiz_t_quiz"
		. "\n WHERE course_id = '".$id."'"
		. "\n ORDER BY c_title"
		;
		$JLMS_DB->setQuery( $query );
		$quizzes = array_merge( $quizzes, $JLMS_DB->loadObjectList() );
	}
	$quiz = mosHTML::selectList( $quizzes,'quiz_id', 'class="inputbox" style="width:200px" size="1" '. $javascript, 'value', 'text', ($is_pool ? -1 : $quiz_id) ); 
	$lists['quiz'] = $quiz;

	$lists['filtered_quiz'] = ($is_pool ? -1 : $quiz_id);

//test	
	if( ($gqp) ) 
	{
		$where = "\n WHERE c_id NOT IN (".implode(',', JLMS_quiz_admin_class::skippedQuestionIds()).")";
	} else {
		$where = '';
	}
	
	$query = "SELECT c_id AS value, c_qtype AS text"
	. "\n FROM #__lms_quiz_t_qtypes"
	. $where	 
	. "\n ORDER BY c_id"
	;
	$JLMS_DB->setQuery( $query );
	$qtypes_lang = $JLMS_DB->loadObjectList();
		
	for($i=0;$i<count($qtypes_lang);$i++) {
		$j=$i+1;		
		$str = '_JLMS_QUIZ_QTYPE_'.$j;
		
		if (defined($str)) {
			$qtypes_lang[$i]->value = $j;
			$qtypes_lang[$i]->text = constant($str);
		}
	}
//
//	$query = "SELECT c_id AS value, c_qtype AS text"
//	. "\n FROM #__lms_quiz_t_qtypes"
//	. "\n ORDER BY c_id"
//	;
//	$JLMS_DB->setQuery( $query );
	$qtypes[] = mosHTML::makeOption( '0', _JLMS_SB_QUIZ_SELECT_QTYPE );
//	$qtypes = array_merge( $qtypes, $JLMS_DB->loadObjectList() );
	$qtypes = array_merge( $qtypes, $qtypes_lang );
	$qtype = mosHTML::selectList( $qtypes, 'qtype_id', 'class="inputbox"  style="width:200px" size="1" '. $javascript, 'value', 'text', $qtype_id ); 
	$lists['qtype'] = $qtype;
	
	
	$query = "SELECT * FROM #__lms_quiz_t_category WHERE course_id = '".$id."' AND is_quiz_cat = '0'";
	$JLMS_DB->setQuery($query);
	$qcats = array();
	$qcats[] = mosHTML::makeOption( '0', _JLMS_SB_QUIZ_SELECT_QCATS, 'c_id', 'c_category' );
	$qcats = array_merge( $qcats, $JLMS_DB->loadObjectList() );
	$lists['qcats'] = mosHTML::selectList( $qcats, 'qcats_id', 'class="inputbox" size="1" '. $javascript, 'c_id', 'c_category', $qcats_id );
	
	$lists['filt_quest'] = $filt_quest;

//	$query = "SELECT c_id AS value, c_qtype AS text"
//	. "\n FROM #__lms_quiz_t_qtypes"
//	. "\n ORDER BY c_id"
//	;
//	$JLMS_DB->setQuery( $query );
	$qtypes = array();
	$qtypes[] = mosHTML::makeOption( '0', _JLMS_SB_QUIZ_SELECT_QTYPE );
//	$qtypes = array_merge( $qtypes, $JLMS_DB->loadObjectList() );
	$qtypes = array_merge( $qtypes, $qtypes_lang );
	if ($quiz_id && !$is_pool) {
		$qtypes[] = mosHTML::makeOption( '20', _JLMS_QUIZ_ADD_QUEST_FROM_POOL );
	}
		
	if($JLMS_CONFIG->get('global_quest_pool',0) && $quiz_id && !$is_pool) {
		$qtypes[] = mosHTML::makeOption( '21', _JLMS_QUIZ_ADD_QUEST_FROM_GQP_POOL );
	}
	
	$qtype = mosHTML::selectList( $qtypes, 'new_qtype_id', 'class="inputbox" style="width:200px" size="1" ', 'value', 'text', $new_qtype_id );
	$lists['new_qtype'] = $qtype;
	
	for($i=0;$i<count($rows);$i++){
		if(isset($rows[$i]->c_question)){
			preg_match_all('#{\w+}(.*){\/\w+}#', $rows[$i]->c_question, $out, PREG_PATTERN_ORDER);
			if(isset($out[0][0]) && isset($out[1][0])){
				$rows[$i]->c_question = str_replace($out[0][0], $out[1][0], $rows[$i]->c_question);
			}
		}
	}
	
	JLMS_quiz_admin_html_class::JQ_showQuestsList( $rows, $lists, $pageNav, $option, $page, $id, $is_pool, $gqp, $levels);
}

function JQ_editQuestion( $id, $option, $qtype, $page, $course_id, $gqp = false ) {

	global $JLMS_DB, $my, $JLMS_SESSION, $Itemid, $JLMS_CONFIG;

	if($gqp && mosGetParam($_REQUEST,'c_id')) {
		$query = "SELECT c_id FROM #__lms_quiz_t_question"
		. "\n WHERE c_id = '".intval(mosGetParam($_REQUEST,'c_id'))."' AND course_id > 0 AND c_quiz_id > 0";
		$JLMS_DB->setQuery( $query );
		$c_id = $JLMS_DB->loadResult();
		
		if($c_id) {
			JLMSRedirect( sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=quizzes&page=setup_gqp") );
		}
	}
	
	$row = new mos_JoomQuiz_Question( $JLMS_DB );
	$row->load( $id );
	
	$quiz_id = intval( mosGetParam( $_REQUEST, 'quiz_id', $JLMS_SESSION->get('quiz_id', 0 )) );
	
	$JLMS_ACL = & JLMSFactory::getACL();
	if (!$JLMS_ACL->CheckPermissions('quizzes', 'manage')) {
		if ($JLMS_ACL->CheckPermissions('quizzes', 'manage_pool')) {
			$quiz_id = -1;
			if ($id) {
				if ($row->c_quiz_id == 0) {
				} else {
					JLMSRedirect( sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=quizzes&id=$course_id&page=quizzes") );
				}
			}
		}
	}
	
	if ($id) {
		if ($row->c_quiz_id == 0) {
			$row->c_quiz_id = -1;
		}
	} elseif(mosGetParam($_REQUEST,'level_id_0')) {
		$row->bind( $_POST );
		/*Fix params (Max)*/
		if(isset($row->params) && count($row->params)){
			$row_params = $row->params;
			
			$row->params = '';
			$i=0;
			if(isset($row_params) && is_array($row_params) && count($row_params)){
				foreach($row_params as $key=>$item){
					$row->params .= $key.'='.$item;
					if($i < count($row_params)){
						$row->params .= "\n";
					}
					$i++;
				}
			}
		}
		/*Fix params (Max)*/
	} else {
		// do stuff for new records
		$row->ordering 		= 0;
		$row->c_quiz_id		= $quiz_id;//intval( mosGetParam( $_REQUEST, 'quiz_id', $JLMS_SESSION->get('quiz_id', 0 )) );
		$row->c_type		= intval(mosGetParam($_REQUEST, 'new_qtype_id', 1));
		$row->c_question	= "Enter question text here";
		//$row->c_qcat		= 0;
	}
	
	$params = new JLMSParameters($row->params);
	$params->def('disable_quest_feedback', 0);
	$params->def('survey_question', 0);
	$params->def('case_sensivity', 0);

	$lists = array();

	$query = "SELECT c_id as value, c_category as text FROM #__lms_quiz_t_category WHERE course_id = '".$course_id."' AND is_quiz_cat = 0 order by c_category";
	$JLMS_DB->setQuery( $query );
	$jq_cats = array();
	$jq_cats[] = mosHTML::makeOption(0, ' - '._JLMS_QUIZ_CAT_TYPE_QUEST.' - ');
	$jq_cats = array_merge($jq_cats, $JLMS_DB->loadObjectList());
	$lists['jq_categories']	= mosHTML::selectList( $jq_cats, 'c_qcat', 'class="inputbox" size="1"', 'value', 'text', $row->c_qcat );
	
	$is_pool = false;
	if ($row->c_quiz_id == -1) {
		$is_pool = true;
	}

	$query = "SELECT a.ordering AS value, a.c_question AS text, a.c_type, a.c_id, a.c_pool, a.c_pool_gqp"
	. "\n FROM #__lms_quiz_t_question AS a"
	. "\n WHERE a.course_id = '".$course_id."' "
	. ($row->c_quiz_id ? ("\n AND a.c_quiz_id = ".($is_pool ? 0 : $row->c_quiz_id)) :'')
	. "\n ORDER BY a.ordering, a.c_id"
	;

	//$text_new_order = _C M N_NEW_ITEM_FIRST;
	//if ( $id ) {
	if (true) {
		###
		$chop = 30;
		$order = array();
		$JLMS_DB->setQuery( $query );
		$orders = $JLMS_DB->loadObjectList();
		if (empty($orders)) {
			$order[] = mosHTML::makeOption( 0, _JLMS_SB_FIRST_ITEM );
		} else {
			// QuestPool compatibility
			$q_from_pool = array();
			foreach ($orders as $rowtmp) {
				if ($rowtmp->c_type == 20) {
					$q_from_pool[] = $rowtmp->c_pool;
				}
			}
			if (count($q_from_pool)) {
				$qp_ids =implode(',',$q_from_pool);
				$query = "SELECT a.* FROM #__lms_quiz_t_question as a"
				. "\n WHERE a.course_id = '".$course_id."' AND a.c_id IN ($qp_ids)";
				$JLMS_DB->setQuery( $query );
				$orders2 = $JLMS_DB->loadObjectList();
				for ($i=0, $n=count( $orders ); $i < $n; $i++) {
					if ($orders[$i]->c_type == 20) {
						for ($j=0, $m=count( $orders2 ); $j < $m; $j++) {
							if ($orders[$i]->c_pool == $orders2[$j]->c_id) {
								$orders[$i]->text = $orders2[$j]->c_question;
								break;
							}
						}
					}
				}
			}
			// Global QuestPool compatibility
			
				
			$q_from_pool_gqp = array();
			foreach ($orders as $rowtmp) {
				if ($rowtmp->c_type == 21) {
					$q_from_pool_gqp[] = $rowtmp->c_pool_gqp;
				}
			}
			
			if (count($q_from_pool_gqp)) {
				$qp_ids_gqp =implode(',',$q_from_pool_gqp);
				$query = "SELECT a.* FROM #__lms_quiz_t_question as a"
				. "\n WHERE a.course_id = 0 AND a.c_id IN ($qp_ids_gqp)";
				$JLMS_DB->setQuery( $query );
				$orders2 = $JLMS_DB->loadObjectList();
				for ($i=0, $n=count( $orders ); $i < $n; $i++) {
					if ($orders[$i]->c_type == 21) {
						for ($j=0, $m=count( $orders2 ); $j < $m; $j++) {
							if ($orders[$i]->c_pool_gqp == $orders2[$j]->c_id) {
								$orders[$i]->text = $orders2[$j]->c_question;
								break;
							}
						}
					}
				}
			}
			
			$order[] = mosHTML::makeOption( 0, '0 '._JLMS_SB_FIRST_ITEM );
			for ($i=0, $n=count( $orders ); $i < $n; $i++) {
				$temp_txt = $orders[$i]->text;
				mosMakeHtmlSafe( $temp_txt );
				$temp_txt = strip_tags($temp_txt);
				if (strlen($temp_txt) > $chop) {
					$text = substr($temp_txt,0,$chop)."...";
				} else {
					$text = $temp_txt;
				}
				$order[] = mosHTML::makeOption( $orders[$i]->c_id, $orders[$i]->value.' ('.$text.')' );
			}
			$order[] = mosHTML::makeOption( -1, ($orders[$i-1]->value+1).' '._JLMS_SB_LAST_ITEM );
		}
		###
		
		$ordering = mosHTML::selectList( $order, 'q_ordering', 'class="inputbox" size="1"', 'value', 'text', intval( $row->c_id ? $row->c_id : -1 ) );
		//set ordering to last element for new questions and without changes for existent questions

	}
	$lists['ordering'] = $ordering; 
	
	$query = "SELECT c_id AS value, c_title AS text"
	. "\n FROM #__lms_quiz_t_quiz"
	. "\n WHERE course_id = '".$course_id."' "
	. "\n ORDER BY c_title"
	;
	$JLMS_DB->setQuery( $query );
	$quizzes = array();
	$quizzes[] = mosHTML::makeOption( '-1', _JLMS_QUIZ_QUEST_POOL );
	$quizzes = array_merge( $quizzes, $JLMS_DB->loadObjectList() );
	$quiz = mosHTML::selectList( $quizzes, 'c_quiz_id', 'class="inputbox" size="1" ', 'value', 'text', intval( $row->c_quiz_id ) ); 
	$lists['quiz'] = $quiz;


	$lists['c_wrong_message'] = '';
	$lists['c_right_message'] = '';
	$query = "SELECT * FROM #__lms_quiz_t_question_fb WHERE quest_id = $id";
	$JLMS_DB->SetQuery($query);
	$q_fbs = $JLMS_DB->LoadObjectList();
	foreach ($q_fbs as $qfb) {
		if ($qfb->choice_id == -1) {
			$lists['c_wrong_message'] = $qfb->fb_text;
		} elseif(!$qfb->choice_id) {
			$lists['c_right_message'] = $qfb->fb_text;
		}
	}

	$JLMS_DB->SetQuery("SELECT c_qtype FROM #__lms_quiz_t_qtypes WHERE c_id = '".$row->c_type."'");
	$qtype_str = $JLMS_DB->LoadResult();
	
	$str = '_JLMS_QUIZ_QTYPE_'.$row->c_type;
	if (defined($str)) {
		$qtype_str = constant($str);
	}
	
	if(mosGetParam($_REQUEST,'c_type'))
		$row->c_type = mosGetParam($_REQUEST,'c_type');

	
	//---------------------------------------kosmos
	if($row->c_type == 21) {
		$javascript = 'onclick="read_filter();" onchange="javascript:write_filter();form.page.value=\'add_quest\';document.adminForm.submit();"';
	}
	else {
		$javascript = 'onclick="read_filter();" onchange="javascript:write_filter();form.page.value=\'edit_quest_gqp\';document.adminForm.submit();"';
	}
	
	//FLMS multicategories
	$levels = array();
	//NEW MULTICAT
	if($id){
		$tmp_level = array();
		$last_catid = 0;
		$i=0;
		foreach($_REQUEST as $key=>$item){
			if(preg_match('#level_id_(\d+)#', $key, $result)){
				if($item){
					$tmp_level[$i] = $result;
					$last_catid = $item;
					$i++;
				}
			}	
		}
		if(!$i){
			$query = "SELECT c_qcat FROM #__lms_quiz_t_question WHERE c_id = '".$id."'";
			$JLMS_DB->setQuery($query);
			$last_catid = $JLMS_DB->loadResult();	
		}
		
		$tmp = array();
		$tmp = JLMS_quiz_admin_class::JLMS_multicats($last_catid, $tmp);
		$tmp = array_reverse($tmp);
		
		$tmp_pop = $tmp;
		$tmp_p = array_pop($tmp_pop);
		if(count($tmp) && $tmp_p->catid){
			$next = count($tmp);
			$tmp[$next] = new stdClass();
			$tmp[$next]->catid = 0;
			$tmp[$next]->parent = $tmp_p->catid;
		}
	} else {
		$tmp_level = array();
		$last_catid = 0;
		$exist_in_request = 0;
		$i=0;
		foreach($_REQUEST as $key=>$item){
			if(preg_match('#level_id_(\d+)#', $key, $result)){
				if(isset($item)){
					$exist_in_request = 1;
					if($item){
						$tmp_level[$i] = $result;
						$last_catid = intval($item);
						$i++;
					}
				}
			}
		}
		if(!$last_catid && !$exist_in_request){
			$last_catid = $JLMS_SESSION->get('S_last_catid');
		}
		$JLMS_SESSION->set('S_last_catid', $last_catid);
		
		$tmp = array();
		$tmp = JLMS_quiz_admin_class::JLMS_multicats($last_catid, $tmp);
		$tmp = array_reverse($tmp);
		
		$tmp_pop = $tmp;
		$tmp_p = array_pop($tmp_pop);
		if(count($tmp) && $tmp_p->catid){
			$next = count($tmp);
			$tmp[$next] = new stdClass();
			$tmp[$next]->catid = 0;
			$tmp[$next]->parent = isset($tmp_p->catid)?$tmp_p->catid:0;
		}
	}
		
	/*
	$query = "SELECT * FROM #__lms_gqp_cats_config ORDER BY id";
	$JLMS_DB->setQuery($query);
	$levels = $JLMS_DB->loadObjectList();
	*/
	if(count($levels) == 0){
		for($i=0;$i<15;$i++){
			$num = $i + 1;
			if($i > 0){
//						$levels[$i]->cat_name = _JLMS_COURSES_COURSES_GROUPS;	
				$levels[$i]->cat_name = 'Level #'.$num;
			} else {
//						$levels[$i]->cat_name = _JLMS_COURSES_COURSES_GROUPS;
				$levels[$i]->cat_name = 'Level #'.$num;
			}
		}
	}
	$level_id = array();
	
	for($i=0;$i<count($levels);$i++){
		if($i == 0){
			$level_id[$i] = intval( mosGetParam( $_REQUEST, 'level_id_'.$i, 0 ) );
			$_REQUEST['level_id_'.$i] = $level_id[$i];
			$JLMS_SESSION->set('GQP_level_id_'.$i, $level_id[$i]);
		} else {
			$level_id[$i] = intval( mosGetParam( $_REQUEST, 'level_id_'.$i, $JLMS_SESSION->get('GQP_level_id_'.$i, 0) ) );
			$_REQUEST['level_id_'.$i] = $level_id[$i];
			$JLMS_SESSION->set('GQP_level_id_'.$i, $level_id[$i]);
		}
		
		if($i == 0){
			$parent_id[$i] = 0;
		} else {
			$parent_id[$i] = $level_id[$i-1];
		}
		$query = "SELECT count(id) FROM `#__lms_gqp_cats` WHERE parent = '".$parent_id[$i]."' ORDER BY c_category";
		$JLMS_DB->setQuery($query);
		$groups = $JLMS_DB->loadResult();
		if($groups==0){
			$level_id[$i] = 0;	
			$JLMS_SESSION->set('GQP_level_id_'.$i, $level_id[$i]);
		}
	}
	for($i=0;$i<count($levels);$i++){
		if($i > 0 && $level_id[$i - 1] == 0){
			$level_id[$i] = 0;
			$_REQUEST['level_id_'.$i] = $level_id[$i];
			$JLMS_SESSION->set('GQP_level_id_'.$i, $level_id[$i]);
			$parent_id[$i] = 0;
		} elseif($i == 0 && $level_id[$i] == 0) {
			$level_id[$i] = 0;
			$_REQUEST['level_id_'.$i] = $level_id[$i];
			$JLMS_SESSION->set('GQP_level_id_'.$i, $level_id[$i]);
			$parent_id[$i] = 0;
		}
	}
	
	for($i=0;$i<count($levels);$i++) {
		if($i == 0 || (isset($tmp[$i]->parent) && $tmp[$i]->parent)){ //(Max): extra requests
			$query = "SELECT * FROM `#__lms_gqp_cats` WHERE parent = '".$tmp[$i]->parent."' ORDER BY c_category";
			$JLMS_DB->setQuery($query);
			$groups = $JLMS_DB->loadObjectList();
			
			if($tmp[$i]->parent && $i > 0 && count($groups)) {
				$type_level[$i][] = mosHTML::makeOption( 0, '&nbsp;' );
				
				foreach ($groups as $group){
					$type_level[$i][] = mosHTML::makeOption( $group->id, $group->c_category );
				}
				$lists['level_'.$i] = mosHTML::selectList($type_level[$i], 'level_id_'.$i, 'class="inputbox" size="1" style="width:266px;" '.$javascript, 'value', 'text', $tmp[$i]->catid );
			} elseif($i == 0) {
				$type_level[$i][] = mosHTML::makeOption( 0, '&nbsp;' );
			
				foreach ($groups as $group){
					$type_level[$i][] = mosHTML::makeOption( $group->id, $group->c_category );
				}
				$lists['level_'.$i] = mosHTML::selectList($type_level[$i], 'level_id_'.$i, 'class="inputbox" size="1" style="width:266px;" '.$javascript, 'value', 'text', $tmp[$i]->catid );
			}
		}
	}
	
	$multicat = array();
	$i=0;
	foreach($lists as $key=>$item){
		if(substr($key, 0, 6) == 'level_'){
			$multicat[] = $lists['level_'.$i];
			$i++;
		}
	}
	$data = new stdClass();
	$i=0;
	foreach($multicat as $m){
		if(isset($level_id[$i])){
			$str_preobj = 'level_'.$i;
			$data->$str_preobj = $level_id[$i];
		}
		$i++;	
	}
	$lists['data'] = $data;
	//----------------------------------------------

	//echo $row->c_type; die;
	
	switch ($row->c_type) {
		case 1:
		case 12:
			if($row->c_type == 12){
				$query = "SELECT a.*, b.imgs_name FROM #__lms_quiz_t_choice as a, #__lms_quiz_images as b WHERE a.c_question_id = '".$row->c_id."' AND b.imgs_id = a.c_choice ORDER BY a.ordering";
			} else {
				$query = "SELECT * FROM #__lms_quiz_t_choice WHERE c_question_id = '".$row->c_id."' ORDER BY ordering";
				
				$lists['random_answers'] = mosHTML::yesnoradioList( 'params[random_answers]', '', $params->get('random_answers', 0) );
			}
			$JLMS_DB->SetQuery( $query );
			$row->choices = array();
			$row->choices = $JLMS_DB->LoadObjectList();
			if($row->c_type == 12){
				$query = "SELECT imgs_id, imgs_name, c_id as i_id FROM #__lms_quiz_images WHERE course_id = '".$course_id."'";
				$JLMS_DB->SetQuery( $query );
				$row->images = array();
				$row->images = $JLMS_DB->LoadObjectList();
			}
			$q_om_type = $row->c_type;
			
			JLMS_quiz_admin_html_class::JQ_editQuest_MChoice( $row, $lists, $option, $page, $course_id, $q_om_type, $qtype_str, $params, $id, $gqp, $levels );
		break;
		case 2:
		case 13:
			if($row->c_type == 13){	
				$query = "SELECT a.*, b.imgs_name FROM #__lms_quiz_t_choice as a, #__lms_quiz_images as b WHERE a.c_question_id = '".$row->c_id."' AND b.imgs_id = a.c_choice ORDER BY a.ordering";
			} else {
				$query = "SELECT * FROM #__lms_quiz_t_choice WHERE c_question_id = '".$row->c_id."' ORDER BY ordering";
				
				$lists['random_answers'] = mosHTML::yesnoradioList( 'params[random_answers]', '', $params->get('random_answers', 0) );
			}
			$JLMS_DB->SetQuery( $query );
			$row->choices = array();
			$row->choices = $JLMS_DB->LoadObjectList();
			if($row->c_type == 13){
				$query = "SELECT imgs_id, imgs_name, c_id as i_id FROM #__lms_quiz_images WHERE course_id = '".$course_id."'";
				$JLMS_DB->SetQuery( $query );
				$row->images = array();
				$row->images = $JLMS_DB->LoadObjectList();
			}
			$q_om_type = $row->c_type;
			JLMS_quiz_admin_html_class::JQ_editQuest_MChoice( $row, $lists, $option, $page, $course_id, $q_om_type, $qtype_str, $params, $id, $gqp, $levels );
		break;
		case 3:
			$query = "SELECT * FROM #__lms_quiz_t_choice WHERE c_question_id = '".$row->c_id."' ORDER BY ordering";
			$JLMS_DB->SetQuery( $query );
			$row->choices = array();
			$row->choices = $JLMS_DB->LoadObjectList();
			$row->choice_true = 1;
			foreach ($row->choices as $eee) {
				if ((strtolower($eee->c_choice) == "false") && ($eee->c_right == 1)) {
					$row->choice_true = 0;
				}
			}
			JLMS_quiz_admin_html_class::JQ_editQuest_TrueFalse( $row, $lists, $option, $page, $course_id, 3, $qtype_str, $params, $id, $gqp, $levels );
		break;
		case 4:
			$query = "SELECT * FROM #__lms_quiz_t_matching WHERE c_question_id = '".$row->c_id."' ORDER BY ordering";
			$JLMS_DB->SetQuery( $query );
			$row->matching = array();
			$row->matching = $JLMS_DB->LoadObjectList();
			JLMS_quiz_admin_html_class::JQ_editQuest_MDragDrop( $row, $lists, $option, $page, $course_id, 4, $qtype_str, $params, $id, $gqp, $levels );
		break;
		case 5:
			$query = "SELECT * FROM #__lms_quiz_t_matching WHERE c_question_id = '".$row->c_id."' ORDER BY ordering";
			$JLMS_DB->SetQuery( $query );
			$row->matching = array();
			$row->matching = $JLMS_DB->LoadObjectList();
			JLMS_quiz_admin_html_class::JQ_editQuest_MDragDrop( $row, $lists, $option, $page, $course_id, 5, $qtype_str, $params, $id, $gqp, $levels );
		break;
		case 6:
			$query = "SELECT b.*,a.c_default FROM #__lms_quiz_t_blank as a, #__lms_quiz_t_text as b WHERE a.c_question_id = '".$row->c_id."' and b.c_blank_id = a.c_id ORDER BY b.ordering";
			$JLMS_DB->SetQuery( $query );
			$row->blank_data = array();
			$row->blank_data = $JLMS_DB->LoadObjectList();
			$query = "SELECT c_default FROM #__lms_quiz_t_blank  WHERE c_question_id = '".$row->c_id."'";
			$JLMS_DB->SetQuery( $query );
			$lists['c_def'] = $JLMS_DB->LoadResult();
			JLMS_quiz_admin_html_class::JQ_editQuest_Blank( $row, $lists, $option, $page, $course_id, 6, $qtype_str, $params, $id, $gqp, $levels );
		break;
		case 7:

			$directory_js = $JLMS_CONFIG->get('live_site').'/images/joomlaquiz/images/';
			$directory = 'images/joomlaquiz/images/';
			$javascript = "onchange=\"javascript:if (document.adminForm.c_image.options[selectedIndex].value!='') {"
			. " document.imagelib.src='$directory' + document.adminForm.c_image.options[selectedIndex].value; } else {"
			. " document.imagelib.src='".$JLMS_CONFIG->get('live_site')."/images/blank.png'}\"";

			$imageFiles = mosReadDirectory( $JLMS_CONFIG->get('absolute_path') . '/' . $directory );
			$images 	= array(  mosHTML::makeOption( '', '- Select Image -' ) );
			foreach ( $imageFiles as $file ) {
				if ( preg_match( "/bmp|gif|jpg|png/i", $file ) ) {
					$images[] = mosHTML::makeOption( $file );
				}
			}
			$lists['images'] = mosHTML::selectList( $images, 'c_image', 'class="inputbox" size="1" '. $javascript, 'value', 'text', $row->c_image);

			//$lists['images'] = mosAdminMenus::images('c_image', $row->c_image, $javascript, $directory);

			$query = "SELECT * FROM #__lms_quiz_t_hotspot WHERE c_question_id = '".$row->c_id."'";
			$JLMS_DB->SetQuery( $query );
			$row->hotspot_data = array();
			$row->hotspot_data = $JLMS_DB->LoadObjectList();
			JLMS_quiz_admin_html_class::JQ_editQuest_HotSpot( $row, $lists, $option, $page, $course_id, 7, $qtype_str, $params, $id, $gqp, $levels );
		break;
		case 8:
			JLMS_quiz_admin_html_class::JQ_editQuest_Survey( $row, $lists, $option, $page, $course_id, 8, $qtype_str, $params, $id, $gqp, $levels );
		break;
		case 9:
			$query = "SELECT * FROM #__lms_quiz_t_scale WHERE c_question_id = '".$row->c_id."' ORDER BY ordering";
			$JLMS_DB->SetQuery( $query );
			$row->scale = array();
			$row->scale = $JLMS_DB->LoadObjectList();
			JLMS_quiz_admin_html_class::JQ_editQuest_Scale( $row, $lists, $option, $page, $course_id, 9, $qtype_str, $params, $id, $gqp, $levels );
		break;
		case 10:
			JLMS_quiz_admin_html_class::JQ_editQuest_Boilerplate( $row, $lists, $option, $page, $course_id, 10, $qtype_str, $params, $id, $gqp, $levels );
		break;
		case 11:
			$query = "SELECT a.*, b.imgs_name as left_name, c.imgs_name as right_name FROM #__lms_quiz_t_matching as a, #__lms_quiz_images as b, #__lms_quiz_images as c WHERE a.c_question_id = '".$row->c_id."' AND b.imgs_id = a.c_left_text AND c.imgs_id = a.c_right_text ORDER BY a.ordering";
			$JLMS_DB->SetQuery( $query );
			$row->matching = array();
			$row->matching = $JLMS_DB->LoadObjectList();
			$query = "SELECT imgs_id, imgs_name, c_id as i_id FROM #__lms_quiz_images WHERE course_id = '".$course_id."'";
			$JLMS_DB->SetQuery( $query );
			$row->images = array();
			$row->images = $JLMS_DB->LoadObjectList();
			JLMS_quiz_admin_html_class::JQ_editQuest_MDragDrop2( $row, $lists, $option, $page, $course_id, 11, $qtype_str, $params, $id, $gqp, $levels );			
		break;
		case 20: //add question from pool

			$query = "SELECT a.c_id AS value, a.c_question AS text"
			. "\n FROM #__lms_quiz_t_question AS a"
			. "\n WHERE a.course_id = '".$course_id."' "
			. "\n AND a.c_quiz_id = 0"
			. "\n ORDER BY a.ordering"
			;

			$qp_array = array();
			$JLMS_DB->setQuery( $query );
			$qp_list = $JLMS_DB->loadObjectList();

			$qp_array[] = mosHTML::makeOption( 0, '- Select question -' );

			for ($i=0, $n=count( $qp_list ); $i < $n; $i++) {
				if (strlen(strip_tags($qp_list[$i]->text)) > 30) {
					$text = substr(strip_tags($qp_list[$i]->text), 0, 30)."...";
				} else {
					$text = strip_tags($qp_list[$i]->text);
				}
				$qp_array[] = mosHTML::makeOption( $qp_list[$i]->value, $text );
			}

			$pool_quests = mosHTML::selectList( $qp_array, 'c_pool', 'class="inputbox" size="1"', 'value', 'text', intval( $row->c_pool ) );
			$lists['pool_quests'] = $pool_quests; 
			
			JLMS_quiz_admin_html_class::JQ_editQuest_Pool( $row, $lists, $option, $page, $course_id, 20, $qtype_str );
		break;
		
		case 21: //add question from pool
		
			if(!$row->c_id) {
				$limit = intval( mosGetParam( $_GET, 'limit', $JLMS_SESSION->get('list_limit', $JLMS_CONFIG->get('list_limit')) ) );
				$JLMS_SESSION->set('list_limit', $limit);
				$limitstart = intval( mosGetParam( $_REQUEST, 'limitstart', 0 ) );
				
				$qtype_id = mosGetParam($_REQUEST, 'qtype_id', 0);
			
				$query = "SELECT c_pool_gqp"
				. "\n FROM #__lms_quiz_t_question"
				. "\n WHERE c_pool_gqp > 0 AND c_quiz_id = $quiz_id"
				;
				$JLMS_DB->setQuery( $query );
				$result_array = $JLMS_DB->loadResultArray();
	
				$use_ids = implode(',', $result_array);
				if($use_ids)	
					$sql_use_ids = "\n AND a.c_id NOT IN ( $use_ids )";
				else 
					$sql_use_ids = '';
					
				$str = '';
				//NEW MUSLTICATS
				
				/*
				$tmp_level = array();
				$last_catid = 0;
				if(isset($_REQUEST['category_filter']) && $_REQUEST['category_filter']){
					$last_catid = $_REQUEST['category_filter'];
				} else {
					$i=0;
					foreach($_REQUEST as $key=>$item){
						if(preg_match('#level_id_(\d+)#', $key, $result)){
							if($item){
								$tmp_level[$i] = $result;
								$last_catid = $item;
								$i++;
							}	
						}	
					}
				}
				*/
				
				$query = "SELECT * FROM #__lms_gqp_cats"
				. "\n ORDER BY id";
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
				$catids = implode(",", $tmp_cats_filter);
				
				if($last_catid && count($tmp_cats_filter)){
					$str .= "\n AND a.c_qcat IN (".$catids.")";
				}
				//NEW MUSLTICATS
				
				/*Old kosmosa
				for ($i=count($level_id);$i>-1;$i--) {
					if(isset($level_id[$i]) && $level_id[$i]) {
						$str = "\n AND d.cat_id = ".$level_id[$i]." AND d.level = $i";
						break;
					}
				}
				*/
				
				$qp_array = array();
				
				$query = "SELECT a.*, b.c_qtype as qtype_full, qc1.c_category"
				. "\n FROM #__lms_quiz_t_question AS a"
				. "\n LEFT JOIN #__lms_quiz_t_qtypes b ON b.c_id = a.c_type"
				. "\n LEFT JOIN #__lms_quiz_t_category as qc ON a.c_qcat = qc.c_id"
//				. "\n LEFT JOIN #__lms_gqp_levels AS d ON d.quest_id = a.c_id"
				. "\n LEFT JOIN #__lms_gqp_cats AS qc1 ON a.c_qcat = qc1.id"			
				. "\n WHERE a.course_id = 0 "
				. "\n AND a.c_quiz_id = 0"
	//			. "\n AND d.quest_id = a.c_id AND d.cat_id =qc1.id "
				. "\n AND a.published = 1"		
				. ( $str ? $str : ' ')	
				. ( $sql_use_ids ? $sql_use_ids : ' ')
				.($qtype_id ? "\n AND a.c_type = '".$qtype_id."'" : '')
				. "\n GROUP BY a.c_id"
				. "\n ORDER BY a.ordering"
				;
				$JLMS_DB->setQuery($query);
				$JLMS_DB->query();
				
				$total = $JLMS_DB->getNumRows();
				
				require_once(_JOOMLMS_FRONT_HOME . DS . "includes" . DS . "classes" . DS . "lms.pagination.php");
				$pageNav = new JLMSPageNav( $total, $limitstart, $limit );
				
				$JLMS_DB->setQuery( $query, $pageNav->limitstart, $pageNav->limit );
				$rows = $JLMS_DB->loadObjectList();
				
				//Tooltip Right Answer (Max - 15.04.2011)
				for($i=0;$i<count($rows);$i++){
					if(in_array($rows[$i]->c_type, array(1, 2, 3))){
						
						$right_answer = '';
						
						$query = "SELECT c_choice"
						. "\n FROM"
						. "\n #__lms_quiz_t_choice"
						. "\n WHERE 1"
						. "\n AND c_right = 1"
						. "\n AND c_question_id = '".$rows[$i]->c_id."'"
						;
						$JLMS_DB->setQuery($query);
						$right_answer = $JLMS_DB->loadResult();
						
						if(strlen($right_answer)){
							$rows[$i]->right_answer = $right_answer;
						}
					} else 
					if(in_array($rows[$i]->c_type, array(20, 21))){
						if($rows[$i]->c_type == 20){
							$select_field = "c_pool";
						} else 
						if($rows[$i]->c_type == 21){
							$select_field = "c_pool_gqp";
						}
						$query = "SELECT ".$select_field
						. "\n FROM"
						. "\n #__lms_quiz_t_question"
						. "\n WHERE 1"
						. "\n AND c_id = '".$rows[$i]->c_id."'"
						;
						$JLMS_DB->setQuery($query);
						$question_id = $JLMS_DB->loadResult();
						
						$right_answer = '';
						
						if(intval($question_id)){
							$query = "SELECT b.c_choice"
							. "\n FROM"
							. "\n #__lms_quiz_t_question as a"
							. "\n, #__lms_quiz_t_choice as b"
							. "\n WHERE 1"
							. "\n AND a.c_id = '".$question_id."'"
							. "\n AND b.c_question_id = '".$question_id."'"
							. "\n AND a.c_type IN (".implode(',', array(1, 2, 3)).")"
							. "\n AND b.c_right = 1"
							;
							$JLMS_DB->setQuery($query);
							$right_answer = $JLMS_DB->loadResult();
						}
						if(strlen($right_answer)){
							$rows[$i]->right_answer = $right_answer;
						}
					}
				}
				//Tooltip Right Answer (Max - 15.04.2011)
				
				/*old kosmosa
				if(mosGetParam($_REQUEST,'filter_id_0') || isset($_SESSION['GQP_filter_id_0'])) {	
					for($i=0;$i<count($rows);$i++) {
						$new_level = $rows[$i]->level+1;
						$query = "SELECT b.c_category FROM #__lms_gqp_levels AS a, #__lms_gqp_cats AS b WHERE a.quest_id = '".$rows[$i]->c_id."' AND a.cat_id = b.id AND a.level = '".$new_level."'";
						$JLMS_DB->SetQuery($query);
						$cat_name = $JLMS_DB->LoadResult();
						if($cat_name) {
							$rows[$i]->c_category = $cat_name;
						}
					}
				}
				*/
				
	//			$qp_array[] = mosHTML::makeOption( 0, '- Select question -' );
	//			for ($i=0, $n=count( $rows ); $i < $n; $i++) {
	//				if (strlen($rows[$i]->text) > 30) {
	//					$text = substr($rows[$i]->text,0,30)."...";
	//				} else {
	//					$text = $qp_list[$i]->text;
	//				}
	//				$qp_array[] = mosHTML::makeOption( $qp_list[$i]->value, $text );
	//			}
	//			$pool_quests = mosHTML::selectList( $qp_array, 'c_pool', 'class="inputbox" size="1"', 'value', 'text', intval( $row->c_pool ) );
	//			$lists['pool_quests'] = $pool_quests; 
	
				$query = "SELECT c_id AS value, c_qtype AS text"
				. "\n FROM #__lms_quiz_t_qtypes"
				. "\n ORDER BY c_id"
				;
				$JLMS_DB->setQuery( $query );
				$qtypes_lang = $JLMS_DB->loadObjectList();
					
				for($i=0;$i<count($qtypes_lang);$i++) {
					$j=$i+1;
					$str = '_JLMS_QUIZ_QTYPE_'.$j;
					if (defined($str)) {
						$qtypes_lang[$i]->value = $j;
						$qtypes_lang[$i]->text = constant($str);
					}
				}
				$qtypes[] = mosHTML::makeOption( '0', _JLMS_SB_QUIZ_SELECT_QTYPE );
				$qtypes = array_merge( $qtypes, $qtypes_lang );
				$qtype = mosHTML::selectList( $qtypes, 'qtype_id', 'class="inputbox"  style="width:200px" size="1" '. $javascript, 'value', 'text', $qtype_id ); 
				$lists['qtype'] = $qtype;
				$lists['qtype_id'] = $qtype_id;
	
				$lists['published'] = mosHTML::yesnoradioList( 'published', '', 1 );
	
				JLMS_quiz_admin_html_class::JQ_editQuest_Pool_GQP( $row, $lists, $option, $page, $course_id, 21, $qtype_str, $rows, $pageNav, $levels );
			} else {
				
	//			$query = "SELECT a.c_id AS value, a.c_question AS text"
	//			. "\n FROM #__lms_quiz_t_question AS a "
	//			. "\n , #__lms_gqp_levels d, #__lms_gqp_cats qc1"			
	//			. "\n WHERE a.course_id = 0 "
	//			. "\n AND a.c_quiz_id = 0"
	//			. "\n AND d.quest_id = a.c_id AND d.cat_id =qc1.id "	
	//			. "\n GROUP BY d.quest_id"
	//			. "\n ORDER BY a.ordering"
	//			;
	//			
	//			$qp_array = array();
	//			$JLMS_DB->setQuery( $query );
	//			$qp_list = $JLMS_DB->loadObjectList();
	//					
	//			$qp_array[] = mosHTML::makeOption( 0, '- Select question -' );
	//
	//			for ($i=0, $n=count( $qp_list ); $i < $n; $i++) {
	//				if (strlen($qp_list[$i]->text) > 30) {
	//					$text = substr($qp_list[$i]->text,0,30)."...";
	//				} else {
	//					$text = $qp_list[$i]->text;
	//				}
	//				$qp_array[] = mosHTML::makeOption( $qp_list[$i]->value, $text );
	//			}
	//
	//			$pool_quests = mosHTML::selectList( $qp_array, 'c_pool', 'class="inputbox" size="1"', 'value', 'text', intval( $row->c_pool_gqp ) );
	//			$lists['pool_quests'] = $pool_quests; 
	//			
				JLMS_quiz_admin_html_class::JQ_editQuest_Pool_gqp_edit( $row, $lists, $option, $page, $course_id, 21, $qtype_str );
			}
		break;		
	}
}

function JLMS_multicats($last_catid, $tmp, $i=0){
	global $JLMS_DB;
	
	$query = "SELECT parent FROM #__lms_gqp_cats WHERE id = '".$last_catid."'";
	$JLMS_DB->setQuery($query);
	$parent = $JLMS_DB->loadResult();
	$tmp[$i] = new stdClass();
	$tmp[$i]->catid = $last_catid;
	$tmp[$i]->parent = isset($parent)?$parent:0;
	if($parent){
		$last_catid = $parent;
		$i++;
		$tmp = JLMS_quiz_admin_class::JLMS_multicats($last_catid, $tmp, $i);
	}
	return $tmp;
}

function JQ_helper_badText($str){
	if(jlms_UTF8string_check($str)){
		$str = str_replace(chr(226).chr(128).chr(153), "'", $str);
	} else {
		$str = str_replace(chr(146), "'", $str);
	}
	return $str;
}

function JQ_saveQuestion( $option, $page, $id, $gqp = false ) {
	global $JLMS_DB, $Itemid, $JLMS_CONFIG;
	
	if($gqp){
		//NEW version multicat
		$tmp_level = array();
		$last_catid = 0;
		$i=0;
		foreach($_REQUEST as $key=>$item){
			if(preg_match('#level_id_(\d+)#', $key, $result)){
				if($item){
					$tmp_level[$i] = $result;
					$last_catid = $item;
					$i++;
				}	
			}	
		}
		$_POST['c_qcat'] = $last_catid;
		//NEW version multicat
	}
	
	$row = new mos_JoomQuiz_Question( $JLMS_DB );
	if (!$row->bind( $_POST )) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}

	$is_new = false;
	if (!$row->c_id) {
		$is_new = true;
	}
	$is_pool = false;
	if ($row->c_quiz_id == -1) {
		$row->c_quiz_id = 0;
		$is_pool = true;
	}

	$JLMS_ACL = & JLMSFactory::getACL();
	if (!$JLMS_ACL->CheckPermissions('quizzes', 'manage')) {
		if ($JLMS_ACL->CheckPermissions('quizzes', 'manage_pool')) {
			if (!$is_pool) {
				JLMSRedirect( sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=quizzes&id=$course_id&page=quizzes") );
			} else {
				if (!$is_new) {
					$query = "SELECT c_quiz_id FROM #__lms_quiz_t_question WHERE c_id = $row->c_id";
					$JLMS_DB->SetQuery($query);
					$old_p = $JLMS_DB->LoadResult();
					if ($old_p) { // previously not a Pool question !!!
						JLMSRedirect( sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=quizzes&id=$course_id&page=quizzes") );
					}
				}
			}
		}
	}

	$row->course_id = $id;
	if ($row->c_type == 20 || $row->c_type == 21) {
		// Question from pool
	} else {
		$row->c_pool = 0;
	}
	$params = mosGetParam( $_POST, 'params', '' );
	$quest_params = '';
	if (is_array( $params )) {
		$txt = array();
		foreach ( $params as $k=>$v) {
			$txt[] = "$k=$v";
		}
		$quest_params = implode( "\n", $txt );
	}
	$row->params = $quest_params;
	$quest_params = new JLMSParameters($row->params);

	$row->c_question = strval(JLMS_getParam_LowFilter($_POST, 'c_question', ''));
	$row->c_question = JLMS_ProcessText_LowFilter($row->c_question);

	$row->c_explanation = strval(JLMS_getParam_LowFilter($_POST, 'c_explanation', ''));
	$row->c_explanation = JLMS_ProcessText_LowFilter($row->c_explanation);
	
	if (!$row->check()) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}
	
	// q_ordering == -1 - last
	// q_ordering == 0 - first
	$q_ordering = intval(mosGetParam($_REQUEST, 'q_ordering', 0));
	
	if(mosGetParam($_REQUEST,'c_type') == 21 && !$row->c_id) {
		foreach ($_REQUEST['cid'] as $k=>$v) {
			$row_cid = isset($_REQUEST['cid'][$k]) ? intval($_REQUEST['cid'][$k]) : 0;
			if($row_cid) {
				$query = "INSERT INTO #__lms_quiz_t_question (course_id, c_quiz_id, c_type, ordering, c_pool_gqp, published)"
				. "\n VALUES('".mosGetParam($_REQUEST,'id')."', '".mosGetParam($_REQUEST,'c_quiz_id')."', '".mosGetParam($_REQUEST,'c_type')."','".mosGetParam($_REQUEST,'q_ordering')."', $row_cid, '".mosGetParam($_REQUEST,'published')."' )";
				$JLMS_DB->SetQuery($query);
				$JLMS_DB->query();
				
			JLMS_quiz_admin_class::JQ_orderQuest_inside(mosGetParam($_REQUEST,'id'), mosGetParam($_REQUEST,'c_quiz_id'), $JLMS_DB->insertid(), $q_ordering);
				
			}
		}
	}	
	
	elseif(mosGetParam($_REQUEST,'c_type') == 21 && $row->c_id) {
		$query = "UPDATE #__lms_quiz_t_question SET c_pool_gqp='".mosGetParam($_REQUEST,'c_pool_gqp')."', c_quiz_id ='".mosGetParam($_REQUEST,'c_quiz_id')."' WHERE c_id = '".$row->c_id."'";	
		$JLMS_DB->setQuery( $query );
		$JLMS_DB->query();
		
		$qid = $row->c_id;
		JLMS_quiz_admin_class::JQ_orderQuest_inside(mosGetParam($_REQUEST,'id'), mosGetParam($_REQUEST,'c_quiz_id'), $row->c_id, $q_ordering);
	}
		
	else {
		if (!$row->store()) {
			echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
			exit();
		}
	
		$qid = $row->c_id;
		JLMS_quiz_admin_class::JQ_orderQuest_inside($id, $row->c_quiz_id, $qid, $q_ordering);
	}

	
	if($quest_params->get('survey_question'))
	{
		$query = "UPDATE #__lms_quiz_t_question SET c_point='0' where c_id='".$qid."'";	
		$JLMS_DB->setQuery( $query );
		$JLMS_DB->query();
	}
	
	if (($row->c_type == 1) || ($row->c_type == 12) || ($row->c_type == 2) || ($row->c_type == 13)) {
		
		$field_order = 0;
		$ans_right = array();
		if (isset($_REQUEST['jq_checked'])) {
			foreach ($_REQUEST['jq_checked'] as $sss) {
				$ans_right[] = $sss;
			}
		}
		if (isset($_POST['jq_hid_fields'])) {
			$fids_arr = array();
			$mcounter = 0;
			foreach ($_POST['jq_hid_fields'] as $f_row) {
				$new_field = new mos_JoomQuiz_ChoiceField( $JLMS_DB );
				if(intval($_POST['jq_hid_fields_ids'][$mcounter]))
						$new_field->c_id = intval($_POST['jq_hid_fields_ids'][$mcounter]);
				$new_field->c_question_id = $qid;
				$f_row_p = (get_magic_quotes_gpc()) ? stripslashes( $f_row ) : $f_row ;
				$f_row_p = JLMS_quiz_admin_class::JQ_helper_badText($f_row_p); //(Max):
				$new_field->c_choice = $f_row_p;
				$new_field->c_right = in_array(($field_order+ 1), $ans_right)?1:0;
				$new_field->ordering = $field_order;
				if (!$new_field->check()) { echo "<script> alert('".$new_field->getError()."'); window.history.go(-1); </script>\n"; exit(); }
				if (!$new_field->store()) { echo "<script> alert('".$new_field->getError()."'); window.history.go(-1); </script>\n"; exit(); }
				$fids_arr[] = $new_field->c_id;
				$field_order ++ ;
				$mcounter ++ ;
			}
			$fieldss = implode(',',$fids_arr);
			$query = "DELETE FROM #__lms_quiz_t_choice WHERE c_question_id = '".$qid."' AND c_id NOT IN (".$fieldss.")";
			$JLMS_DB->setQuery( $query );
			$JLMS_DB->query();
		}
		else 
		{
			$query = "DELETE FROM #__lms_quiz_t_choice WHERE c_question_id = '".$qid."'";
			$JLMS_DB->setQuery( $query );
			$JLMS_DB->query();
		}
	} elseif ($row->c_type == 3) {
		$query = "SELECT c_id,c_choice FROM #__lms_quiz_t_choice WHERE c_question_id = '".$qid."'";
		$JLMS_DB->setQuery( $query );
		$faltrue = $JLMS_DB->LoadObjectList();
		$field_order = 0;
		$ans_right = intval(mosGetParam($_REQUEST, 'znach', 1));
		$ans_true = 0;$ans_false = 0;
		if ($ans_right) { $ans_true = 1; } else { $ans_false = 1; }
		$new_field = new mos_JoomQuiz_ChoiceField( $JLMS_DB );
		if(count($faltrue))
		{
			if($faltrue[0]->c_choice == 'true')
				$new_field->c_id = $faltrue[0]->c_id;
			else
			if($faltrue[1]->c_choice == 'true')
				$new_field->c_id = $faltrue[1]->c_id;
		}
		$new_field->c_question_id = $qid;
		$new_field->c_choice = "true";
		$new_field->c_right = $ans_true;
		$new_field->ordering = 0;
		if (!$new_field->check()) { echo "<script> alert('".$new_field->getError()."'); window.history.go(-1); </script>\n"; exit(); }
		if (!$new_field->store()) { echo "<script> alert('".$new_field->getError()."'); window.history.go(-1); </script>\n"; exit(); }
		
		$new_field = new mos_JoomQuiz_ChoiceField( $JLMS_DB );
		if(count($faltrue))
		{
			if($faltrue[0]->c_choice == 'false')
				$new_field->c_id = $faltrue[0]->c_id;
			else
			if($faltrue[1]->c_choice == 'false')
				$new_field->c_id = $faltrue[1]->c_id;
		}
		$new_field->c_question_id = $qid;
		$new_field->c_choice = "false";
		$new_field->c_right = $ans_false;
		$new_field->ordering = 0;
		if (!$new_field->check()) { echo "<script> alert('".$new_field->getError()."'); window.history.go(-1); </script>\n"; exit(); }
		if (!$new_field->store()) { echo "<script> alert('".$new_field->getError()."'); window.history.go(-1); </script>\n"; exit(); }
	} elseif (($row->c_type == 4) || ($row->c_type == 5) || ($row->c_type == 11)) {
		$mcounter = 0;
		$fids_arr = array();
		$field_order = 0;
		if (isset($_POST['jq_hid_fields_left'])) {
			foreach ($_POST['jq_hid_fields_left'] as $f_row) {
				$new_field = new mos_JoomQuiz_MatchField( $JLMS_DB );
				$new_field->c_question_id = $qid;
				$f_row_p = (get_magic_quotes_gpc()) ? stripslashes( $f_row ) : $f_row ;
				
				$f_row_p = JLMS_quiz_admin_class::JQ_helper_badText($f_row_p); //(Max):
				
				$new_field->c_left_text = $f_row_p;
				$c_right_txt = (isset($_POST['jq_hid_fields_right'][$field_order])?$_POST['jq_hid_fields_right'][$field_order]:'');
				$c_right_txt = (get_magic_quotes_gpc()) ? stripslashes( $c_right_txt ) : $c_right_txt ;
				
				$c_right_txt = JLMS_quiz_admin_class::JQ_helper_badText($c_right_txt); //(Max):		
				
				$new_field->c_right_text = $c_right_txt;
				$new_field->ordering = $field_order;
				if (!$new_field->check()) { echo "<script> alert('".$new_field->getError()."'); window.history.go(-1); </script>\n"; exit(); }
				if (!$new_field->store()) { echo "<script> alert('".$new_field->getError()."'); window.history.go(-1); </script>\n"; exit(); }
				$fids_arr[] = $new_field->c_id;
				$field_order ++ ;
				$mcounter ++ ;
			}
			$fieldss = implode(',',$fids_arr);
			$query = "DELETE FROM #__lms_quiz_t_matching WHERE c_question_id = '".$qid."' AND c_id NOT IN (".$fieldss.")";
			$JLMS_DB->setQuery( $query );
			$JLMS_DB->query();
		}
		else 
		{
			$query = "DELETE FROM #__lms_quiz_t_matching WHERE c_question_id = '".$qid."'";
			$JLMS_DB->setQuery( $query );
			$JLMS_DB->query();
		}
	} elseif ($row->c_type == 6) {
		$JLMS_DB->SetQuery("SELECT c_id FROM #__lms_quiz_t_blank WHERE c_question_id = '".$qid."'");
		$bid = $JLMS_DB->LoadResult();
		if (!$bid) {
			$JLMS_DB->SetQuery("INSERT INTO #__lms_quiz_t_blank (c_question_id, c_default) VALUES('".$qid."','".mysql_real_escape_string($_POST['c_default'])."')");
			$JLMS_DB->query();
			$bid = $JLMS_DB->insertid();
		}
		else 
		{
			$JLMS_DB->SetQuery("UPDATE #__lms_quiz_t_blank SET c_default = '".mysql_real_escape_string($_POST['c_default'])."' WHERE c_question_id = '".$qid."'");
			$JLMS_DB->query();
			
		}
		
		$field_order = 0;
		$mcounter = 0;
		$fids_arr = array();
		if (isset($_POST['jq_hid_fields'])) {
			foreach ($_POST['jq_hid_fields'] as $f_row) {
				$new_field = new mos_JoomQuiz_BlankTextField( $JLMS_DB );
				if(intval($_POST['jq_hid_fields_ids'][$mcounter]))
						$new_field->c_id = intval($_POST['jq_hid_fields_ids'][$mcounter]);
				$new_field->c_blank_id = $bid;
				$f_row_p = (get_magic_quotes_gpc()) ? stripslashes( $f_row ) : $f_row ;
				$f_row_p = JLMS_quiz_admin_class::JQ_helper_badText($f_row_p); //(Max):
				$new_field->c_text = $f_row_p;
				$new_field->ordering = $field_order;
				if (!$new_field->check()) { echo "<script> alert('".$new_field->getError()."'); window.history.go(-1); </script>\n"; exit(); }
				if (!$new_field->store()) { echo "<script> alert('".$new_field->getError()."'); window.history.go(-1); </script>\n"; exit(); }
				$fids_arr[] = $new_field->c_id;
				$field_order ++ ;
				$mcounter ++ ;
			}
			$fieldss = implode(',',$fids_arr);
			$query = "DELETE FROM #__lms_quiz_t_text WHERE c_blank_id = '".$bid."' AND c_id NOT IN (".$fieldss.")";
			$JLMS_DB->setQuery( $query );
			$JLMS_DB->query();
		} else {
			$query = "DELETE FROM #__lms_quiz_t_text WHERE c_blank_id = '".$bid."'";
			$JLMS_DB->setQuery( $query );
			$JLMS_DB->query();
		}
	} elseif ($row->c_type == 9) {
		
		
		$field_order = 0;
		$mcounter = 0;
		$fids_arr = array();
		if (isset($_POST['jq_hid_fields'])) {
			foreach ($_POST['jq_hid_fields'] as $f_row) {
				$new_field = new mos_JoomQuiz_ScaleField( $JLMS_DB );
				if(intval($_POST['jq_hid_fields_ids'][$mcounter]))
						$new_field->c_id = intval($_POST['jq_hid_fields_ids'][$mcounter]);
				$new_field->c_question_id = $qid;
				$f_row_p = (get_magic_quotes_gpc()) ? stripslashes( $f_row ) : $f_row ;
				$f_row_p = JLMS_quiz_admin_class::JQ_helper_badText($f_row_p); //(Max):
				$new_field->c_field = $f_row_p;
				$new_field->ordering = $field_order;
				$new_field->c_type = 0;
				if (!$new_field->check()) { echo "<script> alert('".$new_field->getError()."'); window.history.go(-1); </script>\n"; exit(); }
				if (!$new_field->store()) { echo "<script> alert('".$new_field->getError()."'); window.history.go(-1); </script>\n"; exit(); }
				$fids_arr[] = $new_field->c_id;
				$field_order ++ ;
				$mcounter ++ ;
				
			}
			$fieldss = implode(',',$fids_arr);
			$query = "DELETE FROM #__lms_quiz_t_scale WHERE c_question_id = '".$qid."' AND c_type=0 AND c_id NOT IN (".$fieldss.")";
			$JLMS_DB->setQuery( $query );
			$JLMS_DB->query();
		}
		$field_order = 0;
		$mcounter = 0;
		$fids_arr2 = array();
		if (isset($_POST['jq_hid_fields_mark'])) {
			foreach ($_POST['jq_hid_fields_mark'] as $f_row) {
				$new_field = new mos_JoomQuiz_ScaleField( $JLMS_DB );
				if(intval($_POST['jq_hid_fields_mark_ids'][$mcounter]))
						$new_field->c_id = intval($_POST['jq_hid_fields_mark_ids'][$mcounter]);
				$new_field->c_question_id = $qid;
				$f_row_p = (get_magic_quotes_gpc()) ? stripslashes( $f_row ) : $f_row ;
				$f_row_p = JLMS_quiz_admin_class::JQ_helper_badText($f_row_p); //(Max):
				$new_field->c_field = $f_row_p;
				$new_field->ordering = $field_order;
				$new_field->c_type = 1;
				if (!$new_field->check()) { echo "<script> alert('".$new_field->getError()."'); window.history.go(-1); </script>\n"; exit(); }
				if (!$new_field->store()) { echo "<script> alert('".$new_field->getError()."'); window.history.go(-1); </script>\n"; exit(); }
				$fids_arr2[] = $new_field->c_id;
				$field_order ++ ;
				$mcounter ++ ;
			}
			$fieldss = implode(',',$fids_arr2);
			$query = "DELETE FROM #__lms_quiz_t_scale WHERE c_question_id = '".$qid."' AND c_type=1 AND c_id NOT IN (".$fieldss.")";
			$JLMS_DB->setQuery( $query );
			$JLMS_DB->query();
		}
	}
	
	if(isset($row->c_quiz_id) && $row->c_quiz_id){
		$query = "SELECT * FROM #__lms_quiz_t_quiz WHERE c_id = '".$row->c_quiz_id."'";
		$JLMS_DB->setQuery($query);
		$quiz_data = $JLMS_DB->loadObject();
	}
	if(isset($_REQUEST['c_right_message'])){
		$c_right_message = (get_magic_quotes_gpc()) ? stripslashes( $_REQUEST['c_right_message'] ) : $_REQUEST['c_right_message'];
	} else {
		$c_right_message = (get_magic_quotes_gpc()) ? stripslashes( $quiz_data->c_right_message ) : $quiz_data->c_right_message;
	}
	if(isset($_REQUEST['c_wrong_message'])){
		$c_wrong_message = (get_magic_quotes_gpc()) ? stripslashes( $_REQUEST['c_wrong_message'] ) : $_REQUEST['c_wrong_message'];
	} else {
		$c_wrong_message = (get_magic_quotes_gpc()) ? stripslashes( $quiz_data->c_wrong_message ) : $quiz_data->c_wrong_message;
	}
	
	if(isset($qid) && $qid) {	
		$query = "DELETE FROM #__lms_quiz_t_question_fb WHERE quest_id = $qid";
		$JLMS_DB->SetQuery($query);
		$JLMS_DB->query();
		$query = "INSERT INTO #__lms_quiz_t_question_fb (quest_id, choice_id, fb_text)"
			. "\n VALUES ($qid, -1, ".$JLMS_DB->Quote($c_wrong_message)."),"
			. "\n ($qid, 0, ".$JLMS_DB->Quote($c_right_message).")";
		$JLMS_DB->SetQuery($query);
		$JLMS_DB->query();
	}
	
	
	JLMS_quiz_admin_class::JQ_Calculate_Quiz_totalScore($row->c_quiz_id);
	
//	JLMS_quiz_admin_class::GQP_save_multicat($row->c_id); //sofranenie v tablicu levels, teper ne nugno
	
	
	if ($page == 'apply_quest') {
		JLMSRedirect( sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=quizzes&id=$id&page=editA_quest&c_id=". $row->c_id)) ;
	} elseif($page == 'apply_quest_gqp') {
		JLMSRedirect( sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=quizzes&page=editA_quest_gqp&c_id=". $row->c_id)) ;
	} else {
		if($gqp){
			JLMSRedirect( sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=quizzes&page=setup_gqp") );
		} else {
			JLMSRedirect( sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=quizzes&id=$id&page=setup_quest") );
		}
	}
}

function GQP_save_multicat($id){
	global $JLMS_DB;
	
	$query = "SELECT * FROM #__lms_gqp_levels WHERE quest_id = '".$id."'";
	$JLMS_DB->setQuery($query);
	$prover_mass = $JLMS_DB->loadObjectList();
	
	$save_mass = array();
	$i=0;
	
	foreach($_POST as $key=>$item){
//		if(substr($key, 0, 9) == 'level_id_' && $key != 'level_id_0'){
		if((substr($key, 0, 10) == 'filter_id_') || (substr($key, 0, 9) == 'level_id_')){
			if($item){
				$save_mass[$i]['id'] = isset($prover_mass[$i]->id)?$prover_mass[$i]->id:'';
				$save_mass[$i]['quest_id'] = $id;	
				$save_mass[$i]['cat_id'] = $item;	
				
				if(substr($key, 0, 10) == 'filter_id_') {
					$save_mass[$i]['level'] = (substr($key, 10))?substr($key, 10):substr($key, 10);	
				}
				elseif(substr($key, 0, 9) == 'level_id_') {
					$save_mass[$i]['level'] = (substr($key, 9))?substr($key, 9):substr($key, 9);	
				}
				
				$i++;
			}
		}	
	}
	
	foreach($save_mass as $data){
		$row = new mos_Joomla_GQP_Multicat( $JLMS_DB );
		if (!$row->bind( $data )) {
			echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
			exit();
		}
	
		if (!$row->check()) {
			echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
			exit();
		}
		
		if (!$row->store()) {
			echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
			exit();
		}
	}
	return;
}


function JQ_removeQuestion( &$cid, $option, $page, $course_id, $run_from_quiz_remove = 0, $gqp = false ) {
	global $JLMS_DB, $Itemid;
	if (count( $cid )) {
		$JLMS_ACL = & JLMSFactory::getACL();
		$pool_only = false;
		if (!$JLMS_ACL->CheckPermissions('quizzes', 'manage')) {
			if ($JLMS_ACL->CheckPermissions('quizzes', 'manage_pool')) {
				$pool_only = true;
			}
		}
		
		$cids = implode( ',', $cid );
		$query = "SELECT distinct c_quiz_id FROM #__lms_quiz_t_question WHERE c_id IN ( $cids ) AND course_id = '".$course_id."'";
		$JLMS_DB->SetQuery( $query );
		$ch_quizzes = $JLMS_DB->LoadObjectList();
		$query = "SELECT distinct c_id FROM #__lms_quiz_t_question WHERE c_id IN ( $cids ) AND course_id = '".$course_id."'".($pool_only?" AND c_quiz_id = 0":'');
		$JLMS_DB->setQuery( $query );
		$new_ids = $JLMS_DB->LoadResultArray();
		$query = "DELETE FROM #__lms_quiz_t_question"
		. "\n WHERE c_id IN ( $cids ) AND course_id = '".$course_id."'"
		;
		$JLMS_DB->setQuery( $query );
		$JLMS_DB->query();
		
		$query = "DELETE FROM #__lms_quiz_t_question"
		. "\n WHERE c_pool_gqp IN ( $cids ) AND c_pool_gqp <> 0"
		;
		$JLMS_DB->setQuery( $query );
		$JLMS_DB->query();
		
		if (!$JLMS_DB->query()) {
			echo "<script> alert('".$JLMS_DB->getErrorMsg()."'); window.history.go(-1); </script>\n";
			exit();
		} elseif (count($new_ids)) {
			$cids = implode(',',$new_ids);
			$query = "DELETE FROM #__lms_quiz_t_question_fb WHERE quest_id IN ( $cids )";
			$JLMS_DB->setQuery( $query );
			$JLMS_DB->query();
			$query = "DELETE FROM #__lms_quiz_t_choice WHERE c_question_id IN ( $cids )";
			$JLMS_DB->setQuery( $query );
			$JLMS_DB->query();
			$query = "DELETE FROM #__lms_quiz_t_hotspot WHERE c_question_id IN ( $cids )";
			$JLMS_DB->setQuery( $query );
			$JLMS_DB->query();
			$query = "DELETE FROM #__lms_quiz_t_matching WHERE c_question_id IN ( $cids )";
			$JLMS_DB->setQuery( $query );
			$JLMS_DB->query();
			$query = "SELECT c_id FROM #__lms_quiz_t_blank WHERE c_question_id IN ( $cids )";
			$JLMS_DB->SetQuery( $query );
			$blank_cid = $JLMS_DB->LoadResultArray();
			$query = "DELETE FROM #__lms_quiz_t_blank WHERE c_question_id IN ( $cids )";
			$JLMS_DB->setQuery( $query );
			$JLMS_DB->query();
			if (is_array( $blank_cid ) && (count($blank_cid) > 0)) {
				$blank_cids = implode( ',', $blank_cid );
				$query = "DELETE FROM #__lms_quiz_t_text"
				. "\n WHERE c_blank_id IN ( $blank_cids )"
				;
				$JLMS_DB->setQuery( $query );
				$JLMS_DB->query();
			}
		}
		
		if($gqp) {
			/*
			$query = "DELETE FROM #__lms_gqp_levels WHERE quest_id IN ( $cids )";
			$JLMS_DB->setQuery( $query );
			$JLMS_DB->query();
			*/
		}
		
		//recalculate quizzes TotalScore
		if (count($ch_quizzes) && !$pool_only && !$gqp) {
			foreach ($ch_quizzes as $c_q) {
				JLMS_quiz_admin_class::JQ_Calculate_Quiz_totalScore($c_q->c_quiz_id);
			}
		}
	}
	if (!$run_from_quiz_remove) {
		
		if(!$gqp) {
			JLMSRedirect( sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=quizzes&id=$course_id&page=setup_quest") );
		}
		else {
			JLMSRedirect( sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=quizzes&page=setup_gqp") );
		}
	}
}
function JQ_cancelQuestion( $option, $page, $id, $gqp = false ) {
	global $Itemid;
	
	if(!$gqp) {
		JLMSRedirect( sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=quizzes&id=$id&page=setup_quest") );
	}
	else {
		JLMSRedirect( sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=quizzes&page=setup_gqp") );
	}
}

// last updated - 17 August 2007 (DEN)
function JQ_orderQuest_new( $option, $page, $course_id) {
	global $JLMS_DB, $my, $Itemid;
	//if ((JLMS_GetUserType($my->id, $course_id) == 1)) {
		$order_id = intval(mosGetParam($_REQUEST, 'row_id', 0));
		$query = "SELECT c_quiz_id FROM #__lms_quiz_t_question WHERE c_id = '".$order_id."' AND course_id = '".$course_id."'";
		$JLMS_DB->SetQuery( $query );
		$c_quiz = $JLMS_DB->LoadResult();
		if ($c_quiz !== null) { // make sure that question is from our course
			$JLMS_ACL = & JLMSFactory::getACL();
			if (!$JLMS_ACL->CheckPermissions('quizzes', 'manage')) {
				if ($JLMS_ACL->CheckPermissions('quizzes', 'manage_pool')) {
					if ($c_quiz) { // not a pool
						JLMSRedirect( sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=quizzes&id=$course_id&page=setup_quest") );
					}
				}
			}
			$query = "SELECT c_id, ordering FROM #__lms_quiz_t_question WHERE course_id = '".$course_id."' AND c_quiz_id = '".$c_quiz."' ORDER BY ordering, c_id";
			$JLMS_DB->SetQuery( $query );
			$id_array_obj = $JLMS_DB->LoadObjectList();
			$id_array = array();
			$id_ord_array = array();
			foreach ($id_array_obj as $iao) {
				$id_array[] = $iao->c_id;
				$id_ord_array[$iao->c_id] = $iao->ordering;
			}
			if (count($id_array)) {
				$i = 0;$j = 0;
				while ($i < (count($id_array)-1)) {
					if ($id_array[$i] == $order_id) { $j = $i;}
					$i++;
				}
				$do_update = true; // deprecated :)
				if (($page == 'quest_orderup') && ($j) && isset($id_array[$j-1]) ) {
					$tmp = $id_array[$j-1];
					$id_array[$j-1] = $id_array[$j];
					$id_array[$j] = $tmp;
				} elseif (($page == 'quest_orderdown') && ($j < (count($id_array)-1) && isset($id_array[$j+1])) ) {
					$tmp = $id_array[$j+1];
					$id_array[$j+1] = $id_array[$j];
					$id_array[$j] = $tmp;
				}
				if ($do_update) {
					$i = 0;
					foreach ($id_array as $quest_id) {
						if (isset($id_ord_array[$quest_id]) && ($id_ord_array[$quest_id] == ($i + 1)) ) {
							
						} else {
							$query = "UPDATE #__lms_quiz_t_question SET ordering = '".($i+1)."' WHERE c_id = '".$quest_id."' and course_id = '".$course_id."'";
							$JLMS_DB->SetQuery( $query );
							$JLMS_DB->query();
						}
						$i ++;
					}
				}
			}
		}
	//}
	JLMSRedirect( sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=quizzes&id=$course_id&page=setup_quest") );
}

// quest_order == -1 - last
// quest_order == 0 - first
// last updated - 17 August 2007 (DEN)
function JQ_orderQuest_inside( $course_id, $c_quiz, $order_id, $quest_order ) {
	global $JLMS_DB, $my, $Itemid;
	if ($c_quiz) {
		$JLMS_ACL = & JLMSFactory::getACL();
		if (!$JLMS_ACL->CheckPermissions('quizzes', 'manage')) {
			if ($JLMS_ACL->CheckPermissions('quizzes', 'manage_pool')) {
				JLMSRedirect( sefRelToAbs("index.php?option=com_joomla_lms&Itemid=$Itemid&task=quizzes&id=$course_id&page=setup_quest") );
			}
		}
	}
	$query = "SELECT c_id, ordering FROM #__lms_quiz_t_question WHERE course_id = '".$course_id."' AND c_quiz_id = '".$c_quiz."' ORDER BY ordering, c_id";
	$JLMS_DB->SetQuery( $query );
	$id_array_obj = $JLMS_DB->LoadObjectList();
	$id_array = array();
	$id_ord_array = array();
	foreach ($id_array_obj as $iao) {
		$id_array[] = $iao->c_id;
		$id_ord_array[$iao->c_id] = $iao->ordering;
	}
	if (count($id_array)) {
		if ($quest_order && ($quest_order != -1)) {
			// we need to find edited question and question by which oredering is setted
			$finded = false;
			$new_id_array = array();
			foreach ($id_array as $ia) {
				if ($ia == $quest_order) {
					$new_id_array[] = $order_id;
					$new_id_array[] = $quest_order;
					$finded = true;
				} elseif ($ia == $order_id) {
					
				} else {
					$new_id_array[] = $ia;
				}
			}
			if (!$finded) {
				$new_id_array[] = $order_id;
			}
			$id_array = $new_id_array;
		} elseif (!$quest_order) {
			//place editable question as first item
			if (isset($id_array[0]) && $id_array[0] != $order_id) {
				$new_id_array = array();
				$new_id_array[] = $order_id;
				foreach ($id_array as $ia) {
					if ($ia != $order_id) {
						$new_id_array[] = $ia;
					}
				}
				$id_array = $new_id_array;
			}
		} elseif ($quest_order == -1) {
			//place editable question as last item
			$new_id_array = array();
			foreach ($id_array as $ia) {
				if ($ia != $order_id) {
					$new_id_array[] = $ia;
				}
			}
			$new_id_array[] = $order_id;
			$id_array = $new_id_array;
		}
		// updating
		$i = 0;
		foreach ($id_array as $quest_id) {
			if (isset($id_ord_array[$quest_id]) && ($id_ord_array[$quest_id] == ($i + 1)) ) {
				
			} else {
				$query = "UPDATE #__lms_quiz_t_question SET ordering = '".($i+1)."' WHERE c_id = '".$quest_id."' and course_id = '".$course_id."'";
				$JLMS_DB->SetQuery( $query );
				$JLMS_DB->query();
			}
			$i ++;
		}
	}
}

// last updated - 18 August 2007 (DEN)
function JQ_orderAllQuests( $option, $page, $course_id ) {
	
	global $JLMS_DB, $my, $Itemid;
		
		$cid = mosGetParam( $_POST, 'cid', array(0) );
		
		$order = mosGetParam( $_POST, 'order', array(0) );
		$quiz_id = intval(mosGetParam( $_POST, 'quiz_id', 0 ));
		if ($quiz_id) {
			$JLMS_ACL = & JLMSFactory::getACL();
			if (!$JLMS_ACL->CheckPermissions('quizzes', 'manage')) {
				if ($JLMS_ACL->CheckPermissions('quizzes', 'manage_pool')) {
					JLMSRedirect( sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=quizzes&id=$course_id&page=setup_quest") );
				}
			}
		}
		if (is_array( $cid ) && !empty($cid) && is_array( $order ) && !empty($order) && (count($cid) == count($order)) && $quiz_id ) {
			$total		= count( $cid );
			$order 		= mosGetParam( $_POST, 'order', array(0) );
			$query = "SELECT c_id, ordering FROM #__lms_quiz_t_question WHERE course_id = '".$course_id."' AND c_quiz_id = '".(($quiz_id == -1) ? 0 : $quiz_id)."' ORDER BY ordering, c_id";
			$JLMS_DB->SetQuery( $query );
			$id_array_obj = $JLMS_DB->LoadObjectList();
			$id_ord_array = array();
			foreach ($id_array_obj as $iao) {
				$id_ord_array[$iao->c_id] = $iao->ordering;
			}
			for( $i=0; $i < $total; $i++ ) {
				$quest_id = intval($cid[$i]);
				if ($quest_id) {
					if (isset($id_ord_array[$quest_id]) && ($id_ord_array[$quest_id] == (intval($order[$i]))) ) {
					
					} else {
						$query = "UPDATE #__lms_quiz_t_question SET ordering = '".intval($order[$i])."' WHERE c_id = '".$quest_id."'"
						. "\n AND c_quiz_id = ".(($quiz_id == -1) ? 0 : $quiz_id)." AND course_id = '".$course_id."'";
						$JLMS_DB->SetQuery( $query );
						$JLMS_DB->query();
					}
				}
			}
		}
	JLMSRedirect( sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=quizzes&id=$course_id&page=setup_quest") );
}

function JQ_orderQuestion( $id, $inc, $option ) {
	global $JLMS_DB;

	$limit 		= intval( mosGetParam( $_REQUEST, 'limit', 0 ) );
	$limitstart = intval( mosGetParam( $_REQUEST, 'limitstart', 0 ) );
	$quiz_id 	= intval( mosGetParam( $_REQUEST, 'quiz_id', 0 ) );

	$row = new mos_JoomQuiz_Question( $JLMS_DB );
	$row->load( $id );
	$row->move( $inc );
	$msg 	= 'New question order was saved.';
	JLMSRedirect( 'index.php?tmpl=component&option='. $option . '&task=setup_quest', $msg );
}
function JQ_saveOrderQuestion( &$cid ) {
	global $JLMS_DB, $option;

	$total		= count( $cid );
	$order 		= mosGetParam( $_POST, 'order', array(0) );
	$row 		= new mos_JoomQuiz_Question( $JLMS_DB );
	$conditions = array();

	for( $i=0; $i < $total; $i++ ) {
		$row->load( $cid[$i] );
		if ($row->ordering != $order[$i]) {
			$row->ordering = $order[$i];
			if (!$row->store()) {
				echo "<script> alert('".$JLMS_DB->getErrorMsg()."'); window.history.go(-1); </script>\n";
				exit();
			}
		}
	}
	$msg 	= 'New question order was saved.';
	JLMSRedirect( 'index.php?tmpl=component&option='.$option.'&task=setup_quest', $msg );
}

function JQ_moveQuestionSelect( $option, $page, $course_id, $cid, $gqp = '', $page_button = '' ) {
	global $JLMS_DB, $Itemid, $JLMS_SESSION, $my;
	$JLMS_ACL = & JLMSFactory::getACL();
	if ($JLMS_ACL->CheckPermissions('quizzes', 'manage') || ($page == 'copy_quest_sel' && $JLMS_ACL->CheckPermissions('quizzes', 'manage_pool'))) { // only if we can manage quizzes (not only manage_pool)
		if (!is_array( $cid ) || count( $cid ) < 1) {
			echo "<script> alert('Select an item to move'); window.history.go(-1);</script>\n";
			exit;
		}

	//-------------------------------------------------------------------
	$JLMS_ACL = & JLMSFactory::getACL();
	$usertype_simple = $JLMS_ACL->_role_type;
//	$usertype_simple = JLMS_GetUserType_simple($my->id, false, true);
	
	//FLMS multicat
	$levels = array();
	$lists = array();
	if ($gqp) {
		/*
		$query = "SELECT * FROM #__lms_gqp_cats_config ORDER BY id";
		$JLMS_DB->setQuery($query);
		$levels = $JLMS_DB->loadObjectList();
		*/
		if(count($levels) == 0){
			for($i=0;$i<15;$i++){
				$num = $i + 1;
				if($i>0){
//					$levels[$i]->cat_name = _JLMS_COURSES_COURSES_GROUPS;
					$levels[$i]->cat_name = 'Level #'.$num;	
				} else {
//					$levels[$i]->cat_name = _JLMS_COURSES_COURSES_GROUPS;
					$levels[$i]->cat_name = 'Level #'.$num;
				}
			}
		}

		$level_id = array();
		for($i=0;$i<count($levels);$i++){
			if($i == 0){
				$level_id[$i] = intval( mosGetParam( $_REQUEST, 'filter_id_'.$i.'', $JLMS_SESSION->get('GQP_filter_id_'.$i.'', 0) ) );
				$JLMS_SESSION->set('GQP_filter_id_'.$i.'', $level_id[$i]);
			} else {
				$level_id[$i] = intval( mosGetParam( $_REQUEST, 'filter_id_'.$i.'', $JLMS_SESSION->get('GQP_filter_id_'.$i.'', 0) ) );
				$JLMS_SESSION->set('GQP_filter_id_'.$i.'', $level_id[$i]);
			}
			if($i == 0){
				$parent_id[$i] = 0;
			} else {
				$parent_id[$i] = $level_id[$i-1];
			}
			if($i == 0 || $parent_id[$i]){ //(Max): extra requests
				$query = "SELECT count(id) FROM `#__lms_gqp_cats` WHERE parent = '".$parent_id[$i]."' ORDER BY c_category";
				$JLMS_DB->setQuery($query);
				$groups = $JLMS_DB->loadResult();
				if($groups==0){
					$level_id[$i] = 0;	
					$JLMS_SESSION->set('GQP_filter_id_'.$i.'', $level_id[$i]);
				}
			}
		}
		
		for($i=0;$i<count($levels);$i++){
			if($i > 0 && $level_id[$i - 1] == 0){
				$level_id[$i] = 0;
				$JLMS_SESSION->set('GQP_filter_id_'.$i.'', $level_id[$i]);
				$parent_id[$i] = 0;
			} elseif($i == 0 && $level_id[$i] == 0) {
				$level_id[$i] = 0;
				$JLMS_SESSION->set('GQP_filter_id_'.$i.'', $level_id[$i]);
				$parent_id[$i] = 0;
			}
		}
		
		$javascript = 'onclick="javascript:read_filter();" onchange="javascript:write_filter();document.adminForm.page.value=\''.$page_button.'\';document.adminForm.submit();"';
		
		$query1 = "SELECT group_id FROM `#__lms_users_in_global_groups` WHERE user_id = '".$my->id."'";
		$JLMS_DB->setQuery($query1);
		$user_group_ids = $JLMS_DB->loadResultArray();
		
		for($i=0;$i<count($levels);$i++) {
			if($i == 0 || $parent_id[$i]){ //(Max): extra requests
				if( $parent_id[$i] == 0 && $usertype_simple == 1) { //(Max): roletype_id
					$query = "SELECT * FROM `#__lms_gqp_cats` WHERE `parent` = '0'";
					$query .= "\n ORDER BY `c_category`";
				}
				else {
					$query = "SELECT * FROM `#__lms_gqp_cats` WHERE parent = '".$parent_id[$i]."' ORDER BY c_category";
				}
				
				$JLMS_DB->setQuery($query);
				$groups = $JLMS_DB->loadObjectList();
				
				if($parent_id[$i] && $i > 0 && count($groups)) {
					$type_level[$i][] = mosHTML::makeOption( 0, ' &nbsp; ' );
					foreach ($groups as $group){
						$type_level[$i][] = mosHTML::makeOption( $group->id, $group->c_category );
					}
					$lists['filter_'.$i.''] = mosHTML::selectList($type_level[$i], 'filter_id_'.$i.'', 'class="inputbox" style="width: 266px;" size="1" '.$javascript, 'value', 'text', $level_id[$i] ); //onchange="document.location.href=\''. $link_multi .'\';"
				} elseif($i == 0) {
					$type_level[$i][] = mosHTML::makeOption( 0, ' &nbsp; ' );
					foreach ($groups as $group){
						$type_level[$i][] = mosHTML::makeOption( $group->id, $group->c_category );
					}
					$lists['filter_'.$i.''] = mosHTML::selectList($type_level[$i], 'filter_id_'.$i.'', 'class="inputbox" style="width: 266px;" size="1" '.$javascript, 'value', 'text', $level_id[$i] ); //onchange="document.location.href=\''. $link_multi .'\';"
				}
			}
		}	
	}
	
	//-------------------------------------------------------------------	

		$cids = implode( ',', $cid );
		if($gqp) {
			/*
		$query = "SELECT a.*, b.c_qtype as qtype_full, c.c_title as quiz_name, qc.c_category"
		. "\n FROM #__lms_quiz_t_question a LEFT JOIN #__lms_quiz_t_qtypes b ON b.c_id = a.c_type LEFT JOIN #__lms_quiz_t_quiz c ON a.c_quiz_id = c.c_id AND c.course_id = '".$id."'"
		. "\n LEFT JOIN #__lms_quiz_t_category as qc ON a.c_qcat = qc.c_id AND qc.course_id = '".$id."' AND qc.is_quiz_cat = 0"
		. "\n WHERE a.course_id = '".$id."'"
		
$query = "SELECT b.c_category FROM #__lms_gqp_levels AS a, #__lms_gqp_cats AS b WHERE a.quest_id = '".$rows[$i]->c_id."' AND a.cat_id = b.id AND a.level = '".$new_level."'";
			*/
			$query = "SELECT a.c_question, c.c_category as quiz_name"
			. "\n FROM #__lms_quiz_t_question AS a"
			. "\n LEFT JOIN #__lms_gqp_cats AS c ON a.c_qcat = c.id"
			. "\n WHERE a.c_id IN ( $cids ) AND a.course_id = 0  AND a.c_quiz_id = 0" //AND a.c_id = b.quest_id AND c.id = b.cat_id
			. "\n GROUP BY a.c_id"
			;
		}
		else {
			$query = "SELECT a.c_question, b.c_title as quiz_name"
			. "\n FROM #__lms_quiz_t_question AS a LEFT JOIN #__lms_quiz_t_quiz AS b ON b.c_id = a.c_quiz_id AND b.course_id = '".$course_id."'"
			. "\n WHERE a.c_id IN ( $cids ) AND a.course_id = '".$course_id."'".($JLMS_ACL->CheckPermissions('quizzes', 'manage') ? '' : " AND a.c_quiz_id = 0 ")
			;
		}
		$JLMS_DB->setQuery( $query );
		$items = $JLMS_DB->loadObjectList();
		
//		echo '<pre>';
//		print_r($items);
//		echo '</pre>';
		
		if($gqp) {
			/*		
			for($i=0;$i<count($items);$i++) {
				$query = "SELECT b.c_category FROM #__lms_gqp_levels AS a, #__lms_gqp_cats AS b WHERE a.quest_id = '".$items[$i]->quest_id."' AND a.cat_id = b.id ORDER BY a.level desc LIMIT 1";
				$JLMS_DB->setQuery( $query );
				$items[$i]->quiz_name = $JLMS_DB->loadResult();
			}
			*/
		}

		$quizzes = array();
		$quizzes[] = mosHTML::makeOption( '0', _JLMS_SB_SELECT_QUIZ );
		$quizzes[] = mosHTML::makeOption( '-1', _JLMS_QUIZ_QUEST_POOL );
		if ($JLMS_ACL->CheckPermissions('quizzes', 'manage')) {
			$query = "SELECT a.c_title AS text, a.c_id AS value"
			. "\n FROM #__lms_quiz_t_quiz AS a"
			. "\n WHERE course_id = '".$course_id."'"
			. "\n ORDER BY a.c_title"
			;
			$JLMS_DB->setQuery( $query );
			$quizzes = array_merge( $quizzes, $JLMS_DB->loadObjectList() );
		}
		
		$QuizList = mosHTML::selectList( $quizzes, 'quizmove', 'class="inputbox" size="1"', 'value', 'text', 0 );
	
		JLMS_quiz_admin_html_class::JQ_moveQuest_Select( $option, $page, $course_id, $cid, $QuizList, $items, $gqp, $levels, $lists );
	} else {
		JLMSRedirect( sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=quizzes&id=$course_id&page=setup_quest") );
	}
}
function JQ_moveQuestionSave( $option, $page, $course_id, $cid, $gqp ) {
	global $JLMS_DB, $Itemid;
	$JLMS_ACL = & JLMSFactory::getACL();
	if ($JLMS_ACL->CheckPermissions('quizzes', 'manage')) { // only if we can manage quizzes (not only manage_pool)
		$quizMove = strval( mosGetParam( $_REQUEST, 'quizmove', '' ) );
		$cids = implode( ',', $cid );
		$total = count( $cid );

		if($gqp) {
			$course_id = 0;
		}
		
		//NEW version multicat
		$tmp_level = array();
		$last_catid = 0;
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
		//NEW version multicat

		$query = "SELECT distinct c_quiz_id, c_id FROM #__lms_quiz_t_question WHERE c_id IN ( $cids ) AND course_id = '".$course_id."'";
		$JLMS_DB->SetQuery( $query );
		$ch_quizzes = $JLMS_DB->LoadObjectList();
		
		if($gqp) {
			$query = "UPDATE #__lms_quiz_t_question"
			. "\n SET c_qcat = '".$last_catid."'"
			. "WHERE c_id IN ( $cids ) AND course_id = '".$course_id."'"
			;
			$JLMS_DB->setQuery( $query );
		
			if ( !$JLMS_DB->query() ) {
				echo "<script> alert('". $JLMS_DB->getErrorMsg() ."'); window.history.go(-1); </script>\n";
				exit();
			}
			/*
			$query = "DELETE FROM #__lms_gqp_levels WHERE quest_id IN ( $cids )";
			$JLMS_DB->setQuery($query);
			$JLMS_DB->query();
			for($i=0;$i<count($ch_quizzes);$i++) {
				JLMS_quiz_admin_class::GQP_save_multicat($ch_quizzes[$i]->c_id);
			}
			*/
		}
		else {
			$query = "UPDATE #__lms_quiz_t_question"
			. "\n SET c_quiz_id = '$quizMove'"
			. "WHERE c_id IN ( $cids ) AND course_id = '".$course_id."'"
			;
			$JLMS_DB->setQuery( $query );
		
			if ( !$JLMS_DB->query() ) {
				echo "<script> alert('". $JLMS_DB->getErrorMsg() ."'); window.history.go(-1); </script>\n";
				exit();
			}
		}
		//re-calculate quizzes TotalScore
		if (count($ch_quizzes)) {
			foreach ($ch_quizzes as $c_q) {
				JLMS_quiz_admin_class::JQ_Calculate_Quiz_totalScore($c_q->c_quiz_id);
			}
		}
		JLMS_quiz_admin_class::JQ_Calculate_Quiz_totalScore($quizMove);
		$quizNew = new mos_JoomQuiz_Quiz ( $JLMS_DB );
		$quizNew->load( $quizMove );
	}

	if(!$gqp) {
		JLMSRedirect( sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=quizzes&id=$course_id&page=setup_quest") );
	}
	else {
		JLMSRedirect( sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=quizzes&page=setup_gqp") );
	}

}
function JQ_copyQuestionSave( $option, $page, $course_id, $cid, $run_from_quiz_copy = 0, $quizMove = 0, $gqp = false, $gqp_move = false ) {
	global $JLMS_DB, $Itemid;

	$total = 0;
	if (!$run_from_quiz_copy) {
		$quizMove = intval( mosGetParam( $_REQUEST, 'quizmove', 0 ) );
		$JLMS_ACL = & JLMSFactory::getACL();
		if (!$JLMS_ACL->CheckPermissions('quizzes', 'manage')) {
			if ($JLMS_ACL->CheckPermissions('quizzes', 'manage_pool')) {
				$quizMove = 0;
			}
		}
	}
	$cids = implode( ',', $cid );

	if($gqp) {
		$course_id = 0;
	}
	
	//NEW version multicat
	$tmp_level = array();
	$last_catid = 0;
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
	//NEW version multicat

	$total = count( $cid );
	$query = "SELECT * FROM #__lms_quiz_t_question WHERE c_id IN ( $cids ) AND course_id = '".$course_id."' ORDER BY ordering";
	$JLMS_DB->setQuery( $query );
	$quests_to_copy = $JLMS_DB->loadAssocList();

	$query = "SELECT * FROM #__lms_quiz_t_question_fb WHERE quest_id IN ( $cids ) ORDER BY quest_id";
	$JLMS_DB->setQuery( $query );
	$quests_fb_to_copy = $JLMS_DB->loadAssocList();
	$new_order = 0;
	foreach ($quests_to_copy as $quest2copy) {

		$old_quest_id = $quest2copy['c_id'];
		
		$quest2copy['c_qcat'] = $last_catid;

		if($gqp && $gqp_move) {
			/*
			$query = "DELETE FROM #__lms_gqp_levels WHERE quest_id = '".$old_quest_id."'";
			$JLMS_DB->setQuery($query);
			$JLMS_DB->query();
			*/
		}	

		$new_quest = new mos_JoomQuiz_Question( $JLMS_DB );
		if (!$new_quest->bind( $quest2copy )) { echo "<script> alert('".$new_quest->getError()."'); window.history.go(-1); </script>\n"; exit(); }
		$new_quest->c_id = 0; $new_quest->ordering = $new_order; $new_quest->c_quiz_id = $quizMove;
		$new_quest->course_id = $course_id;
		if ($run_from_quiz_copy) { $new_order++; }
		if (!$new_quest->check()) { echo "<script> alert('".$new_quest->getError()."'); window.history.go(-1); </script>\n"; exit(); }
		if (!$new_quest->store()) { echo "<script> alert('".$new_quest->getError()."'); window.history.go(-1); </script>\n"; exit(); }
		
		$new_quest_id = $new_quest->c_id;
		
		foreach ($quests_fb_to_copy as $questfb2copy) {
			if ($questfb2copy['quest_id'] == $old_quest_id && (intval($questfb2copy['choice_id']) == 0 || intval($questfb2copy['choice_id']) == -1)) {
				$query = "INSERT INTO #__lms_quiz_t_question_fb (quest_id, choice_id, fb_text) VALUES('".$new_quest_id."', '".$questfb2copy['choice_id']."', ".$JLMS_DB->Quote($questfb2copy['fb_text']).")";
				$JLMS_DB->SetQuery( $query );
				$JLMS_DB->query();
			}
		}
		
		if ( ($quest2copy['c_type'] == 1) || ($quest2copy['c_type'] == 2) || ($quest2copy['c_type'] == 3) ) {
			$query = "SELECT * FROM #__lms_quiz_t_choice WHERE c_question_id = '".$old_quest_id."'";
			$JLMS_DB->setQuery( $query );
			$fields_to_copy = $JLMS_DB->loadAssocList();
			
			foreach ($fields_to_copy as $field2copy) {
				$new_field = new mos_JoomQuiz_ChoiceField( $JLMS_DB );
				if (!$new_field->bind( $field2copy )) { echo "<script> alert('".$new_field->getError()."'); window.history.go(-1); </script>\n"; exit(); }
				$new_field->c_id = 0;
				$new_quest->ordering = 0;
				$new_field->c_question_id = $new_quest_id;
				if (!$new_field->check()) { echo "<script> alert('".$new_field->getError()."'); window.history.go(-1); </script>\n"; exit(); }
				if (!$new_field->store()) { echo "<script> alert('".$new_field->getError()."'); window.history.go(-1); </script>\n"; exit(); }
			}
		}
		if ( ($quest2copy['c_type'] == 4) || ($quest2copy['c_type'] == 5)) {
			$query = "SELECT * FROM #__lms_quiz_t_matching WHERE c_question_id = '".$old_quest_id."'";
			$JLMS_DB->setQuery( $query );
			$fields_to_copy = $JLMS_DB->loadAssocList();
			foreach ($fields_to_copy as $field2copy) {
				$new_field = new mos_JoomQuiz_MatchField( $JLMS_DB );
				if (!$new_field->bind( $field2copy )) { echo "<script> alert('".$new_field->getError()."'); window.history.go(-1); </script>\n"; exit(); }
				$new_field->c_id = 0;
				$new_quest->ordering = 0;
				$new_field->c_question_id = $new_quest_id;
				if (!$new_field->check()) { echo "<script> alert('".$new_field->getError()."'); window.history.go(-1); </script>\n"; exit(); }
				if (!$new_field->store()) { echo "<script> alert('".$new_field->getError()."'); window.history.go(-1); </script>\n"; exit(); }
			}
		}
		if ( ($quest2copy['c_type'] == 6)) {
			$query = "SELECT * FROM #__lms_quiz_t_blank WHERE c_question_id = '".$old_quest_id."'";
			$JLMS_DB->setQuery( $query );
			$blank_to_copy = $JLMS_DB->LoadObjectList();
			if (count($blank_to_copy) > 0) {
				$old_blank_id = $blank_to_copy[0]->c_id;
				$old_default = isset($blank_to_copy[0]->c_default) ? $blank_to_copy[0]->c_default : '';
				$query = "SELECT * FROM #__lms_quiz_t_text WHERE c_blank_id = '".$old_blank_id."'";
				$JLMS_DB->setQuery( $query );
				$fields_to_copy = $JLMS_DB->loadAssocList();
				$query = "INSERT INTO #__lms_quiz_t_blank (c_question_id, c_default) VALUES('".$new_quest_id."', ".$JLMS_DB->Quote($old_default).")";
				$JLMS_DB->SetQuery( $query );
				$JLMS_DB->query();
				$new_blank_id = $JLMS_DB->insertid();
				foreach ($fields_to_copy as $field2copy) {
					$new_field = new mos_JoomQuiz_BlankTextField( $JLMS_DB );
					if (!$new_field->bind( $field2copy )) { echo "<script> alert('".$new_field->getError()."'); window.history.go(-1); </script>\n"; exit(); }
					$new_field->c_id = 0;
					$new_quest->ordering = 0;
					$new_field->c_blank_id = $new_blank_id;
					if (!$new_field->check()) { echo "<script> alert('".$new_field->getError()."'); window.history.go(-1); </script>\n"; exit(); }
					if (!$new_field->store()) { echo "<script> alert('".$new_field->getError()."'); window.history.go(-1); </script>\n"; exit(); }
				}
			}
		}
		if ( ($quest2copy['c_type'] == 7)) {
			$query = "SELECT * FROM #__lms_quiz_t_hotspot WHERE c_question_id = '".$old_quest_id."'";
			$JLMS_DB->setQuery( $query );
			$fields_to_copy = $JLMS_DB->loadAssocList();
			foreach ($fields_to_copy as $field2copy) {
				$new_field = new mos_JoomQuiz_HotSpotField( $JLMS_DB );
				if (!$new_field->bind( $field2copy )) { echo "<script> alert('".$new_field->getError()."'); window.history.go(-1); </script>\n"; exit(); }
				$new_field->c_id = 0;
				$new_quest->ordering = 0;
				$new_field->c_question_id = $new_quest_id;
				if (!$new_field->check()) { echo "<script> alert('".$new_field->getError()."'); window.history.go(-1); </script>\n"; exit(); }
				if (!$new_field->store()) { echo "<script> alert('".$new_field->getError()."'); window.history.go(-1); </script>\n"; exit(); }
			}
		}
		if ( ($quest2copy['c_type'] == 9)) {
			$query = "SELECT * FROM #__lms_quiz_t_scale WHERE c_question_id = '".$old_quest_id."'";
			$JLMS_DB->setQuery( $query );
			$fields_to_copy = $JLMS_DB->loadAssocList();
			foreach ($fields_to_copy as $field2copy) {
				$new_field = new mos_JoomQuiz_ScaleField( $JLMS_DB );
				if (!$new_field->bind( $field2copy )) { echo "<script> alert('".$new_field->getError()."'); window.history.go(-1); </script>\n"; exit(); }
				$new_field->c_id = 0;
				$new_quest->ordering = 0;
				$new_field->c_question_id = $new_quest_id;
				if (!$new_field->check()) { echo "<script> alert('".$new_field->getError()."'); window.history.go(-1); </script>\n"; exit(); }
				if (!$new_field->store()) { echo "<script> alert('".$new_field->getError()."'); window.history.go(-1); </script>\n"; exit(); }
			}
		}
		
		/*
		if($gqp) {	
			if($new_quest_id) {			
				JLMS_quiz_admin_class::GQP_save_multicat($new_quest_id);
			}
		}
		*/
	}

	if (!$run_from_quiz_copy) {
		JLMS_quiz_admin_class::JQ_Calculate_Quiz_totalScore($quizMove);
		#$quizNew = new mos_JoomQuiz_Quiz ( $JLMS_DB );
		#$quizNew->load( $quizMove );
		#global $option;
		#$msg = $total ." Questions copied to ". $quizNew->c_title;
		
		if(!$gqp) {
			JLMSRedirect( sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=quizzes&id=$course_id&page=setup_quest") );
		}
		else {
			JLMSRedirect( sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=quizzes&page=setup_gqp") );
		}
	}
}

function JQ_importQuestions($option, $page, $course_id, $gqp=false){
	global $JLMS_DB, $Itemid;
	
	$quiz_id = intval( mosGetParam( $_REQUEST, 'quiz_id', 0 ) );
	$mode_id = intval( mosGetParam( $_REQUEST, 'mode_id', 0 ) );
	
	$lists = array();
	
	/*$javascript = 'onchange="toogleMode();"';
	$mode = array();
	$mode[] = mosHTML::makeOption( 0, 'Quiz' );
	$mode[] = mosHTML::makeOption( 1, 'GQP' );
	$lists['mode'] = mosHTML::selectList($mode, 'mode_id', 'class="inputbox" style="width: 266px;" size="1" '.$javascript, 'value', 'text', $gqp );
	$lists['mode_id'] = $mode_id;
	$gqp = $mode_id;*/
	
	if($gqp){
		$javascript = 'onclick="read_filter();" onchange="javascript:write_filter();';
		$javascript .= 'form.page.value=\'import_quest_gqp\';document.adminForm.submit();"';
		
		//Multicategories
		$levels = array();
		//NEW MULTICAT
		$tmp_level = array();
		$last_catid = 0;
		$i=0;
		foreach($_REQUEST as $key=>$item){
			if(preg_match('#level_id_(\d+)#', $key, $result)){
				if($item){
					$tmp_level[$i] = $result;
					$last_catid = $item;
					$i++;
				}	
			}	
		}
		
		$tmp = array();
		$tmp = JLMS_quiz_admin_class::JLMS_multicats($last_catid, $tmp);
		$tmp = array_reverse($tmp);
		
		$tmp_pop = $tmp;
		$tmp_p = array_pop($tmp_pop);
		if(count($tmp) && $tmp_p->catid){
			$next = count($tmp);
			$tmp[$next] = new stdClass();
			$tmp[$next]->catid = 0;
			$tmp[$next]->parent = $tmp_p->catid;
		}
		
		if(count($levels) == 0){
			for($i=0;$i<15;$i++){
				$num = $i + 1;
				if($i > 0){
//						$levels[$i]->cat_name = _JLMS_COURSES_COURSES_GROUPS;	
					$levels[$i]->cat_name = 'Level #'.$num;
				} else {
//						$levels[$i]->cat_name = _JLMS_COURSES_COURSES_GROUPS;
					$levels[$i]->cat_name = 'Level #'.$num;
				}
			}
		}
		$level_id = array();
		
		for($i=0;$i<count($levels);$i++) {
			if($i == 0 || (isset($tmp[$i]->parent) && $tmp[$i]->parent)){ //(Max): extra requests
				$query = "SELECT * FROM `#__lms_gqp_cats` WHERE parent = '".$tmp[$i]->parent."' ORDER BY c_category";
				$JLMS_DB->setQuery($query);
				$groups = $JLMS_DB->loadObjectList();
				
				if($tmp[$i]->parent && $i > 0 && count($groups)) {
					$type_level[$i][] = mosHTML::makeOption( 0, '&nbsp;' );
					
					foreach ($groups as $group){
						$type_level[$i][] = mosHTML::makeOption( $group->id, $group->c_category );
					}
					$lists['level_'.$i.''] = mosHTML::selectList($type_level[$i], 'level_id_'.$i.'', 'class="inputbox" size="1" style="width:266px;" '.$javascript, 'value', 'text', $tmp[$i]->catid );
				} elseif($i == 0) {
					$type_level[$i][] = mosHTML::makeOption( 0, '&nbsp;' );
				
					foreach ($groups as $group){
						$type_level[$i][] = mosHTML::makeOption( $group->id, $group->c_category );
					}
					$lists['level_'.$i.''] = mosHTML::selectList($type_level[$i], 'level_id_'.$i.'', 'class="inputbox" size="1" style="width:266px;" '.$javascript, 'value', 'text', $tmp[$i]->catid );
				}
			}
		}
		
		$lists['levels'] = $levels;
	} else {
		$query = "SELECT c_id as value, c_category as text FROM #__lms_quiz_t_category WHERE course_id = '".$course_id."' AND is_quiz_cat = 0 order by c_category";
		$JLMS_DB->setQuery( $query );
		$jq_cats = array();
		$jq_cats[] = mosHTML::makeOption(0, ' -'._JLMS_QUIZ_CAT_TYPE_QUEST.' - ');
		$jq_cats = array_merge($jq_cats, $JLMS_DB->loadObjectList());
		$lists['jq_categories']	= mosHTML::selectList( $jq_cats, 'c_qcat', 'class="inputbox" size="1" style="width:266px;"', 'value', 'text', 0 );
	}
	
	JLMS_quiz_admin_html_class::JQ_showImportQuestions($option, $page, $course_id, $quiz_id, $lists, $gqp);
}

function JQ_importQuestionsRun($option, $page, $course_id, $gqp=false){
	global $JLMS_DB, $Itemid;
	
	$quiz_id = intval( mosGetParam( $_REQUEST, 'quiz_id', 0 ) );
	
	$avialable_exts = array('csv');
	
	if($gqp){
		//NEW version multicat
		$tmp_level = array();
		$last_catid = 0;
		$i=0;
		foreach($_REQUEST as $key=>$item){
			if(preg_match('#level_id_(\d+)#', $key, $result)){
				if($item){
					$tmp_level[$i] = $result;
					$last_catid = $item;
					$i++;
				}	
			}	
		}
		$_REQUEST['c_qcat'] = $last_catid;
		//NEW version multicat
	}
	
	$c_qcat = intval( mosGetParam( $_REQUEST, 'c_qcat', 0 ) );
	if(isset($_FILES['userfile_csv']['name']) && $_FILES['userfile_csv']['error'] === 0){
		preg_match('#\S+\.(\S+)$#', $_FILES['userfile_csv']['name'], $out);
		$data_csv = array();
		if(isset($out[0]) && isset($out[1])){
			$ext = strtolower($out[1]);
			if(in_array($ext, $avialable_exts)){
				$index = 0;
				$row = 1;
				$handle = fopen($_FILES['userfile_csv']['tmp_name'], "r");
				while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
				    $num = count($data);
				    $row++;
				    for ($c=0; $c < $num; $c++) {
				        if($index){
				        	$data_csv[$index] = $data;	
				        }
				    }
				    $index++;
				}
				fclose($handle);
			}
		}
		if(isset($data_csv) && count($data_csv)){
			$tmp_data_csv = array();
			$i=0;
			foreach($data_csv as $item_csv){
				$tmp_data_csv[$i] = $item_csv;
				$tmp_data_csv[$i][0] = JLMS_quiz_admin_class::getQuestType($item_csv[0], 1);
				$i++;
			}
			if(isset($tmp_data_csv) && count($tmp_data_csv)){
				$data_csv = array();
				$data_csv = $tmp_data_csv;
			}
			$objs_data_csv = array();
			$tmp_right = array();
			foreach($data_csv as $index=>$item_csv){
				$tmp_answers = array();
				$i_a = 0;
				foreach($item_csv as $n=>$value){
					if($n < 6){
						$objs_data_csv[$index]->course_id = $course_id;
						$objs_data_csv[$index]->c_quiz_id = $quiz_id;
						$objs_data_csv[$index]->c_qcat = $c_qcat;
						switch($n){
							case '0':
								$objs_data_csv[$index]->c_type = intval($value);
							break;
							case '1':
								$objs_data_csv[$index]->c_question = $value;
							break;
							case '2':
								$objs_data_csv[$index]->c_point = $value;
							break;
							case '3':
								$objs_data_csv[$index]->c_attempts = $value;
							break;
							case '4':
								$objs_data_csv[$index]->published = $value;
							break;
							case '5':
								$objs_data_csv[$index]->ordering = $value;
							break;
						}
					}
					if($n == 6){
						$tmp_right[$index] = $value;
					}
				}

				$type = $objs_data_csv[$index]->c_type;
				//preg_match('#\"(.*)\"#', $tmp_right[$index], $out);
				if(isset($tmp_right[$index]) && $tmp_right[$index]){
					$rights = $tmp_right[$index];
				} else {
					$rights = '';
				}
					foreach($item_csv as $n=>$value){
						if($n > 6){
							switch($type){
								case '1':
								case '2':
								case '3':
									if(isset($value) && $value){
										$right = explode("/", $rights);
										$index_item = $i_a + 1;
										$tmp_answers[$index][$i_a]->c_choice = $value;
										$tmp_answers[$index][$i_a]->c_right = '';
										if(in_array($index_item, $right)){
											$tmp_answers[$index][$i_a]->c_right = 1;
										}
										$i_a++;
									}
								break;
								case '4':
								case '5':
									if(isset($value) && $value){
										$right = explode("/", $rights);
										$index_item = $i_a + 1;
										foreach($right as $k=>$r){
											$index_structure = explode(":", $r);
											if($index_item == $index_structure[0]){
												$tmp_answers[$index][$k]->c_left_text = $value;
											}
											if($index_item == $index_structure[1]){	
												$tmp_answers[$index][$k]->c_right_text = $value;
											}
										}
										$i_a++;
									}
								break;
								case '6':
									if(isset($value) && $value){
										$tmp_answers[$index][$i_a]->c_text = $value;
										$i_a++;
									}
								break;
								case '9':
									if(isset($value)){
										$right = explode("/", $rights);
										
										$index_item = $i_a + 1;
										foreach($right as $k=>$r){
											$index_structure = explode(":", $r);
											if($index_item == $index_structure[0]){
												$tmp_answers[$index][$k]->c_field = $value;
											}
											if($index_item == $index_structure[1]){	
												$tmp_answers[$index][$k]->c_type = $value;
											}
										}
										$i_a++;
									}
								break;
							}
						}
					//}
					$objs_data_csv[$index]->answers = array();
					$objs_data_csv[$index]->answers = isset($tmp_answers[$index]) && count($tmp_answers[$index]) ? $tmp_answers[$index] : array();
				}
			}
			
			if(isset($objs_data_csv) && count($objs_data_csv)){
				foreach($objs_data_csv as $obj_data){
					
					$save_data = get_object_vars($obj_data);
					$row = new mos_JoomQuiz_Question( $JLMS_DB );
					if (!$row->bind( $save_data )) {
						echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
						exit();
					}
					if (!$row->check()) {
						echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
						exit();
					}
					if (!$row->store()) {
						break 2;
						echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
						exit();
					}
					
					$qid = $JLMS_DB->insertid();
					
					$type = $obj_data->c_type;
					switch($type){
						case '1':
						case '2':
						case '3':
							$answers = $obj_data->answers;
							if(isset($answers) && count($answers)){
								foreach($answers as $order=>$answer){
									$data = array();
									$data = get_object_vars($answer);
									if($type == 3){
										$data['c_choice'] = strtolower($data['c_choice']);
									}
									$data['c_question_id'] = $qid;
									$data['ordering'] = $order;
									
									$row = new mos_JoomQuiz_ChoiceField($JLMS_DB);
									if (!$row->bind( $data )) {
										echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
										exit();
									}
									if (!$row->check()) {
										echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
										exit();
									}
									if (!$row->store()) {
										break 2;
										echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
										exit();
									}
								}
							}
						break;
						case '4':
						case '5':
							$answers = $obj_data->answers;
							if(isset($answers) && count($answers)){
								foreach($answers as $order=>$answer){
									$data = array();
									$data = get_object_vars($answer);
									$data['c_question_id'] = $qid;
									$data['ordering'] = $order;
									
									$row = new mos_JoomQuiz_MatchField($JLMS_DB);
									if (!$row->bind( $data )) {
										echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
										exit();
									}
									if (!$row->check()) {
										echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
										exit();
									}
									if (!$row->store()) {
										break 2;
										echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
										exit();
									}
								}
							}
						break;	
						case '6':
							$answers = $obj_data->answers;
							if(isset($answers) && count($answers)){
								foreach($answers as $order=>$answer){
									
									$query = "INSERT INTO #__lms_quiz_t_blank"
									. "\n (c_question_id, c_default)"
									. "\n VALUES"
									. "\n ('".$qid."', '')"
									;
									$JLMS_DB->SetQuery($query);
									$JLMS_DB->query();
									$bid = $JLMS_DB->insertid();
									
									$data = array();
									$data = get_object_vars($answer);
									$data['c_blank_id'] = $bid;
									$data['ordering'] = $order;
									
									$row = new mos_JoomQuiz_BlankTextField($JLMS_DB);
									if (!$row->bind( $data )) {
										echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
										exit();
									}
									if (!$row->check()) {
										echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
										exit();
									}
									if (!$row->store()) {
										break 2;
										echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
										exit();
									}
								}
							}
						break;
						case '9':
							$answers = $obj_data->answers;
							if(isset($answers) && count($answers)){
								foreach($answers as $order=>$answer){
									$data = array();
									$data = get_object_vars($answer);
									$data['c_question_id'] = $qid;
									$data['ordering'] = $order;
									
									$row = new mos_JoomQuiz_ScaleField($JLMS_DB);
									if (!$row->bind( $data )) {
										echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
										exit();
									}
									if (!$row->check()) {
										echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
										exit();
									}
									if (!$row->store()) {
										break 2;
										echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
										exit();
									}
								}
							}
						break;	
					}
				}
			}
			
		}
	}
	
	if($gqp){
		JLMSRedirect( sefRelToAbs("index.php?option=$option&task=quizzes&page=setup_gqp&Itemid=$Itemid") );
	} else {
		$link = "index.php?option=$option&task=quizzes&id=$course_id&page=setup_quest";
		$link .= "&quiz_id=$quiz_id";
		$link .= "&Itemid=$Itemid";
		JLMSRedirect( sefRelToAbs($link) );
	}
	
//	echo '<pre>';
//	print_r($objs_data_csv);
//	print_r($data_csv);
//	print_r($tmp_data_csv);
//	print_r($_REQUEST);
//	print_r($_FILES);
//	echo '</pre>';
//	die;
}

function JQ_exportQuestions( $option, $page, $course_id, $cid, $gqp=false ){
	global $JLMS_DB, $Itemid;
	
	$quiz_id = intval( mosGetParam( $_REQUEST, 'quiz_id', 0 ) );
	
	$where = "";
	if($gqp){
		$where .= "\n AND course_id = '0'";
		$where .= "\n AND c_quiz_id = '0'";
	} else {
		if($quiz_id > 0){
			$where .= "\n AND course_id = '".$course_id."'";
			$where .= "\n AND c_quiz_id = '".$quiz_id."'";
		} else {
			$where .= "\n AND course_id = '".$course_id."'";
			$where .= "\n AND c_quiz_id = '0'";
		}
	}
	
	$query = "SELECT *"
	. "\n FROM #__lms_quiz_t_question"
	. "\n WHERE 1"
	. $where
//	. "\n AND published = 1"
	.(count($cid) && $cid[0] ? "\n AND c_id IN (".implode(",", $cid).")" : "")
	;
	$JLMS_DB->setQuery($query);
	$questions = $JLMS_DB->loadObjectList();
	
	$query = "SELECT *"
	. "\n FROM #__lms_quiz_t_question"
	. "\n WHERE 1"
	. "\n AND course_id = '".$course_id."'"
	. "\n AND c_quiz_id = '0'"
	;
	$JLMS_DB->setQuery($query);
	$qpool = $JLMS_DB->loadObjectList();
	
	$query = "SELECT *"
	. "\n FROM #__lms_quiz_t_question"
	. "\n WHERE 1"
	. "\n AND course_id = '0'"
	. "\n AND c_quiz_id = '0'"
	;
	$JLMS_DB->setQuery($query);
	$gqpool = $JLMS_DB->loadObjectList();
	
	if(isset($questions) && count($questions)){
		$tmp_questions = array();
		foreach($questions as $n=>$quest){
			$tmp_questions[$n] = $quest;
			if($quest->c_type == 20){
				if(isset($qpool) && count($qpool)){
					foreach($qpool as $item_qpool){
						if($quest->c_pool == $item_qpool->c_id){
							$tmp_questions[$n]->c_id = $item_qpool->c_id;
							$tmp_questions[$n]->course_id = $quest->course_id;
							$tmp_questions[$n]->c_quiz_id = $quest->c_quiz_id;
							$tmp_questions[$n]->c_point = $item_qpool->c_point;
							$tmp_questions[$n]->c_attempts = $item_qpool->c_attempts;
							$tmp_questions[$n]->c_question = $item_qpool->c_question;
							$tmp_questions[$n]->c_type = $item_qpool->c_type;
							$tmp_questions[$n]->published = $quest->published;
							$tmp_questions[$n]->ordering = $quest->ordering;
						}
					}
				}
			}
			if($quest->c_type == 21){
				if(isset($gqpool) && count($gqpool)){
					foreach($gqpool as $item_gqpool){
						if($quest->c_pool_gqp == $item_gqpool->c_id){
							$tmp_questions[$n]->c_id = $item_gqpool->c_id;
							$tmp_questions[$n]->course_id = $quest->course_id;
							$tmp_questions[$n]->c_quiz_id = $quest->c_quiz_id;
							$tmp_questions[$n]->c_point = $item_gqpool->c_point;
							$tmp_questions[$n]->c_attempts = $item_gqpool->c_attempts;
							$tmp_questions[$n]->c_question = $item_gqpool->c_question;
							$tmp_questions[$n]->c_type = $item_gqpool->c_type;
							$tmp_questions[$n]->published = $quest->published;
							$tmp_questions[$n]->ordering = $quest->ordering;
						}
					}
				}
			}
		}
		if(isset($tmp_questions) && count($tmp_questions)){
			$questions = array();
			$questions = $tmp_questions;
		}
	}
	
//	echo '<pre>';
//	print_r($qpool);
//	print_r($gqpool);
//	print_r($questions);
//	echo '</pre>';
//	die;
	
	$question_ids = array();
	$questions_types = array();
	foreach($questions as $n=>$question){
		$question_ids[$n] = $question->c_id;
		
		$questions_types[$question->c_type][] = $question->c_id;
	}
	
	$variants_answers_types = array();
	foreach($questions_types as $type=>$quests){
		switch(intval($type)){
			case '1':
			case '2':
			case '3':
			#case '12':
			#case '13':
				if(isset($quests) && count($quests)){
					$variants_answers = array();
					$str_quiestions_ids = implode(",", $quests);
					$query = "SELECT c_id, c_question_id, c_choice, c_right, ordering"	
					. "\n FROM #__lms_quiz_t_choice"
					. "\n WHERE 1"
					. "\n AND c_question_id IN (".$str_quiestions_ids.")"
					. "\n ORDER BY c_question_id, ordering"
					;
					$JLMS_DB->setQuery($query);
					$variants_answers = $JLMS_DB->loadObjectList();
					$variants_answers_types[$type] = $variants_answers;
				}
			break;
			case '4':
			case '5':
			#case '11':
				if(isset($quests) && count($quests)){
					$variants_answers = array();
					$str_quiestions_ids = implode(",", $quests);
					$query = "SELECT *"	
					. "\n FROM #__lms_quiz_t_matching"
					. "\n WHERE 1"
					. "\n AND c_question_id IN (".$str_quiestions_ids.")"
					. "\n ORDER BY c_question_id, ordering"
					;
					$JLMS_DB->setQuery($query);
					$variants_answers = $JLMS_DB->loadObjectList();
					$variants_answers_types[$type] = $variants_answers;
				}
			break;
			case '6':
				if(isset($quests) && count($quests)){
					$variants_answers = array();
					$str_quiestions_ids = implode(",", $quests);
					$query = "SELECT qtb.c_id, qtb.c_question_id, qtt.c_blank_id, qtt.c_text, qtt.ordering"	
					. "\n FROM #__lms_quiz_t_blank qtb, #__lms_quiz_t_text as qtt"
					. "\n WHERE 1"
					. "\n AND qtb.c_question_id IN (".$str_quiestions_ids.")"
					. "\n AND qtb.c_id = qtt.c_blank_id"
					. "\n ORDER BY c_question_id, ordering"
					;
					$JLMS_DB->setQuery($query);
					$variants_answers = $JLMS_DB->loadObjectList();
					$variants_answers_types[$type] = $variants_answers;
				}
			break;
			case '9':
				if(isset($quests) && count($quests)){
					$variants_answers = array();
					$str_quiestions_ids = implode(",", $quests);
					$query = "SELECT *"	
					. "\n FROM #__lms_quiz_t_scale"
					. "\n WHERE 1"
					. "\n AND c_question_id IN (".$str_quiestions_ids.")"
					. "\n ORDER BY c_question_id, c_type, ordering"
					;
					$JLMS_DB->setQuery($query);
					$variants_answers = $JLMS_DB->loadObjectList();
					$variants_answers_types[$type] = $variants_answers;
				}
			break;
		}
	}
	
	$max_items = 0;
	
	$exclude_fields = array('c_id', 'c_question_id', 'c_blank_id', 'c_right', 'ordering');
	
	$questions_pre_csv = array();
	foreach($questions as $n=>$quest){
		if(isset($variants_answers_types[$quest->c_type]) && count($variants_answers_types[$quest->c_type])){
			$questions_pre_csv[$n] = $quest;
			$v_answers = $variants_answers_types[$quest->c_type];
			
			$questions_pre_csv[$n]->answers = array();
			$i=0;
			$max_fields = 0;
			foreach($v_answers as $v_answ){
				if($v_answ->c_question_id == $quest->c_id){
					$questions_pre_csv[$n]->answers[] = $v_answ;
					if(!$i){
						$cfields = 0;
						foreach($v_answ as $key=>$item){
							if(!in_array($key, $exclude_fields)){
								$cfields++;
							}
						}
					}
					$i++;
					$max_fields = $cfields*$i;
				}
			}
			if($max_items < $max_fields){
				$max_items = $max_fields;
			}
		}
	}
	
	$question_csv = array();
	foreach($questions_pre_csv as $n=>$qpcsv){
		$question_csv[$n]->type = JLMS_quiz_admin_class::getQuestType($qpcsv->c_type);
		$question_csv[$n]->question = JLMS_processCSVField($qpcsv->c_question);
		$question_csv[$n]->points = $qpcsv->c_point;
		$question_csv[$n]->attempts = $qpcsv->c_attempts;
		$question_csv[$n]->published = $qpcsv->published;
		$question_csv[$n]->ordering = $qpcsv->ordering;
		
		$question_csv[$n]->right = JLMS_processCSVField(JLMS_quiz_admin_class::getRight($qpcsv->c_type, $qpcsv->answers));
		
		if(isset($qpcsv->answers) && count($qpcsv->answers)){
			$tmp_answers = $qpcsv->answers;
			$m=1;
			foreach($tmp_answers as $tmp_a){
				foreach($tmp_a as $key=>$item){
					$answ_item = 'item_'.$m;
					if(!in_array($key, $exclude_fields)){
						$question_csv[$n]->$answ_item = JLMS_processCSVField($item);
						$m++;
					}
				}
			}
			if($m < $max_items){
				for($i=$m;$i<=$max_items;$i++){
					$answ_item = 'item_'.$i;
					$question_csv[$n]->$answ_item = JLMS_processCSVField('');
				}
			}
			/*
			for($i=0;$i<$max_items;$i++){
				$m = $i + 1;
				$answ_item = 'item_'.$m;
				
				if(isset($tmp_answers[$i])){
					switch(intval($qpcsv->c_type)){
						case '1':
						case '2':
						case '3':
							$question_csv[$n]->$answ_item = $tmp_answers[$i]->c_choice;
						break;
						case '4':
						case '5':
							$question_csv[$n]->$answ_item = $tmp_answers[$i]->c_left_text;
						break;	
						case '6':
							$question_csv[$n]->$answ_item = $tmp_answers[$i]->c_text;
						break;
						case '9':
							$question_csv[$n]->$answ_item = $tmp_answers[$i]->c_field;
						break;	
					}
				} else {
					$question_csv[$n]->$answ_item = '';
				}
			}
			*/
		}
	}
	
	$titles = new stdClass();
	$titles->type = _JLMS_QUIZ_TBL_QUEST_TYPE;//'Type';
	$titles->question = str_replace(':','',_JLMS_QUIZ_QUESTION);//'Question';
	$titles->points = str_replace(':','',_JLMS_QUIZ_QUEST_POINTS);//'Points';
	$titles->attempts = str_replace(':','',_JLMS_QUIZ_QUEST_ATTEMPTS);//'Attempts';
	$titles->published = _JLMS_QUIZ_TBL_QUEST_PUBLISH;//'Published';
	$titles->ordering = str_replace(':','',_JLMS_QUIZ_QUEST_ORDERING);//'Ordering';
	
	$titles->right = 'Correct choice';
	
	for($i=1;$i<=$max_items;$i++){
		$titles->{'item_'.$i} = 'Item '.$i;
	}
	
	$data_csv = '';
	$titles = get_object_vars($titles);
	if(isset($titles) && count($titles)){
		foreach ($titles as $tk => $tv) {
			$titles[$tk] = JLMS_processCSVField($tv);
		}
		$data_csv .= implode(",", $titles). "\n";
	}
	if(count($question_csv)){
		foreach($question_csv as $n=>$d){
			$d = get_object_vars($d);
			$data_csv .= implode(",", $d). "\n";
		}
	}
	
	if($course_id > 0 && $quiz_id > 0){
		$course_name = JLMS_getCourseName($course_id);
		$quiz_name = JLMS_getQuizName($quiz_id);
		$tmpl_name = ucfirst($course_name).'_['.ucfirst($quiz_name).']_'.date('dMY');
	} else 
	if($course_id > 0 && $quiz_id < 0){
		$course_name = JLMS_getCourseName($course_id);
		$tmpl_name = ucfirst($course_name).'_['._JLMS_QUIZ_QUEST_POOL.']_'.date('dMY');
	} else {
		$tmpl_name = _JLMS_GLOBAL_QUEST_POOL.'_'.date('dMY');
	}

	$tmpl_name = str_replace(" ", "_", $tmpl_name);
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
	header("Content-Length: ".strlen(trim($data_csv)));
	header('Content-Disposition: attachment; filename="'.$tmpl_name.'.csv"');
	if ($UserBrowser == 'IE') {
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
	} else {
		header('Pragma: no-cache');
	}
	echo $data_csv;
	exit();
	
	
	echo '<pre>';
	print_r($max_items);
//	print_r($_POST);
//	print_r($JLMS_DB->getQuery());
//	print_r($JLMS_DB->getErrorMsg());
//	print_r($variants_answers);
//	print_r($variants_answers_types);
//	print_r($questions);
//	print_r($questions_pre_csv);
	print_r($question_csv);
//	print_r($question_ids);
//	print_r($questions_types);
	echo '</pre>';
	die;
}

function getRight($type, $answers=array()){
	$str_rights = '';
	if(isset($answers) && count($answers)){
		switch(intval($type)){
			case '1':
			case '2':
			case '3':
				$rights = array();
				foreach($answers as $n=>$answer){
					$index = $n + 1;
					if(isset($answer->c_right) && $answer->c_right){
						$rights[] = $index;
					}
				}
			break;
			case '4':
			case '5':
				$rights = array();
				$i=1;
				foreach($answers as $n=>$answer){
					foreach($answer as $key=>$item){
						if($key == 'c_left_text'){
							$c_left_text = $i;
						}
						if($key == 'c_right_text'){
							$c_right_text = $i;
						}
						if($key == 'c_left_text' || $key == 'c_right_text'){
							$i++;
						}
					}
					$rights[] = $c_left_text.':'.$c_right_text;
				}
			break;
			case '6':
				$rights = array();
				foreach($answers as $n=>$answer){
					$index = $n + 1;
					if(isset($answer->c_text) && $answer->c_text){
						$rights[] = $index;
					}
				}
			break;
			case '9':
				$rights = array();
				$i=1;
				foreach($answers as $answer){
					foreach($answer as $key=>$item){
						if($key == 'c_field'){
							$c_field = $i;
						}
						if($key == 'c_type'){
							$c_type = $i;
						}
						if($key == 'c_field' || $key == 'c_type'){
							$i++;
						}
					}
					$rights[] = $c_field.':'.$c_type;
				}
			break;		
		}
		if(isset($rights) && count($rights)){
			$str_rights = implode("/", $rights);
			$str_rights = $str_rights;
		}
	}
	return $str_rights;
}

function getQuestType($type, $import=0){
	$str_type = false;
	
	$types = array();
	$types[1] 	= 'multchoice';
	$types[2] 	= 'multresponse';
	$types[3] 	= 'truefalse';
	$types[4]	= 'matchdragdrop';
	$types[5] 	= 'matchdropdown';
	$types[6] 	= 'fillblank';
	$types[7] 	= 'hotspot';
	$types[8] 	= 'survey';
	$types[9] 	= 'likertscale';
	$types[10] 	= 'boilerplate';
	$types[11] 	= 'matchdragdropimg';
	$types[12] 	= 'multchoiceimg';
	$types[13] 	= 'multresponseimg';
	
	if(isset($import) && $import ){
		$types = array_flip($types);
		$type = strtolower($type);
		
		//fix translate bug, support bug version
		if($type == 'multresponce'){
			$type = 'multresponse';
		} else if($type == 'multresponceimg'){
			$type = 'multresponseimg';
		} else if($type == 'likescale'){
			$type = 'likertscale';
		}
		//fix translate bug, support bug version
	}
	
	if(isset($types[$type])){
		$str_type = $types[$type];
	}
	
	return $str_type;
}

#######################################
###	--- ---		REPORTS 	--- --- ###

function JQ_view_quizReport( $option, $page, $course_id, $is_csv = 0) {
	
	global $JLMS_DB, $JLMS_SESSION, $Itemid, $JLMS_CONFIG;
	
	$quiz_id	= intval( mosGetParam( $_REQUEST, 'quiz_id', $JLMS_SESSION->get('report_quiz_id', 0) ) );
	$user_id	= intval( mosGetParam( $_REQUEST, 'user_id', $JLMS_SESSION->get('report_user_id', 0) ) );
	$limit		= intval( mosGetParam( $_REQUEST, 'limit', $JLMS_SESSION->get('list_limit', $JLMS_CONFIG->get('list_limit')) ) );
	$limitstart = intval( mosGetParam( $_REQUEST, 'limitstart', 0 ) );
	$JLMS_SESSION->set('report_quiz_id',$quiz_id);
	$JLMS_SESSION->set('report_user_id',$user_id);
	$JLMS_SESSION->set('list_limit',$limit);
	
	$view = strval( mosGetParam( $_REQUEST, 'view', '' ) );

	$user_id2 = $user_id;
	if ($user_id == -1) $user_id2 = 0;
	$query = "SELECT COUNT(sq.c_id)"
	. "\n FROM #__lms_quiz_t_quiz as q, #__lms_quiz_r_student_quiz as sq"
	. "\n WHERE sq.c_quiz_id = q.c_id AND q.course_id =  '".$course_id."'"
	. ( $quiz_id ? "\n AND sq.c_quiz_id = $quiz_id" : '' )
	. ( $user_id ? "\n AND sq.c_student_id = $user_id2" : '' )
	;
	$JLMS_DB->setQuery( $query );
	$total = $JLMS_DB->loadResult();

	require_once(_JOOMLMS_FRONT_HOME . DS . "includes" . DS . "classes" . DS . "lms.pagination.php");
	$pageNav = new JLMSPageNav( $total, $limitstart, $limit );

	$query = "SELECT sq.c_id, sq.c_passed, sq.c_total_score, sq.c_total_time, sq.c_date_time, sq.c_passed,"
	. "\n q.c_title, q.c_author, q.c_passing_score,sq.c_student_id, u.username, u.name, u.email, q.c_full_score, q.c_id as cur_quiz_id"
	. "\n FROM #__lms_quiz_t_quiz as q, #__lms_quiz_r_student_quiz as sq"
	. "\n LEFT JOIN #__users as u ON sq.c_student_id = u.id"
	. "\n WHERE sq.c_quiz_id = q.c_id AND q.course_id = '".$course_id."'"
	. ( $quiz_id ? "\n AND sq.c_quiz_id = $quiz_id" : '' )
	. ( $user_id ? "\n AND sq.c_student_id = $user_id2" : '' )
	. "\n ORDER BY sq.c_date_time DESC";
	if ($is_csv == 1) {
		
	} else {
		$query .= "\n LIMIT $pageNav->limitstart, $pageNav->limit";
	}

	$JLMS_DB->SetQuery( $query );
	$rows = $JLMS_DB->LoadObjectList();
	
	$lists = array();

	$query = "SELECT distinct quiz_id FROM #__lms_quiz_t_quiz_pool";
	$JLMS_DB->SetQuery( $query );
	$lists['pool_quizzes'] = $JLMS_DB->loadResultArray();

	if ($JLMS_CONFIG->get('global_quest_pool')) {
		$query = "SELECT distinct quiz_id FROM #__lms_quiz_t_quiz_gqp";
		$JLMS_DB->SetQuery( $query );
		$lists['pool_quizzes_gqp'] = $JLMS_DB->loadResultArray();
	}
	else 
		$lists['pool_quizzes_gqp'] = array();
	
	$javascript = 'onchange="document.adminForm.page.value=\'reports\'; document.adminForm.submit();"';
	$query = "SELECT distinct q.c_id AS value, q.c_title AS text"
	. "\n FROM #__lms_quiz_t_quiz as q, #__lms_quiz_r_student_quiz as sq"
	. "\n WHERE q.course_id = '".$course_id."' AND q.c_id = sq.c_quiz_id"
	. "\n ORDER BY q.c_title"
	;
	$JLMS_DB->setQuery( $query );
	$quizzes = array();
	$quizzes[] = mosHTML::makeOption( '0', _JLMS_SB_SELECT_QUIZ );
	$quizzes = array_merge( $quizzes, $JLMS_DB->loadObjectList() );
	$quiz = mosHTML::selectList( $quizzes,'quiz_id', 'class="inputbox" size="1" style="width:180px" '. $javascript, 'value', 'text', $quiz_id ); 
	$lists['quiz'] = $quiz; 
	$query = "SELECT c_id FROM #__lms_quiz_t_quiz WHERE course_id = '".$course_id."'";
	$JLMS_DB->setQuery( $query );
	$quiz_ids = $JLMS_DB->LoadResultArray();
	if (!count($quiz_ids)) { $quiz_ids = array(0); }
	$quiz_id_str = implode(',',$quiz_ids);
	$query = "SELECT distinct q.id AS value, q.username AS text"
	. "\n FROM #__users as q, #__lms_quiz_r_student_quiz as sq"
	. "\n WHERE q.id = sq.c_student_id AND sq.c_quiz_id IN ($quiz_id_str)"
	. "\n ORDER BY q.username"
	;
	$JLMS_DB->setQuery( $query );
	$users = array();
	$users[] = mosHTML::makeOption( '0', _JLMS_SB_SELECT_USER );
	$users = array_merge( $users, $JLMS_DB->loadObjectList() );
	$link = "index.php?option=$option&amp;Itemid=$Itemid&amp;task=quizzes&amp;id=$course_id";
	$link = $link ."&amp;page=reports&amp;user_id='+this.options[selectedIndex].value+'";
	$link = sefRelToAbs($link);
	$link = str_replace('%5C%27',"'", $link);$link = str_replace('%5B',"[", $link);$link = str_replace('%5D',"]", $link);$link = str_replace('%20',"+", $link);$link = str_replace("\\\\\\","", $link);$link = str_replace('%27',"'", $link);
	$javascript = 'onchange="document.location.href=\''. $link .'\';"';
	$lists['user'] = mosHTML::selectList( $users,'user_id', 'class="inputbox" size="1" style="width:180px" '. $javascript, 'value', 'text', $user_id );
	if ($is_csv == 1) {
		$str = '"'._JLMS_QUIZ_TBL_NUMBER.'","'._JLMS_QUIZ_TBL_QUIZ.'","'._JLMS_QUIZ_TBL_TOTAL_SCORE.'","'._JLMS_QUIZ_TBL_PASS_SCORE.'","'._JLMS_QUIZ_TBL_STUDENT.'","'._JLMS_QUIZ_TBL_USER_SCORE.'","'._JLMS_QUIZ_TBL_DATE_TIME.'","'._JLMS_QUIZ_TBL_SPEND_TIME.'","'._JLMS_QUIZ_TBL_PASSED.'"'."\n";
		for($i=0, $n = count($rows); $i < $n; $i++) {
			$str .= '"'.($i+1).'","';
			$str .= $rows[$i]->c_title.'","';
						
				if ($JLMS_CONFIG->get('global_quest_pool')) {
					$str .= $rows[$i]->c_full_score.( in_array($rows[$i]->cur_quiz_id, $lists['pool_quizzes_gqp']) ? '+' : '' ).'","';
				}
				else {
					$str .= $rows[$i]->c_full_score.( in_array($rows[$i]->cur_quiz_id, $lists['pool_quizzes']) ? '+' : '' ).'","';
				}
			
			$str .= $rows[$i]->c_passing_score.'%","';
			$str .= $rows[$i]->name." (".$rows[$i]->username.')","'.$rows[$i]->c_total_score.'","'.$rows[$i]->c_date_time;
			$tot_min = floor($rows[$i]->c_total_time / 60);
			$tot_sec = $rows[$i]->c_total_time - $tot_min*60;
			$str .= '","'.str_pad($tot_min,2, "0", STR_PAD_LEFT).":".str_pad($tot_sec,2, "0", STR_PAD_LEFT).'","';
			if($rows[$i]->c_passed)
				$str .= _JLMS_YES_ALT_TITLE;
			else
				$str .= _JLMS_NO_ALT_TITLE;
			$str .= "\"\n";
		}
		$UserBrowser = '';
		if (preg_match('/MSIE ([0-9].[0-9]{1,2})/', $_SERVER['HTTP_USER_AGENT'])) {
			$UserBrowser = "IE";
		}
		header("Content-Type:application/vnd.ms-excel");
		header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
		if ($UserBrowser == 'IE') {
			header("Content-Disposition: inline; filename=quiz_results.csv ");
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
		} else {
			header("Content-Disposition: inline; filename=quiz_results.csv ");
			header('Pragma: no-cache');
		}
		echo $str;
		die();
	} else {
		if($view == 'xls'){
			JLMS_quiz_reporting::prepare($course_id, $user_id2, $quiz_id);
		} else {
			JLMS_quiz_admin_html_class::JQ_view_quizReport( $rows, $pageNav, $option, $page, $course_id, $lists);
		}
	}
}
function JQ_view_stuReport( $id, $option, $page, $course_id ) {
	global $JLMS_DB, $JLMS_SESSION, $Itemid, $JLMS_CONFIG;

	$limit		= intval( mosGetParam( $_REQUEST, 'limit', $JLMS_SESSION->get('list_limit', $JLMS_CONFIG->get('list_limit')) ) );
	$limitstart = intval( mosGetParam( $_REQUEST, 'limitstart', 0 ) );
	$JLMS_SESSION->set('list_limit',$limit);

	$query = "SELECT count(*) FROM #__lms_quiz_r_student_quiz_pool WHERE start_id = $id";
	$JLMS_DB->setQuery( $query );
	$is_new_version_of_quiz = $JLMS_DB->LoadResult();
	
	$is_new_version_of_quiz_gqp = 0;
	if ($JLMS_CONFIG->get('global_quest_pool')) {
		$query = "SELECT count(*) FROM #__lms_quiz_r_student_quiz_gqp WHERE start_id = $id";
		$JLMS_DB->setQuery( $query );
		$is_new_version_of_quiz_gqp = $JLMS_DB->LoadResult();
	}
	
	if ($is_new_version_of_quiz_gqp) {
		$query = "SELECT COUNT(a.c_id)"
		. "\n FROM #__lms_quiz_r_student_question as a, #__lms_quiz_r_student_quiz_gqp as b"
		. "\n WHERE a.c_stu_quiz_id = '".$id."' AND a.c_stu_quiz_id = b.start_id AND a.c_question_id = b.quest_id";
	}
	
	elseif ($is_new_version_of_quiz) {
		$query = "SELECT COUNT(a.c_id)"
		. "\n FROM #__lms_quiz_r_student_question as a, #__lms_quiz_r_student_quiz_pool as b"
		. "\n WHERE a.c_stu_quiz_id = '".$id."' AND a.c_stu_quiz_id = b.start_id AND a.c_question_id = b.quest_id";
	} else {
		$query = "SELECT COUNT(a.c_id)"
		. "\n FROM #__lms_quiz_r_student_question as a"
		. "\n WHERE a.c_stu_quiz_id = '".$id."'";
	}

	//echo $query; die;
	
	$JLMS_DB->setQuery( $query );
	$total = $JLMS_DB->loadResult();

	require_once(_JOOMLMS_FRONT_HOME . DS . "includes" . DS . "classes" . DS . "lms.pagination.php");
	$pageNav = new JLMSPageNav( $total, $limitstart, $limit );
	
	if ($is_new_version_of_quiz_gqp) {
		$query = "SELECT sp.c_id, sp.c_score, q.c_type, q.c_point, q.c_question, qt.c_qtype, q.c_pool, q.c_pool_gqp"
		. "\n FROM #__lms_quiz_r_student_quiz_gqp as b, #__lms_quiz_r_student_question as sp LEFT JOIN #__lms_quiz_t_question as q ON sp.c_question_id = q.c_id LEFT JOIN #__lms_quiz_t_qtypes as qt ON q.c_type = qt.c_id"
		. "\n WHERE sp.c_stu_quiz_id = '".$id."' AND sp.c_stu_quiz_id = b.start_id AND sp.c_question_id = b.quest_id"
		. "\n ORDER BY b.ordering, q.c_id"
		. "\n LIMIT $pageNav->limitstart, $pageNav->limit"
		;
	}
	elseif ($is_new_version_of_quiz) {
		$query = "SELECT sp.c_id, sp.c_score, q.c_type, q.c_point, q.c_question, qt.c_qtype, q.c_pool, q.c_pool_gqp"
		. "\n FROM #__lms_quiz_r_student_quiz_pool as b, #__lms_quiz_r_student_question as sp LEFT JOIN #__lms_quiz_t_question as q ON sp.c_question_id = q.c_id LEFT JOIN #__lms_quiz_t_qtypes as qt ON q.c_type = qt.c_id"
		. "\n WHERE sp.c_stu_quiz_id = '".$id."' AND sp.c_stu_quiz_id = b.start_id AND sp.c_question_id = b.quest_id"
		. "\n ORDER BY b.ordering, q.c_id"
		. "\n LIMIT $pageNav->limitstart, $pageNav->limit"
		;
	} else {
		$query = "SELECT sp.c_id, sp.c_score, q.c_type, q.c_point, q.c_question, qt.c_qtype, q.c_pool, q.c_pool_gqp"
		. "\n FROM #__lms_quiz_r_student_question as sp LEFT JOIN #__lms_quiz_t_question as q ON sp.c_question_id = q.c_id LEFT JOIN #__lms_quiz_t_qtypes as qt ON q.c_type = qt.c_id"
		. "\n WHERE sp.c_stu_quiz_id = '".$id."'"
		. "\n ORDER BY q.ordering, q.c_id"
		. "\n LIMIT $pageNav->limitstart, $pageNav->limit"
		;
		
	}

	$JLMS_DB->SetQuery( $query );
	$rows = $JLMS_DB->LoadObjectList();
	
	for($i=0;$i<count($rows);$i++) {
		$str = '_JLMS_QUIZ_QTYPE_'.$rows[$i]->c_type;
		if (defined($str)) {
			$rows[$i]->qtype_full = constant($str);
		}
	}
	
	
	/*-------------------QP--------------------*/
	
	$q_from_pool = array();
	foreach ($rows as $row) {
		if ($row->c_type == 20) {
			$q_from_pool[] = $row->c_pool;
		}
	}
	if (count($q_from_pool)) {
		$qp_ids =implode(',',$q_from_pool);
		$query = "SELECT a.*, b.c_qtype as qtype_full FROM #__lms_quiz_t_question as a LEFT JOIN #__lms_quiz_t_qtypes b ON b.c_id = a.c_type"
		. "\n WHERE a.course_id = '".$course_id."'";
		$JLMS_DB->setQuery( $query );
		$rows2 = $JLMS_DB->loadObjectList();
		
		for($i=0;$i<count($rows2);$i++) {
			$str = '_JLMS_QUIZ_QTYPE_'.$rows2[$i]->c_type;
			if (defined($str)) {
				$rows2[$i]->qtype_full = constant($str);
			}
		}
		
		for ($i=0, $n=count( $rows ); $i < $n; $i++) {
			if ($rows[$i]->c_type == 20) {
				for ($j=0, $m=count( $rows2 ); $j < $m; $j++) {
					if ($rows[$i]->c_pool == $rows2[$j]->c_id) {
						$rows[$i]->c_question = $rows2[$j]->c_question;
						$rows[$i]->c_point = $rows2[$j]->c_point;
						$rows[$i]->c_qtype = _JLMS_QUIZ_QUEST_POOL_SHORT . ' - ' . $rows2[$j]->qtype_full;
						break;
					}
				}
			}
		}
	}
	
	/*----------------------GQP-------------------*/	
	
	$q_from_pool_gqp = array();
	foreach ($rows as $row) {
		if ($row->c_type == 21) {
			$q_from_pool_gqp[] = $row->c_pool_gqp;
		}
	}
	if (count($q_from_pool_gqp)) {
		$qp_ids_gqp =implode(',',$q_from_pool_gqp);
		$query = "SELECT a.*, b.c_qtype as qtype_full FROM #__lms_quiz_t_question as a LEFT JOIN #__lms_quiz_t_qtypes b ON b.c_id = a.c_type"
		. "\n WHERE a.course_id = 0";
		$JLMS_DB->setQuery( $query );
		$rows2 = $JLMS_DB->loadObjectList();
		
		for($i=0;$i<count($rows2);$i++) {
			$str = '_JLMS_QUIZ_QTYPE_'.$rows2[$i]->c_type;
			if (defined($str)) {
				$rows2[$i]->qtype_full = constant($str);
			}
		}
		
		for ($i=0, $n=count( $rows ); $i < $n; $i++) {
			if ($rows[$i]->c_type == 21) {
				for ($j=0, $m=count( $rows2 ); $j < $m; $j++) {
					if ($rows[$i]->c_pool_gqp == $rows2[$j]->c_id) {
						$rows[$i]->c_question = $rows2[$j]->c_question;
						$rows[$i]->c_point = $rows2[$j]->c_point;
						$rows[$i]->c_qtype = _JLMS_QUIZ_QUEST_POOL_GQP_SHORT . ' - ' . $rows2[$j]->qtype_full;
						break;
					}
				}
			}
		}
	}
	
	

	JLMS_quiz_admin_html_class::JQ_view_stuReport( $rows, $pageNav, $option, $page, $course_id, $id);
}
function JQ_delete_quizReport( &$cid, $option, $page, $course_id ) {
	global $JLMS_DB, $Itemid;
	if (count( $cid )) {
		JLMS_quiz_admin_class::JQ_delete_quizReportA( $cid );
	}
	JLMSRedirect( sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=quizzes&id=$course_id&page=reports") );
}
function JQ_delete_quizReportA( &$cid ) {
	global $JLMS_DB;
	$cids = implode( ',', $cid );
	$query = "SELECT c_id FROM #__lms_quiz_r_student_question"
	. "\n WHERE c_stu_quiz_id IN ( $cids )";
	$JLMS_DB->SetQuery( $query );
	$stu_q_id = $JLMS_DB->LoadResultArray();
	if ((!is_array($stu_q_id)) || empty($stu_q_id)) $stu_q_id = array(0);
	$stu_cids = implode( ',', $stu_q_id );
	$query = "DELETE FROM #__lms_quiz_r_student_blank"
	. "\n WHERE c_sq_id IN ( $stu_cids )";
	$JLMS_DB->setQuery( $query );
	if (!$JLMS_DB->query()) {
		echo "<script> alert('".$JLMS_DB->getErrorMsg()."'); window.history.go(-1); </script>\n";
	}
	$query = "DELETE FROM #__lms_quiz_r_student_choice"
	. "\n WHERE c_sq_id IN ( $stu_cids )";
	$JLMS_DB->setQuery( $query );
	if (!$JLMS_DB->query()) {
		echo "<script> alert('".$JLMS_DB->getErrorMsg()."'); window.history.go(-1); </script>\n";
	}
	$query = "DELETE FROM #__lms_quiz_r_student_hotspot"
	. "\n WHERE c_sq_id IN ( $stu_cids )";
	$JLMS_DB->setQuery( $query );
	if (!$JLMS_DB->query()) {
		echo "<script> alert('".$JLMS_DB->getErrorMsg()."'); window.history.go(-1); </script>\n";
	}
	$query = "DELETE FROM #__lms_quiz_r_student_matching"
	. "\n WHERE c_sq_id IN ( $stu_cids )";
	$JLMS_DB->setQuery( $query );
	if (!$JLMS_DB->query()) {
		echo "<script> alert('".$JLMS_DB->getErrorMsg()."'); window.history.go(-1); </script>\n";
	}
	$query = "DELETE FROM #__lms_quiz_r_student_survey"
	. "\n WHERE c_sq_id IN ( $stu_cids )";
	$JLMS_DB->setQuery( $query );
	if (!$JLMS_DB->query()) {
		echo "<script> alert('".$JLMS_DB->getErrorMsg()."'); window.history.go(-1); </script>\n";
	}
	$query = "DELETE FROM #__lms_quiz_r_student_question"
	. "\n WHERE c_stu_quiz_id IN ( $cids )";
	$JLMS_DB->setQuery( $query );
	if (!$JLMS_DB->query()) {
		echo "<script> alert('".$JLMS_DB->getErrorMsg()."'); window.history.go(-1); </script>\n";
	}
	$query = "DELETE FROM #__lms_quiz_r_student_quiz"
	. "\n WHERE c_id IN ( $cids )";
	$JLMS_DB->setQuery( $query );
	if (!$JLMS_DB->query()) {
		echo "<script> alert('".$JLMS_DB->getErrorMsg()."'); window.history.go(-1); </script>\n";
	}
	$query = "DELETE FROM #__lms_quiz_r_student_quiz_pool"
	. "\n WHERE start_id IN ( $cids )";
	$JLMS_DB->setQuery( $query );
	if (!$JLMS_DB->query()) {
		echo "<script> alert('".$JLMS_DB->getErrorMsg()."'); window.history.go(-1); </script>\n";
	}
}


function JQ_delete_stuReport( &$cid, $option, $page, $course_id ) {
	global $JLMS_DB, $id;
	if (count( $cid )) {
		$cids = implode( ',', $cid );
		$query = "DELETE FROM #__lms_quiz_r_student_blank"
		. "\n WHERE c_sq_id IN ( $cids )";
		$JLMS_DB->setQuery( $query );
		if (!$JLMS_DB->query()) {
			echo "<script> alert('".$JLMS_DB->getErrorMsg()."'); window.history.go(-1); </script>\n";
		}
		$query = "DELETE FROM #__lms_quiz_r_student_choice"
		. "\n WHERE c_sq_id IN ( $cids )";
		$JLMS_DB->setQuery( $query );
		if (!$JLMS_DB->query()) {
			echo "<script> alert('".$JLMS_DB->getErrorMsg()."'); window.history.go(-1); </script>\n";
		}
		$query = "DELETE FROM #__lms_quiz_r_student_hotspot"
		. "\n WHERE c_sq_id IN ( $cids )";
		$JLMS_DB->setQuery( $query );
		if (!$JLMS_DB->query()) {
			echo "<script> alert('".$JLMS_DB->getErrorMsg()."'); window.history.go(-1); </script>\n";
		}
		$query = "DELETE FROM #__lms_quiz_r_student_matching"
		. "\n WHERE c_sq_id IN ( $cids )";
		$JLMS_DB->setQuery( $query );
		if (!$JLMS_DB->query()) {
			echo "<script> alert('".$JLMS_DB->getErrorMsg()."'); window.history.go(-1); </script>\n";
		}
		$query = "DELETE FROM #__lms_quiz_r_student_survey"
		. "\n WHERE c_sq_id IN ( $cids )";
		$JLMS_DB->setQuery( $query );
		if (!$JLMS_DB->query()) {
			echo "<script> alert('".$JLMS_DB->getErrorMsg()."'); window.history.go(-1); </script>\n";
		}
		$query = "DELETE FROM #__lms_quiz_r_student_question"
		. "\n WHERE c_id IN ( $cids )";
		$JLMS_DB->setQuery( $query );
		if (!$JLMS_DB->query()) {
			echo "<script> alert('".$JLMS_DB->getErrorMsg()."'); window.history.go(-1); </script>\n";
		}
	}
	$c_id = intval( mosGetParam($_REQUEST, 'c_id', 0));
	JLMSRedirect( sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=quizzes&id=$course_id&page=stu_reportA&c_id=$c_id") );
	
}
function JQ_view_questReport( $id, $option, $page, $course_id ) {
	global $JLMS_DB;
	$query = "SELECT q.c_type, q.c_id,q.c_image, sq.c_stu_quiz_id, q.c_question, q.c_pool, q.c_pool_gqp FROM #__lms_quiz_t_question as q, #__lms_quiz_r_student_question as sq"
	. "\n WHERE q.c_id = sq.c_question_id and sq.c_id = '".$id."'"
	;
	$JLMS_DB->SetQuery( $query );
	$q_data = $JLMS_DB->LoadObjectList();
	
	$lists = array();
	if (count($q_data)) {
		$q_type = $q_data[0]->c_type;
		$q_id = $q_data[0]->c_id;
		$qid = $q_data[0]->c_stu_quiz_id;
		if ($q_type == 20) {
			$query = "SELECT * FROM #__lms_quiz_t_question WHERE c_id = ".$q_data[0]->c_pool;
			$JLMS_DB->SetQuery($query);
			$rtrtr = $JLMS_DB->LoadObject();
			if (is_object($rtrtr)) {
				$q_id = $rtrtr->c_id;
				$q_type = $rtrtr->c_type;
				$q_data[0]->c_question = $rtrtr->c_question;
			}
		}
		
		if ($q_type == 21) {
			$query = "SELECT * FROM #__lms_quiz_t_question WHERE c_id = ".$q_data[0]->c_pool_gqp;
			$JLMS_DB->SetQuery($query);
			$rtrtr = $JLMS_DB->LoadObject();
			if (is_object($rtrtr)) {
				$q_id = $rtrtr->c_id;
				$q_type = $rtrtr->c_type;
				$q_data[0]->c_question = $rtrtr->c_question;
			}
		}
		

		$query = "SELECT u.username, u.name, u.email, u.id FROM #__users as u, #__lms_quiz_r_student_quiz as q WHERE q.c_id = '".$qid."'"
		. "\n and q.c_student_id = u.id";
		$JLMS_DB->SetQuery( $query );
		$user_info = $JLMS_DB->LoadObjectList();
		$group = '';
		$user_id = isset($user_info[0]->id)?$user_info[0]->id:0;
		if ($user_id) {
			$query = "SELECT a.ug_name FROM #__lms_usergroups as a, #__lms_users_in_groups as b"
			. "\n WHERE b.course_id = '".$course_id."' AND b.user_id = '".$user_id."' AND b.group_id = a.id";
			$JLMS_DB->SetQuery( $query );
			$group = $JLMS_DB->LoadResult();
		}
		if (count($user_info)) { $lists['user'] = $user_info[0];}
		else { $lists['user']->username = "Anonymous"; $lists['user']->name = " - "; $lists['user']->email = " - "; }
		$lists['user']->usergroup = $group;
		switch ($q_type) {
			case 1:
			case 2:
			case 3:
				$query = "SELECT c.*, sc.c_id as sc_id FROM #__lms_quiz_t_choice as c LEFT JOIN #__lms_quiz_r_student_choice as sc ON c.c_id = sc.c_choice_id"
				. "\n and sc.c_sq_id = '".$id."'"
				. "\n WHERE c.c_question_id = '".$q_id."'"
				. "\n ORDER BY c.ordering, c.c_id"
				;
				$JLMS_DB->SetQuery( $query );
				$answer = $JLMS_DB->LoadObjectList();
				$lists['id'] = $id;
				$lists['qid'] = $qid;
				$lists['question'] = $q_data[0]->c_question;
				JLMS_quiz_admin_html_class::JQ_view_questionReport( 1, $answer, $option, $page, $course_id, $lists);
			break;
			case 4:
			case 5:
				$query = "SELECT * FROM #__lms_quiz_t_matching as m LEFT JOIN #__lms_quiz_r_student_matching as sm"
				. "\n ON m.c_id = sm.c_matching_id and sm.c_sq_id = '".$id."'"
				. "\n WHERE m.c_question_id = '".$q_id."'"
				. "\n ORDER BY m.ordering, m.c_id"
				;
				$JLMS_DB->SetQuery( $query );
				$answer = $JLMS_DB->LoadObjectList();
				$lists['id'] = $id;
				$lists['qid'] = $qid;
				$lists['question'] = $q_data[0]->c_question;
				JLMS_quiz_admin_html_class::JQ_view_questionReport( 4, $answer, $option, $page, $course_id, $lists);
			break;
			case 6:
				$query = "SELECT * FROM #__lms_quiz_r_student_blank WHERE c_sq_id = '".$id."'";
				$JLMS_DB->SetQuery( $query );
				$answer = $JLMS_DB->LoadObjectList();
				if (!count($answer)) { $answer = array(); $answer[0]->c_answer = ''; }
				$lists['id'] = $id;
				$lists['qid'] = $qid;
				$lists['question'] = $q_data[0]->c_question;
				JLMS_quiz_admin_html_class::JQ_view_questionReport( 6, $answer[0], $option, $page, $course_id, $lists);
			break;
			case 7:
				$query = "SELECT * FROM #__lms_quiz_r_student_hotspot WHERE c_sq_id = '".$id."'";
				$JLMS_DB->SetQuery( $query );
				$answer = $JLMS_DB->LoadObjectList();
				if (!count($answer)) { $answer = array(); $answer[0]->c_select_x = 0; $answer[0]->c_select_y = 0; }
				$lists['id'] = $id;
				$lists['qid'] = $qid;
				$lists['question'] = $q_data[0]->c_question;
				
				$query = "SELECT * FROM #__lms_quiz_t_hotspot WHERE c_question_id = '".$q_id."'";
				$JLMS_DB->SetQuery( $query );
				$hotspot = $JLMS_DB->LoadObjectList();
				if (!count($hotspot)) { $hotspot = array(); $hotspot[0]->c_start_x = 0; $hotspot[0]->c_start_y = 0; $hotspot[0]->c_width = 0; $hotspot[0]->c_height = 0; }
				$lists['image'] = $q_data[0]->c_image;
				$lists['hotspot'] = $hotspot[0];
				JLMS_quiz_admin_html_class::JQ_view_questionReport( 7, $answer[0], $option, $page, $course_id, $lists);
			break;
			case 8:
				$query = "SELECT * FROM #__lms_quiz_r_student_survey WHERE c_sq_id = '".$id."'";
				$JLMS_DB->SetQuery( $query );
				$answer = $JLMS_DB->LoadObjectList();
				if (!count($answer)) { $answer = array(); $answer[0]->c_answer = ''; }
				$lists['id'] = $id;
				$lists['qid'] = $qid;
				$lists['question'] = $q_data[0]->c_question;
				JLMS_quiz_admin_html_class::JQ_view_questionReport( 6, $answer[0], $option, $page, $course_id, $lists);
			break;
			case 9:
				$query = "SELECT * FROM #__lms_quiz_r_student_scale WHERE c_sq_id = '".$id."'";
				$JLMS_DB->SetQuery( $query );
				$lists['id'] = $id;
				$lists['qid'] = $qid;
				$lists['question'] = $q_data[0]->c_question;
				$answers = $JLMS_DB->LoadObjectList();
				$answer = array();
					for($p=0;$p<count($answers);$p++)
					{
						$answer[$p][0] = $answers[$p]->q_scale_id;
						$answer[$p][1] = $answers[$p]->scale_id;
					}
				$query = "SELECT * FROM #__lms_quiz_t_scale WHERE c_question_id = '".$q_id."'"
				. "\n ORDER BY ordering";
				$JLMS_DB->SetQuery( $query );
				$scale_data = $JLMS_DB->LoadObjectList();
				for($i=0;$i<count($scale_data);$i++)
				{
					$scale_data[$i]->inchek = '';
					foreach ($answer as $uansw)
					{
						if($uansw[0] == $scale_data[$i]->c_id)
							$scale_data[$i]->inchek = $uansw[1];
					}
				}
				JLMS_quiz_admin_html_class::JQ_view_questionReport( 9, $scale_data, $option, $page, $course_id, $lists);
			break;
			
		}
	} else {
		$stu_id = intval(mosGetParam($_REQUEST, 'stu_id', 0));
		JLMSRedirect( sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=quizzes&id=$course_id&page=stu_reportA&c_id=$stu_id") );
		#JLMS_quiz_admin_html_class::JQ_view_BlankFormPage( $option, $page, $course_id, $stu_id );
	}
}

	#######################################
	###	--- ---		CHARTS  	--- --- ###

function JQ_ViewCharts($quiz_id, $option , $id, $gqp = false) {
	global $JLMS_DB, $JLMS_CONFIG, $JLMS_SESSION, $my;
	
	$JLMS_ACL = & JLMSFactory::getACL();
	$usertype_simple = $JLMS_ACL->_role_type;
//	$usertype_simple = JLMS_GetUserType_simple($my->id, false, true);

	//-------------------------------------------------------------------
	//FLMS multicat
	$levels = array();
	if ($gqp) {
		/*
		$query = "SELECT * FROM #__lms_gqp_cats_config ORDER BY id";
		$JLMS_DB->setQuery($query);
		$levels = $JLMS_DB->loadObjectList();
		*/
		if(count($levels) == 0){
			for($i=0;$i<15;$i++){
				$num = $i + 1;
				if($i>0){
//					$levels[$i]->cat_name = _JLMS_COURSES_COURSES_GROUPS;	
					$levels[$i]->cat_name = 'Level #'.$num;
				} else {
//					$levels[$i]->cat_name = _JLMS_COURSES_COURSES_GROUPS;
					$levels[$i]->cat_name = 'Level #'.$num;
				}
			}
		}

		$level_id = array();
		for($i=0;$i<count($levels);$i++){
			if($i == 0){
				$level_id[$i] = intval( mosGetParam( $_REQUEST, 'filter_id_'.$i.'', $JLMS_SESSION->get('GQP_filter_id_'.$i.'', 0) ) );
				$JLMS_SESSION->set('GQP_filter_id_'.$i.'', $level_id[$i]);
			} else {
				$level_id[$i] = intval( mosGetParam( $_REQUEST, 'filter_id_'.$i.'', $JLMS_SESSION->get('GQP_filter_id_'.$i.'', 0) ) );
				$JLMS_SESSION->set('GQP_filter_id_'.$i.'', $level_id[$i]);
			}
			if($i == 0){
				$parent_id[$i] = 0;
			} else {
				$parent_id[$i] = $level_id[$i-1];
			}
			if($i == 0 || $parent_id[$i]){ //(Max): extra requests
				$query = "SELECT count(id) FROM `#__lms_gqp_cats` WHERE parent = '".$parent_id[$i]."' ORDER BY c_category";
				$JLMS_DB->setQuery($query);
				$groups = $JLMS_DB->loadResult();
				if($groups==0){
					$level_id[$i] = 0;	
					$JLMS_SESSION->set('GQP_filter_id_'.$i.'', $level_id[$i]);
				}
			}
		}
		
		for($i=0;$i<count($levels);$i++){
			if($i > 0 && $level_id[$i - 1] == 0){
				$level_id[$i] = 0;
				$JLMS_SESSION->set('GQP_filter_id_'.$i.'', $level_id[$i]);
				$parent_id[$i] = 0;
			} elseif($i == 0 && $level_id[$i] == 0) {
				$level_id[$i] = 0;
				$JLMS_SESSION->set('GQP_filter_id_'.$i.'', $level_id[$i]);
				$parent_id[$i] = 0;
			}
		}
		
		$javascript = 'onclick="javascript:read_filter();" onchange="javascript:write_filter();document.adminFormQ.page.value=\'quiz_bars_gqp\';document.adminFormQ.submit();"';
		
		$query1 = "SELECT group_id FROM `#__lms_users_in_global_groups` WHERE user_id = '".$my->id."'";
		$JLMS_DB->setQuery($query1);
		$user_group_ids = $JLMS_DB->loadResultArray();
		
		for($i=0;$i<count($levels);$i++) {
			if($i == 0 || $parent_id[$i]){ //(Max): extra requests
				if( $parent_id[$i] == 0 && $usertype_simple == 1) { //(Max): roletype_id
					$query = "SELECT * FROM `#__lms_gqp_cats` WHERE `parent` = '0'";
					$query .= "\n ORDER BY `c_category`";
				}
				else {
					$query = "SELECT * FROM `#__lms_gqp_cats` WHERE parent = '".$parent_id[$i]."' ORDER BY c_category";
				}
				
				$JLMS_DB->setQuery($query);
				$groups = $JLMS_DB->loadObjectList();
				
				if($parent_id[$i] && $i > 0 && count($groups)) {
					$type_level[$i][] = mosHTML::makeOption( 0, ' &nbsp; ' );
					foreach ($groups as $group){
						$type_level[$i][] = mosHTML::makeOption( $group->id, $group->c_category );
					}
					$lists['filter_'.$i.''] = mosHTML::selectList($type_level[$i], 'filter_id_'.$i.'', 'class="inputbox" style="width: 266px;" size="1" '.$javascript, 'value', 'text', $level_id[$i] ); //onchange="document.location.href=\''. $link_multi .'\';"
				} elseif($i == 0) {
					$type_level[$i][] = mosHTML::makeOption( 0, ' &nbsp; ' );
					foreach ($groups as $group){
						$type_level[$i][] = mosHTML::makeOption( $group->id, $group->c_category );
					}
					$lists['filter_'.$i.''] = mosHTML::selectList($type_level[$i], 'filter_id_'.$i.'', 'class="inputbox" style="width: 266px;" size="1" '.$javascript, 'value', 'text', $level_id[$i] ); //onchange="document.location.href=\''. $link_multi .'\';"
				}
			}
		}	
	}
	//-------------------------------------------------------------------
	
	$is_pool = 0;
	$showtype_id = JRequest::getVar('showtype_id', 0, '','ALNUM' );
	
	if(!$quiz_id)
	$quiz_id = intval(mosGetParam($_REQUEST,'quiz_id',-1));
	if($quiz_id == -1 || $quiz_id == 0) {$is_pool = 1; $quiz_id = 0;}
	$group_id = intval(mosGetParam($_REQUEST, 'group_id', 0));	
	
	
	
	$questCatId = 0;
	if( strpos( $showtype_id, SL_CATPREF ) !== false ) 
	{
		$questCatId = (int) ltrim( $showtype_id, SL_CATPREF);
		$showtype_id = 0;
	}
	
	if($group_id){

		if(!$gqp) {
			$query = "SELECT b.user_id FROM #__lms_users_in_global_groups as b, #__lms_users_in_groups as c";
			$query .= "\n WHERE b.user_id = c.user_id AND c.course_id = '".$id."'";
			$query .= "\n AND b.group_id = '".$group_id."'";
		}
		else {
			$query = "SELECT b.user_id FROM #__lms_users_in_global_groups as b";
			$query .= "\n WHERE ";
			$query .= "\n b.group_id = '".$group_id."'";
		}
		
	} else {
		$query = "SELECT c.user_id FROM #__lms_users_in_groups as c";
		$query .= "\n WHERE c.course_id = '".$id."'";
		
	}
	$JLMS_DB->setQuery($query);
	$user_in_groups = $JLMS_DB->loadResultArray();

	$str_user_in_groups = '0';
	if(count($user_in_groups)){
		$str_user_in_groups = implode(",", $user_in_groups);
	}
	
//	echo '<pre>';
//	print_r($user_in_groups);
//	echo '</pre>';
	$pageNav = '';

	if(!$gqp) {
		
		$AND = '';
		if( $questCatId ) 
		{
			$AND = ' AND qc.c_id = '.$questCatId;
		}
		
		$query = "SELECT a.*, b.c_qtype as qtype_full, c.c_title as quiz_name, qc.c_category"
		. "\n FROM #__lms_quiz_t_question a LEFT JOIN #__lms_quiz_t_qtypes b ON b.c_id = a.c_type LEFT JOIN #__lms_quiz_t_quiz c ON a.c_quiz_id = c.c_id AND c.course_id = '".$id."'"
		. "\n LEFT JOIN #__lms_quiz_t_category as qc ON a.c_qcat = qc.c_id AND qc.course_id = '".$id."' AND qc.is_quiz_cat = 0"
		. "\n WHERE a.course_id = '".$id."'"
		. "\n AND c_quiz_id = '".$quiz_id."'"
		. $AND
		. "\n ORDER BY a.ordering, a.c_id"
		;

		$JLMS_DB->setQuery( $query );
		$total = count($JLMS_DB->loadObjectList());
		
		$limit = intval( mosGetParam( $_GET, 'limit', $JLMS_SESSION->get('list_limit', $JLMS_CONFIG->get('list_limit')) ) );
		$JLMS_SESSION->set('list_limit', $limit);
		$limitstart = intval( mosGetParam( $_REQUEST, 'limitstart', 0 ) );
		
		require_once(_JOOMLMS_FRONT_HOME . DS . "includes" . DS . "classes" . DS . "lms.pagination.php");
		$pageNav = new JLMSPageNav( $total, $limitstart, $limit );
		$query = "SELECT a.*, b.c_qtype as qtype_full, c.c_title as quiz_name, qc.c_category"
		. "\n FROM #__lms_quiz_t_question a LEFT JOIN #__lms_quiz_t_qtypes b ON b.c_id = a.c_type LEFT JOIN #__lms_quiz_t_quiz c ON a.c_quiz_id = c.c_id AND c.course_id = '".$id."'"
		. "\n LEFT JOIN #__lms_quiz_t_category as qc ON a.c_qcat = qc.c_id AND qc.course_id = '".$id."' AND qc.is_quiz_cat = 0"
		. "\n WHERE a.course_id = '".$id."'"
		. "\n AND c_quiz_id = '".$quiz_id."'"
		. $AND
		. "\n ORDER BY a.ordering, a.c_id"
		. "\n LIMIT $pageNav->limitstart, $pageNav->limit"
		;

	} else {
		//echo "qqqq"; die;
		
		$str = '';
		//NEW MUSLTICATS
		$tmp_level = array();
		$last_catid = 0;
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
		if(!$i){
			foreach($_SESSION as $key=>$item){
				if(preg_match('#GQP_filter_id_(\d+)#', $key, $result)){
					if($item){
						$tmp_level[$i] = $result;
						$last_catid = $item;
						$i++;
					}	
				}	
			}	
		}
		
		$query = "SELECT * FROM #__lms_gqp_cats"
		. "\n ORDER BY id";
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
		$catids = implode(",", $tmp_cats_filter);
		
		if($last_catid && count($tmp_cats_filter)){
			$str .= "\n AND a.c_qcat IN (".$catids.")";
		}
		//NEW MUSLTICATS
		
		/*
		for ($i=count($level_id);$i>-1;$i--) {
			if(isset($level_id[$i]) && $level_id[$i]) {
				$str = "\n AND d.cat_id = ".$level_id[$i]."";
				break;
			}
		}
		*/
		
		/*---------------navigation-------------------*/
		
		$query = "SELECT a.*, b.c_qtype as qtype_full, qc.c_category"
		. "\n FROM" 
		. "\n #__lms_quiz_t_question a," 
		. "\n #__lms_quiz_t_qtypes b," 
		. "\n #__lms_gqp_cats as qc" 
		. "\n WHERE b.c_id = a.c_type" 
		. "\n AND a.c_quiz_id = 0"
		. "\n AND a.course_id = 0"
		. $str
		. "\n GROUP BY a.c_id" 
		. "\n ORDER BY a.ordering, a.c_id";
	
		$JLMS_DB->setQuery( $query );
		$total = count($JLMS_DB->loadObjectList());
		
		$limit = intval( mosGetParam( $_GET, 'limit', $JLMS_SESSION->get('list_limit', $JLMS_CONFIG->get('list_limit')) ) );
		$JLMS_SESSION->set('list_limit', $limit);
		$limitstart = intval( mosGetParam( $_REQUEST, 'limitstart', 0 ) );
		
		require_once(_JOOMLMS_FRONT_HOME . DS . "includes" . DS . "classes" . DS . "lms.pagination.php");
		$pageNav = new JLMSPageNav( $total, $limitstart, $limit );

		/*--------------------------------------------*/
			
		$query = "SELECT a.*, b.c_qtype as qtype_full, qc.c_category"
		. "\n FROM" 
		. "\n #__lms_quiz_t_question a," 
		. "\n #__lms_quiz_t_qtypes b," 
		. "\n #__lms_gqp_cats as qc" 
		. "\n WHERE 1"
		. "\n AND b.c_id = a.c_type" 
		. "\n AND a.c_quiz_id = 0"
		. "\n AND a.course_id = 0"
		. $str
		. "\n GROUP BY a.c_id" 
		. "\n ORDER BY a.ordering, a.c_id"
		. "\n LIMIT $pageNav->limitstart, $pageNav->limit"
		;
	}
	
	$JLMS_DB->setQuery( $query );
	$rows = $JLMS_DB->loadObjectList();
	
	for($i=0;$i<count($rows);$i++) {
		$str = '_JLMS_QUIZ_QTYPE_'.$rows[$i]->c_type;
		if (defined($str)) {
			$rows[$i]->qtype_full = constant($str);
		}
	}
		
	$q_from_pool = array();
	foreach ($rows as $row) {
		if ($row->c_type == 20) {
			$q_from_pool[] = $row->c_pool;
		}
	}
	if (count($q_from_pool)) {
		$qp_ids =implode(',',$q_from_pool);
		$query = "SELECT a.*, b.c_qtype as qtype_full FROM #__lms_quiz_t_question as a LEFT JOIN #__lms_quiz_t_qtypes b ON b.c_id = a.c_type"
		. "\n WHERE a.course_id = '".$id."' AND a.c_id IN ($qp_ids)";
		$JLMS_DB->setQuery( $query );
		$rows2 = $JLMS_DB->loadObjectList();
		
		for($i=0;$i<count($rows2);$i++) {
			$str = '_JLMS_QUIZ_QTYPE_'.$rows2[$i]->c_type;
			if (defined($str)) {
				$rows2[$i]->qtype_full = constant($str);
			}
		}
		
		for ($i=0, $n=count( $rows ); $i < $n; $i++) {
			if ($rows[$i]->c_type == 20) {
				for ($j=0, $m=count( $rows2 ); $j < $m; $j++) {
					if ($rows[$i]->c_pool == $rows2[$j]->c_id) {
						$rows[$i]->c_question = $rows2[$j]->c_question;
						$rows[$i]->c_type = $rows2[$j]->c_type;
						$rows[$i]->qtype_full = _JLMS_QUIZ_QUEST_POOL_SHORT . ' - ' . $rows2[$j]->qtype_full;
						break;
					}
				}
			}
		}
	}
	
	$q_from_pool_gqp = array();
	foreach ($rows as $row) {
		if ($row->c_type == 21) {
			$q_from_pool_gqp[] = $row->c_pool_gqp;
		}
	}
	
	if (count($q_from_pool_gqp)) {
		$qp_ids_gqp =implode(',',$q_from_pool_gqp);
		$query = "SELECT a.*, b.c_qtype as qtype_full FROM #__lms_quiz_t_question as a LEFT JOIN #__lms_quiz_t_qtypes b ON b.c_id = a.c_type"
		. "\n WHERE a.course_id = 0 AND a.c_id IN ($qp_ids_gqp)";
		$JLMS_DB->setQuery( $query );
		$rows2 = $JLMS_DB->loadObjectList();
		
		for($i=0;$i<count($rows2);$i++) {
			$str = '_JLMS_QUIZ_QTYPE_'.$rows2[$i]->c_type;
			if (defined($str)) {
				$rows2[$i]->qtype_full = constant($str);
			}
		}
		
		for ($i=0, $n=count( $rows ); $i < $n; $i++) {
			if ($rows[$i]->c_type == 21) {
				for ($j=0, $m=count( $rows2 ); $j < $m; $j++) {
					if ($rows[$i]->c_pool_gqp == $rows2[$j]->c_id) {
						$rows[$i]->c_question = $rows2[$j]->c_question;
						$rows[$i]->c_type = $rows2[$j]->c_type;
						$rows[$i]->qtype_full = _JLMS_QUIZ_QUEST_POOL_GQP_SHORT . ' - ' . $rows2[$j]->qtype_full;
						break;
					}
				}
			}
		}

	}
	// 18 August 2007 - changes (DEN) - added check for GD and FreeType support
		$generate_images = true;
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
	if ($JLMS_CONFIG->get('temp_folder', '') && $generate_images) { // temp folder setup is ready.

//--------- array of bar-images
	$rows_result = array();

	$img_arr = array();
	$title_arr = array();
	$count_graph =array();
	
	$img_correct = array();
	$title_correct = array();
	
	for($i=0,$n=count($rows);$i<$n;$i++) {
		$row = $rows[$i];
		$quest_params = new JLMSParameters($row->params);
		$z = 1;
		$show_case = true;
		if($showtype_id && !$quest_params->get('survey_question'))
		$show_case = false;
		
		if($row->c_pool) {
			$c_question_id = $row->c_pool;
		}
		elseif($row->c_pool_gqp) {	
			$c_question_id = $row->c_pool_gqp;
		}
		else {
			$c_question_id = $row->c_id;
		}
		
		if($gqp) {
			$query = "SELECT c_id FROM #__lms_quiz_t_question WHERE c_pool_gqp = '".($row->c_id)."'";
			$JLMS_DB->setQuery( $query );
			$gqp_id = $JLMS_DB->loadResult();
			
			if($gqp_id) {
				$row->c_id = $gqp_id;
			}
		}
		
		if($show_case){
			require_once(_JOOMLMS_FRONT_HOME . "/includes/libraries/lms.lib.graph.php");
			$obj_GraphStat = JLMS_GraphStatistics($option, $id, $quiz_id, $i, $z, $row, $c_question_id, $group_id, $str_user_in_groups);
			
			foreach($obj_GraphStat as $key=>$item){
				if(preg_match_all('#([a-z]+)_(\w+)#', $key, $out, PREG_PATTERN_ORDER)){
					if($out[1][0] == 'img'){
						$img_arr[$i]->$out[2][0] = $item;	
						$rows_result[$i]->images_array->$out[2][0] = $item;
					} else 
					if($out[1][0] == 'title'){
						$title_arr[$i]->$out[2][0] = $item;	
						$rows_result[$i]->title_array->$out[2][0] = $item;	
					} else 
					if($out[1][0] == 'count'){
						$count_graph[$i]->$out[2][0] = $item;	
						$rows_result[$i]->count_array->$out[2][0] = $item;	
					}
				}	
			}
		
		
		/*if(isset($obj_GraphStat->img_graph)){
			$img_arr[$i]->graph = $obj_GraphStat->img_graph;
		}*/
		/*$img_arr[$i]->graph = $JLMS_CONFIG->get('temp_folder', '').'/'.$filename;
		$title_arr[$i]->graph = trim(strip_tags($row->c_question)); 	
		$count_graph[$i]->graph = 1;
		$img_arr[$i]->correct = $JLMS_CONFIG->get('temp_folder', '').'/'.$filename;
		$title_arr[$i]->correct = trim(strip_tags($row->c_question));
		$count_graph[$i]->correct = 1;*/
		
		}
	}
	
	}
	
//	echo '<pre>';
//	print_r($rows_result);
//	echo '</pre>';
	
	if(!$gqp) {
	$lists = array();
	}
	$javascript = 'onchange="if(document.adminFormQ.quiz_id.value != 0){document.adminFormQ.submit();}"';
	$query = "SELECT c_id AS value, c_title AS text"
	. "\n FROM #__lms_quiz_t_quiz"
	. "\n WHERE course_id = '".$id."'"
	. "\n ORDER BY c_title"
	;
	$JLMS_DB->setQuery( $query );
	$quizzes = array();
	$quizzes[] = mosHTML::makeOption( '0', _JLMS_SB_SELECT_QUIZ );
	$quizzes[] = mosHTML::makeOption( '-1', _JLMS_QUIZ_QUEST_POOL );
	$quizzes = array_merge( $quizzes, $JLMS_DB->loadObjectList() );
	$quiz = mosHTML::selectList( $quizzes,'quiz_id', 'class="inputbox" size="1" '. $javascript, 'value', 'text', ($is_pool ? -1 : $quiz_id) ); 
	$lists['quiz'] = $quiz;
	
	if($gqp) {
	$javascript = 'onchange="document.adminFormQ.page.value=\'quiz_bars_gqp\'; document.adminFormQ.submit();"';
	}
	else {
	$javascript = 'onchange="document.adminFormQ.submit();"';
	}
	
	$query = "SELECT CONCAT( '".SL_CATPREF."', c_id ) AS value, c_category AS text"
	. "\n FROM #__lms_quiz_t_category"
	. "\n WHERE is_quiz_cat = 0 AND course_id = ".$id
	. "\n ORDER BY c_category"
	;
	$JLMS_DB->setQuery( $query );
		
	
	$showtype = array();
	$showtype[] = mosHTML::makeOption( '0', _JLMS_QUIZ_ALL_QUESTIONS );
	$showtype[] = mosHTML::makeOption( '1', _JLMS_QUIZ_SURVEY_QUESTIONS );
	$showtype = array_merge( $showtype, $JLMS_DB->loadObjectList() );
	if( $questCatId ) $showtype_id = SL_CATPREF.$questCatId;
	$shtype = mosHTML::selectList( $showtype,'showtype_id', 'class="inputbox" size="1" '. $javascript, 'value', 'text', $showtype_id ); 
	$lists['showtype'] = $shtype;

	$query = "SELECT id as value, ug_name as text FROM #__lms_usergroups WHERE course_id = '0'";
	$JLMS_DB->setQuery($query);
	$showgroups = array();
	$showgroups[] = mosHTML::makeOption( '0', _JLMS_ATT_FILTER_ALL_GROUPS );
	$showgroups = array_merge($showgroups, $JLMS_DB->loadObjectList());
	$lists['showgroups'] = mosHTML::selectList( $showgroups, 'group_id', 'class="inputbox" size="1" '. $javascript, 'value', 'text', $group_id ); 
	
	JLMS_quiz_admin_html_class::JQ_showBars( $rows_result, $lists, $id, $option, $gqp, $levels, $pageNav );
}


function JQ_ImgsList( $option, $page, $id ){
	global $JLMS_DB, $JLMS_SESSION, $JLMS_CONFIG;
	
	$limit		= intval( mosGetParam( $_GET, 'limit', $JLMS_SESSION->get('imgs_list_limit', $JLMS_CONFIG->get('list_limit')) ) );
	$limitstart = intval( mosGetParam( $_GET, 'limitstart', 0 ) );
	$JLMS_SESSION->set('imgs_list_limit', $limit);

	$query = "SELECT COUNT(*)"
	. "\n FROM #__lms_quiz_images WHERE course_id = '".$id."'";
	$JLMS_DB->setQuery( $query );
	$total = $JLMS_DB->loadResult();

	require_once(_JOOMLMS_FRONT_HOME . DS . "includes" . DS . "classes" . DS . "lms.pagination.php");
	$pageNav = new JLMSPageNav( $total, $limitstart, $limit );

	$query = "SELECT * "
	. "\n FROM #__lms_quiz_images WHERE course_id = '".$id."'"
	. "\n ORDER BY imgs_name"
	. "\n LIMIT $pageNav->limitstart, $pageNav->limit"
	;
	$JLMS_DB->setQuery( $query );
	$rows = $JLMS_DB->loadObjectList();

	JLMS_quiz_admin_html_class::JQ_showImgsList( $rows, $pageNav, $option, $page, $id );	
}

function JQ_ImgsList_v($option, $id){
	global $my, $JLMS_DB, $Itemid, $JLMS_CONFIG;
	
	$imgs_name = mosGetParam($_REQUEST, 'imgs_name', '');
	
	$course_id = $JLMS_CONFIG->get('course_id');
	$usertype = $JLMS_CONFIG->get('current_usertype', 0);
	
	if ( $course_id && ($usertype) ) {
		//JLMS_downloadFile( $id, $option, $imgs_name);
		
		$do_exit = true;
		$query = "SELECT file_name, file_srv_name FROM #__lms_files WHERE id = '".$id."'";
		$JLMS_DB->SetQuery($query);
		$file_char = $JLMS_DB->LoadObjectList();
		
		if (count($file_char)) {
			$srv_name = _JOOMLMS_DOC_FOLDER . $file_char[0]->file_srv_name;
			$file_name = $file_char[0]->file_name;
			
//			if ($new_file_name) {
//				if (strcmp(substr($new_file_name,-4,1),".")) {
//					//bad extension (must be changed)
//					$extension = substr($file_name,-4,4);
//					$file_name = $new_file_name.$extension;
//				} else {
//					$file_name = $new_file_name;
//				}
//			}
			
			
			$prefix="";
			$picout = $srv_name;
			
			//get all params
			list($im_width, $im_height, $im_ext, $attr) = getimagesize($prefix.$picout);
			$canv_width = mosGetParam($_REQUEST, 'pic_width', $im_width);
			$canv_height = mosGetParam($_REQUEST, 'pic_height', $im_height);
			$max_width = mosGetParam($_REQUEST, 'max_width', $canv_width);
			$max_height = mosGetParam($_REQUEST, 'max_height', $canv_height);
			$min_width = mosGetParam($_REQUEST, 'min_width', $canv_width);
			$min_height = mosGetParam($_REQUEST, 'min_height', $canv_height);
			$bg_color = mosGetParam($_REQUEST, 'bg_color', 'ffffff');
			
			//convert background color from hexademical to decimal
			$canv_color['red'] = hexdec(substr($bg_color, 0, 2));
			$canv_color['green'] = hexdec(substr($bg_color, 2, 2));
			$canv_color['blue'] = hexdec(substr($bg_color, 4, 2));
			
			//resize if canvas/image dimensions don't go into min/max
			//only if min/max params are set
			if ($canv_width < $min_width && isset($_REQUEST['min_width'])) { 
				$canv_height = intval($canv_height * $min_width / $canv_width); 
				$canv_width = $min_width; 
			}
			if ($canv_width > $max_width && isset($_REQUEST['max_width'])) { 
				$canv_height = intval($canv_height * $max_width / $canv_width); 
				$canv_width = $max_width; 
			}
			if ($canv_height < $min_height && isset($_REQUEST['min_height'])) { 
				$canv_width = intval($canv_width * $min_height / $canv_height); 
				$canv_height = $min_height; 
			}
			if ($canv_height > $max_height && isset($_REQUEST['max_height'])) { 
				$canv_width = intval($canv_width * $max_height / $canv_height); 
				$canv_height = $max_height; 
			}
			
			//calculate resample factor
			$factor_x = $im_width / $canv_width;
			$factor_y = $im_height / $canv_height;
			if (isset($_REQUEST['no_resample'])) {
				$factor = 1; //not resampled
			}
			else $factor = max($factor_x, $factor_y);
			
			//resample
			$im_width = intval($im_width / $factor);
			$im_height = intval($im_height / $factor);
			
			//add paddings
			$padding_width = intval(($canv_width - $im_width) / 2);
			$padding_height = intval(($canv_height - $im_height) / 2);
			
			//check source image extension and create needed canvas
			switch ($im_ext)
			{
				case 1:
				$im=imagecreatefromgif($prefix.$picout);
				$im_blank=imagecreatetruecolor($canv_width,$canv_height);
				break;
			
				case 2:
				$im=imagecreatefromjpeg($prefix.$picout);
				$im_blank=imagecreatetruecolor($canv_width,$canv_height);
				break;
			
				case 3:
				$im=imagecreatefrompng($prefix.$picout);
				$im_blank=imagecreatetruecolor($canv_width,$canv_height);
				break;
			
				default:
				$im=imagecreatefrompng($prefix."images/blank.png");
				$im_blank=imagecreatetruecolor($canv_width,$canv_height);
			}
			
			//fill background
			$color = imagecolorallocate($im_blank, $canv_color['red'], $canv_color['green'], $canv_color['blue']);
			imagefill($im_blank, 0, 0, $color);
			
			//copy resampled image to canvas
			//imagealphablending($im_blank,true);
			//imagesavealpha($im_blank,TRUE);
			imagecopyresampled($im_blank,$im,$padding_width,$padding_height,0,0,$im_width,$im_height,imagesx($im),imagesy($im));
			//imagealphablending($im_blank,false);
			//return resulting image
			header('Content-Type: image/png');
			imagepng($im_blank);
			die;
		}
		
	}
}

function JQ_ImgsList_edit($id, $option, $course_id ){
	global $my, $JLMS_DB, $Itemid, $JLMS_CONFIG;
	
	$course_id = $JLMS_CONFIG->get('course_id');
	$usertype = $JLMS_CONFIG->get('current_usertype', 0);
	if ( $course_id && ($usertype == 1) ) {
		$lists = array();
		if ($id) {
			$query = "SELECT * FROM #__lms_quiz_images WHERE c_id = '".$id."'";
			$JLMS_DB->setQuery($query);
			$row = $JLMS_DB->loadObjectList();
		} else {
			$row[0]->c_id = 0;
		}
		JLMS_quiz_admin_html_class::showEditImg( $row, $lists, $option, $course_id );
	} else {
		JLMSRedirect( sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=quizzes&id=$course_id") );
	}
}

function JQ_ImgsList_save($option){
	// Axtung!: vse tablicy nabora question options (like 't_choice', 't_matching') are stores imgs_id instead of id from #__lms_quiz_images
	global $my, $JLMS_DB, $Itemid, $JLMS_CONFIG;
	$JLMS_ACL = & JLMSFactory::getACL();
	$course_id = $JLMS_CONFIG->get('course_id');
	$c_id = intval(mosGetParam($_REQUEST, 'c_id', 0));
	
	$imgs_name_post = isset($_REQUEST['imgs_name'])?strval($_REQUEST['imgs_name']):'imgs_name';
	$imgs_name_post = (get_magic_quotes_gpc()) ? stripslashes( $imgs_name_post ) : $imgs_name_post; 
	$imgs_name_post = ampReplace(strip_tags($imgs_name_post));
	
	if(!$c_id){
		$file_id = 0;
		if (isset($_FILES['imgs']) && !empty($_FILES['imgs']['name'])) {
			$file_id = JLMS_uploadFile( $course_id, 'imgs' );
		}
		
		if($file_id){
			$query = "INSERT #__lms_quiz_images (imgs_name, imgs_id, course_id)"
			. "\n VALUES ('".$imgs_name_post."', '".$file_id."', '".$course_id."' )";
			$JLMS_DB->SetQuery( $query );
			$JLMS_DB->query();	
		}
	} else {
		$query = "UPDATE #__lms_quiz_images SET imgs_name = '".$imgs_name_post."' WHERE c_id = '".$c_id."'";
		$JLMS_DB->SetQuery( $query );
		$JLMS_DB->query();
	}
	JLMSRedirect( sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=quizzes&page=imgs&id=$course_id") );
}

function JQ_ImgsList_del($cid, $option, $page, $course_id){
	global $JLMS_DB, $JLMS_CONFIG, $Itemid;
	$course_id = $JLMS_CONFIG->get('course_id');
	$usertype = $JLMS_CONFIG->get('current_usertype', 0);
	if ( $course_id && ($usertype == 1) ) {
		if (count( $cid )) {
			$cids = implode( ',', $cid );
			$query = "SELECT * FROM #__lms_quiz_images WHERE c_id IN ( $cids ) AND course_id = '".$course_id."'";
			$JLMS_DB->setQuery( $query );
			$row = $JLMS_DB->loadObjectList();
			
			$files = array();
			$k=0;
			foreach($row as $data){
				$files[$k] = $data->imgs_id;
				$k++;
			}
			JLMS_deleteFiles($files);
			$query = "DELETE FROM #__lms_quiz_images"
			. "\n WHERE c_id IN ( $cids ) AND course_id = '".$course_id."'";
			$JLMS_DB->setQuery( $query );
			if (!$JLMS_DB->query()) {
				echo "<script> alert('".$JLMS_DB->getErrorMsg()."'); window.history.go(-1); </script>\n";
			}
		}
	}
	JLMSRedirect( sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=quizzes&id=$course_id&page=imgs") );
}

function JQ_ImgListcancel($option, $page, $id) {
	global $Itemid;
	JLMSRedirect(sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=quizzes&id=$id&page=imgs"));
}

function JQ_ViewAnswersSurvey($option, $course_id, $quiz_id, $quest_id){
	global $JLMS_DB, $Itemid;
	
	$group_id = intval(mosGetParam($_REQUEST, 'group_id', 0));
	
//	echo '<pre>';
//	print_r($_POST);
//	echo '</pre>';
	
	if($group_id){
		$query = "SELECT b.user_id FROM #__lms_users_in_global_groups as b, #__lms_users_in_groups as c";
		$query .= "\n WHERE b.user_id = c.user_id AND c.course_id = '".$course_id."'";
		$query .= "\n AND b.group_id = '".$group_id."'";
	} else {
		$query = "SELECT c.user_id FROM #__lms_users_in_groups as c";
		$query .= "\n WHERE c.course_id = '".$course_id."'";
	}
	$JLMS_DB->setQuery($query);
	$user_in_groups = $JLMS_DB->loadResultArray();
	
	$str_user_in_groups = '0';
	if(count($user_in_groups)){
		$str_user_in_groups = implode(",", $user_in_groups);
	}
	
	$query = "SELECT c_question FROM #__lms_quiz_t_question WHERE c_id = '".$quest_id."' AND c_quiz_id = '".$quiz_id."'";
	$JLMS_DB->setQuery($query);
	$question = $JLMS_DB->loadResult();
	
	$query = "SELECT c.* FROM #__lms_quiz_r_student_quiz as a, #__lms_quiz_r_student_question as q, #__lms_quiz_r_student_survey as c";
	$query .= "\n WHERE q.c_id = c.c_sq_id AND q.c_question_id = '".($quest_id)."'";
	$query .= "\n AND a.c_id = q.c_stu_quiz_id";
	if($group_id){
		$query .= "\n AND a.c_student_id IN (".$str_user_in_groups.")";
	}
	$JLMS_DB->setQuery( $query );
	$total_answers = $JLMS_DB->loadObjectList();
	
//	echo '<pre>';
//	print_r($total_count);
//	print_r($total_answers);
//	echo '</pre>';
	
	$lists = array();
	$javascript = 'onchange="if(document.adminFormQ.quiz_id.value != 0){document.adminFormQ.submit();}"';
	$query = "SELECT id as value, ug_name as text FROM #__lms_usergroups WHERE course_id = '0'";
	$JLMS_DB->setQuery($query);
	$showgroups = array();
	$showgroups[] = mosHTML::makeOption( '0', 'All Courses' );
	$showgroups = array_merge($showgroups, $JLMS_DB->loadObjectList());
	$lists['showgroups'] = mosHTML::selectList( $showgroups, 'group_id', 'class="inputbox" size="1" '. $javascript, 'value', 'text', $group_id );
	
	$extra = $group_id ? "&group_id=".$group_id."" : '';
	$link_back = sefRelToAbs("index.php?option=".$option."&Itemid=".$Itemid."&task=quizzes&page=quiz_bars&id=".$course_id."&quiz_id=".$quiz_id."".$extra);
	
	JLMS_quiz_admin_html_class::JQ_ShowAnswersSurvey($lists, $total_answers, $course_id, $quiz_id, $quest_id, $question, $link_back, $option);
}
}

class JLMS_quiz_reporting{
	
	function prepare($course_id, $user_id=0, $quiz_id){
		
		require_once(_JOOMLMS_FRONT_HOME . "/includes/libraries/lms.lib.reporting.php");
		
		$attempts = JLMS_quiz_reporting::attempts($course_id, $user_id, $quiz_id);
		
		for($i=0;$i<count($attempts);$i++){
			$attempts[$i]->questions = JLMS_quiz_reporting::questions($course_id, $attempts[$i]->c_id);
			
			for($j=0;$j<count($attempts[$i]->questions);$j++){
				$attempts[$i]->questions[$j]->question_information = array();				
				if(isset($attempts[$i]->questions[$j]->c_id)){
					$question = $attempts[$i]->questions[$j];				
					if($question->c_id){
						$attempts[$i]->questions[$j]->question_information = JLMS_quiz_reporting::question($course_id, $question->c_id); 
					}
				}
			}
		}
		
		$title_report = 'quiz_reports';
		$title_report = str_replace('_', ' ', $title_report);
		$title_report = ucwords($title_report);
		
		$html = JLMS_quiz_reporting::generate_html($attempts, $title_report);
		if($html){
			JLMS_reporting::outputXLS($html, $title_report);
		}
	}
	
	function generate_html($attempts, $title_report=''){
		global $JLMS_CONFIG;
		
		ob_start();
		JLMS_reporting::addCSSStyles();
		?>
		<table>
			<tr class="title_report">
				<td>
					<?php
					echo $title_report;
					?>
				</td>
			</tr>
			<tr>
				<td>
					<table class="after_header_info">
						<tr>
							<td class="date_title">
								Date:
							</td>
							<td class="date_value">
								<?php
								echo date("d F Y");
								?>
							</td>
						</tr>
					</table>
					<br />	
				</td>
			</tr>
			<tr>
				<td>
		
					<table cellpadding="0" cellspacing="">
						<tr>
							<td colspan="10">
								<table width="100%" cellpadding="0" cellspacing="0" border="0" class="hits_data" id="reports_quiz">
									<tr>
										<th class="header_title">
											Student Name
										</th>
										<th class="header_title">
											Username
										</th>
										<th class="hit_title">
											Quiz
										</th>
										<th class="hit_title">
											User Score
										</th>
										<th class="hit_title">
											Total Score
										</th>
										<th class="hit_title">
											Date/Time
										</th>
										<th class="hit_title">
											Question
										</th>
										<th class="hit_title">
											Quiestions Options
										</th>
										<th class="hit_title">
											User Choice
										</th>
										<th class="hit_title">
											Right Answer
										</th>
									</tr>
									
									<?php
									$k=2;
									for($i=0;$i<count($attempts);$i++){
										$row = $attempts[$i];
										
										$questions = $row->questions;
										
										$qi=0;
										foreach($questions as $question){
											
											$answers = $question->question_information['answers'];
											$data = $question->question_information['data'];
											
											$k = 3 - $k;
											for($ai=0;$ai<count($answers);$ai++){
												if($ai){
													?>
													<tr class="<?php echo 'row_'.$k;?>">
														<td colspan="7">
															
														</td>
														<?php
														if(in_array($question->c_type, array(1,12,2,13,3))){
														?>
														<td class="hit_value">
															<?php
															echo isset($answers[$ai]->c_choice) ? $answers[$ai]->c_choice : '';
															?>
														</td>
														<td class="hit_value">
															<?php 
															echo isset($answers[$ai]->sc_id) && $answers[$ai]->sc_id ? 'X' : '';
															?>
														</td>
														<td class="hit_value">
															<?php 
															echo isset($answers[$ai]->c_right) && $answers[$ai]->c_right ? 'X' : '';
															?>
														</td>
														<?php
														} else 
														if(in_array($question->c_type, array(4,5,11))){
														?>
														<td class="hit_value">
															<?php
															echo $answers[$ai]->c_left_text;
															?>
														</td>
														<td class="hit_value">
															<?php 
															echo $answers[$ai]->c_sel_text;
															?>
														</td>
														<td class="hit_value">
															<?php 
															echo $answers[$ai]->c_right_text;
															?>
														</td>
														<?php	
														} else 
														if(in_array($question->c_type, array(6))){
														?>
														<td class="hit_value">
															
														</td>
														<td class="hit_value">
															<?php 
															echo $answers[$ai]->c_answer;
															?>
														</td>
														<td class="hit_value">
															
														</td>
														<?php	
														} else 
														if(in_array($question->c_type, array(7))){
														?>
														<td class="hit_value">
															<?php 
															echo $data['c_image'];
															?>
														</td>
														<td class="hit_value">
															<?php 
															echo isset($data['c_right']) && $data['c_right'] ? 'right' : 'wrong';
															?>
														</td>
														<td class="hit_value">
															
														</td>
														<?php	
														} /*else
														if(in_array($question->c_type, array(8,9))){
														?>
														<td class="hit_value">
															c
														</td>
														<td class="hit_value">
															c
														</td>
														<td class="hit_value">
															c
														</td>
														<?php	
														}*/
														?>
													</tr>
													<?php
												} else 
												if(!$ai && !$qi){
													?>
													<tr class="<?php echo 'row_'.$k;?>">
														<td class="header_value">
															<?php
															echo $row->name;
															?>
														</td>
														<td class="header_value">
															<?php
															echo $row->username;
															?>
														</td>
														<td class="hit_value">
															<?php
															echo $row->c_title;
															?>
														</td>
														<td class="hit_value">
															<?php
															echo $row->c_full_score;
															?>
														</td>
														<td class="hit_value">
															<?php
															echo $row->c_total_score;
															?>
														</td>
														<td class="hit_value">
															<?php
															echo JLMS_dateToDisplay($row->c_date_time);
															?>
														</td>
														<td class="hit_value">
															<?php
															echo $question->c_question;
															?>
														</td>
														<?php
														if(in_array($question->c_type, array(1,12,2,13,3))){
														?>
														<td class="hit_value">
															<?php
															echo isset($answers[$ai]->c_choice) ? $answers[$ai]->c_choice : '';
															?>
														</td>
														<td class="hit_value">
															<?php 
															echo isset($answers[$ai]->sc_id) && $answers[$ai]->sc_id ? 'X' : '';
															?>
														</td>
														<td class="hit_value">
															<?php 
															echo isset($answers[$ai]->c_right) && $answers[$ai]->c_right ? 'X' : '';
															?>
														</td>
														<?php
														} else 
														if(in_array($question->c_type, array(4,5,11))){
														?>
														<td class="hit_value">
															<?php
															echo $answers[$ai]->c_left_text;
															?>
														</td>
														<td class="hit_value">
															<?php 
															echo $answers[$ai]->c_sel_text;
															?>
														</td>
														<td class="hit_value">
															<?php 
															echo $answers[$ai]->c_right_text;
															?>
														</td>
														<?php	
														} else 
														if(in_array($question->c_type, array(6))){
														?>
														<td class="hit_value">
															
														</td>
														<td class="hit_value">
															<?php 
															echo $answers[$ai]->c_answer;
															?>
														</td>
														<td class="hit_value">
															
														</td>
														<?php	
														} else 
														if(in_array($question->c_type, array(7))){
														?>
														<td class="hit_value">
															<?php 
															echo $data['c_image'];
															?>
														</td>
														<td class="hit_value">
															<?php 
															echo isset($data['c_right']) && $data['c_right'] ? 'right' : 'wrong';
															?>
														</td>
														<td class="hit_value">
															
														</td>
														<?php	
														} /*else
														if(in_array($question->c_type, array(8,9))){
														?>
														<td class="hit_value">
															z
														</td>
														<td class="hit_value">
															z
														</td>
														<td class="hit_value">
															z
														</td>
														<?php	
														}*/
														?>
													</tr>
													<?php
												} else 
												if(!$ai){
													?>
													<tr class="<?php echo 'row_'.$k;?>">
														<td colspan="6">
															
														</td>
														<td class="hit_value">
															<?php
															echo $question->c_question;
															?>
														</td>
														<?php
														if(in_array($question->c_type, array(1,12,2,13,3))){
														?>
														<td class="hit_value">
															<?php
															echo isset($answers[$ai]->c_choice) ? $answers[$ai]->c_choice : '';
															?>
														</td>
														<td class="hit_value">
															<?php 
															echo isset($answers[$ai]->sc_id) && $answers[$ai]->sc_id ? 'X' : '';
															?>
														</td>
														<td class="hit_value">
															<?php 
															echo isset($answers[$ai]->c_right) && $answers[$ai]->c_right ? 'X' : '';
															?>
														</td>
														<?php
														} else 
														if(in_array($question->c_type, array(4,5,11))){
														?>
														<td class="hit_value">
															<?php
															echo $answers[$ai]->c_left_text;
															?>
														</td>
														<td class="hit_value">
															<?php 
															echo $answers[$ai]->c_sel_text;
															?>
														</td>
														<td class="hit_value">
															<?php 
															echo $answers[$ai]->c_right_text;
															?>
														</td>
														<?php	
														} else 
														if(in_array($question->c_type, array(6))){
														?>
														<td class="hit_value">
															
														</td>
														<td class="hit_value">
															<?php 
															echo $answers[$ai]->c_answer;
															?>
														</td>
														<td class="hit_value">
															
														</td>
														<?php	
														} else 
														if(in_array($question->c_type, array(7))){
														?>
														<td class="hit_value">
															<?php 
															echo $data['c_image'];
															?>
														</td>
														<td class="hit_value">
															<?php 
															echo isset($data['c_right']) && $data['c_right'] ? 'right' : 'wrong';
															?>
														</td>
														<td class="hit_value">
															
														</td>
														<?php	
														} else
														if(in_array($question->c_type, array(8,9))){
														?>
														<td class="hit_value">
															
														</td>
														<td class="hit_value">
															
														</td>
														<td class="hit_value">
															
														</td>
														<?php	
														}
														?>
													</tr>
													<?php
												}
												$k = 3 - $k;
											}
											$k = 3 - $k;
											$qi++;
										}
									}
									?>
								</table>
							</td>
						</tr>
					</table>
					
				</td>
			</tr>
		</table>			
		
		<?php
		$html = ob_get_contents();
		ob_get_clean();
		
		return $html;
	}
	
	function attempts($course_id, $user_id=0, $quiz_id){
		$db = & JFactory::getDBO();
		$attempts = array();
		if($user_id){
			$query = "SELECT sq.c_id, sq.c_passed, sq.c_total_score, sq.c_total_time, sq.c_date_time, sq.c_passed,"
			. "\n q.c_title, q.c_author, q.c_passing_score,sq.c_student_id, u.username, u.name, u.email, q.c_full_score, q.c_id as cur_quiz_id"
			. "\n FROM #__lms_quiz_t_quiz as q, #__lms_quiz_r_student_quiz as sq"
			. "\n LEFT JOIN #__users as u ON sq.c_student_id = u.id"
			. "\n WHERE sq.c_quiz_id = q.c_id AND q.course_id = '".$course_id."'"
			. ( $quiz_id ? "\n AND sq.c_quiz_id = $quiz_id" : '' )
			. ( $user_id ? "\n AND sq.c_student_id = $user_id" : '' )
			. "\n ORDER BY sq.c_date_time DESC"
			;
			$db->setQuery($query);
			$attempts = $db->loadObjectList();
		}
		return $attempts;
	}
	
	function questions($course_id, $stu_quiz_id){
		global $JLMS_CONFIG;
		$db = & JFactory::getDBO();
		
		$questions = array();
		if($stu_quiz_id){
			$query = "SELECT count(*) FROM #__lms_quiz_r_student_quiz_pool WHERE start_id = $stu_quiz_id";
			$db->setQuery( $query );
			$is_new_version_of_quiz = $db->LoadResult();
			
			$is_new_version_of_quiz_gqp = 0;
			if ($JLMS_CONFIG->get('global_quest_pool')) {
				$query = "SELECT count(*) FROM #__lms_quiz_r_student_quiz_gqp WHERE start_id = $stu_quiz_id";
				$db->setQuery( $query );
				$is_new_version_of_quiz_gqp = $db->LoadResult();
			}
			
			if ($is_new_version_of_quiz_gqp) {
				$query = "SELECT sp.c_id, sp.c_score, q.c_type, q.c_point, q.c_question, qt.c_qtype, q.c_pool, q.c_pool_gqp"
				. "\n FROM #__lms_quiz_r_student_quiz_gqp as b, #__lms_quiz_r_student_question as sp LEFT JOIN #__lms_quiz_t_question as q ON sp.c_question_id = q.c_id LEFT JOIN #__lms_quiz_t_qtypes as qt ON q.c_type = qt.c_id"
				. "\n WHERE sp.c_stu_quiz_id = '".$stu_quiz_id."' AND sp.c_stu_quiz_id = b.start_id AND sp.c_question_id = b.quest_id"
				. "\n ORDER BY b.ordering, q.c_id"
				;
			}
			elseif ($is_new_version_of_quiz) {
				$query = "SELECT sp.c_id, sp.c_score, q.c_type, q.c_point, q.c_question, qt.c_qtype, q.c_pool, q.c_pool_gqp"
				. "\n FROM #__lms_quiz_r_student_quiz_pool as b, #__lms_quiz_r_student_question as sp LEFT JOIN #__lms_quiz_t_question as q ON sp.c_question_id = q.c_id LEFT JOIN #__lms_quiz_t_qtypes as qt ON q.c_type = qt.c_id"
				. "\n WHERE sp.c_stu_quiz_id = '".$stu_quiz_id."' AND sp.c_stu_quiz_id = b.start_id AND sp.c_question_id = b.quest_id"
				. "\n ORDER BY b.ordering, q.c_id"
				;
			} else {
				$query = "SELECT sp.c_id, sp.c_score, q.c_type, q.c_point, q.c_question, qt.c_qtype, q.c_pool, q.c_pool_gqp"
				. "\n FROM #__lms_quiz_r_student_question as sp LEFT JOIN #__lms_quiz_t_question as q ON sp.c_question_id = q.c_id LEFT JOIN #__lms_quiz_t_qtypes as qt ON q.c_type = qt.c_id"
				. "\n WHERE sp.c_stu_quiz_id = '".$stu_quiz_id."'"
				. "\n ORDER BY q.ordering, q.c_id"
				;
				
			}
			$db->SetQuery( $query );
			$rows = $db->LoadObjectList();
			
			for($i=0;$i<count($rows);$i++) {
				$str = '_JLMS_QUIZ_QTYPE_'.$rows[$i]->c_type;
				if (defined($str)) {
					$rows[$i]->qtype_full = constant($str);
				}
			}
			
			
			/*-------------------QP--------------------*/
			$q_from_pool = array();
			foreach ($rows as $row) {
				if ($row->c_type == 20) {
					$q_from_pool[] = $row->c_pool;
				}
			}
			if (count($q_from_pool)) {
				$qp_ids =implode(',',$q_from_pool);
				$query = "SELECT a.*, b.c_qtype as qtype_full FROM #__lms_quiz_t_question as a LEFT JOIN #__lms_quiz_t_qtypes b ON b.c_id = a.c_type"
				. "\n WHERE a.course_id = '".$course_id."'";
				$db->setQuery( $query );
				$rows2 = $db->loadObjectList();
				
				for($i=0;$i<count($rows2);$i++) {
					$str = '_JLMS_QUIZ_QTYPE_'.$rows2[$i]->c_type;
					if (defined($str)) {
						$rows2[$i]->qtype_full = constant($str);
					}
				}
				
				for ($i=0, $n=count( $rows ); $i < $n; $i++) {
					if ($rows[$i]->c_type == 20) {
						for ($j=0, $m=count( $rows2 ); $j < $m; $j++) {
							if ($rows[$i]->c_pool == $rows2[$j]->c_id) {
								$rows[$i]->c_type = $rows2[$j]->c_type;
								$rows[$i]->c_question = $rows2[$j]->c_question;
								$rows[$i]->c_point = $rows2[$j]->c_point;
								$rows[$i]->c_qtype = _JLMS_QUIZ_QUEST_POOL_SHORT . ' - ' . $rows2[$j]->qtype_full;
								break;
							}
						}
					}
				}
			}
			
			/*----------------------GQP-------------------*/	
			$q_from_pool_gqp = array();
			foreach ($rows as $row) {
				if ($row->c_type == 21) {
					$q_from_pool_gqp[] = $row->c_pool_gqp;
				}
			}
			if (count($q_from_pool_gqp)) {
				$qp_ids_gqp =implode(',',$q_from_pool_gqp);
				$query = "SELECT a.*, b.c_qtype as qtype_full FROM #__lms_quiz_t_question as a LEFT JOIN #__lms_quiz_t_qtypes b ON b.c_id = a.c_type"
				. "\n WHERE a.course_id = 0";
				$db->setQuery( $query );
				$rows2 = $db->loadObjectList();
				
				for($i=0;$i<count($rows2);$i++) {
					$str = '_JLMS_QUIZ_QTYPE_'.$rows2[$i]->c_type;
					if (defined($str)) {
						$rows2[$i]->qtype_full = constant($str);
					}
				}
				
				for ($i=0, $n=count( $rows ); $i < $n; $i++) {
					if ($rows[$i]->c_type == 21) {
						for ($j=0, $m=count( $rows2 ); $j < $m; $j++) {
							if ($rows[$i]->c_pool_gqp == $rows2[$j]->c_id) {
								$rows[$i]->c_type = $rows2[$j]->c_type;
								$rows[$i]->c_question = $rows2[$j]->c_question;
								$rows[$i]->c_point = $rows2[$j]->c_point;
								$rows[$i]->c_qtype = _JLMS_QUIZ_QUEST_POOL_GQP_SHORT . ' - ' . $rows2[$j]->qtype_full;
								break;
							}
						}
					}
				}
			}
			if(count($rows)){
				$questions = $rows;
			}
		}
		return $rows;
	}
	
	function question($course_id, $id){
		global $JLMS_CONFIG;
		$db = & JFactory::getDBO();
		
		$quest_data = array();
		
		if($id){
			$query = "SELECT q.c_type, q.c_id,q.c_image, sq.c_stu_quiz_id, q.c_question, q.c_pool, q.c_pool_gqp FROM #__lms_quiz_t_question as q, #__lms_quiz_r_student_question as sq"
			. "\n WHERE q.c_id = sq.c_question_id and sq.c_id = '".$id."'"
			;
			$db->SetQuery( $query );
			$q_data = $db->LoadObjectList();
			
			$lists = array();
			if (count($q_data)) {
				$q_type = $q_data[0]->c_type;
				$q_id = $q_data[0]->c_id;
				$qid = $q_data[0]->c_stu_quiz_id;
				if ($q_type == 20) {
					$query = "SELECT * FROM #__lms_quiz_t_question WHERE c_id = ".$q_data[0]->c_pool;
					$db->SetQuery($query);
					$rtrtr = $db->LoadObject();
					if (is_object($rtrtr)) {
						$q_id = $rtrtr->c_id;
						$q_type = $rtrtr->c_type;
						$q_data[0]->c_question = $rtrtr->c_question;
					}
				}
				
				if ($q_type == 21) {
					$query = "SELECT * FROM #__lms_quiz_t_question WHERE c_id = ".$q_data[0]->c_pool_gqp;
					$db->SetQuery($query);
					$rtrtr = $db->LoadObject();
					if (is_object($rtrtr)) {
						$q_id = $rtrtr->c_id;
						$q_type = $rtrtr->c_type;
						$q_data[0]->c_question = $rtrtr->c_question;
					}
				}
		
				$query = "SELECT u.username, u.name, u.email, u.id FROM #__users as u, #__lms_quiz_r_student_quiz as q WHERE q.c_id = '".$qid."'"
				. "\n and q.c_student_id = u.id";
				$db->SetQuery( $query );
				$user_info = $db->LoadObjectList();
				$group = '';
				$user_id = isset($user_info[0]->id)?$user_info[0]->id:0;
				if ($user_id) {
					$query = "SELECT a.ug_name FROM #__lms_usergroups as a, #__lms_users_in_groups as b"
					. "\n WHERE b.course_id = '".$course_id."' AND b.user_id = '".$user_id."' AND b.group_id = a.id";
					$db->SetQuery( $query );
					$group = $db->LoadResult();
				}
				if(count($user_info)){ 
					$lists['user'] = $user_info[0];
				} else {
					$lists['user']->username = "Anonymous"; $lists['user']->name = " - "; $lists['user']->email = " - "; 
				}
				$lists['user']->usergroup = $group;
				switch ($q_type) {
					case 1:
					case 12:
					case 2:
					case 13:
					case 3:
						$query = "SELECT c.*"
						.(in_array($q_type, array(12,13)) ? "\n, img.imgs_name as c_choice" : '')
						. "\n, sc.c_id as sc_id"
						. "\n FROM #__lms_quiz_t_choice as c"
						. "\n LEFT JOIN #__lms_quiz_r_student_choice as sc"
						. "\n ON c.c_id = sc.c_choice_id"
						. "\n AND sc.c_sq_id = '".$id."'"
						.(in_array($q_type, array(12,13)) ? "\n LEFT JOIN #__lms_quiz_images as img" : '')
						.(in_array($q_type, array(12,13)) ? "\n ON c.c_choice = img.imgs_id" : '')
						. "\n WHERE c.c_question_id = '".$q_id."'"
						. "\n ORDER BY c.ordering, c.c_id"
						;
						$db->SetQuery( $query );
						$answer = $db->LoadObjectList();
						$lists['id'] = $id;
						$lists['qid'] = $qid;
						$lists['question'] = $q_data[0]->c_question;
					break;
					case 4:
					case 5:
					case 11:
						$query = "SELECT"
						.($q_type == 11 ? "\n *, img_left.imgs_name as c_left_text, img_right.imgs_name as c_right_text, img.imgs_name as c_sel_text" : "\n *")
						. "\n FROM #__lms_quiz_t_matching as m LEFT JOIN #__lms_quiz_r_student_matching as sm"
						. "\n ON m.c_id = sm.c_matching_id and sm.c_sq_id = '".$id."'"
						.($q_type == 11 ? "\n LEFT JOIN #__lms_quiz_images as img_left" : '')
						.($q_type == 11 ? "\n ON m.c_left_text = img_left.imgs_id" : '')
						.($q_type == 11 ? "\n LEFT JOIN #__lms_quiz_images as img_right" : '')
						.($q_type == 11 ? "\n ON m.c_right_text = img_right.imgs_id" : '')
						.($q_type == 11 ? "\n LEFT JOIN #__lms_quiz_images as img" : '')
						.($q_type == 11 ? "\n ON sm.c_sel_text = img.imgs_id" : '')
						. "\n WHERE m.c_question_id = '".$q_id."'"
						. "\n ORDER BY m.ordering, m.c_id"
						;
						$db->SetQuery( $query );
						$answer = $db->LoadObjectList();
						$lists['id'] = $id;
						$lists['qid'] = $qid;
						$lists['question'] = $q_data[0]->c_question;
					break;
					case 6:
						$query = "SELECT * FROM #__lms_quiz_r_student_blank WHERE c_sq_id = '".$id."'";
						$db->SetQuery( $query );
						$answer = $db->LoadObjectList();
						if (!count($answer)) { $answer = array(); $answer[0]->c_answer = ''; }
						$lists['id'] = $id;
						$lists['qid'] = $qid;
						$lists['question'] = $q_data[0]->c_question;
					break;
					case 7:
						$query = "SELECT * FROM #__lms_quiz_r_student_hotspot WHERE c_sq_id = '".$id."'";
						$db->SetQuery( $query );
						$answer = $db->LoadObjectList();
						if (!count($answer)) { $answer = array(); $answer[0]->c_select_x = 0; $answer[0]->c_select_y = 0; }
						$lists['id'] = $id;
						$lists['qid'] = $qid;
						$lists['question'] = $q_data[0]->c_question;
						
						$query = "SELECT * FROM #__lms_quiz_t_hotspot WHERE c_question_id = '".$q_id."'";
						$db->SetQuery( $query );
						$hotspot = $db->LoadObjectList();
						if (!count($hotspot)){
							$hotspot = array(); 
							$hotspot[0]->c_start_x = 0; 
							$hotspot[0]->c_start_y = 0; 
							$hotspot[0]->c_width = 0; 
							$hotspot[0]->c_height = 0; 
						}
						$lists['image'] = $q_data[0]->c_image;
						$lists['hotspot'] = $hotspot[0];
						
						$q_data[0]->c_right = 0;
						if( 
							$hotspot[0]->c_start_x < $answer[0]->c_select_x && $answer[0]->c_select_x < ($hotspot[0]->c_start_x + $hotspot[0]->c_width)
							&&
							$hotspot[0]->c_start_y < $answer[0]->c_select_y && $answer[0]->c_select_y < ($hotspot[0]->c_start_x + $hotspot[0]->c_height)
						){
							$q_data[0]->c_right = 1;
						}
						
					break;
					case 8:
						$query = "SELECT * FROM #__lms_quiz_r_student_survey WHERE c_sq_id = '".$id."'";
						$db->SetQuery( $query );
						$answer = $db->LoadObjectList();
						if (!count($answer)){
							$answer = array(); 
							$answer[0]->c_answer = ''; 
						}
						$lists['id'] = $id;
						$lists['qid'] = $qid;
						$lists['question'] = $q_data[0]->c_question;
					break;
					case 9:
						$query = "SELECT * FROM #__lms_quiz_r_student_scale WHERE c_sq_id = '".$id."'";
						$db->SetQuery( $query );
						$lists['id'] = $id;
						$lists['qid'] = $qid;
						$lists['question'] = $q_data[0]->c_question;
						$answers = $db->LoadObjectList();
						$answer = array();
							for($p=0;$p<count($answers);$p++)
							{
								$answer[$p][0] = $answers[$p]->q_scale_id;
								$answer[$p][1] = $answers[$p]->scale_id;
							}
						$query = "SELECT * FROM #__lms_quiz_t_scale WHERE c_question_id = '".$q_id."'"
						. "\n ORDER BY ordering";
						$db->SetQuery( $query );
						$scale_data = $db->LoadObjectList();
						for($i=0;$i<count($scale_data);$i++)
						{
							$scale_data[$i]->inchek = '';
							foreach ($answer as $uansw)
							{
								if($uansw[0] == $scale_data[$i]->c_id)
									$scale_data[$i]->inchek = $uansw[1];
							}
						}
						$answer = array();
						$answer[0]->c_id = 0;
					break;
				}
				
				$quest_data['data'] = get_object_vars($q_data[0]);
				$quest_data['answers'] = $answer;
				if(isset($lists['image']) && isset($lists['hotspot'])){
					$quest_data['image'] = $lists['image'];
					$quest_data['hotspot'] = $lists['hotspot'];
				}
				$quest_data['user_info'] = get_object_vars($lists['user']);
			}
			
		}
		return $quest_data;
	}
	
}
?>