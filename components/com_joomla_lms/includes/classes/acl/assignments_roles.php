<?php
// no direct access
defined( '_JLMS_EXEC' ) or die( 'Restricted access' );

function & JLMS_get_allow_assignments_roles(){
	
	$assignments = array();
	
	$assignments[0] = new stdClass();
	$assignments[0]->roletype_id = 4;
	$assignments[0]->assignment = array(4,2,5,1,3);
	
	$assignments[1] = new stdClass();
	$assignments[1]->roletype_id = 2;
	$assignments[1]->assignment = array(2,5,1,3);
	
	$assignments[2] = new stdClass();
	$assignments[2]->roletype_id = 5;
	$assignments[2]->assignment = array(1);
	
	$assignments[3] = new stdClass();
	$assignments[3]->roletype_id = 1;
	$assignments[3]->assignment = array();
	
	$assignments[4] = new stdClass();
	$assignments[4]->roletype_id = 3;
	$assignments[4]->assignment = array();
	
	return $assignments;
}
?>