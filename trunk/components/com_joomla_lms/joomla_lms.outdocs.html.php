<?php
/**
* joomla_lms.outdocs.html.php
* (c) JoomaLMS eLearning Software http://www.joomlalms.com/
**/

// no direct access
defined( '_JLMS_EXEC' ) or die( 'Restricted access' );

class JLMS_outdocs_html {
	function show_ZipPack( $option, &$row_zip, &$lists, $doc_type = 'zip' ) {
		global $Itemid, $JLMS_CONFIG;

		JLMS_TMPL::OpenMT();

		$hparams = array(
		'show_menu' => true,
		'simple_menu' => true,
		);
		if ($doc_type == 'document_contents') {
			$toolbar[] = array('btn_type' => 'archive', 'btn_str' => _JLMS_DOWNLOAD,  'btn_js' => sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid&amp;task=get_outdoc&amp;id=$row_zip->doc_id&amp;force=force") );
		}
		/*$back_link = sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid&amp;task=documents&amp;id=$course_id");
		if (isset($lists['lpath_id']) && $lists['lpath_id']) {
			$back_link = sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid&amp;task=compose_lpath&amp;course_id=$course_id&amp;id=".$lists['lpath_id']);
		}*/

		//01.12.2007 - (DEN) - Compatibility for returning from the document view to the doc.tool/course homepage/lpaths list.
		$back_link = JLMSRoute::_("index.php?option=$option&amp;Itemid=$Itemid&task=outdocs".($row_zip->parent_id ? "&folder=$row_zip->parent_id" : '')."&element=$row_zip->id");
		$toolbar[] = array('btn_type' => 'back', 'btn_js' => $back_link );
		JLMS_TMPL::ShowHeader('outdoc', $row_zip->doc_name, $hparams, $toolbar);

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

	function confirm_delLinkedResources( &$del_items, &$old_cid, &$course_names, $option ) {
		global $Itemid, $JLMS_CONFIG; ?>
<script language="javascript" type="text/javascript">
<!--
function submitbutton(pressbutton) {
	var form = document.adminForm;
	if (pressbutton == 'outdoc_delete'){
		form.task.value = pressbutton;
		form.submit();
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
		$hparams['sys_msg'] = _JLMS_RESOURCES_ARE_IN_USE;
	
		$toolbar = array();
		$toolbar[] = array('btn_type' => 'yes', 'btn_js' => "javascript:submitbutton('outdoc_delete');");
		$toolbar[] = array('btn_type' => 'cancel', 'btn_js' => "javascript:submitbutton('cancel_outdoc');");
		$hparams['toolbar_position'] = 'center';
		JLMS_TMPL::ShowHeader('outdoc', '', $hparams, $toolbar);

		JLMS_TMPL::OpenTS();
?>
		<form action="<?php echo sefRelToAbs("index.php?option=".$option."&amp;Itemid=".$Itemid);?>" method="post" name="adminForm">
			<table width="100%" cellpadding="0" cellspacing="0" border="0" class="<?php echo JLMSCSS::_('jlmslist');?>">
				<tr>
					<<?php echo JLMSCSS::tableheadertag();?> width="20" class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center">#</<?php echo JLMSCSS::tableheadertag();?>>
					<<?php echo JLMSCSS::tableheadertag();?> width="16" class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center">&nbsp;</<?php echo JLMSCSS::tableheadertag();?>>
					<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>"><?php echo _JLMS_DOCS_TBL_DOCNAME;?></<?php echo JLMSCSS::tableheadertag();?>>
					<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>"><?php echo _JLMS_COURSES_TBL_HEAD_NAME;?></<?php echo JLMSCSS::tableheadertag();?>>
				</tr>
			<?php
			$k = 1;
			$num = 1;
			for ($i=0, $n=count($del_items); $i < $n; $i++) {
				$row = $del_items[$i];
				foreach ($row->course_ids as $row_course_id) {
?>
					<tr class="<?php echo JLMSCSS::_('sectiontableentry'.$k); ?>">
						<td align="center"><?php echo ( $num ); ?></td>
						<td>
						<?php
						echo "<span style='alignment:center; width:16px; font-weight:bold; vertical-align:middle;'><img class='JLMS_png' src=\"".$JLMS_CONFIG->get('live_site')."/components/com_joomla_lms/lms_images/files/".$row->file_icon.".png\" width='16' height='16' alt='$row->file_icon' /></span>";
						?>
						</td>
						<td align="left">
						<?php echo $row->doc_name;?>
						</td>
						<td><?php echo isset($course_names[$row_course_id]->course_name) ? $course_names[$row_course_id]->course_name : '';?></td>
					</tr>
					<?php
					$k = 3 - $k;
					$num++;
				}
			}?>
			</table>
			<input type="hidden" name="option" value="<?php echo $option;?>" />
			<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
			<input type="hidden" name="task" value="outdoc_delete" />
<?php foreach ($old_cid as $old_cid1) { ?>
<input type="hidden" name="cid[]" value="<?php echo $old_cid1;?>" />
<?php } ?>
			<input type="hidden" name="force_delete" value="1" />
		</form>
	<?php
		JLMS_TMPL::CloseTS();
		JLMS_TMPL::CloseMT();
	}

	function showEditDocument( $doc_details, &$lists, $option ) {
		global $Itemid, $JLMS_CONFIG;
		
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
	if ((pressbutton=='save_outdoc') && ((form.userfile0.value=="") && (form.doc_name0.value==""))){alert("<?php echo _JLMS_DOCS_SELECT_FILE_OR_ENTER_NAME;?>");
	<?php } else {?>
	if ((pressbutton=='save_outdoc') && (form.doc_name0.value=="")){alert("<?php echo _JLMS_PL_ENTER_NAME;?>");
	<?php } ?>
	} else {

<?php 	$editor =& JLMS07062010_JFactory::getEditor();
    	echo $editor->save( 'doc_description' ); ?>

		form.task.value = pressbutton;form.submit();
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

function Add_new_form() {
	i = 1;
	while(window.parent.document.getElementById('tr1_' + i)) {
		if( window.parent.document.getElementById('tr1_' + i).style.display == 'none' && window.parent.document.getElementById('tr1_' + i).style.visibility == 'hidden') {
			window.parent.document.getElementById('tr1_' + i).style.display = '';
			window.parent.document.getElementById('tr1_' + i).style.visibility = 'visible';
			
			window.parent.document.getElementById('tr2_' + i).style.display = '';
			window.parent.document.getElementById('tr2_' + i).style.visibility = 'visible';
			
			window.parent.document.getElementById('tr3_' + i).style.display = '';
			window.parent.document.getElementById('tr3_' + i).style.visibility = 'visible';
			
			break;
		}
		i++;
	}	
}

//--><!]]>
</script>
<?php
		JLMS_TMPL::OpenMT();

		$hparams = array('show_menu' => true,
		'simple_menu' => true,);
		$toolbar = array();
		$title = '';
		if ($doc_details->folder_flag == 1) {
			$title = $doc_details->id ? _JLMS_OUTDOCS_TITLE_EDIT_FOLDER : _JLMS_OUTDOCS_TITLE_NEW_FOLDER;
		} else {
			$title = $doc_details->id ? _JLMS_OUTDOCS_TITLE_EDIT_DOC : _JLMS_OUTDOCS_TITLE_NEW_DOC;
		}
		$toolbar[] = array('btn_type' => 'save', 'btn_js' => "javascript:submitbutton('save_outdoc');");
		$toolbar[] = array('btn_type' => 'cancel', 'btn_js' => "javascript:submitbutton('cancel_outdoc');");
		JLMS_TMPL::ShowHeader('outdoc', $title, $hparams, $toolbar);

		JLMS_TMPL::OpenTS();		
?>
		<form action="<?php echo sefRelToAbs("index.php?option=".$option."&amp;Itemid=".$Itemid);?>" method="post" name="adminForm" enctype="multipart/form-data" onsubmit="setgood();">
			<table width="100%" cellpadding="0" cellspacing="0" border="0" class="jlms_properties_table">
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
			<?php } ?>
			<?php if (!($doc_details->folder_flag == 1) && !$doc_details->id) {?>
			<?php	for($i=1;$i<10;$i++) {?>
						<tr style="visibility:hidden; display:none;" id="tr1_<?php echo $i;?>"><td colspan="2" height="10"></td></tr>
						<tr style="visibility:hidden; display:none;" id="tr2_<?php echo $i;?>">
							<td width="150" valign="middle" style="vertical-align:middle"><?php echo _JLMS_ENTER_NAME;?></td>
							<td><input class="inputbox" size="40" type="text" name="doc_name<?php echo $i;?>" value="<?php echo str_replace('"','&quot;',$doc_details->doc_name);?>" />
							</td>
						</tr>
						<tr style="visibility:hidden; display:none;" id="tr3_<?php echo $i;?>">
							<td valign="middle" style="vertical-align:middle"><br /><?php echo _JLMS_CHOOSE_FILE;?></td>
							<td>
								<br /><input size="40" class="inputbox" type="file" name="userfile<?php echo $i;?>" />
							</td>
						</tr>
				<?php }?>
			<tr><td colspan="2" height="10"></td></tr>
				<tr>
					<td width="15%" valign="middle" style="vertical-align:middle" colspan="2">
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
					<td valign="middle" style="vertical-align:middle"><br /><?php echo _JLMS_PLACE_IN;?></td>
					<td><br /><?php echo $lists['course_folders'];?></td>
				</tr>
				<tr>
					<td valign="middle" style="vertical-align:middle"><br /><?php echo _JLMS_ORDERING;?></td>
					<td><br /><input class="inputbox" size="40" type="text" name="doc_order" maxlength="5" value="<?php echo $doc_details->ordering;?>" /></td>
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
					<td valign="middle"><br /><?php echo _JLMS_OUTDOCS_VISFOR?></td>
					<td><br />
					<?php
						$chk1 = '';
						$chk2 = '';
						$chk3 = '';
						if($doc_details->outdoc_share == 0) $chk1 = ' checked="checked"';
						if($doc_details->outdoc_share == 1) $chk2 = ' checked="checked"';
						if($doc_details->outdoc_share == 2) $chk3 = ' checked="checked"'; ?>
						<input type="radio" name="outdoc_share" id="outdoc_share0" value="0"<?php echo $chk1?> /><label for="outdoc_share0"><?php echo _JLMS_OUTDOCS_PRIVATE?></label>
						<input type="radio" name="outdoc_share" id="outdoc_share1" value="1"<?php echo $chk2?> /><label for="outdoc_share1"><?php echo _JLMS_OUTDOCS_TEACHERS?></label>
						<input type="radio" name="outdoc_share" id="outdoc_share2" value="2"<?php echo $chk3?> /><label for="outdoc_share2"><?php echo _JLMS_OUTDOCS_ALL;?></label>
					</td>
				</tr>
				<tr>
					<td><br /><?php echo _JLMS_OUTDOCS_SHOWCOURSES;?></td>
					<td><br /><?php echo $lists['share_to_courses'];?></td>
				</tr>
				<tr>
					<td colspan="2" valign="top" align="left" style="text-align:left "><br /><?php echo _JLMS_DESCRIPTION;?></td>
				</tr>
				<tr>
					<td colspan="2">
					<?php
						JLMS_editorArea( 'editor1', $doc_details->doc_description, 'doc_description', '100%', '250', '40', '20' ) ; ?>
					</td>
				</tr>
			</table>
			<input type="hidden" name="option" value="<?php echo $option;?>" />
			<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
			<input type="hidden" name="task" value="update_document" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="folder_flag" value="<?php echo $doc_details->folder_flag;?>" />
			<input type="hidden" name="id" value="<?php echo $doc_details->id;?>" />
		</form>
<?php
		JLMS_TMPL::CloseTS();
		JLMS_TMPL::CloseMT();
	}

	//to do: sdelat' preloadimages() - ok
	//to do: vstavit' proverku na nalichie otmechennogo polya pri 'delete' i 'edit' - ok
	// (TIP) v JS pri hide rows stoit display = ''; (dlya CSS2 standarta nugno display = 'table-row' - no iz-za etogo glucki v IE !!)
	function showCourseDocuments( $option, &$rows, &$lists, $is_teacher) {
		global $Itemid, $my, $JLMS_CONFIG;
		
		$JLMS_ACL = & JLMSFactory::getACL();
		$is_teacher = $JLMS_ACL->isTeacher();
		$can_do_everything = $JLMS_ACL->CheckPermissions('library', 'only_own_items') ? false : ( $is_teacher ? true : false);
		$rows_c = $lists['collapsed_folders'];		
		?>		
<script language="javascript" type="text/javascript">
<!--//--><![CDATA[//><!--
function submitbutton(pressbutton) {
	var form = document.adminForm;
	if ((pressbutton == 'outdoc_delete') && (form.boxchecked.value == '0')) {
		alert( "<?php echo _JLMS_ALERT_SELECT_ITEM;?>" );
	} else if((pressbutton == 'outdoc_delete')){
		if(confirm('<?php echo _JLMS_OUTDOCS_JS_CONFIRM_DELETE;?>')){
			form.task.value = pressbutton;
			form.submit();
		}
	} else if ((pressbutton == 'edit_outdoc') && (form.boxchecked.value == '0')) {
		alert( "<?php echo _JLMS_ALERT_SELECT_ITEM;?>" );
	} else {
		form.task.value = pressbutton;
		form.submit();
	}
}
function submitbutton_change(pressbutton, state) {
	var form = document.adminForm;
	if (pressbutton == 'change_outdoc'){
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
	if (pressbutton == 'change_outdoc'){
		form.task.value = pressbutton;
		form.state.value = state;
		form.cid2.value = cid_id;
		form.submit();
	}
}
function submitbutton_order(pressbutton, item_id) {
	var form = document.adminForm;
	if ((pressbutton == 'outdoc_orderup') || (pressbutton == 'outdoc_orderdown')){
		if (item_id) {
		form.task.value = pressbutton;
		form.row_id.value = item_id;
		form.submit();
		}
	}
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
JLMS_preloadImages('<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/docs/expandall.png','<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/docs/collapseall.png'<?php if ($is_teacher) { echo ", '".$JLMS_CONFIG->get('live_site').'/'.$JLMS_CONFIG->get('ajax_settings_small_indicator')."'"; } ?>);
//--><!]]>
</script>
<?php

		JLMS_TMPL::OpenMT();

		$hparams = array(
		'show_menu' => true,
		'simple_menu' => true,
		);
		$toolbar = array();

		JLMS_TMPL::ShowHeader('outdocs', _JLMS_TOOLBAR_LIBRARY, $hparams, $toolbar);

		$max_tree_width = 0; if (isset($rows[0])) {$max_tree_width = $rows[0]->tree_max_width;}
		JLMS_TMPL::OpenTS('', ' align="right" style="text-align:right " width="100%"');
		$is_teacher = $JLMS_ACL->isTeacher(); 
?>
			<form action="<?php echo $JLMS_CONFIG->get('live_site') . "/index.php?option=".$option."&amp;Itemid=".$Itemid;?>" method="post" name="adminForm" enctype="multipart/form-data">
<?php 	if (!empty($rows)) { ?>
				<table width="100%" cellpadding="0" cellspacing="0" border="0" class="<?php echo JLMSCSS::_('jlmslist');?>">
					<tr>
						<<?php echo JLMSCSS::tableheadertag();?> width="20" class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center">#</<?php echo JLMSCSS::tableheadertag();?>>
					<?php //if ($JLMS_ACL->CheckPermissions('docs', 'manage')) { ?>
						<<?php echo JLMSCSS::tableheadertag();?> width="20" class="<?php echo JLMSCSS::_('sectiontableheader');?>"><input type="checkbox" style="visibility:hidden;" /></<?php echo JLMSCSS::tableheadertag();?>>
					<?php //} ?>
						<?php for ($th_i = 0; $th_i < ($max_tree_width + 1); $th_i ++) { ?>
						<<?php echo JLMSCSS::tableheadertag();?> width="1%" class="<?php echo JLMSCSS::_('sectiontableheader');?>"><span style="display: block; width: 16px;">&nbsp;</span></<?php echo JLMSCSS::tableheadertag();?>>
						<?php } ?>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" width="45%"><span style="display: block; width: 150px; text-align: left;"><?php echo _JLMS_DOCS_TBL_DOCNAME;?></span></<?php echo JLMSCSS::tableheadertag();?>>
					<?php //if ($JLMS_ACL->CheckPermissions('docs', 'manage')) { ?>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" colspan="2">&nbsp;</<?php echo JLMSCSS::tableheadertag();?>>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" width="1">&nbsp;</<?php echo JLMSCSS::tableheadertag();?>>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>"><?php echo _JLMS_DOCS_TBL_STARTING;?></<?php echo JLMSCSS::tableheadertag();?>>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>"><?php echo _JLMS_DOCS_TBL_ENDING;?></<?php echo JLMSCSS::tableheadertag();?>>
					<?php //} ?>
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
						$link = sefRelToAbs("index.php?option=".$option."&amp;Itemid=".$Itemid."&amp;task=docs_view_zip&amp;id=".$row->id);
						$link_title = _JLMS_T_A_VIEW_ZIP_PACK;
					} elseif (!$row->folder_flag && $row->file_id) {
						$link = sefRelToAbs("index.php?option=".$option."&amp;Itemid=".$Itemid."&amp;task=get_outdoc&amp;id=".$row->id);
						$link_title = _JLMS_DOCS_LINK_DOWNLOAD;
					} elseif (!$row->folder_flag && !$row->file_id) {
						$link = sefRelToAbs("index.php?option=".$option."&amp;Itemid=".$Itemid."&amp;task=outdocs_view_content&amp;id=".$row->id);
						$link_title = _JLMS_T_A_VIEW_CONTENT;
					} elseif ($row->folder_flag == 3 && $row->file_id) {
						$link = sefRelToAbs("index.php?option=".$option."&amp;Itemid=".$Itemid."&amp;task=playerSCORMFiles&amp;id=".$row->file_id."&amp;doc_id=".$row->id);
						$link_title = _JLMS_T_A_VIEW_CONTENT;
					}
					$time_p = ($row->publish_start || $row->publish_end);
					$alt = ($row->published)?($time_p?_JLMS_STATUS_PUB2:_JLMS_STATUS_PUB):_JLMS_STATUS_UNPUB;
					$image = ($row->published)?($time_p?'btn_publish_wait.png':'btn_accept.png'):'btn_cancel.png';//($time_p?'btn_unpublish_wait.png':'btn_cancel.png');
					if ($time_p) {
						$is_expired = false;
						if ($row->publish_start) {
							$s_date = strtotime($row->start_date);
							if ($s_date > time()) {
								$is_expired = true;
							}
						}
						if ($row->publish_end && (!$is_expired)) {
							$e_date = strtotime($row->end_date);
							if ($e_date < time()) {
								$is_expired = true;
							}
						}
						if ($is_expired) {
							$alt = _JLMS_STATUS_EXPIRED;
							$image = 'btn_expired.png';
						}
					}
					$state = ($row->published)?0:1;
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
					
						<td valign="middle"><?php if ($my->id == $row->owner_id || $can_do_everything ) {echo $checked;} ?></td>
					
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
							<?php if(($is_teacher && $row->outdoc_share) || (!$is_teacher && $row->outdoc_share == 2) || ($my->id == $row->owner_id) || $can_do_everything){
								$add_link_params = '';
								if ($row->folder_flag == 3 && isset($row->scorm_params) && $row->scorm_params) {
									$tmp_params = new JLMSParameters($row->scorm_params);
									if ($tmp_params->get('scorm_layout',0) == 1) {
										$x_size = 0;
										$y_size = 0;
										if (isset($row->scorm_width) && $row->scorm_width > 100) {
											$x_size = $row->scorm_width;
										}
										if (isset($row->scorm_height) && $row->scorm_height > 100) {
											$y_size = $row->scorm_height;
										}
										$add_link_params = ' class="scorm_modal" rel="{handler:\'iframe\', size:{x:'.$x_size.',y:'.$y_size.'}}"';
										JLMS_initialize_SqueezeBox();
									}
								}
?>
<?php if ($link) { ?>
							<a href="<?php echo $link;?>"<?php echo $add_link_params;?> title="<?php echo str_replace('"', '&quot;',$row->doc_name);?>">
<?php } ?>
							<?php } ?>
								&nbsp;<?php echo $row->doc_name;?>
								<?php if(($is_teacher && $row->outdoc_share) || (!$is_teacher && $row->outdoc_share == 2) || ($my->id == $row->owner_id) || $can_do_everything){?>
<?php if ($link) { ?>
							</a>
<?php } ?>
						<?php } }?>
						</span>
						<?php if($JLMS_CONFIG->get('show_library_authors', 0) && $row->author_name){?>
						<br />
						<span class="small"><?php echo _JLMS_HOME_AUTHOR . "&nbsp;" . $row->author_name;?></span>
						<?php } ?>

						</td>
					<?php 
					//if ($JLMS_ACL->CheckPermissions('docs', 'manage')) { ?>
						<td valign="middle" style="vertical-align:middle "><?php if ($row->allow_up == 1 && ($my->id == $row->owner_id || $can_do_everything)) { echo JLMS_orderUpIcon( 1, $row->id, true, 'outdoc_orderup'); } else { echo '&nbsp;';}?></td>
						<td valign="middle" style="vertical-align:middle "><?php if ($row->allow_down == 1 && ($my->id == $row->owner_id || $can_do_everything)) { echo JLMS_orderDownIcon( 1, 3, $row->id, true, 'outdoc_orderdown'); } else { echo '&nbsp;';}?></td>
						<td valign="middle">
							<?php if($my->id == $row->owner_id || $can_do_everything) {
								echo '<a class="jlms_img_link" href="javascript:submitbutton_change2(\'change_outdoc\','.$state.','.$row->id.')" title="'.$alt.'">';
								echo '<img class="JLMS_png" src="'.$JLMS_CONFIG->get('live_site').'/components/com_joomla_lms/lms_images/toolbar/'.$image.'" width="16" height="16" border="0" alt="'.$alt.'" />';
								echo '</a>';
							} else {
								echo '&nbsp;';
							}?>
						</td>
						
						<td align="center" nowrap='nowrap' valign="middle"><?php echo ($row->publish_start?JLMS_dateToDisplay($row->start_date):'-');?></td>
						<td align="center" nowrap='nowrap' valign="middle"><?php echo ($row->publish_end?JLMS_dateToDisplay($row->end_date):'-');?></td>
					<?php  //} ?>
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
<?php
		} else {
			echo '<div class="joomlalms_user_message">'._JLMS_NO_ITEMS_HERE.'</div>';
		}
?>
				<input type="hidden" name="option" value="<?php echo $option;?>" />
				<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
				<input type="hidden" name="task" value="" />
				<input type="hidden" name="boxchecked" value="0" />
				<input type="hidden" name="row_id" value="0" />
				<input type="hidden" name="state" value="0" />
				<input type="hidden" name="cid2" value="0" />
			</form>

<?php

		JLMS_TMPL::CloseTS();

		//if ($JLMS_ACL->CheckPermissions('docs', 'manage')) {
		if($is_teacher){
			$link_foldernew = ampReplace(sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid&amp;task=new_outfolder"));
			$link_filenew = ampReplace(sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid&amp;task=new_outdocs"));
			$link_scormnew = ampReplace(sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid&amp;task=new_scorm"));

			$controls = array();
			$controls[] = array('href' => "javascript:submitbutton_change('change_outdoc',1);", 'title' => _JLMS_SET_PUB, 'img' => 'publish');
			$controls[] = array('href' => "javascript:submitbutton_change('change_outdoc',0);", 'title' => _JLMS_SET_UNPUB, 'img' => 'unpublish');
			$controls[] = array('href' => 'spacer');
			$controls[] = array('href' => $link_foldernew, 'title' => _JLMS_DOCS_LNK_NEW_FOLDER, 'img' => 'foldernew');
			$controls[] = array('href' => $link_filenew, 'title' => _JLMS_DOCS_LNK_NEW_DOC, 'img' => 'filenew');
			$controls[] = array('href' => "javascript:submitbutton('outdoc_delete');", 'title' => _JLMS_DELETE, 'img' => 'delete');
			$controls[] = array('href' => "javascript:submitbutton('edit_outdoc');", 'title' => _JLMS_EDIT, 'img' => 'edit');
			$controls[] = array('href' => $link_scormnew, 'title' => _JLMS_DOCS_NEW_SCORM_PACKAGE, 'img' => 'add_scorm');

			JLMS_TMPL::ShowControlsFooter($controls);
		//}
		}
		JLMS_TMPL::CloseMT();
	}

	function showEditScorm( &$row, &$lists, $option, $params, $lp_params ) {
		global $Itemid, $_MAMBOTS, $JLMS_CONFIG; 
		?>
<script language="javascript" type="text/javascript">
<!--
function setgood() {
	return true;
}
var tmp_sl_var = <?php echo $params->get('scorm_layout',0);?>;
function submitbutton(pressbutton, jform_name) {
	var form = eval("document."+jform_name);//adminForm;
	try {
		form.onsubmit();
	} catch(e) {
		//alert(e);
	}
	if (pressbutton == 'cancel_scorm') {
		form.task.value = 'cancel_scorm';
		form.submit();
	} else {
		if (pressbutton == 'save_scorm') {
				form.task.value = 'save_scorm';
				form.submit();
		}
	}
}
function jlms_change_scorm_stages_view() {
	if (tmp_sl_var == 1) {
		$('scorm_stage_width_section').style.display = '';
	} else {
		$('scorm_stage_width_section').style.display = 'none';
	}
}
function jlms_dis_forms(elem, type) {
	var form = document.adminFormsc;

	if (type == 1 || type == '1') {
		if (elem.checked) {
			scorm_upl_type = 1;
			elem.form.scorm_file.disabled = false;
			document.adminFormsc.scorm_ftp_file.disabled = true;
			
			form.scorm_upl_type.value = 1;
			
		} else {
			scorm_upl_type = 2;
			elem.form.scorm_file.disabled = true;
			document.adminFormsc.scorm_ftp_file.disabled = false;
		}
	}
	if (type == 2 || type == '2') {
		if (elem.checked) {
			scorm_upl_type = 2;
			elem.form.scorm_file.disabled = true;
			document.adminFormsc.scorm_ftp_file.disabled = false;
			
			form.scorm_upl_type.value = 2;
		} else {
			scorm_upl_type = 1;
			elem.form.scorm_file.disabled = false;
			document.adminFormsc.scorm_ftp_file.disabled = true;
		}
	}
}
-->		
</script>
<?php

		JLMS_TMPL::OpenMT();

		$hparams = array('show_menu' => true,
		'simple_menu' => true,);
		$toolbar = array();
		$title = '';

		$title = $row->id ? _JLMS_DOCS_EDIT_SCORM_PACKAGE : _JLMS_DOCS_NEW_SCORM_PACKAGE;

		JLMS_TMPL::ShowHeader('outdoc', $title, $hparams, $toolbar);

		JLMS_TMPL::OpenTS('', ' valign="top"');

			if (!$row->id || ($row->id && $row->folder_flag == 3)) {?>
				<form action="<?php echo sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid");?>" method="post" name="adminFormsc" enctype="multipart/form-data" onsubmit="setgood();">
					<table width="100%" cellpadding="0" cellspacing="0" border="0" class="jlms_properties_table">
					<tr>
						<td align="left" class="contentheading" valign="middle" style="vertical-align:middle ">
							
						</td>
						<td align="right" style="text-align:right ">
						<?php $toolbar = array();
						$toolbar[] = array('btn_type' => 'save', 'btn_js' => "javascript:submitbutton('save_scorm', 'adminFormsc');");
						$toolbar[] = array('btn_type' => 'cancel', 'btn_js' => "javascript:submitbutton('cancel_scorm', 'adminFormsc');");
						echo JLMS_ShowToolbar($toolbar); ?>
						</td>
					</tr>
					</table>
					<table width="100%" cellpadding="0" cellspacing="0" border="0" class="jlms_properties_table">
					<tr>
						<td width="30%"><?php echo _JLMS_ENTER_NAME;?><br /></td>
						<td>
							<input size="40" class="inputbox" type="text" name="doc_name" value="<?php echo $row->doc_name;?>" /><br />
						</td>
					</tr>
					<tr>
						<td colspan="2"><input id="scorm_upl_type_1" type="radio" name="scorm_upl_type" value="1" checked="checked" onchange="jlms_dis_forms(this,1);" /> <label for="scorm_upl_type_1"><strong><?php echo _JLMS_LPATH_CHOOSE_LOCAL_FILE;?></strong></label></td>
					</tr>
					<tr>
						<td><?php echo _JLMS_CHOOSE_FILE;?></td>
						<td>
							<input size="40" class="inputbox" type="file" name="scorm_file" />
						</td>
					</tr>
					<tr>
						<td colspan="2"><input id="scorm_upl_type_2" type="radio" name="scorm_upl_type" value="2" onchange="jlms_dis_forms(this,2);" /> <label for="scorm_upl_type_2"><strong><?php echo _JLMS_LPATH_CHOOSE_FTP_FILE;?></strong></label></td>
					</tr>
					<tr>
						<td><?php echo _JLMS_CHOOSE_FILE;?></td>
						<td>
							<input size="40" class="inputbox" type="text" disabled="disabled"  name="scorm_ftp_file" />
						</td>
					</tr>
					<tr>
						<td valign="middle" style="vertical-align:middle"><br /><?php echo _JLMS_PLACE_IN;?></td>
						<td><br /><?php echo $lists['course_folders'];?></td>
					</tr>
					<tr id="scorm_stage_width_section"<?php if($params->get('scorm_layout',0)==0) { echo ' style="display:none"'; } ?>>
						<td><br /><?php echo _JLMS_LP_SCORM_DISPLAY_WIDTH;?></td>
						<td><br />
							<input size="40" class="inputbox" type="text" name="scorm_width" value="<?php echo $row->scorm_width;?>" />
						</td>
					</tr>
					<tr>
						<td><br /><?php echo _JLMS_LP_SCORM_DISPLAY_HEIGHT;?></td>
						<td><br />
							<input size="40" class="inputbox" type="text" name="scorm_height" value="<?php echo $row->scorm_height;?>" />
						</td>
					</tr>
					<tr>
						<td><br /><?php echo _JLMS_LPATH_SCORM_NAV_BAR_OPTION;?></td>
						<td><br />

							<select class="inputbox" size="1" name="params[scorm_nav_bar]" onchange="tmp_nb_var = this.value;jlms_change_scorm_stages_view();">
								<option<?php if($params->get('scorm_nav_bar',0)==0) echo ' selected="selected"';?> value="0"><?php echo _JLMS_LP_SCORM_NAV_BAR_HIDE;?></option>
								<option<?php if($params->get('scorm_nav_bar',0)==1) echo ' selected="selected"';?> value="1"><?php echo _JLMS_LP_SCORM_NAV_BAR_TOP;?></option>
								<option<?php if($params->get('scorm_nav_bar',0)==2) echo ' selected="selected"';?> value="2"><?php echo _JLMS_LP_SCORM_NAV_BAR_LEFT;?></option>
							</select>
						</td>
					</tr>
					<tr>
						<td><br /><?php echo _JLMS_LP_SCORM_LAYOUT_TYPE;?></td>
						<td><br />
							<select class="inputbox" size="1" name="params[scorm_layout]"  onchange="tmp_sl_var = this.value;jlms_change_scorm_stages_view();">
								<option<?php if($params->get('scorm_layout',0)==0) echo ' selected="selected"';?> value="0"><?php echo _JLMS_LP_SCORM_LAYOUT_INLINE;?></option>
								<option<?php if($params->get('scorm_layout',0)==1) echo ' selected="selected"';?> value="1"><?php echo _JLMS_LP_SCORM_LAYOUT_SBOX;?></option>
							</select>
						</td>
					</tr>
					<tr>
					<td valign="middle"><br /><?php echo _JLMS_OUTDOCS_VISFOR?></td>
					<td><br />
					<?php
						$chk1 = '';
						$chk2 = '';
						$chk3 = '';
						if($row->outdoc_share == 0) $chk1 = ' checked="checked"';
						if($row->outdoc_share == 1) $chk2 = ' checked="checked"';
						if($row->outdoc_share == 2) $chk3 = ' checked="checked"'; ?>
						<input type="radio" name="outdoc_share" id="outdoc_share0" value="0"<?php echo $chk1?> /><label for="outdoc_share0"><?php echo _JLMS_OUTDOCS_PRIVATE?></label>
						<input type="radio" name="outdoc_share" id="outdoc_share1" value="1"<?php echo $chk2?> /><label for="outdoc_share1"><?php echo _JLMS_OUTDOCS_TEACHERS?></label>
						<input type="radio" name="outdoc_share" id="outdoc_share2" value="2"<?php echo $chk3?> /><label for="outdoc_share2"><?php echo _JLMS_OUTDOCS_ALL;?></label>
					</td>
				</tr>
				<tr>
					<td><br /><?php echo _JLMS_OUTDOCS_SHOWCOURSES;?></td>
					<td><br /><?php echo $lists['share_to_courses'];?></td>
				</tr>
				<tr>
					<td colspan="2" valign="top" align="left" style="text-align:left "><br /><?php echo _JLMS_DESCRIPTION;?></td>
				</tr>	
				<tr>
					<td colspan="2">
					<?php
						JLMS_editorArea( 'editor1', $row->doc_description, 'doc_description', '100%;', '250', '40', '20' ) ; ?>
					</td>
				</tr>
				</table>
					<input type="hidden" name="option" value="<?php echo $option;?>" />
					<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
					<input type="hidden" name="task" value="save_scorm" />
					<input type="hidden" name="boxchecked" value="0" />
					<input type="hidden" name="id" value="<?php echo $row->id;?>" />
					</form>
				<?php 
				}
		JLMS_TMPL::CloseTS();
		JLMS_TMPL::CloseMT();
	}
}
?>