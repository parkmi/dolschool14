<?php
/**
* joomla_lms.homework.html.php
* (c) JoomaLMS eLearning Software http://www.joomlalms.com/
**/

// no direct access
defined( '_JLMS_EXEC' ) or die( 'Restricted access' );

class JLMS_homework_html {
	function viewHW( $row, $option, $course_id, &$lists, $status ) {
		global $JLMS_CONFIG;
		$Itemid = $JLMS_CONFIG->get('Itemid');

		JLMS_TMPL::OpenMT();

		$hparams = array();
		$toolbar = array();
				
		if( !$status ) {
			$toolbar[] = array('btn_type' => 'complete', 'btn_js' =>"javascript:submitbutton('hw_change');");
		}
		$toolbar[] = array('btn_type' => 'back', 'btn_js' => "javascript:submitbutton('homework');");
		JLMS_TMPL::ShowHeader('homework', $row->hw_name, $hparams, $toolbar);	
				
		JLMS_TMPL::OpenTS();		
?>
		<script language="javascript" type="text/javascript">		
			function submitbutton( pressbutton ) {
				var form=document.adminForm;
				
				<?php if( $row->activity_type == _ACTIVITY_TYPE_UPLOAD ) { ?>
					if( pressbutton == 'hw_uploadfile' && trim(form.userfile.value) == '' ) 
					{
						alert('<?php echo _JLMS_EM_SELECT_FILE; ?>');
					} 
					<?php if( !$row->file ) { ?>
					else if ( pressbutton == 'hw_change' && trim(form.userfile.value) == '' ) 
					{
						alert('<?php echo _JLMS_EM_SELECT_FILE; ?>');
					}
					<?php } ?>
					else {										
						form.task.value = pressbutton;
						form.submit();
					}	
				<?php } else { ?>
					form.task.value = pressbutton;
					form.submit();				
				<?php } ?>
			}
			
			function trim( str ) {
     		   return str.replace(/^\s+|\s+$/g,"");
		    }
		</script>
		<form action="<?php echo sefRelToAbs("index.php?option=".$option."&amp;Itemid=".$Itemid);?>" method="post" name="adminForm" enctype="multipart/form-data">
			<table width="100%" cellpadding="0" cellspacing="0" border="0" class="jlms_table_no_borders">
				<tr>
					<td width="50%" align="left" style="text-align:left "><br />
						<table align="left" width="80%" cellpadding="0" cellspacing="0" border="0" class="jlms_table_no_borders">
						<tr>
							<td width="50%" align="left" style="text-align:left ">&nbsp;&nbsp;<?php echo _JLMS_HW_GRADE;?></td>
							<td width="50%" align="left" style="text-align:left "><?php echo $row->grade_text;?></td>
						</tr>
						<tr>
							<td width="50%" align="left" style="text-align:left ">&nbsp;&nbsp;<?php echo _JLMS_HW_HOMEWORK_DATE;?></td>
							<td width="50%" align="left" style="text-align:left "><?php echo JLMS_dateToDisplay($row->post_date);?></td>
						</tr>
						<tr>
							<td width="50%" align="left" style="text-align:left ">&nbsp;&nbsp;<?php echo _JLMS_HW_HOMEWORK_ENDDATE;?></td>
							<td width="50%" align="left" style="text-align:left "><?php echo JLMS_dateToDisplay($row->end_date);?></td>
						</tr>
						<tr>
							<td align="left" style="text-align:left ">&nbsp;&nbsp;<?php echo _JLMS_HW_DATE_OF_COMPLETING;?></td>
							<td align="left" style="text-align:left ">
								<?php
								if ( $lists['completed'] == _JLMS_HW_STATUS_INCOMPLETE) {
									echo $lists['completed'];
								} else {
									echo JLMS_dateToDisplay($lists['completed'], false, $JLMS_CONFIG->get('offset')*60*60, ' H:i:s');
								} ?>
							</td>
						</tr>
						</table>
					</td>
					<td></td>
				</tr>
				<tr>
					<td colspan="2"><br />
						<?php echo JLMS_ShowText_WithFeatures( $row->hw_description ); ?>
					</td>					
				</tr>
				<tr>
					<td colspan="2" height="20"></td>
				</tr>
				<?php if( $row->comments ) { ?>
					<tr>
						<td>
							<?php echo _JLMS_HW_TEACHER_COMMS;?> 
						</td>
						<td><?php echo JLMS_ShowText_WithFeatures( $row->comments ); ?></td>
					</tr>
					<tr>
						<td colspan="2" height="20"></td>
					</tr>
				<?php } ?>
			</table>
			<table width="100%" cellpadding="0" cellspacing="0" border="0" class="jlms_table_no_borders">										
				<?php 
				switch( $row->activity_type ) { 
				case _ACTIVITY_TYPE_WRITE:
					?>
					<tr>				
						<td colspan="2"><br /><?php echo _JLMS_HW_WRITE_TEXT;?>:</td>
					</tr>
					<tr>
						<td colspan="2">
						<?php
						if( !$status )
							JLMS_editorArea( 'editor1', '', 'write_text', '100%', '250', '40', '10' ) ;
						else
							echo $row->write_text;
						 
						?>
						</td>
					</tr>
					<?php
				break;
				case _ACTIVITY_TYPE_UPLOAD:
					if( !$status ) {
						?>									
						<tr>
						<td width="30%" valign="middle" style="vertical-align:middle"><br /><?php echo _JLMS_CHOOSE_FILE;?></td>
						<td>							
							<input size="40" class="inputbox" type="file" name="userfile" />						
							<input class="inputbox" onclick="submitbutton('hw_uploadfile');" type="button" name="uploadfile" value="<?php echo _JLMS_FILE_UPLOAD; ?>" />													
						</td>
						</tr>								
						<?php
					} 
					?>
					<tr>
						<td colspan="2" height="20"></td>
					</tr>	
					<tr>
					<td> 
						<?php 
						if( $row->file )
							echo _JLMS_ATTACHED_FILE.":";							 
						?>
					</td>					
					<td>
						<?php
							if( !$row->file )
								echo _JLMS_FILE_NOT_ATTACHED;
							else 
								echo '<a href="'.sefRelToAbs( "index.php?option=".$option."&Itemid=".$Itemid."&task=hw_downloadfile&hw_id=".$row->hw_id."&course_id=".$row->course_id."&file_id=".$row->file_id."&user_id=".$row->user_id ).'" >'.$row->file->file_name."</a>".str_repeat('&nbsp;', 5).$row->file->date;
						?>
					</td>
					</tr>					
					<?php						
					 
				}
				?>
				<tr>
					<td colspan="2" height="20"></td>
				</tr>				
			</table>							
			<input type="hidden" name="option" value="<?php echo $option;?>" />
			<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
			<input type="hidden" name="task" value="" />			
			<input type="hidden" name="course_id" value="<?php echo $course_id;?>" />
			<input type="hidden" name="hw_id" value="<?php echo $row->hw_id;?>" />			
			<input type="hidden" name="id" value="<?php echo $row->id;?>" />	
			</form>					
<?php
		JLMS_TMPL::CloseTS();
		JLMS_TMPL::CloseMT();
	}
	
	function viewHWResult( $row, $option, &$lists ) {
		global $JLMS_CONFIG;
		$Itemid = $JLMS_CONFIG->get('Itemid');
?>
	<script language="javascript" type="text/javascript">
	<!--//--><![CDATA[//><!--
		function submitbutton( pressbutton ) {
			var form=document.adminForm;
							
			form.task.value = pressbutton;
			form.submit();				
		}
		
		function trim( str ) {
 		   return str.replace(/^\s+|\s+$/g,"");
	    }
  //--><!]]>
  </script>
<?php
		JLMS_TMPL::OpenMT();

		$hparams = array();
		$toolbar = array();
	
		$toolbar[] = array('btn_type' => 'save', 'btn_js' =>"javascript:submitbutton('hw_save_result');");		
		$toolbar[] = array('btn_type' => 'back', 'btn_js' => "javascript:submitbutton('hw_stats');");
		JLMS_TMPL::ShowHeader('homework', $row->hw->hw_name, $hparams, $toolbar);	

		JLMS_TMPL::OpenTS();
?>

		<form action="<?php echo sefRelToAbs("index.php?option=".$option."&amp;Itemid=".$Itemid);?>" method="post" name="adminForm" enctype="multipart/form-data">
			<table width="100%" cellpadding="0" cellspacing="0" border="0" class="jlms_table_no_borders">
				<tr>
					<td width="50%" align="left" style="text-align:left "><br />
						<table align="left" width="80%" cellpadding="0" cellspacing="0" border="0" class="jlms_table_no_borders">
						<tr>
							<td width="50%" align="left" style="text-align:left ">&nbsp;&nbsp;<?php echo _JLMS_UI_USERNAME;?></td>
							<td width="50%" align="left" style="text-align:left "><?php echo $row->user->username;?></td>
						</tr>
						<tr>
							<td width="50%" align="left" style="text-align:left ">&nbsp;&nbsp;<?php echo _JLMS_UI_NAME;?></td>
							<td width="50%" align="left" style="text-align:left "><?php echo $row->user->name;?></td>
						</tr>
						<tr>
							<td width="50%" align="left" style="text-align:left ">&nbsp;&nbsp;<?php echo _JLMS_UI_EMAIL;?></td>
							<td width="50%" align="left" style="text-align:left "><?php echo $row->user->email;?></td>
						</tr>
						<tr>
							<td width="50%" align="left" style="text-align:left ">&nbsp;&nbsp;<?php echo _JLMS_UI_GROUP;?></td>
							<td width="50%" align="left" style="text-align:left "><?php echo $row->user->group;?></td>
						</tr>						
						</table>
					</td>
					<td width="50%" align="left" style="text-align:left "><br />
					<table align="left" width="80%" cellpadding="0" cellspacing="0" border="0" class="jlms_table_no_borders">
						<tr>
							<td width="50%" align="left" style="text-align:left ">&nbsp;&nbsp;<?php echo _JLMS_HW_HOMEWORK_DATE;?></td>
							<td width="50%" align="left" style="text-align:left "><?php echo JLMS_dateToDisplay($row->hw->post_date);?></td>
						</tr>
						<tr>
							<td width="50%" align="left" style="text-align:left ">&nbsp;&nbsp;<?php echo _JLMS_HW_HOMEWORK_ENDDATE;?></td>
							<td width="50%" align="left" style="text-align:left "><?php echo JLMS_dateToDisplay($row->hw->end_date);?></td>
						</tr>
						<tr>
							<td align="left" style="text-align:left ">&nbsp;&nbsp;<?php echo _JLMS_HW_DATE_OF_COMPLETING;?></td>
							<td align="left" style="text-align:left ">
								<?php							
									echo JLMS_dateToDisplay($row->hw_date, false, $JLMS_CONFIG->get('offset')*60*60, ' H:i:s');													 ?>
							</td>
						</tr>
						</table>
					</td>
				</tr>										
				<tr>
					<td colspan="2"><br />
						<?php echo JLMS_ShowText_WithFeatures( $row->hw->hw_description ); ?>
					</td>
				</tr>
				<tr>
					<td colspan="2" height="20"><br /></td>
				</tr>
				<?php if( $row->hw->activity_type != _ACTIVITY_TYPE_OFFLINE ) { ?>
				<tr>
					<td><b><?php echo _JLMS_HW_LEARNERS_SUBS; ?></b></td>
					<td>						
						<?php
							if( $row->hw->activity_type == _ACTIVITY_TYPE_WRITE ) 
								echo $row->write_text;
							else if( $row->hw->activity_type == _ACTIVITY_TYPE_UPLOAD && $row->file )								
								echo '<a href="'.sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=hw_downloadfile&hw_id=$row->hw_id&course_id=$row->course_id&file_id=$row->file_id&user_id=".$row->user_id).'" >'.$row->file->file_name."</a>";
						?>
					</td>					
				</tr>
				<tr>
					<td colspan="2" height="20"><br /></td>
				</tr>
				<?php } ?>
				<tr>
					<td><b><?php echo _JLMS_HW_GRADE_THIS_ACTIVITY; ; ?></b></td>
					<td>
						<?php echo $lists['grade']; ?>
					</td>					
				</tr>
				<tr>
					<td colspan="2" height="20"><br /></td>
				</tr>
				<tr>
					<td colspan="2">
					<?php echo _JLMS_COMMENTS;	?>
					</td>
				</tr>								
				<tr>
					<td colspan="2">
					<?php						
					JLMS_editorArea( 'editor1', $row->comments, 'comments', '100%', '250', '40', '10' ) ;						 
					?>
					</td>
				</tr>					
			</table>		
			<input type="hidden" name="option" value="<?php echo $option;?>" />
			<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
			<input type="hidden" name="task" value="" />			
			<input type="hidden" name="course_id" value="<?php echo $row->course_id;?>" />
			<input type="hidden" name="user_id" value="<?php echo $row->user_id;?>" />
			<input type="hidden" name="hw_id" value="<?php echo $row->hw_id;?>" />
			<input type="hidden" name="id" value="<?php echo $row->id;?>" />
		</form>
<?php
		JLMS_TMPL::CloseTS();
		JLMS_TMPL::CloseMT();
	}

	function statsHW( &$stats, &$row, $option, $course_id, &$pageNav, &$lists, $is_teacher = true ) {
		global $JLMS_CONFIG;
		$Itemid = $JLMS_CONFIG->get('Itemid');
		
		$JLMS_ACL = & JLMSFactory::getACL();
		
		JLMS_TMPL::OpenMT();
		
		$hparams = array();
		JLMS_TMPL::ShowHeader('homework', '', $hparams);//$row->hw_name

		JLMS_TMPL::OpenTS();
?>
			<table width="100%" cellpadding="0" cellspacing="0" border="0" class="jlms_table_no_borders">
				<tr>
					<td colspan="2" width="80%">
						<?php echo JLMSCSS::h2($row->hw_name);?>
					</td>
					<td align="right" style="text-align:right; vertical-align:top " valign="top"><br />
					<?php $toolbar = array();
					$toolbar[] = array('btn_type' => 'back', 'btn_js' => sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=homework&id=$course_id"));
					echo JLMS_ShowToolbar($toolbar); ?>
					</td>
				</tr>
				<tr>
					<td width="60%" align="left" style="text-align:left "><br />
						<table align="left" width="100%" cellpadding="0" cellspacing="0" border="0" class="jlms_table_no_borders">
<?php /*						<tr>
							<td width="50%" align="left" style="text-align:left ">&nbsp;&nbsp;<?php echo _JLMS_HW_HOMEWORK_TASK;?></td>
							<td width="50%" align="left" style="text-align:left "><?php echo $row->hw_name;?></td>
						</tr>
*/ ?>
						<tr>
							<td width="50%" align="left" style="text-align:left ">&nbsp;&nbsp;<?php echo _JLMS_HW_HOMEWORK_DATE;?></td>
							<td width="50%" align="left" style="text-align:left "><?php echo JLMS_dateToDisplay($row->post_date);?></td>
						</tr>
						<tr>
							<td width="50%" align="left" style="text-align:left ">&nbsp;&nbsp;<?php echo _JLMS_HW_HOMEWORK_ENDDATE;?></td>
							<td width="50%" align="left" style="text-align:left "><?php echo JLMS_dateToDisplay($row->end_date);?></td>
						</tr>
						</table>
					</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>

				</tr>
				<tr>
					<td colspan="3"><br />
						<table width="100%" cellpadding="0" cellspacing="0" border="0" class="jlms_table_no_borders">
							<tr>
								<td align="left" style="text-align:left; vertical-align:bottom " valign="bottom">
									<div><?php echo $lists['filter'];?></div>
								</td>
								<td align="right" style="text-align:right ">
									<div align="right" style="white-space:nowrap ">
									<?php 
									if ($JLMS_ACL->CheckPermissions('homework', 'manage')) { 
									?>
										<?php 
										echo ($lists['filter2'].'<br />');
										
										if(isset($lists['filter3'])) {
										 echo $lists['filter3'].'<br />';
										}
										 
										echo ($lists['filter_stu'].'<br />');?>
									<?php } else { echo '&nbsp;'; } ?>
									</div>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td colspan="3">
						<!-- start statistics -->
						<table width="100%" cellpadding="0" cellspacing="0" border="0" class="<?php echo JLMSCSS::_('jlmslist');?>">
							<tr>
								<<?php echo JLMSCSS::tableheadertag();?> width="20" class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center">#</<?php echo JLMSCSS::tableheadertag();?>>
								<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>"><?php echo _JLMS_HW_TBL_HEAD_STU;?></<?php echo JLMSCSS::tableheadertag();?>>
								<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>"><?php echo _JLMS_HW_TBL_HEAD_GROUP;?></<?php echo JLMSCSS::tableheadertag();?>>
								<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" colspan="2"><?php echo _JLMS_HW_TBL_HEAD_GRADE;?></<?php echo JLMSCSS::tableheadertag();?>>
								<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" width="20%">&nbsp;</<?php echo JLMSCSS::tableheadertag();?>>
							</tr>
						<?php
						$k = 1;
						for ($i=0, $n=count($stats); $i < $n; $i++) {
							$srow = $stats[$i];
							$mail_link = "index.php?option=$option&amp;Itemid=$Itemid&amp;task=hw_view_result&amp;course_id=$course_id&amp;user_id=".$srow->user_id."&amp;hw_id=".$row->id;
							$image = $srow->hw_status ? 'btn_accept.png' : 'btn_cancel.png';
							$alt = $srow->hw_status ? _JLMS_HW_STATUS_COMPLETED : _JLMS_HW_STATUS_INCOMPLETE;
							?>
							<tr class="<?php echo JLMSCSS::_('sectiontableentry'.$k);?>">
								<td align="center"><?php echo ( $pageNav->limitstart + $i + 1 ); ?></td>
								<td align="left" valign="middle" style="vertical-align:middle ">
								<?php
								if ($JLMS_ACL->CheckPermissions('homework', 'manage')) {
									if( $srow->hw_status ) {
										echo '<a href="'.sefRelToAbs($mail_link).'" title="'._JLMS_HW_LINK_SEND_EMAIL.'">';
									}
									echo $srow->username;
									if( $srow->hw_status ) {
										echo '</a>';
									}
								} else {
									echo $srow->username;
								}
								?>
								</td>
								<td align="left" valign="middle" style="vertical-align:middle ">
									<?php 
//										echo ($srow->group_id?$srow->ug_name:_JLMS_USER_NO_GROUP_NAME);
										echo $srow->ug_name;
									?>
								</td>
								<td align="center" valign="middle" style="vertical-align:middle" width="20">
									<?php
										$grade_num = '';										
										if( $srow->hw_status ) {
											if( $srow->grade != _STATUS_NOT_SELECT && $srow->grade ) {
												$img = '';
												if( $srow->graded_activity ) 
												{												
													$img = 'btn_accept.png';
													$grade_num = $srow->grade;												
												} else {
													switch( $srow->grade ) 
													{													
														case _STATUS_NOT_PASSED:														
															$img = 'btn_cancel.png';
														break;
														case _STATUS_PASSED:
															$img = 'btn_accept.png';
														break;	
													}
												}
												echo '<img src="'.JURI::root().'/components/com_joomla_lms/lms_images/toolbar/'.$img.'" alt="'.$srow->grade.'" />';
											} else {
												$ttip = '<img src="'.JURI::root().'/components/com_joomla_lms/lms_images/toolbar/btn_publish_wait.png" alt="waiting" />';
												echo JLMS_toolTip(_JLMS_HW_AWAITING_REVIEW, '', $ttip, '', false);
											}
										}										
									?>
								</td>
								<td><?php echo $grade_num ? ('<b>'.$grade_num.'</b>') : '&nbsp;';?></td>
								<td align="left" valign="middle" style="vertical-align:middle ">
									<?php echo JLMS_dateToDisplay($srow->hw_date, false, $JLMS_CONFIG->get('offset')*60*60, ' H:i:s');?>
								</td>
							</tr>
							<?php
							$k = 3 - $k;
						}?>
						<tr>
							<td colspan="6" align="center"class="<?php echo JLMSCSS::_('jlmslist-footer_td');?>"><div align="center" style="white-space:nowrap">
							<?php
								$link = "index.php?option=$option&amp;Itemid=$Itemid&amp;task=hw_stats&amp;course_id=$course_id&amp;id={$row->id}";
								echo _JLMS_PN_DISPLAY_NUM . $pageNav->getLimitBox( $link ) . '&nbsp;' . $pageNav->getPagesCounter();
								echo '<br />';
								echo $pageNav->writePagesLinks( $link );
							?> 
							</div></td>
						</tr>
						</table>
						<!-- end statistics -->
					</td>
				</tr>
			</table>
<?php
		JLMS_TMPL::CloseTS();
		JLMS_TMPL::CloseMT();
	}

	function publishIcon( $id, $course_id, $user_id, $state, $task, $alt, $image, $option ) {
		global $JLMS_CONFIG;
		$Itemid = $JLMS_CONFIG->get('Itemid');
		$link = sefRelToAbs("index.php?option=".$option."&Itemid=".$Itemid."&task=".$task."&state=".$state."&id=".$course_id."&user_id=".$user_id."&cid2=".$id);
		return '<a href="'.$link.'" title="'.$alt.'" class="jlms_img_link"><img class="JLMS_png" src="'.$JLMS_CONFIG->get('live_site').'/components/com_joomla_lms/lms_images/toolbar/'.$image.'" width="16" height="16" border="0" alt="'.$alt.'" /></a>';
	}
	function showEditHW( $hw_details, $lists, $option, $id, & $params) {
		global $JLMS_CONFIG;
		$Itemid = $JLMS_CONFIG->get('Itemid');
		?>
<script language="javascript" type="text/javascript">
<!--//--><![CDATA[//><!--
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
	if (form.post_date.value == ''){jlms_getDate('post');}
	if (form.end_date.value == ''){jlms_getDate('end');}
	if ((pressbutton == 'hw_save') && (form.hw_name.value == "")) {
		alert( "<?php echo _JLMS_HW_ENTER_HW_NAME;?>" );
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
		$toolbar[] = array('btn_type' => 'save', 'btn_js' => "javascript:submitbutton('hw_save');");
		$toolbar[] = array('btn_type' => 'cancel', 'btn_js' => "javascript:submitbutton('hw_cancel');");
		JLMS_TMPL::ShowHeader('homework', $hw_details->id ? _JLMS_HW_EDIT_HW : _JLMS_HW_CREATE_HW, $hparams, $toolbar);

		JLMS_TMPL::OpenTS();
?>
		<form action="<?php echo sefRelToAbs("index.php?option=$option&Itemid=$Itemid");?>" method="post" name="adminForm" onsubmit="setgood();">
			<table width="100%" cellpadding="0" cellspacing="0" border="0" id="jlms_item_properties">
				<tr>
					<td width="15%" valign="middle" style="vertical-align:middle "><?php echo _JLMS_ENTER_NAME;?></td>
					<td>
						<input size="40" class="inputbox" type="text" name="hw_name" value="<?php echo str_replace('"','&quot;',$hw_details->hw_name);?>" />
					</td>
				</tr>
				<tr>
					<td valign="middle"><br /><?php echo _JLMS_DATE;?></td>
					<td valign="middle" style="vertical-align:middle "><br />
						<table cellpadding="0" cellspacing="0" border="0" class="jlms_date_outer"><tr>
						<td valign="middle" style="vertical-align:middle ">
						<?php echo JLMS_HTML::_('calendar.calendar',$hw_details->post_date,'post','post'); ?>					
						</td></tr></table>
					</td>
				</tr>	
				<tr>
					<td><br /><?php echo _JLMS_END_DATE;?></td>
					<td valign="middle" style="vertical-align:middle "><br />
						<table cellpadding="0" cellspacing="0" border="0" class="jlms_date_outer"><tr>
						<td valign="middle" style="vertical-align:middle ">
						<?php echo JLMS_HTML::_('calendar.calendar',$hw_details->end_date,'end','end'); ?>
						</td></tr></table>
					</td>
				</tr>
				<tr>
					<td colspan="2" height="20"></td>
				</tr>				
				<tr>
					<td>
					<?php echo _JLMS_STATUS_PUB;?>
					</td>
					<td>
						<?php echo $lists['published'];?>
					</td>
				</tr>
				<tr>
					<td colspan="2" height="20"></td>
				</tr>
				<tr>
					<td>
					<?php echo _JLMS_HW_ACTIVITY_TYPE;?>
					</td>
					<td>
						<?php echo $lists['activity_type'];?>
					</td>
				</tr>
				<tr>
					<td colspan="2" height="20"></td>
				</tr>
				<tr>
					<td>
					<?php echo _JLMS_HW_GRADED_ACTIVITY;?>
					</td>
					<td>
						<?php echo $lists['graded_activity'];?>
					</td>
				</tr>
				<tr>
					<td colspan="2" height="20"></td>
				</tr>
				<tr>
					<td width="15%" valign="top" style="vertical-align:top "><br /><?php echo _JLMS_IS_TIME_RELATED;?></td>
					<td><br />
						<?php JLMS_HTML::_('showperiod.field', $hw_details->is_time_related, $hw_details->show_period ) ?>
					</td>
				</tr>			
				<?php if( $lists['is_limited'] != '' ) { ?>
				<tr>
					<td colspan="2" height="20"></td>
				</tr>
				<tr>
					<td>
					<?php echo _JLMS_LIMIT_RESOURCE_TO_GROUPS;?>
					</td>
					<td>
						<?php echo $lists['is_limited'];?>
					</td>
				</tr>
				<?php } ?>
				<tr>
					<td colspan="2" height="20"></td>
				</tr>

				<tr>
					<td valign="top"><?php echo _JLMS_LIMIT_RESOURCE_USERGROUPS;?></td>
					<td><?php echo $lists['groups'];?></td>
				</tr>
				<tr>
					<td align="left" valign="top" style="vertical-align:top "><br /><?php echo _JLMS_SHORT_DESCRIPTION;?></td>
					<td><br /><textarea class="inputbox" name="hw_shortdescription" cols="50" rows="3"><?php echo $hw_details->hw_shortdescription; ?></textarea></td>
				</tr>
				<tr>
					<td colspan="2"><br /><?php echo _JLMS_DESCRIPTION;?></td>
				</tr>
				<tr>
					<td colspan="2">
					<?php
						JLMS_editorArea( 'editor2', $hw_details->hw_description, 'hw_description', '100%;', '250', '40', '20' ) ; ?>
					</td>
				</tr>
			</table>
			<input type="hidden" name="option" value="<?php echo $option;?>" />
			<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
			<input type="hidden" name="task" value="hw_save" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="course_id" value="<?php echo $id;?>" />
			<input type="hidden" name="id" value="<?php echo $hw_details->id;?>" />
			<?php echo $params['hidden_is_time_related']; ?>
		</form>
<?php
		JLMS_TMPL::CloseTS();
		JLMS_TMPL::CloseMT();
	}

	function showHomeWorks_stu( $id, $option, &$rows, &$pageNav, &$lists ) {
		global $JLMS_CONFIG;
		$Itemid = $JLMS_CONFIG->get('Itemid');
	?>

<script language="javascript" type="text/javascript">
<!--//--><![CDATA[//><!--
function submitbutton_change(pressbutton, state) {
	var form = document.adminForm;
	if (pressbutton == 'hw_change'){
		if (form.boxchecked.value == '0') {
			alert( "<?php echo _JLMS_ALERT_SELECT_ITEM;?>" );
		} else {
			form.task.value = pressbutton;
			form.state.value = state;
			form.submit();
		}
	}
}
function submitbutton_change_item(pressbutton, state, cid_id) {
	var form = document.adminForm;
	if (pressbutton == 'hw_change'){
		form.task.value = pressbutton;
		form.state.value = state;
		form.cid2.value = cid_id;
		form.submit();
	}
}
//--><!]]>
</script>
<?php
		JLMS_TMPL::OpenMT();

		$hparams = array();
		JLMS_TMPL::ShowHeader('homework', _JLMS_HW_TITLE_HW, $hparams);

		JLMS_TMPL::OpenTS();
?>
		<form action="<?php echo sefRelToAbs("index.php?option=$option&Itemid=$Itemid");?>" method="post" name="adminForm">
<?php 	if (!empty($rows) || $lists['used_filter'] ) { ?>
			<table width="100%" cellpadding="0" cellspacing="0" border="0" class="jlms_table_no_borders">
				<tr>
					<td align="left" style="text-align:left ">
						<div align="left" style="white-space:nowrap ">
						&nbsp;
						</div>
					</td>
					<td align="right" style="text-align:right ">
						<div align="right" style="white-space:nowrap ">
						<?php echo _JLMS_HW_FILTER_HW . $lists['filter']; ?>
						</div>
					</td>
				</tr>
			</table>
			<table width="100%" cellpadding="0" cellspacing="0" border="0" class="<?php echo JLMSCSS::_('jlmslist');?>">
				<tr>
					<<?php echo JLMSCSS::tableheadertag();?> width="20" class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center">#</<?php echo JLMSCSS::tableheadertag();?>>
					<<?php echo JLMSCSS::tableheadertag();?> width="20" class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></<?php echo JLMSCSS::tableheadertag();?>>
					<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" width="30%"><?php echo _JLMS_HW_TBL_HEAD_HW;?></<?php echo JLMSCSS::tableheadertag();?>>
					<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" nowrap="nowrap"><?php echo _JLMS_HW_TBL_HEAD_GRADE;?></<?php echo JLMSCSS::tableheadertag();?>>
					<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" nowrap='nowrap' style="white-space:nowrap"><?php echo _JLMS_HW_TBL_HEAD_DATE;?></<?php echo JLMSCSS::tableheadertag();?>>
					<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" nowrap='nowrap' style="white-space:nowrap"><?php echo _JLMS_HW_TBL_HEAD_ENDDATE;?></<?php echo JLMSCSS::tableheadertag();?>>
					<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" width="50%"><?php echo _JLMS_HW_TBL_HEAD_DESCR;?></<?php echo JLMSCSS::tableheadertag();?>>
				</tr>
			<?php
			$k = 1;
			for ($i=0, $n=count($rows); $i < $n; $i++) {
				$row = $rows[$i];
				$link = "index.php?option=$option&Itemid=$Itemid&task=hw_view&course_id=$id&id={$row->id}";
				$alt = ($row->hw_status)?_JLMS_HW_STATUS_COMPLETED:_JLMS_HW_STATUS_INCOMPLETE;
				$image = ($row->hw_status)?'btn_accept.png':'btn_cancel.png';
				$state = ($row->hw_status)?0:1;
				$checked = mosHTML::idBox( $i, $row->id);?>
				<tr class="<?php echo JLMSCSS::_('sectiontableentry'.$k); ?>">
					<td align="center"><?php echo ( $i + 1 ); ?></td>
					<td><?php echo $checked; ?></td>
					<td align="left" valign="middle" style="vertical-align:middle ">
						<a href="<?php echo sefRelToAbs($link);?>" title="<?php echo str_replace('"','&quot;',$row->hw_name);?>">
							<?php echo $row->hw_name;?>
						</a>
					</td>					
					<td align="center" valign="middle" style="vertical-align:middle ">
								<?php
									$grade_num = '';									
									if( $row->hw_status ) {																					
										if( $row->grade && $row->grade != _STATUS_NOT_SELECT ) {
											$img = '';
											if( $row->graded_activity ) 
											{												
												$img = 'btn_accept.png';
												$grade_num = $row->grade;												
											} else {
												switch( $row->grade ) 
												{													
													case _STATUS_NOT_PASSED:														
														$img = 'btn_cancel.png';
													break;
													case _STATUS_PASSED:
														$img = 'btn_accept.png';
													break;	
												}
											}

											echo '<img src="'.JURI::root().'/components/com_joomla_lms/lms_images/toolbar/'.$img.'" alt="'.$row->grade.'" /><b>'.$grade_num.'</b>';
										} else {
											$ttip = '<img src="'.JURI::root().'/components/com_joomla_lms/lms_images/toolbar/btn_publish_wait.png" alt="waiting" />';
											echo JLMS_toolTip(_JLMS_HW_AWAITING_REVIEW, '', $ttip, '', false);
										}
									}										
								?>
					</td>					
					<td valign="middle" style="vertical-align:middle " nowrap='nowrap'>
						<?php echo JLMS_dateToDisplay($row->post_date);?>
					</td>
					<td valign="middle" style="vertical-align:middle " nowrap='nowrap'>
						<?php echo JLMS_dateToDisplay($row->end_date);?>
					</td>
					<td valign="middle" style="vertical-align:middle "><?php echo $row->hw_shortdescription?$row->hw_shortdescription:'&nbsp;';?></td>
				</tr>
				<?php
				$k = 3 - $k;
			}?>
				<tr>
					<td align="center" colspan="7" class="<?php echo JLMSCSS::_('jlmslist-footer_td');?>">
						<div align="center" style="white-space:nowrap">
						<?php
							$link = "index.php?option=$option&amp;Itemid=$Itemid&amp;task=homework&amp;id=$id";
							echo _JLMS_PN_DISPLAY_NUM . $pageNav->getLimitBox( $link ) . '&nbsp;' . $pageNav->getPagesCounter();
							echo '<br />';
							echo $pageNav->writePagesLinks( $link );
						?> 
						</div>
					</td>
				</tr>
			</table>
<?php
		} else {
			echo '<div class="joomlalms_user_message">'._JLMS_NO_ITEMS_HERE.'</div>';
		}
?>
			<input type="hidden" name="option" value="<?php echo $option;?>" />
			<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
			<input type="hidden" name="task" value="homework" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="state" value="0" />
			<input type="hidden" name="cid2" value="0" />
			<input type="hidden" name="id" value="<?php echo $id;?>" />
		</form>
<?php
		JLMS_TMPL::CloseTS();
		JLMS_TMPL::CloseMT();
	}

	function showHomeWorks( $id, $option, &$rows, &$pageNav, $usertype = 1 ) {
		global $JLMS_CONFIG;
		$Itemid = $JLMS_CONFIG->get('Itemid');
		$JLMS_ACL = & JLMSFactory::getACL();

		if($JLMS_ACL->CheckPermissions('homework', 'manage')){?>
<script language="javascript" type="text/javascript">
<!--//--><![CDATA[//><!--
function submitbutton(pressbutton) {
	var form = document.adminForm;
	if ( ((pressbutton == 'hw_delete') || (pressbutton == 'hw_edit')) && (form.boxchecked.value == "0")){
		alert("<?php echo _JLMS_ALERT_SELECT_ITEM;?>");
	} else {
		form.task.value = pressbutton;
		form.submit();
	}
}

function submitbutton_change2(pressbutton, state, cid_id) {
	var form = document.adminForm;
	if (pressbutton == 'hw_publish'){
		form.task.value = pressbutton;
		form.state.value = state;
		form.cid2.value = cid_id;
		form.submit();
	}
}

//--><!]]>
</script>
<?php }
		JLMS_TMPL::OpenMT();

		$hparams = array();
		JLMS_TMPL::ShowHeader('homework', _JLMS_HW_TITLE_HW, $hparams);

		JLMS_TMPL::OpenTS();
?>
		<form action="<?php echo sefRelToAbs("index.php?option=$option&Itemid=$Itemid");?>" method="post" name="adminForm">
<?php 	if (!empty($rows)) { ?>
				<?php
				$hw_colspan = 5;
				?>
				<table width="100%" cellpadding="0" cellspacing="0" border="0" class="<?php echo JLMSCSS::_('jlmslist');?>">
					<tr>
						<<?php echo JLMSCSS::tableheadertag();?> width="20" class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center">#</<?php echo JLMSCSS::tableheadertag();?>>
						<?php 
//						if ($usertype == 1) { 
						if ( ($JLMS_ACL->CheckPermissions('homework', 'manage')) ) {
							$hw_colspan++;
						?>
						<<?php echo JLMSCSS::tableheadertag();?> width="20" class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></<?php echo JLMSCSS::tableheadertag();?>>
						<?php } ?>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" width="30%"><?php echo _JLMS_HW_TBL_HEAD_HW;?></<?php echo JLMSCSS::tableheadertag();?>>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" nowrap='nowrap' style="white-space:nowrap"><?php echo _JLMS_HW_TBL_HEAD_DATE;?></<?php echo JLMSCSS::tableheadertag();?>>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" nowrap='nowrap' style="white-space:nowrap"><?php echo _JLMS_HW_TBL_HEAD_ENDDATE;?></<?php echo JLMSCSS::tableheadertag();?>>
					<?php if($JLMS_ACL->CheckPermissions('homework', 'manage')) {
							$hw_colspan++;
					?>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" nowrap='nowrap' style="white-space:nowrap">&nbsp;</<?php echo JLMSCSS::tableheadertag();?>>
					<?php } ?>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" width="50%"><?php echo _JLMS_HW_TBL_HEAD_DESCR;?></<?php echo JLMSCSS::tableheadertag();?>>
					</tr>
				<?php
				$k = 1;
				for ($i=0, $n=count($rows); $i < $n; $i++) {
					$row = $rows[$i];
					$link = "index.php?option=$option&Itemid=$Itemid&task=hw_stats&course_id=$id&id={$row->id}";
					$alt = ($row->published)?(_JLMS_STATUS_PUB):_JLMS_STATUS_UNPUB;
					$image = ($row->published)?('btn_accept.png'):'btn_cancel.png';
					$state = ($row->published)?0:1;
					$tooltip_txt = '';
					$tooltip_title = '';
					if ($row->is_time_related) {
						if ($row->published) {
							$image = 'btn_publish_wait.png';
							$tooltip_title = _JLMS_STATUS_PUB;
						} else {
							$image = 'btn_unpublish_wait.png';
							$tooltip_title = _JLMS_STATUS_UNPUB;
						}
						$tooltip_txt = _JLMS_WILL_BE_RELEASED_IN;
						$showperiod = $row->show_period;
						$ost1 = $showperiod%(24*60);		
						$sp_days = ($showperiod - $ost1)/(24*60);
						$ost2 = $showperiod%60;						
						$sp_hours = ($ost1 - $ost2)/60;
						$sp_mins = $ost2;
						$release_time_info = false;
						if ($sp_days) {
							$tooltip_txt .= ' '.$sp_days.' '._JLMS_RELEASED_IN_DAYS;
							$release_time_info = true;
						}
						if ($sp_hours) {
							$tooltip_txt .= ' '.$sp_hours.' '._JLMS_RELEASED_IN_HOURS;
							$release_time_info = true;
						}
						if ($sp_mins) {
							$tooltip_txt .= ' '.$sp_mins.' '._JLMS_RELEASED_IN_MINUTES;
							$release_time_info = true;
						}
						if ($release_time_info) {
							$tooltip_txt .= ' '._JLMS_RELEASED_AFTER_ENROLLMENT;
						}
					}

					$checked = '';
					if ($JLMS_ACL->CheckPermissions('homework', 'manage')) {
						$checked = mosHTML::idBox( $i, $row->id, $row->checkedout );
					}?>
					<tr class="<?php echo JLMSCSS::_('sectiontableentry'.$k); ?>">
						<td align="center"><?php echo ( $pageNav->limitstart + $i + 1 ); ?></td>
						<?php 
						if ($JLMS_ACL->CheckPermissions('homework', 'manage')) {
							echo '<td>' . $checked . '</td>';
						} ?>
						<td align="left" valign="middle" style="vertical-align:middle ">
							<a href="<?php echo sefRelToAbs($link);?>" title="<?php echo str_replace('"','&nbsp;',$row->hw_name);?>">
								<?php echo $row->hw_name;?>
							</a>
						</td>
						<td valign="middle" style="vertical-align:middle" nowrap="nowrap">
							<?php echo JLMS_dateToDisplay($row->post_date);?>
						</td>
						<td valign="middle" style="vertical-align:middle" nowrap="nowrap">
							<?php echo JLMS_dateToDisplay($row->end_date);?>
						</td>
					<?php if($JLMS_ACL->CheckPermissions('homework', 'manage')) { ?>
						<td valign="middle">
						<?php
							if ($row->is_time_related) {
								$tooltip_link = 'javascript:submitbutton_change2(\'hw_publish\','.$state.','.$row->id.')';
								$tooltip_name = '<img class="JLMS_png" src="'.$JLMS_CONFIG->get('live_site').'/components/com_joomla_lms/lms_images/toolbar/'.$image.'" width="16" height="16" border="0" alt="'.$alt.'" />';
								echo JLMS_toolTip($tooltip_title, $tooltip_txt, $tooltip_name, $tooltip_link);
							} else {
								echo '<a class="jlms_img_link" href="javascript:submitbutton_change2(\'hw_publish\','.$state.','.$row->id.')" title="'.$alt.'">';
								echo '<img class="JLMS_png" src="'.$JLMS_CONFIG->get('live_site').'/components/com_joomla_lms/lms_images/toolbar/'.$image.'" width="16" height="16" border="0" alt="'.$alt.'" />';
								echo '</a>';
							}
						?>
						</td>
					<?php } ?>
						<td valign="middle" style="vertical-align:middle "><?php echo $row->hw_shortdescription?$row->hw_shortdescription:'&nbsp;';?></td>
					</tr>
					<?php
					$k = 3 - $k;
				} ?>
				
				<tr>
					<td align="center" class="<?php echo JLMSCSS::_('jlmslist-footer_td');?>" colspan="<?php echo $hw_colspan;?>"><div align="center" style="white-space:nowrap;">
					<?php
						$link = "index.php?option=$option&amp;Itemid=$Itemid&amp;task=homework&amp;id=$id";
						echo _JLMS_PN_DISPLAY_NUM . $pageNav->getLimitBox( $link ) . '&nbsp;' . $pageNav->getPagesCounter();
						echo '<br />';
						echo $pageNav->writePagesLinks( $link );
					?> 
					</div></td>
				</tr>
				</table>
<?php
		} else {
			echo '<div class="joomlalms_user_message">'._JLMS_NO_ITEMS_HERE.'</div>';
		}
?>
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
		<input type="hidden" name="task" value="homework" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="id" value="<?php echo $id;?>" />
		<input type="hidden" name="state" value="0" />
		<input type="hidden" name="cid2" value="0" />
		</form>
<?php
		JLMS_TMPL::CloseTS();
		if ( $id && ($JLMS_ACL->CheckPermissions('homework', 'manage')) ) {
			$link_new = sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=hw_create&id=$id");
			$controls = array();
			$controls[] = array('href' => $link_new, 'onclick' => "", 'title' => _JLMS_HW_IMG_NEW_HW, 'img' => 'add');
			$controls[] = array('href' => "javascript:submitbutton('hw_delete');", 'title' => _JLMS_HW_IMG_DEL_HW, 'img' => 'delete');
			$controls[] = array('href' => "javascript:submitbutton('hw_edit');", 'title' => _JLMS_HW_IMG_EDIT_HW, 'img' => 'edit');
			JLMS_TMPL::ShowControlsFooter($controls);
		}
		JLMS_TMPL::CloseMT();
	}
}
?>