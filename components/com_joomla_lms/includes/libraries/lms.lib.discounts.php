<?php
/**
* lms.lib.recurrent.pay.php
* Joomla LMS Component
* * * ElearningForce DK
**/

// no direct access
defined( '_JLMS_EXEC' ) or die( 'Restricted access' );

class JLMS_DISCOUNTS {
	function getTotalDiscounts( $subscriptions_id ) 
	{
		global $JLMS_DB, $my;
					
		if( !isset($subscriptions_id[0]) ) return false;
			
		$query = "SELECT * FROM #__lms_discounts 
						WHERE enabled = 1 AND discount_type = 1
						AND	( 
								( start_date = '0000-00-00' AND end_date = '0000-00-00' ) OR
								( start_date < CURDATE() AND end_date = '0000-00-00' ) OR
								( start_date = '0000-00-00' AND end_date > CURDATE() ) OR
								( CURDATE() = start_date AND CURDATE() = end_date ) OR
								( CURDATE() BETWEEN start_date AND end_date ) 
							)
						";				
		$JLMS_DB->setQuery($query);
		$discounts = $JLMS_DB->loadObjectList();
		
		$groups_id = JLMS_DISCOUNTS::getUserGroups();
				
		$res = 0;
		for( $i=0; $i<count($discounts); $i++ ) 
		{
			$discount = $discounts[$i];					
			$step3 = true;	
			
			$step1 = ( $discount->subscriptions )?false:true;
			for( $j=0; $j<count($subscriptions_id); $j++ ) 
			{	
				if( is_object($subscriptions_id[0]) )
					$sub_id = $subscriptions_id[$j]->id;
				else				
					$sub_id = $subscriptions_id[$j];
					
				if( $discount->subscriptions && in_array( $sub_id, explode(',', $discount->subscriptions ) ) )
					$step1 = true;
			}
			
			$step2 = ( $discount->usergroups )?false:true;
			for( $j=0; $j<count( $groups_id ); $j++ ) 
			{								
				$group_id = $groups_id[$j];					
				if( $discount->usergroups && in_array( $group_id, explode(',', $discount->usergroups ) ) )
					$step2 = true;
			}						
						
			if( $discount->users && !in_array( $my->id, explode(',', $discount->users ) ) )
				$step3 = false;
				
			if( $step1 && $step2 && $step3 )
				$res += $discount->value;			
		}			 					
				
		return $res;
	}
	
	function getPercentDiscounts( $subscription_id ) 
	{	
		global $JLMS_DB, $my;	
				
		if( !$subscription_id ) return false;
		
		$query = "SELECT * FROM #__lms_discounts 
					WHERE enabled = 1 AND discount_type = 0
						AND	( 
								( start_date = '0000-00-00' AND end_date = '0000-00-00' ) OR
								( start_date < CURDATE() AND end_date = '0000-00-00' ) OR
								( start_date = '0000-00-00' AND end_date > CURDATE() ) OR
								( CURDATE() = start_date AND CURDATE() = end_date ) OR
								( CURDATE() BETWEEN start_date AND end_date ) 
							)
						";		
		$JLMS_DB->setQuery($query);
		$discounts = $JLMS_DB->loadObjectList();
		
		$groups_id = JLMS_DISCOUNTS::getUserGroups();
					
		$res = 0;
		for( $i=0; $i<count($discounts); $i++ ) 
		{
			$discount = $discounts[$i];
					
			$step1 = true;			
			$step3 = true;
													
			if( $discount->subscriptions && !in_array( $subscription_id, explode(',', $discount->subscriptions ) ) )
				$step1 = false;
				
			$step2 = ( $discount->usergroups )?false:true;
			for( $j=0; $j<count( $groups_id ); $j++ ) 
			{								
				$group_id = $groups_id[$j];					
				if( $discount->usergroups && in_array( $group_id, explode(',', $discount->usergroups ) ) )
					$step2 = true;
			}
			
			if( $discount->users && !in_array( $my->id, explode(',', $discount->users ) ) )
				$step3 = false;
				
			if( $step1 && $step2 && $step3 )
				$res += $discount->value;			
		}						
				
		return $res;
	}
	
	function isDiscountCouponValid( $code, $subscriptions_id ) 
	{
		global $JLMS_DB, $my;	
		
		if( !isset($subscriptions_id[0]) ) return false;
		
		$code = JString::strtoupper( $code );	
		
		$groups_id = JLMS_DISCOUNTS::getUserGroups();	
		
		$query = "SELECT * FROM #__lms_discount_coupons
					WHERE enabled = 1 AND removed = 0
						AND	( 
								( start_date = '0000-00-00' AND end_date = '0000-00-00' ) OR
								( start_date < CURDATE() AND end_date = '0000-00-00' ) OR
								( start_date = '0000-00-00' AND end_date > CURDATE() ) OR
								( CURDATE() = start_date AND CURDATE() = end_date ) OR
								( CURDATE() BETWEEN start_date AND end_date ) 
							)
						AND code = ".$JLMS_DB->quote( $code )." LIMIT 1";
		  		
		$JLMS_DB->setQuery($query);
		$coupon = $JLMS_DB->loadObject();
		if ( is_object( $coupon ) && isset($coupon->code) ) 
		{	
			if( $coupon->coupon_type == 1 ) //if one-time usage 
			{
				$query = "SELECT count(*) FROM lms_disc_c_usage_stats WHERE coupon_id = ".$coupon->id;
				$JLMS_DB->setQuery( $query );
				if ( $JLMS_DB->loadResult() ) 
				{
					return false;
				}
			}
						
			$step1 = ($coupon->subscriptions )?false:true;
			for( $i=0; $i<count($subscriptions_id); $i++ ) 
			{	
				if( is_object($subscriptions_id[0]) )
					$sub_id = $subscriptions_id[$i]->id;
				else				
					$sub_id = $subscriptions_id[$i];
					
				if( $coupon->subscriptions && in_array( $sub_id, explode(',', $coupon->subscriptions ) ) )
					$step1 = true;
			}			
			
			$step2 = ( $coupon->usergroups )?false:true;
			for( $j=0; $j<count( $groups_id ); $j++ ) 
			{								
				$group_id = $groups_id[$j];					
				if( $coupon->usergroups && in_array( $group_id, explode(',', $coupon->usergroups ) ) )
					$step2 = true;
			}
			
			$step3 = true;
			if( $coupon->users && !in_array( $my->id, explode(',', $coupon->users ) ) )
				$step3 = false;
			
			if( $step1 && $step2 && $step3)	
				return true;
			else
				return false;
		} 
						
		return false;
	}
	
	function getTotalCouponDiscount( $code, $subscriptions_id ) 
	{
		global $JLMS_DB, $my;
		
		if( !isset($subscriptions_id[0]) ) return false;
		
		$code = JString::strtoupper( $code );
				
		$groups_id = JLMS_DISCOUNTS::getUserGroups();	
		
		$query = "SELECT * FROM #__lms_discount_coupons 	
					WHERE enabled = 1 AND discount_type = 1 AND removed = 0
						AND	( 
								( start_date = '0000-00-00' AND end_date = '0000-00-00' ) OR
								( start_date < CURDATE() AND end_date = '0000-00-00' ) OR
								( start_date = '0000-00-00' AND end_date > CURDATE() ) OR
								( CURDATE() = start_date AND CURDATE() = end_date ) OR
								( CURDATE() BETWEEN start_date AND end_date ) 
							)
						AND code = ".$JLMS_DB->quote( $code )." LIMIT 1";		
		$JLMS_DB->setQuery($query);
		$coupon = $JLMS_DB->loadObject();
						
		if ( is_object( $coupon ) && isset($coupon->code) ) 
		{	
			if( $coupon->coupon_type == 1 ) //if one-time usage 
			{
				$query = "SELECT count(*) FROM lms_disc_c_usage_stats WHERE coupon_id = ".$coupon->id;
				$JLMS_DB->setQuery( $query );
				if ( $JLMS_DB->loadResult() ) 
				{
					return 0;
				}
			}
						
			$step3 = true;
					
			$step1 = ( $coupon->subscriptions )?false:true;
			for( $j=0; $j<count($subscriptions_id); $j++ ) 
			{	
				if( is_object($subscriptions_id[0]) )
					$sub_id = $subscriptions_id[$j]->id;
				else				
					$sub_id = $subscriptions_id[$j];
					
				if( $coupon->subscriptions && in_array( $sub_id, explode(',', $coupon->subscriptions ) ) )
					$step1 = true;
			}		
			
			$step2 = ( $coupon->usergroups )?false:true;
			for( $j=0; $j<count( $groups_id ); $j++ ) 
			{								
				$group_id = $groups_id[$j];					
				if( $coupon->usergroups && in_array( $group_id, explode(',', $coupon->usergroups ) ) )
					$step2 = true;
			}
			
			$step3 = true;
			if( $coupon->users && !in_array( $my->id, explode(',', $coupon->users ) ) )
				$step3 = false;
						
			if( $step1 && $step2 && $step3 )	
				return $coupon->value;
			else
				return 0;	
		} 	
								
		return 0;
	}
	
	function getPercentCouponDiscount( $code, $subscription_id ) 
	{
		global $JLMS_DB;		
				
		$step3 = true;	
		
		if( !$subscription_id ) return false;
		
		$code = JString::strtoupper( $code );
		
		$groups_id = JLMS_DISCOUNTS::getUserGroups();
		
		$query = "SELECT * FROM #__lms_discount_coupons 
					WHERE enabled = 1 AND discount_type = 0 AND removed = 0
					AND	( 
							( start_date = '0000-00-00' AND end_date = '0000-00-00' ) OR
							( start_date < CURDATE() AND end_date = '0000-00-00' ) OR
							( start_date = '0000-00-00' AND end_date > CURDATE() ) OR
							( CURDATE() = start_date AND CURDATE() = end_date ) OR
							( CURDATE() BETWEEN start_date AND end_date ) 
						)
					AND code = ".$JLMS_DB->quote( $code )." LIMIT 1";
						
		$JLMS_DB->setQuery($query);
		$coupon = $JLMS_DB->loadObject();
						
		if ( is_object( $coupon ) && isset($coupon->code) ) 
		{	
			if( $coupon->coupon_type == 1 ) //if one-time usage 
			{
				$query = "SELECT count(*) FROM lms_disc_c_usage_stats WHERE coupon_id = ".$coupon->id;
				$JLMS_DB->setQuery( $query );
				if ( $JLMS_DB->loadResult() ) 
				{
					return 0;
				}
			}
					
			$step1 = true;
			$step2 = true;
			$step3 = true;
			
			if( $coupon->subscriptions && !in_array( $subscription_id, explode(',', $coupon->subscriptions ) ) )
				$step1 = false;					
				
			$step2 = ( $coupon->usergroups )?false:true;
			for( $j=0; $j<count( $groups_id ); $j++ ) 
			{								
				$group_id = $groups_id[$j];					
				if( $coupon->usergroups && in_array( $group_id, explode(',', $coupon->usergroups ) ) )
					$step2 = true;
			}
			
			if( $coupon->users && !in_array( $my->id, explode(',', $coupon->users ) ) )
				$step3 = false;
										
			if( $step1 && $step2 && $step3 )	
				return $coupon->value;
			else
				return 0;				
		}
		
		return 0;	
	}
	
	function getUserGroups() 
	{
		global $my, $JLMS_DB;
		
		$query = "SELECT group_id FROM #__lms_users_in_global_groups WHERE user_id = ".$my->id;
		$JLMS_DB->setQuery( $query );
		return $JLMS_DB->loadResultArray();		
	}
	
	function getCouponByCode( $code ) 
	{
		global $my, $JLMS_DB;
		
		$query = "SELECT * FROM #__lms_discount_coupons
				WHERE enabled = 1 AND removed = 0
				AND	( 
						( start_date = '0000-00-00' AND end_date = '0000-00-00' ) OR
						( start_date < CURDATE() AND end_date = '0000-00-00' ) OR
						( start_date = '0000-00-00' AND end_date > CURDATE() ) OR
						( CURDATE() = start_date AND CURDATE() = end_date ) OR
						( CURDATE() BETWEEN start_date AND end_date ) 
					)
				AND code = ".$JLMS_DB->quote( $code )." LIMIT 1";
		$JLMS_DB->setQuery( $query );
		$coupon = $JLMS_DB->loadObject();
		if( is_object( $coupon ) && isset($coupon->code) ) 
		{
			return $coupon;
		} else {
			return false;
		}	
	} 	 
}
?>