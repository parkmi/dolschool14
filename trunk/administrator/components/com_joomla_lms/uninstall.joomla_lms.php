<?php
/**
* uninstall.joomla_lms.php
* (c) JoomaLMS eLearning Software http://www.joomlalms.com/
**/

// Don't allow direct linking
//defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
if (!defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) { die( 'Restricted access' ); }

function com_uninstall()
{
	return "JoomlaLMS component uninstalled successfully.";
}		
?>