<?php
/**
* admin.forums.class.php
* JoomlaLMS Component
*/

//defined( '_VALID_MOS' ) or die( 'Restricted access' );
if (!defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) { die( 'Restricted access' ); }

class JLMS_forum_item extends JLMSDBTable {
	var $id 				= null;
	var $parent_forum		= null;
	var $published			= null;
	var $forum_level 		= null;
	var $user_level 		= null;
	var $moderated			= null;
	var $forum_access 		= null;
	var $forum_permissions	= null;
	var $forum_moderators	= null;
	var $forum_name			= null;
	var $forum_desc			= null;

	function JLMS_forum_item( &$db ) {
		$this->JLMSDBTable( '#__lms_forums', 'id', $db );
	}

	function check() {
		return true;
	}
}
?>