<?php

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

require_once( _JOOMLMS_FRONT_HOME.DS.'includes'.DS.'notifications'.DS.'email.manager.php' );

function JQ_Email($sid,$email_to) {		
	global $JLMS_DB;
	$str = JQ_PrintResultForMail($sid);
	$valid_mail = preg_match( '/^[\w\.\-]+@\w+[\w\.\-]*?\.\w{1,6}$/', $email_to );
	if (!$valid_mail) {
		return false;
	}
	$email = $email_to;
	$subject = "Quiz Results";
	$message = html_entity_decode($str, ENT_QUOTES);
	$app = & JFactory::getApplication();
	if ($app->getCfg('mailfrom') && $app->getCfg('fromname')) {
		$adminName2 = $app->getCfg('fromname');
		$adminEmail2 = $app->getCfg('mailfrom');
	} else {
		$query = "SELECT name, email"
		. "\n FROM #__users"
		. "\n WHERE LOWER( usertype ) = 'superadministrator'"
		. "\n OR LOWER( usertype ) = 'super administrator'"
		;
		$JLMS_DB->setQuery( $query );
		$rows = $JLMS_DB->loadObjectList();
		$row2 			= $rows[0];
		$adminName2 	= $row2->name;
		$adminEmail2 	= $row2->email;
	}
	
	$mailManager = & MailManager::getChildInstance();	
		
	$params['sender'] = array( $adminEmail2, $adminName2 );
	$params['recipient'] = $email;
	$params['subject'] = $subject;
	$params['alttext'] = $message;	
	$params['body'] = nl2br( $message );	 	
					
	$mailManager->prepareEmail( $params );	
	
	return $mailManager->sendEmail();
}

function JQ_PrintResultForMail($sid) {
	global $JLMS_DB;
	$str = "";
	$query = "SELECT * FROM #__lms_quiz_r_student_quiz AS sq, #__lms_quiz_t_quiz AS q, #__users AS u"
	. "\n WHERE sq.c_id = '".$sid."' AND sq.c_quiz_id = q.c_id AND sq.c_student_id = u.id";
	$JLMS_DB->SetQuery( $query );
	$info = $JLMS_DB->LoadAssocList();
	$info = $info[0];

	$quiz_id = $info['c_quiz_id'];

	$query = "SELECT quest_id FROM #__lms_quiz_r_student_quiz_pool WHERE start_id = ".$sid;
	$JLMS_DB->SetQuery( $query );
	$stu_quests = $JLMS_DB->loadResultArray();
	if (count($stu_quests)) {
		$stu_quests_str = implode(',', $stu_quests);
		$query = "SELECT sum(c_point) AS total_score FROM #__lms_quiz_t_question WHERE c_id IN ($stu_quests_str)";
		$JLMS_DB->SetQuery( $query );
		$total = $JLMS_DB->LoadResult();
	} else {
		$query = "SELECT sum(c_point) AS total_score FROM #__lms_quiz_t_question WHERE c_quiz_id = '".$quiz_id."'";
		$JLMS_DB->SetQuery( $query );
		$total = $JLMS_DB->LoadResult();
	}

	$passing_score_suffix = '';
	if (isset($info['c_passing_score']) && $info['c_passing_score']) {
		$pass_score = ($info['c_passing_score'] * $total) / 100;
		$pass_score_ceiled = ceil($pass_score);
		//TODO: add new language variable for 'point/points' ; 'ochko/ochka/ochkov'
		$points_str = 'points';
		$passing_score_suffix = ' ('.$pass_score_ceiled.' '.$points_str.')';
	}

	$str .= "\n";
	$str .= "Quiz Title: ".$info['c_title']."\n";
	$str .= "User Name: ".$info['username']."\n";
	$str .= "Name: ".$info['name']."\n";
	$str .= "User Email: ".$info['email']."\n";
	$str .= "User Score: ".$info['c_total_score']."\n";
	$str .= "Total Score: ".$total."\n";
	$str .= "Passing Score: ".$info['c_passing_score']."%".$passing_score_suffix."\n";
	$tot_min = floor($info['c_total_time'] / 60);
	$tot_sec = $info['c_total_time'] - $tot_min*60;
	$str .= "The user spent ".str_pad($tot_min,2, "0", STR_PAD_LEFT).":".str_pad($tot_sec,2, "0", STR_PAD_LEFT)." time taking the quiz "."\n";
	if ($info['c_passed'] == 1) {
		$str .= $info['name']." has passed the quiz "."\n";
	}
	else {
		$str .= $info['name']." has not passed the quiz "."\n";
	}
	
	$str .= " \n";
	
	$query = "SELECT c_id FROM #__lms_quiz_r_student_question WHERE c_stu_quiz_id = '".$sid."' ORDER BY c_id";
	$JLMS_DB->SetQuery( $query );
	$info = $JLMS_DB->LoadObjectList();
	$total = count($info);
	for($i=0;$i < $total;$i++) {
		$data = JQ_GetResults($info[$i]->c_id);
		$str .= "".($i+1).".[".$data['c_score'].'/'.$data['c_point']."] ".JFilterOutput::cleanText($data['c_question'])."\n";
		$type = $data['c_type'];
		$answer = '';
		if($type==1 || $type==2 || $type==3 || $type==12 || $type==13) {
			for($j=0,$k='A';$j < count($data['c_choice']);$j++,$k++) {
				if($data['c_choice'][$j]['c_choice_id']) $answer .= $k."&nbsp;";
				$str .= "$k. ".$data['c_choice'][$j]['c_choice']."\n";
			}
			$str .= " Answer: $answer \n";
		} elseif($type==4 || $type==5 || $type==11) {
			$str .= "  Answer: \n";
			for($j=0,$k='A';$j < count($data['c_matching']);$j++,$k++) {
				$str .= "  $k. ".$data['c_matching'][$j]['c_left_text']." ";
				$str .= "  ".$data['c_matching'][$j]['c_sel_text']."\n";
			}
			$str .= " ";
		} elseif($type==6) {
			$str .= "  Answer: ".$data['c_blank']['c_answer']."\n";
		} elseif($type==7) {
			if($data['c_score']) $answer = 'right';
			else $answer = ' wrong';
			$str .= "  Answer: ".$answer."\n";
		} elseif($type==8) {
			$str .= "  Answer: ".$data['c_survey']['c_answer']." \n";
		} elseif ($type == 9){
			$str .= "  Answer:";
			$str .= "\n";
			for($j = 0;$j<count($data['c_scale']);$j++)
			{
				$mpz = $data['c_scale'][$j];
				$str .= "  ".$mpz->fq." - ";
				$str .= "&nbsp;&nbsp;".$mpz->fa."\n";
			}
		}
		elseif ($type == 10){
			
		}
		$str .= "\n";
	}
	$str .= " ";
	
	return $str;
}	

function JQ_PrintResultForPDF($sid) {
	global $JLMS_DB;
	$str = "";
	$query = "SELECT sq.*, q.*, u.* FROM #__lms_quiz_r_student_quiz AS sq, #__lms_quiz_t_quiz AS q, #__users AS u"
	. "\n WHERE sq.c_id = '".$sid."' AND sq.c_quiz_id = q.c_id AND sq.c_student_id = u.id";
	$JLMS_DB->SetQuery( $query );
	$info = $JLMS_DB->LoadAssocList();
	$info = $info[0];
	$quiz_id = $info['c_quiz_id'];
	$query = "SELECT quest_id FROM #__lms_quiz_r_student_quiz_pool WHERE start_id = ".$sid;
	$JLMS_DB->SetQuery( $query );
	$stu_quests = $JLMS_DB->loadResultArray();
	if (count($stu_quests)) {
		$stu_quests_str = implode(',', $stu_quests);
		$query = "SELECT sum(c_point) AS total_score FROM #__lms_quiz_t_question WHERE c_id IN ($stu_quests_str)";
		$JLMS_DB->SetQuery( $query );
		$total = $JLMS_DB->LoadResult();
	} else {
		$query = "SELECT sum(c_point) AS total_score FROM #__lms_quiz_t_question WHERE c_quiz_id = '".$quiz_id."'";
		$JLMS_DB->SetQuery( $query );
		$total = $JLMS_DB->LoadResult();
	}

	$passing_score_suffix = '';
	if (isset($info['c_passing_score']) && $info['c_passing_score']) {
		$pass_score = ($info['c_passing_score'] * $total) / 100;
		$pass_score_ceiled = ceil($pass_score);
		//TODO: add new language variable for 'point/points' ; 'ochko/ochka/ochkov'
		$points_str = 'points';
		$passing_score_suffix = ' ('.$pass_score_ceiled.' '.$points_str.')';
	}

	$str .= "<strong>Quiz Title: </strong>".$info['c_title']."<br>";
	$str .= "<strong>User Name: </strong>".$info['username']."<br>";
	$str .= "<strong>Name: </strong>".$info['name']."<br>";
	$str .= "<strong>User Email: </strong>".$info['email'].'<br>';
	$str .= "<strong>User Score: </strong>".$info['c_total_score']."<br>";		
	$str .= "<strong>Total Score: </strong>".$total."<br>";
	$str .= "<strong>Passing Score: </strong>".$info['c_passing_score']."%".$passing_score_suffix."<br>";
	$tot_min = floor($info['c_total_time'] / 60);
	$tot_sec = $info['c_total_time'] - $tot_min*60;
	$str .= "The user spent <strong>".str_pad($tot_min,2, "0", STR_PAD_LEFT).":".str_pad($tot_sec,2, "0", STR_PAD_LEFT)."</strong> time taking the quiz <br>";
	if ($info['c_passed'] == 1) {
		$str .= $info['name']." has <strong>passed</strong> the quiz<br>";
	} else {
		$str .= $info['name']." has <strong>not passed</strong> the quiz<br>";
	}
	
	/*Integration Plugin Percentiles*/
	$_JLMS_PLUGINS = & JLMSFactory::getPlugins();
	$_JLMS_PLUGINS->loadBotGroup('system');
	
	$data = new stdClass();
	$data->course_id = $info['course_id'];
	$data->quiz_id = $info['c_quiz_id'];
	$data->user_id = $info['c_student_id'];
	
	if($out_plugin = $_JLMS_PLUGINS->trigger('onQuizFinish', array($data))){
		if(count($out_plugin)){
			$percentiles = $out_plugin[0];
			$percent = $percentiles->percent.'%';
			$str .= "<strong>Percentiles: </strong>".$percent."<br>";
		}	
	}
	/*Integration Plugin Percentiles*/
	
	$query = "SELECT lqrsq.c_id FROM #__lms_quiz_r_student_question as lqrsq, #__lms_quiz_t_question as lqtq"
	. "\n WHERE 1"
	. "\n AND lqrsq.c_stu_quiz_id = '".$sid."'"
	. "\n AND lqtq.c_id = lqrsq.c_question_id"
	. "\n ORDER BY lqtq.ordering"
	;
	$JLMS_DB->SetQuery( $query );
	$info = $JLMS_DB->LoadObjectList();
	
	$total = count($info);
	$tr = array();
	$i = 0;
	for($k1=0;$k1 < $total;$k1++) {
		$data = JQ_GetResults($info[$k1]->c_id);
		
		if (isset($data['c_question'])) {
			$tr[$i] = array();
			//$as = $tr[$i];
			$tr[$i]['#'] = $i+1;
			$tr[$i]['Questions'] = trim(strip_tags(JLMS_ShowText_WithFeatures($data['c_question']))); //."[".$data['c_score'].'/'.$data['c_point']."]";
			$type = $data['c_type'];
			$answer = '';
			$text_answer = '';
			if($type==1 || $type==2 || $type==3 || $type==12 || $type==13) {
				for($j=0,$k='a';$j < count($data['c_choice']);$j++,$k++) {
					if($data['c_choice'][$j]['c_choice_id']) $answer .= $k;
					$text_answer .= "<strong>$k)</strong> ".$data['c_choice'][$j]['c_choice']."<br />";
				}
				$text_answer .= '<br />Answer: '.$answer;
			} elseif($type==4 || $type==5) {
				for($j=0,$k='a';$j < count($data['c_matching']);$j++,$k++) {
					$text_answer .= "<strong>$k)</strong> ".$data['c_matching'][$j]['c_left_text']." ";
					$text_answer .= "&nbsp;&nbsp;".$data['c_matching'][$j]['c_sel_text']."<br />";
				}
			} elseif($type==6) {
					$text_answer .= $data['c_blank']['c_answer'];
			} elseif($type==7) {
				if($data['c_score']) $answer = 'right';
				else $answer = 'wrong';
				$text_answer .= $answer;
			} elseif($type==8) {
				$text_answer .= $data['c_survey']['c_answer'];
			} elseif ($type == 9){
				for($j = 0;$j<count($data['c_scale']);$j++)
				{
					$mpz = $data['c_scale'][$j];
					$text_answer .= "<strong>".$mpz->fq."</strong> - ";
					$text_answer .= $mpz->fa.'<br>';
				}
			}
			elseif ($type == 10){
				
			} elseif($type == 11) {
				for($j=0,$k='a';$j < count($data['c_matching']);$j++,$k++) {
					$text_answer .= "<strong>$k)</strong>&nbsp;".$data['c_matching'][$j]['c_left_text']."";
					$text_answer .= "&nbsp;&nbsp;".$data['c_matching'][$j]['c_sel_text']."<br />";
				}
			}
	
			$tr[$i]['Answers'] = $text_answer;
			$tr[$i]['Points'] = $data['c_score'].' / '.$data['c_point'];
			$i ++ ;
		}
	}
	$str .= "";
	
	/*
	// convert UTF symbols to ISO (Joomla 1.5.x only)
	if (class_exists('JFactory')) {
		global $JLMS_CONFIG;
		$cur_lang = strtolower($JLMS_CONFIG->get('default_language', 'english'));
		$sup_iso_languages_pre = array('english', 'danish', 'french', 'german', 'italian', 'norwegian', 'spanish', 'dutch');
		$sup_iso_languages = $JLMS_CONFIG->get('iso88591_compat_languages', $sup_iso_languages_pre);
		if (in_array($cur_lang, $sup_iso_languages) || $cur_lang ==  "english" ) {
			if (function_exists('utf8_decode')) {
				$do_utf = true;
				$utf_method = 'utf8_encode';
				$str = utf8_decode($str);
				for ($ii = 0; $ii <= $i; $ii ++ ) {
					if (isset($tr[$ii]['Answers'])) {
						$tr[$ii]['Answers'] = utf8_decode($tr[$ii]['Answers']);
					}
					if (isset($tr[$ii]['Questions'])) {
						$tr[$ii]['Questions'] = utf8_decode($tr[$ii]['Questions']);
					}
				}
			}
		}
	}
	*/

	$str_arr->header = $str;
	$str_arr->table = $tr;	
	$str_arr->colsWidths = array( '#' => 10, 'Answers' => 40, 'Questions' => 40, 'Points' => 10  );
//	echo '<pre>';
//	print_r($str_arr);
//	echo '</pre>';
//	die;
	
	return $str_arr;
}
function JQ_GetResults($id) {
	global $JLMS_DB;
	$query = "SELECT q.c_id c_id, c_question, c_point, c_type, c_score, c_pool, c_pool_gqp"
	. "\n FROM #__lms_quiz_r_student_question AS sq, #__lms_quiz_t_question AS q"
	. "\n WHERE sq.c_id = '".$id."' AND sq.c_question_id = q.c_id";
	$JLMS_DB->setQuery( $query );
	$info = $JLMS_DB->LoadAssocList();
	if (isset($info[0])) {
	$info = $info[0];

	$type_id = $info['c_type'];
	$qid = $info['c_id'];
	/* 25 April 2007 (DEN)
	 * (Question Pool compatibility)
	 */
	if ($type_id == 20) {
		if (isset($info['c_pool']) && $info['c_pool']) {
			$query = "SELECT * FROM #__lms_quiz_t_question WHERE c_id = ".$info['c_pool'];
			$JLMS_DB->setQuery( $query );
			$info_tmp = $JLMS_DB->LoadObject();
			if (is_object($info_tmp)) {
				$type_id = $info_tmp->c_type;
				$qid = $info_tmp->c_id;
				$info['c_question'] = $info_tmp->c_question;
				$info['c_point'] = $info_tmp->c_point;
				$info['c_type'] = $info_tmp->c_type;
				#$info['c_score'] = $info_tmp->c_score;
			} else {
				return $info;
			}
		} else {
			return $info;
		}
	}
	
	if ($type_id == 21) {
		if (isset($info['c_pool_gqp']) && $info['c_pool_gqp']) {
			$query = "SELECT * FROM #__lms_quiz_t_question WHERE c_id = ".$info['c_pool_gqp'];
			$JLMS_DB->setQuery( $query );
			$info_tmp = $JLMS_DB->LoadObject();
			if (is_object($info_tmp)) {
				$type_id = $info_tmp->c_type;
				$qid = $info_tmp->c_id;
				$info['c_question'] = $info_tmp->c_question;
				$info['c_point'] = $info_tmp->c_point;
				$info['c_type'] = $info_tmp->c_type;
				#$info['c_score'] = $info_tmp->c_score;
			} else {
				return $info;
			}
		} else {
			return $info;
		}
	}	

	if($type_id==1 || $type_id==2 || $type_id==3) {
		$query = "SELECT * FROM #__lms_quiz_t_choice AS c LEFT JOIN #__lms_quiz_r_student_choice AS sc"
		. "\n ON c.c_id = sc.c_choice_id AND sc.c_sq_id = '".$id."'"
		. "\n WHERE c.c_question_id = '".$qid."' ORDER BY c.ordering";
		$JLMS_DB->SetQuery( $query );
		$tmp = $JLMS_DB->LoadAssocList();
		
		$info['c_choice'] = $tmp;
	} elseif($type_id==12 || $type_id==13){
		$query = "SELECT c.*, sc.*, im1.imgs_name as c_choice FROM #__lms_quiz_images as im1, #__lms_quiz_t_choice AS c LEFT JOIN #__lms_quiz_r_student_choice AS sc"
		. "\n ON c.c_id = sc.c_choice_id AND sc.c_sq_id = '".$id."'"
		. "\n WHERE c.c_question_id = '".$qid."' AND c.c_choice = im1.imgs_id ORDER BY c.ordering";
		$JLMS_DB->SetQuery( $query );
		$tmp = $JLMS_DB->LoadAssocList();
		
		$info['c_choice'] = $tmp;
	} elseif ($type_id==4 || $type_id==5) {
		$query = "SELECT * FROM #__lms_quiz_t_matching AS m LEFT JOIN #__lms_quiz_r_student_matching AS sm"
		. "\n ON m.c_id = sm.c_matching_id AND sm.c_sq_id = '".$id."' WHERE m.c_question_id = '".$qid."'";
		$JLMS_DB->SetQuery( $query );
		$tmp = $JLMS_DB->LoadAssocList();
		$info['c_matching'] = $tmp;
	} elseif ($type_id == 6) {
		$query = "SELECT * FROM #__lms_quiz_r_student_blank WHERE c_sq_id = '".$id."'";
		$JLMS_DB->SetQuery( $query );
		$tmp = $JLMS_DB->LoadAssocList();
		$info['c_blank'] = $tmp[0];
	} elseif ($type_id == 7) {
		$query = "SELECT * FROM #__lms_quiz_t_question AS q, #__lms_quiz_t_hotspot AS h"
		. "\n WHERE q.c_id = '".$qid."' AND q.c_id = h.c_question_id";
		$JLMS_DB->SetQuery( $query );
		$info['c_hotspot'] = $JLMS_DB->LoadRow();
		$query = "select * from #__lms_quiz_r_student_hotspot where c_sq_id='".$id."'";
		$JLMS_DB->SetQuery( $query );
		$tmp = $JLMS_DB->LoadRow();
		while(list($key,$value) = each($tmp)) {
			$info['c_hotspot'][$key] = $value;
		}
	} elseif ($type_id == 8) {
		$query = "select * from #__lms_quiz_r_student_survey where c_sq_id='".$id."'";
		$JLMS_DB->SetQuery( $query );
		$tmp = $JLMS_DB->LoadAssocList();
		$info['c_survey'] = $tmp[0];
	} elseif ($type_id == 9) {	
		$query = "SELECT sc.c_field as fq,scf.c_field as fa FROM #__lms_quiz_r_student_scale as st, #__lms_quiz_t_scale as sc, #__lms_quiz_t_scale as scf WHERE st.c_sq_id = '".$id."' AND st.q_scale_id = sc.c_id AND st.scale_id = scf.c_id AND scf.c_id!=1";
		$JLMS_DB->SetQuery( $query );
		$tmp = $JLMS_DB->LoadObjectList();
		$info['c_scale'] = $tmp;
	} elseif ($type_id == 10) {	
		$query = "SELECT tq.c_question  FROM #__lms_quiz_t_question as tq, #__lms_quiz_r_student_scale as st WHERE st.c_sq_id = '".$id."' AND st.c_question_id = tq.c_id";
		$JLMS_DB->SetQuery( $query );
		$info['boiler'] = $JLMS_DB->LoadResult();
	} elseif ($type_id==11) {
		$query = "SELECT m.*, sm.*, im1.imgs_name as c_left_text, im2.imgs_name as c_sel_text FROM #__lms_quiz_images as im1, #__lms_quiz_images as im2, #__lms_quiz_t_matching AS m LEFT JOIN #__lms_quiz_r_student_matching AS sm"
		. "\n ON m.c_id = sm.c_matching_id AND sm.c_sq_id = '".$id."' WHERE m.c_question_id = '".$qid."' AND m.c_left_text = im1.imgs_id AND m.c_right_text = im2.imgs_id";
		$JLMS_DB->SetQuery( $query );
		$tmp = $JLMS_DB->LoadAssocList();
		
		$info['c_matching'] = $tmp;	
	} else {
		$info = array('c_type' => 99, 'c_score' => 0, 'c_point' =>0, 'c_question' => '');
	}
	}
	return $info;
	
}
function rel_dofreePDF ( $text_to_pdf ) {
	global $JLMS_CONFIG;	
	

	chdir(JPATH_SITE);
	if ( file_exists( JPATH_SITE . DS .'components'. DS .'com_joomla_lms'. DS .'includes'. DS .'pdf'. DS .'lms.pdf.php') ) {
		include( JPATH_SITE . DS .'components'. DS .'com_joomla_lms'. DS .'includes'. DS .'pdf'. DS .'lms.pdf.php' );
	} else {
		die('Unable to print PDF');
	}

	$text_to_pdf_header 	= rel_pdfCleaner( $text_to_pdf->header );
	$text_to_pdf_table	 	= rel_pdfCleaner2( $text_to_pdf->table );	
				
	$pdf = new JLMSPDF( 'P', 'mm', 'A4', true, 'UTF-8', false );  //A4 Portrait
	$pdf->SetMargins( 10, 10, 10, true );
	$pdf->SetDrawColor( 0, 0, 0 );
	
	$header = "<br /><br /><table width=\"100%\">
						<tr>
							<td width=\"50%\" align=\"right\">".$JLMS_CONFIG->get('sitename')."</td>
							<td width=\"50%\" align=\"right\">{pagination}</td>
						</tr>
					</table>";
	
	$footer = "
		<hr />
		<table>
		    <tr>
		        <td align=\"left\">".$JLMS_CONFIG->get('live_site')."</td>
	";
	
	if ($JLMS_CONFIG->get('is_trial')) {
		$footer .= "<td align=\"center\">Powered by JoomlaLMS (www.joomlalms.com)</td>";
	}
	
	$footer .= "
		        <td align=\"right\">". date( 'j F, Y, H:i', time() + $JLMS_CONFIG->get('offset') * 60 * 60 )."</td>
		    </tr>		  
		</table>
	";
	
	$pdf->setHeaderFont( array('freesans','',6) );
	$pdf->setHeaderHTML( $header );
	
	$pdf->setFooterMargin( 5 );
	$pdf->setFooterFont( array('freesans','',6) );	
	$pdf->setFooterHTML( $footer );	
	
	$pdf->addPage();	
	$pdf->setFont( 'freesans' ); //choose font		
	$pdf->SetFontSize( 8 );
	
	$top = 20;
	$dYl = $top;
		
//	$options['cols'] = array( 'Questions' => $col_quest_text_options);	
	$pdf->setY( $dYl );	
	$pdf->writeHTML( nl2br($text_to_pdf_header) );
	
	ob_clean();
	ob_start();	
		
	echo '<table cellspacing="0" cellpadding="1" border="1">';
	
	if( isset($text_to_pdf_table[0]) ) 
	{
		$keys = array_keys( $text_to_pdf_table[0]);
		echo '<tr>';
		foreach( $keys AS $key ) 
		{
			$key = preg_replace('/\s\s+/', ' ', $key);
			echo '<td width="'.$text_to_pdf->colsWidths[$key].'%">'.$key.'</td>';			
		}			
		echo '</tr>';
	}
		
	for( $i=0; $i<count($text_to_pdf_table); $i++) 
	{		
		$values = array_values( $text_to_pdf_table[$i]);
				
		echo '<tr>';
		foreach( $values AS $value ) 
		{			
			echo '<td>'.nl2br($value).'</td>';	
		}				
		echo '</tr>';				
	}		
	echo  '</table><br />';		
	$html = ob_get_contents();	
	ob_end_flush();	
	ob_clean();		
	$pdf->setFont( 'freesans' ); //choose font			
	$pdf->SetFontSize( 8 );
	$pdf->writeHTML($html, true, false, false, false, '');

	// 15.12.2007 fix for showing PDF in IE. (DEN)
	/* 
	 *  JoomlaLMS uses sessions, but session_start operation sent header 'pragma:no-cache' and 'cache-control:...' - here we will redefine them.
	 */
	/*
	if (preg_match('/MSIE ([0-9].[0-9]{1,2})/', $_SERVER['HTTP_USER_AGENT'])) {
		header('Pragma: anytextexeptno-cache', true);
		//header('Pragma: cache=1', true);
		header('Cache-Control: cache=on', true);
	}
	*/
	
	//footer	
	$pdf->Output( 'QuizResults.pdf', 'I' );
}

function rel_decodeHTML( $string ) {
	$string = strtr( $string, array_flip(get_html_translation_table( HTML_ENTITIES ) ) );
	$string = preg_replace( "/&#([0-9]+);/me", "chr('\\1')", $string );

	return $string;
}

function rel_get_php_setting ($val ) {
	$r = ( ini_get( $val ) == '1' ? 1 : 0 );

	return $r ? 'ON' : 'OFF';
}

function rel_pdfCleaner( $text ) {	
	// Ugly but needed to get rid of all the stuff the PDF class cant handle

	$text = str_replace( '<p>', 			"\n\n", 	$text );
	$text = str_replace( '<P>', 			"\n\n", 	$text );
	$text = str_replace( '<br />', 			"\n", 		$text );
	$text = str_replace( '<br>', 			"\n", 		$text );
	$text = str_replace( '<BR />', 			"\n", 		$text );
	$text = str_replace( '<BR>', 			"\n", 		$text );
	$text = str_replace( '<li>', 			"\n - ", 	$text );
	$text = str_replace( '<LI>', 			"\n - ", 	$text );
	$text = str_replace( '{mosimage}', 		'', 		$text );
	$text = str_replace( '{mospagebreak}', 	'',			$text );
	$text = str_replace( '&nbsp;', 	' ',			$text );
	$text = str_replace( '«', 	'"',			$text );
	$text = str_replace( '»', 	'"',			$text );

	$text = strip_tags( $text );
	$text = rel_decodeHTML( $text );

	return $text;
}

function rel_pdfCleaner2( $text ) {	
	// Ugly but needed to get rid of all the stuff the PDF class cant handle
	
	for($i=0;$i<count($text);$i++){
		if (isset($text[$i]['Questions'])) {
			$text[$i]['Questions'] = str_replace( '<p>', 			"\n\n", 	$text[$i]['Questions'] );
			$text[$i]['Questions'] = str_replace( '<P>', 			"\n\n", 	$text[$i]['Questions'] );
			$text[$i]['Questions'] = str_replace( '<br />', 			"\n", 		$text[$i]['Questions'] );
			$text[$i]['Questions'] = str_replace( '<br>', 			"\n", 		$text[$i]['Questions'] );
			$text[$i]['Questions'] = str_replace( '<BR />', 			"\n", 		$text[$i]['Questions'] );
			$text[$i]['Questions'] = str_replace( '<BR>', 			"\n", 		$text[$i]['Questions'] );
			$text[$i]['Questions'] = str_replace( '<li>', 			"\n - ", 	$text[$i]['Questions'] );
			$text[$i]['Questions'] = str_replace( '<LI>', 			"\n - ", 	$text[$i]['Questions'] );
			$text[$i]['Questions'] = str_replace( '{mosimage}', 		'', 		$text[$i]['Questions'] );
			$text[$i]['Questions'] = str_replace( '{mospagebreak}', 	'',			$text[$i]['Questions'] );
			$text[$i]['Questions'] = str_replace( '&nbsp;', 	' ',			$text[$i]['Questions'] );
			$text[$i]['Questions'] = str_replace( '«', 	'"',			$text[$i]['Questions'] );
			$text[$i]['Questions'] = str_replace( '»', 	'"',			$text[$i]['Questions'] );

			$text[$i]['Questions'] = strip_tags( $text[$i]['Questions'] );
			$text[$i]['Questions'] = rel_decodeHTML( $text[$i]['Questions'] );

			$text[$i]['Answers'] = str_replace( '<p>', 			"\n\n", 	$text[$i]['Answers'] );
			$text[$i]['Answers'] = str_replace( '<P>', 			"\n\n", 	$text[$i]['Answers'] );
			$text[$i]['Answers'] = str_replace( '<br />', 			"\n", 		$text[$i]['Answers'] );
			$text[$i]['Answers'] = str_replace( '<br>', 			"\n", 		$text[$i]['Answers'] );
			$text[$i]['Answers'] = str_replace( '<BR />', 			"\n", 		$text[$i]['Answers'] );
			$text[$i]['Answers'] = str_replace( '<BR>', 			"\n", 		$text[$i]['Answers'] );
			$text[$i]['Answers'] = str_replace( '<li>', 			"\n - ", 	$text[$i]['Answers'] );
			$text[$i]['Answers'] = str_replace( '<LI>', 			"\n - ", 	$text[$i]['Answers'] );
			$text[$i]['Answers'] = str_replace( '{mosimage}', 		'', 		$text[$i]['Answers'] );
			$text[$i]['Answers'] = str_replace( '{mospagebreak}', 	'',			$text[$i]['Answers'] );
			$text[$i]['Answers'] = str_replace( '&nbsp;', 	' ',			$text[$i]['Answers'] );
			$text[$i]['Answers'] = str_replace( '«', 	'"',			$text[$i]['Answers'] );
			$text[$i]['Answers'] = str_replace( '»', 	'"',			$text[$i]['Answers'] );

			$text[$i]['Answers'] = strip_tags( $text[$i]['Answers'] );
			$text[$i]['Answers'] = rel_decodeHTML( $text[$i]['Answers'] );

			$text[$i]['Points'] = str_replace( '<p>', 			"\n\n", 	$text[$i]['Points'] );
			$text[$i]['Points'] = str_replace( '<P>', 			"\n\n", 	$text[$i]['Points'] );
			$text[$i]['Points'] = str_replace( '<br />', 			"\n", 		$text[$i]['Points'] );
			$text[$i]['Points'] = str_replace( '<br>', 			"\n", 		$text[$i]['Points'] );
			$text[$i]['Points'] = str_replace( '<BR />', 			"\n", 		$text[$i]['Points'] );
			$text[$i]['Points'] = str_replace( '<BR>', 			"\n", 		$text[$i]['Points'] );
			$text[$i]['Points'] = str_replace( '<li>', 			"\n - ", 	$text[$i]['Points'] );
			$text[$i]['Points'] = str_replace( '<LI>', 			"\n - ", 	$text[$i]['Points'] );
			$text[$i]['Points'] = str_replace( '{mosimage}', 		'', 		$text[$i]['Points'] );
			$text[$i]['Points'] = str_replace( '{mospagebreak}', 	'',			$text[$i]['Points'] );
			$text[$i]['Points'] = str_replace( '&nbsp;', 	' ',			$text[$i]['Points'] );
			$text[$i]['Points'] = str_replace( '«', 	'"',			$text[$i]['Points'] );
			$text[$i]['Points'] = str_replace( '»', 	'"',			$text[$i]['Points'] );

			$text[$i]['Points'] = strip_tags( $text[$i]['Points'] );
			$text[$i]['Points'] = rel_decodeHTML( $text[$i]['Points'] );
		}
	}

	return $text;
}
?>