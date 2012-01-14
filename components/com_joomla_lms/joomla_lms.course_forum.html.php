<?php
/**
* joomla_lms.course_forum.html.php
* Joomla LMS Component
* * * ElearningForce DK
**/

// no direct access
defined( '_JLMS_EXEC' ) or die( 'Restricted access' );

class JLMS_course_forum_html {
	function wrapper_course_forum( &$link, $option, $course_id, $msg ) {
		global $Itemid, $JLMS_CONFIG, $JLMS_LANGUAGE;
		JLMS_require_lang($JLMS_LANGUAGE, 'course_users.lang', $JLMS_CONFIG->get('default_language'));
		if (!defined('_USERNAME')) {
			define('_USERNAME', $JLMS_LANGUAGE['_JLMS_USER_USERNAME']);
		}
		if (!defined('_PASSWORD')) {
			define('_PASSWORD', $JLMS_LANGUAGE['_JLMS_USER_PASS']);
		}
		if (!defined('_BUTTON_LOGIN')) {
			define('_BUTTON_LOGIN', 'Login');
		}
		JLMS_TMPL::OpenMT();

		$hparams = array();
		JLMS_TMPL::ShowHeader('forum', _JLMS_HEAD_FORUM_STR, $hparams);

		if ($msg) {
			JLMS_TMPL::ShowSysMessage($msg);
		}

		JLMS_TMPL::OpenTS('', ' align="center" style="text-align:center; width:100%; height:100% " valign="top"');

		if ($msg) {
?>
			<br />
			<form action="<?php echo sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid");?>" enctype="multipart/form-data" method="post" name="adminForm">
				<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
					<tr>
						<td align="center">
							<table border="0" cellspacing="0" cellpadding="0" align="center">
								<tr>
									<td align="left">
										<label for="mod_login_username">
											<?php echo _USERNAME; ?>
										</label>
									</td>
									<td align="left">
										<input name="username" id="mod_login_username" type="text" class="inputbox" alt="username" size="10" />
									</td>
								</tr>
								<tr>
									<td align="left">
										<label for="mod_login_password">
											<?php echo _PASSWORD; ?>
										</label>
									</td>
									<td align="left">
										<input type="password" id="mod_login_password" name="passwd" class="inputbox" size="10" alt="password" />
									</td>
								</tr>
								<tr>
									<td colspan="2" align="center">
										<input type="submit" name="Submit" class="button" value="<?php echo _BUTTON_LOGIN; ?>" />
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
				<input type="hidden" name="option" value="<?php echo $option;?>" />
				<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
				<input type="hidden" name="task" value="login_to_forum" />
				<input type="hidden" name="id" value="<?php echo $course_id;?>" />
				<input type="hidden" name="boxchecked" value="0" />
				<input type="hidden" name="state" value="0" />
			</form>
<?php 	} else { ?>
				<script language="javascript" type="text/javascript">
					function iFrameHeight() {
						var h = 0;
						if ( !document.all ) {
							h = document.getElementById('blockrandom').contentDocument.height;
							document.getElementById('blockrandom').style.height = h + 60 + 'px';
						} else if( document.all ) {
							h = document.frames('blockrandom').document.body.scrollHeight;
							document.all.blockrandom.style.height = h + 20 + 'px';
						}
					}
					</script>
					<iframe onload="iFrameHeight()"
					id="blockrandom"
					name="iframe"
					src="<?php echo $link;?>"
					width="100%"
					height="800px"
					scrolling="auto"
					align="top"
					frameborder="0"
					class="wrapper" style="width:100%">
					<?php echo _JLMS_IFRAMES_REQUIRES; ?><br /><a href="<?php echo sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=details_course&id=$course_id");?>">Return to the course home page</a>
					</iframe>
<?php 	}
		JLMS_TMPL::CloseTS();
		JLMS_TMPL::CloseMT();
	}
}
?>