<?php
/**
* admin_html.joomlaquiz.class.php
* (c) JoomaLMS eLearning Software http://www.joomlalms.com/
**/

// no direct access
defined( '_JLMS_EXEC' ) or die( 'Restricted access' );

class JLMS_quiz_admin_html_class
{
	//J1.6 ready
	function JQ_JS_getObj() {
		?>
		<script language="javascript" type="text/javascript">
		function getObj(name) {
		  if (document.getElementById)  {  return document.getElementById(name);  }
		  else if (document.all)  {  return document.all[name];  }
		  else if (document.layers)  {  return document.layers[name];  }
		}
		</script>
		<?php
	}

	//J1.6 tested
	function showQuizHead( $id, $option, $header = _JLMS_QUIZ_TITLE, $show_menu = true, $toolbar = array(), $add_options = '', $gqp = false) {
		global $Itemid;
		$JLMS_ACL = & JLMSFactory::getACL();
		
		JLMS_TMPL::OpenMT();
		if ($gqp) {
			$hparams = array(
			'show_menu' => true,
			'simple_menu' => true,
			);
		} else {
			$hparams = array();
		}
		JLMS_TMPL::ShowHeader('quiz', $header, $hparams, array());
		if ($show_menu) {
			
			$controls = array();
			if(!$gqp) {
				if ($JLMS_ACL->CheckPermissions('quizzes', 'manage')) {
					$controls[] = array('href' => sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid&amp;id=$id&amp;task=quizzes&amp;page=cats"), 'title' => _JLMS_QUIZ_CATS_TITLE, 'img' => 'quiz/btn_cats.png');
				}
				$controls[] = array('href' => sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid&amp;id=$id&amp;task=quizzes&amp;page=quizzes"), 'title' => _JLMS_QUIZ_TITLE, 'img' => 'quiz/btn_quizzes.png');
				if ($JLMS_ACL->CheckPermissions('quizzes', 'view_stats')) {
					$controls[] = array('href' => sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid&amp;id=$id&amp;task=quizzes&amp;page=reports"), 'title' => _JLMS_QUIZ_REPORTS_TITLE, 'img' => 'quiz/btn_reports.png');
				}
				if ($JLMS_ACL->CheckPermissions('quizzes', 'manage')) {
					$controls[] = array('href' => sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid&amp;id=$id&amp;task=quizzes&amp;page=certificates"), 'title' => _JLMS_QUIZ_CERTIFICATES_TITLE, 'img' => 'buttons_22/btn_certificates_22.png');
					$controls[] = array('href' => sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid&amp;id=$id&amp;task=quizzes&amp;page=imgs"), 'title' => _JLMS_QUIZ_IMGS_TITLE, 'img' => 'buttons_22/btn_images_22.png');
				}
				if (count($toolbar) && count($controls)) {
					$controls[] = array('href' => 'spacer');
				}
			}
			
			foreach ($toolbar as $toolbar_btn) {
				$btn_str = $toolbar_btn['btn_txt'];
				$btn_img = '';
				switch ($toolbar_btn['btn_type']) {
					case 'print':
						$btn_img = 'btn_print.png';
					break;
					case 'preview':
						$btn_img = 'btn_preview.png';
					break;
					case 'move':
						$btn_img = 'btn_move.png';
					break;
					case 'copy':
						$btn_img = 'btn_copy.png';
					break;
					case 'save':
						$btn_img = 'btn_save.png';
					break;
					case 'apply':
						$btn_img = 'btn_apply.png';
					break;
					case 'back':
					case 'cancel':
						$btn_img = 'btn_back.png';
					break;
					case 'del':
						$btn_img = 'btn_delete.png';
					break;
					case 'edit':
						$btn_img = 'btn_edit.png';
					break;
					case 'bar':
						$btn_img = 'btn_bars.png';
					break;
					case 'new':
					default:
						$btn_img = 'btn_new.png';
					break;
				}
				$controls[] = array('href' => $toolbar_btn['btn_js'], 'title' => $btn_str, 'img' => 'quiz/'.$btn_img);
			}
			if ($add_options) {
				$controls[] = array('href' => '', 'title' => '', 'img' => '', 'custom' => $add_options);
			}
			JLMS_TMPL::ShowControlsFooter($controls, '', false, true);
		}
		JLMS_TMPL::OpenTS();
	}

	//J1.6 ready
	function showQuizFooter() {
		JLMS_TMPL::CloseTS();
		JLMS_TMPL::CloseMT();
	}

	//J1.6 optimized
	function showQuizHead2( $id, $option, $header = _JLMS_QUIZ_TITLE, $show_menu = true, $toolbar = array(), $add_options = '', $form_quiz = '',$gqp=false ) {
		global $Itemid;
		$JLMS_ACL = & JLMSFactory::getACL();

		JLMS_TMPL::OpenMT();
		if ($gqp) {
			$hparams = array(
			'show_menu' => true,
			'simple_menu' => true,
			);
		} else {
			$hparams = array();
		}
		JLMS_TMPL::ShowHeader('quiz', $header, $hparams, array());
		
		if ($show_menu) {
			$controls = array();
			if(!$gqp) {
				if ($JLMS_ACL->CheckPermissions('quizzes', 'manage')) {
					$controls[] = array('href' => sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid&amp;id=$id&amp;task=quizzes&amp;page=cats"), 'title' => _JLMS_QUIZ_CATS_TITLE, 'img' => 'quiz/btn_cats.png');
				}
				$controls[] = array('href' => sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid&amp;id=$id&amp;task=quizzes&amp;page=quizzes"), 'title' => _JLMS_QUIZ_TITLE, 'img' => 'quiz/btn_quizzes.png');
				if ($JLMS_ACL->CheckPermissions('quizzes', 'view_stats')) {
					$controls[] = array('href' => sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid&amp;id=$id&amp;task=quizzes&amp;page=reports"), 'title' => _JLMS_QUIZ_REPORTS_TITLE, 'img' => 'quiz/btn_reports.png');
				}
				if ($JLMS_ACL->CheckPermissions('quizzes', 'manage')) {
					$controls[] = array('href' => sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid&amp;id=$id&amp;task=quizzes&amp;page=certificates"), 'title' => _JLMS_QUIZ_CERTIFICATES_TITLE, 'img' => 'buttons_22/btn_certificates_22.png');
					$controls[] = array('href' => sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid&amp;id=$id&amp;task=quizzes&amp;page=imgs"), 'title' => _JLMS_QUIZ_IMGS_TITLE, 'img' => 'buttons_22/btn_images_22.png');
				}
				if (count($toolbar)) {
					$controls[] = array('href' => 'spacer');
				}
			}
			foreach ($toolbar as $toolbar_btn) {
				$pre_folder = 'quiz';
				$btn_img = '';
				$btn_str = $toolbar_btn['btn_txt'];
				switch ($toolbar_btn['btn_type']) {
					case 'csv_import':
						$btn_img = 'btn_csv_import_22.png';
						$pre_folder = 'buttons_22';
					break;
					case 'csv_export':
						$btn_img = 'btn_csv_export_22.png';
						$pre_folder = 'buttons_22';
					break;
					
					case 'category':
						$btn_img = 'btn_cats.png';
						$pre_folder = 'quiz';						
					break;	
					case 'published':
						$btn_img = 'btn_publish_22.png';
						$pre_folder = 'buttons_22';
					break;
					case 'unpublished':
						$btn_img = 'btn_unpublish_22.png';
						$pre_folder = 'buttons_22';
					break;
					case 'print':
						$btn_img = 'btn_print.png';
					break;
					case 'preview':
						$btn_img = 'btn_preview.png';
					break;
					case 'move':
						$btn_img = 'btn_move.png';
					break;
					case 'copy':
						$btn_img = 'btn_copy.png';
					break;
					case 'save':
						$btn_img = 'btn_save.png';
					break;
					case 'apply':
						$btn_img = 'btn_apply.png';
					break;
					case 'back':
					case 'cancel':
						$btn_img = 'btn_back.png';
					break;
					case 'del':
						$btn_img = 'btn_delete.png';
					break;
					case 'edit':
						$btn_img = 'btn_edit.png';
					break;
					case 'bar':
						$btn_img = 'btn_bars.png';
					break;
					case 'new':
					default:
						$btn_img = 'btn_new.png';
					break;
				}
				$controls[] = array('href' => $toolbar_btn['btn_js'], 'title' => $btn_str, 'img' => $pre_folder.'/'.$btn_img);
			}
			if ($add_options) {
				$controls[] = array('href' => '', 'title' => '', 'img' => '', 'custom' => $add_options);
			}
			//JLMS_TMPL::CloseMT();
			JLMS_TMPL::OpenTS();//
			echo $form_quiz;
			JLMS_TMPL::OpenMT('jlms_table_no_borders');
			JLMS_TMPL::ShowControlsFooter($controls, '', false, true);
		}
		JLMS_TMPL::OpenTS();
	}

	//Joomla 1.6 optimized
	function JQ_showImportQuestions($option, $page, $course_id, $quiz_id, $lists, $gqp){
		global $Itemid, $JLMS_CONFIG;
		
		$title = _JLMS_QUIZ_TBL_QUEST_IMPORT_QUEST;
		
		$toolbar = array();
		
		if($gqp){
			$toolbar[] = array('btn_type' => 'csv_import', 'btn_txt' => _JLMS_QUIZ_TBL_QUEST_IMPORT_QUEST, 'btn_js' => "javascript:submitbutton('import_quest_run_gqp');");
			$toolbar[] = array('btn_type' => 'cancel', 'btn_txt' => _JLMS_QUIZ_CANCEL_BTN, 'btn_js' => "javascript:submitbutton('setup_gqp');");
		} else {
			$toolbar[] = array('btn_type' => 'csv_import', 'btn_txt' => _JLMS_QUIZ_TBL_QUEST_IMPORT_QUEST, 'btn_js' => "javascript:submitbutton('import_quest_run');");
			$toolbar[] = array('btn_type' => 'cancel', 'btn_txt' => _JLMS_QUIZ_CANCEL_BTN, 'btn_js' => "javascript:submitbutton('setup_quest');");			
		}
		
		$action_form = $JLMS_CONFIG->get('live_site')."/index.php?option=$option&amp;Itemid=$Itemid";
		$add_option = array();
		
		$menu_form = '<form action="'.$JLMS_CONFIG->get('live_site')."/index.php?option=$option&amp;Itemid=$Itemid".'" method="post" name="adminFormQ" enctype="multipart/form-data">';
		?>
		<script language="javascript" type="text/javascript">
		<!--
		
		function submitbutton(pressbutton) {
			if(pressbutton == 'import_quest_run_gqp'){
				var form = document.adminFormQ;
				if( (form.level_id_0.value == 0)) {
					alert( "<?php echo _JLMS_SELECT_CATEGORY;?>" );
				} else {
					form.page.value = pressbutton;
					form.submit();
				}
			} else {
				var form = document.adminFormQ;
				if(pressbutton == 'import_quest_run'){
					form.page.value = pressbutton;
					form.submit();
				} else {
					form.page.value = pressbutton;
					form.submit();
				}
			}
		}
		
		function toogleMode(){
			var form = document.adminFormQ;
			form.page.value = '';
			form.submit();
		}
		<?php
		if($gqp){
		?>
		var old_filters = new Array();
		function read_filter(){
			var form = document.adminFormQ;
			var count_levels = '<?php echo count($lists['levels']);?>';
			for(var i=0;i<parseInt(count_levels);i++){
				if(form['filter_id_'+i] != null){
					old_filters[i] = form['filter_id_'+i].value;
				}
			}
		}
		function write_filter(){
			var form = document.adminFormQ;
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
		<?php
		}
		?>
		//-->
		</script>
		
		<?php
		if($gqp) {
			$multicat = array();
			$i=0;
			
			foreach($lists as $key=>$item){
				if(substr($key, 0, 6) == 'level_'){
					if(isset($lists['level_'.$i])) {				
						$multicat[] = $lists['level_'.$i];
					}	
					$i++;
				}
			}
		}
		?>
	<?php if($gqp){ $menu_form = ''; ?>
		<form action="<?php echo $action_form;?>" method="post" name="adminFormQ" enctype="multipart/form-data">
	<?php } ?>
			<?php
			JLMS_quiz_admin_html_class::showQuizHead2( $course_id, $option, $title, true, $toolbar, $add_option, $menu_form, $gqp );	
			?>
			<table width="100%" cellpadding="0" cellspacing="0" class="jlms_table_no_borders">
				<?php
				/*
				<tr>
					<td width="20%">
						Mode
					</td>
					<td>
						<?php echo $lists['mode'];?>
					</td>
				</tr>
				*/
				?>
				<?php if($gqp) {?>
					<tr>
						<td colspan="2">
						<?php
							JLMS_quiz_admin_html_class::JQ_subCategory($multicat, $lists['levels'], 'adminFormQ');				
						?>	
						</td>
					</tr>
				<?php } else {?> 
					<tr>
						<td width="20%">
							<?php echo _JLMS_QUIZ_ENTER_CAT;?>
						</td>
						<td>
						<?php
							echo $lists['jq_categories'];
						?>	
						</td>
					</tr>
				<?php }?>
				
				<tr>
					<td colspan="2">&nbsp;</td>
				</tr>
				<tr>
					<td width="20%">
						<?php echo _JLMS_CHOOSE_FILE;?>
					</td>
					<td>
						<input type="file" name="userfile_csv" class="inputbox" size="40" />
					</td>
				</tr>
			</table>	
			<?php JLMS_quiz_admin_html_class::showQuizFooter(); ?>
			<?php if($gqp) { JLMS_quiz_admin_html_class::showQuizFooter(); } ?>
			<input type="hidden" name="option" value="<?php echo $option;?>" />
			<input type="hidden" name="page" value="<?php echo $page;?>" />
			<input type="hidden" name="task" value="quizzes" />
			<input type="hidden" name="id" value="<?php echo $course_id;?>" />
			<input type="hidden" name="quiz_id" value="<?php echo $quiz_id;?>" />
			<input type="hidden" name="boxchecked" value="0" />
		</form>
		<?php if(!$gqp) { JLMS_quiz_admin_html_class::showQuizFooter(); } ?>
		
		<?php
	}
	
	//Joomla 1.6 ready
	function JQ_showListCategoryGQP(&$rows, &$lists, &$pageNav, $option, $page, $id, $is_pool, $gqp=true, $levellist){
		global $Itemid, $JLMS_CONFIG;
		
		$toolbar = array();
		
		$toolbar[] = array('btn_type' => 'new', 'btn_txt' => _JLMS_QUIZ_NEW_CAT_BTN, 'btn_js' => "javascript:submitbutton('new_category_gqp');");
		$toolbar[] = array('btn_type' => 'edit', 'btn_txt' => _JLMS_QUIZ_EDIT_CAT_BTN, 'btn_js' => "javascript:submitbutton('edit_category_gqp');");
		$toolbar[] = array('btn_type' => 'del', 'btn_txt' => _JLMS_QUIZ_DEL_CAT_BTN, 'btn_js' => "javascript:submitbutton('delete_category_gqp');");
		$toolbar[] = array('btn_type' => 'cancel', 'btn_txt' => _JLMS_QUIZ_CANCEL_BTN, 'btn_js' => "javascript:submitbutton('setup_gqp');");
		
		$action_form = $JLMS_CONFIG->get('live_site')."/index.php?option=$option&amp;Itemid=$Itemid";
		$add_option = array();
		$title = _JLMS_QUIZ_TBL_CATEGORY_GQP;
		?>
		<script language="javascript" type="text/javascript">
		<!--
		function submitbutton(pressbutton) {
			var form = document.adminForm;
			if ( ((pressbutton == 'edit_category_gqp') || (pressbutton == 'delete_category_gqp') ) && (form.boxchecked.value == "0")) {
				alert('<?php echo _JLMS_ALERT_SELECT_ITEM;?>');
			} else {
				form.page.value = pressbutton;
				form.submit();
			}
		}
		//-->
		</script>
		<form action="<?php echo $action_form;?>" method="post" name="adminForm">
			<?php
			JLMS_quiz_admin_html_class::showQuizHead( $id, $option, $title, true, $toolbar, $add_option, $gqp );	
			?>
			<table width="100%" cellpadding="0" cellspacing="0" border="0" class="<?php echo JLMSCSS::_('jlmslist');?>">
				<thead>
					<tr>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" width="20" align="center">
							#
						</<?php echo JLMSCSS::tableheadertag();?>>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" width="20" align="center">
							<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" />
						</<?php echo JLMSCSS::tableheadertag();?>>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" width="40%">
							Name
						</<?php echo JLMSCSS::tableheadertag();?>>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="right" style="text-align:right">
							<table cellpadding="0" cellspacing="0" border="0" class="jlms_table_no_borders" align="right">
								<tr>
									<td nowrap="nowrap" style="text-align:right;padding:0px;margin:0px;">
										Max Levels
									</td>
									<td style="text-align:right; padding:0px 0px 0px 5px; margin:0px;">
										<?php echo $levellist;?>
									</td>
								</tr>
							</table>
						</<?php echo JLMSCSS::tableheadertag();?>>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td colspan="4" align="center" class="<?php echo JLMSCSS::_('jlmslist-footer_td');?>">
							<?php
							$link_r = "index.php?option=$option&amp;Itemid=$Itemid&amp;task=quizzes&amp;page=$page";
							$link_r .= $pageNav->limit ? "&limit=$pageNav->limit" : "";
							$link_r .= $pageNav->limitstart ? "&limitstart=$pageNav->limitstart" : "";
							echo _JLMS_PN_DISPLAY_NUM . ':' . $pageNav->getLimitBox($link_r) . ' ' . $pageNav->getPagesCounter();
							echo '<br />';
							echo $pageNav->writePagesLinks($link_r);
							?>
						</td>
					</tr>
				</tfoot>
				<tbody>
					<?php
					$k = 1;
					$i = 0;
					$n = count( $rows );
					foreach ($rows as $row) {
						$checked = mosHTML::idBox( $i, $row->id);
						$link = "index.php?option=$option&task=quizzes&page=edit_category_gqp&c_id=".$row->id."&Itemid=".$Itemid;
						?>
						<tr class="<?php echo JLMSCSS::_('sectiontableentry'.$k); ?>">
							<td align="center">
							<?php echo $i + 1 + $pageNav->limitstart;?>
							</td>
							<td>
							<?php echo $checked; ?>
							</td>
							<td nowrap="nowrap">
							<a href="<?php echo $link;?>" title="<?php echo $row->name;?>">
							<?php
								echo $row->treename;
							?>
							</a>
							</td>
							<td>&nbsp;</td>
						</tr>
						<?php
						$k = 3 - $k;
						$i++;
					}
					?>
				</tbody>
			</table>
			<?php JLMS_quiz_admin_html_class::showQuizFooter(); ?>
			<input type="hidden" name="option" value="<?php echo $option;?>" />
			<input type="hidden" name="page" value="<?php echo $page;?>" />
			<input type="hidden" name="task" value="quizzes" />
			<input type="hidden" name="boxchecked" value="0" />
		</form>
		<?php
	}

	//Joomla 1.6 ready
	function JQ_showeditCategoryGQP($menu, $lists, $rows, $option){
		global $JLMS_CONFIG, $Itemid;
		?>
		<script language="javascript" type="text/javascript">
		function submitbutton(pressbutton) {
			var form = document.adminForm;
			// do field validation
			if ((pressbutton == 'save_category_gqp' || pressbutton == 'apply_category_gqp') && trim(form.name.value) == ''){
				alert( "Category must have a name" );
			} else {
				form.page.value = pressbutton;
				form.submit();
			}
		}		
		</script>
		<?php
		$toolbar = array();
		
		$toolbar[] = array('btn_type' => 'save', 'btn_txt' => _JLMS_QUIZ_SAVE_CAT_BTN, 'btn_js' => "javascript:submitbutton('save_category_gqp');");
		$toolbar[] = array('btn_type' => 'apply', 'btn_txt' => _JLMS_QUIZ_APPLY_BTN, 'btn_js' => "javascript:submitbutton('apply_category_gqp');");
		$toolbar[] = array('btn_type' => 'cancel', 'btn_txt' => _JLMS_QUIZ_CANCEL_BTN, 'btn_js' => "javascript:submitbutton('category_gqp');");
		
		$action_form = $JLMS_CONFIG->get('live_site')."/index.php?option=$option&amp;Itemid=$Itemid";
		$add_option = array();
		if(isset($menu->id) && $menu->id){
			$title = _JLMS_QUIZ_EDIT_CAT_BTN;
		} else {
			$title = _JLMS_QUIZ_NEW_CAT_BTN;
		}
		?>
		<form action="<?php echo $action_form;?>" method="post" name="adminForm">
		<?php
		JLMS_quiz_admin_html_class::showQuizHead( 0, $option, $title, true, $toolbar, array(), true );	
		?>
		<table width="100%" border="0" cellpadding="0" cellspacing="0" class="jlms_table_no_borders">
			<tr>
				<td width="15%">
					Name:
				</td>
				<td width="85%">
					<input class="inputbox" type="text" name="name" size="40" maxlength="150" value="<?php echo $menu->name; ?>" />
				</td>
			</tr>
			<?php if ($JLMS_CONFIG->get('multicat_show_admin_levels', 0)) { ?>
			<tr>
				<td valign="top"><br />
					Parent Item:
				</td>
				<td><br />
					<?php echo $lists['parent']; ?>
				</td>
			</tr>
			<?php } ?>
			
		</table>
		<?php JLMS_quiz_admin_html_class::showQuizFooter(); ?>
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="c_id" value="<?php echo $menu->id; ?>" />
		<input type="hidden" name="page" value="" />
		<input type="hidden" name="task" value="quizzes" />
		<input type="hidden" name="hidemainmenu" value="0" />
		</form>
		<script language="Javascript" src="<?php echo $JLMS_CONFIG->get('live_site');?>/includes/js/overlib_mini.js"></script>
	<?php
	}

	//Joomla 1.6 ready
	function JQ_showCatsList( &$rows, &$pageNav, $option, $page, $id ) {
		global $Itemid;
		$toolbar = array();
		$toolbar[] = array('btn_type' => 'new', 'btn_txt' => _JLMS_QUIZ_NEW_CAT_BTN, 'btn_js' => sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid&amp;id=$id&amp;task=quizzes&amp;page=add_cat"));
		$toolbar[] = array('btn_type' => 'edit', 'btn_txt' => _JLMS_QUIZ_EDIT_CAT_BTN, 'btn_js' => "javascript:submitbutton('edit_cat');");
		$toolbar[] = array('btn_type' => 'del', 'btn_txt' => _JLMS_QUIZ_DEL_CAT_BTN, 'btn_js' => "javascript:submitbutton('del_cat');");

		JLMS_quiz_admin_html_class::showQuizHead( $id, $option, _JLMS_QUIZ_CATS_TITLE, true, $toolbar );
		?>

<script language="javascript" type="text/javascript">
<!--
function submitbutton(pressbutton) {
	var form = document.adminForm;
	if ( ((pressbutton == 'edit_cat') || (pressbutton == 'del_cat') ) && (form.boxchecked.value == "0")) {
		alert('<?php echo _JLMS_ALERT_SELECT_ITEM;?>');
	} else {
		form.page.value = pressbutton;
		form.submit();
	}
}
//-->
</script>
		<br />
		<form action="<?php echo sefRelToAbs("index.php?option=$option&Itemid=$Itemid");?>" method="post" name="adminForm">
		<table width="100%" cellpadding="0" cellspacing="0" border="0" class="<?php echo JLMSCSS::_('jlmslist');?>">
			<tr>
				<<?php echo JLMSCSS::tableheadertag();?> width="20" class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center">#</<?php echo JLMSCSS::tableheadertag();?>>
				<<?php echo JLMSCSS::tableheadertag();?> width="20" class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></<?php echo JLMSCSS::tableheadertag();?>>
				<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>"><?php echo _JLMS_QUIZ_TBL_CAT_NAME;?></<?php echo JLMSCSS::tableheadertag();?>>
				<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>"><?php echo _JLMS_QUIZ_CAT_TYPE;?></<?php echo JLMSCSS::tableheadertag();?>>
			</tr>
			<?php
			$k = 1;
			for ($i=0, $n=count($rows); $i < $n; $i++) {
				$row = $rows[$i];
				$link 	= sefRelToAbs("index.php?option=".$option."&amp;Itemid=".$Itemid."&amp;task=quizzes&amp;id=$id&amp;page=editA_cat&amp;c_id=". $row->c_id);
				$checked = mosHTML::idBox( $i, $row->c_id);?>
				<tr class="<?php echo JLMSCSS::_('sectiontableentry'.$k); ?>">
					<td align="center"><?php echo  $pageNav->limitstart + $i + 1; ?></td>
					<td align="center"><?php echo $checked; ?></td>
					<td align="left">
						<span>
							<?php echo JLMS_toolTip($row->c_category, $row->c_instruction, '', $link, 1, 36, 'true', 'jlms_ttip');?>
						</span>
					</td>
					<td align="left">
						<?php echo $row->is_quiz_cat ? _JLMS_QUIZ_CAT_TYPE_QUIZ : _JLMS_QUIZ_CAT_TYPE_QUEST; ?>
					</td>
				</tr>
				<?php
				$k = 3 - $k;
			}?>
			<tr>
				<td colspan="4" align="center" class="<?php echo JLMSCSS::_('jlmslist-footer_td');?>">
					<div align="center" style="white-space:nowrap">
					<?php
						$link = "index.php?option=$option&amp;Itemid=$Itemid&amp;task=quizzes&amp;id=$id&amp;page=$page";
						echo _JLMS_PN_DISPLAY_NUM . '&nbsp;' . $pageNav->getLimitBox( $link ) . '&nbsp;' . $pageNav->getPagesCounter();
						echo '<br />';
						echo $pageNav->writePagesLinks( $link );
					?> 
					</div>
				</td>
			</tr>
		</table>
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="page" value="<?php echo $page;?>" />
		<input type="hidden" name="task" value="quizzes" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="id" value="<?php echo $id;?>" />
		</form>
		<?php JLMS_quiz_admin_html_class::showQuizFooter(); ?>
		<?php
	}

	//Joomla 1.6 ready
	function JQ_editCategory( &$row, &$lists, $option, $page, $course_id ) {
		global $Itemid;
		$toolbar = array();
		$toolbar[] = array('btn_type' => 'save', 'btn_txt' => _JLMS_QUIZ_SAVE_CAT_BTN, 'btn_js' => "javascript:submitbutton('save_cat');");
		$toolbar[] = array('btn_type' => 'cancel', 'btn_txt' => _JLMS_QUIZ_CANCEL_BTN, 'btn_js' => "javascript:submitbutton('cancel_cat');");

		$h = $row->c_id ? _JLMS_QUIZ_CAT_EDIT_TITLE : _JLMS_QUIZ_CAT_NEW_TITLE ;
		JLMS_quiz_admin_html_class::showQuizHead( $course_id, $option, $h, true, $toolbar );
		?>
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

	if (pressbutton == 'cancel_cat') {
		form.page.value = pressbutton;
		form.submit();
		return;
	}
	// do field validation
	if (form.c_category.value == ""){
		alert( "<?php echo _JLMS_PL_ENTER_NAME;?>" );
	} else {
		form.page.value = pressbutton;
		form.submit();
	}
}
//-->
</script>
		<form action="<?php echo sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid");?>" method="post" name="adminForm" onsubmit="setgood();">
		<table width="100%" cellpadding="0" cellspacing="0" border="0" class="jlms_item_properties">
			<tr>
				<td width="15%"><?php echo _JLMS_ENTER_NAME;?></td>
				<td>
					<input size="40" class="inputbox" type="text" name="c_category" value="<?php echo str_replace('"','&quot;',$row->c_category);?>">
				</td>
			</tr>
			<tr>
				<td width="15%"><?php echo _JLMS_QUIZ_CAT_TYPE;?></td>
				<td><br />
					<input class="inputbox" id="is_quiz_category" type="radio" name="is_quiz_cat" value="1"<?php echo $row->is_quiz_cat ? ' checked="checked"':'';?>><label for="is_quiz_category"><?php echo _JLMS_QUIZ_CAT_TYPE_QUIZ;?></label><br />
					<input class="inputbox" id="is_quest_category" type="radio" name="is_quiz_cat" value="0"<?php echo !$row->is_quiz_cat ? ' checked="checked"':'';?>><label for="is_quest_category"><?php echo _JLMS_QUIZ_CAT_TYPE_QUEST;?></label>
				</td>
			</tr>
			<tr>
				<td colspan="2"><br /><?php echo _JLMS_DESCRIPTION;?></td>
			</tr>
			<tr>
				<td colspan="2">
				<?php
					JLMS_editorArea( 'editor2', $row->c_instruction, 'c_instruction', '100%;', '250', '40', '20' ) ; ?>
				</td>
			</tr>
		</table>
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
		<input type="hidden" name="task" value="quizzes" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="id" value="<?php echo $course_id;?>" />
		<input type="hidden" name="page" value="save_cat" />
		<input type="hidden" name="c_id" value="<?php echo $row->c_id; ?>" />
		</form>	
		<?php JLMS_quiz_admin_html_class::showQuizFooter(); ?>	
		<?php
	}

	//Joomla 1.6 ready
	function QuizPublishIcon( $id, $course_id, $state, $page, $alt, $image, $option, $gqp = 0 ) {
		$JLMS_CONFIG = & JLMSFactory::getConfig();
		$Itemid = $JLMS_CONFIG->get('Itemid');
		$link = sefRelToAbs("index.php?option=".$option."&amp;Itemid=".$Itemid."&amp;task=quizzes&amp;id=".$course_id."&amp;page=".$page."&amp;state=".$state."&amp;cidoff=".$id."&amp;gqp=".$gqp);
		return '<a class="jlms_img_link" href="'.$link.'" title="'.$alt.'">
			<img class="JLMS_png" src="'.$JLMS_CONFIG->get('live_site').'/components/com_joomla_lms/lms_images/toolbar/'.$image.'" width="16" height="16" border="0" alt="'.$alt.'" />
		</a>';
	}

	//Joomla 1.6 tested
	function QuizPublishIconTT( $id, $course_id, $state, $page, $alt, $image, $option, $gqp = 0, $tt_text = '' ) {
		$JLMS_CONFIG = & JLMSFactory::getConfig();
		$Itemid = $JLMS_CONFIG->get('Itemid');
		$tooltip_link = sefRelToAbs("index.php?option=".$option."&amp;Itemid=".$Itemid."&amp;task=quizzes&amp;id=".$course_id."&amp;page=".$page."&amp;state=".$state."&amp;cidoff=".$id."&amp;gqp=".$gqp);
		$tooltip_name = '<img class="JLMS_png" src="'.$JLMS_CONFIG->get('live_site').'/components/com_joomla_lms/lms_images/toolbar/'.$image.'" width="16" height="16" border="0" alt="'.$alt.'" />';
		return JLMS_toolTip($alt, $tt_text, $tooltip_name, $tooltip_link);;
	}

	//Joomla 1.6 ready
	function JQ_showQuizList( &$rows, &$lists, &$pageNav, $option, $page, $id ) {
		global $Itemid, $JLMS_CONFIG;

		$JLMS_ACL = & JLMSFactory::getACL();
		$toolbar = array();
		if ($JLMS_ACL->CheckPermissions('quizzes', 'view_stats')) {
			$toolbar[] = array('btn_type' => 'bar', 'btn_txt' => _JLMS_QUIZ_VIEW_STATS, 'btn_js' => "javascript:submitbutton('quiz_bars');");
		}
		if ($JLMS_ACL->CheckPermissions('quizzes', 'manage')) {
			if ($JLMS_ACL->CheckPermissions('quizzes', 'view_stats')) {
				$toolbar[] = array('btn_type' => 'spacer', 'btn_txt' => 'spacer', 'btn_js' => 'spacer');
			}
			$toolbar[] = array('btn_type' => 'edit', 'btn_txt' => _JLMS_QUIZ_EDIT_QUIZ_BTN, 'btn_js' => "javascript:submitbutton('edit_quiz');");
			$toolbar[] = array('btn_type' => 'del', 'btn_txt' => _JLMS_QUIZ_DEL_QUIZ_BTN, 'btn_js' => "javascript:submitbutton('del_quiz');");
			$toolbar[] = array('btn_type' => 'copy', 'btn_txt' => _JLMS_QUIZ_COPY_QUIZ_BTN, 'btn_js' => "javascript:submitbutton('copy_quiz_sel');");
			$toolbar[] = array('btn_type' => 'move', 'btn_txt' => _JLMS_QUIZ_MOVE_QUIZ_BTN, 'btn_js' => "javascript:submitbutton('move_quiz_sel');");
			$toolbar[] = array('btn_type' => 'spacer', 'btn_txt' => 'spacer', 'btn_js' => 'spacer');

			$toolbar[] = array('btn_type' => 'new', 'btn_txt' => _JLMS_QUIZ_NEW_QUIZ_BTN, 'btn_js' => JRoute::_("index.php?option=$option&Itemid=$Itemid&id=$id&task=quizzes&page=add_quiz"));
		}
		JLMS_quiz_admin_html_class::showQuizHead( $id, $option, _JLMS_QUIZ_TITLE, true, $toolbar );
		?>

<script language="javascript" type="text/javascript">
<!--
function submitbutton(pressbutton) {
	var form = document.adminForm;
	if ( ((pressbutton == 'edit_quiz') || (pressbutton == 'del_quiz') || (pressbutton == 'copy_quiz_sel') || (pressbutton == 'move_quiz_sel')) && (form.boxchecked.value == "0")) {
		alert('<?php echo _JLMS_ALERT_SELECT_ITEM;?>');
	} else {
		form.page.value = pressbutton;
		form.submit();
	}
}
//-->
</script>
		<form action="<?php echo $JLMS_CONFIG->get('live_site')."/index.php?option=$option&amp;Itemid=$Itemid";?>" method="post" name="adminForm">
		<table width="100%" cellpadding="0" cellspacing="0" border="0" class="jlms_table_no_borders">
			<tr>
				<td>
				<table width="100%" cellpadding="0" cellspacing="0" border="0" class="jlms_table_no_borders">
					<tr>
						<td align="right" style="text-align:left ">
							<div align="right" style="white-space:nowrap ">
							<?php echo _JLMS_QUIZ_FILTER_BY_CAT.'&nbsp;' . $lists['category'];?>
							</div>
						</td>
					</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td width="100%">
				<?php $quizzes_colspan = 9; ?>
				<table width="100%" cellpadding="0" cellspacing="0" border="0" class="<?php echo JLMSCSS::_('jlmslist');?>" style="margin-top: 0px; padding-top:0px; margin-bottom:0px; padding-bottom: 0px;">
					<tr>
						<<?php echo JLMSCSS::tableheadertag();?> width="20" class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center">#</<?php echo JLMSCSS::tableheadertag();?>>
						<<?php echo JLMSCSS::tableheadertag();?> width="20" class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></<?php echo JLMSCSS::tableheadertag();?>>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>"><?php echo _JLMS_QUIZ_TBL_Q_NAME;?></<?php echo JLMSCSS::tableheadertag();?>>
						<?php if ($JLMS_ACL->CheckPermissions('quizzes', 'publish')) { $quizzes_colspan ++; ?>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>"><?php echo _JLMS_QUIZ_TBL_Q_ACTIVE;?></<?php echo JLMSCSS::tableheadertag();?>>
						<?php } ?>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>"><?php echo _JLMS_QUIZ_TBL_Q_CAT;?></<?php echo JLMSCSS::tableheadertag();?>>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>"><?php echo _JLMS_QUIZ_TBL_Q_T_SCORE;?></<?php echo JLMSCSS::tableheadertag();?>>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>"><?php echo _JLMS_QUIZ_TBL_Q_P_SCORE;?></<?php echo JLMSCSS::tableheadertag();?>>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>"><?php echo _JLMS_QUIZ_TBL_Q_T_LIMIT;?></<?php echo JLMSCSS::tableheadertag();?>>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>"><?php echo _JLMS_QUIZ_TBL_Q_CREATED;?></<?php echo JLMSCSS::tableheadertag();?>>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" width="20">ID</<?php echo JLMSCSS::tableheadertag();?>>
					</tr>
					<?php if ($JLMS_ACL->CheckPermissions('quizzes', 'manage_pool')) { ?>
					<tr class="<?php echo JLMSCSS::_('sectiontableentry1');?>">
						<td align="center" valign="middle">-</td>
						<td align="center" valign="middle">-</td>
						<td align="left" valign="middle">
						<?php $link = sefRelToAbs('index.php?option='.$option.'&amp;Itemid='.$Itemid.'&amp;task=quizzes&amp;id='.$id.'&amp;page=setup_quest&amp;quiz_id=-1');?>
							<a href="<?php echo $link; ?>">
							<?php echo _JLMS_QUIZ_QUEST_POOL; ?>
							</a>
						</td>
						<td align="center" valign="middle">
							&nbsp;
						</td>
						<td align="left" valign="middle" colspan="6"><b><?php echo _JLMS_QUIZ_POOL_QUEST_NUM.' '.$lists['pool_count']; ?></b></td>
					</tr>
					<?php } ?>
				<?php
				$k = 2;
				$quiz_task_page = 'setup_quest';
				$quiz_task = 'quizzes';
				if (!$JLMS_ACL->CheckPermissions('quizzes', 'manage')) {
					$quiz_task_page = 'quiz_bars';
					if (!$JLMS_ACL->CheckPermissions('quizzes', 'view_stats')) {
						$quiz_task_page = '';
						$quiz_task = 'show_quiz';
					}
				}
				for ($i=0, $n=count($rows); $i < $n; $i++) {
					$row = $rows[$i];
					$link 	= sefRelToAbs('index.php?option='.$option.'&amp;Itemid='.$Itemid.($quiz_task?('&amp;task='.$quiz_task):'').'&amp;id='.$id.($quiz_task_page?('&amp;page='.$quiz_task_page):'').'&amp;quiz_id='. $row->c_id);
					$img_published	= $row->published ? 'btn_accept.png' : 'btn_cancel.png';
					$task_published	= $row->published ? 'unpublish_quiz' : 'publish_quiz';
					$alt_published 	= $row->published ? _JLMS_STATUS_PUB : _JLMS_STATUS_UNPUB;
					$state = $row->published ? 0 : 1;
					$checked = mosHTML::idBox( $i, $row->c_id);
					?>
					<tr class="<?php echo JLMSCSS::_('sectiontableentry'.$k); ?>">
						<td align="center" valign="middle"><?php echo  $pageNav->limitstart + $i + 1; ?></td>
						<td align="center" valign="middle"><?php echo $checked; ?></td>
						<td align="left" valign="middle">
						<?php if ($JLMS_ACL->CheckPermissions('quizzes', 'manage')) { ?>
						<?php $txt_for_tip = '<table width="100%" cellpadding=0 cellspacing=0>';
						$txt_for_tip .= '<tr><td>'._JLMS_QUIZ_RANDOMIZE_OPTION.'</td><td align="center" width="25%"><img src="'.$JLMS_CONFIG->get('live_site').'/components/com_joomla_lms/lms_images/toolbar/'.($row->c_random ? 'btn_accept.png' : 'btn_cancel.png').'" width="16" height="16" border="0"/></td></tr>';
						$txt_for_tip .= '<tr><td>'._JLMS_QUIZ_REVIEW_OPTION_W.'</td><td align="center" width="25%"><img src="'.$JLMS_CONFIG->get('live_site').'/components/com_joomla_lms/lms_images/toolbar/'.($row->c_enable_review ? 'btn_accept.png' : 'btn_cancel.png').'" width="16" height="16" border="0"/></td></tr>';
						$txt_for_tip .= '<tr><td>'._JLMS_QUIZ_EMAIL_OPTION_W.'</td><td align="center" width="25%"><img src="'.$JLMS_CONFIG->get('live_site').'/components/com_joomla_lms/lms_images/toolbar/'.($row->c_email_to ? 'btn_accept.png' : 'btn_cancel.png').'" width="16" height="16" border="0"/></td></tr>';
						$txt_for_tip .= '<tr><td>'._JLMS_QUIZ_PRINT_OPTION_W.'</td><td align="center" width="25%"><img src="'.$JLMS_CONFIG->get('live_site').'/components/com_joomla_lms/lms_images/toolbar/'.($row->c_enable_print ? 'btn_accept.png' : 'btn_cancel.png').'" width="16" height="16" border="0"/></td></tr>';
						$txt_for_tip .= '<tr><td>'._JLMS_QUIZ_WITH_CRTF_OPTION_W.'</td><td align="center" width="25%"><img src="'.$JLMS_CONFIG->get('live_site').'/components/com_joomla_lms/lms_images/toolbar/'.($row->c_certificate ? 'btn_accept.png' : 'btn_cancel.png').'" width="16" height="16" border="0"/></td></tr>';
						$txt_for_tip .= '<tr><td>'._JLMS_SHOW_IN_GRADEBOOK_OPTION.'</td><td align="center" width="25%"><img src="'.$JLMS_CONFIG->get('live_site').'/components/com_joomla_lms/lms_images/toolbar/'.($row->c_gradebook ? 'btn_accept.png' : 'btn_cancel.png').'" width="16" height="16" border="0"/></td></tr>';
						$txt_for_tip .= '</table>';
						 ?>
						<?php echo JLMS_toolTip($row->c_title, $txt_for_tip, '', $link, 1, 36, 'true', 'jlms_ttip');?>
						<?php } else { ?>
							<a href="<?php echo $link; ?>">
							<?php echo $row->c_title; ?>
							</a>
						<?php } ?>
						<?php if($JLMS_CONFIG->get('show_quizzes_authors', 0)){?>
						<br />
						<span class="small"><?php echo _JLMS_HOME_AUTHOR . "&nbsp;" . $row->author_name;?></span>
						<?php } ?>
						</td>
						<?php if ($JLMS_ACL->CheckPermissions('quizzes', 'publish')) { ?>
						<td align="center" valign="middle">
						<?php if ($row->is_time_related) {
							if ($row->published) {
								$img_published = 'btn_publish_wait.png';
							}
							$tooltip_txt = _JLMS_WILL_BE_RELEASED_IN;
							$showperiod = $row->show_period;
							$ost1 = $showperiod%(24*60);		
							$sp_days = ($showperiod - $ost1)/(24*60);
							$ost2 = $showperiod%60;						
							$sp_hours = ($ost1 - $ost2)/60;
							$sp_mins = $ost2;
							$release_time_info = false;
							if ($sp_days) {
								$tooltip_txt .= ' '.$sp_days.' '._JLMS_RELEASED_IN_DAYS;
								$release_time_info = true;
							}
							if ($sp_hours) {
								$tooltip_txt .= ' '.$sp_hours.' '._JLMS_RELEASED_IN_HOURS;
								$release_time_info = true;
							}
							if ($sp_mins) {
								$tooltip_txt .= ' '.$sp_mins.' '._JLMS_RELEASED_IN_MINUTES;
								$release_time_info = true;
							}
							if ($release_time_info) {
								$tooltip_txt .= ' '._JLMS_RELEASED_AFTER_ENROLLMENT;
							}
							echo JLMS_quiz_admin_html_class::QuizPublishIconTT( $row->c_id, $id, $state, $task_published, $alt_published, $img_published, $option, 0, $tooltip_txt);
						} else {
							echo JLMS_quiz_admin_html_class::QuizPublishIcon( $row->c_id, $id, $state, $task_published, $alt_published, $img_published, $option);
						} ?>
						</td>
						<?php } ?>
						<td align="left" valign="middle">
							<?php echo $row->c_category?$row->c_category:'&nbsp;'; ?>
						</td>
						<td align="left" valign="middle">
							<?php echo $row->c_full_score . ($row->quests_from_pool?'+':''); ?>
						</td>
						<td align="left" valign="middle">
							<?php echo $row->c_passing_score . '%'; ?>
						</td>
						<td align="left" valign="middle">
							<?php echo $row->c_time_limit; ?>
						</td>
						<td align="left" valign="middle">
							<?php echo JLMS_dateToDisplay($row->c_created_time);?>
						</td>
						<td><?php echo $row->c_id;?></td>
					</tr>
					<?php
					$k = 3 - $k;
				}
				?>
				<tr>
					<td colspan="<?php echo $quizzes_colspan;?>" align="center" class="<?php echo JLMSCSS::_('jlmslist-footer_td');?>">
						<div align="center" style="white-space:nowrap ">
						<?php
							$link = "index.php?option=$option&amp;Itemid=$Itemid&amp;task=quizzes&amp;id=$id".($page?"&amp;page=$page":'');
							echo _JLMS_PN_DISPLAY_NUM . '&nbsp;' . $pageNav->getLimitBox( $link ) . '&nbsp;' . $pageNav->getPagesCounter();
							echo '<br />';
							echo $pageNav->writePagesLinks( $link );
						?> 
						</div>
					</td>
				</tr>
				</table>
				</td>
			</tr>
		</table>
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="page" value="<?php echo $page;?>" />
		<input type="hidden" name="task" value="quizzes" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="id" value="<?php echo $id;?>" />
		</form>
		<?php JLMS_quiz_admin_html_class::showQuizFooter(); ?>
		<?php
	}

	//Joomla 1.6 ready
	function JQ_showQuizList_Stu( &$rows, &$lists, &$pageNav, $option, $id ) {
		global $Itemid, $JLMS_CONFIG;
		JLMS_quiz_admin_html_class::showQuizHead( $id, $option, _JLMS_QUIZ_STU_TITLE, false );
		?>
		
		<form action="<?php echo sefRelToAbs("index.php?option=$option&Itemid=$Itemid");?>" method="post" name="adminForm">
<?php 	if (!empty($rows) || $lists['used_category_filter'] ) { ?>
		<br />
		<table width="100%" cellpadding="0" cellspacing="0" border="0" class="jlms_table_no_borders">
			<tr>
				<td>
				<table width="100%" cellpadding="0" cellspacing="0" border="0" class="jlms_table_no_borders">
					<tr>
						<td align="right" style="text-align:left ">
							<div align="right" style="white-space:nowrap ">
							<?php echo _JLMS_QUIZ_FILTER_BY_CAT.'&nbsp;' . $lists['category'];?>
							<noscript>
								<input type="submit" name="OK" value="OK" />
							</noscript>
							</div>
						</td>
					</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td width="100%">
				<?php $quizzes_colspan = 6; ?>
				<table width="100%" cellpadding="0" cellspacing="0" border="0" class="<?php echo JLMSCSS::_('jlmslist');?>" style="margin-top:0px; padding-top:0px;">
					<tr>
						<<?php echo JLMSCSS::tableheadertag();?> width="20" class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center">#</<?php echo JLMSCSS::tableheadertag();?>>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>"><?php echo _JLMS_QUIZ_TBL_Q_NAME;?></<?php echo JLMSCSS::tableheadertag();?>>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>"><?php echo _JLMS_QUIZ_TBL_Q_CAT;?></<?php echo JLMSCSS::tableheadertag();?>>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>"><?php echo _JLMS_QUIZ_TBL_Q_P_SCORE;?></<?php echo JLMSCSS::tableheadertag();?>>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>"><?php echo _JLMS_QUIZ_YOUR_RESULTS;?></<?php echo JLMSCSS::tableheadertag();?>>
					</tr>
				<?php
				$k = 1;
				for ($i=0, $n=count($rows); $i < $n; $i++) {
					$row = $rows[$i];
					$link 	= sefRelToAbs('index.php?option='.$option.'&amp;Itemid='.$Itemid.'&amp;task=show_quiz&amp;id='.$id.'&amp;quiz_id='. $row->c_id);
					?>
					<tr class="<?php echo JLMSCSS::_('sectiontableentry'.$k); ?>">
						<td align="center"><?php echo  $pageNav->limitstart + $i + 1; ?></td>
						<td align="left">
							<a href="<?php echo $link; ?>" title="<?php echo str_replace('"','&quot;',$row->c_title);?>">
							<?php echo $row->c_title; ?>
							</a>
						<?php if($JLMS_CONFIG->get('show_quizzes_authors', 0)){?>
						<br />
						<span class="small"><?php echo _JLMS_HOME_AUTHOR . "&nbsp;" . $row->author_name;?></span>
						<?php } ?>
						</td>
						<td align="left">
							<?php echo $row->c_category?$row->c_category:'&nbsp;'; ?>
						</td>
						<td align="left">
							<?php echo $row->c_passing_score . '%'; ?>
						</td>
						<?php
						/*
						<td align="center" width="36" nowrap="nowrap">
							<?php 
							if(!isset($row->user_score)) { $row->user_score = 0; }
							if(!isset($row->quiz_max_score)) { $row->quiz_max_score = 0; }	
							
							echo ($row->user_passed == -1) ? "&nbsp;-&nbsp;" : ($row->user_score."&nbsp;/&nbsp;".$row->quiz_max_score);?>
						</td>
						<td align="left">	 
							<?php if(!isset($row->link_certificate)) {
							 	echo (isset($row->user_passed) && ($row->user_passed == 1))?('<img class="JLMS_png" width="16" height="16" border="0" alt="passed" src="'.$JLMS_CONFIG->get('live_site').'/components/com_joomla_lms/lms_images/toolbar/btn_accept.png"/>'):('<img class="JLMS_png" width="16" height="16" border="0" alt="failed" src="'.$JLMS_CONFIG->get('live_site').'/components/com_joomla_lms/lms_images/toolbar/btn_cancel.png"/>');
							 }
							 echo (isset($row->link_certificate))?$row->link_certificate:''?>
						</td>
						*/
						?>
						<td width="1%">
							<?php
							echo JLMS_showQuizStatus($row);
							?>
						</td>
					</tr>
					<?php
					$k = 3 - $k;
				}
				?>
				<tr>
					<td align="center" colspan="<?php echo $quizzes_colspan;?>" class="<?php echo JLMSCSS::_('jlmslist-footer_td');?>">
						<div align="center" style="white-space:nowrap">
						<?php 
						$link = "index.php?option=$option&amp;Itemid=$Itemid&amp;task=quizzes&amp;id=$id";
						echo _JLMS_PN_DISPLAY_NUM .  '&nbsp;' . $pageNav->getLimitBox( $link ) . '&nbsp;' . $pageNav->getPagesCounter();
						echo '<br />';
						echo $pageNav->writePagesLinks( $link ); ?> 
						</div>
					</td>
				</tr>
				</table>
				</td>
			</tr>
		</table>
<?php
		} else {
			echo '<div class="joomlalms_user_message">'._JLMS_NO_ITEMS_HERE.'</div>';
		}
?>
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="task" value="quizzes" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="id" value="<?php echo $id;?>" />
		</form>
		<?php JLMS_quiz_admin_html_class::showQuizFooter(); ?>
		<?php
	}

	//Joomla 1.6 ready
	function JQ_editQuiz( &$row, &$lists, $option, $page, $course_id, &$params, $levels, $count_array ) {
		global $Itemid, $JLMS_CONFIG;
		$toolbar = array();
		$toolbar[] = array('btn_type' => 'save', 'btn_txt' => _JLMS_QUIZ_SAVE_QUIZ_BTN, 'btn_js' => "javascript:submitbutton('save_quiz');");
		$toolbar[] = array('btn_type' => 'apply', 'btn_txt' => _JLMS_QUIZ_APPLY_BTN, 'btn_js' => "javascript:submitbutton('apply_quiz');");
		$toolbar[] = array('btn_type' => 'cancel', 'btn_txt' => _JLMS_QUIZ_CANCEL_BTN, 'btn_js' => "javascript:submitbutton('cancel_quiz');");

		
		$h = $row->c_id ? _JLMS_QUIZ_EDIT_QUIZ_TITLE : _JLMS_QUIZ_NEW_QUIZ_TITLE ;
		JLMS_quiz_admin_html_class::showQuizHead( $course_id, $option, $h, true, $toolbar );?>
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

	if (pressbutton == 'cancel_quiz') {
		form.page.value = pressbutton;
		form.submit();
		return;
	}
	// do field validation
	if (form.c_title.value == "") {
		alert( "<?php echo _JLMS_PL_ENTER_NAME;?>" );
	} else {
		form.page.value = pressbutton;
		form.submit();
	}
}

function gradebook_off(e){
	var form = document.adminForm;
	if(form['params[sh_self_verification]'].checked == 1){
		form.c_certificate.value = 0;
		form.c_certificate.disabled = 1;
		
		form.c_gradebook.value = 0;
		form.c_gradebook_chk.checked = 0;
		form.c_gradebook_chk.disabled = 1;
		
		form.c_enable_review.value = 0;
		form.c_enable_review_chk.checked = 0;
		form.c_enable_review_chk.disabled = 1;
		form['params[sh_user_answer]'].value = 0;
		form['params[sh_user_answer]'].checked = 0;
		form['params[sh_user_answer]'].disabled = 1;
	} else {
		form.c_certificate.disabled = 0;
		form.c_gradebook_chk.disabled = 0;	
		form.c_enable_review_chk.disabled = 0;
		form['params[sh_user_answer]'].disabled = 0;
	}
}
//-->
</script>
		<form action="<?php echo sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid");?>" method="post" name="adminForm" onsubmit="setgood();">
	<?php
		JLMS_TMPL::OpenMT('jlms_table_no_borders');
		JLMS_TMPL::OpenTS();

		$tabs = new JLMSTabs(0);
		echo $tabs->startPane("JLMS");
		echo $tabs->startTab(_JLMS_QUIZ_E_PARAMS_TAB, "jlmstab1");?>
		
		<table width="100%" cellpadding="0" cellspacing="0" border="0" class="jlms_item_properties">
			<tr>
				<td width="20%" valign="middle" align="left"><?php echo _JLMS_ENTER_NAME;?></td>
				<td>
					<input size="40" class="inputbox" type="text" name="c_title" value="<?php echo $row->c_title;?>" />
				</td>
			</tr>
			<tr>
				<td valign="middle" align="left"><br /><?php echo _JLMS_QUIZ_ENTER_CAT;?></td>
				<td><br /><?php echo $lists['jq_categories']; ?></td>
			</tr>
			<tr>
				<td valign="middle" align="left"><br /><?php echo _JLMS_QUIZ_CHOOSE_CRTF;?></td>
				<td><br /><?php echo $lists['jq_certificates']; ?></td>
			</tr>
			<tr>
				<td valign="middle" align="left"><br /><?php echo _JLMS_QUIZ_TIME_LIMIT;?></td>
				<td><br /><input class="inputbox" type="text" name="c_time_limit" size="50" maxlength="100" value="<?php echo $row->c_time_limit; ?>" /></td>
			</tr>
			<tr>
				<td valign="middle" align="left"><br /><?php echo _JLMS_QUIZ_PASSING_SCORE;?></td>
				<td><br /><input class="inputbox" type="text" name="c_passing_score" size="50" maxlength="100" value="<?php echo $row->c_passing_score; ?>" /></td>
			</tr>
			<tr>
				<td valign="middle" align="left"><br /><?php echo _JLMS_STATUS_PUB;?></td>
				<td><br />
					<?php echo $lists['published']; ?>
				</td>
			</tr>
			<tr>
				<td valign="top" style="vertical-align:top "><br /><?php echo _JLMS_IS_TIME_RELATED;?></td>
				<td><br />
					<?php JLMS_HTML::_('showperiod.field', $row->is_time_related, $row->show_period ) ?>
				</td>
			</tr>
			<tr>
				<td colspan="2"><br /><?php echo _JLMS_DESCRIPTION;?></td>
			</tr>
			<tr>
				<td colspan="2">
				<?php
					JLMS_editorArea( 'editor2', $row->c_description, 'c_description', '100%;', '250', '40', '20' ) ; ?>
				</td>
			</tr>
			<tr>
				<td valign="middle" align="left"><br /><?php echo _RESUME_QUIZ_FROM_LAST_QUESTION;?></td>
				<td><br />
					<?php echo $lists['c_resume']; ?>
				</td>
			</tr>
			<tr>
				<td valign="middle" align="left"><br /><?php echo _MAXIMUM_NUMBER_OF_ATTEMPTS;?>:</td>
				<td><br /><input class="inputbox" type="text" name="c_max_numb_attempts" size="50" maxlength="100" value="<?php echo $row->c_max_numb_attempts; ?>" /></td>
			</tr>
			<tr>
				<td valign="middle" align="left"><br /><?php echo _JLMS_QUIZ_TIME_LIMIT_SAME_QUIZ;?></td>
				<td><br /><input class="inputbox" type="text" name="c_min_after" size="50" maxlength="100" value="<?php echo $row->c_min_after; ?>" /></td>
			</tr>
			<tr>
				<td valign="middle" align="left"><br /><?php echo _JLMS_SHOW_IN_SELF_VERIFICATION;?></td>
				<td><br />
					<input type="checkbox" name="params[sh_self_verification]" value="1" <?php echo ($params->get('sh_self_verification') == 1)?"checked":""; ?> onclick="javascript: gradebook_off(this);" />
				</td>
			</tr>
			<tr>
				<td valign="middle" align="left"><br /><?php echo _JLMS_SHOW_IN_GRADEBOOK_OPTION;?></td>
				<td><br />
					<input type="hidden" name="c_gradebook" value="<?php echo $row->c_gradebook; ?>" />
					<input type="checkbox" name="c_gradebook_chk" <?php echo ($params->get('sh_self_verification') == 1)?"disabled":""; ?> onclick="javascript: this.form['c_gradebook'].value = (this.checked)?1:0;" <?php echo ($row->c_gradebook == 1)?"checked":""; ?> />
				</td>
			</tr>
			<tr>
				<td valign="middle" align="left"><br /><?php echo _JLMS_QUIZ_RANDOMIZE_OPTION;?></td>
				<td><br />
					<input type="hidden" name="c_random" value="<?php echo $row->c_random; ?>" />
					<input type="checkbox" name="c_random_chk" onclick="javascript: this.form['c_random'].value = (this.checked)?1:0;" <?php echo ($row->c_random == 1)?"checked":""; ?> />
				</td>
			</tr>
			<tr>
				<td valign="middle" align="left"><br /><?php echo _JLMS_QUIZ_SKIP_QUEST;?></td>
				<td><br />
					<input type="checkbox" name="params[sh_skip_quest]" value="1" <?php echo ($params->get('sh_skip_quest') == 1)?"checked":""; ?> />
				</td>
			</tr>
			<tr>
				<td valign="middle" align="left"><br /><?php echo _JLMS_QUIZ_EMAIL_OPTION;?></td>
				<td><br />
					<?php echo $lists['user_email_to']; ?>
				</td>
			</tr>
			<tr>
				<td valign="middle" align="left"><br /><?php echo _JLMS_QUIZ_PRINT_OPTION;?></td>
				<td><br />
					<input type="hidden" name="c_enable_print" value="<?php echo $row->c_enable_print; ?>" />
					<input type="checkbox" name="c_enable_print_chk" onclick="javascript: this.form['c_enable_print'].value = (this.checked)?1:0;" <?php echo ($row->c_enable_print == 1)?"checked":""; ?> />
				</td>
			</tr>
			<tr>
				<td valign="middle" align="left"><br /><?php echo _JLMS_QUIZ_SHOW_PANEL_CONTENT;?></td>
				<td><br />
					<input type="hidden" name="c_slide" value="<?php echo $row->c_slide; ?>" />
					<input type="checkbox" name="c_slide_chk" onclick="javascript: this.form['c_slide'].value = (this.checked)?1:0;" <?php echo ($row->c_slide == 1)?"checked":""; ?> />
				</td>
			</tr>
			
		</table>
		<?php echo $tabs->endTab();
			echo $tabs->startTab(_JLMS_QUIZ_QUEST_POOL, "jlmstab2"); ?>
		<table width="100%" cellpadding="0" cellspacing="0" border="0" class="jlms_item_properties">
			<tr>
				<td colspan="2">
					<?php echo JLMSCSS::h2(_JLMS_QUIZ_ADD_QUESTS_FROM_POOL);?>
				</td>
			</tr>
			<tr>
				<td width="30%">
					<input type="radio" id="pool_quest_type" name="c_pool_type" value="1"<?php echo $lists['pool_quest_mode']?' checked="checked"':'';?> /><label for="pool_quest_type"><?php echo _JLMS_QUIZ_ADD_POOL_MODE_QUEST;?></label>
				</td>
				<td>
					<input type="text" name="pool_qtype_number" size="3" value="<?php echo ($lists['pool_quest_num'] ? $lists['pool_quest_num'] : 0)?>" />
				</td>
			</tr>
			<tr>
				<td colspan="2" align="left" style="text-align:left">
					<input type="radio" id="pool_cat_type" name="c_pool_type" value="2"<?php echo !$lists['pool_quest_mode']?' checked="checked"':'';?> /><label for="pool_cat_type"><?php echo _JLMS_QUIZ_ADD_POOL_MODE_CAT;?></label>
				</td>
			</tr>
			<?php
				$k = 1;
				for ($i=0, $n=count($lists['jq_pool_categories']); $i < $n; $i++) {
					$plc = $lists['jq_pool_categories'][$i];
					echo "<tr>";
					echo '<td width="30%" align="left">'.$plc->c_category."</td>";
					echo '<td>';
					echo '<input type="hidden" name="pool_cat_id[]" value="'.$plc->c_id.'" />';
					echo '<input type="text" name="pool_cat_number[]" size="3" value="'.($plc->items_number?$plc->items_number:0).'" />';
					echo '</td></tr>';
				}
			?>
			<?php 
			if ($JLMS_CONFIG->get('global_quest_pool')){
				$gqp_title_text = _JLMS_QUIZ_ADD_QUESTS_FROM_GLOBAL_POOL;
				if ($JLMS_CONFIG->get('is_trial', false) && $JLMS_CONFIG->get('trial_gqp_heading_text', '')) {
					$gqp_title_text .= $JLMS_CONFIG->get('trial_gqp_heading_text', '');
				}
			?>
				<tr>
					<td colspan="2">
						<?php echo JLMSCSS::h2($gqp_title_text);?>
					</td>
				</tr>
				<tr>
					<td width="30%">
					<input type="radio" id="pool_quest_type_gqp" name="c_pool_type_gqp" value="1"<?php if(($row->flag == 1) || !$row->flag) echo ' checked="checked"'; else echo '';?> onclick="javascript:document.adminForm.page.value='edit_quiz'; document.adminForm.flag.value=1; document.adminForm.submit();" /><label for="pool_quest_type_gqp"><?php echo _JLMS_QUIZ_ADD_POOL_MODE_QUEST;?></label>
					</td>
					<td>
					<input type="text" name="pool_qtype_number_gqp" size="3" value="<?php echo ($lists['pool_quest_num_gqp'] ? $lists['pool_quest_num_gqp'] : 0)?>" />
					</td>
				</tr>
				<tr>
					<td colspan="2" align="left" style="text-align:left">
						<input type="radio" id="pool_cat_type_gqp" name="c_pool_type_gqp" value="2"<?php if($row->flag == 2) echo ' checked="checked"'; else echo '';?> onclick="javascript:document.adminForm.page.value='edit_quiz'; document.adminForm.flag.value=2; document.adminForm.submit();" />
						<label for="pool_cat_type_gqp"><?php echo _JLMS_QUIZ_ADD_POOL_MODE_CAT;?></label>
					</td>
				</tr>
				<?php
				if($row->flag == 2) {
					if(isset($lists['category'])) {
						for($i=0;$i<count($lists['category']);$i++) {?>
							<tr>
								<td>
									<table border="0" width="100%" cellpadding="2" cellspacing="2" class="jlms_table_no_borders">
										<tr>
											<?php 
											if($levels[$i]!=0) {
														JLMS_quiz_admin_html_class::view_separators($levels, $i);
											}?>				
											<td align="right" width="100%"><?php echo $lists['category'][$i];?></td>
										</tr>
									</table>
								</td>
								<td><input type="text" name="pool_cat_number_gqp[]" size="3" value="<?php if(isset($count_array[$i])) echo $count_array[$i];?>" /></td>
							</tr>
						<?php }
					}
					?>
					<tr>
						<td style="padding:4px;"><?php echo $lists['new_category'];?></td>
						<td><input type="text" name="pool_cat_number_gqp[]" size="3" value="" /></td>
					</tr>
				<?php 
				}
				
				/*
				$k = 1;
				for ($i=0, $n=count($lists['jq_pool_categories_gqp']); $i < $n; $i++) {
					$plc = $lists['jq_pool_categories_gqp'][$i];
					echo "<tr class='sectiontableentry$k'>";
					echo '<td width="30%" align="left">'.$plc->c_category."</td>";
					echo '<td>';
					echo '<input type="hidden" name="pool_cat_id_gqp[]" value="'.$plc->id.'" />';
					echo '<input type="text" name="pool_cat_number_gqp[]" size="3" value="'.($plc->items_number?$plc->items_number:0).'" />';
					echo '</td></tr>';
				}
				*/
				?>
		<?php 
		}
		?>
		</table>
		
		<?php echo $tabs->endTab();
			echo $tabs->startTab(_JLMS_QUIZ_E_FEEDBACKS_TAB, "jlmstab3"); ?>
		<table width="100%" cellpadding="0" cellspacing="0" border="0" class="jlms_item_properties">
			<tr>
				<td valign="middle" align="left"><br /><?php echo _JLMS_QUIZ_FORCE_DIS_QUEST_FEEDBACK;?></td>
				<td><br />
					<input type="checkbox" name="params[disable_quest_feedback]" value="1" <?php echo ($params->get('disable_quest_feedback') == 1)?"checked":""; ?> />
				</td>
			</tr>
			<tr>
				<td valign="middle" align="left"><br /><?php echo _JLMS_QUIZ_MES_ANSWER_CORRECT;?></td>
				<td><br /><textarea class="inputbox" name="c_right_message" cols="50" rows="5"><?php echo $row->c_right_message; ?></textarea></td>
			</tr>
			<tr>
				<td valign="middle" align="left"><br /><?php echo _JLMS_QUIZ_MES_ANSWER_INCORRECT;?></td>
				<td><br /><textarea class="inputbox" name="c_wrong_message" cols="50" rows="5"><?php echo $row->c_wrong_message; ?></textarea></td>
			</tr>
			<tr>
				<td valign="middle" align="left"><br /><?php echo _JLMS_QUIZ_MES_USER_PASSES;?></td>
				<td><br /><textarea class="inputbox" name="c_pass_message" cols="50" rows="5"><?php echo $row->c_pass_message; ?></textarea></td>
			</tr>
			<tr>
				<td valign="middle" align="left"><br /><?php echo _JLMS_QUIZ_MES_USER_FAILS;?></td>
				<td><br /><textarea class="inputbox" name="c_unpass_message" cols="50" rows="5"><?php echo $row->c_unpass_message; ?></textarea></td>
			</tr>
		</table>
		<?php echo $tabs->endTab();
		echo $tabs->startTab(_JLMS_QUIZ_QUESTION_REVIEW_SETTINGS, "jlmstab4"); ?>
		<table width="100%" cellpadding="0" cellspacing="0" border="0" class="jlms_item_properties">
			<tr>
				<td width="25%" valign="middle" align="left"><br /><?php echo _JLMS_QUIZ_REVIEW_OPTION;?></td>
				<td><br />
					<input type="hidden" name="c_enable_review" value="<?php echo ($params->get('sh_self_verification') == 1)?0:$row->c_enable_review; ?>" />
					<?php
					/*
					<input type="checkbox" name="c_enable_review_chk" <?php echo ($params->get('sh_self_verification') == 1)?"disabled":""; ?> onclick="javascript: this.form['c_enable_review'].value = (this.checked)?1:0;" <?php echo ($row->c_enable_review == 1)?"checked":""; ?> />
					*/
					?>
					<?php echo $lists['c_enable_review_chk'];?>
				</td>
			</tr>
			<tr>
				<td valign="middle" align="left"><br /><?php echo _JLMS_QUIZ_REVIEW_OPTION_USER_ANS;?></td>
				<td><br />
					<?php
					/*
					<input type="checkbox" name="params[sh_user_answer]" <?php echo ($params->get('sh_self_verification') == 1)?"disabled":""; ?> value="1" <?php echo ($params->get('sh_user_answer') == 1)?"checked":""; ?> />
					*/
					?>
					<?php echo $lists['sh_user_answer'];?>
				</td>
			</tr>
			<tr>
				<td valign="middle" align="left"><br /><?php echo _JLMS_QUIZ_REVIEW_OPTION_CORRECT_ANS;?></td>
				<td><br />
					<?php
					/*
					<input type="checkbox" name="params[sh_user_answer]" <?php echo ($params->get('sh_self_verification') == 1)?"disabled":""; ?> value="1" <?php echo ($params->get('sh_user_answer') == 1)?"checked":""; ?> />
					*/
					?>
					<?php echo $lists['sh_correct_answer'];?>
				</td>
			</tr>
			<tr>
				<td valign="middle" align="left"><br /><?php echo _JLMS_QUIZ_REVIEW_OPTION_EXPLAIN;?></td>
				<td><br />
					<?php echo $lists['quiz_explanation']; ?>
				</td>
			</tr>
		</table>
		<?php echo $tabs->endTab();
		echo $tabs->startTab(_JLMS_ADVANCED,"jlmstab5"); ?>
			<table cellpadding="0" cellspacing="0" border="0" width="100%" class="jlms_item_properties">
				<tr>
					<td width="25%"><br />
						<label for="f_page1">
						<?php echo _JLMS_QUIZ_FINAL_SHOW_RESULTS;?>
						</label>
					</td>
					<td><br />
						<?php #echo $lists['sh_final_page_text'];?>
						<input type="radio" value="0" id="sh_final_page_text" name="params[sh_final_page_text]" <?php echo $params->get('sh_final_page_text')?'':'checked="checked"';?>/>
						<label for="sh_final_page_text"><?php echo _CMN_NO;?></label>
						<input type="radio" value="1" id="sh_final_page_text" name="params[sh_final_page_text]" <?php echo $params->get('sh_final_page_text')?'checked="checked"':'';?>/>
						<label for="sh_final_page_text"><?php echo _CMN_YES;?></label>
					</td>
				</tr>
				<tr>
					<td align="left" style="text-align:left"><br />
					<?php /* <!--<input type="radio" id="f_page2"  name="params[sh_final_page]" value="2"<?php echo ($params->get('sh_final_page')==2)?' checked="checked"':'';?>><label for="f_page2"><?php echo "Show content text";?></label>--> */ ?>
						<label for="f_page2">
							<?php echo _JLMS_QUIZ_FINAL_DISPLAY_BARS;?>
						</label>
					</td>
					<td><br />
						<?php #echo $lists['sh_final_page_grafic'];?>
						<input type="radio" value="0" id="sh_final_page_grafic" name="params[sh_final_page_grafic]" <?php echo $params->get('sh_final_page_grafic')?'':'checked="checked"';?>/>
						<label for="sh_final_page_grafic"><?php echo _CMN_NO;?></label>
						<input type="radio" value="1" id="sh_final_page_grafic" name="params[sh_final_page_grafic]" <?php echo $params->get('sh_final_page_grafic')?'checked="checked"':'';?>/>
						<label for="sh_final_page_grafic"><?php echo _CMN_YES;?></label>
					</td>
				</tr>
				<tr>
					<td align="left" style="text-align:left"><br />
						<label for="f_page3">
							<?php echo _JLMS_QUIZ_FINAL_SHOW_FEEDBACK;?>
						</label>
					</td>
					<td><br />
						<?php #echo $lists['sh_final_page_fdbck'];?>
						<input type="radio" value="0" id="sh_final_page_fdbck" name="params[sh_final_page_fdbck]" <?php echo $params->get('sh_final_page_fdbck')?'':'checked="checked"';?>/>
						<label for="sh_final_page_fdbck"><?php echo _CMN_NO;?></label>
						<input type="radio" value="1" id="sh_final_page_fdbck" name="params[sh_final_page_fdbck]" <?php echo $params->get('sh_final_page_fdbck')?'checked="checked"':'';?>/>
						<label for="sh_final_page_fdbck"><?php echo _CMN_YES;?></label>
					</td>
				</tr>
			</table>
		<?php echo $tabs->endTab();
		echo $tabs->endPane();
		JLMS_TMPL::CloseTS();
		JLMS_TMPL::CloseMT(); ?>
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
		<input type="hidden" name="task" value="quizzes" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="id" value="<?php echo $course_id;?>" />
		<input type="hidden" name="page" value="save_quiz" />
		<input type="hidden" name="c_id" value="<?php echo $row->c_id; ?>" />
		<input type="hidden" name="flag" value="<?php echo $row->flag; ?>" />
		</form>
			
		<?php
		
		if($row->flag) {?>
		<script language="javascript" type="text/javascript">
			tabPane1.setSelectedIndex(1);
		</script>
		<?php } ?>

		<?php JLMS_quiz_admin_html_class::showQuizFooter(); ?>

		<?php
	}

	//J1.6 ready
	function view_separators($levels, $i) {
		global $JLMS_CONFIG;
		
		for($j=0;$j<$levels[$i];$j++) {
			if($i == (count($levels) - 1)) {
				echo "<td><img src='".$JLMS_CONFIG->get('live_site')."/components/com_joomla_lms/lms_images/treeview/sub2.png'></td>";
			}
			elseif($j == 0) {
				if(isset($levels[$i+1]) && ($levels[$i+1] > $levels[$i])) {
					echo "<td><img src='".$JLMS_CONFIG->get('live_site')."/components/com_joomla_lms/lms_images/treeview/sub1.png'></td>";
				}	
				elseif(isset($levels[$i+1]) && ($levels[$i+1] < $levels[$i]) && $levels[$i] < 2) {
					echo "<td><img src='".$JLMS_CONFIG->get('live_site')."/components/com_joomla_lms/lms_images/treeview/sub2.png'></td>";
				}	
				else {
					echo "<td><img src='".$JLMS_CONFIG->get('live_site')."/components/com_joomla_lms/lms_images/treeview/line.png'></td>";
				}
			}
			else {
				if(isset($levels[$i+1]) && ($levels[$i+1] < $levels[$i])) {
					echo "<td><img src='".$JLMS_CONFIG->get('live_site')."/components/com_joomla_lms/lms_images/treeview/sub2.png'></td>";
				}	
				else {
					echo "<td><img src='".$JLMS_CONFIG->get('live_site')."/components/com_joomla_lms/lms_images/treeview/sub1.png'></td>";
				}
			}
		}
	}

	//Joomla 1.6 optimized
	function JQ_moveQuiz_Select( $option, $page, $course_id, $cid, $CategoryList, $items ) {
		global $Itemid;
		$ttt = 'move_quiz_save';
		$ttt1 = _JLMS_QUIZ_MOVE_QUIZ_BTN;
		if ($page == 'copy_quiz_sel') { $ttt = 'copy_quiz_save'; $ttt1 = _JLMS_QUIZ_COPY_QUIZ_BTN; }
		$toolbar = array();
		$toolbar[] = array('btn_type' => 'save', 'btn_txt' => $ttt1, 'btn_js' => "javascript:submitbutton('".$ttt."');");
		$toolbar[] = array('btn_type' => 'cancel', 'btn_txt' => _JLMS_QUIZ_CANCEL_BTN, 'btn_js' => "javascript:submitbutton('cancel_quiz');");
		$h = ($page == 'move_quiz_sel') ? _JLMS_QUIZ_MOVE_QUIZ_TITLE : _JLMS_QUIZ_COPY_QUIZ_TITLE ;
		JLMS_quiz_admin_html_class::showQuizHead( $course_id, $option, $h, true, $toolbar );?>
		<script language="javascript" type="text/javascript">
		<!--
		function submitbutton(pressbutton) {
			var form = document.adminForm;
			if (pressbutton == 'cancel_quiz') {
				form.page.value = pressbutton;
				form.submit();
				return;
			}
			// do field validation
			if (form.categorymove.value == '0'){
				alert( "<?php echo _JLMS_QUIZ_COPY_MOVE_ALERT;?>" );
			} else {
				form.page.value = pressbutton;
				form.submit();
			}
		}
		//-->
		</script>
		<form action="<?php echo sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid");?>" method="post" name="adminForm">
		<table width="100%" cellpadding="0" cellspacing="0" border="0" class="jlms_table_no_borders">
			<tr>
				<td align="left" valign="top">
				<?php echo '<strong>'._JLMS_QUIZ_COPY_MOVE_TO.'</strong>'. $CategoryList .'<br />'. _JLMS_QUIZ_COPY_MOVE_TIP; ?>
				</td>
			</tr>
			<tr>
				<td>
					<table width="100%" cellpadding="0" cellspacing="0" border="0" class="<?php echo JLMSCSS::_('jlmslist');?>">
					<tr>
						<<?php echo JLMSCSS::tableheadertag();?> width="20" class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center">#</<?php echo JLMSCSS::tableheadertag();?>>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="left"><?php echo _JLMS_QUIZ_COPY_MOVE_HEADER;?></<?php echo JLMSCSS::tableheadertag();?>>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="left"><?php echo _JLMS_QUIZ_COPY_MOVE_FROM;?></<?php echo JLMSCSS::tableheadertag();?>>
					</tr>
					<?php
					$k = 1; $i = 1;
					foreach ( $items as $item ) { ?>
						<tr class="<?php echo JLMSCSS::_('sectiontableentry'.$k); ?>">
							<td align="center"><?php echo $i; ?></td>
							<td align="left">
									<?php echo $item->quiz_name;?>
							</td>
							<td align="left"><?php echo $item->category_name;?></td>
						</tr>
						<?php
						$k = 3 - $k;
						$i ++;
					}?>
					</table>
				</td>
			</tr>
		</table>
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
		<input type="hidden" name="task" value="quizzes" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="id" value="<?php echo $course_id;?>" />
		<input type="hidden" name="page" value="move_quiz_sel" />
		<?php
		foreach ( $cid as $id ) {
			echo "\n <input type=\"hidden\" name=\"cid[]\" value=\"$id\" />";
		}
		?>
		</form>
		<?php JLMS_quiz_admin_html_class::showQuizFooter(); ?>
		<?php
	} 

	//Joomla 1.6 optimized
	function JQ_showQuestsList( &$rows, &$lists, &$pageNav, $option, $page, $id, $is_pool = false, $gqp = false, $levels = array() ) {
		$JLMS_CONFIG = & JLMSFactory::getConfig();
		$Itemid = $JLMS_CONFIG->get('Itemid');
		
		$zzz = '<form action="'.$JLMS_CONFIG->get('live_site')."/index.php?option=$option&amp;Itemid=$Itemid".'" method="post" name="adminFormQ">';
		$toolbar = array();
		
		if(!$gqp) {
			$toolbar[] = array('btn_type' => 'csv_import', 'btn_txt' => _JLMS_QUIZ_TBL_QUEST_IMPORT_QUEST, 'btn_js' => "javascript:submitbutton('import_quest');");
			$toolbar[] = array('btn_type' => 'csv_export', 'btn_txt' => _JLMS_QUIZ_TBL_QUEST_EXPORT_QUEST, 'btn_js' => "javascript:submitbutton('export_quest');");
			$toolbar[] = array('btn_type' => 'spacer', 'btn_txt' => 'spacer', 'btn_js' => 'spacer');
			$toolbar[] = array('btn_type' => 'new', 'btn_txt' => _JLMS_QUIZ_NEW_QUEST_BTN, 'btn_js' => "javascript:submitbutton('add_quest');");
		} else {
			$toolbar[] = array('btn_type' => 'category', 'btn_txt' => _JLMS_QUIZ_TBL_CATEGORY_GQP, 'btn_js' => "javascript:submitbutton('category_gqp');");
			$toolbar[] = array('btn_type' => 'spacer', 'btn_txt' =>'spacer', 'btn_js' => 'spacer');
			$toolbar[] = array('btn_type' => 'csv_import', 'btn_txt' => _JLMS_QUIZ_TBL_QUEST_IMPORT_QUEST, 'btn_js' => "javascript:submitbutton('import_quest_gqp');");
			$toolbar[] = array('btn_type' => 'csv_export', 'btn_txt' => _JLMS_QUIZ_TBL_QUEST_EXPORT_QUEST, 'btn_js' => "javascript:submitbutton('export_quest_gqp');");
			$toolbar[] = array('btn_type' => 'spacer', 'btn_txt' =>'spacer', 'btn_js' => 'spacer');
			$toolbar[] = array('btn_type' => 'bar', 'btn_txt' => _JLMS_QUIZ_VIEW_STATS_GQP, 'btn_js' => "javascript:submitbutton('quiz_bars_gqp');");
			$toolbar[] = array('btn_type' => 'spacer', 'btn_txt' =>'spacer', 'btn_js' => 'spacer');
			$toolbar[] = array('btn_type' => 'new', 'btn_txt' => _JLMS_QUIZ_NEW_QUEST_BTN, 'btn_js' => "javascript:submitbutton('add_quest_gqp');");
		}
		$add_option = $lists['new_qtype'];

		if($gqp) {
			$title = _JLMS_GLOBAL_QUEST_POOL;
			if ($JLMS_CONFIG->get('is_trial', false) && $JLMS_CONFIG->get('trial_gqp_heading_text', '')) {
				$title .= $JLMS_CONFIG->get('trial_gqp_heading_text', '');
			}
		} elseif($is_pool) {
			$title = _JLMS_QUIZ_QUEST_POOL;	
		} else {
			$title = _JLMS_QUIZ_QUESTLIST_TITLE;
		}

		if($gqp && $JLMS_CONFIG->get('is_trial', false) && $JLMS_CONFIG->get('trial_gqp_page_text', '')) {
			echo '<div class="joomlalms_sys_message">'.$JLMS_CONFIG->get('trial_gqp_page_text', '').'</div>';
		}

		JLMS_quiz_admin_html_class::showQuizHead2( $id, $option, $title, true, $toolbar, $add_option, $zzz, $gqp );

		//FLMS multicat
		$multicat = array();
		if ($gqp) {
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
		<!--
		function checkAllQ( n, fldName ) {
			if (!fldName) {
				fldName = 'cb';
			}
			var f = document.adminFormQ;
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
				document.adminFormQ.boxchecked.value = n2;
			} else {
				document.adminFormQ.boxchecked.value = 0;
			}
		}
		function submitbutton(pressbutton) {
			var form = document.adminFormQ;
			if ((pressbutton == 'add_quest') || (pressbutton == 'add_quest_gqp')) {
				if (form.new_qtype_id.value == '0') {
					alert('<?php echo html_entity_decode(_JLMS_QUIZ_SELECT_TYPE_TO_CREATE);?>');
				} else {
					form.page.value = pressbutton;
					form.submit();
				}
			} else if ( ((pressbutton == 'edit_quest' || pressbutton == 'edit_quest_gqp') || (pressbutton == 'del_quest' || pressbutton == 'del_quest_gqp') || (pressbutton == 'copy_quest_sel' || pressbutton == 'copy_quest_sel_gqp') || (pressbutton == 'move_quest_sel' || pressbutton == 'move_quest_sel_gqp') ) && (form.boxchecked.value == "0")) {
				alert('<?php echo _JLMS_ALERT_SELECT_ITEM;?>');
			} else if( (pressbutton == 'publish_quest' || pressbutton == 'unpublish_quest' || pressbutton == 'publish_quest_gqp' || pressbutton == 'unpublish_quest_gqp') && form.boxchecked.value == 0){
				alert('<?php echo _JLMS_ALERT_SELECT_ITEM;?>'); 
			} else if( (pressbutton == 'export_quest' || pressbutton == 'export_quest_gqp') && form.boxchecked.value == 0 && !confirm('<?php echo _JLMS_QUIZ_EXPORT_ALL_QUESTS_CONFIRM;?>')){
				alert('<?php echo _JLMS_ALERT_SELECT_ITEM;?>'); 
			} else {
				form.page.value = pressbutton;
				form.submit();
			}
		}
		function submitbutton_order(pressbutton, item_id) {
			var form = document.adminFormQ;
			if ((pressbutton == 'quest_orderup') || (pressbutton == 'quest_orderdown')){
				if (item_id) {
				form.page.value = pressbutton;
				form.row_id.value = item_id;
				form.submit();
				}
			}
		}
		function submitbutton_allorder(n) {
			var form = document.adminFormQ;
			for ( var j = 0; j <= n; j++ ) {
				box = eval( "document.adminFormQ.cb" + j );
				if ( box ) {
					if ( box.checked == false ) {
						box.checked = true;
					}
				}
			}
			form.page.value = 'saveorederall';
			form.submit();
		}
		function submit_preview() {
			var quest_id = 0;
			var form = document.adminFormQ;
			if (form.boxchecked.value == "0") {
				alert('<?php echo _JLMS_ALERT_SELECT_ITEM;?>');
			} else {
				var selItem = document.adminFormQ['cid[]'];
				if (selItem) {
					if (selItem.length) { var i;
						for (i = 0; i<selItem.length; i++) {
							if (selItem[i].checked) {
								if (selItem[i].value > 0) { quest_id = selItem[i].value; break; }
							}
						}
					} else if (selItem.checked) { quest_id = selItem.value; }
				}
				if (quest_id != 0 && quest_id != '0'){
					var url = '<?php echo $JLMS_CONFIG->get('live_site'). "/index.php?option=com_joomla_lms&Itemid=$Itemid&task=quizzes&id=$id&page=view_preview&c_id='+quest_id+'";?>';
					window.open(url);
				}
			}
		}
		function jlms_jq_isChecked(isitchecked){
			if (isitchecked == true){
				document.adminFormQ.boxchecked.value++;
			}
			else {
				document.adminFormQ.boxchecked.value = document.adminFormQ.boxchecked.value - 1;
			}
		} 
		
		var old_filters = new Array();
		function read_filter(){
			var form = document.adminFormQ;
			var count_levels = '<?php echo count($levels);?>';
			for(var i=0;i<parseInt(count_levels);i++){
				if(form['filter_id_'+i] != null){
					old_filters[i] = form['filter_id_'+i].value;
				}
			}
		}
		function write_filter(){
			var form = document.adminFormQ;
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
		//-->
		</script>
		<table width="100%" cellpadding="0" cellspacing="0" border="0" class="jlms_table_no_borders">
			<tr>
				<td>
				<table width="100%" cellpadding="0" cellspacing="0" border="0" class="jlms_table_no_borders">
					<tr>
						<td align="left" style="text-align:left;">
						<?php
							if(!$gqp) {
								echo $lists['quiz'] . $lists['qtype'] . $lists['qcats'];
							}
							else {
								echo $lists['qtype'] /*. $lists['qcats']*/;
							}
							?>
							<br />
							<input class="inputbox" type="text" name="quest_filter" value="<?php echo $lists['filt_quest'];?>" /><input type="submit" name="Filter" value="<?php echo str_replace(':','',_JLMS_FILTER);?>" /> 
							
							<!--</div>-->
						</td>
						<?php 
						if(count($multicat)) {
						?>
						<td align="right" valign="bottom">
							<table border="0" class="jlms_table_no_borders">
							<?php	
								for($i=0;$i<count($multicat);$i++){
									$num = $i + 1;
								?>
								<tr>
									<td align="right" style="text-align:right;" width="20%">
										<?php
										echo (isset($levels[$i]->cat_name) && $levels[$i]->cat_name != '') ? $levels[$i]->cat_name : '';//'Level #'.$num;
										?>
									</td>
									<td align="left" style="text-align:left;" width="80%">
										<?php
											$m_output = $lists['filter_'.$i];
											$m_output = str_replace('<option value="0" selected="selected"></option>', '<option value="0" selected="selected">&nbsp;</option>', $m_output);
											$m_output = str_replace('<option value="0"></option>', '<option value="0">&nbsp;</option>', $m_output);
											echo $m_output;
										?>
									</td>
								</tr>
								<?php
								}
							?>
							</table>
						</td>
						<?php	
						}
						?>	
					</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td width="100%">
				<?php $quests_colspan = 6;?> 
				<table width="100%" cellpadding="0" cellspacing="0" border="0" class="<?php echo JLMSCSS::_('jlmslist');?>" style="padding-top:0px; margin-top:0px; margin-bottom:0px; padding-bottom:0px;">
					<tr>
						<<?php echo JLMSCSS::tableheadertag();?> width="20" class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center">#</<?php echo JLMSCSS::tableheadertag();?>>
						<<?php echo JLMSCSS::tableheadertag();?> width="20" class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center"><input type="checkbox" name="toggle" value="" onclick="checkAllQ(<?php echo count($rows); ?>);" /></<?php echo JLMSCSS::tableheadertag();?>>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>"><?php echo _JLMS_QUIZ_TBL_QUEST_TEXT;?></<?php echo JLMSCSS::tableheadertag();?>>
						<?php if($gqp) {  $quests_colspan = $quests_colspan + 1; ?>
							<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" width="1%"><?php echo _JLMS_QUIZ_TBL_QUEST_PUBLISH;?></<?php echo JLMSCSS::tableheadertag();?>>
						<?php }?>
				<?php if (isset($lists['filtered_quiz']) && $lists['filtered_quiz'] && !$gqp) { $quests_colspan = $quests_colspan + 5; ?>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" width="1%"><?php echo _JLMS_QUIZ_TBL_QUEST_PUBLISH;?></<?php echo JLMSCSS::tableheadertag();?>>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" colspan="2" width="1%"><?php echo _JLMS_REORDER;?></<?php echo JLMSCSS::tableheadertag();?>>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" width="1%"><?php echo _JLMS_ORDER;?></<?php echo JLMSCSS::tableheadertag();?>>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" width="1%"><a class="jlms_img_link" href="javascript:submitbutton_allorder(<?php echo count( $rows )-1;?>)"><img width="16" height="16" border="0" title="<?php echo _JLMS_SAVEORDER;?>" alt="<?php echo _JLMS_SAVEORDER;?>" src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/filesave.png"/></a></<?php echo JLMSCSS::tableheadertag();?>>
				<?php } ?>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>"><?php echo _JLMS_QUIZ_TBL_QUEST_TYPE;?></<?php echo JLMSCSS::tableheadertag();?>>
					<?php if(!$gqp) { $quests_colspan = $quests_colspan + 1; ?>	
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>"><?php echo _JLMS_QUIZ_TBL_QUEST_QUIZ;?></<?php echo JLMSCSS::tableheadertag();?>>
					<?php }?>	
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>"><?php echo _JLMS_QUIZ_TBL_Q_CAT;?></<?php echo JLMSCSS::tableheadertag();?>>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" width="20">ID</<?php echo JLMSCSS::tableheadertag();?>>
					</tr>
				<?php
				$k = 1;
				for ($i=0, $n=count($rows); $i < $n; $i++) {
					$row = $rows[$i];

					$quiz_task = 'setup_quest';
					$img_published	= $row->published ? 'btn_accept.png' : 'btn_cancel.png';
					$task_published	= $row->published ? 'unpublish_quest' : 'publish_quest';
					$alt_published 	= $row->published ? _JLMS_STATUS_PUB : _JLMS_STATUS_UNPUB;
					$state 			= $row->published ? 0 : 1;

					if(!$gqp) {
						$link 	= "index.php?option=".$option."&amp;Itemid=$Itemid&amp;task=quizzes&amp;id=$id&amp;page=editA_quest&amp;c_id=". $row->c_id;
					}	
					else { 
						$link 	= "index.php?option=".$option."&amp;Itemid=$Itemid&amp;task=quizzes&amp;page=editA_quest_gqp&amp;c_id=". $row->c_id;
					}
					$checked = '<input type="checkbox" id="cb'.$i.'" name="cid[]" value="'.$row->c_id.'" onclick="jlms_jq_isChecked(this.checked);" />';
					?>
					<tr class="<?php echo JLMSCSS::_('sectiontableentry'.$k); ?>">
						<td align="center"><?php echo  $pageNav->limitstart + $i + 1;?></td>
						<td><?php echo $checked; ?></td>
						<td align="left">
							<?php
							mosMakeHtmlSafe( $row->c_question );
							$quest_name = jlms_string_substr(strip_tags($row->c_question), 0, 70);
							
							$quest_desc = '';
							if ($row->c_question) {
								$quest_desc = $row->c_question;
							}
							if (isset($row->right_answer) && $row->right_answer) {
								$quest_desc .= '<br /><br /><span class="tip-title-inner">'._JLMS_QUIZ_CORRECT_ANSWER . '</span> ' . $row->right_answer;
							}

							if (isset($row->c_type) && $row->c_type == 21 && isset($row->c_pool_gqp) && $row->c_pool_gqp) {
								$quest_desc = _JLMS_QUIZ_QUEST_POOL_GQP_SHORT.' ID: '.$row->c_pool_gqp.'<br />'.($quest_desc ? ('<br />'.$quest_desc) : '');
							}
														
							echo JLMS_toolTip($quest_name, $quest_desc, '', sefRelToAbs( $link ), 1, 36, 'true', 'jlms_ttip');
							?>
						</td>
						<?php if (isset($lists['filtered_quiz']) && $lists['filtered_quiz'] && !$gqp) { ?>
							<td valign="middle" align="center"><?php echo JLMS_quiz_admin_html_class::QuizPublishIcon( $row->c_id, $id, $state, $task_published, $alt_published, $img_published, $option); ?></td>
							<td valign="middle" align="center"><?php echo JLMS_orderUpIcon($i, $row->c_id, true, 'quest_orderup');?></td>
							<td valign="middle" align="center"><?php echo JLMS_orderDownIcon($i, $n, $row->c_id, true, 'quest_orderdown');?></td>
							<td colspan="2">
								<input type="text" name="order[]" size="5" value="<?php echo $row->ordering; ?>" class="inputbox" style="text-align: center" />
							</td>
						<?php } ?>

						<?php if($gqp) {?>
							<td valign="middle" align="center"><?php echo JLMS_quiz_admin_html_class::QuizPublishIcon( $row->c_id, $id, $state, $task_published, $alt_published, $img_published, $option,$gqp); ?></td>
						<?php }?>

						<td align="left">
							<?php echo $row->qtype_full; ?>
						</td>
						<?php if(!$gqp) {?>	
							<td align="left">
							<?php 
								if ($row->c_quiz_id) {
									echo $row->quiz_name;
								} else {
									echo _JLMS_QUIZ_QUEST_POOL;
								}
							?>
							</td>
						<?php }?>	
						<td align="left">
							<?php echo $row->c_category?$row->c_category:'&nbsp;'; ?>
						</td>
						<td><?php echo $row->c_id; echo $row->c_pool_gqp ? ('/'.$row->c_pool_gqp) : ''; ?></td>
					</tr>
					<?php
					$k = 3 - $k;
				}
				?>
					<tr>
						<td align="center" colspan="<?php echo $quests_colspan;?>" class="<?php echo JLMSCSS::_('jlmslist-footer_td');?>">
							<div align="center" style="white-space:nowrap">
							<?php
								$link = "index.php?option=$option&amp;Itemid=$Itemid&amp;task=quizzes&amp;id=$id&amp;page=$page";
								echo _JLMS_PN_DISPLAY_NUM . '&nbsp;' . $pageNav->getLimitBox( $link ) . '&nbsp;' . $pageNav->getPagesCounter();
								echo '<br />';
								echo $pageNav->writePagesLinks( $link );
							?> 
							</div>
						</td>
					</tr>
				</table>
				</td>
			</tr>
<?php
			if ($gqp) {
				$controls = array();
				$controls[] = array('href' => "javascript:submitbutton('publish_quest_gqp');", 'title' => _JLMS_QUIZ_TBL_QUEST_PUBLISH_QUEST, 'img' => 'buttons_22/btn_publish_22.png');
				$controls[] = array('href' => "javascript:submitbutton('unpublish_quest_gqp');", 'title' => _JLMS_QUIZ_TBL_QUEST_UNPUBLISH_QUEST, 'img' => 'buttons_22/btn_unpublish_22.png');
				$controls[] = array('href' => "spacer", 'title' => '', 'img' => '');
				$controls[] = array('href' => "javascript:submitbutton('edit_quest_gqp');", 'title' => _JLMS_QUIZ_EDIT_QUEST_BTN, 'img' => 'quiz/btn_edit.png');
				$controls[] = array('href' => "javascript:submitbutton('del_quest_gqp');", 'title' => _JLMS_QUIZ_DEL_QUEST_BTN, 'img' => 'quiz/btn_delete.png');
				$controls[] = array('href' => "javascript:submitbutton('copy_quest_sel_gqp');", 'title' => _JLMS_QUIZ_COPY_QUEST_BTN, 'img' => 'quiz/btn_copy.png');
				$controls[] = array('href' => "javascript:submitbutton('move_quest_sel_gqp');", 'title' => _JLMS_QUIZ_MOVE_QUEST_BTN, 'img' => 'quiz/btn_move.png');
				$controls[] = array('href' => "spacer", 'title' => '', 'img' => '');
				$controls[] = array('href' => "javascript:submit_preview();", 'title' => _JLMS_QUIZ_PREVIEW_QUEST_BTN, 'img' => 'quiz/btn_preview.png');
				JLMS_TMPL::ShowControlsFooter($controls, '', false, true);
			} else {
				$controls = array();
				$controls[] = array('href' => "javascript:submitbutton('publish_quest');", 'title' => _JLMS_QUIZ_TBL_QUEST_PUBLISH_QUEST, 'img' => 'buttons_22/btn_publish_22.png');
				$controls[] = array('href' => "javascript:submitbutton('unpublish_quest');", 'title' => _JLMS_QUIZ_TBL_QUEST_UNPUBLISH_QUEST, 'img' => 'buttons_22/btn_unpublish_22.png');
				$controls[] = array('href' => "spacer", 'title' => '', 'img' => '');
				$controls[] = array('href' => "javascript:submitbutton('edit_quest');", 'title' => _JLMS_QUIZ_EDIT_QUEST_BTN, 'img' => 'quiz/btn_edit.png');
				$controls[] = array('href' => "javascript:submitbutton('del_quest');", 'title' => _JLMS_QUIZ_DEL_QUEST_BTN, 'img' => 'quiz/btn_delete.png');
				$controls[] = array('href' => "javascript:submitbutton('copy_quest_sel');", 'title' => _JLMS_QUIZ_COPY_QUEST_BTN, 'img' => 'quiz/btn_copy.png');
				$controls[] = array('href' => "javascript:submitbutton('move_quest_sel');", 'title' => _JLMS_QUIZ_MOVE_QUEST_BTN, 'img' => 'quiz/btn_move.png');
				$controls[] = array('href' => "spacer", 'title' => '', 'img' => '');
				$controls[] = array('href' => "javascript:submit_preview();", 'title' => _JLMS_QUIZ_PREVIEW_QUEST_BTN, 'img' => 'quiz/btn_preview.png');
				JLMS_TMPL::ShowControlsFooter($controls, '', false, true);
			}
?>
		</table>
		<?php JLMS_quiz_admin_html_class::showQuizFooter(); ?>
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="page" value="<?php echo $page;?>" />
		<input type="hidden" name="task" value="quizzes" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="id" value="<?php echo $id;?>" />
		<input type="hidden" name="row_id" value="0" />
		</form>
	<?php
		JLMS_quiz_admin_html_class::showQuizFooter(); //closetwicely if opened using head2() function
	}

	//J1.6 ready
	function GetQuestEdit_Toolbar($id=0, $gqp = false, $is_apply_false = false) {
		$toolbar = array();
		if($gqp) {
			$toolbar[] = array('btn_type' => 'save', 'btn_txt' => _JLMS_QUIZ_SAVE_QUEST_BTN, 'btn_js' => "javascript:submitbutton('save_quest_gqp');");
			$toolbar[] = array('btn_type' => 'apply', 'btn_txt' => _JLMS_QUIZ_APPLY_BTN, 'btn_js' => "javascript:submitbutton('apply_quest_gqp');");
			if($id){
				$toolbar[] = array('btn_type' => 'preview', 'btn_txt' => _JLMS_QUIZ_PREVIEW_QUEST_BTN, 'btn_js' => "javascript:submitbutton('preview_quest');");
			}
			$toolbar[] = array('btn_type' => 'cancel', 'btn_txt' => _JLMS_QUIZ_CANCEL_BTN, 'btn_js' => "javascript:submitbutton('cancel_quest_gqp');");
		} else {
			$toolbar[] = array('btn_type' => 'save', 'btn_txt' => _JLMS_QUIZ_SAVE_QUEST_BTN, 'btn_js' => "javascript:submitbutton('save_quest');");
			if(!$is_apply_false) {
				$toolbar[] = array('btn_type' => 'apply', 'btn_txt' => _JLMS_QUIZ_APPLY_BTN, 'btn_js' => "javascript:submitbutton('apply_quest');");
			}
			if($id){
				$toolbar[] = array('btn_type' => 'preview', 'btn_txt' => _JLMS_QUIZ_PREVIEW_QUEST_BTN, 'btn_js' => "javascript:submitbutton('preview_quest');");
			}
			$toolbar[] = array('btn_type' => 'cancel', 'btn_txt' => _JLMS_QUIZ_CANCEL_BTN, 'btn_js' => "javascript:submitbutton('cancel_quest');");
		}
		return $toolbar;
	}

	//Joomla 1.6 ready
	function JQ_editQuest_MChoice( &$row, &$lists, $option, $page, $course_id, $q_om_type, &$qtype_str, &$params, $id, $gqp = false, $levels = false ) {
		global $Itemid, $JLMS_CONFIG;?>
		<script language="javascript" type="text/javascript">
		<!--
		var quest_type = <?php echo $q_om_type; ?>;

		function ReAnalize_tbl_Rows( start_index, tbl_id ) {
			start_index = 1;
			var tbl_elem = getObj(tbl_id);
			if (tbl_elem.rows[start_index]) {
				var count = start_index; var row_k = 2 - start_index%2;//0;
				for (var i=start_index; i<tbl_elem.rows.length; i++) {
					tbl_elem.rows[i].cells[0].innerHTML = count;
					Redeclare_element_inputs2(tbl_elem.rows[i].cells[1], i);
					//Redeclare_element_inputs(tbl_elem.rows[i].cells[2],i);
					if (i > 1) { 
						tbl_elem.rows[i].cells[4].innerHTML = '<a class="jlms_img_link" href="javascript: void(0);" onclick="javascript:Up_tbl_row(this); return false;" title="<?php echo _JLMS_MOVEUP;?>"><img src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_uparrow.png" width="16" height="16" border="0" alt="<?php echo _JLMS_MOVEUP;?>" /></a>';
					} else { tbl_elem.rows[i].cells[4].innerHTML = '&nbsp;'; }
					if (i < (tbl_elem.rows.length - 1)) {
						tbl_elem.rows[i].cells[5].innerHTML = '<a class="jlms_img_link" href="javascript: void(0);" onclick="javascript:Down_tbl_row(this); return false;" title="<?php echo _JLMS_MOVEDOWN;?>"><img src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_downarrow.png" width="16" height="16" border="0" alt="<?php echo _JLMS_MOVEDOWN;?>" /></a>';;
					} else { tbl_elem.rows[i].cells[5].innerHTML = '&nbsp;'; }
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
		
		function Redeclare_element_inputs(object) {
			if (object.hasChildNodes()) {
				var children = object.childNodes;
				var i = 0;
				while (i < children.length) {
					if (children[i].nodeName.toLowerCase() == 'input') {
						var inp_name = children[i].name;
						var inp_value = children[i].value;
						var inp_type = children[i].type;
						if (inp_type.toLowerCase() == 'text') {
							var inp_size = children[i].size;
						}
						if (inp_type.toLowerCase() == 'checkbox') {
							var inp_check = children[i].checked;
						}
						object.removeChild(object.childNodes[i]);/*i --;*/
						var input_hidden = document.createElement("input");
						input_hidden.type = inp_type;
						if (inp_type.toLowerCase() == 'text') {
							input_hidden.size = inp_size;
						}
						if (inp_type.toLowerCase() == 'checkbox') {
							input_hidden.checked = inp_check;
							input_hidden.onchange=input_hidden.onclick = new Function('jq_UnselectCheckbox(this)');
						}
						input_hidden.setAttribute('name',inp_name);
						input_hidden.value = inp_value;
						object.appendChild(input_hidden);
					}
					i ++;
				}
			}
		}
		
		function Redeclare_element_inputs2(object, gg) {
			if (object.hasChildNodes()) {
				var children = object.childNodes;
				for (var i = 0; i < children.length; i++) {
					if (children[i].nodeName.toLowerCase() == 'input') {
						var inp_type = children[i].type;
						if (inp_type.toLowerCase() == 'checkbox') {
							object.childNodes[i].value = gg;
						}
					}
				}
			}
		}
		function Redeclare_element_inputs3(object,object2) {
			if (object.hasChildNodes()) {
				var children = object.childNodes;
				for (var i = 0; i < children.length; i++) {
					if (children[i].nodeName.toLowerCase() == 'input') {
						var inp_name = children[i].name;
						//alert(inp_name);
						var inp_value = children[i].value;
						var inp_type = children[i].type;
						if (inp_type.toLowerCase() == 'checkbox') {
							var inp_check = children[i].checked;
						}
						var input_hidden = document.createElement("input");
						input_hidden.type = inp_type;
						input_hidden.setAttribute('name',inp_name);
						input_hidden.value = inp_value;
						if (inp_type.toLowerCase() == 'checkbox') {
						//alert('was: '+inp_check);
							if (inp_check) input_hidden.checked = true;
							input_hidden.onchange=input_hidden.onclick = new Function('jq_UnselectCheckbox(this)');
							//alert('now: '+input_hidden.checked);
						}
						object2.appendChild(children[i]);
						if (inp_type.toLowerCase() == 'checkbox') {
							if (inp_check) input_hidden.checked = true;
							//alert('updated: '+input_hidden.checked);
						}
					}
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
				element.parentNode.parentNode.parentNode.deleteRow(element.parentNode.parentNode.sectionRowIndex);
				// nel'zya prosto skopirovat' staryi innerHTML, t.k. ne sozdadutsya DOM elementy (for IE, Opera compatible).
				var cell5 = document.createElement("td");
				cell5.innerHTML = '<a class="jlms_img_link" href="javascript: void(0);" onclick="javascript:Up_tbl_row(this); return false;" title="<?php echo _JLMS_MOVEUP;?>"><img src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_uparrow.png" width="16" height="16" border="0" alt="<?php echo _JLMS_MOVEUP;?>" /></a>';
				
				row.appendChild(cell5);
				var cell6 = document.createElement("td");
				var cell7 = document.createElement("td");
				cell6.innerHTML = '&nbsp;';
				cell7.innerHTML = '&nbsp;';
				row.appendChild(cell6);
				row.appendChild(cell7);
				
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
				element.parentNode.parentNode.parentNode.deleteRow(element.parentNode.parentNode.sectionRowIndex);
				var cell5 = document.createElement("td");
				cell5.innerHTML = '<a class="jlms_img_link" href="javascript: void(0);" onclick="javascript:Up_tbl_row(this); return false;" title="<?php echo _JLMS_MOVEUP;?>"><img src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_uparrow.png" width="16" height="16" border="0" alt="<?php echo _JLMS_MOVEUP;?>" /></a>';
				row.appendChild(cell5);
				var cell6 = document.createElement("td");
				var cell7 = document.createElement("td");
				cell6.innerHTML = '&nbsp;';
				cell7.innerHTML = '&nbsp;';
				row.appendChild(cell6);
				row.appendChild(cell7);
				
				ReAnalize_tbl_Rows(sec_indx, tbl_id);
			}
		}

		function Add_new_tbl_field(elem_field, tbl_id, field_name) {
			<?php
			if($q_om_type == 12 || $q_om_type == 13){
			?>
			var new_element_txt = getObj(elem_field).options[getObj(elem_field).selectedIndex].text;
			var new_element_value = getObj(elem_field).value;
			<?php
			} else {
			?>
			var new_element_txt = getObj(elem_field).value;
			<?php
			}
			?>
			if (trim(new_element_txt) == '') {
				alert("Please enter text to the field.");return;
			}
			var tbl_elem = getObj(tbl_id);
			var row = tbl_elem.insertRow(tbl_elem.rows.length);
			var cell1 = document.createElement("td");
			var cell2 = document.createElement("td");
			var cell3 = document.createElement("td");
			var cell4 = document.createElement("td");
			var cell5 = document.createElement("td");
			var cell6 = document.createElement("td");
			var cell7 = document.createElement("td");
			var input_hidden = document.createElement("input");
			input_hidden.type = "hidden";
			input_hidden.name = field_name;
			<?php
			if($q_om_type == 12 || $q_om_type == 13){
			?>
			input_hidden.value = new_element_value;
			<?php
			} else {
			?>
			input_hidden.value = new_element_txt;
			<?php
			}
			?>
			var input_hidden_id = document.createElement("input");
			input_hidden_id.type = "hidden";
			input_hidden_id.name = "jq_hid_fields_ids[]";
			input_hidden_id.value = "0";
			var input_check = document.createElement("input");
			input_check.type = "checkbox";
			input_check.name = "jq_checked[]";
			input_check.setAttribute('name', "jq_checked[]");
//			input_check.value = "1";
			
			input_check.onchange=input_check.onclick = new Function('jq_UnselectCheckbox(this)');
			if (window.addEventListener) {
				input_check.addEventListener('change', jq_UnselectCheckbox2, false);
			} else {
				input_check.attachEvent('onchange', jq_UnselectCheckbox2 );
			}
			cell1.align = 'center';
			cell1.innerHTML = 0;
			cell2.appendChild(input_check);
			input_check.setAttribute('name', "jq_checked[]");
			cell3.innerHTML = new_element_txt;
			cell3.appendChild(input_hidden);
			cell3.appendChild(input_hidden_id);
			cell4.innerHTML = '<a class="jlms_img_link" href="javascript: void(0);" onclick="javascript:Delete_tbl_row(this); return false;" title="Delete"><img src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_cancel.png" width="16" height="16" border="0" alt="Delete" /></a>';
			cell5.innerHTML = '<a class="jlms_img_link" href="javascript: void(0);" onclick="javascript:Up_tbl_row(this); return false;" title="<?php echo _JLMS_MOVEUP;?>"><img src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_uparrow.png" width="16" height="16" border="0" alt="<?php echo _JLMS_MOVEUP;?>" /></a>';
			cell6.innerHTML = '&nbsp;';
			cell7.innerHTML = '&nbsp;';
			row.appendChild(cell1);
			row.appendChild(cell2);
			row.appendChild(cell3);
			row.appendChild(cell4);
			row.appendChild(cell5);
			row.appendChild(cell6);
			row.appendChild(cell7);
			input_check.setAttribute('name', "jq_checked[]");
			ReAnalize_tbl_Rows(tbl_elem.rows.length - 2, tbl_id);
			getObj(elem_field).value = '';
		}
		
		function jq_UnselectCheckbox(che) {
			<?php if ($q_om_type == 1 || $q_om_type == 12) { ?>
			f_name = che.form.name;
			ch_name = che.name;
			var a = che.checked;
			start_index = 1;
			var tbl_elem = getObj('qfld_tbl');
			
			var inputs = tbl_elem.getElementsByTagName('input');
			for(i=0;i<inputs.length;i++){
				if(inputs[i].type.toLowerCase() == 'checkbox'){
					inputs[i].checked = false;
				}
			}
			if (a){
				che.checked = true;
			}

		<?php } else { ?>
			return;
		<?php } ?>
		}
		function jq_UnselectCheckbox2(e) {
			if (!e) { e = window.event;}
			var cat2=e.target?e.target:e.srcElement;
			jq_UnselectCheckbox(cat2);
		}

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

			if ((pressbutton == 'cancel_quest') || (pressbutton == 'cancel_quest_gqp')) {
				form.page.value = pressbutton;
				form.submit();
			}
			if (pressbutton == 'preview_quest') {
				if (form.c_id.value == '0') {
					alert("<?php echo _JLMS_QUIZ_SAVE_QUEST_FIRST;?>");
				} else {
					window.open('index.php?option=<?php echo $option;?>&Itemid=<?php echo $Itemid;?>&task=quizzes&id=<?php echo $course_id;?>&page=view_preview&c_id=<?php echo $row->c_id;?>');
					return;
				}
			}
			// do field validation
			if (form.c_question.value == ""){
				alert( "<?php echo _JLMS_QUIZ_ENTER_QUEST_TEXT;?>" );
			} 
			else { 
				<?php if($gqp) {?>
				if( (form.level_id_0.value == 0) && (pressbutton != 'cancel_quest_gqp')) {
					alert( "<?php echo _JLMS_SELECT_CATEGORY;?>" );
				}
				else {
					form.page.value = pressbutton;
					form.submit();
				}
				<?php } else {?>
					form.page.value = pressbutton;
					form.submit();
				<?php }?>
			}	
		}
		function Check_mand(obj)
		{
			var mand = eval(document.adminForm['params[mandatory]']);
			if(obj)
			{
				mand[0].disabled = '';
				mand[1].disabled = '';
			}
			else
			{
				mand[0].disabled = 'disabled';
				mand[1].disabled = 'disabled';
			}
		}
		
		function view_imgs(e){
			var imgs_id = e.value;
			var imgs_name = e.options[e.selectedIndex].value;
			if(imgs_id != 0){
				$('imgs_left').style.display = 'block';
				$('left').src = 'index.php?tmpl=component&option=<?php echo $option;?>&task=quizzes&page=imgs_v&file_id='+imgs_id+'&id=<?php echo $course_id;?>&imgs_name='+imgs_name+'&max_width=<?php echo $JLMS_CONFIG->get('quiz_match_max_width', 250)?>&max_height=<?php echo $JLMS_CONFIG->get('quiz_match_max_height', 30)?>';
			} else {
				$('imgs_left').style.display = 'none';
				$('left').src = '';	
			}
		}
		//-->
		</script>
	<?php
	$toolbar = JLMS_quiz_admin_html_class::GetQuestEdit_Toolbar($id, $gqp);
	
	if($gqp) {
		$multicat = array();
		$i=0;
		
		foreach($lists as $key=>$item){
			if(substr($key, 0, 6) == 'level_'){
				if(isset($lists['level_'.$i])) {				
					$multicat[] = $lists['level_'.$i];
				}	
				$i++;
			}
		}
	}
	
		$h = $row->c_id?_JLMS_QUIZ_QUEST_EDIT_TITLE:_JLMS_QUIZ_QUEST_NEW_TITLE;
	
	JLMS_quiz_admin_html_class::showQuizHead( $course_id, $option, $h, true, $toolbar, '', $gqp );?>
		<form action="<?php echo sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid");?>" method="post" name="adminForm" onsubmit="setgood();">
	<?php
		JLMS_TMPL::OpenMT('jlms_table_no_borders');
		JLMS_TMPL::OpenTS();

		$tabs = new JLMSTabs(0);
		echo $tabs->startPane("JLMS");
		echo $tabs->startTab(_JLMS_QUIZ_E_PARAMS_TAB,"jlmstab1");?>

		<table width="100%" cellpadding="0" cellspacing="0" border="0" class="jlms_item_properties">
		<?php if($gqp) {?>
			<tr>
				<td colspan="2">
				<?php
					JLMS_quiz_admin_html_class::JQ_subCategory($multicat, $levels);				
				?>	
				</td>
			</tr>
		<?php }?>	
			<tr>
				<td align="left" valign="top" colspan="2"><br /><?php echo _JLMS_QUIZ_QUEST_QUEST_TXT;?></td>
			</tr>
			<tr>
				<td colspan="2">
				<?php JLMS_editorArea( 'editor1', $row->c_question, 'c_question', '100%;', '250', '75', '20' ) ; ?>
				</td>
			</tr>
			<tr>
				<td width="20%"><?php echo _JLMS_QUIZ_TBL_QUEST_PUBLISH;?>:</td>
				<td>
					<input type="hidden" name="published" value="<?php echo $row->published; ?>" />
					<input type="checkbox" name="published_chk" onClick="javascript: this.form['published'].value = (this.checked)?1:0;" <?php echo ($row->published == 1)?"checked":""; ?> />
			</tr>
			
		<?php if(!$gqp) {?>	
			<tr>
				<td width="20%"><br /><?php echo _JLMS_QUIZ_QUEST_QUIZ;?></td><td><?php echo $lists['quiz']; ?></td>
			</tr>
			<tr>
				<td valign="middle" align="left"><br /><?php echo _JLMS_QUIZ_ENTER_CAT;?></td>
				<td><br /><?php echo $lists['jq_categories']; ?></td>
			</tr>
		<?php }?>	
			<tr>
				<td><br /><?php echo _JLMS_QUIZ_QUEST_POINTS;?></td><td><br /><input class="inputbox" type="text" name="c_point" size="50" maxlength="5" value="<?php echo $row->c_point; ?>" /></td>
			</tr>
			<tr>
				<td><br /><?php echo _JLMS_QUIZ_QUEST_ATTEMPTS;?></td><td><br /><input class="inputbox" type="text" name="c_attempts" size="50" maxlength="5" value="<?php echo $row->c_attempts; ?>" /></td>
			</tr>
			<tr>
				<td><br /><?php echo _JLMS_QUIZ_QUEST_ORDERING;?></td><td><br /><?php echo $lists['ordering']; ?></td>
			</tr>
			<?php
			if($q_om_type == 1 || $q_om_type == 2){
			?>
			<tr>
				<td><br /><?php echo _JLMS_QUIZ_QUEST_RANDOM_ANSWERS;?></td><td><br /><?php echo $lists['random_answers']; ?></td>
			</tr>
			<?php
			}
			?>
			<tr>
				<td valign="middle" align="left"><br /><?php echo _JLMS_QUIZ_QUESTION_SETTING_SURVEY;?></td>
				<td><br />
					
					<input type="radio" name="params[survey_question]" value="1" <?php echo ($params->get('survey_question'))?"checked":"";?> onclick="Check_mand(this.checked);" /><?php echo _CMN_YES;?>
					<input type="radio" name="params[survey_question]" value="0" <?php echo (!$params->get('survey_question'))?"checked":"";?> onclick="Check_mand(!this.checked);" /><?php echo _CMN_NO;?>
				</td>
			</tr>
			<tr>
				<td valign="middle" align="left"><br /><?php echo _JLMS_QUIZ_QUESTION_SETTING_COMPULSORY;?></td>
				<td><br />
					
					<input type="radio" name="params[mandatory]" value="0" <?php echo (!$params->get('mandatory'))?"checked":"";?> <?php echo (!$params->get('survey_question'))?"disabled":"";?> /><?php echo _CMN_YES;?>
					<input type="radio" name="params[mandatory]" value="1" <?php echo ($params->get('mandatory'))?"checked":"";?> <?php echo (!$params->get('survey_question'))?"disabled":"";?> /><?php echo _CMN_NO;?>
				</td>
			</tr>

		</table>
		<br />
		<table width="100%" cellpadding="0" cellspacing="0" border="0" id="qfld_tbl" class="<?php echo JLMSCSS::_('jlmslist');?>">
			<tr>
				<<?php echo JLMSCSS::tableheadertag();?> width="20" class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center">#</<?php echo JLMSCSS::tableheadertag();?>>
				<<?php echo JLMSCSS::tableheadertag();?> width="30" class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center">-</<?php echo JLMSCSS::tableheadertag();?>>
				<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" width="200"><?php echo _JLMS_QUIZ_QUEST_OPTIONS;?></<?php echo JLMSCSS::tableheadertag();?>>
				<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" width="20" align="center">&nbsp;</<?php echo JLMSCSS::tableheadertag();?>>
				<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" width="20" align="center">&nbsp;</<?php echo JLMSCSS::tableheadertag();?>>
				<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" width="20" align="center">&nbsp;</<?php echo JLMSCSS::tableheadertag();?>>
				<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>">&nbsp;</<?php echo JLMSCSS::tableheadertag();?>>
			</tr>
			<?php
			$k = 1; $ii = 1; $ind_last = count($row->choices);
			foreach ($row->choices as $frow) { ?>
				<tr class="<?php echo JLMSCSS::_('sectiontableentry'.$k); ?>">
					<td align="center"><?php echo $ii;?></td>
					<td align="center"><input <?php echo ($frow->c_right?'checked':'');?> type="checkbox" name="jq_checked[]" value="<?php echo $ii;?>" onClick="jq_UnselectCheckbox2(event);" /></td>
					<td align="left">
						<?php echo ($q_om_type == 12 || $q_om_type == 13)?$frow->imgs_name:$frow->c_choice; $str = str_replace('"','&quot;',$frow->c_choice);// $str = str_replace('&quot;','\"',$str);?>
						<input type="hidden" name="jq_hid_fields[]" value="<?php echo $str;?>" />
						<input type="hidden" name="jq_hid_fields_ids[]" value="<?php echo $frow->c_id;?>" />
					</td>
					<td><a class="jlms_img_link" href="javascript: void(0);" onclick="javascript:Delete_tbl_row(this); return false;" title="Delete"><img src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_cancel.png" width="16" height="16" border="0" alt="Delete" /></a></td>
					<td><?php if ($ii > 1) { ?><a class="jlms_img_link" href="javascript: void(0);" onclick="javascript:Up_tbl_row(this); return false;" title="<?php echo _JLMS_MOVEUP;?>"><img src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_uparrow.png" width="16" height="16" border="0" alt="<?php echo _JLMS_MOVEUP;?>" /></a><?php } else { echo '&nbsp;'; } ?></td>
					<td><?php if ($ii < $ind_last) { ?><a class="jlms_img_link" href="javascript: void(0);" onclick="javascript:Down_tbl_row(this); return false;" title="<?php echo _JLMS_MOVEDOWN;?>"><img src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_downarrow.png" width="16" height="16" border="0" alt="<?php echo _JLMS_MOVEDOWN;?>" /></a><?php } else { echo '&nbsp;'; } ?></td>
					<td>&nbsp;</td>
				</tr>
			<?php
			$k = 3 - $k; $ii ++;
			 } ?>
			</table>
		<table cellpadding="0" cellspacing="0" border="0" width="100%" style="margin-top:2px; margin-bottom:2px;" class="jlms_table_no_borders">
			<tr><td><small>
			<?php echo _JLMS_QUIZ_QUEST_NOTE_CHOICES;?>
			</small></td></tr>
		</table>
		<br />
		<?php
		if($q_om_type == 12 || $q_om_type == 13){
		?>
		<div style="text-align:left; padding-left:30px; ">
		<select id="new_field" class="inputbox" style="width:205px;" name="new_field" onchange="javascript:view_imgs(this);">
			<option value="0"> - Select Name Image - </option>
			<?php
			foreach($row->images as $data){
			?>
				<option value="<?php echo $data->imgs_id;?>"><?php echo $data->imgs_name;?></option>
			<?php
			}
			?>
		</select>
		<input class="inputbox" type="button" name="add_new_field" value="<?php echo _JLMS_QUIZ_QUIZ_ADDOPTION_BTN;?>" onclick="javascript:Add_new_tbl_field('new_field', 'qfld_tbl', 'jq_hid_fields[]');" />
			<div id="imgs_left" style="width:205px; text-align:center; overflow:hidden; display:none; float: left;">
				<img id="left" src="" title="Example" border="0" align="center"/>&nbsp;
			</div>
		</div>
		<?php
		} else {
		?>
		<div style="text-align:left;">
			<input id="new_field" class="inputbox" style="width:205px " type="text" name="new_field" />
			<input class="inputbox" type="button" name="add_new_field" value="<?php echo _JLMS_QUIZ_QUIZ_ADDOPTION_BTN;?>" onclick="javascript:Add_new_tbl_field('new_field', 'qfld_tbl', 'jq_hid_fields[]');" />
		</div>
		<?php
		}
		?>
		<?php echo $tabs->endTab();
			echo $tabs->startTab(_JLMS_QUIZ_E_FEEDBACKS_TAB,"jlmstab2"); ?>
		<table width="100%" cellpadding="0" cellspacing="0" border="0" class="jlms_item_properties">
			<tr>
				<td><br /><?php echo _JLMS_QUIZ_DIS_QUEST_FEEDBACK;?></td><td><br />
					<input type="checkbox" name="params[disable_quest_feedback]" value="1" <?php echo ($params->get('disable_quest_feedback') == 1)?"checked":""; ?> />
				</td>
			</tr>
			<tr>
				<td valign="middle" align="left"><br /><?php echo _JLMS_QUIZ_MES_ANSWER_CORRECT;?></td>
				<td><br /><textarea class="inputbox" name="c_right_message" cols="50" rows="5"><?php echo $lists['c_right_message']; ?></textarea></td>
			</tr>
			<tr>
				<td valign="middle" align="left"><br /><?php echo _JLMS_QUIZ_MES_ANSWER_INCORRECT;?></td>
				<td><br /><textarea class="inputbox" name="c_wrong_message" cols="50" rows="5"><?php echo $lists['c_wrong_message']; ?></textarea></td>
			</tr>
		</table>
		<?php echo $tabs->endTab();
		echo $tabs->startTab(_JLMS_QUIZ_QUESTION_REVIEW_SETTINGS,"jlmstab3"); ?>
		<table width="100%" cellpadding="0" cellspacing="0" border="0" class="jlms_item_properties">
			<tr>
				<td valign="middle" align="left"><br /><?php echo _JLMS_QUIZ_QUESTION_SETTING_EXPLANATION;?></td>
			
				<td>
				<br /><textarea class="inputbox" name="c_explanation" cols="50" rows="5"><?php echo $row->c_explanation;?></textarea>
				</td>
			</tr>
		</table>
		<?php 
		echo $tabs->endTab();
		echo $tabs->endPane();
		JLMS_TMPL::CloseTS();
		JLMS_TMPL::CloseMT(); ?>
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
		<input type="hidden" name="task" value="quizzes" />
		<input type="hidden" name="c_type" value="<?php echo $q_om_type;?>" />
		<input type="hidden" name="id" value="<?php echo $course_id;?>" />
		<input type="hidden" name="page" value="save_quiz" />
		<input type="hidden" name="c_id" value="<?php echo $row->c_id;?>" />
		<input type="hidden" name="gqp" value="<?php echo $gqp;?>" />
		</form>
		<?php JLMS_quiz_admin_html_class::showQuizFooter(); ?>
		<?php
	}

	//Joomla 1.6 optimized
	function JQ_subCategory($multicat, $levels, $adm_form_name = 'adminForm') {
		
		?>
		<script language="javascript" type="text/javascript">
		<!--
		var form=document.adminForm;
			var old_filters = new Array();
			function read_filter(){
				var form = document.<?php echo $adm_form_name;?>;
				var count_levels = '<?php echo count($levels);?>';
				for(var i=0;i<parseInt(count_levels);i++){
					if(form['level_id_'+i] != null){
						old_filters[i] = form['level_id_'+i].value;
					}
				}
			}
			function write_filter(){
				var form = document.<?php echo $adm_form_name;?>;
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
		//-->
		</script>
		
		<table border="0" width="100%" cellpadding="0" cellspacing="0" class="jlms_table_no_borders">
			<tr>
				<td id="multicat_title" width="20%" valign="top" style="vertical-align: top;">
					<table cellpadding="0" cellspacing="0" border="0" width="100%" class="jlms_table_no_borders">
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
					<table cellpadding="0" cellspacing="0" border="0" width="100%" class="jlms_table_no_borders">
					<?php	
						for($i=0;$i<count($multicat);$i++){
							?>
							<tr>
								<td>
								<?php
									$m_output = $multicat[$i];
									if ($adm_form_name == 'adminForm') {
										//ok
									} else {
										$m_output = str_replace('adminForm', 'adminFormQ', $m_output);
									}
									$m_output = str_replace('<option value="0" selected="selected"></option>', '<option value="0" selected="selected">&nbsp;</option>', $m_output);
									$m_output = str_replace('<option value="0"></option>', '<option value="0">&nbsp;</option>', $m_output);
									echo $m_output;
								?>
								<input type="hidden" name="multicat_id" value="" />
								</td>
							</tr>
							<?php
						}
						?>
					</table>
				</td>
			</tr>
		</table>
		<?php
	}
	
	//Joomla 1.6 ready
	function JQ_editQuest_TrueFalse( &$row, &$lists, $option, $page, $course_id, $q_om_type, &$qtype_str, &$params, $id, $gqp= false, $levels = false ) {
		global $Itemid;?>
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

	if (pressbutton == 'cancel_quest') {
		form.page.value = pressbutton;
		form.submit();
	}
	if (pressbutton == 'preview_quest') {
		if (form.c_id.value == '0') {
			alert("<?php echo _JLMS_QUIZ_SAVE_QUEST_FIRST;?>");
		} else {
			window.open('index.php?option=<?php echo $option;?>&Itemid=<?php echo $Itemid;?>&task=quizzes&id=<?php echo $course_id;?>&page=view_preview&c_id=<?php echo $row->c_id;?>');
			return;
		}
	}
	// do field validation
	if (form.c_question.value == ""){
		alert( "<?php echo _JLMS_QUIZ_ENTER_QUEST_TEXT;?>" );
	} 
	else { 
		<?php if($gqp) {?>
		if( (form.level_id_0.value == 0) && (pressbutton != 'cancel_quest_gqp')) {
			alert( "<?php echo _JLMS_SELECT_CATEGORY;?>" );
		}
		else {
			form.page.value = pressbutton;
			form.submit();
		}
		<?php } else {?>
			form.page.value = pressbutton;
			form.submit();
		<?php }?>
	}	
}
//-->
</script>
	<?php
	$toolbar = JLMS_quiz_admin_html_class::GetQuestEdit_Toolbar($id, $gqp);
	
	if($gqp) {
		$multicat = array();
		$i=0;
		
		foreach($lists as $key=>$item){
			if(substr($key, 0, 6) == 'level_'){
				if(isset($lists['level_'.$i])) {				
					$multicat[] = $lists['level_'.$i];
				}	
				$i++;
			}
		}
	}
 
		$h = $row->c_id?_JLMS_QUIZ_QUEST_EDIT_TITLE:_JLMS_QUIZ_QUEST_NEW_TITLE;
	
	JLMS_quiz_admin_html_class::showQuizHead( $course_id, $option, $h, true, $toolbar,'',$gqp );?>
		<form action="<?php echo sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid");?>" method="post" name="adminForm" onsubmit="setgood();">
	<?php
		JLMS_TMPL::OpenMT('jlms_table_no_borders');
		JLMS_TMPL::OpenTS();

		$tabs = new JLMSTabs(0);
		echo $tabs->startPane("JLMS");
		echo $tabs->startTab(_JLMS_QUIZ_E_PARAMS_TAB,"jlmstab1");?>

		<table width="100%" cellpadding="0" cellspacing="0" border="0" class="jlms_item_properties">
		<?php if($gqp) {?>
			<tr>
				<td colspan="2">
				<?php
					JLMS_quiz_admin_html_class::JQ_subCategory($multicat, $levels);				
				?>	
				</td>
			</tr>
		<?php }?>
			<tr>
				<td align="left" valign="top" colspan="2"><br /><?php echo _JLMS_QUIZ_QUEST_QUEST_TXT;?></td>
			</tr>
			<tr>
				<td colspan="2">
				<?php JLMS_editorArea( 'editor1', $row->c_question, 'c_question', '100%;', '250', '75', '20' ) ; ?>
				</td>
			</tr>
			<tr>
				<td width="20%"><?php echo _JLMS_QUIZ_TBL_QUEST_PUBLISH;?>:</td>
				<td>
					<input type="hidden" name="published" value="<?php echo $row->published; ?>" />
					<input type="checkbox" name="published_chk" onClick="javascript: this.form['published'].value = (this.checked)?1:0;" <?php echo ($row->published == 1)?"checked":""; ?> />
			</tr>
		<?php if(!$gqp) {?>	
			<tr>
				<td width="20%"><br /><?php echo _JLMS_QUIZ_QUEST_QUIZ;?></td><td><?php echo $lists['quiz']; ?></td>
			</tr>
			<tr>
				<td valign="middle" align="left"><br /><?php echo _JLMS_QUIZ_ENTER_CAT;?></td>
				<td><br /><?php echo $lists['jq_categories']; ?></td>
			</tr>
		<?php }?>	
			<tr>
				<td><br /><?php echo _JLMS_QUIZ_QUEST_POINTS;?></td><td><br /><input class="inputbox" type="text" name="c_point" size="50" maxlength="5" value="<?php echo $row->c_point; ?>" /></td>
			</tr>
			<tr>
				<td><br /><?php echo _JLMS_QUIZ_QUEST_ATTEMPTS;?></td><td><br /><input class="inputbox" type="text" name="c_attempts" size="50" maxlength="5" value="<?php echo $row->c_attempts; ?>" /></td>
			</tr>
			<tr>
				<td><br /><?php echo _JLMS_QUIZ_QUEST_ORDERING;?></td><td><br /><?php echo $lists['ordering']; ?></td>
			</tr>
			<tr>
				<td><br /><?php echo _JLMS_QUIZ_QUEST_CHOICE?></td><td><br />
				<input type="radio" name="znach" value="1" <?php echo ($row->choice_true)?"checked":"";?> /><?php echo _JLMS_QUIZ_WORD_TRUE;?>
				<input type="radio" name="znach" value="0" <?php echo (!$row->choice_true)?"checked":"";?> /><?php echo _JLMS_QUIZ_WORD_FALSE;?>
				</td>
			</tr>
		</table>
		<?php echo $tabs->endTab();
			echo $tabs->startTab(_JLMS_QUIZ_E_FEEDBACKS_TAB,"jlmstab2"); ?>
		<table width="100%" cellpadding="0" cellspacing="0" border="0" class="jlms_item_properties">
			<tr>
				<td><br /><?php echo _JLMS_QUIZ_DIS_QUEST_FEEDBACK;?></td><td><br />
					<input type="checkbox" name="params[disable_quest_feedback]" value="1" <?php echo ($params->get('disable_quest_feedback') == 1)?"checked":""; ?> />
				</td>
			</tr>
			<tr>
				<td valign="middle" align="left"><br /><?php echo _JLMS_QUIZ_MES_ANSWER_CORRECT;?></td>
				<td><br /><textarea class="inputbox" name="c_right_message" cols="50" rows="5"><?php echo $lists['c_right_message']; ?></textarea></td>
			</tr>
			<tr>
				<td valign="middle" align="left"><br /><?php echo _JLMS_QUIZ_MES_ANSWER_INCORRECT;?></td>
				<td><br /><textarea class="inputbox" name="c_wrong_message" cols="50" rows="5"><?php echo $lists['c_wrong_message']; ?></textarea></td>
			</tr>
		</table>
		<?php echo $tabs->endTab();
		echo $tabs->startTab(_JLMS_QUIZ_QUESTION_REVIEW_SETTINGS,"jlmstab3"); ?>
		<table width="100%" cellpadding="0" cellspacing="0" border="0" class="jlms_item_properties">
			<tr>
				<td valign="middle" align="left"><br /><?php echo _JLMS_QUIZ_QUESTION_SETTING_EXPLANATION;?></td>
			
				<td>
				<br /><textarea class="inputbox" name="c_explanation" cols="50" rows="5"><?php echo $row->c_explanation;?></textarea>
				</td>
			</tr>
		</table>
		<?php echo $tabs->endTab();
		echo $tabs->endPane();
		JLMS_TMPL::CloseTS();
		JLMS_TMPL::CloseMT(); ?>

		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
		<input type="hidden" name="task" value="quizzes" />
		<input type="hidden" name="c_type" value="<?php echo $q_om_type;?>" />
		<input type="hidden" name="id" value="<?php echo $course_id;?>" />
		<input type="hidden" name="page" value="save_quiz" />
		<input type="hidden" name="c_id" value="<?php echo $row->c_id;?>" />
		</form>
		<?php JLMS_quiz_admin_html_class::showQuizFooter(); ?>
		<?php
	}
	
/* 		function for Drag'N'Drop, DropDown questions		*/
	function JQ_editQuest_MDragDrop( &$row, &$lists, $option, $page, $course_id, $q_om_type, &$qtype_str, &$params, $id, $gqp= false, $levels = false ) {
		global $Itemid, $JLMS_CONFIG; ?>
		<script language="javascript" type="text/javascript">
		<!--
		var quest_type = <?php echo $q_om_type; ?>;

		function ReAnalize_tbl_Rows( start_index, tbl_id ) {
			start_index = 1;
			var tbl_elem = getObj(tbl_id);
			if (tbl_elem.rows[start_index]) {
				var count = start_index; var row_k = 2 - start_index%2;//0;
				for (var i=start_index; i<tbl_elem.rows.length; i++) {
					tbl_elem.rows[i].cells[0].innerHTML = count;
					if (i > 1) { 
						tbl_elem.rows[i].cells[4].innerHTML = '<a class="jlms_img_link"href="javascript: void(0);" onclick="javascript:Up_tbl_row(this); return false;" title="<?php echo _JLMS_MOVEUP;?>"><img src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_uparrow.png" width="16" height="16" border="0" alt="<?php echo _JLMS_MOVEUP;?>" /></a>';
					} else { tbl_elem.rows[i].cells[4].innerHTML = '&nbsp;'; }
					if (i < (tbl_elem.rows.length - 1)) {
						tbl_elem.rows[i].cells[5].innerHTML = '<a class="jlms_img_link" href="javascript: void(0);" onclick="javascript:Down_tbl_row(this); return false;" title="<?php echo _JLMS_MOVEDOWN;?>"><img src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_downarrow.png" width="16" height="16" border="0" alt="<?php echo _JLMS_MOVEDOWN;?>" /></a>';;
					} else { tbl_elem.rows[i].cells[5].innerHTML = '&nbsp;'; }
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
		
		function Redeclare_element_inputs(object) {
			if (object.hasChildNodes()) {
				var children = object.childNodes;
				for (var i = 0; i < children.length; i++) {
					if (children[i].nodeName.toLowerCase() == 'input') {
						var inp_name = children[i].name;
						var inp_value = children[i].value;
						object.removeChild(object.childNodes[i]);
						var input_hidden = document.createElement("input");
						input_hidden.type = "hidden";
						input_hidden.setAttribute('name',inp_name);
						input_hidden.value = inp_value;
						object.appendChild(input_hidden);
					}
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

				// nel'zya prosto skopirovat' staryi innerHTML, t.k. ne sozdadutsya DOM elementy (for IE, Opera compatible).
				var cell1 = document.createElement("td");
				cell1.align = 'center';
				var row = table.insertRow(sec_indx - 1);
				row.appendChild(cell1);
				row.appendChild(element.parentNode.parentNode.cells[1]);
				row.appendChild(element.parentNode.parentNode.cells[1]);
				row.appendChild(element.parentNode.parentNode.cells[1]);
				element.parentNode.parentNode.parentNode.deleteRow(element.parentNode.parentNode.sectionRowIndex);
				var cell5 = document.createElement("td");

				cell5.innerHTML = '<a class="jlms_img_link" href="javascript: void(0);" onclick="javascript:Up_tbl_row(this); return false;" title="<?php echo _JLMS_MOVEUP;?>"><img src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_uparrow.png" width="16" height="16" border="0" alt="<?php echo _JLMS_MOVEUP;?>" /></a>';

				row.appendChild(cell5);
				var cell6 = document.createElement("td");
				var cell7 = document.createElement("td");
				cell6.innerHTML = '&nbsp;';
				cell7.innerHTML = '&nbsp;';
				row.appendChild(cell6);
				row.appendChild(cell7);
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
				element.parentNode.parentNode.parentNode.deleteRow(element.parentNode.parentNode.sectionRowIndex);

				var cell5 = document.createElement("td");
				cell5.innerHTML = '<a class="jlms_img_link" href="javascript: void(0);" onclick="javascript:Up_tbl_row(this); return false;" title="<?php echo _JLMS_MOVEUP;?>"><img src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_uparrow.png" width="16" height="16" border="0" alt="<?php echo _JLMS_MOVEUP;?>" /></a>';

				row.appendChild(cell5);
				var cell6 = document.createElement("td");
				var cell7 = document.createElement("td");
				cell6.innerHTML = '&nbsp;';
				cell7.innerHTML = '&nbsp;';
				row.appendChild(cell6);
				row.appendChild(cell7);

				ReAnalize_tbl_Rows(sec_indx, tbl_id);
			}
		}

		function Add_new_tbl_field(elem_field, elem_field2, tbl_id, field_name, field_name2) {
			var new_element_txt = getObj(elem_field).value;
			var new_element_txt2 = getObj(elem_field2).value;
			if (trim(new_element_txt) == '') {
				alert("Please enter text to the field.");return;
			}
			if (trim(new_element_txt2) == '') {
				alert("Please enter text to the field.");return;
			}
			var tbl_elem = getObj(tbl_id);
			var row = tbl_elem.insertRow(tbl_elem.rows.length);
			var cell1 = document.createElement("td");
			var cell2 = document.createElement("td");
			var cell3 = document.createElement("td");
			var cell4 = document.createElement("td");
			var cell5 = document.createElement("td");
			var cell6 = document.createElement("td");
			var cell7 = document.createElement("td");
			var input_hidden = document.createElement("input");
			input_hidden.type = "hidden";
			input_hidden.name = field_name;
			input_hidden.value = new_element_txt;
			var input_hidden2 = document.createElement("input");
			input_hidden2.type = "hidden";
			input_hidden2.name = field_name2;
			input_hidden2.value = new_element_txt2;
			var input_hidden_id = document.createElement("input");
			input_hidden_id.type = "hidden";
			input_hidden_id.name = "jq_hid_fields_ids[]";
			input_hidden_id.value = "0";
			cell1.align = 'center';
			cell1.innerHTML = 0;
			cell3.innerHTML = new_element_txt2;
			cell3.appendChild(input_hidden2);
			cell2.innerHTML = new_element_txt;
			cell2.appendChild(input_hidden);
			cell2.appendChild(input_hidden_id);
			cell4.innerHTML = '<a class="jlms_img_link" href="javascript: void(0);" onclick="javascript:Delete_tbl_row(this); return false;" title="Delete"><img src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_cancel.png" width="16" height="16" border="0" alt="Delete" /></a>';
			cell5.innerHTML = '<a class="jlms_img_link" href="javascript: void(0);" onclick="javascript:Up_tbl_row(this); return false;" title="<?php echo _JLMS_MOVEUP;?>"><img src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_uparrow.png" width="16" height="16" border="0" alt="<?php echo _JLMS_MOVEUP;?>" /></a>';
			cell6.innerHTML = '&nbsp;';
			cell7.innerHTML = '&nbsp;';
			row.appendChild(cell1);
			row.appendChild(cell2);
			row.appendChild(cell3);
			row.appendChild(cell4);
			row.appendChild(cell5);
			row.appendChild(cell6);row.appendChild(cell7);
			ReAnalize_tbl_Rows(tbl_elem.rows.length - 2, tbl_id);
			getObj(elem_field).value = '';
			getObj(elem_field2).value = '';
		}

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

			if (pressbutton == 'cancel_quest') {
				form.page.value = pressbutton;
				form.submit();
			}
			if (pressbutton == 'preview_quest') {
				if (form.c_id.value == '0') {
					alert("<?php echo _JLMS_QUIZ_SAVE_QUEST_FIRST;?>");
				} else {
					window.open('index.php?option=<?php echo $option;?>&Itemid=<?php echo $Itemid;?>&task=quizzes&id=<?php echo $course_id;?>&page=view_preview&c_id=<?php echo $row->c_id;?>');
					return;
				}
			}
			// do field validation
			if (form.c_question.value == ""){
				alert( "<?php echo _JLMS_QUIZ_ENTER_QUEST_TEXT;?>" );
			} 
			else { 
				<?php if($gqp) {?>
				if( (form.level_id_0.value == 0) && (pressbutton != 'cancel_quest_gqp')) {
					alert( "<?php echo _JLMS_SELECT_CATEGORY;?>" );
				}
				else {
					form.page.value = pressbutton;
					form.submit();
				}
				<?php } else {?>
					form.page.value = pressbutton;
					form.submit();
				<?php }?>
			}	
		}
		//-->
		</script>
	<?php
	$toolbar = JLMS_quiz_admin_html_class::GetQuestEdit_Toolbar($id, $gqp);

	if($gqp) {
		$multicat = array();
		$i=0;
		
		foreach($lists as $key=>$item){
			if(substr($key, 0, 6) == 'level_'){
				if(isset($lists['level_'.$i])) {				
					$multicat[] = $lists['level_'.$i];
				}	
				$i++;
			}
		}
	}
 
		$h = $row->c_id?_JLMS_QUIZ_QUEST_EDIT_TITLE:_JLMS_QUIZ_QUEST_NEW_TITLE;

	JLMS_quiz_admin_html_class::showQuizHead( $course_id, $option, $h, true, $toolbar,'',$gqp );?>
		<form action="<?php echo sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid");?>" method="post" name="adminForm" onsubmit="setgood();">
	<?php
		JLMS_TMPL::OpenMT('jlms_table_no_borders');
		JLMS_TMPL::OpenTS();

		$tabs = new JLMSTabs(0);
		echo $tabs->startPane("JLMS");
		echo $tabs->startTab(_JLMS_QUIZ_E_PARAMS_TAB,"jlmstab1");?>

		<table width="100%" cellpadding="0" cellspacing="0" border="0" class="jlms_item_properties">
		<?php if($gqp) {?>
			<tr>
				<td colspan="2">
				<?php
					JLMS_quiz_admin_html_class::JQ_subCategory($multicat, $levels);				
				?>	
			</td>
			</tr>
		<?php }?>
			<tr>
				<td align="left" valign="top" colspan="2"><br /><?php echo _JLMS_QUIZ_QUEST_QUEST_TXT;?></td>
			</tr>
			<tr>
				<td colspan="2">
				<?php JLMS_editorArea( 'editor1', $row->c_question, 'c_question', '100%;', '250', '75', '20' ) ; ?>
				</td>
			</tr>
			<tr>
				<td width="20%"><?php echo _JLMS_QUIZ_TBL_QUEST_PUBLISH;?>:</td>
				<td>
					<input type="hidden" name="published" value="<?php echo $row->published; ?>" />
					<input type="checkbox" name="published_chk" onClick="javascript: this.form['published'].value = (this.checked)?1:0;" <?php echo ($row->published == 1)?"checked":""; ?> />
			</tr>
		<?php if(!$gqp) {?>	
			<tr>
				<td width="20%"><br /><?php echo _JLMS_QUIZ_QUEST_QUIZ;?></td><td><?php echo $lists['quiz']; ?></td>
			</tr>
			<tr>
				<td valign="middle" align="left"><br /><?php echo _JLMS_QUIZ_ENTER_CAT;?></td>
				<td><br /><?php echo $lists['jq_categories']; ?></td>
			</tr>
		<?php }?>	
			<tr>
				<td><br /><?php echo _JLMS_QUIZ_QUEST_POINTS;?></td><td><br /><input class="inputbox" type="text" name="c_point" size="50" maxlength="5" value="<?php echo $row->c_point; ?>" /></td>
			</tr>
			<tr>
				<td><br /><?php echo _JLMS_QUIZ_QUEST_ATTEMPTS;?></td><td><br /><input class="inputbox" type="text" name="c_attempts" size="50" maxlength="5" value="<?php echo $row->c_attempts; ?>" /></td>
			</tr>
			<tr>
				<td><br /><?php echo _JLMS_QUIZ_QUEST_ORDERING;?></td><td><br /><?php echo $lists['ordering']; ?></td>
			</tr>
		</table>
		<br />
		<table width="100%" cellpadding="0" cellspacing="0" border="0" id="qfld_tbl" class="<?php echo JLMSCSS::_('jlmslist');?>">
			<tr>
				<<?php echo JLMSCSS::tableheadertag();?> width="20" class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center">#</<?php echo JLMSCSS::tableheadertag();?>>
				<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" width="200"><?php echo _JLMS_QUIZ_QUEST_OPTIONS;?></<?php echo JLMSCSS::tableheadertag();?>>
				<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" width="200">&nbsp;</<?php echo JLMSCSS::tableheadertag();?>>
				<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" width="20" align="center">&nbsp;</<?php echo JLMSCSS::tableheadertag();?>>
				<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" width="20" align="center">&nbsp;</<?php echo JLMSCSS::tableheadertag();?>>
				<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" width="20" align="center">&nbsp;</<?php echo JLMSCSS::tableheadertag();?>>
				<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>">&nbsp;</<?php echo JLMSCSS::tableheadertag();?>>
			</tr>
			<?php
			$k = 1; $ii = 1; $ind_last = count($row->matching);
			foreach ($row->matching as $frow) { ?>
				<tr class="<?php echo JLMSCSS::_('sectiontableentry'.$k); ?>">
					<td align="center"><?php echo $ii;?></td>
					<td align="left">
						<?php echo stripslashes($frow->c_left_text); $strl = str_replace('"','&quot;',$frow->c_left_text);?>
						<input type="hidden" name="jq_hid_fields_left[]" value="<?php echo $strl;?>" />
						<input type="hidden" name="jq_hid_fields_ids[]" value="<?php echo $frow->c_id;?>" />
					</td>
					<td align="left">
						<?php echo stripslashes($frow->c_right_text); $strr = str_replace('"','&quot;',$frow->c_right_text);?>
						<input type="hidden" name="jq_hid_fields_right[]" value="<?php echo $strr;?>" />
					</td>
					<td><a class="jlms_img_link" href="javascript: void(0);" onclick="javascript:Delete_tbl_row(this); return false;" title="Delete"><img src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_cancel.png" width="16" height="16" border="0" alt="Delete" /></a></td>
					<td><?php if ($ii > 1) { ?><a class="jlms_img_link" href="javascript: void(0);" onclick="javascript:Up_tbl_row(this); return false;" title="<?php echo _JLMS_MOVEUP;?>"><img src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_uparrow.png" width="16" height="16" border="0" alt="<?php echo _JLMS_MOVEUP;?>" /></a><?php } else { echo '&nbsp;';} ?></td>
					<td><?php if ($ii < $ind_last) { ?><a class="jlms_img_link" href="javascript: void(0);" onclick="javascript:Down_tbl_row(this); return false;" title="<?php echo _JLMS_MOVEDOWN;?>"><img src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_downarrow.png" width="16" height="16" border="0" alt="<?php echo _JLMS_MOVEDOWN;?>" /></a><?php } else { echo '&nbsp;';} ?></td>
					<td>&nbsp;</td>
				</tr>
			<?php
			$k = 3 - $k; $ii ++;
			 } ?>
			</table>
		<br />
		<div style="text-align:left; padding-left:30px ">
			<input id="new_field_left" class="inputbox" style="width:205px " type="text" name="new_field_left" />
			<input id="new_field_right" class="inputbox" style="width:205px " type="text" name="new_field_right" />
			<input class="inputbox" type="button" name="add_new_field" value="<?php echo _JLMS_QUIZ_QUIZ_ADD_BTN;?>" onclick="javascript:Add_new_tbl_field('new_field_left', 'new_field_right', 'qfld_tbl', 'jq_hid_fields_left[]', 'jq_hid_fields_right[]');" />
		</div>
		<?php echo $tabs->endTab();
			echo $tabs->startTab(_JLMS_QUIZ_E_FEEDBACKS_TAB,"jlmstab2"); ?>
		<table width="100%" cellpadding="0" cellspacing="0" border="0" class="jlms_item_properties">
			<tr>
				<td><br /><?php echo _JLMS_QUIZ_DIS_QUEST_FEEDBACK;?></td><td><br />
					<input type="checkbox" name="params[disable_quest_feedback]" value="1" <?php echo ($params->get('disable_quest_feedback') == 1)?"checked":""; ?> />
				</td>
			</tr>
			<tr>
				<td valign="middle" align="left"><br /><?php echo _JLMS_QUIZ_MES_ANSWER_CORRECT;?></td>
				<td><br /><textarea class="inputbox" name="c_right_message" cols="50" rows="5"><?php echo $lists['c_right_message']; ?></textarea></td>
			</tr>
			<tr>
				<td valign="middle" align="left"><br /><?php echo _JLMS_QUIZ_MES_ANSWER_INCORRECT;?></td>
				<td><br /><textarea class="inputbox" name="c_wrong_message" cols="50" rows="5"><?php echo $lists['c_wrong_message']; ?></textarea></td>
			</tr>
		</table>
		<?php echo $tabs->endTab();
		echo $tabs->startTab(_JLMS_QUIZ_QUESTION_REVIEW_SETTINGS,"jlmstab3"); ?>
		<table width="100%" cellpadding="0" cellspacing="0" border="0" class="jlms_item_properties">
			<tr>
				<td valign="middle" align="left"><br /><?php echo _JLMS_QUIZ_QUESTION_SETTING_EXPLANATION;?></td>
			
				<td>
				<br /><textarea class="inputbox" name="c_explanation" cols="50" rows="5"><?php echo $row->c_explanation;?></textarea>
				</td>
			</tr>
		</table>
		<?php echo $tabs->endTab();
		echo $tabs->endPane();
		JLMS_TMPL::CloseTS();
		JLMS_TMPL::CloseMT(); ?>

		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
		<input type="hidden" name="task" value="quizzes" />
		<input type="hidden" name="c_type" value="<?php echo $q_om_type;?>" />
		<input type="hidden" name="id" value="<?php echo $course_id;?>" />
		<input type="hidden" name="page" value="save_quiz" />
		<input type="hidden" name="c_id" value="<?php echo $row->c_id;?>" />
		</form>
		<?php JLMS_quiz_admin_html_class::showQuizFooter(); ?>
		<?php
	}
	
	function JQ_editQuest_MDragDrop2( &$row, &$lists, $option, $page, $course_id, $q_om_type, &$qtype_str, &$params, $id, $gqp= false, $levels = false ) {
		global $Itemid, $JLMS_CONFIG; ?>
		<script language="javascript" type="text/javascript">
		<!--
		var quest_type = <?php echo $q_om_type; ?>;
		
		/*var images_value = new Array();
		var images_text = new Array();
		<?php
		$i = 0;$j = 0;
		$last_i = -1;
		while ($i < count($row->images)) {
			if ($last_i != $row->images[$i]->i_id) {
				echo "images_value[".$j."] = '".$row->images[$i]->imgs_id."'; \n";
				$j++;
			}
			$last_i = $row->images[$i]->i_id;
			$i++;
		}
		?>
		<?php
		$i = 0;$j = 0;
		$last_i = -1;
		while ($i < count($row->images)) {
			if ($last_i != $row->images[$i]->i_id) {
				echo "images_text[".$j."] = '".$row->images[$i]->imgs_name."'; \n";
				$j++;
			}
			$last_i = $row->images[$i]->i_id;
			$i++;
		}
		?>
		*/

		function ReAnalize_tbl_Rows( start_index, tbl_id ) {
			start_index = 1;
			var tbl_elem = getObj(tbl_id);
			if (tbl_elem.rows[start_index]) {
				var count = start_index; var row_k = 2 - start_index%2;//0;
				for (var i=start_index; i<tbl_elem.rows.length; i++) {
					tbl_elem.rows[i].cells[0].innerHTML = count;

					if (i > 1) { 
						tbl_elem.rows[i].cells[4].innerHTML = '<a href="javascript: void(0);" onclick="javascript:Up_tbl_row(this); return false;" title="<?php echo _JLMS_MOVEUP;?>"><img src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_uparrow.png" width="16" height="16" border="0" alt="<?php echo _JLMS_MOVEUP;?>" /></a>';
					} else { tbl_elem.rows[i].cells[4].innerHTML = '&nbsp;'; }
					if (i < (tbl_elem.rows.length - 1)) {
						tbl_elem.rows[i].cells[5].innerHTML = '<a href="javascript: void(0);" onclick="javascript:Down_tbl_row(this); return false;" title="<?php echo _JLMS_MOVEDOWN;?>"><img src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_downarrow.png" width="16" height="16" border="0" alt="<?php echo _JLMS_MOVEDOWN;?>" /></a>';;
					} else { tbl_elem.rows[i].cells[5].innerHTML = '&nbsp;'; }
					tbl_elem.rows[i].className = 'sectiontableentry'+row_k;
					count++;
					row_k = 3 - row_k;
				}
			}
		}
		
		function Redeclare_element_inputs(object) {
			if (object.hasChildNodes()) {
				var children = object.childNodes;
				for (var i = 0; i < children.length; i++) {
					if (children[i].nodeName.toLowerCase() == 'input') {
						var inp_name = children[i].name;
						var inp_value = children[i].value;
						object.removeChild(object.childNodes[i]);
						var input_hidden = document.createElement("input");
						input_hidden.type = "hidden";
						input_hidden.name = inp_name;
						input_hidden.value = inp_value;
						object.appendChild(input_hidden);
					}
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

				// nel'zya prosto skopirovat' staryi innerHTML, t.k. ne sozdadutsya DOM elementy (for IE, Opera compatible).
				var cell1 = document.createElement("td");
				cell1.align = 'center';
				var row = table.insertRow(sec_indx - 1);
				row.appendChild(cell1);
				row.appendChild(element.parentNode.parentNode.cells[1]);
				row.appendChild(element.parentNode.parentNode.cells[1]);
				row.appendChild(element.parentNode.parentNode.cells[1]);
				element.parentNode.parentNode.parentNode.deleteRow(element.parentNode.parentNode.sectionRowIndex);
				var cell5 = document.createElement("td");

				cell5.innerHTML = '<a href="javascript: void(0);" onclick="javascript:Up_tbl_row(this); return false;" title="<?php echo _JLMS_MOVEUP;?>"><img src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_uparrow.png" width="16" height="16" border="0" alt="<?php echo _JLMS_MOVEUP;?>" /></a>';
				row.appendChild(cell5);
				var cell6 = document.createElement("td");
				var cell7 = document.createElement("td");
				cell6.innerHTML = '&nbsp;';
				cell7.innerHTML = '&nbsp;';
				row.appendChild(cell6);
				row.appendChild(cell7);
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
				element.parentNode.parentNode.parentNode.deleteRow(element.parentNode.parentNode.sectionRowIndex);
				cell5.innerHTML = '<a href="javascript: void(0);" onclick="javascript:Up_tbl_row(this); return false;" title="<?php echo _JLMS_MOVEUP;?>"><img src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_uparrow.png" width="16" height="16" border="0" alt="<?php echo _JLMS_MOVEUP;?>" /></a>';
				row.appendChild(cell5);
				var cell6 = document.createElement("td");
				var cell7 = document.createElement("td");
				cell6.innerHTML = '&nbsp;';
				cell7.innerHTML = '&nbsp;';
				row.appendChild(cell6);
				row.appendChild(cell7);
				ReAnalize_tbl_Rows(sec_indx, tbl_id);
			}
		}

		function Add_new_tbl_field(elem_field, elem_field2, tbl_id, field_name, field_name2) {
			// Max value, option
			var new_element_txt = getObj(elem_field).options[getObj(elem_field).selectedIndex].text;
			var new_element_value = getObj(elem_field).value;
			var new_element_txt2 = getObj(elem_field2).options[getObj(elem_field2).selectedIndex].text;
			var new_element_value2 = getObj(elem_field2).value;
			
			if (trim(new_element_txt) == '') {
				alert("Please enter text to the field.");return;
			}
			if (trim(new_element_txt2) == '') {
				alert("Please enter text to the field.");return;
			}
			var tbl_elem = getObj(tbl_id);
			var row = tbl_elem.insertRow(tbl_elem.rows.length);
			var cell1 = document.createElement("td");
			var cell2 = document.createElement("td");
			var cell3 = document.createElement("td");
			var cell4 = document.createElement("td");
			var cell5 = document.createElement("td");
			var cell6 = document.createElement("td");
			var cell7 = document.createElement("td");
			var input_hidden = document.createElement("input");
			input_hidden.type = "hidden";
			input_hidden.name = field_name;
			input_hidden.value = new_element_value;
			var input_hidden2 = document.createElement("input");
			input_hidden2.type = "hidden";
			input_hidden2.name = field_name2;
			input_hidden2.value = new_element_value2;
			var input_hidden_id = document.createElement("input");
			input_hidden_id.type = "hidden";
			input_hidden_id.name = "jq_hid_fields_ids[]";
			input_hidden_id.value = "0";
			cell1.align = 'center';
			cell1.innerHTML = 0;
			cell3.innerHTML = new_element_txt2;
			cell3.appendChild(input_hidden2);
			cell2.innerHTML = new_element_txt;
			cell2.appendChild(input_hidden);
			cell2.appendChild(input_hidden_id);
			cell4.innerHTML = '<a href="javascript: void(0);" onclick="javascript:Delete_tbl_row(this); return false;" title="Delete"><img src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_cancel.png" width="16" height="16" border="0" alt="Delete" /></a>';
			cell5.innerHTML = '<a href="javascript: void(0);" onclick="javascript:Up_tbl_row(this); return false;" title="<?php echo _JLMS_MOVEUP;?>"><img src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_uparrow.png" width="16" height="16" border="0" alt="<?php echo _JLMS_MOVEUP;?>" /></a>';
			cell6.innerHTML = '';
			row.appendChild(cell1);
			row.appendChild(cell2);
			row.appendChild(cell3);
			row.appendChild(cell4);
			row.appendChild(cell5);
			row.appendChild(cell6);row.appendChild(cell7);
			ReAnalize_tbl_Rows(tbl_elem.rows.length - 2, tbl_id);
		}
		
		function view_imgs_1(e){
			var imgs_id = e.value;
			var imgs_name = e.options[e.selectedIndex].value;
			if(imgs_id != 0){
				$('i_left').style.display = 'block';
				$('i_left').src = 'index.php?tmpl=component&option=<?php echo $option;?>&task=quizzes&page=imgs_v&file_id='+imgs_id+'&id=<?php echo $course_id;?>&imgs_name='+imgs_name+'&max_width=<?php echo $JLMS_CONFIG->get('quiz_match_max_width', 250)?>&max_height=<?php echo $JLMS_CONFIG->get('quiz_match_max_height', 30)?>';
			} else {
				$('i_left').style.display = 'none';
				$('i_left').src = '';	
			}
		}
		
		function view_imgs_2(e){
			var imgs_id = e.value;
			var imgs_name = e.options[e.selectedIndex].value;
			if(imgs_id != 0){
				$('i_right').style.display = 'block';
				$('i_right').src = 'index.php?tmpl=component&option=<?php echo $option;?>&task=quizzes&page=imgs_v&file_id='+imgs_id+'&id=<?php echo $course_id;?>&imgs_name='+imgs_name+'&max_width=<?php echo $JLMS_CONFIG->get('quiz_match_max_width', 250)?>&max_height=<?php echo $JLMS_CONFIG->get('quiz_match_max_height', 30)?>';
			} else {
				$('i_right').style.display = 'none';
				$('i_right').src = '';	
			}
		}
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

			if (pressbutton == 'cancel_quest') {
				form.page.value = pressbutton;
				form.submit();
			}
			if (pressbutton == 'preview_quest') {
				if (form.c_id.value == '0') {
					alert("<?php echo _JLMS_QUIZ_SAVE_QUEST_FIRST;?>");
				} else {
					window.open('index.php?option=<?php echo $option;?>&Itemid=<?php echo $Itemid;?>&task=quizzes&id=<?php echo $course_id;?>&page=view_preview&c_id=<?php echo $row->c_id;?>');
					return;
				}
			}
			// do field validation
			if (form.c_question.value == ""){
				alert( "<?php echo _JLMS_QUIZ_ENTER_QUEST_TEXT;?>" );
			} 
			else { 
				<?php if($gqp) {?>
				if( (form.level_id_0.value == 0) && (pressbutton != 'cancel_quest_gqp')) {
					alert( "<?php echo _JLMS_SELECT_CATEGORY;?>" );
				}
				else {
					form.page.value = pressbutton;
					form.submit();
				}
				<?php } else {?>
					form.page.value = pressbutton;
					form.submit();
				<?php }?>
			}	
		}
		//-->
		</script>
	<?php
	$toolbar = JLMS_quiz_admin_html_class::GetQuestEdit_Toolbar($id, $gqp);
	if($gqp) {
		$multicat = array();
		$i=0;
		
		foreach($lists as $key=>$item){
			if(substr($key, 0, 6) == 'level_'){
				if(isset($lists['level_'.$i])) {				
					$multicat[] = $lists['level_'.$i];
				}	
				$i++;
			}
		}
	}
	

	$h = $row->c_id?_JLMS_QUIZ_QUEST_EDIT_TITLE:_JLMS_QUIZ_QUEST_NEW_TITLE;
	
	JLMS_quiz_admin_html_class::showQuizHead( $course_id, $option, $h, true, $toolbar,'',$gqp );?>
		<form action="<?php echo sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid");?>" method="post" name="adminForm" onsubmit="setgood();">
	<?php
		JLMS_TMPL::OpenMT();
		JLMS_TMPL::OpenTS();

		$tabs = new JLMSTabs(0);
		echo $tabs->startPane("JLMS");
		echo $tabs->startTab(_JLMS_QUIZ_E_PARAMS_TAB,"jlmstab1");?>

		<table width="100%" cellpadding="0" cellspacing="0" border="0">
		<?php if($gqp) {?>
			<tr>
				<td colspan="2">
				<?php
					JLMS_quiz_admin_html_class::JQ_subCategory($multicat, $levels);				
				?>	
				</td>
			</tr>
		<?php }?>
			<tr>
				<td align="left" valign="top" colspan="2"><br /><?php echo _JLMS_QUIZ_QUEST_QUEST_TXT;?></td>
			</tr>
			<tr>
				<td colspan="2">
				<?php JLMS_editorArea( 'editor1', $row->c_question, 'c_question', '100%;', '250', '75', '20' ) ; ?>
				</td>
			</tr>
			<tr>
				<td width="20%"><?php echo _JLMS_QUIZ_TBL_QUEST_PUBLISH;?>:</td>
				<td>
					<input type="hidden" name="published" value="<?php echo $row->published; ?>" />
					<input type="checkbox" name="published_chk" onClick="javascript: this.form['published'].value = (this.checked)?1:0;" <?php echo ($row->published == 1)?"checked":""; ?> />
			</tr>
		<?php if(!$gqp) {?>	
			<tr>
				<td width="20%"><br /><?php echo _JLMS_QUIZ_QUEST_QUIZ;?></td><td><?php echo $lists['quiz']; ?></td>
			</tr>
			<tr>
				<td valign="middle" align="left"><br /><?php echo _JLMS_QUIZ_ENTER_CAT;?></td>
				<td><br /><?php echo $lists['jq_categories']; ?></td>
			</tr>
		<?php }?>	
			<tr>
				<td><br /><?php echo _JLMS_QUIZ_QUEST_POINTS;?></td><td><br /><input class="inputbox" type="text" name="c_point" size="50" maxlength="5" value="<?php echo $row->c_point; ?>" /></td>
			</tr>
			<tr>
				<td><br /><?php echo _JLMS_QUIZ_QUEST_ATTEMPTS;?></td><td><br /><input class="inputbox" type="text" name="c_attempts" size="50" maxlength="5" value="<?php echo $row->c_attempts; ?>" /></td>
			</tr>
			<tr>
				<td><br /><?php echo _JLMS_QUIZ_QUEST_ORDERING;?></td><td><br /><?php echo $lists['ordering']; ?></td>
			</tr>
		</table>
		<br />
		<table width="100%" cellpadding="0" cellspacing="0" border="0" id="qfld_tbl">
			<tr>
				<td width="20" class="sectiontableheader" align="center">#</td>
				<td class="sectiontableheader" width="200"><?php echo _JLMS_QUIZ_QUEST_OPTIONS;?></td>
				<td class="sectiontableheader" width="200">&nbsp;</td>
				<td class="sectiontableheader" width="20" align="center">&nbsp;</td>
				<td class="sectiontableheader" width="20" align="center">&nbsp;</td>
				<td class="sectiontableheader" width="20" align="center">&nbsp;</td>
				<td class="sectiontableheader">&nbsp;</td>
			</tr>
			<?php
			$k = 1; $ii = 1; $ind_last = count($row->matching);
			foreach ($row->matching as $frow) { ?>
				<tr class="<?php echo "sectiontableentry$k"; ?>">
					<td align="center"><?php echo $ii;?></td>
					<td align="left">
						<?php echo stripslashes($frow->left_name); $strl = str_replace('"','&quot;',$frow->c_left_text);?>
						<input type="hidden" name="jq_hid_fields_left[]" value="<?php echo $strl;?>" />
						<input type="hidden" name="jq_hid_fields_ids[]" value="<?php echo $frow->c_id;?>" />
					</td>
					<td align="left">
						<?php echo stripslashes($frow->right_name); $strr = str_replace('"','&quot;',$frow->c_right_text);?>
						<input type="hidden" name="jq_hid_fields_right[]" value="<?php echo $strr;?>" />
					</td>
					<td><a href="javascript: void(0);" onclick="javascript:Delete_tbl_row(this); return false;" title="Delete"><img src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_cancel.png" width="16" height="16" border="0" alt="Delete" /></a></td>
					<td><?php if ($ii > 1) { ?><a href="javascript: void(0);" onclick="javascript:Up_tbl_row(this); return false;" title="<?php echo _JLMS_MOVEUP;?>"><img src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_uparrow.png" width="16" height="16" border="0" alt="<?php echo _JLMS_MOVEUP;?>" /></a><?php } ?></td>
					<td><?php if ($ii < $ind_last) { ?><a href="javascript: void(0);" onclick="javascript:Down_tbl_row(this); return false;" title="<?php echo _JLMS_MOVEDOWN;?>"><img src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_downarrow.png" width="16" height="16" border="0" alt="<?php echo _JLMS_MOVEDOWN;?>" /></a><?php } ?></td>
					<td></td>
				</tr>
			<?php
			$k = 3 - $k; $ii ++;
			 } ?>
			</table>
		<br />
		<div style="text-align:left; padding-left:30px ">
			<!--<input id="new_field_left" class="inputbox" style="width:205px " type="text" name="new_field_left" />-->
			<select id="new_field_left" class="inputbox" style="width:205px;" name="new_field_left" onchange="javascript:view_imgs_1(this);">
				<option value="0"><?php echo _JLMS_SB_SELECT_IMAGE;?></option>
				<?php
				foreach($row->images as $data){
				?>
					<option value="<?php echo $data->imgs_id;?>"><?php echo $data->imgs_name;?></option>
				<?php
				}
				?>
			</select>
			
			<select id="new_field_right" class="inputbox" style="width:205px;" name="new_field_right" onchange="javascript:view_imgs_2(this);">
				<option value="0"><?php echo _JLMS_SB_SELECT_IMAGE;?></option>
				<?php
				foreach($row->images as $data){
				?>
					<option value="<?php echo $data->imgs_id;?>"><?php echo $data->imgs_name;?></option>
				<?php
				}
				?>
			</select>
			<input class="inputbox" type="button" name="add_new_field" value="<?php echo _JLMS_QUIZ_QUIZ_ADD_BTN;?>" onclick="javascript:Add_new_tbl_field('new_field_left', 'new_field_right', 'qfld_tbl', 'jq_hid_fields_left[]', 'jq_hid_fields_right[]');" />
		</div>
		<div style="text-align:left; padding-left:30px; ">
			<div id="imgs_left" style="width:205px; text-align:center; overflow:hidden; display:block; float: left;">
				<img id="i_left" src="" title="Example" border="0" align="center"/>&nbsp;
			</div>
			<div id="imgs_right" style="width:205px; text-align:center; margin-left:2px; overflow:hidden; display:block; float: left;">
				<img id="i_right" src="" title="Example" border="0" align="center"/>&nbsp;
			</div>
		</div>
		<?php echo $tabs->endTab();
			echo $tabs->startTab(_JLMS_QUIZ_E_FEEDBACKS_TAB,"jlmstab2"); ?>
		<table width="100%" cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td><br /><?php echo _JLMS_QUIZ_DIS_QUEST_FEEDBACK;?></td><td><br />
					<input type="checkbox" name="params[disable_quest_feedback]" value="1" <?php echo ($params->get('disable_quest_feedback') == 1)?"checked":""; ?> />
				</td>
			</tr>
			<tr>
				<td valign="middle" align="left"><br /><?php echo _JLMS_QUIZ_MES_ANSWER_CORRECT;?></td>
				<td><br /><textarea class="inputbox" name="c_right_message" cols="50" rows="5"><?php echo $lists['c_right_message']; ?></textarea></td>
			</tr>
			<tr>
				<td valign="middle" align="left"><br /><?php echo _JLMS_QUIZ_MES_ANSWER_INCORRECT;?></td>
				<td><br /><textarea class="inputbox" name="c_wrong_message" cols="50" rows="5"><?php echo $lists['c_wrong_message']; ?></textarea></td>
			</tr>
		</table>
		<?php echo $tabs->endTab();
		echo $tabs->startTab(_JLMS_QUIZ_QUESTION_REVIEW_SETTINGS,"jlmstab3"); ?>
		<table width="100%" cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td valign="middle" align="left"><br /><?php echo _JLMS_QUIZ_QUESTION_SETTING_EXPLANATION;?></td>
			
				<td>
				<br /><textarea class="inputbox" name="c_explanation" cols="50" rows="5"><?php echo $row->c_explanation;?></textarea>
				</td>
			</tr>
		</table>
		<?php echo $tabs->endTab();
		echo $tabs->endPane();
		JLMS_TMPL::CloseTS();
		JLMS_TMPL::CloseMT(); ?>

		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
		<input type="hidden" name="task" value="quizzes" />
		<input type="hidden" name="c_type" value="<?php echo $q_om_type;?>" />
		<input type="hidden" name="id" value="<?php echo $course_id;?>" />
		<input type="hidden" name="page" value="save_quiz" />
		<input type="hidden" name="c_id" value="<?php echo $row->c_id;?>" />
		</form>
		<?php JLMS_quiz_admin_html_class::showQuizFooter(); ?>
		<?php
	}

	//Joomla 1.6 ready
	function JQ_editQuest_Blank( &$row, &$lists, $option, $page, $course_id, $q_om_type, &$qtype_str, &$params, $id, $gqp= false, $levels = false ) {
		global $Itemid, $JLMS_CONFIG;?>
		<script language="javascript" type="text/javascript">
		<!--
		var quest_type = <?php echo $q_om_type; ?>;

		function ReAnalize_tbl_Rows( start_index, tbl_id ) {
			start_index = 1;
			var tbl_elem = getObj(tbl_id);
			if (tbl_elem.rows[start_index]) {
				var count = start_index; var row_k = 2 - start_index%2;//0;
				for (var i=start_index; i<tbl_elem.rows.length; i++) {
					tbl_elem.rows[i].cells[0].innerHTML = count;
					Redeclare_element_inputs(tbl_elem.rows[i].cells[1]);
					if (i > 1) { 
						tbl_elem.rows[i].cells[3].innerHTML = '<a class="jlms_img_link" href="javascript: void(0);" onclick="javascript:Up_tbl_row(this); return false;" title="<?php echo _JLMS_MOVEUP;?>"><img src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_uparrow.png" width="16" height="16" border="0" alt="<?php echo _JLMS_MOVEUP;?>" /></a>';
					} else { tbl_elem.rows[i].cells[3].innerHTML = ''; }
					if (i < (tbl_elem.rows.length - 1)) {
						tbl_elem.rows[i].cells[4].innerHTML = '<a class="jlms_img_link" href="javascript: void(0);" onclick="javascript:Down_tbl_row(this); return false;" title="<?php echo _JLMS_MOVEDOWN;?>"><img src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_downarrow.png" width="16" height="16" border="0" alt="<?php echo _JLMS_MOVEDOWN;?>" /></a>';;
					} else { tbl_elem.rows[i].cells[4].innerHTML = ''; }
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
		
		function Redeclare_element_inputs(object) {
			if (object.hasChildNodes()) {
				var children = object.childNodes;
				for (var i = 0; i < children.length; i++) {
					if (children[i].nodeName.toLowerCase() == 'input') {
						var inp_name = children[i].name;
						var inp_value = children[i].value;
						object.removeChild(object.childNodes[i]);
						var input_hidden = document.createElement("input");
						input_hidden.type = "hidden";
						input_hidden.name = inp_name;
						input_hidden.value = inp_value;
						object.appendChild(input_hidden);
					}
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
				var cell2_tmp = element.parentNode.parentNode.cells[1].innerHTML;
				element.parentNode.parentNode.parentNode.deleteRow(element.parentNode.parentNode.sectionRowIndex);
				var row = table.insertRow(sec_indx - 1);
				var cell1 = document.createElement("td");
				var cell2 = document.createElement("td");
				var cell3 = document.createElement("td");
				var cell4 = document.createElement("td");
				cell1.align = 'center';
				cell1.innerHTML = 0;
				cell2.align = 'left';
				cell2.innerHTML = cell2_tmp;
				cell3.innerHTML = '<a class="jlms_img_link" href="javascript: void(0);" onclick="javascript:Delete_tbl_row(this); return false;" title="Delete"><img src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_cancel.png" width="16" height="16" border="0" alt="Delete" /></a>';
				cell4.innerHTML = '<a class="jlms_img_link" href="javascript: void(0);" onclick="javascript:Up_tbl_row(this); return false;" title="<?php echo _JLMS_MOVEUP;?>"><img src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_uparrow.png" width="16" height="16" border="0" alt="<?php echo _JLMS_MOVEUP;?>" /></a>';
				row.appendChild(cell1);
				row.appendChild(cell2);
				row.appendChild(cell3);
				row.appendChild(cell4);
				row.appendChild(document.createElement("td"));
				row.appendChild(document.createElement("td"));
				ReAnalize_tbl_Rows(sec_indx - 2, tbl_id);
			}
		}

		function Down_tbl_row(element) {
			if (element.parentNode.parentNode.sectionRowIndex < element.parentNode.parentNode.parentNode.rows.length - 1) {
				var sec_indx = element.parentNode.parentNode.sectionRowIndex;
				var table = element.parentNode.parentNode.parentNode;
				var tbl_id = table.parentNode.id;
				var cell2_tmp = element.parentNode.parentNode.cells[1].innerHTML;
				element.parentNode.parentNode.parentNode.deleteRow(element.parentNode.parentNode.sectionRowIndex);
				var row = table.insertRow(sec_indx + 1);
				var cell1 = document.createElement("td");
				var cell2 = document.createElement("td");
				var cell3 = document.createElement("td");
				var cell4 = document.createElement("td");
				cell1.align = 'center';
				cell1.innerHTML = 0;
				cell2.align = 'left';
				cell2.innerHTML = cell2_tmp;
				cell3.innerHTML = '<a class="jlms_img_link" href="javascript: void(0);" onclick="javascript:Delete_tbl_row(this); return false;" title="Delete"><img src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_cancel.png" width="16" height="16" border="0" alt="Delete" /></a>';
				cell4.innerHTML = '<a class="jlms_img_link" href="javascript: void(0);" onclick="javascript:Up_tbl_row(this); return false;" title="<?php echo _JLMS_MOVEUP;?>"><img src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_uparrow.png" width="16" height="16" border="0" alt="<?php echo _JLMS_MOVEUP;?>" /></a>';
				row.appendChild(cell1);
				row.appendChild(cell2);
				row.appendChild(cell3);
				row.appendChild(cell4);
				row.appendChild(document.createElement("td"));
				row.appendChild(document.createElement("td"));
				ReAnalize_tbl_Rows(sec_indx, tbl_id);
			}
		}

		function Add_new_tbl_field(elem_field, tbl_id, field_name) {
			var new_element_txt = getObj(elem_field).value;
			if (trim(new_element_txt) == '') {
				alert("Please enter text to the field.");return;
			}
			var tbl_elem = getObj(tbl_id);
			var row = tbl_elem.insertRow(tbl_elem.rows.length);
			var cell1 = document.createElement("td");
			var cell2 = document.createElement("td");
			var cell3 = document.createElement("td");
			var cell4 = document.createElement("td");
			var cell5 = document.createElement("td");
			var cell6 = document.createElement("td");
			var input_hidden = document.createElement("input");
			input_hidden.type = "hidden";
			input_hidden.name = field_name;
			input_hidden.value = new_element_txt;
			var input_hidden_id = document.createElement("input");
			input_hidden_id.type = "hidden";
			input_hidden_id.name = "jq_hid_fields_ids[]";
			input_hidden_id.value = "0";
			cell1.align = 'center';
			cell1.innerHTML = 0;
			cell2.innerHTML = new_element_txt;
			cell2.appendChild(input_hidden);
			cell2.appendChild(input_hidden_id);
			cell3.innerHTML = '<a class="jlms_img_link" href="javascript: void(0);" onclick="javascript:Delete_tbl_row(this); return false;" title="Delete"><img src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_cancel.png" width="16" height="16" border="0" alt="Delete" /></a>';
			cell4.innerHTML = '<a class="jlms_img_link" href="javascript: void(0);" onclick="javascript:Up_tbl_row(this); return false;" title="<?php echo _JLMS_MOVEUP;?>"><img src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_uparrow.png" width="16" height="16" border="0" alt="<?php echo _JLMS_MOVEUP;?>" /></a>';
			cell5.innerHTML = '';
			row.appendChild(cell1);
			row.appendChild(cell2);
			row.appendChild(cell3);
			row.appendChild(cell4);
			row.appendChild(cell5);
			row.appendChild(cell6);
			ReAnalize_tbl_Rows(tbl_elem.rows.length - 2, tbl_id);
		}
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
			if (pressbutton == 'cancel_quest') {
				form.page.value = pressbutton;
				form.submit();
			}
			if (pressbutton == 'preview_quest') {
				if (form.c_id.value == '0') {
					alert("<?php echo _JLMS_QUIZ_SAVE_QUEST_FIRST;?>");
				} else {
					window.open('index.php?option=<?php echo $option;?>&Itemid=<?php echo $Itemid;?>&task=quizzes&id=<?php echo $course_id;?>&page=view_preview&c_id=<?php echo $row->c_id;?>');
					return;
				}
			}
			// do field validation
			if (form.c_question.value == ""){
				alert( "<?php echo _JLMS_QUIZ_ENTER_QUEST_TEXT;?>" );
			} 
			else { 
				<?php if($gqp) {?>
				if( (form.level_id_0.value == 0) && (pressbutton != 'cancel_quest_gqp')) {
					alert( "<?php echo _JLMS_SELECT_CATEGORY;?>" );
				}
				else {
					form.page.value = pressbutton;
					form.submit();
				}
				<?php } else {?>
					form.page.value = pressbutton;
					form.submit();
				<?php }?>
			}	
		}
		function Check_mand(obj)
		{
			var mand = eval(document.adminForm['params[mandatory]']);
			if(obj)
			{
				mand[0].disabled = '';
				mand[1].disabled = '';
			}
			else
			{
				mand[0].disabled = 'disabled';
				mand[1].disabled = 'disabled';
			}
		}
		//-->
		</script>
	<?php
	$toolbar = JLMS_quiz_admin_html_class::GetQuestEdit_Toolbar($id, $gqp);
	if($gqp) {
		$multicat = array();
		$i=0;
		
		foreach($lists as $key=>$item){
			if(substr($key, 0, 6) == 'level_'){
				if(isset($lists['level_'.$i])) {				
					$multicat[] = $lists['level_'.$i];
				}	
				$i++;
			}
		}
	}
 
	$h = $row->c_id?_JLMS_QUIZ_QUEST_EDIT_TITLE:_JLMS_QUIZ_QUEST_NEW_TITLE;
		
	JLMS_quiz_admin_html_class::showQuizHead( $course_id, $option, $h, true, $toolbar,'',$gqp );?>
		<form action="<?php echo sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid");?>" method="post" name="adminForm" onsubmit="setgood();">
	<?php
		JLMS_TMPL::OpenMT('jlms_table_no_borders');
		JLMS_TMPL::OpenTS();

		$tabs = new JLMSTabs(0);
		echo $tabs->startPane("JLMS");
		echo $tabs->startTab(_JLMS_QUIZ_E_PARAMS_TAB,"jlmstab1");?>

		<table width="100%" cellpadding="0" cellspacing="0" border="0" class="jlms_item_properties">
			<?php if($gqp) {?>
			<tr>
				<td colspan="2">
				<?php
					JLMS_quiz_admin_html_class::JQ_subCategory($multicat, $levels);				
				?>	
			</td>
			</tr>
			<?php }?>
			<tr>
				<td align="left" valign="top" colspan="2"><br /><?php echo _JLMS_QUIZ_QUEST_QUEST_TXT;?></td>
			</tr>
			<tr>
				<td colspan="2">
				<?php JLMS_editorArea( 'editor1', $row->c_question, 'c_question', '100%;', '250', '75', '20' ) ; ?>
				</td>
			</tr>
			<tr>
				<td width="20%"><?php echo _JLMS_QUIZ_TBL_QUEST_PUBLISH;?>:</td>
				<td>
					<input type="hidden" name="published" value="<?php echo $row->published; ?>" />
					<input type="checkbox" name="published_chk" onClick="javascript: this.form['published'].value = (this.checked)?1:0;" <?php echo ($row->published == 1)?"checked":""; ?> />
			</tr>
		<?php if(!$gqp) {?>	
			<tr>
				<td width="20%"><br /><?php echo _JLMS_QUIZ_QUEST_QUIZ;?></td><td><?php echo $lists['quiz']; ?></td>
			</tr>
			<tr>
				<td valign="middle" align="left"><br /><?php echo _JLMS_QUIZ_ENTER_CAT;?></td>
				<td><br /><?php echo $lists['jq_categories']; ?></td>
			</tr>
		<?php }?>	
			<tr>
				<td><br /><?php echo _JLMS_QUIZ_QUEST_POINTS;?></td><td><br /><input class="inputbox" type="text" name="c_point" size="50" maxlength="5" value="<?php echo $row->c_point; ?>" /></td>
			</tr>
			<tr>
				<td><br /><?php echo _JLMS_QUIZ_QUEST_ATTEMPTS;?></td><td><br /><input class="inputbox" type="text" name="c_attempts" size="50" maxlength="5" value="<?php echo $row->c_attempts; ?>" /></td>
			</tr>
			<tr>
				<td><br /><?php echo _JLMS_QUIZ_QUEST_ORDERING;?></td><td><br /><?php echo $lists['ordering']; ?></td>
			</tr>
			<tr>
				<td valign="middle" align="left"><br /><?php echo _JLMS_QUIZ_QUESTION_SETTING_SURVEY;?></td>
				<td><br />
					
					<input type="radio" name="params[survey_question]" value="1" <?php echo ($params->get('survey_question'))?"checked":"";?> onclick="Check_mand(this.checked);" /><?php echo 'Yes';?>
					<input type="radio" name="params[survey_question]" value="0" <?php echo (!$params->get('survey_question'))?"checked":"";?> onclick="Check_mand(!this.checked);" /><?php echo 'No';?>
				</td>
			</tr>
			<tr>
				<td valign="middle" align="left"><br /><?php echo _JLMS_QUIZ_QUESTION_SETTING_COMPULSORY;?></td>
				<td><br />
					
					<input type="radio" name="params[mandatory]" value="0" <?php echo (!$params->get('mandatory'))?"checked":"";?> <?php echo (!$params->get('survey_question'))?"disabled":"";?> /><?php echo 'Yes';?>
					<input type="radio" name="params[mandatory]" value="1" <?php echo ($params->get('mandatory'))?"checked":"";?> <?php echo (!$params->get('survey_question'))?"disabled":"";?> /><?php echo 'No';?>
				</td>
			</tr>
			<tr>
				<td valign="middle" align="left"><br /><?php echo _JLMS_QUIZ_QUESTION_SETTING_CASE_SENS;?></td>
				<td><br />
					<input type="radio" name="params[case_sensivity]" value="1" <?php echo ($params->get('case_sensivity', 0))?"checked":"";?> /><?php echo 'Yes';?>
					<input type="radio" name="params[case_sensivity]" value="0" <?php echo (!$params->get('case_sensivity', 0))?"checked":"";?> /><?php echo 'No';?>
				</td>
			</tr>
			<tr>
				<td><br /><?php echo _JLMS_QUIZ_QUESTION_SETTING_DEF_ANSWER;?></td><td><br /><input class="inputbox" type="text" name="c_default" size="50" maxlength="100" value="<?php echo $lists['c_def']; ?>" /></td>
			</tr>
		</table>
		<br />
		<table width="100%" cellpadding="0" cellspacing="0" border="0" id="qfld_tbl" class="<?php echo JLMSCSS::_('jlmslist');?>">
			<tr>
				<<?php echo JLMSCSS::tableheadertag();?> width="20" class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center">#</<?php echo JLMSCSS::tableheadertag();?>>
				<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" width="200"><?php echo _JLMS_QUIZ_QUEST_ANSWERS;?></<?php echo JLMSCSS::tableheadertag();?>>
				<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" width="20" align="center">&nbsp;</<?php echo JLMSCSS::tableheadertag();?>>
				<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" width="20" align="center">&nbsp;</<?php echo JLMSCSS::tableheadertag();?>>
				<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" width="20" align="center">&nbsp;</<?php echo JLMSCSS::tableheadertag();?>>
				<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>">&nbsp;</<?php echo JLMSCSS::tableheadertag();?>>
			</tr>
			<?php
			$k = 1; $ii = 1; $ind_last = count($row->blank_data);
			foreach ($row->blank_data as $frow) { ?>
				<tr class="<?php echo JLMSCSS::_('sectiontableentry'.$k); ?>">
					<td align="center"><?php echo $ii;?></td>
					<td align="left">
						<?php echo stripslashes($frow->c_text); $str = str_replace('"','&quot;',$frow->c_text);?>
						<input type="hidden" name="jq_hid_fields[]" value="<?php echo $str;?>" />
						<input type="hidden" name="jq_hid_fields_ids[]" value="<?php echo $frow->c_id;?>" />
					</td>
					<td><a class="jlms_img_link" href="javascript: void(0);" onclick="javascript:Delete_tbl_row(this); return false;" title="Delete"><img src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_cancel.png" width="16" height="16" border="0" alt="Delete" /></a></td>
					<td><?php if ($ii > 1) { ?><a class="jlms_img_link" href="javascript: void(0);" onclick="javascript:Up_tbl_row(this); return false;" title="<?php echo _JLMS_MOVEUP;?>"><img src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_uparrow.png" width="16" height="16" border="0" alt="<?php echo _JLMS_MOVEUP;?>" /></a><?php } ?></td>
					<td><?php if ($ii < $ind_last) { ?><a class="jlms_img_link" href="javascript: void(0);" onclick="javascript:Down_tbl_row(this); return false;" title="<?php echo _JLMS_MOVEDOWN;?>"><img src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_downarrow.png" width="16" height="16" border="0" alt="<?php echo _JLMS_MOVEDOWN;?>" /></a><?php } ?></td>
					<td></td>
				</tr>
			<?php
			$k = 3 - $k; $ii ++;
			 } ?>
			</table>
		<br />
		<div style="text-align:left; padding-left:30px ">
			<input id="new_field" class="inputbox" style="width:205px " type="text" name="new_field" />
			<input class="inputbox" type="button" name="add_new_field" value="<?php echo _JLMS_QUIZ_QUIZ_ADDOPTION_BTN;?>" onclick="javascript:Add_new_tbl_field('new_field', 'qfld_tbl', 'jq_hid_fields[]');" />
		</div>
		<?php echo $tabs->endTab();
			echo $tabs->startTab(_JLMS_QUIZ_E_FEEDBACKS_TAB,"jlmstab2"); ?>
		<table width="100%" cellpadding="0" cellspacing="0" border="0" class="jlms_item_properties">
			<tr>
				<td><br /><?php echo _JLMS_QUIZ_DIS_QUEST_FEEDBACK;?></td><td><br />
					<input type="checkbox" name="params[disable_quest_feedback]" value="1" <?php echo ($params->get('disable_quest_feedback') == 1)?"checked":""; ?> />
				</td>
			</tr>
			<tr>
				<td valign="middle" align="left"><br /><?php echo _JLMS_QUIZ_MES_ANSWER_CORRECT;?></td>
				<td><br /><textarea class="inputbox" name="c_right_message" cols="50" rows="5"><?php echo $lists['c_right_message']; ?></textarea></td>
			</tr>
			<tr>
				<td valign="middle" align="left"><br /><?php echo _JLMS_QUIZ_MES_ANSWER_INCORRECT;?></td>
				<td><br /><textarea class="inputbox" name="c_wrong_message" cols="50" rows="5"><?php echo $lists['c_wrong_message']; ?></textarea></td>
			</tr>
		</table>
		<?php echo $tabs->endTab();
		echo $tabs->startTab(_JLMS_QUIZ_QUESTION_REVIEW_SETTINGS,"jlmstab3"); ?>
		<table width="100%" cellpadding="0" cellspacing="0" border="0" class="jlms_item_properties">
			<tr>
				<td valign="middle" align="left"><br /><?php echo _JLMS_QUIZ_QUESTION_SETTING_EXPLANATION;?></td>
			
				<td>
				<br /><textarea class="inputbox" name="c_explanation" cols="50" rows="5"><?php echo $row->c_explanation;?></textarea>
				</td>
			</tr>
		</table>
		<?php echo $tabs->endTab();
		echo $tabs->endPane();
		JLMS_TMPL::CloseTS();
		JLMS_TMPL::CloseMT(); ?>

		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
		<input type="hidden" name="task" value="quizzes" />
		<input type="hidden" name="c_type" value="<?php echo $q_om_type;?>" />
		<input type="hidden" name="id" value="<?php echo $course_id;?>" />
		<input type="hidden" name="page" value="save_quiz" />
		<input type="hidden" name="c_id" value="<?php echo $row->c_id;?>" />
		</form>
		<?php JLMS_quiz_admin_html_class::showQuizFooter(); ?>
		<?php
	}

	//Joomla 1.6 ready
	function JQ_editQuest_Survey( &$row, &$lists, $option, $page, $course_id, $q_om_type, &$qtype_str, &$params, $id, $gqp= false, $levels = false ) {
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
	if (pressbutton == 'cancel_quest') {
		form.page.value = pressbutton;
		form.submit();
	}
	if (pressbutton == 'preview_quest') {
		if (form.c_id.value == '0') {
			alert("<?php echo _JLMS_QUIZ_SAVE_QUEST_FIRST;?>");
		} else {
			window.open('index.php?option=<?php echo $option;?>&Itemid=<?php echo $Itemid;?>&task=quizzes&id=<?php echo $course_id;?>&page=view_preview&c_id=<?php echo $row->c_id;?>');
			return;
		}
	}
	// do field validation
	if (form.c_question.value == ""){
		alert( "<?php echo _JLMS_QUIZ_ENTER_QUEST_TEXT;?>" );
	} 
	else { 
		<?php if($gqp) {?>
		if( (form.level_id_0.value == 0) && (pressbutton != 'cancel_quest_gqp')) {
			alert( "<?php echo _JLMS_SELECT_CATEGORY;?>" );
		}
		else {
			form.page.value = pressbutton;
			form.submit();
		}
		<?php } else {?>
			form.page.value = pressbutton;
			form.submit();
		<?php }?>
	}	
}
//-->
</script>
	<?php
	$toolbar = JLMS_quiz_admin_html_class::GetQuestEdit_Toolbar($id, $gqp);
	
	if($gqp) {
		$multicat = array();
		$i=0;
		
		foreach($lists as $key=>$item){
			if(substr($key, 0, 6) == 'level_'){
				if(isset($lists['level_'.$i])) {				
					$multicat[] = $lists['level_'.$i];
				}	
				$i++;
			}
		}
	}

	$h = $row->c_id?_JLMS_QUIZ_QUEST_EDIT_TITLE:_JLMS_QUIZ_QUEST_NEW_TITLE;

	JLMS_quiz_admin_html_class::showQuizHead( $course_id, $option, $h, true, $toolbar, '', $gqp );?>
		<form action="<?php echo sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid");?>" method="post" name="adminForm" onsubmit="setgood();">
	<?php
		JLMS_TMPL::OpenMT('jlms_table_no_borders');
		JLMS_TMPL::OpenTS();

		$tabs = new JLMSTabs(0);
		echo $tabs->startPane("JLMS");
		echo $tabs->startTab(_JLMS_QUIZ_E_PARAMS_TAB,"jlmstab1");?>

		<table width="100%" cellpadding="0" cellspacing="0" border="0" class="jlms_item_properties">
		<?php if($gqp) {?>
			<tr>
				<td colspan="2">
				<?php
					JLMS_quiz_admin_html_class::JQ_subCategory($multicat, $levels);				
				?>	
				</td>
			</tr>
		<?php }?>
			<tr>
				<td align="left" valign="top" colspan="2"><br /><?php echo _JLMS_QUIZ_QUEST_QUEST_TXT;?></td>
			</tr>
			<tr>
				<td colspan="2">
				<?php JLMS_editorArea( 'editor1', $row->c_question, 'c_question', '100%;', '250', '75', '20' ) ; ?>
				</td>
			</tr>
			<tr>
				<td width="20%"><?php echo _JLMS_QUIZ_TBL_QUEST_PUBLISH;?>:</td>
				<td>
					<input type="hidden" name="published" value="<?php echo $row->published; ?>" />
					<input type="checkbox" name="published_chk" onClick="javascript: this.form['published'].value = (this.checked)?1:0;" <?php echo ($row->published == 1)?"checked":""; ?> />
			</tr>
		<?php if(!$gqp) {?>	
			<tr>
				<td width="20%"><br /><?php echo _JLMS_QUIZ_QUEST_QUIZ;?></td><td><?php echo $lists['quiz']; ?></td>
			</tr>
			<tr>
				<td valign="middle" align="left"><br /><?php echo _JLMS_QUIZ_ENTER_CAT;?></td>
				<td><br /><?php echo $lists['jq_categories']; ?></td>
			</tr>
		<?php }?>	
			<tr>
				<td><br /><?php echo _JLMS_QUIZ_QUEST_POINTS;?></td><td><br /><input class="inputbox" type="text" name="c_point" size="50" maxlength="5" value="<?php echo $row->c_point; ?>" /></td>
			</tr>
			<tr>
				<td><br /><?php echo _JLMS_QUIZ_QUEST_ATTEMPTS;?></td><td><br /><input class="inputbox" type="text" name="c_attempts" size="50" maxlength="5" value="<?php echo $row->c_attempts; ?>" /></td>
			</tr>
			<tr>
				<td><br /><?php echo _JLMS_QUIZ_QUEST_ORDERING;?></td><td><br /><?php echo $lists['ordering']; ?></td>
			</tr>
			<tr>
				<td valign="middle" align="left"><br /><?php echo _JLMS_QUIZ_QUESTION_SETTING_COMPULSORY;?></td>
				<td><br />
					
					<input type="radio" name="params[mandatory]" value="0" <?php echo (!$params->get('mandatory'))?"checked":"";?> /><?php echo 'Yes';?>
					<input type="radio" name="params[mandatory]" value="1" <?php echo ($params->get('mandatory'))?"checked":"";?> /><?php echo 'No';?>
				</td>
			</tr>
		</table>
		<?php echo $tabs->endTab();
			echo $tabs->startTab(_JLMS_QUIZ_E_FEEDBACKS_TAB,"jlmstab2"); ?>
		<table width="100%" cellpadding="0" cellspacing="0" border="0" class="jlms_item_properties">
			<tr>
				<td><br /><?php echo _JLMS_QUIZ_DIS_QUEST_FEEDBACK;?></td><td><br />
					<input type="checkbox" name="params[disable_quest_feedback]" value="1" <?php echo ($params->get('disable_quest_feedback') == 1)?"checked":""; ?> />
				</td>
			</tr>
			<tr>
				<td valign="middle" align="left"><br /><?php echo _JLMS_QUIZ_MES_ANSWER_CORRECT;?></td>
				<td><br /><textarea class="inputbox" name="c_right_message" cols="50" rows="5"><?php echo $lists['c_right_message']; ?></textarea></td>
			</tr>
			<tr>
				<td valign="middle" align="left"><br /><?php echo _JLMS_QUIZ_MES_ANSWER_INCORRECT;?></td>
				<td><br /><textarea class="inputbox" name="c_wrong_message" cols="50" rows="5"><?php echo $lists['c_wrong_message']; ?></textarea></td>
			</tr>
		</table>
		<?php echo $tabs->endTab();
		echo $tabs->startTab(_JLMS_QUIZ_QUESTION_REVIEW_SETTINGS,"jlmstab3"); ?>
		<table width="100%" cellpadding="0" cellspacing="0" border="0" class="jlms_item_properties">
			<tr>
				<td valign="middle" align="left"><br /><?php echo _JLMS_QUIZ_QUESTION_SETTING_EXPLANATION;?></td>
			
				<td>
				<br /><textarea class="inputbox" name="c_explanation" cols="50" rows="5"><?php echo $row->c_explanation;?></textarea>
				</td>
			</tr>
		</table>
		<?php echo $tabs->endTab();
		echo $tabs->endPane();
		JLMS_TMPL::CloseTS();
		JLMS_TMPL::CloseMT(); ?>
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
		<input type="hidden" name="task" value="quizzes" />
		<input type="hidden" name="c_type" value="<?php echo $q_om_type;?>" />
		<input type="hidden" name="id" value="<?php echo $course_id;?>" />
		<input type="hidden" name="page" value="save_quiz" />
		<input type="hidden" name="c_id" value="<?php echo $row->c_id;?>" />
		</form>
		<?php JLMS_quiz_admin_html_class::showQuizFooter(); ?>
		<?php
	}

	//Joomla 1.6 ready
	function JQ_editQuest_HotSpot( &$row, &$lists, $option, $page, $course_id, $q_om_type, &$qtype_str, &$params, $id, $gqp= false, $levels = false ) {
		global $Itemid, $JLMS_CONFIG;?>
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
	if (pressbutton == 'cancel_quest') {
		form.page.value = pressbutton;
		form.submit();
		return;
	}
	if (pressbutton == 'preview_quest') {
		if (form.c_id.value == '0') {
			alert("<?php echo _JLMS_QUIZ_SAVE_QUEST_FIRST;?>");
		} else {
			window.open('index.php?option=<?php echo $option;?>&Itemid=<?php echo $Itemid;?>&task=quizzes&id=<?php echo $course_id;?>&page=view_preview&c_id=<?php echo $row->c_id;?>');
			return;
		}
	}
	// do field validation
	if (form.c_question.value == ""){
		alert( "<?php echo _JLMS_QUIZ_ENTER_QUEST_TEXT;?>" );
	} 
	else { 
		<?php if($gqp) {?>
		if( (form.level_id_0.value == 0) && (pressbutton != 'cancel_quest_gqp')) {
			alert( "<?php echo _JLMS_SELECT_CATEGORY;?>" );
		}
		else {
			form.page.value = pressbutton;
			form.submit();
		}
		<?php } else {?>
			form.page.value = pressbutton;
			form.submit();
		<?php }?>
	}	
}
//-->
</script>
	<?php
	$toolbar = JLMS_quiz_admin_html_class::GetQuestEdit_Toolbar($id, $gqp);
	if($gqp) {
		$multicat = array();
		$i=0;
		
		foreach($lists as $key=>$item){
			if(substr($key, 0, 6) == 'level_'){
				if(isset($lists['level_'.$i])) {				
					$multicat[] = $lists['level_'.$i];
				}	
				$i++;
			}
		}
	}
 
		$h = $row->c_id?_JLMS_QUIZ_QUEST_EDIT_TITLE:_JLMS_QUIZ_QUEST_NEW_TITLE;

		JLMS_quiz_admin_html_class::showQuizHead( $course_id, $option, $h, true, $toolbar,'',$gqp );?>
		<form action="<?php echo sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid");?>" method="post" name="adminForm" onsubmit="setgood();">
	<?php
		JLMS_TMPL::OpenMT('jlms_table_no_borders');
		JLMS_TMPL::OpenTS();

		$tabs = new JLMSTabs(0);
		echo $tabs->startPane("JLMS");
		echo $tabs->startTab(_JLMS_QUIZ_E_PARAMS_TAB,"jlmstab1");?>
		<table width="100%" cellpadding="0" cellspacing="0" border="0" class="jlms_item_properties">
		
		<?php if($gqp) {?>
			<tr>
				<td colspan="2">
				<?php
					JLMS_quiz_admin_html_class::JQ_subCategory($multicat, $levels);				
				?>	
				</td>
			</tr>
		<?php }?>	
			<tr>
				<td align="left" valign="top" colspan="2"><br /><?php echo _JLMS_QUIZ_QUEST_QUEST_TXT;?></td>
			</tr>
			<tr>
				<td colspan="2">
				<?php JLMS_editorArea( 'editor1', $row->c_question, 'c_question', '100%;', '250', '75', '20' ) ; ?>
				</td>
			</tr>
			<tr>
				<td width="20%"><?php echo _JLMS_QUIZ_TBL_QUEST_PUBLISH;?>:</td>
				<td>
					<input type="hidden" name="published" value="<?php echo $row->published; ?>" />
					<input type="checkbox" name="published_chk" onClick="javascript: this.form['published'].value = (this.checked)?1:0;" <?php echo ($row->published == 1)?"checked":""; ?> />
			</tr>
		<?php if(!$gqp) {?>
			<tr>
				<td width="20%"><br /><?php echo _JLMS_QUIZ_QUEST_QUIZ;?></td><td><?php echo $lists['quiz']; ?></td>
			</tr>
			<tr>
				<td valign="middle" align="left"><br /><?php echo _JLMS_QUIZ_ENTER_CAT;?></td>
				<td><br /><?php echo $lists['jq_categories']; ?></td>
			</tr>
		<?php }?>	
			<tr>
				<td><br /><?php echo _JLMS_QUIZ_QUEST_POINTS;?></td><td><br /><input class="inputbox" type="text" name="c_point" size="50" maxlength="5" value="<?php echo $row->c_point; ?>" /></td>
			</tr>
			<tr>
				<td><br /><?php echo _JLMS_QUIZ_QUEST_ATTEMPTS;?></td><td><br /><input class="inputbox" type="text" name="c_attempts" size="50" maxlength="5" value="<?php echo $row->c_attempts; ?>" /></td>
			</tr>
			<tr>
				<td><br /><?php echo _JLMS_QUIZ_QUEST_ORDERING;?></td><td><br /><?php echo $lists['ordering']; ?></td>
			</tr>
		</table>
		<br />
		<table width="100%" cellpadding="0" cellspacing="0" border="0" class="jlms_table_no_borders">
			<tr><td>
			<?php echo _JLMS_QUIZ_QUEST_HS_NOTE1; ?>
			<br />
			<?php echo _JLMS_QUIZ_QUEST_HS_NOTE2; ?>
			<br />
			<?php echo _JLMS_QUIZ_QUEST_HS_NOTE3; ?>
			</td></tr>
		</table>
		<br />
		<table width="100%" cellpadding="0" cellspacing="0" border="0" class="jlms_item_properties">
			<tr>
				<td align="left" width="20%"><?php echo _JLMS_QUIZ_QUEST_IMAGE;?></td>
				<td>
				<table cellpadding="0" cellspacing="0" border="0" class="jlms_item_properties"><tr>
					<td>
						<?php echo $lists['images'];?>
					</td>
					<td>
						<?php 
						if(!$gqp) {
							$lnk = sefRelToAbs("index.php?tmpl=component&option=$option&amp;Itemid=$Itemid&amp;task=quizzes&amp;id=$course_id&amp;page=uploadimage");
						}
						else {
							$lnk = sefRelToAbs("index.php?tmpl=component&option=$option&amp;Itemid=$Itemid&amp;task=quizzes&amp;page=uploadimage_gqp");
						}
					 	?>
						<a href="javascript:popupWindow('<?php echo $lnk;?>','win1',250,100,'no');"><img src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/filesave.png" border="0" width="16" height="16" alt="filesave" /></a>
					</td>
				</tr></table>
				</td>
			</tr>
			<tr>
				<td colspan="2"><br />
					<img id="img_hotspot" src="<?php echo $JLMS_CONFIG->get('live_site');?>/images/<?php echo ($row->c_image)?('joomlaquiz/images/'.$row->c_image):'blank.png';?>" name="imagelib">
					<br />
					<?php 
						if(!$gqp) {
							$lnk_hs = $JLMS_CONFIG->get('live_site')."/index.php?tmpl=component&option=$option&amp;Itemid=$Itemid&amp;task=quizzes&amp;id=$course_id&amp;no_html=1&amp;page=createhotspot&amp;hotspot=".$row->c_id; 
						}
						else {
							$lnk_hs = $JLMS_CONFIG->get('live_site')."/index.php?tmpl=component&option=$option&amp;Itemid=$Itemid&amp;task=quizzes&amp;no_html=1&amp;page=createhotspot_gqp&amp;hotspot=".$row->c_id; 
						}							
					?>	
					<input class="inputbox" type="button" onclick="popupWindow('<?php echo $lnk_hs;?>','win1',800,600,'no');" name="btn_create_hotspot" value="<?php echo _JLMS_QUIZ_HOTSPOT_BTN;?>">
				</td>
			</tr>
		</table>
		<?php echo $tabs->endTab();
			echo $tabs->startTab(_JLMS_QUIZ_E_FEEDBACKS_TAB,"jlmstab2"); ?>
		<table width="100%" cellpadding="0" cellspacing="0" border="0" class="jlms_item_properties">
			<tr>
				<td><br /><?php echo _JLMS_QUIZ_DIS_QUEST_FEEDBACK;?></td><td><br />
					<input type="checkbox" name="params[disable_quest_feedback]" value="1" <?php echo ($params->get('disable_quest_feedback') == 1)?"checked":""; ?> />
				</td>
			</tr>
			<tr>
				<td valign="middle" align="left"><br /><?php echo _JLMS_QUIZ_MES_ANSWER_CORRECT;?></td>
				<td><br /><textarea class="inputbox" name="c_right_message" cols="50" rows="5"><?php echo $lists['c_right_message']; ?></textarea></td>
			</tr>
			<tr>
				<td valign="middle" align="left"><br /><?php echo _JLMS_QUIZ_MES_ANSWER_INCORRECT;?></td>
				<td><br /><textarea class="inputbox" name="c_wrong_message" cols="50" rows="5"><?php echo $lists['c_wrong_message']; ?></textarea></td>
			</tr>
		</table>
		<?php echo $tabs->endTab();
		echo $tabs->startTab(_JLMS_QUIZ_QUESTION_REVIEW_SETTINGS,"jlmstab3"); ?>
		<table width="100%" cellpadding="0" cellspacing="0" border="0" class="jlms_item_properties">
			<tr>
				<td valign="middle" align="left"><br /><?php echo _JLMS_QUIZ_QUESTION_SETTING_EXPLANATION;?></td>
			
				<td>
				<br /><textarea class="inputbox" name="c_explanation" cols="50" rows="5"><?php echo $row->c_explanation;?></textarea>
				</td>
			</tr>
		</table>
		<?php echo $tabs->endTab();
		echo $tabs->endPane();
		JLMS_TMPL::CloseTS();
		JLMS_TMPL::CloseMT(); ?>
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
		<input type="hidden" name="task" value="quizzes" />
		<input type="hidden" name="c_type" value="<?php echo $q_om_type;?>" />
		<input type="hidden" name="id" value="<?php echo $course_id;?>" />
		<input type="hidden" name="page" value="save_quiz" />
		<input type="hidden" name="c_id" value="<?php echo $row->c_id;?>" />
		</form>
		<?php JLMS_quiz_admin_html_class::showQuizFooter(); ?>
		<?php
	}

	//Joomla 1.6 ready
	function JQ_editQuest_Scale( &$row, &$lists, $option, $page, $course_id, $q_om_type, &$qtype_str, &$params, $id, $gqp= false, $levels = false ) {
		global $Itemid, $JLMS_CONFIG;?>
		<script language="javascript" type="text/javascript">
		<!--
		var quest_type = <?php echo $q_om_type; ?>;

		function ReAnalize_tbl_Rows( start_index, tbl_id ) {
			start_index = 1;
			var tbl_elem = getObj(tbl_id);
			if (tbl_elem.rows[start_index]) {
				var count = start_index; var row_k = 2 - start_index%2;//0;
				for (var i=start_index; i<tbl_elem.rows.length; i++) {
					tbl_elem.rows[i].cells[0].innerHTML = count;
					Redeclare_element_inputs(tbl_elem.rows[i].cells[1]);
					if (i > 1) { 
						tbl_elem.rows[i].cells[3].innerHTML = '<a class="jlms_img_link" href="javascript: void(0);" onclick="javascript:Up_tbl_row(this); return false;" title="<?php echo _JLMS_MOVEUP;?>"><img src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_uparrow.png" width="16" height="16" border="0" alt="<?php echo _JLMS_MOVEUP;?>" /></a>';
					} else { tbl_elem.rows[i].cells[3].innerHTML = ''; }
					if (i < (tbl_elem.rows.length - 1)) {
						tbl_elem.rows[i].cells[4].innerHTML = '<a class="jlms_img_link" href="javascript: void(0);" onclick="javascript:Down_tbl_row(this); return false;" title="<?php echo _JLMS_MOVEDOWN;?>"><img src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_downarrow.png" width="16" height="16" border="0" alt="<?php echo _JLMS_MOVEDOWN;?>" /></a>';;
					} else { tbl_elem.rows[i].cells[4].innerHTML = ''; }
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
		
		function Redeclare_element_inputs(object) {
			if (object.hasChildNodes()) {
				var children = object.childNodes;
				for (var i = 0; i < children.length; i++) {
					if (children[i].nodeName.toLowerCase() == 'input') {
						var inp_name = children[i].name;
						var inp_value = children[i].value;
						object.removeChild(object.childNodes[i]);
						var input_hidden = document.createElement("input");
						input_hidden.type = "hidden";
						input_hidden.name = inp_name;
						input_hidden.value = inp_value;
						object.appendChild(input_hidden);
					}
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
				var cell2_tmp = element.parentNode.parentNode.cells[1].innerHTML;
				element.parentNode.parentNode.parentNode.deleteRow(element.parentNode.parentNode.sectionRowIndex);
				var row = table.insertRow(sec_indx - 1);
				var cell1 = document.createElement("td");
				var cell2 = document.createElement("td");
				var cell3 = document.createElement("td");
				var cell4 = document.createElement("td");
				cell1.align = 'center';
				cell1.innerHTML = 0;
				cell2.align = 'left';
				cell2.innerHTML = cell2_tmp;
				cell3.innerHTML = '<a class="jlms_img_link" href="javascript: void(0);" onclick="javascript:Delete_tbl_row(this); return false;" title="Delete"><img src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_cancel.png" width="16" height="16" border="0" alt="Delete" /></a>';
				cell4.innerHTML = '<a class="jlms_img_link" href="javascript: void(0);" onclick="javascript:Up_tbl_row(this); return false;" title="<?php echo _JLMS_MOVEUP;?>"><img src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_uparrow.png" width="16" height="16" border="0" alt="<?php echo _JLMS_MOVEUP;?>" /></a>';
				row.appendChild(cell1);
				row.appendChild(cell2);
				row.appendChild(cell3);
				row.appendChild(cell4);
				row.appendChild(document.createElement("td"));
				row.appendChild(document.createElement("td"));
				ReAnalize_tbl_Rows(sec_indx - 2, tbl_id);
			}
		}

		function Down_tbl_row(element) {
			if (element.parentNode.parentNode.sectionRowIndex < element.parentNode.parentNode.parentNode.rows.length - 1) {
				var sec_indx = element.parentNode.parentNode.sectionRowIndex;
				var table = element.parentNode.parentNode.parentNode;
				var tbl_id = table.parentNode.id;
				var cell2_tmp = element.parentNode.parentNode.cells[1].innerHTML;
				element.parentNode.parentNode.parentNode.deleteRow(element.parentNode.parentNode.sectionRowIndex);
				var row = table.insertRow(sec_indx + 1);
				var cell1 = document.createElement("td");
				var cell2 = document.createElement("td");
				var cell3 = document.createElement("td");
				var cell4 = document.createElement("td");
				cell1.align = 'center';
				cell1.innerHTML = 0;
				cell2.align = 'left';
				cell2.innerHTML = cell2_tmp;
				cell3.innerHTML = '<a class="jlms_img_link" href="javascript: void(0);" onclick="javascript:Delete_tbl_row(this); return false;" title="Delete"><img src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_cancel.png" width="16" height="16" border="0" alt="Delete" /></a>';
				cell4.innerHTML = '<a class="jlms_img_link" href="javascript: void(0);" onclick="javascript:Up_tbl_row(this); return false;" title="<?php echo _JLMS_MOVEUP;?>"><img src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_uparrow.png" width="16" height="16" border="0" alt="<?php echo _JLMS_MOVEUP;?>" /></a>';
				row.appendChild(cell1);
				row.appendChild(cell2);
				row.appendChild(cell3);
				row.appendChild(cell4);
				row.appendChild(document.createElement("td"));
				row.appendChild(document.createElement("td"));
				ReAnalize_tbl_Rows(sec_indx, tbl_id);
			}
		}

		function Add_new_tbl_field(elem_field, tbl_id, field_name) {
			var new_element_txt = getObj(elem_field).value;
			if (trim(new_element_txt) == '') {
				alert("Please enter text to the field.");return;
			}
			var tbl_elem = getObj(tbl_id);
			var row = tbl_elem.insertRow(tbl_elem.rows.length);
			var cell1 = document.createElement("td");
			var cell2 = document.createElement("td");
			var cell3 = document.createElement("td");
			var cell4 = document.createElement("td");
			var cell5 = document.createElement("td");
			var cell6 = document.createElement("td");
			var input_hidden = document.createElement("input");
			input_hidden.type = "hidden";
			input_hidden.name = field_name;
			input_hidden.value = new_element_txt;
			var input_hidden_id = document.createElement("input");
			input_hidden_id.type = "hidden";
			if(elem_field == 'new_field')
			input_hidden_id.name = "jq_hid_fields_ids[]";
			else
			input_hidden_id.name = "jq_hid_fields_mark_ids[]";
			input_hidden_id.value = "0";
			cell1.align = 'center';
			cell1.innerHTML = 0;
			cell2.innerHTML = new_element_txt;
			cell2.appendChild(input_hidden);
			cell2.appendChild(input_hidden_id);
			cell3.innerHTML = '<a class="jlms_img_link" href="javascript: void(0);" onclick="javascript:Delete_tbl_row(this); return false;" title="Delete"><img src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_cancel.png" width="16" height="16" border="0" alt="Delete" /></a>';
			cell4.innerHTML = '<a class="jlms_img_link" href="javascript: void(0);" onclick="javascript:Up_tbl_row(this); return false;" title="<?php echo _JLMS_MOVEUP;?>"><img src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_uparrow.png" width="16" height="16" border="0" alt="<?php echo _JLMS_MOVEUP;?>" /></a>';
			cell5.innerHTML = '';
			row.appendChild(cell1);
			row.appendChild(cell2);
			row.appendChild(cell3);
			row.appendChild(cell4);
			row.appendChild(cell5);
			row.appendChild(cell6);
			ReAnalize_tbl_Rows(tbl_elem.rows.length - 2, tbl_id);
		}
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
			if (pressbutton == 'cancel_quest') {
				form.page.value = pressbutton;
				form.submit();
			}
			if (pressbutton == 'preview_quest') {
				if (form.c_id.value == '0') {
					alert("<?php echo _JLMS_QUIZ_SAVE_QUEST_FIRST;?>");
				} else {
					window.open('index.php?option=<?php echo $option;?>&Itemid=<?php echo $Itemid;?>&task=quizzes&id=<?php echo $course_id;?>&page=view_preview&c_id=<?php echo $row->c_id;?>');
					return;
				}
			}
			// do field validation
			if (form.c_question.value == ""){
				alert( "<?php echo _JLMS_QUIZ_ENTER_QUEST_TEXT;?>" );
			} 
			else { 
				<?php if($gqp) {?>
				if( (form.level_id_0.value == 0) && (pressbutton != 'cancel_quest_gqp')) {
					alert( "<?php echo _JLMS_SELECT_CATEGORY;?>" );
				}
				else {
					form.page.value = pressbutton;
					form.submit();
				}
				<?php } else {?>
					form.page.value = pressbutton;
					form.submit();
				<?php }?>
			}	
		}
		//-->
		</script>
	<?php
	$toolbar = JLMS_quiz_admin_html_class::GetQuestEdit_Toolbar($id, $gqp);
	if($gqp) {
		$multicat = array();
		$i=0;
		
		foreach($lists as $key=>$item){
			if(substr($key, 0, 6) == 'level_'){
				if(isset($lists['level_'.$i])) {				
					$multicat[] = $lists['level_'.$i];
				}	
				$i++;
			}
		}
	}

	$h = $row->c_id?_JLMS_QUIZ_QUEST_EDIT_TITLE:_JLMS_QUIZ_QUEST_NEW_TITLE;
	
	JLMS_quiz_admin_html_class::showQuizHead( $course_id, $option, $h, true, $toolbar, '', $gqp );?>
		<form action="<?php echo sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid");?>" method="post" name="adminForm" onsubmit="setgood();">
	<?php
		JLMS_TMPL::OpenMT('jlms_table_no_borders');
		JLMS_TMPL::OpenTS();

		$tabs = new JLMSTabs(0);
		echo $tabs->startPane("JLMS");
		echo $tabs->startTab(_JLMS_QUIZ_E_PARAMS_TAB,"jlmstab1");?>

		<table width="100%" cellpadding="0" cellspacing="0" border="0" class="jlms_item_properties">
		<?php if($gqp) {?>
			<tr>
				<td colspan="2">
				<?php
					JLMS_quiz_admin_html_class::JQ_subCategory($multicat, $levels);				
				?>	
				</td>
			</tr>
		<?php }?>	
			<tr>
				<td align="left" valign="top" colspan="2"><br /><?php echo _JLMS_QUIZ_QUEST_QUEST_TXT;?></td>
			</tr>
			<tr>
				<td colspan="2">
				<?php JLMS_editorArea( 'editor1', $row->c_question, 'c_question', '100%;', '250', '75', '20' ) ; ?>
				</td>
			</tr>
			<tr>
				<td width="20%"><?php echo _JLMS_QUIZ_TBL_QUEST_PUBLISH;?>:</td>
				<td>
					<input type="hidden" name="published" value="<?php echo $row->published; ?>" />
					<input type="checkbox" name="published_chk" onClick="javascript: this.form['published'].value = (this.checked)?1:0;" <?php echo ($row->published == 1)?"checked":""; ?> />
			</tr>
			<?php /* <!--tr>
				<td align="left" valign="top" colspan="2"><br /><?php echo 'Question Explanation:';?></td>
			</tr-->
			<!--tr>
				<td colspan="2">
				<?php JLMS_editorArea( 'editor11', $row->c_explanation, 'c_explanation', '100%;', '250', '75', '20' ) ; ?>
				</td>
			</tr--> */ ?>
		<?php if(!$gqp) {?>	
			<tr>
				<td width="20%"><br /><?php echo _JLMS_QUIZ_QUEST_QUIZ;?></td><td><?php echo $lists['quiz']; ?></td>
			</tr>
			<tr>
				<td valign="middle" align="left"><br /><?php echo _JLMS_QUIZ_ENTER_CAT;?></td>
				<td><br /><?php echo $lists['jq_categories']; ?></td>
			</tr>
		<?php }?>	
			<tr>
				<td><br /><?php echo _JLMS_QUIZ_QUEST_ATTEMPTS;?></td><td><br /><input class="inputbox" type="text" name="c_attempts" size="50" maxlength="5" value="<?php echo $row->c_attempts; ?>" /></td>
			</tr>
			<tr>
				<td><br /><?php echo _JLMS_QUIZ_QUEST_ORDERING;?></td><td><br /><?php echo $lists['ordering']; ?></td>
			</tr>
			<tr>
				<td valign="middle" align="left"><br /><?php echo _JLMS_QUIZ_QUESTION_SETTING_SURVEY;?></td>
				<td><br />
					
					<input type="radio" name="params[survey_question]" value="1" <?php echo ($params->get('survey_question'))?"checked":"";?> disabled /><?php echo 'Yes';?>
					<input type="radio" name="params[survey_question]" value="0" <?php echo (!$params->get('survey_question'))?"checked":"";?> disabled /><?php echo 'No';?>
				</td>
			</tr>
			<tr>
				<td valign="middle" align="left"><br /><?php echo _JLMS_QUIZ_QUESTION_SETTING_COMPULSORY;?></td>
				<td><br />
					
					<input type="radio" name="params[mandatory]" value="0" <?php echo (!$params->get('mandatory'))?"checked":"";?> /><?php echo 'Yes';?>
					<input type="radio" name="params[mandatory]" value="1" <?php echo ($params->get('mandatory'))?"checked":"";?> /><?php echo 'No';?>
				</td>
			</tr>
			
		</table>
		<br />
		<table width="100%" cellpadding="0" cellspacing="0" border="0" id="qfld_tbl" class="<?php echo JLMSCSS::_('jlmslist');?>" style="margin-bottom: 0px;">
			<tr>
				<<?php echo JLMSCSS::tableheadertag();?> width="20" class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center">#</<?php echo JLMSCSS::tableheadertag();?>>
				<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" width="200"><?php echo _JLMS_QUIZ_QUESTION_SETTING_LS_OPTIONS;?></<?php echo JLMSCSS::tableheadertag();?>>
				<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" width="20" align="center">&nbsp;</<?php echo JLMSCSS::tableheadertag();?>>
				<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" width="20" align="center">&nbsp;</<?php echo JLMSCSS::tableheadertag();?>>
				<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" width="20" align="center">&nbsp;</<?php echo JLMSCSS::tableheadertag();?>>
				<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>">&nbsp;</<?php echo JLMSCSS::tableheadertag();?>>
			</tr>
			<?php
			$k = 1; $ii = 1; $ind_last = count($row->scale);
			foreach ($row->scale as $frow) { 
			if($frow->c_type == 0){	?>
				<tr class="<?php echo JLMSCSS::_('sectiontableentry'.$k); ?>">
					<td align="center"><?php echo $ii;?></td>
					<td align="left">
						<?php echo stripslashes($frow->c_field); $str = str_replace('"','&quot;',$frow->c_field);?>
						<input type="hidden" name="jq_hid_fields[]" value="<?php echo $str;?>" />
						<input type="hidden" name="jq_hid_fields_ids[]" value="<?php echo $frow->c_id;?>" />
					</td>
					<td><a href="javascript: void(0);" onclick="javascript:Delete_tbl_row(this); return false;" title="Delete"><img src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_cancel.png" width="16" height="16" border="0" alt="Delete" /></a></td>
					<td><?php if ($ii > 1) { ?><a href="javascript: void(0);" onclick="javascript:Up_tbl_row(this); return false;" title="<?php echo _JLMS_MOVEUP;?>"><img src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_uparrow.png" width="16" height="16" border="0" alt="<?php echo _JLMS_MOVEUP;?>" /></a><?php } ?></td>
					<td><?php if ($ii < $ind_last) { ?><a href="javascript: void(0);" onclick="javascript:Down_tbl_row(this); return false;" title="<?php echo _JLMS_MOVEDOWN;?>"><img src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_downarrow.png" width="16" height="16" border="0" alt="<?php echo _JLMS_MOVEDOWN;?>" /></a><?php } ?></td>
					<td></td>
				</tr>
			<?php
			$k = 3 - $k; $ii ++;
			 }
			} ?>
			</table>
		<br />
		<div style="text-align:left; padding-left:30px ">
			<input id="new_field" class="inputbox" style="width:205px " type="text" name="new_field" />
			<input class="inputbox" type="button" name="add_new_field" value="<?php echo _JLMS_QUIZ_QUIZ_ADDOPTION_BTN;?>" onclick="javascript:Add_new_tbl_field('new_field', 'qfld_tbl', 'jq_hid_fields[]');" />
		</div>
		<br />
		<table width="100%" cellpadding="0" cellspacing="0" border="0" id="qfld_tbl_mark" class="<?php echo JLMSCSS::_('jlmslist');?>" style="margin-bottom: 0px;">
			<tr>
				<<?php echo JLMSCSS::tableheadertag();?> width="20" class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center">#</<?php echo JLMSCSS::tableheadertag();?>>
				<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" width="200"><?php echo _JLMS_QUIZ_QUESTION_SETTING_LS_VALUES;?></<?php echo JLMSCSS::tableheadertag();?>>
				<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" width="20" align="center">&nbsp;</<?php echo JLMSCSS::tableheadertag();?>>
				<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" width="20" align="center">&nbsp;</<?php echo JLMSCSS::tableheadertag();?>>
				<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" width="20" align="center">&nbsp;</<?php echo JLMSCSS::tableheadertag();?>>
				<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>">&nbsp;</<?php echo JLMSCSS::tableheadertag();?>>
			</tr>
			<?php
			$k = 1; $ii = 1; $ind_last = count($row->scale);
			foreach ($row->scale as $frow) { 
			if($frow->c_type == 1){	?>
				<tr class="<?php echo JLMSCSS::_('sectiontableentry'.$k); ?>">
					<td align="center"><?php echo $ii;?></td>
					<td align="left">
						<?php echo stripslashes($frow->c_field); $str = str_replace('"','&quot;',$frow->c_field);?>
						<input type="hidden" name="jq_hid_fields_mark[]" value="<?php echo $str;?>" />
						<input type="hidden" name="jq_hid_fields_mark_ids[]" value="<?php echo $frow->c_id;?>" />
					</td>
					<td><a href="javascript: void(0);" onclick="javascript:Delete_tbl_row(this); return false;" title="Delete"><img src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_cancel.png" width="16" height="16" border="0" alt="Delete" /></a></td>
					<td><?php if ($ii > 1) { ?><a href="javascript: void(0);" onclick="javascript:Up_tbl_row(this); return false;" title="<?php echo _JLMS_MOVEUP;?>"><img src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_uparrow.png" width="16" height="16" border="0" alt="<?php echo _JLMS_MOVEUP;?>" /></a><?php } ?></td>
					<td><?php if ($ii < $ind_last) { ?><a href="javascript: void(0);" onclick="javascript:Down_tbl_row(this); return false;" title="<?php echo _JLMS_MOVEDOWN;?>"><img src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_downarrow.png" width="16" height="16" border="0" alt="<?php echo _JLMS_MOVEDOWN;?>" /></a><?php } ?></td>
					<td></td>
				</tr>
			<?php
			$k = 3 - $k; $ii ++;
			 }
			} ?>
			</table>
		<br />
		<div style="text-align:left; padding-left:30px ">
			<input id="new_field_mark" class="inputbox" style="width:205px " type="text" name="new_field_mark" />
			<input class="inputbox" type="button" name="add_new_field_mark" value="<?php echo _JLMS_QUIZ_QUIZ_ADDOPTION_BTN;?>" onclick="javascript:Add_new_tbl_field('new_field_mark', 'qfld_tbl_mark', 'jq_hid_fields_mark[]');" />
		</div>
		<?php echo $tabs->endTab();
			
					
		echo $tabs->endPane();
		JLMS_TMPL::CloseTS();
		JLMS_TMPL::CloseMT(); ?>
		<input type="hidden" name="params[survey_question]" value="1" />
		<input type="hidden" name="c_point" value="0" />
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
		<input type="hidden" name="task" value="quizzes" />
		<input type="hidden" name="c_type" value="<?php echo $q_om_type;?>" />
		<input type="hidden" name="id" value="<?php echo $course_id;?>" />
		<input type="hidden" name="page" value="save_quiz" />
		<input type="hidden" name="c_id" value="<?php echo $row->c_id;?>" />
		</form>
		<?php JLMS_quiz_admin_html_class::showQuizFooter(); ?>
		<?php
	}

	//Joomla 1.6 ready
	function JQ_editQuest_Boilerplate( &$row, &$lists, $option, $page, $course_id, $q_om_type, &$qtype_str, &$params, $id, $gqp= false, $levels = false ) {
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
	if (pressbutton == 'cancel_quest') {
		form.page.value = pressbutton;
		form.submit();
	}
	if (pressbutton == 'preview_quest') {
		if (form.c_id.value == '0') {
			alert("<?php echo _JLMS_QUIZ_SAVE_QUEST_FIRST;?>");
		} else {
			window.open('index.php?option=<?php echo $option;?>&Itemid=<?php echo $Itemid;?>&task=quizzes&id=<?php echo $course_id;?>&page=view_preview&c_id=<?php echo $row->c_id;?>');
			return;
		}
	}
	// do field validation
	if (form.c_question.value == ""){
		alert( "<?php echo _JLMS_QUIZ_ENTER_QUEST_TEXT;?>" );
	} 
	else { 
		<?php if($gqp) {?>
		if( (form.level_id_0.value == 0) && (pressbutton != 'cancel_quest_gqp')) {
			alert( "<?php echo _JLMS_SELECT_CATEGORY;?>" );
		}
		else {
			form.page.value = pressbutton;
			form.submit();
		}
		<?php } else {?>
			form.page.value = pressbutton;
			form.submit();
		<?php }?>
	}	
}
//-->
</script>
	<?php
	$toolbar = JLMS_quiz_admin_html_class::GetQuestEdit_Toolbar($id, $gqp);
	if($gqp) {
		$multicat = array();
		$i=0;
		
		foreach($lists as $key=>$item){
			if(substr($key, 0, 6) == 'level_'){
				if(isset($lists['level_'.$i])) {				
					$multicat[] = $lists['level_'.$i];
				}	
				$i++;
			}
		}
	}

	$h = $row->c_id?_JLMS_QUIZ_QUEST_EDIT_TITLE:_JLMS_QUIZ_QUEST_NEW_TITLE;

	JLMS_quiz_admin_html_class::showQuizHead( $course_id, $option, $h, true, $toolbar, '', $gqp );?>
		<form action="<?php echo sefRelToAbs("index.php?option=".$option."&amp;Itemid=".$Itemid);?>" method="post" name="adminForm" onsubmit="setgood();">
	<?php
		JLMS_TMPL::OpenMT('jlms_table_no_borders');
		JLMS_TMPL::OpenTS();

		$tabs = new JLMSTabs(0);
		echo $tabs->startPane("JLMS");
		echo $tabs->startTab(_JLMS_QUIZ_E_PARAMS_TAB,"jlmstab1");?>

		<table width="100%" cellpadding="0" cellspacing="0" border="0" class="jlms_item_properties">
		<?php if($gqp) {?>
			<tr>
				<td colspan="2">
				<?php
					JLMS_quiz_admin_html_class::JQ_subCategory($multicat, $levels);				
				?>	
				</td>
			</tr>
		<?php }?>	
			<tr>
				<td align="left" valign="top" colspan="2"><br /><?php echo _JLMS_QUIZ_QUEST_QUEST_TXT;?></td>
			</tr>
			<tr>
				<td colspan="2">
				<?php JLMS_editorArea( 'editor1', $row->c_question, 'c_question', '100%;', '250', '75', '20' ) ; ?>
				</td>
			</tr>
			<tr>
				<td width="20%"><?php echo _JLMS_QUIZ_TBL_QUEST_PUBLISH;?>:</td>
				<td>
					<input type="hidden" name="published" value="<?php echo $row->published; ?>" />
					<input type="checkbox" name="published_chk" onClick="javascript: this.form['published'].value = (this.checked)?1:0;" <?php echo ($row->published == 1)?"checked":""; ?> />
			</tr>
		<?php if(!$gqp) {?>	
			<tr>
				<td width="20%"><br /><?php echo _JLMS_QUIZ_QUEST_QUIZ;?></td><td><?php echo $lists['quiz']; ?></td>
			</tr>
			<tr>
				<td valign="middle" align="left"><br /><?php echo _JLMS_QUIZ_ENTER_CAT;?></td>
				<td><br /><?php echo $lists['jq_categories']; ?></td>
			</tr>
		<?php }?>	
			<tr>
				<td><br /><?php echo _JLMS_QUIZ_QUEST_ATTEMPTS;?></td><td><br /><input class="inputbox" type="text" name="c_attempts" size="50" maxlength="5" value="<?php echo $row->c_attempts; ?>" /></td>
			</tr>
			<tr>
				<td><br /><?php echo _JLMS_QUIZ_QUEST_ORDERING;?></td><td><br /><?php echo $lists['ordering']; ?></td>
			</tr>
		</table>
		<?php
		
		echo $tabs->endPane();
		JLMS_TMPL::CloseTS();
		JLMS_TMPL::CloseMT(); ?>
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
		<input type="hidden" name="task" value="quizzes" />
		<input type="hidden" name="c_type" value="<?php echo $q_om_type;?>" />
		<input type="hidden" name="id" value="<?php echo $course_id;?>" />
		<input type="hidden" name="page" value="save_quiz" />
		<input type="hidden" name="c_id" value="<?php echo $row->c_id;?>" />
		</form>
		<?php JLMS_quiz_admin_html_class::showQuizFooter(); ?>
		<?php
	}	
	
	function JQ_editQuest_Pool_gqp_edit( &$row, &$lists, $option, $page, $course_id, $q_om_type, &$qtype_str ) {
		global $Itemid; ?>
		<script language="javascript" type="text/javascript">
		<!--
		function submitbutton(pressbutton) {
			var form = document.adminForm;
			if (pressbutton == 'cancel_quest') {
				form.page.value = pressbutton;
				form.submit();
			}
			if (pressbutton == 'preview_quest') {
				if (form.c_id.value == '0') {
					alert("<?php echo _JLMS_QUIZ_SAVE_QUEST_FIRST;?>");
				} else {
					window.open('index.php?option=<?php echo $option;?>&Itemid=<?php echo $Itemid;?>&task=quizzes&id=<?php echo $course_id;?>&page=view_preview&c_id=<?php echo $row->c_id;?>');
					return;
				}
			}
			// do field validation
			if (form.c_id.value == ""){
				alert( "<?php echo _JLMS_QUIZ_ENTER_QUEST_TEXT;?>" );
			} 
			else {
				form.page.value = pressbutton;
				form.submit();
			}
		}
		//-->
		</script>
	<?php
	$toolbar = JLMS_quiz_admin_html_class::GetQuestEdit_Toolbar(0, false, true);
	$h = $row->c_id?_JLMS_QUIZ_QUEST_EDIT_TITLE:_JLMS_QUIZ_QUEST_NEW_TITLE;
	JLMS_quiz_admin_html_class::showQuizHead( $course_id, $option, $h, true, $toolbar );?>
		<form action="<?php echo sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid");?>" method="post" name="adminForm">
		<table width="100%" cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td width="20%"><br /><?php echo _JLMS_QUIZ_QUEST_QUIZ;?></td><td><?php echo $lists['quiz']; ?></td>
			</tr>
			<tr>
				<td><br /><?php echo _JLMS_QUIZ_QUEST_ORDERING;?></td><td><br /><?php echo $lists['ordering']; ?></td>
			</tr>
			<tr>
				<td><br />Id:</td><td><br /><input type="text" name="c_pool_gqp" value="<?php echo $row->c_pool_gqp;?>"></td>
			</tr>
			<tr>
				<td colspan="2"><br /><a href="<?php echo sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid&amp;task=quizzes&amp;page=editA_quest_gqp&amp;c_id=$row->c_pool_gqp");?>" target="_blank">Edit this question in GQP</a></td>
			</tr>
		</table>
		<br />
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
		<input type="hidden" name="task" value="quizzes" />
		<input type="hidden" name="c_type" value="<?php echo $q_om_type;?>" />
		<input type="hidden" name="id" value="<?php echo $course_id;?>" />
		<input type="hidden" name="page" value="save_quiz" />
		<input type="hidden" name="c_id" value="<?php echo $row->c_id;?>" />
		</form>
		<?php JLMS_quiz_admin_html_class::showQuizFooter(); ?>
		<?php
	}
	
	
	
	function JQ_editQuest_Pool_GQP($row, $lists, $option, $page, $course_id, $q_om_type, $qtype_str, $rows, $pageNav, $levels ) {
		global $Itemid; 
		
		?>
		<script language="javascript" type="text/javascript">
		<!--
		function submitbutton(pressbutton) {
			var form = document.adminForm;
			
			if (pressbutton == 'cancel_quest') {
				form.page.value = pressbutton;
				form.submit();
			}
			if (pressbutton == 'preview_quest') {
					if (form.c_id.value == '0') {
						alert("<?php echo _JLMS_QUIZ_SAVE_QUEST_FIRST;?>");
					} else {
						window.open('index.php?option=<?php echo $option;?>&Itemid=<?php echo $Itemid;?>&task=quizzes&id=<?php echo $course_id;?>&page=view_preview&c_id=<?php echo $row->c_id;?>');
						return;
					}
			}		
			if (pressbutton == 'save_quest') {
			// do field validation
				if (form.boxchecked.value == 0){
					alert( "<?php echo _JLMS_QUIZ_SELECT_QUESTIONS;?>" );
				} 
				else {
					form.page.value = pressbutton;
					form.submit();
				}
			}
		}
		function jlms_jq_isChecked(isitchecked){
			if (isitchecked == true){
				document.adminForm.boxchecked.value++;
			}
			else {
				document.adminForm.boxchecked.value = document.adminForm.boxchecked.value - 1;
			}
		}
		function checkAllQ( n, fldName ) {
			if (!fldName) {
				fldName = 'cb';
			}
			var f = document.adminForm;
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
				document.adminForm.boxchecked.value = n2;
			} else {
				document.adminForm.boxchecked.value = 0;
			}
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
				if(form['level_id_'+i] != null && form['level_id_'+i].value != old_filters[i]){
					j = i;
				}
				if(i > j){
					if(form['level_id_'+i] != null){
						form['level_id_'+i].value = 0;	
					}
				}
			}
		}
		//-->
		</script>	
		
		<?php
		//FLMS multicat
		$multicat = array();
		$i=0;
		foreach($lists as $key=>$item){
			if(substr($key, 0, 6) == 'level_'){
				$multicat[] = $lists['level_'.$i];
				$i++;
			}
		}
		
		$toolbar = JLMS_quiz_admin_html_class::GetQuestEdit_Toolbar(0, false, true);
		$h = $row->c_id?_JLMS_QUIZ_QUEST_EDIT_TITLE:_JLMS_QUIZ_QUEST_NEW_TITLE;
		JLMS_quiz_admin_html_class::showQuizHead( $course_id, $option, $h, true, $toolbar );?>
		
		<form action="<?php echo sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid");?>" method="post" name="adminForm">
				<table width="100%" cellpadding="0" cellspacing="0" border="0">
					<tr>
						<td width="20%" valign="middle" ><?php echo _JLMS_QUIZ_QUEST_QUIZ;?></td><td><?php echo $lists['quiz']; ?></td>
					</tr>
					
					<tr>
						<td  valign="middle" >
							<?php echo _JLMS_QUIZ_TBL_QUEST_PUBLISH;?>:
						</td>
						<td>
							<?php echo $lists['published'];?>
						</td>
					</tr>
						
					<tr>
						<td colspan="2" class="contentheading">
						<?php echo _JLMS_QUIZ_SELECT_QUEST_FROM_POOL;?>
						</td>
					</tr>
				</table>
				<table width="100%" cellpadding="0" cellspacing="0" border="0">
					<tr>
						<td>
							<?php 
								$link_page = "index.php?option=$option&amp;Itemid=$Itemid&amp;task=quizzes&amp;id=$course_id&amp;page=add_quest&amp;c_type=21";
								$link_page .= $lists['qtype_id'] ? "&amp;qtype_id=".$lists['qtype_id'] : '';
								$i=0;
								$data = get_object_vars($lists['data']);
								foreach($data as $key=>$value){
									if(substr($key, 0, 6) == 'level_' && $value){
										$link_page .= "&amp;level_id_".$i."=".$value;
										$i++;		
									}
								}
								echo _JLMS_PN_DISPLAY_NUM . $pageNav->getLimitBox( $link_page ) . $pageNav->getPagesCounter();
								echo '<br />';
								echo $lists['qtype'];
							?>		
						</td>	
						<td align="right">						
							<table border="0">
							<?php	
								for($i=0;$i<count($multicat);$i++){
									JLMS_TMPL::OpenTS('', ' align="right" style="text-align:right " width="100%"');
										echo ((isset($levels[$i]->cat_name) && $levels[$i]->cat_name != '')?$levels[$i]->cat_name:_JLMS_COURSES_COURSES_GROUPS)." ".$lists['level_'.$i]."";
									JLMS_TMPL::CloseTS();
								}
							?>
							</table>
						</td>
					</tr>
					<tr>
						<td colspan="2">							
						
							<table width="100%" cellpadding="0" cellspacing="0" border="0">
									<tr>
										<td width="20" class="sectiontableheader" align="center">#</td>
										<td width="20" class="sectiontableheader" align="center"><input type="checkbox" name="toggle" value="" onclick="checkAllQ(<?php echo count($rows); ?>);" /></td>
										<td class="sectiontableheader"><?php echo _JLMS_QUIZ_TBL_QUEST_TEXT;?></td>
								<?php if (isset($lists['filtered_quiz']) && $lists['filtered_quiz']) { ?>
										<td class="sectiontableheader" width="1%"><?php echo _JLMS_QUIZ_TBL_QUEST_PUBLISH;?></td>
										<td class="sectiontableheader" colspan="2" width="1%"><?php echo _JLMS_QUIZ_TBL_QUEST_REORDER;?></td>
										<td class="sectiontableheader" width="1%"><?php echo 'Order';?></td>
										<td class="sectiontableheader" width="1%"><a href="javascript:submitbutton_allorder(<?php echo count( $rows )-1;?>)"><img width="16" height="16" border="0" alt="Save Order" src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/filesave.png"/></a></td>
								<?php } ?>
										<td class="sectiontableheader"><?php echo _JLMS_QUIZ_TBL_QUEST_TYPE;?></td>
										<td class="sectiontableheader"><?php echo _JLMS_QUIZ_TBL_Q_CAT;?></td>
										<td class="sectiontableheader" width="20">ID</td>
									</tr>
								<?php
								$k = 1;
								for ($i=0, $n=count($rows); $i < $n; $i++) {
									$row = $rows[$i];
									
									$quiz_task = 'setup_quest';
									
									$img_published	= $row->published ? 'btn_accept.png' : 'btn_cancel.png';
									$task_published	= $row->published ? 'unpublish_quest' : 'publish_quest';
									$alt_published 	= $row->published ? _JLMS_STATUS_PUB : _JLMS_STATUS_UNPUB;
									$state 			= $row->published ? 0 : 1;
										
									$checked = '<input type="checkbox" id="cb'.$i.'" name="cid[]" value="'.$row->c_id.'" onclick="jlms_jq_isChecked(this.checked);" />';
									/*mosHTML::idBox( $i, $row->c_id);*/
									?>
									<tr class="<?php echo "sectiontableentry$k"; ?>">
										<td align="center"><?php echo  $pageNav->limitstart + $i + 1;?></td>
										<td><?php echo $checked; ?></td>
										<td align="left">
											<?php
											mosMakeHtmlSafe( $row->c_question );
											?>
											<?php 
//											$link = 'index.php?option='.$option.'&task=quizzes&page=editA_quest_gqp&c_id='.$row->c_id; //not use
											if (isset($row->right_answer) && $row->right_answer) {
												if ($row->c_question) {
													$quest_desc = $row->c_question.'<br />';
												}
												$quest_desc .= '<span class="tip-title-inner">'._JLMS_QUIZ_CORRECT_ANSWER . '</span> ' . $row->right_answer;
											} else {
												$quest_desc = $row->c_question;
											}
											//$quest_desc = isset($row->right_answer) && $row->right_answer ? (_JLMS_QUIZ_CORRECT_ANSWER . ' ' . $row->right_answer) : $row->c_question;
											$link = '#';
											echo JLMS_toolTip(substr(strip_tags($row->c_question), 0, 70), $quest_desc, '', $link);
											?>
										</td>
								<?php if (isset($lists['filtered_quiz']) && $lists['filtered_quiz']) { ?>
										<td valign="middle" align="center"><?php echo JLMS_quiz_admin_html_class::QuizPublishIcon( $row->c_id, $id, $state, $task_published, $alt_published, $img_published, $option); ?></td>
										<td valign="middle" align="center"><?php echo JLMS_orderUpIcon($i, $row->c_id, true, 'quest_orderup');?></td>
										<td valign="middle" align="center"><?php echo JLMS_orderDownIcon($i, $n, $row->c_id, true, 'quest_orderdown');?></td>
										<td colspan="2">
											<input type="text" name="order[]" size="5" value="<?php echo $row->ordering; ?>" class="inputbox" style="text-align: center" />
										</td>
								<?php } ?>
										<td align="left">
											<?php echo $row->qtype_full; ?>
										</td>
										<td align="left">
											<?php echo $row->c_category?$row->c_category:'&nbsp;'; ?>
										</td>
										<td><?php echo $row->c_id;?></td>
									</tr>
									<?php
									$k = 3 - $k;
								}
								?>
								</table>
								<table width="100%">
									<tr>
										<td align="center"><div align="center">
										<?php 
										echo $pageNav->writePagesLinks( $link_page ); ?> 
										</div>
										</td>
									</tr>
								</table>						
						
						</td>
					</tr>
					</table>
				<input type="hidden" name="option" value="<?php echo $option;?>" />
				<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
				<input type="hidden" name="task" value="quizzes" />
				<input type="hidden" name="id" value="<?php echo $course_id;?>" />
				<input type="hidden" name="page" value="editA_quest_gqp" />
				<input type="hidden" name="c_type" value="<?php echo $q_om_type;?>" />
				<input type="hidden" name="boxchecked" value="0" />
		</form>
		<?php JLMS_quiz_admin_html_class::showQuizFooter(); ?>
		<?php
	}
	
	/* 		function for 'Pool' questions		*/
	function JQ_editQuest_Pool( &$row, &$lists, $option, $page, $course_id, $q_om_type, &$qtype_str ) {
		global $Itemid; ?>
		<script language="javascript" type="text/javascript">
		<!--
		function submitbutton(pressbutton) {
			var form = document.adminForm;
			if (pressbutton == 'cancel_quest') {
				form.page.value = pressbutton;
				form.submit();
			}
			if (pressbutton == 'preview_quest') {
				if (form.c_id.value == '0') {
					alert("<?php echo _JLMS_QUIZ_SAVE_QUEST_FIRST;?>");
				} else {
					window.open('index.php?option=<?php echo $option;?>&Itemid=<?php echo $Itemid;?>&task=quizzes&id=<?php echo $course_id;?>&page=view_preview&c_id=<?php echo $row->c_id;?>');
					return;
				}
			}
			// do field validation
			if (form.c_pool.value == ""){
				alert( "<?php echo _JLMS_QUIZ_ENTER_QUEST_TEXT;?>" );
			} 
			else {
				form.page.value = pressbutton;
				form.submit();
			}
		}
		//-->
		</script>
	<?php
	$toolbar = JLMS_quiz_admin_html_class::GetQuestEdit_Toolbar();
	$h = $row->c_id?_JLMS_QUIZ_QUEST_EDIT_TITLE:_JLMS_QUIZ_QUEST_NEW_TITLE;
	JLMS_quiz_admin_html_class::showQuizHead( $course_id, $option, $h, true, $toolbar );?>
		<form action="<?php echo sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid");?>" method="post" name="adminForm">
		<table width="100%" cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td width="20%"><br /><?php echo _JLMS_QUIZ_QUEST_QUIZ;?></td><td><?php echo $lists['quiz']; ?></td>
			</tr>
			<tr>
				<td><br /><?php echo _JLMS_QUIZ_SELECT_QUEST_FROM_POOL;?></td><td><br /><?php echo $lists['pool_quests']; ?></td>
			</tr>

			<tr>
				<td><br /><?php echo _JLMS_QUIZ_QUEST_ORDERING;?></td><td><br /><?php echo $lists['ordering']; ?></td>
			</tr>
		</table>
		<br />
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
		<input type="hidden" name="task" value="quizzes" />
		<input type="hidden" name="c_type" value="<?php echo $q_om_type;?>" />
		<input type="hidden" name="id" value="<?php echo $course_id;?>" />
		<input type="hidden" name="page" value="save_quiz" />
		<input type="hidden" name="c_id" value="<?php echo $row->c_id;?>" />
		</form>
		<?php JLMS_quiz_admin_html_class::showQuizFooter(); ?>
		<?php
	}

	//Joomla 1.6 ready
	function JQ_moveQuest_Select( $option, $page, $course_id, $cid, $QuizList, $items, $gqp = '', $levels = array(), $lists ) {
		global $Itemid;
		
		if(!$gqp) {
			$ttt = 'move_quest_save';
			$ttt1 = _JLMS_QUIZ_MOVE_QUEST_BTN;
			if ($page == 'copy_quest_sel') { $ttt = 'copy_quest_save'; $ttt1 = _JLMS_QUIZ_COPY_QUEST_BTN; }
			$toolbar = array();
			$toolbar[] = array('btn_type' => 'save', 'btn_txt' => $ttt1, 'btn_js' => "javascript:submitbutton('".$ttt."');");
			$toolbar[] = array('btn_type' => 'cancel', 'btn_txt' => _JLMS_QUIZ_CANCEL_BTN, 'btn_js' => "javascript:submitbutton('cancel_quest');");
			$h = ($page == 'move_quest_sel') ? _JLMS_QUIZ_MOVE_QUEST_TITLE : _JLMS_QUIZ_COPY_QUEST_TITLE ;
		}
		else {
			$ttt = 'move_quest_save_gqp';
			$ttt1 = _JLMS_QUIZ_MOVE_QUEST_BTN;
			if ($page == 'copy_quest_sel_gqp') { $ttt = 'copy_quest_save_gqp'; $ttt1 = _JLMS_QUIZ_COPY_QUEST_BTN; }
			$toolbar = array();
			$toolbar[] = array('btn_type' => 'save', 'btn_txt' => $ttt1, 'btn_js' => "javascript:submitbutton('".$ttt."');");
			$toolbar[] = array('btn_type' => 'cancel', 'btn_txt' => _JLMS_QUIZ_CANCEL_BTN, 'btn_js' => "javascript:submitbutton('cancel_quest_gqp');");
			$h = ($page == 'move_quest_sel_gqp') ? _JLMS_QUIZ_MOVE_QUEST_TITLE : _JLMS_QUIZ_COPY_QUEST_TITLE ;
		}
		
		JLMS_quiz_admin_html_class::showQuizHead( $course_id, $option, $h, true, $toolbar, '', $gqp );
		
		//FLMS multicat
		$multicat = array();
		if ($gqp) {
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
		<!--
		function submitbutton(pressbutton) {
			var form = document.adminForm;
			
			<?php if(!$gqp) {?>
			if (pressbutton == 'cancel_quest') {
			<?php }
			else {?>
			if (pressbutton == 'cancel_quest_gqp') {
			<?php }?>	
				form.page.value = pressbutton;
				form.submit();
				return;
			}
			// do field validation
			<?php if(!$gqp) {?>
			if (form.quizmove.value == '0'){
			<?php }
			else {?>	
			if (form.filter_id_0.value == '0'){
			<?php }?>
				alert( "<?php echo _JLMS_QUIZ_QUEST_COPY_MOVE_ALERT;?>" );
			} else {
				form.page.value = pressbutton;
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
		//-->
		</script>
		<form action="<?php echo sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid");?>" method="post" name="adminForm">
		<table width="100%" cellpadding="0" cellspacing="0" border="0" class="jlms_table_no_borders">
		
			<?php 
			if(!$gqp) {
			?>
				<tr>
					<td align="left" valign="top">
						<?php echo '<strong>'._JLMS_QUIZ_QUEST_COPY_MOVE_TO.'</strong>'. $QuizList .'<br />'. _JLMS_QUIZ_QUEST_COPY_MOVE_TIP; ?>
					</td>
				</tr>
				
			<?php
			} else {
			?>	
				<tr>
					<td align="left" valign="top">
					<?php 
					if(count($multicat)){
					?>
						<table border="0" class="jlms_table_no_borders">
						<?php	
							for($i=0;$i<count($multicat);$i++){
								$num = $i + 1;
								?>
								<tr>
									<td align="right" style="text-align:right;" width="10%" nowrap="nowrap">
										<?php
										echo (isset($levels[$i]->cat_name) && $levels[$i]->cat_name != '') ? $levels[$i]->cat_name : 'Level #'.$num;
										?>
									</td>
									<td align="left" style="text-align:left;" nowrap="nowrap">
										<?php
										echo $lists['filter_'.$i];
										?>
									</td>
								</tr>
								<?php
							}
						?>
						</table>
					<?php	
					}
					?>	
					</td>
				</tr>
			<?php 
			}
			?>	
			<tr>
				<td>
					<table width="100%" cellpadding="0" cellspacing="0" border="0" class="<?php echo JLMSCSS::_('jlmslist');?>">
					<tr>
						<<?php echo JLMSCSS::tableheadertag();?> width="20" class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center">#</<?php echo JLMSCSS::tableheadertag();?>>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="left"><?php echo _JLMS_QUIZ_QUEST_COPY_MOVE_HEADER;?></<?php echo JLMSCSS::tableheadertag();?>>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="left"><?php if($gqp) echo _JLMS_QUIZ_TBL_Q_CAT; else echo _JLMS_QUIZ_QUEST_COPY_MOVE_FROM;?></<?php echo JLMSCSS::tableheadertag();?>>
					</tr>
					<?php
					$k = 1; $i = 1;
					foreach ( $items as $item ) { ?>
						<tr class="<?php echo JLMSCSS::_('sectiontableentry'.$k); ?>">
							<td align="center"><?php echo $i; ?></td>
							<td align="left">
									<?php echo substr(strip_tags($item->c_question), 0, 100);?>
							</td>
							<td align="left"><?php echo $item->quiz_name;?></td>
						</tr>
						<?php
						$k = 3 - $k;
						$i ++;
					}?>
					</table>
				</td>
			</tr>
		</table>
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
		<input type="hidden" name="task" value="quizzes" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="id" value="<?php echo $course_id;?>" />
		<input type="hidden" name="page" value="move_quest_sel" />
		<?php
		foreach ( $cid as $id ) {
			echo "\n <input type=\"hidden\" name=\"cid[]\" value=\"$id\" />";
		}
		?>
		</form>
		<?php JLMS_quiz_admin_html_class::showQuizFooter(); ?>
		<?php
	}

//------------------------ Quiz Chart----------------/////////
 function JQ_showBars($rows, &$lists, $course_id, $option, $gqp = false, $levels = array(), $pageNav)
 {
 	
 	global $JLMS_CONFIG, $Itemid;
 	echo '<form action="'.$JLMS_CONFIG->get('live_site')."/index.php?option=$option&amp;Itemid=$Itemid".'" method="post" name="adminFormQ">';
 	$toolbar = array();
 	
	//FLMS multicat
	$multicat = array();
	if ($gqp) {
		$multicat = array();
		$i=0;
		foreach($lists as $key=>$item){
			if(substr($key, 0, 7) == 'filter_'){
				$multicat[] = $lists['filter_'.$i];
				$i++;
			}
		}
	}
 	
 	JLMS_quiz_admin_html_class::showQuizHead( $course_id, $option, $gqp?(_JLMS_QUIZ_REPORTS_TITLE_GQP):(_JLMS_QUIZ_REPORTS_TITLE), true, $toolbar,'', $gqp );
 	?>
 	
<script language="javascript" type="text/javascript">
<!--
var form = document.adminFormQ;
var old_filters = new Array();
function read_filter(){
	var form = document.adminFormQ;
	var count_levels = '<?php echo count($levels);?>';
	for(var i=0;i<parseInt(count_levels);i++){
		if(form['filter_id_'+i] != null){
			old_filters[i] = form['filter_id_'+i].value;
		}
	}
}
function write_filter(){
	var form = document.adminFormQ;
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
//-->
</script>
<?php
$pageNav->writePageNavJS('adminFormQ');
?>
 	<table width="100%" cellpadding="0" cellspacing="0" border="0">
		<tr>
			<td align="center" style="text-align:center ">
				<div align="center" style="white-space:nowrap ">
					<?php 
					if(!$gqp) {
						echo _JLMS_QUIZ_FILTER_BY.'&nbsp;&nbsp;' . $lists['quiz'] . $lists['showtype'] . $lists['showgroups'];
					} else {
						$opened = false;
						if(count($multicat)){
							$opened = true;
							JLMS_TMPL::OpenMT();
							for($i=0;$i<count($multicat);$i++){
								JLMS_TMPL::OpenTS('', ' align="right" style="text-align:right " width="100%"');
									echo ((isset($levels[$i]->cat_name) && $levels[$i]->cat_name != '')?$levels[$i]->cat_name:_JLMS_COURSES_COURSES_GROUPS)." ".$lists['filter_'.$i]."&nbsp;&nbsp;";
								JLMS_TMPL::CloseTS();
							}
						}
						if ($JLMS_CONFIG->get('use_global_groups', 1)) {
							if (!$opened) {
								$opened = true;
								JLMS_TMPL::OpenMT();
							}
							JLMS_TMPL::OpenTS('', ' align="right" style="text-align:right " width="100%"');
								echo _JLMS_QUIZ_FILTER_BY.'&nbsp;&nbsp;' . $lists['showgroups']."&nbsp;&nbsp;";		
							JLMS_TMPL::CloseTS();
						}
						if ($opened) {
							JLMS_TMPL::CloseMT();
						}					
					}
					?>
				</div>
			</td>
		</tr>
	</table>
	<br />
	<table width="100%" align="center" cellpadding="0" cellspacing="0" border="0">
		<?php	
		foreach($rows as $i=>$row){
	 		if(!isset($row->is_survey) && !isset($row->images_array->is_survey)){
		 		if(isset($row->images_array->graph)){
		 			$num_quest = $i + 1 + $pageNav->limitstart;
				 	?>
				 	<tr>
				 		<td>
				 			<?php echo _JLMS_QUIZ_QUESTION_NUM.' '.$num_quest;?>: <b><?php echo $row->title_array->graph;?></b>
				 		</td>
				 	</tr>
				 	<tr>
						<td align="center">	
				 			<img src="<?php echo $JLMS_CONFIG->get('live_site')."/".$row->images_array->graph;?>" width="<?php echo '600';?>" height="<?php echo $row->count_array->graph?($row->count_array->graph*200):200?>" alt="<?php echo $row->title_array->graph;?>" title="<?php echo $row->title_array->graph;?>" border='0' />
				 		</td>
					</tr>
				<?php
		 		}
		 		if(isset($row->images_array->correct)){
		 		?>	
					<tr>
				 		<td>
				 			<?php
				 				#echo _JLMS_QUIZ_VIEW_STATISTICS_CI; //(Max): nuna pridumat name for constant
		 					?>
				 		</td>
				 	</tr>
					<tr>
						<td align="center">	
				 			<img src="<?php echo $JLMS_CONFIG->get('live_site')."/".$row->images_array->correct;?>" width="<?php echo '600';?>" height="<?php echo $row->count_array->correct?($row->count_array->correct*200):200?>" alt="<?php echo $row->title_array->correct;?>" title="<?php echo $row->title_array->correct;?>" border='0' />
				 		</td>
					</tr>
				 	<?php
		 		}
	 		} else {
	 			$num_quest = $i + 1;
	 			?>
	 			<tr>
				 	<td>
			 			<?php echo _JLMS_QUIZ_QUESTION_NUM.' '.$num_quest;?>: <b><?php echo $row->title_array->quest_survey;?></b>
			 		</td>
			 	</tr>
				<tr>
					<td align="center">	
			 			<a href="<?php echo $row->title_array->link_survey;?>" title="<?php echo $row->title_array->quest_survey;?>">
			 				<?php echo _JLMS_QUIZ_VIEW_ANSWERS;?>
			 			</a>
			 		</td>
				</tr>
	 			<?php	
	 		}
		}
		?>
		<tr>
			<td align="center"><div align="center">
			<?php echo $pageNav->writePagesLinksJS(); ?>
			</div></td>
		</tr>
	</table>
	<?php JLMS_quiz_admin_html_class::showQuizFooter(); ?>
	<input type="hidden" name="option" value="<?php echo $option?>" />
	<input type="hidden" name="task" value="quizzes" />
	<input type="hidden" name="page" value="<?php echo $gqp ? 'quiz_bars_gqp' : 'quiz_bars';?>" />
	<input type="hidden" name="limitstart" value="" />
	<input type="hidden" name="id" value="<?php echo $course_id?>" />
	</form>
	<?php			
 }

 function JQ_ShowAnswersSurvey($lists, $rows, $course_id, $quiz_id, $quest_id, $question, $link_back, $option){
 	global $JLMS_CONFIG, $Itemid;
 	echo '<form action="'.$JLMS_CONFIG->get('live_site')."/index.php?option=$option&amp;Itemid=$Itemid".'" method="post" name="adminFormQ">';
 	$toolbar = array();
 	JLMS_quiz_admin_html_class::showQuizHead( $course_id, $option, _JLMS_QUIZ_REPORTS_TITLE, true, $toolbar );
 	?>
 	<table width="100%" cellpadding="0" cellspacing="0" border="0">
		<tr>
			<td>
				<a href="<?php echo $link_back;?>" title="<?php echo _JLMS_TOOLBAR_BACK; ?>">
					<?php echo _JLMS_TOOLBAR_BACK; ?>
				</a>
			</td>
			<td align="center" style="text-align:center ">
				<div align="right" style="white-space:nowrap ">
					<?php echo _JLMS_QUIZ_FILTER_BY . '&nbsp;&nbsp;' . $lists['showgroups'] . '&nbsp;&nbsp;';?>
				</div>
			</td>
		</tr>
	</table>
	<table width="100%" align="center" cellpadding="0" cellspacing="0" border="0">
		<tr>
			<td colspan="2">
				<?php echo _JLMS_QUIZ_QUESTION;?>&nbsp;<b><?php echo $question;?></b>
			</td>
		</tr>
		<?php
		$k=1;
		for($i=0;$i<count($rows);$i++){
			$num = $i + 1;
		?>
		<tr class="sectiontableentry<?php echo $k;?>">
			<td width="5%">
				# <?php echo $num;?>
			</td>
			<td>
				<?php echo $rows[$i]->c_answer;?>
			</td>
		</tr>
		<?php
			$k = 3 - $k;
		}
		?>
	</table>

	<?php JLMS_quiz_admin_html_class::showQuizFooter(); ?>

	<input type="hidden" name="option" value="<?php echo $option?>" />
	<input type="hidden" name="task" value="quizzes" />
	<input type="hidden" name="page" value="view_answ_survey" />
	<input type="hidden" name="id" value="<?php echo $course_id?>" />
	<input type="hidden" name="quiz_id" value="<?php echo $quiz_id?>" />
	<input type="hidden" name="quest_id" value="<?php echo $quest_id?>" />
	</form>
	<?php
	}

	//Joomla 1.6 optimized
	function JQ_view_quizReport( &$rows, &$pageNav, $option, $page, $course_id, &$lists ) {
		global $Itemid, $JLMS_CONFIG;

		$toolbar = array();
		$toolbar[] = array('btn_type' => 'print', 'btn_txt' => _JLMS_QUIZ_CSV_REPORT_BTN, 'btn_js' => "javascript:submitbutton('csv_report');");
		$toolbar[] = array('btn_type' => 'del', 'btn_txt' => _JLMS_QUIZ_DEL_REPORT_BTN, 'btn_js' => "javascript:submitbutton('del_report');");
		JLMS_quiz_admin_html_class::showQuizHead( $course_id, $option, _JLMS_QUIZ_REPORTS_TITLE, true, $toolbar );
		?>
<script language="javascript" type="text/javascript">
<!--
function submitbutton(pressbutton) {
	var form = document.adminForm;
	if ( (pressbutton == 'del_report') && (form.boxchecked.value == "0")) {
		alert('<?php echo _JLMS_ALERT_SELECT_ITEM;?>');
	} else {
		form.page.value = pressbutton;
		form.submit();
	}
}
function submitview(pressbutton){
	var form = document.adminForm;
	if(form.user_id.value == 0){
		alert('Please select User');
	} else {
		form.view.value = pressbutton;
		form.submit();
	}
}
//-->
</script>
		<form action="<?php echo sefRelToAbs("index.php?option=$option&Itemid=$Itemid");?>" method="post" name="adminForm">
		<table width="100%" cellpadding="0" cellspacing="0" border="0" class="jlms_table_no_borders">
			<tr>
				<td>
				<table width="100%" cellpadding="0" cellspacing="0" border="0" class="jlms_table_no_borders">
					<tr>
						<td align="right" style="text-align:right">
							<div align="right" style="white-space:nowrap">
							<?php echo _JLMS_QUIZ_FILTER_BY.'&nbsp;&nbsp;'.$lists['quiz'].',&nbsp;'.$lists['user'];?>
							</div>
						</td>
					</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td width="100%">
				<?php $reports_colspan = 10; ?>
				<table width="100%" cellpadding="0" cellspacing="0" border="0" class="<?php echo JLMSCSS::_('jlmslist');?>" style="margin-top:0px; padding-top:0px; margin-bottom:0px; padding-bottom:0px;">
					<tr>
						<<?php echo JLMSCSS::tableheadertag();?> width="20" class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center">#</<?php echo JLMSCSS::tableheadertag();?>>
						<<?php echo JLMSCSS::tableheadertag();?> width="20" class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></<?php echo JLMSCSS::tableheadertag();?>>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>"><?php echo _JLMS_QUIZ_TBL_DATE_TIME;?></<?php echo JLMSCSS::tableheadertag();?>>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>"><?php echo _JLMS_QUIZ_TBL_STUDENT;?></<?php echo JLMSCSS::tableheadertag();?>>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>"><?php echo _JLMS_QUIZ_TBL_QUIZ;?></<?php echo JLMSCSS::tableheadertag();?>>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>"><?php echo _JLMS_QUIZ_TBL_USER_SCORE;?></<?php echo JLMSCSS::tableheadertag();?>>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>"><?php echo _JLMS_QUIZ_TBL_TOTAL_SCORE;?></<?php echo JLMSCSS::tableheadertag();?>>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>"><?php echo _JLMS_QUIZ_TBL_PASS_SCORE;?></<?php echo JLMSCSS::tableheadertag();?>>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>"><?php echo _JLMS_QUIZ_TBL_PASSED;?></<?php echo JLMSCSS::tableheadertag();?>>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>"><?php echo _JLMS_QUIZ_TBL_SPEND_TIME;?></<?php echo JLMSCSS::tableheadertag();?>>
					</tr>
				<?php
				$k = 1;
				$p_img = $JLMS_CONFIG->get('live_site').'/components/com_joomla_lms/lms_images/toolbar/btn_accept.png';
				$f_img = $JLMS_CONFIG->get('live_site').'/components/com_joomla_lms/lms_images/toolbar/btn_cancel.png';
				for ($i=0, $n=count($rows); $i < $n; $i++) {
					$row = $rows[$i];
					$link 	= sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid&amp;task=quizzes&amp;id=$course_id&amp;page=stu_reportA&c_id=". $row->c_id);
					$checked = mosHTML::idBox( $i, $row->c_id);
					if (!$row->c_student_id) $row->username = "Anonymous";
					if (!$row->username) $row->username = "User not found";
					if (!$row->c_title) $row->c_title = "Quiz not found";
					?>
					<tr class="<?php echo JLMSCSS::_('sectiontableentry'.$k); ?>">
						<td align="center"><?php echo $pageNav->limitstart + $i + 1; ?></td>
						<td><?php echo $checked; ?></td>
						<td align="left">
							<a href="<?php echo $link; ?>">
							<?php echo JLMS_dateToDisplay(strtotime($row->c_date_time), true, $JLMS_CONFIG->get('offset')*60*60, ' H:i:s'); ?>
							</a>
						</td>
						<td align="left">
							<?php echo $row->username.'<br />'.$row->name; ?>
						</td>
						<td align="left">
							<?php echo $row->c_title; ?>
						</td>
						<td align="left">
							<?php echo $row->c_total_score; ?>
						</td>
						<td align="left">
							<?php echo $row->c_full_score.( in_array($row->cur_quiz_id, $lists['pool_quizzes']) ? '+' : '' ); ?>
						</td>
						<td align="left">
							<?php
							
							if ($JLMS_CONFIG->get('global_quest_pool')) {
								$passed_score = ceil(($row->c_full_score * $row->c_passing_score) / 100) . ( in_array($row->cur_quiz_id, $lists['pool_quizzes_gqp']) ? '+' : '' );
							}
							else {
								$passed_score = ceil(($row->c_full_score * $row->c_passing_score) / 100) . ( in_array($row->cur_quiz_id, $lists['pool_quizzes']) ? '+' : '' ) ;
							}
							
							echo $passed_score . (strlen($row->c_passing_score)?(" (".$row->c_passing_score."%)"):'');
							?>
						</td>
						<td align="left">
							<img src="<?php echo $row->c_passed?$p_img:$f_img;?>" width="16" height="16" border="0" alt="<?php echo $row->c_passed?'passed':'failed';?>" />
						</td>
						<td align="left">
							<?php
							$tot_min = floor($row->c_total_time / 60);
							$tot_sec = $row->c_total_time - $tot_min*60;
							echo str_pad($tot_min,2, "0", STR_PAD_LEFT).":".str_pad($tot_sec,2, "0", STR_PAD_LEFT);
							?>
						</td>
					</tr>
					<?php
					$k = 3 - $k;
				}?>
				<tr>
					<td align="center" colspan="<?php echo $reports_colspan;?>" class="<?php echo JLMSCSS::_('jlmslist-footer_td');?>">
						<div align="center" style="white-space: nowrap;">
						<?php
							$link = "index.php?option=$option&amp;Itemid=$Itemid&amp;task=quizzes&amp;id=$course_id&amp;page=$page";
							echo _JLMS_PN_DISPLAY_NUM . '&nbsp;' . $pageNav->getLimitBox( $link ) . '&nbsp;' . $pageNav->getPagesCounter();
							echo '<br />';
							echo $pageNav->writePagesLinks( $link );
						?> 
						</div>
					</td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td align="center">
					<?php
						$controls = array();
						$controls[] = array('href' => "javascript:submitview('xls');", 'title' => 'XLS', 'img' => 'xls');
						JLMS_TMPL::ShowControlsFooterC($controls, '', false, false, _JLMS_EXPORT_TO.':');
					?>	
				</td>
			</tr>
		</table>
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
		<input type="hidden" name="task" value="quizzes" />
		<input type="hidden" name="id" value="<?php echo $course_id;?>" />
		<input type="hidden" name="page" value="<?php echo $page;?>" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="view" value="" />
		</form>
		<?php JLMS_quiz_admin_html_class::showQuizFooter(); ?>
<?php
	}

	//Joomla 1.6 optimized
	function JQ_view_stuReport( &$rows, &$pageNav, $option, $page, $course_id, $id ) {
		global $Itemid;
		$toolbar = array();
		$toolbar[] = array('btn_type' => 'del', 'btn_txt' => _JLMS_QUIZ_DEL_QUEST_REPORT_BTN, 'btn_js' => "javascript:submitbutton('del_stu_report');");
		$toolbar[] = array('btn_type' => 'back', 'btn_txt' => _JLMS_QUIZ_BACK_BTN, 'btn_js' => sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=quizzes&id=$course_id&page=reports"));
		JLMS_quiz_admin_html_class::showQuizHead( $course_id, $option, _JLMS_QUIZ_REPORT_TITLE, true, $toolbar );
		?>
<script language="javascript" type="text/javascript">
<!--
function submitbutton(pressbutton) {
	var form = document.adminForm;
	if ( (pressbutton == 'del_stu_report') && (form.boxchecked.value == "0")) {
		alert('<?php echo _JLMS_ALERT_SELECT_ITEM;?>');
	} else {
		form.page.value = pressbutton;
		form.submit();
	}
}
//-->
</script>
		<form action="<?php echo sefRelToAbs("index.php?option=$option&Itemid=$Itemid");?>" method="post" name="adminForm">
		<table width="100%" cellpadding="0" cellspacing="0" border="0" class="<?php echo JLMSCSS::_('jlmslist');?>">
			<tr>
				<<?php echo JLMSCSS::tableheadertag();?> width="20" class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center">#</<?php echo JLMSCSS::tableheadertag();?>>
				<<?php echo JLMSCSS::tableheadertag();?> width="20" class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></<?php echo JLMSCSS::tableheadertag();?>>
				<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>">Question</<?php echo JLMSCSS::tableheadertag();?>>
				<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>">Type</<?php echo JLMSCSS::tableheadertag();?>>
				<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>">Points</<?php echo JLMSCSS::tableheadertag();?>>
				<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" nowrap="nowrap">User score</<?php echo JLMSCSS::tableheadertag();?>>
			</tr>
		<?php
		$k = 1;$tot = 0; $usr = 0;
		for ($i=0, $n=count($rows); $i < $n; $i++) {
			$row = $rows[$i];
			$tot = $tot + $row->c_point;
			$usr = $usr + $row->c_score;
			$link 	= sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid&amp;task=quizzes&amp;id=$course_id&amp;page=quest_reportA&c_id=". $row->c_id."&amp;stu_id=".$id);
			
			$checked = mosHTML::idBox( $i, $row->c_id);
			?>
			<tr class="<?php echo JLMSCSS::_('sectiontableentry'.$k); ?>">
				<td align="center" valign="middle" style="text-align:center; vertical-align:middle "><?php echo $pageNav->limitstart + $i + 1;; ?></td>
				<td align="center" valign="middle" style="text-align:center; vertical-align:middle "><?php echo $checked; ?></td>
				<td align="left" valign="middle" style="text-align:left; vertical-align:middle ">
					<?php
					if (!$row->c_question) {
						echo "Question not found";
					} else { ?>
					<a href="<?php echo $link; ?>">
					<?php echo substr(strip_tags($row->c_question),0,100); ?>
					</a>
					<?php } ?>
				</td>
				<td align="left" valign="middle" style="text-align:left; vertical-align:middle ">
					<?php echo $row->c_qtype; ?>
				</td>
				<td align="left" valign="middle" style="text-align:left; vertical-align:middle ">
					<?php echo $row->c_point; ?>
				</td>
				<td align="left" valign="middle" style="text-align:left; vertical-align:middle ">
					<?php echo $row->c_score; ?>
				</td>
			</tr>
			<?php
			$k = 3 - $k;
		}
		if (!empty($rows)) { ?>
			<tr>
				<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>">&nbsp;</<?php echo JLMSCSS::tableheadertag();?>>
				<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>">&nbsp;</<?php echo JLMSCSS::tableheadertag();?>>
				<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>">&nbsp;</<?php echo JLMSCSS::tableheadertag();?>>
				<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>">&nbsp;</<?php echo JLMSCSS::tableheadertag();?>>
				<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="left" style="text-align:left; vertical-align:middle"><?php echo $tot;?></<?php echo JLMSCSS::tableheadertag();?>>
				<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="left" style="text-align:left; vertical-align:middle"><?php echo $usr;?></<?php echo JLMSCSS::tableheadertag();?>>
			</tr>
		<?php } ?>
			<tr>
				<td colspan="6" align="center" class="<?php echo JLMSCSS::_('jlmslist-footer_td');?>">
					<div align="center" style="text-align: center; white-space:nowrap;">
					<?php
						$link = "index.php?option=$option&amp;Itemid=$Itemid&amp;task=quizzes&amp;id=$course_id&amp;page=$page&amp;c_id=$id";
						echo _JLMS_PN_DISPLAY_NUM . '&nbsp;' . $pageNav->getLimitBox( $link ) . '&nbsp;' . $pageNav->getPagesCounter();
						echo '<br />';
						echo $pageNav->writePagesLinks( $link );
					?>
					</div>
				</td>
			</tr>
		</table>
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
		<input type="hidden" name="task" value="quizzes" />
		<input type="hidden" name="id" value="<?php echo $course_id;?>" />
		<input type="hidden" name="page" value="<?php echo $page;?>" />
		<input type="hidden" name="task" value="quizzes" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="c_id" value="<?php echo $id;?>" />
		</form>
		<?php JLMS_quiz_admin_html_class::showQuizFooter(); ?>
		<?php
	}

	function JQ_view_questionReport( $type, &$rows, &$option, $page, $course_id, &$lists) {
		global $Itemid, $JLMS_CONFIG;
		$stu_id = intval(mosGetParam($_REQUEST, 'stu_id', 0));
		$toolbar = array();
		$toolbar[] = array('btn_type' => 'back', 'btn_txt' => _JLMS_QUIZ_BACK_BTN, 'btn_js' => sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=quizzes&id=$course_id&page=stu_reportA&c_id=$stu_id"));
		JLMS_quiz_admin_html_class::showQuizHead( $course_id, $option, _JLMS_QUIZ_QUEST_REPORT_TITLE, true, $toolbar );
		?>
		<form action="<?php echo sefRelToAbs("index.php?option=$option&Itemid=$Itemid");?>" method="post" name="adminForm">
<?php	$r_img = $JLMS_CONFIG->get('live_site').'/components/com_joomla_lms/lms_images/toolbar/btn_accept.png';
		switch($type) {
			case 1: ?>
		<table width="100%" cellpadding="0" cellspacing="0" border="0">
		<tr>
			<td class="sectiontableheader" width="75"><?php echo _JLMS_QUIZ_TBL_USER_CHOICE;?></td>
			<td class="sectiontableheader" width="75"><?php echo _JLMS_QUIZ_TBL_RIGHT_ANSWER;?></td>
			<td class="sectiontableheader"><?php echo _JLMS_QUIZ_TBL_QUEST_OPTIONS;?></td>
		</tr>
		<?php
		$k = 1;
		for ($i=0, $n=count($rows); $i < $n; $i++) {
			$row = $rows[$i];
			?>
			<tr class="<?php echo "sectiontableentry$k"; ?>">
				<td align="center">
					<?php if ($row->sc_id) { ?>
					<img src="<?php echo $r_img;?>" width="16" height="16" border="0" alt="<?php echo _JLMS_QUIZ_TBL_USER_CHOICE;?>" />
					<?php } ?>
				</td>
				<td align="center">
					<?php if ($row->c_right) { ?>
					<img src="<?php echo $r_img;?>" width="16" height="16" border="0" alt="<?php echo _JLMS_QUIZ_TBL_RIGHT_ANSWER;?>" />
					<?php } ?>
				</td>
				<td align="left">
					<?php echo $row->c_choice; ?>
				</td>
			</tr>
			<?php
			$k = 3 - $k;
		}?>
		</table>
		<?php break;
		case 4: ?>
		<table width="100%" cellpadding="0" cellspacing="0" border="0">
		<tr>
			<td class="sectiontableheader" colspan="2"><?php echo _JLMS_QUIZ_TBL_USER_CHOICE;?></td>
			<td class="sectiontableheader"><?php echo _JLMS_QUIZ_TBL_RIGHT_ANSWER;?></td>
			<td class="sectiontableheader"><?php echo _JLMS_QUIZ_TBL_QUEST_OPTIONS;?></td>
		</tr>
		<?php
		$k = 1;
		for ($i=0, $n=count($rows); $i < $n; $i++) {
			$row = $rows[$i];
			?>
			<tr class="<?php echo "sectiontableentry$k"; ?>">
				<td align="center" width="75px">
					<?php if ($row->c_sel_text == $row->c_right_text) { ?>
					<img src="<?php echo $r_img;?>" width="16" height="16" border="0" alt="<?php echo _JLMS_QUIZ_TBL_USER_CHOICE;?>" />
					<?php } ?>
				</td>
				<td align="left">
					<?php echo $row->c_sel_text; ?>
				</td>
				<td align="left">
					<?php echo $row->c_right_text; ?>
				</td>
				<td align="left">
					<?php echo $row->c_left_text; ?>
				</td>
			</tr>
			<?php
			$k = 3 - $k;
		}?>
		</table>
		<?php break;
		case 6: ?>
		<table width="100%" cellpadding="0" cellspacing="0" border="0">
		<tr>
			<td class="sectiontableheader"><?php echo _JLMS_QUIZ_TBL_USER_ANSWER;?></td>
		</tr>
		<tr class="sectiontableentry1">
			<td align="left">
			<?php echo $rows->c_answer; ?>
			</td>
		</tr>
		</table>
		<?php break;
		case 7: ?>
		<table><tr><td align="center">
		<div style="text-align:left;">
		<div id="div_hotspot_rec" style="background-color:#FFFFFF; z-index:1001; <?php if (preg_match('/MSIE ([0-9].[0-9]{1,2})/', $_SERVER['HTTP_USER_AGENT'])) { echo "filter:alpha(opacity=50);";}?> -moz-opacity:.50; opacity:.50; border:1px solid #000000; position:relative; left:<?php echo $lists['hotspot']->c_start_x;?>px; top:<?php echo ($lists['hotspot']->c_start_y+$lists['hotspot']->c_height + 12);?>px; width:<?php echo $lists['hotspot']->c_width;?>px; height:<?php echo $lists['hotspot']->c_height;?>px; ">
		<img src="<?php echo $JLMS_CONFIG->get('live_site');?>/images/blank.png" border="0" width="1" height="1" />
		</div>

		<div style='position:relative; z-index:1000; top:<?php echo ($rows->c_select_y + 6);?>px; left:<?php echo ($rows->c_select_x - 6);?>px'>
			<img src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/includes/quiz/hs_round.png" width='12' height='12' />
		</div>
		<img style='position:relative; z-index:999;' src="<?php echo $JLMS_CONFIG->get('live_site');?>/images/joomlaquiz/images/<?php echo $lists['image'];?>" />
		</div></td></tr></table>
		<?php break;
		case 9:
		?>
		<table width="100%" cellpadding="0" cellspacing="0" border="0">
		<tr>
			<?php
			$arr_scale = array();
			for ($i=0, $n=count($rows); $i < $n; $i++) {
				$qone = $rows[$i];
				if($qone->c_type == 1)
				{
					echo "<td  class='sectiontableheader'>".$qone->c_field."</td>" . "\n";
					$arr_scale[] = $qone->c_id;
				}
			}
			?>
			<td class="sectiontableheader"><?php echo _JLMS_QUIZ_TBL_QUEST_OPTIONS;?></td>
		</tr>
		<?php
		$z=1;
		for ($j=0, $n=count($rows); $j < $n; $j++) {
				$qone = $rows[$j];
			
			if($qone->c_type == 0)
			{
				echo "<tr class='sectiontableentry".($z)."'>" . "\n";	
				
				
				for($i=0;$i<count($arr_scale);$i++)
				{
					$chk = '&nbsp;';
					if($qone->inchek && $arr_scale[$i] == $qone->inchek) $chk = '<img src="'.$r_img.'" width="16" height="16" border="0" alt="'._JLMS_QUIZ_TBL_USER_CHOICE.'" />';
					echo "<td align='left'>".$chk."</td>" . "\n";
				}
				echo "<td align='left'>".$qone->c_field."</td>" . "\n";
				$z = 3 - $z;
				echo "</tr>" . "\n";	
			}
		}?>
		</table>
		<?php
		break;			
		} ?>
		<br />
		<table width="100%" cellpadding="0" cellspacing="0" border="0">
		<tr>
			<td class="sectiontableheader"><?php echo _JLMS_QUIZ_TBL_QUEST_TEXT;?></td>
		</tr>
		<tr><td><?php echo $lists['question'];?></td></tr></table>
		<br />
		<table width="100%" cellpadding="0" cellspacing="0" border="0">
		<tr>
			<td colspan="2" class="sectiontableheader"><?php echo _JLMS_QUIZ_TBL_USER_INFO;?></td>
		</tr>
		</table>
		<?php echo JLMS_outputUserInfo($lists['user']->username, $lists['user']->name, $lists['user']->email, $lists['user']->usergroup, '15%', true); ?>
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
		<input type="hidden" name="task" value="quizzes" />
		<input type="hidden" name="id" value="<?php echo $course_id;?>" />
		<input type="hidden" name="page" value="stu_reportA" />
		<input type="hidden" name="qid" value="<?php echo $lists['qid']; ?>" />
		<input type="hidden" name="boxchecked" value="0" />
		</form>
		<?php JLMS_quiz_admin_html_class::showQuizFooter(); ?>
<?php
	}

	//Joomla 1.6 optimized
	function JQ_showImgsList( &$rows, &$pageNav, $option, $page, $id ) {
		global $Itemid;
		$toolbar = array();
		$toolbar[] = array('btn_type' => 'new', 'btn_txt' => _JLMS_QUIZ_NEW_IMGS_BTN, 'btn_js' => sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid&amp;id=$id&amp;task=quizzes&amp;page=add_imgs"));
		$toolbar[] = array('btn_type' => 'edit', 'btn_txt' => _JLMS_QUIZ_EDIT_IMGS_BTN, 'btn_js' => "javascript:submitbutton('edit_imgs');");
		$toolbar[] = array('btn_type' => 'del', 'btn_txt' => _JLMS_QUIZ_DEL_IMGS_BTN, 'btn_js' => "javascript:submitbutton('del_imgs');");

		JLMS_quiz_admin_html_class::showQuizHead( $id, $option, _JLMS_QUIZ_IMGS_TITLE, true, $toolbar );
		?>

<script language="javascript" type="text/javascript">
<!--
function submitbutton(pressbutton) {
	var form = document.adminForm;
	if ( ((pressbutton == 'edit_imgs') || (pressbutton == 'del_imgs') ) && (form.boxchecked.value == "0")) {
		alert('<?php echo _JLMS_ALERT_SELECT_ITEM;?>');
	} else {
		form.page.value = pressbutton;
		form.submit();
	}
}
//-->
</script>
		<form action="<?php echo sefRelToAbs("index.php?option=$option&Itemid=$Itemid");?>" method="post" name="adminForm">
		<table width="100%" cellpadding="0" cellspacing="0" border="0" class="<?php echo JLMSCSS::_('jlmslist');?>">
			<tr>
				<<?php echo JLMSCSS::tableheadertag();?> width="20" class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center">#</<?php echo JLMSCSS::tableheadertag();?>>
				<<?php echo JLMSCSS::tableheadertag();?> width="20" class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></<?php echo JLMSCSS::tableheadertag();?>>
				<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>"><?php echo _JLMS_QUIZ_TBL_IMGS_NAME;?></<?php echo JLMSCSS::tableheadertag();?>>
			</tr>
			<?php
			$k = 1;
			for ($i=0, $n=count($rows); $i < $n; $i++) {
				$row = $rows[$i];
				$link 	= sefRelToAbs("index.php?option=".$option."&amp;Itemid=".$Itemid."&amp;id=$id&amp;task=quizzes&amp;page=editA_imgs&amp;c_id=".$row->c_id);
				$checked = mosHTML::idBox( $i, $row->c_id);?>
				<tr class="<?php echo JLMSCSS::_('sectiontableentry'.$k); ?>">
					<td align="center"><?php echo  $pageNav->limitstart + $i + 1; ?></td>
					<td align="center"><?php echo $checked; ?></td>
					<td align="left">
						<span>
							<?php #echo mosToolTip( mysql_escape_string(nl2br($row->c_instruction)), _JLMS_QUIZ_CAT_TOOLTIP_HEAD, 280, 'tooltip.png', $row->imgs_name, $link );?>
							<a href="<?php echo $link;?>" title="<?php echo $row->imgs_name;?>">
								<?php echo $row->imgs_name;?>
							</a>
						</span>
					</td>
				</tr>
				<?php
				$k = 3 - $k;
			}?>
			<tr>
				<td colspan="3" align="center" class="<?php echo JLMSCSS::_('jlmslist-footer_td');?>">
					<div align="center" style="white-space:nowrap ">
					<?php
						$link = "index.php?option=$option&amp;Itemid=$Itemid&amp;task=quizzes&amp;id=$id&amp;page=$page";
						echo _JLMS_PN_DISPLAY_NUM . '&nbsp;' . $pageNav->getLimitBox( $link ) . '&nbsp;' . $pageNav->getPagesCounter();
						echo '<br />';
						echo $pageNav->writePagesLinks( $link );
					?> 
					</div>
				</td>
			</tr>
		</table>
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="page" value="<?php echo $page;?>" />
		<input type="hidden" name="task" value="quizzes" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="id" value="<?php echo $id;?>" />
		</form>
		<?php JLMS_quiz_admin_html_class::showQuizFooter(); ?>
		<?php
	}

	//Joomla 1.6 ready
	function showEditImg( $imgs_details, &$lists, $option, $id ){
		global $Itemid;	
		
		$toolbar = array();
		$toolbar[] = array('btn_type' => 'save', 'btn_txt' => _JLMS_QUIZ_SAVE_IMGS_BTN, 'btn_js' => "javascript:submitbutton('save_imgs');");
		$toolbar[] = array('btn_type' => 'cancel', 'btn_txt' => _JLMS_QUIZ_CANCEL_BTN, 'btn_js' => "javascript:submitbutton('cancel_imgs');");
		
		$h = $imgs_details[0]->c_id ? _JLMS_QUIZ_IMGS_EDIT_TITLE : _JLMS_QUIZ_IMGS_NEW_TITLE ;
		JLMS_quiz_admin_html_class::showQuizHead( $id, $option, $h, true, $toolbar );
		?>
<script language="javascript" type="text/javascript">
		<!--
		function submitbutton(pressbutton) {
			var form = document.adminForm;

			if (pressbutton == 'cancel_imgs') {
				form.page.value = pressbutton;
				form.submit();
				return;
			}
			// do field validation
			if (form.imgs_name.value == ""){
				alert( "<?php echo _JLMS_PL_ENTER_NAME;?>" );
			} else {
				form.page.value = pressbutton;
				form.submit();
			}
		}

//--><!]]>
</script>
<?php
		JLMS_TMPL::OpenMT('jlms_table_no_borders');

		$hparams = array();
		$toolbar = array();
		$title = '';
		JLMS_TMPL::OpenTS();
?>
		<form action="<?php echo sefRelToAbs("index.php?option=".$option."&Itemid=".$Itemid);?>" method="post" name="adminForm" enctype="multipart/form-data">
			<table width="100%" cellpadding="0" cellspacing="0" border="0" class="jlms_item_properties">
				<tr>
					<td width="15%" valign="middle" style="vertical-align:middle"><?php echo _JLMS_ENTER_NAME;?></td>
					<td><input class="inputbox" size="40" type="text" name="imgs_name" value="<?php echo $imgs_details[0]->c_id?$imgs_details[0]->imgs_name:'';?>">
					</td>
				</tr>
				<tr>
					<td valign="top" style="vertical-align:top"><br /><?php echo isset($imgs_details[0]->imgs_id) ? /*_JLMS_VIEW_FILE*/ (_JLMS_PREVIEW_ALT_TITLE.':') : _JLMS_CHOOSE_FILE;?></td>
					<td valign="top">
						<?php
						if(isset($imgs_details[0]->c_id) && $imgs_details[0]->c_id){
							$link = sefRelToAbs("index.php?tmpl=component&option=$option&task=quizzes&course_id=".$id."&page=imgs_v&file_id=".$imgs_details[0]->imgs_id."&imgs_name=".$imgs_details[0]->imgs_name."");
						?>
						<img src="<?php echo $link;?>" border="0" title="<?php echo $imgs_details[0]->imgs_name;?>">
						<?php
						} else {
						?>
						<br /><input size="40" class="inputbox" type="file" name="imgs" />
						<?php
						}
						?>
					</td>
				</tr>
			</table>
			<input type="hidden" name="option" value="<?php echo $option;?>" />
			<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
			<input type="hidden" name="task" value="quizzes" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="id" value="<?php echo $id;?>" />
			<input type="hidden" name="c_id" value="<?php echo isset($imgs_details[0]->c_id)?$imgs_details[0]->c_id:'';?>" />
			<input type="hidden" name="page" value="save_imgs" />
		</form>
<?php
		JLMS_TMPL::CloseTS();
		JLMS_TMPL::CloseMT();
		JLMS_quiz_admin_html_class::showQuizFooter();
	}
	
}
?>