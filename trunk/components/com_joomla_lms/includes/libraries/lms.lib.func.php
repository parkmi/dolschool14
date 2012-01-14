<?php
// no direct access
defined( '_JLMS_EXEC' ) or die( 'Restricted access' );

function JLMSEmailRoute($url) {
	$JLMS_CONFIG = & JLMSFactory::getConfig();
	$url = JRoute::_($url, false);
	$pos = strpos($url, 'http');
	if ($pos !== 0) {
		$del_part = JURI::root(true);
		$live_site_part = $JLMS_CONFIG->get('live_site');
		if (strlen($del_part)) {
			$live_site_part = str_replace(JURI::root(true).'/','',JURI::root(false));
		}
		$url = $live_site_part.$url;
	}
	return $url;
}

function php4_clone($object) {
	if (version_compare(phpversion(), '5.0') < 0) {
		return $object;
	} else {
		return @clone($object);
	}
}

if(!function_exists('scandir')) {
	function scandir($dir) {
		$files = array(); // added to initialize variable
		if ($dh = opendir($dir)) {
			while(false !== ($filename = readdir($dh))) {
				if($filename == '.' || $filename == '..')
					continue;
				else
					$files[] = $filename;
			}
			closedir($dh);
		}
		return $files;
	}
}
?>