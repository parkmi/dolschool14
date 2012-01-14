<?php

defined( '_JLMS_EXEC' ) or die( 'Restricted access' );

class FLMS_course_html {
	
	
	function viewFParametrs($row, $lists){
		$all_disabled = 0;		
		if($row->type_lesson == 0){
			$all_disabled = 1;
		}
		?>
		<table width="288">
			<tr>
				<th colspan="6">
					<p align="left" class="style8">Type of course</p>
				</th>
			</tr>
			<tr>
				<td width="24">&nbsp;<!--x--></td>
				<td>
					<?php
					$row_ltypes = array();
					$row_ltypes[0] = 'Please select <a href="javascript:document.adminForm.level_id_0.focus();">'.$lists['title_main_category'].'</a>';
					$row_ltypes[1] = 'theory';
					$row_ltypes[2] = 'flight';
					$row_ltypes[3] = 'other';
					if($row->course_id){
						for($i=0;$i<5;$i++){
							if($row->type_lesson == $i){
								echo ucfirst($row_ltypes[$i]);	
							}
						}
					} else {
						for($i=0;$i<5;$i++){
							if($row->type_lesson == $i){
								echo ucfirst($row_ltypes[$i]);	
							}
						}	
					}
					?>
					<input type="hidden" name="flms_type_lesson" value="<?php echo $row->type_lesson;?>" />
				</td>
			</tr>
			<?php
			if($row->type_lesson == 2){
			?>
			<tr>
				<td valign="top">
					<input type="checkbox" name="flms_like_theory_view" value="1" onchange="if(document.adminForm.flms_like_theory_view && document.adminForm.flms_like_theory_view.checked){document.adminForm.flms_like_theory.value = 1} else {document.adminForm.flms_like_theory.value = 0}document.adminForm.submit();" <?php echo intval($lists['like_theory'])?'checked="cheked"':'';?> <?php echo (intval($lists['disabled_like_theory']))?'disabled="disabled"':'';?> />
					<input type="hidden" name="flms_like_theory" value="<?php echo intval($lists['like_theory']);?>" />
				</td>
				<td valign="top">
					"like Theory"
					<?php
					if(isset($lists['disabled_like_theory']) && $lists['disabled_like_theory']){
					?>
					<br />
					<small>This course has a booking, this setting can not be changed.</small>
					<?php
					}
					?>
				</td>
			</tr>
			<?php
			}
			?>
		</table>
		
		
		<table width="290">
			<tr>
				<th colspan="2">
					<p align="left" class="style8">Theory Course Conditions</p>
				</th>
			</tr>
			<tr>
				<td width="24">&nbsp;</td>
				<td width="241">
					<table width="100%"  border="0">
						<tr class="style15">
							<th width="40%" scope="col">
								<div align="left">Duration</div>
							</th>
							<td width="19%" scope="col">
								<div align="left">
								<input name="flms_theory_duration_time" type="text" size="4" value="<?php echo $row->course_id?($row->theory_duration_time?$row->theory_duration_time:''):'';?>" <?php echo ($row->type_lesson!=1 && $row->type_lesson!=3 && !intval($lists['like_theory']))?'disabled="disabled"':($all_disabled?'disabled="disabled"':'');?> />
								</div>
							</td>
							<td width="41%" scope="col">
								min
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		
		
		<table width="312">
			<tr>
				<th colspan="2"><p align="left" class="style8">Flight Course Conditions</p></th>
			</tr>
			<tr>
				<td width="24">&nbsp;</td>
				<td width="276">
					<table width="100%"  border="0">
						<tr class="style15">
							<th width="34%" scope="col">
								<div align="left"># Student</div>
							</th>
							<th width="29%" scope="col">
								<div align="left">Pre-filght briefing</div>
							</th>
							<th width="36%" scope="col">
								<div align="left">Additional Block Time</div>
							</th>
						</tr>
						<?php
						for($i=1;$i<5;$i++){
								$stu_briefing_time = 'stu_'.$i.'_briefing_time';
								$stu_additional_time = 'stu_'.$i.'_additional_time';
						?>
						<tr class="style15">
							<td>
								<div align="left"><?php echo $i;?> student</div>
							</td>
							<td>
								<div align="left">
									<input name="flms_stu_<?php echo $i;?>_briefing_time" type="text" value="<?php echo $row->course_id?(isset($row->$stu_briefing_time)?$row->$stu_briefing_time:''):'';?>" size="4" <?php echo ($row->type_lesson==1 || $row->type_lesson==3)?'disabled="disabled"':($row->type_lesson==2 && intval($lists['like_theory'])?'disabled="disabled"':($all_disabled?'disabled="disabled"':''));?> /> min
								</div>
							</td>
							<td>
								<div align="left">
									<input name="flms_stu_<?php echo $i;?>_additional_time" type="text" value="<?php echo $row->course_id?(isset($row->$stu_additional_time)?$row->$stu_additional_time:''):'';?>" size="4" <?php echo ($row->type_lesson==1 || $row->type_lesson==3)?'disabled="disabled"':($row->type_lesson==2 && intval($lists['like_theory'])?'disabled="disabled"':($all_disabled?'disabled="disabled"':''));?> /> min
								</div>
							</td>
						</tr>
						<?php
						}
						?>
					</table>
					<br />
					
					<table width="100%"  border="0">
						<tr class="style15">
							<th width="91">
								<div align="left">Block PF</div>
							</th>
							<td width="19%">
								<div align="left">
									<input name="flms_pf_time" type="text" size="4" value="<?php echo $row->course_id?( isset($_REQUEST['flms_pf_time'])?$_REQUEST['flms_pf_time']:$row->pf_time ):'';?>"  <?php echo ($row->type_lesson==1 || $row->type_lesson==3)?'disabled="disabled"':($row->type_lesson==2 && intval($lists['like_theory'])?'disabled="disabled"':($all_disabled?'disabled="disabled"':''));?> />
								</div>
							</td>
							<td class="style15">
								min
							</td>
						</tr>
						<tr class="style15">
							<th width="91">
								<div align="left">Block PM</div>
							</th>
							<td width="19%">
								<div align="left">
									<input name="flms_pm_time" type="text" size="4" value="<?php echo $row->course_id?( isset($_REQUEST['flms_pm_time'])?$_REQUEST['flms_pm_time']:$row->pm_time ):'';?>" <?php echo ($row->type_lesson==1 || $row->type_lesson==3)?'disabled="disabled"':($row->type_lesson==2 && intval($lists['like_theory'])?'disabled="disabled"':($all_disabled?'disabled="disabled"':''));?> />
								</div>
							</td>
							<td class="style15">
								min
							</td>
						</tr>
					</table>
					<br />
					<table width="100%" border="0">
						<tr class="style15">
							<th width="91" height="24">
								<div align="left">Debriefing</div>
							</th>
							<td width="19%">
								<div align="left">
									<input name="flms_debriefing_time" type="text" size="4" value="<?php echo $row->course_id?( isset($_REQUEST['flms_debriefing_time'])?$_REQUEST['flms_debriefing_time']:$row->debriefing_time ):'';?>" <?php echo ($row->type_lesson==1 || $row->type_lesson==3)?'disabled="disabled"':($row->type_lesson==2 && intval($lists['like_theory'])?'disabled="disabled"':($all_disabled?'disabled="disabled"':''));?> />
								</div>
							</td>
							<td class="style15">
								min
							</td>
						</tr>
					</table>
				</td>	
			</tr>
			</table>
		
			
			<table width="312">
				<tr>
					<th colspan="2"><p align="left" class="style8">Aircraft Requirements</p></th>
				</tr>
				<tr>
					<td width="24">&nbsp;</td>
					</td>
					<td width="276">
						<table border="0">
							<tbody>
							<tr>
								<th width="93" class="style15">
									<div align="left">Operation</div>
								</th>
								<td>
									<div align="left">
										<?php echo $lists['operation'];?>
									</div>
								</td>
							</tr>
							</tbody>
						</table>	
					</td>
				</tr>
				<tr>
					<td width="24">&nbsp;</td>
					<td width="276">
						<?php
						FLMS_cndts_aircraft_in_course($row->course_id, $row->type_lesson, intval($lists['like_theory']));
						?>
					</td>
				</tr>	
			</table>
			
			
			<table width="312">
				<tr>
					<th colspan="2"><p align="left" class="style8">Instructor Requirements</p></th>
				</tr>
				<tr>
					<td width="24">&nbsp;</td>
					<td width="276">
						<?php
						FLMS_r_inst_in_course($row->course_id, $row->type_lesson, intval($lists['like_theory']));
						?>
					</td>
				</tr>	
			</table>
			
			
			<table width="312">
			<tr>
				<th colspan="2"><p align="left" class="style8">Student Requirements</p></th>
			</tr>
			<tr>
				<td width="24">&nbsp;</td>
				<td width="276">
					<?php
					FLMS_r_stu_in_course($row->course_id, $row->type_lesson, intval($lists['like_theory']));
					?>	
				</td>
			</tr>	
			</table>

			
			<table width="312">
			<tr>
				<th colspan="2"><p align="left" class="style8">Solo flight lesson</p></th>
			</tr>
			<tr>
				<td width="24">&nbsp;</td>
				<td width="276">
					<table width="100%" border="0">
						<tr>
							<th width="94" height="24">
								<div align="left">
									Yes
								</div>
							</th>
							<th>
								<div align="left">
									<input type="checkbox" name="flms_solo_flight_lesson" value="1" class="inputbox" <?php echo $row->course_id?( isset($_REQUEST['solo_flight_lesson']) && $_REQUEST['solo_flight_lesson']?'checked="checked"':($row->solo_flight_lesson?'checked="checked"':'') ):'';?> <?php echo ($row->type_lesson==1 || $row->type_lesson==3)?'disabled="disabled"':($row->type_lesson==2 && intval($lists['like_theory'])?'disabled="disabled"':($all_disabled?'disabled="disabled"':''));?> />
								</div>
							</th>
						</tr>
					</table>	
				</td>
			</tr>	
			</table>
			
			
			<table width="312">
			<tr>
				<th colspan="2"><p align="left" class="style8">Home Study</p></th>
			</tr>
			<tr>
				<td width="24">&nbsp;</td>
				<td width="276">
					<table width="100%" border="0">
						<tr>
							<th width="94" height="24">
								<div align="left">
									No instructor
								</div>
							</th>
							<th>
								<div align="left">
									<input type="checkbox" name="flms_no_instructor" value="1" class="inputbox" <?php echo $row->course_id?( isset($_REQUEST['no_instructor']) && $_REQUEST['no_instructor']?'checked="checked"':($row->no_instructor?'checked="checked"':'') ):'';?> <?php echo ($row->type_lesson!=1 && $row->type_lesson!=3 && !intval($lists['like_theory']))?'disabled="disabled"':($all_disabled?'disabled="disabled"':'');?> />
								</div>
							</th>
						</tr>
						<tr>
							<th width="94" height="24">
								<div align="left">
									No room
								</div>
							</th>
							<th>
								<div align="left">
									<input type="checkbox" name="flms_no_room" value="1" class="inputbox" <?php echo $row->course_id?( isset($_REQUEST['no_room']) && $_REQUEST['no_room']?'checked="checked"':($row->no_room?'checked="checked"':'') ):'';?> <?php echo ($row->type_lesson!=1 && $row->type_lesson!=3 && !intval($lists['like_theory']))?'disabled="disabled"':($all_disabled?'disabled="disabled"':'');?> />
								</div>
							</th>
						</tr>
					</table>	
				</td>
			</tr>	
			</table>
			
			
			<table width="312">
			<tr>
				<th colspan="2"><p align="left" class="style8">Test lesson</p></th>
			</tr>
			<tr>
				<td width="24">&nbsp;</td>
				<td width="276">
					<table width="100%" border="0">
						<tr>
							<th width="94" height="24">
								<div align="left">
									Yes
								</div>
							</th>
							<th>
								<div align="left">
									<input type="checkbox" name="flms_test_lesson" value="1" class="inputbox" <?php echo $row->course_id?( isset($_REQUEST['test_lesson']) && $_REQUEST['test_lesson']?'checked="checked"':($row->test_lesson?'checked="checked"':'') ):'';?> <?php echo /*($row->type_lesson!=1 && $row->type_lesson!=3 && !intval($lists['like_theory']))?'disabled="disabled"':*/($all_disabled ? 'disabled="disabled"' : '');?> />
								</div>
							</th>
						</tr>
					</table>	
				</td>
			</tr>	
			</table>
						
		<input type="hidden" name="flms_f_id" value="<?php echo $row->f_id;?>"/>
		<?php	
	}
	
	function FLMS_conditions_aircraft_in_course($rows, $type_lesson, $like_theory=0){
		$all_disabled = 0;		
		if($type_lesson == 0){
			$all_disabled = 1;
		}
		?>
		<table width="100%" border="0">
		<?php
		for($i=0;$i<count($rows);$i++){
			$row = $rows[$i];
			$selected = isset($row->condition_value)?$row->condition_value:$row->default;
		?>
			<tr>
				<td width="35%">
					<b><?php echo ucfirst($row->name);?></b>
				</td>
				<td width="24">
					<input type="radio" name="condition_<?php echo $row->id;?>" value="0" <?php echo isset($_REQUEST['condition_'.$row->id]) ? ($_REQUEST['condition_'.$row->id]?'':'checked="checked"') : ($selected?'':'checked="checked"');?> <?php echo ($type_lesson==1 || $type_lesson==3)?'disabled="disabled"':($type_lesson==2 && $like_theory?'disabled="disabled"':($all_disabled?'disabled="disabled"':''));?> />
				</td>
				<td width="93">
					<?php echo ucfirst($row->value_1);?>
				</td>
				<td width="24">
					<input type="radio" name="condition_<?php echo $row->id;?>" value="1" <?php echo isset($_REQUEST['condition_'.$row->id]) ? ($_REQUEST['condition_'.$row->id]?'checked="checked"':'') : ($selected?'checked="checked"':'');?> <?php echo ($type_lesson==1 || $type_lesson==3)?'disabled="disabled"':($type_lesson==2 && $like_theory?'disabled="disabled"':($all_disabled?'disabled="disabled"':''));?> />
				</td>
				<td width="93">
					<?php echo ucfirst($row->value_2);?>
				</td>
			</tr>	
		<?php
		}
		?>
		</table>
		<?php
	}
	
	function FLMS_show_r_inst_in_course($rows, $type_lesson, $like_theory=0){
		$all_disabled = 0;		
		if($type_lesson == 0){
			$all_disabled = 1;
		}
		?>
		<table width="100%" border="0">
		<?php
		for($i=0;$i<count($rows);$i++){
			$row = $rows[$i];
			$selected = isset($row->q_value)?$row->q_value:$row->default;
		?>
			<tr>
				<td width="35%">
					<b><?php echo ucfirst($row->requirement);?></b>
				</td>
				<td width="24">
					<input type="radio" name="inst_q_<?php echo $row->id;?>" value="0" <?php echo isset($_REQUEST['inst_q_'.$row->id]) ? ($_REQUEST['inst_q_'.$row->id]?'':'checked="checked"') : ($selected?'':'checked="checked"');?> <?php echo ($type_lesson==1 || $type_lesson==3)?'disabled="disabled"':($type_lesson==2 && $like_theory?'disabled="disabled"':($all_disabled?'disabled="disabled"':''));?> />
				</td>
				<td width="93">
					<?php echo _CMN_NO;?>
				</td>
				<td width="24">
					<input type="radio" name="inst_q_<?php echo $row->id;?>" value="1" <?php echo isset($_REQUEST['inst_q_'.$row->id]) ? ($_REQUEST['inst_q_'.$row->id]?'checked="checked"':'') : ($selected?'checked="checked"':'');?> <?php echo ($type_lesson==1 || $type_lesson==3)?'disabled="disabled"':($type_lesson==2 && $like_theory?'disabled="disabled"':($all_disabled?'disabled="disabled"':''));?> />
				</td>
				<td width="93">
					<?php echo _CMN_YES;?>
				</td>
			</tr>	
		<?php
		}
		?>
		</table>
		<?php
	}
	
	function FLMS_show_r_stu_in_course($rows, $type_lesson, $like_theory=0){
		$all_disabled = 0;		
		if($type_lesson == 0){
			$all_disabled = 1;
		}
		?>
		<table width="100%" border="0">
		<?php
		for($i=0;$i<count($rows);$i++){
			$row = $rows[$i];
			$selected = isset($row->q_value)?$row->q_value:$row->default;
		?>
			<tr>
				<td width="35%">
					<b><?php echo ucfirst($row->requirement);?></b>
				</td>
				<td width="24">
					<input type="radio" name="stu_q_<?php echo $row->id;?>" value="0" <?php echo isset($_REQUEST['stu_q_'.$row->id]) ? ($_REQUEST['stu_q_'.$row->id]?'':'checked="checked"') : ($selected?'':'checked="checked"');?> <?php echo ($type_lesson==1 || $type_lesson==3)?'disabled="disabled"':($type_lesson==2 && $like_theory?'disabled="disabled"':($all_disabled?'disabled="disabled"':''));?> />
				</td>
				<td width="93">
					<?php echo _CMN_NO;?>
				</td>
				<td width="24">
					<input type="radio" name="stu_q_<?php echo $row->id;?>" value="1" <?php echo isset($_REQUEST['stu_q_'.$row->id]) ? ($_REQUEST['stu_q_'.$row->id]?'checked="checked"':'') : ($selected?'checked="checked"':'');?> <?php echo ($type_lesson==1 || $type_lesson==3)?'disabled="disabled"':($type_lesson==2 && $like_theory?'disabled="disabled"':($all_disabled?'disabled="disabled"':''));?> />
				</td>
				<td width="93">
					<?php echo _CMN_YES;?>
				</td>
			</tr>		
		<?php
		}
		?>
		</table>
		<?php
	}
	
	function FLMS_conditions_operation_in_course($rows, $type_lesson){
		?>
		<table width="100%" border="0">
		<?php
		for($i=0;$i<count($rows);$i++){
			$row = $rows[$i];
			$selected = isset($row->condition_value)?$row->condition_value:$row->default;
		?>
			<tr>
				<td width="35%">
					<b><?php echo ucfirst($row->name);?></b>
				</td>
				<td width="93">
					<?php echo ucfirst($row->value_1);?>
				</td>
				<td width="24">
					<input type="radio" name="condition_<?php echo $row->id;?>" value="0" <?php echo isset($_REQUEST['condition_'.$row->id]) ? ($_REQUEST['condition_'.$row->id]?'':'checked="checked"') : ($selected?'':'checked="checked"');?> <?php echo ($type_lesson==1 || $type_lesson==3)?'disabled="disabled"':($all_disabled?'disabled="disabled"':'');?> />
				</td>
				<td width="93">
					<?php echo ucfirst($row->value_2);?>
				</td>
				<td width="24">
					<input type="radio" name="condition_<?php echo $row->id;?>" value="1" <?php echo isset($_REQUEST['condition_'.$row->id]) ? ($_REQUEST['condition_'.$row->id]?'checked="checked"':'') : ($selected?'checked="checked"':'');?> <?php echo ($type_lesson==1 || $type_lesson==3)?'disabled="disabled"':($all_disabled?'disabled="disabled"':'');?> />
				</td>
			</tr>	
		<?php
		}
		?>
		</table>
		<br />
		<?php
	}
}
?>