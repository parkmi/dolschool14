<?php
/**
* joomla_lms.cart.html.php
* (c) JoomaLMS eLearning Software http://www.joomlalms.com/
**/

// no direct access
defined( '_JLMS_EXEC' ) or die( 'Restricted access' );

class JLMS_CART_html {
	function ListSubs( $option , &$subscriptions, $pageNav, &$lists, $levels) {
		global $Itemid, $JLMS_CONFIG, $JLMS_SESSION, $JLMS_DB, $my;
		
		$doc = & JFactory::getDocument();		

		$sub_total = 0;
		$tax_amount = 0;

		//FLMS multicat
		$multicat = array();
		if($JLMS_CONFIG->get('multicat_use', 0)){
			$multicat = array();
			$i=0;
			foreach($lists as $key=>$item){
				if(substr($key, 0, 6) == 'level_'){
					$multicat[] = $lists['level_'.$i];
					$i++;
				}
			}
		}
		?>
		<script language="javascript" type="text/javascript">
		<!--
		<?php
		if ($JLMS_CONFIG->get('multicat_use', 0)) {
		?>
		function getCategoryValue() {
			$select = document.JLMS_adminForm.level_id_0;
			for( $i=0; $i<$select.length; $i++ ) {
				if ($select.options[$i].selected == true) {
					return $select.options[$i].value;
				}
			}
		}
		<?php
		} else {
		?>
		function getCategoryValue() {
			$select = document.JLMS_adminForm.category_filter;
			for( $i=0; $i<$select.length; $i++ ) {
				if ($select.options[$i].selected == true) {
					return $select.options[$i].value;
				}
			}
		}
		<?php
		}
		?>
		function jq_Check_selectCheckbox(check_name, form_name) {
			selItem = eval("document."+form_name+"['"+check_name+"']");
			if (selItem) {
				if (selItem.length) { var i;
					for (i = 0; i<selItem.length; i++) {
						if (selItem[i].checked) {
							if (selItem[i].value) { return true; }
						}}
				} else if (selItem.checked) { return true; }}
			return false;
		}
		function jlms_submitbutton(pressbutton) {
			var form = document.JLMS_adminForm;
			if (pressbutton == 'add_to_cart') {
				var ttt = jq_Check_selectCheckbox('jlms_sub[]', 'JLMS_adminForm');
				if (ttt) {
					form.task.value = pressbutton;
					form.submit();
				} else {
					alert("<?php echo _JLMS_ALERT_SELECT_ITEM;?>");
				}
			} else if (pressbutton == 'show_cart') {
				form.task.value = pressbutton;
				form.submit();
			}
		}
		<?php
		if ($JLMS_CONFIG->get('multicat_use', 0)) {
		?>
		var old_filters = new Array();
		function read_filter(){
			var form = document.JLMS_adminForm;
			var count_levels = '<?php echo count($levels);?>';
			for(var i=0;i<parseInt(count_levels);i++){
				if(form['level_id_'+i] != null){
					old_filters[i] = form['level_id_'+i].value;
				}
			}
		}
		function write_filter(){
			var form = document.JLMS_adminForm;
			var count_levels = '<?php echo count($levels);?>';
			var j;
			for(var i=0;i<parseInt(count_levels);i++){
				if(form['level_id_'+i] != null && form['level_id_'+i].value != old_filters[i]){
					j = i;
				}
				if(i > j){
					if(form['level_id_'+i] != null){
						form['level_id_'+i].value = 0;	
					}
				}
			}
		}
		<?php
		}
		?>
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
		JLMS_TMPL::ShowPageTip('subscription');
		$show_filter = true;

		JLMS_TMPL::OpenTS('', ' align="right" style="text-align:right " width="100%"');
			$link 	= "index.php?option=".$option."&Itemid=".$Itemid."&task=subscription";
			
			if ($JLMS_CONFIG->get('multicat_use', 0)){
				echo ((isset($levels[0]->cat_name) && $levels[0]->cat_name != '')?$levels[0]->cat_name:_JLMS_COURSES_COURSES_GROUPS)." ".$lists['level_0']."&nbsp;&nbsp;";
				if(!count($multicat)){
					echo '<br />'.$lists['course_filter']."&nbsp;&nbsp;";
				}
			} else {
				echo _JLMS_FILTER." ".$lists['course_filter'];
				echo _JLMS_SUBSCR_CATEGORY_FILTER." ".$lists['category_filter'];
			}

		JLMS_TMPL::CloseTS();
		
		if(count($multicat)){ // it is possible only if $JLMS_CONFIG->get('multicat_use', 0)
			for($i=0;$i<count($multicat);$i++){
				if($i > 0){
					JLMS_TMPL::OpenTS('', ' align="right" style="text-align:right " width="100%"');
					echo ((isset($levels[$i]->cat_name) && $levels[$i]->cat_name != '')?$levels[$i]->cat_name:_JLMS_COURSES_COURSES_GROUPS)." ".$lists['level_'.$i]."&nbsp;&nbsp;";
					JLMS_TMPL::CloseTS();
				}
			}
			JLMS_TMPL::OpenTS('', ' align="right" style="text-align:right " width="100%"');
			echo $lists['course_filter']."&nbsp;&nbsp;";
			JLMS_TMPL::CloseTS();
		}

		JLMS_TMPL::CloseMT();
		 ?>
		<br />
		<table cellpadding="0" cellspacing="0" border="0" width="100%" class="jlms_subscriptions_list_outer">
			<?php 
			if (count($subscriptions) > 0){
			?>
			<tr>
				<td>
					<table width="100%" cellpadding="0" cellspacing="0" border="0" class="<?php echo JLMSCSS::_('jlmslist', 'jlms_subscriptions_list');?>">
					<tr>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center" width="16">#</<?php echo JLMSCSS::tableheadertag();?>>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="left" width="20">&nbsp;</<?php echo JLMSCSS::tableheadertag();?>>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="left"><?php echo _JLMS_SUBSCRIBE_SUB_COURSES;?></<?php echo JLMSCSS::tableheadertag();?>>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="left"><?php echo _JLMS_DETAILS;?></<?php echo JLMSCSS::tableheadertag();?>>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center" width="170"><?php echo _JLMS_COURSES_PRICE;?></<?php echo JLMSCSS::tableheadertag();?>>
					</tr>
				<?php
				$jlms_cs = $JLMS_CONFIG->get('jlms_cur_sign');
	
				$temp = '';
								
				for ($i=0, $n=count($subscriptions); $i < $n; $i++) {
					$subscription = $subscriptions[$i];
														
					// calculating taxes, country exists in the tax list
					$sub_total = round($subscription->price,2);
					//$tax_amount = 0;
					$disc = 0;
					$total = 0;				
					
					if ($subscription->account_type == '5'){
						$disc = $subscription->price*($subscription->discount/100);
						$total = $subscription->price-$disc;
						$price = "(".$sub_total."-".$disc.") =". $jlms_cs.sprintf('%.2f',round($total,2));						
					} else if ($subscription->account_type == '6') {																
						$price = JLMS_RECURRENT_PAY::getPriceDesc( $subscription  );											
					} else {
						$price = $jlms_cs.sprintf('%.2f',round($sub_total,2));
					}
					//}
					$proc_checked = '';
					if (count($subscriptions) == 1){
						$proc_checked = 'checked="checked"';
					}

					JLMS_CART_html::ShowSub($subscription, ($pageNav->limitstart + $i + 1), $proc_checked, $price, true);
				}?>

			<?php 
			if ($pageNav->isMultiPages()){
			?>
					<tr>
						<td colspan="5" align="center" height="20" class="<?php echo JLMSCSS::_('jlmslist-footer_td');?>">
							<?php echo _JLMS_PN_DISPLAY_NUM . $pageNav->getLimitBox( $link ).' '.$pageNav->getPagesCounter();?>
							<br />
							<?php echo $pageNav->writePagesLinks( $link );?>
						</td>
					</tr>
			<?php } ?>
				</table>
				</td>		
			</tr>
			<?php if ($my->id){ ?>
			<tr>
				<td align="center" height="20">
				<div class="joomlalms_info_legend">
					<div style="text-align:left ">					
						<img class="JLMS_png" src="<?php echo $JLMS_CONFIG->get('live_site') . '/' . $JLMS_CONFIG->get('lms_path_to_images', 'components/com_joomla_lms/lms_images');?>/buttons/btn_complete.png" align="top" width="16" height="16" border="0" alt="<?php echo _JLMS_COURSES_ALREADY; ?>" title="<?php echo _JLMS_COURSES_ALREADY;?>" />
						&nbsp;- <?php echo _JLMS_COURSES_ALREADY;?>.
					</div>				
				</div>
				</td>
			</tr>
			<?php }

	//$controls = array();
	//$controls[] = array('href' => "javascript:jlms_submitbutton('add_to_cart');", 'title' => 'Add to Shopping Cart / View Shopping Cart', 'img' => 'cart');
	//JLMS_TMPL::ShowControlsFooter($controls, '', false);
		echo '<tr id="cart_footer_controls"><td align="center">';
		$toolbar = array();
		$toolbar[] = array('btn_type' => 'addtocart', 'btn_str' => _JLMS_ADD_TO_CART, 'btn_js' => "javascript:jlms_submitbutton('add_to_cart');");
		$toolbar[] = array('btn_type' => 'viewcart', 'btn_str' => _JLMS_VIEW_CART, 'btn_js' => "javascript:jlms_submitbutton('show_cart');");
		echo JLMS_ShowToolbar($toolbar, true, 'center');
		echo '</td></tr>';?>

		</table>
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
		<input type="hidden" name="task" value="add_to_cart" />
		<input type="hidden" name="id" value="" />
		<noscript>
			<div class="joomlalms_sys_message">
			<?php echo _JLMS_JS_COOKIES_REQUIRES;?>
			</div>
		</noscript>
		</form>
		<?php 
		} else {
			echo "<tr><td><div class='joomlalms_sys_message'>"._JLMS_SUBSCR_NO_SUBS."</div></td></tr></table>";
			?>
			<input type="hidden" name="option" value="<?php echo $option;?>" />
			<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
			<input type="hidden" name="task" value="add_to_cart" />
			<input type="hidden" name="id" value="" />
			</form>
			<?php
		}
		?>
		
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

	function ShowCartCheckOut( $option , &$subscriptions, &$lists, &$procs, $user) {
		global $JLMS_SESSION, $JLMS_CONFIG;

	if (count($subscriptions)) {?>
<script language="javascript" type="text/javascript">
	<!--
	var global_sub_proc_name = '';
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
	function jlms_checkout(pressbutton) {
		var form = document.JLMS_adminForm;
		if (pressbutton == 'checkout_cart') {
			var ppp = jq_Check_selectRadio('proc_id', 'JLMS_adminForm');
			if (!ppp ) {
				alert( "<?php echo 'Select payment method';?>" );
			} else {
			<?php if ($JLMS_CONFIG->get('enableterms')){?>
				if (!form){
					checkProcessor(ppp, '');
				}
				if( (pressbutton == 'checkout_cart') && (document.getElementById('agreement').checked == false) ){
					alert( "<?php echo addslashes(_JLMS_AGREEMENT);?>" );
				}else{
					processor = global_sub_proc_name;//readCookie('checked_proc');					
					if (eval('document.JLMS_'+processor+'')){
						eval('JLMS_'+processor+'_submit();');
					}else{						
						form.task.value = pressbutton;
						form.submit();
					}
				}
			<?php }else{?>
				processor = global_sub_proc_name;//readCookie('checked_proc');
				if (eval('document.JLMS_'+processor+'')){
					eval('JLMS_'+processor+'_submit();');
				}else{										
					form.task.value = pressbutton;
					//form.id.value = ttt;
					form.submit();
				}
			<?php }?>
			}
		}
	}
	//-->
</script>
<?php }

		$custom_code = JLMS_CART_html::ShowCart($option, $subscriptions, $lists, $procs);

		if (count($subscriptions)) {
			JLMS_TMPL::OpenMT('jlms_table_no_borders');
			JLMS_TMPL::OpenTS();

			$sub_proc = $JLMS_SESSION->get('sub_proc');
			if (isset($_COOKIE['proc_id'])){
				$sub_proc = $_COOKIE['proc_id'];
			}

			foreach ($procs as $proc){
				$display = 'none';
				if (count($procs) == 1)	{							
					$display = 'block';
				}
				if ($sub_proc) {
					if ($sub_proc == $proc->id){
						$display = 'block';
					}
				} else {
					if ($proc->default_p){							
						$display = 'block';
					}
				}
				echo '<div id="billing_form'.$proc->id.'" style="display:'.$display.' ">';
					require_once(_JOOMLMS_FRONT_HOME.'/includes/processors/'.$proc->filename.'.php');						
					$newClass = "JLMS_".$proc->filename;
					if (class_exists($newClass)) {
						$newProcObj = new $newClass();
						$newProcObj->show_billing_form($option, $proc, $user, $custom_code);
					} else {
					}
				echo "</div>";
			}
			JLMS_TMPL::CloseTS();
			JLMS_TMPL::OpenTS();?>			 
					<br /><?php if ($JLMS_CONFIG->get('enableterms')){
						echo "<table align='center' class='jlms_table_no_borders'><tr><td width='20'>
							<input type='checkbox' name='agreement' id='agreement' />
						</td><td style='text-align:left'>
							<label for='agreement'>" . $JLMS_CONFIG->get('jlms_terms')."</label>
						</td></tr></table>";
					};?><br />
					<?php $toolbar = array();
					$toolbar[] = array('btn_type' => 'checkout', 'btn_str' => _JLMS_CHECKOUT_ITEMS, 'btn_js' => "javascript:jlms_checkout('checkout_cart');");					
					echo JLMS_ShowToolbar($toolbar, true, 'center');
			JLMS_TMPL::CloseTS();
			JLMS_TMPL::CloseMT();
		}
	}
	
	function initSubscriptionPaymentParams( & $subscription, $rows2 )
	{
		
			$app = & JFactory::getApplication();
						
			$coupon_code = $app->getUserStateFromRequest( 'com_joomla_lms_dis_coupon_code', 'dis_coupon_code', '' );
				
			$p_coupon_disc = 0;
			$p_disc = 0;	
										
			$p_disc = JLMS_DISCOUNTS::getPercentDiscounts( $subscription->id );
			$p_coupon_disc = JLMS_DISCOUNTS::getPercentCouponDiscount( $coupon_code, $subscription->id );
												
			if ($subscription->account_type == '6') {						
				$sub_total = JLMS_RECURRENT_PAY::getFirstDayPrice( $subscription );								
			}else{
				$sub_total = round( $subscription->price,2 );		
			}		
														
			if( $sub_total < 0) $sub_total = 0;											
			
			$tax = 0;
			$tax_type = 0;					
			$tax_amount = 0;					
			$disc = 0;			
			
			if ($subscription->account_type=='5'){				 												
				$disc = $sub_total*( $subscription->discount/100 );												
			}
			
			if( $p_coupon_disc > 0 ) {
				$disc += $sub_total*$p_coupon_disc/100;							
			}		
					
			
			if( $p_disc > 0 ) {
				$disc += $sub_total*$p_disc/100;
			}	
			
			if (count($rows2) > 0) 
			{	
				$price = $sub_total - $disc;
				
				if( $rows2[0]->tax > 0 ) 
				{																				
					if ($rows2[0]->tax_type == 1) 
						$tax_amount = round( $price / 100 * $rows2[0]->tax, 2);
				}				
											
				$tax = $rows2[0]->tax;
				$tax_type = $rows2[0]->tax_type;															
			}
								
			$subscription->disc = $disc;										
			$subscription->tax = $tax;
			$subscription->tax_type = $tax_type;
			$subscription->tax_amount = $tax_amount;
			$subscription->sub_total = $sub_total;
			$subscription->total = $sub_total - $disc;
			if( $subscription->total < 0 )
				$subscription->total = $tax_amount;
			else
				$subscription->total += $tax_amount;									
			$subscription->p_coupon_disc = $p_coupon_disc;
			$subscription->p_disc = $p_disc;					
	}

	function ShowCart( $option , &$subscriptions, &$lists, $procs = array()) {
		global $Itemid, $JLMS_CONFIG, $JLMS_SESSION, $JLMS_DB, $my;
		$jlms_tax_counting = $JLMS_CONFIG->get('enabletax');
		$sub_total = 0;
		$tax_amount = 0;
		$rows2 = array();
		$custom_code = '';	
		$discounts = array();
		
		$app = & JFactory::getApplication();
						
		$coupon_code = $app->getUserStateFromRequest( 'com_joomla_lms_dis_coupon_code', 'dis_coupon_code', '' );	
						
		$discounts['t_coupon_disc'] = JLMS_DISCOUNTS::getTotalCouponDiscount( $coupon_code, $subscriptions );
		$discounts['t_disc'] = JLMS_DISCOUNTS::getTotalDiscounts( $subscriptions );	
		
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
				//$ip_address = '213.184.248.211';
//				$ip_address = '12.225.42.19';
//				$ip_address = '111.215.41.12';
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
				} else {
					$isset_country = true;
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
		function jq_Check_selectCheckbox(check_name, form_name) {
			selItem = eval("document."+form_name+"['"+check_name+"']");
			if (selItem) {
				if (selItem.length) { var i;
					for (i = 0; i<selItem.length; i++) {
						if (selItem[i].checked) {
							if (selItem[i].value) { return true; }
						}}
				} else if (selItem.checked) { return true; }}
			return false;
		}
		function jlms_submitbutton(pressbutton) {
			var form = document.JLMS_adminForm;
			if( pressbutton == 'remove_from_cart' ){
				var ttt = jq_Check_selectCheckbox('jlms_sub[]', 'JLMS_adminForm');
				if (ttt) {
					form.task.value = pressbutton;
					form.submit();
				} else {
					alert("<?php echo _JLMS_ALERT_SELECT_ITEM;?>");
				}
			}
			if( pressbutton == 'update_cart' ){
					form.task.value = pressbutton;
					form.submit();
			}
			if (pressbutton == 'cart_login') {
				form.task.value = pressbutton;
				form.submit();
			}
			
			if (pressbutton == 'apply_coupon_code') {
				form.task.value = pressbutton;
				form.submit();
			}
		}
		//-->
		</script>	

		<form action="<?php echo sefRelToAbs("index.php?option=$option&Itemid=$Itemid");?>" method="post" name="JLMS_adminForm">
		<input type="hidden" value="<?php echo $option;  ?>" name="option">
		<input type="hidden" value="<?php echo $Itemid; ?>" name="Itemid">
		<input type="hidden" value="" name="task">
		<input type="hidden" value="" name="id">
		<?php
		JLMS_TMPL::OpenMT();
		
		$params = array(
			'show_menu' => true,
			'simple_menu' => true,
		);
		JLMS_TMPL::ShowHeader('cart', _JLMS_MY_CART, $params);
		JLMS_TMPL::ShowPageTip('show_cart');

		JLMS_TMPL::CloseMT();
		$count_subs = count($subscriptions);
		 ?>
		<br />
		<?php if ( $count_subs > 0 ) { ?>		
		<input type="text" name="dis_coupon_code" value="<?php echo $coupon_code; ?>" />&nbsp;
		<input type="button" value="Check coupon" onclick="jlms_submitbutton('apply_coupon_code');" />
		<?php } ?>	
		<table cellpadding="0" cellspacing="0" border="0" width="100%" class="jlms_cart_list_outer" style="width:100%">
		<?php if ( $count_subs > 0){?>
			<tr>
				<td>
				<?php $need_upd_txt = false;
				for ($i=0, $n=count($subscriptions); $i < $n; $i++) {
					if (isset($subscriptions[$i]->allow_multiple) && $subscriptions[$i]->allow_multiple) {
						$need_upd_txt = true; break;
					}
				}
				$jlms_cs = $JLMS_CONFIG->get('jlms_cur_sign');										
				
				$total_subs = 0;
				$total_disc = 0;
				$total_tax = 0;				
				
				$discounts['total_p_disc'] = 0;
				
				$reccSubId = false;				
				
				for ($i=0, $n=count($subscriptions); $i < $n; $i++) 
				{										
					JLMS_CART_html::initSubscriptionPaymentParams( $subscriptions[$i], $rows2 );
					
					if( $subscriptions[$i]->account_type == '6' ) 
					{
						$reccSubId = $i;				
					}
					
									
					if (isset($subscriptions[$i]->count_items) && $subscriptions[$i]->count_items && isset($subscriptions[$i]->allow_multiple) && $subscriptions[$i]->allow_multiple) {
						$total_subs += $subscriptions[$i]->sub_total*$subscriptions[$i]->count_items;
						$total_tax += $subscriptions[$i]->tax_amount*$subscriptions[$i]->count_items;
						$total_disc += $subscriptions[$i]->disc*$subscriptions[$i]->count_items;							
					} else {
						$total_subs += $subscriptions[$i]->sub_total;
						$total_tax += $subscriptions[$i]->tax_amount;
						$total_disc += $subscriptions[$i]->disc;									
					}		
					
					if( $subscriptions[$i]->p_coupon_disc || $subscriptions[$i]->p_disc ) 
					{					
						$params['showDiscount'] = true;
					}								
				}
				
				if (isset($rows2[0]->tax_type) && $rows2[0]->tax_type == 2) {
					$total_tax += $rows2[0]->tax; // if tx is not in percentage....
				}		
				
								
				$discounts['total_p_disc'] = $total_disc;								
				$total_disc = $total_disc + ($discounts['t_coupon_disc'] + $discounts['t_disc']);					
				$total_price = $total_subs - $total_disc;								
				
				if( $total_price < 0 ) 
					$total_price = $total_tax;
				else	
					$total_price += $total_tax;
								
				$price_diff = $total_subs - $total_disc;
								 
				if( $price_diff < 0 ) {
					$balance = abs($price_diff);
				} else {
					$balance = 0;
				}												
				
				$recurr_total_desc = '';
				
				if( $reccSubId !== false  ) 
				{
					$price = JLMS_RECURRENT_PAY::getPriceDesc( $subscriptions[$reccSubId], 'basket_list', $balance, $total_price );	
					$recurr_total_desc = JLMS_RECURRENT_PAY::getPriceDesc( $subscriptions[$reccSubId], 'total', $balance, $total_price );
					
					if( $total_price == 0 ) 
					{															
						$total_price = JLMS_RECURRENT_PAY::getAmountFromReccurentPrice( $subscriptions[$reccSubId] );
					}
				}
																			 
				?>

					<div style="float:right; padding-top:5px; padding-right:25px">
						<b><?php echo _JLMS_SUBSCR_SUB_TOTAL.' '.$jlms_cs.sprintf('%.2f',round($total_price,2));?></b>
					</div>
				</td>
			</tr>
			<tr>
				<td>
				<?php if ($need_upd_txt) { ?>
				<table width="100%" cellpadding="0" cellspacing="0" border="0" class="jlms_table_no_borders">
					<tr>
					<td><div style="float:right; text-align:center"><?php echo _JLMS_CART_HAVE_CHANGED_QUANTITY;?><a href="javascript:jlms_submitbutton('update_cart');"><?php echo _JLMS_CART_UPDATE;?></a></div></td>
					</tr>
				</table>
				<?php } ?>

<?php
		$do_show_index = false;
				
		for ($i=0, $n=count($subscriptions); $i < $n; $i++) {
			$subscription = $subscriptions[$i];
			$s = 1;
			foreach ($subscription->course_names as $course_name){
				$course_usertype = 0;
				if ( in_array($subscription->courses[$s-1], $JLMS_CONFIG->get('teacher_in_courses',array(0))) ) {
					$course_usertype = 1;
				} elseif ( in_array($subscription->courses[$s-1], $JLMS_CONFIG->get('student_in_courses',array(0))) ) {
					$course_usertype = 2;
				}
				if ($course_usertype) {
					$do_show_index = true;
					break;
				}
				$s++;
			}
			if ($do_show_index) {
				break;
			}
		}
?>
					<table width="100%" cellpadding="0" cellspacing="0" border="0" class="<?php echo JLMSCSS::_('jlmslist', 'jlms_cart_list');?>">
					<tr>
						<?php if ($do_show_index) { ?><<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center" width="16">#</<?php echo JLMSCSS::tableheadertag();?>><?php } ?>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="left" width="20">&nbsp;</<?php echo JLMSCSS::tableheadertag();?>>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="left"><?php echo _JLMS_SUBSCRIBE_SUB_COURSES;?></<?php echo JLMSCSS::tableheadertag();?>>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="left"><?php echo _JLMS_DETAILS;?></<?php echo JLMSCSS::tableheadertag();?>>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center" width="170"><?php echo _JLMS_COURSES_PRICE;?></<?php echo JLMSCSS::tableheadertag();?>>						
						<?php if ( isset( $params['showDiscount'] ) && $params['showDiscount'] ) { ?>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center" width="40" nowrap="nowrap"><?php echo _JLMS_CART_DISCOUNT;?></<?php echo JLMSCSS::tableheadertag();?>>
						<?php } if( isset($rows2[0]) && $rows2[0]->tax_type == 1 && $rows2[0]->tax ) { ?>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center" width="40" nowrap="nowrap"><?php echo _JLMS_CART_TAX;?></<?php echo JLMSCSS::tableheadertag();?>>
						<?php } ?>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="center" width="40" nowrap="nowrap"><?php echo _JLMS_CART_QUANTITY;?></<?php echo JLMSCSS::tableheadertag();?>>
					</tr>
				<?php
				
				for ($i=0, $n=count($subscriptions); $i < $n; $i++) {
					$subscription = $subscriptions[$i];				
					$tax_amount = 0;
					$disc = 0;					
															
					if ($subscription->account_type !='6') 
					{						
						$price = $jlms_cs.sprintf('%.2f',round($subscriptions[$i]->sub_total,2));					
					}				

					$price_sub = $price;
					$custom_code .= JLMS_CART_html::ShowSub($subscription, ($i + 1), '', $price_sub, $do_show_index, false, $params);

				}											
				?>
				</table>
				</td>
			</tr>
			<tr>
				<td>
					<div style="float:right; padding-top:5px; padding-right:25px; text-align:right;">
						<?php													 							 	
							if( $total_tax || $total_disc ) { 
								echo '<b>'.$jlms_cs.sprintf('%.2f',round($total_subs,2)).'</b><br />';
								if( $total_disc ) 	
									echo '<b>'._JLMS_SUBSCR_DISCOUNT_AMOUNT.' '.$jlms_cs.sprintf('%.2f',round($total_disc,2)).'</b><br />';
								if( $total_tax )
									echo '<b>'._JLMS_SUBSCR_TAX_AMOUNT.' '.$jlms_cs.sprintf('%.2f',round($total_tax,2)).'</b><br />';								
							}
							
							echo '<b>'._JLMS_SUBSCR_SUB_TOTAL.' '.$jlms_cs.sprintf('%.2f',round($total_price,2)).'</b><br />';
							echo '<b>'.$recurr_total_desc.'</b>';?>
					</div>
				</td>
			</tr>
<?php
	$controls = array();
	if ($JLMS_CONFIG->get('under_ssl') && $JLMS_CONFIG->get('real_live_site')) {
		$temp_href = $JLMS_CONFIG->get('real_live_site')."/index.php?option=$option&amp;Itemid=$Itemid&amp;task=subscription";
	} else {
		$temp_href = $JLMS_CONFIG->get('live_site')."/index.php?option=$option&amp;Itemid=$Itemid&amp;task=subscription";
	}
	$controls[] = array('href' => $temp_href, 'title' => _JLMS_CART_BACK_TO_SHOP, 'img' => 'back');
	$controls[] = array('href' => "javascript:jlms_submitbutton('remove_from_cart');", 'title' => _JLMS_CART_REMOVE, 'img' => 'cartremove');
	JLMS_TMPL::ShowControlsFooter($controls, '', false);
?>
		</table>
<?php 	if ($my->id && !empty($procs)) {
			JLMS_TMPL::OpenMT('jlms_table_no_borders');
			JLMS_TMPL::OpenTS();
	
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
				}
				echo '<input type="radio" name="proc_id" value="'.$proc->id.'" onclick="checkProcessor(\''.$proc->id.'\',\''.$proc->filename.'\');" id="proc_'.$proc->id.'" '.$checked.' />&nbsp;<label class="msspro_sel_proc2" for="proc_'.$proc->id.'">'.$proc->name.'</label><br />';
			}
			if($temp2){
				echo '<script type="text/javascript" language="javascript"><!--'."\r\n";
				echo $temp2;
				echo "\r\n--></script>";
			}
			JLMS_TMPL::CloseTS();
			JLMS_TMPL::CloseMT();
		} ?>	
	<?php		 
			echo "</form>";		
			if (!$my->id){
				JLMS_UserSessions_html::loginPanel();
			}
		} else {
			echo "<tr><td><div class='joomlalms_sys_message'>";
			echo _JLMS_CART_IS_EMPTY;
			echo '<br />';
			if ($JLMS_CONFIG->get('under_ssl') && $JLMS_CONFIG->get('real_live_site')) {
				$temp_href = $JLMS_CONFIG->get('real_live_site')."/index.php?option=$option&amp;Itemid=$Itemid&amp;task=subscription";
			} else {
				$temp_href = $JLMS_CONFIG->get('live_site')."/index.php?option=$option&amp;Itemid=$Itemid&amp;task=subscription";
			}
			echo '<a href="'.$temp_href.'" title="'._JLMS_CART_BACK_TO_SHOP.'">['._JLMS_CART_BACK_TO_SHOP.']</a>';
			echo "</div></td></tr></table>";
			echo "</form>";
		}
		return $custom_code;
	}

	function ShowSub(&$subscription, $pageNav_num, $checked, $price, $show_index = true, $show_itemnum = true, $params = false) {
		global $JLMS_CONFIG;
		$for_custom_code = '';?>
		<tr valign="middle" style="vertical-align:middle" class="<?php echo $JLMS_CONFIG->get('visual_set_main_row_class'); ?>">
		<?php if ($show_index) { ?>
			<td width="15" align="center"><?php echo $show_itemnum ? $pageNav_num : '&nbsp;';?></td>
		<?php } ?>
			<td width="20" align="left">
				<input type="checkbox" name="jlms_sub[]" value="<?php echo $subscription->id;?>" <?php echo $checked;?> />
				<input type="hidden" name="jlms_sub_ids[]" value="<?php echo $subscription->id;?>" />
			</td>
			<td style="font-weight:bold; "><?php echo $subscription->sub_name;?></td>
			<td <?php echo ($subscription->account_type == 6)?'colspan="2"':""; ?>><?php
			$for_custom_code = '<input type="hidden" name="jlms_sub_ids[]" value="'.$subscription->id.'" />';
			if ($subscription->account_type == 2) {
				echo _JLMS_SUBSCR_FROM.' '.JLMS_dateToDisplay($subscription->start_date).' '._JLMS_SUBSCR_TO.' '.JLMS_dateToDisplay($subscription->end_date);
			} elseif ($subscription->account_type == 3) {
				echo _JLMS_SUBSCR_FROM.' '.JLMS_dateToDisplay($subscription->start_date).' '._JLMS_SUBSCR_TO.' '._JLMS_DATE_LIFETIME;
			} elseif ($subscription->account_type == 4) {
				echo $subscription->access_days.' '._JLMS_SUBSCR_DAYS_ACCESS;//echo _JLMS_SUBSCR_FROM.' '.JLMS_dateToDisplay(date('Y-m-d')).' '._JLMS_SUBSCR_TO.' '.JLMS_dateToDisplay(date('Y-m-d',strtotime('+'.$subscription->access_days.' day')));
			} elseif ($subscription->account_type == 100) {
				echo $subscription->details. ' credits/courses';
			} elseif ($subscription->account_type == 6) {
				null;
			} else {
				echo "-";
			}?>
			<?php if ( $subscription->account_type != 6 ) { ?>
			</td>
			<td align="center">
			<?php } ?>
			<?php echo $price;?>
			</td>
			<?php			
						
			if( isset( $params['showDiscount'] ) && $params['showDiscount'] ) {				
				$disc = $subscription->p_coupon_disc + $subscription->p_disc + $subscription->discount; 
				if( $disc > 0 ) {
					echo "<td align=\"center\"><font color=\"green\">".$disc."%</font></td>";		
				} else {
					echo "<td align=\"center\"><font color=\"green\">-</font></td>";
				}	
			}
										 
			if( isset($subscription->tax) && $subscription->tax > 0 && $subscription->tax_type == 1 ) {				
				echo "<td align=\"center\"><font color=\"red\">".$subscription->tax."%</font></td>";					
			} 				
			
			?>		
			<?php if (isset($subscription->count_items) && $subscription->count_items && isset($subscription->allow_multiple) && $subscription->allow_multiple) {
				echo "<td align='center'><input type='text' class='inputbox' style='border: 1px solid #c6c6c6;' name='sub_count[]' size='3' value='".$subscription->count_items."' /></td>";
				$for_custom_code .= "<input type='hidden' name='sub_count[]'  value='".$subscription->count_items."' />";
			} elseif (isset($subscription->count_items) && $subscription->count_items) {
				echo "<td align='center'>1<input type='hidden' name='sub_count[]'  value='1' /></td>";
				$for_custom_code .= "<input type='hidden' name='sub_count[]'  value='1' />";
			}?>
		</tr>
		<?php
		$colspan = 3;			
		if( isset($subscription->count_items) && $subscription->count_items ) {			
			$colspan++;
		}
		
		if( isset( $params['showDiscount'] ) && $params['showDiscount'] ) {
			$colspan++;
		}
		
		if( isset($subscription->tax) && $subscription->tax > 0 ) { 
			$colspan++;
		}
			
		if (isset($subscription->description) && $subscription->description) {
			$img1 = "<img src=\"".$JLMS_CONFIG->get('live_site')."/components/com_joomla_lms/lms_images/treeview/empty_line.png\" width='16' height='16' alt=''/>";			 	
		?>
			<tr class="<?php echo $JLMS_CONFIG->get('visual_set_child_row_class'); ?>">
			<?php if ($show_index) { ?>
				<td><?php echo $img1;?></td>
			<?php } ?>
				<td align="center"><?php echo $img1;?></td>
				<td align="left" colspan="<?php echo $colspan; ?>" valign="middle">							
				<?php echo $subscription->description;?>
				</td>
			</tr>
		<?php } else {
			$s = 1;
			foreach ($subscription->course_names as $course_name){
				if ($s != count($subscription->courses) ){
					$src = $JLMS_CONFIG->get('live_site').'/components/com_joomla_lms/lms_images/treeview/sub1.png';
				}else{
					$src = $JLMS_CONFIG->get('live_site').'/components/com_joomla_lms/lms_images/treeview/sub2.png';
				}
				$img1 = "<img src=\"".$JLMS_CONFIG->get('live_site')."/components/com_joomla_lms/lms_images/treeview/empty_line.png\" width='16' height='16' alt=''/>";
				$course_usertype = 0;
				if ( in_array($subscription->courses[$s-1], $JLMS_CONFIG->get('teacher_in_courses',array(0))) ) {
					$course_usertype = 1;
				} elseif ( in_array($subscription->courses[$s-1], $JLMS_CONFIG->get('student_in_courses',array(0))) ) {
					$course_usertype = 2;
				}
				if ($course_usertype) {
					$img1 = "<img src=\"".$JLMS_CONFIG->get('live_site')."/components/com_joomla_lms/lms_images/toolbar/btn_accept.png\" width='16' height='16' alt=''/>";
				}?>
				<tr class="<?php echo $JLMS_CONFIG->get('visual_set_child_row_class'); ?>">
				<?php if ($show_index) { ?>
					<td><?php echo $img1;?></td>
				<?php } ?>
					<td align="center">
					<?php
					echo "<img src=\"$src\" width='16' height='16' alt=''/>";	
					?>
					</td>
					<td style="text-indent:16px;" align="left" colspan="<?php echo $colspan;?>" valign="middle">							
					<?php echo $course_name;?>
					</td>
				</tr>
				<?php 
				$s++;
			}
		}
		return $for_custom_code;
	}
}
?>