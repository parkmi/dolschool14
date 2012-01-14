<?php
/**
* admin.forums.html.php
* JoomlaLMS Component
*/

// no direct access
//defined( '_VALID_MOS' ) or die( 'Restricted access' );
if (!defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) { die( 'Restricted access' ); }

class ALF_html {
	function showList( &$rows, $option, &$roles ){
		global $JLMS_CONFIG; ?>
	<script type="text/javascript">
	<!--
	function submitbutton(pressbutton) {
		var form = document.adminForm;

		form.page.value = pressbutton;
		form.submit();

	}
	
	<?php if( JLMS_J16version() ) { ?>
	Joomla.submitbutton = submitbutton;
	<?php } ?>
	//-->
	</script>
	<?php

	$forum_warning = false;
	if ($JLMS_CONFIG->get('plugin_forum', 0)) {
		if (!$JLMS_CONFIG->get('forum_path','')) {
			$forum_warning = true;
		} elseif (!file_exists($JLMS_CONFIG->get('forum_path','').'/Settings.php')){
			$forum_warning = true;
		} elseif (substr($JLMS_CONFIG->get('forum_path',''),-1) == '/') {
			$forum_warning = true;
		}
	} else {
		$forum_warning = true;
	}
	 if ($forum_warning) { ?>
	<div class="message">
		<?php echo _JLMS_FRM_MSG_SMF_FRM_N_INSTALL; ?>
	</div>
	<?php } ?>
		<form action="index.php" method="post" name="adminForm">		
		<table width="100%" >
			<tr>
			<td valign="top" width="220px">
			<div>
				<?php echo joomla_lms_adm_html::JLMS_menu();?>
			</div>
			</td>
			<td valign="top">
			<?php if (!class_exists('JToolBarHelper')) { ?>
				<table class="adminheading">
					<tr>
						<th class="dbbackup">
							<?php echo _JOOMLMS_COMP_NAME;?>: <small><?php echo _JLMS_FRM_S;?></small>
						</th>
					</tr>
				</table>
			<?php } ?>
				<div class="width-100">
				<fieldset class="adminform">
				<table width="100%" border="0">
					<tr>
						<td valign="top">
							<table class="adminlist">
							<thead>
								<tr>
									<th width="20px">#</th>
									<th width="20px" class="title"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>
									<th width="16px">&nbsp;</th>
									<th class="title"><?php echo _JLMS_FRM_MSG_FRM_NAME; ?></th>
									<th class="title" width= "20px"><?php echo _JLMS_PUBLISHED; ?></th>
									<th class="title"><?php echo _JLMS_FRM_TYPE; ?></th>
									<th class="title"><?php echo _JLMS_PERMISSIONS; ?></th>
									<th class="title" width="30px"><?php echo _JLMS_ID; ?></th>
								</tr>
							</thead>
							<tbody>
							<?php
							$k = 0;
							for ($i=0, $n=count($rows); $i < $n; $i++) {
								$row = $rows[$i];
								$img 	= $row->published ? 'tick.png' : 'publish_x.png';
								$task 	= $row->published ? 'unpublish' : 'publish';
								$alt 	= $row->published ? _JLMS_PUBLISHED : _JLMS_UNPUBLISHED;
								$checked = mosHTML::idBox( $i, $row->id);
								?>
								<tr class="<?php echo "row$k"; ?>">
									<td><?php echo ($i + 1); ?></td>
									<td><?php echo $checked; ?></td>
									<?php if ($row->parent_forum) {
										$img_p = (isset($rows[$i + 1]) && $rows[$i + 1]->parent_forum == $row->parent_forum) ? 'sub1' : 'sub2';
										echo ($row->parent_forum) ? ('<td width="16"><img src="'.$JLMS_CONFIG->getCfg('live_site')."/components/com_joomla_lms/lms_images/treeview/".$img_p.".png".'" width="16" height="16" alt="L" /></td>') : '';
									} ?>
									<td align="left"<?php echo ($row->parent_forum) ? '' : 'colspan = "2"';?>>
										<?php echo $row->forum_name;?>
									</td>
									<td>
										<a href="javascript: void(0);" onclick="return listItemTask('cb<?php echo $i;?>','<?php echo $task;?>')">
										<img src="<?php echo ADMIN_IMAGES_PATH.$img;?>" border="0" alt="<?php echo $alt; ?>" />
									</td>
									<td>
									<?php
										if ($row->forum_level) {
											echo _JLMS_FRM_LP_SPEC_BOARD;
										} elseif ($row->user_level == 1) {
											echo _JLMS_FRM_CRS_SPEC_BOARD;
										} elseif ($row->user_level == 2) {
											echo _JLMS_FRM_SYS_SPEC_BOARD;
										} else {
											echo _JLMS_FRM_REG_BOARD;
										}
									?>
									</td>
									<td>
									<?php
										if ($row->forum_access) {
											$forum_roles = explode(',',$row->forum_permissions);
											$forum_roles_names = array();
											foreach ($forum_roles as $forum_role) {
												if (isset($roles[$forum_role]) && isset($roles[$forum_role]->role_name)) {
													$forum_roles_names[] = $roles[$forum_role]->role_name;
												}
											}
											if (empty($forum_roles_names)) {
												echo '<font color="red">'._JLMS_FRM_PERM_NOT_CONF.'</font>';
											} else {
												echo _JLMS_FRM_CUSTOM.': '.implode(', ', $forum_roles_names);
											}
										} else {
											echo _JLMS_FRM_ANY_CRS_PART;
										}
									?>
									</td>
									<td align="left">
										<?php echo $row->id;?>
									</td>
								</tr>
								<?php
								$k = 1 - $k;
							}?>
							</tbody>
							</table>
						</td>
					</tr>
				</table>
				</fieldset>
				</div>
		</td></tr></table>

		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="task" value="lms_forums" />
		<input type="hidden" name="page" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0" />		
		</form>
		<?php
	}

	function editItem( &$row, &$lists, &$roles, $option ) { ?>
	<script language="javascript" type="text/javascript">
	<!--
	function submitbutton(pressbutton) {
		var form = document.adminForm;

		if (pressbutton == 'cancel') {
			form.page.value = pressbutton;
			form.submit();
			return;
		}
		if (pressbutton == 'save' || pressbutton == 'apply' ) {
			form.page.value = pressbutton;
			form.submit();
			return;
		}
	}
	
	<?php if( JLMS_J16version() ) { ?>
	Joomla.submitbutton = submitbutton;
	<?php } ?>
	//-->
	</script>	
	<form action="index.php" method="post" name="adminForm">	
	<table width="100%" >
		<tr>
			<td valign="top" width="220px">
			<div>
				<?php echo joomla_lms_adm_html::JLMS_menu();?>
			</div>
			</td>
			<td valign="top">
			<?php if (!class_exists('JToolBarHelper')) { ?>
			<table class="adminheading">
			<tr>
				<th class="user">
				<?php echo _JOOMLMS_COMP_NAME;?>:
				<small>
				<?php echo $row->id ? _JLMS_FRM_EDIT_BOARD : _JLMS_FRM_NEW_BOARD;?>
				</small>
				</th>
			</tr>
			</table>
			<?php } ?>
			<div class="width-100">
			<fieldset class="adminform">
			<table width="100%" border="0">
				<tr>
					<td valign="top">
						<table width="100%" >
							<tr>
								<th colspan="2"><?php echo _JLMS_FRM_BOARD_DETS; ?></th>
							<tr>
							<tr>
								<td align="left" width="20%"><?php echo _JLMS_FRM_BOARD_NAME; ?>:</td>
								<td><input class="text_area" type="text" name="forum_name" style="width:266px;" maxlength="100" value="<?php echo str_replace('"','&quot;', $row->forum_name); ?>" /></td>
							</tr>
							<tr>
								<td align="left"><?php echo _JLMS_PUBLISHED; ?>:</td>
								<td><fieldset class="radio"><?php echo $lists['published'];?></fieldset></td>
							</tr>
							<tr>
								<td align="left"><?php echo _JLMS_FRM_PARENT_BOARD; ?>:</td>
								<td><?php echo $lists['parent_forums'];?></td>
							</tr>
							<tr>
								<td align="left"><?php echo _JLMS_FRM_TYPE; ?>:</td>
								<td><?php echo $lists['forum_type'];?></td>
							</tr>
							<tr>
								<td align="left"><?php echo _JLMS_FRM_SET_OWN_AS_MOD; ?>:</td>
								<td><fieldset class="radio"><?php echo $lists['moderated'];?></fieldset></td>
							</tr>
							<tr>
								<td align="left"><?php echo _JLMS_FRM_ADD_BOARD_MOD; ?>:</td>
								<td><input class="text_area" type="text" name="forum_moderators" style="width:50px;" maxlength="100" value="<?php echo str_replace('"','&quot;', $row->forum_moderators); ?>" /></td>
							</tr>
							<tr>
								<td align="left" valign="top"><?php echo _JLMS_FRM_BOARD_DESC; ?>:</td>
								<td>
								<textarea class="text_area" name="forum_desc" style="width:266px" cols="35" rows="3"><?php echo $row->forum_desc; ?></textarea>
								</td>
							</tr>
							<tr>
								<td align="left" colspan="2"><?php echo _JLMS_FRM_YOU_CAN_USE; ?></td>
							</tr>
							<tr>
								<th colspan="2"><?php echo _JLMS_FRM_ADV_ACC_PERMS; ?></th>
							<tr>
							<tr>
								<td align="left" valign="top" style="vertical-align:top "><?php echo _JLMS_FRM_ADV_ACC; ?>:</td>
								<td><fieldset class="radio"><?php echo $lists['custom_access'];?></fieldset></td>
							</tr>
							<tr>
								<td align="left" valign="top"><?php echo _JLMS_FRM_ADD_PERM; ?>:</td>
								<td>
								<?php
									echo '<select id="roles_selections" class="text_area" multiple="multiple" style="width:266px" size="10" name="forum_permissions[]">';
									$prev_roletype = 0;
									$cur_permissions = explode(',',$row->forum_permissions);
									foreach ($roles as $role) {
										if ($role->roletype_id != $prev_roletype) {
											if ($prev_roletype) { echo '</optgroup>'; }
											$prev_roletype = $role->roletype_id;
											if ($role->roletype_id == 4) {
												echo '<optgroup label="Administrator roles">';
											}
											if ($role->roletype_id == 2) {
												echo '<optgroup label="Teacher roles">';
											}
											if ($role->roletype_id == 5) {
												echo '<optgroup label="Assistant roles">';
											}
										}
										$selected = '';
										if (in_array($role->value, $cur_permissions)) {
											$selected = ' selected="selected"';
										}
										echo '<option value="'.$role->value.'"'.$selected.'>'.$role->text.'</option>';
									}
									echo '</optgroup>';
									echo '</select>';
								?>
								</td>
							</tr>
						</table>
						<br />
					</td>
				</tr>
			</table>
			</fieldset>
			</div>
	</td></tr></table>		
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
		<input type="hidden" name="task" value="lms_forums" />
		<input type="hidden" name="page" value="" />
		</form>		
		<?php	
	}
}
?>