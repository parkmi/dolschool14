<?php
/**
* joomla_lms.mailbox.html.php
* (c) JoomaLMS eLearning Software http://www.joomlalms.com/
**/

// no direct access
defined( '_JLMS_EXEC' ) or die( 'Restricted access' );

class JLMS_mailbox_html {
	function JLMS_showInbox($row, $course_id, $pageNav, $option, $unread, $m_count)
	{
		global $Itemid,$JLMS_DB,$JLMS_CONFIG;
		JLMS_TMPL::OpenMT();
		if(!$course_id)		
			$hparams = array('show_menu' => false);
		else 
			$hparams = array();
			
		$toolbar = array();
		$toolbar[] = array('btn_type' => 'mail_inbox', 'btn_js' => "javascript:submitbutton('mailbox');");
		$toolbar[] = array('btn_type' => 'mail_outbox', 'btn_js' => "javascript:submitbutton('mail_sendbox');");
		$toolbar[] = array('btn_type' => 'mail_send', 'btn_js' => "javascript:submitbutton('mailbox_new');");
		
		$inbox_title = _JLMS_MB_INBOX;
		$inbox_title = str_replace('X', $unread, $inbox_title);
		$inbox_title = str_replace('Y', $m_count, $inbox_title);
		
		JLMS_TMPL::ShowHeader('mailbox', $inbox_title, $hparams, $toolbar);

		
		// Commented by DEN - toolbar moved to the header (maybe it wouldn't work at the non-course level - check it!)
		// eto sdelano shtoby toolbar rovno risovalsya dage esli top menu and course selectbox disabled
		
		//JLMS_TMPL::ShowToolbar($toolbar, 'right', true, '');

		JLMS_TMPL::OpenTS(); ?>
		<script type="text/javascript">
		function submitbutton(pressbutton) {
			var form = document.adminForm;
			if ((pressbutton == 'mail_delete') && (form.boxchecked.value == "0")){
				alert( "<?php echo _JLMS_MB_SEL_ITEM;?>" );
			} if ((pressbutton == 'mailbox_reply') && (form.boxchecked.value == "0")) {
				alert( "<?php echo _JLMS_MB_REPL_SEL;?>" );
			}else {
				form.task.value = pressbutton;
				form.submit();
			}
		}
		</script>
		<form action="<?php echo sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid");?>" method="post" name="adminForm">
		<table width="100%" cellpadding="2" cellspacing="0" border="0" class="<?php echo JLMSCSS::_('jlmslist');?>">
			<tr>
				<<?php echo JLMSCSS::tableheadertag();?>  class="<?php echo JLMSCSS::_('sectiontableheader');?>" width="20">
				<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($row); ?>);" />
					<!-- checkbox -->
				</<?php echo JLMSCSS::tableheadertag();?>>
				<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" width="10">
					<!-- file -->
				</<?php echo JLMSCSS::tableheadertag();?>>
				<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" width="30">
					<!-- unread -->
				</<?php echo JLMSCSS::tableheadertag();?>>
				
				<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>">
					<?php echo _JLMS_MB_FROM;?>
				</<?php echo JLMSCSS::tableheadertag();?>>
				<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>">
					<?php echo _JLMS_MB_SUBJECT;?>
				</<?php echo JLMSCSS::tableheadertag();?>>
				<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>">
					<?php echo _JLMS_MB_DATE;?>
				</<?php echo JLMSCSS::tableheadertag();?>>
			</tr>
			<?php
			for($i=0;$i<count($row);$i++)
			{
				$link_it = sefreltoabs("index.php?option=$option&amp;Itemid=$Itemid&amp;task=mail_view&amp;view_id=".$row[$i]->id."&amp;id=$course_id");
				$checked = '<input type="checkbox" id="cb'.$i.'" name="cid[]" value="'.$row[$i]->id.'" onclick="isChecked(this.checked);" />';
				echo '<tr class="'.JLMSCSS::_('sectiontableentry'.(($i%2)+1)).'">';
				echo '<td>'.$checked.'</td>';
				$btn_img = 'buttons/skrep.gif';
				echo '<td>'.($row[$i]->file?("<img src=\"".$JLMS_CONFIG->get('live_site').'/'.$JLMS_CONFIG->get('lms_path_to_images')."/".$btn_img."\" alt='"._JLMS_MB_ATTACHMENT."' />"):'&nbsp;').'</td>';
				$btn_img = 'toolbar/btn_drp_unreaded.png';
				$btn_img2 = 'toolbar/btn_drp_readed.png';
				$link = sefreltoabs("index.php?option=$option&amp;Itemid=$Itemid&amp;task=mk_read&amp;view_id=".$row[$i]->id."&amp;id=$course_id");
				$link2 = sefreltoabs("index.php?option=$option&amp;Itemid=$Itemid&amp;task=mk_unread&amp;view_id=".$row[$i]->id."&amp;id=$course_id");
				echo '<td>'.(!$row[$i]->is_read?("<img src=\"".$JLMS_CONFIG->get('live_site').'/'.$JLMS_CONFIG->get('lms_path_to_images')."/".$btn_img."\" style='cursor:pointer;' onclick=\"window.location.href='".$link."'\" alt=\""._JLMS_MB_MK_READ."\" title=\""._JLMS_MB_MK_READ."\" />"):("<img src=\"".$JLMS_CONFIG->get('live_site').'/'.$JLMS_CONFIG->get('lms_path_to_images')."/".$btn_img2."\" style='cursor:pointer;' onclick=\"window.location.href='".$link2."'\" alt=\""._JLMS_MB_MK_UNREAD."\" title=\""._JLMS_MB_MK_UNREAD."\" />")).'</td>';
				echo '<td>'.$row[$i]->username.'</td>';
				echo '<td><a href="'.$link_it.'">'.stripslashes((strlen($row[$i]->subject)>40)?(substr($row[$i]->subject,0,38).'...'):$row[$i]->subject).'</a>';
				if($row[$i]->course_id){ 
					echo '<br /><span class="small">'._JLMS_MB_COURSE_NAME.': '.JLMS_getCourseName($row[$i]->course_id).'</span>';
				}
				echo '</td>';
				echo '<td>'.$row[$i]->data.'</td>';
				echo '</tr>';
			}
			?>
			<tr>
			<td colspan="7" align="center" class="<?php echo JLMSCSS::_('jlmslist-footer_td');?>">
				<div align="center" style="white-space: nowrap;">
				<?php 
					$link = "index.php?option=$option&amp;Itemid=$Itemid&amp;task=mailbox&amp;id=$course_id";
					echo _JLMS_PN_DISPLAY_NUM . $pageNav->getLimitBox( $link ).' '.$pageNav->getPagesCounter(); 
					echo '<br />';
					echo $pageNav->writePagesLinks( $link );
				?>
				</div>
			</td>
		</tr>
		</table>
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
		<input type="hidden" name="task" value="mailbox" />
		<input type="hidden" name="id" value="<?php echo $course_id;?>" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="state" value="0" />
		</form>
		<?php
		JLMS_TMPL::CloseTS();
		$controls = array();
			
			$controls[] = array('href' => "javascript:submitbutton('mail_delete');", 'title' => _JLMS_DELETE, 'img' => 'delete');
			$controls[] = array('href' => "javascript:submitbutton('mailbox_reply');", 'title' => _JLMS_MB_REPL_REPLY, 'img' => 'mail_reply');
			JLMS_TMPL::ShowControlsFooter($controls);//, sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid&amp;task=mailbox&amp;id=$course_id"));
		JLMS_TMPL::CloseMT();
	
	}
	function JLMS_showOutbox($row, $course_id, $pageNav, $option, $m_count)
	{
		global $Itemid, $JLMS_DB, $JLMS_CONFIG;
		JLMS_TMPL::OpenMT();

		if(!$course_id)		
			$hparams = array('show_menu' => false);
		else 
			$hparams = array();	
		$toolbar = array();
		$toolbar[] = array('btn_type' => 'mail_inbox', 'btn_js' => "javascript:submitbutton('mailbox');");
		$toolbar[] = array('btn_type' => 'mail_outbox', 'btn_js' => "javascript:submitbutton('mail_sendbox');");
		$toolbar[] = array('btn_type' => 'mail_send', 'btn_js' => "javascript:submitbutton('mailbox_new');");
		
		$outbox_title = _JLMS_MB_OUTBOX;
		$outbox_title = str_replace('Y', $m_count, $outbox_title);
		
		JLMS_TMPL::ShowHeader('mailbox', $outbox_title, $hparams, $toolbar);

		JLMS_TMPL::OpenTS(); ?>
		<script type="text/javascript">
		function submitbutton(pressbutton) {
			var form = document.adminForm;

				if ((pressbutton == 'mail_delete') && (form.boxchecked.value == "0")){
					alert( "<?php echo _JLMS_MB_SEL_ITEM;?>" );
				} else {
					form.task.value = pressbutton;
					form.submit();
				}

		}
		</script>
		<form action="<?php echo sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid");?>" method="post" name="adminForm">
		<table width="100%" cellpadding="2" cellspacing="0" border="0" class="<?php echo JLMSCSS::_('jlmslist');?>">
			<tr>
				<<?php echo JLMSCSS::tableheadertag();?>  class="<?php echo JLMSCSS::_('sectiontableheader');?>" width="20">
				<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($row); ?>);" />
					<!-- checkbox -->
				</<?php echo JLMSCSS::tableheadertag();?>>

				<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>">
					<?php echo _JLMS_MB_TO;?>
				</<?php echo JLMSCSS::tableheadertag();?>>
				<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>">
					<?php echo _JLMS_MB_SUBJECT;?>
				</<?php echo JLMSCSS::tableheadertag();?>>
				<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>">
					<?php echo _JLMS_MB_DATE;?>
				</<?php echo JLMSCSS::tableheadertag();?>>
			</tr>
			<?php
			for($i=0;$i<count($row);$i++)
			{
				$link = sefreltoabs("index.php?option=$option&amp;Itemid=$Itemid&amp;task=mail_view&amp;view_id=".$row[$i]->id."&amp;id=$course_id&amp;inb=1");
				$checked = '<input type="checkbox" id="cb'.$i.'" name="cid[]" value="'.$row[$i]->id.'" onclick="isChecked(this.checked);" />';
				$query = "SELECT u.username FROM #__lms_messages_to as mt, #__users as u WHERE mt.id=".$row[$i]->id." AND u.id=mt.user_id";
				$JLMS_DB->setQuery($query);
				$userki = $JLMS_DB->loadResultArray();
				$userz = implode(', ',$userki);
				$userz = (strlen($userz)>20)?(substr($userz,0,18).'...'):$userz;
				echo '<tr class="'.JLMSCSS::_('sectiontableentry'.(($i%2)+1)).'">';
				echo '<td>'.$checked.'</td>';
				echo '<td>'.$userz.'</td>';
				echo '<td><a href="'.$link.'">'.stripslashes((strlen($row[$i]->subject)>40)?(substr($row[$i]->subject,0,38).'...'):$row[$i]->subject).'</a>';
				if($row[$i]->course_id){ 
					echo '<br /><span class="small">'._JLMS_MB_COURSE_NAME.': '.JLMS_getCourseName($row[$i]->course_id).'</span>';
				}
				echo '</td>';
				echo '<td>'.$row[$i]->data.'</td>';
				echo '</tr>';
			}
			?>
			<tr>
			<td colspan="7" align="center" class="<?php echo JLMSCSS::_('jlmslist-footer_td');?>"><div align="center">
				<?php 
				$link = "index.php?option=$option&amp;Itemid=$Itemid&amp;task=mail_sendbox&amp;id=$course_id";
				echo _JLMS_PN_DISPLAY_NUM . $pageNav->getLimitBox( $link ).' '.$pageNav->getPagesCounter(); 
				echo '<br />';
				echo $pageNav->writePagesLinks( $link );?>
				
			</div></td>
		</tr>
		</table>
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
		<input type="hidden" name="task" value="mail_sendbox" />
		<input type="hidden" name="id" value="<?php echo $course_id;?>" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="state" value="0" />
		<input type="hidden" name="sendbox" value="1" />
		</form>
		<?php
		JLMS_TMPL::CloseTS();
		$controls = array();
			
			$controls[] = array('href' => "javascript:submitbutton('mail_delete');", 'title' => _JLMS_DELETE, 'img' => 'delete');
			JLMS_TMPL::ShowControlsFooter($controls);//, sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid&amp;task=mail_sendbox&amp;id=$course_id"));
		JLMS_TMPL::CloseMT();
	
	}
	function mailbox_view( $row, $option, $course_id, $inb )
	{
		global $Itemid,$JLMS_DB;
		JLMS_TMPL::OpenMT();

		if(!$course_id)		
			$hparams = array('show_menu' => false);
		else 
			$hparams = array();	
		JLMS_TMPL::ShowHeader('mailbox', _JLMS_MB_TITLE, $hparams);

		$toolbar = array();
		$toolbar[] = array('btn_type' => 'mail_inbox', 'btn_js' => "javascript:submitbutton('mailbox');");
		$toolbar[] = array('btn_type' => 'mail_outbox', 'btn_js' => "javascript:submitbutton('mail_sendbox');");
		$toolbar[] = array('btn_type' => 'mail_send', 'btn_js' => "javascript:submitbutton('mailbox_new');");
		JLMS_TMPL::ShowToolbar($toolbar, 'right', true, stripslashes($row->subject));

		JLMS_TMPL::OpenTS(); ?>
		<form action="<?php echo sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid");?>" method="post" name="adminForm">
		<table width="100%" class="jlms_table_no_borders">
			<tr>
				<td>
					
				
					<div class="small">
					<?php echo !$inb?(_JLMS_MB_SENDER.' :&nbsp;'):(_JLMS_MB_RECEPIENTS.' :&nbsp;');?>
					<?php 
					if(!$inb)
					{
						echo $row->username;
					}
					else 
					{
						$query = "SELECT u.username FROM #__lms_messages_to as mt, #__users as u WHERE mt.id=".$row->id." AND u.id=mt.user_id";
						$JLMS_DB->setQuery($query);
						$userki = $JLMS_DB->loadResultArray();
						echo $userz = implode(', ',$userki);
						//echo $userz = (strlen($userz)>20)?(substr($userz,0,18).'...'):$userz;
					}
					?>
					</div>
				</td>
			</tr>
			<tr>
				
				<td>
					<?php echo stripslashes($row->message)?>
				</td>
			</tr>
			<?php
			
			if($row->file)
			{
				$query = "SELECT * FROM #__lms_files WHERE id='".$row->file."'";
				$JLMS_DB->setQuery($query);
				$my_file = $JLMS_DB->loadObjectList();
				if (count($my_file))
				{
					
					$filename = $my_file[0]->file_name;
				}
			$link = sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid&amp;task=mfile_load&amp;id=".$course_id."&amp;view_id=".$row->id);	
			?>
			<tr>
				<td>
					<?php echo _JLMS_MB_ATTACHMENT.'&nbsp;'?>
				
					<a href="<?php echo $link?>"><?php echo $filename;?></a>
				</td>
			</tr>
			<?php	
			}
			?>
			
		</table>
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
		<input type="hidden" name="task" value="mailbox" />
		<input type="hidden" name="id" value="<?php echo $course_id;?>" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="state" value="0" />
		<input type="hidden" name="cid[]" value="<?php echo $row->id;?>" />
		</form>
		<?php
		JLMS_TMPL::CloseTS();
		$controls = array();
			$controls[] = array('href' => "javascript:history.back(-1);", 'title' => _JLMS_BACK_ALT_TITLE, 'img' => 'back');
			$controls[] = array('href' => "javascript:submitbutton('mail_delete');", 'title' => _JLMS_DELETE, 'img' => 'delete');
			if(!$inb){
			$controls[] = array('href' => "javascript:submitbutton('mailbox_reply');", 'title' => _JLMS_MB_REPL_REPLY, 'img' => 'mail_reply');
			}
			
			$tsk = $inb ?'mail_sendbox' : 'mailbox' ;
			JLMS_TMPL::ShowControlsFooter($controls, sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid&amp;task=$tsk&amp;id=$course_id"));
		JLMS_TMPL::CloseMT();
	}
	function mailbox_users( &$stats, $option, $course_id, &$lists, $filt_group ) {
		global $Itemid,$JLMS_DB,$JLMS_CONFIG; ?>
		<script language="javascript" type="text/javascript">
		<!--//--><![CDATA[//><!--
		var r_count = parseInt('<?php echo (isset($_POST['usr_id'])?count($_POST['usr_id']):0) + (isset($_POST['grp_id'])?count($_POST['grp_id']):0) + (isset($_POST['mail_id'])?count($_POST['mail_id']):0) + (isset($lists['repl'])?1:0)?>');
		function reanalize_class(){
			var tbl_id = getObj('show_mailz');
			if(tbl_id.rows[1]){
				for(var i=1;i<tbl_id.rows.length;i++){
					if((i % 2 + 1)==1){
						tbl_id.rows[i].className = 'sectiontableentry1 odd';
					}
					else
					{
						tbl_id.rows[i].className = 'sectiontableentry2 even';
					}
				}
			}
		}
		function Delete_tbl_row(element) {
			var del_index = element.parentNode.parentNode.sectionRowIndex;
			var tbl_id = element.parentNode.parentNode.parentNode.parentNode.id;
			element.parentNode.parentNode.parentNode.deleteRow(del_index);
			r_count = r_count - 1;
			reanalize_class();
		}
		function ading_row(texts,valuez,group)
		{
			var form = document.adminForm;
			var tbl_id = getObj('show_mailz');

			var row = tbl_id.insertRow(1);
			row.className = "sectiontableentry1 odd";
			var cell1 = document.createElement("td");
			var cell2 = document.createElement("td");
			var cell3 = document.createElement("td");
			var cell4 = document.createElement("td");
			
			input_button = document.createElement("IMG");
			input_button.src = "<?php echo $JLMS_CONFIG->get('live_site').'/'.$JLMS_CONFIG->get('lms_path_to_images').'/buttons_22/btn_delete_22.png'?>";
			input_button.onclick = new Function('Delete_tbl_row(this)');
			input_button.style.cursor = "pointer";
			
			input_img = document.createElement("IMG");
			if(!group)
			input_img.src = "<?php echo $JLMS_CONFIG->get('live_site').'/'.$JLMS_CONFIG->get('lms_path_to_images').'/buttons_22/btn_edituser_22.png'?>";
			else
			input_img.src = "<?php echo $JLMS_CONFIG->get('live_site').'/'.$JLMS_CONFIG->get('lms_path_to_images').'/buttons_22/btn_editusergroup_22.png'?>";
			
			cell1.appendChild(input_button);
			//cell2.style.width = "10px;";
			cell2.appendChild(input_img);
			cell3.innerHTML = texts;
			var input_text = document.createElement("input");
			input_text.type = "hidden";
			if(group==1) {
				input_text.name = "grp_id[]";
				input_text.setAttribute("name","grp_id[]");
			}
			else if(group==2) {
				input_text.name = "mail_id[]";
				input_text.setAttribute("name","mail_id[]");
			}
			else   {
				input_text.name = "usr_id[]";
				input_text.setAttribute("name","usr_id[]");
			}
			input_text.value = valuez;
			cell3.appendChild(input_text);
			row.appendChild(cell1);
			row.appendChild(cell2);
			row.appendChild(cell3);
			r_count = r_count + 1;
			reanalize_class();
		}
		function setgood() {
			return true;
		}
		function submitbutton(pressbutton) {
			var form = document.adminForm;
			try {
				form.onsubmit();
			} catch(e) {
				//alert(e);
			}
			if (pressbutton == 'mail_send'){
				if (form.jlms_subject.value == ''){
					alert('<?php echo _JLMS_MB_ENTER_SUBJECT;?>');
				}
				else{
					if (r_count){
						form.task.value = pressbutton;
						form.submit();
					}
					else{
					
						alert('<?php echo _JLMS_MB_ENTER_USERNAME;?>');
					}
				}
			}
			else
			{
				form.task.value = pressbutton;
						form.submit();
			}
		}
		function mail_to_text()
		{
			var ulist = document.adminForm['mailbox_users[]'];

			for(i=0;i<ulist.length;i++)
			{
				
				if(ulist[i].value == 0 && ulist[i].selected)
				{
					ading_row(ulist[i].text,'<?php echo $filt_group;?>',1);
					break;
				}
				else if(ulist[i].selected)
				{
					if(ulist[i].value>0)
					ading_row(ulist[i].text,ulist[i].value,0);
					else
					ading_row(ulist[i].text,ulist[i].value,2);
				}
			}
		}
		//--><!]]>
		</script>	
	<?php
		JLMS_TMPL::OpenMT();

		if(!$course_id)		
			$hparams = array('show_menu' => false);
		else 
			$hparams = array();
		
		$toolbar = array();
		$toolbar[] = array('btn_type' => 'mail_inbox', 'btn_js' => "javascript:submitbutton('mailbox');");
		$toolbar[] = array('btn_type' => 'mail_outbox', 'btn_js' => "javascript:submitbutton('mail_sendbox');");
		$toolbar[] = array('btn_type' => 'send', 'btn_js' => "javascript:submitbutton('mail_send');");
		JLMS_TMPL::ShowHeader('mailbox', _JLMS_MB_TITLE, $hparams, $toolbar);

		JLMS_TMPL::OpenTS(); ?>
		<form action="<?php echo sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid");?>" enctype="multipart/form-data" method="post" name="adminForm" onsubmit="setgood();">
			<table width="100%" cellpadding="2" cellspacing="0" border="0" class="jlms_table_no_borders" style="float:left;">
				<tr>
					<td align="left" style="text-align:left; width:360px; " valign="top">
						<div align="left" style="white-space:nowrap ">
							<?php echo $lists['mailbox_users'];?>
						</div>
						<div align="left" style="white-space:nowrap "><?php echo $lists['filter2'];?></div>
				
					</td>
					<td  width="50" valign="top">
						<?php
						$btn_img = '2rightarrow.png';
						// 29.04.2008 - 'alt' and 'src' - changed by DEN
						echo "<img src=\"".$JLMS_CONFIG->get('live_site').'/'.$JLMS_CONFIG->get('lms_path_to_images')."/".$btn_img."\" alt='>' onclick='mail_to_text();' style='cursor:pointer; padding-top:45px;' />";
						?>
					</td>
					<td  valign="top" width="45%">
					<table id="show_mailz" cellpadding="0" cellspacing="0" border="0" width="100%" class="<?php echo JLMSCSS::_('jlmslist');?>">
					<tr>
						<<?php echo JLMSCSS::tableheadertag();?>  class="<?php echo JLMSCSS::_('sectiontableheader');?>" width="25"><!-- gg -->&nbsp;</<?php echo JLMSCSS::tableheadertag();?>>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" width="25"><!-- gg -->&nbsp;</<?php echo JLMSCSS::tableheadertag();?>>
						<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>"><?php echo _JLMS_MB_RECEPIENTS;?></<?php echo JLMSCSS::tableheadertag();?>>
						
					</tr>
					<?php
					$z = 0;
					if(isset($lists['repl'])) {
						$_POST['usr_id'][0] = $lists['repl']->sender_id;
					}
					if(isset($_POST['usr_id']) && count($_POST['usr_id']))
					{
						for($i=0;$i<count($_POST['usr_id']);$i++)
						{
							if($_POST['usr_id'][$i])
							{
								echo '<tr class="'.JLMSCSS::_('sectiontableentry'.($z%2+1)).'">';
								echo '<td width="25" aligh="center"><img src="'.$JLMS_CONFIG->get('live_site').'/'.$JLMS_CONFIG->get('lms_path_to_images').'/buttons_22/btn_delete_22.png" alt="'._JLMS_DELETE.'" onclick="Delete_tbl_row(this);" style="cursor:pointer;" /></td>';
								echo '<td width="10" aligh="center"><img src="'.$JLMS_CONFIG->get('live_site').'/'.$JLMS_CONFIG->get('lms_path_to_images').'//buttons_22/btn_edituser_22.png" /></td>';
								
								if($JLMS_CONFIG->get('use_global_groups', 0)){
									$query = "SELECT a.id, a.name, a.username, a.email, b.ug_name"
									. "\n FROM #__users as a, #__lms_users_in_global_groups as c"
									. "\n LEFT JOIN #__lms_usergroups as b ON c.group_id = b.id AND b.course_id = '0'"
									. "\n WHERE a.id = c.user_id AND a.id=".intval($_POST['usr_id'][$i]).""
									. "\n ORDER BY b.ug_name, a.username";
									$JLMS_DB->SetQuery( $query );
									$users = $JLMS_DB->LoadObjectList();
								} else {
									$query = "SELECT a.id, a.name, a.username, a.email, b.ug_name"
									. "\n FROM #__users as a, #__lms_users_in_groups as c"
									. "\n LEFT JOIN #__lms_usergroups as b ON c.group_id = b.id AND b.course_id = '".$course_id."'"
									. "\n WHERE a.id = c.user_id AND c.course_id = '".$course_id."' AND a.id=".intval($_POST['usr_id'][$i]).""
									. "\n ORDER BY b.ug_name, a.username";
									$JLMS_DB->SetQuery( $query );
									$users = $JLMS_DB->LoadObjectList();
								}
								
								if(!count($users))
								{
									$query = "SELECT username,name FROM #__users WHERE id=".intval($_POST['usr_id'][$i]);
									$JLMS_DB->SetQuery( $query );
									$users = $JLMS_DB->LoadObjectList();
									if(count($users))
									{
										$users[0]->username = _JLMS_ROLE_TEACHER . ' - '.$users[0]->username . ' ('.$users[0]->name.')';
									}
								}
								else {
									$users[0]->username = ($users[0]->ug_name?$users[0]->ug_name:'').' - '.$users[0]->username . ' ('.$users[0]->name.')';
								}
								
									
								
								echo '<td>'.$users[0]->username.'<input type="hidden" name="usr_id[]" value="'.intval($_POST['usr_id'][$i]).'" /></td>';
						
								echo '</tr>';
								$z++;
							}
						}
					}
					
					if(isset($_POST['grp_id']) && count($_POST['grp_id']))
					{
						for($i=0;$i<count($_POST['grp_id']);$i++)
						{
							
								echo '<tr class="'.JLMSCSS::_('sectiontableentry'.($z%2+1)).'">';
								echo '<td width="25" aligh="center"><img src="'.$JLMS_CONFIG->get('live_site').'/'.$JLMS_CONFIG->get('lms_path_to_images').'/buttons_22/btn_delete_22.png" alt="'._JLMS_DELETE.'" onclick="Delete_tbl_row(this);" style="cursor:pointer;" /></td>';
								echo '<td width="10" aligh="center"><img src="'.$JLMS_CONFIG->get('live_site').'/'.$JLMS_CONFIG->get('lms_path_to_images').'/buttons_22/btn_editusergroup_22.png" /></td>';
								$query = "SELECT distinct a.ug_name as text FROM #__lms_usergroups as a"
								. "\n WHERE a.id = '".intval($_POST['grp_id'][$i])."'  ORDER BY a.ug_name";
								$JLMS_DB->SetQuery( $query );
								echo '<td>'.($JLMS_DB->loadResult()?$JLMS_DB->loadResult():_JLMS_MB_ALL_USRS).'<input type="hidden" name="grp_id[]" value="'.intval($_POST['grp_id'][$i]).'" /></td>';

								echo '</tr>';
								$z++;
						}
					}
					if(isset($_POST['mail_id']) && count($_POST['mail_id']))
					{
						for($i=0;$i<count($_POST['mail_id']);$i++)
						{
							
								echo '<tr class="'.JLMSCSS::_('sectiontableentry'.($z%2+1)).'">';
								echo '<td width="25"><img src="'.$JLMS_CONFIG->get('live_site').'/'.$JLMS_CONFIG->get('lms_path_to_images').'/buttons_22/btn_delete_22.png" alt="'._JLMS_DELETE.'" onclick="Delete_tbl_row(this);" style="cursor:pointer;" /></td>';
								echo '<td width="10"><img src="'.$JLMS_CONFIG->get('live_site').'/'.$JLMS_CONFIG->get('lms_path_to_images').'/buttons_22/btn_editusergroup_22.png" /></td>';
								$query = "SELECT pm_name FROM #__lms_messagelist WHERE id=".intval(abs($_POST['mail_id'][$i]));
								$JLMS_DB->SetQuery( $query );
								echo '<td>'.($JLMS_DB->loadResult()?$JLMS_DB->loadResult():_JLMS_MB_ALL_USRS).'<input type="hidden" name="mail_id[]" value="'.intval($_POST['mail_id'][$i]).'" /></td>';
								echo '</tr>';
								$z++;
						}
					}
					?>
					</table>
					<br />
					</td>
				</tr>
				
			</table>
			<div style="clear:both;"></div>
				
			<br />
			<table width="100%" cellpadding="2" cellspacing="0" border="0" class="jlms_table_no_borders">
				<tr>
					<td valign="top" align="left" >
					<?php echo _JLMS_MB_SUBJECT;?> :
					</td>
				</tr>	
				<tr>	
					<td>
					<input  type="text" name="jlms_subject"  size="60" value="<?php if(isset($lists['repl']))  echo _JLMS_MB_REPL_RE.$lists['repl']->subject;?>"  class="inputbox" />
					</td>
				</tr>
				<tr>
					<td align="left">
					<?php echo _JLMS_MB_TEXT." :";?>
					</td>
				</tr>	
				<tr>
					<td>
					<?php
					$curmsg = '';
					if(isset($lists['repl'])) { 
						$date_mas = $lists['repl']->data;
						$main_data = explode('-',substr($date_mas,0,10));
						$other_data = explode(':',substr($date_mas,11,8));
						$view_format = date(_JLMS_MB_REPL_DF,mktime($other_data[0],$other_data[1],$other_data[2],$main_data[1],$main_data[2],$main_data[0]));
						
						$curmsg_pre = $view_format.' '._JLMS_MB_REPL_YW.':<br />';
						$linebreak = '<br /><br />';
						$str = '';
						for($i=0;$i<50;$i++){							
							$str = $str.'-';
						}
						$linebreak .= $str;
						$linebreak .= '<br />';
						
						$old_msg_external = $lists['repl']->message;
						$curmsg = $linebreak.$curmsg_pre.$old_msg_external;
						
					}
					jlms_editorArea( 'editor1', $curmsg , 'jlms_mailbox_letter', '100%;' , '250', '40', '20' ) ; ?>
					</td>
				</tr>
				<tr id="hide">
					<td>
					<a href="javascript:void(0)" onclick="javascript:getObj('attach').style.display = '';getObj('hide').style.display = 'none'"><?php echo _JLMS_MB_ATTACH;?></a>
					</td>
				</tr>
				<tr style="display:none " id="attach">
					<td>
						<table class="jlms_table_no_borders" width="100%">
							<tr>
								<td>
								<a href="javascript:void(0)" onclick="javascript:getObj('attach').style.display = 'none';getObj('hide').style.display = ''"><?php echo _JLMS_MB_HIDE;?></a>
								</td>
							</tr>
							<tr>
								<td><input type="file" name="jlms_attach_file"  size="60" class="inputbox" /></td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
 		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
		<input type="hidden" name="task" value="mailbox_new" />
		<input type="hidden" name="id" value="<?php echo $course_id;?>" />
		<input type="hidden" name="m_course_id" value="<?php echo isset($lists['repl']->course_id) && $lists['repl']->course_id ? $lists['repl']->course_id : -1 ;?>" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="state" value="0" />
		</form>
	<?php
		JLMS_TMPL::CloseTS();
		JLMS_TMPL::CloseMT(); // 29.04.2008 - line added by DEN (layout fix)
	}
}
?>