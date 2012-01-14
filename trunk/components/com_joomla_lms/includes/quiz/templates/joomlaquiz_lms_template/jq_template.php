<?php
/**
* JoomaLMS elearning Software http://www.joomlalms.com/
**/
// no direct access
defined( '_VALID_MOS' ) or die( 'Restricted access' );

class JoomlaQuiz_template_class {
	function JQ_MainScreen($descr_cont = '&nbsp;', $task_cont = '<br />', $self_verification='', $progress_bar_js = true) {
	global $JLMS_CONFIG, $option, $Itemid;
	
	$doc = & JFactory::getDocument();
	
	$progressbar = '';
	if($progress_bar_js && $JLMS_CONFIG->get('quiz_progressbar', 0) == 1){
		$progressbar .= '<tr>
			<td colspan="3" width="100%" align="center" valign="middle" style="text-align:center; vertical-align:middle">
				<div id="progress_bar" style="margin:auto; display:none;"><!-- --></div>';
		if ($progress_bar_js) {
				$progressbar .= '
				<script language="JavaScript" type="text/javascript">
				<!--
				if(document.getElementById(\'progress_bar\') != null){
					var progressbar = new ProgressBar({id:\'progress_bar\',width:'.$JLMS_CONFIG->get('quiz_progressbar_width', 300).',highlight:'.$JLMS_CONFIG->get('quiz_progressbar_highlight', '0').',smooth:'.$JLMS_CONFIG->get('quiz_progressbar_smooth', '1').'});
				}
				//-->
				</script>';
		}
		$progressbar .= '
			</td>
		</tr>';
	}

	$jq_tmpl_html = <<<EOFTMPL
<script language="JavaScript" type="text/javascript">
<!--
//variables for fading message
function blank_enter(oEvent)
{
	if (navigator.appName == "Netscape")
	{
		if (oEvent.which == 13)
		return false;
	}
	else
	{
		if (oEvent.keyCode == 13)
		return false;
	}	
	return true;
}
var fd_startR = 250;
var fd_startG = 250;
var fd_startB = 250;
var fd_endR = 200;
var fd_endG = 0;
var fd_endB = 0;
var tbl_max_step = 0;
function JQ_MM_preloadImages() {
	var d=document;
	if (d.images) {
	if (!d.MM_p) d.MM_p=new Array();
	var i,j=d.MM_p.length,a=JQ_MM_preloadImages.arguments;
	for(i=0; i<a.length; i++) if (a[i].indexOf("#")!=0) { d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];
}}}
EOFTMPL;

$jq_tmpl_html .= "
JQ_MM_preloadImages('".$JLMS_CONFIG->get('live_site')."/components/com_joomla_lms/includes/quiz/templates/joomlaquiz_lms_template/images/hs_round.png','".$JLMS_CONFIG->get('live_site')."/components/com_joomla_lms/includes/quiz/templates/joomlaquiz_lms_template/images/drag_img.gif','".$JLMS_CONFIG->get('live_site')."/components/com_joomla_lms/includes/quiz/templates/joomlaquiz_lms_template/images/cont_img.gif','".$JLMS_CONFIG->get('live_site')."/components/com_joomla_lms/includes/quiz/templates/joomlaquiz_lms_template/images/apply.png','".$JLMS_CONFIG->get('live_site')."/components/com_joomla_lms/includes/quiz/templates/joomlaquiz_lms_template/images/back.png','".$JLMS_CONFIG->get('live_site')."/components/com_joomla_lms/includes/quiz/templates/joomlaquiz_lms_template/images/next.png');
//-->
</script>";

$doc->addStyleSheet( $JLMS_CONFIG->get('live_site').'/components/com_joomla_lms/includes/quiz/templates/joomlaquiz_lms_template/jq_template.css' );
	
$jq_tmpl_html .= '<div id="jq_quiz_container_title" style="visibility:hidden; display:none ">JoomlaQuiz plugin</div>
';

$jq_tmpl_html .= '<form name="selfverForm" method="post" action="'.ampReplace($JLMS_CONFIG->get('live_site').'/index.php?option='.$option.'&Itemid='.$Itemid).'">';
$jq_tmpl_html .= '<table id="jq_quiz_container_tbl" cellpadding="0" cellspacing="0" border="0" width="100%" class="jlms_table_no_borders">
	<tr>
		<td width="150" nowrap="nowrap" align="left" style="text-align:left ">
			<span id="jq_time_tick_container" style="visibility:hidden" >00:00</span><br />
			<span style="visibility:hidden" id="jq_quest_num_container">Question 0 of 0</span><br />
			<span style="visibility:hidden" id="jq_points_container">Point value 0</span>
			<span style="visibility:hidden" id="jq_question_id_container"></span>
		</td>
		<td width="100%" align="center" valign="middle" style="text-align:center; vertical-align:middle">
			<span id="error_messagebox" style="visibility:hidden;">JoomlaQuiz template for LMS</span>
		</td>
		<td width="150" valign="middle" align="right" id="jq_quiz_task_container" style="text-align:right; vertical-align:middle">'.$task_cont.'
		</td>
	</tr>
	'.$progressbar.'	
</table>
<table cellpadding="0" cellspacing="0" border="0" width="100%" style="min-height:250px; height:auto !important; height:250px;" class="jlms_table_no_borders">
	<tr>
		<td width="100%" style="width:100%; min-height:250px; height:auto;" valign="top">
			<br />
			<div id="jq_quiz_result_reviews" style="text-align:center; font-size: 14px; padding: 10px; visibility:hidden; display:none; overflow:hidden;"><!-- --></div>
			
			<div id="jq_quiz_explanation" style="padding: 10px; text-align: center; visibility:hidden; display:none;"><!-- --></div>
			
			<div id="jq_quiz_container1" style="width:100%; min-height:250px;">
				<div id="jq_quiz_container" style="padding:0px 0px 0px 0px; visibility:visible; width:auto !important; width:100%; text-align:center;">
					<div id="jq_quiz_container_selfver" style="padding-left:0px; text-align:left ">'.$self_verification.'</div>
					<div id="jq_quiz_container_description" style="padding-left:0px; text-align:left ">'.$descr_cont.'</div>
					<div id="jq_quiz_container_author" style="padding-left:0px; text-align:left "><!-- --></div>
				</div>
				
				<div id="jq_quiz_result_container" style="padding:0px 20px 0px 10px; visibility:hidden; display:none;"><!-- --></div>
			</div>
		</td>
	</tr>
</table>';
$jq_tmpl_html .= '</form>';

return $jq_tmpl_html;
	}

	function JQ_show_results_msg($header, $msg, $class_msg=0) {
	$class_msg = 'jq_msg_alert_'.$class_msg;
	$jq_tmpl_html = <<<EOFRESMSG
<div class="jq_results_container_outer">
		<div class="$class_msg">
			<table class="jq_results_msg jlms_table_no_borders"" cellpadding="0" cellspacing="0" border="0" align="center"><tr><td>
				$msg
			</td></tr></table>
		</div>
</div>
EOFRESMSG;
	return $jq_tmpl_html;
	}
	
//	function JQ_show_results_msg($header, $msg) {
//	$jq_tmpl_html = <<<EOFRESMSG
//<div class="jq_results_container_outer">
//	<div class="jq_results_container">
//		<div class="jq_results_container_header">
//			$header
//		</div>
//		<div class="jq_results_container_inner">
//		<div class="jq_results_in_container">
//			<table class="jq_results_msg" cellpadding="0" cellspacing="0" border="0"><tr><td>
//				$msg
//			</td></tr></table>
//		</div>
//		</div>
//	</div>
//</div>
//EOFRESMSG;
//	return $jq_tmpl_html;
//	}

	function JQ_show_results($header, $msg_array) {
	/*$jq_tmpl_html = <<<EOF_RES
<div class="jq_results_container_outer">
	<div class="jq_results_container">
		<div class="jq_results_container_header">$header</div>
		<div class="jq_results_container_inner">
			<div class="jq_results_in_container">
			<table class="jq_results_container" cellpadding="0" cellspacing="0" border="0">
EOF_RES;
	foreach ($msg_array as $msg_item) {
		$jq_tmpl_html .= "<tr><td class=\"jq_results_td1\">".$msg_item->text."</td><td class=\"jq_results_td2\">".$msg_item->value."</td></tr>" ."\n";
	}
	$jq_tmpl_html .= "</table>\n</div>\n</div>\n</div>\n</div>";
	return $jq_tmpl_html;*/
		$jq_tmpl_html = '<br /><table align="center" cellpadding="0" cellspacing="0" width="90%" class="'.JLMSCSS::_('jlmslist').'" style="margin: 0 auto; width: 90% !important;"><tr><'.JLMSCSS::tableheadertag().' colspan="2" class="'.JLMSCSS::_('sectiontableheader').'" style="text-align: center !important;">'.$header.'</'.JLMSCSS::tableheadertag().'></tr>';
		$k = 1;
		foreach ($msg_array as $msg_item) {
			$jq_tmpl_html .= '<tr class="'.JLMSCSS::_('sectiontableentry'.$k).'"><td>'.$msg_item->text.'</td><td>'.$msg_item->value.'</td></tr>' ."\n";
			$k = 3 - $k;
		}
		$jq_tmpl_html .= '</table>';
		return $jq_tmpl_html;
	}

	function JQ_show_results_footer($msg_array) {
	$jq_tmpl_html = <<<EOF_RES
<div class="jq_results_container_outer">
	<div class="jq_results_container">
		<div class="jq_results_container_inner">
			<div class="jq_results_in_container">
			<table class="jq_results_container jlms_table_no_borders" cellpadding="0" cellspacing="0" border="0" align="center"><tr>
EOF_RES;
	foreach ($msg_array as $msg_item) {
		$jq_tmpl_html .= "<td class='jq_footer_td' align='center'>".$msg_item->text."</td>";
	}
	$jq_tmpl_html .= "</tr></table>\n</div>\n</div>\n</div>\n</div>";
	return $jq_tmpl_html;
	
	}
	
	function JQ_show_results_footer_content($msg_array) {
	$jq_tmpl_html = <<<EOF_RES

			<table class="jq_results_container jlms_table_no_borders" cellpadding="0" cellspacing="0" border="0" align="center"><tr>
EOF_RES;

		$jq_tmpl_html .= "<td class='jq_footer_td' align='center'>".$msg_array."</td>";

	$jq_tmpl_html .= "</tr></table>\n";
	return $jq_tmpl_html;
	}
	
	function JQ_show_results_footer_content_bars($images_array, $title_array, $count_array, $id) {
		$JLMS_CONFIG = & JLMSFactory::getConfig();

		$jq_tmpl_html = '<table width="100%" cellpadding="0" cellspacing="0" border="0" align="center" class="'.JLMSCSS::_('jlmslist').'" style="margin: 0 auto;">' . "\n\r";
		 	for($i=0,$n=count($images_array);$i<$n;$i++)
		 	{
		 		if(!isset($images_array[$i]->is_survey)){
		 			if(isset($images_array[$i]->graph)){
			 			$q_num = $i+1;
				 		$jq_tmpl_html .= '<tr><td align="left">';
				 		$jq_tmpl_html .= _JLMS_QUIZ_QUESTION_NUM. $q_num .': <b>'.$title_array[$i]->graph.'</b>';
				 		$jq_tmpl_html .= '</td></tr>';	
				 		$jq_tmpl_html .= '<tr><td align="center">';
				 		$jq_tmpl_html .= '<img src="'.$JLMS_CONFIG->get('live_site').'/'.$images_array[$i]->graph.'" width="600" height="'.($count_array[$i]->graph?($count_array[$i]->graph*200):200).'" alt="'.$title_array[$i]->graph.'" title="'.$title_array[$i]->graph.'" border=\'0\' /><br />';
				 		$jq_tmpl_html .= '</td></tr>';
			 		}
			 		if(isset($images_array[$i]->correct)){
			 			$q_num = $i+1;
				 		$jq_tmpl_html .= '<tr><td align="left">';
				 		$jq_tmpl_html .= _JLMS_QUIZ_VIEW_STATISTICS_CI;
				 		$jq_tmpl_html .= '</td></tr>';	
				 		$jq_tmpl_html .= '<tr><td align="center">';
				 		$jq_tmpl_html .= '<img src="'.$JLMS_CONFIG->get('live_site').'/'.$images_array[$i]->correct.'" width="600" height="'.($count_array[$i]->correct?($count_array[$i]->correct*200):200).'" alt="'.$title_array[$i]->correct.'" title="'.$title_array[$i]->correct.'" border=\'0\' /><br />';
				 		$jq_tmpl_html .= '</td></tr>';
			 		}
		 		}
			}
		$jq_tmpl_html .= "</table>\n";
		return $jq_tmpl_html;
	}

	function JQ_show_messagebox($header, $msg, $class_msg=0) {
	$msg = html_entity_decode($msg);
	
	$class_msg = 'jq_msg_alert_'.$class_msg;
	$jq_tmpl_html = <<<EOF_MSG
	<div class="jq_messagebox_container_outer">
		<div class="$class_msg">
			<table width="100%" style="height: 100%;" cellpadding="0" cellspacing="0" class="jlms_table_no_borders"><tr><td align="center" valign="middle" style="text-align: center; vertical-align: middle;">
				$msg
			</td></tr></table>
		</div>
	</div>
EOF_MSG;
	return $jq_tmpl_html;
	}
	
	
//	function JQ_show_messagebox($header, $msg, $class_msg=0) {
//	$msg = html_entity_decode($msg);
//	$jq_tmpl_html = <<<EOF_MSG
//<div class="jq_messagebox_container_outer">
//	<div class="jq_messagebox_container">
//		<div class="jq_messagebox_container_header">$header</div>
//		<div class="jq_messagebox_container_inner">
//			<div class="jq_messagebox_container_message">
//				<table width="100%" height="100%" cellpadding="0" cellspacing="0"><tr><td align="center" valign="middle" class="jq_messagebox_message">
//					$msg
//				</td></tr></table>
//			</div>
//		</div>
//	</div>
//</div>
//EOF_MSG;
//	return $jq_tmpl_html;
//	}

	function JQ_createMChoice($qdata, $qtype, $suffix='') { //html template for 'Multiple Choice' and 'True/False' questions
		global $option, $JLMS_CONFIG, $Itemid;
		
		$course_id = $JLMS_CONFIG->get('course_id');
		$jq_tmpl_html = "<table align='left' class='jlms_table_no_borders qpadding'>" . "\n";
		foreach ($qdata as $qone) {
			if($qtype == 12){
				$jq_tmpl_html .= "<tr><td><input name='quest_choice".$suffix."' id='qc_".$qone->value.$suffix."' value='".$qone->value."' type='radio'".(($qone->c_right == 1)?" checked='checked'":"").(($qone->c_review == 1)?" disabled='disabled'":"")."/></td><td align='left' valign='middle'><label for='qc_".$qone->value."'><img src='".ampReplace($JLMS_CONFIG->get('live_site')."/index.php?tmpl=component&option=".$option."&task=quizzes&page=imgs_v&id=".$course_id."&file_id=".$qone->text."&Itemid=".$Itemid."&imgs_name=".$qone->imgs_name."&pic_width=".$JLMS_CONFIG->get('quiz_match_max_width', 250)."&pic_height=".$JLMS_CONFIG->get('quiz_match_max_height', 250)."&bg_color=dddddd")."' border='0' alt='".$qone->imgs_name."'/></label></td></tr>" . "\n";
			} else {
				$jq_tmpl_html .= "<tr><td><input name='quest_choice".$suffix."' id='qc_".$qone->value.$suffix."' value='".$qone->value."' type='radio'".(($qone->c_right == 1)?" checked='checked'":"").(($qone->c_review == 1)?" disabled='disabled'":"")."/></td><td align='left'><label for='qc_".$qone->value."'>".$qone->text."</label></td></tr>" . "\n";
			}
		}
		$jq_tmpl_html .= "</table>" . "\n";
		return $jq_tmpl_html;
	}

	function JQ_createMResponse($qdata, $qtype, $nojs=0, $suffix='') { //html template for 'Multiple Response' questions
		global $option, $JLMS_CONFIG, $Itemid;
		
		$course_id = $JLMS_CONFIG->get('course_id');
		$jq_tmpl_html = "<table align='left' class='jlms_table_no_borders qpadding'>" . "\n";
		$i = 0;
		foreach ($qdata as $qone) {
			if($nojs){
				if($qtype == 13){
					$jq_tmpl_html .= "<tr><td><input name='quest_choice[".$i."]' id='qc_".$qone->value.$suffix."' value='".$qone->value."' type='checkbox'".(($qone->c_right == 1)?" checked='checked'":"").(($qone->c_review == 1)?" disabled='disabled'":"")."/></td><td align='left'><label for='qc_".$qone->value."'><img src='".ampReplace($JLMS_CONFIG->get('live_site')."/index.php?tmpl=component&option=".$option."&task=quizzes&page=imgs_v&id=".$course_id."&file_id=".$qone->text."&Itemid=".$Itemid."&imgs_name=".$qone->imgs_name."&pic_width=".$JLMS_CONFIG->get('quiz_match_max_width', 250)."&pic_height=".$JLMS_CONFIG->get('quiz_match_max_height', 250)."&bg_color=dddddd")."' border='0' alt='".$qone->imgs_name."'/></label></td></tr>" . "\n";
				} else {
					$jq_tmpl_html .= "<tr><td><input name='quest_choice[".$i."]' id='qc_".$qone->value.$suffix."' value='".$qone->value."' type='checkbox'".(($qone->c_right == 1)?" checked='checked'":"").(($qone->c_review == 1)?" disabled='disabled'":"")."/></td><td align='left'><label for='qc_".$qone->value."'>".$qone->text."</label></td></tr>" . "\n";
				}
			} else {
				if($qtype == 13){
					$jq_tmpl_html .= "<tr><td><input name='quest_choice' id='qc_".$qone->value.$suffix."' value='".$qone->value."' type='checkbox'".(($qone->c_right == 1)?" checked='checked'":"").(($qone->c_review == 1)?" disabled='disabled'":"")."/></td><td align='left'><label for='qc_".$qone->value."'><img src='".ampReplace($JLMS_CONFIG->get('live_site')."/index.php?tmpl=component&option=".$option."&task=quizzes&page=imgs_v&id=".$course_id."&file_id=".$qone->text."&Itemid=".$Itemid."&imgs_name=".$qone->imgs_name."&pic_width=".$JLMS_CONFIG->get('quiz_match_max_width', 250)."&pic_height=".$JLMS_CONFIG->get('quiz_match_max_height', 250)."&bg_color=dddddd")."' border='0' alt='".$qone->imgs_name."'/></label></td></tr>" . "\n";
				} else {
					$jq_tmpl_html .= "<tr><td><input name='quest_choice' id='qc_".$qone->value.$suffix."' value='".$qone->value."' type='checkbox'".(($qone->c_right == 1)?" checked='checked'":"").(($qone->c_review == 1)?" disabled='disabled'":"")."/></td><td align='left'><label for='qc_".$qone->value."'>".$qone->text."</label></td></tr>" . "\n";
				}
			}
			$i++;	
		}
		$jq_tmpl_html .= "</table>" . "\n";
		return $jq_tmpl_html;
	}

	function JQ_createMDropDown($qdata) { //html template for 'Matching Drop-Down' questions
		$jq_tmpl_html = "<table align='center' class='jlms_table_no_borders qpadding'>" . "\n";
		foreach ($qdata as $qone) {
			$jq_tmpl_html .= "<tr><td align='left'>".$qone->c_left_text."</td><td align='left'>".$qone->c_right_text."</td></tr>" . "\n";
		}
		$jq_tmpl_html .= "</table>" . "\n";
		return $jq_tmpl_html;
	}

	function JQ_createBlank($review_val = '', $defcbtext = '') { //html template for 'Blank' questions
		
		$q_val = $defcbtext ? $defcbtext : stripslashes($review_val);
		$jq_tmpl_html = "<input type='text' name='quest_blank' onKeyPress=\"if (!blank_enter(event)) return false;\" class='inputbox' size='32' value=\"".$q_val."\"".(($review_val)?" disabled='disabled'":'')."/>" . "\n";
		return $jq_tmpl_html;
	}

	function JQ_createBlankReview($review_val = '', $defcbtext = '') { //html template for 'Blank' questions
		$q_val = $review_val ? $review_val : $defcbtext;
		$jq_tmpl_html = "<b>".$q_val."</b>". "\n";
		return $jq_tmpl_html;
	}

	function JQ_createSurvey($is_review = 0, $text_survey='') { //html template for 'Survey' questions
		$jq_tmpl_html = "<textarea name='survey_box' rows='5' cols='200' class='inputbox' style='width:85%; height:100px'".(($is_review == 1)?" disabled='disabled'":"").">".$text_survey."</textarea>" . "\n";
		return $jq_tmpl_html;
	}
	function JQ_createScale($qdata, $is_review = 0)
	{
		$jq_tmpl_html = "<table width='80%' align='center' cellpadding='0' cellspacing='0' class='".JLMSCSS::_('jlmslist')."' style='margin-left: auto !important; margin-right: auto !important; width: 80% !important;'><tr><".JLMSCSS::tableheadertag()." class=".JLMSCSS::_('sectiontableheader').">&nbsp;</".JLMSCSS::tableheadertag().">" . "\n";
		$arr_scale = array();
		foreach ($qdata as $qone) {
			if($qone->c_type == 1)
			{
				$jq_tmpl_html .= "<".JLMSCSS::tableheadertag()." class=".JLMSCSS::_('sectiontableheader')." align='center' style='text-align: center !important;'>".$qone->c_field."</".JLMSCSS::tableheadertag().">" . "\n";
				$arr_scale[] = $qone->c_id;
			}
		}
		$jq_tmpl_html .= "</tr>" . "\n";
		
		$k=1;
		$z=0;
		foreach ($qdata as $qone) {
			
			if($qone->c_type == 0)
			{
				$jq_tmpl_html .= "<tr class='".JLMSCSS::_('sectiontableentry'.$k)."'>" . "\n";	
				$jq_tmpl_html .= "<td align='left' style='padding-right:30px;'>".$qone->c_field."</td>" . "\n";
				
				for($i=0;$i<count($arr_scale);$i++)
				{
					$chk = '';
					if(isset($qone->inchek) && $qone->inchek && $arr_scale[$i] == $qone->inchek) $chk = 'checked="checked"';
					$jq_tmpl_html .= "<td align='center'><input name='ch_scale_".$z."' type='radio' value='".$arr_scale[$i]."' ".$chk." ".(($is_review == 1)?" disabled='disabled'":"")." /></td>" . "\n";
				}
				$z++;
				$jq_tmpl_html .= "</tr>" . "\n";
				
				$k = 3 - $k;	
			}
		}
		$jq_tmpl_html .= "</table>" . "\n";
		$jq_tmpl_html .= "<input type='hidden' name='scale_count' value='".$z."' />" . "\n";
		return $jq_tmpl_html;
	}
}
?>