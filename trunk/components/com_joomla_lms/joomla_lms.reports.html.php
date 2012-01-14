<?php
/**
* joomla_lms.reports.html.php
* (c) JoomaLMS eLearning Software http://www.joomlalms.com/
**/

// no direct access
defined( '_JLMS_EXEC' ) or die( 'Restricted access' );

class JLMS_reports_html {
	function JLMS_sreportAccess( $tot_hits, $ti, $hits, $users, $courses, $pageNav, $start_date, $end_date, $lists, $levels, $filt_cat, $filt_group, $option, $is_full){		
		$JLMS_CONFIG = & JLMSFactory::getConfig();
		global $JLMS_DB;
		$Itemid = $JLMS_CONFIG->get('Itemid');		
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
	
	function pickup_date(){
		var form = document.adminForm;
		form.end_date.value = form.pick_date.value;
		form.start_date.value = form.pick_to_date.value;
		var is_cor = 1;
		if(form.start_date.value && form.end_date.value)
		{
			
			if("<?php echo $JLMS_CONFIG->get('date_format',"Y-m-d")?>" == "d-m-Y"){
				if(form.end_date.value.substring(5)<form.start_date.value.substring(5)){
					is_cor = 0;
				}else if( form.end_date.value.substring(2,5)<form.start_date.value.substring(2,5)){
					is_cor = 0;
				}else if( form.end_date.value.substring(0,2)<form.start_date.value.substring(0,2)){
					is_cor = 0;
				}
			}else{	
				if(form.end_date.value.substring(0,4)<form.start_date.value.substring(0,4)){
					is_cor = 0;
				}else if( form.end_date.value.substring(5,7)<form.start_date.value.substring(5,7)){
					is_cor = 0;
				}else if( form.end_date.value.substring(8,10)<form.start_date.value.substring(8,10)){
					is_cor = 0;
				}
			}
			
		}
		if(!is_cor){
			alert("<?php echo _JLMS_REPORTS_SELECT_DATE;?>");
		}else{
			form.view.value = '';	
			form.submit();	
		}
	}
	
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
	function submitFormView(view){
		var form = document.adminForm;
		form.view.value = view;
		form.task.value='report_access';
		form.submit();
	}
	
	//--><!]]>
	</script>
	<?php		
			JLMS_TMPL::OpenMT();
			if($is_full){
				$hparams = array('show_menu'=>false);
			}else{
				$hparams = array('simple_menu'=>true);
			}
			$toolbar = array();

			$page_heading = _JLMS_REPORTS_ACCESS;
			if ($JLMS_CONFIG->get('is_trial', false) && $JLMS_CONFIG->get('trial_reports_heading_text', '')) {
				$page_heading .= $JLMS_CONFIG->get('trial_reports_heading_text', '');
			}

			JLMS_TMPL::ShowHeader('tracking', $page_heading, $hparams, $toolbar);

			JLMS_TMPL::OpenTS();
			$action_url = $is_full ? ($JLMS_CONFIG->get('live_site') . "/index.php?tmpl=component&amp;option=$option&amp;Itemid=$Itemid") : sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid");
	?>
			<form action="<?php echo $action_url;?>" method="post" name="adminForm">
					<table width="100%" cellpadding="0" cellspacing="0" border="0" id="TheTable" class="jlms_table_no_borders">
						<?php
						if(!$is_full){
						?>
						<tr>
							<td align="<?php echo $is_full ? "left" : "right";?>">
								<table <?php echo $is_full?'':'width="30%"'?> class="jlms_table_no_borders">
									<tr>
										<td valign="top" <?php echo $is_full?'width="400"':'width="40%"'?>>
											<table width="100%" border="0" class="jlms_table_no_borders">
												<tr>
													<td <?php echo $is_full?'width="100"':'width="20%"'?> nowrap="nowrap">
														Report Type:
													</td>
													<td>
														<?php
														echo JLMS_switchType($option);
														?>
													</td>
												</tr>
											</table>		
										</td>
									</tr>
								</table>	
							</td>
						</tr>
						<?php
						}
						?>
						<tr>
							<td align="left">
								<table <?php echo $is_full?'':'width="100%"'?> class="jlms_table_no_borders">
									<tr>
										<td valign="top" <?php echo $is_full?'width="400"':'width="40%"'?>>
											<table width="100%" border="0" class="jlms_table_no_borders">
												<?php
												if($is_full){
												?>
												<tr>
													<td <?php echo $is_full?'width="100"':'width="20%"'?> nowrap="nowrap" style="white-space: nowrap;">
														Report Type:
													</td>
													<td>
														<?php
														echo JLMS_switchType($option);
														?>
													</td>
												</tr>
												<?php
												}
												?>
												<tr>
													<td <?php echo $is_full?'width="100"':'width="20%"'?> nowrap="nowrap" style="white-space: nowrap;">
														<?php
														if ($JLMS_CONFIG->get('multicat_use', 0)){
															echo ((isset($levels[0]->cat_name) && $levels[0]->cat_name != '')?$levels[0]->cat_name:_JLMS_COURSES_COURSES_GROUPS);
														} else {
															echo _JLMS_COURSES_COURSES_GROUPS;
														}
														?>
													</td>
													<td>
														<?php
														if ($JLMS_CONFIG->get('multicat_use', 0)){
															echo $lists['filter_0'];
														} else {
															echo $lists['jlms_course_cats'];
														}
														?>
													</td>
												</tr>
												<?php
												if(count($multicat)){
													for($i=0;$i<count($multicat);$i++){
														if($i > 0){
															?>
															<tr>
																<td <?php echo $is_full?'width="100"':'width="20%"'?> nowrap="nowrap" style="white-space: nowrap;">
																	<?php
																		echo ((isset($levels[$i]->cat_name) && $levels[$i]->cat_name != '')?$levels[$i]->cat_name:_JLMS_COURSES_COURSES_GROUPS);
																	?>
																</td>
																<td>
																	<?php
																		echo $lists['filter_'.$i];
																	?>
																</td>
															</tr>
															<?php
														}
													}
												}
												?>
											</table>
										</td>
										<td valign="top" <?php echo $is_full?'width="400"':'width="40%"'?>>
											<table width="100%" border="0" class="jlms_table_no_borders">
												<?php
												if($is_full){
													$x_colsapn = '';
													if($JLMS_CONFIG->get('use_global_groups', 1)){
														$x_colsapn = 'colspan="2"';
													}
												?>
												<tr>
													<td <?php echo $x_colsapn;?>>
														&nbsp;												
													</td>
												</tr>
												<?php 
												}
												$x_colsapn = '';
												if($JLMS_CONFIG->get('use_global_groups', 1)){?>
												<tr>
													<td <?php echo $is_full?'width="100"':'width="20%"'?> nowrap="nowrap" style="padding-left: 5px;">
														<?php echo _JLMS_USER_GROUP_INFO;?>
													</td>
													<td <?php echo $is_full?'width="100%"':''?>>
														<?php  echo $lists['filter'];?>
													</td>
												</tr>
												<?php 
												$x_colsapn = 'colspan="2"';
												} 
												?>
												<tr>
													<td <?php echo $x_colsapn;?>>
														<table width="100%" border="0" class="jlms_table_no_borders">
															<tr>
																<td style="padding:0px 10px;" width="30">From</td>
																<td valign="middle" align="center">
																	<?php echo JLMS_HTML::_('calendar.calendar',$start_date,'pick_to','pick_to', null, null, 'statictext'); ?>
																</td>
																<td style="padding:0px 10px;" width="30">To</td>
																<td valign="middle" align="center">
																	<?php echo JLMS_HTML::_('calendar.calendar',$end_date,'pick','pick', null, null, 'statictext'); ?>					
																</td><td valign="middle" align="center" width="18" style="vertical-align:middle ">
																	<a href="javascript:pickup_date();" title="">
																		<img class="JLMS_png" src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/agenda/loopnone.png" alt="" title="" border="0" width="16" height="16" />
																	</a>
																</td>
															</tr>
														</table>
													</td>
												</tr>
											</table>
										</td>
										<?php if(!$is_full) { ?>
										<td style="padding-left:15px;" align="right">
											<?php
											$link = $JLMS_CONFIG->get('live_site') . "/index.php?tmpl=component&amp;option=".$option."&amp;Itemid=$Itemid&amp;task=report_access&amp;is_full=1";
											if($filt_group){
												$link .= "&amp;filt_group=".$filt_group;	
											}
											?>
										
											<a href="<?php echo $link;?>" target="_blank" title="<?php echo _JLMS_FULL_VIEW_BUTTON;?>"><?php echo _JLMS_FULL_VIEW_BUTTON;?></a>
										</td>
										<?php } ?>
									</tr>
								</table>
							</td>
						</tr>
					</table>
					<?php if(isset($ti->title) && !$is_full){?>
					<table width="100%" class="jlms_table_no_borders">
						<tr>
							<td align="center">
								<?php echo JLMSCSS::h2($ti->title);?>
								<img src="<?php echo $JLMS_CONFIG->getCfg('live_site')."/".$JLMS_CONFIG->get('temp_folder', '')."/$ti->filename";?>" width="<?php echo $ti->width*2;?>" alt="<?php echo $ti->alt;?>" title="<?php echo $ti->alt;?>" border='0' />
							</td>
						</tr>
					</table>
					<?php }?>
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
						Please wait. <?php echo _JLMS_REPORTS_ACCESS;?> is loading.<br /> If this message stays for over 1 minute, please click <a target="_blank" href="<?php echo $link;?>">&lt;here&gt;</a> to open <?php echo _JLMS_REPORTS_ACCESS;?> in new window.
					</div>
					<div id="vw_div" style="overflow: auto; width: 200px; height: auto; display: none;">
					<?php } ?>
					<table width="100%" cellpadding="<?php echo $is_full ? '4' : '0';?>" cellspacing="0" border="0" class="<?php echo JLMSCSS::_('jlmslist', 'jlms_report_fullview_table');?>"<?php echo (!$is_full) ? ' style="margin-bottom:0px;padding-bottom:0px;"' : '';?>>
						
							<?php
								$courses_str = implode(',',$courses);
								$JLMS_DB->setQuery('SELECT course_name FROM #__lms_courses WHERE id IN('.$courses_str.') Order By course_name,id');
								$crs_name = $JLMS_DB->loadResultArray();
								$cut_for = $JLMS_CONFIG->get('cutoff_reports_coursename',0);
								
								echo '<tr>';
								echo '<'.JLMSCSS::tableheadertag().' class="'.JLMSCSS::_('sectiontableheader').'" style="text-align:center; white-space:nowrap">'._JLMS_OU_USER.'</'.JLMSCSS::tableheadertag().'>';
								foreach($crs_name as $c_name){
									if(!$is_full){
										if($cut_for){	
											if(strlen($c_name) > $cut_for){
												$c_name = jlms_string_substr($c_name,0,$cut_for)."...";
											}
										}
									}
									echo '<'.JLMSCSS::tableheadertag().' class="'.JLMSCSS::_('sectiontableheader').'" style="text-align:center;">'.$c_name.'</'.JLMSCSS::tableheadertag().'>';
								}
								echo '</tr>';
								$zzz = 0;
								foreach($users as $usr_id){
									$JLMS_DB->setQuery('SELECT name FROM #__users WHERE id ='.$usr_id);
									$usrname = $JLMS_DB->LoadResult();
									$course_hits = 0;
									echo '<tr class="'.JLMSCSS::_('sectiontableentry'.($zzz%2 + 1)).'">';
									$linka = ampReplace(sefRelToAbs(($is_full?"index.php?tmpl=component&":"index.php?").'option='.$option.'&amp;Itemid='.$Itemid.'&amp;task=report_grade&amp;filt_user='.$usr_id.'&amp;is_full='.$is_full));
									echo '<td nowrap="nowrap"><a href="'.$linka.'">'.$usrname.'</a></td>';
									
									foreach($courses as $course_id){
										 $hit_num = 0; //by Max (move with line 349 (fix)
										 for($i=0;$i<count($hits);$i++){
										 	if($hits[$i]->c_id == $course_id && $hits[$i]->usr_id == $usr_id){
												$hit_num = $hits[$i]->hits;
												break;// by DEN
											}
											
										 }
										 echo '<td align="center">'.$hit_num.'</td>';
									}
									$course_hits += $hit_num;
									echo '</tr>';
									$zzz++;
								}
								if($pageNav->limitstart + $pageNav->limit >= $pageNav->total || $is_full){
								echo '<tr class="'.JLMSCSS::_('sectiontableentry'.($zzz%2 + 1)).'"><td>'._JLMS_REPORTS_TOTAL_ROW.'</td>';
								foreach($courses as $course_id){
								$thits_tot = 0;	
									foreach($tot_hits as $th){
										if($th->c_id == $course_id)
										$thits_tot = $th->hits;	
										
									}
									echo '<td align="center">'.$thits_tot.'</td>';
								}
								echo '</tr>';
								}
							?>
						
					</table>
					
					<?php 
						if(!$is_full){
						?>
					</div>
					<table width="100%" cellpadding="0" cellspacing="0" border="0" class="<?php echo JLMSCSS::_('jlmslist');?>" style="margin-top: 0px; padding-top:0px;">
						<tr>
							<td align="center" style="text-align:left; padding-top:10px;" class="<?php echo JLMSCSS::_('jlmslist-footer_td');?>">
								<div align="center" style="white-space:nowrap ">
								<?php
									$link = "index.php?option=$option&amp;Itemid=$Itemid&amp;task=report_access".($start_date?"&amp;start_date=".$start_date:"").($end_date?"&amp;end_date=".$end_date:"")."&amp;filt_group=$filt_group&amp;filt_cat=$filt_cat";
									echo _JLMS_PN_DISPLAY_NUM . $pageNav->getLimitBox( $link ) . '&nbsp;' . $pageNav->getPagesCounter();
									echo '<br />';
									echo $pageNav->writePagesLinks( $link );
								?> 
								</div>
							</td>
						</tr>
					</table>
					<?php if($JLMS_CONFIG->get('new_lms_features', 1)){
						$controls = array();
						$controls[] = array('href' => "javascript:submitFormView('csv');", 'title' => 'CSV', 'img' => 'csv');
						$controls[] = array('href' => "javascript:submitFormView('xls');", 'title' => 'XLS', 'img' => 'xls');
						JLMS_TMPL::ShowControlsFooterC($controls, '', false, false, _JLMS_EXPORT_TO.':');
					}?>
					<?php } ?>
					<input type="hidden" name="option" value="<?php echo $option;?>" />
					<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
					<input type="hidden" name="task" value="report_access" />
					<input type="hidden" name="start_date" value="<?php echo $start_date;?>" />
					<input type="hidden" name="end_date" value="<?php echo $start_date;?>" />
					<input type="hidden" name="view" value="" />
					<input type="hidden" name="is_full" value="<?php echo $is_full?>" />
			</form>
			
			<?php
				$action_url = $is_full ? ($JLMS_CONFIG->get('live_site') . "/index.php?tmpl=component&option=$option&amp;Itemid=$Itemid") : sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid");
			?>

			<form action="<?php echo $action_url;?>" method="post" name="adminFormCsv">
					<input type="hidden" name="option" value="<?php echo $option;?>" />
					<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
					<input type="hidden" name="task" value="report_access" />
					<input type="hidden" name="is_full" value="1" />
					<input type="hidden" name="view" value="" />
			</form>
		<?php
		JLMS_TMPL::CloseTS();
		JLMS_TMPL::CloseMT();			
	}

	function JLMS_sreportCertif( $hits, $users, $courses, $pageNav, $lists, $levels, $filt_cat, $filt_group, $option, $is_full){
		$JLMS_CONFIG = & JLMSFactory::getConfig();
		global $JLMS_DB;
		$Itemid = $JLMS_CONFIG->get('Itemid');
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
	
	<?php		
			JLMS_TMPL::OpenMT();
	
			if($is_full){
				$hparams = array('show_menu'=>false);
			}else{
				$hparams = array('simple_menu'=>true);
			}
			$toolbar = array();

			$page_heading = _JLMS_REPORTS_CONCLUSION;
			if ($JLMS_CONFIG->get('is_trial', false) && $JLMS_CONFIG->get('trial_reports_heading_text', '')) {
				$page_heading .= $JLMS_CONFIG->get('trial_reports_heading_text', '');
			}

			JLMS_TMPL::ShowHeader('tracking', $page_heading, $hparams, $toolbar);

			JLMS_TMPL::OpenTS();
	?>
	<script language="javascript" type="text/javascript">
	<!--//--><![CDATA[//><!--
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
	function submitFormView(view){
		var form = document.adminForm;
		form.view.value = view;
		form.task.value='report_certif';
		form.submit();
	}
	
	//--><!]]>
	</script>
	<?php
	$action_url = $is_full ? ($JLMS_CONFIG->get('live_site') . "/index.php?tmpl=component&amp;option=$option&amp;Itemid=$Itemid") : sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid");
	?>
			<form action="<?php echo $action_url;?>" method="post" name="adminForm">
					<table width="100%" cellpadding="0" cellspacing="0" border="0" id="TheTable" class="jlms_table_no_borders">
						<?php
						if(!$is_full){
						?>
						<tr>
							<td align="<?php echo $is_full ? "left" : "right";?>">
								<table <?php echo $is_full?'':'width="30%"'?> class="jlms_table_no_borders">
									<tr>
										<td valign="top" <?php echo $is_full?'width="400"':'width="40%"'?>>
											<table width="100%" border="0" class="jlms_table_no_borders">
												<tr>
													<td <?php echo $is_full?'width="100"':'width="20%"'?> nowrap="nowrap">
														Report Type:
													</td>
													<td>
														<?php
														echo JLMS_switchType($option);
														?>
													</td>
												</tr>
											</table>		
										</td>
									</tr>
								</table>	
							</td>
						</tr>
						<?php
						}
						?>
						<tr>	
							<td align="left">
								<table <?php echo $is_full?'':'width="100%"'?> class="jlms_table_no_borders">
									<tr>
										<td valign="top" <?php echo $is_full?'width="400"':'width="40%"'?>>
											<table width="100%" border="0" class="jlms_table_no_borders">
												<?php
												if($is_full){
												?>
												<tr>
													<td <?php echo $is_full?'width="100"':'width="20%"'?> nowrap="nowrap">
														Report Type:
													</td>
													<td>
														<?php
														echo JLMS_switchType($option);
														?>
													</td>
												</tr>
												<?php
												}
												?>
												<tr>
													<td <?php echo $is_full?'width="100"':'width="20%"'?> nowrap="nowrap">
														<?php
														if ($JLMS_CONFIG->get('multicat_use', 0)){
															echo ((isset($levels[0]->cat_name) && $levels[0]->cat_name != '')?$levels[0]->cat_name:_JLMS_COURSES_COURSES_GROUPS);
														} else {
															echo _JLMS_COURSES_COURSES_GROUPS;
														}
														?>
													</td>
													<td>
														<?php
														if ($JLMS_CONFIG->get('multicat_use', 0)){
															echo $lists['filter_0'];
														} else {
															echo $lists['jlms_course_cats'];
														}
														?>
													</td>
												</tr>
												<?php
												if(count($multicat)){
													for($i=0;$i<count($multicat);$i++){
														if($i > 0){
															?>
															<tr>
																<td <?php echo $is_full?'width="100"':'width="20%"'?> nowrap="nowrap">
																	<?php
																		echo ((isset($levels[$i]->cat_name) && $levels[$i]->cat_name != '')?$levels[$i]->cat_name:_JLMS_COURSES_COURSES_GROUPS);
																	?>
																</td>
																<td>
																	<?php
																		echo $lists['filter_'.$i];
																	?>
																</td>
															</tr>
															<?php
														}
													}
												}
												?>
											</table>
										</td>
										
										<td valign="top" <?php echo $is_full?'width="400"':'width="40%"'?>>
										<?php if($JLMS_CONFIG->get('use_global_groups', 1)){?>
											<table width="100%" border="0" cellpadding="0" cellspacing="0" class="jlms_table_no_borders">
												<?php
												if($is_full){
												?>
												<tr>
													<td colspan="2">
														&nbsp;
													</td>
												</tr>
												<?php
												}
												?>
												<tr>
													<td style="padding-left: 5px;" <?php echo $is_full?'width="100"':'width="20%"'?> nowrap="nowrap">
														<?php echo _JLMS_USER_GROUP_INFO;?>
													</td>
													<td>
														<?php  echo $lists['filter'];?>
													</td>
												</tr>
											</table>
										<?php } ?>
										</td>
										
										<?php if(!$is_full) { ?>
										<td style="padding-left:15px;" align="right" class="jlms_table_no_borders">
											<?php
											$link = $JLMS_CONFIG->get('live_site') . "/index.php?tmpl=component&amp;option=".$option."&amp;Itemid=$Itemid&amp;task=report_certif&amp;is_full=1";
											if($filt_group){
												$link .= "&amp;filt_group=".$filt_group;	
											}
											?>
										
											<a href="<?php echo $link;?>" target="_blank" title="<?php echo _JLMS_FULL_VIEW_BUTTON;?>"><?php echo _JLMS_FULL_VIEW_BUTTON;?>]</a>
										</td>
										<?php } ?>
									</tr>
								</table>		
							</td>
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
						Please wait. <?php echo _JLMS_REPORTS_CONCLUSION;?> is loading.<br /> If this message stays for over 1 minute, please click <a target="_blank" href="<?php echo $link;?>">&lt;here&gt;</a> to open <?php echo _JLMS_REPORTS_CONCLUSION;?> in new window.
					</div>
					<div id="vw_div" style="overflow: auto; width: 200px; height: auto; display: none;">
					<?php } ?>
					<table width="100%" cellpadding="<?php echo $is_full ? '4' : '0';?>" cellspacing="0" border="0" class="<?php echo JLMSCSS::_('jlmslist', 'jlms_report_fullview_table');?>" style="margin-bottom: 0px; padding-bottom:0px;">
					<?php
						$courses_str = implode(',',$courses);
						$users_str = implode(',',$users);
						$JLMS_DB->setQuery('SELECT course_name FROM #__lms_courses WHERE id IN('.$courses_str.') ORDER BY course_name');
						$crs_name = $JLMS_DB->loadResultArray();
						
						$JLMS_DB->setQuery('SELECT * FROM #__lms_courses WHERE id IN('.$courses_str.') ORDER BY course_name');
						$crs_options = $JLMS_DB->loadObjectList();
						
						$cut_for = $JLMS_CONFIG->get('cutoff_reports_coursename',0);
						
						echo '<tr>';
						echo '<'.JLMSCSS::tableheadertag().' class="'.JLMSCSS::_('sectiontableheader').'" style="text-align:center; white-space:nowrap">'._JLMS_OU_USER.'</'.JLMSCSS::tableheadertag().'>';
						foreach($crs_name as $key=>$c_name){
							if(!$is_full){
								if($cut_for){	
									if(strlen($c_name) > $cut_for){
										$c_name = jlms_string_substr($c_name,0,$cut_for)."...";
									}
								}
							}
							$view_is_course = 1;
							if($JLMS_CONFIG->get('flms_integration', 1)){
								$params = new JLMSParameters($crs_options[$key]->params);
								$view_is_course = $params->get('show_in_report', 1);	
							}
							if($view_is_course){
								echo '<'.JLMSCSS::tableheadertag().' class="'.JLMSCSS::_('sectiontableheader').'" style="text-align:center;">'.$c_name.'</'.JLMSCSS::tableheadertag().'>';
							}
						}
						echo '</tr>';
						$zzz = 0;
						foreach($users as $usr_id){
							$JLMS_DB->setQuery('SELECT name FROM #__users WHERE id ='.$usr_id);
							$usrname = $JLMS_DB->LoadResult();
							$course_hits = 0;
							echo '<tr class="'.JLMSCSS::_('sectiontableentry'.($zzz%2 + 1)).'">';
							$linka = sefRelToAbs(($is_full?"index.php?tmpl=component&":"index.php?").'option='.$option.'&amp;Itemid='.$Itemid.'&amp;task=report_grade&amp;filt_group='.$filt_group.'&amp;filt_user='.$usr_id.'&amp;is_full='.$is_full);
							echo '<td nowrap="nowrap"><a href="'.$linka.'">'.$usrname.'</a></td>';
							foreach($courses as $key=>$course_id){
								$hit_num = _JLMS_NO_ALT_TITLE;
								for($i=0;$i<count($hits);$i++){
									if($hits[$i]->c_id == $course_id && $hits[$i]->usr_id == $usr_id){
										$hit_num = _JLMS_YES_ALT_TITLE;
										break;// by DEN
									}
								}
								$view_is_course = 1;
								if($JLMS_CONFIG->get('flms_integration', 1)){
									$params = new JLMSParameters($crs_options[$key]->params);
									$view_is_course = $params->get('show_in_report', 1);	
								}
								if($view_is_course){
									echo '<td align="center">'.$hit_num.'</td>';
								}
							}
							echo '</tr>';
							$zzz++;
						}
					?>
					</table>
					<?php
						if ($is_full) {
							$controls = array();
							$controls[] = array('href' => "javascript:submitFormView('csv');", 'title' => 'CSV', 'img' => 'csv');
							$controls[] = array('href' => "javascript:submitFormView('xls');", 'title' => 'XLS', 'img' => 'xls');
							JLMS_TMPL::ShowControlsFooterC($controls, '', false, false, _JLMS_EXPORT_TO.':', true);
						} else {
					?>	
					</div>
					<table width="100%" cellpadding="0" cellspacing="0" border="0" class="<?php echo JLMSCSS::_('jlmslist');?>" style="margin-top:0px; padding-top:0px; margin-bottom:0px;">
						<tr>
							<td align="center" style="text-align:center;" class="<?php echo JLMSCSS::_('jlmslist-footer_td');?>">
								<div align="center" style="white-space:nowrap ">
								<?php
									$link = "index.php?option=$option&amp;Itemid=$Itemid&amp;task=report_certif&amp;filt_group=$filt_group&amp;filt_cat=$filt_cat";
									echo _JLMS_PN_DISPLAY_NUM . $pageNav->getLimitBox( $link ) . '&nbsp;' . $pageNav->getPagesCounter();;
									echo '<br />';
									echo $pageNav->writePagesLinks( $link );
								?> 
								</div>
							</td>
						</tr>
					</table>
						<?php if($JLMS_CONFIG->get('new_lms_features', 1)){
							$controls = array();
							$controls[] = array('href' => "javascript:submitFormView('csv');", 'title' => 'CSV', 'img' => 'csv');
							$controls[] = array('href' => "javascript:submitFormView('xls');", 'title' => 'XLS', 'img' => 'xls');
							JLMS_TMPL::ShowControlsFooterC($controls, '', false, false, _JLMS_EXPORT_TO.':');
						}?>
					<?php }?>
					<input type="hidden" name="option" value="<?php echo $option;?>" />
					<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
					<input type="hidden" name="task" value="report_certif" />
					<input type="hidden" name="view" value="" />
					<input type="hidden" name="is_full" value="<?php echo $is_full?>" />
			</form>
			
			<?php
				$action_url = $is_full ? ($JLMS_CONFIG->get('live_site') . "/index.php?tmpl=component&amp;option=$option&amp;Itemid=$Itemid") : sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid");
			?>

			<form action="<?php echo $action_url;?>" method="post" name="adminFormCsv">
					<input type="hidden" name="option" value="<?php echo $option;?>" />
					<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
					<input type="hidden" name="task" value="report_certif" />
					<input type="hidden" name="is_full" value="1" />
					<input type="hidden" name="view" value="" />
			</form>
		<?php
		JLMS_TMPL::CloseTS();
		JLMS_TMPL::CloseMT();			
	}		

	function JLMS_sreportGrade( $option, &$rows, &$pageNav, &$lists, $levels, $filt_group, $filt_cat, $filt_user, $is_full ) {
		$JLMS_CONFIG = & JLMSFactory::getConfig();
		$Itemid = $JLMS_CONFIG->get('Itemid');

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

		JLMS_TMPL::OpenMT();
			$lists['user_id'] = isset($lists['user_id'])?$lists['user_id']:0;
			if($is_full){
				$hparams = array('show_menu'=>false);
			}else{
				$hparams = array('simple_menu'=>true);
			}
			$toolbar = array();

			$page_heading = _JLMS_REPORTS_USER.' '.date("Y-m-d H:i:s");
			if ($JLMS_CONFIG->get('is_trial', false) && $JLMS_CONFIG->get('trial_reports_heading_text', '')) {
				$page_heading = _JLMS_REPORTS_USER.$JLMS_CONFIG->get('trial_reports_heading_text', '');
			}

			JLMS_TMPL::ShowHeader('tracking', $page_heading, $hparams, $toolbar);
		JLMS_TMPL::OpenTS();
	?>
	<script language="javascript" type="text/javascript">
	<!--//--><![CDATA[//><!--
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
			
			function submitFormView(view){
				var form = document.adminForm;
				form.view.value = view;
				form.task.value='report_grade';
				form.submit();
			}
	//--><!]]>
	</script>
	<?php
	$action_url = $is_full ? ($JLMS_CONFIG->get('live_site') . "/index.php?tmpl=component&option=$option&amp;Itemid=$Itemid") : sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid");
	?>
			<form action="<?php echo $action_url;?>" method="post" name="adminForm">
				<table cellpadding="0" cellspacing="0" border="0" id="TheTable" <?php echo $is_full?"":'width="100%"'?> class="jlms_table_no_borders">
					<?php
					if(!$is_full){
					?>
					<tr>
						<td align="<?php echo $is_full ? "left" : "right";?>" <?php echo !$is_full? 'colspan="4"': '';?>>
							<table <?php echo $is_full?'':'width="30%"'?> class="jlms_table_no_borders">
								<tr>
									<td valign="top" <?php echo $is_full?'width="400"':'width="40%"'?>>
										<table width="100%" border="0" class="jlms_table_no_borders">
											<tr>
												<td <?php echo $is_full?'width="100"':'width="20%"'?> nowrap="nowrap">
													Report Type:
												</td>
												<td>
													<?php
													echo JLMS_switchType($option);
													?>
												</td>
											</tr>
										</table>		
									</td>
								</tr>
							</table>	
						</td>
					</tr>
					<?php
					}
					?>
					<tr>
						<td align="left">
							<table <?php echo $is_full?'':'width="100%"'?> class="jlms_table_no_borders">
								<tr>
									<td valign="top" <?php echo $is_full?'width="400"':'width="40%"'?>>
										<table width="100%" border="0" class="jlms_table_no_borders">
											<?php
											if($is_full){
											?>
											<tr>
												<td <?php echo $is_full?'width="100"':'width="20%"'?> nowrap="nowrap">
													Report Type:
												</td>
												<td>
													<?php
													echo JLMS_switchType($option);
													?>
												</td>
											</tr>
											<?php
											}
											?>
											<tr>
												<td <?php echo $is_full?'width="100"':'width="20%"'?> nowrap="nowrap">
													<?php
													if ($JLMS_CONFIG->get('multicat_use', 0)){
														echo ((isset($levels[0]->cat_name) && $levels[0]->cat_name != '')?$levels[0]->cat_name:_JLMS_COURSES_COURSES_GROUPS);
													} else {
														echo _JLMS_COURSES_COURSES_GROUPS;
													}
													?>
												</td>
												<td>
													<?php
													if ($JLMS_CONFIG->get('multicat_use', 0)){
														echo $lists['filter_0'];
													} else {
														echo $lists['jlms_course_cats'];
													}
													?>
												</td>
											</tr>
											<?php
											if(count($multicat)){
												for($i=0;$i<count($multicat);$i++){
													if($i > 0){
														?>
														<tr>
															<td <?php echo $is_full?'width="100"':'width="20%"'?> nowrap="nowrap">
																<?php
																	echo ((isset($levels[$i]->cat_name) && $levels[$i]->cat_name != '')?$levels[$i]->cat_name:_JLMS_COURSES_COURSES_GROUPS);
																?>
															</td>
															<td>
																<?php
																	echo $lists['filter_'.$i];
																?>
															</td>
														</tr>
														<?php
													}
												}
											}
											?>
											<?php
											if($JLMS_CONFIG->get('flms_integration')){
												?>
												<tr>
													<td>
														Show "Test" only:
													</td>
													<td>
														<?php
														echo $lists['test_lesson'];
														?>
													</td>
												</tr>
												<?php
											}
											?>
										</table>
									</td>
									<td valign="top" <?php echo $is_full?'width="400"':'width="40%"'?>>
										<table width="100%" border="0" class="jlms_table_no_borders">
											<?php 
											if($is_full){
											?>
											<tr>
												<td colspan="2">
													&nbsp;
												</td>
											</tr>
											<?php
											}
											if($JLMS_CONFIG->get('use_global_groups', 1)){
											?>
											<tr>
												<td style="padding-left: 5px;" <?php echo $is_full?'width="100"':'width="20%"'?> nowrap="nowrap">
													<?php
													echo _JLMS_USER_GROUP_INFO;
													?>
												</td>
												<td>
													<?php
													echo $lists['filter'];
													?>
												</td>
											</tr>
											<?php
											}
											?>
											<tr>
												<td style="padding-left: 5px;" <?php echo $is_full?'width="100"':'width="20%"'?> nowrap="nowrap">
													<?php
													echo _JLMS_USERS_TBL_HEAD_USERNAME.':';
													?>
												</td>
												<td>
													<?php
													echo $lists['jlms_filt_user'];
													?>
												</td>
											</tr>
										</table>
									</td>
									<?php if(!$is_full) { ?>
									<td style="padding-left:15px;" align="right">
										<?php
										$link = $JLMS_CONFIG->get('live_site') . "/index.php?tmpl=component&amp;option=".$option."&amp;Itemid=$Itemid&amp;task=report_grade&amp;is_full=1";
										if($filt_group){
											$link .= "&amp;filt_group=".$filt_group;
										}
										if($filt_user){
											$link .= "&amp;filt_user=".$filt_user;	
										}
										?>
										<a href="<?php echo $link;?>" target="_blank" title="<?php echo _JLMS_FULL_VIEW_BUTTON;?>"><?php echo _JLMS_FULL_VIEW_BUTTON;?></a>
									</td>
									<?php } ?>
								</tr>
							</table>			
						</td>
					</tr>
				</table>
			
				<?php
				$max_row = 0;
				$latest_max_row = 0;
				for ($i=0, $n=count($rows); $i < $n; $i++) {
						$sc_rows = array();
						if(count($lists['sc_rows'][$i])){
							$j=0;
							foreach ($lists['sc_rows'][$i] as $sc_row) {
								if ($sc_row->show_in_gradebook) {
									$sc_rows[$j] = $sc_row;
									$j++;
								}
							}	
						}
						$latest_max_row = $latest_max_row + count($sc_rows);
						$latest_max_row = $latest_max_row + count($lists['quiz_rows'][$i]);
						$latest_max_row = $latest_max_row + count($lists['gb_rows'][$i]);
					if($latest_max_row > $max_row){
						$max_row = $latest_max_row;	
					}
				}
				
				if(!$is_full){
					?>
					<div id="pre_div" style="display: block; width: 100%; text-align: center;">
						<br />
						Please wait. <?php echo _JLMS_REPORTS_USER;?> is loading.<br /> If this message stays for over 1 minute, please click <a target="_blank" href="<?php echo $link;?>">&lt;here&gt;</a> to open <?php echo _JLMS_REPORTS_USER;?> in new window.
					</div>
					<?php
					$domready = '
					$(\'pre_div\').setStyles({\'display\': \'none\'});
					';
					$JLMS_CONFIG->set('web20_domready_code', $JLMS_CONFIG->get('web20_domready_code','').$domready);
				}
				for ($i=0, $n=count($rows); $i < $n; $i++) {
						$row = $rows[$i];
				?>
					<?php 
					if(!$is_full){
						$domready = '
						$(\'vw_div_'.$i.'\').setStyles({\'display\': \'block\'});
						var cur_height = $(\'vw_div_'.$i.'\').getStyle(\'height\').toInt() + 18;
						$(\'vw_div_'.$i.'\').setStyles({\'width\': $(\'TheTable\').offsetWidth+\'px\', \'height\': cur_height+\'px\'});
						';
						$JLMS_CONFIG->set('web20_domready_code', $JLMS_CONFIG->get('web20_domready_code','').$domready);
						?>
						<div id="vw_div_<?php echo $i?>" style="overflow: auto; width: 200px; height: auto; display: none;">
					<?php 
					}
					?>
					<br />
					<?php echo JLMSCSS::h2($row->course_name);?>
					<div ><?php echo _JLMS_REPORTS_ACCESSED_TIMES." ".$lists['hits'][$i]?></div>
					
					<table cellpadding="0" cellspacing="0" border="0" width="100%" class="<?php echo JLMSCSS::_('jlmslist');?>" style="margin-bottom:0px; padding-bottom:0px;">
						<tr>
							<<?php echo JLMSCSS::tableheadertag();?> width="120" class="<?php echo JLMSCSS::_('sectiontableheader');?>" style="text-align:center; white-space:nowrap">
								<div style="width: 120px; text-align: center;">
								<?php  echo _JLMS_REPORTS_CONCLUSION_ROW;?>
								</div>
							</<?php echo JLMSCSS::tableheadertag();?>>
							<?php
							$sc_num = 0;
							$i_row = 0;
							foreach ($lists['sc_rows'][$i] as $sc_row) {
								if ($sc_row->show_in_gradebook) {
									$sc_num++;
									echo '<'.JLMSCSS::tableheadertag().' align=\'center\' nowrap=\'nowrap\' class="'.JLMSCSS::_('sectiontableheader').'" style="text-align:center; white-space:nowrap">'.JLMS_reports_html::Echo_tbl_header($sc_row->lpath_name).'</'.JLMSCSS::tableheadertag().'>';
									$i_row++;
								}
							}
							foreach ($lists['quiz_rows'][$i] as $quiz_row) {
								echo '<'.JLMSCSS::tableheadertag().' align=\'center\' nowrap=\'nowrap\' class="'.JLMSCSS::_('sectiontableheader').'" style="text-align:center; white-space:nowrap">'.JLMS_reports_html::Echo_tbl_header($quiz_row->c_title).'</'.JLMSCSS::tableheadertag().'>';
								$i_row++;
							}
							foreach ($lists['gb_rows'][$i] as $gb_row) {
								echo '<'.JLMSCSS::tableheadertag().' align=\'center\' nowrap=\'nowrap\' class="'.JLMSCSS::_('sectiontableheader').'" style="text-align:center; white-space:nowrap">'.JLMS_reports_html::Echo_tbl_header($gb_row->gbi_name).'</'.JLMSCSS::tableheadertag().'>';
								$i_row++;
							}
							if(!$i_row){
								echo '<'.JLMSCSS::tableheadertag().' align=\'center\' nowrap=\'nowrap\' class="'.JLMSCSS::_('sectiontableheader').'" style="text-align:center; white-space:nowrap">&nbsp;</'.JLMSCSS::tableheadertag().'>';
							}
							?>
						</tr>
					<?php
					$k = 1;
					?>
						<tr class="<?php echo JLMSCSS::_('sectiontableentry'.$k); ?>">
							<td align="center" valign="middle" style="text-align: center; vertical-align: middle;">
								<div style="width: 120px; text-align: center;">
								<?php
								$image = $row->user_certificate ? 'btn_accept.png' : 'btn_cancel.png';
								$alt = '';
								$state =  $row->user_certificate ? 0 : 1;
								echo JLMS_reports_html::publishIcon($row->user_id, 0, $state, 'gb_crt', $alt, $image, $option );?>
								</div>
							</td>
							<?php
							if(!$i_row){
								echo '<td align=\'center\' style="text-align:center">&nbsp;</td>';
							}
							$sc_num2 = 0;
							foreach ($lists['sc_rows'][$i] as $sc_row) {
								if ($sc_row->show_in_gradebook) {	
									$j = 0;
									while ($j < count($row->scorm_info)) {
										if ($row->scorm_info[$j]->gbi_id == $sc_row->item_id) {
											if ($sc_num2 < $sc_num) {
												if ($row->scorm_info[$j]->user_status == -1) {
													echo '<td align=\'center\' style="text-align:center">-</td>';
												} else {
													$image = $row->scorm_info[$j]->user_status ? 'btn_accept.png' : 'btn_cancel.png';
													$alt = $row->scorm_info[$j]->user_status ? 'btn_accept' : 'btn_cancel';
													$alt .= '" align="top';
													$img = JLMS_reports_html::publishIcon(0, 0, 0, '', $alt, $image, $option, false );
			
													echo '<td style="vertical-align:middle; text-align:center" nowrap="nowrap">'.$img
													. '&nbsp;<strong>' . $row->scorm_info[$j]->user_grade . "</strong> (" . $row->scorm_info[$j]->user_pts.")"
													. '</td>';
												}
												$sc_num2++;
											}
										}
										$j ++;
									}
								}
							}

							foreach ($lists['quiz_rows'][$i] as $quiz_row) {
								$j = 0;
								while ($j < count($row->quiz_info)) {
									if ($row->quiz_info[$j]->gbi_id == $quiz_row->c_id) {
										if ($row->quiz_info[$j]->user_status == -1) {
											echo '<td align=\'center\' style="text-align:center">-</td>';
										} else {
//											$image = $row->quiz_info[$j]->user_status ? 'btn_accept.png' : 'btn_cancel.png';
//											$alt = $row->quiz_info[$j]->user_status ? 'btn_accept' : 'btn_cancel';
//											$alt .= '" align="top';
//											$img = JLMS_reports_html::publishIcon(0, 0, 0, '', $alt, $image, $option, false );
//		
//											echo '<td style="vertical-align:middle; text-align:center" nowrap="nowrap">'.$img
//											. '&nbsp;<strong>' . $row->quiz_info[$j]->user_grade . "</strong> (" . $row->quiz_info[$j]->user_pts_full .")"
//											. '</td>';
											
											echo '<td style="vertical-align:middle; text-align:center" nowrap="nowrap">';
												echo JLMS_showQuizStatus($row->quiz_info[$j], 50);
											echo '</td>';
										}
									}
									$j ++;
								}
							}
							$j = 0;
							while ($j < count($row->grade_info)) {
								echo '<td align=\'center\' valign="middle" style="vertical-align:middle;text-align:center "><strong>'
								. $row->grade_info[$j]->user_grade
								. '</strong></td>';
								$j ++;
							}
							?>
						</tr>
						
					</table>
				<?php if(!$is_full){ ?>
				</div>
				<?php }
					$k = 3 - $k;
				} 

				if(!$lists['user_id']){
					echo '<div class="joomlalms_page_tip"  style="text-align:center">'._JLMS_REPORTS_SELECT_USER.'</div>';
				}

				if($lists['user_id'] && !$is_full){?>
				<table width="100%" cellpadding="0" cellspacing="0" border="0" class="<?php echo JLMSCSS::_('jlmslist');?>" style="margin-bottom:0px;">
					<tr>
						<td align="center" style="text-align:left; padding-top:0px; margin-top:0px; margin-bottom:0px;" class="<?php echo JLMSCSS::_('jlmslist-footer_td');?>">
							<div align="center" style="white-space:nowrap">
							<?php
								$link = "index.php?option=$option&amp;Itemid=$Itemid&amp;task=report_grade&amp;filt_group=$filt_group&amp;filt_cat=$filt_cat&amp;filt_user=".$lists['user_id'];
								if($JLMS_CONFIG->get('flms_integration')){
									$link .= $lists['test_lesson_value'] ? "&amp;test_lesson=".$lists['test_lesson_value'] : '';
								}
								echo _JLMS_PN_DISPLAY_NUM . $pageNav->getLimitBox( $link ) . '&nbsp;' . $pageNav->getPagesCounter();
								echo '<br />';
								if($JLMS_CONFIG->get('flms_integration')){
									$link .= $lists['test_lesson_value'] ? "&amp;test_lesson=".$lists['test_lesson_value'] : '';
								}
								echo $pageNav->writePagesLinks( $link );
							?> 
							</div>
						</td>
					</tr>
				</table>
				<?php }

				if($lists['user_id']){
					if($JLMS_CONFIG->get('new_lms_features', 1)){
						$controls = array();
						$controls[] = array('href' => "javascript:submitFormView('xls');", 'title' => 'XLS', 'img' => 'xls');
						JLMS_TMPL::ShowControlsFooterC($controls, '', false, false, _JLMS_EXPORT_TO.':');
					}
				}
				?>
				<input type="hidden" name="option" value="<?php echo $option;?>" />
				<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
				<input type="hidden" name="task" value="report_grade" />
				<input type="hidden" name="view" value="" />
				<input type="hidden" name="state" value="0" />
				<input type="hidden" name="boxchecked" value="0" />
				<input type="hidden" name="is_full" value="<?php echo $is_full?>" />
			</form>
			
			<?php
				$action_url = $is_full ? ($JLMS_CONFIG->get('live_site') . "/index.php?tmpl=component&option=$option&amp;Itemid=$Itemid") : sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid");
			?>

			<form action="<?php echo $action_url;?>" method="post" name="adminFormCsv">
					<input type="hidden" name="option" value="<?php echo $option;?>" />
					<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
					<input type="hidden" name="task" value="report_grade" />
					<input type="hidden" name="is_full" value="1" />
					<input type="hidden" name="view" value="" />
			</form>	
		<?php
		JLMS_TMPL::CloseTS();
		JLMS_TMPL::CloseMT();
	}	
		
	function JLMS_sreportGradeFV( $option, &$rows, &$pageNav, &$lists, $levels, $filt_group, $filt_cat, $user_id, $is_full ) {
		$JLMS_CONFIG = & JLMSFactory::getConfig();
		$Itemid = $JLMS_CONFIG->get('Itemid');

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

		JLMS_TMPL::OpenMT();
			$lists['user_id'] = isset($lists['user_id'])?$lists['user_id']:0;
			if($is_full){
				$hparams = array('show_menu'=>false);
			}else{
				$hparams = array('simple_menu'=>true);
			}
			$toolbar = array();
			$page_heading = _JLMS_REPORTS_USER.' '.date("Y-m-d H:i:s");
			if ($JLMS_CONFIG->get('is_trial', false) && $JLMS_CONFIG->get('trial_reports_heading_text', '')) {
				$page_heading = _JLMS_REPORTS_USER.$JLMS_CONFIG->get('trial_reports_heading_text', '');
			}
			JLMS_TMPL::ShowHeader('tracking', $page_heading, $hparams, $toolbar);
		JLMS_TMPL::OpenTS();
	?>
	<script language="javascript" type="text/javascript">
	<!--//--><![CDATA[//><!--
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
			function submitFormView(){
				var form = document.adminForm;
			//	form.view.value = view;
				form.task.value='report_grade';
				form.submit();
			}
	//--><!]]>
	</script>
	<?php
		$action_url = $is_full ? ($JLMS_CONFIG->get('live_site') . "/index.php?tmpl=component&amp;option=$option&amp;Itemid=$Itemid") : sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid");
	?>
			<form action="<?php echo $action_url;?>" method="post" name="adminForm">
			
				<table cellpadding="0" cellspacing="0" border="0" id="TheTable" <?php echo $is_full?"":'width="100%"'?> class="jlms_table_no_borders">
					<?php
					if(!$is_full){
					?>
					<tr>
						<td align="<?php echo $is_full ? "left" : "right";?>" <?php echo !$is_full? 'colspan="2"': '';?>>
							<table <?php echo $is_full?'':'width="30%"'?> class="jlms_table_no_borders">
								<tr>
									<td valign="top" <?php echo $is_full?'width="400"':'width="40%"'?>>
										<table width="100%" border="0" class="jlms_table_no_borders">
											<tr>
												<td <?php echo $is_full?'width="100"':'width="20%"'?> nowrap="nowrap">
													Report Type:
												</td>
												<td>
													<?php
													echo JLMS_switchType($option);
													?>
												</td>
											</tr>
										</table>		
									</td>
								</tr>
							</table>	
						</td>
					</tr>
					<?php
					}
					?>
					<tr>
						<td align="left">
							<table <?php echo $is_full?'':'width="100%"'?> class="jlms_table_no_borders">
								<tr>
									<td valign="top" <?php echo $is_full?'width="400"':'width="40%"'?>>
										<table width="100%" border="0" class="jlms_table_no_borders">
											<?php
											if($is_full){
											?>
											<tr>
												<td <?php echo $is_full?'width="100"':'width="20%"'?> nowrap="nowrap">
													Report Type:
												</td>
												<td>
													<?php
													echo JLMS_switchType($option);
													?>
												</td>
											</tr>
											<?php
											}
											?>
											<tr>
												<td <?php echo $is_full?'width="100"':'width="20%"'?> nowrap="nowrap">
													<?php
													if ($JLMS_CONFIG->get('multicat_use', 0)){
														echo ((isset($levels[0]->cat_name) && $levels[0]->cat_name != '')?$levels[0]->cat_name:_JLMS_COURSES_COURSES_GROUPS);
													} else {
														echo _JLMS_COURSES_COURSES_GROUPS;
													}
													?>
												</td>
												<td>
													<?php
													if ($JLMS_CONFIG->get('multicat_use', 0)){
														echo $lists['filter_0'];
													} else {
														echo $lists['jlms_course_cats'];
													}
													?>
												</td>
											</tr>
											<?php
											if(count($multicat)){
												for($i=0;$i<count($multicat);$i++){
													if($i > 0){
														?>
														<tr>
															<td <?php echo $is_full?'width="100"':'width="20%"'?> nowrap="nowrap">
																<?php
																	echo ((isset($levels[$i]->cat_name) && $levels[$i]->cat_name != '')?$levels[$i]->cat_name:_JLMS_COURSES_COURSES_GROUPS);
																?>
															</td>
															<td>
																<?php
																	echo $lists['filter_'.$i];
																?>
															</td>
														</tr>
														<?php
													}
												}
											}
											?>
										</table>
									</td>
									<td valign="top" <?php echo $is_full?'width="400"':'width="40%"'?>>
										<table width="100%" border="0" class="jlms_table_no_borders">
											<?php
											if($is_full){
											?>
											<tr>
												<td colspan="2">
													&nbsp;
												</td>
											</tr>
											<?php
											}
											if($JLMS_CONFIG->get('use_global_groups', 1)){
											?>
											<tr>
												<td <?php echo $is_full?'width="100"':'width="20%"'?> nowrap="nowrap" style="padding-left: 5px;">
													<?php
													echo _JLMS_USER_GROUP_INFO;
													?>
												</td>
												<td>
													<?php
													echo $lists['filter'];
													?>
												</td>
											</tr>
											<?php
											}
											?>
											<tr>
												<td <?php echo $is_full?'width="100"':'width="20%"'?> nowrap="nowrap" style="padding-left: 5px;">
													<?php
													echo _JLMS_USERS_TBL_HEAD_USERNAME.':';
													?>
												</td>
												<td>
													<?php
													echo $lists['jlms_filt_user'];
													?>
												</td>
											</tr>
										</table>
									</td>
									<?php if(!$is_full) { ?>
									<td style="padding-left:15px;" align="right">
										<?php
										$link = $JLMS_CONFIG->get('live_site') . "/index.php?tmpl=component&option=".$option."&amp;Itemid=$Itemid&amp;task=report_grade&amp;is_full=1";
										if($filt_group){
											$link .= "&amp;filt_group=".$filt_group;
										}
										if($user_id){
											$link .= "&amp;filt_user=".$user_id;	
										}
										?>
										<a href="<?php echo $link;?>" target="_blank" title="<?php echo _JLMS_FULL_VIEW_BUTTON;?>"><?php echo _JLMS_FULL_VIEW_BUTTON;?></a>
									</td>
									<?php } ?>
								</tr>
							</table>
						</td>
					</tr>
				</table>

				<?php
				$max_row = 0;
				for ($i=0, $n=count($rows); $i < $n; $i++) {
						$latest_max_row = 0;
						$sc_rows = array();
						if(count($lists['sc_rows'][$i])){
							$j=0;
							foreach ($lists['sc_rows'][$i] as $sc_row) {
								if ($sc_row->show_in_gradebook) {
									$sc_rows[$j] = $sc_row;
									$j++;
								}
							}	
						}
						$latest_max_row = $latest_max_row + count($sc_rows);
						$latest_max_row = $latest_max_row + count($lists['quiz_rows'][$i]);
						$latest_max_row = $latest_max_row + count($lists['gb_rows'][$i]);
					if($latest_max_row > $max_row){
						$max_row = $latest_max_row;	
					}
				}
				?>

				<table cellpadding="0" cellspacing="0" border="0" width="100%" class="<?php echo JLMSCSS::_('jlmslist');?>">
				<?php
				for ($i=0, $n=count($rows); $i < $n; $i++) {
						$row = $rows[$i];
					?>
						<tr style="border: 0px none;">
							<td colspan="<?php echo ($max_row + 1);?>" style="border: 0px none;">
							<br />
								<?php echo JLMSCSS::h2($row->course_name);?>
								<span class="small"><?php echo _JLMS_REPORTS_ACCESSED_TIMES." ".$lists['hits'][$i]?></span>
							</td>
						</tr>
						<tr class="jlms_report_fullview_row">
							<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader', 'first_td');?>" style="text-align:center; white-space:nowrap"><?php  echo _JLMS_REPORTS_CONCLUSION_ROW;?></<?php echo JLMSCSS::tableheadertag();?>>
							<?php
							$sc_num = 0;
							$i_row = 0;
							foreach ($lists['sc_rows'][$i] as $sc_row) {
								if ($sc_row->show_in_gradebook) {
									$sc_num++;
									echo '<'.JLMSCSS::tableheadertag().' align=\'center\' nowrap=\'nowrap\' class="'.JLMSCSS::_('sectiontableheader').'" style="text-align:center; white-space:nowrap">'.JLMS_reports_html::Echo_tbl_header($sc_row->lpath_name).'</'.JLMSCSS::tableheadertag().'>';
									$i_row++;
								}
							}
							foreach ($lists['quiz_rows'][$i] as $quiz_row) {
								echo '<'.JLMSCSS::tableheadertag().' align=\'center\' nowrap=\'nowrap\' class="'.JLMSCSS::_('sectiontableheader').'" style="text-align:center; white-space:nowrap">'.JLMS_reports_html::Echo_tbl_header($quiz_row->c_title).'</'.JLMSCSS::tableheadertag().'>';
								$i_row++;
							}
							foreach ($lists['gb_rows'][$i] as $gb_row) {
								echo '<'.JLMSCSS::tableheadertag().' align=\'center\' nowrap=\'nowrap\' class="'.JLMSCSS::_('sectiontableheader').'" style="text-align:center; white-space:nowrap">'.JLMS_reports_html::Echo_tbl_header($gb_row->gbi_name).'</'.JLMSCSS::tableheadertag().'>';
								$i_row++;
							}
							if($i_row < $max_row){
								for($jj=$i_row;$jj<$max_row;$jj++){
									echo '<'.JLMSCSS::tableheadertag().' align=\'center\' nowrap=\'nowrap\' class="'.JLMSCSS::_('sectiontableheader').'" style="text-align:center; white-space:nowrap">&nbsp;<!--x--></'.JLMSCSS::tableheadertag().'>';
								}
							}
							?>
						</tr>
					<?php
					$k = 1;
					?>
						<tr class="<?php echo JLMSCSS::_('sectiontableentry'.$k, 'jlms_report_fullview_row_bottom'); ?>">
							<td align="center" valign="middle" style=" text-align:center; vertical-align:middle" class="first_td">
								<?php
								$image = $row->user_certificate ? 'btn_accept.png' : 'btn_cancel.png';
								$alt = '';
								$state =  $row->user_certificate ? 0 : 1;
								echo JLMS_reports_html::publishIcon($row->user_id, 0, $state, 'gb_crt', $alt, $image, $option ); ?>
							</td>
							<?php
							$sc_num2 = 0;
							foreach ($lists['sc_rows'][$i] as $sc_row) {
								if ($sc_row->show_in_gradebook) {	
									$j = 0;
									while ($j < count($row->scorm_info)) {
										if ($row->scorm_info[$j]->gbi_id == $sc_row->item_id) {
											if ($sc_num2 < $sc_num) {
												if ($row->scorm_info[$j]->user_status == -1) {
													echo '<td align=\'center\' style="text-align:center">-</td>';
												} else {
													$image = $row->scorm_info[$j]->user_status ? 'btn_accept.png' : 'btn_cancel.png';
													$alt = $row->scorm_info[$j]->user_status ? 'btn_accept' : 'btn_cancel';
													$alt .= '" align="top';
													$img = JLMS_reports_html::publishIcon(0, 0, 0, '', $alt, $image, $option, false );
			
													echo '<td style="vertical-align:middle; text-align:center" nowrap="nowrap">'.$img
													. '&nbsp;<strong>' . $row->scorm_info[$j]->user_grade . "</strong> (" . $row->scorm_info[$j]->user_pts.")"
													. '</td>';
												}
												$sc_num2++;
											}
										}
										$j ++;
									}
								}
							}
							foreach ($lists['quiz_rows'][$i] as $quiz_row) {
								$j = 0;
								while ($j < count($row->quiz_info)) {
									if ($row->quiz_info[$j]->gbi_id == $quiz_row->c_id) {
										if ($row->quiz_info[$j]->user_status == -1) {
											echo '<td align=\'center\' style="text-align:center">-</td>';
										} else {
//											$image = $row->quiz_info[$j]->user_status ? 'btn_accept.png' : 'btn_cancel.png';
//											$alt = $row->quiz_info[$j]->user_status ? 'btn_accept' : 'btn_cancel';
//											$alt .= '" align="top';
//											$img = JLMS_reports_html::publishIcon(0, 0, 0, '', $alt, $image, $option, false );
//		
//											echo '<td style="vertical-align:middle; text-align:center" nowrap="nowrap">'.$img
//											. '&nbsp;<strong>' . $row->quiz_info[$j]->user_grade . "</strong> (" . $row->quiz_info[$j]->user_pts_full .")"
//											. '</td>';
											
											echo '<td style="vertical-align:middle; text-align:center" nowrap="nowrap">';
												echo JLMS_showQuizStatus($row->quiz_info[$j], 50);
											echo '</td>';
										}
									}
									$j ++;
								}
							}
							$j = 0;
							while ($j < count($row->grade_info)) {
								echo '<td align=\'center\' valign="middle" style="vertical-align:middle;text-align:center "><strong>'
								. $row->grade_info[$j]->user_grade
								. '</strong></td>';
								$j ++;
							} 
							if($i_row < $max_row){
								for($jj=$i_row;$jj<$max_row;$jj++){
									echo '<td align=\'center\' style="text-align:center;">&nbsp;<!--x--></td>';
								}
							}
							?>
						</tr>
					<?php 
						$k = 3 - $k;
					}?>
					</table>
					<?php 
					if(!$lists['user_id']){
						echo '<div class="joomlalms_page_tip"  style="text-align:center">'._JLMS_REPORTS_SELECT_USER.'</div>';
					}
					?>
				<input type="hidden" name="option" value="<?php echo $option;?>" />
				<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
				<input type="hidden" name="task" value="report_grade" />
				<input type="hidden" name="state" value="0" />
				<input type="hidden" name="view" value="" />
				<input type="hidden" name="boxchecked" value="0" />
				<input type="hidden" name="is_full" value="<?php echo $is_full?>" />
	
			</form>
	<?php
		JLMS_TMPL::CloseTS();
		JLMS_TMPL::CloseMT();
	}

	function JLMS_sreportScorm( $option, &$rows, $start_date, $end_date, &$pageNav, &$lists, $levels, $filt_cat, $filt_group, $is_full ){
		global $JLMS_CONFIG;
		$Itemid = $JLMS_CONFIG->get('Itemid');	

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

		JLMS_TMPL::OpenMT();
			$lists['user_id'] = isset($lists['user_id'])?$lists['user_id']:0;
			if($is_full){
				$hparams = array('show_menu'=>false);
			}else{
				$hparams = array('simple_menu'=>true);
			}
			$toolbar = array();
			JLMS_TMPL::ShowHeader('tracking', _JLMS_REPORTS_SCORM.' '.date("Y-m-d H:i:s"), $hparams, $toolbar);
		JLMS_TMPL::OpenTS();
		?>
		<script language="javascript" type="text/javascript">
		<!--//--><![CDATA[//><!--
			function pickup_date(){
				var form = document.adminForm;
				form.end_date.value = form.pick_date.value;
				form.start_date.value = form.pick_to_date.value;
				var is_cor = 1;
				if(form.start_date.value.length == 10 && form.end_date.value.length == 10)
				{
					if("<?php echo $JLMS_CONFIG->get('date_format',"Y-m-d")?>" == "d-m-Y"){
						if(form.end_date.value.substring(5)<form.start_date.value.substring(5)){
							is_cor = 0;
						} else if( form.end_date.value.substring(2,5)<form.start_date.value.substring(2,5)){
							is_cor = 0;
						} else if( form.end_date.value.substring(0,2)<form.start_date.value.substring(0,2)){
							is_cor = 0;
						}
					} else {	
						if(form.end_date.value.substring(0,4)<form.start_date.value.substring(0,4)){
							is_cor = 0;
						} else if ( form.end_date.value.substring(5,7)<form.start_date.value.substring(5,7)){
							is_cor = 0;
						} else if ( form.end_date.value.substring(8,10)<form.start_date.value.substring(8,10)){
							is_cor = 0;
						}
					}
				}
				if(!is_cor){
					alert("<?php echo _JLMS_REPORTS_SELECT_DATE;?>");
				} else {
					form.view.value = '';	
					form.submit();	
				}
			}
			function pickup_date_reset(){
				var form = document.adminForm;
				form.pick_date.value = '-';
				form.pick_to_date.value = '-';
				form.end_date.value = form.pick_date.value;
				form.start_date.value = form.pick_to_date.value;
				form.view.value = '';	
				form.submit();	
			}
		
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
			
			function submitFormView(view){
				var form = document.adminForm;
				form.view.value = view;
				form.task.value='report_scorm';
				form.submit();
			}
		//--><!]]>
		</script>
		<?php
		$action_url = $is_full ? ($JLMS_CONFIG->get('live_site') . "/index.php?tmpl=component&option=$option&amp;Itemid=$Itemid") : sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid");
		?>
		<form action="<?php echo $action_url;?>" method="post" name="adminForm">
			<table cellpadding="0" cellspacing="0" border="0" id="TheTable" <?php echo $is_full?"":'width="100%"'?>>
				<?php
				if(!$is_full){
				?>
				<tr>
					<td align="<?php echo $is_full ? "left" : "right";?>" <?php echo !$is_full? 'colspan="4"': '';?>>
						<table <?php echo $is_full?'':'width="30%"'?>>
							<tr>
								<td valign="top" <?php echo $is_full?'width="400"':'width="40%"'?>>
									<table width="100%" border="0">
										<tr>
											<td <?php echo $is_full?'width="100"':'width="20%"'?> nowrap="nowrap">
												Report Type:
											</td>
											<td>
												<?php
												echo JLMS_switchType($option);
												?>
											</td>
										</tr>
									</table>		
								</td>
							</tr>
						</table>	
					</td>
				</tr>
				<?php
				}
				?>
				<tr>
					<td align="left">
						<table <?php echo $is_full?'':'width="100%"'?>>
							<tr>
								<td valign="top" <?php echo $is_full?'width="400"':'width="40%"'?>>
									<table width="100%" border="0">
										<?php
										if($is_full){
										?>
										<tr>
											<td <?php echo $is_full?'width="100"':'width="20%"'?> nowrap="nowrap">
												Report Type:
											</td>
											<td>
												<?php
												echo JLMS_switchType($option);
												?>
											</td>
										</tr>
										<?php
										}
										?>
										<tr>
											<td <?php echo $is_full?'width="100"':'width="20%"'?> nowrap="nowrap">
												<?php
												if ($JLMS_CONFIG->get('multicat_use', 0)){
													echo ((isset($levels[0]->cat_name) && $levels[0]->cat_name != '')?$levels[0]->cat_name:_JLMS_COURSES_COURSES_GROUPS);
												} else {
													echo _JLMS_COURSES_COURSES_GROUPS;
												}
												?>
											</td>
											<td>
												<?php
												if ($JLMS_CONFIG->get('multicat_use', 0)){
													echo $lists['filter_0'];
												} else {
													echo $lists['jlms_course_cats'];
												}
												?>
											</td>
										</tr>
										<?php
										if(count($multicat)){
											for($i=0;$i<count($multicat);$i++){
												if($i > 0){
													?>
													<tr>
														<td <?php echo $is_full?'width="100"':'width="20%"'?> nowrap="nowrap">
															<?php
																echo ((isset($levels[$i]->cat_name) && $levels[$i]->cat_name != '')?$levels[$i]->cat_name:_JLMS_COURSES_COURSES_GROUPS);
															?>
														</td>
														<td>
															<?php
																echo $lists['filter_'.$i];
															?>
														</td>
													</tr>
													<?php
												}
											}
										}
										?>
										<tr>
											<td <?php echo $is_full?'width="100"':'width="20%"'?> nowrap="nowrap">
												Courses:
											</td>
											<td>
												<?php
												echo $lists['filt_course'];
												?>
											</td>
										</tr>
									</table>
								</td>
								<td valign="top" <?php echo $is_full?'width="400"':'width="40%"'?>>
									<table width="100%" border="0">
										<?php 
										if($is_full){
										?>
										<tr>
											<td colspan="2">
												&nbsp;
											</td>
										</tr>
										<?php
										}
										if($JLMS_CONFIG->get('use_global_groups', 1)){
										?>
										<tr>
											<td style="padding-left: 5px;" <?php echo $is_full?'width="100"':'width="20%"'?> nowrap="nowrap">
												<?php
												echo _JLMS_USER_GROUP_INFO;
												?>
											</td>
											<td>
												<?php
												echo $lists['filt_group'];
												?>
											</td>
										</tr>
										<?php
										}
										?>
										<tr>
											<td colspan="2">
												<table width="100%" border="0">
													<tr>
														<td style="padding:0px 10px;" width="30">From</td>
														<td valign="middle" align="center">
															<?php echo JLMS_HTML::_('calendar.calendar',$start_date,'pick_to','pick_to', null, null, 'statictext'); ?>
														</td>
														<td style="padding:0px 10px;" width="30">To</td>
														<td valign="middle" align="center">
															<?php echo JLMS_HTML::_('calendar.calendar',$end_date,'pick','pick', null, null, 'statictext'); ?>
														</td>
														<td valign="middle" align="center" width="18" style="vertical-align:middle ">
															<a href="javascript:pickup_date_reset();" title="">
																<img class="JLMS_png" src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_publish_hidden.png" alt="" title="Disabled filter date" border="0" width="16" height="16" />
															</a>
														</td>
														<td valign="middle" align="center" width="18" style="vertical-align:middle ">
															<a href="javascript:pickup_date();" title="">
																<img class="JLMS_png" src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_accept.png" alt="" title="Enabled filter date" border="0" width="16" height="16" />
															</a>
														</td>
													</tr>
												</table>
											</td>
										</tr>
									</table>
								</td>
								<?php if(!$is_full) { ?>
								<td style="padding-left:15px; white-space: nowrap;" align="right">
									<?php
									$link = $JLMS_CONFIG->get('live_site') . "/index.php?tmpl=component&option=".$option."&amp;Itemid=$Itemid&amp;task=report_scorm&amp;is_full=1";
									$link .= $filt_group ? "&amp;filt_group=".$filt_group : "";
									$link .= $start_date != "-" ? "&amp;start_date=".JLMS_dateToDisplay($start_date) : "";
									$link .= $end_date != "-" ? "&amp;end_date=".JLMS_dateToDisplay($end_date) : "";
									?>
									<a href="<?php echo $link;?>" target="_blank" title="<?php echo _JLMS_FULL_VIEW_BUTTON;?>"><?php echo _JLMS_FULL_VIEW_BUTTON;?></a>
								</td>
								<?php } ?>
							</tr>
						</table>			
					</td>
				</tr>
			</table>
					
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
				<thead>
					<tr>
						<td class="sectiontableheader" style="white-space: nowrap;">
							Username
						</td>
						<td class="sectiontableheader" style="white-space: nowrap;">
							Name
						</td>
						<td class="sectiontableheader" style="white-space: nowrap;">
							Email
						</td>
						<td class="sectiontableheader" style="white-space: nowrap;">
							Course Name
						</td>
						<td class="sectiontableheader" style="white-space: nowrap;">
							Course ID
						</td>
						<td class="sectiontableheader" style="white-space: nowrap;">
							Date
						</td>
						<td class="sectiontableheader" style="text-align: center; white-space: nowrap;">
							Score
						</td>
						<td class="sectiontableheader" style="text-align: center; white-space: nowrap;">
							Course Status
						</td>
					</tr>
				</thead>
				<tbody>
					<?php
					$k=1;
					for ($i=0, $n=count($rows); $i < $n; $i++) {
						$row = $rows[$i];
						?>
						<tr class="<?php echo "sectiontableentry$k";?>">
							<td>
								<?php
								echo $row->username;
								?>
							</td>
							<td>
								<?php
								echo $row->name;
								?>
							</td>
							<td>
								<?php
								echo $row->email;
								?>
							</td>
							<td>
								<?php
								echo $row->course_name;
								?>
							</td>
							<td>
								<?php
								echo $row->lpath_name;
								?>
							</td>
							<td>
								<?php
								if(isset($row->scorm_data) && $row->scorm_data->status){
									if($row->scorm_data->end){
										$date_end = date("Y-m-d H:i:s", $row->scorm_data->end);
										echo JLMS_dateToDisplay($date_end);
									}
								}
								?>
							</td>
							<td align="center">
								<?php
								if(isset($row->scorm_data)){
									echo $row->scorm_data->score;
								}
								?>
							</td>
							<td align="center">
								<?php
								$image = $row->course_status ? 'btn_accept.png' : 'btn_cancel.png';
								$alt = $row->course_status ? 'btn_accept' : 'btn_cancel';
								$alt .= '" align="top';
								echo JLMS_reports_html::publishIcon(0, 0, 0, '', $alt, $image, $option, false );
								?>
							</td>
						</tr>
						<?php
						$k = 3 - $k;
					}
					?>
				</tbody>
			</table>
			<?php 
			if($lists['user_id'] && !$is_full && count($rows)){
				if($JLMS_CONFIG->get('new_lms_features', 1)){
					$controls = array();
					$controls[] = array('href' => "javascript:submitFormView('csv');", 'title' => 'CSV', 'img' => 'csv');
					JLMS_TMPL::ShowControlsFooterC($controls, '', false, false, _JLMS_EXPORT_TO.':');
				}
			}
			?>	
			<?php if($lists['user_id'] && !$is_full){?>
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td align="center">
					<?php 
					$link = "index.php?option=$option&amp;Itemid=$Itemid&amp;task=report_scorm&amp;filt_group=$filt_group&amp;filt_cat=$filt_cat";
					$link .= strlen($start_date) ? "&amp;start_date=".JLMS_dateToDisplay($start_date) : "";
					$link .= strlen($end_date) ? "&amp;end_date=".JLMS_dateToDisplay($end_date) : "";
					echo $pageNav->writePagesLinks( $link );
					?> 
					</td>
				</tr>
				<tr>
					<td align="center">
						<?php
						echo _JLMS_PN_DISPLAY_NUM . $pageNav->getLimitBox( $link ) . '&nbsp;' . $pageNav->getPagesCounter();
						?>
					</td>
				</tr>
			</table>
			<?php }?>
			<input type="hidden" name="option" value="<?php echo $option;?>" />
			<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
			<input type="hidden" name="task" value="report_scorm" />
			<input type="hidden" name="view" value="" />
			<input type="hidden" name="start_date" value="<?php echo $start_date;?>" />
			<input type="hidden" name="end_date" value="<?php echo $end_date;?>" />
			<input type="hidden" name="state" value="0" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="is_full" value="<?php echo $is_full?>" />
		</form>

		<?php
			$action_url = $is_full ? ($JLMS_CONFIG->get('live_site') . "/index.php?tmpl=component&option=$option&amp;Itemid=$Itemid") : sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid");
		?>
		<form action="<?php echo $action_url;?>" method="post" name="adminFormCsv">
				<input type="hidden" name="option" value="<?php echo $option;?>" />
				<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
				<input type="hidden" name="task" value="report_scorm" />
				<input type="hidden" name="is_full" value="1" />
				<input type="hidden" name="view" value="" />
		</form>	
		<?php
		JLMS_TMPL::CloseTS();
		JLMS_TMPL::CloseMT();
	}

	function publishIcon( $id, $course_id, $state, $task, $alt, $image, $option, $href = true ) {
		global $JLMS_CONFIG;
		$ret_str = '<img class="JLMS_png" src="'.$JLMS_CONFIG->get('live_site').'/components/com_joomla_lms/lms_images/toolbar/'.$image.'" width="16" height="16" border="0" alt="'.$alt.'" />';
		return $ret_str;
	}

	function Echo_tbl_header( $header ) {
		$ret_str = '';
		$ret_str = $header;
		return $ret_str;
	}
}
?>