<?php
/**
* includes/lms_certificates.php
* (c) JoomaLMS eLearning Software http://www.joomlalms.com/
**/
/*
	!!! for rigth work require gradebook.lang.php
*/

// no direct access
defined( '_JLMS_EXEC' ) or die( 'Direct Access to this location is not allowed.' );
class JLMS_Certificates {
function JLMS_editCertificate_Page( &$row, $from_gb = false ) {
	global $Itemid, $JLMS_CONFIG;
	$JLMS_ACL = & JLMSFactory::getACL();
	$lroles = $JLMS_ACL->GetSystemRoles(1);
	if (true/*count($lroles) > 1*/) { ?>
<script language="javascript" type="text/javascript">
<!--//--><![CDATA[//><!--
function jlms_changeCrtfDefaultValue(element, form_suffix) {
	var form = element.form;
	var is_dis = element.checked;
	form['userfile_'+form_suffix].disabled = is_dis;
	form['crtf_text_'+form_suffix].disabled = is_dis;
	var ca_Item = form['crtf_align_'+form_suffix];
	if (ca_Item) {
		if (ca_Item.length) {
			var i;
			for (i = 0; i<ca_Item.length; i++) {
				ca_Item[i].disabled = is_dis;
			}
		} else { ca_Item.disabled = is_dis; }
	}
	var cs_Item = form['crtf_shadow_'+form_suffix];
	if (cs_Item) {
		if (cs_Item.length) {
			var i;
			for (i = 0; i<cs_Item.length; i++) {
				cs_Item[i].disabled = is_dis;
			}
		} else { cs_Item.disabled = is_dis; }
	}
	form['text_x_'+form_suffix].disabled = is_dis;
	form['text_y_'+form_suffix].disabled = is_dis;
	form['text_size_'+form_suffix].disabled = is_dis;
	form['crtf_font_'+form_suffix].disabled = is_dis;
	var c1_Item = form['ctxt_mes_text_'+form_suffix+'[]'];
	if (c1_Item) {
		if (c1_Item.length) {
			var i;
			for (i = 0; i<c1_Item.length; i++) {
				c1_Item[i].disabled = is_dis;
			}
		} else { c1_Item.disabled = is_dis; }
	}
	var c2_Item = form['ctxt_mes_shadow_'+form_suffix+'[]'];
	if (c2_Item) {
		if (c2_Item.length) {
			var i;
			for (i = 0; i<c2_Item.length; i++) {
				c2_Item[i].disabled = is_dis;
			}
		} else { c2_Item.disabled = is_dis; }
	}
	var c3_Item = form['ctxt_mes_x_'+form_suffix+'[]'];
	if (c3_Item) {
		if (c3_Item.length) {
			var i;
			for (i = 0; i<c3_Item.length; i++) {
				c3_Item[i].disabled = is_dis;
			}
		} else { c3_Item.disabled = is_dis; }
	}
	var c4_Item = form['ctxt_mes_y_'+form_suffix+'[]'];
	if (c4_Item) {
		if (c4_Item.length) {
			var i;
			for (i = 0; i<c4_Item.length; i++) {
				c4_Item[i].disabled = is_dis;
			}
		} else { c4_Item.disabled = is_dis; }
	}
	var c5_Item = form['ctxt_mes_h_'+form_suffix+'[]'];
	if (c5_Item) {
		if (c5_Item.length) {
			var i;
			for (i = 0; i<c5_Item.length; i++) {
				c5_Item[i].disabled = is_dis;
			}
		} else { c5_Item.disabled = is_dis; }
	}
	var c6_Item = form['ctxt_mes_font_'+form_suffix+'[]'];
	if (c6_Item) {
		if (c6_Item.length) {
			var i;
			for (i = 0; i<c6_Item.length; i++) {
				c6_Item[i].disabled = is_dis;
			}
		} else { c6_Item.disabled = is_dis; }
	}
	form['new_txt_message_'+form_suffix].disabled = is_dis;
	form['new_txt_mes_shadow_'+form_suffix].disabled = is_dis;
	form['new_txt_message_X_'+form_suffix].disabled = is_dis;
	form['new_txt_message_Y_'+form_suffix].disabled = is_dis;
	form['new_txt_message_H_'+form_suffix].disabled = is_dis;
	form['new_txt_mes_font_'+form_suffix].disabled = is_dis;
	form['add_new_field_'+form_suffix].disabled = is_dis;
}
function jlms_ShowCertificatePreview() {
	var crtf_roles = new Array();
	crtf_roles[0] = 0;
	<?php $i = 1;foreach ($lroles as $lr) { echo 'crtf_roles['.$i.'] = '.$lr->id.';';$i++;}?>
	
	var crtf_id = <?php echo $row->id?$row->id:0;?>;
	if (crtf_id != '0' && crtf_id != 0 && crtf_id != '') {
		var crtf_role_num = 0;
		var iii = 0;
		if (crtf_roles.length) {
			$$('h2.tab').each(function(ael){if (ael.hasClass('selected')) {crtf_role_num = iii;}iii++;});
		}
		var crtf_role = crtf_roles[crtf_role_num];
		<?php if ($from_gb) { ?>
			window.open('<?php echo $JLMS_CONFIG->getCfg('live_site'). "/index.php?tmpl=component&option=com_joomla_lms&Itemid=$Itemid&no_html=1&task=crt_preview&id=$row->course_id";?>&crtf_id='+crtf_id+'&crtf_role='+crtf_role);
		<?php } else { ?>
			window.open('<?php echo $JLMS_CONFIG->getCfg('live_site'). "/index.php?tmpl=component&option=com_joomla_lms&Itemid=$Itemid&no_html=1&task=quizzes&id=$row->course_id&page=preview_crtf";?>&crtf_id='+crtf_id+'&crtf_role='+crtf_role);		
		<?php } ?>
	}
}



function ReAnalize_tbl_Rows( start_index, tbl_id ) {
	start_index = 1;
	var tbl_elem = getObj(tbl_id);
	if (tbl_elem.rows[start_index]) {
		var count = start_index; var row_k = 2 - start_index%2;//0;
		for (var i=start_index; i<(tbl_elem.rows.length - 1); i++) {
			tbl_elem.rows[i].cells[0].innerHTML = count;

			if (i > 1) { 
				tbl_elem.rows[i].cells[8].innerHTML = '<a class="jlms_img_link" href="javascript: void(0);" onclick="javascript:Up_tbl_row(this); return false;" title="<?php echo _JLMS_MOVEUP;?>"><img src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_uparrow.png" width="16" height="16" border="0" alt="<?php echo _JLMS_MOVEUP;?>" \/><\/a>';
			} else { tbl_elem.rows[i].cells[8].innerHTML = '&nbsp;'; }
			if (i < (tbl_elem.rows.length - 2)) {
				tbl_elem.rows[i].cells[9].innerHTML = '<a class="jlms_img_link" href="javascript: void(0);" onclick="javascript:Down_tbl_row(this); return false;" title="<?php echo _JLMS_MOVEDOWN;?>"><img src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_downarrow.png" width="16" height="16" border="0" alt="<?php echo _JLMS_MOVEDOWN;?>" \/><\/a>';;
			} else { tbl_elem.rows[i].cells[9].innerHTML = '&nbsp;'; }
			if (row_k == 1) {
				tbl_elem.rows[i].className = '<?php echo JLMSCSS::_('sectiontableentry1');?>';
			} else {
				tbl_elem.rows[i].className = '<?php echo JLMSCSS::_('sectiontableentry2');?>';
			}
			count++;
			row_k = 3 - row_k;
		}
	}
}
function Delete_tbl_row(element) {
	var del_index = element.parentNode.parentNode.sectionRowIndex;
	var tbl_id = element.parentNode.parentNode.parentNode.parentNode.id;
	element.parentNode.parentNode.parentNode.deleteRow(del_index);
	ReAnalize_tbl_Rows(del_index - 1, tbl_id);
}

function Up_tbl_row(element) {
	if (element.parentNode.parentNode.sectionRowIndex > 1) {
		var sec_indx = element.parentNode.parentNode.sectionRowIndex;
		var table = element.parentNode.parentNode.parentNode;
		var tbl_id = table.parentNode.id;

		var cell1 = document.createElement("td");
		cell1.align = 'center';
		var row = table.insertRow(sec_indx - 1);
		row.appendChild(cell1);
		row.appendChild(element.parentNode.parentNode.cells[1]);
		row.appendChild(element.parentNode.parentNode.cells[1]);
		row.appendChild(element.parentNode.parentNode.cells[1]);
		row.appendChild(element.parentNode.parentNode.cells[1]);
		row.appendChild(element.parentNode.parentNode.cells[1]);
		row.appendChild(element.parentNode.parentNode.cells[1]);
		element.parentNode.parentNode.parentNode.deleteRow(element.parentNode.parentNode.sectionRowIndex);

		var ceLL8 = document.createElement("td");
		var ceLL9 = document.createElement("td");
		var ceLL10 = document.createElement("td");

		ceLL8.innerHTML = '<a class="jlms_img_link" href="javascript: void(0);" onclick="javascript:Delete_tbl_row(this); return false;" title="Delete"><img src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_cancel.png" width="16" height="16" border="0" alt="Delete" \/><\/a>';
		ceLL9.innerHTML = '<a class="jlms_img_link" href="javascript: void(0);" onclick="javascript:Up_tbl_row(this); return false;" title="<?php echo _JLMS_MOVEUP;?>"><img src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_uparrow.png" width="16" height="16" border="0" alt="<?php echo _JLMS_MOVEUP;?>" \/><\/a>';
		ceLL10.innerHTML = '&nbsp;';

		row.appendChild(ceLL8);
		row.appendChild(ceLL9);
		row.appendChild(ceLL10);
		ReAnalize_tbl_Rows(sec_indx - 2, tbl_id);
	}
}

function Down_tbl_row(element) {
	if (element.parentNode.parentNode.sectionRowIndex < (element.parentNode.parentNode.parentNode.rows.length - 2)) {
		var sec_indx = element.parentNode.parentNode.sectionRowIndex;
		var table = element.parentNode.parentNode.parentNode;
		var tbl_id = table.parentNode.id;

		var cell1 = document.createElement("td");
		cell1.align = 'center';
		var row = table.insertRow(sec_indx + 2);
		row.appendChild(cell1);
		row.appendChild(element.parentNode.parentNode.cells[1]);
		row.appendChild(element.parentNode.parentNode.cells[1]);
		row.appendChild(element.parentNode.parentNode.cells[1]);
		row.appendChild(element.parentNode.parentNode.cells[1]);
		row.appendChild(element.parentNode.parentNode.cells[1]);
		row.appendChild(element.parentNode.parentNode.cells[1]);
		element.parentNode.parentNode.parentNode.deleteRow(element.parentNode.parentNode.sectionRowIndex);

		var ceLL8 = document.createElement("td");
		var ceLL9 = document.createElement("td");
		var ceLL10 = document.createElement("td");

		ceLL8.innerHTML = '<a class="jlms_img_link" href="javascript: void(0);" onclick="javascript:Delete_tbl_row(this); return false;" title="Delete"><img src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_cancel.png" width="16" height="16" border="0" alt="Delete" \/><\/a>';
		ceLL9.innerHTML = '<a class="jlms_img_link" href="javascript: void(0);" onclick="javascript:Up_tbl_row(this); return false;" title="<?php echo _JLMS_MOVEUP;?>"><img src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_uparrow.png" width="16" height="16" border="0" alt="<?php echo _JLMS_MOVEUP;?>" \/><\/a>';
		ceLL10.innerHTML = '&nbsp;'; 

		row.appendChild(ceLL8);
		row.appendChild(ceLL9);
		row.appendChild(ceLL10);
		ReAnalize_tbl_Rows(sec_indx, tbl_id);
	}
}

function analyze_change_check(e) {
	if (!e) { e = window.event;}
	var cat2=e.target?e.target:e.srcElement;
	analyze_change_check2(cat2);
	
}
function analyze_change_check2(check_element) {
	var td_element = check_element.parentNode;
	var is_check = check_element.checked;
	if (td_element.hasChildNodes()) {
		var children = td_element.childNodes;
		for (var i = 0; i < children.length; i++) {
			if (children[i].nodeName.toLowerCase() == 'input') {
				var inp_type = children[i].type;
				if (inp_type.toLowerCase() == 'hidden') {
					children[i].value = is_check ? '1' : '0'
				}
			}
		}
	}
}

/*function hasOptions(obj){if(obj!=null && obj.options!=null){return true;}return false;} */
function copyOptions(from,to){
	var options = new Object();
	//if(hasOptions(to)){for(var i=0;i<to.options.length;i++){options[to.options[i].value] = to.options[i].text;}}
	//if(!hasOptions(from)){return;}
	for(var i=0;i<from.options.length;i++){
		var o = from.options[i];
	/*	if(options[o.value] == null || options[o.value] == "undefined" || options[o.value]!=o.text){
			if(!hasOptions(to)){
				var index = 0;
			}else{
				var index=to.options.length;
			}
			to.options[index] = new Option( o.text, o.value, false, false);
		}*/
		var index=to.options.length;
		to.options[index] = new Option( o.text, o.value, false, false);
	}
	to.selectedIndex = from.selectedIndex;
}

function Add_new_tbl_field(button_element, pref) {

	var a_tbl_ctxt_name = 'certificate_custom_msgs'+pref;
	var a_fld_txt_name = 'new_txt_message'+pref;
	var a_fld_txt_mes_select = 'new_txt_mes_font'+pref;
	var a_fld_txt_mes_shadow = 'new_txt_mes_shadow'+pref;
	var a_fld_txt_x = 'new_txt_message_X'+pref;
	var a_fld_txt_y = 'new_txt_message_Y'+pref;
	var a_fld_txt_h = 'new_txt_message_H'+pref;


	var f_hidden_id_name = 'ctxt_mes_id'+pref+'[]';
	var f_txt_mes_name = 'ctxt_mes_text'+pref+'[]';
	var f_font_select_name = 'ctxt_mes_font'+pref+'[]';
	var f_shadow_check_name = 'ctxt_mes_shadow'+pref+'[]';
	var f_shadow_hid_name = 'ctxt_mes_shadow_hid'+pref+'[]';
	var f_txt_mes_x = 'ctxt_mes_x'+pref+'[]';
	var f_txt_mes_y = 'ctxt_mes_y'+pref+'[]';
	var f_txt_mes_h = 'ctxt_mes_h'+pref+'[]';


	var form = button_element.form;


	if (trim(getObj(a_fld_txt_name).value) == '') {
		alert("Please enter text to the field.");return;
	}



	var is_check = getObj(a_fld_txt_mes_shadow).checked;

	var tbl_elem = getObj(a_tbl_ctxt_name);
	var row = tbl_elem.insertRow(tbl_elem.rows.length - 1);
	var ceLL1 = document.createElement("td");
	var ceLL2 = document.createElement("td");
	var ceLL3 = document.createElement("td");
	var ceLL4 = document.createElement("td");
	var ceLL5 = document.createElement("td");
	var ceLL6 = document.createElement("td");
	var ceLL7 = document.createElement("td");
	var ceLL8 = document.createElement("td");
	var ceLL9 = document.createElement("td");
	var ceLL10 = document.createElement("td");


	ceLL1.innerHTML = 0;
	ceLL1.align = 'center';


	var input_hidden = document.createElement("input");
	input_hidden.type = "hidden";
	input_hidden.name = f_hidden_id_name;
	input_hidden.value = '0';
	var input_txt_mes = document.createElement("input");
	input_txt_mes.type = "text";
	input_txt_mes.className = 'inputbox';
	input_txt_mes.size = 24;
	input_txt_mes.name = f_txt_mes_name;
	input_txt_mes.value = getObj(a_fld_txt_name).value;					getObj(a_fld_txt_name).value = '';
	ceLL2.appendChild(input_hidden);
	ceLL2.appendChild(input_txt_mes);


	var input_hidden_s = document.createElement("input");
	input_hidden_s.type = "hidden";
	input_hidden_s.name = f_shadow_hid_name;
	input_hidden_s.value = is_check ? '1' : '0';
	var input_check = document.createElement("input");
	input_check.type = "checkbox";
	input_check.name = f_shadow_check_name;
	input_check.value = '1';
	input_check.checked = is_check;
	input_check.onchange=input_check.onclick = new Function('analyze_change_check2(this)');
	ceLL3.appendChild(input_check);
	ceLL3.appendChild(input_hidden_s);
	ceLL3.align = 'center';


	var input_txt_x = document.createElement("input");
	input_txt_x.type = "text";
	input_txt_x.className = 'inputbox';
	input_txt_x.size = 3;
	input_txt_x.name = f_txt_mes_x;
	input_txt_x.value = getObj(a_fld_txt_x).value;
	ceLL4.appendChild(input_txt_x);


	var input_txt_y = document.createElement("input");
	input_txt_y.type = "text";
	input_txt_y.className = 'inputbox';
	input_txt_y.size = 3;
	input_txt_y.name = f_txt_mes_y;
	input_txt_y.value = getObj(a_fld_txt_y).value;
	ceLL5.appendChild(input_txt_y);


	var input_txt_h = document.createElement("input");
	input_txt_h.type = "text";
	input_txt_h.className = 'inputbox';
	input_txt_h.size = 3;
	input_txt_h.name = f_txt_mes_h;
	input_txt_h.value = getObj(a_fld_txt_h).value;
	ceLL6.appendChild(input_txt_h);


	var input_select = document.createElement("select");
	input_select.name = f_font_select_name;
	input_select.className = 'inputbox';
	copyOptions(form[a_fld_txt_mes_select],input_select);
	input_select.style.width = "180px";

	ceLL7.appendChild(input_select);


	
	

	ceLL8.innerHTML = '<a class="jlms_img_link" href="javascript: void(0);" onclick="javascript:Delete_tbl_row(this); return false;" title="Delete"><img src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_cancel.png" width="16" height="16" border="0" alt="Delete" \/><\/a>';
	ceLL9.innerHTML = '<a class="jlms_img_link" href="javascript: void(0);" onclick="javascript:Up_tbl_row(this); return false;" title="<?php echo _JLMS_MOVEUP;?>"><img src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_uparrow.png" width="16" height="16" border="0" alt="<?php echo _JLMS_MOVEUP;?>" \/><\/a>';
	ceLL10.innerHTML = '';
	row.appendChild(ceLL1);
	row.appendChild(ceLL2);
	row.appendChild(ceLL3);
	row.appendChild(ceLL4);
	row.appendChild(ceLL5);
	row.appendChild(ceLL6);
	row.appendChild(ceLL7);
	row.appendChild(ceLL8);
	row.appendChild(ceLL9);
	row.appendChild(ceLL10);
	ReAnalize_tbl_Rows(tbl_elem.rows.length - 2, a_tbl_ctxt_name);
}

//--><!]]>
</script>
<?php } ?>
	<table width="100%" cellpadding="0" cellspacing="0" border="0" class="jlms_table_no_borders">
		<tr>
			<td valign="middle" style="vertical-align:middle"><br /><?php echo _JLMS_GB_CRT_NAME;?></td>
			<td>
				<br /><input size="40" class="inputbox" type="text" name="crtf_name" value="<?php echo str_replace('"','&quot;',$row->crtf_name);?>" />
			</td>
		</tr>
<?php if ($from_gb) { ?>
		<tr>
			<td valign="middle" style="vertical-align:middle"><br /><?php echo _JLMS_GB_CRT_ENABLED;?></td>
			<td>
				<br /><?php echo mosHTML::yesnoRadioList('published', '', $row->published );?>
			</td>
		</tr>
<?php } ?>
<?php
/* search for font (ttf) files */
$fonts = array();
$path = $JLMS_CONFIG->getCfg('absolute_path') . '/media';
$filter = '.ttf';
$handle = opendir( $path );
while ($file = readdir($handle)) {
	if (($file != ".") && ($file != "..")) {
		if (preg_match( "/$filter/", $file )) {
			$fonts[] = trim($file);
		}
	}
}
$font_s = array();
foreach ($fonts as $font) {
	$rr = new stdClass();
	$rr->text = $font;
	$rr->value = $font;
	$font_s[] = $rr;
}

/* end of fonts search */
	//if (count($lroles) > 1) {
	//DEN: recently we have disabled multiple-roles certificates
	if (false) {
		$tabs = new JLMSTabs(0);
		echo '<tr><td colspan="2"><br />';
		echo $tabs->startPane("JLMS");
		echo $tabs->startTab('Default',"jlmsroletab_0");
		echo '<table width="100%" cellpadding="0" cellspacing="0" border="0" class="jlms_table_no_borders">';
		JLMS_Certificates::JLMS_editCertificate_OneRole($row, $font_s);
		echo '</table>';
		echo $tabs->endTab();
		foreach ($lroles as $lrole) {
			echo $tabs->startTab($lrole->lms_usertype,"jlmsroletab_".$lrole->id);
			echo '<table width="100%" cellpadding="0" cellspacing="0" border="0" class="jlms_table_no_borders">';
			$is_show = false;
			if (!empty($row->add_certificates)) {
				foreach ($row->add_certificates as $rac) {
					if ($rac->crtf_type == $lrole->id) {
						JLMS_Certificates::JLMS_editCertificate_OneRole($rac, $font_s, $lrole->id, false);
						$is_show = true;
						break;
					}
				}
			}
			if (!$is_show) {
				JLMS_Certificates::JLMS_editCertificate_OneRole($row, $font_s, $lrole->id, true);
			}
			echo '</table>';
			echo $tabs->endTab();
		}
		echo $tabs->endPane();
		echo '</td></tr>';
	} else {
		JLMS_Certificates::JLMS_editCertificate_OneRole($row, $font_s);
	}
	echo '	</table>';
}

function JLMS_editCertificate_OneRole( &$row, &$fonts, $pref = '', $default = false ) {
	global $JLMS_DB, $JLMS_CONFIG;
	$parent_id = isset($row->id) ? intval($row->id) : 0;
	$parent_course = isset($row->course_id) ? intval($row->course_id) : 0;
	$query = "SELECT * FROM #__lms_certificates WHERE parent_id = $parent_id AND course_id = $parent_course AND crtf_type = '-2' ORDER BY crtf_align";
	$JLMS_DB->SetQuery( $query );
	$cmsgs_saved = $JLMS_DB->LoadObjectList();
	$f = mosHTML::selectList( $fonts, 'crtf_font'.($pref?'_'.$pref:''), ($default ? ' disabled="disabled"':''), 'value', 'text', isset($row->crtf_font)?$row->crtf_font:'arial.ttf' );
	$f2 = mosHTML::selectList( $fonts, 'new_txt_mes_font'.($pref?'_'.$pref:''), ' style="width:180px"'.($default ? ' disabled="disabled"':''), 'value', 'text' );
	if ($pref) { ?>
		<tr>
			<td valign="middle" style="vertical-align:middle" width="25%"><br /><?php echo _JLMS_CRTF_USE_DEFAULT;?></td>
			<td>
				<br /><input size="40" onchange="jlms_changeCrtfDefaultValue(this, '<?php echo $pref;?>')" class="inputbox" type="checkbox"<?php echo $default ? ' checked="checked"' : '';?> name="certificate_default<?php echo $pref?'_'.$pref:'';?>" value="1" />
			</td>
		</tr>
	<?php } ?>
	<tr>
		<td valign="middle" style="vertical-align:middle" width="25%"><br /><?php echo _JLMS_CHOOSE_FILE;?></td>
		<td>
			<br /><input size="40" class="inputbox" type="file"<?php echo $default ? ' disabled="disabled"' : '';?> name="userfile<?php echo $pref?'_'.$pref:'';?>" />
			<?php echo ($row->file_id && !$default) ? ('<br />'._JLMS_FILE_ATTACHED) : ('<br />'._JLMS_FILE_NOT_ATTACHED);?>
		</td>
	</tr>
	<tr>
		<td><br /><?php echo _JLMS_GB_CRT_TEXT;?></td>
		<td><br />
			<textarea class="inputbox" name="crtf_text<?php echo $pref?'_'.$pref:'';?>"<?php echo $default ? ' disabled="disabled"' : '';?> rows="6" cols="40"><?php echo $row->crtf_text;?></textarea>
		</td>
	</tr>
	<tr><td colspan="2"><div class="joomlalms_info_legend"><?php echo _JLMS_GB_CRTF_TEXT_NOTE;?></div></td></tr>
	<tr>
		<td valign="middle" style="vertical-align:middle"><br /><?php echo _JLMS_GB_CRT_TEXT_ALIGN;?></td>
		<td>
			<?php $list_a = array();
				$list_a[] = mosHTML::makeOption( '0', _JLMS_LEFT);
				$list_a[] = mosHTML::makeOption( '1', _JLMS_CENTER);
				$list_a[] = mosHTML::makeOption( '2', _JLMS_RIGHT);
				$rrr = mosHTML::radioList( $list_a, 'crtf_align'.($pref?'_'.$pref:''), ($default ? ' disabled="disabled"':''), $row->crtf_align );
				?>
			<br /><?php echo $rrr;?>
		</td>
	</tr>
	<tr>
		<td valign="middle" style="vertical-align:middle"><br /><?php echo _JLMS_GB_CRT_TEXT_SHADOW;?></td>
		<td><br />
			<?php echo mosHTML::yesnoRadioList('crtf_shadow'.($pref?'_'.$pref:''), ($default ? ' disabled="disabled"':''), $row->crtf_shadow );?>
		</td>
	</tr>
	<tr>
		<td valign="middle" style="vertical-align:middle"><br /><?php echo _JLMS_GB_CRT_TEXT_X;?></td>
		<td>
			<br /><input size="40" class="inputbox" type="text"<?php echo $default ? ' disabled="disabled"' : '';?> name="text_x<?php echo $pref?'_'.$pref:'';?>" value="<?php echo $row->text_x;?>" />
		</td>
	</tr>
	<tr>
		<td valign="middle" style="vertical-align:middle"><br /><?php echo _JLMS_GB_CRT_TEXT_Y;?></td>
		<td>
			<br /><input size="40" class="inputbox" type="text"<?php echo $default ? ' disabled="disabled"' : '';?> name="text_y<?php echo $pref?'_'.$pref:'';?>" value="<?php echo $row->text_y;?>" />
		</td>
	</tr>
	<tr>
		<td valign="middle" style="vertical-align:middle"><br /><?php echo _JLMS_GB_CRT_TEXT_SIZE;?></td>
		<td>
			<br /><input size="40" class="inputbox" type="text"<?php echo $default ? ' disabled="disabled"' : '';?> name="text_size<?php echo $pref?'_'.$pref:'';?>" value="<?php echo $row->text_size;?>" />
			<input type="hidden" name="certificate_types[]" value="<?php echo $pref;?>" />
		</td>
	</tr>
	<tr>
		<td valign="middle" style="vertical-align:middle"><br /><?php echo _JLMS_CRTF_FONT.':';?></td>
		<td>
			<br /><?php echo $f;?>
		</td>
	</tr>
	<tr>
		<td colspan="2"><?php echo JLMSCSS::h2(_JLMS_CRTF_TEXT_FIELDS);?></td>
	</tr>
	<tr>
		<td colspan="2">
<?php
		echo '<table width="100%" cellpadding="0" cellspacing="0" id="certificate_custom_msgs'.($pref?'_'.$pref:'').'" class="'.JLMSCSS::_('jlmslist').'">';
		echo '<tr>';
		echo '	<'.JLMSCSS::tableheadertag().' width="20" class="'.JLMSCSS::_('sectiontableheader').'" align="center">#</'.JLMSCSS::tableheadertag().'>';
		echo '	<'.JLMSCSS::tableheadertag().' class="'.JLMSCSS::_('sectiontableheader').'" width="200">'._JLMS_CRTF_SHORT_TEXT_FIELD.'</'.JLMSCSS::tableheadertag().'>';
		echo '	<'.JLMSCSS::tableheadertag().' class="'.JLMSCSS::_('sectiontableheader').'" width="30" align="center">'._JLMS_CRTF_SHORT_SHADOW.'</'.JLMSCSS::tableheadertag().'>';
		echo '	<'.JLMSCSS::tableheadertag().' class="'.JLMSCSS::_('sectiontableheader').'" width="40" align="center">'._JLMS_CRTF_SHORT_X.'</'.JLMSCSS::tableheadertag().'>';
		echo '	<'.JLMSCSS::tableheadertag().' class="'.JLMSCSS::_('sectiontableheader').'" width="40" align="center">'._JLMS_CRTF_SHORT_Y.'</'.JLMSCSS::tableheadertag().'>';
		echo '	<'.JLMSCSS::tableheadertag().' class="'.JLMSCSS::_('sectiontableheader').'" width="40" align="center">'._JLMS_CRTF_SHORT_HEIGHT.'</'.JLMSCSS::tableheadertag().'>';
		echo '	<'.JLMSCSS::tableheadertag().' class="'.JLMSCSS::_('sectiontableheader').'">'._JLMS_CRTF_SHORT_FONT.'</'.JLMSCSS::tableheadertag().'>';
		echo '<'.JLMSCSS::tableheadertag().' class="'.JLMSCSS::_('sectiontableheader').'" width="20">&nbsp;</'.JLMSCSS::tableheadertag().'>';
		echo '<'.JLMSCSS::tableheadertag().' class="'.JLMSCSS::_('sectiontableheader').'" width="20">&nbsp;</'.JLMSCSS::tableheadertag().'>';
		echo '<'.JLMSCSS::tableheadertag().' class="'.JLMSCSS::_('sectiontableheader').'" width="20">&nbsp;</'.JLMSCSS::tableheadertag().'>';
		echo '</tr>';
		$k = 1;$i = 1;
		foreach ($cmsgs_saved as $cmsg_s) {
			echo '<tr class="'.JLMSCSS::_('sectiontableentry'.$k).'">';
			echo '<td align="center">'.$i.'</td>';
			echo '<td><input type="hidden" name="ctxt_mes_id'.($pref?'_'.$pref:'').'[]" value="'.$cmsg_s->id.'" /><input type="text" size="24" name="ctxt_mes_text'.($pref?'_'.$pref:'').'[]"'.($default ? ' disabled="disabled"' : '').' value="'.$cmsg_s->crtf_text.'" /></td>';

			echo '<td align="center">';
				echo '<input type="hidden"'.($default ? ' disabled="disabled"' : '').' name="ctxt_mes_shadow_hid'.($pref?'_'.$pref:'').'[]" value="'.($cmsg_s->crtf_shadow?'1':'0').'" />';
				echo '<input type="checkbox"'.($default ? ' disabled="disabled"' : '').' onchange="analyze_change_check2(this)" name="ctxt_mes_shadow'.($pref?'_'.$pref:'').'[]"'.($cmsg_s->crtf_shadow?' checked="checked"':'').' value="1" />';
			echo '</td>';

			echo '<td><input type="text" size="3" name="ctxt_mes_x'.($pref?'_'.$pref:'').'[]"'.($default ? ' disabled="disabled"' : '').' value="'.$cmsg_s->text_x.'" /></td>';

			echo '<td><input type="text" size="3" name="ctxt_mes_y'.($pref?'_'.$pref:'').'[]"'.($default ? ' disabled="disabled"' : '').' value="'.$cmsg_s->text_y.'" /></td>';

			echo '<td><input type="text" size="3" name="ctxt_mes_h'.($pref?'_'.$pref:'').'[]"'.($default ? ' disabled="disabled"' : '').' value="'.$cmsg_s->text_size.'" /></td>';

			$f3 = mosHTML::selectList( $fonts, 'ctxt_mes_font'.($pref?'_'.$pref:'').'[]', ' style="width:180px"'.($default ? ' disabled="disabled"':''), 'value', 'text', isset($cmsg_s->crtf_font)?$cmsg_s->crtf_font:'arial.ttf' );
			echo '<td>'.$f3.'</td>';

			echo '<td><a class="jlms_img_link" href="javascript:void(0);" onclick="Delete_tbl_row(this); return false;" title="Delete"><img src="'.$JLMS_CONFIG->get('live_site').'/components/com_joomla_lms/lms_images/toolbar/btn_cancel.png" width="16" height="16" border="0" alt="Delete" /></a></td>';
			echo '<td>';
			if ($i > 1) {
				echo '<a class="jlms_img_link" href="javascript:void(0);" onclick="Up_tbl_row(this); return false;" title="'._JLMS_MOVEUP.'"><img src="'.$JLMS_CONFIG->get('live_site').'/components/com_joomla_lms/lms_images/toolbar/btn_uparrow.png" width="16" height="16" border="0" alt="'._JLMS_MOVEUP.'" /></a>';
			} else {
				echo '&nbsp;';
			}
			echo '</td>';
			echo '<td>';
			if ($i < count($cmsgs_saved)) {
				echo '<a class="jlms_img_link" href="javascript:void(0);" onclick="Down_tbl_row(this); return false;" title="'._JLMS_MOVEDOWN.'"><img src="'.$JLMS_CONFIG->get('live_site').'/components/com_joomla_lms/lms_images/toolbar/btn_downarrow.png" width="16" height="16" border="0" alt="'._JLMS_MOVEDOWN.'" /></a>';
			} else {
				echo '&nbsp;';
			}

			echo '</tr>';
			$k = 3 - $k;
			$i ++;
		}
		/*echo '</table>';
		echo '<br />';
		echo '<table width="100%" cellpadding="0" cellspacing="0">';*/
		echo '<tr class="'.JLMSCSS::_('sectiontableentry'.$k).'">';
		echo '<td>&nbsp;</td>';
		echo '	<td width="200"><br />';
		echo '	<input id="new_txt_message'.($pref?'_'.$pref:'').'" class="inputbox"'.($default ? ' disabled="disabled"' : '').' size="24" type="text" name="new_txt_message'.($pref?'_'.$pref:'').'" />';
		echo '</td><td width="20" align="center"><br />';
		echo '	<input id="new_txt_mes_shadow'.($pref?'_'.$pref:'').'"'.($default ? ' disabled="disabled"' : '').' type="checkbox" name="new_txt_mes_shadow'.($pref?'_'.$pref:'').'" />';
		echo '</td><td width="40"><br />';
		echo '	<input id="new_txt_message_X'.($pref?'_'.$pref:'').'" class="inputbox"'.($default ? ' disabled="disabled"' : '').' size="3" type="text" name="new_txt_message_X'.($pref?'_'.$pref:'').'" />';
		echo '</td><td width="40"><br />';
		echo '	<input id="new_txt_message_Y'.($pref?'_'.$pref:'').'" class="inputbox"'.($default ? ' disabled="disabled"' : '').' size="3" type="text" name="new_txt_message_Y'.($pref?'_'.$pref:'').'" />';
		echo '</td><td width="40"><br />';
		echo '	<input id="new_txt_message_H'.($pref?'_'.$pref:'').'" class="inputbox"'.($default ? ' disabled="disabled"' : '').' size="3" type="text" name="new_txt_message_H'.($pref?'_'.$pref:'').'" />';
		echo '</td>';
		echo '<td width="100"><br />';
		echo $f2;
		echo '</td><td align="left" colspan="3"><br />';
		echo '	<input class="inputbox" type="button"'.($default ? ' disabled="disabled"' : '').' name="add_new_field'.($pref?'_'.$pref:'').'" style="width:70px " value="'._JLMS_GB_ADD_NEW_FIELD.'" onclick="javascript:Add_new_tbl_field(this,\''.($pref?'_'.$pref:''),'\');" />';
		echo '</td>';
		echo '</tr>';
		echo '</table>';
		?>
		
		
		</td>
	</tr>
<?php
}
function JLMS_saveCertificate( $course_id, $option, $crtf_type, $redirect_url = '' ) {
	global $my, $JLMS_DB, $Itemid;
	$JLMS_ACL = & JLMSFactory::getACL();
	$crtf_id = 0;
	/*echo '<pre>';
	print_r($_REQUEST);*/
	if ($course_id && $JLMS_ACL->CheckPermissions('docs', 'view')) {
		$crtf_name = isset($_REQUEST['crtf_name'])?$_REQUEST['crtf_name']:'';
		$crtf_name = (get_magic_quotes_gpc()) ? stripslashes( $crtf_name ) : $crtf_name;
		$crtf_name = ampReplace(strip_tags($crtf_name));
		$crtf_name = $JLMS_DB->GetEscaped( $crtf_name );
		$crtf_text = isset($_REQUEST['crtf_text'])?$_REQUEST['crtf_text']:'';
		$crtf_text = (get_magic_quotes_gpc()) ? stripslashes( $crtf_text ) : $crtf_text;
		$crtf_text = ampReplace(strip_tags($crtf_text));
		$crtf_text = $JLMS_DB->GetEscaped( $crtf_text );
		$crtf_align = intval(mosGetParam($_REQUEST, 'crtf_align', 0));
		$published = intval(mosGetParam($_REQUEST, 'published', 1));
		$crtf_shadow = intval(mosGetParam($_REQUEST, 'crtf_shadow', 0));
		$crtf_font = strval(mosGetParam($_REQUEST, 'crtf_font', 0));
		if (!preg_match("/^[a-zA-Z0-9\-\_\s]+\.ttf$/",$crtf_font)) {
			$crtf_font = 'arial.ttf';
		}
		if ($crtf_shadow) { $crtf_shadow = 1; }
		if ( !in_array($crtf_align,array(0,1,2)) )$crtf_align = 0;
		$text_x = intval(mosGetParam($_REQUEST, 'text_x', 0));
		$text_y = intval(mosGetParam($_REQUEST, 'text_y', 0));
		$text_size = intval(mosGetParam($_REQUEST, 'text_size', 0));
		$new_file = false;
		$file_id = 0;
		if (isset($_FILES['userfile']) && !empty($_FILES['userfile']['name'])) {
			$file_id = JLMS_uploadFile( $course_id );
			$new_file = true;
		}
		$add_query = '';
		$crtf_id = 0;
		if ($crtf_type == 2) {
			$crtf_id = intval(mosGetParam($_REQUEST, 'crtf_id', 0));
			$add_query = " AND id = '".$crtf_id."'";
		}
		$query = "SELECT * FROM #__lms_certificates WHERE course_id = '".$course_id."' AND crtf_type = '".$crtf_type."' AND parent_id = 0".$add_query;
		$JLMS_DB->SetQuery( $query );
		$old_crt = $JLMS_DB->LoadObjectList();
		if (count($old_crt)) {
			$old_file = $old_crt[0]->file_id;
			if ($old_file && $new_file) {
				$files = array();
				$files[] = $old_file;
				JLMS_deleteFiles($files);
			}
			$crtf_id = $old_crt[0]->id;
			$query = "UPDATE #__lms_certificates SET published = $published, crtf_name = '".$crtf_name."', crtf_text = '".$crtf_text."', crtf_align = $crtf_align, crtf_shadow = $crtf_shadow, text_x = '".$text_x."', text_y = '".$text_y."', text_size = '".$text_size."', crtf_font = ".$JLMS_DB->quote($crtf_font)."".($new_file?(", file_id = '".$file_id."'"):'')." WHERE course_id = '".$course_id."' AND crtf_type = '".$crtf_type."' AND parent_id = 0".$add_query;
			$JLMS_DB->SetQuery( $query );
			$JLMS_DB->query();
		} else {
			$query = "INSERT INTO #__lms_certificates (parent_id, course_id, published, crtf_name, crtf_text, crtf_align, crtf_shadow, text_x, text_y, text_size".($new_file?", file_id":'').", crtf_type, crtf_font) VALUES ( 0, '".$course_id."', '".$published."', '".$crtf_name."', '".$crtf_text."', $crtf_align, $crtf_shadow, '".$text_x."', '".$text_y."', '".$text_size."'".($new_file?(",'".$file_id."'"):'').", '".$crtf_type."', ".$JLMS_DB->quote($crtf_font).")";
			$JLMS_DB->SetQuery( $query );
			$JLMS_DB->query();
			$crtf_id = $JLMS_DB->insertid();
		}
		$crtf_id = intval($crtf_id);


		/* 23 october 2007 - (DEN) */
		/* handle custom text fields */
		$ctxt_mes_ids = josGetArrayInts('ctxt_mes_id', $_REQUEST);
		$ctxt_mes_text = isset($_REQUEST['ctxt_mes_text']) ? $_REQUEST['ctxt_mes_text'] : array();
		$ctxt_mes_shadow_hid = mosGetParam($_REQUEST, 'ctxt_mes_shadow_hid', array());
		$ctxt_mes_x = mosGetParam($_REQUEST, 'ctxt_mes_x', array());
		$ctxt_mes_y = mosGetParam($_REQUEST, 'ctxt_mes_y', array());
		$ctxt_mes_h = mosGetParam($_REQUEST, 'ctxt_mes_h', array());
		$ctxt_mes_font = mosGetParam($_REQUEST, 'ctxt_mes_font', array());
		$p_ids = array();
		$i = 0;
		$add_cmes_ids = array();
		/*print_r($ctxt_mes_ids);*/
		foreach ($ctxt_mes_ids as $cmid) {
			if (isset($ctxt_mes_text[$i]) && isset($ctxt_mes_x[$i]) && isset($ctxt_mes_y[$i]) && isset($ctxt_mes_h[$i]) && isset($ctxt_mes_font[$i]) && isset($ctxt_mes_shadow_hid[$i]) && $ctxt_mes_text[$i]) {
				$crtf_shadow = $ctxt_mes_shadow_hid[$i] ? 1 : 0;
				$crtf_font = '';
				$text_x = intval($ctxt_mes_x[$i]); if ($text_x < 0) { $text_x = 0; }
				$text_y = intval($ctxt_mes_y[$i]); if ($text_y < 0) { $text_y = 0; }
				$text_size = intval($ctxt_mes_h[$i]); if ($text_size < 0) { $text_size = 0; }
				$crtf_text = $ctxt_mes_text[$i];
				$crtf_text = (get_magic_quotes_gpc()) ? stripslashes( $crtf_text ) : $crtf_text;
				$crtf_text = ampReplace(strip_tags($crtf_text));
				$crtf_text = $JLMS_DB->GetEscaped( $crtf_text );
				$crtf_font = strval($ctxt_mes_font[$i]);
				if (!preg_match("/^[a-zA-Z0-9\-\_\s]+\.ttf$/",$crtf_font)) {
					$crtf_font = 'arial.ttf';
				}
				if (!$cmid) {
					$query = "INSERT INTO #__lms_certificates (parent_id, course_id, crtf_name, crtf_text, crtf_align, crtf_shadow, text_x, text_y, text_size, crtf_type, crtf_font) VALUES ( $crtf_id, '".$course_id."', '', '".$crtf_text."', ".$i.", $crtf_shadow, '".$text_x."', '".$text_y."', '".$text_size."', '-2', ".$JLMS_DB->quote($crtf_font).")";
					$JLMS_DB->SetQuery( $query );
					$JLMS_DB->query();
					$crtf_cmes_id = $JLMS_DB->insertid();
					/*echo $JLMS_DB->geterrormsg();*/
					$add_cmes_ids[] = $crtf_cmes_id;
				} else {
					$query = "UPDATE #__lms_certificates SET crtf_text = '".$crtf_text."', crtf_align = $i, crtf_shadow = $crtf_shadow, text_x = '".$text_x."', text_y = '".$text_y."', text_size = '".$text_size."', crtf_font = ".$JLMS_DB->quote($crtf_font)." WHERE course_id = '".$course_id."' AND crtf_type = '-2' AND parent_id = $crtf_id AND id = $cmid";
					$JLMS_DB->SetQuery( $query );
					$JLMS_DB->query();
					$add_cmes_ids[] = $cmid;
				}
			}
			$i ++;
		}
		if (empty($add_cmes_ids)) {
			$add_cmes_ids = array(0);
		}
		/*print_r($add_cmes_ids);*/
		$add_cmes_ids_t = implode(',',$add_cmes_ids);
		$query = "DELETE FROM #__lms_certificates WHERE course_id = $course_id AND parent_id = $crtf_id AND crtf_type = '-2' AND id NOT IN ($add_cmes_ids_t)";
		$JLMS_DB->SetQuery( $query );
		$JLMS_DB->query();
		/*die;*/
		/* end of 'custom text fields' mod */


		if ($crtf_id) {
			$JLMS_ACL = & JLMSFactory::getACL();
			$lroles = $JLMS_ACL->GetSystemRolesIds(1);
			$add_certificates = josGetArrayInts('certificate_types', $_REQUEST);
			$types = array();
			if (!empty($add_certificates)) {
				foreach($add_certificates as $add_cert) {
					if ($add_cert && in_array($add_cert, $lroles)) {
						$certificate_default = intval(mosGetParam($_REQUEST, 'certificate_default_'.$add_cert, 0));
						if (!$certificate_default) {
							$crtf_text = isset($_REQUEST['crtf_text_'.$add_cert])?$_REQUEST['crtf_text_'.$add_cert]:'';
							$crtf_text = (get_magic_quotes_gpc()) ? stripslashes( $crtf_text ) : $crtf_text;
							$crtf_text = ampReplace(strip_tags($crtf_text));
							$crtf_text = $JLMS_DB->GetEscaped( $crtf_text );
							$crtf_align = intval(mosGetParam($_REQUEST, 'crtf_align_'.$add_cert, 0));
							$crtf_shadow = intval(mosGetParam($_REQUEST, 'crtf_shadow_'.$add_cert, 0));
							$crtf_font = strval(mosGetParam($_REQUEST, 'crtf_font_'.$add_cert, 0));
							if (!preg_match("/^[a-zA-Z0-9\-\_\s]+\.ttf$/",$crtf_font)) {
								$crtf_font = 'arial.ttf';
							}
							if ($crtf_shadow) { $crtf_shadow = 1; }
							if ( !in_array($crtf_align,array(0,1,2)) )$crtf_align = 0;
							$text_x = intval(mosGetParam($_REQUEST, 'text_x_'.$add_cert, 0));
							$text_y = intval(mosGetParam($_REQUEST, 'text_y_'.$add_cert, 0));
							$text_size = intval(mosGetParam($_REQUEST, 'text_size_'.$add_cert, 0));
							$new_file = false;
							$file_id = 0;
							if (isset($_FILES['userfile_'.$add_cert]) && !empty($_FILES['userfile_'.$add_cert]['name'])) {
								$file_id = JLMS_uploadFile( $course_id, 'userfile_'.$add_cert );
								$new_file = true;
							}
							$query = "SELECT * FROM #__lms_certificates WHERE course_id = '".$course_id."' AND crtf_type = '".$add_cert."' AND parent_id = $crtf_id";
							$JLMS_DB->SetQuery( $query );
							$old_crt = $JLMS_DB->LoadObjectList();
							if (count($old_crt)) {
								$old_file = $old_crt[0]->file_id;
								if ($old_file && $new_file) {
									$files = array();
									$files[] = $old_file;
									JLMS_deleteFiles($files);
								}
								$crtf_id_c = $old_crt[0]->id;
								$query = "UPDATE #__lms_certificates SET crtf_name = '".$crtf_name."', crtf_text = '".$crtf_text."', crtf_align = $crtf_align, crtf_shadow = $crtf_shadow, text_x = '".$text_x."', text_y = '".$text_y."', text_size = '".$text_size."', crtf_font = ".$JLMS_DB->quote($crtf_font)."".($new_file?(", file_id = '".$file_id."'"):'')." WHERE course_id = '".$course_id."' AND crtf_type = '".$add_cert."' AND parent_id = $crtf_id";
								$JLMS_DB->SetQuery( $query );
								$JLMS_DB->query();
							} else {
								$query = "INSERT INTO #__lms_certificates (parent_id, course_id, crtf_name, crtf_text, crtf_align, crtf_shadow, text_x, text_y, text_size".($new_file?", file_id":'').", crtf_type, crtf_font) VALUES ( $crtf_id, '".$course_id."', '".$crtf_name."', '".$crtf_text."', $crtf_align, $crtf_shadow, '".$text_x."', '".$text_y."', '".$text_size."'".($new_file?(",'".$file_id."'"):'').", '".$add_cert."', ".$JLMS_DB->quote($crtf_font).")";
								$JLMS_DB->SetQuery( $query );
								$JLMS_DB->query();
								$crtf_id_c = $JLMS_DB->insertid();
							}
							
							
							
							/* 23 october 2007 - (DEN) */
							/* handle custom text fields */
							$ctxt_mes_ids = josGetArrayInts('ctxt_mes_id_'.$add_cert, $_REQUEST);
							$ctxt_mes_text = isset($_REQUEST['ctxt_mes_text_'.$add_cert]) ? $_REQUEST['ctxt_mes_text_'.$add_cert] : array();
							$ctxt_mes_shadow_hid = mosGetParam($_REQUEST, 'ctxt_mes_shadow_hid_'.$add_cert, array());
							$ctxt_mes_x = mosGetParam($_REQUEST, 'ctxt_mes_x_'.$add_cert, array());
							$ctxt_mes_y = mosGetParam($_REQUEST, 'ctxt_mes_y_'.$add_cert, array());
							$ctxt_mes_h = mosGetParam($_REQUEST, 'ctxt_mes_h_'.$add_cert, array());
							$ctxt_mes_font = mosGetParam($_REQUEST, 'ctxt_mes_font_'.$add_cert, array());
							$p_ids = array();
							$i = 0;
							$add_cmes_ids = array();
							foreach ($ctxt_mes_ids as $cmid) {
								if (isset($ctxt_mes_text[$i]) && isset($ctxt_mes_x[$i]) && isset($ctxt_mes_y[$i]) && isset($ctxt_mes_h[$i]) && isset($ctxt_mes_font[$i]) && isset($ctxt_mes_shadow_hid[$i]) && $ctxt_mes_text[$i]) {
									$crtf_shadow = $ctxt_mes_shadow_hid[$i] ? 1 : 0;
									$crtf_font = '';
									$text_x = intval($ctxt_mes_x[$i]); if ($text_x < 0) { $text_x = 0; }
									$text_y = intval($ctxt_mes_y[$i]); if ($text_y < 0) { $text_y = 0; }
									$text_size = intval($ctxt_mes_h[$i]); if ($text_size < 0) { $text_size = 0; }
									$crtf_text = $ctxt_mes_text[$i];
									$crtf_text = (get_magic_quotes_gpc()) ? stripslashes( $crtf_text ) : $crtf_text;
									$crtf_text = ampReplace(strip_tags($crtf_text));
									$crtf_text = $JLMS_DB->GetEscaped( $crtf_text );
									$crtf_font = strval($ctxt_mes_font[$i]);
									if (!preg_match("/^[a-zA-Z0-9\-\_\s]+\.ttf$/",$crtf_font)) {
										$crtf_font = 'arial.ttf';
									}
									if (!$cmid) {
										$query = "INSERT INTO #__lms_certificates (parent_id, course_id, crtf_name, crtf_text, crtf_align, crtf_shadow, text_x, text_y, text_size, crtf_type, crtf_font) VALUES ( $crtf_id_c, '".$course_id."', '', '".$crtf_text."', ".$i.", $crtf_shadow, '".$text_x."', '".$text_y."', '".$text_size."', '-2', ".$JLMS_DB->quote($crtf_font).")";
										$JLMS_DB->SetQuery( $query );
										$JLMS_DB->query();
										$crtf_cmes_id = $JLMS_DB->insertid();
										$add_cmes_ids[] = $crtf_cmes_id;
									} else {
										$query = "UPDATE #__lms_certificates SET crtf_text = '".$crtf_text."', crtf_align = $i, crtf_shadow = $crtf_shadow, text_x = '".$text_x."', text_y = '".$text_y."', text_size = '".$text_size."', crtf_font = ".$JLMS_DB->quote($crtf_font)." WHERE course_id = '".$course_id."' AND crtf_type = '-2' AND parent_id = $crtf_id_c AND id = $cmid";
										$JLMS_DB->SetQuery( $query );
										$JLMS_DB->query();
										$add_cmes_ids[] = $cmid;
									}
								}
								$i ++;
							}
							if (empty($add_cmes_ids)) {
								$add_cmes_ids = array(0);
							}
							$add_cmes_ids_t = implode(',',$add_cmes_ids);
							$query = "DELETE FROM #__lms_certificates WHERE course_id = $course_id AND parent_id = $crtf_id_c AND crtf_type = '-2' AND id NOT IN ($add_cmes_ids_t)";
							$JLMS_DB->SetQuery( $query );
							$JLMS_DB->query();
							/* end of 'custom text fields' mod */

							$types[] = $add_cert;
						}
					}
				}
			}
			if (empty($types)) {
				$types = array(-2);
			} else {
				$types[] = -2;
			}
			/*print_r($types);*/
			$types_str = implode(',',$types);
			$query = "SELECT id, file_id FROM #__lms_certificates WHERE course_id = '".$course_id."' AND crtf_type NOT IN ($types_str) AND parent_id = $crtf_id";
			$JLMS_DB->SetQuery( $query );
			$old_sec_certs = $JLMS_DB->LoadObjectList();
			$old_files = array();
			$old_sec_cert_ids = array();
			if (!empty($old_sec_certs)) {
				foreach ($old_sec_certs as $osc) {
					$old_files[] = $osc->file_id;
					$old_sec_cert_ids[] = $osc->id;
				}
				JLMS_deleteFiles($old_files);
			}
			$query = "DELETE FROM #__lms_certificates WHERE course_id = '".$course_id."' AND crtf_type NOT IN ($types_str) AND parent_id = $crtf_id";
			$JLMS_DB->SetQuery( $query );
			$JLMS_DB->query();
			if (!empty($old_sec_cert_ids)) {
				$osc_t = implode(',',$old_sec_cert_ids);
				$query = "DELETE FROM #__lms_certificates WHERE course_id = '".$course_id."' AND crtf_type = '-2' AND parent_id IN ($osc_t)";
				$JLMS_DB->SetQuery( $query );
				$JLMS_DB->query();
			}
		}
	}
	/*die;*/
	if (!$redirect_url) $redirect_url = "index.php?option=$option&Itemid=$Itemid&task=gb_certificates&id=$course_id";
	JLMSRedirect(sefRelToAbs(str_replace('{id}',$crtf_id,$redirect_url)));
}
function JLMS_outputCertificate( $id, $course_id, $txt_mes_obj = null, $user_obj = null ) {
	global $JLMS_DB, $JLMS_CONFIG, $my;
	if (is_null($user_obj)) {
		$user_obj = new stdClass();
		$user_obj->id = $my->id;
		$user_obj->username = $my->username;
		$user_obj->email = $my->email;
		$user_obj->name = $my->name;
	}

	$JLMS_ACL = & JLMSFactory::getACL();
	$is_preview = false;
	$is_exist = false;
	$quiz_id = 0;
	$quiz_name = '';
	$course_name = '';
	if (isset($txt_mes_obj->is_preview)) {
		$is_preview = $txt_mes_obj->is_preview;
	}
	if (isset($txt_mes_obj->quiz_id)) {
		$quiz_id = $txt_mes_obj->quiz_id;
	}
	if (isset($txt_mes_obj->quiz_name)) {
		$quiz_name = $txt_mes_obj->quiz_name;
	}
	if (isset($txt_mes_obj->course_name)) {
		$course_name = $txt_mes_obj->course_name;
	}
	$do_s = true;
	$crtf_role = 0;
	if ($is_preview) {
		$crtf_role = intval(mosGetParam($_REQUEST, 'crtf_role', 0));
	} else {
		$crtf_role = intval($JLMS_ACL->GetRole(1));
	}
	if ($crtf_role) {
		$query = "SELECT a.*, b.course_name FROM #__lms_certificates as a, #__lms_courses as b WHERE a.course_id = '".$course_id."' AND a.course_id = b.id AND a.parent_id = $id AND a.crtf_type = $crtf_role";
		$JLMS_DB->SetQuery( $query );
		$crts = $JLMS_DB->loadObjectList();
		if (count($crts) == 1) {
			if ($crts[0]->file_id) {
				$do_s = false;
			} else {
				$query = "SELECT file_id FROM #__lms_certificates as a WHERE a.id = '".$id."' AND a.course_id = '".$course_id."' AND a.parent_id = 0";
				$JLMS_DB->SetQuery( $query );
				$crts[0]->file_id = $JLMS_DB->LoadResult();
				if ($crts[0]->file_id) {
					$do_s = false;
				}
			}
		}
	}
	if ($do_s) {
		$query = "SELECT a.*, b.course_name FROM #__lms_certificates as a, #__lms_courses as b WHERE a.id = '".$id."' AND a.course_id = '".$course_id."' AND a.course_id = b.id AND a.parent_id = 0";
		$JLMS_DB->SetQuery( $query );
		$crts = $JLMS_DB->loadObjectList();
	}
	if (count($crts) == 1) {
		$is_duplicate = false;
		$print_duplicate_watermark = $JLMS_CONFIG->get('crtf_duplicate_wm', true);
		$crt = $crts[0];
		$JLMS_DB->SetQuery("SELECT file_srv_name FROM #__lms_files WHERE id = '".$crt->file_id."'");
		$cert_name = $JLMS_DB->LoadResult();
		if ($cert_name) {
			$ucode = md5(uniqid(rand(), true));
			$ex_crtf_id = 0;
			$is_saved_on_server = false;
			$ucode = substr($ucode,0,10);
			if (!$is_preview) {
				$query = "SELECT * FROM #__lms_certificate_prints WHERE user_id = $user_obj->id AND role_id = $crtf_role AND course_id = $course_id AND crtf_id = $id AND quiz_id = $quiz_id";
				$JLMS_DB->SetQuery( $query );
				$cr_pr = $JLMS_DB->LoadObject();
				if (is_object($cr_pr) && isset($cr_pr->id)) {
					$is_exist = $cr_pr->id;
					$ex_crtf_id = $cr_pr->id;
					$ucode = $cr_pr->uniq_id;
					$txt_mes_obj->name = $cr_pr->name;
					$txt_mes_obj->username = $cr_pr->username;
					//$txt_mes_obj->course_name = $cr_pr->course_name;
					if (isset($txt_mes_obj->force_update_print_date) && $txt_mes_obj->force_update_print_date && isset($txt_mes_obj->crtf_date) && $txt_mes_obj->crtf_date) {
						$query = "UPDATE #__lms_certificate_prints SET crtf_date = '".$txt_mes_obj->crtf_date."' WHERE id = ".$cr_pr->id." AND user_id = $user_obj->id AND role_id = $crtf_role AND course_id = $course_id AND crtf_id = $id AND quiz_id = $quiz_id";
						$JLMS_DB->SetQuery( $query );
						$JLMS_DB->query();
					} else {
						$txt_mes_obj->crtf_date = strtotime($cr_pr->crtf_date);
					}
					$is_duplicate = true;
					if ($JLMS_CONFIG->get('save_certificates', 1)) {
						$im_crtf_path = $JLMS_CONFIG->get('jlms_crtf_folder', '');
						$file_on_srv = $im_crtf_path . '/' . md5($ex_crtf_id . '_' . $ucode) . '.png';
						if (file_exists($file_on_srv)) {
							$is_saved_on_server = true;
						}
					}
				}
			}
			if ($is_saved_on_server) {
				$loadFile = $file_on_srv;
			} else {
				$loadFile = _JOOMLMS_DOC_FOLDER . $cert_name;
			}
			$im_fullsize = getimagesize($loadFile);
			if (isset($im_fullsize[2])) {
				if ($im_fullsize[2] == 1) {
					$im = imagecreatefromgif($loadFile);
				} elseif ($im_fullsize[2] == 2) {
					$im = imagecreatefromjpeg($loadFile);
				} elseif ($im_fullsize[2] == 3) {
					$im = imagecreatefrompng($loadFile);
					if (function_exists('imagesavealpha')) {
						imagesavealpha($im, true);
					}
				} else { die();}
			} else { die('Bad image format.'); }
			if (!$is_saved_on_server) {
				require_once(dirname(__FILE__) . '/libchart/barcode.php');
				$b_params = array();
				if ($JLMS_CONFIG->get('crtf_show_sn', 1)) {
					$b_params[] = 'text';
				}
				if ($JLMS_CONFIG->get('crtf_show_barcode', 1)) {
					$b_params[] = 'bar';
				}
			}

			$origWidth = $im_fullsize[0]; 
			$origHeight = $im_fullsize[1];
			if ($is_duplicate && $print_duplicate_watermark) {
				require_once(_JOOMLMS_FRONT_HOME . DS . "includes" . DS . "libchart" . DS . "libchart.php");
				JLMS_cleanLibChartCache();
				$watermark = _JOOMLMS_FRONT_HOME . DS . "lms_images" . DS . "duplicate.png";
				$wmTarget = $JLMS_CONFIG->getCfg('absolute_path') . "/".($JLMS_CONFIG->get('temp_folder', '') ? ($JLMS_CONFIG->get('temp_folder', '')."/") : '') . time() . '_' . md5(uniqid(rand(), true)) . ".png";
				$waterMarkInfo = getimagesize($watermark);
				$waterMarkWidth = $waterMarkInfo[0];
				$waterMarkHeight = $waterMarkInfo[1];


				$placementX=0;
				$placementY=0;
				$waterMarkDestWidth=$waterMarkWidth;
				$waterMarkDestHeight=$waterMarkHeight;
				$waterMarkDestWidth = round(($origWidth / $waterMarkDestWidth) * $waterMarkDestWidth);
				$waterMarkDestHeight = round(($origHeight / $waterMarkDestHeight) * $waterMarkDestHeight);
				// both of the watermark dimensions need to be 5% more than the original image...
				// adjust width first.
				#if($waterMarkWidth > $origWidth*0.95 && $waterMarkHeight > $origHeight*0.95) {
					// both are already larger than the original by at least 5%...
					// we need to make the watermark *smaller* for this one.
					/*
					// where is the largest difference?
					$wdiff=$waterMarkDestWidth - $origWidth;
					$hdiff=$waterMarkDestHeight - $origHeight;
					if($wdiff > $hdiff) {
						// the width has the largest difference - get percentage
						$sizer=($wdiff/$waterMarkDestWidth)+0.05;
					} else {
						$sizer=($hdiff/$waterMarkDestHeight)+0.05;
					}
					$waterMarkDestWidth-=$waterMarkDestWidth * $sizer;
					$waterMarkDestHeight-=$waterMarkDestHeight * $sizer;*/
					#$waterMarkDestWidth = round(($origWidth / $waterMarkDestWidth) * $waterMarkDestWidth);
					#$waterMarkDestHeight = round(($origHeight / $waterMarkDestHeight) * $waterMarkDestHeight);
				#} else {
					// the watermark will need to be enlarged for this one
					
					// where is the largest difference?
					/*$wdiff=$origWidth - $waterMarkDestWidth;
					$hdiff=$origHeight - $waterMarkDestHeight;
					if($wdiff > $hdiff) {
						// the width has the largest difference - get percentage
						$sizer=($wdiff/$waterMarkDestWidth)+0.05;
					} else {
						$sizer=($hdiff/$waterMarkDestHeight)+0.05;
					}
					$waterMarkDestWidth+=$waterMarkDestWidth * $sizer;
					$waterMarkDestHeight+=$waterMarkDestHeight * $sizer;*/
					#$waterMarkDestWidth = round(($origWidth / $waterMarkDestWidth) * $waterMarkDestWidth);
					#$waterMarkDestHeight = round(($origHeight / $waterMarkDestHeight) * $waterMarkDestHeight);
				#}
				JLMS_Certificates::resize_png_image($watermark,$waterMarkDestWidth,$waterMarkDestHeight,$wmTarget, false);

				// get the size info for this watermark.
				$wmInfo=getimagesize($wmTarget);
				$waterMarkDestWidth=$wmInfo[0];
				$waterMarkDestHeight=$wmInfo[1];

				$differenceX = $origWidth - $waterMarkDestWidth;
				$differenceY = $origHeight - $waterMarkDestHeight;
				$placementX =  round($differenceX / 2);
				$placementY =  round($differenceY / 2);
			}
			if (!$is_saved_on_server) {
				if (!empty($b_params)) {
					$barcode = new JLMS_barcode($ucode, $b_params);
					$barcode->generate($im, $origWidth, $origHeight);
				}
				$white = imagecolorallocate($im, 255, 255, 255);
				$grey = imagecolorallocate($im, 128, 128, 128);
				$black = imagecolorallocate($im, 0, 0, 0);

				$text_messages = array();
				$crtf_msg = new stdClass();
				$crtf_msg->text_size = $crt->text_size;
				$crtf_msg->text_x = $crt->text_x;
				$crtf_msg->text_y = $crt->text_y;
				$crtf_msg->crtf_font = (isset($crt->crtf_font) && $crt->crtf_font) ? $crt->crtf_font : 'arial.ttf';
				$crtf_msg->crtf_text = $crt->crtf_text;
				$crtf_msg->course_name = $crt->course_name;
				$crtf_msg->crtf_shadow = $crt->crtf_shadow;
				$crtf_msg->crtf_align = $crt->crtf_align;
				$text_messages[] = $crtf_msg;
				$query = "SELECT * FROM #__lms_certificates WHERE course_id = $course_id AND parent_id = $crt->id AND crtf_type = '-2' ORDER BY crtf_align";
				$JLMS_DB->SetQuery($query);
				$add_cert_msgs = $JLMS_DB->LoadObjectList();
				foreach ($add_cert_msgs as $acms) {
					$crtf_msg = new stdClass();
					$crtf_msg->text_size = $acms->text_size;
					$crtf_msg->text_x = $acms->text_x;
					$crtf_msg->text_y = $acms->text_y;
					$crtf_msg->crtf_font = (isset($acms->crtf_font) && $acms->crtf_font) ? $acms->crtf_font : 'arial.ttf';
					$crtf_msg->crtf_text = $acms->crtf_text;
					$crtf_msg->course_name = $crt->course_name;
					$crtf_msg->crtf_shadow = $acms->crtf_shadow;
					$crtf_msg->crtf_align = 0;
					$text_messages[] = $crtf_msg;
				}
				foreach ($text_messages as $crt7) {
					$font_size = $crt7->text_size;
					$font_x = $crt7->text_x;
					$font_y = $crt7->text_y;
					$font_filename = $crt7->crtf_font;
					$inform = array();
					$font_text = $crt7->crtf_text;
					$username = isset($txt_mes_obj->username)?$txt_mes_obj->username:'';
					$name = isset($txt_mes_obj->name)?$txt_mes_obj->name:'';
					$course_name = isset($txt_mes_obj->course_name)?$txt_mes_obj->course_name:($crt7->course_name);
					//$spec_answer = isset($txt_mes_obj->crtf_spec_answer)?$txt_mes_obj->crtf_spec_answer:'';
					$crtf_date = isset($txt_mes_obj->crtf_date)?$txt_mes_obj->crtf_date:time();
					$font_text = str_replace('#username#', $username, $font_text);
					$font_text = str_replace('#name#', $name, $font_text);
					$font_text = str_replace('#course#', $course_name, $font_text);
	
					$font_text = JLMS_Certificates::ReplaceCourseRegAnswers($font_text, $txt_mes_obj, $user_obj->id, $course_id);
					//$font_text = str_replace('#reg_answer#', $spec_answer, $font_text);
	
					$font_text = JLMS_Certificates::ReplaceQuizAnswers($font_text, $txt_mes_obj, $user_obj->id, $course_id);
	
					$font_text = JLMS_Certificates::ReplaceEventOptions($font_text, $txt_mes_obj, $user_obj->id, $course_id);
	
					$font_text = JLMS_Certificates::ReplaceCBProfileOptions($font_text, $txt_mes_obj, $user_obj->id, $course_id);
	
					//$font_text = JLMS_Certificates::ReplaceUPN($font_text, $txt_mes_obj, $user_obj->id, $course_id);
	
					// replace #date#
					$str_format = 'Y-m-d';
					$str_format_pre = '';
					$first_pos = strpos( $font_text,'#date');
					if ($first_pos !== false) {
						$first_str = substr($font_text, $first_pos+5, strlen($font_text) - $first_pos - 5);
						$sec_pos = strpos( $first_str,'#');
						$str_format = substr($first_str, 0, $sec_pos);
						$str_format_pre = $str_format;
						echo $str_format;
						if ($str_format) {
							if (substr($str_format,0,1) == '(') {
								$str_format = substr($str_format,1);
							}
							if (substr($str_format,-1) == ')') {
								$str_format = substr($str_format,0,-1);
							}
						}
						echo $str_format;
					}
					if (!$str_format) { $str_format = 'Y-m-d';}
					$font_text = str_replace('#date'.$str_format_pre.'#', date($str_format, $crtf_date), $font_text);
					// end of #date#
					$font = JPATH_SITE . "/media/arial.ttf";
					if (file_exists(JPATH_SITE . "/media/".$font_filename)) {
						$font = JPATH_SITE . "/media/".$font_filename;
					}
					$text_array = explode("\n",$font_text);
					#print_r($text_array);die;
					$count_lines = count($text_array);
					$text_lines_xlefts = array();
					$text_lines_xrights = array();
					$text_lines_heights = array();
					for ($i = 0; $i< $count_lines; $i++) {
						$font_box = imagettfbbox($font_size, 0, $font, $text_array[$i]);
						$text_lines_xlefts[$i] = $font_box[0];
						$text_lines_xrights[$i] = $font_box[2];
						$text_lines_heights[$i] = $font_box[1]-$font_box[7];
						if ($text_lines_heights[$i] < $font_size) { $text_lines_heights[$i] = $font_size; }
					}
					$min_x = 0;
					$max_x = 0;
					$max_w = 0;
					for ($i = 0; $i< $count_lines; $i++) {
						if ($min_x > $text_lines_xlefts[$i]) $min_x = $text_lines_xlefts[$i];
						if ($max_x < $text_lines_xrights[$i]) $max_x = $text_lines_xrights[$i];
						if ($max_w < ($text_lines_xrights[$i]-$text_lines_xlefts[$i])) $max_w = ($text_lines_xrights[$i] - $text_lines_xlefts[$i]);
					}
					#$crt->crtf_text
					#$alignment = 'left';
					$allow_shadow = ($crt7->crtf_shadow == 1);
					#$alignment = 'left';
					switch(intval($crt7->crtf_align)) {
						case 1:
							for ($i = 0; $i< $count_lines; $i++) {
								$cur_w = $text_lines_xrights[$i] - $text_lines_xlefts[$i];
								$ad = intval(($max_w - $cur_w)/2) - intval($max_w/2);
								if ($allow_shadow) imagettftext($im, $font_size, 0, $font_x + $ad+2, $font_y+2, $grey, $font, $text_array[$i]);
								imagettftext($im, $font_size, 0, $font_x + $ad, $font_y, $black, $font, $text_array[$i]);
								$font_y = $font_y + $text_lines_heights[$i] + 3;
							}
						break;
						case 2:
							for ($i = 0; $i< $count_lines; $i++) {
								$cur_w = $text_lines_xrights[$i] - $text_lines_xlefts[$i];
								$ad = intval($max_w - $cur_w) - intval($max_w);
								if ($allow_shadow) imagettftext($im, $font_size, 0, $font_x + $ad+2, $font_y+2, $grey, $font, $text_array[$i]);
								imagettftext($im, $font_size, 0, $font_x + $ad, $font_y, $black, $font, $text_array[$i]);
								$font_y = $font_y + $text_lines_heights[$i] + 3;
							}
						break;
						default:
							for ($i = 0; $i< $count_lines; $i++) {
								$cur_w = $text_lines_xrights[$i] - $text_lines_xlefts[$i];
								$ad = 0;//intval(($max_w - $cur_w)/2);
								if ($allow_shadow) imagettftext($im, $font_size, 0, $font_x + $ad+2, $font_y+2, $grey, $font, $text_array[$i]);
								imagettftext($im, $font_size, 0, $font_x + $ad, $font_y, $black, $font, $text_array[$i]);
								$font_y = $font_y + $text_lines_heights[$i] + 3;
							}
						break;
					}
				}

				#$font_box = imagettfbbox($font_size, 0, $font, $font_text);
				#imagettftext($im, $font_size, 0, $font_x, $font_y, $grey, $font, $font_text, 'R');
				#imagettftext($im, $font_size, 0, $font_x, $font_y, $black, $font, $font_text, 'R');
	
				#@ob_end_clean();
			}
			if (!$is_preview) {
				if (!$is_exist) {

					$query = "INSERT INTO #__lms_certificate_prints (uniq_id, user_id, role_id, crtf_date, crtf_id, crtf_text, last_printed, name, username, course_id, course_name, quiz_id, quiz_name ) VALUES"
					. "\n (".$JLMS_DB->Quote($ucode).", $user_obj->id, $crtf_role,".$JLMS_DB->Quote(date('Y-m-d H:i:s', $crtf_date)).", $id, ".$JLMS_DB->Quote($crt->crtf_text).","
					. "\n ".$JLMS_DB->Quote(date('Y-m-d H:i:s')).", ".$JLMS_DB->Quote($user_obj->name).", ".$JLMS_DB->Quote($user_obj->username).","
					. "\n $course_id, ".$JLMS_DB->Quote($course_name).", $quiz_id, ".$JLMS_DB->Quote($quiz_name).")";
					$JLMS_DB->SetQuery( $query );
					$JLMS_DB->query();
					$ex_crtf_id = $JLMS_DB->insertid();

				} else {

					$query = "UPDATE #__lms_certificate_prints SET last_printed = ".$JLMS_DB->Quote(date('Y-m-d H:i:s')).","
					. "\n crtf_text = ".$JLMS_DB->Quote($crt->crtf_text).", course_name = ".$JLMS_DB->Quote($course_name).","
					. "\n quiz_name = ".$JLMS_DB->Quote($quiz_name)
					. "\n WHERE id = $is_exist";
					$JLMS_DB->SetQuery( $query );
					$JLMS_DB->query();
					$ex_crtf_id = $is_exist;

				}
			}

			if (preg_match('/Opera(\/| )([0-9].[0-9]{1,2})/', $_SERVER['HTTP_USER_AGENT'])) {
				$UserBrowser = "Opera";
			}
			elseif (preg_match('/MSIE ([0-9].[0-9]{1,2})/', $_SERVER['HTTP_USER_AGENT'])) {
				$UserBrowser = "IE";
			} else {
				$UserBrowser = '';
			}
			$file_name = 'Certificate.png';
			header('Content-Type: image/png');
			header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
			if ($UserBrowser == 'IE') {
				header('Content-Disposition: inline; filename="' . $file_name . '";');
				header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
				header('Pragma: public');
			} else {
				header('Content-Disposition: inline; filename="' . $file_name . '";');
				header('Pragma: no-cache');
			}
			@ob_end_clean();
			if (!$is_saved_on_server && !$is_preview) {
				if ($JLMS_CONFIG->get('save_certificates', 1)) {
					$im_crtf_path = $JLMS_CONFIG->get('jlms_crtf_folder', '');
					if ($im_crtf_path && is_writable($im_crtf_path)) {
						imagepng($im, $im_crtf_path . '/' . md5($ex_crtf_id . '_' . $ucode) . '.png');
					}
				}
			}

			if ($is_duplicate && $print_duplicate_watermark) {
				imagealphablending($im, TRUE);

				$finalWaterMarkImage = imagecreatefrompng($wmTarget);
				$finalWaterMarkWidth = imagesx($finalWaterMarkImage);
				$finalWaterMarkHeight = imagesy($finalWaterMarkImage);

				imagecopy($im,
						$finalWaterMarkImage,
						$placementX,
						$placementY,
						0,
						0,
						$finalWaterMarkWidth,
						$finalWaterMarkHeight
				);

				imagealphablending($im,FALSE);
				imagesavealpha($im,TRUE);
			}
			imagepng($im);
			imagedestroy($im);
			exit;
		} elseif ($is_preview) {
			@ob_end_clean();
			echo 'Certificate preview is not available. Please make sure an image for the certificate is uploaded.';
			exit;
		}
	}
}

function ReplaceCourseRegAnswers($font_text, $txt_mes_obj, $user_id, $course_id) {
	//$font_text = str_replace('#reg_answer#', $spec_answer, $font_text);
	$pos = strpos($font_text, '#reg_answer#');
	if ($pos === false) {
		
	} else {
		global $JLMS_DB;
		$c = '';
		$JLMS_ACL = & JLMSFactory::getACL();
		global $JLMS_DB;
		$role = $JLMS_ACL->UserRole($JLMS_DB, $user_id, 1);
		$query = "SELECT id FROM #__lms_spec_reg_questions WHERE course_id = $course_id AND (role_id = $role OR role_id = 0)"
		. "\n ORDER BY role_id DESC, ordering LIMIT 0,1";
		$JLMS_DB->SetQuery($query);
		$qid = $JLMS_DB->LoadResult();
		if ($qid) {
			$query = "SELECT user_answer FROM #__lms_spec_reg_answers WHERE course_id = $course_id AND user_id = $user_id AND quest_id = $qid";
			$JLMS_DB->SetQuery($query);
			$c = $JLMS_DB->LoadResult();
		}
		$font_text = str_replace('#reg_answer#', $c, $font_text);
	}

	if (!function_exists('QuizCourseRegAnswerReplacer')) {
		function QuizCourseRegAnswerReplacer( &$matches ) {
			$result = preg_match('/\d+/',$matches[1],$found);
			$id = @$found[0];
			$id = intval($id);
	
			$ret_str = "";
	
			if ($id > 0) {
				global $course_ans_replacer_user_id, $course_ans_replacer_course_id, $JLMS_DB;
				$JLMS_ACL = & JLMSFactory::getACL();
				$role = $JLMS_ACL->UserRole($JLMS_DB, $course_ans_replacer_user_id, 1);
				$query = "SELECT count(*) FROM #__lms_spec_reg_questions WHERE course_id = $course_ans_replacer_course_id AND role_id = $role";
				$JLMS_DB->SetQuery($query);
				$is_role_q = $JLMS_DB->LoadResult();
				$query = "SELECT id FROM #__lms_spec_reg_questions WHERE course_id = $course_ans_replacer_course_id AND ".($is_role_q ? "role_id = $role" : "(role_id = $role OR role_id = 0)")." AND id = $id"
				. "\n ORDER BY role_id DESC LIMIT 0,1";
				$JLMS_DB->SetQuery($query);
				$qid = $JLMS_DB->LoadResult();
				if ($qid) {
					$query = "SELECT user_answer FROM #__lms_spec_reg_answers WHERE course_id = $course_ans_replacer_course_id AND user_id = $course_ans_replacer_user_id AND quest_id = $qid";
					$JLMS_DB->SetQuery($query);
					$ret_str = $JLMS_DB->LoadResult();
				}
			}
			return $ret_str;
		}
	}

	$GLOBALS['course_ans_replacer_user_id'] = $user_id;
	$GLOBALS['course_ans_replacer_course_id'] = $course_id;
	$regex = "#\#reg_answer_(.*?)\##s";
	$new_text = preg_replace_callback( $regex, 'QuizCourseRegAnswerReplacer', $font_text );
	return $new_text;
}

function ReplaceEventOptions($font_text, $txt_mes_obj, $user_id, $course_id) {

	if (!function_exists('EventOptionsReplacer')) {
		function EventOptionsReplacer( &$matches ) {
			$result = preg_match('/\w+/',$matches[1],$found);
			$event_option = @$found[0];
	
			$ret_str = "";
	
			if ($event_option) {
				global $event_options_replacer_user_id, $event_options_replacer_course_id, $event_options_replacer_quiz_id, $JLMS_DB;
				switch ($event_option) {
					case 'title':
					if ($event_options_replacer_quiz_id) {
						$query = "SELECT lpath_id FROM #__lms_learn_path_steps WHERE course_id = $event_options_replacer_course_id AND item_id = $event_options_replacer_quiz_id AND step_type = 5";
						$JLMS_DB->SetQuery($query);
						$lp = $JLMS_DB->LoadResult();
						if ($lp) {
							$query = "SELECT a.title FROM #__events_sessions as a, #__events_lms_lpaths as b WHERE a.session_id = b.session_id AND b.course_id = $event_options_replacer_course_id AND b.lpath_id = $lp";
							$JLMS_DB->SetQuery($query);
							$et = $JLMS_DB->LoadResult();
							if ($et) {
								$ret_str = $et;
							}
						}
					}
					break;
					case 'credit':
						$query = "SELECT lpath_id FROM #__lms_learn_path_steps WHERE course_id = $event_options_replacer_course_id AND item_id = $event_options_replacer_quiz_id AND step_type = 5";
						$JLMS_DB->SetQuery($query);
						$lp = $JLMS_DB->LoadResult();
						if ($lp) {
							$query = "SELECT c.event_credit FROM #__events_sessions as a, #__events_lms_lpaths as b, #__events_advance as c WHERE a.session_id = b.session_id AND a.session_id = c.session_id AND b.course_id = $event_options_replacer_course_id AND b.lpath_id = $lp";
							$JLMS_DB->SetQuery($query);
							$et = $JLMS_DB->LoadResult();
							if ($et) {
								$ret_str = $et;
							}
						}
					break;
				}
			}
			return $ret_str;
		}
	}

	$GLOBALS['event_options_replacer_user_id'] = $user_id;
	$GLOBALS['event_options_replacer_course_id'] = $course_id;
	$quiz_id = isset($txt_mes_obj->quiz_id) ? $txt_mes_obj->quiz_id : 0;
	$GLOBALS['event_options_replacer_quiz_id'] = $quiz_id;
	$regex = "#\#event_(.*?)\##s";
	$new_text = preg_replace_callback( $regex, 'EventOptionsReplacer', $font_text );
	return $new_text;
}

function ReplaceQuizAnswers($font_text, $txt_mes_obj, $user_id, $course_id) {
	global $JLMS_DB;

	if (!function_exists('QuizAnswersReplacer')) {
		function QuizAnswersReplacer( &$matches ) {
			global $quiz_ans_replacer_stu_quiz_id, $quiz_ans_replacer_user_id, $quiz_ans_replacer_course_id, $quiz_ans_replacer_quiz_id;
			$result = preg_match('/\d+/',$matches[1],$found);
			$id = @$found[0];
			$id = intval($id);
	
			$ret_str = "";
	
			if ($id > 0 && $quiz_ans_replacer_stu_quiz_id && $quiz_ans_replacer_quiz_id && $quiz_ans_replacer_course_id && $quiz_ans_replacer_user_id) {
				global $JLMS_DB;
				$query = "SELECT c_pool FROM #__lms_quiz_t_question WHERE c_id = $id AND c_type = 20";
				$JLMS_DB->SetQuery($query);
				$c_pool_id = $JLMS_DB->LoadResult();
	
				$query = "SELECT a.c_type, d.c_id, a.c_pool FROM #__lms_quiz_t_question as a, #__lms_quiz_t_quiz as b, #__lms_quiz_r_student_quiz as c, #__lms_quiz_r_student_question as d"
				. "\n" . " WHERE a.c_id = $id AND a.c_quiz_id = b.c_id AND b.c_id = $quiz_ans_replacer_quiz_id AND b.course_id = $quiz_ans_replacer_course_id"
				. "\n" . " AND c.c_quiz_id = b.c_id AND c.c_id = $quiz_ans_replacer_stu_quiz_id AND c.c_student_id = $quiz_ans_replacer_user_id AND d.c_question_id = a.c_id AND d.c_stu_quiz_id = c.c_id";
				$JLMS_DB->SetQuery($query);
				$tmp_obj = $JLMS_DB->LoadObject();
				if (is_object($tmp_obj)) {
					$c_type = $tmp_obj->c_type;
					if ($c_type == 20 && $c_pool_id) {
						$query = "SELECT c_type FROM #__lms_quiz_t_question WHERE c_id = $c_pool_id";
						$JLMS_DB->SetQuery($query);
						$c_type = $JLMS_DB->LoadResult();
					}
					switch($c_type) {
						case 1:
						case 3:
							$query = "SELECT b.c_choice FROM #__lms_quiz_r_student_choice as a, #__lms_quiz_t_choice as b WHERE a.c_sq_id = $tmp_obj->c_id AND a.c_choice_id = b.c_id";
							$JLMS_DB->SetQuery($query);
							$ret_str = $JLMS_DB->LoadResult();
						break;
						case 6:
							$query = "SELECT c_answer FROM #__lms_quiz_r_student_blank WHERE c_sq_id = $tmp_obj->c_id";
							$JLMS_DB->SetQuery($query);
							$ret_str = $JLMS_DB->LoadResult();
						break;
						case 8:
							$query = "SELECT c_answer FROM #__lms_quiz_r_student_survey WHERE c_sq_id = $tmp_obj->c_id";
							$JLMS_DB->SetQuery($query);
							$ret_str = $JLMS_DB->LoadResult();
						break;
					}
				}
			}
			return $ret_str;
		}
	}

	$new_text = $font_text;

	$stu_quiz_id = isset($txt_mes_obj->stu_quiz_id) ? $txt_mes_obj->stu_quiz_id : 0;
	$quiz_id = isset($txt_mes_obj->quiz_id) ? $txt_mes_obj->quiz_id : 0;
	$GLOBALS['quiz_ans_replacer_stu_quiz_id'] = $stu_quiz_id;
	$GLOBALS['quiz_ans_replacer_user_id'] = $user_id;
	$GLOBALS['quiz_ans_replacer_course_id'] = $course_id;
	$GLOBALS['quiz_ans_replacer_quiz_id'] = $quiz_id;
	$regex = "#\#question_(.*?)\##s";
	$new_text = preg_replace_callback( $regex, 'QuizAnswersReplacer', $font_text );

	return $new_text;
}
function ReplaceCBProfileOptions($font_text, $txt_mes_obj, $user_id, $course_id) {
	require_once(_JOOMLMS_FRONT_HOME . '/includes/classes/lms.cb_join.php');
	$all_cb_f = JLMSCBJoin::get_Assocarray();
	foreach ($all_cb_f as $cbf) {
		$tstr = '#'.$cbf.'#';
		$first_pos = strpos( $font_text,$tstr);
		if ($first_pos !== false) {
			$c = JLMSCBJoin::getASSOC($cbf);
			$font_text = str_replace($tstr, $c, $font_text);
		}
	}
	return $font_text;
}

function ReplaceUPN($font_text, $txt_mes_obj, $user_id, $course_id) {
	//$font_text = str_replace('#reg_answer#', $spec_answer, $font_text);
	$pos = strpos($font_text, '#UPN#');
	$quiz_id = isset($txt_mes_obj->quiz_id) ? $txt_mes_obj->quiz_id : 0;
	$stu_quiz_id = isset($txt_mes_obj->stu_quiz_id) ? $txt_mes_obj->stu_quiz_id : 0;
	if ($pos === false) {
		
	} else {
		$c = '';
		if ($quiz_id) {
			global $JLMS_DB;

			$query = "SELECT lpath_id FROM #__lms_learn_path_steps WHERE course_id = $course_id AND item_id = $quiz_id AND step_type = 5";
			$JLMS_DB->SetQuery($query);
			$lp = $JLMS_DB->LoadResult();
			if ($lp) {
				$query = "SELECT d.em_upn FROM #__events_sessions as a, #__events_advance as d, #__events_lms_lpaths as b WHERE a.session_id = b.session_id AND a.session_id = d.session_id AND b.course_id = $course_id AND b.lpath_id = $lp";
				$JLMS_DB->SetQuery($query);
				$c = $JLMS_DB->LoadResult();
			}
		}
		$font_text = str_replace('#reg_answer#', $c, $font_text);
	}

	if (!function_exists('UPNReplacer')) {
		function UPNReplacer( &$matches ) {
			global $upn_replacer_user_id, $upn_replacer_course_id, $JLMS_DB, $upn_replacer_stu_quiz_id, $upn_replacer_quiz_id, $JLMS_DB;
			$result = preg_match('/\d+/',$matches[1],$found);
			$id1 = @$found[0];
			$id1 = intval($id1);
			
			$result2 = preg_match('/\d+/',$matches[2],$found2);
			$id2 = @$found2[0];
			$id2 = intval($id2);
			$ret_str = "";
			$is_live = false;
			if ($id1 > 0 && $id2 > 0) {
				if ($upn_replacer_quiz_id && $upn_replacer_stu_quiz_id) {
					$query = "SELECT c_pool FROM #__lms_quiz_t_question WHERE c_id = $id1 AND c_type = 20";
					$JLMS_DB->SetQuery($query);
					$c_pool_id = $JLMS_DB->LoadResult();
		
					$query = "SELECT a.c_type, d.c_id, a.c_pool FROM #__lms_quiz_t_question as a, #__lms_quiz_t_quiz as b, #__lms_quiz_r_student_quiz as c, #__lms_quiz_r_student_question as d"
					. "\n" . " WHERE a.c_id = $id1 AND a.c_quiz_id = b.c_id AND b.c_id = $upn_replacer_quiz_id AND b.course_id = $upn_replacer_course_id"
					. "\n" . " AND c.c_quiz_id = b.c_id AND c.c_id = $upn_replacer_quiz_id AND c.c_student_id = $upn_replacer_user_id AND d.c_question_id = a.c_id AND d.c_stu_quiz_id = c.c_id";
					$JLMS_DB->SetQuery($query);
					$tmp_obj = $JLMS_DB->LoadObject();
					if (is_object($tmp_obj)) {
						$c_type = $tmp_obj->c_type;
						if ($c_type == 20 && $c_pool_id) {
							$query = "SELECT c_type FROM #__lms_quiz_t_question WHERE c_id = $c_pool_id";
							$JLMS_DB->SetQuery($query);
							$c_type = $JLMS_DB->LoadResult();
						}
						switch($c_type) {
							case 1:
								$query = "SELECT b.c_choice FROM #__lms_quiz_r_student_choice as a, #__lms_quiz_t_choice as b WHERE a.c_sq_id = $tmp_obj->c_id AND a.c_choice_id = b.c_id";
								$JLMS_DB->SetQuery($query);
								$ret_str = $JLMS_DB->LoadResult();
								$live_parts = array('Live Audio Conference','Live Satellite Broadcast','Live Webcast');
								if (in_array($ret_str, $live_parts)) {
									$is_live = true;
								}
							break;
						}
					}
				}
				if ($is_live == true) {
					$is_live = false;
					$query = "SELECT c_pool FROM #__lms_quiz_t_question WHERE c_id = $id2 AND c_type = 20";
					$JLMS_DB->SetQuery($query);
					$c_pool_id = $JLMS_DB->LoadResult();
		
					$query = "SELECT a.c_type, d.c_id, a.c_pool FROM #__lms_quiz_t_question as a, #__lms_quiz_t_quiz as b, #__lms_quiz_r_student_quiz as c, #__lms_quiz_r_student_question as d"
					. "\n" . " WHERE a.c_id = $id2 AND a.c_quiz_id = b.c_id AND b.c_id = $upn_replacer_quiz_id AND b.course_id = $upn_replacer_course_id"
					. "\n" . " AND c.c_quiz_id = b.c_id AND c.c_id = $upn_replacer_quiz_id AND c.c_student_id = $upn_replacer_user_id AND d.c_question_id = a.c_id AND d.c_stu_quiz_id = c.c_id";
					$JLMS_DB->SetQuery($query);
					$tmp_obj = $JLMS_DB->LoadObject();
					if (is_object($tmp_obj)) {
						$c_type = $tmp_obj->c_type;
						if ($c_type == 20 && $c_pool_id) {
							$query = "SELECT c_type FROM #__lms_quiz_t_question WHERE c_id = $c_pool_id";
							$JLMS_DB->SetQuery($query);
							$c_type = $JLMS_DB->LoadResult();
						}
						switch($c_type) {
							case 6:
							$query = "SELECT c_answer FROM #__lms_quiz_r_student_blank WHERE c_sq_id = $tmp_obj->c_id";
							$JLMS_DB->SetQuery($query);
							$ret_str = $JLMS_DB->LoadResult();
							if ($ret_str == date('m-d-Y')) {
								$is_live = true;
							}
						break;
						}
					}
				}
			}
			$rt_str = '';
			$query = "SELECT lpath_id FROM #__lms_learn_path_steps WHERE course_id = $upn_replacer_course_id AND item_id = $upn_replacer_quiz_id AND step_type = 5";
			$JLMS_DB->SetQuery($query);
			$lp = $JLMS_DB->LoadResult();
			if ($lp) {
				if ($is_live) {
					$query = "SELECT d.live_upn FROM #__events_sessions as a, #__events_advance as d, #__events_lms_lpaths as b WHERE a.session_id = b.session_id AND a.session_id = d.session_id AND b.course_id = $course_id AND b.lpath_id = $lp";
					$JLMS_DB->SetQuery($query);
					$rt_str = $JLMS_DB->LoadResult();
				} else {
					$query = "SELECT d.em_upn FROM #__events_sessions as a, #__events_advance as d, #__events_lms_lpaths as b WHERE a.session_id = b.session_id AND a.session_id = d.session_id AND b.course_id = $course_id AND b.lpath_id = $lp";
					$JLMS_DB->SetQuery($query);
					$rt_str = $JLMS_DB->LoadResult();
				}
			}
			return $ret_str;
		}
	}

	$GLOBALS['upn_replacer_user_id'] = $user_id;
	$GLOBALS['upn_replacer_course_id'] = $course_id;
	$GLOBALS['upn_replacer_quiz_id'] = $quiz_id;
	$GLOBALS['upn_replacer_stu_quiz_id'] = $stu_quiz_id;
	$regex = "#\#UPN_(.*?)_(.*?)\##s";
	$new_text = preg_replace_callback( $regex, 'UPNReplacer', $font_text );
	return $new_text;
}

function resize_png_image($img,$newWidth,$newHeight,$target, $with_proportions = true){
	$srcImage=imagecreatefrompng($img);
	if($srcImage==''){
		return FALSE;
	}
	$srcWidth=imagesx($srcImage);
	$srcHeight=imagesy($srcImage);
    if ($with_proportions) {
		$percentage=(double)$newWidth/$srcWidth;
		$destHeight=round($srcHeight*$percentage)-1;
		$destWidth=round($srcWidth*$percentage)-1;
		if($destHeight > $newHeight){
			// if the width produces a height bigger than we want, calculate based on height
			$percentage=(double)$newHeight/$srcHeight;
			$destHeight=round($srcHeight*$percentage)-1;
			$destWidth=round($srcWidth*$percentage)-1;
		}
	} else {
		$destHeight=$newHeight;
		$destWidth=$newWidth;
	}
	$destImage=imagecreatetruecolor($destWidth-1,$destHeight-1);
	if(!imagealphablending($destImage,FALSE)){
		return FALSE;
	}
	if(!imagesavealpha($destImage,TRUE)){
		return FALSE;
	}
	if(!imagecopyresampled($destImage,$srcImage,0,0,0,0,$destWidth,$destHeight,$srcWidth,$srcHeight)){
		return FALSE;
	}
	if(!imagepng($destImage,$target)){
		return FALSE;
	}
	imagedestroy($destImage);
	imagedestroy($srcImage);
	return TRUE;
}

function JLMS_GB_getUserCertificates($id, $user_id, &$lists) {
	/**
	 * Certificates MOD - 04.10.2007 (DEN)
	 * We will show the list of all achieved certificates in the User Gradebook
	 */
	global $JLMS_DB;
	$JLMS_ACL = & JLMSFactory::getACL();
	$query = "SELECT * FROM #__lms_quiz_t_quiz WHERE course_id = '".$id."' AND c_certificate <> 0 ORDER BY c_title";
	$JLMS_DB->SetQuery( $query );
	$quiz_rows = $JLMS_DB->LoadObjectList();
	
	$p = array();
	foreach ($quiz_rows as $qrow) {
		$pp = new stdClass();
		$pp->gbi_id = $qrow->c_id;
		$pp->user_pts = 0;
		$pp->user_status = -1;
		$pp->quiz_name = '';
		$pp->crtf_id = '';
		$p[] = $pp;
	}

	$certificates = array();
	$quiz_ans = array();
	if (count($quiz_rows)) {
		$query = "SELECT a.*, b.c_full_score, b.c_title, b.c_certificate FROM #__lms_quiz_results as a, #__lms_quiz_t_quiz as b WHERE a.course_id = '".$id."'"
		. "\n AND a.quiz_id = b.c_id AND a.user_id = $user_id ORDER BY a.user_id, a.quiz_id";
		$JLMS_DB->SetQuery( $query );
		$quiz_ans = $JLMS_DB->LoadObjectList();
		
		
		
		$j = 0;
		while ($j < count($quiz_ans)) {
			if ($quiz_ans[$j]->user_id == $user_id) {
				$k = 0;
				while ($k < count($p)) {
					if ($p[$k]->gbi_id == $quiz_ans[$j]->quiz_id) {
						$p[$k]->user_pts = $quiz_ans[$j]->user_score;
						$p[$k]->user_status = $quiz_ans[$j]->user_passed;
						$p[$k]->quiz_name = $quiz_ans[$j]->c_title;
						$p[$k]->crtf_id = $quiz_ans[$j]->c_certificate;
						$p[$k]->user_score = $quiz_ans[$j]->user_score;
						$p[$k]->quiz_max_score = $quiz_ans[$j]->quiz_max_score;
					}
					$k ++;
				}
			}
			$j ++;
		}

		$certificates = array();
		
		foreach ($p as $pp) {
			if ($pp->user_status == 1) {
				$query = "SELECT * FROM #__lms_quiz_r_student_quiz WHERE c_quiz_id = $pp->gbi_id AND c_student_id = $user_id AND c_total_score = $pp->user_pts AND c_passed = 1 LIMIT 0,1";
				$JLMS_DB->SetQuery( $query );
				$u_res = $JLMS_DB->LoadObject();
				if (is_object($u_res)) {
					$role = $JLMS_ACL->UserRole($JLMS_DB, $user_id, 1);
					$query = "SELECT crtf_date FROM #__lms_certificate_prints WHERE user_id = $user_id AND (role_id = '".$role."' OR role_id = 0)  AND course_id = $id AND quiz_id = $pp->gbi_id AND crtf_id = $pp->crtf_id"
					. "\n ORDER BY role_id DESC LIMIT 0,1";

					/* !!!!!!!! Bring from DB date of printing by user role or by default role (only if userrole not found) - imenno dlya etogo tut sidit ORDER i LIMIT*/
					$JLMS_DB->SetQuery( $query );
					$crtf_date = $JLMS_DB->LoadResult();
					if (!$crtf_date) {
						$crtf_date = $u_res->c_date_time;
					}
					$ppp = new stdClass();
					$ppp->user_id = $user_id;
					$ppp->stu_quiz_id = $u_res->c_id;
					$ppp->user_unique_id = $u_res->unique_id;
					$ppp->quiz_name = $pp->quiz_name;
					$ppp->crtf_date = $crtf_date;
					$ppp->c_quiz_id = $u_res->c_quiz_id;
					$ppp->user_score = $pp->user_score;
					$ppp->quiz_max_score = $pp->quiz_max_score;
					$certificates[] = $ppp;
				}
			}
		}
	}
	
	$lists['user_quiz_certificates'] = & $certificates;
	/* END of Certificates MOD */
}

}
?>