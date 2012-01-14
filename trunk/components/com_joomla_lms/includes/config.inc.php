<?php
/**
* includes\config.inc.php
* (c) JoomaLMS eLearning Software http://www.joomlalms.com/
**/

defined( '_JEXEC' ) or die( 'Restricted access' );

if (isset($JLMS_CONFIG) && is_object($JLMS_CONFIG) && method_exists($JLMS_CONFIG, 'set')) {

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * * * * * * * * * * * * * * * * *  DEBUG MODE * * * * * * * * * * * * * * * * * * * * *
 */ 	

	$JLMS_CONFIG->set('debug_mode', false);
	$JLMS_CONFIG->set('debug_database', false);
	//$JLMS_CONFIG->set('debug_user', 64);

	$JLMS_CONFIG->set('enable_errorlog', false);

	$JLMS_CONFIG->set('web20_effects', true);

	$JLMS_CONFIG->set('top_menu_type', 24); // 16; 22; 32

	$JLMS_CONFIG->set('do_ie6_png_fix', true);

	if( $_SERVER['SERVER_PORT'] == 443 || @$_SERVER['HTTPS'] == 'on') { // if we are under the HTTPS
		$JLMS_CONFIG->set('do_ie6_png_fix', false); // IE6 PNG fix fails under HTTPS. There is no HTTPS support in IE6 AlphaImageLoader.
	}

	$JLMS_CONFIG->set('always_under_ssl', false); // set to 'true' if your site is always working under SSL.

	$JLMS_CONFIG->set('plugin_quiz', true); // quiz is always enabled - for compatibility with old functionality.

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * * * *  LIST of ISO-8859-1 languages (DO NOT EDIT THIS SECTION !!!!) * * * * * * * * *
 */
 
	$iso88591_compat_languages = array('danish', 'french', 'german', 'italian', 'norwegian', 'spanish', 'dutch', 'brazilian');
	$JLMS_CONFIG->set('iso88591_compat_languages', $iso88591_compat_languages);


/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * * * * * * * * * * * * * * *  INVOICE FIELDS * * * * * * * * * * * * * * * * * * * * *
 */

	$all_invoice_fields = array();
	/*$company_field = new stdClass();
	$company_field->var_name = 'company'; $company_field->profile_var = 'lms_cb_company'; $company_field->lang_var = '_JLMS_INVOICE_CUSTOMER_COMPANY_TEXT';
	$all_invoice_fields[] = $company_field;

	$address_field = new stdClass();
	$address_field->var_name = 'address'; $address_field->profile_var = 'lms_cb_address'; $address_field->lang_var = '_JLMS_INVOICE_CUSTOMER_ADDRESS_TEXT';
	$all_invoice_fields[] = $address_field;

	$city_field = new stdClass();
	$city_field->var_name = 'city'; $city_field->profile_var = 'lms_cb_city'; $city_field->lang_var = '_JLMS_INVOICE_CUSTOMER_CITY_TEXT';
	$all_invoice_fields[] = $city_field;

	$phone_field = new stdClass();
	$phone_field->var_name = 'phone'; $phone_field->profile_var = 'lms_cb_phone'; $phone_field->lang_var = '_JLMS_INVOICE_CUSTOMER_PHONE_TEXT';
	$all_invoice_fields[] = $phone_field;*/

	/*
	// how to create additional field:
	$example_field = new stdClass(); // $example_field - just a variable with any name
	// `var_name` - ay name, should be unique in the array of invoice fields
	// `profile_var` - reference to the user profile - check this variable at the administrator 'CB integration' page (in the right column); i.e. {lms_cb_address} 
	// `lang_var` - name of the Language variable, or name of the profile field if language variable is not defined
	$example_field->var_name = 'example'; $example_field->profile_var = 'lms_cb_pcode'; $example_field->lang_var = 'Example: ';
	// add this newly created filed to the list of all invoice fields
	$all_invoice_fields[] = $example_field;
	*/


	/*
	$zip_field = new stdClass();
	$zip_field->var_name = 'zipcode'; $zip_field->profile_var = 'lms_cb_pcode'; $zip_field->lang_var = 'Zip code: ';
	$all_invoice_fields[] = $zip_field;
	$country_field = new stdClass();
	$country_field->var_name = 'country'; $country_field->profile_var = 'lms_cb_country'; $country_field->lang_var = 'Country: ';
	$all_invoice_fields[] = $country_field;
	*/

	$JLMS_CONFIG->set('custom_invoice_fields', $all_invoice_fields);


/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * * * * * * * * * * * * *  MEMORY ADJUSTEMENTS  * * * * * * * * * * * * * * * * * * * *
 */

	$JLMS_CONFIG->set('memory_limit_global', 64);
	$JLMS_CONFIG->set('memory_limit_gradebook', 96);
	$JLMS_CONFIG->set('memory_limit_import_export', 128);


/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * * * * * * * * * * * SYSTEM MESSAGES SECTION * * * * * * * * * * * * * * * * * * * * *
 */
	
	$jlms_sys_msgs = array();
	
	// * * * description: (needful tool to place messages/instructions for users)
	// 'task' - task in the URL
	// 'course' - 0 - for all course, ID - for specified course.
	// 'message' - message
	// 'align' - ('left', 'right' ,'center') you can miss this parameter - to use default template alignment.
	// * * * examples:
	//$jlms_sys_msgs[] = array( 'task' => 'details_course', 'course' => 2, 'message' => "under construction" );
	//$jlms_sys_msgs[] = array( 'task' => 'details_course', 'course' => 0, 'message' => "Hello ;)", 'align' => 'center' );
	
	//$jlms_sys_msgs[] = array( 'task' => 'details_course', 'course' => 2, 'message' => "Hello ;)   This course is under construction.", 'align' => 'center' );

	//$jlms_sys_msgs[] = array( 'task' => 'courses', 'course' => 0, 'message' => "under construction" );

	$JLMS_CONFIG->set('system_messages', $jlms_sys_msgs);


	// CSS class for the 'system_message' span. By default this class is 'joomlalms_sys_message' and defined in JoomlaLMS CSS file.
	// If your template has typography styles, you can use them.
	$JLMS_CONFIG->set('system_message_css_class', 'joomlalms_sys_message');




/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * * * * * * * * * * * * * * * * * * * VISUAl SETTINGS * * * * * * * * * * * * * * * * *
 */

	// * These settings are used on 'List Subscriptions' page.
	$JLMS_CONFIG->set('visual_set_main_row_class', 'sectiontableentry2 even');
	$JLMS_CONFIG->set('visual_set_child_row_class', 'sectiontableentry1 odd');

	// * These settings are used on 'Tracking' page.
	$JLMS_CONFIG->set('visual_set_tracking_image_base_width', 350);
	$JLMS_CONFIG->set('visual_set_tracking_image_base_height', 260);




/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * * * * * * * * * * * * * * * * * * * COMPONENTS PATHS  * * * * * * * * * * * * * * * *
 * Parameters below in this section do not have to be changed.
 */

	$JLMS_CONFIG->set('lms_path_to_images', 'components/com_joomla_lms/lms_images');
	$JLMS_CONFIG->set('lms_path_to_includes', 'components/com_joomla_lms/includes');
	$JLMS_CONFIG->set('lms_path_to_css', 'components/com_joomla_lms/lms_css');

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * * * * * * * * * * * AJAX REQUESTS SETTINGS (don't edit this section!!!!)  * * * * * *
 * Parameters below in this section do not have to be changed.
 * They are created for programmers only.
 * (These parameters are different in Joomla 1.0.x and Joomla 1.5 - to know this defference contact us.)
 */
	$option = 'com_joomla_lms';
	$Itemid = $JLMS_CONFIG->get('Itemid');
	$JLMS_CONFIG->set('ajax_settings_request_safe_path', 'index.php?tmpl=component&no_html=1&option='.$option.'&Itemid='.$Itemid);
	//$JLMS_CONFIG->set('ajax_settings_request_method', 'POST');
	$JLMS_CONFIG->set('ajax_settings_big_indicator', $JLMS_CONFIG->get('lms_path_to_images').'/loading.gif');
	$JLMS_CONFIG->set('ajax_settings_small_indicator', $JLMS_CONFIG->get('lms_path_to_images').'/indicator_small.gif');

	// To use black indicator uncomment the line below (for black templates);
	//$JLMS_CONFIG->set('ajax_settings_small_indicator', $JLMS_CONFIG->get('lms_path_to_images').'/indicator_small_black.gif');




/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * * * * * * * * * * * JOOMLA CMS VERSION DETECTION  * * * * * * * * * * * * * * * * * *
 * Parameters below in this section do not have to be changed.
 */
	$is_joomla15 = false;
	if (class_exists('JApplication')) {
		$is_joomla15 = true;
	}
	$cms_version = $is_joomla15 ? '1.5' : '1.0';
	$JLMS_CONFIG->set('joomla_cms_type', $is_joomla15);
	$JLMS_CONFIG->set('is_joomla_15', $is_joomla15);
	$JLMS_CONFIG->set('joomla_cms_version', $cms_version);

	if ($is_joomla15) {
		$jversion = new JVersion();
		if (isset($jversion->RELEASE) && $jversion->RELEASE == '1.6') {
			$JLMS_CONFIG->set('joomla_cms_version', '1.6');
			$JLMS_CONFIG->set('is_joomla_16', true);
		} elseif (isset($jversion->RELEASE) && $jversion->RELEASE == '1.7') {
			$JLMS_CONFIG->set('joomla_cms_version', '1.7');
			$JLMS_CONFIG->set('is_joomla_16', true);
			$JLMS_CONFIG->set('is_joomla_17', true);
		} elseif (isset($jversion->RELEASE) && $jversion->RELEASE == '1.8') {
			$JLMS_CONFIG->set('joomla_cms_version', '1.8');
			$JLMS_CONFIG->set('is_joomla_16', true);
			$JLMS_CONFIG->set('is_joomla_17', true);
			$JLMS_CONFIG->set('is_joomla_18', true);
		}
	}


/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * * * * * * * * * * * * CERTIFICATES CONFIGURATION  * * * * * * * * * * * * * * * * * *
 * 
 */
//	$JLMS_CONFIG->set('crtf_option_code_barcode', true);
//	$JLMS_CONFIG->set('crtf_option_code_string', true);
	$JLMS_CONFIG->set('crtf_option_barcode_right_offset', 20);
	$JLMS_CONFIG->set('crtf_option_barcode_bottom_offset', 10);



/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * * * * * * * * * * * * * some adjustements * * * * * * * * * * * * * * * * * * * * * *
 * 			don't change it !!!
 */
	$JLMS_CONFIG->set('use_cart', true);
	$JLMS_CONFIG->set('use_custom_subscr', false);
	$JLMS_CONFIG->set('cart_conf_subs_integration', false);
	$JLMS_CONFIG->set('enable_conference_booking', false);

	if (file_exists(dirname(__FILE__).'/../../com_conf_sales/conf_sales.php')) {
		$JLMS_CONFIG->set('cart_conf_subs_integration', true);
		$JLMS_CONFIG->set('enable_conference_booking', true);
	}

	//$JLMS_CONFIG->set('gradebook_certificate_date', 'm-d-Y');


/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * * * SYSTEM SETTINGS (Don't change anything - CHANGES CAN CRASH YOUR SYSTEM !!!) * * *
 * 
 */

	if (file_exists(dirname(__FILE__).'/../../com_lms_booking/lms_booking.php')) {
		$JLMS_CONFIG->set('flms_integration', 1);
	} else {
		$JLMS_CONFIG->set('flms_integration', 0);
	}
	$JLMS_CONFIG->set('roles_management', 1);
	$JLMS_CONFIG->set('advanced_categories', 1);
	$JLMS_CONFIG->set('multicat_use', 1);
	$JLMS_CONFIG->set('multicat_show_admin_levels', 1);
	$JLMS_CONFIG->set('multicat_no_display_empty', 1);
	$JLMS_CONFIG->set('frontpage_courses_tree', 1);
	$JLMS_CONFIG->set('frontpage_notices_teacher', $JLMS_CONFIG->get('flms_integration'));
	$JLMS_CONFIG->set('juser_integration', 0);
	$JLMS_CONFIG->set('new_forums_config', 1);

	$JLMS_CONFIG->set('empltype_fulltime', 'Full time');
	$JLMS_CONFIG->set('empltype_freelance', 'Freelance');

	$JLMS_CONFIG->set('show_docs_authors', 0);
	$JLMS_CONFIG->set('show_links_authors', 0);
	$JLMS_CONFIG->set('show_lpaths_authors', 0);
	$JLMS_CONFIG->set('show_quizzes_authors', 0);
	$JLMS_CONFIG->set('show_library_authors', 0);

	$JLMS_CONFIG->set('plugins_message', '');

	$JLMS_CONFIG->set('scorm_scrolling', 'no'); // possible options: 'yes', 'no', 'auto'

	$JLMS_CONFIG->set('tableheader_tag', 'td'); // you can place 'th' here to meet WAI accessibility rules

	if ($JLMS_CONFIG->get('is_joomla_16', false)) {
		$JLMS_CONFIG->set('tableheader_tag', 'th');
	}

	$JLMS_CONFIG->set('additional_heading_tag_open', ''); // e.g. '<h2>' - for 'contentheading'
	$JLMS_CONFIG->set('additional_heading_tag_close', '');// e.g. '</h2>' - for 'contentheading'
	$JLMS_CONFIG->set('main_heading_open', '<div class="componentheading" id="jlms_topdiv">');
	$JLMS_CONFIG->set('main_heading_close', '</div>');
	//IMPORTANT: id="jlms_topdiv" is a 'must have' to avoid javascript errors

	if ($JLMS_CONFIG->get('live_site','') === 'http://www.joomlalms.com') {
		// LMS is installed on www.joomlalms.com DEMO - add <h1> tags to the component header (for SEO) and style them to meet our template layout
		$JLMS_CONFIG->set('main_heading_open', '<h1 id="jlms_topdiv">');
		$JLMS_CONFIG->set('main_heading_close', '</h1>');
	} elseif ($JLMS_CONFIG->get('is_joomla_16', false)) {
		$JLMS_CONFIG->set('main_heading_open', '<h1 id="jlms_topdiv">');
		$JLMS_CONFIG->set('main_heading_close', '</h1>');
		$JLMS_CONFIG->set('additional_heading_tag_open', '<h2>');
		$JLMS_CONFIG->set('additional_heading_tag_close', '</h2>');
	}
	$JLMS_CONFIG->set('main_heading_tag_open', ''); // e.g. '<h1>' - for 'componentheading'
	$JLMS_CONFIG->set('main_heading_tag_close', '');// e.g. '</h1>' - for 'componentheading'

	$JLMS_CONFIG->set('show_statistics_reports', 1);
	$JLMS_CONFIG->set('show_reports_images', 1);
	$JLMS_CONFIG->set('cutoff_reports_coursename', 7);


	$JLMS_CONFIG->set('global_quest_pool', true);

	$JLMS_CONFIG->set('quizzes_show_quest_id', $JLMS_CONFIG->get('flms_integration'));
	$JLMS_CONFIG->set('quizzes_quest_id_title', 'Question ID:');

	$JLMS_CONFIG->set('lmspro_roles', true);

	$JLMS_CONFIG->set('new_lms_features', true);

	$JLMS_CONFIG->set('show_scorm_report_link', false);//temporary
	$JLMS_CONFIG->set('time_released_lpaths_prerequisites', false);//temporary
	$JLMS_CONFIG->set('recurrent_payments_feature', true);//temporary
	
	$JLMS_CONFIG->set('enable_timetracking', false); //joomla_lms.course_lpath.php (if Timetracking enabled)
	$JLMS_CONFIG->set('hide_lpath_results_time', false);
	
	$JLMS_CONFIG->set('hide_payments_summary', false);
}
?>