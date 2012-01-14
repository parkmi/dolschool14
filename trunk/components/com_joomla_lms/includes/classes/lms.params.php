<?php
/**
* includes/classes/lms.params.php
* Class to parse parameters (without mosParameters class)
* * * ElearningForce Inc
**/

// no direct access
defined( '_JLMS_EXEC' ) or die( 'Restricted access' );

class JLMSParameters extends MosParameters {

	function JLMSParameters( $paramsValues ) {
	    $this->MosParameters( $paramsValues );
	}

}
?>