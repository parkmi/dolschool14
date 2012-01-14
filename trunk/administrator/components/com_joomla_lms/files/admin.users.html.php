<?php
/**
* admin.roles.html.php
* JoomlaLMS Component
*/

// no direct access
//defined( '_VALID_MOS' ) or die( 'Restricted access' );
if (!defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) { die( 'Restricted access' ); }

class ALU_html {
	
	function showFilterListSystem(&$lists, $add_style=0){
		if($add_style){
		?>
		<div style="width: 100%;">
		<?php
		}
		?>		
		<table  <?php echo $add_style? 'align="right" style="width: 30%;"':'';?>>
			<tr class="row1">
				<td nowrap style="padding:2px 10px 2px 10px; ">
				<?php echo _JLMS_USERS_VIEW_BY; ?>:&nbsp;&nbsp;
				</td>
				<td>
				<?php echo $lists['view_by'];?>
				</td>
			</tr>
			<tr class="row1">
				<td nowrap style="padding:2px 10px 2px 10px; ">
				<?php echo _JLMS_USERS_FILTER_BY_ROLE; ?>:&nbsp;&nbsp;
				</td>
				<td>
				<?php echo $lists['role_filter'];?>
				</td>
			</tr>
			<tr class="row1">
				<td nowrap style="padding:2px 10px 2px 10px; ">
				<?php echo _JLMS_SEARCH; ?>:&nbsp;&nbsp;
				</td>
				<td>
				<input type="text" name="u_search" value="<?php echo $lists['u_search'];?>" class="text_area" style="width:264px" />
				</td>
			</tr>
		</table>		
		<?php
		if($add_style){
		?>
		</div>
		<div style="clear: both;"><!--x--></div>
		<?php
		}
	}
	
	function showFilterListCourse(&$lists, $add_style=0){
		if($add_style){
		?>
		<div style="width: 100%;">
		<?php
		}
		?>
		<table  <?php echo $add_style? 'align="right" style="width: 30%;"':'';?>>
			<tr class="row1">
				<td nowrap style="padding:2px 10px 2px 10px; ">
				<?php echo _JLMS_USERS_VIEW_BY; ?>:&nbsp;&nbsp;
				</td>
				<td>
				<?php echo $lists['view_by'];?>
				</td>
			</tr>
			<tr class="row1">
				<td nowrap style="padding:2px 10px 2px 10px; ">
				<?php echo _JLMS_USERS_FILTER_BY_CRS; ?>:&nbsp;&nbsp;<br />
				</td>
				<td>
				<?php echo $lists['course_filter'];?>
				</td>
			</tr>
			<?php
//			if(isset($lists['course_id']) && $lists['course_id']){
			?>
			<tr class="row1">
				<td nowrap style="padding:2px 10px 2px 10px; ">
				<?php echo _JLMS_USERS_FILTER_BY_ROLE; ?>:&nbsp;&nbsp;
				</td>
				<td>
				<?php echo $lists['role_filter'];?>
				</td>
			</tr>
			<?php
//			}
			?>
			<tr class="row1">
				<td nowrap style="padding:2px 10px 2px 10px; ">
				<?php echo _JLMS_SEARCH; ?>:&nbsp;&nbsp;
				</td>
				<td>
				<input type="text" name="u_search" value="<?php echo $lists['u_search'];?>" class="text_area" style="width:264px" />
				</td>
			</tr>	
		</table>
		<?php
		if($add_style){
		?>
		</div>
		<div style="clear: both;"><!--x--></div>
		<?php
		}
	}
	
	function showListSystem( &$rows, &$pageNav, $option, &$lists ){
		mosCommonHTML::loadOverlib(); ?>
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
		<form action="index.php" method="post" name="adminForm">		
		<table width="100%" >
			<tr>
				<td valign="top" width="220px">
				<div>
					<?php echo joomla_lms_adm_html::JLMS_menu();?>
				</div>
				</td>
				<td valign="top">								
			<?php if (!class_exists('JToolBarHelper')) {?>
				<table class="adminheading">
				<tr>
					<th class="user">
					<?php echo _JOOMLMS_COMP_NAME;?>: <small><?php echo _JLMS_USERS_LIST; ?></small>
					</th>
					<td width="right">
						<?php
							ALU_html::showFilterListSystem($lists);
						?>
					</td>
				</tr>
				</table>
				<?php } else { 
					ALU_html::showFilterListSystem($lists, 1);
				} ?>				
				<table width="100%" border="0">
					<tr>
						<td valign="top">
							<table class="adminlist">
							<thead>
								<tr>
									<th width="20">#</th>
									<th width="20" class="title"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>
									<th class="title"><?php echo _JLMS_USERNAME; ?></th>
									<th class="title"><?php echo _JLMS_NAME; ?></th>
									<th class="title"><?php echo _JLMS_EMAIL; ?></th>
									<th class="title"><?php echo _JLMS_ROLE; ?></th>
									<th class="title"><?php echo _JLMS_GROUPS; ?></th>
								</tr>
							</thead>
							<tfoot>
								<tr>
									<td colspan="7">
									<?php echo $pageNav->getListFooter(); ?>
									</td>
								</tr>
							</tfoot>
							<tbody>
							<?php
							$k = 0;
							for ($i=0, $n=count($rows); $i < $n; $i++) {
								$row = $rows[$i];
								$link 	= 'index.php?option=com_joomla_lms&task=group_managers&filt_groups=0&filt_users='.$row->main_id;
								$checked = mosHTML::idBox( $i, $row->id);?>
								<tr class="<?php echo "row$k"; ?>">
									<td><?php echo $pageNav->rowNumber( $i ); ?></td>
									<td><?php echo $checked; ?></td>
									<td align="left">
										<?php echo $row->username;?>
									</td>
									<td align="left">
										<?php echo $row->name;?>
									</td>
									<td align="left">
										<?php echo $row->email;?>
									</td>
									<td align="left">
										<?php echo $row->lms_usertype;?>
									</td>
									<td align="left">
										<a href="<?php echo $link;?>">
											<?php echo $row->groups;?>
										</a>
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
				</td>
			</tr>
		</table>
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="task" value="lms_users" />
		<input type="hidden" name="page" value="" />
		<input type="hidden" name="course_id" value="0" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0" />		
		</form>
		<?php
	}

	function showListCourse( &$rows, &$pageNav, $option, &$lists ){ ?>
	<script type="text/javascript">	
	<!--
	function submitbutton(pressbutton) {
		var form = document.adminForm;
		if (form.course_id.options[form.course_id.selectedIndex].value == 0 || form.course_id.options[form.course_id.selectedIndex].value == '0') {
			alert('<?php echo _JLMS_USERS_MSG_FLTR_BY_CRS; ?>');
		} else {
			form.page.value = pressbutton;
			form.submit();
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
	<?php if (!class_exists('JToolBarHelper')) {?>	
		<table class="adminheading">
		<tr>
			<th class="user">
			<?php echo _JOOMLMS_COMP_NAME;?>: <small><?php echo _JLMS_USERS_LIST; ?></small>
			</th>
			<td width="right">
				<?php
				ALU_html::showFilterListCourse($lists);				
				?>
			</td>
		</tr>
		</table>
		<?php 
		} else { 
			ALU_html::showFilterListCourse($lists, 1);				
		} 
		?>		
		<table width="100%" border="0">
			<tr>
				<td valign="top">
					<table class="adminlist">
					<thead>
						<tr>
							<th width="20">#</th>
							<th width="20" class="title"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>
							<th class="title"><?php echo _JLMS_USERNAME; ?></th>
							<th class="title"><?php echo _JLMS_NAME; ?></th>
							<th class="title"><?php echo _JLMS_EMAIL; ?></th>
							<th class="title"><?php echo _JLMS_ROLE; ?></th>
							<th class="title"><?php echo _JLMS_COURSE; ?></th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<td colspan="7">
							<?php echo $pageNav->getListFooter(); ?>
							</td>
						</tr>
					</tfoot>
					<tbody>
					<?php
					$k = 0;
					for ($i=0, $n=count($rows); $i < $n; $i++) {
						$row = $rows[$i];
						$checked = mosHTML::idBox( $i, $row->id);?>
						<tr class="<?php echo "row$k"; ?>">
							<td><?php echo $pageNav->rowNumber( $i ); ?></td>
							<td><?php echo $checked; ?></td>
							<?php
							if(!$row->username || !$row->username){
							?>
							<td align="left" colspan="3">
								<?php echo _JLMS_USERS_USR_WAS_REMOVED; ?>
							</td>
							<?php
							} else {
							?>
							<td align="left">
								<?php echo $row->username;?>
							</td>
							<td align="left">
								<?php echo $row->name;?>
							</td>
							<td align="left">
								<?php echo $row->email;?>
							</td>
							<?php
							}
							?>
							<td align="left">
								<?php echo $row->lms_usertype;?>
							</td>
							<td align="left"><?php echo $row->course_name." (ID: ".$row->course_id.")";?></td>
						</tr>
						<?php
						$k = 1 - $k;
					}?>
					</tbody>
					</table>
				</td>
			</tr>
		</table>	
	</td></tr></table>
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="task" value="lms_users" />
		<input type="hidden" name="page" value="" />
		
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0" />		
		</form>
		<?php
	}

	function editItem( &$row, &$lists, $option ) { ?>

<script language="javascript" type="text/javascript">
<!--
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
	if (pressbutton == 'cancel') {
		form.page.value = pressbutton;
		form.submit();
		return;
	}
	var sel_value = form.<?php echo $lists['view_by'] ? 'role_id' : 'lms_usertype_id';?>.options[form.<?php echo $lists['view_by'] ? 'role_id' : 'lms_usertype_id';?>.selectedIndex].value;
<?php if (!$lists['user_id']) {
echo '	var sel_user = form.user_id.options[form.user_id.selectedIndex].value;';
} ?>
	if (sel_value == 0 || sel_value == '0') {
		alert('<?php echo _JLMS_USERS_SCLT_USR_ROLE; ?>')
<?php if (!$lists['user_id']) {
echo '	} else if (sel_user == 0 || sel_user == \'0\') {'."\n";
echo '	alert(\''._JLMS_USERS_SCLT_USR.'\');';
} ?>
	} else {
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
					<?php echo $row->id ? _JLMS_USERS_EDIT_USER : _JLMS_USERS_NEW_USER;?>
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
									<th colspan="2"><?php echo _JLMS_USERS_USR_DETS; ?></th>
								<tr>
							<?php if ($lists['user_id']) { ?>
								<tr>
									<td align="right" width="20%"><?php echo _JLMS_USER; ?>:</td>
									<td><?php echo $lists['user_info'];?></td>
								</tr>
							<?php } else { ?>
								<tr>
									<td align="right" width="20%"><?php echo _JLMS_USERS_SLCT_USRNAME; ?>:</td>
									<td><?php echo $lists['users'];?></td>
								</tr>
								<tr>
									<td align="right" width="20%"><?php echo _JLMS_USERS_OR_NAME; ?>:</td>
									<td><?php echo $lists['users_names'];?></td>
								</tr>
								<tr>
									<td align="right" width="20%"><?php echo _JLMS_USERS_OR_EMAIL; ?>:</td>
									<td><?php echo $lists['users_emails'];?></td>
								</tr>
							<?php } ?>
								<tr>
									<td align="right" width="20%"><?php echo _JLMS_USERS_USER_ROLE; ?>:</td>
									<td><?php echo $lists['roles'];?></td>
								</tr>
								<?php if ($lists['view_by']) { ?>
								<tr>
									<td align="right" width="20%"><?php echo _JLMS_USERS_CRS_NAME; ?>:</td>
									<td><?php echo $lists['course_name'];?></td>
								</tr>
								<?php } ?>
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
		<input type="hidden" name="task" value="lms_users" />
		<input type="hidden" name="course_id" value="<?php echo $lists['course_id'];?>" />
		<input type="hidden" name="view_by" value="<?php echo $lists['view_by'];?>" />
<?php if ($lists['user_id']) { ?>
		<input type="hidden" name="user_id" value="<?php echo $lists['user_id'];?>" />
<?php } ?>
		<input type="hidden" name="page" value="" />		
		</form>
		<?php
	}
}
?>