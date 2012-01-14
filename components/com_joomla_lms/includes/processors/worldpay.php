<?php
defined( '_JLMS_EXEC' ) or die( 'Restricted access' );
class JLMS_worldpay{
function show_checkout( $option, $subscription, $item_id, $proc ) {
	global $Itemid, $JLMS_CONFIG, $my;
	$params = new JLMSParameters( $proc->params );

	if (!$params->get( 'inst_id' )) {
		$redirect_task = 'subscription';
		if ($subscription->payment_type == 2) {
			$redirect_task = 'show_cart';
		}
		JLMSredirect(sefRelToAbs("index.php?option=".$option."&Itemid=".$Itemid."&task=$redirect_task"), "This payment method is not available at the moment.<br /> Ask site administrator to check payment method settings.");
	}
	setcookie('joomlalms_cart_contents', '', time()-3600, '/');

	// generating sites string;
	$prod_descr = $params->get( 'prod_descr' );
	
	?>
	<html>
	<body onLoad="document.forms.ms_checkout_form.submit();">
	<form action="https://select.worldpay.com/wcc/purchase" method="post" name="ms_checkout_form" id="ms_checkout_form">
	<input type="hidden" name="instId" value="<?php echo $params->get( 'inst_id' );?>" />
	<input type="hidden" name="cartId" value="<?php echo $item_id;?>" />
	<input type="hidden" name="amount" value="<?php echo number_format($subscription->price + $subscription->tax_amount, 2, '.', '');?>" />
	<input type="hidden" name="currency" value="<?php echo $JLMS_CONFIG->get('jlms_cur_code')?>" />
	<input type="hidden" name="desc" value="<?php echo $subscription->name;?>" />
	<input type="hidden" name="testMode" value="<?php echo $params->get( 'test_mode' );?>" />
	<input type="hidden" name="MC_order" value="<?php echo $item_id;?>" />
	<input type="hidden" name="MC_tax" value="<?php echo $subscription->tax_amount;?>" />
	<input type="hidden" name="MC_user" value="<?php echo $my->id;?>" />
	</form> 
	</body>
	</html>
	<?php
	die();
}

function show_billing_form($option, $proc, $user){
	//
	
}
function validate_callback($proc) {
	global $JLMS_DB, $Itemid, $JLMS_CONFIG, $JLMS_DB;
	$params = new JLMSParameters( $proc->params );
	
	// assign posted variables to local variables
	$payment_status = $_REQUEST['transStatus'];
	$payment_amount = $_REQUEST['authAmount'];
	$payment_currency = $_REQUEST['authCurrency'];
	$txn_id = $_REQUEST['transId'];
	
	$payment_date = date("Y-m-d H:i:s", mktime());
	$order = $_REQUEST['MC_order'];
	$tax_amount = $_REQUEST['MC_tax'];
	$user_id = $_REQUEST['MC_user'];
	$callback_pw = @$_REQUEST['callbackPW'];

	if ($payment_status != 'Y') die('Invalid transaction status');
	if ($callback_pw != $params->get( 'callback_pw' )) die('Invalid callback password');

	// check that txn_id has not been previously processed
	$query = "SELECT id FROM `#__lms_payments` WHERE txn_id='".$txn_id."'  " ;
	$JLMS_DB->setQuery( $query );
	$res = $JLMS_DB->query();
	if (mysql_num_rows($res)) { die; }
		
	// check that payment_currency is correct
	if ( $payment_currency != $JLMS_CONFIG->get('jlms_cur_code') ) { die; };
	
	require_once(_JOOMLMS_FRONT_HOME.'/includes/joomla_lms.subscription.lib.php');
	
	if ($payment_status == 'Y'){
		if (!jlms_check_payment_transaction(($payment_amount - $tax_amount), $order)) { die('Invalid payment amount'); }
		$query = "SELECT status FROM `#__lms_payments` WHERE id = $order ";
		$JLMS_DB->setQuery($query);
		$prev_payment = $JLMS_DB->LoadResult();
		jlms_update_payment( $order, $txn_id, 'Completed', $payment_date );
		if ($prev_payment == 'Completed') {
		} else {
			jlms_register_new_user($order);
		} ?>
		Thanks for your payment, you were successfully added to the course.
<?php
	}elseif($payment_status == 'C'){
		jlms_update_payment( $order, $txn_id, 'Pending', $payment_date );
?>
		Your payment status is still 'pending'. You will be added to the course as soon as your payment is confirmed.
<?php
	}
}
}//end of class
?>