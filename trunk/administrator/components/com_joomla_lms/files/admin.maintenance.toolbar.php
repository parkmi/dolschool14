<?php
/**
* admin.roles.toolbar.php
* JoomlaLMS Component
*/

//defined( '_VALID_MOS' ) or die( 'Restricted access' );
if (!defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) { die( 'Restricted access' ); }

//processors
class ALM_toolbar {

	function _DEFAULT() {
		JToolBarHelper::title( _JOOMLMS_COMP_NAME.': '._JLMS_MAIN );
	}

	function _CHECK_DATABASE() {
		JToolBarHelper::title( _JOOMLMS_COMP_NAME.': '._JLMS_MAIN_DB_CHECK );
	}

	function _SHOW_BUTTONS() {
		if (class_exists('JToolBarHelper')) {
			if (file_exists(dirname(__FILE__)."/../../../templates/".DEFAULT_ADMIN_TEMPLATE."/images/toolbar/icon-32-export.png")) {
				$doc = & JFactory::getDocument();
				$doc->addStyleDeclaration('.icon-32-export	{ background-image: url(templates/'.DEFAULT_ADMIN_TEMPLATE.'/images/toolbar/icon-32-export.png); }');
				JToolBarHelper::custom('maintenance_log', 'export.png', 'export.png', 'Log', false);
			} else {
				JToolBarHelper::custom('maintenance_log', 'save.png', 'save.png', 'Log', false);
			}
		} else {
			JToolBarHelper::custom('maintenance_log', 'download.png', 'download_f2.png', 'Log', false);
		}
		JToolBarHelper::spacer();
		JToolBarHelper::custom('check_database', 'archive.png', 'archive_f2.png', 'Check DB', false);
		JToolBarHelper::spacer();	
	}
}

function ALM_process_toolbar() {
	$page 	= JRequest::getVar('page', 'list');
	switch ($page) {
		case 'check_database':
		case 'check_tables': 
			ALM_toolbar::_CHECK_DATABASE();
			ALM_toolbar::_SHOW_BUTTONS();
		break;

		default:
			ALM_toolbar::_DEFAULT();
			ALM_toolbar::_SHOW_BUTTONS();
		break;
	}
}
?>