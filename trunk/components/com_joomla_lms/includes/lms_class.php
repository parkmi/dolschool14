<?php
/**
* includes/lms_class.php
* JoomlaLMS Component
* * * ElearningForce Inc.
**/

// no direct access
defined( '_JLMS_EXEC' ) or die( 'Restricted access' );
/*class JLMSTabs extends mosTabs {
	var $JLMSTabs_template = 'luna';
}*/
class JLMSObject
{
	/**
	 * A hack to support __construct() on PHP 4
	 * Hint: descendant classes have no PHP4 class_name() constructors,
	 * so this constructor gets called first and calls the top-layer __construct()
	 * which (if present) should call parent::__construct()
	 *
	 * @return Object
	 */
	function JLMSObject()
	{
		$args = func_get_args();
		call_user_func_array(array(&$this, '__construct'), $args);
	}

	/**
	 * Class constructor, overridden in descendant classes.
	 *
	 * @access	protected
	 */
	function __construct() {}

	/**
	* @param string The name of the property
	* @param mixed The value of the property to set
	*/
	function set( $property, $value=null ) {
		$this->$property = $value;
	}

	/**
	* @param string The name of the property
	* @param mixed  The default value
	* @return mixed The value of the property
	*/
	function get($property, $default=null)
	{
		if(isset($this->$property)) {
			return $this->$property;
		}
		return $default;
	}

	/**
	 * Returns an array of public properties
	 *
	 * @return array
	 */
	function getPublicProperties()
	{
		static $cache = null;

		if (is_null( $cache )) {
			$cache = array();
			foreach (get_class_vars( get_class( $this ) ) as $key=>$val) {
				if (substr( $key, 0, 1 ) != '_') {
					$cache[] = $key;
				}
			}
		}
		return $cache;
	}

	/**
	 * Object-to-string conversion.
	 * Each class can override it as necessary.
	 *
	 * @return string This name of this class
	 */
	function toString()
	{
		return get_class($this);
	}
}


class JLMSTabs extends JLMSObject {
	/** @var int Use cookies */
	var $useCookies = 0;
	var $old_version = 0;

	function JLMSTabs($useCookies) {
		global $mainframe;
		if (class_exists('JFactory')) {
			$document	=& JFactory::getDocument();
			$lang	 	=& JFactory::getLanguage();
			$css		= $lang->isRTL() ? 'tabpane_rtl.css' : 'tabpane.css';
			$url		= $mainframe->isAdmin() ? $mainframe->getSiteURL() : JURI::base();
	
			$document->addStyleSheet( $url. 'includes/js/tabs/'.$css, 'text/css', null, array(' id' => 'luna-tab-style-sheet' ));
			$document->addScript( $url. 'includes/js/tabs/tabpane_mini.js' );
			$this->old_version = 0;
		} else {
			global $mosConfig_live_site;
			JLMSaddHeadTag( '<link rel="stylesheet" type="text/css" media="all" href="includes/js/tabs/tabpane.css" id="luna-tab-style-sheet" />' );
			JLMSaddHeadTag( "<script type=\"text/javascript\" src=\"". $mosConfig_live_site . "/includes/js/tabs/tabpane_mini.js\"></script>");
			$this->old_version = 1;
		}
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
			."<h2 class=\"tab\"><span>".$tabText."</span></h2>"
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