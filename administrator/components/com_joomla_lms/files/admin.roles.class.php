<?php
/**
* admin.forums.class.php
* Joomla LMS Component
*/

//defined( '_VALID_MOS' ) or die( 'Restricted access' );
if (!defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) { die( 'Restricted access' ); }

class JLMS_role_item extends JLMSDBTable {
	var $id 				= null;
	var $roletype_id		= null;
	var $lms_usertype		= null;
	var $default_role		= null;

	function JLMS_role_item( &$db ) {
		$this->JLMSDBTable( '#__lms_usertypes', 'id', $db );
	}

	function check() {
		if ($this->id || (!$this->id && ($this->roletype_id == 4 || $this->roletype_id == 2 || $this->roletype_id == 5 || $this->roletype_id == 1 || $this->roletype_id == 3))) {
			return true;
		} else {
			$this->_error = _JLMS_ROLES_MSG_CR_ONLY_ROLES;
		}
		return false;
	}
}

class JLMS_role_permissions extends JLMSDBTable {
	var $id 				= null;
	var $role_id			= null;
	var $p_category			= null;
	var $p_permission		= null;
	var $p_value			= null;

	function JLMS_role_permissions( &$db ) {
		$this->JLMSDBTable( '#__lms_user_permissions', 'id', $db );
	}

	function check() {

		return true;
	}
}
