<?php
/**
* toolbar.joomla_lms.html.php
* Joomla LMS Component
* * * ElearningForce DK
*/

//defined( '_VALID_MOS' ) or die( 'Restricted access' );
if (!defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) { die( 'Restricted access' ); }

//processors
class TOOLBAR_processorslist {

	function _EDIT() {
		JToolBarHelper::title( _JOOMLMS_COMP_NAME.': '._JLMS_PROCS_TBR_EDIT_PAY_PROC );
		JToolBarHelper::save('save_p',_JLMS_SAVE);
		JToolBarHelper::spacer();
		JToolBarHelper::apply('apply_p',_JLMS_APPLY);
		JToolBarHelper::spacer();
		JToolBarHelper::cancel( 'cancel_p', _JLMS_CLOSE );
		JToolBarHelper::spacer();
	}

	function _DEFAULT() {
		JToolBarHelper::title( _JOOMLMS_COMP_NAME.': '._JLMS_PROCS_TBR_PROCS_LIST );
		JToolBarHelper::makeDefault('defaulta_p');		
		JToolBarHelper::spacer();
		JToolBarHelper::editListX('edit_p');
		JToolBarHelper::spacer();
	}
}

//plugins
class TOOLBAR_pluginslist {

	function _EDIT() {
		JToolBarHelper::title( _JOOMLMS_COMP_NAME.': '._JLMS_PLGS_TBR_EDIT, 'plugin.png' );
		JToolBarHelper::save('save_plugin', _JLMS_SAVE );
		JToolBarHelper::spacer();
		JToolBarHelper::apply('apply_plugin', _JLMS_APPLY );
		JToolBarHelper::spacer();
		JToolBarHelper::cancel( 'cancel_plugin', _JLMS_CLOSE );
		JToolBarHelper::spacer();
	}

	function _DEFAULT() {
		JToolBarHelper::title( _JOOMLMS_COMP_NAME.': '._JLMS_PLGS_TBR_LIST, 'plugin.png' );
		JToolBarHelper::deleteList('','remove_plugin');		
		JToolBarHelper::spacer();
		JToolBarHelper::editListX('edit_plugin');
		JToolBarHelper::spacer();				
	}
}

//waiting lists
class TOOLBAR_waitinglists {

	function _DEFAULT() {
		JToolBarHelper::title( _JOOMLMS_COMP_NAME.': '._JLMS_WAIT_TBR_LIST );
		JToolBarHelper::deleteList('','remove_from_waiting_list');
		JToolBarHelper::spacer();
		JToolBarHelper::makeDefault('add_from_waiting_list', _JLMS_WAIT_TBR_ADVANCE );
		JToolBarHelper::spacer();
	}
}

//countries
class TOOLBAR_countrieslist {

	function _EDIT() {
		global $jlms_toolbar_id;

		JToolBarHelper::title( _JOOMLMS_COMP_NAME.': '.($jlms_toolbar_id?_JLMS_SUBS_TBR_EDIT : _JLMS_SUBS_TBR_NEW) );
		JToolBarHelper::save('save_c', _JLMS_SAVE);
		JToolBarHelper::spacer();
		if ( $jlms_toolbar_id ) {
			JToolBarHelper::cancel( 'cancel_c', _JLMS_CLOSE );
		} else {
			JToolBarHelper::cancel('cancel_c');
		}
		JToolBarHelper::spacer();
	}

	function _DEFAULT() {
		JToolBarHelper::title( _JOOMLMS_COMP_NAME.': '._JLMS_SUBS_TBR_TAXES_LIST );
		JToolBarHelper::publishList('publish_c', _JLMS_PUBLISH);
		JToolBarHelper::spacer();
		JToolBarHelper::unpublishList('unpublish_c');
		JToolBarHelper::spacer();
		JToolBarHelper::deleteList('','remove_c');
		JToolBarHelper::spacer();
		JToolBarHelper::editListX('edit_c');
		JToolBarHelper::spacer();
		JToolBarHelper::addNewX('new_c');
		JToolBarHelper::spacer();
	}
}



class TOOLBAR_Joomla_LMS {

	function _PAGETIPSLIST() {
		JToolBarHelper::title( _JLMS_TIPS_TBR_MANAGEMENT, 'categories.png' );
		JToolBarHelper::addNewX('new_ptip');
		JToolBarHelper::spacer();
		JToolBarHelper::deleteList('', 'del_ptip', _JLMS_DELETE );
		JToolBarHelper::spacer();
		JToolBarHelper::editList('edit_ptip');
		JToolBarHelper::spacer();
	}
	function _PAGETIPEDIT() {
		global $jlms_toolbar_id;

		JToolBarHelper::title( _JOOMLMS_COMP_NAME.': '.($jlms_toolbar_id?_JLMS_TIPS_TBR_EDIT : _JLMS_TIPS_TBR_NEW) );
		JToolBarHelper::save('save_ptip', _JLMS_SAVE);
		JToolBarHelper::spacer();
		JToolBarHelper::apply('apply_ptip', _JLMS_APPLY);
		JToolBarHelper::spacer();
		JToolBarHelper::cancel( 'cancel_ptip', _JLMS_CLOSE );
		JToolBarHelper::spacer();
	}

	function _LANGSLIST(){
		JToolBarHelper::title( _JOOMLMS_COMP_NAME.': '._JLMS_LANG_TBR_LIST, 'langmanager.png' );
		JToolBarHelper::makeDefault('default_lang');
		JToolBarHelper::customX( 'import_lang', 'restore.png', 'restore_f2.png', _JLMS_IMPORT, false );
		JToolBarHelper::spacer();
		JToolBarHelper::custom( 'export_lang', 'upload.png', 'upload_f2.png', _JLMS_EXPORT, true );
		JToolBarHelper::spacer();
		JToolBarHelper::divider();
		JToolBarHelper::spacer();
		JToolBarHelper::deleteList('', 'del_lang', _JLMS_DELETE);
		JToolBarHelper::spacer();
	}
	function _IMPORTLANG() {
		JToolBarHelper::title( _JOOMLMS_COMP_NAME.': '._JLMS_LANG_TBR_IMPORT, 'langmanager.png' );
		JToolBarHelper::save('upload_lang');
		JToolBarHelper::spacer();
		JToolBarHelper::cancel('cancel_lang');
		JToolBarHelper::spacer();
	}
	function _COURSE_EDIT(){
		JToolBarHelper::title( _JOOMLMS_COMP_NAME.': '._JLMS_CRSS_TBR_EDIT, 'categories.png' );
		JToolBarHelper::save('save_course');
		JToolBarHelper::spacer();
		JToolBarHelper::apply('apply_course');
		JToolBarHelper::spacer();
		JToolBarHelper::cancel( 'cancel_course', _JLMS_CLOSE );
		JToolBarHelper::spacer();
	}
	function _COURSE_DEL(){
		JToolBarHelper::deleteList('','course_delete_yes');
		JToolBarHelper::spacer();
		JToolBarHelper::cancel( 'cancel_course', _JLMS_CANCEL );
		JToolBarHelper::spacer();
	}
	function _PAYMENT_SAVE(){
		JToolBarHelper::custom('apply_change', 'apply.png', 'apply_f2.png', _JLMS_APPLY, true);
		JToolBarHelper::spacer();
		JToolBarHelper::custom('skip_change', 'forward.png', 'forward_f2.png', _JLMS_SKIP, false);
		JToolBarHelper::spacer();
	}
	function _CHANGE_PAYMENT_INFO(){
		JToolBarHelper::title( _JOOMLMS_COMP_NAME.': '._JLMS_PAYS_TBR_DETS, 'checkin.png' );
		JToolBarHelper::save('save_payment');
		JToolBarHelper::spacer();
		JToolBarHelper::cancel( 'cancel_payment', 'Cancel' );
		JToolBarHelper::spacer();
	}
	function _CREATE_PAYMENT(){
		JToolBarHelper::title( _JOOMLMS_COMP_NAME.': New payment', 'checkin.png' );
		JToolBarHelper::save('save_newpayment');
		JToolBarHelper::spacer();
		JToolBarHelper::cancel( 'cancel_newpayment', 'Cancel' );
		JToolBarHelper::spacer();
	}
	function _PAYMENT_LIST(){
		JToolBarHelper::title( _JOOMLMS_COMP_NAME.': '._JLMS_PAYS_TBR_LIST, 'checkin.png' );
		JToolBarHelper::addNewX('new_payment');
		JToolBarHelper::spacer();
		JToolBarHelper::editListX('edit_payment');
		JToolBarHelper::spacer();
		JToolBarHelper::deleteList('', 'del_payments', _JLMS_DELETE );
		JToolBarHelper::spacer();
		JToolBarHelper::cancel( 'cancel', _JLMS_CLOSE );
		JToolBarHelper::spacer();
		
		$doc = & JFactory::getDocument();
		$app = & JFactory::getApplication();		
		
		$css = '.icon-32-print {background-image:url("templates/'.$app->getTemplate().'/images/toolbar/icon-32-print.png");}';
		$doc->addStyleDeclaration( $css );
		
		JToolBarHelper::customX( 'pays_list_pdf','print', '', _JLMS_PAYS_EXPORT_TO_PDF, false );
		JToolBarHelper::spacer();
	}
	
	function _SALES_REPORT()
	{
		JToolBarHelper::title( _JOOMLMS_COMP_NAME.': '._JLMS_PAYS_SALES_REPORT, 'checkin.png' );
		
		JToolBarHelper::spacer();		
		$doc = & JFactory::getDocument();
		$app = & JFactory::getApplication();		
		
		$css = '.icon-32-print {background-image:url("templates/'.$app->getTemplate().'/images/toolbar/icon-32-print.png");}';
		$doc->addStyleDeclaration( $css );
		
		JToolBarHelper::customX( 'sales_report_pdf','print', '', _JLMS_PAYS_EXPORT_TO_PDF, false );
		JToolBarHelper::spacer();
	}

	function _SUBSCRIPTION_LIST(){
		JToolBarHelper::title( _JOOMLMS_COMP_NAME.': '._JLMS_SUBS_TBR_LIST );
		JToolBarHelper::custom('assign', 'apply.png', 'apply_f2.png', _JLMS_SUBS_TBR_ASSIGN, false);
		JToolBarHelper::spacer();
		JToolBarHelper::custom('renew', 'default.png', 'default_f2.png', _JLMS_SUBS_TBR_RENEW, true);
		JToolBarHelper::spacer();
		JToolBarHelper::publishList('publish_subscription');
		JToolBarHelper::spacer();
		JToolBarHelper::unpublishList('unpublish_subscription');
		JToolBarHelper::spacer();
		JToolBarHelper::addNewX('new_subscription');
		JToolBarHelper::spacer();
		JToolBarHelper::editListX('edit_subscription');
		JToolBarHelper::spacer();
		JToolBarHelper::deleteList('', 'delete_subscription', _JLMS_DELETE);		
		JToolBarHelper::spacer();
	}
	function _SUBSCRIPTION_CONFIG(){
		JToolBarHelper::title( _JOOMLMS_COMP_NAME.': '._JLMS_SUBS_TBR_INV_CFG, 'config.png');
		JToolBarHelper::save('save_subconf');
		JToolBarHelper::spacer();
	}
	function _SUBSCRIPTION(){
		global $jlms_toolbar_id;

		JToolBarHelper::title( _JOOMLMS_COMP_NAME.': '.($jlms_toolbar_id?_JLMS_SUBS_TBR_EDIT : _JLMS_SUBS_TBR_ADD) );
		JToolBarHelper::save('subscription_save');
		JToolBarHelper::spacer();
		JToolBarHelper::apply('subscription_apply');
		JToolBarHelper::spacer();
		JToolBarHelper::cancel( 'cancel_sub', _JLMS_CLOSE );
		JToolBarHelper::spacer();
	}
	function _SUBSCRIPTION_ASSIGN(){
		global $jlms_toolbar_id;

		JToolBarHelper::title( _JOOMLMS_COMP_NAME.': '.($jlms_toolbar_id?'' : _JLMS_SUBS_TBR_ASSIGN) );
		JToolBarHelper::save('save_assign');
		JToolBarHelper::spacer();
		JToolBarHelper::cancel( 'cancel_assign', _JLMS_CLOSE );
		JToolBarHelper::spacer();
	}	
	
	function _SUBSCRIPTION_RENEW(){
		JToolBarHelper::custom('renew_apply', 'apply.png', 'apply_f2.png', _JLMS_APPLY, true);
		JToolBarHelper::spacer();
		JToolBarHelper::cancel( 'cancel_sub', _JLMS_CANCEL );
		JToolBarHelper::spacer();
	}
	
	function _MENU_MANAGE(){
		global $jlms_toolbar_menutype;
		$a = '';
		switch ($jlms_toolbar_menutype){
			case -1 : $a = _JLMS_MENUM_GUEST_M; break;
			case 0  : $a = _JLMS_MENUM_HOMEPAGE_M; break;
			case 1  : $a = _JLMS_MENUM_TEACHER_M; break;
			case 2  : $a = _JLMS_MENUM_STUDENT_M; break;
			case 6  : $a = _JLMS_MENUM_CEO_PARENT_M; break;
		}
		JToolBarHelper::title( _JOOMLMS_COMP_NAME.': '._JLMS_MENUM.' '.$a, 'config.png' );

		JToolBarHelper::publishList('publish_menu');
		JToolBarHelper::spacer();
		JToolBarHelper::unpublishList('unpublish_menu');
		JToolBarHelper::spacer();
		JToolBarHelper::cancel( 'cancel', _JLMS_CANCEL );
		JToolBarHelper::spacer();
	}
	function _CONFIG(){
		JToolBarHelper::title( _JOOMLMS_COMP_NAME.': '.JText::_( _JLMS_CFG ), 'config.png' );
		JToolBarHelper::save('config_save', _JLMS_SAVE);
		JToolBarHelper::spacer();
	}
	function _LOOK_FEEL(){
		JToolBarHelper::title( _JOOMLMS_COMP_NAME.': '._JLMS_LF_APPEARANCE, 'config.png' );
		JToolBarHelper::save('look_feel_save', _JLMS_SAVE);
		JToolBarHelper::spacer();
	}
	function _CFG_FRONTPAGE(){
		JToolBarHelper::title( _JOOMLMS_COMP_NAME.': '._JLMS_CFG_TBR_FRONT_SETUP, 'config.png' );
		JToolBarHelper::save('fp_save', _JLMS_SAVE);
		JToolBarHelper::spacer();
	}
	function _CFG_CB_INTEGRATION(){
		JToolBarHelper::title( _JOOMLMS_COMP_NAME.': '._JLMS_CBI_CB_INTEGRATION, 'config.png' );
		JToolBarHelper::deleteList('', 'cb_integration_delete', _JLMS_DELETE);
		JToolBarHelper::spacer();
		JToolBarHelper::editListX('cb_integration_edit');
		JToolBarHelper::spacer();
		JToolBarHelper::addNewX('cb_integration_add');
		JToolBarHelper::spacer();
		JToolBarHelper::save('cb_integration_save', _JLMS_SAVE);
		JToolBarHelper::spacer();
	}
	function _CFG_CB_INTEGRATION_EDIT(){
		global $jlms_toolbar_id;

		JToolBarHelper::title( _JOOMLMS_COMP_NAME.':  '.($jlms_toolbar_id?_JLMS_CBI_EDIT_CB_INTEGR : _JLMS_CBI_NEW_CB_INTEGR), 'config.png' );
		JToolBarHelper::save('cb_integration_save');
		JToolBarHelper::spacer();
		JToolBarHelper::cancel('cb_integration');
		JToolBarHelper::spacer();
	}

	function _COURSE_BACKUPSLIST(){
		global $jlms_toolbar_cname;
		JToolBarHelper::title( _JOOMLMS_COMP_NAME.': '._JLMS_BCK_TBR_COURSE.' : '.$jlms_toolbar_cname.'. '._JLMS_BCK_TBR_BACKUPS_LIST.'.' );
		JToolBarHelper::custom('back', 'back.png', 'back_f2.png', _JLMS_BCK_TBR_BACK, false);
		JToolBarHelper::spacer();
		JToolBarHelper::custom('backup', 'archive.png', 'archive_f2.png', _JLMS_BCK_TBR_TOTAL_BACKS, false);
		JToolBarHelper::spacer();
		JToolBarHelper::custom('course_backup_gen', 'archive.png', 'archive_f2.png', _JLMS_BCK_TBR_GENERATE, false);
		JToolBarHelper::spacer();
		if (file_exists(dirname(__FILE__)."/../../templates/".DEFAULT_ADMIN_TEMPLATE."/images/toolbar/icon-32-export.png")) {
			$doc = & JFactory::getDocument();
			$doc->addStyleDeclaration('.icon-32-export	{ background-image: url(templates/'.DEFAULT_ADMIN_TEMPLATE.'/images/toolbar/icon-32-export.png); }');
			JToolBarHelper::custom('course_export', 'export.png', 'export.png', _JLMS_EXPORT, false);
		} else {
			JToolBarHelper::custom('course_export', 'save.png', 'save.png', _JLMS_EXPORT, false);
		}
		JToolBarHelper::spacer();
		JToolBarHelper::deleteList('', 'course_backups_del', _JLMS_DELETE);
		JToolBarHelper::spacer();
		JToolBarHelper::cancel('cancel_backups');
		JToolBarHelper::spacer();
	}
	function _COURSE_COURSESLIST(){
		JToolBarHelper::title( _JOOMLMS_COMP_NAME.': '._JLMS_BCK_CRSS_LIST );
		JToolBarHelper::custom('backup', 'archive.png', 'archive_f2.png', _JLMS_BCK_TBR_TOTAL_BACKS, false);
		JToolBarHelper::spacer();
		JToolBarHelper::custom('import', 'upload.png', 'upload.png', _JLMS_IMPORT, false);
		JToolBarHelper::spacer();
		JToolBarHelper::cancel('cancel_backups');
		JToolBarHelper::spacer();
	}

	function _IMPORT(){
		JToolBarHelper::title( _JOOMLMS_COMP_NAME.': '._JLMS_CRSS_TBR_IMPORT );
		JToolBarHelper::custom('back', 'back.png', 'back_f2.png', _JLMS_BACK, false);
		JToolBarHelper::spacer();
	}
	
	function _CHECK_DATABASE_INTERFACE() {
		JToolBarHelper::title( _JOOMLMS_COMP_NAME.': '._JLMS_BCK_TOTAL_LIST );
		JToolBarHelper::custom('check_database', 'download.png', 'download_f2.png', _JLMS_BCK_TBR_CHECK_DB, false);
		JToolBarHelper::spacer();
	}
	
	function _BACKUPSLIST() {
		JToolBarHelper::title( _JOOMLMS_COMP_NAME.': '._JLMS_BCK_TOTAL_LIST );
		JToolBarHelper::custom('courses_list', 'archive.png', 'archive_f2.png', _JLMS_BCK_TBR_BACKUPS, false);
		JToolBarHelper::spacer();
		JToolBarHelper::custom('backup_generate', 'archive.png', 'archive_f2.png', _JLMS_BCK_TBR_GENERATE, false);
		JToolBarHelper::spacer();
		JToolBarHelper::custom('backup_restore', 'restore.png', 'restore_f2.png', _JLMS_BCK_TBR_RESTORE, true);
		JToolBarHelper::spacer();
		JToolBarHelper::deleteList('', 'backups_delete', _JLMS_DELETE);
		JToolBarHelper::spacer();
		JToolBarHelper::cancel('cancel_backups');
		JToolBarHelper::spacer();
	}
	function _USERSLIST() {
		JToolBarHelper::title( _JOOMLMS_COMP_NAME.': '._JLMS_USERS_LIST, 'user.png' );
		JToolBarHelper::deleteList('', 'del_user', _JLMS_DELETE);
		JToolBarHelper::spacer();
		JToolBarHelper::editListX('edit_user');
		JToolBarHelper::spacer();
		JToolBarHelper::addNewX('add_user');
		JToolBarHelper::spacer();
	}

	function _USEREDIT() {
		global $jlms_toolbar_id;
		JToolBarHelper::title( _JOOMLMS_COMP_NAME.':  '.($jlms_toolbar_id?_JLMS_USERS_EDIT_USER : _JLMS_USERS_NEW_USER), 'user.png' );
		JToolBarHelper::save('save_user');
		JToolBarHelper::spacer();
		JToolBarHelper::apply('apply_user');
		JToolBarHelper::spacer();
		if ( $jlms_toolbar_id ) {
			JToolBarHelper::cancel( 'cancel_user', _JLMS_CLOSE );
		} else {
			JToolBarHelper::cancel('cancel_user');
		}
		JToolBarHelper::spacer();
	}

	function _CLASSESLIST() {
		global $JLMS_CONFIG;
		JToolBarHelper::title( _JOOMLMS_COMP_NAME.': '._JLMS_USERS_GRS_CLS_LIST, 'categories.png' );
		if ($JLMS_CONFIG->get('use_global_groups', 1)) {
			JToolBarHelper::custom('assign_user_group_manager', 'apply.png', 'apply_f2.png', _JLMS_ASSIGN, false);
			JToolBarHelper::spacer();
		}
		JToolBarHelper::addNew('add_class');
		JToolBarHelper::spacer();
		JToolBarHelper::editList('edit_class');
		JToolBarHelper::spacer();
		JToolBarHelper::deleteList('', 'del_class', _JLMS_DELETE);
		JToolBarHelper::spacer();
	}
	function _COURSESLIST() {
		JToolBarHelper::title( JText::_( _JLMS_CRSS_LIST ), 'categories.png' );
		JToolBarHelper::addNew('new_course');
		JToolBarHelper::spacer();
		JToolBarHelper::editList('edit_course');
		JToolBarHelper::spacer();
		JToolBarHelper::deleteList('', 'del_course', _JLMS_DELETE);
		JToolBarHelper::spacer();
	}
	function _NEWCOURSEINSTRUCTIONS() {
		JToolBarHelper::title( JText::_( _JOOMLMS_COMP_NAME.': '._JLMS_CRSS_HW_CRT_NEW_CRS ), 'categories.png' );
		JToolBarHelper::custom('cancel_course', 'back.png', 'back_f2.png', _JLMS_BACK, false);
		JToolBarHelper::spacer();
	}
	function _VIEWCLASS() {
		JToolBarHelper::cancel( 'cancel_class', _JLMS_CANCEL );
		JToolBarHelper::spacer();
	}
	function _VIEWCLASSUSERS() {
		global $option;
		JToolBarHelper::title( _JOOMLMS_COMP_NAME.': '._JLMS_USERS, 'user.png' );
		JToolBarHelper::addNew('add_stu');
		JToolBarHelper::spacer();
		JToolBarHelper::editList( 'edit_stu');
		JToolBarHelper::spacer();
		JToolBarHelper::deleteList('', 'remove_stu', _JLMS_REMOVE);
		JToolBarHelper::spacer();
	}
	function _VIEWASSISTANTS() {
		JToolBarHelper::title( _JOOMLMS_COMP_NAME.': '._JLMS_USERS_ASSISTANS, 'user.png' );
		JToolBarHelper::addNew('add_assistant');
		JToolBarHelper::spacer();
		JToolBarHelper::deleteList('', 'remove_assistant', _JLMS_REMOVE);
		JToolBarHelper::spacer();
	}
	
	function _VIEWCHILDRENS() {
		global $jlms_toolbar_utype;
		JToolBarHelper::title( _JOOMLMS_COMP_NAME.': '._JLMS_MENU_PARENTS_CEO, 'user.png' );
		JToolBarHelper::addNew('add_child');
		JToolBarHelper::spacer();
		JToolBarHelper::deleteList('', 'remove_parent', _JLMS_REMOVE);
		JToolBarHelper::spacer();
		JToolBarHelper::custom('view_parents', 'back.png', 'Back', 'Back', false);
		JToolBarHelper::spacer();
	}
	function _EDITCHILD() {
		global $jlms_toolbar_utype;
		JToolBarHelper::title( _JOOMLMS_COMP_NAME.': '._JLMS_MENU_PARENTS_CEO, 'user.png' );
		JToolBarHelper::save('save_child');
		JToolBarHelper::spacer();
		JToolBarHelper::cancel('view_childrens');
		JToolBarHelper::spacer();
	}
	
	function _VIEWPARENTS() {
		global $jlms_toolbar_utype;
		JToolBarHelper::title( _JOOMLMS_COMP_NAME.': '._JLMS_MENU_PARENTS_CEO, 'user.png' );
		JToolBarHelper::addNew('add_child');
		JToolBarHelper::spacer();
		JToolBarHelper::editList('edit_child');
		JToolBarHelper::spacer();
		JToolBarHelper::deleteList('', 'delete_parent', _JLMS_REMOVE);
		JToolBarHelper::spacer();
	}
	function _ADDASSISTANT() {
		global $jlms_toolbar_utype;
		JToolBarHelper::title( _JOOMLMS_COMP_NAME.': ' .(($jlms_toolbar_utype == 2) ? _JLMS_USERS_NEW_ASSISTANT : _JLMS_USERS_NEW_STUDENT), 'user.png' );
		JToolBarHelper::save('add_user_save');
		JToolBarHelper::spacer();
		JToolBarHelper::cancel('cancel_assistant');
		JToolBarHelper::spacer();
	}
	function _ADDPARENT() {
		global $jlms_toolbar_id;
		JToolBarHelper::title( _JOOMLMS_COMP_NAME.': ' .($jlms_toolbar_id ? _JLMS_CEO_EDIT_PARENT : _JLMS_CEO_NEW_PARENT), 'user.png' );
		JToolBarHelper::save('save_parent');
		JToolBarHelper::spacer();
		JToolBarHelper::cancel('cancel_parent');
		JToolBarHelper::spacer();
	}
	function _ADDSTU() {
		global $jlms_toolbar_utype;
		JToolBarHelper::title( _JOOMLMS_COMP_NAME.': ' .(($jlms_toolbar_utype == 2) ? _JLMS_USERS_NEW_ASSISTANT : _JLMS_USERS_NEW_STUDENT), 'user.png' );
		JToolBarHelper::save('add_user_save');
		JToolBarHelper::spacer();
		JToolBarHelper::cancel('cancel_stu');
		JToolBarHelper::spacer();
	}
	function _EDITSTU() {
		global $jlms_toolbar_utype;
		JToolBarHelper::title( _JOOMLMS_COMP_NAME.': '._JLMS_USERS_EDIT_STUDENT , 'user.png' );
		JToolBarHelper::save('edit_user_save');
		JToolBarHelper::spacer();
		JToolBarHelper::cancel('cancel_stu');
		JToolBarHelper::spacer();
	}
	function _CLASSEDIT() {
		global $jlms_toolbar_id;
		JToolBarHelper::title( _JOOMLMS_COMP_NAME.': ' .($jlms_toolbar_id ? _JLMS_USERS_EDIT_GR : _JLMS_USERS_NEW_GR), 'user.png' );
		JToolBarHelper::save('save_class');
		JToolBarHelper::spacer();
		JToolBarHelper::apply('apply_class');
		JToolBarHelper::spacer();
		if ( $jlms_toolbar_id ) {
			JToolBarHelper::cancel( 'cancel_class', _JLMS_CLOSE );
		} else {
			JToolBarHelper::cancel('cancel_class');
		}
		JToolBarHelper::spacer();
	}
	function _CSV_LOG(){
		JToolBarHelper::title( _JOOMLMS_COMP_NAME.': '._JLMS_CSV_OPER_LOG, 'user.png' );
		JToolBarHelper::custom('csv_back_to', 'back.png', 'back_f2.png', _JLMS_BACK, false);
		JToolBarHelper::spacer();
	}
	function _CSV_DELETE_CONFIRM(){
		JToolBarHelper::title( _JOOMLMS_COMP_NAME.': '._JLMS_CSV_CONFIRM_USER_DELETION, 'user.png' );
		JToolBarHelper::deleteList('', 'csv_do_delete_yes', _JLMS_DELETE);
		JToolBarHelper::spacer();
		JToolBarHelper::cancel('csv_back_to', _JLMS_CANCEL);
		JToolBarHelper::spacer();
	}

	function _ABOUT_PAGE() {
		global $lms_version;
		JToolBarHelper::title( _JOOMLMS_COMP_NAME.' '.$lms_version, 'credits.png' );
	}
	function _SUPPORT_PAGE() {
		JToolBarHelper::title( _JOOMLMS_COMP_NAME.': '._JLMS_ABOUT_TBR_SUPPORT, 'help_header.png' );
	}
	function _CSV_OPERATIONS() {
		global $lms_version;
		JToolBarHelper::title( _JOOMLMS_COMP_NAME.': '._JLMS_CSV_TBR_OPERATIONS, 'user.png' );
	}
	function _MAILSUP_LIST(){
		JToolBarHelper::title( _JOOMLMS_COMP_NAME.': '._JLMS_MAIL_ADDRESS_BOOK );
		JToolBarHelper::editList('mailsup_edit');
		JToolBarHelper::spacer();
		JToolBarHelper::addNew('mailsup_new');
		JToolBarHelper::spacer();
		JToolBarHelper::deleteList('', 'mailsup_delete', _JLMS_REMOVE);
		JToolBarHelper::spacer();
	}
	function _MAILSUP_EDIT(){
		global $jlms_mailsup_cid;
		JToolBarHelper::title( _JOOMLMS_COMP_NAME.': '.($jlms_mailsup_cid?_JLMS_MAIL_EDIT_CONTACT : _JLMS_MAIL_NEW_CONTACT) );
		JToolBarHelper::save('mailsup_save');
		JToolBarHelper::spacer();
		JToolBarHelper::cancel('mailsup_list');
		JToolBarHelper::spacer();
	}
	function _MAILSUP_CONF(){
		JToolBarHelper::title( _JOOMLMS_COMP_NAME.': '._JLMS_MAIL_TBR_MB_CONFIG, 'config.png' );
		JToolBarHelper::save('mailsup_conf_save');
		JToolBarHelper::spacer();
	}	
	
	function _NOTIFICATIONS(){
		JToolBarHelper::title( _JOOMLMS_COMP_NAME.': '._JLMS_NOTS_EMAIL_NOTS, 'config.png' );
		JToolBarHelper::editList('edit_notification');		
		JToolBarHelper::spacer();
		JToolBarHelper::spacer();
	}
	
	function _EDIT_NOTIFICATION(){
		JToolBarHelper::title( _JOOMLMS_COMP_NAME.': '._JLMS_NOTS_EDIT_NOT, 'config.png' );
		JToolBarHelper::save('save_notification');
		JToolBarHelper::spacer();
		JToolBarHelper::apply('apply_notification', 'Apply');
		JToolBarHelper::spacer();
		JToolBarHelper::cancel('notifications');
		JToolBarHelper::spacer();
	}
	function _EMAIL_TEMPLATES(){
		JToolBarHelper::title( _JOOMLMS_COMP_NAME.': '._JLMS_NOTS_EMAIL_TPLS, 'config.png' );
		JToolBarHelper::addNew('new_email_template');
		JToolBarHelper::spacer();
		JToolBarHelper::editList('edit_email_template');		
		JToolBarHelper::spacer();		
		JToolBarHelper::deleteList('', 'delete_email_templates', _JLMS_REMOVE);
		JToolBarHelper::spacer();
	}
	
	function _EDIT_EMAIL_TEMPLATE(){
		global $templateid;
		JToolBarHelper::title( _JOOMLMS_COMP_NAME.':'.(($templateid)?_JLMS_NOTS_EDIT_EML_TPL:_JLMS_NOTS_NEW_EML_TPL), 'config.png' );

		$id 	= intval( mosGetParam( $_REQUEST, 'id', 0 ) );				
		$native = NotificationsManager::getNativeEmailTemplate( $id );
								
		if( !$native ) {		
			JToolBarHelper::save('save_email_template');
			JToolBarHelper::spacer();
			JToolBarHelper::apply('apply_email_template', _JLMS_APPLY);
			JToolBarHelper::spacer();
		}
		JToolBarHelper::cancel('email_templates');
		JToolBarHelper::spacer();
	}

	//FLMS
	function _MULTICAT(){
		JToolBarHelper::title( _JOOMLMS_COMP_NAME.': '._JLMS_FLMS_CATS_MANG, 'categories.png' );
		JToolBarHelper::spacer();
		JToolBarHelper::editList('multicat_edit');
		JToolBarHelper::spacer();
		JToolBarHelper::addNew('multicat_new');
		JToolBarHelper::spacer();
		JToolBarHelper::deleteList('', 'multicat_delete', _JLMS_REMOVE);
		JToolBarHelper::spacer();
	}
	function _MULTICAT_EDIT(){
		global $menuid;
		JToolBarHelper::title( _JOOMLMS_COMP_NAME.': '.($menuid?_JLMS_FLMS_EDIT_CAT:_JLMS_FLMS_ADD_CAT), 'categories.png' );
		JToolBarHelper::spacer();
		JToolBarHelper::save('multicat_save');
		JToolBarHelper::spacer();
		JToolBarHelper::apply('multicat_apply', _JLMS_APPLY);
		JToolBarHelper::spacer();
		JToolBarHelper::cancel('multicat');
		JToolBarHelper::spacer();
	}
	function _MULTICAT_CONF(){
		JToolBarHelper::title( _JOOMLMS_COMP_NAME.': '._JLMS_FLMS_CONF, 'config.png' );
		JToolBarHelper::spacer();
		JToolBarHelper::save('multicat_config_save');
		JToolBarHelper::spacer();
		JToolBarHelper::custom('multicat', 'back.png', 'back_f2.png', _JLMS_BACK, false);
		JToolBarHelper::spacer();
	}

	function _COURTEMPL_LIST()
	{
		JToolBarHelper::title( _JOOMLMS_COMP_NAME.': '._JLMS_CRSS_TPLS, 'install.png' );
		JToolBarHelper::editList('courses_templ_edit');
		JToolBarHelper::spacer();
		JToolBarHelper::addNew('courses_templ_add');
		JToolBarHelper::spacer();
		JToolBarHelper::deleteList('', 'courses_templ_del', _JLMS_REMOVE);
		JToolBarHelper::spacer();
	}
	function _COURTEMPL_EDIT(){
		global $jlms_toolbar_id;
		JToolBarHelper::title( _JOOMLMS_COMP_NAME.': '.($jlms_toolbar_id?_JLMS_CRSS_EDIT_CRS_TPL:_JLMS_CRSS_NEW_CRS_TPL), 'install.png' );
		JToolBarHelper::save('courses_templ_save');
		JToolBarHelper::spacer();
		JToolBarHelper::cancel('courses_template', _JLMS_CANCEL);
		JToolBarHelper::spacer();
	}
}

class TOOLBAR_users_in_groups
{
	function _DEFAULT() {
		JToolBarHelper::title( _JOOMLMS_COMP_NAME.': '._JLMS_USERS, 'user.png' );
		JToolBarHelper::addNew('add_stu_to_group');
		JToolBarHelper::spacer();
		JToolBarHelper::editList( 'add_stu_to_group');
		JToolBarHelper::spacer();
		JToolBarHelper::deleteList('', 'remove_stu_from_group', _JLMS_REMOVE);
		JToolBarHelper::spacer();
		JToolBarHelper::custom('cancel_class', 'back.png', 'back_f2.png', _JLMS_BACK, false);
		JToolBarHelper::spacer();
	}
	function _ADD() {
		JToolBarHelper::title( _JOOMLMS_COMP_NAME.': '._JLMS_USERS_TBR_ADD_STUDENT , 'user.png' );
		JToolBarHelper::save('save_stu_in_group');
		JToolBarHelper::spacer();
		JToolBarHelper::cancel( 'cancel_user_in_group', _JLMS_CANCEL );
		JToolBarHelper::spacer();
	}
}
class TOOLBAR_users_in_courses
{
	function _DEFAULT() {
		JToolBarHelper::title( _JOOMLMS_COMP_NAME.': '._JLMS_USERS, 'user.png' );
		JToolBarHelper::addNew('add_stu_to_course');
		JToolBarHelper::spacer();
		JToolBarHelper::editList( 'edit_stu_in_course');
		JToolBarHelper::spacer();
		JToolBarHelper::deleteList('', 'remove_stu_from_course', _JLMS_REMOVE);
		JToolBarHelper::spacer();
	}
	function _ADD() {
		JToolBarHelper::title( _JOOMLMS_COMP_NAME.': '._JLMS_USERS_TBR_ADD_STUDENT , 'user.png' );
		JToolBarHelper::save('add_user_save');
		JToolBarHelper::spacer();
		JToolBarHelper::cancel( 'cancel_user_in_course', _JLMS_CANCEL );
		JToolBarHelper::spacer();
	}
	function _EDIT() {
		JToolBarHelper::title( _JOOMLMS_COMP_NAME.': '._JLMS_USERS_TBR_EDIT_STUDENT , 'user.png' );
		JToolBarHelper::save('edit_user_save');
		JToolBarHelper::spacer();
		JToolBarHelper::cancel( 'cancel_user_in_course', _JLMS_CANCEL );
		JToolBarHelper::spacer();
	}
	function _DELETE(){
		JToolBarHelper::title( _JOOMLMS_COMP_NAME.': '._JLMS_USERS_TBR_EDIT_STUDENT , 'user.png' );
		JToolBarHelper::deleteList('', 'remove_stu_from_course', _JLMS_REMOVE);
		JToolBarHelper::spacer();
		JToolBarHelper::cancel( 'cancel_user_in_course', _JLMS_CANCEL );
		JToolBarHelper::spacer();
	}
}

class TOOLBAR_plans
{
	function _DEFAULT()
	{
		JToolBarHelper::title( _JOOMLMS_COMP_NAME.': '._JLMS_PLANS, '' );
		JToolBarHelper::addNew('new_plan');
		JToolBarHelper::spacer();
		JToolBarHelper::editList( 'edit_plan');
		JToolBarHelper::spacer();
		JToolBarHelper::deleteList('', 'delete_plan', _JLMS_REMOVE);
		JToolBarHelper::spacer();	
	}
	function _EDIT(){
		global $jlms_toolbar_id;
		JToolBarHelper::title( _JOOMLMS_COMP_NAME.': '.($jlms_toolbar_id?_JLMS_PLANS_EDIT_PLAN:_JLMS_PLANS_NEW_PLAN), 'install.png' );
		JToolBarHelper::save('save_plan');
		JToolBarHelper::spacer();
		JToolBarHelper::cancel('plans', _JLMS_CANCEL);
		JToolBarHelper::spacer();
	}	
}
class TOOLBAR_discounts
{
	function _DEFAULT()
	{
		JToolBarHelper::title( _JOOMLMS_COMP_NAME.': '._JLMS_DISC_S, '' );
		JToolBarHelper::addNew('new_discount');
		JToolBarHelper::spacer();
		JToolBarHelper::editList( 'edit_discount');
		JToolBarHelper::spacer();
		JToolBarHelper::publishList('enable_discount',_JLMS_ENABLE);
		JToolBarHelper::spacer();
		JToolBarHelper::unpublishList('disable_discount', _JLMS_DISABLE);
		JToolBarHelper::spacer();
		JToolBarHelper::deleteList('', 'delete_discount', _JLMS_REMOVE);
		JToolBarHelper::spacer();	
	}
	function _EDIT(){
		global $jlms_toolbar_id;
		JToolBarHelper::title( _JOOMLMS_COMP_NAME.': '.($jlms_toolbar_id?_JLMS_DISC_EDIT:_JLMS_DISC_NEW), 'install.png' );
		JToolBarHelper::save('save_discount');
		JToolBarHelper::spacer();
		JToolBarHelper::cancel('discounts', _JLMS_CANCEL);
		JToolBarHelper::spacer();
	}	
}

class TOOLBAR_discount_coupons
{
	function _DEFAULT()
	{
		JToolBarHelper::title( _JOOMLMS_COMP_NAME.': '._JLMS_DISC_COUPONS, '' );
		JToolBarHelper::addNew('new_discount_coupon');
		JToolBarHelper::spacer();
		JToolBarHelper::editList( 'edit_discount_coupon');
		JToolBarHelper::spacer();
		JToolBarHelper::publishList('enable_discount_coupon',_JLMS_ENABLE);
		JToolBarHelper::spacer();
		JToolBarHelper::unpublishList('disable_discount_coupon', _JLMS_DISABLE);
		JToolBarHelper::spacer();
		JToolBarHelper::deleteList('', 'delete_discount_coupon', _JLMS_REMOVE);
		JToolBarHelper::spacer();	
	}
	
	function _EDIT(){
		global $jlms_toolbar_id;
		JToolBarHelper::title( _JOOMLMS_COMP_NAME.': '.($jlms_toolbar_id?_JLMS_DISC_C_EDIT:_JLMS_DISC_C_NEW), 'install.png' );
		JToolBarHelper::save('save_discount_coupon');
		JToolBarHelper::spacer();
		JToolBarHelper::cancel('discount_coupons', _JLMS_CANCEL);
		JToolBarHelper::spacer();
	}
	
	function _STATISTICS() 
	{
		JToolBarHelper::title( _JOOMLMS_COMP_NAME.': '._JLMS_DISC_C_U_STATS, 'install.png' );
	}	
}
?>