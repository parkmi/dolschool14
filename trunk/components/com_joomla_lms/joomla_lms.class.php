<?php
/**
* joomla_lms.class.php
* Joomla LMS Component
* * * ElearningForce DK
**/

// no direct access
defined( '_VALID_MOS' ) or die( 'Restricted access' );

class mos_Joomla_LMS_menuManage extends JLMSDBTable {
	var $id 					= null;
	var $lang_var				= null;
	var $image					= null;
	var $task					= null;
	var $ordering				= null;
	var $published				= null;
	var $is_separator			= null;
	var $user_access			= null;

	function mos_Joomla_LMS_menuManage( &$db ) {
		$this->JLMSDBTable( '#__lms_menu', 'id', $db );
	}

	function check() {
		return true;
	}
}

class mos_Joomla_LMS_country extends JLMSDBTable {
	var $id 				= null;
	var $name 				= null;
	var $code 				= null;
	var $tax_type 			= null;
	var $tax 				= null;
	var $list 				= null;
	var $published 			= null;
	var $checked_out		= null;
	var $checket_out_time	= null;

	function mos_Joomla_LMS_country( &$db ) {
		$this->JLMSDBTable( '#__lms_subscriptions_countries', 'id', $db );
	}

	function check() {
		return true;
	}
}
class mos_Joomla_LMS_userrole extends JLMSDBTable {
	var $id 				= null;
	var $roletype_id		= null;
	var $lms_usertype		= null;

	function mos_Joomla_LMS_userrole( &$db ) {
		$this->JLMSDBTable( '#__lms_usertypes', 'id', $db );
	}

	function check() {
		if ($this->id || (!$this->id && ($this->roletype_id == 4 || $this->roletype_id == 2 || $this->roletype_id == 5))) {
			return true;
		} else {
			$this->_error = 'You can create only Administrator/Teacher/Assistant roles.'; 
		}
		return false;
	}
}
class mos_Joomla_LMS_pagetip extends JLMSDBTable {
	var $id 				= null;
	var $course_id			= 0;
	var $user_id			= 0;
	var $tip_task			= null;
	var $tip_message		= null;

	function mos_Joomla_LMS_pagetip( &$db ) {
		$this->JLMSDBTable( '#__lms_page_tips', 'id', $db );
	}
	function check() {
		if ($this->tip_task) {
			$db = & JFactory::GetDbo();
			$query = "SELECT count(*) FROM #__lms_page_tips WHERE tip_task = ".$db->Quote($this->tip_task)." AND id <> ".($this->id ? $this->id : 0);
			$db->SetQuery($query);
			$tips = $db->LoadResult();
			if (!$tips) {
				return true;
			}
			$this->_error = 'Tip for this page is already configured';
			return false;
		}
		$this->_error = 'Page is not selected'; 
		return false;
	}
}

class mos_Joomla_LMS_Document extends JLMSDBTable {
	var $id 					= null;
	var $course_id				= null;
	var $owner_id				= null;
	var $file_id				= null;
	var $folder_flag			= null;
	var $parent_id				= null;
	var $doc_name				= null;
	var $doc_description		= null;
	var $ordering				= null;
	var $published				= null;
	var $publish_start			= null;
	var $start_date				= null;
	var $publish_end			= null;
	var $end_date				= null;
	var $is_time_related		= null;
	var $show_period			= null;

	function mos_Joomla_LMS_Document( &$db ) {
		$this->JLMSDBTable( '#__lms_documents', 'id', $db );
	}
	function check() {
		return true;
	}
}
class mos_Joomla_LMS_Outer_Document extends JLMSDBTable {
	var $id 					= null;
	var $owner_id				= null;
	var $file_id				= null;
	var $folder_flag			= null;
	var $parent_id				= null;
	var $doc_name				= null;
	var $doc_description		= null;
	var $ordering				= null;
	var $published				= null;
	var $publish_start			= null;
	var $start_date				= null;
	var $publish_end			= null;
	var $end_date				= null;
	var $outdoc_share			= 0;
	var $allow_link				= 0;

	function mos_Joomla_LMS_Outer_Document( &$db ) {
		$this->JLMSDBTable( '#__lms_outer_documents', 'id', $db );
	}
	function check() {
		return true;
	}
}
class mos_Joomla_LMS_DropBox extends JLMSDBTable {
	var $id 				= null;
	var $course_id			= null;
	var $owner_id			= null;
	var $recv_id			= null;
	var $file_id			= null;
	var $drp_type			= null;
	var $drp_mark			= null;
	var $drp_corrected		= null;
	var $drp_time			= null;
	var $drp_name			= null;
	var $drp_description	= null;

	function mos_Joomla_LMS_DropBox( &$db ) {
		$this->JLMSDBTable( '#__lms_dropbox', 'id', $db );
	}

	function check() {
		return true;
	}
}
class mos_Joomla_LMS_Link extends JLMSDBTable {
	var $id 					= null;
	var $course_id				= null;
	var $owner_id				= null;
	var $item_id				= null;
	var $lp_type				= null;
	var $link_name				= null;
	var $link_href				= null;
	var $link_description		= null;
	var $link_type				= null;
	var $ordering				= null;
	var $published				= null;
	var $is_time_related		= null;
	var $show_period			= null;
	var $params					= '';

	function mos_Joomla_LMS_Link( &$db ) {
		$this->JLMSDBTable( '#__lms_links', 'id', $db );
	}
	function check() {
		return true;
	}
}
class mos_Joomla_LMS_LearnPath extends JLMSDBTable {
	var $id 					= null;
	var $course_id				= null;
	var $owner_id				= null;
	var $item_id				= null;
	var $lp_type				= null;
	var $lpath_name				= null;
	var $lpath_shortdescription	= null;
	var $lpath_description		= null;
	var $ordering				= null;
	var $published				= null;
	var $lp_params				= null;
	var $is_time_related		= null;
	var $show_period			= null;

	function mos_Joomla_LMS_LearnPath( &$db ) {
		$this->JLMSDBTable( '#__lms_learn_paths', 'id', $db );
	}
//to do: proverku na teachera + proverku na validnost' course_id.
	function check() {
		return true;
	}
}
class mos_Joomla_LMS_LearnPathStep extends JLMSDBTable {
	var $id 				= null;
	var $course_id			= null;
	var $lpath_id			= null;
	var $item_id			= null;
	var $step_type			= null;
	var $parent_id			= null;
	var $step_name			= null;
	var $step_shortdescription	= null;
	var $step_description	= null;
	var $ordering			= null;
	var $cond_id			= null;

	function mos_Joomla_LMS_LearnPathStep( &$db ) {
		$this->JLMSDBTable( '#__lms_learn_path_steps', 'id', $db );
	}

	function check() {
		return true;
	}
}
class mos_Joomla_LMS_UserGroup extends JLMSDBTable {
	var $id 				= null;
	var $owner_id			= null;
	var $course_id			= null;
	var $parent_id			= null;
	var $ug_name			= null;
	var $ug_description		= null;
	var $ordering			= null;
	var $group_forum		= null;
	var $group_chat			= null;

	function mos_Joomla_LMS_UserGroup( &$db ) {
		$this->JLMSDBTable( '#__lms_usergroups', 'id', $db );
	}
	function check() {
		return true;
	}
}
class mos_Joomla_LMS_GBScale extends JLMSDBTable {
	var $id 					= null;
	var $course_id				= null;
	var $scale_name				= null;
	var $min_val				= null;
	var $max_val				= null;
	var $ordering				= null;

	function mos_Joomla_LMS_GBScale( &$db ) {
		$this->JLMSDBTable( '#__lms_gradebook_scale', 'id', $db );
	}
	function check() {
		return true;
	}
}
class mos_Joomla_LMS_GBItem extends JLMSDBTable {
	var $id 					= null;
	var $course_id				= null;
	var $gbc_id					= null;
	var $gbi_name				= null;
	var $gbi_description		= null;
	var $gbi_date				= null;
	var $gbi_points				= null;
	var $gbi_option				= null;
	var $ordering				= null;

	function mos_Joomla_LMS_GBItem( &$db ) {
		$this->JLMSDBTable( '#__lms_gradebook_items', 'id', $db );
	}
	function check() {
		return true;
	}
}
class mos_Joomla_LMS_Certificate extends JLMSDBTable {
	var $id 					= null;
	var $course_id				= null;
	var $published				= 1;
	var $file_id				= null;
	var $crtf_name				= null;
	var $crtf_text				= null;
	var $crtf_align				= null;
	var $crtf_shadow			= null;
	var $text_x					= null;
	var $text_y					= null;
	var $text_size				= null;
	var $crtf_font				= null;

	function mos_Joomla_LMS_Certificate( &$db ) {
		$this->JLMSDBTable( '#__lms_certificates', 'id', $db );
	}
}
class mos_Joomla_LMS_quiz_Certificate extends JLMSDBTable {
	var $id 					= null;
	var $course_id				= null;
	var $published				= 1;
	var $file_id				= null;
	var $crtf_name				= null;
	var $crtf_text				= null;
	var $text_x					= null;
	var $text_y					= null;
	var $text_size				= null;
	var $crtf_type				= null;
	var $crtf_align				= null;
	var $crtf_shadow			= null;
	var $crtf_font				= null;

	function mos_Joomla_LMS_quiz_Certificate( &$db ) {
		$this->JLMSDBTable( '#__lms_certificates', 'id', $db );
	}
}

class mos_Joomla_LMS_HomeWork extends JLMSDBTable {
	var $id 					= null;
	var $course_id				= null;
	var $owner_id				= null;
	var $is_limited				= null;
	var $hw_name				= null;
	var $groups					= null;
	var $hw_description			= null;
	var $hw_shortdescription	= null;
	var $post_date				= null;
	var $end_date				= null;
	var $is_time_related		= null;
	var $show_period			= null;
	var $published				= null;
	var $activity_type			= null;
	var $graded_activity		= null;
	var $write_text				= null;
	
	function mos_Joomla_LMS_HomeWork( &$db ) {
		$this->JLMSDBTable( '#__lms_homework', 'id', $db );
	}
	function check() {
		return true;
	}
}


class mos_JLMS_HomeWork_Result extends JLMSDBTable {
	
	var $id 			= null;
  	var $course_id 		= null;
  	var $user_id 		= null;
  	var $hw_id 			= null;
  	var $hw_status 		= null;
  	var $hw_date 		= null;
  	var $write_text 	= null;
  	var $file_id 		= null;
  	var $grade 			= null;
  	var $comments 		= null;
	
	function mos_JLMS_HomeWork_Result( &$db ) {
		$this->JLMSDBTable( '#__lms_homework_results', 'id', $db );
	}
	
	function check() {
		return true;
	}
	
	function loadExt( $courseId, $hwId, $userId ) 
	{
		$this->_db->setQuery("SELECT * FROM #__lms_homework_results WHERE hw_id = '".$hwId."' AND user_id = '".$userId."' AND course_id = '".$courseId."'");
		$row = $this->_db->loadAssoc();
		$this->bind( $row );		
	} 
}

//Max FLMS devel
class mos_Joomla_LMS_Multicat extends JLMSDBTable {

	var $id								= null;
	var $course_id						= null;
	var $cat_id							= null;
	var $level							= null;
	
	function mos_Joomla_LMS_Multicat( &$db ) {
		$this->JLMSDBTable( '#__lms_course_level', 'id', $db );
	}
	
	function check() {
		return true;
	}
}

//Kosmos GQP level
class mos_Joomla_GQP_Multicat extends JLMSDBTable {

	var $id								= null;
	var $quest_id						= null;
	var $cat_id							= null;
	var $level							= null;

	function mos_Joomla_GQP_Multicat( &$db ) {
		$this->JLMSDBTable( '#__lms_gqp_levels', 'id', $db );
	}
	
	function check() {
		return true;
	}
}


require_once(JPATH_SITE.DS.'libraries'.DS.'joomla'.DS.'filter'.DS.'filterinput.php');
class JLMS_InputFilter extends JFilterInput {
	function decode($source){
		//$source = html_entity_decode($source, ENT_QUOTES, "ISO-8859-1");
		return $source;
	}
	function process($source)
	{
		/*
		 * Are we dealing with an array?
		 */
		if (is_array($source))
		{
			foreach ($source as $key => $value)
			{
				// filter element for XSS and other 'bad' code etc.
				if (is_string($value))
				{
					$source[$key] = $this->remove($this->decode($value));
				}
			}
			return $source;
		} else
			/*
			 * Or a string?
			 */
			if (is_string($source) && !empty ($source))
			{
				// filter source for XSS and other 'bad' code etc.
				return $this->remove($this->decode($source));
			} else
			{
				/*
				 * Not an array or string.. return the passed parameter
				 */
				return $source;
			}
	}
	function remove($source)
	{
		$loopCounter = 0;
		/*
		 * Iteration provides nested tag protection
		 */
		while ($source != $this->_cleanTags($source))
		{
			$source = $this->_cleanTags($source);
			$loopCounter ++;
		}
		return $source;
	}
	function filterAttr($attrSet)
	{
		/*
		 * Initialize variables
		 */
		$newSet = array ();

		/*
		 * Iterate through attribute pairs
		 */
		for ($i = 0; $i < count($attrSet); $i ++)
		{
			/*
			 * Skip blank spaces
			 */
			if (!$attrSet[$i])
			{
				continue;
			}

			/*
			 * Split into name/value pairs
			 */
			$attrSubSet = explode('=', trim($attrSet[$i]), 2);
			list ($attrSubSet[0]) = explode(' ', $attrSubSet[0]);

			/*
			 * Remove all "non-regular" attribute names
			 * AND blacklisted attributes
			 */
			if ((!preg_match("/^[a-z]*$/i", $attrSubSet[0])) || (($this->xssAuto) && ((in_array(strtolower($attrSubSet[0]), $this->attrBlacklist)) || (substr($attrSubSet[0], 0, 2) == 'on'))))
			{
				continue;
			}

			/*
			 * XSS attribute value filtering
			 */
			if ($attrSubSet[1])
			{
				// strips unicode, hex, etc
				$attrSubSet[1] = str_replace('&#', '', $attrSubSet[1]);

				// strip normal newline within attr value
				//$attrSubSet[1] = preg_replace('/\s+/', '', $attrSubSet[1]);
				// 25 Jan 2008 - the line above is commented by DEN ! (to allow spaces in the attribute values) (also was added the line below - \n is newline break, but not a \s)
				$attrSubSet[1] = preg_replace('/\n+/', '', $attrSubSet[1]);
				// strip double quotes
				$attrSubSet[1] = str_replace('"', '', $attrSubSet[1]);
				// [requested feature] convert single quotes from either side to doubles (Single quotes shouldn't be used to pad attr value)
				if ((substr($attrSubSet[1], 0, 1) == "'") && (substr($attrSubSet[1], (strlen($attrSubSet[1]) - 1), 1) == "'"))
				{
					$attrSubSet[1] = substr($attrSubSet[1], 1, (strlen($attrSubSet[1]) - 2));
				}
				// strip slashes
				$attrSubSet[1] = stripslashes($attrSubSet[1]);
			}
			/*
			 * Autostrip script tags
			 */
			if (JLMS_InputFilter :: badAttributeValue($attrSubSet))
			{
				continue;
			}

			/*
			 * Is our attribute in the user input array?
			 */
			$attrFound = in_array(strtolower($attrSubSet[0]), $this->attrArray);

			/*
			 * If the tag is allowed lets keep it
			 */
			if ((!$attrFound && $this->attrMethod) || ($attrFound && !$this->attrMethod))
			{
				/*
				 * Does the attribute have a value?
				 */
				if ($attrSubSet[1])
				{
					$newSet[] = $attrSubSet[0].'="'.$attrSubSet[1].'"';
				}
				elseif ($attrSubSet[1] == "0")
				{
					/*
					 * Special Case
					 * Is the value 0?
					 */
					$newSet[] = $attrSubSet[0].'="0"';
				} else
				{
					$newSet[] = $attrSubSet[0].'="'.$attrSubSet[0].'"';
				}
			}
		}
		return $newSet;
	}

	/**
	 * Function to determine if contents of an attribute is safe
	 * 
	 * @access	protected
	 * @param	array	$attrSubSet	A 2 element array for attributes name,value
	 * @return	boolean True if bad code is detected
	 */
	function badAttributeValue($attrSubSet)
	{
		$attrSubSet[0] = strtolower($attrSubSet[0]);
		$attrSubSet[1] = strtolower($attrSubSet[1]);
		return (((strpos($attrSubSet[1], 'expression') !== false) && ($attrSubSet[0]) == 'style') || (strpos($attrSubSet[1], 'javascript:') !== false) || (strpos($attrSubSet[1], 'behaviour:') !== false) || (strpos($attrSubSet[1], 'vbscript:') !== false) || (strpos($attrSubSet[1], 'mocha:') !== false) || (strpos($attrSubSet[1], 'livescript:') !== false));
	}
}


class mos_Joomla_LMS_User extends JLMSDBTable {
	var $id 				= null;
	var $user_id			= null;
	var $lms_usertype_id	= null;
	var $lms_block	 		= null;

	function mos_Joomla_LMS_User( &$db ) {
		$this->JLMSDBTable( '#__lms_users', 'id', $db );
	}

	function check() {
		return true;
	}
}
class mos_Joomla_LMS_Class extends JLMSDBTable {
	var $id 				= null;
	var $owner_id			= null;
	var $course_id			= null;
	var $parent_id			= null;
	var $ug_name	 		= null;
	var $ug_description		= null;
	var $group_forum		= 0;
	var $group_chat			= 0;
	var $start_date			= null;
	var $end_date			= null;
	var $publish_start_date	= null;
	var $publish_end_date	= null;

	function mos_Joomla_LMS_Class( &$db ) {
		$this->JLMSDBTable( '#__lms_usergroups', 'id', $db );
	}

	function check() {
		return true;
	}
}
/*class mos_Joomla_LMS_Parent extends JLMSDBTable {
	var $id 				= null;
	var $parent_id			= null;
	var $user_id			= null;
	function mos_Joomla_LMS_Parent( &$db ) {
		$this->JLMSDBTable( '#__lms_user_parents', 'id', $db );
	}
	function check() {
		return true;
	}
}*/
class mos_Joomla_LMS_Course extends JLMSDBTable {
	var $id 				= null;
	var $course_name		= null;
	var $course_description	= null;
	var $course_sh_description	= null;
	var $owner_id	 		= null;
	var $group_id	 		= null;
	var $published	 		= null;
	var $publish_start		= null;
	var $start_date			= null;
	var $publish_end		= null;
	var $end_date	 		= null;
	var $metadesc	 		= null;
	var $metakeys	 		= null;
	var $language	 		= null;
	var $self_reg	 		= null;
	var $add_forum	 		= null;
	var $add_chat	 		= null;
	var $add_hw	 			= null;
	var $add_attend	 		= null;
	var $cat_id				= null;
	var $paid				= null;
	var $course_price		= null;
	var $spec_reg			= null;
	var $gid				= null;
	var $sec_cat			= null;
	var $params				= null;
	var $ordering			= null;

	function mos_Joomla_LMS_Course( &$db ) {
		$this->JLMSDBTable( '#__lms_courses', 'id', $db );
	}

	function check() {
		return true;
	}
}


class mosLMSsubscriptionprocessor extends JLMSDBTable {
	var $id 		= null;
	var $name 		= null;
	var $filename 	= null;
	var $default_p 	= null;
	var $params		= null;
	var $checked_out = null;
	var $checked_out_time= null;
	var $published	= null;
	var $ordering	= null;

	function mosLMSsubscriptionprocessor( &$db ) {
		$this->JLMSDBTable( '#__lms_subscriptions_procs', 'id', $db );
	}

	function check() {
		return true;
	}
}

class mosLMSplugin extends JLMSDBTable {
	var $id 		= null;
	var $name 		= null;
	var $element 	= null;
	var $folder 	= null;
	var $published	= null;
	var $ordering	= null;
	var $params		= null;
	var $checked_out	= null;
	var $checked_out_time	= null;
	var $short_description	= null;

	function mosLMSplugin( &$db ) {
		$this->JLMSDBTable( '#__lms_plugins', 'id', $db );
	}

	function check() {
		return true;
	}
}

class mos_Joomla_LMS_Subscription extends JLMSDBTable {
	var $id 				= null;
	var $sub_name 			= null;
	var $start_date 		= null;
	var $end_date 			= null;
	var $account_type 		= null;
	var $access_days 		= null;
	var $published	 		= null;
	var $date 				= null;	
	var $price 				= null;
	var $discount			= null;	
	var $sub_descr			= null;
	var $restricted			= null;
	var $restricted_groups	= null;
	
	var $a1					= null;
	var $a2					= null;
	var $a3					= null;

	function mos_Joomla_LMS_Subscription( &$db ) {
		$this->JLMSDBTable( '#__lms_subscriptions', 'id', $db );
	}

	function check() {
		return true;
	}
}

class mos_Joomla_LMS_Categories extends JLMSDBTable {
	var $id 					= null;
	var $c_category				= null;
	var $parent					= null;
	var $restricted				= null;
	var $groups					= null;
	var $lesson_type			= null;

	function mos_Joomla_LMS_Categories( &$db ) {
		$this->JLMSDBTable( '#__lms_course_cats', 'id', $db );
	}

	function check() {
		return true;
	}
}

class mos_Joomla_LMS_plan extends JLMSDBTable 
{
	var $id 				= null;
	var $name 				= null;
	var $description 		= null;
	var $published 			= null;	
	var $p1					= null;
	var $t1					= null;	
	var $p2					= null;
	var $t2					= null;	
	var $p3					= null;
	var $t3					= null;
	var $src				= null;
	var $sra				= null;
	var $srt				= null;
	var $checked_out 		= null;
	var $checked_out_time	= null;
	var $params 			= null;	

	function mos_Joomla_LMS_plan(&$db) 
	{		
		$this->JLMSDBTable( '#__lms_plans', 'id', $db );					
	}
	
	function check() 
	{
		return true;
	}
	
	function load( $id ) 
	{
		if( !$id ) 
		{
			$this->p1 = '';
			$this->p2 = '';
			$this->p3 = 30;
			$this->t1 = 'D';
			$this->t2 = 'D';
			$this->t3 = 'D';
			$this->published = 1;
			$this->src = 1;
			$this->sra = 1;		
			
			return true;
		}
		
		$res = parent::load( $id );
		
		return $res;		  
	}	
}

class mos_Joomla_LMS_discount extends JLMSDBTable 
{
	var $id				= null;
	var $name			= null;
	var $enabled		= null;
	var $discount_type	= null;	
	var $value			= null;
	var $subscriptions	= null;
	var $usergroups		= null;
	var $users			= null;
	var $start_date		= null;
	var $end_date		= null;

	function mos_Joomla_LMS_discount(&$db) 
	{		
		$this->JLMSDBTable( '#__lms_discounts', 'id', $db );
	}
	
	function check() 
	{
		return true;
	}	
}

class mos_Joomla_LMS_discount_coupon extends JLMSDBTable 
{
	var $id				= null;
	var $name			= null;
	var $code			= null;
	var $enabled		= null;
	var $coupon_type	= null;
	var $discount_type	= null;	
	var $value			= null;
	var $subscriptions	= null;
	var $usergroups		= null;
	var $users			= null;
	var $start_date		= null;
	var $end_date		= null;
	var $removed		= null;

	function mos_Joomla_LMS_discount_coupon(&$db) 
	{		
		$this->JLMSDBTable( '#__lms_discount_coupons', 'id', $db );
	}
	
	function check() 
	{
		return true;
	}	
}

class jlmsPluginParameters extends JParameter  
{
	
	public function __construct($data = '', $path = '')
	{	
		if( !$data && !$path )
			return false; 
		
		parent::__construct( $data, $path );
	}	
		
	function render( $name='params' ) {
		$params =& $this->getParams($name, '_default');  // Joomla 1.5.x		

		if (is_array($params)) { // Joomla 1.5.x
			$html = array ();
			$html[] = '<table width="100%" class="paramlist admintable" cellspacing="1">';

			foreach ($params as $param)
			{				
				$html[] = '<tr>';
								
				if( strpos( $param[1], 'type="radio"' ) !== false ) 
				{
					$param1 = '<fieldset class="radio">'.$param[1].'</fieldset>';
				} else {
					$param1 = $param[1];
				}
	
				if ($param[0]) {
					$html[] = '<td width="40%" class="paramlist_key"><span class="editlinktip">'.$param[0].'</span></td>';
					$html[] = '<td class="paramlist_value">'.$param1.'</td>';
				} else {
					$html[] = '<td class="paramlist_value" colspan="2">'.$param1.'</td>';
				}
	
				$html[] = '</tr>';
			}

			if (count($params) < 1) {
				$html[] = "<tr><td colspan=\"2\"><i>".JText::_('There are no Parameters for this item')."</i></td></tr>";
			}

			$html[] = '</table>';

			return implode("\n", $html);
		} else {
			return "<textarea name=\"$name\" cols=\"40\" rows=\"10\" class=\"inputbox\">$this->_raw</textarea>";
		}
	}
}


class jlms_adm_config {
	//Show Status Lapths/Scorms //by Max - 25.02.2011
	var $scorm_status_as 	= '0';
	var $lpath_status_as 	= '0';
	//Show Status Quizzes //by Max - 13.05.2011
	var $quiz_status_as 	= '0';

	var $branding_option = '0';
	var $tracking_enable	= null;
	var $chat_enable		= '0';
	var $conference_enable	= '0';
	var $is_cb_installed	= null;
	var $lms_isonline		= '1';
	var $maxConfClients		= null;
	var $new_user_password	= null;
	var $flascommRoot		= null;
	var $default_language	= null;
	var $allow_import_users	= null;
	var $attendance_days	= null;
	var $offline_message	= null;
	var $date_format		= null;
	var $date_format_fdow	= null;
	var $lms_check_version	= null;
	//911Chef (Max)
	var $show_short_description	= 0;
	var $show_course_publish_dates	= 0;
	var $price_fee_type	= 1;
	
	//courses (1.0.3)
	var $show_fee_column	= 1;
	var $show_paid_courses	= 1;
	var $show_future_courses	= 0; // for 1.0.6
	var $show_course_fee_property = 1;
	var $show_course_spec_property = 1;
	var $show_course_meta_property = 1;
	var $show_course_access_property = 1;
	var $show_course_authors = 1;
	//documents
	var $scorm_folder		= null;
	var $jlms_crtf_folder 	= null; /* 18.10.2007 */
	var $temp_folder		= null;
	var $jlms_doc_folder	= null;
	var $jlms_backup_folder	= null;

	/* 18.10.2007 - certificate options */
	var $save_certificates 	= null;
	var $crtf_show_sn 		= null;
	var $crtf_show_barcode 	= null;
	var $crtf_duplicate_wm 	= null;

	//payments settings
	var $jlms_cur_code		= 'USD';
	var $jlms_cur_sign		= '$';
	var $enabletax			= null;
	var $get_country_info	= null; //by Max (get country information)
	var $default_tax_type	= null;
	var $default_tax		= null;

	// 27.11.2007 (DEN) - SSL mod
	var $use_secure_checkout = 0;
	var $secure_url = '';

	// 05.06.2008 (DEN) - 2nd SSL mod
	var $use_secure_enrollment = 0;

	// 10.01.2008 (DEN) Custom subscription MOD
	var $use_custom_subscr	= 0;
	var $custom_subscr_name	= 'Custom subscription';
	
	var $enableterms		= '0';
	var $jlms_terms			= 'Terms and conditions agreement on subscription checkout.';
	var $jlms_admin_emails	= null;
	var $jlms_ap_redirect	= null;
	//plugin
	var $plugin_quiz		= null;
	var $plugin_forum		= '0';

	var $plugin_private_forum	= '0'; // 15.03.2008 - by DEN - private teachers boards
	var $plugin_private_lpath_forum	= '0'; // 15.03.2008 - by DEN

	// 04.02.08 (TPETb) l_path forum option
	var $plugin_lpath_forum	= '0';

	var $plugin_private_forum_name = '{course_name} - Teachers board';
	var $plugin_private_forum_desc = 'Private discussions';
	var $plugin_lpath_forum_name = '{lpath_name}';
	var $plugin_lpath_forum_desc = '';
	var $plugin_private_lpath_forum_name = '{lpath_name} - Teachers board';
	var $plugin_private_lpath_forum_desc = 'Private discussions';
	

	// 11.02.08 (TPETb) teacher can edit max attendees in course option
	var $max_attendees_change	= '0';
	//end
	var $forum_path			= null;
	var $quiz_hs_offset_manual_correction = '0';
	var $quiz_hs_offset_div_class = 'wrapper';
	var $quiz_hs_ofset_manual_value = '0';
	// 23.01.08 (Max) quiz "hotspot" size block
	var $quiz_match_max_width = '250';
	var $quiz_match_max_height = '30';
	var $quiz_progressbar = '0';
	var $quiz_progressbar_width = '300';
	var $quiz_progressbar_highlight = '0';
	var $quiz_progressbar_smooth = '1';

	//Front Page
	var $frontpage_text		= '';
	var $frontpage_text_guest = '';
	var $frontpage_courses	= 1;
	var $frontpage_courses_expand_all = 0;
	var $frontpage_allcourses = 1;
	var $frontpage_announcements = 1;
	var $frontpage_homework	= 1;
	var $frontpage_dropbox	= 1;
	var $frontpage_mailbox	= 1;
	var $frontpage_certificates	= 1;
	var $frontpage_latest_forum_posts	= 1;
	var $homepage_items		= 10;

	var $jlms_heading		= 'JoomlaLMS';
	var $jlms_title			= 'Online Courses';
	var $meta_keys			= 'online courses, elearning, lms, online education';
	var $meta_desc			= 'Online courses catalog by JoomlaLMS';
	//conference colors (1.0.3)	
	var $conf_background		= '#E0DFE4';
	var $conf_main_color		= '#F1F6CE';
	var $conf_title_color		= '#E7F1B2';
	var $conf_border_color		= '#999999';
	var $conf_title_font_color	= '#798730';
	var $conf_toolbar_color		= '#E0DFE4';
	var $conf_files_font_color	= '#666666';
	var $conf_description		= '';
	//juser integration
	var $jlms_juser_address		= '';
	var $jlms_juser_city		= '';
	var $jlms_juser_state		= '';
	var $jlms_juser_postal_code	= '';
	var $jlms_juser_country		= '';
	var $jlms_juser_phone		= '';
	var $jlms_juser_location	= '';
	var $jlms_juser_website		= '';
	var $jlms_juser_icq			= '';
	var $jlms_juser_aim			= '';
	var $jlms_juser_yim			= '';
	var $jlms_juser_msn			= '';
	var $jlms_juser_company		= '';
	//cb integration	
	var $jlms_cb_address		= '';
	var $jlms_cb_city			= '';
	var $jlms_cb_state			= '';
	var $jlms_cb_postal_code	= '';
	var $jlms_cb_country		= '';
	var $jlms_cb_phone			= '';
	var $jlms_cb_location		= '';
	var $jlms_cb_website		= '';
	var $jlms_cb_icq			= '';
	var $jlms_cb_aim			= '';
	var $jlms_cb_yim			= '';
	var $jlms_cb_msn			= '';
	var $jlms_cb_company		= '';
	var $guest_access_subscriptions = 1;
	//08.11.2007 new payment options
	var $jlms_subscr_status_email = '1';
	var $jlms_subscr_invoice_path = '';
	//secondary categories emplementation
	var $sec_cat_use			= 0;
	var $sec_cat_show			= 0;
	//global groups emplementation
	var $use_global_groups		= 1;
	//--message options-----//
	var $mess_enotify			= 0;
	var $mess_alearn			= 1;	
	var $jlms_help_link			= 'http://www.joomlalms.com/index.php?option=com_lms_help&Itemid=40&task=view_by_task&key={toolname}';
	
	var $pathway_show_lmshome		= 0;
	var $pathway_show_coursehome	= 1;
	
	//--notes options ---//
	var $jlms_notecez			= 1;
	//---look_feel config---///
	var $lofe_show_top			= 1;
	var $lofe_menu_style		= 1;
	var $lofe_show_head			= 1;
	var $lofe_show_course_box	= 1;
	var $lofe_box_type			= 1;
	//10.09.2008 course sort option
	var $lms_courses_sortby		= 0;

	var $backend_access_gid		= '24,25';

	var $branding_free_configured = 0;
	
	function jlms_adm_config() 
	{
		$version = new JVersion();
		if ( $version->RELEASE >= '1.6' ) 
		{
			$this->backend_access_gid = '7,8';
		}
	}
	
	function loadFromDb( &$db ){
		$dbo = & JFactory::getDbo();
		$query = "SELECT * FROM `#__lms_config`";
		$dbo -> setQuery($query);
		$rows = $dbo->loadObjectList();
		$rows1= array();
		foreach ($rows as $row){
			$rows1[$row->lms_config_var] = $row->lms_config_value;
		}
		
		$this->bind($rows1);
	}
	function getPublicVars() {
		$public = array();
		
		$vars = array_keys( get_class_vars( get_class( $this ) ) );
		sort( $vars );
		foreach ($vars as $v) {
			if ($v{0} != '_') {
				$public[] = $v;
			}
		}
		return $public;
	}

	function bind( $array, $ignore='' ) {
		if (!is_array( $array )) {
			$this->_error = strtolower(get_class( $this )).'::bind failed.';
			return false;
		} else {
			return mosBindArrayToObject( $array, $this, $ignore );
		}
	}

	function saveToDb( &$db ){
		$dbo = & JFactory::GetDbo();
		$query = "DELETE FROM `#__lms_config` WHERE lms_config_var != 'jlms_version' ";
		$dbo->setQuery($query);
		$dbo->query();
		$this->attendance_days = serialize($this->attendance_days);
		$vars = $this->getPublicVars();
						
		foreach ($vars as $v) {
			if ($v == 'maxConfClients') {
				$this->$v = intval($this->$v);
			} elseif ($v == 'backend_access_gid' && is_array($this->$v)) {
				$this->$v = implode(',', $this->$v);
			}
			$query = "INSERT INTO `#__lms_config` (lms_config_var, lms_config_value) "
			."VALUES (".$dbo->Quote($v).", ".$dbo->Quote($this->$v).")";
			$dbo->setQuery($query);
			$dbo->query();
		}
	}
}


class JLMS_Mail
{
	var $assigned = -1; 	//negative value makes system send all mails from db
	var $db; 				//db variable
	var $mails = array();	//list mails that should be processed
	var $redirect = '';		//indecates redirection after operations done
	var $end;				//indicates if class called from back or front end

	function JLMS_Mail( &$db, $redirect, $end='front' ) {
		$this->db = &$db;
		$this->end = $end;
		$this->redirect = $redirect;
		$this->redirect = str_replace('&amp;', '&', $this->redirect);
	}

	function setAssigned ( $user_id = 0 ) {
		$this->assigned = $user_id; //0 value indicates notifications assigned to admin
	}

	function getMails () {
		if ($this->assigned < 0) {
			$query = "SELECT * FROM #__lms_notifications WHERE sent=0";
		} else {
			$query = "SELECT * FROM #__lms_notifications WHERE sent=0 AND assigned=$this->assigned";
		}
		$this->db->setQuery($query);
		$this->mails = $this->db->loadObjectList();
	}

	function sendMails () {
		foreach ($this->mails as $mail) {
			$this->sendMail($mail);
		}
	}

	function sendMail ( $mail ) {
		$app = & JFactory::getApplication(); 
		$sent = mosMail($app->getCfg('mailfrom'), $app->getCfg('fromname'), $mail->mail_address, $mail->mail_subject, $mail->mail_body);
		if (!$sent) $query = "UPDATE #__lms_notifications SET sent=-1 WHERE id=$mail->id";
		else $query = "UPDATE #__lms_notifications SET sent=1 WHERE id=$mail->id";
		$this->db->setQuery($query);
		$this->db->query();
	}

	function showPage () {
		if (!defined('_JLMS_IFRAMES_REQUIRES')) {
			define('_JLMS_IFRAMES_REQUIRES', 'This option will not work correctly.  Unfortunately, your browser does not support Inline Frames.');
		}
		?>
		<link href="<?php echo JLMSCSS::link(); ?>" rel="stylesheet" type="text/css" />
		<script language="JavaScript" src="<?php echo JURI::root(); ?>components/com_joomla_lms/includes/js/progressbar.js" type="text/javascript"></script>
		<div class="joomlalms_sys_message" style="width:450px; margin:auto; margin-top:200px; margin-bottom:5px;">Sending notifications to WL users, placed to course.</div>
		<div id="mail_wraper" style="margin:auto;width:300px;">			
			<div id="progress_bar" style="margin:2px;"><!-- --></div>			
				<div id="mailresults" style="width:300px; background-color:#ffffff; color:#555555; font-family:monospace; font-size:9pt;" align="center">Preparing to send notifications</div>
			<iframe src="index.php?tmpl=component&option=com_joomla_lms&task=mail_iframe&assigned=<?php echo $this->assigned; ?>&redirect=<?php echo urlencode($this->redirect);?>" style="display:none">
				<?php echo _JLMS_IFRAMES_REQUIRES; ?>
			</iframe>
		</div>
		<script language="JavaScript" type="text/javascript">
		<!--
			var progressbar = new ProgressBar({id:'progress_bar',width:'300',highlight:'150',smooth:'1'});
		//-->
		</script>
		<?php
	}

	function showIFrame () {
		@set_time_limit(3600);
		while (ob_get_level()) { ob_end_flush(); } 
		$mail_count = count($this->mails);
		if ($mail_count) {
			$addProgress = 100/($mail_count);
		} else {
			$addProgress = 100;
		}
		$i = 1;
		@ob_end_clean();
			@ob_start();
			echo "
			<script type=\"text/javascript\" language=\"javascript\">
				parent.progressbar.addProgress($addProgress);
			</script>";
		@flush();
		@ob_flush();
		foreach ($this->mails as $mail) {			
			$this->sendMail($mail);	
			@ob_end_clean();
			@ob_start();
			echo "
			<script type=\"text/javascript\" language=\"javascript\">
				var m_c_div = parent.document.getElementById('mailresults');
				m_c_div.innerHTML = 'Sending message $i out of $mail_count total.';
				parent.progressbar.addProgress($addProgress);
			</script>";
			@flush();
			@ob_flush();			
			$i++;
			sleep(2);
		}
		@ob_end_clean();
		@ob_start();
		echo "
		<script type=\"text/javascript\" language=\"javascript\">
			var m_c_div = parent.document.getElementById('mailresults');
			m_c_div.innerHTML = 'All messages sent. You will be redirected in few seconds.';
			parent.location.href = '".urldecode($this->redirect)."';
		</script>";
		@flush();
	}
}

?>