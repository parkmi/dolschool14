<?php
defined('_JEXEC') or die( 'Restricted access' );

$email_templates = array();

$template = new stdClass();
$template->id = 1; 
$template->name = 'Email template for Course Enrollment by CSV import (core)';
$template->subject = '{coursename} enrollment info';
$template->template_html_file = 'default.php';
$template->template_alt_text = 'Dear {name},'."\n"
.''."\n"
.'You have successfully enrolled in "{coursename}".'."\n"
.'Please use the following link to access the course:'."\n"
.'{courselink}'."\n"
.''."\n"
.'Best Regards,'."\n"
.'{sitename}'."\n"
;
$template->notification_type = 3;
$email_templates[] = $template;

$template = new stdClass();
$template->id = 2; 
$template->name = 'Email notification template for self Course Enrollment (core)';
$template->subject = '{coursename} enrollment info';
$template->template_html_file = 'course_enrolment.php';
$template->template_alt_text = 'Dear {name},'."\n"
.''."\n"
.'You have successfully enrolled in "{coursename}".'."\n"
.'Please use the following link to access the course:'."\n"
.'{courselink}'."\n"
.''."\n"
.'Best Regards,'."\n"
.'{sitename}'."\n"
;
$template->notification_type = 1;
$email_templates[] = $template;

$template = new stdClass();
$template->id = 3; 
$template->name = 'Course Completion email notification template';
$template->subject = 'You have completed {coursename}';
$template->template_html_file = 'course_completion.php';
$template->template_alt_text = 'Dear {name},'."\n"
.''."\n"
.'Congratulations on completing "{coursename}"!'."\n"
.'If you would like to review your studies or check other courses, please feel free to revisit our learning center here:'."\n"
.'{lmslink}'."\n"
.''."\n"
.'Best Regards,'."\n"
.'{sitename}'."\n"
;
$template->notification_type = 2;
$email_templates[] = $template;

$template = new stdClass();
$template->id = 4; 
$template->name = 'Email notification template for Users imported from CSV (core)';
$template->subject = 'Welcome to {sitename}!';
$template->template_html_file = 'csv_import_user.php';
$template->template_alt_text = 'Dear {name},'."\n"
.''."\n"
.'You have been registered at {sitename}.'."\n"
.'Please use the following credentials to login:'."\n"
.'username: {username}'."\n"
.'password: {password}'."\n"
.'Please feel free to access our online courses using the following link:'."\n"
.'{lmslink}'."\n"
.''."\n"
.'Best Regards,'."\n"
.'{sitename}'."\n"
;
$template->notification_type = 3; 
$email_templates[] = $template;

$template = new stdClass();
$template->id = 5; 
$template->name = 'Quiz completion email notification template (core)';
$template->subject = 'You have completed {quizname}';
$template->template_html_file = 'quiz_completion.php';
$template->template_alt_text = 'Dear {name},'."\n"
.''."\n"
.'Congratulations on completing "{quizname}"!'."\n"
.'Please feel free to check other resources in the course:'."\n"
.'{courselink}'."\n"
.''."\n"
.'Best Regards,'."\n"
.'{sitename}'."\n"
;
$template->notification_type = 4; 
$email_templates[] = $template;

$template = new stdClass();
$template->id = 6; 
$template->name = 'Email notification template for HomeWork reviews (core)';
$template->subject = 'Your homework "{homeworkname}" was reviewed';
$template->template_html_file = 'homework_reviewed.php';
$template->template_alt_text = 'Dear {name},'."\n"
.''."\n"
.'Your homework "{homeworkname}" you submitted for "{coursename}" was reviewed by the course instructor.'."\n"
.'You can view your results by accessing the course here:'."\n"
.'{courselink}'."\n"
.''."\n"
.'Best Regards,'."\n"
.'{sitename}'."\n"
;
$template->notification_type = 5; 
$email_templates[] = $template;

$template = new stdClass();
$template->id = 7; 
$template->name = 'Email notification template for DropBox messages (core)';
$template->subject = 'You have received a new file via DropBox';
$template->template_html_file = 'dropbox_message.php';
$template->template_alt_text = 'Dear {name},'."\n"
.''."\n"
.'You have received {filename} in your dropbox in {coursename}.'."\n"
.'You can check the submitted file by accessing the course here:'."\n"
.'{courselink}'."\n"
.''."\n"
.'Best Regards,'."\n"
.'{sitename}'."\n"
;
$template->notification_type = 6; 
$email_templates[] = $template;

$template = new stdClass();
$template->id = 8; 
$template->name = 'LearningPath completion Email notification template (core)';
$template->subject = 'You have completed {lpathname}';
$template->template_html_file = 'lpath_completion.php';
$template->template_alt_text = 'Dear {name},'."\n"
.''."\n"
.'Congratulations on completing "{lpathname}"!'."\n"
.'Please feel free to check other resources in the course:'."\n"
.'{courselink}'."\n"
.''."\n"
.'Best Regards,'."\n"
.'{sitename}'."\n"
;
$template->notification_type = 7; 
$email_templates[] = $template;

$template = new stdClass();
$template->id = 9; 
$template->name = 'LearningPath completion Email notification template for managers (core)';
$template->subject = 'User {name} has completed {lpathname}';
$template->template_html_file = 'lpath_completion_managers.php';
$template->template_alt_text = 'Hello,'."\n"
.''."\n"
.'{name} has completed "{lpathname}".'."\n"
.'Review the latest course activities:'."\n"
.'{courselink}'."\n"
.''."\n"
.'Best Regards,'."\n"
.'{sitename}'."\n"
;
$template->notification_type = 7; 
$email_templates[] = $template;

$template = new stdClass();
$template->id = 10;
$template->name = 'HomeWork submission email notification template for managers (core)';
$template->subject = 'New work for "{homeworkname}" submitted';
$template->template_html_file = 'homework_submitted_managers.php';
$template->template_alt_text = 'Hello,'."\n"
.''."\n"
.'{name} has submitted a work for "{homeworkname}" in course "{coursename}".'."\n"
.'Review the submission:'."\n"
.'{courselink}'."\n"
.''."\n"
.'Best Regards,'."\n"
.'{sitename}'."\n"
;
$template->notification_type = 8; 
$email_templates[] = $template;

$template = new stdClass();
$template->id = 11;
$template->name = 'HomeWork submission email notification template (core)';
$template->subject = 'Your work for "{homeworkname}" is submitted for review';
$template->template_html_file = 'homework_submitted.php';
$template->template_alt_text = 'Dear {name},'."\n"
.''."\n"
.'You have submitted a work for "{homeworkname}" in course "{coursename}".'."\n"
.'Your submission is awaiting teacher\'s review, you can check the status by accessing the course here:'."\n"
.'{courselink}'."\n"
.''."\n"
.'Best Regards,'."\n"
.'{sitename}'."\n"
;
$template->notification_type = 8; 
$email_templates[] = $template;

$template = new stdClass();
$template->id = 12; 
$template->name = 'HomeWork review email notification template for managers (core)';
$template->subject = '{name}\'s work for "{homeworkname}" has been reviewed';
$template->template_html_file = 'homework_reviewed_managers.php';
$template->template_alt_text = 'Hello,'."\n"
.''."\n"
.'The work for "{homeworkname}" submitted by {name} has been reviewed by the course instructor.'."\n"
.'You can view the detailed information by accessing the course here:'."\n"
.'{courselink}'."\n"
.''."\n"
.'Best Regards,'."\n"
.'{sitename}'."\n"
;
$template->notification_type = 5; 
$email_templates[] = $template;

$template = new stdClass();
$template->id = 13; 
$template->name = 'Quiz completion email notification template for managers (core)';
$template->subject = 'User {name} has completed the quiz {quizname}';
$template->template_html_file = 'quiz_completion_managers.php';
$template->template_alt_text = 'Hello,'."\n"
.''."\n"
.'User {name} has completed the quiz "{quizname}" in "{coursename}".'."\n"
.'Review user\'s results:'."\n"
.'{courselink}'."\n"
.''."\n"
.'Best Regards,'."\n"
.'{sitename}'."\n"
;
$template->notification_type = 4; 
$email_templates[] = $template;
?>