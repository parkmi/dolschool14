<?php
defined( '_JLMS_EXEC' ) or die( 'Restricted access' );

define('_FLMS_ERROR_NO_CORRECT_TIME','Incorrect time. Time should be proportional to 15 minutes.');
define('_FLMS_ERROR_SELECT_OPERATION','Select operation.');
define('_FLMS_ERROR_INCORRECT_FORMAT','Incorrect format.');
define('_FLMS_ERROR_DURATION_TIME','Please enter Duration time.');
define('_FLMS_ERROR_NULL_FIELDS','One should be fill though one field.'); // wtf??? 
define('_FLMS_ERROR_PF_TIME','Please enter PF time.');
define('_FLMS_ERROR_PM_TIME','Please enter PM time.');
define('_FLMS_ERROR_DEBRIEFING_TIME','Please enter Debriefing time.');
define('_FLMS_ERROR_BRIEFING_TIME','Please enter Brefing time. Student #');

function FLMS_params_lesson($id, $type_lesson){
	global $JLMS_DB;
	
	$course_id = $id;
	$row = new mos_FLMS_Course_load( $JLMS_DB );
	$row->load( $id );
	
	if(!$id){
		$row->f_id = 0;
		$row->type_lesson = $type_lesson;
		$row->operation = (isset($_REQUEST['flms_operation']) && $_REQUEST['flms_operation'])?$_REQUEST['flms_operation']:0;
	} else {
		$row->type_lesson = (isset($row->type_lesson) && $row->type_lesson == $type_lesson)?$row->type_lesson:$type_lesson;
	}
	$like_theory = mosGetParam( $_REQUEST, 'flms_like_theory', ($id ? $row->like_theory : 0) );
	
	$disabled_like_theory = 0;
	if(isset($row->f_id) && $row->f_id){
		$query= "SELECT count(*) FROM #__lmsb_booking WHERE lesson_id = '".$row->course_id."'";
		$JLMS_DB->setQuery($query);
		$disabled_like_theory = $JLMS_DB->loadResult();	
	}
	
	
	$lists = array();
	$lists['like_theory'] = $like_theory;
	$lists['disabled_like_theory'] = $disabled_like_theory;
	$query = "SELECT id as value, name as text FROM #__lmsf_operations";
	$JLMS_DB->setQuery($query);
	$course_operations = $JLMS_DB->loadObjectList();
	$operation_mass = array();
	$operation_mass[] = mosHTML::makeOption(0, 'Select operation');
	$operation_mass = array_merge($operation_mass, $course_operations);
	
	$disabled = ($row->type_lesson==1 || $row->type_lesson==3)?'disabled="disabled"':($row->type_lesson==2 && $like_theory?'disabled="disabled"':($row->type_lesson==0?'disabled="disabled"':''));
	$lists['operation'] = mosHTML::selectList( $operation_mass, 'flms_operation', 'class="inputbox" size="1" '.$disabled, 'value', 'text', $id?(isset($_REQUEST['flms_operation'])?$_REQUEST['flms_operation']:$row->operation):$row->operation ); 
	
	$query = "SELECT cat_name FROM #__lms_course_cats_config WHERE id = '1'";
	$JLMS_DB->setQuery($query);
	$lists['title_main_category'] = $JLMS_DB->loadResult();
	
	FLMS_course_html::viewFParametrs($row, $lists);
}

function FLMS_save_params_lesson($id){
	global $JLMS_DB;
	
	$save_mass = array();
	foreach($_POST as $key=>$item){
		if(substr($key, 0, 5) == 'flms_'){
			$save_mass[substr($key, 5)] = $item;
		}	
	}
	
	$save_mass['course_id'] = $id;
	if(!isset($save_mass['like_theory'])){
		$save_mass['like_theory'] = 0;	
	}
	if(!isset($save_mass['no_instructor'])){
		$save_mass['no_instructor'] = 0;	
	}
	if(!isset($save_mass['no_room'])){
		$save_mass['no_room'] = 0;	
	}
	
	$row = new mos_FLMS_Course_save( $JLMS_DB );
	if (!$row->bind( $save_mass )) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}

	if (!$row->check()) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}
	
	if (!$row->store()) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}
	
	return;
}

function FLMS_save_multicat($id){
	global $JLMS_DB;
	
	$query = "SELECT * FROM #__lms_course_level WHERE course_id = '".$id."'";
	$JLMS_DB->setQuery($query);
	$prover_mass = $JLMS_DB->loadObjectList();
	
	$save_mass = array();
	$i=0;
	foreach($_POST as $key=>$item){
		if(substr($key, 0, 9) == 'level_id_' && $key != 'level_id_0'){
			if($item){
				$save_mass[$i]['id'] = isset($prover_mass[$i]->id)?$prover_mass[$i]->id:'';
				$save_mass[$i]['course_id'] = $id;	
				$save_mass[$i]['cat_id'] = $item;	
				$save_mass[$i]['level'] = substr($key, 9);	
				$i++;
			}
		}	
	}
	
	if(count($save_mass)){
		foreach($save_mass as $data){
			$row = new mos_Joomla_LMS_Multicat( $JLMS_DB );
			if (!$row->bind( $data )) {
				echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
				exit();
			}
		
			if (!$row->check()) {
				echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
				exit();
			}
			
			if (!$row->store()) {
				echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
				exit();
			}
		}
	} else {
		$query = "DELETE FROM #__lms_course_level WHERE course_id = '".$id."'";
		$JLMS_DB->setQuery($query);
		$JLMS_DB->query();
	}
	return;
}

function FLMS_delete_params_lesson($id){
	global $JLMS_DB;
	$query = "DELETE FROM #__lmsf_courses WHERE course_id = '".$id."'";
	$JLMS_DB->SetQuery( $query );
	$JLMS_DB->query();
}

function FLMS_delete_multicat($id){
	global $JLMS_DB;
	$query = "DELETE FROM #__lms_course_level WHERE course_id = '".$id."'";
	$JLMS_DB->SetQuery( $query );
	$JLMS_DB->query();
}

function FLMS_cndts_aircraft_in_course($course_id, $type_lesson, $like_theory=0){
	global $JLMS_DB;
	
	$query = "SELECT * FROM #__lmsf_conditions_air WHERE published = '1' ORDER BY id";
	$JLMS_DB->setQuery($query);
	$rows = $JLMS_DB->loadObjectList();
	$query = "SELECT * FROM #__lmsf_course_conditions WHERE course_id = '".$course_id."' ORDER BY condition_id";
	$JLMS_DB->setQuery($query);
	$values = $JLMS_DB->loadObjectList();

	$i=0;
	foreach($rows as $row){
		foreach($values as $value){
			if($row->id == $value->condition_id){
				$rows[$i]->condition_value = $value->condition_value;
				$i++;
			}	
		}	
	}
	FLMS_course_html::FLMS_conditions_aircraft_in_course($rows, $type_lesson, $like_theory);
}

function FLMS_r_stu_in_course($course_id, $type_lesson, $like_theory=0){
	global $JLMS_DB;
	
	$query = "SELECT * FROM #__lmsf_qualifications_stu WHERE published = '1' ORDER BY id";
	$JLMS_DB->setQuery($query);
	$rows = $JLMS_DB->loadObjectList();
	$query = "SELECT * FROM #__lmsf_qualifications_stu_course WHERE course_id = '".$course_id."' ORDER BY q_id";
	$JLMS_DB->setQuery($query);
	$values = $JLMS_DB->loadObjectList();

	$i=0;
	foreach($rows as $row){
		foreach($values as $value){
			if($row->id == $value->q_id){
				$rows[$i]->q_value = $value->q_value;
				$i++;
			}	
		}	
	}
	FLMS_course_html::FLMS_show_r_stu_in_course($rows, $type_lesson, $like_theory);
}

function FLMS_r_inst_in_course($course_id, $type_lesson, $like_theory=0){
	global $JLMS_DB;
	
	$query = "SELECT * FROM #__lmsf_qualifications_inst WHERE published = '1' ORDER BY id";
	$JLMS_DB->setQuery($query);
	$rows = $JLMS_DB->loadObjectList();
	$query = "SELECT * FROM #__lmsf_qualifications_inst_course WHERE course_id = '".$course_id."' ORDER BY q_id";
	$JLMS_DB->setQuery($query);
	$values = $JLMS_DB->loadObjectList();

	$i=0;
	foreach($rows as $row){
		foreach($values as $value){
			if($row->id == $value->q_id){
				$rows[$i]->q_value = $value->q_value;
				$i++;
			}	
		}	
	}
	FLMS_course_html::FLMS_show_r_inst_in_course($rows, $type_lesson, $like_theory);
}

function FLMS_cndts_aircraft_in_course_save($course_id){
	global $JLMS_DB;
	
	$query = "SELECT * FROM #__lmsf_course_conditions WHERE course_id = '".$course_id."' ORDER BY condition_id";
	$JLMS_DB->setQuery($query);
	$rows = $JLMS_DB->loadObjectList();
	
	$conditions = array();
	$i=0;
	foreach($_REQUEST as $key=>$item){
		if(substr($key, 0, 10) == 'condition_'){
			$condition_id = intval(substr($key, 10));
			$conditions[$i]->id = '';
			$conditions[$i]->course_id = $course_id;
			$conditions[$i]->condition_id = $condition_id;
			$conditions[$i]->condition_value = $item;
			$i++;
		}
	}
	
	$query = "SELECT * FROM #__lmsf_course_conditions WHERE course_id = '".$course_id."' ORDER BY condition_id";
	$JLMS_DB->setQuery($query);
	$current_cndts = $JLMS_DB->loadObjectList();
	
	$curr_cid = array();
	foreach($current_cndts as $a){
		$curr_cid[] = $a->condition_id;	
	}
	
	$cid = array();
	foreach($conditions as $a){
		$cid[] = $a->condition_id;	
	}
	
	$add_result = array();
	$updt_result = array();
	$del_result = array();
	foreach($conditions as $data){
		if(!in_array($data->condition_id, $curr_cid)){
			$add_result[] = $data;
		}
		if(in_array($data->condition_id, $curr_cid)){
			$updt_result[] = $data;
		}
	}
	foreach($current_cndts as $current_cndt){
		if(!in_array($current_cndt->condition_id, $cid)){
			$del_result[] = $current_cndt;
		} 
	}
	
	if(count($add_result)){
		foreach($add_result as $add){
			$query = "INSERT INTO #__lmsf_course_conditions (id, course_id, condition_id, condition_value) VALUES ('', ".$add->course_id.", ".$add->condition_id.", ".$add->condition_value.")";
			$JLMS_DB->setQuery($query);
			$JLMS_DB->query();	
		}	
	}
	if(count($updt_result)){
		foreach($updt_result as $updt){
			$query = "UPDATE #__lmsf_course_conditions SET condition_value = '".$updt->condition_value."' WHERE condition_id = '".$updt->condition_id."' AND course_id = '".$course_id."'";	
			$JLMS_DB->setQuery($query);
			$JLMS_DB->query();
		}
	}
	if(count($del_result)){
		foreach($del_result as $del){
			$query = "DELETE FROM #__lmsf_course_conditions WHERE id = '".$del->id."' AND course_id = '".$course_id."'";
			$JLMS_DB->setQuery($query);
			$JLMS_DB->query();
		}
	}
	return;
}

function FLMS_r_inst_in_course_save($course_id){
	global $JLMS_DB;
	
	$query = "SELECT * FROM #__lmsf_qualifications_inst_course WHERE course_id = '".$course_id."' ORDER BY q_id";
	$JLMS_DB->setQuery($query);
	$rows = $JLMS_DB->loadObjectList();
	
	$conditions = array();
	$i=0;
	foreach($_REQUEST as $key=>$item){
		if(substr($key, 0, 7) == 'inst_q_'){
			$condition_id = intval(substr($key, 7));
			$conditions[$i]->id = '';
			$conditions[$i]->course_id = $course_id;
			$conditions[$i]->q_id = $condition_id;
			$conditions[$i]->q_value = $item;
			$i++;
		}
	}
	
	$query = "SELECT * FROM #__lmsf_qualifications_inst_course WHERE course_id = '".$course_id."' ORDER BY q_id";
	$JLMS_DB->setQuery($query);
	$current_cndts = $JLMS_DB->loadObjectList();
	
	$curr_cid = array();
	foreach($current_cndts as $a){
		$curr_cid[] = $a->q_id;	
	}
	
	$cid = array();
	foreach($conditions as $a){
		$cid[] = $a->q_id;	
	}
	
	$add_result = array();
	$updt_result = array();
	$del_result = array();
	foreach($conditions as $data){
		if(!in_array($data->q_id, $curr_cid)){
			$add_result[] = $data;
		}
		if(in_array($data->q_id, $curr_cid)){
			$updt_result[] = $data;
		}
	}
	foreach($current_cndts as $current_cndt){
		if(!in_array($current_cndt->q_id, $cid)){
			$del_result[] = $current_cndt;
		} 
	}
	
	if(count($add_result)){
		foreach($add_result as $add){
			$query = "INSERT INTO #__lmsf_qualifications_inst_course (id, course_id, q_id, q_value) VALUES ('', ".$add->course_id.", ".$add->q_id.", ".$add->q_value.")";
			$JLMS_DB->setQuery($query);
			$JLMS_DB->query();	
		}	
	}
	if(count($updt_result)){
		foreach($updt_result as $updt){
			$query = "UPDATE #__lmsf_qualifications_inst_course SET q_value = '".$updt->q_value."' WHERE q_id = '".$updt->q_id."' AND course_id = '".$course_id."'";	
			$JLMS_DB->setQuery($query);
			$JLMS_DB->query();
		}
	}
	if(count($del_result)){
		foreach($del_result as $del){
			$query = "DELETE FROM #__lmsf_qualifications_inst_course WHERE id = '".$del->id."' AND course_id = '".$course_id."'";
			$JLMS_DB->setQuery($query);
			$JLMS_DB->query();
		}
	}
	return;
}

function FLMS_r_stu_in_course_save($course_id){
	global $JLMS_DB;
	
	$query = "SELECT * FROM #__lmsf_qualifications_stu_course WHERE course_id = '".$course_id."' ORDER BY q_id";
	$JLMS_DB->setQuery($query);
	$rows = $JLMS_DB->loadObjectList();
	
	$conditions = array();
	$i=0;
	foreach($_REQUEST as $key=>$item){
		if(substr($key, 0, 6) == 'stu_q_'){
			$condition_id = intval(substr($key, 6));
			$conditions[$i]->id = '';
			$conditions[$i]->course_id = $course_id;
			$conditions[$i]->q_id = $condition_id;
			$conditions[$i]->q_value = $item;
			$i++;
		}
	}
	
	$query = "SELECT * FROM #__lmsf_qualifications_stu_course WHERE course_id = '".$course_id."' ORDER BY q_id";
	$JLMS_DB->setQuery($query);
	$current_cndts = $JLMS_DB->loadObjectList();
	
	$curr_cid = array();
	foreach($current_cndts as $a){
		$curr_cid[] = $a->q_id;	
	}
	
	$cid = array();
	foreach($conditions as $a){
		$cid[] = $a->q_id;	
	}
	
	$add_result = array();
	$updt_result = array();
	$del_result = array();
	foreach($conditions as $data){
		if(!in_array($data->q_id, $curr_cid)){
			$add_result[] = $data;
		}
		if(in_array($data->q_id, $curr_cid)){
			$updt_result[] = $data;
		}
	}
	foreach($current_cndts as $current_cndt){
		if(!in_array($current_cndt->q_id, $cid)){
			$del_result[] = $current_cndt;
		} 
	}
	
	if(count($add_result)){
		foreach($add_result as $add){
			$query = "INSERT INTO #__lmsf_qualifications_stu_course (id, course_id, q_id, q_value) VALUES ('', ".$add->course_id.", ".$add->q_id.", ".$add->q_value.")";
			$JLMS_DB->setQuery($query);
			$JLMS_DB->query();	
		}	
	}
	if(count($updt_result)){
		foreach($updt_result as $updt){
			$query = "UPDATE #__lmsf_qualifications_stu_course SET q_value = '".$updt->q_value."' WHERE q_id = '".$updt->q_id."' AND course_id = '".$course_id."'";	
			$JLMS_DB->setQuery($query);
			$JLMS_DB->query();
		}
	}
	if(count($del_result)){
		foreach($del_result as $del){
			$query = "DELETE FROM #__lmsf_qualifications_stu_course WHERE id = '".$del->id."' AND course_id = '".$course_id."'";
			$JLMS_DB->setQuery($query);
			$JLMS_DB->query();
		}
	}
	return;
}

?>