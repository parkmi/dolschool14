<?php
/**
* jlms_zip_operation.php
* (c) JoomaLMS eLearning Software http://www.joomlalms.com/
**/

function extractBackupArchive($archivename , $extractdir) {

	$base_Dir = mosPathName( JPATH_SITE . '/media' );

	$archivename = mosPathName( $archivename, false );
	if (preg_match( '/\.zip$/i', $archivename )) {

		require_once(_JOOMLMS_FRONT_HOME . "/includes/libraries/lms.lib.zip.php");
		$backupfile = new PclZip( $archivename );

		$ret = $backupfile->extract( PCLZIP_OPT_PATH, $extractdir );

	}
	return true;
}
?>