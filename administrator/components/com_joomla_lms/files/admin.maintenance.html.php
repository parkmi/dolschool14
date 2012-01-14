<?php
/**
* admin.maintenance.html.php
* JoomlaLMS Component
*/

// no direct access
//defined( '_VALID_MOS' ) or die( 'Restricted access' );
if (!defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) { die( 'Restricted access' ); }

class ALM_html {

	function JLMS_check_database_interface( $option, $out = array() ) {
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
<table width="100%" border="0">
	<tr>
		<td valign="top" width="220px">
		<div>
			<?php echo joomla_lms_adm_html::JLMS_menu();?>
		</div>
		</td>
		<td valign="top">
		<?php if (!class_exists('JToolBarHelper')) { ?>
		<table class="adminheading">
		<tr>
			<th class="config">
			<?php echo _JOOMLMS_COMP_NAME;?>: <small><?php echo _JLMS_MAIN; ?></small>
			</th>
		</tr>
		</table>
		<?php } ?>
		<div class="width-100">
		<fieldset class="adminform">
		<table width="100%" cellpadding="8" cellspacing="0" class="adminlist">
			<tr>
				<td>
				<?php echo _JLMS_MAIN_DESC; ?>
				</td>
			</tr>
		</table>

</td></tr></table>
		<input type="hidden" name="option" value="com_joomla_lms" />
		<input type="hidden" name="task" value="lms_maintenance" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0" />
		<input type="hidden" name="page" value="" />
		</fieldset>
		</div>
		</form>
		<?php
	}

	function JLMS_results_check_database( $option, $out = array(), $end = '', $flag_stop = '', $flag_check_tables = '') {
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
<table width="100%" border="0">
	<tr>
		<td valign="top" width="220px">
		<div>
			<?php echo joomla_lms_adm_html::JLMS_menu();?>
		</div>
		</td>
		<td valign="top">
		<?php if (!class_exists('JToolBarHelper')) { ?>
		<table class="adminheading">
		<tr>
		<tr>
			<th class="config">
			<?php echo _JOOMLMS_COMP_NAME;?>: <small><?php echo _JLMS_MAIN_CHECK_DB; ?></small>
			</th>
		</tr>
		</table>
		<?php } ?>
		<div class="width-100">
		<fieldset class="adminform">
		<table width="100%" cellpadding="8" cellspacing="0" class="adminlist">
		<thead>
			<tr>
				<th class="title" width="49%" align="left"><?php echo _JLMS_MAIN_TABLE; ?></th>
				<th class="title" width="49%" align="left"><?php echo _JLMS_MAIN_STATUS; ?></th>
			</tr>
		</thead>

		<?php if(!$flag_stop) {?>
		<tfoot>
			<tr>
				<td align="center" colspan="2">
					<?php echo str_replace('{link}','index.php?option=com_joomla_lms&amp;task=lms_maintenance&amp;page=check_database&amp;table_num='.$end, _JLMS_MAIN_IF_YOU_SEE ); ?>
				</td>
			</tr>
		</tfoot>

		<?php }
		elseif($flag_stop != 2) {?>
		<tfoot>
			<tr>
				<td align="center" colspan="2">
					<?php echo str_replace('{link}', 'index.php?option=com_joomla_lms&amp;task=lms_maintenance&amp;page=check_tables', _JLMS_MAIN_IF_YOU_SEE ) ?>
				</td>
			</tr>
		</tfoot>							
		<tbody>
		<?php }?>

			<?php 
				$kk = 0;
				for($i=0;$i<count($out);$i++) {
					foreach ($out[$i] as $k=>$v) {
						$flag = 0;
						for($j=0;$j<count($v);$j++) {	
							?><tr class="<?php echo "row$kk"; ?>">
							<td align="left"><?php echo $k;?></td>
							<td align="left"><?php if($v[$j+1]) echo "<font color=\"red\">".$v[$j+1]."</font>"; elseif($v[$j]) echo "<font color=\"green\">".$v[$j]."</font>"; else echo "OK"; $j++;?></td>
							</tr><?php
							$flag = 1;
							$kk = 1 - $kk;
						}
						if($flag == 0) {
							?><tr class="<?php echo "row$kk"; ?>">
							<td align="left"><?php echo $k;?></td>
							<td align="left">OK</td>
							</tr><?php
							$kk = 1 - $kk;
						}
					}
				}	
		?>
		</tbody>
		</table>
		</fieldset>
		</div>
</td></tr></table>
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="task" value="lms_maintenance" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0" />
		<input type="hidden" name="page" value="" />		
		</form>

		<script type="text/javascript">
		
		<?php if(!$flag_stop || $flag_check_tables) {
				if(!$flag_check_tables) {?>
					var page = 'check_database';
				<?php }
				else {?>
					var page = 'check_tables';
				<?php }	?>
	 	setTimeout('redirect()',5000);
		function redirect() {
			window.location="index.php?option=com_joomla_lms&task=lms_maintenance&page="+page+"&table_num=<?php echo $end;?>";	
		}
		<?php }?>
		
		//var winScroller = new Fx.Scroll(window);
		//winScroller.toBottom();

		</script>

		<?php
	}
}
?>