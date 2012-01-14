<?php
/**
* joomla_lms.course_lpath.html.php
* (c) JoomaLMS eLearning Software http://www.joomlalms.com/
**/

// no direct access
defined( '_JLMS_EXEC' ) or die( 'Restricted access' );
//TODO: dobavit' vse nugnye polya + JS proverki
class JLMS_course_lpath_html {
	
		function NewScormLink($scorm_details, $id, $option, $lists){
			global $Itemid, $JLMS_CONFIG;
			
			$rows_c = $lists['collapsed_folders'];
?>
	<script language="javascript" type="text/javascript">
	<!--//--><![CDATA[//><!--
	function submitbutton(pressbutton) {
		var form=document.adminForm;
			form.task.value = pressbutton;form.submit();
	}

	var TreeArray1 = new Array();
	var TreeArray2 = new Array();
	var Is_ex_Array = new Array();
	<?php
	$i = 1;
	foreach ($scorm_details as $row) {
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
	
	//--><!]]>
	</script>
	<?php
		JLMS_TMPL::OpenMT();

		$hparams = array();
		$toolbar = array();
		$title = '';

		$title = _JLMS_SCORM_ADD_A_SCORM_PACKAGE_FROM_THE_LIBRARY;

		$toolbar[] = array('btn_type' => 'save', 'btn_js' => "javascript:submitbutton('save_scormlink');");
		$toolbar[] = array('btn_type' => 'cancel', 'btn_js' => "javascript:submitbutton('cancel_scormlink');");
		JLMS_TMPL::ShowHeader('doc', $title, $hparams, $toolbar);
		JLMS_TMPL::OpenTS(); 
?>
		<form action="<?php echo sefRelToAbs("index.php?option=".$option."&amp;Itemid=".$Itemid);?>" method="post" name="adminForm" enctype="multipart/form-data">
<?php if (count($scorm_details)) { ?>

		<?php $max_tree_width = 0; if (isset($scorm_details[0])) {$max_tree_width = $scorm_details[0]->tree_max_width;}?>

				<table width="100%" cellpadding="0" cellspacing="0" border="0" class="<?php echo JLMSCSS::_('jlmslist');?>">
					<tr>
						<<?php echo JLMSCSS::tableheadertag();?> width="20" class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center">&nbsp;#&nbsp;</<?php echo JLMSCSS::tableheadertag();?>>
						<<?php echo JLMSCSS::tableheadertag();?> width="16" class="<?php echo JLMSCSS::_('sectiontableheader');?>"><input type="checkbox" value="0" name="hidden_box" style="visibility:hidden" /></<?php echo JLMSCSS::tableheadertag();?>>
						<<?php echo JLMSCSS::tableheadertag();?> width="<?php echo (16*($max_tree_width + 1));?>" class="<?php echo JLMSCSS::_('sectiontableheader');?>" colspan="<?php echo ($max_tree_width + 1);?>">&nbsp;</<?php echo JLMSCSS::tableheadertag();?>>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" width="45%"><?php echo _JLMS_LPATH_TBL_HEAD_NAME;?></<?php echo JLMSCSS::tableheadertag();?>>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" width="60%"><?php echo _JLMS_LPATH_TBL_HEAD_DESCR;?></<?php echo JLMSCSS::tableheadertag();?>>
					</tr>
					<?php 
					$k = 1;
					$i_counter = 0;
					$vis_mode = 0;
					for ($i=0, $n=count($scorm_details); $i < $n; $i++) {
							$checked = mosHTML::idBox( $i_counter, $scorm_details[$i]->id);
							
					$max_tree_width = $scorm_details[$i]->tree_max_width;	

					// Collapsed/Expanded view
					$tree_row_style = '';
					$visible_folder = true;//$next_row_is_visible;
					//$next_row_is_visible = true;
					if ($vis_mode) {
						if ($scorm_details[$i]->tree_mode_num < $vis_mode) {
							$vis_mode = 0;
						}
					}
					if (in_array($scorm_details[$i]->id, $rows_c)) {
						//$next_row_is_visible = false;
						if ($vis_mode) {
							if ($scorm_details[$i]->tree_mode_num < $vis_mode) {
								$vis_mode = $scorm_details[$i]->tree_mode_num;
							} else {
								$visible_folder = false;
							}
						} else {
							$vis_mode = $scorm_details[$i]->tree_mode_num+1;
						}
					} elseif($vis_mode) {
						if ($scorm_details[$i]->tree_mode_num >= $vis_mode) {
							$visible_folder = false;
						} else {
							$vis_mode = 0;
						}
					}
					if (!$visible_folder) {
						$tree_row_style = ' style="visibility:hidden;display:none"';
					}
?>							
						<tr id="tree_row_<?php echo $scorm_details[$i]->id;?>" class="<?php echo JLMSCSS::_('sectiontableentry'.$k); ?>"<?php echo $tree_row_style;?>>
						
						<td valign="middle" align="center"><?php echo ( $i_counter + 1 ); ?></td>
						<td valign="middle"><?php if($scorm_details[$i]->folder_flag != 1) echo $checked; ?></td>
						
					<?php $add_img = '';
						if ($scorm_details[$i]->tree_mode_num) {
							$g = 0;
							$tree_modes[$scorm_details[$i]->tree_mode_num - 1] = $scorm_details[$i]->tree_mode;
							while ($g < ($scorm_details[$i]->tree_mode_num - 1)) {
								$pref = '';
								if (isset($tree_modes[$g]) && ($tree_modes[$g] == 2) ) { $pref = 'empty_'; }
								$add_img .= "<td width='16' valign='middle'><img src=\"".$JLMS_CONFIG->get('live_site')."/components/com_joomla_lms/lms_images/treeview/".$pref."line.png\" width='16' height='16' alt='".$pref."line' /></td>";
								$g ++;
							}
							$add_img .= "<td width='16' valign='middle'><img src=\"".$JLMS_CONFIG->get('live_site')."/components/com_joomla_lms/lms_images/treeview/sub".$scorm_details[$i]->tree_mode.".png\" width='16' height='16' alt='sub".$scorm_details[$i]->tree_mode."' /></td>";
							$max_tree_width = $max_tree_width - $g - 1;
						}
						echo $add_img;?>
						
						<td align="center" valign="middle" width='16'>
						
						<?php if ($scorm_details[$i]->folder_flag == 1) {
							$collapse_img = 'collapseall.png';
							$collapse_alt = _JLMS_SCORM_COLL_FOLDER;
							
							if (in_array($scorm_details[$i]->id, $rows_c)) {
								$collapse_img = 'expandall.png';
								$collapse_alt = _JLMS_DOCS_EXP_FOLDER;
							}
			
							echo "<span id='tree_div_".$scorm_details[$i]->id."' style='alignment:center; width:16px; font-weight:bold; cursor:pointer; vertical-align:middle;' onclick='Ex_Folder(".$scorm_details[$i]->id.",".$scorm_details[$i]->id.",true)'><img class='JLMS_png' id='tree_img_".$scorm_details[$i]->id."' src=\"".$JLMS_CONFIG->get('live_site')."/components/com_joomla_lms/lms_images/docs/$collapse_img\" width='13' height='13' alt='".$collapse_alt."' title='".$collapse_alt."' /></span>";
						} else {
							echo "<span style='alignment:center; width:16px; font-weight:bold; vertical-align:middle;'><img class='JLMS_png' src=\"".$JLMS_CONFIG->get('live_site')."/components/com_joomla_lms/lms_images/files/tlb_scorm.png\" width='16' height='16' alt='tlb_scorm' title='tlb_scorm' /></span>";
						}?>
						
						</td>
<!--						<td valign="middle" align="left"><?php if($scorm_details[$i]->parent_id) echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"; echo $scorm_details[$i]->doc_name;?></td>	
-->						
						<td align="left" valign="middle" <?php if ($max_tree_width > 0) { echo "colspan='".($max_tree_width + 1)."'";} ?> width="45%">
							<span style='font-weight:bold; vertical-align:middle;'>
							<?php if ($scorm_details[$i]->folder_flag == 1) {
								echo '&nbsp;<strong>'.$scorm_details[$i]->doc_name.'</strong>';
							} else { ?>
								
									&nbsp;<?php echo $scorm_details[$i]->doc_name;?>
							<?php } ?>
							</span>
						</td>
						
						<td valign="middle">
						<?php
							$doc_descr = strip_tags($scorm_details[$i]->doc_description);
							if (!$scorm_details[$i]->folder_flag && !$scorm_details[$i]->file_id) {
								if (strlen($doc_descr) > 75) {
									$doc_descr = substr($doc_descr, 0, 75)."...";
								}
							}
							echo $doc_descr?$doc_descr:'&nbsp;'; ?>
						</td>
					</tr>		
					<?php 
					$k = 3 - $k;
					$i_counter++;
					}?>
				</table>
<?php } else {
	echo '<div class="joomlalms_user_message">'._JLMS_NO_SHARED_ITEMS.'</div>';
} ?>
			<input type="hidden" name="option" value="<?php echo $option;?>" />
			<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
			<input type="hidden" name="task" value="invite_scorm" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="course_id" value="<?php echo $id;?>" />
		</form>
<?php 
		JLMS_TMPL::CloseTS();
		JLMS_TMPL::CloseMT();		
	}
	
	function show_LPContent( $course_id, $lpath_id, $option, &$row_lp, &$lists, $doc_type = 'content' ) {
		global $Itemid;

		JLMS_TMPL::OpenMT();

		$hparams = array();
		$toolbar = array();
		$toolbar[] = array('btn_type' => 'back', 'btn_js' => sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid&amp;task=compose_lpath&amp;course_id=$course_id&amp;id=$lpath_id") );
		JLMS_TMPL::ShowHeader('lpath', $row_lp->step_name, $hparams, $toolbar);

		if ($doc_type == 'content') {
			JLMS_TMPL::OpenTS();
			$text = JLMS_ShowText_WithFeatures($row_lp->step_description);
			echo $text;
			JLMS_TMPL::CloseTS();
		}

		JLMS_TMPL::CloseMT();
	}

	function newLPath( &$row, &$lists, $option, $course_id, $params, $lp_params ) {		
		global $Itemid, $_MAMBOTS, $JLMS_CONFIG; ?>
<script language="javascript" type="text/javascript">
<!--//--><![CDATA[//><!--
function jlms_isChecked(formname,isitchecked){
	if (isitchecked == true){
		eval('document.'+formname+'.boxchecked.value++');
	}
	else {
		eval('document.'+formname+'.boxchecked.value--');
	}
}
function setgood() {
	return true;
}
function submitbutton(pressbutton, jform_name) {
	var form = eval("document."+jform_name);//adminForm;
	if (pressbutton == 'cancel_lpath') {
		form.task.value = 'cancel_lpath';
		form.submit();
	} else {
		if (pressbutton == 'lpath_add_prereq') {
			if (form.lpath_new_prereq.value && form.lpath_new_prereq.value != 0 && form.lpath_new_prereq.value != '0') {
				form.task.value = 'lpath_add_prereq';
				form.submit();
				return;
			} else {
				return;
			}
		} else if (pressbutton == 'lpath_del_prereq') {
			if (form.boxchecked.value && form.boxchecked.value != 0 && form.boxchecked.value != '0') {
				form.task.value = 'lpath_del_prereq';
				form.submit();
				return;
			} else {
				return;
			}
		}

<?php
	$editor3 =& JLMS07062010_JFactory::getEditor();
	echo $editor3->save( 'lpath_completion_msg' );
	$editor2 =& JLMS07062010_JFactory::getEditor();
	echo $editor2->save( 'lpath_description' );
?>

		if (form.lpath_name.value == "") {
			alert( "<?php echo _JLMS_LPATH_ENTER_NAME;?>" );
		} else {
			try {
				form.onsubmit();
			} catch(e) {
				//alert(e);
			}
			var form_msg = document.adminFormlp_msg;
			try {
				form_msg.onsubmit();
			} catch(e) {
				//alert(e);
			}
			form.lpath_completion_msg.value = form_msg.lpath_completion_msg.value;
			form.submit();
		}
	}
}
//--><!]]>
</script>
<?php
		JLMS_TMPL::OpenMT();

		$hparams = array();
		JLMS_TMPL::ShowHeader(($row->item_id?'scorm':'lpath'), ($row->id?_JLMS_LPATH_EDIT_LPATH:_JLMS_LPATH_NEW_LPATH), $hparams);

		JLMS_TMPL::OpenTS('', ' valign="top"');

				if (!$row->id || ($row->id && !$row->item_id)) { ?>

					<form action="<?php echo sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid");?>" method="post" name="adminFormlp" enctype="multipart/form-data">
					<table width="100%" cellpadding="0" cellspacing="0" border="0" id="jlms_item_properties">
					<tr>
						<td width="30%" align="left" valign="middle" style="vertical-align:middle ">
							&nbsp;
						</td>
						<td align="right" style="text-align:right ">
						<?php $toolbar = array();
						$toolbar[] = array('btn_type' => 'save', 'btn_js' => "javascript:submitbutton('save_lpath', 'adminFormlp');");
						$toolbar[] = array('btn_type' => 'cancel', 'btn_js' => "javascript:submitbutton('cancel_lpath', 'adminFormlp');");
						echo JLMS_ShowToolbar($toolbar); ?>
						</td>
					</tr>
					<tr>
						<td width="30%"><?php echo _JLMS_ENTER_NAME;?></td>
						<td>
							<input size="40" class="inputbox" type="text" name="lpath_name" value="<?php echo $row->lpath_name;?>" />
						</td>
					</tr>
<?php $tr_style = '';
if(!$JLMS_CONFIG->get('plugin_forum') || !$JLMS_CONFIG->get('plugin_lpath_forum')) {
	$tr_style = ' style="display:none;"';
} else {
	if (!$params->get('course_forum_created')) {
		$tr_style = ' style="display:none;"';
	}
} ?>
					<tr<?php echo $tr_style;?>>
						<td align="left" width="15%" valign="middle" style="vertical-align:middle "><br /><?php echo _JLMS_COURSES_ADD_FORUM;?></td>
						<td><br />
							<?php 
							if ($params->get('course_forum_created')) {
								echo mosHTML::yesnoRadioList( "lp_params[add_forum]", 'class="inputbox" ', $lp_params->get('add_forum',0));
							} else {
								echo _JLMS_CREATE_COURSE_FORUM_FIRST;
							}
							?>
						</td>
					</tr>
					<tr>
						<td width="30%"><br />
							<?php echo _JLMS_LPATH_STATUS_PUB;?>:
						</td>
						<td><br />
							<?php echo mosHTML::yesnoRadioList( "published", 'class="inputbox" ', $row->published);?>
						</td>
					</tr>
					<tr>
						<td width="30%"><br />
							<?php echo _JLMS_LPATH_RESUME_LAST_ATTEMPT;?>
						</td>
						<td><br />
							<?php echo mosHTML::yesnoRadioList( "lp_params[resume_last_attempt]", 'class="inputbox" ', $lp_params->get('resume_last_attempt', 1));?>
						</td>
					</tr>
					<tr>
						<td width="30%"><br />
							<?php echo _JLMS_LPATH_SHOW_NAV_LEFT;?>:
						</td>
						<td><br />
							<?php echo mosHTML::yesnoRadioList( "lp_params[navigation_type]", 'class="inputbox" ', $lp_params->get('navigation_type', 0));?>
						</td>
					</tr>
					<tr>
						<td width="15%" valign="top" style="vertical-align:top "><br /><?php echo _JLMS_IS_TIME_RELATED;?></td>
						<td><br />
							<?php JLMS_HTML::_('showperiod.field', $row->is_time_related, $row->show_period, 'adminFormlp' ) ?>
						</td>
					</tr>
					<tr>
						<td align="left" valign="top" style="vertical-align:top "><br /><?php echo _JLMS_SHORT_DESCRIPTION;?></td>
						<td><br /><textarea class="inputbox" name="lpath_shortdescription" cols="50" rows="3"><?php echo $row->lpath_shortdescription; ?></textarea></td>
					</tr>
					<tr>
						<td colspan="2" align="left" style="text-align:left "><br /><?php echo _JLMS_DESCRIPTION;?></td>
					</tr>
					<tr>
						<td colspan="2">
						<?php JLMS_editorArea( 'editor2', $row->lpath_description, 'lpath_description', '100%', '250', '40', '20' ) ; ?>
						</td>
					</tr>
				</table>
				<input type="hidden" name="option" value="<?php echo $option;?>" />
				<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
				<input type="hidden" name="task" value="save_lpath" />
				<input type="hidden" name="boxchecked" value="0" />
				<input type="hidden" name="id" value="<?php echo $row->id;?>" />
				<input type="hidden" name="course_id" value="<?php echo $course_id;?>" />
				<input type="hidden" name="lpath_type" value="1" />
				<input type="hidden" name="lpath_completion_msg" value="" />
				</form>
				<form action="<?php echo sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid");?>" method="post" name="adminFormlp_msg" onsubmit="setgood();">
				<table width="100%" cellpadding="0" cellspacing="0" border="0" class="jlms_table_no_borders">
					<tr>
						<td colspan="2" align="left" style="text-align:left "><br /><?php echo _JLMS_LPATH_COMPLETION_MSG;?></td>
					</tr>
					<tr>
						<td colspan="2">
						<?php JLMS_editorArea( 'editor3', $row->lpath_completion_msg, 'lpath_completion_msg', '100%', '250', '40', '20' ) ;
						 ?>
						</td>
					</tr>
				</table>
				</form>
				<br />
				<form action="<?php echo sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid");?>" method="post" name="adminFormlp_prereq">
	<?php JLMS_course_lpath_html::editLpath_showPrereq($lists, 'adminFormlp_prereq',$row->id?true:false); ?>
				<input type="hidden" name="option" value="<?php echo $option;?>" />
				<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
				<input type="hidden" name="task" value="save_lpath" />
				<input type="hidden" name="boxchecked" value="0" />
				<input type="hidden" name="id" value="<?php echo $row->id;?>" />
				<input type="hidden" name="course_id" value="<?php echo $course_id;?>" />
				<input type="hidden" name="lpath_type" value="1" />
				<input type="hidden" name="lpath_completion_msg" value="" />
				</form>
				<?php
				}
		JLMS_TMPL::CloseTS();
		JLMS_TMPL::CloseMT();
	}

	function newLPath_SCORM( &$row, &$lists, $option, $course_id, $params, $lp_params ) {
		global $Itemid, $_MAMBOTS, $JLMS_CONFIG; 
		
		?>		
<script language="javascript" type="text/javascript">
<!--//--><![CDATA[//><!--
var lpath_sel_type = 2;
var scorm_upl_type = 1;
<?php if(!$row->id) { ?>
var tmp_nb_var = <?php echo $params->get('scorm_nav_bar',0);?>;
var tmp_sl_var = <?php echo $params->get('scorm_layout',0);?>;
var tmp_sc_gb = <?php echo $lp_params->get('show_in_gradebook',1);?>;
var tmp_sc_hid = <?php echo $lp_params->get('hide_in_list',0);?>;
<?php } else { ?>
var tmp_nb_var = 0;
var tmp_sl_var = 0;
var tmp_sc_gb = 0;
var tmp_sc_hid = 0;
<?php } ?>
var tmp_sc_pub = 0;
function jlms_isChecked(formname,isitchecked){
	if (isitchecked == true){
		eval('document.'+formname+'.boxchecked.value++');
	}
	else {
		eval('document.'+formname+'.boxchecked.value--');
	}
}
function jlms_change_scorm_stages_view() {
	if (tmp_sl_var == 1) {
		$('scorm_stage_width_section').style.display = '';
	} else {
		$('scorm_stage_width_section').style.display = 'none';
	}
}
function submitbutton(pressbutton, jform_name) {
	var form = eval("document."+jform_name);//adminForm;
	lpath_sel_type = form.lpath_type.value;
	if (pressbutton == 'cancel_lpath') {
		form.task.value = 'cancel_lpath';
		form.submit();
	} else {
		if (pressbutton == 'lpath_add_prereq') {
			if (form.lpath_new_prereq.value && form.lpath_new_prereq.value != 0 && form.lpath_new_prereq.value != '0') {
				form.task.value = 'lpath_add_prereq';
				form.submit();
			} else {
				return;
			}
		} else if (pressbutton == 'lpath_del_prereq') {
			if (form.boxchecked.value && form.boxchecked.value != 0 && form.boxchecked.value != '0') {
				form.task.value = 'lpath_del_prereq';
				form.submit();
			} else {
				return;
			}
		}
<?php if(!$row->id) { ?>
		if (scorm_upl_type == 1) {
			form.scorm_height.value = document.adminFormsc_media.scorm_height.value;
			
			for (var i=0; i < document.adminFormsc_media.is_time_related.length; i++)
			{
			   if (document.adminFormsc_media.is_time_related[i].checked)
			   {
			    	form.is_time_related.value = document.adminFormsc_media.is_time_related[i].value;			      
			   }
			}
			
			form.days.value = document.adminFormsc_media.days.value;
			form.hours.value = document.adminFormsc_media.hours.value;
			form.mins.value = document.adminFormsc_media.mins.value;
			
			form['params[scorm_nav_bar]'].value = tmp_nb_var;
			form['params[scorm_layout]'].value = tmp_sl_var;
			form['lp_params[hide_in_list]'].value = tmp_sc_hid;
			form['lp_params[published]'].value = tmp_sc_pub;
			if (form.scorm_file.value == "") {
				alert( "<?php echo _JLMS_LPATH_CHOOSE_SCORM;?>" );
			} else {
				form.lpath_shortdescription.value = document.adminFormsc_media.lpath_shortdescription.value;
				form['lp_params[show_in_gradebook]'].value = tmp_sc_gb;
				form.submit();
			}
		} else {
			document.adminFormsc_media.lpath_name.value = form.lpath_name.value;
			if (document.adminFormsc_media.scorm_ftp_file.value == "") {
				alert( "<?php echo _JLMS_LPATH_CHOOSE_SCORM;?>" );
			} else {
				document.adminFormsc_media.submit();
			}
		}
		<?php } else { ?>
		if (form.lpath_name.value == "") {
			alert( "<?php echo _JLMS_LPATH_ENTER_NAME;?>" );
		} else {
			<?php if ($row->id && $row->item_id) {
			} else { ?>
			form.lpath_shortdescription.value = document.adminFormsc_media.lpath_shortdescription.value;
			<?php } ?>
			form.submit();
		}
<?php } ?>
	}
}
<?php if (!$row->id) { ?>
function jlms_dis_forms(elem, type) {
	if (type == 1 || type == '1') {
		if (elem.checked) {
			scorm_upl_type = 1;
			elem.form.scorm_file.disabled = false;
			document.adminFormsc_media.scorm_ftp_file.disabled = true;
		} else {
			scorm_upl_type = 2;
			elem.form.scorm_file.disabled = true;
			document.adminFormsc_media.scorm_ftp_file.disabled = false;
		}
	}
	if (type == 2 || type == '2') {
		if (elem.checked) {
			scorm_upl_type = 2;
			elem.form.scorm_file.disabled = true;
			document.adminFormsc_media.scorm_ftp_file.disabled = false;
		} else {
			scorm_upl_type = 1;
			elem.form.scorm_file.disabled = false;
			document.adminFormsc_media.scorm_ftp_file.disabled = true;
		}
	}
}
<?php } 
elseif( $row->lp_type != 2) {?>
function jlms_dis_forms(elem, type) {
	
	if (type == 1 || type == '1') {
		if (elem.checked) {
			scorm_upl_type = 1;
			elem.form.scorm_file.disabled = false;
			document.adminFormsc.scorm_ftp_file.disabled = true;
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
		} else {
			scorm_upl_type = 1;
			elem.form.scorm_file.disabled = false;
			document.adminFormsc.scorm_ftp_file.disabled = true;
		}
	}
}	
<?php }?>
//--><!]]>
</script>
<?php
		JLMS_TMPL::OpenMT();

		$hparams = array();
		JLMS_TMPL::ShowHeader(($row->item_id?'scorm':'lpath'), ($row->id?_JLMS_LPATH_EDIT_LPATH:_JLMS_LPATH_NEW_LPATH_FROM_SCORM), $hparams);

		JLMS_TMPL::OpenTS('', ' valign="top"');
?>
				<form action="<?php echo sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid");?>" method="post" name="adminFormsc" enctype="multipart/form-data">
					<table width="100%" cellpadding="0" cellspacing="0" border="0" class="jlms_table_no_borders">
					<tr>
						<td align="left" class="contentheading" valign="middle" style="vertical-align:middle ">
							&nbsp;
						</td>
						<td align="right" style="text-align:right ">
						<?php $toolbar = array();
						$toolbar[] = array('btn_type' => 'save', 'btn_js' => "javascript:submitbutton('save_lpath', 'adminFormsc');");
						$toolbar[] = array('btn_type' => 'cancel', 'btn_js' => "javascript:submitbutton('cancel_lpath', 'adminFormsc');");
						echo JLMS_ShowToolbar($toolbar); ?>
						</td>
					</tr>
					</table>
					<table width="100%" cellpadding="0" cellspacing="0" border="0" class="jlms_item_properties">
					<tr>
						<td width="30%"><?php echo _JLMS_ENTER_NAME;?><br /></td>
						<td>
							<input size="40" class="inputbox" type="text" name="lpath_name" value="<?php echo $row->lpath_name;?>" /><br />
						</td>
					</tr>
					<tr <?php if(!$JLMS_CONFIG->get('plugin_forum') || !$JLMS_CONFIG->get('plugin_lpath_forum')) echo 'style="display:none;"';?>>
						<td align="left" width="15%" valign="middle" style="vertical-align:middle ">
							<br />
							<?php echo _JLMS_COURSES_ADD_FORUM;?>
						</td>
						<td>
							<br />
							<?php 
							if ($params->get('course_forum_created')) {
								echo mosHTML::yesnoRadioList( "lp_params[add_forum]", 'class="inputbox" ', $lp_params->get('add_forum',0));
							} else {
								echo _JLMS_CREATE_COURSE_FORUM_FIRST;
							}
							?>
						</td>
					</tr>
					<?php if ($row->lp_type != 2) { ?>					
					
						<tr>
							<td colspan="2">
								<input id="scorm_upl_type_1" type="radio" name="scorm_upl_type" value="1" checked="checked" <?php if($row->lp_type != 2) {?>onchange="jlms_dis_forms(this,1);"<?php }?> /> <label for="scorm_upl_type_1"><strong><?php echo _JLMS_LPATH_CHOOSE_LOCAL_FILE;?></strong></label></td>
						</tr>
						<tr>
							<td><?php echo _JLMS_CHOOSE_FILE;?></td>
							<td>
								<input size="40" class="inputbox" type="file" name="scorm_file" />
							</td>
						</tr>
					
						<tr>
							<td colspan="2"><input id="scorm_upl_type_2" type="radio" name="scorm_upl_type" value="2" <?php if($row->lp_type != 2) {?>onchange="jlms_dis_forms(this,2);" <?php }?> /> <label for="scorm_upl_type_2"><strong><?php echo _JLMS_LPATH_CHOOSE_FTP_FILE;?></strong></label></td>
						</tr>
						
						<?php if($row->id) {?>	
						<tr>
						<td><?php echo _JLMS_CHOOSE_FILE;?></td>
						<td>
							<input size="40" class="inputbox" type="text" disabled="disabled"  name="scorm_ftp_file" />
						</td>
					</tr>
					<?php }?>
					
					<?php } ?>
					<?php if ($row->id) { ?>
					<tr>
						<td align="left" valign="top" style="vertical-align:top "><br /><?php echo _JLMS_SHORT_DESCRIPTION;?></td>
						<td><br /><textarea class="inputbox" name="lpath_shortdescription" cols="50" rows="3"><?php echo $row->lpath_shortdescription; ?></textarea></td>
					</tr>
					<tr>
						<td width="30%"><br />
							<?php echo _JLMS_LPATH_STATUS_PUB;?>:
						</td>
						<td><br />
							<?php echo mosHTML::yesnoRadioList( "published", 'class="inputbox" ', $row->published);?>
						</td>
					</tr>
					<tr>
						<td><br /><?php echo _JLMS_LPATH_HIDE_RESOURCE;?><br /></td>
						<td><br />
							<?php echo mosHTML::yesnoRadioList( "lp_params[hide_in_list]", 'class="inputbox" onchange="if (this.checked) { tmp_sc_hid = this.value;}" ', $lp_params->get('hide_in_list',0));?><br />
						</td>
					</tr>
					<tr>
						<td><br /><?php echo _JLMS_SHOW_IN_GRADEBOOK_OPTION;?><br /></td>
						<td><br />
							<?php echo mosHTML::yesnoRadioList( "lp_params[show_in_gradebook]", 'class="inputbox" onchange="if (this.checked) { tmp_sc_gb = this.value;}" ', $lp_params->get('show_in_gradebook',1));?><br />
						</td>
					</tr>
					<tr id="scorm_stage_width_section"<?php if($params->get('scorm_layout',0)==0) { echo ' style="display:none"'; } ?>>
						<td><br /><?php echo _JLMS_LP_SCORM_DISPLAY_WIDTH;?><br /></td>
						<td><br />
							<input size="40" class="inputbox" type="text" name="scorm_width" value="<?php echo $row->scorm_width;?>" /><br />
						</td>
					</tr>
					<tr>
						<td><br /><?php echo _JLMS_LP_SCORM_DISPLAY_HEIGHT;?><br /></td>
						<td><br />
							<input size="40" class="inputbox" type="text" name="scorm_height" value="<?php echo $row->scorm_height;?>" /><br />
						</td>
					</tr>
					<tr>
						<td><br /><?php echo _JLMS_LPATH_SCORM_NAV_BAR_OPTION;?></td>
						<td><br />
							<select class="inputbox" size="1" name="params[scorm_nav_bar]" onchange="tmp_nb_var = this.value;">
								<option<?php if($params->get('scorm_nav_bar',0)==0) echo ' selected="selected"';?> value="0"><?php echo _JLMS_LP_SCORM_NAV_BAR_HIDE;?></option>
								<option<?php if($params->get('scorm_nav_bar',0)==1) echo ' selected="selected"';?> value="1"><?php echo _JLMS_LP_SCORM_NAV_BAR_TOP;?></option>
								<option<?php if($params->get('scorm_nav_bar',0)==2) echo ' selected="selected"';?> value="2"><?php echo _JLMS_LP_SCORM_NAV_BAR_LEFT;?></option>
							</select>
						</td>
					</tr>
					<tr>
						<td><br /><?php echo _JLMS_LP_SCORM_LAYOUT_TYPE;?></td>
						<td><br />
							<select class="inputbox" size="1" name="params[scorm_layout]" onchange="tmp_sl_var = this.value;jlms_change_scorm_stages_view();">
								<option<?php if($params->get('scorm_layout',0)==0) echo ' selected="selected"';?> value="0"><?php echo _JLMS_LP_SCORM_LAYOUT_INLINE;?></option>
								<option<?php if($params->get('scorm_layout',0)==1) echo ' selected="selected"';?> value="1"><?php echo _JLMS_LP_SCORM_LAYOUT_SBOX;?></option>
							</select>
						</td>
					</tr>
					<tr>
						<td width="30%" valign="top" style="vertical-align:top "><br /><?php echo _JLMS_IS_TIME_RELATED;?></td>
						<td><br />
							<?php JLMS_HTML::_('showperiod.field', $row->is_time_related, $row->show_period, 'adminFormsc' ) ?>
						</td>
					</tr>
					<tr>
						<td colspan="2">
					<?php JLMS_course_lpath_html::editLpath_showPrereq($lists, 'adminFormsc',$row->id?true:false); ?>
						</td>
					</tr>
					<?php } ?>
					</table>
					<input type="hidden" name="option" value="<?php echo $option;?>" />
					<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
					<input type="hidden" name="task" value="save_lpath" />
					<input type="hidden" name="boxchecked" value="0" />
					<input type="hidden" name="id" value="<?php echo $row->id;?>" />
					<input type="hidden" name="course_id" value="<?php echo $course_id;?>" />
					<input type="hidden" name="lpath_type" value="2" />
					<input type="hidden" name="scorm_upl_type" value="1" />
					<?php if (!$row->id) { ?>
					<input type="hidden" name="scorm_height" value="<?php echo $row->scorm_height;?>" />
					<input type="hidden" name="is_time_related" value="<?php echo $row->is_time_related;?>" />
					<input type="hidden" name="days" value="0" />
					<input type="hidden" name="hours" value="0" />
					<input type="hidden" name="mins" value="0" />
					<input type="hidden" name="params[scorm_nav_bar]" value="<?php echo $params->get('scorm_nav_bar', 0);?>" />
					<input type="hidden" name="params[scorm_layout]" value="<?php echo $params->get('scorm_layout', 0);?>" />
					<input type="hidden" name="lp_params[show_in_gradebook]" value="<?php echo $lp_params->get('show_in_gradebook',1);?>" />
					<input type="hidden" name="lp_params[hide_in_list]" value="<?php echo $lp_params->get('hide_in_list',0);?>" />
					<input type="hidden" name="lp_params[published]" value="0" />
					<input type="hidden" name="lpath_shortdescription" value="" />
					<?php } ?>
					</form>
					<form action="<?php echo sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid");?>" method="post" name="adminFormsc_media">
					<?php if (!$row->id) { ?>
					<table width="100%" cellpadding="0" cellspacing="0" border="0" class="jlms_item_properties">
					<tr>
						<td width="30%"><?php echo _JLMS_CHOOSE_FILE;?></td>
						<td>
							<input size="40" class="inputbox" type="text" disabled="disabled"  name="scorm_ftp_file" />
						</td>
					</tr>
					<tr>
						<td align="left" valign="top" style="vertical-align:top "><br /><?php echo _JLMS_SHORT_DESCRIPTION;?></td>
						<td><br /><textarea class="inputbox" name="lpath_shortdescription" cols="50" rows="3"><?php echo $row->lpath_shortdescription; ?></textarea></td>
					</tr>
					<tr>
						<td width="30%"><br />
							<?php echo _JLMS_LPATH_STATUS_PUB;?>:
						</td>
						<td><br />
							<?php echo mosHTML::yesnoRadioList( "lp_params[published]", 'class="inputbox" onchange="if (this.checked) { tmp_sc_pub = this.value;}" ', $row->published);?>
						</td>
					</tr>
					<tr>
						<td><br /><?php echo _JLMS_LPATH_HIDE_RESOURCE;?><br /></td>
						<td><br />
							<?php echo mosHTML::yesnoRadioList( "lp_params[hide_in_list]", 'class="inputbox" onchange="if (this.checked) { tmp_sc_hid = this.value;}" ', $lp_params->get('hide_in_list',0));?><br />
						</td>
					</tr>
					<tr>
						<td><br /><?php echo _JLMS_SHOW_IN_GRADEBOOK_OPTION;?><br /></td>
						<td><br />
						<?php echo mosHTML::yesnoRadioList( "lp_params[show_in_gradebook]", 'class="inputbox" onchange="if (this.checked) { tmp_sc_gb = this.value;}" ', $lp_params->get('show_in_gradebook',1));?><br />
						</td>
					</tr>
					<tr id="scorm_stage_width_section"<?php if($params->get('scorm_layout',0)==0) { echo ' style="display:none"'; } ?>>
						<td><br /><?php echo _JLMS_LP_SCORM_DISPLAY_WIDTH;?><br /></td>
						<td><br />
							<input size="40" class="inputbox" type="text" name="scorm_width" value="<?php echo $row->scorm_width;?>" /><br />
						</td>
					</tr>
					<tr>
						<td><br /><?php echo _JLMS_LP_SCORM_DISPLAY_HEIGHT;?><br /></td>
						<td><br />
							<input size="40" class="inputbox" type="text" name="scorm_height" value="<?php echo $row->scorm_height;?>" /><br />
						</td>
					</tr>					
					<tr>
						<td><br /><?php echo _JLMS_LPATH_SCORM_NAV_BAR_OPTION;?></td>
						<td><br />
						<select class="inputbox" size="1" name="params[scorm_nav_bar]" onchange="tmp_nb_var = this.value;">
							<option<?php if($params->get('scorm_nav_bar',0)==0) echo ' selected="selected"';?> value="0"><?php echo _JLMS_LP_SCORM_NAV_BAR_HIDE;?></option>
							<option<?php if($params->get('scorm_nav_bar',0)==1) echo ' selected="selected"';?> value="1"><?php echo _JLMS_LP_SCORM_NAV_BAR_TOP;?></option>
							<option<?php if($params->get('scorm_nav_bar',0)==2) echo ' selected="selected"';?> value="2"><?php echo _JLMS_LP_SCORM_NAV_BAR_LEFT;?></option>
						</select>
						</td>
					</tr>					
					<tr>
						<td><br /><?php echo _JLMS_LP_SCORM_LAYOUT_TYPE;?></td>
						<td><br />
							<select class="inputbox" size="1" name="params[scorm_layout]" onchange="tmp_sl_var = this.value;jlms_change_scorm_stages_view();">
								<option<?php if($params->get('scorm_layout',0)==0) echo ' selected="selected"';?> value="0"><?php echo _JLMS_LP_SCORM_LAYOUT_INLINE;?></option>
								<option<?php if($params->get('scorm_layout',0)==1) echo ' selected="selected"';?> value="1"><?php echo _JLMS_LP_SCORM_LAYOUT_SBOX;?></option>
							</select>
						</td>
					</tr>					
					<tr>
						<td width="30%" valign="top" style="vertical-align:top "><br /><?php echo _JLMS_IS_TIME_RELATED;?></td>
						<td><br />
							<?php JLMS_HTML::_('showperiod.field', $row->is_time_related, $row->show_period, 'adminFormsc_media' ) ?>
						</td>
					</tr>
					<tr>
						<td colspan="2">
						<?php JLMS_course_lpath_html::editLpath_showPrereq($lists, 'adminFormsc',$row->id?true:false); ?>
						</td>
					</tr>
					</table>
					<?php } ?>
				<input type="hidden" name="option" value="<?php echo $option;?>" />
				<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
				<input type="hidden" name="task" value="save_lpath" />
				<input type="hidden" name="boxchecked" value="0" />
				<input type="hidden" name="published" value="0" />
				<input type="hidden" name="id" value="<?php echo $row->id;?>" />
				<input type="hidden" name="course_id" value="<?php echo $course_id;?>" />
				<input type="hidden" name="lpath_type" value="2" />
				<input type="hidden" name="lpath_name" value="<?php echo $row->lpath_name;?>" />
				<input type="hidden" name="scorm_upl_type" value="2" />
				</form>
<?php	
				JLMS_TMPL::CloseTS();
				JLMS_TMPL::CloseMT();
	}

	function editLpath_showPrereq(&$lists, $formname = 'adminFormlp', $show_full = true) {
		$JLMS_CONFIG = & JLMSFactory::getConfig();
		
		if (!empty($lists['lps_prereq']) || !empty($lists['lps']) || !$show_full ) {
			echo JLMSCSS::h2(_JLMS_LPATH_PREREQUISITES);
		}
		if ($show_full) {
			if (!empty($lists['lps_prereq'])) {
				echo '<table cellpadding="0" cellspacing="0" border="0" width="100%" class="'.JLMSCSS::_('jlmslist').'" style="margin-bottom:0px;">';
				echo '<tr><'.JLMSCSS::tableheadertag().' width="20" class="'.JLMSCSS::_('sectiontableheader').'">#</'.JLMSCSS::tableheadertag().'><'.JLMSCSS::tableheadertag().' class="'.JLMSCSS::_('sectiontableheader').'" colspan="'.($JLMS_CONFIG->get('time_released_lpaths_prerequisites', false) ? '11' : '2').'">'._JLMS_LPATH_LIST_PREREQUISITES.'</'.JLMSCSS::tableheadertag().'></tr>';
				$k = 1;
				foreach ($lists['lps_prereq'] as $lppr) {
					echo '<tr class="'.JLMSCSS::_('sectiontableentry'.$k).'">';
					echo '<td><input type="checkbox" name="cid[]" value="'.$lppr->req_id.'" onclick="jlms_isChecked(\''.$formname.'\', this.checked);"/>';

								echo '<td width="20%" nowrap="nowrap">'.$lppr->lpath_name."</td>";
								if ($JLMS_CONFIG->get('time_released_lpaths_prerequisites', false)) {
									if($lppr->minute){
										echo '<td width="1%">&nbsp;</td>';
										echo '<td width="1%">'._JLMS_LPATH_PREREQ_MINUTE.'</td>';
										echo '<td width="1%">'.$lppr->minute.'</td>';
									} else {
										echo '<td width="1%">&nbsp;</td>';	
										echo '<td width="1%">&nbsp;</td>';	
										echo '<td width="1%">&nbsp;</td>';	
									}
									if($lppr->hour){
										echo '<td width="1%">&nbsp;</td>';
										echo '<td width="1%">'._JLMS_LPATH_PREREQ_HOUR.'</td>';
										echo '<td width="1%">'.$lppr->hour.'</td>';
									} else {
										echo '<td width="1%">&nbsp;</td>';	
										echo '<td width="1%">&nbsp;</td>';	
										echo '<td width="1%">&nbsp;</td>';	
									}
									if($lppr->day){
										echo '<td width="1%">&nbsp;</td>';
										echo '<td width="1%">'._JLMS_LPATH_PREREQ_DAY.'</td>';
										echo '<td width="1%">'.$lppr->day.'</td>';
									} else {
										echo '<td width="1%">&nbsp;</td>';	
										echo '<td width="1%">&nbsp;</td>';	
										echo '<td width="1%">&nbsp;</td>';	
									}
								}
								echo '<td width="auto">&nbsp;</td>';

					echo '</tr>';
				}
				echo '</table>';
			}
			if (isset($lists['lps']) && $lists['lps']) {
				echo '<table cellpadding="0" cellspacing="0" border="0" width="100%" class="jlms_table_no_borders">';
				$controls = array();
				$controls[] = array('href' => "javascript:submitbutton('lpath_del_prereq', '".$formname."')", 'title' => _JLMS_DELETE, 'img' => 'buttons_22/btn_delete_22.png');
				$controls[] = array('href' => "javascript:submitbutton('lpath_add_prereq', '".$formname."')", 'title' => _JLMS_ADD_ITEM, 'img' => 'buttons_22/btn_add_22.png');
				$controls[] = array('href' => '', 'title' => '', 'img' => '', 'custom' => $lists['lps']);
				JLMS_TMPL::ShowControlsFooter($controls, '', false, true);
				echo '</table>';
				
				/*
				?>
				<table cellpadding="2" cellspacing="2" border="0" class="jlms_table_no_borders">
				<tr>
					<td width="22" style="vertical-align:middle ">
						<a href="javascript:submitbutton('lpath_del_prereq', '<?php echo $formname;?>');">
							<img src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/buttons_22/btn_delete_22.png" class="JLMS_png" width="22" height="22" border="0" alt="btn_delete" />
						</a>
					</td>
					<td width="22" style="vertical-align:middle ">
						<a href="javascript:submitbutton('lpath_add_prereq', '<?php echo $formname;?>');">
							<img src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/buttons_22/btn_add_22.png" class="JLMS_png" width="22" height="22" border="0" alt="btn_add" />
						</a>
					</td>
					<td>
						<table border="0" cellpadding="0" cellspacing="0" class="jlms_table_no_borders">
							<tr>
								<td style="vertical-align:middle;">
									<?php echo $lists['lps'];?>
								</td>
								<td>
									&nbsp;&nbsp;
								</td>
							<?php if ($JLMS_CONFIG->get('time_released_lpaths_prerequisites', false)) { ?>
								<td style="vertical-align:middle;">
									<?php echo _JLMS_LPATH_PREREQ_MINUTE;?>
								</td>
								<td style="vertical-align:middle;">
									<input type="text" name="prereq_minute" value="0" size="2" class="inputbox" />
								</td>
								<td>
									&nbsp;&nbsp;
								</td>
								<td style="vertical-align:middle;">
									<?php echo _JLMS_LPATH_PREREQ_HOUR;?>
								</td>
								<td style="vertical-align:middle;">
									<input type="text" name="prereq_hour" value="0" size="1" class="inputbox" />
								</td>
								<td>
									&nbsp;&nbsp;
								</td>
								<td style="vertical-align:middle;">
									<?php echo _JLMS_LPATH_PREREQ_DAY;?>
								</td>
								<td style="vertical-align:middle;">
									<input type="text" name="prereq_day" value="0" size="1" class="inputbox" />
								</td>
							<?php } ?>
							</tr>
						</table>
					</td>
				</tr>
				</table>
			<?php */}
		} else {
			echo _JLMS_LPATH_PREREQUISITES_FIRST_MSG;
		}
	}

	function editLPathStep_Chapter( &$row, &$lists, $option, $course_id, $lpath_id ) {
		global $Itemid; ?>
<script language="javascript" type="text/javascript">
<!--
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
	if (form.step_name.value == "") {
		alert( "<?php echo _JLMS_LPATH_ENTER_CHAP_NAME;?>" );
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
		$toolbar = array();
		$toolbar[] = array('btn_type' => 'save', 'btn_js' => "javascript:submitbutton('lpath_save_chapter');");
		$toolbar[] = array('btn_type' => 'cancel', 'btn_js' => "javascript:submitbutton('cancel_lpath_step');");
		JLMS_TMPL::ShowHeader('lpath', $row->id ? _JLMS_LPATH_EDIT_CHAP : _JLMS_LPATH_NEW_CHAP, $hparams, $toolbar);

		JLMS_TMPL::OpenTS();
?>
		<form action="<?php echo sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid");?>" method="post" name="adminForm" onsubmit="setgood();">
			<table width="100%" cellpadding="0" cellspacing="0" border="0" id="jlms_item_properties">
				<tr>
					<td width="15%" valign="middle" style="vertical-align:middle"><?php echo _JLMS_ENTER_NAME;?></td>
					<td>
						<input size="53" class="inputbox" type="text" name="step_name" value="<?php echo str_replace('"','&quot;',$row->step_name);?>" />
					</td>
				</tr>
				<tr>
					<td valign="middle" style="vertical-align:middle"><br /><?php echo _JLMS_PLACE_IN;?></td>
					<td>
						<br /><?php echo $lists['lpath_chaps'];?>
					</td>
				</tr>
				<tr>
					<td align="left" valign="top" style="vertical-align:top "><br /><?php echo _JLMS_SHORT_DESCRIPTION;?></td>
					<td><br /><textarea class="inputbox" name="step_shortdescription" cols="50" rows="3"><?php echo $row->step_shortdescription; ?></textarea></td>
				</tr>
				<tr>
					<td colspan="2" align="left" style="text-align:left "><br /><?php echo _JLMS_DESCRIPTION;?></td>
				</tr>
				<tr>
					<td colspan="2">
					<?php JLMS_editorArea( 'editor2', $row->step_description, 'step_description', '100%', '250', '40', '20' ) ; ?>
					</td>
				</tr>
			</table>
			<input type="hidden" name="option" value="<?php echo $option;?>" />
			<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
			<input type="hidden" name="task" value="lpath_save_chapter" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="id" value="<?php echo $row->id;?>" />
			<input type="hidden" name="course_id" value="<?php echo $course_id;?>" />
			<input type="hidden" name="lpath_id" value="<?php echo $lpath_id;?>" />
		</form>
<?php
		JLMS_TMPL::CloseTS();
		JLMS_TMPL::CloseMT();

	}

	function editLPathStep_Content( &$row, &$lists, $option, $course_id, $lpath_id ) {
		global $Itemid; ?>
<script language="javascript" type="text/javascript">
<!--
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
	if ( (pressbutton != 'cancel_lpath_step') && (form.step_name.value == "")) {
		alert( "<?php echo _JLMS_LPATH_ENTER_CONTENT_NAME;?>" );
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
		$toolbar = array();
		$toolbar[] = array('btn_type' => 'save', 'btn_js' => "javascript:submitbutton('lpath_save_content');");
		$toolbar[] = array('btn_type' => 'cancel', 'btn_js' => "javascript:submitbutton('cancel_lpath_step');");
		JLMS_TMPL::ShowHeader('lpath', $row->id ? _JLMS_LPATH_EDIT_CONTENT : _JLMS_LPATH_NEW_CONTENT, $hparams, $toolbar);

		JLMS_TMPL::OpenTS();
?>
		<form action="<?php echo sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid");?>" method="post" name="adminForm" onsubmit="setgood();">
			<table width="100%" cellpadding="0" cellspacing="0" border="0" id="jlms_item_properties">
				<tr>
					<td width="15%" valign="middle" style="vertical-align:middle"><?php echo _JLMS_ENTER_NAME;?></td>
					<td>
						<input size="53" class="inputbox" type="text" name="step_name" value="<?php echo str_replace('"','&quot;',$row->step_name);?>" />
					</td>
				</tr>
				<tr>
					<td valign="middle" style="vertical-align:middle"><br /><?php echo _JLMS_PLACE_IN;?></td>
					<td>
						<br /><?php echo $lists['lpath_chaps'];?>
					</td>
				</tr>
				<tr>
					<td align="left" valign="top" style="vertical-align:top "><br /><?php echo _JLMS_SHORT_DESCRIPTION;?></td>
					<td><br /><textarea class="inputbox" name="step_shortdescription" cols="50" rows="3"><?php echo $row->step_shortdescription; ?></textarea></td>
				</tr>
				<tr>
					<td colspan="2" align="left" style="text-align:left "><br /><?php echo _JLMS_DESCRIPTION;?></td>
				</tr>
				<tr>
					<td colspan="2">
					<?php JLMS_editorArea( 'editor2', $row->step_description, 'step_description', '100%', '250', '40', '20' ) ; ?>
					</td>
				</tr>
			</table>
			<input type="hidden" name="option" value="<?php echo $option;?>" />
			<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
			<input type="hidden" name="task" value="lpath_save_content" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="id" value="<?php echo $row->id;?>" />
			<input type="hidden" name="course_id" value="<?php echo $course_id;?>" />
			<input type="hidden" name="lpath_id" value="<?php echo $lpath_id;?>" />
		</form>
<?php
		JLMS_TMPL::CloseTS();
		JLMS_TMPL::CloseMT();

	}

	function editLPathStep_Doc( &$row, &$lists, $option, $course_id, $lpath_id ) {
		global $Itemid; ?>
<script language="javascript" type="text/javascript">
<!--
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
	if ( (pressbutton != 'cancel_lpath_step') && (form.step_name.value == "")) {
		alert( "<?php echo _JLMS_LPATH_ENTER_DOC_NAME;?>" );
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
		$toolbar = array();
		$toolbar[] = array('btn_type' => 'save', 'btn_js' => "javascript:submitbutton('lpath_save_doc');");
		$toolbar[] = array('btn_type' => 'cancel', 'btn_js' => "javascript:submitbutton('cancel_lpath_step');");
		JLMS_TMPL::ShowHeader('lpath', $row->id ? _JLMS_LPATH_EDIT_DOC : _JLMS_LPATH_NEW_DOC, $hparams, $toolbar);

		JLMS_TMPL::OpenTS();
?>
		<form action="<?php echo sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid");?>" method="post" name="adminForm" onsubmit="setgood();">
			<table width="100%" cellpadding="0" cellspacing="0" border="0" class="jlms_item_properties">
				<tr>
					<td width="15%" valign="middle" style="vertical-align:middle"><?php echo _JLMS_ENTER_NAME;?></td>
					<td>
						<input size="53" class="inputbox" type="text" name="step_name" value="<?php echo str_replace('"','&quot;',$row->step_name);?>" />
					</td>
				</tr>
				<tr>
					<td valign="middle" style="vertical-align:middle"><br /><?php echo _JLMS_PLACE_IN;?></td>
					<td>
						<br /><?php echo $lists['lpath_chaps'];?>
					</td>
				</tr>
				<tr>
					<td align="left" valign="top" style="vertical-align:top "><br /><?php echo _JLMS_SHORT_DESCRIPTION;?></td>
					<td><br /><textarea class="inputbox" name="step_shortdescription" cols="50" rows="3"><?php echo $row->step_shortdescription; ?></textarea></td>
				</tr>
				<tr>
					<td colspan="2" align="left" style="text-align:left "><br /><?php echo _JLMS_DESCRIPTION;?></td>
				</tr>
				<tr>
					<td colspan="2">
					<?php JLMS_editorArea( 'editor2', $row->step_description, 'step_description', '100%', '250', '40', '20' ) ; ?>
					</td>
				</tr>
			</table>
			<input type="hidden" name="option" value="<?php echo $option;?>" />
			<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
			<input type="hidden" name="task" value="lpath_save_doc" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="id" value="<?php echo $row->id?>" />
			<input type="hidden" name="course_id" value="<?php echo $course_id;?>" />
			<input type="hidden" name="lpath_id" value="<?php echo $lpath_id;?>" />
			<input type="hidden" name="item_id" value="<?php echo $row->item_id;?>" />
		</form>
<?php
		JLMS_TMPL::CloseTS();
		JLMS_TMPL::CloseMT();

	}

	function editLPathStep_Link( &$row, &$lists, $option, $course_id, $lpath_id ) {
		global $Itemid; ?>
<script language="javascript" type="text/javascript">
<!--
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
	if ( (pressbutton != 'cancel_lpath_step') && (form.step_name.value == "")) {
		alert( "<?php echo _JLMS_LPATH_ENTER_LINK_NAME;?>" );
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
		$toolbar = array();
		$toolbar[] = array('btn_type' => 'save', 'btn_js' => "javascript:submitbutton('lpath_save_link');");
		$toolbar[] = array('btn_type' => 'cancel', 'btn_js' => "javascript:submitbutton('cancel_lpath_step');");
		JLMS_TMPL::ShowHeader('lpath', $row->id ? _JLMS_LPATH_EDIT_LINK : _JLMS_LPATH_NEW_LINK, $hparams, $toolbar);

		JLMS_TMPL::OpenTS();
?>
		<form action="<?php echo sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid");?>" method="post" name="adminForm" onsubmit="setgood();">
			<table width="100%" cellpadding="0" cellspacing="0" border="0" class="jlms_item_properties">
				<tr>
					<td width="15%" valign="middle" style="vertical-align:middle"><?php echo _JLMS_ENTER_NAME;?></td>
					<td>
						<input size="53" class="inputbox" type="text" name="step_name" value="<?php echo str_replace('"','&quot;',$row->step_name);?>" />
					</td>
				</tr>
				<tr>
					<td valign="middle" style="vertical-align:middle"><br /><?php echo _JLMS_PLACE_IN;?></td>
					<td>
						<br /><?php echo $lists['lpath_chaps'];?>
					</td>
				</tr>
				<tr>
					<td align="left" valign="top" style="vertical-align:top "><br /><?php echo _JLMS_SHORT_DESCRIPTION;?></td>
					<td><br /><textarea class="inputbox" name="step_shortdescription" cols="50" rows="3"><?php echo $row->step_shortdescription; ?></textarea></td>
				</tr>
				<tr>
					<td colspan="2" align="left" style="text-align:left "><br /><?php echo _JLMS_DESCRIPTION;?></td>
				</tr>
				<tr>
					<td colspan="2">
					<?php JLMS_editorArea( 'editor2', $row->step_description, 'step_description', '100%', '250', '40', '20' ) ; ?>
					</td>
				</tr>
			</table>
			<input type="hidden" name="option" value="<?php echo $option;?>" />
			<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
			<input type="hidden" name="task" value="lpath_save_link" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="id" value="<?php echo $row->id;?>" />
			<input type="hidden" name="course_id" value="<?php echo $course_id;?>" />
			<input type="hidden" name="lpath_id" value="<?php echo $lpath_id;?>" />
			<input type="hidden" name="item_id" value="<?php echo $row->item_id;?>" />
		</form>
<?php
		JLMS_TMPL::CloseTS();
		JLMS_TMPL::CloseMT();

	}

	function editLPathStep_Quiz( &$row, &$lists, $option, $course_id, $lpath_id ) {
		global $Itemid; ?>
<script language="javascript" type="text/javascript">
<!--
function submitbutton(pressbutton) {
	var form = document.adminForm;
	if ( (pressbutton != 'cancel_lpath_step') && (form.step_name.value == "")) {
		alert( "<?php echo _JLMS_LPATH_ENTER_QUIZ_NAME;?>" );
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
		$toolbar = array();
		$toolbar[] = array('btn_type' => 'save', 'btn_js' => "javascript:submitbutton('lpath_save_quiz');");
		$toolbar[] = array('btn_type' => 'cancel', 'btn_js' => "javascript:submitbutton('cancel_lpath_step');");
		JLMS_TMPL::ShowHeader('lpath', $row->id ? _JLMS_LPATH_EDIT_QUIZ : _JLMS_LPATH_EDIT_QUIZ, $hparams, $toolbar);

		JLMS_TMPL::OpenTS();
?>
		<form action="<?php echo sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid");?>" method="post" name="adminForm">
			<table width="100%" cellpadding="0" cellspacing="0" border="0" class="jlms_item_properties">
				<tr>
					<td width="15%" valign="middle" style="vertical-align:middle"><?php echo _JLMS_ENTER_NAME;?></td>
					<td>
						<input size="53" class="inputbox" type="text" name="step_name" value="<?php echo str_replace('"','&quot;',$row->step_name);?>" />
					</td>
				</tr>
				<tr>
					<td valign="middle" style="vertical-align:middle"><br /><?php echo _JLMS_PLACE_IN;?></td>
					<td>
						<br /><?php echo $lists['lpath_chaps'];?>
					</td>
				</tr>
				<tr>
					<td align="left" valign="top" style="vertical-align:top "><br /><?php echo _JLMS_SHORT_DESCRIPTION;?></td>
					<td><br /><textarea class="inputbox" name="step_shortdescription" cols="50" rows="3"><?php echo $row->step_shortdescription; ?></textarea></td>
				</tr>
			</table>
			<input type="hidden" name="option" value="<?php echo $option;?>" />
			<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
			<input type="hidden" name="task" value="lpath_save_quiz" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="id" value="<?php echo $row->id?>" />
			<input type="hidden" name="course_id" value="<?php echo $course_id;?>" />
			<input type="hidden" name="lpath_id" value="<?php echo $lpath_id;?>" />
			<input type="hidden" name="item_id" value="<?php echo $row->item_id;?>" />
		</form>
<?php
		JLMS_TMPL::CloseTS();
		JLMS_TMPL::CloseMT();

	}

	function editLPathStep_Scorm( &$row, &$lists, $option, $course_id, $lpath_id ) {
		global $Itemid; ?>
<script language="javascript" type="text/javascript">
<!--
function submitbutton(pressbutton) {
	var form = document.adminForm;
	if ( (pressbutton != 'cancel_lpath_step') && (form.step_name.value == "")) {
		alert( "<?php echo _JLMS_LPATH_ENTER_SCORM_NAME;?>" );
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
		$toolbar = array();
		$toolbar[] = array('btn_type' => 'save', 'btn_js' => "javascript:submitbutton('lpath_save_scorm');");
		$toolbar[] = array('btn_type' => 'cancel', 'btn_js' => "javascript:submitbutton('cancel_lpath_step');");
		JLMS_TMPL::ShowHeader('lpath', $row->id ? _JLMS_LPATH_EDIT_SCORM : _JLMS_LPATH_EDIT_SCORM, $hparams, $toolbar);

		JLMS_TMPL::OpenTS();
?>
		<form action="<?php echo sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid");?>" method="post" name="adminForm">
			<table width="100%" cellpadding="0" cellspacing="0" border="0" class="jlms_item_properties">
				<tr>
					<td width="15%" valign="middle" style="vertical-align:middle"><?php echo _JLMS_ENTER_NAME;?></td>
					<td>
						<input size="53" class="inputbox" type="text" name="step_name" value="<?php echo str_replace('"','&quot;',$row->step_name);?>" />
					</td>
				</tr>
				<tr>
					<td valign="middle" style="vertical-align:middle"><br /><?php echo _JLMS_PLACE_IN;?></td>
					<td>
						<br /><?php echo $lists['lpath_chaps'];?>
					</td>
				</tr>
				<tr>
					<td align="left" valign="top" style="vertical-align:top "><br /><?php echo _JLMS_SHORT_DESCRIPTION;?></td>
					<td><br /><textarea class="inputbox" name="step_shortdescription" cols="50" rows="3"><?php echo $row->step_shortdescription; ?></textarea></td>
				</tr>
			</table>
			<input type="hidden" name="option" value="<?php echo $option;?>" />
			<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
			<input type="hidden" name="task" value="lpath_save_scorm" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="id" value="<?php echo $row->id?>" />
			<input type="hidden" name="course_id" value="<?php echo $course_id;?>" />
			<input type="hidden" name="lpath_id" value="<?php echo $lpath_id;?>" />
			<input type="hidden" name="item_id" value="<?php echo $row->item_id;?>" />
		</form>
<?php
		JLMS_TMPL::CloseTS();
		JLMS_TMPL::CloseMT();
	}

// to do: ispravit' JS proverki	
	function showCourseLPaths( $course_id, $option, &$lpaths ) {
		global $Itemid, $my, $JLMS_DB, $JLMS_CONFIG;
		$JLMS_ACL = & JLMSFactory::getACL(); ?>
<script language="javascript" type="text/javascript">
<!--
function submitbutton(pressbutton) {
	var form = document.adminForm;
		
			if ( ((pressbutton == 'edit_lpath') || (pressbutton == 'lpath_delete') ) && (form.boxchecked.value == "0")) {
				alert('<?php echo _JLMS_ALERT_SELECT_ITEM;?>');
			} else {
				if( pressbutton == 'lpath_delete' ) {
					if (confirm('<?php echo _JLMS_ALERT_DELETE_ITEM;?>')) {
						form.task.value = pressbutton;
						form.submit();
					}
				}
				else {	
						form.task.value = pressbutton;
						form.submit();
				}
			}	
}
	
function submitbutton_order(pressbutton, item_id) {
	var form = document.adminForm;
	if ((pressbutton == 'lpath_orderup') || (pressbutton == 'lpath_orderdown')){
		if (item_id) {
		form.task.value = pressbutton;
		form.row_id.value = item_id;
		form.submit();
		}
	}
}
function submitbutton_change(pressbutton, state) {
	var form = document.adminForm;
	if (pressbutton == 'change_lpath'){
		if (form.boxchecked.value == 0 || form.boxchecked.value == "0") {
			alert('<?php echo _JLMS_ALERT_SELECT_ITEM;?>');
		} else {
			form.task.value = pressbutton;
			form.state.value = state;
			form.submit();
		}
	}
}
function submitbutton_change2(pressbutton, state, cid_id) {
	var form = document.adminForm;
	if (pressbutton == 'change_lpath'){
		form.task.value = pressbutton;
		form.state.value = state;
		form.cid2.value = cid_id;
		form.submit();
	}
}
function submitbutton_allorder(n) {
	var form = document.adminForm;

	for ( var j = 0; j <= n; j++ ) {
		box = eval( "document.adminForm.cb" + j );
		if ( box ) {
			if ( box.checked == false ) {
				box.checked = true;
			}
		}
	}
	form.task.value = 'lpath_saveorederall';
	form.submit();
}
//-->
</script>
<?php
		JLMS_TMPL::OpenMT();

		$hparams = array();
		JLMS_TMPL::ShowHeader('lpath', _JLMS_LPATH_TITLE, $hparams);

		JLMS_TMPL::OpenTS();

		//temporary check for number of items
		$num_items = 0;
		$there_were_squeezeboxes = false;
		for ($i=0, $n=count($lpaths); $i < $n; $i++) {
			$row_path = $lpaths[$i];
			$is_hidden = false;
			if ($row_path->item_id) {
				$tmp_params = new JLMSParameters($row_path->lp_params);
				if ($tmp_params->get('hide_in_list',0) == 1) {
					$is_hidden = true;
				}
			}
			if ($JLMS_ACL->CheckPermissions('lpaths', 'manage')) {
				$task = 'compose_lpath';
				$title = _JLMS_LPATH_LINK_TITLE_COMPOSE;
			} elseif ($JLMS_ACL->CheckPermissions('lpaths', 'view')) {
				$task = 'show_lpath';
				$title = _JLMS_LPATH_LINK_TITLE_VIEW;
				if ($is_hidden) {
					continue;
				}
			}
			if (isset($row_path->is_hidden) && $row_path->is_hidden && (!$JLMS_ACL->CheckPermissions('lpaths', 'view_all'))) {
				continue;
			}
			$num_items++;
		}
?>
			<form action="<?php echo $JLMS_CONFIG->getCfg('live_site')."/index.php?option=$option&amp;Itemid=$Itemid";?>" method="post" name="adminForm">
<?php 	if ($num_items) { ?>
				<table width="100%" cellpadding="0" cellspacing="0" border="0" class="<?php echo JLMSCSS::_('jlmslist');?>">
					<tr>
						<<?php echo JLMSCSS::tableheadertag();?> width="20" class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center">#</<?php echo JLMSCSS::tableheadertag();?>>
					<?php if ($JLMS_ACL->CheckPermissions('lpaths', 'manage') || $JLMS_ACL->CheckPermissions('lpaths', 'publish')) { ?>
						<<?php echo JLMSCSS::tableheadertag();?> width="25" class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($lpaths); ?>);" /></<?php echo JLMSCSS::tableheadertag();?>>
					<?php } ?>
						<<?php echo JLMSCSS::tableheadertag();?> width="20" class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center">&nbsp;</<?php echo JLMSCSS::tableheadertag();?>>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>"><?php echo _JLMS_LPATH_TBL_HEAD_NAME;?></<?php echo JLMSCSS::tableheadertag();?>>
					<?php if ($JLMS_ACL->CheckPermissions('lpaths', 'publish')) { ?>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" width="1">&nbsp;</<?php echo JLMSCSS::tableheadertag();?>>
					<?php } ?>
					<?php if ($JLMS_ACL->CheckPermissions('lpaths', 'order')) { ?>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" width="1">&nbsp;</<?php echo JLMSCSS::tableheadertag();?>>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" width="1">&nbsp;</<?php echo JLMSCSS::tableheadertag();?>>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" width="1"><?php echo _JLMS_REORDER;?></<?php echo JLMSCSS::tableheadertag();?>>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" width="1"><a class="jlms_img_link" href="javascript:submitbutton_allorder(<?php echo count( $lpaths )-1;?>)"><img width="16" height="16" border="0" title="<?php echo _JLMS_SAVEORDER;?>" alt="<?php echo _JLMS_SAVEORDER;?>" src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/filesave.png"/></a></<?php echo JLMSCSS::tableheadertag();?>>
					<?php } ?>
					<?php if ($JLMS_ACL->CheckPermissions('lpaths', 'manage')) { ?>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" width="1">&nbsp;</<?php echo JLMSCSS::tableheadertag();?>>
					<?php } ?>
					<?php if (!$JLMS_ACL->CheckPermissions('lpaths', 'manage')) { ?>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" width="1">&nbsp;</<?php echo JLMSCSS::tableheadertag();?>>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center"><?php echo _JLMS_LPATH_TBL_STARTING;?></<?php echo JLMSCSS::tableheadertag();?>>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center"><?php echo _JLMS_LPATH_TBL_ENDING;?></<?php echo JLMSCSS::tableheadertag();?>>
					<?php } ?>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>"><?php echo _JLMS_LPATH_TBL_HEAD_DESCR;?></<?php echo JLMSCSS::tableheadertag();?>>
					</tr>
				<?php
				$k = 1;
				$i_counter = 0;
				
				for ($i=0, $n=count($lpaths); $i < $n; $i++) {
					$row_path = $lpaths[$i];
					$is_hidden = false;
					$is_squeezebox = false;
					if ($row_path->item_id) {
						$tmp_params = new JLMSParameters($row_path->lp_params);
						if ($tmp_params->get('hide_in_list',0) == 1) {
							$is_hidden = true;
						}
						if (isset($row_path->scorm_params)) {
							$tmp_params2 = new JLMSParameters($row_path->scorm_params);
							if ($tmp_params2->get('scorm_layout',0) == 1) {
								$is_squeezebox = true;
								$there_were_squeezeboxes = true;
							}
						}
					}
					$manage_item = false;
					if ($JLMS_ACL->CheckPermissions('lpaths', 'manage')) {
						$manage_item = true;
					}
					if ($JLMS_ACL->CheckPermissions('lpaths', 'only_own_items') && $row_path->owner_id != $my->id) {
						$manage_item = false;
					} elseif ($JLMS_ACL->CheckPermissions('lpaths', 'only_own_role') && $JLMS_ACL->GetRole() != $JLMS_ACL->UserSystemRole($JLMS_DB, $row_path->owner_id)) {
						$manage_item = false;
					}
					if ($manage_item) {
						$task = 'compose_lpath';
						$title = _JLMS_LPATH_LINK_TITLE_COMPOSE;
					} elseif ($JLMS_ACL->CheckPermissions('lpaths', 'view')) {
						$task = 'show_lpath';
						$title = _JLMS_LPATH_LINK_TITLE_VIEW;
						if ($is_hidden && !$JLMS_ACL->CheckPermissions('lpaths', 'view_all')) {
							continue;
						}
					}
					if (isset($row_path->is_hidden) && $row_path->is_hidden && (!$JLMS_ACL->CheckPermissions('lpaths', 'view_all'))) {
						continue;
					}
					$link 	= "index.php?option=".$option."&Itemid=".$Itemid."&task=".$task."&course_id=".$course_id."&id=". $row_path->id;
					// sefRelToAbs() - est' nige
					$icon_img = "toolbar/tlb_lpath";
					$icon_alt = "learnpath";
					if ($row_path->item_id) {
						$title = _JLMS_LPATH_LINK_TITLE_SCORM;
						//$link = "index.php?option=".$option."&Itemid=".$Itemid."&task=player_scorm&course_id=".$course_id."&id=".$row_path->item_id."&lp_type=".$row_path->lp_type;
						$link = "index.php?option=".$option."&Itemid=".$Itemid."&task=show_lpath&course_id=".$course_id."&id=".$row_path->id;//."&lp_type=".$row_path->lp_type;
						$icon_img = "toolbar/tlb_scorm";
						$icon_alt = "scorm";
					}
					$checked = mosHTML::idBox( $i_counter, $row_path->id);
					$alt = ($row_path->published)?($is_hidden?_JLMS_STATUS_PUBLISHED_AND_HIDDEN:_JLMS_LPATH_STATUS_PUB):_JLMS_LPATH_STATUS_UNPUB;
					$image = ($row_path->published)?($is_hidden?'btn_publish_hidden.png':'btn_accept.png'):'btn_cancel.png';
					$state = ($row_path->published)?0:1;
					$released_info_txt = '';
					if ($row_path->is_time_related) {
						$released_info_txt = _JLMS_WILL_BE_RELEASED_IN;
						$showperiod = $row_path->show_period;
						$ost1 = $showperiod%(24*60);		
						$sp_days = ($showperiod - $ost1)/(24*60);
						$ost2 = $showperiod%60;						
						$sp_hours = ($ost1 - $ost2)/60;
						$sp_mins = $ost2;
						$release_time_info = false;
						if ($sp_days) {
							$released_info_txt .= ' '.$sp_days.' '._JLMS_RELEASED_IN_DAYS;
							$release_time_info = true;
						}
						if ($sp_hours) {
							$released_info_txt .= ' '.$sp_hours.' '._JLMS_RELEASED_IN_HOURS;
							$release_time_info = true;
						}
						if ($sp_mins) {
							$released_info_txt .= ' '.$sp_mins.' '._JLMS_RELEASED_IN_MINUTES;
							$release_time_info = true;
						}
						if ($release_time_info) {
							$released_info_txt .= ' '._JLMS_RELEASED_AFTER_ENROLLMENT;
						}
						if ($image == 'btn_accept.png') {
							$image = 'btn_publish_wait.png';
						}
					}
					
					$publish_icon = '&nbsp;';
					if ($JLMS_ACL->CheckPermissions('lpaths', 'publish')) {
						/*$publish_icon = '<a href="javascript:submitbutton_change2(\'change_lpath\','.$state.','.$row_path->id.')" title="'.$alt.'">';
						$publish_icon .= '<img class="JLMS_png" src="'.$JLMS_CONFIG->get('live_site').'/components/com_joomla_lms/lms_images/toolbar/'.$image.'" width="16" height="16" border="0" alt="'.$alt.'" />';
						$publish_icon .= '</a>';*/
						$title_tt = $alt;
						$content_tt = $released_info_txt;
						if (isset($row_path->is_prereqs) && $row_path->is_prereqs) {
							if ($content_tt) {
								$content_tt .= '<br />';
							}
							$content_tt .= _JLMS_STATUS_CONFIGURED_PREREQUISITES;
						}
						$name_tt = '<img class="JLMS_png" src="'.$JLMS_CONFIG->get('live_site').'/components/com_joomla_lms/lms_images/toolbar/'.$image.'" width="16" height="16" border="0" alt="'.$alt.'" />';
						$link_tt = 'javascript:submitbutton_change2(\'change_lpath\','.$state.','.$row_path->id.')';
						$publish_icon = JLMS_toolTip($title_tt, $content_tt, $name_tt, $link_tt);
					}
					if ($JLMS_ACL->CheckPermissions('lpaths', 'only_own_items') && $row_path->owner_id != $my->id) {
						$checked = '&nbsp;';
						$publish_icon = '&nbsp;';
					} elseif ($JLMS_ACL->CheckPermissions('lpaths', 'only_own_role') && $JLMS_ACL->GetRole() != $JLMS_ACL->UserSystemRole($JLMS_DB, $row_path->owner_id)) {
						$checked = '&nbsp;';
						$publish_icon = '&nbsp;';
					} ?>
					<tr class="<?php echo JLMSCSS::_('sectiontableentry'.$k); ?>">
						<td valign="middle" align="center"><?php echo ( $i_counter + 1 ); ?></td>
					<?php if ($JLMS_ACL->CheckPermissions('lpaths', 'manage') || $JLMS_ACL->CheckPermissions('lpaths', 'publish')) { ?>
						<td valign="middle" align="center"><?php if(!isset($row_path->is_link)) echo $checked; ?></td>
					<?php } ?>
						<td valign="middle" align="center">
							<span style="vertical-align:middle; text-align:center">
								<img class='JLMS_png' src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/<?php echo $icon_img;?>.png" width='16' height='16' alt="<?php echo $icon_alt;?>" />
							</span>
						</td>
						<td valign="middle" align="left">
							<?php 	
							if (isset($row_path->is_link) && $row_path->is_link) {
									echo $row_path->lpath_name;
							} elseif ($is_squeezebox) {
								$x_size = 0;
								$y_size = 0;
								if ( isset($row_path->scorm_width) ) {
									$x_size = $row_path->scorm_width;
									if ($x_size == 100) { //settings from 1.0.6 version (means 100% width)
										$x_size = 0;
									}
								}
								if ( isset($row_path->scorm_height) ) {
									$y_size = $row_path->scorm_height;
								}
							?>
									<a href="<?php echo sefRelToAbs($link);?>" class="scorm_modal" rel="{handler:'iframe', size:{x:<?php echo $x_size;?>,y:<?php echo $y_size;?>}}" title="<?php echo str_replace('"','&quot;',$row_path->lpath_name);?>">
										<?php echo $row_path->lpath_name; ?>
									</a>
							<?php	
							} else {
							?>
									<a href="<?php echo sefRelToAbs($link);?>" title="<?php echo str_replace('"','&quot;',$row_path->lpath_name);?>">
										<?php echo $row_path->lpath_name; ?>
									</a>
							<?php
							}
							?>
							
							<?php if($JLMS_CONFIG->get('show_lpaths_authors', 0)){?>
							<br />
							<span class="small"><?php echo _JLMS_HOME_AUTHOR . "&nbsp;" . $row_path->author_name;?></span>
							<?php } ?>
						</td>
					<?php if ($JLMS_ACL->CheckPermissions('lpaths', 'publish')) { ?>
						<td valign="middle">
							<?php echo $publish_icon;?>
						</td>
					<?php } ?>
					<?php if ($JLMS_ACL->CheckPermissions('lpaths', 'order')) { ?>
						<td valign="middle"><?php echo JLMS_orderUpIcon($i_counter, $row_path->id, true, 'lpath_orderup');?></td>
						<td valign="middle"><?php echo JLMS_orderDownIcon($i_counter, $n, $row_path->id, true, 'lpath_orderdown');?></td>
						<td colspan="2">
							<input type="text" name="order[]" size="5" value="<?php echo $row_path->ordering; ?>" class="inputbox" style="text-align: center" />
						</td>
					<?php } ?>
					<?php if ($JLMS_ACL->CheckPermissions('lpaths', 'manage')) { ?>
						<td valign="middle">
						<?php if ($manage_item && (($row_path->item_id && $row_path->lp_type !=2) || !$row_path->item_id )) {
							$download_message = _JLMS_LPATH_LINK_TITLE_DOWN_SCORM;
							if (!$row_path->item_id) {
								$download_message = _JLMS_LPATH_EXPORT_LP;
							}
						?>
						<a class="jlms_img_link" href="<?php echo sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid&amp;task=download_scorm&amp;course_id=$course_id&amp;id={$row_path->id}");?>" title="<?php echo $download_message;?>"><img class='JLMS_png' src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_export.png" border="0" width="16" height="16" alt="<?php echo $download_message;?>" title="<?php echo $download_message;?>" /></a>
						<?php } else { echo '&nbsp;';} ?>
						</td>
					<?php } ?>
					<?php if (!$JLMS_ACL->CheckPermissions('lpaths', 'manage')) {
						$r_img = 'btn_cancel';
						$r_sta = _JLMS_LPATH_STU_LPSTATUS_NOTCOMPLETED;
						$r_start = '-';
						$r_end = '-';
						if (!$row_path->item_id) {
							if (isset($row_path->r_status) && $row_path->r_status == 1) {
								$r_img = 'btn_accept';
								$r_sta = _JLMS_LPATH_STU_LPSTATUS_COMPLETED;
								if ($row_path->r_start) {
									$r_start = JLMS_dateToDisplay($row_path->r_start, false, $JLMS_CONFIG->get('offset')*60*60, '\<\b\\r \/>H:i:s');
								}
								if ($row_path->r_end) {
									$r_end = JLMS_dateToDisplay($row_path->r_end, false, $JLMS_CONFIG->get('offset')*60*60, '\<\b\\r \/>H:i:s');
								}
							} elseif (isset($row_path->r_status) && $row_path->r_status == 0) {
								$r_img = 'btn_pending_cur';
								if ($row_path->r_start) {
									$r_start = JLMS_dateToDisplay($row_path->r_start, false, $JLMS_CONFIG->get('offset')*60*60, '\<\b\\r \/>H:i:s');
								}
							}
						} else {
							if (isset($row_path->s_status) && $row_path->s_status == 1) {
								$r_img = 'btn_accept';
								$r_sta = _JLMS_LPATH_STU_LPSTATUS_COMPLETED;
								$r_start = '-';
								$r_end = '-';
							}
							if ($row_path->lp_type == 1 || $row_path->lp_type == 2) {
								if (isset($row_path->r_end) && $row_path->r_end) {
									$r_end = JLMS_dateToDisplay($row_path->r_end, true, $JLMS_CONFIG->get('offset')*60*60, '\<\b\\r \/>H:i:s');
								}
								if (isset($row_path->r_start) && $row_path->r_start) {
									$r_start = JLMS_dateToDisplay($row_path->r_start, true, $JLMS_CONFIG->get('offset')*60*60, '\<\b\\r \/>H:i:s');
								}
							}
						}?>
						<td valign="middle" align="center" width="16">
							<?php
							//Show Status Lapths/Scorms //by Max - 25.02.2011
							joomla_lms_html::ShowStatusAs($row_path);
							?>	
						</td>
						<td valign="middle" align="center" nowrap="nowrap"><?php echo $r_start;?></td>
						<td valign="middle" align="center" nowrap="nowrap"><?php echo $r_end;?></td>
					<?php } ?>
						<td valign="middle"><?php echo $row_path->lpath_shortdescription?$row_path->lpath_shortdescription:'&nbsp;';?></td>
					</tr>
					<?php
					$k = 3 - $k;
					$i_counter++;
				} ?>
				</table>
<?php
		} else {
			echo '<div class="joomlalms_user_message">'._JLMS_NO_ITEMS_HERE.'</div>';
		}
		if ($there_were_squeezeboxes) {
			JLMS_initialize_SqueezeBox();
		}
?>
				<input type="hidden" name="option" value="<?php echo $option;?>" />
				<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
				<input type="hidden" name="task" value="learnpaths" />
				<input type="hidden" name="id" value="<?php echo $course_id;?>" />
				<input type="hidden" name="boxchecked" value="0" />
				<input type="hidden" name="row_id" value="0" />
				<input type="hidden" name="state" value="0" />
				<input type="hidden" name="cid2" value="0" />
			</form>
<?php
		JLMS_TMPL::CloseTS();

		if ($JLMS_ACL->CheckPermissions('lpaths', 'manage') || $JLMS_ACL->CheckPermissions('lpaths', 'publish')) {
			$link_new = JLMSRoute::_("index.php?option=".$option."&amp;Itemid=".$Itemid."&amp;task=new_lpath&amp;id=".$course_id);
			$link_new_scorm = JLMSRoute::_("index.php?option=".$option."&amp;Itemid=".$Itemid."&amp;task=new_lpath_scorm&amp;id=".$course_id);
			$link_new_scormlib = JLMSRoute::_("index.php?option=$option&amp;Itemid=$Itemid&amp;task=new_scorm_from_library&amp;id=".$course_id);

			$controls = array();
			if ($JLMS_ACL->CheckPermissions('lpaths', 'publish')) {
				$controls[] = array('href' => "javascript:submitbutton_change('change_lpath',1);", 'title' => _JLMS_LPATH_SET_PUB, 'img' => 'publish');
				$controls[] = array('href' => "javascript:submitbutton_change('change_lpath',0);", 'title' => _JLMS_LPATH_SET_UNPUB, 'img' => 'unpublish');
				if ($JLMS_ACL->CheckPermissions('lpaths', 'manage')) {
					$controls[] = array('href' => 'spacer');
				}
			}
			if ($JLMS_ACL->CheckPermissions('lpaths', 'manage')) {
				$controls[] = array('href' => ampReplace($link_new), 'onclick' => "", 'title' => _JLMS_LPATH_DO_NEW_LP, 'img' => 'add_lpath');
				//TODO: translate 'Import SCORM package'
				$controls[] = array('href' => ampReplace($link_new_scorm), 'onclick' => "", 'title' => _JLMS_LPATH_IMPORT_SCORM, 'img' => 'add_scorm');
				$controls[] = array('href' => $link_new_scormlib, 'title' => _JLMS_SCORM_ADD_A_SCORM_PACKAGE_FROM_THE_LIBRARY, 'img' => 'add_library');
				$controls[] = array('href' => 'spacer');
				$controls[] = array('href' => "javascript:submitbutton('lpath_delete');", 'title' => _JLMS_LPATH_DO_DEL_LP, 'img' => 'delete');
				$controls[] = array('href' => "javascript:submitbutton('edit_lpath');", 'title' => _JLMS_LPATH_DO_EDIT_LP, 'img' => 'edit');
			}
			JLMS_TMPL::ShowControlsFooter($controls);
		}

		JLMS_TMPL::CloseMT();
	}

// todo: 1. create new chapter est' tol'ko kogda sovsem pusto, MAY BE pust' vsegda budet vnizu
// NEW: 2. NO punktu 1, + dobaviTb 'PanelUpravleniya' iz knopochek kak v 'CourseDocuments'
// + sefreltoabs()
// (WARNING) vmesto 'step-name' vyvoditsa 'doc_name' - shto odno i to ge , no 'doc_name' pokazyvaet vlogennost' (bylo davno i ne pravda)
// + preloadimages
	function showCourseLPath( $course_id, $lpath_id, &$lpath, &$conds, $option ) {
		global $Itemid, $JLMS_CONFIG; ?>

<script language="javascript" type="text/javascript">
<!--
function submitbutton(pressbutton) {
	var form = document.adminForm;

	// do field validation
	if (((pressbutton == 'lpath_item_delete') || (pressbutton == 'lpath_item_edit')) && (form.boxchecked.value == "0")){
		alert( "<?php echo _JLMS_ALERT_SELECT_ITEM;?>" );
	} else {
		form.task.value = pressbutton;
		form.submit();
	}
}
function submitbutton_order(pressbutton, item_id) {
	var form = document.adminForm;
	if ((pressbutton == 'lpath_item_orderup') || (pressbutton == 'lpath_item_orderdown')){
		if (item_id) {
		form.task.value = pressbutton;
		form.row_id.value = item_id;
		form.submit();
		}
	}
}
/*
Ordering (Max)
*/
function checkAll_button( n ) {
	for ( var j = 0; j <= n; j++ ) {
		box = eval( "document.adminForm.cb" + j );
		if ( box ) {
			if ( box.checked == false ) {
				box.checked = true;
			}
		} else {
			alert("You cannot change the order of items, as an item in the list is `Checked Out`");
			return;
		}
	}
	submitform('lpath_item_saveorder');
}
var TreeArray1 = new Array();
var TreeArray2 = new Array();
var Is_ex_Array = new Array();
<?php
$i = 1;
foreach ($lpath as $lpath_row) {
	echo "TreeArray1[".$i."] = ".$lpath_row->parent_id.";";
	echo "TreeArray2[".$i."] = ".$lpath_row->id.";";
	echo "Is_ex_Array[".$i."] = 1;" ."\n";
	$i ++;
}
?>
function Hide_Folder(fid) {
	var vis_style = 'hidden';var dis_style = 'none';var i = 1;
	while (i < TreeArray1.length) {
		if (TreeArray1[i] == fid) {
			getObj('tree_row_'+TreeArray2[i]).style.visibility = vis_style;
			getObj('tree_row_'+TreeArray2[i]).style.display = dis_style;
			Hide_Folder(TreeArray2[i]); }
		i++; }
}
function Show_Folder(fid) {
	var vis_style = 'visible';var dis_style = '';var i = 1;
	while (i < TreeArray1.length) {
		if (TreeArray1[i] == fid) {
			getObj('tree_row_'+TreeArray2[i]).style.visibility = vis_style;
			getObj('tree_row_'+TreeArray2[i]).style.display = dis_style;
			NoChange_Folder(TreeArray2[i]); }
		i++; }
}
function NoChange_Folder(fid) {
	var vis_style = 'hidden';var dis_style = 'none';var i = 1;var j = 0;
	while (i < TreeArray2.length) {
		if ( (TreeArray2[i] == fid) && (Is_ex_Array[i] == 1) ) {
			vis_style = 'visible';dis_style = '';j = 1; }
		i++; }
	i = 1;
	while (i < TreeArray1.length) {
		if (TreeArray1[i] == fid) {
			getObj('tree_row_'+TreeArray2[i]).style.visibility = vis_style;
			getObj('tree_row_'+TreeArray2[i]).style.display = dis_style;
			if (j == 1) { NoChange_Folder(TreeArray2[i]);
			} else { Hide_Folder(TreeArray2[i]); } }
		i++; }
}

function Ex_Folder(fid) {
	var i = 1;var j = 1;
	while (i < TreeArray2.length) {
		if ( (TreeArray2[i] == fid) && (Is_ex_Array[i] == 1) ) { j = 0; } i ++; }
	if (j == 1) {
		Show_Folder(fid);
		if (getObj('tree_img_' + fid).runtimeStyle) {
			var StStr = getObj('tree_img_' + fid).runtimeStyle.filter;
			var imgStr = getObj('tree_img_' + fid).outerHTML;
			imgStr = imgStr.replace('chapter_expand.png','chapter_collapse.png').replace('<?php echo _JLMS_LPATH_EXP_CHAP;?>', '<?php echo _JLMS_LPATH_COLL_CHAP;?>');
			StStr = StStr.replace('chapter_expand.png','chapter_collapse.png');
			getObj('tree_img_' + fid).outerHTML = imgStr;
			getObj('tree_img_' + fid).runtimeStyle.filter = StStr;
		} else {
			getObj('tree_img_' + fid).src = '<?php echo $JLMS_CONFIG->getCfg('live_site');?>/components/com_joomla_lms/lms_images/learnpath/chapter_collapse.png';
			getObj('tree_img_' + fid).alt = '<?php echo _JLMS_LPATH_COLLAPSE;?>';
			getObj('tree_img_' + fid).title = '<?php echo _JLMS_LPATH_COLL_CHAP;?>';
		}
	} else {
		Hide_Folder(fid);
		if (getObj('tree_img_' + fid).runtimeStyle) {
			var StStr = getObj('tree_img_' + fid).runtimeStyle.filter;
			var imgStr = getObj('tree_img_' + fid).outerHTML;
			imgStr = imgStr.replace('chapter_collapse.png','chapter_expand.png').replace('<?php echo _JLMS_LPATH_COLL_CHAP;?>', '<?php echo _JLMS_LPATH_EXP_CHAP;?>');
			StStr = StStr.replace('chapter_collapse.png','chapter_expand.png');
			getObj('tree_img_' + fid).outerHTML = imgStr;
			getObj('tree_img_' + fid).runtimeStyle.filter = StStr;
		} else {
			getObj('tree_img_' + fid).src = '<?php echo $JLMS_CONFIG->getCfg('live_site');?>/components/com_joomla_lms/lms_images/learnpath/chapter_expand.png';
			getObj('tree_img_' + fid).alt = '<?php echo _JLMS_LPATH_EXPAND;?>';
			getObj('tree_img_' + fid).title = '<?php echo _JLMS_LPATH_EXP_CHAP;?>';
		}
	}
	i = 1;
	while (i < TreeArray2.length) {
		if ( (TreeArray2[i] == fid) ) {
			if (Is_ex_Array[i] == 1) { Is_ex_Array[i] = 0; } else { Is_ex_Array[i] = 1; } }
		i++; }
}
JLMS_preloadImages('<?php echo $JLMS_CONFIG->getCfg('live_site');?>/components/com_joomla_lms/lms_images/learnpath/chapter_expand.png','<?php echo $JLMS_CONFIG->getCfg('live_site');?>/components/com_joomla_lms/lms_images/learnpath/chapter_collapse.png');
//-->
</script>
<?php
		JLMS_TMPL::OpenMT();

		$hparams = array();
		JLMS_TMPL::ShowHeader('lpath', _JLMS_LPATH_TITLE_LP, $hparams);

		$max_tree_width = 0; if (isset($lpath[0])) {$max_tree_width = $lpath[0]->tree_max_width;}
		
		JLMS_TMPL::OpenTS();
?>
		<form action="<?php echo $JLMS_CONFIG->getCfg('live_site')."/index.php?option=".$option."&amp;Itemid=".$Itemid;?>" method="post" name="adminForm">
		<table width="100%" cellpadding="0" cellspacing="0" border="0" class="<?php echo JLMSCSS::_('jlmslist');?>">
			<tr>
				<<?php echo JLMSCSS::tableheadertag();?> width="20" class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center">#</<?php echo JLMSCSS::tableheadertag();?>>
				<<?php echo JLMSCSS::tableheadertag();?> width="20" class="<?php echo JLMSCSS::_('sectiontableheader');?>">&nbsp;</<?php echo JLMSCSS::tableheadertag();?>>
				<<?php echo JLMSCSS::tableheadertag();?> width="<?php echo (16*($max_tree_width + 1));?>" class="<?php echo JLMSCSS::_('sectiontableheader');?>" colspan="<?php echo ($max_tree_width + 1);?>">&nbsp;</<?php echo JLMSCSS::tableheadertag();?>>
				<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" width="30%"><?php echo _JLMS_LPATH_TBL_HEAD_NAME_LP;?></<?php echo JLMSCSS::tableheadertag();?>>
				<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" colspan="2" width="1"><?php echo _JLMS_REORDER;?></<?php echo JLMSCSS::tableheadertag();?>>
				<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" width="1"><?php echo _JLMS_ORDER;?></<?php echo JLMSCSS::tableheadertag();?>>
				<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" width="1">
					<a class="jlms_img_link" href="javascript:checkAll_button(<?php echo count( $lpath )-1;?>)"><img width="16" height="16" border="0" title="<?php echo _JLMS_SAVEORDER;?>" alt="<?php echo _JLMS_SAVEORDER;?>" src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/filesave.png"/></a>
				</<?php echo JLMSCSS::tableheadertag();?>>
				<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" colspan="2" width="1">&nbsp;</<?php echo JLMSCSS::tableheadertag();?>>
				<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" width="60%"><?php echo _JLMS_LPATH_TBL_HEAD_DESCR_LP;?></<?php echo JLMSCSS::tableheadertag();?>>
			</tr>
		<?php
		$k = 1;
		$tree_modes = array();
		
		for ($i=0, $n=count($lpath); $i < $n; $i++) {
			$row_path = $lpath[$i];
			$max_tree_width = $row_path->tree_max_width;
			$link = "index.php?option=".$option."&amp;Itemid=".$Itemid."&amp;task=details_course&amp;id=". $row_path->id;
			//ne zabyt' sefRelToAbs
			$checked = mosHTML::idBox( $i, $row_path->id);
			?>
			
			<tr id="tree_row_<?php echo $row_path->id;?>" class="<?php echo JLMSCSS::_('sectiontableentry'.$k); ?>">
				<td align="center" valign="middle"><?php echo ( $i + 1 ); ?></td>
				<td valign="middle"><?php if(!isset($row_path->is_link)) echo $checked; ?></td>
				<?php $add_img = '';
				if ($row_path->tree_mode_num) {
					$g = 0;
					$tree_modes[$row_path->tree_mode_num - 1] = $row_path->tree_mode;
					while ($g < ($row_path->tree_mode_num - 1)) {
						$pref = '';
						if (isset($tree_modes[$g]) && ($tree_modes[$g] == 2) ) { $pref = 'empty_'; }
						$add_img .= "<td width='16' valign='middle'><img src=\"".$JLMS_CONFIG->get('live_site')."/components/com_joomla_lms/lms_images/treeview/".$pref."line.png\" width='16' height='16' alt='line' border='0' /></td>";
						$g ++;
					}
					$add_img .= "<td width='16' valign='middle'><img src=\"".$JLMS_CONFIG->get('live_site')."/components/com_joomla_lms/lms_images/treeview/sub".$row_path->tree_mode.".png\" width='16' height='16' border='0' alt='sub' /></td>";
					$max_tree_width = $max_tree_width - $g - 1;
				}
				echo $add_img; ?>
				<td valign="middle" align="center" width="16"><div align="center" style="vertical-align:middle;"><?php switch($row_path->step_type) {
					case 1: echo "<span id='tree_div_".$row_path->id."' style='text-align:center; cursor:pointer; vertical-align:middle;' onclick='Ex_Folder(".$row_path->id.",".$row_path->id.",true)'><img id='tree_img_".$row_path->id."' class='JLMS_png' src=\"".$JLMS_CONFIG->get('live_site')."/components/com_joomla_lms/lms_images/learnpath/chapter_collapse.png\" width='16' height='16' border='0' alt='chapter' /></span>";break;
					case 2:
						if ( isset($row_path->folder_flag) && ($row_path->folder_flag == 2) ) {
							echo "<span style='text-align:center;'><img class='JLMS_png' src=\"".$JLMS_CONFIG->get('live_site')."/components/com_joomla_lms/lms_images/files/file_zippack.png\" width='16' height='16' border='0' alt='zip package' /></span>";
						} else {
							echo "<span style='text-align:center;'><img class='JLMS_png' src=\"".$JLMS_CONFIG->get('live_site')."/components/com_joomla_lms/lms_images/files/".$row_path->file_icon.".png\" width='16' height='16' border='0' alt='file' /></span>";
						}
					break;
					case 3:	echo "<span style='text-align:center;'><img class='JLMS_png' src=\"".$JLMS_CONFIG->get('live_site')."/components/com_joomla_lms/lms_images/files/link_world.png\" width='16' height='16' border='0' alt='link' /></span>";break;
					case 4:	echo "<span style='text-align:center;'><img class='JLMS_png' src=\"".$JLMS_CONFIG->get('live_site')."/components/com_joomla_lms/lms_images/files/file_content.png\" width='16' height='16' border='0' alt='content' /></span>";break;
					case 5: echo "<span style='text-align:center;'><img class='JLMS_png' src=\"".$JLMS_CONFIG->get('live_site')."/components/com_joomla_lms/lms_images/toolbar/tlb_quiz.png\" width='16' height='16' border='0' alt='quiz' /></span>";break;
					case 6:	echo "<span style='text-align:center;'><img class='JLMS_png' src=\"".$JLMS_CONFIG->get('live_site')."/components/com_joomla_lms/lms_images/toolbar/tlb_scorm.png\" width='16' height='16' border='0' alt='scorm' /></span>";break;
					} ?>
				</div>
				</td>
				<td width="30%" align="left" valign="middle" <?php if ($max_tree_width > 0) { echo "colspan='".($max_tree_width + 1)."'";} ?>>
					<?php 
					
					if ($row_path->step_type == 1) {
						echo "<strong>".$row_path->doc_name."</strong>";
					} elseif ($row_path->step_type == 2) {
						if ($row_path->folder_flag == 2 && $row_path->file_id) {
							$link_download = sefRelToAbs("index.php?option=".$option."&amp;Itemid=".$Itemid."&amp;task=docs_view_zip&amp;course_id=".$course_id."&amp;id=".$row_path->item_id);
							echo '<a target="_blank" href="'.$link_download.'" title="'._JLMS_T_A_VIEW_ZIP_PACK.'">'.$row_path->doc_name.'</a>';
						} elseif ((!$row_path->folder_flag || $row_path->folder_flag == 3) && !$row_path->file_id) {
							if(!isset($row_path->is_link)){
								$link_download = sefRelToAbs("index.php?option=".$option."&amp;Itemid=".$Itemid."&amp;task=docs_view_content&amp;course_id=".$course_id."&amp;id=".$row_path->item_id);
								echo '<a target="_blank" href="'.$link_download.'" title="'._JLMS_T_A_VIEW_CONTENT.'">'.$row_path->doc_name.'</a>';
							}else{
								echo $row_path->doc_name;
							}
						} else {
							if(!isset($row_path->is_link)){
								$link_download = sefRelToAbs("index.php?option=".$option."&amp;Itemid=".$Itemid."&amp;task=get_document&amp;course_id=".$course_id."&amp;id=".$row_path->item_id."&amp;lpath_id=".$lpath_id);
								echo '<a href="'.$link_download.'" title="'._JLMS_T_A_DOWNLOAD.'">'.$row_path->doc_name.'</a>';
							}else{	
								echo $row_path->doc_name;
							}
						}
					} elseif ($row_path->step_type == 3) {
						echo '<a target="_blank" href="'.$row_path->link_href.'" title="'._JLMS_T_A_VIEW_LINK.'">'.$row_path->doc_name.'</a>';
					} elseif ($row_path->step_type == 4) {
						echo '<a href="'.sefRelToAbs("index.php?option=".$option."&amp;Itemid=".$Itemid."&amp;task=show_lp_content&amp;course_id=".$course_id."&amp;lpath_id=".$row_path->lpath_id."&amp;id=".$row_path->id).'" title="'._JLMS_T_A_VIEW_CONTENT.'">'.$row_path->doc_name.'</a>';
					} else { echo $row_path->doc_name; }
					?>
				</td>
				<td valign="middle" style="vertical-align:middle;"><?php if ($row_path->allow_up == 1) { echo JLMS_orderUpIcon( 1, $row_path->id, true, 'lpath_item_orderup'); } else { echo '&nbsp;';}?></td>
				<td valign="middle" style="vertical-align:middle;"><?php if ($row_path->allow_down == 1) { echo JLMS_orderDownIcon( 1, 3, $row_path->id, true, 'lpath_item_orderdown'); } else { echo '&nbsp;';}?></td>
				<td valign="middle" style="vertical-align:middle;" colspan="2">
					<?php 
//					if ($row_path->step_type != 1) { 
					?>
						<input type="text" name="order[]" size="5" value="<?php echo $row_path->ordering; ?>" class="inputbox" style="text-align: center" />
					<?php 
//					}
					?>
				</td>
				<td valign="middle" style="vertical-align:middle ">
					<?php 
					if ($row_path->step_type == 1) { 
					?>
						<a class="jlms_img_link" href="<?php echo $JLMS_CONFIG->getCfg('live_site')."/index.php?option=".$option."&amp;Itemid=".$Itemid."&amp;task=add_lpath_step&amp;course_id=".$row_path->course_id."&amp;id=".$row_path->lpath_id."&amp;parent=".$row_path->id;?>" title="<?php echo _JLMS_LPATH_LINK_ADDSTEP_TITLE;?>"><img class="JLMS_png" src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_add.png" width="16" height="16" border="0" alt="<?php echo _JLMS_LPATH_LINK_ADDSTEP_TITLE;?>" title="<?php echo _JLMS_LPATH_LINK_ADDSTEP_TITLE;?>" /></a>
					<?php 
					} else {
						echo '&nbsp;'; 	
					} 
					?>
				</td>
				<td valign="middle" style="vertical-align:middle ">
					<?php
						$cond_link = sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid&amp;task=lpath_step_cond&amp;course_id=$course_id&amp;lpath_id=$lpath_id&amp;id={$row_path->id}");
						$cond_descr = _JLMS_LPATH_FLWINDOW_NOCOND;
						$cond_title = _JLMS_LPATH_TITLE_FLWINDOW;
						$cond_img = 'btn_warning';
						if ($row_path->is_condition) {
							$cond_img = 'btn_cond_present';
							$y = 0;
							$is_cond_descr = false;
							$cond_descr = '<table cellpadding=0 cellspacing=0 border=0>';
							while ($y < count($conds)) {
								if ($conds[$y]->step_id == $row_path->id) {
									$ref_name = '';
									$u = 0;
									while ($u < count($lpath)) {
										if ($lpath[$u]->id == $conds[$y]->ref_step) {
											$ref_name = $lpath[$u]->step_name;
											break;
										}
										$u ++;
									}
									if(isset($conds[$y]->cond_time) && $conds[$y]->cond_time){
										$ref_name .= ' ('._JLMS_LPATH_CONDTYPE_SPENT.' '.$conds[$y]->cond_time.' '._JLMS_LPATH_CONDTYPE_TIME_SPENT_MIN.')';
									}
									$cond_descr .= '<tr><td>'._JLMS_LPATH_CONDTYPE_COMPLETE_W.'&nbsp;</td><td>'.$ref_name.'</td></tr>';
									$is_cond_descr = true;
								}
								$y ++;
							}
							$cond_descr .= '</table>';
							if (!$is_cond_descr) {
								$cond_descr = _JLMS_LPATH_FLWINDOW_NOCOND;
							}
						}
						$img_inside_tag = '<img class="JLMS_png" src="'.$JLMS_CONFIG->get('live_site').'/components/com_joomla_lms/lms_images/toolbar/'.$cond_img.'.png" width="16" height="16" border="0" alt="cond" />';
						echo JLMS_toolTip($cond_title, JLMS_txt2overlib($cond_descr), $img_inside_tag, $cond_link);
						?>
				</td>
				<td width="60%" valign="middle" style="vertical-align:middle; "><?php echo strlen($row_path->step_shortdescription)?$row_path->step_shortdescription:'&nbsp;'; ?></td>
			</tr>
			<?php
			$k = 3 - $k;
		} ?>
		</table>
			<input type="hidden" name="option" value="<?php echo $option;?>" />
			<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
			<input type="hidden" name="task" value="lpath_add_chapter" />
			<input type="hidden" name="id" value="<?php echo $lpath_id;?>" />
			<input type="hidden" name="course_id" value="<?php echo $course_id;?>" />
			<input type="hidden" name="row_id" value="0" />
			<input type="hidden" name="boxchecked" value="0" />
		</form>
<?php
		JLMS_TMPL::CloseTS();
		$link_new = ampReplace(sefRelToAbs("index.php?option=".$option."&amp;Itemid=".$Itemid."&amp;task=new_lpath_chapter&amp;id=".$lpath_id."&amp;course_id=".$course_id));
		$controls = array();
		//$controls[] = array('href' => $link_new, 'onclick' => "", 'title' => _JLMS_LPATH_LINK_NEW_CHAP, 'img' => 'add');
		$controls[] = array('href' => "javascript:submitbutton('add_lpath_step');", 'onclick' => "", 'title' => _JLMS_LPATH_LINK_ADDSTEP_TITLE, 'img' => 'add');
		$controls[] = array('href' => "javascript:submitbutton('lpath_item_delete');", 'onclick' => "", 'title' => _JLMS_LPATH_LINK_DEL_ITEM, 'img' => 'delete');
		$controls[] = array('href' => "javascript:submitbutton('lpath_item_edit');", 'onclick' => "", 'title' => _JLMS_LPATH_LINK_EDIT_ITEM, 'img' => 'edit');
		
		//$controls[] = array('href' => "spacer");
		
		
		JLMS_TMPL::ShowControlsFooter($controls, sefRelToAbs("index.php?option=".$option."&amp;Itemid=".$Itemid."&amp;task=learnpaths&amp;id=".$course_id));
		JLMS_TMPL::CloseMT();
	}

//this function is no longer used
function addChapterToLPath( $lpath_id, $course_id, $option, &$lists ) {
	global $Itemid; ?>
<script language="javascript" type="text/javascript">
<!--
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
	if ( (pressbutton == 'lpath_add_chapter') && (form.step_name.value == "") ) {
		alert( "<?php echo _JLMS_LPATH_ENTER_CHAP_NAME;?>" );
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
		$toolbar = array();
		$toolbar[] = array('btn_type' => 'save', 'btn_js' => "javascript:submitbutton('lpath_add_chapter');");
		$toolbar[] = array('btn_type' => 'cancel', 'btn_js' => "javascript:submitbutton('cancel_lpath_step');");
		JLMS_TMPL::ShowHeader('lpath', _JLMS_LPATH_TITLE_ADD_CHAP, $hparams, $toolbar);

		JLMS_TMPL::OpenTS();
?>
	<form action="<?php echo sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid");?>" method="post" name="adminForm" onsubmit="setgoood();">
		<table width="100%" cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td width="15%" valign="middle" style="vertical-align:middle"><?php echo _JLMS_ENTER_NAME;?></td>
				<td>
					<input size="40" class="inputbox" type="text" name="step_name" value="" />
				</td>
			</tr>
			<tr>
				<td valign="middle" style="vertical-align:middle"><br /><?php echo _JLMS_PLACE_IN;?></td>
				<td>
					<br /><?php echo $lists['lpath_chaps'];?>
				</td>
			</tr>
			<tr>
				<td align="left" valign="top" style="vertical-align:top "><br /><?php echo _JLMS_SHORT_DESCRIPTION;?></td>
				<td><br /><textarea class="inputbox" name="step_shortdescription" cols="50" rows="3"></textarea></td>
			</tr>
			<tr>
				<td colspan="2" align="left" style="text-align:left "><br /><?php echo _JLMS_DESCRIPTION;?></td>
			</tr>
			<tr>
				<td colspan="2">
				<?php JLMS_editorArea( 'editor2', '', 'step_description', '100%', '250', '40', '20' ) ; ?>
				</td>
			</tr>
		</table>
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
		<input type="hidden" name="task" value="lpath_add_chapter" />
		<input type="hidden" name="id" value="<?php echo $lpath_id;?>" />
		<input type="hidden" name="lpath_id" value="<?php echo $lpath_id;?>" />
		<input type="hidden" name="course_id" value="<?php echo $course_id;?>" />
	</form>
<?php
		JLMS_TMPL::CloseTS();
		JLMS_TMPL::CloseMT();
	}

	
	// to do: 'alt' + 'title' dlya folderov i failov
	// + kogda $lists['lpath_chaps'] menyaetsa - javascript kotoryi peregrugaet strani4ku (shtoby menyalis' $lists['lpath_order'] )
	// + dobavit' osnovnoe menu na etu strani4ku
	function addItemToLPath( $lpath_id, $course_id, $option, &$my_documents, &$my_links, &$my_quizzes, &$my_scorms, &$lists, $parent ) {
		global $Itemid, $JLMS_CONFIG;?>
<script language="javascript" type="text/javascript">
<!--
function jlms_get_kol_selected(fff) {
	var kol_sel=0;
	selItem=fff['cid[]'];
	var rrr='';
	if (selItem) {
		if (selItem.length) { var i;
			for (i = 0; i<selItem.length; i++) {
				if (selItem[i].checked) { kol_sel++; }
			}
		} else if (selItem.checked) { kol_sel++; }
	}
	return kol_sel;
}
function submitbutton_doc(pressbutton) {
	var form = document.docForm;
	var kol_docs = jlms_get_kol_selected(form);
	if ( (pressbutton == 'lpath_add_doc') && (kol_docs == 0) ) {
		alert( "<?php echo _JLMS_LPATH_SELECT_DOCS;?>" );
	} else {
		form.task.value = pressbutton;
		form.submit();
	}
}
function submitbutton_link(pressbutton) {
	var form = document.linkForm;
	var kol_links = jlms_get_kol_selected(form);
	if ( (pressbutton == 'lpath_add_link') && (kol_links == 0) ) {
		alert( "<?php echo _JLMS_LPATH_SELECT_LINKS;?>" );
	} else {
		form.task.value = pressbutton;
		form.submit();
	}
}
function submitbutton_quiz(pressbutton) {
	var form = document.quizForm;
	var kol_quizzes = jlms_get_kol_selected(form);
	if ( (pressbutton == 'lpath_add_quiz') && (kol_quizzes == 0) ) {
		alert( "<?php echo _JLMS_LPATH_SELECT_QUIZZES;?>" );
	} else {
		form.task.value = pressbutton;
		form.submit();
	}
}
function submitbutton_scorm(pressbutton) {
	var form = document.scormForm;
	var kol_scorms = jlms_get_kol_selected(form);
	if ( (pressbutton == 'lpath_add_scorm') && (kol_scorms == 0) ) {
		alert( "<?php echo _JLMS_LPATH_SELECT_SCORMS;?>" );
	} else {
		form.task.value = pressbutton;
		form.submit();
	}
}

function submitbutton_chap(pressbutton) {
	var form = document.chapForm;
	if ( (pressbutton == 'lpath_add_chapter') && (form.step_name.value == '') ) {
		alert( "<?php echo _JLMS_LPATH_ENTER_CHAP_NAME;?>" );
	} else {
		form.task.value = pressbutton;
		form.submit();
	}
}
function submitbutton_content(pressbutton) {
	var form = document.contentForm;
	try {
		form.onsubmit();
	} catch(e) {
		//alert(e);
	}
	if ( (pressbutton == 'lpath_add_content') && (form.step_name.value == '') ) {
		alert( "<?php echo _JLMS_LPATH_ENTER_CONTENT_NAME;?>" );
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
		JLMS_TMPL::ShowHeader('lpath', '', $hparams);
		JLMS_TMPL::OpenTS();
?>
		<form action="<?php echo sefRelToAbs("index.php?option=".$option."&amp;Itemid=".$Itemid);?>" method="post" name="swapParent">
			<input name="parent" type="hidden" value="<?php echo $parent;?>" />
			<input type="hidden" name="option" value="<?php echo $option;?>" />
			<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
			<input type="hidden" name="task" value="add_lpath_step" />
			<input type="hidden" name="id" value="<?php echo $lpath_id;?>" />
			<input type="hidden" name="lpath_id" value="<?php echo $lpath_id;?>" />
			<input type="hidden" name="course_id" value="<?php echo $course_id;?>" />
		</form>
	<?php echo JLMSCSS::h2(_JLMS_LPATH_TITLE_ADD_ITEMS);?>
<?php
		$tabs = new JLMSTabs(0);
		echo $tabs->startPane("JLMS");
		echo $tabs->startTab(_JLMS_LPATH_CONTENT,"jlmstab6");
		echo JLMSCSS::h2(_JLMS_LPATH_TITLE_ADD_CONTENT);
?>
	<table width="100%" cellpadding="0" cellspacing="0" border="0" class="jlms_table_no_borders">
		<tr>
			<td align="right">
			<?php $toolbar = array();
			$toolbar[] = array('btn_type' => 'save', 'btn_js' => "javascript:submitbutton_content('lpath_add_content');");
			$toolbar[] = array('btn_type' => 'cancel', 'btn_js' => "javascript:submitbutton_content('cancel_lpath_step');");
			echo JLMS_ShowToolbar($toolbar); ?>
			</td>
		</tr>
	</table>
				<form action="<?php echo sefRelToAbs("index.php?option=".$option."&amp;Itemid=".$Itemid);?>" method="post" name="contentForm" onsubmit="setgood();">
					<table width="100%" cellpadding="0" cellspacing="0" border="0" class="jlms_item_properties">
						<tr>
							<td width="15%"><br /><?php echo _JLMS_ENTER_NAME;?></td>
							<td>
								<br /><input type="text" name="step_name" style="width:260px" value="" />
							</td>
						</tr>
						<tr>
							<td width="15%"><br /><?php echo _JLMS_PLACE_IN;?></td>
							<td><br /><?php echo $lists['lpath_chaps1'];?></td>
						</tr>
						<tr>
							<td><br /><?php echo _JLMS_ORDERING;?></td>
							<td colspan="2"><br /><?php echo $lists['lpath_order'];?></td>
						</tr>
						<tr>
							<td align="left" valign="top" style="vertical-align:top "><br /><?php echo _JLMS_SHORT_DESCRIPTION;?></td>
							<td colspan="2"><br /><textarea class="inputbox" name="step_shortdescription" cols="50" rows="3"></textarea></td>
						</tr>
						<tr>
							<td colspan="3" align="left" style="text-align:left "><br /><?php echo _JLMS_DESCRIPTION;?></td>
						</tr>
						<tr>
							<td colspan="3">
							<?php JLMS_editorArea( 'editor1', '', 'step_description', '100%', '250', '40', '20' ) ; ?>
							</td>
						</tr>
					</table>
					<input type="hidden" name="option" value="<?php echo $option;?>" />
					<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
					<input type="hidden" name="task" value="lpath_add_content" />
					<input type="hidden" name="id" value="<?php echo $lpath_id;?>" />
					<input type="hidden" name="lpath_id" value="<?php echo $lpath_id;?>" />
					<input type="hidden" name="course_id" value="<?php echo $course_id;?>" />
					</form>
<?php
		echo $tabs->endTab();
		echo $tabs->startTab(_JLMS_LPATH_CHAPTER,"jlmstab5");
		echo JLMSCSS::h2(_JLMS_LPATH_TITLE_ADD_CHAP);
?>
	<table width="100%" cellpadding="0" cellspacing="0" border="0" class="jlms_table_no_borders">
		<tr>
			<td align="right">
			<?php $toolbar = array();
			$toolbar[] = array('btn_type' => 'save', 'btn_js' => "javascript:submitbutton_chap('lpath_add_chapter');");
			$toolbar[] = array('btn_type' => 'cancel', 'btn_js' => "javascript:submitbutton_chap('cancel_lpath_step');");
			echo JLMS_ShowToolbar($toolbar); ?>
			</td>
		</tr>
	</table>
					<form action="<?php echo sefRelToAbs("index.php?option=".$option."&amp;Itemid=".$Itemid);?>" method="post" name="chapForm">
					<table width="100%" cellpadding="0" cellspacing="0" border="0" class="jlms_item_properties">
						<tr>
							<td width="15%"><br /><?php echo _JLMS_ENTER_NAME;?></td>
							<td>
								<br /><input type="text" name="step_name" style="width:260px" value="" />
							</td>
						</tr>
						<tr>
							<td width="15%"><br /><?php echo _JLMS_PLACE_IN;?></td>
							<td><br /><?php echo $lists['lpath_chaps1'];?></td>
						</tr>
						<tr>
							<td><br /><?php echo _JLMS_ORDERING;?></td>
							<td><br /><?php echo $lists['lpath_order'];?></td>
						</tr>
					</table>
					<input type="hidden" name="option" value="<?php echo $option;?>" />
					<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
					<input type="hidden" name="task" value="lpath_add_chapter" />
					<input type="hidden" name="id" value="<?php echo $lpath_id;?>" />
					<input type="hidden" name="lpath_id" value="<?php echo $lpath_id;?>" />
					<input type="hidden" name="course_id" value="<?php echo $course_id;?>" />
					</form>
<?php
		echo $tabs->endTab();

	if (!empty($my_documents)) {
		echo $tabs->startTab(_JLMS_HEAD_DOCS_STR,"jlmstab1");
		echo JLMSCSS::h2(_JLMS_LPATH_TITLE_ADD_DOCS);?>
	<table width="100%" cellpadding="0" cellspacing="0" border="0" class="jlms_table_no_borders">
		<tr>
			<td align="right">
			<?php $toolbar = array();
			$toolbar[] = array('btn_type' => 'save', 'btn_js' => "javascript:submitbutton_doc('lpath_add_doc');");
			$toolbar[] = array('btn_type' => 'cancel', 'btn_js' => "javascript:submitbutton_doc('cancel_lpath_step');");
			echo JLMS_ShowToolbar($toolbar); ?>
			</td>
		</tr>
	</table>
				<form action="<?php echo sefRelToAbs("index.php?option=".$option."&amp;Itemid=".$Itemid);?>" method="post" name="docForm">
					<?php $max_tree_width = 0; $max_tree_width1 = 0; if (isset($my_documents[0])) {$max_tree_width = $my_documents[0]->tree_max_width;} $max_tree_width1 = $max_tree_width;?>
					<table width="100%" cellpadding="0" cellspacing="0" border="0" class="<?php echo JLMSCSS::_('jlmslist');?>">
						<tr>
							<<?php echo JLMSCSS::tableheadertag();?> width="1%" class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center">#</<?php echo JLMSCSS::tableheadertag();?>>
							<<?php echo JLMSCSS::tableheadertag();?> width="1%" class="<?php echo JLMSCSS::_('sectiontableheader');?>">&nbsp;</<?php echo JLMSCSS::tableheadertag();?>>
							<<?php echo JLMSCSS::tableheadertag();?> width="<?php echo (($max_tree_width + 1));?>%" class="<?php echo JLMSCSS::_('sectiontableheader');?>" colspan="<?php echo ($max_tree_width + 1);?>">&nbsp;</<?php echo JLMSCSS::tableheadertag();?>>
							<<?php echo JLMSCSS::tableheadertag();?> width="50%" class="<?php echo JLMSCSS::_('sectiontableheader');?>"><?php echo _JLMS_LPATH_TBL_HEAD_NAME_DOCS;?></<?php echo JLMSCSS::tableheadertag();?>>
							<<?php echo JLMSCSS::tableheadertag();?> width="<?php echo (48 - ($max_tree_width + 1));?>%" class="<?php echo JLMSCSS::_('sectiontableheader');?>"><span style="display:block; width:150px"><?php echo _JLMS_LPATH_TBL_HEAD_DESCR_DOCS;?></span></<?php echo JLMSCSS::tableheadertag();?>>
						</tr>
					<?php
					$k = 1;
					$tree_modes = array();
					for ($i=0, $n=count($my_documents); $i < $n; $i++) {
						$row = $my_documents[$i];
						$max_tree_width = $row->tree_max_width;
						$link 	= "index.php?option=".$option."&amp;Itemid=".$Itemid."&amp;task=get_document&amp;course_id=".$course_id."&amp;id=".$row->id;
						$checked = '<input type="checkbox" name="cid[]" value="'.$row->id.'" />';?>
						<tr class="<?php echo JLMSCSS::_('sectiontableentry'.$k); ?>">
							<td align="center"><?php echo ( $i + 1 ); ?></td>
							<td align="center" valign="middle"><?php if (!$row->folder_flag || $row->folder_flag == 2 || $row->folder_flag == 3) { echo $checked; } else { echo '&nbsp;'; }?></td>
							<?php $add_img = '';
							if ($row->tree_mode_num) {
								$g = 0;
								$tree_modes[$row->tree_mode_num - 1] = $row->tree_mode;
								while ($g < ($row->tree_mode_num - 1)) {
									$pref = '';
									if (isset($tree_modes[$g]) && ($tree_modes[$g] == 2) ) { $pref = 'empty_'; }
									$add_img .= "<td width='16' valign='middle'><img class='JLMS_png' src=\"".$JLMS_CONFIG->get('live_site')."/components/com_joomla_lms/lms_images/treeview/".$pref."line.png\" width='16' height='16' /></td>";
									$g ++;
								}
								$add_img .= "<td width='16' valign='middle'><img class='JLMS_png' src=\"".$JLMS_CONFIG->get('live_site')."/components/com_joomla_lms/lms_images/treeview/sub".$row->tree_mode.".png\" width='16' height='16' /></td>";
								$max_tree_width = $max_tree_width - $g - 1;
							}
							echo $add_img;?>
							<td align="center" valign="middle" style="vertical-align:middle " width='16'>
							<?php if ($row->folder_flag == 1) {
								echo "<span style='text-align:center; font-weight:bold; vertical-align:middle;'><img class='JLMS_png' src=\"".$JLMS_CONFIG->get('live_site')."/components/com_joomla_lms/lms_images/folder.png\" width='16' height='16' alt='"._JLMS_LPATH_DOC_ALT_FOLDER."' /></span>";
							} else {
								echo "<span style='text-align:center;font-weight:bold; vertical-align:middle;'><img class='JLMS_png' src=\"".$JLMS_CONFIG->get('live_site')."/components/com_joomla_lms/lms_images/files/".$row->file_icon.".png\" width='16' height='16' alt='"._JLMS_LPATH_DOC_ALT_FILE."' /></span>";
							}?>
							</td>
							<td align="left" valign="middle" <?php if ($max_tree_width > 0) { echo "colspan='".($max_tree_width + 1)."'";} ?> width="35%">
							<?php if ($row->folder_flag || (!$row->folder_flag && !$row->file_id)) {
								echo '<strong>'.$row->doc_name.'</strong>';
							} else { ?>
								<?php /*<a href="<?php echo sefRelToAbs($link);?>" title="<?php echo _JLMS_LPATH_LINK_DOC_DOWNLOAD;?>"> */
									echo $row->doc_name;
								/* </a> */
								} ?>
							</td>
							<td><?php
							$doc_descr = trim(strip_tags($row->doc_description));
							if (strlen($doc_descr) > 75) {
								$doc_descr = substr($doc_descr, 0, 75)."...";
							}
							echo $doc_descr?$doc_descr:'&nbsp;';?>
							</td>
						</tr>
						<?php
						$k = 3 - $k;
					}?>
					</table>
					<table width="100%" cellpadding="0" cellspacing="0" border="0" class="jlms_item_properties">
						<tr>
							<td width="15%"><?php echo _JLMS_PLACE_IN;?></td>
							<td><?php echo $lists['lpath_chaps1'];?></td>
						</tr>
						<tr>
							<td><br /><?php echo _JLMS_ORDERING;?></td>
							<td><br /><?php echo $lists['lpath_order'];?></td>
						</tr>
					</table>
					<input type="hidden" name="option" value="<?php echo $option;?>" />
					<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
					<input type="hidden" name="task" value="lpath_add_doc" />
					<input type="hidden" name="id" value="<?php echo $lpath_id;?>" />
					<input type="hidden" name="lpath_id" value="<?php echo $lpath_id;?>" />
					<input type="hidden" name="course_id" value="<?php echo $course_id;?>" />
					</form>
<?php
			echo $tabs->endTab();

	}
	if (!empty($my_links)) {
			echo $tabs->startTab(_JLMS_HEAD_LINK_STR,"jlmstab2");
			echo JLMSCSS::h2(_JLMS_LPATH_TITLE_ADD_LINKS);
?>
	<table width="100%" cellpadding="0" cellspacing="0" border="0" class="jlms_table_no_borders">
		<tr>
			<td align="right">
			<?php $toolbar = array();
			$toolbar[] = array('btn_type' => 'save', 'btn_js' => "javascript:submitbutton_link('lpath_add_link');");
			$toolbar[] = array('btn_type' => 'cancel', 'btn_js' => "javascript:submitbutton_link('cancel_lpath_step');");
			echo JLMS_ShowToolbar($toolbar); ?>
			</td>
		</tr>
	</table>
					<form action="<?php echo sefRelToAbs("index.php?option=".$option."&amp;Itemid=".$Itemid);?>" method="post" name="linkForm">
					<table width="100%" cellpadding="0" cellspacing="0" border="0" class="<?php echo JLMSCSS::_('jlmslist');?>">
						<tr>
							<<?php echo JLMSCSS::tableheadertag();?> width="1%" class="<?php echo JLMSCSS::_('sectiontableheader');?>">#</<?php echo JLMSCSS::tableheadertag();?>>
							<<?php echo JLMSCSS::tableheadertag();?> width="1%" class="<?php echo JLMSCSS::_('sectiontableheader');?>">&nbsp;</<?php echo JLMSCSS::tableheadertag();?>>
							<<?php echo JLMSCSS::tableheadertag();?> width="50%" class="<?php echo JLMSCSS::_('sectiontableheader');?>"><?php echo _JLMS_LPATH_TBL_HEAD_NAME_LINKS;?></<?php echo JLMSCSS::tableheadertag();?>>
							<<?php echo JLMSCSS::tableheadertag();?> width="48%" class="<?php echo JLMSCSS::_('sectiontableheader');?>"><span style="display:block; width:150px"><?php echo _JLMS_LPATH_TBL_HEAD_DESCR_LINKS;?></span></<?php echo JLMSCSS::tableheadertag();?>>
						</tr>
					<?php
					$_JLMS_PLUGINS = & JLMSFactory::getPlugins();
					$_JLMS_PLUGINS->loadBotGroup('content');
					for ($i = 0, $n = count($my_links); $i < $n; $i++) {
						$plugin_result_array = $_JLMS_PLUGINS->trigger('onContentProcess', array(&$my_links[$i]->link_href));
						$plugin_result_array = $_JLMS_PLUGINS->trigger('onContentProcess', array(&$my_links[$i]->link_name));
					}

					$k = 1;
					for ($i=0, $n=count($my_links); $i < $n; $i++) {
						$row = $my_links[$i];
						$link = $row->link_href;//'javascript:void(0);';//"index.php?option=".$option."&Itemid=".$Itemid."&task=get_file&id=".$row->file_id;
						$checked = '<input type="checkbox" name="cid[]" value="'.$row->id.'" />';?>
						<tr class="<?php echo JLMSCSS::_('sectiontableentry'.$k);?>">
							<td align="center"><?php echo ( $i + 1 ); ?></td>
							<td><?php echo $checked; ?></td>
							<td>
								<a target="_blank" href="<?php echo sefRelToAbs($link);?>" title="<?php echo _JLMS_LPATH_VIEW_LINK;?>">
									<?php echo $row->link_name;?>
								</a>
							</td>
							<td><?php echo trim($row->link_description)?$row->link_description:'&nbsp;';?></td>
						</tr>
						<?php
						$k = 3 - $k;
					}?>
					</table>
					<table width="100%" cellpadding="0" cellspacing="0" border="0" class="jlms_item_properties">
						<tr>
							<td width="15%"><?php echo _JLMS_PLACE_IN;?></td>
							<td><?php echo $lists['lpath_chaps1'];?></td>
						</tr>
						<tr>
							<td><br /><?php echo _JLMS_ORDERING;?></td>
							<td><br /><?php echo $lists['lpath_order'];?></td>
						</tr>
					</table>
					<input type="hidden" name="option" value="<?php echo $option;?>" />
					<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
					<input type="hidden" name="task" value="lpath_add_link" />
					<input type="hidden" name="id" value="<?php echo $lpath_id;?>" />
					<input type="hidden" name="lpath_id" value="<?php echo $lpath_id;?>" />
					<input type="hidden" name="course_id" value="<?php echo $course_id;?>" />
					</form>
<?php
				echo $tabs->endTab();
	}
	if (!empty($my_quizzes)) {
				echo $tabs->startTab(_JLMS_HEAD_QUIZ_STR,"jlmstab3");
				echo JLMSCSS::h2(_JLMS_LPATH_TITLE_ADD_QUIZZES);
?>
	<table width="100%" cellpadding="0" cellspacing="0" border="0" class="jlms_table_no_borders">
		<tr>
			<td align="right">
			<?php $toolbar = array();
			$toolbar[] = array('btn_type' => 'save', 'btn_js' => "javascript:submitbutton_quiz('lpath_add_quiz');");
			$toolbar[] = array('btn_type' => 'cancel', 'btn_js' => "javascript:submitbutton_quiz('cancel_lpath_step');");
			echo JLMS_ShowToolbar($toolbar); ?>
			</td>
		</tr>
	</table>
					<form action="<?php echo sefRelToAbs("index.php?option=".$option."&amp;Itemid=".$Itemid);?>" method="post" name="quizForm">
					<table width="100%" cellpadding="0" cellspacing="0" border="0" class="<?php echo JLMSCSS::_('jlmslist');?>">
						<tr>
							<<?php echo JLMSCSS::tableheadertag();?> width="1%" class="<?php echo JLMSCSS::_('sectiontableheader');?>">#</<?php echo JLMSCSS::tableheadertag();?>>
							<<?php echo JLMSCSS::tableheadertag();?> width="1%" class="<?php echo JLMSCSS::_('sectiontableheader');?>">&nbsp;</<?php echo JLMSCSS::tableheadertag();?>>
							<<?php echo JLMSCSS::tableheadertag();?> width="50%" class="<?php echo JLMSCSS::_('sectiontableheader');?>"><?php echo _JLMS_LPATH_TBL_HEAD_NAME_QUIZ;?></<?php echo JLMSCSS::tableheadertag();?>>
							<<?php echo JLMSCSS::tableheadertag();?> width="48%" class="<?php echo JLMSCSS::_('sectiontableheader');?>"><span style="display:block; width:150px"><?php echo _JLMS_LPATH_TBL_HEAD_CAT_QUIZ;?></span></<?php echo JLMSCSS::tableheadertag();?>>
						</tr>
					<?php
					$k = 1;
					for ($i=0, $n=count($my_quizzes); $i < $n; $i++) {
						$row = $my_quizzes[$i];
						// (WARNING) if link will be changed, don't forgot to change target of <a> element
						$link = 'javascript:void(0);';//$row->link_href;//"index.php?option=".$option."&Itemid=".$Itemid."&task=get_file&id=".$row->file_id;
						$checked = '<input type="checkbox" name="cid[]" value="'.$row->c_id.'" />';?>
						<tr class="<?php echo JLMSCSS::_('sectiontableentry'.$k); ?>">
							<td align="center"><?php echo ( $i + 1 ); ?></td>
							<td><?php echo $checked; ?></td>
							<td>
								<a href="<?php echo sefRelToAbs($link);?>">
									<?php echo $row->c_title;?>
								</a>
							</td>
							<td><?php echo trim($row->c_category)?$row->c_category:'&nbsp;';?></td>
						</tr>
						<?php
						$k = 3 - $k;
					}?>
					</table>
					<table width="100%" cellpadding="0" cellspacing="0" border="0" class="jlms_item_properties">
						<tr>
							<td width="15%"><?php echo _JLMS_PLACE_IN;?></td>
							<td><?php echo $lists['lpath_chaps1'];?></td>
						</tr>
						<tr>
							<td><br /><?php echo _JLMS_ORDERING;?></td>
							<td><br /><?php echo $lists['lpath_order'];?></td>
						</tr>
					</table>
					<input type="hidden" name="option" value="<?php echo $option;?>" />
					<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
					<input type="hidden" name="task" value="lpath_add_quiz" />
					<input type="hidden" name="id" value="<?php echo $lpath_id;?>" />
					<input type="hidden" name="lpath_id" value="<?php echo $lpath_id;?>" />
					<input type="hidden" name="course_id" value="<?php echo $course_id;?>" />
					</form>
<?php
			echo $tabs->endTab();
	}
	if (!empty($my_scorms)) {
			echo $tabs->startTab(_JLMS_LPATH_SCORM_OBJECTS,"jlmstab4");
			echo JLMSCSS::h2(_JLMS_LPATH_TITLE_ADD_SCORMS);
?>
	<table width="100%" cellpadding="0" cellspacing="0" border="0" class="jlms_table_no_borders">
		<tr>
			<td align="right">
			<?php $toolbar = array();
			$toolbar[] = array('btn_type' => 'save', 'btn_js' => "javascript:submitbutton_scorm('lpath_add_scorm');");
			$toolbar[] = array('btn_type' => 'cancel', 'btn_js' => "javascript:submitbutton_scorm('cancel_lpath_step');");
			echo JLMS_ShowToolbar($toolbar); ?>
			</td>
		</tr>
	</table>
					<form action="<?php echo sefRelToAbs("index.php?option=".$option."&amp;Itemid=".$Itemid);?>" method="post" name="scormForm">
					<table width="100%" cellpadding="0" cellspacing="0" border="0" class="<?php echo JLMSCSS::_('jlmslist');?>">
						<tr>
							<<?php echo JLMSCSS::tableheadertag();?> width="1%" class="<?php echo JLMSCSS::_('sectiontableheader');?>">#</<?php echo JLMSCSS::tableheadertag();?>>
							<<?php echo JLMSCSS::tableheadertag();?> width="1%" class="<?php echo JLMSCSS::_('sectiontableheader');?>">&nbsp;</<?php echo JLMSCSS::tableheadertag();?>>
							<<?php echo JLMSCSS::tableheadertag();?> width="98%" class="<?php echo JLMSCSS::_('sectiontableheader');?>"><?php echo _JLMS_LPATH_SCORM_OBJECT;?></<?php echo JLMSCSS::tableheadertag();?>>
						</tr>
					<?php
					$k = 1;
					for ($i=0, $n=count($my_scorms); $i < $n; $i++) {
						$row = $my_scorms[$i];
						// (WARNING) if link will be changed, don't forgot to change target of <a> element
						$link = 'javascript:void(0);';//$row->link_href;//"index.php?option=".$option."&Itemid=".$Itemid."&task=get_file&id=".$row->file_id;
						$checked = '<input type="checkbox" name="cid[]" value="'.$row->id.'" />';?>
						<tr class="<?php echo JLMSCSS::_('sectiontableentry'.$k); ?>">
							<td align="center"><?php echo ( $i + 1 ); ?></td>
							<td><?php if(!isset($row->is_link)) echo $checked; ?></td>
							<td>
							
							<?php if(!isset($row->is_link)) {?>
								<a href="<?php echo sefRelToAbs($link);?>">
							<?php }?>	
									<?php  echo $row->lpath_name;?>
									
							<?php if(!isset($row->is_link)) {?>		
								</a>
							<?php }?>	
							</td>
						</tr>
						<?php
						$k = 3 - $k;
					}?>
					</table>
					<table width="100%" cellpadding="0" cellspacing="0" border="0" class="jlms_item_properties">
						<tr>
							<td width="15%"><?php echo _JLMS_PLACE_IN;?></td>
							<td><?php echo $lists['lpath_chaps1'];?></td>
						</tr>
						<tr>
							<td><br /><?php echo _JLMS_ORDERING;?></td>
							<td><br /><?php echo $lists['lpath_order'];?></td>
						</tr>
					</table>
					<input type="hidden" name="option" value="<?php echo $option;?>" />
					<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
					<input type="hidden" name="task" value="lpath_add_scorm" />
					<input type="hidden" name="id" value="<?php echo $lpath_id;?>" />
					<input type="hidden" name="lpath_id" value="<?php echo $lpath_id;?>" />
					<input type="hidden" name="course_id" value="<?php echo $course_id;?>" />
					</form>
<?php
		echo $tabs->endTab();
	}
		echo $tabs->endPane();
		JLMS_TMPL::CloseTS();
		JLMS_TMPL::CloseMT();
	}

	function showStepConditions( $course_id, $lpath_id, $step_id, $option, &$conds, &$lists, $step_name ) {
		global $Itemid, $JLMS_CONFIG; ?>
<script language="javascript" type="text/javascript">
<!--
function submitbutton_del(pressbutton) {
	var form = document.adminForm;
	if (form.boxchecked.value == '0') {
		alert('<?php echo _JLMS_ALERT_SELECT_ITEM;?>');
	} else {
		form.task.value = pressbutton;
		form.submit();
	}
}
function submitbutton_add(pressbutton) {
	var form = document.newcondForm;
	form.task.value = pressbutton;
	form.submit();
}
//-->
</script>
<?php
		JLMS_TMPL::OpenMT();

		$hparams = array();
		$toolbar = array();
		$toolbar[] = array('btn_type' => 'back', 'btn_js' => sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid&amp;task=compose_lpath&amp;course_id=$course_id&amp;id=$lpath_id"));
		JLMS_TMPL::ShowHeader('lpath', '', $hparams, $toolbar);

		JLMS_TMPL::OpenTS();
?>
<?php echo JLMSCSS::h2(_JLMS_LPATH_TITLE_CONDITIONS.$step_name);?>
	<form action="<?php echo sefRelToAbs("index.php?option=".$option."&amp;Itemid=".$Itemid);?>" method="post" name="adminForm">
		<table width="100%" cellpadding="0" cellspacing="0" border="0" class="<?php echo JLMSCSS::_('jlmslist');?>">
			<tr>
				<<?php echo JLMSCSS::tableheadertag();?> width="20" class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center">#</<?php echo JLMSCSS::tableheadertag();?>>
				<<?php echo JLMSCSS::tableheadertag();?> width="20" class="<?php echo JLMSCSS::_('sectiontableheader');?>">&nbsp;</<?php echo JLMSCSS::tableheadertag();?>>
				<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>">&nbsp;</<?php echo JLMSCSS::tableheadertag();?>>
				<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>"><?php echo _JLMS_LPATH_COND_REF_STEP;?></<?php echo JLMSCSS::tableheadertag();?>>
			</tr>
		<?php
		$k = 1;
		for ($i=0, $n=count($conds); $i < $n; $i++) {
			$row_cond = $conds[$i];
			$checked = mosHTML::idBox( $i, $row_cond->id);?>
			<tr id="tree_row_<?php echo $row_cond->id;?>" class="<?php echo JLMSCSS::_('sectiontableentry'.$k); ?>">
				<td align="center" valign="middle"><?php echo ( $i + 1 ); ?></td>
				<td valign="middle"><?php echo $checked; ?></td>
				<td valign="middle"><?php echo _JLMS_LPATH_CONDTYPE_COMPLETE;?></td>
				<td valign="middle" style="vertical-align:middle ">
					<?php 
					if(isset($row_cond->cond_time) && $row_cond->cond_time){
						$row_cond->ref_step_name .= ' ('._JLMS_LPATH_CONDTYPE_SPENT . ' ' . $row_cond->cond_time . ' ' . _JLMS_LPATH_CONDTYPE_TIME_SPENT_MIN . ')';
					}
					echo $row_cond->ref_step_name;
					?>
				</td>
			</tr>
			<?php
			$k = 3 - $k;
		}?>
		</table>
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
		<input type="hidden" name="task" value="lpath_item_delete" />
		<input type="hidden" name="id" value="<?php echo $step_id;?>" />
		<input type="hidden" name="step_id" value="<?php echo $step_id;?>" />
		<input type="hidden" name="lpath_id" value="<?php echo $lpath_id;?>" />
		<input type="hidden" name="course_id" value="<?php echo $course_id;?>" />
		<input type="hidden" name="boxchecked" value="0" />
	</form>
	<form action="<?php echo sefRelToAbs("index.php?option=".$option."&ampItemid=".$Itemid);?>" method="post" name="newcondForm">
<?php
		echo '<table cellpadding="0" cellspacing="0" border="0" width="100%" class="jlms_table_no_borders">';
		$controls = array();
		$controls[] = array('href' => "javascript:submitbutton_del('lpath_cond_delete');", 'title' => _JLMS_LPATH_LINK_DEL_COND, 'img' => 'buttons_22/btn_delete_22.png');
		$controls[] = array('href' => "javascript:submitbutton_add('lpath_save_cond');", 'title' => _JLMS_LPATH_LINK_NEW_COND, 'img' => 'buttons_22/btn_add_22.png');
		$custom = _JLMS_LPATH_CONDTYPE_COMPLETE.'&nbsp;'.$lists['lpath_steps'];
		if($JLMS_CONFIG->get('enable_timetracking')){
			$custom .= '&nbsp;'._JLMS_LPATH_CONDTYPE_AND_SPENT.': <input type="text" name="cond_time" value="" size="5" class="inputbox" style="text-align: center;" /> <small>min</small>';
		}
		$controls[] = array('href' => '', 'title' => '', 'img' => '', 'custom' => $custom );
		JLMS_TMPL::ShowControlsFooter($controls, '', false, true);
		echo '</table>';
/*
		<table width="100%" cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td>
					<table width="100%" cellpadding="0" cellspacing="0" border="0"><tr>
						<td align="center" valign="middle" width="16"><a href="javascript:submitbutton_del('lpath_cond_delete');" title="<?php echo _JLMS_LPATH_LINK_DEL_COND;?>"><img class="JLMS_png" src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_remove.png" width="16" height="16" border="0" alt="<?php echo _JLMS_LPATH_LINK_DEL_COND;?>" title="<?php echo _JLMS_LPATH_LINK_DEL_COND;?>" /></a></td>
						<td align="center" valign="middle" width="16"><a href="javascript:submitbutton_add('lpath_save_cond');" title="<?php echo _JLMS_LPATH_LINK_NEW_COND;?>"><img class="JLMS_png" src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_add.png" width="16" height="16" border="0" alt="<?php echo _JLMS_LPATH_LINK_NEW_COND;?>" title="<?php echo _JLMS_LPATH_LINK_NEW_COND;?>" /></a></td>
						<td align="left" valign="middle">
							&nbsp;<?php echo _JLMS_LPATH_CONDTYPE_COMPLETE. "&nbsp;" . $lists['lpath_steps']; ?>
							&nbsp;<?php echo _JLMS_LPATH_CONDTYPE_AND_SPENT;?>: <input type="text" name="cond_time" value="" size="5" class="inputbox" style="text-align: center;" /> <small>min</small>
						</td>
					</tr></table>
				</td>
			</tr>
		</table>
*/ ?>
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
		<input type="hidden" name="task" value="lpath_save_cond" />
		<input type="hidden" name="id" value="<?php echo $step_id;?>" />
		<input type="hidden" name="step_id" value="<?php echo $step_id;?>" />
		<input type="hidden" name="lpath_id" value="<?php echo $lpath_id;?>" />
		<input type="hidden" name="course_id" value="<?php echo $course_id;?>" />
	</form>
<?php
	JLMS_TMPL::CloseTS();
	JLMS_TMPL::CloseMT();
	}
}
?>