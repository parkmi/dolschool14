<?php
defined('_JEXEC') or die( 'Restricted access' );

$notification_types = array();

$notification = new stdClass();
$notification->id = 1;
$notification->name = 'OnCourseEnrollment';
$notification->markers = "{name}, {email}, {username}, {coursename}, {courselink}";//, {groupname}, {date}, {time}, {datetime}";
$notification->manager_role_types = array(2,3,4,5); 
$notification_types[] = $notification;
/** ***********************************************************/

$notification = new stdClass();
$notification->id = 2;
$notification->name = 'OnCourseCompletion';
$notification->markers = "{name}, {email}, {username}, {coursename}, {lmslink}";//, {groupname}, {date}, {time}, {datetime}";
$notification->manager_role_types = array(2,3,4,5);
$notification_types[] = $notification;
/** ***********************************************************/

$notification = new stdClass();
$notification->id = 3;
$notification->name = 'OnCSVUserImport';
$notification->markers = "{password}, {name}, {email}, {username}, {coursename}, {lmslink}";//, {groupname}, {date}, {time}, {datetime}";
$notification->wrap_markers = "{ifcreated}{/ifcreated}, {ifcourse}{/ifcourse}, {ifgroup}{/ifgroup}";
$notification->manager_role_types = array(2,3,4,5);
$notification_types[] = $notification;
/** ***********************************************************/

$notification = new stdClass();
$notification->id = 4;
$notification->name = 'OnQuizCompletion';
$notification->markers = "{name}, {email}, {username}, {quizname}, {courselink}";//, {groupname}, {date}, {time}, {datetime}";
$notification->manager_role_types = array(2,3,4,5);
$notification_types[] = $notification;
/** ***********************************************************/

$notification = new stdClass();
$notification->id = 5;
$notification->name = 'OnHomeworkReview';
$notification->markers = "{name}, {email}, {username}, {homeworkname}, {coursename}, {courselink}";//, {groupname}, {date}, {time}, {datetime}";
$notification->manager_role_types = array(2,3,4,5);
$notification_types[] = $notification;
/** ***********************************************************/
$notification = new stdClass();
$notification->id = 6;
$notification->name = 'OnNewDropboxFile';
$notification->markers = "{name}, {email}, {username}, {filename}, {coursename}, {courselink}";//, {groupname}, {date}, {time}, {datetime}";
$notification->manager_role_types = array(2,3,4,5);
$notification_types[] = $notification;
/** ***********************************************************/
$notification = new stdClass();
$notification->id = 7;
$notification->name = 'OnLPathCompletion';
$notification->markers = "{name}, {email}, {username}, {lpathname}, {coursename}, {courselink}";//, {groupname}, {date}, {time}, {datetime}";
$notification->manager_role_types = array(2,3,4,5);
$notification_types[] = $notification;
/** ***********************************************************/
$notification = new stdClass();
$notification->id = 8;
$notification->name = 'OnHomeworkSubmission';
$notification->markers = "{name}, {email}, {username}, {homeworkname}, {coursename}, {courselink}";//, {groupname}, {date}, {time}, {datetime}";
$notification->manager_role_types = array(2,3,4,5);
$notification_types[] = $notification;
/** ***********************************************************/

?>