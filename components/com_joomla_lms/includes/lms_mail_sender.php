<?php
/*
This is mail sending subsystem for JLMS
*/

// no direct access
defined( '_JLMS_EXEC' ) or die( 'Restricted access' );

//handler
$task = mosGetParam($_REQUEST, 'task', 'mail_main');
$user_id = mosGetParam($_REQUEST, 'assigned', '-1');
$redirect = $JLMS_SESSION->get('redirect_after_mail');
global $JLMS_DB;

switch ($task) {
	case 'mail_iframe':
		$mail_object = new JLMS_Mail($JLMS_DB, $redirect);
		$mail_object->setAssigned($user_id);
		$mail_object->getMails();
		$mail_object->showIFrame();
		$JLMS_SESSION->clear('redirect_after_mail');
		break;
		
	case 'mail_main':
		$mail_object = new JLMS_Mail($JLMS_DB, $redirect);
		$mail_object->setAssigned($user_id);
		$mail_object->showPage();
		break;
}
?>