<?php
/**
* lms.cart.php
* Joomla LMS Component
* 
* development date: 2008 January 14 - 2008 January 21
* developer: DEN
* 
* * * ElearningForce Inc.
**/

// no direct access
defined( '_JLMS_EXEC' ) or die( 'Restricted access' );

class JLMS_CART {
	function ListSubscriptions($course_id) {
		//
	}

	function GetListSubs(&$pageNav, &$JLMS_DB, $course_id = 0, $category_id = 0, $ids = array()) {
		$where = '';
		if (!empty($ids)) {
			$where = ' AND a.id IN ('.implode(',',$ids).')';
		}
		$query = "SELECT a.*, b.*, c.course_name "
		."FROM `#__lms_subscriptions` as a, `#__lms_subscriptions_courses` as b LEFT JOIN #__lms_courses as c ON b.course_id = c.id "
		."\n WHERE a.id = b.sub_id AND a.published = 1".$where
		."\n ORDER BY a.id, b.course_id ";
		$JLMS_DB->setQuery($query);
		$subscriptions = $JLMS_DB->loadObjectList();
		$new_subs = array();
		$ids = array(0);

		for ($j = 0, $m = count($subscriptions); $j < $m; $j ++) {
			$subscription = $subscriptions[$j];
			$do_new = true;
			for ($i = 0, $n = count($new_subs); $i < $n; $i ++) {
				if ($new_subs[$i]->id == $subscription->id) {
					$new_subs[$i]->courses[] = $subscription->course_id;
					$new_subs[$i]->course_names[] = $subscription->course_name;
					$do_new = false;
				}
			}
			if ($do_new) {
				$if_mogno = true;
				for ($k = 0, $f = count($subscriptions); $k < $f; $k ++) {
					if ($subscriptions[$k]->id == $subscription->id) {
						if (!in_array($subscriptions[$k]->course_id, $avail_courses)) {
							$if_mogno = false; break;
						}
					}
				}
				if ($if_mogno && ($course_id || $category_id)) {
					$if_mogno = false;						
					for ($k = 0, $f = count($subscriptions); $k < $f; $k ++) {
						if ($subscriptions[$k]->id == $subscription->id) {
							if ( $subscriptions[$k]->course_id == $course_id || ($category_id && in_array($subscriptions[$k]->course_id, $category_course_ids)) ) {
								$if_mogno = true; break;
							}
						}
					}
				}
				if ($if_mogno) {
					$new_sub = new stdClass();
					$new_sub->id = $subscription->id;
					$new_sub->price = $subscription->price;
					$new_sub->discount = $subscription->discount;
					$new_sub->access_days = $subscription->access_days;
					$new_sub->account_type = $subscription->account_type;
					$new_sub->sub_name = $subscription->sub_name;
					$new_sub->start_date = $subscription->start_date;
					$new_sub->end_date = $subscription->end_date;
					$new_sub->courses = array($subscription->course_id);
					$new_sub->course_names = array($subscription->course_name);
					$new_subs[] = $new_sub;
					$ids[] = $subscription->id;
				}
			}
		}
		return $new_subs;
	}
}
?>