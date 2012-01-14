<?php
/**
* admin.dev_config.toolbar.php
* JoomlaLMS Component
*/

//defined( '_VALID_MOS' ) or die( 'Restricted access' );
if (!defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) { die( 'Restricted access' ); }

//processors
class ALD_toolbar {

	function _DEFAULT() {
		JToolBarHelper::title( _JOOMLMS_COMP_NAME.': DEV.config', 'config.png' );
		JToolBarHelper::save( 'save_config' );
		JToolBarHelper::spacer();
	}
}

function ALD_process_toolbar() {
	$page 	= mosGetParam( $_REQUEST, 'page', '' );
	switch ($page) {
		case 'save_config':
		default:
			ALD_toolbar::_DEFAULT();
		break;
	}
}
?>