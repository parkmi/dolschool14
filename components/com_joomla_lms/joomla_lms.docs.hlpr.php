<?php
/**
* joomla_lms.docs.hlpr.php
* JoomlaLMS Component
**/

// no direct access
defined( '_JLMS_EXEC' ) or die( 'Restricted access' );

class JLMSDocs {

function FillList($course_id, &$docs_rows, &$docs_possibilities, $folders = false, $show_unavailable = true) {
	global $JLMS_DB, $my;
	$JLMS_ACL = & JLMSFactory::getACL();
	
	$AND_ST = "";
	if( false !== ( $enroll_period = JLMS_getEnrolPeriod( $my->id, $course_id )) ) 
	{
		$AND_ST = " AND IF(a.is_time_related, (a.show_period < '".$enroll_period."' ), 1) ";	
	}
	
	$query = "SELECT a.*, b.file_name, c.name, c.username, c.name as author_name, dp.*, dp.doc_id as doc_perms_db, dp.doc_id as doc_perms, dp.p_manage as p_create, dp.p_publish as p_publish_childs"
	. "\n FROM #__lms_documents as a LEFT JOIN #__lms_files as b ON a.file_id = b.id AND a.folder_flag = 0 LEFT JOIN #__users as c ON a.owner_id = c.id"
	. "\n LEFT JOIN #__lms_documents_perms as dp ON  a.id = dp.doc_id AND dp.role_id = ".$JLMS_ACL->GetRole()
	. "\n WHERE a.course_id = '".$course_id."'".$AND_ST
	. ($folders ? "\n AND a.folder_flag = 1" : "") // select only folders, if necessary
	//. (($JLMS_ACL->CheckPermissions('docs', 'view_all')) ? '' : "\n AND a.published = 1")
	//. (($JLMS_ACL->CheckPermissions('docs', 'view_all')) ? '' : "\n AND ( ((a.publish_start = 1) AND (a.start_date <= '".date('Y-m-d')."')) OR (a.publish_start = 0) )")
	//. (($JLMS_ACL->CheckPermissions('docs', 'view_all')) ? '' : "\n AND ( ((a.publish_end = 1) AND (a.end_date >= '".date('Y-m-d')."')) OR (a.publish_end = 0) )")
	// this code is commented by DEN:
	// because you can reassign 'viewall' permission using folder's "custom permissions" feature
	// HERE: possibly we can insert 'ignore_permssions' cehck here in adition to 'view_all' check (to decrease number of selected records)
	. "\n ORDER BY a.parent_id, a.ordering, a.doc_name, c.username";
	$JLMS_DB->SetQuery( $query );
	$rows = $JLMS_DB->LoadObjectList();

	if (!$folders) { // not only folders are present in 'rows' - we need to filter/remove unnecessary items
		$bad_in = array();
		$rows_n = array();
		for($j=0;$j<count($rows);$j++){
		// TODO: redevelop this part of the code, DO NOT USE database queries within the for/while/foreach loops
			if($rows[$j]->folder_flag == 3) { // this is a file-library link

				$query = "SELECT a.*, b.file_name"
				. "\n FROM #__lms_outer_documents as a LEFT JOIN #__lms_files as b ON a.file_id = b.id AND a.folder_flag = 0 "
				. "\n WHERE a.folder_flag = 0 AND a.id = ".$rows[$j]->file_id;

				$JLMS_DB->SetQuery( $query );
				$out_row = $JLMS_DB->LoadObjectList();

				if(count($out_row) && ($out_row[0]->allow_link == 1)) {
					// resource is found in the Library
					$rows[$j]->doc_name = $out_row[0]->doc_name;
					$rows[$j]->doc_description = $out_row[0]->doc_description;
					$rows[$j]->file_id = $out_row[0]->file_id;
					$rows[$j]->file_name = $out_row[0]->file_name;
				} else {
					// there is no link in the Library (e.g. file-library file was removed or its permissions were changed)
					if($JLMS_ACL->CheckPermissions('docs', 'manage') && $show_unavailable){
						// show 'Resource is not available' message instead of a file
						$rows[$j]->doc_name = _JLMS_LP_RESOURSE_ISUNAV;
						$rows[$j]->is_link = 1;
						$rows[$j]->author_name = '';
					}else{
						// remove item from the array
						$g = 0;
						$rows_n = array();
						// What the fuck?....
						for($z=0;$z<count($rows);$z++){
							if($z != $j && !in_array($z,$bad_in)) {
								$g++;
							} else {
								// populate array of the items which need to be removed from the list of files
								$bad_in[] = $j;
							}
						}
					}
				}
			}
		}
		if(count($bad_in)){
			// remove 'not available' items from the list of files
			$rows_n = array();
			$g = 0;
			for($z=0;$z<count($rows);$z++){
				if (in_array($z, $bad_in)) {
				} else {
					$rows_n[$g] = $rows[$z];
					$g++;
				}
			}
			$rows = $rows_n;
		}
	}

	$rows = JLMS_GetTreeStructure( $rows );

	$rows = AppendFileIcons_toList( $rows );

	$possibilities = new stdClass();
	if (true) {//proceed in any case, even if we have 'ignore_permissions'
		// check notes about custom permissions at the top of the 'documents' source file (after $task section)
		$bad_in = array();
		$permissions = array();
		$permissions[0] = new stdClass();
		$permissions[0]->active = 1; // not used any more ????
		$permissions[0]->p_view = $possibilities->view = $JLMS_ACL->CheckPermissions('docs', 'view') ? 1 : 0;
		$permissions[0]->p_viewall = $possibilities->viewall = $JLMS_ACL->CheckPermissions('docs', 'view_all') ? 1 : 0;
		$permissions[0]->p_order = $possibilities->order = $JLMS_ACL->CheckPermissions('docs', 'order') ? 1 : 0;
		$permissions[0]->p_publish = $possibilities->publish = $JLMS_ACL->CheckPermissions('docs', 'publish') ? 1 : 0;
		$permissions[0]->p_publish_childs = $possibilities->publish_childs = $JLMS_ACL->CheckPermissions('docs', 'publish') ? 1 : 0;
		$permissions[0]->p_manage = $possibilities->manage = $JLMS_ACL->CheckPermissions('docs', 'manage') ? 1 : 0;
		$permissions[0]->p_create = $possibilities->create = $JLMS_ACL->CheckPermissions('docs', 'manage') ? 1 : 0;
		$folder_id = 0;
		$j = 0;
		$n = count($rows);
		while ($j < $n) {
			if ($rows[$j]->folder_flag == 1 && isset($rows[$j]->doc_perms_db) && $rows[$j]->doc_perms_db) { // folder with configured custom permissions

				$old_folder_id = $rows[$j]->parent_id;//$folder_id;
				// process permissions (add them into array from which they will be associated with child items)
				$folder_id = $rows[$j]->id;
				$parent_id = $rows[$j]->parent_id;

				if ($JLMS_ACL->CheckPermissions('docs', 'ignore_permissions')) {
					$permissions[$folder_id] = new stdClass();
					$permissions[$folder_id] = $permissions[0];
					$rows[$j]->p_view = $permissions[0]->p_view;
					$rows[$j]->p_viewall = $permissions[0]->p_viewall;
					$rows[$j]->p_order = $permissions[0]->p_order;
					$rows[$j]->p_publish = $permissions[0]->p_publish;
					$rows[$j]->p_manage = $permissions[0]->p_manage;
					$rows[$j]->p_create = $permissions[0]->p_create;
					$rows[$j]->p_publish_childs = $permissions[0]->p_publish_childs;
				} else {
					$permissions[$folder_id] = new stdClass();
					$permissions[$folder_id]->active = 1; // not used any more ????
					$permissions[$folder_id]->p_view = $rows[$j]->p_view;
					$permissions[$folder_id]->p_viewall = $permissions[$folder_id]->p_view ? $rows[$j]->p_viewall : 0;
					$permissions[$folder_id]->p_order = $permissions[$folder_id]->p_view ? $rows[$j]->p_order : 0;
					$permissions[$folder_id]->p_publish = $permissions[$folder_id]->p_view ? $rows[$j]->p_publish : 0;
					$permissions[$folder_id]->p_publish_childs = $permissions[$folder_id]->p_view ? $rows[$j]->p_publish_childs : 0;
					$permissions[$folder_id]->p_manage = $permissions[$folder_id]->p_view ? $rows[$j]->p_manage : 0;// "no view - no manage" // 'no martiny - no party ;)'
					$permissions[$folder_id]->p_create = $permissions[$folder_id]->p_view ? $rows[$j]->p_create : 0;
					// set parent's permissions for this folder
					$rows[$j]->doc_perms = 1;
					$rows[$j]->p_view = $permissions[$old_folder_id]->p_view ? $rows[$j]->p_view : 0;
					$rows[$j]->p_viewall = $permissions[$old_folder_id]->p_viewall ? $rows[$j]->p_viewall : 0;
					$rows[$j]->p_order = $permissions[$old_folder_id]->p_order ? $rows[$j]->p_order : 0;
					$rows[$j]->p_publish = $permissions[$old_folder_id]->p_publish ? $rows[$j]->p_publish : 0;
					$rows[$j]->p_manage = $permissions[$old_folder_id]->p_manage ? $rows[$j]->p_manage : 0;
					$rows[$j]->p_create = $old_folder_id ? $permissions[$old_folder_id]->p_create : $rows[$j]->p_create;
					$rows[$j]->p_publish_childs = $old_folder_id ? $permissions[$old_folder_id]->p_publish_childs : $rows[$j]->p_publish_childs;
				}

				// change 'view' permission regarding to the 'published' status of the item
				if (!$rows[$j]->p_viewall) {
					if ($rows[$j]->published && $permissions[$old_folder_id]->p_view && 
						( ($rows[$j]->publish_start && strtotime($rows[$j]->start_date) <= strtotime(date('Y-m-d'))) || !$rows[$j]->publish_start ) &&
						( ($rows[$j]->publish_end && strtotime($rows[$j]->end_date) >= strtotime(date('Y-m-d'))) || !$rows[$j]->publish_end )
					) {
						// user can view this item
					} elseif($rows[$j]->owner_id == $my->id && $rows[$j]->p_manage) {
						// this item is unpublished, but user is its owner - he can view it if he has 'manage' rights
					} else {
						$rows[$j]->p_view = 0;
						$rows[$j]->p_viewall = 0;
						$rows[$j]->p_order = 0;
						$rows[$j]->p_publish = 0;
						$rows[$j]->p_manage = 0;
						$rows[$j]->p_create = 0;
						$rows[$j]->p_publish_childs = 0;
						$permissions[$folder_id]->p_view = 0;
						$permissions[$folder_id]->p_viewall = 0;
						$permissions[$folder_id]->p_order = 0;
						$permissions[$folder_id]->p_publish = 0;
						$permissions[$folder_id]->p_manage = 0;
						$permissions[$folder_id]->p_create = 0;
						$permissions[$folder_id]->p_publish_childs = 0;
					}
				}

				$possibilities->create = $possibilities->create ? $possibilities->create : $rows[$j]->p_create;// we can create docs at least in this folder
				$possibilities->publish_childs = $possibilities->publish_childs ? $possibilities->publish_childs : $rows[$j]->p_publish_childs;

			} else { // any other item: file,  file-library link or folder without custom permissions
				$folder_id = $rows[$j]->parent_id;
				if ( isset($permissions[$folder_id]) && isset($permissions[$folder_id]->active) && $permissions[$folder_id]->active ) {
					// set parent's permissions for this item
					$rows[$j]->doc_perms = 1;
					$rows[$j]->p_view = $permissions[$folder_id]->p_view;
					$rows[$j]->p_viewall = $permissions[$folder_id]->p_viewall;
					$rows[$j]->p_order = $permissions[$folder_id]->p_order;
					$rows[$j]->p_publish = $permissions[$folder_id]->p_publish;
					$rows[$j]->p_manage = $permissions[$folder_id]->p_manage;
					$rows[$j]->p_create = 0;//$permissions[$folder_id]->p_create;
					$rows[$j]->p_publish_childs = 0;//$permissions[$folder_id]->p_publish_childs;
					// change 'view' permission regarding to the 'published' status of the item
					if (!$rows[$j]->p_viewall) {
						if ($rows[$j]->published && $permissions[$folder_id]->p_view && 
							( ($rows[$j]->publish_start && strtotime($rows[$j]->start_date) <= strtotime(date('Y-m-d'))) || !$rows[$j]->publish_start ) &&
							( ($rows[$j]->publish_end && strtotime($rows[$j]->end_date) >= strtotime(date('Y-m-d'))) || !$rows[$j]->publish_end )
						) {
							// user can view this item
						} elseif($rows[$j]->owner_id == $my->id && $rows[$j]->p_manage) {
							// this item is unpublished, but user is its owner - he can view it if he has 'manage' rights
						} else {
							$rows[$j]->p_view = 0;
							$rows[$j]->p_viewall = 0;
							$rows[$j]->p_order = 0;
							$rows[$j]->p_publish = 0;
							$rows[$j]->p_manage = 0;
							$rows[$j]->p_create = 0;
							$rows[$j]->p_publish_childs = 0;
							if ($rows[$j]->folder_flag == 1) { // just a folder (without custom permissions)
								$folder_id_current = $rows[$j]->id;
								$permissions[$folder_id_current]->p_view = 0;
								$permissions[$folder_id_current]->p_viewall = 0;
								$permissions[$folder_id_current]->p_order = 0;
								$permissions[$folder_id_current]->p_publish = 0;
								$permissions[$folder_id_current]->p_manage = 0;
								$permissions[$folder_id_current]->p_create = 0;
								$permissions[$folder_id_current]->p_publish_childs = 0;
							}
						}
					}
					$possibilities->view = $possibilities->view ? $possibilities->view : $rows[$j]->p_view;
					$possibilities->viewall = $possibilities->view ? $possibilities->viewall : $rows[$j]->p_viewall;
					$possibilities->order = $possibilities->order ? $possibilities->order : $rows[$j]->p_order;
					$possibilities->publish = $possibilities->publish ? $possibilities->publish : $rows[$j]->p_publish;
					$possibilities->manage = $possibilities->manage ? $possibilities->manage : $rows[$j]->p_manage;
					//$possibilities->create = $possibilities->create ? $possibilities->create : $rows[$j]->p_create;
					//$possibilities->publish_childs = $possibilities->publish_childs ? $possibilities->publish_childs : $rows[$j]->p_publish_childs;
				}
				if ($rows[$j]->folder_flag == 1) { // just a folder (without custom permissions)
					$rows[$j]->p_create = $permissions[$folder_id]->p_create;
					$rows[$j]->p_publish_childs = $permissions[$folder_id]->p_publish_childs;
					$possibilities->create = $possibilities->create ? $possibilities->create : $rows[$j]->p_create;
					$possibilities->publish_childs = $possibilities->publish_childs ? $possibilities->publish_childs : $rows[$j]->p_publish_childs;
					// HERE: inherit permissions from the parent folder
					$folder_id = $rows[$j]->id;
					$parent_id = $rows[$j]->parent_id;
					if ( isset($permissions[$parent_id]) && isset($permissions[$parent_id]->active) && $permissions[$parent_id]->active ) {
						$permissions[$folder_id]->active = 1;
						$permissions[$folder_id]->p_view = ( (isset($permissions[$folder_id]->p_view) && $permissions[$folder_id]->p_view) || !isset($permissions[$folder_id]->p_view)) ? $permissions[$parent_id]->p_view : $permissions[$folder_id]->p_view;
						$permissions[$folder_id]->p_viewall = ( (isset($permissions[$folder_id]->p_viewall) && $permissions[$folder_id]->p_viewall) || !isset($permissions[$folder_id]->p_viewall)) ? $permissions[$parent_id]->p_viewall : $permissions[$folder_id]->p_viewall;
						$permissions[$folder_id]->p_order = ( (isset($permissions[$folder_id]->p_order) && $permissions[$folder_id]->p_order) || !isset($permissions[$folder_id]->p_order)) ? $permissions[$parent_id]->p_order : $permissions[$folder_id]->p_order;
						$permissions[$folder_id]->p_publish = ( (isset($permissions[$folder_id]->p_publish) && $permissions[$folder_id]->p_publish) || !isset($permissions[$folder_id]->p_publish)) ? $permissions[$parent_id]->p_publish : $permissions[$folder_id]->p_publish;
						$permissions[$folder_id]->p_manage = ( (isset($permissions[$folder_id]->p_manage) && $permissions[$folder_id]->p_manage) || !isset($permissions[$folder_id]->p_manage)) ? $permissions[$parent_id]->p_manage : $permissions[$folder_id]->p_manage;
						$permissions[$folder_id]->p_create = ( (isset($permissions[$folder_id]->p_create) && $permissions[$folder_id]->p_create) || !isset($permissions[$folder_id]->p_create)) ? $permissions[$parent_id]->p_create : $permissions[$folder_id]->p_create;
						$permissions[$folder_id]->p_publish_childs = ( (isset($permissions[$folder_id]->p_publish_childs) && $permissions[$folder_id]->p_publish_childs) || !isset($permissions[$folder_id]->p_publish_childs)) ? $permissions[$parent_id]->p_publish_childs : $permissions[$folder_id]->p_publish_childs;
						if (!$permissions[$folder_id]->p_view) {
							// "no view - no manage" // 'no martiny - no party ;)'
							$permissions[$folder_id]->p_viewall = 0;
							$permissions[$folder_id]->p_order = 0;
							$permissions[$folder_id]->p_publish = 0;
							$permissions[$folder_id]->p_manage = 0;
							$permissions[$folder_id]->p_create = 0;
							$permissions[$folder_id]->p_publish_childs = 0;
						}
					}
				}
			}
			$j ++;
		}
	}

	$docs_rows = $rows;
	$docs_possibilities = $possibilities;
}
// returns my permissions for element $id
function & GetItemPermissions(&$rows, $id) {
	global $my;
	$permissions = new stdClass();
	$permissions->view = 0;
	$permissions->viewall = 0;
	$permissions->publish = 0;
	$permissions->published = 0;
	$permissions->order = 0;
	$permissions->manage = 0;
	$permissions->create = 0;
	$permissions->publish_childs = 0;
	$JLMS_ACL = & JLMSFactory::getACL();
	if (!$id) {
		// create fake item to emulate 'docs root'
		$row = new stdClass();
		$row->published = 1;
		$row->publish_start = 0;
		$row->publish_end = 0;
		$row->start_date = date('Y-m-d');
		$row->end_date = date('Y-m-d');
		$row->owner_id = $my->id;
	} else {
		$row = & JLMSDocs::GetItembyID($rows, $id);
	}
	if ($JLMS_ACL->CheckPermissions('docs', 'view') && !is_null($row)) {
		$permissions->published = ($row->published) ? true : false;
		if ($permissions->published) {
			$permissions->published = (($row->publish_start && strtotime($row->start_date) <= strtotime(date('Y-m-d'))) || !$row->publish_start) ? true : false;
		}
		if ($permissions->published) {
			$permissions->published = ( ($row->publish_end && strtotime($row->end_date) >= strtotime(date('Y-m-d'))) || !$row->publish_end ) ? true : false;
		}
		if ($JLMS_ACL->CheckPermissions('docs', 'ignore_permissions') || !$id) {
			// ignore custom permissions or check permissions for the 'root' folder (fake item was created))
			if ($JLMS_ACL->CheckPermissions('docs', 'view_all') || $row->owner_id == $my->id || $permissions->published) {
				$permissions->view = 1;
				if ($JLMS_ACL->CheckPermissions('docs', 'manage')) {
					$permissions->manage = 1;
					$permissions->create = 1;
				}
				if ($JLMS_ACL->CheckPermissions('docs', 'publish')) {
					$permissions->publish = 1;
					$permissions->publish_childs = 1;
				}
				if ($JLMS_ACL->CheckPermissions('docs', 'order')) {
					$permissions->order = 1;
				}
			}
		} else {
			// here we are sure that $row is an element, but not a fake item
			// check element custom permissions
			if (isset($row->p_manage) && $row->p_manage) {
				$permissions->manage = 1;
			}
			if (isset($row->p_view) && $row->p_view) {
				$permissions->view = 1;
			}
			if (isset($row->p_order) && $row->p_order) {
				$permissions->order = 1;
			}
			if (isset($row->p_publish) && $row->p_publish) {
				$permissions->publish = 1;
			}
			if (isset($row->p_create) && $row->p_create) {
				$permissions->create = 1;
			}
			if (isset($row->p_publish_childs) && $row->p_publish_childs) {
				$permissions->publish_childs = 1;
			}
		}
		if ($JLMS_ACL->CheckPermissions('docs', 'only_own_items')) {
			// reset permissions for not owned by me items
			if ($row->owner_id != $my->id) {
				$permissions->manage = 0;
				$permissions->create = 0;
				$permissions->publish = 0;
				$permissions->order = 0;
			}
		} elseif ($JLMS_ACL->CheckPermissions('docs', 'only_own_role')) {
			// reset permissions for not owned by my colleagues items
			if ($JLMS_ACL->GetRole() != $JLMS_ACL->UserSystemRole($JLMS_DB, $row->owner_id)) {
				$permissions->manage = 0;
				$permissions->create = 0;
				$permissions->publish = 0;
				$permissions->order = 0;
			}
		}
	}
	
	return $permissions;
}
// return document element (by $id) from array of the documents
function & GetItembyID(&$rows, $id) {
	static $items_cache;
	if (isset($items_cache[$id])) {
		$doc_row = $rows[$items_cache[$id]];
		return $doc_row;
	}
	$doc_row = NULL;
	$i = 0;
	foreach ($rows as $row) {
		if ($row->id == $id) {
			$items_cache[$id] = $i;
			$doc_row = $row;
			break;
		}
		$i ++;
	}
	return $doc_row;
}
// return array of documents with allowed permission '$permission'
function & GetItemsbyPermission(&$rows, $permission = 'view') {
	$new_rows = array();
	$permission = 'p_'.$permission;
	foreach ($rows as $row) {
		if (isset($row->$permission) && $row->$permission) {
			$new_rows[] = $row;
		}
	}
	return $new_rows;
}
// return array of documents from $subset id's with allowed permission '$permission'
// NOTE: this function is not used any more
function & GetSubSetbyPermission(&$rows, $subset = array(), $permission = 'view') {
	$new_rows = array();
	$permission = 'p_'.$permission;
	foreach ($rows as $row) {
		if (isset($row->$permission) && $row->$permission && in_array($row->id, $subset)) {
			$new_rows[] = $row;
		}
	}
	return $new_rows;
}
function CheckCourseID(&$rows, $id, $course_id) {
	if (!$course_id) { return false; }
	if (!$id) { return true; }
	$row = & JLMSDocs::GetItembyID($rows, $id);
	if (!is_null($row)) {
		if ($row->course_id == $course_id) {
			return true;
		}
	}
	return false;
}
function GetItemParentID(&$rows, $id) {
	if (!$id) { return 0; }
	$row = & JLMSDocs::GetItembyID($rows, $id);
	if (!is_null($row)) {
		return $row->parent_id;
	}
	return 0;
}
function & GetItemChilds(&$rows, $id) {
	$childs = array();
	foreach ($rows as $row) {
		if ($row->parent_id == $id) {
			$childs[] = $row;
		}
	}
	return $childs;
}
function & ExcludeItems(&$rows, $ex_items = array()) {
	if (empty($ex_items)) {
		return $rows;
	} else {
		$new_rows = array();
		$ex_folder = 0;
		$is_ex_folder = false;
		foreach($rows as $row) {
			if ($is_ex_folder) {
				if ($row->parent_id == $ex_folder) {
					$ex_folder = 0;
					$is_ex_folder = false;
				}
			}
			if (!$is_ex_folder) {
				if (!in_array($row->id, $ex_items)) {
					$new_rows[] = $row;
				} else {
					$ex_folder = $row->parent_id;
					$is_ex_folder = true;
				}
			}
		}
		return $new_rows;
	}
}

function & GetChildsToPublish(&$rows, $items, $reset_ticker = false) {
	// if $loop_ticker is very high - seems like our function got into loop forever - interrupt it in this case.
	static $loop_ticker;
	if ($reset_ticker) { $loop_ticker = 0; }
	if (!$loop_ticker) { $loop_ticker = 0; }
	$loop_ticker ++;
	$change_state_items = array();
	foreach ($items as $nc) {
		$childs = & JLMSDocs::GetItemChilds($rows, $nc);
		$child_ids = array();
		$child_folder_ids = array();
		foreach ($childs as $child) {
			if (isset($child->p_publish) && $child->p_publish) {
				$change_state_items[] = $child->id;
			}
			if ($child->folder_flag == 1) {
				$child_folder_ids[] = $child->id;
			}
		}
		if (count($child_folder_ids)) {
			if ($loop_ticker < 100) { // DEN: 100 nested folders is more than enough ;)
				$un_childs = & JLMSDocs::GetChildsToPublish($rows, $child_folder_ids);
			} else {
				$un_childs = array();
			}
			$change_state_items = array_merge($change_state_items, $un_childs);
		}
	}
	return $change_state_items;
}

} // end of class
?>