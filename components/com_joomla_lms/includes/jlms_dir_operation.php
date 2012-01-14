<?php
/**
* JLMS_dir_operation.php
* Joomla LMS Component
* * * ElearningForce DK
*/
if (!function_exists('copydirr')) {
	function copydirr($fromDir,$toDir,$chmod=0757,$verbose=false)
	/*
	copies everything from directory $fromDir to directory $toDir
	and sets up files mode $chmod
	*/
	{
		//* Check for some errors
		$errors=array();
		$messages=array();
		if (!is_writable($toDir)) {
			$errors[]='target '.$toDir.' is not writable';
		}
		if (!is_dir($toDir)) {
			$errors[]='target '.$toDir.' is not a directory';
		}
		if (!is_dir($fromDir)) {
			$errors[]='source '.$fromDir.' is not a directory';
		}
		if (!empty($errors))
		{
			if ($verbose) {
				foreach($errors as $err) {
					echo '<strong>Error</strong>: '.$err.'<br />';
				}
			}
			return false;
		}
		//*/
		$exceptions=array('.','..');
		//* Processing
		$handle=opendir($fromDir);
		while (false!==($item=readdir($handle)))
		if (!in_array($item,$exceptions))
		{
			//* cleanup for trailing slashes in directories destinations
			$from=str_replace('//','/',$fromDir.'/'.$item);
			$to=str_replace('//','/',$toDir.'/'.$item);
			//*/
			if (is_file($from))
			{
				if (@copy($from,$to))
				{
					chmod($to,$chmod);
					touch($to,filemtime($from)); // to track last modified time
					$messages[]='File copied from '.$from.' to '.$to;
				}
				else
				$errors[]='cannot copy file from '.$from.' to '.$to;
			}
			if (is_dir($from))
			{
				if (@mkdir($to))
				{
					chmod($to,$chmod);
					$messages[]='Directory created: '.$to;
				}
				else
				$errors[]='cannot create directory '.$to;
				copydirr($from,$to,$chmod,$verbose);
			}
		}
		closedir($handle);
		//*/
		//* Output
		if ($verbose)
		{
			foreach($errors as $err)
			echo '<strong>Error</strong>: '.$err.'<br />';
			foreach($messages as $msg)
			echo $msg.'<br />';
		}
		//*/
		return true;
	}
}
//delete Directory and all files
if (!function_exists('deldir')) {
	function deldir( $dir ) {
		$current_dir = opendir( $dir );
		$old_umask = umask(0);
		while ($entryname = readdir( $current_dir )) {
			if ($entryname != '.' and $entryname != '..') {
				if (is_dir( $dir . $entryname )) {
					deldir( mosPathName( $dir . $entryname ) );
				} else {
	                @chmod($dir . $entryname, 0777);
					unlink( $dir . $entryname );
				}
			}
		}
		umask($old_umask);
		closedir( $current_dir );
		return rmdir( $dir );
	}
}