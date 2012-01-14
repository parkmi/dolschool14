<?php 
/**
* includes/classes/lms.tabs.php
* (c) JoomaLMS eLearning Software http://www.joomlalms.com/
**/

defined( '_JLMS_EXEC' ) or die( 'Restricted access' );

class JLMSTabs extends JLMSObject {
	/** @var int Use cookies */
	var $useCookies = 0;
	var $old_version = 0;

	function JLMSTabs($useCookies) {
		global $JLMS_CONFIG;
		
		$document	=& JFactory::getDocument();
		$lang	 	=& JFactory::getLanguage();
		$css		= $lang->isRTL() ? 'tabpane_rtl.css?rev=110' : 'tabpane.css?rev=110';
		$url		= $JLMS_CONFIG->get('live_site').'/components/com_joomla_lms/';

		$document->addStyleSheet( $url. 'includes/js/tabs/'.$css, 'text/css', null, array(' id' => 'luna-tab-style-sheet' ));
		$document->addScript( $url. 'includes/js/tabs/tabpane_mini.js' );
		$this->old_version = 0;
		$this->useCookies = $useCookies;
	}

	function startPane($id){
		return "<div class=\"tab-page\" id=\"".$id."\">"
			."<script type=\"text/javascript\">\n"
			."	var tabPane1 = new WebFXTabPane( document.getElementById( \"".$id."\" ), ".(int)$this->useCookies." )\n"
			."</script>\n";
	}

	function endPane() {
		$return =  "</div>";
		return $return;
	}

	function startTab( $tabText, $paneid ) {
		return "<div class=\"tab-page\" id=\"".$paneid."\">"
			."<span class=\"tab\"><span>".$tabText."</span></span>"
			."<script type=\"text/javascript\">\n"
			."  tabPane1.addTabPage( document.getElementById( \"".$paneid."\" ) );"
			."</script>";
	}

	function endTab() {
		$return =  "</div>";
		return $return;
	}
}
?>