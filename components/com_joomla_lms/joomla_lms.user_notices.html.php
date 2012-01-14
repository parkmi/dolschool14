<?php
defined( '_JLMS_EXEC' ) or die( 'Restricted access' );

class FLMS_page_notice {
		
	function new_notice($option, $notices, $ntask, $doc_id, $course_id, $row = array()){
		global $Itemid;
		?>
		<form action="" method="POST" name="form_pgnotice">
		<table cellpadding="0" cellspacing="0" width="80%" align="center">
			<tr>
				<th colspan="2" class="sectiontableheader">
					<?php echo isset($row->id) ? _JLMS_USER_OPTIONS_NOTES_EDIT : _JLMS_USER_OPTIONS_NOTES_ADD;?> <?php echo _JLMS_USER_OPTIONS_NOTES_NOTICE;?>
				</th>
			</tr>
			<tr>
				<td colspan="2">
					<textarea name="p_notice" id="p_notice" style="width:400px; height:120px;"><?php if(count($row)) { echo $row->notice; } ?></textarea>
				</td>
			</tr>
			<tr>
				<td>
					<table border="0" cellpadding="0" cellspacing="3" align="right">
						<tr>
							<td>
								<?php 
								if(count($row)) {
									echo '<input type="hidden" name="v_id" value="'.$row->id.'">'; 
									?>
									<input type="button" value="<?php echo _JLMS_SAVE_ALT_TITLE?>" onclick="pn_validate_edit();" />
									<?php
								}
								else {
									?>
									<input type="button" value="<?php echo _JLMS_SAVE_ALT_TITLE?>" onclick="pn_validate();" />
									<?php
								}
								?>
							</td>
							<td>
								<input type="button" value="<?php echo _JLMS_CLOSE_ALT_TITLE?>" onclick="$('sbox-btn-close').fireEvent('click');" />
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		</form>
		<?php
		FLMS_page_notice::view_notice($notices, $option, $ntask, $doc_id, $course_id);
		die();
	}

	function new_notice_no_ajax($option, $notices, $ntask, $doc_id, $course_id, $lists, $row = array()){
		global $JLMS_CONFIG, $Itemid, $task;

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
	function setgood() {
		return true;
	}
	function submitbutton(pressbutton) {
		var form = document.form_pgnotice;
		try {
			form.onsubmit();
		} catch(e) {
			//alert(e);
		}
		form.task.value = pressbutton;
		form.submit();
	}
	
	var old_filters = new Array();
	function read_filter(){
		var form = document.form_pgnotice;
		var count_levels = '<?php echo count($lists['levels']);?>';
		for(var i=0;i<parseInt(count_levels);i++){
			if(form['filter_id_'+i] != null){
				old_filters[i] = form['filter_id_'+i].value;
			}
		}
	}
	function write_filter(){
		var form = document.form_pgnotice;
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
</script>
		<form action="<?php echo $JLMS_CONFIG->get('live_site') . "/index.php?option=$option&amp;Itemid=$Itemid";?>" method="POST" name="form_pgnotice" onsubmit="setgood();">
		<?php
		JLMS_TMPL::OpenMT();
		$params = array(
			'show_menu' => true,
			'simple_menu' => true,
		);
		JLMS_TMPL::ShowHeader('doc', isset($row->id)?'Edit':'Add'.' notice', $params);
		
		$controls = array();
		$controls[] = array('href' => "javascript:submitbutton('save_notice_no_ajax');", 'title' => 'Save Notice', 'img' => 'save');
		$controls[] = array('href' => $JLMS_CONFIG->get('live_site')."/index.php?option=$option&amp;Itemid=$Itemid&amp;task=view_all_notices", 'title' => 'Back', 'img' => 'back');
		JLMS_TMPL::ShowControlsFooter($controls, '', false);
		?>

		<tr>
			<td align="right">
				<?php
				echo ((isset($lists['levels'][0]->cat_name) && $lists['levels'][0]->cat_name != '')?$lists['levels'][0]->cat_name:_JLMS_COURSES_COURSES_GROUPS)." ".$lists['filter_0'];
				?>
			</td>
		</tr>
		<?php
		if(count($multicat)){
			for($i=0;$i<count($multicat);$i++){
				if($i > 0){
					?>
					<tr>
						<td align="right">
						<?php
							echo ((isset($lists['levels'][$i]->cat_name) && $lists['levels'][$i]->cat_name != '')?$lists['levels'][$i]->cat_name:_JLMS_COURSES_COURSES_GROUPS)." ".$lists['filter_'.$i];
						?>	
						</td>
					</tr>	
					<?php
				}
			}
		}
		if(isset($lists['levels'][0]) || count($multicat)){
			?>
			<tr>
				<td align="right">
					<?php
						echo 'Course: '.$lists['f_course'];
						if(isset($lists['course_available']) && !$lists['course_available']){
							?>
							<input type="hidden" name="course_id" value="<?php echo $lists['course_id'];?>" />
							<?php	
						}
					?>	
				</td>
			</tr>	
			<?php	
		}
		
		JLMS_TMPL::CloseMT(); 
		?>
		<table cellpadding="0" cellspacing="0" width="100%" align="center">
			<tr>
				<td>
					<?php 
					$notice = '';
					if(count($row)) { $notice = $row->notice; }
					JLMS_editorArea( 'editor1', $notice, 'p_notice', '100%;', '250', '40', '20' ) ; ?>
					<!--<textarea name="p_notice" id="p_notice" style="width:400px; height:120px;"><?php #if(count($row)) { echo $row->notice; } ?></textarea>-->
				</td>
			</tr>
		</table>
			<input type="hidden" name="option" value="<?php echo $option;?>" />
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="cid[]" value="<?php echo isset($row->id)?$row->id:'';?>" />
			<input type="hidden" name="v_id" value="<?php echo isset($row->id)?$row->id:'';?>" />
			<input type="hidden" name="ntask" value="<?php echo (isset($row->id) && $row->id) ? $row->task : $task;?>" />
			<?php
			if(isset($row->id)){
				?>
				<!--<input type="hidden" name="course_id" value="<?php #echo $row->course_id;?>" />-->
				<input type="hidden" name="doc_id" value="<?php echo $row->doc_id;?>" />
				<?php	
			}
			?>
			<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
		</form>
		<?php
	}
	function view_notice($notices, $option, $ntask, $doc_id, $course_id){
		?>
		<table cellpadding="0" cellspacing="0" width="80%" align="center">
		<?php
		$link1 = "index.php?tmpl=component&amp;option=$option&amp;ntask=$ntask&amp;doc_id=$doc_id&amp;course_id=$course_id&amp;task=delete_notice";
		$link2 = "index.php?tmpl=component&amp;option=$option&amp;ntask=$ntask&amp;doc_id=$doc_id&amp;course_id=$course_id&amp;task=edit_notice";
		for($i=0;$i<count($notices);$i++)
		{
			?>
			<tr class="sectiontableentry<?php echo (($i%2)+1)?>">
				
				<td width="400">
					<?php echo nl2br($notices[$i]->notice);?>
				</td>
				<td width="30">
					<a href="javascript:void(0);" onclick="pn_del('<?php echo $link1."&amp;v_id=".$notices[$i]->id?>')"><?php echo _JLMS_DELETE;?></a>
				</td>
				<td >
					<a href="javascript:void(0);" onclick="pn_edit('<?php echo $link2."&amp;v_id=".$notices[$i]->id?>')"><?php echo _JLMS_EDIT;?></a>
				</td>
			</tr>
			<?php
		}
		?>
		</table>
		<script>getObj('pn_count').innerHTML = '<?php echo count($notices)?>';</script>
		<?php
	}
	function show_all_notices($option, $lists, $total, $pageNav, $limitstart, $limit){
		global $JLMS_CONFIG, $Itemid;
		
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
		
		$link_pages = "index.php?option=$option&task=view_all_notices";
		?>
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
		function submitbutton(pressbutton) {
			var form = document.adminForm;
			if ((pressbutton == 'delete_notice_no_ajax' || pressbutton == 'edit_notice_no_ajax') && form.boxchecked.value == '0') {
				alert('<?php echo _JLMS_ALERT_SELECT_ITEM;?>');
			} else {
				form.task.value = pressbutton;
				form.submit();
			}
		}
		//--><!]]>
		</script>
		<form action="<?php echo $JLMS_CONFIG->get('live_site')."/index.php?option=$option&amp;Itemid=$Itemid";?>" method="post" name="adminForm">
			<?php
			JLMS_TMPL::OpenMT();
			$params = array(
				'show_menu' => true,
				'simple_menu' => true,
			);
			JLMS_TMPL::ShowHeader('doc', 'View notices', $params);
			
			$controls = array();
			$controls[] = array('href' => $JLMS_CONFIG->get('live_site')."/index.php?option=$option&amp;Itemid=$Itemid&amp;task=new_notice_no_ajax", 'title' => 'New Notice', 'img' => 'add');
			$controls[] = array('href' => "javascript:submitbutton('edit_notice_no_ajax');", 'title' => 'Edit Notice', 'img' => 'edit');
			$controls[] = array('href' => "javascript:submitbutton('delete_notice_no_ajax');", 'title' => 'Delete Notice(s)', 'img' => 'delete');
//			$controls[] = array('href' => 'spacer');
			JLMS_TMPL::ShowControlsFooter($controls, '', false);
			
			JLMS_TMPL::CloseMT(); 
			?>
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td colspan="5">
						<table border="0" cellpadding="0" cellspacing="0" align="right">
							<tr>
								<td align="right">
									<?php
									if ($JLMS_CONFIG->get('multicat_use', 0)){
										echo ((isset($lists['levels'][0]->cat_name) && $lists['levels'][0]->cat_name != '')?$lists['levels'][0]->cat_name:_JLMS_COURSES_COURSES_GROUPS)." ".$lists['filter_0'];
									} else {
										echo _JLMS_COURSES_COURSES_GROUPS." ".$lists['groups_course'];
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
											<td align="right">
											<?php
												echo ((isset($lists['levels'][$i]->cat_name) && $lists['levels'][$i]->cat_name != '')?$lists['levels'][$i]->cat_name:_JLMS_COURSES_COURSES_GROUPS)." ".$lists['filter_'.$i];
											?>	
											</td>
										</tr>	
										<?php
									}
								}
							}
							if(isset($lists['levels'][0]) || count($multicat)){
								?>
								<tr>
									<td align="right">
										<?php
											echo $lists['f_course'];
										?>	
									</td>
								</tr>	
								<?php	
							}
							?>
						</table>
					</td>
				</tr>
				<tr>
					<td colspan="5" align="center">
						<?php echo $pageNav->writePagesCounter().' '.$pageNav->getLimitBox( $link_pages );?>
					</td>
				</tr>
				<tr>
					<td class="sectiontableheader" width="1%">
						#
					</td>
					<td class="sectiontableheader" width="1%">
						<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($lists['my_notices']); ?>);" />
					</td>
					<td class="sectiontableheader">
						Notice
					</td>
					<td class="sectiontableheader" width="20%">
						Course name
					</td>
					<td class="sectiontableheader" width="10%">
						Date
					</td>
				</tr>
				<?php
				$link_base = 'index.php?option=com_joomla_lms';
				$z	= $limitstart;
				$end 	= $limit + $z;
				if ( $end > $total ){ 	
					$end = $total + 1;
				}
				$k=1;
				for( $i=$z; $i < $end; $i++ ){
					if(isset($lists['my_notices'][$i])){
						$m_n = $lists['my_notices'][$i];
						
						$link = $link_base;
						if($m_n->doc_id){
							$link .= '&task='.$m_n->task.'&course_id='.$m_n->course_id.'&id='.$m_n->doc_id.'';	
						} else {
							$link .= '&task='.$m_n->task.'&id='.$m_n->course_id.'';	
						}
						$checked = mosHTML::idBox( $i, $m_n->id);
						?>
						<tr class="<?php echo "sectiontableentry$k"; ?>">
							<td align="center" width="1%"><?php echo ( $pageNav->limitstart + $i + 1 );?></td>
							<td align="center" width="1%"><?php echo $checked;?></td>
							<td style="text-align: justify;">
								<?php
								if(!$m_n->course_id && !$m_n->doc_id){
									/*
									if(strlen($m_n->notice) > 100){
										echo substr($m_n->notice, 0, 100).' ...';	
									} else {
										echo substr($m_n->notice, 0, 100);	
									}
									*/
									echo $m_n->notice;
								} else {
								?>
								<a href="<?php echo sefRelToAbs($link);?>" title="<?php echo str_replace('"', '&quot;',strip_tags(substr($m_n->notice, 0, 30)));?>">
									<?php
										/*
										if(strlen($m_n->notice) > 100){
											echo substr($m_n->notice, 0, 100).' ...';	
										} else {
											echo substr($m_n->notice, 0, 100);	
										}
										*/
										echo $m_n->notice;
									?>
								</a>
								<?php
								}
								?>
							</td>
							<td>
								<?php
									echo $m_n->course_name;
								?>
							</td>
							<td width="10%" nowrap="nowrap">
							on 
							<?php
								echo JLMS_dateToDisplay($m_n->data, false, 0, " H:m:s");
							?>
							</td>
						</tr>
						<?php
						$k = 3 - $k;
					}
				}
				?>
				<tr>
					<td colspan="5" align="center">
					<br />
						<?php echo $pageNav->writePagesLinks( $link_pages ); ?>
					</td>
				</tr>
			</table>
			
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="boxchecked" value="0" />
		
		</form>
		<?php
	}
}	
?>