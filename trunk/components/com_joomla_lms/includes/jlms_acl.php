<?php
/**
* includes/jlms_acl.php
* Joomla LMS Component
* * * ElearningForce DK
**/

defined( '_JLMS_EXEC' ) or die( 'Restricted access' );

function JLMS_checkCourseGID($user_id, $course_gid) {
	global $JLMS_DB, $JLMS_CONFIG;
	
	$acl = & JLMSFactory::getJoomlaACL();

	//Cache functions (save data about user gid, course gid and access results in JLMS_CONFIG)
	$cache_user_gids = $JLMS_CONFIG->get('cache_user_gids', array());
	$user_item = 'user_' . $user_id;	
	
	if (isset($cache_user_gids[$user_item]) && $cache_user_gids[$user_item]) 
	{		
		$userGids = $cache_user_gids[$user_item];
	} else {
		if ( $user_id ) {
			if( JLMS_J16version() ) 
			{
				$userGids = $acl->getGroupsByUser( $user_id );													
			} else {
				$query = "SELECT gid FROM #__users WHERE id = ".$user_id;
				$JLMS_DB->SetQuery($query);
				$userGids[] = $JLMS_DB->LoadResult();
			}
		} else {
			$userGids = array();
		}
		
		$cache_user_gids[$user_item] = $userGids;
		$JLMS_CONFIG->set('cache_user_gids', $cache_user_gids);
	}
			
	$result = false;
	$ac_groups = array(0);
	if ($course_gid) {
		$ac_groups = explode(",", $course_gid);
	}
	
	if (in_array(0, $ac_groups)) {
		$result = true;
	} elseif ( $user_id && is_array($ac_groups) ) {
		$adminGroups = JLMS_getAdminGroups();	
		$ac_groups = array_unique( array_merge( $ac_groups, $adminGroups ));
											
		foreach ($ac_groups as $ag) {
			$cache_gid_checked = $JLMS_CONFIG->get('cache_gid_checked', array());			
		
			foreach( $userGids AS $userGid ) 
			{
				$cg_item = 'check_gid_' . $userGid . '_' . $ag;
				if (isset($cache_gid_checked[$cg_item])) {
					if ($cache_gid_checked[$cg_item]) {
						$result = true; break;
					}
				} else {
					if ($userGid == $ag) {
						$result = true; break;
					}
										
					if ( $acl->is_group_child_of( intval($userGid), intval($ag), 'ARO' ) ) {
						$result = true; break;
					}
				}
				$cache_gid_checked[$cg_item] = $result;
				$JLMS_CONFIG->set('cache_gid_checked', $cache_gid_checked);
			}
		}
	}
	
	return $result;
}
?>