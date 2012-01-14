<?php
/**
* joomla_lms.docs.html.php
* (c) JoomaLMS eLearning Software http://www.joomlalms.com/
**/

// no direct access
defined( '_JLMS_EXEC' ) or die( 'Restricted access' );

class JLMS_docs_html {
	function show_ZipPack( $course_id, $option, &$row_zip, &$lists, $doc_type = 'zip' ) {
		$JLMS_CONFIG = & JLMSFactory::getConfig();
		$Itemid = $JLMS_CONFIG->get('Itemid');

		JLMS_TMPL::OpenMT();

		$hparams = array();
		$toolbar = array();
		if ($doc_type == 'document_contents') {
			$toolbar[] = array('btn_type' => 'archive', 'btn_str' => _JLMS_DOWNLOAD,  'btn_js' => sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid&amp;task=get_document&amp;course_id=$course_id&amp;id=$row_zip->doc_id&amp;force=force") );
		}
		/*$back_link = sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid&amp;task=documents&amp;id=$course_id");
		if (isset($lists['lpath_id']) && $lists['lpath_id']) {
			$back_link = sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid&amp;task=compose_lpath&amp;course_id=$course_id&amp;id=".$lists['lpath_id']);
		}*/

		//01.12.2007 - (DEN) - Compatibility for returning from the document view to the doc.tool/course homepage/lpaths list.
		$back_link = 'javascript:window.history.go(-1);';
		$toolbar[] = array('btn_type' => 'back', 'btn_js' => $back_link );
		JLMS_TMPL::ShowHeader('doc', $row_zip->doc_name, $hparams, $toolbar);

		if ($doc_type == 'zip') {
			JLMS_TMPL::OpenTS();
			echo '<iframe id="zip_contents" name="zip_contents" height="600" width="100%" frameborder="0" scrolling="auto" src="'.$JLMS_CONFIG->get('live_site') . "/" . _JOOMLMS_SCORM_PLAYER . "/".$row_zip->zip_folder."/".$row_zip->startup_file.'">'._JLMS_IFRAMES_REQUIRES.'</iframe>';
			JLMS_TMPL::CloseTS();
		} elseif ($doc_type == 'content') {
			JLMS_TMPL::OpenTS();
			$text = JLMS_ShowText_WithFeatures($row_zip->doc_description);
			echo $text;
			JLMS_TMPL::CloseTS();
		} elseif ($doc_type == 'document_contents') {
			JLMS_TMPL::OpenTS('', ' id="jlms_doc_contents_container"');
			echo $row_zip->doc_description;
			JLMS_TMPL::CloseTS();
		}
		JLMS_TMPL::CloseMT();
	}

	function show_PageChooseStartup( $course_id, $option, &$zp_files, &$lists ) {
		$JLMS_CONFIG = & JLMSFactory::getConfig();
		$Itemid = $JLMS_CONFIG->get('Itemid');
?>
<script language="javascript" type="text/javascript">
<!--//--><![CDATA[//><!--
function submitbutton(pressbutton) {
	var form=document.adminForm;
	if ((pressbutton=='docs_save_startup') && (form.zip_contents.value=="")){alert("<?php echo _JLMS_DOCS_ALERT_STARTUP_FILE;?>");
	} else {form.task.value = pressbutton;form.submit();}
}
//--><!]]>
</script>
<?php
		JLMS_TMPL::OpenMT();

		$hparams = array();
		$toolbar = array();
		$toolbar[] = array('btn_type' => 'save', 'btn_js' => "javascript:submitbutton('docs_save_startup');");
		$toolbar[] = array('btn_type' => 'cancel', 'btn_js' => "javascript:submitbutton('cancel_doc');");
		JLMS_TMPL::ShowHeader('doc', _JLMS_DOCS_TITLE_CHOOSE_STARTUP_FILE, $hparams, $toolbar);

		JLMS_TMPL::OpenTS();
?>
		<form action="<?php echo sefRelToAbs("index.php?option=".$option."&amp;Itemid=".$Itemid);?>" method="post" name="adminForm">
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td width="100%" valign="middle" align="center" style="vertical-align:middle; text-align:center">
					<br />
					<?php echo $zp_files;?>
					</td>
				</tr>
			</table>
			<input type="hidden" name="option" value="<?php echo $option;?>" />
			<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
			<input type="hidden" name="task" value="docs_save_startup" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="id" value="<?php echo $course_id;?>" />
			<input type="hidden" name="doc_id" value="<?php echo $lists['doc_id'];?>" />
		</form>
<?php
		JLMS_TMPL::CloseTS();
		JLMS_TMPL::CloseMT();
	}

	function showEditDocument( $doc_details, &$lists, $option, $id ) {
		$JLMS_CONFIG = & JLMSFactory::getConfig();
		$Itemid = $JLMS_CONFIG->get('Itemid');
		
		$is_dis_start = !($doc_details->publish_start == 1);
		$is_dis_end = !($doc_details->publish_end == 1);
		?>
<script language="javascript" type="text/javascript">
<!--//--><![CDATA[//><!--
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
	var form=document.adminForm;
	try {
		form.onsubmit();
	} catch(e) {
		//alert(e);
	}
	if (is_start_c == 1) {if (form.start_date.value == ''){jlms_getDate('start');}}
	if (is_end_c == 1) {if (form.end_date.value == ''){jlms_getDate('end');}}
	<?php if (!$doc_details->id && !($doc_details->folder_flag ==1)) { ?>
	if ((pressbutton=='save_doc') && ((form.userfile0.value=="") && (form.doc_name0.value==""))){alert("<?php echo _JLMS_DOCS_SELECT_FILE_OR_ENTER_NAME;?>");
	<?php } else {?>
	if ((pressbutton=='save_doc') && (form.doc_name0.value=="")){alert("<?php echo _JLMS_PL_ENTER_NAME;?>");
	<?php } ?>
	} else {

	<?php 
	if(class_exists('JFactory')){ 
		$editor =& JLMS07062010_JFactory::getEditor();
    	echo $editor->save( 'doc_description' );
	} else {
		getEditorContents( 'editor1', 'doc_description' );
	}
	?>

		form.task.value = pressbutton;
		form.submit();
	}
}
function submitbutton_aF2(pb, ri2) {
	var form=document.adminForm2;
	if (pb=='add_perms') {
		form.role_id2.value = 0;
		var sel_value = form.role_id.options[form.role_id.selectedIndex].value;
		if (sel_value == 0 || sel_value == '0') {
		} else {
			form.task2.value = pb;
			form.submit();
		}
	} else if (pb=='del_perms') {
		form.role_id2.value = ri2;
		form.task2.value = pb;
		form.submit();
	}
}
var is_start_c = <?php echo ($doc_details->publish_start)?'1':'0'; ?>; var is_end_c = <?php echo ($doc_details->publish_end)?'1':'0'; ?>;
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
<?php if (!($doc_details->folder_flag == 1) && !$doc_details->id) { ?>
function jlms_changeZips_values(elem) {
	if (elem.name == 'upload_zip') {
		if (elem.value == '1' || elem.value == 1) {
			if (elem.form.zip_package.length) {
				var i;
				for (i = 0; i<elem.form.zip_package.length; i++) {
					if (elem.form.zip_package[i].value > 0) {
						if (elem.form.zip_package[i].checked) {
							elem.form.zip_package[i].checked = false;
						}
					} else {
						if (!elem.form.zip_package[i].checked) {
							elem.form.zip_package[i].checked = true;
						}
					}

				}
			}
			document.getElementById('jlms_newformbtn_row').style.display = '';
			document.getElementById('jlms_newformbtn_row').style.visibility = 'visible';
		}
	} else if (elem.name == 'zip_package') {
		if (elem.value == '1' || elem.value == 1) {
			if (elem.form.upload_zip.length) {
				var i;
				for (i = 0; i<elem.form.upload_zip.length; i++) {
					if (elem.form.upload_zip[i].value > 0) {
						if (elem.form.upload_zip[i].checked) {
							elem.form.upload_zip[i].checked = false;
						}
					} else {
						if (!elem.form.upload_zip[i].checked) {
							elem.form.upload_zip[i].checked = true;
						}
					}

				}
			}
			i = 1;
			while(document.getElementById('tr1_' + i)) {
				document.getElementById('tr1_' + i).style.display = 'none';
				document.getElementById('tr1_' + i).style.visibility = 'hidden';
				document.getElementById('tr2_' + i).style.display = 'none';
				document.getElementById('tr2_' + i).style.visibility = 'hidden';
				i++;
			}
			document.getElementById('jlms_newformbtn_row').style.display = 'none';
			document.getElementById('jlms_newformbtn_row').style.visibility = 'hidden';
		}
	}
}
<?php } else { ?>
function jlms_changeZips_values(elem) {
	
}
<?php } ?>
function Add_new_form() {
	i = 1;
	while(document.getElementById('tr1_' + i)) {
		if( document.getElementById('tr1_' + i).style.display == 'none' && document.getElementById('tr1_' + i).style.visibility == 'hidden') {
			document.getElementById('tr1_' + i).style.display = '';
			document.getElementById('tr1_' + i).style.visibility = 'visible';
			document.getElementById('tr2_' + i).style.display = '';
			document.getElementById('tr2_' + i).style.visibility = 'visible';
			break;
		}
		i++;
	}	
}
//--><!]]>
</script>
<?php
		JLMS_TMPL::OpenMT();

		$hparams = array();
		$toolbar = array();
		$title = '';
		if ($doc_details->folder_flag == 1) {
			$title = $doc_details->id ? _JLMS_DOCS_TITLE_EDIT_FOLDER : _JLMS_DOCS_TITLE_NEW_FOLDER;
		} else {
			$title = $doc_details->id ? _JLMS_DOCS_TITLE_EDIT_DOC : _JLMS_DOCS_TITLE_NEW_DOC;
		}
		$toolbar[] = array('btn_type' => 'save', 'btn_js' => "javascript:submitbutton('save_doc');");
		$toolbar[] = array('btn_type' => 'cancel', 'btn_js' => "javascript:submitbutton('cancel_doc');");
		JLMS_TMPL::ShowHeader('doc', $title, $hparams, $toolbar);

		JLMS_TMPL::OpenTS(); 
?>
		<form action="<?php echo sefRelToAbs("index.php?option=".$option."&amp;Itemid=".$Itemid);?>" method="post" name="adminForm" enctype="multipart/form-data" onsubmit="setgood();">
			<table width="100%" cellpadding="0" cellspacing="0" border="0" id="jlms_item_properties">
				<tr>
					<td width="30%" valign="middle" style="vertical-align:middle"><?php echo _JLMS_ENTER_NAME;?></td>
					<td><input class="inputbox" size="40" type="text" name="doc_name0" value="<?php echo str_replace('"','&quot;',$doc_details->doc_name);?>" />
					</td>
				</tr>
			<?php if (!($doc_details->folder_flag == 1)) { ?>
				<tr>
					<td valign="middle" style="vertical-align:middle"><br /><?php echo _JLMS_CHOOSE_FILE;?></td>
					<td>
						<br /><input size="40" class="inputbox" type="file" name="userfile0" />
					</td>
				</tr>
			<?php if (!$doc_details->id) {?>
			<?php	for($i=1;$i<10;$i++) {?>
						<tr style="visibility:hidden; display:none;" id="tr1_<?php echo $i;?>">
							<td valign="middle" style="vertical-align:middle"><br /><?php echo _JLMS_ENTER_NAME;?></td>
							<td><br /><input class="inputbox" size="40" type="text" name="doc_name<?php echo $i;?>" value="<?php echo str_replace('"','&quot;',$doc_details->doc_name);?>" />
							</td>
						</tr>
						<tr style="visibility:hidden; display:none;" id="tr2_<?php echo $i;?>">
							<td valign="middle" style="vertical-align:middle"><br /><?php echo _JLMS_CHOOSE_FILE;?></td>
							<td>
								<br /><input size="40" class="inputbox" type="file" name="userfile<?php echo $i;?>" />
							</td>
						</tr>
				<?php }?>
				<tr id="jlms_newformbtn_row">
					<td width="30%" valign="middle" style="vertical-align:middle" colspan="2"><br />
					<input type="button" onclick="javascript:Add_new_form();" value="+" style="width: 70px;" name="add_new_g_cat" class="text_area"/>
					</td>
				</tr>
			<?php }?>
			<?php if (!($doc_details->folder_flag == 1) && !$doc_details->id) {?>
				<tr>
					<td valign="middle" style="vertical-align:middle"><br /><?php echo _JLMS_OUTDOCS_UPLOAD_ZIP_FILES;?>:</td>
					<td><br /><?php echo $lists['upload_zip'];?></td>
				</tr>
				<tr><td colspan="2"><span class="small"><?php echo _JLMS_OUTDOCS_UPLOAD_ZIP_FILES_NOTE;?></span></td></tr>
			<?php }?>
				<tr>
					<td valign="middle" style="vertical-align:middle"><br /><?php echo _JLMS_DOCS_UPLOAD_CONTENT_ZIP_PACK;?>:</td>
					<td><br /><?php echo $lists['content_zip_pack'];?></td>
				</tr>
			<?php } ?>
				<tr>
					<td valign="middle" style="vertical-align:middle"><br /><?php echo _JLMS_PLACE_IN;?></td>
					<td><br /><?php echo $lists['course_folders'];?></td>
				</tr>
				<tr>
					<td valign="middle" style="vertical-align:middle"><br /><?php echo _JLMS_PUBLISHING;?></td>
					<td><br /><?php echo $lists['publishing'];?></td>
				</tr>
				<tr>
					<td valign="middle"><br /><?php echo _JLMS_START_DATE;?></td>
					<td valign="middle" style="vertical-align:middle "><br />
						<table class="jlms_date_outer" cellpadding="0" cellspacing="0" border="0"><tr><td valign="middle"><input type="checkbox" value="1" name="is_start" onclick="jlms_Change_start()" <?php echo $doc_details->publish_start?'checked':'';?> /></td>
						<td valign="middle" style="vertical-align:middle ">
						<?php
						$s_date = ($is_dis_start)?date('Y-m-d'):$doc_details->start_date;
						echo JLMS_HTML::_('calendar.calendar',$s_date,'start','start');
						?>
						</td></tr></table>
					</td>
				</tr>	
				<tr>
					<td><br /><?php echo _JLMS_END_DATE;?></td>
					<td valign="middle" style="vertical-align:middle "><br />
						<table class="jlms_date_outer" cellpadding="0" cellspacing="0" border="0"><tr><td valign="middle"><input type="checkbox" value="1" name="is_end" onclick="jlms_Change_end()" <?php echo $doc_details->publish_end?'checked':'';?> /></td>
						<td valign="middle" style="vertical-align:middle ">
						<?php 
						$e_date = ($is_dis_end)?date('Y-m-d'):$doc_details->end_date;
						echo JLMS_HTML::_('calendar.calendar',$e_date,'end','end');
						?>
						</td></tr></table>
					</td>
				</tr>
				<tr>
					<td valign="top" style="vertical-align:top "><br /><?php echo _JLMS_IS_TIME_RELATED;?></td>
					<td><br />
						<?php JLMS_HTML::_('showperiod.field', $doc_details->is_time_related, $doc_details->show_period ) ?>
					</td>
				</tr>	
				<tr>
					<td colspan="2" valign="top" align="left" style="text-align:left "><br /><?php echo _JLMS_DESCRIPTION;?></td>
				</tr>	
				<tr>
					<td colspan="2">
					<?php
					JLMS_editorArea( 'editor1', $doc_details->doc_description, 'doc_description', '100%', '250', '40', '20' );
					?>
					</td>
				</tr>
			</table>
			<input type="hidden" name="option" value="<?php echo $option;?>" />
			<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
			<input type="hidden" name="task" value="update_document" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="course_id" value="<?php echo $id;?>" />
			<input type="hidden" name="folder_flag" value="<?php echo $doc_details->folder_flag;?>" />
			<input type="hidden" name="id" value="<?php echo $doc_details->id;?>" />
		</form>

<?php
$JLMS_ACL = & JLMSFactory::getACL();
if ($JLMS_ACL->CheckPermissions('docs', 'set_permissions') && isset($doc_details->id) && $doc_details->id && isset($doc_details->folder_flag) && $doc_details->folder_flag == 1) {
	$db = & JFactory::getDbo();
	$query = "SELECT a.*, b.lms_usertype as role_name FROM #__lms_documents_perms as a LEFT JOIN #__lms_usertypes as b ON a.role_id = b.id WHERE a.doc_id = ".intval($doc_details->id);
	$db->SetQuery($query);
	$doc_perms = $db->LoadObjectList();


	$role_types = '(1,2,4,5)';

	$query = "SELECT id as value, lms_usertype as text, roletype_id, IF(roletype_id = 4, 1, IF(roletype_id = 2, 2, IF(roletype_id = 1, 4, 3))) as ordering FROM #__lms_usertypes WHERE roletype_id IN $role_types ORDER BY ordering, lms_usertype";
	$db->SetQuery($query);
	$roles = $db->LoadObjectList('value');


	$cur_role = 0;//$view_by ? $row->role_id : $row->lms_usertype_id;
	$sel_name = 'role_id';// : 'lms_usertype_id';
	$sel_html = '<select id="roles_selections" class="text_area" style="width:266px" name="'.$sel_name.'">';
	$sel_html .= '<option value="0"> - Select role - </option>';
	$prev_roletype = 0;
	foreach ($roles as $role) {
		if ($role->roletype_id != $prev_roletype) {
			if ($prev_roletype) { $sel_html .= '</optgroup>'; }
			$prev_roletype = $role->roletype_id;
			if ($role->roletype_id == 4) {
				$sel_html .= '<optgroup label="Administrator roles">';
			}
			if ($role->roletype_id == 2) {
				$sel_html .= '<optgroup label="Teacher roles">';
			}
			if ($role->roletype_id == 5) {
				$sel_html .= '<optgroup label="Assistant roles">';
			}
			if ($role->roletype_id == 1) {
				$sel_html .= '<optgroup label="Learner roles">';
			}
		}
		$selected = '';
		if ($role->value == $cur_role) {
			$selected = ' selected="selected"';
		}
		$sel_html .= '<option value="'.$role->value.'"'.$selected.'>'.$role->text.'</option>';
	}
	$sel_html .= '</optgroup>';
	$sel_html .= '</select>';

$custom_perm_title = _JLMS_CUSTOM_PERMISSIONS;
if ($JLMS_CONFIG->get('is_trial', false) && $JLMS_CONFIG->get('trial_custom_perm_heading_text', '')) {
	$custom_perm_title .= $JLMS_CONFIG->get('trial_custom_perm_heading_text', '');
}
?>
			<a name="perms"></a><div class="contentheading"><?php echo $custom_perm_title;?></div>
<?php
if($JLMS_CONFIG->get('is_trial', false) && $JLMS_CONFIG->get('trial_custom_perm_page_text', '')) {
	echo '<div class="joomlalms_sys_message">'.$JLMS_CONFIG->get('trial_custom_perm_page_text', '').'</div>';
}
?>
		<form action="<?php echo sefRelToAbs("index.php?option=".$option."&amp;Itemid=".$Itemid);?>" method="post" name="adminForm2" enctype="multipart/form-data">
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<<?php echo $JLMS_CONFIG->get('tableheader_tag', 'td');?> width="16" class="sectiontableheader">&nbsp;</<?php echo $JLMS_CONFIG->get('tableheader_tag', 'td');?>>
					<<?php echo $JLMS_CONFIG->get('tableheader_tag', 'td');?> class="sectiontableheader"><?php echo _JLMS_CPERM_ROLE_NAME; ?></<?php echo $JLMS_CONFIG->get('tableheader_tag', 'td');?>>
					<<?php echo $JLMS_CONFIG->get('tableheader_tag', 'td');?> class="sectiontableheader"><?php echo _JLMS_CPERM_VIEW; ?></<?php echo $JLMS_CONFIG->get('tableheader_tag', 'td');?>>
					<<?php echo $JLMS_CONFIG->get('tableheader_tag', 'td');?> class="sectiontableheader"><?php echo _JLMS_CPERM_VIEW_ALL; ?></<?php echo $JLMS_CONFIG->get('tableheader_tag', 'td');?>>
					<<?php echo $JLMS_CONFIG->get('tableheader_tag', 'td');?> class="sectiontableheader"><?php echo _JLMS_CPERM_ORDER; ?></<?php echo $JLMS_CONFIG->get('tableheader_tag', 'td');?>>
					<<?php echo $JLMS_CONFIG->get('tableheader_tag', 'td');?> class="sectiontableheader"><?php echo _JLMS_CPERM_PUBLISH; ?></<?php echo $JLMS_CONFIG->get('tableheader_tag', 'td');?>>
					<<?php echo $JLMS_CONFIG->get('tableheader_tag', 'td');?> class="sectiontableheader"><?php echo _JLMS_CPERM_MANAGE; ?></<?php echo $JLMS_CONFIG->get('tableheader_tag', 'td');?>>
				</tr>
<?php
$k = 1;
if (count($doc_perms)) {
	$yes_img = '<img height="16" width="16" border="0" alt="V" src="'.$JLMS_CONFIG->get('live_site').'/components/com_joomla_lms/lms_images/toolbar/btn_accept.png" class="JLMS_png"/>';
	$no_img = '<img height="16" width="16" border="0" alt="X" src="'.$JLMS_CONFIG->get('live_site').'/components/com_joomla_lms/lms_images/toolbar/btn_cancel.png" class="JLMS_png"/>';
	foreach ($doc_perms as $doc_perm) {
		echo '<tr class="sectiontableentry'.$k.'">';
		echo '<td>';
		echo '<a href="javascript:submitbutton_aF2(\'del_perms\', '.$doc_perm->role_id.');"><img src="'.$JLMS_CONFIG->get('live_site').'/components/com_joomla_lms/lms_images/toolbar/btn_delete.png" class="JLMS_png" width="16" height="16" border="0" alt="btn_add" /></a>';
		echo '</td>';
		echo '<td align="left">'.$doc_perm->role_name.'</td>';
		echo '<td align="center">'.($doc_perm->p_view?$yes_img:$no_img).'</td>';
		echo '<td align="center">'.($doc_perm->p_viewall?$yes_img:$no_img).'</td>';
		echo '<td align="center">'.($doc_perm->p_order?$yes_img:$no_img).'</td>';
		echo '<td align="center">'.($doc_perm->p_publish?$yes_img:$no_img).'</td>';
		echo '<td align="center">'.($doc_perm->p_manage?$yes_img:$no_img).'</td>';
		echo '</tr>';
	}
} ?>
				<tr class="sectiontableentry<?php echo $k;?>">
					<td><a href="javascript:submitbutton_aF2('add_perms', 0);"><img src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_add.png" class="JLMS_png" width="16" height="16" border="0" alt="btn_add" /></a></td>
					<td><?php echo $sel_html;?></td>
					<td align="center"><input class="inputbox" type="checkbox" name="p_view" value="1" /></td>
					<td align="center"><input class="inputbox" type="checkbox" name="p_viewall" value="1" /></td>
					<td align="center"><input class="inputbox" type="checkbox" name="p_order" value="1" /></td>
					<td align="center"><input class="inputbox" type="checkbox" name="p_publish" value="1" /></td>
					<td align="center"><input class="inputbox" type="checkbox" name="p_manage" value="1" /></td>
				</tr>

			</table>
			<input type="hidden" name="option" value="<?php echo $option;?>" />
			<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
			<input type="hidden" name="task" value="save_doc" />
			<input type="hidden" name="role_id2" value="0" />
			<input type="hidden" name="task2" value="add_perms" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="course_id" value="<?php echo $id;?>" />
			<input type="hidden" name="folder_flag" value="<?php echo $doc_details->folder_flag;?>" />
			<input type="hidden" name="id" value="<?php echo $doc_details->id;?>" />
		</form>
<?php
}
?>

<?php 
		JLMS_TMPL::CloseTS();
		JLMS_TMPL::CloseMT();
	}

	//to do: sdelat' preloadimages() - ok
	//to do: vstavit' proverku na nalichie otmechennogo polya pri 'delete' i 'edit' - ok
	// (TIP) v JS pri hide rows stoit display = ''; (dlya CSS2 standarta nugno display = 'table-row' - no iz-za etogo glucki v IE !!)
	function showCourseDocuments( $id, $option, &$rows, &$lists, &$possibilities) {
		$JLMS_CONFIG = & JLMSFactory::getConfig();
		$Itemid = $JLMS_CONFIG->get('Itemid');
		$db = & JFactory::getDbo();
		$user = JLMSFactory::getUser();
		$JLMS_ACL = & JLMSFactory::getACL();
		
		$lms_img_path = $JLMS_CONFIG->get('live_site') . '/' . $JLMS_CONFIG->get('lms_path_to_images', 'components/com_joomla_lms/lms_images');
		
		$rows_c = $lists['collapsed_folders'];?>
<script language="javascript" type="text/javascript">
<!--//--><![CDATA[//><!--
function submitbutton(pressbutton) {
	var form = document.adminForm;
	if ((pressbutton == 'doc_delete') && (form.boxchecked.value == '0')) {
		alert( "<?php echo _JLMS_ALERT_SELECT_ITEM;?>" );
	} else if((pressbutton == 'doc_delete')){
		if(confirm('<?php echo _JLMS_OUTDOCS_JS_CONFIRM_DELETE;?>')){
			form.task.value = pressbutton;
			form.submit();
		}	
	} else if ((pressbutton == 'edit_doc') && (form.boxchecked.value == '0')) {
		alert( "<?php echo _JLMS_ALERT_SELECT_ITEM;?>" );
	} else {
		form.task.value = pressbutton;
		form.submit();
	}
}
function submitbutton_change(pressbutton, state) {
	var form = document.adminForm;
	if (pressbutton == 'change_doc'){
		if (form.boxchecked.value == '0') {
			alert( "<?php echo _JLMS_ALERT_SELECT_ITEM;?>" );
		} else {
			form.task.value = pressbutton;
			form.state.value = state;
			form.submit();
		}
	}
}
function submitbutton_change2(pressbutton, state, cid_id) {
	var form = document.adminForm;
	if (pressbutton == 'change_doc'){
		form.task.value = pressbutton;
		form.state.value = state;
		form.cid2.value = cid_id;
		form.submit();
	}
}
function submitbutton_order(pressbutton, item_id) {
	var form = document.adminForm;
	if ((pressbutton == 'doc_orderup') || (pressbutton == 'doc_orderdown')){
		if (item_id) {
		form.task.value = pressbutton;
		form.row_id.value = item_id;
		form.submit();
		}
	}
}
function cf_saveorder(){
	var form = document.adminForm;	
	form.task.value = 'doc_saveorder';
	form.submit();
}

var TreeArray1 = new Array();
var TreeArray2 = new Array();
var Is_ex_Array = new Array();
<?php
$i = 1;
foreach ($rows as $row) {
	if ($row->p_view) {
		echo "TreeArray1[".$i."] = ".$row->parent_id.";";
		echo "TreeArray2[".$i."] = ".$row->id.";";
		if (in_array($row->id, $rows_c)) {
			echo "Is_ex_Array[".$i."] = 0;";
		} else {
			echo "Is_ex_Array[".$i."] = 1;";
		}
		$i ++;
	}
}
?>
function Hide_Folder(fid) {
	var vis_style = 'hidden';
	var dis_style = 'none';
	var i = 1;
	while (i < TreeArray1.length) {
		if (TreeArray1[i] == fid) {
			getObj('tree_row_'+TreeArray2[i]).style.visibility = vis_style;
			getObj('tree_row_'+TreeArray2[i]).style.display = dis_style;
			Hide_Folder(TreeArray2[i])
		}
		i ++;
	}
}
function Show_Folder(fid) {
	var vis_style = 'visible';
	var dis_style = '';
	var i = 1;
	while (i < TreeArray1.length) {
		if (TreeArray1[i] == fid) {
			if (getObj('tree_row_'+TreeArray2[i])) {
				getObj('tree_row_'+TreeArray2[i]).style.display = dis_style;
				getObj('tree_row_'+TreeArray2[i]).style.visibility = vis_style;
			}
			NoChange_Folder(TreeArray2[i])
		}
		i ++;
	}
}
function NoChange_Folder(fid) {
	var vis_style = 'hidden';var dis_style = 'none';var i = 1;var j = 0;
	while (i < TreeArray2.length) {
		if ( (TreeArray2[i] == fid) && (Is_ex_Array[i] == 1) ) {
			vis_style = 'visible';
			dis_style = '';
			j = 1;
		}
		i ++;
	}
	i = 1;
	while (i < TreeArray1.length) {
		if (TreeArray1[i] == fid) {
			getObj('tree_row_'+TreeArray2[i]).style.visibility = vis_style;
			getObj('tree_row_'+TreeArray2[i]).style.display = dis_style;
			if (j == 1) { NoChange_Folder(TreeArray2[i]);
			} else { Hide_Folder(TreeArray2[i]); }
		}
		i ++;
	}
}
function Ex_Folder(fid) {
	var i = 1;
	var j = 1;
	while (i < TreeArray2.length) {
		if ( (TreeArray2[i] == fid) && (Is_ex_Array[i] == 1) ) { j = 0; }
		i ++;
	}
	if (j == 1) {
		Show_Folder(fid);
		if (getObj('tree_img_' + fid).runtimeStyle) {
			var StStr = getObj('tree_img_' + fid).runtimeStyle.filter;
			var imgStr = getObj('tree_img_' + fid).outerHTML;
			imgStr = imgStr.replace('expandall.png','collapseall.png').replace('<?php echo _JLMS_DOCS_EXP_FOLDER;?>', '<?php echo _JLMS_DOCS_COLL_FOLDER;?>');
			StStr = StStr.replace('expandall.png','collapseall.png');
			getObj('tree_img_' + fid).outerHTML = imgStr;
			getObj('tree_img_' + fid).runtimeStyle.filter = StStr;
		} else {
			getObj('tree_img_' + fid).src = '<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/docs/collapseall.png';
			getObj('tree_img_' + fid).alt = '<?php echo _JLMS_DOCS_COLLAPSE;?>';
			getObj('tree_img_' + fid).title = '<?php echo _JLMS_DOCS_COLL_FOLDER;?>';
		}
	} else {
		Hide_Folder(fid);
		if (getObj('tree_img_' + fid).runtimeStyle) {
			var StStr = getObj('tree_img_' + fid).runtimeStyle.filter;
			var imgStr = getObj('tree_img_' + fid).outerHTML;
			imgStr = imgStr.replace('collapseall.png','expandall.png').replace('<?php echo _JLMS_DOCS_COLL_FOLDER;?>', '<?php echo _JLMS_DOCS_EXP_FOLDER;?>');
			StStr = StStr.replace('collapseall.png','expandall.png');
			getObj('tree_img_' + fid).outerHTML = imgStr;
			getObj('tree_img_' + fid).runtimeStyle.filter = StStr;
		} else {
			getObj('tree_img_' + fid).src = '<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/docs/expandall.png';
			getObj('tree_img_' + fid).alt = '<?php echo _JLMS_DOCS_EXPAND;?>';
			getObj('tree_img_' + fid).title = '<?php echo _JLMS_DOCS_EXP_FOLDER;?>';
		}
	}
	i = 1;
	while (i < TreeArray2.length) {
		if ( (TreeArray2[i] == fid) ) {
			if (Is_ex_Array[i] == 1) { Is_ex_Array[i] = 0;
			} else { Is_ex_Array[i] = 1; }
		}
		i ++;
	}
}
<?php if ($possibilities->manage && count($rows)) { ?>
var docs_save_blocked = false;
function Docs_WriteSysMsg(mes) {
	if (jlms_writetxt('joomlalms_sys_message', mes)) {
		getObj('joomlalms_sys_message_container').style.display = '';
		getObj('joomlalms_sys_message_container').style.visibility = 'visible';
	}
}
function Docs_save_view() {
	if (!docs_save_blocked) {
		var prepare_str = '';
		Docs_WriteSysMsg("<img src='<?php echo $JLMS_CONFIG->get('live_site').'/'.$JLMS_CONFIG->get('ajax_settings_small_indicator'); ?>' />");
		i = 1;
		var ps_pref = '';
		while (i < TreeArray2.length) {
			if (Is_ex_Array[i] == 0) {
				prepare_str = ''+ prepare_str + ps_pref + TreeArray2[i];
				ps_pref = '-';
			}
			i ++;
		}
		Docs_MakeRequest(prepare_str);
	}
}
function Docs_analize_req(http_request) {
	if (http_request.readyState == 4) {
		if ((http_request.status == 200)) {
			if(http_request.responseXML.documentElement == null){
				try {
					http_request.responseXML.loadXML(http_request.responseText)
				} catch (e) {
					/*alert("Can't load");*/
				}
			}
			response  = http_request.responseXML.documentElement;
			var mes = response.getElementsByTagName('message')[0].firstChild.data
		} else {
			var mes = "Request failed";
		}
		Docs_WriteSysMsg(mes);
		docs_save_blocked = false;
	}
}

function Docs_MakeRequest(req_str) {
	docs_save_blocked = true;
	var http_request = false;
	if (window.ActiveXObject) { // IE
		try { http_request = new ActiveXObject("Msxml2.XMLHTTP");
		} catch (e) {
			try { http_request = new ActiveXObject("Microsoft.XMLHTTP");
			} catch (e) {}
		}
	} else if (window.XMLHttpRequest) { // Mozilla, Safari,...
		http_request = new XMLHttpRequest();
		if (http_request.overrideMimeType) {
			http_request.overrideMimeType('text/xml');
		}
	}
	if (!http_request) {
		return false;
	}
	http_request.onreadystatechange = function() { Docs_analize_req(http_request); };
	var lp_url_prefix2 = '';
	var post_target = '<?php echo $JLMS_CONFIG->get('ajax_settings_request_safe_path'); ?>';
	var url = 'task=documents_view_save&id=<?php echo $id;?>&folders='+req_str;
	http_request.open("POST", post_target, true);
	http_request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	http_request.setRequestHeader("Content-length", url.length);
	//http_request.setRequestHeader("Connection", "close");
	http_request.send(url);
}
<?php } ?>
JLMS_preloadImages('<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/docs/expandall.png','<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/docs/collapseall.png'<?php echo ", '".$JLMS_CONFIG->get('live_site').'/'.$JLMS_CONFIG->get('ajax_settings_small_indicator')."'"; ?>);
//--><!]]>
</script>
<?php
		JLMS_TMPL::OpenMT();

		$hparams = array();
		$toolbar = array();
		if ($JLMS_ACL->CheckPermissions('docs', 'manage') && count($rows)) {
			// this feature is available only for users who has 'documents' manage permission (for entire tool)
			$toolbar[] = array('btn_type' => 'save', 'btn_js' => "javascript:Docs_save_view();", 'btn_title' => _JLMS_DOCS_SAVE_VIEW_TITLE );
		}
		JLMS_TMPL::ShowHeader('docs', _JLMS_DOCS_COURSE_DOCS, $hparams, $toolbar);

		$max_tree_width = 0; if (isset($rows[0])) {$max_tree_width = $rows[0]->tree_max_width;}
		JLMS_TMPL::OpenTS();
?>
			<form action="<?php echo $JLMS_CONFIG->get('live_site') . "/index.php?option=".$option."&amp;Itemid=".$Itemid;?>" method="post" name="adminForm" enctype="multipart/form-data">
<?php 	if (!empty($rows)) { ?>
				<table width="100%" cellpadding="0" cellspacing="0" border="0" id="jlms_doc_non_scr" class="<?php echo JLMSCSS::_('jlmslist');?>">
					<tr>
						<<?php echo JLMSCSS::tableheadertag();?> width="20" class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center">#</<?php echo JLMSCSS::tableheadertag();?>>
					<?php if ($possibilities->manage || $possibilities->publish) { ?>
						<<?php echo JLMSCSS::tableheadertag();?> width="20" class="<?php echo JLMSCSS::_('sectiontableheader');?>"><input type="checkbox" value="0" name="fake_checkbox" style="visibility:hidden" /></<?php echo JLMSCSS::tableheadertag();?>>
					<?php } ?>
					<?php
						for ($th_i = 0; $th_i < ($max_tree_width + 1); $th_i ++) { ?>
						<<?php echo JLMSCSS::tableheadertag();?> width="16" class="<?php echo JLMSCSS::_('sectiontableheader');?>">&nbsp;</<?php echo JLMSCSS::tableheadertag();?>>
						<?php } ?>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" width="45%"><span style="display:block; width:150px; text-align:left;"><?php echo _JLMS_DOCS_TBL_DOCNAME;?></span></<?php echo JLMSCSS::tableheadertag();?>>
					<?php if ($possibilities->order) { ?>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" colspan="2" width="1">
						<?php echo JText::_( _JLMS_REORDER );?>
						</<?php echo JLMSCSS::tableheadertag();?>>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" width="1">
							<?php echo _JLMS_ORDER;?>
						</<?php echo JLMSCSS::tableheadertag();?>>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" width="1">
						<a href="javascript:saveorder(<?php echo count( $rows )-1; ?>, 'doc_saveorder');">
							<img src="<?php echo $lms_img_path?>/toolbar/tlb_filesave.png" border="0" width="16" height="16" alt="<?php echo _JLMS_SAVEORDER;?>" title="<?php echo _JLMS_SAVEORDER;?>" />
						</a>
						</<?php echo JLMSCSS::tableheadertag();?>>
					<?php } ?>
					<?php if ($possibilities->manage || $possibilities->publish) { ?>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" width="1">&nbsp;</<?php echo JLMSCSS::tableheadertag();?>>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>"><?php echo _JLMS_DOCS_TBL_STARTING;?></<?php echo JLMSCSS::tableheadertag();?>>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>"><?php echo _JLMS_DOCS_TBL_ENDING;?></<?php echo JLMSCSS::tableheadertag();?>>
					<?php } ?>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" width="60%"><span style="display:block; width:110px;"><?php echo _JLMS_DOCS_TBL_DESCR;?></span></<?php echo JLMSCSS::tableheadertag();?>>
					</tr>
				<?php
				$k = 1;
				$tree_modes = array();
				$visible_folder = true;
				//$next_row_is_visible = true;
				$vis_mode = 0;
				$doc_number = 1;
				for ($i=0, $n=count($rows); $i < $n; $i++) {
					$row = $rows[$i];
					if ($row->p_view) { // if user can view this item
					$max_tree_width = $row->tree_max_width;
					$link = ''; $link_title = '';
					if ($row->folder_flag ==2) {
						$link = sefRelToAbs("index.php?option=".$option."&amp;Itemid=".$Itemid."&amp;task=docs_view_zip&amp;course_id=".$id."&amp;id=".$row->id);
						$link_title = _JLMS_T_A_VIEW_ZIP_PACK;
					} elseif ((!$row->folder_flag || $row->folder_flag==3)  && $row->file_id) {
						$link = sefRelToAbs("index.php?option=".$option."&amp;Itemid=".$Itemid."&amp;task=get_document&amp;course_id=".$id."&amp;id=".$row->id);
						$link_title = _JLMS_DOCS_LINK_DOWNLOAD;
					} elseif ((!$row->folder_flag || $row->folder_flag==3) && !$row->file_id) {
						$link = sefRelToAbs("index.php?option=".$option."&amp;Itemid=".$Itemid."&amp;task=docs_view_content&amp;course_id=".$id."&amp;id=".$row->id);
						$link_title = _JLMS_T_A_VIEW_CONTENT;
					}
					$time_p = ($row->publish_start || $row->publish_end);
					$alt = ($row->published)?($time_p?_JLMS_STATUS_PUB2:_JLMS_STATUS_PUB):_JLMS_STATUS_UNPUB;
					$image = ($row->published)?($time_p?'btn_publish_wait.png':'btn_accept.png'):'btn_cancel.png';//($time_p?'btn_unpublish_wait.png':'btn_cancel.png');
					$start_date_txt = '';
					$end_date_txt = '';
					$released_info_txt = '';
					if ($row->is_time_related) {
						$released_info_txt = _JLMS_WILL_BE_RELEASED_IN;
						$showperiod = $row->show_period;
						$ost1 = $showperiod%(24*60);		
						$sp_days = ($showperiod - $ost1)/(24*60);
						$ost2 = $showperiod%60;						
						$sp_hours = ($ost1 - $ost2)/60;
						$sp_mins = $ost2;
						$released_info_time = false;
						if ($sp_days) {
							$released_info_txt .= ' '.$sp_days.' '._JLMS_RELEASED_IN_DAYS;
							$released_info_time = true;
						}
						if ($sp_hours) {
							$released_info_txt .= ' '.$sp_hours.' '._JLMS_RELEASED_IN_HOURS;
							$released_info_time = true;
						}
						if ($sp_mins) {
							$released_info_txt .= ' '.$sp_mins.' '._JLMS_RELEASED_IN_MINUTES;
							$released_info_time = true;
						}
						if ($released_info_time) {
							$released_info_txt .= ' '._JLMS_RELEASED_AFTER_ENROLLMENT;
						}
					}
					if ($time_p) {
						$is_expired = false;
						if ($row->publish_end) {
							$end_date_txt = _JLMS_COURSES_END_DATE.': '.$row->end_date;
							$e_date = strtotime($row->end_date);
							if ($e_date < time()) {
								$is_expired = true;
							}
						}
						if ($row->publish_start) {
							$start_date_txt = _JLMS_COURSES_ST_DATE.': '.$row->start_date;
						}
						if ($is_expired) {
							$alt = _JLMS_STATUS_EXPIRED;
							$image = 'btn_expired.png';
						} elseif ($row->publish_start && (!$is_expired)) {
							$s_date = strtotime($row->start_date);
							if ($s_date > time()) {
								$alt = _JLMS_STATUS_UPCOMING;
								$image = 'btn_expired.png';
							}
						}
					}
					$state = ($row->published)?0:1;
					$checked = mosHTML::idBox( $i, $row->id);



					$manage_item = false;
					$publish_item = false;
					if ($row->p_manage) {//JLMS_ACL->CheckPermissions('docs', 'manage')) {
						$manage_item = true;
					}
					if ($row->p_publish) {//$JLMS_ACL->CheckPermissions('docs', 'publish')) {
						$publish_item = true;
					}
					if ($JLMS_ACL->CheckPermissions('docs', 'only_own_items') && $row->owner_id != $user->get('id')) {
						$manage_item = false;
						$publish_item = false;
					} elseif ($JLMS_ACL->CheckPermissions('docs', 'only_own_role') && $JLMS_ACL->GetRole() != $JLMS_ACL->UserSystemRole($db, $row->owner_id)) {
						$manage_item = false;
						$publish_item = false;
					}
					if (!$publish_item && !$manage_item) {
						$checked = '&nbsp;';
					}

	
					// Collapsed/Expanded view
					$tree_row_style = '';
					$visible_folder = true;//$next_row_is_visible;
					//$next_row_is_visible = true;
					if ($vis_mode) {
						if ($row->tree_mode_num < $vis_mode) {
							$vis_mode = 0;
						}
					}
					if (in_array($row->id, $rows_c)) {
						//$next_row_is_visible = false;
						if ($vis_mode) {
							if ($row->tree_mode_num < $vis_mode) {
								$vis_mode = $row->tree_mode_num;
							} else {
								$visible_folder = false;
							}
						} else {
							$vis_mode = $row->tree_mode_num+1;
						}
					} elseif($vis_mode) {
						if ($row->tree_mode_num >= $vis_mode) {
							$visible_folder = false;
						} else {
							$vis_mode = 0;
						}
					}
					if (!$visible_folder) {
						$tree_row_style = ' style="visibility:hidden;display:none"';
					}
					?>
					<tr id="tree_row_<?php echo $row->id;?>" class="<?php echo JLMSCSS::_('sectiontableentry'.$k); ?>"<?php echo $tree_row_style;?>>
						<td align="center" valign="middle"><?php echo ( $doc_number ); ?></td>
					<?php if ($possibilities->manage || $possibilities->publish) { ?>
						<td valign="middle"><?php echo ($row->p_manage || $row->p_publish) ? $checked : ('<input type="checkbox" value="0" name="fake_checkbox'.$row->id.'" style="visibility:hidden" />'); ?></td>
					<?php } ?>
						<?php $add_img = '';
						if ($row->tree_mode_num){
							$g = 0;
							$tree_modes[$row->tree_mode_num - 1] = $row->tree_mode;
							while ($g < ($row->tree_mode_num - 1)) {
								$pref = '';
								if (isset($tree_modes[$g]) && ($tree_modes[$g] == 2) ) { $pref = 'empty_'; }
								$add_img .= "<td width='16' valign='middle'><img src=\"".$JLMS_CONFIG->get('live_site')."/components/com_joomla_lms/lms_images/treeview/".$pref."line.png\" width='16' height='16' alt='".$pref."line' /></td>";
								$g ++;
							}
							$add_img .= "<td width='16' valign='middle'><img src=\"".$JLMS_CONFIG->get('live_site')."/components/com_joomla_lms/lms_images/treeview/sub".$row->tree_mode.".png\" width='16' height='16' alt='sub".$row->tree_mode."' /></td>";
							$max_tree_width = $max_tree_width - $g - 1;
						}
						echo $add_img;?>
						<td align="center" valign="middle" width='16'>
						<?php if ($row->folder_flag == 1) {
							$collapse_img = 'collapseall.png';
							$collapse_alt = _JLMS_DOCS_COLL_FOLDER;
							if (in_array($row->id, $rows_c)) {
								$collapse_img = 'expandall.png';
								$collapse_alt = _JLMS_DOCS_EXP_FOLDER;
							}
							echo "<a class='jlms_img_link' id='tree_div_".$row->id."' style='alignment:center; width:16px; font-weight:bold; cursor:pointer; vertical-align:middle;' onclick='Ex_Folder(".$row->id.",".$row->id.",true)' href='javascript:void(0);'><img class='JLMS_png' id='tree_img_".$row->id."' src=\"".$JLMS_CONFIG->get('live_site')."/components/com_joomla_lms/lms_images/docs/$collapse_img\" width='13' height='13' border='0' alt='".$collapse_alt."' title='".$collapse_alt."' /></a>";
						} else {
							echo "<span style='alignment:center; width:16px; font-weight:bold; vertical-align:middle;'><img class='JLMS_png' src=\"".$JLMS_CONFIG->get('live_site')."/components/com_joomla_lms/lms_images/files/".$row->file_icon.".png\" width='16' height='16' alt='$row->file_icon' /></span>";
						}?>
						</td>
						<td align="left" valign="middle" <?php if ($max_tree_width > 0) { echo "colspan='".($max_tree_width + 1)."'";} ?> width="<?php echo ($row->tree_mode_num == $row->tree_max_width) ? '85' : '45';?>%">
						<span style='font-weight:bold; vertical-align:middle;'>
						<?php if ($row->folder_flag == 1) {
							echo '&nbsp;<strong>'.$row->doc_name.'</strong>';
						} else { if(!isset($row->is_link)){?>
							<a href="<?php echo $link;?>" title="<?php echo str_replace('"','&quot;',$row->doc_name);?>">
								&nbsp;<?php echo $row->doc_name;?>
							</a>
						<?php }else {echo $row->doc_name;}
						} ?>
						</span>
						<?php if($JLMS_CONFIG->get('show_docs_authors', 0) && $row->author_name){?>
						<br />
						<span class="small"><?php echo _JLMS_HOME_AUTHOR . "&nbsp;" . $row->author_name;?></span>
						<?php } ?>
						</td>
					<?php if ($possibilities->order) { ?>
						<td valign="middle" style="vertical-align:middle "><?php if ($row->allow_up == 1 && $row->p_order) { echo JLMS_orderUpIcon( 1, $row->id, true, 'doc_orderup'); } else { echo '&nbsp;';}?></td>
						<td valign="middle" style="vertical-align:middle "><?php if ($row->allow_down == 1 && $row->p_order) { echo JLMS_orderDownIcon( 1, 3, $row->id, true, 'doc_orderdown'); } else { echo '&nbsp;';}?></td>
						<td valign="middle" align="center" style="vertical-align:middle; text-align: center; " colspan="2">
							<input type="text" name="order[]" size="5" value="<?php echo $row->ordering; ?>" class="inputbox" style="text-align: center;" />
						</td>
					<?php } ?>
					<?php if ($possibilities->manage || $possibilities->publish) { ?>
						<td valign="middle">
						<?php if ($publish_item) {
							$title = $alt;
							$content = '';
							if ($start_date_txt) {
								$content .= $start_date_txt.'<br />';
							}
							if ($end_date_txt) {
								$content .= $end_date_txt.'<br />';
							}
							if ($released_info_txt) {
								$content .= $released_info_txt.'<br />';
							}
							if ($row->is_time_related) {
								if ($image == 'btn_accept.png') {
									$image = 'btn_publish_wait.png';
								}
							}
							$name = '<img class="JLMS_png" src="'.$JLMS_CONFIG->get('live_site').'/components/com_joomla_lms/lms_images/toolbar/'.$image.'" width="16" height="16" border="0" alt="'.$alt.'" />';
							$link = 'javascript:submitbutton_change2(\'change_doc\','.$state.','.$row->id.')';
							echo JLMS_toolTip($title, $content, $name, $link);
						} else {
							echo '<img class="JLMS_png" src="'.$JLMS_CONFIG->get('live_site').'/components/com_joomla_lms/lms_images/toolbar/'.$image.'" width="16" height="16" border="0" alt="'.$alt.'" />';
						} ?>
						</td>
						<td align="center" nowrap='nowrap' valign="middle"><?php echo ($row->publish_start?JLMS_dateToDisplay($row->start_date):'-');?></td>
						<td align="center" nowrap='nowrap' valign="middle"><?php echo ($row->publish_end?JLMS_dateToDisplay($row->end_date):'-');?></td>
					<?php } ?>
						<td><?php
							$doc_descr = strip_tags($row->doc_description);
							$doc_descr = trim($doc_descr);
							if (!$row->folder_flag && !$row->file_id) {
								if (strlen($doc_descr) > 75) {
									$doc_descr = substr($doc_descr, 0, 75)."...";
								}
							}
							echo $doc_descr?$doc_descr:'&nbsp;'; ?>
						</td>
					</tr>
					<?php
					$k = 3 - $k;
					$doc_number ++;
					} // end "if ($row->p_view)"
				} ?>
				</table>
<?php
		} else {
			echo '<div class="joomlalms_user_message">'._JLMS_NO_ITEMS_HERE.'</div>';
		}
?>
				<input type="hidden" name="option" value="<?php echo $option;?>" />
				<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
				<input type="hidden" name="task" value="" />
				<input type="hidden" name="boxchecked" value="0" />
				<input type="hidden" name="id" value="<?php echo $id;?>" />
				<input type="hidden" name="row_id" value="0" />
				<input type="hidden" name="state" value="0" />
				<input type="hidden" name="cid2" value="0" />
			</form>
			<noscript>
			<style type="text/css">
			 #jlms_doc_non_scr tr.sectiontableentry1, #jlms_doc_non_scr tr.sectiontableentry2 {
			 display: table-row !important;
			 visibility:visible !important;
			 }
			</style>
			<!--[if IE]>
			<style type="text/css">
			 #jlms_doc_non_scr tr.sectiontableentry1, #jlms_doc_non_scr tr.sectiontableentry2 {
				 display: block !important;
				 visibility:visible !important;
			 }
			</style>
			<![endif]-->
			</noscript>
<?php

		JLMS_TMPL::CloseTS();

		$controls = array();
		if ($possibilities->publish) {
			$controls[] = array('href' => "javascript:submitbutton_change('change_doc',1);", 'title' => _JLMS_SET_PUB, 'img' => 'publish');
			$controls[] = array('href' => "javascript:submitbutton_change('change_doc',0);", 'title' => _JLMS_SET_UNPUB, 'img' => 'unpublish');
			if ($possibilities->manage) {
				$controls[] = array('href' => 'spacer');
			}
		}
		if ($possibilities->create) {
			$link_foldernew = ampReplace(sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid&amp;task=new_folder&amp;id=$id"));
			$link_filenew = ampReplace(sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid&amp;task=new_document&amp;id=$id"));
			$controls[] = array('href' => $link_foldernew, 'title' => _JLMS_DOCS_LNK_NEW_FOLDER, 'img' => 'foldernew');
			$controls[] = array('href' => $link_filenew, 'title' => _JLMS_DOCS_LNK_NEW_DOC, 'img' => 'filenew');
		}
		if ($possibilities->manage) {
			$controls[] = array('href' => "javascript:submitbutton('doc_delete');", 'title' => _JLMS_DELETE, 'img' => 'delete');
			$controls[] = array('href' => "javascript:submitbutton('edit_doc');", 'title' => _JLMS_EDIT, 'img' => 'edit');
		}
		if ($JLMS_ACL->CheckPermissions('docs', 'manage')) {
			$controls[] = array('href' => "javascript:submitbutton('add_doclink');", 'title' => _JLMS_DOCS_ADD_FROM_LIBRARY, 'img' => 'add_library');
		}
		if (count($controls)) {
			JLMS_TMPL::ShowControlsFooter($controls);
		}

		JLMS_TMPL::CloseMT();
	}

	function NewDocLink($doc_details, $lists, $id, $option, $cur_id){
		$JLMS_CONFIG = & JLMSFactory::getConfig();
		$Itemid = $JLMS_CONFIG->get('Itemid');

		$rows_c = $lists['collapsed_folders'];	
		$rows = $lists['out_files'];
		
		$is_dis_start = !($doc_details->publish_end == 1);
		$is_dis_end = !($doc_details->publish_end == 1);
	?>	
	<script language="javascript" type="text/javascript">
	<!--//--><![CDATA[//><!--
	window.addEvent('domready', function() {
			document.adminForm.startday.disabled = true;
			document.adminForm.startmonth.disabled = true;
			document.adminForm.startyear.disabled = true;
				
			document.adminForm.endday.disabled = true;
			document.adminForm.endmonth.disabled = true;
			document.adminForm.endyear.disabled = true;		
	}
	);
	function submitbutton(pressbutton) {
		var form=document.adminForm;
		if (is_start_c == 1) {if (form.start_date.value == ''){jlms_getDate('start');}}
		if (is_end_c == 1) {if (form.end_date.value == ''){jlms_getDate('end');}}
	
		form.task.value = pressbutton;form.submit();

	}
	
var TreeArray1 = new Array();
var TreeArray2 = new Array();
var Is_ex_Array = new Array();
<?php
$i = 1;
foreach ($rows as $row) {
	echo "TreeArray1[".$i."] = ".$row->parent_id.";";
	echo "TreeArray2[".$i."] = ".$row->id.";";
	if (in_array($row->id, $rows_c)) {
		echo "Is_ex_Array[".$i."] = 0;";
	} else {
		echo "Is_ex_Array[".$i."] = 1;";
	}
	$i ++;
}
?>
function Hide_Folder(fid) {
	var vis_style = 'hidden';
	var dis_style = 'none';
	var i = 1;
	while (i < TreeArray1.length) {
		if (TreeArray1[i] == fid) {
			getObj('tree_row_'+TreeArray2[i]).style.visibility = vis_style;
			getObj('tree_row_'+TreeArray2[i]).style.display = dis_style;
			Hide_Folder(TreeArray2[i])
		}
		i ++;
	}
}
function Show_Folder(fid) {
	var vis_style = 'visible';
	var dis_style = '';
	var i = 1;
	while (i < TreeArray1.length) {
		if (TreeArray1[i] == fid) {
			if (getObj('tree_row_'+TreeArray2[i])) {
				getObj('tree_row_'+TreeArray2[i]).style.display = dis_style;
				getObj('tree_row_'+TreeArray2[i]).style.visibility = vis_style;
			}
			NoChange_Folder(TreeArray2[i])
		}
		i ++;
	}
}
function NoChange_Folder(fid) {
	var vis_style = 'hidden';var dis_style = 'none';var i = 1;var j = 0;
	while (i < TreeArray2.length) {
		if ( (TreeArray2[i] == fid) && (Is_ex_Array[i] == 1) ) {
			vis_style = 'visible';
			dis_style = '';
			j = 1;
		}
		i ++;
	}
	i = 1;
	while (i < TreeArray1.length) {
		if (TreeArray1[i] == fid) {
			getObj('tree_row_'+TreeArray2[i]).style.visibility = vis_style;
			getObj('tree_row_'+TreeArray2[i]).style.display = dis_style;
			if (j == 1) { NoChange_Folder(TreeArray2[i]);
			} else { Hide_Folder(TreeArray2[i]); }
		}
		i ++;
	}
}
function Ex_Folder(fid) {
	var i = 1;
	var j = 1;
	while (i < TreeArray2.length) {
		if ( (TreeArray2[i] == fid) && (Is_ex_Array[i] == 1) ) { j = 0; }
		i ++;
	}
	if (j == 1) {
		Show_Folder(fid);
		if (getObj('tree_img_' + fid).runtimeStyle) {
			var StStr = getObj('tree_img_' + fid).runtimeStyle.filter;
			var imgStr = getObj('tree_img_' + fid).outerHTML;
			imgStr = imgStr.replace('expandall.png','collapseall.png').replace('<?php echo _JLMS_DOCS_EXP_FOLDER;?>', '<?php echo _JLMS_DOCS_COLL_FOLDER;?>');
			StStr = StStr.replace('expandall.png','collapseall.png');
			getObj('tree_img_' + fid).outerHTML = imgStr;
			getObj('tree_img_' + fid).runtimeStyle.filter = StStr;
		} else {
			getObj('tree_img_' + fid).src = 'components/com_joomla_lms/lms_images/docs/collapseall.png';
			getObj('tree_img_' + fid).alt = '<?php echo _JLMS_DOCS_COLLAPSE;?>';
			getObj('tree_img_' + fid).title = '<?php echo _JLMS_DOCS_COLL_FOLDER;?>';
		}
	} else {
		Hide_Folder(fid);
		if (getObj('tree_img_' + fid).runtimeStyle) {
			var StStr = getObj('tree_img_' + fid).runtimeStyle.filter;
			var imgStr = getObj('tree_img_' + fid).outerHTML;
			imgStr = imgStr.replace('collapseall.png','expandall.png').replace('<?php echo _JLMS_DOCS_COLL_FOLDER;?>', '<?php echo _JLMS_DOCS_EXP_FOLDER;?>');
			StStr = StStr.replace('collapseall.png','expandall.png');
			getObj('tree_img_' + fid).outerHTML = imgStr;
			getObj('tree_img_' + fid).runtimeStyle.filter = StStr;
		} else {
			getObj('tree_img_' + fid).src = 'components/com_joomla_lms/lms_images/docs/expandall.png';
			getObj('tree_img_' + fid).alt = '<?php echo _JLMS_DOCS_EXPAND;?>';
			getObj('tree_img_' + fid).title = '<?php echo _JLMS_DOCS_EXP_FOLDER;?>';
		}
	}
	i = 1;
	while (i < TreeArray2.length) {
		if ( (TreeArray2[i] == fid) ) {
			if (Is_ex_Array[i] == 1) { Is_ex_Array[i] = 0;
			} else { Is_ex_Array[i] = 1; }
		}
		i ++;
	}
}	
	
	
	var is_start_c = <?php echo ($doc_details->publish_start)?'1':'0'; ?>; var is_end_c = <?php echo ($doc_details->publish_end)?'1':'0'; ?>;
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
//--><!]]>
</script>
	<?php

		JLMS_TMPL::OpenMT();

		$hparams = array();
		$toolbar = array();
		$title = '';

			$title = $doc_details->id ? _JLMS_DOCS_TITLE_EDIT_DOC : _JLMS_DOCS_TITLE_NEW_DOC;

		$toolbar[] = array('btn_type' => 'save', 'btn_js' => "javascript:submitbutton('save_doclink');");
		$toolbar[] = array('btn_type' => 'cancel', 'btn_js' => "javascript:submitbutton('cancel_doc');");
		if (!empty($rows)) { 
		JLMS_TMPL::ShowHeader('doc', $title, $hparams, $toolbar);
		}
		JLMS_TMPL::OpenTS(); 
?>
		<form action="<?php echo sefRelToAbs("index.php?option=".$option."&amp;Itemid=".$Itemid);?>" method="post" name="adminForm" enctype="multipart/form-data">
			<?php 	

			$max_tree_width = 0; if (isset($rows[0])) {$max_tree_width = $rows[0]->tree_max_width;}
			if (!empty($rows)) { ?>
				<table width="100%" cellpadding="0" cellspacing="0" border="0" class="<?php echo JLMSCSS::_('jlmslist');?>">
					<tr>
						<<?php echo JLMSCSS::tableheadertag();?> width="20" class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center">&nbsp;#&nbsp;</<?php echo JLMSCSS::tableheadertag();?>>
						<<?php echo JLMSCSS::tableheadertag();?> width="16" class="<?php echo JLMSCSS::_('sectiontableheader');?>"><input type="checkbox" value="0" name="hidden_box" style="visibility:hidden" /></<?php echo JLMSCSS::tableheadertag();?>>
						<?php for ($th_i = 0; $th_i < ($max_tree_width + 1); $th_i ++) { ?>
						<<?php echo JLMSCSS::tableheadertag();?> width="1%" class="<?php echo JLMSCSS::_('sectiontableheader');?>"><span style="display: block; width: 16px;">&nbsp;</span></<?php echo JLMSCSS::tableheadertag();?>>
						<?php } ?>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" width="45%"><span style="display: block; width: 200px; text-align: left;"><?php echo _JLMS_DOCS_TBL_DOCNAME;?></span></<?php echo JLMSCSS::tableheadertag();?>>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" width="60%"><?php echo _JLMS_DOCS_TBL_DESCR;?></<?php echo JLMSCSS::tableheadertag();?>>
					</tr>
				<?php
				$k = 1;
				$tree_modes = array();
				$visible_folder = true;
				//$next_row_is_visible = true;
				$vis_mode = 0;
				for ($i=0, $n=count($rows); $i < $n; $i++) {
					$row = $rows[$i];
					$max_tree_width = $row->tree_max_width;
					$link = ''; $link_title = '';
					if ($row->folder_flag ==2) {
						$link = sefRelToAbs("index.php?option=".$option."&amp;Itemid=".$Itemid."&amp;task=docs_view_zip&amp;course_id=".$id."&amp;id=".$row->id);
						$link_title = _JLMS_T_A_VIEW_ZIP_PACK;
					} elseif ((!$row->folder_flag || $row->folder_flag==3)  && $row->file_id) {
						$link = sefRelToAbs("index.php?option=".$option."&amp;Itemid=".$Itemid."&amp;task=get_document&amp;course_id=".$id."&amp;id=".$row->id);
						$link_title = _JLMS_DOCS_LINK_DOWNLOAD;
					} elseif ((!$row->folder_flag || $row->folder_flag==3) && !$row->file_id) {
						$link = sefRelToAbs("index.php?option=".$option."&amp;Itemid=".$Itemid."&amp;task=docs_view_content&amp;course_id=".$id."&amp;id=".$row->id);
						$link_title = _JLMS_T_A_VIEW_CONTENT;
					}
					
					// Collapsed/Expanded view
					$tree_row_style = '';
					$visible_folder = true;//$next_row_is_visible;
					//$next_row_is_visible = true;
					if ($vis_mode) {
						if ($row->tree_mode_num < $vis_mode) {
							$vis_mode = 0;
						}
					}
					
					$checked = mosHTML::idBox( $i, $row->id);
					// Collapsed/Expanded view
					$tree_row_style = '';
					$visible_folder = true;//$next_row_is_visible;
					//$next_row_is_visible = true;
					if ($vis_mode) {
						if ($row->tree_mode_num < $vis_mode) {
							$vis_mode = 0;
						}
					}
					if (in_array($row->id, $rows_c)) {
						//$next_row_is_visible = false;
						if ($vis_mode) {
							if ($row->tree_mode_num < $vis_mode) {
								$vis_mode = $row->tree_mode_num;
							} else {
								$visible_folder = false;
							}
						} else {
							$vis_mode = $row->tree_mode_num+1;
						}
					} elseif($vis_mode) {
						if ($row->tree_mode_num >= $vis_mode) {
							$visible_folder = false;
						} else {
							$vis_mode = 0;
						}
					}
					if (!$visible_folder) {
						$tree_row_style = ' style="visibility:hidden;display:none"';
					}					
					?>
					
					<tr id="tree_row_<?php echo $row->id;?>" class="<?php echo JLMSCSS::_('sectiontableentry'.$k); ?>"<?php echo $tree_row_style;?>>					
						<td align="center" valign="middle"><?php echo ( $i + 1 ); ?></td>
						<td valign="middle"><?php if ($row->folder_flag != 1 ) {echo $checked;} ?></td>

						<?php $add_img = '';
						if ($row->tree_mode_num) {
							$g = 0;
							$tree_modes[$row->tree_mode_num - 1] = $row->tree_mode;
							while ($g < ($row->tree_mode_num - 1)) {
								$pref = '';
								if (isset($tree_modes[$g]) && ($tree_modes[$g] == 2) ) { $pref = 'empty_'; }
								$add_img .= "<td width='16' valign='middle'><img src=\"".$JLMS_CONFIG->get('live_site')."/components/com_joomla_lms/lms_images/treeview/".$pref."line.png\" width='16' height='16' alt='".$pref."line' /></td>";
								$g ++;
							}
							$add_img .= "<td width='16' valign='middle'><img src=\"".$JLMS_CONFIG->get('live_site')."/components/com_joomla_lms/lms_images/treeview/sub".$row->tree_mode.".png\" width='16' height='16' alt='sub".$row->tree_mode."' /></td>";
							$max_tree_width = $max_tree_width - $g - 1;
						}
						echo $add_img;?>

						<td align="center" valign="middle" width='16'>

						<?php if ($row->folder_flag == 1) {
							$collapse_img = 'collapseall.png';
							$collapse_alt = _JLMS_DOCS_COLL_FOLDER;
							if (in_array($row->id, $rows_c)) {
								$collapse_img = 'expandall.png';
								$collapse_alt = _JLMS_DOCS_EXP_FOLDER;
							}
							echo "<span id='tree_div_".$row->id."' style='alignment:center; width:16px; font-weight:bold; cursor:pointer; vertical-align:middle;' onclick='Ex_Folder(".$row->id.",".$row->id.",true)'><img class='JLMS_png' id='tree_img_".$row->id."' src=\"".$JLMS_CONFIG->get('live_site')."/components/com_joomla_lms/lms_images/docs/$collapse_img\" width='13' height='13' alt='".$collapse_alt."' title='".$collapse_alt."' /></span>";
						} else {
							echo "<span style='alignment:center; width:16px; font-weight:bold; vertical-align:middle;'><img class='JLMS_png' src=\"".$JLMS_CONFIG->get('live_site')."/components/com_joomla_lms/lms_images/files/".$row->file_icon.".png\" width='16' height='16' alt='$row->file_icon' /></span>";
						}?>						
						</td>

						<td align="left" valign="middle" <?php if ($max_tree_width > 0) { echo "colspan='".($max_tree_width + 1)."'";} ?> width="45%">
						<span style='font-weight:bold; vertical-align:middle;'>
						<?php if ($row->folder_flag == 1) {
							echo '&nbsp;<strong>'.$row->doc_name.'</strong>';
						} else { ?>
							<a href="<?php echo $link;?>" title="<?php echo $link_title;?>">
								&nbsp;<?php echo $row->doc_name;?>
							</a>
						<?php } ?>
						</span>
						</td>

						<td><?php
							$doc_descr = strip_tags($row->doc_description);
							if (!$row->folder_flag && !$row->file_id) {
								if (strlen($doc_descr) > 75) {
									$doc_descr = substr($doc_descr, 0, 75)."...";
								}
							}
							echo $doc_descr?$doc_descr:'&nbsp;'; ?>
						</td>
					</tr>
					<?php
					$k = 3 - $k;
				} ?>
				</table>

			<table width="100%" cellpadding="0" cellspacing="0" border="0" class="jlms_item_properties">
				<tr>
					<td valign="middle" style="vertical-align:middle"><br /><?php echo _JLMS_PLACE_IN;?></td>
					<td><br /><?php echo $lists['course_folders'];?></td>
				</tr>	
				<tr>
					<td valign="middle" style="vertical-align:middle"><br /><?php echo _JLMS_PUBLISHING;?></td>
					<td><br /><?php echo $lists['publishing'];?></td>
				</tr>
				<tr>
					<td valign="middle"><br /><?php echo _JLMS_START_DATE;?></td>
					<td valign="middle" style="vertical-align:middle "><br />
						<table cellpadding="0" cellspacing="0" border="0" class="jlms_date_outer"><tr><td valign="middle"><input type="checkbox" value="1" name="is_start" onclick="jlms_Change_start()" <?php echo $doc_details->publish_start?'checked':'';?> /></td>
						<td valign="middle" style="vertical-align:middle ">
						<?php
						$s_date = ($is_dis_start)?date('Y-m-d'):$doc_details->start_date;
						echo JLMS_HTML::_('calendar.calendar',$s_date,'start','start');
						?>
						</td></tr></table>
					</td>
				</tr>	
				<tr>
					<td><br /><?php echo _JLMS_END_DATE;?></td>
					<td valign="middle" style="vertical-align:middle "><br />
						<table cellpadding="0" cellspacing="0" border="0" class="jlms_date_outer"><tr><td valign="middle"><input type="checkbox" value="1" name="is_end" onclick="jlms_Change_end()" <?php echo $doc_details->publish_end?'checked="checked"':'';?> /></td>
						<td valign="middle" style="vertical-align:middle ">
						<?php
						$e_date = ($is_dis_end)?date('Y-m-d'):$doc_details->end_date;
						echo JLMS_HTML::_('calendar.calendar',$e_date,'end','end');
						?>
						</td></tr></table>
					</td>
				</tr>	
			</table>
			<?php } else { echo '<div class="joomlalms_user_message">'._JLMS_NO_ITEMS_HERE.'</div>';} ?>
			<input type="hidden" name="option" value="<?php echo $option;?>" />
			<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
			<input type="hidden" name="task" value="update_document" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="course_id" value="<?php echo $id;?>" />
			<input type="hidden" name="folder_flag" value="0" />
			<input type="hidden" name="id" value="<?php echo $doc_details->id;?>" />
		</form>
<?php 
		JLMS_TMPL::CloseTS();
		JLMS_TMPL::CloseMT();		
	}
}
?>