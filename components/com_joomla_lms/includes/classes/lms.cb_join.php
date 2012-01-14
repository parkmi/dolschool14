<?php 

defined( '_JLMS_EXEC' ) or die( 'Restricted access' );

class JLMSCBJoin
{
	function &getASSOC($field,$usr=null,$db=null)
	{
		static $assoc_ar;
		ob_start();
		if($db===null) {global $JLMS_DB; $db = &$JLMS_DB;}
		if($usr===null) {global $my; $usr = $my->id;}
		if(isset($assoc_ar[$field]) && is_object($assoc_ar[$field])) {
			
		} else {
			$assoc_ar[$field] = JLMSCBJoin::_createASSOC($field,$usr,$db);
		}
		ob_end_clean();
		return $assoc_ar[$field];
		
		
	}
	function &_createASSOC($field_str,$usr,&$JLMS_DB)
	{
		global $JLMS_CONFIG;
		if ($field_str == 'name') {
			$query = "SELECT `name` FROM #__users WHERE id = '".intval($usr)."'";;
			$JLMS_DB->setQuery($query);
			$ret_str = $JLMS_DB->loadResult();
			return $ret_str;
		}
		$is_cb = false;
		$is_juser = false;
		if (file_exists(_JOOMLMS_FRONT_HOME."/../com_juser/juser.php") && $JLMS_CONFIG->get('juser_integration',0)) {
			$is_juser = true;
		}
		if (file_exists(_JOOMLMS_FRONT_HOME."/../com_comprofiler/comprofiler.php")) {
			$is_cb = true;
		}
		$ret_str = '';
		$assoc[0] = array('lms_cb_address','lms_cb_city','lms_cb_state','lms_cb_pcode','lms_cb_country','lms_cb_phone','lms_cb_location','lms_cb_website','lms_cb_icq','lms_cb_aim','lms_cb_yim','lms_cb_msn','lms_cb_company');
		$sel_array = array('jlms_cb_address','jlms_cb_city','jlms_cb_state','jlms_cb_postal_code','jlms_cb_country','jlms_cb_phone','jlms_cb_location','jlms_cb_website','jlms_cb_icq','jlms_cb_aim','jlms_cb_yim','jlms_cb_msn','jlms_cb_company');
		$juser_array = array('jlms_juser_address','jlms_juser_city','jlms_juser_state','jlms_juser_postal_code','jlms_juser_country','jlms_juser_phone','jlms_juser_location','jlms_juser_website','jlms_juser_icq','jlms_juser_aim','jlms_juser_yim','jlms_juser_msn','jlms_juser_company');
		if ($is_cb || $is_juser) {
			if(in_array($field_str,$assoc[0]))
			{
				for ($i=0;$i<count($assoc[0]);$i++)
				{	
					if($assoc[0][$i] == $field_str )	
					{
						if ($is_cb) {
							$query = "SELECT f.name as name FROM #__lms_config as c, #__comprofiler_fields as f";
							$query .= "\n WHERE c.lms_config_value = f.fieldid AND c.lms_config_var = '".$sel_array[$i]."'";
							$JLMS_DB->setQuery($query);
							$assocname = $JLMS_DB->loadResult();
							if($assocname)
							{
								$query = "SELECT `".$assocname."` FROM #__comprofiler";
								$query .= "\n WHERE user_id = '".$usr."'";
								$JLMS_DB->setQuery($query);
								$ret_str = $JLMS_DB->loadResult();
							}
						} elseif ($is_juser) {
							$query = "SELECT c.lms_config_value FROM #__lms_config as c";
							$query .= "\n WHERE c.lms_config_var = '".$juser_array[$i]."'";
							$JLMS_DB->setQuery($query);
							$assoc_fid = $JLMS_DB->loadResult();
							if($assoc_fid)
							{
								$query = "SELECT `uvalue` FROM #__users_extended_data";
								$query .= "\n WHERE user_id = '".$usr."' ANd field_id = $assoc_fid";
								$JLMS_DB->setQuery($query);
								$ret_str = $JLMS_DB->loadResult();
							}
							//var_dump($assoc_fid);die;
						}
					}
				}
			}
			else 
			{
				if ($is_cb) {
					$query = "SELECT cb_field_id FROM #__lms_cb_assoc WHERE cb_assoc = '".$field_str."'";
					$JLMS_DB->setQuery($query);
					$cb_field = $JLMS_DB->loadResult();
					$query = "SELECT name FROM  #__comprofiler_fields";
					$query .= "\n WHERE fieldid = '".$cb_field."'";
					$JLMS_DB->setQuery($query);
					$assocname = $JLMS_DB->loadResult();
					if($assocname)
					{
						$query = "SELECT `".$assocname."` FROM #__comprofiler";
						$query .= "\n WHERE user_id = '".$usr."'";
						$JLMS_DB->setQuery($query);
						$ret_str = $JLMS_DB->loadResult();
					}
				} elseif ($is_juser) {
					//nothing here yet
				}
			}
		}
		return $ret_str;
		
	}
	function &getArrays($field,$usr=null,$db=null)
	{
		static $assoc_ar;
		ob_start();
		if($db===null) {global $JLMS_DB; $db = &$JLMS_DB;}
		if($usr===null) {global $my; $usr = $my->id;}
		if(!is_object($assoc_ar[$field]))
		{
			$assoc_ar[$field] = JLMSCBJoin::_createArrays($field,$usr,$db);
		}
		ob_end_clean();
		return $assoc_ar[$field];
		
		
	}
	
	function &_createArrays($field_str,$usr,&$JLMS_DB)
	{
	
		$ret_str = '';
		$assoc[0] = array('lms_cb_address','lms_cb_city','lms_cb_state','lms_cb_pcode','lms_cb_country','lms_cb_phone','lms_cb_location','lms_cb_website','lms_cb_icq','lms_cb_aim','lms_cb_yim','lms_cb_msn');
		$sel_array = array('jlms_cb_address','jlms_cb_city','jlms_cb_state','jlms_cb_postal_code','jlms_cb_country','jlms_cb_phone','jlms_cb_location','jlms_cb_website','jlms_cb_icq','jlms_cb_aim','jlms_cb_yim','jlms_cb_msn');
		if(in_array($field_str,$assoc[0]))
		{
			for ($i=0;$i<count($assoc[0]);$i++)
			{	
				if($assoc[0][$i] == $field_str )	
				{
					$query = "SELECT f.name as name FROM #__lms_config as c, #__comprofiler_fields as f";
					$query .= "\n WHERE c.lms_config_value = f.fieldid AND c.lms_config_var = '".$sel_array[$i]."'";
					$JLMS_DB->setQuery($query);
					$assocname = $JLMS_DB->loadResult();
					if($assocname)
					{
						$query = "SELECT `".$assocname."` FROM #__comprofiler";
						$query .= "\n WHERE user_id IN (".$usr.")";
						$JLMS_DB->setQuery($query);
						$ret_str = $JLMS_DB->loadResultArray();
					}
				}
			}
		}
		else 
		{
			$query = "SELECT cb_field_id FROM #__lms_cb_assoc WHERE cb_assoc = '".$field_str."'";
			$JLMS_DB->setQuery($query);
			$cb_field = $JLMS_DB->loadResult();
			$query = "SELECT name FROM  #__comprofiler_fields";
				$query .= "\n WHERE fieldid = '".$cb_field."'";
				$JLMS_DB->setQuery($query);
				$assocname = $JLMS_DB->loadResult();
				if($assocname)
				{
					$query = "SELECT `".$assocname."` FROM #__comprofiler";
					$query .= "\n WHERE user_id = (".$usr.")";
					$JLMS_DB->setQuery($query);
					$ret_str = $JLMS_DB->loadResultArray();
				}
		}
		return $ret_str;
		
	}
	
	function &get_Assocarray()
	{
		global $JLMS_DB;
		$assoc = array('lms_cb_address','lms_cb_city','lms_cb_state','lms_cb_pcode','lms_cb_country','lms_cb_phone','lms_cb_location','lms_cb_website','lms_cb_icq','lms_cb_aim','lms_cb_yim','lms_cb_msn');
		$query = "SELECT * FROM #__lms_cb_assoc ";
			$JLMS_DB->setQuery($query);
			$new_rows = $JLMS_DB->LoadObjectList();
			for($i=0;$i<count($new_rows);$i++)
			{
				$assoc[] = $new_rows[$i]->cb_assoc;
			}
		return $assoc;		
	}
}
?>