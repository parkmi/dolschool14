<?php
/**
* includes/classes/lms.pagination.php
* * * ElearningForce Inc
**/

// no direct access
defined( '_JLMS_EXEC' ) or die( 'Restricted access' );

/**
* Page navigation support class
*/
jimport('joomla.html.pagination');
class JLMSPagination extends JPagination {
	function rowNumber($i) {
		return $this->getRowOffset($i);
	}
}
?>