<?php
/**
* toolbar.joomla_lms.php
* Joomla LMS Component
* * * ElearningForce DK
*/

//defined( '_VALID_MOS' ) or die( 'Restricted access' );
if (!defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) { die( 'Restricted access' ); }
require_once( dirname(__FILE__).'/toolbar.joomla_lms.html.php' );

$task = JRequest::getVar('task', '', 'default', 'string');

switch ( $task ) {

	case 'lms_forums':
		require_once(_JOOMLMS_ADMIN_HOME.'/files/admin.forums.toolbar.php');
		ALF_process_toolbar();
	break;
	case 'lms_roles':
		require_once(_JOOMLMS_ADMIN_HOME.'/files/admin.roles.toolbar.php');
		ALR_process_toolbar();
	break;
	case 'lms_maintenance':
		require_once(_JOOMLMS_ADMIN_HOME.'/files/admin.maintenance.toolbar.php');
		ALM_process_toolbar();
	break;
	case 'dev_config':
		require_once(_JOOMLMS_ADMIN_HOME.'/files/admin.dev_config.toolbar.php');
		ALD_process_toolbar();
	break;
	case 'lms_users':
		require_once(_JOOMLMS_ADMIN_HOME.'/files/admin.users.toolbar.php');
		ALU_process_toolbar();
	break;
	case 'group_managers':
		require_once(_JOOMLMS_ADMIN_HOME.'/files/admin.group_managers.toolbar.php');
		ALU_process_toolbar();
	break;
	/* Pages Tips */
	case 'page_tips':
		TOOLBAR_Joomla_LMS::_PAGETIPSLIST();
		break;
	case 'new_ptip':
	case 'edit_ptip':
	case 'editA_ptip':
		TOOLBAR_Joomla_LMS::_PAGETIPEDIT();
	break;
	/* User ROLES */
	case 'roles':
		TOOLBAR_Joomla_LMS::_ROLESLIST();
		break;
	case 'new_role':
	case 'edit_role':
	case 'editA_role':
		TOOLBAR_Joomla_LMS::_ROLEEDIT();
	break;


	// processors
	case 'processorslist':
		TOOLBAR_processorslist::_DEFAULT();
	break;
	case 'config_subscriptions':
		TOOLBAR_Joomla_LMS::_SUBSCRIPTION_CONFIG();
	break;
	case 'edit_p':
	case 'editA_p':
		TOOLBAR_processorslist::_EDIT();
	break;
		
	//---------- PLANS ------------//
	case 'plans':
		TOOLBAR_plans::_DEFAULT();
	break;
	case 'edit_plan':	
	case 'editA_plan':
	case 'new_plan':
		TOOLBAR_plans::_EDIT();
	break;
	//end
	
	//---------- Discounts ------------//
	case 'discounts':
		TOOLBAR_discounts::_DEFAULT();
	break;
	case 'edit_discount':
	case 'editA_discount':
	case 'new_discount':
		TOOLBAR_discounts::_EDIT();
	break;
	//end
	
	//---------- Discount coupons ------------//
	case 'discount_coupons':
		TOOLBAR_discount_coupons::_DEFAULT();
	break;
	case 'edit_discount_coupon':
	case 'editA_discount_coupon':
	case 'new_discount_coupon':
		TOOLBAR_discount_coupons::_EDIT();
	case 'discount_coupons_statistics':
		TOOLBAR_discount_coupons::_STATISTICS();		
	break;
	//end
	
	// countries
	case 'countrieslist':
		TOOLBAR_countrieslist::_DEFAULT();
		break;
	case 'new_c':
	case 'edit_c':
	case 'editA_c':
		TOOLBAR_countrieslist::_EDIT();
		break;
	//users	
	case 'users':
		TOOLBAR_Joomla_LMS::_USERSLIST();
		break;
	/*case 'super_users':
		TOOLBAR_Joomla_LMS::_SUSERSLIST();
	break;*/
	case 'add_user':
	case 'edit_user':
	case 'editA_user':
		TOOLBAR_Joomla_LMS::_USEREDIT();
		break;
	/*case 'add_suser':
	case 'edit_suser':
	case 'editA_suser':
		TOOLBAR_Joomla_LMS::_SUSEREDIT();
	break;*/
	case 'classes':
		TOOLBAR_Joomla_LMS::_CLASSESLIST();
		break;
	case 'courses':
		TOOLBAR_Joomla_LMS::_COURSESLIST();
		break;
	case 'new_course':
		TOOLBAR_Joomla_LMS::_NEWCOURSEINSTRUCTIONS();
		break;
	case 'languages':
		TOOLBAR_Joomla_LMS::_LANGSLIST();
		break;
	case 'import_lang':
		TOOLBAR_Joomla_LMS::_IMPORTLANG();
		break;
	case 'viewA_class': case 'view_class':
		TOOLBAR_Joomla_LMS::_VIEWCLASS();
		break;
	case 'view_class_users':
		TOOLBAR_Joomla_LMS::_VIEWCLASSUSERS();
		break;
	case 'view_assistants':
		TOOLBAR_Joomla_LMS::_VIEWASSISTANTS();
		break;
		
	case 'view_childrens':
		TOOLBAR_Joomla_LMS::_VIEWCHILDRENS();
		break;
	case 'add_child':
	case 'edit_child':
		TOOLBAR_Joomla_LMS::_EDITCHILD();
		break;			
		
	case 'view_parents':
		TOOLBAR_Joomla_LMS::_VIEWPARENTS();
		break;
	case 'add_parent':
	case 'edit_parent':
	case 'editA_parent':
		TOOLBAR_Joomla_LMS::_ADDPARENT();
		break;
	case 'add_stu':
		TOOLBAR_Joomla_LMS::_ADDSTU();
		break;
	case 'add_assistant':
		TOOLBAR_Joomla_LMS::_ADDASSISTANT();
		break;
	case 'edit_stu':
		TOOLBAR_Joomla_LMS::_EDITSTU();
		break;
	case 'add_class':
	case 'edit_class':
	case 'editA_class':
		TOOLBAR_Joomla_LMS::_CLASSEDIT();
		break;
	case 'menu_manage':
		TOOLBAR_Joomla_LMS::_MENU_MANAGE();
		break;
	case 'config':
		TOOLBAR_Joomla_LMS::_CONFIG();
		break;
	case 'look_feel':
		TOOLBAR_Joomla_LMS::_LOOK_FEEL();
		break;	
	case 'cb_integration':
		TOOLBAR_Joomla_LMS::_CFG_CB_INTEGRATION();
		break;
	case 'cb_integration_edit':
	case 'cb_integration_add':
		TOOLBAR_Joomla_LMS::_CFG_CB_INTEGRATION_EDIT();
		break;		
	case 'frontpage':
		TOOLBAR_Joomla_LMS::_CFG_FRONTPAGE();
		break;
	
	case 'backup':
		TOOLBAR_Joomla_LMS::_BACKUPSLIST();
		break;

	case 'check_database_interface':
	case 'check_database':
		TOOLBAR_Joomla_LMS::_CHECK_DATABASE_INTERFACE();
		break;
		
	case 'courses_list':
		TOOLBAR_Joomla_LMS::_COURSE_COURSESLIST();
		break;
	case 'view_course_backup':
		TOOLBAR_Joomla_LMS::_COURSE_BACKUPSLIST();
		break;
	case 'import':
		TOOLBAR_Joomla_LMS::_IMPORT();
		break;	
	case 'new_subscription': case 'edit_subscription': case 'editA_subscription': 
		TOOLBAR_Joomla_LMS::_SUBSCRIPTION();
		break;	
	case 'assign':
		TOOLBAR_Joomla_LMS::_SUBSCRIPTION_ASSIGN();	
		break;
	case 'payments': 
		TOOLBAR_Joomla_LMS::_PAYMENT_LIST();
	break;
	case 'save_newpayment':
	case 'save_payment':
		TOOLBAR_Joomla_LMS::_PAYMENT_SAVE();
	break;
	case 'new_payment': 
		TOOLBAR_Joomla_LMS::_CREATE_PAYMENT();
	break;
	case 'edit_payment': 
	case 'editA_payment': 
		TOOLBAR_Joomla_LMS::_CHANGE_PAYMENT_INFO();
	break;
	case 'sales_report': 
		TOOLBAR_Joomla_LMS::_SALES_REPORT();
	break;
	case'subscriptions':
		TOOLBAR_Joomla_LMS::_SUBSCRIPTION_LIST();
	break;
	case 'renew':
		TOOLBAR_Joomla_LMS::_SUBSCRIPTION_RENEW();
	break;	
	case 'edit_course': case 'editA_course':
		TOOLBAR_Joomla_LMS::_COURSE_EDIT();
	break;
	case 'del_course':
		TOOLBAR_Joomla_LMS::_COURSE_DEL();
	break;	
	
	case 'csv_do_import':
	case 'csv_do_delete_yes':
		TOOLBAR_Joomla_LMS::_CSV_LOG();
	break;
	case 'csv_do_delete':
		TOOLBAR_Joomla_LMS::_CSV_DELETE_CONFIRM();
	break;

	case 'csv_operations':
		TOOLBAR_Joomla_LMS::_CSV_OPERATIONS();
	break;
	
	case 'about':
		TOOLBAR_Joomla_LMS::_ABOUT_PAGE();
	break;
	case 'support':
		TOOLBAR_Joomla_LMS::_SUPPORT_PAGE();
	break;
	case 'courses_template':
		TOOLBAR_Joomla_LMS::_COURTEMPL_LIST();
	break;
	case 'courses_templ_add':
	case 'courses_templ_edit':
		TOOLBAR_Joomla_LMS::_COURTEMPL_EDIT();
	break;
	//---------MESSAGES-------------//
	case 'mailsup_list':
		TOOLBAR_Joomla_LMS::_MAILSUP_LIST();
	break;
	case 'mailsup_new':
	case 'mailsup_edit':
		TOOLBAR_Joomla_LMS::_MAILSUP_EDIT();
	break;
	case 'mailsup_conf':
		TOOLBAR_Joomla_LMS::_MAILSUP_CONF();
	break;
	case 'notifications':
		//TOOLBAR_Joomla_LMS::_NOTIFICATIONS();
	break;
	case 'edit_notification':
		TOOLBAR_Joomla_LMS::_EDIT_NOTIFICATION();
	break;
	case 'email_templates':
		TOOLBAR_Joomla_LMS::_EMAIL_TEMPLATES();
	break;
	case 'new_email_template':
	case 'edit_email_template':
		TOOLBAR_Joomla_LMS::_EDIT_EMAIL_TEMPLATE();
	break;
	
	//----------CATEGORIES----------//
	case 'multicat':
		TOOLBAR_Joomla_LMS::_MULTICAT();
	break;
	case 'multicat_new':
	case 'multicat_edit':
	case 'multicat_editA':
		TOOLBAR_Joomla_LMS::_MULTICAT_EDIT();
	break;
	case 'multicat_config':
		TOOLBAR_Joomla_LMS::_MULTICAT_CONF();
	break;
	
	//----------PLUGINS-------------//
	case 'pluginslist':
		TOOLBAR_pluginslist::_DEFAULT();
	break;
	case 'edit_plugin':
	case 'editA_plugin':
		TOOLBAR_pluginslist::_EDIT();
	break;
	
	//----------WAITING LISTS-------//
	case 'show_waiting_lists':
		TOOLBAR_waitinglists::_DEFAULT();
	break;
	
	//----------Users with global groups enabled-----//
	case 'view_class_users_groups':
		TOOLBAR_users_in_groups::_DEFAULT();
	break;
	case 'add_stu_to_group':
		TOOLBAR_users_in_groups::_ADD();
	break;
	case 'list_courses_student':
		TOOLBAR_users_in_courses::_DELETE();
	break;	
	case 'view_class_users_courses':
		TOOLBAR_users_in_courses::_DEFAULT();
	break;
	case 'add_stu_to_course':
		TOOLBAR_users_in_courses::_ADD();
	break;
	case 'edit_stu_in_course':
		TOOLBAR_users_in_courses::_EDIT();
	break;
	default:
		# -- none -- #
		break;
}
?>