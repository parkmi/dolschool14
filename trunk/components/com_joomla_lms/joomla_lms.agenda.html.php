<?php
/**
* joomla_lms.agenda.html.php
* (c) JoomaLMS eLearning Software http://www.joomlalms.com/
**/

// no direct access
defined( '_JLMS_EXEC' ) or die( 'Restricted access' );

class JLMS_agenda_html {
	function show_head_menu ($id, $option =''){
		$JLMS_CONFIG = & JLMSFactory::getConfig();
		$Itemid = $JLMS_CONFIG->get('Itemid');
		$JLMS_ACL = & JLMSFactory::getACL();
		?>
<script language="JavaScript" type="text/javascript">
<!--//--><![CDATA[//><!--
	function jlms_ShowTBToolTip_agenda(txt_tooltip_agenda) {
		var agenda_elem = getObj('JLMS_toolbar_tooltip_agenda');
		agenda_elem.innerHTML = txt_tooltip_agenda;
	}
//--><!]]>
</script>
<?php
		JLMS_TMPL::OpenMT();

		$hparams = array();
		JLMS_TMPL::ShowHeader('agenda', _JLMS_AGENDA_HEADTITLE, $hparams);
		JLMS_TMPL::OpenTS('', ' align="center"');
?>
		<table align="center" class="jlms_table_no_borders">
			<tr><td align="center" style="text-align:center "><span id="JLMS_toolbar_tooltip_agenda">&nbsp;</span></td></tr>
			<tr>
				<td align="center" nowrap="nowrap" style="white-space:nowrap;">
				<?php if($id && $JLMS_ACL->CheckPermissions('announce', 'manage')){
				if (isset($_REQUEST['cal_date'])){
					$id .= "&amp;date=".$_REQUEST['cal_date']; 
				}?>
				<a class="jlms_img_link" onmouseover='javascript:jlms_ShowTBToolTip_agenda("<?php echo _JLMS_AGENDATB_ADDEVENT;?>");jlms_WStatus("<?php echo _JLMS_AGENDATB_ADDEVENT;?>");return true;' onmouseout='javascript:jlms_ShowTBToolTip_agenda("&nbsp;");jlms_WStatus("");return true;' href="<?php echo JRoute::_("index.php?option=com_joomla_lms&amp;Itemid=".$Itemid."&amp;task=agenda&amp;mode=add_event&amp;id=".$id);?>"><img src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/agenda/ag_edit_add.png" class="JLMS_png" id="add" alt="<?php echo _JLMS_AGENDATB_ADDEVENT;?>" title="<?php echo _JLMS_AGENDATB_ADDEVENT;?>"  width="22" height="22"  border="0"/></a>&nbsp;&nbsp;
				<?php }?>
				<a class="jlms_img_link" onmouseover='javascript:jlms_ShowTBToolTip_agenda("<?php echo _JLMS_AGENDATB_LIST;?>");jlms_WStatus("<?php echo _JLMS_AGENDATB_LIST;?>");return true;' onmouseout='javascript:jlms_ShowTBToolTip_agenda("&nbsp;");jlms_WStatus("");return true;' href="<?php echo sefRelToAbs("index.php?option=com_joomla_lms&amp;Itemid=".$Itemid."&amp;task=agenda&amp;id=".$id);?>"><img src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/agenda/ag_list.png" class="JLMS_png" id="home" alt="<?php echo _JLMS_AGENDATB_LIST;?>" title="<?php echo _JLMS_AGENDATB_LIST;?>" width="22" height="22"  border="0"/></a>&nbsp;&nbsp;
				<a class="jlms_img_link" onmouseover='javascript:jlms_ShowTBToolTip_agenda("<?php echo _JLMS_AGENDATB_MONTHLY_VIEW;?>");jlms_WStatus("<?php echo _JLMS_AGENDATB_MONTHLY_VIEW;?>");return true;' onmouseout='javascript:jlms_ShowTBToolTip_agenda("&nbsp;");jlms_WStatus("");return true;' href="<?php echo sefRelToAbs("index.php?option=com_joomla_lms&amp;Itemid=".$Itemid."&amp;task=agenda&amp;mode=view_month&amp;id=$id");?>"><img src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/agenda/ag_month.png" class="JLMS_png" id="month" alt="<?php echo _JLMS_AGENDATB_MONTHLY_VIEW;?>" title="<?php echo _JLMS_AGENDATB_MONTHLY_VIEW;?>" width="22" height="22" border="0"/></a>&nbsp;&nbsp;
				<a class="jlms_img_link" onmouseover='javascript:jlms_ShowTBToolTip_agenda("<?php echo _JLMS_AGENDATB_WEEKLY_VIEW;?>");jlms_WStatus("<?php echo _JLMS_AGENDATB_WEEKLY_VIEW;?>");return true;' onmouseout='javascript:jlms_ShowTBToolTip_agenda("&nbsp;");jlms_WStatus("");return true;' href="<?php echo sefRelToAbs("index.php?option=com_joomla_lms&amp;Itemid=".$Itemid."&amp;task=agenda&amp;mode=view_week&amp;id=$id");?>"><img src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/agenda/ag_7days.png" class="JLMS_png" id="week" alt="<?php echo _JLMS_AGENDATB_WEEKLY_VIEW;?>" title="<?php echo _JLMS_AGENDATB_WEEKLY_VIEW;?>" width="22" height="22"  border="0"/></a>&nbsp;&nbsp;
				<a class="jlms_img_link" onmouseover='javascript:jlms_ShowTBToolTip_agenda("<?php echo _JLMS_AGENDATB_DAILY_VIEW;?>");jlms_WStatus("<?php echo _JLMS_AGENDATB_DAILY_VIEW;?>");return true;' onmouseout='javascript:jlms_ShowTBToolTip_agenda("&nbsp;");jlms_WStatus("");return true;' href="<?php echo sefRelToAbs("index.php?option=com_joomla_lms&amp;Itemid=".$Itemid."&amp;task=agenda&amp;mode=view_day&amp;id=$id");?>"><img src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/agenda/ag_1day.png"  class="JLMS_png" id="day" alt="<?php echo _JLMS_AGENDATB_DAILY_VIEW;?>" title="<?php echo _JLMS_AGENDATB_DAILY_VIEW;?>"  width="22" height="22" border="0"/></a>
				</td>
			</tr>
		</table>
<?php
		JLMS_TMPL::CloseTS();
		JLMS_TMPL::CloseMT();
	}
	
	function show_agenda_items(  $id, $option, $rows, $date, $lists, $agenda ){
		$JLMS_CONFIG = & JLMSFactory::getConfig();
		$Itemid = $JLMS_CONFIG->get('Itemid');
		$JLMS_ACL = & JLMSFactory::getACL();

		//JLMS_loadCalendar();
		JLMS_dateLoadJSFrameWork(true);
		$k = $exp = 0;
		$m = count($agenda);
		/*<script type="text/javascript" src="<?php echo $JLMS_CONFIG->get('live_site');?>/includes/js/joomla.javascript.js "></script>*/
		?>		
		<script language="javascript" type="text/javascript">
		<!--
		function quick_date(){
			document.forms.jlms_agenda_form_day.submit();
		}
		var test = 'month_num<?php echo $exp;?>';
		
		function JLMS_expandMonth( monthId ) {
			if (test == monthId){
				getObj(monthId).style.display = 'none';
				test = '';
			}
			else{
				JLMS_collapseAllMonth();
				getObj(monthId).style.display = '';
				test = monthId;
			}
		}		
		function JLMS_collapseAllMonth() {
			for (i=0; i<=<?php echo $m?>; i++){
				if (getObj('month_num'+i)){
					getObj('month_num'+i).style.display = 'none';
				}
			}
		}
		//-->
		</script>
		<form name="jlms_filter" action="<?php echo sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid&amp;task=agenda&amp;id=$id");?>" method="post" >
		<table class="jlms_table_no_borders" width='100%' cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td align="right" style="text-align:right; height:30px; " valign="top">
				<?php echo _JLMS_ORDERING ."&nbsp;". $lists['order'];?>
				&nbsp;&nbsp;
				<?php echo _JLMS_FILTER ."&nbsp;". $lists['filter'];?>
				<input type="submit" value="<?php echo _JLMS_AGENDA_GO;?>" class="button" name="<?php echo _JLMS_AGENDA_GO;?>" />
				</td>
			</tr>
		</table>
		</form>
		<?php if (!empty($agenda)){
		echo "<table cellpadding='0' cellspacing='0' width='100%' style='width:100%'>";
		}
		else{
			echo '<div class="joomlalms_user_message">'._JLMS_AGENDA_NO_ITEMS.'</div>';
		}
		$a_id_date = '';
		$selected_id = 0;
		if (isset($_REQUEST['agenda_id'])) {
			 $selected_id = $_REQUEST['agenda_id'];
		}
		$i = 0;
		$k = 0;
		$ste = 2;
		//kolichestvo mesiacev
		$j = 1;
		$new_exp = 0;
		for($i=0; $i<count($agenda);$i++){
			$b_bot = '';
			$border = '';
			$none = "style='display:none'";
			if (($agenda[$i]->a_y.'-'.$agenda[$i]->a_m == date('Y-m')) || (count($agenda) == 1) ){
				$none = '';
				$new_exp = $i;
			}
			if ( isset($selected_id) && ($agenda[$i]->a_y.'-'.$agenda[$i]->a_m == mosGetParam($_REQUEST,'cal_date','' )) ){
				$none   = '';
				$new_exp = $i;
			}
			echo "<tr onclick='JLMS_expandMonth(\"month_num".$i."\")' style='cursor:pointer'><td class='month_header' ".$b_bot." title='"._JLMS_AGENDA_CLICK_HERE."'>".month_lang(strftime('%m %Y',strtotime($agenda[$i]->a_y.'-'.$agenda[$i]->a_m.'-1')),0,2)."</td></tr>";
			echo "<tr $none id='month_num".$i."'><td valign='top' style='padding:3px;' >";
			if (count($agenda[$i]->items)) {
				echo "<table width='100%' cellpadding='0' cellspacing='0' border='0' class='jlms_table_no_borders'>";
			}
			for($j=0; $j<count($agenda[$i]->items); $j++ ){
				$border = '';
				if ($selected_id == $agenda[$i]->items[$j]->agenda_id && ($agenda[$i]->a_y.'-'.$agenda[$i]->a_m == mosGetParam($_REQUEST,'cal_date','' ))){
					$border = 'background:#F2E3D4;';
				}
				echo "<tr><td valign='top' style='$border;' ><a name='anc".$agenda[$i]->items[$j]->agenda_id.'-'.$agenda[$i]->a_y.'-'.$agenda[$i]->a_m."'></a>";
				//print Image v zavisimosti ot tipa (proshedshee sobitie)
				if ( $agenda[$i]->items[$j]->start_date > date('Y-m-d') ){
					echo "<img src=\"".$JLMS_CONFIG->get('live_site')."/components/com_joomla_lms/lms_images/agenda/ag_upcoming.png\" alt='"._JLMS_AGENDA_UPCOMING."' title='"._JLMS_AGENDA_UPCOMING."' align='left' border='0' width='16' height='16' class='JLMS_png' />&nbsp;";
				}
				elseif (($agenda[$i]->items[$j]->end_date >= date('Y-m-d')) && ($agenda[$i]->items[$j]->start_date <= date('Y-m-d')) ){
					echo "<img src=\"".$JLMS_CONFIG->get('live_site')."/components/com_joomla_lms/lms_images/agenda/ag_today.png\" alt='"._JLMS_AGENDA_TODAY."' title='"._JLMS_AGENDA_TODAY."' align='left' border='0' width='16' height='16' class='JLMS_png' />&nbsp;";
				}
				else{
					echo "<img src=\"".$JLMS_CONFIG->get('live_site')."/components/com_joomla_lms/lms_images/agenda/ag_last.png\" alt='"._JLMS_AGENDATB_THELAST."' title='"._JLMS_AGENDATB_THELAST."' align='left' border='0' width='16' height='16' class='JLMS_png' />&nbsp;";
				}
				//print title + description
				echo "<b>".$agenda[$i]->items[$j]->title."</b></td></tr>";
				echo "<tr><td class='createdate' style='$border;'><font color='#585A5C'>"._JLMS_START_DATE."</font>&nbsp;".day_month_lang(date('w m d, Y' , strtotime( $agenda[$i]->items[$j]->start_date )),0,1,2,2)." &nbsp;&nbsp;&nbsp;<font color='#585A5C'> "._JLMS_END_DATE."</font> &nbsp;".day_month_lang(date('w m d, Y' , strtotime($agenda[$i]->items[$j]->end_date )),0,1,2,2)."";
				echo "</td></tr>";
				if ($JLMS_ACL->CheckPermissions('announce', 'manage') && isset($agenda[$i]->items[$j]->is_time_related) && $agenda[$i]->items[$j]->is_time_related) {
					$released_info_txt = _JLMS_WILL_BE_RELEASED_IN;
					$showperiod = $agenda[$i]->items[$j]->show_period;
					$ost1 = $showperiod%(24*60);		
					$sp_days = ($showperiod - $ost1)/(24*60);
					$ost2 = $showperiod%60;						
					$sp_hours = ($ost1 - $ost2)/60;
					$sp_mins = $ost2;
					$released_info_time = false;
					if ($sp_days) {
						$released_info_txt .= ' '.$sp_days.' '._JLMS_RELEASED_IN_DAYS;
						$released_info_time = true;
					}
					if ($sp_hours) {
						$released_info_txt .= ' '.$sp_hours.' '._JLMS_RELEASED_IN_HOURS;
						$released_info_time = true;
					}
					if ($sp_mins) {
						$released_info_txt .= ' '.$sp_mins.' '._JLMS_RELEASED_IN_MINUTES;
						$released_info_time = true;
					}
					if ($released_info_time) {
						$released_info_txt .= ' '._JLMS_RELEASED_AFTER_ENROLLMENT;
					}
					echo "<tr><td class='small' style='$border;'>".$released_info_txt."</td></tr>";
				}
				echo "<tr><td width='100%' align='left' style='$border;'>";
				echo $agenda[$i]->items[$j]->content;
				echo "</td></tr>";
				//redaktirovanie zapisei dlia teachera kotorii ego sozdal ili super admina
				if ( $JLMS_ACL->CheckPermissions('announce', 'manage') ){
					echo "<tr>
							<td align='right' style='$border text-align:right' >
								<a href='".sefRelToabs("index.php?option=$option&amp;Itemid=$Itemid&amp;task=agenda&amp;mode=edit&amp;id=$id&amp;agenda_id=".$agenda[$i]->items[$j]->agenda_id)."' ><img src=\"".$JLMS_CONFIG->get('live_site')."/components/com_joomla_lms/lms_images/toolbar/btn_edit.png\" alt='"._JLMS_EDIT."' title='"._JLMS_EDIT."' align='top' border='0' width='16' height='16' class='JLMS_png' /></a>
								&nbsp;&nbsp;
								<a href='".sefRelToabs("index.php?option=$option&amp;Itemid=$Itemid&amp;task=agenda&amp;mode=delete&amp;id=$id&amp;agenda_id=".$agenda[$i]->items[$j]->agenda_id)."' ><img src=\"".$JLMS_CONFIG->get('live_site')."/components/com_joomla_lms/lms_images/toolbar/btn_delete.png\" alt='"._JLMS_DELETE."' title='"._JLMS_DELETE."' align='top' border='0' width='16' height='16' class='JLMS_png' /></a>&nbsp;
							</td>
						</tr>";
				}
			}
			if (count($agenda[$i]->items)) {
				echo "</table>";
			}
			echo "</td></tr>";
			
		}
		if (!empty($agenda)){
			echo "</table>";
		}
		//zakrivaem <table> po okonchaniu mesiaca
		//echo "</td></tr>";
		?>
		<?php if (isset($new_exp) && $new_exp) { ?>
			<script language="javascript" type="text/javascript">
			<!--
			var test = 'month_num<?php echo $new_exp;?>';
			//-->
			</script>
		<?php } ?>
			<!--tr>
				<td-->
				<div class="joomlalms_info_legend">
				* - <?php echo _JLMS_AGENDA_CLICK_MONTH_TITLE;?><br /><br />
				<b><?php echo _JLMS_AGENDA_COMMENT;?></b><br /><br />
				<img src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/agenda/ag_last.png" align="top" alt='<?php echo _JLMS_AGENDATB_THELAST;?>' title='<?php echo _JLMS_AGENDATB_THELAST;?>' border='0' width='16' height='16' class='JLMS_png' /> 
				- <?php echo _JLMS_AGENDA_DESC_LAST;?><br />
				<img src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/agenda/ag_today.png" align="top" alt='<?php echo _JLMS_AGENDA_TODAY;?>' title='<?php echo _JLMS_AGENDA_TODAY;?>' border='0' width='16' height='16' class='JLMS_png' /> 
				- <?php echo _JLMS_AGENDA_DESC_TOD;?><br />
				<img src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/agenda/ag_upcoming.png" align="top" alt='<?php echo _JLMS_AGENDA_UPCOMING;?>' title='<?php echo _JLMS_AGENDA_UPCOMING;?>' border='0' width='16' height='16' class='JLMS_png' /> 
				- <?php echo _JLMS_AGENDA_DESC_UPC;?>
				</div>
				<!--/td>
			</tr>
		</table-->		
	<?php
	}
	
	function show_calendar_month($id, $option, $rows, $date) {
		$JLMS_CONFIG = & JLMSFactory::getConfig();
		$Itemid = $JLMS_CONFIG->get('Itemid');

		$month = date('m', $date);
		$day   = date('j', $date);
		$year  = date('Y', $date);
		$choose_date = date('Y-m-d', $date);
		$choose_date_display = JLMS_dateToDisplay($choose_date);
		$date_format = $JLMS_CONFIG->get('date_format');
		$date_format_fdow = $JLMS_CONFIG->get('date_format_fdow');	
		/*<script type="text/javascript" src="<?php echo $JLMS_CONFIG->get('live_site');?>/includes/js/joomla.javascript.js "></script>*/		
		?>
		<script type="text/javascript" language="javascript">
		
		function td_hover(elem){
			getObj(elem).style['background'] = '#EEEEEE';
			getObj(elem).style.color = '#000000';
		}
		//function out to other days
		function td_out(elem){
			getObj(elem).style['background'] = '';
			getObj(elem).style.color = '';
		}
		//function out to weekends days
		function td_out2(elem){
			getObj(elem).style['background'] = '#E1CDCD';
			getObj(elem).style.color = '';
		}
		//function out to current day
		function td_out3(elem){
			getObj(elem).style['background'] = '#FBA179';
			getObj(elem).style.color = '';
		}
		
		</script>		
		<script language="javascript" type="text/javascript">
		<!--//
		function quick_date(){
			document.forms.jlms_agenda_form_day.submit();
		}	//-->
		</script>
		<form action="<?php echo sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid");?>" method="post" name="jlms_agenda_form_day">
		<table cellpadding="0" cellspacing="0" border="0" style="height:16px;" class="jlms_table_no_borders">
			<tr>
				<td valign="middle" align="center" width="18">
				<?php echo JLMS_HTML::_('calendar.calendar',$choose_date_display, 'cal', 'jlms_choose', null, null, 'statictext'); ?>			
				</td><td valign="middle" align="center" width="18">
				<span onclick="quick_date()" style="cursor:pointer" title="<?php echo _JLMS_AGENDA_GO_DATE;?>">
				<img src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/agenda/loopnone.png" alt="<?php echo _JLMS_AGENDA_GO_DATE;?>" title="<?php echo _JLMS_AGENDA_GO_DATE;?>" id="go_date" border="0" width="16" height="16" class="JLMS_png" />
				</span>
				</td>
			</tr>
		</table>	
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
		<input type="hidden" name="task" value="agenda" />
		<input type="hidden" name="mode" value="view_month" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="id" value="<?php echo $id;?>" />
		</form>
		<?php 
		$wday = JDDayOfWeek(GregorianToJD($month, 1, $year), 0);
		if ($date_format_fdow == 1){
			if ($wday == 0) {$wday = 7;}
			$n = - ($wday - 2);
			$num_sat = 5;
			$num_sun = 6;
		}elseif($date_format_fdow == 2){
			$n = - ($wday - 1);
			$num_sat = 0;
			$num_sun = 6;
		}
		$cal = array();
		for ($y=0; $y<6; $y++) {
			$row = array();
			$notEmpty = false;
			for ($x=0; $x<7; $x++, $n++) {
				if (checkdate($month, $n, $year)) {
					$row[] = $n;
					$notEmpty = true;
				} else {
					$row[] = "";
				}
			}
			if (!$notEmpty) break;
			$cal[] = $row;
		}
		?>
		<!-- Shablon calendaria. -->
		<table cellpadding="0" cellspacing="0" width="100%">
			<tr style="background: url('<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/agenda/bg.jpg') #cccccc repeat-x; font-weight:bold; color:white;height:20px " valign="middle">
				<td valign="middle" align="center">
				<a class="jlms_img_link" href="<?php echo sefRelToAbs("index.php?option=$option&amp;Itemid=".$Itemid."&amp;task=agenda&amp;mode=view_month&amp;id=".$id."&amp;date=".JLMS_dateToDisplay(strtotime("-1 month",$date),true)); ?> " >
				<img src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/agenda/ag_previous.png" class="JLMS_png" alt="<?php echo _JLMS_AGENDA_PREVIOUSMONTH;?>" title="<?php echo _JLMS_AGENDA_PREVIOUSMONTH;?>" width="16" height="16"  border="0"/>
				</a>
				</td>
				<td align="center" style="text-align:center" valign="middle" colspan="5">
				<?php echo month_lang(strftime('%m %Y',$date),0,2);?>
				</td>
				<td valign="middle" align="center">
				<a class="jlms_img_link" href="<?php echo sefRelToAbs("index.php?option=$option&amp;Itemid=".$Itemid."&amp;task=agenda&amp;mode=view_month&amp;id=".$id."&amp;date=".JLMS_dateToDisplay(strtotime("+1 month",$date),true)); ?> ">
				<img src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/agenda/ag_next.png" class="JLMS_png" alt="<?php echo _JLMS_AGENDA_NEXTMONTH;?>" title="<?php echo _JLMS_AGENDA_NEXTMONTH;?>" width="16" height="16"  border="0"/>
				</a></td>
			</tr>
			<?php if ($date_format_fdow == 1){ ?>
			<tr style="text-align:center ">
				<td class="week_names" id="mon"><?php echo day_month_lang(date('w',strtotime('Monday')),0,1,2,2);//_JLMS_AGENDA_MONDAY;?></td>
				<td class="week_names" id="tue"><?php echo day_month_lang(date('w',strtotime('Tuesday')),0,1,2,2);//_JLMS_AGENDA_TUESDAY;?></td>
				<td class="week_names" id="wed"><?php echo day_month_lang(date('w',strtotime('Wednesday')),0,1,2,2);//_JLMS_AGENDA_WEDNESDAY;?></td>
				<td class="week_names" id="thu"><?php echo day_month_lang(date('w',strtotime('Thursday')),0,1,2,2);//_JLMS_AGENDA_THURSDAY;?></td>
				<td class="week_names" id="fri"><?php echo day_month_lang(date('w',strtotime('Friday')),0,1,2,2);//_JLMS_AGENDA_FRIDAY;?></td>
				<td class="week_names" id="sat"><?php echo day_month_lang(date('w',strtotime('Saturday')),0,1,2,2);//_JLMS_AGENDA_SATURDAY;?></td>
				<td class="week_names" style="border-right:1px solid #B3B3B3; " id="sun"><?php echo day_month_lang(date('w',strtotime('Sunday')),0,1,2,2);//echo _JLMS_AGENDA_SUNDAY;?></td>
			</tr>
			<?php }else{?>
			<tr style="text-align:center ">
				<td class="week_names" id="sun"><?php echo day_month_lang(date('w',strtotime('Sunday')),0,1,2,2);//echo _JLMS_AGENDA_SUNDAY;?></td>
				<td class="week_names" id="mon"><?php echo day_month_lang(date('w',strtotime('Monday')),0,1,2,2);//_JLMS_AGENDA_MONDAY;?></td>
				<td class="week_names" id="tue"><?php echo day_month_lang(date('w',strtotime('Tuesday')),0,1,2,2);//_JLMS_AGENDA_TUESDAY;?></td>
				<td class="week_names" id="wed"><?php echo day_month_lang(date('w',strtotime('Wednesday')),0,1,2,2);//_JLMS_AGENDA_WEDNESDAY;?></td>
				<td class="week_names" id="thu"><?php echo day_month_lang(date('w',strtotime('Thursday')),0,1,2,2);//_JLMS_AGENDA_THURSDAY;?></td>
				<td class="week_names" id="fri"><?php echo day_month_lang(date('w',strtotime('Friday')),0,1,2,2);//_JLMS_AGENDA_FRIDAY;?></td>
				<td class="week_names" style="border-right:1px solid #B3B3B3; " id="sat"><?php echo day_month_lang(date('w',strtotime('Saturday')),0,1,2,2);//_JLMS_AGENDA_SATURDAY;?></td>
				
			</tr>
			<?php }?>
	  		<!-- cycle of rows -->
			
		  <?php 
		  
		  $j = 0;$k = 0;
		  
		 
		  while (isset($rows[$j]) &&  ($j < count($rows)) && (strtotime($rows[$j]->start_date) < strtotime(date($year."-".$month."-1")) )){
				$j++;
		  }
		  foreach ($cal as $row) {
			echo "<tr>";
				foreach ($row as $i=>$v) {
				if ($i == $num_sat || $i == $num_sun){
					$color = '';
					if ($i == 6){
						$color = "border-right:1px solid #B3B3B3;  ";
					}
					$color .= "background:#E1CDCD;border-left:1px solid #B3B3B3; border-bottom:1px solid #B3B3B3; ";
					$w = '2';
				}
				else{
					$color = 'border-bottom:1px solid #B3B3B3; border-left:1px solid #B3B3B3';$w='';
				}
				$today = '';
				$d = str_pad($v, 2, "0", STR_PAD_LEFT);
				if (date("Y-m-".$d,$date) == date('Y-m-d')){
					$color = "background:#FBA179;border-left:1px solid #B3B3B3; border-bottom:1px solid #B3B3B3;";
					$w = '3';
					//$today = " - "._JLMS_AGENDA_TODAY;
				}
				/*if (strlen($v == 1)){
				 	$d = "0".$v;
				} 
				else{
					$d= $v;
				}*/
				
				echo "<td style='".$color.";height:40px;font-size:12px; font-weight:bold' id='bbb".$k."' onmouseover=\"td_hover('bbb".$k."')\" onmouseout=\"td_out".$w."('bbb".$k."')\"> ";
				echo "<a  href='".sefRelToAbs("index.php?option=$option&amp;task=agenda&amp;mode=view_day&amp;id=".$id."&amp;Itemid=".$Itemid."&amp;date=".date("Y-m-".$v,$date))."' title='"._JLMS_AGENDATB_DAILY_VIEW."' >".$v.$today."</a><br />";
				
				$j = 0;
				if ($v){
					while (isset($rows[$j]) &&  ($j < count($rows))  ){
					
					if (strtotime(date('Y-m-'.$d , $date)) >= strtotime($rows[$j]->start_date) && strtotime(date('Y-m-'.$d , $date))<= strtotime($rows[$j]->end_date) ){
						//echo date('Y-m-'.$d , $date) .'-----'. $rows[$j]->start_date.'@@@'.$rows[$j]->end_date;
						$title = substr($rows[$j]->title,0,12);
						$title .= strlen($rows[$j]->title)>12 ? "..." : '';
						$descr = strip_tags($rows[$j]->content);
						$overlib_descr = substr($descr,0,120) . ((strlen($descr)>120)?"...":'');
						$overlib_title = JLMS_txt2overlib($overlib_descr);
						$overlib_title = JLMS_txt2overlib($rows[$j]->title);

						$link = sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid&task=agenda&id=$id&agenda_id=".$rows[$j]->agenda_id."&amp;date=".date("Y-m",$date)."#anc".$rows[$j]->agenda_id.'-'.date("Y-m",$date));
						echo JLMS_toolTip($overlib_title, $overlib_descr, $title, $link, '1', '30', true, 'jlms_ttip');
						echo '<br />';
						}
						$j++;
					}
				}
				echo "</td>";  
				
				$k++;
				
				} ?>
			</tr>
		<?php 
		}
		?>
		</table>
	<?php
	}

	function show_calendar_week( $id, $option, $rows, $date ) {
		$JLMS_CONFIG = & JLMSFactory::getConfig();
		$Itemid = $JLMS_CONFIG->get('Itemid');	

		$month = date('m', $date);
		$day   = date('j', $date);
		$year  = date('Y', $date);
		$choose_date = JLMS_dateToDisplay(date('Y-m-d', $date));

		$date_format_fdow = $JLMS_CONFIG->get('date_format_fdow');
		/*<script type="text/javascript" src="<?php echo $JLMS_CONFIG->get('live_site');?>/includes/js/joomla.javascript.js "></script>*/
		?>
		<script type="text/javascript" language="javascript">
		<!--
		function td_hover(elem){
			getObj(elem).style['background'] = '#EEEEEE';
			getObj(elem).style.color = '#000000';
			//getObj(elem).style.cursor = 'pointer';
		}
		//function out to other days
		function td_out(elem){
			getObj(elem).style['background'] = '';
			getObj(elem).style.color = '';
		}
		//function out to weekneds days
		function td_out2(elem){
			getObj(elem).style['background'] = '#E1CDCD';
			getObj(elem).style.color = '';
		}
		//function out to current day
		function td_out3(elem){
			getObj(elem).style['background'] = '#FBA179';
			getObj(elem).style.color = '';
		}
		-->
		</script>	
		<script language="javascript" type="text/javascript">
		<!--//
		function quick_date(){
			document.forms.jlms_agenda_form_day.submit();
		}	//-->
		</script>
		<form action="<?php echo sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid");?>" method="post" name="jlms_agenda_form_day">
		<table cellpadding="0" cellspacing="0" border="0" style="height:20px" class="jlms_table_no_borders">
			<tr>
				<td valign="middle" align="center" width="18">
				<?php echo JLMS_HTML::_('calendar.calendar',$choose_date, 'cal', 'jlms_choose', null, null, 'statictext'); ?>				
				</td><td valign="middle" align="center" width="18">
				<span onclick="quick_date()" style=" cursor:pointer" title="<?php echo _JLMS_AGENDA_GO_DATE;?>">
				<img src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/agenda/loopnone.png" alt="<?php echo _JLMS_AGENDA_GO_DATE;?>" title="<?php echo _JLMS_AGENDA_GO_DATE;?>" id="go_date" border="0" width="16" height="16" class="JLMS_png" />
				</span>
				</td>
			</tr>
		</table>	

		<input type="hidden" name="option" value="<?php echo $option?>" />
		<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
		<input type="hidden" name="task" value="agenda" />
		<input type="hidden" name="mode" value="view_week" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="id" value="<?php echo $id;?>" />
		</form>
		<?php 
		$wday = JDDayOfWeek(GregorianToJD($month, $day, $year), 0);
		if ($date_format_fdow == 1){
			if ($wday == 0){$wday = 7;}
			$mon_day_num = strtotime("-".($wday-1)." day", $date);
			$sun_day_num = strtotime("+".(7-$wday)." day", $date);
			$num_sat = 5;
			$num_sun = 6;
		}else{
			$mon_day_num = strtotime("-".($wday)." day", $date);
			$sun_day_num = strtotime("+".(6-$wday)." day", $date);
			$num_sat = 0;
			$num_sun = 6;
		}
		?>
		<!-- Shablon calendaria. -->
		<table cellpadding="0" cellspacing="0" width="100%">
			<tr style="background: url('<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/agenda/bg.jpg') #cccccc repeat-x; font-weight:bold; color:white;height:20px " valign="middle">
				<td valign="middle" align="center">
				<a class="jlms_img_link" href="<?php echo sefRelToAbs("index.php?option=$option&amp;Itemid=".$Itemid."&amp;task=agenda&amp;mode=view_week&amp;id=".$id."&amp;date=".JLMS_dateToDisplay(strtotime("-1 week",$date),true)); ?>">
				<img src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/agenda/ag_previous.png" class="JLMS_png" alt="<?php echo _JLMS_AGENDA_PREVIOUSWEEK;?>" title="<?php echo _JLMS_AGENDA_PREVIOUSWEEK;?>" width="16" height="16"  border="0"/>
				</a>
				</td>
				<td align="center" style="text-align:center" valign="middle" colspan="5">
				(<?php echo day_month_lang(date('w d m Y', $mon_day_num), 0,1,5,2)."&nbsp;&nbsp;-&nbsp;&nbsp;".day_month_lang(date('w d m Y', $sun_day_num), 0,1,5,2);?>)
				</td>
				<td valign="middle" align="center">
				<a class="jlms_img_link" href="<?php echo sefRelToAbs("index.php?option=$option&amp;Itemid=".$Itemid."&amp;task=agenda&amp;mode=view_week&amp;id=".$id."&amp;date=".JLMS_dateToDisplay(strtotime("+1 week",$date),true)); ?>">
				<img src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/agenda/ag_next.png" class="JLMS_png" alt="<?php echo _JLMS_AGENDA_NEXTWEEK;?>" title="<?php echo _JLMS_AGENDA_NEXTWEEK;?>" width="16" height="16"  border="0"/>
				</a></td>
			</tr>	
		<!-- cycle of rows -->
		<?php
		echo "<tr>";
		$i = 0;$br_right = '';
		for ($i; $i < 7;$i++){
			if ($i == 6) $br_right = ';border-right:1px solid #B3B3B3';
			echo "<td style='width:14%;text-align:center; height:15px;background:#cccccc;border-bottom:1px solid #B3B3B3; border-left:1px solid #B3B3B3 $br_right' >".month_lang(strftime('%m %Y',strtotime("+$i day",$mon_day_num)),0,2)."<br />".day_month_lang(date("w",strtotime("+$i day",$mon_day_num)),0,1,0,0)."</td>";
		}
		echo "</tr>";
		$i = 0; 
		echo "<tr>";
		for ($i; $i < 7;$i++){
			
			if ($i == $num_sat || $i == $num_sun){
				$color = '';
					if ($i == 6){
						$color = "border-right:1px solid #B3B3B3;  ";
					}
					$color .= "background:#E1CDCD;border-left:1px solid #B3B3B3; border-bottom:1px solid #B3B3B3; ";
				$w = '2';
			}
			else{
				$color = 'border-bottom:1px solid #B3B3B3; border-left:1px solid #B3B3B3;';$w='';
			}
			if (date("".$year."-".$month."-".$i."",$date) == date('Y-m-d')){
				$color = "background:#FBA179;border:1px solid #B3B3B3;";
				$w = '3';
			}
			$today = '';
			
			if (date('Y-m-d',strtotime("+$i day",$mon_day_num)) == date('Y-m-d') ){
				$color = "background:#FBA179;border-left:1px solid #B3B3B3;border-bottom:1px solid #B3B3B3;";
				$w = '3';
				//$today = _JLMS_AGENDA_TODAY;
			}
			
			//if ($i == 0){ echo "<tr>";}
			echo "<td style='".$color."width:14%; height:40px;' valign='top' id='day".$i."' onmouseover=\"td_hover('day".$i."')\" onmouseout=\"td_out".$w."('day".$i."')\">".$today."<br />";
			$k = 0;
			while ( isset($rows[$k]) &&  ($k < count($rows) ) ){
				if ((strtotime("+$i day",$mon_day_num) >= strtotime($rows[$k]->start_date)) && (strtotime("+$i day",$mon_day_num) <= strtotime($rows[$k]->end_date)) ){
					$title = substr($rows[$k]->title,0,12);
					$title .= strlen($rows[$k]->title)>12 ? "..." : '';
					$descr = strip_tags($rows[$k]->content);
					$overlib_descr = substr($descr,0,120) . ((strlen($descr)>120)?"...":'');
					$overlib_descr = JLMS_txt2overlib($overlib_descr);
					$overlib_title = JLMS_txt2overlib($rows[$k]->title);
					
					$link = sefRelToAbs("index.php?option=$option&amp;&amp;Itemid=$Itemid&amp;task=agenda&amp;id=$id&amp;agenda_id=".$rows[$k]->agenda_id."&amp;date=".date("Y-m",$date)."#anc".$rows[$k]->agenda_id.'-'.date("Y-m",$date)."");
					echo JLMS_toolTip($overlib_title, $overlib_descr, $title, $link);
					echo '<br />';
				}
				$k++;
			}
			
			echo "</td>";
		}
		echo "</tr>";
		?>
		</table>
	<?php
	}
	function show_calendar_day($id, $option, &$rows, $date ) {
		$JLMS_CONFIG = & JLMSFactory::getConfig();
		$Itemid = $JLMS_CONFIG->get('Itemid');
				
		$choose_date = JLMS_dateToDisplay(date('Y-m-d', $date));
		/*<script type="text/javascript" src="<?php echo $JLMS_CONFIG->get('live_site');?>/includes/js/joomla.javascript.js "></script>*/
		?>	
		<script language="javascript" type="text/javascript">
		<!--
		function quick_date(){
			document.forms.jlms_agenda_form_day.submit();
		}
		//-->
		</script>
		<form action="<?php echo sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid");?>" method="post" name="jlms_agenda_form_day">
		<table cellpadding="0" cellspacing="0" border="0" style="height:16px;" class="jlms_table_no_borders">
			<tr>
				<td valign="middle" align="center" width="18">
				<?php echo JLMS_HTML::_('calendar.calendar',$choose_date, 'cal', 'jlms_choose', null, null, 'statictext'); ?>
				</td><td valign="middle" align="center" width="18">
				<span onclick="quick_date()" style=" cursor:pointer" title="<?php echo _JLMS_AGENDA_GO_DATE;?>">
				<img src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/agenda/loopnone.png" alt="<?php echo _JLMS_AGENDA_GO_DATE;?>" title="<?php echo _JLMS_AGENDA_GO_DATE;?>" id="go_date" border="0" width="16" height="16" class="JLMS_png" />
				</span>
				</td>
			</tr>
		</table>
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
		<input type="hidden" name="task" value="agenda" />
		<input type="hidden" name="mode" value="view_day" />
		<input type="hidden" name="id" value="<?php echo $id;?>" />
		</form>
		<table cellpadding="0" cellspacing="0" border="0" width="100%">
			<tr style="background: url('<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/agenda/bg.jpg') #cccccc repeat-x; font-weight:bold; color:white;height:20px " valign="middle">
				<td valign="middle" align="center" width="15%">
				<a class="jlms_img_link" href="<?php echo sefRelToAbs("index.php?option=$option&amp;Itemid=".$Itemid."&amp;task=agenda&amp;mode=view_day&amp;id=".$id."&amp;date=".JLMS_dateToDisplay(strtotime("-1 day",$date),true)); ?>" title="<?php echo _JLMS_AGENDA_PREVIOUSDAY;?>">
				<img src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/agenda/ag_previous.png" class="JLMS_png" alt="<?php echo _JLMS_AGENDA_PREVIOUSDAY;?>" title="<?php echo _JLMS_AGENDA_PREVIOUSDAY;?>" width="16" height="16"  border="0"/>
				</a>
				</td>
				<td align="center" valign="middle"  width="70%">
				<?php echo day_month_lang(date('w, d m Y' , $date ),0,1,6,2);?>   
				</td>
				<td valign="middle" align="center" width="15%">
				<a class="jlms_img_link" href="<?php echo sefRelToAbs("index.php?option=$option&amp;Itemid=".$Itemid."&amp;task=agenda&amp;mode=view_day&amp;id=".$id."&amp;date=".JLMS_dateToDisplay(strtotime("+1 day",$date),true)); ?>" title="<?php echo _JLMS_AGENDA_NEXTDAY;?>">
				<img src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/agenda/ag_next.png" class="JLMS_png" alt="<?php echo _JLMS_AGENDA_NEXTDAY;?>" title="<?php echo _JLMS_AGENDA_NEXTDAY;?>" width="16" height="16"  border="0"/>
				</a></td>
			</tr>	
		<?php

		$k = 0;
		$z = 0;
		$ste = 1;
		while (isset($rows[$k]) &&  ($k < count($rows)) ) {
			if( (strtotime(date("Y-m-d",$date)) >= strtotime($rows[$k]->start_date) ) && (strtotime(date("Y-m-d",$date)) <= strtotime($rows[$k]->end_date ) )){
				$ste = 3-$ste;
				echo "<tr><td id='day".$k."' class='sectiontableentry".$ste."' colspan='3'>";
				$title = $rows[$k]->title;
				echo "<a href=".sefRelToAbs('index.php?option='.$option.'&Itemid='.$Itemid.'&task=agenda&id='.$id.'&agenda_id='.$rows[$k]->agenda_id.'&amp;date='.date("Y-m",$date).'#anc'.$rows[$k]->agenda_id).'-'.date("Y-m",$date)." >";
				echo "<img src=\"".$JLMS_CONFIG->get('live_site')."/components/com_joomla_lms/lms_images/agenda/ag_upcoming.png\" align='left' border='0' width='16px' height='16px' style='padding-right:5px' class='JLMS_png' />";
				echo "<b>".$title."</b></a><br />";
				echo "</td></tr><tr><td colspan='3'>";
				echo $rows[$k]->content;
				echo "</td></tr>";
				$z++;
			}	
			$k++;
		}
		if ($z == 0){
		echo "<tr><td style='' colspan='3'>"._JLMS_AGENDA_NO_ITEMS."</td></tr>";
		}
		?>
		</table>
		
	<?php
	}

	function show_add_event( $course_id, $option, &$agenda_item, $lists ) {
		$JLMS_CONFIG = & JLMSFactory::getConfig();
		$Itemid = $JLMS_CONFIG->get('Itemid');
		
		$content = $title = '';
		$start_date = $end_date = date('Y-m-d');

		if ( $agenda_item ){
			$content    = $agenda_item->content;
			$title      = $agenda_item->title;
			$start_date = $agenda_item->start_date;
			$end_date   = $agenda_item->end_date;
			$is_time_related = $agenda_item->is_time_related;
			$show_period = $agenda_item->show_period;
		}
		?>

<script language="javascript" type="text/javascript">
<!--
var start_date = '';
function setgood() {
	return true;
}
function submitbutton(task){
	elem = document.forms.adminForm;
	try {
		elem.onsubmit();
	} catch(e) {
		//alert(e);
	}
	if (task == 'save_agenda'){
		if (elem.jlms_agenda_title.value.length < 1){
			alert ('<?php echo _JLMS_AGENDA_TITLE_INCORRECT;?>');
			elem.jlms_agenda_title.focus();
		}
		else {
			elem.submit();
		}
	}
	else{
		elem.mode.value = task;
		elem.submit();
	}	
}
//-->
</script>
		<form action="<?php echo sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid");?>" method="post" name="adminForm" onsubmit="setgood();">
		<table width="100%" cellpadding="2" cellspacing="0" border="0" id="jlms_item_properties">
			<tr>
				<td colspan="2">
					<table width="100%" cellpadding="0" cellspacing="0" border="0" class="jlms_table_no_borders">
						<tr>
							<td width="100%" valign="middle" style="vertical-align:middle " rowspan="2" class="contentheading">
								<?php echo _JLMS_AGENDA_ADD_ITEM;?>
							</td>
							<td align="right" valign="top" style="vertical-align:top ">
								<?php $toolbar = array();
							$toolbar[] = array('btn_type' => 'save', 'btn_js' => "javascript:submitbutton('save_agenda');");
							$toolbar[] = array('btn_type' => 'cancel', 'btn_js' => "javascript:submitbutton('cancel_agenda');");
							echo JLMS_ShowToolbar($toolbar); ?>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td valign="middle"><br /><?php echo _JLMS_START_DATE;?></td>
				<td valign="middle" style="vertical-align:middle "><br />
					<table cellpadding="0" cellspacing="0" border="0" class="jlms_date_outer"><tr>
					<td valign="middle" style="vertical-align:middle ">
					<?php echo JLMS_HTML::_('calendar.calendar',$start_date,'start','start'); ?>
					</td></tr></table>
				</td>
			</tr>
			<tr>
				<td><br /><?php echo _JLMS_END_DATE;?></td>
				<td valign="middle" style="vertical-align:middle "><br />
					<table cellpadding="0" cellspacing="0" border="0" class="jlms_date_outer"><tr>
					<td valign="middle" style="vertical-align:middle ">
					<?php echo JLMS_HTML::_('calendar.calendar',$end_date,'end','end'); ?>
					</td></tr></table>
				</td>
			</tr>
			<tr>
					<td valign="top" style="vertical-align:top "><br /><?php echo _JLMS_IS_TIME_RELATED;?></td>
					<td><br />
						<?php
							if( isset($is_time_related)) { 
								JLMS_HTML::_('showperiod.field', $is_time_related, $show_period); 
							} else {
								JLMS_HTML::_('showperiod.field'); 
							}
						?>
					</td>
				</tr>
			<tr>
				<td colspan="2" height="20"></td>
			</tr>
			
			<tr>
				<td>
				<?php echo _JLMS_LIMIT_RESOURCE_TO_GROUPS;?>
				</td>
				<td>
					<?php echo $lists['is_limited'];?>
				</td>
			</tr>

			<tr>
				<td colspan="2" height="20"></td>
			</tr>

			<tr>
				<td valign="top">
				<?php echo _JLMS_LIMIT_RESOURCE_USERGROUPS;?>
				</td>
				<td>
				<?php echo $lists['groups'];?>
				</td>
			</tr>
			<tr>
				<td valign="top" align="left"  width="20%" colspan="2">
				<?php echo _JLMS_AGENDA_TITLE;?>
				</td>
			</tr>	
			<tr>	
				<td colspan="2">
				<input  type="text" name="jlms_agenda_title"  size="60" value="<?php echo $title;?>"  class="inputbox" />
				</td>
			</tr>
			<tr>
				<td colspan="2">
				<?php echo _JLMS_DESCRIPTION;?>
				</td>
			</tr>	
			<tr>
				<td  colspan="2">
				<?php echo jlms_editorArea( 'editor1', $content , 'jlms_agenda_detail', '100%;' , '250', '40', '20' ); ?>
				</td>
			</tr>
		</table>
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
		<input type="hidden" name="task" value="agenda" />
		<input type="hidden" name="mode" value="event_save" />
		<?php if (isset($agenda_item->agenda_id)){
			echo "<input type='hidden' name='edit' value='yes' />";
			echo "<input type='hidden' name='agenda_id' value='".$agenda_item -> agenda_id."' />";
		}?>
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="id" value="<?php echo $course_id;?>" />
		</form>
	<?php
	}
}
?>