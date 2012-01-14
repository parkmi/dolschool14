<?php
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

$_JLMS_PLUGINS = & JLMSFactory::getPlugins();
$_JLMS_PLUGINS->registerFunction( 'onCourseJoin', 'notifyCourseJoined' );

function notifyCourseJoined( $users_info ) {
	$_JLMS_PLUGINS = & JLMSFactory::getPlugins();
	$JLMS_CONFIG = & JLMSFactory::getConfig();

	//---->get bot info
	$dbo = & JFactory::GetDbo();
	if ( !isset($_JLMS_PLUGINS->_nontify_bot_params['coursejoinnotify']) ) {
		// load mambot params info
		$query = "SELECT params"
		. "\n FROM #__lms_plugins"
		. "\n WHERE element = 'coursejoinnotify'"
		. "\n AND folder = 'notifications'"
		;
		$dbo->setQuery( $query );
		$bot = $dbo->loadObject();

		// save query to class variable
		$_JLMS_PLUGINS->_notify_bot_params['coursejoinnotify'] = $bot;
	}

	// pull query data from class variable
	$bot = $_JLMS_PLUGINS->_notify_bot_params['coursejoinnotify'];

	$botParams = new jlmsPluginParameters( $bot->params );

	$botParams->def( 'mail_template', 'Hello {user_name}, You have just been added to the course {course_name} attendees list.' );
	$botParams->def( 'mail_subject', 'Course joining notification' );
	//<----

	foreach ($users_info as $info_ids) {
		$query = "SELECT c.course_name AS course_name, u.name AS user_name, u.email AS mail_address FROM #__lms_courses AS c, #__users AS u"
		."\n WHERE c.id=$info_ids->course_id AND u.id=$info_ids->user_id";
		$dbo->setQuery($query);
		$info_text = $dbo->loadObject();
		
		$mail_address = $dbo->getEscaped($info_text->mail_address);
		
		$mail_subject = $dbo->getEscaped($botParams->get( 'mail_subject' ));
		
		$mail_text = $botParams->get( 'mail_template' );
		$mail_text = str_replace('{user_name}', $info_text->user_name, $mail_text);
		$mail_text = str_replace('{course_name}', $info_text->course_name, $mail_text);
		$mail_text = str_replace('{site_URL}', $JLMS_CONFIG->get('live_site'), $mail_text);
		$mail_text = $dbo->getEscaped($mail_text);
		
		$query = "INSERT INTO #__lms_notifications (assigned, mail_address, mail_subject, mail_body, sent) VALUES"
		."\n ($info_ids->teacher_id, '$mail_address', '$mail_subject', '$mail_text', 0)";
		$dbo->setQuery($query);
		$dbo->query();
	}
}
?>