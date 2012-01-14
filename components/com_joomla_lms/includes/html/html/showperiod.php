<?php

class JLMS_HTMLShowperiod
{
	function field( $isTimeRelated = false, $showPeriod  = 0, $formName = 'adminForm' )
	{
		$array = JLMS_HTMLShowperiod::_parse( $showPeriod );		
		JLMS_HTMLShowperiod::_dateLoadJSFramework( $formName );
		
		$disabled = "";		
		if( !$isTimeRelated ) {
			$disabled = "disabled=\"disabled\"";
		} 
			
		echo JHTML::_('select.booleanlist', 'is_time_related', 'onclick="changeStateSPField( this );"', $isTimeRelated, _JLMS_YES_ALT_TITLE, _JLMS_NO_ALT_TITLE );	
		echo "
			<br />
			<br />
			"._JLMS_DAYS."<input ".$disabled." size=\"3\" maxlength=\"3\" type=\"text\" id=\"days\" name=\"days\" value=\"".$array['days']."\" />
			"._JLMS_HOURS."<input ".$disabled." size=\"2\" maxlength=\"2\" type=\"text\" id=\"hours\" name=\"hours\" value=\"".$array['hours']."\" />
			"._JLMS_MINUTES."<input ".$disabled." size=\"2\" maxlength=\"2\" type=\"text\" id=\"mins\" name=\"mins\" value=\"".$array['mins']."\" />			
		";				
	}
	
	function _dateLoadJSFramework( $formName ) {
		global $JLMS_CONFIG;
		?>
		<script language="javascript" type="text/javascript">
		<!--//--><![CDATA[//><!--
		function changeStateSPField( el ) 
		{
			form = document.<?php echo $formName;  ?>;
						
			if( parseInt(el.value) )
				state = false;
			else 		
				state = true;		
				
			form.days.disabled = state;
			form.hours.disabled = state;
			form.mins.disabled = state;
			
			return true;							 
		}	
		//--><!]]>
		</script>
		<?php
	}		
	
	function getminsvalue( $day, $hour, $min )
	{	
		$res = $day*24*60+$hour*60+$min;					
		return $res;
	}
	
	function _parse( $showperiod ) 
	{
		$res = array();	
			
		$ost1 = $showperiod%(24*60);		
		$res['days'] = ($showperiod - $ost1)/(24*60);
		
		$ost2 = $showperiod%60;						
		$res['hours'] = ($ost1 - $ost2)/60;
		
		$res['mins'] = $ost2;
		
		return $res;		
	}
}
