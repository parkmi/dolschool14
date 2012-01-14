<?php
/**
* smf_v1.php
* JoomlaLMS Component
**/

// no direct access
defined( '_JLMS_EXEC' ) or die( 'Restricted access' );

require_once( JPATH_SITE.DS.'components'.DS.'com_joomla_lms'.DS.'includes'.DS.'forums'.DS.'smf'.DS.'smf.php');

class JLMS_SMF_V1 extends JLMS_SMF
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
						
		$data = JLMS_SMF_member::storeAdapter( $data );
								
		foreach ( $data as $key => $value ){
			if ($key == 'website') {
				$key = 'websiteUrl';
				if (!ereg('/(http://)/', $value)){
					$value = 'http://'.$value;
				}		
			}				
			$member->$key = $value;
		}
									
		$member->store();				 
		
		return $member->ID_MEMBER; 
	}	
	
	function storeCategory( $data ) 
	{
		$category = new JLMS_SMF_category( $this->smf_db );
		
		$data = JLMS_SMF_category::storeAdapter( $data );
		
		foreach ( $data as $key => $value ){								
			$category->$key = $value;
		}
		
		if( !$category->id_cat ) {
			$query = "SELECT MAX(catOrder) FROM `#__categories` ";
			$this->smf_db->setQuery($query);
			$max_order = $this->smf_db->loadResult();
			
			if( $max_order )			
				$category->catOrder = ($max_order+1);
		}
						
		$category->store();
		
		return $category->ID_CAT;
	}
	
	function storeMemberGroup( $data ) 
	{		
		$membergroup = new JLMS_SMF_membergroup( $this->smf_db );
		
		$data = JLMS_SMF_membergroup::storeAdapter( $data );
		
		foreach ( $data as $key => $value ){						
			$membergroup->$key = $value;
		}		
			
		$membergroup->store();
		
		return $membergroup->ID_GROUP; 
	}
	
	function storeBoard( $data ) 
	{		
		$board = new JLMS_SMF_board( $this->smf_db );
		
		$data = JLMS_SMF_board::storeAdapter( $data );
		
		foreach ( $data as $key => $value ){						
			$board->$key = $value;
		}		
		
		$board->store();
		
		return $board->ID_BOARD; 
	}	
	
	function loadMemberByName( $name ) 
	{		
		$query = "SELECT * FROM `#__members` WHERE LOWER(memberName) = '".strtolower( $name )."' LIMIT 1";			
		$this->smf_db->setQuery( $query );
		$member = $this->smf_db->loadObject();
				
		$member = JLMS_SMF_member::loadAdapter( $member );
		
		return $member;		
	}
	
	function loadBoard( $id ) 
	{
		$board = new JLMS_SMF_board( $this->smf_db );
		$board->load( $id );
				
		$board = JLMS_SMF_board::loadAdapter( $board );
					
		return $board;
	}
	
	function loadCategory( $id ) 
	{
		$category = new JLMS_SMF_category( $this->smf_db );			
		$category->load( $id );
				
		$category = JLMS_SMF_category::loadAdapter( $category );
				
		return $category;
	}
	
	function setLoginCookie15( $id, $password = '' )
	{
		//setcookie( $this->cookiename, 0 ,0 , '/' );				
		
		$userdata = new JLMS_SMF_member( $this->smf_db );
		$userdata->load( $id );		
		$userdata = JLMS_SMF_member::loadAdapter( $userdata );	
								
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
	
		if (isset($give_course_forums[$i]->id_group) && $give_course_forums[$i]->ID_GROUP) {
			
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
			$query = "DELETE FROM `#__moderators` WHERE ID_BOARD = ".$give_course_forums[$i]->id_board;
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
			$query = "DELETE FROM `#__boards` WHERE ID_BOARD IN ($ids_str)";
			$this->smf_db->setQuery( $query );
			return $this->smf_db->Query();			
		}
		
		return false;
	}
	
	function deleteTopics( $ids ) 
	{
		if( isset($ids[0]) ) {
			$ids_str = implode( ',', $ids );			
			$query = "DELETE FROM `#__topics` WHERE ID_BOARD IN ($ids_str)";
			$this->smf_db->setQuery( $query );
			return $this->smf_db->Query();			
		}
		
		return false;
	}
	
	function deleteMessages( $ids ) 
	{
		if( isset($ids[0]) ) {
			$ids_str = implode( ',', $ids );			
			$query = "DELETE FROM `#__messages` WHERE ID_BOARD IN ($ids_str)";
			$this->smf_db->setQuery( $query );
			return $this->smf_db->Query();			
		}
		
		return false;
	}
	
	function deleteModerators( $ids, $name = '' ) 
	{
		$where = '';
		if( $name ) {
			$query = "SELECT ID_MEMBER FROM `#__members` WHERE LOWER(memberName) = '".strtolower( $name )."' ";
			$this->smf_db->setQuery($query);
			$mem_id = $this->smf_db->loadResult();
			$where = " AND ID_MEMBER = $mem_id";
		}		
		
		if( isset($ids[0]) ) {
			$ids_str = implode( ',', $ids );			
			$query = "DELETE FROM `#__moderators` WHERE ID_BOARD IN ($ids_str)".$where;
			$this->smf_db->setQuery( $query );
			return $this->smf_db->Query();			
		}
		
		return false;
	}
	
	function deleteMembergroups( $ids ) 
	{
		if( isset($ids[0]) ) {
			$ids_str = implode( ',', $ids );			
			$query = "DELETE FROM `#__membergroups` WHERE ID_GROUP IN ($ids_str)";
			$this->smf_db->setQuery( $query );
			return $this->smf_db->Query();			
		}
		
		return false;
	}
	
	function deletePermissions( $ids ) 
	{
		if( isset($ids[0]) ) {
			$ids_str = implode( ',', $ids );			
			$query = "DELETE FROM `#__board_permissions` WHERE ID_GROUP IN ($ids_str)";
			$this->smf_db->setQuery( $query );
			return $this->smf_db->Query();
		}
		
		return false;
	}	
	
	function deleteCollapsedCategories( $memId, $catIds ) 
	{
		if( isset( $catIds[0]) ) {
			$ids_str = implode( ',', $catIds );
			$query = "DELETE FROM `#__collapsed_categories` WHERE ID_MEMBER = ".$memId." AND ID_CAT IN ( $ids_str )";
			$this->smf_db->setQuery( $query );
			return $this->smf_db->Query();			
		}
		
		return false;
	} 
	
	function deleteForumAccsess( $ids ) 
	{
		if( isset( $ids[0]) ) {
			$ids_str = implode( ',', $ids );
			$query = "UPDATE `#__boards` SET memberGroups = '-100' WHERE ID_BOARD IN ( $ids_str )";
			$this->smf_db->setQuery($query);
			return $this->smf_db->query();
		}
		
		return false;
	}
	
	function insertCollapsedCategories( $mem_id, $rrr ) 
	{
		if( isset( $rrr[0] ) ) {
			$query = "INSERT INTO `#__collapsed_categories` ( ID_CAT, ID_MEMBER ) VALUES ";
			$i = 0;
			$count_r = count( $rrr );
			foreach ($rrr as $rrr1) {
				$query .= "\n ('".$rrr1."', '".$mem_id."')".(($i < ($count_r - 1))?',':'');
				$i ++;
			}
			$this->smf_db->setQuery( $query );			
			return $this->smf_db->query();
		}
		
		return false;
	}
	
	function insertBoardPermissions( $group_id, $keys ) 
	{	
		if( isset( $keys[0] ) ) {	
			$query = "INSERT INTO `#__board_permissions` (ID_GROUP , ID_BOARD, permission, addDeny ) VALUES ";
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
		$query = "SELECT ID_MEMBER FROM `#__members` WHERE LOWER(memberName) = '".strtolower( $name )."'";
		$this->smf_db->setQuery( $query );
		$mid = $this->smf_db->LoadResult();
		
		if ($mid) {
			$query = "INSERT INTO `#__moderators` (ID_BOARD, ID_MEMBER) VALUES ('".$id_board."', '".$mid."')";
			$this->smf_db->setQuery($query);			
			return $this->smf_db->query();		 
		}
						
		return false;
	}
	
	function selectBoards() 
	{
		$query = "SELECT b.* FROM `#__categories` as a, `#__boards` as b WHERE b.ID_CAT = a.ID_CAT";
		$this->smf_db->setQuery( $query );
		$res = $this->smf_db->loadObjectList();
			
		$res = JLMS_SMF_board::loadAdapter( $res );
		$res = JLMS_SMF_category::loadAdapter( $res );	
				
		return $res;
	}
	
	function selectMembers( $ids ) 
	{
		if( isset($ids[0])) {
			$ids_str = implode( ',', $ids );
			$query = "SELECT mem.*, IFNULL(a.ID_ATTACH, 0) AS id_attach, a.filename, a.attachmentType AS attachment_type"
			. "\n FROM `#__members` AS mem"
			. "\n LEFT JOIN `#__attachments` AS a ON (a.ID_MEMBER = mem.ID_MEMBER)"
			. "\n WHERE mem.ID_MEMBER IN ( $ids_str )";
			$this->smf_db->setQuery( $query );			
			$res = $this->smf_db->loadObjectList(); 
			
			$res = JLMS_SMF_member::loadAdapter( $res );
						
			return $res;
		}
		 
		return false;		
	}
	
	function getLatestPosts($user_id, $count=5, $board_ids=array()){
		
		$messages = array();
		
		$query = "SELECT
			mes.ID_MSG AS id_msg,
			mes.ID_TOPIC AS id_topic,
			mes.ID_BOARD AS id_board,
			mes.posterTime AS poster_time,
			mes.ID_MEMBER AS id_member,
			mes.ID_MSG_MODIFIED AS id_msg_modified,
			mes.posterName AS poster_name,
			mes.posterEmail AS poster_email,
			mes.posterIP AS poster_ip,
			mes.smileysEnabled AS smileys_enabled,
			mes.modifiedTime AS modified_time,
			mes.modifiedName AS modified_name,
			mes.subject,
			mes.body,
			(SELECT mes1.subject FROM #__messages as mes1 WHERE mes1.ID_TOPIC = mes.ID_TOPIC ORDER BY mes1.ID_MSG ASC LIMIT 1) AS topic_name 	 
		"
		. "\n FROM #__messages as mes"
		. "\n WHERE 1"
		.(count($board_ids) ? "\n AND mes.ID_BOARD IN (".implode(',', $board_ids).")" : '')
		. "\n ORDER BY mes.posterTime DESC, mes.ID_MSG, mes.ID_TOPIC, mes.ID_BOARD"
		;
		$this->smf_db->setQuery( $query, 0, $count );
		$messages = $this->smf_db->loadObjectList();
		
		return $messages;
	}
}


class JLMS_SMF_member extends SMFTable 
{
	var $ID_MEMBER = null;
  	var $memberName = null;
	var $dateRegistered = null;
  	var $posts = null;
  	var $ID_GROUP = null;
  	var $lngfile = null;
  	var $lastLogin = null;
  	var $realName = null;
  	var $instantMessages = null;
  	var $unreadMessages = null;
  	var $buddy_list = null;
  	var $pm_ignore_list = null;
  	var $messageLabels = null;
  	var $passwd = null;
  	var $emailAddress = null;
  	var $personalText = null;
  	var $gender = null;
  	var $birthdate = null;
  	var $websiteTitle = null;
  	var $websiteUrl = null;
  	var $location = null;
  	var $ICQ = null;
  	var $AIM = null;
  	var $YIM = null;
  	var $MSN = null;
  	var $hideEmail = null;
  	var $showOnline = null;
  	var $timeFormat = null;
  	var $signature = null;
  	var $timeOffset = null;
  	var $avatar = null;
  	var $pm_email_notify = null;
  	var $karmaBad = null;
  	var $karmaGood = null;
  	var $usertitle = null;
  	var $notifyAnnouncements = null;
  	var $notifyOnce = null;
  	var $notifySendBody = null;
  	var $notifyTypes = null;
  	var $memberIP = null;
  	var $memberIP2 = null;
  	var $secretQuestion = null;
  	var $secretAnswer = null;
  	var $ID_THEME = null;
  	var $is_activated = null;
  	var $validation_code = null;
  	var $ID_MSG_LAST_VISIT = null;
  	var $additionalGroups = null;
  	var $smileySet = null;
  	var $ID_POST_GROUP = null;
  	var $totalTimeLoggedIn = null;
  	var $passwordSalt = null;
	  
	function __construct( &$_db ) {
		parent::__construct( '#__members', 'ID_MEMBER', $_db );
	}
	
	function getMarkers() 
	{
		$markers['id_member'] = 'ID_MEMBER';
		$markers['member_name'] = 'memberName';
		$markers['date_registered'] = 'dateRegistered';
		$markers['posts'] = 'posts'; 	 
		$markers['id_group'] = 'ID_GROUP';
		$markers['lngfile'] = 'lngfile';
		$markers['last_login'] = 'lastLogin';
		$markers['real_name'] = 'realName';
		$markers['instant_messages'] = 'instantMessages';
		$markers['unread_messages'] = 'unreadMessages';
		$markers['unread_messages'] = 'unreadMessages';
		$markers['buddy_list'] = 'buddy_list';
		$markers['pm_ignore_list'] = 'pm_ignore_list';  	
		$markers['message_labels'] = 'messageLabels';
		$markers['passwd'] = 'passwd';		
		$markers['email_address'] = 'emailAddress';
		$markers['personal_text'] = 'personalText';
		$markers['gender'] = 'gender';
		$markers['birthdate'] = 'birthdate'; 	
		$markers['website_title'] = 'websiteTitle';
		$markers['website_url'] = 'websiteUrl';
		$markers['location'] = 'location';		
		$markers['icq'] = 'ICQ';
		$markers['aim'] = 'AIM';
		$markers['yim'] = 'YIM';
		$markers['msn'] = 'MSN';
		$markers['hide_email'] = 'hideEmail';
		$markers['show_online'] = 'showOnline';
		$markers['time_format'] = 'timeFormat';				
		$markers['time_offset'] = 'timeOffset';
		$markers['karma_bad'] = 'karmaBad';
		$markers['karma_good'] = 'karmaGood';
		$markers['usertitle'] = 'usertitle';		
		$markers['notify_announcements'] = 'notifyAnnouncements';
		$markers['notify_once'] = 'notifyOnce';
		$markers['notify_send_body'] = 'notifySendBody';
		$markers['notify_types'] = 'notifyTypes';
		$markers['member_ip'] = 'memberIP';
		$markers['member_ip2'] = 'memberIP2';
		$markers['secret_question'] = 'secretQuestion';
		$markers['secret_answer'] = 'secretAnswer';
		$markers['id_theme'] = 'ID_THEME';
		$markers['is_activated'] = 'is_activated';
		$markers['validation_code'] = 'validation_code';  	
		$markers['id_msg_last_visit'] = 'ID_MSG_LAST_VISIT';
		$markers['additional_groups'] = 'additionalGroups';
		$markers['smiley_set'] = 'smileySet';
		$markers['id_post_group'] = 'ID_POST_GROUP';
		$markers['total_time_logged_in'] = 'totalTimeLoggedIn';
		$markers['password_salt'] = 'passwordSalt'; 
		
		return $markers;
	}
	
	function storeAdapter( $fields ) 
	{	
		$markers = JLMS_SMF_member::getMarkers();			
		return parent::storeAdapter( $fields, $markers );
	}
	
	function loadAdapter( $objs )
	{					
		$markers = JLMS_SMF_member::getMarkers();
		return parent::loadAdapter( $objs, $markers );		
	}	
}

class JLMS_SMF_board extends SMFTable 
{
	var $ID_BOARD = null;
  	var $ID_CAT = null;
  	var $childLevel = null;
  	var $ID_PARENT = null;
  	var $boardOrder = null;
  	var $ID_LAST_MSG = null;
  	var $ID_MSG_UPDATED = null;
  	var $memberGroups = null;
  	var $name = null;
  	var $description = null;
  	var $numTopics = null;
  	var $numPosts = null;
  	var $countPosts = null;
  	var $ID_THEME = null;
  	var $permission_mode = null;
  	var $override_theme = null;
  	
  	function __construct( &$_db ) {
		parent::__construct( '#__boards', 'ID_BOARD', $_db );
	}
	
	function getMarkers() 
	{
		$markers['id_board'] = 'ID_BOARD';
		$markers['id_cat'] = 'ID_CAT';
		$markers['child_level'] = 'childLevel';
		$markers['id_parent'] = 'ID_PARENT';
		$markers['board_order'] = 'boardOrder';
		$markers['id_last_msg'] = 'ID_LAST_MSG';
		$markers['id_msg_updated'] = 'ID_MSG_UPDATED';
		$markers['member_groups'] = 'memberGroups';
		$markers['name'] = 'name';
		$markers['description'] = 'description';
		$markers['num_topics'] = 'numTopics';
		$markers['num_posts'] = 'numPosts';
		$markers['count_posts'] = 'countPosts';
		$markers['id_theme'] = 'ID_THEME';
		$markers['permission_mode'] = 'permission_mode';
		$markers['override_theme'] = 'override_theme'; 
		
		return $markers;
	}
	
	function storeAdapter( $fields ) 
	{	
		$markers = JLMS_SMF_board::getMarkers();		
		return parent::storeAdapter( $fields, $markers );
	}
	
	function loadAdapter( $objs )
	{					
		$markers = JLMS_SMF_board::getMarkers();
		return parent::loadAdapter( $objs, $markers );		
	}	
}

class JLMS_SMF_membergroup extends SMFTable 
{
	var $ID_GROUP = null;
  	var $groupName = null;
  	var $onlineColor = null;
  	var $minPosts = null;
  	var $maxMessages = null;
  	var $stars = null;
  	
  	function __construct( &$_db ) {
		parent::__construct( '#__membergroups', 'ID_GROUP', $_db );
	}
	
	function getMarkers() 
	{
		$markers['id_group'] = 'ID_GROUP';
		$markers['group_name'] = 'groupName';
		$markers['online_color'] = 'onlineColor';
		$markers['min_posts'] = 'minPosts';
		$markers['max_messages'] = 'maxMessages';
		$markers['stars'] = 'stars';  
		
		return $markers;
	}
	
	function storeAdapter( $fields ) 
	{	
		$markers = JLMS_SMF_membergroup::getMarkers();		
		return parent::storeAdapter( $fields, $markers );
	}
	
	function loadAdapter( $objs )
	{					
		$markers = JLMS_SMF_membergroup::getMarkers();
		return parent::loadAdapter( $objs, $markers );		
	}
}

class JLMS_SMF_category extends SMFTable 
{
	var $ID_CAT = null;
  	var $catOrder = null;
  	var $name = null;
  	var $canCollapse = null;
  	
  	function __construct( &$_db ) {
		parent::__construct( '#__categories', 'ID_CAT', $_db );
	}
	
	function getMarkers() 
	{
		$markers['id_cat'] = 'ID_CAT';
		$markers['cat_order'] = 'catOrder';
		$markers['name'] = 'name';
		$markers['can_collapse'] = 'canCollapse';		 
		
		return $markers;
	}
	
	function storeAdapter( $fields ) 
	{	
		$markers = JLMS_SMF_category::getMarkers();			
		return parent::storeAdapter( $fields, $markers );
	}
	
	function loadAdapter( $objs )
	{					
		$markers = JLMS_SMF_category::getMarkers();
		return parent::loadAdapter( $objs, $markers );		
	}
}