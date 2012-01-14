<?php
/**
* joomla_lms.course_lpathstu.html.php
* (c) JoomaLMS eLearning Software http://www.joomlalms.com/
**/

// no direct access
defined( '_JLMS_EXEC' ) or die( 'Restricted access' );

class JLMS_course_lpathstu_html {
	function showLPath_MainPage( $course_id, $lpath_id, $option, &$lpath_data, &$lpath_contents, &$quizzes_data ) {
		$JLMS_CONFIG = & JLMSFactory::getConfig();
		$Itemid = $JLMS_CONFIG->get('Itemid');
		$lp_params = new JLMSParameters($lpath_data->lp_params);
		$show_lpath_contents_at_the_left = $lp_params->get('navigation_type', 0 ) ? true : false;
		$is_quiz = (count($quizzes_data)?true:false);
		$is_drag_drop = false;
		$c_time_limit = 0;
		$inside_lp = 1;
		$c_slide = false; //pri starte quiza
		$c_generated_panel = true; // Contents of quiz will be generated on 'satrt' action
		$c_slide_update = false;
		$quiz_id = 0;
		foreach ($quizzes_data as $qd) {
			$quiz_id = $qd->c_id;
			foreach ($qd->panel_data as $q) {
				if ($q->c_type == 4) {
					$is_drag_drop = true; break;
				}
			}
		}
		require_once(_JOOMLMS_FRONT_HOME . "/includes/ajax_features.class.php");

		// preloading QUIZ languge (28.02.2007 new method) (all quizzes messages now in global quiz language)
		global $JLMS_LANGUAGE;
		JLMS_require_lang($JLMS_LANGUAGE, 'quiz.lang', $JLMS_CONFIG->get('default_language'));
		require(dirname(__FILE__) . '/includes/quiz/quiz_language.php');
		global $jq_language;

		$e = true; // enable force echo

		$AF = new JLMS_Ajax_Features();
		$AF->set('c_slide', $c_slide);
		$AF->set('c_generated_panel', $c_generated_panel);
		$AF->set('c_slide_update', $c_slide_update);
		$AF->set('quiz_id', $quiz_id);
		if ($is_quiz) {
			$AF->GetInclude_Msgs($e);
		}
		$AF->JS_open($e);
		$AF->GetFunc_JS_in_array($e);
		$AF->GetFunc_RFE($e);
		if($is_quiz) {
			$document = & JFactory::getDocument();
			$document->addStyleSheet( $JLMS_CONFIG->getCfg('live_site').'/components/com_joomla_lms/includes/quiz/templates/joomlaquiz_lms_template/jq_template.css');
			echo "function jlms_gotoQuestion(qid) { if (stu_step_type == 5 && user_unique_id && quiz_id) { jlms_SwitchOpenedContents();JQ_gotoQuestionOn(qid);} }";
			$AF->QUIZ_JS_DrDr_Code($e);
			$AF->QUIZ_preloadMsgs($e, $jq_language);
			$AF->QUIZ_doInitialize($e, $JLMS_CONFIG->getCfg('live_site')."/index.php?tmpl=component&option=$option&inside_lp=$inside_lp&Itemid=$Itemid&jlms=1&task=quiz_ajax_action&id=$course_id", '');

			/* We must override this func (to reduce JS weigth)
			$AF->QUIZ_MakeRequest($e); */
?>
function jq_MakeRequest(url, do_clear) {
	if (do_clear == 1) {
		jq_showLoading();
	}
	quiz_blocked == 1;
	jlms_MakeRequest('jq_AnalizeRequest', url, 'quiz');
}			
<?php
			$req_tasks = array('start', 'seek_quest', 'review_start', 'review_next', 'review_finish', 'next', 'no_attempts', 'email_results', 'time_is_up', 'finish', 'results', 'failed');
			$AF->QUIZ_AnalizeRequest($e, 'jq_AnalizeRequest', $req_tasks);
			$AF->QUIZ_releaseblock($e);
			$AF->QUIZ_StartTickTack($e);
			$AF->QUIZ_ContinueTickTack($e);
			//$AF->QUIZ_StartQuizOn($e);
			$AF->QUIZ_StartQuizOn($e);
			$AF->QUIZ_StartQuiz($e);
			$AF->QUIZ_GoToQuestionOn($e);
			$AF->QUIZ_GoToQuestion($e);
			$AF->QUIZ_EmailResults($e);
			$AF->QUIZ_StartReview($e);
			$AF->QUIZ_ReviewNext($e);
			$AF->QUIZ_ReviewPrev($e);
			$AF->QUIZ_Check_selectRadio($e);
			$AF->QUIZ_Check_selectCheckbox($e);
			$AF->QUIZ_Check_valueItem($e);
			$AF->QUIZ_QuizNextOn($e);
			
			$toolbar = array();
			$toolbar[] = array('btn_type' => 'quiz_ok', 'btn_js' => "javascript:jq_QuizNextOn(); void(0);");
			if ($inside_lp && !$show_lpath_contents_at_the_left) {
				$toolbar[] = array('btn_type' => 'contents', 'btn_js' => "javascript:ajax_action('contents_lpath');");
			} else {
				if ($c_slide && !$show_lpath_contents_at_the_left) {
					$toolbar[] = array('btn_type' => 'contents', 'btn_js' => "javascript:jq_ShowPanel();");
				}
			}
			$m_str_no_skip = JLMS_ShowToolbar($toolbar);
			//8.10.08 - (Max) - dva toolbars dlia skip i standart
			$toolbar = array();
			$toolbar[] = array('btn_type' => 'quiz_ok', 'btn_js' => "javascript:jq_QuizNextOn(); void(0);");
			$toolbar[] = array('btn_type' => 'skip', 'btn_js' => "javascript:JQ_gotoQuestion(__skip__);void(0);");	
			if ($inside_lp && !$show_lpath_contents_at_the_left) {
				$toolbar[] = array('btn_type' => 'contents', 'btn_js' => "javascript:ajax_action('contents_lpath');");
			} else {
				if ($c_slide && !$show_lpath_contents_at_the_left) {
					$toolbar[] = array('btn_type' => 'contents', 'btn_js' => "javascript:jq_ShowPanel();");
				}
			}
			$m_str_skip = JLMS_ShowToolbar($toolbar);
			
			$AF->QUIZ_QuizContinue($e, $m_str_no_skip, $m_str_skip);

			$cf_url = "'&atask=finish_stop&quiz='+quiz_id+'&stu_quiz_id='+stu_quiz_id";
			$AF->QUIZ_QuizContinueFinish($e, $cf_url);

			$toolbar = array();
			$toolbar[] = array('btn_type' => 'quiz_ok', 'btn_js' => "javascript:jq_QuizNextOn(); void(0);");
			if ($inside_lp && !$show_lpath_contents_at_the_left) {
				$toolbar[] = array('btn_type' => 'contents', 'btn_js' => "javascript:ajax_action('contents_lpath');");
			} else {
				if ($c_slide && !$show_lpath_contents_at_the_left) {
					$toolbar[] = array('btn_type' => 'contents', 'btn_js' => "javascript:jq_ShowPanel();");
				}
			}
			$m_str = JLMS_ShowToolbar($toolbar);
			$AF->QUIZ_QuizBack($e, $m_str);

			$AF->QUIZ_Next($e);

			$AF->QUIZ_showLoading($e);
			/*
			$AF->QUIZ_UpdateTaskDiv_htm($e);
			$AF->QUIZ_UpdateTaskDiv($e, $c_slide);*/
			// We must override task div functionality for quiz
			?>
function jq_UpdateTaskDiv_htm(htm_txt) {
	getObj('jlms_lpath_menu').innerHTML = htm_txt;
}
function jq_UpdateTaskDiv(task) {

	switch (task) {
		case 'start':
			getObj('jlms_lpath_menu').innerHTML = jq_StartButton('jq_StartQuizOn()', mes_quiz_start);
		break;
		case 'next':
			getObj('jq_quest_num_container').innerHTML = mes_quest_number.replace("{X}", cur_quest_num).replace("{Y}", quiz_count_quests);
			getObj('jq_quest_num_container').style.visibility = "visible";
			getObj('jq_points_container').innerHTML = mes_quest_points.replace("{X}", cur_quest_score);
			getObj('jq_points_container').style.visibility = "visible";
			<?php if($JLMS_CONFIG->get('quiz_progressbar', 0) == 1){?>
			if (getObj('progress_bar')) { getObj('progress_bar').style.display = "block"; }
			<?php } ?>
		break;
		case 'review_next':
			getObj('jq_quest_num_container').innerHTML = mes_quest_number.replace("{X}", cur_quest_num).replace("{Y}", quiz_count_quests);
			getObj('jq_quest_num_container').style.visibility = "visible";
			getObj('jq_points_container').innerHTML = mes_quest_points.replace("{X}", cur_quest_score);
			getObj('jq_points_container').style.visibility = "visible";
		break;
		case 'continue':
			<?php if($JLMS_CONFIG->get('quiz_progressbar', 0) == 1){?>
			if (getObj('progress_bar')) { getObj('progress_bar').style.display = "block"; }
			<?php } ?>
		break;
		case 'continue_finish':
			<?php if($JLMS_CONFIG->get('quiz_progressbar', 0) == 1){?>
			if (getObj('progress_bar')) { getObj('progress_bar').style.display = "block"; }
			<?php } ?>
		break;
		case 'finish':
			getObj('jlms_lpath_menu').innerHTML = lp_menu_item_contents;
			getObj('jq_quest_num_container').style.visibility = 'hidden';
			getObj('jq_points_container').style.visibility = 'hidden';
			<?php if($JLMS_CONFIG->get('quiz_progressbar', 0) == 1){?>
			if (getObj('progress_bar')) { getObj('progress_bar').style.display = "none"; }
			<?php } ?>
		break;
		case 'clear':
			getObj('jlms_lpath_menu').innerHTML = '';
			getObj('jq_quest_num_container').style.visibility = 'hidden';
			getObj('jq_points_container').style.visibility = 'hidden';
		break;
	}
<?php if ($c_slide) { ?>	
	if (result_is_shown == 1) { jq_ShowPanel(); }
<?php } ?>
}
<?php
			$AF->QUIZ_NextButton($e);
			$AF->QUIZ_ContinueButton($e);
			$AF->QUIZ_StartButton($e);
			$AF->QUIZ_BackButton($e);
			if ($c_slide) {
				$AF->QUIZ_ShowPanel_go($e);
				$AF->QUIZ_HidePanel_go($e);
				$AF->QUIZ_ShowPanel($e);
			}
		}
		$AF->GetFunc_JS_URLencode($e);
		$AF->GetFunc_JS_TRIM_str($e);
		$AF->JS_close($e);
		$lpc_btn = $AF->Get_LPContents_btn(false);
		$toolbar = array();
		$toolbar[] = array('btn_type' => 'next', 'btn_js' => "javascript:ajax_action('next_lpathstep');");
		if (!$show_lpath_contents_at_the_left) {
			$toolbar[] = array('btn_type' => 'contents', 'btn_js' => "javascript:ajax_action('contents_lpath');");
		}
		$rs = JLMS_ShowToolbar($toolbar);
		$rs = str_replace('"components/com_joomla_lms','"'.$JLMS_CONFIG->getCfg('live_site').'/components/com_joomla_lms',$rs);
		$lpc_btn = str_replace('/','\/',str_replace('"', "\\\"", $rs));
?>
<script language="javascript" type="text/javascript">
<!--//--><![CDATA[//><!--
var timer_KeepAL = 990066;
<?php /* variable timer_keepAl was added 21.08.2007 - for keeping joomla session whilst SCORM playing */ ?>
var lp_menu_item_contents = "<?php echo $lpc_btn; ?>";
var jlms_contents_visible = 0;
var jlms_contents_visible_only = 0;
var jlms_lpath = <?php echo $lpath_id;?>;
var jlms_course = <?php echo $course_id;?>;
function ajax_action(pressbutton) {
	if ((jlms_blocked == 1) && (pressbutton != 'contents_lpath') && (pressbutton != 'get_document')) {
		if (jlms_allow_pending_task == 1) {
			if (jlms_is_pending_task == 0) {
				jlms_is_pending_task = 1;
				jlms_pending_task = pressbutton;
			}
		}
	} else {
		jlms_blocked = 1;
		if ((pressbutton != 'contents_lpath') && (pressbutton != 'get_document')) {
			$('jlms_lpath_completion_msg_container').setStyles({visibility: 'hidden',display: 'none'});
		}
		switch (pressbutton) {
			case 'lpath_restart':
<?php if (!$show_lpath_contents_at_the_left) { ?>
				jlms_SwitchOpenedContents();
<?php } ?>
				jlms_MakeRequest('jlms_AnalizeRequest', '&action=restart_lpath&id='+jlms_lpath, 'lpath');
			break;
			case 'start_lpath':
<?php if (!$show_lpath_contents_at_the_left) { ?>
				jlms_SwitchOpenedContents();
<?php } ?>
				jlms_MakeRequest('jlms_AnalizeRequest', '&action=start_lpath&id='+jlms_lpath, 'lpath');
			break;
			case 'next_lpathstep':
<?php if (!$show_lpath_contents_at_the_left) { ?>
				jlms_SwitchOpenedContents();
<?php } ?>
				jlms_MakeRequest('jlms_AnalizeRequest', '&action=next_lpathstep&id='+jlms_lpath+'&step_id='+stu_step_id, 'lpath');
			break;
			case 'prev_lpathstep':
<?php if (!$show_lpath_contents_at_the_left) { ?>
				jlms_SwitchOpenedContents();
<?php } ?>
				jlms_MakeRequest('jlms_AnalizeRequest', '&action=prev_lpathstep&id='+jlms_lpath+'&step_id='+stu_step_id, 'lpath');
			break;
			case 'lpath_seek':
<?php if (!$show_lpath_contents_at_the_left) { ?>
				jlms_SwitchOpenedContents();
<?php } ?>
				jlms_MakeRequest('jlms_AnalizeRequest', '&action=seek_lpathstep&id='+jlms_lpath+'&step_id='+seek_step_id, 'lpath');
			break;
			case 'contents_lpath':
				jlms_blocked = 0;
<?php if (!$show_lpath_contents_at_the_left) { ?>
				jlms_SwitchContents();
<?php } else { ?>
				jlms_SwitchContents2();
<?php } ?>
			break;
			case 'get_document':
				jlms_blocked = 0;
				/*window.open('index.php?tmpl=component&no_html=1&option=<?php echo $option;?>&Itemid=<?php echo $Itemid;?>&task=show_lpath&action=get_lpath_doc&user_unique_id=' + user_unique_id +'&user_start_id='+user_start_id+'&id='+jlms_lpath+'&course_id='+jlms_course+'&doc_id='+get_doc_id+'&step_id='+stu_step_id
				,null,"height=200,width=400,status=yes,toolbar=no,menubar=no,location=no");*/
				window.location.href = '<?php echo $JLMS_CONFIG->getCfg('live_site');?>/index.php?tmpl=component&no_html=1&option=<?php echo $option;?>&Itemid=<?php echo $Itemid;?>&task=show_lpath&action=get_lpath_doc&user_unique_id=' + lp_user_unique_id +'&user_start_id='+user_start_id+'&id='+jlms_lpath+'&course_id='+jlms_course+'&doc_id='+get_doc_id+'&step_id='+stu_step_id;
				//return true;
				void(0);
			break;
			default:
				jlms_blocked = 0;
			break;
		}
	}
}
<?php
	if( JLMS_mootools12() ) {
		$fxFunc = 'Tween';
	} else {
		$fxFunc = 'Style';
	}	 
	
	$additon_js = '
var mySlide_contents2;
var mySlide_contents2_width_start = 0;
var mySlide_contents2_width_end = 0;
var mySlide_contents3;
var mySlide_contents2_mode = 2;
var mySlide_contents3_margin_start = 0;
var mySlide_contents3_margin_end = 10;
var mySlide_contents4;
var winScroller2 = new Fx.Scroll(window);
function jlms_prepare_el_mySlide_contents2() {
	mySlide_contents2 = new Fx.'.$fxFunc.'(\'jlms_lpath_contents_container\', \'width\');
	mySlide_contents2_width_start = $(\'jlms_lpath_contents_container\').getStyle(\'width\');
	mySlide_contents3_margin_start = $(\'jlms_lpath_descr\').getStyle(\'margin-left\');
	mySlide_contents3 = new Fx.'.$fxFunc.'(\'jlms_lpath_descr\', \'margin-left\');
	mySlide_contents4 = new Fx.'.$fxFunc.'(\'jlms_lpath_completion_msg_container\', \'margin-left\');
	$(\'jlms_lpath_completion_msg_container\').setStyles({\'margin-left\': \'0\'});
	$(\'jlms_lpath_descr\').setStyles({\'margin-left\': \'0\'});
	//mySlide_contents2.hide();
	//$(\'jlms_lpath_contents_container\').setStyles({visibility: \'visible\',display: \'\'});
}
function jlms_SwitchContents2() {
	if (mySlide_contents2_mode == 2) {
		$(\'jlms_lpath_contents_container\').setStyles({visibility: \'visible\',display: \'\'});
		mySlide_contents2.start(0, mySlide_contents2_width_start);
		mySlide_contents3.start(0, mySlide_contents3_margin_start);
		mySlide_contents4.start(0, mySlide_contents3_margin_start);
		mySlide_contents2_mode = 1;
		$(\'left_nav_collapser_container\').setStyles({visibility: \'visible\',display: \'\'});
	} else if (mySlide_contents2_mode == 1) {
		mySlide_contents2.start(mySlide_contents2_width_end);
		mySlide_contents3.start(mySlide_contents3_margin_end);
		mySlide_contents4.start(mySlide_contents3_margin_end);
		$(\'left_nav_collapser_container\').setStyles({visibility: \'hidden\',display: \'none\'});
		mySlide_contents2_mode = 0;
	} else {
		$(\'jlms_lpath_contents_container\').setStyles({visibility: \'visible\',display: \'\'});
		mySlide_contents2.start(mySlide_contents2_width_start);
		mySlide_contents3.start(mySlide_contents3_margin_start);
		mySlide_contents4.start(mySlide_contents3_margin_start);
		mySlide_contents2_mode = 1;
		$(\'left_nav_collapser_container\').setStyles({visibility: \'visible\',display: \'\'});
	}
}
';
if( JLMS_mootools12() ) 
{
	$setHTML = 'set(\'html\',';					
} else {
	$setHTML = 'setHTML(';
}
$JLMS_CONFIG->set('jlms_aditional_js_code', $JLMS_CONFIG->get('jlms_aditional_js_code','').$additon_js);
$domready = '
jlms_prepare_el_mySlide_contents2();
';
$JLMS_CONFIG->set('web20_domready_code', $JLMS_CONFIG->get('web20_domready_code','').$domready);
?>


<?php if ($JLMS_CONFIG->get('web20_effects', true) && !$show_lpath_contents_at_the_left) {
	$additon_js = '
var mySlide_contents;
function jlms_prepare_el_mySlide_contents() {
	mySlide_contents = new Fx.Slide(\'jlms_lpath_contents_container\');
	mySlide_contents.hide();
	$(\'jlms_lpath_contents_container\').setStyles({visibility: \'visible\',display: \'\'});
}
';
$JLMS_CONFIG->set('jlms_aditional_js_code', $JLMS_CONFIG->get('jlms_aditional_js_code','').$additon_js);
$domready = '
jlms_prepare_el_mySlide_contents();
';
$JLMS_CONFIG->set('web20_domready_code', $JLMS_CONFIG->get('web20_domready_code','').$domready);

?>

function jlms_SwitchOpenedContents() {
	if ($defined(mySlide_contents)) {
		var type = typeof mySlide_contents;
		if (type == 'object') {
			mySlide_contents.hide();
		}
	} else {
		jlms_prepare_el_mySlide_contents();
	}
}
function jlms_SwitchContentsOnly(par) {
	if (par == 'show') {
		mySlide_contents.slideIn();
	} else {
		mySlide_contents.hide();
	}
}
function jlms_SwitchContents() {
	mySlide_contents.toggle();
}
<?php } elseif($show_lpath_contents_at_the_left) { ?>
function jlms_SwitchOpenedContents() {
	jlms_SwitchContents();
}
function jlms_SwitchContentsOnly(par) {
	if (par == 'show') {
		if (jlms_contents_visible == 1) {
			
		} else {
			var vis_style1 = 'visible';
			var disp_style1 = '';
			var jlcc = getObj('jlms_lpath_contents_container');
			jlcc.style.visibility = vis_style1;
			jlcc.style.display = disp_style1;
		}
	}
}
function jlms_SwitchContents() {
	if (jlms_contents_visible == 1) {
		
	} else {
		var vis_style1 = 'visible';
		var disp_style1 = '';
		var vis_style2 = 'visible';
		var disp_style2 = '';
		var jlcc = getObj('jlms_lpath_contents_container');
		var jldc = getObj('jlms_lpath_descr');
		jlcc.style.visibility = vis_style1;
		jlcc.style.display = disp_style1;
		jldc.style.visibility = vis_style2;
		jldc.style.display = disp_style2;
		if (jlms_contents_visible == 1) { jlms_contents_visible = 0;}
		else { jlms_contents_visible = 1; }
	}
}
<?php } else{ ?>
function jlms_SwitchOpenedContents() {
	if (jlms_contents_visible == 1) {
		jlms_SwitchContents();
	}
}
function jlms_SwitchContentsOnly(par) {
	if (par == 'show') {
		var vis_style1 = 'visible';
		var disp_style1 = '';
	} else {
		var vis_style1 = 'hidden';
		var disp_style1 = 'none';
	}
	var jlcc = getObj('jlms_lpath_contents_container');
	jlcc.style.visibility = vis_style1;
	jlcc.style.display = disp_style1;
}
function jlms_SwitchContents() {
	var vis_style1 = 'visible';
	var disp_style1 = '';
	var vis_style2 = 'hidden';
	var disp_style2 = 'none';
	if (jlms_contents_visible == 1) {
		var vis_style2 = 'visible';
		var disp_style2 = '';
		var vis_style1 = 'hidden';
		var disp_style1 = 'none';
	}
	var jlcc = getObj('jlms_lpath_contents_container');
	var jldc = getObj('jlms_lpath_descr');
	jlcc.style.visibility = vis_style1;
	jlcc.style.display = disp_style1;
	jldc.style.visibility = vis_style2;
	jldc.style.display = disp_style2;
	if (jlms_contents_visible == 1) { jlms_contents_visible = 0;}
	else { jlms_contents_visible = 1; }
}
<?php } ?>
function jlms_SwitchContentsOnly2(par) {
	if (par == 'show') {
		mySlide_contents2.slideIn();
	} else {
		mySlide_contents2.hide();
	}
}
var stu_step_id = 0;
var stu_last_cur_id = 0;
var stu_step_type = 0;
var jlms_blocked = 0;
var jlms_is_pending_task = 0;
var jlms_pending_task = '';
var jlms_allow_pending_task = 1;
var seek_step_id = 0;
var get_doc_id = 0;
var lp_url_prefix = '<?php echo $JLMS_CONFIG->get('live_site');?>/index.php?option=<?php echo $option;?>&Itemid=<?php echo $Itemid;?>';
var lp_user_unique_id = '';
var user_start_id = 0;
var mCfg_live_site = '';
function jlms_MakeRequest(onstate, url, mr_type) {
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
	http_request.onreadystatechange = function() { eval(onstate+'(http_request);') };
	var lp_url_prefix2 = '';
	var post_target = '<?php echo $JLMS_CONFIG->get('live_site');?>/index.php?jlms=1';
	if (mr_type == 'lpath') {
		jlms_blocked == 1;
		jlms_showLoading();
		lp_url_prefix2 = 'jlms=1&option=<?php echo $option;?>&Itemid=<?php echo $Itemid;?>&task=show_lpath&user_unique_id=' + lp_user_unique_id +'&user_start_id='+user_start_id+'&id='+jlms_lpath+'&course_id='+jlms_course;
		post_target = mCfg_live_site + lp_url_prefix;
	} else if (mr_type == 'quiz'){
		lp_url_prefix2 = 'user_unique_id=' + user_unique_id + '&lp_user_unique_id=' + lp_user_unique_id +'&user_start_id='+user_start_id+'&lpath_id='+jlms_lpath+'&step_id='+stu_step_id;
		post_target = mCfg_live_site + url_prefix;
	}
	//http_request.open('GET', mCfg_live_site + lp_url_prefix + lp_url_prefix2 + url, true);
	//http_request.send(null);
	http_request.open("POST", post_target, false);
	http_request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	http_request.setRequestHeader("Content-length", lp_url_prefix2.length + url.length);
	//http_request.setRequestHeader("Connection", "close"); - if close - bug in IE7 - it hungs up
	http_request.send(lp_url_prefix2 + url);
	if (mr_type == 'lpath') {
		jlms_allow_pending_task = 0;
	}
}
function jlms_AnalizeRequest(http_request) {
	if (http_request.readyState == 4) {
		if ((http_request.status == 200)) {
			jlms_WStatus('');
			
			if(http_request.responseXML.documentElement == null){
				try {
					//alert(http_request.responseXML.parseError.reason);
					http_request.responseXML.loadXML(http_request.responseText)
				} catch (e) {
					/*alert("Can't load");*/
				}
			}
			response = http_request.responseXML.documentElement;
			var task = jlms_RFE(response,'task');
			jlms_blocked = 1;
			jlms_allow_pending_task = 1;
			setTimeout("jlms_releaseBlock()", 1000);
<?php if (!$show_lpath_contents_at_the_left) { ?>
			jlms_SwitchOpenedContents();
<?php } ?>
			switch (task) {
				case 'start_restart':
				case 'start':
					if ($('joomlalms_sys_message_container')) {
		                //hide course enrollment message (if autoredirect to lpath enabled)
		                $('joomlalms_sys_message_container').setStyles({visibility: 'hidden',display: 'none'});
					}
					lp_user_unique_id = jlms_RFE(response,'user_unique_id');
					user_start_id = jlms_RFE(response,'user_start_id');
					stu_step_type = jlms_RFE(response,'step_type');
					if (stu_step_type == 5) {
						quiz_blocked = 0;
						timer_sec = 0;
						stop_timer = 0;
						quiz_id = jlms_RFE(response,'step_item_id');
					}
					prev_step_type = stu_step_type;
					stu_step_id = jlms_RFE(response,'step_id');
					stu_last_cur_id = stu_step_id;
					prev_step_id = stu_step_id;
					jlms_ChangeFrontPage(response);
					if (task == 'start_restart') {
						$('jlms_lpath_completion_msg_container').setStyles({visibility: 'hidden',display: 'none'});
						jlms_setPendingSteps('cancel',response);
						jlms_setPendingSteps('quiz',response);
					}
					jlms_setPendingSteps('pending',response);
					jlms_setPendingSteps('accept',response);
				break;
				case 'restart':
					$('jlms_lpath_completion_msg_container').setStyles({visibility: 'hidden',display: 'none'});
					jlms_ChangeFrontPage(response);
					getObj('jlms_lpath_contents').innerHTML = jlms_RFE(response,'contents_data');
					jlms_setPendingSteps('pending',response);
				break;
				case 'check_cond':
					lp_user_unique_id = jlms_RFE(response,'user_unique_id');
					user_start_id = jlms_RFE(response,'user_start_id');	
				
					//stu_step_id = jlms_RFE(response,'step_id');
					//prev_step_id = stu_step_id;
					jlms_ChangeFrontPage(response);
					jlms_changePendingSteps();
					//jlms_setPendingSteps('pending',response);
					jlms_setPendingSteps('accept',response);
				break;
				case 'seek_step':
				case 'next_step':
					user_unique_id = '';
					quiz_id = 0;
					stu_step_type = jlms_RFE(response,'step_type');
					if (stu_step_type == 5) {
						quiz_id = jlms_RFE(response,'step_item_id');
					}
					prev_step_type = stu_step_type;
					stu_step_id = jlms_RFE(response,'step_id');
					prev_step_id = stu_step_id;
					jlms_ChangeFrontPage(response);
					jlms_setPendingSteps('pending',response);
					jlms_setPendingSteps('accept',response);
				break;
				case 'finish_lpath_quick':// without break;
					lp_user_unique_id = jlms_RFE(response,'user_unique_id');
					user_start_id = jlms_RFE(response,'user_start_id');
				case 'finish_lpath':
					jlms_ChangeFrontPage(response);

					var is_show_cmsg = jlms_RFE(response,'show_completion_msg');
					if (is_show_cmsg == 1 || is_show_cmsg == '1') {
						var cmsg_txt = jlms_RFE(response,'lpath_completion_msg');
						$('jlms_lpath_completion_msg_container').<?php echo $setHTML; ?>cmsg_txt);
						$('jlms_lpath_completion_msg_container').setStyles({visibility: 'visible',display: ''});
					}

					jlms_setPendingSteps('accept',response);
<?php if (!$show_lpath_contents_at_the_left) { ?>
					jlms_SwitchContentsOnly('show');
<?php } ?>
				break;
				case 'failed':
					getObj('jlms_lpath_descr').innerHTML = '<div class="joomlalms_sys_message"><?php echo str_replace('/', '\/',_JLMS_LPATH_LOAD_DATA_ERROR);?><\/div>';
					//getObj('jlms_lpath_menu').innerHTML = jlms_RFE(response,'menu_contents');
				break;
				default:
					getObj('jlms_lpath_descr').innerHTML = '<div class="joomlalms_sys_message"><?php echo str_replace('/', '\/',_JLMS_LPATH_LOAD_DATA_ERROR);?><\/div>';
					getObj('jlms_lpath_menu').innerHTML = '';
				break;
			}
		} else {
			alert('Bad Request status');
		}
	}
}
function jlms_RFE(response,elem_name) {
	return response.getElementsByTagName(''+elem_name)[0].firstChild ? response.getElementsByTagName(''+elem_name)[0].firstChild.data : 0;
}
var is_collapser_timer = 0;
<?php
echo JLMSCSS::h2_js(); //JLMSCSS_h2_js function
?>
function jlms_ChangeFrontPage(response) {
	var head_data = jlms_RFE(response,'step_name');
	if (head_data != '') {
		getObj('jlms_lpath_head').innerHTML = JLMSCSS_h2_js(head_data);
	}
	var tmp_div = document.createElement("div");
	tmp_div.id = 'temporary_div_tst';
	tmp_div.innerHTML = jlms_RFE(response,'step_descr');
	tmp_div.style.width = '100%';
	getObj('jlms_lpath_descr').innerHTML = '';
	getObj('jlms_lpath_descr').appendChild(tmp_div);

	//getObj('jlms_lpath_descr').innerHTML = jlms_RFE(response,'step_descr');
	getObj('jlms_lpath_menu').innerHTML = jlms_RFE(response,'menu_contents');
	var temp_script = jlms_RFE(response,'step_exec_script');
	if (temp_script == 1 || temp_script == '1') {
		var exec_script = jlms_RFE(response,'step_exec_script_contents');
		var new_script_el = document.createElement("script");
		new_script_el.text = exec_script;
		new_script_el.type="text/javascript";
		document.getElementsByTagName("head")[0].appendChild(new_script_el);
		//eval(exec_script);
	}
<?php if ($show_lpath_contents_at_the_left) { ?>
	jlms_ChangeCollapserHeight(0);
<?php } ?>
	if (window.set_height) {
		set_height();
	}
	jlms_ScrollBrowserWindow();
	setTimeout("jlms_ScrollBrowserWindow()", 300);
}
function jlms_ScrollBrowserWindow() {
	window.scrollTo(0,$('jlms_topdiv').getTop());
}
function jlms_ChangeCollapserHeight(by_timer) {
	var leftnav_h, main_st_h = 0;
	leftnav_h = $('jlms_lpath_contents_container').getStyle('height').toInt();
	main_st_h = $('jlms_lpath_descr').getStyle('height').toInt();
	if (leftnav_h < main_st_h) {
		leftnav_h = main_st_h;
	}
	$('left_nav_collapser_container').setStyle('height', leftnav_h+'px');
	if (is_collapser_timer == 1) {
		if (by_timer == 1) {
			setTimeout("jlms_ChangeCollapserHeight(1)", 300);
		}
	} else {
		is_collapser_timer = 1;
		setTimeout("jlms_ChangeCollapserHeight(1)", 300);
	}
}
function jlms_releaseBlock() {
	jlms_blocked = 0;
	if (jlms_is_pending_task == 1) {
		if (jlms_pending_task != '') {
			jlms_is_pending_task = 0;
			eval("ajax_action('"+jlms_pending_task+"')");
			jlms_pending_task = '';
		}
	}
}
function jlms_showLoading() {
	jlms_SwitchContentsOnly('hide');
	getObj('jlms_lpath_descr').innerHTML = '<br \/><br \/><center><img src="<?php echo str_replace('/','\/',$JLMS_CONFIG->get('live_site'));?>\/components\/com_joomla_lms\/lms_images\/loading.gif" height="32" width="32" border="0" alt="loading" \/><\/center>';
}
function jlms_setPendingSteps(step_type, response) {
	var st = 'pending_steps';
	var prfx = 'jlms_step_';
	var is_lp = true;
	switch (step_type) {
		case 'pending': st = 'pending_steps'; break;
		case 'accept': st = 'completed_steps'; break;
		case 'cancel': st = 'incompleted_steps'; break;
		case 'quiz': st = 'incompleted_quests'; prfx = 'quest_result_'; is_lp = false; break;
	}
	if (is_lp) {
		var steps_ids;
		steps_ids = jlms_RFE(response,st);
		var arr = steps_ids.split(',');
		var i = 0;
		while (i < arr.length ) {
			if (getObj(prfx+arr[i])) {
				getObj(prfx+arr[i]).innerHTML = '<img class=\'JLMS_png\' src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_'+step_type+'.png" height="16" width="16" border="0" alt="'+step_type+'" />';
			}
			i ++;
		}
		if (step_type == 'pending') {
			var r = getObj(prfx+stu_step_id);
			if (r) {r.innerHTML = '<img class=\'JLMS_png\' src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_'+step_type+'_cur.png" height="16" width="16" border="0" alt="'+step_type+'" />';}
			if (stu_last_cur_id != stu_step_id) {
				r = getObj(prfx+stu_last_cur_id);
				if (r) {r.innerHTML = '<img class=\'JLMS_png\' src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_'+step_type+'.png" height="16" width="16" border="0" alt="'+step_type+'" />';}
			}
			stu_last_cur_id = stu_step_id;
		}
	} else {
		var steps_ids;
		steps_ids = jlms_RFE(response,st);
		var arr = steps_ids.split(',');
		var i = 0;
		while (i < arr.length ) {
			if (getObj(prfx+arr[i])) {
				getObj(prfx+arr[i]).innerHTML = '-';
			}
			i ++;
		}
	}
}
function jlms_changePendingSteps() {
	r = getObj('jlms_step_'+stu_last_cur_id);
	if (r) {r.innerHTML = '<img class=\'JLMS_png\' src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_pending.png" height="16" width="16" border="0" alt="pending" />';}
}
JLMS_preloadImages('<?php echo $JLMS_CONFIG->getCfg('live_site');?>/components/com_joomla_lms/lms_images/loading.gif','<?php echo $JLMS_CONFIG->getCfg('live_site');?>/components/com_joomla_lms/lms_images/buttons/btn_back.png','<?php echo $JLMS_CONFIG->getCfg('live_site');?>/components/com_joomla_lms/lms_images/buttons/btn_restart.png', '<?php echo $JLMS_CONFIG->getCfg('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_pending.png', '<?php echo $JLMS_CONFIG->getCfg('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_accept.png');
//--><!]]>
</script>
<?php
		JLMS_TMPL::OpenMT();

		$hparams = array();
		//$toolbar = array();
		//$toolbar[] = array('btn_type' => 'start', 'btn_js' => "javascript:ajax_action('start_lpath');");
		JLMS_TMPL::ShowHeader('lpath', '', $hparams);
		//JLMS_TMPL::ShowToolbar($toolbar, 'right', true, $lpath_data->lpath_name, 2);

		//JLMS_TMPL::CloseMT();
		JLMS_TMPL::OpenTS();
?>
		<table width="100%" cellpadding="0" cellspacing="0" border="0" class="jlms_table_no_borders">
			<tr>
				<td align="left" valign="middle" id="jlms_lpath_head" width="100%">
					<?php echo JLMSCSS::h2($lpath_data->lpath_name); ?>
				</td>
				<td align="right" style="text-align:right " valign="middle" id="jlms_lpath_menu">
					<?php $toolbar = array();
					$toolbar[] = array('btn_type' => 'start', 'btn_js' => "javascript:void(0);");
					//$toolbar[] = array('btn_type' => 'start', 'btn_js' => sefrelToAbs("index.php?option=$option&amp;Itemid=$Itemid&amp;task=show_lpath_nojs&amp;course_id=$course_id&amp;id=$lpath_id&amp;action=start_lpath"));
					//no-js functionality commented (version 1.1.0) due to the lots of bugs, lack of usage/testing 
					echo JLMS_ShowToolbar($toolbar); ?>
				</td>
			</tr>
		</table>
<?php JLMS_TMPL::CloseTS();JLMS_TMPL::OpenTS();?>
<?php
			$toolbar = array();
			$toolbar[] = array('btn_type' => 'start', 'btn_js' => "javascript:ajax_action('start_lpath');");
			$rs = JLMS_ShowToolbar($toolbar);
			$rs = str_replace('"components/com_joomla_lms','"'.$JLMS_CONFIG->getCfg('live_site').'/components/com_joomla_lms',$rs);
			$lpc_btn = str_replace('/','\/',str_replace('"', "\\\"", $rs));

	$additon_js = '
var lp_menu_item_contents_pre = "'.$lpc_btn.'";
';
$JLMS_CONFIG->set('jlms_aditional_js_code', $JLMS_CONFIG->get('jlms_aditional_js_code','').$additon_js);
$domready = '
$(\'jlms_lpath_menu\').innerHTML = lp_menu_item_contents_pre;

if (document.constructor) {
	document.constructor.prototype.write = function() { };
} else {
	document.write = function() { };
}
';
$JLMS_CONFIG->set('web20_domready_code', $JLMS_CONFIG->get('web20_domready_code','').$domready);

if ($show_lpath_contents_at_the_left) {
?>
		<div id="jlms_lpath_contents_container" style="visibility:hidden; display:none; width:203px; float:left; overflow-x:hidden; margin-right: -1px">
		<?php global $JLMS_CONFIG;
		$JLMS_CONFIG->set('show_lpath_contents_at_the_left', $show_lpath_contents_at_the_left); ?>

			<?php JLMS_course_lpathstu_html::showLPath_contents($lpath_contents, $quizzes_data); ?><br />
		</div>
		<div id="left_nav_collapser_container" style="width:7px; float:left; overflow-x:hidden; visibility:hidden; display:none ">
			<a id="left_nav_collapser" href="javascript:jlms_SwitchContents2();"><img class="collapse_button_maximized_xxx" border="1" width="1" height="1" src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/spacer.png"/></a>
		</div>
		<div id="jlms_lpath_completion_msg_container" class="jlms_lpath_completion_message" style="visibility:hidden; display:none; margin-left:210px; width:auto;">
			<!--x-->
		</div>
		<div id="jlms_lpath_descr" style="margin-left:210px; width:auto;">
			<?php $text = JLMS_ShowText_WithFeatures($lpath_data->lpath_description);
				echo $text; ?>
		</div>
		<br />
<?php
} else {
?>
		<div id="jlms_lpath_completion_msg_container" class="jlms_lpath_completion_message" style="visibility:hidden; display:none">
			<!--x-->
		</div>
		<div id="jlms_lpath_contents_container" style="visibility:hidden; display:none; width:100%">
			<?php JLMS_course_lpathstu_html::showLPath_contents($lpath_contents, $quizzes_data); ?><br />
		</div>
		<div id="jlms_lpath_descr" style="width:100%">
			<?php $text = JLMS_ShowText_WithFeatures($lpath_data->lpath_description);
				echo $text; ?>
		</div>
<?php } ?>
<?php JLMS_TMPL::CloseTS();JLMS_TMPL::CloseMT();?>
<?php
	}
	function showLPath_contents(&$lpath_contents, &$quizzes_data, $nojs = false, $user_unique_id = 0, $user_start_id = 0, $user_unique_id_quiz=0) {
		global $Itemid, $JLMS_CONFIG;
?>
<script language="javascript" type="text/javascript">
<!--
var TreeArray1 = new Array();
var TreeArray2 = new Array();
var Is_ex_Array = new Array();
<?php
$i = 1;
foreach ($lpath_contents as $lpath_row) {
	echo "TreeArray1[".$i."] = ".$lpath_row->parent_id.";";
	echo "TreeArray2[".$i."] = ".$lpath_row->id.";";
	echo "Is_ex_Array[".$i."] = 1;" ."\n";
	$i ++;
}
?>
function Hide_Folder(fid) {
	var vis_style = 'hidden';var dis_style = 'none';var i = 1;
	while (i < TreeArray1.length) {
		if (TreeArray1[i] == fid) {
			getObj('tree_row_'+TreeArray2[i]).style.visibility = vis_style;
			getObj('tree_row_'+TreeArray2[i]).style.display = dis_style;
			Hide_Folder(TreeArray2[i]); }
		i++; }
}
function Show_Folder(fid) {
	var vis_style = 'visible';var dis_style = '';var i = 1;
	while (i < TreeArray1.length) {
		if (TreeArray1[i] == fid) {
			getObj('tree_row_'+TreeArray2[i]).style.visibility = vis_style;
			getObj('tree_row_'+TreeArray2[i]).style.display = dis_style;
			NoChange_Folder(TreeArray2[i]); }
		i++; }
}
function NoChange_Folder(fid) {
	var vis_style = 'hidden';var dis_style = 'none';var i = 1;var j = 0;
	while (i < TreeArray2.length) {
		if ( (TreeArray2[i] == fid) && (Is_ex_Array[i] == 1) ) {
			vis_style = 'visible';dis_style = '';j = 1; }
		i++; }
	i = 1;
	while (i < TreeArray1.length) {
		if (TreeArray1[i] == fid) {
			getObj('tree_row_'+TreeArray2[i]).style.visibility = vis_style;
			getObj('tree_row_'+TreeArray2[i]).style.display = dis_style;
			if (j == 1) { NoChange_Folder(TreeArray2[i]);
			} else { Hide_Folder(TreeArray2[i]); } }
		i++; }
}

function Ex_Folder(fid) {
	var i = 1;var j = 1;
	while (i < TreeArray2.length) {
		if ( (TreeArray2[i] == fid) && (Is_ex_Array[i] == 1) ) { j = 0; } i ++; }
	if (j == 1) {
		Show_Folder(fid);
		if (getObj('tree_img_' + fid).runtimeStyle) {
			var StStr = getObj('tree_img_' + fid).runtimeStyle.filter;
			var imgStr = getObj('tree_img_' + fid).outerHTML;
			imgStr = imgStr.replace('chapter_expand.png','chapter_collapse.png').replace('<?php echo _JLMS_LPATH_EXP_CHAP;?>', '<?php echo _JLMS_LPATH_COLL_CHAP;?>');
			StStr = StStr.replace('chapter_expand.png','chapter_collapse.png');
			getObj('tree_img_' + fid).outerHTML = imgStr;
			getObj('tree_img_' + fid).runtimeStyle.filter = StStr;
		} else {
			getObj('tree_img_' + fid).src = '<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/learnpath/chapter_collapse.png';
			getObj('tree_img_' + fid).alt = '<?php echo _JLMS_LPATH_COLLAPSE;?>';
			getObj('tree_img_' + fid).title = '<?php echo _JLMS_LPATH_COLL_CHAP;?>';
		}
	} else {
		Hide_Folder(fid);
		if (getObj('tree_img_' + fid).runtimeStyle) {
			var StStr = getObj('tree_img_' + fid).runtimeStyle.filter;
			var imgStr = getObj('tree_img_' + fid).outerHTML;
			imgStr = imgStr.replace('chapter_collapse.png','chapter_expand.png').replace('<?php echo _JLMS_LPATH_COLL_CHAP;?>', '<?php echo _JLMS_LPATH_EXP_CHAP;?>');
			StStr = StStr.replace('chapter_collapse.png','chapter_expand.png');
			getObj('tree_img_' + fid).outerHTML = imgStr;
			getObj('tree_img_' + fid).runtimeStyle.filter = StStr;
		} else {
			getObj('tree_img_' + fid).src = '<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/learnpath/chapter_expand.png';
			getObj('tree_img_' + fid).alt = '<?php echo _JLMS_LPATH_EXPAND;?>';
			getObj('tree_img_' + fid).title = '<?php echo _JLMS_LPATH_EXP_CHAP;?>';
		}
	}
	i = 1;
	while (i < TreeArray2.length) {
		if ( (TreeArray2[i] == fid) ) {
			if (Is_ex_Array[i] == 1) { Is_ex_Array[i] = 0; } else { Is_ex_Array[i] = 1; } }
		i++; }
		mySlide_contents.show();
}
JLMS_preloadImages('<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/learnpath/chapter_expand.png','<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/learnpath/chapter_collapse.png');
//-->
</script>
<?php
		$max_tree_width = 0; if (isset($lpath_contents[0])) {$max_tree_width = $lpath_contents[0]->tree_max_width;}
		if (!empty($lpath_contents)) {
			echo '<div><table width="100%" cellpadding="0" cellspacing="0" border="0" class="'.JLMSCSS::_('jlmslist').'">';
		}
		$k = 1;
		$tree_modes = array();
		for ($i=0, $n=count($lpath_contents); $i < $n; $i++) {
			$row_path = $lpath_contents[$i];
			$max_tree_width = $row_path->tree_max_width;?>
			<tr id="tree_row_<?php echo $row_path->id;?>" class="<?php echo JLMSCSS::_("sectiontableentry$k"); ?>">
				<td align="center" valign="middle" width="20"><?php echo ( $i + 1 ); ?></td>
				<td valign="middle" width="20" id="jlms_step_<?php echo $row_path->id;?>">
				<?php if (isset($row_path->nojs_contents_status) && $row_path->nojs_contents_status == 3) { ?>
					<img class='JLMS_png' src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_pending_cur.png" height="16" width="16" border="0" alt="current" />
				<?php } elseif (isset($row_path->nojs_contents_status) && $row_path->nojs_contents_status == 2) { ?>
					<img class='JLMS_png' src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_pending.png" height="16" width="16" border="0" alt="viewed" />
				<?php } elseif (isset($row_path->nojs_contents_status) && $row_path->nojs_contents_status == 1) { ?>
					<img class='JLMS_png' src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_accept.png" height="16" width="16" border="0" alt="completed" />
				<?php } else { ?>
					<img class='JLMS_png' src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_cancel.png" height="16" width="16" border="0" alt="incomplete" />
				<?php } ?>
				</td>
				<?php $add_img = '';
				if ($row_path->tree_mode_num) {
					$g = 0;
					$tree_modes[$row_path->tree_mode_num - 1] = $row_path->tree_mode;
					while ($g < ($row_path->tree_mode_num - 1)) {
						$pref = '';
						if (isset($tree_modes[$g]) && ($tree_modes[$g] == 2) ) { $pref = 'empty_'; }
						$add_img .= "<td width='16' valign='middle'><img src=\"".$JLMS_CONFIG->get('live_site')."/components/com_joomla_lms/lms_images/treeview/".$pref."line.png\" width='16' height='16' alt='line' border='0' /></td>";
						$g ++;
					}
					$add_img .= "<td width='16' valign='middle'><img src=\"".$JLMS_CONFIG->get('live_site')."/components/com_joomla_lms/lms_images/treeview/sub".$row_path->tree_mode.".png\" width='16' height='16' border='0' alt='sub' /></td>";
					$max_tree_width = $max_tree_width - $g - 1;
				}
				echo $add_img; ?>
				<td valign="middle" align="center" width="16"><div align="center" style="vertical-align:middle;"><?php switch($row_path->step_type) {
					case 1:
							echo "<span id='tree_div_".$row_path->id."' style='text-align:center; cursor:pointer; vertical-align:middle;' onclick='Ex_Folder(".$row_path->id.",".$row_path->id.",true)'><img id='tree_img_".$row_path->id."' class='JLMS_png' src=\"".$JLMS_CONFIG->get('live_site')."/components/com_joomla_lms/lms_images/learnpath/chapter_collapse.png\" width='16' height='16' border='0' alt='collapse' /></span>";
						break;
					case 2:
						if (isset($row_path->folder_flag) && $row_path->folder_flag == 2) {
							echo "<span style='text-align:center;'><img class='JLMS_png' src=\"".$JLMS_CONFIG->get('live_site')."/components/com_joomla_lms/lms_images/files/file_zippack.png\" width='16' height='16' border='0' alt='zip' /></span>";
						} else {
							echo "<span style='text-align:center;'><img class='JLMS_png' src=\"".$JLMS_CONFIG->get('live_site')."/components/com_joomla_lms/lms_images/files/".$row_path->file_icon.".png\" width='16' height='16' border='0' alt='$row_path->file_icon' /></span>";
						}
					break;
					case 3:	echo "<span style='text-align:center;'><img class='JLMS_png' src=\"".$JLMS_CONFIG->get('live_site')."/components/com_joomla_lms/lms_images/files/link_world.png\" width='16' height='16' border='0' alt='link' /></span>";break;
					case 4:	echo "<span style='text-align:center;'><img class='JLMS_png' src=\"".$JLMS_CONFIG->get('live_site')."/components/com_joomla_lms/lms_images/files/file_content.png\" width='16' height='16' border='0' alt='content' /></span>";break;
					case 5:	echo "<span style='text-align:center;'><img class='JLMS_png' src=\"".$JLMS_CONFIG->get('live_site')."/components/com_joomla_lms/lms_images/toolbar/tlb_quiz.png\" width='16' height='16' border='0' alt='quiz' /></span>";break;
					case 6:	echo "<span style='text-align:center;'><img class='JLMS_png' src=\"".$JLMS_CONFIG->get('live_site')."/components/com_joomla_lms/lms_images/toolbar/tlb_scorm.png\" width='16' height='16' border='0' alt='scorm' /></span>";break;
					} ?>
				</div>
				</td>
				<td width="100%" align="left" valign="middle" <?php if ($max_tree_width >= 0) { echo "colspan='".($max_tree_width + 1 )."'";}?>>
					<?php
						if ($nojs) {
							$link_step = sefRelToAbs("index.php?option=com_joomla_lms&Itemid=".$Itemid."&task=show_lpath_nojs&course_id=".$row_path->course_id."&step_id=".$row_path->id."&id=".$row_path->lpath_id."&action=seek_lpathstep&user_start_id=$user_start_id&user_unique_id=$user_unique_id");
						} else {
							$link_step = "javascript:seek_step_id=".$row_path->id.";ajax_action('lpath_seek');";//sefRelToAbs("index.php?option=".$option."&Itemid=".$Itemid."&task=lpath_seek&id=".$row_path->id);
						}
					 if ($row_path->step_type == 1) {
						echo "<strong>".'<a href="'.$link_step.'">'.$row_path->doc_name."</a></strong>";
					} else {
						echo '<a href="'.$link_step.'">'.$row_path->doc_name."</a>";
					} ?>
				</td>
			</tr>
			<?php 
			if ($row_path->step_type == 5) {
				$ar_element = 'quiz_'.$row_path->item_id;
				$i_quiz = 0;
				$n_quiz = isset($quizzes_data[$ar_element]->panel_data) ? count($quizzes_data[$ar_element]->panel_data) : 0;
				if (isset($quizzes_data[$ar_element]->panel_data) && isset($quizzes_data[$ar_element]->c_slide) && $quizzes_data[$ar_element]->c_slide) {
					foreach ($quizzes_data[$ar_element]->panel_data as $qpd) { ?>
						<tr id="tree_row_quiz<?php echo $qpd->c_id;?>" class="<?php echo "sectiontableentry$k"; ?>">
							<td align="center" valign="middle" width="20">&nbsp;</td>
							<td valign="middle" width="20" >&nbsp;</td>
							<?php $add_img = '';
							if ($row_path->tree_mode_num) {
								$g = 0;
								$tree_modes[$row_path->tree_mode_num - 1] = $row_path->tree_mode;
								while ($g < ($row_path->tree_mode_num - 1)) {
									$pref = '';
									if (isset($tree_modes[$g]) && ($tree_modes[$g] == 2) ) { $pref = 'empty_'; }
									$add_img .= "<td width='16' valign='middle'><img src=\"".$JLMS_CONFIG->get('live_site')."/components/com_joomla_lms/lms_images/treeview/".$pref."line.png\" width='16' height='16' alt='line' border='0' /></td>";
									$g ++;
								}
								$pref = '';
								if ($i == ($n - 1)) {
									$pref = 'empty_';
								}
								$add_img .= "<td width='16' valign='middle'><img src=\"".$JLMS_CONFIG->get('live_site')."/components/com_joomla_lms/lms_images/treeview/".$pref."line.png\" width='16' height='16' border='0' alt='sub' /></td>";
							}
							echo $add_img; ?>
							<td valign="middle" align="center" width="16"><div align="center" style="vertical-align:middle;">
							<?php $suff = 1;
								if ($i_quiz == ($n_quiz - 1)) { $suff = 2; }
								echo "<span style='text-align:center;'><img src=\"".$JLMS_CONFIG->get('live_site')."/components/com_joomla_lms/lms_images/treeview/sub".$suff.".png\" width='16' height='16' border='0' alt='sub' /></span>";
								?>
							</div>
							</td>
							<td width="100%" align="left" valign="middle" <?php if ($max_tree_width >= 0) { echo "colspan='".($max_tree_width + 1)."'";} ?>>
							<?php
								if ($nojs) {
									$link_step = sefRelToAbs("index.php?option=com_joomla_lms&Itemid=".$Itemid."&task=quiz_action&id=".$row_path->course_id."&step_id=".$row_path->id."&quiz=".$row_path->item_id."&atask=goto_quest&user_start_id=$user_start_id&user_unique_id=$user_unique_id_quiz&stu_quiz_id=".$quizzes_data[$ar_element]->stu_quiz_id."&seek_quest_id=".$qpd->c_id."");
								} else {
									$link_step = "javascript:jlms_gotoQuestion(".$qpd->c_id.");";//sefRelToAbs("index.php?option=".$option."&Itemid=".$Itemid."&task=lpath_seek&id=".$row_path->id);
								}
								if (isset($row_path->nojs_contents_status) && $row_path->nojs_contents_status == 3 && $user_unique_id_quiz ){
									$link_nojs_contents_status = '<a href="'.$link_step.'">'.substr(strip_tags($qpd->c_question),0,50).'</a>';	
								} else {
									$link_nojs_contents_status = substr(strip_tags($qpd->c_question),0,50);
								}
								echo '<div style="float:left">'.$link_nojs_contents_status."</div><div style='float:right; width:40px'>".$qpd->c_point."</div>";
								$quest_result_container = "-";
								if ($qpd->stu_quest) {
									if ( $qpd->stu_score && ($qpd->stu_score == $qpd->c_point) ) {
										$quest_result_container = "<img class='JLMS_png' src=\"".$JLMS_CONFIG->get('live_site')."/components/com_joomla_lms/lms_images/toolbar/btn_accept.png\" height=\"16\" width=\"16\" border=\"0\" alt=\"accept\" />";
									} else {
										$quest_result_container = "<img class='JLMS_png' src=\"".$JLMS_CONFIG->get('live_site')."/components/com_joomla_lms/lms_images/toolbar/btn_cancel.png\" height=\"16\" width=\"16\" border=\"0\" alt=\"cancel\" />";
									}
								}
								echo "<div style='float:right; width:25px' id='quest_result_".$qpd->c_id."'>".$quest_result_container."</div>";
							?>
							</td>
						</tr>
						<?php
						$i_quiz ++;
						$k = 3 - $k;
					}
				}
			}
			$k = 3 - $k;
		}
		if (!empty($lpath_contents)) {
			echo '</table></div>';
		}
	}
	
	function show_NoJs_Page($lpath_name, $lpath_contents, $i, $mark_complete, $user_unique_id, $user_start_id, $user_unique_id_quiz=0, $quiz_id=0, $quest_id=0, $stu_quiz_id=0, $mark_pending = array(), $completion_msg = '', $is_finish = false) {
		global $Itemid, $JLMS_CONFIG;

		JLMS_TMPL::OpenMT();
		$hparams = array();
		JLMS_TMPL::ShowHeader('lpath', $lpath_name, $hparams);
		JLMS_TMPL::OpenTS();
?>
		<table width="100%" cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td class="contentheading" align="left" valign="middle" id="jlms_lpath_head" width="100%">
<?php
					if ($completion_msg === '***JLMS***SHOW***CONTENTS***') {
						echo _JLMS_CONTENTS_ALT_TITLE;
					} else {
						echo $lpath_contents[$i]->step_name;
					} ?>
				</td>
				<td align="right" style="text-align:right " valign="middle" id="jlms_lpath_menu">
					<form name="lpath_MForm" action="<?php echo sefRelToAbs("index.php?option=com_joomla_lms&Itemid=$Itemid");?>" method="post" >
							<table cellspacing="0" cellpadding="0" border="0" align="right" style="text-align: right;"><tr>
							<?php if ($completion_msg === '***JLMS***SHOW***CONTENTS***') { ?>
								<?php /*<td><input type="image" name="action" value="seek_lpathstep" src="components/com_joomla_lms/lms_images/buttons/btn_back.png" alt="<?php echo _JLMS_BACK_ALT_TITLE;?>" /></td><td>&nbsp;<?php echo _JLMS_BACK_ALT_TITLE;?>&nbsp;</td>*/ ?>
								<td><input type="submit" name="action" value="seek_lpathstep" style="background:transparent url(<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/buttons/btn_back.png) no-repeat scroll left top; display:block; cursor: pointer; border: 0 none; padding: 0px; font-size: 0; width: 32px; height:32px" alt="<?php echo _JLMS_BACK_ALT_TITLE;?>" /></td><td>&nbsp;<?php echo _JLMS_BACK_ALT_TITLE;?>&nbsp;</td>
							<?php } elseif ($is_finish) { ?>
								<?php /*<td><input type="image" name="action" value="restart_lpath" src="components/com_joomla_lms/lms_images/buttons/btn_restart.png" alt="<?php echo _JLMS_RESTART_ALT_TITLE;?>" /></td><td>&nbsp;<?php echo _JLMS_RESTART_ALT_TITLE;?>&nbsp;</td>*/ ?>
								<td><input type="submit" name="action" value="restart_lpath" style="background:transparent url(<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/buttons/btn_restart.png) no-repeat scroll left top; display:block; cursor: pointer; border: 0 none; padding: 0px; font-size: 0; width: 32px; height:32px" alt="<?php echo _JLMS_RESTART_ALT_TITLE;?>" /></td><td>&nbsp;<?php echo _JLMS_RESTART_ALT_TITLE;?>&nbsp;</td>
							<?php } else {
								if ($i > 0) { ?>
									<?php /*<td><input type="image" name="action" value="prev_lpathstep" src="components/com_joomla_lms/lms_images/buttons/btn_back.png" alt="<?php echo _JLMS_PREV_ALT_TITLE;?>" /></td><td>&nbsp;<?php echo _JLMS_PREV_ALT_TITLE;?>&nbsp;</td>*/ ?>
									<td><input type="submit" name="action" value="prev_lpathstep" style="background:transparent url(<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/buttons/btn_back.png) no-repeat scroll left top; display:block; cursor: pointer; border: 0 none; padding: 0px; font-size: 0; width: 32px; height:32px" alt="<?php echo _JLMS_PREV_ALT_TITLE;?>" /></td><td>&nbsp;<?php echo _JLMS_PREV_ALT_TITLE;?>&nbsp;</td>
								<?php } ?>
								<td><input type="submit" name="action" value="next_lpathstep" style="background:transparent url(<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/buttons/btn_start.png) no-repeat scroll left top; display:block; cursor: pointer; border: 0 none; padding: 0px; font-size: 0; width: 32px; height:32px;" alt="<?php echo _JLMS_NEXT_ALT_TITLE;?>" /></td><td>&nbsp;<?php echo _JLMS_NEXT_ALT_TITLE;?>&nbsp;</td>
								<td><input type="submit" name="action" value="contents_lpath" style="background:transparent url(<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/buttons/btn_contents.png) no-repeat scroll left top; display:block; cursor: pointer; border: 0 none; padding: 0px; font-size: 0; width: 32px; height:32px;" alt="<?php echo _JLMS_CONTENTS_ALT_TITLE;?>" /></td><td>&nbsp;<?php echo _JLMS_CONTENTS_ALT_TITLE;?>&nbsp;</td>
							<?php } ?>
							</tr></table>
						<input type="hidden" name="option" value="com_joomla_lms" />
						<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
						<?php
						if($quest_id && $quiz_id && $stu_quiz_id && $user_unique_id_quiz){
						?>
						<input type="hidden" name="task" value="quiz_action" />
						<input type="hidden" name="atask" value="goto_quest" />
						<input type="hidden" name="seek_quest_id" value="<?php echo $quest_id;?>" />
						<input type="hidden" name="id" value="<?php echo $lpath_contents[$i]->course_id; ?>" />
						<input type="hidden" name="quiz" value="<?php echo $quiz_id; ?>" />
						<input type="hidden" name="stu_quiz_id" value="<?php echo $stu_quiz_id; ?>" />
						<input type="hidden" name="user_unique_id" value="<?php echo $user_unique_id_quiz; ?>" />
						<?php
						} else {
						?>
						<input type="hidden" name="task" value="show_lpath_nojs" />
						<input type="hidden" name="course_id" value="<?php echo $lpath_contents[$i]->course_id; ?>" />
						<input type="hidden" name="id" value="<?php echo $lpath_contents[$i]->lpath_id; ?>" />
						<input type="hidden" name="user_unique_id" value="<?php echo $user_unique_id; ?>" />
						<?php
						}
						?>
						<input type="hidden" name="step_id" value="<?php echo $lpath_contents[$i]->id; ?>" />
						<input type="hidden" name="user_start_id" value="<?php echo $user_start_id; ?>" />
						
					</form>
				</td>
			</tr>
		</table>
<?php JLMS_TMPL::CloseTS();JLMS_TMPL::OpenTS();
		if ($completion_msg && $completion_msg !== '***JLMS***SHOW***CONTENTS***') { ?>
		<div id="jlms_lpath_completion_msg_container" class="jlms_lpath_completion_message">
			<?php echo $completion_msg; ?>
		</div>
		<?php } ?>
		<div id="jlms_lpath_descr" style="width:100%">
<?php
			if ($is_finish) {
				$completion_msg = '***JLMS***SHOW***CONTENTS***';
			}
			if ($completion_msg === '***JLMS***SHOW***CONTENTS***') {
				$quizzes_data = array();


			$course_id = $lpath_contents[$i]->course_id;
			$lpath_id = $lpath_contents[$i]->lpath_id;
			global $my, $JLMS_DB;

			$count_quizzes = 0;
			$quiz_ids = array();
			$quiz_steps = array();
			$quizzes_data = array();
			foreach ($lpath_contents as $lc) {
				if ($lc->step_type == 5) {
					$count_quizzes ++;
					$quiz_ids[] = $lc->item_id;
					$quiz_steps[] = $lc->id;
				}
			}
			if ($count_quizzes) {
				$qqi = 0;
				foreach ($quiz_ids as $qi) {
					$fquiz = new stdClass();
					$fquiz->c_id = $qi;
					$query = "SELECT c_slide FROM #__lms_quiz_t_quiz WHERE c_id = '".$qi."'";
					$JLMS_DB->setQuery($query);
					$quiz_obj = $JLMS_DB->loadObject();
					$fquiz->c_slide = $quiz_obj->c_slide;
					$query = "SELECT a.id as result_id, b.stu_quiz_id FROM #__lms_learn_path_results as a, #__lms_learn_path_step_quiz_results as b WHERE a.user_id = '".$my->id."'"
					. "\n AND a.course_id = '".$course_id."' AND a.lpath_id = '".$lpath_id."' AND b.result_id = a.id AND b.step_id = ".$quiz_steps[$qqi];
					$JLMS_DB->SetQuery( $query );
					$stu_quiz_id = 0;
					$res_id = 0;
					$step_status = 0;
					$trtrtrt = $JLMS_DB->LoadObject();
					if (is_object($trtrtrt)) {
						$stu_quiz_id = $trtrtrt->stu_quiz_id;
						$res_id = $trtrtrt->result_id;
						if ($res_id) {
							$query = "SELECT step_status FROM #__lms_learn_path_step_results WHERE result_id = $res_id AND step_id = $quiz_steps[$qqi]";
							$JLMS_DB->SetQuery( $query );
							$rrtt = $JLMS_DB->loadResult();
							if ($rrtt) {
								$step_status = $rrtt;
							}
						}
					}

					// 27 April 2007 (DEN) - Question Pool support
					if ($stu_quiz_id && $step_status) {
						$query = "SELECT a.*, b.c_id as stu_quest, b.c_score as stu_score FROM #__lms_quiz_r_student_quiz_pool as qp, #__lms_quiz_t_question as a"
						. "\n LEFT JOIN #__lms_quiz_r_student_question as b ON b.c_stu_quiz_id = $stu_quiz_id AND b.c_question_id = a.c_id"
						. "\n WHERE qp.start_id = $stu_quiz_id AND qp.quest_id = a.c_id AND a.course_id = ".$course_id." ORDER BY qp.ordering";// (a.c_quiz_id = ".$this->quiz_id." OR (a.c_quiz_id = 0 AND )) ORDER BY b.ordering";
						$JLMS_DB->SetQuery( $query );
						$fquiz->panel_data = $JLMS_DB->LoadObjectList();
					} else {
						// old way
						/*$query = "SELECT a.*, b.c_id as stu_quest, b.c_score as stu_score FROM #__lms_quiz_t_question as a"
						. "\n LEFT JOIN #__lms_quiz_r_student_question as b ON b.c_stu_quiz_id = $stu_quiz_id AND b.c_question_id = a.c_id"
						. "\n WHERE a.c_quiz_id = '".$qi."' ORDER BY ordering, c_id";*/
						$fquiz->panel_data = array();
					}

					$fquiz->stu_quiz_id = $stu_quiz_id;

					$q_from_pool = array();
					foreach ($fquiz->panel_data as $row) {
						if ($row->c_type == 20) {
							$q_from_pool[] = $row->c_pool;
						}
					}
					if (count($q_from_pool)) {
						$qp_ids =implode(',',$q_from_pool);
						$query = "SELECT a.* FROM #__lms_quiz_t_question as a"
						. "\n WHERE a.course_id = ".$course_id;
						$JLMS_DB->setQuery( $query );
						$rows2 = $JLMS_DB->loadObjectList();
						for ($iii=0, $n=count( $fquiz->panel_data ); $iii < $n; $iii++) {
							if ($fquiz->panel_data[$iii]->c_type == 20) {
								for ($j=0, $m=count( $rows2 ); $j < $m; $j++) {
									if ($fquiz->panel_data[$iii]->c_pool == $rows2[$j]->c_id) {
										$fquiz->panel_data[$iii]->c_question = $rows2[$j]->c_question;
										$fquiz->panel_data[$iii]->c_point = $rows2[$j]->c_point;
										$fquiz->panel_data[$iii]->c_attempts = $rows2[$j]->c_attempts;
										$fquiz->panel_data[$iii]->c_type = $rows2[$j]->c_type;
										break;
									}
								}
							}
						}
					}

					$ar_element = 'quiz_'.$qi;
					$quizzes_data[$ar_element] = $fquiz;
					$qqi ++;
				}

			}


				$ii = 0;
				while ($ii < count($lpath_contents)) {
					$lpath_contents[$ii]->nojs_contents_status = 0;
					if ($lpath_contents[$ii]->id == $lpath_contents[$i]->id && !$is_finish) {
						$lpath_contents[$ii]->nojs_contents_status = 3;
					} elseif (in_array($lpath_contents[$ii]->id, $mark_complete)) {
						$lpath_contents[$ii]->nojs_contents_status = 1;
					} elseif (in_array($lpath_contents[$ii]->id, $mark_pending)) {
						$lpath_contents[$ii]->nojs_contents_status = 2;
					}
					$ii ++;
				}

//				echo '<pre>';
//				print_r($quizzes_data);
//				echo '</pre>';

							
				JLMS_course_lpathstu_html::showLPath_contents($lpath_contents, $quizzes_data, true, $user_unique_id, $user_start_id, $user_unique_id_quiz);
				echo $is_finish;
			} else {
				$elem_info = JLMS_prepareLPath_Elem_DATA($lpath_contents[$i], $user_unique_id, $user_start_id);
				echo $elem_info['step_descr'];	
			}
?>
		</div>
<?php JLMS_TMPL::CloseTS();JLMS_TMPL::CloseMT();
	}

	function show_NoJs_Page2( &$element, $lpath_name, &$lpath_contents, $i) {
		global $Itemid, $JLMS_CONFIG;
		
//		echo '<pre>';
//		var_dump($element);
//		echo '</pre>';


		JLMS_TMPL::OpenMT();
		$hparams = array();
		JLMS_TMPL::ShowHeader('lpath', $lpath_name, $hparams);
		JLMS_TMPL::OpenTS();

		$is_finish = isset($element['task']->value) && ($element['task']->value == 'finish_lpath_quick' || $element['task']->value == 'finish_lpath');
		$element_task = isset($element['task']->value) ? $element['task']->value : '' ;
?>
		<table width="100%" cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td class="contentheading" align="left" valign="middle" id="jlms_lpath_head" width="100%">
<?php
					if ($is_finish) {
						echo _JLMS_CONTENTS_ALT_TITLE;
					} else {
						echo $lpath_contents[$i]->step_name;
					} ?>
				</td>
				<td align="right" style="text-align:right " valign="middle" id="jlms_lpath_menu">
				<?php if (isset($element['step_type']->value) && $element['step_type']->value == 5 && isset($element['end_quiz']->value) && $element['end_quiz']->value == 0 ) { /* !!!!! */ } else { ?>
					<form name="lpath_MForm" action="<?php echo sefRelToAbs("index.php?option=com_joomla_lms&Itemid=$Itemid");?>" method="post" >
				<?php } ?>
							<table cellspacing="0" cellpadding="0" border="0" align="right" style="text-align: right;"><tr>
							<?php if ($is_finish) { ?>
								<?php /*<td><input type="image" name="action" value="restart_lpath" src="components/com_joomla_lms/lms_images/buttons/btn_restart.png" alt="<?php echo _JLMS_RESTART_ALT_TITLE;?>" /></td><td>&nbsp;<?php echo _JLMS_RESTART_ALT_TITLE;?>&nbsp;</td>*/ ?>
								<td><input type="submit" name="action" value="restart_lpath" style="background:transparent url(<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/buttons/btn_restart.png) no-repeat scroll left top; display:block; cursor: pointer; border: 0 none; padding: 0px; font-size: 0; width: 32px; height:32px;" alt="<?php echo _JLMS_RESTART_ALT_TITLE;?>" /></td><td>&nbsp;<?php echo _JLMS_RESTART_ALT_TITLE;?>&nbsp;</td>
							<?php } elseif ($element_task == 'check_cond') { ?>
								<?php /*<td><input type="image" name="action" value="seek_lpathstep" src="components/com_joomla_lms/lms_images/buttons/btn_start.png" alt="<?php echo _JLMS_CONTINUE_ALT_TITLE;?>" /></td><td>&nbsp;<?php echo _JLMS_CONTINUE_ALT_TITLE;?>&nbsp;</td>*/ ?>
								<td><input type="submit" name="action" value="seek_lpathstep" style="background:transparent url(<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/buttons/btn_start.png) no-repeat scroll left top; display:block; cursor: pointer; border: 0 none; padding: 0px; font-size: 0; width: 32px; height:32px;" alt="<?php echo _JLMS_CONTINUE_ALT_TITLE;?>" /></td><td>&nbsp;<?php echo _JLMS_CONTINUE_ALT_TITLE;?>&nbsp;</td>
								<?php /*<td><input type="image" name="action" value="contents_lpath" src="components/com_joomla_lms/lms_images/buttons/btn_contents.png" alt="<?php echo _JLMS_CONTENTS_ALT_TITLE;?>" /></td><td>&nbsp;<?php echo _JLMS_CONTENTS_ALT_TITLE;?>&nbsp;</td>*/ ?>
								<td><input type="submit" name="action" value="contents_lpath" style="background:transparent url(<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/buttons/btn_contents.png) no-repeat scroll left top; display:block; cursor: pointer; border: 0 none; padding: 0px; font-size: 0; width: 32px; height:32px;" alt="<?php echo _JLMS_CONTENTS_ALT_TITLE;?>" /></td><td>&nbsp;<?php echo _JLMS_CONTENTS_ALT_TITLE;?>&nbsp;</td>
							<?php } else { ?>
								<?php if ($i > 0) { ?>
										<td>
										<?php if (isset($element['step_type']->value) && $element['step_type']->value == 5 && isset($element['end_quiz']->value) && $element['end_quiz']->value == 0) { ?>
											<form name="lpath_MForm" action="<?php echo sefRelToAbs("index.php?option=com_joomla_lms&Itemid=$Itemid");?>" method="post" >
												<input type="hidden" name="option" value="com_joomla_lms" />
												<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
												<input type="hidden" name="task" value="show_lpath_nojs" />
												<input type="hidden" name="course_id" value="<?php echo $lpath_contents[$i]->course_id; ?>" />
												<input type="hidden" name="id" value="<?php echo $lpath_contents[$i]->lpath_id; ?>" />
												<?php /*<input type="image" name="action" value="prev_lpathstep" src="components/com_joomla_lms/lms_images/buttons/btn_back.png" alt="<?php echo _JLMS_PREV_ALT_TITLE;?>" />*/ ?>
												<input type="submit" name="action" value="prev_lpathstep" style="background:transparent url(<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/buttons/btn_back.png) no-repeat scroll left top; display:block; cursor: pointer; border: 0 none; padding: 0px; font-size: 0; width: 32px; height:32px;" alt="<?php echo _JLMS_PREV_ALT_TITLE;?>" />
												<input type="hidden" name="step_id" value="<?php echo $lpath_contents[$i]->id; ?>" />
												<input type="hidden" name="user_start_id" value="<?php echo (isset($element['user_start_id']->value) ? $element['user_start_id']->value : ''); ?>" />
												<input type="hidden" name="user_unique_id" value="<?php echo (isset($element['user_unique_id']->value) ? $element['user_unique_id']->value : '') ?>" />
											</form>
										<?php } else { ?>
											<?php /*<input type="image" name="action" value="prev_lpathstep" src="components/com_joomla_lms/lms_images/buttons/btn_back.png" alt="<?php echo _JLMS_PREV_ALT_TITLE;?>" />*/ ?>
											<input type="submit" name="action" value="prev_lpathstep" style="background:transparent url(<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/buttons/btn_back.png) no-repeat scroll left top; display:block; cursor: pointer; border: 0 none; padding: 0px; font-size: 0; width: 32px; height:32px;" alt="<?php echo _JLMS_PREV_ALT_TITLE;?>" />
										<?php } ?>
										</td><td>&nbsp;<?php echo _JLMS_PREV_ALT_TITLE;?>&nbsp;</td>
									<?php }
									if (isset($element['step_type']->value) && $element['step_type']->value == 5 && isset($element['end_quiz']->value) && $element['end_quiz']->value == 0) { ?>
										<td>
											<form name="lpath_MForm" action="<?php echo sefRelToAbs("index.php?option=com_joomla_lms&Itemid=$Itemid");?>" method="post" >
												<input type="hidden" name="option" value="com_joomla_lms" />
												<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
												<?php /*<input type="image" name="atask" value="start" src="components/com_joomla_lms/lms_images/buttons/btn_start.png" alt="<?php echo _JLMS_NEXT_ALT_TITLE;?>" />*/ ?>
												<input type="submit" name="atask" value="start" style="background:transparent url(<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/buttons/btn_start.png) no-repeat scroll left top; display:block; cursor: pointer; border: 0 none; padding: 0px; font-size: 0; width: 32px; height:32px;" alt="<?php echo _JLMS_NEXT_ALT_TITLE;?>" />
												<input type="hidden" name="task" value="quiz_action" />
												<input type="hidden" name="id" value="<?php echo $lpath_contents[$i]->course_id; ?>" />
												<input type="hidden" name="atask" value="start" />
												<input type="hidden" name="quiz" value="<?php echo (isset($lpath_contents[$i]->item_id) ? $lpath_contents[$i]->item_id : ''); ?>" />
												<input type="hidden" name="inside_lp" value="1" />
												<input type="hidden" name="step_id" value="<?php echo $lpath_contents[$i]->id; ?>" />
												<input type="hidden" name="lpath_id" value="<?php echo $lpath_contents[$i]->lpath_id; ?>" />
												<input type="hidden" name="user_start_id" value="<?php echo (isset($element['user_start_id']->value) ? $element['user_start_id']->value : ''); ?>" />
												<input type="hidden" name="lp_user_unique_id" value="<?php echo (isset($element['user_unique_id']->value) ? $element['user_unique_id']->value : '') ?>" />
												<!--<input type="hidden" name="this_lpath" value="1"/>-->
											</form>
										</td><td>&nbsp;<?php echo _JLMS_NEXT_ALT_TITLE;?>&nbsp;</td>
									<?php } else { ?>
										<?php /*<td><input type="image" name="action" value="next_lpathstep" src="components/com_joomla_lms/lms_images/buttons/btn_start.png" alt="<?php echo _JLMS_NEXT_ALT_TITLE;?>" /></td><td>&nbsp;<?php echo _JLMS_NEXT_ALT_TITLE;?>&nbsp;</td>*/ ?>
										<td><input type="submit" name="action" value="next_lpathstep" style="background:transparent url(<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/buttons/btn_start.png) no-repeat scroll left top; display:block; cursor: pointer; border: 0 none; padding: 0px; font-size: 0; width: 32px; height:32px" alt="<?php echo _JLMS_NEXT_ALT_TITLE;?>" /></td><td>&nbsp;<?php echo _JLMS_NEXT_ALT_TITLE;?>&nbsp;</td>
									<?php } ?>
									<td>
										<?php if (isset($element['step_type']->value) && $element['step_type']->value == 5 && isset($element['end_quiz']->value) && $element['end_quiz']->value == 0 ) { ?>
											<form name="lpath_MForm" action="<?php echo sefRelToAbs("index.php?option=com_joomla_lms&Itemid=$Itemid");?>" method="post" >
												<input type="hidden" name="option" value="com_joomla_lms" />
												<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
												<input type="hidden" name="task" value="show_lpath_nojs" />
												<input type="hidden" name="course_id" value="<?php echo $lpath_contents[$i]->course_id; ?>" />
												<input type="hidden" name="id" value="<?php echo $lpath_contents[$i]->lpath_id; ?>" />
												<?php /*<input type="image" name="action" value="contents_lpath" src="components/com_joomla_lms/lms_images/buttons/btn_contents.png" alt="<?php echo _JLMS_CONTENTS_ALT_TITLE;?>" />*/?>
												<input type="submit" name="action" value="contents_lpath" style="background:transparent url(<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/buttons/btn_contents.png) no-repeat scroll left top; display:block; cursor: pointer; border: 0 none; padding: 0px; font-size: 0; width: 32px; height:32px" alt="<?php echo _JLMS_CONTENTS_ALT_TITLE;?>" />
												<input type="hidden" name="step_id" value="<?php echo $lpath_contents[$i]->id; ?>" />
												<input type="hidden" name="user_start_id" value="<?php echo (isset($element['user_start_id']->value) ? $element['user_start_id']->value : ''); ?>" />
												<input type="hidden" name="user_unique_id" value="<?php echo (isset($element['user_unique_id']->value) ? $element['user_unique_id']->value : '') ?>" />
											</form>
										<?php } else { ?>
										<?php /*<input type="image" name="action" value="contents_lpath" src="components/com_joomla_lms/lms_images/buttons/btn_contents.png" alt="<?php echo _JLMS_CONTENTS_ALT_TITLE;?>" />*/?>
										<input type="submit" name="action" value="contents_lpath" style="background:transparent url(<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/buttons/btn_contents.png) no-repeat scroll left top; display:block; cursor: pointer; border: 0 none; padding: 0px; font-size: 0; width: 32px; height:32px" alt="<?php echo _JLMS_CONTENTS_ALT_TITLE;?>" />
										<?php } ?>
									</td><td>&nbsp;<?php echo _JLMS_CONTENTS_ALT_TITLE;?>&nbsp;</td>
							<?php } ?>
							</tr></table>
						<?php if (isset($element['step_type']->value) && $element['step_type']->value == 5 && isset($element['end_quiz']->value) && $element['end_quiz']->value == 0) { /* !!!!! */ } else { ?>
						<input type="hidden" name="option" value="com_joomla_lms" />
						<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
						<input type="hidden" name="task" value="show_lpath_nojs" />
						<input type="hidden" name="course_id" value="<?php echo $lpath_contents[$i]->course_id; ?>" />
						<input type="hidden" name="id" value="<?php echo $lpath_contents[$i]->lpath_id; ?>" />
						<?php if ($element_task == 'check_cond') { ?>
						<input type="hidden" name="seek_step_id" value="<?php echo isset($element['seek_id']->value) ? $element['seek_id']->value : $lpath_contents[$i]->id; ?>" />
						<?php } ?>
						<input type="hidden" name="step_id" value="<?php echo $lpath_contents[$i]->id; ?>" />
						<input type="hidden" name="user_start_id" value="<?php echo (isset($element['user_start_id']->value) ? $element['user_start_id']->value : ''); ?>" />
						<input type="hidden" name="user_unique_id" value="<?php echo (isset($element['user_unique_id']->value) ? $element['user_unique_id']->value : '') ?>" />
						<?php } ?>
					</form>
				</td>
			</tr>
		</table>
<?php JLMS_TMPL::CloseTS();JLMS_TMPL::OpenTS();
		if (isset($element['lpath_completion_msg']->value) && $element['lpath_completion_msg']->value) { ?>
		<div id="jlms_lpath_completion_msg_container" class="jlms_lpath_completion_message">
			<?php echo $element['lpath_completion_msg']->value; ?>
		</div>
		<?php } ?>
		<div id="jlms_lpath_descr" style="width:100%">
<?php
			//if ($is_finish) {
			//	$completion_msg = '***JLMS***SHOW***CONTENTS***';
			//}
			$user_unique_id = isset($element['user_unique_id']->value) ? $element['user_unique_id']->value : '';
			$user_start_id = isset($element['user_start_id']->value) ? $element['user_start_id']->value : '';

			if ($is_finish) {
				$quizzes_data = array();
				$mark_complete = array();
				$mark_pending = array();
				
				//Max 26.03.08 Quizzes_data
				$course_id = $lpath_contents[$i]->course_id;
				$lpath_id = $lpath_contents[$i]->lpath_id;
				global $my, $JLMS_DB;
	
				$count_quizzes = 0;
				$quiz_ids = array();
				$quiz_steps = array();
				$quizzes_data = array();
				foreach ($lpath_contents as $lc) {
					if ($lc->step_type == 5) {
						$count_quizzes ++;
						$quiz_ids[] = $lc->item_id;
						$quiz_steps[] = $lc->id;
					}
				}
				if ($count_quizzes) {
					$qqi = 0;
					foreach ($quiz_ids as $qi) {
						$fquiz = new stdClass();
						$fquiz->c_id = $qi;
						$query = "SELECT c_slide FROM #__lms_quiz_t_quiz WHERE c_id = '".$qi."'";
						$JLMS_DB->setQuery($query);
						$quiz_obj = $JLMS_DB->loadObject();
						$fquiz->c_slide = $quiz_obj->c_slide;
						$query = "SELECT a.id as result_id, b.stu_quiz_id FROM #__lms_learn_path_results as a, #__lms_learn_path_step_quiz_results as b WHERE a.user_id = '".$my->id."'"
						. "\n AND a.course_id = '".$course_id."' AND a.lpath_id = '".$lpath_id."' AND b.result_id = a.id AND b.step_id = ".$quiz_steps[$qqi];
						$JLMS_DB->SetQuery( $query );
						$stu_quiz_id = 0;
						$res_id = 0;
						$step_status = 0;
						$trtrtrt = $JLMS_DB->LoadObject();
						if (is_object($trtrtrt)) {
							$stu_quiz_id = $trtrtrt->stu_quiz_id;
							$res_id = $trtrtrt->result_id;
							if ($res_id) {
								$query = "SELECT step_status FROM #__lms_learn_path_step_results WHERE result_id = $res_id AND step_id = $quiz_steps[$qqi]";
								$JLMS_DB->SetQuery( $query );
								$rrtt = $JLMS_DB->loadResult();
								if ($rrtt) {
									$step_status = $rrtt;
								}
							}
						}
			
						// 27 April 2007 (DEN) - Question Pool support
						if ($stu_quiz_id && $step_status) {
							$query = "SELECT a.*, b.c_id as stu_quest, b.c_score as stu_score FROM #__lms_quiz_r_student_quiz_pool as qp, #__lms_quiz_t_question as a"
							. "\n LEFT JOIN #__lms_quiz_r_student_question as b ON b.c_stu_quiz_id = $stu_quiz_id AND b.c_question_id = a.c_id"
							. "\n WHERE qp.start_id = $stu_quiz_id AND qp.quest_id = a.c_id AND a.course_id = ".$course_id." ORDER BY qp.ordering";// (a.c_quiz_id = ".$this->quiz_id." OR (a.c_quiz_id = 0 AND )) ORDER BY b.ordering";
							$JLMS_DB->SetQuery( $query );
							$fquiz->panel_data = $JLMS_DB->LoadObjectList();
						} else {
							// old way
							/*$query = "SELECT a.*, b.c_id as stu_quest, b.c_score as stu_score FROM #__lms_quiz_t_question as a"
							. "\n LEFT JOIN #__lms_quiz_r_student_question as b ON b.c_stu_quiz_id = $stu_quiz_id AND b.c_question_id = a.c_id"
							. "\n WHERE a.c_quiz_id = '".$qi."' ORDER BY ordering, c_id";*/
							$fquiz->panel_data = array();
						}
			
						$fquiz->stu_quiz_id = $stu_quiz_id;
			
						$q_from_pool = array();
						foreach ($fquiz->panel_data as $row) {
							if ($row->c_type == 20) {
								$q_from_pool[] = $row->c_pool;
							}
						}
						if (count($q_from_pool)) {
							$qp_ids =implode(',',$q_from_pool);
							$query = "SELECT a.* FROM #__lms_quiz_t_question as a"
							. "\n WHERE a.course_id = ".$course_id;
							$JLMS_DB->setQuery( $query );
							$rows2 = $JLMS_DB->loadObjectList();
							for ($iii=0, $n=count( $fquiz->panel_data ); $iii < $n; $iii++) {
								if ($fquiz->panel_data[$iii]->c_type == 20) {
									for ($j=0, $m=count( $rows2 ); $j < $m; $j++) {
										if ($fquiz->panel_data[$iii]->c_pool == $rows2[$j]->c_id) {
											$fquiz->panel_data[$iii]->c_question = $rows2[$j]->c_question;
											$fquiz->panel_data[$iii]->c_point = $rows2[$j]->c_point;
											$fquiz->panel_data[$iii]->c_attempts = $rows2[$j]->c_attempts;
											$fquiz->panel_data[$iii]->c_type = $rows2[$j]->c_type;
											break;
										}
									}
								}
							}
						}
			
						$ar_element = 'quiz_'.$qi;
						$quizzes_data[$ar_element] = $fquiz;
						$qqi ++;
					}
				}

				if (isset($element['mark_complete']->value) && is_array($element['mark_complete']->value)) {
					$mark_complete = $element['mark_complete']->value;
				}
				if (isset($element['mark_pending']->value) && is_array($element['mark_pending']->value)) {
					$mark_pending = $element['mark_pending']->value;
				}
				$ii = 0;
				while ($ii < count($lpath_contents)) {
					$lpath_contents[$ii]->nojs_contents_status = 0;
					if (!$is_finish && $lpath_contents[$ii]->id == $lpath_contents[$i]->id) {
						$lpath_contents[$ii]->nojs_contents_status = 3;
					} elseif (in_array($lpath_contents[$ii]->id, $mark_complete)) {
						$lpath_contents[$ii]->nojs_contents_status = 1;
					} elseif (in_array($lpath_contents[$ii]->id, $mark_pending)) {
						$lpath_contents[$ii]->nojs_contents_status = 2;
					}
					$ii ++;
				}
				JLMS_course_lpathstu_html::showLPath_contents($lpath_contents, $quizzes_data, true, $user_unique_id, $user_start_id);
				if (isset($element['step_descr']->value) && $element['step_descr']->value) {
					echo $element['step_descr']->value;
				}
			} elseif ($element_task == 'next_step' || $element_task == 'start_restart' || $element_task == 'start') {
				$elem_info = JLMS_prepareLPath_Elem_DATA($lpath_contents[$i], $user_unique_id, $user_start_id);
				echo $elem_info['step_descr'];	
			} elseif ($element_task == 'check_cond') {
				if (!empty($element['conditions'])) {
					echo '<div class="'.$JLMS_CONFIG->get('system_message_css_class', 'joomlalms_sys_message').'">';
					echo _JLMS_LPATH_COMPLETE_STEPS_TXT. '<br />';
					foreach ($element['conditions'] as $cond) {
						echo '<a href="'.sefRelToAbs("index.php?option=com_joomla_lms&Itemid=".$Itemid."&task=show_lpath_nojs&course_id=".$element['course_id']->value."&step_id=".$cond->seek_id."&id=".$element['lpath_id']->value."&action=seek_lpathstep&user_start_id=$user_start_id&user_unique_id=$user_unique_id").'"title="'._JLMS_LPATH_COMPLETE_STEP_TITLE.'">'.$cond->seek_name.'<a><br />';
					}
					echo '</div>';
				} else {
					echo '<div class="joomlalms_sys_message">'._JLMS_LPATH_LOAD_DATA_ERROR.'</div>';
				}
			} else {
				echo '<div class="joomlalms_sys_message">'._JLMS_LPATH_LOAD_DATA_ERROR.'</div>';
			}
?>
		</div>
<?php JLMS_TMPL::CloseTS();JLMS_TMPL::CloseMT();
	}

	function show_ErrorMessage($error) {
		global $Itemid;

		JLMS_TMPL::OpenMT();
		$hparams = array();
		JLMS_TMPL::ShowHeader('lpath', _JLMS_LPATH_TITLE, $hparams);
		JLMS_TMPL::OpenTS();
			echo '<div class="joomlalms_sys_message">'.$error.'</div>';
		JLMS_TMPL::CloseTS();
		JLMS_TMPL::CloseMT();
	}

}
?>