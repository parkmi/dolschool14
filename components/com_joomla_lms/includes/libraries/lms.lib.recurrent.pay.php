<?php
/**
* lms.lib.recurrent.pay.php
* Joomla LMS Component
* * * ElearningForce DK
**/

// no direct access
defined( '_JLMS_EXEC' ) or die( 'Restricted access' );

class JLMSReccPrice extends JObject 
{
	var $base_a;
	
	var $a;
	
	var $p;
	
	var $tax;
	
	var $taxType;
	
	var $taxAmount = 0;
	
	var $pDisc;
	
	var $amountDisc = 0;
	
	var $srt;
	
	var $recPath = '';
	
	var $recPWithBalance = false;
	
	function JLMSReccPrice( $a = 0, $p = 0, $tax = 0, $pDisc = 0, $srt = 0, $taxType = 0 ) 
	{				
		$this->base_a = $a;
		$this->a = $a;
		$this->p = $p;
		$this->pDisc = $pDisc;
		$this->tax = $tax;
		$this->srt = $srt;
		$this->taxType = $taxType;
	}
	
	//recalculate discount
	function recDiscount() 
	{
		$this->amountDisc = round( $this->a*$this->pDisc/100, 2);
	}
	
	function recTax() 
	{
		if ($this->taxType == 1) 
		{
			$this->taxAmount = round( $this->a / 100 * $this->tax, 2);			
		}
	}
	
	function recPriceWithTax() 
	{	
		if( $this->taxAmount ) 
		{
			$this->recPath .= '<font color="red">+'.$this->taxAmount.'</font>';
			
			$this->a += $this->taxAmount;
			
			if( $this->a < 0) $this->a = 0;			
		}
	}

	
	function recPriceWithDisc() 
	{
		if( $this->amountDisc ) 
		{
			$this->recPath .= '<font color="green">-'.$this->amountDisc.'</font>';
			
			$this->a -= $this->amountDisc;
			
			if( $this->a < 0) $this->a = 0;			
		}
	}
	
	function recPriceWithBalance( & $balance, $priceObj1 = null, $priceObj2 = null ) 
	{			
		if( !$balance ) return false;
		
		$this->recPWithBalance = true;			
		
		if( $priceObj2 )
		{											
			$fraction = fmod($balance, $this->a);
			$free_times_count = ($balance - $fraction)/$this->a;
																																								
			if( $free_times_count < $this->srt || $this->srt == 0 ) 
			{
				if( $this->srt )								
					$this->srt -= $free_times_count;
					
				$priceObj1->incSet('p', $free_times_count*$this->p );
											
				if( $fraction ) 
				{												
					if( $this->srt == 0 ) 
					{
						$this->set('a', $this->get('a') - $fraction );							
						$this->set('srt', 1 );
					} else {
						if( $priceObj1->isPeriodTrial() ) 
						{
							$priceObj2->set('a', $this->get('a') - $fraction );
							$priceObj2->set('p', $this->get('p') );
						} else {
							$priceObj1->set('a', $this->get('a') - $fraction );
							$priceObj1->set('p', $this->get('p') );
						}
					}													 
				}						 	  
			 } else {			 	
			 	$priceObj1->incSet('p', $this->srt*$this->p );
				 			 						 	
			 	$this->a = 0;											
				$this->srt = 1;																							
			}														
		} else {			 							
			if( $balance >= $this->a ) 
			{						
				$balance = $balance - $this->a;															
				$priceObj1->incSet('p', $this->p );
				
				$this->a = 0;
				$this->p = 0;															
			} else {				
				$this->a -= $balance;				
				$balance = 0;														 
			}	
		}				
	}
	
	function showPrice() 
	{		
		return ( $this->a > 0 && $this->p  )?true:false;
	}
	
	//increase field value 
	function incSet( $field, $value ) 
	{
		$this->{$field} += $value;
	}
	
	//function uses only for price1 
	function isPeriodTrial() 
	{		
		return ( $this->p && $this->a == 0  )?true:false;
	}
	
	function getCalcPath() 
	{
		if( $this->recPath && !$this->recPWithBalance && (int)$this->base_a ) 
		{			
			$res = '('.number_format( $this->base_a, 2).$this->recPath.') = '.number_format($this->a, 2); 
		} else {
			$res = number_format($this->a, 2);
		}
		
		return $res;
	}	
}

class JLMS_RECURRENT_PAY {
	function getObjByReccSubsId( $id ) 
	{
		global $JLMS_DB;
		
		$query = "SELECT p.id, p.name, p.description, p.published, p.p1, p.t1, p.p2, p.t2, p.p3, p.t3, s.a1, s.a2, s.a3, p.src, p.srt, p.sra, p.params" 
					."\n FROM #__lms_plans p, #__lms_plans_subscriptions ps, #__lms_subscriptions s"
					."\n WHERE s.id=ps.subscr_id AND ps.plan_id=p.id AND ps.subscr_id=".$id;
		$JLMS_DB->setQuery($query);
		$obj = $JLMS_DB->loadObject();
		if( is_object( $obj ) ) 
		{
			return $obj;	
		}			
		
		return false;
	}
	
	function getFirstDayPrice( $subscription, $tax = 0, $taxType = 1 ) 
	{
		global $JLMS_DB;
		
		return ($subscription->p1 > 0 )?$subscription->a1:0;
	}	
	
	function isFirstPeriodTrial( & $obj ) 
	{		
		return ( $obj->p1 && $obj->a1 == 0  )?true:false;
	}
	
	function recalcSubsParams( & $obj, $showType, $balance = 0, $amount = 0 ) 
	{			
		if( ($balance != 0 || $amount != 0) && $showType == 'total' )		
			$obj->price1->set('a', 0);		
						
		$balance = ($balance < 0)?0:$balance;
			
		if( $amount < 0 )
			$amount = 0;
						
		$obj->price2->recDiscount();
		$obj->price3->recDiscount();		
		
		$obj->price2->recPriceWithDisc();
		$obj->price3->recPriceWithDisc();
													
		if( $balance != 0 && $showType == 'total' ) 
		{				
			if ( $obj->price2->showPrice() ) 
			{														
				$obj->price2->recPriceWithBalance( $balance, $obj->price1 );																			
			}		
								
			$obj->price3->recPriceWithBalance( $balance, $obj->price1, $obj->price2 );				
		}
			
		$obj->price2->recTax();
		$obj->price3->recTax();
					
		$obj->price2->recPriceWithTax();
		$obj->price3->recPriceWithTax();
								
		if( $amount ) 
		{
			if( $showType == 'total' ) 
			{
				$obj->price1->set('a', $amount);
			
				if( !$obj->price1->showPrice() && !$obj->price1->isPeriodTrial() ) 
				{		
					$obj->price1->set('p', 1);									
				}
			}
		}		
	} 
	
	function getPriceDesc( & $subscription, $showType = 'default', $balance = 0, $amount = 0 ) 
	{					
		global $JLMS_CONFIG;	
									
		if( !isset( $subscription->p1 ) ) 
		{			
			$obj = JLMS_RECURRENT_PAY::getObjByReccSubsId( $subscription->id );
			foreach( get_object_vars( $obj ) AS $key => $value ) 
			{
				$subscription->{$key} = $value; 
			}			
		}
		/* else {
			$obj = php4_clone( $subscription );	
		}	
		*/		
		
		
		JLMS_RECURRENT_PAY::initPricesObjects( $subscription );
		JLMS_RECURRENT_PAY::recalcSubsParams( $subscription, $showType, $balance, $amount );	
		
								
		$marks = array(				
			'{a1}' => $subscription->price1->getCalcPath(),
			'{a2}' => $subscription->price2->getCalcPath(),
			'{a3}' => $subscription->price3->getCalcPath(),
			'{p1}' => $subscription->price1->get('p'),
			'{p2}' => $subscription->price2->get('p'),
			'{p3}' => $subscription->price3->get('p')																						
		);			
		
		$marks['{free}'] = "";
		$marks['{then}'] = "";
		$marks['{next}'] = "";		
		$marks['{srt}'] = $subscription->price3->get('srt');
			
		$part1=$part2=$part3=$part4='';	
				
		$marks['{cur}'] = $JLMS_CONFIG->get('jlms_cur_code');		
		 							
		if( $subscription->price1->showPrice() || $subscription->price1->isPeriodTrial() ) {				
			if( $subscription->price1->isPeriodTrial() ) 
			{					
				$marks['{free}'] = _JLMS_RECURRENT_PAYMENT_FREE;
				$marks['{a1}'] = "";
				$marks['{cur}'] = "";
			}		
			
			if( $subscription->price1->get('p') == 1 ) {
				$part1 = strtr(_JLMS_RECURRENT_PAYMENT_FIRST_DAY,$marks);
			} else {
				$part1 = strtr(_JLMS_RECURRENT_PAYMENT_FIRST_DAYS,$marks);
			}
			$part1 .= "<br />";
		}		 		
		
		
		$marks['{cur}'] = $JLMS_CONFIG->get('jlms_cur_code');	
		$marks['{free}'] = "";
		
		if( $subscription->price2->showPrice() ) {														
			if( $subscription->price1->showPrice() || $subscription->price1->isPeriodTrial() ) 
			{				
				$marks['{then}'] = _JLMS_RECURRENT_PAYMENT_THEN;
				$marks['{next}'] = _JLMS_RECURRENT_PAYMENT_THE_NEXT;	
			}			
			
			if( $subscription->price2->get('p') == 1 ) {
				$part2 = strtr(_JLMS_RECURRENT_PAYMENT_NEXT_DAY, $marks);
			} else {
				$part2 = strtr(_JLMS_RECURRENT_PAYMENT_NEXT_DAYS, $marks);
			}			
			
			$part2 .= "<br />";
		} 
		
		$marks['{cur}'] = $JLMS_CONFIG->get('jlms_cur_code');
			
		if( $subscription->src && $subscription->price3->get('srt') > 0 ) 
		{		
				if( $subscription->price1->showPrice() || $subscription->price2->showPrice() ) 
				{
					$marks['{then}'] = _JLMS_RECURRENT_PAYMENT_THEN;					
				} else {
					$marks['{then}'] = '';				
				}
				
				if( $subscription->price3->get('a') ) 
				{
					if( $subscription->price3->get('p') == 1 ) {
						$part4 = strtr(_JLMS_RECURRENT_PAYMENT_INSTALLMENTS,$marks);
					} else {
						$part4 = strtr(_JLMS_RECURRENT_PAYMENT_INSTALLMENTS_DAYS,$marks);
					}
					$part4 .= "<br />";
				}			
													
		} else if( $subscription->src ) {				
				if( $subscription->price1->showPrice() || $subscription->price2->showPrice() ) 
				{
					$marks['{then}'] = _JLMS_RECURRENT_PAYMENT_THEN;					
				} else {
					$marks['{then}'] = '';				
				}
								
				if( $subscription->price3->get('a') ) 
				{
					if( $subscription->price3->get('p') == 1 ) {
						$part4 = strtr(_JLMS_RECURRENT_PAYMENT_FOREACH,$marks);
					} else {						
						$part4 = strtr(_JLMS_RECURRENT_PAYMENT_FOREACH_DAYS,$marks);						
					}
					$part4 .= "<br />";
				}						
		} else {			
			if( $subscription->price1->showPrice() || $subscription->price2->showPrice() ) 
			{				
				$marks['{then}'] = _JLMS_RECURRENT_PAYMENT_THEN;					
			} else {
				$marks['{then}'] = '';				
			}
			
			if( $subscription->price3->get('a') ) 
			{
				if( $subscription->price3->get('p') == 1 ) {
					$part3 = strtr(_JLMS_RECURRENT_PAYMENT_ONE_DAY,$marks);
				} else {
					$part3 = strtr(_JLMS_RECURRENT_PAYMENT_MORE_DAYS,$marks);
				}
				$part3 .= "<br />";
			}
			
		}				
		
		$res= $part1.$part2.$part3.$part4;		
						
		return $res; 
	}	
	
	function initPricesObjects( & $subscription ) 
	{		
		$p_disc = 0;
		if( isset($subscription->p_coupon_disc) && $subscription->p_coupon_disc )
			$p_disc = $subscription->p_coupon_disc;
			
		if( isset($subscription->p_disc) && $subscription->p_disc )
			$p_disc += $subscription->p_disc;
		
		if( !isset($subscription->tax) )	
			$tax = 0;
		else	
			$tax =$subscription->tax;
		
		if( !isset($subscription->tax_type) )	
			$tax_type = 0;
		else	
			$tax_type = $subscription->tax_type;	
			 
		$subscription->price1 = new JLMSReccPrice( $subscription->a1, $subscription->p1, $tax, $p_disc, 0, $tax_type  );
		$subscription->price2 = new JLMSReccPrice( $subscription->a2, $subscription->p2, $tax, $p_disc, 0, $tax_type );
		$subscription->price3 = new JLMSReccPrice( $subscription->a3, $subscription->p3, $tax, $p_disc, $subscription->srt, $tax_type  );
	}
	
	function getAmountFromReccurentPrice( & $obj ) 
	{	
		if( !$obj->price1->isPeriodTrial() )	
			return $obj->price3->get('a');
		else
			return 0;		
	}  
}
?>