<?php
/**
* joomla_lms.mailbox.html.php
* Joomla LMS Component
* * * ElearningForce Inc.
**/

// no direct access
defined( '_JLMS_EXEC' ) or die( 'Restricted access' );

class JLMS_subscription_html {
		function JLMS_list_paid_subscriptions( $option , &$subscriptions, $pageNav, &$procs, &$lists, &$avail_courses, &$user, $very_global_sub_id = 0 ) {
		global $Itemid, $JLMS_CONFIG, $JLMS_SESSION, $JLMS_DB, $my;
		
		$doc = & JFactory::getDocument();
		
		$jlms_tax_counting = $JLMS_CONFIG->get('enabletax');
		$sub_total = 0;
		$tax_amount = 0;
		$rows2 = array();

		$enable_custom_subscriptions = $JLMS_CONFIG->get('use_custom_subscr', false);
		$custom_sub_courses = array();

		// counting taxes
		if ($jlms_tax_counting) {
			$is_cb_installed = $JLMS_CONFIG->get('is_cb_installed', 0);
			$get_country_info = $JLMS_CONFIG->get('get_country_info', 0);
			$cb_country_filed_id = intval($JLMS_CONFIG->get('jlms_cb_country'));
			
			$isset_country = false;
			if($is_cb_installed && $get_country_info && $cb_country_filed_id){ //by Max (get country info)
				$query = "SELECT cf.name"
				. "\n FROM #__comprofiler_fields as cf"
				. "\n WHERE 1"
				. "\n AND cf.fieldid = '".$cb_country_filed_id."'"
				;
				$JLMS_DB->setQuery($query);
				$cb_country_field_name = $JLMS_DB->loadResult();
				
				$query = "SELECT ".$cb_country_field_name.""
				. "\n FROM #__comprofiler"
				. "\n WHERE 1"
				. "\n AND user_id = '".$my->id."'"
				;
				$JLMS_DB->setQuery($query);
				$country_name = $JLMS_DB->loadResult();
				
				require_once('components'. DS .$option. DS .'includes'. DS .'libraries'. DS .'lms.lib.countries.php');
				$CodeCountry = new CodeCountries();
				$code = $CodeCountry->code($country_name);
				if($code){
					$user_country = $code;	
				}
				$user_country_name = '';
				$us_state = '';
			} else {			
				$ip_address = $_SERVER['REMOTE_ADDR'];
				//$ip_address = '12.225.42.19';
				$isset_country = false;
				if(@ini_get('allow_url_fopen')){
					$fn = @file('http://api.hostip.info/get_html.php?ip='.$ip_address);
					// country ip identified
					if ($fn != false) {
						$ip_info = implode('',$fn);
						preg_match_all("(\(..\))", $ip_info, $dop);
						$user_country = str_replace('(','',str_replace(")",'',$dop[0][0]));
						
						preg_match_all("(\:.*\()", $ip_info, $dop2);
						$user_country_name = str_replace(': ','',str_replace(" (",'',$dop2[0][0]));
						preg_match_all("(\, ..)", $ip_info, $dop3);
						$us_state = @str_replace(', ','',$dop3[0][0]);
						//echo $us_state;
					}
				}
			}
			if(isset($user_country)){
				$query = "SELECT * FROM #__lms_subscriptions_countries WHERE published = 1 AND code='".$user_country."' ";
				$JLMS_DB->setQuery( $query );
				$rows2 = $JLMS_DB->loadObjectList();
				// if no country found
				if (!count($rows2)) {
					// check if in EU
					$query = "SELECT * FROM #__lms_subscriptions_countries WHERE published = 1 AND code='EU' AND list REGEXP '".$user_country."' ";
					$JLMS_DB->setQuery( $query );
					$rows_eu = $JLMS_DB->loadObjectList();
					
					if (count($rows_eu)) {
						$isset_country = true;
						$rows2[0]->tax_type = $rows_eu[0]->tax_type;
						$rows2[0]->tax = $rows_eu[0]->tax;
						$user_country_name = $rows_eu[0]->name.' ('.$user_country_name.')';
					}
				}
				// additional check for US
				if ($user_country == 'US') {
					$query = "SELECT * FROM #__lms_subscriptions_countries WHERE published = 1 AND code = 'US-".$us_state."' ";
					$JLMS_DB->setQuery( $query );
					$rows_states = $JLMS_DB->loadObjectList();
					if (count($rows_states)) {
						$isset_country = true;
						$rows2 = array();
						$rows2[0]->tax_type = $rows_states[0]->tax_type;
						$rows2[0]->tax = $rows_states[0]->tax;
						$user_country_name = 'United states ('.$rows_states[0]->name.' )';
					}
				}
			}
			//10.01.09 (Max) default tax option
			if(!$isset_country){
				$rows2[0]->tax_type = $JLMS_CONFIG->get('default_tax_type', 1);
				$rows2[0]->tax = $JLMS_CONFIG->get('default_tax', 0);	
			}
		}
		?>
		<script language="javascript" type="text/javascript">
		<!--
		var global_sub_proc_name = '';
		function jq_Check_selectRadio(rad_name, form_name) {
			var tItem = eval('document.'+form_name);
			if (tItem) {
				var selItem = eval('document.'+form_name+'.'+rad_name);
				if (selItem) {
					if (selItem.length) { var i;
						for (i = 0; i<selItem.length; i++) {
							if (selItem[i].checked) {
								if (selItem[i].value > 0 || 0 > selItem[i].value) { return selItem[i].value; } } }
					} else if (selItem.checked) { return selItem.value; } }
				return false; }
			return false;
		}
		function createCookie(name,value,days) {
			if (days) {
				var date = new Date();
				date.setTime(date.getTime()+(days*24*60*60*1000));
				var expires = "; expires="+date.toGMTString();
			}
			else var expires = "";
			document.cookie = name+"="+value+expires+"; path=/";
		}
		function readCookie(name) {
			var nameEQ = name + "=";
			var ca = document.cookie.split(';');
			for(var i=0;i < ca.length;i++) {
				var c = ca[i];
				while (c.charAt(0)==' ') c = c.substring(1,c.length);
				if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
			}
			return null;
		}
		
		function eraseCookie(name) {
			createCookie(name,"",-1);
		}	
				
		function calculator(element, sub_total,discount, tax, total){
			if (element) {
				if (element.value) {
					createCookie('sub_id', element.value);
				}
			}
			if (tax && sub_total){
				document.getElementById('tax').innerHTML = tax;
				document.getElementById('sub_total').innerHTML = sub_total;
			}
			document.getElementById('total_amount').innerHTML = total;
			if (discount){
				document.getElementById('discount').innerHTML = discount;
			}else{
				document.getElementById('discount').innerHTML = '';
			}
				
		}
		var ProcAr = Array(<?php 
			$i = 0;
			foreach ($procs as $proc){
				if ($i != 0 && $i != count($procs)){
					echo ","; 
				}
				echo $proc->id;
				$i++;
			}
			?>);
		
		var form = document.JLMS_adminForm;
		
		function checkProcessor(currentProc, ProcName){
			global_sub_proc_name = ProcName;
			createCookie('proc_id', currentProc);
			createCookie('checked_proc', ProcName);
			for(i=0; i< ProcAr.length; i++){
				if (document.getElementById('billing_form'+ProcAr[i])) {
					document.getElementById('billing_form'+ProcAr[i]).style.display = 'none';
				}
			}
			if (document.getElementById('billing_form'+currentProc)) {
				document.getElementById('billing_form'+currentProc).style.display = 'block';
			}
		}
		function jlms_submitbutton(pressbutton) {
			var form = document.JLMS_adminForm;
			if( pressbutton == 'subscribe' ){
				var ttt = jq_Check_selectRadio('jlms_sub', 'JLMS_adminForm');
				var ppp = jq_Check_selectRadio('proc_id', 'JLMS_adminForm');
				
				<?php if ($JLMS_CONFIG->get('enableterms')){?>
					if (ttt) {
						if (ppp){
							if (!form){
								checkProcessor(ppp, '');
							}
							if( (pressbutton == 'subscribe') && (document.getElementById('agreement').checked == false) ){
								alert( "<?php echo addslashes(_JLMS_AGREEMENT);?>" );
							}else{
								processor = global_sub_proc_name;//readCookie('checked_proc');
								if (eval('document.JLMS_'+processor+'')){
									eval('JLMS_'+processor+'_submit();');
								}else{
									form.task.value = pressbutton;
									form.id.value = ttt;
									form.submit();
								}
							}
						}else{
							alert( "<?php echo _JLMS_ALERT_SELECT_ITEM;?>" );
						}
					}else{
						alert( "<?php echo _JLMS_ALERT_SELECT_ITEM;?>" );
					}
				<?php }else{?>
					if (!ttt || !ppp ) {
						alert( "<?php echo _JLMS_ALERT_SELECT_ITEM;?>" );
					}	
					else{						
						processor = global_sub_proc_name;//readCookie('checked_proc');
						if (eval('document.JLMS_'+processor+'')){
							eval('JLMS_'+processor+'_submit();');
						}else{							
							form.task.value = pressbutton;
							form.id.value = ttt;
							form.submit();
						}
					}
				<?php }?>
			}
		}
		function getCategoryValue() {
			$select = document.JLMS_adminForm.category_filter;
			for( $i=0; $i<$select.length; $i++ ) {
				if ($select.options[$i].selected == true) {
					return $select.options[$i].value;
				}
			}
		}
		//-->
		</script>	
		
		<form action="<?php echo sefRelToAbs("index.php?option=$option&Itemid=$Itemid");?>" method="post" name="JLMS_adminForm">
		
		<?php
		JLMS_TMPL::OpenMT();
	
		$params = array(
			'show_menu' => true,
			'simple_menu' => true,
		);
		JLMS_TMPL::ShowHeader('subscription', _JLMS_HEAD_SUBSCRIPTION_STR, $params);
		$show_filter = true;
		if (count($subscriptions) > 0 || (isset($lists['course_filter_big']) && $lists['course_filter_big'] && isset($lists['course_filter_cur']) && $lists['course_filter_cur']) ){
			JLMS_TMPL::OpenTS('', ' align="right" style="text-align:right " width="100%"');
				$link 	= "index.php?option=".$option."&Itemid=".$Itemid."&task=subscription";
				echo _JLMS_PN_DISPLAY_NUM . $pageNav->getLimitBox( $link );
				echo _JLMS_SUBSCR_CATEGORY_FILTER." ".$lists['category_filter'];
				echo _JLMS_FILTER." ".$lists['course_filter'];
				//echo "Sub name: "."<input type='text' class='inputbox' name='sub_name' />"."&nbsp;&nbsp;";
			JLMS_TMPL::CloseTS();
		}
		JLMS_TMPL::CloseMT();
		 ?>
		<br />
		<table cellpadding="0" cellspacing="0" border="0" class="contentpane" style="width:100%">
			<?php if (count($subscriptions) > 0){?>
			<tr>
				<td colspan="2" >
					<table width="100%" cellpadding="0" cellspacing="0" border="0">
					<tr>
						<<?php echo $JLMS_CONFIG->get('tableheader_tag', 'td');?> class="sectiontableheader" align="center" width="16">#</<?php echo $JLMS_CONFIG->get('tableheader_tag', 'td');?>>
						<<?php echo $JLMS_CONFIG->get('tableheader_tag', 'td');?> class="sectiontableheader" align="left" width="20">&nbsp;</<?php echo $JLMS_CONFIG->get('tableheader_tag', 'td');?>>
						<<?php echo $JLMS_CONFIG->get('tableheader_tag', 'td');?> class="sectiontableheader" align="left" width="180"><?php echo _JLMS_SUBSCRIBE_SUB_COURSES;?></<?php echo $JLMS_CONFIG->get('tableheader_tag', 'td');?>>
						<<?php echo $JLMS_CONFIG->get('tableheader_tag', 'td');?> class="sectiontableheader" align="left" width="160"><?php echo _JLMS_SUBSCR_SUB_TYPE;?></<?php echo $JLMS_CONFIG->get('tableheader_tag', 'td');?>>
						<<?php echo $JLMS_CONFIG->get('tableheader_tag', 'td');?> class="sectiontableheader" align="left"><?php echo _JLMS_SUBSCR_PERIOD;?></<?php echo $JLMS_CONFIG->get('tableheader_tag', 'td');?>>
						<<?php echo $JLMS_CONFIG->get('tableheader_tag', 'td');?> class="sectiontableheader" align="center" width="170"><?php echo _JLMS_COURSES_PRICE;?></<?php echo $JLMS_CONFIG->get('tableheader_tag', 'td');?>>
					</tr>
				<?php
				$jlms_cs = $JLMS_CONFIG->get('jlms_cur_sign');
				$sub_id = $JLMS_SESSION->get('sub_id');
				if ($very_global_sub_id) {
					$sub_id = $very_global_sub_id;
				} elseif (isset($_COOKIE['sub_id'])){
					$sub_id = $_COOKIE['sub_id'];
				}

				$temp = '';
				for ($i=0, $n=count($subscriptions); $i < $n; $i++) {
					$subscription = $subscriptions[$i];
					
					// calculating taxes, country exists in the tax list
					$sub_total = round($subscription->price,2);
					$tax_amount = 0;
					$disc = 0;
					$total = 0;
					if (count($rows2) > 0) {
						$sub_total1 = $sub_total;
						if ($subscription->account_type=='5'){
							$disc = $subscription->price*($subscription->discount/100);
							$sub_total1 = $sub_total-$disc;
						}
						if ($rows2[0]->tax_type == 1) $tax_amount = round( $sub_total1 / 100 * $rows2[0]->tax, 2);
						if ($rows2[0]->tax_type == 2) $tax_amount = $rows2[0]->tax;	
						if ($subscription->account_type=='5'){
							$disc_view = $jlms_cs.$disc." (".round($subscription->discount,0)."%)";
							$total = ($sub_total-$disc)+$tax_amount;
							$price = "(".$sub_total."-".$disc.")+".$tax_amount." = ".$jlms_cs.(($sub_total-$disc)+$tax_amount);
							$calc = "'".$jlms_cs.$sub_total."', '".$disc_view."','".$jlms_cs.$tax_amount."','".$jlms_cs.$total."'";
						}else{
							$total = $sub_total+$tax_amount;
							$price = $sub_total."+".$tax_amount." = ".$jlms_cs.$total;							
							$calc = "' ".$jlms_cs.$sub_total."', 0,'".$jlms_cs.$tax_amount."','".$jlms_cs.$total."'";
							
						}
					}else{
						if ($subscription->account_type=='5'){
							$disc = $subscription->price*($subscription->discount/100);
							$disc_view = $jlms_cs.$disc." (".round($subscription->discount,0)."%)";
							$total = $sub_total-$disc;
							$price = "(".$sub_total."-".$disc.") =". $jlms_cs.$total;
							$calc = "0,'".$disc_view."',0,'".$jlms_cs.$total."'";
						}else{
							$price = $jlms_cs.$sub_total;
							$total = $sub_total;
							$calc = " 0,0,0,'".$jlms_cs.$total."'";
						}
					}
					$proc_checked = '';
					if ($sub_id && ($subscription->id == $sub_id )){
						$proc_checked = 'checked="checked"';
						$temp = "calculator('', ".$calc.");";
//						setcookie('sub_id', $subscription->id);
					}
					if (count($subscriptions) == 1){
						$proc_checked = 'checked="checked"';
						if (!$my->id) {
							//$_COOKIE['sub_id'] = $subscription->id;
							setcookie('sub_id', $subscription->id);
						}
					}
					if ($sub_id){	$JLMS_SESSION->set('sub_id' , $sub_id); }

					if ($enable_custom_subscriptions) {
						if ($subscription->id == -1) {
							foreach ($subscription->courses as $csubc) {
								$custom_sub_courses[] = $csubc;
							}
						}
					}
					?>
					<tr valign="middle" style="vertical-align:middle" class="<?php echo $JLMS_CONFIG->get('visual_set_main_row_class'); ?>">
						<td width="15" align="center"><?php echo ( $pageNav->limitstart + $i + 1 );?></td>
						<td width="20" align="left"><input type="radio" name="jlms_sub" value="<?php echo $subscription->id;?>" <?php echo $proc_checked;?> onclick="calculator(this, <?php echo $calc;?>)" /></td>
						<td style="font-weight:bold; "><?php echo $subscription->sub_name;?></td>
						<td><?php 
							if ($subscription->account_type=='1') 	  {echo _JLMS_SUBSCR_BASIC;}
							elseif ($subscription->account_type=='2') {echo _JLMS_SUBSCR_PERIOD;}
							elseif ($subscription->account_type=='3') {echo _JLMS_SUBSCR_DATE_LIFETIME;}
							elseif ($subscription->account_type=='4') {echo $subscription->access_days.' '._JLMS_SUBSCR_DAYS_ACCESS;}
							elseif ($subscription->account_type=='5') {echo _JLMS_SUBSCR_WITH_DICOUNT;}
						?></td>
						<td><?php
						if ($subscription->account_type == 2){
							echo _JLMS_SUBSCR_FROM.' '.JLMS_dateToDisplay($subscription->start_date).' '._JLMS_SUBSCR_TO.' '.$subscription->end_date;
						}elseif($subscription->account_type=='3'){
							echo _JLMS_SUBSCR_FROM.' '.JLMS_dateToDisplay($subscription->start_date).' '._JLMS_SUBSCR_TO.' Lifetime';
						}elseif($subscription->account_type=='4'){
							echo _JLMS_SUBSCR_FROM.' '.JLMS_dateToDisplay(date('Y-m-d')).' '._JLMS_SUBSCR_TO.' '.JLMS_dateToDisplay(date('Y-m-d',strtotime('+'.$subscription->access_days.' day')));
						}else{
							echo "-";
						}?>
						</td>
						<td align="center">
						<?php echo $price;?>
						 </td>
					</tr>
					<?php 
					$s = 1;
					foreach ($subscription->course_names as $course_name){
						if ($s != count($subscription->courses) ){
							$src = 'components/com_joomla_lms/lms_images/treeview/sub1.png';
						}else{
							$src = 'components/com_joomla_lms/lms_images/treeview/sub2.png';
						}
						$img1 = "<img src='components/com_joomla_lms/lms_images/treeview/empty_line.png' width='16' height='16' alt=''/>";
						$course_usertype = 0;
						if ( in_array($subscription->courses[$s-1], $JLMS_CONFIG->get('teacher_in_courses',array(0))) ) {
							$course_usertype = 1;
						} elseif ( in_array($subscription->courses[$s-1], $JLMS_CONFIG->get('student_in_courses',array(0))) ) {
							$course_usertype = 2;
						}
						if ($course_usertype) {
							$img1 = "<img src='components/com_joomla_lms/lms_images/toolbar/btn_accept.png' width='16' height='16' alt=''/>";
						}?>
						<tr class="<?php echo $JLMS_CONFIG->get('visual_set_child_row_class'); ?>">
							<td><?php echo $img1;?></td>
							<td align="center">
							<?php
							echo "<img src='$src' width='16' height='16' alt=''/>";	
							?>
							</td>
							<td style="text-indent:16px;" align="left" colspan="4" valign="middle">							
							<?php echo $course_name;?>
							</td>
						</tr>
						<?php 
						$s++;
					}
				}?>
				</table>
				</td>		
			</tr>
			<tr>
				<td align="center" height="20" colspan="2" ><?php echo $pageNav->writePagesLinks( $link );?></td>
			</tr>
			<?php if ($my->id){?>
			<tr>
				<td align="center" height="20" colspan="2" >
				<div class="joomlalms_info_legend">
					<div style="text-align:left ">					
						<img class="JLMS_png" src="<?php echo $JLMS_CONFIG->get('lms_path_to_images', 'components/com_joomla_lms/lms_images');?>/buttons/btn_complete.png" align="top" width="16" height="16" border="0" alt="<?php echo _JLMS_COURSES_ALREADY; ?>" title="<?php echo _JLMS_COURSES_ALREADY;?>" />
						&nbsp;- <?php echo _JLMS_COURSES_ALREADY;?>.
					</div>				
				</div>
				</td>
			</tr>
			<?php }?>
			<tr>
				<<?php echo $JLMS_CONFIG->get('tableheader_tag', 'td');?> colspan="2" class="sectiontableheader"><?php echo _JLMS_SUBSCR_SELECT_PAY_METOD;?>
				</<?php echo $JLMS_CONFIG->get('tableheader_tag', 'td');?>>
			</tr>
			<tr>
				<td>
					<?php 
						$disabled = '';	
						if(!$my->id) $disabled = "disabled='disabled'";
						
						$sub_proc = $JLMS_SESSION->get('sub_proc');
						if (isset($_COOKIE['proc_id'])){
							$sub_proc = $_COOKIE['proc_id'];
						}
						$temp2 = "";
						foreach ($procs as $proc) {
							$checked = '';
							if (count($procs) == 1)	{
								$temp2 = "checkProcessor('".$proc->id."','".$proc->filename."');";
								$checked = 'checked="checked"';
							}
							if ($sub_proc){
								if ($sub_proc == $proc->id){
									$temp2 = "checkProcessor('".$proc->id."','".$proc->filename."');";
									$checked = 'checked="checked"';
								}
							}else{
								if ($proc->default_p){
									$temp2 = "checkProcessor('".$proc->id."','".$proc->filename."');";
									$checked = 'checked="checked"';
								}
							}?>
							<input type="radio" name="proc_id" value="<?php echo $proc->id;?>" onclick="checkProcessor('<?php echo $proc->id;?>','<?php echo $proc->filename;?>' );" id="proc_<?php echo $proc->id;?>" <?php echo $checked." ".$disabled;?> />&nbsp;<label class="msspro_sel_proc2" for="proc_<?php echo $proc->id;?>"><?php echo $proc->name;?></label><br />
							<?php 
						}
					?>
				</td>
				<td width="50%" align="right">
					<table cellpadding="0" cellspacing="0" border="0" width="200">
						<?php if (count($rows2) > 0) {?>
						<tr>
							<td align="right"><?php echo _JLMS_SUBSCR_SUB_TOTAL;?>&nbsp;</td>
							<td align="left">
								<span id="sub_total">
								<?php echo count($subscriptions) == 1 ? $sub_total : ""?>
								</span>
							</td>
						</tr>
						<?php }?>
						<tr>
							<td align="right"><?php echo _JLMS_SUBSCR_DISCOUNT;?>&nbsp;</td>
							<td align="left">
								<span id="discount">
								<?php echo count($subscriptions) == 1 ? ($disc?$disc:'') : ""?>
								</span>
							</td>
						</tr>
						<?php if (count($rows2) > 0) {?>
						<tr>
							<td align="right"><?php echo _JLMS_SUBSCR_TAX_AMOUNT;?>&nbsp;</td>
							<td align="left">
								<span id="tax"><?php echo count($subscriptions) == 1 ? $tax_amount : ""?></span>
							</td>
						</tr>
						<?php }?>
						<tr>
							<td align="right" width="105"><br /><b><?php echo _JLMS_SUBSCR_TOTAL;?>&nbsp;</b></td>
							<td align="left" width="95"><br />
								<span id="total_amount" style="font-weight:bold"><?php echo count($subscriptions) == 1 ? $jlms_cs.$total : "&nbsp;"?></span>
							</td>
						</tr>
					</table>
				</td>
			</tr>			
		</table>
		<?php if ($my->id){?>
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
		<input type="hidden" name="tax_amount" value="<?php echo $tax_amount;?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="id" value="" />
		<input type="hidden" name="custom_courses" value="<?php echo ( ($enable_custom_subscriptions && count($custom_sub_courses)) ? implode(',', $custom_sub_courses) : '');?>" />		
		<?php }?>		
		</form>
		<table width="100%" style="width:100%" cellpadding="0" cellspacing="0" border="0" class="contentpane">	
			<tr>
				<td colspan="2">
				<?php JLMS_UserSessions_html::loginPanel( $course_id, 'subs'); ?>
				</td>
			</tr>	
			<tr>
				<td colspan="2">					
					<?php 
					if ($my->id){
						$custom_code = '';
						if ($enable_custom_subscriptions && count($custom_sub_courses)) {
							$custom_code = '<input type="hidden" name="custom_courses" value="'. implode(',', $custom_sub_courses). '" />';
						}
						foreach ($procs as $proc){
							$display = 'none';
							if (count($procs) == 1)	{							
								$display = 'block';
							}
							if ($proc->default_p){							
								$display = 'block';
							}
							echo '<div id="billing_form'.$proc->id.'" style="display:'.$display.' ">';
								require_once(_JOOMLMS_FRONT_HOME.'/includes/processors/'.$proc->filename.'.php');						
								$newClass = "JLMS_".$proc->filename;
								if (class_exists($newClass)){
									$newProcObj = new $newClass();
									$newProcObj->show_billing_form($option, $proc, $user, $custom_code);
								}else{
									
								}
							echo "</div>";
						}
						if($temp2){
							echo '<script type="text/javascript" language="javascript"><!--'."\r\n";
							echo $temp2;
							echo "\r\n--></script>";
						}
					}?>
				</td>
			</tr>
			<?php if ($my->id){?>
			<tr>
				<td colspan="2" align="left">
				<br /><?php if ($JLMS_CONFIG->get('enableterms')){
					echo "<table align='center'><tr><td width='20'>
						<input type='checkbox' name='agreement' id='agreement' />
					</td><td style='text-align:left'>
						<label for='agreement'>" . $JLMS_CONFIG->get('jlms_terms')."</label>
					</td></tr></table>";
				//echo "<input type='checkbox' name='agreement' id='agreement' />";
				//echo $JLMS_CONFIG->get('jlms_terms');
				};?><br />
				<?php $toolbar = array();
				$toolbar[] = array('btn_type' => 'yes', 'btn_str' => _JLMS_SUBSCRIBE, 'btn_js' => "javascript:jlms_submitbutton('subscribe','');");
				echo JLMS_ShowToolbar($toolbar, true, 'center'); ?>
				</td>
			</tr>
			<?php 
			}
			}else{
				echo "<tr><td><div class='joomlalms_sys_message'>"._JLMS_SUBSCR_NO_SUBS."</div></td></tr>";
			}
			?>
		</table>
		<?php 
		if ($JLMS_CONFIG->get('jlms_title')) {
			$doc->setTitle( $JLMS_CONFIG->get('jlms_title') );
		}
		if(isset($temp) && $temp){
			echo '<script type="text/javascript" language="javascript"><!--'."\r\n";
			echo $temp;
			echo "\r\n--></script>";
		}
		?>
		
	<?php
	}
	function JLMS_free_subscriptions( $option , $course_info, $course_id, $multi_sub = false  ) {
		/*if ($multi_sub) {
			$course_info = $course_info[0];
		}*/
				
		global $Itemid, $JLMS_CONFIG, $my; ?>
		<script language="javascript" type="text/javascript">
		<!--
		function jlms_submitbutton(pressbutton) {
			var form = document.JLMS_adminForm;
			<?php if ($JLMS_CONFIG->get('enableterms')){?>
			if( (pressbutton == 'course_subscribe') && (document.getElementById('agreement').checked == false) ){
				alert( "<?php echo addslashes(_JLMS_AGREEMENT)?>" );
			}
			else{
				form.task.value = pressbutton;
				form.submit();
			}
			<?php }else{?>
				form.task.value = pressbutton;
				form.submit();
			<?php }?>
		}
		//-->
		</script>
<?php
		JLMS_TMPL::OpenMT();
	
		$params = array(
			'show_menu' => true,
			'simple_menu' => true,
		);
		JLMS_TMPL::ShowHeader('subscription', _JLMS_HEAD_SUBSCRIPTION_STR, $params);

		JLMS_TMPL::OpenTS('', ' align="right" style="text-align:right " width="100%"');

		//echo "Sub name: "."<input type='text' class='inputbox' name='sub_name' />"."&nbsp;&nbsp;";
		JLMS_TMPL::CloseTS();
		JLMS_TMPL::CloseMT(); ?>
		<form action="<?php echo sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid");?>" method="post" name="JLMS_adminForm">
		<table width="100%" cellpadding="0" cellspacing="0" border="0" class="jlms_table_no_borders">
			<tr>
				<td colspan="2">
				<table width="100%" cellpadding="0" cellspacing="0" border="0" class="<?php echo JLMSCSS::_('jlmslist');?>">
					<tr>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="left" width="200"><?php echo _JLMS_SUBSCRIBE_COURSE_NAME;?></<?php echo JLMSCSS::tableheadertag();?>>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="left" width="70" ><?php echo _JLMS_COURSES_FEE_TYPE;?></<?php echo JLMSCSS::tableheadertag();?>>
					</tr>
					<?php if ($multi_sub) {
						$k = 1;
						foreach ($course_info as $course_info1) { ?>
							<tr class="<?php echo JLMSCSS::_('sectiontableentry'.$k);?>">
								<td align="left" valign="middle"><?php echo $course_info1->course_name;?></td>
								<td align="left" valign="middle"><?php echo _JLMS_COURSES_FREE;?></td>
							</tr>
						<?php $k = 3 - $k;
						}
					} else {?>
					<tr class="<?php echo JLMSCSS::_('sectiontableentry1');?>">
						<td align="left" valign="middle"><?php echo $course_info->course_name;?></td>
						<td align="left" valign="middle"><?php echo _JLMS_COURSES_FREE;?></td>
					</tr>
					<?php } ?>
				</table>
				</td>
			</tr>
			<?php if ($JLMS_CONFIG->get('enableterms')){
				if ($my->id) {
					echo "<tr><td colspan='2'><table align='center' class='jlms_table_no_borders'><tr><td width='20'>
							<input type='checkbox' name='agreement' id='agreement' />
						</td><td style='text-align:left'>
							<label for='agreement'>" . $JLMS_CONFIG->get('jlms_terms')."</label>
						</td></tr></table></td></tr>";
				} else {
				/*	echo "<tr><td colspan='2'><br /><table align='center'><tr><td width='20'>
							<input type='checkbox' name='agreement' id='agreement' disabled='disabled' />
						</td><td style='text-align:left'>
							<label for='agreement'>" . $JLMS_CONFIG->get('jlms_terms')."</label>
						</td></tr></table></td></tr>";*/
				}
				/*

				?>
			<tr>
				<td class="sectiontableheader">Terms and conditions</td>
			</tr>
			<tr>
				<td>
				<?php 
				echo "<input type='checkbox' name='agreement' id='agreement' />";
				echo $JLMS_CONFIG->get('jlms_terms');
				?>			
				</td>
			</tr>
			<?php */}?>	
			<tr>
				<td colspan="2" align="center">
				<?php $toolbar = array();
				if ($my->id) {
					$toolbar[] = array('btn_type' => 'yes', 'btn_str' => _JLMS_SUBSCRIBE, 'btn_js' => "javascript:jlms_submitbutton('course_subscribe','');");
					echo JLMS_ShowToolbar($toolbar, true, 'center');
				} else {
					//$toolbar[] = array('btn_type' => 'yes', 'btn_str' => _JLMS_SUBSCRIBE, 'btn_js' => "javascript:void(0);");
					//echo JLMS_ShowToolbar($toolbar, true, 'center');
				}
?>
				<noscript>
					<div class="joomlalms_sys_message">
					<?php echo _JLMS_JS_COOKIES_REQUIRES;?>
					</div>
				</noscript>
				</td>
			</tr>		
		</table>	
 		<input type="hidden" name="option" value="<?php echo $option?>" />
		<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="id" value="<?php echo $course_id;?>" />
		<input type="hidden" name="state" value="0" />
		</form>		
		<?php 
		if( !$my->id ) {
			JLMS_UserSessions_html::loginPanel( $course_id, 'subs' );
		} 
		?>
<?php
	}	
}
?>