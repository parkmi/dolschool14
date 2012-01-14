<?php
/**
* admin.roles.html.php
* (c) JoomaLMS eLearning Software http://www.joomlalms.com/
**/

// no direct access
if (!defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) { die( 'Restricted access' ); }

class ALR_html {
	
	function showAssignment($option, &$roles, &$assignments){
		global $is_jlms_trial_roles_heading_text;
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
		<form action="index.php" name="adminForm" method="post">
			<fieldset>
				<div style="float: right;">
					<button onclick="submitbutton('save_assignment');window.top.setTimeout('window.parent.document.getElementById(\'sbox-btn-close\').fireEvent(\'click\')', 700);" type="button">
						Save
					</button> 
					<button onclick="submitbutton('save_assignment');" type="button"> <!--window.top.setTimeout('window.parent.document.getElementById(\'sbox-window\').close()', 700);-->
						Apply
					</button>
					<button onclick="window.parent.document.getElementById('sbox-btn-close').fireEvent('click');" type="button">
						Cancel
					</button>
				</div>
				<div class="configuration">
					Roles assignments<?php echo $is_jlms_trial_roles_heading_text ? $is_jlms_trial_roles_heading_text : '';?>
				</div>
			</fieldset>
				
			<fieldset>
				<legend>
					Configuration
				</legend>
				
				<table class="adminlist">
					<thead>
						<tr>
							<th class="title" width="15%">
								&nbsp;
							</th>
							<?php
							foreach($roles as $role){
								?>
								<th class="title">
									<?php
									echo $role->lms_usertype;
									#echo ' (RID: '.$role->id.', RTID: '.$role->roletype_id.')';
									?>
								</th>
								<?php
							}
							?>
						</tr>
					</thead>
					<tbody>
						<?php
						
						$order = array(4,2,5);
						$vroles = array();
						foreach($order as $x){
							foreach($roles as $role){
								if($role->roletype_id == $x){
									$vroles[] = $role;
								}
							}
						}
						
						foreach($vroles as $role){
						?>
						<tr>
							<td>
								<?php
								echo $role->lms_usertype;
								#echo ' (RID: '.$role->id.', RTID: '.$role->roletype_id.')';
								?>
							</td>
							<?php
							$hroles = $roles;
							foreach($hroles as $hrole){
								
								$form_name = 'role_'.$role->id.'[]';
								$checked = '';
								foreach($assignments as $assignment){
									if($role->id == $assignment->role_id && $hrole->id == $assignment->role_assign){
										if($assignment->value){
											$checked = 'checked="checked"';
										}
									} 
								}
								$disabled = '';
								if($role->roletype_id == 5 && in_array($hrole->roletype_id, array(4,3))){
									$disabled = 'disabled="disabled"';
								}
								
								?>
								<td align="center">
									<input type="checkbox" name="<?php echo $form_name;?>" value="<?php echo $hrole->id;?>" <?php echo $checked . $disabled;?> />
								</td>
								<?php
							}
							?>
						</tr>
						<?php
						}
						?>
					</tbody>
				</table>
			</fieldset>
			
			<fieldset style="float: right;">
				<div style="float: right;">
					<button onclick="submitbutton('default_assignment');" type="button" style="white-space: nowrap;">
						Default Assignments
					</button>
				</div>
			</fieldset>
			
			<input type="hidden" name="option" value="<?php echo $option;?>" />
			<input type="hidden" name="task" value="lms_roles" />
			<input type="hidden" name="page" value="" />					
		</form>
		<?php
	}
	
	function showList( &$roles, $option, &$lists ){
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
	<!--temp message-->
	<?php
	if ($JLMS_CONFIG->get('flms_integration', 0)){
	?>
	<div class="message">
		<?php echo _JLMS_ROLES_DONT_CHANGE_ROLES ?>
	</div>
	<?php
	}
	global $is_jlms_trial_roles_page_text;
	if ($is_jlms_trial_roles_page_text) {
	?>
	<div class="message">
		<?php echo $is_jlms_trial_roles_page_text ?>
	</div>
	<?php
	}
	?>
	<!--temp message-->
		<form action="index.php" method="post" name="adminForm">		
		<table width="100%" >
			<tr>
				<td valign="top" width="220">
				<div>
					<?php echo joomla_lms_adm_html::JLMS_menu();?>
				</div>
				</td>
				<td valign="top">
				<?php if (!class_exists('JToolBarHelper')) { ?>
				<table class="adminheading" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<th class="categories">
					<?php echo _JOOMLMS_COMP_NAME;?>: <small><?php echo _JLMS_ROLES_MNGM; ?></small>
					</th>
				</tr>
				</table>
				<?php } ?>				
				<table width="100%" border="0">
					<tr>
						<td valign="top">
							<table class="adminlist">
							<thead>
								<tr>
									<th width="20" align="center">&nbsp;</th>
									<th class="title" colspan="2"><?php echo _JLMS_ROLES_TYPE_NAME; ?></th>
									<th width="32" align="left"><?php echo _JLMS_DEFAULT; ?></th>
									<th width="32" align="left">ID</th>
								</tr>
							</thead>
							<tbody>
							<?php
							$k = 0;
							$latest_rtype = 0;
							$roles_sorted = array();
							$sort_order = array(4,2,5,1,3);
							foreach ($sort_order as $so) {
								foreach ($roles as $rr) {
									if ($rr->roletype_id == $so) {
										$rr1 = new stdClass();
										$rr1->roletype_id = $rr->roletype_id;
										$rr1->lms_usertype = $rr->lms_usertype;
										$rr1->id = $rr->id;
										$roles_sorted[] = $rr;
									}
								}
							}
							
							for ($i=0, $n=count($roles_sorted); $i < $n; $i++) {
								$row = $roles_sorted[$i];
								if ($row->roletype_id) {
									if ($row->roletype_id == 4 || $row->roletype_id == 2 || $row->roletype_id == 5 || $row->roletype_id == 1 || $row->roletype_id == 3) {
										$checked = mosHTML::idBox( $i, $row->id);
									} else {
										$checked = '&nbsp;';
									}
									if ($row->roletype_id != $latest_rtype) {
										$latest_rtype = $row->roletype_id;
										echo '<tr class="row'.$k.'">';
										echo '<td align="center">&nbsp;</td><td colspan="4"><b>';
										switch ($latest_rtype) {
											case 1: echo _JLMS_ROLES_LEARNER_ROLES; break;
											case 2: echo _JLMS_ROLES_TEACHER_ROLES; break;
											case 3: echo _JLMS_ROLES_STAFF_ROLES; break;
											case 4: echo _JLMS_ROLES_ADMIN_ROLES; break;
											case 5: echo _JLMS_ROLES_ASSISTANT_ROLES; break;
											default: echo _JLMS_ROLES_UNKNOWN_ROLES; break;
										}
										echo '</b></td></tr>';
										$k = 1 - $k;
									}
									$aaa = 1;
									if (!isset($roles_sorted[$i+1]) || (isset($roles_sorted[$i+1]) && $roles_sorted[$i+1]->roletype_id != $latest_rtype)) {
										$aaa = 2;
									} 
									?>
								<tr class="<?php echo "row$k"; ?>">
									<td align="center"><?php echo $checked;?></td>
									<td align="center" width="16"><img src="<?php echo $JLMS_CONFIG->getCfg('live_site')."/components/com_joomla_lms/lms_images/treeview/sub".$aaa.".png";?>" /></td>
									<td>
										<?php 
										echo $row->lms_usertype;
										
										?>
									</td>
									<td align="center">
										<?php
										if(isset($row->default_role) && $row->default_role){
											echo '<img src="images/tick.png" alt="" border=0/>';
										}
										?>
									</td>
									<td><?php echo $row->id;?></td>
								</tr>
								<?php
								}
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
		<input type="hidden" name="task" value="lms_roles" />
		<input type="hidden" name="page" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0" />		
		</form>
		<?php
	}

	function editItem( &$row, &$lists, &$text_permisions, &$permissions, $option ) {
		global $is_jlms_trial_roles_heading_text; ?>
		<script language="javascript" type="text/javascript">
		<!--
		function submitbutton(pressbutton) {
			var form = document.adminForm;
			if (pressbutton == 'cancel') {
				form.page.value = pressbutton;
				form.submit();
				return;
			}
			var sel_value = form.roletype_id.options[form.roletype_id.selectedIndex].value;
			var role_name = form.lms_usertype.value;
			if (sel_value == 0 || sel_value == '0') {
				alert('<?php echo _JLMS_ROLES_MSG_SELECT_ROLE_TYPE; ?>')
			} else if (!role_name) {
				alert( '<?php echo _JLMS_ROLES_MSG_ENTER_ROLE_NAME; ?>' )
			} else {
				form.page.value = pressbutton;
				form.submit();
				return;
			}
		}
		
		<?php if( JLMS_J16version() ) { ?>
		Joomla.submitbutton = submitbutton;
		<?php } ?>
		
		//extra BrustersIceCream
//		function JLMS_ceostore_trigger(e){
//			var form = document.adminForm;
//
//			if(e.checked && e.name == 'permissions[ceo][store_manager]'){
//				form['permissions[ceo][corporate_admin]'][0].checked = true;
//				form['permissions[ceo][corporate_admin]'][1].checked = false;
//			}
//			if(e.checked && e.name == 'permissions[ceo][corporate_admin]'){
//				form['permissions[ceo][store_manager]'][0].checked = true;
//				form['permissions[ceo][store_manager]'][1].checked = false;
//			}
//		}
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
							<?php echo $row->id ? _JLMS_ROLES_EDIT_USER_ROLE : _JLMS_ROLES_NEW_USER_ROLE; echo $is_jlms_trial_roles_heading_text ? $is_jlms_trial_roles_heading_text : '';?>
							</small>
							</th>
						</tr>
						</table>
					<?php } ?>									
					<table width="100%" border="0">
						<tr>
							<td valign="top">
								<table width="100%" >
									<tr>
										<th colspan="2"><?php echo _JLMS_ROLES_USER_ROLE_DETAILS; ?></th>
									<tr>
									<tr>
										<td align="right" width="20%"><?php echo _JLMS_ROLES_EDIT_ROLE_NAME; ?>:</td>
										<td><input type="text" name="lms_usertype" class="text_area" style="width:266px;" value="<?php echo $row->lms_usertype;?>" /></td>
									</tr>
									<tr>
										<td align="right" width="20%"><?php echo _JLMS_ROLES_EDIT_ROLE_TYPE; ?>:</td>
										<td><?php echo $lists['role_type'];?></td>
									</tr>
									<?php
									if($row->roletype_id == 2 || $row->roletype_id == 1){
									?>
									<tr>
										<td>
											Default Role:
										</td>
										<td>
											<table>
												<tr>
													<td>
														<input type="radio" name="default_role" value="0" <?php echo (isset($row->default_role) && $row->default_role)?'':'checked="checked"';?> />
													</td>
													<td>
														<?php echo _JLMS_NO;?>
													</td>
													<td>
														<input type="radio" name="default_role" value="1" <?php echo (isset($row->default_role) && $row->default_role)?'checked="checked"':'';?> />
													</td>
													<td>
														<?php echo _JLMS_YES;?>
													</td>
												</tr>
											</table>
										</td>
									</tr>
									<?php
									}
									?>
								</table>
								<br />
							</td>
						</tr>
					</table>
					<table width="100%" border="0">
						<tr>
							<td valign="top">
								<table width="100%" >
									<tr>
										<th><?php echo _JLMS_ROLES_ROLE_PERM; ?></th>
									</tr>
									<tr>
										<td>
										<?php
										if(count($permissions)){
											$old_key = '';
											foreach($permissions as $key=>$tmp_prms){
												if($key != $old_key){
													
													$onclick_ceo_check = '';
													/*if($key == 'ceo'){
														$onclick_ceo_check = 'onclick="JLMS_ceostore_trigger(this);"';
													}*/
													?>
													<fieldset>
														<legend>
															<?php
															echo strtoupper($key);
															?>
														</legend>
														<table >
														<?php
														$tmp_step_permisions = $tmp_prms;
														$k=1;
														foreach($tmp_step_permisions as $text_prms=>$tmp_step_prms){
															?>
															<tr class="row<?php echo $k;?>">
																<td width="20%">
																	<?php
																	$text_info_permission = isset($text_permisions[$key]->$text_prms) ? $text_permisions[$key]->$text_prms : '<i>'.$text_prms.'</i>';
																	echo $text_info_permission.':';
																	?>
																</td>
																<td>
																	<table>
																		<tr>
																			<td>
																				<input type="radio" <?php echo $onclick_ceo_check;?> name="<?php echo 'permissions'.'['.$key.']'.'['.$text_prms.']';?>" value="0" <?php echo (isset($tmp_step_prms) && $tmp_step_prms)?'':'checked="checked"';?> />
																			</td>
																			<td>
																				<?php echo _JLMS_NO;?>
																			</td>
																			<td>
																				<input type="radio" <?php echo $onclick_ceo_check;?> name="<?php echo 'permissions'.'['.$key.']'.'['.$text_prms.']';?>" value="1" <?php echo (isset($tmp_step_prms) && $tmp_step_prms)?'checked="checked"':'';?> />
																			</td>
																			<td>
																				<?php echo _JLMS_YES;?>
																			</td>
																		</tr>
																	</table>
																</td>
															</tr>
															<?php
															$k = 1 - $k;	
														}
														?>
														</table>
													</fieldset>
													<?php
												}
												$old_key = $key;
											}
										} else {
											echo _JLMS_ROLES_MSG_SLCT_ROLE_T;	
										}
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
				</td>
			</tr>
		</table>	
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
		<input type="hidden" name="task" value="lms_roles" />
		<input type="hidden" name="page" value="" />		
		</form>
		<?php
	}
}
?>