<?php
/**
* /languages/german/main.lang.php
* (c) JoomaLMS eLearning Software http://www.joomlalms.com/
**/

// no direct access
defined( '_JEXEC' ) or die( 'eingeschr&auml;nkter Zugang' );

// See this link for details: http://en.wikipedia.org/wiki/Locale
$JLMS_LANGUAGE['_JLMS_LOCALE'] = 'de_DE';
//For windows server see locale here: http://msdn.microsoft.com/library/default.asp?url=/library/en-us/vclib/html/_crt_language_strings.asp
$JLMS_LANGUAGE['_JLMS_LOCALE_WIN'] = 'german';

/* 1.0.7 */
$JLMS_LANGUAGE['_JLMS_FILTER_ALL_SUBGROUPS'] = 'All subgroups';

$JLMS_LANGUAGE['_JLMS_STATUS_PUBLISHED_AND_HIDDEN'] = 'Published but hidden';
$JLMS_LANGUAGE['_JLMS_STATUS_CONFIGURED_PREREQUISITES'] = 'Has pre-requisites configured';
$JLMS_LANGUAGE['_JLMS_STATUS_UPCOMING'] = 'Upcoming resource';
$JLMS_LANGUAGE['_JLMS_WILL_BE_RELEASED_IN'] = 'To be released for student in';
$JLMS_LANGUAGE['_JLMS_RELEASED_AFTER_ENROLLMENT'] = 'after enrollment';
$JLMS_LANGUAGE['_JLMS_RELEASED_IN_DAYS'] = 'day(s)';
$JLMS_LANGUAGE['_JLMS_RELEASED_IN_HOURS'] = 'hour(s)';
$JLMS_LANGUAGE['_JLMS_RELEASED_IN_MINUTES'] = 'minute(s)';

$JLMS_LANGUAGE['_JLMS_LIMIT_RESOURCE_TO_GROUPS'] = 'Limit to specific groups of learners:';
$JLMS_LANGUAGE['_JLMS_LIMIT_RESOURCE_USERGROUPS'] = 'UserGroups:';

$JLMS_LANGUAGE['_JLMS_OUTDOCS_JS_CONFIRM_DELETE'] = 'Are you sure you want to delete selected documents?';

$JLMS_LANGUAGE['_JLMS_PRINT_RESULTS'] = 'Print results';
$JLMS_LANGUAGE['_JLMS_FULL_VIEW_BUTTON'] = '[Full View]';

$JLMS_LANGUAGE['_JLMS_USERS_NAME'] = 'Name';
$JLMS_LANGUAGE['_JLMS_USERS_EMAIL'] = 'Email';
$JLMS_LANGUAGE['_JLMS_USER_INFORMATION'] = 'User Information';
$JLMS_LANGUAGE['_JLMS_REPORTS_SCORM'] = "SCORM Report";

$JLMS_LANGUAGE['_JLMS_FILE_UPLOAD'] = 'Upload a file';
$JLMS_LANGUAGE['_JLMS_ATTACHED_FILE'] = 'Attached file';

$JLMS_LANGUAGE['_JLMS_COMMENTS'] = 'Comments:';

$JLMS_LANGUAGE['_JLMS_RECURRENT_PAYMENT_THEN'] = "Then ";
$JLMS_LANGUAGE['_JLMS_RECURRENT_PAYMENT_THE_NEXT'] = " the next ";
$JLMS_LANGUAGE['_JLMS_RECURRENT_PAYMENT_FREE'] = "Free ";


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
$JLMS_LANGUAGE['_JLMS_RECURRENT_PAYMENT_FIRST_DAY'] = "{free}{a1} {cur} for the first day";
$JLMS_LANGUAGE['_JLMS_RECURRENT_PAYMENT_FIRST_DAYS'] = "{free}{a1} {cur} for the first {p1} days";
$JLMS_LANGUAGE['_JLMS_RECURRENT_PAYMENT_NEXT_DAY'] = "{then}{free}{a2} {cur} for{next}day";
$JLMS_LANGUAGE['_JLMS_RECURRENT_PAYMENT_NEXT_DAYS'] = "{then}{free}{a2} {cur} for{next}{p2} days";
$JLMS_LANGUAGE['_JLMS_RECURRENT_PAYMENT_ONE_DAY'] = "{then}{a3} {cur} for one day";
$JLMS_LANGUAGE['_JLMS_RECURRENT_PAYMENT_MORE_DAYS'] = "{then}{a3} {cur} for {p3} days";
$JLMS_LANGUAGE['_JLMS_RECURRENT_PAYMENT_FOREACH'] = "{then}{a3} {cur} for each day";
$JLMS_LANGUAGE['_JLMS_RECURRENT_PAYMENT_FOREACH_DAYS'] = "{then}{a3} {cur} for each {p3} days";
$JLMS_LANGUAGE['_JLMS_RECURRENT_PAYMENT_INSTALLMENTS'] = "{then}{a3} {cur} for each day, for {srt} installments";
$JLMS_LANGUAGE['_JLMS_RECURRENT_PAYMENT_INSTALLMENTS_DAYS'] = "{then}{a3} {cur} for each {p3} days, for {srt} installments";
$JLMS_LANGUAGE['_JLMS_RECURRENT_MORE_THAN_ONE'] = "More than one recurrent payment";

$JLMS_LANGUAGE['_JLMS_IS_TIME_RELATED'] = 'Time released:';
$JLMS_LANGUAGE['_JLMS_DAYS'] = 'Days:';
$JLMS_LANGUAGE['_JLMS_HOURS'] = 'Hours:';
$JLMS_LANGUAGE['_JLMS_MINUTES'] = 'Minutes:';
$JLMS_LANGUAGE['_JLMS_ENROLL_TIME'] = 'Enroll Time';

/*1.0.6*/

$JLMS_LANGUAGE['_JLMS_STATUS_FUTURE_COURSE'] = 'zuk&uuml;nftiger Kurs';

$JLMS_LANGUAGE['_JLMS_NO_SHARED_ITEMS'] = 'Es gibt keine gemeinsamen Ressourcen in der Datei-Bibliothek';
$JLMS_LANGUAGE['_JLMS_RESOURCES_ARE_IN_USE'] = 'Die folgenden Ressourcen werden in den Kursen verwendet. Sie werden aus den Kursen gel&ouml;scht.<br /> M&ouml;chten Sie fortfahren?';

$JLMS_LANGUAGE['_JLMS_ORDER'] = 'ordnen';
$JLMS_LANGUAGE['_JLMS_REORDER'] = 'neu ordnen';
$JLMS_LANGUAGE['_JLMS_SAVEORDER'] = 'Reihenfolge speichern';

$JLMS_LANGUAGE['_JLMS_NOT_AUTH_SESSION_EXPIRED'] = "Sie haben keinen Zugang zu dieser Seite oder Ihre Session ist abgelaufen. Bitte schlie&szlig;en Sie das Fenster und loggen Sie sich ein.";

$JLMS_LANGUAGE['_JLMS_CUSTOM_PERMISSIONS'] = 'spezielle Rechte';
$JLMS_LANGUAGE['_JLMS_CPERM_ROLE_NAME'] = 'Rollenname';
$JLMS_LANGUAGE['_JLMS_CPERM_VIEW'] = 'anzeigen';
$JLMS_LANGUAGE['_JLMS_CPERM_VIEW_ALL'] = 'Unver&ouml;ffentlichte anzeigen';
$JLMS_LANGUAGE['_JLMS_CPERM_ORDER'] = 'ordnen';
$JLMS_LANGUAGE['_JLMS_CPERM_PUBLISH'] = 'ver&ouml;ffentlichen';
$JLMS_LANGUAGE['_JLMS_CPERM_MANAGE'] = 'verwalten';
//statistics (quizzes)
$JLMS_LANGUAGE['_JLMS_GRAPH_STATISTICS_CORRECT'] = 'richtige Antwort';
$JLMS_LANGUAGE['_JLMS_GRAPH_STATISTICS_INCORRECT'] = 'falsche Antwort';

//courses list new messages
$JLMS_LANGUAGE['_JLMS_SH_DESCRIPTION'] = 'Kurzbeschreibung:';
$JLMS_LANGUAGE['_JLMS_COURSES_ST_DATE'] = 'Start Datum';
$JLMS_LANGUAGE['_JLMS_COURSES_END_DATE'] = 'End Datum';
$JLMS_LANGUAGE['_JLMS_COURSES_FEETYPE'] = 'Geb&uuml;hrenart';

$JLMS_LANGUAGE['_JLMS_SB_QUIZ_SELECT_QCATS'] = ' - Fragenkategorie w&auml;hlen - ';
$JLMS_LANGUAGE['_JLMS_SELECT_CATEGORY'] = 'Bitte w&auml;hlen Sie eine Kategorie';

$JLMS_LANGUAGE['_JLMS_TOOLBAR_VIEW_ALL_NOTICES'] = 'Alle Notizen anzeigen';

//global reports
$JLMS_LANGUAGE['_JLMS_REPORTS_MODULE'] = "Berichte";
$JLMS_LANGUAGE['_JLMS_REPORTS_SELECT_DATE'] = "Bitte w&auml;hlen Sie richtiges Datum";
$JLMS_LANGUAGE['_JLMS_REPORTS_ACCESS'] = "Zugangsbericht";
$JLMS_LANGUAGE['_JLMS_REPORTS_CONCLUSION'] = "Abschlussbericht";
$JLMS_LANGUAGE['_JLMS_REPORTS_USER'] = "Nutzer-Status-Bericht";
$JLMS_LANGUAGE['_JLMS_REPORTS_CONCLUSION_ROW'] = "Ergebnis";
$JLMS_LANGUAGE['_JLMS_REPORTS_TOTAL_ROW'] = "gesamt";
$JLMS_LANGUAGE['_JLMS_REPORTS_ACCESSED_TIMES'] = "Zugangszeiten:";
$JLMS_LANGUAGE['_JLMS_REPORTS_SELECT_USER'] = "Bitte w&auml;hlen Sie einen Nutzer aus der Liste";

//new toolbar items
$JLMS_LANGUAGE['_JLMS_TOOLBAR_GQP_PARENT'] = 'Globaler Fragenpool';

//new image titles
$JLMS_LANGUAGE['_JLMS_RESUME_ALT_TITLE'] = "Alt. Titel";

/* 1.0.5 fixes */
$JLMS_LANGUAGE['_JLMS_MY_CART'] = 'Mein Warenkorb';

$JLMS_LANGUAGE['_JLMS_MOVEUP'] = 'hoch bewegen';
$JLMS_LANGUAGE['_JLMS_MOVEDOWN'] = 'runter bewegen';

$JLMS_LANGUAGE['_JLMS_COURSES_SEC_CAT'] = 'Secund&auml;rkategorien:';

$JLMS_LANGUAGE['_JLMS_LP_RESOURSE_ISUNAV'] = 'Ressource nicht verf&uuml;gbar';
/* 1.0.5 */
$JLMS_LANGUAGE['_JLMS_JS_COOKIES_REQUIRES'] = 'Hierf&uuml;r m&uuml;ssen Javascript und Cookies aktiviert sein.';
$JLMS_LANGUAGE['_JLMS_IFRAMES_REQUIRES'] = 'Diese Option arbeitet nicht richtig, weil Ihr Browser leider keine Inline Frames unterst&uuml;tzt.';
$JLMS_LANGUAGE['_JLMS_JS_FLASH_REQUIRES'] = 'Um dieses Tool zu nutzen, muss JavaScript aktiviert sein und ben&ouml;tigen Sie die neueste Version des Flash Players.';
$JLMS_LANGUAGE['_JLMS_ADD_ITEM'] = 'Item hinzuf&uuml;gen';
$JLMS_LANGUAGE['_JLMS_DEL_ITEM'] = 'Item entfernen';
$JLMS_LANGUAGE['_JLMS_DETAILS'] = 'Details';
$JLMS_LANGUAGE['_JLMS_DOWNLOAD'] = 'Download';

$JLMS_LANGUAGE['_JLMS_FILE_ATTACHED'] = 'Datei ist bereits angef&uuml;gt';
$JLMS_LANGUAGE['_JLMS_FILE_NOT_ATTACHED'] = 'Datei ist nicht angef&uuml;gt';
/* other texts */
$JLMS_LANGUAGE['_JLMS_ADVANCED'] = 'erweitert'; // i.e. 'Advanced' settings tab
$JLMS_LANGUAGE['_JLMS_SB_SELECT_IMAGE'] = ' - ein Bild w&auml;hlen - '; // for image selectboxes
$JLMS_LANGUAGE['_JLMS_SB_SELECT_CATEGORY'] = ' - Kategorie w&auml;hlen - ';
$JLMS_LANGUAGE['_JLMS_DISABLE_OPTION'] = '- diese Option abschalten -';

$JLMS_LANGUAGE['_JLMS_ATT_FILTER_ALL_GROUPS'] = 'Alle Gruppen';//moved from attendance.lang.php

$JLMS_LANGUAGE['_JLMS_PUBLISH_ELEMENT'] = 'Element ver&ouml;ffentlichen';
$JLMS_LANGUAGE['_JLMS_UNPUBLISH_ELEMENT'] = 'Element deaktivieren';
$JLMS_LANGUAGE['_JLMS_DELETE_ELEMENT'] = 'Element l&ouml;schen';
$JLMS_LANGUAGE['_JLMS_ADD_ELEMENTS'] = 'Elemente hinzuf&uuml;gen';

$JLMS_LANGUAGE['_JLMS_JANUARY'] = "Januar";
$JLMS_LANGUAGE['_JLMS_FEBRUARY'] = "Februar";
$JLMS_LANGUAGE['_JLMS_MARCH'] = "M&auml;rz";
$JLMS_LANGUAGE['_JLMS_APRIL'] = "April";
$JLMS_LANGUAGE['_JLMS_MAY'] = "Mai";
$JLMS_LANGUAGE['_JLMS_JUNE'] = "Juni";
$JLMS_LANGUAGE['_JLMS_JULY'] = "Juli";
$JLMS_LANGUAGE['_JLMS_AUGUST'] = "August";
$JLMS_LANGUAGE['_JLMS_SEPTEMBER'] = "September";
$JLMS_LANGUAGE['_JLMS_OCTOBER'] = "Oktober";
$JLMS_LANGUAGE['_JLMS_NOVEMBER'] = "November";
$JLMS_LANGUAGE['_JLMS_DECEMBER'] = "Dezember";

$JLMS_LANGUAGE['_JLMS_MONDAY'] = "Montag";
$JLMS_LANGUAGE['_JLMS_TUESDAY'] = "Dienstag";
$JLMS_LANGUAGE['_JLMS_WEDNESDAY'] = "Mittwoch";
$JLMS_LANGUAGE['_JLMS_THURSDAY'] = "Donnerstag";
$JLMS_LANGUAGE['_JLMS_FRIDAY'] = "Freitag";
$JLMS_LANGUAGE['_JLMS_SATURDAY'] = "Samstag";
$JLMS_LANGUAGE['_JLMS_SANDAY'] = "Sonntag";

$JLMS_LANGUAGE['_JLMS_MON'] = "Mo";
$JLMS_LANGUAGE['_JLMS_TUE'] = "Di";
$JLMS_LANGUAGE['_JLMS_WED'] = "Mi";
$JLMS_LANGUAGE['_JLMS_THU'] = "Do";
$JLMS_LANGUAGE['_JLMS_FRI'] = "Fr";
$JLMS_LANGUAGE['_JLMS_SAT'] = "Sa";
$JLMS_LANGUAGE['_JLMS_SAN'] = "So";

/* 1.0.4 */
/* User info */
$JLMS_LANGUAGE['_JLMS_FIELD_REQUIRED'] = '* - Pflichtfeld';
$JLMS_LANGUAGE['_JLMS_USER_FIRSTNAME'] = 'Vorname:';
$JLMS_LANGUAGE['_JLMS_USER_LASTTNAME'] = 'Nachname:';
$JLMS_LANGUAGE['_JLMS_USER_ADDRESS'] = 'Stra&szlig;e:';
$JLMS_LANGUAGE['_JLMS_USER_CITY'] = 'Ort:';
$JLMS_LANGUAGE['_JLMS_USER_STATE'] = 'Bundesland/Kanton:';
$JLMS_LANGUAGE['_JLMS_USER_POSTAL_CODE'] = 'Postleitzahl:';
$JLMS_LANGUAGE['_JLMS_USER_COUNTRY'] = 'Land:';
$JLMS_LANGUAGE['_JLMS_USER_PHONE'] = 'Telefon:';
$JLMS_LANGUAGE['_JLMS_USER_EMAIL'] = 'Email Adresse:';
/* Check input */
$JLMS_LANGUAGE['_JLMS_ENTER_FIRST_NAME'] 	= 'Bitte geben Sie Ihren Vornamen ein.';
$JLMS_LANGUAGE['_JLMS_ENTER_LAST_NAME'] 	= 'Bitte geben Sie Ihren Nachnahmen ein.';
$JLMS_LANGUAGE['_JLMS_ENTER_ADDRESS'] 		= 'Bitte geben Sie Ihre Adresse ein.';
$JLMS_LANGUAGE['_JLMS_ENTER_CITY'] 			= 'Bitte geben Sie Ihren Wohnort ein.';
$JLMS_LANGUAGE['_JLMS_ENTER_POSTAL_CODE'] 	= 'Bitte geben Sie Ihre Postleitzahl ein.';
$JLMS_LANGUAGE['_JLMS_ENTER_COUNTRY'] 		= 'Bitte geben Sie das Land ein.';
$JLMS_LANGUAGE['_JLMS_ENTER_PHONE'] 		= 'Bitte geben Sie Ihre Telefonnummer ein.';
$JLMS_LANGUAGE['_JLMS_ENTER_EMAIL'] 		= 'Bitte g&uuml;ltige eMail Adresse eingeben.';
$JLMS_LANGUAGE['_JLMS_ENTER_CARD_NUMBER']	= 'Bitte geben Sie Ihre Kartennummer ein.';
$JLMS_LANGUAGE['_JLMS_ENTER_CARD_CODE']		= 'Bitte geben Sie den Sicherheitscode Ihrer Karte ein.';

$JLMS_LANGUAGE['_JLMS_NO_ITEMS_HERE'] = 'Es gibt keine Items in diesem Bereich.';
/* page navigation */
/* for 'Yes' and 'No' you should use _JLMS_NO_ALT_TITLE and _JLMS_YES_ALT_TITLE */
$JLMS_LANGUAGE['_JLMS_PN_RESULTS'] = 'Ergebnisse';
$JLMS_LANGUAGE['_JLMS_PN_OF_TOTAL'] = 'von insgesamt';
$JLMS_LANGUAGE['_JLMS_PN_NO_RESULTS'] = 'keine Ergebnisse';
$JLMS_LANGUAGE['_JLMS_PN_FIRST_PAGE'] = 'Anfang';
$JLMS_LANGUAGE['_JLMS_PN_PREV_PAGE'] = 'vor';
$JLMS_LANGUAGE['_JLMS_PN_END_PAGE'] = 'Ende';
$JLMS_LANGUAGE['_JLMS_PN_NEXT_PAGE'] = 'weiter';
$JLMS_LANGUAGE['_JLMS_PN_DISPLAY_NUM'] = 'Anzeigen #';
$JLMS_LANGUAGE['_JLMS_SEPARATOR'] = 'Trennzeichen';
$JLMS_LANGUAGE['_JLMS_LEFT'] = 'links';
$JLMS_LANGUAGE['_JLMS_CENTER'] = 'zentriert';
$JLMS_LANGUAGE['_JLMS_RIGHT'] = 'rechts';

$JLMS_LANGUAGE['_JLMS_TXT_TOP'] = 'oben';
$JLMS_LANGUAGE['_JLMS_TXT_BACK'] = 'zur&uuml;ck';

$JLMS_LANGUAGE['_JLMS_PLEASE_LOGIN'] = "Sind Sie registriert? Bitte einloggen.";
$JLMS_LANGUAGE['_JLMS_PLEASE_REGISTER'] = "Neu hier? Bitte registrieren.";
$JLMS_LANGUAGE['_JLMS_SHOW_LOGIN'] = "Loginformular anzeigen.";
$JLMS_LANGUAGE['_JLMS_SHOW_REGISTRATION'] = "Das Login-Formular anzeigen.";
$JLMS_LANGUAGE['_JLMS_REGISTRATION_DISABLED'] = "Registrierung ist deaktiviert.";
$JLMS_LANGUAGE['_JLMS_REGISTRATION_COMPLETE'] = "Registrierung ist abgeschlossen! Danke f&uuml; Ihre Anmeldung.";
$JLMS_LANGUAGE['_JLMS_LOGIN_SUCCESS'] = "Sie sind erfolgreich eingeloggt.";
$JLMS_LANGUAGE['_JLMS_REGISTRATION_ACTIVATION'] = 'Ein Zugangskonto wurde erstellt und ein Aktivierungslink an Ihre e-Mail Adresse gesandt.';
$JLMS_LANGUAGE['_JLMS_LOGIN_INCORRECT'] = 'Nutznamen oder Passwort falsch. Bitte versuchen Sie es noch einmal.';

$JLMS_LANGUAGE['_JLMS_SB_COURSE_FOLDER'] = ' - Kursordner - ';
$JLMS_LANGUAGE['_JLMS_SB_NO_CERTIFICATE'] = ' - Kein Zertifikat - ';
$JLMS_LANGUAGE['_JLMS_SB_FIRST_ITEM'] = ' - erstes Item - ';
$JLMS_LANGUAGE['_JLMS_SB_LAST_ITEM'] = ' - letztes Item - ';
$JLMS_LANGUAGE['_JLMS_SB_QUIZ_SELECT_QTYPE'] = ' - w&auml;hlen Sie die Fragenart - ';
$JLMS_LANGUAGE['_JLMS_SB_SELECT_USER'] = ' - w&auml;hlen Sie Nutzer - ';
$JLMS_LANGUAGE['_JLMS_SB_SELECT_QUIZ'] = ' - w&auml;hlen Sie Test - ';
$JLMS_LANGUAGE['_JLMS_SB_FILTER_NONE'] = 'keine';


$JLMS_LANGUAGE['_JLMS_SB_ALL_USERS'] = ' - alle Nutzer - ';

/* 1.0.2 */
$JLMS_LANGUAGE['_JLMS_SHOW_IN_GRADEBOOK_OPTION'] = 'Im Notenbuch anzeigen';

/* 1.0.1 */
$JLMS_LANGUAGE['_JLMS_NO_ALT_TITLE'] = 'nein';
$JLMS_LANGUAGE['_JLMS_SETTINGS_ALT_TITLE'] = 'Einstellungen';

/* 1.0.0 */
//roles
$JLMS_LANGUAGE['_JLMS_ROLE_TEACHER'] = 'Lehrer';
$JLMS_LANGUAGE['_JLMS_ROLE_STU'] = 'Student';
$JLMS_LANGUAGE['_JLMS_ROLE_CEO'] = 'Chef / Eltern';
$JLMS_LANGUAGE['_JLMS_GROUP'] = 'Gruppe';

$JLMS_LANGUAGE['_JLMS_ENTER_NAME'] = 'Namen eingeben:';
$JLMS_LANGUAGE['_JLMS_CHOOSE_FILE'] = 'Datei ausw&auml;hlen:';
$JLMS_LANGUAGE['_JLMS_SHORT_DESCRIPTION'] = 'Kurzbeschreibung:';
$JLMS_LANGUAGE['_JLMS_DESCRIPTION'] = 'Beschreibung:';
$JLMS_LANGUAGE['_JLMS_COMMENT'] = 'Kommentar:';
$JLMS_LANGUAGE['_JLMS_TEACHER_COMMENT'] = 'Kommentar des Lehrers:';
$JLMS_LANGUAGE['_JLMS_PLACE_IN'] = 'Platzieren in:';
$JLMS_LANGUAGE['_JLMS_ORDERING'] = 'Reihenfolge:';
$JLMS_LANGUAGE['_JLMS_FILTER'] = 'Filter:';
$JLMS_LANGUAGE['_JLMS_LINK_LOCATION'] = 'Ort:';
$JLMS_LANGUAGE['_JLMS_PUBLISHING'] = 'Ver&ouml;ffentlichung:';
$JLMS_LANGUAGE['_JLMS_DATE'] = 'Datum:';
$JLMS_LANGUAGE['_JLMS_PERIOD'] = 'Zeitspanne ausw&auml;hlen:';
$JLMS_LANGUAGE['_JLMS_START_DATE'] = 'Anfangsdatum:';
$JLMS_LANGUAGE['_JLMS_END_DATE'] = 'Enddatum:';
$JLMS_LANGUAGE['_JLMS_DATES_PUBLISH'] = 'Datum der Ver&ouml;ffentlichung:';
$JLMS_LANGUAGE['_JLMS_EDIT'] = 'editieren';
$JLMS_LANGUAGE['_JLMS_DELETE'] = 'l&ouml;schen';
$JLMS_LANGUAGE['_JLMS_VIEW_DETAILS'] = 'Details anzeigen';
$JLMS_LANGUAGE['_JLMS_TOTAL'] = '<b>GESAMT:</b>';
$JLMS_LANGUAGE['_JLMS_ENROLL'] = 'einschreiben';
// please ...
$JLMS_LANGUAGE['_JLMS_SELECT_FILE'] = 'Bitte Datei ausw&auml;hlen';
$JLMS_LANGUAGE['_JLMS_PL_ENTER_NAME'] = 'Bitte Namen eingeben';
//alt & titles for img.buttons
$JLMS_LANGUAGE['_JLMS_SAVE_ALT_TITLE'] = 'speichern';
$JLMS_LANGUAGE['_JLMS_SEND_ALT_TITLE'] = 'senden';
$JLMS_LANGUAGE['_JLMS_PREVIEW_ALT_TITLE'] = 'ansehen';
$JLMS_LANGUAGE['_JLMS_IMPORT_ALT_TITLE'] = 'importieren';
$JLMS_LANGUAGE['_JLMS_CANCEL_ALT_TITLE'] = 'abbrechen';
$JLMS_LANGUAGE['_JLMS_BACK_ALT_TITLE'] = 'zur&uuml;ck';
$JLMS_LANGUAGE['_JLMS_COMPLETE_ALT_TITLE'] = 'vollst&auml;ndig';
$JLMS_LANGUAGE['_JLMS_START_ALT_TITLE'] = 'Start';
$JLMS_LANGUAGE['_JLMS_NEXT_ALT_TITLE'] = 'weiter';
$JLMS_LANGUAGE['_JLMS_CONTINUE_ALT_TITLE'] = 'weiter';
$JLMS_LANGUAGE['_JLMS_PREV_ALT_TITLE'] = 'zur&uuml;ck';
$JLMS_LANGUAGE['_JLMS_CONTENTS_ALT_TITLE'] = 'Inhalt';
$JLMS_LANGUAGE['_JLMS_RESTART_ALT_TITLE'] = 'Neustart';
$JLMS_LANGUAGE['_JLMS_EXPORT_ALT_TITLE'] = 'exportieren';
$JLMS_LANGUAGE['_JLMS_CLEAR_ALT_TITLE'] = 'Clear';

$JLMS_LANGUAGE['_JLMS_OK_ALT_TITLE'] = 'OK';
$JLMS_LANGUAGE['_JLMS_YES_ALT_TITLE'] = 'ja';
$JLMS_LANGUAGE['_JLMS_ARCHIVE_ALT_TITLE'] = 'Archiv';
//JS alerts
$JLMS_LANGUAGE['_JLMS_ALERT_SELECT_ITEM'] = 'Bitte ein Item ausw&auml;hlen';
$JLMS_LANGUAGE['_JLMS_ALERT_ENTER_PERIOD'] = 'Bitte Zeitspanne eingeben';

//header alt's and titles
$JLMS_LANGUAGE['_JLMS_HEAD_USERMAN_STR'] = 'Nutzermanagement';
$JLMS_LANGUAGE['_JLMS_HEAD_CHAT_STR'] = 'Chat';
$JLMS_LANGUAGE['_JLMS_HEAD_USERGROUP_STR'] = 'Gruppenmanagement';
$JLMS_LANGUAGE['_JLMS_HEAD_USER_STR'] = 'Nutzer eintragen';
$JLMS_LANGUAGE['_JLMS_HEAD_UNDEFINED_STR'] = 'Undefiniert';
$JLMS_LANGUAGE['_JLMS_HEAD_LPATH_STR'] = 'Lernpfad';
$JLMS_LANGUAGE['_JLMS_HEAD_SCORM_STR'] = 'SCORM Paket';
$JLMS_LANGUAGE['_JLMS_HEAD_CONF_STR'] = 'Konferenz';
$JLMS_LANGUAGE['_JLMS_HEAD_AGENDA_STR'] = 'Termine';
$JLMS_LANGUAGE['_JLMS_HEAD_LINK_STR'] = 'Links';
$JLMS_LANGUAGE['_JLMS_HEAD_DOCS_STR'] = 'Dokumente';
$JLMS_LANGUAGE['_JLMS_HEAD_COURSES_STR'] = 'Kurse';
$JLMS_LANGUAGE['_JLMS_HEAD_DROPBOX_STR'] = 'DropBox';
$JLMS_LANGUAGE['_JLMS_HEAD_HOMEWORK_STR'] = 'Hausarbeit';
$JLMS_LANGUAGE['_JLMS_HEAD_ATTENDANCE_STR'] = 'Anwesenheit';
$JLMS_LANGUAGE['_JLMS_HEAD_TRACKING_STR'] = 'Tracking';
$JLMS_LANGUAGE['_JLMS_HEAD_GRADEBOOK_STR'] = 'Notenbuch';
$JLMS_LANGUAGE['_JLMS_HEAD_CERTIFICATE_STR'] = 'Zertifikat';
$JLMS_LANGUAGE['_JLMS_HEAD_MAILBOX_STR'] = 'Mailbox';
$JLMS_LANGUAGE['_JLMS_HEAD_QUIZ_STR'] = 'Tests';
$JLMS_LANGUAGE['_JLMS_HEAD_FORUM_STR'] = 'Forum';
$JLMS_LANGUAGE['_JLMS_HEAD_SUBSCRIPTION_STR'] = 'Einschreibung';

$JLMS_LANGUAGE['_JLMS_PATHWAY_HOME'] = 'LMS Home';
$JLMS_LANGUAGE['_JLMS_PATHWAY_COURSE_HOME'] = 'Startseite des Kurses';

//TOP toolbar text's
$JLMS_LANGUAGE['_JLMS_TOOLBAR_LIBRARY'] = 'File Library'; /* 1.0.5 */
$JLMS_LANGUAGE['_JLMS_TOOLBAR_HOME'] = 'Startseite';
$JLMS_LANGUAGE['_JLMS_TOOLBAR_COURSES'] = 'Kurse';
$JLMS_LANGUAGE['_JLMS_TOOLBAR_CEO_PARENT'] = 'CEO/Parent access';
$JLMS_LANGUAGE['_JLMS_TOOLBAR_SUBSCRIPTIONS'] = 'Subscriptions';
$JLMS_LANGUAGE['_JLMS_TOOLBAR_CHAT'] = 'Chat';
$JLMS_LANGUAGE['_JLMS_TOOLBAR_FORUM'] = 'Forum';
$JLMS_LANGUAGE['_JLMS_TOOLBAR_CONF'] = 'Konferenz';
$JLMS_LANGUAGE['_JLMS_TOOLBAR_QUIZZES'] = 'Tests';
$JLMS_LANGUAGE['_JLMS_TOOLBAR_DOCS'] = 'Dokumente';
$JLMS_LANGUAGE['_JLMS_TOOLBAR_LINKS'] = 'Links';
$JLMS_LANGUAGE['_JLMS_TOOLBAR_DROP'] = 'DropBox';
$JLMS_LANGUAGE['_JLMS_TOOLBAR_AGENDA'] = 'Termine';
$JLMS_LANGUAGE['_JLMS_TOOLBAR_LPATH'] = 'Lernpfad';
$JLMS_LANGUAGE['_JLMS_TOOLBAR_ATTEND'] = 'Anwesenheit';
$JLMS_LANGUAGE['_JLMS_TOOLBAR_TRACK'] = 'Tracking';
$JLMS_LANGUAGE['_JLMS_TOOLBAR_USERS'] = 'Nutzer &amp; Gruppenmanagement';
$JLMS_LANGUAGE['_JLMS_TOOLBAR_CONFIG'] = 'Konfiguration';
$JLMS_LANGUAGE['_JLMS_TOOLBAR_TO_STU'] = 'Studentenansicht';
$JLMS_LANGUAGE['_JLMS_TOOLBAR_TO_TEACH'] = 'Lehreransicht';
$JLMS_LANGUAGE['_JLMS_TOOLBAR_BACK'] = 'zur&uuml;ck';
$JLMS_LANGUAGE['_JLMS_TOOLBAR_HOMEWORK'] = 'Hausarbeit';
$JLMS_LANGUAGE['_JLMS_TOOLBAR_GRADEBOOK'] = 'Notenbuch';
$JLMS_LANGUAGE['_JLMS_TOOLBAR_MAILBOX'] = 'Mailbox';
$JLMS_LANGUAGE['_JLMS_TOOLBAR_HELP'] = 'Hilfe';
$JLMS_LANGUAGE['_JLMS_TOOLBAR_USER_OPTIONS'] = 'Nutzeroptionen';

$JLMS_LANGUAGE['_JLMS_CURRENT_COURSE'] = 'aktuelle Kurse:';
//user info
$JLMS_LANGUAGE['_JLMS_UI_USERNAME'] = 'Nutzername:';
$JLMS_LANGUAGE['_JLMS_UI_NAME'] = 'Name:';
$JLMS_LANGUAGE['_JLMS_UI_EMAIL'] = 'e-Mail:';
$JLMS_LANGUAGE['_JLMS_UI_GROUP'] = 'Gruppe:';
//
$JLMS_LANGUAGE['_JLMS_STATUS_PUB'] = 'ver&ouml;ffentlicht';
$JLMS_LANGUAGE['_JLMS_STATUS_PUB2'] = 'ver&ouml;ffentlicht mit Bedingungen';
$JLMS_LANGUAGE['_JLMS_STATUS_UNPUB'] = 'unver&ouml;ffentlicht';
$JLMS_LANGUAGE['_JLMS_SET_PUB'] = 'ver&ouml;ffentlichen';
$JLMS_LANGUAGE['_JLMS_SET_UNPUB'] = 'Ver&ouml;ffentlichung aufheben';
$JLMS_LANGUAGE['_JLMS_STATUS_EXPIRED'] = 'abgelaufen';

//redirect (user-switch)
$JLMS_LANGUAGE['_JLMS_CLICK_HERE_TO_REDIRECT'] = 'Hier klicken, falls der Browser Sie nicht automatisch umleitet';
$JLMS_LANGUAGE['_JLMS_REDIRECTING'] = 'umleiten';

$JLMS_LANGUAGE['_JLMS_RESULT_OK'] = 'OK';
$JLMS_LANGUAGE['_JLMS_RESULT_FAIL'] = 'gescheitert';

//mainpage
$JLMS_LANGUAGE['_JLMS_HOME_COURSES_TITLE'] = 'meine Kurse';
$JLMS_LANGUAGE['_JLMS_HOME_DROPBOX_TITLE'] = 'DropBox ( <font color="green">{X}</font> / {Y} )'; //{X} - neue eingehende Items; {Y} - all incoming items; {X} and {Y} - optional parameters (and tag '<font>' too)
$JLMS_LANGUAGE['_JLMS_HOME_DROPBOX_NO_ITEMS'] = 'keine neuen Items in der digitalen Dropbox';
$JLMS_LANGUAGE['_JLMS_HOME_COURSES_NO_ITEMS'] = 'Sie haben keine Kurse';
$JLMS_LANGUAGE['_JLMS_HOME_AGENDA_NO_ITEMS'] = 'heute keine Termine';
$JLMS_LANGUAGE['_JLMS_HOME_HOMEWORK_NO_ITEMS'] = 'heute keine Hausarbeiten';
$JLMS_LANGUAGE['_JLMS_HOME_AGENDA_TITLE'] = 'Termine';
$JLMS_LANGUAGE['_JLMS_HOME_HOMEWORK_TITLE'] = 'Hausarbeit';
$JLMS_LANGUAGE['_JLMS_HOME_COURSES_LIST'] = 'Alle Kurslisten';
$JLMS_LANGUAGE['_JLMS_HOME_COURSES_LIST_HREF'] = 'Klicken Sie hier, um alle Kurse zu sehen';
$JLMS_LANGUAGE['_JLMS_HOME_AUTHOR'] = 'Autor:';
$JLMS_LANGUAGE['_JLMS_HOME_COURSE_DETAIL'] = 'Kursdetail';

$JLMS_LANGUAGE['_JLMS_AGREEMENT'] = 'Sie m&uuml;ssen unseren AGB zustimmen.';
$JLMS_LANGUAGE['_JLMS_CONGRATULATIONS'] = "Gl&uuml;ckwunsch! Sie sind erfolgreich im Kurs #COURSENAME# eingeschrieben. Wir w&uuml;nschen Ihnen Erfolg f&uuml;r diese Fortbildung!";
$JLMS_LANGUAGE['JLMS_FORUM_NOT_MEMBER'] = "Sie sind im Augenblick nicht Mitglied des Forums. Bitte loggen Sie sich erneut ein.";

//some image titles and alts
$JLMS_LANGUAGE['_JLMS_T_A_VIEW_ZIP_PACK'] = "ZIP Paket anzeigen";
$JLMS_LANGUAGE['_JLMS_T_A_VIEW_CONTENT'] = "Inhaltsitem anzeigen";
$JLMS_LANGUAGE['_JLMS_T_A_DOWNLOAD'] = "Dokument herunterladen";
$JLMS_LANGUAGE['_JLMS_T_A_VIEW_LINK'] = "Link anzeigen";

//
$JLMS_LANGUAGE['_JLMS_MESSAGE_SHORT_COURSE_INFO'] = "Sie sind nicht in diesen Kurs eingeschrieben. Wenn Sie ein Student dieses Kurses sind und Sie diese Mitteilung sehen, kann es sein, dass Ihr Konto gesperrt oder gel&ouml;scht wurde oder Ihre Einschreibung ist abgelaufen.";

//some error messages
$JLMS_LANGUAGE['_JLMS_EM_SELECT_FILE'] = "Bitte Datei zum Hochladen ausw&auml;hlen";
$JLMS_LANGUAGE['_JLMS_EM_BAD_FILENAME'] = "Der Dateiname darf nur alphanumerische Zeichen (keine Leerzeichen) enthalten.";
$JLMS_LANGUAGE['_JLMS_EM_BAD_FILEEXT'] = "Anh&auml;nge zu dieser Datei werden nicht unterst&uuml;tzt.";
$JLMS_LANGUAGE['_JLMS_EM_BAD_SCORM'] = "Kein SCORM Paket";
$JLMS_LANGUAGE['_JLMS_EM_SCORM_FOLDER'] = "Fehler beim erstellen des Ordners f&uuml;r das SCORM Dokument.";
$JLMS_LANGUAGE['_JLMS_EM_READ_PACKAGE_ERROR'] = "Fehler bei Lesen des Pakets.";
$JLMS_LANGUAGE['_JLMS_EM_UPLOAD_SIZE_ERROR'] = "Fehler beim Hochladen. Kontaktieren Sie den Administrator der Website to check 'upload_max_filesize' PHP setting.";
$JLMS_LANGUAGE['_JLMS_EM_DISABLED_OPTION'] = 'This option is disabled for the course.';

// 'User option' floating window text's:
$JLMS_LANGUAGE['_JLMS_UO_SELECT_LANGUAGE'] = "Sprache ausw&auml;hlen:";
$JLMS_LANGUAGE['_JLMS_UO_SWITCH_TYPE'] = "Kurs anzeigen als:";

$JLMS_LANGUAGE['_JLMS_ONLINE_USERS'] = "Nutzer online:";
$JLMS_LANGUAGE['_JLMS_OU_USER'] = "Nutzername";
$JLMS_LANGUAGE['_JLMS_OU_LAST_ACTIVE'] = "Letzte Aktivit&auml;t";

// spec registration
$JLMS_LANGUAGE['_JLMS_COURSES_SPEC_REG'] = "Zusatzinformationen zur Einschreibung";
$JLMS_LANGUAGE['_JLMS_COURSES_SPEC_REG_QUEST'] = "Frage zur Einschreibung";
$JLMS_LANGUAGE['_JLMS_COURSES_SPEC_REG_CONFIRM'] = "Bitte best&auml;tigen Sie die eingegebene Information. Angaben k&ouml;nnen nicht nachtr&auml;glich ge&auml;ndert werden!";

?>