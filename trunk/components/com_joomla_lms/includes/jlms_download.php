<?php
/**
* jlms_download.php
* (c) JoomaLMS eLearning Software http://www.joomlalms.com/
**/

defined( '_JLMS_EXEC' ) or die( 'Restricted access' );


function jlms_readfile_chunked($filename,$retbytes=true) {
	$chunksize = 1*(1024*1024); // how many bytes per chunk
	$buffer = '';
	$cnt =0;
	// $handle = fopen($filename, 'rb');
	$handle = fopen($filename, 'rb');
	if ($handle === false) {
		return false;
	}
	while (!feof($handle)) {
		$buffer = fread($handle, $chunksize);
		echo $buffer;
		@ob_flush();
		@flush();
		if ($retbytes) {
			$cnt += strlen($buffer);
		}
	}
	$status = fclose($handle);
	if ($retbytes && $status) {
		return $cnt; // return num. bytes delivered like readfile() does.
	}
	return $status;
} 


function JLMS_download ( $file, $path, $do_exit = true ){
	if (preg_match('/Opera(\/| )([0-9].[0-9]{1,2})/', $_SERVER['HTTP_USER_AGENT'])) {
	$UserBrowser = "Opera";
	}
	elseif (preg_match('/MSIE ([0-9].[0-9]{1,2})/', $_SERVER['HTTP_USER_AGENT'])) {
		$UserBrowser = "IE";
	} else {
		$UserBrowser = '';
	}
	$mime_type = ($UserBrowser == 'IE' || $UserBrowser == 'Opera') ?'application/octetstream' : 'application/octet-stream';
	@ob_end_clean();
	header('Content-Type: ' . $mime_type);
	header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
	if ($UserBrowser == 'IE') {
		header('Content-Disposition: attachment; filename="'.$file.'"');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Content-Length: '. filesize($path));
		header('Pragma: public');
	} else {
		header('Content-Disposition: attachment; filename="'.$file.'"');
		header('Content-Length: '. filesize($path));
		header('Pragma: no-cache');
	}
	jlms_readfile_chunked($path);
	if ($do_exit) {
		exit();
	}
}
?>