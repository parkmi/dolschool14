<?php
/**
* joomla_lms.gradebook.html.php
* (c) JoomaLMS eLearning Software http://www.joomlalms.com/
**/

// no direct access
defined( '_JLMS_EXEC' ) or die( 'Restricted access' );

class JLMS_gradebook_html {

	function showListPath( $rows, $lists, $option, $course_id, $Itemid, $id ) {
		global $Itemid, $my, $JLMS_CONFIG;
		
		
		JLMS_TMPL::OpenMT();
		
		$hparams = array();
		$toolbar = array();
		$toolbar[] = array('btn_type' => 'back', 'btn_js' => sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid&amp;task=gradebook&amp;id=$course_id"));
		JLMS_TMPL::ShowHeader('gradebook', _CONFIGURE_COURSE_COMPLETION, $hparams, $toolbar);
		
		JLMS_TMPL::OpenTS();
		
?>
		<script language="javascript" type="text/javascript">
			function check_selectbox() {
				var form = document.adminForm;	
				if (form.learn_path_id.value == 0) {
					alert( "<?php echo _JLMS_ALERT_SELECT_ITEM;?>" );
				}
				else {
					form.submit();
				}
			}
		</script>

		<form action="<?php echo sefRelToAbs("index.php?option=$option&Itemid=$Itemid");?>" method="post" name="adminForm">
			<div style="width:100%; overflow:auto;">
				<table width="100%" cellpadding="0" cellspacing="0" border="0" class="<?php echo JLMSCSS::_('jlmslist');?>">
					<tr>
						<<?php echo JLMSCSS::tableheadertag();?> width="20" class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center">#</<?php echo JLMSCSS::tableheadertag();?>>
						<<?php echo JLMSCSS::tableheadertag();?> width="20" class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></<?php echo JLMSCSS::tableheadertag();?>>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" colspan="2"><?php echo _JLMS_TOOLBAR_LPATH;?></<?php echo JLMSCSS::tableheadertag();?>>
					</tr>
				<?php
				$k = 1;
				for ($i=0, $n=count($rows); $i < $n; $i++) {
					$row = $rows[$i];
					$checked = mosHTML::idBox( $i, $row->id);
					?>
					<tr class="<?php echo JLMSCSS::_('sectiontableentry'.$k); ?>">
						<td align="center" valign="middle"><?php echo ( $i + 1 ); ?></td>
						<td align="center" valign="middle"><?php echo $checked; ?></td>
						<td valign="middle" align="center" width="16">
							<span style="vertical-align: middle; text-align: center;">
								<img class="JLMS_png" width="16" height="16" alt="learnpath" src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/tlb_lpath.png"/>
							</span>
						</td>
						<td align="left" valign="middle" style="vertical-align:middle ">
							<?php  echo (!$row->published) ? '<span style="color:red">' : ''; ?>
									<?php echo $row->lpath_real_id ? $row->path_name : _JLMS_LP_RESOURSE_ISUNAV;?>
							<?php  echo (!$row->published) ? '</span>' : ''; ?>
						</td>
					</tr>
					<?php
					$k = 3 - $k;
				}?>
				</table>
			</div>
			<input type="hidden" name="option" value="<?php echo $option;?>" />
			<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
			<input type="hidden" name="course_id" value="<?php echo $course_id;?>" />
			<input type="hidden" name="task" value="gb_save_path" />
			<input type="hidden" name="state" value="0" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="id" value="<?php echo $id;?>" />
		
		
<?php
		$JLMS_ACL = & JLMSFactory::getACL();
		if ($JLMS_ACL->CheckPermissions('gradebook', 'manage')) {
			$controls = array();
			$link_new = "index.php?option=".$option."&amp;Itemid=".$Itemid."&amp;task=gbi_new&amp;id=".$course_id;
			$controls[] = array('href' => "javascript:submitbutton('gb_del_path')", 'title' => _JLMS_DEL_ITEM, 'img' => 'delete');
			$controls[] = array('href' => "javascript:document.adminForm.task.value='gb_save_path';check_selectbox();", 'title' => _JLMS_ADD_ITEM, 'img' => 'add');
			
			$add_options = $lists['learn_path'];
			if ($add_options) {
				$controls[] = array('href' => '', 'title' => '', 'img' => '', 'custom' => $add_options);
			}
			
			JLMS_TMPL::ShowControlsFooterC($controls);
		}
		
		?></form><?php
		
		JLMS_TMPL::CloseTS();
		JLMS_TMPL::CloseMT();
		
		
	}
	
	function showEditGBScale( &$row, &$lists, $option, $course_id ) {
		global $Itemid; ?>
<script language="javascript" type="text/javascript">
<!--
function submitbutton(pressbutton) {
	var form = document.adminForm;
	if (pressbutton == 'gbs_cancel') {
		form.task.value = 'gbs_cancel';
		form.submit();
	} else {
		var reg = /^\d*$/;
		if (form.scale_name.value == "") {
			alert( "<?php echo _JLMS_GB_ENTER_NAME;?>" );
		} else if (!reg.test(form.min_val.value)) {
			alert( "<?php echo _JLMS_GBS_ENTER_VALID_NUMS;?>" );
		} else if (!reg.test(form.max_val.value)) {
			alert( "<?php echo _JLMS_GBS_ENTER_VALID_NUMS;?>" );
		} else { form.submit(); }
	}
}
//-->
</script>
<?php
		JLMS_TMPL::OpenMT();

		$hparams = array();
		$toolbar = array();
		$toolbar[] = array('btn_type' => 'save', 'btn_js' => "javascript:submitbutton('gbs_save');");
		$toolbar[] = array('btn_type' => 'cancel', 'btn_js' => "javascript:submitbutton('gbs_cancel');");
		JLMS_TMPL::ShowHeader('gradebook', ($row->id ? _JLMS_GBS_EDIT_SCALE : _JLMS_GBS_NEW_ITEM), $hparams, $toolbar);

		JLMS_TMPL::OpenTS();
?>
		<form action="<?php echo sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid");?>" method="post" name="adminForm">
			<table width="100%" cellpadding="0" cellspacing="0" border="0" id="jlms_item_properties">
				<tr>
					<td width="15%"><?php echo _JLMS_ENTER_NAME;?></td>
					<td>
						<input size="40" class="inputbox" type="text" name="scale_name" value="<?php echo str_replace('"','&quot;',$row->scale_name);?>" />
					</td>
				</tr>
				<tr>
					<td width="15%"><br /><?php echo _JLMS_GBS_TBL_HEAD_MINPOINTS;?>:</td>
					<td>
						<br /><input size="40" class="inputbox" type="text" name="min_val" value="<?php echo $row->min_val;?>" />
					</td>
				</tr>
				<tr>
					<td width="15%"><br /><?php echo _JLMS_GBS_TBL_HEAD_MAXPOINTS;?>:</td>
					<td>
						<br /><input size="40" class="inputbox" type="text" name="max_val" value="<?php echo $row->max_val;?>" />
					</td>
				</tr>
			</table>
			<input type="hidden" name="option" value="<?php echo $option;?>" />
			<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
			<input type="hidden" name="task" value="gbs_save" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="id" value="<?php echo $row->id;?>" />
			<input type="hidden" name="course_id" value="<?php echo $course_id;?>" />
		</form>
<?php
		JLMS_TMPL::CloseTS();
		JLMS_TMPL::CloseMT();
	}

	function showGBScale( $course_id, $option, &$rows ) {
		global $Itemid, $my; ?>
<script language="javascript" type="text/javascript">
<!--
function submitbutton(pressbutton) {
	var form = document.adminForm;
	if ( ((pressbutton == 'gbs_edit') || (pressbutton == 'gbs_delete') ) && (form.boxchecked.value == "0")) {
		alert('<?php echo _JLMS_ALERT_SELECT_ITEM;?>');
	} else {
		form.task.value = pressbutton;
		form.submit();
	}
}
function submitbutton_order(pressbutton, item_id) {
	var form = document.adminForm;
	if ((pressbutton == 'gbs_orderup') || (pressbutton == 'gbs_orderdown')){
		if (item_id) {
			form.task.value = pressbutton;
			form.row_id.value = item_id;
			form.submit();
		}
	}
}
//-->
</script>

<?php
		JLMS_TMPL::OpenMT();

		$hparams = array();
		$toolbar = array();
		$toolbar[] = array('btn_type' => 'back', 'btn_js' => sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid&amp;task=gradebook&amp;id=$course_id"));
		JLMS_TMPL::ShowHeader('gradebook', _JLMS_GBS_TITLE, $hparams, $toolbar);

		JLMS_TMPL::OpenTS();
?>
			<form action="<?php echo sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid");?>" method="post" name="adminForm">
				<table width="100%" cellpadding="0" cellspacing="0" border="0" class="<?php echo JLMSCSS::_('jlmslist');?>">
					<tr>
						<<?php echo JLMSCSS::tableheadertag();?> width="20" class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center">#</<?php echo JLMSCSS::tableheadertag();?>>
						<<?php echo JLMSCSS::tableheadertag();?> width="20" class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></<?php echo JLMSCSS::tableheadertag();?>>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>"><?php echo _JLMS_GBS_TBL_HEAD_NAME;?></<?php echo JLMSCSS::tableheadertag();?>>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" colspan="2" width="1">&nbsp;</<?php echo JLMSCSS::tableheadertag();?>>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>"><?php echo _JLMS_GBS_TBL_HEAD_MINPOINTS;?></<?php echo JLMSCSS::tableheadertag();?>>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>"><?php echo _JLMS_GBS_TBL_HEAD_MAXPOINTS;?></<?php echo JLMSCSS::tableheadertag();?>>
					</tr>
				<?php
				$k = 1;
				for ($i=0, $n=count($rows); $i < $n; $i++) {
					$row = $rows[$i];
					$title = _JLMS_GBS_EDIT_SCALE;
					$link 	= "index.php?option=".$option."&amp;Itemid=".$Itemid."&amp;task=gbs_editA&amp;course_id=".$course_id."&amp;id=". $row->id;
					// sefRelToAbs() - est' nige
					$checked = mosHTML::idBox( $i, $row->id);?>
					<tr class="<?php echo JLMSCSS::_('sectiontableentry'.$k); ?>">
						<td valign="middle" align="center"><?php echo ( $i + 1 ); ?></td>
						<td valign="middle"><?php echo $checked; ?></td>
						<td valign="middle" align="left">
							<a href="<?php echo sefRelToAbs($link);?>" title="<?php echo $title;?>">
								<?php echo $row->scale_name;?>
							</a>
						</td>
						<td valign="middle"><?php echo JLMS_orderUpIcon($i, $row->id, true, 'gbs_orderup');?></td>
						<td valign="middle"><?php echo JLMS_orderDownIcon($i, $n, $row->id, true, 'gbs_orderdown');?></td>
						<td valign="middle"><?php echo $row->min_val;?></td>
						<td valign="middle"><?php echo $row->max_val;?></td>
					</tr>
					<?php
					$k = 3 - $k;
				} ?>
				</table>
				<input type="hidden" name="option" value="<?php echo $option;?>" />
				<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
				<input type="hidden" name="task" value="gb_scale" />
				<input type="hidden" name="course_id" value="<?php echo $course_id;?>" />
				<input type="hidden" name="boxchecked" value="0" />
				<input type="hidden" name="row_id" value="0" />
				<input type="hidden" name="state" value="0" />
			</form>
<?php
		JLMS_TMPL::CloseTS();
		$JLMS_ACL = & JLMSFactory::getACL();
		if ($JLMS_ACL->CheckPermissions('gradebook', 'manage')) {
			$controls = array();
			$link_new = "index.php?option=".$option."&amp;Itemid=".$Itemid."&amp;task=gbs_new&amp;course_id=".$course_id;
			$controls[] = array('href' => sefRelToAbs($link_new), 'title' => _JLMS_GBS_NEW_ITEM, 'img' => 'add');
			$controls[] = array('href' => "javascript:submitbutton('gbs_delete')", 'title' => _JLMS_GBS_DEL_ITEM, 'img' => 'delete');
			$controls[] = array('href' => "javascript:submitbutton('gbs_edit');", 'title' => _JLMS_GBS_EDIT_SCALE, 'img' => 'edit');
			JLMS_TMPL::ShowControlsFooter($controls);
		}
		JLMS_TMPL::CloseMT();
	}

	function showEditGBItem( &$row, &$lists, $option, $course_id ) {
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
	if (pressbutton == 'gbi_cancel') {
		form.task.value = 'gbi_cancel';
		form.submit();
	} else {
		if (form.gbi_name.value == "") {
			alert( "<?php echo _JLMS_GB_ENTER_NAME;?>" );
		}
		else { form.submit(); }
	}
}
//-->
</script>

<?php
		JLMS_TMPL::OpenMT();

		$hparams = array();
		$toolbar = array();
		$toolbar[] = array('btn_type' => 'save', 'btn_js' => "javascript:submitbutton('gbi_save');");
		$toolbar[] = array('btn_type' => 'cancel', 'btn_js' => "javascript:submitbutton('gbi_cancel');");
		JLMS_TMPL::ShowHeader('gradebook', ($row->id ? _JLMS_GB_EDIT_ITEM : _JLMS_GB_NEW_ITEM), $hparams, $toolbar);

		JLMS_TMPL::OpenTS();
?>
		<form action="<?php echo sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid");?>" method="post" name="adminForm" onsubmit="setgood();">
			<table width="100%" cellpadding="0" cellspacing="0" border="0" id="jlms_item_properties">
				<tr>
					<td width="15%"><?php echo _JLMS_ENTER_NAME;?></td>
					<td>
						<input size="40" class="inputbox" type="text" name="gbi_name" value="<?php echo str_replace('"','&quot;',$row->gbi_name);?>" />
					</td>
				</tr>
				<tr>
					<td><br /><?php echo _JLMS_GBI_CATEGORY;?></td>
					<td><br /><?php echo $lists['gb_cats'];?></td>
				</tr>
				<tr>
					<td colspan="2"><br /><?php echo _JLMS_DESCRIPTION;?></td>
				</tr>
				<tr>
					<td colspan="2">
					<?php JLMS_editorArea( 'editor1', $row->gbi_description, 'gbi_description', '100%;', '250', '40', '20' ); ?>
					</td>
				</tr>
			</table>
			<input type="hidden" name="option" value="<?php echo $option;?>" />
			<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
			<input type="hidden" name="task" value="gbi_save" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="id" value="<?php echo $row->id;?>" />
			<input type="hidden" name="course_id" value="<?php echo $course_id;?>" />
		</form>
<?php
		JLMS_TMPL::CloseTS();
		JLMS_TMPL::CloseMT();
	}
	function showGBItems( $course_id, $option, &$rows ) {
		global $Itemid, $my; ?>
<script language="javascript" type="text/javascript">
<!--
function submitbutton(pressbutton) {
	var form = document.adminForm;
	if ( ((pressbutton == 'gbi_edit') || (pressbutton == 'gbi_delete') ) && (form.boxchecked.value == "0")) {
		alert('<?php echo _JLMS_ALERT_SELECT_ITEM;?>');
	} else {
		form.task.value = pressbutton;
		form.submit();
	}
}
function submitbutton_order(pressbutton, item_id) {
	var form = document.adminForm;
	if ((pressbutton == 'gbi_orderup') || (pressbutton == 'gbi_orderdown')){
		if (item_id) {
		form.task.value = pressbutton;
		form.row_id.value = item_id;
		form.submit();
		}
	}
}
//-->
</script>

<?php
		JLMS_TMPL::OpenMT();

		$hparams = array();
		$toolbar = array();
		$toolbar[] = array('btn_type' => 'back', 'btn_js' => sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid&amp;task=gradebook&amp;id=$course_id"));
		JLMS_TMPL::ShowHeader('gradebook', _JLMS_GBI_TITLE, $hparams, $toolbar);

		JLMS_TMPL::OpenTS();
?>
		<form action="<?php echo sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid");?>" method="post" name="adminForm">
			<table width="100%" cellpadding="0" cellspacing="0" border="0" class="<?php echo JLMSCSS::_('jlmslist');?>">
				<tr>
					<<?php echo JLMSCSS::tableheadertag();?> width="20" class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center">#</<?php echo JLMSCSS::tableheadertag();?>>
					<<?php echo JLMSCSS::tableheadertag();?> width="20" class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></<?php echo JLMSCSS::tableheadertag();?>>
					<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>"><?php echo _JLMS_GBI_TBL_HEAD_NAME;?></<?php echo JLMSCSS::tableheadertag();?>>
					<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>"><?php echo _JLMS_GBI_TBL_HEAD_CAT;?></<?php echo JLMSCSS::tableheadertag();?>>
					<?php /*<td class="sectiontableheader"><?php echo _JLMS_GBI_TBL_HEAD_POINTS;?></td>*/?>
					<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" colspan="2" width="1">&nbsp;</<?php echo JLMSCSS::tableheadertag();?>>
					<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>"><?php echo _JLMS_GBI_TBL_HEAD_DESCR;?></<?php echo JLMSCSS::tableheadertag();?>>
				</tr>
			<?php
			$k = 1;
			for ($i=0, $n=count($rows); $i < $n; $i++) {
				$row = $rows[$i];
				$title = _JLMS_GB_EDIT_ITEM;
				$link 	= "index.php?option=".$option."&amp;Itemid=".$Itemid."&amp;task=gbi_editA&amp;course_id=".$course_id."&amp;id=". $row->id;
				// sefRelToAbs() - est' nige
				$checked = mosHTML::idBox( $i, $row->id);?>
				<tr class="<?php echo JLMSCSS::_('sectiontableentry'.$k); ?>">
					<td valign="middle" align="center"><?php echo ( $i + 1 ); ?></td>
					<td valign="middle"><?php echo $checked; ?></td>
					<td valign="middle" align="left">
						<a href="<?php echo sefRelToAbs($link);?>" title="<?php echo $title;?>">
							<?php echo $row->gbi_name;?>
						</a>
					</td>
					<td valign="middle"><?php echo $row->gb_category;?></td>
					<?php /*<td valign="middle"><?php echo $row->gbi_points;?></td>*/?>
					<td valign="middle"><?php echo JLMS_orderUpIcon($i, $row->id, true, 'gbi_orderup');?></td>
					<td valign="middle"><?php echo JLMS_orderDownIcon($i, $n, $row->id, true, 'gbi_orderdown');?></td>
					<td valign="middle"><?php echo $row->gbi_description ? $row->gbi_description : '&nbsp;';?></td>
				</tr>
				<?php
				$k = 3 - $k;
			} ?>
			</table>

			<input type="hidden" name="option" value="<?php echo $option;?>" />
			<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
			<input type="hidden" name="task" value="gb_items" />
			<input type="hidden" name="id" value="<?php echo $course_id;?>" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="row_id" value="0" />
			<input type="hidden" name="state" value="0" />
		</form>
<?php
		JLMS_TMPL::CloseTS();
		$JLMS_ACL = & JLMSFactory::getACL();
		if ($JLMS_ACL->CheckPermissions('gradebook', 'manage')) {
			$controls = array();
			$link_new = "index.php?option=".$option."&amp;Itemid=".$Itemid."&amp;task=gbi_new&amp;id=".$course_id;
			$controls[] = array('href' => sefRelToAbs($link_new), 'title' => _JLMS_GB_NEW_ITEM, 'img' => 'add');
			$controls[] = array('href' => "javascript:submitbutton('gbi_delete')", 'title' => _JLMS_GB_DEL_ITEM, 'img' => 'delete');
			$controls[] = array('href' => "javascript:submitbutton('gbi_edit');", 'title' => _JLMS_GB_EDIT_ITEM, 'img' => 'edit');
			JLMS_TMPL::ShowControlsFooter($controls);
		}
		JLMS_TMPL::CloseMT();
	}

	function showGradebook( $id, $option, &$rows, &$pageNav, &$lists, $manage = 0 ) { 
		global $Itemid, $JLMS_CONFIG;
		$is_full = $lists['is_full'];
		
		?>
		<script type="text/javascript" language="javascript">
		<!--//--><![CDATA[//><!--
		function submitFormView(view){
			var form = document.adminForm;
			form.view.value = view;
			form.task.value='gradebook';
			form.submit();
		}
		<?php
		if(!$is_full){		
		?>
		function submitbutton_crtf(pressbutton, state, cid_id) {
			var form = document.adminForm;
			if (pressbutton == 'gb_crt'){
				form.task.value = pressbutton;
				form.state.value = state;
				form.cid2.value = cid_id;
				form.submit();
			}
		}
		<?php
		} else {
		?>
		function submutFormChangeCourse(el){
			var form = document.adminForm;
			var new_id = parseInt(form.course_id.options[form.course_id.selectedIndex].value);
			form.view.value = '';
			form.id.value = new_id;
			form.task.value='gradebook';
			form.submit();	
		}
		<?php	
		}
		?>
		//--><!]]>
		</script>
		<?php
		JLMS_TMPL::OpenMT();
		if(!$is_full){	
			$hparams = array();
			JLMS_TMPL::ShowHeader('gradebook', _JLMS_GB_TITLE, $hparams);
	
			$controls = array();
			$controls[] = array('href' => sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid&amp;task=gb_scale&amp;course_id=".$id), 'title' => _JLMS_GB_MENU_SCALE, 'img' => 'gradebook/gb_scale.png');
			$controls[] = array('href' => sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid&amp;task=gb_items&amp;id=".$id), 'title' => _JLMS_GB_MENU_ITEMS, 'img' => 'gradebook/gb_items.png');
			$controls[] = array('href' => sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid&amp;task=gb_certificates&amp;id=".$id), 'title' => _JLMS_GB_MENU_CERTS, 'img' => 'buttons_22/btn_certificates_22.png');
			//kosmos added
			$controls[] = array('href' => sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid&amp;task=gb_setup_path&amp;id=".$id), 'title' => _CONFIGURE_COURSE_COMPLETION, 'img' => 'buttons_22/btn_lpath_crt_22.png');
			//--end
			JLMS_TMPL::ShowControlsFooter($controls, '', false, true);
		}
		JLMS_TMPL::OpenTS();
		$action_url = $is_full ? ($JLMS_CONFIG->get('live_site') . "/index.php?tmpl=component&amp;option=$option&amp;Itemid=$Itemid") : sefRelToAbs("index.php?option=$option&Itemid=$Itemid");
		?>
		<form action="<?php echo $action_url;?>" method="post" name="adminForm">
			<?php
			if($is_full){
				?>
				<table class="jlms_table_no_borders">
					<tr>
						<td>
							<?php 
							if(isset($lists['courses'])){
								echo _JLMS_CURRENT_COURSE. ' ' . $lists['courses'];	
							}
							?>
						</td>
					</tr>
				</table>
				<?php
				//$controls = array();
				//$controls[] = array('href' => "javascript:submitFormView('csv');", 'title' => 'CSV', 'img' => 'csv');
				//JLMS_TMPL::ShowControlsFooterC($controls, '', false, false, _JLMS_EXPORT_TO.':');
			}
			?>
			<table width="100%" cellpadding="0" cellspacing="0" border="0" id="TheTable" class="jlms_table_no_borders">
				<tr>
					<td align="left" style="text-align:left ">
						<div align="left" style="white-space:nowrap ">
							<?php 
							echo "&nbsp;". _JLMS_FILTER."&nbsp;".$lists['filter'];?>&nbsp;
							<input class="inputbox" type="text" name="username_filter" value="<?php echo $lists['filt_user'];?>" />
							<input type="submit" name="Filter" value="<?php echo str_replace(':','',_JLMS_FILTER);?>" />
						</div>
					</td>
					<?php
					if(!$is_full){	
					?>
					<td align="right">
						<?php
						$link_full = $JLMS_CONFIG->get('live_site') . '/index.php?tmpl=component&amp;option='.$option.'&amp;Itemid='.$Itemid.'&amp;task=gradebook&amp;id='.$id;
						$link_full .= '&amp;is_full=1';
						?>
						<a target="_blank" href="<?php echo $link_full;?>">
							<?php echo _JLMS_FULL_VIEW_BUTTON;?>
						</a>
					</td>
					<?php 
					}
					/*<td align="left" style="text-align:right ">
						<div align="left" style="white-space:nowrap ">
							<?php echo $lists['filter'];?><input type="text" name="username_filter" value="<?php echo $lists['filt_user'];?>" /><input type="submit" name="Filter" value="Filter" />
						</div>
					</td>*/
					?>
				</tr>
			</table>
			<?php
			if(!$is_full){
				$domready = '
				$(\'pre_div\').setStyles({\'display\': \'none\'});
				$(\'vw_div\').setStyles({\'display\': \'block\'});
				var cur_height = $(\'vw_div\').getStyle(\'height\').toInt() + 18;
				$(\'vw_div\').setStyles({\'width\': $(\'TheTable\').offsetWidth+\'px\', \'height\': cur_height+\'px\'});
				';
				$JLMS_CONFIG->set('web20_domready_code', $JLMS_CONFIG->get('web20_domready_code','').$domready);
			?>
			<div id="pre_div" style="display: block; width: 100%; text-align: center;">
				<br />
				Please wait. Gradebook is loading.<br /> If this message stays for over 1 minute, please click <a target="_blank" href="<?php echo $link_full;?>">&lt;here&gt;</a> to open Gradebook in new window.
			</div>
			<div id="vw_div" style="overflow: auto; width: 200px; height: auto; display: none;">
			<?php
			}
			if ($is_full) { echo '<br />'; }
			?>
			<?php $gb_colspan = 2;?>
				<table width="100%" cellpadding="<?php echo $is_full ? '4' : '0';?>" cellspacing="0" border="0" class="<?php echo JLMSCSS::_('jlmslist', 'jlms_gradebook_fullview_table');?>"<?php echo (!$is_full) ? ' style="margin-bottom:0px;padding-bottom:0px;"' : '';?>>
					<tr>
						<<?php echo JLMSCSS::tableheadertag();?> width="20" class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center">#</<?php echo JLMSCSS::tableheadertag();?>>
						<<?php echo JLMSCSS::tableheadertag();?> width="20" class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></<?php echo JLMSCSS::tableheadertag();?>>
					<?php if(!$is_full) { $gb_colspan++; ?>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>"><?php echo _JLMS_GB_TBL_HEAD_STU;?></<?php echo JLMSCSS::tableheadertag();?>>
					<?php } else { $gb_colspan = $gb_colspan + 3; ?>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>"><?php echo str_replace(':', '', _JLMS_UI_NAME);?></<?php echo JLMSCSS::tableheadertag();?>>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>"><?php echo str_replace(':', '', _JLMS_UI_USERNAME);?></<?php echo JLMSCSS::tableheadertag();?>>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>"><?php echo str_replace(':', '', _JLMS_UI_EMAIL);?></<?php echo JLMSCSS::tableheadertag();?>>
					<?php } ?>
						
					<?php
						$com_transcript_exist = file_exists(JPATH_SITE . DS . 'components' . DS . 'com_jlms_transcript' . DS . 'jlms_transcript.php') ? true : false;
						if($com_transcript_exist){
							$gb_colspan++; ?>
							<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>">&nbsp;</<?php echo JLMSCSS::tableheadertag();?>>
					<?php 
						}
						$gb_colspan++;
					?>
						
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>"><?php echo _JLMS_GB_TBL_HEAD_GROUP;?></<?php echo JLMSCSS::tableheadertag();?>>
						<?php
						if(isset($lists['regenroll_fields']) && count($lists['regenroll_fields'])){
							$regenroll_fields = $lists['regenroll_fields'];
							foreach($regenroll_fields as $field){
								$gb_colspan++;
								echo '<'.JLMSCSS::tableheadertag().' align=\'center\' nowrap=\'nowrap\' class="'.JLMSCSS::_('sectiontableheader').'" style="text-align:center; white-space:nowrap">'.$field->title.'</'.JLMSCSS::tableheadertag().'>';	
							}	
						}
						$gb_colspan = $gb_colspan + 2;
						?>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center" nowrap="nowrap"><?php echo _JLMS_COURSE_COMPLETION_TABLE;?></<?php echo JLMSCSS::tableheadertag();?>>
						<?php
						if($JLMS_CONFIG->get('enable_timetracking')){
							?>
							<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center" nowrap="nowrap"><?php echo _JLMS_TIME_SPENT_TABLE;?></<?php echo JLMSCSS::tableheadertag();?>>
							<?php
						}
						?>
						<?php if($is_full) { $gb_colspan++; ?>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center" nowrap="nowrap"><?php echo str_replace(':', '', _JLMS_DATE);?></<?php echo JLMSCSS::tableheadertag();?>>
						<?php } ?>
						<?php /*
						<td class="sectiontableheader" align="center" nowrap="nowrap"><?php echo 'Access#';?></td>
						*/ ?>
						<?php
						$sc_num = 0;
						foreach ($lists['sc_rows'] as $sc_row) {
							if ($sc_row->show_in_gradebook) {
								$sc_num++;
								$link = isset($sc_row->is_link)?$sc_row->is_link:'';
								if ($is_full) {
									echo '<'.JLMSCSS::tableheadertag().' align=\'center\' nowrap=\'nowrap\' class="'.JLMSCSS::_('sectiontableheader').'">'.$sc_row->lpath_name.'</'.JLMSCSS::tableheadertag().'>';
								} else {
									echo '<'.JLMSCSS::tableheadertag().' align=\'center\' nowrap=\'nowrap\' class="'.JLMSCSS::_('sectiontableheader').'">'.JLMS_gradebook_html::Echo_tbl_header($sc_row->lpath_name, $link).'</'.JLMSCSS::tableheadertag().'>';
								}
								$gb_colspan++;
							}
						}
						foreach ($lists['quiz_rows'] as $quiz_row) {
							if ($is_full) {
								echo '<'.JLMSCSS::tableheadertag().' align=\'center\' nowrap=\'nowrap\' class="'.JLMSCSS::_('sectiontableheader').'">'.$quiz_row->c_title.'</'.JLMSCSS::tableheadertag().'>';
							} else {
								echo '<'.JLMSCSS::tableheadertag().' align=\'center\' nowrap=\'nowrap\' class="'.JLMSCSS::_('sectiontableheader').'">'.JLMS_gradebook_html::Echo_tbl_header($quiz_row->c_title).'</'.JLMSCSS::tableheadertag().'>';
							}
							$gb_colspan++;
						}
						foreach ($lists['gb_rows'] as $gb_row) {
							if ($is_full) {
								echo '<'.JLMSCSS::tableheadertag().' align=\'center\' nowrap=\'nowrap\' class="'.JLMSCSS::_('sectiontableheader').'">'.$gb_row->gbi_name.'</'.JLMSCSS::tableheadertag().'>';
							} else {
								echo '<'.JLMSCSS::tableheadertag().' align=\'center\' nowrap=\'nowrap\' class="'.JLMSCSS::_('sectiontableheader').'">'.JLMS_gradebook_html::Echo_tbl_header($gb_row->gbi_name).'</'.JLMSCSS::tableheadertag().'>';
							}
							$gb_colspan++;
						}?>
						
					</tr>
				<?php
				$k = 1;
				for ($i=0, $n=count($rows); $i < $n; $i++) {
					$row = $rows[$i];
					$link = "index.php?option=$option&amp;Itemid=$Itemid&amp;task=gb_usergrade&amp;course_id=$id&amp;id={$row->user_id}";
					$checked = mosHTML::idBox( $i, $row->user_id);
					?>
					<tr class="<?php echo JLMSCSS::_('sectiontableentry'.$k); ?>">
						<td align="center" valign="middle"><?php echo ( $pageNav->limitstart + $i + 1 ); ?></td>
						<td align="center" valign="middle"><?php echo $checked; ?></td>
					<?php if(!$is_full) { ?>
						<td align="left" valign="middle">
							<?php /*<a href="<?php echo sefRelToAbs($link);?>" title="<?php echo _JLMS_GB_VIEW_USER;?>">*/?>
								<?php echo JLMS_gradebook_html::Echo_userinfo($row->name, $row->username, $row->email, sefRelToAbs($link));?>
							<?php /*</a> */ ?>
						</td>
					<?php } else { ?>
						<td align="left" valign="middle">
							<a href="<?php echo sefRelToAbs($link);?>" title="<?php echo _JLMS_GB_VIEW_USER;?>">
								<?php echo $row->name;?>
							</a>
						</td>
						<td align="left" valign="middle">
							<a href="<?php echo sefRelToAbs($link);?>" title="<?php echo _JLMS_GB_VIEW_USER;?>">
								<?php echo $row->username;?>
							</a>
						</td>
						<td align="left" valign="middle">
							<a href="<?php echo sefRelToAbs($link);?>" title="<?php echo _JLMS_GB_VIEW_USER;?>">
								<?php echo $row->email;?>
							</a>
						</td>
					<?php } ?>
					<?php
						if($com_transcript_exist){
					?>
						<td align="left" valign="middle">
							<?php 
							$link_com_transcript = 'index.php?option=com_jlms_transcript&user_id='.$row->user_id;
							?>
							<a href="<?php echo $link_com_transcript;?>" title="transcript">
								<?php echo 'transcript';?>
							</a>
						</td>
					<?php
						}
					?>	
					
						<td align="left" valign="middle">
							<?php echo $row->ug_name?$row->ug_name:'&nbsp;';?>
						</td>
						<?php
						if(isset($lists['regenroll_fields']) && count($lists['regenroll_fields'])){
							foreach($regenroll_fields as $field){
								$field_name = $field->name;
								?>
								<td align="left" valign="middle">
								<?php
								echo isset($row->$field_name) && $row->$field_name ? $row->$field_name : '';	
								?>
								</td>
								<?php
							}	
						}	
						?>
						<td align="center" valign="middle">
							<?php
							$image = $row->user_certificate ? 'btn_accept.png' : 'btn_cancel.png';
							$alt = $row->user_certificate ? _JLMS_GB_USER_HAVE_CRT : _JLMS_GB_USER_HAVE_NO_CRT;
							if(!$is_full){
								$state =  $row->user_certificate ? 0 : 1;
								if($manage) {
									echo '<a href="javascript:submitbutton_crtf(\'gb_crt\','.$state.','.$row->user_id.')" title="'.$alt.'" class="jlms_img_link"><img class="JLMS_png" src="'.$JLMS_CONFIG->get('live_site').'/components/com_joomla_lms/lms_images/toolbar/'.$image.'" width="16" height="16" border="0" alt="'.$alt.'" /></a>';
								}
								else {
									echo '<img class="JLMS_png" src="'.$JLMS_CONFIG->get('live_site').'/components/com_joomla_lms/lms_images/toolbar/'.$image.'" width="16" height="16" border="0" alt="'.$alt.'" />';
								}
								if (isset($row->date_completed) && $row->date_completed ) {
									echo '<br />'.JLMS_offsetDateToDisplay($row->date_completed);
								}
							} else {
								echo '<img class="JLMS_png" src="'.$JLMS_CONFIG->get('live_site').'/components/com_joomla_lms/lms_images/toolbar/'.$image.'" width="16" height="16" border="0" alt="'.$alt.'" />';
							}
							?>
						</td>
						<?php
						if($JLMS_CONFIG->get('enable_timetracking')){
							?>
							<td align="center" valign="middle"><?php echo $row->time_spent; ?></td>
							<?php
						}	
						?>		
						<?php if($is_full) { ?>
						<td align="center" valign="middle">
							<?php
							echo JLMS_offsetDateToDisplay($row->date_completed);
							?>
						</td>
						<?php } ?>
						<?php /*
						<td align="center" valign="middle">
							<?php
							echo (int) $row->access;
							?>
						</td>
						*/ ?>
						<?php
						$sc_num2 = 0;
						foreach ($lists['sc_rows'] as $sc_row) {
							if ($sc_row->show_in_gradebook) {	
								$j = 0;
								while ($j < count($row->scorm_info)) {
									if ($row->scorm_info[$j]->gbi_id == $sc_row->item_id) {
										if ($sc_num2 < $sc_num) {
											if ($row->scorm_info[$j]->user_status == -1) {
												echo '<td align=\'center\'>-</td>';
											} else {
												$image = $row->scorm_info[$j]->user_status ? 'btn_accept.png' : 'btn_cancel.png';
												$alt = $row->scorm_info[$j]->user_status ? 'btn_accept' : 'btn_cancel';
												$alt .= '" align="top';
												$img = JLMS_gradebook_html::publishIcon(0, 0, 0, '', $alt, $image, $option, false );
		
												echo '<td valign="middle" align="center" nowrap="nowrap">'.$img
												. '&nbsp;<strong>' . $row->scorm_info[$j]->user_grade . "</strong> (" . $row->scorm_info[$j]->user_pts . _JLMS_GB_POINTS . ")"
												. '</td>';
											}
											$sc_num2++;
										}
									}
									$j ++;
								}
							}
						}
						foreach ($lists['quiz_rows'] as $quiz_row) {
							$j = 0;
							while ($j < count($row->quiz_info)) {
								if ($row->quiz_info[$j]->gbi_id == $quiz_row->c_id) {
//									$image = $row->quiz_info[$j]->user_status ? 'btn_accept.png' : 'btn_cancel.png';
//									$alt = $row->quiz_info[$j]->user_status ? 'btn_accept' : 'btn_cancel';
//									$alt .= '" align="top';
//									$img = JLMS_gradebook_html::publishIcon(0, 0, 0, '', $alt, $image, $option, false );
//									echo $img . '&nbsp;<strong>' . $row->quiz_info[$j]->user_grade . "</strong> (" . $row->quiz_info[$j]->user_pts_full . ")";
//									/*Integration Plugin Percentiles*/
//									if(isset($row->quiz_info[$j]->user_percentile)){
//										echo ' - '.$row->quiz_info[$j]->user_percentile;
//									}
//									/*Integration Plugin Percentiles*/
									
									echo '<td valign="middle" align="center" nowrap="nowrap">';
										echo JLMS_showQuizStatus($row->quiz_info[$j], 50);
									echo '</td>';
								}
								$j ++;
							}
						}
						$j = 0;
						while ($j < count($row->grade_info)) {
							echo '<td align=\'center\' valign="middle"><strong>'
							. $row->grade_info[$j]->user_grade
							. '</strong></td>';
							$j ++;
						} ?>
					</tr>
					<?php
					$k = 3 - $k;
				}?>
				<?php if ($is_full) { ?>
				<tr>
					<td align="center" colspan="<?php echo $gb_colspan;?>"class="<?php echo JLMSCSS::_('jlmslist-footer_td');?>"><div align="center">
					<?php if(!$is_full){
							$link = "index.php?option=$option&amp;Itemid=$Itemid&amp;task=gradebook&amp;id=$id";
							echo _JLMS_PN_DISPLAY_NUM . $pageNav->getLimitBox( $link ) . '&nbsp;' . $pageNav->getPagesCounter();
							echo '<br />';
						}
					?>
					<?php $link = "index.php?option=$option&amp;Itemid=$Itemid&amp;task=gradebook&amp;id=$id";
					echo $pageNav->writePagesLinks( $link ); ?> 
					</div></td>
				</tr>
				<?php } ?>
				</table>
			<?php
			if ($is_full && count($rows)) {
				$controls = array();
				$controls[] = array('href' => "javascript:submitFormView('csv');", 'title' => 'CSV', 'img' => 'csv');
				$controls[] = array('href' => "javascript:submitFormView('xls');", 'title' => 'XLS', 'img' => 'xls');
				JLMS_TMPL::ShowControlsFooterC($controls, '', false, false, _JLMS_EXPORT_TO.':', true);
			} else {
			?>
			</div>
			<?php if (!$is_full) { ?>
			<table width="100%" cellpadding="0" cellspacing="0" border="0" class="<?php echo JLMSCSS::_('jlmslist');?>" style="padding: 0px; margin-top:0px; margin-bottom:0px;">
				<tr>
					<td align="center" class="<?php echo JLMSCSS::_('jlmslist-footer_td');?>"><div align="center">
					<?php
					$link = "index.php?option=$option&amp;Itemid=$Itemid&amp;task=gradebook&amp;id=$id";
					echo _JLMS_PN_DISPLAY_NUM . $pageNav->getLimitBox( $link ) . '&nbsp;' . $pageNav->getPagesCounter();
					echo '<br />';
					echo $pageNav->writePagesLinks( $link ); ?> 
					</div></td>
				</tr>
			</table>
			<?php } ?>
			<?php 
			if($JLMS_CONFIG->get('new_lms_features', 1) && count($rows)){
				$controls = array();
				$controls[] = array('href' => "javascript:submitFormView('csv');", 'title' => 'CSV', 'img' => 'csv');
				$controls[] = array('href' => "javascript:submitFormView('xls');", 'title' => 'XLS', 'img' => 'xls');
				JLMS_TMPL::ShowControlsFooterC($controls, '', false, false, _JLMS_EXPORT_TO.':');
			}
			}
			?>
			<input type="hidden" name="option" value="<?php echo $option;?>" />
			<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
			<input type="hidden" name="task" value="gradebook" />
			<input type="hidden" name="state" value="0" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="id" value="<?php echo $id;?>" />
			<input type="hidden" name="cid2" value="0" />
			<input type="hidden" name="view" value="" />
			<?php
			if($is_full){
			?>
			<input type="hidden" name="is_full" value="1" />
			<?php
			}
			?>
		</form>
<?php
		JLMS_TMPL::CloseTS();
		JLMS_TMPL::CloseMT();
	}

	function showGradebook_CEO( $id, $option, &$rows, &$lists ) {
		global $Itemid, $JLMS_CONFIG;
		$is_full = $lists['is_full'];

		if(!$is_full){
			?>
			<script type="text/javascript" language="javascript">
			<!--//--><![CDATA[//><!--
			function submitFormView(view){
				var form = document.adminForm;
				form.view.value = view;
				form.task.value='gradebook';
				form.submit();
			}
			//--><!]]>
			</script>
			<?php
			JLMS_TMPL::OpenMT();
			$hparams = array();
			JLMS_TMPL::ShowHeader('gradebook', _JLMS_GB_TITLE, $hparams);
		}
		JLMS_TMPL::OpenTS();
		$action_url = $is_full ? ($JLMS_CONFIG->get('live_site') . "/index.php?tmpl=component&amp;option=$option&amp;Itemid=$Itemid") : sefRelToAbs("index.php?option=$option&Itemid=$Itemid");
		?>
		<form action="<?php echo $action_url;?>" method="post" name="adminForm">
			<?php
			if(!$is_full){
				$link_full = $JLMS_CONFIG->get('live_site') . '/index.php?tmpl=component&amp;option='.$option.'&amp;Itemid='.$Itemid.'&amp;task=gradebook&amp;id='.$id.'&amp;is_full=1';
			?>
			<table width="100%" border="0" cellpadding="0" cellspacing="0" id="TheTable" class="jlms_table_no_borders">
				<tr>
					<td>
						&nbsp;
					</td>
				</tr>
				<tr>
					<td align="right">
						<a target="_blank" href="<?php echo $link_full;?>">
							<?php echo _JLMS_FULL_VIEW_BUTTON;?>
						</a>
					</td>
				</tr>
				<tr>
					<td>
						&nbsp;
					</td>
				</tr>
			</table>
			<?php
				$domready = '
				$(\'pre_div\').setStyles({\'display\': \'none\'});
				$(\'vw_div\').setStyles({\'display\': \'block\'});
				var cur_height = $(\'vw_div\').getStyle(\'height\').toInt() + 18;
				$(\'vw_div\').setStyles({\'width\': $(\'TheTable\').offsetWidth+\'px\', \'height\': cur_height+\'px\'});
				';
				$JLMS_CONFIG->set('web20_domready_code', $JLMS_CONFIG->get('web20_domready_code','').$domready);
			?>
			<div id="pre_div" style="display: block; width: 100%; text-align: center;">
				<br />
				Please wait. Gradebook is loading.<br /> If this message stays for over 1 minute, please click <a target="_blank" href="<?php echo $link_full;?>">&lt;here&gt;</a> to open Gradebook in new window.
			</div>
			<div id="vw_div" style="overflow: auto; width: 200px; height: auto; display: none;">
			<?php
			}
			?>
				<table width="100%" cellpadding="0" cellspacing="0" border="0" class="<?php echo JLMSCSS::_('jlmslist');?>">
					<tr>
						<<?php echo JLMSCSS::tableheadertag();?> width="20" class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center">#</<?php echo JLMSCSS::tableheadertag();?>>
						<<?php echo JLMSCSS::tableheadertag();?> width="20" class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></<?php echo JLMSCSS::tableheadertag();?>>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>"><?php echo _JLMS_GB_TBL_HEAD_STU;?></<?php echo JLMSCSS::tableheadertag();?>>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>"><?php echo _JLMS_GB_TBL_HEAD_GROUP;?></<?php echo JLMSCSS::tableheadertag();?>>
						<?php
						foreach ($lists['sc_rows'] as $sc_row) {
							if ($sc_row->show_in_gradebook) {
								$link = isset($sc_row->is_link)?$sc_row->is_link:'';
								echo '<'.JLMSCSS::tableheadertag().' align=\'center\' nowrap="nowrap" class="'.JLMSCSS::_('sectiontableheader').'">'.JLMS_gradebook_html::Echo_tbl_header($sc_row->lpath_name, $link).'</'.JLMSCSS::tableheadertag().'>';
							}
						}
						foreach ($lists['quiz_rows'] as $quiz_row) {
							echo '<'.JLMSCSS::tableheadertag().' align=\'center\' nowrap="nowrap" class="'.JLMSCSS::_('sectiontableheader').'">'.JLMS_gradebook_html::Echo_tbl_header($quiz_row->c_title).'</'.JLMSCSS::tableheadertag().'>';
						}
						foreach ($lists['gb_rows'] as $gb_row) {
							echo '<'.JLMSCSS::tableheadertag().' align=\'center\' nowrap="nowrap" class="'.JLMSCSS::_('sectiontableheader').'">'.JLMS_gradebook_html::Echo_tbl_header($gb_row->gbi_name).'</'.JLMSCSS::tableheadertag().'>';
						}?>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center" nowrap="nowrap"><?php echo _JLMS_COURSE_COMPLETION_TABLE;?></<?php echo JLMSCSS::tableheadertag();?>>
					</tr>
				<?php
				$k = 1;
				for ($i=0, $n=count($rows); $i < $n; $i++) {
					$row = $rows[$i];
					$checked = mosHTML::idBox( $i, $row->user_id);
					$link = "index.php?option=$option&amp;Itemid=$Itemid&amp;task=gb_usergrade&amp;course_id=$id&amp;id={$row->user_id}";
					?>
					<tr class="<?php echo JLMSCSS::_('sectiontableentry'.$k); ?>">
						<td align="center" valign="middle"><?php echo ( $i + 1 ); ?></td>
						<td align="center" valign="middle"><?php echo $checked; ?></td>
						<td align="left" valign="middle">
							<a href="<?php echo sefRelToAbs($link);?>" title="<?php echo _JLMS_GB_VIEW_USER;?>">
								<?php echo $row->username;?>
							</a>
						</td>
						<td align="left" valign="middle">
							<?php echo $row->ug_name;?>
						</td>
						<?php
						foreach ($lists['sc_rows'] as $sc_row) {
							if ($sc_row->show_in_gradebook) {	
								$j = 0;
								while ($j < count($row->scorm_info)) {
									if ($row->scorm_info[$j]->gbi_id == $sc_row->item_id) {
										if ($row->scorm_info[$j]->user_status == -1) {
											echo '<td align=\'center\' valign="middle" nowrap="nowrap">'
											. '-'
											. '</td>';
										} else {
											$image = $row->scorm_info[$j]->user_status ? 'btn_accept.png' : 'btn_cancel.png';
											$alt = $row->scorm_info[$j]->user_status ? 'btn_accept' : 'btn_cancel';
											$img = JLMS_gradebook_html::publishIcon(0, 0, 0, '', $alt, $image, $option, false );

											echo '<td align="center" valign="middle" nowrap="nowrap">'.$img
											. '&nbsp;<strong>' . $row->scorm_info[$j]->user_grade . "</strong> (" . $row->scorm_info[$j]->user_pts . _JLMS_GB_POINTS . ")"
											. '</td>';
										}
									}
									$j ++;
								}
							}
						}
						foreach ($lists['quiz_rows'] as $quiz_row) {
							$j = 0;
							while ($j < count($row->quiz_info)) {
								if ($row->quiz_info[$j]->gbi_id == $quiz_row->c_id) {
									if ($row->quiz_info[$j]->user_status == -1) {
										echo '<td align=\'center\' valign="middle" nowrap="nowrap">'
										. '-'
										. '</td>';
									} else {
										$image = $row->quiz_info[$j]->user_status ? 'btn_accept.png' : 'btn_cancel.png';
										$alt = $row->quiz_info[$j]->user_status ? 'btn_accept' : 'btn_cancel';
										$img = JLMS_gradebook_html::publishIcon(0, 0, 0, '', $alt, $image, $option, false );
	
										echo '<td align=\'center\' valign="middle" nowrap="nowrap">'
										. $img . '&nbsp;<strong>' . $row->quiz_info[$j]->user_grade . "</strong> (" . $row->quiz_info[$j]->user_pts_full . ")"//'<br />' . $row->scorm_info[$j]->user_status
										. '</td>';
									}
								}
								$j ++;
							}
						}
						$j = 0;
						while ($j < count($row->grade_info)) {
							echo '<td align=\'center\' valign="middle"><strong>'
							. $row->grade_info[$j]->user_grade
							. '</strong></td>';
							$j ++;
						} ?>
						<td align="center" valign="middle">
							<?php
							$image = $row->user_certificate ? 'btn_accept.png' : 'btn_cancel.png';
							$alt = $row->user_certificate ? _JLMS_GB_USER_HAVE_CRT : _JLMS_GB_USER_HAVE_NO_CRT;
							if(!$is_full){
								$state =  $row->user_certificate ? 0 : 1;
								echo JLMS_gradebook_html::publishIcon(0, 0, 0, '', $alt, $image, $option, false );
							} else {
								echo '<img class="JLMS_png" src="'.$JLMS_CONFIG->get('live_site').'/components/com_joomla_lms/lms_images/toolbar/'.$image.'" width="16" height="16" border="0" alt="'.$alt.'" />';	
							}
							?>
						</td>
					</tr>
					<?php
					$k = 3 - $k;
				}?>
				</table>
			<?php
			if(!$is_full){
			?>	
			</div>
			<?php 
			if($JLMS_CONFIG->get('new_lms_features', 1)){
				$controls = array();
				$controls[] = array('href' => "javascript:submitFormView('csv');", 'title' => 'CSV', 'img' => 'csv');
				$controls[] = array('href' => "javascript:submitFormView('xls');", 'title' => 'XLS', 'img' => 'xls');
				JLMS_TMPL::ShowControlsFooterC($controls, '', false, false, _JLMS_EXPORT_TO.':');
			}
			?>	
			<?php
			}
			?>
			<input type="hidden" name="option" value="<?php echo $option;?>" />
			<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
			<input type="hidden" name="task" value="gradebook" />
			<input type="hidden" name="state" value="0" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="id" value="<?php echo $id;?>" />
			<input type="hidden" name="view" value="" />
		</form>
<?php
		JLMS_TMPL::CloseTS();
		JLMS_TMPL::CloseMT();
	}

function Echo_tbl_header( $header, $link = '' ) {
	$ret_str = '';
	if (strlen($header) > 13) {
		$overlib_description = JLMS_txt2overlib($header);
		$ret_str = '';
		if($link==''){
			$inside_tag = substr($header, 0, 13).'...';
			$ret_str .= JLMS_toolTip($overlib_description, '', $inside_tag, 'javascript:void(0);', 1, 150, 'false');
		}
	} else {
		$ret_str = $header;
	}
	return $ret_str;
}
function Echo_userinfo( $name, $username, $email, $link) {
	$ret_str = '';
	$title = JLMS_txt2overlib(_JLMS_USER_INFORMATION);
	$content = _JLMS_UI_USERNAME.' '.$username.'<br />'._JLMS_UI_NAME.' '.$name.'<br />'._JLMS_UI_EMAIL.' '.$email;
	return JLMS_toolTip($title, $content, $name, $link, 1, 30, 'true', 'jlms_ttip');
}

function gbUserPDF( $id, $option, &$rows, &$lists ) {		
		global $Itemid, $JLMS_CONFIG, $JLMS_DB;
		
		$juser = JLMSFactory::getUser();
		
		$JLMS_ACL = & JLMSFactory::getACL();
		
		chdir(JPATH_SITE);
		if ( file_exists( JPATH_SITE . DS .'components'. DS .'com_joomla_lms'. DS .'includes'. DS .'pdf'. DS .'lms.pdf.php') ) {
			include( JPATH_SITE . DS .'components'. DS .'com_joomla_lms'. DS .'includes'. DS .'pdf'. DS .'lms.pdf.php' );
		} else {
			die('Unable to print PDF');
		}
			
				
		$pdf = new JLMSPDF( 'P', 'mm', 'A4', true, 'UTF-8', false );  //A4 Portrait
		$pdf->SetMargins( 10, 10, 10, true );
		$pdf->SetDrawColor( 0, 0, 0 );
		
		$header = "<br /><div align=\"center\">".$JLMS_CONFIG->get('course_name')."</div>";
	
		$footer = "
			<hr />
			<table>
			    <tr>
			        <td align=\"left\">".$JLMS_CONFIG->get('sitename').' - '.$JLMS_CONFIG->get('live_site')."</td>
		";
		
		if ($JLMS_CONFIG->get('is_trial')) {
			$footer .= "<td align=\"center\">Powered by JoomlaLMS (www.joomlalms.com)</td>";
		}
		
		$footer .= "
			        <td align=\"right\">". date( 'j F, Y, H:i', time() + $JLMS_CONFIG->get('offset') * 60 * 60 )."</td>
			    </tr>		  
			</table>
		";
		
		$pdf->setHeaderFont( array('freesans','',6) );
		$pdf->setHeaderHTML( $header );
		
		$pdf->setFooterMargin( 5 );
		$pdf->setFooterFont( array('freesans','',6) );	
		$pdf->setFooterHTML( $footer );	
				
		$pdf->addPage();	
		$pdf->setFont( 'freesans' ); //choose font		
		$pdf->SetFontSize( 8 );		
		ob_clean();
		ob_start();		
		?>
		<table width="100%">
			<tr>
				<td align="right">				
				<font size="10"><?php 
					if( $rows[0]->user_certificate ) 
					{
						echo $JLMS_ACL->CheckPermissions('gradebook', 'manage')?_JLMS_COURSE_COMPLETED_ADMIN:($juser->get('id') == $rows[0]->user_id ? _JLMS_COURSE_COMPLETED_USER : _JLMS_COURSE_COMPLETED_ADMIN); echo str_repeat('&nbsp;', 15);
					} 
				?></font>				
				</td>
			</tr>
		</table>
		<br />
		<?php		
					
		$params_cs = array();		
			
		if ($JLMS_ACL->CheckPermissions('gradebook', 'manage') && !empty($lists['enrollment_answers'])) {
			?>			
			<table width="100%">
			<tr>			
				<td width="50%" valign="top">
					<div><strong><font size="10"><?php echo _JLMS_GB_USER_INFORMATION; ?></font></strong></div>
					<table width="100%" cellpadding="0" cellspacing="0" border="0" >				
					<tr>
						<td width="20%" align="left"><strong><?php echo _JLMS_UI_USERNAME; ?></strong></td>
						<td align="left"><?php echo $rows[0]->username; ?></td>
					</tr>
					<tr>
						<td align="left"><strong><?php echo _JLMS_UI_NAME; ?></strong></td>
						<td align="left"><?php echo $rows[0]->name; ?></td>
					</tr>
					<tr>
						<td align="left"><strong><?php echo _JLMS_UI_EMAIL; ?></strong></td>
						<td align="left"><?php echo $rows[0]->email; ?></td>
					</tr>
					<tr>
						<td align="left"><strong><?php echo _JLMS_UI_GROUP; ?></strong></td>
						<td align="left"><?php echo ($rows[0]->ug_name?$rows[0]->ug_name:'&nbsp;'); ?></td>
					</tr>
					</table>					
				</td>							
				<td width="50%" valign="top">					
					<div><strong><font size="10"><?php echo _JLMS_GB_REG_INFORMATION; ?></font></strong></div>
					<table width="100%" cellpadding="0" cellspacing="0" border="0">						<?php					
						foreach($lists['enrollment_answers'] as $ea) {
							echo '<tr><td width: "30%"><strong>'.$ea->course_question.'</strong></td><td>'.($ea->user_answer?$ea->user_answer:'&nbsp;').'</td></tr>';							 
						}
						?>
					</table>
				</td>
			</tr>
			</table>
			<?php								
		} else {		
			?>
			<table width="100%" cellpadding="0" cellspacing = "0">
				<tr><td width="20%" align="left"><strong><?php echo _JLMS_UI_USERNAME; ?></strong></td><td align="left"><?php echo $rows[0]->username; ?></td></tr>
				<tr><td align="left"><strong><?php echo _JLMS_UI_NAME; ?></strong></td><td align="left"><?php echo $rows[0]->name; ?></td></tr>
				<tr><td align="left"><strong><?php echo _JLMS_UI_EMAIL; ?></strong></td><td align="left"><?php echo $rows[0]->email; ?></td></tr>
				<tr><td align="left"><strong><?php echo _JLMS_UI_GROUP; ?></strong></td><td align="left"><?php echo ($rows[0]->ug_name ? $rows[0]->ug_name : '&nbsp;'); ?></td></tr>
			</table>
			<?php			
		}		
		
		$html = ob_get_contents();	
		ob_end_flush();	
		ob_clean();		
		$pdf->writeHTML($html, true, false, false, false, '');
		
		$pdf->setY( $pdf->GetY() + 20 );
		
		/**
		 * Certificates MOD - 04.10.2007 (DEN)
		 * We will show the list of all achieved certificates in the User Gradebook
		 */
					
		$query = "SELECT count(*) FROM #__lms_certificates WHERE course_id = '".$id."' AND published = 1 AND crtf_type = 1 AND parent_id = 0";
		$JLMS_DB->SetQuery( $query );
		$is_course_certificate = $JLMS_DB->loadResult();
		if (!empty($lists['user_quiz_certificates']) || (isset($rows[0]->user_certificate) && $rows[0]->user_certificate && $is_course_certificate)) {
			$pdf->setFontSize( 10 );	
			$pdf->setFont( 'freesansb' ); //choose font								
			$pdf->Cell( 0, 10, _JLMS_GB_CERTIFICATES, '', 1, 'L' );
			$pdf->setFont( 'freesans' ); //choose font
			$pdf->setFontSize( 8 );									
			ob_clean();
			ob_start();									
			$old_format = $JLMS_CONFIG->get('date_format', 'Y-m-d');
			$new_format = $JLMS_CONFIG->get('gradebook_certificate_date', '');
			if ($new_format) {
				$JLMS_CONFIG->set('date_format', $new_format);
			}
			echo '<table width="100%" cellpadding="0" cellspacing="0" border="1">';			
			foreach ($lists['user_quiz_certificates'] as $crtf_row) 
			{
				?>				
				<tr>					
					<td width="50%" align="left">&nbsp;<strong><?php echo $crtf_row->quiz_name; ?></strong></td>
					<td align="center"><?php echo JLMS_dateToDisplay($crtf_row->crtf_date); ?></td>
				</tr>
				<?php				
			}
			if (isset($rows[0]->user_certificate) && $rows[0]->user_certificate && $is_course_certificate) {
				$cert_user_id = 0; 
				if (isset($lists['user_id']) && $lists['user_id']) {
					global $my;
					if ($my->id == $lists['user_id'] ) {
					} else {
						$cert_user_id = $lists['user_id'];
					}
				}
				?>				
				<tr>
					<td width="50%" align="left">&nbsp;<strong><?php echo _JLMS_GB_COURSE_CERTIFICATE; ?></strong></td>
					<td align="center"><?php echo JLMS_dateToDisplay($rows[0]->user_certificate_date); ?></td>
				</tr>
				<?php 
			}			
			$JLMS_CONFIG->set('date_format', $old_format);
			echo '</table>';
			$html = ob_get_contents();	
			ob_end_flush();	
			ob_clean();
			$pdf->writeHTML($html, true, false, false, false, '');
		}
		/* END of Certificates MOD */		
		if (count($lists['sc_rows'])) {			
			$is_shown = 0;					
			foreach ($lists['sc_rows'] as $sc_row) {
				if ($sc_row->show_in_gradebook) {
					$is_shown ++;
					if ($is_shown == 1) {						
						$pdf->setFontSize( 10 );	
						$pdf->setFont( 'freesansb' ); //choose font							
						$pdf->cell( 0, 10, _JLMS_GB_SCORM_RESULTS, '', 1, 'L' );
						$pdf->setFont( 'freesans' ); //choose font
						$pdf->setFontSize( 8 );						
						ob_clean();
						ob_start();
						echo '<table width="100%" cellpadding="0" cellspacing="0" border="1">';
					}	
					$j = 0;
					while ($j < count($rows[0]->scorm_info)) {
						if ($rows[0]->scorm_info[$j]->gbi_id == $sc_row->item_id) {
							if ($rows[0]->scorm_info[$j]->user_status == -1) {
								?>
								<tr>
									<td width="50%" align="left">&nbsp;<strong><?php echo $sc_row->lpath_name; ?></strong></td>
									<td width="20%" align="center"> - </td>
									<td width="30%" align="center"> </td>
								</tr>
								<?php								
							} else {
								$status = $rows[0]->scorm_info[$j]->user_status ? _JLMS_GB_SCORM_COMPLETED : _JLMS_GB_SCORM_INCOMPLETED;
								?>								
								<tr>
									<td width="50%" align="left">&nbsp;<strong><?php echo $sc_row->lpath_name; ?></strong></td>
									<td width="20%" align="center"><strong><?php echo $status; ?></strong></td>	
									<td width="30%" align="center"><b><?php echo $rows[0]->scorm_info[$j]->user_grade . "</b> (" . $rows[0]->scorm_info[$j]->user_pts . _JLMS_GB_POINTS .")"; ?></td>
								</tr>
								<?php								
							}
						}
						$j ++;
					}
				}
			}
			if( $is_shown ) {
				echo '</table>';
				$html = ob_get_contents();	
				ob_end_flush();	
				ob_clean();
				$pdf->writeHTML($html, true, false, false, false, '');
			}
					
		}
				
		if (count($lists['quiz_rows'])) {
			$pdf->setFontSize( 10 );	
			$pdf->setFont( 'freesansb' ); //choose font
			$pdf->cell( 0, 10, _JLMS_GB_QUIZ_RESULTS, '', 1, 'L' );
			$pdf->setFont( 'freesans' ); //choose font
			$pdf->setFontSize( 8 );			
			ob_clean();
			ob_start();									
			/*Integration Plugin Percentiles*/			
			echo '<table width="100%" cellpadding="0" cellspacing="0" border="1">';			
			foreach ($lists['quiz_rows'] as $quiz_row) {
				$j = 0;				
				
				while ($j < count($rows[0]->quiz_info)) {
					if ($rows[0]->quiz_info[$j]->gbi_id == $quiz_row->c_id) {						
						if ($rows[0]->quiz_info[$j]->user_status == -1) {
							?>
							<tr>
								<td width="50%" align="left">&nbsp;<strong><?php echo $quiz_row->c_title; ?></strong></td>
								<td width="20%" align="center"> - </td>
								<td width="30%" align="center"> </td>
							</tr>
							<?php							
						} else {
							$status = $rows[0]->quiz_info[$j]->user_status ? _JLMS_GB_SCORM_COMPLETED : _JLMS_RESULT_FAIL ;
							?>							
							<tr>
								<td width="50%" align="left">&nbsp;<strong><?php echo $quiz_row->c_title; ?></strong></td>
								<td width="20%" align="center"><strong><?php echo $status; ?></strong></td>
								<td width="30%" align="center">					
								<?php									
									if( $rows[0]->quiz_info[$j]->user_grade != '-' ) { 			
										echo"<b>".$rows[0]->quiz_info[$j]->user_grade."</b> (".$rows[0]->quiz_info[$j]->user_pts_full.")";
									} else {
										echo $rows[0]->quiz_info[$j]->user_pts_full;
									}
									
									if (isset($rows[0]->quiz_info[$j]->user_percentile)) {
										echo ' - '.$rows[0]->quiz_info[$j]->user_percentile;
									}
								?>
								</td>
							</tr>
						<?php					
						}
					}
					$j ++;
				}
			}
			echo '</table>';
			$html = ob_get_contents();	
			ob_end_flush();	
			ob_clean();			
			
			$pdf->writeHTML($html, true, false, false, false, '');				
		}	
		
		if (count($lists['gb_rows'])) {	
			$pdf->setFontSize( 10 );	
			$pdf->setFont( 'freesansb' ); //choose font
			$pdf->cell( 0, 10, _JLMS_GB_GBI_RESULTS, '', 1, 'L' );
			$pdf->setFont( 'freesans' ); //choose font
			$pdf->setFontSize( 8 );			
			ob_clean();
			ob_start();				
						
			echo '<table width="100%" cellpadding="0" cellspacing="0" border="1">';			
			foreach ($lists['gb_rows'] as $gb_row) {
				$j = 0;
				while ($j < count($rows[0]->grade_info)) {
					if ($rows[0]->grade_info[$j]->gbi_id == $gb_row->id) {
						?>
						<tr>
							<td width="50%" align="left">&nbsp;<strong><?php echo $gb_row->gbi_name; ?></strong></td>
							<td width="50%" align="center">
								<?php echo ($rows[0]->grade_info[$j]->user_grade != '-')?$rows[0]->grade_info[$j]->user_grade:''; ?>						
							</td>
						</tr>
						<?php
					}
					$j ++;
				}
			}
			echo '</table>';
			$html = ob_get_contents();	
			ob_end_flush();	
			ob_clean();
			
			$pdf->writeHTML($html, true, false, false, false, '');
		}				
			
		$pdf->Output( 'gradebook_'.$rows[0]->username.'_course'.$id.'.pdf', 'I' );
		
		die();
	}
	
function showUserGradebook( $id, $option, &$rows, &$lists ) {
	global $Itemid, $JLMS_CONFIG;
	$juser = JLMSFactory::getUser();
	$JLMS_ACL = & JLMSFactory::getACL();	
	if ($JLMS_ACL->CheckPermissions('gradebook', 'manage')) { ?>
<script language="javascript" type="text/javascript">
<!--
function submitbutton(pressbutton) {
	var form = document.adminForm;
	form.task.value = pressbutton;
	form.submit();
}
function submitbutton_crtf(pressbutton, state, cid_id) {
	var form = document.adminForm;
	if (pressbutton == 'gb_crtA'){
		form.task.value = pressbutton;
		form.state.value = state;
		form.cid2.value = cid_id;
		form.submit();
	}
}
//-->
</script>
	<?php } ?>

	<?php		
		echo '<table class="contentpane" id="joomlalms_usergradebook" style="width:100%" cellpadding="0" cellspacing="0" border="0">'."\r\n";

		$hparams = array();
		$toolbar = array();
		if ($JLMS_ACL->CheckPermissions('gradebook', 'manage')) {
			if (count($lists['gb_rows'])) {
				$toolbar[] = array('btn_type' => 'save', 'btn_js' => "javascript:submitbutton('save_grades');");
			}
			$toolbar[] = array('btn_type' => 'pdf', 'btn_js' => JLMSRoute::_("index.php?option=$option&Itemid=$Itemid&task=gb_user_pdf&course_id=$id&id=".$lists['user_id']));
			$toolbar[] = array('btn_type' => 'back', 'btn_js' => sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=gradebook&id=$id"));
		} else {
			$toolbar[] = array('btn_type' => 'pdf', 'btn_js' => JLMSRoute::_("index.php?option=$option&Itemid=$Itemid&task=gb_user_pdf&course_id=$id&id=".$lists['user_id']));
		}
		JLMS_TMPL::ShowHeader('gradebook', _JLMS_GB_TITLE, $hparams, $toolbar);

		$userinfo = array();
		$userinfo['text'] = JLMS_outputUserInfo($rows[0]->username,$rows[0]->name,$rows[0]->email,$rows[0]->ug_name, '20%');
		$crtf_txt = array();
		$crtf_txt['text'] = '';
		$crtf_txt['attrib'] = ' nowrap="nowrap"';
		if ($JLMS_ACL->CheckPermissions('gradebook', 'manage')) {
			$crtf_txt['text'] = '<table width="100%" cellpadding="0" cellspacing="0" border="0" class="jlms_table_no_borders"><tr><td width="30%" nowrap="nowrap">'._JLMS_COURSE_COMPLETED_ADMIN.'</td>';
			$crtf_txt['text'] .= '<td>&nbsp;&nbsp;';
			$image = $rows[0]->user_certificate ? 'btn_accept.png' : 'btn_cancel.png';
			$alt = $rows[0]->user_certificate ? _JLMS_GB_USER_HAVE_CRT : _JLMS_GB_USER_HAVE_NO_CRT;
			$state =  $rows[0]->user_certificate ? 0 : 1;
			$crtf_txt['text'] .= '<a class="jlms_img_link" href="javascript:submitbutton_crtf(\'gb_crtA\','.$state.','.$rows[0]->user_id.')" title="'.$alt.'"><img class="JLMS_png" src="'.$JLMS_CONFIG->get('live_site').'/components/com_joomla_lms/lms_images/toolbar/'.$image.'" width="16" height="16" border="0" alt="'.$alt.'" /></a>';
			$crtf_txt['text'] .= '</td></tr></table>';
		} elseif ($JLMS_ACL->CheckPermissions('gradebook', 'view') && $rows[0]->user_certificate) {
			
			$crtf_txt['text'] = '<table width="100%" cellpadding="0" cellspacing="0" border="0" id="usergradebook_header_crtf_info" class="jlms_table_no_borders"><tr><td width="30%">'.( $juser->get('id') == $rows[0]->user_id ? _JLMS_COURSE_COMPLETED_USER : _JLMS_COURSE_COMPLETED_ADMIN ).'</td>';
			$crtf_txt['text'] .= '<td>&nbsp;&nbsp;';
			$image = $rows[0]->user_certificate ? 'btn_accept.png' : 'btn_cancel.png';
			$alt = $rows[0]->user_certificate ? _JLMS_GB_USER_HAVE_CRT : _JLMS_GB_USER_HAVE_NO_CRT;
			$state =  $rows[0]->user_certificate ? 0 : 1;
			$crtf_txt['text'] .= JLMS_gradebook_html::publishIcon($rows[0]->user_id, $id, $state, 'gb_crtA', $alt, $image, $option, '' );
			$crtf_txt['text'] .= '</td></tr></table>';
		}
		$params_cs = array();
		if ($JLMS_ACL->CheckPermissions('gradebook', 'manage') && !empty($lists['enrollment_answers'])) {
			JLMS_TMPL::OpenTS('', ' id="usergradebook_header"');
			echo '<table width="100%" class="jlms_table_no_borders">';
			echo '<tr><td width="50%" valign="top"><table width="100%" cellpadding="0" cellspacing="0" border="0" id="usergradebook_header_user_info" class="'.JLMSCSS::_('jlmslist').'" style="margin:0px;padding:0px;">';
			echo '<tr><'.JLMSCSS::tableheadertag().' class="'.JLMSCSS::_('sectiontableheader').'" colspan="2">'._JLMS_GB_USER_INFORMATION.'</'.JLMSCSS::tableheadertag().'></tr>';
			echo '<tr class="'.JLMSCSS::_('sectiontableentry1').'"><td align="left">'._JLMS_UI_USERNAME.'</td><td align="left">'.$rows[0]->username.'</td></tr>';
			echo '<tr class="'.JLMSCSS::_('sectiontableentry2').'"><td align="left">'._JLMS_UI_NAME.'</td><td align="left">'.$rows[0]->name.'</td></tr>';
			echo '<tr class="'.JLMSCSS::_('sectiontableentry1').'"><td align="left">'._JLMS_UI_EMAIL.'</td><td align="left">'.$rows[0]->email.'</td></tr>';
			echo '<tr class="'.JLMSCSS::_('sectiontableentry2').'"><td align="left">'._JLMS_UI_GROUP.'</td><td align="left">'.($rows[0]->ug_name?$rows[0]->ug_name:'&nbsp;').'</td></tr>';

			echo '</table><br />';

			echo $crtf_txt['text'];

			echo '</td><td width="50%" valign="top"><table width="100%" cellpadding="0" cellspacing="0" border="0" id="usergradebook_header_reg_info" class="'.JLMSCSS::_('jlmslist').'" style="margin:0px;padding:0px;">';
			echo '<tr><'.JLMSCSS::tableheadertag().' class="'.JLMSCSS::_('sectiontableheader').'" colspan="2">'._JLMS_GB_REG_INFORMATION.'</'.JLMSCSS::tableheadertag().'></tr>';
			$k = 1;
			foreach($lists['enrollment_answers'] as $ea) {
				echo '<tr class="'.JLMSCSS::_('sectiontableentry'.$k).'"><td>'.$ea->course_question.'</td><td>'.($ea->user_answer?$ea->user_answer:'&nbsp;').'</td></tr>';
				$k = 3 - $k; 
			}
			echo '</table></td></tr>';
			echo '</table>';
			JLMS_TMPL::CloseTS();
		} else {
			$params_cs[] = $userinfo; $params_cs[] = $crtf_txt;
			JLMS_TMPL::ShowCustomSection($params_cs);
		}

		/**
		 * Certificates MOD - 04.10.2007 (DEN)
		 * We will show the list of all achieved certificates in the User Gradebook
		 */
		global $JLMS_DB;
		$query = "SELECT count(*) FROM #__lms_certificates WHERE course_id = '".$id."' AND published = 1 AND crtf_type = 1 AND parent_id = 0";
		$JLMS_DB->SetQuery( $query );
		$is_course_certificate = $JLMS_DB->loadResult();
		if (!empty($lists['user_quiz_certificates']) || (isset($rows[0]->user_certificate) && $rows[0]->user_certificate && $is_course_certificate)) {
			JLMS_TMPL::OpenTS('', ' id="usergradebook_certificates_title"');
			echo '<br />';
			echo JLMSCSS::h2(_JLMS_GB_CERTIFICATES);
			JLMS_TMPL::CloseTS();
			JLMS_TMPL::OpenTS();
			$old_format = $JLMS_CONFIG->get('date_format', 'Y-m-d');
			$new_format = $JLMS_CONFIG->get('gradebook_certificate_date', '');
			if ($new_format) {
				$JLMS_CONFIG->set('date_format', $new_format);
			}
			echo '<table class="'.JLMSCSS::_('jlmslist').'" id="usergradebook_certificates" style="width:100%" cellpadding="0" cellspacing="0" border="0">'."\r\n";
			$k = 2;
			foreach ($lists['user_quiz_certificates'] as $crtf_row) {
				echo '<tr class="'.JLMSCSS::_('sectiontableentry'.$k).'"><td width="16"><img class="JLMS_png" src="'.$JLMS_CONFIG->get('live_site').'/components/com_joomla_lms/lms_images/toolbar/btn_certificate.png" alt="certificate" width="16" height="16" /></td><td width="30%" align="left"><strong>'.$crtf_row->quiz_name.'</strong></td>'
				. '<td>'.JLMS_offsetDateToDisplay($crtf_row->crtf_date).'</td>'
				. '<td align=\'left\'>'
				. (($JLMS_ACL->CheckPermissions('gradebook', 'view')) ? ('<a target="_blank" href="'.ampReplace($JLMS_CONFIG->get('live_site')."/index.php?tmpl=component&option=$option&Itemid=$Itemid&no_html=1&task=print_quiz_cert&course_id=$id&stu_quiz_id=$crtf_row->stu_quiz_id&user_unique_id=$crtf_row->user_unique_id").'">'._JLMS_GB_PRINT_CERTIFICATE.'</a>') : '')
				. '</td></tr>';
				$k = 3 - $k;
			}
			if (isset($rows[0]->user_certificate) && $rows[0]->user_certificate && $is_course_certificate) {
				$cert_user_id = 0; 
				if (isset($lists['user_id']) && $lists['user_id']) {
					global $my;
					if ($my->id == $lists['user_id'] ) {
					} else {
						$cert_user_id = $lists['user_id'];
					}
				}
				$link = ampReplace(sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid&amp;task=gb_get_cert&amp;id=$id".($cert_user_id ? "&amp;user_id=$cert_user_id" : '')));
				echo '<tr class="'.JLMSCSS::_('sectiontableentry'.$k).'"><td width="16"><img class="JLMS_png" src="'.$JLMS_CONFIG->get('live_site').'/components/com_joomla_lms/lms_images/toolbar/btn_certificate.png" alt="certificate" width="16" height="16" /></td><td width="30%" align="left"><strong>'._JLMS_GB_COURSE_CERTIFICATE.'</strong></td>'
					. '<td>'.JLMS_offsetDateToDisplay($rows[0]->user_certificate_date).'</td>'
					. '<td align=\'left\'>'
					. (($JLMS_ACL->CheckPermissions('gradebook', 'view')) ? ('<a target="_blank" href="'.$link.'">'._JLMS_GB_PRINT_CERTIFICATE.'</a>') : '&nbsp;' )
					. '</td></tr>';
			}
			JLMS_TMPL::CloseMT();
			JLMS_TMPL::CloseTS();
			$JLMS_CONFIG->set('date_format', $old_format);
		}
		/* END of Certificates MOD */



		if (count($lists['sc_rows'])) { 
			$k = 2;
			$is_shown = 0;
			foreach ($lists['sc_rows'] as $sc_row) {
				if ($sc_row->show_in_gradebook) {
					$is_shown ++;
					if ($is_shown == 1) {
						JLMS_TMPL::OpenTS('', ' id="usergradebook_scorms_title"');
						echo '<br />';
						echo JLMSCSS::h2(_JLMS_GB_SCORM_RESULTS);
						JLMS_TMPL::CloseTS();
						JLMS_TMPL::OpenTS();
						echo '<table class="'.JLMSCSS::_('jlmslist').'" id="usergradebook_scorms" style="width:100%" cellpadding="0" cellspacing="0" border="0">'."\r\n";
					}	
					$j = 0;
					while ($j < count($rows[0]->scorm_info)) {
						if ($rows[0]->scorm_info[$j]->gbi_id == $sc_row->item_id) {
							if ($rows[0]->scorm_info[$j]->user_status == -1) {
								echo '<tr class="'.JLMSCSS::_('sectiontableentry'.$k).'"><td width="30%" align="left"><strong>'.$sc_row->lpath_name.'</strong></td>'
								.'<td align=\'left\' colspan="2">'
								. '-'
								. '</td></tr>';
								$k = 3 - $k;
							} else {
								$image = $rows[0]->scorm_info[$j]->user_status ? 'btn_accept.png' : 'btn_cancel.png';
								$alt = $rows[0]->scorm_info[$j]->user_status ? 'btn_accept' : 'btn_cancel';
								$img = JLMS_gradebook_html::publishIcon(0, 0, 0, '', $alt, $image, $option, false );
								echo '<tr class="'.JLMSCSS::_('sectiontableentry'.$k).'"><td width="30%" align="left"><strong>'.$sc_row->lpath_name.'</strong></td>'
								. '<td width="16">'. $img.'</td>'
								. '<td align=\'left\'>'
								. '<b>' . $rows[0]->scorm_info[$j]->user_grade . "</b> (" . $rows[0]->scorm_info[$j]->user_pts . _JLMS_GB_POINTS .")"
								. '</td></tr>';
								$k = 3 - $k;
							}
						}
						$j ++;
					}
				}
			}
			if ($is_shown) {
				JLMS_TMPL::CloseMT();
				JLMS_TMPL::CloseTS();
			}
		}

		if (count($lists['quiz_rows'])) { 
			JLMS_TMPL::OpenTS('', ' id="usergradebook_quizzes_title"');
			echo '<br />';
			echo JLMSCSS::h2(_JLMS_GB_QUIZ_RESULTS);
			JLMS_TMPL::CloseTS();
			JLMS_TMPL::OpenTS();
			
			/*Integration Plugin Percentiles*/
			if(isset($lists['chart_percentiles']->show) && $lists['chart_percentiles']->show){
				JLMS_TMPL::OpenTS();
				echo $lists['chart_percentiles']->chart;
				JLMS_TMPL::CloseTS();
			}
			/*Integration Plugin Percentiles*/
			
			echo '<table class="'.JLMSCSS::_('jlmslist').'" id="usergradebook_quizzes" style="width:100%" cellpadding="0" cellspacing="0" border="0">'."\r\n";
			$k = 2;
			foreach ($lists['quiz_rows'] as $quiz_row) {
				$j = 0;
				while ($j < count($rows[0]->quiz_info)) {
					if ($rows[0]->quiz_info[$j]->gbi_id == $quiz_row->c_id) {
						
//						if ($rows[0]->quiz_info[$j]->user_status == -1) {
//							echo '<tr class="'.JLMSCSS::_('sectiontableentry'.$k).'"><td width="30%" align="left"><strong>'.$quiz_row->c_title.'</strong></td>'
//							. '<td align=\'left\' colspan="2">'
//							. '-'
//							. '</td></tr>';
//							$k = 3 - $k;
//						} else {
//							$image = $rows[0]->quiz_info[$j]->user_status ? 'btn_accept.png' : 'btn_cancel.png';
//							$alt = $rows[0]->quiz_info[$j]->user_status ? 'btn_accept' : 'btn_cancel';
//							$img = JLMS_gradebook_html::publishIcon(0, 0, 0, '', $alt, $image, $option, false );
							
							echo '<tr class="'.JLMSCSS::_('sectiontableentry'.$k).'"><td width="30%" align="left"><strong>'.$quiz_row->c_title.'</strong></td>';
							
							//old functional
//							echo '<td width="16">'. $img.'</td>';
//							echo '<td align=\'left\'>'
//							. '<b>' . $rows[0]->quiz_info[$j]->user_grade . "</b> (" . $rows[0]->quiz_info[$j]->user_pts_full .")";
//							/*Integration Plugin Percentiles*/
//							if (isset($rows[0]->quiz_info[$j]->user_percentile)) {
//								echo ' - '.$rows[0]->quiz_info[$j]->user_percentile;
//							}
//							/*Integration Plugin Percentiles*/
//							echo '</td>';
							
							//new functional
							echo '<td>';
								echo JLMS_showQuizStatus($rows[0]->quiz_info[$j]);	
							echo '</td>';

							if (isset($rows[0]->quiz_info[$j]->user_unique_id)) {
								$show_print = false;
								if (isset($rows[0]->quiz_info[$j]->allow_user_pdf_print) && $rows[0]->quiz_info[$j]->allow_user_pdf_print) {
									$show_print = true;
								} elseif ($JLMS_ACL->CheckPermissions('gradebook', 'manage')) {
									$show_print = true;
								} elseif ($JLMS_ACL->isStaff()) {
									$show_print = true;
								}
								if ($show_print) {
									echo '' 
									. '<td><a target="_blank" href="'
		                        	. $JLMS_CONFIG->get('live_site') .'/index.php?tmpl=component&amp;option=com_joomla_lms&amp;Itemid='.$Itemid.'&amp;no_html=1&amp;task=print_quiz_result&amp;course_id='.$quiz_row->course_id
		                        	. '&amp;stu_quiz_id='.$rows[0]->quiz_info[$j]->stu_quiz_id.'&amp;user_unique_id='.$rows[0]->quiz_info[$j]->user_unique_id.'">'
									. _JLMS_PRINT_RESULTS.'</a></td>';
								} else {
									echo '<td>';
										echo '&nbsp;';
									echo '</td>';
								}
							} else {
								echo '<td>';
									echo '&nbsp;';
								echo '</td>';
							}
							echo '</tr>';
							$k = 3 - $k;
//						}
					}
					$j ++;
				}
			}
			JLMS_TMPL::CloseMT();
			JLMS_TMPL::CloseTS();
		}

		if (count($lists['gb_rows'])) { 
			JLMS_TMPL::OpenTS('', ' id="usergradebook_grades_title"');
			echo '<br />';
			echo JLMSCSS::h2(_JLMS_GB_GBI_RESULTS);
			JLMS_TMPL::CloseTS();
			JLMS_TMPL::OpenTS();
			?>
			<form action="<?php echo sefRelToAbs("index.php?option=$option&Itemid=$Itemid");?>" method="post" name="adminForm">

			<?php
			echo '<table class="'.JLMSCSS::_('jlmslist').'" id="usergradebook_grades" style="width:100%" cellpadding="0" cellspacing="0" border="0">'."\r\n";
			$k = 2;
			foreach ($lists['gb_rows'] as $gb_row) {
				$j = 0;
				while ($j < count($rows[0]->grade_info)) {
					if ($rows[0]->grade_info[$j]->gbi_id == $gb_row->id) {
						echo '<tr class="'.JLMSCSS::_('sectiontableentry'.$k).'"><td width="30%" align="left"><strong>'.$gb_row->gbi_name.'</strong></td>'
						. '<td align=\'left\' colspan="2">'
						. ( ($JLMS_ACL->CheckPermissions('gradebook', 'manage')) ? JLMS_gradebook_html::Create_SelectList( $id, $rows[0]->grade_info[$j]->scale_id, $gb_row->id) : $rows[0]->grade_info[$j]->user_grade )// . _JLMS_GB_POINTS
						;
						echo '</td></tr>';
						$k = 3 - $k;
					}
					$j ++;
				}
			}
			JLMS_TMPL::CloseMT();
			?>
				<input type="hidden" name="option" value="<?php echo $option;?>" />
				<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
				<input type="hidden" name="task" value="gradebook" />
				<input type="hidden" name="state" value="0" />
				<input type="hidden" name="cid2" value="0" />
				<input type="hidden" name="boxchecked" value="0" />
				<input type="hidden" name="id" value="<?php echo $id;?>" />
				<input type="hidden" name="user_id" value="<?php echo $lists['user_id'];?>" />
			</form>	
			<?php
			JLMS_TMPL::CloseTS();
			JLMS_TMPL::CloseMT();
		} else {
			JLMS_TMPL::CloseMT();
			?>
			<form action="<?php echo sefRelToAbs("index.php?option=$option&Itemid=$Itemid");?>" method="post" name="adminForm">
				<input type="hidden" name="option" value="<?php echo $option;?>" />
				<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
				<input type="hidden" name="task" value="gradebook" />
				<input type="hidden" name="state" value="0" />
				<input type="hidden" name="cid2" value="0" />
				<input type="hidden" name="boxchecked" value="0" />
				<input type="hidden" name="id" value="<?php echo $id;?>" />
				<input type="hidden" name="user_id" value="<?php echo $lists['user_id'];?>" />
			</form>	
			<?php
		}
		
?>
	
<?php		
	}
	function Create_SelectList( $course_id, $cur_pts, $box_name) {
		global $JLMS_DB;
		$query = "SELECT id as value, scale_name as text FROM #__lms_gradebook_scale WHERE course_id = $course_id ORDER BY ordering, scale_name";
		$JLMS_DB->SetQuery( $query );
		$gb_scale = array();
		$gb_scale[] = mosHTML::makeOption(0, '-');
		$gb_scale = array_merge($gb_scale, $JLMS_DB->LoadObjectList());

		$hidden_field = '<input type="hidden" name="gb_item[]" value="'.$box_name.'" />';
		$list = mosHTML::SelectList( $gb_scale, 'gbi_val[]', 'class="inputbox" size="1" style="width:75px"', 'value', 'text', $cur_pts);
		return $hidden_field . $list;
	}
	function showCourseCertificate( $course_id, $option, $row, $lists ) {
		global $Itemid;?>
<script language="JavaScript" type="text/javascript">
<!--//--><![CDATA[//><!--
function CRT_save(pressbutton) {
	var form = document.adminForm;
		form.task.value = pressbutton;
		form.submit();
}
function CRT_preview(txt_tooltip) {

}
//--><!]]>
</script>


<?php
		JLMS_TMPL::OpenMT();

		$hparams = array();
		$toolbar = array();
		$toolbar[] = array('btn_type' => 'save', 'btn_js' =>"javascript:CRT_save('crt_save')");		
		$toolbar[] = array('btn_type' => 'preview', 'btn_js' => "javascript:jlms_ShowCertificatePreview()");
		$toolbar[] = array('btn_type' => 'back', 'btn_js' => sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid&amp;task=gradebook&amp;id=$course_id"));
		JLMS_TMPL::ShowHeader('certificate', _JLMS_GBC_TITLE, $hparams, $toolbar);

		JLMS_TMPL::OpenTS();
?>
	<form action="<?php echo sefRelToAbs("index.php?option=$option&Itemid=$Itemid");?>" method="post" name="adminForm" enctype="multipart/form-data">

<?php
		require_once(_JOOMLMS_FRONT_HOME . "/includes/lms_certificates.php");
		JLMS_Certificates::JLMS_editCertificate_Page($row, true);
?>
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
		<input type="hidden" name="task" value="gb_certificates" />
		<input type="hidden" name="state" value="0" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="id" value="<?php echo $course_id;?>" />
	</form>		
<?php
		JLMS_TMPL::CloseTS();
		JLMS_TMPL::CloseMT();
	}
	function publishIcon( $id, $course_id, $state, $task, $alt, $image, $option, $href = true ) {
		global $JLMS_CONFIG;
		$ret_str = '';
		if ($href) {
			global $Itemid;
			$link = "index.php?option=".$option."&amp;Itemid=".$Itemid."&amp;task=".$task."&amp;state=".$state."&amp;id=".$course_id."&amp;cid[]=".$id;
			$ret_str .= '<a class="jlms_img_link" href="'.$link.'" title="'.$alt.'">';
		}
			$ret_str .= '<img class="JLMS_png" src="'.$JLMS_CONFIG->get('live_site').'/components/com_joomla_lms/lms_images/toolbar/'.$image.'" width="16" height="16" border="0" alt="'.$alt.'" />';
		if ($href) {
			$ret_str .= '</a>';
		}
		return $ret_str;
	}
	function GB_Menu($id, $option = 'com_joomla_lms'){
		global $Itemid, $JLMS_CONFIG;?>
<script language="JavaScript" type="text/javascript">
<!--//--><![CDATA[//><!--
	function jlms_Show_GB_ToolTip(txt_tooltip) {
		var gb_tt = getObj('JLMS_toolbar_tooltip_gb');
		gb_tt.innerHTML = txt_tooltip;
	}
//--><!]]>
</script>
		<table align="center">
			<tr><td align="center"><span id="JLMS_toolbar_tooltip_gb">&nbsp;</span></td></tr>
		</table>
		<table align="center">
			<tr>
				<td align="center" nowrap="nowrap">
				<a onmouseover='javascript:jlms_Show_GB_ToolTip("<?php echo _JLMS_GB_MENU_SCALE;?>");jlms_WStatus("<?php echo _JLMS_GB_MENU_SCALE;?>");return true;' onmouseout='javascript:jlms_Show_GB_ToolTip("&nbsp;");jlms_WStatus("");return true;' href="<?php echo sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid&amp;task=gb_scale&amp;course_id=".$id);?>">
				<img src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/gradebook/gb_scale.png" class="JLMS_png" alt="<?php echo _JLMS_GB_MENU_SCALE;?>" title="<?php echo _JLMS_GB_MENU_SCALE;?>"  width="22" height="22"  border="0"/>
				</a>&nbsp;&nbsp;
				<a onmouseover='javascript:jlms_Show_GB_ToolTip("<?php echo _JLMS_GB_MENU_ITEMS;?>");jlms_WStatus("<?php echo _JLMS_GB_MENU_ITEMS;?>");return true;' onmouseout='javascript:jlms_Show_GB_ToolTip("&nbsp;");jlms_WStatus("");return true;' href="<?php echo sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid&amp;task=gb_items&amp;id=".$id);?>">
				<img src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/gradebook/gb_items.png" class="JLMS_png" alt="<?php echo _JLMS_GB_MENU_ITEMS;?>" title="<?php echo _JLMS_GB_MENU_ITEMS;?>"  width="22" height="22"  border="0"/>
				</a>&nbsp;&nbsp;
				<a onmouseover='javascript:jlms_Show_GB_ToolTip("<?php echo _JLMS_GB_MENU_CERTS;?>");jlms_WStatus("<?php echo _JLMS_GB_MENU_CERTS;?>");return true;' onmouseout='javascript:jlms_Show_GB_ToolTip("&nbsp;");jlms_WStatus("");return true;' href="<?php echo sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid&amp;task=gb_certificates&amp;id=".$id);?>">
				<img src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/gradebook/gb_certificates.png" class="JLMS_png" alt="<?php echo _JLMS_GB_MENU_CERTS;?>" title="<?php echo _JLMS_GB_MENU_CERTS;?>" width="22" height="22"  border="0"/>
				</a>
				</td>
			</tr>
		</table>
<?php 
	}
}
?>