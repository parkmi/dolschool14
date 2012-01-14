<?php
/**
* includes/lms_html_tmpl.php
* JoomaLMS eLearning Software http://www.joomlalms.com/
* * * (c) ElearningForce Inc - http://www.elearningforce.biz/
**/

defined( '_JLMS_EXEC' ) or die( 'Restricted access' );

//parameter ($href_link = false) used for quiz (shtoby v url javascript code ne zanosilsya)
function JLMS_ShowToolbar($toolbar, $href_link = true, $align='right', $cellpad = 0, $cellspac = 0) {
	global $Itemid, $JLMS_CONFIG;
	
	//b Events JLMS Plugins
	$_JLMS_PLUGINS = & JLMSFactory::getPlugins();
	$_JLMS_PLUGINS->loadBotGroup('system');
	$args = array();
	$args[] = &$toolbar;
	$_JLMS_PLUGINS->trigger('onShowToolbar', $args);
	//e Events JLMS Plugins
	
	$extra_style = '';
	if($align == 'center'){
		$extra_style .= 'margin: 0 auto;';
	}
	
	$toolbar_thml = '';
	
	if(count($toolbar)){
		$toolbar_thml .= "<table class='jlms_toolbar_buttons' align='$align' style='text-align: $align; width:auto; $extra_style' cellpadding='$cellpad' cellspacing='$cellspac' border='0'><tr>";
		foreach ($toolbar as $toolbar_btn) {
			$toolbar_thml .= "<td>";
			$btn_w = "32";$btn_h = "32";
			switch ($toolbar_btn['btn_type']) {
				case 'send':
					$btn_str = _JLMS_SEND_ALT_TITLE;
					$btn_img = 'buttons/btn_send.png';
				break;
				case 'addtocart':
					$btn_str = _JLMS_ADD_TO_CART;
					$btn_img = 'buttons/btn_addtocart.png';
				break;
				case 'checkout':
					$btn_str = _JLMS_CHECKOUT_ITEMS;
					$btn_img = 'buttons/btn_checkout.png';
				break;
				case 'viewcart':
					$btn_str = _JLMS_VIEW_CART;
					$btn_img = 'buttons/btn_viewcart.png';
				break;
				case 'edit':
					$btn_str = _JLMS_EDIT;
					$btn_img = 'buttons/btn_edit.png';
				break;
				case 'save':
					$btn_str = _JLMS_SAVE_ALT_TITLE;
					$btn_img = 'buttons/btn_save.png';
				break;
				case 'preview':
					$btn_str = _JLMS_PREVIEW_ALT_TITLE;
					$btn_img = 'buttons/btn_preview.png';
				break;
				case 'back':
					$btn_str = _JLMS_BACK_ALT_TITLE;
					$btn_img = 'buttons/btn_back.png';
				break;
				case 'import':
					$btn_str = _JLMS_IMPORT_ALT_TITLE;
					$btn_img = 'buttons/btn_upload.png';
				break;
				case 'cancel':
					$btn_str = _JLMS_CANCEL_ALT_TITLE;
					$btn_img = 'buttons/btn_cancel.png';
				break;
				//homework
				case 'complete':
					$btn_str = _JLMS_COMPLETE_ALT_TITLE;
					$btn_img = 'buttons/btn_complete.png';
				break;
				case 'yes':
					$btn_str = _JLMS_YES_ALT_TITLE;
					$btn_img = 'buttons/btn_complete.png';
				break;
	
				case 'newtopic':
					$btn_str = _JLMS_YES_ALT_TITLE;
					$btn_img = 'buttons/btn_newtopic.png';
				break;
	
				//quiz
				case 'quiz_ok':
					$btn_str = _JLMS_OK_ALT_TITLE;
					$btn_img = 'buttons/btn_complete.png';
				break;
	
				//'show_lpath' section
				case 'start':
					$btn_str = _JLMS_START_ALT_TITLE;
					$btn_img = 'buttons/btn_start.png';
				break;
				
				case 'resume':
					$btn_str = _JLMS_RESUME_ALT_TITLE;
					$btn_img = 'buttons/btn_start.png';
				break;	
				
				case 'next':
					$btn_str = _JLMS_NEXT_ALT_TITLE;
					$btn_img = 'buttons/btn_start.png';
				break;
				case 'continue':
					$btn_str = _JLMS_CONTINUE_ALT_TITLE;
					$btn_img = 'buttons/btn_start.png';
				break;
				case 'prev':
					$btn_str = _JLMS_PREV_ALT_TITLE;
					$btn_img = 'buttons/btn_back.png';
				break;
				case 'contents':
					$btn_str = _JLMS_CONTENTS_ALT_TITLE;
					$btn_img = 'buttons/btn_contents.png';
				break;
				case 'restart':
					$btn_str = _JLMS_RESTART_ALT_TITLE;
					$btn_img = 'buttons/btn_restart.png';
				break;
				case 'export':
					$btn_str = _JLMS_EXPORT_ALT_TITLE;
					$btn_img = 'buttons/btn_export.png';
				break;
				case 'clear':
					$btn_str = _JLMS_CLEAR_ALT_TITLE;
					$btn_img = 'buttons/btn_clear.png';
				break;
				case 'settings':
					$btn_str = _JLMS_SETTINGS_ALT_TITLE;
					$btn_img = 'buttons/btn_configure.png';
				break;
				case 'archive':
					$btn_str = _JLMS_ARCHIVE_ALT_TITLE;
					$btn_img = 'buttons/btn_archive.png';
				break;
				case 'booking':
					$btn_str = 'Booking';
					$btn_img = 'buttons/schedule.png';
				break;	
				//---messages--///
				case 'mail_inbox':
					$btn_str = _JLMS_INBOX_ALT_TITLE;
					$btn_img = 'buttons/btn_inbox.png';
				break;	
				case 'mail_outbox':
					$btn_str = _JLMS_OUTBOX_ALT_TITLE;
					$btn_img = 'buttons/btn_outbox.png';
				break;
				case 'mail_send':
					$btn_str = _JLMS_COMPOSE_ALT_TITLE;
					$btn_img = 'buttons/btn_compose.png';
				break;
				
				//Max 6.10.08 Quiz Skip mod
				case 'skip':
					$btn_str = 'Skip';
					$btn_img = 'buttons/btn_next.png';
				break;
				//Max 26.03.08 Quiz
				case 'certificate_fbar':
					$btn_str = _JLMS_q_quiz_fin_btn_certificate;
					$btn_img = 'buttons/btn_certificate_fbar.png';
				break;
				case 'print_fbar':
					$btn_str = _JLMS_q_quiz_fin_btn_print;
					$btn_img = 'buttons/btn_print_fbar.png';
				break;
				case 'email_to_fbar':
					$btn_str = _JLMS_q_quiz_fin_btn_email;
					$btn_img = 'buttons/btn_email_to_fbar.png';
				break;
				case 'review_fbar':
					$btn_str = _JLMS_q_quiz_fin_btn_review;
					$btn_img = 'buttons/btn_review_fbar.png';
				break;
				
				//Max 10.04.08 Booking Timetable
				case 'new':
					$btn_str = 'New';
					$btn_img = 'buttons/btn_new.png';
				break;
				case 'edit':
					$btn_str = _JLMS_EDIT;
					$btn_img = 'buttons/btn_edit.png';
				break;
				case 'delete':
					$btn_str = _JLMS_DELETE;
					$btn_img = 'buttons/btn_trash.png';
				break;
				case 'apply':
					$btn_str = 'Apply';
					$btn_img = 'buttons/btn_complete.png';
				break;
				case 'save':
					$btn_str = _JLMS_SAVE_ALT_TITLE;
					$btn_img = 'buttons/btn_save.png';
				break;
				case 'cancel':
					$btn_str = _JLMS_CANCEL_ALT_TITLE;
					$btn_img = 'buttons/btn_cancel.png';
				break;
				case 'pdf':
					$btn_str = 'PDF';
					$btn_img = 'buttons/btn_pdf.png';
				break;			
	
				default:
					$btn_str = _JLMS_SAVE_ALT_TITLE;
					$btn_img = 'buttons/btn_save.png';
				break;		
			}
			if (isset($toolbar_btn['btn_str'])) {
				$btn_str = $toolbar_btn['btn_str'];
			}
			$btn_str_link = $btn_str;
			if (isset($toolbar_btn['btn_title'])) {
				$btn_str = $toolbar_btn['btn_title'];
			}
			$target = '';
			if (isset($toolbar_btn['btn_target']) && $toolbar_btn['btn_target']) {
				$target = " target='".$toolbar_btn['btn_target']."'";
			}
			
			if ($href_link) {
				$toolbar_thml .= "<a".$target." class=\"jlms_img_link\" href=\"".$toolbar_btn['btn_js']."\" title=\"".$btn_str."\" onmouseover=\"jlms_WStatus('".addslashes($btn_str)."');return true;\" onmouseout=\"jlms_WStatus('');return true;\">";
			} else {
				$toolbar_thml .= "<a".$target." class=\"jlms_img_link\" style=\"cursor:pointer\" onclick=\"".$toolbar_btn['btn_js']."\" title=\"".$btn_str."\" onmouseover=\"jlms_WStatus('".addslashes($btn_str)."');return true;\" onmouseout=\"jlms_WStatus('');return true;\">";		
			}
			
			if(substr($btn_img, 0, 5) == 'http:' || substr($btn_img, 0, 6) == 'https:'){
				$toolbar_thml .= "<img class='JLMS_png' src=\"".$btn_img."\" width='".$btn_w."' height='".$btn_h."' border='0' alt=\"".$btn_str."\" title=\"".$btn_str."\" />";				
			} else {
				$toolbar_thml .= "<img class='JLMS_png' src=\"".$JLMS_CONFIG->getCfg('live_site').'/'.$JLMS_CONFIG->get('lms_path_to_images')."/".$btn_img."\" width='".$btn_w."' height='".$btn_h."' border='0' alt=\"".$btn_str."\" title=\"".$btn_str."\" />";				
			}
			
			$toolbar_thml .= "</a>";
			$toolbar_thml .= "</td>";
			$toolbar_thml .= "<td valign='middle' style='vertical-align:middle'>";
			if ($href_link) {
				$toolbar_thml .= "<a".$target." href=\"".$toolbar_btn['btn_js']."\" title=\"".$btn_str."\" onmouseover=\"jlms_WStatus('".addslashes($btn_str)."');return true;\" onmouseout=\"jlms_WStatus('');return true;\">";
			} else {
				$toolbar_thml .= "<a".$target." style=\"cursor:pointer\" onclick=\"".$toolbar_btn['btn_js']."\" title=\"".$btn_str."\" onmouseover=\"jlms_WStatus('".addslashes($btn_str)."');return true;\" onmouseout=\"jlms_WStatus('');return true;\">";
			}
			$toolbar_thml .= "&nbsp;".$btn_str_link."&nbsp;&nbsp;";
			$toolbar_thml .= "</a>";
			$toolbar_thml .= "</td>";
		}
		$toolbar_thml .= "</tr></table>";
	}
	
	return $toolbar_thml;
}



function JLMS_showHeadPicture( $pic_type, $path = 'headers', $size = '48' ) {
	global $JLMS_CONFIG;
	$comp_folder = 'com_joomla_lms';
	$html_output = '';
	switch ($pic_type) {
		case 'userman':
			$html_output = '<img class="JLMS_png" src="'.$JLMS_CONFIG->get('live_site').'/'.$JLMS_CONFIG->get('lms_path_to_images').'/'.$path.'/head_userman.png" width="'.$size.'" height="'.$size.'" border="0" title="'._JLMS_HEAD_USERMAN_STR.'" alt="'._JLMS_HEAD_USERMAN_STR.'" />';
		break;
		case 'chat':
			$html_output = '<img class="JLMS_png" src="'.$JLMS_CONFIG->get('live_site').'/'.$JLMS_CONFIG->get('lms_path_to_images').'/'.$path.'/head_chat.png" width="'.$size.'" height="'.$size.'" border="0" title="'._JLMS_HEAD_CHAT_STR.'" alt="'._JLMS_HEAD_CHAT_STR.'" />';
		break;
		case 'usergroup':
			$html_output = '<img class="JLMS_png" src="'.$JLMS_CONFIG->get('live_site').'/'.$JLMS_CONFIG->get('lms_path_to_images').'/'.$path.'/head_usergroup.png" width="'.$size.'" height="'.$size.'" border="0" title="'._JLMS_HEAD_USERGROUP_STR.'" alt="'._JLMS_HEAD_USERGROUP_STR.'" />';
		break;
		case 'user':
			$html_output = '<img class="JLMS_png" src="'.$JLMS_CONFIG->get('live_site').'/'.$JLMS_CONFIG->get('lms_path_to_images').'/'.$path.'/head_user.png" width="'.$size.'" height="'.$size.'" border="0" title="'._JLMS_HEAD_USER_STR.'" alt="'._JLMS_HEAD_USER_STR.'" />';
		break;
		case 'lpath':
			$html_output = '<img class="JLMS_png" src="'.$JLMS_CONFIG->get('live_site').'/'.$JLMS_CONFIG->get('lms_path_to_images').'/'.$path.'/head_lpath.png" width="'.$size.'" height="'.$size.'" border="0" title="'._JLMS_HEAD_LPATH_STR.'" alt="'._JLMS_HEAD_LPATH_STR.'" />';
		break;
		case 'scorm':
			$html_output = '<img class="JLMS_png" src="'.$JLMS_CONFIG->get('live_site').'/'.$JLMS_CONFIG->get('lms_path_to_images').'/'.$path.'/head_scorm.png" width="'.$size.'" height="'.$size.'" border="0" title="'._JLMS_HEAD_SCORM_STR.'" alt="'._JLMS_HEAD_SCORM_STR.'" />';
		break;
		case 'conference':
			$html_output = '<img class="JLMS_png" src="'.$JLMS_CONFIG->get('live_site').'/'.$JLMS_CONFIG->get('lms_path_to_images').'/'.$path.'/head_conference.png" width="'.$size.'" height="'.$size.'" border="0" title="'._JLMS_HEAD_CONF_STR.'" alt="'._JLMS_HEAD_CONF_STR.'" />';
		break;
		case 'agenda':
			$html_output = '<img class="JLMS_png" src="'.$JLMS_CONFIG->get('live_site').'/'.$JLMS_CONFIG->get('lms_path_to_images').'/'.$path.'/head_agenda.png" width="'.$size.'" height="'.$size.'" border="0" title="'._JLMS_HEAD_AGENDA_STR.'" alt="'._JLMS_HEAD_AGENDA_STR.'" />';
		break;
		case 'link':
			$html_output = '<img class="JLMS_png" src="'.$JLMS_CONFIG->get('live_site').'/'.$JLMS_CONFIG->get('lms_path_to_images').'/'.$path.'/head_link.png" width="'.$size.'" height="'.$size.'" border="0" title="'._JLMS_HEAD_LINK_STR.'" alt="'._JLMS_HEAD_LINK_STR.'" />';
		break;
		case 'docs':
			$html_output = '<img class="JLMS_png" src="'.$JLMS_CONFIG->get('live_site').'/'.$JLMS_CONFIG->get('lms_path_to_images').'/'.$path.'/head_docs.png" width="'.$size.'" height="'.$size.'" border="0" title="'._JLMS_HEAD_DOCS_STR.'" alt="'._JLMS_HEAD_DOCS_STR.'" />';
		break;
		case 'doc':
			$html_output = '<img class="JLMS_png" src="'.$JLMS_CONFIG->get('live_site').'/'.$JLMS_CONFIG->get('lms_path_to_images').'/'.$path.'/head_doc.png" width="'.$size.'" height="'.$size.'" border="0" title="'._JLMS_HEAD_DOCS_STR.'" alt="'._JLMS_HEAD_DOCS_STR.'" />';
		break;
		case 'outdocs':
			$html_output = '<img class="JLMS_png" src="'.$JLMS_CONFIG->get('live_site').'/'.$JLMS_CONFIG->get('lms_path_to_images').'/'.$path.'/head_library.png" width="'.$size.'" height="'.$size.'" border="0" title="'._JLMS_HEAD_DOCS_STR.'" alt="'._JLMS_HEAD_DOCS_STR.'" />';
		break;
		case 'outdoc':
			$html_output = '<img class="JLMS_png" src="'.$JLMS_CONFIG->get('live_site').'/'.$JLMS_CONFIG->get('lms_path_to_images').'/'.$path.'/head_outdoc.png" width="'.$size.'" height="'.$size.'" border="0" title="'._JLMS_HEAD_DOCS_STR.'" alt="'._JLMS_HEAD_DOCS_STR.'" />';
		break;
		case 'course':
			$html_output = '<img class="JLMS_png" src="'.$JLMS_CONFIG->get('live_site').'/'.$JLMS_CONFIG->get('lms_path_to_images').'/'.$path.'/head_courses.png" width="'.$size.'" height="'.$size.'" border="0" title="'._JLMS_HEAD_COURSES_STR.'" alt="'._JLMS_HEAD_COURSES_STR.'" />';
		break;
		case 'dropbox':
			$html_output = '<img class="JLMS_png" src="'.$JLMS_CONFIG->get('live_site').'/'.$JLMS_CONFIG->get('lms_path_to_images').'/'.$path.'/head_dropbox.png" width="'.$size.'" height="'.$size.'" border="0" title="'._JLMS_HEAD_DROPBOX_STR.'" alt="'._JLMS_HEAD_DROPBOX_STR.'" />';
		break;
		case 'homework':
			$html_output = '<img class="JLMS_png" src="'.$JLMS_CONFIG->get('live_site').'/'.$JLMS_CONFIG->get('lms_path_to_images').'/'.$path.'/head_homework.png" width="'.$size.'" height="'.$size.'" border="0" title="'._JLMS_HEAD_HOMEWORK_STR.'" alt="'._JLMS_HEAD_HOMEWORK_STR.'" />';
		break;
		case 'attendance':
			$html_output = '<img class="JLMS_png" src="'.$JLMS_CONFIG->get('live_site').'/'.$JLMS_CONFIG->get('lms_path_to_images').'/'.$path.'/head_attendance.png" width="'.$size.'" height="'.$size.'" border="0" title="'._JLMS_HEAD_ATTENDANCE_STR.'" alt="'._JLMS_HEAD_ATTENDANCE_STR.'" />';
		break;
		case 'tracking':
			$html_output = '<img class="JLMS_png" src="'.$JLMS_CONFIG->get('live_site').'/'.$JLMS_CONFIG->get('lms_path_to_images').'/'.$path.'/head_tracking.png" width="'.$size.'" height="'.$size.'" border="0" title="'._JLMS_HEAD_TRACKING_STR.'" alt="'._JLMS_HEAD_TRACKING_STR.'" />';
		break;
		case 'gradebook':
			$html_output = '<img class="JLMS_png" src="'.$JLMS_CONFIG->get('live_site').'/'.$JLMS_CONFIG->get('lms_path_to_images').'/'.$path.'/head_gradebook.png" width="'.$size.'" height="'.$size.'" border="0" title="'._JLMS_HEAD_GRADEBOOK_STR.'" alt="'._JLMS_HEAD_GRADEBOOK_STR.'" />';
		break;
		case 'certificate':
			$html_output = '<img class="JLMS_png" src="'.$JLMS_CONFIG->get('live_site').'/'.$JLMS_CONFIG->get('lms_path_to_images').'/'.$path.'/head_certificate.png" width="'.$size.'" height="'.$size.'" border="0" title="'._JLMS_HEAD_CERTIFICATE_STR.'" alt="'._JLMS_HEAD_CERTIFICATE_STR.'" />';
		break;
		case 'mailbox':
			$html_output = '<img class="JLMS_png" src="'.$JLMS_CONFIG->get('live_site').'/'.$JLMS_CONFIG->get('lms_path_to_images').'/'.$path.'/head_mailbox.png" width="'.$size.'" height="'.$size.'" border="0" title="'._JLMS_HEAD_MAILBOX_STR.'" alt="'._JLMS_HEAD_MAILBOX_STR.'" />';
		break;
		case 'forum':
			$html_output = '<img class="JLMS_png" src="'.$JLMS_CONFIG->get('live_site').'/'.$JLMS_CONFIG->get('lms_path_to_images').'/'.$path.'/head_forum.png" width="'.$size.'" height="'.$size.'" border="0" title="'._JLMS_HEAD_FORUM_STR.'" alt="'._JLMS_HEAD_FORUM_STR.'" />';
		break;
		case 'quiz':
			$html_output = '<img class="JLMS_png" src="'.$JLMS_CONFIG->get('live_site').'/'.$JLMS_CONFIG->get('lms_path_to_images').'/'.$path.'/head_quiz.png" width="'.$size.'" height="'.$size.'" border="0" title="'._JLMS_HEAD_QUIZ_STR.'" alt="'._JLMS_HEAD_QUIZ_STR.'" />';
		break;
		case 'subscription':
			$html_output = '<img class="JLMS_png" src="'.$JLMS_CONFIG->get('live_site').'/'.$JLMS_CONFIG->get('lms_path_to_images').'/'.$path.'/head_subscription.png" width="'.$size.'" height="'.$size.'" border="0" title="'._JLMS_HEAD_SUBSCRIPTION_STR.'" alt="'._JLMS_HEAD_SUBSCRIPTION_STR.'" />';
		break;
		case 'cart':
			$html_output = '<img class="JLMS_png" src="'.$JLMS_CONFIG->get('live_site').'/'.$JLMS_CONFIG->get('lms_path_to_images').'/'.$path.'/head_cart.png" width="'.$size.'" height="'.$size.'" border="0" title="'._JLMS_MY_CART.'" alt="'._JLMS_MY_CART.'" />';
		break;
		default:
			$html_output = '<img class="JLMS_png" src="'.$JLMS_CONFIG->get('live_site').'/'.$JLMS_CONFIG->get('lms_path_to_images').'/'.$path.'/head_error.png" width="'.$size.'" height="'.$size.'" border="0" title="'._JLMS_HEAD_UNDEFINED_STR.'" alt="'._JLMS_HEAD_UNDEFINED_STR.'" />';
		break;
	}
	return $html_output;
}


	function JLMS_showMyHomeWork($option, $Itemid, &$my_homework) {
		global $JLMS_CONFIG;
		$ret_str = '	<table width="100%" cellpadding="0" cellspacing="0" border="0">'."\r\n"
			.'		<tr><td class="sectiontableheader" align="center" colspan="2">'._JLMS_HOME_HOMEWORK_TITLE.'</td></tr>'."\r\n";
			$k = 1;
			if (count($my_homework)) {
				foreach ($my_homework as $my_hw_item) {
					$link 	= sefRelToAbs("index.php?option=".$option."&Itemid=".$Itemid."&task=".($my_hw_item->teach_id?'hw_stats':'hw_view')."&course_id=". $my_hw_item->course_id."&id=".$my_hw_item->id);
					$description = & joomla_lms_html::PrepareDescription($my_hw_item->hw_description);
					$add_info = '';
					if ( isset($my_hw_item->course_name) && $my_hw_item->course_name ) {
						$add_info = "&nbsp;(".$my_hw_item->course_name.")";
					}
					$img = 'tlb_homework.png';
					$alt = 'homework';
					if ($my_hw_item->hw_status == 1) {
						$img = 'btn_accept.png';
						$alt = 'complete';
					}
					//$title = JLMS_txt2overlib($my_hw_item->hw_name);
					$title = $my_hw_item->hw_name;
					$ret_str .=  "<tr class='sectiontableentry$k'><td width='16'>"
					."<img class='JLMS_png' src=\"".$JLMS_CONFIG->get('live_site')."/components/com_joomla_lms/lms_images/toolbar/".$img."\" width='16' height='16' border='0' alt='$alt' />"
					."</td><td>"
					.JLMS_toolTip($title, $description, $title, $link)
					.$add_info."</td></tr>";
					$k = 3 - $k;
				}
			} else {
				$ret_str .= "<tr class='sectiontableentry$k'><td colspan='2'>"._JLMS_HOME_HOMEWORK_NO_ITEMS."</td></tr>";
			}
		$ret_str .= '	</table>';
		return $ret_str;
	}

	function JLMS_showMyAgenda( $option, $Itemid, &$my_announcements ) {
		global $JLMS_CONFIG;
		$ret_str = '	<table width="100%" cellpadding="0" cellspacing="0" border="0">'."\r\n"
			.'		<tr><td class="sectiontableheader" align="center" colspan="2">'._JLMS_HOME_AGENDA_TITLE.'</td></tr>'."\r\n";
		$k = 1;
		if (count($my_announcements)) {
			foreach ($my_announcements as $my_agenda) {
				$link 	= sefRelToAbs("index.php?option=".$option."&amp;Itemid=".$Itemid."&amp;id=".$my_agenda->course_id."&amp;task=agenda&amp;agenda_id=".$my_agenda->agenda_id."&amp;date=".date('Y-m')."#anc".$my_agenda->agenda_id."-".date('Y')."-".date('m'));
				$description = & joomla_lms_html::PrepareDescription($my_agenda->content);
				$add_info = '';
				if ( isset($my_agenda->course_name) && $my_agenda->course_name ) {
					$add_info = "&nbsp;(".$my_agenda->course_name.")";
				}
				//$title = JLMS_txt2overlib($my_agenda->title);
				$title = $my_agenda->title;
				$ret_str .= "<tr class='sectiontableentry$k'><td width='16'>"
				."<img class='JLMS_png' src=\"".$JLMS_CONFIG->get('live_site')."/components/com_joomla_lms/lms_images/toolbar/tlb_agenda.png\" width='16' height='16' border='0' alt='agenda' />"
				."</td><td>"
				.JLMS_toolTip($title, $description, $title, $link)
				.$add_info."</td></tr>";
				$k = 3 - $k;
			}
		} else {
			$ret_str .= "<tr class='sectiontableentry$k'><td colspan='2'>"._JLMS_HOME_AGENDA_NO_ITEMS."</td></tr>";
		}
		$ret_str .= '	</table>';
		return $ret_str;
	}

	function JLMS_showMyDropBox( $option, $Itemid, &$my_dropbox, &$lists ) {
		global $JLMS_CONFIG;
		$ret_str = '	<table width="100%" cellpadding="0" cellspacing="0" border="0">'."\r\n"
			.'		<tr><td class="sectiontableheader" align="center" colspan="2">'.str_replace('{Y}', $lists['dropbox_total'], str_replace('{X}', $lists['dropbox_total_new'], _JLMS_HOME_DROPBOX_TITLE)).'</td></tr>'."\r\n";
			$k = 1;
			if (count($my_dropbox)) {
				foreach ($my_dropbox as $my_dropboxitem) {
					$link 	= sefRelToAbs("index.php?option=".$option."&Itemid=".$Itemid."&task=dropbox&id=". $my_dropboxitem->course_id);
					$description = & joomla_lms_html::PrepareDescription($my_dropboxitem->drp_description);
					$add_info = '';
					if ( isset($my_dropboxitem->course_name) && $my_dropboxitem->course_name ) {
						$add_info = "&nbsp;(".$my_dropboxitem->course_name.")";
					}
					//$title = JLMS_txt2overlib($my_dropboxitem->drp_name);
					$title = $my_dropboxitem->drp_name;					
					$ret_str .= "<tr class='sectiontableentry$k'><td width='16'>"
					."<img class='JLMS_png' src=\"".$JLMS_CONFIG->get('live_site')."/components/com_joomla_lms/lms_images/toolbar/tlb_dropbox.png\" width='16' height='16' border='0' alt='dropbox' />"
					."</td><td>"
					.JLMS_toolTip($title, $description, $title, $link)
					.$add_info."</td></tr>";
					$k = 3 - $k;
				}
			} else {
				$ret_str .= "<tr class='sectiontableentry$k'><td colspan='2'>"._JLMS_HOME_DROPBOX_NO_ITEMS."</td></tr>";
			}
		$ret_str .= '	</table>';
		return $ret_str;
	}
	
	function JLMS_showMyMailBox($option, $Itemid, &$my_mailbox, &$lists){
		global $JLMS_CONFIG;
		
		$unread = 0;
		$all = count($my_mailbox);
		if(count($my_mailbox)){
			foreach($my_mailbox as $m){
				if(!$m->is_read){
					$unread++;
				}
			}
		}
		
		ob_start();
		?>
		<table width="100%" cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td class="sectiontableheader" align="center" colspan="2">
					<?php echo str_replace("( X / Y )", "( ".$unread." / ".$all." )", _JLMS_HOME_MAILBOX_TITLE);?>
				</td>
			</tr>
			<?php
			$k=1;
			if(count($my_mailbox)){
				foreach($my_mailbox as $my_mailboxitem){
					
					$subject = $my_mailboxitem->subject;
					$from = _JLMS_HOME_MAILBOX_FROM.': '.$my_mailboxitem->from_name.' ('.$my_mailboxitem->from_username.')';
					
					$link = 'index.php?option='.$option.'&task=mail_view';
					$link .= $my_mailboxitem->course_id ? '&id='.$my_mailboxitem->course_id : '';
					$link .= '&view_id='.$my_mailboxitem->id;
					$link .= '&Itemid='.$Itemid;
					
					$link = JRoute::_($link);
					
					if($my_mailboxitem->is_read){
						$img = 'btn_drp_readed.png';
					} else {
						$img = 'btn_drp_unreaded.png';
					}
					?>
					<tr class="sectiontableentry<?php echo $k;?>">
						<td width="16">
							<img class="JLMS_png" src="<?php echo $JLMS_CONFIG->get('live_site')."/components/com_joomla_lms/lms_images/toolbar/".$img;?>" width='16' height='16' border='0' alt='mailbox' />
						</td>
						<td>
							<?php echo JLMS_toolTip($subject, $from, $subject, $link);?>
						</td>
					</tr>
					<?php
					$k = 3 - $k;
				}
			} else {
				?>
				<tr class="sectiontableentry<?php echo $k;?>">
					<td colspan="2">
						<?php echo _JLMS_HOME_MAILBOX_NO_ITEMS;?>
					</td>
				</tr>
				<?php
			}
			?>
		</table>
		<?php
		$ret_str = ob_get_contents();
		ob_get_clean();
		return $ret_str;
	}
	
	function JLMS_showMyCertificates($option, $Itemid, &$my_certificates, &$lists){
		global $JLMS_CONFIG;
		
		ob_start();
		?>
		<table width="100%" cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td class="sectiontableheader" align="center" colspan="2">
					<?php echo _JLMS_HOME_CERTIFICATES_TITLE;?>
				</td>
			</tr>
			<?php
			$k=1;
			if(count($my_certificates)){
				foreach($my_certificates as $my_certificateitem){
					
					$subject = _JLMS_DATE.' '.JLMS_dateToDisplay($my_certificateitem->crt_date);
					$from = '';
					if(isset($my_certificateitem->uniq_id) && $my_certificateitem->uniq_id){
						$from = strtoupper($my_certificateitem->uniq_id);
						$from = _JLMS_HOME_CERTIFICATES_SN.': <b>'.$from.'</b>';
					}
					$in_tag = $my_certificateitem->course_name;
					
					$link = 'index.php?option='.$option.'&task=gradebook';
					$link .= $my_certificateitem->course_id ? '&id='.$my_certificateitem->course_id : '';
					$link .= '&Itemid='.$Itemid;
					$link = JRoute::_($link);
					
					$img = 'btn_certificate.png';
					?>
					<tr class="sectiontableentry<?php echo $k;?>">
						<td width="16">
							<img class="JLMS_png" src="<?php echo $JLMS_CONFIG->get('live_site')."/components/com_joomla_lms/lms_images/toolbar/".$img;?>" width='16' height='16' border='0' alt='certificate' />
						</td>
						<td>
							<?php echo JLMS_toolTip($subject, $from, $in_tag, $link);?>
						</td>
					</tr>
					<?php
					$k = 3 - $k;
				}
			} else {
				?>
				<tr class="sectiontableentry<?php echo $k;?>">
					<td colspan="2">
						<?php echo _JLMS_HOME_CERTIFICATES_NO_ITEMS;?>
					</td>
				</tr>
				<?php
			}
			?>
		</table>
		<?php
		$ret_str = ob_get_contents();
		ob_get_clean();
		return $ret_str;
	}
	
	function JLMS_showLatestForumPosts($option, $Itemid, &$latest_forum_posts, &$lists){
		global $JLMS_CONFIG;
		
		ob_start();
		?>
		<table width="100%" cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td class="sectiontableheader" align="center" colspan="2">
					<?php echo _JLMS_HOME_LATEST_FORUM_POSTS_TITLE;?>
				</td>
			</tr>
			<?php
			$k=1;
			if(count($latest_forum_posts)){
				foreach($latest_forum_posts as $lfpitem){
					
					$subject = $lfpitem->subject;
					$body = '';
					if(isset($lfpitem->posterTime) && $lfpitem->posterTime){
						$body = _JLMS_DATE.' '.JLMS_dateToDisplay(date("Y-m-d H:i:s", $lfpitem->posterTime));
					}
					
					$body .= "<br />"._JLMS_COURSE.': '.$lfpitem->course_name;
					$body .= "<br />"._JLMS_TOPIC.': '.$lfpitem->topic_name;
					$body .= "<br />"._JLMS_AUTHOR.': '.$lfpitem->poster_name;
					
					$in_tag = strlen($lfpitem->body) > 90 ? substr($lfpitem->body, 0, 90).'...' : $lfpitem->body;				
					
					$link = 'index.php?option='.$option.'&task=course_forum';
					$link .= isset($lfpitem->course_id) && $lfpitem->course_id ? '&id='.$lfpitem->course_id : '';
					$link .= '&topic_id='.$lfpitem->ID_TOPIC;
					$link .= '&message_id='.$lfpitem->ID_MSG;
					$link = JRoute::_($link);
					
					$img = 'btn_notice.png';
					?>
					<tr class="sectiontableentry<?php echo $k;?>">
						<td width="16">
							<img class="JLMS_png" src="<?php echo $JLMS_CONFIG->get('live_site')."/components/com_joomla_lms/lms_images/toolbar/".$img;?>" width='16' height='16' border='0' alt='certificate' />
						</td>
						<td>
							<?php echo JLMS_toolTip($subject, $body, $in_tag, $link, 1, 120, 'false', 'jlms_ttip_posts');?>
						</td>
					</tr>
					<?php
					$k = 3 - $k;
				}
			} else {
				?>
				<tr class="sectiontableentry<?php echo $k;?>">
					<td colspan="2">
						<?php echo _JLMS_HOME_LATEST_FORUM_POSTS_NO_ITEMS;?>
					</td>
				</tr>
				<?php
			}
			?>
		</table>
		<?php
		$ret_str = ob_get_contents();
		ob_get_clean();
		return $ret_str;
	}


function JLMS_showPageImageHeaderMenu( $option, $course_id, $head_pic, $head_title ) {
?>
	<table id='jlms_header_table' width="100%" cellpadding="0" cellspacing="0" border="0">
		<tr>
			<td width="48">
				<?php echo JLMS_showHeadPicture($head_pic);?>
			</td>
			<td class="contentheading" width="100%" valign="middle" style="vertical-align:middle ">
				&nbsp;<?php echo $head_title;?>
			</td>
			<td align="right" valign="top" style="vertical-align:top ">
				<?php JLMS_showTopMenu( $course_id, $option );?>
			</td>
		</tr>
	</table>
<?php
}
class JLMS_TMPL {

	function OpenMT($class = 'contentpane', $style = '', $id = 'jlms_mainarea') {
		static $id_index = 0;
		if ($id == 'jlms_mainarea') {
			if ($id_index) {
				$id = 'jlms_mainarea-'.$id_index;//aoid duplicated ids
			}
			$id_index++;
		}
		echo '<table'.($class?(' class="'.$class.'"'):'').($id?(' id="'.$id.'"'):'').($style?(' style="'.$style.'"'):'').' style="width:100%" cellpadding="0" cellspacing="0" border="0">'."\r\n";
	}

	function OpenTS($tr_attrib = '', $td_attrib = '') {
		echo '<tr'.$tr_attrib.'>'."\r\n"
			.'	<td'.$td_attrib.'>'."\r\n";
	}

	function CloseTS() {
		echo '	</td>'."\r\n"
			.'</tr>'."\r\n";
	}

	function CloseMT() {
		echo '</table>'."\r\n";
	}

	function ShowPageTip($page) {
		global $JLMS_DB;
		$query = "SELECT tip_message FROM #__lms_page_tips WHERE tip_task = ".$JLMS_DB->Quote($page);
		$JLMS_DB->SetQuery($query);
		$tip = $JLMS_DB->LoadResult();
		if ($tip) {
			JLMS_TMPL::OpenTS();
			JLMS_TMPL::RenderPageTip($tip);
			JLMS_TMPL::CloseTS();
		}
	}

	function RenderPageTip($tip) {
		if ($tip) {
			echo '<div class="joomlalms_page_tip">'.$tip.'</div>';
		}
	}

	function ShowHeader($head_pic, $head_title = '', $params = array(), $toolbar = array(),$gqp = false) {
		
		global $JLMS_CONFIG, $JLMS_SESSION, $option;
		// set default parameters values
		$with_sys_msg = true;
		$sys_msg = '';
		$simple_menu = false;
		$show_menu = true;
		$toolbar_s = '';
		$toolbar_pos = '';
		$add_html_code = '';
		$output_blank_sys_msg_container = true;
		//$JLMS_CONFIG->set('start_menu_new', true);
		$second_tb_header = '';
		$add_html_tb_code = '';
		$course_id = $JLMS_CONFIG->get('course_id');
		// redefine parameters from $params variable.
		if (isset($params['with_sys_msg'])) $with_sys_msg = $params['with_sys_msg'];
		if (isset($params['sys_msg'])) $sys_msg = $params['sys_msg'];
		if (isset($params['toolbar_position'])) $toolbar_pos = $params['toolbar_position'];
		if (isset($params['show_menu'])) $show_menu = $params['show_menu'];
		if (isset($params['simple_menu'])) $simple_menu = $params['simple_menu'];
		if (isset($params['second_tb_header'])) $second_tb_header = $params['second_tb_header'];
		if (isset($params['toolbar'])) $toolbar_s = $params['toolbar'];
		if (isset($params['add_html_code'])) $add_html_code = $params['add_html_code'];
		if (isset($params['html_code_before_toolbar'])) $add_html_tb_code = $params['html_code_before_toolbar'];
		if (isset($params['output_blank_sys_msg_container'])) $output_blank_sys_msg_container = $params['output_blank_sys_msg_container'];

		if ($show_menu) {
			if (!$JLMS_CONFIG->get('lofe_show_top', true) && !$JLMS_CONFIG->get('lofe_show_course_box', true)) {
				$show_menu = 0;
				$simple_menu = 0;
			}
		}
		$skin = 'nopro';//'standart';
		//$skin = 'pro';
		if ($simple_menu) {
			if (!$JLMS_CONFIG->get('lofe_show_top', true)) {
				$simple_menu = 0;
				$show_menu = 0;
			}
		}

		$is_nopro_toolbar_shown = false;

		if ($skin == 'pro') {
			
			/*if ($show_menu) {
				JLMS_TMPL::OpenTS('', ' width="100%"');
				JLMS_showTopMenu( $course_id, $option );
				JLMS_TMPL::CloseTS();
			}*/
			JLMS_TMPL::OpenTS('', ' width="100%"');
			echo '		<table id="jlms_header_table" width="100%" cellpadding="0" cellspacing="0" border="0">'."\r\n"
				.'			<tr>'."\r\n"
				.'				<td width="48" valign="top" style="vertical-align:top">'."\r\n"
				. JLMS_showHeadPicture($head_pic) . "\r\n"
				.'				</td>'."\r\n";
			if ($show_menu) {
				
			
				if ($simple_menu) {
					echo '				<td class="contentheading" width="100%" valign="middle" style="vertical-align:middle">'."\r\n"
						.'					&nbsp;'.($head_title ? ($JLMS_CONFIG->get('additional_heading_tag_open').$head_title.$JLMS_CONFIG->get('additional_heading_tag_close')) : '')."\r\n"
						.'				</td>'."\r\n";

					$menus = $JLMS_CONFIG->get('jlms_menu');
					echo '				<td align="right"'.($add_html_code?' colspan="2"':'').' valign="top"'.($simple_menu?' nowrap="nowrap"':'').' style="vertical-align:top ">'."\r\n";
					//JLMS_showTopMenu_simple($option);
					JLMS_TMPL::showTopMenu_simple( $menus,$option, $skin );
				} else {
					echo '				<td align="right"'.($add_html_code?' colspan="2"':'').' valign="top"'.($simple_menu?' nowrap="nowrap"':'').' style="vertical-align:top ">'."\r\n";
					$menus = $JLMS_CONFIG->get('jlms_menu');
					JLMS_TMPL::showTopMenu( $menus, $course_id, $option, $skin );
					/*echo '<div style="width:100%; text-alignment: center;" class="jlms_controls_footer">';
					JLMS_TMPL::showMenuToolTips();
					echo '</div>';*/
				}
				echo '				</td>'."\r\n";
				if (!$simple_menu) {
					echo '</tr><tr><td colspan="2" width="100%"><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr>';
					if (empty($toolbar)) {
						echo '				<td class="contentheading" width="100%" valign="middle" style="vertical-align:middle">'."\r\n"
							.'					&nbsp;'.($head_title ? ($JLMS_CONFIG->get('additional_heading_tag_open').$head_title.$JLMS_CONFIG->get('additional_heading_tag_close')) : '')."\r\n"
							.'				</td>'."\r\n";
					} else {
						echo '<td>&nbsp;</td>';
					}
				}
				if ($add_html_code) {
					echo '				<td align="right">'."\r\n";
					echo $add_html_code;
					echo '				</td>'."\r\n";
				}
				if (!$simple_menu) {
					if ($JLMS_CONFIG->get('lofe_show_course_box', true)) {
						echo '<td nowrap="nowrap" valign="top" align="right" style="vertical-align:top; text-align:right">';
							if (/*$JLMS_CONFIG->get('lofe_box_type',1)*/false) {
								JLMS_TMPL::ShowSelectCourseSB($course_id);
							} else {
								JLMS_TMPL::ShowSelectCourseSB_form($course_id);
							}
						echo '</td>';
					}
				}
			} else {
				
				$at_first_row = true;
				echo '				<td class="contentheading" width="100%" valign="middle" style="vertical-align:middle">'."\r\n"
					.'					&nbsp;'.($head_title ? ($JLMS_CONFIG->get('additional_heading_tag_open').$head_title.$JLMS_CONFIG->get('additional_heading_tag_close')) : '')."\r\n"
					.'				</td>'."\r\n";
				if ($toolbar_s) {
					echo '				<td align="right"'.($add_html_code?' colspan="2"':'').'>'."\r\n";
					echo JLMS_ShowToolbar($toolbar_s);
					echo '				</td>'."\r\n";
				}
			}
			if (!$simple_menu && $show_menu) {
				echo '			</tr></table></td></tr>'."\r\n";
			} else {
				echo '			</tr>'."\r\n";
			}
			echo '		</table>'."\r\n";
		} else {
			

			JLMS_TMPL::OpenTS('', ' width="100%"');
			echo '		<table id="jlms_header_table" width="100%" cellpadding="0" cellspacing="0" border="0">'."\r\n"
				.'			<tr>'."\r\n"
				.'				<td width="48">'."\r\n"
				. JLMS_showHeadPicture($head_pic) . "\r\n"
				.'				</td>'."\r\n"
				.'				<td class="contentheading" width="100%" valign="middle" style="vertical-align:middle">'."\r\n"
				.'					&nbsp;'.($head_title ? ($JLMS_CONFIG->get('additional_heading_tag_open').$head_title.$JLMS_CONFIG->get('additional_heading_tag_close')) : '')."\r\n"
				.'				</td>'."\r\n";
			if ($add_html_code) {
				echo '				<td align="right">'."\r\n";
				echo $add_html_code;
				echo '				</td>'."\r\n";
			}
			if ($show_menu) {
				echo '				<td align="right" valign="top"'.($simple_menu?' nowrap="nowrap"':'').' style="vertical-align:top ">'."\r\n";
				if ($simple_menu) {
					JLMS_showTopMenu_simple($option);
				} else {
					JLMS_showTopMenu( $course_id, $option, false,'','',$gqp );
				}
				echo '				</td>'."\r\n";
			} elseif ($toolbar_s) {
				echo '				<td align="right">'."\r\n";
				echo JLMS_ShowToolbar($toolbar_s);
				echo '				</td>'."\r\n";
			} elseif (!empty($toolbar) && $toolbar_pos != 'center') {
				echo '				<td align="right" nowrap="nowrap">'."\r\n";
				echo JLMS_ShowToolbar($toolbar);
				echo '				</td>'."\r\n";
				$is_nopro_toolbar_shown = true;
				/*JLMS_TMPL::ShowToolbar($toolbar, ($toolbar_pos?$toolbar_pos:'right'), true, $second_tb_header, 2 );
				$is_nopro_toolbar_shown = true;*/
			}
			echo '			</tr>'."\r\n"
				.'		</table>'."\r\n";
				
		}

		if (!$simple_menu) {
			if ($show_menu) {
				JLMS_TMPL::ShowPlugins('star_menu');
			}
		}

		JLMS_TMPL::CloseTS();
		if ($with_sys_msg) {
			if (!$sys_msg) {
				$sys_msg = $JLMS_SESSION->get('joomlalms_sys_message');
			}
			$JLMS_SESSION->clear('joomlalms_sys_message');
			$tr_attrib = ' id="joomlalms_sys_message_container"';
			if (!$sys_msg) {
				$tr_attrib .= ' style="visibility:hidden;display:none"';
			}
			if ($sys_msg || $output_blank_sys_msg_container) {
				JLMS_TMPL::OpenTS($tr_attrib);
				echo '		<div class="'.$JLMS_CONFIG->get('system_message_css_class', 'joomlalms_sys_message').'" id="joomlalms_sys_message" style="text-align:center">'.$sys_msg.'</div>'."\r\n";
				JLMS_TMPL::CloseTS();
			}

			$task = strval(mosGetParam($_REQUEST, 'task', ''));
			if ($task) {
				$sys_msgs = $JLMS_CONFIG->get('system_messages', array());
				if (!empty($sys_msgs)) {
					foreach ($sys_msgs as $sysmsg) {
						if (isset($sysmsg['message']) && $sysmsg['message'] && isset($sysmsg['task']) && $sysmsg['task'] == $task && ( ( isset($sysmsg['course']) && $sysmsg['course'] == $JLMS_CONFIG->get('course_id')) || empty($sysmsg['course']) ) ) {
							JLMS_TMPL::OpenTS();
							echo '		<div class="'.$JLMS_CONFIG->get('system_message_css_class', 'joomlalms_sys_message').'"'.((isset($sysmsg['align']) && $sysmsg['align'])?(' style="text-align:'.$sysmsg['align'].'"'):'').'>'.$sysmsg['message'].'</div>'."\r\n";
							JLMS_TMPL::CloseTS();
						}
					}
					/*$sysmsg_var = $task;
					if (isset($sys_msgs[$sysmsg_var]) && $sys_msgs[$sysmsg_var]) {
						JLMS_TMPL::OpenTS();
						echo '		<span class="'.$JLMS_CONFIG->get('system_message_css_class', 'joomlalms_sys_message').'">'.$sys_msgs[$sysmsg_var].'</span>'."\r\n";
						JLMS_TMPL::CloseTS();
					}*/
				}
			}
		}

		if ($add_html_tb_code) {
			JLMS_TMPL::OpenTS();
				echo $add_html_tb_code;
			JLMS_TMPL::CloseTS();
		}

		if (!empty($toolbar)/*) {//}*/ && !$is_nopro_toolbar_shown) {
			if ($skin == 'pro') {
				$second_tb_header = $head_title ? $head_title : $second_tb_header;
			}
			JLMS_TMPL::ShowToolbar($toolbar, ($toolbar_pos?$toolbar_pos:'right'), true, $second_tb_header, 2 );
		}
	}

	// (DEN) don't use this method outside this class !!!
	function ShowToolbar($toolbar, $align='right', $href_link = true, $heading = '', $pad = 0 ) {
		if ($heading) {
			if (!empty($toolbar)) {
				JLMS_TMPL::OpenTS();
				echo '		<table width="100%" cellpadding="0" cellspacing="0" border="0" class="jlms_table_no_borders">'."\r\n"
				.'			<tr>'."\r\n"
				.'				<td width="100%" valign="middle" style="vertical-align:middle">'."\r\n"
				.'					&nbsp;'.($heading ? JLMSCSS::h2($heading) : '')."\r\n"
				.'				</td>'."\r\n"
				. '				<td align="right" style="text-align:right; white-space:nowrap " nowrap="nowrap">'."\r\n";
				echo JLMS_ShowToolbar($toolbar, $href_link, $align);
				echo '				</td>'."\r\n";
				echo '			</tr>'."\r\n"
				.'		</table>'."\r\n";
			} else {
				JLMS_TMPL::OpenTS('','  class="contentheading"');
				echo $heading;
			}
		} else {
			$td_style = '';
			if ($pad) {
				$td_style = ' style="padding-top:'.$pad.'px; padding-bottom:'.$pad.'px"'.(($align == 'center') ? ' align="center"' : '');
			}
			JLMS_TMPL::OpenTS('',$td_style);
			echo JLMS_ShowToolbar($toolbar, $href_link, $align);
		}
		JLMS_TMPL::CloseTS();
	}

	function ShowSysMessage($sys_msg = '', $align = '') {
		global $JLMS_CONFIG, $JLMS_SESSION;
		if (!$sys_msg) {
			$sys_msg = $JLMS_SESSION->get('joomlalms_sys_message');
			$JLMS_SESSION->clear('joomlalms_sys_message');
			$align = 'center';
		}
		if ($sys_msg) {
			JLMS_TMPL::OpenTS();
			echo '		<div class="'.$JLMS_CONFIG->get('system_message_css_class', 'joomlalms_sys_message').'"'.($align?(' style="text-align:'.$align.'"'):'').'>'.$sys_msg.'</div>'."\r\n";
			JLMS_TMPL::CloseTS();
		}
	}

	function ShowSection(&$text) {
		if ($text) {
			JLMS_TMPL::OpenTS();
			echo $text;
			JLMS_TMPL::CloseTS();
		}
	}

	function ShowCustomSection(&$sections, $cpad = '0', $cspc = '0') {
		if (!empty($sections)){
			$col_sections = count($sections);
			//show max 5 sections;
			$sec_widths = array();
			$sec_widths[] = array('100%');
			$sec_widths[] = array('50%', '50%');
			$sec_widths[] = array('33%', '34%', '33%');
			$sec_widths[] = array('25%', '25%', '25%', '25%');
			$sec_widths[] = array('20%', '20%', '20%', '20%', '20%');
			
//			if ($col_sections > 5) { $col_sections = 5; }
			
			$f2 = $col_sections%2;
			$f3 = $col_sections%3;
			
			if($f2 < $f3){
				$po = 2;
			} else 
			if($f2 > $f3){
				$po = 3;
			} else 
			if($f2 == $f3){
				$po = 3;
			}
			if ($col_sections) {
				JLMS_TMPL::OpenTS();
				$p = 0;
				$i = 0;
				while ($i < $col_sections) {
					$ost = $col_sections - $i;
					if($po == 2 && $f2 == 1 && $ost == 3){
						$po = 3;
						
						echo '		</table>'."\r\n";
						echo '		<table width="100%" class="jlms_table_no_borders" cellpadding="'.$cpad.'" cellspacing="'.$cspc.'" border="0">'."\r\n";
					}
					if($i == 0){
						echo '		<table width="100%" class="jlms_table_no_borders" cellpadding="'.$cpad.'" cellspacing="'.$cspc.'" border="0">'."\r\n";
					}
					if($p == 0){
						echo '			<tr>'."\r\n";
					}
					$width = '';
					if(true){
						$width .= 'width="'.$sec_widths[($po-1)][$p].'"';
					}
						echo '				<td'.((isset($sections[$i]['attrib']) && $sections[$i]['attrib'])?$sections[$i]['attrib']:'').' '.$width.'>'."\r\n";
						echo $sections[$i]['text'];
						echo '				</td>'."\r\n";
						$i++;
						$p++;
					if($p == $po){
						echo '			</tr>'."\r\n";
						$p = 0;
					}
					if($i == ($col_sections)){
						echo '		</table>'."\r\n";
					}
				}
				JLMS_TMPL::CloseTS();
			}
		}
	}

	function ShowControlsFooter($controls, $back_link = '', $top_link = true, $full_path = false, $pretext = '') {
		global $JLMS_CONFIG;
		if (!empty($controls)) {
			JLMS_TMPL::OpenTS();
			JLMS_TMPL::ShowControlsFooterC($controls, $back_link, $top_link, $full_path, $pretext);
			JLMS_TMPL::CloseTS();
		}
	}

	function ShowControlsFooterC($controls, $back_link = '', $top_link = true, $full_path = false, $pretext = '', $text_only = false) {
		global $JLMS_CONFIG;
		static $rrr;
		static $is_toplink_api_loaded;
		if (!empty($controls)) {
			echo '		<div class="jlms_controls_footer">';

			echo '<table cellpadding="2" cellspacing="1" border="0" style="float:left; width:auto;"><tr>';

			if (!$rrr) {
				$rrr = 10;
			}
			$rrr++;
			//$rrr = rand(10,100);
			if ($pretext) {
				echo '<td>'.$pretext.'</td>';
			}
			$is_nojs = false;
			foreach ($controls as $control) {
				$new_control = $control;
				if (!$full_path) {
					$new_control['img'] = 'buttons_22/btn_'.(isset($control['img'])?$control['img']:'').'_22.png';
				}
				if (!$new_control['href'] && isset($new_control['custom']) && $new_control['custom']) {
					echo '<td>'.$new_control['custom'].'</td>';
				} else {
					if ($text_only) {
						$new_control['img'] = '';
						$new_control['text_only'] = $new_control['title'];
					}
					JLMS_TMPL::ShowControl($new_control, $rrr);
					if (isset($new_control['noscript']) && $new_control['noscript']) {
						$is_nojs = true;
					}
				}
			}

			if ($JLMS_CONFIG->get('web20_effects', true)) {
				echo '<td width="8"><!--x--></td><td nowrap="nowrap" style="vertical-align:middle"><span id="jlms_footer_txt'.$rrr.'"><!--x--></span></td>';
			}
			echo '</tr></table>';

			if ($JLMS_CONFIG->get('web20_effects', true) && $top_link) {
				echo '<div class="jlms_footer_link"><a class="jlms_toTop" href="javascript:void(0);">'._JLMS_TXT_TOP.'</a></div>';
				if (!$is_toplink_api_loaded) {
					$domready = '
						var winScroller = new Fx.Scroll(window);
						$$(\'a.jlms_toTop\').each(function(ael){
							ael.addEvent(\'click\', function(){
								winScroller.toTop();
							});
						});';
					$JLMS_CONFIG->set('web20_domready_code', $JLMS_CONFIG->get('web20_domready_code','').$domready);
					$is_toplink_api_loaded = true;
				}
			}
			if ($JLMS_CONFIG->get('web20_effects', true)) {
				
				if( JLMS_mootools12() ) {					
					$domready = '
						var is_mlf'.$rrr.' = false;
						var fx_op'.$rrr.' = new Fx.Tween(\'jlms_footer_txt'.$rrr.'\', {property: \'opacity\'}).set(0);
						var fx_txt_cont'.$rrr.' = $(\'jlms_footer_txt'.$rrr.'\');
						$$(\'a.jlms_footer_control'.$rrr.'\').each(function(aelfc){
							aelfc.addEvent(\'mouseenter\', function(){
									is_mlf'.$rrr.' = false;
									fx_txt_cont'.$rrr.'.set(\'html\',this.getElement(\'img\').getProperty(\'alt\'));									
									fx_op'.$rrr.'.cancel().start(0,1).chain(function(){if (is_mlf'.$rrr.') { fireEvent(\'mouseleave\', aelfc);}});
							});
							aelfc.addEvent(\'mouseleave\', function(){
									is_mlf'.$rrr.' = true;																											
									fx_op'.$rrr.'.cancel().start(1,0);
							});
							aelfc.addEvent(\'blur\', function(){
									is_mlf'.$rrr.' = true;																																
									fx_op'.$rrr.'.cancel().start(1,0);
							});
							aelfc.addEvent(\'focus\', function(){
									is_mlf'.$rrr.' = false;
									fx_txt_cont'.$rrr.'.set(\'html\', this.getElement(\'img\').getProperty(\'alt\'));																	
									fx_op'.$rrr.'.cancel().start(0,1).chain(function(){if (is_mlf'.$rrr.') { fireEvent(\'mouseleave\', aelfc);}});
							});
						});
					';
				} else {					
					$domready = '
						var is_mlf'.$rrr.' = false;
						var fx_op'.$rrr.' = new Fx.Style(\'jlms_footer_txt'.$rrr.'\', \'opacity\').set(0);
						var fx_txt_cont'.$rrr.' = $(\'jlms_footer_txt'.$rrr.'\');
						$$(\'a.jlms_footer_control'.$rrr.'\').each(function(aelfc){
							aelfc.addEvent(\'mouseenter\', function(){
									is_mlf'.$rrr.' = false;
									fx_txt_cont'.$rrr.'.setHTML(this.getElement(\'img\').getProperty(\'alt\'));
									fx_op'.$rrr.'.stop();
									fx_op'.$rrr.'.start(0,1).chain(function(){if (is_mlf'.$rrr.') { fireEvent(\'mouseleave\', aelfc);}});
							});
							aelfc.addEvent(\'mouseleave\', function(){
									is_mlf'.$rrr.' = true;
									fx_op'.$rrr.'.stop();
									fx_op'.$rrr.'.start(1,0);
							});
							aelfc.addEvent(\'blur\', function(){
									is_mlf'.$rrr.' = true;
									fx_op'.$rrr.'.stop();
									fx_op'.$rrr.'.start(1,0);
							});
							aelfc.addEvent(\'focus\', function(){
									is_mlf'.$rrr.' = false;
									fx_txt_cont'.$rrr.'.setHTML(this.getElement(\'img\').getProperty(\'alt\'));
									fx_op'.$rrr.'.stop();
									fx_op'.$rrr.'.start(0,1).chain(function(){if (is_mlf'.$rrr.') { fireEvent(\'mouseleave\', aelfc);}});
							});
						});
					';
				}
			
				$JLMS_CONFIG->set('web20_domready_code', $JLMS_CONFIG->get('web20_domready_code','').$domready);

			}
			if ($back_link) {
				echo '<div class="jlms_footer_link"><a href="'.$back_link.'">'._JLMS_TXT_BACK.'</a></div>';
			}

			echo '<div style="clear:both"><!--x--></div>';
			if ($is_nojs) {
				echo "<script type=\"text/javascript\">if (getObj('nojs_control".$rrr."')) {getObj('nojs_control".$rrr."').style.display = '';getObj('nojs_control".$rrr."').style.visibility = 'visible';}</script>";
			}
			echo '		</div>';
		}
	}

	function ShowControl( &$control, $rrr = '') {
		global $JLMS_CONFIG;
		if ($control['href'] == 'spacer') {
			echo '<td width="8"><!--x--></td>';
		} else {
			echo '<td width="22">';
			$display = '';
			if (isset($control['noscript']) && $control['noscript']) {
				echo '<noscript><input class="jlms_footer_control'.$rrr.'" type="image" name="'.((isset($control['name']) && $control['name'])?$control['name']:'submit').'" value="1" src="'.$JLMS_CONFIG->get('live_site').'/components/com_joomla_lms/lms_images/'.$control['img'].'" title="'.$control['title'].'" /></noscript>';
				$display = ' style= "display:none; visibility:hidden" id="nojs_control'.$rrr.'"';
			}
			if ($JLMS_CONFIG->get('web20_effects', true)) { // without 'titles'; only 'alt' for image
				if (isset($control['text_only']) && $control['text_only'] && !$control['img']) {
					echo '<a class="jlms_footer_control'.$rrr.'"'.$display.' href="'.$control['href'].'" '.(!empty($control['onclick'])?('onclick="'.$control['onclick'].'" '):'').'>'.$control['text_only'].'</a>';
				} else {
					echo '<a class="jlms_img_link jlms_footer_control'.$rrr.'"'.$display.' href="'.$control['href'].'" '.(!empty($control['onclick'])?('onclick="'.$control['onclick'].'" '):'').'><img class="JLMS_png" src="'.$JLMS_CONFIG->get('live_site').'/components/com_joomla_lms/lms_images/'.$control['img'].'" width="22" height="22" border="0" alt="'.$control['title'].'" /></a>';
				}
			} else {
				echo '<a class="jlms_img_link jlms_footer_control'.$rrr.'"'.$display.' href="'.$control['href'].'" '.(!empty($control['onclick'])?('onclick="'.$control['onclick'].'" '):'').'title="'.$control['title'].'"><img class="JLMS_png" src="'.$JLMS_CONFIG->get('live_site').'/components/com_joomla_lms/lms_images/'.$control['img'].'" width="22" height="22" border="0" alt="'.$control['title'].'" title="'.$control['title'].'" /></a>';
			}
			echo '</td>';
		}
	}

	function ShowPlugins($plugin = '') {
		global $JLMS_CONFIG;
		if ($plugin == 'star_menu') {
			$id = $JLMS_CONFIG->get('course_id', 0);
			global $Itemid, $option;
			?>
			<script type="text/javascript">
			<!--
			function jlms_changeLang() {
				var user_lang = $('jlms_lang').value;
				window.top.location.href='<?php echo str_replace('__lang__', "'+user_lang+'", $JLMS_CONFIG->getCfg('live_site')."/index.php?option=$option&Itemid=$Itemid&task=user_lang&course_id=$id&lang=__lang__")?>';
			}
			function jlms_changeRole() {
				var user_role = $('jlms_user_role').value;
				if (user_role == 1) {
					window.top.location.href='<?php echo $JLMS_CONFIG->getCfg('live_site')."/index.php?option=$option&Itemid=$Itemid&task=to_teacher&id=$id";?>';
				} else if (user_role == 2) {
					window.top.location.href='<?php echo $JLMS_CONFIG->getCfg('live_site')."/index.php?option=$option&Itemid=$Itemid&task=to_student&id=$id";?>';
				} else if (user_role == 6) {
					window.top.location.href='<?php echo $JLMS_CONFIG->getCfg('live_site')."/index.php?option=$option&Itemid=$Itemid&task=to_ceo&id=$id";?>';
				}
			}
			-->
			</script>
<?php
			echo '<div id="joomlalms_star_menu" style="visibility:hidden; display:none;">';
			echo '<div class="jlms_plugin" >';
			echo _JLMS_UO_SELECT_LANGUAGE;
			echo JLMS_TMPL::selectList( $JLMS_CONFIG->get('lms_languages'), 'lms_lang', 'class="inputbox" style="width:160px; margin-right:30px; padding:0px;" size="1" id="jlms_lang" onchange="jlms_changeLang()" ', 'value', 'text', $JLMS_CONFIG->get('default_language') );
			$user_roles = array();
			$teach_role = mosHTML::makeOption('1', _JLMS_ROLE_TEACHER);
			$stu_role = mosHTML::makeOption('2', _JLMS_ROLE_STU);
			$ceo_role = mosHTML::makeOption('6', _JLMS_ROLE_CEO);
			$cur_role = $JLMS_CONFIG->get('current_usertype');
			if ($JLMS_CONFIG->get('main_usertype') == 1) {
				$user_roles[] = $teach_role;
				$user_roles[] = $stu_role;
			} elseif ($JLMS_CONFIG->get('current_usertype') == 2) {
				if ($JLMS_CONFIG->get('main_usertype') == 1) {
					$user_roles[] = $teach_role;
				}
				$user_roles[] = $stu_role;
			} elseif ($JLMS_CONFIG->get('main_usertype') == 2) {
				$user_roles[] = $stu_role;
			}
			if ($JLMS_CONFIG->get('is_user_parent') == 1) {
				if (in_array($id, $JLMS_CONFIG->get('parent_in_courses'))) {
					$user_roles[] = $ceo_role;
				}
			}
			if (count($user_roles) > 1)  {
				echo _JLMS_UO_SWITCH_TYPE;
				echo JLMS_TMPL::selectList( $user_roles, 'lms_user_role', 'class="inputbox" style="width:160px; padding:0px;" size="1" id="jlms_user_role" onchange="jlms_changeRole()" ', 'value', 'text', $JLMS_CONFIG->get('current_usertype') );
			}
			if ($JLMS_CONFIG->get('jlms_notecez', 1)) {
				require($JLMS_CONFIG->getCfg('absolute_path').'/components/com_joomla_lms/includes/lms_notice_tpl.php' );
				get_notice_html($option);
			}
			if ($JLMS_CONFIG->get('plugins_message', '')) {
				echo '<br /><br />'.$JLMS_CONFIG->get('plugins_message', '');
			}
			echo '</div>';
			echo '</div>';
			if ($JLMS_CONFIG->get('web20_effects', true)) {
				$JLMS_CONFIG->set('jlms_aditional_js_code', $JLMS_CONFIG->get('jlms_aditional_js_code','').'var mySlide_star_menu;');
				$domready = '				
					mySlide_star_menu = new Fx.Slide(\'joomlalms_star_menu\');
					mySlide_star_menu.hide();
					$(\'joomlalms_star_menu\').setStyles({visibility: \'visible\',display: \'\'});					
					if ($(\'jlms_plugins_run\')) {						
						$(\'jlms_plugins_run\').addEvent(\'click\', function(e){
							e = new Event(e);
							mySlide_star_menu.toggle();
							e.stop();
						});
					}
				';
				$JLMS_CONFIG->set('web20_domready_code', $JLMS_CONFIG->get('web20_domready_code','').$domready);
			} else {
				$JLMS_CONFIG->set('jlms_aditional_js_code', $JLMS_CONFIG->get('jlms_aditional_js_code','').'var star_menu_hidden = true;');
				$domready = '
					if ($(\'jlms_plugins_run\')) {
						$(\'jlms_plugins_run\').addEvent(\'click\', function(e){
							e = new Event(e);
							if (star_menu_hidden) {
								$(\'joomlalms_star_menu\').setStyles({visibility: \'visible\',display: \'\'});
								star_menu_hidden = false;
							} else {
								$(\'joomlalms_star_menu\').setStyles({visibility: \'hidden\',display: \'none\'});
								star_menu_hidden = true;
							}
							e.stop();
						});
					}
				';
				$JLMS_CONFIG->set('web20_domready_code', $JLMS_CONFIG->get('web20_domready_code','').$domready);
			}
			
		} elseif ($plugin == 'notes') {
			echo '';
		}
	}

	function selectList( &$arr, $tag_name, $tag_attribs, $key, $text, $selected=NULL, $box_id='', $show_option_ids = true ) {
		// check if array
		if ( is_array( $arr ) ) {
			reset( $arr );
		}

		$html 	= "\n<select name=\"$tag_name\" $tag_attribs".($box_id?" id=\"$box_id\"":'').">";
		$count 	= count( $arr );

		for ($i=0, $n=$count; $i < $n; $i++ ) {
			$k = $arr[$i]->$key;
			$t = $arr[$i]->$text;

			$id = $show_option_ids ? ( isset($arr[$i]->id) ? @$arr[$i]->id : null) : null;

			$extra = '';
			$extra .= $id ? " id=\"" . $arr[$i]->id . "\"" : '';
			if (is_array( $selected )) {
				foreach ($selected as $obj) {
					$k2 = $obj->$key;
					if ($k == $k2) {
						$extra .= " selected=\"selected\"";
						break;
					}
				}
			} else {
				$extra .= ($k == $selected ? " selected=\"selected\"" : '');
			}
			$html .= "\n\t<option value=\"".$k."\"$extra>" . $t . "</option>";
		}
		$html .= "\n</select>\n";

		return $html;
	}

	function ShowMenuControl( &$control, $skin = '', $location = 'toolbar', $size = '16', $help_task = '') {
		global $JLMS_SESSION, $JLMS_LANGUAGE, $JLMS_CONFIG;
		$disabled = 0;
		if (isset($control->disabled) && $control->disabled) { $disabled = 1; }
		$uo = 0;
		if (isset($control->user_options) && $control->user_options) { $uo = 1; }
		if (!$disabled){
			$title = '';
			if (!$JLMS_CONFIG->get('lofe_show_head', true)) {
				if( $uo ) {
					$title = ' title="'._JLMS_TOOLBAR_USER_OPTIONS.'"';
				} elseif (isset($JLMS_LANGUAGE[$control->lang_var])) {
					$title = ' title="'.$JLMS_LANGUAGE[$control->lang_var].'"';
				}
			}
			if (isset($control->is_separator) && $control->is_separator){
				//echo '<img src="components/com_joomla_lms/lms_images/spacer.png" border="0" width="2" height="'.$imh.'" style="background-color:#666666 " alt="spacer" />';
				echo '<td width="8"><img src="'.$JLMS_CONFIG->get('live_site').'/components/com_joomla_lms/lms_images/blank.gif" width="8" height="8" border="0" alt=" " /></td>';
			}else{
				if (isset($control->help_task) && $control->help_task){
					$control->menulink = $control->menulink.$help_task;
				}
				if (!$disabled && !$uo){
					echo '<td width="'.$size.'"><a class="jlms_menu_control" href="'.$control->menulink.'"><img class="JLMS_png" src="'.$JLMS_CONFIG->get('live_site').'/components/com_joomla_lms/lms_images/'.$location.'/'.$control->image.'" width="'.$size.'" height="'.$size.'" border="0" alt="'.$JLMS_LANGUAGE[$control->lang_var].'"'.$title.' /></a></td>';
				}	
			}

			if( $uo ){
				echo '<td width="'.$size.'"><a id="jlms_plugins_run" class="jlms_menu_control" href="javascript:void(0);"><img class="JLMS_png" src="'.$JLMS_CONFIG->get('live_site').'/components/com_joomla_lms/lms_images/'.$location.'/'.$control->image.'" width="'.$size.'" height="'.$size.'" border="0" alt="'._JLMS_TOOLBAR_USER_OPTIONS.'"'.$title.' /></a></td>';
			}
		}
	}

	function showMenuToolTips() {
		global $JLMS_CONFIG;
		echo '<span id="JLMS_toolbar_tooltip"><!--x-->&nbsp;</span>';
		if ($JLMS_CONFIG->get('web20_effects', true)) {
			if( JLMS_mootools12() ) {
				$domready = '
					var is_mlf_menu = false;
					var fx_op_menu = new Fx.Tween(\'JLMS_toolbar_tooltip\', \'opacity\');
					var fx_txt_cont_menu = $(\'JLMS_toolbar_tooltip\');
					$$(\'a.jlms_menu_control\').each(function(aelfm){
						aelfm.addEvent(\'mouseenter\', function(){
								is_mlf_menu = false;
								fx_txt_cont_menu.set( \'html\', this.getElement(\'img\').getProperty(\'alt\'));
								fx_op_menu.stop();
								fx_op_menu.start(0,1).chain(function(){if (is_mlf_menu) { fireEvent(\'mouseleave\', aelfm);}});
						});
						aelfm.addEvent(\'mouseleave\', function(){
								is_mlf_menu = true;
								fx_op_menu.stop();
								fx_op_menu.start(1,0);
						});
					});
				';
			} else {
				$domready = '
					var is_mlf_menu = false;
					var fx_op_menu = new Fx.Style(\'JLMS_toolbar_tooltip\', \'opacity\');
					var fx_txt_cont_menu = $(\'JLMS_toolbar_tooltip\');
					$$(\'a.jlms_menu_control\').each(function(aelfm){
						aelfm.addEvent(\'mouseenter\', function(){
								is_mlf_menu = false;
								fx_txt_cont_menu.setHTML(this.getElement(\'img\').getProperty(\'alt\'));
								fx_op_menu.stop();
								fx_op_menu.start(0,1).chain(function(){if (is_mlf_menu) { fireEvent(\'mouseleave\', aelfm);}});
						});
						aelfm.addEvent(\'mouseleave\', function(){
								is_mlf_menu = true;
								fx_op_menu.stop();
								fx_op_menu.start(1,0);
						});
					});
				';
			}
			$JLMS_CONFIG->set('web20_domready_code', $JLMS_CONFIG->get('web20_domready_code','').$domready);
		}
	}

	function showTopMenu( &$menus, $id, $option, $skin, $help_task = '' ) { 
		global $my, $Itemid, $JLMS_SESSION, $JLMS_CONFIG;

		$back_status = $JLMS_SESSION->has('jlms_section')?$JLMS_SESSION->get('jlms_section'):'&nbsp;';
		$user_access = $JLMS_CONFIG->get('current_usertype');
		if (!$help_task) { $help_task = $JLMS_SESSION->get('jlms_task'); }
		if($user_access == 2){
			$help_task = "stu_".$help_task;
		}elseif($user_access == 6){
			$help_task = "ceo_".$help_task;
		}
		//$skin = 'pro';

		if ($skin == 'pro') {
			$imp = 'toolbar_24';
			$imp = 'toolbar';
			$imh = '24';
			$imh = '16';
			if (!$JLMS_CONFIG->get('lofe_menu_style', 1)) {
				$imp = 'toolbar';$imh = '16';
			} elseif ($JLMS_CONFIG->get('lofe_menu_style', 1) == 1) {
				$imp = 'toolbar_24';$imh = '24';
			} elseif ($JLMS_CONFIG->get('lofe_menu_style', 1) == 2) {
				$imp = 'toolbar_32';$imh = '32';
			}
			if ($JLMS_CONFIG->get('lofe_show_head', true)) {
				echo '<table class="lms_pro_menu" cellpadding="0" cellspacing="0" border="0"><tr><td nowrap="nowrap" colspan="'.count($menus).'" align="center" style="text-align:center">';
				JLMS_TMPL::showMenuToolTips();
				echo '</td></tr><tr><td><table style="float:right" class="lms_pro_menu" cellpadding="0" cellspacing="0" border="0"><tr>';
				foreach ($menus as $menu){
					JLMS_TMPL::ShowMenuControl($menu, $skin, $imp, $imh, $help_task);
				}
				echo '</tr></table></td>';
				echo '</tr></table>';
			} else {
				echo '<table class="lms_pro_menu" cellpadding="0" cellspacing="0" border="0"><tr>';
				foreach ($menus as $menu){
					JLMS_TMPL::ShowMenuControl($menu, $skin, $imp, $imh, $help_task);
				}
				echo '</tr></table>';
			}
		}
	}
	
	function showTopMenu_simple( &$menus, $option, $skin ) {
		global $Itemid, $JLMS_SESSION, $JLMS_CONFIG, $JLMS_LANGUAGE;
		$back_status = $JLMS_SESSION->has('jlms_section')?$JLMS_SESSION->get('jlms_section'):'&nbsp;';
		
		$menus = $JLMS_CONFIG->get('jlms_menu');
		//JLMS_require_lang($JLMS_LANGUAGE, 'main.lang', $JLMS_CONFIG->get('default_language'));

		if ($skin == 'pro') {
			$imp = 'toolbar_24';
			$imp = 'toolbar';
			$imh = '24';
			$imh = '16';
			if (!$JLMS_CONFIG->get('lofe_menu_style', 1)) {
				$imp = 'toolbar';$imh = '16';
			} elseif ($JLMS_CONFIG->get('lofe_menu_style', 1) == 1) {
				$imp = 'toolbar_24';$imh = '24';
			} elseif ($JLMS_CONFIG->get('lofe_menu_style', 1) == 2) {
				$imp = 'toolbar_32';$imh = '32';
			}
			echo '<table class="lms_pro_menu" cellpadding="0" cellspacing="0" border="0"><tr><td colspan="'.count($menus).'" align="center" style="text-align:center">';
			JLMS_TMPL::showMenuToolTips();
			echo '</td></tr><tr>';
			foreach ($menus as $menu){
				JLMS_TMPL::ShowMenuControl($menu, $skin, $imp, $imh);
				/*if ($menu->is_separator){
					echo '<img src="components/com_joomla_lms/lms_images/spacer.png" border="0" width="2" height="16" style="background-color:#666666 " alt="spacer" />';
				}else{
					echo "<a $menu->target href='".$menu->menulink."' title='".$JLMS_LANGUAGE[$menu->lang_var]."'><img class='JLMS_png' src='components/com_joomla_lms/lms_images/toolbar/".$menu->image."' border='0' width='16' height='16' alt='".$JLMS_LANGUAGE[$menu->lang_var]."' title='".$JLMS_LANGUAGE[$menu->lang_var]."' /></a>&nbsp;";
				}*/
			}
			echo '</tr></table>';
		}
	}

	function ShowSelectCourseSB($id) {
		global $JLMS_CONFIG, $option, $Itemid, $JLMS_SESSION;
		$cid = $JLMS_CONFIG->get('teacher_in_courses', array());
		$cid = array_merge($cid, $JLMS_CONFIG->get('student_in_courses', array()) );
		$cid = array_merge($cid, $JLMS_CONFIG->get('parent_in_courses', array()) );
		$cid = array_unique($cid);
		$courses = JLMS_CoursesNames( $cid );

		$cur_course = 'undefined';
		foreach ($courses as $course) {
			if ($id == $course->id) { $cur_course = $course->course_name; }//substr($course->course_name,0,15);}
		}
		
		$add_js = "
		function jlms_redirect(redirect_url) {
			top.location.href = redirect_url;
		}
		function jlms_tr_over(td) {
			td.style['background'] = '#FFFFFF';			
		}
		function jlms_tr_out(td) {
			td.style['background'] = '#EEEEEE';			
		}
		JLMS_preloadImages('components/com_joomla_lms/lms_images/front_menu/menu_bg3.png');
		";
		$JLMS_CONFIG->set('jlms_aditional_js_code', $JLMS_CONFIG->get('jlms_aditional_js_code','').$add_js);
		?>
		<table cellpadding="0" cellspacing="0" border="0" style="float:right">
			<tr>
				<td align="right" nowrap="nowrap">
					<?php echo _JLMS_CURRENT_COURSE;?>
				</td>
				<td width="120">
					<table width="120" cellpadding="0" cellspacing="0" border="0" align="right">
					<tr>
						<td colspan="2" align="left" style="text-align:left; background:url(<?php echo $JLMS_CONFIG->getCfg('live_site');?>/components/com_joomla_lms/lms_images/front_menu/menu_bg.png) no-repeat; ">

						<table style="cursor:pointer; border-bottom:1px solid #666666; width:220px;" id="demo1run1" width="220" cellpadding="0" cellspacing="0" border="0"><tr><td align="left">
							<div style="cursor:pointer; overflow:hidden; white-space:nowrap; width:200px;" >&nbsp;&nbsp;<?php echo $cur_course;?></div>
						</td><td align="right" width="20"><img class="JLMS_png" src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/course_select_arrow.png" alt="select_arrow" title="select_arrow" border="0" width="10" height="10" />&nbsp;&nbsp;</td></tr></table>
						<div align="right" id="course_menu_cont" style="position: absolute; visibility: hidden; width: 220px;">
						<div>
							<div id="demo1">
							<table width="100%" cellpadding="0" cellspacing="0" border="0" class="jlms_top_menu_items_table" id="jlms_top_menu_items_table_id">
								<?php
									$i = 0;
									foreach ($courses as $course) {
										$link = sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=".$JLMS_SESSION->get('jlms_task')."&id=".$course->id);
										echo "<tr id='cmenu_".$i."' onmouseover=\"jlms_tr_over(this);\" onmouseout=\"jlms_tr_out(this);\" onclick=\"jlms_redirect('".$link."');\"><td align='left'><div>&nbsp;".(($id == $course->id)?('<b>'.$course->course_name.'</b>'):$course->course_name)."</div></td></tr>";
										$i ++;
									} ?>
									<tr style='cursor:pointer; background:url(<?php echo $JLMS_CONFIG->getCfg('live_site');?>/components/com_joomla_lms/lms_images/front_menu/menu_bg2.png) no-repeat; background-position: bottom;'><td style="height:4px; border:0px"></td></tr>
							</table>
							</div>
						</div>
						<?php if ($JLMS_CONFIG->get('web20_effects', true)) {
							$domready = '
					var demo1effect = new Fx.Slide(\'demo1\');
					demo1effect.hide();
					$(\'course_menu_cont\').setStyle(\'visibility\', \'visible\');
					$(\'demo1run1\').addEvent(\'click\', function(e){
						e = new Event(e);
						demo1effect.toggle();
						e.stop();
					});
							';
							$JLMS_CONFIG->set('web20_domready_code', $JLMS_CONFIG->get('web20_domready_code','').$domready);
						} else {
							$domready = '
					var course_menu_hidden = true;
					$(\'demo1run1\').addEvent(\'click\', function(e){
						e = new Event(e);
						if (course_menu_hidden) {
							$(\'course_menu_cont\').setStyle(\'visibility\', \'visible\');
							course_menu_hidden = false;
						} else {
							$(\'course_menu_cont\').setStyle(\'visibility\', \'hidden\');
							course_menu_hidden = true;
						}
						e.stop();
					});
							';
							$JLMS_CONFIG->set('web20_domready_code', $JLMS_CONFIG->get('web20_domready_code','').$domready);
						} ?>
						</div>
						</td>
					</tr>
					</table>
				</td>
			</tr>
		</table>
<?php
	}

	function ShowSelectCourseSB_form($id) {
		global $JLMS_CONFIG, $option, $Itemid, $JLMS_SESSION;
		$cid = $JLMS_CONFIG->get('teacher_in_courses', array());
		$cid = array_merge($cid, $JLMS_CONFIG->get('student_in_courses', array()) );
		$cid = array_merge($cid, $JLMS_CONFIG->get('parent_in_courses', array()) );
		$cid = array_unique($cid);
		$courses = JLMS_CoursesNames( $cid );

		$cur_course = 'undefined';
		foreach ($courses as $course) {
			if ($id == $course->id) { $cur_course = $course->course_name; }//substr($course->course_name,0,15);}
		}
		
		$add_js = "
		function jlms_redirect(sel_element) {
			var id = sel_element.options[sel_element.selectedIndex].value;
			var redirect_url = '';
			switch (id) {
";
foreach ($courses as $course) {
	$add_js .= "
				case '$course->id':
					redirect_url = '".sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=".$JLMS_SESSION->get('jlms_task')."&id=".$course->id)."'
				break;
";
}

$add_js .= "
				default:
				break;
			}
			if (redirect_url) {
				top.location.href = redirect_url;
			}
		}
		";
		$JLMS_CONFIG->set('jlms_aditional_js_code', $JLMS_CONFIG->get('jlms_aditional_js_code','').$add_js);
		?>
		<table cellpadding="0" cellspacing="0" border="0" style="float:right" class="jlms_coursebox_cont">
			<tr>
				<td align="right" nowrap="nowrap">
					<?php echo _JLMS_CURRENT_COURSE;?>
				</td>
				<td width="120">
					<form name="jlms_change_course" action="<?php echo sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid");?>" method="post">
				<noscript>
					<input type="hidden" name="option" value="<?php echo $option;?>" />
					<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
					<input type="hidden" name="task" value="<?php echo $JLMS_SESSION->get('jlms_task');?>" />
				</noscript>
					<select name="id" style="width:200px; border:1px solid #666666;" onchange="jlms_redirect(this)">
					<?php
					$i = 0;
					foreach ($courses as $course) {
						echo '<option value="'.$course->id.'"'.(($id == $course->id) ? ' selected="selected"':'').'>'.$course->course_name.'</option>';
						$i ++;
					} ?>
					</select>
				<noscript>
					<input type="submit" name="OK" value="OK" />
				</noscript>
					</form>
				</td>
			</tr>
		</table>
<?php
	}
}
?>