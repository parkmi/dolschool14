<?php
/**
* admin.users.toolbar.php
* (c) JoomaLMS eLearning Software http://www.joomlalms.com/
**/
defined('_JEXEC') or die;

class ALU_toolbar {

	function _EDIT() {
		global $jlms_toolbar_id;
		JToolBarHelper::title( _JOOMLMS_COMP_NAME.': '.($jlms_toolbar_id?_JLMS_USERS_EDIT_USER : _JLMS_USERS_NEW_USER) );
		JToolBarHelper::save('save', _JLMS_SAVE);
		JToolBarHelper::spacer();
		JToolBarHelper::apply('apply', _JLMS_APPLY);
		JToolBarHelper::spacer();
		JToolBarHelper::cancel( 'cancel', _JLMS_CLOSE);
		JToolBarHelper::spacer();
	}

	function _DEFAULT() {
		JToolBarHelper::title( _JLMS_USERS_MANAGEMENT, 'user.png' );
		JToolBarHelper::addNewX('new');
		JToolBarHelper::spacer();
		JToolBarHelper::editList('edit');
		JToolBarHelper::spacer();
		JToolBarHelper::deleteList('', 'delete', _JLMS_DELETE);
		JToolBarHelper::spacer();
	}
}

function ALU_process_toolbar() {
	$page 	= JRequest::getVar('page', 'list');
	switch ($page) {
		case 'edit':
		case 'editA':
		case 'new':
			ALU_toolbar::_EDIT();
		break;
		case 'list':
		default:
			ALU_toolbar::_DEFAULT();
		break;
	}
}
?>