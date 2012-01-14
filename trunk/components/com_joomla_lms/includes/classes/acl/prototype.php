<?php 
/**
* includes/classes/acl/teacher.php
* Joomla LMS Component
* * * ElearningForce Biz
**/

// no direct access
defined( '_JLMS_EXEC' ) or die( 'Restricted access' );

function & JLMS_role_prototype($roletype_id = '') {
	$role = array();
	
	if(isset($roletype_id) && ($roletype_id == 2 || $roletype_id == 3 || $roletype_id == 4 || $roletype_id == 5)) {
			$permissions_advanced = new stdClass();
			$permissions_advanced->assigned_groups_only = "Work with assigned groups only";
			$permissions_advanced->view_all_course_categories = "View all course categories";
			
			$permissions_advanced->role_types = array(1,2,3,4); // teacher + admin + assistant
		$role['advanced'] = & $permissions_advanced;
	} elseif (isset($roletype_id) && $roletype_id == 1 ) {
			$permissions_advanced = new stdClass();
			$permissions_advanced->assigned_groups_only = "Work within assigned group only<br /><small>(dropbox, chat, mailbox and forum)</small>";
			$permissions_advanced->role_types = array(1); // teacher + admin + assistant
		$role['advanced'] = & $permissions_advanced;
	}

		$permissions_lms = new stdClass();
		$permissions_lms->create_course = "Create courses";
		$permissions_lms->order_courses = "Order courses";
		$permissions_lms->role_types = array(2,4); // teacher + admin
	$role['lms'] = & $permissions_lms;	

		$permissions_docs = new stdClass();
		$permissions_docs->view = "View list of documents (this setting grants access to the 'documents' tool)";
		$permissions_docs->view_all = "View unpublished items in the list";
		$permissions_docs->order = "Order items in the list";
		$permissions_docs->publish = "Publish/Unpublish items";
		$permissions_docs->manage = "Manage items (create/edit/delete)";
		$permissions_docs->only_own_items = "User can manage only their own items";
		$permissions_docs->only_own_role = "User can manage only items created by users of the same user role";
		$permissions_docs->set_permissions = "Set custom permissions for folders";
		$permissions_docs->ignore_permissions = "Ignore custom permissions configured for folders";
		$permissions_docs->role_types = array(1,2,4,5); // learner + teacher + admin + assistant
	$role['docs'] = & $permissions_docs;

		$permissions_quizzes = new stdClass();
		$permissions_quizzes->view = "View list of quizzes (this setting grants access to the 'quizzes' tool)";
		$permissions_quizzes->view_all =  "View all (published and unpublished) items in the list";
		$permissions_quizzes->view_stats = "View quizzes statistics";
		$permissions_quizzes->publish = "Publish/Unpublish items";
		$permissions_quizzes->manage = "Manage quizzes/questions (create/edit/delete)";
		$permissions_quizzes->manage_pool = "Manage course questions pool";
		$permissions_quizzes->role_types = array(1,2,4,5); // learner + teacher + admin + assistant
	$role['quizzes'] = & $permissions_quizzes;

		$permissions_links = new stdClass();
		$permissions_links->view = "View list of links (this setting grants access to the 'links' tool)";
		$permissions_links->view_all = "View all (published and unpublished) items in the list";
		$permissions_links->order = "Order items in the list";
		$permissions_links->publish = "Publish/Unpublish items";
		$permissions_links->manage = "Manage links (create/edit/delete)";
		$permissions_links->only_own_items = "User can manage only their own items";
		$permissions_links->only_own_role = "User can manage only items created by users of the same user role";
		$permissions_links->role_types = array(1,2,4,5); // learner + teacher + admin + assistant
	$role['links'] = & $permissions_links;

		$permissions_lpaths = new stdClass();
		$permissions_lpaths->view = "View list of Learning Paths (this setting grants acess to the 'learning paths' tool)";
		$permissions_lpaths->view_all = "View all (published and unpublished) items in the list";
		$permissions_lpaths->order = "Order items in the list";
		$permissions_lpaths->publish = "Publish/Unpublish items";
		$permissions_lpaths->manage = "Manage LPaths (create/edit/delete)";
		$permissions_lpaths->only_own_items = "User can manage only their own items";
		$permissions_lpaths->only_own_role = "User can manage only items created by users of the same user role";
		$permissions_lpaths->role_types = array(1,2,4,5); // learner + teacher + admin + assistant
	$role['lpaths'] = & $permissions_lpaths;

		$permissions_announce = new stdClass();
		$permissions_announce->view = "View list of announcements (this setting grants acess to the 'announcements' tool)";
		$permissions_announce->manage = "View all (published and unpublished) items in the list";
		$permissions_announce->only_own_items = "User can manage only their own items";
		$permissions_announce->only_own_role = "User can manage only items created by users of the same user role";
		$permissions_announce->role_types = array(1,2,3,4,5); // learner + teacher + ceo + admin + assistant
	$role['announce'] = & $permissions_announce;
	
		$permissions_dropbox = new stdClass();
		$permissions_dropbox->view = "View list of DropBox items";
		$permissions_dropbox->send_to_teachers = "Send items to teachers";
		$permissions_dropbox->send_to_learners = "Send items to learners";
		$permissions_dropbox->mark_as_corrected = "Mark items as corrected";
		$permissions_dropbox->role_types = array(1,2,4,5); // learner + teacher + admin + assistant
	$role['dropbox'] = & $permissions_dropbox;
	
		$permissions_homework = new stdClass();
		$permissions_homework->view = "View list of homework items";
		$permissions_homework->view_stats = "View statistics";
		$permissions_homework->view_all = "View all (published and unpublished) items in the list";
		$permissions_homework->publish = "Publish/Unpublish items";
		$permissions_homework->manage = "Manage items (create/edit/delete)";		
		$permissions_homework->role_types = array(1,2,3,4,5); // learner + teacher + ceo + admin + assistant
	$role['homework'] = & $permissions_homework;
	
		$permissions_attendance = new stdClass();
		$permissions_attendance->view = "Access to attendance tool";
		$permissions_attendance->manage = "Manage attendance records";
		$permissions_attendance->role_types = array(1,2,3,4,5); // learner + teacher + ceo + admin + assistant
	$role['attendance'] = & $permissions_attendance;

		$permissions_chat = new stdClass();
		$permissions_chat->view = "Access course/group chat";
		$permissions_chat->manage = "Access all groups chats ('manager' rights)";
		$permissions_chat->role_types = array(1,2,4,5); // learner + teacher + admin + assistant
	$role['chat'] = & $permissions_chat;

		$permissions_conf = new stdClass();
		$permissions_conf->view = "Access course conference tool";
		$permissions_conf->manage = "Participate in the conference as 'manager'";
		$permissions_conf->role_types = array(1,2,4,5); // learner + teacher + admin + assistant
	$role['conference'] = & $permissions_conf;

		$permissions_gbook = new stdClass();
		$permissions_gbook->view = "View GradeBook";
		$permissions_gbook->manage = "Manage course 'completion' status";

		if(isset($roletype_id) && ($roletype_id == 2|| $roletype_id == 4 || $roletype_id == 5)) {
			$permissions_gbook->configure = "Configure (create/edit grades, scales, etc.)";
		}
		
		$permissions_gbook->role_types = array(1,2,3,4,5); // learner + teacher + ceo + admin + assistant
	$role['gradebook'] = & $permissions_gbook;

		$permissions_track = new stdClass();
		$permissions_track->manage = "View course tracking information";
		$permissions_track->clear_stats = "Clear course tracking statistics";
		//$permissions_track->view = 1;
		$permissions_track->role_types = array(2,4,5); // teacher + admin + assistant
	$role['tracking'] = & $permissions_track;

		$permissions_mail = new stdClass();
		$permissions_mail->view = "Use course mailbox";
		$permissions_mail->manage = "Manage course mailbox";
		$permissions_mail->role_types = array(1,2,4,5); // learner + teacher + admin + assistant
	$role['mailbox'] = & $permissions_mail;

		$permissions_users = new stdClass();
		$permissions_users->manage = "Manage course participants";
		$permissions_users->manage_teachers = "Manage course teachers/assistants";
		$permissions_users->view = "View list of course participants";
		$permissions_users->import_users = "Import new users";
		$permissions_users->role_types = array(2,3,4,5); // teacher + admin + assistant
	$role['users'] = & $permissions_users;

		$permissions_course = new stdClass();
		$permissions_course->edit = "Edit course properties";
		$permissions_course->manage_settings = "Manage course settings";
		$permissions_course->manage_certificates = "Manage course certificates";
		$permissions_course->role_types = array(2,4,5); // teacher + admin + assistant
	$role['course'] = & $permissions_course;

		$permissions_library = new stdClass();
		$permissions_library->only_own_items = "Teacher can manage only their own items";
		$permissions_library->role_types = array(2,4); // teacher + admin
	$role['library'] = & $permissions_library;
	
	$permissions_ceostore = new stdClass();
//		$permissions_ceostore->store_manager = "Store Manager";
//		$permissions_ceostore->corporate_admin = "Corporate Admin";
		$permissions_ceostore->view_all_users = "View All users";
		$permissions_ceostore->role_types = array(3); // ceo
	$role['ceo'] = & $permissions_ceostore;

	return $role;
}
?>