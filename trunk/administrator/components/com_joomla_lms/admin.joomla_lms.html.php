<?php
/**
* admin.joomla_lms.html.php
* (c) JoomaLMS eLearning Software http://www.joomlalms.com/
**/

// no direct access
if (!defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) { die( 'Restricted access' ); }

class joomla_lms_adm_html
{
	##########################################################################
	###	--- ---   JLMS admin menu	 --- --- ###
	##########################################################################
	function JLMS_menu(){
		global $JLMS_CONFIG;

		$task = mosGetParam($_REQUEST, 'task', '');
		$show = 0;
		$info_img = JURI::root().'components/com_joomla_lms/lms_images/agenda/info.png';
		$support_img = JURI::root().'components/com_joomla_lms/lms_images/toolbar/tlb_quiz.png';
		$user_img = JURI::root().'components/com_joomla_lms/lms_images/toolbar/tlb_users.png';
		$courses_img = JURI::root().'components/com_joomla_lms/lms_images/toolbar/tlb_courses.png';
		$addrr_book_img = JURI::root().'components/com_joomla_lms/lms_images/toolbar/tlb_config.png';
		$txt_img = JURI::root().'components/com_joomla_lms/lms_images/files/file_text.png';
		$payments_img = JURI::root().'components/com_joomla_lms/lms_images/files/file_text.png';
		$sales_report_img = JURI::root().'components/com_joomla_lms/lms_images/files/file_text.png';
		$config_img = JURI::root().'components/com_joomla_lms/lms_images/toolbar/tlb_config.png';
		$mail_img = JURI::root().'components/com_joomla_lms/lms_images/toolbar/tlb_config.png';
		$appearance_img = JURI::root().'components/com_joomla_lms/lms_images/toolbar/tlb_config.png';
		$menu_img = JURI::root().'components/com_joomla_lms/lms_images/toolbar/tlb_config.png';
		$notifications_list_img = JURI::root().'components/com_joomla_lms/lms_images/toolbar/tlb_config.png';
		$email_templates_list_img = JURI::root().'components/com_joomla_lms/lms_images/toolbar/tlb_config.png';
		$language_img = JURI::root().'components/com_joomla_lms/lms_images/toolbar/tlb_config.png';
		$subscriptions_img = JURI::root().'components/com_joomla_lms/lms_images/toolbar/btn_subscriptions.png';
		$processors_img = JURI::root().'components/com_joomla_lms/lms_images/toolbar/btn_processors.png';
		$plugins_img = JURI::root().'components/com_joomla_lms/lms_images/toolbar/btn_processors.png';
		$backups_img = JURI::root().'components/com_joomla_lms/lms_images/toolbar/db_update.png';
		$export_img = JURI::root().'components/com_joomla_lms/lms_images/toolbar/tlb_fileexport.png';
		$certificate_img = JURI::root().'components/com_joomla_lms/lms_images/toolbar/btn_certificate.png';
		//FLMS (max)
		$multicat_img = JURI::root().'components/com_joomla_lms/lms_images/toolbar/tlb_courses.png';
		$multicat_config_img = JURI::root().'components/com_joomla_lms/lms_images/toolbar/tlb_config.png';
		if (class_exists('JToolBarHelper')) {						
			if (file_exists(JPATH_SITE.'/administrator/templates/'.DEFAULT_ADMIN_TEMPLATE.'/images/menu/icon-16-info.png')) {
				$info_img = JURI::root().'administrator/templates/'.DEFAULT_ADMIN_TEMPLATE.'/images/menu/icon-16-info.png';
			}
			if (file_exists(JPATH_SITE.'/administrator/templates/'.DEFAULT_ADMIN_TEMPLATE.'/images/menu/icon-16-help.png')) {
				$support_img = JURI::root().'administrator/templates/'.DEFAULT_ADMIN_TEMPLATE.'/images/menu/icon-16-help.png';
			}
			if (file_exists(JPATH_SITE.'/administrator/templates/'.DEFAULT_ADMIN_TEMPLATE.'/images/menu/icon-16-config.png')) {
				$config_img = JURI::root().'administrator/templates/'.DEFAULT_ADMIN_TEMPLATE.'/images/menu/icon-16-config.png';
			}
			if (file_exists(JPATH_SITE.'/administrator/templates/'.DEFAULT_ADMIN_TEMPLATE.'/images/menu/icon-16-user.png')) {
				$user_img = JURI::root().'administrator/templates/'.DEFAULT_ADMIN_TEMPLATE.'/images/menu/icon-16-user.png';
			}
			if (file_exists(JPATH_SITE.'/administrator/templates/'.DEFAULT_ADMIN_TEMPLATE.'/images/menu/icon-16-massmail.png')) {
				$addrr_book_img = JURI::root().'administrator/templates/'.DEFAULT_ADMIN_TEMPLATE.'/images/menu/icon-16-massmail.png';
			}
			if (file_exists(JPATH_SITE.'/administrator/templates/'.DEFAULT_ADMIN_TEMPLATE.'/images/menu/icon-16-checkin.png')) {
				$sales_report_img = JURI::root().'administrator/templates/'.DEFAULT_ADMIN_TEMPLATE.'/images/menu/icon-16-checkin.png';
			}			
			if (file_exists(JPATH_SITE.'/administrator/templates/'.DEFAULT_ADMIN_TEMPLATE.'/images/menu/icon-16-checkin.png')) {
				$payments_img = JURI::root().'administrator/templates/'.DEFAULT_ADMIN_TEMPLATE.'/images/menu/icon-16-checkin.png';
			}
			if (file_exists(JPATH_SITE.'/administrator/templates/'.DEFAULT_ADMIN_TEMPLATE.'/images/menu/icon-16-plugin.png')) {
				$plugins_img = JURI::root().'administrator/templates/'.DEFAULT_ADMIN_TEMPLATE.'/images/menu/icon-16-plugin.png';
			}
			if (file_exists(JPATH_SITE.'/administrator/templates/'.DEFAULT_ADMIN_TEMPLATE.'/images/menu/icon-16-language.png')) {
				$language_img = JURI::root().'administrator/templates/'.DEFAULT_ADMIN_TEMPLATE.'/images/menu/icon-16-language.png';
			}
			if (file_exists(JPATH_SITE.'/administrator/templates/'.DEFAULT_ADMIN_TEMPLATE.'/images/menu/icon-16-menu.png')) {
				$menu_img = JURI::root().'administrator/templates/'.DEFAULT_ADMIN_TEMPLATE.'/images/menu/icon-16-menu.png';
			}
			if( JLMS_J16version() ) 
			{ 
				if (file_exists(JPATH_SITE.'/administrator/templates/'.DEFAULT_ADMIN_TEMPLATE.'/images/menu/icon-16-config.png')) {
					$appearance_img = JURI::root().'administrator/templates/'.DEFAULT_ADMIN_TEMPLATE.'/images/menu/icon-16-config.png';
				}
			} else {
				if (file_exists(JPATH_SITE.'/administrator/templates/'.DEFAULT_ADMIN_TEMPLATE.'/images/menu/icon-16-frontpage.png')) {
					$appearance_img = JURI::root().'administrator/templates/'.DEFAULT_ADMIN_TEMPLATE.'/images/menu/icon-16-frontpage.png';
				}
			}
			if (file_exists(JPATH_SITE.'/administrator/templates/'.DEFAULT_ADMIN_TEMPLATE.'/images/menu/icon-16-messages.png')) {
				$mail_img = JURI::root().'administrator/templates/'.DEFAULT_ADMIN_TEMPLATE.'/images/menu/icon-16-messages.png';
			}
			if (file_exists(JPATH_SITE.'/administrator/templates/'.DEFAULT_ADMIN_TEMPLATE.'/images/menu/icon-16-messages.png')) {
				$notifications_list_img = JURI::root().'administrator/templates/'.DEFAULT_ADMIN_TEMPLATE.'/images/menu/icon-16-messages.png';
			}
			if (file_exists(JPATH_SITE.'/administrator/templates/'.DEFAULT_ADMIN_TEMPLATE.'/images/menu/icon-16-messages.png')) {
				$email_templates_list_img = JURI::root().'administrator/templates/'.DEFAULT_ADMIN_TEMPLATE.'/images/menu/icon-16-messages.png';
			}
			if (file_exists(JPATH_SITE.'/administrator/templates/'.DEFAULT_ADMIN_TEMPLATE.'/images/menu/icon-16-archive.png')) {
				$backups_img = JURI::root().'administrator/templates/'.DEFAULT_ADMIN_TEMPLATE.'/images/menu/icon-16-archive.png';
			}
			if (file_exists(JPATH_SITE.'/administrator/templates/'.DEFAULT_ADMIN_TEMPLATE.'/images/menu/icon-16-install.png')) {
				$export_img = JURI::root().'administrator/templates/'.DEFAULT_ADMIN_TEMPLATE.'/images/menu/icon-16-install.png';
			}
			if (file_exists(JPATH_SITE.'/administrator/templates/'.DEFAULT_ADMIN_TEMPLATE.'/images/menu/icon-16-content.png')) {
				$txt_img = JURI::root().'administrator/templates/'.DEFAULT_ADMIN_TEMPLATE.'/images/menu/icon-16-content.png';
			}
		} else {
			if (file_exists(JPATH_SITE.'/includes/js/ThemeOffice/users.png')) {
				$user_img = JURI::root().'includes/js/ThemeOffice/users.png';
			}
		}
	?>
	<?php
	switch ($task) {
		case 'csv_do_import':case 'csv_do_export':case 'csv_do_delete':case 'csv_operations':case 'csv_import':case 'csv_export':case 'csv_delete':
		case 'view_childrens': case'add_child': case 'edit_child':		
		case 'view_parents':case 'add_parent':case 'edit_parent':case 'view_class_users':case 'view_assistants':case 'add_stu':case 'edit_stu':case 'add_assistant':case 'add_stu_to_group':case 'add_stu_to_course':case 'view_class_users_groups':case 'view_class_users_courses':
		case 'users':case 'edit_user':case 'editA_user': case 'add_user':
		case 'classes':case 'add_class':case 'edit_class':case 'editA_class':case 'view_class':
		case 'certificates':
		case 'group_managers':
		//case 'roles':case 'new_role':case 'edit_role':case 'editA_role':
		case 'lms_roles': case 'lms_users':
		case 'show_waiting_lists':
		case 'list_courses_student':
			$show = 1;
			break;

		case 'courses':case 'edit_course':case 'editA_course':case 'courses_template':case 'courses_templ_add':case 'courses_templ_edit':
			$show = 2;
			break;
		
		//FLMS
		case 'multicat':case 'multicat_new':case 'multicat_edit':case 'multicat_editA':case 'multicat_config':
			$show = 3;
			break;
			
		case 'config':case 'lms_forums':case 'frontpage':case 'languages':case 'import_lang':case 'menu_manage':case 'cb_integration':case 'cb_integration_edit':case 'cb_integration_add':case 'page_tips':case 'new_ptip':case 'edit_ptip':case 'editA_ptip':case 'look_feel':
			$show = 4;
			break;
			
		case 'notifications': case 'email_templates': case 'edit_email_template': case 'new_email_template': case 'edit_notification':
			$show = 5;
			break;

		case 'assign':case 'save_assign':case 'cancel_assign':case 'subscriptions':case 'config_subscriptions':case 'payments':case 'edit_subscription':case 'editA_subscription':case 'processorslist':case 'editA_c':case 'new_c':case 'edit_c':case 'countrieslist' :case 'new_subscription':case 'edit_p':case 'editA_p':case 'editA_payment':case 'edit_payment':case 'new_payment':case 'save_payment':case 'renew':
		case 'plans': case 'duplicate_plan': case 'edit_plan': case 'editA_plan': case 'editA_plan': case 'new_plan':
		case 'new_discount': case 'edit_discount': case 'editA_discount': case 'discounts':
		case 'new_discount_coupon': case 'edit_discount_coupon': case 'editA_discount_coupon': case 'discount_coupons' : case 'discount_coupons_statistics': case 'sales_report':
			$show = 6;
			break;

		case 'pluginslist':case 'editA_plugin':case 'edit_plugin':
			$show = 7;
			break;

		case 'courses_list':case 'backup':case 'view_course_backup': case 'import':
			$show = 8;
			break;

//		case 'help':case 'faq':
//			$show = 9;
//			break;
		
		case 'lms_maintenance':
		case 'dev_config':
			$show = 9;
			break;	
			
		default:
			$show = 0;
			break;
	}
	if (substr($task,0,8) == 'mailsup_') {
		$show = 5;
	}

$jlms_menu_style_css = 'h3 { font-size:13px !important;}

a.menu_link:link, a.menu_link:visited {font-weight:bold;color:#000000; }
a.menu_link:hover{ color:#3366CC; text-decoration:none; }
/* pane-sliders  */
.pane-sliders .title { margin: 0;padding: 2px;color: #666; cursor: pointer;}
.pane-sliders .panel   { border: 1px solid #ccc; margin-bottom: 3px; text-align:left;}

.pane-sliders .panel h3 { background:transparent url('.JURI::root().'components/com_joomla_lms/lms_images/admin/panel_bg.png) repeat-x scroll 0%; color: #666}

.pane-sliders .content { background: #f6f6f6; }

.pane-sliders .adminlistm     { border: 0 none; }
.pane-sliders .adminlistm td  { border: 0 none; }

.jpane-toggler  span     { background: transparent url('.JURI::root().'components/com_joomla_lms/lms_images/admin/j_arrow.png) 5px 50% no-repeat; padding-left: 20px;}
.jpane-toggler-down span { background: transparent url('.JURI::root().'components/com_joomla_lms/lms_images/admin/j_arrow_down.png) 5px 50% no-repeat; padding-left: 20px;}

.jpane-toggler-down {  border-bottom: 1px solid #ccc;  }
.jpane-toggler .adminlistm tr { text-align:left;}

/* some redefines: */
div.pane-sliders .panel {
	border-right: none;
	border-left: none;
}
/* radio fieldset */ 
fieldset.radio { border: 0 none; margin: 0 !important; padding: 0 !important; }
';
// Add style to document head
$doc = & JFactory::getDocument();
$doc->addStyleDeclaration($jlms_menu_style_css);
if (!class_exists('JToolBarHelper')) { ?>
<script type="text/javascript" src="<?php echo JURI::root();?>components/com_joomla_lms/includes/js/mootools/mootools.js"></script>
<?php } ?>
<script type="text/javascript">
// <!--
window.addEvent('domready', function() {
		new Accordion($$('.panel h3.jpane-toggler'), $$('.panel div.jpane-slider'), {show:<?php echo $show;?>,onActive: function(toggler, i) { toggler.addClass('jpane-toggler-down'); toggler.removeClass('jpane-toggler'); },onBackground: function(toggler, i) { toggler.addClass('jpane-toggler'); toggler.removeClass('jpane-toggler-down'); },duration: 300,opacity: false});
});
// -->
</script>

	<table width="202" style="height:100%" cellpadding="0" cellspacing="0" >
	<tr ><td style=" height:7px; width:200px;background:url(<?php echo JURI::root();?>components/com_joomla_lms/lms_images/admin/top_menu_bg.jpg) no-repeat bottom left ">
	<img src="<?php echo JURI::root();?>components/com_joomla_lms/lms_images/blank.png" alt=' ' />
	</td></tr>
	<tr>
		<td style="border-left:1px solid #cccccc;border-right:1px solid #cccccc; background: white;" align="center"><img src="<?php echo JURI::root();?>components/com_joomla_lms/lms_images/logo_lms_small.png" alt="JoomlaLMS logo" title="JoomlaLMS" border="0"/></td>
	</tr>
	<tr>
		<td style="border-left:1px solid #cccccc;border-right:1px solid #cccccc; background: white;">
			<div id="content-pane" class="pane-sliders" style="margin: 0 !important;">
				<div class="panel"><h3 class="jpane-toggler title"><span><?php echo _JLMS_MENU_ABOUT_JOOMLALMS; ?></span></h3>
					<div class="jpane-slider content">
						<table class="adminlistm" style="border-spacing:0px;" cellpadding="4" cellspacing="4">
							<tr>
								<td width="16"><img src="<?php echo $info_img;?>" alt='i' /></td>
								<td class="title">
									<a class="menu_link" href="index.php?option=com_joomla_lms&amp;task=about"><?php echo _JLMS_MENU_ABOUT_JOOMLALMS; ?></a>
								</td>
							</tr>
							<tr>
								<td width="16"><img src="<?php echo $support_img;?>" alt='?' /></td>
								<td>
									<a class="menu_link" href="index.php?option=com_joomla_lms&amp;task=support"><?php echo _JLMS_MENU_SUPPORT; ?></a>
								</td>
							</tr>
						</table>
					</div>
				</div>
				<div class="panel"><h3 class="jpane-toggler title"><span>Users management</span></h3>
					<div class="jpane-slider content">
					<table class="adminlistm" style="border-spacing:0px;" cellpadding="4" cellspacing="4">
						<tr>
							<td width="16"><img src="<?php echo $user_img;?>" alt=" " /></td>
							<td class="title" colspan="2"><a class="menu_link" href="index.php?option=com_joomla_lms&amp;task=lms_users"><?php echo _JLMS_MENU_USERS_MANAGEMENT; ?></a></td>
						</tr>
						<tr>
							<td width="16"><img src="<?php echo $user_img;?>" alt=' ' /></td>
							<td class="title" colspan="2"><a class="menu_link" href="index.php?option=com_joomla_lms&amp;task=csv_operations"><?php echo _JLMS_MENU_CSV_OPERATIONS; ?></a></td>
						</tr>
						<?php if ($task == 'csv_do_import' || $task == 'csv_do_export' || $task == 'csv_do_delete' || $task == 'csv_operations' || $task == 'csv_import' || $task == 'csv_export' || $task == 'csv_delete') { ?>
						<tr>
							<td width="16"><img src="<?php echo JURI::root();?>components/com_joomla_lms/lms_images/treeview/sub1.png" alt=" " /></td>
							<td colspan="2"><a class="menu_link" href="index.php?option=com_joomla_lms&amp;task=csv_import"><?php echo _JLMS_MENU_CSV_IMPORT; ?></a></td>
						</tr>
						<tr>
							<td width="16"><img src="<?php echo JURI::root();?>components/com_joomla_lms/lms_images/treeview/sub1.png" alt=" " /></td>
							<td colspan="2"><a class="menu_link" href="index.php?option=com_joomla_lms&amp;task=csv_export"><?php echo _JLMS_MENU_CSV_EXPORT; ?></a></td>
						</tr>
						<tr>
							<td width="16"><img src="<?php echo JURI::root();?>components/com_joomla_lms/lms_images/treeview/sub2.png" alt=" " /></td>
							<td colspan="2"><a class="menu_link" href="index.php?option=com_joomla_lms&amp;task=csv_delete"><?php echo _JLMS_MENU_CSV_DELETE; ?></a></td>
						</tr>
						<?php }?>
						<?php if ($JLMS_CONFIG->get('roles_management', 0)) { ?>
						<tr>
							<td width="16"><img src="<?php echo JURI::root();?>components/com_joomla_lms/lms_images/toolbar/tlb_users.png" alt=" " /></td>
							<td class="title" colspan="2"><a class="menu_link" href="index.php?option=com_joomla_lms&amp;task=lms_roles"><?php echo _JLMS_MENU_ROLES_MANAGEMENT; ?></a></td>
						</tr>
						<?php } ?>
						<tr>
							<td width="16"><img src="<?php echo $user_img;?>" alt=" " /></td>
							<td class="title" colspan="2"><a class="menu_link" href="index.php?option=com_joomla_lms&amp;task=classes"><?php echo _JLMS_MENU_GROUPS_CLASSES; ?></a></td>
						</tr>
						<tr>
							<td width="16"><img src="<?php echo $user_img;?>" alt=" " /></td>
							<td class="title" colspan="2"><a class="menu_link" href="index.php?option=com_joomla_lms&amp;task=group_managers"><?php echo _JLMS_MENU_GROUP_MANAGERS;?></a></td>
						</tr>
						<tr>
							<td width="16"><img src="<?php echo $user_img;?>" alt=" " /></td>
							<td class="title" colspan="2"><a class="menu_link" href="index.php?option=com_joomla_lms&amp;task=lms_users&amp;view_by=1"><?php echo _JLMS_MENU_VIEW_ASSISTANS;?></a></td>
						</tr>
<?php
						global $JLMS_CONFIG;
						if ($JLMS_CONFIG->get('use_global_groups', 1)) {
?>
						<tr>
							<td width="16"><img src="<?php echo $user_img;?>" alt=" " /></td>
							<td class="title" colspan="2"><a class="menu_link" href="index.php?option=com_joomla_lms&amp;task=view_class_users_groups"><?php echo _JLMS_MENU_GROUP_MEMBERS; ?></a></td>
						</tr>
						<tr>
							<td width="16"><img src="<?php echo $user_img;?>" alt=" " /></td>
							<td class="title" colspan="2"><a class="menu_link" href="index.php?option=com_joomla_lms&amp;task=view_class_users_courses"><?php echo _JLMS_MENU_VIEW_STUDENTS_IN_COURSES; ?></a></td>
						</tr>
							<?php
						}
						else {
						?>
						<tr>
							<td width="16"><img src="<?php echo $user_img;?>" alt=" " /></td>
							<td class="title" colspan="2"><a class="menu_link" href="index.php?option=com_joomla_lms&amp;task=view_class_users"><?php echo _JLMS_MENU_VIEW_STUDENTS; ?></a></td>
						</tr>
						<?php
						}
						?>
						<tr>
							<td width="16"><img src="<?php echo $user_img;?>" alt=" " /></td>
							<td class="title" colspan="2"><a class="menu_link" href="index.php?option=com_joomla_lms&amp;task=view_parents"><?php echo _JLMS_MENU_PARENTS_CEO; ?></a></td>
						</tr>
						<tr>
							<td width="16"><img src="<?php echo $certificate_img;?>" alt=" " /></td>
							<td class="title" colspan="2"><a class="menu_link" href="index.php?option=com_joomla_lms&amp;task=certificates"><?php echo _JLMS_MENU_CERTIFICATES; ?></a></td>
						</tr>
						<tr>
							<td width="16"><img src="<?php echo $txt_img;?>" alt=" " /></td>
							<td class="title" colspan="2"><a class="menu_link" href="index.php?option=com_joomla_lms&amp;task=show_waiting_lists"><?php echo _JLMS_MENU_WAITING_LISTS; ?></a></td>
						</tr>
					</table>
					</div>
				</div>
				<div class="panel"><h3 class="jpane-toggler title"><span><?php echo _JLMS_MENU_COURSES_MANAGEMENT; ?></span></h3>
					<div class="jpane-slider content">
					<table class="adminlistm" style="border-spacing:0px;" cellpadding="4" cellspacing="4">
						<tr>
							<td width="16"><img src="<?php echo $courses_img;?>" alt=" " /></td>
							<td class="title"><a class="menu_link" href="index.php?option=com_joomla_lms&amp;task=courses"><?php echo _JLMS_MENU_COURSES_MANAGEMENT; ?></a></td>
						</tr>
						<tr>
							<td width="16"><img src="<?php echo $courses_img;?>" alt=" " /></td>
							<td class="title"><a class="menu_link" href="index.php?option=com_joomla_lms&amp;task=courses_template"><?php echo _JLMS_MENU_COURSES_TEMPLATES; ?></a></td>
						</tr>
					</table>
					</div>
				</div>
				<div class="panel"><h3 class="jpane-toggler title"><span><?php echo _JLMS_MENU_COURSE_CATEGORIES; ?></span></h3>
					<div class="jpane-slider content">
					<table class="adminlistm" style="border-spacing:0px;" cellpadding="4" cellspacing="4">
						<tr>
							<td width="16"><img src="<?php echo $multicat_img;?>" alt=" " /></td>
							<td>
								<a class="menu_link" href="index.php?option=com_joomla_lms&amp;task=multicat"><?php echo _JLMS_MENU_CATEGORIES_MANAGEMENT; ?> </a>
							</td>
						</tr>
						<?php
						if ($JLMS_CONFIG->get('multicat_show_admin_levels', 0)){
						?>
						<tr>
							<td width="16"><img src="<?php echo $multicat_config_img;?>" alt=" " /></td>
							<td class="title">
								<a class="menu_link" href="index.php?option=com_joomla_lms&amp;task=multicat_config"><?php echo _JLMS_MENU_CONFIGURATION; ?></a>
							</td>
						</tr>
						<?php
						}
						?>
					</table>
					</div>
				</div>
				<div class="panel"><h3 class="jpane-toggler title"><span><?php echo _JLMS_MENU_CONFIGURATION; ?></span></h3>
					<div class="jpane-slider content">
					<table class="adminlistm" style="border-spacing:0px;" cellpadding="4" cellspacing="4">
						<tr>
							<td width="16"><img src="<?php echo $config_img;?>" alt=" " /></td>
							<td colspan="2" class="title"><a class="menu_link" href="index.php?option=com_joomla_lms&amp;task=config"><?php echo _JLMS_MENU_CONFIGURATION; ?></a></td>
						</tr>
						<tr>
							<td width="16"><img src="<?php echo $appearance_img;?>" alt=" " /></td>
							<td colspan="2"><a class="menu_link" href="index.php?option=com_joomla_lms&amp;task=look_feel"><?php echo _JLMS_MENU_APPEARANCE; ?></a></td>
						</tr>
				<?php if ($JLMS_CONFIG->get('new_forums_config', 0)) { ?>
						<tr>
							<td width="16"><img src="<?php echo $config_img;?>" alt=" " /></td>
							<td colspan="2"><a class="menu_link" href="index.php?option=com_joomla_lms&amp;task=lms_forums"><?php echo _JLMS_MENU_SMF_FORUM_INTEGRATION; ?></a></td>
						</tr>
				<?php } ?>
						<tr>
							<td width="16"><img src="<?php echo $config_img;?>" alt=" " /></td>
							<td class="title" colspan="2"><a class="menu_link" href="index.php?option=com_joomla_lms&amp;task=cb_integration"><?php echo _JLMS_MENU_SMF_CB_INTEGRATION; ?></a></td>
						</tr>
						<tr>
							<td width="16"><img src="<?php echo $txt_img;?>" alt=" " /></td>
							<td colspan="2" class="title"><a class="menu_link" href="index.php?option=com_joomla_lms&amp;task=page_tips"><?php echo _JLMS_MENU_PAGE_TIPS_CONFIG; ?></a></td>
						</tr>
						<tr>
							<td width="16"><img src="<?php echo $menu_img;?>" alt=" " /></td>
							<td colspan="2" class="title"><a class="menu_link" href="index.php?option=com_joomla_lms&amp;task=menu_manage"><?php echo _JLMS_MENU_MANAGER; ?></a></td>
						</tr>
						<?php if ($task == 'menu_manage') { ?>
						<tr>
							<td width="16"><img src="<?php echo JURI::root();?>components/com_joomla_lms/lms_images/treeview/sub2.png" alt=" " /></td>
							<td><a class="menu_link" href="index.php?option=com_joomla_lms&amp;task=menu_manage&amp;menutype=-1"><?php echo _JLMS_MENU_GUEST_MENU; ?></a></td>
						</tr>
						<tr>
							<td width="16"><img src="<?php echo JURI::root();?>components/com_joomla_lms/lms_images/treeview/sub2.png" alt=" " /></td>
							<td><a class="menu_link" href="index.php?option=com_joomla_lms&amp;task=menu_manage&amp;menutype=0"><?php echo _JLMS_MENU_HOMEPAGE_MENU; ?></a></td>
						</tr>
						<tr>
							<td width="16"><img src="<?php echo JURI::root();?>components/com_joomla_lms/lms_images/treeview/sub2.png" alt=" " /></td>
							<td><a class="menu_link" href="index.php?option=com_joomla_lms&amp;task=menu_manage&amp;menutype=1"><?php echo _JLMS_MENU_TEACHER_MENU; ?></a></td>
						</tr>
						<tr>
							<td width="16"><img src="<?php echo JURI::root();?>components/com_joomla_lms/lms_images/treeview/sub2.png" alt=" " /></td>
							<td><a class="menu_link" href="index.php?option=com_joomla_lms&amp;task=menu_manage&amp;menutype=2"><?php echo _JLMS_MENU_STUDENT_MENU; ?></a></td>
						</tr>
						<tr>
							<td width="16"><img src="<?php echo JURI::root();?>components/com_joomla_lms/lms_images/treeview/sub2.png" alt=" " /></td>
							<td><a class="menu_link" href="index.php?option=com_joomla_lms&amp;task=menu_manage&amp;menutype=6"><?php echo _JLMS_MENU_CEO_MENU; ?></a></td>
						</tr>
						<?php }?>
						<tr>
							<td width="16"><img src="<?php echo $language_img;?>" alt=" " /></td>
							<td class="title" colspan="2"><a class="menu_link" href="index.php?option=com_joomla_lms&amp;task=languages"><?php echo _JLMS_MENU_LANGUAGES; ?></a></td>
						</tr>
					</table>
					</div>
				</div>
				<div class="panel"><h3 class="jpane-toggler title"><span><?php echo _JLMS_MENU_MAILBOX_SETTINGS; ?></span></h3>
					<div class="jpane-slider content">
					<table class="adminlistm" style="border-spacing:0px;" cellpadding="4" cellspacing="4">
						<tr>
							<td width="16"><img src="<?php echo $mail_img;?>" alt=" " /></td>
							<td>
								<a class="menu_link" href="index.php?option=com_joomla_lms&amp;task=mailsup_conf"><?php echo _JLMS_MENU_MAILBOX_CONFIGURATION; ?></a>
							</td>
						</tr>
						<tr>
							<td width="16"><img src="<?php echo $addrr_book_img;?>" alt=" " /></td>
							<td class="title">
								<a class="menu_link" href="index.php?option=com_joomla_lms&amp;task=mailsup_list"><?php echo _JLMS_MENU_ADDRESS_BOOK; ?></a>
							</td>
						</tr>						
						<tr>
							<td width="16"><img src="<?php echo $notifications_list_img;?>" alt=" " /></td>
							<td class="title">
								<a class="menu_link" href="index.php?option=com_joomla_lms&amp;task=notifications"><?php echo _JLMS_MENU_EMAIL_NOTIFICATIONS; ?></a>
							</td>
						</tr>
						<tr>
							<td width="16"><img src="<?php echo $email_templates_list_img;?>" alt=" " /></td>
							<td class="title">
								<a class="menu_link" href="index.php?option=com_joomla_lms&amp;task=email_templates"><?php echo _JLMS_MENU_TEMPLATES_MANAGER; ?></a>
							</td>
						</tr>	
					</table>
					</div>
				</div>
				<div class="panel"><h3 class="jpane-toggler title"><span><?php echo _JLMS_MENU_SUBSCRIPTIONS; ?></span></h3>
					<div class="jpane-slider content">
					<table class="adminlistm" style="border-spacing:0px;" cellpadding="4" cellspacing="4">
						<tr>
							<td width="16"><img src="<?php echo $subscriptions_img;?>" alt=" " /></td>
							<td class="title" colspan="2">
								<a class="menu_link" href="index.php?option=com_joomla_lms&amp;task=subscriptions"><?php echo _JLMS_MENU_SUBSCRIPTIONS_LIST; ?></a>
							</td>
						</tr>
						<tr>
							<td width="16"><img src="<?php echo $sales_report_img;?>" alt=" " /></td>
							<td class="title" colspan="2">
								<a class="menu_link" href="index.php?option=com_joomla_lms&amp;task=sales_report"><?php echo _JLMS_MENU_SALES_REPORT; ?></a>
							</td>
						</tr>
						<tr>
							<td width="16"><img src="<?php echo $payments_img;?>" alt=" " /></td>
							<td class="title" colspan="2">
								<a class="menu_link" href="index.php?option=com_joomla_lms&amp;task=payments"><?php echo _JLMS_MENU_PAYMENTS_LIST; ?></a>
							</td>
						</tr>
						<tr>
							<td width="16"><img src="<?php echo $config_img;?>" alt=" " /></td>
							<td class="title" colspan="2">
								<a class="menu_link" href="index.php?option=com_joomla_lms&amp;task=config_subscriptions"><?php echo _JLMS_MENU_INVOICE_CONFIG; ?></a>
							</td>
						</tr>
						<tr>
							<td width="16"><img src="<?php echo $txt_img;?>" alt=" " /></td>
							<td class="title" colspan="2">
								<a class="menu_link" href="index.php?option=com_joomla_lms&amp;task=countrieslist"><?php echo _JLMS_MENU_COUNTRIES_TAXES_LIST; ?></a>
							</td>
						</tr>
						<tr>
							<td width="16"><img src="<?php echo $processors_img;?>" alt=" " /></td>
							<td class="title" colspan="2">
								<a class="menu_link" href="index.php?option=com_joomla_lms&amp;task=processorslist"><?php echo _JLMS_MENU_PAYMENTS_PROCESSORS; ?></a>
							</td>
						</tr>
				<?php
					global $license_lms_recurrent;
					if ($JLMS_CONFIG->get('recurrent_payments_feature', false) && $license_lms_recurrent) { ?>
						<tr>
							<td width="16"><img src="<?php echo $payments_img;?>" alt=" " /></td>
							<td class="title" colspan="2">
								<a class="menu_link" href="index.php?option=com_joomla_lms&amp;task=plans"><?php echo _JLMS_MENU_PLANS_LIST; ?></a>
							</td>
						</tr>
				<?php } ?>
						<tr>
							<td width="16"><img src="<?php echo $payments_img;?>" alt=" " /></td>
							<td class="title" colspan="2">
								<a class="menu_link" href="index.php?option=com_joomla_lms&amp;task=discounts"><?php echo _JLMS_MENU_DISCOUNTS_LIST; ?></a>
							</td>
						</tr>
						<tr>
							<td width="16"><img src="<?php echo $payments_img;?>" alt=" " /></td>
							<td class="title" colspan="2">
								<a class="menu_link" href="index.php?option=com_joomla_lms&amp;task=discount_coupons"><?php echo _JLMS_MENU_DISCOUNTS_COUPONS_LIST; ?></a>
							</td>
						</tr>
						<tr>
							<td width="16"><img src="<?php echo $payments_img;?>" alt=" " /></td>
							<td class="title" colspan="2">
								<a class="menu_link" href="index.php?option=com_joomla_lms&amp;task=discount_coupons_statistics"><?php echo _JLMS_MENU_DISCOUNT_COUPONS_STATS; ?></a>
							</td>
						</tr>
					</table>
					</div>
				</div>
				<div class="panel"><h3 class="jpane-toggler title"><span><?php echo _JLMS_MENU_PLUGINS; ?></span></h3>
					<div class="jpane-slider content">
					<table class="adminlistm" style="border-spacing:0px;" cellpadding="4" cellspacing="4">
						<tr>
							<td width="16"><img src="<?php echo $plugins_img;?>" alt=" " /></td>
							<td class="title">
								<a class="menu_link" href="index.php?option=com_joomla_lms&amp;task=pluginslist"><?php echo _JLMS_MENU_SHOW_PLUGINS; ?></a>
							</td>
						</tr>
					</table>
					</div>
				</div>
				<div class="panel"><h3 class="jpane-toggler title"><span><?php echo _JLMS_MENU_BACKUPS; ?></span></h3>
					<div class="jpane-slider content">
					<table class="adminlistm" style="border-spacing:0px;" cellpadding="4" cellspacing="4">
						<tr>
							<td width="16"><img src="<?php echo $backups_img;?>" alt=" " /></td>
							<td class="title">
								<a class="menu_link" href="index.php?option=com_joomla_lms&amp;task=backup"><?php echo _JLMS_MENU_TOTAL_BACKUPS; ?></a>
							</td>
						</tr>
						<tr>
							<td width="16"><img src="<?php echo $export_img;?>" alt=" " /></td>
							<td>
								<a class="menu_link" href="index.php?option=com_joomla_lms&amp;task=courses_list"><?php echo _JLMS_MENU_COURSES_EXPORT; ?></a>
							</td>
						</tr>
					</table>
					</div>
				</div>
				<div class="panel"><h3 class="jpane-toggler title"><span><?php echo _JLMS_MENU_MAINTENANCE; ?></span></h3>
					<div class="jpane-slider content">
					<table class="adminlistm" style="border-spacing:0px;" cellpadding="4" cellspacing="4">
						<tr>
							<td width="16"><img src="<?php echo $backups_img;?>" alt=" " /></td>
							<td class="title">
								<a class="menu_link" href="index.php?option=com_joomla_lms&amp;task=lms_maintenance"><?php echo _JLMS_MENU_MAINTENANCE; ?></a>
							</td>
						</tr>
					</table>
					</div>
				</div>
			</div>
		</td>
	</tr>
	<tr ><td style=" height:8px; width:200px;background:url(<?php echo JURI::root();?>components/com_joomla_lms/lms_images/admin/bottom_menu_bg.jpg) no-repeat top left ">
		<img src="<?php echo JURI::root();?>components/com_joomla_lms/lms_images/blank.png" alt=' ' />
	</td></tr>
</table>
<?php
	}


	##########################################################################
	###	--- ---  PAFE TIPS 	--- --- ###
	##########################################################################
	function JLMS_showPageTipsList( &$tips, $option, &$lists) {
?>
<form action="index.php" method="post" name="adminForm">
<table width="100%" >
	<tr>
		<td valign="top" width="220">
		<div>
			<?php echo joomla_lms_adm_html::JLMS_menu();?>
		</div>
		</td>
		<td valign="top">		
		<?php if (!class_exists('JToolBarHelper')) { ?>
		<table class="adminheading" cellpadding="0" cellspacing="0" border="0">
		<tr>
			<th class="categories">
			<?php echo _JOOMLMS_COMP_NAME;?>: <small><?php echo _JLMS_TIPS_PAGE_MANAGEMENT; ?></small>
			</th>
		</tr>
		</table>
		<?php } ?>		
		<table width="100%" border="0">
			<tr>
				<td valign="top">
					<table class="adminlist">
					<thead>
						<tr>
							<th width="20" align="center">#</th>
							<th width="20" align="center">&nbsp;</th>
							<th class="title"><?php echo _JLMS_TIPS_PAGE; ?></th>
							<th align="left"><?php echo _JLMS_TIPS_USER_TIP_TEXT; ?></th>
						</tr>
					</thead>
					<tbody>
					<?php
					$k = 0;
					for ($i=0, $n=count($tips); $i < $n; $i++) {
						$tip = $tips[$i];
						$checked = mosHTML::idBox( $i, $tip->id);?>
						<tr class="<?php echo "row$k"; ?>">
							<td align="center"><?php echo ($i + 1);?></td>
							<td align="center"><?php echo $checked;?></td>
							<td>
						<?php 	switch ($tip->tip_task) {
							case 'subscription': echo _JLMS_TIPS_LIST_OF_SUBCRIPTIONS; break;
							case 'courses': echo _JLMS_TIPS_LIST_OF_COURSES; break;
							case 'show_cart': echo _JLMS_TIPS_MY_CART; break;
							case 'pre_enrollment': echo _JLMS_TIPS_PRE_ENROLLMENT_QUESTIONS; break;
							default: echo _JLMS_TIPS_UNKNOWN_PAGE; break;
								} ?>
							</td>
							<td><?php echo substr(strip_tags($tip->tip_message),0,200);?></td>
						</tr>
						<?php
						$k = 1 - $k;
					}?>
					</tbody>
					</table>					
				</td>
			</tr>
		</table>		
	</td></tr></table>
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="task" value="page_tips" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0" />		
		</form>
		<?php
	}

	function jlms_editPageTip( &$row, &$lists, $option ) {
		?>
<script language="javascript" type="text/javascript">
<!--
function setgood() {
	return true;
}

function submitbutton(pressbutton) {
	var form = document.adminForm;

	try {
		form.onsubmit();
	} catch(e) {
		//alert(e);
	}

	if (pressbutton == 'cancel_ptip') {
		submitform( pressbutton );
		return;
	}
	submitform( pressbutton );
} 

<?php if( JLMS_J16version() ) { ?>
	Joomla.submitbutton = submitbutton;
	<?php } ?>
//-->
</script>
<form action="index.php" method="post" name="adminForm" onsubmit="setgood();">
<table width="100%" >
	<tr>
		<td valign="top" width="220">
		<div>
			<?php echo joomla_lms_adm_html::JLMS_menu();?>
		</div>
		</td>
		<td valign="top">
		<div class="width-100">
		<fieldset class="adminform">
	<?php if (!class_exists('JToolBarHelper')) { ?>
		<table class="adminheading">
		<tr>
			<th class="user">
			<?php echo _JOOMLMS_COMP_NAME;?>:
			<small>
			<?php echo $row->id ? JLMS_TIPS_EDIT_PAGE_TIP : _JLMS_TIPS_NEW_PAGE_TIP;?>
			</small>
			</th>
		</tr>
		</table>
	<?php } ?>	
		<table width="100%" border="0">
			<tr>
				<td valign="top">
					<table width="100%" >
						<tr>
							<th colspan="2"><?php echo _JLMS_TIPS_PAGE_TIP_DETAILS; ?></th>
						<tr>
						<tr>
							<td align="right" width="20%"><?php echo _JLMS_TIPS_SELECT_A_PAGE; ?></td>
							<td>
								<select class="text_area" style="width:350px;" name="tip_task">
									<option value="subscription"<?php echo $row->tip_task == 'subscription' ? ' selected="selected"' : ''?>><?php echo _JLMS_TIPS_LIST_OF_SUBCRIPTIONS; ?></option>
									<option value="courses"<?php echo $row->tip_task == 'courses' ? ' selected="selected"' : ''?>><?php echo _JLMS_TIPS_LIST_OF_COURSES; ?></option>
									<option value="show_cart"<?php echo $row->tip_task == 'show_cart' ? ' selected="selected"' : ''?>><?php echo _JLMS_TIPS_MY_CART; ?></option>
									<option value="pre_enrollment"<?php echo $row->tip_task == 'pre_enrollment' ? ' selected="selected"' : ''?>><?php echo _JLMS_TIPS_PRE_ENROLLMENT_QUESTIONS; ?></option>
								</select>
							</td>
						</tr>
						<tr>
							<td align="right" width="20%"><?php echo _JLMS_TIPS_TIP_MESSAGE; ?></td>
							<td>
							<?php JLMS_editorArea( 'editor1', $row->tip_message, 'tip_message', '100%;', '250', '40', '20' ) ; ?>
							</td>
						</tr>
					</table>
					<br />
				</td>
			</tr>
		</table>
		</form>
		</fieldset>
		</div>
</td></tr></table>
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
		<input type="hidden" name="task" value="" />		
		<?php
	}
	##########################################################################
	###	--- ---  USER ROLES 	--- --- ###
	##########################################################################

	function JLMS_showRolesList( &$roles, $option, &$lists) {
?>
<form action="index.php" method="post" name="adminForm">
<table width="100%" >
	<tr>
		<td valign="top" width="220">
		<div>
			<?php echo joomla_lms_adm_html::JLMS_menu();?>
		</div>
		</td>
		<td valign="top">		
		<?php if (!class_exists('JToolBarHelper')) { ?>
		<table class="adminheading" cellpadding="0" cellspacing="0" border="0">
		<tr>
			<th class="categories">
			<?php echo _JOOMLMS_COMP_NAME;?>: <small><?php echo _JLMS_ROLES_MANAGEMENT; ?></small>
			</th>
		</tr>
		</table>
		<?php } ?>		
		<table width="100%" border="0">
			<tr>
				<td valign="top">
					<table class="adminlist">
					<thead>
						<tr>
							<th width="20" align="center">#</th>
							<th width="20" align="center">&nbsp;</th>
							<th class="title" colspan="2"><?php echo _JLMS_ROLES_ROLE_TYPE_NAME; ?></th>
							<th width="32" align="left"><?php echo _JLMS_ROLES_ID; ?></th>
						</tr>
					</thead>
					<tbody>
					<?php
					$k = 0;
					$latest_rtype = 0;
					$roles_sorted = array();
					$sort_order = array(4,2,5,1,3);
					foreach ($sort_order as $so) {
						foreach ($roles as $rr) {
							if ($rr->roletype_id == $so) {
								$rr1 = new stdClass();
								$rr1->roletype_id = $rr->roletype_id;
								$rr1->lms_usertype = $rr->lms_usertype;
								$rr1->id = $rr->id;
								$roles_sorted[] = $rr;
							}
						}
					}
					for ($i=0, $n=count($roles_sorted); $i < $n; $i++) {
						$row = $roles_sorted[$i];
						if ($row->roletype_id) {
							if ($row->roletype_id == 2 || $row->roletype_id == 4 || $row->roletype_id == 5) {
								$checked = mosHTML::idBox( $i, $row->id);
							} else {
								$checked = '&nbsp;';
							}
							if ($row->roletype_id != $latest_rtype) {
								$latest_rtype = $row->roletype_id;
								echo '<tr class="row'.$k.'">';
								echo '<td align="center">&nbsp;</td><td align="center">&nbsp;</td><td colspan="3"><b>';
								switch ($latest_rtype) {
									case 1: echo _JLMS_ROLES_LEARNER_ROLES; break;
									case 2: echo _JLMS_ROLES_TEACHER_ROLES; break;
									case 3: echo _JLMS_ROLES_STAFF_ROLES; break;
									case 4: echo _JLMS_ROLES_ADMIN_ROLES; break;
									case 5: echo _JLMS_ROLES_ASSISTANT_ROLES; break;
									default: echo _JLMS_ROLES_UNKNOWN_ROLES; break;
								}
								echo '</b></td></tr>';
								$k = 1 - $k;
							}
							$aaa = 1;
							if (!isset($roles[$i+1]) || (isset($roles[$i+1]) && $roles[$i+1]->roletype_id != $latest_rtype)) {
								$aaa = 2;
							} ?>
						<tr class="<?php echo "row$k"; ?>">
							<td align="center"><?php echo ($i + 1);?></td>
							<td align="center"><?php echo $checked;?></td>
							<td align="center" width="16"><img src="<?php echo JURI::root()."components/com_joomla_lms/lms_images/tree/sub".$aaa.".gif";?>" /></td>
							<td><?php echo $row->lms_usertype;?></td>
							<td><?php echo $row->id;?></td>
						</tr>
						<?php
						}
						$k = 1 - $k;
					}?>
					</tbody>
					</table>
				</td>
			</tr>
		</table>		
	</td></tr></table>
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="task" value="roles" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0" />		
		</form>
		<?php
	}

	function jlms_editRole( &$row, &$lists, $option ) {
		?>
		<script language="javascript" type="text/javascript">
		<!--
		function submitbutton(pressbutton) {
			var form = document.adminForm;

			if (pressbutton == 'cancel_role') {
				submitform( pressbutton );
				return;
			}
			var sel_value = form.roletype_id.options[form.roletype_id.selectedIndex].value;
			var role_name = form.lms_usertype.value;
			if (sel_value == 0 || sel_value == '0') {
				alert('<?php echo _JLMS_ROLES_MSG_SELECT_ROLE_TYPE; ?>')
			} else if (!role_name) {
				alert('<?php echo _JLMS_ROLES_MSG_ENTER_ROLE_NAME; ?>')
			} else {
				submitform( pressbutton );
			}
		}
		
		<?php if( JLMS_J16version() ) { ?>
		Joomla.submitbutton = submitbutton;
		<?php } ?>
		//-->
		</script>

<form action="index.php" method="post" name="adminForm">
<table width="100%" >
	<tr>
		<td valign="top" width="220">
		<div>
			<?php echo joomla_lms_adm_html::JLMS_menu();?>
		</div>
		</td>
		<td valign="top">
		<div class="width-100">
		<fieldset class="adminform">
	<?php if (!class_exists('JToolBarHelper')) { ?>
		<table class="adminheading">
		<tr>
			<th class="user">
			<?php echo _JOOMLMS_COMP_NAME;?>:
			<small>
			<?php echo $row->id ? _JLMS_ROLES_EDIT_USER_ROLE : _JLMS_ROLES_NEW_USER_ROLE;?>
			</small>
			</th>
		</tr>
		</table>
	<?php } ?>		
		<table width="100%" border="0">
			<tr>
				<td valign="top">
					<table width="100%" >
						<tr>
							<th colspan="2"><?php echo _JLMS_ROLES_USER_ROLE_DETAILS; ?></th>
						<tr>
						<tr>
							<td align="right" width="20%"><?php echo _JLMS_ROLES_EDIT_ROLE_NAME; ?></td>
							<td><input type="text" name="lms_usertype" class="text_area" style="width:266px;" value="<?php echo $row->lms_usertype;?>" /></td>
						</tr>
						<tr>
							<td align="right" width="20%"><?php echo _JLMS_ROLES_EDIT_ROLE_TYPE; ?></td>
							<td><?php echo $lists['role_type'];?></td>
						</tr>
						<tr>
							<td colspan="2"><?php echo _JLMS_ROLES_EDIT_ROLE_DESCRIPTION; ?></td>
						</tr>
					</table>
					<br />
				</td>
			</tr>
		</table>
		</fieldset>
		</div>
</td></tr></table>
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
		<input type="hidden" name="task" value="" />		
		</form>
		<?php
	}



	##########################################################################
	###	--- ---  CERTIFICATES 	--- --- ###
	##########################################################################


	function JLMS_showCertificatesList( &$rows, &$pageNav, $option, &$lists ) {
?>
<form action="index.php" method="post" name="adminForm">
<table width="100%" >
	<tr>
		<td valign="top" width="220">
		<div>
			<?php echo joomla_lms_adm_html::JLMS_menu();?>
		</div>
		</td>
		<td valign="top" align="right">		
		<?php if (!class_exists('JToolBarHelper')) { ?>
		<table class="adminheading" cellpadding="0" cellspacing="0" border="0" style="width: 30%;">
		<tr>
			<th class="categories">
			<?php echo _JOOMLMS_COMP_NAME;?>: <small><?php echo _JLMS_CERTS_LIST; ?></small>
			</th>
			<td width="right">
				<table class="adminlist" ><tr class="row1">			
					<td align="left" width="100%">&nbsp;</td>		
					<td><?php echo _JLMS_CERTS_FILTER; ?></td>
					<td><input name="filt_crtf" class="text_area" size="10" value="<?php echo $lists['jlms_crtfs'];?>" /></td>
					<td><?php echo $lists['jlms_users'];?></td>
					<td><?php echo $lists['jlms_courses'];?></td>
				</tr></table>
			</td>
		</tr>
		</table>
		<?php } else {
			JToolBarHelper::title( _JOOMLMS_COMP_NAME.': '._JLMS_CERTS_LIST );
		?>		
		<table cellpadding="0" cellspacing="0" border="0" class="adminlist" style="width: 30%;">
		<tr class="row1">
			<td align="left" width="100%">&nbsp;</td>			
			<td><?php echo _JLMS_CERTS_FILTER; ?></td>
			<td><input name="filt_crtf" class="text_area" size="10" value="<?php echo $lists['jlms_crtfs'];?>" /></td>
			<td><?php echo $lists['jlms_users'];?></td>
			<td><?php echo $lists['jlms_courses'];?></td>
		</tr>
		</table>
		<?php } ?>		
		<table width="100%" border="0">
			<tr>
				<td valign="top">
					<table class="adminlist">
					<thead>
						<tr>
							<th width="20" align="center">#</th>
							<th class="title"><?php echo _JLMS_CERTS_SN; ?></th>
							<th class="title"><?php echo _JLMS_CERTS_DATE; ?></th>
							<th class="title"><?php echo _JLMS_CERTS_USER; ?></th>
							<th class="title"><?php echo _JLMS_CERTS_COURSE_NAME; ?></th>
							<th class="title"><?php echo _JLMS_CERTS_QUIZ_NAME; ?></th>
							<th class="title"><?php echo _JLMS_CERTS_LAST_PRINTED; ?></th>
							<th class="title" width="20"><?php echo _JLMS_CERTS_TYPE; ?></th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<td colspan="8">
							<?php echo $pageNav->getListFooter(); ?>
							</td>
						</tr>
					</tfoot>
					<tbody>
					<?php
					$k = 0;
					for ($i=0, $n=count($rows); $i < $n; $i++) {
						$row = $rows[$i];?>
						<tr class="<?php echo "row$k"; ?>">
							<td align="center"><?php echo $pageNav->rowNumber( $i );?></td>
							<td><?php echo $row->uniq_id;?></td>
							<td><?php echo mosFormatDate($row->crtf_date, '%Y-%m-%d');?></td>
							<td><?php
							$usually = true;
							if (!$row->cur_user) {
								echo "<span style='color:red'>"._JLMS_CERTS_USER_REMOVED."</span><br />";
								$usually = false;
							} elseif ($row->cur_name != $row->name || $row->cur_username != $row->username) {
								echo "<span style='color:green'>$row->cur_name ($row->cur_username)</span><br />";
								$usually = false;
							}
							if (!$usually) {
								echo _JLMS_CERTS_PRINTED_AS." <span style='color:red'>";
							}
							echo $row->name." (".$row->username.")";
							if (!$usually) {
								echo "</span>";
								} ?>
							</td>
							<td><?php
							$usually = true;
							if (!$row->course_name) {
								$row->course_name = $row->cur_course_name;
							}
							if (!$row->cur_course_name) {
								echo "<span style='color:red'>"._JLMS_CERTS_REMOVED."</span><br />";
								$usually = false;
							} elseif ($row->cur_course_name != $row->course_name) {
								echo "<span style='color:green'>$row->cur_course_name</span><br />";
								$usually = false;
							}
							if ($row->course_name) {
								if (!$usually) {
									echo _JLMS_CERTS_OLD_NAME." <span style='color:red'>";
								}
								echo $row->course_name;
								if (!$usually) {
									echo "</span>";
								}
							} ?>
							</td>
							<td>
							<?php
							if (!$row->quiz_id) {
								echo '-';
							} else {
								$usually = true;
								if (!$row->quiz_name) {
									$row->quiz_name = $row->cur_quiz_name;
								}
								if (!$row->cur_quiz_name) {
									echo "<span style='color:red'>"._JLMS_CERTS_REMOVED."</span><br />";
									$usually = false;
								} elseif ($row->cur_quiz_name != $row->quiz_name) {
									echo "<span style='color:green'>$row->cur_quiz_name</span><br />";
									$usually = false;
								}
								if ($row->quiz_name) {
									if (!$usually) {
										echo _JLMS_CERTS_OLD_NAME." <span style='color:red'>";
									}
									echo $row->quiz_name;
									if (!$usually) {
										echo "</span>";
									} 
								}
							}
							?>
							</td>
							<td><?php echo mosFormatDate($row->last_printed, '%Y-%m-%d');?></td>
							<td><?php $timg = $row->quiz_id ? 'tlb_quiz.png':'tlb_courses.png'; echo "<img width='16' height='16' border='0' src='".JURI::root()."components/com_joomla_lms/lms_images/toolbar/$timg' alt='$timg' />";?></td>
						</tr>
						<?php
						$k = 1 - $k;
					}?>
					</tbody>
					</table>
				</td>
			</tr>
		</table>
	</td></tr></table>	
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="task" value="certificates" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0" />		
		</form>
		<?php
	}

	##########################################################################
	###	--- ---  CSV Operations 	--- --- ###
	##########################################################################

	function jlms_showOperation( $type, $option, &$lists ) {
		global $JLMS_CONFIG;
		$show_groups = 1;
		if ($JLMS_CONFIG->get('use_global_groups', 1)) {
			$show_groups = 1;
		}
		$tabs = new mosTabs(1);
		$course_id_i = mosGetParam($_REQUEST,'course_id_i', 0);
		?>
		<script language="javascript" type="text/javascript">
		<!--
		function submitbutton(pressbutton) {
			var form = document.adminForm;
			// do field validation
			if (pressbutton == 'csv_do_import' && form.csv_file_import.value == '') {
				alert('<?php echo _JLMS_CSV_MSG_CHOOSE_CSV_FILE; ?>');
				return;
			}
			else if (pressbutton == 'csv_do_delete' && form.csv_file_delete.value == '') {
				alert('<?php echo _JLMS_CSV_MSG_CHOOSE_CSV_FILE; ?>');
				return;
			}
			<?php if( JLMS_J16version() ) { ?>
			Joomla.submitbutton( pressbutton );
			<?php } else { ?>
			submitform( pressbutton );
			<?php } ?>
		}		
		//-->
		</script>
		<form action="index.php" method="post" name="adminForm" enctype="multipart/form-data">		
		<table width="100%">
		<tr>
		<td width="220" valign="top">
		<div>
			<?php echo joomla_lms_adm_html::JLMS_menu();?>
		</div>
		</td>
		<td valign="top">
		<?php if (!class_exists('JToolBarHelper')) {?>
		<table class="adminheading">
		<tr>
			<th class="user"><?php echo _JOOMLMS_COMP_NAME;?>:
				<small>
					<?php echo _JLMS_CSV_OPERATIONS; ?>
				</small>
			</th>
		</tr>
		</table>
		<?php } ?>
		<table width="100%" border="0">
			<tr>
				<td valign="top" >
				<?php
				$tabs->startPane("csvOperationPane");
				/////////////////////////////////////////////////////////////
				//	CSV Import
				$tabs->startTab( _JLMS_CSV_IMPORT, "import-page" );
				//TODO: check if administrator/images/download_f2.png exists in Joomla15/16
				//TODO: check if administrator/images/upload_f2.png exists in Joomla15/16
				//TODO: check if administrator/images/delete_f2.png exists in Joomla15/16
								
				if( JLMS_J16version() ) {
					$app = & JFactory::getApplication(); 
					$img_upload = "<div class=\"toolbar-list\"><span class=\"icon-32-upload\"> </span></div>"; 
					$img_download = "<div class=\"toolbar-list\"><span class=\"icon-32-export\"> </span></div>";
					$img_delete = "<div class=\"toolbar-list\"><span class=\"icon-32-delete\"> </span></div>";
				} else {
					$img_upload = "<img src=\"".JURI::root()."administrator/images/upload_f2.png\"  alt=\""._JLMS_CSV_IMPORT."\" name=\"del_class\" title=\""._JLMS_CSV_IMPORT."\" align=\"middle\" border=\"0\"  style=\"vertical-align:middle\" />"; 
					$img_download = "<img src=\"".JURI::root()."administrator/images/download_f2.png\"  alt=\""._JLMS_CSV_EXPORT."\" name=\"del_class\" title=\""._JLMS_CSV_EXPORT."\" align=\"middle\" border=\"0\"  style=\"vertical-align:middle\" />&nbsp;"._JLMS_CSV_EXPORT;
					$img_delete = "<img src=\"".JURI::root()."administrator/images/delete_f2.png\"  alt=\""._JLMS_CSV_DELETE."\" name=\"del_class\" title=\""._JLMS_CSV_DELETE."\" align=\"middle\" border=\"0\"  style=\"vertical-align:middle\" />&nbsp;"._JLMS_CSV_DELETE;					
				} 
				 
				?>
				<div class="width-100">
				<fieldset class="adminform">
				<table width="100%">
					<tr><td width="220"><?php echo _JLMS_CSV_IMPORT_USERS_INTO; ?> *</td><td><?php echo $lists['jlms_courses_imp'];?></td>
					<td rowspan="2" width="52" style=" text-align:right;vertical-align:top "><a class="toolbar" href="javascript:submitbutton('csv_do_import');">							
							<?php echo $img_download; ?>&nbsp;<?php echo _JLMS_CSV_IMPORT; ?></a>
						</td>
					</tr>
					<?php if ( (($course_id_i != -3) && ($course_id_i != 0) ) || ($JLMS_CONFIG->get('use_global_groups', 1)) ) {?>
					<tr<?php echo $show_groups?'':' style="display:none"';?>>
						<td><?php echo _JLMS_CSV_SELECT_USERGROUP; ?></td>
						<td><?php echo $lists['jlms_groups_import'];?></td>
					</tr>
					<?php }
					else {?>
						<tr><td colspan="3"><input type="hidden" name="joomla_user_export" value="1" ></td></tr>
					<?php }?>
					<tr><td valign="middle" style="vertical-align:middle " ><?php echo _JLMS_CSV_SELECT_FILE; ?> *</td>
						<td colspan="2">
							<input size="60" class="inputbox" type="file" name="csv_file_import" />
						</td>						
					</tr>
				</table>
				</fieldset>
				</div>
				<?php
				$tabs->endTab();
				/////////////////////////////////////////////////////////////
				//	CSV Export
				$tabs->startTab( _JLMS_CSV_CSV_EXPORT,"export-page");
				?>
				<div class="width-100">
				<fieldset class="adminform">
				<table width="100%">
					<tr><td style="vertical-align:top" width="220"><?php echo _JLMS_CSV_EXPORT_USERS_FROM; ?> *</td>
						<td><?php echo $lists['jlms_courses_exp'];?></td>
						<td width="52" rowspan="2" style=" text-align:right;vertical-align:top "><a class="toolbar" href="javascript:submitbutton('csv_do_export');">
						<?php echo $img_upload; ?>&nbsp;<?php echo _JLMS_CSV_EXPORT; ?></a>
						</td>
					</tr>
					<?php if ($show_groups) { ?>
					<tr><td style="vertical-align:top "><?php echo _JLMS_CSV_USER_GROUPS; ?></td>
						<td colspan="2"><?php echo $lists['jlms_groups_export'];?></td>
					</tr>
					<?php } ?>
					<?php /*
					<tr>
						<td colspan="3" style="vertical-align:middle "><?php echo _JLMS_CSV_OPTIONS; ?></td>
					</tr>
					<tr>
						<td colspan="3" style="vertical-align:middle "><input type="radio" value="0" name="sel_exp_type" checked="checked" id = "export_from_selected_groups" /><label for="export_from_selected_groups"><?php echo _JLMS_CSV_EXPORT_FROM_GROUPS_COURSES; ?></label></td>
					</tr>
					<tr>
						<td colspan="3" style="vertical-align:middle "><input type="radio" value="1" name="sel_exp_type" id="export_all_joomla_users" /><label for="export_all_joomla_users"><?php echo _JLMS_CSV_EXPORT_ALL_JOOMLA_USERS; ?></label></td>
					</tr>
					*/ ?>
				</table>
				</fieldset>
				</div>
				<?php
				$tabs->endTab();
				/////////////////////////////////////////////////////////////
				//	CSV Delete
				$tabs->startTab( _JLMS_CSV_CSV_DELETE, "delete-page");
				?>
				<div class="width-100">
				<fieldset class="adminform">
				<table width="100%">
					<tr><td style="vertical-align:top" width="220"><?php echo _JLMS_CSV_DELETE_USERS_FROM; ?> *</td>
						<td><?php echo $lists['jlms_courses_del'];?></td>
						<td width="52" rowspan="2" style=" text-align:right;vertical-align:top "><a class="toolbar" href="javascript:submitbutton('csv_do_delete');">
						<?php echo $img_delete; ?></a>&nbsp;<?php echo _JLMS_CSV_DELETE; ?></a>
						</td>
					</tr>
					<?php if ($show_groups && $JLMS_CONFIG->get('use_global_groups', 1)) { ?>
					<tr><td style="vertical-align:top "><?php echo _JLMS_CSV_SELECT_USERGROUP; ?></td>
						<td><?php echo $lists['jlms_groups_delete'];?></td>
					</tr>
					<?php } ?>
					<tr><td valign="middle" style="vertical-align:middle " ><?php echo _JLMS_CSV_SELECT_FILE; ?> *</td>
						<td>
							<input size="60" class="inputbox" type="file" name="csv_file_delete" />
						</td>
					</tr>
				</table>
				</fieldset>
				</div>
				<?php
				$tabs->endTab();
				$tabs->endPane();
				if ($type != -1) {
					if (class_exists('JToolBarHelper')) { ?>
				<script language="javascript" type="text/javascript"><!--
				<?php if ($type == 2) { ?>
					window.addEvent('domready', function(){ $('delete-page').fireEvent('click'); });
				<?php } elseif ($type == 1) { ?>
					window.addEvent('domready', function(){ $('export-page').fireEvent('click'); });
				<?php } else { ?>
					window.addEvent('domready', function(){ $('import-page').fireEvent('click'); });
				<?php } ?>
				//-->
				</script>
					<?php } else { ?>
				<script language="javascript" type="text/javascript"><!--
					tabPane1.setSelectedIndex(<?php echo $type;?>)
				//-->
				</script>
					<?php } ?>
				<?php }?>
				</td>
			</tr>
		</table>
</td></tr></table>

		<input type="hidden" name="option" value="<?php echo $option; ?>"/>
		<input type="hidden" name="view_type" value="<?php echo $type; ?>"/>
	  	<input type="hidden" name="boxchecked" value=""/>
		<input type="hidden" name="task" value=""/>		
		</form>
	<?php
	}

	function confirm_delUsers( $del_users, $lists, $option, $sel_courses, $del_type, $courses_ids, $group_id, $courses_names = '', $group_name = '' ) {
		//global $Itemid;
		?>
		<form action="index.php" method="post" name="adminForm">		
		<table width="100%">
		<tr>
		<td width="220" valign="top">
		<div>
			<?php echo joomla_lms_adm_html::JLMS_menu();?>
		</div>
		</td>
		<td valign="top">	
		<?php if (!class_exists('JToolBarHelper')) {?>
		<table class="adminheading">
		<tr>
			<th class="user"><?php echo _JOOMLMS_COMP_NAME;?>:
				<small>
					<?php echo _JLMS_CSV_CONFIRM_USER_DELETION; ?>
				</small>
			</th>
		</tr>
		</table>
		<?php } ?>
		<div style="width:100%">
<?php
if ($del_type == 1) {
	echo _JLMS_CSV_MSG_DEL_USER_TYPE1;
} elseif ($del_type == 2) {
	echo _JLMS_CSV_MSG_DEL_USER_TYPE2;
	echo _JLMS_CSV_SELECTED_USERGROUP.$group_name;
} else {
	echo str_replace( '{and_usergroup}', ($group_name ? _JLMS_CSV_AND_USERGROUP : ''), _JLMS_CSV_MSG_DEL_USER_TYPE3 );
	echo _JLMS_CSV_SELECTED_COURSES.$courses_names.'<br />';
	echo $group_name ? (_JLMS_CSV_SELECTED_USERGROUP.$group_name) : '';
}
?>
		</div>			
				<table width="100%" cellpadding="0" cellspacing="0" border="0" class="adminlist">
					<tr>
						<th width="20px" align="center">#</th>
						<th width="20px" align="center"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($del_users); ?>);" /></th>
						<th align="left"><?php echo _JLMS_CSV_NAME; ?></th>
						<th align="left"><?php echo _JLMS_CSV_USERNAME; ?></th>
						<th align="left"><?php echo _JLMS_CSV_EMAIL; ?></th>
					</tr>
				<?php
				$k = 0;
				for ($i=0, $n=count($del_users); $i < $n; $i++) {
					$row = $del_users[$i];
					$checked = '<input type="checkbox" id="cb'.$i.'" name="cid[]" value="'.$row->id.'" onclick="isChecked(this.checked);" checked />';
	 				?>
					<tr class="<?php echo "row$k"; ?>">
						<td align="center"><?php echo ( $i + 1 ); ?></td>
						<td><?php echo $checked; ?></td>
						<td align="left"><?php echo $row->name;?></td>
						<td align="left"><?php echo $row->username;?></td>
						<td><?php echo $row->email;?></td>
					</tr>
					<?php
					$k = 1 - $k;
				}?>
				</table>				
				</td></tr></table>

					<input type="hidden" name="courses_ids" value="<?php echo $courses_ids;?>" />
					<input type="hidden" name="sel_courses" value="<?php echo $sel_courses;?>" />
					<input type="hidden" name="del_type" value="<?php echo $del_type;?>" />
					<input type="hidden" name="option" value="<?php echo $option;?>" />
					<input type="hidden" name="task" value="csv_do_delete_yes" />
					<input type="hidden" name="boxchecked" value="<?php echo count($del_users);?>" />
					<input type="hidden" name="back_to" value="csv_delete"/>
					<input type="hidden" name="group_id" value="<?php echo $group_id;?>"/>				
			</form>
	<?php
	}

	function showLog( $log, $option, $course_id, $group_id, $title, $back_to ) {
		global $Itemid;
		?>
		<form action="index.php" method="post" name="adminForm">		
		<table width="100%">
		<tr>
		<td width="220" valign="top">
		<div>
			<?php echo joomla_lms_adm_html::JLMS_menu();?>
		</div>
		</td>
		<td valign="top">		
		<?php if (!class_exists('JToolBarHelper')) {?>
		<table class="adminheading">
		<tr>
			<th class="user"><?php echo _JOOMLMS_COMP_NAME;?>:
				<small>
					<?php echo $title;?>
				</small>
			</th>
		</tr>
		</table>
		<?php } ?>	
		<table width="100%" cellpadding="0" cellspacing="0" border="0" class="contentpane">
			<tr>
				<td width="100%">
				<table width="100%" cellpadding="0" cellspacing="0" border="0" class="adminlist">
					<tr>
						<th align="left" width="60%"><?php echo _JLMS_CSV_USER_INFORMATION; ?></th>
						<th align="left"><?php echo _JLMS_CSV_RESULT; ?></th>
					</tr>
					<?php
					$k = 0;
					for ($i=0, $n=count($log); $i < $n; $i++) {
						$user_log = $log[$i];
						?>
						<tr class="<?php echo "row$k"; ?>">
							<td><?php echo $user_log->userinfo;?></td>
							<td><?php echo $user_log->result;?></td>
						</tr>
						<?php
						$k = 1 - $k;
					} ?>
				</table>
				</td>
			</tr>
		</table>	
		</td></tr></table>

		<input type="hidden" name="option" value="<?php echo $option; ?>"/>
		<input type="hidden" name="back_to" value="<?php echo $back_to; ?>"/>
	  	<input type="hidden" name="boxchecked" value=""/>
		<input type="hidden" name="task" value="csv_back_to"/>		
		</form>
	<?php
	}

	##########################################################################
	###	--- ---  SUBSCRIPTIONS 	--- --- ###
	##########################################################################

	function jlms_showCountriesList( &$rows, &$lists, &$row, &$pageNav, $option ) {
		global $my,$JLMS_CONFIG;

		JHTML::_('behavior.tooltip');
		?>
		<form action="index.php" method="post" name="adminForm">		
		<table width="100%" >
			<tr>
				<td valign="top" width="220">
				<div>
					<?php joomla_lms_adm_html::JLMS_menu();?>
				</div>
				</td>
				<td valign="top">			
				<?php if (!class_exists('JToolBarHelper')) { ?>
					<table class="adminheading">
						<tr>
							<th valign="middle"><?php echo _JOOMLMS_COMP_NAME;?>:
								<small>
									<?php echo _JLMS_SUBS_COUNTRIES_TAXES_LIST; ?>
								</small>
							</th>
							<td width="right">
								<div class="current">
								<table  style="margin: 0;" class="adminlist">
									<tr class="row1">
										<td nowrap="nowrap">
											<?php echo _JLMS_SUBS_ENABLE_TAX_COUNTING; ?>
										</td>
										<td nowrap="nowrap">																		
										<fieldset class="radio" style="background-color: #CCCCCC;">
											<?php echo $lists['enabletax'];?>
										</fieldset>										
										</td>
									</tr>
									<tr class="row1">
										<td nowrap="nowrap">
											<?php echo _JLMS_SUBS_GET_COUNTRY_INFOFMATION; ?>
										</td>
										<td nowrap="nowrap">
											<?php echo $lists['get_country_info'];?>
										</td>
									</tr>
									<tr class="row1">
										<td nowrap="nowrap">
											<?php echo _JLMS_SUBS_DEFAULT_TAX_TYPE; ?>
										</td>
										<td nowrap="nowrap">
											<select name="default_tax_type" size="2" >
												<option value="1" <?php if ($row->default_tax_type=='' || $row->default_tax_type=='1'){ echo 'selected="selected"'; }?>><?php echo _JLMS_SUBS_PERCENTAGE; ?></option>
												<option value="2" <?php if ($row->default_tax_type=='2'){ echo 'selected="selected"'; }?>><?php echo _JLMS_SUBS_ADDITIONAL; ?></option>
											</select>
										</td>
									</tr>
									<tr class="row1">
										<td nowrap="nowrap">
											<?php echo _JLMS_SUBS_DEFAULT_TAX_AMOUNT; ?>
										</td>
										<td nowrap="nowrap">
											<input class="inputbox" type="text" name="default_tax" size="5" value="<?php echo $row->default_tax; ?>" />
										</td>
									</tr>
									<tr class="row1">
										<td>&nbsp;</td>
										<td nowrap="nowrap">
											<input type="button" name="s_button" class="text_area" value="save" onclick="javascript:document.adminForm.task.value='save_default_tax';document.adminForm.submit();" style="width: 100%;" />
										</td>
									</tr>
								</table>
								</div>
							</td>
						</tr>
					</table>
				<?php 
				} else {
				?>			
					<table align="right">
						<tr>
							<td width="right">
								<div class="current">
								<table  style="margin: 0;" class="adminlist">
									<tr class="row1">
										<td nowrap="nowrap">
											<?php echo _JLMS_SUBS_ENABLE_TAX_COUNTING; ?>
										</td>
										<td nowrap="nowrap" style="padding-left: 5px;">																	
											<fieldset class="radio" style="background-color: #F0F0EE;">
											<?php echo $lists['enabletax'];?>
											</fieldset>											
										</td>
									</tr>
									<tr class="row1">
										<td nowrap="nowrap">
											<?php echo _JLMS_SUBS_GET_COUNTRY_INFORMATION; ?>
										</td>
										<td nowrap="nowrap">
											<?php echo $lists['get_country_info'];?>
										</td>
									</tr>
									<tr class="row1">
										<td nowrap="nowrap">
											Default Tax type:
										</td>
										<td nowrap="nowrap">
											<select name="default_tax_type" size="2" >
												<option value="1" <?php if ($row->default_tax_type=='' || $row->default_tax_type=='1'){ echo 'selected="selected"'; }?>><?php echo _JLMS_SUBS_PERCENTAGE; ?></option>
												<option value="2" <?php if ($row->default_tax_type=='2'){ echo 'selected="selected"'; }?>><?php echo _JLMS_SUBS_ADDITIONAL; ?></option>
											</select>
										</td>
									</tr>
									<tr class="row1">
										<td nowrap="nowrap">
											<?php echo _JLMS_SUBS_DEFAULT_TAX_AMOUNT; ?>
										</td>
										<td nowrap="nowrap">
											<input class="inputbox" type="text" name="default_tax" size="5" value="<?php echo $row->default_tax; ?>" />
										</td>
									</tr>
									<tr class="row1">
										<td>&nbsp;</td>
										<td nowrap="nowrap">
											<input type="button" name="s_button" class="text_area" value="save" onclick="javascript:document.adminForm.task.value='save_default_tax';document.adminForm.submit();" style="width: 100%;" />
										</td>
									</tr>
								</table>
								</div>
							</td>
						</tr>
					</table>
					<div style="clear: both;"><!-- --></div>
				</div>	
				<?php
				}
				?>				
					<table class="adminlist">
					<thead>
						<tr>
							<th width="20">#</th>
							<th width="20" class="title"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>
							<th class="title"><?php echo _JLMS_SUBS_NAME; ?></th>
							<th class="title"><?php echo _JLMS_SUBS_CODE;  ?></th>
							<th class="title" align="center"><?php echo _JLMS_SUBS_PUBLISHED; ?></th>
							<th class="title" align="center"><?php echo _JLMS_SUBS_TAX_TYPE; ?></th>
							<th class="title" align="center" colspan="2"><?php echo _JLMS_SUBS_TAX_AMOUNT; ?></th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<td colspan="8">
								<?php echo $pageNav->getListFooter(); ?>
							</td>
						</tr>
					</tfoot>
					<tbody>
					<?php
					$k = 0;
					for ($i=0, $n=count($rows); $i < $n; $i++) {
						$row = $rows[$i];

						$link 	= 'index.php?option=com_joomla_lms&amp;task=editA_c&amp;hidemainmenu=1&amp;id='. $row->id;

						$img 	= $row->published ? 'tick.png' : 'publish_x.png';
						$task 	= $row->published ? 'unpublish' : 'publish';
						$alt 	= $row->published ? _JLMS_SUBS_PUBLISHED : _JLMS_SUBS_UNPUBLISHED;

						$row->editor = '';
						//$checked 	= mosCommonHTML::CheckedOutProcessing( $row, $i );
						$checked = mosHTML::idBox( $i, $row->id);

						?>
						<tr class="<?php echo "row$k"; ?>">
							<td><?php echo $pageNav->rowNumber( $i ); ?></td>
							<td><?php echo $checked; ?></td>
							<td align="left">
							<?php
							if (!(strpos($row->code,'US-') === false)) $name_to_show = 'US - '.$row->name;
							else $name_to_show = $row->name;
							if ( $row->checked_out && ( $row->checked_out != $my->id ) ) {
								echo $name_to_show;
							} else {
								?>
								<a href="<?php echo $link; ?>" title="<?php echo _JLMS_SUBS_EDIT_COUNTRY; ?>">
								<?php echo $name_to_show; ?>
								</a>
								<?php
							}
							?>
							</td>
							<td align="left"><?php echo $row->code;?></td>
							<td align="center">
								<a href="javascript: void(0);" onclick="return listItemTask('cb<?php echo $i;?>','<?php echo $task.'_c';?>')">
								<img src="<?php echo ADMIN_IMAGES_PATH.$img;?>" border="0" alt="<?php echo $alt; ?>" />
								</a>
							</td>
							<td align="left">
								<?php if ($row->tax_type == '1') echo _JLMS_SUBS_PERCENTAGE;?>
								<?php if ($row->tax_type == '2') echo _JLMS_SUBS_ADDITIONAL;?>
							</td>
							<td align="left"><?php echo $row->tax;?></td>
						</tr>
						<?php
						$k = 1 - $k;
					} ?>
					</tbody>
					</table>				
				</td>
			</tr>
		</table>


		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="task" value="countrieslist" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0" />		
		</form>
		<?php
	}


	function jlms_editCountry( &$row, &$lists, $option, &$params ) {
		?>
		<script language="javascript" type="text/javascript">
		<!--
		function submitbutton(pressbutton) {
			var form = document.adminForm;

			if (pressbutton == 'cancel_c') {
				submitform( pressbutton );
				return;
			}
			// do field validation
			if (form.code.value == '') {
				alert("<?php echo _JLMS_SUBS_MSG_CHOOSE_THE_COUNTRY; ?>");
			}
			else if (form.tax.value == '') {
				alert("<?php echo _JLMS_SUBS_MSG_SPECIFY_TAX_AMOUNT; ?>");
			}
			else if (form.code.value == 'EU' && form.list.value == '') {
				alert("<?php echo _JLMS_SUBS_MSG_FILL_COUNTRIES_LIST; ?>");
			}
			else {
				submitform( pressbutton );
			}
		}
		
		<?php if( JLMS_J16version() ) { ?>
		Joomla.submitbutton = submitbutton;
		<?php } ?>
	
		function change_code() {
			var form = document.adminForm;
			document.getElementById('country_code').innerHTML = form.code.value;
			if (form.code.selectedIndex && form.code.selectedIndex > -1) {
				form.name.value = form.code.options[form.code.selectedIndex].text;
			}
			if (form.code.value == 'EU') {
				document.getElementById('msspro_clist').style.visibility = 'visible';
				document.getElementById('msspro_remark').style.visibility = 'visible';
				document.getElementById('msspro_cremarktext').innerHTML = "<?php echo _JLMS_SUBS_MSG_CHANGE_CODE1; ?>";
				if (form.list.value == '') {
					form.list.value = 'AT,BE,CY,CZ,DK,EE,FI,FR,DE,GR,HU,IE,IT,LV,LT,LU,MT,NL,PL,PT,SK,SL,ES,SE,GB';
				}
			}
			else if (form.code.value == 'US') {
				document.getElementById('msspro_clist').style.visibility = 'hidden';
				document.getElementById('msspro_remark').style.visibility = 'visible';
				document.getElementById('msspro_cremarktext').innerHTML = "<?php echo _JLMS_SUBS_MSG_CHANGE_CODE2; ?>";
			}
			else {
				document.getElementById('msspro_clist').style.visibility = 'hidden';
				document.getElementById('msspro_remark').style.visibility = 'hidden';
			}

			if (form.code.selectedIndex > 2 && form.code.selectedIndex < 62) {
				document.getElementById('msspro_remark').style.visibility = 'visible';
				document.getElementById('msspro_cremarktext').innerHTML = "<?php echo _JLMS_SUBS_MSG_CHANGE_CODE3; ?>";
			}
		}
		//-->
		</script>		
		<form action="index.php" method="post" name="adminForm">		
<table width="100%" >
	<tr>
		<td valign="top" width="220">
		<div>
			<?php joomla_lms_adm_html::JLMS_menu();?>
		</div>
		</td>
		<td valign="top">
		<div class="width-100">
		<fieldset class="adminform">
		<?php if (!class_exists('JToolBarHelper')) { ?>
					<table class="adminheading">
						<tr>
							<th valign="middle"><?php echo _JOOMLMS_COMP_NAME;?>:
								<small>
									<?php echo $row->id ? _JLMS_SUBS_EDIT_COUNTRY : _JLMS_SUBS_NEW_COUNTRY;?>
								</small>
							</th>
						</tr>
					</table>
				<?php } ?>
			<?php //$tabs = new mosTabs(0);?>		
			<table width="100%">
			<tr>
				<td width="60%" valign="top">
					<table width="100%" class="adminlist">
					<tr>
						<th colspan="2"><?php echo _JLMS_SUBS_COUNTRY_DETAILS; ?></th>
					</tr>
					<tr>
						<td width="20%" align="right"><?php echo _JLMS_SUBS_NAME; ?></td>
						<td><input type="hidden" name="name" value="" />
							<select name="code" size="10" onchange="change_code()" >
<option value="EU" <?php if ($row->name=='European union') echo 'selected="selected"';?>>European union</option>
<option value="">------------------------------</option>
<option value="US" <?php if ($row->name=='United States (all states)') echo 'selected="selected"';?>>United States (all states)</option>
<optgroup label="US states:">
<option value="US-AL" <?php if ($row->name=='Alabama') echo 'selected="selected"';?>>Alabama</option>
<option value="US-AK" <?php if ($row->name=='Alaska') echo 'selected="selected"';?>>Alaska</option>
<option value="US-AS" <?php if ($row->name=='American Samoa') echo 'selected="selected"';?>>American Samoa</option>
<option value="US-AZ" <?php if ($row->name=='Arizona') echo 'selected="selected"';?>>Arizona</option>
<option value="US-AR" <?php if ($row->name=='Arkansas') echo 'selected="selected"';?>>Arkansas</option>
<option value="US-CA" <?php if ($row->name=='California') echo 'selected="selected"';?>>California</option>
<option value="US-CO" <?php if ($row->name=='Colorado') echo 'selected="selected"';?>>Colorado</option>
<option value="US-CT" <?php if ($row->name=='Connecticut') echo 'selected="selected"';?>>Connecticut</option>
<option value="US-DE" <?php if ($row->name=='Delaware') echo 'selected="selected"';?>>Delaware</option>
<option value="US-DC" <?php if ($row->name=='District of Columbia') echo 'selected="selected"';?>>District of Columbia</option>
<option value="US-FM" <?php if ($row->name=='Federated States of Micronesia') echo 'selected="selected"';?>>Federated States of Micronesia</option>
<option value="US-FL" <?php if ($row->name=='Florida') echo 'selected="selected"';?>>Florida</option>
<option value="US-GA" <?php if ($row->name=='Georgia') echo 'selected="selected"';?>>Georgia</option>
<option value="US-GU" <?php if ($row->name=='Guam') echo 'selected="selected"';?>>Guam</option>
<option value="US-HI" <?php if ($row->name=='Hawaii') echo 'selected="selected"';?>>Hawaii</option>
<option value="US-ID" <?php if ($row->name=='Idaho') echo 'selected="selected"';?>>Idaho</option>
<option value="US-IL" <?php if ($row->name=='Illinois') echo 'selected="selected"';?>>Illinois</option>
<option value="US-IN" <?php if ($row->name=='Indiana') echo 'selected="selected"';?>>Indiana</option>
<option value="US-IA" <?php if ($row->name=='Iowa') echo 'selected="selected"';?>>Iowa</option>
<option value="US-KS" <?php if ($row->name=='Kansas') echo 'selected="selected"';?>>Kansas</option>
<option value="US-KY" <?php if ($row->name=='Kentucky') echo 'selected="selected"';?>>Kentucky</option>
<option value="US-LA" <?php if ($row->name=='Louisiana') echo 'selected="selected"';?>>Louisiana</option>
<option value="US-ME" <?php if ($row->name=='Maine') echo 'selected="selected"';?>>Maine</option>
<option value="US-MH" <?php if ($row->name=='Marshall Islands') echo 'selected="selected"';?>>Marshall Islands</option>
<option value="US-MD" <?php if ($row->name=='Maryland') echo 'selected="selected"';?>>Maryland</option>
<option value="US-MA" <?php if ($row->name=='Massachusetts') echo 'selected="selected"';?>>Massachusetts</option>
<option value="US-MI" <?php if ($row->name=='Michigan') echo 'selected="selected"';?>>Michigan</option>
<option value="US-MN" <?php if ($row->name=='Minnesota') echo 'selected="selected"';?>>Minnesota</option>
<option value="US-MS" <?php if ($row->name=='Mississippi') echo 'selected="selected"';?>>Mississippi</option>
<option value="US-MO" <?php if ($row->name=='Missouri') echo 'selected="selected"';?>>Missouri</option>
<option value="US-MT" <?php if ($row->name=='Montana') echo 'selected="selected"';?>>Montana</option>
<option value="US-NE" <?php if ($row->name=='Nebraska') echo 'selected="selected"';?>>Nebraska</option>
<option value="US-NV" <?php if ($row->name=='Nevada') echo 'selected="selected"';?>>Nevada</option>
<option value="US-NH" <?php if ($row->name=='New Hampshire') echo 'selected="selected"';?>>New Hampshire</option>
<option value="US-NJ" <?php if ($row->name=='New Jersey') echo 'selected="selected"';?>>New Jersey</option>
<option value="US-NM" <?php if ($row->name=='New Mexico') echo 'selected="selected"';?>>New Mexico</option>
<option value="US-NY" <?php if ($row->name=='New York') echo 'selected="selected"';?>>New York</option>
<option value="US-NC" <?php if ($row->name=='North Carolina') echo 'selected="selected"';?>>North Carolina</option>
<option value="US-ND" <?php if ($row->name=='North Dakota') echo 'selected="selected"';?>>North Dakota</option>
<option value="US-MP" <?php if ($row->name=='Northern Mariana Islands') echo 'selected="selected"';?>>Northern Mariana Islands</option>
<option value="US-OH" <?php if ($row->name=='Ohio') echo 'selected="selected"';?>>Ohio</option>
<option value="US-OK" <?php if ($row->name=='Oklahoma') echo 'selected="selected"';?>>Oklahoma</option>
<option value="US-OR" <?php if ($row->name=='Oregon') echo 'selected="selected"';?>>Oregon</option>
<option value="US-PW" <?php if ($row->name=='Palau') echo 'selected="selected"';?>>Palau</option>
<option value="US-PA" <?php if ($row->name=='Pennsylvania') echo 'selected="selected"';?>>Pennsylvania</option>
<option value="US-PR" <?php if ($row->name=='Puerto Rico') echo 'selected="selected"';?>>Puerto Rico</option>
<option value="US-RI" <?php if ($row->name=='Rhode Island') echo 'selected="selected"';?>>Rhode Island</option>
<option value="US-SC" <?php if ($row->name=='South Carolina') echo 'selected="selected"';?>>South Carolina</option>
<option value="US-SD" <?php if ($row->name=='South Dakota') echo 'selected="selected"';?>>South Dakota</option>
<option value="US-TN" <?php if ($row->name=='Tennessee') echo 'selected="selected"';?>>Tennessee</option>
<option value="US-TX" <?php if ($row->name=='Texas') echo 'selected="selected"';?>>Texas</option>
<option value="US-UT" <?php if ($row->name=='Utah') echo 'selected="selected"';?>>Utah</option>
<option value="US-VT" <?php if ($row->name=='Vermont') echo 'selected="selected"';?>>Vermont</option>
<option value="US-VI" <?php if ($row->name=='Virgin Islands') echo 'selected="selected"';?>>Virgin Islands</option>
<option value="US-VA" <?php if ($row->name=='Virginia') echo 'selected="selected"';?>>Virginia</option>
<option value="US-WA" <?php if ($row->name=='Washington') echo 'selected="selected"';?>>Washington</option>
<option value="US-WV" <?php if ($row->name=='West Virginia') echo 'selected="selected"';?>>West Virginia</option>
<option value="US-WI" <?php if ($row->name=='Wisconsin') echo 'selected="selected"';?>>Wisconsin</option>
<option value="US-WY" <?php if ($row->name=='Wyoming') echo 'selected="selected"';?>>Wyoming</option>
</optgroup>

<option value="">------------------------------</option>
<option value="AF" <?php if ($row->name=='Afghanistan') echo 'selected="selected"';?>>Afghanistan</option>
<option value="AL" <?php if ($row->name=='Albania') echo 'selected="selected"';?>>Albania</option>
<option value="DZ" <?php if ($row->name=='Algeria') echo 'selected="selected"';?>>Algeria</option>
<option value="AS" <?php if ($row->name=='American Samoa') echo 'selected="selected"';?>>American Samoa</option>
<option value="AD" <?php if ($row->name=='Andorra') echo 'selected="selected"';?>>Andorra</option>
<option value="AO" <?php if ($row->name=='Angola') echo 'selected="selected"';?>>Angola</option>
<option value="AI" <?php if ($row->name=='Anguilla') echo 'selected="selected"';?>>Anguilla</option>
<option value="AQ" <?php if ($row->name=='Antarctica') echo 'selected="selected"';?>>Antarctica</option>
<option value="AG" <?php if ($row->name=='Antigua and Barbuda') echo 'selected="selected"';?>>Antigua and Barbuda</option>
<option value="AR" <?php if ($row->name=='Argentina') echo 'selected="selected"';?>>Argentina</option>
<option value="AM" <?php if ($row->name=='Armenia') echo 'selected="selected"';?>>Armenia</option>
<option value="AW" <?php if ($row->name=='Aruba') echo 'selected="selected"';?>>Aruba</option>
<option value="AU" <?php if ($row->name=='Australia') echo 'selected="selected"';?>>Australia</option>
<option value="AT" <?php if ($row->name=='Austria') echo 'selected="selected"';?>>Austria</option>
<option value="AZ" <?php if ($row->name=='Azerbaijan') echo 'selected="selected"';?>>Azerbaijan</option>
<option value="BS" <?php if ($row->name=='Bahamas') echo 'selected="selected"';?>>Bahamas</option>
<option value="BH" <?php if ($row->name=='Bahrain') echo 'selected="selected"';?>>Bahrain</option>
<option value="BD" <?php if ($row->name=='Bangladesh') echo 'selected="selected"';?>>Bangladesh</option>
<option value="BB" <?php if ($row->name=='Barbados') echo 'selected="selected"';?>>Barbados</option>
<option value="BY" <?php if ($row->name=='Belarus') echo 'selected="selected"';?>>Belarus</option>
<option value="BE" <?php if ($row->name=='Belgium') echo 'selected="selected"';?>>Belgium</option>
<option value="BZ" <?php if ($row->name=='Belize') echo 'selected="selected"';?>>Belize</option>
<option value="BJ" <?php if ($row->name=='Benin') echo 'selected="selected"';?>>Benin</option>
<option value="BM" <?php if ($row->name=='Bermuda') echo 'selected="selected"';?>>Bermuda</option>
<option value="BT" <?php if ($row->name=='Bhutan') echo 'selected="selected"';?>>Bhutan</option>
<option value="BO" <?php if ($row->name=='Bolivia') echo 'selected="selected"';?>>Bolivia</option>
<option value="BA" <?php if ($row->name=='Bosnia and Herzegowina') echo 'selected="selected"';?>>Bosnia and Herzegowina</option>
<option value="BW" <?php if ($row->name=='Botswana') echo 'selected="selected"';?>>Botswana</option>
<option value="BV" <?php if ($row->name=='Bouvet Island') echo 'selected="selected"';?>>Bouvet Island</option>
<option value="BR" <?php if ($row->name=='Brazil') echo 'selected="selected"';?>>Brazil</option>
<option value="IO" <?php if ($row->name=='British Indian Ocean Territory') echo 'selected="selected"';?>>British Indian Ocean Territory</option>
<option value="BN" <?php if ($row->name=='Brunei Darussalam') echo 'selected="selected"';?>>Brunei Darussalam</option>
<option value="BG" <?php if ($row->name=='Bulgaria') echo 'selected="selected"';?>>Bulgaria</option>
<option value="BF" <?php if ($row->name=='Burkina Faso') echo 'selected="selected"';?>>Burkina Faso</option>
<option value="BI" <?php if ($row->name=='Burundi') echo 'selected="selected"';?>>Burundi</option>
<option value="KH" <?php if ($row->name=='Cambodia') echo 'selected="selected"';?>>Cambodia</option>
<option value="CM" <?php if ($row->name=='Cameroon') echo 'selected="selected"';?>>Cameroon</option>
<option value="CA" <?php if ($row->name=='Canada') echo 'selected="selected"';?>>Canada</option>
<option value="CV" <?php if ($row->name=='Cape Verde') echo 'selected="selected"';?>>Cape Verde</option>
<option value="KY" <?php if ($row->name=='Cayman Islands') echo 'selected="selected"';?>>Cayman Islands</option>
<option value="CF" <?php if ($row->name=='Central African Republic') echo 'selected="selected"';?>>Central African Republic</option>
<option value="TD" <?php if ($row->name=='Chad') echo 'selected="selected"';?>>Chad</option>
<option value="CL" <?php if ($row->name=='Chile') echo 'selected="selected"';?>>Chile</option>
<option value="CN" <?php if ($row->name=='China') echo 'selected="selected"';?>>China</option>
<option value="CX" <?php if ($row->name=='Christmas Island') echo 'selected="selected"';?>>Christmas Island</option>
<option value="CC" <?php if ($row->name=='Cocos (Keeling) Islands') echo 'selected="selected"';?>>Cocos (Keeling) Islands</option>
<option value="CO" <?php if ($row->name=='Colombia') echo 'selected="selected"';?>>Colombia</option>
<option value="KM" <?php if ($row->name=='Comoros') echo 'selected="selected"';?>>Comoros</option>
<option value="CG" <?php if ($row->name=='Congo') echo 'selected="selected"';?>>Congo</option>
<option value="CD" <?php if ($row->name=='Congo, the Democratic Republic of the') echo 'selected="selected"';?>>Congo, the Democratic Republic of the</option>
<option value="CK" <?php if ($row->name=='Cook Islands') echo 'selected="selected"';?>>Cook Islands</option>
<option value="CR" <?php if ($row->name=='Costa Rica') echo 'selected="selected"';?>>Costa Rica</option>
<option value="CI" <?php if ($row->name=='Cote d&acute;Ivoire') echo 'selected="selected"';?>>Cote d&acute;Ivoire</option>
<option value="HR" <?php if ($row->name=='Croatia (local name: Hrvatska)') echo 'selected="selected"';?>>Croatia (local name: Hrvatska)</option>
<option value="CU" <?php if ($row->name=='Cuba') echo 'selected="selected"';?>>Cuba</option>
<option value="CY" <?php if ($row->name=='Cyprus') echo 'selected="selected"';?>>Cyprus</option>
<option value="CZ" <?php if ($row->name=='Czech Republic') echo 'selected="selected"';?>>Czech Republic</option>
<option value="DK" <?php if ($row->name=='Denmark') echo 'selected="selected"';?>>Denmark</option>
<option value="DJ" <?php if ($row->name=='Djibouti') echo 'selected="selected"';?>>Djibouti</option>
<option value="DM" <?php if ($row->name=='Dominica') echo 'selected="selected"';?>>Dominica</option>
<option value="DO" <?php if ($row->name=='Dominican Republic') echo 'selected="selected"';?>>Dominican Republic</option>
<option value="TP" <?php if ($row->name=='East Timor') echo 'selected="selected"';?>>East Timor</option>
<option value="EC" <?php if ($row->name=='Ecuador') echo 'selected="selected"';?>>Ecuador</option>
<option value="EG" <?php if ($row->name=='Egypt') echo 'selected="selected"';?>>Egypt</option>
<option value="SV" <?php if ($row->name=='El Salvador') echo 'selected="selected"';?>>El Salvador</option>
<option value="EE" <?php if ($row->name=='Estonia') echo 'selected="selected"';?>>Estonia</option>
<option value="FK" <?php if ($row->name=='Falkland Islands (Malvinas)') echo 'selected="selected"';?>>Falkland Islands (Malvinas)</option>
<option value="FO" <?php if ($row->name=='Faroe Islands') echo 'selected="selected"';?>>Faroe Islands</option>
<option value="FJ" <?php if ($row->name=='Fiji') echo 'selected="selected"';?>>Fiji</option>
<option value="FI" <?php if ($row->name=='Finland') echo 'selected="selected"';?>>Finland</option>
<option value="FR" <?php if ($row->name=='France') echo 'selected="selected"';?>>France</option>
<option value="FX" <?php if ($row->name=='France, Metropolitan') echo 'selected="selected"';?>>France, Metropolitan</option>
<option value="GF" <?php if ($row->name=='French Guiana') echo 'selected="selected"';?>>French Guiana</option>
<option value="PF" <?php if ($row->name=='French Polynesia') echo 'selected="selected"';?>>French Polynesia</option>
<option value="TF" <?php if ($row->name=='French Southern Territories') echo 'selected="selected"';?>>French Southern Territories</option>
<option value="GA" <?php if ($row->name=='Gabon') echo 'selected="selected"';?>>Gabon</option>
<option value="GM" <?php if ($row->name=='Gambia') echo 'selected="selected"';?>>Gambia</option>
<option value="GE" <?php if ($row->name=='Georgia') echo 'selected="selected"';?>>Georgia</option>
<option value="DE" <?php if ($row->name=='Germany') echo 'selected="selected"';?>>Germany</option>
<option value="GH" <?php if ($row->name=='Ghana') echo 'selected="selected"';?>>Ghana</option>
<option value="GI" <?php if ($row->name=='Gibraltar') echo 'selected="selected"';?>>Gibraltar</option>
<option value="GR" <?php if ($row->name=='Greece') echo 'selected="selected"';?>>Greece</option>
<option value="GL" <?php if ($row->name=='Greenland') echo 'selected="selected"';?>>Greenland</option>
<option value="GD" <?php if ($row->name=='Grenada') echo 'selected="selected"';?>>Grenada</option>
<option value="GP" <?php if ($row->name=='Guadeloupe') echo 'selected="selected"';?>>Guadeloupe</option>
<option value="GU" <?php if ($row->name=='Guam') echo 'selected="selected"';?>>Guam</option>
<option value="GT" <?php if ($row->name=='Guatemala') echo 'selected="selected"';?>>Guatemala</option>
<option value="GN" <?php if ($row->name=='Guinea') echo 'selected="selected"';?>>Guinea</option>
<option value="GW" <?php if ($row->name=='Guinea-Bissau') echo 'selected="selected"';?>>Guinea-Bissau</option>
<option value="GY" <?php if ($row->name=='Guyana') echo 'selected="selected"';?>>Guyana</option>
<option value="HT" <?php if ($row->name=='Haiti') echo 'selected="selected"';?>>Haiti</option>
<option value="HM" <?php if ($row->name=='Heard and Mc Donald Islands') echo 'selected="selected"';?>>Heard and Mc Donald Islands</option>
<option value="VA" <?php if ($row->name=='Holy see (Vatican City State)') echo 'selected="selected"';?>>Holy see (Vatican City State)</option>
<option value="HN" <?php if ($row->name=='Honduras') echo 'selected="selected"';?>>Honduras</option>
<option value="HK" <?php if ($row->name=='Hong Kong') echo 'selected="selected"';?>>Hong Kong</option>
<option value="HU" <?php if ($row->name=='Hungary') echo 'selected="selected"';?>>Hungary</option>
<option value="IS" <?php if ($row->name=='Iceland') echo 'selected="selected"';?>>Iceland</option>
<option value="IN" <?php if ($row->name=='India') echo 'selected="selected"';?>>India</option>
<option value="ID" <?php if ($row->name=='Indonesia') echo 'selected="selected"';?>>Indonesia</option>
<option value="IR" <?php if ($row->name=='Iran (Islamic Republic of)') echo 'selected="selected"';?>>Iran (Islamic Republic of)</option>
<option value="IQ" <?php if ($row->name=='Iraq') echo 'selected="selected"';?>>Iraq</option>
<option value="IE" <?php if ($row->name=='Ireland') echo 'selected="selected"';?>>Ireland</option>
<option value="IL" <?php if ($row->name=='Israel') echo 'selected="selected"';?>>Israel</option>
<option value="IT" <?php if ($row->name=='Italy') echo 'selected="selected"';?>>Italy</option>
<option value="JM" <?php if ($row->name=='Jamaica') echo 'selected="selected"';?>>Jamaica</option>
<option value="JP" <?php if ($row->name=='Japan') echo 'selected="selected"';?>>Japan</option>
<option value="JO" <?php if ($row->name=='Jordan') echo 'selected="selected"';?>>Jordan</option>
<option value="KZ" <?php if ($row->name=='Kazakhstan') echo 'selected="selected"';?>>Kazakhstan</option>
<option value="KE" <?php if ($row->name=='Kenya') echo 'selected="selected"';?>>Kenya</option>
<option value="KI" <?php if ($row->name=='Kiribati') echo 'selected="selected"';?>>Kiribati</option>
<option value="KP" <?php if ($row->name=='Korea, Democratic People&acute;s Republic of') echo 'selected="selected"';?>>Korea, Democratic People&acute;s Republic of</option>
<option value="KR" <?php if ($row->name=='Korea, Republic of') echo 'selected="selected"';?>>Korea, Republic of</option>
<option value="KW" <?php if ($row->name=='Kuwait') echo 'selected="selected"';?>>Kuwait</option>
<option value="KG" <?php if ($row->name=='Kyrgyzstan') echo 'selected="selected"';?>>Kyrgyzstan</option>
<option value="LA" <?php if ($row->name=='Lao People&acute;s Democratic Republic') echo 'selected="selected"';?>>Lao People&acute;s Democratic Republic</option>
<option value="LV" <?php if ($row->name=='Latvia') echo 'selected="selected"';?>>Latvia</option>
<option value="LB" <?php if ($row->name=='Lebanon') echo 'selected="selected"';?>>Lebanon</option>
<option value="LS" <?php if ($row->name=='Lesotho') echo 'selected="selected"';?>>Lesotho</option>
<option value="LR" <?php if ($row->name=='Liberia') echo 'selected="selected"';?>>Liberia</option>
<option value="LY" <?php if ($row->name=='Libyan Arab Jamahiriya') echo 'selected="selected"';?>>Libyan Arab Jamahiriya</option>
<option value="LI" <?php if ($row->name=='Liechtenstein') echo 'selected="selected"';?>>Liechtenstein</option>
<option value="LT" <?php if ($row->name=='Lithuania') echo 'selected="selected"';?>>Lithuania</option>
<option value="LU" <?php if ($row->name=='Luxembourg') echo 'selected="selected"';?>>Luxembourg</option>
<option value="MO" <?php if ($row->name=='Macau') echo 'selected="selected"';?>>Macau</option>
<option value="MK" <?php if ($row->name=='Macedonia, the former Yugoslav Republic of') echo 'selected="selected"';?>>Macedonia, the former Yugoslav Republic of</option>
<option value="MG" <?php if ($row->name=='Madagascar') echo 'selected="selected"';?>>Madagascar</option>
<option value="MW" <?php if ($row->name=='Malawi') echo 'selected="selected"';?>>Malawi</option>
<option value="MY" <?php if ($row->name=='Malaysia') echo 'selected="selected"';?>>Malaysia</option>
<option value="MV" <?php if ($row->name=='Maldives') echo 'selected="selected"';?>>Maldives</option>
<option value="ML" <?php if ($row->name=='Mali') echo 'selected="selected"';?>>Mali</option>
<option value="MT" <?php if ($row->name=='Malta') echo 'selected="selected"';?>>Malta</option>
<option value="MH" <?php if ($row->name=='Marshall Islands') echo 'selected="selected"';?>>Marshall Islands</option>
<option value="MQ" <?php if ($row->name=='Martinique') echo 'selected="selected"';?>>Martinique</option>
<option value="MR" <?php if ($row->name=='Mauritania') echo 'selected="selected"';?>>Mauritania</option>
<option value="MU" <?php if ($row->name=='Mauritius') echo 'selected="selected"';?>>Mauritius</option>
<option value="YT" <?php if ($row->name=='Mayotte') echo 'selected="selected"';?>>Mayotte</option>
<option value="MX" <?php if ($row->name=='Mexico') echo 'selected="selected"';?>>Mexico</option>
<option value="FM" <?php if ($row->name=='Micronesia, Federated States of') echo 'selected="selected"';?>>Micronesia, Federated States of</option>
<option value="MD" <?php if ($row->name=='Moldova, Republic of') echo 'selected="selected"';?>>Moldova, Republic of</option>
<option value="MC" <?php if ($row->name=='Monaco') echo 'selected="selected"';?>>Monaco</option>
<option value="MN" <?php if ($row->name=='Mongolia') echo 'selected="selected"';?>>Mongolia</option>
<option value="MS" <?php if ($row->name=='Montserrat') echo 'selected="selected"';?>>Montserrat</option>
<option value="MA" <?php if ($row->name=='Morocco') echo 'selected="selected"';?>>Morocco</option>
<option value="MZ" <?php if ($row->name=='Mozambique') echo 'selected="selected"';?>>Mozambique</option>
<option value="MM" <?php if ($row->name=='Myanmar') echo 'selected="selected"';?>>Myanmar</option>
<option value="NA" <?php if ($row->name=='Namibia') echo 'selected="selected"';?>>Namibia</option>
<option value="NR" <?php if ($row->name=='Nauru') echo 'selected="selected"';?>>Nauru</option>
<option value="NP" <?php if ($row->name=='Nepal') echo 'selected="selected"';?>>Nepal</option>
<option value="NL" <?php if ($row->name=='Netherlands') echo 'selected="selected"';?>>Netherlands</option>
<option value="AN" <?php if ($row->name=='Netherlands Antilles') echo 'selected="selected"';?>>Netherlands Antilles</option>
<option value="NC" <?php if ($row->name=='New Caledonia') echo 'selected="selected"';?>>New Caledonia</option>
<option value="NZ" <?php if ($row->name=='New Zealand') echo 'selected="selected"';?>>New Zealand</option>
<option value="NI" <?php if ($row->name=='Nicaragua') echo 'selected="selected"';?>>Nicaragua</option>
<option value="NE" <?php if ($row->name=='Niger') echo 'selected="selected"';?>>Niger</option>
<option value="NG" <?php if ($row->name=='Nigeria') echo 'selected="selected"';?>>Nigeria</option>
<option value="NU" <?php if ($row->name=='Niue') echo 'selected="selected"';?>>Niue</option>
<option value="NF" <?php if ($row->name=='Norfolk Island') echo 'selected="selected"';?>>Norfolk Island</option>
<option value="MP" <?php if ($row->name=='Northern Mariana Islands') echo 'selected="selected"';?>>Northern Mariana Islands</option>
<option value="NO" <?php if ($row->name=='Norway') echo 'selected="selected"';?>>Norway</option>
<option value="OM" <?php if ($row->name=='Oman') echo 'selected="selected"';?>>Oman</option>
<option value="PK" <?php if ($row->name=='Pakistan') echo 'selected="selected"';?>>Pakistan</option>
<option value="PW" <?php if ($row->name=='Palau') echo 'selected="selected"';?>>Palau</option>
<option value="PS" <?php if ($row->name=='Palestinian Territory, occupied') echo 'selected="selected"';?>>Palestinian Territory, occupied</option>
<option value="PA" <?php if ($row->name=='Panama') echo 'selected="selected"';?>>Panama</option>
<option value="PG" <?php if ($row->name=='Papua New Guinea') echo 'selected="selected"';?>>Papua New Guinea</option>
<option value="PY" <?php if ($row->name=='Paraguay') echo 'selected="selected"';?>>Paraguay</option>
<option value="PE" <?php if ($row->name=='Peru') echo 'selected="selected"';?>>Peru</option>
<option value="PH" <?php if ($row->name=='Philippines') echo 'selected="selected"';?>>Philippines</option>
<option value="PN" <?php if ($row->name=='Pitcairn') echo 'selected="selected"';?>>Pitcairn</option>
<option value="PL" <?php if ($row->name=='Poland') echo 'selected="selected"';?>>Poland</option>
<option value="PT" <?php if ($row->name=='Portugal') echo 'selected="selected"';?>>Portugal</option>
<option value="PR" <?php if ($row->name=='Puerto Rico') echo 'selected="selected"';?>>Puerto Rico</option>
<option value="QA" <?php if ($row->name=='Qatar') echo 'selected="selected"';?>>Qatar</option>
<option value="RE" <?php if ($row->name=='Reunion') echo 'selected="selected"';?>>Reunion</option>
<option value="RO" <?php if ($row->name=='Romania') echo 'selected="selected"';?>>Romania</option>
<option value="RU" <?php if ($row->name=='Russian Federation') echo 'selected="selected"';?>>Russian Federation</option>
<option value="RW" <?php if ($row->name=='Rwanda') echo 'selected="selected"';?>>Rwanda</option>
<option value="KN" <?php if ($row->name=='Saint Kitts and Nevis') echo 'selected="selected"';?>>Saint Kitts and Nevis</option>
<option value="LC" <?php if ($row->name=='Saint Lucia') echo 'selected="selected"';?>>Saint Lucia</option>
<option value="VC" <?php if ($row->name=='Saint Vincent and the Grenadines') echo 'selected="selected"';?>>Saint Vincent and the Grenadines</option>
<option value="WS" <?php if ($row->name=='Samoa') echo 'selected="selected"';?>>Samoa</option>
<option value="SM" <?php if ($row->name=='San Marino') echo 'selected="selected"';?>>San Marino</option>
<option value="ST" <?php if ($row->name=='Sao Tome and Principe') echo 'selected="selected"';?>>Sao Tome and Principe</option>
<option value="SA" <?php if ($row->name=='Saudi Arabia') echo 'selected="selected"';?>>Saudi Arabia</option>
<option value="SN" <?php if ($row->name=='Senegal') echo 'selected="selected"';?>>Senegal</option>
<option value="SC" <?php if ($row->name=='Seychelles') echo 'selected="selected"';?>>Seychelles</option>
<option value="SL" <?php if ($row->name=='Sierra Leone') echo 'selected="selected"';?>>Sierra Leone</option>
<option value="SG" <?php if ($row->name=='Singapore') echo 'selected="selected"';?>>Singapore</option>
<option value="SK" <?php if ($row->name=='Slovakia (Slovak Republic)') echo 'selected="selected"';?>>Slovakia (Slovak Republic)</option>
<option value="SI" <?php if ($row->name=='Slovenia') echo 'selected="selected"';?>>Slovenia</option>
<option value="SB" <?php if ($row->name=='Solomon Islands') echo 'selected="selected"';?>>Solomon Islands</option>
<option value="SO" <?php if ($row->name=='Somalia') echo 'selected="selected"';?>>Somalia</option>
<option value="ZA" <?php if ($row->name=='South Africa') echo 'selected="selected"';?>>South Africa</option>
<option value="GS" <?php if ($row->name=='South Georgia and the South Sandwich Islands') echo 'selected="selected"';?>>South Georgia and the South Sandwich Islands</option>
<option value="ES" <?php if ($row->name=='Spain') echo 'selected="selected"';?>>Spain</option>
<option value="LK" <?php if ($row->name=='Sri Lanka') echo 'selected="selected"';?>>Sri Lanka</option>
<option value="SH" <?php if ($row->name=='St. Helena') echo 'selected="selected"';?>>St. Helena</option>
<option value="PM" <?php if ($row->name=='St. Pierre and Miquelon') echo 'selected="selected"';?>>St. Pierre and Miquelon</option>
<option value="SD" <?php if ($row->name=='Sudan') echo 'selected="selected"';?>>Sudan</option>
<option value="SR" <?php if ($row->name=='Suriname') echo 'selected="selected"';?>>Suriname</option>
<option value="SJ" <?php if ($row->name=='Svalbard and Jan Mayen Islands') echo 'selected="selected"';?>>Svalbard and Jan Mayen Islands</option>
<option value="SZ" <?php if ($row->name=='Swaziland') echo 'selected="selected"';?>>Swaziland</option>
<option value="SE" <?php if ($row->name=='Sweden') echo 'selected="selected"';?>>Sweden</option>
<option value="CH" <?php if ($row->name=='Switzerland') echo 'selected="selected"';?>>Switzerland</option>
<option value="SY" <?php if ($row->name=='Syrian Arab Republic') echo 'selected="selected"';?>>Syrian Arab Republic</option>
<option value="TW" <?php if ($row->name=='Taiwan, Republic of China') echo 'selected="selected"';?>>Taiwan, Republic of China</option>
<option value="TJ" <?php if ($row->name=='Tajikistan') echo 'selected="selected"';?>>Tajikistan</option>
<option value="TZ" <?php if ($row->name=='Tanzania, United Republic of') echo 'selected="selected"';?>>Tanzania, United Republic of</option>
<option value="TH" <?php if ($row->name=='Thailand') echo 'selected="selected"';?>>Thailand</option>
<option value="TG" <?php if ($row->name=='Togo') echo 'selected="selected"';?>>Togo</option>
<option value="TK" <?php if ($row->name=='Tokelau') echo 'selected="selected"';?>>Tokelau</option>
<option value="TO" <?php if ($row->name=='Tonga') echo 'selected="selected"';?>>Tonga</option>
<option value="TT" <?php if ($row->name=='Trinidad and Tobago') echo 'selected="selected"';?>>Trinidad and Tobago</option>
<option value="TN" <?php if ($row->name=='Tunisia') echo 'selected="selected"';?>>Tunisia</option>
<option value="TR" <?php if ($row->name=='Turkey') echo 'selected="selected"';?>>Turkey</option>
<option value="TM" <?php if ($row->name=='Turkmenistan') echo 'selected="selected"';?>>Turkmenistan</option>
<option value="TC" <?php if ($row->name=='Turks and Caicos Islands') echo 'selected="selected"';?>>Turks and Caicos Islands</option>
<option value="TV" <?php if ($row->name=='Tuvalu') echo 'selected="selected"';?>>Tuvalu</option>
<option value="UG" <?php if ($row->name=='Uganda') echo 'selected="selected"';?>>Uganda</option>
<option value="UA" <?php if ($row->name=='Ukraine') echo 'selected="selected"';?>>Ukraine</option>
<option value="AE" <?php if ($row->name=='United Arab Emirates') echo 'selected="selected"';?>>United Arab Emirates</option>
<option value="GB" <?php if ($row->name=='United Kingdom') echo 'selected="selected"';?>>United Kingdom</option>
<option value="UM" <?php if ($row->name=='United States minor outlying islands') echo 'selected="selected"';?>>United States minor outlying islands</option>
<option value="UY" <?php if ($row->name=='Uruguay') echo 'selected="selected"';?>>Uruguay</option>
<option value="UZ" <?php if ($row->name=='Uzbekistan') echo 'selected="selected"';?>>Uzbekistan</option>
<option value="VU" <?php if ($row->name=='Vanuatu') echo 'selected="selected"';?>>Vanuatu</option>
<option value="VE" <?php if ($row->name=='Venezuela') echo 'selected="selected"';?>>Venezuela</option>
<option value="VN" <?php if ($row->name=='Viet Nam') echo 'selected="selected"';?>>Viet Nam</option>
<option value="VG" <?php if ($row->name=='Virgin Islands (British)') echo 'selected="selected"';?>>Virgin Islands (British)</option>
<option value="VI" <?php if ($row->name=='Virgin Islands (U.S.)') echo 'selected="selected"';?>>Virgin Islands (U.S.)</option>
<option value="WF" <?php if ($row->name=='Wallis and Futuna Islands') echo 'selected="selected"';?>>Wallis and Futuna Islands</option>
<option value="EH" <?php if ($row->name=='Western Sahara') echo 'selected="selected"';?>>Western Sahara</option>
<option value="YE" <?php if ($row->name=='Yemen') echo 'selected="selected"';?>>Yemen</option>
<option value="YU" <?php if ($row->name=='Yugoslavia') echo 'selected="selected"';?>>Yugoslavia</option>
<option value="ZM" <?php if ($row->name=='Zambia') echo 'selected="selected"';?>>Zambia</option>
<option value="ZW" <?php if ($row->name=='Zimbabwe') echo 'selected="selected"';?>>Zimbabwe</option>
						</select>
					</td>
				</tr>
				<tr>
					<td align="right"><?php echo _JLMS_SUBS_CODE; ?>:</td>
					<td><div id="country_code"><?php echo $row->code; ?></div></td>
				</tr>
				<tr id="msspro_clist" style="visibility:<?php if ($row->code=='EU') echo 'visible'; else echo 'hidden';?>">
					<td align="right"><?php echo _JLMS_SUBS_EU_COUNTRIES_CODES; ?>:</td>
					<td><input class="inputbox" type="text" name="list" size="80" value="<?php echo $row->list; ?>" /></td>
				</tr>
				<tr id="msspro_remark" style="visibility:<?php if ($row->code=='EU' || $row->code=='US') echo 'visible'; else echo 'hidden';?>">
					<td align="right"><?php echo _JLMS_SUBS_REMARK; ?>:</td>
					<td><div id="msspro_cremarktext"></div></td>
				</tr>

				</table>
				<script type="text/javascript" language="javascript">
				<!--
				<?php if (class_exists('JToolBarHelper')) { ?>
				window.addEvent('domready', function(){ change_code(); });
				<?php } else { ?>
				change_code();
				<?php } ?>
				//-->
				</script>				
				<table width="100%" class="adminlist">
				<tr>
					<th colspan="2"><?php echo _JLMS_SUBS_TAX_DETAILS; ?></th>
				</tr>
				<tr>
					<td width="20%"><?php echo _JLMS_SUBS_TAX_TYPE; ?>:</td>
					<td><select name="tax_type" size="2" >
							<option value="1" <?php if ($row->tax_type=='' || $row->tax_type=='1') echo 'selected="selected"';?>><?php echo _JLMS_SUBS_PERCENTAGE; ?></option>
							<option value="2" <?php if ($row->tax_type=='2') echo 'selected="selected"';?>><?php echo _JLMS_SUBS_ADDITIONAL; ?></option>
						</select>
					</td>
				</tr>
				<tr>
					<td align="right"><?php echo _JLMS_SUBS_TAX_AMOUNT; ?>:</td>
					<td><input class="inputbox" type="text" name="tax" size="10" value="<?php echo $row->tax; ?>" /></td>
				</tr>
				</table>
			</td>
			<td width="40%" valign="top">
				<table width="100%" class="adminlist">
				<tr>
					<th colspan="2">
					<?php echo _JLMS_SUBS_PUBLISHING_INFO; ?>
					</th>
				</tr>
				<tr>
					<td valign="top" align="right">
					<?php echo _JLMS_PUBLISHED; ?>:
					</td>
					<td>
					<fieldset class="radio">
					<?php echo $lists['published']; ?>
					</fieldset>
					</td>
				</tr>
				<tr>
					<td colspan="2">&nbsp;

					</td>
				</tr>
				</table>
			</td>
		</tr>
		</table>
		</fieldset>
		</div>
</td></tr></table>
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
		<input type="hidden" name="task" value="" />	
		</form>		
		<?php
	}

	function JLMS_ReNewSubscription_HTML( $rows, $option, $sub_id){
	?>
	<form action="index.php" method="post" name="adminForm">	
	<table width="100%">
		<tr>
			<td valign="top" width="220">
			<div>
				<?php joomla_lms_adm_html::JLMS_menu();?>
			</div>
			</td>
			<td valign="top">		
			<?php if (!class_exists('JToolBarHelper')) { ?>
				<table class="adminheading">
				<tr>
					<th class="user">
					<small>
					Users listed below are already subscribed for this subscription. <br />Do you want to apply new subscription details for them?
					</small>
					</th>
				</tr>
				</table>
			<?php } else {
				$app = & JFactory::getApplication('administrator');
				$msg = _JLMS_SUBS_MSG_APPLY_NEW_SUBSCRIPTION_DETAILS;
				$app->enqueueMessage($msg);
			} ?>			
				<table width="100%" border="0" class="adminlist">
					<tr>
						<td colspan="5"><b><?php echo _JLMS_SUBS_LIST_OF_USERS; ?>:</b></td>
					</tr>
					<tr>
						<th width="15px" align="center">#</th>
						<th width="20px" align="center"><input type="checkbox" name="toggle" checked value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>
						<th align="left"><?php echo _JLMS_USERNAME; ?></th>
						<th align="left"><?php echo _JLMS_NAME; ?></th>
						<th></th>
					</tr>
					<?php
					$k = 0;
					for ($i=0, $n=count($rows); $i < $n; $i++) {
						$row = $rows[$i];
						$checked = joomla_lms_adm_html::jlms_idBox( $i, $row->user_id);?>
						<tr class="<?php echo "row$k"; ?>">
							<td align="center"><?php echo ($i + 1); ?></td>
							<td align="center"><?php echo $checked; ?></td>
							<td align="left">
								<?php echo $row->username;?>
							</td>
							<td align="left">
								<?php echo $row->name;?>
							</td>
							<td></td>
						</tr>
						<?php
						$k = 1 - $k;
					}?>
				</table>				
			</td>
		</tr>
	</table>
	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="<?php echo count($rows);?>" />
	<input type="hidden" name="id" value="<?php echo $sub_id?>" />	
	</form>

	<?php
	}

	function JLMS_createSubscription($rows, $option, $lists, $subs, $courses_in, $plans ){
		global $JLMS_CONFIG;
		mosCommonHTML::loadCalendar();
	?>
	<script type="text/javascript" language="javascript">
	<!--
	function setgood() {
		return true;
	}
	function submitbutton(pressbutton) {
		var form = document.adminForm;
		try {
			form.onsubmit();
		} catch(e) {
			//alert(e);
		}
				
		if (pressbutton == 'subscription_save' || pressbutton == 'subscription_apply') {
			if (form.sub_name.value < 3){
				alert('<?php echo _JLMS_SUBS_MSG_NAME_IS_TOO_SHORT; ?>');
			}else if( getObj('price_a3').style.visibility == 'visible' && parseFloat(form.a3.value) <= 0)
			{
				alert('<?php echo _JLMS_SUBS_MSG_SET_REG_PERIOD_PRICE; ?>');
			}
			else{
				if (form['courses_in_sub[]'].length > 0){
					for (var i=0; i < form['courses_in_sub[]'].length; i++) {
						form['courses_in_sub[]'].options[i].selected = true;
					}
					form.task.value = pressbutton;
					form.submit();
				}else{
					alert ('<?php echo _JLMS_SUBS_MSG_SELECT_COURSE_TO_SUBS; ?>');
				}
			}
		}else{
			form.task.value = pressbutton;
			form.submit();
		}


	}
	
	<?php if( JLMS_J16version() ) { ?>
	Joomla.submitbutton = submitbutton;
	<?php } ?>
	
	function SelectedToList( frmName, srcListName, tgtListName ) {
		var form = eval( 'document.' + frmName );
		var srcList = eval( "form['" + srcListName+"']" );
		var tgtList = eval( "form['" + tgtListName+"']"  );

		var srcLen = srcList.length;
		var tgtLen = tgtList.length;
		var tgt = "x";

		//build array of target items
		for (var i=tgtLen-1; i > -1; i--) {
			tgt += "," + tgtList.options[i].value + ","
		}

		//Pull selected resources and add them to list
		//for (var i=srcLen-1; i > -1; i--) {
		for (var i=0; i < srcLen; i++) {
			if (srcList.options[i].selected && tgt.indexOf( "," + srcList.options[i].value + "," ) == -1) {
				opt = new Option( srcList.options[i].text, srcList.options[i].value );
				tgtList.options[tgtList.length] = opt;
				calculator();
			}
		}
	}
	function delSelectedItems( frmName, srcListName ) {
		var form = eval( 'document.' + frmName );
		var srcList = eval( "form['" + srcListName+"']" );

		var srcLen = srcList.length;

		for (var i=srcLen-1; i > -1; i--) {
			if (srcList.options[i].selected) {
				srcList.options[i] = null;
				calculator();
			}
		}
	}
	function calculator(){
		form = document.adminForm;
		var courses_in = document.adminForm['courses_in_sub[]'];
		var courses_list = document.adminForm.courses_list;
		var srcLen = document.adminForm['courses_in_sub[]'].length;

		subTotal = 0;
//		for (var i=srcLen-1; i > -1; i--) { //old (Max - 24.03.2011)
		for (var i=0;i<srcLen;i++) {
//			test = 'id_'+courses_list.options[i].value; //old (Max - 24.03.2011)
			test = 'id_'+courses_in.options[i].value;
			price = eval('form.'+test+'.value');
			subTotal = parseFloat(subTotal) + parseFloat(price);
		}
		subTotal = (Math.round((subTotal)*100)/100).toFixed(2);
		var discount = parseFloat(document.adminForm.discount.value);
		if (discount < 0 || discount > 100) {discount = 0;}if (!discount) {discount = 0;}
		if (document.adminForm.discount.value == '') {

		} else {
			document.adminForm.discount.value = discount;
		}
		var price = parseFloat(document.adminForm.price.value);
		if (price < 0) {price = 0;}if (!price) {price = 0;}
		if (document.adminForm.price.value == '') {

		} else {
			var price_to_field = price;
			if ((document.adminForm.price.value == ''+price+'.') || (document.adminForm.price.value == ''+price+'.0') || (document.adminForm.price.value == ''+price+'.00') || (document.adminForm.price.value == ''+price+'0') ) {
				price_to_field = document.adminForm.price.value;
			}
			document.adminForm.price.value = price_to_field;
		}

		document.adminForm.sub_total.value = (Math.round(subTotal*100)/100).toFixed(2);


		new_price = price - (price * (discount/100));
		//price.value = (Math.round(new_price*100)/100).toFixed(2);
		document.adminForm.discount_price.value = (Math.round(new_price*100)/100).toFixed(2);
	}

	function getObj(el_id)
	{
		if (document.getElementById)
		{
			return document.getElementById(el_id);
		}
		else if (document.all)
		{
			return document.all[el_id];
		}
		else if (document.layers)
		{
			return document.layers[el_id];
		}
	}

	function check_ms_options() {
		var form = document.adminForm;
		getObj('lms_start_date').style.visibility = 'hidden';
		getObj('lms_end_date').style.visibility = 'hidden';
		getObj('lms_access_days').style.visibility = 'hidden';
		getObj('discount_area').style.visibility = 'hidden';
		getObj('discount_price_area').style.visibility = 'hidden';
		form.discount.value = 0;
		calculator();
		if (form.account_type.value == '1') {

		}
		if (form.account_type.value == '2') {
			getObj('lms_start_date').style.visibility = 'visible';
			getObj('lms_end_date').style.visibility = 'visible';
		}
		if (form.account_type.value == '3') {
			getObj('lms_start_date').style.visibility = 'visible';
		}
		if (form.account_type.value == '4') {
			getObj('lms_access_days').style.visibility = 'visible';
		}
		if (form.account_type.value == '5') {
			getObj('discount_area').style.visibility = 'visible';
			getObj('discount_price_area').style.visibility = 'visible';
		}
		if(form.account_type.value.lastIndexOf('6') != -1)
		{
			getObj('price_area').style.visibility = 'hidden';
			getObj('subtotal_area').style.visibility = 'hidden';
			getObj('price_a1').style.visibility = 'visible';
			getObj('price_a2').style.visibility = 'visible';
			getObj('price_a3').style.visibility = 'visible';
		}
		else
		{
			getObj('price_area').style.visibility = 'visible';
			getObj('subtotal_area').style.visibility = 'visible';
			getObj('price_a1').style.visibility = 'hidden';
			getObj('price_a2').style.visibility = 'hidden';
			getObj('price_a3').style.visibility = 'hidden';
		}
	}
	<?php
	if ($JLMS_CONFIG->get('use_global_groups', 1)) {
	?>
	function view_fields(el){
		var form = document.adminForm;
		if(el.value && form['restricted_groups[]'].disabled){
			form['restricted_groups[]'].disabled = false;	
		} else {
			form['restricted_groups[]'].disabled = true;	
		}	
	}
	<?php
	}
	?>
	-->
	</script>	
	<form action="index.php" method="post" name="adminForm" onsubmit="setgood();">	
	<div class="current">
	<table width="100%">
		<tr>
		<td valign="top" width="220">
		<div>
			<?php joomla_lms_adm_html::JLMS_menu();?>
		</div>
		</td>
		<td valign="top">	
		<?php if (!class_exists('JToolBarHelper')) { ?>
			<table class="adminheading">
			<tr>
				<th class="edit">
				<?php echo _JOOMLMS_COMP_NAME;?>:<small><?php echo $subs->id ? ' '._JLMS_SUBS_EDIT_SUBSCRIPTION : ' '._JLMS_SUBS_ADD_SUBSCRIPTION;?></small>
				</th>
				<td align="right"><?php //echo $lists['status_filter']?></td>
			</tr>
			</table>
		<?php } ?>		
			<table width="100%" border="0" class="adminlist">
			<thead>
				<tr>
					<th class="title" colspan="2" align="left"><?php echo _JLMS_SUBS_SUBSCRIPTION_DETAILS; ?></th>
				</tr>
			</thead>
				<tr>
					<td valign="top" width="100px"><br />
					<b><?php echo _JLMS_SUBS_SUBSCRIPTION_NAME; ?>:</b>
					</td>
					<td><br />
					<input type="text" size="70" name="sub_name" class="text_area" value="<?php echo str_replace('"', '&quot;',$subs->sub_name);?>">
					</td>
				</tr>
				<tr>
					<td valign="top" width="20%" colspan="2">
					<table ><tr>
						<td width="100px">
						<?php echo _JLMS_SUBS_AVAILABLE_COURSES; ?>:<br />
						<?php
						echo '<select name="courses_list" class="text_area" size="10" multiple="multiple" style="width:340px">';
						$k = '';
						for ($i=0; $i<count($rows);$i++){
							echo "<option value='".$rows[$i]->id."'>".$rows[$i]->course_name."</option>";
							$k .= "<input type='hidden' name='id_".$rows[$i]->id."' value='".(strval($rows[$i]->course_price) === '' ? '0' : $rows[$i]->course_price)."' />";
						}
						echo "</select>";
						echo $k;
						?>
						</td>
						<td>
						<input class="button" value="&gt;&gt;" onclick="SelectedToList('adminForm','courses_list','courses_in_sub[]');" title="<?php echo _JLMS_ADD; ?>" type="button">
						<br />
						<input class="button" value="&lt;&lt;" onclick="delSelectedItems('adminForm','courses_in_sub[]')" title="<?php echo _JLMS_REMOVE; ?>" type="button">
						</td>
						<td width="200px">
						<?php echo _JLMS_SUBS_COURSES_IN_THE_SUBS; ?>: <br />
						<?php
						echo '<select name="courses_in_sub[]" class="text_area" size="10" multiple="multiple" style="width:340px">';
						$k = '';
						for ($i=0; $i<count($courses_in);$i++){
							echo "<option value='".$courses_in[$i]->id."'>".$courses_in[$i]->course_name."</option>";
						}
						echo "</select>";
						?>
						<?php //echo $lists['courses_in_sub']?>
						</td>
					</tr></table>
					</td>
				</tr>
				<tr>
					<td>
					<?php echo _JLMS_PUBLISHED; ?>:
					</td>
					<td>
					<fieldset class="radio">
					<?php echo $lists['published']?>
					</fieldset>
					</td>
				</tr>
			</table><br />
			<table width="100%" class="adminlist">
			<thead>
				<tr><th colspan="3"><?php echo _JLMS_SUBS_SUBSCRIPTION_TYPE; ?></th></tr>
			</thead>
				<tr>
					<td width="10%" valign="top"><?php echo _JLMS_TYPE; ?>:</td>
					<td width="20%"  valign="top" >					
					<select name="account_type" size="10" style="width: 130px;" class="text_area" onchange="check_ms_options();">
							<option value="1" <?php if ($subs->account_type=='' || $subs->account_type=='1') echo 'selected="selected"';?>><?php echo _JLMS_BASIC; ?></option>
							<option value="2" <?php if ($subs->account_type=='2') echo 'selected="selected"';?>><?php echo _JLMS_DATE_TO_DATE; ?></option>
							<option value="3" <?php if ($subs->account_type=='3') echo 'selected="selected"';?>><?php echo _JLMS_DATE_TO_LIFETIME; ?></option>
							<option value="4" <?php if ($subs->account_type=='4') echo 'selected="selected"';?>><?php echo _JLMS_X_DAYS_ACCESS; ?></option>
							<option value="5" <?php if ($subs->account_type=='5') echo 'selected="selected"';?>><?php echo _JLMS_WITH_DISCOUNT; ?></option>
							<?php
							if ($plans)
							{
								foreach($plans as $plan)
								{								
									?>
									<option value="6:<?php echo $plan->id?>" <?php if ($subs->account_type=='6:'.$plan->id) echo 'selected="selected"';?>><?php echo _JLMS_SUBS_PLAN; ?>:<?php echo $plan->name?></option>
									<?php
								}
							}
							?>
						</select>
					</td>
					<td valign="top">									
					<table width="100%">
					<tr id="lms_start_date" style="visibility:<?php if ($subs->account_type=='2' || $subs->account_type=='3') echo 'visible'; else echo 'hidden';?>">
						<td width="100"><?php echo _JLMS_START_DATE; ?></php>:</td>
						<td>
	<?php if (class_exists('JHTML')) {
		$joomla_generated_code = JHTML::_('calendar', $subs->start_date, 'start_date', 'start_date', '%Y-%m-%d', array('class' => 'text_area'));
		//ignore joomla generated code ;)
		echo '<input type="text" name="start_date" id="start_date" value="'.htmlspecialchars($subs->start_date, ENT_COMPAT, 'UTF-8').'" size="10" maxlength="10" />&nbsp;'.
			 '<img class="calendar" src="'.JURI::root().'templates/system/images/calendar.png" alt="calendar" id="start_date_img" align="absbottom" />';
	} else { ?>
		<input class="text_area" type="text" name="start_date" id="start_date" size="10" maxlength="10" value="<?php echo $subs->start_date;?>" />
		<input type="button" class="button" value="..." onclick="showCalendar('start_date', '%Y-%m-%d');return showCalendar('start_date', '%Y-%m-%d');" />
	<?php } ?>
						</td>
					</tr>
	
					<tr id="lms_end_date" style="visibility:<?php if ($subs->account_type=='2') echo 'visible'; else echo 'hidden';?>">
						<td><?php echo _JLMS_END_DATE; ?>:</td>
						<td>
	<?php if (class_exists('JHTML')) {
		$joomla_generated_code = JHTML::_('calendar', $subs->end_date, 'end_date', 'end_date', '%Y-%m-%d', array('class' => 'text_area'));
		//ignore joomla generated code ;)
		echo '<input type="text" name="end_date" id="end_date" value="'.htmlspecialchars($subs->end_date, ENT_COMPAT, 'UTF-8').'" size="10" maxlength="10" />&nbsp;'.
			 '<img class="calendar" src="'.JURI::root().'templates/system/images/calendar.png" alt="calendar" id="end_date_img" align="absbottom" />';
	} else { ?>
		<input class="text_area" type="text" name="end_date" id="end_date" size="10" maxlength="10" value="<?php echo $subs->end_date;?>" />
		<input type="button" class="button" value="..." onclick="showCalendar('end_date', 'y-mm-dd');return showCalendar('end_date', 'y-mm-dd');" />
	<?php } ?>
						</td>
					</tr>
					<tr id="lms_access_days" style="visibility:<?php if ($subs->account_type=='4') echo 'visible'; else echo 'hidden';?>">
						<td align="right"><?php echo _JLMS_SUBS_TERM_OF_ACCESS; ?>:</td>
						<td><input class="text_area" type="text" name="access_days" size="7" maxlength="10" value="<?php echo $subs->access_days; ?>" /> <?php echo _JLMS_SUBS_DAYS; ?></td>
					</tr>
					<tr id="discount_area" style="visibility:<?php if ($subs->account_type=='5' ) echo 'visible'; else echo 'hidden';?>">
						<td width="300px" align="right"><b><?php echo _JLMS_SUBS_DISCOUNT_P; ?>:</b></td>
						<td align="left" >
						<input type="text" class="text_area" name="discount" size="6" value="<?php echo $subs->discount;?>" onkeyup="calculator();" onchange="calculator();" />
						</td>
					</tr>
					<tr id="price_a1" style="visibility:<?php if (strpos($subs->account_type, '6')===false) echo 'hidden'; else echo 'visible';?>">
						<td width="300px" align="right"><b><?php echo _JLMS_SUBS_FIRST_TRIAL_PRICE; ?>:</b></td>
						<td align="left" >
						<input type="text" class="text_area" name="a1" size="6" value="<?php echo round($subs->a1,2);?>" onkeyup="calculator();" onchange="calculator();" />
						</td>
					</tr>
					<tr id="price_a2" style="visibility:<?php if (strpos($subs->account_type, '6')===false) echo 'hidden'; else echo 'visible';?>">
						<td width="300px" align="right"><b><?php echo _JLMS_SUBS_SECOND_TRIAL_PRICE; ?>:</b></td>
						<td align="left" >
						<input type="text" class="text_area" name="a2" size="6" value="<?php echo round($subs->a2,2);?>" onkeyup="calculator();" onchange="calculator();" />
						</td>
					</tr>
					<tr id="price_a3" style="visibility:<?php if (strpos($subs->account_type, '6')===false) echo 'hidden'; else echo 'visible';?>">
						<td width="300px" align="right"><b><?php echo _JLMS_SUBS_REGULAR_PERIOD_PRICE; ?>:</b></td>
						<td align="left" >
						<input type="text" class="text_area" name="a3" size="6" value="<?php echo round($subs->a3,2);?>" onkeyup="calculator();" onchange="calculator();" />
						</td>
					</tr>		
					<tr id="subtotal_area" style="visibility:<?php if (strpos($subs->account_type, '6')===false) echo 'visible'; else echo 'hidden';?>">
						<td width="300px" align="right"><b><?php echo _JLMS_SUBS_PRICE_SELECTED_COURSES; ?>:</b></td>
						<td align="left" >
						<input type="text" class="text_area" size="6" name="sub_total" value="<?php echo $subs->price?>" disabled="disabled" />&nbsp;<input type="button" class="button" name="V" value="V" onclick="document.adminForm.price.value = document.adminForm.sub_total.value; calculator();" />
						</td>
					</tr>
					<tr id="price_area" style="visibility:<?php if (strpos($subs->account_type, '6')===false) echo 'visible'; else echo 'hidden';?>">
						<td width="300px" align="right"><b><?php echo _JLMS_SUBS_PRICE; ?>:</b></td>
						<td align="left" >
						<input type="text" class="text_area" name="price" size="6" value="<?php echo round($subs->price,2);?>" onkeyup="calculator();" onchange="calculator();" />
						</td>
					</tr>
					<tr id="discount_price_area" style="visibility:<?php if ($subs->account_type=='5' ) echo 'visible'; else echo 'hidden';?>">
						<td width="300px" align="right"><b><?php echo _JLMS_SUBS_SUBSCRIPTION_PRICE; ?>:</b></td>
						<td align="left" >
						<input type="text" class="text_area" name="discount_price" size="6" value="<?php echo $subs->discount;?>" disabled="disabled" />
						</td>
					</tr>
				</table>
				</td>
				</tr>				
			</table>
			<br />
			<table width="100%" class="adminlist">
				<thead>
				<tr><th class="title"><?php echo _JLMS_SUBS_SUBSCRIPTION_DESC; ?></th></tr>
				</thead>
				<tr>
					<td>
					<?php JLMS_editorArea( 'editor1', $subs->sub_descr, 'sub_descr', '100%;', '250', '40', '20' ) ; ?>
					</td>
				</tr>
			</table>
			<?php
			if ($JLMS_CONFIG->get('use_global_groups', 1)) {
			?>
			<br />
			<table width="100%" class="adminlist">
				<thead>
					<tr>
						<th class="title" colspan="2"><?php echo _JLMS_SUBS_RESTRICTED; ?></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td width="20%">
							<?php echo _JLMS_SUBS_RESTRICTED_SUBS; ?>:
						</td>
						<td>
							<fieldset class="radio">
							<?php echo $lists['restricted'];?>
							</fieldset>
						</td>
					</tr>
					<tr>
						<td>
							<?php echo _JLMS_SUBS_RESTRICTED_GROUPS; ?>:
						</td>
						<td>
							<?php echo $lists['restricted_groups'];?>
						</td>
					</tr>
				</tbody>
			</table>
			<?php
			}
			?>
		</td>
	</tr>
	</table>
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="task" value="subscriptions" />
		<input type="hidden" name="id" value="<?php echo $subs->id?>" />
		<input type="hidden" name="hidemainmenu" value="0" />
		<script type="text/javascript">calculator();</script>	
		</div>	
		</form>		
		<?php
	}


	function showProcessorsList( &$rows, &$pageNav, $option, $msg ) {
		global $my;

		JHTML::_('behavior.tooltip');
		if ($msg) echo '<div class="message"><br /><br />'.$msg.'<br /></div>';
		?>
		<form action="index.php" method="post" name="adminForm">		
		<table width="100%">
		<tr>
			<td valign="top" width="220">
			<div>
				<?php echo joomla_lms_adm_html::JLMS_menu();?>
			</div>
			</td>
			<td valign="top">		
			<?php if (!class_exists('JToolBarHelper')) { ?>
				<table class="adminheading">
				<tr>
					<th class="sections">
					<?php echo _JOOMLMS_COMP_NAME;?>: <small><?php echo _JLMS_PROCS_PROCS_LIST; ?></small>
					</th>
					<td align="right"></td>
				</tr>
				</table>
			<?php } ?>				
				<table class="adminlist">
					<thead>
						<tr>
							<th width="20">#</th>
							<th width="20" class="title"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>
							<th class="title"><?php echo _JLMS_NAME; ?></th>
							<th class="title"><?php echo _JLMS_SINGLE_PAYMENTS; ?></th>
							<?php global $license_lms_recurrent;
							if ($license_lms_recurrent) { ?>
							<th class="title"><?php echo _JLMS_RECURRENT_PAYMENTS; ?></th>
							<?php } ?>							
							<th class="title" align="center"><?php echo _JLMS_DEFAULT; ?></th>
							<th class="title" align="center" width="40"><?php echo _JLMS_PUBLISHED; ?></th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<td colspan="<?php echo $license_lms_recurrent ? '7' : '6';?>">
							<?php echo $pageNav->getListFooter();?>
							</td>
						</tr>
					</tfoot>
					<tbody>
					<?php
					$k = 0;
					for ($i=0, $n=count($rows); $i < $n; $i++) {
						$row = $rows[$i];

						$link 	= 'index.php?option=com_joomla_lms&amp;task=editA_p&amp;hidemainmenu=1&amp;id='. $row->id;
						$link2 	= 'index.php?option=com_joomla_lms&amp;task=default_p&amp;hidemainmenu=1&amp;id='. $row->id;

						$img 	= $row->published ? 'tick.png' : 'publish_x.png';
						$task 	= $row->published ? 'unpublish' : 'publish';
						$alt 	= $row->published ? _JLMS_PUBLISHED : _JLMS_UNPUBLISHED;
						
						$img_s 	= $row->is_single ? 'tick.png' : 'publish_x.png';
						$alt_s 	= $row->is_single ? _JLMS_YES : _JLMS_NO;
						$img_r 	= $row->is_recurrent ? 'tick.png' : 'publish_x.png';
						$alt_r 	= $row->is_recurrent ? _JLMS_YES : _JLMS_NO;

						$row->editor = '';
						$checked 	= mosCommonHTML::CheckedOutProcessing( $row, $i );

						?>
						<tr class="<?php echo "row$k"; ?>">
							<td width="20"><?php echo $pageNav->rowNumber( $i ); ?></td>
							<td width="20"><?php echo $checked; ?></td>
							<td align="left">
							<?php
							if ( $row->checked_out && ( $row->checked_out != $my->id ) ) {
								echo $row->name;
							} else {
								?>
								<a href="<?php echo $link; ?>" title="<?php echo _JLMS_PROCS_EDIT_PROC; ?>">
								<?php echo $row->name; ?>
								</a>
								<?php
							}
							?>
							</td>
							<td align="center">
							<img src="<?php echo ADMIN_IMAGES_PATH.$img_s;?>" border="0" alt="<?php echo $alt_s; ?>" />
							</td>
							<?php
							if ($license_lms_recurrent) { ?>
							<td align="center">
							<img src="<?php echo ADMIN_IMAGES_PATH.$img_r;?>" border="0" alt="<?php echo $alt_r; ?>" />
							</td>
							<?php } ?>
							<td align="center">
							<?php if ($row->default_p) echo '<img src="'.ADMIN_IMAGES_PATH.'tick.png" alt="'. _JLMS_DEFAULT.'">';
							else {?>
								<a href="<?php echo $link2;?>"><?php echo _JLMS_PROCS_MAKE_DEFAULT; ?></a>
							<?php }
							?>
							</td>
							<td align="center" width="40">
								<a href="javascript: void(0);" onclick="return listItemTask('cb<?php echo $i;?>','<?php echo $task.'_proc';?>')">
								<img src="<?php echo ADMIN_IMAGES_PATH.$img;?>" border="0" alt="<?php echo $alt; ?>" />
								</a>
							</td>
						</tr>
				<?php $k = 1 - $k;
					}?>
					</tbody>
					</table>				
					</td>
				</tr>
		</table>
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="task" value="processorslist" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0" />		
		</form>
	<?php
	}

	function configSubscription($row, $option) {
		$app = & JFactory::getApplication('administrator');
	?>
	<script type="text/javascript">
	<!--
	function submitbutton(pressbutton) {
		var form = document.adminForm;

		form.task.value = pressbutton;
		form.submit();

	}
	
	<?php if( JLMS_J16version() ) { ?>
	Joomla.submitbutton = submitbutton;
	<?php } ?>
	
	//-->
	</script>
	<form action="index.php" method="post" name="adminForm">	
	<table width="100%">
		<tr>
			<td valign="top" width="220">
			<div>
				<?php echo joomla_lms_adm_html::JLMS_menu();?>
			</div>
			</td>
		<td valign="top">	
		<?php if (!class_exists('JToolBarHelper')) { ?>
			<table class="adminheading">
			<tr>
				<th class="edit">
				<?php echo _JOOMLMS_COMP_NAME?>: <small><?php echo _JLMS_SUBS_CONF_INVOICE_CONF; ?></small>
				</th>
				<td align="right"></td>
			</tr>
			</table>
		<?php } ?>	
		<table width="100%" class="adminlist">
			<thead>
			<tr>
				<th class="title" colspan="2">
					<?php echo _JLMS_SUBS_CONF_INVOICE_SETT; ?>
				</th>
			</tr>
			</thead>
			<tbody>
			<tr>
				<td valign="top" width="200">
					<?php echo _JLMS_SUBS_CONF_COMPANY_NAME; ?>
				</td>
				<td>
					<input class="text_area" type="text" maxlength="150" style="width:350px;" name="site_name" value="<?php echo (isset($row->site_name) && $row->site_name)?(str_replace('"','&quot;',$row->site_name)):(str_replace('http://','',JURI::root()));?>" />
				</td>
			</tr>
			<tr>
				<td  valign="top">
					<?php echo _JLMS_SUBS_CONF_SHORT_INFO; ?>
				</td>
				<td>
					<textarea class="text_area" style="width:350px;" rows="10" name="site_descr" ><?php echo (isset($row->site_descr) && $row->site_descr)?$row->site_descr:($app->getCfg('sitename' ));?></textarea>
				</td>
			</tr>
			<tr>
				<td  valign="top">
					<?php echo _JLMS_SUBS_CONF_COMPANY_DESC; ?>
				</td>
				<td>
					<textarea class="text_area" style="width:350px;" rows="10" name="comp_descr" ><?php echo isset($row->comp_descr)?$row->comp_descr:''?></textarea>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<?php echo _JLMS_SUBS_CONF_COMMENTS_OR_SPEC_INSTR; ?>
				</td>
				<td>
					<textarea class="text_area" style="width:350px;" rows="10" name="comments" ><?php echo isset($row->comments)?$row->comments:''?></textarea>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<?php echo _JLMS_SUBS_CONF_PAYMENT_TERMS; ?>
				</td>
				<td>
					<textarea class="text_area" style="width:350px;" rows="10" name="invoice_descr" ><?php echo isset($row->invoice_descr)?$row->invoice_descr:''?></textarea>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<?php echo _JLMS_SUBS_CONF_INVOICE_FOOTER; ?>
				</td>
				<td>
					<input class="text_area" type="text" maxlength="150" style="width:350px;" name="thanks_text" value="<?php echo isset($row->thanks_text)?$row->thanks_text:_JLMS_SUBS_CONF_DEF_THANKS_TEXT?>" />
				</td>
			</tr>
			</tbody>
		</table>
		<table width="100%" class="adminlist">
			<thead>
			<tr>
				<th class="title" colspan="2">
					<?php echo _JLMS_SUBS_CONF_MAIL_SETTINGS; ?>
				</th>
			</tr>
			</thead>
			<tbody>
			<tr>
				<td width="200" valign="top">
					<?php echo _JLMS_SUBS_CONF_MAIL_SUBJECT; ?>
				</td>
				<td>
				<?php 
				if (!isset($row->mail_subj) || (isset($row->mail_subj) && !$row->mail_subj)) {
						$row->mail_subj = _JLMS_SUBS_CONF_INV_FOR_Y_ORDER;
					}
				if (!isset($row->mail_body) || (isset($row->mail_body) && !$row->mail_body)) {
						$row->mail_body = _JLMS_SUBS_CONF_CHECK_ATT_FILE;
					}
				?>
					<input class="text_area" type="text" maxlength="150" style="width:350px;" name="mail_subj" value="<?php echo isset($row->mail_subj)?(str_replace('"','&quot;',$row->mail_subj)):'';?>" />
				</td>
			</tr>
			<tr>
				<td  width="200" valign="top">
					<?php echo _JLMS_SUBS_CONF_MAIL_BODY; ?>
				</td>
				<td>
					<textarea class="text_area" style="width:350px;" rows="10" name="mail_body" ><?php echo isset($row->mail_body)?$row->mail_body:''?></textarea>
				</td>
			</tr>
			</tbody>
		</table>
				</td>
			</tr>
		</table>	
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0" />		
		</form>
	<?php
	}

	function editProcessor( &$row, &$lists, $option, &$params ) {
		$tabs = new mosTabs(0);
		?>
		<script language="javascript" type="text/javascript">
		<!--
		function submitbutton(pressbutton) {
			var form = document.adminForm;
			if (pressbutton == 'cancel_p') {
				submitform( pressbutton );
				return;
			}
			else {
				submitform( pressbutton );
			}
		}
		
		<?php if( JLMS_J16version() ) { ?>
		Joomla.submitbutton = submitbutton;
		<?php } ?>
		//-->
		</script>

		<form action="index.php" method="post" name="adminForm">		
	<table width="100%">
		<tr>
		<td valign="top" width="220">
		<div>
			<?php echo joomla_lms_adm_html::JLMS_menu();?>
		</div>
		</td>
		<td valign="top">
		<div class="width-100">
		<fieldset class="adminform">
		<?php if (!class_exists('JToolBarHelper')) { ?>
			<table class="adminheading">
			<tr>
				<th class="edit">
				<?php echo _JOOMLMS_COMP_NAME?>: <small><?php echo _JLMS_PROCS_EDIT_PAY_PROC; ?></small>
				</th>
				<td align="right"></td>
			</tr>
			</table>
		<?php } ?>		
			<table width="100%">
			<tr>
				<td width="40%" valign="top">
					<table width="100%" >
					<tr>
						<th colspan="2">
						<?php echo _JLMS_PROCS_PAY_PROC_DETAILS; ?>
						</th>
					<tr>
					<tr>
						<td width="20%" align="right">
						<?php echo _JLMS_PROCS_FILENAMES; ?>:
						</td>
						<td >
						<?php echo $row->filename.'.php, '.$row->filename.'.xml';?>
						</td>
					</tr>
					<tr>
						<th colspan="2">
						<?php echo _JLMS_DESCRIPTION; ?>:
						</th>
					<tr>
					<tr>
						<td width="20%" align="right" colspan="2">
						<?php echo $row->description;?>
						</td>
					</tr>
					</table>
				</td>
				<td width="60%" valign="top">
					<table width="100%" >
						<tr>
							<th colspan="2">
							<?php echo _JLMS_PROCS_PARAMETERS; ?>
							</th>
						</tr>
						<tr>
							<td colspan="2">
							<table class="paramlist admintable" width="100%">
								<tr>
									<td class="paramlist_key" width="40%">
										<?php echo _JLMS_PROCS_PROC_NAME; ?>:
									</td>
									<td class="paramlist_value">
										<input class="text_area" type="text" size="40" value="<?php echo $row->name;?>" name="name"/>
									</td>
								</tr>
							</table>
							</td>
						</tr>
						<tr>
							<td colspan="2">
							<?php echo $params->render();?>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			</table>
		</td></tr></table>
		</fieldset>
		</div>		
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
		<input type="hidden" name="task" value="" />		
		</form>
		<?php
	}
	
	function JLMS_assign( &$row, &$lists, $option, $sub_name, $sub_id ) { ?>

	<script language="javascript" type="text/javascript">
	<!--
	function jlms_changeUsergroup() {
		var form = document.adminForm;
		form.task.value = 'assign';
		form.submit();
		return;
	}
function jlms_changeUserSelect(c_e) {
	var sel_value = c_e.options[c_e.selectedIndex].value;
	var sel1 = c_e.form.user_name;
	for (var i = 0; i < sel1.options.length; i++) {
		if (sel1.options[i].value == sel_value) {
			sel1.options[i].selected = true;
		}
	}
	var sel2 = c_e.form.user_email;
	for (var i = 0; i < sel2.options.length; i++) {
		if (sel2.options[i].value == sel_value) {
			sel2.options[i].selected = true;
		}
	}
	var sel3 = c_e.form.user_id;
	for (var i = 0; i < sel3.options.length; i++) {
		if (sel3.options[i].value == sel_value) {
			sel3.options[i].selected = true;
		}
	}
}
	function submitbutton(pressbutton) {
		var form = document.adminForm;
		if (pressbutton == 'cancel_assign') {
			form.task.value = 'subscriptions';
			form.submit();
			return;
		}
		else {
			if(form.usergroup.value == 0 && form.user_id.value == 0 && form.user_name.value == 0 && form.user_email.value == 0) {
				alert('<?php echo _JLMS_SUBS_MSG_SELECT_USR_OR_USRGROUP; ?>');				
			}
			else {
				form.task.value = 'save_assign';
				form.submit();
				return;
			}	
		}
	}
	
	<?php if( JLMS_J16version() ) { ?>
	Joomla.submitbutton = submitbutton;
	<?php } ?>
	//-->
	</script>
		
		<form action="index.php" method="post" name="adminForm">		
		<table width="100%" >
			<tr>
				<td valign="top" width="220">
				<div>
					<?php echo joomla_lms_adm_html::JLMS_menu();?>
				</div>
				</td>
				<td valign="top">
				<div class="width-100">
				<fieldset class="adminform">
			<?php if (!class_exists('JToolBarHelper')) { ?>
				<table class="adminheading">
				<tr>
					<th class="user">
					<?php echo _JOOMLMS_COMP_NAME;?>:
					<small>
					<?php echo _JLMS_SUBS_ASSIGN_SUBS;?>
					</small>
					</th>
				</tr>
				</table>
			<?php } ?>			
				<table width="100%" border="0">
					<tr>
						<td valign="top">
							<table width="100%" >
								<tr>
									<th colspan="2"></th>
								</tr>
								<tr>
									<td align="right" width="20%"><?php echo _JLMS_SUBSCRIPTION; ?>:</td>
									<td><?php echo $sub_name;?></td>
								</tr>
								<tr>
									<td align="right" width="20%"><?php echo _JLMS_USERGROUP; ?>:</td>
									<td><?php echo $lists['usergroups'];?></td>
								</tr>
								<tr>
									<td align="right" width="20%"><?php echo _JLMS_USERNAME; ?>:</td>
									<td><?php echo $lists['users'];?></td>
								</tr>
								<tr>
									<td align="right" width="20%"><?php echo _JLMS_SUBS_OR_NAME; ?>:</td>
									<td><?php echo $lists['users_names'];?></td>
								</tr>
								<tr>
									<td align="right" width="20%"><?php echo _JLMS_SUBS_OR_EMAIL; ?>:</td>
									<td><?php echo $lists['users_emails'];?></td>
								</tr>
							</table>
							<br />
						</td>
					</tr>
				</table>
				</fieldset>
				</div>
		</td></tr></table>
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="sub_id" value="<?php echo $sub_id;?>" />		
		</form>
		<?php
	}
	
	function JLMS_subscriptionsList( $rows, $pageNav, $option, $lists ){
?>
		<script language="javascript">
		function submitbutton(pressbutton) {
			if(pressbutton == 'assign') {
				if (document.adminForm.boxchecked.value == 0) { 
						alert('<?php echo _JLMS_SUBS_SELECT_SUBS_FROM_LIST; ?>');
				}
				else {
					submitform(pressbutton);
				}
			}	
			else{
				submitform(pressbutton);
			}
		}
		<?php if( JLMS_J16version() ) { ?>
		Joomla.submitbutton = submitbutton;
		<?php } ?>
		</script>
		<form action="index.php" method="post" name="adminForm">		
<table width="100%" >
	<tr>
	<td valign="top" width="220">
	<div>
		<?php joomla_lms_adm_html::JLMS_menu();?>
	</div>
	</td>
	<td valign="top">	
		<table cellpadding="0" cellspacing="0" border="0">
		<tr>
			<td align="left" width="100%">&nbsp;</td>
			<td nowrap="nowrap">
				<table class="adminlist">
				<tr class="row1">	
					<td nowrap="nowrap" style="padding:2px 10px 2px 10px;"><?php echo _JLMS_SUBS_STATUS; ?>:&nbsp;&nbsp;</td>			
					<td nowrap="nowrap" style="padding:2px 10px 2px 10px;"><?php echo $lists['status_filter'];?></td>
				</tr>			
				</table>
			</td>
		</tr>
		</table>			
		<table width="100%" border="0">
			<tr>
				<td valign="top">
					<table class="adminlist" width="100%">
					<thead>
						<tr>
							<th width="20px">#</th>
							<th width="20px" class="title"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>
							<th class="title" width="150px"><?php echo _JLMS_SUBS_SUBSCRIPTION_NAME; ?></th>
							<th class="title" width="150px"><?php echo _JLMS_COURSES; ?></th>
							<th class="title" width="100px"><?php echo _JLMS_TYPE; ?></th>
							<th class="title" width="100px"><?php echo _JLMS_START_DATE; ?></th>
							<th class="title" width="100px"><?php echo _JLMS_END_DATE; ?></th>
							<th class="title" width="70px"><?php echo _JLMS_PUBLISHED; ?></th>
							<th class="title" width="40px"><?php echo _JLMS_PRICE; ?></th>
							<th class="title" width="140px"><?php echo _JLMS_DATE; ?></th>
							<th class="title" width="30"><?php echo _JLMS_ID; ?></th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<td colspan="11">
							<?php echo $pageNav->getListFooter();?>
							</td>
						</tr>
					</tfoot>
					<tbody>
					<?php
					$k = 0;
					for ($i=0, $n=count($rows); $i < $n; $i++) {
						$row = $rows[$i];
						$img_published	= $row->published ? 'tick.png' : 'publish_x.png';
						$task_published	= $row->published ? 'unpublish_subscription' : 'publish_subscription';
						$alt_published 	= $row->published ? _JLMS_PUBLISHED : _JLMS_UNPUBLISHED;
						$checked = mosHTML::idBox( $i, $row->id);?>
						<tr class="<?php echo "row$k"; ?>">
							<td><?php echo $pageNav->rowNumber( $i ); ?></td>
							<td><?php echo $checked; ?></td>

							<td align="left">
								<?php echo "<a href='index.php?option=$option&amp;task=editA_subscription&amp;id=".$row->id."' >".$row->sub_name."</a>";?>
							</td>
							<td align="left">
								<?php
								$db = & JFactory::GetDbo();
								$query = "SELECT course_name FROM `#__lms_subscriptions_courses` as a, `#__lms_courses` as b WHERE a.sub_id = '".$row->id."' AND a.course_id = b.id ";
								$db->setQuery($query);
								$courses = $db->loadObjectList();
								foreach ($courses as $course){
									echo $course->course_name."<br />";
								}
								?>
							</td>
							<td align="left">
								<?php
								$start_date = '-';
								$end_date 	= '-';
								if ($row->account_type == 1){echo _JLMS_BASIC;}
								elseif($row->account_type == 2){
									echo _JLMS_DATE_TO_DATE;
									$start_date = $row->start_date;
									$end_date 	= $row->end_date;
								}
								elseif($row->account_type == 3){
									echo _JLMS_DATE_TO_LIFETIME;
									$start_date = $row->start_date;
									$end_date 	= 'Lifetime';
								}
								elseif($row->account_type == 4){
									echo _JLMS_X_DAYS_ACCESS;
									$start_date = _JLMS_SUBS_DAY_OF_PAYMENT;
									$end_date 	= '+'.$row->access_days.' '._JLMS_SUBS_DAYS;
								}
								elseif($row->account_type == 5){
									echo _JLMS_WITH_DISCOUNT;
								}elseif(isset($row->plan_name)) 
								{
									echo $row->plan_name;
								}
								?>
							</td>
							<td align="left">
								<?php echo $start_date;?>
							</td>
							<td align="left">
								<?php echo $end_date;?>
							</td>
							<td align="center">
								<a href="javascript: void(0);" onclick="return listItemTask('cb<?php echo $i;?>','<?php echo $task_published;?>')">
									<img src="<?php echo ADMIN_IMAGES_PATH.$img_published;?>" border="0" alt="<?php echo $alt_published; ?>" />
								</a>
							</td>
							<td align="left" nowrap="nowrap"><?php
							if ( $row->account_type == 5 && round($row->discount,2) ) {
								printf('%.2f',round($row->price,2)); echo  " - " . round($row->discount,2) . "% = "; printf('%.2f', round($row->price - ($row->price*$row->discount/100) ,2));
							} else if( $row->account_type == 6 ) {
								echo JLMS_RECURRENT_PAY::getPriceDesc( $row );
							} else {
								if ($row->account_type == 5) { echo "<font color='red'>";}
								printf('%.2f',round($row->price,2));
								if ($row->account_type == 5) { echo "</font>";}
							}?>
							</td>
							<td align="left">
								<?php echo $row->date;?>
							</td>
							<td><?php echo $row->id;?></td>
						</tr>
						<?php
						$k = 1 - $k;
					}?>
					</tbody>
					</table>
				</td>
			</tr>
		</table>		
</td></tr></table>
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="task" value="subscriptions" />
		<input type="hidden" name="boxchecked" value="0" />

		<input type="hidden" name="hidemainmenu" value="0" />		
		</form>
		<?php
	}


	function JLMS_paymentsList( $rows, $pageNav, $option, $lists, $params ){
		global $JLMS_CONFIG;
		JHTML::_('behavior.tooltip');		
		?>
		<script type="text/javascript">
			function submitbutton(pressbutton) 
			{
				var old_task = document.adminForm.task.value;
								
				submitform(pressbutton);				
				
				document.adminForm.task.value = old_task;
			}
			
			<?php if( JLMS_J16version() ) { ?>
			Joomla.submitbutton = submitbutton;
			<?php } ?>
		</script>
		<form action="index.php" method="post" name="adminForm">		
		<table width="100%" >
			<tr>
			<td valign="top" width="220">
			<div>
				<?php echo joomla_lms_adm_html::JLMS_menu();?>
			</div>
			</td>
			<td valign="top">				
				<?php if (!class_exists('JToolBarHelper')) { ?>
				<table cellpadding="0" cellspacing="0" border="0">
				<tr>
					<th class="sections">
					<?php echo _JOOMLMS_COMP_NAME;?>: <small><?php echo _JLMS_PAYS_PAYS_LIST; ?></small>
					</th>
					<td width="right">
						<table class="adminlist">
						<tr class="row1">
							<td nowrap="nowrap" style="padding:2px 10px 2px 10px;"><?php echo _JLMS_PAYS_PAY_STATUS; ?>:&nbsp;&nbsp;</td>
							<td nowrap="nowrap" style="padding:2px 10px 2px 10px;"><?php echo $lists['status_filter'];?></td>
						</tr>
						<tr class="row1">
							<td nowrap="nowrap" style="padding:2px 10px 2px 10px;"><?php echo _JLMS_PAYS_PAY_PROC; ?>:&nbsp;&nbsp;</td>
							<td nowrap="nowrap" style="padding:2px 10px 2px 10px;"><?php echo $lists['proc_filter'];?></td>
						</tr>
						<tr class="row1">
							<td nowrap="nowrap" style="padding:2px 10px 2px 10px;"><?php echo _JLMS_PAYS_PAY_PERIOD; ?>:&nbsp;&nbsp;</td>
							<td nowrap="nowrap" style="padding:2px 10px 2px 10px;"><?php echo $lists['period_filter'];?></td>
						</tr>
						<tr class="row1">
							<td nowrap="nowrap" style="padding:2px 10px 2px 10px;"><?php echo _JLMS_SEARCH; ?>:&nbsp;&nbsp;</td>
							<td nowrap="nowrap" style="padding:2px 10px 2px 10px;">
								<input type="text" name="search_term" value="<?php echo str_replace('"', '&quot;', $lists['search_term']);?>" class="text_area" style="width:264px" />
							</td>
						</tr>
						</table>
					</td>
				</tr>
				</table>
				<?php } else { ?>
				<table cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td align="left" width="100%">&nbsp;</td>
					<td nowrap="nowrap">
						<table ><tr class="row1">
							<td nowrap="nowrap" style="padding:2px 10px 2px 10px;"><?php echo _JLMS_PAYS_PAY_STATUS; ?>:&nbsp;&nbsp;</td>
							<td nowrap="nowrap" style="padding:2px 10px 2px 10px;"><?php echo $lists['status_filter'];?></td>
						</tr>
						<tr class="row1">
							<td nowrap="nowrap" style="padding:2px 10px 2px 10px;"><?php echo _JLMS_PAYS_PAY_PROC; ?>:&nbsp;&nbsp;</td>
							<td nowrap="nowrap" style="padding:2px 10px 2px 10px;"><?php echo $lists['proc_filter'];?></td>
						</tr>
						<tr class="row1">
							<td nowrap="nowrap" style="padding:2px 10px 2px 10px;"><?php echo _JLMS_PAYS_PAY_PERIOD; ?>:&nbsp;&nbsp;</td>
							<td nowrap="nowrap" style="padding:2px 10px 2px 10px;"><?php echo $lists['period_filter'];?></td>
						</tr>
						<tr class="row1">
							<td nowrap="nowrap" style="padding:2px 10px 2px 10px;"><?php echo _JLMS_SEARCH; ?>:&nbsp;&nbsp;</td>
							<td nowrap="nowrap" style="padding:2px 10px 2px 10px;">
								<input type="text" name="search_term" value="<?php echo str_replace('"', '&quot;', $lists['search_term']);?>" class="text_area" style="width:264px" />
							</td>
						</tr>
						</table>
					</td>
				</tr>
				</table>
				<?php } ?>				
				<table width="100%" border="0">
					<tr>
						<td valign="top">
							<table class="adminlist" width="100%">
							<thead>
								<tr>
									<th width="20px">#</th>
									<th width="20px" class="title"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>
									<th class="title"><?php echo _JLMS_PAYS_LIST_SUB_NAME; ?></th>
									<th class="title"><?php echo _JLMS_USERNAME; ?></th>
									<th class="title"><?php echo _JLMS_PAYS_PAY_STATUS;?></th>
									<th class="title"><?php echo _JLMS_PAYS_PAY_METHOD;?></th>
									<th class="title"><?php echo _JLMS_DATE; ?></th>
									<th class="title"><?php echo _JLMS_PAYS_LIST_TXN_ID; ?></th>
									<th class="title"><?php echo _JLMS_PAYS_AMOUNT; ?></th>
									<th class="title"><?php echo _JLMS_PAYS_LIST_INVOICE; ?></th>
								</tr>
							</thead>
							<tfoot>
								<tr>
									<td colspan="10">
									<?php echo $pageNav->getListFooter(); ?>
									</td>
								</tr>
							</tfoot>
							<tbody>
							<?php
							$k = 0;							
							for ($i=0, $n=count($rows); $i < $n; $i++) {
								$row = $rows[$i];								
								
								$checked = mosHTML::idBox( $i, $row->id);?>
								<tr class="<?php echo "row$k"; ?>">
									<td><?php echo $pageNav->rowNumber( $i ); ?></td>
									<td><?php echo $checked; ?></td>
									<td align="left">
										<a href="index.php?option=com_joomla_lms&amp;task=editA_payment&amp;id=<?php echo $row->id?>" title="<?php echo _JLMS_PAYS_EDIT_PAYMENT; ?>"><?php echo ($row->payment_type == 1)?_JLMS_PAYS_CUSTOM_SUBS:(($row->payment_type == 2)? _JLMS_PAYS_PAYMENT_N.$row->id:($row->course_name?$row->course_name:_JLMS_PAYS_SUBS_INF_NOT_FOUND));?></a>
									</td>
									<td align="left">
										<?php echo $row->username ? $row->username : _JLMS_PAYS_USR_WAS_REM_FROM_J;?>
									</td>
									<td align="center">
										<?php echo ucfirst($row->status);?>
									</td>
									<td align="left">
										<?php echo $row->processor;?>
									</td>
									<td align="left">
										<?php echo $row->date;?>
									</td>
									<td align="left">
										<?php echo $row->txn_id;?>
									</td>
									<td>
									<span <?php echo (strtolower($row->status) == 'completed') ? '' : ' style="color: #aaaaaa;"';?>>
									<?php echo number_format($row->amount, 2, '.', '').'&nbsp;'.($row->cur_code ? $row->cur_code : $JLMS_CONFIG->get('jlms_cur_code'));?>
									</span>
									</td>
									<td align="left">
									<?php if($row->filename && is_file($row->filename) && file_exists($row->filename)) { ?>
										<a href="index.php?option=com_joomla_lms&amp;task=get_payment_invoice&amp;id=<?php echo $row->id?>" target="_blank"><?php echo _JLMS_PAYS_VIEW_INVOICE; ?></a>
									<?php } ?>
									</td>
								</tr>
								<?php
								$k = 1 - $k;
							}?>
							</tbody>
							</table>
						</td>
					</tr>
				</table>		
				<?php if (!$JLMS_CONFIG->get('hide_payments_summary', false)) { ?>		
				<table width="100%">
				<tr>
				<td width="80%"></td>
				<td align="left">			
				<table>
					<tr>
						<td><?php echo _JLMS_PAYS_TOTAL_COMP_PAYS ?>:</td><td><?php echo $params['count_completed']; ?></td>
					</tr>
				</table>
				<table>
					<?php 
					if( count( $params['total_compl_amount'] ) ) 
					{
						$itr = 0;
						foreach( $params['total_compl_amount'] AS $key => $value ) 
						{						
							$itr++;
						?>
						<tr>
							<td>
								<?php if( $itr == 1 ) 						
										echo _JLMS_PAYS_TOTAL_AMOUNT.':'; 
								?>
							</td>
							<td><?php echo $value.' '.$key; ?></td>
						</tr>
						<?php
						}
					} else {
					?>
						<tr>
							<td><?php echo _JLMS_PAYS_TOTAL_AMOUNT; ?>:</td>
							<td>0</td>
						</tr>
					<?php
					} 
					?>
				</table>
				</td>
				</tr>
				</table>	
				
			<?php } ?>						
		</td></tr></table>
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="task" value="payments" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0" />		
		</form>
		<?php
	}
	
	function JLMS_paymentsListPDF( $rows, $params ){
		global $JLMS_CONFIG;
		
		include( JPATH_SITE . DS .'components'. DS .'com_joomla_lms'. DS .'includes'. DS .'pdf'. DS .'lms.pdf.php' );
		
		$pdf = new JLMSPDF( 'P', 'mm', 'A4', true, 'UTF-8', false );  //A4 Portrait				
		$pdf->SetXY( 0, 0 );
		$pdf->SetMargins( 10, 20, 10, true );		
		$pdf->setFont( 'freesans' ); //choose font
		$pdf->setFontSize( 8 ); //choose font	
		$pdf->SetDrawColor( 0, 0, 0 );
		
		$header = "<br /><div align=\"center\">".$JLMS_CONFIG->get('sitename').' - '.strtolower( _JLMS_PAYS_PAYS_LIST )."</div>";
			
		$footer = "
			<hr />
			<table width=\"100%\">
			    <tr>
			        <td align=\"left\">".$JLMS_CONFIG->get('live_site')."</td>
		";
		
		if ($JLMS_CONFIG->get('is_trial')) {
			$footer .= "<td align=\"center\">Powered by JoomlaLMS (www.joomlalms.com)</td>";
		}
		
		$footer .= "
			        <td align=\"right\">". date( 'j F, Y, H:i', time() + $JLMS_CONFIG->get('offset') * 60 * 60 ).'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$pdf->getAliasNumPage().' / '.$pdf->getAliasNbPages()."</td>
			    </tr>		  
			</table>
		";
	
		$pdf->setHeaderFont( array('freesans','',6) );
		$pdf->setHeaderHTML( $header );
		
		$pdf->setFooterMargin( 5 );
		$pdf->setFooterFont( array('freesans','',6) );	
		$pdf->setFooterHTML( $footer );	
			
		$pdf->AddPage();
		
		ob_clean();
		ob_start();				
		?>			
		
			<table width="100%">		
				<tr>
					<th width="5%" align="center"><strong>#</strong></th>									
					<th width="15%" align="center"><strong><?php echo _JLMS_PAYS_LIST_SUB_NAME; ?></strong></th>
					<th width="20%" align="center"><strong><?php echo _JLMS_USERNAME; ?></strong></th>
					<th width="10%" align="center"><strong><?php echo _JLMS_PAYS_PAY_STATUS;?></strong></th>
					<th width="20%" align="center"><strong><?php echo _JLMS_PAYS_PAY_METHOD;?></strong></th>
					<th width="20%" align="center"><strong><?php echo _JLMS_DATE; ?></strong></th>									
					<th width="10%"><strong><?php echo _JLMS_PAYS_AMOUNT; ?></strong></th>						
				</tr>	
				<tr><td colspan="7"><hr /></td></tr>	
			<?php
			$k = 0;			
			for ($i=0, $n=count($rows); $i < $n; $i++) {
				$row = $rows[$i];
				?>
				<tr>
					<td align="center"><?php echo $i+1; ?></td>									
					<td align="center">
						<?php echo ($row->payment_type == 1)?_JLMS_PAYS_CUSTOM_SUBS:(($row->payment_type == 2)? _JLMS_PAYS_PAYMENT_N.$row->id:($row->course_name?$row->course_name:_JLMS_PAYS_SUBS_INF_NOT_FOUND));?>
					</td>
					<td align="center">
						<?php echo $row->username ? $row->username : _JLMS_PAYS_USR_WAS_REM_FROM_J;?>
					</td>
					<td align="center">
						<?php echo ucfirst($row->status);?>
					</td>
					<td align="center">
						<?php echo $row->processor;?><br />
						<?php echo $row->txn_id;?>
					</td>
					<td align="center">
						<?php echo $row->date;?>
					</td>									
					<td align="center">
					<span <?php echo (strtolower($row->status) == 'completed') ? '' : ' style="color: #aaaaaa;"';?>>
					<?php echo number_format($row->amount, 2, '.', '').'&nbsp;'.($row->cur_code ? $row->cur_code : $JLMS_CONFIG->get('jlms_cur_code'));?>
					</span>
					</td>									
				</tr>
				<?php
				$k = 1 - $k;
			}?>			
			</table>
			<br />
			<br />
			<br />	
		<?php if (!$JLMS_CONFIG->get('hide_payments_summary', false)) { ?>			
			<table width="100%">
			<tr>
			<td width="60%"></td>
			<td align="left">
			<table>
				<tr>
					<td><?php echo _JLMS_PAYS_TOTAL_COMP_PAYS ?>:</td><td><?php echo $params['count_completed']; ?></td>
				</tr>
			</table>
			<table>
				<?php 
				if( count( $params['total_compl_amount'] ) ) 
				{
					$itr = 0;
					foreach( $params['total_compl_amount'] AS $key => $value ) 
					{						
						$itr++;
					?>
					<tr>
						<td>
							<?php if( $itr == 1 ) 						
									echo _JLMS_PAYS_TOTAL_AMOUNT.':'; 
							?>
						</td>
						<td><?php echo $value.' '.$key; ?></td>
					</tr>
					<?php
					}
				} else {
				?>
					<tr>
						<td><?php echo _JLMS_PAYS_TOTAL_AMOUNT; ?>:</td>
						<td>0</td>
					</tr>
				<?php
				} 
				?>
			</table>
			</td>
			</tr>
			</table>					
		<?php
		}
		$content = ob_get_contents();
		ob_end_clean();
		
		$pdf->writeHTML( $content );
		$pdf->Output( 'payments_list', 'I' );	
		
		die();	
	}
	
	function JLMS_salesReport( $rows, $pageNav, $option, $lists ){
		global $JLMS_CONFIG;
		JHTML::_('behavior.tooltip');		
		?>
		<script type="text/javascript">
			function submitbutton(pressbutton) 
			{
				var old_task = document.adminForm.task.value;
								
				submitform(pressbutton);				
				
				document.adminForm.task.value = old_task;
			}
			
			<?php if( JLMS_J16version() ) { ?>
			Joomla.submitbutton = submitbutton;
			<?php } ?>
		</script>
		<form action="index.php" method="post" name="adminForm">		
		<table width="100%" >
			<tr>
			<td valign="top" width="220">
			<div>
				<?php echo joomla_lms_adm_html::JLMS_menu();?>
			</div>
			</td>
			<td valign="top">					
				<table cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td align="left" width="100%">&nbsp;</td>
					<td nowrap="nowrap">
						<table class="adminlist">
						<tr class="row1">						
							<td nowrap="nowrap" style="padding:2px 10px 2px 10px;"><?php echo _JLMS_PAYS_SUBS_STATUS; ?>:&nbsp;&nbsp;</td>
							<td nowrap="nowrap" style="padding:2px 10px 2px 10px;"><?php echo $lists['status_filter'];?></td>
						</tr>						
						<tr class="row1">
							<td nowrap="nowrap" style="padding:2px 10px 2px 10px;"><?php echo _JLMS_PAYS_PAY_PERIOD; ?>:&nbsp;&nbsp;</td>
							<td nowrap="nowrap" style="padding:2px 10px 2px 10px;"><?php echo $lists['period_filter'];?></td>
						</tr>
						<tr class="row1">
							<td nowrap="nowrap" style="padding:2px 10px 2px 10px;"><?php echo _JLMS_SEARCH; ?>:&nbsp;&nbsp;</td>
							<td nowrap="nowrap" style="padding:2px 10px 2px 10px;">
								<input type="text" name="search_term" value="<?php echo str_replace('"', '&quot;', $lists['search_term']);?>" class="text_area" style="width:264px" />
							</td>
						</tr>
						</table>
					</td>
				</tr>
				</table>							
				<table width="100%" border="0">
					<tr>
						<td valign="top">
							<table class="adminlist" width="100%">
							<thead>
								<tr>
									<th width="20px">#</th>									
									<th width="80%" class="title"><?php echo _JLMS_PAYS_SUBS_NAME; ?></th>
									<th class="title"><?php echo _JLMS_PAYS_NUMB_OF_ORDERS; ?></th>									
								</tr>
							</thead>
							<tfoot>
								<tr>
									<td colspan="10">
									<?php echo $pageNav->getListFooter(); ?>
									</td>
								</tr>
							</tfoot>
							<tbody>
							<?php
							$k = 0;							
							for ($i=0, $n=count($rows); $i < $n; $i++) {
								$row = $rows[$i];								
								
								$checked = mosHTML::idBox( $i, $row->id);?>
								<tr class="<?php echo "row$k"; ?>">
									<td><?php echo $pageNav->rowNumber( $i ); ?></td>									
									<td width="80%" align="left">
										<a href="index.php?option=com_joomla_lms&amp;task=editA_subscription&amp;id=<?php echo $row->id?>" title="<?php echo _JLMS_PAYS_EDIT_SUBS; ?>"><?php echo $row->name; ?></a>
									</td>	
									<td align="center">
										<?php echo $row->count; ?>
									</td>								
								</tr>
								<?php
								$k = 1 - $k;
							}?>
							</tbody>
							</table>
						</td>
					</tr>
				</table>								
		</td></tr></table>
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="task" value="sales_report" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0" />		
		</form>
		<?php
	}
	
	function JLMS_salesReportPDF( $rows ){
			global $JLMS_CONFIG;
		
		include( JPATH_SITE . DS .'components'. DS .'com_joomla_lms'. DS .'includes'. DS .'pdf'. DS .'lms.pdf.php' );
		
		$pdf = new JLMSPDF( 'P', 'mm', 'A4', true, 'UTF-8', false );  //A4 Portrait				
		$pdf->SetXY( 0, 0 );
		$pdf->SetMargins( 10, 20, 10, true );		
		$pdf->setFont( 'freesans' ); //choose font
		$pdf->setFontSize( 8 ); //choose font	
		$pdf->SetDrawColor( 0, 0, 0 );
		
		$header = "<br /><div align=\"center\">".$JLMS_CONFIG->get('sitename').' - '.strtolower( _JLMS_PAYS_SALES_REPORT )."</div>";
			
		$footer = "
			<hr />
			<table width=\"100%\">
			    <tr>
			        <td align=\"left\">".$JLMS_CONFIG->get('live_site')."</td>
		";
		
		if ($JLMS_CONFIG->get('is_trial')) {
			$footer .= "<td align=\"center\">Powered by JoomlaLMS (www.joomlalms.com)</td>";
		}
		
		$footer .= "
			        <td align=\"right\">". date( 'j F, Y, H:i', time() + $JLMS_CONFIG->get('offset') * 60 * 60 ).'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$pdf->getAliasNumPage().' / '.$pdf->getAliasNbPages()."</td>
			    </tr>		  
			</table>
		";
	
		$pdf->setHeaderFont( array('freesans','',6) );
		$pdf->setHeaderHTML( $header );
		
		$pdf->setFooterMargin( 5 );
		$pdf->setFooterFont( array('freesans','',6) );	
		$pdf->setFooterHTML( $footer );	
			
		$pdf->AddPage();
		
		ob_clean();
		ob_start();				
		?>		
		
			<table width="100%">		
				<tr>
					<th width="5%" align="center"><strong>#</strong></th>									
					<th width="80%"><strong><?php echo _JLMS_PAYS_SUBS_NAME; ?></strong></th>
					<th width="15%"><strong><?php echo _JLMS_PAYS_NUMB_OF_ORDERS; ?></strong></th>						
				</tr>	
				<tr><td colspan="3"><hr /></td></tr>	
			<?php
			$k = 0;			
			for ($i=0, $n=count($rows); $i < $n; $i++) {
				$row = $rows[$i];
				?>
				<tr>
					<td width="5%" align="center"><?php echo $i+1; ?></td>									
					<td width="80%" align="left">
						<?php echo $row->name; ?>
					</td>	
					<td width="15%" align="center">
						<?php echo $row->count; ?>
					</td>									
				</tr>
				<?php				
			}?>			
			</table>					
		<?php
		$content = ob_get_contents();
		ob_end_clean();
		
		$pdf->writeHTML( $content );
		$pdf->Output( 'sales_report', 'I' );	
		
		die();	
	}

	function JLMS_editPaymentInfo_HTML( $payments, $order, $option, $lists, $courses_in ){		
	?>
		<form action="index.php" method="post" name="adminForm">		
		<table width="100%" cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td valign="top" width="220">
				<div>
					<?php joomla_lms_adm_html::JLMS_menu();?>
				</div>
				</td>
				<td valign="top">	
				<div class="width-100">
				<fieldset class="adminform">			
				<?php if (!class_exists('JToolBarHelper')) { ?>
					<table class="adminheading">
						<tr>
							<th class="edit">
							<?php echo _JOOMLMS_COMP_NAME;?>: <small><?php echo _JLMS_PAYS_PAY_DETAILS; ?></small>
							</th>
							<td align="right"><?php //echo $lists['status_filter']?></td>
						</tr>
					</table>
				<?php } ?>				
					<table width="100%" border="0">
						<tr>
							<td width="30%" valign="top">
								<table class="adminlist">
									<tr>
										<th colspan="2" align="left"><?php echo _JLMS_MAIN; ?></th>
									</tr>
									<tr>
										<td align="left" height="47px" width="35%"><strong><?php echo _JLMS_STATUS; ?>:</strong></td>
										<td align="center" width="65%"><?php echo $lists['status'];?></td>
									</tr>
									<tr>
										<th colspan="2" align="left"><?php echo ($order->payment_type == 2) ? _JLMS_PAYS_PAY_DETAILS.':' : _JLMS_PAYS_SUBS_DETAILS.':';?></th>
									</tr>
								<?php if ($order->payment_type == 2) {
									$k = 1;
									foreach ($lists['subscriptions'] as $sub1) {
										echo '<tr class="row'.$k.'"><td colspan="2">'.$sub1.'</td></tr>';
										$k = 1 - $k;
									} ?>
									<tr>
										<td colspan="2">&nbsp;</td>
									</tr>
									<tr>
										<th colspan="2" align="left"><?php echo _JLMS_PAYS_INVOICE_DETAILS; ?>:</th>
									</tr>
									<tr>
										<td><?php echo _JLMS_PAYS_INVOICE_ID; ?>:</td>
										<td><?php echo ( isset($order->invoice_file) && $order->invoice_file ) ? $order->id : ' -' ?></td>
									</tr>
									<tr>
										<td><?php echo _JLMS_PAYS_INVOICE_NUM;?>:</td>
										<td><?php echo ( isset($order->invoice_file) && $order->invoice_file ) ? substr($order->invoice_file, 0, -4) : ' -' ?></td>
									</tr>
									<?php if ( isset($order->invoice_file) && $order->invoice_file ) { ?>
									<tr>
										<td colspan="2" align="center"><a href="index.php?option=com_joomla_lms&amp;task=get_payment_invoice&amp;id=<?php echo $order->id; ?>" target="_blank"><?php echo _JLMS_PAYS_VIEW_INVOICE; ?></a></td>
									</tr>
									<?php } ?>
									<tr>
										<td colspan="2" align="center"><a href="index.php?option=com_joomla_lms&amp;task=gen_payment_invoice&amp;id=<?php echo $order->id; ?>"><?php echo ( isset($order->invoice_file) && $order->invoice_file ) ? _JLMS_PAYS_INVOICE_REGEN : _JLMS_PAYS_INVOICE_GENERATE;?></a></td>
									</tr>
								<?php } else { ?>
									<tr>
										<td align="left"><?php echo _JLMS_NAME; ?>:</td>
										<td><?php echo $order->sub_name;?></td>
									</tr>
									<tr>
										<td align="left"><?php echo _JLMS_TYPE; ?>:</td>
										<td>
										<?php
										if ($order->account_type=='' || $order->account_type=='1') {
											echo _JLMS_BASIC;
										}else if ($order->account_type=='2'){
											echo _JLMS_DATE_TO_DATE;
										}else if ($order->account_type=='3'){
											echo _JLMS_DATE_TO_LIFETIME;
										}else if ($order->account_type=='4'){
											echo _JLMS_X_DAYS_ACCESS.' ('.$order->access_days." ".(intval($order->access_days) > 1 ? _JLMS_DAYS : _JLMS_DAY).")";
										}else if ($order->account_type=='5'){
											echo _JLMS_WITH_DISCOUNT.' - '.round($order->discount,2)." %";
										}?>
										</td>
									</tr>
									<tr>
										<td colspan="2" align="left"><?php echo _JLMS_PAYS_ACCESS_PERIOD; ?>:</td>
									</tr>
									<tr>
										<td align="left"><?php echo _JLMS_START_DATE; ?>:</td>
										<td align="left"><?php echo _JLMS_END_DATE; ?>:</td>
									</tr>
									<tr>
										<td align="left">
											<?php
											echo $order->start_date;
											?>
										</td>
										<td align="left">
										<?php
										if ($order->account_type=='2' ) {
											echo $order->end_date;
										}else if ($order->account_type=='' || $order->account_type=='1' || $order->account_type=='3' || $order->account_type=='5' ){
											echo _JLMS_PAYS_LIFETIME;
										}else if ($order->account_type=='4'){
											echo date('Y-m-d',strtotime("+".$order->access_days." "._JLMS_DAYS." ", strtotime($order->start_date)));
											}?>
										</td>
									</tr>
									<tr>
										<td align="left" colspan="2">&nbsp;</td>
									</tr>
								<?php  }?>
								</table>
							</td>
							<td valign="top" width="70%">
								<table class="adminlist" width="100%">
									<tr>
										<th colspan="2"><?php echo _JLMS_PAYS_USER_INFO; ?></th>
									</tr>
									<tr>
										<td><?php echo _JLMS_USERNAME; ?>:</td>
										<td><?php echo $order->username?></td>
									</tr>
									<tr>
										<td><?php echo _JLMS_EMAIL; ?>:</td>
										<td><?php echo $order->email?></td>
									</tr>
									<tr>
										<th colspan="2" ><?php echo _JLMS_PAYS_ORDER_INFO; ?></th>
									</tr>
									<tr>
										<td width="100px"><?php echo _JLMS_PAYS_ORDER_ID; ?>:</td>
										<td><?php echo $order->id?></td>
									</tr>
									<tr>
										<td><?php echo _JLMS_STATUS; ?>:</td>
										<td><b><?php echo $order->status?></b></td>
									</tr>
									<tr>
										<td><?php echo _JLMS_PROCESSOR; ?>:</td>
										<td><b><?php echo $order->processor?></b></td>
									</tr>
									<tr>
										<td><?php echo _JLMS_PAYS_TRANS_NUM; ?>:</td>
										<td><?php echo $order->txn_id ? $order->txn_id : ' - ';?></td>
									</tr>
									<tr>
										<td><?php echo _JLMS_PAYS_TRANS_DATE; ?>:</td>
										<td><input class="text_area" type="text" name="p_date" size="23" maxlength="19" value="<?php echo $order->date?>" /></td>
									</tr>
									<tr>
										<td align="left"><?php echo _JLMS_AMOUNT; ?>:</td>
										<td><?php echo round($order->amount,2);?></td>
									</tr>
								</table>
							</td>
						</tr>					
					</table>					
					</fieldset>
					<?php if( count($payments) > 1 ) { ?>
					<fieldset class="adminform">					
					<table class="adminlist">					
					<tr>
					<th width="5%" align="center"><strong>#</strong></th>					
						<th width="10%" align="center"><strong><?php echo 'ID';?></strong></th>
						<th width="10%" align="center"><strong><?php echo _JLMS_PAYS_PAY_STATUS;?></strong></th>						
						<th width="20%" align="center"><strong><?php echo _JLMS_DATE; ?></strong></th>									
						<th width="10%"><strong><?php echo _JLMS_PAYS_AMOUNT; ?></strong></th>						
					</tr>					
					<?php foreach( $payments AS $payment ) { ?>
					<th width="5%" align="center">#</td>					
						<td width="10%" align="center">
						<a href="index.php?option=com_joomla_lms&amp;task=editA_payment&amp;id=<?php echo $payment->id?>" title="<?php echo _JLMS_PAYS_EDIT_PAYMENT; ?>">
						<?php echo _JLMS_PAYS_PAYMENT_N.$payment->id;?>
						</a>
						</td>
						<td width="10%" align="center"><?php echo $payment->status;?></td>						
						<td width="20%" align="center"><?php echo $payment->date; ?></td>									
						<td width="10%"><?php echo ($payment->amount.' '.$payment->cur_code); ?></td>						
					</tr>		
					<?php } ?>
					</table>					
					</fieldset>
					<?php } ?>
					</div>				
				</td>
			</tr>
		</table>
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="old_status" value="<?php echo $order->status;?>" />
		<input type="hidden" name="username" value="<?php echo $order->username;?>" />
		<input type="hidden" name="hidemainmenu" value="0" />
		<input type="hidden" name="id" value="<?php echo $order->id;?>" />		
		</form>
		<?php
	}

	function JLMS_createPayment($option, &$lists ){
		mosCommonHTML::loadCalendar();
	?>
<script language="javascript" type="text/javascript">
<!--
function jlms_changeUserSelect(c_e) {
	var sel_value = c_e.options[c_e.selectedIndex].value;
	var sel1 = c_e.form.user_name;
	for (var i = 0; i < sel1.options.length; i++) {
		if (sel1.options[i].value == sel_value) {
			sel1.options[i].selected = true;
		}
	}
	var sel2 = c_e.form.user_email;
	for (var i = 0; i < sel2.options.length; i++) {
		if (sel2.options[i].value == sel_value) {
			sel2.options[i].selected = true;
		}
	}
	var sel3 = c_e.form.user_id;
	for (var i = 0; i < sel3.options.length; i++) {
		if (sel3.options[i].value == sel_value) {
			sel3.options[i].selected = true;
		}
	}
}
function submitbutton(pressbutton) {
	var form = document.adminForm;
	if (pressbutton == 'cancel_newpayment') {
		form.task.value = pressbutton;
		form.submit();
		return;
	}
	if (pressbutton == 'save_newpayment') {
		var sel_user = form.user_name.options[form.user_name.selectedIndex].value;
		if (sel_user == 0 || sel_user == '0') {
			alert('<?php echo _JLMS_PLS_SELECT_USER; ?>')
		} else {
			var sel_sub = form.item_id.options[form.item_id.selectedIndex].value;
			var sel_pm = form.payment_method.options[form.payment_method.selectedIndex].value;
			if (sel_sub == 0 || sel_sub == '0' || sel_pm == 0 || sel_pm == '0') {
				alert('<?php echo _JLMS_PAYS_SELECT_SUB_AND_PAYM_METHOD; ?>')
			} else {
				form.task.value = pressbutton;
				form.submit();
				return;
			}
		}
	}
}

<?php if( JLMS_J16version() ) { ?>
Joomla.submitbutton = submitbutton;
<?php } ?>
//-->
</script>
		<form action="index.php" method="post" name="adminForm">		
		<table width="100%" cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td valign="top" width="220">
				<div>
					<?php joomla_lms_adm_html::JLMS_menu();?>
				</div>
				</td>
				<td valign="top">	
				<div class="width-100">
				<fieldset class="adminform">		
				<?php if (!class_exists('JToolBarHelper')) { ?>
					<table class="adminheading">
						<tr>
							<th class="edit">
							<?php echo _JOOMLMS_COMP_NAME;?>: <small><?php echo _JLMS_PAYS_NEW_PAYMENT; ?></small>
							</th>
							<td align="right"><?php //echo $lists['status_filter']?></td>
						</tr>
					</table>
				<?php } ?>				
					<table width="100%" border="0" >
						<tr>
							<td width="30%" valign="top">
								<table class="adminlist">
									<tr>
										<th colspan="2" align="left"><?php echo _JLMS_PAYS_TBR_DETS; ?></th>
									</tr>
									<tr>
										<td align="left" height="47px" width="35%"><?php echo _JLMS_STATUS; ?>:</td>
										<td align="center" width="65%"><?php echo $lists['status'];?></td>
									</tr>
									<tr>
										<td>
										<?php echo _JLMS_SUBSCRIPTION; ?>:
										</td>
										<td>
											<?php echo $lists['subscriptions'];?> 
										</td>
									</tr>
								</table>
							</td>
							<td valign="top" width="70%">
								<table width="100%" class="adminlist">
									<tr>
										<th colspan="2"><?php echo _JLMS_USER_INFORMATION; ?></th>
									</tr>
									<tr>
										<td><?php echo _JLMS_USERNAME; ?>:</td>
										<td><?php echo $lists['users'];?></td>
									</tr>
									<tr>
										<td><?php echo _JLMS_NAME; ?>:</td>
										<td><?php echo $lists['users_names'];?></td>
									</tr>
									<tr>
										<td><?php echo _JLMS_EMAIL; ?>:</td>
										<td><?php echo $lists['users_emails'];?></td>
									</tr>
									<tr>
										<th colspan="2"><?php echo _JLMS_PAYS_ORDER_INFO; ?></th>
									</tr>
									<tr>
										<td><?php echo _JLMS_PROCESSOR; ?>:</td>
										<td><b><?php echo $lists['payment_methods'];?></b></td>
									</tr>
									<tr>
										<td><?php echo _JLMS_DATE; ?>:</td>
										<td>
										<?php
										if (class_exists('JHTML')) {
											$joomla_generated_code = JHTML::_('calendar', date('Y-m-d'), 'p_date', 'p_date', '%Y-%m-%d', array('class' => 'text_area'));
											//ignore joomla generated code ;)
											echo '<input type="text" name="p_date" id="p_date" value="'.htmlspecialchars(date('Y-m-d'), ENT_COMPAT, 'UTF-8').'" size="10" maxlength="10" />&nbsp;'.
												 '<img class="calendar" src="'.JURI::root().'templates/system/images/calendar.png" alt="calendar" id="p_date_img" align="absbottom" />';

										} else { ?>
											<input class="text_area" type="text" name="p_date" id="p_date" size="10" maxlength="10" value="<?php echo date('Y-m-d');?>" />
											<input type="button" class="button" value="..." onclick="showCalendar('p_date', 'y-mm-dd');return showCalendar('p_date', 'y-mm-dd');" />
										<?php } ?>
										</td>
									</tr>
									<tr>
										<td align="left"><?php echo _JLMS_PAYS_AMOUNT;?>:</td>
										<td><input class="text_area" type="text" name="p_amount" size="10" maxlength="10" value="0.00" /></td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
					</fieldset>
					</div>				
				</td>
			</tr>
		</table>
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="old_status" value="<?php echo $order->status;?>" />
		<input type="hidden" name="username" value="<?php echo $order->username;?>" />
		<input type="hidden" name="hidemainmenu" value="0" />
		<input type="hidden" name="id" value="<?php echo $order->id;?>" />		
		</form>
		<?php
	}

	function JLMS_editPaymentInfo_STEP2_HTML($status, $old_status, $courses_in, $id, $username, $option){
	?>
	<form action="index.php" method="post" name="adminForm">	
	<table width="100%" cellpadding="0" cellspacing="0" border="0">
		<tr>
			<td valign="top" width="220">
			<div>
				<?php joomla_lms_adm_html::JLMS_menu();?>
			</div>
			</td>
			<td valign="top">		
			<?php if (!class_exists('JToolBarHelper')) { ?>
				<table class="adminheading">
					<tr>
						<th class="edit">
						<small>
						<?php echo _JLMS_PAYS_CHANGED_PAY_STATUS; ?> <b style="color:#333333 "><?php echo $status;?></b>.<br />
						<?php /*You had changed payment details. Now status is <b style="color:#333333 "><?php echo $status;?></b>.<br />*/ ?>
						<?php echo (strtolower($status) == 'completed') ? str_replace( '{username}', $username, _JLMS_PAYS_YOU_WANT_SUBS_USR ) : str_replace( '{username}', $username, _JLMS_PAYS_YOU_WANT_DEL_USR ) ;?>
						</div><br />
						</small>
						</th>
						<td align="right"><?php //echo $lists['status_filter']?></td>
					</tr>
				</table>
			<?php } else {
				$app = & JFactory::getApplication('administrator');
				$msg = _JLMS_PAYS_CHANGED_PAY_STATUS.' <b style="color:#333333 ">'.$status.'</b>. '.( (strtolower($status) == 'completed') ? str_replace( '{username}', $username, _JLMS_PAYS_YOU_WANT_SUBS_USR ) : str_replace( '{username}', $username, _JLMS_PAYS_YOU_WANT_DEL_USR ));
				$app->enqueueMessage($msg);
			} ?>				
				<table width="100%" border="0" class="adminlist">
					<tr>
						<td colspan="4"><b><?php echo _JLMS_PAYS_COURSES_IN_THE_SUBS; ?>:</b></td>
					</tr>
					<tr>
						<th width="15px" align="center">#</th>
						<th width="20px" align="center"><input type="checkbox" name="toggle" checked value="" onclick="checkAll(<?php echo count($courses_in); ?>);" /></th>
						<th align="left"><?php echo _JLMS_PAYS_COURSE_NAME; ?></th>
						<th></th>
					</tr>
					<?php
					$k = 0;
					for ($i=0, $n=count($courses_in); $i < $n; $i++) {
						$row = $courses_in[$i];

						$checked = joomla_lms_adm_html::jlms_idBox( $i, $row->id);?>
						<tr class="<?php echo "row$k"; ?>">
							<td align="center"><?php echo ($i+1); ?></td>
							<td align="center"><?php echo $checked; ?></td>
							<td align="left">
								<?php echo $row->course_name;?>
							</td>
							<td></td>
						</tr>
						<?php
						$k = 1 - $k;
					}?>
				</table>			
			</td>
		</tr>
	</table>
	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="old_status" value="<?php echo $old_status;?>" />
	<input type="hidden" name="hidemainmenu" value="0" />
	<input type="hidden" name="boxchecked" value="<?php echo count($courses_in);?>" />
	<input type="hidden" name="id" value="<?php echo $id?>" />	
	</form>
	<?php
	}
	function jlms_idBox($rowNum, $recId){
		return '<input type="checkbox" id="cb'.$rowNum.'" name="cid[]" value="'.$recId.'" checked onclick="isChecked(this.checked);" />';
	}

	##########################################################################
	###	--- ---   JLMS show config	 --- --- ###
	##########################################################################
	function JLMS_cLook_Feel( &$row, &$lists, $option) {
		JHTML::_('behavior.tooltip');
		?>
		<script language="javascript" type="text/javascript">
		<!--
		function setgood() {
			return true;
		}
		function submitbutton(pressbutton) {
			var form = document.adminForm;
		<?php
			if (class_exists('JFactory')) {
				$editor =& JLMS07062010_JFactory::getEditor();
				echo $editor->save( 'frontpage_text_guest' );
			} else {
				getEditorContents( 'editor3', 'frontpage_text_guest' ) ;
			}
		?>
			try {
				form.onsubmit();
			} catch(e) {
				//alert(e);
			}
			var form2 = document.adminForm_text_guest;
			try {
				form2.onsubmit();
			} catch(e) {
				//alert(e);
			}
			form.frontpage_text_guest.value = form2.frontpage_text_guest.value;
			submitform( pressbutton );
		}
		
		<?php if( JLMS_J16version() ) { ?>
		Joomla.submitbutton = submitbutton;
		<?php } ?>
		//-->
		</script>
		
		<?php
		
		//for table
		$colgroup = '
		<colgroup>
			<col width="180">
		</colgroup>
		';
		
		?>
		
	<div>
<table width="100%" >
	<tr>
		<td width="220" valign="top">
		<div>
			<?php echo joomla_lms_adm_html::JLMS_menu();?>
		</div>
		</td>
		<td valign="top">
		<div class="width-100">
		<fieldset class="adminform">
		<?php if (!class_exists('JToolBarHelper')) { ?>
		<table class="adminheading">
		<tr>
			<th class="config"><?php echo _JOOMLMS_COMP_NAME;?>:
				<small>
					<?php echo _JLMS_LF_APPEARANCE; ?>
				</small>
			</th>
		</tr>
		</table>
		<?php } ?>
		<table width="100%" border="0">
			<tr>
				<td valign="top" >				
	<form action="index.php" method="post" name="adminForm" onsubmit="setgood();">	
				<table >
					<tr>
					<td>
					<div class="width-100">
					<fieldset class="adminform"><legend><?php echo _JLMS_LF_JLMS_TOP_MENU; ?></legend>
					<table cellpadding="0" cellspacing="0">
						<?php echo $colgroup;?>
						<tr><td valign="top"><?php echo _JLMS_LF_SHOW_TOP_MENU; ?>:</td>
							<td>
							<fieldset class="radio">
							<?php echo $lists['lofe_show_top'];?>
							</fieldset>
							</td>
						</tr>
						<tr><td valign="top"><?php echo _JLMS_LF_TOP_MENU_STYLE; ?>:</td>
							<td>
								<fieldset class="radio">
								<?php echo $lists['lofe_menu_style'];?>
								</fieldset>
							</td>
						</tr>
						<tr><td valign="top"><?php echo _JLMS_LF_SHOW_MENU_ITEM_HEAD; ?>:</td>
							<td>
							<fieldset class="radio">
							<?php echo $lists['lofe_show_head'];?>
							</fieldset>
							</td>
						</tr>
					</table>
					</fieldset>
					</div>
					</td>
					</tr>
					<tr>
					<td>
					<div class="width-100">
					<fieldset class="adminform"><legend><?php echo _JLMS_LF_COURSE_SELECTBOX; ?></legend>
					<table cellpadding="0" cellspacing="0">
						<?php echo $colgroup;?>
						<tr><td valign="top"><?php echo _JLMS_LF_SHOW_COURSE_SELB; ?>:</td>
							<td>
							<fieldset class="radio">
							<?php echo $lists['lofe_show_course_box'];?>
							</fieldset>
							</td>
						</tr>						
					</table>
					</fieldset>
					</div>
					</td>
					</tr>
					<?php
					//Show Status Lpaths/Scorms //by Max - 25.02.2011
					?>
					<tr>
						<td>
						<div class="width-100">
							<fieldset class="adminform">
								<legend><?php echo _JLMS_LF_RESULTS_DISPLAY_OPTIONS;?></legend>
								<table cellpadding="0" cellspacing="0">
									<?php echo $colgroup;?>
									<tr>
										<td valign="top">
											<?php echo _JLMS_LF_SCORM_STATUS_AS; ?>:
										</td>
										<td>
											<fieldset class="radio">
												<?php echo $lists['scorm_status_as'];?>
											</fieldset>
										</td>
									</tr>
									<tr>
										<td valign="top">
											<?php echo _JLMS_LF_LPATH_STATUS_AS; ?>:
										</td>
										<td>
											<fieldset class="radio">
												<?php echo $lists['lpath_status_as'];?>
											</fieldset>
										</td>
									</tr>
									<tr>
										<td valign="top">
											<?php echo _JLMS_LF_QUIZ_STATUS_AS; ?>:
										</td>
										<td>
											<fieldset class="radio">
												<?php echo $lists['quiz_status_as'];?>
											</fieldset>
										</td>
									</tr>
								</table>
							</fieldset>
						</div>
						</td>
					</tr>

					<tr>
					<td>
					<div class="width-100">
					<fieldset class="adminform"><legend><?php echo _JLMS_LF_META_DATA; ?></legend>
					<table cellpadding="0" cellspacing="0">
						<?php echo $colgroup;?>
						<tr>
							<td><?php echo _JLMS_LF_META_DESC; ?>:</td>
							<td>
								<textarea class="text_area" cols="60" rows="2" style="width:500px; height:40px" name="meta_desc"><?php echo $row->meta_desc; ?></textarea>
							</td>
						</tr>
						<tr>
							<td><?php echo _JLMS_LF_META_KEYW; ?>:</td>
							<td>
								<textarea class="text_area" cols="60" rows="2" style="width:500px; height:40px" name="meta_keys"><?php echo $row->meta_keys; ?></textarea>
							</td>
						</tr>
					</table>
					</fieldset>
					</div>
					</td>
					</tr>
					<tr>
					<td>
					<div class="width-100">
					<fieldset class="adminform"><legend><?php echo _JLMS_LF_HPAGE_CONFIG; ?></legend>
					<table cellpadding="0" cellspacing="0">
						<?php echo $colgroup;?>
						<tr><td valign="top"><?php echo _JLMS_LF_CH_COURSES_LI_MOD; ?>:</td>
							<td>
							<fieldset class="radio">
							<?php echo $lists['frontpage_courses'];?>
							</fieldset>
							</td>
						</tr>
						<tr><td valign="top"><?php echo _JLMS_LF_CH_COURSES_EXPAND_ALL; ?>:</td>
							<td>
							<fieldset class="radio">
							<?php echo $lists['frontpage_courses_expand_all'];?>
							</fieldset>
							</td>
						</tr>
						<tr><td valign="top"><?php echo _JLMS_LF_CH_ALL_COURSES_L; ?>:</td>
							<td>
							<fieldset class="radio">
							<?php echo $lists['frontpage_allcourses'];?>
							</fieldset>
							</td>
						</tr>
						<tr><td valign="top"><?php echo _JLMS_LF_CH_ANNOUNCEMENTS_M; ?>:</td>
							<td>
							<fieldset class="radio">
							<?php echo $lists['frontpage_announcements'];?>
							</fieldset>
							</td>
						</tr>
						<tr><td valign="top"><?php echo _JLMS_LF_CH_HOMEW_M; ?>:</td>
							<td>
							<fieldset class="radio">
							<?php echo $lists['frontpage_homework'];?>
							</fieldset>
							</td>
						</tr>
						<tr><td valign="top"><?php echo _JLMS_LF_CH_DROPB_M; ?> :</td>
							<td>
							<fieldset class="radio">
							<?php echo $lists['frontpage_dropbox'];?>
							</fieldset>
							</td>
						</tr>
						<tr><td valign="top"><?php echo _JLMS_LF_CH_MAILB_M; ?> :</td>
							<td>
							<fieldset class="radio">
							<?php echo $lists['frontpage_mailbox'];?>
							</fieldset>
							</td>
						</tr>
						<tr><td valign="top"><?php echo _JLMS_LF_CH_CRT_M; ?> :</td>
							<td>
							<fieldset class="radio">
							<?php echo $lists['frontpage_certificates'];?>
							</fieldset>
							</td>
						</tr>
						<tr><td valign="top"><?php echo _JLMS_LF_CH_LFP_M; ?> :</td>
							<td>
							<fieldset class="radio">
							<?php echo $lists['frontpage_latest_forum_posts'];?>
							</fieldset>
							</td>
						</tr>
						<tr><td><?php echo _JLMS_LF_NUM_ITEMS_IN_M; ?>:</td>
							<td><input class="text_area" type="text" name="homepage_items" size="3" maxlength="4" value="<?php echo $row->homepage_items; ?>"/>							
							</td>
						</tr>
						<tr><td valign="top"><?php echo _JLMS_LF_HOMEP_TEXT; ?>:</td>
							<td>
							<?php JLMS_editorArea( 'editor2', $row->frontpage_text, 'frontpage_text', '100%;', '250', '40', '20' ) ; ?>
							</td>
						</tr>
					</table>
					</fieldset>
					</div>
					</td>
					</tr>
				</table>
	<input type="hidden" name="option" value="<?php echo $option; ?>"/>
	<input type="hidden" name="boxchecked" value=""/>
	<input type="hidden" name="task" value=""/>
	<input type="hidden" name="frontpage_text_guest" value=""/>	
	</form>	
	</fieldset>
	</div>
				<table >
					<tr>
					<td>
					<form action="index.php" method="post" name="adminForm_text_guest" onsubmit="setgood();">
					<div class="width-100">
						<fieldset class="adminform"><legend><?php echo _JLMS_LF_HOMEP_TEXT_UNR_USRS; ?></legend>
							<table cellpadding="0" cellspacing="0">
								<tr>
									<td>
										<?php JLMS_editorArea( 'editor3', $row->frontpage_text_guest, 'frontpage_text_guest', '100%;', '250', '40', '20' ) ; ?>
									</td>
								</tr>
							</table>
						</fieldset>
					</div>
					</form>
					</td>
					</tr>
				</table>
				</td>
			</tr>
		</table>

		</td>
	</tr>
</table>

	</div>

<?php
	}

	function JLMS_CB_integration( &$row, &$lists, $option) {
		global $JLMS_CONFIG;
		JHTML::_('behavior.tooltip');
		$is_juser = false;
		if (file_exists(dirname(__FILE__)."/../../../components/com_juser/juser.php") && $JLMS_CONFIG->get('juser_integration', 0)) {
			$is_juser = true;
		}
	?>	
	<form action="index.php" method="post" name="adminForm">	
<table width="100%" >
	<tr>
		<td width="220" valign="top">
		<div>
			<?php echo joomla_lms_adm_html::JLMS_menu();?>
		</div>
		</td>
		<td valign="top">
		<div class="width-100">
		<fieldset class="adminform">
		<?php if (!class_exists('JToolBarHelper')) { ?>
		<table class="adminheading">
		<tr>
			<th class="config"><?php echo _JOOMLMS_COMP_NAME;?>:
				<small>
					<?php echo _JLMS_CBI_CB_INTEGRATION; ?>
				</small>
			</th>
		</tr>
		</table>
		<?php } ?>	
		<table width="100%" border="0">
			<tr>
				<td valign="top">
				<table >
					<tr>
						<th colspan="<?php echo $is_juser ? '6' : '5';?>" align="left">Global</th>
					</tr>
					<tr><td width="185" colspan="2"><?php echo _JLMS_CBI_IS_CB_INSTALLED; ?></td>
						<td align="left"><fieldset class="radio"><?php echo $lists['is_cb_installed'];?></fieldset></td>
						<td colspan="2"><?php echo _JLMS_CBI_SELECT_YES_IF_CB_INSTALLED; ?></td>
					</tr>
					<tr><th width="25"><input type="checkbox" disabled /></th>
						<th width="185"><b><?php echo _JLMS_CBI_JLMS_PROF_FIELD; ?>:</b></th>
						<th width="185"><b><?php echo _JLMS_CBI_CB_FIELD; ?>:</b></th>
						<?php echo $is_juser ? '<th width="185"><b>'._JLMS_CBI_JUSR_FIELD.':</b></th>' : '';?>
						<th><b><?php echo _JLMS_CBI_REMARKS; ?>:</b></th>
						<th><b><?php echo _JLMS_CBI_ASSOCIATION;?>:</b></th>
					</tr>
					<tr><td width="25"><input type="checkbox" disabled /></td>
						<td width="185"><?php echo _JLMS_ADDRESS; ?>:</td>
						<td><?php echo $lists['jlms_cb_address'];?></td>
						<?php echo $is_juser ? ('<td>'.$lists['jlms_juser_address'].'</td>') : '';?>
						<td><?php echo _JLMS_CBI_SHBE_TEXTF_TYPE; ?></td>
						<td><?php echo $lists['assoc'][0];?></td>
					</tr>
					<tr><td width="25"><input type="checkbox" disabled /></td>
						<td width="185"><?php echo _JLMS_COUNTRY; ?>:</td>
						<td><?php echo $lists['jlms_cb_country'];?></td>
						<?php echo $is_juser ? ('<td>'.$lists['jlms_juser_country'].'</td>') : '';?>
						<td><?php echo _JLMS_CBI_SHBE_TEXTF_TYPE; ?></td>
						<td><?php echo $lists['assoc'][4];?></td>
					</tr>
					<tr><td width="25"><input type="checkbox" disabled /></td>
						<td width="185"><?php echo _JLMS_CITY; ?>:</td>
						<td><?php echo $lists['jlms_cb_city'];?></td>
						<?php echo $is_juser ? ('<td>'.$lists['jlms_juser_city'].'</td>') : '';?>
						<td><?php echo _JLMS_CBI_SHBE_TEXTF_TYPE; ?></td>
						<td><?php echo $lists['assoc'][1];?></td>
					</tr>
					<tr><td width="25"><input type="checkbox" disabled /></td>
						<td width="185"><?php echo _JLMS_STATE_PROVINCE; ?>:</td>
						<td><?php echo $lists['jlms_cb_state'];?></td>
						<?php echo $is_juser ? ('<td>'.$lists['jlms_juser_state'].'</td>') : '';?>
						<td><?php echo _JLMS_CBI_SHBE_TEXTF_TYPE; ?></td>
						<td><?php echo $lists['assoc'][2];?></td>
					</tr>
					<tr><td width="25"><input type="checkbox" disabled /></td>
						<td width="185"><?php echo _JLMS_POSTAL_CODE; ?>:</td>
						<td><?php echo $lists['jlms_cb_postal_code'];?></td>
						<?php echo $is_juser ? ('<td>'.$lists['jlms_juser_postal_code'].'</td>') : '';?>
						<td><?php echo _JLMS_CBI_SHBE_TEXTF_TYPE; ?></td>
						<td><?php echo $lists['assoc'][3];?></td>
					</tr>
					<tr><td width="25"><input type="checkbox" disabled /></td>
						<td width="185"><?php echo _JLMS_PHONE; ?>:</td>
						<td><?php echo $lists['jlms_cb_phone'];?></td>
						<?php echo $is_juser ? ('<td>'.$lists['jlms_juser_phone'].'</td>') : '';?>
						<td><?php echo _JLMS_CBI_SHBE_TEXTF_TYPE; ?></td>
						<td><?php echo $lists['assoc'][5];?></td>
					</tr>
					<tr><td width="25"><input type="checkbox" disabled /></td>
						<td width="185"><?php echo _JLMS_LOCATION; ?>:</td>
						<td><?php echo $lists['jlms_cb_location'];?></td>
						<?php echo $is_juser ? ('<td>'.$lists['jlms_juser_location'].'</td>') : '';?>
						<td>Should be the 'Text Field' type</td>
						<td><?php echo $lists['assoc'][6];?></td>
					</tr>
					<tr><td width="25"><input type="checkbox" disabled /></td>
						<td width="185"><?php echo _JLMS_WEBSITE; ?>:</td>
						<td><?php echo $lists['jlms_cb_website'];?></td>
						<?php echo $is_juser ? ('<td>'.$lists['jlms_juser_website'].'</td>') : '';?>
						<td><?php echo _JLMS_CBI_SHBE_TEXTF_TYPE; ?></td>
						<td><?php echo $lists['assoc'][7];?></td>
					</tr>
					<tr><td width="25"><input type="checkbox" disabled /></td>
						<td width="185">ICQ:</td>
						<td><?php echo $lists['jlms_cb_icq'];?></td>
						<?php echo $is_juser ? ('<td>'.$lists['jlms_juser_icq'].'</td>') : '';?>
						<td><?php echo _JLMS_CBI_SHBE_TEXTF_TYPE; ?></td>
						<td><?php echo $lists['assoc'][8];?></td>
					</tr>
					<tr><td width="25"><input type="checkbox" disabled /></td>
						<td width="185">AIM:</td>
						<td><?php echo $lists['jlms_cb_aim'];?></td>
						<?php echo $is_juser ? ('<td>'.$lists['jlms_juser_aim'].'</td>') : '';?>
						<td><?php echo _JLMS_CBI_SHBE_TEXTF_TYPE; ?></td>
						<td><?php echo $lists['assoc'][9];?></td>
					</tr>
					<tr><td width="25"><input type="checkbox" disabled /></td>
						<td width="185">YIM:</td>
						<td><?php echo $lists['jlms_cb_yim'];?></td>
						<?php echo $is_juser ? ('<td>'.$lists['jlms_juser_yim'].'</td>') : '';?>
						<td><?php echo _JLMS_CBI_SHBE_TEXTF_TYPE; ?></td>
						<td><?php echo $lists['assoc'][10];?></td>
					</tr>
					<tr><td width="25"><input type="checkbox" disabled /></td>
						<td width="185">MSN:</td>
						<td><?php echo $lists['jlms_cb_msn'];?></td>
						<?php echo $is_juser ? ('<td>'.$lists['jlms_juser_msn'].'</td>') : '';?>
						<td><?php echo _JLMS_CBI_SHBE_TEXTF_TYPE; ?></td>
						<td><?php echo $lists['assoc'][11];?></td>
					</tr>
					<tr><td width="25"><input type="checkbox" disabled /></td>
						<td width="185"><?php echo _JLMS_COMPANY; ?>:</td>
						<td><?php echo $lists['jlms_cb_company'];?></td>
						<?php echo $is_juser ? ('<td>'.$lists['jlms_juser_company'].'</td>') : '';?>
						<td><?php echo _JLMS_CBI_SHBE_TEXTF_TYPE; ?></td>
						<td><?php echo $lists['assoc'][12];?></td>
					</tr>

					<?php
					for($i=0;$i<count($lists['new_fields']);$i++)
					{
						$cur_col = $lists['new_fields'][$i];
						$checked = mosHTML::idBox( $i, $cur_col['cb_id']);
						echo '<tr><td width="25">'.$checked.'</td>';
						echo '<td width="185">'.stripslashes($cur_col['cb_name']).':</td>';
						echo '<td>'.$cur_col['cb_field'].'</td>';
						echo $is_juser ? ('<td>disabled</td>') : '';
						echo '<td>'._JLMS_CBI_SHBE_TEXTF_TYPE.'</td>';
						echo '<td>{'.$cur_col['cb_assoc'].'}</td></tr>';
					}
					?>
					<tr>
						<td colspan="5">
						<?php echo _JLMS_CBI_ASSOC_LMS_FIELDS_W_CB; ?>
						</td>
					</tr>
				</table>
				</td>
			</tr>
		</table>
		</fieldset>
		</div>
		</td>
	</tr>
</table>

	<input type="hidden" name="option" value="<?php echo $option; ?>"/>
	<input type="hidden" name="boxchecked" value=""/>
	<input type="hidden" name="task" value=""/>
	<input type="hidden" name="hidemainmenu" value="0" />	
	</form>	
<?php
	}
	function JLMS_CB_integration_edit( &$row, &$lists, $option) {
		JHTML::_('behavior.tooltip');

	?>
	<script language="javascript" type="text/javascript">
	<!--
	function submitbutton(pressbutton) {
		var form = document.adminForm;
		if (pressbutton == 'cb_integration') {
			submitform( pressbutton );
			return;
		}
		else {
			if(form.field_name.value && form.cb_assoc.value)
			submitform( pressbutton );
			else
			alert("<?php echo _JLMS_CBI_SPECIFY_ALL_FIELDS; ?>");
		}
	}
	
	<?php if( JLMS_J16version() ) { ?>
	Joomla.submitbutton = submitbutton;
	<?php } ?>
	//-->
		</script>	
	<form action="index.php" method="post" name="adminForm">	
	<table width="100%" >
		<tr>
			<td width="220" valign="top">
			<div>
				<?php echo joomla_lms_adm_html::JLMS_menu();?>
			</div>
			</td>
			<td valign="top">
			<div class="width-100">
			<fieldset class="adminform">
			<?php if (!class_exists('JToolBarHelper')) { ?>
			<table class="adminheading">
			<tr>
				<th class="config"><?php echo _JOOMLMS_COMP_NAME;?>:
					<small>
						<?php if($row->id) echo _JLMS_CBI_EDIT_CB_INTEGR.' '; else echo _JLMS_CBI_NEW_CB_INTEGR.' '; ?>
					</small>
				</th>
			</tr>
			</table>

			<?php } ?>			
			<table width="100%" border="0">
			<tr>
				<td valign="top">
					<table >
						<tr>
							<td>
								<?php echo _JLMS_CBI_JLMS_PROF_FIELD; ?>:
							</td>
							<td>
								<input type="text" maxlength="100" name="field_name" value="<?php echo $row->field_name?stripslashes($row->field_name):'';?>" />
							</td>
						</tr>
						<tr>
							<td>
								<?php echo _JLMS_CBI_CB_PROF_FIELD; ?>:
							</td>
							<td>
								<?php echo $lists['cb_field'];?>
							</td>
						</tr>
						<tr>
							<td>
								<?php echo _JLMS_CBI_ASSOCIATION; ?>:
							</td>
							<td>
								<input type="text" maxlength="100" name="cb_assoc" value="<?php echo $row->cb_assoc?stripslashes($row->cb_assoc):'';?>" />
							</td>
						</tr>
					</table>
					<input type="hidden" name="option" value="<?php echo $option; ?>"/>
					<input type="hidden" name="boxchecked" value=""/>
					<input type="hidden" name="task" value=""/>
					<input type="hidden" name="hidemainmenu" value="0" />
					<input type="hidden" name="cb_id" value="<?php echo $row->id?>" />
				</td>
			</tr>
			</table>
			</fieldset>
			</div>
			</td>
		</tr>
	</table>	
	</form>	
		<?php
	}
	function JLMS_showMenuManage ($menus, $option, $pageNav, $menutype, &$lists, &$JLMS_config ){
		global $JLMS_CONFIG;
		JHTML::_('behavior.tooltip');
		require_once(JPATH_SITE.'/components/com_joomla_lms/languages/english/main.lang.php');

		?>		
		<form action="index.php" method="post" name="adminForm">		
		<table width="100%">
			<tr>
				<td width="220" valign="top">
				<div>
					<?php echo joomla_lms_adm_html::JLMS_menu();?>
				</div>
				</td>
				<td valign="top" align="right">				
				<?php if (!class_exists('JToolBarHelper')) { ?>
				<table class="adminheading" width="30%">
				<tr>
					<th class="menus"><?php echo _JOOMLMS_COMP_NAME;?>:
						<small>
							<?php echo _JLMS_MENUM; ?> <?php
							switch ($menutype){
								case -1 : echo _JLMS_MENUM_GUEST_M; break;
								case 0  : echo _JLMS_MENUM_HOMEPAGE_M; break;
								case 1  : echo _JLMS_MENUM_TEACHER_M; break;
								case 2  : echo _JLMS_MENUM_STUDENT_M; break;
								case 6  : echo _JLMS_MENUM_CEO_PARENT_M; break;
							}
							?>
						</small>
					</th>					
					<td align="right">
					<table class="adminlist"><tr class="row1">					
					<td><?php echo _JLMS_MENUM_SELECT_M_TYPE; ?>:</td>
					<td><?php echo $lists['menutype'];?></td>
					</tr></table>
					</td>
				</tr>
				</table>
				<?php } else { ?>
				<table cellpadding="0" cellspacing="0" border="0" style="width: 15%;" class="adminlist">
				<tr class="row1">										
					<td nowrap="nowrap" style="padding-right: 5px;"><?php echo _JLMS_MENUM_SELECT_M_TYPE; ?>:</td>
					<td><?php echo $lists['menutype'];?></td>					
				</tr>
				</table>
				<?php } ?>				
				<table class="adminlist">
					<thead>
						<tr>
							<th width="1%">
							#
							</th>
							<th width="1%">
							<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($menus); ?>);" />
							</th>
							<th class="title" width="5%">
							<?php echo _JLMS_MENUM_M_IMAGE; ?>
							</th>
							<th class="title" width="50%">
							<?php echo _JLMS_MENUM_M_ITEM; ?>
							</th>
							<th width="10%">							
							<?php echo _JLMS_PUBLISHED; ?>
							</th>
							<th colspan="2" width="1%">
							<?php echo _JLMS_REORDER; ?>
							</th>
							<th width="1%">
							<?php echo _JLMS_ORDER; ?>
							</th>
							<th width="1%">
							<a href="javascript: saveorder( <?php echo count( $menus )-1; ?> )"><img src="<?php echo ADMIN_IMAGES_PATH; ?>filesave.png" border="0" alt="<?php echo _JLMS_MENUM_SAVE_ORDER; ?>" /></a>
							</th>
							<th align="center" width="2%">
								<?php echo _JLMS_ID_ITEM;?>
							</th>
						</tr>
					</thead>
					<tbody>
					<?php
					$disabled_ex = 0;
					$k = 0;
					$i = 0;
					$n = count( $menus );
					foreach ($menus as $menu) {
						if($menu->task == 'view_all_notices' && !$JLMS_CONFIG->get('flms_integration', 0)){
						
						} else {
							//$checked
							//$access 	= mosCommonHTML::AccessProcessing( $row, $i );
							$checked = mosHTML::idBox( $i, $menu->id);
							$published 	= mosCommonHTML::PublishedProcessing( $menu, $i );
							$img_published	= $menu->published ? 'tick.png' : 'publish_x.png';
							$task_published	= $menu->published ? 'unpublish_menu' : 'publish_menu';
							$alt_published 	= $menu->published ? 'Published' : 'Unpublished';
							$disabled = 0;
							if ($menu->lang_var == '_JLMS_TOOLBAR_CHAT' && !$JLMS_config->chat_enable){
								$disabled = 1;
							}elseif($menu->lang_var == '_JLMS_TOOLBAR_TRACK' && !$JLMS_config->tracking_enable){
								$disabled = 1;
							}elseif($menu->lang_var == '_JLMS_TOOLBAR_CONF' && !$JLMS_config->conference_enable){
								$disabled = 1;
							}elseif($menu->lang_var == '_JLMS_TOOLBAR_QUIZZES' && !$JLMS_config->plugin_quiz){
								$disabled = 1;
							}elseif($menu->lang_var == '_JLMS_TOOLBAR_FORUM' && !$JLMS_config->plugin_forum){
								$disabled = 1;
							}
	
							if($disabled == 1){
								$disabled_ex = 1;
								$img_published = 'disabled.png';
								$alt_published = 'Disabled';
								echo '<input type="checkbox" id="cb'.$i.'" name="cid[]" value="'.$menu->id.'" style="visibility:hidden" onclick="isChecked(this.checked);" />';
								$checked = "<img src='".ADMIN_IMAGES_PATH."checked_out.png' border='0' alt='".$alt_published."'  />";
							}
							?>
							<tr class="<?php echo "row$k"; ?>">
								<td align="center">
								<?php echo $i + 1;?>
								</td>
								<td align="center">
								<?php echo $checked; ?>
								</td>
								<td nowrap="nowrap" align="center">
								<?php
								if ($menu->is_separator){
									echo "---";
								}else{
									echo "<img src='".JURI::root()."components/com_joomla_lms/lms_images/toolbar/".$menu->image."' alt='".$JLMS_LANGUAGE[$menu->lang_var]."' title='".$JLMS_LANGUAGE[$menu->lang_var]."' width='16' height = '16' />";
								}
								?>
								</td>
								<td nowrap="nowrap">
								<?php
								if ($menu->is_separator == 1){
									echo _JLMS_SEPARATOR;
								}else{
									echo $JLMS_LANGUAGE[$menu->lang_var];
								}
								?>
								</td>
								<td width="10%" align="center">
									<?php if ($img_published == 'disabled.png'){?>
										<img src="<?php echo ADMIN_IMAGES_PATH.$img_published;?>" border="0" alt="<?php echo $alt_published; ?>" title="<?php echo $alt_published; ?>" />
									<?php }else{?>
									<a href="javascript: void(0);" onclick="return listItemTask('cb<?php echo $i;?>','<?php echo $task_published;?>')">
										<img src="<?php echo ADMIN_IMAGES_PATH.$img_published;?>" border="0" alt="<?php echo $alt_published; ?>" title="<?php echo $alt_published; ?>" />
									</a>
									<?php }?>
								</td>
								<td class="order">
								<?php echo $pageNav->orderUpIcon( $i ); ?>
								</td>
								<td class="order">
								<?php echo $pageNav->orderDownIcon( $i, $n ); ?>
								</td>
								<td align="center" colspan="2">
								<input type="text" name="order[]" size="5" value="<?php echo $menu->ordering; ?>" class="text_area" style="text-align: center" />
								</td>
								<td align="center"><?php echo isset($menu->id) && $menu->id ? $menu->id : '-';?></td>
							</tr>
							<?php
							$k = 1 - $k;
							$i++;
						}
					}
					?>
					</tbody>
					<?php
					if($disabled_ex){
						?>
						<tfoot>
							<tr>
								<td colspan="10"><img src='<?php echo ADMIN_IMAGES_PATH; ?>checked_out.png' border='0' alt=''  /> - <?php echo _JLMS_MENUM_MSG_MARKED_ITEMS; ?></td>
							</tr>
						</tfoot>
						<?php
					}
					?>
					</table>					
					<input type="hidden" name="option" value="<?php echo $option; ?>" />
					<input type="hidden" name="task" value="menu_manage" />
					<input type="hidden" name="boxchecked" value="0" />
					<input type="hidden" name="hidemainmenu" value="0" />

				</td>
			</tr>
		</table>		
		</form>
	<?php
	}

	function JLMS_showconfig( &$row, &$lists, &$periods, &$course_cats, &$gradebook_cats, $file_types, $option, $colors) {
		$tabs = new mosTabs(1);
		?>
		<link rel="stylesheet" href="<?php echo JURI::root();?>components/com_joomla_lms/includes/colorselector/color_select.css" type="text/css"/>
		<script type="text/javascript" src="<?php echo JURI::root();?>components/com_joomla_lms/includes/colorselector/color_select.js"></script>

		<script type="text/javascript"><!--//
		var colors = new Array();
		<?php for ($i =1; $i<=count($colors); $i++){
			echo "colors[".$i."] = new Array('".$colors[$i][0]."', '".$colors[$i][1]."','".$colors[$i][2]."','".$colors[$i][3]."','".$colors[$i][4]."','".$colors[$i][5]."','".$colors[$i][6]."'); \r\n";
		};?>
		function change_colors(index){
			if (index != 0 ){
				document.adminForm.conf_background.value = colors[index][0];//"#E0DFE4";
				document.adminForm.conf_main_color.value = colors[index][1];//"#F1F6CE";
				document.adminForm.conf_title_color.value = colors[index][2];//"#E7F1B2";
				document.adminForm.conf_border_color.value = colors[index][3];//"#999999";
				document.adminForm.conf_title_font_color.value = colors[index][4];//"#798730";
				document.adminForm.conf_toolbar_color.value = colors[index][5];//"#E0DFE4";
				document.adminForm.conf_files_font_color.value = colors[index][6];//"#666666";
			}

			cs1.setrgb(document.adminForm.conf_background.value);
			cs2.setrgb(document.adminForm.conf_main_color.value);
			cs3.setrgb(document.adminForm.conf_title_color.value);
			cs4.setrgb(document.adminForm.conf_border_color.value);
			cs5.setrgb(document.adminForm.conf_title_font_color.value);
			cs6.setrgb(document.adminForm.conf_toolbar_color.value);
			cs7.setrgb(document.adminForm.conf_files_font_color.value);
		}

		function cs1_change_update(new_color) {
			window.status = new_color;
			document.adminForm.conf_background.value = new_color;
			document.getElementById('color_display_icon1').style.background = new_color;
		}
		function cs2_change_update(new_color) {
			window.status = new_color;
			document.adminForm.conf_main_color.value = new_color;
			document.getElementById('color_display_icon2').style.background = new_color;
		}
		function cs3_change_update(new_color) {
			window.status = new_color;
			document.adminForm.conf_title_color.value = new_color;
			document.getElementById('color_display_icon3').style.background = new_color;
		}
		function cs4_change_update(new_color) {
			window.status = new_color;
			document.adminForm.conf_border_color.value = new_color;
			document.getElementById('color_display_icon4').style.background = new_color;
		}
		function cs5_change_update(new_color) {
			window.status = new_color;
			document.adminForm.conf_title_font_color.value = new_color;
			document.getElementById('color_display_icon5').style.background = new_color;
		}
		function cs6_change_update(new_color) {
			window.status = new_color;
			document.adminForm.conf_toolbar_color.value = new_color;
			document.getElementById('color_display_icon6').style.background = new_color;
		}
		function cs7_change_update(new_color) {
			window.status = new_color;
			document.adminForm.conf_files_font_color.value = new_color;
			document.getElementById('color_display_icon7').style.background = new_color;
		}


		function cs_init() {
			cs1 = new color_select('cs1', '<?php echo str_replace('#' , '0x', $row->conf_background);?>');
			cs2 = new color_select('cs2', '<?php echo str_replace('#' , '0x', $row->conf_main_color);?>');
			cs3 = new color_select('cs3', '<?php echo str_replace('#' , '0x', $row->conf_title_color);?>');
			cs4 = new color_select('cs4', '<?php echo str_replace('#' , '0x', $row->conf_border_color);?>');
			cs5 = new color_select('cs5', '<?php echo str_replace('#' , '0x', $row->conf_title_font_color);?>');
			cs6 = new color_select('cs6', '<?php echo str_replace('#' , '0x', $row->conf_toolbar_color);?>');
			cs7 = new color_select('cs7', '<?php echo str_replace('#' , '0x', $row->conf_files_font_color);?>');
			// spatially attach the color select to an element
			// (when triggered, it will always appear below this element)
			cs1.attach_to_element(document.getElementById("color_select_icon1"));
			cs2.attach_to_element(document.getElementById("color_select_icon2"));
			cs3.attach_to_element(document.getElementById("color_select_icon3"));
			cs4.attach_to_element(document.getElementById("color_select_icon4"));
			cs5.attach_to_element(document.getElementById("color_select_icon5"));
			cs6.attach_to_element(document.getElementById("color_select_icon6"));
			cs7.attach_to_element(document.getElementById("color_select_icon7"));
			//cs6.attach_to_element(jlms_getObj("color_select_icon6"));
			cs1.setrgb(document.adminForm.conf_background.value);
			cs2.setrgb(document.adminForm.conf_main_color.value);
			cs3.setrgb(document.adminForm.conf_title_color.value);
			cs4.setrgb(document.adminForm.conf_border_color.value);
			cs5.setrgb(document.adminForm.conf_title_font_color.value);
			cs6.setrgb(document.adminForm.conf_toolbar_color.value);
			cs7.setrgb(document.adminForm.conf_files_font_color.value);
		}

		//-->
		</script>
		<div>
		<form action="index.php" method="post" name="adminForm">
<table width="100%">
	<tr>
		<td width="220" valign="top">
		<div>
			<?php echo joomla_lms_adm_html::JLMS_menu();?>
		</div>
		</td>
		<td valign="top">
		<div class="width-100">
		<fieldset class="adminform">
		<?php if (!class_exists('JToolBarHelper')) { ?>
		<table class="adminheading">
		<tr>
		<?php } else { ?>
		<table >
		<tr>
		<?php } ?>
		<?php if (!class_exists('JToolBarHelper')) { ?>
			<th class="config"><?php echo _JOOMLMS_COMP_NAME;?>:
				<small>
					<?php echo _JLMS_CFG; ?>
				</small>
			</th>
		<?php } else { ?>
			<td width="100%" align="left">&nbsp;</td>
			<td nowrap="nowrap" valign="top">
				<table align="center">
				<?php
				joomla_lms_adm_html::writableCell_cfg( _JLMS_CFG_SCORMS_FOLDER, $row->scorm_folder, true );
				joomla_lms_adm_html::writableCell_cfg( _JLMS_CFG_BACKUPS_FOLDER, $row->jlms_backup_folder );
				joomla_lms_adm_html::writableCell_cfg( _JLMS_CFG_DOCS_FOLDER, $row->jlms_doc_folder );
				joomla_lms_adm_html::writableCell_cfg( _JLMS_CFG_CERTS_FOLDER, $row->jlms_crtf_folder );
				joomla_lms_adm_html::writableCell_cfg( _JLMS_CFG_TEMP_FOLDER, $row->temp_folder, true );
				?>
				</table>
			</td>
			<td align="left" nowrap="nowrap">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
		<?php } ?>
			<td nowrap="nowrap">
				<?php global $lms_version, $lms_version_build; $expired_str = isset($lists['expired_str']) ? $lists['expired_str'] : ''; $users_str = isset($lists['users_str']) ? $lists['users_str'] : ''; ?>
				<table width="250">
					<tr>
						<td width="40%" align="left" nowrap><?php echo _JLMS_CFG_INSTALLED_V; ?>:</td>
						<td align="left" nowrap> &nbsp;<b><?php echo $lms_version;?></b><?php echo $lms_version_build ? ('&nbsp;&nbsp;'.$lms_version_build) :'';?></td>
					</tr>
					<tr>
						<td align="left" nowrap><?php echo _JLMS_CFG_LATEST_V; ?>:</td>
						<td nowrap style="overflow:hidden" align="left"><div style="overflow:hidden"><?php echo jlms_update_checker();?></div></td>
					</tr>
					<tr>
						<td align="left" nowrap><?php echo _JLMS_CFG_LICENSE_EXPS_ON; ?>:</td>
						<td nowrap align="left">&nbsp;<?php echo $expired_str;?></td>
					</tr>
					<tr>
						<td align="left" nowrap><?php echo _JLMS_CFG_LICENSE_USERS; ?>:</td>
						<td nowrap align="left">&nbsp;<?php echo $users_str;?></td>
					</tr>
					<tr>
						<td align="left" nowrap><?php echo _JLMS_CFG_BRANDING_FREE; ?>:</td>
						<td nowrap align="left">&nbsp;<?php
						if (false) {
							echo '<span style = "font-weight:bold; color:red">No</span>';
						} else {
							echo $lists['branding_free_configured'];
						}
						?></td>
					</tr>
				</table>
			</td>
		</tr>
		</table>
		</fieldset></div>
		<table width="100%" border="0">
			<tr>
				<td valign="top" >
				<?php
				$tabs->startPane("configPane");
				/////////////////////////////////////////////////////////////
				//	Global settings
				$offw = (isset($lists['lms_isoffline_warning']) && $lists['lms_isoffline_warning']) ? true : false;
				$cbw = (isset($lists['lms_cb_warning']) && $lists['lms_cb_warning']) ? true : false;
				$tab_warning = ($offw || $cbw ) ? true : false;

				$cow = !joomla_lms_adm_html::jlms_admin_writable_cfg($row->jlms_crtf_folder );

				$tabs->startTab((($tab_warning?"<b><font color=\"red\">":'')._JLMS_CFG_GLOBAL.($tab_warning?"</font></b>":'')),"global-page");
				?>
				<table class="adminlist" >
					<tr><td colspan="2">					
					<div class="width-100">					
					<fieldset class="adminform"><legend><?php echo _JLMS_CFG_OFF_ON_LINE_MODE; ?></legend>					
						<table cellpadding="0" cellspacing="0" border="0">
							<tr><td width="200"><?php echo $offw?'<b><font color="red">':'';?><?php echo _JLMS_CFG_JLMS_IS_OFFLINE; ?><?php echo $offw?'</font></b>':'';?></td>
								<td>							
								<fieldset class="radio">	
								<?php echo $lists['lms_isonline']; ?>
								</fieldset>								
								</td>
							</tr>
							<tr><td><?php echo _JLMS_CFG_OFFLINE_MSG; ?>:</td>
								<td>
								<textarea class="text_area" cols="60" rows="4" style="width:300px; height:60px" name="offline_message"><?php echo $row->offline_message; ?></textarea><?php
									echo mosToolTip( _JLMS_CFG_LMS_OFFLINE_MSG );	?>
								</td>
							</tr>
						</table>						
					</fieldset>
					</div>					
					</td></tr>
					<tr><td colspan="2">
					<div class="width-100">
					<fieldset class="adminform"><legend><?php echo _JLMS_CFG_CERT_OPTIONS; ?></legend>
						<table cellpadding="0" cellspacing="0" border="0">
							<tr>
								<td width="200">Save certificates on server</td>
								<td>
								<fieldset class="radio">
								<?php echo $lists['save_certificates'];
								echo mosToolTip( _JLMS_CFG_COLLECT_PRINTED_CERTS );
								$scf = $row->save_certificates;
								if ($row->save_certificates === null) {
									$scf = 1;
								}
								?>
								</fieldset>
								</td>
							</tr>
							<?php
							/* estimate disk space */
							$size = 0;
							if ($row->jlms_crtf_folder && is_dir($row->jlms_crtf_folder) && is_readable($row->jlms_crtf_folder)) {
								$dirname = $row->jlms_crtf_folder;
								$dirname_stack[] = $dirname;
								$size = 0;

								do {
									$dirname = array_shift($dirname_stack);
									$handle = opendir($dirname);
									while (false !== ($file = readdir($handle))) {
										if ($file != '.' && $file != '..' && is_readable($dirname . DIRECTORY_SEPARATOR . $file)) {
											if (is_dir($dirname . DIRECTORY_SEPARATOR . $file)) {
												$dirname_stack[] = $dirname . DIRECTORY_SEPARATOR . $file;
											}
											$size += filesize($dirname . DIRECTORY_SEPARATOR . $file);
										}
									}
									closedir($handle);
								} while (count($dirname_stack) > 0);
							}
							if (false && $scf) { ?>
							<tr><td><?php echo _JLMS_CFG_PATH_TO_SAVE_CERTS; ?>:</td>
								<td>&nbsp;&nbsp;<?php echo (!$cow) ? ('<font color="green">'.$row->jlms_crtf_folder.'</font>') : '<b><font color="red">'._JLMS_CFG_PATH_IS_NOT_CFG.'</font></b>';?></td>
							</tr>
							<tr><td><?php echo _JLMS_CFG_DISK_SPACE_TAKEN; ?>:</td>
								<td>&nbsp;&nbsp;<?php if ($size) {printf('%.2f',round(($size/1048576),2));} else { echo '0.00'; }?>Mb</td>
							</tr>
							<?php } ?>
							<tr><td><?php echo _JLMS_CFG_PRINT_SERIAL_NUM; ?>:</td>
								<td>					
								<fieldset class="radio">			
								<?php echo $lists['crtf_show_sn'];
								echo mosToolTip( _JLMS_CFG_TIP_PRINT_SERIAL_NUM );
								?>						
								</fieldset>		
								</td>
							</tr>
							<tr><td><?php echo _JLMS_CFG_PR_BARCODE_ON_CERTS; ?>:</td>
								<td>
								<fieldset class="radio">
								<?php echo $lists['crtf_show_barcode'];
								echo mosToolTip( _JLMS_CFG_TIP_PR_BARCODE_ON_CERTS );
								?>
								</fieldset>
								</td>
							</tr>
							<tr><td><?php echo _JLMS_CFG_PR_WMARK_ON_DUPLS; ?>:</td>
								<td>
								<fieldset class="radio">
								<?php echo $lists['crtf_duplicate_wm'];
								echo mosToolTip( _JLMS_CFG_TIP_PR_WMARK_ON_DUPLS );
								?>
								</fieldset>
								</td>
							</tr>
						</table>
					</fieldset>
					</div>
					</td></tr>
					<tr><td colspan="2">
					<div class="width-100">
					<fieldset class="adminform"><legend><?php echo _JLMS_CFG_GLOBAL_PREFS; ?></legend>
						<table cellpadding="0" cellspacing="0" border="0">
						<tr><td width="200"><?php echo _JLMS_CFG_JLMS_COMP_HEADING; ?>:</td>
							<td>
							<input type="text" class="text_area" style="width:300px" name="jlms_heading" value="<?php echo str_replace('"', '&quot;', $row->jlms_heading);?>" />
							<?php echo mosToolTip( _JLMS_CFG_TIP_JLMS_COMP_HEADING ); ?>
							</td>
						</tr>
						<tr><td><?php echo _JLMS_CFG_JLMS_P_TITLE; ?>:</td>
							<td>
							<input type="text" class="text_area" style="width:300px" name="jlms_title" value="<?php echo str_replace('"', '&quot;', $row->jlms_title);?>" />
							</td>
						</tr>
						<tr><td><?php echo _JLMS_CFG_ENABLE_CHAT; ?>:</td>
							<td>
							<fieldset class="radio">
							<?php echo $lists['chat_enable'];
								echo mosToolTip( _JLMS_CFG_TIP_ENABLE_CHAT );	?>
							</fieldset>
							</td>
						</tr>
						<tr><td><?php echo _JLMS_CFG_ENABLE_TRACKING; ?>:</td>
							<td>
							<fieldset class="radio">
							<?php echo $lists['tracking_enable'];
							echo mosToolTip( _JLMS_CFG_TIP_ENABLE_TRACKING );
							?>
							</fieldset>
							</td>
						</tr>
						<tr><td><?php echo _JLMS_CFG_USER_GRS_MODE; ?>:</td>
							<td>
							<fieldset class="radio">
							<?php echo $lists['use_global_groups'];
							echo mosToolTip( _JLMS_CFG_TIP_USER_GRS_MODE );
							?>
							</fieldset>
							</td>
						</tr>
						<tr><td><?php echo _JLMS_CFG_DEF_LANG; ?>:</td>
							<td>
							<fieldset class="radio">
							<?php echo $lists['default_language'];
							echo mosToolTip( _JLMS_CFG_TIP_DEF_LANG );							
							?>
							</fieldset>
							</td>
						</tr>
						<tr><td><?php echo $cbw?'<b><font color="red">':'';?><?php echo _JLMS_CFG_CB_INSTALLED; ?>:<?php echo $cbw?'</font></b>':'';?></td>
							<td>
							<fieldset class="radio">
							<?php echo $lists['is_cb_installed'];
								echo mosToolTip( _JLMS_CFG_TIP_CB_INSTALLED );	?>
							</fieldset>
							</td>
						</tr>
						<tr><td><?php echo _JLMS_CFG_ALLOW_GST_ACCESS; ?>:</td>
							<td>
							<fieldset class="radio">
							<?php echo $lists['guest_access_subscriptions'];
								echo mosToolTip( _JLMS_CFG_TIP_ALLOW_GST_ACCESS );?>
							</fieldset>
							</td>
						</tr>
						<tr><td><?php echo _JLMS_CFG_DATE_FORMAT; ?>:</td>
							<td>
							<fieldset class="radio">
							<?php echo $lists['date_format'];
								echo mosToolTip( _JLMS_CFG_TIP_DATE_FORMAT );	?>
							</fieldset>
							</td>							
						</tr>
						<tr><td><?php echo _JLMS_CFG_FIRST_DAY_OF_W; ?>:</td>
							<td>
							<fieldset class="radio">
							<?php echo $lists['date_format_fdow'];
							echo mosToolTip( _JLMS_CFG_TIP_FIRST_DAY_OF_W );
							?>
							</fieldset>
							</td>
						</tr>
						<tr>
							<td>
							<?php echo _JLMS_CFG_VERSION_CHECKING; ?>:
							</td>
							<td>
							<?php echo $lists['lms_check_version'];
							echo '&nbsp;'.mosToolTip( _JLMS_CFG_TIP_VERSION_CHECKING )
							?>
							</td>
						</tr>
						</table>
					</fieldset>
					</div>
					</td></tr>
					<tr><td colspan="2">
					<div class="width-100">
					<fieldset class="adminform"><legend><?php echo _JLMS_CFG_CUST_JLMS_BACKEND_ACC; ?></legend>
						<table cellpadding="0" cellspacing="0" border="0">
						<tr><td width="200"><?php echo _JLMS_CFG_CUST_SELECT_JLMS_MNGS; ?>:</td>
							<td>
							<?php echo $lists['backend_access_gid'];?> 
							</td>
						</tr>
						</table>
					</fieldset>
					</div>
					</td></tr>
				</table>
				<?php
				$tabs->endTab();

				/////////////////////////////////////////////////////////////
				//	Conference settings
				$confw = (isset($lists['conference_warning']) && $lists['conference_warning']) ? true : false;
				$confpw = (isset($lists['confpath_warning']) && $lists['confpath_warning']) ? true : false;
				$confcw = (isset($lists['confclients_warning']) && $lists['confclients_warning']) ? true : false;

				$tab_warning = ($confw || $confpw || $confcw) ? true : false;

				$tabs->startTab((($tab_warning?"<b><font color=\"red\">":'')._JLMS_CFG_CONFERENCE.($tab_warning?"</font></b>":'')),"conf-page");?>
				<table class="adminlist">
					<tr>
						<td colspan="2">
						<div class="width-100">
							<fieldset class="adminform"><legend><?php echo _JLMS_CFG_CONF_SETTINGS; ?></legend>
							<table cellpadding="0" cellspacing="0">
								<tr>
									<td width="185px"><?php echo $confw?'<b><font color="red">':'';?><?php echo _JLMS_CFG_ENABLE_CONF; ?>:<?php echo $confw?'</font></b>':'';?></td>
									<td>
									<fieldset class="radio">
									<?php echo $lists['conference_enable'];
									echo mosToolTip( _JLMS_CFG_TIP_ENABLE_CONF );
									?>
									</fieldset>
									</td>
								</tr>
								<tr><td><?php echo $confpw?'<b><font color="red">':'';?><?php echo _JLMS_CFG_FLASH_SERVER_URL; ?>:<?php echo $confpw?'</font></b>':'';?></td>
									<td><input class="text_area" type="text" name="flascommRoot" size="50" value="<?php echo str_replace('"', '&quot;', $row->flascommRoot); ?>"/><?
									echo mosToolTip( _JLMS_CFG_TIP_FLASH_SERVER_URL );
									?></td>
								</tr>
								<tr><td><?php echo $confcw?'<b><font color="red">':'';?><?php echo _JLMS_CFG_NUM_OF_CONC_USRS; ?>:<?php echo $confcw?'</font></b>':'';?></td>
									<td><input class="text_area" type="text" name="maxConfClients" size="10" value="<?php echo intval($row->maxConfClients); ?>"/><?
									echo mosToolTip( _JLMS_CFG_TIP_NUM_OF_CONC_USRS );
									?></td>
								</tr>
							</table>
							</fieldset>
						</div>
						</td>
					</tr>
					<tr>
						<td colspan="2">
						<div class="width-100">
						<fieldset class="adminform"><legend><?php echo _JLMS_CFG_CONF_COLORS; ?></legend>
							<table cellpadding="0" cellspacing="0">
								<tr>
									<td align="right"><?php echo _JLMS_CFG_COLOR_SCHEMES; ?>:</td>
									<td colspan="2">
										<?php echo $lists['colors_scheme'];?>
									</td>
								</tr>
								<tr>
									<td colspan="2"></td>
									<td rowspan="8" align="right">
									<img src="../components/com_joomla_lms/lms_images/admin/conference_colors.jpg" width="400" height="288" alt="<?php echo _JLMS_CFG_CONF_COLORS; ?>" title="<?php echo _JLMS_CFG_CONF_COLORS; ?>" />
									</td>
								</tr>
								<tr>
									<td align="right" width="185px"><?php echo _JLMS_CFG_BACKGROUND; ?>:</td>
									<td width="220">
									<div style="float:left; height:18px">
									<input id="conf_background" name="conf_background" value="<?php echo $row->conf_background;?>" class="text_area" style="width:10ex;" onChange="cs1.setrgb(this.value);" onKeyPress="if (event.keyCode == 13){return false;}">
									<span id="color_select_icon1" class="color_select_icon" onclick="cs1.toggle_color_select();">&nbsp;&nbsp;&nbsp;&nbsp;</span>&nbsp;&nbsp;
									</div>
									<div id="color_display_icon1" style="float:left; width:40px; height:18px; background:<?php echo $row->conf_background;?>">
									</div>
									<div style="clear:both;"></div>
									</td>
									<td></td>
								</tr>
								<tr>
									<td align="right"><?php echo _JLMS_CFG_MAIN_COLOR; ?>:</td>
									<td>
									<div style="float:left; height:18px">
									<input id="conf_main_color" name="conf_main_color" value = "<?php echo $row->conf_main_color;?>" class="text_area" style="width:10ex;" onChange="cs2.setrgb(this.value);" onKeyPress="if (event.keyCode == 13){return false;}">
									<span id="color_select_icon2" class="color_select_icon" onclick="cs2.toggle_color_select();">&nbsp;&nbsp;&nbsp;&nbsp;</span>&nbsp;&nbsp;
									</div>
									<div id="color_display_icon2" style="float:left; width:40px; height:18px; background:<?php echo $row->conf_main_color;?>">
									</div>
									<div style="clear:both;"></div>
									</td>
									<td></td>
								</tr>
								<tr>
									<td align="right"><?php echo _JLMS_CFG_TITLE_COLOR; ?>:</td>
									<td>
									<div style="float:left; height:18px">
									<input id="conf_title_color" name="conf_title_color" value = "<?php echo $row->conf_title_color;?>" class="text_area" style="width:10ex;" onChange="cs3.setrgb(this.value);" onKeyPress="if (event.keyCode == 13){return false;}">
									<span id="color_select_icon3" class="color_select_icon" onclick="cs3.toggle_color_select();">&nbsp;&nbsp;&nbsp;&nbsp;</span>&nbsp;&nbsp;
									</div>
									<div id="color_display_icon3" style="float:left; width:40px; height:18px; background:<?php echo $row->conf_title_color;?>">
									</div>
									<div style="clear:both;"></div>
									</td>
									<td></td>
								</tr>
								<tr>
									<td align="right"><?php echo _JLMS_CFG_BORDER_COLOR; ?>:</td>
									<td>
									<div style="float:left; height:18px">
									<input id="conf_border_color" name="conf_border_color" value = "<?php echo $row->conf_border_color;?>" class="text_area" style="width:10ex;" onChange="cs4.setrgb(this.value);" onKeyPress="if (event.keyCode == 13){return false;}">
									<span id="color_select_icon4" class="color_select_icon" onclick="cs4.toggle_color_select();">&nbsp;&nbsp;&nbsp;&nbsp;</span>&nbsp;&nbsp;
									</div>
									<div id="color_display_icon4" style="float:left; width:40px; height:18px; background:<?php echo $row->conf_border_color;?>">
									</div>
									<div style="clear:both;"></div>
									</td>
									<td></td>
								</tr>
								<tr>
									<td align="right"><?php echo _JLMS_CFG_TITLE_FONT_COLOR; ?>:</td>
									<td>
									<div style="float:left; height:18px">
									<input id="conf_title_font_color" name="conf_title_font_color" value="<?php echo $row->conf_title_font_color;?>" class="text_area" style="width:10ex;" onChange="cs5.setrgb(this.value);" onKeyPress="if (event.keyCode == 13){return false;}">
									<span id="color_select_icon5" class="color_select_icon" onclick="cs5.toggle_color_select();">&nbsp;&nbsp;&nbsp;&nbsp;</span>&nbsp;&nbsp;
									</div>
									<div id="color_display_icon5" style="float:left; width:40px; height:18px; background:<?php echo $row->conf_title_font_color;?>">
									</div>
									<div style="clear:both;"></div>

									</td>
									<td></td>
								</tr>
								<tr>
									<td align="right"><?php echo _JLMS_CFG_FILTERS_USRS_F_CLR; ?>:</td>
									<td>
									<div style="float:left; height:18px">
									<input id="conf_files_font_color" name="conf_files_font_color" value="<?php echo $row->conf_files_font_color;?>" class="text_area" style="width:10ex;" onChange="cs7.setrgb(this.value);" onKeyPress="if (event.keyCode == 13){return false;}">
									<span id="color_select_icon7" class="color_select_icon" onclick="cs7.toggle_color_select();">&nbsp;&nbsp;&nbsp;&nbsp;</span>&nbsp;&nbsp;
									</div>
									<div id="color_display_icon7" style="float:left; width:40px; height:18px; background:<?php echo $row->conf_files_font_color;?>">
									</div>
									<div style="clear:both;"></div>
									</td>
									<td></td>
								</tr>
								<tr>
									<td align="right" ><?php echo _JLMS_CFG_TOOLBAR_CLR; ?>:</td>
									<td>
									<div style="float:left; height:18px">
									<input id="conf_toolbar_color" name="conf_toolbar_color" value="<?php echo $row->conf_toolbar_color;?>" class="text_area" style="width:10ex;" onChange="cs6.setrgb(this.value);" onKeyPress="if (event.keyCode == 13){return false;}">
									<span id="color_select_icon6" class="color_select_icon" onclick="cs6.toggle_color_select();">&nbsp;&nbsp;&nbsp;&nbsp;</span>&nbsp;&nbsp;
									</div>
									<div id="color_display_icon6" style="float:left; width:40px; height:18px; background:<?php echo $row->conf_toolbar_color;?>">
									</div>
									<div style="clear:both;"></div>
									</td>
									<td></td>
								</tr>
							</table>
							</fieldset>
						</div>
						</td>
					</tr>
					<tr>
						<td colspan="2">
						<div class="width-100">
						<fieldset class="adminform"><legend><?php echo _JLMS_CFG_CONF_DESC; ?></legend>
						<table cellpadding="0" cellspacing="0" width="100%">
							<tr>
								<td>
								<?php #editorArea( 'editor2', $row->conf_description, 'conf_description', '100%;', '250', '60', '20' ) ; ?>
								<textarea name="conf_description" cols="60" rows="20" style="width: 100%;" ><?php echo $row->conf_description;?></textarea>
								</td>
							</tr>
						</table>
						</fieldset>
						</div>
						</td>
					</tr>
				</table>

				<?php
				$tabs->endTab();


				/////////////////////////////////////////////////////////////
				//	Courses settings
				$tabs->startTab( _JLMS_COURSES,"courses-page");?>

				<script language="javascript" type="text/javascript">

				//var tab1 = getObj('grade-page');
				//if (tab1){
				//if (tab1.style.display == 'none'){
				document.body.onkeypress = courses_cat_change;
				//}
				//}
				function courses_cat_change(ev) {
					ev || (ev = window.event);
					if (ev.keyCode == 13) {
						var tab1 = getObj('grade-page');
						var tab2 = getObj('courses-page');
						if ((tab1.style.display == 'none') && (tab2.style.display == 'block')) {
							analyze_edit_cat();
						} else if ((tab1.style.display == 'block') && (tab2.style.display == 'none')) {
							analyze_edit_grade_cat();
						}
						return false;
					}
					return true;
				}

				function getObj(el_id)	{
					if (document.getElementById)	{
						return document.getElementById(el_id);	}
						else if (document.all)	{
							return document.all[el_id];	}
							else if (document.layers)	{
								return document.layers[el_id];	}
				}

				function analyze_edit_cat(){
					var element = getObj('inp_tmp');
					if (element){
						var parent = element.parentNode;

						var inpu_value = element.value;
						parent.removeChild(element);
						var  cat_id_sss = '0';
						if (parent.hasChildNodes()) {
							var children = parent.childNodes;
							for (var i = 0; i < children.length; i++) {
								if (children[i].nodeName.toLowerCase() == 'input') {
									if (children[i].name == 'jlms_cat_name[]') {
										cat_id_sss = children[i].value;// = inp1_value;
									}
								}
							}
						}
						var input_cat2 = document.createElement("input");
						input_cat2.type = "hidden";
						input_cat2.name = 'jlms_cat_name[]';
						input_cat2.value = inpu_value;
						var input_id2 = document.createElement("input");
						input_id2.type = "hidden";
						input_id2.name = 'jlms_cat_id[]';
						input_id2.value = cat_id_sss;
						parent.innerHTML = inpu_value;
						parent.appendChild(input_cat2);
						var ttt = document.createElement("br");
						parent.appendChild(ttt);
						parent.appendChild(input_id2);
					}
				}
				function edit_cat_name(e){
					analyze_edit_cat();

					if (!e) { e = window.event;}
					var cat2=e.target?e.target:e.srcElement;

					Redeclare_element_inputs2(cat2);
					var cat_name_value = '';
					if (cat2.hasChildNodes()) {
						var children = cat2.childNodes;
						for (var i = 0; i < children.length; i++) {
							if (children[i].nodeName.toLowerCase() == 'input') {
								if (children[i].name == 'jlms_cat_name[]') {
									cat_name_value = children[i].value;// = inp1_value;
								}
							} else {
								cat2.removeChild(cat2.childNodes[i]);
							}
						}
					}
					var input_cat3 = document.createElement("input");
					input_cat3.type = "text";
					input_cat3.id = "inp_tmp";
					input_cat3.name = "inp_tmp";//cat_name;

					input_cat3.value = cat_name_value;
					input_cat3.setAttribute("style","z-index:5000");
					if (window.addEventListener) { input_cat3.addEventListener('dblclick', analyze_edit_cat, false);}else { input_cat3.attachEvent('ondblclick', analyze_edit_cat );}
					cat2.appendChild(input_cat3);
					cat2.appendChild(document.createElement("br"));
					cat2.appendChild(document.createElement("br"));
				}
				function ReAnalize_cat_Rows( start_index, tbl_id ) {//ignore start_index! vsegda ==1;
					start_index = 1;
					var tbl_elem = getObj(tbl_id);
					//nugno perebirat' vse TR. potomu kak dobavlena funkciya Redeclare_element_inputs();
					//kotoraya udalyaet a zatem sozdaet zanavo <input>.
					//t.k. v MOZILLA 1.6 (and old versions) poryadok inputov na forme ne menyaetsa esli ix ne udalyat'.
					//if (!start_index) { start_index = 1; }
					//if (start_index < 0) { start_index = 1; }
					if (tbl_elem.rows[start_index]) {
						var count = start_index; var row_k = 1 - start_index%2;//0;
						for (var i=start_index; i<tbl_elem.rows.length; i++) {
							tbl_elem.rows[i].cells[0].innerHTML = count;
							Redeclare_element_inputs2(tbl_elem.rows[i].cells[1]);

							tbl_elem.rows[i].className = 'row'+row_k;
							count++;
							row_k = 1 - row_k;
						}
					}
				}
				//function js_in_array(n, ha){for(h in ha){if(ha[h]==n){return true;}}return false;}
				function Redeclare_element_inputs2(object) {
					//var z = Array(); k = 0;
					if (object.hasChildNodes()) {
						var children = object.childNodes;
						for (var i = 0; i < children.length; i++) {
							if (children[i].nodeName.toLowerCase() == 'input') {
								//if (!js_in_array(children[i].name,z)) {
								//z[k] = children[i].name; k++;
								var inp_name = children[i].name;

								var inp_value = children[i].value;
								object.removeChild(object.childNodes[i]);
								//i --;
								var input_hidden = document.createElement("input");
								input_hidden.type = "hidden";
								input_hidden.name = inp_name;
								input_hidden.value = inp_value;
								object.appendChild(input_hidden);
								//}
							}
						}
					}
				}
				function Delete_cat_row(element) {
					var del_index = element.parentNode.parentNode.sectionRowIndex;
					var tbl_id = element.parentNode.parentNode.parentNode.parentNode.id;
					element.parentNode.parentNode.parentNode.deleteRow(del_index);
					ReAnalize_cat_Rows('7',tbl_id);
				}

				function Add_new_cat(elem_field, tbl_id,field_name, field_name2) {
					//var new_cat_txt = getObj(elem_field).options[getObj(elem_field).selectedIndex].text;
					var new_cat_txt2 = getObj(elem_field).value;
					if (new_cat_txt2) {
						var tbl_elem = getObj(tbl_id);
						var row = tbl_elem.insertRow(tbl_elem.rows.length);
						if (getObj('grade-page').style.display == 'none'){
							if (window.addEventListener) { row.addEventListener('dblclick', edit_cat_name, false);}else { row.attachEvent('ondblclick', edit_cat_name);}
						}else{
							if (window.addEventListener) { row.addEventListener('dblclick', edit_cat_grade_name, false);}else { row.attachEvent('ondblclick', edit_cat_grade_name);}
						}
						var cell1 = document.createElement("td");
						var cell2 = document.createElement("td");
						var cell3 = document.createElement("td");
						var cell4 = document.createElement("td");
	
						var input_hidden 	= document.createElement("input");
						input_hidden.type 	= "hidden";
						input_hidden.name 	= field_name2;
						input_hidden.value 	= new_cat_txt2;
						var input_hidden2 	= document.createElement("input");
						input_hidden2.type 	= "hidden";
						input_hidden2.name 	= field_name;
						input_hidden2.value = '0';
						cell1.align = 'center';
						cell1.innerHTML = 0;
						cell2.innerHTML = new_cat_txt2;
						cell2.appendChild(input_hidden);
						cell2.appendChild(document.createElement("br"));
						cell2.appendChild(input_hidden2);
						cell3.innerHTML = '<a href="javascript: void(0);" onclick="javascript:Delete_cat_row(this); return false;" title="Delete"><img src="<?php echo ADMIN_IMAGES_PATH;?>publish_x.png" border="0" alt="Delete"></a>';
						cell4.innerHTML = '';
						row.appendChild(cell1);
						row.appendChild(cell2);
						row.appendChild(cell3);
						row.appendChild(cell4);
						ReAnalize_cat_Rows('7',tbl_id);
					} else {
						alert('<?php echo _JLMS_CFG_MSG_ENTER_CAT_NAME; ?>');
					}
				}

				</script>
				<div class="width-100">
				<table class="adminlist">
				<tr><td>
				<fieldset class="adminform"><legend><?php echo _JLMS_CFG_COURS_L_DETS; ?></legend>
							<table cellpadding="0" cellspacing="0">
								<tr><td><?php echo _JLMS_CFG_SHOW_PAID_COURSES; ?>:</td>
									<td>
									<fieldset class="radio">
									<?php echo $lists['show_paid_courses'];?>
									</fieldset>
									</td>
								</tr>
								<tr><td><?php echo _JLMS_CFG_SHOW_FUTURE_CRSES ?>:</td>
									<td>
									<fieldset class="radio">
									<?php echo $lists['show_future_courses'];
									echo mosToolTip( _JLMS_CFG_SHOW_TIP_FUTURE_CRSES );
									?>
									</fieldset>
									</td>
								</tr>
								<tr>
									<td width="250"><?php echo _JLMS_CFG_SHOW_SHORT_DESC; ?>:</td>
									<td>
									<fieldset class="radio">
										<?php echo $lists['show_short_description'];?>
									</fieldset>
									</td>
								</tr>
								<tr>
									<td width="250"><?php echo _JLMS_CFG_SHOW_ST_EN_DATES; ?>:</td>
									<td>
									<fieldset class="radio">
									<?php echo $lists['show_course_publish_dates'];?>
									</fieldset>
									</td>
								</tr>
								<tr>
									<td width="250"><?php echo _JLMS_CFG_SHOW_PR_FEE_COL; ?>:</td>
									<td><?php echo $lists['price_fee_type'];?></td>
								</tr>
								<tr><td><?php echo _JLMS_CFG_SHOW_CRS_AUTHS; ?>:</td>
									<td>
									<fieldset class="radio">
									<?php echo $lists['show_course_authors'];?>
									</fieldset>
									</td>
								</tr>
								<tr><td><?php echo _JLMS_CFG_SORT_CRS_BY; ?>:</td>
									<td>
									<?php echo $lists['lms_courses_sortby'];?></td>
								</tr>
							</table>
				</fieldset>
				</td></tr>	
				<tr><td>			
				<fieldset class="adminform"><legend><?php echo _JLMS_CFG_SHOW_HIDE_CRS_PROPS; ?></legend>
							<table cellpadding="0" cellspacing="0">
								<tr>
									<td width="250"><?php echo _JLMS_CFG_SHOW_CR_META_PROPS; ?>:</td>
									<td>
									<fieldset class="radio">
									<?php echo $lists['show_course_meta_property'];
									echo mosToolTip( _JLMS_CFG_TIP_SHOW_CR_META_PROPS );
									?>
									</fieldset>
									</td>
								</tr>
								<tr>
									<td><?php echo _JLMS_CFG_SHOW_ACCESS_LVL_PROP; ?>:</td>
									<td>
									<fieldset class="radio">
									<?php echo $lists['show_course_access_property'];
									echo mosToolTip( _JLMS_CFG_TIP_SH_ACCESS_LVL_PROP );
									?>
									</fieldset>
									</td>
								</tr>
								<tr>
									<td><?php echo _JLMS_CFG_SH_CR_FEE_PROP; ?>:</td>
									<td>
									<fieldset class="radio">
									<?php echo $lists['show_course_fee_property'];
									echo mosToolTip( _JLMS_CFG_TIP_SH_CR_FEE_PROP );
									?>
									</fieldset>
									</td>
								</tr>
								<tr><td><?php echo _JLMS_CFG_SH_ADD_REG_PROP; ?>:</td>
									<td>
									<fieldset class="radio">
									<?php echo $lists['show_course_spec_property'];
									echo mosToolTip( _JLMS_CFG_TIP_SH_ADD_REG_PROP );
									?>
									</fieldset>
									</td>
								</tr>
								<tr><td><?php echo _JLMS_CFG_SH_MAX_ATT_PROP; ?>:</td>
									<td>
									<fieldset class="radio">
									<?php echo $lists['max_attendees_change'];
									echo mosToolTip( _JLMS_CFG_TIP_SH_MAX_ATT_PROP );
									?>
									</fieldset>
									</td>
								</tr>
							</table>
				</fieldset>
				</td></tr>
				<tr><td>
				<fieldset class="adminform"><legend><?php echo _JLMS_CFG_SECN_CATS_MECH; ?></legend>
							<table cellpadding="0" cellspacing="0">
								<tr>
									<td width="250"><?php echo _JLMS_CFG_EN_SECN_CATS_MECH; ?>:</td>
									<td>
									<fieldset class="radio">
									<?php echo $lists['sec_cat_use'];
									echo mosToolTip( _JLMS_CFG_TIP_EN_SECN_CATS_MECH );
									?>
									</fieldset>
									</td>
								</tr>
								<tr>
									<td><?php echo _JLMS_CFG_A_TEACH_SPEC_SECN_CATS; ?>:</td>
									<td>
									<fieldset class="radio">
									<?php echo $lists['sec_cat_show'];
									echo mosToolTip( _JLMS_CFG_TIP_A_TEACH_SPEC_SECN_CATS );
									?>
									</fieldset>
									</td>
								</tr>
							</table>
				</fieldset>
				</td></tr>
				</table>				
				</div>
<?php /*
				<fieldset class="adminform"><legend>Configure your course categories</legend>
				<table class="adminlist">
					<tr>
						<td><br />
						<input class="text_area" type="text" name="category_course" id="category_course" size="50" />
						<input class="text_area" type="button" name="add_new_scale" style="width:70px" value="Add" onclick="javascript:Add_new_cat('category_course', 'course_cat','jlms_cat_id[]', 'jlms_cat_name[]');">
						</td>
					</tr>
				</table>
				<table height="200px" class="adminlist">
					<tr>
						<td valign="top">
							<table class="adminlist" id="course_cat">
								<thead>
								<tr>
									<th width="20px" align="center">#</th>
									<th width="300px" align="left">Category name</th>
									<th width="20px">Delete</th>
									<th width="auto"></th>
								</tr>
								</thead>
							<?php
							$k = 0; $ii = 1; $ind_last = count($course_cats);
							if ($course_cats){
								foreach ($course_cats as $frow) { ?>
									<tr class="<?php echo "row$k"; ?>">
										<td align="center"><?php echo $ii;?></td>
										<td align="left" onDblClick="edit_cat_name(event);" >
											<?php echo $frow->c_category;?>
											<input type="hidden" name="jlms_cat_name[]" value="<?php echo $frow->c_category;?>" /><br />
											<input type="hidden" name="jlms_cat_id[]" value="<?php echo $frow->id;?>" />
										</td>
										<td><a href="javascript: void(0);" onclick="javascript:Delete_cat_row(this); return false;" title="Delete"><img src="images/publish_x.png" border="0" alt="Delete"></a></td>
										<td></td>
									</tr>
								<?php
								$k = 1 - $k; $ii ++;
								}
							}
							?>
							</table>
						</td>
					</tr>
					<tr>
						<td>
						<b>* - You can edit category by double clicking it's title. You can save changes by press Enter, double clicking it's title or title of any  other category.</b><br /><br />
						<b>* - Categories will be saved after "Save" or "Apply" only.</b>
						</td>
					</tr>
				</table>
				</fieldset>
*/ ?>
				<?php
				$tabs->endTab();
				/////////////////////////////////////////////////////////////
				//	Gradebook settings
				$tabs->startTab( _JLMS_GRADEBOOK ,"grade-page");?>
				<script language="javascript" type="text/javascript">
				//var tab = getObj('courses-page');
				//if (tab){
				//if (tab.style.display == 'none'){
				//document.body.onkeypress = gradebook_cat_change;
				//}
				//}
				/*function gradebook_cat_change(ev) {
				ev || (ev = window.event);
				if (ev.keyCode == 13) {
				analyze_edit_grade_cat();
				return false;
				}
				return true;
				}*/

				function analyze_edit_grade_cat(){
					var element = getObj('inp_tmp');
					if (element){
						var parent = element.parentNode;

						var inpu_value = element.value;
						parent.removeChild(element);
						var  cat_id_sss = '0';
						if (parent.hasChildNodes()) {
							var children = parent.childNodes;
							for (var i = 0; i < children.length; i++) {
								if (children[i].nodeName.toLowerCase() == 'input') {
									if (children[i].name == 'jlms_grade_cat_name[]') {
										cat_id_sss = children[i].value;// = inp1_value;
									}
								}
							}
						}
						var input_cat2 = document.createElement("input");
						input_cat2.type = "hidden";
						input_cat2.name = 'jlms_grade_cat_name[]';
						input_cat2.value = inpu_value;
						var input_id2 = document.createElement("input");
						input_id2.type = "hidden";
						input_id2.name = 'jlms_grade_cat_id[]';
						input_id2.value = cat_id_sss;
						parent.innerHTML = inpu_value;
						parent.appendChild(input_cat2);
						var ttt = document.createElement("br");
						parent.appendChild(ttt);
						parent.appendChild(input_id2);
					}
				}
				function edit_cat_grade_name(e){
					analyze_edit_grade_cat();

					if (!e) { e = window.event;}
					var cat2=e.target?e.target:e.srcElement;

					Redeclare_element_inputs2(cat2);
					var cat_name_value = '';
					if (cat2.hasChildNodes()) {
						var children = cat2.childNodes;
						for (var i = 0; i < children.length; i++) {
							if (children[i].nodeName.toLowerCase() == 'input') {
								if (children[i].name == 'jlms_grade_cat_name[]') {
									cat_name_value = children[i].value;// = inp1_value;
								}
							} else {
								cat2.removeChild(cat2.childNodes[i]);
							}
						}
					}
					var input_cat3 = document.createElement("input");
					input_cat3.type = "text";
					input_cat3.id = "inp_tmp";
					input_cat3.name = "inp_tmp";//cat_name;
					input_cat3.value = cat_name_value;
					input_cat3.setAttribute("style","z-index:5000");

					if (window.addEventListener) { input_cat3.addEventListener('dblclick', analyze_edit_grade_cat, false);}else { input_cat3.attachEvent('ondblclick', analyze_edit_grade_cat );}
					cat2.appendChild(input_cat3);
					cat2.appendChild(document.createElement("br"));
					cat2.appendChild(document.createElement("br"));
				}
				</script>
				<table class="adminlist">
					<tr>
						<th valign="top" class="title"><?php echo _JLMS_CFG_GRDBOOK_CATS; ?></th>
					</tr>
					<tr>
						<td align="left">
						<input class="text_area" type="text" name="category_gradebook" id="category_gradebook" size="50" />
						<input class="text_area" type="button" name="add_new_g_cat" style="width:70px" value="<?php echo _JLMS_ADD; ?>" onclick="javascript:Add_new_cat('category_gradebook', 'gradebook_cat','jlms_grade_cat_id[]', 'jlms_grade_cat_name[]');">
						</td>
					</tr>
				</table>
				<table height="200px" class="adminlist">
					<tr>
						<td valign="top">
							<table class="adminlist" id="gradebook_cat">
								<thead>
									<tr>
										<th width="20px" align="center">#</th>
										<th width="300px" align="left"><?php echo _JLMS_CFG_CAT_NAME; ?></th>
										<th width="20px"><?php echo _JLMS_DELETE; ?></th>
										<th width="auto"></th>
									</tr>
								</thead>
							<?php
							$k = 0; $ii = 1; $ind_last = count($gradebook_cats);
							if ($gradebook_cats){
								foreach ($gradebook_cats as $frow) { ?>
									<tr class="<?php echo "row$k"; ?>">
										<td align="center"><?php echo $ii;?></td>
										<td align="left" onDblClick="edit_cat_grade_name(event);" >
											<?php echo $frow->gb_category;?>
											<input type="hidden" name="jlms_grade_cat_name[]" value="<?php echo $frow->gb_category;?>" /><br />
											<input type="hidden" name="jlms_grade_cat_id[]" value="<?php echo $frow->id;?>" />
										</td>
										<td><a href="javascript: void(0);" onclick="javascript:Delete_cat_row(this); return false;" title="Delete"><img src="<?php echo ADMIN_IMAGES_PATH; ?>publish_x.png" border="0" alt="<?php echo _JLMS_DELETE; ?>"></a></td>
										<td></td>
									</tr>
								<?php
								$k = 1 - $k; $ii ++;
								}
							}
							?>
							</table>
						</td>
					</tr>
					<tr>
						<td>
						<b>* - <?php echo _JLMS_CFG_GRDBOOK_MSG1; ?></b><br /><br />
						<b>* - <?php echo _JLMS_CFG_GRDBOOK_MSG2; ?></b>
						</td>
					</tr>
				</table>
				<?php
				$tabs->endTab();

				/////////////////////////////////////////////////////////////
				//	Files settings

				$scw = !joomla_lms_adm_html::jlms_admin_writable_cfg($row->scorm_folder, true );
				$baw = !joomla_lms_adm_html::jlms_admin_writable_cfg($row->jlms_backup_folder );
				$dow = !joomla_lms_adm_html::jlms_admin_writable_cfg($row->jlms_doc_folder );
				$cow = !joomla_lms_adm_html::jlms_admin_writable_cfg($row->jlms_crtf_folder );
				$pow = !joomla_lms_adm_html::jlms_admin_writable_cfg($row->jlms_subscr_invoice_path );
				$tew = !joomla_lms_adm_html::jlms_admin_writable_cfg($row->temp_folder, true );
				$ftw = $file_types ? false : true;
				$tab_warning = ($scw || $baw || $dow || $tew || $ftw || $cow || $pow ) ? true : false;

				$site_abs_path = str_replace('\\\\', '\\', JPATH_SITE);
				$site_abs_path = str_replace('\\', '/', $site_abs_path);

				$tabs->startTab((($tab_warning?"<b><font color=\"red\">":'').'Files'.($tab_warning?"</font></b>":'')),"files-page");?>
				<div class="width-100">
				<fieldset class="adminform">
				<table >
					<tr><td width="185px"><?php echo _JLMS_CFG_SITE_ABS_PATH; ?>:</td>
						<td><b><font color="green"><?php echo $site_abs_path;?></font></b></td>
					</tr>
					<tr><td width="185px"><?php echo $scw?'<b><font color="red">':'';?><?php echo _JLMS_CFG_SCORMS_FOLDER_RP; ?>:<?php echo $scw?'</font></b>':'';?></td>
						<td><input class="text_area" type="text" name="scorm_folder" style="width:366px;" value="<?php echo $row->scorm_folder; ?>"/><?php
							echo mosToolTip( _JLMS_CFG_TIP_SCORMS_FOLDER_RP );?></td>
					</tr>
					<tr><td width="185px"><?php echo $baw?'<b><font color="red">':'';?><?php echo _JLMS_CFG_BCKS_FOLDER_AP; ?>:<?php echo $baw?'</font></b>':'';?></td>
						<td><input class="text_area" type="text" name="jlms_backup_folder" style="width:366px;" value="<?php echo $row->jlms_backup_folder; ?>"/><?php
							echo mosToolTip( _JLMS_CFG_TIP_BCKS_FOLDER_AP );?>
						</td>
					</tr>
					<tr>
						<td width="185px"><?php echo $dow?'<b><font color="red">':'';?><?php echo _JLMS_CFG_DOCS_FOLDER_AP; ?>:<?php echo $dow?'</font></b>':'';?></td>
						<td><input class="text_area" type="text" name="jlms_doc_folder" style="width:366px;" value="<?php echo $row->jlms_doc_folder; ?>"/><?php
						echo mosToolTip( _JLMS_CFG_TIP_DOCS_FOLDER_AP );
						?></td>
					</tr>
					<tr>
						<td width="185px"><?php echo $dow?'<b><font color="red">':'';?><?php echo _JLMS_CFG_CERTS_FOLDER_AP; ?>:<?php echo $cow?'</font></b>':'';?></td>
						<td><input class="text_area" type="text" name="jlms_crtf_folder" style="width:366px;" value="<?php echo $row->jlms_crtf_folder; ?>"/><?php
						echo mosToolTip( _JLMS_CFG_TIP_CERTS_FOLDER_AP );
						?></td>
					</tr>
					<tr><td width="185px"><?php echo $tew?'<b><font color="red">':'';?><?php echo _JLMS_CFG_TEMP_FOLDER_RP;?>:<?php echo $tew?'</font></b>':'';?></td>
						<td><input class="text_area" type="text" name="temp_folder" style="width:366px;" value="<?php echo $row->temp_folder; ?>"/><?php
							echo mosToolTip( _JLMS_CFG_TIP_TEMP_FOLDER_RP );?></td>
					</tr>
					<tr><td width="185px"><?php echo $pow?'<b><font color="red">':'';?><?php echo _JLMS_CFG_PDF_INV_FOLDER_AP; ?>:<?php echo $pow?'</font></b>':'';?></td>
						<td><input class="text_area" type="text" name="jlms_subscr_invoice_path" style="width:366px;" value="<?php echo $row->jlms_subscr_invoice_path; ?>"/><?php
							echo mosToolTip( _JLMS_CFG_TIP_PDF_INV_FOLDER_AP );?></td>
					</tr>
					<tr><td width="185px"><?php echo $ftw?'<b><font color="red">':'';?><?php echo _JLMS_CFG_SUPP_FI_TYPES_F_UPLOAD; ?>:<?php echo $ftw?'</font></b>':'';?></td>
						<td><textarea class="text_area" name="jlms_file_types" wrap="hard" style="white-space:normal " cols="50" rows="4" /><?php echo $file_types; ?></textarea><?php
							echo mosToolTip( _JLMS_CFG_TIP_SUPP_FI_TYPES_F_UPLOAD );?>
						</td>
					</tr>
				</table>
				</fieldset>
				</div>
				<?php
				$tabs->endTab();

				/////////////////////////////////////////////////////////////
				//	Attendance settings
				$tabs->startTab( _JLMS_CFG_ATTENDANCE ,"attend-page");?>
				<div class="width-100">
				<fieldset class="adminform">
				<table >
					<tr>
						<td width="185px" valign="top"><?php echo _JLMS_CFG_ATTENDANCE_DAYS; ?>:<?php echo "&nbsp;&nbsp;".mosToolTip( _JLMS_CFG_TIP_ATTENDANCE_DAYS );?></td>
						<td>
							<table>
						<?php
						$attendance = unserialize($row->attendance_days);
						if (!is_array($attendance)){$attendance = array();}
						for($i = 0; $i<7; $i++){
							echo "<tr><td><input type='checkbox' name='attendance_days[]' value='".($i+1)."'".((in_array($i+1,$attendance))? " checked": "" )." />";
							if ($i == 0){ echo _JLMS_WD_MONDAY;}elseif ($i == 1) {echo _JLMS_WD_TUESDAY;}elseif ($i == 2) {echo _JLMS_WD_WEDNESDAY;}	elseif ($i == 3) {echo _JLMS_WD_THURSDAY;}	elseif ($i == 4) {echo _JLMS_WD_FRIDAY;}elseif ($i == 5) {echo _JLMS_WD_SATURDAY;}elseif ($i == 6) {echo _JLMS_WD_SUNDAY;}
							echo "</td></tr>";
						}
							?></table>
						</td>
					</tr>
				</table>				
				<script language="javascript" type="text/javascript">

				function ReAnalize_tbl_Rows( start_index, tbl_id ) {//ignore start_index! vsegda ==1;
					start_index = 1;
					var tbl_elem = getObj(tbl_id);
					//nugno perebirat' vse TR. potomu kak dobavlena funkciya Redeclare_element_inputs();
					//kotoraya udalyaet a zatem sozdaet zanavo <input>.
					//t.k. v MOZILLA 1.6 (and old versions) poryadok inputov na forme ne menyaetsa esli ix ne udalyat'.
					//if (!start_index) { start_index = 1; }
					//if (start_index < 0) { start_index = 1; }
					if (tbl_elem.rows[start_index]) {
						var count = start_index; var row_k = 1 - start_index%2;//0;
						for (var i=start_index; i<tbl_elem.rows.length; i++) {
							tbl_elem.rows[i].cells[0].innerHTML = count;
							Redeclare_element_inputs(tbl_elem.rows[i].cells[1]);
							Redeclare_element_inputs(tbl_elem.rows[i].cells[2]);
							if (i > 1) {
								tbl_elem.rows[i].cells[4].innerHTML = '<a href="javascript: void(0);" onclick="javascript:Up_tbl_row(this); return false;" title="<?php echo _JLMS_MOVE_UP; ?>"><img src="<?php echo ADMIN_IMAGES_PATH; ?>uparrow.png" border="0" alt="<?php echo _JLMS_MOVE_UP; ?>"></a>';
							} else { tbl_elem.rows[i].cells[4].innerHTML = ''; }
							if (i < (tbl_elem.rows.length - 1)) {
								tbl_elem.rows[i].cells[5].innerHTML = '<a href="javascript: void(0);" onclick="javascript:Down_tbl_row(this); return false;" title="<?php echo _JLMS_MOVE_DOWN; ?>"><img src="<?php echo ADMIN_IMAGES_PATH; ?>downarrow.png" border="0" alt="<?php echo _JLMS_MOVE_DOWN; ?>"></a>';;
							} else { tbl_elem.rows[i].cells[5].innerHTML = ''; }
							tbl_elem.rows[i].className = 'row'+row_k;
							count++;
							row_k = 1 - row_k;
						}
					}
				}
				function Redeclare_element_inputs(object) {
					if (object.hasChildNodes()) {
						var children = object.childNodes;
						for (var i = 0; i < children.length; i++) {
							if (children[i].nodeName.toLowerCase() == 'input') {
								var inp_name = children[i].name;
								var inp_value = children[i].value;
								object.removeChild(object.childNodes[i]);
								var input_hidden = document.createElement("input");
								input_hidden.type = "hidden";
								input_hidden.name = inp_name;
								input_hidden.value = inp_value;
								object.appendChild(input_hidden);
							}
						}
					}
				}
				function Delete_tbl_row(element) {
					var del_index = element.parentNode.parentNode.sectionRowIndex;
					var tbl_id = element.parentNode.parentNode.parentNode.parentNode.id;
					element.parentNode.parentNode.parentNode.deleteRow(del_index);
					ReAnalize_tbl_Rows(del_index - 1, tbl_id);
				}
				function Up_tbl_row(element) {
					if (element.parentNode.parentNode.sectionRowIndex > 1) {
						var sec_indx = element.parentNode.parentNode.sectionRowIndex;
						var table = element.parentNode.parentNode.parentNode;
						var tbl_id = table.parentNode.id;
						var cell2_tmp = element.parentNode.parentNode.cells[1].innerHTML;
						var cell3_tmp = element.parentNode.parentNode.cells[2].innerHTML;
						element.parentNode.parentNode.parentNode.deleteRow(element.parentNode.parentNode.sectionRowIndex);
						// nel'zya prosto skopirovat' staryi innerHTML, t.k. ne sozdadutsya DOM elementy (for IE, Opera compatible).
						var row = table.insertRow(sec_indx - 1);
						var cell1 = document.createElement("td");
						var cell2 = document.createElement("td");
						var cell3 = document.createElement("td");
						var cell4 = document.createElement("td");
						var cell5 = document.createElement("td");
						cell1.align = 'center';
						cell1.innerHTML = 0;
						cell2.align = 'left';
						cell2.innerHTML = cell2_tmp;
						cell3.innerHTML = cell3_tmp;
						cell4.innerHTML = '<a href="javascript: void(0);" onclick="javascript:Delete_tbl_row(this); return false;" title="<?php echo _JLMS_DELETE; ?>"><img src="<?php echo ADMIN_IMAGES_PATH; ?>publish_x.png" border="0" alt="<?php echo _JLMS_DELETE; ?>"></a>';
						cell5.innerHTML = '<a href="javascript: void(0);" onclick="javascript:Up_tbl_row(this); return false;" title="<?php echo _JLMS_MOVE_UP; ?>"><img src="<?php echo ADMIN_IMAGES_PATH; ?>uparrow.png" border="0" alt="<?php echo _JLMS_MOVE_UP; ?>"></a>';
						row.appendChild(cell1);
						row.appendChild(cell2);
						row.appendChild(cell3);
						row.appendChild(cell4);
						row.appendChild(cell5);
						row.appendChild(document.createElement("td"));
						row.appendChild(document.createElement("td"));
						ReAnalize_tbl_Rows(sec_indx - 2, tbl_id);
					}
				}
				function Down_tbl_row(element) {
					if (element.parentNode.parentNode.sectionRowIndex < element.parentNode.parentNode.parentNode.rows.length - 1) {
						var sec_indx = element.parentNode.parentNode.sectionRowIndex;
						var table = element.parentNode.parentNode.parentNode;
						var tbl_id = table.parentNode.id;
						var cell2_tmp = element.parentNode.parentNode.cells[1].innerHTML;
						var cell3_tmp = element.parentNode.parentNode.cells[2].innerHTML;
						element.parentNode.parentNode.parentNode.deleteRow(element.parentNode.parentNode.sectionRowIndex);
						var row = table.insertRow(sec_indx + 1);
						var cell1 = document.createElement("td");
						var cell2 = document.createElement("td");
						var cell3 = document.createElement("td");
						var cell4 = document.createElement("td");
						var cell5 = document.createElement("td");
						cell1.align = 'center';
						cell1.innerHTML = 0;
						cell2.align = 'left';
						cell2.innerHTML = cell2_tmp;
						cell3.innerHTML = cell3_tmp;
						cell4.innerHTML = '<a href="javascript: void(0);" onclick="javascript:Delete_tbl_row(this); return false;" title="<?php echo _JLMS_DELETE; ?>"><img src="<?php echo ADMIN_IMAGES_PATH; ?>publish_x.png" border="0" alt="<?php echo _JLMS_DELETE; ?>"></a>';
						cell5.innerHTML = '<a href="javascript: void(0);" onclick="javascript:Up_tbl_row(this); return false;" title="<?php echo _JLMS_MOVE_UP; ?>"><img src="<?php echo ADMIN_IMAGES_PATH; ?>uparrow.png" border="0" alt="<?php echo _JLMS_MOVE_UP; ?>"></a>';
						row.appendChild(cell1);
						row.appendChild(cell2);
						row.appendChild(cell3);
						row.appendChild(cell4);
						row.appendChild(cell5);
						row.appendChild(document.createElement("td"));
						row.appendChild(document.createElement("td"));
						ReAnalize_tbl_Rows(sec_indx, tbl_id);
					}
				}
				function Add_new_tbl_field(elem_field1,elem_field2,elem_field3,elem_field4, tbl_id, field_name, field_name2) {
					var new_start_hour_txt = getObj(elem_field1).options[getObj(elem_field1).selectedIndex].text;
					var new_start_hour_txt2 = getObj(elem_field1).value;
					var new_start_min_txt = getObj(elem_field2).options[getObj(elem_field2).selectedIndex].text;
					var new_start_min_txt2 = getObj(elem_field2).value;
					var new_end_hour_txt = getObj(elem_field3).options[getObj(elem_field3).selectedIndex].text;
					var new_end_hour_txt2 = getObj(elem_field3).value;
					var new_end_min_txt = getObj(elem_field4).options[getObj(elem_field4).selectedIndex].text;
					var new_end_min_txt2 = getObj(elem_field4).value;

					var tbl_elem = getObj(tbl_id);
					var row = tbl_elem.insertRow(tbl_elem.rows.length);
					var cell1 = document.createElement("td");
					var cell2 = document.createElement("td");
					var cell3 = document.createElement("td");
					var cell4 = document.createElement("td");
					var cell5 = document.createElement("td");
					var cell6 = document.createElement("td");
					var cell7 = document.createElement("td");
					var input_hidden = document.createElement("input");
					var input_hidden2 = document.createElement("input");

					input_hidden.type = "hidden";
					input_hidden.name = field_name;//"sf_hid_fields[]";
					input_hidden.value = new_start_hour_txt2+":"+new_start_min_txt2+":00";

					input_hidden2.type = "hidden";
					input_hidden2.name = field_name2;//"sf_hid_fields[]";
					input_hidden2.value = new_end_hour_txt2+":"+new_end_min_txt2+":00";

					cell1.align = 'center';
					cell1.innerHTML = '0';
					cell2.innerHTML = new_start_hour_txt+":"+new_start_min_txt+":00";
					cell2.appendChild(input_hidden);
					cell3.innerHTML = new_end_hour_txt+":"+new_end_min_txt+":00";
					cell3.appendChild(input_hidden2);
					cell4.innerHTML = '<a href="javascript: void(0);" onclick="javascript:Delete_tbl_row(this); return false;" title="<?php echo _JLMS_DELETE; ?>"><img src="<?php echo ADMIN_IMAGES_PATH; ?>publish_x.png" border="0" alt="<?php echo _JLMS_DELETE; ?>"></a>';
					cell5.innerHTML = '<a href="javascript: void(0);" onclick="javascript:Up_tbl_row(this); return false;" title="<?php echo _JLMS_MOVE_UP; ?>"><img src="<?php echo ADMIN_IMAGES_PATH; ?>uparrow.png" border="0" alt="<?php echo _JLMS_MOVE_UP; ?>"></a>';
					cell6.innerHTML = '';
					cell7.innerHTML = '';
					row.appendChild(cell1);
					row.appendChild(cell2);
					row.appendChild(cell3);
					row.appendChild(cell4);
					row.appendChild(cell5);
					row.appendChild(cell6);
					row.appendChild(cell7);
					ReAnalize_tbl_Rows(tbl_elem.rows.length - 2, tbl_id);
				}

				</script>
				<table  class="adminlist">
					<tr>
						<th valign="top" class="title"><?php echo _JLMS_CFG_CFG_YOUR_ATT_PERDS; ?></th>
					</tr>
					<tr>
						<td >
						<div style="float:left; width:200px "><?php echo _JLMS_CFG_STARTING_TIME; ?>:<br />
						<?php echo _JLMS_TM_HOUR; ?>:
						<select name="start_hour" id="start_hour" class="text_area">
						<?php
						for ($i=1; $i<=24; $i++){
							if ($i < 10){
								$i = "0".$i;
							}
							echo "<option value=$i>$i</option>";
						}?>
						</select>
						<?php echo _JLMS_TM_MINUTE; ?>:
						<select name="start_min" id="start_min" class="text_area">
						<?php
						for ($i=0; $i<=60; $i=$i+5){
							if ($i < 10){
								$i = "0".$i;
							}
							echo "<option value=$i>$i</option>";
						}?><>
						</select>
						</div>
						<div style="float:left ">
						<?php echo _JLMS_CFG_ENDING_TIME; ?>: <br />
						<?php echo _JLMS_TM_HOUR; ?>r:&nbsp;&nbsp;
						<select name="end_hour" id="end_hour" class="text_area">
						<?php for ($i=1; $i<=24; $i++){
							if ($i < 10){
								$i = "0".$i;
							}
							echo "<option value=$i>$i</option>";
						}?>
						</select>
						<?php echo _JLMS_TM_MINUTE; ?>:&nbsp;&nbsp;
						<select name="end_min" id="end_min" class="text_area">
						<?php
						for ($i=0; $i<=60; $i=$i+5){
							if ($i < 10){
								$i = "0".$i;
							}
							echo "<option value=$i>$i</option>";
						}?>
						</select>&nbsp;&nbsp;
						<input class="text_area" type="button" name="add_new_scale" style="width:70px" value="<?php echo _JLMS_ADD; ?>" onclick="javascript:Add_new_tbl_field('start_hour','start_min','end_hour','end_min', 'jlms_periods', 'sf_hid_scale[]', 'sf_hid_scale2[]');">
						</div>
						</td>
					</tr>
				</table>
				<table height="200px" class="adminlist">
					<tr>
						<td valign="top">
							<table class="adminlist" id="jlms_periods">
								<thead>
									<tr>
										<th width="20px" align="center">#</th>
										<th width="100px"><?php echo _JLMS_CFG_PERIOD_BEGIN; ?></php></th>
										<th width="100px" align="center" class="title"><?php echo _JLMS_CFG_PERIOD_END; ?></th>
										<th width="20px" align="center" class="title"></th>
										<th width="20px"></th>
										<th width="20px"></th>
										<th width="auto"></th>
									</tr>
								</thead>
							<?php
							$k = 0; $ii = 1; $ind_last = count($periods);
							if ($periods){
							foreach ($periods as $frow) { ?>
								<tr class="<?php echo "row$k"; ?>">
									<td align="center"><?php echo $ii;?></td>
									<td align="left">
										<?php echo $frow->period_begin;?>
										<input type="hidden" name="sf_hid_scale[]" value="<?php echo $frow->period_begin;?>">
									</td>
									<td align="left">
										<?php echo $frow->period_end;?>
										<input type="hidden" name="sf_hid_scale2[]" value="<?php echo $frow->period_end;?>">
									</td>
									<td><a href="javascript: void(0);" onclick="javascript:Delete_tbl_row(this); return false;" title="<?php echo _JLMS_DELETE; ?>"><img src="<?php echo ADMIN_IMAGES_PATH; ?>publish_x.png" border="0" alt="<?php echo _JLMS_DELETE; ?>"></a></td>
									<td><?php if ($ii > 1) { ?><a href="javascript: void(0);" onclick="javascript:Up_tbl_row(this); return false;" title="<?php echo _JLMS_MOVE_UP; ?>"><img src="<?php echo ADMIN_IMAGES_PATH; ?>uparrow.png" border="0" alt="<?php echo _JLMS_MOVE_UP; ?>"></a><?php } ?></td>
									<td><?php if ($ii < $ind_last) { ?><a href="javascript: void(0);" onclick="javascript:Down_tbl_row(this); return false;" title="<?php echo _JLMS_MOVE_DOWN; ?>"><img src="<?php echo ADMIN_IMAGES_PATH; ?>downarrow.png" border="0" alt="<?php echo _JLMS_MOVE_DOWN; ?>"></a><?php } ?></td>
									<td></td>
								</tr>
							<?php
							$k = 1 - $k; $ii ++;
							}
							}
							?>
							</table>
						</td>
					</tr>
				</table>
				</fieldset>
				</div>
				<?php
				$tabs->endTab();

				/////////////////////////////////////////////////////////////
				//	Users settings

				$tabs->startTab( _JLMS_USERS ,"user-page");?>
				<div class="width-100">
				<fieldset class="adminform">
				<table >
					<tr>
						<td width="185px"><?php echo _JLMS_CFG_ALLOW_USERS_IMPORT; ?>:</td>
						<td>
						<fieldset class="radio">
						<?php echo $lists['allow_import_users']; ?><?php
						echo mosToolTip( _JLMS_CFG_TIP_ALLOW_USERS_IMPORT );
						?>
						</fieldset>
						</td>
					</tr>
					<tr><td><?php echo _JLMS_CFG_IMPORT_USER_PASS; ?>:</td>
						<td><input class="text_area" type="text" name="new_user_password" size="50" value="<?php echo str_replace('"', '&quot;', $row->new_user_password); ?>"/><?
						echo mosToolTip( _JLMS_CFG_TIP_IMPORT_USER_PASS );
						?></td>
					</tr>
				</table>
				</fieldset>
				</div>
				<?php
				$tabs->endTab();
				/////////////////////////////////////////////////////////////
				//	Payments settings

				$tabs->startTab( _JLMS_PAYMENTS,"pay-page");?>
				<table class="adminlist">
					<tr><td>
					<div class="width-100">
					<fieldset class="adminform"><legend><?php echo _JLMS_CFG_SSL_SETTINGS; ?></legend>
						<table cellpadding="0" cellspacing="0" border="0">
							<tr><td width="200">Secure checkout:</td>
								<td>
								<fieldset class="radio">
								<?php echo $lists['use_secure_checkout'];
								echo mosToolTip( _JLMS_CFG_TIP_SSL_SETTINGS );
								?>
								</fieldset>
								</td>
							</tr>
							<tr><td><?php echo _JLMS_CFG_SECR_CRS_REG; ?>:</td>
								<td>
								<fieldset class="radio">
								<?php echo $lists['use_secure_enrollment'];
								echo mosToolTip( _JLMS_CFG_TIP_SECR_CRS_REG );
								?>
								</fieldset>
								</td>
							</tr>
							<tr><td><?php echo _JLMS_CFG_SECURE_URL; ?>:</td>
								<td><input class="text_area" type="text" name="secure_url" size="50" value="<?php echo str_replace('"', '&quot;', $row->secure_url); ?>"/><?php
								echo mosToolTip( _JLMS_CFG_TIP_SECURE_URL );
								?></td>
							</tr>
						</table>
					</fieldset>
					</div>
					</td></tr>
					<tr><td>
					<div class="width-100">
					<fieldset class="adminform"><legend><?php echo _JLMS_CFG_CHECKOUT_OPTS; ?></legend>
						<table cellpadding="0" cellspacing="0" border="0"> 
						<tr><td width="200"><?php echo _JLMS_CFG_CURRENCY_CODE; ?>:</td>
							<td><input class="text_area" type="text" name="jlms_cur_code" size="3" maxlength="3" value="<?php echo str_replace('"', '&quot;', $row->jlms_cur_code); ?>"/><?php
							echo mosToolTip( _JLMS_CFG_TIP_CURRENCY_CODE );
							?></td>
						</tr>
						<tr><td><?php echo _JLMS_CFG_CURRENCY_SIGN; ?>:</td>
							<td><input class="text_area" type="text" name="jlms_cur_sign" size="3" maxlength="8" value="<?php echo str_replace('"', '&quot;', $row->jlms_cur_sign); ?>"/><?php
							echo mosToolTip( _JLMS_CFG_TIP_CURRENCY_SIGN );
							?></td>
						</tr>
						<tr><td><?php echo _JLMS_CFG_ENABLE_TAX_COUNT; ?>:</td>
							<td><fieldset class="radio"><?php echo $lists['enabletax']; ?><?php
							echo mosToolTip( _JLMS_CFG_TIP_ENABLE_TAX_COUNT );
							?></fieldset></td>
						</tr>
						<tr><td><?php echo _JLMS_CFG_ENABLE_INVS_PDF; ?>:</td>
							<td>
							<fieldset class="radio">
							<?php echo $lists['subscr_status_email']; ?><?php
							echo mosToolTip( _JLMS_CFG_TIP_ENABLE_INVS_PDF );
							?>
							</fieldset>
							</td>
						</tr>
						<tr><td><?php echo _JLMS_CFG_ENABLE_TERMS_CONDS; ?>:</td>
							<td>
							<fieldset class="radio">
							<?php echo $lists['enableterms']; ?><?php
							echo mosToolTip( _JLMS_CFG_TIP_ENABLE_TERMS_CONDS );
							?>
							</fieldset>
							</td>
						</tr>
						<tr><td valign="top"><?php echo _JLMS_CFG_SUBS_TERMS_CONDS; ?>:</td>
							<td><textarea class="text_area" cols="50" rows="3" style="width:300px; height:100px" name="jlms_terms"><?php echo htmlspecialchars($row->jlms_terms, ENT_QUOTES); ?></textarea></td>
						</tr>
						</table>
					</fieldset>
					</div>
					</td></tr>
				</table>
				<?php
				$tabs->endTab();
				/////////////////////////////////////////////////////////////
				//	Plugins settings

				$fw = (isset($lists['forum_warning']) && $lists['forum_warning']) ? true : false;
				$tabs->startTab((($fw?"<b><font color=\"red\">":'')._JLMS_PLUGINS.($fw?"</font></b>":'')),"plugin-page");?>	
				<script type="text/javascript" language="javascript">
				var is_start_c = 0;
				function jlms_Change() {
					var form=document.adminForm;
					if (form.quiz_hs_offset_manual_correction.value == 0) {
						form.quiz_hs_offset_div_class.disabled = true;
						form.quiz_hs_ofset_manual_value.disabled = true;
					} else {
						form.quiz_hs_offset_div_class.disabled = false;
						form.quiz_hs_ofset_manual_value.disabled = false;
					}
				}
				</script>
				<table class="adminlist">
				<tr><td>
				<div class="width-100">
				<fieldset class="adminform" style="text-align:left "><legend><?php echo _JLMS_CFG_QUIZ_SETTINGS; ?></legend>
				<table>
					<tr style="display:none; visibility:hidden"><td width="250px"><?php echo _JLMS_CFG_QUIZ_ENABLE; ?>:</td>
						<td>
						<fieldset class="radio">
						<?php echo $lists['plugin_quiz'];?>
						</fieldset>
						</td>
					</tr>
					<tr><td width="250px"><?php echo _JLMS_CFG_DRG_AND_DRP_BLK_W; ?>:</td>
						<td><input class="text_area" type="text" name="quiz_match_max_width" size="3" value="<?php echo $row->quiz_match_max_width; ?>"/> px</td>
					</tr>
					<tr><td><?php echo _JLMS_CFG_DRG_AND_DRP_BLK_W; ?>:</td>
						<td><input class="text_area" type="text" name="quiz_match_max_height" size="3" value="<?php echo $row->quiz_match_max_height; ?>"/> px</td>
					</tr>
					<tr><td><?php echo _JLMS_CFG_SHW_PROGRESS_BR; ?>:</td>
						<td>
						<fieldset class="radio">
						<?php echo $lists['quiz_progressbar'];
						echo mosToolTip( _JLMS_CFG_TIP_SHW_PROGRESS_BR );
						?>
						</fieldset>
						</td>
					</tr>
					<tr><td><?php echo _JLMS_CFG_PROGRESS_BR_W; ?>:</td>
						<td><input class="text_area" type="text" name="quiz_progressbar_width" size="3" value="<?php echo $row->quiz_progressbar_width; ?>"/> px</td>
					</tr>
					<tr><td><?php echo _JLMS_CFG_ENBL_HIGHLGHT_F_PR_BR; ?>:</td>
						<td>
						<fieldset class="radio">
						<?php echo $lists['quiz_progressbar_highlight'];?>
						</fieldset>
						</td>
					</tr>
					<tr><td><?php echo _JLMS_CFG_ENBL_SMOOTH_F_PR_BR; ?>:</td>
						<td>
						<fieldset class="radio">
						<?php echo $lists['quiz_progressbar_smooth'];?>
						</fieldset>
						</td>
					</tr>
				</table>
				</fieldset>
				</td></tr>
				<tr><td>				
				<fieldset class="adminform"><legend><?php echo _JLMS_CFG_ONLINE_HLP; ?></legend>
						<table cellpadding="0" cellspacing="0" border="0">
						<tr><td width="200"><?php echo _JLMS_CFG_LNK_ONLINE_HLP_S; ?>:</td>
							<td>
							<input type="text" class="text_area" style="width:300px" name="jlms_help_link" value="<?php echo str_replace('"', '&quot;', $row->jlms_help_link);?>" />
							</td>
						</tr>
						</table>
					</fieldset>
				</td></tr>
				<tr><td>
				<fieldset class="adminform"><legend><?php echo _JLMS_CFG_CRS_FORUM_SETT; ?> ( <?php echo  mosToolTip( _JLMS_CFG_TIP_SMF_IN_SHORT );?> <?php echo _JLMS_CFG_READ_MORE_AB_SMF; ?> <a style="text-decoration:underline" href="http://www.simplemachines.org">here</a>)</legend>
				<table >
					<tr><td width="200px"><?php echo _JLMS_CFG_ENBL_CRS_FORUM; ?>:</td>
						<td>
						<fieldset class="radio">
						<?php echo $lists['plugin_forum']; echo mosToolTip( _JLMS_CFG_TIP_ENBL_CRS_FORUM );?>
						</fieldset>
						</td>
					</tr>
					<tr><td width="185px"><?php echo _JLMS_CFG_SITE_ABS_PATH; ?>:</td>
						<td><b><font color="green"><?php echo $site_abs_path;?></font></b></td>
					</tr>
					<tr><td><?php echo $fw?'<b><font color="red">':'';?><?php echo _JLMS_CFG_FRM_ABS_PATH; ?>:<?php echo $fw?'</font></b>':'';?></td>
						<td align="left"><input class="text_area" type="text" name="forum_path" size="50" value="<?php echo $row->forum_path; ?>"/>
						<?php echo mosToolTip( _JLMS_CFG_TIP_FRM_ABS_PATH );?>
						</td>
					</tr>
					<tr>
						<td colspan="2">
						<?php echo _JLMS_CFG_USE_SMF_MENU; ?>
						</td>
					</tr>
				</table>
				</fieldset>
				</td></tr>
				<tr><td>
				<fieldset class="adminform"><legend><?php echo _JLMS_CFG_PTHW_PLG; ?></legend>
				<table>
					<tr><td width="200"><?php echo _JLMS_CFG_INCL_HOMEP_IT; ?>:</td>
						<td>
							<fieldset class="radio">
							<?php echo $lists['pathway_show_lmshome'];
							echo mosToolTip( _JLMS_CFG_TIP_INCL_HOMEP_IT );
							?>
							</fieldset>
						</td>
					</tr>
					<tr><td><?php echo _JLMS_CFG_CRS_HOMEP_IT; ?>:</td>
						<td>
							<fieldset class="radio">
							<?php echo $lists['pathway_show_coursehome'];
							echo mosToolTip( _JLMS_CFG_TIP_CRS_HOMEP_IT );
							?>
							</fieldset>
						</td>
					</tr>
				</table>
				</fieldset>
				</td></tr>
				<tr><td>
				<fieldset class="adminform"><legend><?php echo _JLMS_CFG_USR_NOTES_PLG; ?></legend>
					<table>
					<tr><td width="200"><?php echo _JLMS_CFG_ENBL_USR_NOTES_PLG; ?>:</td>
						<td>
							<fieldset class="radio">
							<?php echo $lists['jlms_notecez'];
							echo mosToolTip( _JLMS_CFG_TIP_ENBL_USR_NOTES_PLG );
							?>
							</fieldset>
						</td>
					</tr>

				</table>
				</fieldset>
				</td></tr>
				</table>
				</div>
	<?php		$tabs->endTab();
				/////////////////////////////////////////////////////////////
				//	Users settings
				$tabs->endPane(); ?>
			</td>
		</tr>
	</table>
<?php if (!class_exists('JToolBarHelper')) { ?>
<table align="center">
<?php
joomla_lms_adm_html::writableCell_cfg( _JLMS_CFG_SCORMS_FOLDER, $row->scorm_folder, true );
joomla_lms_adm_html::writableCell_cfg( _JLMS_CFG_BACKUPS_FOLDER, $row->jlms_backup_folder );
joomla_lms_adm_html::writableCell_cfg( _JLMS_CFG_DOCS_FOLDER, $row->jlms_doc_folder );
joomla_lms_adm_html::writableCell_cfg( _JLMS_CFG_CERTS_FOLDER, $row->jlms_crtf_folder );
joomla_lms_adm_html::writableCell_cfg( _JLMS_CFG_TEMP_FOLDER, $row->temp_folder, true );
?>
</table>
<?php } ?>
</td></tr></table>

		<input type="hidden" name="option" value="<?php echo $option; ?>" />
	  	<input type="hidden" name="boxchecked" value=""/>
		<input type="hidden" name="task" value=""/>
		</form>
		</div>		
		<script type="text/javascript" language="javascript">
		<!--//
		setTimeout("cs_init()",200);
		//-->
		</script>
		<?php
	}

	function writableCell_cfg( $txt, $folder, $add_a_p = false ) {
		echo '<tr>';
		echo '<td class="item">' . $txt . '/</td>';
		echo '<td align="left">';
		if ($folder) {
			if ($add_a_p) {
				$folder = JPATH_SITE . "/" . $folder;
			}
			if (file_exists( $folder ) ) {
				echo is_writable( $folder ) ? '<b><font color="green">'._JLMS_WRITABLE.'</font></b>' : '<b><font color="red">'._JLMS_UNWRITABLE.'</font></b>';
			} else {
				echo '<b><font color="red">! '._JLMS_UNACCESSIBLE.' !</font></b>';
			}
		} else {
			echo '<b><font color="red">'._JLMS_CFG_PLEASE_SETUP.'</font></b>';
		}
		echo '</td>';
		echo '</tr>';
	}

	function jlms_admin_writable_cfg( $folder, $add_a_p = false ) {
		if ($folder) {
			if ($add_a_p) {
				$folder = JPATH_SITE . "/" . $folder;
			}
			if (file_exists( $folder ) ) {
				return true;
			}
		}
		return false;

	}

	##########################################################################
	###	--- ---   JAVASCRIPTS	 --- --- ###
	##########################################################################
	function SF_JS_getObj() {
		?>
		<script language="javascript" type="text/javascript">
		function getObj(name)
		{
			if (document.getElementById)  {  return document.getElementById(name);  }
			else if (document.all)  {  return document.all[name];  }
			else if (document.layers)  {  return document.layers[name];  }
		}
		</script>
		<?php
	}
	##########################################################################
	###	--- ---  COURSE	BACKUPS	 	--- --- ###
	##########################################################################
	function JLMS_coursesList( $rows, $pageNav, $option){
		JHTML::_('behavior.tooltip');
		?>
		<form action="index.php" method="post" name="adminForm">		
<table width="100%" >
	<tr>
	<td valign="top" width="220">
	<div>
		<?php echo joomla_lms_adm_html::JLMS_menu();?>
	</div>
	</td>
	<td valign="top">
	<?php if (!class_exists('JToolBarHelper')) { ?>
		<table class="adminheading">
			<tr>
				<th class="dbbackup">
					<?php echo _JOOMLMS_COMP_NAME;?>: <small><?php echo _JLMS_BCK_CRSS_LIST; ?></small>
				</th>
			</tr>
		</table>
	<?php } ?>	
		<table width="100%" border="0">
			<tr>
				<td valign="top">
					<table class="adminlist">
					<thead>
						<tr>
							<th width="20px">#</th>
							<th class="title" width="450px" ><?php echo _JLMS_BCK_CRSS_NAME; ?></th>
							<th class="title" width="30px" ><?php echo _JLMS_ID; ?></th>
							<th class="title"></th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<td colspan="4">
							<?php echo $pageNav->getListFooter(); ?>
							</td>
						</tr>
					</tfoot>
					<tbody>
					<?php
					$k = 0;
					for ($i=0, $n=count($rows); $i < $n; $i++) {
						$row = $rows[$i];
						?>
						<tr class="<?php echo "row$k"; ?>">
							<td><?php echo $pageNav->rowNumber( $i ); ?></td>
							<td align="left">
								<a href="index.php?option=com_joomla_lms&amp;task=view_course_backup&amp;id=<?php echo $row->id;?>"><?php echo $row->course_name;?></a>
							</td>
							<td align="left">
								<?php echo $row->id;?>
							</td>

							<td></td>
						</tr>
						<?php
						$k = 1 - $k;
					}?>
					</tbody>
					</table>
				</td>
			</tr>
		</table>
</td></tr></table>		
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="task" value="courses_list" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0" />		
		</form>
		<?php
	}


	function JLMS_coursebackupsList(  $rows, $pageNav, $option, $course_id, $c_name){
		$db = & JFactory::getDbo();
		$query = "SELECT lms_config_value FROM `#__lms_config` WHERE lms_config_var = 'jlms_backup_folder'";
		$db->setQuery($query);
		$backup_path = $db->loadResult();
		JHTML::_('behavior.tooltip');
		?>
		<form action="index.php" method="post" name="adminForm">		
<table width="100%" >
<tr>
	<td valign="top" width="220">
	<div>
		<?php echo joomla_lms_adm_html::JLMS_menu();?>
	</div>
	</td>
	<td valign="top">
	<?php if (!class_exists('JToolBarHelper')) { ?>
		<table class="adminheading">
		<tr>
			<th class="dbbackup">
			<?php echo _JOOMLMS_COMP_NAME;?>: <small> <?php str_replace( '{crs_name}', $c_name, _JLMS_BCK_NAME_BK_LIST ); ?></small>
			</th>
		</tr>
		</table>
	<?php } ?>	
		<table width="100%" border="0">
			<tr>
				<td valign="top" align="center">
				<table class="content">
				<?php
				joomla_lms_adm_html::writableCell( $backup_path );
				?>
				</table>
					<table class="adminlist" width="100%">
					<thead>
					<tr>
						<th width="20px">#</th>
						<th width="20px" class="title" ><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>
						<th class="title" width="350px" ><?php echo _JLMS_BCK_NAME; ?></th>
						<th class="title" width="150px" ><?php echo _JLMS_DATE; ?></th>
						<th class="title" width="50px" ><?php echo _JLMS_DOWNLOAD; ?></th>
						<th class="title" width="30px" ><?php echo _JLMS_ID; ?></th>
						<th class="title"></th>
					</tr>
					</thead>
					<tfoot>
						<tr>
							<td colspan="7">
							<?php echo $pageNav->getListFooter(); ?>
							</td>
						</tr>
					</tfoot>
					<?php
					$k = 0;
					for ($i=0, $n=count($rows); $i < $n; $i++) {
						$row = $rows[$i];
						//$link 	= 'index.php?option=com_surveyforce&task=surveys&catid='. $row->id;

						$checked = mosHTML::idBox( $i, $row->id);?>
						<tr class="<?php echo "row$k"; ?>">
							<td><?php echo $pageNav->rowNumber( $i ); ?></td>
							<td><?php echo $checked; ?></td>

							<td align="left">
								<?php echo $row->name . '(';

								if (file_exists($backup_path.'/'.$row->name)) {
									if (is_readable($backup_path.'/'.$row->name)) {
										echo round(filesize($backup_path.'/'.$row->name)/1000, 1)."&nbsp;Kb";
									} else {
										echo _JLMS_FILE_UNR;
									}
								} else {
									echo _JLMS_F_DNT_EXISTS_OR_UNR;
								}
								echo ')'; ?>
							</td>
							<td align="left">
								<?php echo $row->backupdate;?>
							</td>
							<td align="left">
								<a href="index.php?option=com_joomla_lms&amp;task=download&amp;backup_id=<?php echo $row->id;?>" ><img src="<?php echo ADMIN_IMAGES_PATH;  ?>filesave.png" border="0" alt="<?php echo _JLMS_DOWNLOAD; ?>" title="<?php echo _JLMS_DOWNLOAD; ?>" /></a>
							</td>
							<td align="left">
								<?php echo $row->id;?>
							</td>
							<td></td>
						</tr>
						<?php
						$k = 1 - $k;
					}?>
					</table>
				</td>
			</tr>
		</table>	
	</td></tr></table>
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="task" value="view_course_backup" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="id" value="<?php echo $course_id; ?>" />
		<input type="hidden" name="hidemainmenu" value="0" />		
		</form>
		<?php
	}
	function JLMS_courseImport( $option ){
		JHTML::_('behavior.tooltip');
		?>

<form enctype="multipart/form-data" action="index.php" method="post" name="adminForm">
	<table width="100%" >
	<tr>
		<td valign="top" width="220">
		<div>
			<?php echo joomla_lms_adm_html::JLMS_menu();?>
		</div>
		</td>
		<td valign="top">
		<div class="width-100">
		<fieldset class="adminform">
		<?php if (!class_exists('JToolBarHelper')) { ?>
		<table class="adminheading">
		<tr>
			<th class="install">
			<?php echo _JOOMLMS_COMP_NAME;?>: <small><?php echo _JLMS_CRSS_IMP_CRS; ?></small>
			</th>
		</tr>
		</table>
		<?php } ?>		
		<table width="100%" border="0">
			<tr>
				<td valign="top">
					<table >
					<tr>
						<th>
						<?php echo _JLMS_CRSS_UPL_PKG_F; ?>
						</th>
					</tr>
					<tr>
						<td align="left">
						<?php echo _JLMS_CRSS_PKG_F; ?>:
						<input class="text_area" name="jlms_ifile" type="file" size="70"/>
						<input class="button" type="submit" value="<?php echo _JLMS_CRSS_UPL_F_INST; ?>" />
						</td>
					</tr>
					<tr>
						<td height="50px">&nbsp;

						</td>
					</tr>
					</table>
				</td>
			</tr>
		</table>
</td></tr></table>
		</fieldset>
		</div>
		<input type="hidden" name="task" value="course_import"/>
		<input type="hidden" name="option" value="<?php echo $option;?>"/>
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0" />		
		</form>
		<br />

		<?php

	}
	function JLMS_ListCoursesTemplate( $rows, $pageNav, $option)
	{
		?>
		<form action="index.php" method="post" name="adminForm">		
		<table width="100%" >
		<tr>
			<td valign="top" width="220">
			<div>
				<?php echo joomla_lms_adm_html::JLMS_menu();?>
			</div>
			</td>
			<td valign="top">		
			<?php if (!class_exists('JToolBarHelper')) { ?>
			<table class="adminheading">
			<tr>
				<th class="install">
				<?php echo _JOOMLMS_COMP_NAME;?>: <small><?php echo _JLMS_CRSS_TPLS; ?></small>
				</th>
			</tr>
			</table>
			<?php }?>		
			<table width="100%" border="0" class="adminlist">

					<thead>
					<tr>
						<th width="20px">#</th>
						<th width="20px" class="title" ><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>
						<th class="title"  ><?php echo _JLMS_CRSS_TPL_NAME; ?></th>
						<th class="title" width="150px" ><?php echo _JLMS_CRSS_CRS_NAME; ?></th>

					</tr>
					</thead>
					<tfoot>
						<tr>
							<td colspan="7">
							<?php echo $pageNav->getListFooter(); ?>
							</td>
						</tr>
					</tfoot>
						<?php
						for($i=0;$i<count($rows);$i++)
						{
						$checked = mosHTML::idBox( $i, $rows[$i]->id);?>


						<tr class="<?php echo "row".($i%2); ?>">
							<td><?php echo $pageNav->rowNumber( $i ); ?></td>
							<td><?php echo $checked; ?></td>
							<td align="left" width="50%">
								<?php echo $rows[$i]->templ;?>
							</td>
							<td align="left" width="50%">
								<?php echo $rows[$i]->course_name;?>
							</td>
						</tr>
						<?php
						}
						?>
				</table>				
				</td>
				</tr>
		</table>	
		<input type="hidden" name="task" value="courses_template"/>
		<input type="hidden" name="option" value="<?php echo $option;?>"/>
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0" />		
		</form>
		<br />
		<?php
	}
	function JLMS_ListCoursesTemplAdd( $row, $lists, $option)
	{
		?>
	<script language="javascript" type="text/javascript">
	<!--
	function submitbutton(pressbutton) {
		var form = document.adminForm;

		if (pressbutton == 'courses_template') {
			submitform( pressbutton );
			return;
		}
		if (form.templ.value == '') {
			alert( '<?php echo _JLMS_CRSS_ENTER_TPL_NAME; ?>' );
		} else {
			<?php if (isset($row->id) && $row->id) { ?>
				submitform( pressbutton );
			<?php } else { ?>
			if (form.courses_list.options[form.courses_list.selectedIndex].value == '0' || form.courses_list.options[form.courses_list.selectedIndex].value == 0) {
				alert( '<?php echo _JLMS_CRSS_SELECT_CRS_F_LIST; ?>' );
			} else {
				submitform( pressbutton );
			}
			<?php } ?>
		}
	}
	
	<?php if( JLMS_J16version() ) { ?>
	Joomla.submitbutton = submitbutton;
	<?php } ?>
	//-->
	</script>
		<form action="index.php" method="post" name="adminForm">		
		<table width="100%" >
		<tr>
			<td valign="top" width="220">
			<div>
				<?php echo joomla_lms_adm_html::JLMS_menu();?>
			</div>
			</td>
			<td valign="top">
			<div class="width-100">
			<fieldset class="adminform">
			<?php if (!class_exists('JToolBarHelper')) { ?>
			<table class="adminheading">
			<tr>
				<th class="install">
				<?php echo _JOOMLMS_COMP_NAME;?>: <small><?php echo isset($row->id)?_JLMS_CRSS_EDIT_CRS_TPL:_JLMS_CRSS_NEW_CRS_TPL; ?></small>
				</th>
			</tr>
			</table>
			<?php } ?>			
			<table width="100%" border="0">
				<tr>
					<td valign="top">
						<table >
						<tr>
							<th colspan="2">
							<?php echo _JLMS_CRSS_CRS_TPL_PROP; ?>
							</th>
						</tr>

						<tr>
							<td align="left">
								<?php echo _JLMS_CRSS_TPL_NAME; ?>:
							</td>
							<td align="left">
								<input type="text" name="templ" style="width: 266px;" class="text_area" value="<?php echo isset($row->templ)?str_replace('"', '&quot;',$row->templ):'';?>"/>
							</td>
						</tr>
						<?php
						if(!isset($row->id)){
						?>
						<tr>
							<td>
								<?php echo _JLMS_COURSE; ?>:
							</td>
							<td>
								<?php echo $lists['courses_list']?>
							</td>
						</tr>
						<?php
						}
						?>
						</table>
					</td>
				</tr>
			</table>
			</fieldset>
			</div>
	</td></tr></table>
		<input type="hidden" name="task" value="courses_templ_save"/>
		<input type="hidden" name="option" value="<?php echo $option;?>"/>
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0" />
		<input type="hidden" name="tpl_id" value="<?php echo isset($row->id)?$row->id:0;?>" />		
		</form>
		<br />
		<?php
	}


	
	
	
	##########################################################################
	###	--- ---  TOTAL	BACKUPS	 	--- --- ###
	##########################################################################
	function JLMS_backupsListhtml( &$rows, &$pageNav, $option ) {
		$db = & JFactory::GetDbo();
		JHTML::_('behavior.tooltip');
		$query = "SELECT lms_config_value FROM `#__lms_config` WHERE lms_config_var = 'jlms_backup_folder'";
		$db->setQuery($query);
		$backup_path = $db->loadResult();
		?>

<form action="index.php" method="post" name="adminForm">
<div class="width-100">
<fieldset class="adminform">
<table width="100%" >
	<tr>
		<td valign="top" width="220">
		<div>
			<?php echo joomla_lms_adm_html::JLMS_menu();?>
		</div>
		</td>
		<td valign="top">	
		<?php if (!class_exists('JToolBarHelper')) { ?>
		<table class="adminheading">
		<tr>
			<th class="dbbackup">
			<?php echo _JOOMLMS_COMP_NAME;?>: <small><?php echo _JLMS_BCK_TOTAL_LIST; ?></small>
			</th>
		</tr>
		</table>
		<?php } ?>
		<table width="100%" border="0">
			<tr>
				<td valign="top" align="center">
				<table class="content">
				<?php
				joomla_lms_adm_html::writableCell( $backup_path );
				?>
				</table>
					<table class="adminlist">
					<thead>
						<tr>
							<th width="20px">#</th>
							<th width="20px" class="title"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>
							<th class="title" width="300px"><?php echo _JLMS_BCK_NAME; ?></th>
							<th class="title" width="200px" ><?php echo _JLMS_DATE; ?></th>
							<th class="title" width="100px" align="center"><?php echo _JLMS_BCK_FILE_SIZE; ?></th>
							<th class="title" width="30px"><?php echo _JLMS_ID; ?></th>
							<th class="title"></th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<td colspan="7">
							<?php echo $pageNav->getListFooter(); ?>
							</td>
						</tr>
					</tfoot>
					<tbody>
					<?php
					$k = 0;
					for ($i=0, $n=count($rows); $i < $n; $i++) {
						$row = $rows[$i];
						//$link 	= 'index.php?option=com_surveyforce&task=surveys&catid='. $row->id;
						$checked = mosHTML::idBox( $i, $row->id);?>
						<tr class="<?php echo "row$k"; ?>">
							<td><?php echo $pageNav->rowNumber( $i ); ?></td>
							<td><?php echo $checked; ?></td>
							<td align="left">
								<?php echo $row->name;?>
							</td>
							<td align="left">
								<?php echo $row->backupdate;?>
							</td>
							<td align="center">
								<?php
								if (file_exists($backup_path.'/'.$row->name)) {
									if (is_readable($backup_path.'/'.$row->name)) {
										echo round(filesize($backup_path.'/'.$row->name)/1000, 1);
									} else {
										echo _JLMS_FILE_UNR;
									}
								} else {
									echo _JLMS_F_DNT_EXISTS_OR_UNR;
								}
								?>
							</td>
							<td align="left">
								<?php echo $row->id;?>
							</td>
							<td></td>
						</tr>
						<?php
						$k = 1 - $k;
					}?>
					</tbody>
					</table>
				</td>
			</tr>
		</table>
</td></tr></table>
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="task" value="backup" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0" />
		</form>			
		<?php
	}

	function writableCell( $folder ) {
		echo '<tr>';
		echo '<td class="item">' . $folder . '/</td>';
		echo '<td align="left">';
		echo is_writable( $folder ) ? '<b><font color="green">'._JLMS_WRITABLE.'</font></b>' : '<b><font color="red">'._JLMS_UNWRITABLE.'</font></b>' . '</td>';
		echo '</tr>';
	}
	##########################################################################
	###	--- ---   	USERS	 	--- --- ###
	##########################################################################
	function JLMS_showUsersList( &$rows, &$pageNav, $option ) {
		JHTML::_('behavior.tooltip');
		?>

<form action="index.php" method="post" name="adminForm">
<table width="100%" >
	<tr>
		<td valign="top" width="220">
		<div>
			<?php echo joomla_lms_adm_html::JLMS_menu();?>
		</div>
		</td>
		<td valign="top">	
	<?php if (!class_exists('JToolBarHelper')) {?>
		<table class="adminheading">
		<tr>
			<th class="user">
			<?php echo _JOOMLMS_COMP_NAME;?>: <small><?php echo _JLMS_USERS_LIST; ?></small>
			</th>
		</tr>
		</table>
	<?php } ?>		
		<table width="100%" border="0">
			<tr>
				<td valign="top">
					<table class="adminlist">
					<thead>
						<tr>
							<th width="20">#</th>
							<th width="20" class="title"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>
							<!--th class="title" width="40px">Active</th-->
							<th class="title"><?php echo _JLMS_USERNAME; ?></th>
							<th class="title"><?php echo _JLMS_TYPE; ?></th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<td colspan="4">
							<?php echo $pageNav->getListFooter(); ?>
							</td>
						</tr>
					</tfoot>
					<tbody>
					<?php
					$k = 0;
					for ($i=0, $n=count($rows); $i < $n; $i++) {
						$row = $rows[$i];
						//$link 	= 'index.php?option=com_surveyforce&task=surveys&catid='. $row->id;

						$img_published	= !$row->lms_block ? 'tick.png' : 'publish_x.png';
						$task_published	= !$row->lms_block ? 'unpublish_surv' : 'publish_surv';
						$alt_published 	= !$row->lms_block ? _JLMS_BLOCKED : _JLMS_ACTIVE;

						$checked = mosHTML::idBox( $i, $row->id);?>
						<tr class="<?php echo "row$k"; ?>">
							<td><?php echo $pageNav->rowNumber( $i ); ?></td>
							<td><?php echo $checked; ?></td>
							<!--td><a href="javascript: void(0);" onclick="return listItemTask('cb<?php echo $i;?>','<?php echo $task_published;?>')">
									<img src="images/<?php echo $img_published;?>" width="12" height="12" border="0" alt="<?php echo $alt_published; ?>" />
							</a></td-->
							<td align="left">
								<span><?php echo mosToolTip( '<b>'._JLMS_NAME.': </b>'.$row->name . "<br>" . '<b>'._JLMS_EMAIL.': </b>' . $row->email, _JLMS_USERS_JPROPS, 280, 'tooltip.png', $row->username );?></span>
							</td>
							<td align="left">
								<?php echo $row->lms_usertype;?>
							</td>
						</tr>
						<?php
						$k = 1 - $k;
					}?>
					</tbody>
					</table>
				</td>
			</tr>
		</table>	
	</td></tr></table>
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="task" value="users" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0" />		
		</form>
		<?php
	}

	function JLMS_editUser( &$row, &$lists, $option ) {
		JHTML::_('behavior.tooltip');
		?>
		<script language="javascript" type="text/javascript">
		<!--
		function submitbutton(pressbutton) {
			var form = document.adminForm;

			if (pressbutton == 'cancel_user') {
				submitform( pressbutton );
				return;
			}
			submitform( pressbutton );
		}
		
		<?php if( JLMS_J16version() ) { ?>
		Joomla.submitbutton = submitbutton;
		<?php } ?>
		//-->
		</script>

<form action="index.php" method="post" name="adminForm">
<table width="100%" >
	<tr>
		<td valign="top" width="220">
		<div>
			<?php echo joomla_lms_adm_html::JLMS_menu();?>
		</div>
		</td>
		<td valign="top">
		<div class="width-100">
		<fieldset class="adminform">
	<?php if (!class_exists('JToolBarHelper')) { ?>
		<table class="adminheading">
		<tr>
			<th class="user">
			<?php echo _JOOMLMS_COMP_NAME;?>:
			<small>
			<?php echo $row->id ? _JLMS_USERS_EDIT_USER : _JLMS_USERS_NEW_USER;?>
			</small>
			</th>
		</tr>
		</table>
	<?php } ?>	
		<table width="100%" border="0">
			<tr>
				<td valign="top">
					<table width="100%" >
						<tr>
							<th colspan="2"><?php echo _JLMS_USERS_USR_DETS; ?></th>
						<tr>
						<tr>
							<td align="right" width="20%"><?php echo _JLMS_USERS_SELECT_USR; ?>:</td>
							<td><?php echo $lists['jlms_users'];?></td>
						</tr>
						<tr>
							<td align="right" width="20%" valign="top"><?php echo _JLMS_USERS_SELECT_USR_TYPE; ?>:</td>
							<td><?php echo $lists['jlms_usertypes'];?></td>
						</tr>

					</table>
					<br />
				</td>
			</tr>
		</table>
		</fieldset>
		</div>
</td></tr></table>
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
		<input type="hidden" name="task" value="" />		
		</form>
		<?php
	}

	##########################################################################
	###	--- ---   	CLASSES	 	--- --- ###
	##########################################################################
	function JLMS_showClassesList( &$rows, &$pageNav, &$lists, $option, $members_without_group, $teacher_assistanse ) {
		global $JLMS_CONFIG;
		JHTML::_('behavior.tooltip');

		if ($JLMS_CONFIG->get('use_global_groups', 1)) {
?>
	<script type="text/javascript">
	<!--
	function submitbutton(pressbutton) {
		var form = document.adminForm;
		if(pressbutton == 'assign_user_group_manager') {
			form.task.value = 'group_managers';
			form.page.value = 'assign_user_group_manager';	
		}
		else {
			form.task.value = pressbutton;
		}
		form.submit();
	}
	
	<?php if( JLMS_J16version() ) { ?>
	Joomla.submitbutton = submitbutton;
	<?php } ?>
	//-->
	</script>
<form action="index.php" method="post" name="adminForm">
<table width="100%" >
	<tr>
		<td valign="top" width="220">
		<div>
			<?php echo joomla_lms_adm_html::JLMS_menu();?>
		</div>
		</td>
		<td valign="top">	
		<?php if (!class_exists('JToolBarHelper')) { ?>
		<table class="adminheading" cellpadding="0" cellspacing="0" border="0">
		<tr>
			<th class="categories">
			<?php echo _JOOMLMS_COMP_NAME;?>: <small><?php echo _JLMS_USERS_GLBL_GR_LIST; ?></small>
			</th>
		</tr>
		</table>
		<?php } ?>		
		<table width="100%" border="0">
			<tr>
				<td valign="top">
					<table class="adminlist">
					<thead>
					<tr>
						<th width="20">#</th>
						<th width="20" class="title"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>
						<th class="title"><?php echo _JLMS_USERS_GR_NAME; ?></th>
						<th class="title" width="25%"><?php echo _JLMS_MEMBERS; ?></th>
						<th class="title" width="25%"><?php echo _JLMS_MANAGERS; ?></th>
						<th class="title" width="1%">ID</th>
					</tr>
					</thead>
					<tfoot>
						<tr>
							<td colspan="6">
							<?php echo $pageNav->getListFooter(); ?>
							</td>
						</tr>
					</tfoot>
					<?php
					$k = 0;
					$i=0;
					$rows_parent = $rows;
					
					foreach($rows as $ka=>$row) {
						
						//for ($i=0, $n=count($rows); $i < $n; $i++) {
							//$row = $rows[$i];
							if($row->parent_id == 0) {
								$link 	= 'index.php?option=com_joomla_lms&amp;task=view_class_users_groups&amp;group_id='. $row->id;
								$link2 	= 'index.php?option=com_joomla_lms&amp;task=group_managers&amp;filt_groups='. $row->id;
								$checked = mosHTML::idBox( $i, $row->id);?>
								<tr class="<?php echo "row$k"; ?>">
									<td><?php echo $pageNav->rowNumber( $i ); ?></td>
									<td><?php echo $checked; ?></td>
									<td align="left">
										<a href="<?php echo $link;?>">
											<?php echo $row->ug_name;?>
										</a>
									</td>
									<td>
										<a href="<?php echo $link;?>">
											<?php echo $row->members;?>
										</a>
									</td>
									<td>
										<a href="<?php echo $link2;?>">
											<?php echo $row->managers;?> 
										</a>
									</td>
									<td>
										<?php echo $row->id;?> 
									</td>
								</tr>
								<?php
								$k = 1 - $k;
								$i++;
								
								foreach($rows_parent as $y=>$z) {
									//echo "<hr>";
									//echo $y."<br>";
								//for($j=0;$j<count($rows);$j++) {
									if($z->parent_id == $row->id) {
										//echo $z->id."<br>";
										$link 	= 'index.php?option=com_joomla_lms&amp;task=view_class_users_groups&amp;subgroup1_id='. $z->id .'&amp;group_id='. $z->parent_id;
										$link2 	= 'index.php?option=com_joomla_lms&amp;task=group_managers&amp;filt_groups='. $z->parent_id .'&amp;subgroup1_id='.$z->id;
										$checked = mosHTML::idBox( $i, $z->id);?>
										<tr class="<?php echo "row$k"; ?>">
											<td><?php echo $pageNav->rowNumber( $i ); ?></td>
											<td><?php echo $checked; ?></td>
											<td align="left" style="padding:0px; ">
												<table width="100%" cellpadding="0" cellspacing="0" border="0" >
													<tr>
														<td width="16" align="center" style="border:0px;"><img src="<?php echo JURI::root();?>components/com_joomla_lms/lms_images/treeview/sub2.png" alt=" " /></td>												
														<td style="border:0px;"><a href="<?php echo $link;?>">
															<?php echo $z->ug_name;?>
															</a>
														</td>
													</tr>
												</table>
											</td>
											<td>
												<a href="<?php echo $link;?>">
													<?php echo $z->members;?>
												</a>
											</td>
											<td>
												<!--<a href="<?php echo $link2;?>">
													<?php //echo $z->managers;?> 
												</a>-->
											</td>
											<td>
												<?php echo $z->id;?> 
											</td>
										</tr>
										<?php
										$k = 1 - $k;
										$i++;
										//unset($rows[$y]);
									}
								}
						}
					}?>
					</table>
				</td>
			</tr>
		</table>	
	</td></tr></table>
	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	<input type="hidden" name="task" value="classes" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="hidemainmenu" value="0" />
	<input type="hidden" name="page" value="" />	
	</form>
		<?php
		}
		else {
		?>
<form action="index.php" method="post" name="adminForm">
<table width="100%" >
	<tr>
		<td valign="top" width="220">
		<div>
			<?php echo joomla_lms_adm_html::JLMS_menu();?>
		</div>
		</td>
		<td valign="top">	
		<?php if (!class_exists('JToolBarHelper')) { ?>
		<table class="adminheading" cellpadding="0" cellspacing="0" border="0">
		<tr>
			<th class="categories">
			<?php echo _JOOMLMS_COMP_NAME;?>: <small><?php echo _JLMS_USERS_GRS_CLS_LIST; ?></small>
			</th>
			<td width="right">
				<table class="adminlist">
					<tr class="row1">
						<td nowrap="nowrap" style="padding:2px 10px 2px 10px;"><?php echo _JLMS_FILTER; ?>:&nbsp;&nbsp;</td>
						<td><?php echo $lists['jlms_courses'];?></td>
					</tr>
				</table>
			</td>
		</tr>
		</table>
		<?php } else { ?>
		<div style="width: 100%;">
		<table  align="right" style="width: 30%;" class="adminlist">
			<tr class="row1">
				<td nowrap="nowrap" style="padding:2px 10px 2px 10px;"><?php echo _JLMS_FILTER; ?>:&nbsp;&nbsp;</td>
				<td><?php echo $lists['jlms_courses'];?></td>
			</tr>
		</table>
		</div>
		<div style="clear: both;"><!--x--></div>
		<?php } ?>		
		<table width="100%" border="0">
			<tr>
				<td valign="top">
					<table class="adminlist">
					<thead>
					<tr>
						<th width="20">#</th>
						<th width="20" class="title"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>
						<th class="title"><?php echo _JLMS_USERS_GR_NAME; ?></th>
						<th class="title"><?php echo _JLMS_USERS_CRS_NAME; ?></th>
						<th class="title"><?php echo _JLMS_TEACHER; ?></th>
						<th class="title"><?php echo _JLMS_MEMBERS; ?></th>
					</tr>
					</thead>
					<tfoot>
						<tr>
							<td colspan="6">
							<?php echo $pageNav->getListFooter(); ?>
							</td>
						</tr>
					</tfoot>
					<tr class="row0">
						<td align="center">-</td>
						<td align="center">-</td>
						<td><a href="index.php?option=com_joomla_lms&amp;task=lms_users&amp;view_by=1<?php echo ((isset($lists['filt_course']) && $lists['filt_course']) ? ('&amp;course_id='.$lists['filt_course']) :'');?>"><?php echo _JLMS_USERS_TCHR_ASSISTS; ?></a></td>
						<td>-</td>
						<td>-</td>
						<td><a href="index.php?option=com_joomla_lms&amp;task=lms_users&amp;view_by=1<?php echo ((isset($lists['filt_course']) && $lists['filt_course']) ? ('&amp;course_id='.$lists['filt_course']) :'');?>"><?php echo $teacher_assistanse;?></a></td>
					</tr>
					<tr class="row1">
						<td align="center">-</td>
						<td align="center">-</td>
						<td><a href="index.php?option=com_joomla_lms&amp;task=view_class_users&amp;class_id=0"><?php echo _JLMS_USERS_USRS_WOUT_GR; ?></a></td>
						<td>-</td>
						<td>-</td>
						<td><a href="index.php?option=com_joomla_lms&amp;task=view_class_users&amp;class_id=0"><?php echo $members_without_group;?></a></td>
					</tr>
					<?php
					$k = 0;
					for ($i=0, $n=count($rows); $i < $n; $i++) {
						$row = $rows[$i];
						$link 	= 'index.php?option=com_joomla_lms&amp;task=view_class_users&amp;class_id='. $row->id . '&amp;course_id=' . $row->course_id;
						$checked = mosHTML::idBox( $i, $row->id);?>
						<tr class="<?php echo "row$k"; ?>">
							<td><?php echo $pageNav->rowNumber( $i ); ?></td>
							<td><?php echo $checked; ?></td>
							<td align="left">
							<a href="<?php echo $link;?>">
								<?php echo $row->ug_name;?>
							</a>
							</td>
							<td align="left">
								<?php echo $row->course_name;?>
							</td>
							<td align="left">
								<span><?php echo mosToolTip( '<b>'._JLMS_USERS_NAME.': </b>'.$row->name . "<br>" . '<b>'._JLMS_USERS_EMAIL.': </b>' . $row->email, _JLMS_USER_INFORMATION, 280, 'tooltip.png', $row->username );?></span>
							</td>
							<td>
								<a href="<?php echo $link;?>">
									<?php echo $row->members;?>
								</a>
							</td>

						</tr>
						<?php
						$k = 1 - $k;
					}?>
					</table>
				</td>
			</tr>
		</table>
	</td></tr></table>
	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	<input type="hidden" name="task" value="classes" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="hidemainmenu" value="0" />	
	</form>
		<?php
		}
	}

	function JLMS_ViewClassUsersList( &$rows, &$lists, &$pageNav, $option ) {
		JHTML::_('behavior.tooltip'); ?>
<script language="javascript" type="text/javascript">
<!--
function submitbutton(pressbutton) {
	var form = document.adminForm;
	if ( pressbutton == 'remove_stu' ) {
		var sel_elem = document.adminForm.course_id;
		if (sel_elem.options[sel_elem.selectedIndex].value == 0 || sel_elem.options[sel_elem.selectedIndex].value == '0') {
			pressbutton = 'list_courses_student';
			form.task.value = pressbutton; form.submit();			
		} else {
			form.task.value = pressbutton; form.submit();
		}
	} else {
		form.task.value = pressbutton; form.submit();
	}
}

<?php if( JLMS_J16version() ) { ?>
Joomla.submitbutton = submitbutton;
<?php } ?>
//-->
</script>
<form action="index.php" method="post" name="adminForm">
<table width="100%" >
	<tr>
		<td valign="top" width="220">
		<div>
			<?php echo joomla_lms_adm_html::JLMS_menu();?>
		</div>
		</td>
		<td valign="top">		
	<?php if (!class_exists('JToolBarHelper')) { ?>
		<table class="adminheading" cellpadding="0" cellspacing="0" border="0">
		<tr>
			<th class="user">
			<?php echo _JOOMLMS_COMP_NAME;?>: <small><?php echo _JLMS_USERS; ?></small>
			</th>
			<td width="right">
				<table class="adminlist">
					<tr class="row1">
						<td nowrap="nowrap" style="padding:2px 10px 2px 10px;"><?php echo _JLMS_USERS_FILTER_CRS; ?>:&nbsp;&nbsp;</td>
						<td><?php echo $lists['jlms_courses'];?></td>
					</tr>
					<?php
					if($lists['jlms_course_id']){
					?>
					<tr class="row1">
						<td nowrap="nowrap" style="padding:2px 10px 2px 10px; "><?php echo _JLMS_USERS_FILTER_GRP; ?>:&nbsp;&nbsp;</td>
						<td><?php echo $lists['jlms_groups'];?></td>
					</tr>
					<?php
					}
					?>
					<tr class="row1">
						<td nowrap="nowrap" style="padding:2px 10px 2px 10px; ">
						<?php echo _JLMS_SEARCH; ?>:&nbsp;&nbsp;
						</td>
						<td>
						<input type="text" name="u_search" value="<?php echo $lists['u_search'];?>" class="text_area" style="width: 263px;" />
						</td>
					</tr>
				</table>
			</td>
		</tr>
		</table>
		<?php } else { ?>	
		<table  align="right" style="width: 30%;" class="adminlist">
			<tr class="row1">
				<td nowrap="nowrap" align="right" width="100%" style="padding:2px 10px 2px 10px;"><?php echo _JLMS_USERS_FILTER_CRS; ?>:&nbsp;&nbsp;</td>
				<td nowrap="nowrap">
					<?php echo $lists['jlms_courses'];?>
				</td>
			</tr>
			<?php
			if($lists['jlms_course_id']){
			?>
			<tr class="row1">
				<td nowrap="nowrap" align="right" width="100%" style="padding:2px 10px 2px 10px;"><?php echo _JLMS_USERS_FILTER_GRP; ?>:&nbsp;&nbsp;</td>
				<td nowrap="nowrap">
					<?php echo $lists['jlms_groups'];?>
				</td>
			</tr>
			<?php
			}
			?>
			<tr class="row1">
				<td nowrap="nowrap" align="right" width="100%" style="padding:2px 10px 2px 10px;"><?php echo _JLMS_SEARCH; ?>:&nbsp;&nbsp;</td>
				<td nowrap="nowrap">
					<input type="text" name="u_search" value="<?php echo $lists['u_search'];?>" class="text_area" style="width: 263px;" />
				</td>
			</tr>
		</table>	
		<div style="clear: both;"><!--x--></div>
		<?php } ?>	
		<table width="100%" border="0">
			<tr>
				<td valign="top">
					<table class="adminlist">
					<thead>
					<tr>
						<th width="20">#</th>
						<th width="20" class="title"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>
						<th class="title"><?php echo _JLMS_USER; ?></th>
						<th class="title"><?php echo _JLMS_USERS_GR_NAME; ?></th>
						<th class="title"><?php echo _JLMS_USERS_CRS_NAME; ?></th>
						<th class="title" colspan="2"><?php echo _JLMS_USERS_ACCESS_PRD; ?></th>
					</tr>
					</thead>
					<tfoot>
						<tr>
							<td colspan="7">
							<?php echo $pageNav->getListFooter(); ?>
							</td>
						</tr>
					</tfoot>
					<tbody>
					<?php
					$k = 0;
					for ($i=0, $n=count($rows); $i < $n; $i++) {
						$row = $rows[$i];
						$checked = mosHTML::idBox( $i, $row->id.'_'.$row->course_id);?>
						<tr class="<?php echo "row$k"; ?>">
							<td><?php echo $pageNav->rowNumber( $i ); ?></td>
							<td><?php echo $checked; ?></td>
							<td>
							<span><?php echo mosToolTip( '<b>'._JLMS_NAME.': </b>'.$row->name . "<br>" . '<b>'._JLMS_EMAIL.': </b>' . $row->email, _JLMS_USER_INFORMATION, 280, 'tooltip.png', $row->username );?></span>
							</td>
							<td align="left">
								<?php echo $row->ug_name;?>
							</td>
							<td align="left">
								<?php echo $row->course_name;?>
							</td>
							<td nowrap align="center"><?php echo ($row->publish_start?$row->start_date:'-');?></td>
							<td nowrap align="center"><?php echo ($row->publish_end?$row->end_date:'-');?></td>
						</tr>
						<?php
						$k = 1 - $k;
					}?>
					</tbody>
					</table>
				</td>
			</tr>
		</table>		
	</td></tr></table>
	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	<input type="hidden" name="task" value="view_class_users" />
	<input type="hidden" name="group_id" value="<?php echo $lists['group_id'];?>" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="hidemainmenu" value="0" />	
	</form>
		<?php
	}
	
	function JLMS_ListCoursesStudent($user_info, $rows, $option, $checkedUsers = array() ){
		JHTML::_('behavior.tooltip');
		
		$oneUser = !empty($user_info);
		
		$app = & JFactory::getApplication();
		if( !$oneUser )
			$app->enqueueMessage( _JLMS_USERS_YOU_ARE_TRYING_REMOVE, 'notice' );
		 			
		?>
		<script language="javascript" type="text/javascript">
		<!--
		function submitbutton(pressbutton) {
			var form = document.adminForm;
			if((pressbutton == 'remove_stu_from_course'  || pressbutton == 'remove_stu') && form.boxchecked.value == 0){
				alert('<?php echo _JLMS_USERS_SELECT_COURSES_TO_REMOVE_USER;?>');
			} else {
				form.task.value = pressbutton;
				form.submit();
			}
		}
		
		<?php if( JLMS_J16version() ) { ?>
		Joomla.submitbutton = submitbutton;
		<?php } ?>
	
		//-->
		</script>
		<form action="index.php" method="post" name="adminForm">		
		<table width="100%" >
			<tr>
				<td valign="top" width="220">
					<div>
						<?php echo joomla_lms_adm_html::JLMS_menu();?>
					</div>
					</td>
					<td valign="top">					
					<?php if (!class_exists('JToolBarHelper')) { ?>
					<table class="adminheading" cellpadding="0" cellspacing="0" border="0">
					<tr>
						<th class="user">
						<?php echo _JOOMLMS_COMP_NAME;?>: <small><?php echo _JLMS_USERS_USRS_IN_CRSS; ?></small>
						</th>
					</tr>
					</table>
					<?php } 
						
						if( $oneUser ) {								
					 ?>					
						<legend><?php echo _JLMS_USER_INFORMATION;?></legend>
						<table border="0" cellpadding="0" cellspacing="0" width="100%">
							<tr>
								<td width="10%">
									<?php echo _JLMS_USERNAME;?>:
								</td>
								<td>
									<b><?php echo $user_info->username;?></b>
								</td>
							</tr>
							<tr>
								<td>
									<?php echo _JLMS_NAME;?>:
								</td>
								<td>
									<b><?php echo $user_info->name;?></b>
								</td>
							</tr>
							<tr>
								<td>
									<?php echo _JLMS_EMAIL;?>:
								</td>
								<td>
									<b><?php echo $user_info->email;?></b>
								</td>
							</tr>
						</table>
					<?php }
					if( !$oneUser ) { 
					?>														
					<legend><?php echo _JLMS_USERS_LIST_OF_STUDENT_COURSES;?></legend>
					<?php } ?>
					<table width="100%" border="0">
						<tr>
							<td valign="top">
								<table class="adminlist">
								<thead>
								<tr>
									<th width="20">#</th>
									<?php 	
										if( $oneUser ) { 
									?>
									<th width="20" class="title"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>
									<?php 
										} 
									?>
									<?php if( !$oneUser ) { ?>														
									<th width="10%">									
									<?php echo _JLMS_USERNAME;?>:
									</th>
									<?php } ?>
									<th class="title"><?php echo _JLMS_USERS_CRS_NAME; ?></th>
									<th class="title"><?php echo _JLMS_ROLE; ?></th>
								</tr>
								</thead>
								<tbody>
								<?php
								$k = 0;
								$isChecked	= false;
								$checkbox	= '';
								$hidden		= '';
								for ($i=0, $n=count($rows); $i < $n; $i++) {
									$row = $rows[$i];									
									$checked = '';
									
									if( $oneUser ) 
									{																		
										if( isset($checkedUsers[$row->user_id]) && in_array($row->id, $checkedUsers[$row->user_id] ) ) 
										{
											$isChecked = true;
											$checked = 'checked="checked"';
										}
										
										$checkbox = '<input type="checkbox" id="cb'.$i.'" name="cid[]" value="'.$row->user_id.'_'.$row->id.'" '.$checked.' onclick="isChecked(this.checked);" title="'.JText::sprintf('JGRID_CHECKBOX_ROW_N', ($i + 1)).'" />';
									} else {
										$isChecked = true;										
										if( !( isset($checkedUsers[$row->user_id]) && in_array( $row->id, $checkedUsers[$row->user_id]) ) ) 
										{
											continue;
										}			
										$hidden = '<input type="hidden" id="cb'.$i.'" name="cid[]" value="'.$row->user_id.'_'.$row->id.'" '.$checked.' />';	
									}
									
									?>									
									<tr class="<?php echo "row$k"; ?>">
										<td><?php echo ( $i + 1 ); ?></td>
										<?php 
											if( $oneUser ) { 
										?>
										<td><?php echo $checkbox; ?></td>	
										<?php 
											} 
											if( !$oneUser ) {
										?>									
										<td><?php echo $row->username; echo $hidden; ?></td>
										<?php 
											}									
										?>										
										<td align="left">
											<?php echo $row->course_name;?>
										</td>
										<td align="left">
											<?php echo $row->lms_usertype;?>
										</td>
									</tr>
									<?php
									$k = 1 - $k;
								}?>
								</tbody>
								</table>
							</td>
						</tr>
					</table>					
				</td>
			</tr>
			</table>
			
			<input type="hidden" name="option" value="<?php echo $option; ?>" />
			<input type="hidden" name="task" value="view_class_users_courses" />
			<input type="hidden" name="boxchecked" value="<?php echo $isChecked; ?>" />
			<input type="hidden" name="hidemainmenu" value="0" />			
			<input type="hidden" name="is_list" value="1" />		
		</form>
		<?php
	}
	
	function JLMS_ViewUsersInCourses( &$rows, &$lists, &$pageNav, $option ) {
		JHTML::_('behavior.tooltip');
		?>
<script language="javascript" type="text/javascript">
<!--
function submitbutton(pressbutton) {
	var form = document.adminForm;

	if(pressbutton == 'remove_stu_from_course'  || pressbutton == 'remove_stu'){
		var sel_elem = document.adminForm.course_id;
		if (sel_elem.options[sel_elem.selectedIndex].value == 0 || sel_elem.options[sel_elem.selectedIndex].value == '0') {
			pressbutton = 'list_courses_student';
			form.task.value = pressbutton;
			form.submit();
		} else {
			form.task.value = pressbutton;
			form.submit();
		}
	} else {
		form.task.value = pressbutton;
		form.submit();
	}
}

<?php if( JLMS_J16version() ) { ?>
Joomla.submitbutton = submitbutton;
<?php } ?>
	
//-->
</script>
<form action="index.php" method="post" name="adminForm">
<table width="100%" >
	<tr>
		<td valign="top" width="220">
		<div>
			<?php echo joomla_lms_adm_html::JLMS_menu();?>
		</div>
		</td>
		<td valign="top">		
	<?php if (!class_exists('JToolBarHelper')) { ?>
		<table class="adminheading" cellpadding="0" cellspacing="0" border="0">
		<tr>
			<th class="user">
			<?php echo _JOOMLMS_COMP_NAME;?>: <small><?php echo _JLMS_USERS_USRS_IN_CRSS; ?></small>
			</th>
			<td width="right">
				<table class="adminlist">
					<tr class="row1">
						<td nowrap="nowrap" style="padding:2px 10px 2px 10px;">
						<?php echo _JLMS_USERS_FILTER_CRS; ?>:&nbsp;&nbsp;
						</td>
						<td>
							<?php echo $lists['jlms_courses'];?>
						</td>
					</tr>
					<tr class="row1">
						<td nowrap="nowrap" style="padding:2px 10px 2px 10px;">
						<?php echo _JLMS_USERS_FILTER_GRP; ?>:&nbsp;&nbsp;
						</td>
						<td>
							<?php echo $lists['jlms_groups'];?>
						</td>
					</tr>
					<tr class="row1">
						<td nowrap="nowrap" style="padding:2px 10px 2px 10px;">
						<?php echo _JLMS_SEARCH;  ?>:&nbsp;&nbsp;
						</td>
						<td>
							<input type="text" name="u_search" value="<?php echo $lists['u_search'];?>" class="text_area" style="width: 263px;" />
						</td>
					</tr>
				</table>
			</td>
		</tr>
		</table>
		<?php } else { ?>		
			<table  align="right" style="width: 30%;" class="adminlist">
				<tr class="row1">
					<td nowrap="nowrap" align="left" width="100%" style="padding:2px 10px 2px 10px;">
					<?php echo _JLMS_USERS_FILTER_CRS; ?>:&nbsp;&nbsp;
					</td>
					<td nowrap="nowrap">
						<?php echo $lists['jlms_courses'];?>
					</td>
				</tr>
				<tr class="row1">
					<td nowrap="nowrap" style="padding:2px 10px 2px 10px;">
					<?php echo _JLMS_USERS_FILTER_GRP; ?>:&nbsp;&nbsp;
					</td>
					<td>
						<?php echo $lists['jlms_groups'];?>
					</td>
				</tr>
				<tr class="row1">
					<td nowrap="nowrap" align="left" width="100%" style="padding:2px 10px 2px 10px;">
					<?php echo _JLMS_SEARCH;  ?>:&nbsp;&nbsp;
					</td>
					<td>
						<input type="text" name="u_search" value="<?php echo $lists['u_search'];?>" class="text_area" style="width: 263px;" />
					</td>
				</tr>
			</table>		
		<div style="clear: both;"><!--x--></div>
		<?php } ?>		
		<table width="100%" border="0">
			<tr>
				<td valign="top">
					<table class="adminlist">
					<thead>
					<tr>
						<th width="20">#</th>
						<th width="20" class="title"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>
						<th class="title"><?php echo _JLMS_USER; ?></th>
						<th class="title"><?php echo _JLMS_USERS_CRS_NAME; ?></th>
						<th class="title"><?php echo _JLMS_ROLE; ?></th>
						<th class="title" colspan="2"><?php echo _JLMS_USERS_ACCESS_PRD; ?></th>
					</tr>
					</thead>
					<tfoot>
						<tr>
							<td colspan="7">
							<?php echo $pageNav->getListFooter(); ?>
							</td>
						</tr>
					</tfoot>
					<tbody>
					<?php
					$k = 0;
					for ($i=0, $n=count($rows); $i < $n; $i++) {
						$row = $rows[$i];
						$checked = mosHTML::idBox( $i, $row->id.'_'.$row->course_id);?>
						<tr class="<?php echo "row$k"; ?>">
							<td><?php echo $pageNav->rowNumber( $i ); ?></td>
							<td><?php echo $checked; ?></td>
							<td>
							<span><?php echo mosToolTip( '<b>'._JLMS_NAME.': </b>'.$row->name . "<br>" . '<b>'._JLMS_EMAIL.': </b>' . $row->email, _JLMS_USER_INFORMATION, 280, 'tooltip.png', $row->username );?></span>
							</td>
							<td align="left">
								<?php echo $row->course_name;?>
							</td>
							<td align="left">
								<?php echo $row->lms_usertype;?>
							</td>
							<td nowrap align="center"><?php echo ($row->publish_start?$row->start_date:'-');?></td>
							<td nowrap align="center"><?php echo ($row->publish_end?$row->end_date:'-');?></td>
						</tr>
						<?php
						$k = 1 - $k;
					}?>
					</tbody>
					</table>
				</td>
			</tr>
		</table>	
	</td></tr></table>
	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	<input type="hidden" name="task" value="view_class_users_courses" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="hidemainmenu" value="0" />	
	</form>
		<?php
	}
	function JLMS_ViewUsersInGroups( &$rows, &$lists, &$pageNav, $option, $group_id ) {
		JHTML::_('behavior.tooltip');
?>
<form action="index.php" method="post" name="adminForm">
<table width="100%" >
	<tr>
		<td valign="top" width="220">
		<div>
			<?php echo joomla_lms_adm_html::JLMS_menu();?>
		</div>
		</td>
		<td valign="top">		
	<?php if (!class_exists('JToolBarHelper')) { ?>
		<table class="adminheading" cellpadding="0" cellspacing="0" border="0">
		<tr>
			<th class="user">
			<?php echo _JOOMLMS_COMP_NAME;?>: <small><?php echo _JLMS_USERS_GRP_MEMBERS; ?></small>
			</th>
			<td width="right">
				<table >
				<tr class="row1">
					<td nowrap="nowrap" style="padding:2px 10px 2px 10px;">
					<?php echo _JLMS_FILTER; ?>:&nbsp;&nbsp;
					</td>
					<td><?php echo $lists['jlms_groups'];?></td>
				</tr>
				
				<?php if($group_id) {?>
				<tr class="row1">
					<td nowrap="nowrap" style="padding:2px 10px 2px 10px;">
					<?php echo _JLMS_USERS_FILTER_SUBGR; ?>:&nbsp;&nbsp;
					</td>
					<td><?php echo $lists['jlms_subgroups'];?></td>
				</tr>
				<?php }?>

				<tr class="row1">
					<td nowrap="nowrap" style="padding:2px 10px 2px 10px;">
					<?php echo _JLMS_SEARCH; ?>:&nbsp;&nbsp;
					</td>
					<td>
					<input type="text" name="u_search" value="<?php echo $lists['u_search'];?>" class="text_area" style="width: 263px;" />
					</td>
				</tr>
				</table>
			</td>
		</tr>
		</table>
		<?php } else { ?>
		<div style="width: 100%;">
		<table  cellpadding="0" cellspacing="0" border="0" align="right" style="width: 30%;" class="adminlist">
			<tr class="row1">
				<td nowrap="nowrap" align="right" style="padding:2px 10px 2px 10px; ">
				<?php echo _JLMS_FILTER; ?>:&nbsp;&nbsp;
				</td>
				<td><?php echo $lists['jlms_groups'];?></td>
			</tr>
			<tr class="row1">
				<td nowrap="nowrap" align="right" style="padding:2px 10px 2px 10px; ">
				<?php echo _JLMS_SEARCH; ?>:&nbsp;&nbsp;
				</td>
				<td>
				<input type="text" name="u_search" value="<?php echo $lists['u_search'];?>" class="text_area" style="width: 263px" />
				</td>
			</tr>
		</table>
		</div>
		<div style="clear: both;"><!--x--></div>
		<?php } ?>		
		<table width="100%" border="0">
			<tr>
				<td valign="top">
					<table class="adminlist">
					<thead>
					<tr>
						<th width="20">#</th>
						<th width="20" class="title"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>
						<th class="title" width="25%"><?php echo _JLMS_USER; ?></th>
						<th class="title"><?php echo _JLMS_USERS_GR_NAME; ?></th>
					</tr>
					</thead>
					<tfoot>
						<tr>
							<td colspan="7">
							<?php echo $pageNav->getListFooter(); ?>
							</td>
						</tr>
					</tfoot>
					<tbody>
					<?php
					$k = 0;
					for ($i=0, $n=count($rows); $i < $n; $i++) {
						$row = $rows[$i];
						$checked = mosHTML::idBox( $i, $row->id);?>
						<tr class="<?php echo "row$k"; ?>">
							<td><?php echo $pageNav->rowNumber( $i ); ?></td>
							<td><?php echo $checked; ?></td>
							<td>
							<span><?php echo mosToolTip( '<b>'._JLMS_NAME.': </b>'.$row->name . "<br>" . '<b>'._JLMS_EMAIL.': </b>' . $row->email, _JLMS_USER_INFORMATION, 280, 'tooltip.png', $row->username );?></span>
							</td>
							<td align="left">
								<?php echo $row->group_name;?><?php if(isset($row->subgroup)) echo ' -> '.$row->subgroup;?>
							</td>
						</tr>
						<?php
						$k = 1 - $k;
					}?>
					</tbody>
					</table>
				</td>
			</tr>
		</table>
	</td></tr></table>
	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	<input type="hidden" name="task" value="view_class_users_groups" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="hidemainmenu" value="0" />	
	</form>
		<?php
	}

	function idBox2( $rowNum, $recId, $course_id = 0, $checked=false, $name='cid' ) {
		return '<input type="radio" id="cb'.$rowNum.'" name="'.$name.'[]" value="'.$recId.'" onclick="this.form.assistant_course.value = \''.$course_id.'\'; isChecked(this.checked);"  '.($checked?'checked':'').' />';
	}

	function JLMS_ViewClassAssistants( &$rows, &$lists, &$pageNav, $option ) {
		JHTML::_('behavior.tooltip');
		?>
<form action="index.php" method="post" name="adminForm">
<table width="100%" >
	<tr>
		<td valign="top" width="220">
		<div>
			<?php echo joomla_lms_adm_html::JLMS_menu();?>
		</div>
		</td>
		<td valign="top">	
	<?php if (!class_exists('JToolBarHelper')) { ?>
		<table class="adminheading" cellpadding="0" cellspacing="0" border="0">
		<tr>
			<th class="user">
			<?php echo _JOOMLMS_COMP_NAME;?>: <small><?php echo _JLMS_ASSISTANS; ?></small>
			</th>
			<td width="right">
				<table class="adminlist"><tr class="row1">
					<td nowrap="nowrap" style="padding:2px 10px 2px 10px;"><?php echo _JLMS_FILTER; ?>:&nbsp;&nbsp;<?php echo $lists['jlms_courses'];?></td>
				</tr></table>
			</td>
		</tr>
		</table>
		<?php } else { ?>
		<div style="width: 100%">
			<table cellpadding="0" cellspacing="0" border="0" class="adminlist">
			<tr class="row1">
				<td align="left" width="100%">&nbsp;</td>
				<td nowrap="nowrap">
					<?php echo $lists['jlms_courses'];?>
				</td>
			</tr>
			</table>
		</div>
		<div style="clear: both;"><!--x--></div>
		<?php } ?>	
		<table width="100%" border="0">
			<tr>
				<td valign="top">
					<table class="adminlist">
					<thead>
						<tr>
							<th width="20" align="center">#</th>
							<th width="20" class="title" align="center">-</th>
							<th class="title"><?php echo _JLMS_ASSISTAN; ?></th>
							<th class="title"><?php echo _JLMS_USERS_CRS_NAME; ?></th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<td colspan="4">
							<?php echo $pageNav->getListFooter(); ?>
							</td>
						</tr>
					</tfoot>
					<tbody>
					<?php
					$k = 0;
					for ($i=0, $n=count($rows); $i < $n; $i++) {
						$row = $rows[$i];
						$checked = joomla_lms_adm_html::idBox2( $i, $row->id, $row->course_id);?>
						<tr class="<?php echo "row$k"; ?>">
							<td align="center"><?php echo $pageNav->rowNumber( $i ); ?></td>
							<td align="center"><?php echo $checked; ?></td>
							<td>
							<span><?php echo mosToolTip( '<b>'._JLMS_NAME.': </b>'.$row->name . "<br>" . '<b>'._JLMS_EMAIL.': </b>' . $row->email, _JLMS_USER_INFORMATION, 280, 'tooltip.png', $row->username );?></span>
							</td>
							<td align="left">
								<?php echo $row->course_name;?>
							</td>
						</tr>
						<?php
						$k = 1 - $k;
					}?>
					</tbody>
					</table>
				</td>
			</tr>
		</table>	
	</td></tr></table>
	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	<input type="hidden" name="task" value="view_assistants" />
	<input type="hidden" name="assistant_course" value="0" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="hidemainmenu" value="0" />	
	</form>
		<?php

	}
	function addUserToGlobalGroup ($lists, $option, $row, $rows_groups) {
		JHTML::_('behavior.tooltip');
	?>
<script language="javascript" type="text/javascript">
<!--
function jlms_changeUserSelect(c_e) {
	var sel_value = c_e.options[c_e.selectedIndex].value;
	var sel1 = c_e.form.user_name;
	for (var i = 0; i < sel1.options.length; i++) {
		if (sel1.options[i].value == sel_value) {
			sel1.options[i].selected = true;
		}
	}
	var sel2 = c_e.form.user_email;
	for (var i = 0; i < sel2.options.length; i++) {
		if (sel2.options[i].value == sel_value) {
			sel2.options[i].selected = true;
		}
	}
	var sel3 = c_e.form.user_id;
	for (var i = 0; i < sel3.options.length; i++) {
		if (sel3.options[i].value == sel_value) {
			sel3.options[i].selected = true;
		}
	}
}
function submitbutton(pressbutton) {
	var form = document.adminForm;
	if ((pressbutton == 'save_user_in_group') && (form.user_id.value=='' || form.user_id.value=='0' || form.group_id.value=='' || form.group_id.value=='0')) {
		alert( '<?php echo _JLMS_USERS_MSG_SLCT_USR_A_GR; ?>' );
	} else {
		form.task.value = pressbutton;
		form.submit();
	}
}

<?php if( JLMS_J16version() ) { ?>
Joomla.submitbutton = submitbutton;
<?php } ?>
//-->
</script>

<form action="index.php" method="post" name="adminForm">
<table width="100%" >
	<tr>
		<td valign="top" width="220">
		<div>
			<?php echo joomla_lms_adm_html::JLMS_menu();?>
		</div>
		</td>
		<td valign="top">	
		<div class="width-100">
		<fieldset class="adminform">
		<?php if (!class_exists('JToolBarHelper')) {?>
		<table class="adminheading">
		<tr>
			<th class="user">
			<?php echo _JOOMLMS_COMP_NAME;?>:
			<small>
			<?php echo _JLMS_USERS_GRP_MEMBERS; ?>
			</small>
			</th>
		</tr>
		</table>
		<?php } ?>		
		<table width="100%" border="0">
			<tr>
				<td valign="top">
					<table width="100%" >
						<tr>
							<th colspan="2"><?php echo _JLMS_DETAILS; ?></th>
						</tr>
						<?php if($row->user_id) {?>
							<tr>
								<td align="right" width="20%"><?php echo _JLMS_USER; ?>:</td>
								<td><?php echo $row->username.' , '.$row->name.'('.$row->email.')';?></td>
							</tr>
						<?php }?>	
						<tr>
							<td align="right" width="20%"><?php echo _JLMS_SELECT_GROUP; ?>:</td>
							<td><?php echo $lists['jlms_groups'];?></td>
						</tr>
						
						<?php if($row->group_id) {?>
						<tr>
							<td align="right" width="20%"><?php echo _JLMS_USERS_SLCT_SUBGROUP; ?>:</td>
							<td><?php echo $lists['jlms_subgroups'];?></td>
						</tr>
						<?php }?>
						
						<?php if(!$row->user_id) {?>
							<tr>
								<td width="15%" valign="middle" style="vertical-align:middle ">
									<?php echo _JLMS_USERS_SLCT_USRNAME; ?>:
								</td>
								<td width="100%">
								<?php echo $lists['users']; ?>
								</td>
							</tr>
							<tr>
								<td width="15%" valign="middle" style="vertical-align:middle ">
									<?php echo _JLMS_USERS_OR_NAME; ?>:
								</td>
								<td>
								<?php echo $lists['users_names']; ?>
								</td>
							</tr>
							<tr>
								<td valign="middle" style="vertical-align:middle ">
									<?php echo _JLMS_USERS_OR_EMAIL; ?>:
								</td>
								<td>
								<?php echo $lists['users_emails']; ?>
								</td>
							</tr>
						<?php }?>
					</table>
				</td>
			</tr>
			<?php if(count($rows_groups)) {?>	
			<tr>
				<td valign="top">
					<table class="adminlist">
						<thead>
							<tr>
								<th class="title" width="100%"><?php echo _JLMS_USERS_USR_MNGED_FOLOW_GRPS; ?></th>
							</tr>
						</thead>
						<?php
						$k = 0;
						for ($i=0, $n=count($rows_groups); $i < $n; $i++) {
							$row1 = $rows_groups[$i];?>
							<tr class="<?php echo "row$k"; ?>">
								<td align="left">
									<?php echo $row1->ug_name;?><?php if(isset($row1->subgroup)) echo ' -> '.$row1->subgroup;?>
								</td>
							</tr>
							<?php
							$k = 1 - $k;
						}?>	
					</table>	
				</td>
			</tr>
			<?php }?>
		</table>
		</fieldset>
		</div>	
	</td></tr></table>
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="task" value="add_user_to_group_save" />
		<input type="hidden" name="cid[]" value="<?php echo $row->id;?>" />
		<?php if($row->user_id) {?>	
			<input type="hidden" name="user_id" value="<?php echo $row->user_id;?>" />
			<input type="hidden" name="id" value="<?php echo $row->id;?>" />
			<input type="hidden" name="edit_user" value="1" />
		<?php }?>		
	</form>
<?php
	}

	function addCourseUser( &$lists, $option, $utype ) {
		global $JLMS_CONFIG;

		JHTML::_('behavior.tooltip');
		mosCommonHTML::loadCalendar();

		if ($JLMS_CONFIG->get('use_global_groups', 1)) { ?>
<script language="javascript" type="text/javascript">
function jlms_changeUserSelect(c_e) {
	var sel_value = c_e.options[c_e.selectedIndex].value;
	var sel1 = c_e.form.user_name;
	for (var i = 0; i < sel1.options.length; i++) {
		if (sel1.options[i].value == sel_value) {
			sel1.options[i].selected = true;
		}
	}
	var sel2 = c_e.form.user_email;
	for (var i = 0; i < sel2.options.length; i++) {
		if (sel2.options[i].value == sel_value) {
			sel2.options[i].selected = true;
		}
	}
	var sel3 = c_e.form.user_id;
	for (var i = 0; i < sel3.options.length; i++) {
		if (sel3.options[i].value == sel_value) {
			sel3.options[i].selected = true;
		}
	}
}
function submitbutton(pressbutton) {
	var form = document.adminForm;
	if ((pressbutton == 'add_user_save') && (((form.user_id.value == '') || (form.user_id.value == '0') || (form.course_id.value=='') || (form.course_id.value=='0')))) {
		alert( '<?php echo _JLMS_USERS_MSG_SLCT_USR_A_GR;  ?>' );
	} else {
		form.task.value = pressbutton;
		form.submit();
	}
}

<?php if( JLMS_J16version() ) { ?>
Joomla.submitbutton = submitbutton;
<?php } ?>
</script>

<form action="index.php" method="post" name="adminForm">
<table width="100%" >
<tr>
	<td valign="top" width="220">		
		<?php echo joomla_lms_adm_html::JLMS_menu();?>		
	</td>
	<td valign="top">
		<div class="width-100">
		<fieldset class="adminform">
		<table width="100%" border="0">
			<tr>
				<td valign="top">
					<table width="100%" >
						<tr>
							<th colspan="2"><?php echo _JLMS_DETAILS; ?></th>
						</tr>
						<tr>
							<td align="right" width="20%"><?php echo _JLMS_SELECT_COURSE; ?>:</td>
							<td><?php echo $lists['jlms_courses'];?></td>
						</tr>
						<?php if ($utype == 1) { ?>						
						<tr>							
							<td align="right" width="20%">
								<input type="hidden" name="group_id" value="0" />
								<?php echo _JLMS_START_DATE; ?>:
							</td>
							<td>
							<fieldset class="radio">
							<?php 
								echo $lists['publish_start'];
								echo JHTML::_('calendar', date('Y-m-d'), 'start_date', 'start_date', '%Y-%m-%d', array('class' => 'text_area'));				
							?>						
							</fieldset>
							</td>
						</tr>
						<tr>
							<td align="right" width="20%"><?php echo _JLMS_END_DATE; ?>:</td>
							<td>
								<fieldset class="radio">
								<?php 
									echo $lists['publish_end'];								
									echo JHTML::_('calendar', date('Y-m-d'), 'end_date', 'end_date', '%Y-%m-%d', array('class' => 'text_area'));
								?>
							</fieldset>
							</td>
						</tr>
						<?php } ?>
						<tr>
							<td width="15%" align="middle" style="vertical-align:middle">
								<?php echo _JLMS_ROLE; ?>:
							</td>
							<td width="100%">
							<?php echo $lists['role']; ?>
							</td>
						</tr>
						<tr>
							<td width="15%" valign="middle" style="vertical-align:middle ">
								<?php echo _JLMS_SELECT_USERNAME; ?>:
							</td>
							<td width="100%">
							<?php echo $lists['users']; ?>
							</td>
						</tr>
						<tr>
							<td width="15%" valign="middle" style="vertical-align:middle ">
								<?php echo _JLMS_USERS_OR_NAME; ?>:
							</td>
							<td>
							<?php echo $lists['users_names']; ?>
							</td>
						</tr>
						<tr>
							<td valign="middle" style="vertical-align:middle ">
								<?php echo _JLMS_USERS_OR_EMAIL ?>:
							</td>
							<td>
							<?php echo $lists['users_emails']; ?>
							</td>
						</tr>
					</table>
					<br />
				</td>
			</tr>
		</table>
		</fieldset>
		</div>
	</td>
</tr>
</table>
</div>
<input type="hidden" name="option" value="<?php echo $option;?>" />
<input type="hidden" name="task" value="add_user_save" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="utype" value="<?php echo $utype;?>" />
</form>
<?php
		}
		else {
	?>
<script language="javascript" type="text/javascript">
<!--
function jlms_changeUserSelect(c_e) {
	var sel_value = c_e.options[c_e.selectedIndex].value;
	var sel1 = c_e.form.user_name;
	for (var i = 0; i < sel1.options.length; i++) {
		if (sel1.options[i].value == sel_value) {
			sel1.options[i].selected = true;
		}
	}
	var sel2 = c_e.form.user_email;
	for (var i = 0; i < sel2.options.length; i++) {
		if (sel2.options[i].value == sel_value) {
			sel2.options[i].selected = true;
		}
	}
	var sel3 = c_e.form.user_id;
	for (var i = 0; i < sel3.options.length; i++) {
		if (sel3.options[i].value == sel_value) {
			sel3.options[i].selected = true;
		}
	}
}
function submitbutton(pressbutton) {
	var form = document.adminForm;
	if ((pressbutton == 'add_user_save') && (((form.user_id.value == '') || (form.user_id.value == '0')) || ((form.course_id.value == '') || (form.course_id.value == '0')) <?php if ($utype == 1) { echo "|| ((form.class_id.value == '-1') || (form.class_id.value == -1))"; }?> )) {
		alert( "<?php echo ($utype == 2) ? _JLMS_USERS_MSG_SLCT_USR_A_CRS : _JLMS_USERS_MSG_SLCT_USR_CRS_A_GR; ?>" );
	} else {
		form.task.value = pressbutton;
		form.submit();
	}
}

<?php if( JLMS_J16version() ) { ?>
Joomla.submitbutton = submitbutton;
<?php } ?>

//-->
</script>


<form action="index.php" method="post" name="adminForm">
<table width="100%" >
	<tr>
		<td valign="top" width="220">
		<div>
			<?php echo joomla_lms_adm_html::JLMS_menu();?>
		</div>
		</td>
		<td valign="top">
		<div class="width-100">
		<fieldset class="adminform">
		<?php if (!class_exists('JToolBarHelper')) {?>
		<table class="adminheading">
		<tr>
			<th class="user">
			<?php echo _JOOMLMS_COMP_NAME;?>:
			<small>
			<?php echo ($utype == 2) ? _JLMS_USERS_NEW_ASSISTANT : _JLMS_USERS_NEW_STUDENT;?>
			</small>
			</th>
		</tr>
		</table>
		<?php } ?>		
		<table width="100%" border="0">
			<tr>
				<td valign="top">
					<table width="100%" >
						<tr>
							<th colspan="2"><?php echo _JLMS_DETAILS; ?></th>
						<tr>
						<tr>
							<td align="right" width="20%"><?php echo _JLMS_SELECT_COURSE; ?>:</td>
							<td><?php echo $lists['jlms_courses'];?></td>
						</tr>
						<?php if ($utype == 1) { ?>
						<tr>
							<td align="right" width="20%"><?php echo _JLMS_SELECT_GROUP; ?>:</td>
							<td><?php echo $lists['jlms_groups'];?></td>
						</tr>
						<tr>
							<td align="right" width="20%"><?php echo _JLMS_START_DATE; ?>:</td>
							<td>
							<fieldset class="radio">
							<?php 
								echo $lists['publish_start'];
								echo JHTML::_('calendar', date('Y-m-d'), 'start_date', 'start_date', '%Y-%m-%d', array('class' => 'text_area'));					 
							?>
							</fieldset>
							</td>
						</tr>
						<tr>
							<td align="right" width="20%"><?php echo _JLMS_END_DATE; ?>:</td>
							<td>
								<fieldset class="radio">
								<?php 
									echo $lists['publish_end'];
									echo JHTML::_('calendar', date('Y-m-d'), 'end_date', 'end_date', '%Y-%m-%d', array('class' => 'text_area'));			
								?>
							</fieldset>
							</td>
						</tr>
						<?php } ?>
						<tr>
							<td width="15%" valign="middle" style="vertical-align:middle ">
								<?php echo _JLMS_SELECT_USERNAME; ?>:
							</td>
							<td width="100%">
							<?php echo $lists['users']; ?>
							</td>
						</tr>
						<tr>
							<td width="15%" valign="middle" style="vertical-align:middle ">
								<?php echo _JLMS_USERS_OR_NAME; ?>:
							</td>
							<td>
							<?php echo $lists['users_names']; ?>
							</td>
						</tr>
						<tr>
							<td valign="middle" style="vertical-align:middle ">
								<?php echo _JLMS_USERS_OR_EMAIL; ?>:
							</td>
							<td>
							<?php echo $lists['users_emails']; ?>
							</td>
						</tr>
					</table>
					<br />
				</td>
			</tr>
		</table>
		</fieldset>
		</div>
	</td></tr></table>
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="task" value="add_user_save" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="utype" value="<?php echo $utype;?>" />	
	</form>
<?php
		}
	}

	function editCourseUser( &$row, &$lists, $option ) {
		JHTML::_('behavior.tooltip');
		mosCommonHTML::loadCalendar();		
		
		$app = & JFactory::getApplication();	
	?>
<script language="javascript" type="text/javascript">
<!--
function submitbutton(pressbutton) {
	var form = document.adminForm;
	if ( (pressbutton == 'edit_user_save') && ((form.class_id.value == '-1') || (form.class_id.value == -1)) ) {
		alert( '<?php echo _JLMS_USERS_MSG_SLCT_GR; ?>' );
	} else if ( !isDateValid( form.enrol_time.value ) ) {
		alert( '<?php echo _JLMS_USERS_MSG_I_VLD_ENROLL_T; ?>' );
	} else {
		form.task.value = pressbutton;
		form.submit();
	}
}

<?php if( JLMS_J16version() ) { ?>
Joomla.submitbutton = submitbutton;
<?php } ?>

function isDateValid( dt )
{
    return /^\d{4}-\d{2}-\d{2} \d{2}:\d{2}$/.test(dt);          
}
//-->
</script>


<form action="index.php" method="post" name="adminForm">
<table width="100%" >
	<tr>
		<td valign="top" width="220">
		<div>
			<?php echo joomla_lms_adm_html::JLMS_menu();?>
		</div>
		</td>
		<td valign="top">
		<div class="width-100">
		<fieldset class="adminform">
		<?php if (!class_exists('JToolBarHelper')) {?>
		<table class="adminheading">
		<tr>
			<th class="user">
			<?php echo _JOOMLMS_COMP_NAME;?>:
			<small>
			<?php echo _JLMS_USERS_EDIT_STUDENT; ?>
			</small>
			</th>
		</tr>
		</table>
		<?php } ?>	
		<table width="100%" border="0">
			<tr>
				<td valign="top">
					<table width="100%" >
						<tr>
							<th colspan="2"><?php echo _JLMS_DETAILS; ?></th>
						<tr>
						<tr>
							<td align="right" width="20%"><?php echo _JLMS_USER; ?>:</td>
							<td><?php echo $lists['user_info'];?></td>
						</tr>
						<tr>
							<td align="right" width="20%"><?php echo _JLMS_COURSE; ?>:</td>
							<td><?php echo $lists['course_info'];?></td>
						</tr>
						<tr>
							<td align="right" width="20%"><?php echo _JLMS_ROLE; ?>:</td>
							<td><?php echo $lists['role'];?></td>
						</tr>
<?php
						global $JLMS_CONFIG;
						if ($JLMS_CONFIG->get('use_global_groups', 1)) {
							echo '<tr style="display:none">';
						} else {
							echo '<tr>';
						}
?>
							<td align="right" width="20%"><?php echo _JLMS_SELECT_GROUP; ?>:</td>
							<td><?php echo $lists['jlms_groups'];?></td>
						</tr>
						<tr>
							<td align="right" width="20%"><?php echo _JLMS_ENROL_TIME; ?></a>:</td>
							<td>							
							<?php 
								echo JHTML::_('calendar', JLMS_replDbNullDate($row->enrol_time), 'enrol_time', 'enrol_time', '%Y-%m-%d %H:%M', array('class'=>'inputbox', 'size'=>'16',  'maxlength'=>'16'));																
							?>
							</td>
						</tr>
						<tr>
							<td align="right" width="20%"><?php echo _JLMS_START_DATE; ?>:</td>
							<td>
							<fieldset class="radio">
							<?php 
									echo $lists['publish_start'];
									echo JHTML::_('calendar', JLMS_replDbNullDate($row->start_date), 'start_date', 'start_date', '%Y-%m-%d', array('class'=>'inputbox', 'size'=>'10',  'maxlength'=>'10'));						
							?>
							</fieldset>
							</td>
						</tr>
						<tr>
							<td align="right" width="20%"><?php echo _JLMS_END_DATE; ?>:</td>
							<td>
							<fieldset class="radio">
							<?php 
									echo $lists['publish_end'];									
									echo JHTML::_('calendar', JLMS_replDbNullDate( $row->end_date ), 'end_date', 'end_date', '%Y-%m-%d', array('class'=>'inputbox', 'size'=>'10',  'maxlength'=>'10'));
									?>
							</fieldset>
							</td>
						</tr>						
						<?php if ($row->spec_reg && isset($row->spec_answers) && count($row->spec_answers)) { ?>
						<tr><td colspan='2'><hr /></td></tr>
						<tr><td colspan='2' valign='left' style='text-align:left'><?php echo _JLMS_USERS_CRS_REG_QUESTS; ?>:<input type="hidden" name="spec_reg" value="1" /></td></tr>
							<?php foreach ($row->spec_answers as $one_spec_answer) { ?>
								<tr><td><?php echo $one_spec_answer->course_question;?></td>
									<td>
									<input type="hidden" name="spec_reg_quest_id[]" value="<?php echo $one_spec_answer->id;?>" />
									<textarea class="text_area" name="user_answer[]" cols="50" rows="3"><?php echo $one_spec_answer->user_answer;?></textarea>
									</td>
								</tr>
							<?php } ?>
						<?php } ?>
					</table>
					<br />
				</td>
			</tr>
		</table>
		</fieldset>
		</div>
	</td></tr></table>
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="task" value="edit_user_save" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="user_id" value="<?php echo $row->user_id;?>" />
		<input type="hidden" name="course_id" value="<?php echo $row->course_id;?>" />	
	</form>
<?php

	}

	function JLMS_editClassHtml( &$row, &$lists, $option ) {
		JHTML::_('behavior.tooltip');
		mosCommonHTML::loadCalendar();
		?>
		<script language="javascript" type="text/javascript">
		<!--
		function setgood() {
			return true;
		}
		function submitbutton(pressbutton) {
			var form = document.adminForm;
			try {
				form.onsubmit();
			} catch(e) {
				//alert(e);
			}
			if (pressbutton == 'cancel_class') {
				submitform( pressbutton );
				return;
			}
			if (form.ug_name.value == '') {
				alert( '<?php echo _JLMS_USERS_ENTR_GR_NAME; ?>' );
			} else {
				submitform( pressbutton );
			}
		}
		
		<?php if( JLMS_J16version() ) { ?>
		Joomla.submitbutton = submitbutton;
		<?php } ?>
	
		function hide_fields() {
			if(getObj('parent_id') && getObj('parent_id').value > 0) {
				if(getObj('start_date')) {			
					getObj('start_date').style.visibility = 'hidden';
					//getObj('start_date').style.display = 'none';
				}
				if(getObj('end_date')) {			
					getObj('end_date').style.visibility = 'hidden';
					//getObj('end_date').style.display = 'none';
				}
				getObj('forum').style.visibility = 'hidden';
				//getObj('forum').style.display = 'none';

				getObj('chat').style.visibility = 'hidden';
				//getObj('chat').style.display = 'none';
				
			}
			else {
				if(getObj('start_date')) {	
					getObj('start_date').style.visibility = 'visible';
					//getObj('start_date').style.display = 'block';
				}

				if(getObj('end_date')) {	
					getObj('end_date').style.visibility = 'visible';
					//getObj('end_date').style.display = 'block';
				}
				
				getObj('forum').style.visibility = 'visible';
				//getObj('forum').style.display = 'block';
				
				getObj('chat').style.visibility = 'visible';
				//getObj('chat').style.display = 'block';
			}
		}
		function getObj(name) {
			if (document.getElementById) { return document.getElementById(name); }
			else if (document.all) { return document.all[name]; }
			else if (document.layers) { return document.layers[name]; }
			else return false;
		}
		//-->
		</script>
<form action="index.php" method="post" name="adminForm" onsubmit="setgood();">
<table width="100%" >
	<tr>
		<td valign="top" width="220">
		<div>
			<?php echo joomla_lms_adm_html::JLMS_menu();?>
		</div>
		</td>
		<td valign="top">
		<div class="width-100">
		<fieldset class="adminform">
		<?php if (!class_exists('JToolBarHelper')) { ?>
		<table class="adminheading">
		<tr>
			<th class="user">
			<?php echo _JOOMLMS_COMP_NAME;?>:
			<small>
			<?php echo $row->id ? _JLMS_USERS_EDIT_GR : _JLMS_USERS_NEW_GR;?>
			</small>
			</th>
		</tr>
		</table>
		<?php } ?>

<?php
$add_form_hidden_tag = '';
?>	
		<table width="100%" border="0">
			<tr>
				<td valign="top">
					<table width="100%" >
						<tr>
							<th colspan="2"><?php echo _JLMS_USERS_GR_DETAILS; ?></th>
						</tr>
						<?php
						global $JLMS_CONFIG;
						if (!$JLMS_CONFIG->get('use_global_groups', 1)) {
							if (!$row->id) { ?>
						<tr>
							<td align="right" width="20%"><?php echo _JLMS_SELECT_COURSE; ?>:</td>
							<td><?php echo $lists['jlms_courses'];?></td>
						</tr>
						<?php } else { $add_form_hidden_tag =  '<input type="hidden" name="course_id" value="'.$row->course_id.'" />'; }?>
						<?php } else { $add_form_hidden_tag =  '<input type="hidden" name="course_id" value="0" />'; }?>
						<tr>
							<td align="right" width="20%"><?php echo _JLMS_USERS_GR_NAME; ?>:</td>
							<td><input class="text_area" type="text" name="ug_name" size="50" maxlength="250" value="<?php echo str_replace('"', '&quot;',$row->ug_name); ?>" /></td>
						</tr>
						
						<?php if ($JLMS_CONFIG->get('use_global_groups', 1) && isset($lists['ug_names'])) {?>
							<tr>
								<td align="right" width="20%"><?php echo _JLMS_USERS_PARENT_GR; ?>:</td>
								<td><?php echo $lists['ug_names'];?></td>
							</tr>
						<?php }?>
						
						<?php
						if($row->course_id == 0 && $JLMS_CONFIG->get('flms_integration', 0)){
						?>
						<tr id="start_date">
							<td valign="middle"><?php echo _JLMS_START_DATE; ?>:</td>
							<td valign="middle" style="vertical-align:middle">
								<?php if (!$row->start_date) { $row->start_date = date('Y-m-d');} ?>
								<?php if (class_exists('JHTML')) {
									$joomla_generated_code = JHTML::_('calendar', $row->start_date, 'start_date', 'start_date', '%Y-%m-%d', array('class' => 'text_area'));
									//ignore joomla generated code ;)
									echo '<input type="text" name="start_date" id="start_date" value="'.htmlspecialchars($row->start_date, ENT_COMPAT, 'UTF-8').'" size="10" maxlength="10" style="text-align:center; background:transparent; border:0px solid; font-size:12px; font-weight:bold; line-height:14px; " />&nbsp;'.
										 '<img class="calendar" src="'.JURI::root().'templates/system/images/calendar.png" alt="calendar" id="start_date_img" align="absbottom" />';
								} else { ?>
									<fieldset class="radio">
									<?php echo $lists['published_start'];?>									
									<input class="text_area" type="text" name="start_date" id="start_date" size="10" maxlength="10" value="<?php echo $row->start_date;?>" />
									<input type="button" class="button" value="..." onclick="return showCalendar('start_date', 'y-mm-dd');" />
									</fieldset>
								<?php } ?>
							</td>
						</tr>
						<tr id="end_date">
							<td><?php echo _JLMS_END_DATE; ?>:</td>
							<td valign="middle" style="vertical-align:middle ">
								<?php if (!$row->end_date) { $row->end_date = date('Y-m-d');} ?>
								<?php if (class_exists('JHTML')) {
									$joomla_generated_code = JHTML::_('calendar', $row->end_date, 'end_date', 'end_date', '%Y-%m-%d', array('class' => 'text_area'));
									//ignore joomla generated code ;)
									echo '<input type="text" name="end_date" id="end_date" value="'.htmlspecialchars($row->end_date, ENT_COMPAT, 'UTF-8').'" size="10" maxlength="10" style="text-align:center; background:transparent; border:0px solid; font-size:12px; font-weight:bold; line-height:14px; " />&nbsp;'.
										 '<img class="calendar" src="'.JURI::root().'templates/system/images/calendar.png" alt="calendar" id="end_date_img" align="absbottom" />';
								} else { ?>
									<fieldset class="radio">
									<?php echo $lists['published_end'];?>
									<input class="text_area" type="text" name="end_date" id="end_date" size="10" maxlength="10" value="<?php echo $row->end_date;?>" />
									<input type="button" class="button" value="..." onclick="return showCalendar('end_date', 'y-mm-dd');" />
									</fieldset>
								<?php } ?>
							</td>
						</tr>
						<?php
						}
						?>
						
						<tr>
							<td align="right" width="20%"><?php echo _JLMS_USERS_GR_DESC; ?>:</td>
							<td><?php JLMS_editorArea( 'editor2', $row->ug_description, 'ug_description', '100%;', '250', '40', '20' ) ; ?></td>
						</tr>
						<tr id="forum">
							<td align="right" width="20%"><?php echo _JLMS_USERS_GR_FORUM; ?>:</td>
							<td>
							<fieldset class="radio">
							<?php echo $lists['group_forum'];?>
							</fieldset>
							</td>
						</tr>
						<tr id="chat">
							<td align="right" width="20%"><?php echo _JLMS_USERS_GR_CHAT; ?>:</td>
							<td>
							<fieldset class="radio">
							<?php echo $lists['group_chat'];?>
							</fieldset>
							</td>
						</tr>
					</table>
					<br />
				</td>
			</tr>
		</table>
		</fieldset>
		</div>
	</td></tr></table>	
	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
<?php echo $add_form_hidden_tag; ?> 
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="is_subgroup" value="<?php echo ($row->parent_id)?($row->id):'';?>" />	
	</form>
	
	<script language="javascript" type="text/javascript">
	<!--
	hide_fields();
	-->
	</script>
		<?php
	}
	function JLMS_viewClassUsers( &$rows, &$lists, $option ) {
		JHTML::_('behavior.tooltip');
		?>
		<script language="javascript" type="text/javascript">
		<!--
		function submitbutton(pressbutton) {
			var form = document.adminForm;

			if (pressbutton == 'remove_from_class' || pressbutton == 'add_to_class' || pressbutton == 'add_new_to_class' || pressbutton == 'cancel_class') {
				submitform( pressbutton );
				return;
			}
		}
		
		<?php if( JLMS_J16version() ) { ?>
		Joomla.submitbutton = submitbutton;
		<?php } ?>
		//-->
		</script>
	<form action="index.php" method="post" name="adminForm">	
	<table width="100%">
	<tr>
		<td valign="top" width="220">
		<div>
			<?php echo joomla_lms_adm_html::JLMS_menu();?>
		</div>
		</td>
		<td valign="top">
		<div class="width-100">
		<fieldset class="adminform">
		<table class="adminheading">
		<tr>
			<th class="user">
			<?php echo _JOOMLMS_COMP_NAME;?>: <small><?php echo $lists['group_name'];?> <?php echo _JLMS_USERS_LC_USERS; ?></small>
			</th>
		</tr>
		</table>
		<table align="left" width="95%" border="0">
			<tr>
				<td width="33%" valign="top" align="center">
				<?php echo $lists['class_users'];?><br />
				<input type="button" name="btn_remove" onclick="submitbutton('remove_from_class');" value="&gt;&gt; <?php echo _JLMS_USERS_RMV_FROM_GR; ?>" />
				</td>
				<td width="34%" align="center"><?php echo $lists['course_users'];?><br /><input type="button" onclick="submitbutton('add_to_class');" name="btn_add" value="&lt;&lt; <?php echo _JLMS_USERS_ADD_TO_GR; ?>" /></td>
				<td width="33%" align="center"><?php echo $lists['joomla_users'];?><br /><input type="button" onclick="submitbutton('add_new_to_class');" name="btn_add" value="&lt;&lt; <?php echo _JLMS_USERS_ADD_NEW_USR_TO_GR; ?>" /></td>
			</tr>
		</table>
		</fieldset>
		</div>
	</td></tr></table>
	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	<input type="hidden" name="task" value="view_class" />
	<input type="hidden" name="group_id" value="<?php echo $lists['group_id'];?>" />
	<input type="hidden" name="course_id" value="<?php echo $lists['course_id'];?>" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="hidemainmenu" value="0" />
	</form>

		<?php
	}

	##########################################################################
	###	--- ---   	COURSES	 	--- --- ###
	##########################################################################
	function JLMS_showCoursesList( &$rows, &$pageNav, &$lists, $option ) {
		global $JLMS_CONFIG;
		JHTML::_('behavior.tooltip');
		
		//FLMS multicat
		$multicat = array();
		if ($JLMS_CONFIG->get('multicat_use', 0)) {
			$multicat = array();
			$i=0;
			foreach($lists as $key=>$item){
				if(substr($key, 0, 7) == 'filter_'){
					$multicat[] = $lists['filter_'.$i];
					$i++;
				}
			}
		}		
		?>
		<script language="javascript" type="text/javascript">
		<?php
		if ($JLMS_CONFIG->get('multicat_use', 0)) {
		?>
			var old_filters = new Array();
			function read_filter(){
				var form = document.adminForm;
				var count_levels = '<?php echo count($lists['levels']);?>';
				for(var i=0;i<parseInt(count_levels);i++){
					if(form['filter_id_'+i] != null){
						old_filters[i] = form['filter_id_'+i].value;
					}
				}
			}
			function write_filter(){
				var form = document.adminForm;
				var count_levels = '<?php echo count($lists['levels']);?>';
				var j;
				for(var i=0;i<parseInt(count_levels);i++){
					if(form['filter_id_'+i+''] != null && form['filter_id_'+i+''].value != old_filters[i]){
						j = i;
					}
					if(i > j){
						if(form['filter_id_'+i] != null){
							form['filter_id_'+i].value = 0;	
						}
					}
				}
			}
		<?php
		}
		?>	
			function c_saveorder( n ) {
				c_checkAll_button( n );
			}
			
			//needed by saveorder function
			function c_checkAll_button( n ) {
				for ( var j = 0; j <= n; j++ ) {
					box = eval( "document.adminForm.cb" + j );
					if ( box ) {
						if ( box.checked == false ) {
							box.checked = true;
						}
					} else {
						alert( '<?php echo _JLMS_CRSS_MSG_CANT_CHANGE_ITEM; ?>' );
						return;
					}
				}
				submitform('course_save_order');
			}
		</script>
		<form action="index.php" method="post" name="adminForm">		
		<table width="100%" cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td valign="top" width="220">
				<div>
					<?php echo joomla_lms_adm_html::JLMS_menu();?>
				</div>
				</td>
				<td valign="top">								
				<?php if (!class_exists('JToolBarHelper')) { ?>
				<table class="adminheading" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<th class="categories">
					<?php echo _JOOMLMS_COMP_NAME;?>: <small><?php echo _JLMS_CRSS_LIST; ?></small>
					</th>
					<td width="right">
						<table class="adminlist">
						<?php 
						if ($JLMS_CONFIG->get('multicat_use', 0)){
						?>
							<tr class="row1">
								<td nowrap="nowrap" style="padding:2px 10px 2px 10px; ">
									<?php echo ((isset($lists['levels'][0]->cat_name) && $lists['levels'][0]->cat_name != '')?$lists['levels'][0]->cat_name:_JLMS_COURSES_COURSES_GROUPS);?>
								</td>
								<td nowrap="nowrap" style="padding:2px 10px 2px 10px; ">
									<?php echo $lists['filter_0'];?>
								</td>
							</tr>
						<?php
						} else {
						?>
							<tr class="row1">
								<td nowrap="nowrap" style="padding:2px 10px 2px 10px; ">
									<?php echo _JLMS_FILTER; ?>:
								</td>
								<td nowrap="nowrap" style="padding:2px 10px 2px 10px; "><?php echo $lists['jlms_course_cats'];?></td>
							</tr>
						<?php
						}
						if(count($multicat)){
							for($i=0;$i<count($multicat);$i++){
								if($i > 0){
								?>	
									<tr class="row1">
										<td nowrap="nowrap" style="padding:2px 10px 2px 10px; ">
										<?php echo ((isset($lists['levels'][$i]->cat_name) && $lists['levels'][$i]->cat_name != '')?$lists['levels'][$i]->cat_name:_JLMS_COURSES_COURSES_GROUPS);?>
										</td>
										<td nowrap="nowrap" style="padding:2px 10px 2px 10px; ">
										<?php echo $lists['filter_'.$i];?>
										</td>
									</tr>
								<?php	
								}
							}
						}
						?>
						</table>
					</td>
				</tr>
				</table>
				<?php } else { ?>				
					<table  align="right" style="width: 30%;" class="adminlist">
					<?php 
					if ($JLMS_CONFIG->get('multicat_use', 0)){
					?>
						<tr class="row1">
							<td nowrap="nowrap" align="left" width="100%" style="padding:2px 10px 2px 10px;">
								<?php echo ((isset($lists['levels'][0]->cat_name) && $lists['levels'][0]->cat_name != '')?$lists['levels'][0]->cat_name:_JLMS_COURSES_COURSES_GROUPS);?>
							</td>
							<td nowrap="nowrap" style="padding:2px 10px 2px 10px; ">
								<?php echo $lists['filter_0'];?>
							</td>
						</tr>
					<?php
					} else {
					?>
						<tr class="row1">
							<td nowrap="nowrap" align="left" width="100%" style="padding:2px 10px 2px 10px;"><?php echo _JLMS_FILTER; ?>:</td>
							<td nowrap="nowrap" style="padding:2px 10px 2px 10px; "><?php echo $lists['jlms_course_cats'];?></td>
						</tr>
					<?php
					}
					if(count($multicat)){
						for($i=0;$i<count($multicat);$i++){
							if($i > 0){
							?>	
								<tr class="row1">
									<td nowrap="nowrap" align="left" width="100%" style="padding:2px 10px 2px 10px;">
										<?php echo ((isset($lists['levels'][$i]->cat_name) && $lists['levels'][$i]->cat_name != '')?$lists['levels'][$i]->cat_name:_JLMS_COURSES_COURSES_GROUPS);?>
									</td>
									<td nowrap="nowrap" style="padding:2px 10px 2px 10px;">
									<?php echo $lists['filter_'.$i];?>
									</td>
								</tr>
							<?php	
							}
						}
					}
					?>
					</table>				
				<div style="clear: both;"><!--x--></div>	
				<?php } ?>			
				<table width="100%" border="0">
					<tr>
						<td valign="top">
							<table class="adminlist">
							<thead>
								<tr>
									<th width="20">#</th>
									<th width="20" class="title"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>
									<th class="title"><?php echo _JLMS_CRSS_CRS_NAME; ?></th>
									<?php 
									if($JLMS_CONFIG->get('lms_courses_sortby',0) == 1){
									?>
									<th colspan="2" width="5%">
									<?php echo _JLMS_REORDER; ?>
									</th>
									<th width="2%">
									<?php echo _JLMS_ORDER; ?>
									</th>
									<th width="1%">
									<a href="javascript: c_saveorder( <?php echo count( $rows )-1; ?> )"><img src="<?php echo ADMIN_IMAGES_PATH; ?>filesave.png" border="0" width="16" height="16" alt="<?php echo _JLMS_SAVE_ORDER; ?>" /></a>
									</th>
									<?php
									}
									?>
									<th class="title"><?php echo _JLMS_CATEGORY; ?></th>
									<th class="title"><?php echo _JLMS_OWNER; ?></th>
									<th class="title"><?php echo _JLMS_PUBLISHED; ?></th>
									<th class="title"><?php echo _JLMS_PUBLISH_START; ?></th>
									<th class="title"><?php echo _JLMS_PUBLISH_END; ?></th>
									<th class="title"><?php echo _JLMS_FEE; ?></th>
									<th class="title" width="20"><?php echo _JLMS_ID; ?></th>
								</tr>
							</thead>
							<tfoot>
								<tr>
									<td colspan="14">
									<?php echo $pageNav->getListFooter(); ?>
									</td>
								</tr>
							</tfoot>
							<tbody>
							<?php
							$k = 0;
							for ($i=0, $n=count($rows); $i < $n; $i++) {
								$row = $rows[$i];
								$img_published	= $row->published ? 'tick.png' : 'publish_x.png';
								$task_published	= $row->published ? 'unpublish_course' : 'publish_course';
								$alt_published 	= $row->published ? _JLMS_PUBLISHED : _JLMS_UNPUBLISHED;
								$link 	= 'index.php?option=com_joomla_lms&amp;task=editA_course&amp;id='. $row->id;
								$checked = mosHTML::idBox( $i, $row->id);?>
								<tr class="<?php echo "row$k"; ?>">
									<td><?php echo $pageNav->rowNumber( $i ); ?></td>
									<td><?php echo $checked; ?></td>
									<td align="left">
									<a href="<?php echo $link;?>">
										<?php echo $row->course_name;?>
									</a>
									</td>
									<?php 
									if($JLMS_CONFIG->get('lms_courses_sortby',0) == 1){
									?>
									<td class="order">
									<?php echo $pageNav->orderUpIcon( $i, true, 'course_order_up' ); ?>
									</td>
									<td class="order">
									<?php echo $pageNav->orderDownIcon( $i, $n, true, 'course_order_down' ); ?>
									</td>
									<td align="center" colspan="2">
									<input type="text" name="order[]" size="5" value="<?php echo $row->ordering; ?>" class="text_area" style="text-align: center" />
									</td>
									<?php }?>
									<td align="left">
										<?php echo $row->c_category;?>
									</td>
									<td align="left">
										<span><?php echo mosToolTip( '<b>'._JLMS_NAME.': </b>'.$row->name . "<br>" . '<b>'._JLMS_EMAIL.': </b>' . $row->email, _JLMS_USER_INFORMATION, 280, 'tooltip.png', $row->username );?></span>
									</td>
									<td align="center">
										<a href="javascript: void(0);" onclick="return listItemTask('cb<?php echo $i;?>','<?php echo $task_published;?>')">
											<img src="<?php echo ADMIN_IMAGES_PATH.$img_published;?>" border="0" alt="<?php echo $alt_published; ?>" />
										</a>
									</td>
									<td align="center">
										<?php echo ($row->publish_start?$row->start_date:'-');?>
									</td>
									<td align="center">
										<?php echo ($row->publish_end?$row->end_date:'-');?>
									</td>
									<td align="center">
										<?php echo ($row->paid?($row->course_price?$row->course_price:'0'):_JLMS_FREE);?>
									</td>
									<td align="center">
										<?php echo $row->id;?>
									</td>
								</tr>
								<?php
								$k = 1 - $k;
							}?>
							</tbody>
							</table>
						</td>
					</tr>
				</table>				
			</td></tr></table>
			<input type="hidden" name="option" value="<?php echo $option; ?>" />
			<input type="hidden" name="task" value="courses" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="hidemainmenu" value="0" />		
		</form>
		<?php
	}


	function editCourse( &$row, &$lists, $option, $params = '', $levels=array() ) {
		global $JLMS_CONFIG;
		JHTML::_('behavior.tooltip');
		mosCommonHTML::loadCalendar();

		$db = & JFactory::GetDbo();
		
		if ($JLMS_CONFIG->get('multicat_use', 0)) {
			$multicat = array();
			$i=0;
			foreach($lists as $key=>$item){
				if(substr($key, 0, 6) == 'level_'){
					$multicat[] = $lists['level_'.$i];
					$i++;
				}
			}
		}
		?>
		<script language="javascript" type="text/javascript">
		<!--
		function submitbutton(pressbutton) {
			var form = document.adminForm;

			if (pressbutton == 'cancel_course') {
				submitform( pressbutton );
				return;
			}
			if (pressbutton == 'save_course' || pressbutton == 'apply_course' ) {
				submitform( pressbutton );
				return;
			}
		}
		
		<?php if( JLMS_J16version() ) { ?>
		Joomla.submitbutton = submitbutton;
		<?php } ?>
	
		<?php
		if ($JLMS_CONFIG->get('multicat_use', 0)) {
		?>
		var old_filters = new Array();
		function read_filter(){
			var form = document.adminForm;
			var count_levels = '<?php echo count($levels);?>';
			for(var i=0;i<parseInt(count_levels);i++){
				if(form['level_id_'+i] != null){
					old_filters[i] = form['level_id_'+i].value;
				}
			}
		}
		function write_filter(){
			var form = document.adminForm;
			var count_levels = '<?php echo count($levels);?>';
			var j;
			for(var i=0;i<parseInt(count_levels);i++){
				if(form['level_id_'+i+''] != null && form['level_id_'+i+''].value != old_filters[i]){
					j = i;
				}
				if(i > j){
					if(form['level_id_'+i] != null){
						form['level_id_'+i].value = 0;	
					}
				}
			}
		}
		<?php
		}
		?>
		//-->
		</script>

<form action="index.php" method="post" name="adminForm">
<table width="100%" >
	<tr>
		<td valign="top" width="220">
		<div>
			<?php echo joomla_lms_adm_html::JLMS_menu();?>
		</div>
		</td>
		<td valign="top">
		<div class="width-100">
		<fieldset class="adminform">
		<?php if (!class_exists('JToolBarHelper')) { ?>
		<table class="adminheading">
		<tr>
			<th class="user">
			<?php echo _JOOMLMS_COMP_NAME;?>:
			<small>
			<?php echo $row->id ? _JLMS_CRSS_EDIT_COURSE : _JLMS_CRSS_NEW_COURSE;?>
			</small>
			</th>
		</tr>
		</table>
		<?php } ?>			
		<table width="100%" border="0">
			<tr>
				<td valign="top">
					<table width="100%" >
						<tr>
							<th colspan="2"><?php echo _JLMS_CRSS_COURSE_DETAILS; ?></th>
						<tr>
						<tr>
							<td align="right" width="20%"><?php echo _JLMS_PRICE; ?>:</td>
							<td><input class="text_area" type="text" name="course_price" size="8" maxlength="100" value="<?php echo $row->course_price; ?>" /></td>
						</tr>
						<?php
						if ($JLMS_CONFIG->get('multicat_use', 0)) {
							for($i=0;$i<count($multicat);$i++){
							?>
							<tr>
								<td align="right" width="20%">
									<?php 
										echo ((isset($levels[$i]->cat_name) && $levels[$i]->cat_name != '')?$levels[$i]->cat_name:_JLMS_CRSS_COURSE_CATS);
									?>	
								</td>
								<td>
									<?php 
										echo $multicat[$i];
									?>
								</td>
							</tr>
							<?php
							}
						} else {
						?>
						<tr>
							<td align="right" width="20%"><?php echo _JLMS_SELECT_CATEGORY; ?>:</td>
							<td><?php echo $lists['cat_id'];?></td>
						</tr>
						<?php
						}
						$query = "SELECT lms_config_value FROM #__lms_config WHERE lms_config_var='sec_cat_use'";
						$db->setQuery($query);
						if ($db->loadResult()) {
							?>
						<tr>
							<td align="left" valign="middle" style="vertical-aligh:middle "><?php echo _JLMS_CRSS_SECOND_CATS; ?>:</td>
							<td><?php echo $lists['sec_cat_id'];?></td>
						</tr>
							<?php
						}
						?>
						<tr>
							<td align="left" valign="middle" style="vertical-align:middle "><?php echo _JLMS_CRSS_CRS_NAME; ?>:</td>
							<td><input class="text_area" type="text" name="course_name" style="width:266px;" maxlength="100" value="<?php echo str_replace('"','&quot;', $row->course_name); ?>" /></td>
						</tr>
						<tr>
							<td align="right" width="20%"><?php echo _JLMS_PUBLISHED; ?>:</td>
							<td>
							<fieldset class="radio">
							<?php echo $lists['published'];?>
							</fieldset>
							</td>
						</tr>
						<tr>
							<td align="right" width="20%"><?php echo _JLMS_START_DATE; ?>:</td>
							<td>
							<fieldset class="radio">
							<?php echo $lists['publish_start'];?>
<?php
//$sf_date = mosFormatDate($row->start_date, "%Y-%m-%d");
$format	= '%Y-%m-%d';
$class	= 'inputbox';
$id   = 'start_date';
$name = 'start_date';
$value = $row->start_date;//($sf_date != '-')?$sf_date:'';
if (class_exists('JHTML')) {
	$joomla_generated_code = JHTML::_('calendar', $value, $name, $id, $format, array('class' => $class));
	//ignore joomla generated code ;)
	echo '<input type="text" name="'.$name.'" id="'.$id.'" value="'.htmlspecialchars($value, ENT_COMPAT, 'UTF-8').'" size="10" maxlength="10" />&nbsp;'.
		 '<img class="calendar" src="'.JURI::root().'templates/system/images/calendar.png" alt="calendar" id="'.$id.'_img" align="absbottom" />';

} else { ?>
	<input class="text_area" type="text" name="<?php echo $name;?>" id="<?php echo $id;?>" size="10" maxlength="10" value="<?php echo $value;?>" />
	<input type="button" class="button" value="..." onclick="showCalendar('<?php echo $id;?>', 'y-mm-dd');return showCalendar('<?php echo $id;?>', 'y-mm-dd');" />
<?php } ?>
						</fieldset>
							</td>
						</tr>
						<tr>
							<td align="right" width="20%"><?php echo _JLMS_END_DATE; ?>:</td>
							<td>
							<fieldset class="radio">
							<?php echo $lists['publish_end']; ?>
<?php
//$sf_date = mosFormatDate($row->end_date, "Y-%m-%d");
$format	= '%Y-%m-%d';
$class	= 'inputbox';
$id   = 'end_date';
$name = 'end_date';
$value = $row->end_date;//($sf_date != '-')?$row->end_date:'';
if (class_exists('JHTML')) {
	$joomla_generated_code = JHTML::_('calendar', $value, $name, $id, $format, array('class' => $class));
	//ignore joomla generated code ;)
	echo '<input type="text" name="'.$name.'" id="'.$id.'" value="'.htmlspecialchars($value, ENT_COMPAT, 'UTF-8').'" size="10" maxlength="10" />&nbsp;'.
		 '<img class="calendar" src="'.JURI::root().'templates/system/images/calendar.png" alt="calendar" id="'.$id.'_img" align="absbottom" />';

} else { ?>
	<input class="text_area" type="text" name="<?php echo $name;?>" id="<?php echo $id;?>" size="10" maxlength="10" value="<?php echo $value;?>" />
	<input type="button" class="button" value="..." onclick="showCalendar('<?php echo $id;?>', 'y-mm-dd');return showCalendar('<?php echo $id;?>', 'y-mm-dd');" />
<?php } ?>
						</fieldset>
							</td>
						</tr>
						<tr>
							<td align="left" valign="top" style="vertical-align:top "><?php echo _JLMS_CRSS_ACCESS_LEVEL; ?>:</td>
							<td><?php echo $lists['gid'];?></td>
						</tr>
						<tr>
							<td align="left" valign="top"><?php echo _JLMS_DESCRIPTION; ?>:</td>
							<td>
							<?php JLMS_editorArea( 'editor1', $row->course_description, 'course_description', '100%;', '250', '40', '20' ) ; ?>
							</td>
						</tr>
						<tr>
							<td align="left" valign="top" style="vertical-align:top"><?php echo _JLMS_SHORT_DESC; ?>:</td>
							<td><textarea class="inputbox" name="course_sh_description" cols="50" rows="3"><?php echo $row->course_sh_description; ?></textarea></td>
						</tr>
						<tr>
							<td align="left" valign="top"><?php echo _JLMS_CRSS_META_DATA;  ?>:</td>
							<td>
							<textarea class="text_area" name="metadesc" cols="50" rows="3"><?php echo $row->metadesc; ?></textarea>
							</td>
						</tr>
						<tr>
							<td align="left" valign="top"><?php echo _JLMS_CRSS_META_KEYS; ?>:</td>
							<td>
							<textarea class="text_area" name="metakeys" cols="50" rows="3"><?php echo $row->metakeys; ?></textarea>
							</td>
						</tr>
						<tr>
							<td align="right" width="20%"><?php echo _JLMS_LANGUAGE; ?>:</td>
							<td><?php echo $lists['language'];?></td>
						</tr>
						<tr>
							<td align="right" width="20%"><?php echo _JLMS_CRSS_ENABLE_CHAT; ?>:</td>
							<td>
							<fieldset class="radio">
							<?php echo $lists['add_chat'];?>
							</fieldset>
							</td>
						</tr>
						<tr>
							<td align="right" width="20%"><?php echo _JLMS_CRSS_ENABLE_HOMEWORKS; ?>:</td>
							<td>
							<fieldset class="radio">
							<?php echo $lists['add_hw'];?>
							</fieldset>
							</td>
						</tr>
						<tr>
							<td align="right" width="20%"><?php echo _JLMS_CRSS_USR_REG_ATTEND; ?>:</td>
							<td>
							<fieldset class="radio">
							<?php echo $lists['add_attend'];?>
							</fieldset>
							</td>
						</tr>
						<tr>
							<td align="right" width="20%"><?php echo _JLMS_CRSS_ENBL_SELF_REG; ?>:</td>
							<td>
							<fieldset class="radio">
							<?php echo $lists['self_reg'];?>
							</fieldset>
							</td>
						</tr>
						<tr>
							<td align="left" width="15%" valign="middle" style="vertical-align:middle "><?php echo _JLMS_CRSS_FEE_TYPE; ?>:</td>
							<td>
								<fieldset class="radio">
									<input type="radio" name="paid" id="free_type"  value="0" <?php echo $row->paid ? '' : 'checked';?> />
									<label for="free_type"><?php echo _JLMS_FREE; ?></label>
									<input type="radio" name="paid" id="paid_type" value="1" <?php echo $row->paid ? 'checked' : '';?> />
									<label for="paid_type"><?php echo _JLMS_PAID; ?></label>
								</fieldset>
							</td>
						</tr>
						
						<?php
						//Course Properties Event//
						if(isset($lists['plugin_return']) && count($lists['plugin_return'])){
							$fields = $lists['plugin_return'];
							foreach($fields as $field){
								?>
								<tr>
									<td align="left" width="20%" valign="middle" style="vertical-align:middle ">
										<br />
										<?php echo $field->name;?>:
									</td>
									<td colspan="2">
										<br />
										<?php echo $field->control;?>
									</td>
								</tr>
								<?php
							}
						}
						//Course Properties Event//
						?>
						
						<tr>
							<td align="right" width="20%"><?php echo _JLMS_CRSS_ENBL_FORUM; ?>:</td>
							<td>
							<fieldset class="radio">
							<?php echo $lists['add_forum'];?><?php echo _JLMS_CRSS_TIP_ENBL_FORUM; ?>
							</fieldset>
							</td>
						</tr>
						<tr>
							<td align="right" width="20%"><?php echo _JLMS_CRSS_SET_MAX_A_COUNTS; ?></td>
							<td><input type="text" name="params[max_attendees]" class="text_area" value="<?php echo $params->get('max_attendees', 0); ?>" />
								<?php
								if (isset($params->_params)) {
									foreach ($params->_params as $name => $value) {
										switch ($name) {
											case 'max_attendees': break;
											default : echo "<input type='hidden' name='params[$name]' value='$value' />"; break;
										}
									}
								} elseif (isset($params->_registry['_default']['data'])) {
									foreach ($params->_registry['_default']['data'] as $name => $value) {
										switch ($name) {
											case 'max_attendees': break;
											default : echo "<input type='hidden' name='params[$name]' value='$value' />"; break;
										}
									}
								}
								?>
							</td>
						</tr>
						<tr>
							<td align="right" width="20%"><?php echo _JLMS_CRSS_CRS_AUTHOR_ID; ?>:</td>
							<td><input class="text_area" type="text" name="owner_id" value="<?php echo $row->owner_id; ?>" /></td>
						</tr>
					</table>
					<br />
				</td>
			</tr>
		</table>		
		</fieldset>
		</div>
</td>
</tr>
</table>
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
		<input type="hidden" name="task" value="editA_course" />		
		</form>
		<?php
	}

	function view_preDeletePage( &$rows, $id, $option ) {
		?>
<script language="javascript" type="text/javascript">
<!--
function submitbutton(pressbutton) {
	var form = document.adminForm;
	if ((pressbutton == 'course_delete_yes') && (form.boxchecked.value == "0")){
		alert( '<?php echo _JLMS_CRSS_MSG_SELECT_ITEM; ?>' );
	} else {
		form.task.value = pressbutton;
		form.submit();
	}
}

<?php if( JLMS_J16version() ) { ?>
Joomla.submitbutton = submitbutton;
<?php } ?>
	
//-->
</script>

<form action="index.php" method="post" name="adminForm">
<table width="100%" >
	<tr>
		<td valign="top" width="220">
		<div>
			<?php echo joomla_lms_adm_html::JLMS_menu();?>
		</div>
		</td>
		<td valign="top">	
		<table class="adminheading">
		<tr>
			<th class="categories">
			<?php echo _JOOMLMS_COMP_NAME;?>: <small><?php echo _JLMS_CRSS_DELETE_CRSS; ?></small>
			</th>
		</tr>
		</table>		
		<table width="100%" border="0">
			<tr>
				<td valign="top">
					<table class="adminlist">
					<tr>
						<th width="20">#</th>
						<th width="20" class="title"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>
						<th class="title"><?php echo _JLMS_CRSS_COURSE_NAME; ?></th>
					</tr>
					<?php
					$k = 0;
					for ($i=0, $n=count($rows); $i < $n; $i++) {
						$row = $rows[$i];
						$link 	= 'index.php?option=com_joomla_lms&amp;task=editA_course&amp;id='. $row->id;
						$checked = joomla_lms_adm_html::idBox( $i, $row->id, true);?>
						<tr class="<?php echo "row$k"; ?>">
							<td align="center"><?php echo ($i + 1); ?></td>
							<td align="center"><?php echo $checked; ?></td>
							<td align="left">
								<?php echo $row->course_name;?>
							</td>
						</tr>
						<?php
						$k = 1 - $k;
					}?>
					</table>
				</td>
			</tr>
		</table>		
	</td></tr></table>
	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	<input type="hidden" name="task" value="del_course" />
	<input type="hidden" name="boxchecked" value="<?php echo count($rows);?>" />
	<input type="hidden" name="hidemainmenu" value="0" />	
	</form>
	<?php
	}
	function idBox( $rowNum, $recId, $checked=false, $name='cid' ) {
		return '<input type="checkbox" id="cb'.$rowNum.'" name="'.$name.'[]" value="'.$recId.'" onclick="isChecked(this.checked);" '.($checked?'checked':'').' />';
	}

	##########################################################################
	###	--- ---   	LANGUAGES	 	--- --- ###
	##########################################################################
	function JLMS_showLangsList( &$rows, &$pageNav, &$lists, $option ) {
		JHTML::_('behavior.tooltip');
		?>
<form action="index.php" method="post" name="adminForm">
<table width="100%" >
	<tr>
		<td valign="top" width="220">
		<div>
			<?php echo joomla_lms_adm_html::JLMS_menu();?>
		</div>
		</td>
		<td valign="top">	
		<?php if (!class_exists('JToolBarHelper')) { ?>
		<table class="adminheading">
		<tr>
			<th class="categories">
			<?php echo _JOOMLMS_COMP_NAME;?>: <small><?php echo _JLMS_LANG_LIST; ?></small>
			</th>
		</tr>
		</table>
		<?php } ?>	
		<table width="100%" border="0">
			<tr>
				<td valign="top">
					<table class="adminlist">
					<thead>
						<tr>
							<th width="20">#</th>
							<th width="20" class="title"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>
							<th width="40"><?php echo _JLMS_PUBLISHED; ?></th>
							<th class="title"><?php echo _JLMS_LANG_NAME; ?></th>
							<th width="1" class="title"><?php echo _JLMS_DEFAULT; ?></th>
							<th width="120" class="title"><?php echo _JLMS_LANG_FRONT_TRANS; ?></th>
							<th width="120" class="title"><?php echo _JLMS_LANG_BACK_TRANS; ?></th>							
						</tr>
					</thead>
					<tfoot>
						<tr>
							<td colspan="7">
							<?php echo $pageNav->getListFooter(); ?>
							</td>
						</tr>
					</tfoot>
					<tbody>
					<?php
					$k = 0;					
					for ($i=0, $n=count($rows); $i < $n; $i++) {
						$row = $rows[$i];
						$img_published	= $row->published ? 'tick.png' : 'publish_x.png';
						$task_published	= $row->published ? 'unpublish_lang' : 'publish_lang';
						$alt_published 	= $row->published ? _JLMS_PUBLISHED : _JLMS_UNPUBLISHED;						
						$img_front_trans = $row->is_front_trans?'tick.png':'publish_x.png';
						$front_trans_alt = $row->is_front_trans?_JLMS_LANG_AVAILABLE:_JLMS_LANG_NOT_AVAILABLE;				
						$img_back_trans = $row->is_back_trans?'tick.png':'publish_x.png';
						$back_trans_alt = $row->is_back_trans?_JLMS_LANG_AVAILABLE:_JLMS_LANG_NOT_AVAILABLE;
						$default = $row->default?'<img src="'.ADMIN_IMAGES_PATH.'tick.png" border="0" />':'';
						
						$checked = mosHTML::idBox( $i, $row->id);?>
						<tr class="<?php echo "row$k"; ?>">
							<td align="center"><?php echo $pageNav->rowNumber( $i ); ?></td>
							<td><?php echo $checked; ?></td>
							<td align="center">
								<a href="javascript: void(0);" onclick="return listItemTask('cb<?php echo $i;?>','<?php echo $task_published;?>')">
									<img src="<?php echo ADMIN_IMAGES_PATH.$img_published;?>" border="0" alt="<?php echo $alt_published; ?>" />
								</a>
							</td>
							<td align="left">
								<?php echo $row->lang_name;?>
							</td>
							<td align="center">
								<?php echo $default; ?>
							</td>
							<td align="center">
								<img src="<?php echo ADMIN_IMAGES_PATH.$img_front_trans;?>" border="0" alt="<?php echo $front_trans_alt; ?>" />
							</td>
							<td align="center">
								<img src="<?php echo ADMIN_IMAGES_PATH.$img_back_trans;?>" border="0" alt="<?php echo $back_trans_alt; ?>" />
							</td>
						</tr>
						<?php
						$k = 1 - $k;
					}?>
					</tbody>
					</table>
				</td>
			</tr>
		</table>	
<table align="center">
<?php
joomla_lms_adm_html::writableCell_cfg( 'components/com_joomla_lms/languages', 'components/com_joomla_lms/languages', true );
?>
</table>
	</td></tr></table>
	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	<input type="hidden" name="task" value="languages" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="hidemainmenu" value="0" />	
	</form>
		<?php
	}

	function JLMS_showImportLang( $option ) {
		?>
<script language="javascript" type="text/javascript">
<!--
function submitbutton(pressbutton) {
	var form = document.adminForm;
	submitform( pressbutton );
	return;
}

<?php if( JLMS_J16version() ) { ?>
Joomla.submitbutton = submitbutton;
<?php } ?>
	
//-->
</script>
<form action="index.php" method="post" name="adminForm" enctype="multipart/form-data">
<table width="100%" >
	<tr>
		<td valign="top" width="220">
		<div>
			<?php echo joomla_lms_adm_html::JLMS_menu();?>
		</div>
		</td>
		<td valign="top">
		<div class="width-100">
		<fieldset class="adminform">
		<?php if (!class_exists('JToolBarHelper')) { ?>
		<table class="adminheading">
		<tr>
			<th class="categories">
			<?php echo _JOOMLMS_COMP_NAME;?>: <small><?php echo _JLMS_IMPORT_LANG; ?></small>
			</th>
		</tr>
		</table>
		<?php } ?>		
		<table width="100%" >
			<tr>
				<th colspan="2"><?php echo _JLMS_LANG_IMPORT_LANG; ?></th>
			</tr>
			<tr>
				<td colspan="2">
					<?php echo _JLMS_LANG_MSG_IMPORT_NEW; ?>					
				</td>
			</tr>
			<tr>
				<td align="right" width="20%" valign="top"><?php echo _JLMS_SELECT_FILE; ?>:</td>
				<td valign="top">
					<input type="file" size="35" name="import_lang_file" >
				</td>
			</tr>
		</table>
		</fieldset>
		</div>
	</td></tr></table>
	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	<input type="hidden" name="task" value="import_lang" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="hidemainmenu" value="0" />	
	</form>
<?php
	}
	
	function JLMS_ViewChildrens($rows, $pagination, $lists, $option){
		?>
		
		<script language="javascript" type="text/javascript">
		function submitbutton(pressbutton) {
			var form = document.adminForm;
			if (pressbutton == 'edit_child' && form.boxchecked.value == 0) {
				alert('<?php echo _JLMS_JS_ALERT_SELECT_FROM_LIST_TO_.' '._JLMS_JS_ALERT_TO_EDIT;?>');
			} else
			if (pressbutton == 'delete_child' && form.boxchecked.value == 0) {
				alert('<?php echo _JLMS_JS_ALERT_SELECT_FROM_LIST_TO_.' '._JLMS_JS_ALERT_TO_DELETE;?>');
			} if(pressbutton == 'switch_parent'){
				form.task.value = 'view_childrens';
				form.submit();	
			} else {
				form.task.value = pressbutton;
				form.submit();
			}
		}
		
		<?php if( JLMS_J16version() ) { ?>
		Joomla.submitbutton = submitbutton;
		<?php } ?>
		//-->
		</script>
		
		<form action="index.php" method="post" name="adminForm">		
			<table width="100%" >
				<tr>
					<td valign="top" width="220">
						<div>
							<?php echo joomla_lms_adm_html::JLMS_menu();?>
						</div>
					</td>
					<td valign="top">				
						<?php if (!class_exists('JToolBarHelper')) { ?>
						<table class="adminheading">
						<tr>
							<th class="user">
							<?php echo _JOOMLMS_COMP_NAME;?>: <small><?php echo _JLMS_CEO_PARENTS_CEO; ?></small>
							</th>
						</tr>
						</table>
						<?php } ?>
						
						<div style="width: 100%;">
							<table  align="right" style="width: 30%;" class="adminlist">
								<tr class="row1">
									<td nowrap="nowrap" align="left" width="100%" style="padding:2px 10px 2px 10px;">
									<?php echo _JLMS_CEO_PARENTS_CEO; ?>:&nbsp;&nbsp;
									</td>
									<td nowrap="nowrap">
										<?php echo $lists['f_parents'];?>
									</td>
								</tr>
							</table>
						</div>
						<div style="clear: both;"><!--x--></div>						
						<table class="adminlist" cellpadding="0" cellspacing="0" border="0" width="100%">
							<thead>
							<tr>
								<th width="1%" class="sectiontableheader" align="center">
									#
								</th>
								<th width="1%" class="sectiontableheader" align="center">
									<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" />
								</th>
								<th class="sectiontableheader">
									<?php echo _JLMS_USERNAME; ?>
								</th>
								<th class="sectiontableheader">
									<?php echo _JLMS_NAME; ?>
								</th>
								<th class="sectiontableheader">
									<?php echo _JLMS_EMAIL; ?>
								</th>
							</tr>
							</thead>
							<tfoot>
								<tr>
									<td colspan="7" align="center">
									<?php echo $pagination->getListFooter(); ?>
									</td>
								</tr>
							</tfoot>
							<tbody>
							<?php
							$k = 1;
							for ($i=0, $n=count($rows); $i < $n; $i++) {
								$row = $rows[$i];
								if(isset($row->user_id) && $row->user_id){
									$checked = JHTML::_('grid.id', $i, $row->id);
								} else 
								if(isset($row->group_id) && $row->group_id){
									$checked = JHTML::_('grid.id', $i, $row->id, false, 'gid');
								}
								?>
								<tr class="<?php echo "sectiontableentry$k"; ?>">
									<td align="center"><?php echo $pagination->getRowOffset( $i ); ?></td>
									<td align="center"><?php echo $checked; ?></td>
									<?php
									if(isset($row->user_id) && $row->user_id){
									?>
									<td>
										<?php echo $row->username;?>
									</td>
									<td>
										<?php echo $row->name;?>
									</td>
									<td>
										<?php echo $row->email;?>
									</td>
									<?php
									} else 
									if(isset($row->group_id) && $row->group_id){
									?>
									<td colspan="3">
										<?php echo $row->ug_name;?>
									</td>
									<?php	
									}
									?>
								</tr>
								<?php
								$k = 3 - $k;
							}?>
							</tbody>
						</table>						
					</td>
				</tr>
			</table>
			
			<input type="hidden" name="option" value="<?php echo $option; ?>" />
			<input type="hidden" name="task" value="view_parents" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="hidemainmenu" value="0" />		
		</form>
		
		<?php
	}
	
	function JLMS_EditChildren($rows, $pagination, $lists, $option){
		?>
		<script language="javascript" type="text/javascript">
		function submitbutton(pressbutton) {
			var form = document.adminForm;
			if (pressbutton == 'save_child' && form.parent_id.value == 0){
				alert('<?php echo _JLMS_CEO_JS_ALERT_SELECT_PARENT;?>');
			} else 
			if(pressbutton == 'save_child' && (form['group_ids[]'].value == 0 && form.boxchecked.value == 0)){
				alert( '<?php echo _JLMS_JS_ALERT_SELECT_FROM_LIST_TO_.' '._JLMS_JS_ALERT_TO_SAVE;?>' );
			} else
			if(pressbutton == 'switch_parent'){
				if(form.task.value == 'add_child'){
					form.action = form.action.replace('add_child', 'edit_child');
				}
				form.task.value = 'edit_child';
				form.submit();
			} else {
				form.task.value = pressbutton;
				form.submit();
			}
		}
		
		<?php if( JLMS_J16version() ) { ?>
		Joomla.submitbutton = submitbutton;
		<?php } ?>
		//-->
		</script>
		<form action="index.php" method="post" name="adminForm">		
			<table width="100%" >
				<tr>
					<td valign="top" width="220">
						<div>
							<?php echo joomla_lms_adm_html::JLMS_menu();?>
						</div>
					</td>
					<td valign="top">				
						<?php if (!class_exists('JToolBarHelper')) { ?>
						<table class="adminheading">
						<tr>
							<th class="user">
							<?php echo _JOOMLMS_COMP_NAME;?>: <small><?php echo _JLMS_CEO_PARENTS_CEO; ?></small>
							</th>
						</tr>
						</table>
						<?php } ?>					
						<table  width="100%" cellpadding="0" cellspacing="0" border="0">
							<tr>
							<td colspan="2">
							<div class="width-100">
							<fieldset class="adminform">
							<table  width="100%">
								<tr>
								<td colspan="2" class="contentheading">
									<b><?php echo _JLMS_DETAILS; ?></b>
								</td>
								</tr>
								<tr>
									<td width="20%">
										<?php echo _JLMS_CEO_PARENTS_CEO; ?>:
									</td>
									<td>
										<?php echo $lists['list_parents'];?>
									</td>
								</tr>
								<tr>
									<td colspan="2">&nbsp;</td>
								</tr>
								<tr>
									<td colspan="2" class="contentheading">
										<b><?php echo JText::_(_JLMS_CEO_LABEL_FIELDS_ADD_GROUPS);?></b>
									</td>
								</tr>
								<tr>
									<td width="20%"><?php echo _JLMS_SELECT_GROUP; ?>:</td>
									<td><?php echo $lists['list_groups'];?></td>
								</tr>
								
								<tr>
									<td colspan="2" class="contentheading">
										<b><?php echo JText::_(_JLMS_CEO_LABEL_FIELDS_AND_OR_ADD_USERS);?></b>
									</td>
								</tr>
							</table>
							</fieldset>
							</div>
							</td>							
							</tr>
							
							<tr>
								<td colspan="2">
									
									<div style="width: 100%;">
										<table  align="right" style="width: 30%;" class="adminlist">
											<tr class="row1">
												<td nowrap="nowrap" align="left" width="100%" style="padding:2px 10px 2px 10px;">
												<?php echo _JLMS_USERS_FILTER_BY_GR; ?>:&nbsp;&nbsp;
												</td>
												<td nowrap="nowrap">
													<?php echo $lists['f_groups'];?>
												</td>
											</tr>
										</table>
									</div>
									<div style="clear: both;"><!--x--></div>
								
									<table class="adminlist" border="0" cellpadding="0" cellspacing="0">
										<thead>
											<tr>
												<th width="1%" class="sectiontableheader" align="center">
													#
												</th>
												<th width="1%" class="sectiontableheader" align="center">
													<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" />
												</th>
												<th class="sectiontableheader">
													<?php echo _JLMS_USERNAME; ?>
												</th>
												<th class="sectiontableheader">
													<?php echo _JLMS_NAME; ?>
												</th>
												<th class="sectiontableheader">
													<?php echo _JLMS_EMAIL; ?>
												</th>
											</tr>
										</thead>
										<tfoot>
											<tr>
												<td colspan="7" align="center">
												<?php echo $pagination->getListFooter(); ?>
												</td>
											</tr>
										</tfoot>
										<tbody>
											<?php
											$k = 1;
											for ($i=0, $n=count($rows); $i < $n; $i++) {
												$row = $rows[$i];
												$checked = JHTML::_('grid.id', $i, $row->id);?>
												<tr class="<?php echo "sectiontableentry$k"; ?>">
													<td align="center"><?php echo $pagination->getRowOffset( $i ); ?></td>
													<td align="center"><?php echo $checked; ?></td>
													<td>
														<?php
														echo $row->username;
														?>
													</td>
													<td>
														<?php
														echo $row->name;
														?>
													</td>
													<td>
														<?php
														echo $row->email;
														?>
													</td>
												</tr>
												<?php
												$k = 3 - $k;
											}?>
										</tbody>
									</table>
								
								</td>
							</tr>
							<?php 
							/*if(!$row->user_id) {
								?>
								<tr>
									<td width="15%" valign="middle" style="vertical-align:middle ">
										<?php echo _JLMS_USERS_SLCT_USRNAME; ?>:
									</td>
									<td width="100%">
									<?php echo $lists['users']; ?>
									</td>
								</tr>
								<tr>
									<td width="15%" valign="middle" style="vertical-align:middle ">
										<?php echo _JLMS_USERS_OR_NAME; ?>:
									</td>
									<td>
									<?php echo $lists['users_names']; ?>
									</td>
								</tr>
								<tr>
									<td valign="middle" style="vertical-align:middle ">
										<?php echo _JLMS_USERS_OR_EMAIL; ?>:
									</td>
									<td>
									<?php echo $lists['users_emails']; ?>
									</td>
								</tr>
								<?php 
							}*/
							?>
						</table>					
					</td>
				</tr>
			</table>			
			<input type="hidden" name="option" value="<?php echo $option; ?>" />
			<input type="hidden" name="task" value="add_child" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="hidemainmenu" value="0" />		
		</form>
		<?php
	}
	
	function JLMS_ViewParents( &$rows, &$lists, &$pagination, $option ) {
		JHTML::_('behavior.tooltip');
	?>
	<script language="javascript" type="text/javascript">
		function switch_type(){
			var form = document.adminForm;
				form.task.value = 'view_parents';
				form.submit();	
		}
	</script>
	<form action="index.php" method="post" name="adminForm">	
		<table width="100%" >
			<tr>
				<td valign="top" width="220">
					<div>
						<?php echo joomla_lms_adm_html::JLMS_menu();?>
					</div>
				</td>
				<td valign="top">			
					<?php if (!class_exists('JToolBarHelper')) { ?>
					<table class="adminheading">
					<tr>
						<th class="user">
						<?php echo _JOOMLMS_COMP_NAME;?>: <small><?php echo _JLMS_CEO_PARENTS_CEO; ?></small>
						</th>
					</tr>
					</table>
					<?php } ?>					
					<table class="adminlist" cellpadding="0" cellspacing="0" border="0" width="100%">
						<thead>
							<tr>
								<th width="1%" class="sectiontableheader" align="center">
									#
								</th>
								<th width="1%" class="sectiontableheader" align="center">
									<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" />
								</th>
								<th class="sectiontableheader" width="25%">
									<?php echo _JLMS_USERNAME; ?>
								</th>
								<th class="sectiontableheader">
									<?php echo _JLMS_CEO_CHILDRENS;?>
								</th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<td colspan="7" align="center">
								<?php echo $pagination->getListFooter(); ?>
								</td>
							</tr>
						</tfoot>
						<tbody>
						<?php
						$k = 1;
						for ($i=0, $n=count($rows); $i < $n; $i++) {
							$row = $rows[$i];
							$checked = JHTML::_('grid.id', $i, $row->user_id);?>
							<tr class="<?php echo "sectiontableentry$k"; ?>">
								<td align="center"><?php echo $pagination->getRowOffset( $i ); ?></td>
								<td align="center"><?php echo $checked; ?></td>
								<td>
									<a href="<?php echo JRoute::_("index.php?option=com_jlms_users&task=childrens&parent_id=$row->user_id");?>">
										<span><?php echo JHTML::_('tooltip', '<b>'._JLMS_NAME.': </b>'.$row->name . "<br>" . '<b>'._JLMS_EMAIL.': </b>' . $row->email, _JLMS_USER_INFORMATION, 'tooltip.png', $row->username );?></span>
									</a>
								</td>
								<td align="left">
									<div>
										<?php
										if($row->count_groups){
										?>
										<a href="<?php echo JRoute::_("index.php?option=com_joomla_lms&task=group_managers&filt_users=$row->user_id");?>">
											<b><?php echo JText::_($row->count_groups.' '._JLMS_GROUPS);?>:</b>
										</a>
										<?php
										} else {
											?>
											<b><?php echo JText::_($row->count_groups.' '._JLMS_GROUPS);?>:</b>
											<?php
										}
										if(isset($row->groups) && count($row->groups)){
										?>
										[
										<?php
										foreach($row->groups as $m=>$grp){
											if($m < 15){
												?>
												<a href="<?php echo JRoute::_("index.php?option=com_joomla_lms&task=view_class_users_groups&group_id=$grp->group_id");?>">
													<?php echo $grp->group_name;?>
												</a>
												<?php
												if($m < count($row->groups) - 1){
													echo ', ';
												}
											} else {
												echo ' ...';
												break;
											}
										}
										?>
										]
										<?php
										}
										?>
									</div>
									<div>
										<?php
										if($row->count_users){
										?>
										<a href="<?php echo JRoute::_("index.php?option=com_joomla_lms&task=view_childrens&parent_id=$row->user_id");?>">
											<b><?php echo JText::_($row->count_users.' '._JLMS_USERS);?>:</b>
										</a>
										<?php
										} else {
											?>
											<b><?php echo JText::_($row->count_users.' '._JLMS_USERS);?>:</b>												
											<?php
										}
										if(isset($row->users) && count($row->users)){
										?>
										[
										<?php
										foreach($row->users as $m=>$usr){
											if($m < 15){
												/*
												?>
												<a href="<?php echo JRoute::_("index.php?option=com_jlms_users&task=group_members&f_group_id=$usr->user_id&Itemid=".$lists['Itemid']);?>">
													<?php echo $usr->username;?>
												</a>
												<?php
												*/
												echo JHTML::_('tooltip', '<b>'._JLMS_NAME.': </b>'.$usr->name . "<br>" . '<b>'._JLMS_EMAIL.': </b>' . $usr->email, _JLMS_USER_INFORMATION, 'tooltip.png', $usr->username );
												if($m < count($row->users) - 1){
													echo ', ';
												}
											} else {
												echo ' ...';
												break;
											}
										}
										?>
										]
										<?php
										}
										?>
									</div>
								</td>
							</tr>
							<?php
							$k = 3 - $k;
						}?>
						</tbody>
					</table>					
				</td>
			</tr>
		</table>
		
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="task" value="view_parents" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0" />	
	</form>
		<?php

	}

	function JLMS_editParentHtml( &$row, &$lists, $option, &$learners, &$stores ) {
		global $JLMS_CONFIG;
		JHTML::_('behavior.tooltip');
		?>
<script language="javascript" type="text/javascript">
<!--
function getObj(name) {
	if (document.getElementById){return document.getElementById(name);}
	else if (document.all){return document.all[name];}
	else if (document.layers){return document.layers[name];}
}
function ReAnalize_tbl_Rows( start_index, tbl_id ) {
	start_index = 1;
	var tbl_elem = getObj(tbl_id);
	if (tbl_elem.rows[start_index]) {
		var count = start_index; var row_k = 1 - start_index%2;//0;
		for (var i=start_index; i<tbl_elem.rows.length; i++) {
			tbl_elem.rows[i].cells[0].innerHTML = count;
			Redeclare_element_inputs(tbl_elem.rows[i].cells[1]);
			tbl_elem.rows[i].className = 'row'+row_k;
			count++;
			row_k = 1 - row_k;
		}
	}
}
function Redeclare_element_inputs(object) {
	if (object.hasChildNodes()) {
		var children = object.childNodes;
		for (var i = 0; i < children.length; i++) {
			if (children[i].nodeName.toLowerCase() == 'input') {
				var inp_name = children[i].name;
				//alert(inp_name);
				var inp_value = children[i].value;
				object.removeChild(object.childNodes[i]);
				var input_hidden = document.createElement("input");
				input_hidden.type = "hidden";
				input_hidden.name = inp_name;
				input_hidden.value = inp_value;
				object.appendChild(input_hidden);
			}
		}
	}
}
function Delete_tbl_row(element) {
	var del_index = element.parentNode.parentNode.sectionRowIndex;
	var tbl_id = element.parentNode.parentNode.parentNode.parentNode.id;
	element.parentNode.parentNode.parentNode.deleteRow(del_index);
	ReAnalize_tbl_Rows(del_index - 1, tbl_id);
}
function Add_new_tbl_field(tbl_id, is_group) {
	
	if(is_group){
		var zzz = getObj('jlms_group_ids').selectedIndex;
		var new_grp_un = getObj('jlms_group_ids').options[zzz].text;
		var new_grp_id = getObj('jlms_group_ids').options[zzz].value;
		if (new_grp_un == '0' || new_grp_un == 0 || new_grp_un == '') {
			alert("<?php echo _JLMS_CEO_SLCT_GR; ?>");return;
		}
		
	} else {
		var zzz = getObj('jlms_user_ids').selectedIndex;
		var new_u_un = getObj('jlms_user_ids').options[zzz].text;
		var new_u_id = getObj('jlms_user_ids').options[zzz].value;
		var zzz = getObj('jlms_user_names').selectedIndex;
		var new_u_name = getObj('jlms_user_names').options[zzz].text;
		var zzz = getObj('jlms_user_emails').selectedIndex;
		var new_u_email = getObj('jlms_user_emails').options[zzz].text;
		if (new_u_un == '0' || new_u_un == 0 || new_u_un == '') {
			alert("<?php echo _JLMS_CEO_SLCT_LNR; ?>");return;
		}
	}

	var tbl_elem = getObj(tbl_id);
	var row = tbl_elem.insertRow(tbl_elem.rows.length);
	var cell1 = document.createElement("td");
	var cell2 = document.createElement("td");
	var cell3 = document.createElement("td");
	var cell4 = document.createElement("td");
	
	<?php
	if($JLMS_CONFIG->get('use_global_groups', 1)){
	?>
	var cell5 = document.createElement("td");
	var cell6 = document.createElement("td");
	var cell7 = document.createElement("td");
	var cell8 = document.createElement("td");
	<?php
	} else {
	?>
	var cell5 = document.createElement("td");
	var cell6 = document.createElement("td");
	var cell7 = document.createElement("td");
	<?php
	}
	?>
	var input_hidden = document.createElement("input");
		input_hidden.type = "hidden";
	if(is_group){
		input_hidden.name = 'jq_hid_fields_grp_ids[]';
		input_hidden.value = new_grp_id;
		<?php
		if($JLMS_CONFIG->get('use_global_groups', 1)){
		?>
		cell1.align = 'center';
		cell1.innerHTML = 0;
		cell2.innerHTML = '';
		cell3.innerHTML = new_grp_un;
		cell3.appendChild(input_hidden);
		cell4.innerHTML = '';
		cell5.innerHTML = '';
		<?php
		} else {
		?>
		cell1.align = 'center';
		cell1.innerHTML = 0;
		cell2.innerHTML = new_grp_un;
		cell2.appendChild(input_hidden);
		cell3.innerHTML = '';
		cell4.innerHTML = '';
		<?php
		}
		?>
	} else {
		input_hidden.name = 'jq_hid_fields_ids[]';
		input_hidden.value = new_u_id;
		<?php
		if($JLMS_CONFIG->get('use_global_groups', 1)){
		?>
		cell1.align = 'center';
		cell1.innerHTML = 0;
		cell2.innerHTML = new_u_un;
		cell2.appendChild(input_hidden);
		cell3.innerHTML = '';
		cell4.innerHTML = new_u_name;
		cell5.innerHTML = new_u_email;
		<?php
		} else {
		?>
		cell1.align = 'center';
		cell1.innerHTML = 0;
		cell2.innerHTML = new_u_un;
		cell2.appendChild(input_hidden);
		cell3.innerHTML = new_u_name;
		cell4.innerHTML = new_u_email;
		<?php
		}
		?>
	}
	<?php
	if($JLMS_CONFIG->get('use_global_groups', 1)){
	?>
	cell6.innerHTML = '';
	cell6.width = 'auto';
	cell7.innerHTML = '<a href="javascript: void(0);" onclick="javascript:Delete_tbl_row(this); return false;" title="<?php echo _JLMS_DELETE; ?>"><img src="<?php echo ADMIN_IMAGES_PATH; ?>publish_x.png" border="0" alt="<?php echo _JLMS_DELETE; ?>"></a>';
	cell8.innerHTML = '';
	cell8.width = '40px';
	<?php
	} else {
	?>
	cell5.innerHTML = '';
	cell5.width = 'auto';
	cell6.innerHTML = '<a href="javascript: void(0);" onclick="javascript:Delete_tbl_row(this); return false;" title="<?php echo _JLMS_DELETE; ?>"><img src="<?php echo ADMIN_IMAGES_PATH; ?>publish_x.png" border="0" alt="<?php echo _JLMS_DELETE; ?>"></a>';
	cell7.innerHTML = '';
	cell7.width = '40px';
	<?php
	}
	?>
	row.appendChild(cell1);
	row.appendChild(cell2);
	row.appendChild(cell3);
	row.appendChild(cell4);
	
	<?php
	if($JLMS_CONFIG->get('use_global_groups', 1)){
	?>
	row.appendChild(cell5);
	row.appendChild(cell6);
	row.appendChild(cell7);
	row.appendChild(cell8);
	<?php
	} else {
	?>
	row.appendChild(cell5);
	row.appendChild(cell6);
	row.appendChild(cell7);
	<?php
	}
	?>
	ReAnalize_tbl_Rows(tbl_elem.rows.length - 2, tbl_id);
}

<?php
if($JLMS_CONFIG->get('use_global_groups', 1)){
?>
//extra BrustersIceCream
function Add_store_tbl_field(tbl_id, is_group){
	
	var zzz = getObj('jlms_group_store_ids').selectedIndex;
	var new_grp_un = getObj('jlms_group_store_ids').options[zzz].text;
	var new_grp_id = getObj('jlms_group_store_ids').options[zzz].value;
	if (new_grp_id == '0' || new_grp_id == 0 || new_grp_id == '') {
		alert("<?php echo _JLMS_CEO_SLCT_GR; ?>");return;
	}

	var tbl_elem = getObj(tbl_id);
	var row = tbl_elem.insertRow(tbl_elem.rows.length);
	
	var cell1 = document.createElement("td");
	var cell2 = document.createElement("td");
	var cell3 = document.createElement("td");
	var cell4 = document.createElement("td");
	var cell5 = document.createElement("td");

	var input_hidden = document.createElement("input");
		input_hidden.type = "hidden";
		input_hidden.name = 'jq_hid_store_grp_ids[]';
		input_hidden.value = new_grp_id;
		
		cell1.align = 'center';
		cell1.innerHTML = 0;
		cell2.innerHTML = new_grp_un;
		cell2.appendChild(input_hidden);
		cell3.innerHTML = '';
		cell3.width = 'auto';
		cell4.innerHTML = '<a href="javascript: void(0);" onclick="javascript:Delete_tbl_row(this); return false;" title="<?php echo _JLMS_DELETE; ?>"><img src="<?php echo ADMIN_IMAGES_PATH; ?>publish_x.png" border="0" alt="<?php echo _JLMS_DELETE; ?>"></a>';
		cell5.innerHTML = '';
		cell5.width = '40px';
	
	row.appendChild(cell1);
	row.appendChild(cell2);
	row.appendChild(cell3);
	row.appendChild(cell4);
	row.appendChild(cell5);
	
	ReAnalize_tbl_Rows(tbl_elem.rows.length - 2, tbl_id);
}
<?php
}
?>

function jlms_changeUserSelect(c_e) {
	var sel_value = c_e.options[c_e.selectedIndex].value;
	var sel1 = c_e.form.parent_name;
	for (var i = 0; i < sel1.options.length; i++) {
		if (sel1.options[i].value == sel_value) {
			sel1.options[i].selected = true;
		}
	}
	var sel2 = c_e.form.parent_email;
	for (var i = 0; i < sel2.options.length; i++) {
		if (sel2.options[i].value == sel_value) {
			sel2.options[i].selected = true;
		}
	}
	var sel3 = c_e.form.parent_id;
	for (var i = 0; i < sel3.options.length; i++) {
		if (sel3.options[i].value == sel_value) {
			sel3.options[i].selected = true;
		}
	}
}
function jlms_changeUserSelect_stu(c_e) {
	var sel_value = c_e.options[c_e.selectedIndex].value;
	var sel1 = c_e.form.user_name;
	for (var i = 0; i < sel1.options.length; i++) {
		if (sel1.options[i].value == sel_value) {
			sel1.options[i].selected = true;
		}
	}
	var sel2 = c_e.form.user_email;
	for (var i = 0; i < sel2.options.length; i++) {
		if (sel2.options[i].value == sel_value) {
			sel2.options[i].selected = true;
		}
	}
	var sel3 = c_e.form.user_id;
	for (var i = 0; i < sel3.options.length; i++) {
		if (sel3.options[i].value == sel_value) {
			sel3.options[i].selected = true;
		}
	}
}
function submitbutton(pressbutton) {
	var form = document.adminForm;

	if (pressbutton == 'cancel_parent') {
		submitform( pressbutton );
		return;
	}
	
	if (form.parent_id.value == '' || form.parent_id.value == 0){
		alert( '<?php echo _JLMS_CEO_MSG_SELECT_PARENT; ?>' );
	} else if(form['jq_hid_fields_grp_ids[]'] == undefined && form['jq_hid_fields_ids[]'] == undefined && form['jq_hid_store_grp_ids[]'] == undefined){
		alert( '<?php echo _JLMS_CEO_SELECT_LEARNER; ?>' );
	} else {
		submitform( pressbutton );
	}
}

<?php if( JLMS_J16version() ) { ?>
Joomla.submitbutton = submitbutton;
<?php } ?>
//-->
</script>

<form action="index.php" method="post" name="adminForm">
<table width="100%" >
	<tr>
		<td valign="top" width="220">
		<div>
			<?php echo joomla_lms_adm_html::JLMS_menu();?>
		</div>
		</td>
		<td valign="top">	
		<?php if (!class_exists('JToolBarHelper')) { ?>
		<table class="adminheading">
		<tr>
			<th class="user">
			<?php echo _JOOMLMS_COMP_NAME;?>:
			<small>
			<?php echo $row->id ? _JLMS_CEO_EDIT_PARENT : _JLMS_CEO_NEW_PARENT;?>
			</small>
			</th>
		</tr>
		</table>
		<?php } ?>	
		<table width="100%" border="0">
			<tr>
				<td valign="top">
					<table width="100%" >
						<tr>
							<th colspan="2"><?php echo _JLMS_CEO_DETAILS; ?></th>
						<tr>
						<tr>
							<td align="right" width="20%"><?php echo _JLMS_SELECT_PARENT;  ?>:</td>
							<td><?php echo $lists['parents'];?><br /><?php echo $lists['parents_names'];?><br /><?php echo $lists['parents_emails'];?></td>
						</tr>
						<tr>
							<td colspan="2">
								<br />
								<table class="adminlist" id="qfld_tbl">
								<thead>
									<tr>
										<th width="20px" align="center">#</th>
										<th class="title" width="200px"><?php echo _JLMS_CEO_LEARNER_USERNAME; ?></th>
										<?php
										if($JLMS_CONFIG->get('use_global_groups', 1)){
										?>
										<th class="title" width="200px"><?php echo _JLMS_CEO_GROUP_NAME; ?></th>
										<?php
										}
										?>
										<th class="title" width="200px"><?php echo _JLMS_NAME; ?></th>
										<th class="title" width="200px"><?php echo _JLMS_EMAIL; ?></th>
										<th width="auto"></th>
										<th width="15px" align="center" class="title"></th>
										<th width="40px"></th>
									</tr>
								</thead>
								<?php
								$k = 0; $ii = 1; $ind_last = count($learners);
								foreach ($learners as $frow) { ?>
									<tr class="<?php echo "row$k"; ?>">
										<td align="center"><?php echo $ii;?></td>
										<td align="left">
											<?php echo stripslashes($frow->username);?>
											<input type="hidden" name="jq_hid_fields_ids[]" value="<?php echo $frow->user_id;?>" />
										</td>
										<?php
										if($JLMS_CONFIG->get('use_global_groups', 1)){
										?>
										<td align="left">
											<?php
											if(isset($frow->ug_name) && strlen($frow->ug_name)){
												echo stripslashes($frow->ug_name);
											}
											?>
										</td>
										<?php
										}
										?>
										<td align="left">
											<?php echo stripslashes($frow->name);?>
										</td>
										<td align="left">
											<?php echo stripslashes($frow->email);?>
										</td>
										<td width="auto"></td>
										<td><a href="javascript: void(0);" onclick="javascript:Delete_tbl_row(this); return false;" title="<?php echo _JLMS_DELETE; ?>"><img src="<?php echo ADMIN_IMAGES_PATH; ?>publish_x.png" border="0" alt="<?php echo _JLMS_DELETE; ?>"></a></td>
										<td></td>
									</tr>
								<?php
								$k = 1 - $k; $ii ++;
								 } ?>
								</table>
								<br />
								<br />
							</td>
						<tr>
						<?php
						if($JLMS_CONFIG->get('use_global_groups', 1)){
						?>
						<tr>
							<td align="right" width="20%"><?php echo _JLMS_SELECT_GROUP; ?>:</td>
							<td align="left" style="text-align:left ">
								<div style="text-align:left " align="left">
									<?php echo $lists['groups'];?>
									<br />
									<input type="button" name="add_new_field" style="width:70px " value="<?php echo _JLMS_ADD; ?>" onclick="javascript:Add_new_tbl_field('qfld_tbl', 1);" /></div>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<b><?php echo _JLMS_OR; ?></b>
							</td>
						</tr>
						<?php
						}
						?>
						<tr>
							<td align="right" width="20%"><?php echo _JLMS_SELECT_LEARNER; ?>:</td>
							<td align="left" style="text-align:left ">
								<div style="text-align:left " align="left">
									<?php echo $lists['users'];?>
									<br />
									<?php echo $lists['users_names'];?>
									<br />
									<?php echo $lists['users_emails'];?>
									<br />
									<input type="button" name="add_new_field" style="width:70px " value="Add" onclick="javascript:Add_new_tbl_field('qfld_tbl', 0);" /></div>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								&nbsp;
							</td>
						</tr>
						<?php
						if($JLMS_CONFIG->get('use_global_groups', 1)){
						?>
						<tr>
							<td colspan="2">
								<br />
								<table class="adminlist" id="store_tbl">
								<thead>
									<tr>
										<th width="20px" align="center">#</th>
										<th class="title" width="200px"><?php echo _JLMS_CEO_GROUP_NAME;  ?></th>
										<th width="auto"></th>
										<th width="15px" align="center" class="title"></th>
										<th width="40px"></th>
									</tr>
								</thead>
								<?php
								$k = 0; $ii = 1; $ind_last = count($stores);
								foreach ($stores as $store) { ?>
									<tr class="<?php echo "row$k"; ?>">
										<td align="center"><?php echo $ii;?></td>
										<td align="left">
											<?php
											if(isset($store->ug_name) && strlen($store->ug_name)){
												echo stripslashes($store->ug_name);
											}
											?>
											<input type="hidden" name="jq_hid_store_grp_ids[]" value="<?php echo $store->grp_id;?>" />
										</td>
										<td width="auto"></td>
										<td><a href="javascript: void(0);" onclick="javascript:Delete_tbl_row(this); return false;" title="Delete"><img src="<?php echo ADMIN_IMAGES_PATH; ?>publish_x.png" border="0" alt="<?php echo _JLMS_DELETE; ?>"></a></td>
										<td></td>
									</tr>
								<?php
								$k = 1 - $k; $ii ++;
								 } ?>
								</table>
								<br />
								<br />
							</td>
						</tr>
						<tr>
							<td align="right" width="20%">Select group:</td>
							<td align="left" style="text-align:left ">
								<div style="text-align:left " align="left">
									<?php echo $lists['groups_store'];?>
									<br />
									<input type="button" name="add_new_field" style="width:70px " value="<?php echo _JLMS_ADD; ?>" onclick="javascript:Add_store_tbl_field('store_tbl', 1);" /></div>
							</td>
						</tr>
						<?php
						}
						?>
						<tr>
							<td colspan="2">
								<?php echo _JLMS_CEO_MSG_CLICK_SAVE; ?>
							</td>
						</tr>
					</table>
					<br />
					
				</td>
			</tr>
		</table>	
	</td></tr></table>
	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="view_switch" value="<?php echo $lists['view_switch'];?>" />	
	</form>
		<?php
	}
	##########################################################################
	###	--- ---   MESSAGE SYSTEM	 --- --- ###
	##########################################################################
	function JLMS_MailSupList($rows, $pageNav, $option)
	{
	?>
	<form action="index.php" method="post" name="adminForm">	
	<table width="100%" >
		<tr>
			<td valign="top" width="220">
			<div>
				<?php echo joomla_lms_adm_html::JLMS_menu();?>
			</div>
			</td>
			<td valign="top">			
			<?php if (!class_exists('JToolBarHelper')) { ?>
			<table class="adminheading">
			<tr>
				<th class="user">
				<?php echo _JOOMLMS_COMP_NAME;?>:
				<small>
				<?php echo _JLMS_MAIL_ADDRESS_BOOK;?>
				</small>
				</th>
			</tr>
			</table>
			<?php } ?>			
			<table class="adminlist">
					<thead>
						<tr>
							<th width="20">#</th>
							<th width="20" class="title"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>
							<th class="title"><?php echo _JLMS_NAME;?></th>
							<th class="title" align="center"><?php echo _JLMS_EMAIL;?></th>

						</tr>
					</thead>
					<tfoot>
						<tr>
							<td colspan="5">
							<?php echo $pageNav->getListFooter();?>
							</td>
						</tr>
					</tfoot>
					<tbody>
					<?php
					$k = 0;
					for ($i=0, $n=count($rows); $i < $n; $i++) {
						$row = $rows[$i];

						//$link 	= 'index.php?option=com_joomla_lms&task=editA_p&hidemainmenu=1&id='. $row->id;


						$row->editor = '';
						$checked = '<input type="checkbox" id="cb'.$i.'" name="cid[]" value="'.$row->id.'" onclick="isChecked(this.checked);" />';

						?>
						<tr class="<?php echo "row$k"; ?>">
							<td width="20"><?php echo $pageNav->rowNumber( $i ); ?></td>
							<td width="20"><?php echo $checked; ?></td>
							<td align="left">


								<?php echo $row->pm_name; ?>


							</td>
							<td align="left">
								<?php echo $row->pm_email?>
							</td>
						</tr>
				<?php $k = 1 - $k;
					}?>
					</tbody>
				</table>				
			</td>
		</tr>
	</table>
	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	<input type="hidden" name="task" value="mailsup_list" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="hidemainmenu" value="0" />	
	</form>
	<?php
	}
	function JLMS_MailSupEdit($row, $option)
	{
	?>
	<form action="index.php" method="post" name="adminForm">	
	<table width="100%" >
		<tr>
			<td valign="top" width="220">
			<div>
				<?php echo joomla_lms_adm_html::JLMS_menu();?>
			</div>
			</td>
			<td valign="top">		
			<?php if (!class_exists('JToolBarHelper')) { ?>
			<table class="adminheading">
			<tr>
				<th class="user">
				<?php echo _JOOMLMS_COMP_NAME;?>:
				<small>
				<?php echo isset($row->id) ? _JLMS_MAIL_EDIT_CONTACT : _JLMS_MAIL_NEW_CONTACT;?>
				</small>
				</th>
			</tr>
			</table>
			<?php } ?>		
			<table class="adminlist">
					<thead>
						<tr>
							<th class="title" colspan="2"><?php echo _JLMS_MAIL_ENTER_VALUES; ?></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td width="200"><?php echo _JLMS_NAME; ?>:</td>
							<td><input class="text_area" style="width:350px" type="text" maxlength="100" name="pm_name" value="<?php echo isset($row->pm_name)?str_replace('"','&quot;',$row->pm_name):'';?>" /></td>
						</tr>
						<tr>
							<td width="200"><?php echo _JLMS_EMAIL; ?>:</td>
							<td><input class="text_area" style="width:350px" type="text" maxlength="100" name="pm_email" value="<?php echo isset($row->pm_email)?str_replace('"','&quot;',$row->pm_email):'';?>" /></td>
						</tr>
					</tbody>
			</table>		
		</td>
		</tr>
	</table>
	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="hidemainmenu" value="0" />
	<input type="hidden" name="sm_id" value="<?php echo isset($row->id)?$row->id:0;?>">	
	</form>
	<?php
	}
	function JLMS_MailSupConf($mess_enotify, $mess_alearn, $mail_title, $mail_body, $option)
	{
	?>
	<form action="index.php" method="post" name="adminForm">
	<div class="current">	
	<table width="100%" >
		<tr>
			<td valign="top" width="220">
			<div>
				<?php echo joomla_lms_adm_html::JLMS_menu();?>
			</div>
			</td>
			<td valign="top">			
			<?php if (!class_exists('JToolBarHelper')) { ?>
			<table class="adminheading">			
			<tr>
				<th class="user">
				<?php echo _JOOMLMS_COMP_NAME;?>:
				<small>
				<?php echo _JLMS_MAIL_CONFIG;?>
				</small>
				</th>
			</tr>
			</table>
			<?php } ?>			
			<table class="adminlist">
					<thead>
						<tr>
							<th class="title" colspan="2"><?php echo _JLMS_MAIL_BOX_SETTGS; ?></th>
						</tr>
					</thead>
					<tbody>
						<!--<tr>
							<td width="200">Allow learners sent messages to learners:</td>
							<td><input style="text_area" type="radio" name="mess_alearn" value="0" <?php #if(!$mess_alearn) echo "checked"?>/>No
								<input style="text_area" type="radio" name="mess_alearn" value="1" <?php #if($mess_alearn) echo "checked"?>/>Yes
							</td>
						</tr>-->
						<tr>
							<td width="200"><?php echo _JLMS_MAIL_SEND_EML_NOT; ?>:</td>
							<td>							
							<fieldset class="radio">
							<input id="lms_mess_enotify0" style="text_area" type="radio" name="mess_enotify" value="0" <?php if(!$mess_enotify) echo "checked=\"checked\""?>/>
							<label for="lms_mess_enotify0"><?php echo _JLMS_NO; ?></label>
							<input id="lms_mess_enotify1" style="text_area" type="radio" name="mess_enotify" value="1" <?php if($mess_enotify) echo "checked=\"checked\""; ?>/>
							<label for="lms_mess_enotify1" ><?php echo _JLMS_YES; ?></label>
							</fieldset>							
							</td>							
						</tr>
						
					</tbody>
			</table>
			<table class="adminlist">
					<thead>
						<tr>

							<th class="title" colspan="2"><?php echo _JLMS_MAIL_EML_NOT_SETT; ?></th>

						</tr>
					</thead>

					<tbody>
						<tr>
							<td width="200"><?php echo _JLMS_MAIL_EML_SUBJ; ?>:</td>
							<?php
							if(!isset($mail_title) || !$mail_title){
								$mail_title = _JLMS_MAIL_EML_TITLE;
							}
							?>
							<td><input class="text_area" type="text" style="width:350px;" name="mail_title" value="<?php echo str_replace('"', '&quot;',$mail_title);?>"/>
							</td>
						</tr>
						<tr>
							<td width="200"><?php echo _JLMS_MAIL_EML_TEXT; ?>:</td>
							<?php
							if(!isset($mail_body) || !$mail_body){
$mail_body = _JLMS_MAIL_EML_BODY;
							}
							?>
							<td><textarea class="text_area" name="mail_body" style="width:350px;" rows="10"><?php echo $mail_body;?></textarea></td>
						</tr>
					</tbody>
			</table>		
		</td>
		</tr>
	</table>
	</div>
	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="hidemainmenu" value="0" />	
	</form>
	<?php
	}


	##########################################################################
	###	--- ---   VSYAKI TRASH	 --- --- ###
	##########################################################################

	function newCourseInstructions ($option) {
	 ?>
<table width="100%">
	<tr>
		<td valign="top" width="220">
		<div>
			<?php joomla_lms_adm_html::JLMS_menu();?>
		</div>
		</td>
		<td valign="top">	
		<?php if (!class_exists('JToolBarHelper')) { ?>
			<table class="adminheading">
			<tr>
				<th>
				<?php echo _JOOMLMS_COMP_NAME?>: <small><?php echo _JLMS_CRSS_HW_CRT_NEW_CRS; ?></small>
				</th>
			</tr>
			</table>
		<?php } ?>
			<table width="100%" border="0">
			<tr>
				<td valign="top">
					<table class="adminlist">
					<tr><td>
					<div style="text-align:left; padding:5px; font-family: verdana, arial, sans-serif; font-size: 9pt;">
					<?php echo _JLMS_CRSS_CRT_NEW_INSTR; ?>
					</div>
					</td></tr></table>
				</td></tr></table>	
		</td></tr></table>
		<form action="index.php" method="post" name="adminForm">
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="task" value="cancel_course" />
		</form>
	<?php	
	}

	function View_AboutPage($expired_str, $users_str) {
		global $lms_version, $lms_version_build, $lms_license_edition_str, $license_lms_branding_free;
		if ($license_lms_branding_free) {
			$bfree_str = '<span style = "font-weight:bold; color:green">'._JLMS_YES.'</span>';
		} else {
			$bfree_str = '<span style = "font-weight:bold; color:red">'._JLMS_NO.'</span>';
		}
	 ?>
<table width="100%">
	<tr>
		<td valign="top" width="220">
		<div>
			<?php joomla_lms_adm_html::JLMS_menu();?>
		</div>
		</td>
		<td valign="top">	
		<?php if (!class_exists('JToolBarHelper')) { ?>
			<table class="adminheading">
			<tr>
				<th>
				<?php echo _JOOMLMS_COMP_NAME.' '.$lms_version;?>
				</th>
			</tr>
			</table>
		<?php } else {
			JToolBarHelper::title( $lms_license_edition_str );
		}?>
			<table width="100%" border="0">
				<tr>
					<td valign="top">
				<table border="0" width="100%" style="background-color: #F7F8F9; border: solid 1px #d5d5d5; width: 100%; padding: 10px; border-collapse: collapse;" cellpadding="4" cellspacing="4">
					<tr>
						<th class="cpanel" style="text-align:left; font-size:14px; font-weight:400; line-height:18px; border: solid 1px #d5d5d5; " colspan="2"><strong><?php echo $lms_license_edition_str;?></strong></th></td>
					</tr>
					<tr>
						<td bgcolor="#FFFFFF" colspan="2" style="border: solid 1px #d5d5d5;"><br />
							<div style="width=100%" align="left">
							<a target="_blank" href="http://www.joomlalms.com"><img src="<?php echo JURI::root();?>components/com_joomla_lms/lms_images/logo_lms.png" alt="JoomlaLMS logo" title="JoomlaLMS" border="0"/></a>
							</div>
						</td>
					</tr>
					<tr>
						<td width="120" bgcolor="#FFFFFF" align="left" style="border: solid 1px #d5d5d5;"><?php echo _JLMS_ABOUT_INST_VER; ?>:</td>
						<td bgcolor="#FFFFFF" align="left" style="border: solid 1px #d5d5d5;"> &nbsp;<b><?php echo $lms_version;?></b><?php echo $lms_version_build ? ('&nbsp;&nbsp;build <strong>'.$lms_version_build.'</strong>') :'';?></td>
					 </tr>
					 <tr>
						<td bgcolor="#FFFFFF" align="left" style="border: solid 1px #d5d5d5;"><?php echo _JLMS_ABOUT_LATEST_VER; ?>:</td>
						<td align="left" style="border: solid 1px #d5d5d5;"><?php echo jlms_update_checker();?></td>
					 </tr>
					 <tr>
						<td bgcolor="#FFFFFF" style="border: solid 1px #d5d5d5;" align="left"><?php echo _JLMS_ABOUT_LICENSE_EXP_ON; ?>:</td>
						<td bgcolor="#FFFFFF" style="border: solid 1px #d5d5d5;" align="left">&nbsp;<?php echo $expired_str;?></td>
					 </tr>
					 <tr>
						<td bgcolor="#FFFFFF" style="border: solid 1px #d5d5d5;" align="left"><?php echo _JLMS_ABOUT_LICENSE_USERS; ?>:</td>
						<td bgcolor="#FFFFFF" style="border: solid 1px #d5d5d5;" align="left">&nbsp;<?php echo $users_str;?></td>
					 </tr>
					 <tr>
						<td bgcolor="#FFFFFF" style="border: solid 1px #d5d5d5;" align="left"><?php echo _JLMS_ABOUT_BRANDING_FREE; ?>:</td>
						<td bgcolor="#FFFFFF" style="border: solid 1px #d5d5d5;" align="left">&nbsp;<?php echo $bfree_str;?></td>
					 </tr>
					 <tr>
						<td valign="top" bgcolor="#FFFFFF" style="border: solid 1px #d5d5d5;" align="left">Support:</td>
						<td bgcolor="#FFFFFF" style="border: solid 1px #d5d5d5;" align="left">
							Skype ID: <a href="skype:joomlalms?chat">joomlalms</a> or <a href="skype:joomlalms?chat">joomlalms_consultant</a><br />
							Email: <a href="mailto:support@joomlalms.com">support@joomlalms.com</a><br />
							Helpdesk: <a target="_blank" href="http://www.joomlalms.com/helpdesk/ticket_submit.php">http://www.joomlalms.com/helpdesk/ticket_submit.php</a><br />
							Forum: <a target="_blank" href="http://www.joomlalms.com/support/forum.html">www.joomlalms.com/support/forum.html</a>
						</td>
					</tr>
				</table>
					</td>
				</tr>
			</table>		
		</td>
	</tr>
</table>
	<?php
	}

	function View_SupportPage()
	{
	 ?>
<table width="100%">
	<tr>
		<td valign="top" width="220">
		<div>
			<?php joomla_lms_adm_html::JLMS_menu();?>
		</div>
		</td>
		<td valign="top">		
		<?php if (!class_exists('JToolBarHelper')) { ?>
			<table class="adminheading">
			<tr>
				<th>
				<?php echo _JOOMLMS_COMP_NAME?> <?php echo _JLMS_SUPP;  ?>
				</th>
			</tr>
			</table>
		<?php } ?>
			<table width="100%" border="0">
			<tr>
				<td valign="top">
					<fieldset  style="padding: 0;">
					<table class="adminlist" style="border:none; border-spacing:0" width="100%">
					<tr><td>
					
					<div style="text-align:left; padding:5px; font-family: verdana, arial, sans-serif; font-size: 9pt;">
					<?php echo _JLMS_SUPP_IF_HAVE_QUESTS; ?>
					</div>
					
					</td></tr></table></fieldset>
				</td></tr></table>		
		</td></tr></table>
	<?php
	}


	//plugins implementation by TPETb
	function showPluginsList( &$rows, &$pageNav, $option, $msg ) {
		global $my;
				
		JHTML::_('behavior.tooltip');
		if ($msg) echo '<div class="message"><br /><br />'.$msg.'<br /></div>';
				
		?>
		<script language="javascript" type="text/javascript">
		<!--
			function submitbutton2(pressbutton) {
				var form = document.installForm;
		
				// do field validation
				if (form.install_package.value == ""){
					alert( "<?php echo JText::_( 'Please select a directory', true ); ?>" );
				} else {				
					form.submit();
				}
			}	
			
			<?php if( JLMS_J16version() ) { ?>
			Joomla.submitbutton = submitbutton;
			<?php } ?>
		//-->
		</script>		
		<table width="100%">
		<tr>
			<td valign="top" width="220">
			<div>
				<?php echo joomla_lms_adm_html::JLMS_menu();?>
			</div>
			</td>
			<td valign="top">		
			<?php if (!class_exists('JToolBarHelper')) { ?>
				<table class="adminheading">
				<tr>
					<th class="sections">
					<?php echo _JOOMLMS_COMP_NAME;?>: <small><?php echo _JLMS_PLGS_LIST; ?></small>
					</th>
					<td align="right"></td>
				</tr>
				</table>
			<?php } ?>
				<form action="index.php" method="post" name="adminForm">				
				<table class="adminlist">
					<thead>
						<tr>
							<th width="20">#</th>
							<th width="20" class="title"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>
							<th class="title"><?php echo _JLMS_NAME; ?></th>
							<th class="title"><?php echo _JLMS_SHORT_DESC; ?></th>								
							<th class="title"><?php echo _JLMS_ORDER; ?></th>
							<th class="title">
								<a href="javascript:saveorder(<?php echo ( count( $rows ) - 1 ); ?>, 'save_plugins_order')" title="<?php echo _JLMS_SAVE_ORDER; ?>"><img src="/administrator/images/filesave.png" alt="<?php echo _JLMS_SAVE_ORDER; ?>"></a>	
							</th>
							<th class="title" align="center" width="40"><?php echo _JLMS_PUBLISHED; ?></th>
							<th class="title" align="center" width="40"><?php echo _JLMS_FILE; ?></th>
							<th class="title" align="center" width="40"><?php echo _JLMS_TYPE; ?></th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<td colspan="9">
							<?php echo $pageNav->getListFooter();?>
							</td>
						</tr>
					</tfoot>
					<tbody>
					<?php
					$k = 0;
					for ($i=0, $n=count($rows); $i < $n; $i++) {
						$row = $rows[$i];

						$link 	= 'index.php?option=com_joomla_lms&amp;task=editA_plugin&amp;hidemainmenu=0&amp;id='. $row->id;

						$img 	= $row->published ? 'tick.png' : 'publish_x.png';
						$task 	= $row->published ? 'unpublish' : 'publish';
						$alt 	= $row->published ? _JLMS_PUBLISHED : _JLMS_UNPUBLISHED;

						$row->editor = '';
						$checked 	= mosCommonHTML::CheckedOutProcessing( $row, $i );

						?>
						<tr class="<?php echo "row$k"; ?>">
							<td width="20"><?php echo $pageNav->rowNumber( $i ); ?></td>
							<td width="20"><?php echo $checked; ?></td>
							<td align="left">
							<?php
							if ( $row->checked_out && ( $row->checked_out != $my->id ) ) {
								echo $row->name;
							} else {
							?>
								<a href="<?php echo $link; ?>" title="<?php echo _JLMS_PLGS_EDIT_PLG; ?>" > <?php echo $row->name; ?></a>
								<?php
							}
							?>
							</td>
							<td align="left"><?php echo $row->short_description; ?></td>
							<td class="order" colspan="2">
								<span><?php echo $pageNav->orderUpIcon( $i, ($row->folder == @$rows[$i-1]->folder), 'pluginsorderup', 'Move Up', 'ordering' ); ?></span>
								<span><?php echo $pageNav->orderDownIcon( $i, $n, ($row->folder == @$rows[$i+1]->folder),'pluginsorderdown', 'Move Down', 'ordering' ); ?></span>
								<?php $disabled = ''; ?>
								<input type="text" name="order[]" size="5" value="<?php echo $row->ordering; ?>" <?php echo $disabled ?> class="text_area" style="text-align: center" />
							</td>
							<td align="left" width="40">
								<a href="javascript: void(0);" onclick="return listItemTask('cb<?php echo $i;?>','<?php echo $task.'_plugin';?>')">
								<img src="<?php echo ADMIN_IMAGES_PATH.$img;?>" border="0" alt="<?php echo $alt; ?>" />
								</a>
							</td>
							<td align="left">
								<?php echo $row->element.".php"; ?>
							</td>
							<td align="left">
								<?php echo $row->folder; ?>
							</td>
						</tr>
				<?php $k = 1 - $k;
					}?>
					</tbody>
					</table>
					<input type="hidden" name="option" value="<?php echo $option; ?>" />
					<input type="hidden" name="task" value="pluginslist" />
					<input type="hidden" name="boxchecked" value="0" />
					<input type="hidden" name="hidemainmenu" value="0" />
					<?php echo JHTML::_( 'form.token' ); ?>					
					</form>
					<form enctype="multipart/form-data" action="index.php" method="post" name="installForm">				
						<table >
						<tr>
							<th colspan="2"><?php echo JText::_( 'Upload Package File' ); ?></th>
						</tr>
						<tr>
							<td width="120">
								<label for="install_package"><?php echo JText::_( 'Package File' ); ?>:</label>
							</td>
							<td>
								<input class="input_box" id="install_package" name="install_package" type="file" size="57" />
								<input class="button" type="button" value="<?php echo JText::_( 'Upload File' ); ?> &amp; <?php echo JText::_( 'Install' ); ?>" onclick="submitbutton2()" />
							</td>
						</tr>
						</table>
						<input type="hidden" name="type" value="" />			
						<input type="hidden" name="task" value="install_plugin" />
						<input type="hidden" name="option" value="com_joomla_lms" />
						<?php echo JHTML::_( 'form.token' ); ?>					
					</form>
				</td>
			</tr>			
		</table>							
	<?php
	}

	function editPlugin( &$row, &$lists, &$params, $option ) {

		$row->nameA = '';
		if ( $row->id ) {
			$row->nameA = '<small><small>[ '. $row->name .' ]</small></small>';
		}
		
		JHTML::_('behavior.tooltip');	
		?>
		<div id="overDiv" style="position:absolute; visibility:hidden; z-index:10000;"></div>
		<script language="javascript" type="text/javascript">
		function submitbutton(pressbutton) {
			if (pressbutton == "cancel") {
				submitform(pressbutton);
				return;
			}
			// validation
			var form = document.adminForm;
			if (form.name.value == "") {
				alert( "<?php echo _JLMS_PLGS_MSG_PL_MU_HAVE_N; ?>" );
			} else if (form.element.value == "") {
				alert( "<?php echo _JLMS_PLGS_MSG_PL_MU_HAVE_FN; ?>" );
			} else {
				submitform(pressbutton);
			}
		}
		
		<?php if( JLMS_J16version() ) { ?>
		Joomla.submitbutton = submitbutton;
		<?php } ?>
		</script>		
		<form action="index.php" method="post" name="adminForm">		
<table width="100%">
<tr>
	<td valign="top" width="220">
		<div>
			<?php echo joomla_lms_adm_html::JLMS_menu();?>
		</div>
	</td>
	<td valign="top">
	<div class="width-100">
	<fieldset class="adminform">
	<?php if (!class_exists('JToolBarHelper')) { ?>
		<table class="adminheading">
		<tr>
			<th class="mambots">
			JLMS Plugin:
			<small>
			<?php echo $row->id ? _JLMS_EDIT : _JLMS_NEW;?>
			</small>
			<?php echo $row->nameA; ?>
			</th>
		</tr>
		</table>
	<?php } ?>		
		<table cellspacing="0" cellpadding="0" width="100%">
		<tr>
			<td colspan="2">
				<table >
					<tr>
						<th colspan="2">
						<?php echo _JLMS_PLGS_PLG_DESC; ?>
						</th>
					<tr>
					<tr>
						<td align="left"><?php echo $row->description; ?></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr valign="top">
			<td width="60%" valign="top">
				<table >
				<tr>
					<th colspan="2">
					<?php echo _JLMS_PLGS_PLG_DETS; ?>
					</th>
				<tr>
				<tr>
					<td width="100" align="left">
					<?php echo _JLMS_NAME; ?>:
					</td>
					<td>
					<input class="text_area" type="text" name="name" size="35" value="<?php echo str_replace('"', '&quot;', $row->name); ?>" />
					</td>
				</tr>
				<tr>
					<td valign="top" align="left">
					<?php echo _JLMS_FOLDER; ?>:
					</td>
					<td>
					<?php echo $lists['folder']; ?>
					</td>
				</tr>
				<tr>
					<td valign="top" align="left">
					<?php echo _JLMS_PLGS_PLG_FILE; ?>:
					</td>
					<td>
					<input class="text_area" type="text" name="element" size="35" value="<?php echo $row->element; ?>" readonly="readonly" />.php
					</td>
				</tr>
				<tr style="display:none">
					<td valign="top" align="left">
					<?php echo _JLMS_PLGS_PLG_ORDER; ?>:
					</td>
					<td>
					<?php echo $lists['ordering']; ?>
					</td>
				</tr>
				<tr>
					<td valign="top">
					<?php echo _JLMS_PUBLISHED; ?>:
					</td>
					<td>
					<fieldset class="radio">
					<?php echo $lists['published']; ?>
					</fieldset>
					</td>
				</tr>
				<tr>
					<td valign="top" colspan="2">&nbsp;

					</td>
				</tr>
				</table>
			</td>
			<td width="40%">
				<table >
				<tr>
					<th colspan="2">
					<?php echo _JLMS_PARAMETERS; ?>:
					</th>
				<tr>
				<tr>
					<td>
					<?php
					if ( $row->id ) {
						echo $params->render();
					} else {
						echo '<i>'._JLMS_PLGS_NO_PARAMS.'</i>';
					}
					?>
					</td>
				</tr>
				</table>
			</td>
		</tr>
		</table>
		</fieldset>
		</div>
	</td>
</tr>
</table>

		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="id" value="<?php echo $row->id; ?>" />		
		<input type="hidden" name="task" value="" />		
		</form>		
		<?php
	}

	function showWaitingLists ($rows, $pageNav, $lists, $option) {
		JHTML::_('behavior.tooltip');
		?>
<form action="index.php" method="post" name="adminForm">
<table width="100%" >
	<tr>
		<td valign="top" width="220">
		<div>
			<?php echo joomla_lms_adm_html::JLMS_menu();?>
		</div>
		</td>
		<td valign="top" align="right">	
	<?php if (!class_exists('JToolBarHelper')) { ?>
		<table class="adminheading" cellpadding="0" cellspacing="0" border="0" style="width: 30%;">
		<tr>
			<th class="categories">
			<?php echo _JOOMLMS_COMP_NAME;?>: <small><?php echo _JLMS_WAIT_LIST; ?></small>
			</th>
			<td width="right">
				<table class="adminlist" ><tr class="row1">
					<td nowrap="nowrap" style="padding:2px 10px 2px 10px;"><?php echo _JLMS_FILTER; ?>:&nbsp;&nbsp;<?php echo $lists['jlms_courses'];?></td>
				</tr></table>
			</td>
		</tr>
		</table>
		<?php } else { ?>
		<table cellpadding="0" cellspacing="0" border="0" class="adminlist" style="width: 30%;">
		<tr class="row1">			
			<td nowrap="nowrap" align="right">
				<?php echo $lists['jlms_courses'];?>
			</td>
		</tr>
		</table>
		<?php } ?>		
		<table width="100%" border="0">
			<tr>
				<td valign="top">
					<table class="adminlist">
					<thead>
					<tr>
						<th width="20">#</th>
						<th width="20" class="title"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>
						<th width="150" class="title"><?php echo _JLMS_USER_NAME; ?></th>
						<th width="150" class="title"><?php echo _JLMS_COURSE_NAME; ?></th>
						<th width="72" class="title" colspan="4"><?php echo _JLMS_CONTROLS; ?></th>
						<th class="title" />
					</tr>
					</thead>
					<tfoot>
						<tr>
							<td colspan="9">
							<?php echo $pageNav->getListFooter(); ?>
							</td>
						</tr>
					</tfoot>
					<?php
					$k = 0;
					for ($i=0, $n=count($rows); $i < $n; $i++) {
						$row = $rows[$i];
						//						$link 	= 'index.php?option=com_joomla_lms&task=view_class_users&class_id='. $row->id;
						$checked = mosHTML::idBox( $i, $row->id);?>
						<tr class="<?php echo "row$k"; ?>">
							<td><?php echo $pageNav->rowNumber( $i ); ?></td>
							<td><?php echo $checked; ?></td>
							<td align="left">
								<?php echo $row->user_name;?>
							</td>
							<td align="left">
								<?php echo $row->course_name;?>
							</td>
							<td>
							<?php
							if ($row->allow_up) {
									?>
									<a href="index.php?option=<?php echo $option;?>&amp;task=orderup_waiting_list&amp;id=<?php echo $row->id;?>">
										<img src="<?php echo ADMIN_IMAGES_PATH;?>uparrow.png" border="0" />
									</a>
									<?php
							}
								?>
							</td>
							<td>
								<?php
								if ($row->allow_down) {
									?>
									<a href="index.php?option=<?php echo $option;?>&amp;task=orderdown_waiting_list&amp;id=<?php echo $row->id;?>">
										<img src="<?php echo ADMIN_IMAGES_PATH; ?>downarrow.png" border="0" />
									</a>
									<?php
								}
								?>
							</td>
							<td>
								<a href="index.php?option=<?php echo $option;?>&amp;task=add_from_waiting_list&amp;cid[]=<?php echo $row->id;?>" title="<?php echo _JLMS_WAIT_ADV_USR_ATT; ?>">
									<img src="<?php echo ADMIN_IMAGES_PATH; ?>tick.png" border="0" alt="<?php echo _JLMS_WAIT_ADV_USR_ATT; ?>" />
								</a>
							</td>
							<td>
								<a href="index.php?option=<?php echo $option;?>&amp;task=remove_from_waiting_list&amp;cid[]=<?php echo $row->id;?>" title="Remove user from waiting list">
									<img src="<?php echo ADMIN_IMAGES_PATH; ?>publish_x.png" border="0" alt="<?php echo _JLMS_WAIT_REM_USR_F_LIST; ?>" />
								</a>
							</td>
							<td />
						</tr>
						<?php
						$k = 1 - $k;
					}?>
					</table>
				</td>
			</tr>
		</table>	
	</td></tr></table>
	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	<input type="hidden" name="task" value="show_waiting_lists" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="hidemainmenu" value="0" />	
	</form>
		<?php
	}
	
	function FLMS_vListCategories($option, $rows, $pageNav, $levellist){
		global $JLMS_CONFIG;
	
		JHTML::_('behavior.tooltip');
		?>
		<form action="index.php" method="post" name="adminForm">		
		<table width="100%" >
			<tr>
				<td valign="top" width="220">
					<div>
						<?php echo joomla_lms_adm_html::JLMS_menu();?>
					</div>
				</td>
				<td valign="top">			
					<?php if (!class_exists('JToolBarHelper')) { ?>
					<table class="adminheading" cellpadding="0" cellspacing="0" border="0">
					<tr>
						<th class="categories">
						<?php echo _JOOMLMS_COMP_NAME;?>: <small><?php echo _JLMS_FLMS_CATS_MANG; ?></small>
						</th>
						<?php if ($JLMS_CONFIG->get('multicat_show_admin_levels', 0)) { ?>
						<td align="right">
							<table class="adminlist">
								<tr class="row1">
									<td nowrap="true">
										<?php echo _JLMS_OTH_MAX_LEVLS; ?>
									</td>
									<td>
										<?php echo $levellist;?>
									</td>
								</tr>
							</table>
						</td>
						<?php } ?>
					</tr>
					</table>
					<?php } else { ?>
					<div style="width: 100%;">
						<table  align="right" style="width: 10%;" class="adminlist">
							<tr class="row1">
								<td nowrap="nowrap" align="left" width="100%" style="padding:2px 10px 2px 10px;">
								<?php echo _JLMS_FLMS_MAX_LEVLS; ?>
								</td>
								<td nowrap="nowrap">
									<?php echo $levellist;?>
								</td>
							</tr>
						</table>
					</div>
					<div style="clear: both;"><!--x--></div>
					<?php
					}
					?>					
					<table class="adminlist">
						<thead>
							<tr>
								<th class="title" width="20">
									#
								</th>
								<th class="title" width="20">
									<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" />
								</th>
								<th class="title" width="40%">
									<?php echo _JLMS_NAME; ?>
								</th>
								<th class="title" width="auto"></th>
							</tr>
						</thead>
						<tfoot>
							<tr> 
								<td colspan="4">
									<?php echo $pageNav->getListFooter(); ?>
								</td>
							</tr>
						</tfoot>
						<tbody>
							<?php
							$k = 0;
							$i = 0;
							$n = count( $rows );
							foreach ($rows as $row) {
								$checked = mosHTML::idBox( $i, $row->id);
								$link = "index.php?option=$option&task=multicat_editA&id=".$row->id;
								?>
								<tr class="<?php echo "row$k"; ?>">
									<td>
									<?php echo $i + 1 + $pageNav->limitstart;?>
									</td>
									<td>
									<?php echo $checked; ?>
									</td>
									<td nowrap="nowrap">
									<a href="<?php echo $link;?>" title="<?php echo $row->name;?>">
									<?php
										echo $row->treename;
									?>
									</a>
									</td>
									<td width="auto"></td>
								</tr>
								<?php
								$k = 1 - $k;
								$i++;
							}
							?>
						</tbody>
					</table>					
				</td>
			</tr>
		</table>

			<input type="hidden" name="option" value="<?php echo $option; ?>" />
			<input type="hidden" name="task" value="multicat" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="hidemainmenu" value="0" />		
		</form>
		<?php
	}
	
	function FLMS_vEditCategories($menu, $lists, $rows, $option){
		global $JLMS_CONFIG;
		global $menuid;
		//global menuid variable required by toolbat to detect if new item created or an edit old one.
		$menuid = $menu->id;
		?>
		<div id="overDiv" style="position:absolute; visibility:hidden; z-index:10000;"></div>
		<script language="javascript" type="text/javascript">
		function submitbutton(pressbutton) {
			var form = document.adminForm;
			if (pressbutton == 'multicat') {
				submitform( pressbutton );
				return;
			}

			// do field validation
			if ((form.name.value) == ""){
				alert( "<?php echo _JLMS_FLMS_MSG_CAT_MU_HAVE_N; ?>" );
			} else {
				submitform( pressbutton );
			}
		}
		
		<?php if( JLMS_J16version() ) { ?>
		Joomla.submitbutton = submitbutton;
		<?php } ?>
		
		function view_fields(element, type){
			if(type == 1) {
				if(element.value == 0) {
					<?php if ($JLMS_CONFIG->get('use_global_groups', 1)) {?>
						if(document.getElementById('restricted1').checked == true) {
							document.getElementById('restricted_groups').disabled = false;
						} else {
							document.getElementById('restricted_groups').disabled = true;	
						}
						document.getElementById('restricted0').disabled = false;
						document.getElementById('restricted1').disabled = false;
					<?php }?>
					/*Lesson Type Mod*/
					var rows_ltype = document.getElementsByName('lesson_type');
					for(var i=0;i<rows_ltype.length;i++){
						rows_ltype[i].disabled = false;	
					}
				} else {
					<?php if ($JLMS_CONFIG->get('use_global_groups', 1)) {?>
						document.getElementById('restricted0').disabled = true;
						document.getElementById('restricted1').disabled = true;
						document.getElementById('restricted_groups').disabled = true;
					<?php }?>
					/*Lesson Type Mod*/
					var rows_ltype = document.getElementsByName('lesson_type');
					for(var i=0;i<rows_ltype.length;i++){
						rows_ltype[i].disabled = true;	
					}
				}
			} else {
				<?php if ($JLMS_CONFIG->get('use_global_groups', 1)) {?>
					if(element.value == 1) {
						document.getElementById('restricted_groups').disabled = false;
					}
					else {
						document.getElementById('restricted_groups').disabled = true;
						var restricted_groups = document.getElementById('restricted_groups');
						for(i=0;i<restricted_groups.options.length;i++){
							restricted_groups.options[i].selected = false;
						}
					}
				<?php }?>	
			}
		}
		</script>
		
		<form action="index.php" method="post" name="adminForm">		
			<table width="100%" >
				<tr>
					<td valign="top" width="220">
						<div>
							<?php echo joomla_lms_adm_html::JLMS_menu();?>
						</div>
					</td>
					<td valign="top">
					<div class="width-100">
					<fieldset class="adminform">
						<?php if (!class_exists('JToolBarHelper')) { ?>
						<table class="adminheading" cellpadding="0" cellspacing="0" border="0">
						<tr>
							<th class="categories">
							<?php echo _JOOMLMS_COMP_NAME;?>: <small><?php echo $menu->id ? _JLMS_FLMS_EDIT_CAT : _JLMS_FLMS_ADD_CAT;?></small>
							</th>
						</tr>
						</table>
						<?php } ?>					
							<table >
								<tr>
									<th colspan="2">
									<?php echo _JLMS_DETAILS; ?>
									</th>
								</tr>
								<tr>
									<td width="20%" align="right">
									<?php echo _JLMS_NAME; ?>:
									</td>
									<td width="80%">
									<input class="text_area" type="text" name="name" size="50" maxlength="150" value="<?php echo $menu->name; ?>" />
									</td>
								</tr>
								<?php if ($JLMS_CONFIG->get('multicat_show_admin_levels', 0)) { ?>
								<tr>
									<td align="right" valign="top">
									<?php echo _JLMS_FLMS_PARENT_ITEM; ?>:
									</td>
									<td>
									<?php echo $lists['parent']; ?>
									</td>
								</tr>
								<?php } 
								if($JLMS_CONFIG->get('flms_integration', 0)){
								?>
								<tr>
									<td width="20%" valign="top">
									<?php echo _JLMS_FLMS_LESSON_TYPE; ?>:
									</td>
									<td>
										<table cellpadding="0" cellspacing="0" border="0">
											<?php
											$title_lesson_type = array();
											$title_lesson_type[0] = 'theory';
											$title_lesson_type[1] = 'flight';
											$title_lesson_type[2] = 'other';
											for($i=1;$i<4;$i++){
												$checked = '';
												$disabled = '';
												if(!$menu->id || $menu->parent){
													$disabled = 'disabled="disabled"';
												}
												if($menu->lesson_type == $i){
													$checked = 'checked="checked"';	
												}
												
												?>
												<tr>
													<td>
														<input type="radio" name="lesson_type" value="<?php echo $i;?>" <?php echo $checked;?> <?php echo $disabled;?> />
													</td>
													<td>
														<?php echo ucfirst($title_lesson_type[$i-1]);?>
													</td>
												</tr>
												<?php	
											}
											?>
										</table>
									</td>
								</tr>
								<?php 
								}
								if ($JLMS_CONFIG->get('use_global_groups', 1)) {?>
								<tr>
									<td>
										<?php echo _JLMS_FLMS_RESTR_CAT;  ?>:
									</td>
									<td>
										<fieldset class="radio">
										<?php echo $lists['restricted_category'];?>
										</fieldset>								
									</td>
								</tr>	
								<tr>
									<td>
										<?php echo _JLMS_FLMS_RESTR_GRPS;  ?>:							
									</td>
									<td>
										<?php echo $lists['restricted_groups'];?>
									</td>
								</tr>				
								<?php } else { ?>
								<tr>
									<td colspan="2"><?php echo _JLMS_FLMS_MSG_USE_PERM; ?></td>
								</tr>
								<?php } ?>
							</table>
						</fieldset>
						</div>						
					</td>
				</tr>
			</table>
			
			<input type="hidden" name="option" value="<?php echo $option;?>" />
			<input type="hidden" name="id" value="<?php echo $menu->id; ?>" />
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="hidemainmenu" value="0" />			
		</form>		
		<?php
	}
	
	function FLMS_vCategoriesConfig($rows, $levellist, $option){
		?>
		<div id="overDiv" style="position:absolute; visibility:hidden; z-index:10000;"></div>		
		<form action="index.php" method="post" name="adminForm">		
		<table width="100%" >
			<tr>
				<td valign="top" width="220">
					<div>
						<?php echo joomla_lms_adm_html::JLMS_menu();?>
					</div>
				</td>
				<td valign="top">
					<div class="width-100">
					<fieldset class="adminform">
					<?php if (!class_exists('JToolBarHelper')) { ?>
					<table class="adminheading" cellpadding="0" cellspacing="0" border="0">
					<tr>
						<th class="config">
						<?php echo _JOOMLMS_COMP_NAME;?>: <small><?php echo _JLMS_FLMS_CONF; ?></small>
						</th>
						<td nowrap="true">
						<?php echo _JLMS_FLMS_MAX_LEVLS; ?>:
						</td>
						<td>
						<?php echo $levellist;?>
						</td>
					</tr>
					</table>
					<?php } ?>				
					<table width="100%">
					<tr valign="top">
						<td width="60%">
						<table >
							<!--<tr>
								<th>
								Title
								</th>
								<th>
								Categories
								</th>
							</tr>-->
							<?php
							$padding = 0;
							
							$k = 0;
							$i = 0;
							$n = count( $rows );
							foreach ($rows as $row) {
								$num = $i + 1;
							?>
							<tr class="<?php echo "row$k"; ?>">
								<td width="10%" align="right">
								Title Level <?php echo $num;?>
								</td>
								<td style="padding-left: <?php echo $padding;?>px;">
								<input type="text" size="40" name="titlecat_<?php echo $i;?>" value="<?php echo $row->cat_name;?>" />
								</td>
								<!--<td width="80%">
								<?php #echo $row->treename;?>
								</td>-->
							</tr>
							<?php
								$padding = $padding + 15;	
							
								$k = 1 - $k;
								$i++;
							}
							?>
						</table>
						</td>
					</tr>
					</table>
					</fieldset>
					</div>
				</td>
			</tr>
		</table>
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="task" value="multicat_config" />
		<input type="hidden" name="hidemainmenu" value="0" />		
		</form>		
		<?php
	}
	
	function JLMS_ListNotifications( & $rows, $option ) 
	{
	?>
		<form action="index.php" method="post" name="adminForm">		
		<table width="100%" >
			<tr>
				<td valign="top" width="220">
				<div>
					<?php echo joomla_lms_adm_html::JLMS_menu();?>
				</div>
				</td>
				<td valign="top">			
				<?php if (!class_exists('JToolBarHelper')) { ?>
				<table class="adminheading">
				<tr>
					<th class="user">
					<?php echo _JOOMLMS_COMP_NAME;?>:
					<small>
					<?php echo _JLMS_NOTS_EMAIL_NOTS; ?>
					</small>
					</th>
				</tr>
				</table>
				<?php } ?>			
				<table class="adminlist">
						<thead>
							<tr>																
								<th colspan="2" width="180" class="title"><?php echo _JLMS_NAME; ?></th>
								<th class="title"><?php echo _JLMS_DESCRIPTION; ?></th>
								<th width="20" class="title" align="center"><?php echo _JLMS_ENABLED; ?></th>	
							</tr>
						</thead>						
						<tbody>
						<?php
						foreach ( $rows AS $type => $notifications) {
							$k = 0;
							echo '<tr class="row'.$k.'">';
							echo '<td colspan="4"><b>';
							echo $type;
							echo '</b></td></tr>';							
							for ($i=0, $n=count($notifications); $i < $n; $i++) {
								$row = $notifications[$i];
								
								$link 	= 'index.php?option=com_joomla_lms&amp;task=edit_notification&amp;hidemainmenu=1&amp;id='. $row->id;			
								$task_d = $row->disabled?'enable_notification':'disable_notification';						
								$link_d 	= 'index.php?option=com_joomla_lms&amp;task='.$task_d.'&amp;id='.$row->id;	
								$checked = '<input type="checkbox" id="cb'.$i.'" name="cid[]" value="'.$row->id.'" onclick="isChecked(this.checked);" />';
		
								?>
								<tr class="<?php echo "row$k"; ?>">
								<?php if (isset($notifications[$i + 1])) { ?>
									<td align="center" width="16"><img src="<?php echo JURI::root()."components/com_joomla_lms/lms_images/treeview/sub1.png";?>" alt=' ' /></td>
								<?php } else { ?>
									<td align="center" width="16"><img src="<?php echo JURI::root()."components/com_joomla_lms/lms_images/treeview/sub2.png";?>" alt=' ' /></td>
								<?php } ?>							
									<td align="left">	
										<a href="<?php echo $link; ?>">
										<?php echo $row->name; ?>
										</a>	
									</td>
									<td align="left">
										<?php echo $row->description; ?>								
									</td>
									<td align="center">
										<a href="<?php echo $link_d; ?>">
										<img src="<?php echo ADMIN_IMAGES_PATH.($row->disabled?'publish_x.png':'tick.png');?>" alt=" " />
										</a>
									</td>
								</tr>
							<?php $k = 1 - $k;
						}}?>
						</tbody>
				</table>			
				</td>
			</tr>
		</table>
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="task" value="notifications" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0" />		
		</form>
	<?php	
	}
	
	function JLMS_EditNotification( & $row, $option, & $lists ) 
	{
		$editor = &JFactory::getEditor();
	?>
		<script language="javascript" type="text/javascript">		
		function submitbutton(pressbutton) {
			var form = document.adminForm;
			if (pressbutton == 'notifications') {
				submitform( pressbutton );
				return;
			}

			<?php 
			if( $row->use_manager_template && $row->use_learner_template ) { ?>			
			// do field validation
			if ( !form.learner_template_disabled.checked && !parseInt(form.learner_email_template.options[form.learner_email_template.selectedIndex].value)){
				alert("<?php echo _JLMS_NOTS_SELECT_USR_TPL; ?>");
			}
			<?php if( !$row->skip_managers ) { ?>			 
			else if( !form.manager_template_disabled.checked && !parseInt(form.manager_email_template.options[form.manager_email_template.selectedIndex].value) ) {
				alert("<?php echo _JLMS_NOTS_SELECT_MNGR_TPL; ?>");
			} 			
			<?php } ?>
			<?php } else if( $row->use_learner_template ) { ?>
			if ( !form.learner_template_disabled.checked && !parseInt(form.learner_email_template.options[form.learner_email_template.selectedIndex].value) ){
				alert("<?php echo _JLMS_NOTS_SELECT_USR_TPL; ?>");
			}	
			<?php } else if( $row->use_manager_template && !$row->skip_managers ) { ?>
			if( !form.manager_template_disabled.checked && !parseInt(form.manager_email_template.options[form.manager_email_template.selectedIndex].value) ) {
				alert("<?php echo _JLMS_NOTS_SELECT_MNGR_TPL; ?>");
			} 	
			<?php } ?>
			else {
				submitform( pressbutton );
			}
		}
		
		<?php if( JLMS_J16version() ) { ?>
		Joomla.submitbutton = submitbutton;
		<?php } ?>
		
		function change_markers_tip( list ) 
		{				
			for( var i=1; i<list.options.length; i++ ) 
			{				 		
				document.getElementById('markers_tip'+list.options[i].value).style.display = 'none';				
			}		
						
			if( list.selectedIndex != 0 )
				document.getElementById('markers_tip'+list.options[list.selectedIndex].value).style.display = 'inline';		
		}
		
		</script>
		<form action="index.php" method="post" name="adminForm">		
		<table width="100%" >
			<tr>
				<td valign="top" width="220">
				<div>
					<?php echo joomla_lms_adm_html::JLMS_menu();?>
				</div>
				</td>
				<td valign="top">			
				<?php if (!class_exists('JToolBarHelper')) { ?>
				<table class="adminheading">
				<tr>
					<th class="user">
					<?php echo _JOOMLMS_COMP_NAME;?>:
					<small>
						<?php echo _JLMS_NOTS_EDIT_NOT; ?>
					</small>
					</th>
				</tr>
				</table>
				<?php } ?>				
				<table class="adminlist">
						<thead>
							<tr>
								<th class="title" colspan="2">Enter values</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td width="200"><?php echo _JLMS_NAME; ?>:</td>
								<td><?php echo $row->name; ?></td>
							</tr>
							<tr>
								<td width="200"><?php echo _JLMS_ENABLED; ?>:</td>
								<td>
									<?php echo _JLMS_YES; ?>
									<input type="radio" name="enabled" value="1" <?php if(!$row->disabled) echo 'checked'; ?> />
									<?php echo _JLMS_NO; ?>
									<input type="radio" name="enabled" value="0" <?php if($row->disabled) echo 'checked'; ?> />
								</td>
							</tr>
							<?php if( !$row->skip_managers ) { ?>
							<tr>
								<td valign="middle" style="vertical-align:middle " ><?php echo _JLMS_NOTS_SELECT_ROLES; ?>: </td>
								<td>
									<?php echo $lists['jlms_roles'];?>
								</td>
							</tr>
							<?php
							}						
								if( $row->use_learner_template ) { 
							?> 
							<tr>
								<td valign="middle" style="vertical-align:middle " ><?php echo _JLMS_NOTS_SELECT_USR_E_TPL; ?>: </td>
								<td>
									<?php echo $lists['jlms_learner_email_template'];?>
									<?php if( $row->skip_managers) { echo '<div style="display:none">'; } ?>
									<input style="margin-left: 20px;" name="learner_template_disabled" type="checkbox" <?php echo ($row->learner_template_disabled)?"checked=checked":"";  ?> value="1" />
									<span>  :<?php echo _JLMS_NOTS_NOT_SEND_TO_LENRS; ?></span>
									<?php if( $row->skip_managers) { echo '</div>'; } ?>				
								</td>
							</tr>
							<?php }
							if( $row->use_manager_template && !$row->skip_managers ) {
							 ?> 
							<tr>
								<td valign="middle" style="vertical-align:middle " ><?php echo _JLMS_NOTS_SELECT_MNGR_E_TPL; ?>: </td>
								<td>
									<?php echo $lists['jlms_manager_email_template'];?>
									<input style="margin-left: 20px;" name="manager_template_disabled" type="checkbox" <?php echo ($row->manager_template_disabled)?"checked=checked":"";  ?> value="1" />
									<span >  :<?php echo _JLMS_NOTS_NOT_SEND_TO_MNGRS; ?></span>					
								</td>
							</tr>
							<?php } ?>
						</tbody>
				</table>			
			</td>
			</tr>
		</table>
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0" />
		<input type="hidden" name="id" value="<?php echo isset($row->id)?$row->id:0;?>">		
		</form>
	<?php	
	}
	
	function JLMS_ListEmailTemplates( & $rows, $pageNav, $option ) 
	{		
		?>		
		<form action="index.php" method="post" name="adminForm">		
		<table width="100%" >
			<tr>
				<td valign="top" width="220">
				<div>
					<?php echo joomla_lms_adm_html::JLMS_menu();?>
				</div>
				</td>
				<td valign="top">			
				<?php if (!class_exists('JToolBarHelper')) { ?>
				<table class="adminheading">
				<tr>
					<th class="user">
					<?php echo _JOOMLMS_COMP_NAME;?>:
					<small>
					<?php echo _JLMS_NOTS_EMAIL_TPLS;?>
					</small>
					</th>
				</tr>
				</table>
				<?php } ?>				
				<table class="adminlist">
						<thead>
							<tr>
								<th width="20">#</th>	
								<th width="20" class="title"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>							
								<th class="title"><?php echo _JLMS_NAME;?></th>
								<th class="title"><?php echo _JLMS_SUBJECT;?></th>
								<th width="20" class="title" align="center"><?php echo _JLMS_ENABLED;?></th>	
							</tr>
						</thead>
						<tfoot>
							<tr>
								<td colspan="5">
								<?php echo $pageNav->getListFooter();?>
								</td>
							</tr>
						</tfoot>
						<tbody>
						<?php
						$k = 0;
						for ($i=0, $n=count($rows); $i < $n; $i++) {
							$row = $rows[$i];
							
							$link 	= 'index.php?option=com_joomla_lms&amp;task=edit_email_template&amp;hidemainmenu=1&amp;id='. $row->id;			
							$task_d = $row->disabled?'enable_email_template':'disable_email_template';						
							$link_d 	= 'index.php?option=com_joomla_lms&amp;task='.$task_d.'&amp;id='.$row->id;	
							$checked = '<input '.(($row->native)?'disabled="disabled"':'').' type="checkbox" '.(($row->native)?'id="nb'.$i.'"':'id="cb'.$i.'"').' name="cid[]" value="'.$row->id.'" onclick="isChecked(this.checked);" />';
	
							?>
							<tr class="<?php echo "row$k"; ?>">							
								<td width="20"><?php echo $pageNav->getRowOffset( $i ); ?></td>		
								<td><?php echo $checked; ?></td>
								<td align="left">							
									<a href="<?php echo $link; ?>">
									<?php echo $row->name; ?>
									</a>	
								</td>					
								<td align="left">								
									<?php echo $row->subject; ?>
								</td>
								<td align="center">
									<?php if(!$row->native){ ?>
									<a href="<?php echo $link_d; ?>">
									<?php } ?>
									<img src="<?php echo ADMIN_IMAGES_PATH.($row->disabled?'publish_x.png':'tick.png');?>" />
									<?php if(!$row->native){ ?>
									</a>
									<?php } ?>
								</td>
							</tr>
						<?php $k = 1 - $k;
						}?>
						</tbody>
				</table>			
				</td>
			</tr>
		</table>
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="task" value="email_templates" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0" />		
		</form>
	<?php	
	}
	
	function JLMS_EditEmailTemplate( & $row, $option, & $lists ) 
	{				
		$editor = &JFactory::getEditor();	
		$disabled = $row->native?'disabled="disabled"':'';
	?>
		<script language="javascript" type="text/javascript">		
		function submitbutton(pressbutton) {
			var form = document.adminForm;
			if (pressbutton == 'email_templates') {
				submitform( pressbutton );
				return;
			}

			// do field validation
			if (trim(form.name.value) == ""){
				alert("<?php echo _JLMS_NOTS_MSG_ENTR_TPL_NAME; ?>");
			} else if( trim(form.subject.value) == "" ) {
				alert("<?php echo _JLMS_NOTS_MSG_ENTR_TPL_SBJ; ?>");
			} else if( !parseInt(form.notification_type.value) ) {
				alert("<?php echo _JLMS_NOTS_MSG_SELCT_NOT_TYPE; ?>");
			} else {
				submitform( pressbutton );
			}
		}
		
		<?php if( JLMS_J16version() ) { ?>
		Joomla.submitbutton = submitbutton;
		<?php } ?>
		
		function change_markers_tip( list ) 
		{				
			for( var i=1; i<list.options.length; i++ ) 
			{				 		
				document.getElementById('markers_tip'+list.options[i].value).style.display = 'none';				
			}		
						
			if( list.selectedIndex != 0 )
				document.getElementById('markers_tip'+list.options[list.selectedIndex].value).style.display = 'inline';		
		}
		
		function trim( value ) {
			return value.replace(/(^\s+)|(\s+$)/g, "");
		}		
		</script>
		
		<form action="index.php" method="post" name="adminForm">		
		<table width="100%" >
			<tr>
				<td valign="top" width="220">
				<div>
					<?php echo joomla_lms_adm_html::JLMS_menu();?>
				</div>
				</td>
				<td valign="top">			
				<?php if (!class_exists('JToolBarHelper')) { ?>
				<table class="adminheading">
				<tr>
					<th class="user">
					<?php echo _JOOMLMS_COMP_NAME;?>:
					<small>
						<?php echo ($row->id)?_JLMS_NOTS_EDIT_EML_TPL:_JLMS_NOTS_NEW_EML_TPL; ?>
					</small>
					</th>
				</tr>
				</table>
				<?php } ?>				
				<table class="adminlist">
						<thead>
							<tr>
								<th class="title" colspan="2"><?php echo _JLMS_NOTS_ENTER_VLS; ?></th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td width="200"><?php echo _JLMS_NAME; ?>: *</td>
								<td><input <?php echo $disabled ?> class="text_area" style="width:350px" type="text" maxlength="100" name="name" value="<?php echo str_replace('"', '&quot;', $row->name); ?>" /></td>
							</tr>
							<tr>
								<td width="200"><?php echo _JLMS_SUBJECT; ?>: *</td>
								<td><input <?php echo $disabled ?> class="text_area" style="width:350px" type="text" maxlength="100" name="subject" value="<?php echo str_replace('"', '&quot;', $row->subject); ?>" /></td>
							</tr>
							<tr>
								<td valign="middle" style="vertical-align:middle " ><?php echo _JLMS_NOTS_SELCT_NOT_TYPE; ?>: *</td>
								<td>
									<?php echo $lists['jlms_notification_types'];?><br />							 
								</td>
							</tr>
							<tr>
								<td valign="middle" style="vertical-align:middle " ><?php echo _JLMS_NOTS_TEXT_SNIPPETS; ?>:</td>
								<td>									
									<?php echo $lists['jlms_markers_tips']; ?> 
								</td>
							</tr>
							<tr>
								<td width="200"><?php echo _JLMS_ENABLED; ?>:</td>
								<td>
									Yes
									<input <?php echo $disabled ?> type="radio" name="enabled" value="1"  <?php if(!$row->disabled) echo 'checked'; ?> />
									No
									<input <?php echo $disabled ?> type="radio" name="enabled" value="0" <?php if($row->disabled) echo 'checked'; ?> />
								</td>
							</tr>							
							<tr>
								<td width="200"><?php echo _JLMS_NOTS_BODY_HTML; ?>:</td>
								<td>
									<?php
										if( $row->native ) { 
											echo $row->template_html;
										} else {
											echo $editor->display( 'body_html',  $row->template_html , '100%', '550', '75', '20' ) ;
										}
									?>
								</td>
							</tr>
							<tr>
								<td width="200"><?php echo _JLMS_NOTS_BODY_TEXT; ?>:</td>
								<td>
									<textarea <?php echo $disabled ?> name="body_text" style="width: 100%; height: 350px;" rows="20" cols="75"><?php echo $row->template_alt_text;?></textarea>
								</td>
							</tr>
						</tbody>
				</table>			
			</td>
			</tr>
		</table>
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0" />
		<input type="hidden" name="id" value="<?php echo isset($row->id)?$row->id:0;?>">		
		</form>
	<?php	
	}
	
	function JLMS_showPlans($rows, $pageNav, $option)
	{
		global $my;

		JHTML::_('behavior.tooltip');
		?>
		<script language="javascript" type="text/javascript">
		<!--
		function submitbutton(pressbutton) {
			var form = document.adminForm;
			if (pressbutton == 'delete_plan') {
				submitform( pressbutton );
				return;
			}
			else {
				submitform( pressbutton );
				return;
			}
		}
		
		<?php if( JLMS_J16version() ) { ?>
		Joomla.submitbutton = submitbutton;
		<?php } ?>
		//-->
		</script>		
		<form action="index.php" method="post" name="adminForm">		
		<table width="100%" >
			<tr>
				<td valign="top" width="220">
				<div>
					<?php echo joomla_lms_adm_html::JLMS_menu();?>
				</div>
				</td>
				<td valign="top">				
					<?php 
					if (!class_exists('JToolBarHelper')) 
					{ 
					?>
					<table class="adminheading" cellpadding="0" cellspacing="0" border="0">
					<tr>
						<th class="categories">
						<?php echo _JOOMLMS_COMP_NAME;?>: <small><?php echo _JLMS_PLANS; ?></small>
						</th>
					</tr>
					</table>
					<?php 
					}
					?>				
					<table width="100%" border="0">
					<tr>
						<td valign="top">
							<table class="adminlist">
							<thead>
							<tr>
								<th width="20">#</th>
								<th width="20" class="title"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>
								<th class="title"><?php echo _JLMS_NAME; ?></th>
								<th class="title"><?php echo _JLMS_PUBLISHED; ?></th>								
								<th class="title"><?php echo _JLMS_PLANS_TRIAL_P1; ?></th>								
								<th class="title"><?php echo _JLMS_PLANS_TRIAL_P2; ?></th>								
								<th class="title"><?php echo _JLMS_PLANS_REGULAR_P; ?></th>								
								<th class="title"><?php echo _JLMS_PLANS_RECR_TIMES; ?></th>								
							</tr>	
							</thead>
							<tfoot>
							<tr>
								<td colspan="10">
									<?php echo $pageNav->getListFooter(); ?>
								</td>
							</tr>
							</tfoot>
							<tbody>
							<?php
							$k = 0;
							for ($i=0, $n=count($rows); $i < $n; $i++) {
								$row = $rows[$i];
								$link 	= 'index.php?option=com_joomla_lms&task=editA_plan&hidemainmenu=1&id='. $row->id;
								$row->editor = '';
								$checked = mosCommonHTML::CheckedOutProcessing( $row, $i );
								$src = ($row->src) ? '<img src="'.ADMIN_IMAGES_PATH.'tick.png" border="0" alt="Yes" />' : '<img src="'.ADMIN_IMAGES_PATH.'publish_x.png" border="0" alt="Yes" />';
								$sra = ($row->sra) ? '<img src="'.ADMIN_IMAGES_PATH.'tick.png" border="0" alt="Yes" />' : '<img src="'.ADMIN_IMAGES_PATH.'publish_x.png" border="0" alt="Yes" />';
								
								$img 	= $row->published ? 'tick.png' : 'publish_x.png';
								$task 	= $row->published ? 'unpublish_plan' : 'publish_plan';
								$alt 	= $row->published ? _JLMS_PUBLISHED: _JLMS_UNPUBLISHED;
								?>
								<tr class="<?php echo "row$k"; ?>">

									<td><?php echo $pageNav->rowNumber( $i ); ?></td>

									<td><?php echo $checked; ?></td>

									<td align="left">
									<?php
									if ( $row->checked_out && ( $row->checked_out != $my->id ) ) {
										echo $row->name;
									} else {
										?>
										<a href="<?php echo $link; ?>" title="<?php echo _JLMS_PLANS_EDIT_PLAN; ?>"><?php echo $row->name; ?></a>
										<?php
									}
									?>
									</td>
									
									<td><a href="javascript: void(0);" onClick="return listItemTask('cb<?php echo $i;?>','<?php echo $task;?>')">
										<img src="<?php echo ADMIN_IMAGES_PATH.$img;?>" border="0" alt="<?php echo $alt; ?>" />
										</a>
									</td>
																		
									<td><?php if ($row->p1) { echo $row->p1;
										if ($row->t1 == 'D') echo ' '._JLMS_PLANS_DAYS;
										if ($row->t1 == 'W') echo ' '._JLMS_PLANS_WEEKS;
										if ($row->t1 == 'M') echo ' '._JLMS_PLANS_MONTHS;
										if ($row->t1 == 'Y') echo ' '._JLMS_PLANS_YEARS;
										}
										?>
									</td>
																		
									<td><?php if ($row->p2) { echo $row->p2;
										if ($row->t2 == 'D') echo ' '._JLMS_PLANS_DAYS;
										if ($row->t2 == 'W') echo ' '._JLMS_PLANS_WEEKS;
										if ($row->t2 == 'M') echo ' '._JLMS_PLANS_MONTHS;
										if ($row->t2 == 'Y') echo ' '._JLMS_PLANS_YEARS;
										}
										?>
									</td>
																		
									<td><?php if ($row->p3) { echo $row->p3;
										if ($row->t3 == 'D') echo ' '._JLMS_PLANS_DAYS;
										if ($row->t3 == 'W') echo ' '._JLMS_PLANS_WEEKS;
										if ($row->t3 == 'M') echo ' '._JLMS_PLANS_MONTHS;
										if ($row->t3 == 'Y') echo ' '._JLMS_PLANS_YEARS;
										}
										?>
									</td>									
									<td><?php echo ($row->srt) ? $row->srt : '';?></td>
								</tr>
								<?php
								$k = 1 - $k;
							}
							?>														
							</tbody>
							</table>					
						</td>
					</tr>
					</table>
			</td>
		</tr>
		</table>		
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="task" value="plans" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0">	
		</form>
		<?php
	}

	function JLMS_editPlan( &$row, &$lists, $option ) {
		JHTML::_('behavior.tooltip');
		?>
		<script language="javascript" type="text/javascript">
		<!--
		function submitbutton(pressbutton) 
		{
			var form = document.adminForm;
			
			if ( pressbutton == 'plans' ) {
				submitform( pressbutton );
				return;
			}			
			
			if ( form.p1.value == 0 && form.p2.value != 0 )
			{
				alert('<?php echo _JLMS_PLANS_TRIAL2_SPEC; ?>');
				return false;
			}
			
			if ( form.p3.value == 0 )
			{
				alert("<?php echo _JLMS_PLANS_REG_PERIOD_REQ; ?>");
				return false;
			}		
			
			if( trim(form.name.value)  == '' ) {			
				alert('<?php echo _JLMS_PLANS_ENTER_P_NAME; ?>');
			} else {
				submitform( pressbutton );
			}
			
			return true;			
		}
		
		<?php if( JLMS_J16version() ) { ?>
		Joomla.submitbutton = submitbutton;
		<?php } ?>
		
		function trim( value ) {
			return value.replace(/(^\s+)|(\s+$)/g, "");
		}
		//-->
		</script>
		
		<form action="index.php" method="post" name="adminForm">		
		<table width="100%" >
			<tr>
				<td valign="top" width="220">
				<div>
					<?php echo joomla_lms_adm_html::JLMS_menu();?>
				</div>
				</td>
				<td valign="top">
				<div class="width-100">
				<fieldset class="adminform">
				<?php 
				if (!class_exists('JToolBarHelper')) 
				{ 
				?>
					<table class="adminheading" cellpadding="0" cellspacing="0" border="0">
					<tr>
						<th class="categories">
						<?php echo _JOOMLMS_COMP_NAME;?>: <small><?php echo $row->id ? _JLMS_PLANS_EDIT_PLAN : _JLMS_PLANS_NEW_PLAN;?></small>
						</th>
					</tr>
					</table>
				<?php 
				}
				?>				
				<table width="100%" >
				<tr><th colspan="2"><?php echo _JLMS_PLANS_PLAN_DETS; ?></th></tr>
				<tr><td width="20%" align="right"><?php echo _JLMS_NAME; ?>:</td>
					<td><input class="inputbox" type="text" name="name" size="50" maxlength="100" value="<?php echo $row->name; ?>" /></td>
				</tr>
<input class="inputbox" type="hidden" name="params" size="10" maxlength="100" value="<?php echo $row->params; ?>" />
				<tr><td align="right" colspan="2"><?php echo _JLMS_DESCRIPTION; ?>:
					<br /><?php	editorArea( 'editor1',  $row->description , 'description', '100%;', '250', '75', '30' ) ; ?>
					</td>
				</tr>
				<tr><td colspan="2">&nbsp;</td></tr>
				</table>
				
				<div  style="float:left;width:50%">
				<table width="100%" >
					<tr><th colspan="2"><?php echo _JLMS_PLANS_PAY_PERIOD_SET; ?>:</th></tr>
					<tr><td width="180"><?php echo _JLMS_PLANS_PERIOD1_LENGTH; ?>:</td>
						<td><input class="inputbox" type="text" name="p1" size="10" maxlength="10" value="<?php echo $row->p1;?>" />&nbsp;&nbsp;<?php echo mosToolTip( _JLMS_PLANS_TIP_TRIAL_1_PAY );?></td>
					</tr>	
					<tr><td width="180"><?php echo _JLMS_PLANS_PERIOD2_LENGTH; ?>:</td>
						<td><input class="inputbox" type="text" name="p2" size="10" maxlength="10" value="<?php echo $row->p2;?>" />&nbsp;&nbsp;<?php echo mosToolTip( _JLMS_PLANS_TIP_TRIAL_2_PAY );?></td>
					</tr>				
					<tr><td width="180"><?php echo _JLMS_PLANS_REG_PERIOD_LENGTH; ?>:</td>
						<td><input class="inputbox" type="text" name="p3" size="10" maxlength="10" value="<?php echo $row->p3; ?>" />&nbsp;&nbsp;<?php echo mosToolTip( _JLMS_PLANS_TIP_REGULAR_PAY );?></td>
					</tr>						
					<tr><td colspan="2">&nbsp;</td></tr>					
				</table>
				</div> 

				<div  style="float:right;width:50%">
				<table width="100%" >
					<tr><th colspan="2"><?php echo _JLMS_PLANS_RECURR_OPT; ?></th></tr>				
					<tr><td width="180"><?php echo _JLMS_PLANS_RECR_TIMES; ?>:</td>
						<td><input class="inputbox" type="text" name="srt" size="10" maxlength="10" value="<?php echo $row->srt;?>" />&nbsp;&nbsp;<?php echo mosToolTip( _JLMS_PLANS_TIP_RECR_TIMES );?></td>
					</tr>									
					<tr><td colspan="2">&nbsp;</td></tr>
				</table>								
				</div>

				<div style="clear:both "></div>	
				
				<table width="100%" >
					<tr><th colspan="2"><?php echo _JLMS_PLANS_PUBLISH_INF; ?></th></tr>
					<tr><td width="180"><?php echo _JLMS_PUBLISHED; ?>:</td>
						<td>
						<fieldset class="radio">
						<?php echo $lists['published']; ?>
						</fieldset>
						</td>
					</tr>
					<tr><td colspan="2">&nbsp;</td></tr>
				</table>
				</fieldset>
				</div>
			</td>
		</tr>
		</table>	
		
		<input type="hidden" name="t1" value="D"/>
		<input type="hidden" name="t2" value="D"/>
		<input type="hidden" name="t3" value="D"/>
		<input type="hidden" name="sra" value="1"/>
		<input type="hidden" name="src" value="1"/>
		
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
		<input type="hidden" name="task" value="" />		
		</form>
		<?php
	}

	
	function JLMS_showDiscounts($rows, $pageNav, $option)
	{
		?>				
		<form action="index.php" method="post" name="adminForm">		
		<table width="100%" >
			<tr>
				<td valign="top" width="220">
				<div>
					<?php echo joomla_lms_adm_html::JLMS_menu();?>
				</div>
				</td>
				<td valign="top">				
					<?php 
					if (!class_exists('JToolBarHelper')) 
					{ 
					?>
					<table class="adminheading" cellpadding="0" cellspacing="0" border="0">
					<tr>
						<th class="categories">
						<?php echo _JOOMLMS_COMP_NAME;?>: <small><?php echo _JLMS_DISCS; ?></small>
						</th>
					</tr>
					</table>
					<?php 
					}
					?>				
					<table width="100%" border="0">
					<tr>
						<td valign="top">
							<table class="adminlist">
							<thead>
							<tr>
								<th width="20">#</th>
								<th width="20" class="title"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>
								<th class="title"><?php echo _JLMS_NAME; ?></th>
								<th class="title"><?php echo _JLMS_DISC_TYPE; ?></th>
								<th class="title"><?php echo _JLMS_DISC_VALUE;?></th>
								<th class="title"><?php echo _JLMS_SUBSCRIPTIONS; ?></th>
								<th class="title"><?php echo _JLMS_DISC_USER_GROUPS; ?></th>
								<th class="title"><?php echo _JLMS_USERS; ?></th>								
								<th class="title"><?php echo _JLMS_START_DATE; ?></th>
								<th class="title"><?php echo _JLMS_END_DATE; ?></th>
								<th class="title"><?php echo _JLMS_ENABLED; ?></th>
							</tr>	
							</thead>
							<tfoot>
							<tr>
								<td colspan="11">
									<?php echo $pageNav->getListFooter();?>
								</td>
							</tr>
							</tfoot>
							<tbody>
							<?php
							$k = 0;							
							for ($i=0, $n=count($rows); $i < $n; $i++) {
								$row = $rows[$i];
								$link 	= 'index.php?option=com_joomla_lms&task=editA_discount&hidemainmenu=1&id='. $row->id;									
								
								$img 	= $row->enabled ? 'tick.png' : 'publish_x.png';
								$task 	= $row->enabled ? 'disable_discount' : 'enable_discount';
								$alt 	= $row->enabled ? _JLMS_ENABLED : _JLMS_DISABLED;
								$discount_type 	= ( $row->discount_type ) ? _JLMS_TOTAL : _JLMS_PERCENT;
								$checked = '<input type="checkbox" id="cb'.$i.'" name="cid[]" value="'.$row->id.'" onclick="isChecked(this.checked);" />';	
								?>
								<tr class="<?php echo "row$k"; ?>">
									<td align="center" width="20"><?php echo $pageNav->getRowOffset( $i ); ?></td>
									<td><?php echo $checked; ?></php></td>
									<td align="center">																		
										<a href="<?php echo $link; ?>" title="<?php echo _JLMS_DISC_EDIT; ?>"><?php echo $row->name; ?></a>	
									</td>									
									<td align="center"><?php echo $discount_type; ?></td>
									<td align="center"><?php echo $row->value; ?></td>
									<td align="center"><?php echo $row->count_subscriptions; ?></td>
									<td align="center"><?php echo $row->count_usergroups; ?></td>
									<td align="center"><?php echo $row->count_users; ?></td>
									<td align="center"><?php echo $row->start_date; ?></td>
									<td align="center"><?php echo $row->end_date; ?></td>									
									<td align="center"><a href="javascript: void(0);" onClick="return listItemTask('cb<?php echo $i;?>','<?php echo $task;?>')">
										<img src="<?php echo ADMIN_IMAGES_PATH.$img;?>" border="0" alt="<?php echo $alt; ?>" />
										</a>
									</td>													
								</tr>
								<?php
								$k = 1 - $k;
							}
							?>
							</table>							
							</tbody>
						</table>				
				</td>
			</tr>
		</table>		
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="task" value="discounts" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0">
			
		</form>
		<?php
	}

	function JLMS_editDiscount( &$row, &$lists, $option ) {
?>

		<script language="javascript" type="text/javascript">
		<!--
		function submitbutton(pressbutton) {
			var form = document.adminForm;
			if (pressbutton == 'discounts') {
				submitform( pressbutton );
				return;
			}
									
			if( trim(form.name.value)  == '' ) {			
				alert('<?php echo _JLMS_DISC_MSG_ENTER_NAME; ?>');
			} else if( trim(form.users.value) != '' && !/^[0-9,\,]*$/.test( form.users.value ) ) 
			{
				alert( '<?php echo _JLMS_DISC_MSG_INCOR_VAL; ?>' );
			} else {
				submitform( pressbutton );
			}
			
			return true;			
		}
		
		<?php if( JLMS_J16version() ) { ?>
		Joomla.submitbutton = submitbutton;
		<?php } ?>
		
		function trim( value ) {
			return value.replace(/(^\s+)|(\s+$)/g, "");
		}
		//-->
		</script>		
		<form action="index.php" method="post" name="adminForm">		
		<table width="100%" >
			<tr>
				<td valign="top" width="220">
				<div>
					<?php echo joomla_lms_adm_html::JLMS_menu();?>
				</div>
				</td>
				<td valign="top">
				<div class="width-100">
				<fieldset class="adminform">
				<?php 
				if (!class_exists('JToolBarHelper')) 
				{ 
				?>
					<table class="adminheading" cellpadding="0" cellspacing="0" border="0">
					<tr>
						<th class="categories">
						<?php echo _JOOMLMS_COMP_NAME;?>: <small><?php echo $row->id ? _JLMS_DISC_EDIT : _JLMS_DISC_NEW;?></small>
						</th>
					</tr>
					</table>
				<?php 
				}
				?>			
				<table width="100%" >
					<tr><th colspan="2"><?php echo _JLMS_DISC_DETAILS; ?></th></tr>
					<tr><td width="20%" align="right"><?php echo _JLMS_NAME; ?>:</td>
						<td><input class="inputbox" type="text" name="name" size="50" maxlength="100" value="<?php echo $row->name; ?>" /></td>
					</tr>				
					<tr>
							<td width="200"><?php echo _JLMS_ENABLED; ?>:</td>
							<td>
								<fieldset class="radio">								
								<input id="disc_enabled_yes" type="radio" name="enabled" value="1" <?php if($row->enabled) echo 'checked'; ?> />
								<label for="disc_enabled_yes"><?php echo _JLMS_YES; ?></label>								
								<input id="disc_enabled_no" type="radio" name="enabled" value="0" <?php if(!$row->enabled) echo 'checked'; ?> />
								<label for="disc_enabled_no"><?php echo _JLMS_NO; ?></label>
								</fieldset>
							</td>
					</tr>					
					<tr>
							<td width="200"><?php echo _JLMS_DISC_TYPE; ?>:</td>
							<td>
								<fieldset class="radio">								
								<input id="disc_type_total" type="radio" name="discount_type" value="1" <?php if( $row->discount_type ) echo 'checked'; ?> />
								<label for="disc_type_total"><?php echo _JLMS_TOTAL; ?></label>
								<input id="disc_type_percent" type="radio" name="discount_type" value="0" <?php if( !$row->discount_type ) echo 'checked'; ?> />
								<label for="disc_type_percent"><?php echo _JLMS_PERCENT; ?></label>
								</fieldset>
							</td>
					</tr>					
					<tr>
							<td width="200"><?php echo _JLMS_DISC_VALUE; ?>:</td>
							<td>								
								<input class="inputbox" type="text" name="value" size="12" maxlength="12" value="<?php echo $row->value; ?>" />
							</td>
					</tr>					
					<tr>
						<td width="100"><?php echo _JLMS_START_DATE; ?>:</td>
						<td>
							<?php								
								echo JHTML::_('calendar', $row->start_date, 'start_date', 'start_date', '%Y-%m-%d', array('class' => 'text_area'));								
						 	?>	
						</td>
					</tr>					
					<tr>
						<td width="100"><?php echo _JLMS_END_DATE; ?>:</td>
						<td>
							<?php 
									echo JHTML::_('calendar', $row->end_date, 'end_date', 'end_date', '%Y-%m-%d', array('class' => 'text_area'));								 
						 	?>	
						</td>
					</tr>														
				</table>				
				<table width="100%" >
				<tr><th colspan="2"><?php echo _JLMS_DISC_LIMIT_TO; ?></th></tr>
				<tr>
					<td width="20%" align="left"><?php echo _JLMS_USER_GROUPS; ?>:</td>
					<td width="20%" align="left"><?php echo _JLMS_SUBSCRIPTIONS; ?>:</td>
					<td width="60%" align="left"><?php echo _JLMS_USERS; ?>:</td>
				</tr>
				<tr>
					<td><?php echo $lists['usergroups']?></td>				
					<td><?php echo $lists['subscriptions']?></td>
					<td valign="top"><textarea name="users" cols="40" rows="9" ><?php echo $row->users;?></textarea></td>
				</tr>
				</table>				
				</fieldset>
				</div>
			</td>
		</tr>
		</table>		
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
		<input type="hidden" name="task" value="" />		
		</form>
		<?php
	}
	
	function JLMS_showDiscountCoupons($rows, $pageNav, $option)
	{
		?>				
		<form action="index.php" method="post" name="adminForm">		
		<table width="100%" >
			<tr>
				<td valign="top" width="220">
				<div>
					<?php echo joomla_lms_adm_html::JLMS_menu();?>
				</div>
				</td>
				<td valign="top">			
					<?php 
					if (!class_exists('JToolBarHelper')) 
					{ 
					?>
					<table class="adminheading" cellpadding="0" cellspacing="0" border="0">
					<tr>
						<th class="categories">
						<?php echo _JOOMLMS_COMP_NAME;?>: <small><?php echo _JLMS_DISC_COUPONS; ?></small>
						</th>
					</tr>
					</table>
					<?php 
					}
					?>				
					<table width="100%" border="0">
					<tr>
						<td valign="top">
							<table class="adminlist">
							<thead>
							<tr>
								<th width="20">#</th>
								<th width="20" class="title"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>
								<th class="title"><?php echo _JLMS_NAME; ?></th>
								<th class="title"><?php echo _JLMS_DISC_C_CODE; ?></th>
								<th class="title"><?php echo _JLMS_DISC_C_TYPE; ?></th>
								<th class="title"><?php echo _JLMS_DISC_TYPE; ?></th>											
								<th class="title"><?php echo _JLMS_DISC_VALUE; ?></th>												
								<th class="title"><?php echo _JLMS_START_DATE; ?></th>
								<th class="title"><?php echo _JLMS_END_DATE; ?></th>
								<th class="title"><?php echo _JLMS_ENABLED; ?></th>
							</tr>	
							</thead>
							<tfoot>
							<tr>
								<td colspan="11">
									<?php echo $pageNav->getListFooter();?>
								</td>
							</tr>
							</tfoot>
							<tbody>
							<?php
							$k = 0;							
							for ($i=0, $n=count($rows); $i < $n; $i++) {
								$row = $rows[$i];
								$link 	= 'index.php?option=com_joomla_lms&task=editA_discount_coupon&hidemainmenu=1&id='. $row->id;									
								
								$img 	= $row->enabled ? 'tick.png' : 'publish_x.png';
								$task 	= $row->enabled ? 'disable_discount_coupon' : 'enable_discount_coupon';
								$alt 	= $row->enabled ? _JLMS_ENABLED : _JLMS_DISABLED;
								$discount_type 	= ( $row->discount_type  ) ? _JLMS_TOTAL:_JLMS_PERCENT;
								$coupont_type 	= ( $row->coupon_type ) ? _JLMS_DISC_ONE_TIME : _JLMS_DISC_PERMANENT;
								$checked = '<input type="checkbox" id="cb'.$i.'" name="cid[]" value="'.$row->id.'" onclick="isChecked(this.checked);" />';	
								?>
								<tr class="<?php echo "row$k"; ?>">
									<td align="center" width="20"><?php echo $pageNav->getRowOffset( $i ); ?></td>
									<td><?php echo $checked; ?></td>
									<td align="center">															
										<a href="<?php echo $link; ?>" title="<?php echo _JLMS_DISC_EDIT; ?>"><?php echo $row->name; ?></a>	
									</td>									
									<td align="center"><?php echo $row->code; ?></td>
									<td align="center"><?php echo $coupont_type; ?></td>
									<td align="center"><?php echo $discount_type; ?></td>
									<td align="center"><?php echo $row->value; ?></td>								
									<td align="center"><?php echo $row->start_date; ?></td>
									<td align="center"><?php echo $row->end_date; ?></td>									
									<td align="center"><a href="javascript: void(0);" onClick="return listItemTask('cb<?php echo $i;?>','<?php echo $task;?>')">
										<img src="<?php echo ADMIN_IMAGES_PATH.$img;?>" border="0" alt="<?php echo $alt; ?>" />
										</a>
									</td>													
								</tr>
								<?php
								$k = 1 - $k;
							}
							?>
							</table>							
							</tbody>
							</table>						
					</td>
				</tr>
		</table>		
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="task" value="discount_coupons" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0">				
		</form>
		<?php
	}

	function JLMS_editDiscountCoupon( &$row, &$lists, $option ) {
?>

		<script language="javascript" type="text/javascript">
		<!--
		function submitbutton(pressbutton) {
			var form = document.adminForm;
			if (pressbutton == 'discount_coupons') {
				submitform( pressbutton );
				return;
			}
						
			if( trim(form.name.value)  == '' ) {			
				alert('<?php echo _JLMS_DISC_MSG_ENTR_C_NAME; ?>');
			} else if( trim(form.code.value)  == '' ) {			
				alert('<?php echo _JLMS_DISC_MSG_ENTR_C_CODE; ?>');
			} else if( trim(form.users.value) != '' && !/^[0-9,\,]*$/.test( form.users.value ) ) 
			{
				alert('<?php echo _JLMS_DISC_MSG_USRS_INCOR_VAL; ?>');
			} else if( !/^[0-9,A-Z]*$/.test( form.code.value ) ) 
			{
				alert('<?php echo _JLMS_DISC_MSG_C_CODE_INCOR_VAL; ?>');
			} else {
				submitform( pressbutton );
			}
			
			return true;			
		}
		
		<?php if( JLMS_J16version() ) { ?>
		Joomla.submitbutton = submitbutton;
		<?php } ?>
		
		function trim( value ) {
			return value.replace(/(^\s+)|(\s+$)/g, "");
		}
		//-->
		</script>
		
		<form action="index.php" method="post" name="adminForm">		
		<table width="100%" >
			<tr>
				<td valign="top" width="220">
				<div>
					<?php echo joomla_lms_adm_html::JLMS_menu();?>
				</div>
				</td>
				<td valign="top">
				<div class="width-100">
				<fieldset class="adminform">
				<?php 
				if (!class_exists('JToolBarHelper')) 
				{ 
				?>
					<table class="adminheading" cellpadding="0" cellspacing="0" border="0">
					<tr>
						<th class="categories">
						<?php echo _JOOMLMS_COMP_NAME;?>: <small><?php echo $row->id ? _JLMS_DISC_C_EDIT : _JLMS_DISC_C_NEW;?></small>
						</th>
					</tr>
					</table>
				<?php 
				}
				?>			
				<table width="100%" >
					<tr><th colspan="3"><?php echo _JLMS_DISC_C_DETS; ?></th></tr>
					<tr><td width="20%" align="right"><?php echo _JLMS_NAME; ?>:</td>
						<td><input class="inputbox" type="text" name="name" size="50" maxlength="100" value="<?php echo $row->name; ?>" /></td>
						<td></td>
					</tr>
					<tr><td colspan="3">&nbsp;</td></tr>
					<tr><td width="20%" align="right"><?php echo _JLMS_CODE; ?>:</td>
						<td><input class="inputbox" type="text" name="code" size="50" maxlength="32" value="<?php echo $row->code; ?>" onblur="this.value = this.value.toUpperCase(); return true;" /></td>
						<td></td>
					</tr>				
					<tr>
							<td width="200"><?php echo _JLMS_ENABLED; ?>:</td>
							<td>
								<fieldset class="radio">						
								<input id="diccoupon_yes" type="radio" name="enabled" value="1" <?php if($row->enabled) echo 'checked'; ?> />
								<label for="diccoupon_yes"><?php echo _JLMS_YES; ?></label>								
								<input id="diccoupon_no" type="radio" name="enabled" value="0" <?php if(!$row->enabled) echo 'checked'; ?> />
								<label for="diccoupon_no"><?php echo _JLMS_NO; ?></label>
								</fieldset>
							</td>
							<td></td>
					</tr>					
					<tr>
							<td width="200"><?php echo _JLMS_DISC_C_TYPE; ?>:</td>
							<td>
								<fieldset class="radio">								
								<input id="diccoupon_onetime" type="radio" name="coupon_type" value="1" <?php if( $row->coupon_type ) echo 'checked'; ?> />					
								<label for="diccoupon_onetime"><?php echo _JLMS_DISC_ONE_TIME; ?></label>			
								<input id="diccoupon_permanent" type="radio" name="coupon_type" value="0" <?php if( !$row->coupon_type ) echo 'checked'; ?> />
								<label for="diccoupon_permanent"><?php echo _JLMS_DISC_PERMANENT; ?></label>
								</fieldset>
							</td>
							<td>
								<?php echo _JLMS_DISC_C_TYPE_DESC; ?>							
							</td>
					</tr>					
					<tr>
							<td width="200"><?php echo _JLMS_DISC_TYPE; ?>:</td>
							<td>
								<fieldset class="radio">								
								<input id="diccoupon_total" type="radio" name="discount_type" value="1" <?php if( $row->discount_type ) echo 'checked'; ?> />
								<label for="diccoupon_total"><?php echo _JLMS_TOTAL; ?></label>								
								<input id="diccoupon_percent" type="radio" name="discount_type" value="0" <?php if( !$row->discount_type ) echo 'checked'; ?> />
								<label for="diccoupon_percent"><?php echo _JLMS_PERCENT; ?></label>
								</fieldset>
							</td>
							<td></td>
					</tr>					
					<tr>
							<td width="200"><?php echo _JLMS_DISC_VALUE; ?>:</td>
							<td>								
								<input class="inputbox" type="text" name="value" size="12" maxlength="12" value="<?php echo $row->value; ?>" />
							</td>
							<td></td>
					</tr>
					<tr><td colspan="3">&nbsp;</td></tr>
					<tr>
						<td width="100"><?php echo _JLMS_START_DATE; ?>:</td>
						<td>
							<?php								
								echo JHTML::_('calendar', $row->start_date, 'start_date', 'start_date', '%Y-%m-%d', array('class' => 'text_area'));								
						 	?>	
						</td>
						<td></td>
					</tr>					
					<tr>
						<td width="100"><?php echo _JLMS_END_DATE; ?>:</td>
						<td>
							<?php 
									echo JHTML::_('calendar', $row->end_date, 'end_date', 'end_date', '%Y-%m-%d', array('class' => 'text_area'));								 
						 	?>	
						</td>
						<td></td>
					</tr>														
				</table>				
				<table width="100%">
				<tr><th colspan="2">Limited to</th></tr>
				<tr>
					<td width="20%" align="left"><?php echo _JLMS_USERS_GROUPS; ?>:</td>
					<td width="20%" align="left"><?php echo _JLMS_SUBSCRIPTIONS; ?>:</td>
					<td width="60%" align="left"><?php echo _JLMS_USERS; ?>:</td>
				</tr>
				<tr>
					<td><?php echo $lists['usergroups']?></td>				
					<td><?php echo $lists['subscriptions']?></td>
					<td valign="top"><textarea name="users" cols="40" rows="9" ><?php echo $row->users;?></textarea></td>
				</tr>
				</table>				
				</fieldset>
				</div>
			</td>
		</tr>
		</table>		
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
		<input type="hidden" name="task" value="" />		
		</form>
		<?php
	}
	
	function JLMS_showDiscountCouponsStatistics($rows, $pageNav, $option)
	{
		?>				
		<form action="index.php" method="post" name="adminForm">		
		<table width="100%" >
			<tr>
				<td valign="top" width="220">
				<div>
					<?php echo joomla_lms_adm_html::JLMS_menu();?>
				</div>
				</td>
				<td valign="top">			
					<?php 
					if (!class_exists('JToolBarHelper')) 
					{ 
					?>
					<table class="adminheading" cellpadding="0" cellspacing="0" border="0">
					<tr>
						<th class="categories">
						<?php echo _JOOMLMS_COMP_NAME;?>: <small><?php echo _JLMS_DISC_C_U_STATS; ?></small>
						</th>
					</tr>
					</table>
					<?php 
					}
					?>				
					<table width="100%" border="0">
					<tr>
						<td valign="top">
							<table class="adminlist">
							<thead>
							<tr>
								<th width="20">#</th>
								<th width="20" class="title"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>								
								<th class="title"><?php echo _JLMS_DISC_C_CODE; ?></th>
								<th class="title"><?php echo _JLMS_USERNAME; ?></th>
								<th class="title"><?php echo _JLMS_NAME; ?></th>
								<th class="title"><?php echo _JLMS_EMAIL ?></th>														
								<th class="title"><?php echo _JLMS_DATE; ?></th>
								<th class="title"><?php echo _JLMS_DISC_PAY_ID;?></th>								
							</tr>	
							</thead>
							<tfoot>
							<tr>
								<td colspan="11">
									<?php echo $pageNav->getListFooter();?>
								</td>
							</tr>
							</tfoot>
							<tbody>
							<?php
							$k = 0;							
							for ($i=0, $n=count($rows); $i < $n; $i++) {
								$row = $rows[$i];								
					
								$checked = '<input type="checkbox" id="cb'.$i.'" name="cid[]" value="'.$row->id.'" onclick="isChecked(this.checked);" />';	
								if( $row->coupon_id )
									$coupon_link = '<a href="index.php?option=com_joomla_lms&task=editA_discount_coupon&id='. $row->coupon_id .'" title="'._JLMS_DISC_VIEW_C.'">'.$row->coupon_code.'</a>';
								else
									$coupon_link = $row->code;
									
								if( $row->payment_id )
									$payment_link = '<a href="index.php?option=com_joomla_lms&task=editA_payment&id='. $row->payment_id.'" title="'._JLMS_DISC_VIEW_C.'">'.$row->payment_id.'</a>';
								else
									$payment_link = $row->payment_id;
																		
								?>
								<tr class="<?php echo "row$k"; ?>">
									<td align="center" width="20"><?php echo $pageNav->getRowOffset( $i ); ?></td>
									<td><?php echo $checked; ?></php></td>								
									<td align="center">																				
										<?php echo $coupon_link; ?>	
									</td>								
									<td align="center"><?php echo $row->username; ?></td>
									<td align="center"><?php echo $row->name; ?></td>
									<td align="center"><?php echo $row->email; ?></td>								
									<td align="center"><?php echo $row->date; ?></td>
									<td align="center">																				
										<?php echo $payment_link; ?>	
									</td>																				
								</tr>
								<?php
								$k = 1 - $k;
							}
							?>
							</tbody>
							</table>
						</td>
					</tr>
					</table>				
				</td>
			</tr>
		</table>
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="task" value="discount_coupons_statistics" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0" />				
		</form>
		<?php
	}
	
}

function jlms_update_checker(){
	global $lms_version, $lms_version_check;
	?>
	<table cellpadding="0" cellspacing="0" border="0" width="100%" class="adminheading">
		<tr>
			<td style="padding:0;margin:0;"><?php
			if ($lms_version_check) {
				?><div id="lms_LatestVersion"><a href="check_now" onclick="return lms_CheckVersion();" style="cursor: pointer; text-decoration:underline;">&nbsp;check now</a></div><?php
			} else {
				?><div id="lms_LatestVersion" style="color:#CCC">&nbsp;...</div><?php
			}
			?></td>
		</tr>
    </table>

	<script type="text/javascript"><!--//--><![CDATA[//><!--

			function makeRequest(url) {

				var http_request = false;

				if (window.XMLHttpRequest) { // Mozilla, Safari,...
					http_request = new XMLHttpRequest();
					if (http_request.overrideMimeType) {
						http_request.overrideMimeType('text/xml');
						// See note below about this line
					}
				} else if (window.ActiveXObject) { // IE
					try {
						http_request = new ActiveXObject("Msxml2.XMLHTTP");
					} catch (e) {
						try {
							http_request = new ActiveXObject("Microsoft.XMLHTTP");
						} catch (e) {}
					}
				}

				if (!http_request) {
					// alert('Giving up: Cannot create an XMLHTTP instance');
					return false;
				}
				http_request.onreadystatechange = function() { alertContents(http_request); };
				http_request.open('GET', url, true);
				http_request.send(null);
			}

			function alertContents(http_request) {

				if (http_request.readyState == 4) {
					if ((http_request.status == 200) && (http_request.responseText.length < 1025)) {
						document.getElementById('lms_LatestVersion').innerHTML = '&nbsp;'+http_request.responseText;
					} else {
						document.getElementById('lms_LatestVersion').innerHTML = '...failed...';
					}
				}

			}

			function lms_CheckVersion() {
				document.getElementById('lms_LatestVersion').innerHTML = 'checking...';
				makeRequest('<?php
				echo "index3.php?option=com_joomla_lms&task=latestVersion&no_html=1";
				?>');
				return false;
			}
			function lms_InitAjax() {
				makeRequest('<?php
				echo "index3.php?option=com_joomla_lms&task=latestVersion&no_html=1";
				?>');
			}

			function lms_AddEvent(obj, evType, fn){
				if (obj.addEventListener){
					obj.addEventListener(evType, fn, true);
					return true;
				} else if (obj.attachEvent){
					var r = obj.attachEvent("on"+evType, fn);
					return r;
				} else {
					return false;
				}
			}

			<?php
			if (!$lms_version_check) {
				?>
				lms_AddEvent(window, 'load', lms_InitAjax);
				<?php
			}
			?>
			//--><!]]></script>
			<?php
}
?>