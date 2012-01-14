<?php
/**
* joomla_lms.course_links.php
* (c) JoomaLMS eLearning Software http://www.joomlalms.com/
**/

// no direct access
defined( '_JLMS_EXEC' ) or die( 'Restricted access' );
require_once(_JOOMLMS_FRONT_HOME . "/joomla_lms.course_links.html.php");

	global $JLMS_CONFIG;
	$course_id = $JLMS_CONFIG->get('course_id',0);
	$pathway = array();
	$pathway[] = array('name' => _JLMS_PATHWAY_HOME, 'link' => sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid"), 'is_home' => true);
	$pathway[] = array('name' => $JLMS_CONFIG->get('course_name'), 'link' => sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid&amp;task=details_course&amp;id=$course_id"), 'is_course' => true);
	$pathway[] = array('name' => _JLMS_TOOLBAR_LINKS, 'link' => sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid&amp;task=links&amp;id=$course_id"));
	JLMSAppendPathWay($pathway);

JLMS_ShowHeading();
$id 	= intval( mosGetParam( $_REQUEST, 'id', 0 ) ); 
$task 	= mosGetParam( $_REQUEST, 'task', '' );
switch ($task) {
################################		LINKS		##############################
	case 'links':				JLMS_showLinks( $option );				break;
	case 'pre_create_link':		JLMS_editLink( 0, $option );			break;
	case 'pre_link_edit':		$cid = mosGetParam( $_POST, 'cid', array(0) );
				if (!is_array( $cid )) { $cid = array(0); }
				JLMS_editLink( $cid[0], $option );						break;
	case 'save_link':			JLMS_saveLink( $option );				break;
	case 'link_delete':			JLMS_doDeleteLinks( $option );			break;
	case 'link_orderup':
	case 'link_orderdown':		JLMS_OrderLinks( $option );				break;
	case 'cancel_link':			JLMS_cancelLink( $option );				break;
	case 'change_link':			JLMS_changeLink( $option );				break;
	case 'view_inline_link':	JLMS_viewInlineLink( $id, $option );	break;

}

function JLMS_viewInlineLink( $id, $option ) {
	global $my, $JLMS_DB, $Itemid, $JLMS_CONFIG;
	$JLMS_ACL = & JLMSFactory::getACL();
	$course_id = $JLMS_CONFIG->get('course_id');
	if ($course_id && $JLMS_ACL->CheckPermissions('links', 'view') ) {
		$query = "SELECT a.*"
		. "\n FROM #__lms_links as a "
		. "\n WHERE a.id = $id AND a.course_id = '".$course_id."'"
		. (($JLMS_ACL->CheckPermissions('links', 'view_all')) ? '' : "\n AND a.published = 1")
		;
		$JLMS_DB->SetQuery( $query );
		$row = $JLMS_DB->LoadObject();
		if (is_object($row)) {
			$_JLMS_PLUGINS = & JLMSFactory::getPlugins();
			$_JLMS_PLUGINS->loadBotGroup('content');
			$plugin_result_array = $_JLMS_PLUGINS->trigger('onContentProcess', array(&$row->link_href));
			$plugin_result_array = $_JLMS_PLUGINS->trigger('onContentProcess', array(&$row->link_name));
			if ($row->params) {
				$params = new JLMSParameters($row->params);
			} else {
				$params = new JLMSParameters('display_width=720'."\n".'display_height=540');
			}
			JLMS_course_links_html::showInlineLink( $id, $option, $row, $params );
		} else {
			JLMSRedirect( sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=links&id=$course_id") );
		}
	} else {
		JLMSRedirect( sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=links&id=$course_id") );
	}
}

function JLMS_editLink( $id, $option ) {
	global $my, $JLMS_DB, $Itemid, $JLMS_CONFIG;
	$JLMS_ACL = & JLMSFactory::getACL();
	$course_id = $JLMS_CONFIG->get('course_id');
	if ( $course_id && ($JLMS_ACL->CheckPermissions('links', 'manage')) ) {// && ( ($id && (JLMS_GetLinkCourse($id) == $course_id)) || !$id ) ) {
		$AND_ST = "";
		if( false !== ( $enroll_period = JLMS_getEnrolPeriod( $my->id, $course_id )) ) 
		{
			$AND_ST = " AND IF(is_time_related, (show_period < '".$enroll_period."' ), 1) ";	
		}
		
	
		$row = new mos_Joomla_LMS_Link( $JLMS_DB );
		$row->addCond( $AND_ST );
		$row->load( $id );
		if ($id) {
			if ($row->course_id != $course_id) {
				JLMSRedirect( sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=links&id=$course_id") );
			}
			if ($JLMS_ACL->CheckPermissions('links', 'only_own_items') && $row->owner_id != $my->id) {
				JLMSRedirect( sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=links&id=$course_id") );
			} elseif ($JLMS_ACL->CheckPermissions('links', 'only_own_role') && $JLMS_ACL->GetRole() != $JLMS_ACL->UserSystemRole($JLMS_DB, $row->owner_id)) {
				JLMSRedirect( sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=links&id=$course_id") );
			}
if ($row->params) {
				$params = new JLMSParameters($row->params);
			} else {
				$params = new JLMSParameters('display_width=0'."\n".'display_height=0');
			}
			//$row->checkout($my->id);
		} else {
			$row->published = 0;
			$params = new JLMSParameters('display_width=0'."\n".'display_height=0');
		}
		$lists = array();
		$lists['published'] = mosHTML::yesnoRadioList( 'published', 'class="inputbox" ', $row->published);
				
		JLMS_course_links_html::showEditLink( $row, $lists, $option, $course_id, $params );
	} else {
		JLMSRedirect( sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=links&id=$course_id") );
	}
}
function JLMS_cancelLink( $option ) {
	global $Itemid, $JLMS_CONFIG;
	$course_id = $JLMS_CONFIG->get('course_id');
	JLMSRedirect(sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=links&id=$course_id"));
}
function JLMS_saveLink( $option ) {
	global $my, $JLMS_DB, $Itemid, $JLMS_CONFIG;
	$JLMS_ACL = & JLMSFactory::getACL();
	$course_id = $JLMS_CONFIG->get('course_id');

	$id = intval(mosGetParam($_REQUEST, 'id', 0));
	if ( $JLMS_ACL->CheckPermissions('links', 'manage') && ( ($id && (JLMS_GetLinkCourse($id) == $course_id)) || !$id ) ) {
		$row = new mos_Joomla_LMS_Link( $JLMS_DB );
		if (!$row->bind( $_POST )) {
			echo "<script> alert('".addslashes($row->getError())."'); window.history.go(-1); </script>\n";
			exit();
		}
$params = '';
		$params_p= mosGetParam( $_POST, 'params', '' );
		if (is_array( $params_p )) {
			$txt = array();
			foreach ( $params_p as $k=>$v) {
				$txt[] = "$k=$v";
			}
			$params = implode( "\n", $txt );
		}
		$row->params = $params;
		//$row->link_name = strval(mosGetParam($_POST, 'link_name', ''));
		$row->link_href = strval(mosGetParam($_POST, 'link_href', ''));
				
		$days = intval(mosGetParam($_POST, 'days', ''));
		$hours = intval(mosGetParam($_POST, 'hours', ''));
		$mins = intval(mosGetParam($_POST, 'mins', ''));
		
		if( $row->is_time_related ) {
			$row->show_period = JLMS_HTML::_('showperiod.getminsvalue', $days, $hours, $mins );
		}
						
		if (!$id) {
			$row->owner_id = $my->id;
		} else {
			unset($row->owner_id);
			if ($JLMS_ACL->CheckPermissions('links', 'only_own_items') && JLMS_GetLinkOwner($id) != $my->id) {
				JLMSRedirect( sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=links&id=$course_id") );
			} elseif ($JLMS_ACL->CheckPermissions('links', 'only_own_role') && $JLMS_ACL->GetRole() != $JLMS_ACL->UserSystemRole($JLMS_DB, JLMS_GetLinkOwner($id))) {
				JLMSRedirect( sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=links&id=$course_id") );
			}
		}

		$row->link_name = strval(JLMS_getParam_LowFilter($_POST, 'link_name', ''));
		$row->link_name = JLMS_Process_ContentNames($row->link_name);

		// 26.02.2007 (Media content integration)
		$row->link_description = strval(JLMS_getParam_LowFilter($_POST, 'link_description', ''));
		$row->link_description = JLMS_ProcessText_LowFilter($row->link_description);
		//$iFilter = new JLMS_InputFilter(null,null,1,1);
		//$row->link_description = $iFilter->process( $row->link_description );
	
		$row->link_type = intval(mosGetParam($_REQUEST, 'link_type', 0));
		if (!$JLMS_ACL->CheckPermissions('links', 'publish')) {
			$row->published = 0;
		}
		if (!$JLMS_ACL->CheckPermissions('links', 'order')) {
			$row->ordering = 0;
		}
		
		if (!$row->check()) {
			echo "<script> alert('".addslashes($row->getError())."'); window.history.go(-1); </script>\n";
			exit();
		}
		if (!$row->store()) {
			echo "<script> alert('".addslashes($row->getError())."'); window.history.go(-1); </script>\n";
			exit();
		}
	}
	JLMSRedirect( sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=links&id=$course_id") );
}
//to do: proverku na teacher, student,. ...
//+ otobragat' daty sozdaniya linka i ego publikacii..
function JLMS_showLinks( $option) {
	$JLMS_CONFIG = & JLMSFactory::getCOnfig();
	$Itemid = $JLMS_CONFIG->get('Itemid');
	$db = & JFactory::getDbo();
	$user = JLMSFactory::getUser();
	$my_id = $user->get('id');

	$JLMS_ACL = & JLMSFactory::getACL();
	$id = $JLMS_CONFIG->get('course_id');
	
	$AND_ST = "";
	if( false !== ( $enroll_period = JLMS_getEnrolPeriod( $my_id, $id )) ) 
	{
		$AND_ST = " AND IF(a.is_time_related, (a.show_period < '".$enroll_period."' ), 1) ";
	}

	if ($id && $JLMS_ACL->CheckPermissions('links', 'view') ) {
		$query = "SELECT a.*, b.name as author_name"
		. "\n FROM #__lms_links as a LEFT JOIN #__users as b ON a.owner_id = b.id"
		. "\n WHERE a.course_id = '".$id."'".$AND_ST
		. (($JLMS_ACL->CheckPermissions('links', 'view_all')) ? '' : "\n AND a.published = 1")
		. "\n ORDER BY a.ordering, a.link_name";
		$db->SetQuery( $query );
		$rows = $db->LoadObjectList();
	
		$lms_titles_cache = & JLMSFactory::getTitles();
		$lms_titles_cache->setArray('links', $rows, 'id', 'link_name');
	
		JLMS_course_links_html::showCourseLinks( $id, $option, $rows );
	} elseif ($id) {
		JLMSRedirect( sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=details_course&id=$id") );
	} else {
		JLMSRedirect( sefRelToAbs("index.php?option=$option&Itemid=$Itemid") );
	}
}
// (WARNING) "owner_id = '".$my->id."'";" - xm xm xm
function JLMS_doDeleteLinks( $option ) {
	global $my, $JLMS_DB, $Itemid, $JLMS_CONFIG;
	$course_id = $JLMS_CONFIG->get('course_id');
	$JLMS_ACL = & JLMSFactory::getACL();
	if ( $course_id && $JLMS_ACL->CheckPermissions('links', 'manage') ) {
		$cid = mosGetParam( $_POST, 'cid', array(0) );
		if (!is_array( $cid )) {
			$cid = array(0);
		}
		$i = 0;
		while ($i < count($cid)) {
			$cid[$i] = intval($cid[$i]);
			$i ++;
		}
		$cids = implode(',',$cid);
		$query = "DELETE FROM #__lms_links WHERE id IN ($cids) AND course_id = '".$course_id."'"
		. ($JLMS_ACL->CheckPermissions('links', 'only_own_items') ? (" AND owner_id = '".$my->id."'"):'');
		$JLMS_DB->SetQuery( $query );
		$JLMS_DB->query();
		
		//topics
		$query = "DELETE FROM #__lms_topic_items WHERE item_id IN ($cids) AND item_type = 3";
		$JLMS_DB->SetQuery( $query );
		$JLMS_DB->query();
		//-------------

		
	}
	JLMSRedirect(sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=links&id=$course_id"));
}
//to do: dobavit' proverki na teachera. (25.10. - OK)
function JLMS_OrderLinks( $option ) {
	global $JLMS_DB, $my, $task, $Itemid, $JLMS_CONFIG;
	$course_id = $JLMS_CONFIG->get('course_id');
	$JLMS_ACL = & JLMSFactory::getACL();
	if ( $course_id && $JLMS_ACL->CheckPermissions('links', 'order') ) {
		$order_id = intval(mosGetParam($_REQUEST, 'row_id', 0));
		$query = "SELECT id FROM #__lms_links WHERE course_id = '".$course_id."' ORDER BY ordering";
		$JLMS_DB->SetQuery( $query );
		$id_array = $JLMS_DB->LoadResultArray();
		if (count($id_array)) {
			$i = 0;$j = 0;
			while ($i < count($id_array)) {
				if ($id_array[$i] == $order_id) { $j = $i;}
				$i ++;
			}
			$do_update = true;
			if (($task == 'link_orderup') && ($j) ) {
				$tmp = $id_array[$j-1];
				$id_array[$j-1] = $id_array[$j];
				$id_array[$j] = $tmp;
			} elseif (($task == 'link_orderdown') && ($j < (count($id_array)-1)) ) {
				$tmp = $id_array[$j+1];
				$id_array[$j+1] = $id_array[$j];
				$id_array[$j] = $tmp;
			}
			$i = 0;
			foreach ($id_array as $link_id) {
				$query = "UPDATE #__lms_links SET ordering = '".$i."' WHERE id = '".$link_id."' AND course_id = '".$course_id."'";
				//. ($JLMS_ACL->CheckPermissions('links', 'only_own_items') ? (" AND owner_id = '".$my->id."'"):'');
				$JLMS_DB->SetQuery( $query );
				$JLMS_DB->query();
				$i ++;
			}
		}
	}
	JLMSRedirect("index.php?option=$option&Itemid=$Itemid&task=links&id=$course_id");
}
function JLMS_changeLink( $option ) {
	global $JLMS_DB, $my, $Itemid, $JLMS_CONFIG;
	$course_id = $JLMS_CONFIG->get('course_id');
	$JLMS_ACL = & JLMSFactory::getACL();
	if ( $course_id && $JLMS_ACL->CheckPermissions('links', 'publish') ) {
		$state = intval(mosGetParam($_REQUEST, 'state', 0));
		if ($state != 1) { $state = 0; }
		$cid = mosGetParam( $_REQUEST, 'cid', array(0) );
		$cid2 = intval(mosGetParam( $_REQUEST, 'cid2', 0 ));
		if ($cid2) {
			$cid = array();
			$cid[] = $cid2;
		}
		if (!is_array( $cid )) {
			$cid = array(0);
		} 
		if (!is_array( $cid ) || count( $cid ) < 1) {
			$action = 1 ? 'Publish' : 'Unpublish';
			echo "<script> alert('Select an item to $action'); window.history.go(-1);</script>\n";
			exit();
		}
		$cids = implode( ',', $cid );
		$query = "UPDATE #__lms_links"
		. "\n SET published = $state"
		. "\n WHERE id IN ( $cids ) AND course_id = $course_id"
		. ($JLMS_ACL->CheckPermissions('links', 'only_own_items') ? (" AND owner_id = '".$my->id."'"):'');
		;
		$JLMS_DB->setQuery( $query );
		if (!$JLMS_DB->query()) {
			echo "<script> alert('".$JLMS_DB->getErrorMsg()."'); window.history.go(-1); </script>\n";
			exit();
		}
	}
	JLMSRedirect( sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=links&id=$course_id") );
}
function JLMS_GetLinkOwner($file_id) {
	global $JLMS_DB;
	$query = "SELECT owner_id FROM #__lms_links WHERE id = '".$file_id."'";
	$JLMS_DB->SetQuery( $query );
	return $JLMS_DB->LoadResult();
}
function JLMS_GetLinkCourse($link_id) {
	global $JLMS_DB;
	$query = "SELECT course_id FROM #__lms_links WHERE id = '".$link_id."'";
	$JLMS_DB->SetQuery( $query );
	return $JLMS_DB->LoadResult();
}
?>