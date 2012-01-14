<?php
defined( '_JLMS_EXEC' ) or die( 'Restricted access' );
class JLMS_2checkout{
function show_checkout($option, $subscription, $item_id, $proc ) {
	global $Itemid, $my, $JLMS_CONFIG;

	$params = new JLMSParameters( $proc->params );

	if (!$params->get( 'x_login' )) {
		$redirect_task = 'subscription';
		if ($subscription->payment_type == 2) {
			$redirect_task = 'show_cart';
		}
		JLMSredirect(sefRelToAbs("index.php?option=".$option."&Itemid=".$Itemid."&task=$redirect_task"), "This payment method is not available at the moment.<br /> Ask site administrator to check payment method settings.");
	}
	setcookie('joomlalms_cart_contents', '', time()-3600, '/');
	// generating sites string;
	$item_name = $params->get( 'item_name' );
	?>
	<html>
	<body onLoad="document.forms.ms_checkout_form.submit();">
	<form action="https://www2.2checkout.com/2co/buyer/purchase" method="post" name="ms_checkout_form" id="ms_checkout_form">
	<input type="hidden" name="x_login" value="<?php echo $params->get( 'x_login' );?>" />
	<input type="hidden" name="x_amount" value="<?php echo number_format($subscription->price + $subscription->tax_amount, 2, '.', '');?>" />
	<input type="hidden" name="x_invoice_num" value="<?php echo $item_id;?>"	/>
	<input type="hidden" name="x_receipt_link_url" value="<?php echo sefRelToAbs('index.php?option=com_joomla_lms&task=callback&proc='.$proc->id);?>"/> 
	<input type="hidden" name="x_email" value="<?php echo $my->email;?>" />
	<?php if($params->get('demo')) {?>
	<input type="hidden" name="demo" value="Y" />
	<?php }?>
	<input type="hidden" name="x_email_merchant" value="<?php echo $params->get( 'x_email_merchant' );?>" />
	<input type="hidden" name="fixed" value="Y"/>
	<input type="hidden" name="lang" value="<?php echo $params->get( 'lang' );?>" />
	<input type="hidden" name="return_url" value="<?php if ($params->get( 'return_url' ) == '') echo sefRelToAbs($JLMS_CONFIG->get('live_site')."/index.php?option=".$option."&Itemid=".$Itemid); else echo $params->get( 'return_url' );?>" />	
	<input type="hidden" name="pay_method" value="<?php echo $params->get( 'pay_method' );?>" />	 
	<input type="hidden" name="custom" value="<?php echo $item_id;?>" />
	<input type="hidden" name="userid" value="<?php echo $my->id; ?>" />	
	<input type="hidden" name="tax_amount" value="<?php echo $subscription->tax_amount;?>" />	
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
	global $Itemid, $JLMS_CONFIG, $JLMS_DB;
	
	$params = new JLMSParameters( $proc->params );
	
	if( !isset( $_REQUEST["x_invoice_num"] ) || empty( $_REQUEST["x_invoice_num"] )) echo "Order ID is not set or empty!";
	else {

		if ( $_REQUEST['x_Login'] != $params->get( 'x_login', '' )) die('Invalid account number.');

		// demo transaction
		if ( isset($_REQUEST['demo']) && $_REQUEST['demo'] == "Y" && !$params->get( 'demo', '' )) {
			die('Demo mode is switched off.');
		}
		
		$order_number = mosGetParam( $_REQUEST, "x_invoice_num" ); 
		$compare_string = $params->get('x_secret') . $params->get('x_login') . $_REQUEST['order_number'] . $_REQUEST['x_amount'];
		$payment_date = date("Y-m-d H:i:s", mktime());
		$compare_hash1 = strtoupper(md5($compare_string));
		$compare_hash2 = $_REQUEST['x_MD5_Hash'];
		if ($compare_hash1 != $compare_hash2 && !$params->get('demo')) die('Invalid secret hash.');
		
		if ($_REQUEST['x_response_code'] == '1' && $_REQUEST['x_2checked'] == 'Y') {
			$payment_amount = $_REQUEST['x_amount'];
			$tax_amount = $_REQUEST['tax_amount'];
			$txn_id = $_REQUEST['x_trans_id'];
			if ($params->get( 'demo')) $txn_id .= ' - demo mode';
			$payment_date = date("Y-m-d H:i:s");
			$order_id = $_REQUEST['custom'];
			$user_id = $_REQUEST['userid'];
			$payment_currency = $JLMS_CONFIG->get('jlms_cur_code');
			
			require_once(_JOOMLMS_FRONT_HOME.'/includes/joomla_lms.subscription.lib.php');
			if (!jlms_check_payment_transaction(($payment_amount - $tax_amount), $order_id)) { die('Invalid payment amount'); }
			$query = "SELECT status FROM `#__lms_payments` WHERE id = $order_id ";
			$JLMS_DB->setQuery($query);
			$prev_payment = $JLMS_DB->LoadResult();
			jlms_update_payment( $order_id, $txn_id, 'Completed', $payment_date );
			if ($prev_payment == 'Completed') {
			} else {
				jlms_register_new_user( $order_id );
			}
		}
		
		if ($params->get('return_url') == '') JLMSRedirect($JLMS_CONFIG->get('live_site'));
		else JLMSRedirect($params->get('return_url'));
	}
}
}//end of class
?>