<?php
/**
* smf_v2.php
* JoomlaLMS Component
* * * ElearningForce Inc.
**/

// no direct access
defined( '_JLMS_EXEC' ) or die( 'Restricted access' );

require_once( JPATH_SITE.DS.'components'.DS.'com_joomla_lms'.DS.'includes'.DS.'forums'.DS.'smf'.DS.'smf.php');

class JLMS_SMF_V2 extends JLMS_SMF
{	
	function registerOnForum( $user, $pass, $groups, $cb_info = array() ) {
		global $my;
		
		$email = $user->email;
		$name  = $user->name;
		$ip = $_SERVER['REMOTE_ADDR'];
		
		$password = $this->password( $user->username, $pass );
		
		$storeData = array();	
		$storeData['member_name'] = $user->username;
		$storeData['date_registered'] = time();
		$storeData['real_name'] = $name;
		$storeData['passwd'] = $password;
		$storeData['email_address'] = $email;
		$storeData['pm_email_notify'] = 1;
		$storeData['show_online'] = 1;
		$storeData['notify_announcements'] = 1;
		$storeData['notify_types'] = 2;
		$storeData['member_ip'] = $ip;
		$storeData['member_ip2'] = $ip;
		$storeData['is_activated'] = 1;
		$storeData['additional_groups'] = $groups;
		$storeData['id_post_group'] = 4;
		$storeData['password_salt'] = $this->passwordSalt();				
		$storeData = array_merge( $storeData, $cb_info );			
		
		return $this->storeMember( $storeData );
	}
	
	function password( $username, $password ) 
	{
		return sha1(strtolower($username) .$password);
	}
	
	function passwordSalt() 
	{
		return substr(md5(rand()), 0, 4);
	}
	
	function storeMember( $data ) 
	{		
		$member = new JLMS_SMF_member( $this->smf_db );
		foreach ( $data as $key => $value ){
			if ($key == 'website') {
				$key = 'website_url';
				if (!ereg('/(http://)/', $value)){
					$value = 'http://'.$value;
				}		
			}				
			$member->$key = $value;
		}
						
		$member->store();	
		
		return $member->id_member; 
	}	
	
	function storeCategory( $data ) 
	{
		$category = new JLMS_SMF_category( $this->smf_db );
		foreach ( $data as $key => $value ){								
			$category->$key = $value;
		}
		
		if( !$category->id_cat ) {
			$query = "SELECT MAX(cat_order) FROM `#__categories` ";
			$this->smf_db->setQuery($query);
			$max_order = $this->smf_db->loadResult();
			
			if( $max_order )			
				$category->cat_order = ($max_order+1);
		}
						
		$category->store();
		
		return $category->id_cat;
	}
	
	function storeMemberGroup( $data ) 
	{		
		$membergroup = new JLMS_SMF_membergroup( $this->smf_db );
		foreach ( $data as $key => $value ){						
			$membergroup->$key = $value;
		}		
		
		$membergroup->store();
		
		return $membergroup->id_group; 
	}
	
	function storeBoard( $data ) 
	{		
		$board = new JLMS_SMF_board( $this->smf_db );
		foreach ( $data as $key => $value ){						
			$board->$key = $value;
		}		
		
		$board->store();
		
		return $board->id_board; 
	}	
	
	function loadMemberByName( $name ) 
	{		
		$query = "SELECT * FROM `#__members` WHERE LOWER(member_name) = '".strtolower( $name )."' LIMIT 1";			
		$this->smf_db->setQuery( $query );
		return $this->smf_db->loadObject();		
	}
	
	function loadBoard( $id ) 
	{
		$board = new JLMS_SMF_board( $this->smf_db );			
		return $board->load( $id );
	}
	
	function loadCategory( $id ) 
	{
		$category = new JLMS_SMF_category( $this->smf_db );			
		return $category->load( $id );
	}
	
	function setLoginCookie15( $id, $password = '' )
	{
		//setcookie( $this->cookiename, 0 ,0 , '/' );
				
		$query = "SELECT member_name, password_salt FROM `#__members` WHERE id_member  = '$id' ";
		$this->smf_db->setQuery($query);
		$userdata = $this->smf_db->loadObject();		
		if (isset($userdata->member_name)) {									
			$this->setLoginCookieUN($id, $userdata, $password);
		}	
	}
	
	function create( &$give_course_forums, $i, $course_name, $owner_name, &$all_moderators, $id_cat) {			
		$forum_name = $give_course_forums[$i]->forum_name;
		$forum_desc = $give_course_forums[$i]->forum_desc;
		$forum_name = str_replace('{course_name}', $course_name, $forum_name);
		$forum_name = str_replace('{group_name}', $give_course_forums[$i]->item_title, $forum_name);
		$forum_name = str_replace('{lpath_name}', $give_course_forums[$i]->item_title, $forum_name);
		$forum_desc = str_replace('{course_name}', $course_name, $forum_desc);
		$forum_desc = str_replace('{group_name}', $give_course_forums[$i]->item_title, $forum_desc);
		$forum_desc = str_replace('{lpath_name}', $give_course_forums[$i]->item_title, $forum_desc);
	
		$storeData = array();
		$storeData['group_name'] = $forum_name;
		$storeData['min_posts'] = -1;
		$storeData['max_messages'] = 0;
		$storeData['stars'] = '1#star.gif';
		
		$id_group = $this->storeMemberGroup( $storeData );
	
		$parent_id = 0;
		if ($give_course_forums[$i]->parent_forum) {
			for ($j = 0; $j < count($give_course_forums); $j++) {
				if ($give_course_forums[$j]->board_type == $give_course_forums[$i]->parent_forum) {
					if (!$give_course_forums[$i]->group_id || ($give_course_forums[$i]->group_id == $give_course_forums[$j]->group_id)) {
						if (isset($give_course_forums[$j]->id_board) && $give_course_forums[$j]->id_board) {
							$parent_id = $give_course_forums[$j]->id_board;
						}
					}
				}
			}
		}
		
		$storeData = array();
		$storeData['id_cat'] = $id_cat;
		$storeData['id_parent'] = $parent_id;
		$storeData['member_groups'] = $id_group;
		$storeData['name'] = $forum_name;
		$storeData['description'] = $forum_desc;
		
		$id_board = $this->storeBoard( $storeData );
	
		$keys = array( 'post_new', 'poll_view' ,'post_reply_own', 'post_reply_any', 'delete_own', 'modify_own', 'mark_any_notify', 'mark_notify','moderate_board', 'report_any', 'send_topic', 'poll_vote', 'poll_edit_own',	'poll_post', 'poll_add_own', 'post_attachment', 'lock_own', 'remove_own', 'view_attachments' );
			
		$this->insertBoardPermissions( $id_group, $keys );	
	
		if ($give_course_forums[$i]->moderated && $owner_name) {
			$this->insertModerator( $id_board, $owner_name );
		}
	
		if ($give_course_forums[$i]->forum_moderators) {
			$curr_forum_moders = explode(',', $give_course_forums[$i]->forum_moderators);
			for ($i = 0; $i < count($curr_forum_moders); $i++) {
				$curr_forum_moders[$i] = intval($curr_forum_moders[$i]);
			}
			$curr_forum_moders = array_unique($curr_forum_moders);
			if (!empty($curr_forum_moders)) {
				foreach ($curr_forum_moders as $cfm) {
					if (isset($all_moderators[$cfm]) && $all_moderators[$cfm]->username) {
						$this->insertModerator( $id_board, $all_moderators[$cfm]->username );
					}
				}
				
			}
		}
	
		$give_course_forums[$i]->id_cat = $id_cat;
		$give_course_forums[$i]->id_group = $id_group;
		$give_course_forums[$i]->id_board = $id_board;
		$tmp = new stdClass();
		$tmp->group_id = $give_course_forums[$i]->group_id;
		$tmp->id_cat = $id_cat;
		$tmp->id_group = $id_group;
		$tmp->id_board = $id_board;
		$tmp->board_type = $give_course_forums[$i]->board_type;
		return $tmp;
	}
	
	function update( &$give_course_forums, $i, $course_name, $owner_name, &$all_moderators, $id_cat) {
		global $JLMS_DB; //should be connected to the SMF !!!!
		$forum_name = $give_course_forums[$i]->forum_name;
		$forum_desc = $give_course_forums[$i]->forum_desc;
		$forum_name = str_replace('{course_name}', $course_name, $forum_name);
		if (isset($give_course_forums[$i]->item_title)) {
			$forum_name = str_replace('{group_name}', $give_course_forums[$i]->item_title, $forum_name);
			$forum_name = str_replace('{lpath_name}', $give_course_forums[$i]->item_title, $forum_name);
		}
		$forum_desc = str_replace('{course_name}', $course_name, $forum_desc);
		if (isset($give_course_forums[$i]->item_title)) {
			$forum_desc = str_replace('{group_name}', $give_course_forums[$i]->item_title, $forum_desc);
			$forum_desc = str_replace('{lpath_name}', $give_course_forums[$i]->item_title, $forum_desc);
		}
	
		if (isset($give_course_forums[$i]->id_group) && $give_course_forums[$i]->id_group) {		
			
			$storeData = array();
			$storeData['id_group'] = $give_course_forums[$i]->id_group;
			$storeData['group_name'] = $forum_name;			
			$this->storeMemberGroup( $storeData );
					
		}
	
		$parent_id = 0;
		if ($give_course_forums[$i]->parent_forum) {
			for ($j = 0; $j < count($give_course_forums); $j++) {
				if ($give_course_forums[$j]->board_type == $give_course_forums[$i]->parent_forum) {
					if (!$give_course_forums[$i]->group_id || ($give_course_forums[$i]->group_id == $give_course_forums[$j]->group_id)) {
						if (isset($give_course_forums[$j]->id_board) && $give_course_forums[$j]->id_board) {
							$parent_id = $give_course_forums[$j]->id_board;
						}
					}
				}
			}
		}
		if (isset($give_course_forums[$i]->id_board) && $give_course_forums[$i]->id_board) {
			
			$storeData = array();			
			$storeData['id_board'] = $give_course_forums[$i]->id_board;						
			$storeData['id_parent'] = $parent_id;			
			$storeData['name'] = $forum_name;
			$storeData['description'] = $forum_desc;
			
			$this->storeBoard( $storeData );
						
			// TODO: this operation can remove moderators who was configured by SMF admin.
			$query = "DELETE FROM `#__moderators` WHERE id_board = ".$give_course_forums[$i]->id_board;
			$this->smf_db->setQuery($query);
			$this->smf_db->query();
	
			$owner_was_added = false;
			if ($give_course_forums[$i]->moderated && $owner_name) {
				$this->insertModerator( $give_course_forums[$i]->id_board, $owner_name );
				$owner_was_added = true;
			}
		
			if ($give_course_forums[$i]->forum_moderators) {
				$curr_forum_moders = explode(',', $give_course_forums[$i]->forum_moderators);
				for ($j = 0; $j < count($curr_forum_moders); $j++) {
					$curr_forum_moders[$j] = intval($curr_forum_moders[$j]);
				}
				$curr_forum_moders = array_unique($curr_forum_moders);
				if (!empty($curr_forum_moders)) {
					foreach ($curr_forum_moders as $cfm) {
						if (isset($all_moderators[$cfm]) && $all_moderators[$cfm]->username && (($owner_was_added && $all_moderators[$cfm]->username != $owner_name) || !$owner_was_added)) {
							$this->insertModerator( $give_course_forums[$i]->id_board, $all_moderators[$cfm]->username );
						}
					}
					
				}
			}
		}
	
		$give_course_forums[$i]->was_updated = 1;
		return $give_course_forums[$i]->id;
	}
	
	function deleteBoards( $ids ) 
	{
		if( isset($ids[0]) ) {
			$ids_str = implode( ',', $ids );		
			$query = "DELETE FROM `#__boards` WHERE id_board IN ($ids_str)";
			$this->smf_db->setQuery( $query );
			$this->smf_db->Query();
			
			return true;
		}
		
		return false;
	}
	
	function deleteTopics( $ids ) 
	{
		if( isset($ids[0]) ) {
			$ids_str = implode( ',', $ids );			
			$query = "DELETE FROM `#__topics` WHERE id_board IN ($ids_str)";
			$this->smf_db->setQuery( $query );
			$this->smf_db->Query();
			
			return true;
		}
		
		return false;
	}
	
	function deleteMessages( $ids ) 
	{
		if( isset($ids[0]) ) {
			$ids_str = implode( ',', $ids );			
			$query = "DELETE FROM `#__messages` WHERE id_board IN ($ids_str)";
			$this->smf_db->setQuery( $query );
			$this->smf_db->Query();
			
			return true;
		}
		
		return false;
	}
	
	function deleteModerators( $ids, $name = '' ) 
	{
		$where = '';
		if( $name ) {
			$query = "SELECT id_member FROM `#__members` WHERE LOWER(member_name) = '".strtolower( $name )."' ";
			$this->smf_db->setQuery($query);
			$mem_id = $this->smf_db->loadResult();
			$where = " AND id_member = $mem_id";
		}		
		
		if( isset($ids[0]) ) {
			$ids_str = implode( ',', $ids );			
			$query = "DELETE FROM `#__moderators` WHERE id_board IN ($ids_str)".$where;
			$this->smf_db->setQuery( $query );
			$this->smf_db->Query();
			
			return true;
		}
		
		return false;
	}
	
	function deleteMembergroups( $ids ) 
	{
		if( isset($ids[0]) ) {
			$ids_str = implode( ',', $ids );			
			$query = "DELETE FROM `#__membergroups` WHERE id_group IN ($ids_str)";
			$this->smf_db->setQuery( $query );
			$this->smf_db->Query();
			
			return true;
		}
		
		return false;
	}
	
	function deletePermissions( $ids ) 
	{
		if( isset($ids[0]) ) {
			$ids_str = implode( ',', $ids );			
			$query = "DELETE FROM `#__board_permissions` WHERE id_group IN ($ids_str)";
			$this->smf_db->setQuery( $query );
			$this->smf_db->Query();
			
			return true;
		}
		
		return false;
	}	
	
	function deleteCollapsedCategories( $memId, $catIds ) 
	{
		if( isset( $catIds[0]) ) {
			$ids_str = implode( ',', $catIds );
			$query = "DELETE FROM `#__collapsed_categories` WHERE id_member = ".$memId." AND id_cat IN ( $ids_str )";
			$this->smf_db->setQuery( $query );
			$this->smf_db->Query();
			
			return true;
		}
		
		return false;
	} 
	
	function deleteForumAccsess( $ids ) 
	{
		if( isset( $ids[0]) ) {
			$ids_str = implode( ',', $ids );
			$query = "UPDATE `#__boards` SET member_groups = '-100' WHERE id_board IN ( $ids_str )";
			$this->smf_db->setQuery($query);
			return $this->smf_db->query();
		}
		
		return false;
	}
	
	function insertCollapsedCategories( $mem_id, $rrr ) 
	{
		if( isset( $rrr[0] ) ) {
			$query = "INSERT INTO `#__collapsed_categories` ( id_cat, id_member ) VALUES ";
			$i = 0;
			$count_r = count( $rrr );
			foreach ($rrr as $rrr1) {
				$query .= "\n ('".$rrr1."', '".$mem_id."')".(($i < ($count_r - 1))?',':'');
				$i ++;
			}
			
			$this->smf_db->setQuery( $query );
			return $this->smf_db->Query();
		}
		
		return false;
	}
	
	function insertBoardPermissions( $group_id, $keys ) 
	{	
		if( isset( $keys[0] ) ) {	
			$query = "INSERT INTO `#__board_permissions` (id_group , id_profile, permission, add_deny ) VALUES ";
			$count_k = count($keys);
			for ($i = 0; $i<count($keys); $i++){
				$query .= "\n ('".$group_id."', '0' , '".$keys[$i]."', '1')".(($i < ($count_k - 1))?',':'');
			}
			
			$this->smf_db->setQuery( $query );
			return $this->smf_db->Query(); 
		}
				
		return false;
	}
	
	function insertModerator( $id_board, $name ) 
	{	
		$query = "SELECT id_member FROM `#__members` WHERE LOWER(member_name) = '".strtolower( $name )."'";
		$this->smf_db->setQuery( $query );
		$mid = $this->smf_db->LoadResult();
		
		if ($mid) {
			$query = "INSERT INTO `#__moderators` (id_board, id_member) VALUES ('".$id_board."', '".$mid."')";
			$this->smf_db->setQuery($query);
			
			return $this->smf_db->query();		 
		}
						
		return false;
	}
	
	function selectBoards() 
	{
		$query = "SELECT b.* FROM `#__categories` as a, `#__boards` as b WHERE b.id_cat = a.id_cat";
		$this->smf_db->setQuery( $query );
		return $this->smf_db->loadObjectList();		
	}
	
	function selectMembers( $ids ) 
	{
		if( isset($ids[0])) {
			$ids_str = implode( ',', $ids );
			$query = "SELECT mem.*, IFNULL(a.id_attach, 0) AS id_attach, a.filename, a.attachment_type"
			. "\n FROM `#__members` AS mem"
			. "\n LEFT JOIN `#__attachments` AS a ON (a.id_member = mem.id_member)"
			. "\n WHERE mem.id_member IN ( $ids_str )";
			$this->smf_db->setQuery( $query );
			return $this->smf_db->loadObjectList();
		}
		 
		return false;		
	}
	
	function getLatestPosts($user_id, $count=5, $board_ids=array()){
		
		$messages = array();
		
		$query = "SELECT mes.*, (SELECT mes1.subject FROM #__messages as mes1 WHERE mes1.id_topic = mes.id_topic ORDER BY mes1.id_msg ASC LIMIT 1) AS topic_name"
		. "\n FROM #__messages as mes"
		. "\n WHERE 1"
		.(count($board_ids) ? "\n AND mes.id_board IN (".implode(',', $board_ids).")" : '')
		. "\n ORDER BY mes.poster_time DESC, mes.id_msg, mes.id_topic, mes.id_board"
		;
		$this->smf_db->setQuery( $query, 0, $count );
		$messages = $this->smf_db->loadObjectList();
			
		for($i=0;$i<count($messages);$i++){
			if(isset($messages[$i]->id_board)){
				$messages[$i]->ID_BOARD = $messages[$i]->id_board;
			}
			if(isset($messages[$i]->id_msg)){
				$messages[$i]->ID_MSG = $messages[$i]->id_msg;
			}
			if(isset($messages[$i]->id_topic)){
				$messages[$i]->ID_TOPIC = $messages[$i]->id_topic;
			}
			if(isset($messages[$i]->id_group)){
				$messages[$i]->ID_GROUP = $messages[$i]->id_group;
			}
			if(isset($messages[$i]->id_cat)){
				$messages[$i]->ID_CAT = $messages[$i]->id_cat;
			}
			if(isset($messages[$i]->id_member)){
				$messages[$i]->ID_MEMBER = $messages[$i]->id_member;
			}
			if(isset($messages[$i]->poster_time)){
				$messages[$i]->posterTime = $messages[$i]->poster_time;
			}
		}
		
		return $messages;
	}
}

class JLMS_SMF_member extends JTable 
{
	var $id_member = null;
  	var $member_name = null;
  	var $date_registered = null;
  	var $posts = null;
  	var $id_group = null;
  	var $lngfile = null;
  	var $last_login = null;
  	var $real_name = null;
  	var $instant_messages = null;
  	var $unread_messages = null;
  	var $new_pm = null;
  	var $buddy_list = null;
  	var $pm_ignore_list = null;
  	var $pm_prefs = null;
  	var $mod_prefs = null;
  	var $message_labels = null;
  	var $passwd = null;
  	var $openid_uri = null;
  	var $email_address = null;
  	var $personal_text = null;
  	var $gender = null;
  	var $birthdate = null;
  	var $website_title = null;
  	var $website_url = null;
  	var $location = null;
  	var $icq = null;
  	var $aim = null;
  	var $yim = null;
  	var $msn = null;
  	var $hide_email = null;
  	var $show_online = null;
  	var $time_format = null;
  	var $signature = null;
  	var $time_offset = null;
  	var $avatar = null;
  	var $pm_email_notify = null;
  	var $karma_bad = null;
  	var $karma_good = null;
  	var $usertitle = null;
  	var $notify_announcements = null;
  	var $notify_regularity = null;
  	var $notify_send_body = null;
  	var $notify_types = null;
  	var $member_ip = null;
  	var $member_ip2 = null;
  	var $secret_question = null;
  	var $secret_answer = null;
  	var $id_theme = null;
  	var $is_activated = null;  	
  	var $validation_code = null;
	var $id_msg_last_visit = null;
  	var $additional_groups = null;
  	var $smiley_set = null;
  	var $id_post_group = null;
  	var $total_time_logged_in = null;
  	var $password_salt = null;
  	var $ignore_boards = null;
  	var $warning = null;
  	var $passwd_flood = null;
  	var $pm_receive_from = null;
	  
	function __construct( &$_db ) {
		parent::__construct( '#__members', 'id_member', $_db );
	}	
}

class JLMS_SMF_board extends JTable 
{
	var $id_board = null;
  	var $id_cat = null;
  	var $child_level = null;
  	var $id_parent = null;
  	var $board_order = null;
  	var $id_last_msg = null;
  	var $id_msg_updated = null;
  	var $member_groups = null;
  	var $id_profile = null;
  	var $name = null;
  	var $description = null;
  	var $num_topics = null;
  	var $num_posts = null;
  	var $count_posts = null;
  	var $id_theme = null;
  	var $override_theme = null;
  	var $unapproved_posts = null;
  	var $unapproved_topics = null;
  	var $redirect = null;
  	
  	function __construct( &$_db ) {
		parent::__construct( '#__boards', 'id_board', $_db );
	}
}

class JLMS_SMF_membergroup extends JTable 
{
	var $id_group = null;
  	var $group_name = null;
  	var $description = null;
  	var $online_color = null;
  	var $min_posts = null;
  	var $max_messages = null;
  	var $stars = null;
  	var $group_type = null;
  	var $hidden = null;
  	var $id_parent = null;
  	
  	function __construct( &$_db ) {
		parent::__construct( '#__membergroups', 'id_group', $_db );
	}
}

class JLMS_SMF_category extends JTable 
{
	var $id_cat = null;
  	var $cat_order = null;
  	var $name = null;
  	var $can_collapse = null;
  	
  	function __construct( &$_db ) {
		parent::__construct( '#__categories', 'id_cat', $_db );
	}
}

