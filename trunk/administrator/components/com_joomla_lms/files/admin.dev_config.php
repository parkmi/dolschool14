<?php
/**
* admin.dev_config.php
* (c) JoomaLMS eLearning Software http://www.joomlalms.com/
**/

// no direct access
if (!defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) { die( 'Restricted access' ); }
define( '_JLMS_EXEC', 1 );

require_once(_JOOMLMS_ADMIN_HOME.'/files/admin.dev_config.html.php');

$task 	= mosGetParam( $_REQUEST, 'task', 'dev_config' );
$page 	= mosGetParam( $_REQUEST, 'page', '' );

switch ($page) {

	case 'save_config':
		saveConfigSource( $option );
	break;

	default:
		editConfigSource( $option );
	break;
}

function editConfigSource( $option ) {

	$file = JPATH_SITE .'/components/com_joomla_lms/includes/config.inc.php';
	
	if ( $fp = fopen( $file, 'r' ) ) {
		$content = fread( $fp, filesize( $file ) );
		$content = htmlspecialchars( $content );

		HTML_config::editConfigSource( $content, $option );
	} else {
		mosRedirect( 'index.php?option='. $option .'&task=dev_config' , _JLMS_CFG_MSG_COULD_NOT_OPEN.' '.$file );
	}
}

function saveConfigSource( $option ) {
	josSpoofCheck();

	//$filecontent 	= mosGetParam( $_POST, 'filecontent', '', _MOS_ALLOWHTML );
	$filecontent = isset($_POST['filecontent'])?strval($_POST['filecontent']):'';
	$filecontent = (get_magic_quotes_gpc()) ? stripslashes( $filecontent ) : $filecontent;
//	var_dump($filecontent);die;
	if ($filecontent) {
		$file = JPATH_SITE .'/components/com_joomla_lms/includes/config.inc.php';
	
		$enable_write 	= mosGetParam($_POST,'enable_write',0);
		$oldperms 		= fileperms($file);
	
		if ($enable_write) @chmod($file, $oldperms | 0222);
	
		clearstatcache();
		if ( is_writable( $file ) == false ) {
			mosRedirect( 'index.php?option='. $option .'&task=dev_config' , str_replace( '{file}', $file, _JLMS_CFG_MSG_F_NOT_WRITABLE ) );
		}
	
		if ( $fp = fopen ($file, 'w' ) ) {
			fputs( $fp, $filecontent, strlen( $filecontent ) );
			fclose( $fp );
			if ($enable_write) {
				@chmod($file, $oldperms);
			} else {
				if (mosGetParam($_POST,'disable_write',0))
					@chmod($file, $oldperms & 0777555);
			} // if
			mosRedirect( 'index.php?option='. $option .'&task=dev_config' );
		} else {
			if ($enable_write) @chmod($file, $oldperms);
			mosRedirect( 'index.php?option='. $option .'&task=dev_config', _JLMS_CFG_MSG_F_FAILD_TO_OPEN );
		}
	} else {
		mosRedirect( 'index.php?option='. $option .'&task=dev_config', _JLMS_CFG_MSG_UNKNOWN_ERROR );
	}
}
?>