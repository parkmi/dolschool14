<?php
/**
* front_html.joomlaquiz.class.php
* (c) JoomaLMS eLearning Software http://www.joomlalms.com/
**/

defined( '_JEXEC' ) or die( 'Restricted access' );
class JLMS_quiz_front_html_class {
	function JQ_ShowQuiz( $option, $course_id, $quiz, $jq_language, $self_verification, $is_preview = false, $preview_quest = 0, $preview_id = '' ) {
		global $Itemid, $JLMS_CONFIG, $JLMS_DB;
		
		$doc = & JFactory::getDocument();

$domready = '
if (document.constructor) {
	document.constructor.prototype.write = function() { };
} else {
	document.write = function() { };
}
';
$JLMS_CONFIG->set('web20_domready_code', $JLMS_CONFIG->get('web20_domready_code','').$domready);

$doc->addScript( $JLMS_CONFIG->get('live_site').'/components/com_joomla_lms/includes/quiz/bits_message.js' );

if( JLMS_mootools12() ){
	$doc->addScript( $JLMS_CONFIG->get('live_site').'/components/com_joomla_lms/includes/quiz/dragdrop_1.3.js' );	
} else {
	$doc->addScript( $JLMS_CONFIG->get('live_site').'/components/com_joomla_lms/includes/quiz/dragdrop_1.12.js' );	
}

if($JLMS_CONFIG->get('quiz_progressbar', 0) == 1) {
	$doc->addScript( $JLMS_CONFIG->get('live_site').'/components/com_joomla_lms/includes/quiz/progressbar.js' );	
}

?>
<script language="JavaScript" type="text/javascript">
<!--//--><![CDATA[//><!--
	var stu_quiz_id = 0;

	// *** DRAG'and'DROP CODE *** //
<?php if ($quiz->if_dragdrop_exist) { ?>
	var kol_drag_elems = 0;
	var drag_array = new Array(kol_drag_elems);
	var coord_left = new Array(kol_drag_elems);
	var coord_top = new Array(kol_drag_elems);
	var ids_in_cont = new Array(kol_drag_elems);
	var cont_for_ids = new Array(kol_drag_elems);
	var answ_ids = new Array(kol_drag_elems);
	var cont_index = 0;
	var last_drag_id = '';
	var last_drag_id_drag = '';
	
	/*drag&drop mootools realised*/
	function excute_draggable(){
		var DD = new QuizDragDrop('cont', {dropitems: 'jq_drop', dragitems: 'jq_drag'});
	}
	
	function js_in_array(n, ha){
		for(h in ha){
			if(ha[h]==n){
				return true;
			}
		}
		return false;
	}

//});
	/*drag&drop mootools realised*/
	
<?php } ?>
	// *** end of DRAG'and'DROP CODE *** //
	var kol_main_elems = 0;
	var main_ids_array = new Array(kol_main_elems); //for likert quest
	// *** MESSAGES *** (addslashes ???)
	var mes_complete_this_part = '<?php echo $jq_language['mes_complete_this_part'];?>';
	var mes_loading = '<?php echo $jq_language['quiz_load_data'];?>';
	var mes_failed = '<?php echo $jq_language['quiz_failed'];?>';
	var mes_please_wait = '<?php echo $jq_language['mes_please_wait'];?>';
	var mes_time_is_up = '<?php echo $jq_language['quiz_mes_timeout'];?>';
	var mes_quest_number = '<?php echo $jq_language['quiz_question_number'];?>';
	var mes_quest_points = '<?php echo $jq_language['quiz_question_points'];?>';
	// *** some script variables ***
	var user_email_to = '';
	var user_unique_id = '';
	var cur_quest_type = '';
	var saved_prev_quest_data = '';
	var saved_prev_quest_exec_quiz_script = '';
	var save_prev_quest_exec_quiz_script_data = '';
	var saved_prev_res_data = '';
	var saved_prev_quest_id = 0;
	var saved_prev_quest_type = 0;
	var saved_prev_quest_score = 0;
	var saved_prev_quest_num = 0;
	var cur_quest_id = 0;
	var cur_quest_score = 0;
	var cur_quest_num = 0;
	var quiz_count_quests = 0;
	var cur_impscale_ex = 0;
	var response;
	var prev_correct = 0;
	var allow_attempt = 0;
	var timer_sec = <?php echo ( isset($quiz->resume_timer_value) && $quiz->resume_timer_value) ? $quiz->resume_timer_value : 0 ; ?>;
	var stop_timer = 0;
	var result_is_shown = 0;
	var max_quiz_time = <?php echo ($quiz->c_time_limit)?($quiz->c_time_limit * 60):3600; ?>;
	var quiz_blocked = 0; // set block after each question (release after 2 seconds).
	var url_prefix = '<?php echo $JLMS_CONFIG->get('live_site') . "/index.php?option=$option&Itemid=$Itemid&jlms=1&task=quiz_ajax_action&id=$course_id";?>';//'components/com_joomlaquiz/ajax_quiz.php';
	var mCfg_live_site = '';
	
	var stu_quiz_id = 0;
	var user_unique_id = 0;
	
	var quiz_progress = 0; // progressbar value
	
	var review = 0;

	function jq_MakeRequest(url, do_clear) {
		//if (do_clear == 1) jq_UpdateTaskDiv('clear');
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
		if (do_clear == 1) {
			jq_showLoading();
		}
		quiz_blocked == 1;
		http_request.onreadystatechange = function() { jq_AnalizeRequest(http_request); };
		<?php if ($is_preview) { ?>
		var url_prefix2 = 'preview_id=<?php echo $preview_id;?>';
		<?php } else { ?>
		var url_prefix2 = 'user_unique_id=' + user_unique_id;
		<?php } ?>
		http_request.open('POST', mCfg_live_site + url_prefix, true);
		//http_request.send(null);
		http_request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		http_request.setRequestHeader("Content-length", url_prefix2.length + url.length);
		//http_request.setRequestHeader("Content-Encoding", "utf-8");
		//http_request.setRequestHeader("Connection", "close"); - if close - bug in IE7 - it hungs up
		http_request.send(url_prefix2 + url);
	}
	function jq_AnalizeRequest(http_request) {
		if (http_request.readyState == 4) {
			if ((http_request.status == 200)) {
				//alert(http_request.responseText);
				if(http_request.responseXML.documentElement == null){
					try {
						http_request.responseXML.loadXML(http_request.responseText)
					} catch (e) {
						/*alert('error');*/
					}
				}

				response  = http_request.responseXML.documentElement;
				var task = response.getElementsByTagName('task')[0].firstChild.data;
				ShowMessage('error_messagebox',0,'');

				switch (task) {
					case 'resume':

						user_unique_id = response.getElementsByTagName('user_unique_id')[0].firstChild.data;
						stu_quiz_id = response.getElementsByTagName('stu_quiz_id')[0].firstChild.data;

						cur_quest_id = response.getElementsByTagName('quest_id')[0].firstChild.data;

						quiz_count_quests = response.getElementsByTagName('quiz_count_quests')[0].firstChild.data;
						saved_prev_quest_id = cur_quest_id;
						
						<?php if ($quiz->c_slide) { ?>
							getObj('jq_quiz_result_container').innerHTML = response.getElementsByTagName('quiz_panel_data')[0].firstChild.data;
						<?php } ?>

						cur_quest_num = response.getElementsByTagName('quiz_quest_num')[0].firstChild.data;
						quiz_progress = response.getElementsByTagName('progress_quests_done')[0].firstChild.data;

						<?php if($JLMS_CONFIG->get('quiz_progressbar', 0) == 1){ ?>
						progressbar.setProgress(quiz_progress);
						<?php } ?>

						jq_QuizContinue();
						jq_Start_TickTackResume();
					break;

					case 'start':
						quiz_blocked = 1;
						setTimeout("jq_releaseBlock()", 1000);
						user_unique_id = response.getElementsByTagName('user_unique_id')[0].firstChild.data;
						stu_quiz_id = response.getElementsByTagName('stu_quiz_id')[0].firstChild.data;
						cur_quest_type = response.getElementsByTagName('quest_type')[0].firstChild.data;
						saved_prev_quest_type = cur_quest_type;
						cur_quest_id = response.getElementsByTagName('quest_id')[0].firstChild.data;
						saved_prev_quest_id = cur_quest_id;
						cur_quest_score = response.getElementsByTagName('quest_score')[0].firstChild.data;
						saved_prev_quest_score = cur_quest_score;
						quiz_count_quests = response.getElementsByTagName('quiz_count_quests')[0].firstChild.data;
						cur_quest_num = response.getElementsByTagName('quiz_quest_num')[0].firstChild.data;
						saved_prev_quest_num = cur_quest_num;
						getObj('jq_quiz_container').innerHTML = '';
						if (cur_quest_type == 7) {
							var div_insidey=document.createElement("div");
							div_insidey.id = 'quest_div_hs';
							getObj('jq_quiz_container').appendChild(div_insidey);
						}
						var div_inside1=document.createElement("div");
						div_inside1.id = 'quest_div';

						div_inside1.innerHTML = response.getElementsByTagName('quest_data')[0].firstChild.data + response.getElementsByTagName('quest_data_user')[0].firstChild.data;
						saved_prev_quest_data = response.getElementsByTagName('quest_data')[0].firstChild.data + response.getElementsByTagName('quest_data_user')[0].firstChild.data;
						getObj('jq_quiz_container').appendChild(div_inside1);

						var quiz_menu = response.getElementsByTagName('quiz_menu')[0].firstChild.data;
						jq_UpdateTaskDiv_htm(quiz_menu);
						jq_UpdateTaskDiv('next');
						var is_exec_quiz_script = response.getElementsByTagName('exec_quiz_script')[0].firstChild.data;
						saved_prev_quest_exec_quiz_script = is_exec_quiz_script;
						if (is_exec_quiz_script == 1 || is_exec_quiz_script == '1' ) {
							var v_quiz_script_data = response.getElementsByTagName('quiz_script_data')[0].firstChild.data;
							saved_prev_quest_exec_quiz_script_data = v_quiz_script_data;
							eval(v_quiz_script_data);
						}
						jq_Start_TickTack();
						<?php if ($quiz->c_slide) { ?>
							getObj('jq_quiz_result_container').innerHTML = response.getElementsByTagName('quiz_panel_data')[0].firstChild.data;
						<?php } ?>
					break;
					case 'seek_quest':
						quiz_blocked = 1;
						setTimeout("jq_releaseBlock()", 1000);
						cur_quest_type = response.getElementsByTagName('quest_type')[0].firstChild.data;
						saved_prev_quest_type = cur_quest_type;
						cur_quest_id = response.getElementsByTagName('quest_id')[0].firstChild.data;
						saved_prev_quest_id = cur_quest_id;
						cur_quest_score = response.getElementsByTagName('quest_score')[0].firstChild.data;
						saved_prev_quest_score = cur_quest_score;
						cur_quest_num = response.getElementsByTagName('quiz_quest_num')[0].firstChild.data;
						saved_prev_quest_num = cur_quest_num;
						getObj('jq_quiz_container').innerHTML = '';
						if (cur_quest_type == 7) {
							var div_insidey=document.createElement("div");
							div_insidey.id = 'quest_div_hs';
							getObj('jq_quiz_container').appendChild(div_insidey);
						}
						var div_inside1=document.createElement("div");
						div_inside1.id = 'quest_div';
						div_inside1.innerHTML = response.getElementsByTagName('quest_data')[0].firstChild.data + response.getElementsByTagName('quest_data_user')[0].firstChild.data;
						saved_prev_quest_data = response.getElementsByTagName('quest_data')[0].firstChild.data + response.getElementsByTagName('quest_data_user')[0].firstChild.data;
						getObj('jq_quiz_container').appendChild(div_inside1);
						var quiz_menu = response.getElementsByTagName('quiz_menu')[0].firstChild.data;
						jq_UpdateTaskDiv_htm(quiz_menu);
						jq_UpdateTaskDiv('next');

						quiz_progress = response.getElementsByTagName('progress_quests_done')[0].firstChild.data;
						<?php if($JLMS_CONFIG->get('quiz_progressbar', 0) == 1){ ?>
							progressbar.setProgress(quiz_progress);
						<?php } ?>

						var is_exec_quiz_script = response.getElementsByTagName('exec_quiz_script')[0].firstChild.data;
						saved_prev_quest_exec_quiz_script = is_exec_quiz_script;
						if (is_exec_quiz_script == 1 || is_exec_quiz_script == '1' ) {
							var v_quiz_script_data = response.getElementsByTagName('quiz_script_data')[0].firstChild.data;
							saved_prev_quest_exec_quiz_script_data = v_quiz_script_data;
							eval(v_quiz_script_data);
						}
					break;
					case 'review_start':
						quiz_blocked = 1;
						review = 1;
						setTimeout("jq_releaseBlock()", 1000);
						cur_quest_type = response.getElementsByTagName('quest_type')[0].firstChild.data;
						saved_prev_quest_type = cur_quest_type;
						cur_quest_id = response.getElementsByTagName('quest_id')[0].firstChild.data;
						saved_prev_quest_id = cur_quest_id;
						cur_quest_score = response.getElementsByTagName('quest_score')[0].firstChild.data;
						saved_prev_quest_score = cur_quest_score;
						quiz_count_quests = response.getElementsByTagName('quiz_count_quests')[0].firstChild.data;
						cur_quest_num = response.getElementsByTagName('quiz_quest_num')[0].firstChild.data;
						saved_prev_quest_num = cur_quest_num;
						
						
						getObj('jq_quiz_container').innerHTML = '';
						var div_inside1=document.createElement("div");
						div_inside1.id = 'quest_div';
						div_inside1.innerHTML = response.getElementsByTagName('quest_data')[0].firstChild.data + response.getElementsByTagName('quest_data_user')[0].firstChild.data;
						saved_prev_quest_data = response.getElementsByTagName('quest_data')[0].firstChild.data + response.getElementsByTagName('quest_data_user')[0].firstChild.data;
						getObj('jq_quiz_container').appendChild(div_inside1);
						var quiz_menu = response.getElementsByTagName('quiz_menu')[0].firstChild.data;
						quiz_review_correct = response.getElementsByTagName('quiz_review_correct')[0].firstChild.data;
						getObj('jq_quiz_result_reviews').innerHTML = quiz_review_correct;
						getObj('jq_quiz_result_reviews').style.display = 'block';
						getObj('jq_quiz_result_reviews').style.visibility = 'visible';
						getObj('jq_quiz_explanation').innerHTML = response.getElementsByTagName('quiz_review_explanation')[0].firstChild.data;;
						getObj('jq_quiz_explanation').style.display = 'block';
						getObj('jq_quiz_explanation').style.visibility = 'visible';
						jq_UpdateTaskDiv_htm(quiz_menu);
						jq_UpdateTaskDiv('review_next');
						
						var is_exec_quiz_script = response.getElementsByTagName('exec_quiz_script')[0].firstChild.data;
						saved_prev_quest_exec_quiz_script = is_exec_quiz_script;
						if (is_exec_quiz_script == 1 || is_exec_quiz_script == '1' ) {
							var v_quiz_script_data = response.getElementsByTagName('quiz_script_data')[0].firstChild.data;
							saved_prev_quest_exec_quiz_script_data = v_quiz_script_data;
							eval(v_quiz_script_data);
						}
						
						<?php if ($quiz->c_slide) { ?>
							//getObj('jq_panel_link_container').style.visibility = 'visible';
						<?php } ?>
					break;
					case 'review_next':
						quiz_blocked = 1;
						setTimeout("jq_releaseBlock()", 1000);
						cur_quest_type = response.getElementsByTagName('quest_type')[0].firstChild.data;
						saved_prev_quest_type = cur_quest_type;
						cur_quest_id = response.getElementsByTagName('quest_id')[0].firstChild.data;
						saved_prev_quest_id = cur_quest_id;
						prev_quest_id = response.getElementsByTagName('prev_quest_id')[0].firstChild.data;
						cur_quest_score = response.getElementsByTagName('quest_score')[0].firstChild.data;
						saved_prev_quest_score = cur_quest_score;
						cur_quest_num = response.getElementsByTagName('quiz_quest_num')[0].firstChild.data;
						saved_prev_quest_num = cur_quest_num;
						
						getObj('jq_quiz_container').innerHTML = '';
						var div_inside1=document.createElement("div");
						div_inside1.id = 'quest_div';
						div_inside1.innerHTML = response.getElementsByTagName('quest_data')[0].firstChild.data + response.getElementsByTagName('quest_data_user')[0].firstChild.data;
						saved_prev_quest_data = response.getElementsByTagName('quest_data')[0].firstChild.data + response.getElementsByTagName('quest_data_user')[0].firstChild.data;
						getObj('jq_quiz_container').appendChild(div_inside1);
						var quiz_menu = response.getElementsByTagName('quiz_menu')[0].firstChild.data;
						quiz_review_correct = response.getElementsByTagName('quiz_review_correct')[0].firstChild.data;
						getObj('jq_quiz_result_reviews').innerHTML = quiz_review_correct;
						getObj('jq_quiz_result_reviews').style.display = 'block';
						getObj('jq_quiz_result_reviews').style.visibility = 'visible';
						getObj('jq_quiz_explanation').innerHTML = response.getElementsByTagName('quiz_review_explanation')[0].firstChild.data;;
						getObj('jq_quiz_explanation').style.display = 'block';
						getObj('jq_quiz_explanation').style.visibility = 'visible';
						jq_UpdateTaskDiv_htm(quiz_menu);
						jq_UpdateTaskDiv('review_next');
						
						var is_exec_quiz_script = response.getElementsByTagName('exec_quiz_script')[0].firstChild.data;
						saved_prev_quest_exec_quiz_script = is_exec_quiz_script;
						if (is_exec_quiz_script == 1 || is_exec_quiz_script == '1' ) {
							var v_quiz_script_data = response.getElementsByTagName('quiz_script_data')[0].firstChild.data;
							saved_prev_quest_exec_quiz_script_data = v_quiz_script_data;
							eval(v_quiz_script_data);
						}
					break;
					case 'review_finish':
						quiz_blocked = 1;
						review = 0;
						setTimeout("jq_releaseBlock()", 1000);
						jq_UpdateTaskDiv('finish');
						var quiz_cont = getObj('jq_quiz_container');
						quiz_cont.innerHTML = saved_prev_res_data;//'<form name=\'quest_form\'><\/form>'+saved_prev_res_data;
					break;
					case 'next':
						quiz_blocked = 1;

						quiz_progress = response.getElementsByTagName('progress_quests_done')[0].firstChild.data;
						<?php if($JLMS_CONFIG->get('quiz_progressbar', 0) == 1){ ?>
						progressbar.setProgress(quiz_progress);
						<?php } ?>

						setTimeout("jq_releaseBlock()", 1000);
						prev_correct = response.getElementsByTagName('quiz_prev_correct')[0].firstChild.data;
						var quiz_cont = getObj('jq_quiz_container');
						var children = quiz_cont.childNodes;
						for (var i = 0; i < children.length; i++) { quiz_cont.removeChild(quiz_cont.childNodes[i]); };
						quiz_cont.innerHTML = '';//'<form name=\'quest_form\'><\/form>';
						quest_feedback = response.getElementsByTagName('quest_feedback')[0].firstChild.data;
						if (quest_feedback == '1') {
							var qmb = response.getElementsByTagName('quiz_message_box')[0].firstChild.data;
							var quiz_menu = response.getElementsByTagName('quiz_menu')[0].firstChild.data;
							jq_UpdateTaskDiv_htm(quiz_menu);
							if (prev_correct == '1') {
							<?php if ($quiz->c_slide) { ?>
								getObj('quest_result_'+saved_prev_quest_id).innerHTML = "<img src='<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_accept.png' border=0>";
							<?php } ?>
								quiz_cont.innerHTML = qmb;//'<form name=\'quest_form\'><\/form>'+qmb;
								jq_UpdateTaskDiv('continue');
							} else {
							<?php if ($quiz->c_slide) { ?>
								getObj('quest_result_'+saved_prev_quest_id).innerHTML = "<img src='<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_cancel.png' border=0>";
							<?php } ?>
								quiz_cont.innerHTML = qmb;//'<form name=\'quest_form\'><\/form>'+qmb;
								allow_attempt = response.getElementsByTagName('quiz_allow_attempt')[0].firstChild.data;
								if (allow_attempt == '1') { allow_attempt = 0; jq_UpdateTaskDiv('back_continue');
								} else { allow_attempt = 0; jq_UpdateTaskDiv('continue'); }
							}
						} else {
							var qmb = '';
							var qfrf = response.getElementsByTagName('quest_feedback_repl_func')[0].firstChild.data;
							if (prev_correct == '1') {
							<?php if ($quiz->c_slide) { ?>
								getObj('quest_result_'+saved_prev_quest_id).innerHTML = "<img src='<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_accept.png' border=0>";
							<?php } ?>
							} else {
							<?php if ($quiz->c_slide) { ?>
								getObj('quest_result_'+saved_prev_quest_id).innerHTML = "<img src='<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_cancel.png' border=0>";
							<?php } ?>
							}
							eval(qfrf);
						}
					break;
					<?php 
					if($is_preview){
					?>
					case 'quest_preview':
						quiz_blocked = 1;
						setTimeout("jq_releaseBlock()", 1000);
						quiz_count_quests = response.getElementsByTagName('quiz_count_quests')[0].firstChild.data;
						var quiz_cont = getObj('jq_quiz_container');
						var children = quiz_cont.childNodes;
						for (var i = 0; i < children.length; i++) { quiz_cont.removeChild(quiz_cont.childNodes[i]); };
						var qmb = response.getElementsByTagName('quiz_message_box')[0].firstChild.data;
						quiz_cont.innerHTML = '';//<form name=\'quest_form\'></form>';
						cur_quest_type = response.getElementsByTagName('quest_type')[0].firstChild.data;
						saved_prev_quest_type = cur_quest_type;
						cur_quest_id = response.getElementsByTagName('quest_id')[0].firstChild.data;
						saved_prev_quest_id = cur_quest_id;
						cur_quest_score = response.getElementsByTagName('quest_score')[0].firstChild.data;
						saved_prev_quest_score = cur_quest_score;
						cur_quest_num = response.getElementsByTagName('quiz_quest_num')[0].firstChild.data;
						saved_prev_quest_num = cur_quest_num;
						var quiz_cont = getObj('jq_quiz_container');
						quiz_cont.innerHTML = '';
						if (cur_quest_type == 7) {
							var div_insidey=document.createElement("div");
							div_insidey.id = 'quest_div_hs';
							getObj('jq_quiz_container').appendChild(div_insidey);
						}
						var div_inside1=document.createElement("div");
						div_inside1.id = 'quest_div';
						div_inside1.innerHTML = response.getElementsByTagName('quest_data')[0].firstChild.data +response.getElementsByTagName('quest_data_user')[0].firstChild.data;
						saved_prev_quest_data = response.getElementsByTagName('quest_data')[0].firstChild.data + response.getElementsByTagName('quest_data_user')[0].firstChild.data;
						quiz_cont.appendChild(div_inside1);
						var quiz_menu = response.getElementsByTagName('quiz_menu')[0].firstChild.data;
						jq_UpdateTaskDiv_htm(quiz_menu);
						jq_UpdateTaskDiv('next');
						var is_exec_quiz_script = response.getElementsByTagName('exec_quiz_script')[0].firstChild.data;
						saved_prev_quest_exec_quiz_script = is_exec_quiz_script;
						if (is_exec_quiz_script == 1 || is_exec_quiz_script == '1' ) {
							var v_quiz_script_data = response.getElementsByTagName('quiz_script_data')[0].firstChild.data;
							saved_prev_quest_exec_quiz_script_data = v_quiz_script_data;
							eval(v_quiz_script_data);
						}
					break;
					case 'preview_finish':
						quiz_blocked = 1;
						setTimeout("jq_releaseBlock()", 1000);
						prev_correct = response.getElementsByTagName('quiz_prev_correct')[0].firstChild.data;
						var quiz_cont = getObj('jq_quiz_container');
						var children = quiz_cont.childNodes;
						for (var i = 0; i < children.length; i++) { quiz_cont.removeChild(quiz_cont.childNodes[i]); };
						var qmb = response.getElementsByTagName('quiz_message_box')[0].firstChild.data;
						quiz_cont.innerHTML = '';//'<form name=\'quest_form\'><\/form>';
						var quiz_menu = response.getElementsByTagName('quiz_menu')[0].firstChild.data;
						jq_UpdateTaskDiv_htm(quiz_menu);
						if (prev_correct == '1') {
							quiz_cont.innerHTML = qmb;//'<form name=\'quest_form\'><\/form>'+qmb;
							jq_UpdateTaskDiv('preview_back');
						} else {
							quiz_cont.innerHTML = qmb;//'<form name=\'quest_form\'><\/form>'+qmb;
							jq_UpdateTaskDiv('preview_back');
						}
					break;
					<?php 
					}
					?>
					case 'no_attempts':
						quiz_blocked = 1;
						setTimeout("jq_releaseBlock()", 1000);
						var qmb = response.getElementsByTagName('quiz_message_box')[0].firstChild.data;
						var quiz_cont = getObj('jq_quiz_container');
						quiz_cont.innerHTML = qmb;//'<form name=\'quest_form\'><\/form>'+qmb;
						var quiz_menu = response.getElementsByTagName('quiz_menu')[0].firstChild.data;
						jq_UpdateTaskDiv_htm(quiz_menu);
						jq_UpdateTaskDiv('next_no_attempts');
					break;
					case 'email_results':
						quiz_blocked = 1;
						//setTimeout("jq_releaseBlock()", 1000);
						var email_msg = response.getElementsByTagName('email_msg')[0].firstChild.data;
						ShowMessage('error_messagebox', 1, email_msg);
					break;
					case 'time_is_up':
						quiz_blocked = 1;
						setTimeout("jq_releaseBlock()", 1000);
						var quiz_cont = getObj('jq_quiz_container');
						var children = quiz_cont.childNodes;
						for (var i = 0; i < children.length; i++) { quiz_cont.removeChild(quiz_cont.childNodes[i]); };
						var qmb = response.getElementsByTagName('quiz_message_box')[0].firstChild.data;
														
						stu_quiz_id = response.getElementsByTagName('stu_quiz_id')[0].firstChild.data;
						user_unique_id = response.getElementsByTagName('user_unique_id')[0].firstChild.data;
						
						quiz_cont.innerHTML = '';//'<form name=\'quest_form\'><\/form>';
						quiz_cont.innerHTML = qmb;//'<form name=\'quest_form\'><\/form>'+qmb;
						var quiz_menu = response.getElementsByTagName('quiz_menu')[0].firstChild.data;
						jq_UpdateTaskDiv_htm(quiz_menu);
						jq_UpdateTaskDiv('continue_finish');
					break;
					case 'finish':
						quiz_blocked = 1;

						quiz_progress = response.getElementsByTagName('progress_quests_done')[0].firstChild.data;
						<?php if($JLMS_CONFIG->get('quiz_progressbar', 0) == 1){ ?>
						progressbar.setProgress(quiz_progress);
						<?php } ?>

						setTimeout("jq_releaseBlock()", 1000);
						prev_correct = response.getElementsByTagName('quiz_prev_correct')[0].firstChild.data;
						var quiz_cont = getObj('jq_quiz_container');
						var children = quiz_cont.childNodes;
						for (var i = 0; i < children.length; i++) { quiz_cont.removeChild(quiz_cont.childNodes[i]); };
						quest_feedback = response.getElementsByTagName('quest_feedback')[0].firstChild.data;
						quiz_cont.innerHTML = '';//'<form name=\'quest_form\'><\/form>';
						if (quest_feedback == '1') {
							var qmb = response.getElementsByTagName('quiz_message_box')[0].firstChild.data;
							var quiz_menu = response.getElementsByTagName('quiz_menu')[0].firstChild.data;
							jq_UpdateTaskDiv_htm(quiz_menu);
							if (prev_correct == '1') {
							<?php if ($quiz->c_slide) { ?>
								getObj('quest_result_'+saved_prev_quest_id).innerHTML = "<img src='<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_accept.png' border=0>";
							<?php } ?>
								//stop_timer = 1;
								quiz_cont.innerHTML = qmb;//'<form name=\'quest_form\'><\/form>'+qmb;
								jq_UpdateTaskDiv('continue_finish');
							} else {
							<?php if ($quiz->c_slide) { ?>
								getObj('quest_result_'+saved_prev_quest_id).innerHTML = "<img src='<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_cancel.png' border=0>";
							<?php } ?>
								quiz_cont.innerHTML = qmb;//'<form name=\'quest_form\'><\/form>'+qmb;
								allow_attempt = response.getElementsByTagName('quiz_allow_attempt')[0].firstChild.data;
								if (allow_attempt == '1') {
									allow_attempt = 0;
									jq_UpdateTaskDiv('back_continue_finish');
								} else {
									allow_attempt = 0;
									//stop_timer = 1;
									jq_UpdateTaskDiv('continue_finish');
								}
							}
						} else {
							var qmb = '';
							var qfrf = response.getElementsByTagName('quest_feedback_repl_func')[0].firstChild.data;
							if (prev_correct == '1') {
							<?php if ($quiz->c_slide) { ?>
								getObj('quest_result_'+saved_prev_quest_id).innerHTML = "<img src='<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_accept.png' border=0>";
							<?php } ?>
							} else {
							<?php if ($quiz->c_slide) { ?>
								getObj('quest_result_'+saved_prev_quest_id).innerHTML = "<img src='<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_cancel.png' border=0>";
							<?php } ?>
							}
							eval(qfrf);
						}
					break;
					case 'results':
						quiz_blocked = 1;
						setTimeout("jq_releaseBlock()", 100);
						var quiz_cont = getObj('jq_quiz_container');
						var children = quiz_cont.childNodes;
						for (var i = 0; i < children.length; i++) { quiz_cont.removeChild(quiz_cont.childNodes[i]); };
						quiz_cont.innerHTML = '';//'<form name=\'quest_form\'><\/form>';
						stop_timer = 1;
						getObj('jq_time_tick_container').style.visibility = "hidden";
						jq_UpdateTaskDiv('finish');
						var finish_msg = response.getElementsByTagName('finish_msg')[0].firstChild.data;
						var quiz_results = response.getElementsByTagName('quiz_results')[0].firstChild.data;
						var quiz_footer = response.getElementsByTagName('quiz_footer')[0].firstChild.data;
						var quiz_cont = getObj('jq_quiz_container');
						quiz_cont.innerHTML = quiz_results+finish_msg+quiz_footer;//'<form name=\'quest_form\'><\/form>'+quiz_results+finish_msg+quiz_footer;
						saved_prev_res_data = quiz_results+finish_msg+quiz_footer;
					break;
					case 'failed':
						ShowMessage('error_messagebox', 1, mes_failed);
						quiz_blocked = 1;
						setTimeout("jq_releaseBlock()", 1000);
					break;
					default:
					break;
				}
			} else {
				ShowMessage('error_messagebox', 1, '<?php echo $jq_language['quiz_failed_request'];?>');
			}
		}
	}
	function jq_releaseBlock() { quiz_blocked = 0; }
	function jq_Start_TickTack() {
		timer_sec = 1;
		getObj('jq_time_tick_container').innerHTML = '00:01';
		getObj('jq_time_tick_container').style.visibility = "visible";
		setTimeout("jq_Continue_TickTack()", 1000);
	}
	function jq_Start_TickTackResume() {
		jq_ParseTickTackTimer(timer_sec);
		getObj('jq_time_tick_container').style.visibility = "visible";
		setTimeout("jq_Continue_TickTack()", 1000);
	}
	function jq_Continue_TickTack() {
		if (stop_timer == 1) {
			getObj('jq_time_tick_container').style.visibility = "hidden";
		} else {
			timer_sec ++;
			if ( max_quiz_time && (timer_sec > max_quiz_time) ) {
				getObj('jq_time_tick_container').innerHTML = mes_time_is_up;
			} else {
				jq_ParseTickTackTimer(timer_sec);
				setTimeout("jq_Continue_TickTack()", 1000);
			}
		}
	}
	function jq_ParseTickTackTimer(timer_sec) {
		var timer_hours = parseInt(timer_sec/3600);
		var timer_min = parseInt(timer_sec/60) - (timer_hours*60);
		var plus_sec = timer_sec - (timer_min*60) - (timer_hours*3600);
		if (timer_min < 0) { timer_min = timer_min*(-1); }
		if (plus_sec < 0) { plus_sec = plus_sec*(-1); }
		if (timer_hours < 0) { timer_hours = timer_hours*(-1); }
		var time_str_hours = '';
		if (timer_hours) {
			time_str_hours = timer_hours + '';
			if (time_str_hours.length == 1) time_str_hours = '0'+time_str_hours;
			time_str_hours = time_str_hours + ':';
		}
		var time_str = timer_min + '';
		if (time_str.length == 1) time_str = '0'+time_str;
		time_str2 = plus_sec + '';
		if (time_str2.length == 1) time_str2 = '0'+time_str2;
		getObj('jq_time_tick_container').innerHTML = time_str_hours + time_str + ':' + time_str2;
	}

		function jq_ResumeQuizOn(resume_id, unique_id, last_question) {
	<?php if($quiz->c_email_to == 2) { ?>
		/*var jq_email_cont = getObj('jq_user_email');
		var re_email = /[0-9a-z_]+@[0-9a-z_^.]+.[a-z]{2,3}/;
		if (!re_email.test(jq_email_cont.value)) {
			alert("Please enter a correct e-mail address");
			return;
		}
		user_email_to = jq_email_cont.value;*/
	<?php } ?>
		if (!quiz_blocked) {
			//ShowMessage('error_messagebox', 1, mes_loading);
			//jq_showLoading();//
			timerID = setTimeout("jq_ResumeQuiz("+resume_id+", '"+unique_id+"', "+last_question+")", 300);
		} else {
			ShowMessage('error_messagebox', 1, mes_please_wait);
		}
	}
	function jq_ResumeQuiz(resume_id, unique_id, last_question) { 
		jq_MakeRequest('&atask=resume_quiz&quiz=<?php echo $quiz->c_id;?>&resume_id='+resume_id+'&unique_id='+unique_id+'&last_question='+last_question,1); 
	}

	
	function jq_StartQuizOn() {
	<?php if($quiz->c_email_to == 2) { ?>
		/*var jq_email_cont = getObj('jq_user_email');
		var re_email = /[0-9a-z_]+@[0-9a-z_^.]+.[a-z]{2,3}/;
		if (!re_email.test(jq_email_cont.value)) {
			alert("Please enter a correct e-mail address");
			return;
		}
		user_email_to = jq_email_cont.value;*/
	<?php } ?>
		if (!quiz_blocked) {
			//ShowMessage('error_messagebox', 1, mes_loading);
			//jq_showLoading();//
			timerID = setTimeout("jq_StartQuiz()", 300);
		} else {
			ShowMessage('error_messagebox', 1, mes_please_wait);
		}
	}
	function jq_StartQuiz() { jq_MakeRequest('&atask=start&quiz=<?php echo $quiz->c_id;?>',1); }
	
	function jq_StartQuizOn_selfver() {
	<?php if($quiz->c_email_to == 2) { ?>
		/*var jq_email_cont = getObj('jq_user_email');
		var re_email = /[0-9a-z_]+@[0-9a-z_^.]+.[a-z]{2,3}/;
		if (!re_email.test(jq_email_cont.value)) {
			alert("Please enter a correct e-mail address");
			return;
		}
		user_email_to = jq_email_cont.value;*/
	<?php } ?>
		if (!quiz_blocked) {
			//ShowMessage('error_messagebox', 1, mes_loading);
			//jq_showLoading();//
			timerID = setTimeout("jq_StartQuiz_selfver()", 300);
		} else {
			ShowMessage('error_messagebox', 1, mes_please_wait);
		}
	}
	
	function implode( glue, pieces ) {
	    return ( ( pieces instanceof Array ) ? pieces.join ( glue ) : pieces );
	}
	
	function jq_StartQuiz_selfver(){ 
		var form = document.selfverForm;
		var string_params = '';
		
		if(form.c_pool_type.value == 1){
			string_params = '&mode_self=1&pool_num='+form.pool_qtype_number.value;
		} else if(form.c_pool_type.value == 2){
			if (form['pool_cat_id[]'].length) {
				var arr_cat_id = new Array(form['pool_cat_id[]'].length);
				var arr_cat_number = new Array(form['pool_cat_number[]'].length);
				for (var i = 0; i<form['pool_cat_number[]'].length; i++) {
						if (form['pool_cat_number[]'][i].value > 0) { 
							arr_cat_id[i] = form['pool_cat_id[]'][i].value;
							arr_cat_number[i] = form['pool_cat_number[]'][i].value; 
						} 
				}
				string_params = '&mode_self=2&cats_id='+implode(',', arr_cat_id)+'&pool_num='+implode(',', arr_cat_number);
			} else if (form['pool_cat_id[]'].value > 0) { 
				var arr_cat_id;
				var arr_cat_number;
				arr_cat_id = form['pool_cat_id[]'].value;
				arr_cat_number = form['pool_cat_number[]'].value;
				string_params = '&mode_self=3&cats_id='+arr_cat_id+'&pool_num='+arr_cat_number;
			} 
		}
		jq_MakeRequest('&atask=start&quiz=<?php echo $quiz->c_id;?>'+string_params,1);
	}
	
	
	function JQ_gotoQuestionOn(qid) {
		if (!quiz_blocked) {
			//ShowMessage('error_messagebox', 1, mes_loading);
			//jq_showLoading();//
			timerID = setTimeout("JQ_gotoQuestion("+qid+")", 300);
		} else {
			ShowMessage('error_messagebox', 1, mes_please_wait);
			setTimeout("jq_releaseBlock()", 1000);
		}
	}
	function JQ_gotoQuestion(qid){
		if(review){
			jq_MakeRequest('&atask=review_next&prev=1&quiz=<?php echo $quiz->c_id;?>&stu_quiz_id='+stu_quiz_id+'&quest_id='+qid, 1 ); 
		} else {
			jq_MakeRequest('&atask=goto_quest&quiz=<?php echo $quiz->c_id;?>&stu_quiz_id='+stu_quiz_id+'&seek_quest_id='+qid, 1 ); 
		}
	}
	function jq_emailResults() {
		if (!quiz_blocked) {
			ShowMessage('error_messagebox', 1, mes_loading);
			jq_MakeRequest('&atask=email_results&quiz=<?php echo $quiz->c_id;?>&stu_quiz_id='+stu_quiz_id<?php echo ($quiz->c_email_to == 2)?"+'&email_address='+user_email_to":''; ?>,0);
		} else {
			//ShowMessage('error_messagebox', 1, mes_please_wait);// setTimeout("jq_releaseBlock()", 1000);
		}
	}
	function jq_startReview() {
		if (!quiz_blocked) {
			jq_MakeRequest('&atask=review_start&quiz=<?php echo $quiz->c_id;?>&stu_quiz_id='+stu_quiz_id, 1);
		} else {
			ShowMessage('error_messagebox', 1, mes_please_wait); setTimeout("jq_releaseBlock()", 1000);
		}
	}
	function jq_QuizReviewNext() {
		if (!quiz_blocked) {
			jq_MakeRequest('&atask=review_next&quiz=<?php echo $quiz->c_id;?>'+'&stu_quiz_id='+stu_quiz_id+'&quest_id='+cur_quest_id, 1);
		} else {
			ShowMessage('error_messagebox', 1, mes_please_wait);
			setTimeout("jq_releaseBlock()", 1000);
		}
	}
	function jq_QuizReviewPrev() {
		if (!quiz_blocked) {
			jq_MakeRequest('&atask=review_next&prev=1&quiz=<?php echo $quiz->c_id;?>'+'&stu_quiz_id='+stu_quiz_id+'&quest_id='+prev_quest_id, 1);
		} else {
			ShowMessage('error_messagebox', 1, mes_please_wait);
			setTimeout("jq_releaseBlock()", 1000);
		}
	}
	function jq_Check_selectRadio(rad_name, form_name) {
		var tItem = eval('document.'+form_name);
		if (tItem) {
			var selItem = eval('document.'+form_name+'.'+rad_name);
			if (selItem) {
				if (selItem.length) { var i;
					for (i = 0; i<selItem.length; i++) {
						if (selItem[i].checked) {
							if (selItem[i].value > 0) { return selItem[i].value; } } }
				} else if (selItem.checked) { return selItem.value; } }
			return false; }
		return false;
	}
	function jq_Check_selectCheckbox(check_name, form_name) {
		selItem = eval('document.'+form_name+'.'+check_name);
		var rrr = '';
		if (selItem) {
			if (selItem.length) { var i;
				for (i = 0; i<selItem.length; i++) {
					if (selItem[i].checked) {
						if (selItem[i].value > 0) { rrr = rrr + selItem[i].value + ', '; }
					}}
				rrr = rrr.substring(0, rrr.length - 2);
			} else if (selItem.checked) { rrr = rrr + selItem.value; }}
		return rrr;
	}
	function jq_Check_valueItem(item_name, form_name) {
		selItem = eval('document.'+form_name+'.'+item_name);
		var rrr = '';
		if (selItem) {
			if (selItem.length) { var i;
				for (i = 0; i<selItem.length; i++) {
					if (selItem[i].value == '{0}') return '';
					rrr = rrr + selItem[i].value + '```';
				}
				rrr = rrr.substring(0, rrr.length - 3);
			} else { rrr = rrr + selItem.value;	}}
		return rrr;
	}
	function jq_QuizNextOn() { // Two steps CHECK (delete this func in the future)
		switch (cur_quest_type) {
			case '1': //Multi choice
			case '12':
				if (!jq_Check_selectRadio('quest_choice', 'quest_form') && !document.quest_form.ismandatory.value) {
					ShowMessage('error_messagebox', 1, mes_complete_this_part);
					return false;}
			break;
			case '2': //Multi Response
			case '13':
				var res = jq_Check_selectCheckbox('quest_choice', 'quest_form');
				if (res == '') {
					ShowMessage('error_messagebox', 1, mes_complete_this_part);
					return false;}
			break;
			case '3': //true-false
				if (!jq_Check_selectRadio('quest_choice', 'quest_form')) {
					ShowMessage('error_messagebox', 1, mes_complete_this_part);
					return false;}
			break;
			case '4': // Drag'AND'Drop
			case '11':
				var i_id; var i_value; var complete = true;
				for (i=0; i<kol_drag_elems; i++) {
					if ( (ids_in_cont[i] > 0) && (ids_in_cont[i] <= kol_drag_elems) ) {
						//alert(ids_in_cont[i] + ' - ' + cont_for_ids[ids_in_cont[i] - 1] + ' - ' + answ_ids[ids_in_cont[i]])
						if (cont_for_ids[ids_in_cont[i] - 1] == i+1) { ;
						} else { complete = false; }
					} else { complete = false; }
				}
				if (!complete) {
					ShowMessage('error_messagebox', 1, mes_complete_this_part);
					return false;
				}
			break;
			case '5': //drop-down
				var res = jq_Check_valueItem('quest_match', 'quest_form');
				if (res == '') {
					ShowMessage('error_messagebox', 1, mes_complete_this_part);
					return false;
				}
			break;
			case '6': //fill in the blank
				var blank_item = document.quest_form.quest_blank;
				var res = TRIM_str(blank_item.value);
				if (res == '' && !document.quest_form.ismandatory.value) {
					ShowMessage('error_messagebox', 1, mes_complete_this_part);
					return false;}
			break;
			case '7': //hotspot question
				var hs_x = parseInt(document.quest_form.hotspot_x.value);
				var hs_y = parseInt(document.quest_form.hotspot_y.value);
				if ((hs_x == 0) && (hs_y == 0)) {
					ShowMessage('error_messagebox', 1, mes_complete_this_part);
					return false;}
			break;
			case '8': //survey question
				var answer = document.quest_form.survey_box.value;
				if (TRIM_str(answer) == '' && !document.quest_form.ismandatory.value) {
					ShowMessage('error_messagebox', 1, mes_complete_this_part);
					return false;}
			break;
			case '10':
			break;
			/*case '11':
				var i_id; var i_value; var complete = true;
				for (i=0; i<kol_drag_elems; i++) {
					if ( (ids_in_cont[i] > 0) && (ids_in_cont[i] <= kol_drag_elems) ) {
						//alert(ids_in_cont[i] + ' - ' + cont_for_ids[ids_in_cont[i] - 1] + ' - ' + answ_ids[ids_in_cont[i]])
						if (cont_for_ids[ids_in_cont[i] - 1] == i+1) { ;
						} else { complete = false; }
					} else { complete = false; }
				}
				if (!complete) {
					ShowMessage('error_messagebox', 1, mes_complete_this_part);
					return false;}
			break;*/
		}
		if (!quiz_blocked) {
			//ShowMessage('error_messagebox', 1, mes_loading);
			//jq_showLoading();
			quiz_blocked = 1;
			timerID = setTimeout("jq_QuizNext()", 300);
		} else { ShowMessage('error_messagebox', 1, mes_please_wait); setTimeout("jq_releaseBlock()", 1000); }
	}

	function jq_QuizContinue() {

		cur_quest_type = response.getElementsByTagName('quest_type')[0].firstChild.data;
		saved_prev_quest_type = cur_quest_type;
		cur_quest_id = response.getElementsByTagName('quest_id')[0].firstChild.data;
		saved_prev_quest_id = cur_quest_id;
		cur_quest_score = response.getElementsByTagName('quest_score')[0].firstChild.data;
		saved_prev_quest_score = cur_quest_score;
		cur_quest_num = response.getElementsByTagName('quiz_quest_num')[0].firstChild.data;
		saved_prev_quest_num = cur_quest_num;
		skip_next_quest = response.getElementsByTagName('quiz_skip_next_quest')[0].firstChild ? response.getElementsByTagName('quiz_skip_next_quest')[0].firstChild.data : 0;
		var quiz_cont = getObj('jq_quiz_container');
		quiz_cont.innerHTML = '';
		if (cur_quest_type == 7) {
			var div_insidey=document.createElement("div");
			div_insidey.id = 'quest_div_hs';
			getObj('jq_quiz_container').appendChild(div_insidey);
		}
		var div_inside1=document.createElement("div");
		div_inside1.id = 'quest_div';
		div_inside1.innerHTML = response.getElementsByTagName('quest_data')[0].firstChild.data +response.getElementsByTagName('quest_data_user')[0].firstChild.data;
		saved_prev_quest_data = response.getElementsByTagName('quest_data')[0].firstChild.data + response.getElementsByTagName('quest_data_user')[0].firstChild.data;
		quiz_cont.appendChild(div_inside1);
<?php
	//Max: modign skip question
?>
		if(parseInt(skip_next_quest)){
	<?php
		$toolbar = array();
		$toolbar[] = array('btn_type' => 'quiz_ok', 'btn_js' => "javascript:jq_QuizNextOn(); void(0);");
		$toolbar[] = array('btn_type' => 'skip', 'btn_js' => "javascript:JQ_gotoQuestion(__skip__);void(0);");	
		if ($quiz->c_slide) {
			$toolbar[] = array('btn_type' => 'contents', 'btn_js' => "javascript:jq_ShowPanel();");
		}
		$m_str = JLMS_ShowToolbar($toolbar, false);
	?>
			var subject = '<?php echo str_replace('/','\/', addslashes($m_str));?>';
			html_replace = subject.split('__skip__').join(skip_next_quest);
			jq_UpdateTaskDiv_htm(html_replace);
		} else {
	<?php
		$toolbar = array();
		$toolbar[] = array('btn_type' => 'quiz_ok', 'btn_js' => "javascript:jq_QuizNextOn(); void(0);");
		if ($quiz->c_slide) {
			$toolbar[] = array('btn_type' => 'contents', 'btn_js' => "javascript:jq_ShowPanel();");
		}
		$m_str = JLMS_ShowToolbar($toolbar, false);
	?>			
			jq_UpdateTaskDiv_htm('<?php echo str_replace('/','\/', addslashes($m_str));?>');
		}

			jq_UpdateTaskDiv('next');
		
		var is_exec_quiz_script = response.getElementsByTagName('exec_quiz_script')[0].firstChild.data;
		saved_prev_quest_exec_quiz_script = is_exec_quiz_script;
		if (is_exec_quiz_script == 1 || is_exec_quiz_script == '1' ) {
			var v_quiz_script_data = response.getElementsByTagName('quiz_script_data')[0].firstChild.data;
			saved_prev_quest_exec_quiz_script_data = v_quiz_script_data;
			eval(v_quiz_script_data);
		}
	}
	function jq_QuizContinueFinish() {
		jq_MakeRequest('&atask=finish_stop&quiz=<?php echo $quiz->c_id;?>'+'&stu_quiz_id='+stu_quiz_id+'&user_unique_id='+user_unique_id, 1);
	}
	function jq_QuizBack() {
		cur_quest_id = saved_prev_quest_id;
		cur_quest_type = saved_prev_quest_type;
		cur_quest_score = saved_prev_quest_score;
		cur_quest_num = saved_prev_quest_num;
		var quiz_cont = getObj('jq_quiz_container');
		quiz_cont.innerHTML = '';
		if (cur_quest_type == 7) {
			var div_insidey=document.createElement("div");
			div_insidey.id = 'quest_div_hs';
			getObj('jq_quiz_container').appendChild(div_insidey);
		}
		var div_inside1=document.createElement("div");
		div_inside1.id = 'quest_div';
		div_inside1.innerHTML = saved_prev_quest_data;
		quiz_cont.appendChild(div_inside1);
<?php
	$toolbar = array();
	$toolbar[] = array('btn_type' => 'quiz_ok', 'btn_js' => "javascript:jq_QuizNextOn(); void(0);");
	if ($quiz->c_slide) {
		$toolbar[] = array('btn_type' => 'contents', 'btn_js' => "javascript:jq_ShowPanel();");
	}
	$m_str = JLMS_ShowToolbar($toolbar, false);
?>
		jq_UpdateTaskDiv_htm('<?php echo str_replace('/','\/',addslashes($m_str));?>');
		jq_UpdateTaskDiv('next');
		var is_exec_quiz_script = saved_prev_quest_exec_quiz_script;
		if (is_exec_quiz_script == 1 || is_exec_quiz_script == '1' ) {
			var v_quiz_script_data = saved_prev_quest_exec_quiz_script_data;
			eval(v_quiz_script_data);
		}
	}
	function URLencode(sStr) {
		return escape(sStr).replace(/\+/g, '%2B').replace(/\"/g,'%22').replace(/\'/g, '%27').replace(/\//g,'%2F');
	}
	function TRIM_str(sStr) {
		return (sStr.replace(/^[\s\xA0]+/, "").replace(/[\s\xA0]+$/, ""));
	}
	function jq_QuizNext() { //send 'TASK = next'
		<?php if ($is_preview) { ?>
		var jq_task = 'next_preview';
		<?php } else { ?>
		var jq_task = 'next';
		<?php } ?>
		switch (cur_quest_type) {
			case '1':
			case '12':
			var answer = jq_Check_selectRadio('quest_choice', 'quest_form');
				if (answer || document.quest_form.ismandatory.value != '0') {
					jq_MakeRequest('&atask=' + jq_task + '&quiz=<?php echo $quiz->c_id;?>'+'&stu_quiz_id='+stu_quiz_id+'&quest_id='+cur_quest_id+'&answer='+answer, 1);
				} else { ShowMessage('error_messagebox', 1, mes_complete_this_part); setTimeout("jq_releaseBlock()", 1000); return false; }
			break;
			case '3':
				var answer = jq_Check_selectRadio('quest_choice', 'quest_form');
				if (answer) {
					jq_MakeRequest('&atask=' + jq_task + '&quiz=<?php echo $quiz->c_id;?>'+'&stu_quiz_id='+stu_quiz_id+'&quest_id='+cur_quest_id+'&answer='+answer, 1);
				} else { ShowMessage('error_messagebox', 1, mes_complete_this_part); setTimeout("jq_releaseBlock()", 1000); return false; }
			break;
			case '2':
			case '13':
				var answer = jq_Check_selectCheckbox('quest_choice', 'quest_form');
				if (answer != '') {
					jq_MakeRequest('&atask=' + jq_task + '&quiz=<?php echo $quiz->c_id;?>'+'&stu_quiz_id='+stu_quiz_id+'&quest_id='+cur_quest_id+'&answer='+answer, 1);
				} else { ShowMessage('error_messagebox', 1, mes_complete_this_part); setTimeout("jq_releaseBlock()", 1000); return false; }
			break;
			case '4':
			case '11':
				var i_id;
				var i_value;
				var answer = '';
				var complete = true;
				var mas_ans = new Array(kol_drag_elems);
				for (i=0; i<kol_drag_elems; i++) {
					mas_ans[i] = 0;
					if ( (ids_in_cont[i] > 0) && (ids_in_cont[i] <= kol_drag_elems) ) {
						if (cont_for_ids[ids_in_cont[i] - 1] == i+1) {
							mas_ans[i] = ids_in_cont[i];
							answer = answer + answ_ids[ids_in_cont[i]] + '```';
						} else { complete = false; }
					} else { complete = false; }
				}
				if (!complete) {
					ShowMessage('error_messagebox', 1, mes_complete_this_part);
					setTimeout("jq_releaseBlock()", 1000);
					return false;
				} else {
					answer = answer.substring(0, answer.length - 3);
					answer = URLencode(answer);
					jq_MakeRequest('&atask=' + jq_task + '&quiz=<?php echo $quiz->c_id;?>'+'&stu_quiz_id='+stu_quiz_id+'&quest_id='+cur_quest_id+'&answer='+answer, 1);
				}
			break;
			case '5':
				var answer = jq_Check_valueItem('quest_match', 'quest_form');
				answer = URLencode(answer);
				if (answer != '') {
					jq_MakeRequest('&atask=' + jq_task + '&quiz=<?php echo $quiz->c_id;?>'+'&stu_quiz_id='+stu_quiz_id+'&quest_id='+cur_quest_id+'&answer='+answer, 1);
				} else { ShowMessage('error_messagebox', 1, mes_complete_this_part); setTimeout("jq_releaseBlock()", 1000); return false; }
			break;
			case '6':
				var blank_item = document.quest_form.quest_blank;
				var answer = URLencode(TRIM_str(blank_item.value));
				if (answer != '' || document.quest_form.ismandatory.value != '0') {
					jq_MakeRequest('&atask=' + jq_task + '&quiz=<?php echo $quiz->c_id;?>'+'&stu_quiz_id='+stu_quiz_id+'&quest_id='+cur_quest_id+'&answer='+answer, 1);
				} else { ShowMessage('error_messagebox', 1, mes_complete_this_part); setTimeout("jq_releaseBlock()", 1000); return false; }
			break;
			case '7':
				var hs_x = parseInt(document.quest_form.hotspot_x.value);
				var hs_y = parseInt(document.quest_form.hotspot_y.value);
				if ((hs_x != 0) && (hs_y != 0)) {
					var answer = hs_x + ',' + hs_y;
					jq_MakeRequest('&atask=' + jq_task + '&quiz=<?php echo $quiz->c_id;?>'+'&stu_quiz_id='+stu_quiz_id+'&quest_id='+cur_quest_id+'&answer='+answer, 1);
				} else { ShowMessage('error_messagebox', 1, mes_complete_this_part); setTimeout("jq_releaseBlock()", 1000); return false; }
			break;
			case '8':
				var answer = URLencode(TRIM_str(document.quest_form.survey_box.value));
				if (answer != '' || document.quest_form.ismandatory.value != '0') {
					jq_MakeRequest('&atask=' + jq_task + '&quiz=<?php echo $quiz->c_id;?>'+'&stu_quiz_id='+stu_quiz_id+'&quest_id='+cur_quest_id+'&answer='+answer, 1);
				} else { ShowMessage('error_messagebox', 1, mes_complete_this_part); setTimeout("jq_releaseBlock()", 1000); return false; }
			break;
			case '9':
				var complete = true;
				var scale_count = parseInt(document.quest_form.scale_count.value);
				var answer = new Array(scale_count);
				for(i=0;i<scale_count;i++)
				{
					var cur_answer = jq_Check_selectRadio('ch_scale_'+i, 'quest_form');
					if(!cur_answer)
						complete = false;
					else
						answer[i] = cur_answer;	
				}
				if (!complete && document.quest_form.ismandatory.value!='1') {
					ShowMessage('error_messagebox', 1, mes_complete_this_part);
					setTimeout("jq_releaseBlock()", 1000);
					return false;
				} else {
					
					jq_MakeRequest('&atask=' + jq_task + '&quiz=<?php echo $quiz->c_id;?>'+'&stu_quiz_id='+stu_quiz_id+'&quest_id='+cur_quest_id+'&answer='+answer, 1);
				}
			break;	
			case '10':
				jq_MakeRequest('&atask=' + jq_task + '&quiz=<?php echo $quiz->c_id; ?>'+'&stu_quiz_id='+stu_quiz_id+'&quest_id='+cur_quest_id+'&answer=0', 1);
			break;
			default:
				ShowMessage('error_messagebox', 1, '<?php echo $jq_language['quiz_unknown_error'];?>');
				setTimeout("jq_releaseBlock()", 1000);
			break;
		}
	}

	function jq_showLoading() {
		ShowMessage('error_messagebox', 0, '&nbsp;');

		getObj('jq_quiz_result_reviews').style.visibility = 'hidden';
		getObj('jq_quiz_explanation').style.visibility = 'hidden';
		getObj('jq_quiz_result_reviews').style.display = 'none';
		getObj('jq_quiz_explanation').style.display = 'none';
		getObj('jq_quiz_container').innerHTML = '<br /><br /><center><img src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/loading.gif" height="32" width="32" border="0" alt="loading" /><\/center>';
	}

	function jq_UpdateTaskDiv_htm(htm_txt) {
		getObj('jq_quiz_task_container').innerHTML = htm_txt;
	}

	function jq_UpdateTaskDiv(task) {
		switch (task) {
			case 'start':
				getObj('jq_quiz_task_container').innerHTML = jq_StartButton('jq_StartQuizOn()', '<?php echo $jq_language['quiz_start'];?>');
			break;
			case 'resume':
			case 'next':
				//getObj('jq_quiz_task_container').innerHTML = jq_NextButton('jq_QuizNextOn()', '<?php echo $jq_language['quiz_next'];?>');
				getObj('jq_quest_num_container').innerHTML = mes_quest_number.replace("{X}", cur_quest_num).replace("{Y}", quiz_count_quests);
				getObj('jq_quest_num_container').style.visibility = "visible";
				getObj('jq_points_container').innerHTML = mes_quest_points.replace("{X}", cur_quest_score);
				getObj('jq_points_container').style.visibility = "visible";
				getObj('jq_question_id_container').style.visibility = "hidden";

				<?php if($JLMS_CONFIG->get('quizzes_show_quest_id', 0) == 1){?>

					quest_id_gqp = response.getElementsByTagName('quest_id_gqp')[0].firstChild.data;				
					quest_id_pool = response.getElementsByTagName('quest_id_pool')[0].firstChild.data;	

					if(quest_id_gqp > 0) {
						getObj('jq_question_id_container').innerHTML = "<?php echo $JLMS_CONFIG->get('quizzes_quest_id_title', 0);?>"+quest_id_gqp;
					}
					if(quest_id_pool > 0) {
						getObj('jq_question_id_container').innerHTML = "<?php echo $JLMS_CONFIG->get('quizzes_quest_id_title', 0);?>"+quest_id_pool;
					}

					if(quest_id_gqp > 0 || quest_id_pool > 0) {
						getObj('jq_question_id_container').style.display = "block";
						getObj('jq_question_id_container').style.visibility = "visible";
					}
					else {
						getObj('jq_question_id_container').style.display = "none";
						getObj('jq_question_id_container').style.visibility = "hidden";
					}

				<?php } ?>
				<?php if($JLMS_CONFIG->get('quiz_progressbar', 0) == 1){?>
				getObj('progress_bar').style.display = "block";
				<?php } ?>
			break;
			case 'review_next':
				//getObj('jq_quiz_task_container').innerHTML = jq_ContinueButton('jq_QuizReviewNext()', '<?php echo $jq_language['quiz_next'];?>');
				getObj('jq_quest_num_container').innerHTML = mes_quest_number.replace("{X}", cur_quest_num).replace("{Y}", quiz_count_quests);
				getObj('jq_quest_num_container').style.visibility = "visible";
				getObj('jq_points_container').innerHTML = mes_quest_points.replace("{X}", cur_quest_score);
				getObj('jq_points_container').style.visibility = "visible";
			break;
			case 'next_no_attempts':
				//getObj('jq_quiz_task_container').innerHTML = jq_ContinueButton('jq_QuizContinue()', '<?php echo $jq_language['quiz_continue'];?>');
			break;
			case 'finish':
				<?php if($JLMS_CONFIG->get('quiz_progressbar', 0) == 1){?>
				getObj('progress_bar').style.display = "none";
				if (getObj('jq_question_id_container')) { getObj('jq_question_id_container').style.visibility = "hidden"; }
				<?php } ?>
			case 'clear':
				getObj('jq_quiz_task_container').innerHTML = "";
				getObj('jq_quest_num_container').style.visibility = "hidden";
				getObj('jq_points_container').style.visibility = "hidden";
				getObj('jq_question_id_container').style.visibility = "hidden";
			break;
			case 'continue':
			break;
			case 'continue_finish':
			break;
			case 'back_continue':
				//getObj('jq_quiz_task_container').innerHTML = jq_ContinueButton('jq_QuizContinue()', '<?php echo $jq_language['quiz_continue'];?>')+jq_BackButton('jq_QuizBack()', '<?php echo $jq_language['quiz_back'];?>');
			break;
			case 'back_continue_finish':
				//getObj('jq_quiz_task_container').innerHTML = jq_ContinueButton('jq_QuizContinueFinish()', '<?php echo $jq_language['quiz_continue'];?>')+jq_BackButton('jq_QuizBack()', '<?php echo $jq_language['quiz_back'];?>');
			break;
			<?php if ($is_preview) { ?>
			case 'preview_back':
				//getObj('jq_quiz_task_container').innerHTML = jq_BackButton('JQ_previewQuest()', '<?php echo $jq_language['quiz_back'];?>');
			break;
			<?php } ?>
		}
		<?php if ($quiz->c_slide) { ?>
		if (result_is_shown == 1) { jq_ShowPanel(); }
		<?php } ?>
		if (task == 'finish') {
			//var obj_plc = getObj('jq_panel_link_container');
			//if (obj_plc) obj_plc.style.visibility = 'hidden';
		}
	}
	function jq_NextButton(task, text) {
		return "<div id=\"jq_next_link_container\" onclick=\""+task+"\"><div class=\"back_button\" id=\"jq_quiz_task_link_container\"><a href=\"javascript: void(0)\">"+text+"<\/a><\/div><br /><\/div>";
	}
	function jq_ContinueButton(task, text) {
		return "<div id=\"jq_continue_link_container\" onclick=\""+task+"\"><div class=\"back_button\" id=\"jq_quiz_task_link_container\"><a href=\"javascript: void(0)\">"+text+"<\/a><\/div><br /><\/div>";
	}
	function jq_StartButton(task, text) {
		return "<div id=\"jq_start_link_container\" onclick=\""+task+"\"><div class=\"back_button\" id=\"jq_quiz_task_link_container\"><a href=\"javascript: void(0)\">"+text+"<\/a><\/div><br /><\/div>";
	}
	function jq_BackButton(task, text) {
		return "<div id=\"jq_back_link_container\" onclick=\""+task+"\"><div class=\"back_button\" id=\"jq_quiz_task_link_container\"><a href=\"javascript: void(0)\">"+text+"<\/a><\/div><br /><\/div>";
	}
	function jq_ShowPanel_go() {
		var jq_quiz_c_cont = getObj('jq_quiz_container');
		if (jq_quiz_c_cont) { jq_quiz_c_cont.style.visibility = 'hidden'; jq_quiz_c_cont.style.display = 'none';}
		var jq_quiz_r_c = getObj('jq_quiz_result_container');
		if (jq_quiz_r_c) { jq_quiz_r_c.style.visibility = 'visible'; jq_quiz_r_c.style.display = 'block';}
	}
	function jq_HidePanel_go() {
		var jq_quiz_r_c = getObj('jq_quiz_result_container');
		if (jq_quiz_r_c) { jq_quiz_r_c.style.visibility = 'hidden'; jq_quiz_r_c.style.display = 'none';}
		var jq_quiz_c_cont = getObj('jq_quiz_container');
		if (jq_quiz_c_cont) { jq_quiz_c_cont.style.visibility = 'visible'; jq_quiz_c_cont.style.display = 'block';}
	}
	function jq_ShowPanel() {
<?php if ($quiz->c_slide) { ?>	
		if (result_is_shown == 1) { jq_HidePanel_go(); result_is_shown = 0;	}
		else { jq_ShowPanel_go();	result_is_shown = 1; }
<?php } ?>
	}
<?php if ($is_preview) { ?>
	function JQ_previewQuest() {
		jq_MakeRequest('&atask=preview_quest&quiz=<?php echo $quiz->c_id;?>'+'&preview_id=<?php echo $preview_id;?>&quest_id=<?php echo $preview_quest;?>', 1);
	}
<?php } ?>

//--><!]]>
</script>
<div>
	<?php $quiz->template_name = 'joomlaquiz_lms_template';
		if ($quiz->template_name){
			require(dirname(__FILE__) . '/templates/'.$quiz->template_name.'/jq_template.php');
			
			//$url_link = 'index.php?option='.$option.'&task=quiz_action&id='.$course_id.'&atask=start&quiz='.$quiz->c_id.'&Itemid='.$Itemid.'';
			//no-js functionality commented in JoomlaLMS 1.1.0 (due to the lost of bugs and lack of usage)
			$url_link = 'javascript:void(0);';
			$page = strval(mosGetParam( $_REQUEST, 'page', '' ));
			
			$task_cont = '';
			if($page != 'view_preview'){
			$task_cont .= "
			<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" align=\"right\" style=\"text-align: right;\" class=\"jlms_table_no_borders\">
				<tbody>
					<tr>
						<td>";
			if($self_verification != ''){
				$task_cont .= "<input type='image' src='".$JLMS_CONFIG->get('live_site')."/components/com_joomla_lms/lms_images/buttons/btn_start.png' name='atask' value='start'/>";
				$task_cont .= "<input type='hidden' name='task' value='quiz_action'/>";
				$task_cont .= "<input type='hidden' name='id' value='".$course_id."'/>";
				$task_cont .= "<input type='hidden' name='quiz' value='".$quiz->c_id."'/>";
				$task_cont .= "<input type='hidden' name='atask' value='start'/>";
			} else { 
			$task_cont .= "<a style=\"cursor: pointer;\" href=\"".sefRelToAbs($url_link)."\">
								<img width=\"32\" height=\"32\" border=\"0\" title=\""._JLMS_START_ALT_TITLE."\" alt=\""._JLMS_START_ALT_TITLE."\" src=\"".$JLMS_CONFIG->get('live_site')."/components/com_joomla_lms/lms_images/buttons/btn_start.png\" class=\"JLMS_png\"/>
							</a>";
			}
			$task_cont .= "</td>
						<td valign=\"middle\" style=\"vertical-align: middle;\">";
			if($self_verification != ''){
				$task_cont .= "&nbsp;"._JLMS_START_ALT_TITLE."&nbsp;";
			} else {
				$task_cont .= "<a style=\"cursor: pointer;\" href=\"".sefRelToAbs($url_link)."\">&nbsp;"._JLMS_START_ALT_TITLE."&nbsp;</a>";	
			}
			$task_cont .= "</td>
					</tr>
				</tbody>
			</table>";
			}
			
			$query = "SELECT a.*, 'joomlaquiz_lms_template' as template_name FROM #__lms_quiz_t_quiz as a WHERE a.c_id = ".$quiz->c_id." and a.course_id = ".$course_id;
			$JLMS_DB->SetQuery($query);
			$quiz_params = $JLMS_DB->LoadObjectList();
			$descr_cont = isset($quiz_params[0]->c_description) ? $quiz_params[0]->c_description : '';
			
			$descr_cont = JLMS_ShowText_WithFeatures($descr_cont);
			
			$progress_bar_js = true; //by Max
			if($preview_quest){
				$progress_bar_js = false;	
			}
			
			echo JoomlaQuiz_template_class::JQ_MainScreen($descr_cont, $task_cont, $self_verification, $progress_bar_js);
			if ($is_preview) {?>
			<script language="JavaScript" type="text/javascript">
			<!--//--><![CDATA[//><!--
				var jq_quiz_c_t = getObj('jq_quiz_container_title');
				if (jq_quiz_c_t) jq_quiz_c_t.innerHTML = "<?php echo addslashes($quiz->c_title);?>";
				var jq_quiz_c_d = getObj('jq_quiz_container_description');
				if (jq_quiz_c_d) jq_quiz_c_d.innerHTML = "<?php echo "Click <a href='javascript:void(0)' onclick='JQ_previewQuest();'>here<\/a> to preview the question"?>";
			//--><!]]>
			</script>
			</div>
			<?php
			} else {
				$quiz_params = new JLMSParameters($quiz->params);
				
				if($quiz_params->get('sh_self_verification', 0) == 1){
					$toolbar = array();
					
					if( ($quiz->attempts_of_this_quiz < $quiz->c_max_numb_attempts) || $quiz->c_max_numb_attempts == 0) 					
						$toolbar[] = array('btn_type' => 'start', 'btn_js' => "javascript:jq_StartQuizOn_selfver(); return false;");
				} else {
					$toolbar = array();
					
					if( ($quiz->attempts_of_this_quiz < $quiz->c_max_numb_attempts) || $quiz->c_max_numb_attempts == 0) 
						$toolbar[] = array('btn_type' => 'start', 'btn_js' => "javascript:jq_StartQuizOn(); return false;");
				}
				
				//if(isset($quiz->resume_quiz) && $quiz->resume_quiz && $quiz->last_question) 
				if(isset($quiz->resume_quiz) && $quiz->resume_quiz && !$quiz->c_total_time) 
						$toolbar[] = array('btn_type' => 'resume', 'btn_js' => "javascript:jq_ResumeQuizOn(".$quiz->resume_quiz.",'".$quiz->unique_id."', ".$quiz->last_question."); return false;");
				
				$m_str = JLMS_ShowToolbar($toolbar, false);
				
//				$m_str = addslashes($m_str);
//				$m_str = addslashes($m_str);
				$domready2 = '
				jq_UpdateTaskDiv_htm("'.str_replace('/','\/', addslashes($m_str)).'");
				var jq_quiz_c_t = getObj(\'jq_quiz_container_title\');
				if (jq_quiz_c_t) jq_quiz_c_t.innerHTML = "'.addslashes($quiz->c_title).'";
				var jq_quiz_c_d = getObj(\'jq_quiz_container_description\');
//				if (jq_quiz_c_d) jq_quiz_c_d.innerHTML = "'.(str_replace( "\n", '',str_replace( "\r", '', str_replace("/", "\/", addslashes($quiz->c_description))))).'";
				var jq_qiuz_c_selfver = getObj(\'jq_quiz_container_selfver\');
				if (jq_qiuz_c_selfver) jq_qiuz_c_selfver.innerHTML = "'.(str_replace( "\n", '',str_replace( "\r", '', str_replace("/", "\/", addslashes($self_verification))))).'";
				';
				$JLMS_CONFIG->set('web20_domready_code', $JLMS_CONFIG->get('web20_domready_code','').$domready2);
?>

			</div>
<?php		}
		//TODO: replace getObj with mootools $ operand.... !!!!!! NOTE !!!!!!!!!!!! we should update DOM using $ only after ondomready !!!
		}
	}

	function show_toolbar(){
		
	}
} ?>