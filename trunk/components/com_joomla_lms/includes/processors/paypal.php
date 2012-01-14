<?php
defined( '_JLMS_EXEC' ) or die( 'Restricted access' );

require_once(_JOOMLMS_FRONT_HOME . DS . "includes" . DS . "libraries" . DS . "lms.lib.recurrent.pay.php");

class JLMS_paypal{

function show_checkout( $option, $subscription, $item_id, $proc ) {
	global $Itemid, $JLMS_CONFIG, $JLMS_DB;
	
	$params = new JLMSParameters( $proc->params );
	if (!$params->get( 'server_url' ) || !$params->get( 'business_email' )) {
		$redirect_task = 'subscription';
		if ($subscription->payment_type == 2) {
			$redirect_task = 'show_cart';
		}
		JLMSredirect(sefRelToAbs("index.php?option=".$option."&Itemid=".$Itemid."&task=$redirect_task"), "This payment method is not available at the moment.<br /> Ask site administrator to check payment method settings.");
	}
	//setcookie('joomlalms_cart_contents', '', time()-3600, '/');
	JLMSCookie::setcookie('joomlalms_cart_contents', '$current_cart_cookie',time()-3600, '/');
	global $JLMS_DB;
	
	$subscr_ids = implode(',', $subscription->subscriptions);

	if (!$subscription->sub_name && count($subscription->subscriptions) && ( strpos($params->get( 'item_name'), '[sub]') !== false || strpos($params->get( 'item_name'), '[SUB]') !== false) ) {
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
	
	if ($subscription->payment_type) {		
		$query = "SELECT course_id FROM #__lms_subscriptions_courses WHERE sub_id IN (".$subscr_ids.")";
		$JLMS_DB->SetQuery($query);
		$courses = $JLMS_DB->LoadResultArray();
	} else {
		$courses = array();
	}

	$where = '';	
	$whereacc = '';	
	$where .= " AND ps.subscr_id IN (".$subscr_ids.")";
	$query = "SELECT p.id, p.name, p.description, p.published, p.p1, p.t1, p.p2, p.t2, p.p3, p.t3, s.a1, s.a2, s.a3, p.sra, p.src, p.srt, p.params" 
			."\n FROM #__lms_plans_subscriptions ps, #__lms_plans p, #__lms_subscriptions s"
			."\n WHERE s.id=ps.subscr_id AND ps.plan_id=p.id AND s.account_type=6".$where;			
	$JLMS_DB->setQuery($query);
	$plan = $JLMS_DB->loadObject();
		
	if( is_object($plan) ) {
		$plan->tax = $subscription->tax;
		$plan->tax_type = $subscription->tax_type;		
		JLMS_RECURRENT_PAY::initPricesObjects( $plan );		
	}
		
	//check if there is subscription with account_type==4
	$whereacc .= " AND s.id IN (".$subscr_ids.")";
	$query = "SELECT s.id, s.access_days" 
			."\n FROM #__lms_subscriptions s"
			."\n WHERE s.account_type=4".$whereacc;			
	$JLMS_DB->setQuery($query);
	$subscriptionAccesses = $JLMS_DB->loadObjectList();
	$adddays=0;
	if ($subscriptionAccesses)
	{
		foreach($subscriptionAccesses as $subscr)
		{
			$adddays += $subscr->access_days;
		}
	}
			
	
	//get user_id
	$query = "SELECT user_id FROM #__lms_payments WHERE id=".$item_id;
	$JLMS_DB->setQuery($query);
	$uid = $JLMS_DB->loadResult();
	?>
	<html>	
  	<head>
  		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
  	</head>
	<body onLoad="document.forms.jlms_checkout_form.submit();">
<?php

	$print_item_name = $params->get( 'item_name');
	if ( strpos($print_item_name, '[sub]') !== false || strpos($print_item_name, '[SUB]') !== false) {
		//compare and repalce both uppercase and lowercase strings (stri_repalce not used for PHP4 compat)
		$print_item_name = str_replace('[sub]', $subscription->sub_name, $print_item_name);
		$print_item_name = str_replace('[SUB]', $subscription->sub_name, $print_item_name);
	}
	if (!$print_item_name) {
		$print_item_name = $subscription->sub_name;
	}

	if (!is_object($plan))
	{
	?>
	<form action="<?php echo $params->get( 'server_url' );?>" method="post" name="jlms_checkout_form" id="jlms_checkout_form" />
	<input type="hidden" name="cmd" value="_xclick" />
	<input type="hidden" name="business" value="<?php echo $params->get( 'business_email' );?>" />
	<input type="hidden" name="on0" value="Tax amount" />
	<input type="hidden" name="os0" value="<?php echo $subscription->tax_amount;?>" />
	<input type="hidden" name="item_number" value="<?php echo $item_id;?>" />
	<input type="hidden" name="item_name" value="<?php echo $print_item_name ;?>" />
	<input type="hidden" name="no_shipping" value="1" />
<?php
	if (count($courses) == 1 && $subscription->payment_type == 0) {?>
	<input type="hidden" name="return" value="<?php echo JURI::root();?>index.php?option=<?php echo $option;?>&Itemid=<?php echo $Itemid;?>&task=details_course&id=<?php echo $courses[0];?>" />
<?php } else { ?>

	<input type="hidden" name="return" value="<?php echo JURI::root();?>index.php?option=<?php echo $option;?>&Itemid=<?php echo $Itemid;?>" />
<?php /*
	<input type="hidden" name="return" value="<?php echo $JLMS_CONFIG->get('live_site');?>/index.php?option=com_joomla_lms&task=callback&trs_id=<?php echo $item_id?>&proc=<?php echo $proc->id;?>" />
*/ ?>
<?php } ?>
	<input type="hidden" name="rm" value="2" />
	<input type="hidden" name="cancel_return" value="<?php if ($params->get( 'cancel_url' ) == '') echo sefRelToAbs("index.php?option=com_joomla_lms&Itemid=".$Itemid); else echo $params->get( 'cancel_url' );?>">
	<input type="hidden" name="notify_url" value="<?php echo JURI::root();?>index.php?option=com_joomla_lms&task=callback&trs_id=<?php echo $item_id?>&proc=<?php echo $proc->id;?>" />
	<input type="hidden" name="amount" value="<?php echo number_format($subscription->price + $subscription->tax_amount, 2, '.', '');?>" />
	<input type="hidden" name="currency_code" value="<?php echo $JLMS_CONFIG->get('jlms_cur_code');?>" />
	<input type="hidden" name="receiver_email" value="<?php echo $params->get( 'business_email' )?>" />
	<input type="hidden" name="charset" value="utf-8" />
	</form>
<?php
	}
	else
	{		
					
		if( $subscription->recurrent_obj ) 
			$plan = $subscription->recurrent_obj;		
	?>
	<form action="<?php echo $params->get('server_url');?>" method="post" name="jlms_checkout_form" id="jlms_checkout_form">
	<input type="hidden" name="cmd" value="_xclick-subscriptions" />
	<input type="hidden" name="business" value="<?php echo $params->get('business_email');?>" />
	<input type="hidden" name="item_name" value="<?php echo $print_item_name ;?>" />
	<input type="hidden" name="item_number" value="<?php echo $item_id;?>" />	
	<input type="hidden" name="no_shipping" value="1" />	
	<input type="hidden" name="rm" value="2" />
	<input type="hidden" name="no_note" value="1" />	
	<input type="hidden" name="currency_code" value="<?php echo $JLMS_CONFIG->get('jlms_cur_code');?>" />	
	<?php
	if (count($courses) == 1 && $subscription->payment_type == 0) {?>
	<input type="hidden" name="return" value="<?php echo JURI::root();?>index.php?option=<?php echo $option;?>&Itemid=<?php echo $Itemid;?>&task=details_course&id=<?php echo $courses[0];?>" />
	<?php } else { ?>

	<input type="hidden" name="return" value="<?php echo JURI::root();?>index.php?option=<?php echo $option;?>&Itemid=<?php echo $Itemid;?>" />
	<?php /*
		<input type="hidden" name="return" value="<?php echo $JLMS_CONFIG->get('live_site');?>/index.php?option=com_joomla_lms&task=callback&trs_id=<?php echo $item_id?>&proc=<?php echo $proc->id;?>" />
	*/ ?>
	<?php } ?>
	
	<input type="hidden" name="cancel_return" value="<?php if ($params->get( 'cancel_url' ) == '') echo sefRelToAbs("index.php?option=com_joomla_lms&Itemid=".$Itemid); else echo $params->get( 'cancel_url' );?>" />
	<input type="hidden" name="notify_url" value="<?php echo JURI::root(); ?>index.php?option=com_joomla_lms&task=callback&trs_id=<?php echo $item_id?>&proc=<?php echo $proc->id;?>&subscr=1" />
	<?php
	
	//if is basic then no other parameters should be considered		
	if( $plan->price1->showPrice() || $plan->price1->isPeriodTrial() ) {
		?>
		<input type="hidden" name="a1" value="<?php echo $plan->price1->get('a'); ?>"/>
		<input type="hidden" name="p1" value="<?php echo $plan->price1->get('p'); ?>"/>
		<input type="hidden" name="t1" value="<?php echo $plan->t1;?>"/>
		<?php
	} 
	
	if( $plan->price2->showPrice() ) 
	{		
		?>
		<input type="hidden" name="a2" value="<?php echo $plan->price2->get('a');?>"/>
		<input type="hidden" name="p2" value="<?php echo $plan->price2->get('p');?>"/>
		<input type="hidden" name="t2" value="<?php echo $plan->t2;?>"/>
		<?php
	} 
	?>
		<input type="hidden" name="a3" value="<?php echo $plan->price3->get('a');?>"/>
		<input type="hidden" name="p3" value="<?php echo $plan->price3->get('p');?>"/>
		<input type="hidden" name="t3" value="<?php echo $plan->t3;?>"/>
	<?php		
	if( $plan->src && $plan->price3->get('srt') != 1 )
	{
	?>
		<input type="hidden" name="srt" value="<?php echo $plan->price3->get('srt');?>"/>
		<input type="hidden" name="src" value="<?php echo $plan->src;?>"/>
		<input type="hidden" name="sra" value="<?php echo $plan->sra?>">
	<?php				
	}
	
	?>	
	<input type="hidden" name="charset" value="utf-8" />
	</form>	
	<?php
	}
	?>
	<?php /*<br /><br /><br /><br /><br /><br />
	<center>
	<img class="JLMS_png" src="<?php echo $JLMS_CONFIG->get('live_site') . '/' . $JLMS_CONFIG->get('lms_path_to_images', 'components/com_joomla_lms/lms_images');?>/loading.gif" width="32" height="32" border="0" alt="..." title="..." />
	</center> */ ?>
	</body>
	</html>
	<?php
	die();
}


function show_billing_form($option, $proc, $user){
	//no billing details
}
function validate_callback($proc) {
	global $Itemid, $JLMS_CONFIG, $JLMS_DB;

	$params = new JLMSParameters( $proc->params );

	$req = 'cmd=_notify-validate';
	foreach ($_POST as $key => $value) {
		$value = urlencode(stripslashes($value));
		$req .= "&$key=$value";
	}
	
	// post back to PayPal system to validate
	$server_url2 = str_replace('https://','',str_replace('/cgi-bin/webscr','', $params->get( 'server_url' ) ));
	
	$header = "POST /cgi-bin/webscr HTTP/1.0\r\n";
	$header .= "Host: " . $server_url2 . "\r\n";
	$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
	$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
	$fp = fsockopen ($server_url2, 80, $errno, $errstr, 30);
	//mosMail( 'lms@elearningforce.biz', 'BOT LMS Trial', 'dimiurgs@gmail.com', 'Paypal', $server_url2, false, NULL, NULL );
	// assign posted variables to local variables
	$item_name = $_POST['item_name'];
	$item_number = $_POST['item_number'];
	$payment_status = $_POST['payment_status'];
	$payment_amount = $_POST['mc_gross'];
	$payment_currency = $_POST['mc_currency'];
	$txn_id = $_POST['txn_id'];
	$receiver_email = $_POST['receiver_email'];
	$business = $_POST['business'];
	$payer_email = $_POST['payer_email'];
	$payment_date = $_POST['payment_date'];
	$tax_amount = $_POST['option_selection1'];
	$tax_paypal = isset($_POST['tax'])?$_POST['tax']:0;	
			
	if (!$fp) {
		// nothing here ?!
	} else {		
		fputs ($fp, $header . $req);
		while (!feof($fp)) {
			$res_pp = fgets ($fp, 1024);
			if (strcmp ($res_pp, "VERIFIED") == 0) {		/// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!  VERIFIED  !!!!!!!!!!!!!!!!!11
				require_once(_JOOMLMS_FRONT_HOME.'/includes/joomla_lms.subscription.lib.php');
				if ( $payment_status == 'Completed' ) { 

					// check that receiver_email is your Primary PayPal email
					if ( $receiver_email != $params->get( 'business_email' ) && $business != $params->get( 'business_email' )  ) { die; }

					if ( $payment_currency != $JLMS_CONFIG->get('jlms_cur_code') ) { die; }

					if (!jlms_check_payment_transaction(($payment_amount - $tax_paypal), $item_number)) {die('Invalid payment amount'); }
					$query = "SELECT status FROM `#__lms_payments` WHERE id = $item_number ";
					$JLMS_DB->setQuery($query);
					$prev_payment = $JLMS_DB->LoadResult();
					
					jlms_update_payment( $item_number, $txn_id, 'Completed', $payment_date, ($tax_amount + $tax_paypal), $tax_paypal );
					if ($prev_payment == 'Completed') {
						
					} else {
						jlms_register_new_user($item_number);
					}

				} elseif( $payment_status == 'Pending' ){
					jlms_update_payment( $item_number, $txn_id, 'Pending', $payment_date, $tax_amount, $tax_paypal );
				}
			} elseif (strcmp ($res_pp, "INVALID") == 0) {
				JLMSRedirect (sefRelToAbs("index.php?option=com_joomla_lms&Itemid=$Itemid"));
			}
		}
	fclose ($fp);	
	}	
}

function validate_recurrent_subscription($proc)
{
	global $Itemid, $JLMS_CONFIG, $JLMS_DB;
	
	$params = new JLMSParameters( $proc->params );	
	// post back to PayPal system to validate
	/*
	foreach ($_POST as $key=>$value) $postdata.=$key."=".urlencode($value)."&";	
	$server_url2 = str_replace('https://','',str_replace('/cgi-bin/webscr','', $params->get( 'server_url' ) ));
	$curl = curl_init('https://'.$server_url2.'/cgi-bin/webscr');
	curl_setopt ($curl, CURLOPT_HEADER, 0);
	curl_setopt ($curl, CURLOPT_POST, 1);
	curl_setopt ($curl, CURLOPT_POSTFIELDS, $postdata);
	curl_setopt ($curl, CURLOPT_SSL_VERIFYPEER, 0);
	//curl_setopt ($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt ($curl, CURLOPT_SSL_VERIFYHOST, 1);  
	$response = curl_exec ($curl);  	
	curl_close ($curl);
	*/
	$req = 'cmd=_notify-validate';
	foreach ($_POST as $key => $value) {
		$value = urlencode(stripslashes($value));
		$req .= "&$key=$value";
	}
	
	// post back to PayPal system to validate
	$server_url2 = str_replace('https://','',str_replace('/cgi-bin/webscr','', $params->get( 'server_url' ) ));
	
	$header = "POST /cgi-bin/webscr HTTP/1.0\r\n";
	$header .= "Host: " . $server_url2 . "\r\n";
	$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
	$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
	$fp = fsockopen ($server_url2, 80, $errno, $errstr, 30);
		
	$verifed = false; 
	
	if (!$fp) {
		die();
	} else {		
		fputs ($fp, $header . $req);
		while (!feof($fp)) {
			$res_pp = fgets ($fp, 1024);
						
			if (strcmp ($res_pp, "VERIFIED") == 0) {
				$verifed = true;
			}
		}
		fclose ($fp);
	}
		
	if( !$verifed ) die();	
	
	//if ($response != "VERIFIED") die("You should not do that ..."); 	
	//mosMail( 'lms@elearningforce.biz', 'LMS Trial', 'sergey.cured@gmail.com', 'Paypal', $server_url2, false, NULL, NULL );
	// assign posted variables to local variables
	// assign posted variables to local variables
	$item_name = mosGetParam($_POST, 'item_name', 0);
	$item_number = mosGetParam($_POST, 'item_number','');
	$payment_status = mosGetParam($_POST, 'payment_status', '');		
	$payment_amount = mosGetParam($_POST, 'mc_gross', '');
	$payment_currency = mosGetParam($_POST, 'mc_currency', '');
	$txn_id = mosGetParam($_POST, 'txn_id', '');
	$receiver_email = mosGetParam($_POST, 'receiver_email', '');
	$business = mosGetParam($_POST, 'business', '');
	$payer_email = mosGetParam($_POST, 'payer_email', '');
	$payment_date = mosGetParam($_POST, 'payment_date', '');
	$subscr_date = mosGetParam($_POST, 'subscr_date', '');
	
	$mc_gross = mosGetParam($_POST, 'mc_gross', ''); //amount	
	$mc_fee = mosGetParam($_POST, 'mc_fee', '');

	$txn_type = mosGetParam($_POST, 'txn_type', '');
	$subscr_id = mosGetParam($_POST, 'subscr_id', '');
	$subscr_date = mosGetParam($_POST, 'subscr_date', '');
	$recurring = mosGetParam($_POST, 'recurring', '');
	$recur_times = mosGetParam($_POST, 'recur_times', '');
	$reattempt = mosGetParam($_POST, 'reattempt', '');
	$period1 = mosGetParam($_POST, 'period1', '');
	$period2 = mosGetParam($_POST, 'period2', '');
	$period3 = mosGetParam($_POST, 'period3', '');
	$amount1 = mosGetParam($_POST, 'amount1', '');
	$amount2 = mosGetParam($_POST, 'amount2', '');
	$amount3 = mosGetParam($_POST, 'amount3', '');
	$mc_amount1 = mosGetParam($_POST, 'mc_amount1', '');
	$mc_amount2 = mosGetParam($_POST, 'mc_amount2', '');
	$mc_amount3 = mosGetParam($_POST, 'mc_amount3', '');
	
	$tax_amount = isset($_POST['option_selection1'])?$_POST['option_selection1']:0;	
	$tax_paypal = isset($_POST['tax'])?$_POST['tax']:0;	
			
	if ($JLMS_CONFIG->get('debug_mode', false)) 
	{
		jimport('joomla.error.log');
		$log = & JLog::getInstance('payments.log');
		ob_start();
		var_dump( $_REQUEST );
		$content = ob_get_contents();
		ob_end_clean();
		$entry['COMMENT'] = $content;
		$log->addEntry( $entry );
	}	

	require_once(_JOOMLMS_FRONT_HOME.'/includes/joomla_lms.subscription.lib.php');	
				
	if ( $receiver_email != $params->get( 'business_email' ) && $business != $params->get( 'business_email' )  ) { die; }	
	if ( $payment_currency != $JLMS_CONFIG->get('jlms_cur_code') ) { die; }					
	// new subscription	
	
	if ( $txn_type == 'subscr_signup' ) 
	{				
		$query = "SELECT * FROM #__lms_payments_checksum WHERE payment_id = ".$item_number;	
		$JLMS_DB->setQuery( $query );
		$checksum = $JLMS_DB->loadObject();		
				
		if( (!$checksum->a1 && $checksum->p1) || (!$checksum->a2 && $checksum->p2) ) 
		{			
			$query = "SELECT * FROM `#__lms_payments` WHERE id = $item_number";
			$JLMS_DB->setQuery($query);
			$payment_info = $JLMS_DB->loadObject();
			
			$subscr_date_obj = JFactory::getDate($subscr_date);
			$subscr_date_mysql = $subscr_date_obj->toMySQL();
					
			jlms_update_payment( $item_number, $txn_id, 'Completed', $subscr_date_mysql, ($tax_amount + $tax_paypal), $tax_paypal, $isReccuring = true );
			jlms_register_new_user( $item_number );		
			
			if( $checksum->a2 ) {				 
				$next_amount = $checksum->a2; 
			} else if ( $checksum->a3 ) {				
				$next_amount = $checksum->a3;	
			}			
			
			if( $next_amount ) 
			{
				$parent_id = ( $payment_info->parent_id )?$payment_info->parent_id:$payment_info->id;			
																	
				$query = "INSERT INTO `#__lms_payments` SET txn_id = '', status = 'Pending', tax_amount = '$tax_amount', tax2_amount = '$tax2_amount', date ='".JLMS_gmdate()."', parent_id = '$parent_id', amount = '$next_amount', cur_code = '$payment_info->cur_code', user_id = $payment_info->user_id, payment_type = '$payment_info->payment_type', sub_id = '$payment_info->sub_id', proc_id = '$payment_info->proc_id', processor = '$payment_info->processor'";
				
				$JLMS_DB->setQuery($query);
				$JLMS_DB->query();				
			}
		}	
				
		//get plan_id
	
		/*$query = "SELECT status FROM `#__lms_payments` WHERE id = $item_number ";
		$JLMS_DB->setQuery($query);
		$prev_payment = $JLMS_DB->LoadResult();
		*/		
		//jlms_update_payment( $item_number, $txn_id, 'Completed', $payment_date, $tax_amount, 0, $isReccuring = false );
		//jlms_register_new_user( $item_number );
	} else if ( $txn_type == 'subscr_payment' ) {
		//mosMail( 'lms@elearningforce.biz', 'LMS Trial', 'sergey.cured@gmail.com', 'Paypal', $payment_status, false, NULL, NULL );
		//if ( $payment_status != 'Completed' ) { die; }	
		if( $txn_id ) 
		{
			$query = "SELECT count(1) FROM `#__lms_payments` WHERE txn_id = ".$JLMS_DB->quote( $txn_id );
			$JLMS_DB->setQuery( $query );
			$txt_id_exists = $JLMS_DB->loadResult();
			
			if( $txt_id_exists ) die();
		}		
				
		$query = "SELECT p.id, p.name, p.description, p.published, p.p1, p.t1, p.p2, p.t2, p.p3, p.t3, s.a1, s.a2, s.a3, p.sra, p.src, p.srt, p.params"
				."\n FROM #__lms_payment_items pi,"
				."\n #__lms_subscriptions s,"
				."\n #__lms_plans_subscriptions ps,"
				."\n #__lms_plans p"
				."\n WHERE pi.payment_id=".$item_number
				."\n AND pi.item_id=s.id"
				."\n AND ps.subscr_id=s.id"
				."\n AND p.id=ps.plan_id";
		$JLMS_DB->setQuery($query);
		$plan = $JLMS_DB->loadObject();		
	
		if( empty($plan) ) { die(); }// checking if subscription data exists	
		
		$query = "SELECT * FROM #__lms_payments_checksum WHERE payment_id = ".$item_number;	
		$JLMS_DB->setQuery( $query );
		$checksum = $JLMS_DB->loadObject();
			
		if( empty( $checksum ) ) { die(); }		
						
		$query = "SELECT * FROM `#__lms_payments` WHERE (id = $item_number OR parent_id = $item_number) AND status != 'Completed' AND amount = '".($mc_gross - $tax_paypal)."' ORDER BY id DESC LIMIT 1";
		$JLMS_DB->setQuery($query);
		$payment_info = $JLMS_DB->loadObject();		
				
		if( empty( $payment_info ) ) { die(); }
		
		jlms_update_payment( ( $payment_info->id ), $txn_id, $payment_status, $payment_date, ($tax_amount + $tax_paypal), $tax_paypal, $isReccuring = true );
				
		if( $payment_status ==  'Completed' && $checksum->a3 ) 
		{				
			$next_amount = 0;	
			if( !$payment_info->parent_id && $checksum->a2 ) {				 
				$next_amount = $checksum->a2; 
			} else {				
				$next_amount = $checksum->a3;	
			}	
						
			if( $next_amount ) 
			{
				$parent_id = ( $payment_info->parent_id )?$payment_info->parent_id:$payment_info->id;
																	
				$query = "INSERT INTO `#__lms_payments` SET txn_id = '', status = 'Pending', tax_amount = '$tax_amount', tax2_amount = '$tax2_amount', date ='".JLMS_gmdate()."', parent_id = '$parent_id', amount = '$next_amount', cur_code = '$payment_info->cur_code', user_id = $payment_info->user_id, payment_type = '$payment_info->payment_type', sub_id = '$payment_info->sub_id', proc_id = '$payment_info->proc_id', processor = '$payment_info->processor'";
				
				$JLMS_DB->setQuery($query);
				$JLMS_DB->query();				
			}			
			
			jlms_register_new_user( $item_number );
		}
	
					
		$user_id = $payment_info->user_id;					
									
	} else if ( $txn_type == 'subscr_cancel' || $txn_type == 'subscr_eot' ) 
	{			
		if( $txn_type == 'subscr_cancel' ) 
		{
			$query = "SELECT *  FROM `#__lms_payments` WHERE (id = $item_number OR parent_id = $item_number) AND status != 'Completed' ORDER BY id DESC LIMIT 1";
			$JLMS_DB->setQuery($query);
			$payment_info = $JLMS_DB->loadObject();			
			
			if( is_object( $payment_info ) ) 
			{
				$query = "UPDATE `#__lms_payments` SET status = 'Canceled' WHERE id = ".$JLMS_DB->quote($payment_info->id);
				$JLMS_DB->setQuery($query);
				$JLMS_DB->Query();			
			}			
		} 			
										
		jlms_register_new_user($item_number, 1);		
	}
	
	if( $payment_info->id )
		return $payment_info->id;
	else
		return $item_number;
		//}	
	//fclose ($fp);
	//}		
}
//end
}
?>