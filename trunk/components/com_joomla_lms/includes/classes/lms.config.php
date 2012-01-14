<?php 

defined( '_JEXEC' ) or die( 'Restricted access' );

class JLMS_Config extends JObject {
	var $config_vars = array();
	var $_db;

	function __construct() {
		$this->initConfig();
	}

	function initConfig() {
		global $my;
		
		$user	= JFactory::getUser();
		
		/* add global config vars to JLMS config*/
		$jConfig = & JFactory::getConfig();	
				
		foreach( $jConfig->toArray() AS $k=>$v ) 
		{
			$this->set($k, $v);
		}
		
		$version = new JVersion();
		if( strnatcasecmp( $version->RELEASE, '1.6' ) >= 0 ) 
		{
			$JLMS_J16version = true; 
		} else {
			$JLMS_J16version = false;
		}
		if($JLMS_J16version && isset($this->config_vars['lms_cfg_offset'])){
			$tempDateTimeZone = new DateTimeZone($user->getParam('timezone', $this->config_vars['lms_cfg_offset']));
			$tmpDateTime = new DateTime("now", $tempDateTimeZone);
			$timeOffset = $tempDateTimeZone->getOffset($tmpDateTime);
			/*if($timeOffset < 0){
				$timeOffset = $timeOffset;
			} else {
				$timeOffset = $timeOffset - 1;
			}*/
			$this->config_vars['lms_cfg_offset'] = $timeOffset / (60 * 60);
		}
		
		$user_id = 0;
		if (class_exists('JFactory')) {
			$user =& JFactory::getUser();
			$user_id = $user->get('id');
		} elseif (isset($my->id)) {
			$user_id = $my->id;
		}
		$this->_db = & JFactory::getDbo();
		$query = "SELECT * FROM #__lms_config";
		$this->_db->SetQuery( $query );
		$cfg_data = $this->_db->LoadObjectList();
		foreach ($cfg_data as $cfg_param) {
			$str_var = 'lms_cfg_'.$cfg_param->lms_config_var;
			$this->config_vars[$str_var] = $cfg_param->lms_config_value;
		}
		if (isset($this->config_vars['lms_cfg_plugin_lpath_forum'])) {
			unset($this->config_vars['lms_cfg_plugin_lpath_forum']);
		}
		if (!$user_id && isset($this->config_vars['lms_cfg_frontpage_text_guest']) && $this->config_vars['lms_cfg_frontpage_text_guest']) {
			$this->config_vars['lms_cfg_frontpage_text'] = $this->config_vars['lms_cfg_frontpage_text_guest'];
		}		
	}

	function getItemid() {
		static $lms_itemid;
		if (is_null($lms_itemid)) {
			$temp_itemid = 0;
			$temp_activeid = 0;
			$app = & JFactory::getApplication();
			if (!$app->isAdmin()) {
				if (class_exists('JSite')) {
					$menu = &JSite::getMenu();
					$menuItem = &$menu->getActive();
					if (isset($menuItem->link) && substr($menuItem->link,0,31) == 'index.php?option=com_joomla_lms') {
						$temp_itemid = $menuItem->id;
						$temp_activeid = $menuItem->id;
					}
					if (!$temp_itemid) {
						global $Itemid;
						if (!$Itemid) {
							$Itemid = JRequest::getInt('Itemid');
						}
						if ($Itemid) {
							if ($temp_activeid && $temp_activeid == $Itemid) {
								//we have already checkd this
							} else {
								$menuItem = &$menu->getItem($Itemid);
								if (isset($menuItem->link) && substr($menuItem->link,0,31) == 'index.php?option=com_joomla_lms') {
									$temp_itemid = $menuItem->id;
								}
							}
						}
					}
				}
			}
			if (!$temp_itemid) {
				$db = & JFactory::getDbo();
				
				$version = new JVersion();	
		
				if ($version->RELEASE >= '1.6')
					$query = "SELECT id FROM #__menu WHERE link LIKE '%index.php?option=com_joomla_lms%' AND type = 'component' AND published = 1 AND client_id = 0 LIMIT 1";
				else
					$query = "SELECT id FROM #__menu WHERE link LIKE '%index.php?option=com_joomla_lms%' AND type = 'component' AND published = 1 LIMIT 1";	
									
				$db->setQuery($query);
				$temp_itemid = $db->loadResult();
				if(!$temp_itemid) {
					$temp_itemid = 0;
				}
			}
			$lms_itemid = $temp_itemid;
		}
		return $lms_itemid;
	}	

	function &get($name, $default = null) {
		$str_var = 'lms_cfg_'.$name;
		if ($name == 'live_site') {
			//live_ite need to be before "if(isset)" : because it is set in joomla configuratino to blank string
			$live_site = substr_replace(JURI::root(), '', -1, 1);
			$this->config_vars[$str_var] = $live_site;
			return $live_site;
		} elseif ( isset($this->config_vars[$str_var]) ){
			return $this->config_vars[$str_var];
		} elseif ($name == 'Itemid') {			 
			$jlmsItemid = $this->getItemid();			
			return $jlmsItemid;
		} elseif ($name == 'option') {
			$jlmsOption = 'com_joomla_lms';
			return $jlmsOption;
		} elseif ($name == 'absolute_path') {
			$abs_path = JPATH_SITE;		
			return $abs_path;			
		} elseif ($name == 'list_limit') {
			$list_limit_var = 25;
			return $list_limit_var;
		} elseif ($name == 'plugin_lpath_forum') {
			$query = "SELECT count(*) FROM #__lms_forums WHERE published = 1 AND forum_level = 1";
			$this->_db->SetQuery($query);
			$lpath_forums = $this->_db->LoadResult() ? true : false;
			$this->config_vars[$str_var] = $lpath_forums;
			return $lpath_forums;
		}
		return $default;
	}

	function &getCfg($name, $default = null) {
		return $this->get($name, $default);
	}

	function &getByPrefix($prefix, $default = null) {
		$length = strlen($prefix);
		$result = null;
		foreach ($this->config_vars as $key=>$value) {
			$str_var = $key;
			if( substr($str_var, 8, $length) == $prefix){
				if ($value){
					$result[substr($key,8)] = $value;
				}
			}	
		}
		if (is_array($result)){
			return $result;
		}else{
			return $default;
		}	
	}

	function set($name, $value) {
		$str_var = 'lms_cfg_'.$name;
		$this->config_vars[$str_var] = $value;
		return $value;
	}
	
	function getVersionToken(){
		$version_token = '10102011';
		return $version_token;
	}
}
?>