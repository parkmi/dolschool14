<?php
/**
* includes/lms_menu.php
* JoomaLMS eLearning Software http://www.joomlalms.com/
* * * (c) ElearningForce Inc - http://www.elearningforce.biz/
**/

// no direct access
defined( '_JLMS_EXEC' ) or die( 'Restricted access' );


function JLMS_showTopMenu_simple($option) {
	global $Itemid, $JLMS_SESSION, $JLMS_CONFIG, $JLMS_LANGUAGE;
	$back_status = $JLMS_SESSION->has('jlms_section')?$JLMS_SESSION->get('jlms_section'):'&nbsp;';
	
	$menus = $JLMS_CONFIG->get('jlms_menu');
	
	JLMS_require_lang($JLMS_LANGUAGE, 'main.lang', $JLMS_CONFIG->get('default_language'));

	$imh = '16';//$JLMS_CONFIG->get('top_menu_type');
	$imp = 'toolbar';
	if (!$JLMS_CONFIG->get('lofe_menu_style', 1)) {
		$imp = 'toolbar';$imh = '16';
	} elseif ($JLMS_CONFIG->get('lofe_menu_style', 1) == 1) {
		$imp = 'toolbar_24';$imh = '24';
	} elseif ($JLMS_CONFIG->get('lofe_menu_style', 1) == 2) {
		$imp = 'toolbar_32';$imh = '32';
	}

	$hide_gqp = true;
	if ($JLMS_CONFIG->get('global_quest_pool', 0)) {
		$JLMS_ACL = & JLMSFactory::getACL();
		$hide_gqp = !$JLMS_ACL->isTeacher();
	}

	foreach ($menus as $menu){
		$disabled = 0;
		if (isset($menu->disabled) && $menu->disabled) { //Max dobavil, etoj proverki ne bilo
		} else {
			if ($menu->is_separator) {
				echo '<img src="'.$JLMS_CONFIG->get('live_site').'/components/com_joomla_lms/lms_images/spacer.png" border="0" width="2" height="16" style="background-color:#666666 " alt=" " />';
			} elseif ($menu->lang_var && $menu->lang_var == '_JLMS_TOOLBAR_GQP_PARENT' && $hide_gqp) {

			} else {
				$lang_var_menu = '';
				if ($menu->lang_var && defined($menu->lang_var)) {
					$lang_var_menu = constant($menu->lang_var);
				} elseif (isset($JLMS_LANGUAGE[$menu->lang_var]) && $JLMS_LANGUAGE[$menu->lang_var]) {
					$lang_var_menu = $JLMS_LANGUAGE[$menu->lang_var];
				}
				echo "<a $menu->target class='jlms_menu_control' href='".$menu->menulink."' title='".$lang_var_menu."'><img class='JLMS_png' src=\"".$JLMS_CONFIG->get('live_site')."/components/com_joomla_lms/lms_images/".$imp."/".$menu->image."\" border='0' width='".$imh."' height='".$imh."' alt='".$lang_var_menu."' title='".$lang_var_menu."' /></a>&nbsp;";
			}
		}
	}	
}
/*	function JLMS_showTopMenu() 11.10.2006 
	  * $id		-	course_id
	  * $option	-	option
	  * $with_back - if 'true' - with back link as last item in menu
	  * $back_link - href for bak link			*/
function JLMS_showTopMenu( $id, $option, $with_back = false, $back_link = '', $help_task = '', $gqp = false ) { 
	global $my, $Itemid, $JLMS_SESSION, $JLMS_CONFIG, $JLMS_LANGUAGE;
	$back_status = $JLMS_SESSION->has('jlms_section')?$JLMS_SESSION->get('jlms_section'):'&nbsp;';
	
	//$back_status = $gqp?(_JLMS_TOOLBAR_GQP_PARENT):$back_status;
	
	if (!$help_task) { $help_task = $JLMS_SESSION->get('jlms_task'); }
	$user_access = $JLMS_CONFIG->get('current_usertype');
	if($user_access == 2){
		$help_task = "stu_".$help_task;
	}elseif($user_access == 6){
		$help_task = "ceo_".$help_task;
	}
	
	$menus = $JLMS_CONFIG->get('jlms_menu');

	JLMS_require_lang($JLMS_LANGUAGE, 'main.lang', $JLMS_CONFIG->get('default_language'));

	if (($JLMS_CONFIG->get('lofe_show_course_box', true) && $JLMS_CONFIG->get('lofe_box_type',1)) || ($JLMS_CONFIG->get('lofe_show_head', true) && $JLMS_CONFIG->get('lofe_show_top', true)) ) { ?>
	<script language="JavaScript" type="text/javascript">
	<!--//--><![CDATA[//><!--
	<?php
	 
	$add_js = '';
	
	if ($JLMS_CONFIG->get('lofe_show_head', true) && $JLMS_CONFIG->get('lofe_show_top', true)) {	
		$add_js = "
			function jlms_ShowTBToolTip(txt_tooltip) {
					$('JLMS_toolbar_tooltip').innerHTML = txt_tooltip;
			}
		";	
	}
	  
	if ($JLMS_CONFIG->get('lofe_show_course_box', true) && $JLMS_CONFIG->get('lofe_box_type',1)) { 
		$add_js .= "
			function jlms_redirect(redirect_url) {
				top.location.href = redirect_url;
			}
			function jlms_tr_over(td) {
				td.style['background'] = '#FFFFFF';			
			}
			function jlms_tr_out(td) {
				td.style['background'] = '#EEEEEE';			
			}
			JLMS_preloadImages('".$JLMS_CONFIG->getCfg('live_site')."/components/com_joomla_lms/lms_images/front_menu/menu_bg3.png');
			";				
 	}
	
	if( $add_js ) 
	{ 
		$JLMS_CONFIG->set('jlms_aditional_js_code', $JLMS_CONFIG->get('jlms_aditional_js_code','').$add_js);
	} 
	 
	 ?>
	//--><!]]>
	</script>
<?php } ?>
<?php if ($JLMS_CONFIG->get('lofe_show_top', true) || $JLMS_CONFIG->get('lofe_show_course_box', true)) { ?>
	<table cellpadding="0" cellspacing="0" border="0" align="right" class="jlms_top_menu_outer">
	<?php if ($JLMS_CONFIG->get('lofe_show_head', true) && $JLMS_CONFIG->get('lofe_show_top', true)) { ?>
	<tr><td align="center" style="text-align:center ">
		<table width="100%" cellpadding="0" cellspacing="0" border="0" align="center" class="jlms_top_menu_tooltips">
		<tr>
			<td align="center" width="100%" style="text-align:center ">
				<span id="JLMS_toolbar_tooltip"><?php echo $back_status;?></span>
			</td>
			<?php //if ($JLMS_CONFIG->get('current_usertype') == 1) {
			/*if (false) { ?>
			<td align="right" nowrap="nowrap" style="white-space:nowrap ">
			<?php $u = JLMS_getOnlineUsers( $id );?>
				<a href="<?php echo sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid&amp;task=online_users&amp;course_id=$id");?>"><?php echo (_JLMS_ONLINE_USERS.' '.count($u));?></a>
			</td>
			<?php }*/?>
		</tr>
		</table>
	</td></tr>
	<?php }
	if ($JLMS_CONFIG->get('lofe_show_top', true)) { ?>
	<tr><td nowrap="nowrap" style="white-space:nowrap; text-align:right " align="right">
	<?php 
	$script = 0;
	$imh = '16';//$JLMS_CONFIG->get('top_menu_type');
	$imp = 'toolbar';
	if (!$JLMS_CONFIG->get('lofe_menu_style', 1)) {
		$imp = 'toolbar';$imh = '16';
	} elseif ($JLMS_CONFIG->get('lofe_menu_style', 1) == 1) {
		$imp = 'toolbar_24';$imh = '24';
	} elseif ($JLMS_CONFIG->get('lofe_menu_style', 1) == 2) {
		$imp = 'toolbar_32';$imh = '32';
	}
	/*if ($imh == 32) {
		$imp = 'toolbar_32';
	} elseif ($imh == 22) {
		$imp = 'toolbar_22';
	} elseif ($imh == 24) {
		$imp = 'toolbar_24';
	}*/
	
	$help_link = $JLMS_CONFIG->get('jlms_help_link', "http://www.joomlalms.com/index.php?option=com_lms_help&Itemid=40&task=view_by_task&key={toolname}");
	$was_separator = false;
	$JLMS_ACL = & JLMSFactory::getACL();

//	echo '<pre>';
//	print_r($menus);
//	echo '</pre>';
	
	foreach ($menus as $menu){
		$disabled = 0;
		if (isset($menu->disabled) && $menu->disabled) {
		} else {
			$is_shown = true;
			if ($menu->is_separator) {
				if (!$was_separator) {
					echo '<img src="'.$JLMS_CONFIG->get('live_site').'/components/com_joomla_lms/lms_images/spacer.png" border="0" width="2" height="'.$imh.'" style="background-color:#666666 " alt=" " />';
				}
				$was_separator = true;
			} else {
				if($menu->task == 'view_all_notices' && !$JLMS_CONFIG->get('flms_integration', 0)){
				
				} else {
					// check user permissions (14.09.2008 - DEN)
					/*if (isset($menu->lang_var) && $menu->lang_var == '_JLMS_TOOLBAR_DOCS') {
						$is_shown = $JLMS_ACL->CheckToolPermissions('docs');
					} elseif (isset($menu->lang_var) && $menu->lang_var == '_JLMS_TOOLBAR_QUIZZES') {
						$is_shown = $JLMS_ACL->CheckToolPermissions('quizzes');
					} elseif (isset($menu->lang_var) && $menu->lang_var == '_JLMS_TOOLBAR_LINKS') {
						$is_shown = $JLMS_ACL->CheckToolPermissions('links');
					} elseif (isset($menu->lang_var) && $menu->lang_var == '_JLMS_TOOLBAR_LPATH') {
						$is_shown = $JLMS_ACL->CheckToolPermissions('lpaths');
					} elseif (isset($menu->lang_var) && $menu->lang_var == '_JLMS_TOOLBAR_AGENDA') {
						$is_shown = $JLMS_ACL->CheckToolPermissions('announce');
					} elseif (isset($menu->lang_var) && $menu->lang_var == '_JLMS_TOOLBAR_ATTEND') {
						$is_shown = $JLMS_ACL->CheckToolPermissions('attendance');
					} elseif (isset($menu->lang_var) && $menu->lang_var == '_JLMS_TOOLBAR_CHAT') {
						$is_shown = $JLMS_ACL->CheckToolPermissions('chat');
					} elseif (isset($menu->lang_var) && $menu->lang_var == '_JLMS_TOOLBAR_CONF') {
						$is_shown = $JLMS_ACL->CheckToolPermissions('conference');
					} elseif (isset($menu->lang_var) && $menu->lang_var == '_JLMS_TOOLBAR_GRADEBOOK') {
						$is_shown = $JLMS_ACL->CheckToolPermissions('gradebook');
					} elseif (isset($menu->lang_var) && $menu->lang_var == '_JLMS_TOOLBAR_TRACK') {
						$is_shown = $JLMS_ACL->CheckToolPermissions('tracking');
					} elseif (isset($menu->lang_var) && $menu->lang_var == '_JLMS_TOOLBAR_MAILBOX') {
						$is_shown = $JLMS_ACL->CheckToolPermissions('mailbox');
					} elseif (isset($menu->lang_var) && $menu->lang_var == '_JLMS_TOOLBAR_USERS') {
						$is_shown = $JLMS_ACL->CheckToolPermissions('users');
					} elseif (isset($menu->user_options) && $menu->user_options) {
						$is_shown = $JLMS_ACL->CheckToolPermissions('user_settings');
					}*/
					if ($is_shown) {
						$was_separator = false;
						if (isset($menu->help_task) && $menu->help_task ) {
							$help_link = ampReplace(str_replace('{toolname}',$help_task,$help_link));
							$menu->menulink = $help_link;
						}	
						if (isset($menu->user_options) && $menu->user_options ) {
						} else {
							$lang_var_menu = '';
							if ($menu->lang_var && defined($menu->lang_var)) {
								$lang_var_menu = constant($menu->lang_var);
							} elseif (isset($JLMS_LANGUAGE[$menu->lang_var]) && $JLMS_LANGUAGE[$menu->lang_var]) {
								$lang_var_menu = $JLMS_LANGUAGE[$menu->lang_var];
							}
							$add = '';
							if ($JLMS_CONFIG->get('lofe_show_head', true)) {
								$add = "onmouseover='javascript:jlms_ShowTBToolTip(\"".$lang_var_menu."\");jlms_WStatus(\"".$lang_var_menu."\");return true;' ".$menu->target." onmouseout='javascript:jlms_ShowTBToolTip(\"".$back_status."\");jlms_WStatus(\"\");return true;'";
							}
							?>
							<a <?php echo $add;?> class="jlms_menu_control" href="<?php echo $menu->menulink;?>" title="<?php echo $lang_var_menu;?>"><img class='JLMS_png' src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/<?php echo $imp . "/" .$menu->image;?>" border="0" width="<?php echo $imh;?>" height="<?php echo $imh;?>" alt="<?php echo $lang_var_menu;?>" title="<?php echo $lang_var_menu;?>" /></a>
							<?php 
						}
					}
				}	
			}
			
			if (isset($menu->user_options) && $menu->user_options && $is_shown ) {
				$script = 1;
				$add = '';
				if ($JLMS_CONFIG->get('lofe_show_head', true)) {
					$add = " onmouseover='javascript:jlms_ShowTBToolTip(\""._JLMS_TOOLBAR_USER_OPTIONS."\");jlms_WStatus(\""._JLMS_TOOLBAR_USER_OPTIONS."\");return true;' onmouseout='javascript:jlms_ShowTBToolTip(\"".$back_status."\");jlms_WStatus(\"\");return true;'";
				}
			?>
				<a id="jlms_plugins_run"<?php echo $add;?> class="jlms_menu_control" href="javascript:void(0);" title="<?php echo _JLMS_TOOLBAR_TO_TEACH;?>"><img class='JLMS_png' src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/<?php echo $imp . "/" .$menu->image;?>" border="0" width="<?php echo $imh;?>" height="<?php echo $imh;?>" alt="<?php echo _JLMS_TOOLBAR_USER_OPTIONS;?>" title="<?php echo _JLMS_TOOLBAR_USER_OPTIONS;?>" /></a>
			<?php
			}
		}
	}	
	echo "<br />";
	echo "</td></tr>";
	}
	
if(!$gqp) {	
	
	
if ($JLMS_CONFIG->get('lofe_show_course_box', true)) {
		$cid = $JLMS_CONFIG->get('teacher_in_courses', array());
		$cid = array_merge($cid, $JLMS_CONFIG->get('student_in_courses', array()) );
		$cid = array_merge($cid, $JLMS_CONFIG->get('parent_in_courses', array()) );
		$cid = array_unique($cid);
		$courses = JLMS_CoursesNames( $cid );

		$cur_course = 'undefined';
		foreach ($courses as $course) {
			if ($id == $course->id) { $cur_course = $course->course_name; }//substr($course->course_name,0,15);}
		} ?>
	<tr>
	<td align="right" style="text-align:right ">
		<?php if (/*$JLMS_CONFIG->get('lofe_box_type',1)*/false) { ?>
		<table cellpadding="0" cellspacing="0" border="0" width="100%">
			<tr>
				<td align="right" nowrap="nowrap" style="text-align:right; font-size:10px; line-height:1.5">
					<?php echo _JLMS_CURRENT_COURSE;?>
				</td>
				<td width="120">
					<table width="120" cellpadding="0" cellspacing="0" border="0" align="right">
					<tr>
						<td colspan="2" align="left" style="text-align:left; background:url(<?php echo $JLMS_CONFIG->getCfg('live_site');?>/components/com_joomla_lms/lms_images/front_menu/menu_bg.png) no-repeat; ">

						<table style="cursor:pointer; border-bottom:1px solid #666666; width:220px;" id="demo1run1" width="220" cellpadding="0" cellspacing="0" border="0"><tr><td align="left">
							<div style="cursor:pointer; overflow:hidden; white-space:nowrap; width:200px;" >&nbsp;&nbsp;<?php echo $cur_course;?></div>
						</td><td align="right" width="20"><img class="JLMS_png" src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/com_joomla_lms/lms_images/course_select_arrow.png" alt="select_arrow" title="select_arrow" border="0" width="10" height="10" />&nbsp;&nbsp;</td></tr></table>
						<div align="right" id="course_menu_cont" style="position: absolute; visibility: hidden; width: 220px; font-size:10px; line-height:1.5">
						<div>
							<div id="demo1">
							<table width="100%" cellpadding="0" cellspacing="0" border="0" class="jlms_top_menu_items_table" id="jlms_top_menu_items_table_id">
								<?php
									$i = 0;
									foreach ($courses as $course) {
										$link = ampReplace(sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid&amp;task=".$JLMS_SESSION->get('jlms_task')."&amp;id=$course->id"));	
										echo "<tr id='cmenu_".$i."' onmouseover=\"jlms_tr_over(this);\" onmouseout=\"jlms_tr_out(this);\" onclick=\"jlms_redirect('".$link."');\"><td align='left'><div>&nbsp;".(($id == $course->id)?('<b>'.$course->course_name.'</b>'):$course->course_name)."</div></td></tr>";
										$i ++;
									} ?>
									<tr style='cursor:pointer; background:url(<?php echo $JLMS_CONFIG->getCfg('live_site');?>/components/com_joomla_lms/lms_images/front_menu/menu_bg2.png) no-repeat; background-position: bottom;'><td style="height:4px; border:0px"></td></tr>
							</table>
							</div>
						</div>
						<?php if ($JLMS_CONFIG->get('web20_effects', true)) {
							$domready = '
					var demo1effect = new Fx.Slide(\'demo1\');
					demo1effect.hide();
					$(\'course_menu_cont\').setStyle(\'visibility\', \'visible\');
					$(\'demo1run1\').addEvent(\'click\', function(e){
						e = new Event(e);
						demo1effect.toggle();
						e.stop();
					});
							';
							$JLMS_CONFIG->set('web20_domready_code', $JLMS_CONFIG->get('web20_domready_code','').$domready);
						} else {
							$domready = '
					var course_menu_hidden = true;
					$(\'demo1run1\').addEvent(\'click\', function(e){
						e = new Event(e);
						if (course_menu_hidden) {
							$(\'course_menu_cont\').setStyle(\'visibility\', \'visible\');
							course_menu_hidden = false;
						} else {
							$(\'course_menu_cont\').setStyle(\'visibility\', \'hidden\');
							course_menu_hidden = true;
						}
						e.stop();
					});
							';
							$JLMS_CONFIG->set('web20_domready_code', $JLMS_CONFIG->get('web20_domready_code','').$domready);
						} ?>
						</div>
						</td>
					</tr>
					</table>
				</td>
			</tr>
		</table>
		<?php } else {
		$add_js = "
		function jlms_redirect_form(sel_element) {
			var id = sel_element.options[sel_element.selectedIndex].value;
			var redirect_url = '';
			switch (id) {
";
foreach ($courses as $course) {
	$add_js .= "
				case '$course->id':
					redirect_url = '".str_replace('&amp;', '&', sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=".$JLMS_SESSION->get('jlms_task')."&id=".$course->id))."'
				break;
";
}

$add_js .= "
				default:
				break;
			}
			if (redirect_url) {
				top.location.href = redirect_url;
			}
		}
		";
		$JLMS_CONFIG->set('jlms_aditional_js_code', $JLMS_CONFIG->get('jlms_aditional_js_code','').$add_js);
		?>
		<table cellpadding="0" cellspacing="0" border="0" style="float:right" class="jlms_coursebox_cont">
			<tr>
				<td align="right" nowrap="nowrap">
					<?php echo _JLMS_CURRENT_COURSE;?>
				</td>
				<td width="120" nowrap="nowrap">
					<form name="jlms_change_course" action="<?php echo sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid");?>" method="post">
				<noscript>
					<input type="hidden" name="option" value="<?php echo $option;?>" />
					<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
					<input type="hidden" name="task" value="<?php echo $JLMS_SESSION->get('jlms_task');?>" />
				</noscript>
					<table cellpadding="0" cellspacing="0" border="0" class="jlms_coursebox"><tr><td>
					<select name="id" style="width:200px; border:1px solid #666666;" onchange="jlms_redirect_form(this)">
					<?php
					$i = 0;
					foreach ($courses as $course) {
						echo '<option value="'.$course->id.'"'.(($id == $course->id) ? ' selected="selected"':'').'>'.$course->course_name.'</option>';
						$i ++;
					} ?>
					</select>
					</td><td>
					<noscript>
						<input type="submit" name="OK" value="OK" />
					</noscript>
				</td></tr></table>
					</form>
				</td>
			</tr>
		</table>
		<?php } ?>
	</td>
	</tr>
<?php }} 

?>
	</table>
<?php } ?>
<?php
}
?>