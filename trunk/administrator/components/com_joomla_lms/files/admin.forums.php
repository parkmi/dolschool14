<?php
/**
* admin.forums.php
* (c) JoomaLMS eLearning Software http://www.joomlalms.com/
**/

// no direct access
if (!defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) { die( 'Restricted access' ); }

require_once(_JOOMLMS_ADMIN_HOME.'/files/admin.forums.html.php');
require_once(_JOOMLMS_ADMIN_HOME.'/files/admin.forums.class.php');

$task 	= mosGetParam( $_REQUEST, 'task', 'lms_forums' );
$page 	= mosGetParam( $_REQUEST, 'page', 'list' );

$id 	= intval( mosGetParam( $_REQUEST, 'id', 0 ) );
$cid 	= mosGetParam( $_POST, 'cid', mosGetParam( $_GET, 'cid', array(0) ) );

switch ($page) {
	case 'edit':
		ALF_editItem( intval( $cid[0] ), $option);
	break;
	case 'editA':
		ALF_editItem( $id, $option);
	break;
	case 'new':
		ALF_editItem( 0, $option);
	break;
	case 'save':
	case 'apply':
		ALF_saveItem( $id, $option, $page );
	break;
	case 'unpublish':
		ALF_changestatus( $cid, 0, $option );
	break;
	case 'publish':
		ALF_changestatus( $cid, 1, $option );
	break;
	case 'cancel':
		mosRedirect( "index.php?option=$option&task=lms_forums" );
	break;
	case 'remove':
		ALF_removeItem(intval( $cid[0] ), $option);
	break;
	case 'list':
	default:
		ALF_showList( $option );
	break;
}

function ALF_editItem( $id, $option ) {
	global $JLMS_CONFIG;
	$db = & JFactory::getDbo();

	$row = new JLMS_forum_item( $db );
	$row->load( $id );
	if (isset($row->id) && $row->id || !$id) {
		if (!$row->parent_forum) { $row->parent_forum = 0; }
		if (!$row->forum_access) { $row->forum_access = 0; }
		if (!$row->published) { $row->published = 0; }
		if (!$row->forum_level) { $row->forum_level = 0; }
		if (!$row->user_level) { $row->user_level = 0; }
		if (!$row->moderated) { $row->moderated = 0; }

		$parent_forums = & ALF_get_forums_list('id as value, forum_name as text, parent_forum');
		for ($j = 1, $n = count($parent_forums); $j < $n; $j ++) {
			if ($parent_forums[$j]->parent_forum) {
				$parent_forums[$j]->text = '&nbsp;&nbsp;&nbsp;-&nbsp;'.$parent_forums[$j]->text;
			}
		}
		array_unshift($parent_forums, mosHTML::makeOption( '0', '&nbsp;') );
		$lists['parent_forums'] = mosHTML::selectList( $parent_forums, 'parent_forum', 'class="text_area" style="width:266px" ', 'value', 'text', $row->parent_forum );
		$lists['custom_access'] = mosHTML::yesnoRadioList( 'forum_access', 'class="text_area" ', $row->forum_access);
		$lists['published'] = mosHTML::yesnoRadioList( 'published', 'class="text_area" ', $row->published);
		$lists['moderated'] = mosHTML::yesnoRadioList( 'moderated', 'class="text_area" ', $row->moderated);

		$query = "SELECT id as value, lms_usertype as text, roletype_id, IF(roletype_id = 4, 1, IF(roletype_id = 2, 2, 3)) as ordering FROM #__lms_usertypes WHERE roletype_id IN (2,4,5) ORDER BY ordering, lms_usertype";
		$db->SetQuery($query);
		$roles = $db->LoadObjectList('value');
		//TODO: check if this loadobjectlist('value') work in Joomla15 and Joomla16

		$forum_types = array();
		$forum_types[] = mosHTML::makeOption( '0', 'Regular board');
		$forum_types[] = mosHTML::makeOption( '1', 'LearningPath specific board ');
		$forum_types[] = mosHTML::makeOption( '2', 'Usergroup (course) specific board');
		$forum_types[] = mosHTML::makeOption( '3', 'Usergroup (system) specific board');

		$forum_type = 0;
		if ($row->forum_level == 1) {
			$forum_type = 1;
		} elseif ($row->user_level == 1) {
			$forum_type = 2;
		} elseif ($row->user_level == 2) {
			$forum_type = 3;
		}
		$lists['forum_type'] = mosHTML::selectList( $forum_types, 'forum_type', 'class="text_area" style="width:266px" ', 'value', 'text', $forum_type );

		ALF_html::editItem( $row, $lists, $roles, $option );
	} else {
		mosRedirect( "index.php?option=$option&task=lms_forums" );
	}
}

function ALF_saveItem( $id, $option, $page ) {
	$db = & JFactory::getDbo();
	$row = new JLMS_forum_item( $db );
	if (!$row->bind( $_POST )) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}
	$msg = '';
	$forum_type = intval( mosGetParam( $_REQUEST, 'forum_type', 0 ) );
	$forum_moderators_str = strval( mosGetParam( $_REQUEST, 'forum_moderators', '' ) );
	$moderators_ids_pre = array();
	if ($forum_moderators_str) {
		$forum_moderators = explode(',',$forum_moderators_str);
		$f_mods = '';
		foreach ($forum_moderators as $fm) {
			$fm = intval($fm);
			if ($fm && !in_array($fm, $moderators_ids_pre)) {
				$moderators_ids_pre[] = $fm;
			}
		}
		if (!empty($moderators_ids_pre)) {
			$moderators_ids_str = implode(',',$moderators_ids_pre);
			$query = "SELECT distinct id FROM #__users WHERE id IN ($moderators_ids_str)";
			$db->SetQuery($query);
			$mds = $db->LoadResultArray();
			if (!empty($mds)) {
				$f_mods = implode(',',$mds);
			}
		}
		$row->forum_moderators = $f_mods;
	}

	if ($forum_type == 1) {
		$row->forum_level = 1;
		$row->user_level = 0;
	} elseif ($forum_type == 2) {
		$row->forum_level = 0;
		$row->user_level = 1;
	} elseif ($forum_type == 3) {
		$row->forum_level = 0;
		$row->user_level = 2;
	} else {
		$row->forum_level = 0;
		$row->user_level = 0;
	}
	if ($row->forum_access) {
		$forum_permissions = josGetArrayInts( 'forum_permissions', $_REQUEST );
		if (empty($forum_permissions)) {
			$row->forum_permissions = '';
		} else {
			$row->forum_permissions = implode(',',$forum_permissions);
		}
	} else {
		$row->forum_permissions = '';
	}

	if ($row->parent_forum) {
		$query = "SELECT parent_forum, user_level, forum_level FROM #__lms_forums WHERE id = $row->parent_forum";
		$db->SetQuery($query);
		$parent = $db->LoadObject();
		if (is_object($parent)) {
			if ($parent->parent_forum) {
				$row->parent_forum = 0;
			} elseif ($parent->forum_level && $row->forum_level != $parent->forum_level) {
				$row->parent_forum = 0;
			} elseif ($parent->user_level && $row->user_level != $parent->user_level) {
				$row->parent_forum = 0;
			}
		} else {
			$row->parent_forum = 0;
		}
	}

	$forum_name_post = isset($_REQUEST['forum_name'])?strval($_REQUEST['forum_name']):'';
	$forum_name_post = (get_magic_quotes_gpc()) ? stripslashes( $forum_name_post ) : $forum_name_post;
	$row->forum_name = $forum_name_post;
	$forum_desc_post = isset($_REQUEST['forum_desc'])?strval($_REQUEST['forum_desc']):'';
	$forum_desc_post = (get_magic_quotes_gpc()) ? stripslashes( $forum_desc_post ) : $forum_desc_post;
	$row->forum_desc = $forum_desc_post;

	if ($row->id) {
		$old_id = $row->id;
		$old_row = new JLMS_forum_item( $db );
		$old_row->load( $old_id );
		if ($old_row->forum_level != $row->forum_level || $old_row->user_level != $row->user_level) {
			$row->forum_level = $old_row->forum_level;
			$row->user_level = $old_row->user_level;
			$msg = "You cannot change type of the forum board.<br />";
		}
		if ($old_row->forum_name != $row->forum_name || $old_row->forum_desc != $row->forum_desc || $old_row->forum_moderators != $row->forum_moderators || $old_row->moderated != $row->moderated) {
			$query = "UPDATE #__lms_forum_details SET need_update = 1 WHERE board_type = ".intval($row->id);
			$db->SetQuery($query);
			$db->query();
		}
		//TODO:
		// 1) implement `old_moderators` functionality ?????  
	}
	if (!$row->check()) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}
	if (!$row->store()) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}
	if ($page == 'apply') {
		mosRedirect( "index.php?option=com_joomla_lms&task=lms_forums&page=editA&id=$id", $msg );
	} else {
		mosRedirect( "index.php?option=com_joomla_lms&task=lms_forums", $msg);
	}
}

function ALF_showList( $option ) {
	$db = & JFactory::getDbo();
	$all_forums = & ALF_get_forums_list('*');
	$query = "SELECT id, lms_usertype as  role_name FROM #__lms_usertypes ORDER BY lms_usertype";
	$db->SetQuery($query);
	$roles = $db->LoadObjectList('id');
	ALF_html::showList( $all_forums, $option, $roles );
}
function ALF_changestatus( $cid=null, $state=0, $option ) {
	$db = & JFactory::getDbo();
	if (!is_array( $cid ) || count( $cid ) < 1) {
		mosRedirect( "index.php?option=$option&task=lms_forums" );
	}
	$state = intval( $state );
	if ($state) { $state = 1; } else { $state = 0; }
	$cids = implode( ',', $cid );

	$query = "UPDATE #__lms_forums"
	. "\n SET published = " . intval( $state )
	. "\n WHERE id IN ( $cids )"
	;
	$db->setQuery( $query );
	if (!$db->query()) {
		echo "<script> alert('".$db->getErrorMsg()."'); window.history.go(-1); </script>\n";
		exit();
	}
	if (!$state) {
		$query = "UPDATE #__lms_forums"
		. "\n SET published = " . intval( $state )
		. "\n WHERE parent_forum IN ( $cids )"
		;
		$db->setQuery( $query );
		if (!$db->query()) {
			echo "<script> alert('".$db->getErrorMsg()."'); window.history.go(-1); </script>\n";
			exit();
		}
	} elseif ($state) {
		$query = "SELECT parent_forum FROM #__lms_forums WHERE id IN ( $cids ) AND parent_forum > 0";
		$db->setQuery( $query );
		$parents = $db->LoadResultArray();
		if (count($parents)) {
			$pcids = implode( ',', $parents );
			$query = "UPDATE #__lms_forums"
			. "\n SET published = " . intval( $state )
			. "\n WHERE id IN ( $pcids )"
			;
			$db->setQuery( $query );
			if (!$db->query()) {
				echo "<script> alert('".$db->getErrorMsg()."'); window.history.go(-1); </script>\n";
				exit();
			}
		}
	}
	mosRedirect( "index.php?option=$option&task=lms_forums" );
}
function & ALF_get_forums_list( $columns = '*') {
	$db = & JFactory::getDbo();
	$query = "SELECT ".$columns.", parent_forum as pf_internal_use, id as id_internal_use FROM #__lms_forums ORDER BY parent_forum ASC, id";
	$db->SetQuery($query);
	$rows = $db->loadObjectList();
	$all_forums = array();
	foreach ($rows as $row) {
		if ($row->pf_internal_use) {
			// search through all forums and insert into necessary place
			$found_position = false;
			for ($j = 0, $n = count($all_forums); $j < $n; $j ++) {
				if ($all_forums[$j]->id_internal_use == $row->pf_internal_use) {
					$found_position = $j; break;
				}
			}
			if ($found_position !== false) {
				$row1 = clone($row);
				array_push($all_forums,$row1);
				for ($j = (count($all_forums) -1); $j > ($found_position + 1); $j --) {
					$tmp_row = $all_forums[$j];
					$all_forums[$j] = $all_forums[$j-1];
					$all_forums[$j-1] = $tmp_row;
				}
			}
		} else {
			$all_forums[] = clone($row);
		}
	}
	return $all_forums;
}

function ALF_removeItem($id, $option) {
	$db = & JFactory::getDbo();
	$msg = '';
	if ($id) {
		$query = "SELECT count(*) FROM #__lms_forums WHERE parent_forum = $id";
		$db->SetQuery($query);
		$num_childs = $db->LoadResult();
		if ($num_childs) {
			$msg = "Please remove all child boards first";
		} else {
			$query = "DELETE FROM #__lms_forums WHERE id = $id";
			$db->SetQuery($query);
			$db->query();
			$query = "SELECT id FROM #__lms_forum_details WHERE board_type = $id";
			$db->SetQuery($query);
			$forum_det_ids = $db->LoadResultArray();
			if (count($forum_det_ids)) {
				require_once(_JOOMLMS_FRONT_HOME."/includes/lms_del_operations.php");
				JLMS_DelOp_deleteForums($db, $forum_det_ids);
			}
		}
	}
	mosRedirect( "index.php?option=$option&task=lms_forums", $msg );
}
?>