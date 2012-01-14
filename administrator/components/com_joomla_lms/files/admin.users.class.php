<?php
/**
* admin.forums.class.php
* Joomla LMS Component
*/

//defined( '_VALID_MOS' ) or die( 'Restricted access' );
if (!defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) { die( 'Restricted access' ); }

class JLMS_user_system_level extends JLMSDBTable {
	var $id 				= null;
	var $user_id			= null;
	var $lms_usertype_id	= null;
	var $lms_block			= 0;
	var $_error				= '';

	function JLMS_user_system_level( &$db ) {
		$this->JLMSDBTable( '#__lms_users', 'id', $db );
	}

	function check() {
		if (!$this->user_id) {
			$this->_error = _JLMS_USERS_MSG_USR_NOT_SELECT;
			return false;
		}
		if (!$this->lms_usertype_id) {
			$this->_error = _JLMS_USERS_MSG_USR_ROLE_NOT_SELECT;
			return false;
		}
		return true;
	}
	function getError() {
		return $this->_error;
	}
}
?>