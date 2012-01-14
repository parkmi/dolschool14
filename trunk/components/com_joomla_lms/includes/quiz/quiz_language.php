<?php
/**
* includes/quiz/quiz_language.php
* JoomlaQuiz plugin fo LMS
* * * ElearningForce DK
**/

// no direct access
defined( '_JLMS_EXEC' ) or die( 'Restricted access' );

$GLOBALS['jq_language'] = array();
global $jq_language;
global $JLMS_LANGUAGE;
foreach ($JLMS_LANGUAGE as $JL_key => $JL_val) {
	if (substr($JL_key,0, 8) == '_JLMS_q_') {
		$new_key = substr($JL_key,8);
		$new_val = $JL_val;
		if (defined($JL_key)) {
			$new_val = constant($JL_key);
			if (!$new_val) {
				$new_val = $JL_val;
			}
		}
		$jq_language[$new_key] = $new_val;
	}
}

?>