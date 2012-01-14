<?php
class jlmsPluginHandler extends JObject {

	var $_events			= null;
	var $_lists				= null;
	var $_bots				= null;
	var $_loading			= null;


	var $_content_bots	= null;
	var $_content_bot_params	= array();
	var $_user_bot_params	= array();
	var $_course_bot_params	= array();
	var $_search_bot_params	= array();

	function jlmsPluginHandler() {
		$this->_events = array();
	}

	function loadBotGroup( $group ) {
		$db = & JFactory::getDbo();

		$group = trim( $group );

		switch ( $group ) {
			case 'content':
				if (!defined( '_JLMS_CONTENT_botS' )) {
					/** ensure that query is only called once */
					define( '_JLMS_CONTENT_botS', 1 );

					$query = "SELECT folder, element, published, params"
					. "\n FROM #__lms_plugins"
					. "\n WHERE published >= 1"
					. "\n AND folder = 'content'"
					. "\n ORDER BY ordering"
					;
					$db->setQuery( $query );

					// load query into class variable _content_bots
					if (!($this->_content_bots = $db->loadObjectList())) {
						//echo "Error loading bots: " . $db->getErrorMsg();
						return false;
					}
				}

				// pull bots to be processed from class variable
				$bots = $this->_content_bots;
				break;

			default:
				$query = "SELECT folder, element, published, params"
				. "\n FROM #__lms_plugins"
				. "\n WHERE published >= 1"
				. "\n AND folder = " . $db->Quote( $group )
				. "\n ORDER BY ordering"
				;
				$db->setQuery( $query );

				if (!($bots = $db->loadObjectList())) {
					//echo "Error loading bots: " . $db->getErrorMsg();
					return false;
				}
				break;
		}

		// load bots found by queries
		$n = count( $bots);
		for ($i = 0; $i < $n; $i++) {
			$this->getPluginParams($bots[$i]->element, $bots[$i]->params);
			$this->loadBot( $bots[$i]->folder, $bots[$i]->element, $bots[$i]->published, $bots[$i]->params );
		}

		return true;
	}

	function getPluginParams($plugin_name, $params = null) {
		static $all_params;
		if (!is_null($params)) {
			if ($plugin_name) {
				$all_params[$plugin_name] = new jlmsPluginParameters( $params );
			}
		} else {
			if ($plugin_name) {
				if (isset($all_params[$plugin_name])) {
					return $all_params[$plugin_name];
				} else {
					$db = & JFactory::getDbo();
					$query = "SELECT params"
					. "\n FROM #__lms_plugins"
					. "\n WHERE element = '".$plugin_name."'"
					;
					$db->setQuery( $query );
					$bot = $db->loadObject();
					$all_params[$plugin_name] = new jlmsPluginParameters( $bot->params );
					return $all_params[$plugin_name];
				}
			}
			$fake_params = new jlmsPluginParameters('');
			return $fake_params;
		}
	}

	function loadBot( $folder, $element, $published, $params='' ) {

		$path = _JOOMLMS_FRONT_HOME . '/includes/plugins/' . $folder . '/' . $element . '.php';
		if (file_exists( $path )) {
			$this->_loading = count( $this->_bots );
			$bot = new stdClass;
			$bot->folder 	= $folder;
			$bot->element 	= $element;
			$bot->published = $published;
			$bot->lookup 	= $folder . '/' . $element;
			$bot->params 	= $params;
			$this->_bots[] 	= $bot;

			require_once( $path );

			$this->_loading = null;
		}
	}

	function registerFunction( $event, $function ) {
		$this->_events[$event][] = array( $function, $this->_loading );
	}

	function addListOption( $group, $listName, $value, $text='' ) {
		$this->_lists[$group][$listName][] = mosHTML::makeOption( $value, $text );
	}

	function getList( $group, $listName ) {
		return $this->_lists[$group][$listName];
	}

	function trigger( $event, $args=null, $doUnpublished=false ) {
		$result = array();

		if ($args === null) {
			$args = array();
		}
		$doUnpublished = false;// !!! important
		if ($doUnpublished) {
			// prepend the published argument
			array_unshift( $args, null );
		}
		if (isset( $this->_events[$event] )) {
			foreach ($this->_events[$event] as $func) {
				if (function_exists( $func[0] )) {
					if ($doUnpublished) {
						$args[0] = $this->_bots[$func[1]]->published;
						$result[] = call_user_func_array( $func[0], $args );
					} else if ($this->_bots[$func[1]]->published) {
						$result[] = call_user_func_array( $func[0], $args );
					}
				}
			}
		}
		return $result;
	}

	function call( $event ) {
		$doUnpublished=false;

		$args =& func_get_args();
		array_shift( $args );

		if (isset( $this->_events[$event] )) {
			foreach ($this->_events[$event] as $func) {
				if (function_exists( $func[0] )) {
					if ($this->_bots[$func[1]]->published) {
						return call_user_func_array( $func[0], $args );
					}
				}
			}
		}
		return null;
	}
}
?>