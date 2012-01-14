<?php
/**
* joomla_lms.course_dropbox.html.php
* Joomla LMS Component
* * * ElearningForce DK
**/

// no direct access
defined( '_JLMS_EXEC' ) or die( 'Restricted access' );
// to do: dobavit' vse nugnye polya + JS proverki
class JLMS_course_dropbox_html {
	function editDropBox( &$row, &$lists, $option, $course_id ) {
		global $Itemid, $my; ?>
<script language="javascript" type="text/javascript">
<!--//--><![CDATA[//><!--
function setgood() {
	return true;
}
function submitbutton(pressbutton) {
	var form=document.adminForm;
	try {
		form.onsubmit();
	} catch(e) {
		//alert(e);
	}
	if ((pressbutton=='save_dropbox') && ((form.userfile.value=="") && (form.dropbox_name.value==""))){
			alert("<?php echo _JLMS_SELECT_FILE_ENTER_NAME;?>");
	}
	 else {form.task.value = pressbutton;form.submit();}
}
//--><!]]>
</script>
<?php
		JLMS_TMPL::OpenMT();

		$hparams = array();
		$toolbar = array();
		$toolbar[] = array('btn_type' => 'save', 'btn_js' => "javascript:submitbutton('save_dropbox');");
		$toolbar[] = array('btn_type' => 'cancel', 'btn_js' => "javascript:submitbutton('cancel_dropbox');");
		JLMS_TMPL::ShowHeader('dropbox', _JLMS_DROP_ADD_ITEM, $hparams, $toolbar);

		JLMS_TMPL::OpenTS();
?>
		<form action="<?php echo sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid");?>" method="post" name="adminForm" enctype="multipart/form-data" onsubmit="setgood();">
			<table width="100%" cellpadding="0" cellspacing="0" border="0" id="jlms_item_properties">
				<tr>
					<td width="30%" valign="top" style="vertical-align:top"><?php echo _JLMS_DROP_SEND_TO;?></td>
					<td>
						<?php echo $lists['course_users']; ?>
					</td>
				</tr>
				
				<tr>
					<td height="15"></td>
					<td></td>
				</tr>
				<tr>
					<td valign="middle" style="vertical-align:middle">
						<?php echo _JLMS_DROP_NAME;?>
					</td>
					<td>
						<input type="text" class="inputbox" name="dropbox_name" size="52" value="<?php echo $row->drp_name;?>">
					</td>				
				</tr>
				<?php
				$detect = false;
				$path_detect = JPATH_SITE . DS . 'components' . DS . 'com_jlms_profile' . DS . 'jlms_profile_detect.php';
				if(file_exists($path_detect)){
					include_once($path_detect);
					$detect = COMPONENT_Profile_Detect();
				}	
				if($detect){
					$params = array();
					$params['width'] = 800;
					$params['height'] = 600;
					echo showChooseFiles($params);
				} else {
				?>
				<tr>
					<td valign="middle" style="vertical-align:middle"><br /><?php echo _JLMS_CHOOSE_FILE;?></td>
					<td>
						<br />
						<input class="inputbox" size="40" type="file" name="userfile">
					</td>
				</tr>
				<?php
				}
				?>
				<?php
//				if (JLMS_GetUserType($my->id, $course_id) == 1) {
				$JLMS_ACL = & JLMSFactory::getACL();
				if ($JLMS_ACL->CheckPermissions('dropbox', 'mark_as_corrected')) {
				?>
				<tr>
					<td valign="middle" style="vertical-align:middle"><br /><?php echo _JLMS_DROP_MARK_AS_CORRECTED;?></td>
					<td>
						<br /><?php echo mosHTML::yesnoRadioList( 'drp_corrected', 'class="inputbox" ', 0);?>
					</td>
				</tr>
				<?php 
				} 
				?>
				<tr>
					<td colspan="2"><br /><?php echo _JLMS_COMMENT;?></td>
				</tr>
				<tr>
					<td colspan="2">
					<?php
						JLMS_editorArea( 'editor1', $row->drp_description, 'drp_description', '100%;', '250', '40', '20' ) ; ?>
					</td>
				</tr>
			</table>
			<input type="hidden" name="option" value="<?php echo $option;?>" />
			<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
			<input type="hidden" name="task" value="save_dropbox" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="id" value="<?php echo $row->id;?>" />
			<input type="hidden" name="course_id" value="<?php echo $course_id;?>" />
		</form>
<?php
		JLMS_TMPL::CloseTS();
		JLMS_TMPL::CloseMT();
	}
	function viewCourseDropBox ( $drp_description, $option, $drp_name = '' ) {

		JLMS_TMPL::OpenMT();

		$hparams = array();
		$toolbar = array();
		$back_link = 'javascript:window.history.go(-1);';
		$toolbar[] = array('btn_type' => 'back', 'btn_js' => $back_link );

		
		
		JLMS_TMPL::ShowHeader('dropbox', $drp_name, $hparams, $toolbar);

		JLMS_TMPL::OpenTS();
?>
			<table width="100%" cellpadding="10" cellspacing="10" border="0" class="jlms_table_no_borders">
				<tr>
					<td>
						<?php echo $drp_description;?>					
					</td>				
				</tr>
			</table>		
		<?php
		JLMS_TMPL::CloseTS();
		JLMS_TMPL::CloseMT();
	}
		
	function showCourseDropBox( $course_id, $option, &$rows, &$lists ) {
		global $Itemid, $my, $JLMS_CONFIG;?>
<script language="javascript" type="text/javascript">
<!--//--><![CDATA[//><!--
function submitbutton(pressbutton) {
	var form = document.adminForm;
	if ((pressbutton == 'del_dropbox') && (form.boxchecked.value == '0')) {
		alert( "<?php echo _JLMS_ALERT_SELECT_ITEM;?>" );
	} else {
		form.task.value = pressbutton;
		form.submit();
	}
}

function submitbutton_change(pressbutton, state) {
	var form = document.adminForm;
	if (pressbutton == 'change_dropbox'){
		if (form.boxchecked.value == '0') {
			alert('<?php echo _JLMS_DROP_SELECT_ITEM;?>');
		} else {
			form.task.value = pressbutton;
			form.state.value = state;
			form.submit();
		}
	}
}
function submitbutton_change2(pressbutton, state, cid_id) {
	var form = document.adminForm;
	if (pressbutton == 'change_dropbox'){
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
		JLMS_TMPL::ShowHeader('dropbox', _JLMS_DROP_TITLE, $hparams);

		JLMS_TMPL::OpenTS();
?>
		<form action="<?php echo sefRelToAbs("index.php?option=".$option."&amp;Itemid=".$Itemid);?>" method="post" name="adminForm">
<?php 	if (!empty($rows)) { ?>
			<table width="100%" cellpadding="0" cellspacing="0" border="0" class="<?php echo JLMSCSS::_('jlmslist');?>">
				<tr>
					<<?php echo JLMSCSS::tableheadertag();?> colspan="8" class="<?php echo JLMSCSS::_('sectiontableheader');?>">
						<table width="100%" cellpadding="0" cellspacing="0" border="0">
							<tr>
								<td width="50%" align="center">
									<?php echo _JLMS_DROP_INBOX.' '.$lists['dropbox_in_new'].' / '.$lists['dropbox_in_total'];?>
								</td>
								<td width="50%" align="center">
									<?php echo _JLMS_DROP_OUTBOX.' '.$lists['dropbox_out_total'];?>
								</td>
							</tr>
						</table>
					</<?php echo JLMSCSS::tableheadertag();?>>
				</tr>
				<tr>
					<<?php echo JLMSCSS::tableheadertag();?> width="20" class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center">#</<?php echo JLMSCSS::tableheadertag();?>>
					<<?php echo JLMSCSS::tableheadertag();?> width="20" class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></<?php echo JLMSCSS::tableheadertag();?>>
					<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" width="16">&nbsp;</<?php echo JLMSCSS::tableheadertag();?>>
					<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>"><?php echo _JLMS_DROP_TBL_HEAD_NAME;?></<?php echo JLMSCSS::tableheadertag();?>>
					<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>"><?php echo _JLMS_DROP_TBL_HEAD_FROM;?></<?php echo JLMSCSS::tableheadertag();?>>
					<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>"><?php echo _JLMS_DROP_TBL_HEAD_TO;?></<?php echo JLMSCSS::tableheadertag();?>>
					<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>"><?php echo _JLMS_DROP_TBL_HEAD_CORRECTED;?></<?php echo JLMSCSS::tableheadertag();?>>
					<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>"><?php echo _JLMS_DROP_TBL_HEAD_DESCR;?></<?php echo JLMSCSS::tableheadertag();?>>
				</tr>
			<?php
			$k = 1;
			for ($i=0, $n=count($rows); $i < $n; $i++) {
				$row = $rows[$i];
				$link 	= sefRelToAbs("index.php?option=".$option."&amp;Itemid=".$Itemid."&amp;task=get_frombox&amp;course_id=".$course_id."&amp;id=".$row->id);
				$checked = mosHTML::idBox( $i, $row->id);
				$mark_read = 1;
				if ( ($row->drp_mark == 1) && ($row->recv_id == $my->id) ) {$mark_read = 0;}
				if ($mark_read) {
					$alt = _JLMS_DROP_STATUS_READ;
					$image = 'btn_drp_readed.png';
					$state = 1;
				} else {
					$alt = _JLMS_DROP_STATUS_UNREAD;
					$image = 'btn_drp_unreaded.png';
					$state = 0;
				}
				$state = 0; // 23.11.2006 - Bjarne request
				#if ($row->drp_mark == 1) { $alt = _JLMS_DROP_STATUS_READ; } elseif ($row->drp_mark == 2) { $alt = _JLMS_DROP_STATUS_CORRECT; } else { $alt = _JLMS_DROP_STATUS_UNREAD; }
				#if ($row->drp_mark == 1) { $image = 'btn_drp_readed.png'; } elseif ($row->drp_mark == 2) { $image = 'btn_drp_corrected.png'; } else { $image = 'btn_drp_unreaded.png'; }
				#if ($row->drp_mark == 1) { $state = 2; } elseif ($row->drp_mark == 2) { $state = 0; } else { $state = 1; }
				?>
				<tr class="<?php echo JLMSCSS::_('sectiontableentry'.$k); ?>">
					<td align="center" valign="middle"><?php echo ( $i + 1 ); ?></td>
					<td align="center" valign="middle"><?php echo $checked; ?></td>
					<td align="center" valign="middle">
<?php
						echo '<a class="jlms_img_link" href="javascript:submitbutton_change2(\'change_dropbox\','.$state.','.$row->id.')" title="'.$alt.'">';
						echo '<img class="JLMS_png" src="'.$JLMS_CONFIG->get('live_site').'/components/com_joomla_lms/lms_images/toolbar/'.$image.'" width="16" height="16" border="0" alt="'.$alt.'" />';
						echo'</a>';
?>
					</td>
					<td align="left" valign="middle">
					<?php if (!$mark_read) { echo '<strong>'; } ?>
						<a href="<?php echo $link;?>" title="<?php echo str_replace('"','&quot;',$row->drp_name);?>">
							<?php echo $row->drp_name;?>
						</a>
					<?php if (!$mark_read) { echo '</strong>'; } ?>
					</td>
					<td valign="middle"><?php echo $row->owner_username;?></td>
					<td valign="middle"><?php echo $row->recv_username;?></td>
					<td valign="middle"><?php if ($row->drp_corrected) {?>
						<img class="JLMS_png" src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_accept.png" width="16" height="16" alt="<?php echo _JLMS_DROP_STATUS_CORRECT;?>" title="<?php echo _JLMS_DROP_STATUS_CORRECT;?>" border="0" />
						<?php } else { echo '&nbsp;';} ?>
					</td>
					<td valign="middle"><?php
						$drp_descr = strip_tags($row->drp_description);
						if (strlen($drp_descr) > 100) {
							$link_drp_descr = sefrelToAbs("index.php?option=$option&amp;Itemid=$Itemid&amp;task=drp_view_descr&amp;course_id=$course_id&amp;id={$row->id}"); 
							$drp_descr = substr($drp_descr, 0, 100)."... <a title='"._JLMS_DROP_LINK_FULL_DESCR."' href='".$link_drp_descr."'>"._JLMS_DROP_LINK_MORE_TEXT."</a>";
						}
						echo $drp_descr?$drp_descr:'&nbsp;'; ?>
					</td>
				</tr>
				<?php
				$k = 3 - $k;
			}?>
			</table>
<?php
		} else {
			echo '<div class="joomlalms_user_message">'._JLMS_NO_ITEMS_HERE.'</div>';
		}
?>
			<input type="hidden" name="option" value="<?php echo $option;?>" />
			<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
			<input type="hidden" name="task" value="dropbox" />
			<input type="hidden" name="id" value="<?php echo $course_id;?>" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="row_id" value="0" />
			<input type="hidden" name="state" value="0" />
			<input type="hidden" name="cid2" value="0" />
		</form>
<?php
		JLMS_TMPL::CloseTS();
			$link_new = "index.php?option=".$option."&amp;Itemid=".$Itemid."&amp;task=new_dropbox&amp;id=".$course_id;
			$controls = array();
			$controls[] = array('href' => "javascript:submitbutton_change('change_dropbox',0);", 'title' => _JLMS_DROP_SET_READ, 'img' => 'letter');
//			$controls[] = array('href' => ampReplace(sefRelToAbs($link_new)), 'title' => _JLMS_DROP_NEW_ITEM, 'img' => 'add');
			$controls[] = array('href' => "javascript:submitbutton('new_dropbox');", 'title' => _JLMS_DROP_NEW_ITEM, 'img' => 'add');
			$controls[] = array('href' => "javascript:submitbutton('del_dropbox');", 'title' => _JLMS_DROP_DEL_ITEM, 'img' => 'delete');
			JLMS_TMPL::ShowControlsFooter($controls);
		JLMS_TMPL::CloseMT();

	}

	function viewDRPDescription( $course_id, $option, &$row, &$lists ) {
		global $Itemid;

		JLMS_TMPL::OpenMT();

		$hparams = array();
		$toolbar = array();
		$toolbar[] = array('btn_type' => 'back', 'btn_js' => sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid&amp;task=dropbox&amp;id=$course_id"));
		JLMS_TMPL::ShowHeader('dropbox', $row->drp_name, $hparams, $toolbar);

		JLMS_TMPL::OpenTS();
?>
			<table width="100%" cellpadding="0" cellspacing="0" border="0" class="jlms_table_no_borders">
				<tr>
					<td align="left" style="text-align:left "><br />
						<table align="left" width="60%" cellpadding="0" cellspacing="0" border="0" class="jlms_table_no_borders">
						<tr>
							<td width="50%" align="left" style="text-align:left ">&nbsp;&nbsp;<?php echo _JLMS_DROP_SENDER;?></td>
							<td width="50%" align="left" style="text-align:left "><?php echo $row->owner_username;?></td>
						</tr>
						<tr>
							<td width="50%" align="left" style="text-align:left ">&nbsp;&nbsp;<?php echo _JLMS_DROP_RECV;?></td>
							<td width="50%" align="left" style="text-align:left "><?php echo $row->recv_username;?></td>
						</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td><br />
						<?php echo $row->drp_description; ?>
					</td>
				</tr>
			</table>
<?php
		JLMS_TMPL::CloseTS();
		JLMS_TMPL::CloseMT();
	}
}
?>