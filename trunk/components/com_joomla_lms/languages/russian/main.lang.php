<?php
/**
* /languages/russian/main.lang.php
* (c) JoomaLMS eLearning Software http://www.joomlalms.com/
**/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

$JLMS_LANGUAGE['_JLMS_REPORTING_USERGROUP'] = 'Группа Пользователя';
$JLMS_LANGUAGE['_JLMS_REPORTING_ACCESSED_TIMES'] = 'Accessed Times';
$JLMS_LANGUAGE['_JLMS_REPORTING_TOTAL'] = 'ВСЕГО';

// See this link for details: http://en.wikipedia.org/wiki/Locale
$JLMS_LANGUAGE['_JLMS_LOCALE'] = 'ru_RU';
//For windows server see locale here: http://msdn.microsoft.com/library/default.asp?url=/library/en-us/vclib/html/_crt_language_strings.asp
$JLMS_LANGUAGE['_JLMS_LOCALE_WIN'] = 'russian';

$JLMS_LANGUAGE['_JLMS_CLOSE_ALT_TITLE'] = 'Закрыть';

$JLMS_LANGUAGE['_JLMS_USER_OPTIONS_NOTES'] = 'Примечания';
$JLMS_LANGUAGE['_JLMS_USER_OPTIONS_NOTES_ADD'] = 'Добавить';
$JLMS_LANGUAGE['_JLMS_USER_OPTIONS_NOTES_EDIT'] = 'Редактировать';
$JLMS_LANGUAGE['_JLMS_USER_OPTIONS_NOTES_NOTICE'] = 'примечание';

$JLMS_LANGUAGE['_JLMS_QUIZ_STATUS_COMPLETED'] = 'Завершенный';
$JLMS_LANGUAGE['_JLMS_QUIZ_STATUS_INCOMPLETE'] = 'Незавершенный';
$JLMS_LANGUAGE['_JLMS_QUIZ_STATUS_PASSED'] = 'Принят';
$JLMS_LANGUAGE['_JLMS_QUIZ_STATUS_FAILED'] = 'Неудачно';

/* 1.1.0 */
$JLMS_LANGUAGE['_JLMS_HOME_LATEST_FORUM_POSTS_TITLE'] = 'Последние сообщения форума';
$JLMS_LANGUAGE['_JLMS_HOME_LATEST_FORUM_POSTS_NO_ITEMS'] = 'Нет новых сообщений на форумах';

$JLMS_LANGUAGE['_JLMS_HOME_CERTIFICATES_SN'] = 'S/N';
$JLMS_LANGUAGE['_JLMS_HOME_CERTIFICATES_TITLE'] = 'Сертификаты';
$JLMS_LANGUAGE['_JLMS_HOME_CERTIFICATES_NO_ITEMS'] = 'У вас нет сертификатов еще';
$JLMS_LANGUAGE['_JLMS_HOME_MAILBOX_TITLE'] = 'Почтовый ящик ( X / Y )';
$JLMS_LANGUAGE['_JLMS_HOME_MAILBOX_NO_ITEMS'] = 'Нет новых сообщений';
$JLMS_LANGUAGE['_JLMS_HOME_MAILBOX_FROM'] = 'От';

$JLMS_LANGUAGE['_JLMS_FILTER_LPATHS'] = ' Все программы обучения';

/* 1.0.7 */
$JLMS_LANGUAGE['_JLMS_EXPORT_TO'] = 'Экспортировать в';
$JLMS_LANGUAGE['_JLMS_REQUESTED_RESOURCE_WILL_BE_RELEASED'] = 'Требуемый ресурс будет доступен через';

$JLMS_LANGUAGE['_JLMS_FILTER_ALL_SUBGROUPS'] = 'Все подгруппы';

$JLMS_LANGUAGE['_JLMS_STATUS_PUBLISHED_AND_HIDDEN'] = 'Допущен, но скрыт';
$JLMS_LANGUAGE['_JLMS_STATUS_CONFIGURED_PREREQUISITES'] = 'Предварительные условия настроены';
$JLMS_LANGUAGE['_JLMS_STATUS_UPCOMING'] = 'Ресурсы, которые скоро будут доступны';
$JLMS_LANGUAGE['_JLMS_WILL_BE_RELEASED_IN'] = 'Будет доступен для ученика через';
$JLMS_LANGUAGE['_JLMS_RELEASED_AFTER_ENROLLMENT'] = 'после зачисления';
$JLMS_LANGUAGE['_JLMS_RELEASED_IN_DAYS'] = 'дней';
$JLMS_LANGUAGE['_JLMS_RELEASED_IN_HOURS'] = 'часов';
$JLMS_LANGUAGE['_JLMS_RELEASED_IN_MINUTES'] = 'минут';

$JLMS_LANGUAGE['_JLMS_LIMIT_RESOURCE_TO_GROUPS'] = 'Доступ ограничен определенными группами учеников:';
$JLMS_LANGUAGE['_JLMS_LIMIT_RESOURCE_USERGROUPS'] = 'Группы пользователей:';

$JLMS_LANGUAGE['_JLMS_OUTDOCS_JS_CONFIRM_DELETE'] = 'Вы уверены, что хотите удалить выбранные документы?';

$JLMS_LANGUAGE['_JLMS_PRINT_RESULTS'] = 'Напечатать результаты';
$JLMS_LANGUAGE['_JLMS_FULL_VIEW_BUTTON'] = '[Полное представление]';

$JLMS_LANGUAGE['_JLMS_USERS_NAME'] = 'Имя';
$JLMS_LANGUAGE['_JLMS_USERS_EMAIL'] = 'E-mail';
$JLMS_LANGUAGE['_JLMS_USER_INFORMATION'] = 'Информация о пользователе';
$JLMS_LANGUAGE['_JLMS_REPORTS_SCORM'] = "Отчет SCORM";

$JLMS_LANGUAGE['_JLMS_FILE_UPLOAD'] = 'Загрузить файл';
$JLMS_LANGUAGE['_JLMS_ATTACHED_FILE'] = 'Присоединенный файл';

$JLMS_LANGUAGE['_JLMS_COMMENTS'] = 'Комментарии:';

$JLMS_LANGUAGE['_JLMS_RECURRENT_PAYMENT_THEN'] = "Затем ";
$JLMS_LANGUAGE['_JLMS_RECURRENT_PAYMENT_THE_NEXT'] = " следующий ";
$JLMS_LANGUAGE['_JLMS_RECURRENT_PAYMENT_FREE'] = "Бесплатно ";


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
$JLMS_LANGUAGE['_JLMS_RECURRENT_PAYMENT_FIRST_DAY'] = "{free}{a1} {cur} за первый день";
$JLMS_LANGUAGE['_JLMS_RECURRENT_PAYMENT_FIRST_DAYS'] = "{free}{a1} {cur} за первые {p1} дней";
$JLMS_LANGUAGE['_JLMS_RECURRENT_PAYMENT_NEXT_DAY'] = "{then}{free}{a2} {cur} за {next} день";
$JLMS_LANGUAGE['_JLMS_RECURRENT_PAYMENT_NEXT_DAYS'] = "{then}{free}{a2} {cur} за {next}{p2} дней";
$JLMS_LANGUAGE['_JLMS_RECURRENT_PAYMENT_ONE_DAY'] = "{then}{a3} {cur} за один день";
$JLMS_LANGUAGE['_JLMS_RECURRENT_PAYMENT_MORE_DAYS'] = "{then}{a3} {cur} за {p3} дней";
$JLMS_LANGUAGE['_JLMS_RECURRENT_PAYMENT_FOREACH'] = "{then}{a3} {cur} за каждый день";
$JLMS_LANGUAGE['_JLMS_RECURRENT_PAYMENT_FOREACH_DAYS'] = "{then}{a3} {cur} за каждые {p3} дней";
$JLMS_LANGUAGE['_JLMS_RECURRENT_PAYMENT_INSTALLMENTS'] = "{then}{a3} {cur} за каждый день, в {srt} этапов";
$JLMS_LANGUAGE['_JLMS_RECURRENT_PAYMENT_INSTALLMENTS_DAYS'] = "{then}{a3} {cur} за каждые {p3} дней, в {srt} этапов";
$JLMS_LANGUAGE['_JLMS_RECURRENT_MORE_THAN_ONE'] = "Более одного платежа в рассрочку";

$JLMS_LANGUAGE['_JLMS_IS_TIME_RELATED'] = 'Допустить с задержкой:';
$JLMS_LANGUAGE['_JLMS_DAYS'] = 'Дней:';
$JLMS_LANGUAGE['_JLMS_HOURS'] = 'Часов:';
$JLMS_LANGUAGE['_JLMS_MINUTES'] = 'Минут:';
$JLMS_LANGUAGE['_JLMS_ENROLL_TIME'] = 'Время зачисления';
/*1.0.6*/

$JLMS_LANGUAGE['_JLMS_STATUS_FUTURE_COURSE'] = 'Будущий курс';

$JLMS_LANGUAGE['_JLMS_NO_SHARED_ITEMS'] = 'В библиотеке нет ресурсов, доступных для добавления в курсы.';
$JLMS_LANGUAGE['_JLMS_RESOURCES_ARE_IN_USE'] = 'Следующие ресурсы используются в курсах. Они будут удалены из курсов.<br /> Продолжить?';

$JLMS_LANGUAGE['_JLMS_ORDER'] = 'Порядок';
$JLMS_LANGUAGE['_JLMS_REORDER'] = 'Изменить порядок';
$JLMS_LANGUAGE['_JLMS_SAVEORDER'] = 'Сохранить порядок';

$JLMS_LANGUAGE['_JLMS_NOT_AUTH_SESSION_EXPIRED'] = "У вас нет доступа к странице или ваша сессия авторизации истекла. Пожалуйста, закройте это окно и авторизуйтесь на сайте.";

$JLMS_LANGUAGE['_JLMS_CUSTOM_PERMISSIONS'] = 'Права доступа';
$JLMS_LANGUAGE['_JLMS_CPERM_ROLE_NAME'] = 'Название роли';
$JLMS_LANGUAGE['_JLMS_CPERM_VIEW'] = 'Просмотр';
$JLMS_LANGUAGE['_JLMS_CPERM_VIEW_ALL'] = 'Просмотр скрытых документов';
$JLMS_LANGUAGE['_JLMS_CPERM_ORDER'] = 'Изменение порядка';
$JLMS_LANGUAGE['_JLMS_CPERM_PUBLISH'] = 'Допуск';
$JLMS_LANGUAGE['_JLMS_CPERM_MANAGE'] = 'Управление';
//statistics (quizzes)
$JLMS_LANGUAGE['_JLMS_GRAPH_STATISTICS_CORRECT'] = 'Правильный ответ';
$JLMS_LANGUAGE['_JLMS_GRAPH_STATISTICS_INCORRECT'] = 'Неправильный ответ';

//courses list new messages
$JLMS_LANGUAGE['_JLMS_SH_DESCRIPTION'] = 'Краткое описание:';
$JLMS_LANGUAGE['_JLMS_COURSES_ST_DATE'] = 'Дата начала';
$JLMS_LANGUAGE['_JLMS_COURSES_END_DATE'] = 'Дата окончания';
$JLMS_LANGUAGE['_JLMS_COURSES_FEETYPE'] = 'Тип курса';

$JLMS_LANGUAGE['_JLMS_SB_QUIZ_SELECT_QCATS'] = ' - Выберите категорию вопроса - ';
$JLMS_LANGUAGE['_JLMS_SELECT_CATEGORY'] = 'Пожалуйста, выберите категорию';

$JLMS_LANGUAGE['_JLMS_TOOLBAR_VIEW_ALL_NOTICES'] = 'Просмотреть все заметки';

//global reports
$JLMS_LANGUAGE['_JLMS_REPORTS_MODULE'] = "Отчеты";
$JLMS_LANGUAGE['_JLMS_REPORTS_SELECT_DATE'] = "Пожалуйста, выберите дату";
$JLMS_LANGUAGE['_JLMS_REPORTS_ACCESS'] = "Отчет о посещениях";
$JLMS_LANGUAGE['_JLMS_REPORTS_CONCLUSION'] = "Отчет о завершенности курсов";
$JLMS_LANGUAGE['_JLMS_REPORTS_USER'] = "Отчет об успеваемости";
$JLMS_LANGUAGE['_JLMS_REPORTS_CONCLUSION_ROW'] = "Курс пройден";
$JLMS_LANGUAGE['_JLMS_REPORTS_TOTAL_ROW'] = "Всего";
$JLMS_LANGUAGE['_JLMS_REPORTS_ACCESSED_TIMES'] = "Количество посещений:";
$JLMS_LANGUAGE['_JLMS_REPORTS_SELECT_USER'] = "Выберите пользователя из списка";

//new toolbar items
$JLMS_LANGUAGE['_JLMS_TOOLBAR_GQP_PARENT'] = 'Общая база вопросов';

//new image titles
$JLMS_LANGUAGE['_JLMS_RESUME_ALT_TITLE'] = "Продолжить";




/* 1.0.5 fixes */
$JLMS_LANGUAGE['_JLMS_MY_CART'] = 'Моя корзина';

$JLMS_LANGUAGE['_JLMS_MOVEUP'] = 'Переместить вверх';
$JLMS_LANGUAGE['_JLMS_MOVEDOWN'] = 'Переместить вниз';

$JLMS_LANGUAGE['_JLMS_COURSES_SEC_CAT'] = 'Дополнительные категории:';

$JLMS_LANGUAGE['_JLMS_LP_RESOURSE_ISUNAV'] = 'Ресурс недоступен';
/* 1.0.5 */
$JLMS_LANGUAGE['_JLMS_JS_COOKIES_REQUIRES'] = 'Для использования этой опции необходимо активировать Javascript и cookies.';
$JLMS_LANGUAGE['_JLMS_IFRAMES_REQUIRES'] = 'Для корректной работы данной опции необходимо, чтобы Ваш браузер поддерживал Inline Frames.';
$JLMS_LANGUAGE['_JLMS_JS_FLASH_REQUIRES'] = 'Чтобы использовать этот инструмент, активируйте JavaScript и установите последнюю версию Flash Player.';
$JLMS_LANGUAGE['_JLMS_ADD_ITEM'] = 'Добавить элемент';
$JLMS_LANGUAGE['_JLMS_DEL_ITEM'] = 'Удалить элемент';
$JLMS_LANGUAGE['_JLMS_DETAILS'] = 'Подробнее';
$JLMS_LANGUAGE['_JLMS_DOWNLOAD'] = 'Загрузить';

$JLMS_LANGUAGE['_JLMS_FILE_ATTACHED'] = 'Файл прикреплен';
$JLMS_LANGUAGE['_JLMS_FILE_NOT_ATTACHED'] = 'Файл не прикреплен';
/* other texts */
$JLMS_LANGUAGE['_JLMS_ADVANCED'] = 'Расширения'; // i.e. 'Advanced' settings tab
$JLMS_LANGUAGE['_JLMS_SB_SELECT_IMAGE'] = ' - Выберите изображение - '; // for image selectboxes
$JLMS_LANGUAGE['_JLMS_SB_SELECT_CATEGORY'] = ' - Выберите категорию - ';
$JLMS_LANGUAGE['_JLMS_DISABLE_OPTION'] = '- деактивировать эту опцию -';

$JLMS_LANGUAGE['_JLMS_ATT_FILTER_ALL_GROUPS'] = 'Все группы';//moved from attendance.lang.php

$JLMS_LANGUAGE['_JLMS_PUBLISH_ELEMENT'] = 'Допустить элемент';
$JLMS_LANGUAGE['_JLMS_UNPUBLISH_ELEMENT'] = 'Скрыть элемент';
$JLMS_LANGUAGE['_JLMS_DELETE_ELEMENT'] = 'Удалить элемент';
$JLMS_LANGUAGE['_JLMS_ADD_ELEMENTS'] = 'Добавить элемент';

$JLMS_LANGUAGE['_JLMS_JANUARY'] = "Январь";
$JLMS_LANGUAGE['_JLMS_FEBRUARY'] = "Февраль";
$JLMS_LANGUAGE['_JLMS_MARCH'] = "Март";
$JLMS_LANGUAGE['_JLMS_APRIL'] = "Апрель";
$JLMS_LANGUAGE['_JLMS_MAY'] = "Май";
$JLMS_LANGUAGE['_JLMS_JUNE'] = "Июнь";
$JLMS_LANGUAGE['_JLMS_JULY'] = "Июль";
$JLMS_LANGUAGE['_JLMS_AUGUST'] = "Август";
$JLMS_LANGUAGE['_JLMS_SEPTEMBER'] = "Сентябрь";
$JLMS_LANGUAGE['_JLMS_OCTOBER'] = "Октябрь";
$JLMS_LANGUAGE['_JLMS_NOVEMBER'] = "Ноябрь";
$JLMS_LANGUAGE['_JLMS_DECEMBER'] = "Декабрь";

$JLMS_LANGUAGE['_JLMS_MONDAY'] = "Понедельник";
$JLMS_LANGUAGE['_JLMS_TUESDAY'] = "Вторник";
$JLMS_LANGUAGE['_JLMS_WEDNESDAY'] = "Среда";
$JLMS_LANGUAGE['_JLMS_THURSDAY'] = "Четверг";
$JLMS_LANGUAGE['_JLMS_FRIDAY'] = "Пятница";
$JLMS_LANGUAGE['_JLMS_SATURDAY'] = "Суббота";
$JLMS_LANGUAGE['_JLMS_SANDAY'] = "Воскресенье";

$JLMS_LANGUAGE['_JLMS_MON'] = "ПН";
$JLMS_LANGUAGE['_JLMS_TUE'] = "ВТ";
$JLMS_LANGUAGE['_JLMS_WED'] = "СР";
$JLMS_LANGUAGE['_JLMS_THU'] = "ЧТ";
$JLMS_LANGUAGE['_JLMS_FRI'] = "ПТ";
$JLMS_LANGUAGE['_JLMS_SAT'] = "СБ";
$JLMS_LANGUAGE['_JLMS_SAN'] = "ВС";

/* 1.0.4 */
/* User info */
$JLMS_LANGUAGE['_JLMS_FIELD_REQUIRED'] = '* - Обязательное для заполнения поле';
$JLMS_LANGUAGE['_JLMS_USER_FIRSTNAME'] = 'Имя:';
$JLMS_LANGUAGE['_JLMS_USER_LASTTNAME'] = 'Фамилия:';
$JLMS_LANGUAGE['_JLMS_USER_ADDRESS'] = 'Адрес:';
$JLMS_LANGUAGE['_JLMS_USER_CITY'] = 'Город:';
$JLMS_LANGUAGE['_JLMS_USER_STATE'] = 'Штат/Область:';
$JLMS_LANGUAGE['_JLMS_USER_POSTAL_CODE'] = 'Почтовый индекс:';
$JLMS_LANGUAGE['_JLMS_USER_COUNTRY'] = 'Страна:';
$JLMS_LANGUAGE['_JLMS_USER_PHONE'] = 'Телефон:';
$JLMS_LANGUAGE['_JLMS_USER_EMAIL'] = 'Адрес электронной почты:';
/* Check input */
$JLMS_LANGUAGE['_JLMS_ENTER_FIRST_NAME'] 	= 'Пожалуйста, введите ваше имя.';
$JLMS_LANGUAGE['_JLMS_ENTER_LAST_NAME'] 	= 'Пожалуйста, введите вашу фамилию.';
$JLMS_LANGUAGE['_JLMS_ENTER_ADDRESS'] 		= 'Пожалуйста, укажите адрес.';
$JLMS_LANGUAGE['_JLMS_ENTER_CITY'] 			= 'Пожалуйста, укажите город.';
$JLMS_LANGUAGE['_JLMS_ENTER_POSTAL_CODE'] 	= 'Пожалуйста, укажите почтовый индекс.';
$JLMS_LANGUAGE['_JLMS_ENTER_COUNTRY'] 		= 'Пожалуйста, укажите страну.';
$JLMS_LANGUAGE['_JLMS_ENTER_PHONE'] 		= 'Пожалуйста, укажите телефон.';
$JLMS_LANGUAGE['_JLMS_ENTER_EMAIL'] 		= 'Пожалуйста, укажите действительный адрес электронной почты.';
$JLMS_LANGUAGE['_JLMS_ENTER_CARD_NUMBER']	= 'Пожалуйста, укажите номер карты.';
$JLMS_LANGUAGE['_JLMS_ENTER_CARD_CODE']		= 'Пожалуйста, укажите код карты.';

$JLMS_LANGUAGE['_JLMS_NO_ITEMS_HERE'] = 'В данном разделе нет доступных элементов.';
/* page navigation */
/* for 'Yes' and 'No' you should use _JLMS_NO_ALT_TITLE and _JLMS_YES_ALT_TITLE */
$JLMS_LANGUAGE['_JLMS_PN_RESULTS'] = 'Всего';
$JLMS_LANGUAGE['_JLMS_PN_OF_TOTAL'] = 'из';
$JLMS_LANGUAGE['_JLMS_PN_NO_RESULTS'] = 'Нет элементов для отображения';
$JLMS_LANGUAGE['_JLMS_PN_FIRST_PAGE'] = 'В начало';
$JLMS_LANGUAGE['_JLMS_PN_PREV_PAGE'] = 'Предыдущие';
$JLMS_LANGUAGE['_JLMS_PN_END_PAGE'] = 'В конец';
$JLMS_LANGUAGE['_JLMS_PN_NEXT_PAGE'] = 'Следующие';
$JLMS_LANGUAGE['_JLMS_PN_DISPLAY_NUM'] = 'Показывать';
$JLMS_LANGUAGE['_JLMS_SEPARATOR'] = 'Разделительный знак';
$JLMS_LANGUAGE['_JLMS_LEFT'] = 'Влево';
$JLMS_LANGUAGE['_JLMS_CENTER'] = 'Посередине';
$JLMS_LANGUAGE['_JLMS_RIGHT'] = 'Вправо';

$JLMS_LANGUAGE['_JLMS_TXT_TOP'] = 'наверх';
$JLMS_LANGUAGE['_JLMS_TXT_BACK'] = 'назад';

$JLMS_LANGUAGE['_JLMS_PLEASE_LOGIN'] = "Если Вы уже зарегистрированы на сайте, пожалуйста, авторизуйтесь на сайте.";
$JLMS_LANGUAGE['_JLMS_PLEASE_REGISTER'] = "Если Вы еще не зарегистрированы на сайте, пожалуйста зарегистрируйтесь.";
$JLMS_LANGUAGE['_JLMS_SHOW_LOGIN'] = "Показать форму авторизации.";
$JLMS_LANGUAGE['_JLMS_SHOW_REGISTRATION'] = "Показать форму регистрации.";
$JLMS_LANGUAGE['_JLMS_REGISTRATION_DISABLED'] = "Регистрация невозможна.";
$JLMS_LANGUAGE['_JLMS_REGISTRATION_COMPLETE'] = "Спасибо, регистрация завершена.";
$JLMS_LANGUAGE['_JLMS_LOGIN_SUCCESS'] = "Вы успешно вошли в систему.";
$JLMS_LANGUAGE['_JLMS_REGISTRATION_ACTIVATION'] = 'Ваша учетная запись создана, ссылка для активации Вашей учетной записи выслана на указанный Вами адрес электронной почты.';
$JLMS_LANGUAGE['_JLMS_LOGIN_INCORRECT'] = 'Неверное имя пользователя или пароль. Пожалуйста, попробуйте еще раз.';

$JLMS_LANGUAGE['_JLMS_SB_COURSE_FOLDER'] = ' - Папка курса - ';
$JLMS_LANGUAGE['_JLMS_SB_NO_CERTIFICATE'] = ' - Нет сертификата - ';
$JLMS_LANGUAGE['_JLMS_SB_FIRST_ITEM'] = ' - Первый элемент - ';
$JLMS_LANGUAGE['_JLMS_SB_LAST_ITEM'] = ' - Последний элемент - ';
$JLMS_LANGUAGE['_JLMS_SB_QUIZ_SELECT_QTYPE'] = ' - Выберите тип вопроса - ';
$JLMS_LANGUAGE['_JLMS_SB_SELECT_USER'] = ' - Выберите пользователя - ';
$JLMS_LANGUAGE['_JLMS_SB_SELECT_QUIZ'] = ' - Выберите тест - ';
$JLMS_LANGUAGE['_JLMS_SB_FILTER_NONE'] = 'Нет';


$JLMS_LANGUAGE['_JLMS_SB_ALL_USERS'] = ' - Все пользователи - ';
/* 1.0.2 */
$JLMS_LANGUAGE['_JLMS_SHOW_IN_GRADEBOOK_OPTION'] = 'Показывать в журнале оценок';

/* 1.0.1 */
$JLMS_LANGUAGE['_JLMS_NO_ALT_TITLE'] = 'Нет';
$JLMS_LANGUAGE['_JLMS_SETTINGS_ALT_TITLE'] = 'Настройки';

/* 1.0.0 */
//roles
$JLMS_LANGUAGE['_JLMS_ROLE_TEACHER'] = 'Преподаватель';
$JLMS_LANGUAGE['_JLMS_ROLE_STU'] = 'Студент';
$JLMS_LANGUAGE['_JLMS_ROLE_CEO'] = 'Руководитель/Родитель';
$JLMS_LANGUAGE['_JLMS_GROUP'] = 'Группа';

$JLMS_LANGUAGE['_JLMS_ENTER_NAME'] = 'Введите название:';
$JLMS_LANGUAGE['_JLMS_CHOOSE_FILE'] = 'Выберите файл';
$JLMS_LANGUAGE['_JLMS_SHORT_DESCRIPTION'] = 'Краткое описание:';
$JLMS_LANGUAGE['_JLMS_DESCRIPTION'] = 'Описание:';
$JLMS_LANGUAGE['_JLMS_COMMENT'] = 'Комментарий';
$JLMS_LANGUAGE['_JLMS_TEACHER_COMMENT'] = 'Комментарий преподавателя:';
$JLMS_LANGUAGE['_JLMS_PLACE_IN'] = 'Разместить в:';
$JLMS_LANGUAGE['_JLMS_ORDERING'] = 'Порядок:';
$JLMS_LANGUAGE['_JLMS_FILTER'] = 'Фильтр:';
$JLMS_LANGUAGE['_JLMS_LINK_LOCATION'] = 'Расположение:';
$JLMS_LANGUAGE['_JLMS_PUBLISHING'] = 'Публикация:';
$JLMS_LANGUAGE['_JLMS_DATE'] = 'Дата:';
$JLMS_LANGUAGE['_JLMS_PERIOD'] = 'Выберите период:';
$JLMS_LANGUAGE['_JLMS_START_DATE'] = 'Дата начала:';
$JLMS_LANGUAGE['_JLMS_END_DATE'] = 'Дата окончания:';
$JLMS_LANGUAGE['_JLMS_DATES_PUBLISH'] = 'Сроки публикации';
$JLMS_LANGUAGE['_JLMS_EDIT'] = 'Изменить';
$JLMS_LANGUAGE['_JLMS_DELETE'] = 'Удалить';
$JLMS_LANGUAGE['_JLMS_VIEW_DETAILS'] = 'Просмотреть детали';
$JLMS_LANGUAGE['_JLMS_TOTAL'] = '<b>ИТОГО:</b>';
$JLMS_LANGUAGE['_JLMS_ENROLL'] = 'Зачислить';
// please ...
$JLMS_LANGUAGE['_JLMS_SELECT_FILE'] = 'Пожалуйста, выберите файл.';
$JLMS_LANGUAGE['_JLMS_PL_ENTER_NAME'] = 'Пожалуйста, введите название.';
//alt & titles for img.buttons
$JLMS_LANGUAGE['_JLMS_SAVE_ALT_TITLE'] = 'Сохранить';
$JLMS_LANGUAGE['_JLMS_SEND_ALT_TITLE'] = 'Отправить';
$JLMS_LANGUAGE['_JLMS_PREVIEW_ALT_TITLE'] = 'Предварительный просмотр';
$JLMS_LANGUAGE['_JLMS_IMPORT_ALT_TITLE'] = 'Импортировать';
$JLMS_LANGUAGE['_JLMS_CANCEL_ALT_TITLE'] = 'Отменить';
$JLMS_LANGUAGE['_JLMS_BACK_ALT_TITLE'] = 'Назад';
$JLMS_LANGUAGE['_JLMS_COMPLETE_ALT_TITLE'] = 'Пометить как выполненное';
$JLMS_LANGUAGE['_JLMS_START_ALT_TITLE'] = 'Начать';
$JLMS_LANGUAGE['_JLMS_NEXT_ALT_TITLE'] = 'Следующий';
$JLMS_LANGUAGE['_JLMS_CONTINUE_ALT_TITLE'] = 'Продолжить';
$JLMS_LANGUAGE['_JLMS_PREV_ALT_TITLE'] = 'Назад';
$JLMS_LANGUAGE['_JLMS_CONTENTS_ALT_TITLE'] = 'Содержание';
$JLMS_LANGUAGE['_JLMS_RESTART_ALT_TITLE'] = 'Начать заново';
$JLMS_LANGUAGE['_JLMS_EXPORT_ALT_TITLE'] = 'Экспортировать';
$JLMS_LANGUAGE['_JLMS_CLEAR_ALT_TITLE'] = 'Очистить';
$JLMS_LANGUAGE['_JLMS_OK_ALT_TITLE'] = 'OK';
$JLMS_LANGUAGE['_JLMS_YES_ALT_TITLE'] = 'Да';
$JLMS_LANGUAGE['_JLMS_ARCHIVE_ALT_TITLE'] = 'Архив';

//JS alerts
$JLMS_LANGUAGE['_JLMS_ALERT_SELECT_ITEM'] = 'Пожалуйста, выберите элемент.';
$JLMS_LANGUAGE['_JLMS_ALERT_ENTER_PERIOD'] = 'Пожалуйста, укажите период времени.';

//header alt's and titles
$JLMS_LANGUAGE['_JLMS_HEAD_USERMAN_STR'] = 'Управление пользователями';
$JLMS_LANGUAGE['_JLMS_HEAD_CHAT_STR'] = 'Чат';
$JLMS_LANGUAGE['_JLMS_HEAD_USERGROUP_STR'] = 'Управление группами';
$JLMS_LANGUAGE['_JLMS_HEAD_USER_STR'] = 'Добавить пользователей';
$JLMS_LANGUAGE['_JLMS_HEAD_UNDEFINED_STR'] = 'Не определен';
$JLMS_LANGUAGE['_JLMS_HEAD_LPATH_STR'] = 'Программа обучения';
$JLMS_LANGUAGE['_JLMS_HEAD_SCORM_STR'] = 'Пакет SCORM';
$JLMS_LANGUAGE['_JLMS_HEAD_CONF_STR'] = 'Конференция';
$JLMS_LANGUAGE['_JLMS_HEAD_AGENDA_STR'] = 'Объявления';
$JLMS_LANGUAGE['_JLMS_HEAD_LINK_STR'] = 'Ссылки';
$JLMS_LANGUAGE['_JLMS_HEAD_DOCS_STR'] = 'Документы';
$JLMS_LANGUAGE['_JLMS_HEAD_COURSES_STR'] = 'Курсы';
$JLMS_LANGUAGE['_JLMS_HEAD_DROPBOX_STR'] = 'Обмен файлами';
$JLMS_LANGUAGE['_JLMS_HEAD_HOMEWORK_STR'] = 'Домашние задания';
$JLMS_LANGUAGE['_JLMS_HEAD_ATTENDANCE_STR'] = 'Посещения';
$JLMS_LANGUAGE['_JLMS_HEAD_TRACKING_STR'] = 'Статистика';
$JLMS_LANGUAGE['_JLMS_HEAD_GRADEBOOK_STR'] = 'Журнал оценок';
$JLMS_LANGUAGE['_JLMS_HEAD_CERTIFICATE_STR'] = 'Сертификат';
$JLMS_LANGUAGE['_JLMS_HEAD_MAILBOX_STR'] = 'Почтовый ящик';
$JLMS_LANGUAGE['_JLMS_HEAD_QUIZ_STR'] = 'Тесты';
$JLMS_LANGUAGE['_JLMS_HEAD_FORUM_STR'] = 'Форум';
$JLMS_LANGUAGE['_JLMS_HEAD_SUBSCRIPTION_STR'] = 'Подписки';

$JLMS_LANGUAGE['_JLMS_PATHWAY_HOME'] = 'Главная страница СДО';
$JLMS_LANGUAGE['_JLMS_PATHWAY_COURSE_HOME'] = 'Главная страница курса';

//TOP toolbar text's
$JLMS_LANGUAGE['_JLMS_TOOLBAR_LIBRARY'] = 'Библиотека документов'; /* 1.0.5 */
$JLMS_LANGUAGE['_JLMS_TOOLBAR_HOME'] = 'Главная страница';
$JLMS_LANGUAGE['_JLMS_TOOLBAR_COURSES'] = 'Курсы';
$JLMS_LANGUAGE['_JLMS_TOOLBAR_CEO_PARENT'] = 'Доступ Руководителя/Родителя';
$JLMS_LANGUAGE['_JLMS_TOOLBAR_SUBSCRIPTIONS'] = 'Подписки';
$JLMS_LANGUAGE['_JLMS_TOOLBAR_CHAT'] = 'Чат';
$JLMS_LANGUAGE['_JLMS_TOOLBAR_FORUM'] = 'Форум';
$JLMS_LANGUAGE['_JLMS_TOOLBAR_CONF'] = 'Конференция';
$JLMS_LANGUAGE['_JLMS_TOOLBAR_QUIZZES'] = 'Тесты';
$JLMS_LANGUAGE['_JLMS_TOOLBAR_DOCS'] = 'Документы';
$JLMS_LANGUAGE['_JLMS_TOOLBAR_LINKS'] = 'Ссылки';
$JLMS_LANGUAGE['_JLMS_TOOLBAR_DROP'] = 'Обмен файлами';
$JLMS_LANGUAGE['_JLMS_TOOLBAR_AGENDA'] = 'Объявления';
$JLMS_LANGUAGE['_JLMS_TOOLBAR_LPATH'] = 'Программа обучения';
$JLMS_LANGUAGE['_JLMS_TOOLBAR_ATTEND'] = 'Посещения';
$JLMS_LANGUAGE['_JLMS_TOOLBAR_TRACK'] = 'Статистика';
$JLMS_LANGUAGE['_JLMS_TOOLBAR_USERS'] = 'Управление пользователями и группами';
$JLMS_LANGUAGE['_JLMS_TOOLBAR_CONFIG'] = 'Конфигурация';
$JLMS_LANGUAGE['_JLMS_TOOLBAR_TO_STU'] = 'Переключиться в режим студента';
$JLMS_LANGUAGE['_JLMS_TOOLBAR_TO_TEACH'] = 'Переключиться в режим преподавателя';
$JLMS_LANGUAGE['_JLMS_TOOLBAR_BACK'] = 'Назад';
$JLMS_LANGUAGE['_JLMS_TOOLBAR_HOMEWORK'] = 'Домашние задания';
$JLMS_LANGUAGE['_JLMS_TOOLBAR_GRADEBOOK'] = 'Журнал оценок';
$JLMS_LANGUAGE['_JLMS_TOOLBAR_MAILBOX'] = 'Почтовый ящик';
$JLMS_LANGUAGE['_JLMS_TOOLBAR_HELP'] = 'Помощь';
$JLMS_LANGUAGE['_JLMS_TOOLBAR_USER_OPTIONS'] = 'Опции пользователя';
$JLMS_LANGUAGE['_JLMS_TOOLBAR_ONLINE'] = 'Пользователей онлайн';

$JLMS_LANGUAGE['_JLMS_CURRENT_COURSE'] = 'Текущий курс';
//user info
$JLMS_LANGUAGE['_JLMS_UI_USERNAME'] = 'Пользователь:';
$JLMS_LANGUAGE['_JLMS_UI_NAME'] = 'Имя:';
$JLMS_LANGUAGE['_JLMS_UI_EMAIL'] = 'E-mail:';
$JLMS_LANGUAGE['_JLMS_UI_GROUP'] = 'Группа:';
//
$JLMS_LANGUAGE['_JLMS_STATUS_PUB'] = 'Допущен';
$JLMS_LANGUAGE['_JLMS_STATUS_PUB2'] = 'Допущен с условиями';
$JLMS_LANGUAGE['_JLMS_STATUS_UNPUB'] = 'Скрыт';
$JLMS_LANGUAGE['_JLMS_SET_PUB'] = 'Допустить';
$JLMS_LANGUAGE['_JLMS_SET_UNPUB'] = 'Скрыть';
$JLMS_LANGUAGE['_JLMS_STATUS_EXPIRED'] = 'Недействителен';

//redirect (user-switch)
$JLMS_LANGUAGE['_JLMS_CLICK_HERE_TO_REDIRECT'] = 'Нажмите сюда, если браузер не переадресует Вас автоматически.';
$JLMS_LANGUAGE['_JLMS_REDIRECTING'] = 'Переадресация';

$JLMS_LANGUAGE['_JLMS_RESULT_OK'] = 'OK';
$JLMS_LANGUAGE['_JLMS_RESULT_FAIL'] = 'ошибка';

//mainpage
$JLMS_LANGUAGE['_JLMS_HOME_COURSES_TITLE'] = 'Мои курсы';
$JLMS_LANGUAGE['_JLMS_HOME_DROPBOX_TITLE'] = 'Обмен файлами ( <font color="green">{X}</font> / {Y} )'; //{X} - новые входящие элементы; {Y} - все входящие элементы; {X} и {Y} - необязательные параметры (и тег '<font>' также)
$JLMS_LANGUAGE['_JLMS_HOME_DROPBOX_NO_ITEMS'] = 'Нет новых элементов';
$JLMS_LANGUAGE['_JLMS_HOME_COURSES_NO_ITEMS'] = 'У Вас нет своих курсов';
$JLMS_LANGUAGE['_JLMS_HOME_AGENDA_NO_ITEMS'] = 'На сегодня нет объявлений';
$JLMS_LANGUAGE['_JLMS_HOME_HOMEWORK_NO_ITEMS'] = 'На сегодня нет домашних заданий';
$JLMS_LANGUAGE['_JLMS_HOME_AGENDA_TITLE'] = 'Объявления';
$JLMS_LANGUAGE['_JLMS_HOME_HOMEWORK_TITLE'] = 'Домашние задания';
$JLMS_LANGUAGE['_JLMS_HOME_COURSES_LIST'] = 'Список всех курсов';
$JLMS_LANGUAGE['_JLMS_HOME_COURSES_LIST_HREF'] = 'Нажмите сюда, чтобы просмотреть все курсы';
$JLMS_LANGUAGE['_JLMS_HOME_AUTHOR'] = 'Автор:';
$JLMS_LANGUAGE['_JLMS_HOME_COURSE_DETAIL'] = 'Детали курса';

$JLMS_LANGUAGE['_JLMS_AGREEMENT'] = 'Согласие с нашими условиями и предоставляемыми услугами.';
$JLMS_LANGUAGE['_JLMS_CONGRATULATIONS'] = "Спасибо, что записались на курс #COURSENAME#.";
$JLMS_LANGUAGE['JLMS_FORUM_NOT_MEMBER'] = "В настоящее время Вы не являетесь участником форума. Пожалуйста, введите свой пароль и имя пользователя еще раз.";

//some image titles and alts
$JLMS_LANGUAGE['_JLMS_T_A_VIEW_ZIP_PACK'] = "Просмотреть ZIP-пакет";
$JLMS_LANGUAGE['_JLMS_T_A_VIEW_CONTENT'] = "Просмотреть пункт содержания";
$JLMS_LANGUAGE['_JLMS_T_A_DOWNLOAD'] = "Скачать документ";
$JLMS_LANGUAGE['_JLMS_T_A_VIEW_LINK'] = "Перейти по ссылке";

//
$JLMS_LANGUAGE['_JLMS_MESSAGE_SHORT_COURSE_INFO'] = "Вы не зачислены в данный курс. Если Вы являетесь студентом этого курса и видите это сообщение, возможно, это означает, что Ваша учетная запись была приостановлена (или удалена) либо срок Вашей подписки истек.";

//some error messages
$JLMS_LANGUAGE['_JLMS_EM_SELECT_FILE'] = "Пожалуйста, выберите файл для загрузки.";
$JLMS_LANGUAGE['_JLMS_EM_BAD_FILENAME'] = "Имя файла может содержать только буквы и цифры без пробелов.";
$JLMS_LANGUAGE['_JLMS_EM_BAD_FILEEXT'] = "Данное расширение файла не поддерживается.";
$JLMS_LANGUAGE['_JLMS_EM_BAD_SCORM'] = "Загруженный файл не является SCORM-пакетом";
$JLMS_LANGUAGE['_JLMS_EM_SCORM_FOLDER'] = "Произошла ошибка во время создания папки для SCORM-пакета.";
$JLMS_LANGUAGE['_JLMS_EM_READ_PACKAGE_ERROR'] = "Произошла ошибка во время считывания данных пакета.";
$JLMS_LANGUAGE['_JLMS_EM_UPLOAD_SIZE_ERROR'] = "Сбой во время загрузки. Свяжитесь с администратором сайта, чтобы проверить PHP настройки 'upload_max_filesize'.";
$JLMS_LANGUAGE['_JLMS_EM_DISABLED_OPTION'] ='Эта опция не активирована для данного курса.';

// 'User option' floating window text's:
$JLMS_LANGUAGE['_JLMS_UO_SELECT_LANGUAGE'] = "Выберите язык:";
$JLMS_LANGUAGE['_JLMS_UO_SWITCH_TYPE'] = "Просмотреть курс как:";

$JLMS_LANGUAGE['_JLMS_ONLINE_USERS'] = "Пользователей онлайн:";
$JLMS_LANGUAGE['_JLMS_OU_USER'] = "Имя пользователя";
$JLMS_LANGUAGE['_JLMS_OU_LAST_ACTIVE'] = "Последние действия";

// spec registration
$JLMS_LANGUAGE['_JLMS_COURSES_SPEC_REG'] = "Дополнительная регистрационная информация";
$JLMS_LANGUAGE['_JLMS_COURSES_SPEC_REG_QUEST'] = "Регистрационный вопрос:";
$JLMS_LANGUAGE['_JLMS_COURSES_SPEC_REG_CONFIRM'] = "Пожалуйста, проверьте информацию, которую Вы внесли. Помните, что после того, как информация будет сохранена, ее нельзя будет изменить.";
?>