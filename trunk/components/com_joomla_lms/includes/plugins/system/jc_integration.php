<?php
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

define('_JLMS_PLUGIN_JC_HEAD_SEARCH', 'Search');
define('_JLMS_PLUGIN_JC_SEARCH', 'Go');
define('_JLMS_PLUGIN_JC_RESET', 'Reset');
define('_JLMS_PLUGIN_JC_COMMENTS', 'Comments ({X})');
define('_JLMS_PLUGIN_JC_ADD_COMMENTS', 'Add comment');

define('_JLMS_PLUGIN_JC_A_TITLE', 'read or write comments');

$_JLMS_PLUGINS = & JLMSFactory::getPlugins();

$_JLMS_PLUGINS->registerFunction( 'onAboveCourseDetailsPage'/*'onBeforeCourseDescription'*/, 'preCommentsShort' );

$_JLMS_PLUGINS->registerFunction( 'onBelowCourseDescription', 'preComments' );

$_JLMS_PLUGINS->registerFunction( 'onBelowCourseDetailsPage', 'preComments' );

$_JLMS_PLUGINS->registerFunction( 'onAboveTopicDescription', 'preTopicCommentsShort' );
$_JLMS_PLUGINS->registerFunction( 'onBelowTopicPage', 'preTopicComments' );


$_JLMS_PLUGINS->registerFunction( 'onPreparePageText', 'preSearchComments' );


function preCommentsShort($id){
	$option = 'com_joomla_lms';
	$data = new stdClass();
	$data->component = $option.'_course';
	$data->title = 'course';
	$data->id = $id;
	$data->short = mosGetParam($_REQUEST, 'short', 0);
	
	showCommentsShort($data);
}

function showCommentsShort($data){
	$db = & JFactory::getDbo();
	$_JLMS_PLUGINS = & JLMSFactory::getPlugins();
	global $option, $Itemid;

	$botParams = $_JLMS_PLUGINS->getPluginParams('jc_integration');
	
	$obj_data = new stdClass();
	
	if($botParams->get('course_comments', 1)){
		if(!isset($data->short) || !$data->short){
			if(!$botParams->get('cc_view_type', 2)){
				$comments = JPATH_SITE . '/components/com_jcomments/jcomments.php';
				if (file_exists($comments)) {
					require_once($comments);
					
					include_once (JCOMMENTS_HELPERS.DS.'system.php');
					$document = & JFactory::getDocument();
					$document->addStyleSheet(JCommentsSystemPluginHelper::getCSS());
					
					$count = JComments::getCommentsCount($data->id, $data->component);
					
					$count = isset($count) && intval($count) ? $count : 0;
					
					$link = 'index.php?option='.$option.'&task=details_course&id='.$data->id;
					$link .= '&short=1';
					$link .= '&Itemid='.$Itemid;

					$user = & JFactory::getUser();
					$link_text = ($count || !$user->get('id')) ? str_replace('{X}', $count, _JLMS_PLUGIN_JC_COMMENTS) : _JLMS_PLUGIN_JC_ADD_COMMENTS;
							
					$obj_data->short = '<div class="jcomments-links"><a class="comments-link" href="'.JLMSRoute::_($link).'" title="'._JLMS_PLUGIN_JC_A_TITLE.'">'.$link_text.'</a></div>';
				}
			}	
		}
	}
	if(isset($obj_data->short)){
		JLMS_TMPL::OpenTS('', ' align="right"');	
			echo $obj_data->short;
		JLMS_TMPL::CloseTS();
	}
	return;
}

function preComments($id){
	$option = 'com_joomla_lms';
	$data = new stdClass();
	$data->component = $option.'_course';
	$data->title = 'course';
	$data->id = $id;
	$data->short = mosGetParam($_REQUEST, 'short', 0);
	
	showComments($data);
}

function showComments($data) {
	$db = & JFactory::getDbo();
	$_JLMS_PLUGINS = & JLMSFactory::getPlugins();
	$option = 'com_joomla_lms';

	$botParams = $_JLMS_PLUGINS->getPluginParams('jc_integration');
	
	static $num_show = 0;
	$num_show++;
	
	if($data->short){
		$botParams->set('cc_view_type', 1);	
	}
	
	$search_jc 			= JRequest::getVar('search_jc', '', 'request', 'int');
	$search_jc_text 	= JRequest::getVar('search_jc_text', '', 'request', 'string');
	
	$obj_data = new stdClass();
	
	if($botParams->get('course_comments', 1)){
		if(isset($data->short) && $data->short){
//			if($botParams->get('cc_view_type', 1)){
			if($botParams->get('cc_view_type', 2) == $num_show){
//			if(true){
				$comments = JPATH_SITE . '/components/com_jcomments/jcomments.php';
				if (file_exists($comments)) {
					require_once($comments);
					
					$obj_data->form = '';
					
					if($botParams->get('search_course_comments', 1)){
						$search_through = $botParams->get('search_course_through', 0);
						
						$obj_data->form .= showSearchCommentsStandard($search_through);
					}
					
					if($search_jc){
						$comments_form = JLMS_JComments::showComments($data->id, $data->component, $data->title, $search_jc_text);
					} else {
						$comments_form = JComments::showComments($data->id, $data->component, $data->title);
					}
					
					$obj_data->form .= $comments_form;
				}
			}
		} else {
			if($botParams->get('cc_view_type', 2) == $num_show){
				$comments = JPATH_SITE . '/components/com_jcomments/jcomments.php';
				if (file_exists($comments)) {
					require_once($comments);
					
					$obj_data->form = '';
					
					if($botParams->get('search_course_comments', 1)){
						$search_through = $botParams->get('search_course_through', 0);
						
						$obj_data->form .= showSearchCommentsStandard($search_through);
					}
					
					if($search_jc){
						$comments_form = JLMS_JComments::showComments($data->id, $data->component, $data->title, $search_jc_text);
					} else {
						$comments_form = JComments::showComments($data->id, $data->component, $data->title);
					}
					
					$obj_data->form .= $comments_form;
				}
			}	
		}
	}
	if(isset($obj_data->form)){
		JLMS_TMPL::OpenTS();	
			echo $obj_data->form;
		JLMS_TMPL::CloseTS();
	}
	return;
}

function preTopicCommentsShort($topic_id){
	$option = 'com_joomla_lms';
	$data = new stdClass();
	$data->component = $option.'_topic';
	$data->title = 'topic';
	$data->id = $topic_id;
	$data->course_id = mosGetParam($_REQUEST, 'id', 0);
	$data->short = mosGetParam($_REQUEST, 'short', 0);

	showTopicCommentsShort($data);
}

function showTopicCommentsShort($data) {
	$db = & JFactory::getDbo();
	$_JLMS_PLUGINS = & JLMSFactory::getPlugins();
	$option = 'com_joomla_lms';
	$JLMS_CONFIG = & JLMSFactory::getConfig();
	$Itemid = $JLMS_CONFIG->get('Itemid');

	$botParams = $_JLMS_PLUGINS->getPluginParams('jc_integration');
	
	$obj_data = new stdClass();
	
	if($botParams->get('topic_comments', 1)){
		$comments = JPATH_SITE . '/components/com_jcomments/jcomments.php';
		if (file_exists($comments)) {
			require_once($comments);
			
			include_once (JCOMMENTS_HELPERS.DS.'system.php');
			$document = & JFactory::getDocument();
			$document->addStyleSheet(JCommentsSystemPluginHelper::getCSS());
			
			$count = JComments::getCommentsCount($data->id, $data->component);
			
			$count = isset($count) && intval($count) ? $count : 0;
			
			$link = 'index.php?option='.$option.'&task=topic&course_id='.$data->course_id.'&topic_id='.$data->id;
			$link .= '&short=1';
			$link .= '&Itemid='.$Itemid;
			$user = & JFactory::getUser();
			$link_text = ($count || !$user->get('id')) ? str_replace('{X}', $count, _JLMS_PLUGIN_JC_COMMENTS) : _JLMS_PLUGIN_JC_ADD_COMMENTS;
			
			$obj_data->short = '<div class="jcomments-links"><a class="comments-link" href="'.JLMSRoute::_($link).'" title="'._JLMS_PLUGIN_JC_A_TITLE.'">'.$link_text.'</a></div>';		
		}
	}
	if(isset($obj_data->short)){
		JLMS_TMPL::OpenTS('', ' colspan="2" align="right"');	
			echo $obj_data->short;
		JLMS_TMPL::CloseTS();
	}

	return $obj_data;
}

function preTopicComments($topic_id){
	$option = 'com_joomla_lms';
	$data = new stdClass();
	$data->component = $option.'_topic';
	$data->title = 'topic';
	$data->id = $topic_id;
	$data->short = mosGetParam($_REQUEST, 'short', 0);
	
	showTopicComments($data);
}

function showTopicComments($data) {
	$_JLMS_PLUGINS = & JLMSFactory::getPlugins();
	$option = 'com_joomla_lms';
	$db = & Jfactory::getDbo();

	$botParams = $_JLMS_PLUGINS->getPluginParams('jc_integration');
	
	$search_jc 			= JRequest::getVar('search_jc', '', 'request', 'int');
	$search_jc_text 	= JRequest::getVar('search_jc_text', '', 'request', 'string');
	
	$obj_data = new stdClass();
	
	if($botParams->get('topic_comments', 1)){
		$comments = JPATH_SITE . '/components/com_jcomments/jcomments.php';
		if (file_exists($comments)) {
			require_once($comments);
			
			$obj_data->form = '';
			
			if($botParams->get('search_topic_comments', 1)){
				$obj_data->form .= showSearchCommentsStandard(1);
			}
			
			if($search_jc){
				$comments_form = JLMS_JComments::showComments($data->id, $data->component, $data->title, $search_jc_text);
			} else {
				$comments_form = JComments::showComments($data->id, $data->component, $data->title);
			}
			
			$obj_data->form .= $comments_form;
		}
	}
	if(isset($obj_data->form)){
		JLMS_TMPL::OpenTS();	
			echo $obj_data->form;
		JLMS_TMPL::CloseTS();
	}

	return $obj_data;
}

function preSearchComments(&$text){
	$_JLMS_PLUGINS = & JLMSFactory::getPlugins();
	$db = & JFactory::getDbo();
	$JLMS_CONFIG = & JLMSFactory::getConfig();
	$option = 'com_joomla_lms';

	$task = mosGetParam($_REQUEST, 'task', '');

	if(isset($task) && ($task == 'details_course' || $task == 'topic')){ 
		$suffix = '';
		if(isset($task) && $task == 'details_course'){ 
			$suffix = '_course';
			$id = mosGetParam($_REQUEST, 'id', 0);
			$course_id = $id;
		} else if(isset($task) && $task == 'topic'){
			$suffix = '_topic';
			$id = mosGetParam($_REQUEST, 'topic_id', 0);
			$course_id = mosGetParam($_REQUEST, 'course_id', 0);
		}
		
		$data = new stdClass();
		$data->component = $option.$suffix;
		$data->title = str_replace('_', '', $suffix);
		$data->id = $id;
		$data->course_id = $course_id;
		$data->short = mosGetParam($_REQUEST, 'short', 0);
		
		showSearchComments($data, $db, $text);
	}
}

function showSearchComments($data, &$db, &$text){
	$_JLMS_PLUGINS = & JLMSFactory::getPlugins();
	$JLMS_CONFIG = & JLMSFactory::getConfig();
	$option = 'com_joomla_lms';
	$Itemid = $JLMS_CONFIG->get('Itemid');

	$botParams = $_JLMS_PLUGINS->getPluginParams('jc_integration');
	
	static $num_form = 0;
	$num_form++;	
	
	if($botParams->get('course_comments', 1) || $botParams->get('topic_comments', 1)){
	
		$task = JRequest::getCmd('task');
	
		$search_jc_text	= JRequest::getVar('search_jc_text', '', 'request', 'string');
		
		$sall = JRequest::getVar('sall', 'request', 0, 'int');
		
		$query_string = $_SERVER['QUERY_STRING'];
		preg_match_all('#([\&a-zA-Z\_]*)=([a-z\d\_]*)#', $query_string, $out, PREG_PATTERN_ORDER);
		
		$vars = $out[1];
		$available_vars = array('Itemid');
		$inputs = array();
		
		$inputs['task'] = 'details_course';
		$inputs['id'] = $data->course_id;
		
		foreach($_REQUEST as $var=>$value){
			if(in_array($var, $available_vars)){
				$inputs[$var] = $value;		
			}	
		}
		
		$inputs['short'] = 1;
		$inputs['sall'] = 1;
		
		$action = 'index.php?option='.$option;
		$action .= '&task=details_course';
		$action .= '&id='.$data->course_id;
		foreach($inputs as $name=>$value){
			if(!in_array($name, array('option', 'task', 'short', 'sall', 'Itemid'))){
				$action .= '&'.$name.'='.$value;	
			}
		}
		$action .= '&Itemid='.$Itemid;
	}

	$nameForm = 'searchJCForm_'.$num_form;
	
	ob_start();
	if($botParams->get('course_comments', 1) || $botParams->get('topic_comments', 1)){
		?>
		<style type="text/css">
			#search_jc h4{
				background-color:inherit;
				border-bottom:1px solid #D2DADB;
				color:#555555;
				font-weight:bold;
				margin-bottom:10px;
				padding:0 0 2px;
				text-align:left;
			}
			
			#search_jc input{
				background-color:#FFFFFF;
				border:1px solid #CCCCCC;
				color:#444444;
				padding:0;
			}
			#search_jc .search_fields{
				height:1%;
				margin-left:20px;
			}
		</style>
		<div id="search_jc">
			<form action="<?php echo JRoute::_($action);?>" name="<?php echo $nameForm;?>" method="post">
				<h4>
					<?php echo _JLMS_PLUGIN_JC_HEAD_SEARCH;?>
				</h4>
				<div class="search_fields">
					<table width="100%" cellpadding="0" cellspacing="3" border="0">
						<tr>
							<td align="center">
								<input type="text" name="search_jc_text" value="<?php echo $search_jc_text;?>" class="inputbox" style="width: 98%" />
							</td>
							<td width="1%">
								<input type="button" name="btn_search" value="<?php echo _JLMS_PLUGIN_JC_SEARCH;?>" onclick="document.forms.<?php echo $nameForm;?>.submit();" />
							</td>
							<td width="1%">
								<input type="button" name="btn_reset" value="<?php echo _JLMS_PLUGIN_JC_RESET;?>" onclick="document.forms.<?php echo $nameForm;?>.search_jc_text.value='';document.forms.<?php echo $nameForm;?>.short.value='0';document.forms.<?php echo $nameForm;?>.sall.value='0';document.forms.<?php echo $nameForm;?>.submit();" />
							</td>
						</tr>
					</table>
				</div>
				
				<input type="hidden" name="search_jc" value="1" />
				<input type="hidden" name="option" value="<?php echo $option;?>" />
				<?php
				foreach($inputs as $name=>$value){
					?>
					<input type="hidden" name="<?php echo trim($name);?>" value="<?php echo $value;?>" />
					<?php
				}
				?>
			</form>
		</div>
		<?php
	}
		
	$ret = ob_get_contents();
	ob_end_clean();
	
	$text = str_replace('{search_comments}', $ret, $text);
	
	return;
}

function showSearchCommentsStandard($search_through=0){
	$_JLMS_PLUGINS = & JLMSFactory::getPlugins();
	$JLMS_CONFIG = & JLMSFactory::getConfig();
	$option = 'com_joomla_lms';
	$Itemid = $JLMS_CONFIG->get('Itemid');
	
	$task = JRequest::getCmd('task');

	$search_jc_text	= JRequest::getVar('search_jc_text', '', 'request', 'string');
	
	$query_string = $_SERVER['QUERY_STRING'];
	preg_match_all('#([\&a-zA-Z\_]*)=([a-z\d\_]*)#', $query_string, $out, PREG_PATTERN_ORDER);
	
	$vars = $out[1];
	$available_vars = array('task', 'id', 'course_id', 'topic_id', 'short', 'Itemid');
	$inputs = array();
	
	foreach($_REQUEST as $var=>$value){
		if(in_array($var, $available_vars)){
			$inputs[$var] = $value;		
		}	
	}
//	$inputs['short'] = 1;
	if(!$search_through){
		$inputs['sall'] = 1;
	}
	
	$action = 'index.php?option='.$option;
	$action .= '&task='.$task;
	foreach($inputs as $name=>$value){
		if(!in_array($name, array('option', 'task', 'short', 'sall', 'Itemid'))){
			$action .= '&'.$name.'='.$value;	
		}
	}
	$action .= '&Itemid='.$Itemid;
		
	ob_start();
	?>
	<style type="text/css">
		#search_jc h4{
			background-color:inherit;
			border-bottom:1px solid #D2DADB;
			color:#555555;
			font-weight:bold;
			margin-bottom:10px;
			padding:0 0 2px;
			text-align:left;
		}
		
		#search_jc input{
			background-color:#FFFFFF;
			border:1px solid #CCCCCC;
			color:#444444;
			padding:0;
		}
		#search_jc .search_fields{
			height:1%;
			margin-left:20px;
		}
	</style>
	<div id="search_jc">
		<form action="<?php echo JRoute::_($action);?>" name="searchJCFormStnd" method="post">
			<h4>
				<?php echo _JLMS_PLUGIN_JC_HEAD_SEARCH;?>
			</h4>
			<div class="search_fields">
				<table width="100%" cellpadding="0" cellspacing="3" border="0">
					<tr>
						<td align="center">
							<input type="text" name="search_jc_text" value="<?php echo $search_jc_text;?>" class="inputbox" style="width: 98%" />
						</td>
						<td width="1%">
							<input type="button" name="btn_search" value="<?php echo _JLMS_PLUGIN_JC_SEARCH;?>" onclick="document.forms.searchJCFormStnd.submit();" />
						</td>
						<td width="1%">
							<input type="button" name="btn_reset" value="<?php echo _JLMS_PLUGIN_JC_RESET;?>" onclick="document.forms.searchJCFormStnd.search_jc_text.value='';document.forms.searchJCFormStnd.sall.value='0';document.forms.searchJCFormStnd.submit();" />
						</td>
					</tr>
				</table>
			</div>
			
			<input type="hidden" name="search_jc" value="1" />
			<input type="hidden" name="option" value="<?php echo $option;?>" />
			<?php
			foreach($inputs as $name=>$value){
				?>
				<input type="hidden" name="<?php echo trim($name);?>" value="<?php echo $value;?>" />
				<?php
			}
			?>
		</form>
	</div>
	<?php
	$ret = ob_get_contents();
	ob_end_clean();
	
	return $ret;
}

$comments = JPATH_SITE . '/components/com_jcomments/jcomments.php';
if (file_exists($comments)) {
	require_once($comments);
	
	class JLMS_JComments extends JComments {
		function showComments( $object_id, $object_group = 'com_content', $object_title = '', $search_text='' )
		{
			return JLMS_JComments::show($object_id, $object_group, $object_title, $search_text);
		}
		
		function show( $object_id, $object_group = 'com_content', $object_title = '', $search_text='' )
		{	
			$object_id = (int) $object_id;
			$object_group = trim($object_group);
			$object_title = trim($object_title);
	
			$acl = & JCommentsFactory::getACL();
			$config = & JCommentsFactory::getConfig();
			$JLMS_CONFIG = & JLMSFactory::getConfig();
			$app = & JFactory::getApplication();
	
			$tmpl = & JCommentsFactory::getTemplate($object_id, $object_group);
			$tmpl->load('tpl_index');
	
			if ($config->getInt('object_locked', 0) == 1) {
				$config->set('enable_rss', 0);
				$tmpl->addVar('tpl_index', 'comments-form-locked', 1);
			}
	
			if (JCOMMENTS_JVERSION == '1.5') {
				$document = & JFactory::getDocument();
			}
		
			if (!defined('JCOMMENTS_CSS')) {
				include_once (JCOMMENTS_HELPERS . DS . 'system.php');
				if ($app->isAdmin()) {
					$tmpl->addVar('tpl_index', 'comments-css', 1);
				} else {
					$link = JCommentsSystemPluginHelper::getCSS();					
					$document->addStyleSheet($link);					 
				}
			}
	
			if (!defined('JCOMMENTS_JS')) {
				include_once (JCOMMENTS_HELPERS . DS . 'system.php');
				if ($config->getInt('gzip_js') == 1) {					
					$document->addScript(JCommentsSystemPluginHelper::getCompressedJS());					
				} else {
					$document->addScript(JCommentsSystemPluginHelper::getCoreJS());					
	                if (!defined('JOOMLATUNE_AJAX_JS')) 
					{					
						$document->addScript(JCommentsSystemPluginHelper::getAjaxJS());						
	                    define('JOOMLATUNE_AJAX_JS', 1);
					}
				}
			}
	
			$tmpl->addVar('tpl_index', 'comments-form-captcha', $acl->check('enable_captcha'));
			$tmpl->addVar('tpl_index', 'comments-form-link', $config->getInt('form_show') ? 0 : 1);
	
			if ($config->getInt('enable_rss') == 1) 
			{
				$link = JCommentsFactory::getLink('rss', $object_id, $object_group);			
				$attribs = array('type' => 'application/rss+xml', 'title' => strip_tags($object_title));
				$document->addHeadLink($link, 'alternate', 'rel', $attribs);				 
			}
	
			$cacheEnabled = intval($JLMS_CONFIG->get('caching')) == 1;
	
			if ($cacheEnabled == 0) {
				$jrecache = $JLMS_CONFIG->get('absolute_path') . DS . 'components' . DS . 'com_jrecache' . DS . 'jrecache.config.php';
	
				if (is_file($jrecache)) {
					$cfg = new _JRECache_Config();
					$cacheEnabled = $cacheEnabled && $cfg->enable_cache;
				}
			}
	
			$load_cached_comments = $config->getInt('load_cached_comments', 0);
	
			if ($cacheEnabled) {
				$tmpl->addVar('tpl_index', 'comments-anticache', 1);
			}
	
			if (!$cacheEnabled || $load_cached_comments === 1) {
				if ($config->get('template_view') == 'tree' && !strlen($search_text)) {
					$tmpl->addVar('tpl_index', 'comments-list', JLMS_JComments::getCommentsTree($object_id, $object_group, $search_text));
				} else {
					$tmpl->addVar('tpl_index', 'comments-list', JLMS_JComments::getCommentsList($object_id, $object_group, $search_text));
				}
			}
	
			$needScrollToComment = ($cacheEnabled || ($config->getInt('comments_per_page') > 0));
			$tmpl->addVar('tpl_index', 'comments-gotocomment', (int) $needScrollToComment);
			$tmpl->addVar('tpl_index', 'comments-form', JComments::getCommentsForm($object_id, $object_group, ($config->getInt('form_show') == 1)));
	
			$result = $tmpl->renderTemplate('tpl_index');
			$tmpl->freeAllTemplates();
	
			return $result;
		}
		
		function getCommentsCount( $object_id, $object_group = 'com_content', $filter = '' )
		{
			$sall 	= JRequest::getVar('sall', 'request', 0, 'int');
			
			$object_id = (int) $object_id;
			$object_group = trim($object_group);
	
			$acl = & JCommentsFactory::getACL();
			$dbo = & JCommentsFactory::getDBO();
	
			$query = "SELECT count(*) "
				."\nFROM #__jcomments "
				."\nWHERE 1"
				.(isset($sall) && $sall ? "" : "\nAND object_id = '".$object_id."'")
				.(isset($sall) && $sall ? "" : "\nAND object_group = '".$object_group."'")
				.(($acl->canPublish() == 0) ? "\nAND published = 1" : "")
				.(JCommentsMultilingual::isEnabled() ? "\nAND lang = '" . JCommentsMultilingual::getLanguage() . "'" : "")
				."\n".$filter
				;
			$dbo->setQuery($query);
			
			return $dbo->loadResult();
		}
		
		function getCommentsList( $object_id, $object_group = 'com_content', $search_text='', $page = 0 )
		{
			global $my;
	
			$object_id = (int) $object_id;
			$object_group = trim( $object_group );
	
			$acl = & JCommentsFactory::getACL();
			$dbo = & JCommentsFactory::getDBO();
			$config = & JCommentsFactory::getConfig();
	
			$comments_per_page = $config->getInt('comments_per_page');
			$comments_page_limit = $config->getInt('comments_page_limit');
			$canPublish = $acl->canPublish();
			$canComment = $acl->canComment();
	
			$limitstart = 0;
			$total = null;
			
			$where = '';
			if($search_text){
				$sall 	= JRequest::getVar('sall', 'request', 0, 'int');
				if(isset($sall) && $sall){
					$task 		= JRequest::getCmd('task');
					if($task == 'details_course'){
						$course_id 	= JRequest::getVar('id', 'request', 0, 'int');
					} else if($task == 'topic'){
						$course_id 	= JRequest::getVar('course_id', 'request', 0, 'int');
					}
					
					$query = "SELECT id"
					. "\n FROM #__lms_topics"
					. "\n WHERE 1"
					. "\n AND course_id = '".$course_id."'"
					;
					$dbo->setQuery($query);
					$list_topics = $dbo->loadResultArray();
					
					if(count($list_topics)){
						
						$where .= " AND (";
						
						$obj_grp = 'com_joomla_lms_course';
						$where .= " (object_group = '".$obj_grp."'";	
						$where .= " AND object_id IN (".implode(',', array($course_id))."))";
						
						$where .= " OR";
						
						$obj_grp = 'com_joomla_lms_topic';
						$where .= " (object_group = '".$obj_grp."'";	
						$where .= " AND object_id IN (".implode(',', $list_topics)."))";
						
						$where .= " )";	
					}
				}
				
				$words = explode(' ', $search_text);
				$wheres = array();
				foreach ($words as $word) {
					$wheres2 = array();
					$wheres2[] = "LOWER(name) LIKE '%$word%'";
					$wheres2[] = "LOWER(comment) LIKE '%$word%'";
				}
				if(isset($wheres2) && count($wheres2)){
					$where .= " AND (";
					$where .= implode(' OR ', $wheres2);
					$where .= " )";
				}
			}
			
			if ($canComment == 0) {
				$total = JLMS_JComments::getCommentsCount($object_id, $object_group, $where);
				if ($total == 0) {
					return '';
				}
			}
	
			if ($comments_per_page > 0) {
	
				$page = (int) $page;
				$page = max(1, $page);
	
				$total = isset($total) ? $total : JLMS_JComments::getCommentsCount($object_id, $object_group, $where);
				$total_pages = ceil( $total / $comments_per_page );
	
				if ($total > 0) {
					if (($comments_page_limit > 0)
					&& ($total_pages > $comments_page_limit)) {
						$total_pages = $comments_page_limit;
						$comments_per_page = ceil($total / $total_pages);
					}
	
					if ($page <= 0) {
						$this_page = ($config->get('comments_order') == 'DESC') ? 1 : $total_pages;
					} else if ($page > $total_pages) {
						$this_page = $total_pages;
					} else {
						$this_page = $page;
					}
					$limitstart = (($this_page-1) * $comments_per_page);
	
				} else {
					$limitstart = 0;
					$this_page = 0;
				}
			}
	
			if ($total > 0) {
				$query = "SELECT c.id, c.object_id, c.object_group, c.userid, c.name, c.username, c.title, c.comment"
					."\n, c.email, c.homepage, c.date as datetime, c.ip, c.published, c.checked_out, c.checked_out_time"
					."\n, c.isgood, c.ispoor"
					."\n, v.value as voted"
					."\nFROM #__jcomments AS c"
					."\nLEFT JOIN #__jcomments_votes AS v ON c.id = v.commentid " . ( $my->id ? " AND  v.userid = ".$my->id : " AND  v.ip = '".$acl->getUserIP() . "'" )
					."\nWHERE 1"
					.(isset($sall) && $sall ? "" : "\nAND c.object_id = '".$object_id."'")
					.(isset($sall) && $sall ? "" : "\nAND c.object_group = '".$object_group."'")
					.(JCommentsMultilingual::isEnabled() ? "\nAND c.lang = '" . JCommentsMultilingual::getLanguage() . "'" : "")
					.(($canPublish == 0) ? "\nAND c.published = 1" : "")
					
					.$where
					
					."\nORDER BY c.date " . $config->get('comments_order')
					.(($comments_per_page > 0) ? "\nLIMIT $limitstart, $comments_per_page" : "")
					;
				$dbo->setQuery($query);
				$rows = $dbo->loadObjectList();
			} else {
				$rows = array();
			}
	
			$tmpl = & JCommentsFactory::getTemplate($object_id, $object_group);
			$tmpl->load('tpl_list');
			$tmpl->load('tpl_comment');
	
			if (count($rows)) {
	
				$isLocked = ($config->getInt('object_locked', 0) == 1);
	
				$tmpl->addVar( 'tpl_list', 'comments-refresh', intval(!$isLocked));
				$tmpl->addVar( 'tpl_list', 'comments-rss', intval($config->getInt('enable_rss') && !$isLocked));
				$tmpl->addVar( 'tpl_list', 'comments-can-subscribe', intval($my->id && $acl->check('enable_subscribe') && !$isLocked));
	
				if ($my->id && $acl->check('enable_subscribe')) {
					require_once (JCOMMENTS_BASE . DS . 'jcomments.subscription.php');
					$manager =& JCommentsSubscriptionManager::getInstance();
					$isSubscribed = $manager->isSubscribed($object_id, $object_group, $my->id);
					$tmpl->addVar( 'tpl_list', 'comments-user-subscribed', $isSubscribed);
				}
	
				if ($config->get('comments_order') == 'DESC') {
				        if ($comments_per_page > 0) {
						$i = $total - ($comments_per_page*($page > 0 ? $page-1 : 0));
			        	} else {
			        		$i =  count($rows);
				        }
				} else {
					$i = $limitstart + 1;
				}
	
				if ($config->getInt('enable_mambots') == 1) {
					require_once (JCOMMENTS_HELPERS . DS . 'plugin.php');
					JCommentsPluginHelper::importPlugin('jcomments');
					JCommentsPluginHelper::trigger('onBeforeDisplayCommentsList', array(&$rows));
	
					if ($acl->check('enable_gravatar')) {
						JCommentsPluginHelper::trigger('onPrepareAvatars', array(&$rows));
					}
				}
	
				$items = array();
	
				foreach ($rows as $row) {
					if ($config->getInt('enable_mambots') == 1) {
						JCommentsPluginHelper::trigger('onBeforeDisplayComment', array(&$row));
					}
	
					// run autocensor, replace quotes, smiles and other pre-view processing
					JComments::prepareComment($row);
	
					// setup toolbar
					if (!$acl->canModerate($row)) {
						$tmpl->addVar('tpl_comment', 'comments-panel-visible', 0);
					} else {
						$tmpl->addVar('tpl_comment', 'comments-panel-visible', 1);
						$tmpl->addVar('tpl_comment', 'button-edit', $acl->canEdit($row));
						$tmpl->addVar('tpl_comment', 'button-delete', $acl->canDelete($row));
						$tmpl->addVar('tpl_comment', 'button-publish', $acl->canPublish($row));
						$tmpl->addVar('tpl_comment', 'button-ip', $acl->canViewIP($row));
					}
	
					$tmpl->addVar('tpl_comment', 'comment-show-vote', $config->getInt('enable_voting'));
					$tmpl->addVar('tpl_comment', 'comment-show-email', $acl->canViewEmail($row));
					$tmpl->addVar('tpl_comment', 'comment-show-homepage', $acl->canViewHomepage($row));
					$tmpl->addVar('tpl_comment', 'comment-show-title', $config->getInt('comment_title'));
					$tmpl->addVar('tpl_comment', 'button-vote', $acl->canVote($row));
					$tmpl->addVar('tpl_comment', 'button-quote', $acl->canQuote($row));
					$tmpl->addVar('tpl_comment', 'button-reply', $acl->canReply($row));
	
					if (isset($row->_number)) {
						$tmpl->addVar('tpl_comment', 'comment-number', $row->_number);
					} else {
						$tmpl->addVar('tpl_comment', 'comment-number', $i);
					}
	
					$tmpl->addVar('tpl_comment', 'avatar', $acl->check('enable_gravatar'));
	
					$tmpl->addObject('tpl_comment', 'comment', $row);
	
					$items[$row->id] = $tmpl->renderTemplate('tpl_comment');
	
					if (!isset($row->_number)) {
						if ($config->get('comments_order') == 'DESC') {
					        	$i--;
						} else {
						        $i++;
						}
					}
				}
	
				$tmpl->addObject('tpl_list', 'comments-items', $items);
	
				// build page navigation
				if (($comments_per_page > 0) && ($total_pages > 1)) {
					$tmpl->addVar('tpl_list', 'comments-nav-first', 1);
					$tmpl->addVar('tpl_list', 'comments-nav-total', $total_pages);
					$tmpl->addVar('tpl_list', 'comments-nav-active', $this_page);
	
					$pagination = $config->get('comments_pagination');
	
					// show top pagination
					if (($pagination == 'both') || ($pagination == 'top')) {
						$tmpl->addVar('tpl_list', 'comments-nav-top', 1);
					}
	
					// show bottom pagination
					if (($pagination == 'both') || ($pagination == 'bottom')) {
						$tmpl->addVar('tpl_list', 'comments-nav-bottom', 1);
					}
				}
				unset($rows);
			}
			return $tmpl->renderTemplate('tpl_list');
		}
		
		function getCommentsTree( $object_id, $object_group = 'com_content', $search_text='' )
		{
			global $my;
	
			$object_id = (int) $object_id;
			$object_group = trim($object_group);
	
			$acl = & JCommentsFactory::getACL();
			$dbo = & JCommentsFactory::getDBO();
			$config = & JCommentsFactory::getConfig();
	
			$canPublish = $acl->canPublish();
			$canComment = $acl->canComment();
			
			$where = '';
			if($search_text){
				$words = explode(' ', $search_text);
				$wheres = array();
				foreach ($words as $word) {
					$wheres2 = array();
					$wheres2[] = "LOWER(name) LIKE '%$word%'";
					$wheres2[] = "LOWER(comment) LIKE '%$word%'";
				}
				if(isset($wheres2) && count($wheres2)){
					$where .= ' AND (';
					$where .= implode(' OR ', $wheres2);
					$where .= ' )';
				}
			}
			
			if ($canComment == 0) {
				$total = JLMS_JComments::getCommentsCount($object_id, $object_group, $where);
				if ($total == 0) {
					return '';
				}
			}
	
			$query = "SELECT c.id, c.parent, c.object_id, c.object_group, c.userid, c.name, c.username, c.title, c.comment"
				."\n , c.email, c.homepage, c.date as datetime, c.ip, c.published, c.checked_out, c.checked_out_time"
				."\n , c.isgood, c.ispoor"
				."\n , v.value as voted"
				."\n FROM #__jcomments AS c"
				."\n LEFT JOIN #__jcomments_votes AS v ON c.id = v.commentid " . ($my->id ? " AND  v.userid = " . $my->id : " AND  v.ip = '" . $acl->getUserIP() . "'")
				."\n WHERE c.object_id = ".$object_id
				."\n AND c.object_group = '".$object_group."'"
				.(JCommentsMultilingual::isEnabled() ? "\nAND c.lang = '" . JCommentsMultilingual::getLanguage() . "'" : "")
				.(($canPublish == 0) ? "\nAND c.published = 1" : "")
				.$where
				."\n ORDER BY c.parent, c.date ASC"
				;
			$dbo->setQuery($query);
			$rows = $dbo->loadObjectList();
	
			$tmpl = & JCommentsFactory::getTemplate($object_id, $object_group);
			$tmpl->load('tpl_tree');
			$tmpl->load('tpl_comment');
	
			if (count($rows)){
	
				$isLocked = ($config->getInt('object_locked', 0) == 1);
	
				$tmpl->addVar( 'tpl_tree', 'comments-refresh', intval(!$isLocked));
				$tmpl->addVar( 'tpl_tree', 'comments-rss', intval($config->getInt('enable_rss') && !$isLocked));
				$tmpl->addVar( 'tpl_tree', 'comments-can-subscribe', intval($my->id && $acl->check('enable_subscribe') && !$isLocked));
	
				if ($my->id && $acl->check('enable_subscribe')) {
	
					require_once (JCOMMENTS_BASE . DS . 'jcomments.subscription.php');
					$manager = & JCommentsSubscriptionManager::getInstance();
					$isSubscribed = $manager->isSubscribed($object_id, $object_group, $my->id);
	
					$tmpl->addVar('tpl_tree', 'comments-user-subscribed', $isSubscribed);
				}
	
				$i = 1;
	
				if ($config->getInt('enable_mambots') == 1) {
					require_once (JCOMMENTS_HELPERS . DS . 'plugin.php');
					JCommentsPluginHelper::importPlugin('jcomments');
					JCommentsPluginHelper::trigger('onBeforeDisplayCommentsList', array(&$rows));
	
					if ($acl->check('enable_gravatar')) {
						JCommentsPluginHelper::trigger('onPrepareAvatars', array(&$rows));
					}
				}
	
				require_once (JCOMMENTS_LIBRARIES . DS . 'joomlatune' . DS . 'tree.php');
	
				$tree = new JoomlaTuneTree($rows);
				$items = $tree->get();
				
				foreach ($rows as $row) {
					if ($config->getInt('enable_mambots') == 1) {
						JCommentsPluginHelper::trigger('onBeforeDisplayComment', array(&$row));
					}
	
					// run autocensor, replace quotes, smiles and other pre-view processing
					JComments::prepareComment($row);
	
					// setup toolbar
					if (!$acl->canModerate($row)) {
						$tmpl->addVar('tpl_comment', 'comments-panel-visible', 0);
					} else {
						$tmpl->addVar('tpl_comment', 'comments-panel-visible', 1);
						$tmpl->addVar('tpl_comment', 'button-edit', $acl->canEdit($row));
						$tmpl->addVar('tpl_comment', 'button-delete', $acl->canDelete($row));
						$tmpl->addVar('tpl_comment', 'button-publish', $acl->canPublish($row));
						$tmpl->addVar('tpl_comment', 'button-ip', $acl->canViewIP($row));
					}
	
					$tmpl->addVar('tpl_comment', 'comment-show-vote', $config->getInt('enable_voting'));
					$tmpl->addVar('tpl_comment', 'comment-show-email', $acl->canViewEmail($row));
					$tmpl->addVar('tpl_comment', 'comment-show-homepage', $acl->canViewHomepage($row));
					$tmpl->addVar('tpl_comment', 'comment-show-title', $config->getInt('comment_title'));
					$tmpl->addVar('tpl_comment', 'button-vote', $acl->canVote($row));
					$tmpl->addVar('tpl_comment', 'button-quote', $acl->canQuote($row));
					$tmpl->addVar('tpl_comment', 'button-reply', $acl->canReply($row));
					$tmpl->addVar('tpl_comment', 'avatar', $acl->check('enable_gravatar'));
	
					if (isset($items[$row->id])) {
						$tmpl->addVar('tpl_comment', 'comment-number', '');
						$tmpl->addObject('tpl_comment', 'comment', $row);
						$items[$row->id]->html = $tmpl->renderTemplate('tpl_comment');
	
						$i++;
					}
				}
	
				$tmpl->addObject('tpl_tree', 'comments-items', $items);
	
				unset($rows);
			}
			return $tmpl->renderTemplate( 'tpl_tree' );
		}	
	}
}
?>