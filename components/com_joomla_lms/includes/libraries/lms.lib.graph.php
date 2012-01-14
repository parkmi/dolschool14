<?php
defined( '_JLMS_EXEC' ) or die( 'Restricted access' );

require_once(_JOOMLMS_FRONT_HOME . "/includes/libchart/libchart.php");
JLMS_cleanLibChartCache();

function JLMS_GraphStatistics($option, $id, $quiz_id, $i, $z, $row=array(), $c_question_id=0, $group_id=0, $str_user_in_groups='', $no_js=0 ){
	global $JLMS_DB, $JLMS_CONFIG;
	$obj = new stdClass();
	if(isset($row->c_type)){
		switch ($row->c_type){
			case '1':
			case '2':
			case '3':
			case '12':
			case '13':
					$query = "SELECT c_id FROM #__lms_quiz_t_choice WHERE c_question_id = '".$c_question_id."' ORDER BY ordering";
					$JLMS_DB->SetQuery( $query );
					$choice_ids = $JLMS_DB->LoadResultArray(); 
					$choice_id = implode(',', $choice_ids);
					if (!$choice_id) { $choice_id = '0';}
					$query = "SELECT COUNT(*) FROM #__lms_quiz_r_student_quiz as a, #__lms_quiz_r_student_question as q, #__lms_quiz_r_student_choice as c";
					$query .= "\n WHERE q.c_id = c.c_sq_id AND q.c_question_id = '".($row->c_id)."' AND c.c_choice_id IN (".$choice_id.")";
					$query .= "\n AND a.c_id = q.c_stu_quiz_id";
					if($group_id){
						$query .= "\n AND a.c_student_id IN (".$str_user_in_groups.")";
					}
					$JLMS_DB->setQuery( $query );
					$total_count = $JLMS_DB->loadResult();

					if($row->c_type == '12' || $row->c_type == '13'){
						$query = "SELECT a.c_id as value, a.c_choice as text, a.c_right, b.imgs_name"
						. "\n FROM #__lms_quiz_t_choice as a"
						. "\n, #__lms_quiz_images as b"
						. "\n WHERE a.c_question_id = '".$c_question_id."'"
						. "\n AND a.c_choice = b.imgs_id"
						. "\n ORDER BY a.ordering";
					} else {
						$query = "SELECT a.c_id as value, a.c_choice as text, a.c_right"
						. "\n FROM #__lms_quiz_t_choice as a"
						. "\n WHERE a.c_question_id = '".$c_question_id."'"
						. "\n ORDER BY a.ordering";
					}
					$JLMS_DB->SetQuery( $query );
					$choice_data = $JLMS_DB->LoadObjectList();

					if($total_count) {

						$chart = new MultiVerticalChart(600, 200, 1);
						$chart->maxval = 100;
						$chart->maintitle = '';

						for ($j=0;$j<count($choice_data);$j++)
						{
							$query = "SELECT count(*) FROM #__lms_quiz_r_student_quiz as a, #__lms_quiz_r_student_choice as sch, #__lms_quiz_r_student_question as qst"
							. "\n WHERE sch.c_choice_id = '".$choice_data[$j]->value."' AND sch.c_sq_id=qst.c_id AND qst.c_question_id='".($row->c_id)."'"
							. "\n AND a.c_id = qst.c_stu_quiz_id"
							.($group_id ? "\n AND a.c_student_id IN (".$str_user_in_groups.")" : '')
							;
							$JLMS_DB->setQuery($query);
							$choice_this = $JLMS_DB->loadResult();
	
							$row_percent = round(($choice_this*100)/$total_count);
							if($row->c_type == '12' || $row->c_type == '13'){
								$chart->addPoint($z, new Point(trim(strip_tags($choice_data[$j]->imgs_name)), $row_percent), $choice_this);
							} else {
								$chart->addPoint($z, new Point(trim(strip_tags($choice_data[$j]->text)), $row_percent), $choice_this);	
							}
						}
						$chart->usr_answers[$z] = $i+1;
						$chart->titles[$z] = '';
						$filename = time() . '_' . md5(uniqid(rand(), true)) . ".png";
						//$this->clearOldImages(1);		
						$chart->render($JLMS_CONFIG->get('temp_folder', '').'/'.$filename);
						$obj->img_graph = $JLMS_CONFIG->get('temp_folder', '').'/'.$filename;
						$obj->title_graph = trim(strip_tags($row->c_question)); 	
						$obj->count_graph = 1;
						$obj->points = array();
						if(true){
							$query = "SELECT a.c_student_id, q.* FROM #__lms_quiz_r_student_quiz as a, #__lms_quiz_r_student_question as q, #__lms_quiz_r_student_choice as c";
							$query .= "\n WHERE q.c_id = c.c_sq_id AND q.c_question_id = '".($row->c_id)."' AND c.c_choice_id IN (".$choice_id.")";
							$query .= "\n AND a.c_id = q.c_stu_quiz_id";
							if($group_id){
								$query .= "\n AND a.c_student_id IN (".$str_user_in_groups.")";
							}
							$JLMS_DB->setQuery( $query );
							$answers_students = $JLMS_DB->loadObjectList();
							
							$right_answ = 0;
							foreach($answers_students as $answ_stu){
								if($answ_stu->c_correct == 2){
									$right_answ = $right_answ + 1;	
								}
							}
							$no_right_answ = $total_count - $right_answ;

							$chart = new MultiVerticalChart(600, 200, 1);
							$chart->maxval = 100;
							$chart->maintitle = '';
							$row_percent = round(($right_answ*100)/$total_count);
							$chart->addPoint($z, new Point(_JLMS_GRAPH_STATISTICS_CORRECT, $row_percent), $right_answ);
							$row_percent = round(($no_right_answ*100)/$total_count);
							$chart->addPoint($z, new Point(_JLMS_GRAPH_STATISTICS_INCORRECT, $row_percent), $no_right_answ);
							$chart->usr_answers[$z] = $i+1;
							$chart->titles[$z] = '';
							$filename = $row->c_type . '_' . time() . '_' . md5(uniqid(rand(), true)) . ".png";
							$chart->render($JLMS_CONFIG->get('temp_folder', '').'/'.$filename);
							$obj->img_correct = $JLMS_CONFIG->get('temp_folder', '').'/'.$filename;
							$obj->title_correct = trim(strip_tags($row->c_question));
							$obj->count_correct = 1;	
						}
					}
			break;
			case '4':
			case '5':
			case '11':
					$query = "SELECT a.c_id, a.c_right_text, a.c_left_text"
					.($row->c_type == '11' ? "\n, b.imgs_name as left_imgs, b.imgs_name as right_imgs" : '')
					. "\n FROM #__lms_quiz_t_matching as a"
					.($row->c_type == '11' ? "\n, #__lms_quiz_images as b" : '')
					. "\n WHERE a.c_question_id = '".$c_question_id."'"
					.($row->c_type == '11' ? "\n AND a.c_left_text = b.imgs_id AND a.c_right_text = b.imgs_id" : '')
					. "\n ORDER BY a.ordering";
					$JLMS_DB->SetQuery( $query );
					$fields_ids = $JLMS_DB->LoadObjectList();
					$left_answ = array();
					$left_imgs = array();

					for($j=0;$j<count($fields_ids);$j++)
					{
						$left_answ[] = $fields_ids[$j]->c_right_text;
						if($row->c_type == '11'){
							$left_imgs[] = $fields_ids[$j]->right_imgs;
						}
					}
					$chart = new MultiVerticalChart(600, 200*count($fields_ids), count($fields_ids));
					$chart->maxval = 100;
					$chart->maintitle = '';
					$adxz = 0;

					for($j=0;$j<count($fields_ids);$j++)
					{ 
						$fields = $fields_ids[$j];
						$query = "SELECT c_sel_text FROM #__lms_quiz_r_student_quiz as a, #__lms_quiz_r_student_question as q, #__lms_quiz_r_student_matching as c";
						$query .= "\n WHERE q.c_id = c.c_sq_id AND q.c_question_id = '".$row->c_id."' AND c.c_matching_id = '".$fields->c_id."'";
						$query .= "\n AND a.c_id = q.c_stu_quiz_id";
						if($group_id){
							$query .= "\n AND a.c_student_id IN (".$str_user_in_groups.")";
						}
						$JLMS_DB->setQuery( $query );
						$total_ids = $JLMS_DB->LoadObjectList();
						$total_count = 0;

						for ($g=0;$g<count($total_ids);$g++)
						{
							if(in_array($total_ids[$g]->c_sel_text,$left_answ)){
								$total_count++;
							}
						}

						for ($g=0;$g<count($left_answ);$g++)
						{
							$query = "SELECT COUNT(*) FROM #__lms_quiz_r_student_quiz as a, #__lms_quiz_r_student_question as q, #__lms_quiz_r_student_matching as c";
							$query .= "\n WHERE q.c_id = c.c_sq_id AND q.c_question_id = '".$row->c_id."' AND c.c_matching_id = '".$fields->c_id."' AND c.c_sel_text = '".addslashes($left_answ[$g])."'";
							$query .= "\n AND a.c_id = q.c_stu_quiz_id";
							if($group_id){
								$query .= "\n AND a.c_student_id IN (".$str_user_in_groups.")";
							}
							$JLMS_DB->setQuery( $query );
							$cur_count = $JLMS_DB->LoadResult();

							if(!$total_count) {
								$row_percent = 0;	
							}
							else {								
								$row_percent = round(($cur_count*100)/$total_count);
							}	

							if($row->c_type == '11'){
								$chart->addPoint($z, new Point(trim(strip_tags($left_imgs[$g])), $row_percent), $cur_count);
							} else {
								$chart->addPoint($z, new Point(trim(strip_tags($left_answ[$g])), $row_percent), $cur_count);	
							}
							$adxz++;
						}

						$chart->usr_answers[$z] = $i+1;
						if($row->c_type == '11'){
							$chart->titles[$z] = trim(strip_tags($fields_ids[$j]->left_imgs));
						} else {
							$chart->titles[$z] = trim(strip_tags($fields_ids[$j]->c_left_text));
						}
						$z++;
					}
					if($z>1 && $adxz)
					{
						$filename = time() . '_' . md5(uniqid(rand(), true)) . ".png";
						$chart->render($JLMS_CONFIG->get('temp_folder', '').'/'.$filename);
						$obj->img_graph = $JLMS_CONFIG->get('temp_folder', '').'/'.$filename;
						$obj->title_graph = trim(strip_tags($row->c_question));
						$obj->count_graph = count($fields_ids);
					}
					if($z>1 && $adxz){
						$z=1;
						$query = "SELECT COUNT(*) FROM #__lms_quiz_r_student_quiz as a, #__lms_quiz_r_student_question as q"; //, #__lms_quiz_r_student_matching as c
						$query .= "\n WHERE q.c_question_id = '".$row->c_id."'"; // AND c.c_matching_id IN (".$str_fields.") AND c.c_sel_text = '".addslashes($left_answ[$g])."'
						$query .= "\n AND a.c_id = q.c_stu_quiz_id";
						if($group_id){
							$query .= "\n AND a.c_student_id IN (".$str_user_in_groups.")";
						}
						$JLMS_DB->setQuery( $query );
						$answers_count = $JLMS_DB->loadResult();

						$query = "SELECT a.c_student_id, q.* FROM #__lms_quiz_r_student_quiz as a, #__lms_quiz_r_student_question as q"; //, #__lms_quiz_r_student_matching as c
						$query .= "\n WHERE q.c_question_id = '".$row->c_id."'"; // AND c.c_matching_id IN (".$str_fields.") AND c.c_sel_text = '".addslashes($left_answ[$g])."'
						$query .= "\n AND a.c_id = q.c_stu_quiz_id";
						if($group_id){
							$query .= "\n AND a.c_student_id IN (".$str_user_in_groups.")";
						}
						$JLMS_DB->setQuery( $query );
						$answers_students = $JLMS_DB->loadObjectList();
						
						$right_answ = 0;
						foreach($answers_students as $answ_stu){
							if($answ_stu->c_correct == 2){
								$right_answ = $right_answ + 1;	
							}
						}
						$no_right_answ = $answers_count - $right_answ;

						$chart = new MultiVerticalChart(600, 200, 1);
						$chart->maxval = 100;
						$chart->maintitle = '';
						$row_percent = $answers_count ? round(($right_answ*100)/$answers_count): 0;
						$chart->addPoint($z, new Point(_JLMS_GRAPH_STATISTICS_CORRECT, $row_percent), $right_answ);
						$row_percent = $answers_count ? round(($no_right_answ*100)/$answers_count): 0;
						$chart->addPoint($z, new Point(_JLMS_GRAPH_STATISTICS_INCORRECT, $row_percent), $no_right_answ);
						$chart->usr_answers[$z] = '';
						$chart->titles[$z] = '';
						$filename = $row->c_type . '_' . time() . '_' . md5(uniqid(rand(), true)) . ".png";
						$chart->render($JLMS_CONFIG->get('temp_folder', '').'/'.$filename);

						$obj->img_correct = $JLMS_CONFIG->get('temp_folder', '').'/'.$filename;
						$obj->title_correct = trim(strip_tags($row->c_question));
						$obj->count_correct = 1; 
					}
			break;
			case '6':
					$quest_params = new JLMSParameters($row->params);
					if($quest_params->get('survey_question'))
					{
						$display_count = 5;
						$popualr = array();
						$query = "SELECT c.* FROM #__lms_quiz_r_student_quiz as a, #__lms_quiz_r_student_question as q, #__lms_quiz_r_student_blank as c";
						$query .= "\n WHERE q.c_id = c.c_sq_id AND q.c_question_id = '".$row->c_id."'";
						$query .= "\n AND a.c_id = q.c_stu_quiz_id";
						if($group_id){
							$query .= "\n AND a.c_student_id IN (".$str_user_in_groups.")";
						}
						$JLMS_DB->setQuery( $query );
						$total_answer = $JLMS_DB->loadObjectList();
						$total_count = count($total_answer);
						if($total_count)
						{
							$query = "SELECT DISTINCT(c.c_answer) FROM #__lms_quiz_r_student_quiz as a, #__lms_quiz_r_student_question as q, #__lms_quiz_r_student_blank as c";
							$query .= "\n WHERE q.c_id = c.c_sq_id AND q.c_question_id = '".$row->c_id."'";
							$query .= "\n AND a.c_id = q.c_stu_quiz_id";
							if($group_id){
								$query .= "\n AND a.c_student_id IN (".$str_user_in_groups.")";
							}
							$JLMS_DB->setQuery( $query );
							$notdubl_answer = $JLMS_DB->loadObjectList();
							for ($j=0;$j<count($notdubl_answer);$j++)
							{
								$query = "SELECT COUNT(*) FROM #__lms_quiz_r_student_quiz as a, #__lms_quiz_r_student_question as q, #__lms_quiz_r_student_blank as c";
								$query .= "\n WHERE q.c_id = c.c_sq_id AND q.c_question_id = '".$row->c_id."' AND c.c_answer = '".$notdubl_answer[$j]->c_answer."'";
								$query .= "\n AND a.c_id = q.c_stu_quiz_id";
								if($group_id){
									$query .= "\n AND a.c_student_id IN (".$str_user_in_groups.")";
								}
								$JLMS_DB->setQuery( $query );
								$local_count = $JLMS_DB->loadResult();
								$popualr[$j][0] = $local_count;
								$popualr[$j][1] = $notdubl_answer[$j]->c_answer;
							}
							if(count($popualr))
							{
								$chart = new MultiVerticalChart(600, 200, 1);
								$chart->maxval = 100;
								$chart->maintitle = '';
								rsort($popualr);
								for ($j=0;$j<$display_count;$j++)
								{
									if(!empty($popualr[$j][0]))
									{
										$row_percent = round(($popualr[$j][0]*100)/$total_count);
										$chart->addPoint($z, new Point(trim(strip_tags($popualr[$j][1])), $row_percent), $popualr[$j][0]);
									}
								
								}
								$chart->usr_answers[$z] = $i+1;
								$chart->titles[$z] = '';
								$filename = time() . '_' . md5(uniqid(rand(), true)) . ".png";
								//$this->clearOldImages(1);		
								$chart->render($JLMS_CONFIG->get('temp_folder', '').'/'.$filename);
								$obj->img_graph = $JLMS_CONFIG->get('temp_folder', '').'/'.$filename;
								$obj->title_graph = trim(strip_tags($row->c_question));
								$obj->count_graph = 1;
							}
						}
					}
					else 
					{
						$query = "SELECT c.* FROM #__lms_quiz_r_student_quiz as a, #__lms_quiz_r_student_question as q, #__lms_quiz_r_student_blank as c";
						$query .= "\n WHERE q.c_id = c.c_sq_id AND q.c_question_id = '".$row->c_id."'";
						$query .= "\n AND a.c_id = q.c_stu_quiz_id";
						if($group_id){
							$query .= "\n AND a.c_student_id IN (".$str_user_in_groups.")";
						}
						$JLMS_DB->setQuery( $query );
						$total_answer = $JLMS_DB->loadObjectList();
						$total_count = count($total_answer);
						if($total_count)
						{
							$query = "SELECT c_text FROM  #__lms_quiz_t_blank as b, #__lms_quiz_t_text as t ";
							$query .= "\n WHERE b.c_id = t.c_blank_id AND b.c_question_id = '".$row->c_id."'";
							//$query .= "\n AND a.c_id = b.c_stu_quiz_id";
							if($group_id){
								//$query .= "\n AND a.c_student_id IN (".$str_user_in_groups.")";
							}
							$JLMS_DB->setQuery( $query );
							$r_answer = $JLMS_DB->loadObjectList();
							$right_answers = array();
							for($g=0;$g<count($r_answer);$g++)
							{
								$right_answers[] = $r_answer[$g]->c_text;
							}
							if(count($right_answers))
							{
								$total_right = 0;
								for($g=0;$g<count($total_answer);$g++)
								{
									if(in_array($total_answer[$g]->c_answer,$right_answers))
										$total_right++;
								}
								$row_percent = round(($total_right*100)/$total_count);
								$row_percent_wrong = 100-$row_percent;
								$chart = new MultiVerticalChart(600, 200, 1);
								$chart->maxval = 100;
								$chart->maintitle = '';
								$chart->addPoint($z, new Point(_JLMS_GRAPH_STATISTICS_CORRECT, $row_percent), $total_right);
								$chart->addPoint($z, new Point(_JLMS_GRAPH_STATISTICS_INCORRECT, $row_percent_wrong), ($total_count - $total_right));
								$chart->usr_answers[$z] = $i+1;
								$chart->titles[$z] = '';
								$filename = time() . '_' . md5(uniqid(rand(), true)) . ".png";
								$chart->render($JLMS_CONFIG->get('temp_folder', '').'/'.$filename);
								$obj->img_graph = $JLMS_CONFIG->get('temp_folder', '').'/'.$filename;
								$obj->title_graph = trim(strip_tags($row->c_question));
								$obj->count_graph = 1;
							}
						}
					}	
					
			break;
			case '7':
					$query = "SELECT c.* FROM #__lms_quiz_r_student_quiz as a, #__lms_quiz_r_student_question as q, #__lms_quiz_r_student_hotspot as c";
					$query .= "\n WHERE q.c_id = c.c_sq_id AND q.c_question_id = '".$row->c_id."'";	
					$query .= "\n AND a.c_id = q.c_stu_quiz_id";
					if($group_id){
						$query .= "\n AND a.c_student_id IN (".$str_user_in_groups.")";
					}
					$JLMS_DB->SetQuery( $query );
					$total_answer = $JLMS_DB->LoadObjectList();
					$total_count = count($total_answer);
					$right_answers = 0;
					if($total_count)
					{
						$query = "SELECT a.c_point, a.c_attempts, b.c_start_x, b.c_start_y, b.c_width, b.c_height FROM #__lms_quiz_t_question as a, #__lms_quiz_t_hotspot as b WHERE a.c_id = '".$row->c_id."' and b.c_question_id = a.c_id";
						$JLMS_DB->SetQuery( $query );
						$ddd = $JLMS_DB->LoadObjectList();
						if(count($ddd)) 
						{
							for ($g=0;$g<$total_count;$g++)
							{
								if ( ($total_answer[$g]->c_select_x >= $ddd[0]->c_start_x) && ($total_answer[$g]->c_select_x <= ($ddd[0]->c_start_x+$ddd[0]->c_width)) && ($total_answer[$g]->c_select_y >= $ddd[0]->c_start_y) && ($total_answer[$g]->c_select_y <= ($ddd[0]->c_start_y+$ddd[0]->c_height)) ) 
								{
									$right_answers++;
								}
							}
							$row_percent = round(($right_answers*100)/$total_count);
							$row_percent_wrong = 100-$row_percent;
							$chart = new MultiVerticalChart(600, 200, 1);
							$chart->maxval = 100;
							$chart->maintitle = '';
							$chart->addPoint($z, new Point(_JLMS_GRAPH_STATISTICS_CORRECT, $row_percent), $right_answers);
							$chart->addPoint($z, new Point(_JLMS_GRAPH_STATISTICS_INCORRECT, $row_percent_wrong), ($total_count - $right_answers));
							$chart->usr_answers[$z] = $i+1;
							$chart->titles[$z] = '';
							$filename = time() . '_' . md5(uniqid(rand(), true)) . ".png";
							//$this->clearOldImages(1);		
							$chart->render($JLMS_CONFIG->get('temp_folder', '').'/'.$filename);
							$obj->img_graph = $JLMS_CONFIG->get('temp_folder', '').'/'.$filename;
							$obj->title_graph = trim(strip_tags($row->c_question));
							$obj->count_graph = 1;
						}
					}
							
			break;
			case '8':
				$extra = $group_id ? "&group_id=".$group_id."" : '';
				$obj->img_is_survey = 1;
				$obj->title_quest_survey = trim(strip_tags($row->c_question));
				$obj->title_link_survey = sefRelToAbs('index.php?option='.$option.'&Itemid='.$JLMS_CONFIG->get('Itemid').'&task=quizzes&page=view_answ_survey&id='.$id.'&quiz_id='.$quiz_id.'&quest_id='.$row->c_id.''.$extra);
			break;	
			
			/*
			Max: Den skazal ne vivodit etott tip voprosa
			Max:-(10.12.08)- prishel task vivesti etot tip
			*/
			case '9':
					$query = "SELECT * FROM #__lms_quiz_t_scale";
					$query .= "\n WHERE c_question_id = '".$row->c_id."' ORDER BY ordering";	
					$JLMS_DB->SetQuery( $query );
					$total_quest = $JLMS_DB->LoadObjectList();
					$scale_quest = array();
					$scale_answ = array();
					$quest_text = array();
					$answ_text = array();

					for($g=0;$g<count($total_quest);$g++)
					{
						if($total_quest[$g]->c_type)
						{
							$scale_answ[] = $total_quest[$g]->c_id;
							$answ_text[] = $total_quest[$g]->c_field;	
						}
						else
						{
							$scale_quest[] = $total_quest[$g]->c_id;
							$quest_text[] = $total_quest[$g]->c_field;	
							
						}	
					}
					if(count($total_quest))
					{
						$scale_answ_id = implode(',',$scale_answ);
						if(count($scale_quest))
						{
							$chart = new MultiVerticalChart(600, 200*count($scale_quest), count($scale_quest));
							$chart->maxval = 100;
							$chart->maintitle = '';

							for($g=0;$g<count($scale_quest);$g++)
							{
								$query = "SELECT COUNT(*) FROM #__lms_quiz_r_student_quiz as a, #__lms_quiz_r_student_question as q, #__lms_quiz_r_student_scale as c";
								$query .= "\n WHERE q.c_id = c.c_sq_id AND q.c_question_id = '".$row->c_id."'";	
								$query .= "\n AND c.q_scale_id = '".$scale_quest[$g]."' AND scale_id IN (".$scale_answ_id.")";
								$query .= "\n AND a.c_id = q.c_stu_quiz_id";
								if($group_id){
									$query .= "\n AND a.c_student_id IN (".$str_user_in_groups.")";
								}
								$JLMS_DB->SetQuery( $query );
								$total_answer = $JLMS_DB->LoadResult();

								for($t=0;$t<count($scale_answ);$t++)
								{	
									$query = "SELECT COUNT(*) FROM #__lms_quiz_r_student_quiz as a, #__lms_quiz_r_student_question as q, #__lms_quiz_r_student_scale as c";
									$query .= "\n WHERE q.c_id = c.c_sq_id AND q.c_question_id = '".$row->c_id."'";	
									$query .= "\n AND c.q_scale_id = '".$scale_quest[$g]."' AND scale_id = '".$scale_answ[$t]."'";
									$query .= "\n AND a.c_id = q.c_stu_quiz_id";
									if($group_id){
										$query .= "\n AND a.c_student_id IN (".$str_user_in_groups.")";
									}
									$JLMS_DB->SetQuery( $query );
									$count_answer = $JLMS_DB->LoadResult();	

									if($total_answer) {
										$row_percent = round(($count_answer*100)/$total_answer);
									}
									else {
										$row_percent = 0;
										$count_answer = 0;
									}

									$chart->addPoint($z, new Point(trim(strip_tags($answ_text[$t])), $row_percent), $count_answer);
								
								}
								$chart->usr_answers[$z] = $g+1;
								$chart->titles[$z] = trim(strip_tags($quest_text[$g]));
								$z++;

							}
							if($z>1)
							{
								$filename = time() . '_' . md5(uniqid(rand(), true)) . ".png";
								$chart->render($JLMS_CONFIG->get('temp_folder', '').'/'.$filename);
								$obj->img_graph = $JLMS_CONFIG->get('temp_folder', '').'/'.$filename;
								$obj->title_graph = trim(strip_tags($row->c_question));
								$obj->count_graph = count($scale_quest);
							}
						}
					}
					
					/* This graph not use
					if($z>1){
						$z=1;
						$query = "SELECT COUNT(*) FROM #__lms_quiz_r_student_quiz as a, #__lms_quiz_r_student_question as q";
						$query .= "\n WHERE q.c_question_id = '".$row->c_id."'";	
						$query .= "\n AND a.c_id = q.c_stu_quiz_id";
						if($group_id){
							$query .= "\n AND a.c_student_id IN (".$str_user_in_groups.")";
						}
						$JLMS_DB->setQuery($query);
						$answers_count = $JLMS_DB->loadResult();

						$query = "SELECT a.c_student_id, q.* FROM #__lms_quiz_r_student_quiz as a, #__lms_quiz_r_student_question as q";
						$query .= "\n WHERE q.c_question_id = '".$row->c_id."'";	
						$query .= "\n AND a.c_id = q.c_stu_quiz_id";
						if($group_id){
							$query .= "\n AND a.c_student_id IN (".$str_user_in_groups.")";
						}
						$JLMS_DB->setQuery($query);
						$answers_students = $JLMS_DB->loadObjectList();

						$right_answ = 0;
						foreach($answers_students as $answ_stu){
							if($answ_stu->c_correct == 2){
								$right_answ = $right_answ + 1;	
							}
						}
						$no_right_answ = $answers_count - $right_answ;
						$chart = new MultiVerticalChart(600, 200, 1);
						$chart->maxval = 100;
						$chart->maintitle = '';
						$row_percent = $answers_count ? round(($right_answ*100)/$answers_count) : 0;
						$chart->addPoint($z, new Point(_JLMS_GRAPH_STATISTICS_CORRECT, $row_percent), $right_answ);
						$row_percent = $answers_count ? round(($no_right_answ*100)/$answers_count) : 0;
						$chart->addPoint($z, new Point(_JLMS_GRAPH_STATISTICS_INCORRECT, $row_percent), $no_right_answ);
						$chart->usr_answers[$z] = '';
						$chart->titles[$z] = '';
						$filename = $row->c_type . '_' . time() . '_' . md5(uniqid(rand(), true)) . ".png";
						$chart->render($JLMS_CONFIG->get('temp_folder', '').'/'.$filename);
						$obj->img_correct = $JLMS_CONFIG->get('temp_folder', '').'/'.$filename;
						$obj->title_correct = trim(strip_tags($row->c_question));
						$obj->count_correct = 1; 
					}
					*/
			break;
		}
	}
	return $obj;
}
?>