<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/*
if (!defined('DS')) define('DS', DIRECTORY_SEPARATOR);
if(file_exists(JPATH_SITE . DS . 'components' . DS . 'com_jreviews' . DS . 'jreviews' . DS . 'framework.php')){
	require(JPATH_SITE . DS . 'components' . DS . 'com_jreviews' . DS . 'jreviews' . DS . 'framework.php');
}
//TODO: sdelat' proverku na to shto file framework.php not found - inache vyzov funkcii nige rabotat' ne budet a budut errors
class JReviews_integration extends JObject{
	function __construct(){
		parent::__construct();
	}
	
	function getJReviews_Form($listing_id, $tmpl_suffix=''){
		global $JLMS_CONFIG;
		
		# Populate $params array with module settings
		$JreParams['data']['extension'] = 'com_joomla_lms';
		$JreParams['data']['tmpl_suffix'] = $tmpl_suffix;
		$JreParams['data']['controller'] = 'everywhere';
		$JreParams['data']['action'] = 'index';
		$JreParams['data']['listing_id'] = $listing_id;
		$JreParams['data']['limit_special'] = $JLMS_CONFIG->getCfg('list_limit', 15);
		
		// Load dispatch class
		$Dispatcher = new S2Dispatcher('jreviews', true);
		
		$jreDetail = $Dispatcher->dispatch($JreParams);
		
		$form = false;
		if($jreDetail) {
			$form = $jreDetail['output'];
		}
		return $form;
	}
	
	function getJReviews_Rating($listing_id, $tmpl_suffix=''){
		global $JLMS_CONFIG;
		
		# Populate $params array with module settings
		$JreParams['data']['extension'] = 'com_joomla_lms';
		$JreParams['data']['tmpl_suffix'] = $tmpl_suffix;
		$JreParams['data']['controller'] = 'everywhere';
		$JreParams['data']['action'] = 'category';
		$JreParams['data']['listing_id'] = $listing_id;
		
		// Load dispatch class
		$Dispatcher = new S2Dispatcher('jreviews');
		
		$jreCategory = $Dispatcher->dispatch($JreParams);
		
		$rating = false;
		if($jreCategory) {
			$rating = $jreCategory['output'];
		}
		return $rating;
	}
}*/

?>