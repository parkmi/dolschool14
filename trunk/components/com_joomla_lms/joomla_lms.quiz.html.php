<?php
/**
* joomla_lms.quiz.html.php
* (c) JoomaLMS eLearning Software http://www.joomlalms.com/
**/

// no direct access
defined( '_JLMS_EXEC' ) or die( 'Restricted access' );

class JLMS_quiz_html {
	function ShowCertificates( $option, $id, &$rows ) {
		global $Itemid;
		$JLMS_CONFIG = & JLMSFactory::getConfig();
		$toolbar = array();
		$toolbar[] = array('btn_type' => 'new', 'btn_txt' => _JLMS_QUIZ_NEW_CRTF_BTN, 'btn_js' => sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid&amp;id=$id&amp;task=quizzes&amp;page=add_crtf"));
		$toolbar[] = array('btn_type' => 'edit', 'btn_txt' => _JLMS_QUIZ_EDIT_CRTF_BTN, 'btn_js' => "javascript:submitbutton('edit_crtf');");
		$toolbar[] = array('btn_type' => 'del', 'btn_txt' => _JLMS_QUIZ_DEL_CRTF_BTN, 'btn_js' => "javascript:submitbutton('del_crtf');");
		$toolbar[] = array('btn_type' => 'preview', 'btn_txt' => _JLMS_QUIZ_PREVIEW_CRTF_BTN, 'btn_js' => "javascript:submit_preview();");

		JLMS_quiz_admin_html_class::showQuizHead( $id, $option, _JLMS_QUIZ_CERTIFICATES_TITLE, true, $toolbar );
		?>
<script language="javascript" type="text/javascript">
<!--
function submitbutton(pressbutton) {
	var form = document.adminForm;
	if ( ((pressbutton == 'edit_crtf') || (pressbutton == 'del_crtf') ) && (form.boxchecked.value == "0")) {
		alert('<?php echo _JLMS_ALERT_SELECT_ITEM;?>');
	} else {
		form.page.value = pressbutton;
		form.submit();
	}
}
function submit_preview() {
	var crtf_id = 0;
	var form = document.adminForm;
	if (form.boxchecked.value == "0") {
		alert('<?php echo _JLMS_ALERT_SELECT_ITEM;?>');
	} else {
		var selItem = document.adminForm['cid[]'];
		if (selItem) {
			if (selItem.length) { var i;
				for (i = 0; i<selItem.length; i++) {
					if (selItem[i].checked) {
						if (selItem[i].value > 0) { crtf_id = selItem[i].value; break; }
					}
				}
			} else if (selItem.checked) { crtf_id = selItem.value; }
		}
		if (crtf_id != '0' && crtf_id != '0') {
			window.open('<?php echo $JLMS_CONFIG->get('live_site')."/index.php?tmpl=component&option=com_joomla_lms&Itemid=$Itemid&no_html=1&task=quizzes&id=$id&page=preview_crtf&crtf_id='+crtf_id+'";?>');
		}
	}
}
//-->
</script>
		<form action="<?php echo sefRelToAbs("index.php?option=$option&Itemid=$Itemid");?>" method="post" name="adminForm">
		<table width="100%" cellpadding="0" cellspacing="0" border="0" class="<?php echo JLMSCSS::_('jlmslist');?>">
			<tr>
				<<?php echo JLMSCSS::tableheadertag();?> width="20" class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center">#</<?php echo JLMSCSS::tableheadertag();?>>
				<<?php echo JLMSCSS::tableheadertag();?> width="20" class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></<?php echo JLMSCSS::tableheadertag();?>>
				<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>"><?php echo _JLMS_QUIZ_TBL_CRTF_NAME;?></<?php echo JLMSCSS::tableheadertag();?>>
			</tr>
		<?php
		$k = 1;
		for ($i=0, $n=count($rows); $i < $n; $i++) {
			$row = $rows[$i];
			#$link 	= sefRelToAbs("index.php?option=".$option."&amp;Itemid=".$Itemid."&amp;task=quizzes&amp;id=$id&amp;page=quizzes&amp;cat_id=". $row->c_id);
			$checked = mosHTML::idBox( $i, $row->id);?>
			<tr class="<?php echo JLMSCSS::_('sectiontableentry'.$k); ?>">
				<td align="center"><?php echo $i + 1; ?></td>
				<td align="center"><?php echo $checked; ?></td>
				<td align="left">
					<?php echo $row->crtf_name ? $row->crtf_name : $row->crtf_text;?>
				</td>
			</tr>
			<?php
			$k = 3 - $k;
		}?>
		</table>
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="page" value="" />
		<input type="hidden" name="task" value="quizzes" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="id" value="<?php echo $id;?>" />
		</form>
		<?php JLMS_quiz_admin_html_class::showQuizFooter(); ?>
<?php
	}
	function ShowCertificate( $course_id, $option, &$row, &$lists ) {
		global $Itemid;
		$toolbar = array();
		$toolbar[] = array('btn_type' => 'save', 'btn_txt' => _JLMS_QUIZ_SAVE_CRTF_BTN, 'btn_js' => "javascript:submitbutton('save_crtf');");
		$toolbar[] = array('btn_type' => 'apply', 'btn_txt' => _JLMS_QUIZ_SAVE_CRTF_BTN, 'btn_js' => "javascript:submitbutton('apply_crtf');");
		//$toolbar[] = array('btn_type' => 'preview', 'btn_txt' => _JLMS_QUIZ_PREVIEW_CRTF_BTN, 'btn_js' => sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=quizzes&id=$course_id&page=preview_crtf&crtf_id=".$row->id), 'btn_target' => '_blank');
		$toolbar[] = array('btn_type' => 'preview', 'btn_txt' => _JLMS_QUIZ_PREVIEW_CRTF_BTN, 'btn_js' => "javascript:jlms_ShowCertificatePreview()");
		$toolbar[] = array('btn_type' => 'cancel', 'btn_txt' => _JLMS_QUIZ_CANCEL_BTN, 'btn_js' => "javascript:submitbutton('cancel_crtf');");
		$h = $row->id ? _JLMS_QUIZ_EDIT_CRTF_BTN : _JLMS_QUIZ_NEW_CRTF_BTN ;
		JLMS_quiz_admin_html_class::showQuizHead( $course_id, $option, $h, true, $toolbar );
		require_once(_JOOMLMS_FRONT_HOME . "/includes/lms_certificates.php");
		?>
<script language="javascript" type="text/javascript">
<!--
function submitbutton(pressbutton) {
	var proceed_to_submit = true;
	var form = document.adminForm;
	if (pressbutton == 'save_crtf' || pressbutton == 'apply_crtf') {
		if (form.crtf_name.value == '') {
			alert('<?php echo _JLMS_PL_ENTER_NAME;?>');
			proceed_to_submit = false;
		}
	}
	if (proceed_to_submit) {
		form.page.value = pressbutton;
		form.submit();
	}
}
//-->
</script>
		<form action="<?php echo sefRelToAbs("index.php?option=$option&Itemid=$Itemid");?>" method="post" name="adminForm" enctype="multipart/form-data">
		<table width="100%" cellpadding="0" cellspacing="0" border="0" class="jlms_table_no_borders">
			<tr>
				<td width="100%">
				<?php JLMS_Certificates::JLMS_editCertificate_Page( $row );?>
				</td>
			</tr>
		</table>
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="page" value="" />
		<input type="hidden" name="task" value="quizzes" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="id" value="<?php echo $course_id;?>" />
		<input type="hidden" name="crtf_id" value="<?php echo $row->id;?>" />
		</form>
		<?php JLMS_quiz_admin_html_class::showQuizFooter(); ?>
<?php
	}
}
?>