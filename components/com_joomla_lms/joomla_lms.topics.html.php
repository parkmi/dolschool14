<?php
/**
* joomla_lms.topics.html.php
* (c) JoomaLMS eLearning Software http://www.joomlalms.com/
**/
// no direct access
defined( '_JLMS_EXEC' ) or die( 'Restricted access' );
//definition for row outputing
define('_ROW_RO', 0);//only read type. used for choosing from list
define('_ROW_ED', 1);//edit type.used in most situations, allows editing

class JLMS_topic_html {
	function showTopicsList ($course_id, $topics, $links, $elements, $span=1) {		
		global $option, $Itemid, $max_lvl, $JLMS_CONFIG;
	//	echo '<pre>'; var_dump($elements);die;
		$usertype = $JLMS_CONFIG->get('current_usertype', 0);
		$date_format = $JLMS_CONFIG->get('date_format_fdow', 1);
		?>
		<script language="javascript" type="text/javascript">
		<!--//--><![CDATA[//><!--
		function topicSubmit ($topic_id, $task) {
			var $form = document.forms['topicForm_'+$topic_id];
			$form.task.value = $task;
			$form.submit();
		}
		function topicDelete ($topic_id) {
			var $form = document.forms['topicForm_'+$topic_id];
			$form.task.value = 'delete_topic';
			$form.submit();
		}
		function topicAdd ($topic_id) {
			var $form = document.forms['topicForm_'+$topic_id];
			$form.task.value = 'add_topic_element';
			$form.submit();
		}
		function topicChange ($topic_id, $def) {
			var $form = document.forms['topicForm_'+$topic_id];
			$form.task.value = 'change_element';
			$form.state.value = $def;
			$form.submit();
		}
		//same for isChecked function but has FORM name param
		function isChecked_mod(isitchecked, $form_name){
			if (isitchecked == true){
				document.forms[$form_name].boxchecked.value++;
			}
			else {
				document.forms[$form_name].boxchecked.value--;
			}
		}
		function checkAll_mod( n, fldName, $form_name ) {
			if (!fldName) {
				fldName = 'cb';
			}
			var f = document.forms[$form_name];
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
				document.forms[$form_name].boxchecked.value = n2;
			} else {
				document.forms[$form_name].boxchecked.value = 0;
			}
		}
		//--><!]]>
		</script>
		<?php
		$topics_count = count($topics);
		foreach ($topics as $topic) {
			$topic_publish = publishUtility($topic->published, -1, $topic->publish_start, $topic->publish_end, $topic->start_date, $topic->end_date);			
		?>
		<div id="topicmain_<?php echo $topic->id;?>" class="topicmaindiv<?php echo ($usertype != 2 && $topic_publish->state == 1)? 2 : ''; ?>">
			<a name="topic_<?php echo $topic->id; ?>"><!--x--></a>
			<?php echo JLMSCSS::h2($topic->name); ?>
			
			<div class="contentmain">
				<form name="topicForm_<?php echo $topic->id; ?>" method="post" action="<?php echo sefRelToAbs("index.php?option=".$option."&amp;Itemid=".$Itemid); ?>">
				<?php if ( ($topic->publish_start || $topic->publish_end) || ($topic->description) || (isset($links[$topic->id]) && count($links[$topic->id])) ) { ?>
				
				<div class="topics">
					
					<?php
					JLMS_TMPL::OpenMT();

					$_JLMS_PLUGINS = & JLMSFactory::getPlugins();
					$_JLMS_PLUGINS->loadBotGroup('system');
					$plugin_args = array();
					$plugin_args[] = $topic->id;
					$_JLMS_PLUGINS->trigger('onAboveTopicDescription', $plugin_args);

					JLMS_TMPL::CloseMT();
					?>
	
					<?php if ($topic->publish_start || $topic->publish_end) { ?>
					<div class="topic_createdate">
						<?php
						//if ($topic->publish_start) echo _JLMS_TOPIC_T_STARTS_ON.date("Y-m-d",strtotime($topic->start_date));
						//if ($topic->publish_end) echo _JLMS_TOPIC_T_ENDS_ON.date("Y-m-d",strtotime($topic->end_date));
						// 06.12.2007 - (DEN)
						if ($topic->publish_start) echo _JLMS_TOPIC_T_STARTS_ON.' '.JLMS_dateToDisplay($topic->start_date).(($topic->publish_end)?'&nbsp;':'');
						if ($topic->publish_end) echo _JLMS_TOPIC_T_ENDS_ON.' '.JLMS_dateToDisplay($topic->end_date);
						?>
					</div>
					<?php } ?>
					<?php 
					if (
						$topic->description &&
						isset($links[$topic->id]) && count($links[$topic->id])
					){ 
					?>
					<div class="topic_description">
						<?php 
						echo JLMS_ShowText_WithFeatures($topic->description);
						#echo JLMS_ShowText_WithFeatures('{readmore title="FIRST"}text{readmore}text{/readmore}'); //for test
						?>
						<?php if (isset($links[$topic->id]) && count($links[$topic->id])) { echo '<br />';} ?>
						<div class="topic_elements">
							<?php
							echo JLMS_topic_html::showTopicElements($course_id, $topic, $links, $elements, $span);
							?>
						</div>
						<?php
						global $JLMS_topic_readmore_closeTag, $JLMS_count_begin_tags, $JLMS_count_end_tags;
						if($JLMS_topic_readmore_closeTag){
							if(isset($JLMS_count_begin_tags) && isset($JLMS_count_end_tags)){
								$stop = 0;
								if($JLMS_count_begin_tags > $JLMS_count_end_tags){
									$stop = $JLMS_count_begin_tags - $JLMS_count_end_tags;
								}
								$i=0;
								while($i < $stop){
									echo '<div class="clr"><!-- --></div>' . "\n";
									echo '</div>'  . "\n";
									$i++;
								}
							}
						}
						?>
					</div>
					<?php 
					} else 
					if(
						!$topic->description &&
						isset($links[$topic->id]) && count($links[$topic->id])
					){
					?>
						<div class="topic_elements">
							<?php
							//echo JLMS_topic_html::showTopicElements($course_id, $topic, $links, $elements, $span=1);
							echo JLMS_topic_html::showTopicElements($course_id, $topic, $links, $elements, $span);
							?>
						</div>
					<?php	
					} else 
					if(
						$topic->description &&
						(!isset($links[$topic->id]) || !count($links[$topic->id]))
					){
					?>
						<div class="topic_description">
						<?php echo JLMS_ShowText_WithFeatures($topic->description); ?>
						<?php if (isset($links[$topic->id]) && count($links[$topic->id])) { echo '<br />';} ?>
						<?php
						global $JLMS_topic_readmore_closeTag, $JLMS_count_begin_tags, $JLMS_count_end_tags;
						if($JLMS_topic_readmore_closeTag){
							if(isset($JLMS_count_begin_tags) && isset($JLMS_count_end_tags)){
								$stop = 0;
								if($JLMS_count_begin_tags > $JLMS_count_end_tags){
									$stop = $JLMS_count_begin_tags - $JLMS_count_end_tags;
								}
								$i=0;
								while($i < $stop){
									echo '<div class="clr"><!-- --></div>' . "\n";
									echo '</div>'  . "\n";
									$i++;
								}
							}
						}
						?>
					</div>
					<?php	
					}
					?>
				</div>
				<?php } ?>
				<input type="hidden" name="option" value="<?php echo $option;?>" />
				<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
				<input type="hidden" name="boxchecked" value="0" />
				<input type="hidden" name="task" value="" />
				<input type="hidden" name="id" value="<?php echo $course_id;?>" />
				<input type="hidden" name="state" value="0" />
				<input type="hidden" name="topic_id" value="<?php echo $topic->id; ?>" />
				<input type="hidden" name="topic_ordering" value="<?php echo $topic->ordering; ?>" />
				<?php
				$is_curtopic = intval(mosgetparam($_REQUEST,'t_id',0));
					echo "<input type='hidden' name='t_id' value='".$is_curtopic."' />";
				?>	
				</form>			
				<?php			
				$controls = array();
				$controls[] = array('href' => "javascript:if(document.topicForm_$topic->id.boxchecked.value==0){alert('".str_replace(' ', '%20',_JLMS_TOPIC_E_NO_ELEMENTS_CHOSEN)."');}else{topicChange($topic->id,0);}", 'title' => _JLMS_PUBLISH_ELEMENT, 'img' => 'publish');
				$controls[] = array('href' => "javascript:if(document.topicForm_$topic->id.boxchecked.value==0){alert('".str_replace(' ', '%20',_JLMS_TOPIC_E_NO_ELEMENTS_CHOSEN)."');}else{topicChange($topic->id,1);}", 'title' => _JLMS_UNPUBLISH_ELEMENT, 'img' => 'unpublish');
				$controls[] = array('href' => "javascript:if(document.topicForm_$topic->id.boxchecked.value==0){alert('".str_replace(' ', '%20',_JLMS_TOPIC_E_NO_ELEMENTS_CHOSEN)."');}else{topicSubmit($topic->id,'delete_topic_element');}", 'title' => _JLMS_DELETE_ELEMENT, 'img' => 'delete');
				$controls[] = array('href' => "javascript:topicAdd($topic->id);", 'title' => _JLMS_ADD_ELEMENTS, 'img' => 'add');
				$controls[] = array('href' => 'spacer');
				$controls[] = array('href' => 'spacer');
	
				$publish_options = publishUtility_topic($topic->published, $topic->publish_start, $topic->publish_end, $topic->start_date, $topic->end_date);
				$controls[] = array('href' => JLMSRoute::_("index.php?option=".$option."&amp;Itemid=".$Itemid."&amp;task=publish_topic&amp;state=".(1-$publish_options->state)."&amp;id=".$course_id."&amp;topic_id=$topic->id"), 'title' => (($publish_options->state == 1) ? _JLMS_TOPIC_PUBLISHED : _JLMS_TOPIC_UNPUBLISHED), 'img' => (($publish_options->state == 1) ? 'publish' : 'unpublish'));
				$controls[] = array('href' => "javascript:if(confirm('".str_replace(' ', '%20',_JLMS_TOPIC_T_CONFIRM_DELETE)."')){topicDelete($topic->id);}", 'title' => _JLMS_TOPIC_T_DELETE, 'img' => 'delete');			
				global $Itemid, $option; $course_id	= intval( mosGetParam( $_REQUEST, 'id', 0 ) );
				$controls[] = array('href' => JLMSRoute::_("index.php?option=$option&amp;Itemid=$Itemid&amp;task=edit_topic&amp;id=$course_id&amp;topic_id=$topic->id"), 'title' => _JLMS_TOPIC_T_EDIT, 'img' => 'edit');
				if ($topic->ordering > 0) {
					$controls[] = array('href' => "javascript:topicSubmit($topic->id,'orderup_topic')", 'title' => _JLMS_TOPIC_T_MOVEUP, 'img' => 'up');
				}
				if ($topic->ordering < $topics_count-1) {
					$controls[] = array('href' => "javascript:topicSubmit($topic->id,'orderdown_topic')", 'title' => _JLMS_TOPIC_T_MOVEDOWN, 'img' => 'down');
				}
				if ($usertype == 2) $controls = array();
				JLMS_TMPL::ShowControlsFooterC($controls);
				?>
			</div>
		</div>
			<?php
		}
	}
	
	function showTopicElements($course_id, $topic, $links, $elements, $span=1){
		global $option, $Itemid, $max_lvl, $JLMS_CONFIG;
		
		$usertype = $JLMS_CONFIG->get('current_usertype', 0);
		$date_format = $JLMS_CONFIG->get('date_format_fdow', 1);
		
		ob_start();
		?>
			<table border="0" cellpadding="0" cellspacing="0" width="100%" id="topic_<?php echo $topic->id; ?>_elements_table" class="<?php echo JLMSCSS::_('jlmslist');?>">
				<tr>
					<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" style="width:1%;"><?php echo ($usertype != 2) ? '#' : '&nbsp;'; ?></<?php echo JLMSCSS::tableheadertag();?>>
					<?php if ($usertype != 2) { ?><<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" style="width:1%;"><input type="checkbox" onclick="checkAll_mod(<?php echo count($links[$topic->id]); ?>, 'cb<?php echo $topic->id.'_'; ?>', 'topicForm_<?php echo $topic->id; ?>');" value="" name="toggle"/></<?php echo JLMSCSS::tableheadertag();?>><?php } ?>
					<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" style="width:95%;" colspan="<?php echo $span+1;?>"><?php echo _JLMS_TOPIC_E_NAME; ?></<?php echo JLMSCSS::tableheadertag();?>>
					<?php if ($usertype != 2) { ?><<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" colspan="3" align="center" style="width:1%;"><?php echo _JLMS_TOPIC_E_CONTROLS; ?></<?php echo JLMSCSS::tableheadertag();?>><?php } ?>
					<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" colspan="<?php echo ($usertype != 2) ? 2 : 3; ?>" align="center" style="width:1%;"><?php echo _JLMS_TOPIC_E_DETAILS; ?></<?php echo JLMSCSS::tableheadertag();?>>
					<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" style="width:1%;"><span style="display: block; width:150px;"><?php echo _JLMS_TOPIC_E_DESRIPTION; ?></span></<?php echo JLMSCSS::tableheadertag();?>>
				</tr>
				<?php
				$i = 0;
				$vis_mode = 0;
				$k = 2;
				$displayed = 0;
				if ($count = count($links[$topic->id])) {//checks if there are elements in topic
					foreach ($links[$topic->id] as $link) {
						if (isset($link->item_type) && $link->item_type == _DOCUMENT_ID) {
							if (!isset($elements[_DOCUMENT_ID][$link->item_id])) { continue;} /* (23 July 2008) - added by DEN */
						}

						// 01.12.2007 '$i' chenged to '$topic_id*1000+$i' by DEN - to avoid dublicate id's
						$iii = $topic->id.'_'.$i;
						$checked = '<input type="checkbox" id="cb'.$iii.'" name="cid[]" value="'.$link->id.'" onclick="isChecked_mod(this.checked, \'topicForm_'.$topic->id.'\');" />';
						switch ($link->item_type) {
							case _DOCUMENT_ID:
							$elements[_DOCUMENT_ID][$link->item_id]->allow_up = ($i > 0) ? 1 : 0;
							$elements[_DOCUMENT_ID][$link->item_id]->allow_down = ($i < $count - 1) ? 1 : 0;
							$elements[_DOCUMENT_ID][$link->item_id]->show=$link->show;
							$elements[_DOCUMENT_ID][$link->item_id]->ordering=$link->ordering;
							$elements[_DOCUMENT_ID][$link->item_id]->link_id=$link->id;										
							$publish_options = publishUtility($elements[_DOCUMENT_ID][$link->item_id]->published, $elements[_DOCUMENT_ID][$link->item_id]->show, $elements[_DOCUMENT_ID][$link->item_id]->publish_start, $elements[_DOCUMENT_ID][$link->item_id]->publish_end, $elements[_DOCUMENT_ID][$link->item_id]->start_date, $elements[_DOCUMENT_ID][$link->item_id]->end_date);
							if ($usertype == 2 && ($publish_options->show * $publish_options->state == 0)) continue;
							if (!$elements[_DOCUMENT_ID][$link->item_id]->folder_flag || $elements[_DOCUMENT_ID][$link->item_id]->folder_flag == 3 || $elements[_DOCUMENT_ID][$link->item_id]->folder_flag == 2) JLMS_topic_html::showDocumentRow($k, $elements[_DOCUMENT_ID][$link->item_id], $i, $topic->id, $checked, 1, 0, $span);
							else showFolderWithContent($k, $elements[_DOCUMENT_ID][$link->item_id], $i, $topic->id, $checked, $span);
							$displayed++;
							break;

							case _LINK_ID:
							$elements[_LINK_ID][$link->item_id]->allow_up = ($i > 0) ? 1 : 0;
							$elements[_LINK_ID][$link->item_id]->allow_down = ($i < $count - 1) ? 1 : 0;
							$elements[_LINK_ID][$link->item_id]->show=$link->show;
							$elements[_LINK_ID][$link->item_id]->ordering=$link->ordering;
							$elements[_LINK_ID][$link->item_id]->link_id=$link->id;										
							if ($usertype == 2 && ($elements[_LINK_ID][$link->item_id]->show * $elements[_LINK_ID][$link->item_id]->published == 0)) continue;
							JLMS_topic_html::showLinkRow($k, $elements[_LINK_ID][$link->item_id], $i, $topic->id, $checked, 1, $span);
							$displayed++;
							break;

							case _QUIZ_ID:
							$elements[_QUIZ_ID][$link->item_id]->allow_up = ($i > 0) ? 1 : 0;
							$elements[_QUIZ_ID][$link->item_id]->allow_down = ($i < $count - 1) ? 1 : 0;
							$elements[_QUIZ_ID][$link->item_id]->show=$link->show;
							$elements[_QUIZ_ID][$link->item_id]->ordering=$link->ordering;
							$elements[_QUIZ_ID][$link->item_id]->link_id=$link->id;
							if ($usertype == 2 && ($elements[_QUIZ_ID][$link->item_id]->show * $elements[_QUIZ_ID][$link->item_id]->published == 0)) continue;
							JLMS_topic_html::showQuizRow($k, $elements[_QUIZ_ID][$link->item_id], $i, $topic->id, $checked, 1, $span);
							$displayed++;
							break;

							case _LPATH_ID:
							$elements[_LPATH_ID][$link->item_id]->allow_up = ($i > 0) ? 1 : 0;
							$elements[_LPATH_ID][$link->item_id]->allow_down = ($i < $count - 1) ? 1 : 0;
							$elements[_LPATH_ID][$link->item_id]->show=$link->show;
							$elements[_LPATH_ID][$link->item_id]->ordering=$link->ordering;
							$elements[_LPATH_ID][$link->item_id]->link_id=$link->id;
							
							if(isset($link->lpath_name))
								$elements[_LPATH_ID][$link->item_id]->lpath_name=$link->lpath_name;
							if(isset($link->is_link))	
								$elements[_LPATH_ID][$link->item_id]->is_link=$link->is_link;

							// 01.12.2007 - (DEN) - esli v topike ni odin element ne 'shown' - vyletaet notice
							if (!isset($elements[_LPATH_ID][$link->item_id]->published)) {
								$elements[_LPATH_ID][$link->item_id]->published = 0;
							}

							if ($usertype == 2 && ($elements[_LPATH_ID][$link->item_id]->show * $elements[_LPATH_ID][$link->item_id]->published == 0)) continue;
							$displayed += JLMS_topic_html::showLPathRow($k, $elements[_LPATH_ID][$link->item_id], $i, $topic->id, $checked, 1, $span);
							break;
						}
						$i++;
					}
				}
				?>
			</table>
			<?php
			if ($displayed == 0) {
				?>
			<script type="text/javascript" language="javascript">
			document.getElementById('topic_<?php echo $topic->id; ?>_elements_table').style.display='none';
			</script>
			<?php }	?>
			<?php
		$return = ob_get_clean();
		
		return $return;
	}

	function showElementsList ($course_id, $topic_id, $elements, $linked_elements) {
		global $Itemid, $option, $max_lvl, $JLMS_CONFIG;
		$max_lvl = 1;
		//work out chapters... TODO

		//work out documents
		JLMS_TMPL::OpenMT();

		$hparams = array();
		$toolbar = array();
		$title = '';

		$title = _JLMS_TOPIC_T_LINK_ELEMENT;

		$toolbar[] = array('btn_type' => 'save', 'btn_js' => "javascript:submitbutton('add_submit_topic_element');");
		$toolbar[] = array('btn_type' => 'cancel', 'btn_js' => "javascript:submitbutton('details_course');");
		JLMS_TMPL::ShowHeader('doc', $title, $hparams, $toolbar);

		JLMS_TMPL::OpenTS();
		?>
		<form name="adminForm" method="post" action="<?php echo $JLMS_CONFIG->get('live_site') . "/index.php?option=".$option."&amp;Itemid=".$Itemid;?>">
		<table border="0" cellpadding="0" cellspacing="0" width="100%" class="<?php echo JLMSCSS::_('jlmslist');?>">
		<?php
		$k = 2;
		//documents section
		$element_group = $elements[_DOCUMENT_ID];
		$i = 0;
		$is_any_elements = 0;
		if (!empty($element_group))
		foreach ($element_group as $element) {
			if (@in_array($element->id, $linked_elements[_DOCUMENT_ID])) continue;
			if ($i == 0) { ?>
				<tr>
					<td colspan="7"><?php echo JLMSCSS::h2(_JLMS_TOPIC_E_DOCUMENTS); ?></td>
				</tr>	
				<?php
			}
			$tmp = _DOCUMENT_ID.'_'.$element->id;
			$checked = '<input type="checkbox" id="doc_cb'.$i.'" name="cid[]" value="'.$tmp.'" onclick="isChecked(this.checked);" />';
			JLMS_topic_html::showDocumentRow($k, $element, $i, 0, $checked, _ROW_RO,0,1,0);
			$i++;
		}
		$is_any_elements = $is_any_elements + $i;
		//links section
		$element_group = $elements[_LINK_ID];
		$i = 0;
		if (!empty($element_group))
		foreach ($element_group as $element) {
			if (@in_array($element->id, $linked_elements[_LINK_ID])) continue;
			if ($i == 0) { ?>
				<tr>
					<td colspan="7"><?php echo JLMSCSS::h2(_JLMS_TOPIC_E_LINKS); ?></td>
				</tr>	
				<?php
			}
			$tmp = _LINK_ID.'_'.$element->id;
			$checked = '<input type="checkbox" id="link_cb'.$i.'" name="cid[]" value="'.$tmp.'" onclick="isChecked(this.checked);" />';
			JLMS_topic_html::showLinkRow($k, $element, $i, 0, $checked, _ROW_RO);
			$i++;
		}
		$is_any_elements = $is_any_elements + $i;
		//quizs section
		$element_group = $elements[_QUIZ_ID];		
		$i = 0;
		if (!empty($element_group))
		foreach ($element_group as $element) {
			if (@in_array($element->id, $linked_elements[_QUIZ_ID])) continue;
			if ($i == 0) { ?>
				<tr>
					<td colspan="7"><?php echo JLMSCSS::h2(_JLMS_TOPIC_E_QUIZZES); ?></td>
				</tr>	
				<?php
			}
			$tmp = _QUIZ_ID.'_'.$element->id;
			$checked = '<input type="checkbox" id="quiz_cb'.$i.'" name="cid[]" value="'.$tmp.'" onclick="isChecked(this.checked);" />';
			JLMS_topic_html::showQuizRow($k, $element, $i, 0, $checked, _ROW_RO);
			$i++;
		}
		$is_any_elements = $is_any_elements + $i;
		//Lpaths section
		$element_group = isset($elements[_LPATH_ID]) ? $elements[_LPATH_ID] : array();
		$i = 0;
		if (!empty($element_group))
		foreach ($element_group as $element) {
			if (@in_array($element->id, $linked_elements[_LPATH_ID])) continue;
			if ($i == 0) { ?>
				<tr>
					<td colspan="7"><?php echo JLMSCSS::h2(_JLMS_TOPIC_E_LPATHS); ?></td>
				</tr>	
				<?php
			}
			$tmp = _LPATH_ID.'_'.$element->id;
			$checked = '<input type="checkbox" id="lpath_cb'.$i.'" name="cid[]" value="'.$tmp.'" onclick="isChecked(this.checked);" />';
			JLMS_topic_html::showLPathRow($k, $element, $i, 0, $checked, _ROW_RO);
			$i++;
		}
		$is_any_elements = $is_any_elements + $i;
		if (!$is_any_elements) { ?>
				<tr>
					<td colspan="7"><?php echo '<div class="joomlalms_user_message">'._JLMS_TOPICS_NO_ELEMENTS.'</div>'; ?></td>
				</tr>	
		<?php } ?>
		</table>
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
		<input type="hidden" name="task" value="add_submit_topic_element" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="topic_id" value="<?php echo $topic_id; ?>" />
		<input type="hidden" name="id" value="<?php echo $course_id;?>" />
		<?php
			$is_curtopic = intval(mosgetparam($_REQUEST,'t_id',0));
				echo "<input type='hidden' name='t_id' value='".$is_curtopic."' />";
			?>
		</form>
		<?php
		JLMS_TMPL::CloseTS();
		JLMS_TMPL::CloseMT();
	}

	/**
	 * Prints one row of document type content
	 *
	 * @param stdClass $row all info on this row
	 * @param int $i number of row in list
	 * @param int $topic_id id of topic element belongs to
	 * @param string $checked allows creation of special view of cid[] checkbox values
	 * @param int $manage control buttons won't be displayed if set to 0
	 */
	function showDocumentRow (&$k, $row, $i, $topic_id, $checked = null, $manage=1, $in_folder=0, $span=1, $den_fix=1) {
		global $Itemid, $vis_mode, $option, $max_lvl, $JLMS_CONFIG;
		$JLMS_ACL = & JLMSFactory::getACL();
		$usertype = $JLMS_CONFIG->get('current_usertype', 0);
		$course_id 	= intval( mosGetParam( $_REQUEST, 'id', 0 ) );
		$link = ''; $link_title = '';
		if ($row->folder_flag ==2) {
			$link = sefRelToAbs("index.php?option=".$option."&amp;Itemid=".$Itemid."&amp;task=docs_view_zip&amp;course_id=".$course_id."&amp;id=".$row->id);
			$link_title = _JLMS_T_A_VIEW_ZIP_PACK;
		} elseif ((!$row->folder_flag || $row->folder_flag == 3) && $row->file_id) {
			$link = sefRelToAbs("index.php?option=".$option."&amp;Itemid=".$Itemid."&amp;task=get_document&amp;course_id=".$course_id."&amp;id=".$row->id);
			$link_title = _JLMS_DOCS_LINK_DOWNLOAD;
		} elseif ((!$row->folder_flag || $row->folder_flag == 3) && !$row->file_id) {
			$link = sefRelToAbs("index.php?option=".$option."&amp;Itemid=".$Itemid."&amp;task=docs_view_content&amp;course_id=".$course_id."&amp;id=".$row->id);
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
		/*if (!$JLMS_ACL->CheckPermissions('docs', 'manage')) {
			$checked = null;
			$manage = 0;
		}*/
		if (!$checked && $manage==1) {
			$checked = mosHTML::idBox( $topic_id*1000+$i, $row->link_id);
			// 01.12.2007 '$i' changed to '$topic_id*1000+$i' by DEN - to avoid dublicate id's
		}

		if ($usertype == 2) {
			$checked = null;
			$manage = 0;
		}

		// Collapsed/Expanded view
		$tree_row_style = '';
		$visible_folder = true;//$next_row_is_visible;
		//$next_row_is_visible = true;

		$k = 3 - $k;
		?>
		<tr class="<?php echo JLMSCSS::_('sectiontableentry'.$k); ?>"<?php echo $tree_row_style;?>>
			<td width='1%' align="center" valign="middle"><?php if (!$in_folder && $usertype != 2) echo ( $i + 1 ); else echo '&nbsp;'; ?></td>
			<?php if ($checked) echo "<td width='1%'>$checked</td>"; ?>
			<?php 
			for ($j=$max_lvl; $j>$span; $j--) {
				if (isset($in_folder[$max_lvl-$j])) {
					if ($in_folder[$max_lvl-$j] == 1 && $j>$span+1) $in_folder[$max_lvl-$j] = 3;
					switch ($in_folder[$max_lvl-$j]) {
						case 0:	$img = 'empty_line.png';	break;
						case 1:	$img = 'sub1.png';			break;
						case 2:	$img = 'sub2.png';			break;
						case 3:	$img = 'line.png';			break;
					}
					echo "<td width='1%' valign='middle' align='center'><img src=\"".$JLMS_CONFIG->get('live_site')."/components/com_joomla_lms/lms_images/treeview/".$img."\" width='16' height='16' alt='".$img."' /></td>";
				}
			}
			?>
			<td align="center" valign="middle" width='1%'>
				<?php echo "<span style='alignment:center; width:16px; font-weight:bold; vertical-align:middle;'><img class='JLMS_png' src=\"".$JLMS_CONFIG->get('live_site')."/components/com_joomla_lms/lms_images/files/".$row->file_icon.".png\" width='16' height='16' alt='$row->file_icon' title='$row->file_icon' /></span>";?>
			</td>
			<td align="left" valign="middle" colspan="<?php echo $span; ?>">
			<span style='vertical-align:middle;'>
			<?php if ($row->folder_flag == 1) {
				echo '&nbsp;<strong>'.$row->doc_name.'</strong>';
			} else { if(!isset($row->is_link)){?>
			<a href="<?php echo $link;?>" title="<?php echo str_replace('"','&quot;',$row->doc_name);?>">
				&nbsp;<?php echo $row->doc_name;?>
			</a>
			<?php } else {echo $row->doc_name;}
			}?>
			</span>
			</td>
			<?php if ($usertype != 2 ) { //teacher/admin table handler?>
				<?php if ($manage == 1) { ?>
					<td valign="middle" class="controltd"><?php if ($row->allow_up == 1) { echo JLMS_orderIcon_element($topic_id, $course_id, 'orderup_element', _JLMS_TOPIC_I_MOVEUP, $row->ordering); } else { echo '&nbsp;';}?></td>
					<td valign="middle" class="controltd"><?php if ($row->allow_down == 1) { echo JLMS_orderIcon_element($topic_id, $course_id, 'orderdown_element', _JLMS_TOPIC_I_MOVEDOWN, $row->ordering); } else { echo '&nbsp;';}?></td>
					<?php $state = publishUtility($row->published, $row->show, $row->publish_start, $row->publish_end, $row->start_date, $row->end_date); ?>
					<td valign="middle" class="controltd"><?php echo JLMS_showHideIcon($row->link_id, $course_id, $topic_id, $state, 'change_element', $option); ?></td>
				<?php } elseif ($den_fix) { ?>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				<?php } ?>
				<td align="center" nowrap='nowrap' valign="middle"><?php echo ($row->publish_start?JLMS_dateToDisplay($row->start_date):'-');?></td>
				<td align="center" nowrap='nowrap' valign="middle"><?php echo ($row->publish_end?JLMS_dateToDisplay($row->end_date):'-');?></td>
			<?php } else { //user/student table handler ?>
				<td align="center" nowrap='nowrap' valign="middle">-</td>
				<td align="center" nowrap='nowrap' valign="middle">-</td>
				<td align="center" nowrap='nowrap' valign="middle">-</td>
			<?php } ?>
			<td><?php
			$doc_descr = strip_tags($row->doc_description);
			if ((!$row->folder_flag || $row->folder_flag == 3) && !$row->file_id) {
				if (strlen($doc_descr) > 75) {
					$doc_descr = substr($doc_descr, 0, 75)."...";
				}
			}
			echo $doc_descr.'&nbsp;'; ?>
			</td>
		</tr>		
		<?php
	}

	/**
	 * Prints one row of link type content
	 *
	 * @param stdClass $row contains info on element
	 * @param int $i number of row
	 * @param int $topic_id no comments
	 * @param string $checked used for creating special checkbox
	 * @param int $manage show/hide control buttons
	 */
	function showLinkRow (&$k, $row, $i, $topic_id, $checked = null, $manage=1, $span=1) {
		$JLMS_CONFIG = & JLMSFactory::getConfig();
		$Itemid = $JLMS_CONFIG->get('Itemid');
		$course_id = $JLMS_CONFIG->get('course_id');
		global $my, $option;
		$usertype = $JLMS_CONFIG->get('current_usertype', 0);
		if ($usertype == 2) {
			$checked = null;
			$manage = 0;
		}
		$course_id 	= intval( mosGetParam( $_REQUEST, 'id', 0 ) );
		$k = 3 - $k;
		$link 	= $row->link_href;
		if (!$checked && $manage==1) $checked = mosHTML::idBox( $topic_id*1000+$i, $row->link_id);// 01.12.2007 '$i' chenged to '$topic_id*1000+$i' by DEN - to avoid dublicate id's
		?>
		<tr class="<?php echo JLMSCSS::_('sectiontableentry'.$k); ?>">
			<td width='1%' align="center"><?php if ($usertype != 2) echo ( $i + 1 ); else echo '&nbsp;'; ?></td>
			<?php if ($checked) echo "<td width='1%'>$checked</td>"; ?>
			<td width="1%"><img src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/tlb_links.png" alt='link' /></td>
			<td align="left" colspan="<?php echo $span; ?>">
			<?php
				$add_link_params = '';
				if ($row->link_type == 3) {
					$tmp_params = new JLMSParameters($row->params);
					$x_size = 0;
					$y_size = 0;
					if ( isset($tmp_params->display_width) ) {
						$x_size = $tmp_params->display_width;
					}
					if ( isset($tmp_params->display_height) ) {
						$y_size = $tmp_params->display_height;
					}
					$add_link_params = ' class="jlms_modal" rel="{handler:\'iframe\', size:{x:'.$x_size.',y:'.$y_size.'}}"';
					JLMS_initialize_SqueezeBox(false); ?>
					<a href="<?php echo $link;?>"<?php echo $add_link_params;?> title="<?php echo str_replace('"','&quot;',$row->link_name);?>">
						<?php echo $row->link_name;?>
					</a>
				<?php } else {
						if ($row->link_type == 2) {
							$link = sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=view_inline_link&course_id=$course_id&id=$row->id");
						}
						if ($link) { ?>
						<a <?php echo (!$row->link_type?'target="_blank" ':' ');?>href="<?php echo $link;?>" title="<?php echo str_replace('"','&quot;',$row->link_name);?>">
							<?php echo $row->link_name;?>
						</a>
						<?php } else { echo $row->link_name; } ?>
				<?php } ?>
			</td>
			<?php if ($usertype !=2 ) { //teacher/admin table handler?>
				<?php if ($manage == 1) { ?>
					<td valign="middle" class="controltd"><?php if ($row->allow_up == 1 && $manage==1) { echo JLMS_orderIcon_element($topic_id, $course_id, 'orderup_element', _JLMS_TOPIC_I_MOVEUP, $row->ordering); } else { echo '&nbsp;';}?></td>
					<td valign="middle" class="controltd"><?php if ($row->allow_down == 1 && $manage==1) { echo JLMS_orderIcon_element($topic_id, $course_id, 'orderdown_element', _JLMS_TOPIC_I_MOVEDOWN, $row->ordering); } else { echo '&nbsp;';}?></td>
					<?php $state = publishUtility($row->published, $row->show) ?>
					<td valign="middle" class="controltd"><?php echo JLMS_showHideIcon($row->link_id, $course_id, $topic_id, $state, 'change_element', $option);?></td>
				<?php } ?>
				<td align="center">-</td>
				<td align="center">-</td>
			<?php } else { ?>
				<td align="center">-</td>
				<td align="center">-</td>
				<td align="center">-</td>
			<?php } ?>
			<td><?php
			$descr = strip_tags($row->link_description);
			if (strlen($descr) > 75) {
				$descr = substr($descr, 0, 75)."...";
			}
			echo $descr?$descr:'&nbsp;'; ?>
			</td>
		</tr>
		<?php
	}

	function showQuizRow (&$k, $row, $i, $topic_id, $checked=null, $manage=1, $span=1) {
		global $option, $Itemid, $JLMS_CONFIG;
		$usertype = $JLMS_CONFIG->get('current_usertype', 0);
		$course_id 	= intval( mosGetParam( $_REQUEST, 'id', 0 ) );
		$k = 3 - $k;
		if ($usertype == 1) {
			$link = sefRelToAbs('index.php?option='.$option.'&amp;Itemid='.$Itemid.'&amp;task=quizzes&amp;id='.$course_id.'&amp;page=setup_quest&amp;quiz_id='. $row->id);
		}
		if ($usertype == 2) {
			$link = sefRelToAbs('index.php?option='.$option.'&amp;Itemid='.$Itemid.'&amp;task=show_quiz&amp;id='.$course_id.'&amp;quiz_id='. $row->id);
			$manage = 0;
			$checked = null;
		}
		if (!$checked && $manage==1) $checked = mosHTML::idBox( $topic_id*1000+$i, $row->link_id);// 01.12.2007 '$i' chenged to '$topic_id*1000+$i' by DEN - to avoid dublicate id's
		?>
		<tr class="<?php echo JLMSCSS::_('sectiontableentry'.$k); ?>">
			<td width='1%' align="center"><?php if ($usertype != 2) echo ( $i + 1 ); else echo '&nbsp;'; ?></td>
			<?php if ($checked) echo "<td width='1%'>$checked</td>"; ?>
			<td width="1%"><img src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/tlb_quiz.png" alt='quiz' /></td>
			<td align="left" valign="middle" colspan="<?php echo $span; ?>">
				<a href="<?php echo $link; ?>" title="<?php echo str_replace('"','&quot;',$row->c_title);?>">
				<?php echo $row->c_title; ?>
				</a>
			</td>
			<?php if ($usertype !=2 ) { //teacher/admin table handler?>
				<?php if ($manage == 1) { ?>
					<td valign="middle" class="controltd"><?php if ($row->allow_up == 1 && $manage==1) { echo JLMS_orderIcon_element($topic_id, $course_id, 'orderup_element', _JLMS_TOPIC_I_MOVEUP, $row->ordering); } else { echo '&nbsp;';}?></td>
					<td valign="middle" class="controltd"><?php if ($row->allow_down == 1 && $manage==1) { echo JLMS_orderIcon_element($topic_id, $course_id, 'orderdown_element', _JLMS_TOPIC_I_MOVEDOWN, $row->ordering); } else { echo '&nbsp;';}?></td>
					<?php $state = publishUtility($row->published, $row->show); ?>
					<td valign="middle" class="controltd"><?php echo JLMS_showHideIcon($row->link_id, $course_id, $topic_id, $state, 'change_element', $option);?></td>
				<?php } ?>
				<td align="center">-</td>
				<td align="center">-</td>
			<?php } else { //user/student table handler?>
				<td align="center">
					<?php
					echo JLMS_showQuizStatus($row, 50);
					/* 
					if ($row->status == -1) {
						echo '<img class="JLMS_png" width="16" height="16" border="0" alt="Not Completed" src="'.$JLMS_CONFIG->get('live_site').'/components/com_joomla_lms/lms_images/toolbar/btn_cancel.png"/>';
					} elseif ($row->status == 0) {
						echo '<img class="JLMS_png" width="16" height="16" border="0" alt="Failed" src="'.$JLMS_CONFIG->get('live_site').'/components/com_joomla_lms/lms_images/toolbar/btn_cancel.png"/>';
					} elseif ($row->status == 1) {
						if (isset($row->link_certificate) && $row->link_certificate) {
							echo $row->link_certificate;
						} else {
							echo '<img class="JLMS_png" width="16" height="16" border="0" alt="Success" src="'.$JLMS_CONFIG->get('live_site').'/components/com_joomla_lms/lms_images/toolbar/btn_accept.png"/>';
						}
					}
					if ($row->points != -1) {
						echo '<br />'.$row->points;
					} else {
						//echo '<br /> - ';
					}
					*/
					?>
				</td>
				<td align="center" valign="middle" nowrap="nowrap"><?php echo (isset($row->start_date)) ? $row->start_date : '-'; ?></td>
				<td align="center" valign="middle" nowrap="nowrap"><?php echo (isset($row->end_date)) ? $row->end_date : '-'; ?></td>
			<?php } ?>
			<td><?php
			$descr = strip_tags($row->c_description);
			if (strlen($descr) > 75) {
				$descr = substr($descr, 0, 75)."...";
			}
			echo $descr?$descr:'&nbsp;'; ?>
			</td>
		</tr>		
		<?php
	}

	function showLPathRow (&$k, $row, $i, $topic_id, $checked=null, $manage=1, $span=1) {
		global $option, $Itemid, $my, $JLMS_DB, $Itemid, $JLMS_CONFIG, $JLMS_SESSION;
		$usertype = $JLMS_CONFIG->get('current_usertype', 0);
		$course_id 	= intval( mosGetParam( $_REQUEST, 'id', 0 ) );
		$k = 3 - $k;

		$is_hidden = false;
		if (isset($row->item_id) && $row->item_id) {
			$tmp_params = new JLMSParameters($row->lp_params);
			if ($tmp_params->get('hide_in_list',0) == 1) {
				$is_hidden = true;
			}
		}
		if ($usertype == 1) {
			$task = 'compose_lpath';
			$title = _JLMS_LPATH_LINK_TITLE_COMPOSE;
		} elseif ($usertype == 2) {
			$task = 'show_lpath';
			$title = _JLMS_LPATH_LINK_TITLE_VIEW;
			if ($is_hidden) {
				return 0;
			}
		}
		if (isset($row->is_hidden) && $row->is_hidden && ($usertype == 2)) {
			return 0;
		}
		$link 	= "index.php?option=".$option."&Itemid=".$Itemid."&task=".$task."&course_id=".$course_id."&id=". $row->id;
		$icon_img = "toolbar/tlb_lpath";
		$icon_alt = "learnpath";
		if ($row->item_id) {
			$title = _JLMS_LPATH_LINK_TITLE_SCORM;
			//$link = "index.php?option=".$option."&Itemid=".$Itemid."&task=player_scorm&course_id=".$course_id."&id=".$row->item_id."&lp_type=".$row->lp_type;
			$link = "index.php?option=".$option."&Itemid=".$Itemid."&task=show_lpath&course_id=".$course_id."&id=".$row->id;//."&lp_type=".$row->lp_type;
			$icon_img = "toolbar/tlb_scorm";
			$icon_alt = _JLMS_TOPIC_SCORM;
		}
		$title = $row->lpath_name;
		if (!$checked && $manage==1) $checked = mosHTML::idBox( $topic_id*1000+$i, $row->link_id);// 01.12.2007 '$i' chenged to '$topic_id*1000+$i' by DEN - to avoid dublicate id's
		$alt = ($row->published)?_JLMS_LPATH_STATUS_PUB:_JLMS_LPATH_STATUS_UNPUB;
		$image = ($row->published)?($is_hidden?'btn_publish_hidden.png':'btn_accept.png'):'btn_cancel.png';
		$state = ($row->published)?0:1;?>
		<tr class="<?php echo JLMSCSS::_('sectiontableentry'.$k); ?>">
			<td width='1%' valign="middle" align="center"><?php if ($usertype != 2) echo ( $i + 1 ); else echo '&nbsp;'; ?></td>
		<?php if ($usertype == 1) { ?>
			<?php if ($checked) {
					echo "<td width='1%'>";
						if(!isset($row->is_link))	
							echo $checked;
					echo "</td>"; 
			}?>
		<?php } ?>
			<td valign="middle" align="center" width="1%">				
					<img class='JLMS_png' src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/<?php echo $icon_img;?>.png" width='16' height='16' alt="<?php echo $icon_alt;?>" />
			</td>
			<td valign="middle" align="left" colspan="<?php echo $span; ?>">
			<?php if(!isset($row->is_link)) {
				$add_link_params = '';
				if (isset($row->scorm_params) && $row->scorm_params) {
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
				<a href="<?php echo sefRelToAbs($link);?>"<?php echo $add_link_params;?> title="<?php echo $title;?>">
			<?php }?>	
					<?php echo $row->lpath_name;?>
			<?php if(!isset($row->is_link)) {?>		
				</a>
			<?php }?>	
			</td>
		<?php if ($usertype !=2 ) { //teacher/admin table handler?>
			<?php if ($manage == 1) { ?>	
				<td valign="middle" class="controltd"><?php if ($row->allow_up == 1) { echo JLMS_orderIcon_element($topic_id, $course_id, 'orderup_element', _JLMS_TOPIC_I_MOVEUP, $row->ordering); } else { echo '&nbsp;';}?></td>
				<td valign="middle" class="controltd"><?php if ($row->allow_down == 1 && $manage==1) { echo JLMS_orderIcon_element($topic_id, $course_id, 'orderdown_element', _JLMS_TOPIC_I_MOVEDOWN, $row->ordering); } else { echo '&nbsp;';}?></td>
				<?php $state = publishUtility($row->published, $row->show); ?>
				<td valign="middle" class="controltd"><?php echo JLMS_showHideIcon($row->link_id, $course_id, $topic_id, $state, 'change_element', $option);?></td>
			<?php } ?>
			<td align="center">-</td>
			<td align="center">-</td>
		<?php } else { //user/student table handler
			$r_img = 'btn_cancel';
			$r_sta = _JLMS_LPATH_STU_LPSTATUS_NOTCOMPLETED;
			$r_start = '-';
			$r_end = '-';
			if (!$row->item_id) {
				if (isset($row->r_status) && $row->r_status == 1) {
					$r_img = 'btn_accept';
					$r_sta = _JLMS_LPATH_STU_LPSTATUS_COMPLETED;
					if ($row->r_start) $r_start = JLMS_dateToDisplay($row->r_start, false, $JLMS_CONFIG->get('offset')*60*60, '\<\b\\r \/>H:i:s');
					if ($row->r_end) $r_end = JLMS_dateToDisplay($row->r_end, false, $JLMS_CONFIG->get('offset')*60*60, '\<\b\\r \/>H:i:s');
				} elseif (isset($row->r_status) && $row->r_status == 0) {
					$r_img = 'btn_pending_cur';
					if ($row->r_start) $r_start = JLMS_dateToDisplay($row->r_start, false, $JLMS_CONFIG->get('offset')*60*60, '\<\b\\r \/>H:i:s');
				}
			} else {
				if (isset($row->s_status) && $row->s_status == 1) {
					$r_img = 'btn_accept';
					$r_sta = _JLMS_LPATH_STU_LPSTATUS_COMPLETED;
					$r_start = '-';
					$r_end = '-';
				}
				if ($row->lp_type == 1 || $row->lp_type == 2) {
					if (isset($row->r_end) && $row->r_end) $r_end = JLMS_dateToDisplay($row->r_end, true, $JLMS_CONFIG->get('offset')*60*60, '\<\b\\r \/>H:i:s');
					if (isset($row->r_start) && $row->r_start) $r_start = JLMS_dateToDisplay($row->r_start, true, $JLMS_CONFIG->get('offset')*60*60, '\<\b\\r \/>H:i:s');
				}
			}?>
			<td valign="middle" align="center" width="1%">
				<?php
				//Show Status Lapths/Scorms //by Max - 25.02.2011
				joomla_lms_html::ShowStatusAs($row);
				?>
			</td>
			<td valign="middle" align="center" nowrap="nowrap"><?php echo $r_start;?></td>
			<td valign="middle" align="center" nowrap="nowrap"><?php echo $r_end;?></td>
		<?php } ?>
			<td><?php
			$descr = strip_tags($row->lpath_shortdescription);
			if (strlen($descr) > 75) {
				$descr = substr($descr, 0, 75)."...";
			}
			echo $descr?$descr:'&nbsp;'; ?>
			</td>
		</tr>
		<?php
		return 1;
	}
	
	function showTopic($course_id, $topic_id, $row, $lists) {
		global $option, $Itemid, $JLMS_CONFIG;
		
		JLMS_TMPL::OpenMT();
		
		$hparams = array();
		$toolbar = array();
		$title = isset($row->id) ? $row->name : '';
		
		$toolbar[] = array('btn_type' => 'back', 'btn_js' => "javascript:submitbutton('cancel_topic');");
		
		JLMS_TMPL::ShowHeader('doc', $title, $hparams, $toolbar);
		
		JLMS_TMPL::OpenTS();
		?>
		<form action="<?php echo sefRelToAbs("index.php?option=".$option."&amp;Itemid=".$Itemid);?>" method="post" name="adminForm" enctype="multipart/form-data">
			<input type="hidden" name="option" value="<?php echo $option;?>" />
			<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
			<input type="hidden" name="task" value="details_course" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="weekly" value="0" />
			<input type="hidden" name="course_id" value="<?php echo $course_id;?>" />
			<input type="hidden" name="id" value="<?php echo $row->id;?>" />
		</form>
		<?php
		echo JLMS_ShowText_WithFeatures($row->description);
		?>
		<?php
		JLMS_TMPL::CloseTS();
		
		$_JLMS_PLUGINS = & JLMSFactory::getPlugins();
		$_JLMS_PLUGINS->loadBotGroup('system');
		$plugin_args = array();
		$plugin_args[] = $row->id;
		$_JLMS_PLUGINS->trigger('onBelowTopicPage', $plugin_args);
		
		JLMS_TMPL::CloseMT();
	}

	function editTopic ($course_id, $topic_id, $row, $lists) {
		global $option, $Itemid, $JLMS_CONFIG;
		
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
		var form=document.adminForm;

		try {
			form.onsubmit();
		} catch(e) {
			//alert(e);
		}

		if (is_start_c == 1) {if (form.start_date.value == ''){jlms_getDate('start');}}
		if (is_end_c == 1) {if (form.end_date.value == ''){jlms_getDate('end');}}
		if ((pressbutton=='save_topic') && (form.name.value=="" && $weekly==0)){alert("<?php echo _JLMS_TOPIC_T_NAME_NOT_SET;?>");
		} else {form.task.value = pressbutton;form.submit();}
	}
	var is_start_c = <?php echo ($row->publish_start)?'1':'0'; ?>; 
	var is_end_c = <?php echo ($row->publish_end)?'1':'0'; ?>;
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
	var $weekly=0;
	function jlms_Change_weekly() {
		showNameFields();
		var form=document.adminForm;
		$weekly = getNameFieldsCount();
		if ($weekly > 1) {
			$weekly = 0;
		} else {
			$weekly = 1;
		}
		
		if ($weekly == 1) {
			$weekly = 0;
			form.weekly.value = 0;
			form.publish_end.disabled = false;			
		} else {
			$weekly = 1;
			form.weekly.value = 1;
			is_end_c = 0;
			form.publish_end.checked = false;			
			form.publish_end.disabled = true;			
			form.endday.disabled = true;
			form.endmonth.disabled = true;
			form.endyear.disabled = true;
			is_start_c = 1
			form.publish_start.checked = true;
			form.startday.disabled = false;
			form.startmonth.disabled = false;
			form.startyear.disabled = false;
		}
	}
	function getNameFieldsCount() {
		var $select = document.adminForm.number;
		for ($i=0; $i<$select.length; $i++) {
			if ($select.options[$i].selected == true) {
				return $select.options[$i].value;
			}
		}
	}
	function showNameFields() {
		$count = getNameFieldsCount();
		for ($i=2; $i<=$count; $i++) {
			$div = document.getElementById('name_'+$i);
			$div.style.display = 'block';
		}
		for ( ; $i<=10; $i++) {
			$div = document.getElementById('name_'+$i);
			$div.style.display = 'none';
		}
	}	
	
	//--><!]]>
</script>
<?php
JLMS_TMPL::OpenMT();

$hparams = array();
$toolbar = array();
$title = '';

$title = $row->id ? _JLMS_TOPIC_T_EDIT : _JLMS_TOPIC_T_NEW;

$toolbar[] = array('btn_type' => 'save', 'btn_js' => "javascript:submitbutton('save_topic');");
$toolbar[] = array('btn_type' => 'cancel', 'btn_js' => "javascript:submitbutton('cancel_topic');");
JLMS_TMPL::ShowHeader('doc', $title, $hparams, $toolbar);

JLMS_TMPL::OpenTS();
?>
		<form action="<?php echo sefRelToAbs("index.php?option=".$option."&amp;Itemid=".$Itemid);?>" method="post" name="adminForm" enctype="multipart/form-data" onsubmit="setgood();">
			<table width="100%" cellpadding="0" cellspacing="0" border="0" id="jlms_item_properties">
				<tr>
					<td width="15%" valign="middle" style="vertical-align:middle"><?php echo _JLMS_TOPIC_T_NAME;?></td>
					<td><input class="inputbox" size="40" type="text" name="name" value="<?php echo str_replace('"','&quot;',$row->name);?>" />
						<?php 
						if ($topic_id == 0) echo $lists['names'];
						?>
					</td>
				</tr>
				<tr>
					<td valign="middle" style="vertical-align:middle"><br /><?php echo _JLMS_PUBLISHING;?></td>
					<td><br /><?php echo $lists['publishing'];?></td>
				</tr>
				<?php if ($topic_id == 0) { ?>
				<tr>
					<td><?php echo _JLMS_TOPIC_T_10_WEEKLY; ?></td>
					<td><?php echo $lists['number']; ?></td>
				</tr>
				<?php } ?>
				<tr>
					<td valign="middle" style="vertical-align:middle"><br /><?php echo _JLMS_ORDERING;?></td>
					<td><br /><?php echo $lists['ordering'];?></td>
				</tr>
				<tr>
					<td valign="middle"><br /><?php echo _JLMS_START_DATE;?></td>
					<td valign="middle" style="vertical-align:middle "><br />
						<table cellpadding="0" cellspacing="0" border="0" class="jlms_date_outer"><tr><td valign="middle"><input type="checkbox" value="1" name="publish_start" onclick="jlms_Change_start()" <?php echo $row->publish_start?'checked':'';?> /></td>
						<td valign="middle" style="vertical-align:middle ">
						<?php 
						$s_date = ($is_dis_start)?date('Y-m-d'):$row->start_date;
						echo JLMS_HTML::_('calendar.calendar',$s_date,'start','start');
						?>
						</td></tr></table>
					</td>
				</tr>	
				<tr>
					<td><br /><?php echo _JLMS_END_DATE;?></td>
					<td valign="middle" style="vertical-align:middle "><br />
						<table cellpadding="0" cellspacing="0" border="0" class="jlms_date_outer"><tr><td valign="middle"><input type="checkbox" value="1" name="publish_end" onclick="jlms_Change_end()" <?php echo $row->publish_end?'checked':'';?> /></td>
						<td valign="middle" style="vertical-align:middle ">
						<?php 
						$e_date = ($is_dis_end)?date('Y-m-d'):$row->end_date;
						echo JLMS_HTML::_('calendar.calendar',$e_date,'end','end');
						?>
						</td></tr></table>
					</td>
				</tr>
				<tr>
					<td width="15%" valign="top" style="vertical-align:top "><br /><?php echo _JLMS_IS_TIME_RELATED;?></td>
					<td><br />
						<?php JLMS_HTML::_('showperiod.field', $row->is_time_related, $row->show_period ) ?>
					</td>
				</tr>					
				<tr>
					<td colspan="2" valign="top" align="left" style="text-align:left "><br /><?php echo _JLMS_DESCRIPTION;?></td>
				</tr>	
				<tr>
					<td colspan="2">
						<?php JLMS_editorArea( 'editor1', $row->description, 'description', '100%;', '250', '40', '20' ) ; ?>
					</td>
				</tr>
			</table>
			<input type="hidden" name="option" value="<?php echo $option;?>" />
			<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
			<input type="hidden" name="task" value="details_course" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="weekly" value="0" />
			<input type="hidden" name="course_id" value="<?php echo $course_id;?>" />
			<input type="hidden" name="id" value="<?php echo $row->id;?>" />
		</form>
<?php
JLMS_TMPL::CloseTS();
JLMS_TMPL::CloseMT();
	}
	
}
/*
class JLMS_topic_toolbar
{
	function topic($course_id, $topic_id, $topic_pos, $topics_count, $publish_options) {
		global $JLMS_CONFIG;
		?>
		<table border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td>
				<?php
				echo JLMS_addElementIcon_topic($topic_id);
				echo JLMS_orderUpIcon_topic($topic_pos, $topic_id);
				echo JLMS_orderDownIcon_topic($topic_pos, $topics_count, $topic_id);
				echo JLMS_deleteIcon_topic($topic_id);
				echo JLMS_publishIcon_topic($topic_id, $course_id, $publish_options);
				echo JLMS_editTopicIcon($course_id, $topic_id);
				?>
				</td>
			</tr>
		</table>
		<?php
	}
}*/
?>