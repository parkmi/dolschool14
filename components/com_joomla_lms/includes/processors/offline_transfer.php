<?php
defined( '_JLMS_EXEC' ) or die( 'Restricted access' );

class JLMS_offline_transfer{

	function show_checkout( $option, $subscription, $item_id, $proc ) {
		global $Itemid, $JLMS_CONFIG, $JLMS_DB;
		$task2 = strval( mosGetParam( $_REQUEST, 'task2', '' ) );
		if ($task2 == 'show_info') {
			// do nothing... proceed to the code below
		} else {
			JLMSRedirect(sefRelToAbs("index.php?option=".$option."&amp;Itemid=".$Itemid."&amp;task=checkout_cart&amp;task2=show_info&amp;order_id=$item_id&amp;proc=$proc->id&amp;proc_id=$proc->id"));
			exit();
		}
		$params = new JLMSParameters( $proc->params );
		$html =	$params->get('page_info', 'Your OrderID: {ORDER_ID}, Total amount: {TOTAL}. Invoice: {invoice}.'); // do not use language files for this setting, this is default value from processor XML
		$rowz = new jlms_adm_config();
		$rowz->loadFromDb( $JLMS_DB );
		$replace_it = '{invoice}';
		$replace_to = 'link';
		$inv_close_pos = strpos($html, '{/invoice}');
		if ($inv_close_pos === false) {
		} else {
			$inv_open_pos = strpos($html, '{invoice}');
			if ($inv_open_pos === false) {
			} elseif ($inv_open_pos < $inv_close_pos) {
				$replace_it = substr($html,$inv_open_pos,($inv_close_pos - $inv_open_pos) + 10);
				$replace_to = substr($html,$inv_open_pos + 9,($inv_close_pos - $inv_open_pos) - 9);
			}
		}
		if($params->get('subscr_status') == 1 && $rowz->jlms_subscr_status_email)
		{
			$query = "SELECT lms_config_value FROM #__lms_config  WHERE lms_config_var = 'jlms_subscr_invoice_path'";
			$JLMS_DB->setQuery( $query );
			$file_path = $JLMS_DB->loadResult();
			$query = "SELECT filename FROM #__lms_subs_invoice WHERE subid = ". (int) $item_id;
			$JLMS_DB->setQuery( $query );
			if($JLMS_DB->loadResult()) {
				$filename = $file_path.'/'.$JLMS_DB->loadResult();
			} else { 
				$filename = '';
			}
			if($filename && is_file($filename))	
			{
				$filename = '<a href="'.JRoute::_('index.php?option=com_joomla_lms&Itemid='.$Itemid.'&task=get_payment_invoice&id='.$item_id).'" target="_blank">'.$replace_to.'</a>';
				$html = str_replace($replace_it, $filename,$html);
			}
			else 
			{
				$html = str_replace($replace_it, '-',$html);
			}
		}
		else 
			{
				$html = str_replace($replace_it, '-',$html);
			}
		$html = str_replace('{ORDER_ID}', $item_id,$html);
		$total = number_format($subscription->price + $subscription->tax_amount, 2, '.', '').''.$JLMS_CONFIG->get('jlms_cur_code');
		$html = str_replace('{TOTAL}', $total, $html);
		?>
			<?php echo $subscription->sub_name ? JLMSCSS::h2($subscription->sub_name) : '';
				echo $html;
			?><br /><br />
			<a href='<?php echo sefRelToAbs("index.php?option=com_joomla_lms&Itemid=$Itemid&task=courses");?>'><?php echo _JLMS_HOME_COURSES_LIST_HREF;?></a>
		<?php
	}//end of show checkout function
	
	function show_billing_form($option, $proc, $user){
		global $Itemid, $JLMS_CONFIG;
		$params2 = new JLMSParameters( $proc->params );
		if ( $params2->get( 'description', 'Use this processor if you pay fees by bank account' ) ){ // do not use language files here, this is default value for 'description' parameter from processor XML
		?>
		<br />
		<table cellpadding="0" cellspacing="2" width="100%" border="0" class="jlms_table_no_borders">		
		<tr>
			<td align="left" valign="top" width="50%">
				<?php echo JLMSCSS::h2(_JLMS_DESCRIPTION);
				echo $params2->get( 'description', 'Use this processor if you pay fees by bank account' );
				?>
			</td>
		</tr>
		</table>
		<br />
		<?php
		}
	}//end of billng form function
	
	function validate_callback($proc) {
		
	}//end of validate function
}//end of class
?>