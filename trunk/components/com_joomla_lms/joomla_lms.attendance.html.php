<?php
/**
* joomla_lms.attendance.html.php
* Joomla LMS Component
* * * ElearningForce DK
**/

// no direct access
defined( '_JLMS_EXEC' ) or die( 'Restricted access' );
class JLMS_attendance_html {
	function showExportAT( $id, $option, $lists ) {
		global $Itemid, $my, $JLMS_CONFIG;
		?>		
<script language="javascript" type="text/javascript">
<!--//--><![CDATA[//><!--
function submitbutton(pressbutton) {
	var form = document.adminForm;
	if ((pressbutton == 'at_export') && ((form.at1_date.value == "") || (form.at_date.value == ""))) {
		alert( "<?php echo _JLMS_ALERT_ENTER_PERIOD;?>" );
	} else {
		form.task.value = pressbutton;
		form.submit();
	}
}
//--><!]]>
</script>
<?php
		JLMS_TMPL::OpenMT();

		$hparams = array();
		$toolbar = array();
		$toolbar[] = array('btn_type' => 'export', 'btn_js' => "javascript:submitbutton('at_export');");
		$toolbar[] = array('btn_type' => 'back', 'btn_js' => sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid&amp;task=attendance&amp;id=$id"));
		JLMS_TMPL::ShowHeader('attendance', _JLMS_ATT_TITLE_EXPORT, $hparams, $toolbar);

		JLMS_TMPL::OpenTS();
?>
		<form action="<?php echo sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid");?>" method="post" name="adminForm">
			<table width="100%" cellpadding="0" cellspacing="0" border="0" class="jlms_table_no_borders">
				<tr>
					<td width="15%" valign="middle"><br /><?php echo _JLMS_PERIOD;?></td>
					<td valign="middle" style="vertical-align:middle "><br />
						<?php echo JLMS_HTML::_('calendar.calendar',$lists['at1_date'],'at1','at1', null, null, 'statictext'); ?>						
					</td>
				</tr>	
				<tr>
					<td><br />&nbsp;</td>
					<td valign="middle" style="vertical-align:middle "><br />
						<?php echo JLMS_HTML::_('calendar.calendar',$lists['at_date'],'at','at', null, null, 'statictext'); ?>
					</td>
				</tr>
				<tr>
					<td><br /><?php echo _JLMS_ATT_SELECT_USER;?></td>
					<td><br /><?php echo $lists['at_users'];?>
					<br /><?php echo $lists['at_groups'];?></td>
				</tr>
			</table>
			<input type="hidden" name="option" value="<?php echo $option;?>" />
			<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
			<input type="hidden" name="task" value="at_export" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="id" value="<?php echo $id;?>" />
		</form>
<?php
		JLMS_TMPL::CloseTS();
		JLMS_TMPL::CloseMT();
	}

	function showAttendance( $id, $option, &$rows, &$per_rows, &$pageNav, $at_date, $box, &$lists, $is_teacher = false ) {
		global $Itemid, $my, $JLMS_CONFIG;
		?>		
<script language="javascript" type="text/javascript">
<!--//--><![CDATA[//><!--
function submitbutton_change(pressbutton, period_id, state) {
	var form = document.adminForm;
	if (pressbutton == 'at_periodchange'){
		if (form.boxchecked.value == 0) {
			alert( "<?php echo _JLMS_ALERT_SELECT_ITEM;?>" );
		} else {
			form.task.value = pressbutton;
			form.state.value = state;
			form.period_id.value = period_id;
			form.submit();
		}
	}
}
function submitbutton_change_user(pressbutton, period_id, state, cid_id, at_date_value) {
	var form = document.adminForm;
	if (pressbutton == 'at_change'){
		form.cid2.value = cid_id;		
		form.task.value = pressbutton;
		form.at_date.value = at_date_value;
		form.state.value = state;
		form.period_id.value = period_id;
		form.submit();
	}
}
function pickup_date(){
	var form = document.adminForm;
	form.at_date.value = form.pick_date.value;
	form.submit();
}
//--><!]]>
</script>
<?php
		JLMS_TMPL::OpenMT();

		$hparams = array();
		$toolbar = array();
		if ($is_teacher) {
			$hparams['second_tb_header'] = JLMS_dateToDisplay($at_date);
			$toolbar[] = array('btn_type' => 'export', 'btn_js' => sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid&amp;task=at_pre_export&amp;id=$id"));
		}
		JLMS_TMPL::ShowHeader('attendance', _JLMS_ATT_TITLE, $hparams, $toolbar);
		if (!$is_teacher) {
			JLMS_TMPL::OpenTS('', ' class="contentheading"');
			echo JLMS_dateToDisplay($at_date);
			JLMS_TMPL::CloseTS();
		}

		JLMS_TMPL::OpenTS();
?>
			<form action="<?php echo sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid");?>" method="post" name="adminForm">
				<table width="100%" cellpadding="0" cellspacing="0" border="0" class="jlms_table_no_borders">
					<tr>
						<td align="left" style="text-align:left">
							&nbsp;
						</td>
						<td align="right" style="text-align:right">
							<div align="right" style="white-space:nowrap">
								<table cellpadding="0" cellspacing="0" border="0" align="right" style="height:16px" class="jlms_table_no_borders">
									<tr>
										<td valign="middle" align="center" width="16">
											<?php echo JLMS_HTML::_('calendar.calendar', $at_date, 'pick', 'pick', null, null, 'statictext'); ?>
										</td>
										<td valign="middle" align="center" width="18" style="vertical-align:middle ">
											<a class="jlms_img_link" href="javascript:pickup_date();" title="<?php echo _JLMS_AGENDA_GO_DATE;?>">
												<img class="JLMS_png" src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/agenda/loopnone.png" alt="<?php echo _JLMS_AGENDA_GO_DATE;?>" title="<?php echo _JLMS_AGENDA_GO_DATE;?>" border="0" width="16" height="16" />
											</a>
										</td>
									</tr>
								</table><br />
								<table width="100%" class="jlms_table_no_borders">	
									<tr>
										<td align="right">
										<?php echo (isset($lists['filter']))?$lists['filter']:'';
										 if(isset($lists['filter3'])) {
										 echo '<br />'.$lists['filter3'];
										 }
										?>
										</td>
									</tr>
								</table>	
							</div>
						</td>
					</tr>
				</table>
				<?php
				$at_colspan = 4;
				?>
				<table width="100%" cellpadding="0" cellspacing="0" border="0" class="<?php echo JLMSCSS::_('jlmslist');?>">
					<tr>
						<<?php echo JLMSCSS::tableheadertag();?> width="20" class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center">#</<?php echo JLMSCSS::tableheadertag();?>>
						<<?php echo JLMSCSS::tableheadertag();?> width="20" class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></<?php echo JLMSCSS::tableheadertag();?>>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>"><?php echo _JLMS_ATT_TBL_HEAD_STU;?></<?php echo JLMSCSS::tableheadertag();?>>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>"><?php echo _JLMS_ATT_TBL_HEAD_GROUP;?></<?php echo JLMSCSS::tableheadertag();?>>
						<?php
						foreach ($per_rows as $per_row) {
							$at_colspan++;
							echo '<'.JLMSCSS::tableheadertag().' align=\'center\' nowrap="nowrap" class="'.JLMSCSS::_('sectiontableheader').'" style="text-align:center; white-space:nowrap">'.substr($per_row->period_begin,0,5).' - '.substr($per_row->period_end,0,5).'</'.JLMSCSS::tableheadertag().'>';
						} ?>
					</tr>
				<?php
				$k = 1;
				for ($i=0, $n=count($rows); $i < $n; $i++) {
					$row = $rows[$i];
					$link = "index.php?option=$option&amp;Itemid=$Itemid&amp;task=at_userattend&amp;course_id=$id&amp;at_date=".JLMS_dateToDisplay($at_date)."&amp;id={$row->user_id}";
					$checked = JLMS_attendance_html::idBox( $i, $row->user_id, $row->is_selected);?>
					<tr class="<?php echo JLMSCSS::_('sectiontableentry'.$k); ?>">
						<td align="center" valign="middle"><?php echo ( $pageNav->limitstart + $i + 1 ); ?></td>
						<td><?php echo $checked; ?></td>
						<td align="left" valign="middle" style="vertical-align:middle ">
						 <?php if ($is_teacher) { ?>
							<a href="<?php echo sefRelToAbs($link);?>" title="<?php echo _JLMS_ATT_VIEW_STU_ATTENDANCE;?>">
								<?php echo $row->username;?>
							</a>
						<?php } else { echo $row->username; } ?>
						</td>
						<td align="left" valign="middle" style="vertical-align:middle ">
							<?php echo $row->ug_name;?>
						</td>
						<?php
						$j = 0;
						while ($j < count($row->attendance)) {
							$alt = ($row->attendance[$j]->at_status)?_JLMS_ATT_STATUS_ATTENDED:_JLMS_ATT_STATUS_NOTATTENDED;
							$image = ($row->attendance[$j]->at_status)?'btn_accept.png':'btn_cancel.png';
							$state = ($row->attendance[$j]->at_status)?0:1;
							echo '<td align=\'center\' valign="middle" style="vertical-align:middle;text-align:center ">';
							if ($is_teacher) {
								echo '<a class="jlms_img_link" href="javascript:submitbutton_change_user(\'at_change\','.$row->attendance[$j]->period_id.','.$state.','.$row->user_id.',\''.$at_date.'\');" title="'.$alt.'"><img class="JLMS_png" src="'.$JLMS_CONFIG->get('live_site').'/components/com_joomla_lms/lms_images/toolbar/'.$image.'" width="16" height="16" border="0" alt="'.$alt.'" /></a>';								
							} else {
							echo '<img class="JLMS_png" src="'.$JLMS_CONFIG->get('live_site').'/components/com_joomla_lms/lms_images/toolbar/'.$image.'" width="16" height="16" border="0" alt="'.$alt.'" />';
							}
							echo '</td>';
							$j ++;
						} ?>
					</tr>
					<?php
					$k = 3 - $k;
				}
					if ($is_teacher) {?>
					<tr class="<?php echo JLMSCSS::_('sectiontableentry'.$k); ?>">
						<td colspan="4"><?php echo _JLMS_ATT_WITH_SELECTED;?></td>
						<?php
						foreach ($per_rows as $per_row) {
							echo '<td align=\'center\' valign="middle" style="text-align:center; vertical-align:middle;">';
							echo '<a class="jlms_img_link" href="javascript:submitbutton_change(\'at_periodchange\','.$per_row->id.',1);" title="'._JLMS_ATT_MARK_ATTENDED.'">';
							echo '<img class="JLMS_png" src="'.$JLMS_CONFIG->get('live_site').'/components/com_joomla_lms/lms_images/toolbar/btn_accept.png" width="16" height="16" border="0" alt="'._JLMS_ATT_MARK_ATTENDED.'" />';
							echo '</a>&nbsp;';
							echo '<a class="jlms_img_link" href="javascript:submitbutton_change(\'at_periodchange\','.$per_row->id.',0);" title="'._JLMS_ATT_MARK_NOTATTENDED.'">';
							echo '<img class="JLMS_png" src="'.$JLMS_CONFIG->get('live_site').'/components/com_joomla_lms/lms_images/toolbar/btn_cancel.png" width="16" height="16" border="0" alt="'._JLMS_ATT_MARK_NOTATTENDED.'" />';
							echo '</a>';
							echo '</td>';
						} ?>
					</tr>
				<?php } ?>
					<tr>
						<td align="center" colspan="<?php echo $at_colspan;?>" class="<?php echo JLMSCSS::_('jlmslist-footer_td');?>">
							<div align="center" style="white-space:nowrap">
							<?php
								$link = "index.php?option=$option&amp;Itemid=$Itemid&amp;task=attendance&amp;id=$id";
								echo _JLMS_PN_DISPLAY_NUM . $pageNav->getLimitBox( $link ) . '&nbsp;' . $pageNav->getPagesCounter();;
								echo '<br />';
								echo $pageNav->writePagesLinks( $link );
							?> 
							</div>
						</td>
					</tr>
				</table>
				<input type="hidden" name="option" value="<?php echo $option;?>" />
				<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
				<input type="hidden" name="task" value="attendance" />
				<input type="hidden" name="at_date" value="<?php echo $at_date;?>" />
				<input type="hidden" name="period_id" value="0" />
				<input type="hidden" name="state" value="0" />
				<input type="hidden" name="boxchecked" value="<?php echo $box;?>" />
				<input type="hidden" name="id" value="<?php echo $id;?>" />
				<input type="hidden" name="cid2" value="0" />
			</form>
<?php
		JLMS_TMPL::CloseTS();
		JLMS_TMPL::CloseMT();
	}

function showUserAttendance( $option, &$rows, &$per_rows, &$date_rows, &$lists ) {
		global $Itemid, $my, $JLMS_CONFIG;
		$JLMS_ACL = & JLMSFactory::getACL();
		?>		
<script language="javascript" type="text/javascript">
<!--//--><![CDATA[//><!--
function submitbutton_change(pressbutton, period_id, state) {
	var form = document.adminForm;
	if (pressbutton == 'at_dateschange'){
		if (form.boxchecked.value == 0) {
			alert( "<?php echo _JLMS_ALERT_SELECT_ITEM;?>" );
		} else {
			form.task.value = pressbutton;
			form.state.value = state;
			form.period_id.value = period_id;
			form.submit();
		}
	}
}
function submitbutton_change_user(pressbutton, period_id, state, cid_id, at_date_value) {
	var form = document.adminForm;
	if (pressbutton == 'at_uchange'){
		form.cid2.value = cid_id;		
		form.task.value = pressbutton;
		form.at_date.value = at_date_value;
		form.state.value = state;
		form.period_id.value = period_id;
		form.submit();
	}
}
function pickup_date(){
	var form = document.adminForm;
	form.at_date.value = form.pick_date.value;
	form.submit();
}
//--><!]]>
</script>
<?php
		JLMS_TMPL::OpenMT();

		$hparams = array();
		$toolbar = array();
		if ($JLMS_ACL->CheckPermissions('attendance', 'manage')) {
			$hparams['second_tb_header'] = $lists['username'].',&nbsp;'.$lists['name'];
			$toolbar[] = array('btn_type' => 'back', 'btn_js' => sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid&amp;task=attendance&amp;id=".$lists['course_id']));
		}
		JLMS_TMPL::ShowHeader('attendance', (($JLMS_ACL->CheckPermissions('attendance', 'manage'))?_JLMS_ATT_TITLE:_JLMS_ATT_TITLE_STU), $hparams, $toolbar);
		if ($JLMS_ACL->CheckPermissions('attendance', 'manage')) {
			JLMS_TMPL::OpenTS('', ' class="contentheading"');
			//echo $lists['username'].',&nbsp;'.$lists['name'];
			JLMS_TMPL::CloseTS();
		}

		JLMS_TMPL::OpenTS();
?>
		<form action="<?php echo sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid");?>" method="post" name="adminForm">
			<table width="100%" cellpadding="0" cellspacing="0" border="0" class="jlms_table_no_borders">
				<tr>
					<td align="left" style="text-align:left ">
						<div align="left" style="white-space:nowrap ">
						&nbsp;
						</div>
					</td>
					<td align="right" style="text-align:right ">
						<div align="right" style="white-space:nowrap ">
							<table cellpadding="0" cellspacing="0" border="0" style="height:16px" class="jlms_table_no_borders">
								<tr>
									<td><?php echo $lists['filter'];?>&nbsp;&nbsp;</td>
									<td valign="middle" align="center" width="16">
										<?php echo JLMS_HTML::_('calendar.calendar',$lists['at_date'],'pick','pick', null, null, 'statictext'); ?>				
									</td><td valign="middle" align="center" width="18" style="vertical-align:middle ">
										<a class="jlms_img_link" href="javascript:pickup_date();" title="<?php echo _JLMS_AGENDA_GO_DATE;?>">
											<img class="JLMS_png" src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/agenda/loopnone.png" alt="<?php echo _JLMS_AGENDA_GO_DATE;?>" title="<?php echo _JLMS_AGENDA_GO_DATE;?>" border="0" width="16" height="16" />
										</a>
										<noscript>
											<input type="submit" name="OK" value="OK" />
											<input type="hidden" name="no_script" value="1" />
										</noscript>
									</td>
								</tr>
							</table>	
						</div>
					</td>
				</tr>
			</table>

			<table width="100%" cellpadding="0" cellspacing="0" border="0" class="<?php echo JLMSCSS::_('jlmslist');?>">
				<tr>
					<<?php echo JLMSCSS::tableheadertag();?> width="20" class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center">#</<?php echo JLMSCSS::tableheadertag();?>>
					<?php if ($JLMS_ACL->CheckPermissions('attendance', 'manage')) { ?>
					<<?php echo JLMSCSS::tableheadertag();?> width="20" class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></<?php echo JLMSCSS::tableheadertag();?>>
					<?php } ?>
					<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>">date</<?php echo JLMSCSS::tableheadertag();?>>
					<?php
					foreach ($per_rows as $per_row) {
						echo '<'.JLMSCSS::tableheadertag().' align=\'center\' nowrap="nowrap" class="'.JLMSCSS::_('sectiontableheader').'" style="text-align:center; white-space:nowrap">'.substr($per_row->period_begin,0,5).' - '.substr($per_row->period_end,0,5).'</'.JLMSCSS::tableheadertag().'>';
					} ?>
				</tr>
			<?php
			$k = 1;
			for ($i=0, $n=count($rows); $i < $n; $i++) {
				$row = $rows[$i];
				#$link = "index.php?option=$option&amp;Itemid=".$Itemid."&amp;task=at_userattend&amp;course_id=".$lists['course_id']."&amp;at_date=".$lists['at_date']."&amp;id={$row->user_id}";
				$checked = JLMS_attendance_html::idBox( $i, $row->at_date, $row->is_selected);?>
				<tr class="<?php echo JLMSCSS::_('sectiontableentry'.$k); ?>">
					<td align="center" valign="middle"><?php echo ( /*$pageNav->limitstart +*/ $i + 1 ); ?></td>
					<?php if ($JLMS_ACL->CheckPermissions('attendance', 'manage')) { ?>
					<td><?php echo $checked; ?></td>
					<?php } ?>
					<td align="left" valign="middle" style="vertical-align:middle ">
						<?php echo JLMS_dateToDisplay($row->at_date);?>
					</td>
					<?php
					$j = 0;
					while ($j < count($row->at_stats)) {
						$alt = ($row->at_stats[$j]->at_status)?_JLMS_ATT_STATUS_ATTENDED:_JLMS_ATT_STATUS_NOTATTENDED;
						$image = ($row->at_stats[$j]->at_status)?'btn_accept.png':'btn_cancel.png';
						$state = ($row->at_stats[$j]->at_status)?0:1;
						echo '<td align=\'center\' valign="middle" style="vertical-align:middle;text-align:center ">';
						if ($JLMS_ACL->CheckPermissions('attendance', 'manage')) {
							echo '<a class="jlms_img_link" href="javascript:submitbutton_change_user(\'at_uchange\','.$row->at_stats[$j]->period_id.','.$state.','.$lists['user_id'].',\''.$row->at_date.'\');" title="'.$alt.'"><img class="JLMS_png" src="'.$JLMS_CONFIG->get('live_site').'/components/com_joomla_lms/lms_images/toolbar/'.$image.'" width="16" height="16" border="0" alt="'.$alt.'" /></a>';
						} elseif ($JLMS_ACL->CheckPermissions('attendance', 'view')) {
							echo '<img class="JLMS_png" src="'.$JLMS_CONFIG->get('live_site').'/components/com_joomla_lms/lms_images/toolbar/'.$image.'" width="16" height="16" border="0" alt="'.$alt.'" />';
						}
						echo '</td>';
						$j ++;
					} ?>
				</tr>
				<?php
				$k = 3 - $k;
			}
			if ($JLMS_ACL->CheckPermissions('attendance', 'manage')) {?>
				<tr class="<?php echo JLMSCSS::_('sectiontableentry'.$k); ?>">
					<td colspan="3"><?php echo _JLMS_ATT_WITH_SELECTED;?></td>
					<?php
					foreach ($per_rows as $per_row) {
						echo '<td align=\'center\' valign="middle" style="text-align:center; vertical-align:middle;">';
						echo '<a class="jlms_img_link" href="javascript:submitbutton_change(\'at_dateschange\','.$per_row->id.',1);" title="'._JLMS_ATT_MARK_ATTENDED.'">';
						echo '<img class="JLMS_png" src="'.$JLMS_CONFIG->get('live_site').'/components/com_joomla_lms/lms_images/toolbar/btn_accept.png" width="16" height="16" border="0" alt="'._JLMS_ATT_MARK_ATTENDED.'" />';
						echo '</a>&nbsp;';
						echo '<a class="jlms_img_link" href="javascript:submitbutton_change(\'at_dateschange\','.$per_row->id.',0);" title="'._JLMS_ATT_MARK_NOTATTENDED.'">';
						echo '<img class="JLMS_png" src="'.$JLMS_CONFIG->get('live_site').'/components/com_joomla_lms/lms_images/toolbar/btn_cancel.png" width="16" height="16" border="0" alt="'._JLMS_ATT_MARK_NOTATTENDED.'" />';
						echo '</a>';
						echo '</td>';
					}?>
				</tr>
		<?php } ?>
			</table>
			<input type="hidden" name="option" value="<?php echo $option;?>" />
			<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
			<input type="hidden" name="task" value="<?php echo (($JLMS_ACL->CheckPermissions('attendance', 'manage'))?'at_userattend':'attendance');?>" />
			<input type="hidden" name="at_date" value="<?php echo JLMS_dateToDisplay($lists['at_date']);?>" />
			<input type="hidden" name="period_id" value="0" />
			<input type="hidden" name="state" value="0" />
			<input type="hidden" name="cid2" value="0" />
			<input type="hidden" name="boxchecked" value="<?php echo $lists['box'];?>" />
			<input type="hidden" name="course_id" value="<?php echo $lists['course_id'];?>" />
			<input type="hidden" name="id" value="<?php echo (($JLMS_ACL->CheckPermissions('attendance', 'manage'))?$lists['user_id']:$lists['course_id']);?>" />
		</form>
<?php
		JLMS_TMPL::CloseTS();
		JLMS_TMPL::CloseMT();
	}

	function idBox( $rowNum, $recId, $checked=false, $name='cid' ) {
		return '<input type="checkbox" id="cb'.$rowNum.'" name="'.$name.'[]" value="'.$recId.'"'.($checked?' checked="checked"':'').' onclick="isChecked(this.checked);" />';
	} 
}
?>