<?php
/**
* admin.group_managers.toolbar.php
* JoomlaLMS Component
*/

//defined( '_VALID_MOS' ) or die( 'Restricted access' );
if (!defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) { die( 'Restricted access' ); }

//processors
class ALU_toolbar {

	function _EDIT() {
		global $jlms_toolbar_id;
		JToolBarHelper::title( _JOOMLMS_COMP_NAME.': '.($jlms_toolbar_id?_JLMS_USERS_EDIT_USER : _JLMS_USERS_NEW_USER) );
		JToolBarHelper::save('save', _JLMS_SAVE );
		JToolBarHelper::spacer();
		JToolBarHelper::cancel( 'cancel', _JLMS_CLOSE );
		JToolBarHelper::spacer();
	}

	function _DEFAULT() {
		JToolBarHelper::title( _JOOMLMS_COMP_NAME.': '._JLMS_USERS_GR_MNGRS_LIST, 'categories.png' );
		JToolBarHelper::addNew('new');		
		JToolBarHelper::spacer();
		JToolBarHelper::editList('edit');
		JToolBarHelper::spacer();
		JToolBarHelper::deleteList('', 'delete', _JLMS_DELETE );
		JToolBarHelper::spacer();
	}
}

function ALU_process_toolbar() {
	$page 	= mosGetParam( $_REQUEST, 'page', 'list' );
	switch ($page) {
		case 'assign_user_group_manager':
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