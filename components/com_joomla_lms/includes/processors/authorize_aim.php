<?php
defined( '_JLMS_EXEC' ) or die( 'Restricted access' );
class JLMS_authorize_aim {

	function show_checkout($option, $subscription, $item_id, $proc ) {
		if (mosGetParam($_REQUEST, 'act', '') == 'pay'){
			
			process_authorize($subscription, $item_id, $proc);
		}
	}

	function show_billing_form($option, $proc, $user, $custom_code = ''){
	global $my, $Itemid, $JLMS_CONFIG;
	$params2 = new JLMSParameters( $proc->params );
	
	$app =& JFactory::getApplication(); 
	
	$link = sefRelToAbs('index.php?option=com_joomla_lms&Itemid='.$Itemid);
	if ($params2->get('enable_https')) $link = str_replace('http://', 'https://', $link);
	
		#Form name should be "JLMS_" + processor name
		?>
		<script type="text/javascript" language="javascript">
		<!--
		function JLMS_authorize_aim_submit(){
			
			var ttt = jq_Check_selectRadio('jlms_sub', 'JLMS_adminForm');
			var this_form = document.JLMS_authorize_aim;
			err_access = new Array(); plms_focus_access = 0;
			this_form.id.value = ttt;
			this_form.jlms_sub.value = ttt;
			if(this_form.x_first_name.value == ''){
				alert('<?php echo _JLMS_ENTER_FIRST_NAME;?>');
				this_form.x_first_name.focus();
			}else if(this_form.x_last_name.value == ''){
				alert('<?php echo _JLMS_ENTER_LAST_NAME;?>');
				this_form.x_last_name.focus();
			}else if(this_form.x_address.value == ''){
				alert('<?php echo _JLMS_ENTER_ADDRESS;?>');
				this_form.x_address.focus();
			}else if(this_form.x_city.value == ''){
				alert('<?php echo _JLMS_ENTER_CITY;?>');
				this_form.x_city.focus();
			}else if(this_form.x_zip.value == ''){
				alert('<?php echo _JLMS_ENTER_POSTAL_CODE;?>');
				this_form.x_zip.focus();
			}else if(this_form.x_country.value == ''){
				alert('<?php echo _JLMS_ENTER_COUNTRY;?>');
				this_form.x_country.focus();
			}else if(this_form.x_phone.value == ''){
				alert('<?php echo _JLMS_ENTER_PHONE;?>');
				this_form.x_phone.focus();
			}else if(!validate_email(this_form.x_email.value)){
				alert('<?php echo _JLMS_ENTER_EMAIL;?>');
				this_form.x_email.focus();
			}else if (this_form.x_card_num.value == ''){
				alert('<?php echo _JLMS_ENTER_CARD_NUMBER;?>');				
				this_form.x_card_num.focus();
			}else if(this_form.x_card_code.value == ''){
				alert('<?php echo _JLMS_ENTER_CARD_CODE;?>');
				this_form.x_card_num.focus();
			}else{
				this_form.submit();
			}
		}
		function validate_email(email){
			var reg = new RegExp("[0-9a-z_]+@[0-9a-z\-_^.]+\\.[a-z]", 'i');
			if (!reg.test(email)) { return false;}
			else return true;
		}
		-->
		</script>
		<form action="<?php echo $link;?>" method="post" name="JLMS_authorize_aim" id="JLMS_authorize_aim"><br />
		<table cellpadding="0" cellspacing="2" width="100%" border="0" class="jlms_table_no_borders">
		<?php if ($params2->get( 'pre_text' )) {?>
		<tr><td colspan="2"><br /><?php echo nl2br($params2->get( 'pre_text' ));?></td></tr>						
		<?php }?>
		<tr>
			<td align="left" valign="top" width="50%">
				<table cellpadding="0" cellspacing="2" width="100%" class="jlms_table_no_borders">
					<tr><td colspan="2" class="sectiontableheader"><?php echo ($JLMS_CONFIG->get('is_joomla_16', false)) ? '<h2>' : '';?><?php echo _JLMS_SUBSCRIBE_ORDER_INFO;?><?php echo ($JLMS_CONFIG->get('is_joomla_16', false)) ? '</h2>' : '';?></td></tr>
					<?php /* available user info			
					$my->id - 			UserID of Joomla
					$my->username - 	Username
					$my->name - 		Name
					$my->email - 		Email
					//CB Info
					$user->first_name -	First name
					$user->last_name - 	Last Name 
					$user->address - 	Address 
					$user->city -	 	City
					$user->state -	 	State
					$user->postal_code -Postal Code
					$user->country - 	User Country
					$user->phone - 	 	Phone
					 */?>					
					<tr><td><?php echo _JLMS_USER_FIRSTNAME;?> *</td><td><input type="text" name="x_first_name" class="inputbox"  value="<?php echo $user ? $user->first_name : '';?>"/></td></tr>
					<tr><td><?php echo _JLMS_USER_LASTTNAME;?> *</td><td><input type="text" name="x_last_name" class="inputbox" value="<?php echo $user ? $user->last_name : '';?>" /></td></tr>
					<tr><td><?php echo _JLMS_USER_ADDRESS;?> *</td><td><input type="text" name="x_address" class="inputbox" value="<?php echo $user ? $user->address : '';?>" /></td></tr>
					<tr><td><?php echo _JLMS_USER_CITY;?> *</td><td><input type="text" name="x_city" class="inputbox" value="<?php echo $user ? $user->city : '';?>" /></td></tr>
					<tr><td><?php echo _JLMS_USER_STATE;?> </td><td><input type="text" name="x_state" class="inputbox" value="<?php echo (isset($user->state) && $user->state) ? $user->state : '';?>"  /></td></tr>
					<tr><td><?php echo _JLMS_USER_POSTAL_CODE;?> *</td><td><input type="text" name="x_zip" class="inputbox" value="<?php echo $user ? $user->postal_code : '';?>"  /></td></tr>
					<tr><td><?php echo _JLMS_USER_COUNTRY;?> *</td><td><input type="text" name="x_country" class="inputbox" value="<?php echo $user ? $user->country : '';?>"  /></td></tr>
					<tr><td><?php echo _JLMS_USER_EMAIL;?> *</td><td><input type="text" name="x_email" class="inputbox" value="<?php echo $my->email;?>" /></td></tr>
					<tr><td><?php echo _JLMS_USER_PHONE;?> *</td><td><input type="text" name="x_phone" class="inputbox" value="<?php echo $user ? $user->phone : '';?>"  /></td></tr>
					<tr><td colspan="2"><div class="joomlalms_info_legend"><div style="text-align: left;"> <?php echo _JLMS_FIELD_REQUIRED;?></div></div></td></tr>
					<tr><td colspan="2">&nbsp;</td></tr>
				</table>
			</td>
			<td align="left" valign="top" width="50%">
				<table cellpadding="0" cellspacing="2" width="100%" class="jlms_table_no_borders">
					<tr><td colspan="2"  class="sectiontableheader"><?php echo ($JLMS_CONFIG->get('is_joomla_16', false)) ? '<h2>' : '';?><?php echo _JLMS_SUBSCRIBE_CARD_INFO;?><?php echo ($JLMS_CONFIG->get('is_joomla_16', false)) ? '</h2>' : '';?></td></tr>
					<tr><td id="card_number"><?php echo _JLMS_SUBSCRIBE_CARD_NUMBER;?> *</td><td><input type="text" name="x_card_num" class="inputbox" /></td></tr>
					<tr><td id="card_cvn"><?php echo _JLMS_SUBSCRIBE_CARD_CVN;?> *</td><td><input type="text" name="x_card_code" class="inputbox" size="5" /></td></tr>
					<tr><td><?php echo _JLMS_SUBSCRIBE_EXP_DATE;?> *</td>
						<td><select name="card_expirationMonth" class="inputbox">
							<option value="01">01</option>
							<option value="02">02</option>
							<option value="03">03</option>
							<option value="04">04</option>
							<option value="05">05</option>
							<option value="06">06</option>
							<option value="07">07</option>
							<option value="08">08</option>
							<option value="09">09</option>
							<option value="10">10</option>
							<option value="11">11</option>
							<option value="12">12</option>
							</select>
							&nbsp;
							<select name="card_expirationYear" class="inputbox">
							<?php for ($x=date("Y"); $x< (date("Y")+10); $x++) {?>
							<option value="<?php echo substr($x, 2, 2);?>"><?php echo $x;?></option>
							<?php }?>
							</select>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		</table>
		<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
		<input type="hidden" name="proc_id" value="<?php echo $proc->id;?>" />
		<input type="hidden" name="dis_coupon_code2" value="<?php echo $app->getUserStateFromRequest( 'com_joomla_lms_dis_coupon_code', 'dis_coupon_code', '' ); ?>" />
		<input type="hidden" name="task" value="<?php echo $JLMS_CONFIG->get('use_cart',false) ? 'checkout_cart' : 'subscribe';?>" />
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="jlms_sub" value="" />
		<input type="hidden" name="id" value="" />
		<input type="hidden" name="act" value="pay" />
		<?php echo $custom_code;?>
		</form>
		<?php
	}
}

class authorizenet_class {

   var $field_string;
   var $fields = array();
   
   var $response_string;
   var $response = array();
   
   var $gateway_url = "https://secure.authorize.net/gateway/transact.dll";
   
   function add_field($field, $value) {
      $this->fields["$field"] = urlencode($value);   
   }

   function process() {
       global $option, $Itemid;
		// This function actually processes the payment.  This function will 
		// load the $response array with all the returned information.  The return
		// values for the function are:
		// 1 - Approved
		// 2 - Declined
		// 3 - Error
	 
		// construct the fields string to pass to authorize.net
		foreach( $this->fields as $key => $value ){ 
			//$this->field_string .= "$key=" . urlencode( $value ) . "&";
			// 29.11.2007 (DEN) - bug withprinting customer emails at the Authorize side (double urlencode!)
			$this->field_string .= "$key=" . $value  . "&";
		}  
		  // execute the HTTPS post via CURL
		
		if (function_exists('curl_init')){
			$ch = curl_init($this->gateway_url); 
			curl_setopt($ch, CURLOPT_HEADER, 0); 
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
			curl_setopt($ch, CURLOPT_POSTFIELDS, rtrim( $this->field_string, "& " )); 
		}else{
			JLMSRedirect(sefRelToAbs('index.php?option='.$option.'&task=subscription&Itemid='.$Itemid), 'This payment type is not supported by server.');
		}
		//curl_setopt ($ch, CURLOPT_HTTPPROXYTUNNEL, TRUE);
		//curl_setopt ($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
		//curl_setopt ($ch, CURLOPT_PROXY,"http://proxy.shr.secureserver.net:3128");
		//curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		$this->response_string = urldecode(curl_exec($ch)); 
		  
		if (curl_errno($ch)) {
			$this->response['Response Reason Text'] = curl_error($ch);
			return 3;
		}else {curl_close ($ch);}
	 
		   
		// load a temporary array with the values returned from authorize.net
		$temp_values = explode('|', $this->response_string);
	 
		// load a temporary array with the keys corresponding to the values 
		// returned from authorize.net (taken from AIM documentation)
		$temp_keys= array ( 
			"Response Code", "Response Subcode", "Response Reason Code", "Response Reason Text",
			"Approval Code", "AVS Result Code", "Transaction ID", "Invoice Number", "Description",
			"Amount", "Method", "Transaction Type", "Customer ID", "Cardholder First Name",
			"Cardholder Last Name", "Company", "Billing Address", "City", "State",
			"Zip", "Country", "Phone", "Fax", "Email", "Ship to First Name", "Ship to Last Name",
			"Ship to Company", "Ship to Address", "Ship to City", "Ship to State",
			"Ship to Zip", "Ship to Country", "Tax Amount", "Duty Amount", "Freight Amount",
			"Tax Exempt Flag", "PO Number", "MD5 Hash", "Card Code (CVV2/CVC2/CID) Response Code",
			"Cardholder Authentication Verification Value (CAVV) Response Code"
		);
	 
		// add additional keys for reserved fields and merchant defined fields
		for ($i=0; $i<=27; $i++) {
			array_push($temp_keys, 'Reserved Field '.$i);
		}
		$i=0;
		while (sizeof($temp_keys) < sizeof($temp_values)) {
			array_push($temp_keys, 'Merchant Defined Field '.$i);
			$i++;
		}
	 
		// combine the keys and values arrays into the $response array.  This
		// can be done with the array_combine() function instead if you are using
		// php 5.
		for ($i=0; $i<sizeof($temp_values);$i++) {
			$this->response["$temp_keys[$i]"] = $temp_values[$i];
		}
		// Return the response code.
		return $this->response['Response Code'];
	}
   
	function get_response_reason_text() {
		return $this->response['Response Reason Text'];
	}

	function dump_fields() {
 
      // Used for debugging, this function will output all the field/value pairs
      // that are currently defined in the instance of the class using the
      // add_field() function.
      
      echo "<h3>authorizenet_class->dump_fields() Output:</h3>";
      echo "<table width=\"95%\" border=\"1\" cellpadding=\"2\" cellspacing=\"0\">
            <tr>
               <td bgcolor=\"black\"><b><font color=\"white\">Field Name</font></b></td>
               <td bgcolor=\"black\"><b><font color=\"white\">Value</font></b></td>
            </tr>"; 
            
      foreach ($this->fields as $key => $value) {
         echo "<tr><td>$key</td><td>".urldecode($value)."&nbsp;</td></tr>";
      }
 
      echo "</table><br>"; 
	}

	function dump_response() {
		// Used for debuggin, this function will output all the response field
		// names and the values returned for the payment submission.  This should
		// be called AFTER the process() function has been called to view details
		// about authorize.net's response.

		echo "<h3>authorizenet_class->dump_response() Output:</h3>";
		echo "<table width=\"95%\" border=\"1\" cellpadding=\"2\" cellspacing=\"0\">
			<tr>
				<td bgcolor=\"black\"><b><font color=\"white\">Index&nbsp;</font></b></td>
				<td bgcolor=\"black\"><b><font color=\"white\">Field Name</font></b></td>
				<td bgcolor=\"black\"><b><font color=\"white\">Value</font></b></td>
			</tr>";

		$i = 0;
		foreach ($this->response as $key => $value) {
			echo "<tr>
					<td valign=\"top\" align=\"center\">$i</td>
					<td valign=\"top\">$key</td>
					<td valign=\"top\">$value&nbsp;</td>
				</tr>";
			$i++;
		} 
		echo "</table><br>";
	}
}

function process_authorize($subscription, $item_id, $proc) {
	global $Itemid, $JLMS_DB, $JLMS_CONFIG;

	$params2 = new JLMSParameters( $proc->params );
	
	$orderNumber = $item_id;//$params2->get( 'x_invoice_num' );
	$orderDescr = $params2->get( 'x_description' );
	
	if (!$subscription->sub_name && count($subscription->subscriptions) && ( strpos($params2->get( 'x_description'), '[sub]') !== false || strpos($params2->get( 'x_description'), '[SUB]') !== false) ) {
		$subscr_ids = implode(',', $subscription->subscriptions);
		$query = "SELECT id, sub_name FROM #__lms_subscriptions WHERE id IN ($subscr_ids)";
		$JLMS_DB->SetQuery($query);
		$subs_names_list_db = $JLMS_DB->LoadObjectList();
		//create item name from list of all subscriptions separated by comma, ordered as in cart
		$subs_names_list = array();
		foreach ($subscription->subscriptions as $cart_sub_id) {
			foreach ($subs_names_list_db as $subname_item) {
				if ($subname_item->id == $cart_sub_id) {
					$subs_names_list[] = $subname_item->sub_name;
					break;
				}
			}
		}
		$subscription->sub_name = implode(', ', $subs_names_list);
	}

	$orderDescr = str_replace('[SUB]', $subscription->sub_name, $orderDescr);
	$orderDescr = str_replace('[sub]', $subscription->sub_name, $orderDescr);
	
	$a = new authorizenet_class();

	//if ($params2->get('x_test_request') == "TRUE") $a->gateway_url = 'https://test.authorize.net/gateway/transact.dll';

	$a->add_field('x_login', $params2->get('x_login'));
	$a->add_field('x_tran_key', $params2->get('x_tran_key'));

	$a->add_field('x_invoice_num', $orderNumber);
	$a->add_field('x_description', $orderDescr);

	$a->add_field('x_version', '3.1');
	$a->add_field('x_type', 'AUTH_CAPTURE');
	$a->add_field('x_test_request', $params2->get('x_test_request'));
	$a->add_field('x_relay_response', 'FALSE');

	$a->add_field('x_delim_data', 'TRUE');
	$a->add_field('x_delim_char', '|');
	$a->add_field('x_encap_char', '');

	$a->add_field('x_email_customer', $params2->get('x_email_customer'));
	$a->add_field('x_merchant_email', $params2->get('x_merchant_email'));

	$query = "SELECT user_id FROM `#__lms_payments` WHERE id = $item_id";
	$JLMS_DB->setQuery($query);
	$user_id_of_payment = $JLMS_DB->LoadResult();
	$user_id_of_payment = intval($user_id_of_payment);

	$a->add_field('x_first_name', mosGetParam($_POST, 'x_first_name', ''));
	$a->add_field('x_last_name', mosGetParam($_POST, 'x_last_name', ''));
	$a->add_field('x_cust_id', $user_id_of_payment);
	$a->add_field('x_address', mosGetParam($_POST, 'x_address', ''));
	$a->add_field('x_city', mosGetParam($_POST, 'x_city', ''));
	$a->add_field('x_state', mosGetParam($_POST, 'x_state', ''));
	$a->add_field('x_zip', mosGetParam($_POST, 'x_zip', ''));
	$a->add_field('x_country', mosGetParam($_POST, 'x_country', ''));
	$a->add_field('x_email', mosGetParam($_POST, 'x_email', ''));
	$a->add_field('x_phone', mosGetParam($_POST, 'x_phone', ''));

	$a->add_field('x_method', 'CC');
	$a->add_field('x_card_num', mosGetParam($_POST, 'x_card_num', '')); 
	$a->add_field('x_amount', number_format($subscription->price + $subscription->tax_amount, 2, '.', '') );
	$a->add_field('x_currency_code', $JLMS_CONFIG->get('jlms_cur_code') );
	$a->add_field('x_exp_date', mosGetParam($_POST, 'card_expirationMonth', '').mosGetParam($_POST, 'card_expirationYear', ''));
	$a->add_field('x_card_code', mosGetParam($_POST, 'x_card_code', ''));

	switch ($a->process()) {

		case 1:  // Successs
			//$payment_amount = ($subscription->price + $subscription->tax_amount);			
			$payment_currency = $JLMS_CONFIG->get('jlms_cur_code');
			$txn_id = $a->response['Transaction ID'];
			require_once(_JOOMLMS_FRONT_HOME.'/includes/joomla_lms.subscription.lib.php');
			//if (!jlms_check_payment_transaction(($payment_amount - $subscription->tax_amount), $item_id)) { die('Invalid payment amount'); }
			$payment_date = date('Y-m-d H:i:s');

			$query = "SELECT status FROM `#__lms_payments` WHERE id = $item_id ";
			$JLMS_DB->setQuery($query);
			$prev_payment = $JLMS_DB->LoadResult();
			jlms_update_payment( $item_id, $txn_id, 'Completed', $payment_date, $subscription->tax_amount );
			if ($prev_payment == 'Completed') {
			} else {
				jlms_register_new_user($item_id);
				//TODO: generate invoice only if enabled
				JLMS_CART_generateinvoice( $item_id, $params2);
			}
			setcookie('joomlalms_cart_contents', '', time()-3600, '/');
			
/*SoulPowerUniversity_MOD*/
			/*
			mail_notification($subscription);
			*/
/*SoulPowerUniversity_MOD*/
			
			if ($params2->get( 'return_url' ) == '') {
				$query = "SELECT b.course_id FROM `#__lms_payments` as a, `#__lms_subscriptions_courses` as b WHERE a.id = $item_id AND a.sub_id = b.sub_id ";
				$JLMS_DB->setQuery($query);
				$courses = $JLMS_DB->loadObjectList();

				if(count($courses) == 1){
					JLMSRedirect( sefRelToAbs("index.php?option=com_joomla_lms&amp;task=details_course&amp;id=".$courses[0]->course_id."&amp;Itemid=".$Itemid), $params2->get( 'success_message'));	
				} else {
					JLMSRedirect( sefRelToAbs("index.php?option=com_joomla_lms&Itemid=$Itemid"), $params2->get( 'success_message' ) );
				}
			} else {
				JLMSRedirect($params2->get( 'return_url' ));
			}	
		break;

		case 2:  // Declined
			$error_text = str_replace(array("\r\n", "\r", "\n"), '\n', $a->get_response_reason_text());
			echo "<script> alert(\"".addslashes($error_text)."\"); window.history.go(-1); </script>\n";
			exit(); 
		break;

		case 3:  // Error
			$error_text = str_replace(array("\r\n", "\r", "\n"), '\n', $a->get_response_reason_text());
			echo "<script> alert(\"".addslashes($error_text)."\"); window.history.go(-1); </script>\n";
			exit(); 
		break;
	}
}

function mail_notification($subscription){
	if(in_array(15, $subscription->courses)){
		jimport( 'joomla.mail.helper' );
		
		$JLMS_CONFIG = & JLMSFactory::getConfig(); 		
		
		$SiteName 	= $JLMS_CONFIG->get('sitename');
		$MailFrom 	= $JLMS_CONFIG->get('mailfrom');
		$FromName 	= $JLMS_CONFIG->get('fromname'); 
		
		JLoader::import('autoresponder_spu', JPATH_SITE, '');
		
		$subject = AutoResponder::getSubject();
		
		$body	= AutoResponder::getBody();
		$body	= sprintf( $body );
		
		$subject = JMailHelper::cleanSubject($subject);
		$body	 = JMailHelper::cleanBody($body);
		$from	 = $SiteName . ' ' . $FromName;
		$sender	 = JMailHelper::cleanAddress($MailFrom);
		$email 	 = JMailHelper::cleanAddress(JRequest::getVar('x_email', ''));
		
		$user = & JFactory::getUser();
		$name = explode(' ', $user->name);
		$firstname = isset($name[0]) && $name[0] ? $name[0] : $user->name;
		$body = str_replace('{firstname}', $firstname, $body);
		
		if ( JUtility::sendMail($from, $sender, $email, $subject, $body, true) !== true )
		{
			JError::raiseNotice( 500, JText:: _ ('EMAIL_NOT_SENT' ));
		}
	}
}
?>