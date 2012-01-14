<?php
/**
* joomla_lms.course_links.html.php
* (c) JoomaLMS eLearning Software http://www.joomlalms.com/
**/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class JLMS_course_links_html {

	function showInlineLink( $id, $option, &$row, &$params ) {
		$JLMS_CONFIG = JLMSFactory::getConfig();
		$Itemid = $JLMS_CONFIG->get('Itemid');

		JLMS_TMPL::OpenMT();

		$hparams = array();
		$toolbar = array();

		//01.12.2007 - (DEN) - Compatibility for returning from the document view to the doc.tool/course homepage/lpaths list.
		$back_link = 'javascript:window.history.go(-1);';
		$toolbar[] = array('btn_type' => 'back', 'btn_js' => $back_link );
		JLMS_TMPL::ShowHeader('link', $row->link_name, $hparams, $toolbar);

			JLMS_TMPL::OpenTS();
			echo '<iframe id="jlms_inline_link" name="jlms_inline_link" height="'.$params->get('display_height', 540).'px" width="100%" frameborder="0" scrolling="auto" src="'.$row->link_href.'">'._JLMS_IFRAMES_REQUIRES.'</iframe>';
			JLMS_TMPL::CloseTS();

		JLMS_TMPL::CloseMT();
	}

	function showEditLink( $link_details, $lists, $option, $id, & $params ) {
		$JLMS_CONFIG = JLMSFactory::getConfig();
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
	if ((pressbutton == 'save_link') && (form.link_name.value == "")) {
		alert( "<?php echo _JLMS_LINKS_ENTER_LINK_NAME;?>" );
	} else if ((pressbutton == 'save_link') && (form.link_href.value.substring(0,7) != "http://") && (form.link_href.value.substring(0,8) != "https://")) {
		alert( "<?php echo _JLMS_LINKS_ENTER_VALID_LINK_NAME;?>" );
	} else {
		form.task.value = pressbutton;
		form.submit();
	}
}

var tmp_lnk_type_var = 0;
function jlms_change_link_type() {
	if (tmp_lnk_type_var == 2) {
		$('link_stage_height_section').style.display = '';
		$('link_stage_width_section').style.display = 'none';
	} else if (tmp_lnk_type_var == 3) {
		$('link_stage_height_section').style.display = '';
		$('link_stage_width_section').style.display = '';
	} else {
		$('link_stage_height_section').style.display = 'none';
		$('link_stage_width_section').style.display = 'none';
	}
}
//--><!]]>
</script>
<?php
		JLMS_TMPL::OpenMT();

		$hparams = array();
		$toolbar = array();
		$toolbar[] = array('btn_type' => 'save', 'btn_js' => "javascript:submitbutton('save_link');");
		$toolbar[] = array('btn_type' => 'cancel', 'btn_js' => "javascript:submitbutton('cancel_link');");
		JLMS_TMPL::ShowHeader('link', $link_details->id ? _JLMS_LINKS_EDIT_LINK : _JLMS_LINKS_CREATE_LINK, $hparams, $toolbar);

		JLMS_TMPL::OpenTS();
?>
		<form action="<?php echo sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid");?>" method="post" name="adminForm" onsubmit="setgood();">
			<table width="100%" cellpadding="0" cellspacing="0" border="0" id="jlms_item_properties">
				<tr>
					<td width="15%" valign="middle" style="vertical-align:middle "><?php echo _JLMS_ENTER_NAME;?></td>
					<td>
						<input size="40" class="inputbox" type="text" name="link_name" value="<?php echo str_replace('"','&quot;',$link_details->link_name);?>" />
					</td>
				</tr>
				<tr>
					<td width="15%" valign="middle" style="vertical-align:middle "><br /><?php echo _JLMS_LINK_LOCATION;?></td>
					<td><br />
						<input size="40" class="inputbox" type="text" name="link_href" value="<?php echo $link_details->link_href;?>" />
						<select name="link_type" onchange="tmp_lnk_type_var = this.value;jlms_change_link_type();">
							<option value="0"<?php if (!$link_details->link_type) { echo ' selected="selected"';}?>><?php echo _JLMS_LINKS_TYPE_NEW_WINDOW;?></option>
							<option value="1"<?php if ($link_details->link_type == 1) { echo ' selected="selected"';}?>><?php echo _JLMS_LINKS_TYPE_SAME_WINDOW;?></option>
							<option value="2"<?php if ($link_details->link_type == 2) { echo ' selected="selected"';}?>><?php echo _JLMS_LINKS_TYPE_IFRAME;?></option>
							<option value="3"<?php if ($link_details->link_type == 3) { echo ' selected="selected"';}?>><?php echo _JLMS_LINKS_TYPE_SQBOX;?></option>
						</select>
					</td>
				</tr>
				<tr id="link_stage_width_section"<?php if(!$link_details->link_type || $link_details->link_type == 1 || $link_details->link_type == 2) { echo ' style="display:none"'; } ?>>
					<td><br /><?php echo _JLMS_LINKS_DISPLAY_WIDTH.":";?><br /></td>
					<td><br />
						<input size="40" class="inputbox" type="text" name="params[display_width]" value="<?php echo $params->get('display_width', 0);?>" /><br />
					</td>
				</tr>
				<tr id="link_stage_height_section"<?php if(!$link_details->link_type || $link_details->link_type == 1 ) { echo ' style="display:none"'; } ?>>
					<td><br /><?php echo _JLMS_LINKS_DISPLAY_HEIGHT.":";?><br /></td>
					<td><br />
						<input size="40" class="inputbox" type="text" name="params[display_height]" value="<?php echo $params->get('display_height', 0);?>" /><br />
					</td>
				</tr>
				<tr>
					<td>
					<br /><?php echo _JLMS_STATUS_PUB;?>
					</td>
					<td><br />
						<?php echo $lists['published'];?>
					</td>
				</tr>
				<tr>
					<td width="15%" valign="top" style="vertical-align:top "><br /><?php echo _JLMS_IS_TIME_RELATED;?></td>
					<td><br />
						<?php JLMS_HTML::_('showperiod.field', $link_details->is_time_related, $link_details->show_period ) ?>
					</td>
				</tr>
				<tr>
					<td><br /><?php echo _JLMS_DESCRIPTION;?></td>
					<td><br />
					<?php
						JLMS_editorArea( 'editor2', $link_details->link_description, 'link_description', '100%', '250', '40', '20' ) ; ?>
					</td>
				</tr>
			</table>
			<input type="hidden" name="option" value="<?php echo $option;?>" />
			<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
			<input type="hidden" name="task" value="save_link" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="course_id" value="<?php echo $id;?>" />
			<input type="hidden" name="id" value="<?php echo $link_details->id;?>" />
		</form>
<?php
		JLMS_TMPL::CloseTS();
		JLMS_TMPL::CloseMT();
	}

	// to do: vstavit' proverku na nalichie otmechennogo polya pri 'delete' i 'edit' (25.10 - OK)
	// to do: vstavit' knopku 'BACK' (25. 10 ne nado)
	// to do: ispravit' JS proverki	(25.10 OK)
	function showCourseLinks( $id, $option, &$rows ) {
		$user = JLMSFactory::getUser();
		$db = & JFactory::getDbo();
		$JLMS_ACL = & JLMSFactory::getACL();
		$JLMS_CONFIG = JLMSFactory::getConfig();
		$Itemid = $JLMS_CONFIG->get('Itemid');
?>
<script language="javascript" type="text/javascript">
<!--//--><![CDATA[//><!--
function submitbutton(pressbutton) {
	var form = document.adminForm;
	if ( ((pressbutton == 'link_delete') || (pressbutton == 'pre_link_edit')) && (form.boxchecked.value == "0")){
		alert("<?php echo _JLMS_ALERT_SELECT_ITEM;?>");
	} else {
		form.task.value = pressbutton;
		form.submit();
	}
}
function submitbutton_order(pressbutton, item_id) {
	var form = document.adminForm;
	if ((pressbutton == 'link_orderup') || (pressbutton == 'link_orderdown')){
		if (item_id) {
		form.task.value = pressbutton;
		form.row_id.value = item_id;
		form.submit();
		}
	}
}
function submitbutton_change(pressbutton, state) {
	var form = document.adminForm;
	if (pressbutton == 'change_link'){
		if (form.boxchecked.value == "0") {
			alert("<?php echo _JLMS_ALERT_SELECT_ITEM;?>");
		} else {
			form.task.value = pressbutton;
			form.state.value = state;
			form.submit();
		}
	}
}
function submitbutton_change2(pressbutton, state, cid_id) {
	var form = document.adminForm;
	if (pressbutton == 'change_link'){
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
		JLMS_TMPL::ShowHeader('link', _JLMS_LINKS_COURSE_LINKS, $hparams);

		JLMS_TMPL::OpenTS();
?>
		<form action="<?php echo $JLMS_CONFIG->get('live_site') . "/index.php?option=$option&amp;Itemid=$Itemid";?>" method="post" name="adminForm">
<?php 	if (!empty($rows)) { ?>
			<table width="100%" cellpadding="0" cellspacing="0" border="0" class="<?php echo JLMSCSS::_('jlmslist');?>">
				<tr>
					<<?php echo JLMSCSS::tableheadertag();?> width="20" class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center">#</<?php echo $JLMS_CONFIG->get('tableheader_tag', 'td');?>>
				<?php if ($JLMS_ACL->CheckPermissions('links', 'manage') || $JLMS_ACL->CheckPermissions('links', 'publish')) { ?>
					<<?php echo JLMSCSS::tableheadertag();?> width="20" class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></<?php echo $JLMS_CONFIG->get('tableheader_tag', 'td');?>>
				<?php } ?>
					<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" width="40%"><?php echo _JLMS_LINKS_TBL_HEAD_LINK;?></<?php echo $JLMS_CONFIG->get('tableheader_tag', 'td');?>>
				<?php if ($JLMS_ACL->CheckPermissions('links', 'publish')) { ?>
					<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" width="1">&nbsp;</<?php echo $JLMS_CONFIG->get('tableheader_tag', 'td');?>>
				<?php }
					if ($JLMS_ACL->CheckPermissions('links', 'order')) { ?>
					<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" colspan="2" width="1">&nbsp;</<?php echo $JLMS_CONFIG->get('tableheader_tag', 'td');?>>
				<?php } ?>
					<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" width="60%"><?php echo _JLMS_LINKS_TBL_HEAD_DESCR;?></<?php echo $JLMS_CONFIG->get('tableheader_tag', 'td');?>>
				</tr>
			<?php
			$there_were_squeezeboxes = false;
			$k = 1;
			for ($i=0, $n=count($rows); $i < $n; $i++) {
				$is_squeezebox = false;
				$row = $rows[$i];
				$link 	= $row->link_href;
				if ($row->link_type == 2) {
					$link = sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=view_inline_link&course_id=$id&id=$row->id");
				}
				if ($row->link_type == 3) {
					$is_squeezebox = true;
					$there_were_squeezeboxes = true;
				}
				$alt = ($row->published)?_JLMS_STATUS_PUB:_JLMS_STATUS_UNPUB;
				$image = ($row->published)?'btn_accept.png':'btn_cancel.png';
				$state = ($row->published)?0:1;
				if ($row->is_time_related) {
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
				$checked = mosHTML::idBox( $i, $row->id);?>
				<tr class="<?php echo JLMSCSS::_('sectiontableentry'.$k); ?>">
					<td align="center"><?php echo ( $i + 1 ); ?></td>
				<?php if ($JLMS_ACL->CheckPermissions('links', 'manage') || $JLMS_ACL->CheckPermissions('links', 'publish')) {
					$show_check = true;
					if ($JLMS_ACL->CheckPermissions('links', 'only_own_items') && $row->owner_id != $user->get('id')) {
						$show_check = false;
					} elseif ($JLMS_ACL->CheckPermissions('links', 'only_own_role') && $JLMS_ACL->GetRole() != $JLMS_ACL->UserSystemRole($db, $row->owner_id)) {
						$show_check = false;
					} ?>
					<td>
					<?php echo $show_check?$checked:'&nbsp;'; ?>
					</td>
				<?php } ?>
					<td align="left">
						<?php if ($is_squeezebox) {
							$tmp_params = new JLMSParameters($row->params);
							$x_size = 0;
							$y_size = 0;
							if ( is_object($tmp_params) && $tmp_params->get('display_width') ) {
								$x_size = intval($tmp_params->get('display_width'));
							} elseif ( isset($tmp_params->display_width) ) {
								$x_size = intval($tmp_params->display_width);
							}
							if ( is_object($tmp_params) && $tmp_params->get('display_height') ) {
								$y_size = intval($tmp_params->get('display_height'));
							} elseif ( isset($tmp_params->display_height) ) {
								$y_size = intval($tmp_params->display_height);
							} ?>
							<a class="jlms_modal" rel="{handler:'iframe', size:{x:<?php echo $x_size;?>,y:<?php echo $y_size;?>}}" href="<?php echo $link;?>" title="<?php echo str_replace('"','&quot;',$row->link_name);?>">
								<?php echo $row->link_name;?>
							</a>
						<?php } else { ?>
							<?php if ($link) { ?>
							<a <?php echo (!$row->link_type?'target="_blank" ':' ');?>href="<?php echo $link;?>" title="<?php echo str_replace('"','&quot;',$row->link_name);?>">
								<?php echo $row->link_name;?>
							</a>
							<?php } else { echo $row->link_name; } ?>
						<?php } ?>
						<?php if($JLMS_CONFIG->get('show_links_authors', 0)){?>
						<br />
						<span class="small"><?php echo _JLMS_HOME_AUTHOR . "&nbsp;" . $row->author_name;?></span>
						<?php } ?>
					</td>
				<?php if ($JLMS_ACL->CheckPermissions('links', 'publish')) { ?>
					<td valign="middle">
					<?php if ($JLMS_ACL->CheckPermissions('links', 'only_own_items') && $row->owner_id != $user->get('id')) { ?>
						<img class="JLMS_png" src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/<?php echo $image;?>" width="16" height="16" border="0" alt="<?php echo $alt;?>" />
					<?php } elseif ($JLMS_ACL->CheckPermissions('links', 'only_own_role') && $JLMS_ACL->GetRole() != $JLMS_ACL->UserSystemRole($db, $row->owner_id)) { ?>
						<img class="JLMS_png" src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/<?php echo $image;?>" width="16" height="16" border="0" alt="<?php echo $alt;?>" />
					<?php } else {
						if ($row->is_time_related) {
							if ($row->published) {
								$image = 'btn_publish_wait.png';
							}
							$tooltip_link = 'javascript:submitbutton_change2(\'change_link\','.$state.','.$row->id.')';
							$tooltip_name = '<img class="JLMS_png" src="'.$JLMS_CONFIG->get('live_site').'/components/com_joomla_lms/lms_images/toolbar/'.$image.'" width="16" height="16" border="0" alt="'.$alt.'" />';
							echo JLMS_toolTip($alt, $tooltip_txt, $tooltip_name, $tooltip_link);
						} else {
							echo '<a href="javascript:submitbutton_change2(\'change_link\','.$state.','.$row->id.')" title="'.$alt.'">';
							echo '<img class="JLMS_png" src="'.$JLMS_CONFIG->get('live_site').'/components/com_joomla_lms/lms_images/toolbar/'.$image.'" width="16" height="16" border="0" alt="'.$alt.'" />';
							echo '</a>';
						}
					} ?>
					</td>
				<?php } if ($JLMS_ACL->CheckPermissions('links', 'order')) { ?>
					<td><?php echo JLMS_orderUpIcon($i, $row->id, true, 'link_orderup');?></td>
					<td><?php echo JLMS_orderDownIcon($i, $n, $row->id, true, 'link_orderdown');?></td>
				<?php } ?>
					<td><?php echo $row->link_description?$row->link_description:'&nbsp;';?></td>
				</tr>
				<?php
				$k = 3 - $k;
			} ?>
			</table>
<?php
			if ($there_were_squeezeboxes) {
				JLMS_initialize_SqueezeBox(false);
			}

		} else {
			echo '<div class="joomlalms_user_message">'._JLMS_NO_ITEMS_HERE.'</div>';
		}
?>
			<input type="hidden" name="option" value="<?php echo $option;?>" />
			<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
			<input type="hidden" name="task" value="setup_category" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="id" value="<?php echo $id;?>" />
			<input type="hidden" name="row_id" value="0" />
			<input type="hidden" name="state" value="0" />
			<input type="hidden" name="cid2" value="0" />
		</form>
<?php
		JLMS_TMPL::CloseTS();
		if ( $id && ($JLMS_ACL->CheckPermissions('links', 'manage') || $JLMS_ACL->CheckPermissions('links', 'publish')) ) {
			$link_new = JLMSRoute::_("index.php?option=$option&amp;Itemid=$Itemid&amp;task=pre_create_link&amp;id=$id");
			$controls = array();
			if ($JLMS_ACL->CheckPermissions('links', 'publish')) {
				$controls[] = array('href' => "javascript:submitbutton_change('change_link',1);", 'title' => _JLMS_SET_PUB, 'img' => 'publish');
				$controls[] = array('href' => "javascript:submitbutton_change('change_link',0);", 'title' => _JLMS_SET_UNPUB, 'img' => 'unpublish');
				if ($JLMS_ACL->CheckPermissions('links', 'manage')) {
					$controls[] = array('href' => 'spacer');
				}
			}
			if ($JLMS_ACL->CheckPermissions('links', 'manage')) {
				$controls[] = array('href' => $link_new, 'onclick' => "", 'title' => _JLMS_LINKS_IMG_NEW_LINK, 'img' => 'add');
				$controls[] = array('href' => "javascript:submitbutton('link_delete');", 'title' => _JLMS_LINKS_IMG_DEL_LINK, 'img' => 'delete');
				$controls[] = array('href' => "javascript:submitbutton('pre_link_edit');", 'title' => _JLMS_LINKS_IMG_EDIT_LINK, 'img' => 'edit');
			}
			JLMS_TMPL::ShowControlsFooter($controls);
		}
		JLMS_TMPL::CloseMT();
	}
}
?>