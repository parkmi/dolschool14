<?php

class JLMS_HTMLCalendar
{
	function getKey( $format ) 
	{
		if( !$format )
			return '_';
		else
			return  str_replace( array('-%', '  %', ' %', ':%', '%'), '_', $format);	
	}
	function calendar($value, $name, $id, $format = null, $attribs = null, $type = 'selectlist' )
	{
		global $JLMS_CONFIG;
		JHTML::_('behavior.calendar'); //load the calendar behavior
									
		if( is_null($format) )
			$format = '%Y-%m-%d';
			
		$suffix = '';
		$jsSuffix = '';	
		$showsTime = false;
		
		if( strpos( $format, '%H' ) !== false ) 
		{
			$suffix = ' H:i';
			$jsSuffix = ' %H:%M';
			$showsTime = true;			
		}
																
		$value = JLMS_dateToDisplay( $value, $is_time = false, $offset = 0, $suffix );
				
		$jsFormat = strtr( JLMS_dateGetFormat(2), array( 'd'=>'%d', 'm'=>'%m', 'Y'=>'%Y' ) ).$jsSuffix;		
						
		switch( $type ) {			
			case 'statictext':
				JLMS_HTMLCalendar::_dateLoadJSFramework( $jsFormat, false, $showsTime );
				echo '<div style="white-space: nowrap;"><span style="vertical-align:middle" onclick="jlms_showCalendar'.JLMS_HTMLCalendar::getKey( $jsFormat ).'(\''.$id.'_date\', \''.$name.'\');"><img src="'.$JLMS_CONFIG->get('live_site').'/components/com_joomla_lms/lms_images/agenda/ag_date.png" width="16" height="16" alt="calendar" border="0" style="cursor:pointer " class="JLMS_png" /></span>
				<input class="inputbox" style="text-align:center; background:transparent; border:0px solid; font-size:12px; font-weight:bold; line-height:14px;" type="text" name="'.$name.'_date" id="'.$id.'_date" size="9" maxlength="10" value="'.$value.'" /></div>';				 
			break;			
			default:				
				JLMS_HTMLCalendar::_dateLoadJSFramework( $jsFormat, true, $showsTime );
				echo JLMS_HTMLCalendar::_dateGenerateSelectLists( $name ,$value, $format);
				echo '<span style="vertical-align:middle" onclick="jlms_showCalendar'.JLMS_HTMLCalendar::getKey( $jsFormat ).'(\''.$id.'_date\', \''.$name.'\');"><img src="'.$JLMS_CONFIG->get('live_site').'/components/com_joomla_lms/lms_images/agenda/ag_date.png" width="16" height="16" alt="calendar" border="0" style="cursor:pointer " class="JLMS_png" /></span>
				<input class="inputbox" style="visibility:hidden" type="text" name="'.$name.'_date" id="'.$id.'_date" size="1" maxlength="10" value="'.$value.'" />';					 
			break;
		}	
	}		
	
	function _dateLoadJSFramework( $format, $splitField = true, $showsTime = false  ) {
		global $JLMS_CONFIG;
		static $instances;
		$MondayFirst = ($JLMS_CONFIG->get('date_format_fdow', 1) == 1);
		 		
		$key = JLMS_HTMLCalendar::getKey( $format );
		
		if( empty($instances) ) {		
			?>	
			<script language="javascript" type="text/javascript">
			<!--//--><![CDATA[//><!--
			
			function jlms_closeHandler(cal) {
				cal.hide();
				Calendar.removeEvent(document, "mousedown", jlms_checkCalendar);
			}
			    
			var jlms_calendar = null;
			var sellist_name = '';			
			
			function jlms_checkCalendar(ev) {
				var el = Calendar.is_ie ? Calendar.getElement(ev) : Calendar.getTargetElement(ev);
				for (; el != null; el = el.parentNode)
				if (el == jlms_calendar.element || el.tagName == "A") break;
				if (el == null) {
					jlms_calendar.callCloseHandler(); Calendar.stopEvent(ev);
				}
			}		
			
			<?php if( $splitField ) { ?>
				function jlms_getDate(elem, format){			
					jlms_day   = getObj(elem+'dd').value;
					if (jlms_day.length == 1){
						jlms_day = '0'+jlms_day;
					}
					jlms_month = getObj(elem+'mm').value;
					jlms_month = Number(jlms_month) + 1;
					jlms_month = jlms_month + '';
					if (jlms_month.length == 1){
						jlms_month = '0'+jlms_month;
					}
					jlms_year  = getObj(elem+'yy').value;
					
					hoursObj = getObj(elem+'hh');
					jlms_hour = '';
					if( hoursObj != undefined ) {				
						jlms_hour = hoursObj.value;
					}
						
					minsObj = getObj(elem+'ii');
					jlms_min = '';
					if( minsObj != undefined ) {				
						jlms_min = minsObj.value;
					}
													
					getObj(elem+'_date').value = format.replace('Y',jlms_year).replace('m',jlms_month).replace('d', jlms_day).replace('H', jlms_hour).replace('i', jlms_min);									
				}
			<?php } ?>		
			//--><!]]>
		</script>
		<?php
		}
				
		
		if( !isset($instances[$key]) ) 
		{
			?>
			<script language="javascript" type="text/javascript">		
			function jlms_showCalendar<?php echo $key; ?>(id, sec_name) {
			sellist_name = sec_name;
									
			var el = document.getElementById(id);
						    	
				var cal = new Calendar(<?php echo $MondayFirst?"true":"false"; ?>, null, jlms_selected<?php echo $key; ?>, jlms_closeHandler);
				<?php
				if( $showsTime )	echo 'cal.showsTime = true;';
				?>							
				jlms_calendar = cal;					
				cal.setRange(1900, 2070);
				cal.setDateFormat('<?php echo $format; ?>');				
				jlms_calendar.create();				
				jlms_calendar.parseDate(el.value);
		
			jlms_calendar.sel = el;
			jlms_calendar.showAtElement(el);
			Calendar.addEvent(document, "mousedown", jlms_checkCalendar);
			return false;
			}
			
			function jlms_selected<?php echo $key; ?>(cal, date) {				
			cal.sel.value = date;
			if (cal.sel.defaultValue != undefined) {
				cal.sel.defaultValue = date;
			}
	<?php if( $splitField ) { ?>
			var el1 = document.getElementById(sellist_name+'dd');
			el1.value = cal.date.getDate();
			var el2 = document.getElementById(sellist_name+'mm');
			el2.value = cal.date.getMonth();
			var el3 = document.getElementById(sellist_name+'yy');
			el3.value = cal.date.getFullYear();
			<?php
				if( $showsTime )	
				{
				?>
			var el4 = document.getElementById(sellist_name+'hh');
			var hours = cal.date.getHours();
			if( hours < 10 )
				hours = '0'+hours;
			el4.value = hours; 
			var el5 = document.getElementById(sellist_name+'ii');
			var mins = cal.date.getMinutes();
			if( mins < 10 )
				mins = '0'+mins;
			el5.value = mins;
				<?php	
				}
			} ?>		
		}
			</script>
			<?php		
		}		
		
		$instances[$key] = true;
	}
	
	function _dateGenerateSelectLists($name, $date, $currFormat )
	{			
		if( $date == '-' )
			$timestamp = time();
		else			
			$timestamp = strtotime($date);
		
		$format = JLMS_dateGetFormat(2);	
		
		$addFormat = '';
		if( strpos( $currFormat, '%H' ) !== false ) $format .= ' H';
		if( strpos( $currFormat, '%M' ) !== false ) $format .= ':i';
				
		//Generate SelectList of days
		$str_days = "<select name='".$name."day' id='".$name."dd' onchange=\"jlms_getDate('".$name."','".$format."')\">";
		for ($i=1;$i<=31;$i++){
			$check1 = '';
			if ($date != ''){
				if ($i == intval(date('j',$timestamp))){
					$check1 = " selected='selected'";
				}
			}
			elseif($i == date("j")){
				$check1 = " selected='selected'";
			}
			$str_days .=  "<option value='$i'".$check1.">$i</option>";
		}
		$str_days .= '</select>';
				
		//Generate SelectList of months
		$str_months = "<select name='".$name."month' id='".$name."mm' onchange=\"jlms_getDate('".$name."','".$format."')\">";
		$first_day = mktime(1,1,1,1,1,06);
		for ($j=0;$j<12;$j++){
			$check2 = '';
			if ($date != ''){
				if (($j+1) == intval(date('n',$timestamp))){
					$check2 = " selected='selected'";
				}
			}
			elseif(($j+1) == date("n")){
				$check2 = " selected='selected'";
			}
			$str_months .= "<option value='".($j)."'$check2>".month_lang(strftime("%m",strtotime("+".$j." month",$first_day)),0,2)."</option>";
		}
		$str_months .= '</select>';
				
		//generate SelectList of Years
		$str_years = "<select name='".$name."year' id='".$name."yy' onchange=\"jlms_getDate('".$name."','".$format."')\">";
		for ($k=2006;$k<2021;$k++){
			$check3 = '';
			if ($date != ''){
				if ($k == intval(date('Y',$timestamp))){
					$check3 = " selected='selected'";
				}
			}
			elseif($k == date("Y")){
				$check3 = " selected='selected'";
			}
			$str_years .= "<option value='$k'$check3>".$k."</option>";
		}
		$str_years .= '</select>';	
				
		$str_hours = '';
		if( strpos($format,'H') !== false  ) {			
			//generate SelectList of Hours
			$str_hours = "<select name='".$name."hour' id='".$name."hh' onchange=\"jlms_getDate('".$name."','".$format."')\">";
			for ( $k=0; $k<24; $k++){
				$check4 = '';
				if ($date != ''){
					if ($k == intval(date('H',$timestamp))){												 												
						$check4 = " selected='selected'";
					}
				}
				elseif($k == date("H")){
					$check4 = " selected='selected'";
				}			
				
				if( $k < 10 ) {
					$str_hours .= "<option value='0$k'$check4>0".$k."</option>";
				} else {
					$str_hours .= "<option value='$k'$check4>".$k."</option>";
				}
			}
			$str_hours .= '</select>';				
		}
		
		$str_mins = '';
		if( strpos($format,'i') !== false  ) {
			//generate SelectList of Minutes
			$str_mins = "<select name='".$name."minute' id='".$name."ii' onchange=\"jlms_getDate('".$name."','".$format."')\">";
			for ( $k=0; $k<60; $k++){
				$check5 = '';
				if ($date != ''){
					if ($k == intval(date('i',$timestamp))){						
						$check5 = " selected='selected'";
					}
				}
				elseif($k == date("i")){
					$check5 = " selected='selected'";
				}
				if( $k < 10 ) {
					$str_mins .= "<option value='0$k'$check5>0".$k."</option>";
				} else {
					$str_mins .= "<option value='$k'$check5>".$k."</option>";
				}
			}
			$str_mins .= '</select>';			
		}
																	
		$trans = array( 'Y' => $str_years, 'm' => $str_months, 'd' => $str_days, 'H' => $str_hours, 'i' => $str_mins, "-" => " " );
						
		$ret = strtr($format, $trans);				 		
			
		return $ret;
	}
}
