<?php
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

$_JLMS_PLUGINS = & JLMSFactory::getPlugins();
$_JLMS_PLUGINS->registerFunction( 'onCourseJoin', 'botAssociateCEO' );

function botAssociateCEO( $users_info) {
	$_JLMS_PLUGINS = & JLMSFactory::getPlugins();

	$dbo = & JFactory::GetDbo();
	//---->get bot info
	if ( !isset($_JLMS_PLUGINS->_user_bot_params['ceoassoc']) ) {
		// load mambot params info
		$query = "SELECT params"
		. "\n FROM #__lms_plugins"
		. "\n WHERE element = 'ceoassoc'"
		. "\n AND folder = 'user'"
		;
		$dbo->setQuery( $query );
		$bot = $dbo->loadObject();

		// save query to class variable
		$_JLMS_PLUGINS->_user_bot_params['ceoassoc'] = $bot;
	}

	// pull query data from class variable
	$bot = $_JLMS_PLUGINS->_user_bot_params['ceoassoc'];

	$botParams = new jlmsPluginParameters( $bot->params );

	$botParams->def( 'ceo_user_id', 0 );
	//<----

	if ($parent_id = $botParams->get( 'ceo_user_id', 0)) {

		if (is_array($users_info)) {
			foreach ($users_info as $user_info) {
				//---->check if user already associated to CEO and add associate if not
				$query = "SELECT COUNT(*) FROM #__lms_user_parents WHERE parent_id = $parent_id AND user_id = $user_info->user_id";
				$dbo->setQuery($query);
				if (!$dbo->loadResult()) {
					$query = "INSERT INTO #__lms_user_parents (`parent_id`, `user_id`) VALUES ($parent_id, $user_info->user_id)";
					$dbo->setQuery($query);
					$dbo->query();
				}
				//<----
			}
		} else {
			$user_info = $users_info;
			//---->check if user already associated to CEO and add associate if not
			$query = "SELECT COUNT(*) FROM #__lms_user_parents WHERE parent_id = $parent_id AND user_id = $user_info->user_id";
			$dbo->setQuery($query);
			if (!$dbo->loadResult()) {
				$query = "INSERT INTO #__lms_user_parents (`parent_id`, `user_id`) VALUES ($parent_id, $user_info->user_id)";
				$dbo->setQuery($query);
				$dbo->query();
			}
			//<----
		}

	}
	return ;
}
?>