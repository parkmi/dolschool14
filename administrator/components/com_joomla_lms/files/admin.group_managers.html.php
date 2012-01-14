<?php
/**
* admin.roles.html.php
* JoomlaLMS Component
*/

// no direct access
//defined( '_VALID_MOS' ) or die( 'Restricted access' );
if (!defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) { die( 'Restricted access' ); }

class ALU_html {
	function JLMS_showGroup_managers( &$rows, &$pageNav, &$lists, $option ) {
		global $JLMS_CONFIG;

		?>
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
		<?php if (!class_exists('JToolBarHelper')) { ?>
		<table class="adminheading" cellpadding="0" cellspacing="0" border="0">
		<tr>
			<th class="categories">
			<?php echo _JOOMLMS_COMP_NAME;?>: <small><?php echo _JLMS_USERS_TBR_GR_MANGRS; ?></small>
			</th>
			<td width="right">
				<table >
					<tr class="row1">
						<td nowrap style="padding:2px 10px 2px 10px; "><?php echo _JLMS_USERS_FILTER_BY_GR; ?>:&nbsp;&nbsp;</td>
						<td><?php echo $lists['jlms_groups'];?></td>
					</tr>
					<tr class="row1">
						<td nowrap style="padding:2px 10px 2px 10px; "><?php echo _JLMS_USERS_FILTER_BY_MNGR; ?>:&nbsp;&nbsp;</td>
						<td><?php echo $lists['jlms_users'];?></td>
					</tr>
					
				</table>
			</td>
		</tr>
		</table>
		<?php } else { ?>
		<div style="width: 100%;">
		<table  align="right" style="width: 30%;">
			<tr class="row1">
				<td nowrap style="padding:2px 10px 2px 10px; "><?php echo _JLMS_USERS_FILTER_BY_GR; ?>:&nbsp;&nbsp;</td>
				<td><?php echo $lists['jlms_groups'];?></td>
			</tr>
			<tr class="row1">
				<td nowrap style="padding:2px 10px 2px 10px; ">
				Filter by manager:&nbsp;&nbsp;
				</td>
				<td>
				<?php echo $lists['jlms_users'];?>
				</td>
			</tr>
		</table>
		</div>
		<div style="clear: both;"><!--x--></div>
		<?php } ?>		
		<table width="100%" border="0">
			<tr>
				<td valign="top">
					<table class="adminlist">
					<thead>
					<tr>
						<th width="20">#</th>
						<th width="20" class="title"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>
						<th class="title" width="25%"><?php echo _JLMS_MANAGER; ?></th>
						<th class="title" width="25%"><?php echo _JLMS_USERNAME; ?></th>
						<th class="title"><?php echo _JLMS_GROUP; ?></th>
					</tr>
					</thead>
					<tfoot>
						<tr>
							<td colspan="5">
							<?php echo $pageNav->getListFooter(); ?>
							</td>
						</tr>
					</tfoot>
					<?php
					$k = 0;
					for ($i=0, $n=count($rows); $i < $n; $i++) {
						$row = $rows[$i];
						$checked = mosHTML::idBox( $i, $row->user_id."_".$row->group_id);?>
						<tr class="<?php echo "row$k"; ?>">
							<td><?php echo $pageNav->rowNumber( $i ); ?></td>
							<td><?php echo $checked; ?></td>
							<td align="left">
								<?php echo $row->name;?>
							</td>
							<td align="left">
								<?php echo $row->username;?>
							</td>
							<td align="left">
								<?php echo $row->ug_name;?>
							</td>
						</tr>
						<?php
						$k = 1 - $k;
					}?>
					</table>
				</td>
			</tr>
		</table>		
	</td></tr></table>
	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	<input type="hidden" name="task" value="group_managers" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="hidemainmenu" value="0" />
	<input type="hidden" name="page" value="" />
	<input type="hidden" name="filt_groups_" value="<?php echo mosGetParam($_REQUEST,'filt_groups');?>" />	
	</form>
		<?php
}
	function editItem( &$row, &$lists, $option, $redirect, $rows_groups ) { 
		?>
	<script type="text/javascript">
	<!--
	function submitbutton(pressbutton) {
		var form = document.adminForm;
		if (pressbutton == 'cancel') {
			form.page.value = pressbutton;
			form.submit();
			return;
		}
		var group_value = form.group_id.value;
		var user_value = form.user_id.value;
		
		if (group_value == 0) {
			alert( '<?php echo _JLMS_USERS_MSG_SLCT_ROLE; ?>' )
		}
		else if (user_value == 0) {
			alert('<?php echo _JLMS_USERS_MSG_SLCT_USER; ?>');
		}
		else {
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
				<div class="width-100">
				<fieldset class="adminform">
			<?php if (!class_exists('JToolBarHelper')) { ?>
				<table class="adminheading">
				<tr>
					<th class="user">
					<?php echo _JOOMLMS_COMP_NAME;?>:
					<small>
					<?php echo ($row->user_id) ? _JLMS_USERS_EDIT_MNGR: _JLMS_USERS_NEW_MNGR;?>
					</small>
					</th>
				</tr>
				</table>
			<?php } ?>			
				<table width="100%" border="0" cellpadding="0" cellspacing="0">
					<tr>
						<td valign="top">
							<table width="100%" >
								<tr>
									<th colspan="2"><?php echo _JLMS_USERS_USR_DETS; ?></th>
								<tr>
							<?php if($row->user_id) {?>
								<tr>
									<td align="right" width="20%"><?php echo _JLMS_USER; ?>:</td>
									<td><?php echo $row->username.' , '.$row->name.'('.$row->email.')';?></td>
								</tr>
							<?php }?>	
								<tr>
									<td align="right" width="20%"><?php echo _JLMS_USERS_SLCT_USR_GR; ?>:</td>
									<td><?php echo $lists['ug_names'];?></td>
								</tr>
							<?php if(!$row->user_id) {?>	
								<tr>
									<td align="right" width="20%"><?php echo _JLMS_USERS_SLCT_USR;  ?>:</td>
									<td><?php echo $lists['users_names'];?></td>
								</tr>
							<?php }?>	
							</table>
						</td>
					</tr>
					<?php if(count($rows_groups)) {?>	
					<tr>
						<td valign="top">
							<table class="adminlist">
								<thead>
									<tr>
										<th class="title" width="100%"><?php echo _JLMS_USERS_MANGD_GR_LIST; ?></th>
									</tr>
								</thead>
								<?php
								$k = 0;
								for ($i=0, $n=count($rows_groups); $i < $n; $i++) {
									$row1 = $rows_groups[$i];?>
									<tr class="<?php echo "row$k"; ?>">
										<td align="left">
											<?php echo $row1->ug_name;?>
										</td>
									</tr>
									<?php
									$k = 1 - $k;
								}?>	
							</table>	
						</td>
					</tr>
					<?php }?>
				</table>
		</td></tr>
		
		</table>
	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	<input type="hidden" name="task" value="group_managers" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="hidemainmenu" value="0" />
	<input type="hidden" name="page" value="" />
	<input type="hidden" name="redirect" value="<?php echo $redirect;?>" />
	
	<?php if($row->user_id) {?>	
		<input type="hidden" name="user_id" value="<?php echo $row->user_id;?>" />
		<input type="hidden" name="old_group_id" value="<?php echo $row->group_id;?>" />
		<input type="hidden" name="edit_manager" value="1" />
	<?php }?>
	</fieldset>
	</div>
	</form>
		<?php
	}	
}
?>