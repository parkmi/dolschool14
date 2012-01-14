<?php
/**
* joomla_lms.html.php
* (c) JoomaLMS eLearning Software http://www.joomlalms.com/
**/

// no direct access
defined( '_JLMS_EXEC' ) or die( 'Restricted access' );

class joomla_lms_html {

	function showCoursesForGuest( $option, &$lists, &$rows, &$pageNav, $enrollment ) {
		global $Itemid, $JLMS_CONFIG; 
//		$show_fee_col = $JLMS_CONFIG->get('show_fee_column');
		$lms_img_path = $JLMS_CONFIG->get('live_site') . '/' . $JLMS_CONFIG->get('lms_path_to_images', 'components/com_joomla_lms/lms_images');
		$show_paid_courses = $JLMS_CONFIG->get('show_paid_courses', 1);
		$show_course_author = $JLMS_CONFIG->get('show_course_authors', 1);
		
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
		
	<?php if ($enrollment) { ?>
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
		function isChecked(isitchecked){
			if (isitchecked == true){
				document.adminForm.boxchecked.value++;
			}
			else {
				document.adminForm.boxchecked.value--;
			}
		}
		var old_filters = new Array();
		function read_filter(){
			var form = document.adminForm;
			var count_levels = '<?php echo count($lists['levels']);?>';
			for(var i=0;i<parseInt(count_levels);i++){
				if(form['filter_id_'+i] != null){
					old_filters[i] = form['filter_id_'+i].value;
				}
			}
		}
		function write_filter(){
			var form = document.adminForm;
			var count_levels = '<?php echo count($lists['levels']);?>';
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
		//--><!]]>
		</script>
	<?php }
		JLMS_TMPL::OpenMT();

		$params = array(
			'show_menu' => true,
			'simple_menu' => true,
		);

		JLMS_TMPL::OpenTS('', ' align="right" style="text-align:right " width="100%"');
			JLMS_showTopMenu_simple($option);
		JLMS_TMPL::CloseTS();

		JLMS_TMPL::OpenTS('',' align="left"');
			echo JLMS_ShowText_WithFeatures($lists['homepage_text']);
		JLMS_TMPL::CloseTS();
		JLMS_TMPL::OpenTS();

		if ($enrollment) { ?>
			<form action="<?php echo JURI::base()."index.php?option=$option&amp;Itemid=$Itemid";?>" method="post" name="adminForm">
		<?php } ?>
			<table width="100%" cellpadding="0" cellspacing="0" border="0" id="jlms_courses_guest">
<?php
	if ($enrollment) {
		if($JLMS_CONFIG->get('multicat_use', 0)){
			JLMS_TMPL::OpenTS('', ' align="right" style="text-align:right " width="100%"');
				echo ((isset($lists['levels'][0]->cat_name) && $lists['levels'][0]->cat_name != '')?$lists['levels'][0]->cat_name:_JLMS_COURSES_COURSES_GROUPS)." ".$lists['filter_0'];
			JLMS_TMPL::CloseTS();			
		} else {
			JLMS_TMPL::OpenTS('', ' align="right" style="text-align:right " width="100%"');
				echo _JLMS_COURSES_COURSES_GROUPS." ".$lists['groups_course'];
			JLMS_TMPL::CloseTS();
		}
		if(count($multicat)){
			for($i=0;$i<count($multicat);$i++){
				if($i > 0){
					JLMS_TMPL::OpenTS('', ' align="right" style="text-align:right " width="100%"');
						echo ((isset($lists['levels'][$i]->cat_name) && $lists['levels'][$i]->cat_name != '')?$lists['levels'][$i]->cat_name:_JLMS_COURSES_COURSES_GROUPS)." ".$lists['filter_'.$i];
					JLMS_TMPL::CloseTS();
				}
			}
		}
		$controls = array();
		$controls[] = array('href' => "javascript:submitbutton('enroll','');", 'noscript' => 'submit', 'value' => '1', 'title' => _JLMS_ENROLL, 'img' => 'publish');
		JLMS_TMPL::ShowControlsFooter($controls, '', false);
	}
?>
<tr><td><table width="100%" cellpadding="0" cellspacing="0" border="0" class="<?php echo JLMSCSS::_('jlmslist');?>">
				<tr>
					<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center" width="1%">#</<?php echo JLMSCSS::tableheadertag();?>>
			<?php if ($enrollment) { ?>
					<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center" width="1%">&nbsp;</<?php echo JLMSCSS::tableheadertag();?>>
			<?php }?>
					<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" width="35%" align="left" id="jlms_courselist_name"><?php echo _JLMS_COURSES_TBL_HEAD_NAME;?></<?php echo JLMSCSS::tableheadertag();?>>
					<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" width="20%" align="left" id="jlms_courselist_category"><?php echo _JLMS_COURSES_COURSES_CAT;?></<?php echo JLMSCSS::tableheadertag();?>>
					<?php
				if($show_course_publish_dates){
					?>
					<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" width="6%" align="left" nowrap="nowrap"><?php echo _JLMS_COURSES_ST_DATE;?></<?php echo JLMSCSS::tableheadertag();?>>
					<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" width="6%" align="left" nowrap="nowrap"><?php echo _JLMS_COURSES_END_DATE;?></<?php echo JLMSCSS::tableheadertag();?>>
					<?php
				}
				if($price_fee_type == 1){?>
					<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" width="4%" align="left" nowrap="nowrap"><?php echo _JLMS_COURSES_FEETYPE;?></<?php echo JLMSCSS::tableheadertag();?>>
					<?php 
				} else if($price_fee_type == 2){?>
					<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" width="4%" align="left" nowrap="nowrap"><?php echo _JLMS_COURSES_PRICE;?></<?php echo JLMSCSS::tableheadertag();?>>							
					<?php
				}
					?>
				</tr>
			<?php
			$k = 1;
			for ($i=0, $n=count($rows); $i < $n; $i++) {
				$row = $rows[$i];
				if (!$show_paid_courses && $row->paid){
					continue;
				}
				$course_descr = strip_tags($row->course_description);
				if (strlen($course_descr) > 100) {
					$course_descr = substr($course_descr, 0, 100)."...";
				}
				if ($row->self_reg == 0) {
					$checked = "<a href='mailto:".$row->email."'><img class='JLMS_png' src=\"".$lms_img_path."/dropbox/dropbox_corr.png\" width='16' height='16' border='0' alt='' title='' /></a>"; 
				} else {
					if ($row->gid) {
						$checked = "<a href='mailto:".$row->email."'><img class='JLMS_png' src=\"".$lms_img_path."/dropbox/dropbox_corr.png\" width='16' height='16' border='0' alt='' title='' /></a>"; 
					} else {
						$checked = mosHTML::idBox( $i, $row->id);
					}
				}
				$link 	= sefRelToAbs("index.php?option=".$option."&amp;Itemid=".$Itemid."&amp;task=course_guest&amp;id=". $row->id); ?>
				<?php
				if($show_short_description){
				?>
				<tr valign="middle" style="vertical-align:middle" class="<?php echo JLMSCSS::_('sectiontableentry2');?>">
				<?php
				} else {
				?>
				<tr valign="middle" style="vertical-align:middle" class="<?php echo JLMSCSS::_('sectiontableentry'.$k);?>">				
				<?php
				}
				?>
					<td align="center"><?php echo ( $pageNav->limitstart + $i + 1 );?></td>
					<?php if ($enrollment) { ?>
					<td align="center"><?php echo $checked;?></td>
					<?php }?>
					<td align="left">
						<a href="<?php echo $link;?>" title="<?php echo _JLMS_VIEW_DETAILS;?>">
							<?php echo $row->course_name;?>
							</a><br />
							<?php if($show_course_author){?>
							<span class="small"><?php echo _JLMS_HOME_AUTHOR."&nbsp;".$row->user_fullname;?></span>
							<?php } ?>
					</td>
					<td align="left"><?php echo $row->c_category?$row->c_category:'&nbsp;';?></td>
					<?php
//						if (!$JLMS_ACL->CheckPermissions('lms', 'order_courses')) {
						if($show_course_publish_dates){
						?>
						<td align="center" nowrap="nowrap"><?php echo $row->publish_start?JLMS_dateToDisplay($row->start_date):'&nbsp;';?></td>
						<td align="center" nowrap="nowrap"><?php echo $row->publish_end?JLMS_dateToDisplay($row->end_date):'&nbsp;';?></td>
						<?php
						}
//						}
						?>
					<?php 
					/*if($show_fee_col == 1){?>
					<td align="left"><?php echo $row->paid ? _JLMS_COURSES_PAID : _JLMS_COURSES_FREE;?></td>
					<?php }*/
					if($price_fee_type){
						if($price_fee_type == 1){
						?>
						<td align="center"><?php echo $row->paid ? _JLMS_COURSES_PAID : _JLMS_COURSES_FREE;?></td>
						<?php
						} else if($price_fee_type == 2){
						?>
						<td align="center"><?php echo $row->paid ? $jlms_cs.sprintf('%.2f',round($row->course_price,2)) : _JLMS_COURSES_FREE;?></td>	
						<?php
						}		
					}
					?>
				</tr>
				<?php
				if($show_short_description){
					if(strlen($row->course_sh_description)){
					?>
					<tr class="<?php echo JLMSCSS::_('sectiontableentry1');?>">
						<td>&nbsp;</td>
						<td colspan="<?php echo $colspan_sh_description;?>" style="text-align: justify;">
							<?php echo $row->course_sh_description;?>
						</td>
					</tr>
					<?php
					}
				}
				?>
				<?php
				$k = 3 - $k;
			}?>
				<tr>
					<td class="<?php echo JLMSCSS::_('jlmslist-footer_td');?>" colspan="<?php echo $colspan_sh_description+1;?>" align="center"><div align="center">
<?php
					$filter_groups = intval( mosGetParam( $_REQUEST, 'groups_course', 0 ) );
					$link = "index.php?option=$option&amp;Itemid=$Itemid";
					if ($filter_groups) {
						$link = $link ."&amp;groups_course=$filter_groups";
					}
					
					echo $pageNav->writePagesLinks( $link ); ?> 
					</div></td>
				</tr></table></td></tr>
				
<?php 
		if ($enrollment) {
			JLMS_TMPL::ShowControlsFooter($controls);
		}
?>
</table>
							
	<?php
		if ($enrollment) { ?>
			<input type="hidden" name="task" value="subscription" />
			<input type="hidden" name="id" value="" />
			<input type="hidden" name="boxchecked" value="0" />
		</form>
	<?php }

		JLMS_TMPL::CloseTS();
		JLMS_TMPL::CloseMT();
	}
	
	function showCoursesForGuest_blog( $option, &$lists, &$rows, &$pageNav, $enrollment ) {
		global $Itemid, $JLMS_CONFIG; 
//		$show_fee_col = $JLMS_CONFIG->get('show_fee_column');
		$lms_img_path = $JLMS_CONFIG->get('live_site') . '/' . $JLMS_CONFIG->get('lms_path_to_images', 'components/com_joomla_lms/lms_images');
		$show_paid_courses = $JLMS_CONFIG->get('show_paid_courses', 1);
		$show_course_author = $JLMS_CONFIG->get('show_course_authors', 1);
		
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
		
	<?php if ($enrollment) { ?>
		<script language="javascript" type="text/javascript">
		<!--//--><![CDATA[//><!--
		var old_filters = new Array();
		function read_filter(){
			var form = document.adminForm;
			var count_levels = '<?php echo count($lists['levels']);?>';
			for(var i=0;i<parseInt(count_levels);i++){
				if(form['filter_id_'+i] != null){
					old_filters[i] = form['filter_id_'+i].value;
				}
			}
		}
		function write_filter(){
			var form = document.adminForm;
			var count_levels = '<?php echo count($lists['levels']);?>';
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
		//--><!]]>
		</script>
	<?php }
		JLMS_TMPL::OpenMT();

		$params = array(
			'show_menu' => true,
			'simple_menu' => true,
		);

		JLMS_TMPL::OpenTS('', ' align="right" style="text-align:right " width="100%"');
			JLMS_showTopMenu_simple($option);
		JLMS_TMPL::CloseTS();

		JLMS_TMPL::OpenTS('',' align="left"');
			echo JLMS_ShowText_WithFeatures($lists['homepage_text']);
		JLMS_TMPL::CloseTS();
		JLMS_TMPL::OpenTS();
		?>
		<form action="<?php echo $JLMS_CONFIG->getCfg('live_site')."/index.php?option=$option&amp;Itemid=$Itemid";?>" method="post" name="adminForm">
		<table width="100%" cellpadding="0" cellspacing="0" border="0">
		<?php
		if($JLMS_CONFIG->get('multicat_use', 0)){
			JLMS_TMPL::OpenTS('', ' align="right" style="text-align:right " width="100%"');
				echo ((isset($lists['levels'][0]->cat_name) && $lists['levels'][0]->cat_name != '')?$lists['levels'][0]->cat_name:_JLMS_COURSES_COURSES_GROUPS)." ".$lists['filter_0']."&nbsp;&nbsp;";
			JLMS_TMPL::CloseTS();			
		} else {
			JLMS_TMPL::OpenTS('', ' align="right" style="text-align:right " width="100%"');
				echo _JLMS_COURSES_COURSES_GROUPS." ".$lists['groups_course']."&nbsp;&nbsp;";
			JLMS_TMPL::CloseTS();
		}
		if(count($multicat)){
			for($i=0;$i<count($multicat);$i++){
				if($i > 0){
					JLMS_TMPL::OpenTS('', ' align="right" style="text-align:right " width="100%"');
						echo ((isset($lists['levels'][$i]->cat_name) && $lists['levels'][$i]->cat_name != '')?$lists['levels'][$i]->cat_name:_JLMS_COURSES_COURSES_GROUPS)." ".$lists['filter_'.$i]."&nbsp;&nbsp;";
					JLMS_TMPL::CloseTS();
				}
			}
		}
	
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
				<td colspan="<?php echo $lists['menu_params']->get('num_columns', 2);?>" valign="top">
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
				for($z=0; $z < $lists['menu_params']->get('num_columns', 2); $z++){
					if($z > 0){$divider = ' column_separator';}
				?>
				<td class="article_column<?php echo $divider;?>" valign="top" width="<?php echo intval(100 / $lists['menu_params']->get('num_columns'));?>%">
					<?php
					for($x=0;$x<count($rows);$x++){
						$n = $x * $lists['menu_params']->get('num_columns', 2) + $z;
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
		?>
		</table>
			<input type="hidden" name="task" value="subscription" />
			<input type="hidden" name="id" value="" />
			<input type="hidden" name="boxchecked" value="0" />
		</form>
		<?php

		JLMS_TMPL::CloseTS();
		JLMS_TMPL::CloseMT();
	}
	
	function showCourseGuest( $id, &$row, $option, $enrollment ) {
		$_JLMS_PLUGINS = & JLMSFactory::getPlugins();
		$JLMS_CONFIG = & JLMSFactory::getConfig();
		$Itemid = $JLMS_CONFIG->get('Itemid');

		JLMS_TMPL::OpenMT();

		$params = array(
			'show_menu' => true,
			'simple_menu' => true,
		);
		$toolbar = array();
		$show_down_back = true;
		if ($enrollment) {
			if ($row->self_reg && !$row->gid) {
				$params['sys_msg'] = _JLMS_COURSES_ENROLL_MSG;
				$toolbar[] = array('btn_type' => 'yes','btn_str' => _JLMS_SUBSCRIBE, 'btn_js' => "javascript:submitbutton('subscription','');" );
				$toolbar[] = array('btn_type' => 'cancel', 'btn_js' => sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid&amp;task=courses") );
				$params['toolbar_position'] = 'center';
				$show_down_back = false;
				$html_code_before_toolbar = "
				<form action='".sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid")."' method='post' name='adminForm_enroll'>
				<script language='javascript' type='text/javascript'>
					<!--
					
					function submitbutton(pressbutton) {
						var form = document.adminForm_enroll;
						form.task.value = pressbutton;
						form.submit();
					}
					//-->
				</script>
				";
				/*if ($row->paid) {
					$html_code_before_toolbar .= "
						<input type='hidden' name='option' value='".$option."' />
						<input type='hidden' name='Itemid' value='".$Itemid."' />
						<input type='hidden' name='task' value='subscription' />
						<input type='hidden' name='cid[]' value='".$row->id."' />
						<input type='hidden' name='state' value='0' />
						</form>
					";
				} else {*/
					$html_code_before_toolbar .= "
						<input type='hidden' name='option' value='".$option."' />
						<input type='hidden' name='Itemid' value='".$Itemid."' />
						<input type='hidden' name='task' value='courses' />
						<input type='hidden' name='id' value='".$row->id."' />
						<input type='hidden' name='cid[]' value='".$row->id."' />
						<input type='hidden' name='state' value='0' />
						</form>
					";
				//}
				$params['html_code_before_toolbar'] = $html_code_before_toolbar;
			}
		}
		
		JLMS_TMPL::ShowHeader('course', $row->course_name, $params, $toolbar);

		JLMS_TMPL::OpenTS('', ' width="100%"');?>
				<table cellpadding="0" cellspacing="0" border="0" width="100%" id="jlms_course_desc_guest">
					<?php
					$_JLMS_PLUGINS->loadBotGroup('system');
					$plugin_args = array();
					$plugin_args[] = $id;
					$_JLMS_PLUGINS->trigger('onAboveCourseDetailsPage', $plugin_args);
					?>
					<tr>
						<td>
						<?php echo JLMS_ShowText_WithFeatures($row->course_description);?>
						</td>
					</tr>
					<?php
					//$_JLMS_PLUGINS->loadBotGroup('system');
					$plugin_args = array();
					$plugin_args[] = $id;
					$_JLMS_PLUGINS->trigger('onBelowCourseDetailsPage', $plugin_args);
					?>
				</table>
		<?php
		JLMS_TMPL::CloseTS();
		if ($show_down_back) {
			JLMS_TMPL::OpenTS('',' style="text-align:center"'); ?>
			<br /><br /><a class="back_button" href="<?php echo sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid");?>"><?php echo _JLMS_TXT_BACK;?></a>
		<?php
			JLMS_TMPL::CloseTS();
		}
		JLMS_TMPL::CloseMT();
	}

	function showMainPage_front( $option, &$lists, &$my_courses, &$my_dropbox, &$my_homework, &$my_announcements, &$my_mailbox, &$my_certificates, &$latest_forum_posts) {
		global $Itemid, $JLMS_CONFIG;
		
		$JLMS_ACL = & JLMSFactory::getACL();
		?>
		
		<table width="100%" cellpadding="0" cellspacing="3" border="0" id="jlms_mainarea" style="border-collapse: separate">
		<?php if ($JLMS_CONFIG->get('lofe_show_top', true)) { ?>
			<tr>
				<td colspan="2" align="right"><?php JLMS_showTopMenu_simple($option);?></td>
			</tr>
		<?php } ?>
			<tr>
				<td colspan="2" align="left">
				<?php echo JLMS_ShowText_WithFeatures($lists['homepage_text']);?><br /><br />
				</td>
			</tr>
			<?php
			global $JLMS_DB, $my;
			if(class_exists('Jfactory')){
				$user = JLMSFactory::getUser();
				$my->id = $user->id;	
			}
			
			$is_ceo = $JLMS_ACL->isStaff();
			
			$JLMS_ACL = & JLMSFactory::getACL();
			if ($JLMS_CONFIG->get('show_statistics_reports')) {
				if ($JLMS_ACL->isTeacher()) {
					// user is a teacher - he can see reports module
				} else if($is_ceo){
					$JLMS_CONFIG->set('show_statistics_reports', true);	
				} else {
					$JLMS_CONFIG->set('show_statistics_reports', false);
				}
			}
			
			$on_modules = false;
			if (
				$JLMS_CONFIG->get('frontpage_announcements') ||
				$JLMS_CONFIG->get('frontpage_dropbox') ||
				$JLMS_CONFIG->get('frontpage_homework') ||
				$JLMS_CONFIG->get('frontpage_mailbox') ||
				$JLMS_CONFIG->get('frontpage_certificates') ||
				$JLMS_CONFIG->get('plugin_forum') ||
				$JLMS_CONFIG->get('show_statistics_reports') ||
				$JLMS_CONFIG->get('frontpage_notices_teacher')
			){
				$on_modules = true;
			}
			
			$td_width="100%";
			$colspan = ' colspan="2"';
			if ($on_modules){
				$td_width="50%";
				$colspan = '';
			}
			?>
			<tr>
				<td width="<?php echo $td_width;?>" valign="top"<?php echo $colspan;?>>
					<?php
					if ($JLMS_CONFIG->get('frontpage_courses_tree', 1) && $JLMS_CONFIG->get('frontpage_courses')) {
						joomla_lms_html::echoMyCourses_tree($option, $Itemid, $my_courses, $lists);
					} elseif($JLMS_CONFIG->get('frontpage_courses')) {
						joomla_lms_html::echoMyCourses($option, $Itemid, $my_courses);
					} else {
						echo '&nbsp;';
					}
					echo '<br />';
					if ($JLMS_CONFIG->get('frontpage_allcourses', 1)) {?>
					<table width="100%" cellpadding="0" cellspacing="0" border="0" class="<?php echo JLMSCSS::_('jlmslist', 'jlms_homepage_list');?>">
						<tr><<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center"><?php echo _JLMS_HOME_COURSES_LIST;?></<?php echo JLMSCSS::tableheadertag();?>></tr>
						<tr class='<?php echo JLMSCSS::_('sectiontableentry1');?>'><td><a href="<?php echo sefRelToabs("index.php?option=".$option."&amp;Itemid=".$Itemid."&amp;task=courses");?>" title="<?php echo _JLMS_HOME_COURSES_LIST_HREF;?>"><?php echo _JLMS_HOME_COURSES_LIST_HREF;?></a></td></tr>
					</table>
					<?php } ?>
				</td>
				<?php
				if ($on_modules){
				?>
				<td width="50%" valign="top">
				<?php
					if ($JLMS_CONFIG->get('frontpage_announcements')) {
						joomla_lms_html::echoMyAgenda($option, $Itemid, $my_announcements );
						echo '<br />';
					}
					if ($JLMS_CONFIG->get('frontpage_dropbox')) {
						joomla_lms_html::echoMyDropBox($option, $Itemid, $my_dropbox, $lists);
						echo '<br />';
					}
					if ($JLMS_CONFIG->get('frontpage_homework')) {
						joomla_lms_html::echoMyHomeWork($option, $Itemid, $my_homework);
						echo '<br />';
					}
					if ($JLMS_CONFIG->get('frontpage_mailbox')) {
						joomla_lms_html::echoMyMailBox($option, $Itemid, $my_mailbox, $lists);
						echo '<br />';
					}
					if ($JLMS_CONFIG->get('frontpage_certificates')) {
						joomla_lms_html::echoMyCertificates($option, $Itemid, $my_certificates, $lists);
						echo '<br />';
					}
					if ($JLMS_CONFIG->get('plugin_forum') && $JLMS_CONFIG->get('frontpage_latest_forum_posts')) {
						joomla_lms_html::echoLatestForumPosts($option, $Itemid, $latest_forum_posts, $lists);
						echo '<br />';
					}
					if ($JLMS_CONFIG->get('show_statistics_reports')) {
						joomla_lms_html::echoMyReports($option, $Itemid);
						echo '<br />';
					}
					if ($JLMS_CONFIG->get('frontpage_notices_teacher')) {
						joomla_lms_html::echoMyNotices($option, $Itemid, $lists['my_notices']);
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
	function & PrepareDescription( &$pr_desc ) {
		$description = strip_tags($pr_desc);
		if (strlen($description) > 120) {
			$description = jlms_string_substr($description,0,120)."...";
		}
		//$description = JLMS_txt2overlib($description);
		return $description;
	}
	function echoMyHomeWork($option, $Itemid, &$my_homework) {
		global $JLMS_CONFIG;
	?>
		<table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-top:0; margin-bottom:0;" class="<?php echo JLMSCSS::_('jlmslist', 'jlms_homepage_list');?>">
		<tr>
			<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" colspan="2" align="center" style="text-align:center"><?php echo _JLMS_HOME_HOMEWORK_TITLE;?></<?php echo JLMSCSS::tableheadertag();?>>
		</tr>
		<?php
			$k = 1;
			if (count($my_homework)) {
				foreach ($my_homework as $my_hw_item) {
					$link 	= sefRelToAbs("index.php?option=".$option."&amp;Itemid=".$Itemid."&amp;task=".($my_hw_item->teach_id?'hw_stats':'hw_view')."&course_id=". $my_hw_item->course_id."&id=".$my_hw_item->id);
					$description = & joomla_lms_html::PrepareDescription($my_hw_item->hw_description);
					$add_info = '';
					if ( isset($my_hw_item->course_name) && $my_hw_item->course_name ) {
						$add_info = "&nbsp;(".$my_hw_item->course_name.")";
					}
					$title = JLMS_txt2overlib($my_hw_item->hw_name);
					$img = 'tlb_homework.png';
					$alt = 'homework';
					if ($my_hw_item->hw_status == 1) {
						$img = 'btn_accept.png';
						$alt = 'complete';
					}
					echo "<tr class='".JLMSCSS::_('sectiontableentry'.$k)."'><td valign='middle' width='16'>"
					."<img class='JLMS_png' src=\"".$JLMS_CONFIG->get('live_site')."/components/com_joomla_lms/lms_images/toolbar/".$img."\" width='16' height='16' border='0' alt='$alt' />"
					."</td><td>"
					.JLMS_toolTip($title, $description, '', $link, 1, 120, 'false', 'jlms_ttip_posts')
					.$add_info
					."</td></tr>";
					$k = 3 - $k;
				}
			} else {
				echo "<tr class='".JLMSCSS::_('sectiontableentry'.$k)."'><td colspan='2'>"._JLMS_HOME_HOMEWORK_NO_ITEMS."</td></tr>";
			}
		?>
		</table>
<?php	
	}

	function echoMyReports( $option, $Itemid ) {
		$JLMS_CONFIG = & JLMSFactory::getConfig();
		$user = JLMSFactory::getUser();
		$my_id = $user->get('id');
		$db = & JFactory::getDbo();

		$JLMS_ACL = & JLMSFactory::getACL();
		?>
		<table width="100%" cellpadding="0" cellspacing="0" border="0" class="<?php echo JLMSCSS::_('jlmslist', 'jlms_homepage_list');?>" style="margin-top:0; margin-bottom:0;">
		<tr>
<?php
$reports_heading = _JLMS_REPORTS_MODULE;
$add_text = '';
if ($JLMS_CONFIG->get('is_trial', false) && $JLMS_CONFIG->get('trial_reports_module_heading', '')) {
	$reports_heading .= $JLMS_CONFIG->get('trial_reports_module_heading', '');
}
if ($JLMS_CONFIG->get('is_trial', false) && $JLMS_CONFIG->get('trial_reports_module_text', '')) {
	$add_text = $JLMS_CONFIG->get('trial_reports_module_text', '');
}
?>
			<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" colspan="2" align="center" style="text-align:center"><?php echo $reports_heading;?></<?php echo JLMSCSS::tableheadertag();?>>
		</tr>
		<?php
if ($add_text) {
	echo '<tr><td colspan="2"><div class="joomlalms_sys_message">'.$add_text.'</div></td></tr>';
}
		if($JLMS_ACL->isTeacher()/* || $JLMS_ACL->isStaff()*/){
			$link 	= sefRelToAbs("index.php?option=".$option."&amp;Itemid=".$Itemid."&amp;task=report_access");		
				echo "<tr class='".JLMSCSS::_('sectiontableentry1')."'>";
				echo "<td valign='middle' width='16'><img class='JLMS_png' src=\"".$JLMS_CONFIG->get('live_site')."/components/com_joomla_lms/lms_images/toolbar/tlb_attendance.png\" width='16' height='16' border='0' alt='agenda' /></td>";
				echo "<td><a href='".$link."' >"._JLMS_REPORTS_ACCESS."</a></td></tr>";
			$link 	= sefRelToAbs("index.php?option=".$option."&amp;Itemid=".$Itemid."&amp;task=report_certif");	
				echo "<tr class='".JLMSCSS::_('sectiontableentry2')."'>";
				echo "<td valign='middle' width='16'><img class='JLMS_png' src=\"".$JLMS_CONFIG->get('live_site')."/components/com_joomla_lms/lms_images/toolbar/tlb_attendance.png\" width='16' height='16' border='0' alt='agenda' /></td>";
				echo "<td><a href='".$link."' >"._JLMS_REPORTS_CONCLUSION."</a></td></tr>";
			$link 	= sefRelToAbs("index.php?option=".$option."&amp;Itemid=".$Itemid."&amp;task=report_grade");		
				echo "<tr class='".JLMSCSS::_('sectiontableentry1')."'>";
				echo "<td valign='middle' width='16'><img class='JLMS_png' src=\"".$JLMS_CONFIG->get('live_site')."/components/com_joomla_lms/lms_images/toolbar/tlb_attendance.png\" width='16' height='16' border='0' alt='agenda' /></td>";
				echo "<td><a href='".$link."' >"._JLMS_REPORTS_USER."</a></td></tr>";
		}	
		if ( ($JLMS_ACL->isTeacher() || $JLMS_ACL->isStaff()) && $JLMS_CONFIG->get('show_scorm_report_link', false) ){
			$link 	= sefRelToAbs("index.php?option=".$option."&amp;Itemid=".$Itemid."&amp;task=report_scorm");		
				echo "<tr class='".JLMSCSS::_('sectiontableentry2')."'>";
				echo "<td valign='middle' width='16'><img class='JLMS_png' src=\"".$JLMS_CONFIG->get('live_site')."/components/com_joomla_lms/lms_images/toolbar/tlb_attendance.png\" width='16' height='16' border='0' alt='agenda' /></td>";
				echo "<td><a href='".$link."' >"._JLMS_REPORTS_SCORM."</a></td></tr>";	
		}		
		?>
	</table>
<?php
	}
	function echoMyAgenda( $option, $Itemid, &$my_announcements ) {
		global $JLMS_CONFIG;
	?>
		<table width="100%" cellpadding="0" cellspacing="0" border="0" class="<?php echo JLMSCSS::_('jlmslist', 'jlms_homepage_list');?>" style="margin-top:0; margin-bottom:0;">
		<tr>
			<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" colspan="2" align="center" style="text-align:center"><?php echo _JLMS_HOME_AGENDA_TITLE;?></<?php echo JLMSCSS::tableheadertag();?>>
		</tr>
		<?php
		$k = 1;
		
		if (count($my_announcements)) {
			foreach ($my_announcements as $my_agenda) {
				$link 	= sefRelToAbs("index.php?option=".$option."&amp;Itemid=".$Itemid."&amp;id=".$my_agenda->course_id."&amp;task=agenda&amp;agenda_id=".$my_agenda->agenda_id."&amp;date=".date('Y-m')."#anc".$my_agenda->agenda_id."-".date('Y')."-".date('m'));
				$description = & joomla_lms_html::PrepareDescription($my_agenda->content);
				$add_info = '';
				if ( isset($my_agenda->course_name) && $my_agenda->course_name ) {
					$add_info = "&nbsp;(".$my_agenda->course_name.")";
				}
				$title = JLMS_txt2overlib($my_agenda->title);
				echo "<tr class='".JLMSCSS::_('sectiontableentry'.$k)."'><td valign='middle' width='16'>"
				."<img class='JLMS_png' src=\"".$JLMS_CONFIG->get('live_site')."/components/com_joomla_lms/lms_images/toolbar/tlb_agenda.png\" width='16' height='16' border='0' alt='agenda' />"
				."</td><td>"
				.JLMS_toolTip($title, $description, $title, $link, 1, 120, 'false', 'jlms_ttip_posts')
				.$add_info."</td></tr>";
				$k = 3 - $k;
			}
		} else {
			echo "<tr class='".JLMSCSS::_('sectiontableentry'.$k)."'><td colspan='2'>"._JLMS_HOME_AGENDA_NO_ITEMS."</td></tr>";
		} ?>
	</table>
<?php
	}
	function echoMyDropBox( $option, $Itemid, &$my_dropbox, &$lists ) {
		global $JLMS_CONFIG;
	?>
		<table width="100%" cellpadding="0" cellspacing="0" border="0" class="<?php echo JLMSCSS::_('jlmslist', 'jlms_homepage_list');?>" style="margin-top:0; margin-bottom:0;">
		<tr>
			<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" colspan="2" align="center" style="text-align:center"><?php echo str_replace('{Y}', $lists['dropbox_total'], str_replace('{X}', $lists['dropbox_total_new'], _JLMS_HOME_DROPBOX_TITLE));?></<?php echo JLMSCSS::tableheadertag();?>>
		</tr>
		<?php
			$k = 1;
			if (count($my_dropbox)) {
				foreach ($my_dropbox as $my_dropboxitem) {
					$link 	= sefRelToAbs("index.php?option=".$option."&amp;Itemid=".$Itemid."&amp;task=dropbox&amp;id=". $my_dropboxitem->course_id);
					$description = & joomla_lms_html::PrepareDescription($my_dropboxitem->drp_description);
					$add_info = '';
					if ( isset($my_dropboxitem->course_name) && $my_dropboxitem->course_name ) {
						$add_info = "&nbsp;(".$my_dropboxitem->course_name.")";
					}
					$title = JLMS_txt2overlib($my_dropboxitem->drp_name);
					echo "<tr class='".JLMSCSS::_('sectiontableentry'.$k)."'><td valign='middle' width='16'>"
					."<img class='JLMS_png' src=\"".$JLMS_CONFIG->get('live_site')."/components/com_joomla_lms/lms_images/toolbar/tlb_dropbox.png\" width='16' height='16' border='0' alt='dropbox' />"
					."</td><td>"
					.JLMS_toolTip($title, $description, $title, $link, 1, 120, 'false', 'jlms_ttip_posts')
					.$add_info."</td></tr>";
					$k = 3 - $k;
				}
			} else {
				echo "<tr class='".JLMSCSS::_('sectiontableentry'.$k)."'><td colspan='2'>"._JLMS_HOME_DROPBOX_NO_ITEMS."</td></tr>";
			} ?>
	</table>
<?php
	}
	
	function echoMyMailBox( $option, $Itemid, &$my_mailbox, &$lists ) {
		global $JLMS_CONFIG;
		
		$unread = 0;
		$all = count($my_mailbox);
		if(count($my_mailbox)){
			foreach($my_mailbox as $m){
				if(!$m->is_read){
					$unread++;
				}
			}
		}
	?>
		<table class="<?php echo JLMSCSS::_('jlmslist', 'jlms_homepage_list');?>" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-top:0; margin-bottom:0;">
			<tr>
				<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center" colspan="2" style="text-align:center">
					<?php echo str_replace("( X / Y )", "( ".$unread." / ".$all." )", _JLMS_HOME_MAILBOX_TITLE);?>
				</<?php echo JLMSCSS::tableheadertag();?>>
			</tr>
			<?php
			$k=1;
			if(count($my_mailbox)){
				foreach($my_mailbox as $my_mailboxitem){
					
					$subject = $my_mailboxitem->subject;
					$from = _JLMS_HOME_MAILBOX_FROM.': '.$my_mailboxitem->from_name.' ('.$my_mailboxitem->from_username.')';
					
					$link = 'index.php?option='.$option.'&task=mail_view';
					$link .= $my_mailboxitem->course_id ? '&id='.$my_mailboxitem->course_id : '';
					$link .= '&view_id='.$my_mailboxitem->id;
					$link .= '&Itemid='.$Itemid;
					
					$link = JRoute::_($link);
					
					if($my_mailboxitem->is_read){
						$img = 'btn_drp_readed.png';
					} else {
						$img = 'btn_drp_unreaded.png';
					}
					?>
					<tr class="<?php echo JLMSCSS::_('sectiontableentry'.$k);?>">
						<td width="16">
							<img class="JLMS_png" src="<?php echo $JLMS_CONFIG->get('live_site')."/components/com_joomla_lms/lms_images/toolbar/".$img;?>" width='16' height='16' border='0' alt='mailbox' />
						</td>
						<td>
							<?php echo JLMS_toolTip($subject, $from, $subject, $link, 1, 120, 'false', 'jlms_ttip_posts');?>
						</td>
					</tr>
					<?php
				}
			} else {
				?>
				<tr class="<?php echo JLMSCSS::_('sectiontableentry'.$k);?>">
					<td colspan="2">
						<?php echo _JLMS_HOME_MAILBOX_NO_ITEMS;?>
					</td>
				</tr>
				<?php
			}
			?>
		</table>
<?php
	}
	
	function echoMyCertificates( $option, $Itemid, &$my_certificates, &$lists ) {
		global $JLMS_CONFIG;
		
	?>
		<table class="<?php echo JLMSCSS::_('jlmslist', 'jlms_homepage_list');?>" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-top:0; margin-bottom:0;">
			<tr>
				<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center" colspan="2" style="text-align:center">
					<?php echo _JLMS_HOME_CERTIFICATES_TITLE;?>
				</<?php echo JLMSCSS::tableheadertag();?>>
			</tr>
			<?php
			$k = 1;
			if(count($my_certificates)){
				foreach($my_certificates as $my_certificateitem){
					
					$subject = _JLMS_DATE.' '.JLMS_dateToDisplay($my_certificateitem->crt_date);
					$from = '';
					if(isset($my_certificateitem->uniq_id) && $my_certificateitem->uniq_id){
						$from = strtoupper($my_certificateitem->uniq_id);
						$from = _JLMS_HOME_CERTIFICATES_SN.': <b>'.$from.'</b>';
					}
					$in_tag = $my_certificateitem->course_name;
					
					$link = 'index.php?option='.$option.'&task=gradebook';
					$link .= $my_certificateitem->course_id ? '&id='.$my_certificateitem->course_id : '';
					$link .= '&Itemid='.$Itemid;
					$link = JRoute::_($link);
					
					$img = 'btn_certificate.png';
					?>
					<tr class="<?php echo JLMSCSS::_('sectiontableentry'.$k);?>">
						<td width="16">
							<img class="JLMS_png" src="<?php echo $JLMS_CONFIG->get('live_site')."/components/com_joomla_lms/lms_images/toolbar/".$img;?>" width='16' height='16' border='0' alt='certificate' />
						</td>
						<td>
							<?php echo JLMS_toolTip($subject, $from, $in_tag, $link, 1, 120, 'false', 'jlms_ttip_posts');?>
						</td>
					</tr>
					<?php
					$k = 3 - $k;
				}
			} else {
				?>
				<tr class="<?php echo JLMSCSS::_('sectiontableentry'.$k);?>">
					<td colspan="2">
						<?php echo _JLMS_HOME_CERTIFICATES_NO_ITEMS;?>
					</td>
				</tr>
				<?php
			}
			?>
		</table>
<?php
	}
	
	function echoLatestForumPosts( $option, $Itemid, &$latest_forum_posts, &$lists ) {
		global $JLMS_CONFIG;
		
	?>
		<table class="<?php echo JLMSCSS::_('jlmslist', 'jlms_homepage_list');?>" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-top:0; margin-bottom:0;">
			<tr>
				<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center" colspan="2" style="text-align:center">
					<?php echo _JLMS_HOME_LATEST_FORUM_POSTS_TITLE;?>
				</<?php echo JLMSCSS::tableheadertag();?>>
			</tr>
			<?php
			$k = 1;
			if(count($latest_forum_posts)){
				foreach($latest_forum_posts as $lfpitem){
					
					$subject = $lfpitem->subject;
					$body = '';
					if(isset($lfpitem->posterTime) && $lfpitem->posterTime){
						$body = _JLMS_DATE.' '.JLMS_dateToDisplay(date("Y-m-d H:i:s", $lfpitem->posterTime));
					}
					
					$body .= "<br />"._JLMS_COURSE.': '.$lfpitem->course_name;
					$body .= "<br />"._JLMS_TOPIC.': '.$lfpitem->topic_name;
					$body .= "<br />"._JLMS_AUTHOR.': '.$lfpitem->poster_name;
					
					$in_tag = strlen($lfpitem->body) > 90 ? substr($lfpitem->body, 0, 90).'...' : $lfpitem->body;				
					
					$link = 'index.php?option='.$option.'&task=course_forum';
					$link .= isset($lfpitem->course_id) && $lfpitem->course_id ? '&id='.$lfpitem->course_id : '';
					$link .= '&topic_id='.$lfpitem->ID_TOPIC;
					$link .= '&message_id='.$lfpitem->ID_MSG;
					$link = JRoute::_($link);
					
					$img = 'btn_notice.png';
					?>
					<tr class="<?php echo JLMSCSS::_('sectiontableentry'.$k);?>">
						<td width="16">
							<img class="JLMS_png" src="<?php echo $JLMS_CONFIG->get('live_site')."/components/com_joomla_lms/lms_images/toolbar/".$img;?>" width='16' height='16' border='0' alt='certificate' />
						</td>
						<td>
							<?php echo JLMS_toolTip($subject, $body, $in_tag, $link, 1, 120, 'false', 'jlms_ttip_posts');?>
						</td>
					</tr>
					<?php
					$k = 3 - $k;
				}
			} else {
				?>
				<tr class="<?php echo JLMSCSS::_('sectiontableentry'.$k);?>">
					<td colspan="2">
						<?php echo _JLMS_HOME_LATEST_FORUM_POSTS_NO_ITEMS;?>
					</td>
				</tr>
				<?php
			}
			?>
		</table>
<?php
	}
	
	function echoMyCourses( $option, $Itemid, &$my_courses) {
		global $JLMS_CONFIG;
	?>
	<table width="100%" cellpadding="0" cellspacing="0" border="0" class="<?php echo JLMSCSS::_('jlmslist', 'jlms_homepage_list');?>" style="margin-top:0; margin-bottom:0;">
		<tr><<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center" style="text-align:center"><?php echo _JLMS_HOME_COURSES_TITLE;?></<?php echo JLMSCSS::tableheadertag();?>></tr>
	<?php
		$k = 1;
		if (count($my_courses)) {
			$my_courses_teach = array();
			$my_courses_enroll = array();
			foreach ($my_courses as $my_course) {
				if ($my_course->user_course_role == 1) {
					$my_courses_teach[] = $my_course;
				} else {
					$my_courses_enroll[] = $my_course;
				}
			}
			if (count($my_courses_teach)) {
				echo "<tr class='".JLMSCSS::_('sectiontableentry'.$k)."'><td align='left'>"._JLMS_COURSE_FP_COURSES_TEACH."</td></tr>";
				$k = 3 - $k;
				foreach ($my_courses_teach as $my_course) {
					$link 	= sefRelToAbs("index.php?option=".$option."&amp;Itemid=".$Itemid."&amp;task=details_course&amp;id=". $my_course->id);
					$description = & joomla_lms_html::PrepareDescription($my_course->course_description);
					$title = JLMS_txt2overlib($my_course->course_name);
					echo "<tr class='sectiontableentry$k".(($k % 2) ? ' odd' : ' even')."'><td>
					".JLMS_toolTip($title, $description, $link)."
					</td></tr>";
					$k = 3 - $k;
				}
			}
			if (count($my_courses_enroll)) {
				echo "<tr class='".JLMSCSS::_('sectiontableentry'.$k)."'><td align='left'>"._JLMS_COURSE_FP_COURSES_ENROLL."</td></tr>";
				$k = 3 - $k;
				foreach ($my_courses_enroll as $my_course) {
					$link 	= sefRelToAbs("index.php?option=".$option."&amp;Itemid=".$Itemid."&amp;task=details_course&amp;id=". $my_course->id);
					$description = & joomla_lms_html::PrepareDescription($my_course->course_description);
					$title = JLMS_txt2overlib($my_course->course_name);
					echo "<tr class='sectiontableentry$k".(($k % 2) ? ' odd' : ' even')."'><td>
					".JLMS_toolTip($title, $description, $link)."
					</td></tr>";
					$k = 3 - $k;
				}
			}
		} else {
			echo "<tr class='sectiontableentry$k".(($k % 2) ? ' odd' : ' even')."'><td>"._JLMS_HOME_COURSES_NO_ITEMS."</td></tr>";
		} ?>
	</table>
	<?php
	}
	function echoMyCourses_tree( $option, $Itemid, &$my_courses, &$lists){
		global $my, $JLMS_CONFIG, $Itemid;
		
		$cookies = array();
		$rows_c = array();
		if($my->id && isset($_COOKIE['flms_my_course_tree_'.$my->id])){
			$cookies = $_COOKIE['flms_my_course_tree_'.$my->id];
			$rows_c = explode(",", $cookies);
		} else {
			$i=0;
			foreach($my_courses as $data){
				if($data->folder_flag){
					$i++;
				}
			}
			if($i == 1){
				$rows_c = array();
			} else {
				foreach($my_courses as $data){
					if(isset($data->folder_flag)){
						$rows_c[] = $data->id;	
					}
				}
			}
		}
		
		//(Max): if one course then open all categories
		$count_course = 0;
		foreach($my_courses as $c){
			if(isset($c->is_course) && $c->is_course){
				$count_course++;	
			}
		}
		if($count_course == 1 || $JLMS_CONFIG->get('frontpage_courses_expand_all', 0)){
			$rows_c = array();	
		}
		//(Max): if one course then open all categories
	?>
	<script language="javascript" type="text/javascript">
		var TreeArray1 = new Array();
		var TreeArray2 = new Array();
		var Is_ex_Array = new Array();
		<?php
		$i = 1;
		foreach ($my_courses as $row) {
//			$row->id = isset($row->c_id)?$row->c_id:$row->id;
			$row->id = $row->id;
			echo "TreeArray1[".$i."] = ".$row->parent.";" . "\n";
			echo "TreeArray2[".$i."] = ".($row->id).";" . "\n";
			if (in_array($row->id, $rows_c)) {
				echo "Is_ex_Array[".$i."] = 0;" . "\n";
			} else {
				echo "Is_ex_Array[".$i."] = 1;" . "\n";
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
					imgStr = imgStr.replace('chapter_expand.png','chapter_collapse.png').replace('<?php echo '+';?>', '<?php echo '-';?>');
					StStr = StStr.replace('chapter_expand.png','chapter_collapse.png');
					getObj('tree_img_' + fid).outerHTML = imgStr;
					getObj('tree_img_' + fid).runtimeStyle.filter = StStr;
				} else {
					getObj('tree_img_' + fid).src = '<?php echo $JLMS_CONFIG->getCfg('live_site');?>/components/com_joomla_lms/lms_images/learnpath/chapter_collapse.png';
					getObj('tree_img_' + fid).alt = '<?php echo _JLMS_MULTICAT_COLLAPSE;?>';
					getObj('tree_img_' + fid).title = '<?php echo _JLMS_MULTICAT_COLLAPSE;?>';
				}
			} else {
				Hide_Folder(fid);
				if (getObj('tree_img_' + fid).runtimeStyle) {
					var StStr = getObj('tree_img_' + fid).runtimeStyle.filter;
					var imgStr = getObj('tree_img_' + fid).outerHTML;
					imgStr = imgStr.replace('chapter_collapse.png','chapter_expand.png').replace('<?php echo '+';?>', '<?php echo '-';?>');
					StStr = StStr.replace('chapter_collapse.png','chapter_expand.png');
					getObj('tree_img_' + fid).outerHTML = imgStr;
					getObj('tree_img_' + fid).runtimeStyle.filter = StStr;
				} else {
					getObj('tree_img_' + fid).src = '<?php echo $JLMS_CONFIG->getCfg('live_site');?>/components/com_joomla_lms/lms_images/learnpath/chapter_expand.png';
					getObj('tree_img_' + fid).alt = '<?php echo _JLMS_MULTICAT_EXPAND;?>';
					getObj('tree_img_' + fid).title = '<?php echo _JLMS_MULTICAT_EXPAND;?>';
				}
			}
			
			var savePosition;
			var tmp_savePosition = new Array();
			
			i = 1;
			k = 0;
			while (i < TreeArray2.length) {
				if ( (TreeArray2[i] == fid) ) {
					if(Is_ex_Array[i] == 1){ 
						Is_ex_Array[i] = 0;
					} else { 
						Is_ex_Array[i] = 1; 
					}
				}
				if( parseInt(TreeArray2[i]) > 0 && Is_ex_Array[i] == 0){
					tmp_savePosition[k] = TreeArray2[i];	
					k++;
				}
				i ++;
			}
			savePosition = js_implode(",", tmp_savePosition);
			if(<?php echo $my->id;?>){
				<?php
				$now_year = date("Y");
				$expire_year = intval($now_year) + 1;
				$expire_date = date("D, d-F-Y H:i:s", mktime(0, 0, 0, 1, 5, $expire_year)).' GMT';
				?>
				tree_setCookie('flms_my_course_tree_<?php echo $my->id;?>', savePosition, '/', '<?php echo $expire_date;?>');
			}
		}
		
		function tree_setCookie(name, value, path, expires, domain, secure) {
		  var curCookie = name + "=" + (value) +
			((expires) ? "; expires=" + expires : "") +
			((path) ? "; path=" + path : "; path=/") +
			((domain) ? "; domain=" + domain : "") +
			((secure) ? "; secure" : "");
		  document.cookie = curCookie;
		}
		
		function js_implode( glue, pieces ) {
		    return ( ( pieces instanceof Array ) ? pieces.join ( glue ) : pieces );
		}
	</script>
	<?php
		$max_tree_width = 0; if (isset($my_courses[0])) {$max_tree_width = $my_courses[0]->tree_max_width;}
	?>
	<table width="100%" cellpadding="0" cellspacing="0" border="0" class="<?php echo JLMSCSS::_('jlmslist', 'jlms_homepage_modules');?>" style="margin-top:0; margin-bottom:0;">
		<tr><<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center" style="text-align:center"><?php echo _JLMS_HOME_COURSES_TITLE;?></<?php echo JLMSCSS::tableheadertag();?>></tr>
		<tr><td style="border: none; border-collapse: 0px; margin:0px;padding:0px">
			<table width="100%" cellpadding="0" cellspacing="0" border="0" class="<?php echo JLMSCSS::_('jlmslist', 'jlms_homepage_list');?>" style="margin-top:0; margin-bottom:0;">
				<?php
				$k = 1;
				$tree_modes = array();
				$visible_folder = true;
				//$next_row_is_visible = true;
				$vis_mode = 0;
				for ($i=0, $n=count($my_courses); $i < $n; $i++) {
					$row = $my_courses[$i];
					$max_tree_width = $row->tree_max_width;
					$link = ''; $link_title = '';
					
					if($row->is_course){
						$link_course = sefRelToAbs("index.php?option=".$option."&task=details_course&id=".$row->c_id."&Itemid=".$Itemid);
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
					/*if($one_course == 1){
						$tree_row_style = '';
					}*/
					if($row->folder_flag == 1){
						$k = 2;	
					} else {
						$k = 1;	
					}
					?>
					<tr id="tree_row_<?php echo ($row->id);?>" class="<?php echo JLMSCSS::_('sectiontableentry'.$k); ?>"<?php echo $tree_row_style;?>>
						<!--<td align="center" valign="middle" width="5%"><?php #echo ( $i + 1 ); ?></td>-->
						<?php $add_img = '';
						if ($row->tree_mode_num) {
							$g = 0;
							$tree_modes[$row->tree_mode_num - 1] = $row->tree_mode;
							while ($g < ($row->tree_mode_num - 1)) {
								$pref = '';
								if (isset($tree_modes[$g]) && ($tree_modes[$g] == 2) ) { $pref = 'empty_'; }
								$add_img .= "<td width='1%' valign='middle'><img src='".$JLMS_CONFIG->get('live_site')."/components/com_joomla_lms/lms_images/treeview/".$pref."line.png' width='16' height='16' alt='".$pref."line' /></td>";
								$g ++;
							}
							$add_img .= "<td width='1%' valign='middle'><img src='".$JLMS_CONFIG->get('live_site')."/components/com_joomla_lms/lms_images/treeview/sub".$row->tree_mode.".png' width='16' height='16' alt='sub".$row->tree_mode."' /></td>";
							$max_tree_width = $max_tree_width - $g - 1;
						}
						echo $add_img;?>
						<td width="1%" align="center" valign="middle" >
						<?php if ($row->folder_flag == 1) {
							$collapse_img = 'chapter_collapse.png';
							$collapse_alt = _JLMS_MULTICAT_COLLAPSE;
							if (in_array($row->id, $rows_c)) {
								$collapse_img = 'chapter_expand.png';
								$collapse_alt = _JLMS_MULTICAT_EXPAND;
							}
							echo "<span id='tree_div_".$row->id."' style='alignment:center; width:16px; font-weight:bold; cursor:pointer; vertical-align:middle;' onclick='Ex_Folder(".$row->id.",".$row->id.",true)'><img class='JLMS_png' id='tree_img_".$row->id."' src='".$JLMS_CONFIG->get('live_site')."/components/com_joomla_lms/lms_images/learnpath/$collapse_img' width='16' height='16' alt='".$collapse_alt."' title='".$collapse_alt."' /></span>";
						} else if($row->is_course){
							$icon_course = 'tlb_courses';
							if($row->certificate){
//								$icon_course = 'btn_certificate';	
								$icon_course = 'btn_accept';	
							}
							echo "<span style='alignment:center; width:16px; font-weight:bold; vertical-align:middle;'><img class='JLMS_png' src='".$JLMS_CONFIG->get('live_site')."/components/com_joomla_lms/lms_images/toolbar/".$icon_course.".png' width='16' height='16' alt='' title='' /></span>";
						} else {
							echo "<span style='alignment:center; width:16px; font-weight:bold; vertical-align:middle;'><img class='JLMS_png' src='".$JLMS_CONFIG->get('live_site')."/components/com_joomla_lms/lms_images/files/file_none.png' width='16' height='16' alt='' title='' /></span>";
						}?>
						</td>
						<td width="100%" align="left" valign="middle" <?php if ($max_tree_width > 0) { echo "colspan='".($max_tree_width + 1)."'";} ?>>
						<?php if ($row->folder_flag == 1) {
							echo "<span style='font-weight:bold; cursor:pointer; vertical-align:middle;' onclick='Ex_Folder(".$row->id.",".$row->id.",true)'>";
							echo '&nbsp;<strong>'.$row->name.'</strong>';
							echo "</span>";
						} else if($row->is_course == 0) { ?>
							<span style='font-weight:bold; vertical-align:middle;'>
								<?php echo $row->name;?>
							</span>
						<?php 
						} else { ?>
							<a href="<?php echo $link_course;?>" title="">
								<?php echo $row->name;?>
							</a>
						<?php
						}
						?>
						</td>
						<?php
						if($JLMS_CONFIG->get('flms_integration', 0)){
							if(isset($row->lesson_type) && $row->lesson_type == 2){
							?>
							<td width="3%">
								<?php echo $row->pf_time;?>
							</td>
							<td width="3%">
								<?php echo $row->pm_time;?>
							</td>
							<td width="3%">
								<?php echo $row->total_time;?>
							</td>
							<?php
							} else {
							?>
							<td width="3%">
								&nbsp;
							</td>
							<td width="3%">
								&nbsp;
							</td>
							<td width="3%">
								&nbsp;
							</td>
							<?php
							}
						}
						?>
					</tr>
					<?php
//					$k = 3 - $k;
				}
				?>
			</table>
		</td></tr>
	</table>
	<?php
	}
	
	function echoMyNotices($option, $Itemid, $my_notices){
		global $Itemid, $JLMS_CONFIG;
		
		$link_base = 'index.php?option=com_joomla_lms&amp;Itemid='.$Itemid;
		$link_notices = $link_base;
		$link_notices .= '&amp;task=view_all_notices';
		?>
		<table width="100%" cellpadding="0" cellspacing="0" border="0" class="<?php echo JLMSCSS::_('jlmslist', 'jlms_homepage_list');?>" style="margin-top:0; margin-bottom:0;">
		<tr><<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" colspan="2" align="center" style="text-align:center"><?php echo _JLMS_HOME_NOTICES;?></<?php echo JLMSCSS::tableheadertag();?>></tr>
		<?php
		$k=1;
		foreach($my_notices as $m_n){
			$link = $link_base;
			if($m_n->doc_id){
				$link .= '&amp;task='.$m_n->task.'&amp;course_id='.$m_n->course_id.'&amp;id='.$m_n->doc_id.'';	
			} else {
				$link .= '&amp;task='.$m_n->task.'&amp;id='.$m_n->course_id.'';	
			}
		?>
		<tr class="<?php echo JLMSCSS::_('sectiontableentry'.$k); ?>">
			<td valign="top" width="16" style="padding-top: 5px;">
				<img class='JLMS_png' src="<?php echo $JLMS_CONFIG->get('live_site')."/components/com_joomla_lms/lms_images/toolbar/btn_notice.png";?>" width="16" height="16" border="0" alt="dropbox" />
			</td>
			<td>
				<div class="notes_head">
					on <?php 
					echo JLMS_dateToDisplay($m_n->data, false, 0, " H:m:s");
					?>
				</div>
				<div class="clr"></div>
				<div class="notes_foot">
					<?php
					if(!$m_n->course_id && !$m_n->doc_id){
						if(strlen($m_n->notice) > 100){
							echo substr($m_n->notice, 0, 100).' ...';	
						} else {
							echo substr($m_n->notice, 0, 100);	
						}
					} else {
						?>
						<a href="<?php echo ampReplace(sefRelToAbs($link));?>" title="<?php echo substr($m_n->notice, 0, 30);?>">
						<?php
							if(strlen($m_n->notice) > 100){
								echo substr($m_n->notice, 0, 100).' ...';	
							} else {
								echo substr($m_n->notice, 0, 100);	
							}
							?>
						</a>
						<?php
					}
					?>
				</div>
			</td>
		</tr>
		<?php
			$k = 3 - $k;
		}
		?>
		<tr>
			<td colspan="2" align="right" class="<?php echo JLMSCSS::_('jlmslist-footer_td');?>">
				<a href="<?php echo sefRelToAbs($link_notices);?>" title="view all notices" style="font-size: 9px;">
					view all notes
				</a>
			</td>
		</tr>
		</table>
		<?php	
	}
	function showCEO_page( $option, &$lists, &$rows ) {
		global $Itemid, $JLMS_CONFIG; ?>
		
		<table width="100%" cellpadding="0" cellspacing="3" border="0" class="jlms_table_no_borders">
			<tr>
				<td align="right"><?php JLMS_showTopMenu_simple($option);?></td>
			</tr>
			<tr>
				<td>
				<table width="100%" cellpadding="0" cellspacing="0" border="0" class="<?php echo JLMSCSS::_('jlmslist');?>">
				<tr>
					<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" width="16">&nbsp;</<?php echo JLMSCSS::tableheadertag();?>>
					<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" width="16">&nbsp;</<?php echo JLMSCSS::tableheadertag();?>>
					<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>"><?php echo _JLMS_CEO_PAGE_LEARNER_COURSE_TITLE;?></<?php echo JLMSCSS::tableheadertag();?>>
				</tr>
				<?php
				$user_id = 0;
				$is_open = false;
				$i = 0;
				$k = 1;
				foreach ($rows as $row) {
					if ($row->user_id != $user_id) {
						echo '<tr class="'.$JLMS_CONFIG->get('visual_set_main_row_class', ('sectiotableentry'.$k)).'"><td colspan="3">'.$row->username.', '.$row->name.' ('.$row->email.')'."</td></tr>\n";
						$k = 3 - $k;
						$user_id = $row->user_id;
					}
					if ($row->course_id) {
						$sub_index = 2;
						if ((isset($rows[$i + 1]) && $rows[$i + 1]->user_id == $row->user_id)) {
							$sub_index = 1;
						}
						$completion_image = 'spacer';
						if (isset($row->course_completion)) {
							if ($row->course_completion == 1) {
								$completion_image = 'toolbar/btn_accept';
							} elseif ($row->course_completion == 2) {
								$completion_image = 'toolbar/btn_certificate';
							}
						}
						echo '<tr class="'.$JLMS_CONFIG->get('visual_set_child_row_class', ('sectiotableentry'.$k)).'">';
						echo '<td><img src="'.$JLMS_CONFIG->get('live_site').'/components/com_joomla_lms/lms_images/treeview/sub'.$sub_index.'.png" width="16" height="16" border="0" alt="sub" /></td>';
						echo '<td><img src="'.$JLMS_CONFIG->get('live_site').'/components/com_joomla_lms/lms_images/'.$completion_image.'.png" width="16" height="16" border="0" alt="sub" /></td>';
						echo '<td><a href="'.sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid&amp;task=enter_ceo&amp;course_id={$row->course_id}").'">'.$row->course_name.'</a></td></tr>';
						$k = 3 - $k;
					}
					$i ++;
				}
				?>
				</table>
				</td>
			</tr>
		</table>
	<?php
	}
	function showSR_page( $option, $course_id, &$prepared_questions ) {
		global $Itemid, $JLMS_CONFIG;?>
<script language="javascript" type="text/javascript">
<!--//--><![CDATA[//><!--
function submitbutton(pressbutton) {
	var form = document.adminForm;
	if (pressbutton == 'spec_reg_answer'){
		form.task.value = pressbutton;
		form.submit();
	}
}
//--><!]]>
</script>
	<form action="<?php echo sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid");?>" method="post" name="adminForm">
		<table width="100%" cellpadding="0" cellspacing="0" border="0" class="jlms_table_no_borders">
<?php
	foreach ($prepared_questions as $srq) {
		echo '<tr>';
		echo '<td width="30%" align="left">';
		echo $srq->course_question.(!$srq->is_optional?' <font color="red">*</font>':'');
		echo '</td>';
		echo '<td>';
		echo '<input type="hidden" name="user_answer_id[]" value="'.$srq->id.'" />';
		echo '<input class="inputbox" type="text" name="user_answer[]" size="40"'.((!empty($srq->is_answered) && $srq->is_answered) ? ' readonly="readonly"' : '').' value="'.(!empty($srq->default_answer) ? $srq->default_answer : '').'" />';
		echo '</td>';
		echo '</tr>';
	}?>
			<tr>
				<td align="center" colspan="2">
				<br /><input class="inputbox" type="button" name="OK" value="OK" onclick="submitbutton('spec_reg_answer');" />
				</td>
			</tr>
		</table>
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="Itemid" value="<?php echo $Itemid; ?>" />
		<input type="hidden" name="id" value="<?php echo $course_id; ?>" />
		<input type="hidden" name="task" value="spec_reg_answer" />
	</form>
	<?php
	}
	
	function ShowStatusAs($row){
		$JLMS_CONFIG = & JLMSFactory::getConfig();
		
		if($row->item_id){ //scorm
			switch($JLMS_CONFIG->get('scorm_status_as', 0)){
				case 0:
					require_once(_JOOMLMS_FRONT_HOME . "/includes/lms_grades.lib.php");
					echo JLMS_getLpathProgress($row);
				break;
				case 1:
					if(isset($row->s_status) && $row->s_status){
						echo '<b>'._JLMS_LPATH_STU_LPSTATUS_COMPLETED.'</b>';
					} else {
						echo '<b>'._JLMS_LPATH_STU_LPSTATUS_NOTCOMPLETED.'</b>';
					}
				break;
				case 2:
					$r_img = 'btn_cancel';
					$r_sta = _JLMS_LPATH_STU_LPSTATUS_NOTCOMPLETED;
					if(isset($row->s_status) && $row->s_status){
						$r_img = 'btn_accept';
						$r_sta = _JLMS_LPATH_STU_LPSTATUS_COMPLETED;
					} else 
					if(isset($row->s_status) && $row->s_status == 0){
						$r_img = 'btn_pending_cur';
					}
					echo '<img class=\'JLMS_png\' src="'.$JLMS_CONFIG->get('live_site').'/components/com_joomla_lms/lms_images/toolbar/'.$r_img.'.png" width=\'16\' height=\'16\' alt="'.$r_sta.'" title="'.$r_sta.'" />';
				break;	
			}
		} else { //lpath
			switch($JLMS_CONFIG->get('lpath_status_as', 0)){
				case 0:
					require_once(_JOOMLMS_FRONT_HOME . "/includes/lms_grades.lib.php");
					echo JLMS_getLpathProgress($row);
				break;
				case 1:
					if($row->r_status){
						echo '<b>'._JLMS_LPATH_STU_LPSTATUS_COMPLETED.'</b>';
					} else {
						echo '<b>'._JLMS_LPATH_STU_LPSTATUS_NOTCOMPLETED.'</b>';
					}
				break;
				case 2:
					$r_img = 'btn_cancel';
					$r_sta = _JLMS_LPATH_STU_LPSTATUS_NOTCOMPLETED;
					if(isset($row->r_status) && $row->r_status){
						$r_img = 'btn_accept';
						$r_sta = _JLMS_LPATH_STU_LPSTATUS_COMPLETED;
					} else 
					if(isset($row->r_status) && $row->r_status == 0){
						$r_img = 'btn_pending_cur';
					}
					echo '<img class=\'JLMS_png\' src="'.$JLMS_CONFIG->get('live_site').'/components/com_joomla_lms/lms_images/toolbar/'.$r_img.'.png" width=\'16\' height=\'16\' alt="'.$r_sta.'" title="'.$r_sta.'" />';
				break;
			}
		}
	}
}
?>