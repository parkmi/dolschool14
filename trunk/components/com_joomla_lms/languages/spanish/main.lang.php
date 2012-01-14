<?php
/**
* /languages/spanish/main.lang.php
* (c) JoomaLMS eLearning Software http://www.joomlalms.com/
**/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// See this link for details: http://en.wikipedia.org/wiki/Locale
$JLMS_LANGUAGE['_JLMS_LOCALE'] = 'es_ES';//this one is correct locale name
//For windows server see locale here: http://msdn.microsoft.com/library/default.asp?url=/library/en-us/vclib/html/_crt_language_strings.asp
$JLMS_LANGUAGE['_JLMS_LOCALE_WIN'] = 'spanish';//this one is correct locale name

/* 1.0.7 */
$JLMS_LANGUAGE['_JLMS_FILTER_ALL_SUBGROUPS'] = 'Todos los subgrupos';

$JLMS_LANGUAGE['_JLMS_STATUS_PUBLISHED_AND_HIDDEN'] = 'Publicado pero oculto';
$JLMS_LANGUAGE['_JLMS_STATUS_CONFIGURED_PREREQUISITES'] = 'Hay configurados requisitos previos';
$JLMS_LANGUAGE['_JLMS_STATUS_UPCOMING'] = 'actividad siguiente';
$JLMS_LANGUAGE['_JLMS_WILL_BE_RELEASED_IN'] = 'A realizar por el alumno en';
$JLMS_LANGUAGE['_JLMS_RELEASED_AFTER_ENROLLMENT'] = 'después de matricularse';
$JLMS_LANGUAGE['_JLMS_RELEASED_IN_DAYS'] = 'día/s';
$JLMS_LANGUAGE['_JLMS_RELEASED_IN_HOURS'] = 'hora/s';
$JLMS_LANGUAGE['_JLMS_RELEASED_IN_MINUTES'] = 'minuto/s';

$JLMS_LANGUAGE['_JLMS_LIMIT_RESOURCE_TO_GROUPS'] = 'Limitar para grupos concretos de alumnos:';
$JLMS_LANGUAGE['_JLMS_LIMIT_RESOURCE_USERGROUPS'] = 'Grupo de usuarios:';

$JLMS_LANGUAGE['_JLMS_OUTDOCS_JS_CONFIRM_DELETE'] = '¿Confirmas que deseas borrar los documentos seleccionados?';

$JLMS_LANGUAGE['_JLMS_PRINT_RESULTS'] = 'Imprimir resultados';
$JLMS_LANGUAGE['_JLMS_FULL_VIEW_BUTTON'] = '[Pantalla completa]';

$JLMS_LANGUAGE['_JLMS_USERS_NAME'] = 'Nombre';
$JLMS_LANGUAGE['_JLMS_USERS_EMAIL'] = 'Correo electrónico';
$JLMS_LANGUAGE['_JLMS_USER_INFORMATION'] = 'Información del usuario';
$JLMS_LANGUAGE['_JLMS_REPORTS_SCORM'] = "Informe SCORM";

$JLMS_LANGUAGE['_JLMS_FILE_UPLOAD'] = 'Subir archivo';
$JLMS_LANGUAGE['_JLMS_ATTACHED_FILE'] = 'Adjuntar archivo';

$JLMS_LANGUAGE['_JLMS_COMMENTS'] = 'Comentarios:';

$JLMS_LANGUAGE['_JLMS_RECURRENT_PAYMENT_THEN'] = "Entonces ";
$JLMS_LANGUAGE['_JLMS_RECURRENT_PAYMENT_THE_NEXT'] = " el siguiente ";
$JLMS_LANGUAGE['_JLMS_RECURRENT_PAYMENT_FREE'] = "Gratuito ";


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
$JLMS_LANGUAGE['_JLMS_RECURRENT_PAYMENT_FIRST_DAY'] = "{free}{a1} {cur} durante el primer día";
$JLMS_LANGUAGE['_JLMS_RECURRENT_PAYMENT_FIRST_DAYS'] = "{free}{a1} {cur} durante los primeros {p1} días";
$JLMS_LANGUAGE['_JLMS_RECURRENT_PAYMENT_NEXT_DAY'] = "{then}{free}{a2} {cur} durante{next}día";
$JLMS_LANGUAGE['_JLMS_RECURRENT_PAYMENT_NEXT_DAYS'] = "{then}{free}{a2} {cur} durante{next}{p2} días";
$JLMS_LANGUAGE['_JLMS_RECURRENT_PAYMENT_ONE_DAY'] = "{then}{a3} {cur} por un día";
$JLMS_LANGUAGE['_JLMS_RECURRENT_PAYMENT_MORE_DAYS'] = "{then}{a3} {cur} por {p3} días";
$JLMS_LANGUAGE['_JLMS_RECURRENT_PAYMENT_FOREACH'] = "{then}{a3} {cur} por cada día";
$JLMS_LANGUAGE['_JLMS_RECURRENT_PAYMENT_FOREACH_DAYS'] = "{then}{a3} {cur} por cada {p3} días";
$JLMS_LANGUAGE['_JLMS_RECURRENT_PAYMENT_INSTALLMENTS'] = "{then}{a3} {cur} por cada día, por {srt} instalación/es";
$JLMS_LANGUAGE['_JLMS_RECURRENT_PAYMENT_INSTALLMENTS_DAYS'] = "{then}{a3} {cur} por cada {p3} días, por {srt} instalación/es";
$JLMS_LANGUAGE['_JLMS_RECURRENT_MORE_THAN_ONE'] = "Más de un pago realizado";

$JLMS_LANGUAGE['_JLMS_IS_TIME_RELATED'] = 'Tiempo empleado:';
$JLMS_LANGUAGE['_JLMS_DAYS'] = 'Días:';
$JLMS_LANGUAGE['_JLMS_HOURS'] = 'Horas:';
$JLMS_LANGUAGE['_JLMS_MINUTES'] = 'Minutos:';
$JLMS_LANGUAGE['_JLMS_ENROLL_TIME'] = 'Tiempo realizado';

/*1.0.6*/

$JLMS_LANGUAGE['_JLMS_STATUS_FUTURE_COURSE'] = 'Próximo curso';

$JLMS_LANGUAGE['_JLMS_NO_SHARED_ITEMS'] = 'Noy hay recursos compartidos en la Biblioteca de Documentos';
$JLMS_LANGUAGE['_JLMS_RESOURCES_ARE_IN_USE'] = 'Estos recursos están siendo utilizados en los cursos. Tenga presente que también se borrarán de cada uno de los cursos.<br /> ¿Está seguro de que desea borrar estos recursos?';

$JLMS_LANGUAGE['_JLMS_ORDER'] = 'Ordenar';
$JLMS_LANGUAGE['_JLMS_REORDER'] = 'Reordenar';
$JLMS_LANGUAGE['_JLMS_SAVEORDER'] = 'Guardar';

$JLMS_LANGUAGE['_JLMS_NOT_AUTH_SESSION_EXPIRED'] = "No tiene acceso a esta página porque su sesión ha finalizado. Por favor, cierre esta ventana y vuelva a introducir sus datos de usuario en el campus.";

$JLMS_LANGUAGE['_JLMS_CUSTOM_PERMISSIONS'] = 'Permisos personalizados';
$JLMS_LANGUAGE['_JLMS_CPERM_ROLE_NAME'] = 'Nombre de la función';
$JLMS_LANGUAGE['_JLMS_CPERM_VIEW'] = 'Ver';
$JLMS_LANGUAGE['_JLMS_CPERM_VIEW_ALL'] = 'Ver los no publicados';
$JLMS_LANGUAGE['_JLMS_CPERM_ORDER'] = 'Ordenar';
$JLMS_LANGUAGE['_JLMS_CPERM_PUBLISH'] = 'Publicar';
$JLMS_LANGUAGE['_JLMS_CPERM_MANAGE'] = 'Administración';
//statistics (quizzes)
$JLMS_LANGUAGE['_JLMS_GRAPH_STATISTICS_CORRECT'] = 'respuesta correcta';
$JLMS_LANGUAGE['_JLMS_GRAPH_STATISTICS_INCORRECT'] = 'respuesta incorrecta';

//courses list new messages
$JLMS_LANGUAGE['_JLMS_SH_DESCRIPTION'] = 'Breve descripción:';
$JLMS_LANGUAGE['_JLMS_COURSES_ST_DATE'] = 'Fecha de inicio';
$JLMS_LANGUAGE['_JLMS_COURSES_END_DATE'] = 'Fecha de finalización';
$JLMS_LANGUAGE['_JLMS_COURSES_FEETYPE'] = 'Tipo de matrícula';

$JLMS_LANGUAGE['_JLMS_SB_QUIZ_SELECT_QCATS'] = ' - Seleccione categoría de la pregunta - ';
$JLMS_LANGUAGE['_JLMS_SELECT_CATEGORY'] = 'Seleccione categoría';

$JLMS_LANGUAGE['_JLMS_TOOLBAR_VIEW_ALL_NOTICES'] = 'Ver todas las notas';

//global reports
$JLMS_LANGUAGE['_JLMS_REPORTS_MODULE'] = "Informes";
$JLMS_LANGUAGE['_JLMS_REPORTS_SELECT_DATE'] = "Seleccione fecha correcta";
$JLMS_LANGUAGE['_JLMS_REPORTS_ACCESS'] = "Infome de accesos";
$JLMS_LANGUAGE['_JLMS_REPORTS_CONCLUSION'] = "Informe de conclusiones";
$JLMS_LANGUAGE['_JLMS_REPORTS_USER'] = "Informe del estado del usuario";
$JLMS_LANGUAGE['_JLMS_REPORTS_CONCLUSION_ROW'] = "Conclusión";
$JLMS_LANGUAGE['_JLMS_REPORTS_TOTAL_ROW'] = "Total";
$JLMS_LANGUAGE['_JLMS_REPORTS_ACCESSED_TIMES'] = "Número de accesos al campus:";
$JLMS_LANGUAGE['_JLMS_REPORTS_SELECT_USER'] = "Seleccione un usuario de la lista";

//new toolbar items
$JLMS_LANGUAGE['_JLMS_TOOLBAR_GQP_PARENT'] = 'Biblioteca General de Preguntas';

//new image titles
$JLMS_LANGUAGE['_JLMS_RESUME_ALT_TITLE'] = "Continuar";


/* 1.0.5 fixes */
$JLMS_LANGUAGE['_JLMS_MY_CART'] = 'Mis compras';

$JLMS_LANGUAGE['_JLMS_MOVEUP'] = 'Arriba';
$JLMS_LANGUAGE['_JLMS_MOVEDOWN'] = 'Abajo';

$JLMS_LANGUAGE['_JLMS_COURSES_SEC_CAT'] = 'Categorías secundarias:';

$JLMS_LANGUAGE['_JLMS_LP_RESOURSE_ISUNAV'] = 'Producto no disponible';
/* 1.0.5 */
$JLMS_LANGUAGE['_JLMS_JS_COOKIES_REQUIRES'] = 'Para utilizar esta característica es necesario que active en su navegador de internet la función javascript y acepte las cookies.';
$JLMS_LANGUAGE['_JLMS_IFRAMES_REQUIRES'] = 'Esta opción no funcionará correctamente. Desafortunadamente su navegador de internet no acepta el uso de Frames.';
$JLMS_LANGUAGE['_JLMS_JS_FLASH_REQUIRES'] = 'Para utilizar esta herramienta debe activar la función Javascript en su navegador de internet y también tener instalado en su ordenador la última versión del complemento de reproductor de archivos multimedia Flash.';
$JLMS_LANGUAGE['_JLMS_ADD_ITEM'] = 'Añadir elemento';
$JLMS_LANGUAGE['_JLMS_DEL_ITEM'] = 'Borrar elemento';
$JLMS_LANGUAGE['_JLMS_DETAILS'] = 'Detalles';
$JLMS_LANGUAGE['_JLMS_DOWNLOAD'] = 'Descargar';

$JLMS_LANGUAGE['_JLMS_FILE_ATTACHED'] = 'El archivo se ha adjuntado correctamente';
$JLMS_LANGUAGE['_JLMS_FILE_NOT_ATTACHED'] = 'El archivo no se ha adjuntado';

/* other texts */
$JLMS_LANGUAGE['_JLMS_ADVANCED'] = 'Avanzado'; // i.e. 'Advanced' settings tab
$JLMS_LANGUAGE['_JLMS_SB_SELECT_IMAGE'] = ' - seleccionar imagen - '; // for image selectboxes
$JLMS_LANGUAGE['_JLMS_SB_SELECT_CATEGORY'] = ' - seleccionar categoría - ';
$JLMS_LANGUAGE['_JLMS_DISABLE_OPTION'] = '- desactivar esta opción -';

$JLMS_LANGUAGE['_JLMS_ATT_FILTER_ALL_GROUPS'] = 'Todos los Grupos de usuarios';//moved from attendance.lang.php

$JLMS_LANGUAGE['_JLMS_PUBLISH_ELEMENT'] = 'Publicar el elemento';
$JLMS_LANGUAGE['_JLMS_UNPUBLISH_ELEMENT'] = 'No publicar el elemento';
$JLMS_LANGUAGE['_JLMS_DELETE_ELEMENT'] = 'Borrar elemento';
$JLMS_LANGUAGE['_JLMS_ADD_ELEMENTS'] = 'Añadir elemento';

$JLMS_LANGUAGE['_JLMS_JANUARY'] = "Enero";
$JLMS_LANGUAGE['_JLMS_FEBRUARY'] = "Febrero";
$JLMS_LANGUAGE['_JLMS_MARCH'] = "Marzo";
$JLMS_LANGUAGE['_JLMS_APRIL'] = "Abril";
$JLMS_LANGUAGE['_JLMS_MAY'] = "Mayo";
$JLMS_LANGUAGE['_JLMS_JUNE'] = "Junio";
$JLMS_LANGUAGE['_JLMS_JULY'] = "Julio";
$JLMS_LANGUAGE['_JLMS_AUGUST'] = "Agosto";
$JLMS_LANGUAGE['_JLMS_SEPTEMBER'] = "Septiembre";
$JLMS_LANGUAGE['_JLMS_OCTOBER'] = "Octubre";
$JLMS_LANGUAGE['_JLMS_NOVEMBER'] = "Noviembre";
$JLMS_LANGUAGE['_JLMS_DECEMBER'] = "Deciembre";

$JLMS_LANGUAGE['_JLMS_MONDAY'] = "Lunes";
$JLMS_LANGUAGE['_JLMS_TUESDAY'] = "Martes";
$JLMS_LANGUAGE['_JLMS_WEDNESDAY'] = "Miércoles";
$JLMS_LANGUAGE['_JLMS_THURSDAY'] = "Jueves";
$JLMS_LANGUAGE['_JLMS_FRIDAY'] = "Viernes";
$JLMS_LANGUAGE['_JLMS_SATURDAY'] = "Sábado";
$JLMS_LANGUAGE['_JLMS_SANDAY'] = "Domingo";

$JLMS_LANGUAGE['_JLMS_MON'] = "Lun";
$JLMS_LANGUAGE['_JLMS_TUE'] = "Mar";
$JLMS_LANGUAGE['_JLMS_WED'] = "Mie";
$JLMS_LANGUAGE['_JLMS_THU'] = "Jue";
$JLMS_LANGUAGE['_JLMS_FRI'] = "Vie";
$JLMS_LANGUAGE['_JLMS_SAT'] = "Sab";
$JLMS_LANGUAGE['_JLMS_SAN'] = "Dom";

/* 1.0.4 */
/* User info */
$JLMS_LANGUAGE['_JLMS_FIELD_REQUIRED'] = '* - Obligatorio';
$JLMS_LANGUAGE['_JLMS_USER_FIRSTNAME'] = 'Nombre:';
$JLMS_LANGUAGE['_JLMS_USER_LASTTNAME'] = 'Apellidos:';
$JLMS_LANGUAGE['_JLMS_USER_ADDRESS'] = 'Dirección:';
$JLMS_LANGUAGE['_JLMS_USER_CITY'] = 'Ciudad:';
$JLMS_LANGUAGE['_JLMS_USER_STATE'] = 'Provincia/Comunidad o Estado:';
$JLMS_LANGUAGE['_JLMS_USER_POSTAL_CODE'] = 'Código postal:';
$JLMS_LANGUAGE['_JLMS_USER_COUNTRY'] = 'País:';
$JLMS_LANGUAGE['_JLMS_USER_PHONE'] = 'Teléfono:';
$JLMS_LANGUAGE['_JLMS_USER_EMAIL'] = 'Correo electrónico:';
/* Check input */
$JLMS_LANGUAGE['_JLMS_ENTER_FIRST_NAME'] 	= 'Por favor, escriba su nombre completo.';
$JLMS_LANGUAGE['_JLMS_ENTER_LAST_NAME'] 	= 'Por favor, escriba sus apellidos.';
$JLMS_LANGUAGE['_JLMS_ENTER_ADDRESS'] 		= 'Por favor, escriba su dirección.';
$JLMS_LANGUAGE['_JLMS_ENTER_CITY'] 			= 'Por favor, escriba el nombre de la ciudad donde reside actualmente.';
$JLMS_LANGUAGE['_JLMS_ENTER_POSTAL_CODE'] 	= 'Por favor, escriba el código postal.';
$JLMS_LANGUAGE['_JLMS_ENTER_COUNTRY'] 		= 'Por favor, escriba el país donde reside actualmente.';
$JLMS_LANGUAGE['_JLMS_ENTER_PHONE'] 		= 'Por favor, escriba su número de teléfono con el prefijo internacional.';
$JLMS_LANGUAGE['_JLMS_ENTER_EMAIL'] 		= 'Por favor, escriba correctamente su correo electrónico.';
$JLMS_LANGUAGE['_JLMS_ENTER_CARD_NUMBER']	= 'Por favor, escriba su número de su tarjeta de crédito.';
$JLMS_LANGUAGE['_JLMS_ENTER_CARD_CODE']		= 'Por favor, escriba su código de seguridad de tres cifras de su tarjeta de crédito.';

$JLMS_LANGUAGE['_JLMS_NO_ITEMS_HERE'] = 'De momento esta sección no tiene contenidos.';
/* page navigation */
/* for 'Yes' and 'No' you should use _JLMS_NO_ALT_TITLE and _JLMS_YES_ALT_TITLE */
$JLMS_LANGUAGE['_JLMS_PN_RESULTS'] = 'Resultados';
$JLMS_LANGUAGE['_JLMS_PN_OF_TOTAL'] = 'del total';
$JLMS_LANGUAGE['_JLMS_PN_NO_RESULTS'] = 'No disponemos de resultados para su consulta';
$JLMS_LANGUAGE['_JLMS_PN_FIRST_PAGE'] = 'Primero';
$JLMS_LANGUAGE['_JLMS_PN_PREV_PAGE'] = 'Previo';
$JLMS_LANGUAGE['_JLMS_PN_END_PAGE'] = 'Final';
$JLMS_LANGUAGE['_JLMS_PN_NEXT_PAGE'] = 'Siguiente';
$JLMS_LANGUAGE['_JLMS_PN_DISPLAY_NUM'] = 'Ver No.';
$JLMS_LANGUAGE['_JLMS_SEPARATOR'] = 'Separador';
$JLMS_LANGUAGE['_JLMS_LEFT'] = 'Derecha';
$JLMS_LANGUAGE['_JLMS_CENTER'] = 'Centro';
$JLMS_LANGUAGE['_JLMS_RIGHT'] = 'Izquierda';

$JLMS_LANGUAGE['_JLMS_TXT_TOP'] = 'arriba';
$JLMS_LANGUAGE['_JLMS_TXT_BACK'] = 'volver';

$JLMS_LANGUAGE['_JLMS_PLEASE_LOGIN'] = "¿Estás registrado? Por favor, utiliza tus datos de usuario y contraseña.";
$JLMS_LANGUAGE['_JLMS_PLEASE_REGISTER'] = "¿No estás registrado? Es indispensable que te registres previamente.";
$JLMS_LANGUAGE['_JLMS_SHOW_LOGIN'] = "Mostrar formulario de acceso.";
$JLMS_LANGUAGE['_JLMS_SHOW_REGISTRATION'] = "Mostrar formulario de registro.";
$JLMS_LANGUAGE['_JLMS_REGISTRATION_DISABLED'] = "El sistema de registro está desactivado.";
$JLMS_LANGUAGE['_JLMS_REGISTRATION_COMPLETE'] = "¡Tu alta se ha realizado correctamente! Muchas gracias.";
$JLMS_LANGUAGE['_JLMS_LOGIN_SUCCESS'] = "Acabas de acceder correctamente.";
$JLMS_LANGUAGE['_JLMS_REGISTRATION_ACTIVATION'] = 'Tu cuenta de usuario acaba de ser creada. Hemos enviado a tu correo electrónico un enlace para confirmar y activar tu cuenta de usuario.';
$JLMS_LANGUAGE['_JLMS_LOGIN_INCORRECT'] = 'El nombre del usuario o la contraseña que ha escrito no es correcto. ¡Por favor, inténtelo de nuevo!';

$JLMS_LANGUAGE['_JLMS_SB_COURSE_FOLDER'] = ' - carpeta del curso - ';
$JLMS_LANGUAGE['_JLMS_SB_NO_CERTIFICATE'] = ' - sin emisión de certificado - ';
$JLMS_LANGUAGE['_JLMS_SB_FIRST_ITEM'] = ' - primer elemento - ';
$JLMS_LANGUAGE['_JLMS_SB_LAST_ITEM'] = ' - último elemento - ';
$JLMS_LANGUAGE['_JLMS_SB_QUIZ_SELECT_QTYPE'] = ' - elije el tipo de pregunta - ';
$JLMS_LANGUAGE['_JLMS_SB_SELECT_USER'] = ' - selecciona un alumno - ';
$JLMS_LANGUAGE['_JLMS_SB_SELECT_QUIZ'] = ' - selecciona un ejercicio - ';
$JLMS_LANGUAGE['_JLMS_SB_FILTER_NONE'] = 'Nada';


$JLMS_LANGUAGE['_JLMS_SB_ALL_USERS'] = ' - todos los usuarios - ';
/* 1.0.2 */
$JLMS_LANGUAGE['_JLMS_SHOW_IN_GRADEBOOK_OPTION'] = 'Mostrar en el Libro de Calificaciones';

/* 1.0.1 */
$JLMS_LANGUAGE['_JLMS_NO_ALT_TITLE'] = 'No';
$JLMS_LANGUAGE['_JLMS_SETTINGS_ALT_TITLE'] = 'Configuración';

/* 1.0.0 */
//roles
$JLMS_LANGUAGE['_JLMS_ROLE_TEACHER'] = 'Profesor'; 
$JLMS_LANGUAGE['_JLMS_ROLE_STU'] = 'Alumno'; 
$JLMS_LANGUAGE['_JLMS_ROLE_CEO'] = 'Tutor'; 
$JLMS_LANGUAGE['_JLMS_GROUP'] = 'Grupo de usuarios'; 

$JLMS_LANGUAGE['_JLMS_ENTER_NAME'] = 'Escribe tu nombre:'; 
$JLMS_LANGUAGE['_JLMS_CHOOSE_FILE'] = 'Elije un archivo :'; 
$JLMS_LANGUAGE['_JLMS_SHORT_DESCRIPTION'] = 'Breve descripción:'; 
$JLMS_LANGUAGE['_JLMS_DESCRIPTION'] = 'Descripción:'; 
$JLMS_LANGUAGE['_JLMS_COMMENT'] = 'Comentario:'; 
$JLMS_LANGUAGE['_JLMS_TEACHER_COMMENT'] = 'Comentario del profesor:'; 
$JLMS_LANGUAGE['_JLMS_PLACE_IN'] = 'Colocar en:'; 
$JLMS_LANGUAGE['_JLMS_ORDERING'] = 'Orden de visualización:'; 
$JLMS_LANGUAGE['_JLMS_FILTER'] = 'Filtro:'; 
$JLMS_LANGUAGE['_JLMS_LINK_LOCATION'] = 'Localización:'; 
$JLMS_LANGUAGE['_JLMS_PUBLISHING'] = 'Publicado:'; 
$JLMS_LANGUAGE['_JLMS_DATE'] = 'Fecha:'; 
$JLMS_LANGUAGE['_JLMS_PERIOD'] = 'Seleccione un período:'; 
$JLMS_LANGUAGE['_JLMS_START_DATE'] = 'Fecha de inicio:'; 
$JLMS_LANGUAGE['_JLMS_END_DATE'] = 'Fecha de finalización:'; 
$JLMS_LANGUAGE['_JLMS_DATES_PUBLISH'] = 'Fechas de publicación'; 
$JLMS_LANGUAGE['_JLMS_EDIT'] = 'Editar'; 
$JLMS_LANGUAGE['_JLMS_DELETE'] = 'Borrar'; 
$JLMS_LANGUAGE['_JLMS_VIEW_DETAILS'] = 'más información'; 
$JLMS_LANGUAGE['_JLMS_TOTAL'] = '<b>TOTAL:</b>';
$JLMS_LANGUAGE['_JLMS_ENROLL'] = 'Pulsa este icono para matricularte o inscribirte en el curso';  
// please ...
$JLMS_LANGUAGE['_JLMS_SELECT_FILE'] = 'Por favor elije un archivo.'; 
$JLMS_LANGUAGE['_JLMS_PL_ENTER_NAME'] = 'Por favor escribe tu nombre'; 
//alt & titles for img.buttons
$JLMS_LANGUAGE['_JLMS_SAVE_ALT_TITLE'] = 'Guardar';
$JLMS_LANGUAGE['_JLMS_SEND_ALT_TITLE'] = 'Enviar';
$JLMS_LANGUAGE['_JLMS_PREVIEW_ALT_TITLE'] = 'Previsualizar';
$JLMS_LANGUAGE['_JLMS_IMPORT_ALT_TITLE'] = 'Importar'; 
$JLMS_LANGUAGE['_JLMS_CANCEL_ALT_TITLE'] = 'Cancelar'; 
$JLMS_LANGUAGE['_JLMS_BACK_ALT_TITLE'] = 'Atrás'; 
$JLMS_LANGUAGE['_JLMS_COMPLETE_ALT_TITLE'] = 'Completo';
$JLMS_LANGUAGE['_JLMS_START_ALT_TITLE'] = 'Comenzar'; 
$JLMS_LANGUAGE['_JLMS_NEXT_ALT_TITLE'] = 'Siguiente'; 
$JLMS_LANGUAGE['_JLMS_CONTINUE_ALT_TITLE'] = 'Continuar'; 
$JLMS_LANGUAGE['_JLMS_PREV_ALT_TITLE'] = 'más reciente'; 
$JLMS_LANGUAGE['_JLMS_CONTENTS_ALT_TITLE'] = 'Contenidos'; 
$JLMS_LANGUAGE['_JLMS_RESTART_ALT_TITLE'] = 'Reiniciar'; 
$JLMS_LANGUAGE['_JLMS_EXPORT_ALT_TITLE'] = 'Exportar'; 
$JLMS_LANGUAGE['_JLMS_CLEAR_ALT_TITLE'] = 'Borrar'; 
$JLMS_LANGUAGE['_JLMS_OK_ALT_TITLE'] = 'OK'; 
$JLMS_LANGUAGE['_JLMS_YES_ALT_TITLE'] = 'Si'; 
$JLMS_LANGUAGE['_JLMS_ARCHIVE_ALT_TITLE'] = 'Archive';

//JS alerts
$JLMS_LANGUAGE['_JLMS_ALERT_SELECT_ITEM'] = 'Por favor, primero debes seleccionar un elemento.';
$JLMS_LANGUAGE['_JLMS_ALERT_ENTER_PERIOD'] = 'Por favor, primero debes especificar un período.'; 

//header alt's and titles
$JLMS_LANGUAGE['_JLMS_HEAD_USERMAN_STR'] = 'Administrador de usuarios';
$JLMS_LANGUAGE['_JLMS_HEAD_CHAT_STR'] = 'Sala de conversaciones'; 
$JLMS_LANGUAGE['_JLMS_HEAD_USERGROUP_STR'] = 'Administrador de grupos de usuarios'; 
$JLMS_LANGUAGE['_JLMS_HEAD_USER_STR'] = 'Agreguar usuarios'; 
$JLMS_LANGUAGE['_JLMS_HEAD_UNDEFINED_STR'] = 'Sin definir'; 
$JLMS_LANGUAGE['_JLMS_HEAD_LPATH_STR'] = 'Procesos de aprendizaje'; 
$JLMS_LANGUAGE['_JLMS_HEAD_SCORM_STR'] = 'Administrador de archivos SCORM'; 
$JLMS_LANGUAGE['_JLMS_HEAD_CONF_STR'] = 'Sala de videoconferencias'; 
$JLMS_LANGUAGE['_JLMS_HEAD_AGENDA_STR'] = 'Notificaciones'; 
$JLMS_LANGUAGE['_JLMS_HEAD_LINK_STR'] = 'Enlaces'; 
$JLMS_LANGUAGE['_JLMS_HEAD_DOCS_STR'] = 'Biblioteca de documentos'; 
$JLMS_LANGUAGE['_JLMS_HEAD_COURSES_STR'] = 'Listado de cursos'; 
$JLMS_LANGUAGE['_JLMS_HEAD_DROPBOX_STR'] = 'Buzón de intercambios'; 
$JLMS_LANGUAGE['_JLMS_HEAD_HOMEWORK_STR'] = 'Ejercicios y trabajos'; 
$JLMS_LANGUAGE['_JLMS_HEAD_ATTENDANCE_STR'] = 'Libro de asistencias'; 
$JLMS_LANGUAGE['_JLMS_HEAD_TRACKING_STR'] = 'Seguimientos'; 
$JLMS_LANGUAGE['_JLMS_HEAD_GRADEBOOK_STR'] = 'Libro de calificaciones'; 
$JLMS_LANGUAGE['_JLMS_HEAD_CERTIFICATE_STR'] = 'Libro de certificaciones'; 
$JLMS_LANGUAGE['_JLMS_HEAD_MAILBOX_STR'] = 'Mensajería'; 
$JLMS_LANGUAGE['_JLMS_HEAD_QUIZ_STR'] = 'Pruebas, autoevaluaciones y exámenes'; 
$JLMS_LANGUAGE['_JLMS_HEAD_FORUM_STR'] = 'Foro'; 
$JLMS_LANGUAGE['_JLMS_HEAD_SUBSCRIPTION_STR'] = 'Inscripción y matriculación'; 

$JLMS_LANGUAGE['_JLMS_PATHWAY_HOME'] = 'Acceso al campus';
$JLMS_LANGUAGE['_JLMS_PATHWAY_COURSE_HOME'] = 'Acceso al curso';

//TOP toolbar text's
$JLMS_LANGUAGE['_JLMS_TOOLBAR_LIBRARY'] = 'Biblioteca de archivos'; /* 1.0.5 */
$JLMS_LANGUAGE['_JLMS_TOOLBAR_HOME'] = 'Acceso al campus'; 
$JLMS_LANGUAGE['_JLMS_TOOLBAR_COURSES'] = 'Listado de cursos'; 
$JLMS_LANGUAGE['_JLMS_TOOLBAR_CEO_PARENT'] = 'Acceso de tutores'; 
$JLMS_LANGUAGE['_JLMS_TOOLBAR_SUBSCRIPTIONS'] = 'Suscripciones';
$JLMS_LANGUAGE['_JLMS_TOOLBAR_CHAT'] = 'Sala de conversaciones';
$JLMS_LANGUAGE['_JLMS_TOOLBAR_FORUM'] = 'Foro'; 
$JLMS_LANGUAGE['_JLMS_TOOLBAR_CONF'] = 'Sala de videoconferencias'; 
$JLMS_LANGUAGE['_JLMS_TOOLBAR_QUIZZES'] = 'Pruebas, autoevaluaciones y exámenes'; 
$JLMS_LANGUAGE['_JLMS_TOOLBAR_DOCS'] = 'Biblioteca de documentos'; 
$JLMS_LANGUAGE['_JLMS_TOOLBAR_LINKS'] = 'Enlaces'; 
$JLMS_LANGUAGE['_JLMS_TOOLBAR_DROP'] = 'Buzón de intercambio'; 
$JLMS_LANGUAGE['_JLMS_TOOLBAR_AGENDA'] = 'Notificaciones'; 
$JLMS_LANGUAGE['_JLMS_TOOLBAR_LPATH'] = 'Procesos de aprendizaje'; 
$JLMS_LANGUAGE['_JLMS_TOOLBAR_ATTEND'] = 'Asistencias'; 
$JLMS_LANGUAGE['_JLMS_TOOLBAR_TRACK'] = 'Seguimientos'; 
$JLMS_LANGUAGE['_JLMS_TOOLBAR_USERS'] = 'Administrador de usuarios';
$JLMS_LANGUAGE['_JLMS_TOOLBAR_CONFIG'] = 'configuración';  
$JLMS_LANGUAGE['_JLMS_TOOLBAR_TO_STU'] = 'Visionado como alumno'; 
$JLMS_LANGUAGE['_JLMS_TOOLBAR_TO_TEACH'] = 'Visionado como profesor'; 
$JLMS_LANGUAGE['_JLMS_TOOLBAR_BACK'] = 'Atrás'; 
$JLMS_LANGUAGE['_JLMS_TOOLBAR_HOMEWORK'] = 'Ejercicios'; 
$JLMS_LANGUAGE['_JLMS_TOOLBAR_GRADEBOOK'] = 'Libro de calificaciones'; 
$JLMS_LANGUAGE['_JLMS_TOOLBAR_MAILBOX'] = 'Mensajería'; 
$JLMS_LANGUAGE['_JLMS_TOOLBAR_HELP'] = 'Ayuda'; 
$JLMS_LANGUAGE['_JLMS_TOOLBAR_USER_OPTIONS'] = 'Opciones de usuario'; 

$JLMS_LANGUAGE['_JLMS_CURRENT_COURSE'] = 'Ahora mismo te encuentras en el curso: '; 
//user info
$JLMS_LANGUAGE['_JLMS_UI_USERNAME'] = 'Usuario:'; 
$JLMS_LANGUAGE['_JLMS_UI_NAME'] = 'Nombre:'; 
$JLMS_LANGUAGE['_JLMS_UI_EMAIL'] = 'Correo electrónico:'; 
$JLMS_LANGUAGE['_JLMS_UI_GROUP'] = 'Grupo de usuario:'; 
//
$JLMS_LANGUAGE['_JLMS_STATUS_PUB'] = 'Publicado'; 
$JLMS_LANGUAGE['_JLMS_STATUS_PUB2'] = 'Publicado pero con condiciones'; 
$JLMS_LANGUAGE['_JLMS_STATUS_UNPUB'] = 'No publicado'; 
$JLMS_LANGUAGE['_JLMS_SET_PUB'] = 'Publicar'; 
$JLMS_LANGUAGE['_JLMS_SET_UNPUB'] = 'No publicar'; 
$JLMS_LANGUAGE['_JLMS_STATUS_EXPIRED'] = 'Expirado'; 

//redirect (user-switch)
$JLMS_LANGUAGE['_JLMS_CLICK_HERE_TO_REDIRECT'] = 'Pulsa aquí si tu navegador de internet no te redirige automáticamente.'; 
$JLMS_LANGUAGE['_JLMS_REDIRECTING'] = 'Espera por favor'; 

$JLMS_LANGUAGE['_JLMS_RESULT_OK'] = 'OK'; 
$JLMS_LANGUAGE['_JLMS_RESULT_FAIL'] = 'Error'; 

//mainpage
$JLMS_LANGUAGE['_JLMS_HOME_COURSES_TITLE'] = '-- CURSOS EN LOS QUE ESTÁS MATRICULADO --'; 
$JLMS_LANGUAGE['_JLMS_HOME_DROPBOX_TITLE'] = '-- BUZON DE INTERCAMBIO ( <font color="green">{X}</font> / {Y} ) --'; //{X} - elementos nuevos que se reciben; {Y} - todos elementos que se reciben; {X} and {Y} - Parametros opcionales (y etiqueta '<font>' tambien)
$JLMS_LANGUAGE['_JLMS_HOME_DROPBOX_NO_ITEMS'] = 'Tu Buzón de intercambio está vacío'; 
$JLMS_LANGUAGE['_JLMS_HOME_COURSES_NO_ITEMS'] = 'todavía no estás matrículado en ningún curso'; 
$JLMS_LANGUAGE['_JLMS_HOME_AGENDA_NO_ITEMS'] = 'no tienes notificaciones'; 
$JLMS_LANGUAGE['_JLMS_HOME_HOMEWORK_NO_ITEMS'] = 'no tienes ejercicios pendientes'; 
$JLMS_LANGUAGE['_JLMS_HOME_AGENDA_TITLE'] = '-- NOTIFICACIONES --'; 
$JLMS_LANGUAGE['_JLMS_HOME_HOMEWORK_TITLE'] = '-- EJERCICIOS --'; 
$JLMS_LANGUAGE['_JLMS_HOME_COURSES_LIST'] = '-- LISTADO DE TODOS LOS CURSOS DEL CAMPUS --'; 
$JLMS_LANGUAGE['_JLMS_HOME_COURSES_LIST_HREF'] = 'pulsa para ver todos los cursos disponibles'; 
$JLMS_LANGUAGE['_JLMS_HOME_AUTHOR'] = 'Profesor:'; 
$JLMS_LANGUAGE['_JLMS_HOME_COURSE_DETAIL'] = 'Detalles del curso'; 

$JLMS_LANGUAGE['_JLMS_AGREEMENT'] = 'Para continuar, acepta el pliego de condiciones.'; 
$JLMS_LANGUAGE['_JLMS_CONGRATULATIONS'] = "¡Enhorabuena! Acabas de matricularte como alumno del curso #COURSENAME#.<br>Deseamos que este curso de formación sea de tu total agrado.";
$JLMS_LANGUAGE['JLMS_FORUM_NOT_MEMBER'] = "Debes utilizar tu nombre de usuario y contraseña de nuevo para acceder al foro.";

//some image titles and alts
$JLMS_LANGUAGE['_JLMS_T_A_VIEW_ZIP_PACK'] = "Ver el contenido del archivo comprimido"; 
$JLMS_LANGUAGE['_JLMS_T_A_VIEW_CONTENT'] = "Ver el contenido"; 
$JLMS_LANGUAGE['_JLMS_T_A_DOWNLOAD'] = "Descargar el documento"; 
$JLMS_LANGUAGE['_JLMS_T_A_VIEW_LINK'] = "Ver el enlace"; 

//
$JLMS_LANGUAGE['_JLMS_MESSAGE_SHORT_COURSE_INFO'] = "Te informamos que no estás matriculado en este curso. Si estabas matriculado como alumno y ves este mensaje, es probable que tu cuenta como usuario esté bloqueada o haya sido borrada de nuestra base de datos. También es probable que el período para realizar este curso haya finalizado.<br><br>Para que tengas una información más exacta sobre esta incidencia ponte en contacto con nosotros. Disculpa las molestias. Muchas gracias por tu comprensión.";

//some error messages
$JLMS_LANGUAGE['_JLMS_EM_SELECT_FILE'] = "Selecciona el archivo que quieres subir al campus virtual."; 
$JLMS_LANGUAGE['_JLMS_EM_BAD_FILENAME'] = "Escribe el nombre del archivo exclusivamente con caracteres alfanuméricos y sin espacios entre ellos."; 
$JLMS_LANGUAGE['_JLMS_EM_BAD_FILEEXT'] = "La extensión de este archivo no está permitida."; 
$JLMS_LANGUAGE['_JLMS_EM_BAD_SCORM'] = "No es un archivo SCORM";  
$JLMS_LANGUAGE['_JLMS_EM_SCORM_FOLDER'] = "Este archivo SCORM ha producido un error en el sistema cuando intentabas crear una carpeta en el servidor."; 
$JLMS_LANGUAGE['_JLMS_EM_READ_PACKAGE_ERROR'] = "Se ha producido un error al leer el archivo."; 
$JLMS_LANGUAGE['_JLMS_EM_UPLOAD_SIZE_ERROR'] = "La subida del archivo ha fallado. Por favor, contacta urgentemente con el administrador del campus para que configuren correctamente la sección 'upload_max_filesize' del archivo PHP del servidor.";  
$JLMS_LANGUAGE['_JLMS_EM_DISABLED_OPTION'] ='Esta opción ha sido deshabilitada en este curso.'; 

// 'User option' floating window text's:
$JLMS_LANGUAGE['_JLMS_UO_SELECT_LANGUAGE'] = "Elegir idioma:"; 
$JLMS_LANGUAGE['_JLMS_UO_SWITCH_TYPE'] = "Ver el curso como:";
$JLMS_LANGUAGE['_JLMS_ONLINE_USERS'] = "Alumnos conectados:";
$JLMS_LANGUAGE['_JLMS_OU_USER'] = "Nombre de usuario";
$JLMS_LANGUAGE['_JLMS_OU_LAST_ACTIVE'] = "Actividad más reciente";

// spec registration
$JLMS_LANGUAGE['_JLMS_COURSES_SPEC_REG'] = "Información adicional sobre la matrícula";
$JLMS_LANGUAGE['_JLMS_COURSES_SPEC_REG_QUEST'] = "Pregunta sobre la matrícula:";
$JLMS_LANGUAGE['_JLMS_COURSES_SPEC_REG_CONFIRM'] = "Por favor, verifica cuidadosamente los datos que has escrito. Recuerde que no podrás editar ni corregir estos datos cuando se guarden en el sistema del campus.";
?>