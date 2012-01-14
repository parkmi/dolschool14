<?php
/**
* joomla_lms.chat.html.php
* (c) JoomaLMS eLearning Software http://www.joomlalms.com/
**/

// no direct access
defined( '_JLMS_EXEC' ) or die( 'Restricted access' );
// to do: dobavit' vse nugnye polya + JS proverki
// vystavit' nugnye classy dlya <table> (a to po umolchaniyu oni vsegda s cellpadding cellspacing
class JLMS_chat_html {

	function showChat( $course_id, $group_id, $option, $lists, $chat_users ) {
		global $Itemid, $JLMS_CONFIG;

	$additon_js = '
var Chat_scroll_obj;
function jlms_prepare_el_Chat_scroll_obj() {
	var Chat_scroll_obj = new Fx.Scroll(\'jlms_chat_window\', {
		wait: false,
		duration: 0,
		offset: {\'x\': 0, \'y\': 0}
	});
	Chat_scroll_obj.toBottom();
}
';
$JLMS_CONFIG->set('jlms_aditional_js_code', $JLMS_CONFIG->get('jlms_aditional_js_code','').$additon_js);
$domready = '
jlms_prepare_el_Chat_scroll_obj();
';
$JLMS_CONFIG->set('web20_domready_code', $JLMS_CONFIG->get('web20_domready_code','').$domready);
?>
<script language="javascript" type="text/javascript">
<!--
var timer_msec = 4000;
var block_chat = 0;
var chat_last_msg = 0;
var ste_k = 2;
var tID = '';
var url_prefix = '<?php echo "index.php?tmpl=component&option=$option&Itemid=$Itemid&id=$course_id&group_id=$group_id";?>';
function jlms_MakeRequest(url) {
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
		return false;
	}
	http_request.onreadystatechange = function() { if (window.jlms_AnalizeRequest) {jlms_AnalizeRequest(http_request);} };
	http_request.open('POST', url_prefix, true);
	http_request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	http_request.setRequestHeader("Content-length", url.length);	
	http_request.send(url);
}
function jlms_AnalizeRequest(http_request) {
	if (http_request.readyState == 4) {
		if ((http_request.status == 200)) {
			block_chat = 0;
			if(http_request.responseXML.documentElement == null){
				try {
					http_request.responseXML.loadXML(http_request.responseText)
				} catch (e) {
					/*alert("Can't load");*/
				}
			}
			response  = http_request.responseXML.documentElement;
			var task = response.getElementsByTagName('task')[0].firstChild.data;
			switch (task) {
				case 'chat_xml':
					//var response_chat = response.getElementsByTagName('chat_history')[0].firstChild.data;
					var response_users = response.getElementsByTagName('chat_users')[0].firstChild.data;
					var chat_last_id = response.getElementsByTagName('chat_last_id')[0].firstChild.data;
					if (parseInt(chat_last_id)) {
						chat_last_msg = parseInt(chat_last_id);
					}
					//var response_do_scroll = response.getElementsByTagName('do_scroll')[0].firstChild.data;

					/*getObj('JLMS_chat_history').innerHTML = response_chat;
					getObj('JLMS_chat_users').innerHTML   = response_users;

					if (response_do_scroll == 1 || response_do_scroll == '1') {
						if ($defined(Chat_scroll_obj)) {
							var type = typeof Chat_scroll_obj;
							if (type == 'object') {
								Chat_scroll_obj.toBottom();
							} else {
								jlms_prepare_el_Chat_scroll_obj();
							}
						} else {
							jlms_prepare_el_Chat_scroll_obj();
						}
					}*/
					var count_new_mess = response.getElementsByTagName('count_new_msgs')[0].firstChild.data;
					if(parseInt(count_new_mess)){
						count_new_mess = parseInt(count_new_mess);
						var el_history = getObj('jlms_chathistory');
						for(var j=1;j < (count_new_mess + 1);j++){
							var message = response.getElementsByTagName('chat_message_'+j)[0].firstChild.data;
							var tbody = new Array();
							tbody = el_history.childNodes;
							for(var i=0;i < tbody.length;i++){
								if(tbody[i].tagName == "TBODY"){
									var el_tbody = tbody[i];	
								}
							}
							var tr_111 = document.createElement("tr");
							var td_111 = document.createElement("td");
							td_111.innerHTML = message;
							tr_111.appendChild(td_111);
							tr_111.className = 'sectiontableentry'+ste_k;
							ste_k = 3 - ste_k;
							el_tbody.appendChild(tr_111);
						}
						/*var scroll_y = parseInt(el_history.offsetHeight);
						document.getElementById("jlms_chat_window").scrollTop = scroll_y;*/
						if ($defined(Chat_scroll_obj)) {
							var type = typeof Chat_scroll_obj;
							if (type == 'object') {
								Chat_scroll_obj.toBottom();
							} else {
								jlms_prepare_el_Chat_scroll_obj();
							}
						} else {
							jlms_prepare_el_Chat_scroll_obj();
						}
					}
					clearTimeout(tID);
					tID = setTimeout("jlms_RequestChatHistory()", timer_msec);
				break;
			}
		} else {
		}
	}
}
function ctrlenter(e){
	$('chat_new_mes').onkeypress = function(event){
		var event = new Event(event);
		if ( ((window.opera || window.gecko) && event.code == '13' && event.control) || ((window.ie || window.webkit) && event.code == '10' && event.control) ){
			jlms_chatPostMessage();
		}
	}
}
function jlms_RequestChatHistory() {
	jlms_MakeRequest('task=get_chat_xml&last_msg='+chat_last_msg);
}
function TRIM_str(sStr) {
	return (sStr.replace(/^[\s\xA0]+/, "").replace(/[\s\xA0]+$/, ""));
}
function URLencode(sStr) {
	return escape(sStr).replace(/\+/g, '%2B').replace(/\"/g,'%22').replace(/\'/g, '%27').replace(/\//g,'%2F');
}
function jlms_chatPostMessage() {
	if (block_chat != 1) {
		clearTimeout(tID);
		var form = document.chatMesForm;
		var user_message = form.chat_new_mes.value;
		if (user_message != '') {
			form.chat_new_mes.value = '';
			if (TRIM_str(user_message) != '') 
			{
				block_chat = 1;				
				jlms_MakeRequest('task=chat_post&last_msg='+chat_last_msg+'&message='+URLencode(TRIM_str(user_message)));
			}
		}
	}
}
//-->
</script>
<?php
		JLMS_TMPL::OpenMT();

		$hparams = array();
		JLMS_TMPL::ShowHeader('chat',_JLMS_CHAT_TITLE, $hparams);

		JLMS_TMPL::OpenTS();
?>
		<noscript>
			<div class="joomlalms_sys_message">
			<?php echo _JLMS_JS_COOKIES_REQUIRES;?>
			</div>
		</noscript>
		<table cellpadding="0" cellspacing="0" border="0" width="100%" class="jlms_table_no_borders">
			<tr>
				<td colspan="2" align="right" style="text-align:right ">
					<form action="<?php echo sefRelToAbs("index.php?option=$option&Itemid=$Itemid");?>" method="post" name="chatForm">
						<?php echo $lists['course_chats'];?>
						<input type="hidden" name="option" value="<?php echo $option;?>" />
						<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
						<input type="hidden" name="task" value="chat" />
						<input type="hidden" name="id" value="<?php echo $course_id;?>" />
					</form>
				</td>
			</tr>
		</table>
		<div style="width:100%; height:400px; overflow:auto; overflow-x:hidden;" id="jlms_chat_window">
		<table cellpadding="0" cellspacing="1" border="0" width="100%" class="<?php echo JLMSCSS::_('jlmslist');?>">
			<tr>
				<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" width="70%"><?php echo _JLMS_CHAT_TBL_HEAD_CHAT;?></<?php echo JLMSCSS::tableheadertag();?>>
				<<?php echo JLMSCSS::tableheadertag();?> class="<?php echo JLMSCSS::_('sectiontableheader');?>" width="30%"><?php echo _JLMS_CHAT_TBL_HEAD_USERS;?></<?php echo JLMSCSS::tableheadertag();?>>
			</tr>
			<tr>
				<td id="JLMS_chat_history" valign="top" align="left" style="text-align:left;padding:0px;margin:0px;" width="70%">
					<table width="100%" cellpadding="0" cellspacing="0" border="0" id="jlms_chathistory" class="<?php echo JLMSCSS::_('jlmslist');?>" style="margin:0px; padding:0px;">
						<tr class="<?php echo JLMSCSS::_('sectiontableentry1');?>"><td align="left"><?php echo _JLMS_CHAT_WELCOME_MESSAGE;?></td></tr>
					</table>
				</td>
				<td id="JLMS_chat_users" valign="top" align="left" style="text-align:left;padding:0px;margin:0px;" width="30%">
					<?php echo JLMS_chat_html::prepareUserList($chat_users); ?>
				</td>
			</tr>
		</table>
		</div>
		<table cellpadding="0" cellspacing="0" border="0" width="100%" class="jlms_table_no_borders">
			<tr>
				<td>
					<form action="<?php echo sefRelToAbs("index.php?option=$option&Itemid=$Itemid");?>" onsubmit="jlms_chatPostMessage();return false;" method="get" name="chatMesForm">
					<table cellpadding="0" cellspacing="0" border="0" width="100%" class="jlms_table_no_borders">
						<tr>
							<td align="left" width="90%">
								<textarea id="chat_new_mes" name="chat_new_mes" class="inputbox" cols="65" rows="3" style="width:99%; overflow-y:auto; border: 1px solid rgb(198, 198, 198);" onkeypress="ctrlenter(this);"></textarea>
							</td>
							<td align="left" style="padding-left:20px; width:64px;">
								<img class="JLMS_png" src="<?php echo $JLMS_CONFIG->get('live_site');?>/components/<?php echo $option;?>/lms_images/send_post_chat.png" border="0" alt="<?php echo _JLMS_CHAT_BTN_POST;?>" title="<?php echo _JLMS_CHAT_BTN_POST;?>" onclick="jlms_chatPostMessage();" style="cursor:pointer;"/>
							</td>
						</tr>
					</table>
					</form>
				</td>
			</tr>
		</table>
		<script language="javascript" type="text/javascript">
		<!--
		tID = setTimeout("jlms_RequestChatHistory()", timer_msec);
		//-->
		</script>
<?php
		JLMS_TMPL::CloseTS();
		JLMS_TMPL::CloseMT();
	}

function prepareUserList($rows) {
	$user_list_str = '<table width="100%" cellpadding="0" cellspacing="0" border="0" class="'.JLMSCSS::_('jlmslist').'" style="padding:0px;margin:0px">'."\n";
	$k = 1;
	foreach ($rows as $chat_user) {
		$user_list_str .= '<tr class="'.JLMSCSS::_('sectiontableentry'.$k).'"><td align="left">'.$chat_user->username.'</td></tr>';
		$k = 3 - $k;
	}
	$user_list_str .= '</table>';
	return $user_list_str;
}
function prepareChatHistory($rows) {
	$chat_history_str = '<table width="100%" cellpadding="0" cellspacing="0" border="0" id="jlms_chathistory" class="'.JLMSCSS::_('jlmslist').'" style="padding:0px;margin:0px">'."\n";
	$chat_history_str .= '<tr class="'.JLMSCSS::_('sectiontableentry1').'"><td align="left">'._JLMS_CHAT_WELCOME_MESSAGE.'</td></tr>'."\n";
	$k = 2;
	foreach ($rows as $chat_row) {
		$chat_history_str .= '<tr class="'.JLMSCSS::_('sectiontableentry'.$k).'"><td align="left"><b>'.$chat_row->username.': </b>'.stripslashes($chat_row->user_message).'</td></tr>';
		$k = 3 - $k;
	}
	$chat_history_str .= '</table>';
	return $chat_history_str;
}

}
?>