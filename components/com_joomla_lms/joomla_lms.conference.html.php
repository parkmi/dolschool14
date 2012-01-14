<?php
/**
* joomla_lms.conference.html.php
* (c) JoomaLMS eLearning Software http://www.joomlalms.com/
**/

// no direct access
defined( '_JLMS_EXEC' ) or die( 'Restricted access' );

class JLMS_conference_html {
	
	function jlms_room_selecting($course_id, $option, $user_id, $usertype, $recorded_session, $param_book, $en_book, $msg_name, $msg_descr, $msg_access ){
		global $my, $JLMS_DB, $Itemid, $JLMS_CONFIG;
		
		$doc = & JFactory::getDocument();
		
		$flashcomroot = $JLMS_CONFIG->get('flascommRoot');
		$webRoot =  $JLMS_CONFIG->get('live_site');
		$maxclients = $JLMS_CONFIG->get('maxConfClients');		
		//include( _JOOMLMS_FRONT_HOME."/includes/flashfix.inc.php" );
		//flashfix_init("components/com_joomla_lms/includes/js" );
		if ($usertype == 1){
			$recorded_session = "&recorded_session=".$recorded_session;
		}else{
			$recorded_session = '';
		}
		$room_height = 643;
		if ($JLMS_CONFIG->get('is_trial')) {
			$room_height = 687;
		}
				
		$doc->addScript( $JLMS_CONFIG->get('live_site').'/components/com_joomla_lms/includes/js/swfobject.js' );
		
		?>
		<script language="javascript" type="text/javascript">
		<!--//
			function startApp()
			{
				var y= (screen.height/2) - 359;
				var x= (screen.width/2) - 510 ;
				window.open('<?php echo str_replace('&amp;', '&', sefRelToAbs("index.php?tmpl=component&option=com_joomla_lms&Itemid=$Itemid&task=conference&mode=conference_room&course_id=$course_id".$recorded_session));?>','Conference_room','top='+y+', left='+x+',width=994, height=<?php echo $room_height;?>, titlebar=0, menubar=no, scrollbars=0, status=1, toolbar=0, resizable=no');
				<?php /* do not replace & with '&amp;' here! */ ?>
			}
		//-->
		</script>
<?php
		JLMS_TMPL::OpenMT();

		$hparams = array();
		$toolbar = array();
		if($usertype == 1) {
			if ($en_book) {
				$toolbar[] = array('btn_type' => 'booking', 'btn_js' => sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid&amp;task=conference&amp;id=$course_id&amp;mode=booking"));
			}
		}

		$toolbar[] = array('btn_type' => 'archive', 'btn_js' => sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid&amp;task=conference&amp;id=$course_id&amp;mode=archive"));
		
		JLMS_TMPL::ShowHeader('conference', _JLMS_CONFERENCE_TITLE, $hparams, $toolbar);

		JLMS_TMPL::OpenTS();
?>
		<table width="100%" cellpadding="0" cellspacing="0" border="0" class="jlms_table_no_borders">
			<tr>
<?php
				$head_msg = _JLMS_CONFERENCE_WELCOME;
				$head_desc = '';
				if ($en_book) {
					if(!$param_book) {
						$head_desc = $msg_name;
						$head_msg = '';
					} elseif($param_book) {
						$head_msg = $msg_name;
						$head_desc = $msg_descr;
						if($msg_access && $usertype == 1) {
							if ($head_desc) {
								$head_desc .= "<br /><br />".$msg_access;
							} else {
								$head_desc = $msg_access;
							}
						}
					}
				} ?>
				<td colspan="2"><?php if ($head_msg) { echo JLMSCSS::h2($head_msg); } if ($head_desc) { ?><div class="joomlalms_sys_message" style="text-align: left;"><?php echo $head_desc;?></div><?php } ?></td>
			</tr>
<?php if($param_book) { ?>
			<tr>
				<td width="280"><br />
				<div id="room_enter">
					<strong><?php echo _JLMS_JS_FLASH_REQUIRES;?></strong>
				</div>
				<?php $params = 'course_id='.$course_id.'&amp;flashcommRoot='.$flashcomroot.'&amp;maxClients='.$maxclients.'&amp;master='.$usertype;
				$doc->addScriptDeclaration('
					// <![CDATA[
					window.addEvent(\'domready\', function(){
						var so = new SWFObject("'.$JLMS_CONFIG->get('live_site').'/components/com_joomla_lms/includes/conference/room_enter_106.swf?'.$params.'", "sotester", "220", "180", "8", "#ffffff");
						so.addVariable("allowScriptAccess", "sameDomain"); // this line is optional, but this example uses the variable and displays this text inside the flash movie
						so.addVariable("wmode", "transparent"); 
						so.addVariable("flashvars", "hello there"); 
						so.addVariable("salign", "t"); 
						so.addVariable("menu", "false");
						so.write("room_enter");
					});
					// ]]>
				');
				?>	
				
				<?php 
					/*$params = 'course_id='.$course_id.'&amp;flashcommRoot='.$flashcomroot.'&amp;maxClients='.$maxclients.'&amp;master='.$usertype;
					echo flashfix_html( 'components/com_joomla_lms/includes/conference/room_enter.swf?'.$params, 220, 180,	array( 'allowScriptAccess'=>'sameDomain', 'wmode'=>'transparent', 'bgcolor'=>'#ffffff',"flashvars"=> "hello there", 'salign'=>'t', 'menu'=>'false') // additional parameters
					);*/
				?>	
				</td>
				<td align="left">
				<?php
					$text = JLMS_ShowText_WithFeatures($JLMS_CONFIG->get('conf_description'));
					echo $text;
				?>
				</td>
			</tr>
	<?php } ?>
		</table>
<?php
		JLMS_TMPL::CloseTS();
		JLMS_TMPL::CloseMT();
	}

	function JLMS_editRecordDetails(  $course_id, $option, &$record, &$lists ){
		global $my, $Itemid,$JLMS_CONFIG,$JLMS_SESSION;
		?>
		<script type="text/javascript" language="javascript">
		<!--//--><![CDATA[//><!--
		function submitbutton(pressbutton) {
			var form = document.adminForm;
			if ( (pressbutton == 'save_record')){
				form.mode.value = pressbutton;
				form.submit();
			}
		}
		//--><!]]>
		</script>
		<form action="<?php echo sefRelToAbs("index.php?option=$option&Itemid=$Itemid");?>" method="post" name="adminForm">
		<?php
		JLMS_TMPL::OpenMT();
	
		$params = array(
			'show_menu' => true
		);
		JLMS_TMPL::ShowHeader('conference', _JLMS_HEAD_CONF_STR, $params);
	
		JLMS_TMPL::OpenTS('', ' align="right" style="text-align:right " width="100%"');
			
		JLMS_TMPL::CloseTS();
		JLMS_TMPL::CloseMT();
		?>
		<table width="100%" cellpadding="0" cellspacing="0" border="0" class="contentpane">
			<tr>
				<td>
					<table width="100%" cellpadding="0" cellspacing="0" border="0">
						<tr>
							<td style="text-align:right " colspan="3"><br />
								<?php $toolbar = array();
								$toolbar[] = array('btn_type' => 'save', 'btn_js' => "javascript:submitbutton('save_record');" );
								$toolbar[] = array('btn_type' => 'back', 'btn_js' => sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid&amp;task=conference&mode=archive&amp;id=$course_id") );
								echo JLMS_ShowToolbar($toolbar); ?>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td>
				<table width="100%" cellpadding="0" cellspacing="0" border="0">
					<tr>
						<td width="150" height="30px"><?php echo _JLMS_CONFERENCE_RECORD_NAME;?></td>
						<td><input type="text" class="inputbox" maxlength="200" size="51" name="record_name" value="<?php echo $record->record_name;?>" /></td>
					</tr>
					<tr>
						<td width="150" valign="top"><?php echo _JLMS_CONFERENCE_RECORD_DESC;?></td>
						<td><textarea class="inputbox" rows="3" cols="38" name="description"><?php echo $record->description;?></textarea></td>
					</tr>
					<tr>
						<td><?php echo _JLMS_STATUS_PUB;?></td>
						<td><?php echo $lists["published"].":";?></td>
					</tr>				
				</table>
				</td>				
			</tr>
		</table>
		<input type="hidden" name="task" value="conference" />
		<input type="hidden" name="mode" value="" />
		<input type="hidden" name="id" value="<?php echo $course_id;?>" />
		<input type="hidden" name="cid" value="<?php echo $record->id;?>" />
		</form>
		<?php
	}
	
	//-------------------------Archive------------------------------------------//
	function jlms_conference_archive( $course_id, $option, $records, $pageNav ){
	global $my, $Itemid,$JLMS_CONFIG,$JLMS_SESSION;
	//$usertype = $JLMS_CONFIG->get('current_usertype') ;
	$JLMS_ACL = & JLMSFactory::getACL();
	?>
	
	
	<script type="text/javascript" language="javascript">
	<!--//--><![CDATA[//><!--
	function submitbutton(pressbutton) {
		var form = document.adminForm;
		if ( form.boxchecked.value == "0" ){
			alert("<?php echo _JLMS_ALERT_SELECT_ITEM;?>");
		} else {
			if (pressbutton == 'record_delete'){
				if (confirm("<?php echo 'Are you sure?';?>") == true){
					form.mode.value = pressbutton;
					form.submit();
				}
			}else{
				form.mode.value = pressbutton;
				form.submit();
			}
		}
	}
	function startAppPlayback(path)
	{
		var y= (screen.height/2) - 359;
		var x= (screen.width/2) - 510 ;
		window.open(path,'Conference_playback','top='+y+', left='+x+',width=995, height=640, scrollbars=no, status=0, toolbar=no, resizable=yes');
	}
	function submitbutton_change(pressbutton, state) {
		var form = document.adminForm;
		if (pressbutton == 'change_record'){
			if (form.boxchecked.value == "0") {
				alert("<?php echo _JLMS_ALERT_SELECT_ITEM;?>");
			} else {
				form.mode.value = pressbutton;
				form.state.value = state;
				form.submit();
			}
		}
	}
	function submitbutton_change_record(pressbutton, state, cid_id) {
		var form = document.adminForm;
		if (pressbutton == 'change_record'){
			form.mode.value = pressbutton;
			form.state.value = state;
			form.cid2.value = cid_id;
			form.submit();
		}
	}
	//--><!]]>
	</script>
	<?php
		JLMS_TMPL::OpenMT();
	
		$params = array(
			'show_menu' => true
		);
		JLMS_TMPL::ShowHeader('conference', _JLMS_HEAD_CONF_STR, $params);
	
		JLMS_TMPL::OpenTS();//'', ' align="right" style="text-align:right " width="100%"');
?>
		<table width="100%" cellpadding="0" cellspacing="0" border="0" class="jlms_table_no_borders">
			<tr>
				<td colspan="2">
					&nbsp;
				</td>
				<td style="text-align:right;"><br />
					<?php $toolbar = array();
					//$toolbar[] = array('btn_type' => 'edit', 'btn_js' => sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid&amp;task=conference&amp;id=$course_id&amp;mode=archive") );
					$toolbar[] = array('btn_type' => 'back', 'btn_js' => sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid&amp;task=conference&amp;id=$course_id") );
					echo JLMS_ShowToolbar($toolbar); ?>
				</td>
			</tr>
		</table>
<?php
		JLMS_TMPL::CloseTS();
		JLMS_TMPL::OpenTS();
?>
		<form action="<?php echo sefRelToAbs("index.php?option=$option&Itemid=$Itemid");?>" method="post" name="adminForm">
			<table width="100%" cellpadding="0" cellspacing="0" border="0" class="<?php echo JLMSCSS::_('jlmslist');?>">
				<tr>
					<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="left" width="15">#</<?php echo JLMSCSS::tableheadertag();?>>
					<?php if($JLMS_ACL->CheckPermissions('conference', 'manage')){?>
					<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" align="left" width="10"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($records); ?>);" /></<?php echo JLMSCSS::tableheadertag();?>>
					<?php }?>
					<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" width="25%" align="left"><?php echo _JLMS_CONFERENCE_RECORD;?></<?php echo JLMSCSS::tableheadertag();?>>
					<?php if($JLMS_ACL->CheckPermissions('conference', 'manage')){?>
					<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" width="140" align="left"><?php echo _JLMS_CONFERENCE_SESSION;?></<?php echo JLMSCSS::tableheadertag();?>>
					<?php }?>
					<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" width="110" align="left"><?php echo _JLMS_CONFERENCE_RECORD_START;?></<?php echo JLMSCSS::tableheadertag();?>>
					<?php if($JLMS_ACL->CheckPermissions('conference', 'manage')){?>
					<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" width="50" align="center">&nbsp;</<?php echo JLMSCSS::tableheadertag();?>>
					<?php }?>
					<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>">&nbsp;</<?php echo JLMSCSS::tableheadertag();?>>
					<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" width="100" align="center" style="text-align:center;"><?php echo _JLMS_CONFERENCE_RECORD_PB;?></<?php echo JLMSCSS::tableheadertag();?>>
					
				</tr>
			<?php
			$k = 1;			
			for ($i=0, $n=count($records); $i < $n; $i++) {
				$row = $records[$i];
				
				$link 	= sefRelToAbs("index.php?option=".$option."&Itemid=".$Itemid."&task=details_course&id=". $row->id);
				$checked = mosHTML::idBox( $i, $row->id);
				$overlib_descr = JLMS_txt2overlib($row->description);
				$overlib_title = "Description";
				?>
				<tr valign="middle" style="vertical-align:middle" class="<?php echo JLMSCSS::_('sectiontableentry'.$k); ?>">
					<td align="left"><?php echo ( $pageNav->limitstart + $i + 1 );?></td>
					<?php if($JLMS_ACL->CheckPermissions('conference', 'manage')){?>
					<td align="left"><?php echo $checked;?></td>
					<?php }?>
					<td align="left">
						<?php 
						$inside_tag = $row->record_name ? $row->record_name : "Record";
						$link_href = "javascript:startAppPlayback('".$JLMS_CONFIG->get('live_site') . '/index.php?tmpl=component&option=com_joomla_lms&amp;task=conference&amp;mode=conference_playback&amp;Itemid='.$Itemid.'&amp;id='.$course_id.'&amp;name='.$row->session_name."');";
						echo JLMS_toolTip($overlib_title, $overlib_descr, $inside_tag, $link_href);
						?>
						<br />
						<span class="small"><?php echo _JLMS_HOME_AUTHOR . "&nbsp;" . $row->username;?></span>
					</td>
					<?php if($JLMS_ACL->CheckPermissions('conference', 'manage')){?>
					<td align="left">
					<?php echo $row->session_name;
					/*
						<a href="javascript:startAppPlayback('<?php echo sefRelToAbs('index.php?tmpl=component&option=com_joomla_lms&task=conference&mode=conference_playback&Itemid='.$Itemid.'&id='.$course_id.'&name='.$row->record_name );?>');" title="View details">
							<?php echo $row->session_name;?>
						</a>
					*/?>	
					</td>
					<?php }?>
					<td align="left"><?php echo JLMS_dateToDisplay($row->start_date);?></td>
					<?php if($JLMS_ACL->CheckPermissions('conference', 'manage')){?>
					<td align="center" valign="middle">
						<?php if($JLMS_ACL->CheckPermissions('conference', 'manage')){
							$alt = $row->published ? _JLMS_STATUS_PUB:_JLMS_STATUS_UNPUB;
							$image = ($row->published)? 'btn_accept.png' : 'btn_cancel.png';
							$state = ($row->published)?0:1;
							echo '<a href="javascript:submitbutton_change_record(\'change_record\', '.$state.', '.$row->id.')" title="'.$alt.'"><img class="JLMS_png" src="'.$JLMS_CONFIG->get('live_site').'/components/com_joomla_lms/lms_images/toolbar/'.$image.'" width="16" height="16" border="0" alt="'.$alt.'" /></a>';
						}?>
					</td>
					<?php }?>
					<td>&nbsp;</td>
					<td align="center" valign="middle">
						<a href="javascript:startAppPlayback('<?php echo $JLMS_CONFIG->get('live_site') . '/index.php?tmpl=component&option=com_joomla_lms&amp;task=conference&amp;mode=conference_playback&amp;Itemid='.$Itemid.'&amp;id='.$course_id.'&amp;name='.$row->session_name;?>');" title="<?php echo _JLMS_CONFERENCE_RECORD_PB;?>" >
							<img class="JLMS_png" src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/toolbar/btn_playback.png" width="16" height="16" border="0" alt="<?php echo _JLMS_CONFERENCE_RECORD_PB;?>" title="<?php echo _JLMS_CONFERENCE_RECORD_PB;?>" />
						</a>
					</td>
				</tr>
				<?php
				$k = 3 - $k;
			}
			?>
				<tr>
					<td class="<?php echo JLMSCSS::_('jlmslist-footer_td');?>" colspan="<?php echo (($JLMS_ACL->CheckPermissions('conference', 'manage'))?8:5);?>" align="center"><div align="center">
					<?php 
					if (count($records) == 0 ){
						echo _JLMS_CONFERENCE_NO_RECORD;
					}else{
						$link_PN = "index.php?option=$option&Itemid=$Itemid&task=conference&mode=archive&course_id=".$course_id;
						echo _JLMS_PN_DISPLAY_NUM . $pageNav->getLimitBox($link_PN)."&nbsp;".$pageNav->getPagesCounter( $link_PN ).'<br />';
						echo $pageNav->writePagesLinks( $link_PN );
					}
					?>
					</div></td>
				</tr>
			</table>
			<input type="hidden" name="task" value="conference" />
			<input type="hidden" name="mode" value="" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="id" value="<?php echo $course_id;?>" />
			<input type="hidden" name="state" value="0" />
			<input type="hidden" name="cid2" value="0" />
		</form>
<?php
		JLMS_TMPL::CloseTS();

		if ($JLMS_ACL->CheckPermissions('conference', 'manage')) {
			$controls = array();
			$controls[] = array('href' => "javascript:submitbutton_change('change_record',1);", 'title' => _JLMS_SET_PUB, 'img' => 'publish');
			$controls[] = array('href' => "javascript:submitbutton_change('change_record',0);", 'title' => _JLMS_SET_UNPUB, 'img' => 'unpublish');
			$controls[] = array('href' => 'spacer');
			$controls[] = array('href' => "javascript:submitbutton('record_delete');", 'title' => _JLMS_DELETE, 'img' => 'delete');
			$controls[] = array('href' => "javascript:submitbutton('edit_record');", 'title' => _JLMS_EDIT, 'img' => 'edit');
			JLMS_TMPL::ShowControlsFooter($controls, sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid&amp;task=conference&amp;id=$course_id"));
		}

		JLMS_TMPL::CloseMT();
	}

	function jlms_conference_playback( $course_id, $option, $records ){
		global $Itemid,$my, $JLMS_CONFIG;
		$doc = & JFactory::getDocument();
		
		$course_id = intval(mosGetParam($_REQUEST, 'id', 0));		
		
		$doc->setTitle('Conference playback');
		
		$flashcomroot = $JLMS_CONFIG->get('flascommRoot');
		$webRoot =  $JLMS_CONFIG->get('live_site');//$JLMS_CONFIG->get('flascommRoot');
		$streamName = mosGetParam($_REQUEST, 'name', '');
		$globBg 	= $JLMS_CONFIG->get('conf_background');
		$mainBg 	= $JLMS_CONFIG->get('conf_main_color');
		$borderCl 	= $JLMS_CONFIG->get('conf_border_color');
		$titleBg 	= $JLMS_CONFIG->get('conf_title_color');
		//include( _JOOMLMS_FRONT_HOME."/includes/flashfix.inc.php" );
		//flashfix_init("components/com_joomla_lms/includes/js" );
		
	?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-gb" lang="en-gb" dir="ltr">
	<head>
	<script type="text/javascript" src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/includes/js/swfobject.js"></script>
	</head>
<body style="margin:0px; padding:0px">
		<script language="javascript" type="text/javascript">
		<!--//

			function openProfile(name)
			{
				var y= (screen.height/2) -75;
				var x= (screen.width/2) -200;
				<?php if ($JLMS_CONFIG->get('is_cb_installed')){
					?>
					window.open("<?php echo sefRelToAbs('index.php?option=com_joomla_lms&Itemid='.$Itemid.'&course_id='.$course_id.'&task=conference&mode=cb_profile&username=\'+name+\'');?>");
					<?php
				}else{?>
					window.open("<?php echo $JLMS_CONFIG->get('live_site') . '/index.php?tmpl=component&option=com_joomla_lms&Itemid='.$Itemid.'&course_id='.$course_id.'&task=conference&mode=profile&username=\'+name+\'';?>",'TEST','top='+y+', left='+x+', width=400,height=150, status=no, directories=no, toolbar=no, location=no, menubar=no, scrollbars=no, resizable=no');
				<?php }?>
			}
				
			
			function closeApp() {
				//top.location.href="<?php echo sefRelToAbs('index.php?option=com_joomla_lms&Itemid='.$Itemid.'&id='.$course_id.'&task=details_course');?>";
				window.close();
			}
			function getObj(name) {
				if (document.getElementById) { return document.getElementById(name); }
				else if (document.all) { return document.all[name]; }
				else if (document.layers) { return document.layers[name]; }
			}
			function errorFromServer(msg){
				alert(msg);
				closeApp();
			}
			function display_div(){
				//getObj('conference_center').style.display = 'block';
				//getObj('conference_bottom').style.display = 'block';
			}
			//-->
		</script>

		<script type="text/javascript" language="javascript">
			<!--//
			function click_change(){
				upload();
			}
			//function dlia podderganiia Sessii
			
			function jlms_conference_timer(){
				getObj('frame_reload').src = '<?php echo $JLMS_CONFIG->get('live_site') . '/index.php?tmpl=component&no_html=1'; ?>';
				setTimeout ("jlms_conference_timer()", 300000);
			}
			setTimeout ("jlms_conference_timer()", 300000);
			//-->
		</script>
		<table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:<?php echo $globBg;?>;height:100%">
			<tr>
				
				<td style="text-align:center; width:100%;height:640px; background:<?php echo $globBg;?>;" align="center">
					<div style="float:left;width:990px;text-align:right;margin:0px;" id="leftbar_pb">
					<?php $params = "course_id=".$course_id."&amp;streamName=".$streamName;?>
					<script type="text/javascript">
						// <![CDATA[
						var so = new SWFObject("<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/includes/conference_playback/conference_playback_106.swf?<?php echo $params;?>", "leftbar_pb", "990", "641", "8", "<?php echo $globBg;?>");
						so.addVariable("allowScriptAccess", "sameDomain"); // this line is optional, but this example uses the variable and displays this text inside the flash movie
						so.addVariable("wmode", "transparent"); 
						so.addVariable("flashvars", "hello there"); 
						so.addVariable("salign", "t"); 
						so.addVariable("menu", "false");
						so.write("leftbar_pb");
						// ]]>
					</script>
					</div>
					
					<iframe name="mainFrame" id="mainFrame" width="50" height="50" frameborder="0" scrolling="no"  style=" position:absolute; left:-600px; top:-600px " ></iframe>
					<iframe name="frame_reload" id="frame_reload" width="50" height="50" frameborder="0" scrolling="no"  style=" position:absolute; left:-600px; top:-600px " ></iframe>
				
				</td>
			</tr>
		</table>
</body>
</html>
		<?php
		die();
	}
	
	
	function jlms_conference_room($usertype, $option){
		global $Itemid,$my,$JLMS_CONFIG;
		
		$doc = & JFactory::getDocument(); 
		
		$course_id = intval(mosGetParam($_REQUEST, 'course_id', 0));
		$recorded_session = mosGetParam($_REQUEST, 'recorded_session', '');
		//echo $recorded_session;
		$doc->setTitle('Conference');
		
		$flashcomroot = $JLMS_CONFIG->get('flascommRoot');
		$webRoot =  $JLMS_CONFIG->get('live_site');//$JLMS_CONFIG->get('flascommRoot');
		
		$globBg 	= $JLMS_CONFIG->get('conf_background');
		$mainBg 	= $JLMS_CONFIG->get('conf_main_color');
		$borderCl 	= $JLMS_CONFIG->get('conf_border_color');
		$titleBg 	= $JLMS_CONFIG->get('conf_title_color');

		if ($usertype == 1){
			$master = 'yes';
		}
		elseif($usertype == 2){
			$master = 'no';
		}?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-gb" lang="en-gb" dir="ltr">
	<head>
	<script type="text/javascript" src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/includes/js/swfobject.js"></script>
	</head>
<body style="margin:0px; padding:0px">

		<script language="javascript" type="text/javascript">
			<!--//
				function openProfile(name)
				{
					var y= (screen.height/2) -75;
					var x= (screen.width/2) -200;
					<?php if ($JLMS_CONFIG->get('is_cb_installed')){
						?>
						window.open("<?php echo $JLMS_CONFIG->get('live_site') . '/index.php?option=com_joomla_lms&Itemid='.$Itemid.'&course_id='.$course_id.'&task=conference&mode=cb_profile&username=\'+name+\'';?>");
						<?php
					}else{?>
						window.open("<?php echo $JLMS_CONFIG->get('live_site') . '/index.php?tmpl=component&option=com_joomla_lms&Itemid='.$Itemid.'&course_id='.$course_id.'&task=conference&mode=profile&username=\'+name+\'';?>",'TEST','top='+y+', left='+x+', width=400,height=150, status=no, directories=no, toolbar=no, location=no, menubar=no, scrollbars=no, resizable=no');
					<?php }?>
				}
				
				function closeApp() {
					<?php /*//top.location.href="<?php echo sefRelToAbs('index.php?option=com_joomla_lms&Itemid='.$Itemid.'&id='.$course_id.'&task=details_course');?>";*/?>
					window.close();
				}
				function getObj(name) {
					if (document.getElementById) { return document.getElementById(name); }
					else if (document.all) { return document.all[name]; }
					else if (document.layers) { return document.layers[name]; }
				}
				function errorFromServer(msg){
					alert(msg);
					closeApp();
				}
				function jq_AnalizeRequest(http_request)
				{
					
					if (http_request.readyState == 4) {
					if ((http_request.status == 200)) {
						//alert(http_request.responseText);
						response  = http_request.responseXML.documentElement;
						var parambook = response.getElementsByTagName('parambook')[0].firstChild.data;
						//alert(parambook);
						
						if('<?php echo $usertype?>' != "1" && parambook != 1)
						{
							alert("The conference is closed.");
							closeApp();
						}
					}
					}
				}
				function jq_MakeRequest(url) {
					
					var http_request = false;
					if (window.ActiveXObject) { // IE
						try { http_request = new ActiveXObject("Msxml2.XMLHTTP");
						} catch (e) {
							try { http_request = new ActiveXObject("Microsoft.XMLHTTP");
							} catch (e) {}
						}
					} else if (window.XMLHttpRequest) { // Mozilla, Safari,...
						http_request = new XMLHttpRequest();
						if (http_request.overrideMimeType) {
							http_request.overrideMimeType('text/xml');
						}
					}
					if (!http_request) {
						//alert("4");
						return false;
					}
					http_request.onreadystatechange = function() { jq_AnalizeRequest(http_request); };
					http_request.open('GET', url.replace(/amp;/g,''), true);
					http_request.send(null);
					setTimeout ("jq_MakeRequest('<?php echo $JLMS_CONFIG->get('live_site') . '/index.php?tmpl=component&option=com_joomla_lms&task=conference&mode=param_request&course_id='.$course_id;?>')", 60000);
					
				}
				
				jq_MakeRequest("<?php echo $JLMS_CONFIG->get('live_site') . '/index.php?tmpl=component&option=com_joomla_lms&task=conference&mode=param_request&course_id='.$course_id;?>");
			
				//-->
			</script>
<?php
$upload_height = 70;
if ($JLMS_CONFIG->get('is_trial')) {
	$upload_height = 135;
}
?>
			<script type="text/javascript" language="javascript">
			<!--
			var IE5 = (document.all && parseFloat(navigator.appVersion.split("MSIE")[1])>=5.5)? true : false;
			var IE6 = (document.all && parseFloat(navigator.appVersion.split("MSIE")[1])>=6)? true : false;
			var Saf = (navigator.userAgent.indexOf('Safari') != -1)? true: false;
			var NS = (!Saf && document.getElementById && navigator.appName.indexOf("Netscape")>=0)? true: false;
			var NS7 = (NS && parseFloat(navigator.appVersion)>=5)? true: false;
			var Moz = (NS && navigator.userAgent.indexOf("Netscape")<0)? true: false;
			var FireFox = (navigator.userAgent.indexOf("Firefox")!=-1)  ? true:false;
			var F_B = (Moz && navigator.userAgent.indexOf('Firebird') != -1)? true: false;
			var mov = ((!document.all || IE5) && (!NS || NS7 || Moz))? true: false;
			var ec = 2;
			var btx; var bty; var btx2; var bty2;
			
			function coord(btn_x, btn_y, btn_w, btn_h)
			{
				f_form = getObj("jlms_form_upload");
				parc = getObj("fichier");
				swf = getObj("upload");
				if(IE5 && !IE6){
					ec = 5;
				}
				btx = btn_x+swf.offsetLeft;
				bty = btn_y+swf.offsetTop;
				btx2 = btx+btn_w + ec;
				bty2 = bty+btn_h + ec;
			}
		
			function move(xy){
				f_form = getObj("jlms_form_upload");
				parc = getObj("fichier");
				swf = getObj("upload");
				if(IE5){
					x = event.x+document.body.scrollLeft;
					y = event.y+document.body.scrollTop;
				}
				else{
					x = xy.pageX;
					y = xy.pageY;
				}
				if(x>btx && x<btx2 && y>bty && y<bty2)
				{
					if(IE5)swf.SetVariable('btn_over', 1);
					parc.style.cursor = "hand";
					f_form.style.left=x-130;
					f_form.style.top=y-15;
				}
				else{
					if(IE5)swf.SetVariable('btn_over', 0);
					f_form.style.left=-500;
					f_form.style.top=-500;
				}
				//document.write("wwwwww");
			}
			function popup (x,y){
				pop1 = window.open('<?php echo $JLMS_CONFIG->get('live_site') . '/index.php?tmpl=component&option=com_joomla_lms&task=conference&mode=upload_popup&course_id='.$course_id;?>','nom_de_la_fenetre','width=310, height=<?php echo $upload_height;?>, left='+x+', top='+y+', scrollbars=no, toolbar=no, resizable=yes'); pop1.focus();
			}
			function upload(){
				f_form.submit();
			}
			
			function flash_roll(){
				f_form.style.left=btx;
				f_form.style.top=bty;
			}
			
			function display_div(){
				//getObj('conference_center').style.display = 'block';
				//getObj('conference_bottom').style.display = 'block';
			}
			function click_change(){
				upload();
			}
			//function dlia podderganiia Sessii
			
			function jlms_conference_timer(){
				getObj('frame_reload').src = '<?php echo $JLMS_CONFIG->get('live_site') . '/index.php?tmpl=component&no_html=1';?>';
				setTimeout ("jlms_conference_timer()", 300000);
			}
			setTimeout ("jlms_conference_timer()", 300000);
			
			//-->
			</script>
			
		<table width="100%" cellpadding="0" cellspacing="0" border="0" style="min-width:1000px;height:100%;background-color:<?php echo $globBg;?>; ">
			<tr>
				<td style="text-align:center; width:100%;height:638px; background:<?php echo $globBg;?>;" align="center">
					<div id="leftBar" style="float:left;width:990px;  overflow:hidden;text-align:right;margin:0px;">
					<strong><?php echo _JLMS_JS_FLASH_REQUIRES;?></strong>
					<?php $params = "course_id=".$course_id."&amp;recorded_session=".$recorded_session;	?>
					<script type="text/javascript">
						// <![CDATA[
						var so = new SWFObject("<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/includes/conference/conference_106.swf?<?php echo $params;?>", "leftbar", "990", "643", "8", "<?php echo $globBg;?>");
						so.addVariable("allowScriptAccess", "sameDomain"); // this line is optional, but this example uses the variable and displays this text inside the flash movie
						so.addVariable("wmode", "transparent"); 
						so.addVariable("flashvars", "hello there"); 
						so.addVariable("salign", "t"); 
						so.addVariable("menu", "false");
						so.write("leftBar");
						// ]]>
					</script>
					
					</div>
					<div style="clear:both "></div>
					<iframe name="mainFrame" id="mainFrame" width="50" height="50" frameborder="0" scrolling="no"  style=" position:absolute; left:-600px; top:-600px " ></iframe>
					<iframe name="frame_reload" id="frame_reload" width="50" height="50" frameborder="0" scrolling="no"  style=" position:absolute; left:-600px; top:-600px " ></iframe>
				
					<script type="text/javascript" language="javascript">
					<!--
					if(Moz) act_file = "onClick";
					else act_file = "onChange";
						document.write("<div style='display:none'><form id='jlms_upload_form' enctype='multipart/form-data' method='post' action='<?php echo $JLMS_CONFIG->get('live_site') . '/index.php?tmpl=component&option=com_joomla_lms&Itemid='.$Itemid.'&task=conference&mode=upload';?>' target='frame_upload' style='z-index: 5;  filter: alpha(opacity=0); opacity: 0; -moz-opacity: 0;'>");
						document.write("<input type='file' name='fichier' id='fichier' size='10' " + act_file + " = 'javascript:click_change();' \/>");
						document.write("<\/form><\/div>");
						//-->
					</script>
					
					<iframe name="frame_upload" width="50" height="50" frameborder="0" scrolling="no" style="position:absolute; left:-600px; top:-600px; "></iframe>
				</td>
			</tr>
		</table>
<?php
if ($JLMS_CONFIG->get('is_trial')) {
	require_once(_JOOMLMS_FRONT_HOME . DS . "joomla_lms.branding.php");
	echo '<div style="font-family: tahoma; font-size: 11px; line-height: 12px; margin-top:5px">';
	JLMS_showPoweredBy(true);
	echo '</div>';
}
?>
</body>
</html>
		<?php
		die();
	}
	
	//function otrisovivaet formu popupa dlia uploada
	function jlms_conference_upload_popup($option, $course_id){
	global $my,$Itemid;
	
	$doc = & JFactory::getDocument();	
	$doc->setTitle('Upload a file');
	
	?>
		<link rel="stylesheet" href="administrator/templates/joomla_admin/css/template_css.css" type="text/css" />
		<form method="post" action='<?php echo sefRelToAbs("index.php?tmpl=component&option=$option&amp;Itemid=$Itemid");?>' target='frame_upload' enctype="multipart/form-data" name="jlms_upload_form">
		
		<table cellpadding="0" cellspacing="0" border="0" width="100%">
		<tr>
			<th class="title">
				File Upload : conference	
			</th>
		</tr>
		<tr>
			<td align="center">
				<input class="inputbox" name="fichier" type="file" id='fichier' />
			</td>
		</tr>
		<tr>
			<td>
				<input class="button" type="submit" value="Upload" name="up" />
				Max size = <?php echo "2Mb";?>	
			</td>
		</tr>
		</table>
		<input type="hidden" name="task" value="conference" />
		<input type="hidden" name="mode" value="upload" />
		<input type="hidden" name="course_id" value="<?php echo $course_id;?>" />
		<input type="hidden" name="user_id" value="<?php echo $my->id;?>" />
		</form>

	<?php	
	}
	//end function

	//vspomogatelnaia function return all files to this course
	function getFilesFromDB($course_id ){
		global $Itemid, $my, $JLMS_DB;
		//$pseudo = strtoupper($login);
		$query = "SELECT filename FROM `#__lms_conference_doc` WHERE course_id = $course_id ORDER BY doc_id";
		$JLMS_DB -> setQuery($query);
		$files = $JLMS_DB->loadObjectList();
		
		$i = 0;
		$files_list = '';
		foreach ($files as $file){
			if (file_exists(_JOOMLMS_FRONT_HOME."/upload/".$file->filename)){
				$files_list .= "&amp;arg".$i."=". urlencode($file->filename);
				$i++;
			}
		}
		return $files_list;
	}


	function jlms_booking_list($course_id, $option, &$rows, $pageNav, &$lists)
	{
		global $Itemid, $my, $JLMS_DB, $JLMS_CONFIG;

		JLMS_TMPL::OpenMT();
	
		$params = array(
			'show_menu' => true
		);
		JLMS_TMPL::ShowHeader('conference', _JLMS_HEAD_CONF_STR, $params);
	
		JLMS_TMPL::OpenTS();
?>
		
	<script type="text/javascript" language="javascript">
	<!--//--><![CDATA[//><!--
	function submitbutton(pressbutton) {
		var form = document.adminForm;
		if (pressbutton == 'new_period')
		{
			form.mode.value = pressbutton;
			form.submit();
		}
		else
		{
			if ( form.boxchecked.value == "0" ){
				alert("<?php echo _JLMS_ALERT_SELECT_ITEM;?>");
			} else {
				if (pressbutton == 'period_delete'){
					if (confirm("<?php echo 'Are you sure?';?>") == true){
						form.mode.value = pressbutton;
						form.submit();
					}
				}else{
					form.mode.value = pressbutton;
					form.submit();
				}
			}
		}	
	}
	//--><!]]>
	</script>
	<form action="<?php echo sefRelToAbs("index.php?option=$option&Itemid=$Itemid");?>" method="post" name="adminForm">
		<table width="100%" cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td colspan="2">
					<?php 
					$link_PN = "index.php?option=$option&Itemid=$Itemid&task=conference&mode=booking&course_id=".$course_id;
					echo $pageNav->getLimitBox($link_PN)."&nbsp;".$pageNav->getPagesCounter( $link_PN );
					echo '. Filter: '.$lists['filter_teach'].$lists['filter_stu'];
					?>
				</td>
				<td style="text-align:right;"><br />
					<?php $toolbar = array();
					//$toolbar[] = array('btn_type' => 'edit', 'btn_js' => sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid&amp;task=conference&amp;id=$course_id&amp;mode=archive") );
					$toolbar[] = array('btn_type' => 'back', 'btn_js' => sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid&amp;task=conference&amp;id=$course_id") );
					echo JLMS_ShowToolbar($toolbar); ?>
				</td>
			</tr>
		</table>
<?php
		/*JLMS_TMPL::CloseTS();
		JLMS_TMPL::OpenTS();*/
?>
		
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td class="sectiontableheader" align="left" width="15">#</td>

					<td class="sectiontableheader" align="left" width="10"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></td>
					<td class="sectiontableheader" align="left"><?php echo "Name";?></td>
					<td class="sectiontableheader" align="left"><?php echo "Teacher";?></td>
					<td class="sectiontableheader" nowrap="nowrap" align="left"><?php echo "Time";?></td>

					<td class="sectiontableheader" align="left"><?php echo "Date";?></td>

					<td class="sectiontableheader" align="left"><?php echo "Access";?></td>

					
					
				</tr>
			<?php
			$k = 1;		
				
			for ($i=0, $n=count($rows); $i < $n; $i++) {
				$row = $rows[$i];
				
				//$link 	= sefRelToAbs("index.php?option=".$option."&Itemid=".$Itemid."&task=details_course&id=". $row->course_id);
				$checked = mosHTML::idBox( $i, $row->p_id);
				$overlib_descr = JLMS_txt2overlib($row->p_description);
				$overlib_title = "Description";
				?>
				<tr valign="middle" style="vertical-align:middle" class="<?php echo "sectiontableentry$k"; ?>">
					<td align="left"><?php echo ( $pageNav->limitstart + $i + 1 );?></td>
					<td align="left"><?php echo $checked;?></td>
					<td align="left">
						<?php 
						$inside_tag = $row->p_name ? $row->p_name : "";
						echo JLMS_toolTip($overlib_title, $overlib_descr, $inside_tag);
						?> 
					</td>
					<td align="left">
						<?php
						if ($row->user_teacher_id) {
							echo $row->name;
						} else {
							echo 'Not selected';
						}
						?>
					</td>
					<td align="left">
						<?php 
						echo  date("H:i",$row->from_time)." - ".date("H:i",$row->to_time);
						?>
					</td>
					<td align="left">
					<?php echo date("Y-m-d",$row->from_time);
					/*
						<a href="javascript:startAppPlayback('<?php echo sefRelToAbs('index.php?tmpl=component&option=com_joomla_lms&task=conference&mode=conference_playback&Itemid='.$Itemid.'&id='.$course_id.'&name='.$row->record_name );?>');" title="View details">
							<?php echo $row->session_name;?>
						</a>
					*/?>	
					</td>
					<td align="left">
						<?php 
							$query = "SELECT u.username FROM #__lms_conference_usr as cu, #__users as u WHERE cu.p_id = '".$row->p_id."' AND cu.user_id= u.id";
							$JLMS_DB->setQuery($query);
							$cur_users = $JLMS_DB->LoadResultArray();
							
							$cur_users = implode(',',$cur_users);						
							
							
							$ulink = sefRelToAbs('index.php?option=com_joomla_lms&task=conference&mode=user_access&Itemid='.$Itemid.'&course_id='.$course_id.'&amp;pid='.$row->p_id);
							echo $row->public?'Public':'<a href="'.$ulink.'">'.($cur_users?$cur_users:"Specify user").'</a>';
						?>
					</td>
				</tr>
				<?php
				$k = 3 - $k;
			}
			?>
				<tr>
					<td colspan="7" align="center">	
						<?php 
						if (count($rows) == 0 ){
							echo _JLMS_CONFERENCE_NO_RECORD;
						}else{
							echo $pageNav->writePagesLinks( $link_PN );
						}
						?>
					</td>	
				</tr>
			</table>
			<input type="hidden" name="task" value="conference" />
			<input type="hidden" name="mode" value="booking" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="id" value="<?php echo $course_id;?>" />
			<input type="hidden" name="state" value="0" />
		</form>
<?php
		JLMS_TMPL::CloseTS();
		$controls = array();
		$controls[] = array('href' => "javascript:submitbutton('period_delete');", 'title' => _JLMS_DELETE, 'img' => 'delete');
		$controls[] = array('href' => "javascript:submitbutton('edit_period');", 'title' => _JLMS_EDIT, 'img' => 'edit');
		$controls[] = array('href' => "javascript:submitbutton('new_period');", 'title' => 'New', 'img' => 'add');
		JLMS_TMPL::ShowControlsFooter($controls, sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid&amp;task=conference&amp;id=$course_id"));

		JLMS_TMPL::CloseMT();
	
	}
	function jlms_booking_edit($course_id, $option, &$rows, &$lists, $en_book)
	{
		global $Itemid, $my, $JLMS_DB, $JLMS_CONFIG;

		JLMS_TMPL::OpenMT();
	
		$params = array(
			'show_menu' => true
		);
		JLMS_TMPL::ShowHeader('conference', _JLMS_HEAD_CONF_STR, $params);
	
		JLMS_TMPL::OpenTS();
		$row = $rows[0];
?>
	<script type="text/javascript" language="javascript">
	<!--//--><![CDATA[//><!--
	function submitbutton(pressbutton) {
		var form = document.adminForm;
		if (pressbutton == 'period_cancel'){
			form.mode.value = pressbutton;
			form.submit();
		}
		else if (pressbutton == 'save_period'){
			if(form.p_name.value == '')
			{
				alert("Specify name");
			}
			else
			if(form.sel_option[1].checked)
			{
			
			var w_sel = 0;
			var m_sel = 0;	
			   var element = eval(document.adminForm['weekday[]']);
			   for (var i=0; i<element.length; i++) {
					var o = element[i];
					 if(o.checked)
						w_sel = 1;
				}
				var element2 = eval(document.adminForm['monthday[]']);
			   for (var i=0; i<element2.length; i++) {
					var o = element2[i];
					 if(o.checked)
						m_sel = 1;
				}
		
				if(m_sel && w_sel)
				{
					form.mode.value = pressbutton;
					form.submit();
				}
				else
				{
					alert("Please Specify week day and month");
				}
			}
			else
			{
				form.mode.value = pressbutton;
				form.submit();
			}
		}	
	}
	function getObj(name) {
				if (document.getElementById) { return document.getElementById(name); }
				else if (document.all) { return document.all[name]; }
				else if (document.layers) { return document.layers[name]; }
			}
	function Choose_option(opt)
	{
		if (opt)
		{
			getObj("sel_date").style.display = "block";
			getObj("sel_period").style.display = "none";
		}
		else
		{
			getObj("sel_period").style.display = "block";
			getObj("sel_date").style.display = "none";
		}
	}
	//--><!]]>
	</script>
		<table width="100%" cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td colspan="2">&nbsp;
					
				</td>
				<td style="text-align:right;"><br />
					<?php $toolbar = array();
					$toolbar[] = array('btn_type' => 'save', 'btn_js' => "javascript:submitbutton('save_period');" );
					$toolbar[] = array('btn_type' => 'cancel', 'btn_js' => sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid&amp;task=conference&amp;mode=booking&amp;id=$course_id") );
					echo JLMS_ShowToolbar($toolbar); ?>
				</td>
			</tr>
		</table>
<?php
		JLMS_TMPL::CloseTS();
		JLMS_TMPL::OpenTS();
?>
		<form action="<?php echo sefRelToAbs("index.php?option=$option&Itemid=$Itemid");?>" method="post" name="adminForm">
			<table cellpadding="0" cellspacing="0" border="0" width="100%">
				<tr>
					<td class="sectiontableheader">
						Main options
					</td>
				</tr>
			</table>
			<table cellpadding="2" cellspacing="0" border="0" width="100%">
				<tr>
					<td width="170">
						Name
					</td>
					<td>
						<input type="text" name="p_name" maxlenght="100" value="<?php echo $row->p_name;?>" />
					</td>
				</tr>
				<tr>
					<td width="170">
						Professor
					</td>
					<td>
						<?php echo $lists['teacher_id'];?>
					</td>
				</tr>
				<tr>
					<td>
						Description
					</td>
					<td>
						<textarea name="p_description" cols="40" rows="7"><?php echo $row->p_description;?></textarea>
					</td>
				</tr>
			</table>
			<table  cellpadding="1" cellspacing="0" border="0" width="100%">
				<tr>
					<td width="170"> Start Time </td>
					<td>	
						<table>
							<tr>		
								<td>
									<?php echo $lists['from_time']?>
								</td>
								<td>
									<?php echo $lists['from_minutes']?>
								</td>
							</tr>
						</table>
					</td>			
				</tr>
				<tr>	
					<td width="170"> End Time </td>
					<td>	
						<table>
							<tr>			
								<td>
									<?php echo $lists['to_time']?>
								</td>
								<td>
									<?php echo $lists['to_minutes']?>
								</td>
							</tr>
						</table>
					</td>		
				</tr>
			</table>

			<table cellpadding="1" <?php if($row->p_id) echo 'style="display:none;"';?> cellspacing="0" border="0" width="100%">
				<tr>
					<td width="170">
						Select Date
					</td>
					<td>
						<input type="radio" name="sel_option" checked value="0" onchange="Choose_option(1);" />
					</td>
				</tr>
				<tr>
					<td>
						Select period
					</td>
					<td>
						<input type="radio" name="sel_option" value="1" onchange="Choose_option(0);" />
					</td>
				</tr>
			</table>
			<table  cellpadding="1" cellspacing="0" border="0" id="sel_date" width="100%">
				<tr>		
					<td width="170"> Choose Date </td>
					<td  valign="middle" style="vertical-align:middle ">
						<?php echo JLMS_HTML::_('calendar.calendar',$row->cur_date,'start','start'); ?>
					</td>
				</tr>
			</table>
			<table  cellpadding="1" cellspacing="0" border="0" id="sel_period" style="display:none;" width="100%">	
				<tr>
					<td width="170"> Select days of week </td>
					<td valign="top">
						
							<?php
								$weekday = array("Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday");
								for($i=0;$i<7;$i++)
								{
									echo '<br /><input type="checkbox" name="weekday[]"  value="'.$i.'" />'.$weekday[$i];
								}	
							?>
						
					</td>
				</tr>	
				<tr>
					<td width="170"> Select months </td>	
					<td valign="top">

							<?php
			
							for($i=0;$i<7;$i++)
								{
									$month_num = date("m_Y",mktime(0,0,0,date("m")+$i,1,date("Y")));
									$month_text = date("F Y",mktime(0,0,0,date("m")+$i,1,date("Y")));
									echo '<br /><input type="checkbox" name="monthday[]" value="'.$month_num.'" />'.$month_text;
								}
							?>
						
					</td>
					
				</tr>
			</table>
			<table  cellpadding="1" cellspacing="0" border="0" width="100%">				
				<tr>
					<td width="170">Public</td>
					<td>
						<?php echo mosHTML::yesnoRadioList( 'c_public', 'class="inputbox" ', $row->public);?>
					</td>
				</tr>
			</table>
				
			</table>
			<input type="hidden" name="task" value="conference" />
			<input type="hidden" name="mode" value="" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="p_id" value="<?php echo $row->p_id;?>" />
			<input type="hidden" name="id" value="<?php echo $course_id;?>" />
			<input type="hidden" name="state" value="0" />
		</form>
<?php
		JLMS_TMPL::CloseTS();
		/*$controls = array();
		$controls[] = array('href' => "javascript:submitbutton('period_cancel');", 'title' => _JLMS_CANCEL_ALT_TITLE, 'img' => 'cancel');
		$controls[] = array('href' => "javascript:submitbutton('save_period');", 'title' => _JLMS_SAVE_ALT_TITLE, 'img' => 'save');
		
		JLMS_TMPL::ShowControlsFooter($controls, sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid&amp;task=conference&amp;mode=booking&amp;id=$course_id"));
*/
		JLMS_TMPL::CloseMT();
	
	}
	function jlms_booking_users( $course_id, $option, $lists, $pid, $rows )
	{
		global $Itemid, $my, $JLMS_DB;

		JLMS_TMPL::OpenMT();
	
		$params = array(
			'show_menu' => true
		);
		JLMS_TMPL::ShowHeader('conference', _JLMS_HEAD_CONF_STR, $params);
	
		JLMS_TMPL::OpenTS();

?>
	<script type="text/javascript" language="javascript">
	<!--//--><![CDATA[//><!--
	function submitbutton(pressbutton) {
		var form = document.adminForm;

			form.mode.value = pressbutton;
			form.submit();
		
	}
	//--><!]]>
	</script>
		<table width="100%" cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td colspan="2">&nbsp;
					
				</td>
				<td style="text-align:right;"><br />
					<?php $toolbar = array();
					//$toolbar[] = array('btn_type' => 'edit', 'btn_js' => sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid&amp;task=conference&amp;id=$course_id&amp;mode=archive") );
					$toolbar[] = array('btn_type' => 'save', 'btn_js' => "javascript:submitbutton('save_users');" );
					$toolbar[] = array('btn_type' => 'cancel', 'btn_js' => sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid&amp;task=conference&amp;mode=booking&amp;id=$course_id") );
					//echo JLMS_ShowToolbar($toolbar); ?>
				</td>
			</tr>
		</table>
<?php
		JLMS_TMPL::CloseTS();
		JLMS_TMPL::OpenTS();

?>
		<form action="<?php echo sefRelToAbs("index.php?option=$option&Itemid=$Itemid");?>" method="post" name="adminForm">
			<table cellpadding="0" cellspacing="0" border="0" width="100%">
				<tr>
					<td width="20%">
						Select user
					</td>
					<td>
						<?php echo $lists['usrs'];?>
					</td>
				</tr>
			</table>
			
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td class="sectiontableheader" align="left" width="15">#</td>

					<td class="sectiontableheader" align="left" width="10"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></td>
					<td class="sectiontableheader" align="left"><?php echo "Username";?></td>
				</tr>
			<?php
			$k = 1;			
			for ($i=0, $n=count($rows); $i < $n; $i++) {
				$row = $rows[$i];
				$checked = mosHTML::idBox( $i, $row->user_id);
				?>			
				<tr valign="middle" style="vertical-align:middle" class="<?php echo "sectiontableentry$k"; ?>">
					<td align="left"><?php echo (  $i + 1 );?></td>
					<td align="left"><?php echo $checked;?></td>
					<td align="left">
						<?php 
						echo $row->name."(".$row->username.")";
						?> 
					</td>
				</tr>
				<?php
				$k = 3 - $k;
			}
			?>
			</table>
			<input type="hidden" name="task" value="conference" />
			<input type="hidden" name="mode" value="" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="p_id" value="<?php echo $pid;?>" />
			<input type="hidden" name="id" value="<?php echo $course_id;?>" />
			<input type="hidden" name="state" value="0" />
		</form>
		<?php
		JLMS_TMPL::CloseTS();
		$controls = array();
		$controls[] = array('href' => "javascript:submitbutton('save_users');", 'title' => 'Save', 'img' => 'save');
		$controls[] = array('href' => "javascript:submitbutton('delete_users_from_conference');", 'title' => _JLMS_DELETE, 'img' => 'delete');
		JLMS_TMPL::ShowControlsFooter($controls, sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid&amp;task=conference&amp;mode=booking&amp;id=$course_id"));

		/*$controls = array();
		$controls[] = array('href' => "javascript:submitbutton('period_cancel');", 'title' => _JLMS_CANCEL_ALT_TITLE, 'img' => 'cancel');
		$controls[] = array('href' => "javascript:submitbutton('save_users');", 'title' => _JLMS_SAVE_ALT_TITLE, 'img' => 'save');
		
		JLMS_TMPL::ShowControlsFooter($controls, sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid&amp;task=conference&amp;mode=booking&amp;id=$course_id"));
*/
		JLMS_TMPL::CloseMT();
	
	}
	
}	
?>