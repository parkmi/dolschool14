<?php
/**
* joomla_lms.course_users.html.php
* (c) JoomaLMS eLearning Software http://www.joomlalms.com/
**/

// no direct access
defined( '_JLMS_EXEC' ) or die( 'Restricted access' );

class JLMS_course_users_html {
	function showUserGroups( $course_id, $option, &$rows, &$lists ) {
		$JLMS_CONFIG = & JLMSFactory::getConfig();
		$Itemid = $JLMS_CONFIG->get('Itemid');
		?>
		<script language="javascript" type="text/javascript">
		<!--
		function submitbutton(pressbutton) {
			var form = document.adminForm;
			if ( ((pressbutton == 'edit_usergroup') || (pressbutton == 'export_usergroup')) && (form.boxchecked.value == '0')) {
				alert('<?php echo _JLMS_ALERT_SELECT_ITEM;?>');
			} else if ((pressbutton == 'usergroup_delete') && (form.boxchecked.value == '0')) {
				alert('<?php echo _JLMS_ALERT_SELECT_ITEM;?>');
			} else {
				form.task.value = pressbutton;
				form.submit();
			}
		}
		//-->
		</script>
		<?php
		JLMS_TMPL::OpenMT();

		$hparams = array();
		JLMS_TMPL::ShowHeader('userman', _JLMS_USERGROUPS_TITLE, $hparams);
		
		$JLMS_ACL = & JLMSFactory::getACL();
		JLMS_TMPL::OpenTS();
		?>
		<form action="<?php echo $JLMS_CONFIG->get('live_site')."/index.php?option=".$option."&amp;Itemid=".$Itemid;?>" method="post" name="adminForm">
			<table width="100%" cellpadding="0" cellspacing="0" border="0" class="<?php echo JLMSCSS::_('jlmslist');?>">
				<tr>
					<<?php echo JLMSCSS::tableheadertag();?> width="20" class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center">#</<?php echo JLMSCSS::tableheadertag();?>>
					<?php
					if ($JLMS_ACL->CheckPermissions('users', 'manage')){
					?>
					<<?php echo JLMSCSS::tableheadertag();?> width="20" class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows)+1; ?>);" /></<?php echo JLMSCSS::tableheadertag();?>>
					<?php
					} else {
					?>
					<<?php echo JLMSCSS::tableheadertag();?> width="20" class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center">&nbsp;</<?php echo JLMSCSS::tableheadertag();?>>
					<?php
					}
					?>
					<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>"><?php echo _JLMS_USERS_TBL_HEAD_GROUPNAME;?></<?php echo JLMSCSS::tableheadertag();?>>
					<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>"><?php echo _JLMS_USERS_TBL_HEAD_GROUPDESCR;?></<?php echo JLMSCSS::tableheadertag();?>>
				</tr>
				<?php if ($JLMS_ACL->CheckPermissions('users', 'manage_teachers')) { ?>
				<tr class="<?php echo JLMSCSS::_('sectiontableentry2');?>">
					<td align="center">-</td>
					<td align="center">&nbsp;</td>
					<td><?php $link = "index.php?option=".$option."&amp;Itemid=".$Itemid."&amp;task=view_assistants&amp;id=".$course_id; ?>
						<a href="<?php echo sefRelToAbs($link);?>" title="<?php echo _JLMS_USERS_GROUP_LINK_TITLE;?>">
							<?php echo _JLMS_USER_ASSIST_GROUP_NAME;?>
						</a>
					</td>
					<td><?php echo _JLMS_USER_ASSIST_GROUP_DESCR;?></td>
				</tr>
			<?php } ?>
			<?php if ($lists['no_group']) { ?>
				<tr class="<?php echo JLMSCSS::_('sectiontableentry2');?>">
					<td align="center">-</td>
					<td align="center">
						<?php 
						if ($JLMS_ACL->CheckPermissions('users', 'manage')) {
							echo mosHTML::idBox( 0, 0);
						} else {
							echo '&nbsp;';
						}						
						?>
					</td>
					<td><?php $link = "index.php?option=".$option."&amp;Itemid=".$Itemid."&amp;task=view_users&amp;course_id=".$course_id."&amp;id=0"; ?>
						<a href="<?php echo sefRelToAbs($link);?>" title="<?php echo _JLMS_USERS_GROUP_LINK_TITLE;?>">
							<?php echo _JLMS_USER_NO_GROUP_NAME;?>
						</a>
					</td>
					<td><?php echo _JLMS_USER_ASSIST_GROUP_DESCR;?></td>
				</tr>
			<?php }
			$k = 1;
			for ($i=0, $n=count($rows); $i < $n; $i++) {
				$row = $rows[$i];
				$link = "index.php?option=".$option."&amp;Itemid=".$Itemid."&amp;task=view_users&amp;course_id=".$course_id."&amp;id=". $row->id;
				$checked = mosHTML::idBox( $i+1, $row->id);
				if (!$JLMS_ACL->CheckPermissions('users', 'manage')) {
					$checked = '&nbsp;';	
				}
				?>
				<tr class="<?php echo JLMSCSS::_('sectiontableentry'.$k); ?>">
					<td align="center"><?php echo ( $i + 1 ); ?></td>
					<td><?php echo $checked; ?></td>
					<td align="left">
						<a href="<?php echo sefRelToAbs($link);?>" title="<?php echo _JLMS_USERS_GROUP_LINK_TITLE;?>">
							<?php echo $row->ug_name;?>
						</a>
					</td>
					<td><?php echo $row->ug_description;?></td>
				</tr>
				<?php
				$k = 3 - $k;
			}?>
			</table>
			<input type="hidden" name="option" value="<?php echo $option;?>" />
			<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
			<input type="hidden" name="task" value="course_users" />
			<input type="hidden" name="id" value="<?php echo $course_id;?>" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="row_id" value="0" />
			<input type="hidden" name="state" value="0" />
		</form>
<?php
		JLMS_TMPL::CloseTS();
		$link_new = "index.php?option=".$option."&amp;Itemid=".$Itemid."&amp;task=new_usergroup&amp;id=".$course_id;
		$link_csvdelete = "index.php?option=".$option."&amp;Itemid=".$Itemid."&amp;task=user_csv_delete&amp;id=".$course_id;
		$controls = array();
		if ($JLMS_ACL->CheckPermissions('users', 'manage')) {
			$controls[] = array('href' => "javascript:submitbutton('export_usergroup');", 'title' => _JLMS_USER_ALT_EXPGROUP, 'img' => 'export');
			$controls[] = array('href' => 'spacer');
			$controls[] = array('href' => ampReplace(sefRelToAbs($link_new)), 'title' => _JLMS_USER_ALT_NEWGROUP, 'img' => 'addusergroup');
			$controls[] = array('href' => "javascript:submitbutton('usergroup_delete');", 'title' => _JLMS_USER_ALT_DELGROUP, 'img' => 'delusergroup');
			$controls[] = array('href' => "javascript:submitbutton('edit_usergroup');", 'title' => _JLMS_USER_ALT_EDITGROUP, 'img' => 'editusergroup');
			$controls[] = array('href' => 'spacer');
			$controls[] = array('href' => ampReplace(sefRelToAbs($link_csvdelete)), 'title' => _JLMS_USERS_CSV_DELETE, 'img' => 'delete');
		}
		JLMS_TMPL::ShowControlsFooter($controls);
		JLMS_TMPL::CloseMT();
	}

	function showDeleteUsersCSV( $course_id, $option ) {
		global $Itemid; ?>
<script language="javascript" type="text/javascript">
<!--
function submitbutton(pressbutton) {
	var form = document.adminForm;
	if ((pressbutton == 'import_csv_delete') && (form.csv_file.value == '') ) {
		alert( "<?php echo _JLMS_SELECT_FILE;?>" );
	}
	else {
		form.task.value = pressbutton;
		form.submit();
	}
}
//-->
</script>
<?php
		JLMS_TMPL::OpenMT();

		$hparams = array();
		$toolbar = array();
		$toolbar[] = array('btn_type' => 'yes', 'btn_str' => 'Delete', 'btn_js' => "javascript:submitbutton('import_csv_delete');");
		$toolbar[] = array('btn_type' => 'cancel', 'btn_js' => "javascript:submitbutton('cancel_csv_delete');");
		JLMS_TMPL::ShowHeader('userman', _JLMS_USERS_CSV_DELETE, $hparams, $toolbar);

		JLMS_TMPL::OpenTS();
?>
		<form action="<?php echo sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid");?>" method="post" name="adminForm" enctype="multipart/form-data">
			<table width="100%" cellpadding="0" cellspacing="0" border="0" class="jlms_table_no_borders">
				<tr>
					<td width="15%" valign="middle" style="vertical-align:middle "><?php echo _JLMS_CHOOSE_FILE;?></td>
					<td>
						<input size="40" class="inputbox" type="file" name="csv_file" />
					</td>
				</tr>
			</table>
			<input type="hidden" name="option" value="<?php echo $option;?>" />
			<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
			<input type="hidden" name="task" value="csv_delete_users" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="course_id" value="<?php echo $course_id;?>" />
		</form>
<?php
		JLMS_TMPL::CloseTS();
		JLMS_TMPL::CloseMT();
	}

	function editUserGroup( &$row, &$lists, $option, $course_id ) {
		global $Itemid; ?>
<script language="javascript" type="text/javascript">
<!--
function setgood() {
	return true;
}
function submitbutton(pressbutton) {
	var form = document.adminForm;
	try {
		form.onsubmit();
	} catch(e) {
		//alert(e);
	}
	if ( (pressbutton == 'save_usergroup') && (form.ug_name.value == "") ) {
		alert( "<?php echo _JLMS_ENTER_USERGROUP_NAME;?>" );
	} else {
		form.task.value = pressbutton;
		form.submit();
	}
}
//-->
</script>
<?php
		JLMS_TMPL::OpenMT();

		$hparams = array();
		$toolbar = array();
		$toolbar[] = array('btn_type' => 'save', 'btn_js' => "javascript:submitbutton('save_usergroup');");
		$toolbar[] = array('btn_type' => 'cancel', 'btn_js' => "javascript:submitbutton('cancel_usergroup');");
		JLMS_TMPL::ShowHeader('usergroup', $row->id ? _JLMS_USERGROUP_EDIT_TITLE : _JLMS_USERGROUP_NEW_TITLE, $hparams, $toolbar);

		JLMS_TMPL::OpenTS();
?>
		<form action="<?php echo sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid");?>" method="post" name="adminForm" onsubmit="setgood();">
			<table width="100%" cellpadding="0" cellspacing="0" border="0" id="jlms_item_properties">
				<tr>
					<td width="30%"><br /><?php echo _JLMS_ENTER_NAME;?></td>
					<td>
						<br /><input size="40" class="inputbox" type="text" name="ug_name" value="<?php echo str_replace('"','&quot;',$row->ug_name);?>" />
					</td>
				</tr>
				<tr>
					<td colspan="2"><br /><?php echo _JLMS_DESCRIPTION;?></td>
				</tr>
				<tr>
					<td colspan="2">
					<?php
						JLMS_editorArea( 'editor1', $row->ug_description, 'ug_description', '100%', '250', '40', '20' ) ; ?>
					</td>
				</tr>
				<tr>
					<td width="15%"><br /><?php echo _JLMS_USERS_IND_GROUP_FORUM;?></td>
					<td>
						<br /><?php echo $lists['group_forum'];?>
					</td>
				</tr>
				<tr>
					<td width="15%"><br /><?php echo _JLMS_USERS_IND_GROUP_CHAT;?></td>
					<td>
						<br /><?php echo $lists['group_chat'];?>
					</td>
				</tr>
			</table>
			<input type="hidden" name="option" value="<?php echo $option;?>" />
			<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
			<input type="hidden" name="task" value="save_usergroup" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="id" value="<?php echo $row->id;?>" />
			<input type="hidden" name="course_id" value="<?php echo $course_id;?>" />
		</form>
<?php
		JLMS_TMPL::CloseTS();
		JLMS_TMPL::CloseMT();
	}

/*	Show users in group
	$view_type == 1 - show users from usergroup
	$view_type == 2 - show teacher assistants */
	function showUsers( $course_id, $id, $option, &$rows, &$lists, &$pageNav, $u_search, $view_type = 1 ) {
		global $Itemid, $task;
		$JLMS_CONFIG = & JLMSFactory::getConfig();
		$JLMS_ACL = & JLMSFactory::getACL();
?>
<script language="javascript" type="text/javascript">
<!--
function submitbutton(pressbutton) {
	var form = document.adminForm;
	if ((pressbutton == 'edit_user' || pressbutton == 'delete_user') && (form.boxchecked.value == "0")){
		alert( "<?php echo _JLMS_USER_SELECT_USER;?>" );
	} else {
		form.task.value = pressbutton;
		form.submit();
	}
}
//-->
</script>
<?php
		JLMS_TMPL::OpenMT();

		$hparams = array();
		$toolbar = array();
		$title = '';
		if ($view_type == 1) {
			$title = '&nbsp;'._JLMS_USERS_TITLE.(isset($rows[0]->ug_name)?(" (".$rows[0]->ug_name.")"):'');
		} elseif ($view_type == 2) {
			$title = '&nbsp;'._JLMS_USERS_ASSISTS_TITLE;
		}
		$toolbar[] = array('btn_type' => 'back', 'btn_js' => sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid&amp;task=course_users&amp;id=$course_id"));
		JLMS_TMPL::ShowHeader('usergroup', $title, $hparams, $toolbar);

		if ($view_type == 1) {
			JLMS_TMPL::OpenTS();
			echo $lists['groups'];
			JLMS_TMPL::CloseTS();
		}
		JLMS_TMPL::OpenTS();

		$colspan = $view_type==1 ? 7 : 6;
?>
		<form action="<?php echo $JLMS_CONFIG->get('live_site') ."/index.php?option=".$option."&amp;Itemid=".$Itemid;?>" method="post" name="adminForm">
			<table width="100%" cellpadding="0" cellspacing="0" border="0" class="jlms_table_no_borders">
				<tr>
					<td>
						<input type="text" class="inputbox" name="u_search" value="<?php echo $u_search;?>" />
						<input type="button" value="Search" onclick="javascript:submitbutton('<?php echo $task;?>');" />
					</td>
				</tr>
			</table>
			<table width="100%" cellpadding="0" cellspacing="0" border="0" class="<?php echo JLMSCSS::_('jlmslist');?>">
				<tr>
					<<?php echo JLMSCSS::tableheadertag();?> width="20" class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center">#</<?php echo JLMSCSS::tableheadertag();?>>
					<?php
					if($view_type == 1){
						$view_checkbox = 1;
					} else {
						$view_checkbox = 0;
						foreach($rows as $row){
							if($row->roletype_id == 2){
								$view_checkbox = 1;
								break;
							}
						}
					}
					if ($JLMS_ACL->CheckPermissions('users', 'manage') && $view_checkbox) {
					?>
					<<?php echo JLMSCSS::tableheadertag();?> width="20" class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></<?php echo JLMSCSS::tableheadertag();?>>
					<?php
					} else {
					?>
					<<?php echo JLMSCSS::tableheadertag();?> width="20" class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center">&nbsp;</<?php echo JLMSCSS::tableheadertag();?>>
					<?php	
					}
					?>
					<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>"><?php echo _JLMS_USERS_TBL_HEAD_USERNAME;?></<?php echo JLMSCSS::tableheadertag();?>>
					<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>"><?php echo _JLMS_USERS_TBL_HEAD_USER_ADDINFO;?></<?php echo JLMSCSS::tableheadertag();?>>
					<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>"><?php echo _JLMS_TBL_HEAD_USERS_ROLE;?></<?php echo JLMSCSS::tableheadertag();?>>
				<?php if ($view_type == 1) { ?>
					<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" colspan="2" align="center"><?php echo _JLMS_USERS_TBL_HEAD_USER_ACC_PERIOD;?></<?php echo JLMSCSS::tableheadertag();?>>
					<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>"><?php echo _JLMS_USERS_TBL_HEAD_USER_COMMENT;?></<?php echo JLMSCSS::tableheadertag();?>>
				<?php } else { ?>

				<?php } ?>
				</tr>
			<?php
			$k = 1;
			for ($i=0, $n=count($rows); $i < $n; $i++) {
				$row = $rows[$i];
				$link 	= "index.php?option=".$option."&amp;Itemid=".$Itemid."&amp;task=view_users&amp;course_id=".$course_id."&amp;id=". $row->id;
				$checked = mosHTML::idBox( $i, $row->id);
				/*if ($JLMS_ACL->GetTypeofRole($row->role_id) == 2) {
					$checked = '&nbsp;';
				} elseif (!$JLMS_ACL->isTeacher()) {
					$checked = '&nbsp;';
				}*/
				if (!$JLMS_ACL->CheckPermissions('users', 'manage')) {
					$checked = '&nbsp;';
				}
				if($row->roletype_id == 2){ //Max: no select teachers
					$checked = '&nbsp;';
				}
				if($row->roletype_id == 5){ //Den: do not select assistants who is not in your 'roles assignments' permissions
					$ass_roles = $JLMS_ACL->GetSystemRolesIds(5, true);
					if (is_array($ass_roles) && in_array($row->role_id, $ass_roles)) {
						//ok
					} else {
						$checked = '&nbsp;';
					}
				}
			?>
				<tr class="<?php echo JLMSCSS::_('sectiontableentry'.$k); ?>">
					<td align="center"><?php echo ( $pageNav->limitstart + $i + 1 );?></td>
					<td><?php echo $checked; ?></td>
					<td align="left">
					<?php echo $row->username?>
					</td>
					<td><?php echo $row->name . "(".$row->email.")";?></td>
					<td><?php echo $row->lms_usertype;?></td>
					<?php if ($view_type == 1) { ?>
					<td align="center"><?php echo ($row->publish_start?JLMS_dateToDisplay($row->start_date):'-');?></td>
					<td align="center"><?php echo ($row->publish_end?JLMS_dateToDisplay($row->end_date):'-');?></td>
					<td><?php echo ($row->user_add_comment)?$row->user_add_comment:'&nbsp;';?></td>
					<?php } else { ?>

					<?php } ?>
				</tr>
				<?php
				$k = 3 - $k;
			}?>
				<tr>
					<td colspan="<?php echo (($view_type == 1)?'8':'5');?>" align="center" class="<?php echo JLMSCSS::_('jlmslist-footer_td');?>">
						<div align="center" style="white-space: nowrap;">
						<?php
							if ($view_type == 1) $link = "index.php?option=$option&amp;Itemid=$Itemid&amp;task=view_users&amp;course_id=$course_id&amp;id=$id";
							else $link = "index.php?option=$option&amp;Itemid=$Itemid&amp;task=view_assistants&amp;id=$course_id";
							if($u_search != ''){
								$link .= '&amp;u_search='.$u_search;
							}
							echo _PN_DISPLAY_NR . $pageNav->getLimitBox( $link ).' '.$pageNav->getPagesCounter();
							echo '<br />';
							echo $pageNav->writePagesLinks( $link );
						?>
						</div>
					</td>
				</tr>
			</table>
			<input type="hidden" name="option" value="<?php echo $option;?>" />
			<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
			<input type="hidden" name="task" value="<?php echo $task;?>" />
			<input type="hidden" name="course_id" value="<?php echo $course_id;?>" />
			<input type="hidden" name="id" value="<?php echo $id;?>" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="row_id" value="0" />
			<input type="hidden" name="state" value="0" />
			<input type="hidden" name="utype" value="<?php echo $view_type;?>" />
		</form>
<?php
		JLMS_TMPL::CloseTS();
		$link_add = $JLMS_CONFIG->get('live_site')."/index.php?option=".$option."&amp;task=add_user&amp;course_id=".$course_id."&amp;id=".$id."&amp;Itemid=".$Itemid."";
		$link_import = $JLMS_CONFIG->get('live_site')."/index.php?option=".$option."&amp;task=import_users_csv&amp;course_id=".$course_id."&amp;id=".$id."&amp;Itemid=".$Itemid."";
		if ($view_type == 2) {
			$link_add .= '&amp;utype=2';
			$link_import .= '&amp;utype=2';
		}
		$controls = array();
		if($JLMS_ACL->CheckPermissions('users', 'manage')){
			$controls[] = array('href' => $link_add, 'title' => _JLMS_USER_ALT_ADDUSER, 'img' => 'adduser');
			$controls[] = array('href' => "javascript:submitbutton('delete_user');", 'title' => _JLMS_USER_ALT_DELUSER, 'img' => 'deluser');
			if ($view_type == 1) {
				$controls[] = array('href' => "javascript:submitbutton('edit_user');", 'title' => _JLMS_USER_ALT_EDITUSER, 'img' => 'edituser');
			}
			
			if ($JLMS_ACL->CheckPermissions('users', 'import_users')) {
				$controls[] = array('href' => 'spacer');
				$controls[] = array('href' => $link_import, 'title' => _JLMS_USER_IMPORT_TITLE, 'img' => 'csv_import');
			}
		}
		JLMS_TMPL::ShowControlsFooter($controls, sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid&amp;task=course_users&amp;id=$course_id") );

		JLMS_TMPL::CloseMT();
	}

	function showUsersGlobal( $course_id, $group_id, $option, &$rows, &$lists, &$pageNav, $u_search, $view_type = 1 ) {
		global $Itemid, $JLMS_CONFIG; 
		$JLMS_ACL = & JLMSFactory::getACL();
		?>
		<script language="javascript" type="text/javascript">
		<!--
		function submitbutton(pressbutton) {
			var form = document.adminForm;
			if ((pressbutton == 'edit_user' || pressbutton == 'delete_user') && (form.boxchecked.value == "0")){
				alert( "<?php echo _JLMS_USER_SELECT_USER;?>" );
			} else {
				form.task.value = pressbutton;
				form.submit();
			}
		}
		//-->
		</script>
		<?php
		JLMS_TMPL::OpenMT();

		$hparams = array();
		$toolbar = array();
		$title = '';
		if ($view_type == 1) {
			$title = '&nbsp;'._JLMS_USERS_TITLE;
		} elseif ($view_type == 2) {
			$title = '&nbsp;'._JLMS_USERS_ASSISTS_TITLE;
		}
//		$toolbar[] = array('btn_type' => 'back', 'btn_js' => sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid&amp;task=course_users&amp;id=$course_id"));
		JLMS_TMPL::ShowHeader('usergroup', $title, $hparams, $toolbar);

		if ($view_type == 1) {
			JLMS_TMPL::OpenTS();
			echo $lists['groups'];
			JLMS_TMPL::CloseTS();
		}

		JLMS_TMPL::OpenTS();

		$colspan = $view_type==1 ? 9 : 4;
?>
		<form action="<?php echo $JLMS_CONFIG->get('live_site') ."/index.php?option=".$option."&amp;Itemid=".$Itemid;?>" method="post" name="adminForm">
			<table width="100%" cellpadding="0" cellspacing="0" border="0" class="jlms_table_no_borders" style="margin-top: 2px;">
				<tr>
					<td>
						<input type="text" class="inputbox" name="u_search" value="<?php echo $u_search;?>" />
						<input type="button" value="Search" onclick="javascript:submitbutton('course_users');" />
					</td>
				</tr>
			</table>
			<table width="100%" cellpadding="0" cellspacing="0" border="0" class="<?php echo JLMSCSS::_('jlmslist');?>" style="margin-top: 2px;">
				<tr>
					<<?php echo JLMSCSS::tableheadertag();?> width="20" class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center">#</<?php echo JLMSCSS::tableheadertag();?>>
					<?php
					if ($JLMS_ACL->CheckPermissions('users', 'manage')) {
					?>
					<<?php echo JLMSCSS::tableheadertag();?> width="20" class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></<?php echo JLMSCSS::tableheadertag();?>>
					<?php
					} else {
					?>
					<<?php echo JLMSCSS::tableheadertag();?> width="20" class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center">&nbsp;</<?php echo JLMSCSS::tableheadertag();?>>					
					<?php	
					}
					?>
					<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>"><?php echo _JLMS_USERS_TBL_HEAD_USERNAME;?></<?php echo JLMSCSS::tableheadertag();?>>
					<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>"><?php echo _JLMS_USERS_TBL_HEAD_USER_ADDINFO;?></<?php echo JLMSCSS::tableheadertag();?>>
					<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>"><?php echo _JLMS_USERS_TBL_HEAD_GROUP;?></<?php echo JLMSCSS::tableheadertag();?>>
					<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>"><?php echo _JLMS_TBL_HEAD_USERS_ROLE;?></<?php echo JLMSCSS::tableheadertag();?>>
				<?php if ($view_type == 1) { ?>
					<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" colspan="2" align="center"><?php echo _JLMS_USERS_TBL_HEAD_USER_ACC_PERIOD;?></<?php echo JLMSCSS::tableheadertag();?>>
					<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>"><?php echo _JLMS_USERS_TBL_HEAD_USER_COMMENT;?></<?php echo JLMSCSS::tableheadertag();?>>
				<?php } ?>
				</tr>
			<?php
			$k = 1;
			for ($i=0, $n=count($rows); $i < $n; $i++) {
				$row = $rows[$i];
				$link 	= "index.php?option=".$option."&amp;Itemid=".$Itemid."&amp;task=view_users&amp;course_id=".$course_id."&amp;id=". $row->id;
				$checked = mosHTML::idBox( $i, $row->id); 
				if (!$JLMS_ACL->CheckPermissions('users', 'manage')) {
					$checked = '&nbsp;';
				}
				?>
				<tr class="<?php echo JLMSCSS::_('sectiontableentry'.$k); ?>">
					<td align="center"><?php echo ( $pageNav->limitstart + $i + 1 );?></td>
					<td><?php echo $checked; ?></td>
					<td align="left">
					<?php echo $row->username?>
					</td>
					<td><?php echo $row->name . "(".$row->email.")";?></td>
					<?php if ($view_type == 1) { ?>
					<td align="center"><?php echo $row->ug_name;?></td>
					<td align="center"><?php echo $row->lms_usertype;?></td>
					<td align="center"><?php echo ($row->publish_start?JLMS_dateToDisplay($row->start_date):'-');?></td>
					<td align="center"><?php echo ($row->publish_end?JLMS_dateToDisplay($row->end_date):'-');?></td>
					<td><?php echo ($row->user_add_comment)?$row->user_add_comment:'&nbsp;';?></td>
					<?php } ?>
				</tr>
				<?php
				$k = 3 - $k;
			}?>
				<tr>
					<td colspan="<?php echo (($view_type == 1)?'9':'4');?>" align="center" class="<?php echo JLMSCSS::_('jlmslist-footer_td');?>">
					<div align="center" style="white-space: nowrap;">
					<?php
					$link = "index.php?option=$option&amp;Itemid=$Itemid&amp;task=course_users&amp;id=$course_id&amp;group_id=$group_id";
					if($u_search != ''){
						$link .= '&amp;u_search='.$u_search;
					}
					echo _PN_DISPLAY_NR . $pageNav->getLimitBox( $link ).' '.$pageNav->getPagesCounter();
					echo '<br />';
					echo $pageNav->writePagesLinks( $link ); ?>
					</div>
					</td>
				</tr>
			</table>
			<input type="hidden" name="option" value="<?php echo $option;?>" />
			<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
			<input type="hidden" name="task" value="course_users" />
			<input type="hidden" name="group_id" value="<?php echo $group_id;?>" />
			<input type="hidden" name="id" value="<?php echo $course_id;?>" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="row_id" value="0" />
			<input type="hidden" name="state" value="0" />
			<input type="hidden" name="utype" value="<?php echo $view_type;?>" />
		</form>
<?php
		JLMS_TMPL::CloseTS();
		$link_add = $JLMS_CONFIG->get('live_site')."/index.php?option=".$option."&amp;task=add_user&amp;course_id=".$course_id."&amp;id=0&amp;Itemid=".$Itemid."";
		$link_import = $JLMS_CONFIG->get('live_site')."/index.php?option=".$option."&amp;task=import_users_csv&amp;course_id=".$course_id."&amp;id=0&amp;Itemid=".$Itemid."";
		if ($view_type == 2) {
			$link_add .= '&amp;utype=2';
		}
		$controls = array();
		if ($JLMS_ACL->CheckPermissions('users', 'manage_teachers')) {
			$controls[] = array('href' => "javascript:submitbutton('view_assistants');", 'title' => _JLMS_USER_ASSIST_GROUP_NAME, 'img' => 'editusergroup');
			$controls[] = array('href' => 'spacer');
		}
		if ($JLMS_ACL->CheckPermissions('users', 'manage')) {
			$controls[] = array('href' => $link_add, 'title' => _JLMS_USER_ALT_ADDUSER, 'img' => 'adduser');
			$controls[] = array('href' => "javascript:submitbutton('delete_user');", 'title' => _JLMS_USER_ALT_DELUSER, 'img' => 'deluser');
			if ($view_type == 1) {
				$controls[] = array('href' => "javascript:submitbutton('edit_user');", 'title' => _JLMS_USER_ALT_EDITUSER, 'img' => 'edituser');
			}
			
			if ($JLMS_ACL->CheckPermissions('users', 'import_users')) {
				$controls[] = array('href' => 'spacer');
				$controls[] = array('href' => $link_import, 'title' => _JLMS_USER_IMPORT_TITLE, 'img' => 'csv_import');
			}
			
		}
		JLMS_TMPL::ShowControlsFooter($controls, '' );
		JLMS_TMPL::CloseMT();
	}

	function showImportUsersCSV($option, $course_id, $group_id, $utype, $lists){
		global $Itemid;
		?>
<script language="javascript" type="text/javascript">
<!--
function setgood() {
	return true;
}
function submitbutton(pressbutton) {
	var form = document.importForm;
	try {
		form.onsubmit();
	} catch(e) {
		//alert(e);
	}
	form.task.value = pressbutton;
	form.submit();
}
//-->
</script>
		<?php
		JLMS_TMPL::OpenMT();

		$hparams = array();
		
		$toolbar = array();
		$toolbar[] = array('btn_type' => 'import', 'btn_js' => "javascript:submitbutton('import_users');");
		$toolbar[] = array('btn_type' => 'cancel', 'btn_js' => "javascript:submitbutton('cancel_user');");
		
		JLMS_TMPL::ShowHeader('user', _JLMS_USER_IMPORT_TITLE, $hparams, $toolbar);

		
		JLMS_TMPL::OpenTS();
		?>		
		<?php if ($utype == 1 && !$lists['disabled_import']) { ?>
		<form action="<?php echo sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid");?>" method="post" name="importForm" enctype="multipart/form-data" onsubmit="setgood();">
			<table width="100%" cellpadding="0" cellspacing="0" border="0" class="jlms_table_no_borders">
				<tr>
					<td width="15%" valign="middle" style="vertical-align:middle "><?php echo _JLMS_CHOOSE_FILE;?></td>
					<td>
						<input size="40" class="inputbox" type="file" name="csv_file" />
					</td>
				</tr>
			</table>	
				<?php
				if(isset($lists['role_id']) && $lists['role_id']){
					echo '<input type="hidden" name="role_id" value="'.$lists['role_id'].'" />';
				} else {
					?>
					<table  width="100%" cellpadding="0" cellspacing="0" border="0" class="jlms_table_no_borders">
						<tr>
							<td colspan="2"><div class="contentheading"><?php echo _JLMS_USERS_ROLE;?></div></td>
						</tr>
						<tr>
							<td>
							<?php
							echo $lists['role'];
							?>
							</td>
						</tr>
					</table>
					<?php 
				}
				?>
			<table width="100%" cellpadding="0" cellspacing="0" border="0" class="jlms_table_no_borders">	
				<tr>
					<td colspan="2" valign="top"><br /><?php echo _JLMS_TEACHER_COMMENT;?></td>
				</tr>
				<tr>
					<td colspan="2">
					<?php
						JLMS_editorArea( 'editor2', '', 'teacher_comment', '100%', '250', '40', '20' ) ; ?>
					</td>
				</tr>
			</table>
			<input type="hidden" name="option" value="<?php echo $option;?>" />
			<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
			<input type="hidden" name="task" value="import_users" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="course_id" value="<?php echo $course_id;?>" />
			<input type="hidden" name="group_id" value="<?php echo $group_id;?>" />
		</form>
		<?php
		}
		JLMS_TMPL::CloseTS();
		JLMS_TMPL::CloseMT();
	}
	
	function addUser( $usrs, $option, $course_id, $group_id, $utype, $pageNav, $u_search, &$lists ) {
		global $Itemid, $JLMS_CONFIG;
				
		$user_data = new stdClass();
		
		if ($utype == 1) {			
			$is_lifetime = true;			
			$user_data->publish_start = 1;
			$user_data->publish_end = 0;
			$user_data->start_date = date('Y-m-d');
			$user_data->end_date = date('Y-m-d');
		}	 
		
		$is_dis_start = !(isset($user_data->publish_start) && $user_data->publish_start == 1);
		$is_dis_end = !(isset($user_data->publish_end) && $user_data->publish_end == 1);
?>
<script language="javascript" type="text/javascript">
<!--
window.addEvent('domready', function() {
			document.adminForm.startday.disabled = true;
			document.adminForm.startmonth.disabled = true;
			document.adminForm.startyear.disabled = true;
				
			document.adminForm.endday.disabled = true;
			document.adminForm.endmonth.disabled = true;
			document.adminForm.endyear.disabled = true;		
}
);
function setgood() {
	return true;
}
function jlms_changeUserSelect(c_e) {
	var sel_value = c_e.options[c_e.selectedIndex].value;
	var sel1 = c_e.form.user_name;
	for (var i = 0; i < sel1.options.length; i++) {
		if (sel1.options[i].value == sel_value) {
			sel1.options[i].selected = true;
		}
	}
	var sel2 = c_e.form.user_email;
	for (var i = 0; i < sel2.options.length; i++) {
		if (sel2.options[i].value == sel_value) {
			sel2.options[i].selected = true;
		}
	}
	var sel3 = c_e.form.user_id;
	for (var i = 0; i < sel3.options.length; i++) {
		if (sel3.options[i].value == sel_value) {
			sel3.options[i].selected = true;
		}
	}
}
function submitbutton(pressbutton) {
	var form = document.adminForm;
	try {
		form.onsubmit();
	} catch(e) {
		//alert(e);
	}
	var form2 = document.importForm;
	try {
		form2.onsubmit();
	} catch(e) {
		//alert(e);
	}
	if ((pressbutton == 'add_user_save') && (form.boxchecked.value == '0') ) {
		alert( "<?php echo _JLMS_USER_SELECT_USER;?>" );
	} else if ((pressbutton == 'import_users') && (form2.csv_file.value == '') ) {
		alert( "<?php echo _JLMS_SELECT_FILE;?>" );
	} else if((pressbutton == 'add_user_save') && form.role_id && form.role_id.value == '0'){
		alert( "<?php echo _JLMS_USERS_SELECT_ROLE;?>" );
		form.role_id.focus();
	} else {
		if (pressbutton == 'import_users') {
			form2.task.value = pressbutton;
			form2.submit();
		} else {
			form.task.value = pressbutton;
			form.submit();
		}
	}
}
function f_submitbutton(pressbutton){
	var form = document.filterForm;
	form.task.value = pressbutton;
	form.submit();	
}
<?php if ($utype == 1) { ?>
var is_start_c = <?php echo ($user_data->publish_start)?'1':'0'; ?>; var is_end_c = <?php echo ($user_data->publish_end)?'1':'0'; ?>;
function jlms_Change_start() {
	var form=document.adminForm;
	if (is_start_c == 1) {		
		is_start_c = 0
		form.startday.disabled = true;
		form.startmonth.disabled = true;
		form.startyear.disabled = true;
	} else {
		is_start_c = 1
		form.startday.disabled = false;
		form.startmonth.disabled = false;
		form.startyear.disabled = false;
	}
}
function jlms_Change_end() {
	var form=document.adminForm;
	if (is_end_c == 1) {
		is_end_c = 0
		form.endday.disabled = true;
		form.endmonth.disabled = true;
		form.endyear.disabled = true;
	} else {
		is_end_c = 1
		form.endday.disabled = false;
		form.endmonth.disabled = false;
		form.endyear.disabled = false;
	}
}
function jlms_um_change_type(type_elem) {
	if (type_elem.checked) {
		val = type_elem.value;
		if (val == 1 || val == '1') {
			type_elem.form.publish_end.disabled = true;
			type_elem.form.publish_start.disabled = true;
			type_elem.form.days_number.disabled = true;
			type_elem.form.endday.disabled = true;
			type_elem.form.endmonth.disabled = true;
			type_elem.form.endyear.disabled = true;
			type_elem.form.startday.disabled = true;
			type_elem.form.startmonth.disabled = true;
			type_elem.form.startyear.disabled = true;
		} else if (val == 2 || val == '2') {
			type_elem.form.publish_end.disabled = false;
			type_elem.form.publish_start.disabled = false;
			type_elem.form.days_number.disabled = true;
			if (is_start_c != 1) {
				type_elem.form.startday.disabled = true;
				type_elem.form.startmonth.disabled = true;
				type_elem.form.startyear.disabled = true;
			} else {
				type_elem.form.startday.disabled = false;
				type_elem.form.startmonth.disabled = false;
				type_elem.form.startyear.disabled = false;
			}
			if (is_end_c != 1) {
				type_elem.form.endday.disabled = true;
				type_elem.form.endmonth.disabled = true;
				type_elem.form.endyear.disabled = true;
			} else {
				type_elem.form.endday.disabled = false;
				type_elem.form.endmonth.disabled = false;
				type_elem.form.endyear.disabled = false;
			}
		} else if (val == 3 || val == '3') {
			type_elem.form.publish_end.disabled = true;
			type_elem.form.publish_start.disabled = true;
			type_elem.form.days_number.disabled = false;
			type_elem.form.endday.disabled = true;
			type_elem.form.endmonth.disabled = true;
			type_elem.form.endyear.disabled = true;
			type_elem.form.startday.disabled = true;
			type_elem.form.startmonth.disabled = true;
			type_elem.form.startyear.disabled = true;
		}
	}
}
<?php } /*if ($utype == 1) {*/ ?>
//-->
</script>
<?php
		JLMS_TMPL::OpenMT();

		$hparams = array();
		$toolbar = array();
		$toolbar[] = array('btn_type' => 'save', 'btn_js' => "javascript:submitbutton('add_user_save');");
		$toolbar[] = array('btn_type' => 'cancel', 'btn_js' => "javascript:submitbutton('cancel_user');");
		JLMS_TMPL::ShowHeader('user', (($utype == 1)?_JLMS_USER_ADD_TITLE:_JLMS_USER_ASSIST_ADD_TITLE), $hparams, $toolbar);

		JLMS_TMPL::OpenTS();
?>		
		<form action="<?php echo sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid");?>" method="post" name="filterForm">
			<table  width="100%" cellpadding="0" cellspacing="0" border="0" class="jlms_table_no_borders">
				<tr>
					<td>
						<input type="text" class="inputbox" name="u_search" value="<?php echo $u_search;?>" />
						<input type="button" value="Search" onclick="javascript:f_submitbutton('add_user');" />
					</td>
				</tr>
			</table>
			<input type="hidden" name="option" value="<?php echo $option;?>" />
			<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
			<input type="hidden" name="id" value="0" />
			<input type="hidden" name="task" value="add_user" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="course_id" value="<?php echo $course_id;?>" />
			<input type="hidden" name="group_id" value="<?php echo $group_id;?>" />
			<input type="hidden" name="utype" value="<?php echo $utype;?>" />
		</form>
		<form action="<?php echo sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid");?>" method="post" name="adminForm" onsubmit="setgood();">

			<table width="100%" cellpadding="0" cellspacing="0" border="0" class="<?php echo JLMSCSS::_('jlmslist');?>">
				<tr>
					<<?php echo JLMSCSS::tableheadertag();?> width="20" class="<?php echo JLMSCSS::_('sectiontableheader');?>">#</<?php echo JLMSCSS::tableheadertag();?>>
					<<?php echo JLMSCSS::tableheadertag();?> width="20" class="<?php echo JLMSCSS::_('sectiontableheader');?>">&nbsp;</<?php echo JLMSCSS::tableheadertag();?>>
					<<?php echo JLMSCSS::tableheadertag();?> width="15%" valign="middle" style="vertical-align:middle " class="<?php echo JLMSCSS::_('sectiontableheader');?>">
						<?php echo _JLMS_USER_USERNAME;?>
					</<?php echo JLMSCSS::tableheadertag();?>>
					<<?php echo JLMSCSS::tableheadertag();?> width="15%" valign="middle" style="vertical-align:middle " class="<?php echo JLMSCSS::_('sectiontableheader');?>">
						<?php echo _JLMS_USER_NAME;?>
					</<?php echo JLMSCSS::tableheadertag();?>>
					<<?php echo JLMSCSS::tableheadertag();?> valign="middle" style="vertical-align:middle " class="<?php echo JLMSCSS::_('sectiontableheader');?>">
						<?php echo _JLMS_USER_EMAIL;?>
					</<?php echo JLMSCSS::tableheadertag();?>>
				</tr>
				<?php
				for($i=0;$i<count($usrs);$i++){
				?>
				<tr class="<?php echo JLMSCSS::_('sectiontableentry'.(($i%2)+1));?>">
					<td><?php echo $i+1;?></td>
					<?php $checked = mosHTML::idBox( $i, $usrs[$i]->id); ?>
					<td><?php echo $checked;?></td>
					<td><?php echo $usrs[$i]->username?></td>
					<td><?php echo $usrs[$i]->name?></td>
					<td><?php echo $usrs[$i]->email?></td>
				</tr>
				<?php	
				}
				?>
				<tr>
					<td colspan="5" align="center" class="<?php echo JLMSCSS::_('jlmslist-footer_td');?>">
						<div align="center" style="white-space: nowrap;">
						<?php
							$link = "index.php?option=$option&amp;Itemid=$Itemid&amp;task=add_user&amp;course_id=$course_id&amp;utype=$utype&amp;group_id=$group_id";
							if($u_search != ''){
								$link .= '&amp;u_search='.$u_search;
							}
							echo $pageNav->writePagesLinks( $link );
						?>
						</div>
					</td>
				</tr>
			</table>	
			<?php 
			if ($utype == 2) { 
				if(isset($lists['user_role_id']) && $lists['user_role_id']){
					echo '<input type="hidden" name="role_id" value="'.$lists['user_role_id'].'" />';
				} else {
					?>
					<table  width="100%" cellpadding="0" cellspacing="0" border="0" class="jlms_table_no_borders">
						<tr>
							<td colspan="2"><?php echo JLMSCSS::h2(_JLMS_USERS_ROLE);?></td>
						</tr>
						<tr>
							<td>
							<?php
							echo $lists['user_role'];
							?>
							</td>
						</tr>
					</table>
					<?php 
				}
			}
			?>
			<?php /* Added additional access period information (DEN) - 21 August 2007 */
			if ($utype == 1) {
				if(isset($lists['role_id']) && $lists['role_id']){
					echo '<input type="hidden" name="role_id" value="'.$lists['role_id'].'" />';
				} else {
					?>
					<table width="100%" cellpadding="0" cellspacing="0" border="0" class="jlms_table_no_borders">
						<tr>
							<td colspan="2"><?php echo JLMSCSS::h2(_JLMS_USERS_ROLE);?></td>
						</tr>
						<tr>
							<td>
							<?php
							echo $lists['role'];
							?>
							</td>
						</tr>
					</table>
					<?php 
				}
			?>
			<table width="100%" cellpadding="0" cellspacing="0" border="0" id="jlms_item_properties">
				<tr>
					<td colspan="2"><br /><?php echo JLMSCSS::h2(_JLMS_USERS_TBL_HEAD_USER_ACC_PERIOD);?></td>
				</tr>
				<tr>
					<td colspan="2" style="text-align:left">
					<input onclick="jlms_um_change_type(this);" type="radio" id="access_period_type_1" name="access_period_type" value="1"<?php echo ($is_lifetime)?' checked="checked"':'';?> />
					<label for="access_period_type_1"><?php echo _JLMS_USERS_LIFETIME_ACC;?></label></td>
				</tr>
				<tr>
					<td colspan="2" style="text-align:left"><br />
					<input onclick="jlms_um_change_type(this);" type="radio" id="access_period_type_2" name="access_period_type" value="2"<?php echo (!$is_lifetime)?' checked="checked"':'';?> />
					<label for="access_period_type_2"><?php echo _JLMS_USERS_DTD_ACC;?></label></td>
				</tr>
				<tr>
					<td width="20%" valign="middle"><br /><?php echo _JLMS_START_DATE;?></td>
					<td valign="middle" style="vertical-align:middle "><br />
						<table cellpadding="0" cellspacing="0" border="0" class="jlms_table_no_borders">
							<tr>
								<td valign="middle">
									<input disabled="disabled" type="checkbox" value="1" name="publish_start" onclick="jlms_Change_start()"<?php echo $user_data->publish_start?' checked="checked"':'';?> />
								</td>
								<td valign="middle" style="vertical-align:middle ">
									<?php 
									$s_date = ($is_dis_start)?date('Y-m-d'):$user_data->start_date;
									echo JLMS_HTML::_('calendar.calendar',$s_date,'start','start');
								?>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td valign="middle"><br /><?php echo _JLMS_END_DATE;?></td>
					<td valign="middle" style="vertical-align:middle "><br />
						<table cellpadding="0" cellspacing="0" border="0" class="jlms_date_outer"><tr><td valign="middle">
						<input disabled="disabled" type="checkbox" value="1" name="publish_end" onclick="jlms_Change_end()"<?php echo $user_data->publish_end?' checked="checked"':'';?> /></td>
						<td valign="middle" style="vertical-align:middle ">
						<?php 
						$e_date = ($is_dis_end)?date('Y-m-d'):$user_data->end_date;
						echo JLMS_HTML::_('calendar.calendar',$e_date,'end','end');
						?>
						</td></tr></table>
					</td>
				</tr>
				<tr>
					<td colspan="2" style="text-align:left"><br />
					<input onclick="jlms_um_change_type(this);" type="radio" id="access_period_type_3" name="access_period_type" value="3" />
					<label for="access_period_type_3"><?php echo _JLMS_USERS_XDAYS_ACC;?></label></td>
				</tr>
				<tr>
					<td valign="middle"><br /><?php echo _JLMS_USERS_XDAYS_NUMBER;?></td>
					<td valign="middle" style="vertical-align:middle "><br />
						<input type="text" name="days_number" value="" disabled="disabled" />
					</td>
				</tr>
			
				<tr>
					<td colspan="2" valign="top"><br /><?php echo _JLMS_TEACHER_COMMENT;?></td>
				</tr>
				<tr>
					<td colspan="2">
					<?php
						JLMS_editorArea( 'editor1', '', 'teacher_comment', '100%', '250', '40', '20' ) ; ?>
					</td>
				</tr>
			</table>
			<?php 
			}
			?>
			<input type="hidden" name="option" value="<?php echo $option;?>" />
			<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
			<input type="hidden" name="id" value="0" />
			<input type="hidden" name="task" value="add_user" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="course_id" value="<?php echo $course_id;?>" />
			<input type="hidden" name="group_id" value="<?php echo $group_id;?>" />
			<input type="hidden" name="utype" value="<?php echo $utype;?>" />
		</form>
<?php 
		JLMS_TMPL::CloseTS();
		JLMS_TMPL::CloseMT();
	}

	function editUser( $user_data, $lists, $option, $course_id, $group_id ) {
		global $Itemid, $JLMS_CONFIG;
		
		$is_dis_start = !(isset($user_data->publish_start) && $user_data->publish_start == 1); 
		$is_dis_end = !(isset($user_data->publish_end) && $user_data->publish_end == 1);						
		?>
<script language="javascript" type="text/javascript">
<!--
window.addEvent('domready', function() {
	<?php
		if($is_dis_start) { 
	?>	
			document.adminForm.startday.disabled = true;
			document.adminForm.startmonth.disabled = true;
			document.adminForm.startyear.disabled = true;
	<?php 
		}	
		if($is_dis_end) 
		{ 
	?>			
			document.adminForm.endday.disabled = true;
			document.adminForm.endmonth.disabled = true;
			document.adminForm.endyear.disabled = true;	
	<?php 
		}
	?>	
}
);
function setgood() {
	return true;
}
function submitbutton(pressbutton) {
	var form = document.adminForm;
	try {
		form.onsubmit();
	} catch(e) {
		//alert(e);
	}
	// do field validation
	if((pressbutton == 'edit_user_save') && form.role_id && form.role_id.value == '0'){
		alert( "<?php echo _JLMS_USERS_SELECT_ROLE;?>" );
		form.role_id.focus();
	} else if ((pressbutton == 'edit_user_save') || (pressbutton == 'cancel_user') ) {
		form.task.value = pressbutton;
		form.submit();
	}
}
var is_start_c = <?php echo ($user_data->publish_start)?'1':'0'; ?>; var is_end_c = <?php echo ($user_data->publish_end)?'1':'0'; ?>;
function jlms_Change_start() {
	var form=document.adminForm;
	if (is_start_c == 1) {
		is_start_c = 0
		form.startday.disabled = true;
		form.startmonth.disabled = true;
		form.startyear.disabled = true;
	} else {
		is_start_c = 1
		form.startday.disabled = false;
		form.startmonth.disabled = false;
		form.startyear.disabled = false;
	}
}
function jlms_Change_end() {
	var form=document.adminForm;
	if (is_end_c == 1) {
		is_end_c = 0
		form.endday.disabled = true;
		form.endmonth.disabled = true;
		form.endyear.disabled = true;
	} else {
		is_end_c = 1
		form.endday.disabled = false;
		form.endmonth.disabled = false;
		form.endyear.disabled = false;
	}
}
function jlms_um_change_type(type_elem) {
	if (type_elem.checked) {
		val = type_elem.value;
		if (val == 1 || val == '1') {
			type_elem.form.publish_end.disabled = true;
			type_elem.form.publish_start.disabled = true;
			type_elem.form.days_number.disabled = true;
			type_elem.form.endday.disabled = true;
			type_elem.form.endmonth.disabled = true;
			type_elem.form.endyear.disabled = true;
			type_elem.form.startday.disabled = true;
			type_elem.form.startmonth.disabled = true;
			type_elem.form.startyear.disabled = true;
		} else if (val == 2 || val == '2') {
			type_elem.form.publish_end.disabled = false;
			type_elem.form.publish_start.disabled = false;
			type_elem.form.days_number.disabled = true;
			if (is_start_c != 1) {
				type_elem.form.startday.disabled = true;
				type_elem.form.startmonth.disabled = true;
				type_elem.form.startyear.disabled = true;
			} else {
				type_elem.form.startday.disabled = false;
				type_elem.form.startmonth.disabled = false;
				type_elem.form.startyear.disabled = false;
			}
			if (is_end_c != 1) {
				type_elem.form.endday.disabled = true;
				type_elem.form.endmonth.disabled = true;
				type_elem.form.endyear.disabled = true;
			} else {
				type_elem.form.endday.disabled = false;
				type_elem.form.endmonth.disabled = false;
				type_elem.form.endyear.disabled = false;
			}
		} else if (val == 3 || val == '3') {
			type_elem.form.publish_end.disabled = true;
			type_elem.form.publish_start.disabled = true;
			type_elem.form.days_number.disabled = false;
			type_elem.form.endday.disabled = true;
			type_elem.form.endmonth.disabled = true;
			type_elem.form.endyear.disabled = true;
			type_elem.form.startday.disabled = true;
			type_elem.form.startmonth.disabled = true;
			type_elem.form.startyear.disabled = true;
		}
	}

}
//-->
</script>
<?php
		JLMS_TMPL::OpenMT();

		$hparams = array();
		$toolbar = array();
		$toolbar[] = array('btn_type' => 'save', 'btn_js' => "javascript:submitbutton('edit_user_save');");
		$toolbar[] = array('btn_type' => 'cancel', 'btn_js' => "javascript:submitbutton('cancel_user');");
		JLMS_TMPL::ShowHeader('user', _JLMS_USER_EDIT_TITLE, $hparams, $toolbar);

		JLMS_TMPL::OpenTS();

		$is_lifetime = (!$user_data->publish_start && !$user_data->publish_end);
?>
		<form action="<?php echo sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid");?>" method="post" name="adminForm" onsubmit="setgood();">
			<table width="100%" cellpadding="0" cellspacing="0" border="0" id="jlms_item_properties">
				<tr>
					<td width="15%" valign="middle" style="vertical-align:middle ">
						<?php echo _JLMS_USER_INFO;?>
					</td>
					<td>
					<?php echo $user_data->username.", ".$user_data->name." (".$user_data->email.")"; ?>
					</td>
				</tr>
				<tr>
				<?php if ($JLMS_CONFIG->get('use_global_groups', 1)) {
					?>
					<td colspan="2">
					<input type="hidden" name="group_id" value="<?php echo $group_id; ?>" />
					</td>
					<?php
				} else {
					?>
					<td><br /><?php echo _JLMS_USER_GROUP_INFO;?></td>
					<td><br /><?php echo $lists['groups'];?></td>
					<?php
				}
				?>
				</tr>
				<tr>
					<td colspan="2"><br /><?php echo JLMSCSS::h2(_JLMS_USERS_ROLE);?></td>
				</tr>
				<tr>
					<td>
					<?php echo $lists['role'];?>
					</td>
				</tr>
				<tr>
					<td colspan="2"><br /><?php echo JLMSCSS::h2(_JLMS_USERS_TBL_HEAD_USER_ACC_PERIOD);?></td>
				</tr>
				<tr>
					<td colspan="2" style="text-align:left"><input onclick="jlms_um_change_type(this);" type="radio" id="access_period_type_1" name="access_period_type" value="1"<?php echo ($is_lifetime)?' checked="checked"':'';?> /><label for="access_period_type_1"><?php echo _JLMS_USERS_LIFETIME_ACC;?></label></td>
				</tr>
				<tr>
					<td colspan="2" style="text-align:left"><br /><input onclick="jlms_um_change_type(this);" type="radio" id="access_period_type_2" name="access_period_type" value="2"<?php echo (!$is_lifetime)?' checked="checked"':'';?> /><label for="access_period_type_2"><?php echo _JLMS_USERS_DTD_ACC;?></label></td>
				</tr>
				<tr>
					<td valign="middle"><br /><?php echo _JLMS_START_DATE;?></td>
					<td valign="middle" style="vertical-align:middle "><br />
						<table cellpadding="0" cellspacing="0" border="0" class="jlms_date_outer"><tr><td width="20" valign="middle"><input <?php echo ($is_lifetime)?'disabled="disabled" ':'';?>type="checkbox" value="1" name="publish_start" onclick="jlms_Change_start()"<?php echo $user_data->publish_start?' checked="checked"':'';?> /></td>
						<td valign="middle" style="vertical-align:middle ">
						<?php												
						$s_date = ($is_dis_start)?date('Y-m-d'):$user_data->start_date;
						JLMS_HTML::_('calendar.calendar',$s_date,'start','start');					
						?>
						</td></tr></table>				
					</td>
				</tr>
				<tr>
					<td valign="middle"><br /><?php echo _JLMS_END_DATE;?></td>
					<td valign="middle" style="vertical-align:middle "><br />
						<table cellpadding="0" cellspacing="0" border="0" class="jlms_date_outer"><tr><td width="20" valign="middle"><input <?php echo ($is_lifetime)?'disabled="disabled" ':'';?>type="checkbox" value="1" name="publish_end" onclick="jlms_Change_end()"<?php echo $user_data->publish_end?' checked="checked"':'';?> /></td>
						<td valign="middle" style="vertical-align:middle ">
						<?php						
						$e_date = ($is_dis_end)?date('Y-m-d'):$user_data->end_date;
						JLMS_HTML::_('calendar.calendar',$e_date,'end','end');
						?>					
						</td></tr></table>
					</td>
				</tr>
				<?php if( $user_data->value ) { ?>
					<tr>
					<td valign="middle"><br /><?php echo _JLMS_ENROLL_TIME;?></td>
					<td valign="middle" style="vertical-align:middle "><br />
					<table cellpadding="0" cellspacing="0" border="0" class="jlms_date_outer"><tr><td width="20" valign="middle"></td>
						<td valign="middle" style="vertical-align:middle ">
						<?php JLMS_HTML::_('calendar.calendar',$user_data->enrol_time,'enrol_time','enrol_time', '%Y-%m-%d %H:%M'); ?> 
					</td></tr></table>				
					</td>
					</tr>
				<?php } ?>
				<tr>
					<td colspan="2" style="text-align:left"><br /><input onclick="jlms_um_change_type(this);" type="radio" id="access_period_type_3" name="access_period_type" value="3" /><label for="access_period_type_3"><?php echo _JLMS_USERS_XDAYS_ACC;?></label></td>
				</tr>
				<tr>
					<td valign="middle"><br /><?php echo _JLMS_USERS_XDAYS_NUMBER;?></td>
					<td valign="middle" style="vertical-align:middle "><br />
						<input type="text" name="days_number" value="" disabled="disabled" />
					</td>
				</tr>
				<tr>
					<td colspan="2" valign="top"><br /><?php echo _JLMS_TEACHER_COMMENT;?></td>
				</tr>
				<tr>
					<td colspan="2">
					<?php
						JLMS_editorArea( 'editor1', $user_data->teacher_comment, 'teacher_comment', '100%', '250', '40', '20' ) ; ?>
					</td>
				</tr>
				<?php if ($user_data->spec_reg) {
					echo '<tr><td colspan="2">&nbsp;</td></tr>';
					foreach ($user_data->spec_answers as $ucsra) {
						echo '<tr><td>'.$ucsra->course_question.'</td><td>'.($ucsra->user_answer?$ucsra->user_answer:'&nbsp;').'</td></tr>';
					}
				} ?>
			</table>
			<input type="hidden" name="option" value="<?php echo $option;?>" />
			<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
			<input type="hidden" name="task" value="add_user_save" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="course_id" value="<?php echo $course_id;?>" />
			<input type="hidden" name="user_id" value="<?php echo $user_data->value;?>" />
		</form>
	<?php
		JLMS_TMPL::CloseTS();
		JLMS_TMPL::CloseMT();
	}

	function showImportLog( $import_log, $option, $course_id, $group_id ) {
		global $Itemid;

		JLMS_TMPL::OpenMT();

		$hparams = array();
		$toolbar = array();
		$toolbar[] = array('btn_type' => 'back', 'btn_js' => sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid&amp;task=view_users&amp;course_id=$course_id&amp;id=$group_id"));
		JLMS_TMPL::ShowHeader('user', _JLMS_USER_IMPORT_LOG_TITLE, $hparams, $toolbar);

		JLMS_TMPL::OpenTS();
?>
		<table width="100%" cellpadding="0" cellspacing="0" border="0" class="<?php echo JLMSCSS::_('jlmslist');?>">
			<tr>
				<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>"><?php echo _JLMS_USERS_TBL_HEAD_LOG_USER;?></<?php echo JLMSCSS::tableheadertag();?>>
				<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>"><?php echo _JLMS_USERS_TBL_HEAD_LOG_RESULT;?></<?php echo JLMSCSS::tableheadertag();?>>
			</tr>
			<?php foreach($import_log as $user_log) {
				echo "<tr><td>".$user_log->userinfo."</td><td>".$user_log->result."</td></tr>";
			} ?>
		</table>
<?php
		JLMS_TMPL::CloseTS();
		JLMS_TMPL::CloseMT();
	}

	function confirm_delUsers( &$del_users, &$lists, $option, $course_id, $group_id, $utype, $del_group ) {
		global $Itemid; ?>
<script language="javascript" type="text/javascript">
<!--
function submitbutton(pressbutton) {
	var form = document.adminForm;
	if ((pressbutton == 'user_delete_yes') && (form.boxchecked.value == "0")){
		alert( "<?php echo _JLMS_USER_SELECT_USER;?>" );
	} else {
		form.task.value = pressbutton;
		form.submit();
	}
}
//-->
</script>
	<?php
		JLMS_TMPL::OpenMT();

		$hparams = array();
		if ($utype == 1) {
			$hparams['sys_msg'] = _JLMS_USERS_DEL_ALERT_MESSAGE;
		} elseif ($utype == 2) {
			$hparams['sys_msg'] = _JLMS_USERS_DEL_A_ALERT_MESSAGE;
		}
		$toolbar = array();
		$toolbar[] = array('btn_type' => 'yes', 'btn_js' => "javascript:submitbutton('".($group_id == -1? 'user_delete_yes2': 'user_delete_yes')."');");
		$toolbar[] = array('btn_type' => 'cancel', 'btn_js' => "javascript:submitbutton('".($group_id == -1? 'cancel_csv_delete': 'cancel_user')."');");
		$hparams['toolbar_position'] = 'center';
		JLMS_TMPL::ShowHeader('userman', _JLMS_USERS_DEL_TITLE, $hparams, $toolbar);

		JLMS_TMPL::OpenTS();
?>
		<form action="<?php echo sefRelToAbs("index.php?option=".$option."&amp;Itemid=".$Itemid);?>" method="post" name="adminForm">
			<table width="100%" cellpadding="0" cellspacing="0" border="0" class="<?php echo JLMSCSS::_('jlmslist');?>">
				<tr>
					<<?php echo JLMSCSS::tableheadertag();?> width="20" class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center">#</<?php echo JLMSCSS::tableheadertag();?>>
					<<?php echo JLMSCSS::tableheadertag();?> width="20" class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($del_users); ?>);" /></<?php echo JLMSCSS::tableheadertag();?>>
					<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>"><?php echo _JLMS_USERS_TBL_HEAD_USERNAME;?></<?php echo JLMSCSS::tableheadertag();?>>
					<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>"><?php echo _JLMS_USERS_TBL_HEAD_USER_ADDINFO;?></<?php echo JLMSCSS::tableheadertag();?>>
					<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>"><?php echo _JLMS_USERS_TBL_HEAD_USER_GROUP;?></<?php echo JLMSCSS::tableheadertag();?>>
				</tr>
			<?php
			$k = 1;
			for ($i=0, $n=count($del_users); $i < $n; $i++) {
				$row = $del_users[$i];
				$link 	= "index.php?option=".$option."&amp;Itemid=".$Itemid."&amp;task=view_users&amp;course_id=".$course_id."&amp;id=". $row->id;
				$checked = JLMS_course_users_html::idBox( $i, $row->id, true); ?>
				<tr class="<?php echo JLMSCSS::_('sectiontableentry'.$k); ?>">
					<td align="center"><?php echo ( $i + 1 ); ?></td>
					<td><?php echo $checked; ?></td>
					<td align="left">
					<?php echo $row->username;?>
					</td>
					<td><?php echo $row->name;?> (<?php echo $row->email;?>)</td>
					<?php if ($utype == 1) { ?>
					<td><?php echo ($row->ug_name?$row->ug_name:_JLMS_USER_NO_GROUP_NAME);?></td>
					<?php } else { ?>
					<td><?php echo _JLMS_USER_ASSIST_GROUP_NAME;?></td>
					<?php } ?>
				</tr>
				<?php
				$k = 3 - $k;
			}?>
			</table>
			<input type="hidden" name="option" value="<?php echo $option;?>" />
			<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
			<input type="hidden" name="task" value="delete_user" />
			<input type="hidden" name="course_id" value="<?php echo $course_id;?>" />
			<input type="hidden" name="id" value="<?php echo $group_id;?>" />
			<input type="hidden" name="group_id" value="<?php echo $group_id;?>" />
			<input type="hidden" name="boxchecked" value="<?php echo count($del_users);?>" />
			<input type="hidden" name="row_id" value="0" />
			<input type="hidden" name="state" value="0" />
			<input type="hidden" name="del_group" value="<?php echo $del_group?'1':'0';?>" />
			<input type="hidden" name="utype" value="<?php echo $utype;?>" />
		</form>
	<?php
		JLMS_TMPL::CloseTS();
		JLMS_TMPL::CloseMT();
	}
	function idBox( $rowNum, $recId, $checked=false, $name='cid' ) {
		return '<input type="checkbox" id="cb'.$rowNum.'" name="'.$name.'[]" value="'.$recId.'" onclick="isChecked(this.checked);"'.($checked?' checked="checked"':'').' />';
	}
}
?>