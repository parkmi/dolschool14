<?php 
/**
* includes/classes/acl/teacher.php
* Joomla LMS Component
* * * ElearningForce Biz
**/

// no direct access
defined( '_JLMS_EXEC' ) or die( 'Restricted access' );

function & JLMS_get_learner_role() {
	$role = array();
		$permissions_advanced = new stdClass();
		$permissions_advanced->view_all_users = 1;
		$permissions_advanced->assigned_groups_only = 0;
	$role['advanced'] = & $permissions_advanced;

		$permissions_lms = new stdClass();
		$permissions_lms->create_course = 0;
	$role['lms'] = & $permissions_lms;

		$permissions_docs = new stdClass();
		$permissions_docs->view = 1;
		$permissions_docs->view_all = 0;
		$permissions_docs->order = 0;
		$permissions_docs->publish = 0;
		$permissions_docs->manage = 0;
		$permissions_docs->only_own_items = 0;
		$permissions_docs->only_own_role = 0;
	$role['docs'] = & $permissions_docs;

		$permissions_quizzes = new stdClass();
		$permissions_quizzes->view = 1;
		$permissions_quizzes->view_all = 0;
		$permissions_quizzes->view_stats = 0;
		$permissions_quizzes->publish = 0;
		$permissions_quizzes->manage = 0;
		$permissions_quizzes->manage_pool = 0;
	$role['quizzes'] = & $permissions_quizzes;

		$permissions_links = new stdClass();
		$permissions_links->view = 1;
		$permissions_links->view_all = 0;
		$permissions_links->order = 0;
		$permissions_links->publish = 0;
		$permissions_links->manage = 0;
	$role['links'] = & $permissions_links;

		$permissions_lpaths = new stdClass();
		$permissions_lpaths->view = 1;
		$permissions_lpaths->view_all = 0;
		$permissions_lpaths->order = 0;
		$permissions_lpaths->publish = 0;
		$permissions_lpaths->manage = 0;
		$permissions_lpaths->only_own_items = 0;
		$permissions_lpaths->only_own_role = 0;
	$role['lpaths'] = & $permissions_lpaths;

		$permissions_announce = new stdClass();
		$permissions_announce->view = 1;
		$permissions_announce->manage = 0;
	$role['announce'] = & $permissions_announce;
	
		$permissions_dropbox = new stdClass();
		$permissions_dropbox->view = 1;
		$permissions_dropbox->send_to_teachers = 1;
		$permissions_dropbox->send_to_learners = 0;
		$permissions_dropbox->mark_as_corrected = 0;
	$role['dropbox'] = & $permissions_dropbox;
	
		$permissions_homework = new stdClass();
		$permissions_homework->view = 1;
		$permissions_homework->view_stats = 0;
		$permissions_homework->view_all = 0;
		$permissions_homework->publish = 0;
		$permissions_homework->manage = 0;		
	$role['homework'] = & $permissions_homework;
	
		$permissions_attendance = new stdClass();
		$permissions_attendance->manage = 0;
		$permissions_attendance->view = 1;
	$role['attendance'] = & $permissions_attendance;

		$permissions_chat = new stdClass();
		$permissions_chat->manage = 0;
		$permissions_chat->view = 1;
	$role['chat'] = & $permissions_chat;

		$permissions_conf = new stdClass();
		$permissions_conf->manage = 0;
		$permissions_conf->view = 1;
	$role['conference'] = & $permissions_conf;

		$permissions_gbook = new stdClass();
		$permissions_gbook->manage = 0;
		$permissions_gbook->view = 1;
	$role['gradebook'] = & $permissions_gbook;

		$permissions_mail = new stdClass();
		$permissions_mail->manage = 0;
		$permissions_mail->view = 1;
	$role['mailbox'] = & $permissions_mail;

		$permissions_track = new stdClass();
		$permissions_track->manage = 0;
		$permissions_track->clear_stats = 0;
	$role['tracking'] = & $permissions_track;

		$permissions_users = new stdClass();
		$permissions_users->manage = 0;
		$permissions_users->manage_teachers = 0;
		$permissions_users->view = 0;
		$permissions_users->import_users = 0;
	$role['users'] = & $permissions_users;

		$permissions_course = new stdClass();
		$permissions_course->manage_certificates = 0;
		$permissions_course->manage_settings = 0;
		$permissions_course->edit = 0;
	$role['course'] = & $permissions_course;

		$permissions_library = new stdClass();
		$permissions_library->view = 1;
		$permissions_library->view_all = 0;
		$permissions_library->order = 0;
		$permissions_library->publish = 0;
		$permissions_library->manage = 0;
	$role['library'] = & $permissions_library;

		$permissions_us = new stdClass();
		$permissions_us->switch_role = 0;
		$permissions_us->switch_language = 1;
	$role['user_settings'] = & $permissions_us;

	return $role;
}
?>