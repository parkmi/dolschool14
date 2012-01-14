<?php
/**
* admin.roles.toolbar.php
* (c) JoomaLMS eLearning Software http://www.joomlalms.com/
**/

if (!defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) { die( 'Restricted access' ); }

//processors
class ALR_toolbar {

	function _EDIT() {
		global $is_jlms_trial_roles_heading_text;
		if (class_exists('JToolBarHelper')) {
			global $jlms_toolbar_id;
			JToolBarHelper::title( _JOOMLMS_COMP_NAME.': '.($jlms_toolbar_id?_JLMS_ROLES_EDIT_USER_ROLE : _JLMS_ROLES_NEW_USER_ROLE).($is_jlms_trial_roles_heading_text ? $is_jlms_trial_roles_heading_text : '') );
		}
		JToolBarHelper::save('save', _JLMS_SAVE );
		JToolBarHelper::spacer();
		JToolBarHelper::apply('apply', _JLMS_APPLY );
		JToolBarHelper::spacer();
		JToolBarHelper::cancel( 'cancel', _JLMS_CLOSE );
		JToolBarHelper::spacer();
	}

	function _DEFAULT() {
		global $is_jlms_trial_roles_heading_text;
		if (class_exists('JToolBarHelper')) {
			JToolBarHelper::title( _JLMS_ROLES_MANAGEMENT.($is_jlms_trial_roles_heading_text ? $is_jlms_trial_roles_heading_text : ''), 'user.png' );
		}
		JToolBarHelper::custom( 'default_role', 'publish.png', 'publish_f2.png', _JLMS_DEFAULT, true );
		JToolBarHelper::spacer();
		JToolBarHelper::divider();
			
		if( JLMS_J16version() ) 
		{
			$cfgClass = 'options';			
		} else {
			$cfgClass = 'config';
		}
		$bar = & JToolBar::getInstance('toolbar');
		$bar->appendButton( 'Popup', $cfgClass, 'Roles assignments', 'index.php?tmpl=component&option=com_joomla_lms&task=lms_roles&page=assignment', 950, 500 );
//		$bar->appendButton( 'Popup', 'config', 'Allow role assignments', 'index.php?option=com_joomla_lms&task=lms_roles&page=new' );
		
		JToolBarHelper::divider();
		JToolBarHelper::spacer();
		JToolBarHelper::addNewX('new');
		JToolBarHelper::spacer();
		JToolBarHelper::editList('edit');
		JToolBarHelper::spacer();
		JToolBarHelper::deleteList('', 'delete', _JLMS_DELETE);
		JToolBarHelper::spacer();
	}
}

function ALR_process_toolbar() {
	$page 	= mosGetParam( $_REQUEST, 'page', 'list' );
	switch ($page) {
		case 'edit':
		case 'editA':
		case 'new':
			ALR_toolbar::_EDIT();
		break;
		case 'list':
		default:
			ALR_toolbar::_DEFAULT();
		break;
	}
}
?>