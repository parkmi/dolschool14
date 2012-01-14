<?php
/**
* smf.php
* JoomlaLMS Component
* * * ElearningForce Inc.
**/

// no direct access
defined( '_JLMS_EXEC' ) or die( 'Restricted access' );

class JLMS_SMF 
{
	var $smf_db = null;
		
	var $boardurl = null;
		
	var $cookiename = null;	
	
	var $mbname = null;
	
	function JLMS_SMF() 
	{		
		$this->_createDBO();		 
	}
	
	function &getInstance()
	{
		$JLMS_CONFIG = & JLMSFactory::GetConfig();
				
		static $instance;

		if ( !is_object( $instance ) && file_exists( $JLMS_CONFIG->get('forum_path').'/Settings.php' ) )
		{	
			$smf_version = '1';
			
			if( file_exists( $JLMS_CONFIG->get('forum_path').'/Sources/ScheduledTasks.php' ) ) {					
				$smf_version = '2';
			}
			
			if( $smf_version == '1' ) 
			{
				require_once( JPATH_SITE.DS.'components'.DS.'com_joomla_lms'.DS.'includes'.DS.'forums'.DS.'smf'.DS.'smf_v1.php');			
				$instance = new JLMS_SMF_V1();				
								
			} else if( $smf_version == '2' ) 
			{
				require_once( JPATH_SITE.DS.'components'.DS.'com_joomla_lms'.DS.'includes'.DS.'forums'.DS.'smf'.DS.'smf_v2.php');			
				$instance = new JLMS_SMF_V2();								
			}			
		}

		return $instance;
	}
	
	function getBoardURL() 
	{
		return $this->boardurl;		
	}
	
	function getCookieName() 
	{
		return $this->cookiename;		
	}
	
	function getMbname() 
	{
		return $this->mbname;
	}
	
	function _createDBO()
	{
		$JLMS_CONFIG = & JLMSFactory::GetConfig();
		
		jimport('joomla.database.database');
		//jimport( 'joomla.database.table' );
		
		$conf =& JFactory::getConfig();		
		$driver 	= $conf->getValue('config.dbtype');
		$debug 	= $conf->getValue('config.debug');
		
		require( $JLMS_CONFIG->get('forum_path').'/Settings.php' );	
		
		$this->boardurl = $boardurl;
		$this->cookiename = $cookiename;
		
		$options	= array ( 'driver' => $driver, 'host' => $db_server, 'user' => $db_user, 'password' => $db_passwd, 'database' => $db_name, 'prefix' => $db_prefix );

		$this->smf_db  =& JDatabase::getInstance( $options );
		
		if ( JError::isError( $this->smf_db ) ) {
			jexit('Database Error: ' . $this->smf_db->toString() );
		}

		if ($this->smf_db->getErrorNum() > 0) {
			JError::raiseError(500 , 'JDatabase::getInstance: Could not connect to database <br />' . 'joomla.library:'.$db->getErrorNum().' - '.$this->smf_db->getErrorMsg() );
		}

		$this->smf_db->debug( $debug );		
	} 
	
	function md5Hmac($data, $key)
	{
		$key = str_pad(strlen($key) <= 64 ? $key : pack('H*', md5($key)), 64, chr(0x00));
		return md5(($key ^ str_repeat(chr(0x5c), 64)) . pack('H*', md5(($key ^ str_repeat(chr(0x36), 64)) . $data)));
	}
	
	function setLoginCookieUN($id, $userdata, $password = '') {			
		$password = sha1(sha1(strtolower($userdata->member_name) . $password) . $userdata->password_salt );
			
		$data = serialize(empty($id) ? array(0, '', 0) : array($id, $password, time() , 0));
		$cookie_url = $this->urlParts();
				
		// Set the cookie, $_COOKIE, and session variable.
		if( $cookie_url[0]  == 'localhost' ) 
		{
			setcookie($this->cookiename, $data, time() + (60*60*24*365), $cookie_url[1]);
		} else {
			setcookie($this->cookiename, $data, time() + (60*60*24*365), $cookie_url[1], $cookie_url[0], 0);	
		}		
		// cookies are for 1 year... to force 'remember me' to avoid double-login	
					
		$_COOKIE[$this->cookiename] = $data;
		$_SESSION['login_' . $this->cookiename] = $data;
	}
	
	function urlParts()
	{
		// Parse the URL with PHP to make life easier.
		$parsed_url = parse_url($this->boardurl);
		if ( isset($parsed_url['port']) )
			$parsed_url['host'] .= ':' . $parsed_url['port'];
		
		// Is local cookies off?
		if ( empty($parsed_url['path']) )
			$parsed_url['path'] = '';
	
		// Globalize cookies across domains (filter out IP-addresses)?
		if ( !preg_match('~^\d{1,3}(\.\d{1,3}){3}$~', $parsed_url['host']) )
		{
			// If we can't figure it out, just skip it.
			if (preg_match('~(?:[^\.]+\.)?([^\.]{2,}\..+)\z~i', $parsed_url['host'], $parts) == 1)
				$parsed_url['host'] = '.' . $parts[1];
		}				
	
		return array($parsed_url['host'], '/');
	}
	
	function getSomething() {
		static $instance;
		static $is_already_populated;
		if ($is_already_populated === true) {
			return $instance;
		} else {
			//populate !!!!!!!!!!!!!!
			$is_already_populated = true;
			return $instance;
		}
	}
	
	function populateCourseForums( $course_id, &$user_forums, &$all_forums, &$type) {
		$is_ex = false;
		foreach ($all_forums as $af) {
			$af->item_title = '';
			if ($af->board_type == $type->id) {
				$is_ex = true;
				if ($af->is_active) {
					$user_forums[] = clone($af);
				}
			}
		}
		/* course forums are created in the course new/edit interface  - there is no need to create them here*/
		if (!$is_ex) { 
			$new_forum = clone($type);
			$new_forum->id = 0;
			$new_forum->item_title = '';			
			$new_forum->group_id = 0;
			$new_forum->course_id = $course_id;
			$new_forum->board_type = $type->id;
			$new_forum->id_cat = 0;
			$new_forum->id_group = 0;
			$new_forum->id_board = 0;
			$user_forums[] = $new_forum;
		}
	}
	
	function populateLgroupForums( $course_id, &$user_forums, &$all_forums, &$type) {
		global $my, $JLMS_DB;
			
		$JLMS_ACL = & JLMSFactory::getACL();
		if ($JLMS_ACL->isTeacher()) {
			$query = "SELECT distinct id, ug_name FROM #__lms_usergroups WHERE course_id = '".$course_id."' ORDER BY ug_name";
		} else {
			$query = "SELECT distinct a.id, a.ug_name FROM #__lms_usergroups as a, #__lms_users_in_groups as b WHERE a.course_id = '".$course_id."' AND a.course_id = b.course_id AND a.id = b.group_id AND b.user_id = ".$my->id." ORDER BY a.ug_name";
		}
		$JLMS_DB->SetQuery( $query );
		$user_groups = $JLMS_DB->LoadObjectList('id');
		$user_groups_ids = array();
		foreach ($user_groups as $ug) {
			$user_groups_ids[] = $ug->id;
		}	
		$is_ex = false;
		if (count($user_groups)) {
			foreach ($all_forums as $af) {
				$af->item_title = '';
				if ($af->board_type == $type->id) {
					$is_ex = true;
					if ($af->is_active && in_array($af->group_id, $user_groups_ids)) {
						$af->item_title = isset($user_groups[$af->group_id]->ug_name) ? $user_groups[$af->group_id]->ug_name : '';
						$user_forums[] = clone($af);
					}
				}
			}
		}
		/* local groups forums are created in the course new/edit interface  - there is no need to create them here*/
		/*if (!$is_ex) {
			$new_forum = clone($type);
			$new_forum->id = 0;
			$new_forum->board_type = $type->id;
			$user_forums[] = $new_forum;
		}*/
	}
	
	function populateGgroupForums( $course_id, &$user_forums, &$all_forums, &$type) {
		global $my, $JLMS_DB;
		$JLMS_ACL = & JLMSFactory::getACL();
		
		if ($JLMS_ACL->isTeacher()) {
			
			$groups_where_admin_manager = "'0'";
			if($JLMS_ACL->CheckPermissions('advanced', 'assigned_groups_only')) {
				$query = "SELECT a.group_id FROM `#__lms_user_assign_groups` as a WHERE a.user_id = '".$my->id."' group by a.group_id"
				;
				$JLMS_DB->setQuery($query);
				$groups_where_admin_manager = $JLMS_DB->loadResultArray();
				
				if(count($groups_where_admin_manager) == 1) {
					$filt_group = $groups_where_admin_manager[0];
				}
				
				$groups_where_admin_manager = implode(',', $groups_where_admin_manager);
				
				if($groups_where_admin_manager == '') {
					$groups_where_admin_manager = "'0'";
				}
			}
			
			$query = "SELECT distinct ug.id, ug.ug_name"
			."\n FROM #__lms_users_in_groups AS uig, #__lms_users_in_global_groups AS uigg, #__lms_usergroups AS ug"
			."\n WHERE uig.course_id = $course_id"
			."\n AND ug.group_forum = 1"
			."\n AND uig.user_id = uigg.user_id"
			."\n AND ug.id = uigg.group_id AND ug.course_id = 0"
			. ($JLMS_ACL->CheckPermissions('advanced', 'assigned_groups_only') ? ("\n AND ug.id IN ($groups_where_admin_manager)") :'')
			;			
	
		} else {
			$query = "SELECT distinct ug.id, ug.ug_name FROM #__lms_usergroups AS ug, #__lms_users_in_global_groups as a WHERE ug.id = a.group_id AND a.user_id = $my->id AND ug.course_id = 0 AND ug.group_forum = 1";
		}
		$JLMS_DB->SetQuery( $query );
		$user_groups = $JLMS_DB->LoadObjectList('id');
		
		
		$user_groups_ids = array();
		foreach ($user_groups as $ug) {
			$user_groups_ids[] = $ug->id;
		}
		$groups_ex = array();
		if (count($user_groups)) {
			foreach ($all_forums as $af) {
				$af->item_title = '';
				if ($af->board_type == $type->id) {
					if ($af->is_active && in_array($af->group_id, $user_groups_ids)) {
						$af->item_title = isset($user_groups[$af->group_id]->ug_name) ? $user_groups[$af->group_id]->ug_name : '';
						$user_forums[] = clone($af);
						$groups_ex[] = $af->group_id;
					}
				}
			}
		}
		// we need to create missing global groups forums
		if (count($groups_ex) < count($user_groups)) {
			foreach ($user_groups as $ug) {				
				if (!in_array($ug->id,$groups_ex)) {
					$new_forum = clone($type);
					$new_forum->id = 0;
					$new_forum->group_id = $ug->id;
					$new_forum->board_type = $type->id;
					$new_forum->item_title = $ug->ug_name;
					$new_forum->id_cat = 0;
					$new_forum->id_group = 0;
					$new_forum->id_board = 0;
					$user_forums[] = $new_forum;
				}
			}
		}
	}
	
	function populateLpathForums( $course_id, &$user_forums, &$all_forums, &$type){
		global $my, $JLMS_DB;
		$JLMS_ACL = & JLMSFactory::getACL();
		if ($JLMS_ACL->isTeacher()) {
			$query = "SELECT a.*"
			. "\n FROM #__lms_learn_paths as a"
			. "\n WHERE a.course_id = '".$course_id."' AND a.lp_params like '%add_forum=1%'"
			. "\n ORDER BY a.ordering";
			$JLMS_DB->SetQuery( $query );
			$user_lpaths = $JLMS_DB->LoadObjectList();
			$user_lpaths_ids = array();
			foreach ($user_lpaths as $ul) {
				$user_lpaths_ids[] = $ul->id;
			}
		} else {
			/* Get list of Published LPaths and check access rights to them (i.e. access is restricted by prerequisites) */
			$query = "SELECT a.*, '' as r_status, '' as r_start, '' as r_end"
			. "\n FROM #__lms_learn_paths as a"
			. "\n WHERE a.course_id = '".$course_id."'"
			//. "\n AND a.published = 1"
			. "\n ORDER BY a.ordering";
			$JLMS_DB->SetQuery( $query );
			$lpaths = $JLMS_DB->LoadObjectList();
	
			require_once(_JOOMLMS_FRONT_HOME . "/includes/lms_grades.lib.php");
			$user_ids = array();
			$user_ids[] = $my->id;
			JLMS_LP_populate_results($course_id, $lpaths, $user_ids);
	
			// 13 August 2007 (DEN) Check for prerequisites.
			// 1. get the list of lpath_ids.
			$lpath_ids = array();
			foreach ($lpaths as $lpath) {
				$lpath_ids[] = $lpath->id;
			}
			if (!empty($lpath_ids)) {
				$lpath_ids_str = implode(',', $lpath_ids);
				// 2. get the list of prerequisites
				// SELECT from two tables (+ #__lms_learn_paths) - because the prereq lpath could be deleted...
				$query = "SELECT a.* FROM #__lms_learn_path_prerequisites as a, #__lms_learn_paths as b"
				. "\n WHERE a.lpath_id IN ($lpath_ids_str) AND a.req_id = b.id";
				$JLMS_DB->SetQuery($query);
				$prereqs = $JLMS_DB->LoadObjectList();
				if (!empty($prereqs)) {
					// 3. compare lists of prereqs to the lists of lpaths.
					$i = 0;
					while ($i < count($lpaths)) {
						$is_hidden = false;
						$o = 0;
						while ($o < count($prereqs)) {
							if ($prereqs[$o]->lpath_id == $lpaths[$i]->id) {
								$j = 0;
								while ($j < count($lpaths)) {
									if ($lpaths[$j]->id == $prereqs[$o]->req_id) {
										if (!$lpaths[$j]->item_id) {
											if (empty($lpaths[$j]->r_status)) {
												$is_hidden = true;
												break;
											} else {
												$end_time = strtotime($lpaths[$j]->r_end);
												$current_time = strtotime(date("Y-m-d H:i:s"));
												if($current_time > $end_time && (($current_time - $end_time) < ($prereqs[$o]->time_minutes*60))){
													$is_hidden = true;
													break;	
												}
											}
										} else {
											if (empty($lpaths[$j]->s_status)) {
												$is_hidden = true;
												break;
											} else {
												$end_time = strtotime($lpaths[$j]->r_end);
												$current_time = strtotime(date("Y-m-d H:i:s"));
												if($current_time > $end_time && (($current_time - $end_time) < ($prereqs[$o]->time_minutes*60))){
													$is_hidden = true;
													break;	
												}
											}
										}
									}
									$j ++;
								}
							}
							$o ++;
						}
						if ($is_hidden) {
							$lpaths[$i]->published = 0;
						}
						$i ++;
					}
				}
			}
			$user_lpaths = array();
			$user_lpaths_ids = array();
			foreach ($lpaths as $lp) {
				if ($lp->published) {
					$pos = strpos($lp->lp_params, 'add_forum=1');
					if ($pos === false) {
					} else { // forum is allowed for this lpath
						$rrr = new stdClass();
						$rrr = clone($lp);
						$user_lpaths[] = $rrr;
						$user_lpaths_ids[] = $rrr->id;
					}
				}
			}
		}
	
		$groups_ex = array();
		if (count($user_lpaths)) {
			foreach ($all_forums as $af) {
				$af->item_title = '';
				if ($af->board_type == $type->id) {
					if ($af->is_active && in_array($af->group_id, $user_lpaths_ids)) {
						foreach ($user_lpaths as $ulp_item) {
							if ($ulp_item->id == $af->group_id) {
								$af->item_title = $ulp_item->lpath_name;
								break;
							}
						}
						$user_forums[] = clone($af);
						$groups_ex[] = $af->group_id;
					}
				}
			}
		}
		// we need to create missing lpaths forums
		if (count($groups_ex) < count($user_lpaths)) {
			foreach ($user_lpaths as $ul) {
				if (!in_array($ul->id,$groups_ex)) {
					$new_forum = clone($type);
					$new_forum->id = 0;
					$new_forum->group_id = $ul->id;
					$new_forum->board_type = $type->id;
					$new_forum->item_title = $ul->lpath_name;
					$new_forum->id_cat = 0;
					$new_forum->id_group = 0;
					$new_forum->id_board = 0;
					$user_forums[] = $new_forum;
				}
			}
		}
	} 
}

class SMFTable extends JTable 
{		
	function storeAdapter( $fields, $markers ) 
	{			
		$keys = array_keys( $fields );
		$res = array();
				
		foreach( $keys AS $key ) 
		{
			if( isset($markers[$key])) 
			{
				$res[$markers[$key]] = $fields[$key];  
			}
		}	 	 	
		
		return $res; 
	}
	
	function loadAdapter( $objs, $markers )
	{					
		$markers_f = array_flip( $markers );
			
		if( empty( $objs ) ) return $objs;
						
		if( is_array($objs) ) 
		{
			$vars = get_object_vars($objs[0]);
			$keys = array_keys( $vars );		
						
			for( $i = 0; $i < count( $objs ); $i++ ) 
			{
				foreach( $keys AS $key ) 
				{
					if( isset($markers_f[$key]) ) 
					{
						$objs[$i]->{$markers_f[$key]} = $vars[$key];
					}	
				}				 
			}
			
		} else {			
			$vars = get_object_vars($objs);
			$keys = array_keys( $vars );
									
			foreach( $keys AS $key ) 
			{
				if( isset($markers_f[$key]) ) 
				{					
					$objs->{$markers_f[$key]} = $vars[$key];
				}	
			}						 
		}
		
		return $objs;		
	}	
}
