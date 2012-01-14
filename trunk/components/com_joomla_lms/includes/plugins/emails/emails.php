<?php
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

$_JLMS_PLUGINS = & JLMSFactory::getPlugins();

require_once( _JOOMLMS_FRONT_HOME.DS.'includes'.DS.'notifications'.DS.'notifications.manager.php' );
require_once( _JOOMLMS_FRONT_HOME.DS.'includes'.DS.'notifications'.DS.'email.manager.php' );
require_once( _JOOMLMS_FRONT_HOME.DS.'includes'.DS.'classes'.DS.'lms.acl.php' );

$notific_events = NotificationsManager::getNotificationEvents();

if( isset($notific_events[0]) ) {
	foreach( $notific_events AS $notific_event) 
	{	
		$_JLMS_PLUGINS->registerFunction( $notific_event->event_action, 'sendNotification' );	
	}
}
	
function sendNotification(  $params ) 
{
	$db = JFactory::getDBO();		
	$mailManager = & MailManager::getChildInstance();
	$app = JFactory::getApplication();
			
	$notification_event = NotificationsManager::getNotificationEventByActionName( $params['action_name'] );
				
	if( !$notification_event )
		return '';  
		
	if( !isset($params['sender']) )	
		$params['sender'] = array($app->getCfg('mailfrom'), $app->getCfg('fromname')); 
		
	$sql = "SELECT learner_template, manager_template, selected_manager_roles, learner_template_disabled, manager_template_disabled,  disabled FROM #__lms_email_notifications WHERE id = ".$db->quote($notification_event->id);	
	$db->setQuery($sql);
	$row = $db->loadObject();
		
	if( $row ) 
	{
		$notification_event->disabled = $row->disabled;
		$notification_event->learner_template = $row->learner_template;
		$notification_event->manager_template = $row->manager_template;
		$notification_event->learner_template_disabled = $row->learner_template_disabled;
		$notification_event->manager_template_disabled = $row->manager_template_disabled;
		$notification_event->selected_manager_roles = explode( ',', $row->selected_manager_roles );	
	}
		
	if( $notification_event->disabled || (!$notification_event->learner_template && !$notification_event->manager_template) ) 
		return '';
	
	$learner_template = '';	
	$manager_template = '';	 	
	
	if( $notification_event->learner_template )
		$learner_template = NotificationsManager::getEmailTemplate( $notification_event->learner_template );
	if( $notification_event->manager_template )
		$manager_template = NotificationsManager::getEmailTemplate( $notification_event->manager_template );
		
	if( !is_object($learner_template) && !is_object($manager_template) )
		return '';
	
	if( isset($params['markers']) && is_array($params['markers']) ) 
	{
		$params['markers'] = NotificationsManager::addDateTimeMarkers($params['markers']);
	} else {
		$params['markers'] = NotificationsManager::addDateTimeMarkers(array());
	}
			
	if( isset($params['markers']) && count($params['markers']) ) 
	{
		$markers_keys = array_keys($params['markers']);
		$markers_values = array_values($params['markers']);		
		
		if( isset( $params['markers_nohtml'] ) && is_array( $params['markers_nohtml'] ) ) {
			$markers_nohtml_keys = array_keys($params['markers_nohtml']);
			$markers_nohtml_values = array_values($params['markers_nohtml']);
		} else {
			$markers_nohtml_keys = array();
			$markers_nohtml_values = array();
		}
				
		if( is_object($learner_template) && $notification_event->learner_template && $notification_event->use_learner_template ) {
			$learner_template->subject = str_replace($markers_keys, $markers_values, $learner_template->subject);
			$learner_template->template_html = str_replace($markers_keys, $markers_values, $learner_template->template_html);
			$learner_template->template_alt_text = str_replace($markers_nohtml_keys, $markers_nohtml_values, $learner_template->template_alt_text);
			$learner_template->template_alt_text = str_replace($markers_keys, $markers_values, $learner_template->template_alt_text);	
		}	
		
		if( is_object($manager_template) && $notification_event->manager_template && $notification_event->use_manager_template && !$notification_event->skip_managers ) {	
			$manager_template->subject = str_replace($markers_keys, $markers_values, $manager_template->subject);
			$manager_template->template_html = str_replace($markers_keys, $markers_values, $manager_template->template_html);
			$manager_template->template_alt_text = str_replace( $markers_nohtml_keys, $markers_nohtml_values, $manager_template->template_alt_text );
			$manager_template->template_alt_text = str_replace($markers_keys, $markers_values, $manager_template->template_alt_text);			} 	
	}	
	
	if( isset( $params['wrappers'] ) && count( $params['wrappers'] ) ) 
	{
		if( is_object($learner_template) && $notification_event->learner_template && $notification_event->use_learner_template ) {							
			$learner_template->subject = NotificationsManager::replaceWrappers( $params['wrappers'], $learner_template->subject );
			$learner_template->template_html = NotificationsManager::replaceWrappers( $params['wrappers'], $learner_template->template_html );
			$learner_template->template_alt_text = NotificationsManager::replaceWrappers( $params['wrappers'], $learner_template->template_alt_text );
		}
		
		if( is_object($manager_template) && $notification_event->manager_template && $notification_event->use_manager_template && !$notification_event->skip_managers ) {
			$manager_template->subject = NotificationsManager::replaceWrappers( $params['wrappers'], $manager_template->subject );
			$manager_template->template_html = NotificationsManager::replaceWrappers( $params['wrappers'], $manager_template->template_html );
			$manager_template->template_alt_text = NotificationsManager::replaceWrappers( $params['wrappers'], $manager_template->template_alt_text );
		}
	}
				
	$managers_array = array();
	$already_sent_users = array();
	
	if( is_object($learner_template) && $notification_event->use_learner_template && !$notification_event->learner_template_disabled ) 
	{		
		if( isset($params['user_id']) && $params['user_id'] ) 
		{
			$sql = "SELECT email, name FROM #__users WHERE id = ".$db->quote($params['user_id']);	
			$db->setQuery($sql);
			$user = $db->loadObject();
			
			$params['recipient'] = $user->email;//array( $user->name, $user->email );
			$params['subject'] = $learner_template->subject;
			$params['body'] = $learner_template->template_html;
			$params['alttext'] = $learner_template->template_alt_text;
			if(isset($notification_event->notification_type)	&& $notification_event->notification_type == 2){
				$params['attachment']->file_source = getCertificateFile($params);	
				$params['attachment']->file_name = 'Certificate.png';				
			}		
						
			$mailManager->prepareEmail( $params );
			$mailManager->sendEmail();
			$already_sent_users[] = intval($params['user_id']);									
			
			unset($params['attachment']);
		}		
	}
	
	if( is_object($manager_template) && $notification_event->use_manager_template && !$notification_event->manager_template_disabled && !$notification_event->skip_managers ) 
	{
		if( isset($params['course_id']) && $params['course_id'] && isset($notification_event->selected_manager_roles[0]) ) 
		{
			$all_teachers_role_ids = JLMS_ACL_HELPER::getTeachersRoleIds();
			$teachers_role_ids = array_intersect( $notification_event->selected_manager_roles, $all_teachers_role_ids );

			$teachers = JLMS_ACL_HELPER::getCourseTeachers( $params['course_id'], $teachers_role_ids );

			$all_assistans_role_ids = JLMS_ACL_HELPER::getAssistansRolesIds();
			$assistans_role_ids = array_intersect( $notification_event->selected_manager_roles, $all_assistans_role_ids );

			$teachers = JLMS_ACL_HELPER::getCourseTeachers( $params['course_id'], $teachers_role_ids );
			$assistans = JLMS_ACL_HELPER::getCourseAssistans( $params['course_id'], $assistans_role_ids );

			if(isset($teachers[0])) 
			{
				foreach( $teachers AS $teacher ) 
				{
					if (!in_array($teacher->id, $already_sent_users)) {
						$params['recipient'] = $teacher->email;//array( $teacher->name, $teacher->email );
						$params['subject'] = $manager_template->subject;
						$params['body'] = $manager_template->template_html;
						$params['alttext'] = $manager_template->template_alt_text;

						$mailManager->prepareEmail( $params );
						$mailManager->sendEmail();
						$already_sent_users[] = $teacher->id;						
					} 
				}
			}

			if(isset($assistans[0])) 
			{
				foreach( $assistans AS $assistan ) 
				{
					if (!in_array($assistan->id, $already_sent_users)) {
						$params['recipient'] = $assistan->email;//array( $assistan->name, $assistan->email );
						$params['subject'] = $manager_template->subject;
						$params['body'] = $manager_template->template_html;
						$params['alttext'] = $manager_template->template_alt_text;

						$mailManager->prepareEmail( $params );
						$mailManager->sendEmail();
						$already_sent_users[] = $assistan->id;					
					}
				}
			}	
		}
		
		if( isset($params['user_id']) && $params['user_id'] && isset($notification_event->selected_manager_roles[0]) ) 
		{			
			$all_CEO_role_ids = JLMS_ACL_HELPER::getCEORoleIds();

			$CEO_role_ids = array_intersect( $notification_event->selected_manager_roles, $all_CEO_role_ids );			
			$all_user_CEO = JLMS_ACL_HELPER::getUserCEO( $params['user_id'], $CEO_role_ids );

			if(isset($all_user_CEO[0])) 
			{
				foreach( $all_user_CEO AS $CEO ) 
				{
					if (!in_array($CEO->id, $already_sent_users)) {
						$params['recipient'] = $CEO->email;//array( $CEO->name, $CEO->email );
						$params['subject'] = $manager_template->subject;
						$params['body'] = $manager_template->template_html;
						$params['alttext'] = $manager_template->template_alt_text;
						
						$mailManager->prepareEmail( $params );
						$mailManager->sendEmail();
						$already_sent_users[] = $CEO->id;						
					}
				}
			}		
		}		

		if( isset($notification_event->selected_manager_roles[0]) ) 
		{
			$all_admin_role_ids = JLMS_ACL_HELPER::getAdminsRoleIds();						
			$admin_role_ids = array_intersect( $notification_event->selected_manager_roles, $all_admin_role_ids );
			$admins = JLMS_ACL_HELPER::getAdmins( $admin_role_ids );		

			if( isset($admins[0]) ) {
				foreach( $admins AS $admin ) 
				{
					if (!in_array($admin->id, $already_sent_users)) {
						$params['recipient'] = $admin->email;//array( $admin->name, $admin->email );
						$params['subject'] = $manager_template->subject;
						$params['body'] = $manager_template->template_html;
						$params['alttext'] = $manager_template->template_alt_text;
						
						$mailManager->prepareEmail( $params );
						$mailManager->sendEmail();
						$already_sent_users[] = $admin->id;													
					}
				}
			}
		}
	}
	
}

function getCertificateFile($params){
	global $my, $JLMS_DB, $JLMS_CONFIG, $Itemid;
		
	$course_id = $params['course_id'];
	
	//$usertype = JLMS_GetUserType($my->id, $course_id);
	$JLMS_ACL = & JLMSFactory::getACL();
	$user_id_request = $params['user_id'];
	$user_id = $my->id;
	if ($user_id_request && $user_id_request != $user_id) {
		if ($JLMS_ACL->isCourseTeacher()) {
			$user_id = $user_id_request;
		} elseif ($JLMS_ACL->isStaff() && isset($JLMS_ACL->_staff_learners) && is_array($JLMS_ACL->_staff_learners) && in_array($user_id_request, $JLMS_ACL->_staff_learners)) {
			$user_id = $user_id_request;
		}
	}

	$query = "SELECT id, crt_date FROM #__lms_certificate_users WHERE course_id = '".$course_id."' AND user_id = '".$user_id."' AND crt_option = 1";
	$JLMS_DB->SetQuery( $query );
	$row = $JLMS_DB->LoadObject();

	$tm_obj = new stdClass();
	if ($row->crt_date == '0000-00-00 00:00:00' || !$row->crt_date) {
		$row->crt_date = date('Y-m-d H:i:s');
		$query = "UPDATE #__lms_certificate_users SET crt_date = '".date('Y-m-d H:i:s')."' WHERE id = ".$row->id." AND course_id = '".$course_id."' AND user_id = '".$user_id."' AND crt_option = 1";
		$JLMS_DB->SetQuery( $query );
		$JLMS_DB->query();
		$tm_obj->force_update_print_date = true;//to fix bug with 'null' date of print.
	}
	$query = "SELECT id FROM #__lms_certificates WHERE course_id = '".$course_id."' AND crtf_type = 1 AND published = 1";
	$JLMS_DB->SetQuery( $query );
	$crtf_id = $JLMS_DB->LoadResult();
	require_once(_JOOMLMS_FRONT_HOME . "/includes/lms_certificates.php");
	$query = "SELECT * FROM #__users WHERE id = '".$user_id."'";
	$JLMS_DB->SetQuery( $query );
	$u_data = $JLMS_DB->LoadObjectList();
	$tm_obj->username = isset($u_data[0]->username)?$u_data[0]->username:'';
	$tm_obj->name = isset($u_data[0]->name)?$u_data[0]->name:'';
	$tm_obj->crtf_date = strtotime($row->crt_date);//time();
	$tm_obj->crtf_spec_answer = '';

	$tm_obj->is_preview = false;
	$user = new stdClass();
	$user->id = isset($u_data[0]->id) ? $u_data[0]->id : 0;
	$user->username = isset($u_data[0]->username) ? $u_data[0]->username : '';
	$user->name = isset($u_data[0]->name) ? $u_data[0]->name : '';
	$user->email = isset($u_data[0]->email) ? $u_data[0]->email : '';
	
	$id = $crtf_id;
	$course_id = $course_id;
	$txt_mes_obj = isset($tm_obj) ? $tm_obj : null;
	$user_obj = isset($user) ? $user : null;
	
	if (is_null($user_obj)) {
		$user_obj = new stdClass();
		$user_obj->id = $my->id;
		$user_obj->username = $my->username;
		$user_obj->email = $my->email;
		$user_obj->name = $my->name;
	}
	
//	$JLMS_ACL = & JLMSFactory::getACL();
	
	$is_preview = false;
	$is_exist = false;
	$quiz_id = 0;
	$quiz_name = '';
	$course_name = '';
	if (isset($txt_mes_obj->is_preview)) {
		$is_preview = $txt_mes_obj->is_preview;
	}
	if (isset($txt_mes_obj->quiz_id)) {
		$quiz_id = $txt_mes_obj->quiz_id;
	}
	if (isset($txt_mes_obj->quiz_name)) {
		$quiz_name = $txt_mes_obj->quiz_name;
	}
	if (isset($txt_mes_obj->course_name)) {
		$course_name = $txt_mes_obj->course_name;
	}
	$do_s = true;
	$crtf_role = 0;
	if ($is_preview) {
		$crtf_role = intval(mosGetParam($_REQUEST, 'crtf_role', 0));
	} else {
		$crtf_role = intval($JLMS_ACL->GetRole(1));
	}
	
	if ($crtf_role) {
		$query = "SELECT a.*, b.course_name FROM #__lms_certificates as a, #__lms_courses as b WHERE a.course_id = '".$course_id."' AND a.course_id = b.id AND a.parent_id = $id AND a.crtf_type = $crtf_role";
		$JLMS_DB->SetQuery( $query );
		$crts = $JLMS_DB->loadObjectList();
		if (count($crts) == 1) {
			if ($crts[0]->file_id) {
				$do_s = false;
			} else {
				$query = "SELECT file_id FROM #__lms_certificates as a WHERE a.id = '".$id."' AND a.course_id = '".$course_id."' AND a.parent_id = 0";
				$JLMS_DB->SetQuery( $query );
				$crts[0]->file_id = $JLMS_DB->LoadResult();
				if ($crts[0]->file_id) {
					$do_s = false;
				}
			}
		}
	}
	if ($do_s) {
		$query = "SELECT a.*, b.course_name FROM #__lms_certificates as a, #__lms_courses as b WHERE a.id = '".$id."' AND a.course_id = '".$course_id."' AND a.course_id = b.id AND a.parent_id = 0";
		$JLMS_DB->SetQuery( $query );
		$crts = $JLMS_DB->loadObjectList();
	}
	if (count($crts) == 1) {
		$is_duplicate = false;
		$print_duplicate_watermark = $JLMS_CONFIG->get('crtf_duplicate_wm', true);
		$crt = $crts[0];
		$JLMS_DB->SetQuery("SELECT file_srv_name FROM #__lms_files WHERE id = '".$crt->file_id."'");
		$cert_name = $JLMS_DB->LoadResult();
		if ($cert_name) {
			$ucode = md5(uniqid(rand(), true));
			$ex_crtf_id = 0;
			$is_saved_on_server = false;
			$ucode = substr($ucode,0,10);
			if (!$is_preview) {
				$query = "SELECT * FROM #__lms_certificate_prints WHERE user_id = $user_obj->id AND role_id = $crtf_role AND course_id = $course_id AND crtf_id = $id AND quiz_id = $quiz_id";
				$JLMS_DB->SetQuery( $query );
				$cr_pr = $JLMS_DB->LoadObject();
				if (is_object($cr_pr) && isset($cr_pr->id)) {
					$is_exist = $cr_pr->id;
					$ex_crtf_id = $cr_pr->id;
					$ucode = $cr_pr->uniq_id;
					$txt_mes_obj->name = $cr_pr->name;
					$txt_mes_obj->username = $cr_pr->username;
					//$txt_mes_obj->course_name = $cr_pr->course_name;
					if (isset($txt_mes_obj->force_update_print_date) && $txt_mes_obj->force_update_print_date && isset($txt_mes_obj->crtf_date) && $txt_mes_obj->crtf_date) {
						$query = "UPDATE #__lms_certificate_prints SET crtf_date = '".$txt_mes_obj->crtf_date."' WHERE id = ".$cr_pr->id." AND user_id = $user_obj->id AND role_id = $crtf_role AND course_id = $course_id AND crtf_id = $id AND quiz_id = $quiz_id";
						$JLMS_DB->SetQuery( $query );
						$JLMS_DB->query();
					} else {
						$txt_mes_obj->crtf_date = strtotime($cr_pr->crtf_date);
					}
					$is_duplicate = true;
					if ($JLMS_CONFIG->get('save_certificates', 1)) {
						$im_crtf_path = $JLMS_CONFIG->get('jlms_crtf_folder', '');
						$file_on_srv = $im_crtf_path . '/' . md5($ex_crtf_id . '_' . $ucode) . '.png';
						if (file_exists($file_on_srv)) {
							$is_saved_on_server = true;
						}
					}
				}
			}
			if ($is_saved_on_server) {
				$loadFile = $file_on_srv;
			} else {
				$loadFile = _JOOMLMS_DOC_FOLDER . $cert_name;
			}
			$im_fullsize = getimagesize($loadFile);
			if (isset($im_fullsize[2])) {
				if ($im_fullsize[2] == 1) {
					$im = imagecreatefromgif($loadFile);
				} elseif ($im_fullsize[2] == 2) {
					$im = imagecreatefromjpeg($loadFile);
				} elseif ($im_fullsize[2] == 3) {
					$im = imagecreatefrompng($loadFile);
					if (function_exists('imagesavealpha')) {
						imagesavealpha($im, true);
					}
				} else { die();}
			} else { die('Bad image format.'); }
			if (!$is_saved_on_server) {
				require_once(_JOOMLMS_FRONT_HOME . DS . 'includes' . DS . 'libchart' . DS . 'barcode.php');
				$b_params = array();
				if ($JLMS_CONFIG->get('crtf_show_sn', 1)) {
					$b_params[] = 'text';
				}
				if ($JLMS_CONFIG->get('crtf_show_barcode', 1)) {
					$b_params[] = 'bar';
				}
			}

			$origWidth = $im_fullsize[0]; 
			$origHeight = $im_fullsize[1];
			if ($is_duplicate && $print_duplicate_watermark) {
				require_once(_JOOMLMS_FRONT_HOME . DS . "includes" . DS . "libchart" . DS . "libchart.php");
				JLMS_cleanLibChartCache();
				$watermark = _JOOMLMS_FRONT_HOME . DS . "lms_images" . DS . "duplicate.png";
				$wmTarget = $JLMS_CONFIG->getCfg('absolute_path') . "/".($JLMS_CONFIG->get('temp_folder', '') ? ($JLMS_CONFIG->get('temp_folder', '')."/") : '') . time() . '_' . md5(uniqid(rand(), true)) . ".png";
				
				$waterMarkInfo = getimagesize($watermark);
				$waterMarkWidth = $waterMarkInfo[0];
				$waterMarkHeight = $waterMarkInfo[1];

				$placementX=0;
				$placementY=0;
				$waterMarkDestWidth=$waterMarkWidth;
				$waterMarkDestHeight=$waterMarkHeight;
				$waterMarkDestWidth = round(($origWidth / $waterMarkDestWidth) * $waterMarkDestWidth);
				$waterMarkDestHeight = round(($origHeight / $waterMarkDestHeight) * $waterMarkDestHeight);
				
				JLMS_Certificates::resize_png_image($watermark,$waterMarkDestWidth,$waterMarkDestHeight,$wmTarget, false);

				// get the size info for this watermark.
				$wmInfo=getimagesize($wmTarget);
				$waterMarkDestWidth=$wmInfo[0];
				$waterMarkDestHeight=$wmInfo[1];

				$differenceX = $origWidth - $waterMarkDestWidth;
				$differenceY = $origHeight - $waterMarkDestHeight;
				$placementX =  round($differenceX / 2);
				$placementY =  round($differenceY / 2);
			}
			if (!$is_saved_on_server) {
				if (!empty($b_params)) {
					$barcode = new JLMS_barcode($ucode, $b_params);
					$barcode->generate($im, $origWidth, $origHeight);
				}
				$white = imagecolorallocate($im, 255, 255, 255);
				$grey = imagecolorallocate($im, 128, 128, 128);
				$black = imagecolorallocate($im, 0, 0, 0);

				$text_messages = array();
				$crtf_msg = new stdClass();
				$crtf_msg->text_size = $crt->text_size;
				$crtf_msg->text_x = $crt->text_x;
				$crtf_msg->text_y = $crt->text_y;
				$crtf_msg->crtf_font = (isset($crt->crtf_font) && $crt->crtf_font) ? $crt->crtf_font : 'arial.ttf';
				$crtf_msg->crtf_text = $crt->crtf_text;
				$crtf_msg->course_name = $crt->course_name;
				$crtf_msg->crtf_shadow = $crt->crtf_shadow;
				$crtf_msg->crtf_align = $crt->crtf_align;
				$text_messages[] = $crtf_msg;
				$query = "SELECT * FROM #__lms_certificates WHERE course_id = $course_id AND parent_id = $crt->id AND crtf_type = '-2' ORDER BY crtf_align";
				$JLMS_DB->SetQuery($query);
				$add_cert_msgs = $JLMS_DB->LoadObjectList();
				foreach ($add_cert_msgs as $acms) {
					$crtf_msg = new stdClass();
					$crtf_msg->text_size = $acms->text_size;
					$crtf_msg->text_x = $acms->text_x;
					$crtf_msg->text_y = $acms->text_y;
					$crtf_msg->crtf_font = (isset($acms->crtf_font) && $acms->crtf_font) ? $acms->crtf_font : 'arial.ttf';
					$crtf_msg->crtf_text = $acms->crtf_text;
					$crtf_msg->course_name = $crt->course_name;
					$crtf_msg->crtf_shadow = $acms->crtf_shadow;
					$crtf_msg->crtf_align = 0;
					$text_messages[] = $crtf_msg;
				}
				foreach ($text_messages as $crt7) {
					$font_size = $crt7->text_size;
					$font_x = $crt7->text_x;
					$font_y = $crt7->text_y;
					$font_filename = $crt7->crtf_font;
					$inform = array();
					$font_text = $crt7->crtf_text;
					$username = isset($txt_mes_obj->username)?$txt_mes_obj->username:'';
					$name = isset($txt_mes_obj->name)?$txt_mes_obj->name:'';
					$course_name = isset($txt_mes_obj->course_name)?$txt_mes_obj->course_name:($crt7->course_name);
					//$spec_answer = isset($txt_mes_obj->crtf_spec_answer)?$txt_mes_obj->crtf_spec_answer:'';
					$crtf_date = isset($txt_mes_obj->crtf_date)?$txt_mes_obj->crtf_date:time();
					$font_text = str_replace('#username#', $username, $font_text);
					$font_text = str_replace('#name#', $name, $font_text);
					$font_text = str_replace('#course#', $course_name, $font_text);
	
					$font_text = JLMS_Certificates::ReplaceCourseRegAnswers($font_text, $txt_mes_obj, $user_obj->id, $course_id);
					//$font_text = str_replace('#reg_answer#', $spec_answer, $font_text);
	
					$font_text = JLMS_Certificates::ReplaceQuizAnswers($font_text, $txt_mes_obj, $user_obj->id, $course_id);
	
					$font_text = JLMS_Certificates::ReplaceEventOptions($font_text, $txt_mes_obj, $user_obj->id, $course_id);
	
					$font_text = JLMS_Certificates::ReplaceCBProfileOptions($font_text, $txt_mes_obj, $user_obj->id, $course_id);
	
					//$font_text = JLMS_Certificates::ReplaceUPN($font_text, $txt_mes_obj, $user_obj->id, $course_id);
	
					// replace #date#
					$str_format = 'Y-m-d';
					$str_format_pre = '';
					$first_pos = strpos( $font_text,'#date');
					if ($first_pos !== false) {
						$first_str = substr($font_text, $first_pos+5, strlen($font_text) - $first_pos - 5);
						$sec_pos = strpos( $first_str,'#');
						$str_format = substr($first_str, 0, $sec_pos);
						$str_format_pre = $str_format;
						echo $str_format;
						if ($str_format) {
							if (substr($str_format,0,1) == '(') {
								$str_format = substr($str_format,1);
							}
							if (substr($str_format,-1) == ')') {
								$str_format = substr($str_format,0,-1);
							}
						}
						echo $str_format;
					}
					if (!$str_format) { $str_format = 'Y-m-d';}
					$font_text = str_replace('#date'.$str_format_pre.'#', date($str_format, $crtf_date), $font_text);
					// end of #date#
					$font = JPATH_SITE . "/media/arial.ttf";
					if (file_exists(JPATH_SITE . "/media/".$font_filename)) {
						$font = JPATH_SITE . "/media/".$font_filename;
					}
					$text_array = explode("\n",$font_text);
					#print_r($text_array);die;
					$count_lines = count($text_array);
					$text_lines_xlefts = array();
					$text_lines_xrights = array();
					$text_lines_heights = array();
					for ($i = 0; $i< $count_lines; $i++) {
						$font_box = imagettfbbox($font_size, 0, $font, $text_array[$i]);
						$text_lines_xlefts[$i] = $font_box[0];
						$text_lines_xrights[$i] = $font_box[2];
						$text_lines_heights[$i] = $font_box[1]-$font_box[7];
						if ($text_lines_heights[$i] < $font_size) { $text_lines_heights[$i] = $font_size; }
					}
					$min_x = 0;
					$max_x = 0;
					$max_w = 0;
					for ($i = 0; $i< $count_lines; $i++) {
						if ($min_x > $text_lines_xlefts[$i]) $min_x = $text_lines_xlefts[$i];
						if ($max_x < $text_lines_xrights[$i]) $max_x = $text_lines_xrights[$i];
						if ($max_w < ($text_lines_xrights[$i]-$text_lines_xlefts[$i])) $max_w = ($text_lines_xrights[$i] - $text_lines_xlefts[$i]);
					}
					#$crt->crtf_text
					#$alignment = 'left';
					$allow_shadow = ($crt7->crtf_shadow == 1);
					#$alignment = 'left';
					switch(intval($crt7->crtf_align)) {
						case 1:
							for ($i = 0; $i< $count_lines; $i++) {
								$cur_w = $text_lines_xrights[$i] - $text_lines_xlefts[$i];
								$ad = intval(($max_w - $cur_w)/2) - intval($max_w/2);
								if ($allow_shadow) imagettftext($im, $font_size, 0, $font_x + $ad+2, $font_y+2, $grey, $font, $text_array[$i]);
								imagettftext($im, $font_size, 0, $font_x + $ad, $font_y, $black, $font, $text_array[$i]);
								$font_y = $font_y + $text_lines_heights[$i] + 3;
							}
						break;
						case 2:
							for ($i = 0; $i< $count_lines; $i++) {
								$cur_w = $text_lines_xrights[$i] - $text_lines_xlefts[$i];
								$ad = intval($max_w - $cur_w) - intval($max_w);
								if ($allow_shadow) imagettftext($im, $font_size, 0, $font_x + $ad+2, $font_y+2, $grey, $font, $text_array[$i]);
								imagettftext($im, $font_size, 0, $font_x + $ad, $font_y, $black, $font, $text_array[$i]);
								$font_y = $font_y + $text_lines_heights[$i] + 3;
							}
						break;
						default:
							for ($i = 0; $i< $count_lines; $i++) {
								$cur_w = $text_lines_xrights[$i] - $text_lines_xlefts[$i];
								$ad = 0;//intval(($max_w - $cur_w)/2);
								if ($allow_shadow) imagettftext($im, $font_size, 0, $font_x + $ad+2, $font_y+2, $grey, $font, $text_array[$i]);
								imagettftext($im, $font_size, 0, $font_x + $ad, $font_y, $black, $font, $text_array[$i]);
								$font_y = $font_y + $text_lines_heights[$i] + 3;
							}
						break;
					}
				}

				#$font_box = imagettfbbox($font_size, 0, $font, $font_text);
				#imagettftext($im, $font_size, 0, $font_x, $font_y, $grey, $font, $font_text, 'R');
				#imagettftext($im, $font_size, 0, $font_x, $font_y, $black, $font, $font_text, 'R');
	
				#@ob_end_clean();
			}
			
			if (!$is_preview) {
				if (!$is_exist) {

					$query = "INSERT INTO #__lms_certificate_prints (uniq_id, user_id, role_id, crtf_date, crtf_id, crtf_text, last_printed, name, username, course_id, course_name, quiz_id, quiz_name ) VALUES"
					. "\n (".$JLMS_DB->Quote($ucode).", $user_obj->id, $crtf_role,".$JLMS_DB->Quote(date('Y-m-d H:i:s', $crtf_date)).", $id, ".$JLMS_DB->Quote($crt->crtf_text).","
					. "\n ".$JLMS_DB->Quote(date('Y-m-d H:i:s')).", ".$JLMS_DB->Quote($user_obj->name).", ".$JLMS_DB->Quote($user_obj->username).","
					. "\n $course_id, ".$JLMS_DB->Quote($course_name).", $quiz_id, ".$JLMS_DB->Quote($quiz_name).")";
					$JLMS_DB->SetQuery( $query );
					$JLMS_DB->query();
					$ex_crtf_id = $JLMS_DB->insertid();

				} else {

					$query = "UPDATE #__lms_certificate_prints SET last_printed = ".$JLMS_DB->Quote(date('Y-m-d H:i:s')).","
					. "\n crtf_text = ".$JLMS_DB->Quote($crt->crtf_text).", course_name = ".$JLMS_DB->Quote($course_name).","
					. "\n quiz_name = ".$JLMS_DB->Quote($quiz_name)
					. "\n WHERE id = $is_exist";
					$JLMS_DB->SetQuery( $query );
					$JLMS_DB->query();
					$ex_crtf_id = $is_exist;

				}
			}
			
			$file_server_name = '';
			if($is_saved_on_server){
				$file_server_name = $im_crtf_path . '/' . md5($ex_crtf_id . '_' . $ucode) . '.png';
			} else {
				header('Content-Type: image/png');
				if (!$is_saved_on_server && !$is_preview) {
					if ($JLMS_CONFIG->get('save_certificates', 1)) {
						$im_crtf_path = $JLMS_CONFIG->get('jlms_crtf_folder', '');
						if ($im_crtf_path && is_writable($im_crtf_path)) {
							$file_server_name = $im_crtf_path . '/' . md5($ex_crtf_id . '_' . $ucode) . '.png';
							imagepng($im, $file_server_name);
						}
					}
				}
				imagedestroy($im);
			}
			
			return $file_server_name;
		}
	}
	
	return '';
}
?>