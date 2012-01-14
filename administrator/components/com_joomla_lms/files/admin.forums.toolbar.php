<?php
/**
* admin.forums.toolbar.php
* JoomlaLMS Component
*/

//defined( '_VALID_MOS' ) or die( 'Restricted access' );
if (!defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) { die( 'Restricted access' ); }

//processors
class ALF_toolbar {

	function _EDIT() {
		JToolBarHelper::title( _JOOMLMS_COMP_NAME.': '._JLMS_FRM_EDIT_FRM_BOARD );
		JToolBarHelper::save('save', _JLMS_SAVE );
		JToolBarHelper::spacer();
		JToolBarHelper::apply('apply', _JLMS_APPLY );
		JToolBarHelper::spacer();
		JToolBarHelper::cancel( 'cancel', _JLMS_CLOSE );
		JToolBarHelper::spacer();
	}

	function _DEFAULT() {
		JToolBarHelper::title( _JOOMLMS_COMP_NAME.': '._JLMS_FRM_S );
		JToolBarHelper::publishList('publish', _JLMS_PUBLISH );
		JToolBarHelper::spacer();
		JToolBarHelper::unpublishList('unpublish');
		JToolBarHelper::spacer();
		JToolBarHelper::deleteList('','remove');
		JToolBarHelper::spacer();
		JToolBarHelper::editListX('edit');
		JToolBarHelper::spacer();
		JToolBarHelper::addNewX('new');
		JToolBarHelper::spacer();
	}
}

function ALF_process_toolbar() {
	$page 	= mosGetParam( $_REQUEST, 'page', 'list' );
	switch ($page) {
		case 'edit':
		case 'editA':
		case 'new':
			ALF_toolbar::_EDIT();
		break;
		case 'list':
		default:
			ALF_toolbar::_DEFAULT();
		break;
	}
}
?>