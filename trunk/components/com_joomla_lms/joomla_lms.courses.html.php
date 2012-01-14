<?php
/**
* joomla_lms.courses.html.php
* (c) JoomaLMS eLearning Software http://www.joomlalms.com/
**/

// no direct access
defined( '_JLMS_EXEC' ) or die( 'Restricted access' );

class JLMS_course_html {

	function viewCourses( &$rows, &$pageNav, $option, $usertype, $lists, $levels ) {
		global $Itemid,	$JLMS_CONFIG, $my, $JLMS_DB, $acl;
		$JLMS_ACL = & JLMSFactory::getACL();

		$pres_icons = new stdClass();
		$pres_icons->mail = 0;
		$pres_icons->already = 0;
		$pres_icons->my = 0;
		$pres_icons->wl = 0;

		$lms_img_path = $JLMS_CONFIG->get('live_site') . '/' . $JLMS_CONFIG->get('lms_path_to_images', 'components/com_joomla_lms/lms_images');
//		$show_fee_col = $JLMS_CONFIG->get('show_fee_column', 1);
		
		$price_fee_type = $JLMS_CONFIG->get('price_fee_type', 1);
		$show_short_description = $JLMS_CONFIG->get('show_short_description', 0);
		$show_course_publish_dates = $JLMS_CONFIG->get('show_course_publish_dates', 0);
		$jlms_cs = $JLMS_CONFIG->get('jlms_cur_sign');
		
		$colspan_sh_description = 4;
		if($show_course_publish_dates){
			$colspan_sh_description = 6;	
		}
		if(!$price_fee_type){
			$colspan_sh_description = $colspan_sh_description - 1;	
		}
		
		$show_course_author = $JLMS_CONFIG->get('show_course_authors', 1);
		$course_id = mosGetParam($_REQUEST,'c_id','');
		if ($course_id){
			//TODO: replace this db query with usage of new lmstitles object
			$query = "SELECT course_name FROM #__lms_courses WHERE id = '$course_id'";
			$JLMS_DB->setQuery($query);
			$course_name = $JLMS_DB->loadResult();
		}
		
		//FLMS multicat
		$multicat = array();
		if ($JLMS_CONFIG->get('multicat_use', 0)) {
			$multicat = array();
			$i=0;
			foreach($lists as $key=>$item){
				if(substr($key, 0, 7) == 'filter_'){
					$multicat[] = $lists['filter_'.$i];
					$i++;
				}
			}
		}
		?>
		
		<script language="javascript" type="text/javascript">
		<!--//--><![CDATA[//><!--
		function submitbutton(pressbutton, course_id) {
			var form = document.adminForm;
			if ( (pressbutton == 'delete_course') || (pressbutton == 'edit_course') || (pressbutton == 'export_course_pre') ) {
				form.id.value = course_id;
				form.task.value = pressbutton;
				form.submit();
			}
			else if( (pressbutton == 'enroll') && (form.boxchecked.value == '0') ){
				alert( "<?php echo _JLMS_ALERT_SELECT_ITEM;?>" );
			}
			else {
				if (pressbutton == 'enroll'){
					form.task.value = 'subscription';
					form.submit();
				}
			}
		}
		<?php
		if ($JLMS_CONFIG->get('multicat_use', 0)) {
		?>
		var old_filters = new Array();
		function read_filter(){
			var form = document.adminForm;
			var count_levels = '<?php echo count($levels);?>';
			for(var i=0;i<parseInt(count_levels);i++){
				if(form['filter_id_'+i] != null){
					old_filters[i] = form['filter_id_'+i].value;
				}
			}
		}
		function write_filter(){
			var form = document.adminForm;
			var count_levels = '<?php echo count($levels);?>';
			var j;
			for(var i=0;i<parseInt(count_levels);i++){
				if(form['filter_id_'+i+''] != null && form['filter_id_'+i+''].value != old_filters[i]){
					j = i;
				}
				if(i > j){
					if(form['filter_id_'+i] != null){
						form['filter_id_'+i].value = 0;	
					}
				}
			}
		}
		<?php
		}
		?>
		<?php
		if($JLMS_CONFIG->get('lms_courses_sortby',0) == 1){	
		?>
		function submitbutton_order(pressbutton, item_id){
			var form = document.adminForm;
			if ((pressbutton == 'fcourse_orderup') || (pressbutton == 'fcourse_orderdown')){
				if (item_id) {
				form.task.value = pressbutton;
				form.row_id.value = item_id;
				form.submit();
				}
			}
		}
		function cf_saveorder(){
			var form = document.adminForm;	
			form.task.value = 'fcourse_save_order';
					form.submit();
				//submitform('fcourse_save_order');
		}
		<?php
		}
		?>	
		//--><!]]>
		</script>
<?php
		$style = "
		table.jlmslist td.sectiontableheader, table.jlmslist th.sectiontableheader{
			white-space: nowrap;
		}";
		$doc = & JFactory::getDocument();
		$doc->addStyleDeclaration($style);
?>
		<form action="<?php echo JURI::base()."index.php?option=$option&amp;Itemid=$Itemid";?>" method="post" name="adminForm">
<?php
	JLMS_TMPL::OpenMT();

	$params = array(
		'show_menu' => true,
		'simple_menu' => true,
	);
	
	JLMS_TMPL::ShowHeader('course', _JLMS_COURSES_COURSES_LIST, $params);
	JLMS_TMPL::ShowPageTip('courses');

	JLMS_TMPL::OpenTS('', ' align="right" style="text-align:right " width="100%"');
		$link = "index.php?option=$option&amp;Itemid=$Itemid&amp;task=courses";
//		echo _JLMS_PN_DISPLAY_NUM . $pageNav->getLimitBox( $link );
//		echo _JLMS_COURSES_FILTER." : ".$lists['courses_type'];
		if ($JLMS_CONFIG->get('multicat_use', 0)){
			echo ((isset($levels[0]->cat_name) && $levels[0]->cat_name != '')?$levels[0]->cat_name:_JLMS_COURSES_COURSES_GROUPS)." ".$lists['filter_0'];
		} else {
			echo _JLMS_COURSES_COURSES_GROUPS." ".$lists['groups_course'];
		}
	JLMS_TMPL::CloseTS();

	if(count($multicat)){
		for($i=0;$i<count($multicat);$i++){
			if($i > 0){
				JLMS_TMPL::OpenTS('', ' align="right" style="text-align:right " width="100%"');
					echo ((isset($levels[$i]->cat_name) && $levels[$i]->cat_name != '')?$levels[$i]->cat_name:_JLMS_COURSES_COURSES_GROUPS)." ".$lists['filter_'.$i];
				JLMS_TMPL::CloseTS();
			}
		}
	}

	$controls = array();
//	if ($usertype == 1) {
	$JLMS_ACL = & JLMSFactory::getACL();
	if ($JLMS_ACL->CheckPermissions('lms', 'create_course')) {
		$controls[] = array('href' => JLMSRoute::_("index.php?option=$option&amp;Itemid=$Itemid&amp;task=add_course"), 'title' => _JLMS_COURSES_NEW, 'img' => 'add');
		$controls[] = array('href' => JLMSRoute::_("index.php?option=$option&amp;Itemid=$Itemid&amp;task=course_import"), 'title' => _JLMS_COURSES_IMPORT, 'img' => 'courseimport');
		$controls[] = array('href' => 'spacer');
	}
	$controls[] = array('href' => "javascript:submitbutton('enroll','');", 'title' => _JLMS_ENROLL, 'img' => 'publish');
	JLMS_TMPL::ShowControlsFooter($controls, '', false);

	JLMS_TMPL::OpenTS();
	 ?>		
	 			<table width="100%" cellpadding="0" cellspacing="0" border="0" class="<?php echo JLMSCSS::_('jlmslist');?>">
					<tr>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center" width="1%">#</<?php echo $JLMS_CONFIG->get('tableheader_tag', 'td');?>>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center" width="1%">&nbsp;</<?php echo $JLMS_CONFIG->get('tableheader_tag', 'td');?>>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" width="35%" align="left"><?php echo _JLMS_COURSES_TBL_HEAD_NAME;?></<?php echo $JLMS_CONFIG->get('tableheader_tag', 'td');?>>
						
						<?php
						if(isset($lists['extra_columns']) && count($lists['extra_columns'])){
							foreach($lists['extra_columns'] as $column){
								?>
								<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" width="20%" align="left"><?php echo ucfirst($column->name);?></<?php echo $JLMS_CONFIG->get('tableheader_tag', 'td');?>>
								<?php
							}
						}
						?>
						
						
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" width="20%" align="left"><?php echo _JLMS_COURSES_COURSES_CAT;?></<?php echo $JLMS_CONFIG->get('tableheader_tag', 'td');?>>
						<?php
						if (!$JLMS_ACL->CheckPermissions('lms', 'order_courses')) {
						if($show_course_publish_dates){
						?>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" width="6%" align="left" nowrap="nowrap"><?php echo _JLMS_COURSES_ST_DATE;?></<?php echo $JLMS_CONFIG->get('tableheader_tag', 'td');?>>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" width="6%" align="left" nowrap="nowrap"><?php echo _JLMS_COURSES_END_DATE;?></<?php echo $JLMS_CONFIG->get('tableheader_tag', 'td');?>>
						<?php
						}
						}
						?>
						<?php 
						if($price_fee_type == 1){?>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" width="4%" align="left" nowrap="nowrap"><?php echo _JLMS_COURSES_FEETYPE;?></<?php echo $JLMS_CONFIG->get('tableheader_tag', 'td');?>>
						<?php 
						} else if($price_fee_type == 2){?>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" width="4%" align="left" nowrap="nowrap"><?php echo _JLMS_COURSES_PRICE;?></<?php echo $JLMS_CONFIG->get('tableheader_tag', 'td');?>>							
						<?php
						}
						if($JLMS_CONFIG->get('lms_courses_sortby',0) == 1){	
							if ($JLMS_ACL->CheckPermissions('lms', 'order_courses')){
							?>
						<<?php echo JLMSCSS::tableheadertag();?> colspan="2" width="5%" class="<?php echo JLMSCSS::_('sectiontableheader');?>">
									<?php echo _JLMS_REORDER;?>
									</<?php echo JLMSCSS::tableheadertag();?>>
									<<?php echo JLMSCSS::tableheadertag();?> width="2%" class="<?php echo JLMSCSS::_('sectiontableheader');?>">
									<?php echo _JLMS_ORDER;?>
									</<?php echo JLMSCSS::tableheadertag();?>>
									<<?php echo JLMSCSS::tableheadertag();?> width="1%" class="<?php echo JLMSCSS::_('sectiontableheader');?>">
									<a href="javascript:cf_saveorder();"><img src="<?php echo $lms_img_path?>/toolbar/tlb_filesave.png" border="0" width="16" height="16" alt="<?php echo _JLMS_SAVEORDER;?>" title="<?php echo _JLMS_SAVEORDER;?>" /></a>
									</<?php echo JLMSCSS::tableheadertag();?>>
						<?php 
							}
						}
						if ($JLMS_ACL->CheckPermissions('lms', 'create_course')) { ?>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" width="40" align="center"><?php echo _JLMS_COURSES_PUBLISHED_COURSE;?></<?php echo $JLMS_CONFIG->get('tableheader_tag', 'td');?>>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" width="40" align="center"><?php echo _JLMS_COURSES_DELETE_COURSE;?></<?php echo $JLMS_CONFIG->get('tableheader_tag', 'td');?>>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" width="40" align="center"><?php echo _JLMS_COURSES_EDIT_COURSE;?></<?php echo $JLMS_CONFIG->get('tableheader_tag', 'td');?>>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" width="30" align="center"><?php echo _JLMS_COURSES_EXPORT;?></<?php echo $JLMS_CONFIG->get('tableheader_tag', 'td');?>>
						<?php }?>
					</tr>
				<?php
				$k = 1;
				$iii = 0;
				$number_of_columns_in_table = 0;
				$show_paid_courses = $JLMS_CONFIG->get('show_paid_courses', 1);
				for ($i=0, $n=count($rows); $i < $n; $i++) {
					$row = $rows[$i];
					$number_of_columns_in_table = 0;
					$course_usertype = 0;
					if ( in_array($row->id, $JLMS_CONFIG->get('teacher_in_courses',array(0))) ) {
						$course_usertype = 1;
					} elseif ( in_array($row->id, $JLMS_CONFIG->get('student_in_courses',array(0))) ) {
						$course_usertype = 2;
					}
					//$course_usertype = JLMS_GetUserType($my->id, $row->id);
					if (!$show_paid_courses && !$course_usertype && $row->paid){
						continue;
					}
					$course_descr = strip_tags($row->course_description);
					if (strlen($course_descr) > 100) {
						$course_descr = substr($course_descr, 0, 100)."...";
					}
					$link 	= sefRelToAbs("index.php?option=".$option."&Itemid=".$Itemid."&task=details_course&id=". $row->id);
					$checked='';
					if ($course_usertype) {
						$bg = '';
						if ($course_usertype == 1) {
							$checked = '<img class="JLMS_png" src="'.$lms_img_path.'/toolbar/tlb_courses.png" width="16" height="16" border="0" alt="" title="" />';
							$pres_icons->my = 1;
						} elseif ($course_usertype == 2) {
							$checked = '<img class="JLMS_png" src="'.$lms_img_path.'/buttons/btn_complete.png" width="16" height="16" border="0" alt="" title="" />';
							$pres_icons->already = 1;
						}
					} elseif ($row->self_reg == 0) {
						$checked = "<a href='mailto:".$row->email."'><img class='JLMS_png' src=\"".$lms_img_path."/dropbox/dropbox_corr.png\" width='16' height='16' border='0' alt='' title='' /></a>";
						$pres_icons->mail = 1;
					} else {
						if ($row->in_wl) {
							$checked = '<img class="JLMS_png" src="'.$lms_img_path.'/buttons/btn_waiting.png" width="16" height="16" border="0" alt="" title="" />';
							$pres_icons->wl = 1;
						} else {
							$can_enroll = JLMS_checkCourseGID($my->id, $row->gid);
							if ($can_enroll) {
								$checked = mosHTML::idBox( $i, $row->id);
							} else {
								$checked = "<a href='mailto:".$row->email."'><img class='JLMS_png' src=\"".$lms_img_path."/dropbox/dropbox_corr.png\" width='16' height='16' border='0' alt='' title='' /></a>";
								$pres_icons->mail = 1;
							}
						}
					}?>
					<?php
					if($show_short_description && !$JLMS_ACL->CheckPermissions('lms', 'order_courses')){
					?>
					<tr valign="middle" style="vertical-align:middle" class="<?php echo JLMSCSS::_('sectiontableentry2');?>">
					<?php
					} else {
					?>
					<tr valign="middle" style="vertical-align:middle" class="<?php echo JLMSCSS::_('sectiontableentry'.$k);?>">					
					<?php
					}
					$number_of_columns_in_table = $number_of_columns_in_table + 4; // index, checkbox, name,category
					?>
						<td align="center"><?php echo ( $pageNav->limitstart + $iii + 1 );?></td>
						<td align="center"><?php echo $checked;?></td>
						<td align="left" class="jlms_coursename_cont_td">
							<a href="<?php echo $link;?>" title="<?php echo str_replace('"','&quot;',$row->course_name);?>">
								<?php echo $row->course_name;?>
							</a>
							<br />
							<?php if($show_course_author){?>
								<span class="small"><?php echo _JLMS_HOME_AUTHOR . "&nbsp;" . $row->user_fullname;?></span>
							<?php } ?>
							<?php 
							if(isset($row->plugin_course_list_extra_information) && count($row->plugin_course_list_extra_information)){
								echo '<br />';
								
								$fields = $row->plugin_course_list_extra_information;
								$c=1;
								foreach($fields as $field){
									echo '<span class="small">'.$field->name.': '.$field->value.'</span>';
									if($c < count($fields)){
										echo '<br />';
									}
									$c++;
								}
							}
							?>
						</td>
						
						<?php
						if(isset($lists['extra_columns']) && count($lists['extra_columns'])){
							foreach($lists['extra_columns'] as $column){
							?>
								<td align="left">
								<?php 
								if(isset($row->plugin_course_list_extra_column) && count($row->plugin_course_list_extra_column)){
									$fields = $row->plugin_course_list_extra_column;
									foreach($fields as $field){
										
//										echo $field->name.' '.$column->value;
//										echo '<br />';
										
										if($column->value == $field->name){
											echo $field->value;
										}
									}
								}
								?>
								</td>
							<?php
							}
						}
						?>
						
						<td align="left"><?php echo $row->c_category?$row->c_category:'&nbsp;';?></td>
						<?php
						if (!$JLMS_ACL->CheckPermissions('lms', 'order_courses')) {
							if($show_course_publish_dates){
								$number_of_columns_in_table = $number_of_columns_in_table + 2;
								?>
								<td align="center" nowrap="nowrap"><?php echo $row->publish_start?JLMS_dateToDisplay($row->start_date):'&nbsp;';?></td>
								<td align="center" nowrap="nowrap"><?php echo $row->publish_end?JLMS_dateToDisplay($row->end_date):'&nbsp;';?></td>
								<?php
							}
						}
						if($price_fee_type) {
							if($price_fee_type == 1) {
								$number_of_columns_in_table ++;
							?>
							<td align="center"><?php echo $row->paid ? _JLMS_COURSES_PAID : _JLMS_COURSES_FREE;?></td>
							<?php
							} else if($price_fee_type == 2) {
								$number_of_columns_in_table ++;
							?>
							<td align="center"><?php echo $row->paid ? $jlms_cs.sprintf('%.2f',round($row->course_price,2)) : _JLMS_COURSES_FREE;?></td>	
							<?php
							}		
						}
						if($JLMS_CONFIG->get('lms_courses_sortby',0) == 1){	
							if ($JLMS_ACL->CheckPermissions('lms', 'order_courses')) {
								$number_of_columns_in_table = $number_of_columns_in_table + 4; //up, down, number(colspan=2)
								?>
								<td>
								<?php echo JLMS_orderUpIcon( $i, $row->id, true, 'fcourse_orderup' ); ?>
								</td>
								<td>
								<?php echo JLMS_orderDownIcon( $i, $n, $row->id, true, 'fcourse_orderdown' ); ?>
								</td>
								<td align="center" colspan="2">
								<input type="text" name="order[]" size="5" value="<?php echo $row->ordering; ?>" class="text_area" style="text-align: center" />
								<input type="checkbox" name="t_chk[]" value="<?php echo $row->id?>" style="visibility:hidden; display:none;" checked="checked" />
								</td>
							<?php 
							}
						} 
						if ($JLMS_ACL->CheckPermissions('lms', 'create_course')) {
							$number_of_columns_in_table = $number_of_columns_in_table + 4; // publish, delete, edit, export
						?>			
						<td align="center" valign="middle">
							<?php if ($course_usertype == 1) {
								$time_p = ($row->publish_start || $row->publish_end);
								$alt = ($row->published)?($time_p?_JLMS_STATUS_PUB:_JLMS_STATUS_PUB):_JLMS_STATUS_UNPUB;
								$image = ($row->published)?($time_p?'btn_publish_wait.png':'btn_accept.png'):'btn_cancel.png';
								$add_options = '';
								$html_txt = '';
								$html_txt_title = '';
								if ($time_p) {
									$html_txt_title = $alt;
									$is_expired = false;
									$is_future_course = false;
									$html_txt = '<table cellpadding=0 cellspacing=0 border=0>';
									if ($row->publish_start) {
										$html_txt .= '<tr><td align=left>'._JLMS_START_DATE.'&nbsp;</td><td align=left>'.$row->start_date.'</td></tr>';
										$s_date = strtotime($row->start_date);
										if ($s_date > time()) {
											$is_future_course = true;
										}
									} else {
										$html_txt .= '<tr><td align=left>'._JLMS_START_DATE.'&nbsp;</td><td align=left>-</td></tr>';
									}
									if ($row->publish_end) {
										$html_txt .= '<tr><td align=left>'._JLMS_END_DATE.'&nbsp;</td><td align=left>'.$row->end_date.'</td></tr>';
										$e_date = strtotime($row->end_date);
										if ($e_date < time()) {
											$is_expired = true;
										}
									} else {
										$html_txt .= '<tr><td align=left>'._JLMS_END_DATE.'&nbsp;</td><td align=left>-</td></tr>';
									}
									if ($is_expired) {
										$alt = _JLMS_STATUS_EXPIRED;
										$html_txt_title = _JLMS_STATUS_EXPIRED;
										$image = 'btn_expired.png';
									} elseif ($is_future_course) {
										$alt = _JLMS_STATUS_FUTURE_COURSE;
										if (!$row->published) {
											$alt .= ' / '._JLMS_STATUS_UNPUB;
										}
										$html_txt_title = $alt;
										$image = 'btn_expired.png';
										if ($JLMS_CONFIG->get('show_future_courses', false) && $is_future_course && $row->published) {
											$image = 'btn_publish_wait.png';
										}
									}

									$html_txt .= '</table>';
								}
								$state = ($row->published)?0:1;
								$plink = JLMSRoute::_("index.php?option=".$option."&amp;Itemid=".$Itemid."&amp;task=change_course&amp;state=".$state."&amp;id=".$row->id);
								$inside_tag = '<img class="JLMS_png" src="'.$lms_img_path.'/toolbar/'.$image.'" width="16" height="16" border="0" alt="'.$alt.'" title="'.$alt.'" />';
								echo JLMS_toolTip($html_txt_title, $html_txt, $inside_tag, $plink, 1, 30, true, 'jlms_ttip jlms_img_link');
								?>
							<?php } else { echo '&nbsp;'; }?>
						</td>
						<td align="center" valign="middle">
							<?php if ($course_usertype == 1) {?>
							<a class="jlms_img_link" href="javascript:submitbutton('delete_course',<?php echo $row->id;?>);" title="<?php echo _JLMS_DELETE;?>"><img class="JLMS_png" src="<?php echo $lms_img_path;?>/toolbar/btn_delete.png" width="16" height="16" border="0" alt="<?php echo _JLMS_DELETE;?>" title="<?php echo _JLMS_DELETE;?>" /></a>
							<?php } else { echo '&nbsp;'; }?>
						</td>
						<td align="center" valign="middle">
							<?php if ($course_usertype == 1) {?>
							<a class="jlms_img_link" href="javascript:submitbutton('edit_course',<?php echo $row->id;?>);" title="<?php echo _JLMS_EDIT;?>"><img class="JLMS_png" src="<?php echo $lms_img_path;?>/toolbar/btn_edit.png" width="16" height="16" border="0" alt="<?php echo _JLMS_EDIT;?>" title="<?php echo _JLMS_EDIT;?>" /></a>
							<?php } else { echo '&nbsp;'; }?>
						</td>
						<td align="center" valign="middle">
							<?php if ($course_usertype == 1) {?>
							<a class="jlms_img_link" href="javascript:submitbutton('export_course_pre',<?php echo $row->id;?>);" title="<?php echo _JLMS_EDIT;?>"><img class="JLMS_png" src="<?php echo $lms_img_path;?>/toolbar/btn_export.png" width="16" height="16" border="0" alt="<?php echo _JLMS_COURSES_EXPORT;?>" title="<?php echo _JLMS_COURSES_EXPORT;?>" /></a>
							<?php } else { echo '&nbsp;'; }?>
						</td>
						<?php }?>
					</tr>
					<?php
					if (!$JLMS_ACL->CheckPermissions('lms', 'order_courses')) {
					if($show_short_description){
						if(strlen($row->course_sh_description)){
						?>
						<tr class="<?php echo JLMSCSS::_('sectiontableentry1');?>">
							<td>&nbsp;</td>
							<td colspan="<?php echo $number_of_columns_in_table ? ($number_of_columns_in_table - 1) : $colspan_sh_description;?>" style="text-align: justify;">
								<?php echo $row->course_sh_description;?>
							</td>
						</tr>
						<?php
						}
					}
					}
					?>
					<?php
					$k = 3 - $k;
					$iii++;
				}?>
				<?php if ($pageNav->isMultiPages()) { ?>
					<tr>
						<?php
						if($JLMS_ACL->CheckPermissions('lms', 'order_courses')){
							$colspan_sh_description = $colspan_sh_description + 7;	
						}
						?>
						<td class="<?php echo JLMSCSS::_('jlmslist-footer_td');?>" colspan="<?php echo $number_of_columns_in_table ? $number_of_columns_in_table : ($colspan_sh_description + 1);?>" align="center">
							<div align="center" style="text-align:center">
							<?php
							$link = "index.php?option=$option&amp;Itemid=$Itemid&amp;task=courses";
							echo _JLMS_PN_DISPLAY_NUM . $pageNav->getLimitBox( $link ).' '.$pageNav->getPagesCounter(); 
							?>
							</div>
							<br />
							<div align="center" style="text-align:center">
							<?php
							$link = "index.php?option=$option&amp;Itemid=$Itemid&amp;task=courses";
							echo $pageNav->writePagesLinks( $link ); ?>
							</div>
						</td>
					</tr>
				<?php } ?>
				</table>
				
	<?php
	JLMS_TMPL::CloseTS();

	JLMS_TMPL::ShowControlsFooter($controls);

	if ($pres_icons->already || $pres_icons->mail || $pres_icons->my || $pres_icons->wl) {
	JLMS_TMPL::OpenTS('',' align="left"'); ?>
			<div class="joomlalms_info_legend">
			<?php if ($pres_icons->already) {?>
				<div style="text-align:left ">
					<img class="JLMS_png" src="<?php echo $lms_img_path;?>/buttons/btn_complete.png" align="top" width="16" height="16" border="0" alt="<?php echo _JLMS_COURSES_ALREADY; ?>" title="<?php echo _JLMS_COURSES_ALREADY;?>" />
					&nbsp;- <?php echo _JLMS_COURSES_ALREADY;?>.
				</div>
			<?php }?>
			<?php if ($pres_icons->mail) {?>
				<div style="text-align:left ">
					<img class='JLMS_png' src="<?php echo $lms_img_path;?>/dropbox/dropbox_corr.png" align="top" width='16' height='16' border='0' alt='' title='' />
					&nbsp;- <?php echo _JLMS_COURSES_ADMIN_SENT;?>
				</div>
			<?php }?>
			<?php if ($pres_icons->my) {?>
				<div style="text-align:left ">
					<img class="JLMS_png" src="<?php echo $lms_img_path;?>/toolbar/tlb_courses.png" align="top" width="16" height="16" border="0" alt="<?php echo _JLMS_COURSES_TITLE_MY_COURSES; ?>" title="<?php echo _JLMS_COURSES_TITLE_MY_COURSES; ?>" />
					&nbsp;- <?php echo _JLMS_COURSES_TITLE_MY_COURSES;?>.
				</div>
			<?php }?>
			<?php if ($pres_icons->wl) {?>
				<div style="text-align:left ">
					<img class="JLMS_png" src="<?php echo $lms_img_path;?>/buttons/btn_waiting.png" align="top" width="16" height="16" border="0" alt="<?php echo _JLMS_COURSES_TITLE_MY_COURSES; ?>" title="<?php echo _JLMS_COURSES_TITLE_MY_COURSES; ?>" />
					&nbsp;- <?php echo _JLMS_COURSES_IN_WL;?>.
				</div>
			<?php }?>
			</div>
<?php
	JLMS_TMPL::CloseTS();
	}
	JLMS_TMPL::CloseMT(); ?>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="id" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="itemid" value="" />
		<input type="hidden" name="row_id" value="0" />
		</form>
		<?php
	}
	
	function viewCourses_blog( &$rows, &$pageNav, $option, $usertype, $lists, $levels ) {
		global $Itemid,	$JLMS_CONFIG, $my, $JLMS_DB, $acl;
		$JLMS_ACL = & JLMSFactory::getACL();

		$pres_icons = new stdClass();
		$pres_icons->mail = 0;
		$pres_icons->already = 0;
		$pres_icons->my = 0;
		$pres_icons->wl = 0;

		$lms_img_path = $JLMS_CONFIG->get('live_site') . '/' . $JLMS_CONFIG->get('lms_path_to_images', 'components/com_joomla_lms/lms_images');
//		$show_fee_col = $JLMS_CONFIG->get('show_fee_column', 1);
		
		$price_fee_type = $JLMS_CONFIG->get('price_fee_type', 1);
		$show_short_description = $JLMS_CONFIG->get('show_short_description', 0);
		$show_course_publish_dates = $JLMS_CONFIG->get('show_course_publish_dates', 0);
		$jlms_cs = $JLMS_CONFIG->get('jlms_cur_sign');
		
		$colspan_sh_description = 4;
		if($show_course_publish_dates){
			$colspan_sh_description = 6;	
		}
		if(!$price_fee_type){
			$colspan_sh_description = $colspan_sh_description - 1;	
		}
		
		$show_course_author = $JLMS_CONFIG->get('show_course_authors', 1);
		$course_id = mosGetParam($_REQUEST,'c_id','');
		if ($course_id){
			$query = "SELECT course_name FROM #__lms_courses WHERE id = '$course_id'";
			$JLMS_DB->setQuery($query);
			$course_name = $JLMS_DB->loadResult();
		}
		
		//FLMS multicat
		$multicat = array();
		if ($JLMS_CONFIG->get('multicat_use', 0)) {
			$multicat = array();
			$i=0;
			foreach($lists as $key=>$item){
				if(substr($key, 0, 7) == 'filter_'){
					$multicat[] = $lists['filter_'.$i];
					$i++;
				}
			}
		}
		?>
		
		<script language="javascript" type="text/javascript">
		<!--//--><![CDATA[//><!--
		function submitbutton(pressbutton, course_id) {
			var form = document.adminForm;
			if ( (pressbutton == 'delete_course') || (pressbutton == 'edit_course') || (pressbutton == 'export_course_pre') ) {
				form.id.value = course_id;
				form.task.value = pressbutton;
				form.submit();
			}
			else if( (pressbutton == 'enroll') && (form.boxchecked.value == '0') ){
				alert( "<?php echo _JLMS_ALERT_SELECT_ITEM;?>" );
			}
			else {
				if (pressbutton == 'enroll'){
					form.task.value = 'subscription';
					form.submit();
				}
			}
		}
		<?php
		if ($JLMS_CONFIG->get('multicat_use', 0)) {
		?>
		var old_filters = new Array();
		function read_filter(){
			var form = document.adminForm;
			var count_levels = '<?php echo count($levels);?>';
			for(var i=0;i<parseInt(count_levels);i++){
				if(form['filter_id_'+i] != null){
					old_filters[i] = form['filter_id_'+i].value;
				}
			}
		}
		function write_filter(){
			var form = document.adminForm;
			var count_levels = '<?php echo count($levels);?>';
			var j;
			for(var i=0;i<parseInt(count_levels);i++){
				if(form['filter_id_'+i+''] != null && form['filter_id_'+i+''].value != old_filters[i]){
					j = i;
				}
				if(i > j){
					if(form['filter_id_'+i] != null){
						form['filter_id_'+i].value = 0;	
					}
				}
			}
		}
		<?php
		}
		?>
		//--><!]]>
		</script>

		<form action="<?php echo $JLMS_CONFIG->getCfg('live_site')."/index.php?option=$option&amp;Itemid=$Itemid";?>" method="post" name="adminForm">
<?php
	JLMS_TMPL::OpenMT();

	$params = array(
		'show_menu' => true,
		'simple_menu' => true,
	);
	
	JLMS_TMPL::ShowHeader('course', _JLMS_COURSES_COURSES_LIST, $params);
	JLMS_TMPL::ShowPageTip('courses');

	JLMS_TMPL::OpenTS('', ' align="right" style="text-align:right " width="100%"');
//		echo _JLMS_PN_DISPLAY_NUM . $pageNav->getLimitBox( $link );
//		echo _JLMS_COURSES_FILTER." : ".$lists['courses_type'];
		if ($JLMS_CONFIG->get('multicat_use', 0)){
			echo ((isset($levels[0]->cat_name) && $levels[0]->cat_name != '')?$levels[0]->cat_name:_JLMS_COURSES_COURSES_GROUPS)." ".$lists['filter_0']."&nbsp;&nbsp;";
		} else {
			echo _JLMS_COURSES_COURSES_GROUPS." ".$lists['groups_course']."&nbsp;&nbsp;";
		}
	JLMS_TMPL::CloseTS();

	if(count($multicat)){
		for($i=0;$i<count($multicat);$i++){
			if($i > 0){
				JLMS_TMPL::OpenTS('', ' align="right" style="text-align:right " width="100%"');
					echo ((isset($levels[$i]->cat_name) && $levels[$i]->cat_name != '')?$levels[$i]->cat_name:_JLMS_COURSES_COURSES_GROUPS)." ".$lists['filter_'.$i]."&nbsp;&nbsp;";
				JLMS_TMPL::CloseTS();
			}
		}
	}

	$controls = array();
	$JLMS_ACL = & JLMSFactory::getACL();
	if ($JLMS_ACL->CheckPermissions('lms', 'create_course')) {
		$controls[] = array('href' => JLMSRoute::_("index.php?option=$option&amp;Itemid=$Itemid&amp;task=add_course"), 'title' => _JLMS_COURSES_NEW, 'img' => 'add');
		$controls[] = array('href' => JLMSRoute::_("index.php?option=$option&amp;Itemid=$Itemid&amp;task=course_import"), 'title' => _JLMS_COURSES_IMPORT, 'img' => 'courseimport');
		$controls[] = array('href' => 'spacer');
		JLMS_TMPL::ShowControlsFooter($controls, '', false);
	}
	//$controls[] = array('href' => "javascript:submitbutton('enroll','');", 'title' => _JLMS_ENROLL, 'img' => 'publish');	

	JLMS_TMPL::OpenTS('', ' style="padding: 5px;"');
	
	if(JLMS_J16version()){
		?>
		<div class="lms_courses_blog">
			<div class="blog-featured">
				<?php
				if(isset($lists['leading_courses']) && $lists['leading_courses']){
				?>
				<div class="items-leading">
					<?php
					$lead_indx = 0;
					foreach($rows as $row){
						if(isset($row->leading_course) && $row->leading_course){
							$leading = 'leading-'.$lead_indx;
							?>
							<div class="<?php echo $leading;?>">
								<h2>
									<a href="<?php echo JRoute::_('index.php?option='.$option.'&task=details_course&id='.$row->id);?>" title="<?php echo $row->course_name;?>">
										<?php echo $row->course_name;?>
									</a>
									<?php
									$_JLMS_PLUGINS = & JLMSFactory::getPlugins();
									$_JLMS_PLUGINS->loadBotGroup('system');
									$plugin_args = array();
									$plugin_args[] = $row->id;
									$_JLMS_PLUGINS->trigger('onShowBlogCourseInfo', $plugin_args);
									?>
									<div class="clr"><!-- --></div>
								</h2>
								<dl class="article-info">
									<?php 
									if($show_course_author){
										?>
										<dd class="createdby">
											<?php echo _JLMS_HOME_AUTHOR . ' ' . $row->user_fullname;?>
										</dd>
										<?php 
									}
									?>
								</dl>
								<?php echo JLMS_ShowText_WithFeatures($row->course_sh_description);?>
								<div class="item-separator"><!-- --></div>
							</div>
							<?php
							$lead_indx++;
						}
					}
					?>
				</div>
				<?php
				}
				
				$class = 'items-row';
				$cols_class = 'cols-'.$lists['menu_params']->get('num_columns', 2);
				$row_class = 'row-0';
				
				$class = $class . ' ' . $cols_class . ' ' . $row_class;
				?>
				<div class="<?php echo $class;?>">
				<?php
				$n=1;
				for($i=0;$i<count($rows);$i++){
					$row = $rows[$i];
					if(!isset($row->leading_course) || !$row->leading_course){
					?>
					
						<?php
						$column_class = 'item';
						$column_class .= ' ' . 'column-'.$n;
						?>
						<div class="<?php echo $column_class;?>">
							<h2>
								<a href="<?php echo JRoute::_('index.php?option='.$option.'&task=details_course&id='.$row->id);?>" title="<?php echo $row->course_name;?>">
									<?php echo $row->course_name;?>
								</a>
								<?php
								$_JLMS_PLUGINS = & JLMSFactory::getPlugins();
								$_JLMS_PLUGINS->loadBotGroup('system');
								$plugin_args = array();
								$plugin_args[] = $row->id;
								$_JLMS_PLUGINS->trigger('onShowBlogCourseInfo', $plugin_args);
								?>
								<div class="clr"><!-- --></div>
							</h2>
							<dl class="article-info">
								<?php 
								if($show_course_author){
									?>
									<dd class="createdby">
										<?php echo _JLMS_HOME_AUTHOR . ' ' . $row->user_fullname;?>
									</dd>
									<?php 
								}
								?>
							</dl>
							
							<?php echo JLMS_ShowText_WithFeatures($row->course_sh_description);?>
							<div class="item-separator"><!-- --></div>
						</div>
						<?php
						if($lists['menu_params']->get('num_columns', 2) == $n){
							?>
							<span class="row-separator"><!-- --></span>
							<?php
							$n=1;
						} else {
							$n++;
						}
					}
				}	
				?>
				</div>
			</div>
		</div>
		<?php
	} else {
		?>
		<table width="100%" cellpadding="0" cellspacing="0" border="0">
			<?php
			if(isset($lists['leading_courses']) && $lists['leading_courses']){
			?>
			<tr>
				<td colspan="<?php echo (is_object($lists['menu_params']) && method_exists($lists['menu_params'], 'get')) ? $lists['menu_params']->get('num_columns', 2) : 2;?>" valign="top">
					<?php
					
					for($x=0;$x<count($rows);$x++){
						$row = $rows[$x];
						if(isset($row->leading_course) && $row->leading_course){
							?>
							<div class="course_info">
								<table width="100%" cellpadding="0" cellspacing="0" border="0">
									<tr>
										<td class="contentheading">
											<a href="<?php echo sefRelToAbs('index.php?option='.$option.'&task=details_course&id='.$row->id);?>" title="<?php echo $row->course_name;?>">
												<?php echo $row->course_name;?>
											</a>
										</td>
									</tr>
								</table>
								<?php if($show_course_author){?>
								<div class="course_author">
									<span class="small"><?php echo _JLMS_HOME_AUTHOR . "&nbsp;" . $row->user_fullname;?></span>
								</div>
								<?php } ?>
								<?php
								$_JLMS_PLUGINS = & JLMSFactory::getPlugins();
								$_JLMS_PLUGINS->loadBotGroup('system');
								$plugin_args = array();
								$plugin_args[] = $row->id;
								$_JLMS_PLUGINS->trigger('onShowBlogCourseInfo', $plugin_args);
								?>
								<div class="course_sh_dsc">
									<?php echo $row->course_sh_description;?>
								</div>
							</div>
							<span class="article_separator">&nbsp;</span>
							<?php
						}
					}
					
					?>
				</td>
			</tr>
			<?php
			}
			?>	
			<tr>
				<?php
				$divider = '';
				$menu_params_columns = (is_object($lists['menu_params']) && method_exists($lists['menu_params'], 'get')) ? $lists['menu_params']->get('num_columns', 2) : 2;
				for($z=0; $z < $menu_params_columns; $z++){
					if($z > 0){$divider = ' column_separator';}
				?>
				<td class="article_column<?php echo $divider;?>" valign="top" width="<?php echo intval(100 / $menu_params_columns);?>%">
					<?php
					for($x=0;$x<count($rows);$x++){
						$n = $x * $menu_params_columns + $z;
						if(isset($rows[$n])){
							$row = $rows[$n];
							if(!isset($row->leading_course) || !$row->leading_course){
								?>
								<div class="course_info">
									<table width="100%" cellpadding="0" cellspacing="0" border="0">
										<tr>
											<td class="contentheading">
												<a href="<?php echo sefRelToAbs('index.php?option='.$option.'&task=details_course&id='.$row->id);?>" title="<?php echo $row->course_name;?>">
													<?php echo $row->course_name;?>
												</a>
											</td>
										</tr>
									</table>
									<?php if($show_course_author){?>
									<div class="course_author">
										<span class="small"><?php echo _JLMS_HOME_AUTHOR . "&nbsp;" . $row->user_fullname;?></span>
									</div>
									<?php } ?>
									<?php
									$_JLMS_PLUGINS = & JLMSFactory::getPlugins();
									$_JLMS_PLUGINS->loadBotGroup('system');
									$plugin_args = array();
									$plugin_args[] = $row->id;
									$_JLMS_PLUGINS->trigger('onShowBlogCourseInfo', $plugin_args);
									?>
									<div class="course_sh_dsc">
										<?php echo $row->course_sh_description;?>
									</div>
								</div>
								<span class="article_separator">&nbsp;</span>
								<?php
							}
						}
					}
					?>
				</td>
				<?php
				}
				?>
			</tr>
		</table>
	<?php
	}
	?>	
	<center>
	<?php
		$link = "index.php?option=$option&amp;Itemid=$Itemid&amp;task=courses";
		echo _JLMS_PN_DISPLAY_NUM . ' ' . $pageNav->getLimitBox( $link );
		echo $pageNav->writePagesCounter();
	?>
	</center>
	<center>
		<?php
		$link = "index.php?option=$option&amp;Itemid=$Itemid&amp;task=courses";
		echo $pageNav->writePagesLinks( $link ); 
		?>
	</center>
	<?php
	JLMS_TMPL::CloseTS();

	if ($pres_icons->already || $pres_icons->mail || $pres_icons->my || $pres_icons->wl) {
	JLMS_TMPL::OpenTS('',' align="left"'); ?>
			<div class="joomlalms_info_legend">
			<?php if ($pres_icons->already) {?>
				<div style="text-align:left ">
					<img class="JLMS_png" src="<?php echo $lms_img_path;?>/buttons/btn_complete.png" align="top" width="16" height="16" border="0" alt="<?php echo _JLMS_COURSES_ALREADY; ?>" title="<?php echo _JLMS_COURSES_ALREADY;?>" />
					&nbsp;- <?php echo _JLMS_COURSES_ALREADY;?>.
				</div>
			<?php }?>
			<?php if ($pres_icons->mail) {?>
				<div style="text-align:left ">
					<img class='JLMS_png' src="<?php echo $lms_img_path;?>/dropbox/dropbox_corr.png" align="top" width='16' height='16' border='0' alt='' title='' />
					&nbsp;- <?php echo _JLMS_COURSES_ADMIN_SENT;?>
				</div>
			<?php }?>
			<?php if ($pres_icons->my) {?>
				<div style="text-align:left ">
					<img class="JLMS_png" src="<?php echo $lms_img_path;?>/toolbar/tlb_courses.png" align="top" width="16" height="16" border="0" alt="<?php echo _JLMS_COURSES_TITLE_MY_COURSES; ?>" title="<?php echo _JLMS_COURSES_TITLE_MY_COURSES; ?>" />
					&nbsp;- <?php echo _JLMS_COURSES_TITLE_MY_COURSES;?>.
				</div>
			<?php }?>
			<?php if ($pres_icons->wl) {?>
				<div style="text-align:left ">
					<img class="JLMS_png" src="<?php echo $lms_img_path;?>/buttons/btn_waiting.png" align="top" width="16" height="16" border="0" alt="<?php echo _JLMS_COURSES_TITLE_MY_COURSES; ?>" title="<?php echo _JLMS_COURSES_TITLE_MY_COURSES; ?>" />
					&nbsp;- <?php echo _JLMS_COURSES_IN_WL;?>.
				</div>
			<?php }?>
			</div>
	<?php
	JLMS_TMPL::CloseTS();
	}
	JLMS_TMPL::CloseMT(); ?>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="id" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="itemid" value="" />
		<input type="hidden" name="row_id" value="0" />
	</form>
	<?php
	}

	function editCourse( &$row, &$lists, $option, $is_inside = 0, $levels=array() ) {
		global $Itemid, $JLMS_CONFIG;
		$lms_img_path = $JLMS_CONFIG->get('live_site') . '/' . $JLMS_CONFIG->get('lms_path_to_images', 'components/com_joomla_lms/lms_images');		
		
		if ($JLMS_CONFIG->get('multicat_use', 0)) {
			$multicat = array();
			$i=0;
			foreach($lists as $key=>$item){
				if(substr($key, 0, 6) == 'level_'){
					$multicat[] = $lists['level_'.$i];
					$i++;
				}
			}
		}
		
		$is_dis_start = !($row->publish_start == 1);
		$is_dis_end = !($row->publish_end == 1);
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
	var form = document.adminForm;

	try {
		form.onsubmit();
	} catch(e) {
		//alert(e);
	}

	if (is_start_c == 1) {if (form.start_date.value == ''){jlms_getDate('start');}}
	if (is_end_c == 1) {if (form.end_date.value == ''){jlms_getDate('end');}}

	if (pressbutton == 'cancel_course') {
		form.task.value = pressbutton;
		form.submit();
	}
	if (pressbutton == 'save_course'){
		<?php
		if($JLMS_CONFIG->get('flms_integration')){
		?>
		var select_type = <?php echo isset($lists['lesson_type'])?$lists['lesson_type']:0;?>;
		var like_theory = (form.flms_like_theory)?parseInt(form.flms_like_theory.value):0;

		var valid_pf_time = js_fmod(form.flms_pf_time.value, 15);
		var valid_pm_time = js_fmod(form.flms_pm_time.value, 15);
		var valid_debriefing_time = js_fmod(form.flms_debriefing_time.value, 15);
		<?php
		}
		if ($JLMS_CONFIG->get('multicat_use', 1)) {
			$i = 0;
			foreach($multicat as $data){
				if($i < (count($multicat) - 1)){
		?>
					if (form.level_id_<?php echo $i;?>.selectedIndex == 0){
						alert('<?php echo _JLMS_COURSES_CHOOSE_GROUP;?>');
					} else
		<?php
				} else if($i==0 && $i < count($multicat)){
		?>
					if (form.level_id_<?php echo $i;?>.selectedIndex == 0){
						alert('<?php echo _JLMS_COURSES_CHOOSE_GROUP;?>');
					} else
		<?php	
				}
			$i++;
			}
		} else {
		?>
		if (form.cat_id.selectedIndex == 0){
			alert('<?php echo _JLMS_COURSES_CHOOSE_GROUP;?>');
		} else
		<?php
		}
		?>
		if (form.course_name.value == ""){
			alert( "<?php echo _JLMS_PL_ENTER_NAME;?>" );
		}
		<?php
		if($JLMS_CONFIG->get('flms_integration')){
		?>
		else if(select_type == 1){ //|| select_type == 3
			var valid_duration_time = js_fmod(form.flms_theory_duration_time.value, 15);
			if(form.flms_theory_duration_time.value == '' && form.flms_theory_duration_time.value == 0){
				alert("<?php echo _FLMS_ERROR_DURATION_TIME;?>");
				form.flms_theory_duration_time.focus();
			} else
			if((form.flms_theory_duration_time.value != '' && form.flms_theory_duration_time.value != 0) && !parseInt(form.flms_theory_duration_time.value)){
				alert('<?php echo _FLMS_ERROR_INCORRECT_FORMAT;?>');
				form.flms_theory_duration_time.focus();
			}
			else	
			if(valid_duration_time != true && (form.flms_theory_duration_time.value != '' || form.flms_theory_duration_time.value != 0)){
				alert("<?php echo _FLMS_ERROR_NO_CORRECT_TIME;?>");
				form.flms_theory_duration_time.focus();	
			}
			else{
				form.task.value = pressbutton;
				form.submit();
			}
		}
		else if(select_type == 2 && !like_theory){
			<?php
				for($i=1;$i<5;$i++){
				?>
				var valid_brefing_time_<?php echo $i;?> = js_fmod(form.flms_stu_<?php echo $i;?>_briefing_time.value, 15);
				var valid_addiditional_time_<?php echo $i;?> = js_fmod(form.flms_stu_<?php echo $i;?>_additional_time.value, 15);
				<?php
				}
				?>
				<?php
				for($i=1;$i<5;$i++){				
					if($i==1){
						?>
						if(form.flms_stu_<?php echo $i;?>_briefing_time.value == '' && form.flms_stu_<?php echo $i;?>_briefing_time.value == 0){
							alert("<?php echo _FLMS_ERROR_BRIEFING_TIME;?><?php echo $i;?>");
							form.flms_stu_<?php echo $i;?>_briefing_time.focus();
						} else
						if(!parseInt(form.flms_stu_<?php echo $i;?>_briefing_time.value) && (form.flms_stu_<?php echo $i;?>_briefing_time.value != '' && form.flms_stu_<?php echo $i;?>_briefing_time.value != 0)){
							alert('<?php echo _FLMS_ERROR_INCORRECT_FORMAT;?>');
						} else
						if((form.flms_stu_<?php echo $i;?>_briefing_time.value != '' && form.flms_stu_<?php echo $i;?>_briefing_time.value != 0) && valid_brefing_time_<?php echo $i;?> != true){
							alert("<?php echo _FLMS_ERROR_NO_CORRECT_TIME;?>");
							form.flms_stu_<?php echo $i;?>_briefing_time.focus();
						} 
						
						<?php
					} else {
						?>
						else
						if(form.flms_stu_<?php echo $i;?>_briefing_time.value == '' && form.flms_stu_<?php echo $i;?>_briefing_time.value == 0){
							alert("<?php echo _FLMS_ERROR_BRIEFING_TIME;?><?php echo $i;?>");
							form.flms_stu_<?php echo $i;?>_briefing_time.focus();
						}
						else
						if(!parseInt(form.flms_stu_<?php echo $i;?>_briefing_time.value) && (form.flms_stu_<?php echo $i;?>_briefing_time.value != '' && form.flms_stu_<?php echo $i;?>_briefing_time.value != 0)){
							alert('<?php echo _FLMS_ERROR_INCORRECT_FORMAT;?>');
						}
						else if((form.flms_stu_<?php echo $i;?>_briefing_time.value != '' && form.flms_stu_<?php echo $i;?>_briefing_time.value != 0) && valid_brefing_time_<?php echo $i;?> != true){
							alert("<?php echo _FLMS_ERROR_NO_CORRECT_TIME;?>");
							form.flms_stu_<?php echo $i;?>_briefing_time.focus();
						}
						<?php
					}
				}
				?>
				else
				<?php
				for($i=1;$i<5;$i++){				
					if($i==1){
						?>
						if(!parseInt(form.flms_stu_<?php echo $i;?>_additional_time.value) && (form.flms_stu_<?php echo $i;?>_additional_time.value != '' && form.flms_stu_<?php echo $i;?>_additional_time.value != 0)){
							alert('<?php echo _FLMS_ERROR_INCORRECT_FORMAT;?>');
						} else
						if(form.flms_stu_<?php echo $i;?>_additional_time.value != '' && valid_addiditional_time_<?php echo $i;?> != true){
							alert("<?php echo _FLMS_ERROR_NO_CORRECT_TIME;?>");	
							form.flms_stu_<?php echo $i;?>_additional_time.focus();
						}
						<?php
					} else {
						?>
						else
						if(!parseInt(form.flms_stu_<?php echo $i;?>_additional_time.value) && (form.flms_stu_<?php echo $i;?>_additional_time.value != '' && form.flms_stu_<?php echo $i;?>_additional_time.value != 0)){
							alert('<?php echo _FLMS_ERROR_INCORRECT_FORMAT;?>');
						}
						else if((form.flms_stu_<?php echo $i;?>_additional_time.value != '' && form.flms_stu_<?php echo $i;?>_additional_time.value != 0) && valid_addiditional_time_<?php echo $i;?> != true){
							alert("<?php echo _FLMS_ERROR_NO_CORRECT_TIME;?>");	
							form.flms_stu_<?php echo $i;?>_additional_time.focus();
						}
						<?php
					}
				}
			?>
			else if(select_type == 3){
				form.task.value = pressbutton;
				form.submit();
			}
			else if(form.flms_pf_time.value == ''){
				alert("<?php echo _FLMS_ERROR_PF_TIME;?>");
				form.flms_pf_time.focus();
			}
			else if(valid_pf_time != true){
				alert("<?php echo _FLMS_ERROR_NO_CORRECT_TIME;?>");
				form.flms_pf_time.focus();		
			}
			else if(form.flms_pm_time.value == ''){
				alert("<?php echo _FLMS_ERROR_PM_TIME;?>");
				form.flms_pm_time.focus();
			}
//			else if(!parseInt(form.flms_pm_time.value) && form.flms_pm_time.value != ''){
//				alert('<?php echo _FLMS_ERROR_INCORRECT_FORMAT;?>');
//				form.flms_pm_time.focus();
//			}
			else if(valid_pm_time != true){
				alert("<?php echo _FLMS_ERROR_NO_CORRECT_TIME;?>");
				form.flms_pm_time.focus();		
			}
			else if(form.flms_debriefing_time.value == ''){
				alert("<?php echo _FLMS_ERROR_DEBRIEFING_TIME;?>");
				form.flms_debriefing_time.focus();
			}
//			else if(!parseInt(form.flms_debriefing_time.value)){
//				alert('<?php echo _FLMS_ERROR_INCORRECT_FORMAT;?>');
//				form.flms_debriefing_time.focus();
//			}
			else if(valid_debriefing_time != true){
				alert("<?php echo _FLMS_ERROR_NO_CORRECT_TIME;?>");
				form.flms_debriefing_time.focus();		
			}	
			else if(form.flms_operation.value == 0){
				alert("<?php echo _FLMS_ERROR_SELECT_OPERATION;?>");
				form.flms_operation.focus();	
			}
			
			else{
				form.task.value = pressbutton;
				form.submit();
			}
		}
		else if(select_type == 2 && like_theory){
			if(form.flms_theory_duration_time.value == '' && form.flms_theory_duration_time.value == 0){
				alert("<?php echo _FLMS_ERROR_DURATION_TIME;?>");
				form.flms_theory_duration_time.focus();
			} else {
				form.task.value = pressbutton;
				form.submit();
			}
		}
		<?php
		}
		?>
		else{
			form.task.value = pressbutton;
			form.submit();
		}
	}
	else {
		form.task.value = pressbutton;
		form.submit();
	}
}

<?php
if($JLMS_CONFIG->get('flms_integration')){
?>
function js_fmod(x, y){
	var a = x/y;
	var b = Math.floor(x/y);
	var c = a - b;
	var result = false;
	if(c == 0){
		result = true;
	}
	return result;
}
<?php
}
?>

var is_start_c = <?php echo ($row->publish_start == 1)?'1':'0'; ?>; var is_end_c = <?php echo ($row->publish_end == 1)?'1':'0'; ?>;
function jlms_Change_start() {
	var form=document.adminForm;
	if (is_start_c == 1) {
		is_start_c = 0;
		form.startday.disabled = true;
		form.startmonth.disabled = true;
		form.startyear.disabled = true;
	} else {
		is_start_c = 1;
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

function FLMS_load_cat(e){
	var form = document.adminForm;
	
	form.task.value = 'add_course';
	form.submit();
}

var old_filters = new Array();
function read_filter(){
	var form = document.adminForm;
	var count_levels = '<?php echo count($levels);?>';
	for(var i=0;i<parseInt(count_levels);i++){
		if(form['level_id_'+i] != null){
			old_filters[i] = form['level_id_'+i].value;
		}
	}
}
function write_filter(){
	var form = document.adminForm;
	var count_levels = '<?php echo count($levels);?>';
	var j;
	for(var i=0;i<parseInt(count_levels);i++){
		if(form['level_id_'+i+''] != null && form['level_id_'+i+''].value != old_filters[i]){
			j = i;
		}
		if(i > j){
			if(form['level_id_'+i] != null){
				form['level_id_'+i].value = 0;	
			}
		}
	}
}

//--><!]]>
</script>
<?php
		JLMS_TMPL::OpenMT();

		$params = array();
		$params['show_menu'] = false;
		$toolbar = array();
		$toolbar[] = array('btn_type' => 'save', 'btn_js' => "javascript:submitbutton('save_course');");
		$toolbar[] = array('btn_type' => 'cancel', 'btn_js' => "javascript:submitbutton('cancel_course');");
		$params['toolbar'] = $toolbar;
		JLMS_TMPL::ShowHeader('course', $row->id ? _JLMS_COURSES_TITLE_EDIT_COURSE : _JLMS_COURSES_TITLE_NEW_COURSE, $params);

		JLMS_TMPL::OpenTS();
		
		if ($JLMS_CONFIG->get('sec_cat_use', 0) && $JLMS_CONFIG->get('sec_cat_show', 0)) {
			$rowsapn = 6;
		} else {
			$rowsapn = 5;	
		}
?>
		<script language="javascript" type="text/javascript">
			window.addEvent('domready', function(){
				
			});
		</script>
		<form action="<?php echo sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid");?>" method="post" name="adminForm" onsubmit="setgood();">
			<table width="100%" cellpadding="0" cellspacing="0" border="0" id="jlms_course_properties">
				<tr>
					<?php
					if ($JLMS_CONFIG->get('multicat_use', 0)) {
					?>
					<td id="multicat_title" width="20%" valign="top" style="vertical-align: top;">
						<table class="jlms_course_properties_cats" cellpadding="0" cellspacing="0" border="0" width="100%">
						<?php	
							for($i=0;$i<count($multicat);$i++){
								?>
								<tr>
									<td style="line-height: 22px;">
									<?php 
										echo $levels[$i]->cat_name;
									?>
									</td>
								</tr>
								<?php
							}
							?>
						</table>
					</td>
					<td id="multicat" valign="top" style="vertical-align: top;">
						<table class="jlms_course_properties_cats" cellpadding="0" cellspacing="0" border="0" width="100%">
						<?php	
							for($i=0;$i<count($multicat);$i++){
								?>
								<tr>
									<td>
									<?php
										echo $multicat[$i];
									?>
									<input type="hidden" name="multicat_id" value="" />
									</td>
								</tr>
								<?php
							}
							?>
						</table>
					</td>
					<?php
					} else {
					?>
					<td align="left" width="20%" valign="middle" style="vertical-align: middle;"><br /><?php echo _JLMS_COURSES_COURSES_GROUPS;?></td>
					<td>
						<br />
						<?php echo $lists['cat_id'];?>
					</td>
					<?php
					}
					if($JLMS_CONFIG->get('flms_integration')){
					?>
					<td rowspan="<?php echo $rowsapn;?>">
						<?php
						FLMS_params_lesson($row->id, $lists['lesson_type']);
						?>
					</td>
					<?php
					} else {
						echo '&nbsp;';
					}
					?>
				</tr>
				<?php
				if ($JLMS_CONFIG->get('sec_cat_use', 0) && $JLMS_CONFIG->get('sec_cat_show', 0)) {
					?>
				<tr>
					<td align="left" valign="middle" style="vertical-aligh:middle "><br /><?php echo _JLMS_COURSES_SEC_CAT;?></td>
					<td><br /><?php echo $lists['sec_cat_id'];?></td>
				</tr>
					<?php
				}
				?>
				<tr>
					<td align="left" width="20%" valign="middle" style="vertical-align:middle "><br /><?php echo _JLMS_ENTER_NAME;?></td>
					<td><br /><input class="inputbox" type="text" name="course_name" style="width:266px;" maxlength="100" value="<?php echo (isset($_REQUEST['course_name']))? str_replace('"','&quot;', $_REQUEST['course_name']) : str_replace('"','&quot;', $row->course_name); ?>" /></td>
				</tr>
				
				<tr>
					<td valign="middle"><br /><?php echo _JLMS_COURSES_START_DATE;?></td>
					<td colspan="2" valign="middle" style="vertical-align:middle "><br />
						<table class="jlms_date_outer" cellpadding="0" cellspacing="0" border="0"><tr><td valign="middle"><input type="checkbox" value="1" name="publish_start" onclick="jlms_Change_start()"<?php echo $row->publish_start?' checked="checked"':'';?> /></td>
						<td valign="middle" style="vertical-align:middle ">
						<?php
						$s_date = ($is_dis_start)?date('Y-m-d'):$row->start_date;
						echo JLMS_HTML::_('calendar.calendar',$s_date,'start','start');
						?>
						</td></tr></table>
					</td>
				</tr>
				<tr>
					<td><br /><?php echo _JLMS_COURSES_ENDING_DATE;?></td>
					<td colspan="2" valign="middle" style="vertical-align:middle "><br />
						<table class="jlms_date_outer" cellpadding="0" cellspacing="0" border="0"><tr><td valign="middle"><input type="checkbox" value="1" name="publish_end" onclick="jlms_Change_end()"<?php echo $row->publish_end?' checked="checked"':'';?> /></td>
						<td valign="middle" style="vertical-align:middle ">
						<?php 
						$e_date = ($is_dis_end)?date('Y-m-d'):$row->end_date;
						echo JLMS_HTML::_('calendar.calendar',$e_date,'end','end');
						?>
						</td></tr></table>
					</td>
				</tr>
				<tr>
					<td align="left" width="20%" valign="middle" style="vertical-align:middle "><br /><?php echo _JLMS_COURSES_PUBLISHED;?></td>
					<td colspan="2"><br /><?php echo $lists['published'];?></td>
				</tr>
				
				<tr>
					<td colspan="3" align="left" valign="top"><br /><?php echo _JLMS_DESCRIPTION;?></td>
				</tr>
				<tr>
					<td colspan="3" align="left">
					<?php JLMS_editorArea( 'editor1', $row->course_description, 'course_description', '100%;', '250', '40', '20' ) ; ?>
					</td>
				</tr>
				<tr>
					<td align="left" width="20%" valign="top" style="vertical-align:top "><br /><?php echo _JLMS_SH_DESCRIPTION;?></td>
					<td colspan="2"><br /><textarea class="inputbox" name="course_sh_description" cols="50" rows="3"><?php echo $row->course_sh_description; ?></textarea></td>
				</tr>
				<?php if($JLMS_CONFIG->get('show_course_meta_property', 1) == 1){ ?>
				<tr>
					<td align="left" width="20%" valign="top" style="vertical-align:top "><br /><?php echo _JLMS_COURSES_METADATA;?></td>
					<td colspan="2"><br /><textarea class="inputbox" name="metadesc" cols="50" rows="3"><?php echo $row->metadesc; ?></textarea></td>
				</tr>
				<tr>
					<td align="left" width="15%" valign="top" style="vertical-align:top "><br /><?php echo _JLMS_COURSES_METAKEYS;?></td>
					<td colspan="2"><br /><textarea class="inputbox" name="metakeys" cols="50" rows="3"><?php echo $row->metakeys; ?></textarea></td>
				</tr>
				<?php } ?>
				<?php if($JLMS_CONFIG->get('show_course_access_property', 1) == 1){ ?>
				<tr>
					<td align="left" width="20%" valign="top" style="vertical-align:top "><br /><?php echo _JLMS_COURSES_ACCESS_LEVEL;?></td>
					<td colspan="2"><br /><?php echo $lists['gid'];?></td>
				</tr>
				<?php } ?>
				<tr>
					<td align="left" width="20%" valign="middle" style="vertical-align:middle "><br /><?php echo _JLMS_COURSES_COURSE_LANG;?></td>
					<td colspan="2"><br /><?php echo $lists['language'];?></td>
				</tr>
				<tr>
					<td align="left" width="20%" valign="middle" style="vertical-align:middle "><br /><?php echo _JLMS_COURSES_ADD_CHAT;?></td>
					<td colspan="2"><br /><?php echo $lists['add_chat'];?></td>
				</tr>
				<tr>
					<td align="left" width="20%" valign="middle" style="vertical-align:middle "><br /><?php echo _JLMS_COURSES_ADD_HW;?></td>
					<td colspan="2"><br /><?php echo $lists['add_hw'];?></td>
				</tr>
				<tr>
					<td align="left" width="20%" valign="middle" style="vertical-align:middle "><br /><?php echo _JLMS_COURSES_ADD_ATTEND;?></td>
					<td colspan="2"><br /><?php echo $lists['add_attend'];?></td>
				</tr>
				<?php if($JLMS_CONFIG->get('plugin_forum') == 1){ ?>
				<tr>
					<td align="left" width="20%" valign="middle" style="vertical-align:middle "><br /><?php echo _JLMS_COURSES_ADD_FORUM;?></td>
					<td colspan="2"><br /><?php echo $lists['add_forum'];?></td>
				</tr>
				<?php }?>
				<tr>
					<td align="left" width="20%" valign="middle" style="vertical-align:middle "><br /><?php echo _JLMS_COURSES_SELF_REG;?></td>
					<td colspan="2"><br /><?php echo $lists['self_reg'];?></td>
				</tr>
				<?php if($JLMS_CONFIG->get('show_course_fee_property', 1) == 1){ ?>
				<tr>
					<td align="left" width="20%" valign="middle" style="vertical-align:middle "><br /><?php echo _JLMS_COURSES_FEE_TYPE;?></td>
					<td colspan="2"><br /><input type="radio" name="paid" id="free_type"  value="0"<?php echo $row->paid ? '' : ' checked="checked"'; ?> /><label for="free_type"><?php echo _JLMS_COURSES_FREE;?></label>
					<input type="radio" name="paid" id="paid_type" value="1"<?php echo $row->paid ? ' checked="checked"' : ''; ?> /><label for="paid_type"><?php echo _JLMS_COURSES_PAID;?></label>
					</td>
				</tr>
				<?php } ?>
				
				<?php
				//Course Properties Event//
				if(isset($lists['plugin_return']) && count($lists['plugin_return'])){
					$fields = $lists['plugin_return'];
					foreach($fields as $field){
						?>
						<tr>
							<td align="left" width="20%" valign="middle" style="vertical-align:middle ">
								<br />
								<?php echo $field->name;?>:
							</td>
							<td colspan="2">
								<br />
								<?php echo $field->control;?>
							</td>
						</tr>
						<?php
					}
				}
				//Course Properties Event//
				?>
				
			</table>
			<input type="hidden" name="option" value="<?php echo $option; ?>" />
			<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
			<input type="hidden" name="task" value="<?php echo $row->id?'add_course':'edit_course';?>" />
			<input type="hidden" name="is_inside" value="<?php echo $is_inside;?>" />
			<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
		</form>
<?php
		JLMS_TMPL::CloseTS();
		JLMS_TMPL::CloseMT();
	}

	function showCourseSettings( &$teachers_menus,	&$students_menus, $option, $id, &$fp_lists, $lists ) {
		global $Itemid, $JLMS_CONFIG, $JLMS_SESSION;

		$lms_img_path = $JLMS_CONFIG->get('live_site') . '/' . $JLMS_CONFIG->get('lms_path_to_images', 'components/com_joomla_lms/lms_images');

		// 01.03.2007 iz peremennoy $JLMS_LANGUAGE zagrugayutsa messagi menu dlya otobrageniya
		//TODO: (low) do not use JLMS_LANG variable here
		global $JLMS_LANGUAGE;
		?>
<script language="javascript" type="text/javascript">
<!--//--><![CDATA[//><!--
function submitbutton(pressbutton) {
	var form = document.adminForm;

	if (pressbutton == 'cancel_course') {
		form.task.value = pressbutton;
		form.submit();
	}
	if (pressbutton == 'settings_save'){
		form.task.value = pressbutton;
		form.submit();
	}
}
function jlms_disable_form_element(){
	var form = document.adminForm;
	elem1 = getObj("params[lpath_redirect]0").checked;
	elem2 = getObj("params[lpath_redirect]1").checked;
	elem3_1 = getObj("params[homework_view]0");
	elem3_2 = getObj("params[homework_view]1");
	elem4_1 = getObj("params[agenda_view]0");
	elem4_2 = getObj("params[agenda_view]1");
	elem5_1 = getObj("params[dropbox_view]0");
	elem5_2 = getObj("params[dropbox_view]1");
	elem6_1 = getObj("params[mailbox_view]0");
	elem6_2 = getObj("params[mailbox_view]1");
	elem7_1 = getObj("params[certificates_view]0");
	elem7_2 = getObj("params[certificates_view]1");
	elem8_1 = getObj("params[latest_forum_posts_view]0");
	elem8_2 = getObj("params[latest_forum_posts_view]1");
	elem_select = form["params[learn_path]"];

	if (elem1 == true){
		elem_select.disabled = true;

		<?php if($JLMS_CONFIG->get('course_homework')) {?>

		elem3_1.disabled = false;
		elem3_2.disabled = false;

		<?php } ?>

		elem4_1.disabled = false;
		elem4_2.disabled = false;
		elem5_1.disabled = false;
		elem5_2.disabled = false;
		elem6_1.disabled = false;
		elem6_2.disabled = false;
		elem7_1.disabled = false;
		elem7_2.disabled = false;
		elem8_1.disabled = false;
		elem8_2.disabled = false;
	}else{
		elem_select.disabled = false;

		<?php if($JLMS_CONFIG->get('course_homework')) {?>

		elem3_1.disabled = true;
		elem3_2.disabled = true;

		<?php } ?>

		elem4_1.disabled = true;
		elem4_2.disabled = true;
		elem5_1.disabled = true;
		elem5_2.disabled = true;
		elem6_1.disabled = true;
		elem6_2.disabled = true;
		elem7_1.disabled = true;
		elem7_2.disabled = true;
		elem8_1.disabled = true;
		elem8_2.disabled = true;
	}
}
//--><!]]>
</script>
<?php
		JLMS_TMPL::OpenMT();

		$params = array();
		$params['show_menu'] = false;
		$toolbar = array();
		$toolbar[] = array('btn_type' => 'save', 'btn_js' => "javascript:submitbutton('settings_save');");
		$toolbar[] = array('btn_type' => 'back', 'btn_js' => "javascript:submitbutton('cancel_course');");
		$params['toolbar'] = $toolbar;
		JLMS_TMPL::ShowHeader('course', _JLMS_COURSES_TITLE_SETTINGS_COURSE.' [ '.$JLMS_CONFIG->get('course_name').' ] ', $params);

		JLMS_TMPL::OpenTS();
?>
		<form action="<?php echo sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid");?>" method="post" name="adminForm">
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td align="left" colspan="2" class="sectiontableheader" style="height:30px">
					<?php echo _JLMS_COURSE_TRACK_SET_TITLE;?>
				</td>
			</tr>
			<tr class="sectiontableentry1">
				<td width="50%">
					<strong><?php echo _JLMS_COURSE_TRACK_TYPE_STR;?></strong>
				</td>
				<td align="left">
				<?php echo $fp_lists['track_type'];?>
				</td>
			</tr>
			<tr><td colspan="2"><br /></td></tr>
			<?php if ($JLMS_CONFIG->get('enable_conference_booking', false)) { ?>
			<tr>
				<td align="left" colspan="2" class="sectiontableheader" style="height:30px">
					<?php echo 'Booking';?>
				</td>
			</tr>
			<tr class="sectiontableentry1">
				<td width="50%">
					<strong><?php echo 'Enable conference booking';?></strong>
				</td>
				<td align="left">
				<?php echo $fp_lists['conf_book'];?>
				</td>
			</tr>
			<tr><td colspan="2"><br /></td></tr>
			<?php } ?>
			<tr>
				<td align="left" colspan="2" class="sectiontableheader" style="height:30px">
					<?php echo _JLMS_COURSEHOME_TITLE;?>
				</td>
			</tr>
			<tr class="sectiontableentry1">
				<td width="50%">
					<strong><?php echo _JLMS_COURSEHOME_LP_REDIRECT;?>	</strong>
				</td>
				<td align="left">
				<?php echo $fp_lists['lpath_redirect'];?>
				</td>
			</tr>
			<tr class="sectiontableentry2">
				<td width="50%">

				</td>
				<td align="left">
				<?php echo $fp_lists['learn_path'];?>
				</td>
			</tr>
			<tr class="sectiontableentry1">
				<td>
					<strong><?php echo _JLMS_COURSES_SHOW_DESC_FOR_LEARNERS;?></strong>
				</td>
				<td>
				<?php echo $fp_lists['show_description'];?>
				</td>
			</tr>
			<tr class="sectiontableentry2">
				<td>
					<strong><?php echo _JLMS_COURSEHOME_HW_MODULE_VIEW;?></strong>
					<?php if(!$JLMS_CONFIG->get('course_homework')) { echo "*"; } ?>
				</td>
				<td>
				<?php echo $fp_lists['homework_view'];?>
				</td>
			</tr>
			<tr class="sectiontableentry1">
				<td>
					<strong><?php echo _JLMS_COURSEHOME_AG_MODULE_VIEW;?></strong>
				</td>
				<td>
				<?php echo $fp_lists['agenda_view'];?>
				</td>
			</tr>
			<tr class="sectiontableentry2">
				<td>
					<strong><?php echo _JLMS_COURSEHOME_DP_MODULE_VIEW;?></strong>
				</td>
				<td>
				<?php echo $fp_lists['dropbox_view'];?>
				</td>
			</tr>
			<tr class="sectiontableentry1">
				<td>
					<strong><?php echo _JLMS_COURSEHOME_MB_MODULE_VIEW;?></strong>
				</td>
				<td>
				<?php echo $fp_lists['mailbox_view'];?>
				</td>
			</tr>
			<tr class="sectiontableentry2">
				<td>
					<strong><?php echo _JLMS_COURSEHOME_CRT_MODULE_VIEW;?></strong>
				</td>
				<td>
				<?php echo $fp_lists['certificates_view'];?>
				</td>
			</tr>
			<tr class="sectiontableentry1">
				<td>
					<strong><?php echo _JLMS_COURSEHOME_LFP_MODULE_VIEW;?></strong>
				</td>
				<td>
				<?php echo $fp_lists['latest_forum_posts_view'];?>
				</td>
			</tr>
			<?php
			if($JLMS_CONFIG->get('flms_integration', 0)){
			?>
			<tr class="sectiontableentry2">
				<td>
					<strong><?php
					define('_JLMS_COURSEHOME_SHOW_IN_REPORTS', "Show in reports");
					echo _JLMS_COURSEHOME_SHOW_IN_REPORTS;?></strong>
				</td>
				<td>
				<?php echo $fp_lists['show_in_report'];?>
				</td>
			</tr>
			<?php
			}
			?>
			<tr>
				<td align="left" colspan="2" class="sectiontableheader" style="height:30px">
					<?php echo _JLMS_COURSES_OTHER_SETTINGS;?>
				</td>
			</tr>
			<?php
			//Course Properties Event//
			if(isset($lists['plugin_return']) && count($lists['plugin_return'])){
				$fields = $lists['plugin_return'];
				$k=1;
				foreach($fields as $field){
					?>
					<tr class="sectiontableentry<?php echo $k;?>">
						<td>
							<strong><?php echo $field->name;?></strong>
						</td>
						<td>
							<?php echo $field->control;?>
						</td>
					</tr>
					<?php
					$k=3-$k;
				}
			}
			//Course Properties Event//
			?>
			
			
			<tr class="sectiontableentry1" <?php echo (!$JLMS_CONFIG->get('max_attendees_change', 0)) ? 'style="display:none"' : '';?>>
				<td width="50%"><?php echo _JLMS_COURSES_MAX_ATTENDEES;?></td>
				<td><?php echo $fp_lists['max_attendees']?>				</td>
			</tr>
			<tr>
				<td width="50%" height="100%" valign="top" align="right" style="padding:2px;"><br />
					<table width="100%" cellpadding="0" cellspacing="0" border="0" class="contentpane">
						<tr>
							<td class="contentheading" colspan="4" align="left" style="height:30px"><?php echo _JLMS_COURSES_MENU_TEACHERS;?></td>
						</tr>
						<tr>
							<td width="1%" align="center" class="sectiontableheader">
							#
							</td>
							<td width="5%" align="center" class="sectiontableheader">
							</td>
							<td align="left" class="sectiontableheader">
								<?php echo _JLMS_COURSES_MENU_ITEM;?>
							</td>
							<td align="left" class="sectiontableheader">
								<?php echo _JLMS_COURSES_MENU_ENABLED;?>
							</td>
						</tr>
						<?php
						$i = 1;$k = 1;

						$add_tracking	= $JLMS_CONFIG->get('tracking_enable');
						$add_quiz		= $JLMS_CONFIG->get('plugin_quiz');
						$add_chat		= ( $JLMS_CONFIG->get('chat_enable') && $JLMS_CONFIG->get('course_chat') );
						$add_forum 		= ( $JLMS_CONFIG->get('plugin_forum') && $JLMS_CONFIG->get('course_forum') );
						$add_conference	= $JLMS_CONFIG->get('conference_enable');
						$add_hw			= $JLMS_CONFIG->get('course_homework');
						$add_attend		= $JLMS_CONFIG->get('course_attendance');

						foreach ($teachers_menus as $menu){
							$disabled = 1;
							//global
							$glob_dis = '';
							if ($menu->disabled){
								$disabled = 0;
							}
							if ($menu->lang_var == '_JLMS_TOOLBAR_QUIZZES' && !$add_quiz){
								$glob_dis = 'disabled="disabled"';
								$disabled = 0;
							}elseif($menu->lang_var == '_JLMS_TOOLBAR_HOMEWORK' && !$add_hw){
								$glob_dis = 'disabled="disabled"';
								$disabled = 0;
							}elseif($menu->lang_var == '_JLMS_TOOLBAR_ATTEND' && !$add_attend){
								$glob_dis = 'disabled="disabled"';
								$disabled = 0;
							}elseif($menu->lang_var == '_JLMS_TOOLBAR_FORUM' && !$add_forum ){
								$glob_dis = 'disabled="disabled"';
								$disabled = 0;
							}elseif($menu->lang_var == '_JLMS_TOOLBAR_CHAT' && !$add_chat ){
								$glob_dis = 'disabled="disabled"';
								$disabled = 0;
							}elseif($menu->lang_var == '_JLMS_TOOLBAR_CONF' && !$add_conference ){
								$glob_dis = 'disabled="disabled"';
								$disabled = 0;
							}elseif($menu->lang_var == '_JLMS_TOOLBAR_TRACK' && !$add_tracking ){
								$glob_dis = 'disabled="disabled"';
								$disabled = 0;
							}
							if (!$menu->published){
								$glob_dis = 'disabled="disabled"';
								$disabled = 0;
							}
							$enabled = mosHTML::yesnoRadioList( 'enabled_'.$menu->user_access."_".$menu->id, "class='inputbox' ".$glob_dis, $disabled, _JLMS_COURSES_MENU_SHOW, _JLMS_COURSES_MENU_HIDE )
						?>
						<tr class="<?php echo "sectiontableentry$k"; ?>">
							<td align="center"><?php echo $i;?></td>
							<td style="height:25px" align="left">
							<?php
							$menu_lang_var = ($menu->lang_var && isset($JLMS_LANGUAGE[$menu->lang_var])) ? $JLMS_LANGUAGE[$menu->lang_var] : '';
							if ($menu->lang_var && defined($menu->lang_var)) {
								if (constant($menu->lang_var)) {
									$menu_lang_var = constant($menu->lang_var);
								} 
							}
							if ($menu->is_separator){
								echo "---";
							}else{
								echo "<img src='".$lms_img_path."/toolbar/".$menu->image."' alt='".$menu_lang_var."' width='16' height = '16' />";
							}
							?>
							</td>
							<td align="left"  >
							<?php
							if ($menu->is_separator == 1){
								echo _JLMS_SEPARATOR;
							}else{
								echo $menu_lang_var;
								if ($glob_dis){
									echo " <sup style='font-size:10px;'>*</sup> ";
								}
							}
							$i++;
							?>
							</td>
							<td align="left" nowrap='nowrap'>
								<?php echo $enabled;?>
							</td>
						</tr>
						<?php
						$k = 3- $k;
						}?>
					</table>
				</td>
				<td width="50%" height="100%" valign="top" style="padding:2px;"><br />
					<table width="100%" cellpadding="0" cellspacing="0" border="0" class="contentpane">
						<tr>
							<td class="contentheading" colspan="4" style="height:30px"><?php echo _JLMS_COURSES_MENU_STUDENTS;?></td>
						</tr>
						<tr>
							<td width="1%" class="sectiontableheader" align="center" style="text-align:center ">
							#
							</td>
							<td width="5%" class="sectiontableheader" align="center" >
							</td>
							<td align="left" class="sectiontableheader">
								<?php echo _JLMS_COURSES_MENU_ITEM;?>
							</td>
							<td align="left" class="sectiontableheader">
								<?php echo _JLMS_COURSES_MENU_ENABLED;?>
							</td>
						</tr>
						<?php
						$very_glob_dis = 0;
						$i = 1;$k = 1;
						foreach ($students_menus as $menu){
							$disabled = 1;
							//global
							$glob_dis = '';
							if ($menu->disabled){
								$disabled = 0;
							}
							if ($menu->lang_var == '_JLMS_TOOLBAR_QUIZZES' && !$add_quiz){
								$glob_dis = 'disabled="disabled"';
								$disabled = 0;
							}elseif($menu->lang_var == '_JLMS_TOOLBAR_HOMEWORK' && !$add_hw){
								$glob_dis = 'disabled="disabled"';
								$disabled = 0;
							}elseif($menu->lang_var == '_JLMS_TOOLBAR_ATTEND' && !$add_attend){
								$glob_dis = 'disabled="disabled"';
								$disabled = 0;
							}elseif($menu->lang_var == '_JLMS_TOOLBAR_FORUM' && !$add_forum ){
								$glob_dis = 'disabled="disabled"';
								$disabled = 0;
							}elseif($menu->lang_var == '_JLMS_TOOLBAR_CHAT' && !$add_chat ){
								$glob_dis = 'disabled="disabled"';
								$disabled = 0;
							}elseif($menu->lang_var == '_JLMS_TOOLBAR_CONF' && !$add_conference ){
								$glob_dis = 'disabled="disabled"';
								$disabled = 0;
							}elseif($menu->lang_var == '_JLMS_TOOLBAR_TRACK' && !$add_tracking ){
								$glob_dis = 'disabled="disabled"';
								$disabled = 0;
							}
							if (!$menu->published){
								$glob_dis = 'disabled="disabled"';
								$disabled = 0;
							}

							$enabled = mosHTML::yesnoRadioList( 'enabled_'.$menu->user_access."_".$menu->id, "class='inputbox' ".$glob_dis, $disabled, _JLMS_COURSES_MENU_SHOW, _JLMS_COURSES_MENU_HIDE )
						?>
						<tr class="<?php echo "sectiontableentry$k"; ?>">
							<td align="center"><?php echo $i;?></td>
							<td style='height:25px' align="left" >
							<?php
							$menu_lang_var = ($menu->lang_var && isset($JLMS_LANGUAGE[$menu->lang_var])) ? $JLMS_LANGUAGE[$menu->lang_var] : '';
							if ($menu->lang_var && defined($menu->lang_var)) {
								if (constant($menu->lang_var)) {
									$menu_lang_var = constant($menu->lang_var);
								} 
							}
							if ($menu->is_separator){
								echo "---";
							}else{
								echo "<img src='".$lms_img_path."/toolbar/".$menu->image."' alt='".$menu_lang_var."' width='16' height = '16' />";
							}
							echo "</td><td align='left'>";
							if ($menu->is_separator == 1){
								echo "Separator";
							}else{
								echo $menu_lang_var;
								if ($glob_dis){
									$very_glob_dis = 1;
									echo " <sup style='font-size:10px;'>*</sup> ";
								}
							}
							$i++;
							echo "</td>";
							echo "<td align='left' nowrap='nowrap'>".$enabled."</td></tr>";
							$k = 3- $k;
						 }?>
					</table>
				</td>
			</tr>
		<?php if ($very_glob_dis) { ?>
			<tr>
				<td align="left" colspan="2"><hr />* <?php echo _JLMS_COURSES_MENU_INFO;?></td>
			</tr>
		<?php } ?>
		<?php if($JLMS_CONFIG->get('show_course_spec_property', 1) == 1){ ?>
			<tr>
				<td colspan="2"><br /></td>
			</tr>
			<tr>
				<td colspan="2" class="sectiontableheader">
					<?php echo _JLMS_COURSES_REG_QUESTIONS;?>
				</td>
			</tr>
<?php
global $JLMS_DB;
$rowc = new mos_Joomla_LMS_Course( $JLMS_DB );
$rowc->load( $id );
$lists['spec_reg'] = mosHTML::yesnoRadioList( 'spec_reg', 'class="inputbox" onchange="jlms_change_spec_reg_quest(this);" ', $rowc->spec_reg);
?>
			<tr class="sectiontableentry1">
				<td><?php  echo _JLMS_COURSES_SPEC_REG;?></td>
				<td><?php echo $lists['spec_reg'];?></td>
			</tr>
			<tr>
				<td colspan="2"><div id="SpecRegOptions" style="width:100%<?php echo $rowc->spec_reg ? '' : ';visibility:hidden;display:none;';?>">
<script language="javascript" type="text/javascript">
<!--
function jlms_change_spec_reg_quest(elem) {
	ttt = getObj('SpecRegOptions');
	if (elem.checked) {
		if (elem.value == 1) {
			ttt.style.visibility = 'visible';
			ttt.style.display = '';
		} else {
			ttt.style.visibility = 'hidden';
			ttt.style.display = 'none';
		}
	} else {
		if (elem.value == 0) {
			ttt.style.visibility = 'hidden';
			ttt.style.display = 'none';
		} else {
			ttt.style.visibility = 'visible';
			ttt.style.display = '';
		}
	}
}

function ReAnalize_tbl_Rows( start_index, tbl_id ) {
	start_index = 1;
	var tbl_elem = getObj(tbl_id);
	if (tbl_elem.rows[start_index]) {
		var count = start_index; var row_k = 2 - start_index%2;//0;
		for (var i=start_index; i<tbl_elem.rows.length; i++) {
			tbl_elem.rows[i].cells[0].innerHTML = count;

			if (i > 1) {
				tbl_elem.rows[i].cells[6].innerHTML = '<a href="javascript: void(0);" onclick="javascript:Up_tbl_row(this); return false;" title="<?php echo _JLMS_MOVEUP;?>"><img src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_uparrow.png" width="16" height="16" border="0" alt="<?php echo _JLMS_MOVEUP;?>" /></a>';
			} else { tbl_elem.rows[i].cells[6].innerHTML = '&nbsp;'; }
			if (i < (tbl_elem.rows.length - 1)) {
				tbl_elem.rows[i].cells[7].innerHTML = '<a href="javascript: void(0);" onclick="javascript:Down_tbl_row(this); return false;" title="<?php echo _JLMS_MOVEDOWN;?>"><img src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_downarrow.png" width="16" height="16" border="0" alt="<?php echo _JLMS_MOVEDOWN;?>" /></a>';;
			} else { tbl_elem.rows[i].cells[7].innerHTML = '&nbsp;'; }
			tbl_elem.rows[i].className = 'sectiontableentry'+row_k;
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
		element.parentNode.parentNode.parentNode.deleteRow(element.parentNode.parentNode.sectionRowIndex);

		var cell3 = document.createElement("td");
		var cell4 = document.createElement("td");
		var cell5 = document.createElement("td");
		var cell6 = document.createElement("td");
		cell3.innerHTML = '<a href="javascript: void(0);" onclick="javascript:Delete_tbl_row(this); return false;" title="Delete"><img src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_cancel.png" width="16" height="16" border="0" alt="Delete" /></a>';
		cell4.innerHTML = '<a href="javascript: void(0);" onclick="javascript:Up_tbl_row(this); return false;" title="<?php echo _JLMS_MOVEUP;?>"><img src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_uparrow.png" width="16" height="16" border="0" alt="<?php echo _JLMS_MOVEUP;?>" /></a>';
		cell5.innerHTML = '&nbsp;';
		cell6.innerHTML = '&nbsp;';

		row.appendChild(cell3);
		row.appendChild(cell4);
		row.appendChild(cell5);
		row.appendChild(cell6);
		ReAnalize_tbl_Rows(sec_indx - 2, tbl_id);
	}
}

function Down_tbl_row(element) {
	if (element.parentNode.parentNode.sectionRowIndex < element.parentNode.parentNode.parentNode.rows.length - 1) {
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
		element.parentNode.parentNode.parentNode.deleteRow(element.parentNode.parentNode.sectionRowIndex);

		var cell3 = document.createElement("td");
		var cell4 = document.createElement("td");
		var cell5 = document.createElement("td");
		var cell6 = document.createElement("td");

		cell3.innerHTML = '<a href="javascript: void(0);" onclick="javascript:Delete_tbl_row(this); return false;" title="Delete"><img src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_cancel.png" width="16" height="16" border="0" alt="Delete" /></a>';
		cell4.innerHTML = '<a href="javascript: void(0);" onclick="javascript:Up_tbl_row(this); return false;" title="<?php echo _JLMS_MOVEUP;?>"><img src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_uparrow.png" width="16" height="16" border="0" alt="<?php echo _JLMS_MOVEUP;?>" /></a>';
		cell5.innerHTML = '&nbsp;';
		cell6.innerHTML = '&nbsp;';

		row.appendChild(cell3);
		row.appendChild(cell4);
		row.appendChild(cell5);
		row.appendChild(cell6);

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

function Add_new_tbl_field(elem_field, check_opt_field, elem_def_field, tbl_id, field_name, field_name2, filed_name_da, field_name3, field_name4) {
	var new_element_txt = getObj(elem_field).value;
	var new_element_def_ans = getObj(elem_def_field).value;
	if (trim(new_element_txt) == '') {
		alert("Please enter text to the field.");return;
	}
	var is_check = getObj(check_opt_field).checked;
	getObj(elem_field).value = '';
	getObj(elem_def_field).value = '';
	var tbl_elem = getObj(tbl_id);
	var row = tbl_elem.insertRow(tbl_elem.rows.length);
	var cell1 = document.createElement("td");
	var cell1a = document.createElement("td");
	var cell2 = document.createElement("td");
	var cell2a = document.createElement("td");
	var cell2b = document.createElement("td");
	var cell3 = document.createElement("td");
	var cell4 = document.createElement("td");
	var cell5 = document.createElement("td");
	var cell6 = document.createElement("td");
	var input_hidden = document.createElement("input");
	input_hidden.type = "hidden";
	input_hidden.name = field_name;
	input_hidden.value = '0';
	var input_check = document.createElement("input");
	input_check.type = "checkbox";
	input_check.name = field_name3;
	input_check.value = '1';
	input_check.checked = is_check;
	input_check.onchange=input_check.onclick = new Function('analyze_change_check2(this)');

	var input_check_hid = document.createElement("input");
	input_check_hid.type = "hidden";
	input_check_hid.name = field_name4;
	input_check_hid.value = is_check?'1':'0';
	var input_text = document.createElement("input");
	input_text.type = "text";
	input_text.size = 30;
	input_text.name = field_name2;
	input_text.value = new_element_txt;
	var input_text_da = document.createElement("input");
	input_text_da.type = "text";
	input_text_da.size = 20;
	input_text_da.name = filed_name_da;
	input_text_da.value = new_element_def_ans;
	cell1.align = 'center';
	cell1.innerHTML = 0;
	cell1a.align = 'center';
	cell1a.innerHTML = 0;
	cell2.appendChild(input_hidden);
	cell2.appendChild(input_text);
	cell2a.appendChild(input_check);
	cell2a.appendChild(input_check_hid);
	cell2b.appendChild(input_text_da);
	cell3.innerHTML = '<a href="javascript: void(0);" onclick="javascript:Delete_tbl_row(this); return false;" title="Delete"><img src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_cancel.png" width="16" height="16" border="0" alt="Delete" /></a>';
	cell4.innerHTML = '<a href="javascript: void(0);" onclick="javascript:Up_tbl_row(this); return false;" title="<?php echo _JLMS_MOVEUP;?>"><img src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_uparrow.png" width="16" height="16" border="0" alt="<?php echo _JLMS_MOVEUP;?>" /></a>';
	cell5.innerHTML = '&nbsp;';
	cell6.innerHTML = '&nbsp;';
	row.appendChild(cell1);
	row.appendChild(cell1a);
	row.appendChild(cell2);
	row.appendChild(cell2a);
	row.appendChild(cell2b);
	row.appendChild(cell3);
	row.appendChild(cell4);
	row.appendChild(cell5);
	row.appendChild(cell6);
	ReAnalize_tbl_Rows(tbl_elem.rows.length - 2, tbl_id);
}

function jlms_changeSRegDefaultValue(element, form_suffix) {
	var form = element.form;
	var is_dis = element.checked;
	form['new_field_left_'+form_suffix].disabled = is_dis;
	form['new_field_optional_'+form_suffix].disabled = is_dis;
	form['add_new_field_'+form_suffix].disabled = is_dis;
	form['new_field_def_'+form_suffix].disabled = is_dis;
	var ca_Item = form['course_question_'+form_suffix+'[]'];
	if (ca_Item) {
		if (ca_Item.length) {
			var i;
			for (i = 0; i<ca_Item.length; i++) {
				ca_Item[i].disabled = is_dis;
			}
		} else { ca_Item.disabled = is_dis; }
	}
	var cb_Item = form['course_question_id_'+form_suffix+'[]'];
	if (cb_Item) {
		if (cb_Item.length) {
			var i;
			for (i = 0; i<cb_Item.length; i++) {
				cb_Item[i].disabled = is_dis;
			}
		} else { cb_Item.disabled = is_dis; }
	}
	var cc_Item = form['course_quest_optional_hid_'+form_suffix+'[]'];
	if (cc_Item) {
		if (cc_Item.length) {
			var i;
			for (i = 0; i<cc_Item.length; i++) {
				cc_Item[i].disabled = is_dis;
			}
		} else { cc_Item.disabled = is_dis; }
	}
	var cd_Item = form['course_quest_optional_'+form_suffix+'[]'];
	if (cd_Item) {
		if (cd_Item.length) {
			var i;
			for (i = 0; i<cd_Item.length; i++) {
				cd_Item[i].disabled = is_dis;
			}
		} else { cd_Item.disabled = is_dis; }
	}
	var ce_Item = form['course_quest_def_answer_'+form_suffix+'[]'];
	if (ce_Item) {
		if (ce_Item.length) {
			var i;
			for (i = 0; i<ce_Item.length; i++) {
				ce_Item[i].disabled = is_dis;
			}
		} else { ce_Item.disabled = is_dis; }
	}
}
//-->
</script>
<?php
$JLMS_ACL = & JLMSFactory::getACL();
$lroles = $JLMS_ACL->GetSystemRoles(1);
global $JLMS_DB;
$row = null;
$query = "SELECT * FROM #__lms_spec_reg_questions WHERE course_id = $id AND role_id = 0 ORDER BY ordering";
$JLMS_DB->SetQuery($query);
$rows = $JLMS_DB->LoadObjectList();
$query = "SELECT * FROM #__lms_spec_reg_questions WHERE course_id = $id AND role_id <> 0 ORDER BY role_id, ordering";
$JLMS_DB->SetQuery($query);
$rows_add = $JLMS_DB->LoadObjectList();
	//if (count($lroles) > 1) { // temporary commented by DEN (13.April 2009)
	if (false) {
		$tabs = new JLMSTabs(0);
		echo $tabs->startPane("JLMS");
		echo $tabs->startTab('Default',"jlmsroletab_0");
		JLMS_course_html::JLMS_SpecQuestions_OneRole($rows);
		echo $tabs->endTab();
		foreach ($lroles as $lrole) {
			echo $tabs->startTab($lrole->lms_usertype,"jlmsroletab_".$lrole->id);
			$is_show = false;
			if (!empty($rows_add)) {
				$role_rows = array();
				foreach ($rows_add as $rac) {
					if ($rac->role_id == $lrole->id) {
						$role_rows[] = $rac;
					}
				}
				if (!empty($role_rows)) {
					JLMS_course_html::JLMS_SpecQuestions_OneRole($role_rows, $lrole->id, false);
					$is_show = true;
				}
			}
			if (!$is_show) {
				JLMS_course_html::JLMS_SpecQuestions_OneRole($rows, $lrole->id, true);
			}
			echo $tabs->endTab();
		}
		echo $tabs->endPane();
	} else {
		JLMS_course_html::JLMS_SpecQuestions_OneRole($rows);
	}
?>
					</div>
				</td>
			</tr>
		<?php } /* end of 'if($JLMS_CONFIG->get('show_course_spec_property'.....*/ ?>
		</table>
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="id" value="<?php echo $id; ?>" />
		<input type="hidden" name="task" value="settings_save" />
		<input type="hidden" name="is_inside" value="1" />
		<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
		</form>
<?php
		JLMS_TMPL::CloseTS();
		JLMS_TMPL::CloseMT();
	}

	function JLMS_SpecQuestions_OneRole( &$rows, $pref = '', $default = false ) {
		global $JLMS_CONFIG;
		if ($pref) {
			echo '<table width="100%" cellpadding="0" cellspacing="0"><tr>';
				echo '<td width="25%">Use default questions </td><td align="left">'.'<input size="30" onchange="jlms_changeSRegDefaultValue(this, \''.$pref.'\')" class="inputbox" type="checkbox"'.($default ? ' checked="checked"' : '').' name="course_quest_default'.($pref?'_'.$pref:'').'" value="1" /></td>';
			echo '</tr></table>';
		}
		echo '<table width="100%" cellpadding="0" cellspacing="0" id="course_questios'.($pref?'_'.$pref:'').'">';
		echo '<tr>';
		echo '	<td width="20" class="sectiontableheader" align="center">#</td>';
		echo '	<td width="20" class="sectiontableheader" align="center">ID</td>';
		echo '	<td class="sectiontableheader" width="200">'._JLMS_COURSES_REG_QUESTION.'</td>';
		echo '	<td class="sectiontableheader" width="30">'._JLMS_COURSES_REG_QUEST_OPTIONAL.'</td>';
		echo '	<td class="sectiontableheader" width="100">'._JLMS_COURSES_REG_QUEST_DEF_ANSWER.'</td>';
		echo '	<td class="sectiontableheader" width="20" align="center">&nbsp;</td>';
		echo '	<td class="sectiontableheader" width="20" align="center">&nbsp;</td>';
		echo '	<td class="sectiontableheader" width="20" align="center">&nbsp;</td>';
		echo '	<td class="sectiontableheader">&nbsp;</td>';
		echo '</tr>';
		if (!empty($rows)) {
			$k = 1;
			$ii = 1;
			$ind_last = count($rows);
			foreach ($rows as $row) {
				echo '<tr class="sectiontableentry'.$k.'">';
				echo '<td align="center">'.$ii.'</td>';
				echo '<td align="center">'.($default?0:$row->id).'</td>';
				echo '<td>';
					echo '<input type="hidden"'.($default ? ' disabled="disabled"' : '').' name="course_question_id'.($pref?'_'.$pref:'').'[]" value="'.$row->id.'" />';
					$strl = str_replace('"','&quot;',$row->course_question);
					echo '<input type="text" size="30" class="inputbox"'.($default ? ' disabled="disabled"' : '').' name="course_question'.($pref?'_'.$pref:'').'[]" value="'.$strl.'" />';
				echo '</td>';
				echo '<td>';
					echo '<input type="hidden"'.($default ? ' disabled="disabled"' : '').' name="course_quest_optional_hid'.($pref?'_'.$pref:'').'[]" value="'.($row->is_optional?'1':'0').'" />';
					echo '<input type="checkbox"'.($default ? ' disabled="disabled"' : '').' onchange="analyze_change_check(event)" name="course_quest_optional'.($pref?'_'.$pref:'').'[]"'.($row->is_optional?' checked="checked"':'').' value="1" />';
				echo '</td>';
				echo '<td>';
					$str2 = str_replace('"','&quot;',$row->default_answer);
					echo '<input type="text" size="20" class="inputbox"'.($default ? ' disabled="disabled"' : '').' name="course_quest_def_answer'.($pref?'_'.$pref:'').'[]" value="'.$str2.'" />';
				echo '</td>';
				echo '<td><a href="javascript:void(0);" onclick="Delete_tbl_row(this); return false;" title="Delete"><img src="'.$JLMS_CONFIG->get('live_site').'/components/com_joomla_lms/lms_images/toolbar/btn_cancel.png" width="16" height="16" border="0" alt="Delete" /></a></td>';
				echo '<td>';
				if ($ii > 1) {
					echo '<a href="javascript:void(0);" onclick="Up_tbl_row(this); return false;" title="'._JLMS_MOVEUP.'"><img src="'.$JLMS_CONFIG->get('live_site').'/components/com_joomla_lms/lms_images/toolbar/btn_uparrow.png" width="16" height="16" border="0" alt="'._JLMS_MOVEUP.'" /></a>';
				} else {
					echo '&nbsp;';
				}
				echo '</td>';
				echo '<td>';
				if ($ii < $ind_last) {
					echo '<a href="javascript:void(0);" onclick="Down_tbl_row(this); return false;" title="'._JLMS_MOVEDOWN.'"><img src="'.$JLMS_CONFIG->get('live_site').'/components/com_joomla_lms/lms_images/toolbar/btn_downarrow.png" width="16" height="16" border="0" alt="'._JLMS_MOVEDOWN.'" /></a>';
				} else {
					echo '&nbsp;';
				}
				echo '</td>';
				echo '<td>&nbsp;</td>';
				echo '</tr>';
				$k = 3 - $k; $ii ++;
			}
		}
		echo '</table>';
		echo '<br />';
		echo '<table width="100%" cellpadding="0" cellspacing="0">';
		echo '<tr>';
		echo '	<td width="60" align="center">&nbsp;</td>';
		echo '	<td width="200">';
		echo '	<input type="hidden" name="course_sr_types[]" value="'.$pref.'" />';
		echo '	<input id="new_field_left'.($pref?'_'.$pref:'').'" class="inputbox"'.($default ? ' disabled="disabled"' : '').' size="30" type="text" name="new_field_left'.($pref?'_'.$pref:'').'" />';
		echo '</td><td width="30">';
		echo '	<input id="new_field_optional'.($pref?'_'.$pref:'').'"'.($default ? ' disabled="disabled"' : '').' type="checkbox" name="new_field_optional'.($pref?'_'.$pref:'').'" />';
		echo '</td><td align="left">';
		//echo '<td width="100">';
			echo '<input id="new_field_def'.($pref?'_'.$pref:'').'"type="text" size="20" class="inputbox"'.($default ? ' disabled="disabled"' : '').' name="new_field_def'.($pref?'_'.$pref:'').'" />';
		echo '</td><td align="left">';
		echo '	<input class="inputbox" type="button"'.($default ? ' disabled="disabled"' : '').' name="add_new_field'.($pref?'_'.$pref:'').'" style="width:70px " value="New" onclick="javascript:Add_new_tbl_field(\'new_field_left'.($pref?'_'.$pref:'').'\', \'new_field_optional'.($pref?'_'.$pref:'').'\', \'new_field_def'.($pref?'_'.$pref:'').'\', \'course_questios'.($pref?'_'.$pref:'').'\', \'course_question_id'.($pref?'_'.$pref:'').'[]\', \'course_question'.($pref?'_'.$pref:'').'[]\', \'course_quest_def_answer'.($pref?'_'.$pref:'').'[]\', \'course_quest_optional'.($pref?'_'.$pref:'').'[]\', \'course_quest_optional_hid'.($pref?'_'.$pref:'').'[]\');" />';
		echo '</td>';
		echo '</tr>';
		echo '</table>';
	}

	function showCourseDetails( $id, &$row, &$params, $option, &$lists, $view_type = 'view' ) {
		$_JLMS_PLUGINS = & JLMSFactory::getPlugins();
		global $Itemid, $my, $JLMS_CONFIG, $JLMS_SESSION;
		$count_modules = 0;
		$JLMS_ACL = & JLMSFactory::getACL();
		if ( $JLMS_ACL->GetRole() && !$JLMS_ACL->isStaff() ) {
			if ( $params->get('homework_view') && $JLMS_CONFIG->get('course_homework') ) $count_modules++;
			if ( $params->get('agenda_view') ) $count_modules++;
			if ( $params->get('dropbox_view') ) $count_modules++;
			if ( $params->get('mailbox_view') ) $count_modules++;
			if ( $params->get('certificates_view') ) $count_modules++;
			if ( $params->get('latest_forum_posts_view') ) $count_modules++;
			if ($count_modules) { ?>
		
		<?php } }

		JLMS_TMPL::OpenMT();

		$hparams = array();
		if($view_type == 'view'){
			$hparams['simple_menu'] = true;
			$hparams['sys_msg'] = _JLMS_MESSAGE_SHORT_COURSE_INFO;
		}
		if ($view_type == 'offerWL') {
			$hparams['sys_msg'] = _JLMS_MESSAGE_OFFER_JOIN_WAITING_LIST;
		} elseif ($view_type == 'inWL') {
			$hparams['sys_msg'] = _JLMS_MESSAGE_ALREADY_IN_WATING_LIST;
			$JLMS_CONFIG->set('enableterms', 0);
		}
		if ($JLMS_SESSION->get('joomlalms_just_joined', 0)) {
			if (isset($hparams['sys_msg'])) $hparams['sys_msg'] = $JLMS_SESSION->get('joomlalms_sys_message', '').'<br />'.$hparams['sys_msg'];
			else $hparams['sys_msg'] = $JLMS_SESSION->get('joomlalms_sys_message', '');
			$JLMS_SESSION->clear('joomlalms_just_joined');
		}
		$toolbar = array();		
		$JLMS_ACL = & JLMSFactory::getACL();		
		if ($JLMS_ACL->CheckPermissions('course', 'manage_settings')) {			
			$toolbar[] = array('btn_type' => 'newtopic', 'btn_js' => "index.php?option=$option&amp;Itemid=$Itemid&amp;task=add_topic&amp;id=$id", 'btn_str' => _JLMS_TOPIC_T_ADD );
			$toolbar[] = array('btn_type' => 'settings', 'btn_js' => sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid&amp;task=settings&amp;id=$id") );
			$toolbar[] = array('btn_type' => 'edit', 'btn_js' => ampReplace(sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid&amp;task=edit_course&amp;id=$id&amp;is_inside=1")) );
		} elseif($view_type == 'view' || $view_type == 'offerWL' || $view_type == 'inWL') {			
			$hparams['simple_menu'] = true;
			if ($my->id && $row->self_reg && JLMS_checkCourseGID($my->id, $row->gid)) {//check that enrollment is available and user gid is allowed
				if ($row->paid) {
					if ($view_type != 'inWL') $toolbar[] = array('btn_type' => 'yes','btn_str' => _JLMS_SUBSCRIBE, 'btn_js' => "javascript:submitbutton('subscription','');" );
				} else {
					if ($view_type != 'inWL') $toolbar[] = array('btn_type' => 'yes','btn_str' => _JLMS_SUBSCRIBE, 'btn_js' => "javascript:submitbutton('course_subscribe','');" );
				}
				if ($view_type != 'inWL') $toolbar[] = array('btn_type' => 'cancel', 'btn_js' => sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid&amp;task=courses") );
				else $toolbar[] = array('btn_type' => 'back', 'btn_js' => sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid&amp;task=courses") );
				$hparams['toolbar_position'] = 'center';
				$html_code_before_toolbar = "
				<form action='".sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid")."' method='post' name='adminForm_enroll'>
				<script language='javascript' type='text/javascript'>
					<!--

					function submitbutton(pressbutton) {
						var form = document.adminForm_enroll;";
				if ($JLMS_CONFIG->get('enableterms') && !$row->paid){
					$html_code_before_toolbar .= "
						if( (pressbutton == 'course_subscribe') && (document.getElementById('jlms_agreement').checked == false) ){
							alert( '".addslashes(_JLMS_AGREEMENT)."' );
						}
						else{
							form.task.value = pressbutton;
							form.submit();
						}
						";
				} else {
					$html_code_before_toolbar .= "
							form.task.value = pressbutton;
							form.submit();
					";
				}
				$html_code_before_toolbar .= "
							}
					//-->
					</script>
					";
				if ($JLMS_CONFIG->get('enableterms') && !$row->paid){
					$html_code_before_toolbar .= "
					<table align='center' class='jlms_table_no_borders'><tr><td width='20'>
						<input type='checkbox' name='agreement' id='jlms_agreement' />
					</td><td style='text-align:left'>
						<label for='jlms_agreement'>" . $JLMS_CONFIG->get('jlms_terms')."</label>
					</td></tr></table>
					";
				}
				if ($row->paid) {
					$html_code_before_toolbar .= "
						<input type='hidden' name='option' value='".$option."' />
						<input type='hidden' name='Itemid' value='".$Itemid."' />
						<input type='hidden' name='task' value='subscription' />
						<input type='hidden' name='cid[]' value='".$row->id."' />
						<input type='hidden' name='state' value='0' />
						</form>
					";
				} else {
					$html_code_before_toolbar .= "
						<input type='hidden' name='option' value='".$option."' />
						<input type='hidden' name='Itemid' value='".$Itemid."' />
						<input type='hidden' name='task' value='courses' />
						<input type='hidden' name='id' value='".$row->id."' />
						<input type='hidden' name='state' value='0' />
						</form>
					";
				}
				
				$hparams['html_code_before_toolbar'] = $html_code_before_toolbar;
			} else {
				$toolbar[] = array('btn_type' => 'back', 'btn_js' => sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid&amp;task=courses") );
			}
		} elseif ($view_type == 'future_course') {			
			$hparams['simple_menu'] = true;
			if ($row->publish_start) {
				$hparams['sys_msg'] = str_replace('{date}', JLMS_dateToDisplay($row->start_date), _JLMS_COURSE_IS_PENDING_MSG);
			} else {
				$query = "SELECT start_date FROM #__lms_users_in_groups WHERE course_id = ".$row->id." AND user_id = ".$my->id;
				global $JLMS_DB;
				$JLMS_DB->SetQuery($query);
				$user_start = $JLMS_DB->LoadResult();
				$hparams['sys_msg'] = str_replace('{date}', JLMS_dateToDisplay($user_start), _JLMS_COURSE_USER_IS_PENDING);
			}
		}		
		JLMS_TMPL::ShowHeader('course', _JLMS_COURSES_DETAILS, $hparams, $toolbar);
		
		$_JLMS_PLUGINS->loadBotGroup('system');
		$plugin_args = array();
		$plugin_args[] = $id;
		$_JLMS_PLUGINS->trigger('onAboveCourseDetailsPage', $plugin_args);
		
		if ( $JLMS_ACL->GetRole() && !$JLMS_ACL->isStaff() && $count_modules ) {
			$custom_sections = array();
			if ($JLMS_CONFIG->get('course_homework') && $params->get('homework_view')) {
				$txt = JLMS_showMyHomeWork($option, $Itemid, $lists['my_hw']);
				$custom_sections[] = array('text' => $txt, 'attrib' => ' valign="top"');
			}
			if ($params->get('agenda_view')) {
				$txt = JLMS_showMyAgenda($option, $Itemid, $lists['my_announcements']);
				$custom_sections[] = array('text' => $txt, 'attrib' => ' valign="top"');
			}
			if ($params->get('dropbox_view')) {
				$txt = JLMS_showMyDropBox($option, $Itemid, $lists['my_dropbox'], $lists);
				$custom_sections[] = array('text' => $txt, 'attrib' => ' valign="top"');
			}
			if ($params->get('mailbox_view')) {
				$txt = JLMS_showMyMailBox($option, $Itemid, $lists['my_mailbox'], $lists);
				$custom_sections[] = array('text' => $txt, 'attrib' => ' valign="top"');
			}
			if ($params->get('certificates_view')) {
				$txt = JLMS_showMyCertificates($option, $Itemid, $lists['my_certificates'], $lists);
				$custom_sections[] = array('text' => $txt, 'attrib' => ' valign="top"');
			}
			if ($JLMS_CONFIG->get('plugin_forum') && $params->get('latest_forum_posts_view')) {
				$txt = JLMS_showLatestForumPosts($option, $Itemid, $lists['latest_forum_posts'], $lists);
				$custom_sections[] = array('text' => $txt, 'attrib' => ' valign="top"');
			}
			JLMS_TMPL::ShowCustomSection($custom_sections,1,1);
		}

		$show_description = true;
		if ( $JLMS_ACL->GetRole() && !$JLMS_ACL->isStaff() ) {
			if (!$params->get('show_description', 1)) {
				$show_description = false;
			}
		}

		if ($show_description) {
			$text = JLMS_ShowText_WithFeatures($row->course_description);
			JLMS_TMPL::ShowSection($text);
		}

		//$_JLMS_PLUGINS->loadBotGroup('system');
		$plugin_args = array();
		$plugin_args[] = $id;
		$_JLMS_PLUGINS->trigger('onBelowCourseDescription', $plugin_args);

		/*Fix short course description + all comments*/
		if(isset($lists['short']) && !$lists['short']){
			if ( $JLMS_ACL->GetRole() && !$JLMS_ACL->isStaff() ) {
				//show topics of the course
				global $JLMS_DB;
				$query = "SELECT count(*) FROM #__lms_topics WHERE course_id = $id";
				$JLMS_DB->SetQuery($query);
				$is_any_topic = $JLMS_DB->LoadResult();
				if ($is_any_topic) {
					JLMS_TMPL::OpenTS();
					$course = new JLMS_Course_HomePage($id);
					$course->listTopics();
					JLMS_TMPL::CloseTS();
				}
			}
		}

		//$_JLMS_PLUGINS->loadBotGroup('system');
		$plugin_args = array();
		$plugin_args[] = $id;
		$_JLMS_PLUGINS->trigger('onBelowCourseDetailsPage', $plugin_args);

		JLMS_TMPL::CloseMT();
	}

	/**
	 * Otobragaet interface dlya vybora course tools dlya posleduyushego exporta
	 *
	 * @param int $id - course ID
	 * @param string $option - Joomla option variable
	 */
	function view_preExportPage( $id, $option ) {
		global $Itemid; ?>
<script language="javascript" type="text/javascript">
<!--
function submitbutton(pressbutton) {
	var form = document.adminForm;
	if ( (pressbutton == 'export_course') && form.boxchecked.value == "0"){
		alert( "<?php echo _JLMS_ALERT_SELECT_ITEM;?>" );
	} else {
		form.task.value = pressbutton;
		form.submit();
	}
}
//-->
</script>
<?php
		JLMS_TMPL::OpenMT();

		$params = array(
			'sys_msg' => _JLMS_COURSES_EXPORT_MESSAGE,
			'show_menu' => false,
			'toolbar_position' => 'center'
		);
		$toolbar = array();
		$toolbar[] = array('btn_type' => 'export', 'btn_js' => "javascript:submitbutton('export_course');");
		$toolbar[] = array('btn_type' => 'back', 'btn_js' => "javascript:submitbutton('cancel_course');");
		JLMS_TMPL::ShowHeader('course', _JLMS_COURSES_TITLE_EXPORT_COURSE, $params, $toolbar);

			$rows = array();
			$rows[] = array('id' => 1, 'name' => _JLMS_TOOLBAR_DOCS);
			$rows[] = array('id' => 2, 'name' => _JLMS_TOOLBAR_LPATH);
			$rows[] = array('id' => 3, 'name' => 'SCORMs');
			$rows[] = array('id' => 4, 'name' => _JLMS_TOOLBAR_LINKS);
			$rows[] = array('id' => 5, 'name' => _JLMS_TOOLBAR_QUIZZES);
			$rows[] = array('id' => 6, 'name' => _JLMS_TOOLBAR_AGENDA);
			$rows[] = array('id' => 7, 'name' => _JLMS_TOOLBAR_HOMEWORK);
			$rows[] = array('id' => 8, 'name' => _JLMS_TOOLBAR_GRADEBOOK.' '._JLMS_COURSES_EXPORT_GB_SETTINGS_ONLY);

		JLMS_TMPL::OpenTS();
?>
				<br />
					<form action="<?php echo sefRelToAbs("index.php?option=".$option."&Itemid=".$Itemid);?>" method="post" name="adminForm">
				<table width="100%" cellpadding="0" cellspacing="0" border="0" class="<?php echo JLMSCSS::_('jlmslist');?>">
					<tr>
						<<?php echo JLMSCSS::tableheadertag();?> width="20" class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center">#</<?php echo JLMSCSS::tableheadertag();?>>
						<<?php echo JLMSCSS::tableheadertag();?> width="20" class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></<?php echo JLMSCSS::tableheadertag();?>>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>"><?php echo _JLMS_COURSES_EXPORT_TOOL_NAME;?></<?php echo JLMSCSS::tableheadertag();?>>
					</tr>
				<?php
				$k = 1;
				for ($i=0, $n=count($rows); $i < $n; $i++) {
					$row = $rows[$i];
					$checked = JLMS_course_html::idBox( $i, $row['id'], true); ?>
					<tr class="<?php echo JLMSCSS::_('sectiontableentry'.$k); ?>">
						<td align="center"><?php echo ( $i + 1 ); ?></td>
						<td><?php echo $checked; ?></td>
						<td align="left">
						<?php echo $row['name'];?>
						</td>
					</tr>
					<?php
					$k = 3 - $k;
				}?>
				</table>
					<input type="hidden" name="option" value="<?php echo $option;?>" />
					<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
					<input type="hidden" name="task" value="export_course" />
					<input type="hidden" name="id" value="<?php echo $id;?>" />
					<input type="hidden" name="boxchecked" value="<?php echo count($rows);?>" />
					</form>
<?php
	JLMS_TMPL::CloseTS();
	JLMS_TMPL::CloseMT();
	}


	function JLMS_Import( $option, &$lists ){
		global $my, $Itemid; ?>
<script language="javascript" type="text/javascript">
<!--
function copy_ch_Selected(allbuttons, targetbuttons){
	for (i=0;i<allbuttons.length;i++) {
		for (j=0;j<targetbuttons.length;j++) {
			if (targetbuttons[j].value == allbuttons[i].value) {
				targetbuttons[j].checked = allbuttons[i].checked;
			}
		}
	}
}
function submitform_BB( bb_form_name ) {
	bb_f_name = eval('document.'+bb_form_name);
	bb_f_name.bb_course_name.value = document.adminForm_BB_name.bb_course_name.value;
	<?php if (isset($lists['do_merge']) && $lists['do_merge']) { ?>
	bb_f_name.merge_course.value = getSelectedValue('adminForm_BB_merge', 'merge_course');
	<?php } ?>
	if ( document.adminForm_BB_options.boxchecked.value == "0"){
		alert( "<?php echo _JLMS_ALERT_SELECT_ITEM;?>" );
	} else {
		if (bb_f_name.jlms_ifile.value) {
			copy_ch_Selected(document.adminForm_BB_options['cid[]'], bb_f_name['cid[]']);
			bb_f_name.submit();
		}
	}
}
function submitform_LMS(adForm) {
	lms_f_name = eval('document.'+adForm);
	if (lms_f_name.jlms_ifile.value) {
		if (lms_f_name.boxchecked.value == "0") {
			alert( "<?php echo _JLMS_ALERT_SELECT_ITEM;?>" );
		} else {
			lms_f_name.submit();
		}
	}
}
function isChecked_BB(isitchecked){
	if (isitchecked == true){
		document.adminForm_BB_options.boxchecked.value++;
	}
	else {
		document.adminForm_BB_options.boxchecked.value = document.adminForm_BB_options.boxchecked.value - 1;
	}
}
function checkAll_BB( n, fldName ) {
  if (!fldName) {
     fldName = 'cb';
  }
	var f = document.adminForm_BB_options;
	var c = f.toggle.checked;
	var n2 = 0;
	for (i=0; i < n; i++) {
		cb = eval( 'f.' + fldName + '' + i );
		if (cb) {
			cb.checked = c;
			n2++;
		}
	}
	if (c) {
		document.adminForm_BB_options.boxchecked.value = n2;
	} else {
		document.adminForm_BB_options.boxchecked.value = 0;
	}
}
//-->
</script>
	<?php
		JLMS_TMPL::OpenMT();

		$params = array();
		$params['show_menu'] = false;
		$toolbar = array();
		$toolbar[] = array('btn_type' => 'back', 'btn_js' => sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid&amp;task=courses"));
		$params['toolbar'] = $toolbar;
		JLMS_TMPL::ShowHeader('course', _JLMS_COURSES_IMPORT, $params);

			$rows = array();
			$rows[] = array('id' => 1, 'name' => _JLMS_TOOLBAR_DOCS);
			$rows[] = array('id' => 2, 'name' => _JLMS_TOOLBAR_LPATH);
			$rows[] = array('id' => 3, 'name' => 'SCORMs');
			$rows[] = array('id' => 4, 'name' => _JLMS_TOOLBAR_LINKS);
			$rows[] = array('id' => 5, 'name' => _JLMS_TOOLBAR_QUIZZES);
			$rows[] = array('id' => 6, 'name' => _JLMS_TOOLBAR_AGENDA);
			$rows[] = array('id' => 7, 'name' => _JLMS_TOOLBAR_HOMEWORK);
			$rows[] = array('id' => 8, 'name' => _JLMS_TOOLBAR_GRADEBOOK.' '._JLMS_COURSES_EXPORT_GB_SETTINGS_ONLY);

			$rows_bb = array();
			$rows_bb[] = array('id' => 1, 'name' => _JLMS_TOOLBAR_DOCS);
			$rows_bb[] = array('id' => 4, 'name' => _JLMS_TOOLBAR_LINKS);
			$rows_bb[] = array('id' => 5, 'name' => _JLMS_TOOLBAR_QUIZZES);
			$rows_bb[] = array('id' => 6, 'name' => _JLMS_TOOLBAR_AGENDA);
			$rows_bb[] = array('id' => 7, 'name' => _JLMS_TOOLBAR_HOMEWORK);
			$rows_bb[] = array('id' => 8, 'name' => _JLMS_TOOLBAR_GRADEBOOK);

		JLMS_TMPL::OpenTS('', ' valign="top"');

					$tabs = new JLMSTabs(0);
					echo $tabs->startPane("JLMS");
					echo $tabs->startTab("JoomlaLMS","jlmstab1");?>
					<form action="<?php echo sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid");?>" method="post" name="adminForm"  enctype="multipart/form-data">
					<table width="100%" cellpadding="0" cellspacing="0" border="0" class="jlms_table_no_borders">
					<tr>
						<th align="left" colspan="2">
						<?php echo _JLMS_COURSE_UPLOAD_PACKAGE;?>
						</th>
					</tr>
					<tr>
						<td align="left" style="text-align:left" width="200">
						<?php echo _JLMS_COURSE_PACKAGE_FILE;?></td><td>
						<input name="jlms_ifile" type="file"/>
						<input type="hidden" name="pack_type" value="joomlalms" />
						<input class="button" type="button" onclick="submitform_LMS('adminForm');" value="<?php echo _JLMS_COURSES_IMPORT_BTN;?>" />
						</td>
					</tr>
					<?php if (isset($lists['do_merge']) && $lists['do_merge']) { ?>
					<tr>
						<td align="left" style="text-align:left;">
						<?php
						echo _JLMS_COURSES_IMPORT_MERGE;?></td><td>
						<?php
						echo $lists['my_courses']; ?>
						</td>
					</tr>
					<?php } ?>
					<tr>
						<td colspan="2">
						<table width="100%" cellpadding="0" cellspacing="0" border="0" class="<?php echo JLMSCSS::_('jlmslist');?>">
							<tr>
								<<?php echo JLMSCSS::tableheadertag();?> width="20" class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center">#</<?php echo JLMSCSS::tableheadertag();?>>
								<<?php echo JLMSCSS::tableheadertag();?> width="20" class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></<?php echo JLMSCSS::tableheadertag();?>>
								<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>"><?php echo _JLMS_COURSES_EXPORT_TOOL_NAME;?></<?php echo JLMSCSS::tableheadertag();?>>
							</tr>
						<?php
						$k = 1;
						for ($i=0, $n=count($rows); $i < $n; $i++) {
							$row = $rows[$i];
							$checked = JLMS_course_html::idBox( $i, $row['id'], true); ?>
							<tr class="<?php echo JLMSCSS::_('sectiontableentry'.$k); ?>">
								<td align="center"><?php echo ( $i + 1 ); ?></td>
								<td><?php echo $checked; ?></td>
								<td align="left">
								<?php echo $row['name'];?>
								</td>
							</tr>
							<?php
							$k = 3 - $k;
						}?>
						</table>
						</td>
					</tr>
					</table>
					<input type="hidden" name="task" value="import" />
					<input type="hidden" name="boxchecked" value="<?php echo count($rows);?>" />
					</form>
					<?php if (isset($lists['template_list']) && count($lists['template_list'])) { ?>
					<form action="<?php echo sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid");?>" method="post" name="adminForm2">
						<script type="text/javascript"><!--
						var chk = false;
						function frm_validate(){
							if(!chk)
							{
								alert("<?php echo _JLMS_ALERT_SELECT_ITEM;?>");
							}
							else if(document.adminForm2.course_zname.value == ''){
								alert("<?php echo _JLMS_PL_ENTER_NAME;?>");
							}
							else
							{
								document.adminForm2.submit();
							}
						}
						//-->
						</script>
						<table cellpadding="0" cellspacing="0" border="0" width="100%" class="jlms_table_no_borders">
							<tr>
								<td colspan="2">
								<table cellpadding="0" cellspacing="0" border="0" width="100%" class="<?php echo JLMSCSS::_('jlmslist');?>">
								<tr>
									<<?php echo JLMSCSS::tableheadertag();?> width="20" class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center">#</<?php echo JLMSCSS::tableheadertag();?>>
									<<?php echo JLMSCSS::tableheadertag();?> width="20" class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center">&nbsp;</<?php echo JLMSCSS::tableheadertag();?>>
									<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>">Import Template</<?php echo JLMSCSS::tableheadertag();?>>
								</tr>
								<?php
								$k = 1;
								for ($i=0, $n=count($lists['template_list']); $i < $n; $i++) {

									?>
									<tr class="<?php echo JLMSCSS::_('sectiontableentry'.$k); ?>">
										<td align="center" width="20"><?php echo ( $i + 1 ); ?></td>
										<td width="20"><input type="radio" name="tpl_id" value="<?php echo $lists['template_list'][$i]->id;?>" onclick="javascript:chk=true;" /></td>
										<td align="left">
										<?php echo $lists['template_list'][$i]->templ;?>
										</td>
									</tr>
									<?php
									$k = 3 - $k;
								}?>

								</table>
								</td>
							</tr>
							<tr>
								<td width="200">
									<?php echo _JLMS_COURSES_TBL_HEAD_NAME;?>
								</td>
								<td>
									<input type="text" class="inputbox" style="width:200px" maxlength="100" name="course_zname" value="" />
									<input class="button" type="button" onclick="frm_validate();" value="<?php echo _JLMS_COURSES_INSTALL_BTN;?>" />
									<input type="hidden" name="task" value="import_tpl" />
								</td>
							</tr>
						</table>
					</form>
					<?php } /* end of template importing */ ?>
				<?php echo $tabs->endTab();
					echo $tabs->startTab("BlackBoard","jlmstab2"); ?>
					<table cellpadding="0" cellspacing="0" border="0" width="100%" class="jlms_table_no_borders">
					<tr>
						<td align="left" style="text-align:left">
						<?php echo _JLMS_COURSES_TBL_HEAD_NAME;?></td><td>
						<form onsubmit="return false;" method="post" name="adminForm_BB_name">
						<input class="inputbox" style="width:200px" name="bb_course_name" type="text" value=""/>
						</form>
						</td>
					</tr>
					<tr>
						<th colspan="2" align="left" style="text-align:left">
						<?php echo _JLMS_COURSES_BB_IMPORT;?>
						</th>
					</tr>
					<tr>
						<td align="left" style="text-align:left">
						<?php echo _JLMS_COURSE_PACKAGE_FILE;?></td><td>
						<form action="<?php echo sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid");?>" method="post" name="adminForm_BB"  enctype="multipart/form-data">
						<input class="inputbox" style="width:240px" name="jlms_ifile" type="file"/>
						<input type="hidden" name="pack_type" value="blackboard" />
						<input class="button" type="button" onclick="submitform_BB('adminForm_BB');" value="<?php echo _JLMS_COURSES_IMPORT_BTN;?>" />
						<input name="bb_course_name" type="hidden" value=""/>
						<input type="hidden" name="task" value="import" />
						<input type="hidden" name="merge_course" value="0" />
						<?php
						for ($i=0, $n=count($rows_bb); $i < $n; $i++) {
							$row = $rows_bb[$i];
							echo '<input type="checkbox" name="cid[]" value="'.$row['id'].'"'.($checked?' checked="checked"':'').' style="visibility:hidden" />';
						}?>
						</form>
						</td>
					</tr>
					<tr>
						<th colspan="2" align="left" style="text-align:left">
						<?php echo _JLMS_COURSES_BB_MEDIA_IMPORT;?>
						</th>
					</tr>
					<tr>
						<td align="left" style="text-align:left">
						<?php echo _JLMS_COURSES_IMPORT_BB_PACK;?></td><td>
						<form action="<?php echo sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid");?>" method="post" name="adminForm_BB_media">
						<input class="inputbox" style="width:240px" name="jlms_ifile" type="text"/>
						<input type="hidden" name="pack_type" value="blackboard_media" />
						<input class="button" type="button" onclick="submitform_BB('adminForm_BB_media');" value="<?php echo _JLMS_COURSES_INSTALL_BTN;?>" />
						<input name="bb_course_name" type="hidden" value=""/>
						<input type="hidden" name="task" value="import" />
						<input type="hidden" name="merge_course" value="0" />
						<?php
						for ($i=0, $n=count($rows_bb); $i < $n; $i++) {
							$row = $rows_bb[$i];
							echo '<input type="checkbox" name="cid[]" value="'.$row['id'].'"'.($checked?' checked="checked"':'').' style="visibility:hidden" />';
						}?>
						</form>
						</td>
					</tr>
					<?php if (isset($lists['do_merge']) && $lists['do_merge']) { ?>
					<tr>
						<td align="left" style="text-align:left;">
						<?php
						echo _JLMS_COURSES_IMPORT_MERGE;?></td><td>
						<form onsubmit="return false;" method="post" name="adminForm_BB_merge">
						<?php echo $lists['my_courses']; ?>
						</form>
						</td>
					</tr>
					<?php } ?>
					<tr>
						<td colspan="2">
						<form onsubmit="return false;" method="post" name="adminForm_BB_options">
						<table width="100%" cellpadding="0" cellspacing="0" border="0" class="<?php echo JLMSCSS::_('jlmslist');?>">
							<tr>
								<<?php echo JLMSCSS::tableheadertag();?> width="20" class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center">#</<?php echo JLMSCSS::tableheadertag();?>>
								<<?php echo JLMSCSS::tableheadertag();?> width="20" class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center"><input type="checkbox" name="toggle" value="" onclick="checkAll_BB(<?php echo count($rows); ?>, 'cbb');" /></<?php echo JLMSCSS::tableheadertag();?>>
								<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>"><?php echo _JLMS_COURSES_EXPORT_TOOL_NAME;?></<?php echo JLMSCSS::tableheadertag();?>>
							</tr>
						<?php
						$k = 1;
						for ($i=0, $n=count($rows_bb); $i < $n; $i++) {
							$row = $rows_bb[$i];
							$checked = JLMS_course_html::idBox( $i, $row['id'], true, 'cid', 'cbb', 'isChecked_BB'); ?>
							<tr class="<?php echo JLMSCSS::_('sectiontableentry'.$k); ?>">
								<td align="center"><?php echo ( $i + 1 ); ?></td>
								<td><?php echo $checked; ?></td>
								<td align="left">
								<?php echo $row['name'];?>
								</td>
							</tr>
							<?php
							$k = 3 - $k;
						}?>
						</table>
						<input type="hidden" name="boxchecked" value="<?php echo count($rows_bb);?>" />
						</form>
						</td>
					</tr>
					</table>
				<?php echo $tabs->endTab();
				echo $tabs->endPane();
		JLMS_TMPL::CloseTS();
		JLMS_TMPL::CloseMT();
	}

	function JLMS_Import_succesfull($option){
		global $Itemid;

		JLMS_TMPL::OpenMT();

		$params = array(
			'show_menu' => false,
			'toolbar_position' => 'center'
		);
		$toolbar = array();
		$toolbar[] = array('btn_type' => 'back', 'btn_js' => sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid&amp;task=courses"));
		JLMS_TMPL::ShowHeader('course', _JLMS_COURSES_IMPORT, $params, $toolbar);

		JLMS_TMPL::CloseMT();
	}

	function view_preDeletePage( &$rows, $id, $option ) {
		global $Itemid; ?>
<script language="javascript" type="text/javascript">
<!--
function submitbutton(pressbutton) {
	var form = document.adminForm;
	if ((pressbutton == 'course_delete_yes') && (form.boxchecked.value == "0")){
		alert( "<?php echo _JLMS_ALERT_SELECT_ITEM;?>" );
	} else {
		form.task.value = pressbutton;
		form.submit();
	}
}
//-->
</script>
	<?php
		JLMS_TMPL::OpenMT();

		$params = array(
			'show_menu' => false,
			'sys_msg' => _JLMS_COURSES_DEL_ALERT_MESSAGE,
			'toolbar_position' => 'center'
		);
		$toolbar = array();
		$toolbar[] = array('btn_type' => 'yes', 'btn_js' => "javascript:submitbutton('course_delete_yes');");
		$toolbar[] = array('btn_type' => 'cancel', 'btn_js' => "javascript:submitbutton('cancel_course');");
		JLMS_TMPL::ShowHeader('course', _JLMS_COURSES_TITLE_DEL_COURSE, $params, $toolbar);

		JLMS_TMPL::OpenTS();
		?>
				<br />
					<form action="<?php echo sefRelToAbs("index.php?option=".$option."&Itemid=".$Itemid);?>" method="post" name="adminForm">
				<table width="100%" cellpadding="0" cellspacing="0" border="0" class="<?php echo JLMSCSS::_('jlmslist');?>">
					<tr>
						<<?php echo JLMSCSS::tableheadertag();?> width="20" class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center">#</<?php echo JLMSCSS::tableheadertag();?>>
						<<?php echo JLMSCSS::tableheadertag();?> width="20" class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></<?php echo JLMSCSS::tableheadertag();?>>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>"><?php echo _JLMS_COURSES_TBL_HEAD_NAME;?></<?php echo JLMSCSS::tableheadertag();?>>
					</tr>
				<?php
				$k = 1;
				for ($i=0, $n=count($rows); $i < $n; $i++) {
					$row = $rows[$i];
					$checked = JLMS_course_html::idBox( $i, $row->id, true); ?>
					<tr class="<?php echo JLMSCSS::_('sectiontableentry'.$k); ?>">
						<td align="center"><?php echo ( $i + 1 ); ?></td>
						<td><?php echo $checked; ?></td>
						<td align="left">
						<?php echo $row->course_name;?>
						</td>
					</tr>
					<?php
					$k = 3 - $k;
				}?>
				</table>
					<input type="hidden" name="option" value="<?php echo $option;?>" />
					<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
					<input type="hidden" name="task" value="delete_course" />
					<input type="hidden" name="id" value="<?php echo $id;?>" />
					<input type="hidden" name="boxchecked" value="<?php echo count($rows);?>" />
					</form>
	<?php
		JLMS_TMPL::CloseTS();
		JLMS_TMPL::CloseMT();
	}

	function idBox( $rowNum, $recId, $checked=false, $name='cid', $fldName = 'cb', $ch_func = 'isChecked' ) {
		return '<input type="checkbox" id="'.$fldName.$rowNum.'" name="'.$name.'[]" value="'.$recId.'" onclick="'.$ch_func.'(this.checked);"'.($checked?' checked="checked"':'').' />';
	}
}
?>