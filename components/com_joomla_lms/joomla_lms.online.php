<?php
/**
* joomla_lms.course_links.php
* Joomla LMS Component
* * * ElearningForce DK
**/

// no direct access
defined( '_JLMS_EXEC' ) or die( 'Restricted access' );
require_once(_JOOMLMS_FRONT_HOME . "/joomla_lms.online.html.php");
	global $JLMS_CONFIG;
	$course_id = $JLMS_CONFIG->get('course_id',0);
	$pathway = array();
	$pathway[] = array('name' => _JLMS_PATHWAY_HOME, 'link' => sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid"), 'is_home' => true);
	$pathway[] = array('name' => _JLMS_TOOLBAR_ONLINE, 'link' => sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid&amp;task=online_users&amp;course_id=$course_id"));
	JLMSAppendPathWay($pathway);

JLMS_ShowHeading();
$task 	= mosGetParam( $_REQUEST, 'task', '' );
switch ($task) {
################################		LINKS		##############################
	case 'online_users':	JLMS_showOU( $course_id, $option );		break;
}
function JLMS_showOU( $id, $option) {
	global $my;
	$usertype = JLMS_GetUserType($my->id, $id);
	if ($id && ($usertype == 1)) {
		$rows = JLMS_getOnlineUsers_TR( $id );
		JLMS_course_ou_html::showOU( $id, $option, $rows );
	}
}
?>