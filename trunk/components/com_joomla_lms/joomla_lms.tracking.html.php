<?php
/**
* joomla_lms.tracking.html.php
* (c) JoomaLMS eLearning Software http://www.joomlalms.com/
**/

// no direct access
defined( '_JLMS_EXEC' ) or die( 'Restricted access' );

class JLMS_tracking_html {
	function Echo_userinfo( $name, $username, $email, $link) {
		$ret_str = '';
		$title = JLMS_txt2overlib(_JLMS_USER_INFORMATION);
		$content = _JLMS_UI_USERNAME.' '.$username.'<br />'._JLMS_UI_NAME.' '.$name.'<br />'._JLMS_UI_EMAIL.' '.$email;
		return JLMS_toolTip($title, $content, $name, $link, '1', '30', true, 'jlms_ttip' );
	}

	function showTracking( $id, $option, &$rows, &$lists, &$track_images, &$latest_activities, $msg = '' ) {
		global $my;
		$JLMS_ACL = & JLMSFactory::getACL();
		$JLMS_CONFIG = & JLMSFactory::getConfig();
		$Itemid = $JLMS_CONFIG->get('Itemid');

		JLMS_TMPL::OpenMT();

		$hparams = array();
		//$hparams['second_tb_header'] = _JLMS_TRACK_TITLE_ACCESS.JLMS_TRACKING_getTitle(null);
		$toolbar = array();
		if ($JLMS_ACL->CheckPermissions('tracking', 'clear_stats') ) {
			$toolbar[] = array('btn_type' => 'clear', 'btn_js' => sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid&amp;task=track_clear&amp;id=$id") );
		}
		JLMS_TMPL::ShowHeader('tracking', _JLMS_TRACK_TITLE, $hparams, $toolbar);

		JLMS_TMPL::OpenTS();
?>
		<form action="<?php echo sefRelToAbs("index.php?option=$option&Itemid=$Itemid");?>" method="post" name="adminForm">
			<table width="100%" cellpadding="2" cellspacing="0" border="0" class="jlms_table_no_borders">
			<tr>
				<td width="65%" valign="top">
					<?php echo JLMSCSS::h2(_JLMS_TRACKING_LATEST_COURSE_ACTIVITIES);?>
					<table width="100%" cellpadding="0" cellspacing="0" border="0" class="<?php echo JLMSCSS::_('jlmslist');?>">
						<tr>
							<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>">
							<?php echo _JLMS_TRACK_TBL_H_STU;?>
							</<?php echo JLMSCSS::tableheadertag();?>>
							<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>">
							&nbsp;
							</<?php echo JLMSCSS::tableheadertag();?>>
							<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>">
							<?php echo _JLMS_TRACK_TBL_H_ACTIVITY;?>
							</<?php echo JLMSCSS::tableheadertag();?>>
							<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>">
							<?php echo _JLMS_TRACK_TBL_H_TIME;?>
							</<?php echo JLMSCSS::tableheadertag();?>>
						</tr>
<?php
$k = 1;
if (!count($latest_activities)) {
	echo '<tr class="'.JLMSCSS::_('sectiontableentry'.$k).'"><td colspan="4">'._JLMS_TRACKING_NO_STATISTICS.'</td></tr>';
}
foreach ($latest_activities as $latest_activity) {
	echo '<tr class="'.JLMSCSS::_('sectiontableentry'.$k).'">';
	echo '<td>'.$latest_activity->user.'</td>';
	echo '<td width="16"><img src="'.$JLMS_CONFIG->get('live_site').'/components/com_joomla_lms/lms_images/'.$latest_activity->icon.'" alt="i" width="16" height="16" border="0" /></td>';
	echo '<td>'.$latest_activity->activity.'</td>';
	echo '<td>'.JLMS_dateToDisplay($latest_activity->time, true, $JLMS_CONFIG->get('offset')*60*60, ' H:i:s').'</td>';
	echo '</tr>';
	$k = 3 - $k;
}
?>
					</table>
				</td>
				<td valign="top">
					<?php echo JLMSCSS::h2(_JLMS_TRACKING_STATISTICS_REPORTS);?>
					<table width="100%" cellpadding="0" cellspacing="0" border="0" class="<?php echo JLMSCSS::_('jlmslist');?>">
						<tr>
							<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" colspan="2">
							<?php echo _JLMS_TRACKING_STATISTICS_REPORTS;?>
							</<?php echo JLMSCSS::tableheadertag();?>>
						</tr>
						<tr class="<?php echo JLMSCSS::_('sectiontableentry1');?>">
							<td width="16"><img src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/tlb_tracking.png" alt="i" width="16" height="16" border="0" /></td>
							<td><a href="<?php echo JLMSRoute::_("index.php?option=$option&amp;Itemid=$Itemid&amp;task=tracking&amp;id=$id&amp;page=12");?>"><?php echo _JLMS_TRACKING_DOCUMENTS_STATISTICS;?></a></td>
						</tr>
						<tr class="<?php echo JLMSCSS::_('sectiontableentry2');?>">
							<td width="16"><img src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/tlb_tracking.png" alt="i" width="16" height="16" border="0" /></td>
							<td><a href="<?php echo JLMSRoute::_("index.php?option=$option&amp;Itemid=$Itemid&amp;task=tracking&amp;id=$id&amp;page=13");?>"><?php echo _JLMS_TRACKING_LPATHS_STATISTICS;?></a></td>
						</tr>
						<tr class="<?php echo JLMSCSS::_('sectiontableentry1');?>">
							<td width="16"><img src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/tlb_tracking.png" alt="i" width="16" height="16" border="0" /></td>
							<td><a href="<?php echo JLMSRoute::_("index.php?option=$option&amp;Itemid=$Itemid&amp;task=tracking&amp;id=$id&amp;page=14");?>"><?php echo _JLMS_TRACKING_LATEST_COURSE_ACTIVITIES_REPORT;?></a></td>
						</tr>
						<tr class="<?php echo JLMSCSS::_('sectiontableentry2');?>">
							<td width="16"><img src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/tlb_quiz.png" alt="i" width="16" height="16" border="0" /></td>
							<td><a href="<?php echo JLMSRoute::_("index.php?option=$option&amp;Itemid=$Itemid&amp;task=quizzes&amp;id=$id&amp;page=reports");?>"><?php echo _JLMS_TRACKING_QUIZZES_REPORT;?></a></td>
						</tr>
						<tr class="<?php echo JLMSCSS::_('sectiontableentry1');?>">
							<td width="16"><img src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/tlb_quiz.png" alt="i" width="16" height="16" border="0" /></td>
							<td><a href="<?php echo JLMSRoute::_("index.php?option=$option&amp;Itemid=$Itemid&amp;task=quizzes&amp;id=$id&amp;page=quiz_bars");?>"><?php echo _JLMS_TRACKING_QUIZZES_ANSWERS_STATISTICS;?></a></td>
						</tr>
					</table>
				</td>
			</tr>
			</table><br />
			<?php echo JLMSCSS::h2(_JLMS_TRACK_TITLE_ACCESS.JLMS_TRACKING_getTitle(null));?>
			<table width="100%" cellpadding="0" cellspacing="0" border="0" class="jlms_table_no_borders">
				<tr>
					<td align="right" style="text-align:right">
						<div align="right" style="white-space:nowrap">
						<?php
						$lists['filter_month'] = str_replace('<option value="0" selected="selected"></option>', '<option value="0" selected="selected">&nbsp;</option>', $lists['filter_month']);
						$lists['filter_month'] = str_replace('<option value="0"></option>', '<option value="0">&nbsp;</option>', $lists['filter_month']);
						?>
							<?php echo $lists['filter'];?>&nbsp;<?php echo $lists['filter_month'].'<br />';
								if(isset($lists['filter2'])) {
									echo ($lists['filter2'].'<br />');
								}	
								if(isset($lists['filter3'])) {
									echo $lists['filter3'].'<br />';
								}
								echo $lists['filter_stu'];
							?>
						</div>
					</td>
				</tr>
			</table>
	<?php $link = "index.php?option=$option&amp;Itemid=$Itemid&amp;task=tracking&amp;id=$id"; ?>
			<table width="100%" cellpadding="0" cellspacing="0" border="0" class="<?php echo JLMSCSS::_('jlmslist');?>">
				<tr>
					<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>"><?php echo _JLMS_TRACK_TBL_H_DATE;?></<?php echo JLMSCSS::tableheadertag();?>>
					<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center"><a href="<?php echo sefRelToAbs($link."&amp;page=1");?>" title="<?php echo _JLMS_TRACK_TBL_H_DOCS;?>"><?php echo _JLMS_TRACK_TBL_H_DOCS;?></a></<?php echo JLMSCSS::tableheadertag();?>>
					<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center"><a href="<?php echo sefRelToAbs($link."&amp;page=2");?>" title="<?php echo _JLMS_TRACK_TBL_H_LINKS;?>"><?php echo _JLMS_TRACK_TBL_H_LINKS;?></a></<?php echo JLMSCSS::tableheadertag();?>>
					<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center"><a href="<?php echo sefRelToAbs($link."&amp;page=3");?>" title="<?php echo _JLMS_TRACK_TBL_H_DROP;?>"><?php echo _JLMS_TRACK_TBL_H_DROP;?></a></<?php echo JLMSCSS::tableheadertag();?>>
					<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center"><a href="<?php echo sefRelToAbs($link."&amp;page=4");?>" title="<?php echo _JLMS_TRACK_TBL_H_LPATH;?>"><?php echo _JLMS_TRACK_TBL_H_LPATH;?></a></<?php echo JLMSCSS::tableheadertag();?>>
					<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center"><a href="<?php echo sefRelToAbs($link."&amp;page=5");?>" title="<?php echo _JLMS_TRACK_TBL_H_HW;?>"><?php echo _JLMS_TRACK_TBL_H_HW;?></a></<?php echo JLMSCSS::tableheadertag();?>>
					<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center"><a href="<?php echo sefRelToAbs($link."&amp;page=6");?>" title="<?php echo _JLMS_TRACK_TBL_H_ANNOUNC;?>"><?php echo _JLMS_TRACK_TBL_H_ANNOUNC;?></a></<?php echo JLMSCSS::tableheadertag();?>>
					<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center"><a href="<?php echo sefRelToAbs($link."&amp;page=7");?>" title="<?php echo _JLMS_TRACK_TBL_H_CONF;?>"><?php echo _JLMS_TRACK_TBL_H_CONF;?></a></<?php echo JLMSCSS::tableheadertag();?>>
					<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center"><a href="<?php echo sefRelToAbs($link."&amp;page=8");?>" title="<?php echo _JLMS_TRACK_TBL_H_CHAT;?>"><?php echo _JLMS_TRACK_TBL_H_CHAT;?></a></<?php echo JLMSCSS::tableheadertag();?>>
					<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center"><a href="<?php echo sefRelToAbs($link."&amp;page=9");?>" title="<?php echo _JLMS_TRACK_TBL_H_LPPLAY;?>"><?php echo _JLMS_TRACK_TBL_H_LPPLAY;?></a></<?php echo JLMSCSS::tableheadertag();?>>
					<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center"><a href="<?php echo sefRelToAbs($link."&amp;page=10");?>" title="<?php echo _JLMS_TRACK_TBL_H_FORUM;?>"><?php echo _JLMS_TRACK_TBL_H_FORUM;?></a></<?php echo JLMSCSS::tableheadertag();?>>
					<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center"><a href="<?php echo sefRelToAbs($link."&amp;page=11");?>" title="<?php echo _JLMS_TRACK_TBL_H_QUIZ;?>"><?php echo _JLMS_TRACK_TBL_H_QUIZ;?></a></<?php echo JLMSCSS::tableheadertag();?>>
					<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center"><?php echo _JLMS_TOTAL;?></<?php echo JLMSCSS::tableheadertag();?>>
				</tr>
			<?php
			$k = 1;
			$i = 0;
			$total_counts = array();
			for ($j=0;$j<11;$j++) {
				$total_counts[$j] = 0;
			}
			while ($i < count($rows)) {
				$row = $rows[$i];
				$subtotal = 0;
				$link = "index.php?option=$option&amp;Itemid=$Itemid&amp;task=tracking&amp;id=$id&amp;filter_month={$row->month}";
				?>
				<tr class="<?php echo JLMSCSS::_('sectiontableentry'.$k); ?>">
					<td align="left" valign="middle" style="vertical-align:middle ">
					<?php if ($lists['is_month']) {
							echo JLMS_dateToDisplay(mktime(0,0,0,$lists['month'],$row->month,$lists['year']), true);
						} else {
							echo '<a href="'.sefRelToAbs($link).'" title="'._JLMS_VIEW_DETAILS.'">'.month_lang(strftime("%m", mktime(0,0,0,$row->month+1,0,0)),0,2).'</a>';
						} ?>
					</td>
					<?php
					$j = 1;
					$month = $rows[$i]->month;
					$year = $rows[$i]->year;
					do {
						while ($j < $rows[$i]->page_id) {
							echo '<td align="center">0</td>';
							$j ++;
						}
						if ($rows[$i]->month == $month && $rows[$i]->year == $year) {
							echo '<td align="center">'.$rows[$i]->count_hits.'</td>';
							$total_counts[$j-1] = $total_counts[$j-1] + $rows[$i]->count_hits;
							$subtotal = $subtotal + $rows[$i]->count_hits;
						}
						$j ++;
						$i ++;
					} while($i < count($rows) && $rows[$i]->month == $month && $rows[$i]->year == $year);
					while ($j <=11) {
						echo '<td align="center">0</td>';
						$j ++;
					}
					echo '<td align="center"><strong>'.$subtotal.'</strong></td>';
					if (isset($rows[$i]->month) && $rows[$i]->month != $month) { $i --;}
					?>
				</tr>
				<?php
				$k = 3 - $k;
				$i ++;
			}?>
				<tr class="<?php echo JLMSCSS::_('sectiontableentry'.$k); ?>">
					<td align="left" valign="middle" style="vertical-align:middle "><?php echo _JLMS_TOTAL;?></td>
					<?php
					$subtotal = 0;
					for ($j=0;$j<11;$j++) {
						echo '<td align="center"><strong>'.$total_counts[$j].'</strong></td>';
						$subtotal = $subtotal + $total_counts[$j];
					}
					echo '<td align="center"><strong>'.$subtotal.'</strong></td>';
					?>
				</tr>
			</table><br />
			<?php
			if (!empty($track_images)) {
				echo "<table cellpadding='0' cellspacing='0' border='0' width='100%' class='jlms_table_no_borders'>";
				$i = 0;
				foreach ($track_images as $ti) {
					if ($i == 0) {
						echo "<tr><td align='center' valign='top'>";
					} elseif ($i == 2) {
						echo "</td><td align='center' valign='top'>";
					} elseif ($i == 3) {
						echo "</td></tr><tr><td colspan='2' align='center' valign='top'>";
					} elseif ($i == 4) {
						echo "</td></tr><tr><td colspan='2' align='center' valign='top'>";
					}
					?>
					<?php echo JLMSCSS::h2($ti->title);?>
					<img src="<?php echo $JLMS_CONFIG->get('live_site')."/".$JLMS_CONFIG->get('temp_folder', '')."/$ti->filename";?>" width="<?php echo $ti->width;?>" height="<?php echo $ti->height;?>" alt="<?php echo $ti->alt;?>" title="<?php echo $ti->alt;?>" border='0' />
				<?php
				if ($i == 4) {
					echo "</td></tr>";
				}
					$i ++;
				}
				if (count($track_images) == 4) {
					echo "</td></tr>";
				}
				echo "</table>";
			} elseif ($msg) {
				echo '<div class="joomlalms_sys_message">'.$msg.'</div>';
			}?>

		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
		<input type="hidden" name="task" value="tracking" />
		<input type="hidden" name="state" value="0" />
		<input type="hidden" name="id" value="<?php echo $id;?>" />
		</form>
<?php
		JLMS_TMPL::CloseTS();
		JLMS_TMPL::CloseMT();
	}
	function showpageTracking( $id, $option, &$rows, &$lists ) {
		$JLMS_CONFIG = & JLMSFactory::getConfig();
		$JLMS_SESSION = & JLMSFactory::getSession();
		$Itemid = $JLMS_CONFIG->get('Itemid');
		global $my;

		JLMS_TMPL::OpenMT();

		$hparams = array();
		
		$page_13_stats_shown = false;
		$page_12_stats_shown = false;
		$page_14_stats_shown = false;

		if ($lists['page'] == 12) {
			$hparams['second_tb_header'] = _JLMS_TRACKING_DOCUMENTS_STATISTICS;
		} elseif ($lists['page'] == 13) {
			$hparams['second_tb_header'] = _JLMS_TRACKING_LPATHS_STATISTICS;
		} elseif ($lists['page'] == 14) {
			$hparams['second_tb_header'] = _JLMS_TRACKING_LATEST_COURSE_ACTIVITIES_REPORT;
		} else {
			$hparams['second_tb_header'] = _JLMS_TRACK_TITLE_ACCESS.JLMS_TRACKING_getTitle($lists['page']);
		}
		$toolbar = array();
		$toolbar[] = array('btn_type' => 'back', 'btn_js' => sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=tracking&id=$id"));
		JLMS_TMPL::ShowHeader('tracking', _JLMS_TRACK_TITLE, $hparams, $toolbar);

		JLMS_TMPL::OpenTS();
?>
		<form action="<?php echo sefRelToAbs("index.php?option=$option&Itemid=$Itemid");?>" method="post" name="adminForm">
			<table width="100%" cellpadding="0" cellspacing="0" border="0" class="jlms_table_no_borders">
				<tr>
					<td align="right" style="text-align:right ">
						<div align="right" style="white-space:nowrap ">
						<?php if ($lists['page'] == 12 || $lists['page'] == 13 || $lists['page'] == 14) {
							//only group and user filters
							if(isset($lists['filter_lpath']) && $lists['page'] == 13){
								echo $lists['filter_lpath'].'<br />';
							}
							if(isset($lists['filter2'])) {
								echo ($lists['filter2'].'<br />');
							}	
							if(isset($lists['filter3'])) {
							 echo $lists['filter3'].'<br />';
							}
							echo $lists['filter_stu'].'<br />';
						} else { 
							echo ($lists['filter_pages'].$lists['filter'].$lists['filter_month'].$lists['filter_day']);?><br />
							<?php
							/*if(isset($lists['filter_lpath'])){
								echo $lists['filter_lpath'].'<br />';
							}*/
							if(isset($lists['filter2'])) {
								echo ($lists['filter2'].'<br />');
							}	
							if(isset($lists['filter3'])) {
							 echo $lists['filter3'].'<br />';
							}
							echo $lists['filter_stu'].'<br />';
						} ?>
						</div>
					</td>
				</tr>
			</table>
<?php if ($lists['page'] == 12 || $lists['page'] == 13 || $lists['page'] == 14) {
	//do nothing
} else {
	//show hits statistics
?>
	<?php $link = "index.php?option=$option&amp;Itemid=$Itemid&amp;task=tracking&amp;id=$id"; ?>
			<table width="100%" cellpadding="0" cellspacing="0" border="0" class="<?php echo JLMSCSS::_('jlmslist');?>">
				<tr>
					<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>"><?php echo _JLMS_TRACK_TBL_H_STU;?></<?php echo JLMSCSS::tableheadertag();?>>
					<?php
						$rr = array();
						if ($lists['is_day']) {
							foreach ($lists['months'] as $ma) {
								echo '<'.JLMSCSS::tableheadertag().' class="'.JLMSCSS::_('sectiontableheader').'" align="center">'.JLMS_dateToDisplay(mktime(0,0,0,$ma->month,$ma->day,$ma->year), true).'</'.JLMSCSS::tableheadertag().'>';
								$rr[] = $ma->month;
							}
						} else {
							foreach ($lists['months'] as $ma) {
								echo '<'.JLMSCSS::tableheadertag().' class="'.JLMSCSS::_('sectiontableheader').'" align="center">'.month_lang(strftime('%m',mktime(0,0,0,$ma->month+1,0,0)),0,2).', '.$ma->year.'</'.JLMSCSS::tableheadertag().'>';
								$rr[] = $ma->month;
							}
						}?>
				</tr>
			<?php
			$k = 1;
			$i = 0;
			$total_counts = array();
			for ($j=0;$j<count($lists['months']);$j++) {
				$total_counts[$j] = 0;
			}
			while ($i < count($rows)) {
				$row = $rows[$i];
				$link = '';
				$link = "index.php?option=$option&amp;Itemid=$Itemid&amp;task=tracking&amp;id=$id&amp;filter_stu=".$row->user_id."&amp;page=".$lists['page'];
				?>
				<tr class="<?php echo JLMSCSS::_('sectiontableentry'.$k); ?>">
					<td align="left" valign="middle" style="vertical-align:middle ">
						<a href="<?php echo sefRelToAbs($link);?>" title="<?php echo _JLMS_VIEW_DETAILS;?>">
							<?php echo $row->username;?>
						</a>
					</td>
					<?php
					#$j = $lists['month']-1;
					$e = 0;
					$user = $rows[$i]->user_id;
					#$month = $rows[$i]->month;
					#$year = $rows[$i]->year;
					do {
						if ($lists['is_day']) {
							while ( (($lists['months'][$e]->day != $rows[$i]->day) || ($lists['months'][$e]->month != $rows[$i]->month) || ($lists['months'][$e]->year != $rows[$i]->year)) && ($e < count($lists['months']))) {
								echo '<td align="center">0</td>';
								$e ++;
							}
							if ($rows[$i]->user_id == $user && $rows[$i]->day == $lists['months'][$e]->day && $rows[$i]->month == $lists['months'][$e]->month && $rows[$i]->year == $lists['months'][$e]->year) {
								echo '<td align="center">'.$rows[$i]->count_hits.'</td>';
								$total_counts[$e] = $total_counts[$e] + $rows[$i]->count_hits;
							}
						} else {
							while ( (($lists['months'][$e]->month != $rows[$i]->month) || ($lists['months'][$e]->year != $rows[$i]->year)) && $e < count($lists['months'])) {
								echo '<td align="center">0</td>';
								$e ++;
							}
							if ($rows[$i]->user_id == $user && $rows[$i]->month == $lists['months'][$e]->month && $rows[$i]->year == $lists['months'][$e]->year) {
								echo '<td align="center">'.$rows[$i]->count_hits.'</td>';
								$total_counts[$e] = $total_counts[$e] + $rows[$i]->count_hits;
							}
						}
						#$j ++;
						$e ++;
						$i ++;
					} while($i < count($rows) && $rows[$i]->user_id == $user);
					while ($e <count($lists['months'])) {
						echo '<td align="center">0</td>';
						$e ++;
					}
					if (isset($rows[$i]->user_id) && $rows[$i]->user_id != $user) { $i --;}
					?>
				</tr>
				<?php
				$k = 3 - $k;
				$i ++;
			}?>
				<tr class="<?php echo JLMSCSS::_('sectiontableentry'.$k); ?>">
					<td align="left" valign="middle" style="vertical-align:middle "><?php echo _JLMS_TOTAL;?></td>
					<?php
					for ($j=0;$j<count($lists['months']);$j++) {
						echo '<td align="center"><strong>'.$total_counts[$j].'</strong></td>';
					}
					?>
				</tr>
			</table>
			<br /><br />
<?php } ?>
		<?php if ($lists['page'] == 14 /*&& $lists['filter_stu_val']*/ && isset($lists['page14_stats']) && !empty($lists['page14_stats'])) {
			$page_14_stats_shown = true;
		?>
		
			<script type="text/javascript">
				function submitbutton(pressbutton){
					var form = document.adminForm;
					form.view.value = pressbutton;
					form.submit();
				}
			</script>
		
			<table width="100%" cellpadding="0" cellspacing="0" border="0" class="<?php echo JLMSCSS::_('jlmslist');?>">
				<tr>
					<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" width="20">#</<?php echo JLMSCSS::tableheadertag();?>>
					<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>">
					<?php echo _JLMS_TRACK_TBL_H_STU;?>
					</<?php echo JLMSCSS::tableheadertag();?>>
					<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>">
					&nbsp;
					</<?php echo JLMSCSS::tableheadertag();?>>
					<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>">
					<?php echo _JLMS_TRACK_TBL_H_ACTIVITY;?>
					</<?php echo JLMSCSS::tableheadertag();?>>
					<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>">
					<?php echo _JLMS_TRACK_TBL_H_TIME;?>
					</<?php echo JLMSCSS::tableheadertag();?>>
				</tr>
			<?php

			$limit = intval( mosGetParam( $_GET, 'limit', $JLMS_SESSION->get('list_limit', $JLMS_CONFIG->getCfg('list_limit')) ) );
			$JLMS_SESSION->set('list_limit', $limit);
			$limitstart = intval( mosGetParam( $_GET, 'limitstart', 0 ) );
			$total = count($lists['page14_stats']);
			require_once(_JOOMLMS_FRONT_HOME . DS . "includes" . DS . "classes" . DS . "lms.pagination.php");
			$pageNav = new JLMSPageNav( $total, $limitstart, $limit );

			$k = 1;
			$count_activities = 0;
			$items_counter = 0;
			foreach ($lists['page14_stats'] as $latest_activity) {
				if ($count_activities >= $pageNav->limitstart && $count_activities < ($pageNav->limitstart + $pageNav->limit)) {
					echo '<tr class="'.JLMSCSS::_('sectiontableentry'.$k).'">';
					echo '<td>';
						echo $pageNav->limitstart + $items_counter + 1;
					echo '</td>';
					echo '<td>'.$latest_activity->user.'</td>';
					echo '<td width="16"><img src="'.$JLMS_CONFIG->get('live_site').'/components/com_joomla_lms/lms_images/'.$latest_activity->icon.'" alt="i" width="16" height="16" border="0" /></td>';
					echo '<td>'.$latest_activity->activity.'</td>';
					echo '<td>'.JLMS_dateToDisplay($latest_activity->time, true, $JLMS_CONFIG->get('offset')*60*60, ' H:i:s').'</td>';
					echo '</tr>';
					$k = 3 - $k;
					$items_counter ++;
				}
				$count_activities ++ ;
			}
			?>
			<tr>
				<td colspan="5" align="center"class="<?php echo JLMSCSS::_('jlmslist-footer_td');?>">
					<div align="center">
					<?php
					$link = "index.php?option=$option&amp;Itemid=$Itemid&amp;task=tracking&amp;id=$id&amp;page=14";
					echo $pageNav->writePagesLinks( $link ); ?>
					</div>
				</td>
			</tr>
			</table>
			<table width="100%" cellpadding="0" cellspacing="0" class="jlms_table_no_borders">
			<tr>
				<td align="center">
					<?php
						$controls = array();
						$controls[] = array('href' => "javascript:submitbutton('csv');", 'title' => 'CSV', 'img' => 'csv');
						$controls[] = array('href' => "javascript:submitbutton('xls');", 'title' => 'XLS', 'img' => 'xls');
						JLMS_TMPL::ShowControlsFooterC($controls, '', false, false, _JLMS_EXPORT_TO.':');
					?>	
				</td>
			</tr>
			</table>
		<?php } elseif ($lists['page'] == 14 && !$page_14_stats_shown) {
					echo '<div class="joomlalms_user_message">'._JLMS_TRACKING_NO_STATISTICS.'</div>';
			} elseif ($lists['page'] == 12 && $lists['filter_stu_val'] && isset($lists['page12_stats']) && !empty($lists['page12_stats'])) {
			$page_12_stats_shown = true;
			$max_tree_width = 0; if (isset($lists['page12_stats'][0])) {$max_tree_width = $lists['page12_stats'][0]->tree_max_width;} ?>
			<table width="100%" cellpadding="0" cellspacing="0" border="0" class="<?php echo JLMSCSS::_('jlmslist');?>">
				<tr>
					<<?php echo JLMSCSS::tableheadertag();?> width="<?php echo (16*($max_tree_width + 1)); ?>" class="<?php echo JLMSCSS::_('sectiontableheader');?>" colspan="<?php echo ($max_tree_width + 1); ?>">&nbsp;</<?php echo JLMSCSS::tableheadertag();?>>
					<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" width="45%"><?php echo _JLMS_TRACK_TBL_DOC_NAME;?></<?php echo JLMSCSS::tableheadertag();?>>
					<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center"><?php echo _JLMS_TRACK_TBL_DOC_DOWNS;?></<?php echo JLMSCSS::tableheadertag();?>>
					<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center"><?php echo _JLMS_TRACK_TBL_DOC_LAST;?></<?php echo JLMSCSS::tableheadertag();?>>
				</tr>
			<?php
			$k = 1;
			$tree_modes = array();
			for ($i=0, $n=count($lists['page12_stats']); $i < $n; $i++) {
				$row_doc = $lists['page12_stats'][$i];
				$max_tree_width = $row_doc->tree_max_width; ?>
				<tr class="<?php echo JLMSCSS::_('sectiontableentry'.$k); ?>">
					<?php $add_img = '';
					if ($row_doc->tree_mode_num) {
						$g = 0;
						$tree_modes[$row_doc->tree_mode_num - 1] = $row_doc->tree_mode;
						while ($g < ($row_doc->tree_mode_num - 1)) {
							$pref = '';
							if (isset($tree_modes[$g]) && ($tree_modes[$g] == 2) ) { $pref = 'empty_'; }
							$add_img .= "<td width='16' valign='middle'><img src=\"".$JLMS_CONFIG->get('live_site')."/components/com_joomla_lms/lms_images/treeview/".$pref."line.png\" width='16' height='16' /></td>";
							$g ++;
						}
						$add_img .= "<td width='16' valign='middle'><img src=\"".$JLMS_CONFIG->get('live_site')."/components/com_joomla_lms/lms_images/treeview/sub".$row_doc->tree_mode.".png\" width='16' height='16' /></td>";
						$max_tree_width = $max_tree_width - $g - 1;
					}
					echo $add_img;?>
					<td align="center" valign="middle" style="vertical-align:middle " width='16'>
					<?php if ($row_doc->folder_flag == 1) {
						echo "<span style='alignment:center; font-weight:bold; vertical-align:middle;'><img class='JLMS_png' src=\"".$JLMS_CONFIG->get('live_site')."/components/com_joomla_lms/lms_images/folder.png\" width='16' height='16' alt='Folder' /></span>";
					} else {
						echo "<span style='alignment:center; font-weight:bold; vertical-align:middle;'><img class='JLMS_png' src=\"".$JLMS_CONFIG->get('live_site')."/components/com_joomla_lms/lms_images/files/".$row_doc->file_icon.".png\" width='16' height='16' alt='File' /></span>";
					}?>
					</td>
					<td align="left" valign="middle" <?php if ($max_tree_width > 0) { echo "colspan='".($max_tree_width + 1)."'";} ?> width="35%">
					<?php if ($row_doc->folder_flag || (!$row_doc->folder_flag && !$row_doc->file_id)) {
						echo '<strong>'.$row_doc->doc_name.'</strong>';
					} else {
						echo $row_doc->doc_name;
					} ?>
					</td>
					<td valign="middle" align="center">
						<?php echo $row_doc->downloads; ?>
					</td>
					<td valign="middle" align="center">
						<?php echo JLMS_dateToDisplay($row_doc->last_access, false, $JLMS_CONFIG->get('offset')*60*60, ' H:i:s'); ?>
					</td>
				</tr>
				<?php
				$k = 3 - $k;
			}
			?>
			</table>
		<?php } elseif ($lists['page'] == 12 && !$lists['filter_stu_val'] && isset($lists['page12_stats']) && !empty($lists['page12_stats'])) {
			$page_12_stats_shown = true;
			$max_tree_width = 0; if (isset($lists['page12_stats'][0])) {$max_tree_width = $lists['page12_stats'][0]->tree_max_width;} ?>
<script language="javascript" type="text/javascript">
<!--//--><![CDATA[//><!--
var block_stats = 0;
var tID = '';
var url_prefix = '<?php echo $JLMS_CONFIG->get('live_site') . "/index.php?tmpl=component&option=$option&Itemid=$Itemid&id=$id";?>';
function jlms_MakeRequest(url) {
	var http_request = false;
	if (window.ActiveXObject) { // IE
		try { http_request = new ActiveXObject("Msxml2.XMLHTTP");
		} catch (e) {
			try { http_request = new ActiveXObject("Microsoft.XMLHTTP");
			} catch (e) {}
		}
	} else if (window.XMLHttpRequest) { // Mozilla, Safari,...
		http_request = new XMLHttpRequest();
		if (http_request.overrideMimeType) {
			http_request.overrideMimeType('text/xml');
		}
	}
	if (!http_request) {
		return false;
	}
	http_request.onreadystatechange = function() { jlms_AnalizeRequest(http_request); };
	http_request.open('GET', url_prefix + url, true);
	http_request.send(null);
}
function jlms_AnalizeRequest(http_request) {
	if (http_request.readyState == 4) {
		if ((http_request.status == 200)) {
			block_stats = 0;
			if(http_request.responseXML.documentElement == null){
				try {
					http_request.responseXML.loadXML(http_request.responseText)
				} catch (e) {
					/*alert("Can't load");*/
				}
			}
			response  = http_request.responseXML.documentElement;
			var task = response.getElementsByTagName('task')[0].firstChild.data;
			switch (task) {
				case 'doc_xml':
					var response_data = response.getElementsByTagName('stats_table')[0].firstChild.data;
					var sec_indx = gl_el.parentNode.sectionRowIndex;
					var table = gl_el.parentNode.parentNode;
					var row = table.insertRow(sec_indx + 1);
					var cell1 = document.createElement("td");
					cell1.align = 'left';
					cell1.colSpan = "<?php echo ($max_tree_width + 5);?>";
					cell1.style.padding = '0px';
					cell1.style.margin = '0px';
					cell1.innerHTML = response_data;
					row.appendChild(cell1);
					gl_el.innerHTML = "<img class='JLMS_png' src='<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_accept.png' width='16' height='16' alt=\"done\" title=\"done\" />";
				break;
			}
		} else {
		}
	}
}
var gl_el = '';
function jlms_RequestDOCS_stats( doc_id, element ) {
	if (block_stats == 0) {
		block_stats = 1;
		gl_el = element.parentNode;
		jlms_MakeRequest('&task=get_docs_stats&doc_id='+doc_id+'&colspan=<?php echo ($max_tree_width + 2);?>');
		gl_el.innerHTML = "<img src='<?php echo $JLMS_CONFIG->get('live_site').'/'.$JLMS_CONFIG->get('ajax_settings_small_indicator'); ?>' width='16' height='16' alt=\"loading\" title=\"loading\" />";
	}
}
JLMS_preloadImages('<?php echo $JLMS_CONFIG->get('live_site').'/'.$JLMS_CONFIG->get('ajax_settings_small_indicator'); ?>', '<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_accept.png');
//--><!]]> 
</script>
			<table width="100%" cellpadding="0" cellspacing="0" border="0" class="<?php echo JLMSCSS::_('jlmslist');?>">
				<tr>
					<<?php echo JLMSCSS::tableheadertag();?> width="16" class="<?php echo JLMSCSS::_('sectiontableheader');?>">&nbsp;</<?php echo JLMSCSS::tableheadertag();?>>
					<<?php echo JLMSCSS::tableheadertag();?> width="<?php echo (16*($max_tree_width + 1)); ?>" class="<?php echo JLMSCSS::_('sectiontableheader');?>" colspan="<?php echo ($max_tree_width + 1); ?>">&nbsp;</<?php echo JLMSCSS::tableheadertag();?>>
					<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" width="45%"><?php echo _JLMS_TRACK_TBL_DOC_NAME;?></<?php echo JLMSCSS::tableheadertag();?>>
					<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" width="100" align="center"><?php echo _JLMS_TRACK_TBL_DOC_DOWNS;?></<?php echo JLMSCSS::tableheadertag();?>>
					<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" width="100" align="center"><?php echo _JLMS_TRACK_TBL_DOC_LAST;?></<?php echo JLMSCSS::tableheadertag();?>>
				</tr>
			<?php
			$k = 1;
			$tree_modes = array();
			for ($i=0, $n=count($lists['page12_stats']); $i < $n; $i++) {
				$row_doc = $lists['page12_stats'][$i];
				$max_tree_width = $row_doc->tree_max_width; ?>
				<tr class="<?php echo JLMSCSS::_('sectiontableentry'.$k); ?>">
					<td valign="middle" align="center">
					<?php if ($row_doc->folder_flag != 1) { ?>
						<span style="alignment:center; vertical-align:middle; cursor:pointer" onclick="jlms_RequestDOCS_stats(<?php echo $row_doc->id;?>, this);">
							<img class='JLMS_png' src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_pending_cur.png" width='16' height='16' alt="<?php echo _JLMS_TRACK_VIEW_DETAILS;?>" title="<?php echo _JLMS_TRACK_VIEW_DETAILS;?>" />
						</span>
					<?php } else { echo '&nbsp;'; } ?>
					</td>
					<?php $add_img = '';
					if ($row_doc->tree_mode_num) {
						$g = 0;
						$tree_modes[$row_doc->tree_mode_num - 1] = $row_doc->tree_mode;
						while ($g < ($row_doc->tree_mode_num - 1)) {
							$pref = '';
							if (isset($tree_modes[$g]) && ($tree_modes[$g] == 2) ) { $pref = 'empty_'; }
							$add_img .= "<td width='16' valign='middle'><img src=\"".$JLMS_CONFIG->get('live_site')."/components/com_joomla_lms/lms_images/treeview/".$pref."line.png\" width='16' height='16' alt='line' /></td>";
							$g ++;
						}
						$add_img .= "<td width='16' valign='middle'><img src=\"".$JLMS_CONFIG->get('live_site')."/components/com_joomla_lms/lms_images/treeview/sub".$row_doc->tree_mode.".png\" width='16' height='16' alt='sub' /></td>";
						$max_tree_width = $max_tree_width - $g - 1;
					}
					echo $add_img;?>
					<td align="center" valign="middle" style="vertical-align:middle " width='16'>
					<?php if ($row_doc->folder_flag == 1) {
						echo "<span style='alignment:center; font-weight:bold; vertical-align:middle;'><img class='JLMS_png' src=\"".$JLMS_CONFIG->get('live_site')."/components/com_joomla_lms/lms_images/folder.png\" width='16' height='16' alt='Folder' /></span>";
					} else {
						echo "<span style='alignment:center; font-weight:bold; vertical-align:middle;'><img class='JLMS_png' src=\"".$JLMS_CONFIG->get('live_site')."/components/com_joomla_lms/lms_images/files/".$row_doc->file_icon.".png\" width='16' height='16' alt='File' /></span>";
					}?>
					</td>
					<td align="left" valign="middle" <?php if ($max_tree_width > 0) { echo "colspan='".($max_tree_width + 1)."'";} ?> width="35%">
					<?php if ($row_doc->folder_flag || (!$row_doc->folder_flag && !$row_doc->file_id)) {
						echo '<strong>'.$row_doc->doc_name.'</strong>';
					} else {
						echo $row_doc->doc_name;
					} ?>
					</td>
					<td valign="middle" align="center">
						<?php echo $row_doc->downloads; ?>
					</td>
					<td valign="middle" align="center">
						<?php echo JLMS_dateToDisplay($row_doc->last_access, false, $JLMS_CONFIG->get('offset')*60*60, ' H:i:s'); ?>
					</td>
				</tr>
				<?php
				$k = 3 - $k;
			}
			?>
			</table>
		<?php } elseif ($lists['page'] == 12 && !$page_12_stats_shown) {
				echo '<div class="joomlalms_user_message">'._JLMS_TRACKING_NO_STATISTICS.'</div>';
			} elseif ($lists['page'] == 13 && $lists['filter_stu_val'] && isset($lists['page13_stats']) && !empty($lists['page13_stats'])){
				$page_13_stats_shown = true; ?>
<script language="javascript" type="text/javascript">
<!--//--><![CDATA[//><!--

function submitbutton(pressbutton){
	var form = document.adminForm;
	form.view.value = pressbutton;
	form.submit();
}

var block_stats = 0;
var tID = '';
var url_prefix = '<?php echo $JLMS_CONFIG->get('live_site') . "/index.php?tmpl=component&option=$option&Itemid=$Itemid&id=$id";?>';
function jlms_MakeRequest(url) {
	var http_request = false;
	if (window.XMLHttpRequest) { // Mozilla, Safari,...
		http_request = new XMLHttpRequest();
		if (http_request.overrideMimeType) {
			http_request.overrideMimeType('text/xml');
		}
	} else if (window.ActiveXObject) { // IE
		try { http_request = new ActiveXObject("Msxml2.XMLHTTP");
		} catch (e) {
			try { http_request = new ActiveXObject("Microsoft.XMLHTTP");
			} catch (e) {}
		}
	}
	if (!http_request) {
		return false;
	}
	http_request.onreadystatechange = function() { jlms_AnalizeRequest(http_request); };
	http_request.open('GET', url_prefix + url, true);
	http_request.send(null);
}
function jlms_AnalizeRequest(http_request) {
	if (http_request.readyState == 4) {
		if ((http_request.status == 200)) {
			block_stats = 0;
			if(http_request.responseXML.documentElement == null){
				try {
					http_request.responseXML.loadXML(http_request.responseText)
				} catch (e) {
					/*alert("Can't load");*/
				}
			}
			response  = http_request.responseXML.documentElement;
			var task = response.getElementsByTagName('task')[0].firstChild.data;
			switch (task) {
				case 'lpath_xml':
					var response_data = response.getElementsByTagName('stats_table')[0].firstChild.data;
					if (response_data && response_data != '' && response_data != ' ') {
						var sec_indx = gl_el.parentNode.sectionRowIndex;
						var table = gl_el.parentNode.parentNode;
						var row = table.insertRow(sec_indx + 1);
						var cell1 = document.createElement("td");
						cell1.align = 'left';
						cell1.colSpan = "3";
						cell1.style.padding = '0px';
						cell1.style.margin = '0px';
						cell1.innerHTML = response_data;
						row.appendChild(cell1);
					}
					gl_el.innerHTML = "<img class='JLMS_png' src='<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_accept.png' width='16' height='16' alt=\"done\" title=\"done\" />";
				break;
				case 'scorm_progress_xml':
					var response_data = response.getElementsByTagName('stats_table')[0].firstChild.data;
					var res_user = response.getElementsByTagName('user_id')[0].firstChild.data;
					var res_lpsc = response.getElementsByTagName('lpath_id')[0].firstChild.data;
					if (response_data && response_data != '' && response_data != ' ') {
						gl_el_res = document.getElementById('sc_progress_td_'+res_user+'_'+res_lpsc);
						if (gl_el_res) {
							gl_el_res.innerHTML = response_data;
						}
					}
				break;
			}
		} else {
		}
	}
}
var gl_el = '';
function jlms_RequestLP_stats( lp_id, element ) {
	if (block_stats == 0) {
		block_stats = 1;
		gl_el = element.parentNode;
		jlms_MakeRequest('&task=get_lp_stats&lpath_id='+lp_id);
		gl_el.innerHTML = "<img src='<?php echo $JLMS_CONFIG->get('live_site').'/'.$JLMS_CONFIG->get('ajax_settings_small_indicator'); ?>' width='16' height='16' alt=\"loading\" title=\"loading\" />";
	}
}
function ChangeScormProgress( user_id, sc_id, new_stat, element) {
	if (block_stats == 0) {
		block_stats = 1;
		gl_el = document.getElementById('sc_progress_td_'+user_id+'_'+sc_id);
		jlms_MakeRequest('&task=get_lp_stats&mode=scormstatus&user_id='+user_id+'&nstat='+new_stat+'&lpath_id='+sc_id);
		gl_el.innerHTML = "<img src='<?php echo $JLMS_CONFIG->get('live_site').'/'.$JLMS_CONFIG->get('ajax_settings_small_indicator'); ?>' width='16' height='16' alt=\"loading\" title=\"loading\" />";
	}
}
JLMS_preloadImages('<?php echo $JLMS_CONFIG->get('live_site').'/'.$JLMS_CONFIG->get('ajax_settings_small_indicator'); ?>', '<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_accept.png', '<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_cancel.png');
//--><!]]> 
</script>
			<table width="100%" cellpadding="0" cellspacing="0" border="0" class="<?php echo JLMSCSS::_('jlmslist');?>">
				<tr>
					<<?php echo JLMSCSS::tableheadertag();?> width="16" class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center">&nbsp;</<?php echo JLMSCSS::tableheadertag();?>>
					<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>"><?php echo _JLMS_LPATH_TBL_HEAD_NAME;?></<?php echo JLMSCSS::tableheadertag();?>>
					<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" width="1" colspan="3">&nbsp;</<?php echo JLMSCSS::tableheadertag();?>>
					<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center"><?php echo _JLMS_LPATH_TBL_TIME_SPENT;?></<?php echo JLMSCSS::tableheadertag();?>>
					<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center"><?php echo _JLMS_LPATH_TBL_STARTING;?></<?php echo JLMSCSS::tableheadertag();?>>
					<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center"><?php echo _JLMS_LPATH_TBL_ENDING;?></<?php echo JLMSCSS::tableheadertag();?>>
				</tr>
			<?php
			$k = 1;
			for ($i=0, $n=count($lists['page13_stats']); $i < $n; $i++) {
				$row_path = $lists['page13_stats'][$i];
				$icon_img = "toolbar/tlb_lpath";
				$icon_alt = "learnpath";
				if ($row_path->item_id) {
					$icon_img = "toolbar/tlb_scorm";
					$icon_alt = "scorm";
				}?>
				<tr class="<?php echo JLMSCSS::_('sectiontableentry'.$k); ?>">
					<td valign="middle" align="center">
						&nbsp;
					</td>
					<td valign="middle" align="left">
						<?php
						if($lists['f_lpath']){
							echo JLMS_tracking_html::Echo_userinfo($row_path->u_name, $row_path->username, $row_path->email, 'javascript:void(0);');	
						} else {
							echo $row_path->lpath_name;
						}
						?>
					</td>
					<?php
					if($lists['f_lpath']){
					?>
						<td width="1%" style="white-space: nowrap;"><?php echo JLMS_getLpathProgress($row_path, $row_path->user_id ? $row_path->user_id : 0); ?></td>
					<?php
					} else {
					?>
						<td width="1%" style="white-space: nowrap;"><?php echo JLMS_getLpathProgress($row_path, $lists['filter_stu_val']); ?></td>
					<?php
					}
					?>
				<?php
					$r_img = 'btn_cancel';
					$r_sta = _JLMS_LPATH_STU_LPSTATUS_NOTCOMPLETED;
					$time_spent = $row_path->time_spent;
					$r_start = '-';
					$r_end = '-';
					$new_s_status = '1';
					if (!$row_path->item_id) {
						if (isset($row_path->r_status) && $row_path->r_status == 1) {
							$r_img = 'btn_accept';
							$r_sta = _JLMS_LPATH_STU_LPSTATUS_COMPLETED;
							$r_start = isset($row_path->r_start) && $row_path->r_start?JLMS_dateToDisplay($row_path->r_start, false, $JLMS_CONFIG->get('offset')*60*60, '\<\b\\r \/>H:i:s'):'-';
							$r_end = isset($row_path->r_start) && $row_path->r_start?JLMS_dateToDisplay($row_path->r_end, false, $JLMS_CONFIG->get('offset')*60*60, '\<\b\\r \/>H:i:s'):'-';
						} elseif (isset($row_path->r_status) && $row_path->r_status == 0) {
							$r_img = 'btn_pending_cur';
							$r_start = isset($row_path->r_start) && $row_path->r_start?JLMS_dateToDisplay($row_path->r_start, false, $JLMS_CONFIG->get('offset')*60*60, '\<\b\\r \/>H:i:s'):'-';
						}
					} else {
						if (isset($row_path->s_status) && $row_path->s_status == 1) {
							$r_img = 'btn_accept';
							$r_sta = _JLMS_LPATH_STU_LPSTATUS_COMPLETED;
							$r_start = '-';
							$r_end = '-';
							$new_s_status = '0';
						}
						if ($row_path->lp_type == 1 || $row_path->lp_type == 2) {
							$r_end = isset($row_path->r_end) && $row_path->r_end ? JLMS_dateToDisplay($row_path->r_end, true, $JLMS_CONFIG->get('offset')*60*60, '\<\b\\r \/>H:i:s'):'-';
							$r_start = isset($row_path->r_start) && $row_path->r_start?JLMS_dateToDisplay($row_path->r_start, true, $JLMS_CONFIG->get('offset')*60*60, '\<\b\\r \/>H:i:s'):'-';
						}
					} ?>
					<?php if ($row_path->item_id) {?>
					<td valign="middle" align="center" width="16" id="sc_progress_td_<?php echo $lists['filter_stu_val']."_".$row_path->item_id;?>">
						<a class="jlms_img_link" id="sc_progress_a_<?php echo $lists['filter_stu_val']."_".$row_path->item_id;?>" href="javascript:ChangeScormProgress(<?php echo $lists['filter_stu_val'];?>,<?php echo $row_path->item_id;?>,<?php echo $new_s_status;?>,this);"><img id="sc_progress_img_<?php echo $lists['filter_stu_val']."_".$row_path->item_id;?>" align="absmiddle" class="JLMS_png" src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/<?php echo $r_img;?>.png" width="16" height="16" border="0" alt="<?php echo $r_sta;?>" title="<?php echo $r_sta;?>" /></a>
					<?php } else { ?>
					<td valign="middle" align="center" width="16">
						<img class='JLMS_png' src='<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/<?php echo $r_img;?>.png' width='16' height='16' alt="<?php echo $r_sta;?>" title="<?php echo $r_sta;?>" />
					<?php } ?>
					</td>
					<td valign="middle" align="center" nowrap="nowrap">
						<?php echo $row_path->item_id ? (isset($row_path->s_score)?($row_path->s_score._JLMS_GB_POINTS):'&nbsp;') : '&nbsp;'; ?>
					</td>
					<td valign="middle" align="center" nowrap="nowrap"><?php echo $time_spent;?></td>
					<td valign="middle" align="center" nowrap="nowrap"><?php echo $r_start;?></td>
					<td valign="middle" align="center" nowrap="nowrap"><?php echo $r_end;?></td>
				</tr>
				<?php
				$k = 3 - $k;
			}
			?>
			</table>
			<table width="100%" cellpadding="0" cellspacing="0" class="jlms_table_no_borders">
				<tr>
					<td align="center">
						<?php
							$controls = array();
							$controls[] = array('href' => "javascript:submitbutton('csv');", 'title' => 'CSV', 'img' => 'csv');
							$controls[] = array('href' => "javascript:submitbutton('xls');", 'title' => 'XLS', 'img' => 'xls');
							JLMS_TMPL::ShowControlsFooterC($controls, '', false, false, _JLMS_EXPORT_TO.':');
						?>	
					</td>
				</tr>
			</table>
		<?php 
		} elseif ($lists['page'] == 13 && !$lists['filter_stu_val'] && isset($lists['page13_stats']) && !empty($lists['page13_stats'])){
			$page_13_stats_shown = true; 
		?>
<script language="javascript" type="text/javascript">
<!--//--><![CDATA[//><!--
var block_stats = 0;
var tID = '';
var url_prefix = '<?php echo $JLMS_CONFIG->get('live_site') . "/index.php?tmpl=component&option=$option&Itemid=$Itemid&id=$id";?>';
function jlms_MakeRequest(url) {
	var http_request = false;
	if (window.XMLHttpRequest) { // Mozilla, Safari,...
		http_request = new XMLHttpRequest();
		if (http_request.overrideMimeType) {
			http_request.overrideMimeType('text/xml');
		}
	} else if (window.ActiveXObject) { // IE
		try { http_request = new ActiveXObject("Msxml2.XMLHTTP");
		} catch (e) {
			try { http_request = new ActiveXObject("Microsoft.XMLHTTP");
			} catch (e) {}
		}
	}
	if (!http_request) {
		return false;
	}
	http_request.onreadystatechange = function() { jlms_AnalizeRequest(http_request); };
	http_request.open('GET', url_prefix + url, true);
	http_request.send(null);
}
function jlms_AnalizeRequest(http_request) {
	if (http_request.readyState == 4) {
		if ((http_request.status == 200)) {
			block_stats = 0;
			if(http_request.responseXML.documentElement == null){
				try {
					http_request.responseXML.loadXML(http_request.responseText)
				} catch (e) {
					/*alert("Can't load");*/
				}
			}
			response  = http_request.responseXML.documentElement;
			var task = response.getElementsByTagName('task')[0].firstChild.data;
			switch (task) {
				case 'lpath_xml':
					var response_data = response.getElementsByTagName('stats_table')[0].firstChild.data;
					if (response_data && response_data != '' && response_data != ' ') {
						var sec_indx = gl_el.parentNode.sectionRowIndex;
						var table = gl_el.parentNode.parentNode;
						var row = table.insertRow(sec_indx + 1);
						var cell1 = document.createElement("td");
						cell1.align = 'left';
						cell1.colSpan = "3";
						cell1.style.padding = '0px';
						cell1.style.margin = '0px';
						cell1.innerHTML = response_data;
						row.appendChild(cell1);
					}
					gl_el.innerHTML = "<img class='JLMS_png' src='<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_accept.png' width='16' height='16' alt=\"done\" title=\"done\" />";
				break;
				case 'scorm_progress_xml':
					var response_data = response.getElementsByTagName('stats_table')[0].firstChild.data;
					var res_user = response.getElementsByTagName('user_id')[0].firstChild.data;
					var res_lpsc = response.getElementsByTagName('lpath_id')[0].firstChild.data;
					if (response_data && response_data != '' && response_data != ' ') {
						gl_el_res = document.getElementById('sc_progress_td_'+res_user+'_'+res_lpsc);
						if (gl_el_res) {
							gl_el_res.innerHTML = response_data;
						}
					}
				break;
			}
		} else {
		}
	}
}
var gl_el = '';
function jlms_RequestLP_stats( lp_id, element ) {
	if (block_stats == 0) {
		block_stats = 1;
		gl_el = element.parentNode;
		jlms_MakeRequest('&task=get_lp_stats&lpath_id='+lp_id);
		gl_el.innerHTML = "<img src='<?php echo $JLMS_CONFIG->get('live_site').'/'.$JLMS_CONFIG->get('ajax_settings_small_indicator'); ?>' width='16' height='16' alt=\"loading\" title=\"loading\" />";
	}
}
function ChangeScormProgress( user_id, sc_id, new_stat, element) {
	if (block_stats == 0) {
		block_stats = 1;
		gl_el = document.getElementById('sc_progress_td_'+user_id+'_'+sc_id);
		jlms_MakeRequest('&task=get_lp_stats&mode=scormstatus&user_id='+user_id+'&nstat='+new_stat+'&lpath_id='+sc_id);
		gl_el.innerHTML = "<img src='<?php echo $JLMS_CONFIG->get('live_site').'/'.$JLMS_CONFIG->get('ajax_settings_small_indicator'); ?>' width='16' height='16' alt=\"loading\" title=\"loading\" />";
	}
}
JLMS_preloadImages('<?php echo $JLMS_CONFIG->get('live_site').'/'.$JLMS_CONFIG->get('ajax_settings_small_indicator'); ?>', '<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_accept.png');
//--><!]]> 
</script>
			<table width="100%" cellpadding="0" cellspacing="0" border="0" class="<?php echo JLMSCSS::_('jlmslist');?>">
				<tr>
					<<?php echo JLMSCSS::tableheadertag();?> width="16" class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center">&nbsp;</<?php echo JLMSCSS::tableheadertag();?>>
					<<?php echo JLMSCSS::tableheadertag();?> width="16" class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center">&nbsp;</<?php echo JLMSCSS::tableheadertag();?>>
					<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" width="100%"><?php echo _JLMS_LPATH_TBL_HEAD_NAME;?></<?php echo JLMSCSS::tableheadertag();?>>
				</tr>
			<?php
			$k = 1;
			for ($i=0, $n=count($lists['page13_stats']); $i < $n; $i++) {
				$row_path = $lists['page13_stats'][$i];
				$icon_img = "toolbar/tlb_lpath";
				$icon_alt = "learnpath";
				if ($row_path->item_id) {
					$icon_img = "toolbar/tlb_scorm";
					$icon_alt = "scorm";
				}?>
				<tr class="<?php echo JLMSCSS::_('sectiontableentry'.$k); ?>">
					<td valign="middle" align="center">
					<?php if(!isset($row_path->is_link)) {?>
						<span style="alignment:center; vertical-align:middle; cursor:pointer" onclick="jlms_RequestLP_stats(<?php echo $row_path->id;?>, this);">
							<img class='JLMS_png' src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_pending_cur.png" width='16' height='16' alt="<?php echo _JLMS_TRACK_VIEW_DETAILS;?>" title="<?php echo _JLMS_TRACK_VIEW_DETAILS;?>" />
						</span>	
					<?php }?>	
					</td>
					<td valign="middle" align="center">
						<span style="alignment:center; vertical-align:middle">
							<img class='JLMS_png' src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/<?php echo $icon_img;?>.png" width='16' height='16' alt="<?php echo $icon_alt;?>" />
						</span>
					</td>
					<td valign="middle" align="left">
						<?php echo $row_path->lpath_name;?>
					</td>
				</tr>
				<?php
				$k = 3 - $k;
			}
			?>
			</table>
		<?php 
		} elseif($lists['page'] == 13 && !$page_13_stats_shown){
			echo '<div class="joomlalms_user_message">'._JLMS_TRACKING_NO_STATISTICS.'</div>';
		} elseif ($lists['page'] == 5 && $lists['filter_stu_val'] && isset($lists['page5_stats'])){
		?>
			<br /><br />
			<table width="100%" cellpadding="0" cellspacing="0" border="0" class="<?php echo JLMSCSS::_('jlmslist');?>">
				<tr>
					<<?php echo JLMSCSS::tableheadertag();?> width="20" class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center">#</<?php echo JLMSCSS::tableheadertag();?>>
					<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" width="30%"><?php echo _JLMS_HW_TBL_HEAD_HW;?></<?php echo JLMSCSS::tableheadertag();?>>
					<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>"><?php echo _JLMS_HW_TBL_HEAD_DATE;?></<?php echo JLMSCSS::tableheadertag();?>>
					<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>"><?php echo _JLMS_HW_TBL_HEAD_ENDDATE;?></<?php echo JLMSCSS::tableheadertag();?>>
					<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" width="1">&nbsp;</<?php echo JLMSCSS::tableheadertag();?>>
				</tr>
			<?php
			$k = 1;
			for ($i=0, $n=count($lists['page5_stats']); $i < $n; $i++) {
				$row_hw = $lists['page5_stats'][$i];
				$alt = ($row_hw->hw_status)?_JLMS_HW_STATUS_COMPLETED:_JLMS_HW_STATUS_INCOMPLETED;
				$image = ($row_hw->hw_status)?'btn_accept.png':'btn_cancel.png';?>
				<tr class="<?php echo JLMSCSS::_('sectiontableentry'.$k); ?>">
					<td align="center"><?php echo ( $i + 1 ); ?></td>
					<td align="left" valign="middle" style="vertical-align:middle ">
						<?php echo $row_hw->hw_name;?>
					</td>
					<td valign="middle" style="vertical-align:middle ">
						<?php echo $row_hw->post_date;?>
					</td>
					<td valign="middle" style="vertical-align:middle ">
						<?php echo $row_hw->end_date;?>
					</td>
					<td align="left" valign="middle" style="vertical-align:middle; text-align:left; white-space:nowrap " nowrap="nowrap">
						<?php
						echo '<img align="absmiddle" class="JLMS_png" src="'.$JLMS_CONFIG->get('live_site').'/components/com_joomla_lms/lms_images/toolbar/'.$image.'" width="16" height="16" border="0" alt="'.$alt.'" />';
						if ($row_hw->hw_status) {
							echo '&nbsp;&nbsp;'.JLMS_dateToDisplay($row_hw->hw_date, false, $JLMS_CONFIG->get('offset')*60*60, ' H:i:s');
						}
						?>
					</td>
				</tr>
				<?php
				$k = 3 - $k;
			}
			?>
			</table>
		<?php } elseif ($lists['page'] == 11 && $lists['filter_stu_val'] && isset($lists['page11_stats']) && !empty($lists['page11_stats'])) { ?>
			<br /><br />
			<table width="100%" cellpadding="0" cellspacing="0" border="0" class="<?php echo JLMSCSS::_('jlmslist');?>">
				<tr>
					<<?php echo JLMSCSS::tableheadertag();?> width="16" class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center">&nbsp;</<?php echo JLMSCSS::tableheadertag();?>>
					<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>"><?php echo _JLMS_LPATH_TBL_HEAD_NAME;?></<?php echo JLMSCSS::tableheadertag();?>>
					<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>">&nbsp;</<?php echo JLMSCSS::tableheadertag();?>>
					<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" width="1" colspan="2">&nbsp;</<?php echo JLMSCSS::tableheadertag();?>>
					
				</tr>
			<?php
			$k = 1;
			for ($i=0, $n=count($lists['page11_stats']); $i < $n; $i++) {
				$row_path = $lists['page11_stats'][$i];
				$icon_img = "toolbar/tlb_lpath";
				$icon_alt = "learnpath";
				if ($row_path->c_id) {
					$icon_img = "toolbar/tlb_scorm";
					$icon_alt = "scorm";
				}?>
				<tr class="<?php echo JLMSCSS::_('sectiontableentry'.$k); ?>">
					<td valign="middle" align="center">
						<span style="alignment:center; vertical-align:middle">
							<img class='JLMS_png' src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/<?php echo $icon_img;?>.png" width='16' height='16' alt="<?php echo $icon_alt;?>" />
						</span>
					</td>
					<td valign="middle" align="left">
							<?php echo $row_path->c_title;?>
					</td>
					
				<?php
					$r_img = 'btn_cancel';
					$r_sta = _JLMS_LPATH_STU_LPSTATUS_NOTCOMPLETED;
					$r_start = '-';
					$r_end = '-';
					if (!$row_path->c_id) {
						if (isset($row_path->published) && $row_path->published == 1) {
							$r_img = 'btn_accept';
							$r_sta = _JLMS_LPATH_STU_LPSTATUS_COMPLETED;
							$r_start = $row_path->r_start?JLMS_dateToDisplay($row_path->r_start, false, $JLMS_CONFIG->get('offset')*60*60, '\<\b\\r \/>H:i:s'):'-';
							$r_end = $row_path->r_start?JLMS_dateToDisplay($row_path->r_end, false, $JLMS_CONFIG->get('offset')*60*60, '\<\b\\r \/>H:i:s'):'-';
						} elseif (isset($row_path->published) && $row_path->published == 0) {
							$r_img = 'btn_pending_cur';
							$r_start = $row_path->r_start?JLMS_dateToDisplay($row_path->r_start, false, $JLMS_CONFIG->get('offset')*60*60, '\<\b\\r \/>H:i:s'):'-';
						}
					} else {
						if (isset($row_path->published) && $row_path->published == 1) {
							$r_img = 'btn_accept';
							$r_sta = _JLMS_LPATH_STU_LPSTATUS_COMPLETED;
							$r_start = '-';
							$r_end = '-';
						}
						
					} ?>
					<td valign="middle" align="center">
					<a style="border:0px;" href="<?php echo sefRelToAbs("index.php?option=".$option."&task=quizzes&id=".$id."&page=reports&quiz_id=".$row_path->c_id."")?>"><img class='JLMS_png' src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_pending_cur.png" width='16' height='16' alt="<?php echo 'Report';?>" title="<?php echo 'Report';?>" border="0" /></a>
					</td>
					<td valign="middle" align="center" width="16">
						<img class='JLMS_png' src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/<?php echo $r_img;?>.png" width='16' height='16' alt="<?php echo $r_sta;?>" title="<?php echo $r_sta;?>" />
					</td>
					<td valign="middle" align="center" nowrap="nowrap">
						<?php echo $row_path->c_id ? (isset($row_path->s_score)?($row_path->s_score._JLMS_GB_POINTS):'&nbsp;') : '&nbsp;'; ?>
					</td>
					
				</tr>
				<?php
				$k = 3 - $k;
			}
			?>
			</table>
		<?php } elseif ($lists['page'] == 11 && !$lists['filter_stu_val'] && isset($lists['page11_stats']) && !empty($lists['page11_stats'])) { ?>
<script language="javascript" type="text/javascript">
<!--//--><![CDATA[//><!--
var block_stats = 0;
var tID = '';
var url_prefix = '<?php echo $JLMS_CONFIG->get('live_site') . "/index.php?tmpl=component&option=$option&Itemid=$Itemid&id=$id";?>';
function jlms_MakeRequest(url) {
	var http_request = false;
	if (window.XMLHttpRequest) { // Mozilla, Safari,...
		http_request = new XMLHttpRequest();
		if (http_request.overrideMimeType) {
			http_request.overrideMimeType('text/xml');
		}
	} else if (window.ActiveXObject) { // IE
		try { http_request = new ActiveXObject("Msxml2.XMLHTTP");
		} catch (e) {
			try { http_request = new ActiveXObject("Microsoft.XMLHTTP");
			} catch (e) {}
		}
	}
	if (!http_request) {
		return false;
	}
	http_request.onreadystatechange = function() { jlms_AnalizeRequest(http_request); };
	http_request.open('GET', url_prefix + url, true);
	http_request.send(null);
}
function jlms_AnalizeRequest(http_request) {
	if (http_request.readyState == 4) {
		if ((http_request.status == 200)) {
			block_stats = 0;
			if(http_request.responseXML.documentElement == null){
				try {
					http_request.responseXML.loadXML(http_request.responseText)
				} catch (e) {
					/*alert("Can't load");*/
				}
			}
			response  = http_request.responseXML.documentElement;
			var task = response.getElementsByTagName('task')[0].firstChild.data;
			switch (task) {
				case 'lpath_xml':
					var response_data = response.getElementsByTagName('stats_table')[0].firstChild.data;
					if (response_data && response_data != '' && response_data != ' ') {
						var sec_indx = gl_el.parentNode.sectionRowIndex;
						var table = gl_el.parentNode.parentNode;
						var row = table.insertRow(sec_indx + 1);
						var cell1 = document.createElement("td");
						cell1.align = 'left';
						cell1.colSpan = "4";
						cell1.style.padding = '0px';
						cell1.style.margin = '0px';
						cell1.innerHTML = response_data;
						row.appendChild(cell1);
					}
					gl_el.innerHTML = "<img class='JLMS_png' src='<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_accept.png' width='16' height='16' alt=\"done\" title=\"done\" />";
				break;
				case 'scorm_progress_xml':
					var response_data = response.getElementsByTagName('stats_table')[0].firstChild.data;
					var res_user = response.getElementsByTagName('user_id')[0].firstChild.data;
					var res_lpsc = response.getElementsByTagName('quiz_id')[0].firstChild.data;
					if (response_data && response_data != '' && response_data != ' ') {
						gl_el_res = document.getElementById('quizid_'+res_lpsc);
						if (gl_el_res) {
							gl_el_res.innerHTML = response_data;
						}
					}
				break;
			}
		} else {
		}
	}
}
var gl_el = '';
function jlms_RequestQuiz_stats( lp_id, element ) {
	if (block_stats == 0) {
		block_stats = 1;
		gl_el = element.parentNode;
		jlms_MakeRequest('&task=get_quiz_stats&quiz_id='+lp_id);
		gl_el.innerHTML = "<img src='<?php echo $JLMS_CONFIG->get('live_site').'/'.$JLMS_CONFIG->get('ajax_settings_small_indicator'); ?>' width='16' height='16' alt=\"loading\" title=\"loading\" />";
	}
}

JLMS_preloadImages('<?php echo $JLMS_CONFIG->get('live_site').'/'.$JLMS_CONFIG->get('ajax_settings_small_indicator'); ?>', '<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_accept.png', 'components/com_joomla_lms/lms_images/toolbar/btn_cancel.png');
//--><!]]> 
</script>
			<table width="100%" cellpadding="0" cellspacing="0" border="0" class="<?php echo JLMSCSS::_('jlmslist');?>">
				<tr>
					<<?php echo JLMSCSS::tableheadertag();?> width="16" class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center">&nbsp;</<?php echo JLMSCSS::tableheadertag();?>>
					<<?php echo JLMSCSS::tableheadertag();?> width="16" class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center">&nbsp;</<?php echo JLMSCSS::tableheadertag();?>>
					<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" ><?php echo _JLMS_LPATH_TBL_HEAD_NAME;?></<?php echo JLMSCSS::tableheadertag();?>>
					<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>">&nbsp;</<?php echo JLMSCSS::tableheadertag();?>>
				</tr>
			<?php
			$k = 1;
			for ($i=0, $n=count($lists['page11_stats']); $i < $n; $i++) {
				$row_path = $lists['page11_stats'][$i];
				$icon_img = "toolbar/tlb_lpath";
				$icon_alt = "learnpath";
				if ($row_path->c_id) {
					$icon_img = "toolbar/tlb_quiz";
					$icon_alt = "scorm";
				}?>
				<tr class="<?php echo JLMSCSS::_('sectiontableentry'.$k); ?>">
					<td valign="middle" align="center">
						<span style="alignment:center; vertical-align:middle; cursor:pointer" onclick="jlms_RequestQuiz_stats(<?php echo $row_path->c_id;?>, this);">
							<img class='JLMS_png' src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_pending_cur.png" width='16' height='16' alt="<?php echo _JLMS_TRACK_VIEW_DETAILS;?>" title="<?php echo _JLMS_TRACK_VIEW_DETAILS;?>" />
						</span>	
					</td>
					<td valign="middle" align="center">
						<span style="alignment:center; vertical-align:middle">
							<img class='JLMS_png' src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/<?php echo $icon_img;?>.png" width='16' height='16' alt="<?php echo $icon_alt;?>" />
						</span>
					</td>
					<td valign="middle" align="left">
						<?php echo $row_path->c_title;?>
					</td>
					<td valign="middle" align="center">
					<a class="jlms_img_link" style="border:0px;" href="<?php echo sefRelToAbs("index.php?option=".$option."&task=quizzes&id=".$id."&page=reports&quiz_id=".$row_path->c_id."")?>"><img class='JLMS_png' src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_pending_cur.png" width='16' height='16' alt="<?php echo 'Report';?>" title="<?php echo 'Report';?>" border="0" /></a>
					</td>
				</tr>
				<?php
				$k = 3 - $k;
			}
			?>
			</table>
		<?php } ?>
			<input type="hidden" name="option" value="<?php echo $option;?>" />
			<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
			<input type="hidden" name="task" value="tracking" />
			<input type="hidden" name="id" value="<?php echo $id;?>" />
			
			<input type="hidden" name="page" value="<?php echo $lists['page'];?>" />
			<input type="hidden" name="view" value="" />
		</form>
<?php
		JLMS_TMPL::CloseTS();
		JLMS_TMPL::CloseMT();		
	}
	function showTR_clear( $id, $option, &$lists ) {
		global $Itemid, $my, $JLMS_CONFIG;
		?>
<script language="javascript" type="text/javascript">
<!--//--><![CDATA[//><!--
window.addEvent('domready', function() {
			document.adminForm.startday.disabled = true;
			document.adminForm.startmonth.disabled = true;
			document.adminForm.startyear.disabled = true;
				
			document.adminForm.endday.disabled = true;
			document.adminForm.endmonth.disabled = true;
			document.adminForm.endyear.disabled = true;		
}
);
function submitbutton(pressbutton) {
	var form=document.adminForm;
	if (is_se == 1) {
		if (form.start_date.value == ''){jlms_getDate('start');}
		if (form.end_date.value == ''){jlms_getDate('end');}
	}
	if (pressbutton=='track_do_clear') {
		form.task.value = pressbutton;form.submit();
	}
}
var is_se = 0;
function jlms_Change_se(rr) {
	if (rr) {is_se = 0;} else {is_se = 1;}
	var form=document.adminForm;
	form.endday.disabled = rr;
	form.endmonth.disabled = rr;
	form.endyear.disabled = rr;
	form.startday.disabled = rr;
	form.startmonth.disabled = rr;
	form.startyear.disabled = rr;
}
//--><!]]>
</script>
<?php
		JLMS_TMPL::OpenMT();

		$hparams = array();
		$toolbar = array();
		$toolbar[] = array('btn_type' => 'clear', 'btn_js' => "javascript:submitbutton('track_do_clear')" );
		$toolbar[] = array('btn_type' => 'cancel', 'btn_js' => sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid&amp;task=tracking&amp;id=$id") );
		JLMS_TMPL::ShowHeader('tracking', _JLMS_TRACK_CLEAR_TITLE, $hparams, $toolbar);

		JLMS_TMPL::OpenTS();?>

		<form action="<?php echo sefRelToAbs("index.php?option=$option&Itemid=$Itemid");?>" method="post" name="adminForm">
			<table width="100%" cellpadding="0" cellspacing="0" border="0" class="jlms_table_no_borders">
				<tr>
					<td width="15%" valign="top" align="left"><br />
					&nbsp;
					</td>
					<td valign="top"><br />
						<input type="radio" onclick="jlms_Change_se(true)" name="tr_clear_type" value="1" checked="checked" /><?php echo _JLMS_TRACK_CLEAR_ALL;?><br />
						<input type="radio" onclick="jlms_Change_se(false)" name="tr_clear_type" value="2" /><?php echo _JLMS_TRACK_CLEAR_PERIOD;?>
					</td>
				</tr>
				<tr>
					<td valign="middle"><br /><?php echo _JLMS_START_DATE;?></td>
					<td valign="middle" style="vertical-align:middle "><br />
						<table cellpadding="0" cellspacing="0" border="0" class="jlms_date_outer"><tr>						
						<td valign="middle">
							<?php echo JLMS_HTML::_('calendar.calendar','','start','start'); ?>
						</td></tr></table>
					</td>
				</tr>	
				<tr>
					<td><br /><?php echo _JLMS_END_DATE;?></td>
					<td valign="middle" style="vertical-align:middle "><br />
						<table cellpadding="0" cellspacing="0" border="0" class="jlms_date_outer"><tr>						
						<td valign="middle" ><?php echo JLMS_HTML::_('calendar.calendar','','end','end'); ?>
						</td></tr></table>
					</td>
				</tr>	
			</table>
			<input type="hidden" name="option" value="<?php echo $option;?>" />
			<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
			<input type="hidden" name="task" value="track_clear" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="id" value="<?php echo $id;?>" />
		</form>
<?php
		JLMS_TMPL::CloseTS();
		JLMS_TMPL::CloseMT();		
	}
}
?>