<?php
/**
* admin.dev_config.html.php
* JoomlaLMS Component
*/

// no direct access
//defined( '_VALID_MOS' ) or die( 'Restricted access' );
if (!defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) { die( 'Restricted access' ); }

class HTML_config {

	function editConfigSource( $content, $option ) {
		$config_path = JPATH_SITE .'/components/com_joomla_lms/includes/config.inc.php';
		?>

		<script type="text/javascript">
		<!--
		function submitbutton(pressbutton) {
			var form = document.adminForm;
			form.page.value = pressbutton;
			form.submit();
		}
		
		<?php if( JLMS_J16version() ) { ?>
		Joomla.submitbutton = submitbutton;
		<?php } ?>
	
		//-->
		</script>

		<form action="index.php" method="post" name="adminForm">		
		<table cellpadding="0" cellspacing="0" border="0" width="100%">
		<tr>
			<td valign="top" width="220px">
			<div>
				<?php echo joomla_lms_adm_html::JLMS_menu();?>
			</div>
			</td>
			<td valign="top">
			<table cellpadding="0" cellspacing="0" border="0" width="100%">
			<tr>
			<td width="290">
		<?php if (!class_exists('JToolBarHelper')) { ?>
			<table class="adminheading"><tr><th class="config"><?php echo _JOOMLMS_COMP_NAME.': <small>DEV.config</small>';?></th></tr></table>
		<?php } ?>
			</td>
			<td width="220">
				<span class="componentheading">config.inc.php is :
				<b><?php echo is_writable($config_path) ? '<font color="green"> '._JLMS_WRITABLE.'</font>' : '<font color="red"> '._JLMS_UNWRITABLE.'</font>' ?></b>
				</span>
			</td>
<?php
			if (mosIsChmodable($config_path)) {
				if (is_writable($config_path)) {
?>
			<td>
				<input type="checkbox" id="disable_write" name="disable_write" value="1"/>
				<label for="disable_write"><?php echo _JLMS_CFG_MSG_UNWR_AFTER_SAVE; ?></label>
			</td>
<?php
				} else {
?>
			<td>
				<input type="checkbox" id="enable_write" name="enable_write" value="1"/>
				<label for="enable_write"><?php echo _JLMS_CFG_MSG_OVERRIDE_PROTT; ?></label>
			</td>
<?php
				} // if
			} // if
?>
		</tr>
		</table>
		<div class="width-100">
		<fieldset class="adminform">
		<table >
			<tr><th><?php echo $config_path; ?></th></tr>
			<tr><td><textarea style="width:100%;height:500px" cols="110" rows="25" name="filecontent" class="inputbox"><?php echo $content; ?></textarea></td></tr>
		</table>
		</fieldset>
		</div>
		</td>
		</tr>
		</table>		
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="task" value="dev_config" />
		<input type="hidden" name="page" value="save_config" />
		<input type="hidden" name="<?php echo josSpoofValue(); ?>" value="1" />
		</form>		
		<?php
	}
}
?>