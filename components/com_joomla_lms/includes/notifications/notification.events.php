<?php
defined('_JEXEC') or die( 'Restricted access' );

$notification_events = array();

/** *************************OnCourseEnrolment group events begin**********************************/

$notification_type = 1;

$event = new stdClass();
$event->id = 1;
$event->name = 'SelfEnrolment into free course';
$event->description = '';
$event->event_action = 'OnSelfEnrolmentIntoFreeCourse'; // unique_name – событие которое будет обрабатываться плагином.
$event->learner_template = 1;
$event->manager_template = 1;
$event->use_learner_template = true;
$event->use_manager_template = true;
$event->learner_template_disabled = false;
$event->manager_template_disabled = false;
$event->skip_managers = false;
$event->notification_type = $notification_type;
$event->selected_manager_roles = array(1,4,5);
$event->disabled = true;

$notification_events[] = $event;

/** ***********************************************************/
$event = new stdClass();
$event->id = 2;
$event->name = 'SelfEnrolment into paid course';
$event->description = '';
$event->event_action = 'OnSelfEnrolmentIntoPaidCourse'; // unique_name – событие которое будет обрабатываться плагином.
$event->learner_template = 1;
$event->manager_template = 1;
$event->use_learner_template = true;
$event->use_manager_template = true;
$event->learner_template_disabled = false;
$event->manager_template_disabled = false;
$event->skip_managers = false;
$event->notification_type = $notification_type;
$event->selected_manager_roles = array(1,4,5);
$event->disabled = true;

$notification_events[] = $event;

/** ***********************************************************/
$event = new stdClass();
$event->id = 3;
$event->name = 'Enrolment into the course by course teacher';
$event->description = '';
$event->event_action = 'OnEnrolmentInCourseByTeacher'; // unique_name – событие которое будет обрабатываться плагином.
$event->learner_template = 1;
$event->manager_template = 1;
$event->use_learner_template = true;
$event->use_manager_template = true;
$event->learner_template_disabled = false;
$event->manager_template_disabled = false;
$event->skip_managers = false;
$event->notification_type = $notification_type;
$event->selected_manager_roles = array(1,4,5);
$event->disabled = true;

$notification_events[] = $event;

/** ***********************************************************/
$event = new stdClass();
$event->id = 4;
$event->name = 'Enrolment into the course by Joomla Administrator';
$event->description = '';
$event->event_action = 'OnEnrolmentInCourseByJoomlaAdmin'; // unique_name – событие которое будет обрабатываться плагином.
$event->learner_template = 1;
$event->manager_template = 1;
$event->use_learner_template = true;
$event->use_manager_template = true;
$event->learner_template_disabled = false;
$event->manager_template_disabled = false;
$event->skip_managers = false;
$event->notification_type = $notification_type;
$event->selected_manager_roles = array(1,4,5);
$event->disabled = true;

$notification_events[] = $event;
/** *****************************OnCourseEnrolment group events end******************************/

/** *****************************OnCourseCompletion group events begin******************************/

$notification_type = 2;

$event = new stdClass();
$event->id = 5;
$event->name = 'User completes the course';
$event->description = '';
$event->event_action = 'OnUserCompletesTheCourse'; // unique_name – событие которое будет обрабатываться плагином.
$event->learner_template = 1;
$event->manager_template = 1;
$event->use_learner_template = true;
$event->use_manager_template = true;
$event->learner_template_disabled = false;
$event->manager_template_disabled = false;
$event->skip_managers = false;
$event->notification_type = $notification_type;
$event->selected_manager_roles = array(1,4,5);
$event->disabled = true;

$notification_events[] = $event;

/** ***********************************************************/
$event = new stdClass();
$event->id = 6;
$event->name = 'Teacher marks course completion';
$event->description = '';
$event->event_action = 'OnTeacherMarksCourseCompletion'; // unique_name – событие которое будет обрабатываться плагином.
$event->learner_template = 3;
$event->manager_template = 3;
$event->use_learner_template = true;
$event->use_manager_template = true;
$event->learner_template_disabled = false;
$event->manager_template_disabled = false;
$event->skip_managers = false;
$event->notification_type = $notification_type;
$event->selected_manager_roles = array(1,4,5);
$event->disabled = true;

$notification_events[] = $event;
/** *****************************OnCourseCompletion group events end******************************/

/** *****************************OnCSVImportUser group events begin******************************/
$notification_type = 3;

$event = new stdClass();
$event->id = 7;
$event->name = 'Import user into LMS from CSV';
$event->description = '';
$event->event_action = 'OnCSVImportUser'; // unique_name – событие которое будет обрабатываться плагином.
$event->learner_template = 4;
$event->manager_template = 4;
$event->use_learner_template = true;
$event->use_manager_template = true;
$event->learner_template_disabled = false;
$event->manager_template_disabled = false;
$event->skip_managers = false;
$event->notification_type = $notification_type;
$event->selected_manager_roles = array(1,4,5);
$event->disabled = true;

$notification_events[] = $event;
/** *****************************OnCSVImportUser group events end******************************/


$notification_type = 4; //on quiz completion

$event = new stdClass();
$event->id = 8;
$event->name = 'User completes the quiz';
$event->description = '';
$event->event_action = 'OnQuizCompletion'; // unique_name – событие которое будет обрабатываться плагином.
$event->learner_template = 5;
$event->manager_template = 13;
$event->use_learner_template = true;
$event->use_manager_template = true;
$event->learner_template_disabled = false;
$event->manager_template_disabled = false;
$event->skip_managers = false;
$event->notification_type = $notification_type;
$event->selected_manager_roles = array(1,4,5);
$event->disabled = true;

$notification_events[] = $event;




$notification_type = 5; //on homework review

$event = new stdClass();
$event->id = 9;
$event->name = 'Teacher reviews homework submission';
$event->description = '';
$event->event_action = 'OnHomeworkReview'; // unique_name – событие которое будет обрабатываться плагином.
$event->learner_template = 6;
$event->manager_template = 12;
$event->use_learner_template = true;
$event->use_manager_template = true;
$event->learner_template_disabled = false;
$event->manager_template_disabled = false;
$event->skip_managers = false;
$event->notification_type = $notification_type;
$event->selected_manager_roles = array(1,4,5);
$event->disabled = true;

$notification_events[] = $event;




$notification_type = 6; //on new dropbox

$event = new stdClass();
$event->id = 10;
$event->name = 'New Dropbox file received';
$event->description = '';
$event->event_action = 'OnNewDropboxFile'; // unique_name – событие которое будет обрабатываться плагином.
$event->learner_template = 7;
$event->manager_template = 7;
$event->use_learner_template = true;
$event->use_manager_template = true;
$event->learner_template_disabled = false;
$event->manager_template_disabled = false;
$event->skip_managers = true;
$event->notification_type = $notification_type;
$event->selected_manager_roles = array(1,4,5);
$event->disabled = true;

$notification_events[] = $event;


$notification_type = 7; //on lpath completion

$event = new stdClass();
$event->id = 11;
$event->name = 'User completes learning path';
$event->description = '';
$event->event_action = 'OnLPathCompletion'; // unique_name – событие которое будет обрабатываться плагином.
$event->learner_template = 8;
$event->manager_template = 9;
$event->use_learner_template = true;
$event->use_manager_template = true;
$event->learner_template_disabled = false;
$event->manager_template_disabled = false;
$event->skip_managers = false;
$event->notification_type = $notification_type;
$event->selected_manager_roles = array(1,4,5); //roles IDs
$event->disabled = true;

$notification_events[] = $event;


$notification_type = 8; //on homework submission

$event = new stdClass();
$event->id = 12;
$event->name = 'User submits homework';
$event->description = '';
$event->event_action = 'OnHomeworkSubmission';
$event->learner_template = 11;
$event->manager_template = 10;
$event->use_learner_template = true;
$event->use_manager_template = true;
$event->learner_template_disabled = false;
$event->manager_template_disabled = false;
$event->skip_managers = false;
$event->notification_type = $notification_type;
$event->selected_manager_roles = array(1,4,5); //roles IDs
$event->disabled = true;

$notification_events[] = $event;
?>