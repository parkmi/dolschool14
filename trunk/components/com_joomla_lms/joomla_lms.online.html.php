<?php
/**
* joomla_lms.course_links.html.php
* Joomla LMS Component
* * * ElearningForce DK
**/

// no direct access
defined( '_JLMS_EXEC' ) or die( 'Restricted access' );

class JLMS_course_ou_html {
	function showOU( $id, $option, &$rows ) {
		global $Itemid, $my;
	?>
		<form action="<?php echo sefRelToAbs("index.php?option=$option&Itemid=$Itemid");?>" method="post" name="adminForm">
		<table width="100%" cellpadding="0" cellspacing="0" border="0" class="contentpane">
			<tr>
				<td>
					<table width="100%" cellpadding="0" cellspacing="0" border="0">
						<tr>
							<td width="48">
								<?php echo JLMS_showHeadPicture('online_users');?>
							</td>
							<td class="contentheading" width="100%" valign="middle" style="vertical-align:middle ">
								&nbsp;<?php echo _JLMS_TOOLBAR_ONLINE;?>
							</td>
							<td align="right" valign="top" style="vertical-align:top ">
								<?php JLMS_showTopMenu( $id, $option );?>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td width="100%"><br />
				<table width="100%" cellpadding="0" cellspacing="0" border="0">
					<tr>
						<td width="20px" class="sectiontableheader" align="center">#</td>
						<td class="sectiontableheader" width="40%"><?php echo _JLMS_OU_USER;?></td>
						<td class="sectiontableheader" width="60%"><?php echo _JLMS_OU_LAST_ACTIVE;?></td>
					</tr>
				<?php
				$k = 1;
				for ($i=0, $n=count($rows); $i < $n; $i++) {
					$row = $rows[$i];?>
					<tr class="<?php echo "sectiontableentry$k"; ?>">
						<td align="center"><?php echo ( $i + 1 ); ?></td>
						<td align="left">
							<?php echo $row->username;?>
						</td>
						<td><?php echo $row->last_course_time;?></td>
					</tr>
					<?php
					$k = 3 - $k;
				} ?>
				</table>
				</td>
			</tr>
		</table>
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
		<input type="hidden" name="task" value="online_users" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="id" value="<?php echo $id;?>" />
		</form>
	<?php
	}
}
?>