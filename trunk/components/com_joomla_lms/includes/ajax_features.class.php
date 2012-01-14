<?php

defined( '_JLMS_EXEC' ) or die( 'Direct Access to this location is not allowed.' );
class JLMS_Ajax_Features extends JLMSObject {
	var $quiz_id;
	var $c_slide;
	var $c_generated_panel = false;
	var $c_slide_update;
	function __construct($quiz_id = 0) {
		$this->quiz_id = $quiz_id;
	}
	function GetFunc_RFE($is_echo = false) {
		$ret_str = "function jlms_RFE(response,elem_name) {"
		. "\n\t return response.getElementsByTagName(''+elem_name)[0].firstChild ? response.getElementsByTagName(''+elem_name)[0].firstChild.data : 0"
		. "}";
		if ($is_echo) { echo $ret_str; }
		else return $ret_str;
	}
	function GetInclude_Msgs($is_echo = false) {
		global $JLMS_CONFIG;
		$ret_str = '<script language="JavaScript" src="'.$JLMS_CONFIG->getCfg('live_site').'/components/com_joomla_lms/includes/quiz/bits_message.js" type="text/javascript"></script>
		<script language="JavaScript" src="'.$JLMS_CONFIG->getCfg('live_site').'/components/com_joomla_lms/includes/quiz/progressbar.js" type="text/javascript"></script>';
		if ($is_echo) { echo $ret_str; }
		else return $ret_str;
	}
	function JS_open($is_echo = false, $hard = false) {
		$ret_str = '<script language="javascript" type="text/javascript">'
		. "\n\t <!--".($hard?("//--><![CDATA[//><!--"."\n"):"\n");
		if ($is_echo) { echo $ret_str; }
		else return $ret_str;
	}
	function JS_close($is_echo = false, $hard = false) {
		$ret_str = "\n\t" . '//-->'.($hard?("<!]]>"."\n"):"\n" ). '</script>';
		if ($is_echo) { echo $ret_str; }
		else return $ret_str;
	}
	function GetFunc_JS_in_array($is_echo = false) {
		$ret_str = "function js_in_array(n, ha){for(h in ha){if(ha[h]==n){return true;}}return false;}";
		if ($is_echo) { echo $ret_str; }
		else return $ret_str;
	}
	//warning dolgno byt': replace('/\"/g, "%22") (ono moglo isporticca)
	function GetFunc_JS_URLencode($is_echo = false) {
		$ret_str = "function URLencode(sStr) { return escape(sStr).replace(/\+/g, '%2B').replace(/\\\"/g,'%22').replace(/\'/g, '%27').replace(/\//g,'%2F'); }";
		if ($is_echo) { echo $ret_str; }
		else return $ret_str;
	}
	function GetFunc_JS_TRIM_str($is_echo = false) {
		$ret_str = "function TRIM_str(sStr) { return (sStr.replace(/^[\s\xA0]+/, '').replace(/[\s\xA0]+$/, '')); }";
		if ($is_echo) { echo $ret_str; }
		else return $ret_str;
	}

	//*** QUIZ FrameWork ***//
	function QUIZ_JS_DrDr_Code($is_echo = false) {
		global $JLMS_CONFIG;
		
		$doc = & JFactory::getDocument();
		if( JLMS_mootools12() ) 
		{
			$doc->addScript( $JLMS_CONFIG->get('live_site').'/components/com_joomla_lms/includes/quiz/dragdrop_1.3.js' );					
		} else {
			$doc->addScript( $JLMS_CONFIG->get('live_site').'/components/com_joomla_lms/includes/quiz/dragdrop_1.12.js' );	
		}
		
		$ret_str = "// *** DRAG'and'DROP CODE *** //
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
	/*drag&drop mootools realised*/
// *** end of DRAG'and'DROP CODE *** //";
		if ($is_echo) { echo $ret_str; }
		else return $ret_str;
	}
	function QUIZ_preloadMsgs($is_echo = false, &$jq_language) {
		$ret_str = "\n"."var mes_complete_this_part = '".$jq_language['mes_complete_this_part']."';
var mes_loading = '".$jq_language['quiz_load_data']."';
var mes_failed = '".$jq_language['quiz_failed']."';
var mes_please_wait = '".$jq_language['mes_please_wait']."';
var mes_time_is_up = '".$jq_language['quiz_mes_timeout']."';
var mes_quest_number = '".$jq_language['quiz_question_number']."';
var mes_quest_points = '".$jq_language['quiz_question_points']."';
var mes_quiz_unknown_er = '".$jq_language['quiz_unknown_error']."';
var mes_quiz_start = '".$jq_language['quiz_start']."';
var mes_quiz_next = '".$jq_language['quiz_next']."';
var mes_quiz_back = '".$jq_language['quiz_back']."';
var mes_quiz_failed_request = '".$jq_language['quiz_failed_request']."';
var mes_quiz_continue = '".$jq_language['quiz_continue']."';";
		if ($is_echo) { echo $ret_str; }
		else return $ret_str;
	}
	function QUIZ_doInitialize($is_echo = false, $url_prefix = '', $live_site = '', $c_time_limit = 0, $stu_quiz_id = 0, $user_unique_id = '') {
		$ret_str = "var stu_quiz_id = ".$stu_quiz_id.";
		var quiz_id = ".$this->quiz_id.";
		var quiz_progress = 0;
		var user_email_to = '';
		var user_unique_id = '".$user_unique_id."';
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
		var timer_sec = 0;
		var stop_timer = 0;
		var result_is_shown = 0;
		var ticktack_hand = 555;
		var max_quiz_time = ".$c_time_limit.";
		var progressbar;
		var quiz_blocked = 0; // set block after each question (release after 2 seconds).
		var url_prefix = '".$url_prefix."';
		var mCfg_live_site = '';
		var review = 0;
		";

		if ($is_echo) { 
			echo $ret_str; 
		} else {
			return $ret_str;
		}
	}
	function QUIZ_MakeRequest($is_echo = false) {
		$ret_str = "function jq_MakeRequest(url, do_clear) {
	//if (do_clear == 1) jq_UpdateTaskDiv('clear');
	var http_request = false;
	if (window.ActiveXObject) { // IE
		try { http_request = new ActiveXObject(\"Msxml2.XMLHTTP\");
		} catch (e) {
			try { http_request = new ActiveXObject(\"Microsoft.XMLHTTP\");
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
		//getObj('jq_quiz_container').innerHTML = '';
	}
	quiz_blocked == 1;
	http_request.onreadystatechange = function() { jq_AnalizeRequest(http_request); };
	var url_prefix2 = '&user_unique_id=' + user_unique_id;
	http_request.open('GET', mCfg_live_site + url_prefix + url_prefix2 + url, true);
	http_request.send(null);
}";
		if ($is_echo) { echo $ret_str; }
		else return $ret_str;
	}
	function QUIZ_AR_begin($ar_func_name = 'jq_AnalizeRequest') {
		$ret_str = "function ".$ar_func_name."(http_request) {
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
			
			switch (task) {";
		return $ret_str;
	}
	function QUIZ_AR_task_start() {
		global $JLMS_CONFIG;
		$ret_str = "	case 'start':
		quiz_blocked = 1;
		setTimeout(\"jq_releaseBlock()\", 1000);
		user_unique_id = jlms_RFE(response, 'user_unique_id');
		stu_quiz_id = jlms_RFE(response, 'stu_quiz_id');
		cur_quest_type = jlms_RFE(response, 'quest_type');
		saved_prev_quest_type = cur_quest_type;
		cur_quest_id = jlms_RFE(response, 'quest_id');
		saved_prev_quest_id = cur_quest_id;
		cur_quest_score = jlms_RFE(response, 'quest_score');
		saved_prev_quest_score = cur_quest_score;
		quiz_count_quests = jlms_RFE(response, 'quiz_count_quests');
		cur_quest_num = jlms_RFE(response, 'quiz_quest_num');
		saved_prev_quest_num = cur_quest_num;
		getObj('jq_quiz_container').innerHTML = '';
		if (cur_quest_type == 7) {
			var div_insidey=document.createElement(\"div\");
			div_insidey.id = 'quest_div_hs';
			getObj('jq_quiz_container').appendChild(div_insidey);
		}
		var div_inside1=document.createElement(\"div\");
		div_inside1.id = 'quest_div';
			div_inside1.innerHTML = jlms_RFE(response, 'quest_data') + jlms_RFE(response, 'quest_data_user');
			saved_prev_quest_data = jlms_RFE(response, 'quest_data') + jlms_RFE(response, 'quest_data_user');
			getObj('jq_quiz_container').appendChild(div_inside1);
		var quiz_menu = jlms_RFE(response, 'quiz_menu');
		jq_UpdateTaskDiv_htm(quiz_menu);
		jq_UpdateTaskDiv('next');
		var is_exec_quiz_script = jlms_RFE(response, 'exec_quiz_script');
		saved_prev_quest_exec_quiz_script = is_exec_quiz_script;
		if (is_exec_quiz_script == 1 || is_exec_quiz_script == '1' ) {
			var v_quiz_script_data = jlms_RFE(response, 'quiz_script_data');
			saved_prev_quest_exec_quiz_script_data = v_quiz_script_data;
			eval(v_quiz_script_data);
		}
		clearTimeout(ticktack_hand);
		stop_timer = 0;
		jq_Start_TickTack();";
		if ($this->c_generated_panel) {
			$ret_str .= "
		//qpd_gen = jlms_RFE(response, 'quiz_panel_data_gen');
		var tr_tmp_cur_step = getObj('tree_row_'+stu_step_id);
		if (tr_tmp_cur_step) {
			var tr_tmp_cur_table = tr_tmp_cur_step.parentNode;
			var sec_indx = tr_tmp_cur_step.sectionRowIndex;
			var sd = sec_indx + 1;
			doing_del = true;
			while (doing_del == true) {
				if (sd < tr_tmp_cur_table.rows.length) {
					var tr_id = tr_tmp_cur_table.rows[sd].id;
					if (tr_id.substring(0, 13) == 'tree_row_quiz' ) {
							tr_tmp_cur_table.deleteRow(sd);
					} else {
						sd ++;
						doing_del = false;
					}
				} else { doing_del = false; }
			}
			sec_indx ++;
			var td_count = parseInt(jlms_RFE(response, 'prev_tds_count'));
			var qqq_count = parseInt(jlms_RFE(response, 'quest_count_c_gen'));
			var qqq_quest_colspan = parseInt(jlms_RFE(response, 'quest_colspan'));
			cn_ind = 1;
			for (sd = 0; sd<qqq_count; sd++) {
				var row = tr_tmp_cur_table.insertRow(sec_indx);
				rq_id = jlms_RFE(response, 'question_'+(sd+1)+'_id');
				rq_points = jlms_RFE(response, 'question_'+(sd+1)+'_points');
				row.className = 'sectiontableentry'+cn_ind;
				row.id = 'tree_row_quiz'+cn_ind;
				cell = document.createElement('td');cell.innerHTML = '&nbsp;';row.appendChild(cell);
				cell = document.createElement('td');cell.innerHTML = '&nbsp;';row.appendChild(cell);
				for (gh = 0; gh<td_count; gh++) {
					cell = document.createElement('td');
					cell.innerHTML = jlms_RFE(response, 'prev_td_'+(gh+1)); 
					row.appendChild(cell);
				}
				cell = document.createElement('td');
				var td_i_suff = '1';
				if (sd == (qqq_count - 1)) { td_i_suff = '2';}
				cell.innerHTML = '<span align=\"center\"><img src=\"".$JLMS_CONFIG->getCfg('live_site')."/components\/com_joomla_lms\/lms_images\/treeview\/sub'+td_i_suff+'.png\" width=\"16\" height=\"16\" border=\"0\" alt=\"sub\" \/><\/span>';
				row.appendChild(cell);
				cell = document.createElement('td');

				var link_code = 'javascript:jlms_gotoQuestion(\"'+rq_id+'\");';
				var trd_html = '<div style=\"float:left\"><a href=\''+link_code+'\'>'+jlms_RFE(response, 'question_'+(sd+1)+'_text')+'<\/a><\/div><div style=\"float:right; width:40px\">'+rq_points+'<\/div>';
				trd_html = trd_html+ '<div style=\"float:right; width:25px\" id=\"quest_result_'+rq_id+'\">-<\/div>';
				if (qqq_quest_colspan && qqq_quest_colspan > 1) { cell.colSpan = qqq_quest_colspan; }
				cell.innerHTML = trd_html;//jlms_RFE(response, 'question_'+(sd+1)+'_text'); 
				row.appendChild(cell);
				sec_indx++;
				cn_ind = 3 - cn_ind;
			}
		}
		";
		} elseif ($this->c_slide) {
			$ret_str .= "\n" . "getObj('jq_quiz_result_container').innerHTML = jlms_RFE(response, 'quiz_panel_data');";
		}
		$ret_str .= "\n" . "	break;";

				$ret_str .= "case 'resume':
						user_unique_id = response.getElementsByTagName('user_unique_id')[0].firstChild.data;
						stu_quiz_id = response.getElementsByTagName('stu_quiz_id')[0].firstChild.data;

						cur_quest_id = response.getElementsByTagName('quest_id')[0].firstChild.data;
						quiz_count_quests = response.getElementsByTagName('quiz_count_quests')[0].firstChild.data;
						saved_prev_quest_id = cur_quest_id;

						cur_quest_num = jlms_RFE(response, 'quiz_quest_num');
						quiz_progress = jlms_RFE(response, 'progress_quests_done');";
				if($JLMS_CONFIG->get('quiz_progressbar', 0) == 1){
				$ret_str .= "		
						if(progressbar != undefined){
							progressbar.setProgress(quiz_progress);
						}
						";
				}
				$ret_str .= "		
						getObj('jq_quiz_result_container').innerHTML = response.getElementsByTagName('quiz_panel_data')[0].firstChild.data;
						jq_QuizContinue();
						clearTimeout(ticktack_hand);
						stop_timer = 0;
						jq_Start_TickTackResume();

					break;";

		return $ret_str;
	}
	function QUIZ_AR_task_seek_quest() {
		global $JLMS_CONFIG;
		$ret_str = "	case 'seek_quest':
		quiz_blocked = 1;
		setTimeout(\"jq_releaseBlock()\", 1000);
		cur_quest_type = jlms_RFE(response, 'quest_type');
		saved_prev_quest_type = cur_quest_type;
		cur_quest_id = jlms_RFE(response, 'quest_id');
		saved_prev_quest_id = cur_quest_id;
		cur_quest_score = jlms_RFE(response, 'quest_score');
		saved_prev_quest_score = cur_quest_score;
		cur_quest_num = jlms_RFE(response, 'quiz_quest_num');
		saved_prev_quest_num = cur_quest_num;
		getObj('jq_quiz_container').innerHTML = '';
		if (cur_quest_type == 7) {
			var div_insidey=document.createElement(\"div\");
			div_insidey.id = 'quest_div_hs';
			getObj('jq_quiz_container').appendChild(div_insidey);
		}
		var div_inside1=document.createElement(\"div\");
		div_inside1.id = 'quest_div';
		div_inside1.innerHTML = jlms_RFE(response, 'quest_data') + jlms_RFE(response, 'quest_data_user');
		saved_prev_quest_data = jlms_RFE(response, 'quest_data') + jlms_RFE(response, 'quest_data_user');
		getObj('jq_quiz_container').appendChild(div_inside1);
		var quiz_menu = jlms_RFE(response, 'quiz_menu');
		jq_UpdateTaskDiv_htm(quiz_menu);
		jq_UpdateTaskDiv('next');
		quiz_progress = jlms_RFE(response, 'progress_quests_done');";
		if($JLMS_CONFIG->get('quiz_progressbar', 0) == 1){
		$ret_str .= "		
			if (progressbar != undefined) { progressbar.setProgress(quiz_progress); }";
		}
		$ret_str .= "	
		var is_exec_quiz_script = jlms_RFE(response, 'exec_quiz_script');
		saved_prev_quest_exec_quiz_script = is_exec_quiz_script;
		if (is_exec_quiz_script == 1 || is_exec_quiz_script == '1' ) {
			var v_quiz_script_data = jlms_RFE(response, 'quiz_script_data');
			saved_prev_quest_exec_quiz_script_data = v_quiz_script_data;
			eval(v_quiz_script_data);
		}
	break;";
		return $ret_str;
	}
	function QUIZ_AR_task_review_start() {
		$ret_str = "	
		case 'review_start':
			quiz_blocked = 1;
			review = 1;
			setTimeout(\"jq_releaseBlock()\", 1000);
			cur_quest_type = jlms_RFE(response, 'quest_type');
			saved_prev_quest_type = cur_quest_type;
			cur_quest_id = jlms_RFE(response, 'quest_id');
			saved_prev_quest_id = cur_quest_id;
			cur_quest_score = jlms_RFE(response, 'quest_score');
			saved_prev_quest_score = cur_quest_score;
			quiz_count_quests = jlms_RFE(response, 'quiz_count_quests');
			cur_quest_num = jlms_RFE(response, 'quiz_quest_num');
			saved_prev_quest_num = cur_quest_num;
			getObj('jq_quiz_container').innerHTML = '';
			var div_inside1=document.createElement(\"div\");
			div_inside1.id = 'quest_div';
			div_inside1.innerHTML = jlms_RFE(response, 'quest_data') + jlms_RFE(response, 'quest_data_user');
			saved_prev_quest_data = jlms_RFE(response, 'quest_data') + jlms_RFE(response, 'quest_data_user');
			getObj('jq_quiz_container').appendChild(div_inside1);
			var quiz_menu = jlms_RFE(response, 'quiz_menu');
				quiz_review_correct = jlms_RFE(response, 'quiz_review_correct');
				getObj('jq_quiz_result_reviews').innerHTML = quiz_review_correct;
				getObj('jq_quiz_result_reviews').style.display = 'block';
				getObj('jq_quiz_result_reviews').style.visibility = 'visible';
				getObj('jq_quiz_explanation').innerHTML = jlms_RFE(response, 'quiz_review_explanation');
				getObj('jq_quiz_explanation').style.display = 'block';
				getObj('jq_quiz_explanation').style.visibility = 'visible';
			jq_UpdateTaskDiv_htm(quiz_menu);
			jq_UpdateTaskDiv('review_next');";
			if ($this->c_slide) {
				$ret_str .= "\n" . "//getObj('jq_panel_link_container').style.visibility = 'visible';";
			}
			$ret_str .= "	
				var is_exec_quiz_script = jlms_RFE(response, 'exec_quiz_script');
				saved_prev_quest_exec_quiz_script = is_exec_quiz_script;
				if (is_exec_quiz_script == 1 || is_exec_quiz_script == '1' ) {
					var v_quiz_script_data = jlms_RFE(response, 'quiz_script_data');
					saved_prev_quest_exec_quiz_script_data = v_quiz_script_data;
					eval(v_quiz_script_data);
				}
			";
		$ret_str .= "\n" . "	break;";
		return $ret_str;
	}
	function QUIZ_AR_task_review_next() {
		$ret_str = "	case 'review_next':
		quiz_blocked = 1;
		setTimeout(\"jq_releaseBlock()\", 1000);
		cur_quest_type = jlms_RFE(response, 'quest_type');
		saved_prev_quest_type = cur_quest_type;
		cur_quest_id = jlms_RFE(response, 'quest_id');
		saved_prev_quest_id = cur_quest_id;
		prev_quest_id = jlms_RFE(response, 'prev_quest_id');
		cur_quest_score = jlms_RFE(response, 'quest_score');
		saved_prev_quest_score = cur_quest_score;
		cur_quest_num = jlms_RFE(response, 'quiz_quest_num');
		saved_prev_quest_num = cur_quest_num;
		getObj('jq_quiz_container').innerHTML = '';
		var div_inside1=document.createElement(\"div\");
		div_inside1.id = 'quest_div';
		div_inside1.innerHTML = jlms_RFE(response, 'quest_data') + jlms_RFE(response, 'quest_data_user');
		saved_prev_quest_data = jlms_RFE(response, 'quest_data') + jlms_RFE(response, 'quest_data_user');
		getObj('jq_quiz_container').appendChild(div_inside1);
		var quiz_menu = jlms_RFE(response, 'quiz_menu');
			quiz_review_correct = jlms_RFE(response, 'quiz_review_correct');
			getObj('jq_quiz_result_reviews').innerHTML = quiz_review_correct;
			getObj('jq_quiz_result_reviews').style.display = 'block';
			getObj('jq_quiz_result_reviews').style.visibility = 'visible';
			getObj('jq_quiz_explanation').innerHTML = jlms_RFE(response, 'quiz_review_explanation');
			getObj('jq_quiz_explanation').style.display = 'block';
			getObj('jq_quiz_explanation').style.visibility = 'visible';
		jq_UpdateTaskDiv_htm(quiz_menu);
		jq_UpdateTaskDiv('review_next');
		";
		$ret_str .= "	
				var is_exec_quiz_script = jlms_RFE(response, 'exec_quiz_script');
				saved_prev_quest_exec_quiz_script = is_exec_quiz_script;
				if (is_exec_quiz_script == 1 || is_exec_quiz_script == '1' ) {
					var v_quiz_script_data = jlms_RFE(response, 'quiz_script_data');
					saved_prev_quest_exec_quiz_script_data = v_quiz_script_data;
					eval(v_quiz_script_data);
				}
			";
		$ret_str .= "\n" . "	break;";
		return $ret_str;
	}
	function QUIZ_AR_task_review_finish() {
		$ret_str = "	
		case 'review_finish':
			quiz_blocked = 1;
			review = 0;
			setTimeout(\"jq_releaseBlock()\", 1000);
			jq_UpdateTaskDiv('finish');
			var quiz_cont = getObj('jq_quiz_container');
			quiz_cont.innerHTML = saved_prev_res_data;//'<form name=\'quest_form\'><\/form>'+saved_prev_res_data;
		break;";
		return $ret_str;
	}
	function QUIZ_AR_task_next() {
		global $JLMS_CONFIG;
		$ret_str = "	case 'next':
		quiz_blocked = 1;
		quiz_progress = jlms_RFE(response, 'progress_quests_done');";
		if($JLMS_CONFIG->get('quiz_progressbar', 0) == 1){
		$ret_str .= "		
			if (progressbar != undefined) { progressbar.setProgress(quiz_progress); }";
		}
		$ret_str .= "
		setTimeout(\"jq_releaseBlock()\", 1000);
		prev_correct = jlms_RFE(response, 'quiz_prev_correct');
		var quiz_cont = getObj('jq_quiz_container');
		var children = quiz_cont.childNodes;
		for (var i = 0; i < children.length; i++) { quiz_cont.removeChild(quiz_cont.childNodes[i]); };
		quest_feedback = jlms_RFE(response, 'quest_feedback');
		quiz_cont.innerHTML = '';//'<form name=\'quest_form\'><\/form>';
		if (quest_feedback == '1') {
		var qmb = jlms_RFE(response, 'quiz_message_box');
		var quiz_menu = jlms_RFE(response, 'quiz_menu');
		jq_UpdateTaskDiv_htm(quiz_menu);
		if (prev_correct == '1') {";
		if ($this->c_slide_update) {
			$ret_str .= "\n" . "			getObj('quest_result_'+saved_prev_quest_id).innerHTML = \"<img src='".$JLMS_CONFIG->getCfg('live_site')."/components/com_joomla_lms/lms_images/toolbar/btn_accept.png' border=0>\";";
		}
		$ret_str .= "\n" . "			quiz_cont.innerHTML = qmb;//'<form name=\'quest_form\'><\/form>'+qmb;
			jq_UpdateTaskDiv('continue');
		} else {";
		if ($this->c_slide_update) {
			$ret_str .= "\n" . "			getObj('quest_result_'+saved_prev_quest_id).innerHTML = \"<img src='".$JLMS_CONFIG->getCfg('live_site')."/components/com_joomla_lms/lms_images/toolbar/btn_cancel.png' border=0>\";";
		}
		$ret_str .= "			quiz_cont.innerHTML = qmb;//'<form name=\'quest_form\'><\/form>'+qmb;
			allow_attempt = jlms_RFE(response, 'quiz_allow_attempt');
			if (allow_attempt == '1') { allow_attempt = 0; jq_UpdateTaskDiv('back_continue');
			} else { allow_attempt = 0; jq_UpdateTaskDiv('continue'); }
		}} else {
			var qmb = '';
			var qfrf = jlms_RFE(response, 'quest_feedback_repl_func');
			if (prev_correct == '1') {";
		if ($this->c_slide_update) {
			$ret_str .= "\n" . "			getObj('quest_result_'+saved_prev_quest_id).innerHTML = \"<img src='".$JLMS_CONFIG->getCfg('live_site')."/components/com_joomla_lms/lms_images/toolbar/btn_accept.png' border=0>\";";
		}
		$ret_str .= "\n" . "} else {";
		if ($this->c_slide_update) {
			$ret_str .= "\n" . "			getObj('quest_result_'+saved_prev_quest_id).innerHTML = \"<img src='".$JLMS_CONFIG->getCfg('live_site')."/components/com_joomla_lms/lms_images/toolbar/btn_cancel.png' border=0>\";";
		}
		$ret_str .= "\n" . "}
		eval(qfrf);
		}
	break;";
		return $ret_str;
	}
	function QUIZ_AR_task_no_attempts() {
		$ret_str = "	case 'no_attempts':
			quiz_blocked = 1;
			setTimeout(\"jq_releaseBlock()\", 1000);
			var qmb = jlms_RFE(response, 'quiz_message_box');
			var quiz_cont = getObj('jq_quiz_container');
			quiz_cont.innerHTML = qmb;//'<form name=\'quest_form\'><\/form>'+qmb;
			var quiz_menu = jlms_RFE(response, 'quiz_menu');
			jq_UpdateTaskDiv_htm(quiz_menu);
			jq_UpdateTaskDiv('next_no_attempts');
		break;";
		return $ret_str;
	}
	function QUIZ_AR_task_email_results() {
		$ret_str = "	case 'email_results':
			quiz_blocked = 1;
			//setTimeout('jq_releaseBlock()', 1000);
			var email_msg = jlms_RFE(response, 'email_msg');
			ShowMessage('error_messagebox', 1, email_msg);
		break;";
		return $ret_str;
	}
	function QUIZ_AR_task_time_is_up() {
		$ret_str = "	case 'time_is_up':
		quiz_blocked = 1;
		setTimeout(\"jq_releaseBlock()\", 1000);
		var quiz_cont = getObj('jq_quiz_container');
		
		stu_quiz_id = response.getElementsByTagName('stu_quiz_id')[0].firstChild.data;
		user_unique_id = response.getElementsByTagName('user_unique_id')[0].firstChild.data;
		
		var children = quiz_cont.childNodes;
		for (var i = 0; i < children.length; i++) { quiz_cont.removeChild(quiz_cont.childNodes[i]); };
		var qmb = jlms_RFE(response, 'quiz_message_box');
		quiz_cont.innerHTML = '';//'<form name=\'quest_form\'><\/form>';
		quiz_cont.innerHTML = qmb;//'<form name=\'quest_form\'><\/form>'+qmb;
		var quiz_menu = jlms_RFE(response, 'quiz_menu');
		jq_UpdateTaskDiv_htm(quiz_menu);
		jq_UpdateTaskDiv('continue_finish');
	break;";
		return $ret_str;
	}
	function QUIZ_AR_task_finish() {
		global $JLMS_CONFIG;
		$ret_str = "	case 'finish':
		quiz_blocked = 1;
		quiz_progress = jlms_RFE(response, 'progress_quests_done');";
		if($JLMS_CONFIG->get('quiz_progressbar', 0) == 1){
		$ret_str .= "		
			if (progressbar != undefined) { progressbar.setProgress(quiz_progress); }";
		}
		$ret_str .= "
		setTimeout('jq_releaseBlock()', 1000);
		prev_correct = jlms_RFE(response, 'quiz_prev_correct');
		var quiz_cont = getObj('jq_quiz_container');
		var children = quiz_cont.childNodes;
		for (var i = 0; i < children.length; i++) { quiz_cont.removeChild(quiz_cont.childNodes[i]); };
		quest_feedback = jlms_RFE(response, 'quest_feedback');
		quiz_cont.innerHTML = '';//'<form name=\'quest_form\'><\/form>';
		if (quest_feedback == '1') {
		var qmb = jlms_RFE(response, 'quiz_message_box');
		var quiz_menu = jlms_RFE(response, 'quiz_menu');
		jq_UpdateTaskDiv_htm(quiz_menu);
		if (prev_correct == '1') {";
		if ($this->c_slide_update) {
			$ret_str .= "\n" . "getObj('quest_result_'+saved_prev_quest_id).innerHTML = \"<img src='".$JLMS_CONFIG->getCfg('live_site')."/components/com_joomla_lms/lms_images/toolbar/btn_accept.png' border=0>\";";
		}
		$ret_str .= "//stop_timer = 1;
			quiz_cont.innerHTML = qmb;//'<form name=\'quest_form\'><\/form>'+qmb;
			jq_UpdateTaskDiv('continue_finish');
		} else {";
		if ($this->c_slide_update) {
			$ret_str .= "getObj('quest_result_'+saved_prev_quest_id).innerHTML = \"<img src='".$JLMS_CONFIG->getCfg('live_site')."/components/com_joomla_lms/lms_images/toolbar/btn_cancel.png' border=0>\";";
		}
		$ret_str .= "quiz_cont.innerHTML = qmb;//'<form name=\'quest_form\'><\/form>'+qmb;
			allow_attempt = jlms_RFE(response, 'quiz_allow_attempt');
			if (allow_attempt == '1') {
				allow_attempt = 0;
				jq_UpdateTaskDiv('back_continue_finish');
			} else {
				allow_attempt = 0;
				//stop_timer = 1;
				jq_UpdateTaskDiv('continue_finish');
			}
		}} else {
			var qmb = '';
			var qfrf = jlms_RFE(response, 'quest_feedback_repl_func');
			if (prev_correct == '1') {";
			if ($this->c_slide_update) {
				$ret_str .= "\n" . "getObj('quest_result_'+saved_prev_quest_id).innerHTML = \"<img src='".$JLMS_CONFIG->getCfg('live_site')."/components/com_joomla_lms/lms_images/toolbar/btn_accept.png' border=0>\";";
			}
			$ret_str .= "\n" . "} else {";
			if ($this->c_slide_update) {
				$ret_str .= "\n" . "getObj('quest_result_'+saved_prev_quest_id).innerHTML = \"<img src='".$JLMS_CONFIG->getCfg('live_site')."/components/com_joomla_lms/lms_images/toolbar/btn_cancel.png' border=0>\";";
			}
			$ret_str .= "\n" . "}
			eval(qfrf);
		}
	break;";
		return $ret_str;
	}
	function QUIZ_AR_task_results() {
		$ret_str = "	case 'results':
		quiz_blocked = 1;
		setTimeout('jq_releaseBlock()', 100);
		var quiz_cont = getObj('jq_quiz_container');
		var children = quiz_cont.childNodes;
		for (var i = 0; i < children.length; i++) { quiz_cont.removeChild(quiz_cont.childNodes[i]); };
		quiz_cont.innerHTML = '';//'<form name=\'quest_form\'><\/form>';
		stop_timer = 1;
		var jttc = getObj('jq_time_tick_container');
		if (jttc) { jttc.style.visibility = 'hidden'; }
		jq_UpdateTaskDiv('finish');
		var finish_msg = jlms_RFE(response, 'finish_msg');
		var quiz_results = jlms_RFE(response, 'quiz_results');
		var quiz_footer = jlms_RFE(response, 'quiz_footer');
		var quiz_cont = getObj('jq_quiz_container');
		quiz_cont.innerHTML = quiz_results+finish_msg+quiz_footer;//'<form name=\'quest_form\'><\/form>'+quiz_results+finish_msg+quiz_footer;
		saved_prev_res_data = quiz_results+finish_msg+quiz_footer;
	break;";
		return $ret_str;
	}
	function QUIZ_AR_task_failed() {
		$ret_str = "	case 'failed':
		ShowMessage('error_messagebox', 1, mes_failed);
		quiz_blocked = 1;
		setTimeout(\"jq_releaseBlock()\", 1000);
	break;";
		return $ret_str;
	}
	function QUIZ_AR_processtasks($req_tasks = array('failed')) {
		$ret_str = '';
		foreach ($req_tasks as $rt) {
			if ($rt == 'start') {
				$ret_str .= $this->QUIZ_AR_task_start();
			}
			if ($rt == 'seek_quest') {
				$ret_str .= $this->QUIZ_AR_task_seek_quest();
			}
			if ($rt == 'review_start') {
				$ret_str .= $this->QUIZ_AR_task_review_start();
			}
			if ($rt == 'review_next') {
				$ret_str .= $this->QUIZ_AR_task_review_next();
			}
			if ($rt == 'review_finish') {
				$ret_str .= $this->QUIZ_AR_task_review_finish();
			}
			if ($rt == 'next') {
				$ret_str .= $this->QUIZ_AR_task_next();
			}
			if ($rt == 'no_attempts') {
				$ret_str .= $this->QUIZ_AR_task_no_attempts();
			}
			if ($rt == 'email_results') {
				$ret_str .= $this->QUIZ_AR_task_email_results();
			}
			if ($rt == 'time_is_up') {
				$ret_str .= $this->QUIZ_AR_task_time_is_up();
			}
			if ($rt == 'finish') {
				$ret_str .= $this->QUIZ_AR_task_finish();
			}
			if ($rt == 'results') {
				$ret_str .= $this->QUIZ_AR_task_results();
			}
			if ($rt == 'failed') {
				$ret_str .= $this->QUIZ_AR_task_failed();
			}
		}
		return $ret_str;
	}
	function QUIZ_AR_end() {
		$ret_str = "		}
	} else {
		ShowMessage('error_messagebox', 1, mes_quiz_failed_request);
	}
}
}";
		return $ret_str;
	}
	function QUIZ_AnalizeRequest($is_echo = false, $ar_func_name = 'jq_AnalizeRequest', $req_tasks = array('failed')) {
		$ret_str = $this->QUIZ_AR_begin($ar_func_name);
		$ret_str .= $this->QUIZ_AR_processtasks($req_tasks);
		$ret_str .= $this->QUIZ_AR_end();
		if ($is_echo) { echo $ret_str; }
		else return $ret_str;
	}
	function QUIZ_releaseblock($is_echo = false) {
		$ret_str = "function jq_releaseBlock() { quiz_blocked = 0; }";
		if ($is_echo) { echo $ret_str; }
		else return $ret_str;
	}
	function QUIZ_StartTickTack($is_echo = false) {
		$ret_str = "function jq_Start_TickTack() {
	timer_sec = 1;
	getObj('jq_time_tick_container').innerHTML = '00:00';
	getObj('jq_time_tick_container').style.visibility = 'visible';
	ticktack_hand = setTimeout('jq_Continue_TickTack()', 1000);
}
function jq_Start_TickTackResume() {
	jq_ParseTickTackTimer(timer_sec);
	getObj('jq_time_tick_container').style.visibility = 'visible';
	ticktack_hand = setTimeout('jq_Continue_TickTack()', 1000);
}";
		if ($is_echo) { echo $ret_str; }
		else return $ret_str;
	}
	function QUIZ_ContinueTickTack($is_echo = false) {
		$ret_str = "function jq_Continue_TickTack() {
	var jttc = getObj('jq_time_tick_container');
	if (stop_timer == 1) {
		if (jttc) { jttc.style.visibility = 'hidden'; }
	} else {
		timer_sec ++;
		if ( max_quiz_time && (timer_sec > max_quiz_time) ) {
			if (jttc) { jttc.innerHTML = mes_time_is_up; }
		} else {
			jq_ParseTickTackTimer(timer_sec);
			ticktack_hand = setTimeout('jq_Continue_TickTack()', 1000);
		}
	}
}
function jq_ParseTickTackTimer(ts) {
	var th = parseInt(ts/3600);
	var tm = parseInt(ts/60) - (th*60);
	var ps = ts - (tm*60) - (th*3600);
	if (tm < 0) { tm = tm*(-1); }
	if (ps < 0) { ps = ps*(-1); }
	if (th < 0) { th = th*(-1); }
	var tsh = '';
	if (th) {
		tsh = th + '';
		if (tsh.length == 1) tsh = '0'+tsh;
		tsh = tsh + ':';
	}
	var tsm = tm + '';
	if (tsm.length == 1) tsm = '0'+tsm;
	tsm2 = ps + '';
	if (tsm2.length == 1) tsm2 = '0'+tsm2;
	var jttc = getObj('jq_time_tick_container');
	if (jttc) { jttc.innerHTML = tsh + tsm + ':' + tsm2; }	
}";
		if ($is_echo) { echo $ret_str; }
		else return $ret_str;
	}
	function QUIZ_StartQuizOn($is_echo = false) {
		$ret_str = "function jq_StartQuizOn() {
	if (!quiz_blocked) {
		timerID = setTimeout('jq_StartQuiz()', 300);
	} else {
		ShowMessage('error_messagebox', 1, mes_please_wait);
	}
}
function jq_ResumeQuizOn(resume_id, unique_id, last_question) {
		if (!quiz_blocked) {
			timerID = setTimeout(\"jq_ResumeQuiz(\"+resume_id+\", '\"+unique_id+\"', \"+last_question+\")\", 300);
		} else {
			ShowMessage('error_messagebox', 1, mes_please_wait);
		}
	}
";
		if ($is_echo) { echo $ret_str; }
		else return $ret_str;
	}
	function QUIZ_StartQuiz($is_echo = false) {
		$ret_str = "function jq_StartQuiz() { jq_MakeRequest('&atask=start&quiz='+quiz_id,1); }";
		$ret_str .= "function jq_ResumeQuiz(resume_id, unique_id, last_question) { jq_MakeRequest('&atask=resume_quiz&quiz='+quiz_id+'&resume_id='+resume_id+'&unique_id='+unique_id+'&last_question='+last_question+'&inside_lp=1',1); }";
		if ($is_echo) { echo $ret_str; }
		else return $ret_str;
	}

	function QUIZ_GoToQuestionOn($is_echo = false) {
		$ret_str = "function JQ_gotoQuestionOn(qid) {
			if (!quiz_blocked) {
				timerID = setTimeout(\"JQ_gotoQuestion(\"+qid+\")\", 300);
			} else {
				ShowMessage('error_messagebox', 1, mes_please_wait);
				setTimeout('jq_releaseBlock()', 1000);
			}
		}";
		if ($is_echo) { echo $ret_str; }
		else return $ret_str;
	}
	function QUIZ_GoToQuestion($is_echo = false) {
		$ret_str = "function JQ_gotoQuestion(qid) {
			if(review){
				jq_MakeRequest('&atask=review_next&prev=1&quiz='+quiz_id+'&stu_quiz_id='+stu_quiz_id+'&quest_id='+qid, 1 ); 
			} else {
				jq_MakeRequest('&atask=goto_quest&quiz='+quiz_id+'&stu_quiz_id='+stu_quiz_id+'&seek_quest_id='+qid, 1 ); 
			}
		}";
		if ($is_echo) { echo $ret_str; }
		else return $ret_str;
	}
	function QUIZ_EmailResults($is_echo = false) {
		$ret_str = "	function jq_emailResults() {
	if (!quiz_blocked) {
		ShowMessage('error_messagebox', 1, mes_loading);
		jq_MakeRequest('&atask=email_results&quiz='+quiz_id+'&stu_quiz_id='+stu_quiz_id,0);
	} else {
		//ShowMessage('error_messagebox', 1, mes_please_wait);// setTimeout(\"jq_releaseBlock()\", 1000);
	}
}";
		if ($is_echo) { echo $ret_str; }
		else return $ret_str;
	}
	function QUIZ_StartReview($is_echo = false) {
		$ret_str = "	function jq_startReview() {
	if (!quiz_blocked) {
		jq_MakeRequest('&atask=review_start&quiz='+quiz_id+'&stu_quiz_id='+stu_quiz_id, 1);
	} else {
		ShowMessage('error_messagebox', 1, mes_please_wait); setTimeout(\"jq_releaseBlock()\", 1000);
	}
}";
		if ($is_echo) { echo $ret_str; }
		else return $ret_str;
	}
	function QUIZ_ReviewNext($is_echo = false) {
		$ret_str = "function jq_QuizReviewNext() {
			if (!quiz_blocked) {
				jq_MakeRequest('&atask=review_next&quiz='+quiz_id+'&stu_quiz_id='+stu_quiz_id+'&quest_id='+cur_quest_id, 1);
			} else {
				ShowMessage('error_messagebox', 1, mes_please_wait);
				setTimeout(\"jq_releaseBlock()\", 1000);
			}
		}";
		if ($is_echo) { echo $ret_str; }
		else return $ret_str;
	}
	function QUIZ_ReviewPrev($is_echo = false) {
		$ret_str = "function jq_QuizReviewPrev() {
			if (!quiz_blocked) {
				jq_MakeRequest('&atask=review_next&prev=1&quiz='+quiz_id+'&stu_quiz_id='+stu_quiz_id+'&quest_id='+prev_quest_id, 1);
			} else {
				ShowMessage('error_messagebox', 1, mes_please_wait);
				setTimeout(\"jq_releaseBlock()\", 1000);
			}
		}";
		if ($is_echo) { echo $ret_str; }
		else return $ret_str;
	}
	function QUIZ_Check_selectRadio($is_echo = false) {
		$ret_str = "function jq_Check_selectRadio(rad_name, form_name) {
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
		}";
		if ($is_echo) { echo $ret_str; }
		else return $ret_str;
	}
	function QUIZ_Check_selectCheckbox($is_echo = false) {
		$ret_str = "function jq_Check_selectCheckbox(check_name, form_name) {
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
}";
		if ($is_echo) { echo $ret_str; }
		else return $ret_str;
	}
	function QUIZ_Check_valueItem($is_echo = false) {
		$ret_str = "function jq_Check_valueItem(item_name, form_name) {
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
}";
		if ($is_echo) { echo $ret_str; }
		else return $ret_str;
	}
	function QUIZ_QuizNextOn($is_echo = false) {
		$ret_str = "function jq_QuizNextOn() { // Two steps CHECK (delete this func in the future)
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
				return false;}
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
	}
	if (!quiz_blocked) {
		//ShowMessage('error_messagebox', 1, mes_loading);
		//jq_showLoading();
		quiz_blocked = 1;
		timerID = setTimeout(\"jq_QuizNext()\", 300);
	} else { ShowMessage('error_messagebox', 1, mes_please_wait); setTimeout(\"jq_releaseBlock()\", 1000); }
}";
		if ($is_echo) { echo $ret_str; }
		else return $ret_str;
	}
	function QUIZ_Next($is_echo = true) {
		$ret_str = "function jq_QuizNext() { //send 'TASK = next'
	var jq_task = 'next';
	switch (cur_quest_type) {
		case '1':
		case '12':
			var answer = jq_Check_selectRadio('quest_choice', 'quest_form');
			if (answer || document.quest_form.ismandatory.value != '0') {
				jq_MakeRequest('&atask=' + jq_task + '&quiz=' + quiz_id + '&stu_quiz_id='+stu_quiz_id+'&quest_id='+cur_quest_id+'&answer='+answer, 1);
			} else { ShowMessage('error_messagebox', 1, mes_complete_this_part); setTimeout(\"jq_releaseBlock()\", 1000); return false; }
		break;
		case '3':
			var answer = jq_Check_selectRadio('quest_choice', 'quest_form');
			if (answer) {
				jq_MakeRequest('&atask=' + jq_task + '&quiz=' + quiz_id + '&stu_quiz_id='+stu_quiz_id+'&quest_id='+cur_quest_id+'&answer='+answer, 1);
			} else { ShowMessage('error_messagebox', 1, mes_complete_this_part); setTimeout(\"jq_releaseBlock()\", 1000); return false; }
		break;
		case '2':
		case '13':
			var answer = jq_Check_selectCheckbox('quest_choice', 'quest_form');
			if (answer != '') {
				jq_MakeRequest('&atask=' + jq_task + '&quiz='+quiz_id+'&stu_quiz_id='+stu_quiz_id+'&quest_id='+cur_quest_id+'&answer='+answer, 1);
			} else { ShowMessage('error_messagebox', 1, mes_complete_this_part); setTimeout(\"jq_releaseBlock()\", 1000); return false; }
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
				setTimeout(\"jq_releaseBlock()\", 1000);
				return false;
			} else {
				answer = answer.substring(0, answer.length - 3);
				jq_MakeRequest('&atask=' + jq_task + '&quiz='+quiz_id+'&stu_quiz_id='+stu_quiz_id+'&quest_id='+cur_quest_id+'&answer='+answer, 1);
			}
		break;
		case '5':
			var answer = jq_Check_valueItem('quest_match', 'quest_form');
			answer = URLencode(answer);
			if (answer != '') {
				jq_MakeRequest('&atask=' + jq_task + '&quiz='+quiz_id+'&stu_quiz_id='+stu_quiz_id+'&quest_id='+cur_quest_id+'&answer='+answer, 1);
			} else { ShowMessage('error_messagebox', 1, mes_complete_this_part); setTimeout(\"jq_releaseBlock()\", 1000); return false; }
		break;
		case '6':
			var blank_item = document.quest_form.quest_blank;
			var answer = URLencode(TRIM_str(blank_item.value));
			if (answer != '' || document.quest_form.ismandatory.value != '0') {
				jq_MakeRequest('&atask=' + jq_task + '&quiz='+quiz_id+'&stu_quiz_id='+stu_quiz_id+'&quest_id='+cur_quest_id+'&answer='+answer, 1);
			} else { ShowMessage('error_messagebox', 1, mes_complete_this_part); setTimeout(\"jq_releaseBlock()\", 1000); return false; }
		break;
		case '7':
			var hs_x = parseInt(document.quest_form.hotspot_x.value);
			var hs_y = parseInt(document.quest_form.hotspot_y.value);
			if ((hs_x != 0) && (hs_y != 0)) {
				var answer = hs_x + ',' + hs_y;
				jq_MakeRequest('&atask=' + jq_task + '&quiz='+quiz_id+'&stu_quiz_id='+stu_quiz_id+'&quest_id='+cur_quest_id+'&answer='+answer, 1);
			} else { ShowMessage('error_messagebox', 1, mes_complete_this_part); setTimeout(\"jq_releaseBlock()\", 1000); return false; }
		break;
		case '8':
			var answer = URLencode(TRIM_str(document.quest_form.survey_box.value));
			if (answer != '' || document.quest_form.ismandatory.value != '0') {
				jq_MakeRequest('&atask=' + jq_task + '&quiz='+quiz_id+'&stu_quiz_id='+stu_quiz_id+'&quest_id='+cur_quest_id+'&answer='+answer, 1);
			} else { ShowMessage('error_messagebox', 1, mes_complete_this_part); setTimeout(\"jq_releaseBlock()\", 1000); return false; }
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
					setTimeout('jq_releaseBlock()', 1000);
					return false;
				} else {
					
					jq_MakeRequest('&atask=' + jq_task + '&quiz='+quiz_id+'&stu_quiz_id='+stu_quiz_id+'&quest_id='+cur_quest_id+'&answer='+answer, 1);
				}
			break;	
			case '10':
				jq_MakeRequest('&atask=' + jq_task + '&quiz='+quiz_id+'&stu_quiz_id='+stu_quiz_id+'&quest_id='+cur_quest_id+'&answer=0', 1);
			break;
		default:
			ShowMessage('error_messagebox', 1, mes_quiz_unknown_er);
			setTimeout(\"jq_releaseBlock()\", 1000);
		break;
	}
}";
		if ($is_echo) { echo $ret_str; }
		else return $ret_str;
	}
	function QUIZ_QuizContinue($is_echo = false, $end_toolbar_no_skip = '', $end_toolbar_skip = '') {
		global $JLMS_CONFIG;
		$ret_str = "function jq_QuizContinue() {
	cur_quest_type = jlms_RFE(response, 'quest_type');
	saved_prev_quest_type = cur_quest_type;
	cur_quest_id = jlms_RFE(response, 'quest_id');
	saved_prev_quest_id = cur_quest_id;
	cur_quest_score = jlms_RFE(response, 'quest_score');
	saved_prev_quest_score = cur_quest_score;
	cur_quest_num = jlms_RFE(response, 'quiz_quest_num');
	saved_prev_quest_num = cur_quest_num;
	skip_next_quest = jlms_RFE(response, 'quiz_skip_next_quest');
	var quiz_cont = getObj('jq_quiz_container');
	quiz_cont.innerHTML = '';
	if (cur_quest_type == 7) {
		var div_insidey=document.createElement(\"div\");
		div_insidey.id = 'quest_div_hs';
		getObj('jq_quiz_container').appendChild(div_insidey);
	}
	var div_inside1=document.createElement(\"div\");
	div_inside1.id = 'quest_div';
	div_inside1.innerHTML = jlms_RFE(response, 'quest_data') + jlms_RFE(response, 'quest_data_user');
	saved_prev_quest_data = jlms_RFE(response, 'quest_data') + jlms_RFE(response, 'quest_data_user');
	quiz_cont.appendChild(div_inside1);
	if(parseInt(skip_next_quest)){
		var subject = '".str_replace('/','\/',addslashes(str_replace('"components/com_joomla_lms','"'.$JLMS_CONFIG->getCfg('live_site').'/components/com_joomla_lms',$end_toolbar_skip)))."';
		html_replace = subject.split('__skip__').join(skip_next_quest);
		jq_UpdateTaskDiv_htm(html_replace);		
	} else {
		jq_UpdateTaskDiv_htm('".str_replace('/','\/',addslashes(str_replace('"components/com_joomla_lms','"'.$JLMS_CONFIG->getCfg('live_site').'/components/com_joomla_lms',$end_toolbar_no_skip)))."');
	}
	jq_UpdateTaskDiv('next');
	var is_exec_quiz_script = jlms_RFE(response, 'exec_quiz_script');
	saved_prev_quest_exec_quiz_script = is_exec_quiz_script;
	if (is_exec_quiz_script == 1 || is_exec_quiz_script == '1' ) {
		var v_quiz_script_data = jlms_RFE(response, 'quiz_script_data');
		saved_prev_quest_exec_quiz_script_data = v_quiz_script_data;
		eval(v_quiz_script_data);
	}
}";
		if ($is_echo) { echo $ret_str; }
		else return $ret_str;
	}
	//url must be in JS format with trailing '';
	function QUIZ_QuizContinueFinish($is_echo = false, $url = '&atask=finish_stop') {
		
		//$url .= "&user_unique_id='+user_unique_id";	
	
		$ret_str = "function jq_QuizContinueFinish() {
				
	jq_MakeRequest(".$url.", 1);
}";
		if ($is_echo) { echo $ret_str; }
		else return $ret_str;
	}
	function QUIZ_QuizBack($is_echo = false, $end_toolbar = '') {
		global $JLMS_CONFIG;
		$ret_str = "function jq_QuizBack() {
	cur_quest_id = saved_prev_quest_id;
	cur_quest_type = saved_prev_quest_type;
	cur_quest_score = saved_prev_quest_score;
	cur_quest_num = saved_prev_quest_num;
	var quiz_cont = getObj('jq_quiz_container');
	quiz_cont.innerHTML = '';
	if (cur_quest_type == 7) {
		var div_insidey=document.createElement(\"div\");
		div_insidey.id = 'quest_div_hs';
		getObj('jq_quiz_container').appendChild(div_insidey);
	}
	var div_inside1=document.createElement(\"div\");
	div_inside1.id = 'quest_div';
	div_inside1.innerHTML = saved_prev_quest_data;
	quiz_cont.appendChild(div_inside1);
	jq_UpdateTaskDiv_htm('".str_replace('/','\/',addslashes(str_replace('"components/com_joomla_lms','"'.$JLMS_CONFIG->getCfg('live_site').'/components/com_joomla_lms',$end_toolbar)))."');
	jq_UpdateTaskDiv('next');
	var is_exec_quiz_script = saved_prev_quest_exec_quiz_script;
	if (is_exec_quiz_script == 1 || is_exec_quiz_script == '1' ) {
		var v_quiz_script_data = saved_prev_quest_exec_quiz_script_data;
		eval(v_quiz_script_data);
	}
}";
		if ($is_echo) { echo $ret_str; }
		else return $ret_str;
	}
	function QUIZ_showLoading($is_echo = false) {
		global $JLMS_CONFIG;
		$ret_str = "function jq_showLoading() {
			ShowMessage('error_messagebox', 0, '&nbsp;');
			getObj('jq_quiz_result_reviews').style.visibility = 'hidden';
			getObj('jq_quiz_explanation').style.visibility = 'hidden';
			getObj('jq_quiz_result_reviews').style.display = 'none';
			getObj('jq_quiz_explanation').style.display = 'none';
			getObj('jq_quiz_container').innerHTML = '<br /><br /><center><img src=\"".$JLMS_CONFIG->getCfg('live_site')."/components/com_joomla_lms/lms_images/loading.gif\" height=\"32\" width=\"32\" border=\"0\" alt=\"loading\" /><\/center>';
		}";
		if ($is_echo) { echo $ret_str; }
		else return $ret_str;
	}
	function QUIZ_UpdateTaskDiv_htm($is_echo = false) {
		$ret_str = "function jq_UpdateTaskDiv_htm(htm_txt) {
	getObj('jq_quiz_task_container').innerHTML = htm_txt;
}";
		if ($is_echo) { echo $ret_str; }
		else return $ret_str;
	}
	function QUIZ_UpdateTaskDiv($is_echo = false, $c_slide = false) {
		$ret_str = "function jq_UpdateTaskDiv(task) {
	switch (task) {
		case 'start':
			getObj('jq_quiz_task_container').innerHTML = jq_StartButton('jq_StartQuizOn()', mes_quiz_start);
		break;
		case 'next':
			getObj('jq_quest_num_container').innerHTML = mes_quest_number.replace(\"{X}\", cur_quest_num).replace(\"{Y}\", quiz_count_quests);
			getObj('jq_quest_num_container').style.visibility = \"visible\";
			getObj('jq_points_container').innerHTML = mes_quest_points.replace(\"{X}\", cur_quest_score);
			getObj('jq_points_container').style.visibility = \"visible\";
		break;
		case 'review_next':
			getObj('jq_quest_num_container').innerHTML = mes_quest_number.replace(\"{X}\", cur_quest_num).replace(\"{Y}\", quiz_count_quests);
			getObj('jq_quest_num_container').style.visibility = \"visible\";
			getObj('jq_points_container').innerHTML = mes_quest_points.replace(\"{X}\", cur_quest_score);
			getObj('jq_points_container').style.visibility = \"visible\";
		break;
		case 'finish':
		case 'clear':
			getObj('jq_quiz_task_container').innerHTML = '';
			getObj('jq_quest_num_container').style.visibility = 'hidden';
			getObj('jq_points_container').style.visibility = 'hidden';
		break;
	}";
	if ($c_slide) {
		$ret_str .= "\n"."if (result_is_shown == 1) { jq_ShowPanel(); }"."\n";
	}
	$ret_str .= "}";
		if ($is_echo) { echo $ret_str; }
		else return $ret_str;
	}
	function QUIZ_NextButton($is_echo = false) {
		$ret_str = 'function jq_NextButton(task, text) {
	return "<div id=\"jq_next_link_container\" onclick=\""+task+"\"><div class=\"back_button\" id=\"jq_quiz_task_link_container\"><a href=\"javascript: void(0)\">"+text+"<\/a><\/div><br \/><\/div>";
}';
		if ($is_echo) { echo $ret_str; }
		else return $ret_str;
	}
	function QUIZ_ContinueButton($is_echo = false) {
		$ret_str = 'function jq_ContinueButton(task, text) {
	return "<div id=\"jq_continue_link_container\" onclick=\""+task+"\"><div class=\"back_button\" id=\"jq_quiz_task_link_container\"><a href=\"javascript: void(0)\">"+text+"<\/a><\/div><br \/><\/div>";
}';
		if ($is_echo) { echo $ret_str; }
		else return $ret_str;
	}
	function QUIZ_StartButton($is_echo = false) {
		$ret_str = 'function jq_StartButton(task, text) {
	return "<div id=\"jq_start_link_container\" onclick=\""+task+"\"><div class=\"back_button\" id=\"jq_quiz_task_link_container\"><a href=\"javascript: void(0)\">"+text+"<\/a><\/div><br \/><\/div>";
}';
		if ($is_echo) { echo $ret_str; }
		else return $ret_str;
	}
	function QUIZ_BackButton($is_echo = false) {
		$ret_str = 'function jq_BackButton(task, text) {
	return "<div id=\"jq_back_link_container\" onclick=\""+task+"\"><div class=\"back_button\" id=\"jq_quiz_task_link_container\"><a href=\"javascript: void(0)\">"+text+"<\/a><\/div><br \/><\/div>";
}';
		if ($is_echo) { echo $ret_str; }
		else return $ret_str;
	}
	function QUIZ_ShowPanel_go($is_echo = false) {
		$ret_str = "function jq_ShowPanel_go() {
	var jq_quiz_c_cont = getObj('jq_quiz_container');
	if (jq_quiz_c_cont) { jq_quiz_c_cont.style.visibility = 'hidden'; jq_quiz_c_cont.style.display = 'none';}
	var jq_quiz_r_c = getObj('jq_quiz_result_container');
	if (jq_quiz_r_c) { jq_quiz_r_c.style.visibility = 'visible'; jq_quiz_r_c.style.display = 'block';}
}";
		if ($is_echo) { echo $ret_str; }
		else return $ret_str;
	}
	function QUIZ_HidePanel_go($is_echo = false) {
		$ret_str = "function jq_HidePanel_go() {
	var jq_quiz_r_c = getObj('jq_quiz_result_container');
	if (jq_quiz_r_c) { jq_quiz_r_c.style.visibility = 'hidden'; jq_quiz_r_c.style.display = 'none';}
	var jq_quiz_c_cont = getObj('jq_quiz_container');
	if (jq_quiz_c_cont) { jq_quiz_c_cont.style.visibility = 'visible'; jq_quiz_c_cont.style.display = 'block';}
}";
		if ($is_echo) { echo $ret_str; }
		else return $ret_str;
	}
	function QUIZ_ShowPanel($is_echo = false) {
		$ret_str = "function jq_ShowPanel() {
	if (result_is_shown == 1) { jq_HidePanel_go(); result_is_shown = 0;	}
	else { jq_ShowPanel_go();	result_is_shown = 1; }
}";
		if ($is_echo) { echo $ret_str; }
		else return $ret_str;
	}

	function Get_LPContents_btn($is_echo = false) {
		global $JLMS_CONFIG;
		$ret_str = "<table style=\\\"text-align: right;\\\" align=\\\"right\\\" border=\\\"0\\\" cellpadding=\\\"0\\\" cellspacing=\\\"0\\\"><tr><td><a href=\\\"javascript:ajax_action('contents_lpath');\\\" title=\\\"Contents\\\" onmouseover=\\\"jlms_WStatus('Contents');return true;\\\" onmouseout=\\\"jlms_WStatus('');return true;\\\"><img class=\\\"JLMS_png\\\" src=\\\"".$JLMS_CONFIG->getCfg('live_site')."/components/com_joomla_lms/lms_images/buttons/btn_contents.png\\\" alt=\\\"Contents\\\" title=\\\"Contents\\\" border=\\\"0\\\" height=\\\"32\\\" width=\\\"32\\\"><\/a><\/td><td style=\\\"vertical-align: middle;\\\" valign=\\\"middle\\\"><a href=\\\"javascript:ajax_action('contents_lpath');\\\" title=\\\"Contents\\\" onmouseover=\\\"jlms_WStatus('Contents');return true;\\\" onmouseout=\\\"jlms_WStatus('');return true;\\\">&nbsp;Contents&nbsp;&nbsp;<\/a><\/td><\/tr><\/table>";
		if ($is_echo) { echo $ret_str; }
		else return $ret_str;
	}
}
?>