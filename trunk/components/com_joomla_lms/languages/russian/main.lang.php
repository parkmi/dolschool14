<?php
/**
* /languages/russian/main.lang.php
* (c) JoomaLMS eLearning Software http://www.joomlalms.com/
**/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

$JLMS_LANGUAGE['_JLMS_REPORTING_USERGROUP'] = '������ ������������';
$JLMS_LANGUAGE['_JLMS_REPORTING_ACCESSED_TIMES'] = 'Accessed Times';
$JLMS_LANGUAGE['_JLMS_REPORTING_TOTAL'] = '�����';

// See this link for details: http://en.wikipedia.org/wiki/Locale
$JLMS_LANGUAGE['_JLMS_LOCALE'] = 'ru_RU';
//For windows server see locale here: http://msdn.microsoft.com/library/default.asp?url=/library/en-us/vclib/html/_crt_language_strings.asp
$JLMS_LANGUAGE['_JLMS_LOCALE_WIN'] = 'russian';

$JLMS_LANGUAGE['_JLMS_CLOSE_ALT_TITLE'] = '�������';

$JLMS_LANGUAGE['_JLMS_USER_OPTIONS_NOTES'] = '����������';
$JLMS_LANGUAGE['_JLMS_USER_OPTIONS_NOTES_ADD'] = '��������';
$JLMS_LANGUAGE['_JLMS_USER_OPTIONS_NOTES_EDIT'] = '�������������';
$JLMS_LANGUAGE['_JLMS_USER_OPTIONS_NOTES_NOTICE'] = '����������';

$JLMS_LANGUAGE['_JLMS_QUIZ_STATUS_COMPLETED'] = '�����������';
$JLMS_LANGUAGE['_JLMS_QUIZ_STATUS_INCOMPLETE'] = '�������������';
$JLMS_LANGUAGE['_JLMS_QUIZ_STATUS_PASSED'] = '������';
$JLMS_LANGUAGE['_JLMS_QUIZ_STATUS_FAILED'] = '��������';

/* 1.1.0 */
$JLMS_LANGUAGE['_JLMS_HOME_LATEST_FORUM_POSTS_TITLE'] = '��������� ��������� ������';
$JLMS_LANGUAGE['_JLMS_HOME_LATEST_FORUM_POSTS_NO_ITEMS'] = '��� ����� ��������� �� �������';

$JLMS_LANGUAGE['_JLMS_HOME_CERTIFICATES_SN'] = 'S/N';
$JLMS_LANGUAGE['_JLMS_HOME_CERTIFICATES_TITLE'] = '�����������';
$JLMS_LANGUAGE['_JLMS_HOME_CERTIFICATES_NO_ITEMS'] = '� ��� ��� ������������ ���';
$JLMS_LANGUAGE['_JLMS_HOME_MAILBOX_TITLE'] = '�������� ���� ( X / Y )';
$JLMS_LANGUAGE['_JLMS_HOME_MAILBOX_NO_ITEMS'] = '��� ����� ���������';
$JLMS_LANGUAGE['_JLMS_HOME_MAILBOX_FROM'] = '��';

$JLMS_LANGUAGE['_JLMS_FILTER_LPATHS'] = ' ��� ��������� ��������';

/* 1.0.7 */
$JLMS_LANGUAGE['_JLMS_EXPORT_TO'] = '�������������� �';
$JLMS_LANGUAGE['_JLMS_REQUESTED_RESOURCE_WILL_BE_RELEASED'] = '��������� ������ ����� �������� �����';

$JLMS_LANGUAGE['_JLMS_FILTER_ALL_SUBGROUPS'] = '��� ���������';

$JLMS_LANGUAGE['_JLMS_STATUS_PUBLISHED_AND_HIDDEN'] = '�������, �� �����';
$JLMS_LANGUAGE['_JLMS_STATUS_CONFIGURED_PREREQUISITES'] = '��������������� ������� ���������';
$JLMS_LANGUAGE['_JLMS_STATUS_UPCOMING'] = '�������, ������� ����� ����� ��������';
$JLMS_LANGUAGE['_JLMS_WILL_BE_RELEASED_IN'] = '����� �������� ��� ������� �����';
$JLMS_LANGUAGE['_JLMS_RELEASED_AFTER_ENROLLMENT'] = '����� ����������';
$JLMS_LANGUAGE['_JLMS_RELEASED_IN_DAYS'] = '����';
$JLMS_LANGUAGE['_JLMS_RELEASED_IN_HOURS'] = '�����';
$JLMS_LANGUAGE['_JLMS_RELEASED_IN_MINUTES'] = '�����';

$JLMS_LANGUAGE['_JLMS_LIMIT_RESOURCE_TO_GROUPS'] = '������ ��������� ������������� �������� ��������:';
$JLMS_LANGUAGE['_JLMS_LIMIT_RESOURCE_USERGROUPS'] = '������ �������������:';

$JLMS_LANGUAGE['_JLMS_OUTDOCS_JS_CONFIRM_DELETE'] = '�� �������, ��� ������ ������� ��������� ���������?';

$JLMS_LANGUAGE['_JLMS_PRINT_RESULTS'] = '���������� ����������';
$JLMS_LANGUAGE['_JLMS_FULL_VIEW_BUTTON'] = '[������ �������������]';

$JLMS_LANGUAGE['_JLMS_USERS_NAME'] = '���';
$JLMS_LANGUAGE['_JLMS_USERS_EMAIL'] = 'E-mail';
$JLMS_LANGUAGE['_JLMS_USER_INFORMATION'] = '���������� � ������������';
$JLMS_LANGUAGE['_JLMS_REPORTS_SCORM'] = "����� SCORM";

$JLMS_LANGUAGE['_JLMS_FILE_UPLOAD'] = '��������� ����';
$JLMS_LANGUAGE['_JLMS_ATTACHED_FILE'] = '�������������� ����';

$JLMS_LANGUAGE['_JLMS_COMMENTS'] = '�����������:';

$JLMS_LANGUAGE['_JLMS_RECURRENT_PAYMENT_THEN'] = "����� ";
$JLMS_LANGUAGE['_JLMS_RECURRENT_PAYMENT_THE_NEXT'] = " ��������� ";
$JLMS_LANGUAGE['_JLMS_RECURRENT_PAYMENT_FREE'] = "��������� ";


/* * * * text messages for recurrent payments
 * do not change {free} {a1} {a2} {a3} {cur} {next} {then} {srt} text snippets
 * EXAMPLES: 
 // 5 USD for the first day
 // 5 USD for the first 10 days
 // then 10 USD for the next day
 // then 10 USD for the next 5 days
 // then 10 USD for one day
 // then 10 USD for 5 days
 // then 10 USD for each day
 // then 10 USD for each day, for 3 installments
 // then 10 USD for each 5 days, for 3 installments
 * * * */
$JLMS_LANGUAGE['_JLMS_RECURRENT_PAYMENT_FIRST_DAY'] = "{free}{a1} {cur} �� ������ ����";
$JLMS_LANGUAGE['_JLMS_RECURRENT_PAYMENT_FIRST_DAYS'] = "{free}{a1} {cur} �� ������ {p1} ����";
$JLMS_LANGUAGE['_JLMS_RECURRENT_PAYMENT_NEXT_DAY'] = "{then}{free}{a2} {cur} �� {next} ����";
$JLMS_LANGUAGE['_JLMS_RECURRENT_PAYMENT_NEXT_DAYS'] = "{then}{free}{a2} {cur} �� {next}{p2} ����";
$JLMS_LANGUAGE['_JLMS_RECURRENT_PAYMENT_ONE_DAY'] = "{then}{a3} {cur} �� ���� ����";
$JLMS_LANGUAGE['_JLMS_RECURRENT_PAYMENT_MORE_DAYS'] = "{then}{a3} {cur} �� {p3} ����";
$JLMS_LANGUAGE['_JLMS_RECURRENT_PAYMENT_FOREACH'] = "{then}{a3} {cur} �� ������ ����";
$JLMS_LANGUAGE['_JLMS_RECURRENT_PAYMENT_FOREACH_DAYS'] = "{then}{a3} {cur} �� ������ {p3} ����";
$JLMS_LANGUAGE['_JLMS_RECURRENT_PAYMENT_INSTALLMENTS'] = "{then}{a3} {cur} �� ������ ����, � {srt} ������";
$JLMS_LANGUAGE['_JLMS_RECURRENT_PAYMENT_INSTALLMENTS_DAYS'] = "{then}{a3} {cur} �� ������ {p3} ����, � {srt} ������";
$JLMS_LANGUAGE['_JLMS_RECURRENT_MORE_THAN_ONE'] = "����� ������ ������� � ���������";

$JLMS_LANGUAGE['_JLMS_IS_TIME_RELATED'] = '��������� � ���������:';
$JLMS_LANGUAGE['_JLMS_DAYS'] = '����:';
$JLMS_LANGUAGE['_JLMS_HOURS'] = '�����:';
$JLMS_LANGUAGE['_JLMS_MINUTES'] = '�����:';
$JLMS_LANGUAGE['_JLMS_ENROLL_TIME'] = '����� ����������';
/*1.0.6*/

$JLMS_LANGUAGE['_JLMS_STATUS_FUTURE_COURSE'] = '������� ����';

$JLMS_LANGUAGE['_JLMS_NO_SHARED_ITEMS'] = '� ���������� ��� ��������, ��������� ��� ���������� � �����.';
$JLMS_LANGUAGE['_JLMS_RESOURCES_ARE_IN_USE'] = '��������� ������� ������������ � ������. ��� ����� ������� �� ������.<br /> ����������?';

$JLMS_LANGUAGE['_JLMS_ORDER'] = '�������';
$JLMS_LANGUAGE['_JLMS_REORDER'] = '�������� �������';
$JLMS_LANGUAGE['_JLMS_SAVEORDER'] = '��������� �������';

$JLMS_LANGUAGE['_JLMS_NOT_AUTH_SESSION_EXPIRED'] = "� ��� ��� ������� � �������� ��� ���� ������ ����������� �������. ����������, �������� ��� ���� � ������������� �� �����.";

$JLMS_LANGUAGE['_JLMS_CUSTOM_PERMISSIONS'] = '����� �������';
$JLMS_LANGUAGE['_JLMS_CPERM_ROLE_NAME'] = '�������� ����';
$JLMS_LANGUAGE['_JLMS_CPERM_VIEW'] = '��������';
$JLMS_LANGUAGE['_JLMS_CPERM_VIEW_ALL'] = '�������� ������� ����������';
$JLMS_LANGUAGE['_JLMS_CPERM_ORDER'] = '��������� �������';
$JLMS_LANGUAGE['_JLMS_CPERM_PUBLISH'] = '������';
$JLMS_LANGUAGE['_JLMS_CPERM_MANAGE'] = '����������';
//statistics (quizzes)
$JLMS_LANGUAGE['_JLMS_GRAPH_STATISTICS_CORRECT'] = '���������� �����';
$JLMS_LANGUAGE['_JLMS_GRAPH_STATISTICS_INCORRECT'] = '������������ �����';

//courses list new messages
$JLMS_LANGUAGE['_JLMS_SH_DESCRIPTION'] = '������� ��������:';
$JLMS_LANGUAGE['_JLMS_COURSES_ST_DATE'] = '���� ������';
$JLMS_LANGUAGE['_JLMS_COURSES_END_DATE'] = '���� ���������';
$JLMS_LANGUAGE['_JLMS_COURSES_FEETYPE'] = '��� �����';

$JLMS_LANGUAGE['_JLMS_SB_QUIZ_SELECT_QCATS'] = ' - �������� ��������� ������� - ';
$JLMS_LANGUAGE['_JLMS_SELECT_CATEGORY'] = '����������, �������� ���������';

$JLMS_LANGUAGE['_JLMS_TOOLBAR_VIEW_ALL_NOTICES'] = '����������� ��� �������';

//global reports
$JLMS_LANGUAGE['_JLMS_REPORTS_MODULE'] = "������";
$JLMS_LANGUAGE['_JLMS_REPORTS_SELECT_DATE'] = "����������, �������� ����";
$JLMS_LANGUAGE['_JLMS_REPORTS_ACCESS'] = "����� � ����������";
$JLMS_LANGUAGE['_JLMS_REPORTS_CONCLUSION'] = "����� � ������������� ������";
$JLMS_LANGUAGE['_JLMS_REPORTS_USER'] = "����� �� ������������";
$JLMS_LANGUAGE['_JLMS_REPORTS_CONCLUSION_ROW'] = "���� �������";
$JLMS_LANGUAGE['_JLMS_REPORTS_TOTAL_ROW'] = "�����";
$JLMS_LANGUAGE['_JLMS_REPORTS_ACCESSED_TIMES'] = "���������� ���������:";
$JLMS_LANGUAGE['_JLMS_REPORTS_SELECT_USER'] = "�������� ������������ �� ������";

//new toolbar items
$JLMS_LANGUAGE['_JLMS_TOOLBAR_GQP_PARENT'] = '����� ���� ��������';

//new image titles
$JLMS_LANGUAGE['_JLMS_RESUME_ALT_TITLE'] = "����������";




/* 1.0.5 fixes */
$JLMS_LANGUAGE['_JLMS_MY_CART'] = '��� �������';

$JLMS_LANGUAGE['_JLMS_MOVEUP'] = '����������� �����';
$JLMS_LANGUAGE['_JLMS_MOVEDOWN'] = '����������� ����';

$JLMS_LANGUAGE['_JLMS_COURSES_SEC_CAT'] = '�������������� ���������:';

$JLMS_LANGUAGE['_JLMS_LP_RESOURSE_ISUNAV'] = '������ ����������';
/* 1.0.5 */
$JLMS_LANGUAGE['_JLMS_JS_COOKIES_REQUIRES'] = '��� ������������� ���� ����� ���������� ������������ Javascript � cookies.';
$JLMS_LANGUAGE['_JLMS_IFRAMES_REQUIRES'] = '��� ���������� ������ ������ ����� ����������, ����� ��� ������� ����������� Inline Frames.';
$JLMS_LANGUAGE['_JLMS_JS_FLASH_REQUIRES'] = '����� ������������ ���� ����������, ����������� JavaScript � ���������� ��������� ������ Flash Player.';
$JLMS_LANGUAGE['_JLMS_ADD_ITEM'] = '�������� �������';
$JLMS_LANGUAGE['_JLMS_DEL_ITEM'] = '������� �������';
$JLMS_LANGUAGE['_JLMS_DETAILS'] = '���������';
$JLMS_LANGUAGE['_JLMS_DOWNLOAD'] = '���������';

$JLMS_LANGUAGE['_JLMS_FILE_ATTACHED'] = '���� ����������';
$JLMS_LANGUAGE['_JLMS_FILE_NOT_ATTACHED'] = '���� �� ����������';
/* other texts */
$JLMS_LANGUAGE['_JLMS_ADVANCED'] = '����������'; // i.e. 'Advanced' settings tab
$JLMS_LANGUAGE['_JLMS_SB_SELECT_IMAGE'] = ' - �������� ����������� - '; // for image selectboxes
$JLMS_LANGUAGE['_JLMS_SB_SELECT_CATEGORY'] = ' - �������� ��������� - ';
$JLMS_LANGUAGE['_JLMS_DISABLE_OPTION'] = '- �������������� ��� ����� -';

$JLMS_LANGUAGE['_JLMS_ATT_FILTER_ALL_GROUPS'] = '��� ������';//moved from attendance.lang.php

$JLMS_LANGUAGE['_JLMS_PUBLISH_ELEMENT'] = '��������� �������';
$JLMS_LANGUAGE['_JLMS_UNPUBLISH_ELEMENT'] = '������ �������';
$JLMS_LANGUAGE['_JLMS_DELETE_ELEMENT'] = '������� �������';
$JLMS_LANGUAGE['_JLMS_ADD_ELEMENTS'] = '�������� �������';

$JLMS_LANGUAGE['_JLMS_JANUARY'] = "������";
$JLMS_LANGUAGE['_JLMS_FEBRUARY'] = "�������";
$JLMS_LANGUAGE['_JLMS_MARCH'] = "����";
$JLMS_LANGUAGE['_JLMS_APRIL'] = "������";
$JLMS_LANGUAGE['_JLMS_MAY'] = "���";
$JLMS_LANGUAGE['_JLMS_JUNE'] = "����";
$JLMS_LANGUAGE['_JLMS_JULY'] = "����";
$JLMS_LANGUAGE['_JLMS_AUGUST'] = "������";
$JLMS_LANGUAGE['_JLMS_SEPTEMBER'] = "��������";
$JLMS_LANGUAGE['_JLMS_OCTOBER'] = "�������";
$JLMS_LANGUAGE['_JLMS_NOVEMBER'] = "������";
$JLMS_LANGUAGE['_JLMS_DECEMBER'] = "�������";

$JLMS_LANGUAGE['_JLMS_MONDAY'] = "�����������";
$JLMS_LANGUAGE['_JLMS_TUESDAY'] = "�������";
$JLMS_LANGUAGE['_JLMS_WEDNESDAY'] = "�����";
$JLMS_LANGUAGE['_JLMS_THURSDAY'] = "�������";
$JLMS_LANGUAGE['_JLMS_FRIDAY'] = "�������";
$JLMS_LANGUAGE['_JLMS_SATURDAY'] = "�������";
$JLMS_LANGUAGE['_JLMS_SANDAY'] = "�����������";

$JLMS_LANGUAGE['_JLMS_MON'] = "��";
$JLMS_LANGUAGE['_JLMS_TUE'] = "��";
$JLMS_LANGUAGE['_JLMS_WED'] = "��";
$JLMS_LANGUAGE['_JLMS_THU'] = "��";
$JLMS_LANGUAGE['_JLMS_FRI'] = "��";
$JLMS_LANGUAGE['_JLMS_SAT'] = "��";
$JLMS_LANGUAGE['_JLMS_SAN'] = "��";

/* 1.0.4 */
/* User info */
$JLMS_LANGUAGE['_JLMS_FIELD_REQUIRED'] = '* - ������������ ��� ���������� ����';
$JLMS_LANGUAGE['_JLMS_USER_FIRSTNAME'] = '���:';
$JLMS_LANGUAGE['_JLMS_USER_LASTTNAME'] = '�������:';
$JLMS_LANGUAGE['_JLMS_USER_ADDRESS'] = '�����:';
$JLMS_LANGUAGE['_JLMS_USER_CITY'] = '�����:';
$JLMS_LANGUAGE['_JLMS_USER_STATE'] = '����/�������:';
$JLMS_LANGUAGE['_JLMS_USER_POSTAL_CODE'] = '�������� ������:';
$JLMS_LANGUAGE['_JLMS_USER_COUNTRY'] = '������:';
$JLMS_LANGUAGE['_JLMS_USER_PHONE'] = '�������:';
$JLMS_LANGUAGE['_JLMS_USER_EMAIL'] = '����� ����������� �����:';
/* Check input */
$JLMS_LANGUAGE['_JLMS_ENTER_FIRST_NAME'] 	= '����������, ������� ���� ���.';
$JLMS_LANGUAGE['_JLMS_ENTER_LAST_NAME'] 	= '����������, ������� ���� �������.';
$JLMS_LANGUAGE['_JLMS_ENTER_ADDRESS'] 		= '����������, ������� �����.';
$JLMS_LANGUAGE['_JLMS_ENTER_CITY'] 			= '����������, ������� �����.';
$JLMS_LANGUAGE['_JLMS_ENTER_POSTAL_CODE'] 	= '����������, ������� �������� ������.';
$JLMS_LANGUAGE['_JLMS_ENTER_COUNTRY'] 		= '����������, ������� ������.';
$JLMS_LANGUAGE['_JLMS_ENTER_PHONE'] 		= '����������, ������� �������.';
$JLMS_LANGUAGE['_JLMS_ENTER_EMAIL'] 		= '����������, ������� �������������� ����� ����������� �����.';
$JLMS_LANGUAGE['_JLMS_ENTER_CARD_NUMBER']	= '����������, ������� ����� �����.';
$JLMS_LANGUAGE['_JLMS_ENTER_CARD_CODE']		= '����������, ������� ��� �����.';

$JLMS_LANGUAGE['_JLMS_NO_ITEMS_HERE'] = '� ������ ������� ��� ��������� ���������.';
/* page navigation */
/* for 'Yes' and 'No' you should use _JLMS_NO_ALT_TITLE and _JLMS_YES_ALT_TITLE */
$JLMS_LANGUAGE['_JLMS_PN_RESULTS'] = '�����';
$JLMS_LANGUAGE['_JLMS_PN_OF_TOTAL'] = '��';
$JLMS_LANGUAGE['_JLMS_PN_NO_RESULTS'] = '��� ��������� ��� �����������';
$JLMS_LANGUAGE['_JLMS_PN_FIRST_PAGE'] = '� ������';
$JLMS_LANGUAGE['_JLMS_PN_PREV_PAGE'] = '����������';
$JLMS_LANGUAGE['_JLMS_PN_END_PAGE'] = '� �����';
$JLMS_LANGUAGE['_JLMS_PN_NEXT_PAGE'] = '���������';
$JLMS_LANGUAGE['_JLMS_PN_DISPLAY_NUM'] = '����������';
$JLMS_LANGUAGE['_JLMS_SEPARATOR'] = '�������������� ����';
$JLMS_LANGUAGE['_JLMS_LEFT'] = '�����';
$JLMS_LANGUAGE['_JLMS_CENTER'] = '����������';
$JLMS_LANGUAGE['_JLMS_RIGHT'] = '������';

$JLMS_LANGUAGE['_JLMS_TXT_TOP'] = '������';
$JLMS_LANGUAGE['_JLMS_TXT_BACK'] = '�����';

$JLMS_LANGUAGE['_JLMS_PLEASE_LOGIN'] = "���� �� ��� ���������������� �� �����, ����������, ������������� �� �����.";
$JLMS_LANGUAGE['_JLMS_PLEASE_REGISTER'] = "���� �� ��� �� ���������������� �� �����, ���������� �����������������.";
$JLMS_LANGUAGE['_JLMS_SHOW_LOGIN'] = "�������� ����� �����������.";
$JLMS_LANGUAGE['_JLMS_SHOW_REGISTRATION'] = "�������� ����� �����������.";
$JLMS_LANGUAGE['_JLMS_REGISTRATION_DISABLED'] = "����������� ����������.";
$JLMS_LANGUAGE['_JLMS_REGISTRATION_COMPLETE'] = "�������, ����������� ���������.";
$JLMS_LANGUAGE['_JLMS_LOGIN_SUCCESS'] = "�� ������� ����� � �������.";
$JLMS_LANGUAGE['_JLMS_REGISTRATION_ACTIVATION'] = '���� ������� ������ �������, ������ ��� ��������� ����� ������� ������ ������� �� ��������� ���� ����� ����������� �����.';
$JLMS_LANGUAGE['_JLMS_LOGIN_INCORRECT'] = '�������� ��� ������������ ��� ������. ����������, ���������� ��� ���.';

$JLMS_LANGUAGE['_JLMS_SB_COURSE_FOLDER'] = ' - ����� ����� - ';
$JLMS_LANGUAGE['_JLMS_SB_NO_CERTIFICATE'] = ' - ��� ����������� - ';
$JLMS_LANGUAGE['_JLMS_SB_FIRST_ITEM'] = ' - ������ ������� - ';
$JLMS_LANGUAGE['_JLMS_SB_LAST_ITEM'] = ' - ��������� ������� - ';
$JLMS_LANGUAGE['_JLMS_SB_QUIZ_SELECT_QTYPE'] = ' - �������� ��� ������� - ';
$JLMS_LANGUAGE['_JLMS_SB_SELECT_USER'] = ' - �������� ������������ - ';
$JLMS_LANGUAGE['_JLMS_SB_SELECT_QUIZ'] = ' - �������� ���� - ';
$JLMS_LANGUAGE['_JLMS_SB_FILTER_NONE'] = '���';


$JLMS_LANGUAGE['_JLMS_SB_ALL_USERS'] = ' - ��� ������������ - ';
/* 1.0.2 */
$JLMS_LANGUAGE['_JLMS_SHOW_IN_GRADEBOOK_OPTION'] = '���������� � ������� ������';

/* 1.0.1 */
$JLMS_LANGUAGE['_JLMS_NO_ALT_TITLE'] = '���';
$JLMS_LANGUAGE['_JLMS_SETTINGS_ALT_TITLE'] = '���������';

/* 1.0.0 */
//roles
$JLMS_LANGUAGE['_JLMS_ROLE_TEACHER'] = '�������������';
$JLMS_LANGUAGE['_JLMS_ROLE_STU'] = '�������';
$JLMS_LANGUAGE['_JLMS_ROLE_CEO'] = '������������/��������';
$JLMS_LANGUAGE['_JLMS_GROUP'] = '������';

$JLMS_LANGUAGE['_JLMS_ENTER_NAME'] = '������� ��������:';
$JLMS_LANGUAGE['_JLMS_CHOOSE_FILE'] = '�������� ����';
$JLMS_LANGUAGE['_JLMS_SHORT_DESCRIPTION'] = '������� ��������:';
$JLMS_LANGUAGE['_JLMS_DESCRIPTION'] = '��������:';
$JLMS_LANGUAGE['_JLMS_COMMENT'] = '�����������';
$JLMS_LANGUAGE['_JLMS_TEACHER_COMMENT'] = '����������� �������������:';
$JLMS_LANGUAGE['_JLMS_PLACE_IN'] = '���������� �:';
$JLMS_LANGUAGE['_JLMS_ORDERING'] = '�������:';
$JLMS_LANGUAGE['_JLMS_FILTER'] = '������:';
$JLMS_LANGUAGE['_JLMS_LINK_LOCATION'] = '������������:';
$JLMS_LANGUAGE['_JLMS_PUBLISHING'] = '����������:';
$JLMS_LANGUAGE['_JLMS_DATE'] = '����:';
$JLMS_LANGUAGE['_JLMS_PERIOD'] = '�������� ������:';
$JLMS_LANGUAGE['_JLMS_START_DATE'] = '���� ������:';
$JLMS_LANGUAGE['_JLMS_END_DATE'] = '���� ���������:';
$JLMS_LANGUAGE['_JLMS_DATES_PUBLISH'] = '����� ����������';
$JLMS_LANGUAGE['_JLMS_EDIT'] = '��������';
$JLMS_LANGUAGE['_JLMS_DELETE'] = '�������';
$JLMS_LANGUAGE['_JLMS_VIEW_DETAILS'] = '����������� ������';
$JLMS_LANGUAGE['_JLMS_TOTAL'] = '<b>�����:</b>';
$JLMS_LANGUAGE['_JLMS_ENROLL'] = '���������';
// please ...
$JLMS_LANGUAGE['_JLMS_SELECT_FILE'] = '����������, �������� ����.';
$JLMS_LANGUAGE['_JLMS_PL_ENTER_NAME'] = '����������, ������� ��������.';
//alt & titles for img.buttons
$JLMS_LANGUAGE['_JLMS_SAVE_ALT_TITLE'] = '���������';
$JLMS_LANGUAGE['_JLMS_SEND_ALT_TITLE'] = '���������';
$JLMS_LANGUAGE['_JLMS_PREVIEW_ALT_TITLE'] = '��������������� ��������';
$JLMS_LANGUAGE['_JLMS_IMPORT_ALT_TITLE'] = '�������������';
$JLMS_LANGUAGE['_JLMS_CANCEL_ALT_TITLE'] = '��������';
$JLMS_LANGUAGE['_JLMS_BACK_ALT_TITLE'] = '�����';
$JLMS_LANGUAGE['_JLMS_COMPLETE_ALT_TITLE'] = '�������� ��� �����������';
$JLMS_LANGUAGE['_JLMS_START_ALT_TITLE'] = '������';
$JLMS_LANGUAGE['_JLMS_NEXT_ALT_TITLE'] = '���������';
$JLMS_LANGUAGE['_JLMS_CONTINUE_ALT_TITLE'] = '����������';
$JLMS_LANGUAGE['_JLMS_PREV_ALT_TITLE'] = '�����';
$JLMS_LANGUAGE['_JLMS_CONTENTS_ALT_TITLE'] = '����������';
$JLMS_LANGUAGE['_JLMS_RESTART_ALT_TITLE'] = '������ ������';
$JLMS_LANGUAGE['_JLMS_EXPORT_ALT_TITLE'] = '��������������';
$JLMS_LANGUAGE['_JLMS_CLEAR_ALT_TITLE'] = '��������';
$JLMS_LANGUAGE['_JLMS_OK_ALT_TITLE'] = 'OK';
$JLMS_LANGUAGE['_JLMS_YES_ALT_TITLE'] = '��';
$JLMS_LANGUAGE['_JLMS_ARCHIVE_ALT_TITLE'] = '�����';

//JS alerts
$JLMS_LANGUAGE['_JLMS_ALERT_SELECT_ITEM'] = '����������, �������� �������.';
$JLMS_LANGUAGE['_JLMS_ALERT_ENTER_PERIOD'] = '����������, ������� ������ �������.';

//header alt's and titles
$JLMS_LANGUAGE['_JLMS_HEAD_USERMAN_STR'] = '���������� ��������������';
$JLMS_LANGUAGE['_JLMS_HEAD_CHAT_STR'] = '���';
$JLMS_LANGUAGE['_JLMS_HEAD_USERGROUP_STR'] = '���������� ��������';
$JLMS_LANGUAGE['_JLMS_HEAD_USER_STR'] = '�������� �������������';
$JLMS_LANGUAGE['_JLMS_HEAD_UNDEFINED_STR'] = '�� ���������';
$JLMS_LANGUAGE['_JLMS_HEAD_LPATH_STR'] = '��������� ��������';
$JLMS_LANGUAGE['_JLMS_HEAD_SCORM_STR'] = '����� SCORM';
$JLMS_LANGUAGE['_JLMS_HEAD_CONF_STR'] = '�����������';
$JLMS_LANGUAGE['_JLMS_HEAD_AGENDA_STR'] = '����������';
$JLMS_LANGUAGE['_JLMS_HEAD_LINK_STR'] = '������';
$JLMS_LANGUAGE['_JLMS_HEAD_DOCS_STR'] = '���������';
$JLMS_LANGUAGE['_JLMS_HEAD_COURSES_STR'] = '�����';
$JLMS_LANGUAGE['_JLMS_HEAD_DROPBOX_STR'] = '����� �������';
$JLMS_LANGUAGE['_JLMS_HEAD_HOMEWORK_STR'] = '�������� �������';
$JLMS_LANGUAGE['_JLMS_HEAD_ATTENDANCE_STR'] = '���������';
$JLMS_LANGUAGE['_JLMS_HEAD_TRACKING_STR'] = '����������';
$JLMS_LANGUAGE['_JLMS_HEAD_GRADEBOOK_STR'] = '������ ������';
$JLMS_LANGUAGE['_JLMS_HEAD_CERTIFICATE_STR'] = '����������';
$JLMS_LANGUAGE['_JLMS_HEAD_MAILBOX_STR'] = '�������� ����';
$JLMS_LANGUAGE['_JLMS_HEAD_QUIZ_STR'] = '�����';
$JLMS_LANGUAGE['_JLMS_HEAD_FORUM_STR'] = '�����';
$JLMS_LANGUAGE['_JLMS_HEAD_SUBSCRIPTION_STR'] = '��������';

$JLMS_LANGUAGE['_JLMS_PATHWAY_HOME'] = '������� �������� ���';
$JLMS_LANGUAGE['_JLMS_PATHWAY_COURSE_HOME'] = '������� �������� �����';

//TOP toolbar text's
$JLMS_LANGUAGE['_JLMS_TOOLBAR_LIBRARY'] = '���������� ����������'; /* 1.0.5 */
$JLMS_LANGUAGE['_JLMS_TOOLBAR_HOME'] = '������� ��������';
$JLMS_LANGUAGE['_JLMS_TOOLBAR_COURSES'] = '�����';
$JLMS_LANGUAGE['_JLMS_TOOLBAR_CEO_PARENT'] = '������ ������������/��������';
$JLMS_LANGUAGE['_JLMS_TOOLBAR_SUBSCRIPTIONS'] = '��������';
$JLMS_LANGUAGE['_JLMS_TOOLBAR_CHAT'] = '���';
$JLMS_LANGUAGE['_JLMS_TOOLBAR_FORUM'] = '�����';
$JLMS_LANGUAGE['_JLMS_TOOLBAR_CONF'] = '�����������';
$JLMS_LANGUAGE['_JLMS_TOOLBAR_QUIZZES'] = '�����';
$JLMS_LANGUAGE['_JLMS_TOOLBAR_DOCS'] = '���������';
$JLMS_LANGUAGE['_JLMS_TOOLBAR_LINKS'] = '������';
$JLMS_LANGUAGE['_JLMS_TOOLBAR_DROP'] = '����� �������';
$JLMS_LANGUAGE['_JLMS_TOOLBAR_AGENDA'] = '����������';
$JLMS_LANGUAGE['_JLMS_TOOLBAR_LPATH'] = '��������� ��������';
$JLMS_LANGUAGE['_JLMS_TOOLBAR_ATTEND'] = '���������';
$JLMS_LANGUAGE['_JLMS_TOOLBAR_TRACK'] = '����������';
$JLMS_LANGUAGE['_JLMS_TOOLBAR_USERS'] = '���������� �������������� � ��������';
$JLMS_LANGUAGE['_JLMS_TOOLBAR_CONFIG'] = '������������';
$JLMS_LANGUAGE['_JLMS_TOOLBAR_TO_STU'] = '������������� � ����� ��������';
$JLMS_LANGUAGE['_JLMS_TOOLBAR_TO_TEACH'] = '������������� � ����� �������������';
$JLMS_LANGUAGE['_JLMS_TOOLBAR_BACK'] = '�����';
$JLMS_LANGUAGE['_JLMS_TOOLBAR_HOMEWORK'] = '�������� �������';
$JLMS_LANGUAGE['_JLMS_TOOLBAR_GRADEBOOK'] = '������ ������';
$JLMS_LANGUAGE['_JLMS_TOOLBAR_MAILBOX'] = '�������� ����';
$JLMS_LANGUAGE['_JLMS_TOOLBAR_HELP'] = '������';
$JLMS_LANGUAGE['_JLMS_TOOLBAR_USER_OPTIONS'] = '����� ������������';
$JLMS_LANGUAGE['_JLMS_TOOLBAR_ONLINE'] = '������������� ������';

$JLMS_LANGUAGE['_JLMS_CURRENT_COURSE'] = '������� ����';
//user info
$JLMS_LANGUAGE['_JLMS_UI_USERNAME'] = '������������:';
$JLMS_LANGUAGE['_JLMS_UI_NAME'] = '���:';
$JLMS_LANGUAGE['_JLMS_UI_EMAIL'] = 'E-mail:';
$JLMS_LANGUAGE['_JLMS_UI_GROUP'] = '������:';
//
$JLMS_LANGUAGE['_JLMS_STATUS_PUB'] = '�������';
$JLMS_LANGUAGE['_JLMS_STATUS_PUB2'] = '������� � ���������';
$JLMS_LANGUAGE['_JLMS_STATUS_UNPUB'] = '�����';
$JLMS_LANGUAGE['_JLMS_SET_PUB'] = '���������';
$JLMS_LANGUAGE['_JLMS_SET_UNPUB'] = '������';
$JLMS_LANGUAGE['_JLMS_STATUS_EXPIRED'] = '��������������';

//redirect (user-switch)
$JLMS_LANGUAGE['_JLMS_CLICK_HERE_TO_REDIRECT'] = '������� ����, ���� ������� �� ������������ ��� �������������.';
$JLMS_LANGUAGE['_JLMS_REDIRECTING'] = '�������������';

$JLMS_LANGUAGE['_JLMS_RESULT_OK'] = 'OK';
$JLMS_LANGUAGE['_JLMS_RESULT_FAIL'] = '������';

//mainpage
$JLMS_LANGUAGE['_JLMS_HOME_COURSES_TITLE'] = '��� �����';
$JLMS_LANGUAGE['_JLMS_HOME_DROPBOX_TITLE'] = '����� ������� ( <font color="green">{X}</font> / {Y} )'; //{X} - ����� �������� ��������; {Y} - ��� �������� ��������; {X} � {Y} - �������������� ��������� (� ��� '<font>' �����)
$JLMS_LANGUAGE['_JLMS_HOME_DROPBOX_NO_ITEMS'] = '��� ����� ���������';
$JLMS_LANGUAGE['_JLMS_HOME_COURSES_NO_ITEMS'] = '� ��� ��� ����� ������';
$JLMS_LANGUAGE['_JLMS_HOME_AGENDA_NO_ITEMS'] = '�� ������� ��� ����������';
$JLMS_LANGUAGE['_JLMS_HOME_HOMEWORK_NO_ITEMS'] = '�� ������� ��� �������� �������';
$JLMS_LANGUAGE['_JLMS_HOME_AGENDA_TITLE'] = '����������';
$JLMS_LANGUAGE['_JLMS_HOME_HOMEWORK_TITLE'] = '�������� �������';
$JLMS_LANGUAGE['_JLMS_HOME_COURSES_LIST'] = '������ ���� ������';
$JLMS_LANGUAGE['_JLMS_HOME_COURSES_LIST_HREF'] = '������� ����, ����� ����������� ��� �����';
$JLMS_LANGUAGE['_JLMS_HOME_AUTHOR'] = '�����:';
$JLMS_LANGUAGE['_JLMS_HOME_COURSE_DETAIL'] = '������ �����';

$JLMS_LANGUAGE['_JLMS_AGREEMENT'] = '�������� � ������ ��������� � ���������������� ��������.';
$JLMS_LANGUAGE['_JLMS_CONGRATULATIONS'] = "�������, ��� ���������� �� ���� #COURSENAME#.";
$JLMS_LANGUAGE['JLMS_FORUM_NOT_MEMBER'] = "� ��������� ����� �� �� ��������� ���������� ������. ����������, ������� ���� ������ � ��� ������������ ��� ���.";

//some image titles and alts
$JLMS_LANGUAGE['_JLMS_T_A_VIEW_ZIP_PACK'] = "����������� ZIP-�����";
$JLMS_LANGUAGE['_JLMS_T_A_VIEW_CONTENT'] = "����������� ����� ����������";
$JLMS_LANGUAGE['_JLMS_T_A_DOWNLOAD'] = "������� ��������";
$JLMS_LANGUAGE['_JLMS_T_A_VIEW_LINK'] = "������� �� ������";

//
$JLMS_LANGUAGE['_JLMS_MESSAGE_SHORT_COURSE_INFO'] = "�� �� ��������� � ������ ����. ���� �� ��������� ��������� ����� ����� � ������ ��� ���������, ��������, ��� ��������, ��� ���� ������� ������ ���� �������������� (��� �������) ���� ���� ����� �������� �����.";

//some error messages
$JLMS_LANGUAGE['_JLMS_EM_SELECT_FILE'] = "����������, �������� ���� ��� ��������.";
$JLMS_LANGUAGE['_JLMS_EM_BAD_FILENAME'] = "��� ����� ����� ��������� ������ ����� � ����� ��� ��������.";
$JLMS_LANGUAGE['_JLMS_EM_BAD_FILEEXT'] = "������ ���������� ����� �� ��������������.";
$JLMS_LANGUAGE['_JLMS_EM_BAD_SCORM'] = "����������� ���� �� �������� SCORM-�������";
$JLMS_LANGUAGE['_JLMS_EM_SCORM_FOLDER'] = "��������� ������ �� ����� �������� ����� ��� SCORM-������.";
$JLMS_LANGUAGE['_JLMS_EM_READ_PACKAGE_ERROR'] = "��������� ������ �� ����� ���������� ������ ������.";
$JLMS_LANGUAGE['_JLMS_EM_UPLOAD_SIZE_ERROR'] = "���� �� ����� ��������. ��������� � ��������������� �����, ����� ��������� PHP ��������� 'upload_max_filesize'.";
$JLMS_LANGUAGE['_JLMS_EM_DISABLED_OPTION'] ='��� ����� �� ������������ ��� ������� �����.';

// 'User option' floating window text's:
$JLMS_LANGUAGE['_JLMS_UO_SELECT_LANGUAGE'] = "�������� ����:";
$JLMS_LANGUAGE['_JLMS_UO_SWITCH_TYPE'] = "����������� ���� ���:";

$JLMS_LANGUAGE['_JLMS_ONLINE_USERS'] = "������������� ������:";
$JLMS_LANGUAGE['_JLMS_OU_USER'] = "��� ������������";
$JLMS_LANGUAGE['_JLMS_OU_LAST_ACTIVE'] = "��������� ��������";

// spec registration
$JLMS_LANGUAGE['_JLMS_COURSES_SPEC_REG'] = "�������������� ��������������� ����������";
$JLMS_LANGUAGE['_JLMS_COURSES_SPEC_REG_QUEST'] = "��������������� ������:";
$JLMS_LANGUAGE['_JLMS_COURSES_SPEC_REG_CONFIRM'] = "����������, ��������� ����������, ������� �� ������. �������, ��� ����� ����, ��� ���������� ����� ���������, �� ������ ����� ��������.";
?>