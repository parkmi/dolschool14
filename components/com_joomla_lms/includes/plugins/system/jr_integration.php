<?php
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

if(file_exists(JPATH_SITE . DS . 'components' . DS . 'com_jreviews' . DS . 'jreviews' . DS . 'framework.php')){
	require_once(JPATH_SITE . DS . 'components' . DS . 'com_jreviews' . DS . 'jreviews' . DS . 'framework.php');
	define('_JLMS_JREVIEWS_ENABLED', 1);
} else {
	define('_JLMS_JREVIEWS_ENABLED', 0);
}
if (defined('_JLMS_JREVIEWS_ENABLED') && _JLMS_JREVIEWS_ENABLED) {
	$_JLMS_PLUGINS = & JLMSFactory::getPlugins();
	$_JLMS_PLUGINS->registerFunction( 'onShowBlogCourseInfo', 'jlmsjr_onShowBlogCourseInfo' );
	$_JLMS_PLUGINS->registerFunction( 'onAboveCourseDetailsPage', 'jlmsjr_onAboveCourseDetailsPage' );
	$_JLMS_PLUGINS->registerFunction( 'onBelowCourseDetailsPage', 'jlmsjr_onBelowCourseDetailsPage' );
	$_JLMS_PLUGINS->registerFunction( 'onJoomlaLMSend', 'jlmsjr_onJoomlaLMSend' );
}
class JReviews_integration extends JObject{

	function __construct(){
		parent::__construct();
	}

	function getJReviews_Form($listing_id, $tmpl_suffix=''){
		$JLMS_CONFIG = & JLMSFactory::getConfig();

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
}

function jlmsjr_onShowBlogCourseInfo($id) {
	if (!defined('_JLMSJR_TICKER')) { define('_JLMSJR_TICKER', 1); }
	echo '<div class="course_rating">';
	$JR_Int = new JReviews_integration();
	echo $JR_Int->getJReviews_Rating($id);
	echo '<div class="clr"><!-- --></div>';
	echo '</div>';
}
function jlmsjr_onAboveCourseDetailsPage($id) {
	if (!defined('_JLMSJR_TICKER')) { define('_JLMSJR_TICKER', 1); }
	JLMS_TMPL::OpenTS('', ' align="right"');
	$JR_Int = new JReviews_integration();
	echo $JR_Int->getJReviews_Rating($id);
	JLMS_TMPL::CloseTS();
}
function jlmsjr_onBelowCourseDetailsPage($id) {
	if (!defined('_JLMSJR_TICKER')) { define('_JLMSJR_TICKER', 1); }
	JLMS_TMPL::OpenTS();
	echo '<div class="jlms_jreviews_form">';
	$JR_Int = new JReviews_integration();
	echo $JR_Int->getJReviews_Form($id);
	echo '<div class="clr"><!-- --></div>';
	echo '</div>';
	JLMS_TMPL::CloseTS();
}
function jlmsjr_onJoomlaLMSend() {
	if (defined('_JLMSJR_TICKER')) {
		$style = '
		#jlms_mainarea td.rating_value{
			padding: 2px 2px 2px 5px;
		}
//		.lms_courses_blog .items-leading,
//		.lms_courses_blog .item{
//			position: relative;
//		}
//		.lms_courses_blog .course_rating{
//			position: absolute;
//			right: 5px;
//			top: 7px;
//		}
		.lms_courses_blog .course_rating{
			float: right;
			font-size: 12px;
		}
		.clr{
			clear: both;
		}
		';
		$doc = & JFactory::getDocument();
		$doc->addStyleDeclaration($style);
	}
}
?>