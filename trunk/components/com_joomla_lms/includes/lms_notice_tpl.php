<?php
defined( '_JLMS_EXEC' ) or die( 'Restricted access' );
global $JLMS_CONFIG, $JLMS_SESSION;
$task 		= mosGetParam( $_REQUEST, 'task', '' );
	$id 		= intval(mosGetParam( $_REQUEST, 'id', '' ));
	$course_id = $JLMS_CONFIG->get('course_id');
	
	$doc = & JFactory::getDocument(); 
	
	if($task == $JLMS_SESSION->get('jlms_task'))
	{
		$id=0;
	}
	if($task == "quizzes")
	{
		if(isset($_REQUEST['page']))
		{
			
			$id = isset($_REQUEST['quiz_id'])?$_REQUEST['quiz_id']:(isset($_REQUEST['c_id'])?$_REQUEST['c_id']:$id);
		}
	}
	else
	if($task != "docs_view_content" && $task != "compose_lpath" && $task != "show_lp_content")
	{
		$task = $JLMS_SESSION->get('jlms_task');
	}
	

	/*
	$doc->addStyleSheet($JLMS_CONFIG->get('live_site').'/components/com_joomla_lms/lms_css/moodalbox.css', 'text/css', 'screen' );	
	
	if( JLMS_mootools12() ) {
		$doc->addScript($JLMS_CONFIG->get('live_site').'/components/com_joomla_lms/includes/js/moodalbox16.js' );
	} else {
		$doc->addScript($JLMS_CONFIG->get('live_site').'/components/com_joomla_lms/includes/js/moodalbox.js' );
	}	
		
	$domready = 'setTimeout(\'MOOdalBox.init();\', 100);';
	$JLMS_CONFIG->set('web20_domready_code', $JLMS_CONFIG->get('web20_domready_code','').$domready);
	*/
?>
		
		<script type="text/javascript">
		<!--
		 function pn_validate(){
		 	var form = document.form_pgnotice;
		 	if(form.p_notice.value == '')
		 	{
		 		alert('Error');
		 	}
		 	else
		 	{
		 		var p_notice = form.p_notice.value;
		 		var url = "index.php?tmpl=component&option=<?php echo $option;?>&task=save_notice&ntask=<?php echo $task?>&doc_id=<?php echo $id?>&course_id=<?php echo $course_id?>";
					new Ajax(url, {
						method: 'post',
						data: "p_notice="+p_notice,
						update: $('sbox-content'),
						evalScripts : true
					}).request();
		 	}
		 }
		 function pn_validate_edit(){
		 	var form = document.form_pgnotice;
		 	if(form.p_notice.value == '')
		 	{
		 		alert('Error');
		 	}
		 	else
		 	{
		 		var p_notice = form.p_notice.value;
		 		var v_id = form.v_id.value;
		 		var url = "index.php?tmpl=component&option=<?php echo $option;?>&task=save_notice&ntask=<?php echo $task?>&doc_id=<?php echo $id?>&course_id=<?php echo $course_id?>&v_id="+v_id;
					new Ajax(url, {
						method: 'post',
						data: "p_notice="+p_notice,
						update: $('sbox-content'),
						evalScripts : true
					}).request();
		 	}
		 }
		 function pn_del(url){
		 	new Ajax(url, {
						method: 'post',
						update: $('sbox-content'),
						evalScripts : true
					}).request();
			var url2 = "index.php?tmpl=component&option=<?php echo $option;?>&task=get_notice_count&ntask=<?php echo $task?>&doc_id=<?php echo $id?>&course_id=<?php echo $course_id?>";		
			new Ajax(url2, {
						method: 'post',
						update: $('pn_count')
					}).request();		
		 }
		  function pn_edit(url){
		 	new Ajax(url, {
						method: 'post',
						update: $('sbox-content')
					}).request();
					var scroll = new Fx.Scroll('sbox-content', {
						wait: false,
						duration: 100,
						offset: {'x': 0, 'y': 0},
						transition: Fx.Transitions.Quad.easeInOut
					});
					scroll.toTop();
					
		 }
		 -->
		</script>
		
<?php


function get_notice_html($option)
{
	global $JLMS_DB,$my,$JLMS_CONFIG,$JLMS_SESSION;
	
	JHtml::_('behavior.mootools');
		
	//if( !JLMS_J16version() ) 
	//{
	//JHtml::_('behavior.modal');
	JLMS_SqueezeBox('joomla');
	//}
	
	$task 		= strval(mosGetParam( $_REQUEST, 'task', '' ));
	$id 		= intval(mosGetParam( $_REQUEST, 'id', 0 ));
	if($task == $JLMS_SESSION->get('jlms_task'))
	{
		$id=0;
	}
	if($task == "quizzes")
	{
		if(isset($_REQUEST['page']))
		{
			
			$id = isset($_REQUEST['quiz_id'])?$_REQUEST['quiz_id']:(isset($_REQUEST['c_id'])?$_REQUEST['c_id']:$id);
		}
	}
	else
	if($task != "docs_view_content" && $task != "compose_lpath" && $task != "show_lp_content")
	{
		$task = $JLMS_SESSION->get('jlms_task');
	}
	
	$course_id = $JLMS_CONFIG->get('course_id');
	$query = "SELECT COUNT(*) FROM #__lms_page_notices WHERE usr_id=".$my->id." AND course_id=$course_id AND task=".$JLMS_DB->quote($task)." AND doc_id=$id";
	$JLMS_DB->setQuery($query);
	$count = $JLMS_DB->loadResult();
	
	echo ' <a class="modal" style="position: static;" rel="{size: {x: 500, y: 400}, onClose: function() {}}" href="index.php?tmpl=component&option='.$option.'&amp;task=new_notice&amp;ntask='.$task.'&amp;course_id='.$course_id.'&amp;doc_id='.$id.'">'._JLMS_USER_OPTIONS_NOTES.' <span id="pn_count">'.$count.'</span></a>';
}
?>