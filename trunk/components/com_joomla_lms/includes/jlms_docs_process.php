<?php
/**
* includes/jlms_docs_process.php
* Joomla LMS Component
* * * ElearningForce DK
* 29 May 2007 (DEN) Library for output doc contents. (play video/sound, show images, and so on (each extension should be handled .....)
**/

// no direct access
defined( '_JLMS_EXEC' ) or die( 'Restricted access' );


function JLMS_PrepareActiveContent( &$active_content ) {
	global $JLMS_CONFIG;
	$js_contents = addslashes($active_content); # Handle special characters properly
    $js_contents = str_replace(chr(13), "", $js_contents); # remove CRs - all in one line
    $js_contents = str_replace(chr(10), "", $js_contents); # remove LFs - all in one line
    # 1. Embed that tiny little external JS to work as actual embedder.
    # 2. Embed the original occurrence inside a JS variable -- 
    # 3. Call the tiny little embedder to dynamically output the variable
    # 4. Embed the original, unchanged occurrence in a <noscript>...</noscript> area as fall-back
    $file_contents = "<script src=\"".$JLMS_CONFIG->getCfg('live_site')."/components/com_joomla_lms/includes/media/jlms_activecontent_fix.js\" type=\"text/javascript\"></script>\n".
"<script language=\"JavaScript\">\n".
"<!--\n".
	"var js_contents = '$js_contents';\n".
	"JLMS_RunActiveContent(js_contents);". # So: Use the external one-liner function to perform the trick
"//-->\n".
"</script>\n".
"<noscript>$active_content</noscript>";
	return $file_contents;
}

/**
 * Enter description here...
 *
 * @param int $file_id
 * @param int $doc_id
 * @param string $doc_name
 * @param boolean $do_tracking
 * @return ---  html code or 'false' if file downloadable only.
 */
function JLMS_showMediaDocument( $file_id, $doc_id, $doc_name, &$do_tracking, $params = array() ) {
	global $JLMS_DB, $JLMS_CONFIG, $Itemid, $JLMS_CONFIG;
	$course_id = $JLMS_CONFIG->get('course_id');
	$query = "SELECT * FROM #__lms_files WHERE id = $file_id";
	$JLMS_DB->SetQuery($query);
	$file_contents = false;
	$prepare_active_contents = ( isset($params['disable_activate_content']) && $params['disable_activate_content']) ? false : true;
	$enable_auto_start = ( isset($params['enable_suto_start']) && $params['enable_suto_start']) ? true : false;
	if ( isset($params['doc_get_link']) && $params['doc_get_link']) {
		$link_to_downloadable_file = $params['doc_get_link'];
	} else {
		$link_to_downloadable_file = $JLMS_CONFIG->getCfg('live_site')."/index.php?tmpl=component&option=com_joomla_lms&Itemid=$Itemid&course_id=$course_id&task=get_document&id=$doc_id&force=force";
		$link_to_downloadable_file .= '';
	}
	if ( isset($params['doc_get_link_url_enc']) && $params['doc_get_link_url_enc']) {
		$link_to_downloadable_file_url_enc = $params['doc_get_link_url_enc'];
	} else {
		$link_to_downloadable_file_url_enc = $JLMS_CONFIG->getCfg('live_site')."/index.php%3Ftmpl%3Dcomponent%26option%3Dcom_joomla_lms%26Itemid%3D$Itemid%26course_id%3D$course_id%26task%3Dget_document%26id%3D$doc_id%26force%3Dforce";
	}
	$file_data = $JLMS_DB->LoadObject();
	if (is_object($file_data) && isset($file_data->file_name)) {
		$file_name = $file_data->file_name;
		$file_srv_name = $file_data->file_srv_name;
		$array = explode(".", $file_name);
		$nr = count($array);
		$file_extension = strtolower($array[$nr-1]);
		switch ($file_extension) {

		/* Images files */

			case 'bmp':
			case 'gif':
			case 'jpe':
			case 'jpg':
			case 'jpeg':
			case 'pct':
			case 'pic':
			case 'pict':
			case 'png':
			case 'svg':
			case 'svgz':
			case 'tif':
			case 'tiff':
				$file_contents = "<div id='jlms_doc_container' align='center' style='text-align:center'><div id='jlms_doc_image_container' align='center' style='overflow: auto; text-align:center'><img border='0' src='".$link_to_downloadable_file."' alt='".$doc_name."' title='".$doc_name."' /></div></div>";
				$do_tracking = false;
			break;

		/* Media files (audio, video, flash) */

			case 'mp3':
				$link_to_downloadable_file_url_enc = str_replace('force%3Dforce','force%3Dplayer', $link_to_downloadable_file_url_enc);
				$media_width = '300';
				$media_height = '20';
				$autostart = $enable_auto_start ? 'true' : 'false';
				$type = 'mp3';
				$player = 'flvplayer.swf';
				$file_contents_pre = "
				<div id='jlms_doc_container' align='center' style='text-align:center'>
					<object type='application/x-shockwave-flash' data='".$JLMS_CONFIG->getCfg('live_site')."/components/com_joomla_lms/includes/media/$player' classid='clsid:D27CDB6E-AE6D-11cf-96B8-444553540000' codebase='http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,29,0' width='".$media_width."' height='".$media_height."' wmode='transparent'>
						<param name='height' value='".$media_height."' />
						<param name='width' value='".$media_width."' />
						<param name='movie' value='".$JLMS_CONFIG->getCfg('live_site')."/components/com_joomla_lms/includes/media/$player' />
						<param name='flashvars' value='file=".$link_to_downloadable_file_url_enc."&autostart=$autostart&repeat=true&type=$type' />
						<param name='wmode' value='transparent' />
						<embed flashvars='file=".$link_to_downloadable_file_url_enc."&autostart=$autostart&type=$type&repeat=true' src='".$JLMS_CONFIG->getCfg('live_site')."/components/com_joomla_lms/includes/media/$player' quality='high' pluginspage='http://www.macromedia.com/go/getflashplayer' type='application/x-shockwave-flash' width='$media_width' height='$media_height' wmode='transparent'></embed>
					</object>
				</div>
				";
				$file_contents = $prepare_active_contents ? JLMS_PrepareActiveContent($file_contents_pre) : $file_contents_pre;
				$do_tracking = false;
			break;

			case 'flv':
				$link_to_downloadable_file_url_enc = str_replace('force%3Dforce','force%3Dplayer', $link_to_downloadable_file_url_enc);
				$media_width = '400';
				$media_height = '323';
				$autostart = $enable_auto_start ? 'true' : 'false';
				$usefullscreen = 'true';
				$type = 'flv';
				$player = 'flvplayer.swf';
				$file_contents_pre = "<div id='jlms_doc_container' align='center' style='text-align:center'>
<object type='application/x-shockwave-flash' data='".$JLMS_CONFIG->getCfg('live_site')."/components/com_joomla_lms/includes/media/$player' classid='clsid:D27CDB6E-AE6D-11cf-96B8-444553540000' codebase='http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,29,0' width='".$media_width."' height='".$media_height."' wmode='transparent'>
	<param name='height' value='".$media_height."' />
	<param name='width' value='".$media_width."' />
	<param name='movie' value='".$JLMS_CONFIG->getCfg('live_site')."/components/com_joomla_lms/includes/media/$player' />
	<param name='flashvars' value='file=".$link_to_downloadable_file_url_enc."&autostart=$autostart&usefullscreen=$usefullscreen&repeat=true&type=$type' />
	<param name='wmode' value='transparent' />
	<param name='allowfullscreen' value='$usefullscreen' />
	<embed flashvars='file=".$link_to_downloadable_file_url_enc."&autostart=$autostart&usefullscreen=$usefullscreen&type=$type&repeat=true' src='".$JLMS_CONFIG->getCfg('live_site')."/components/com_joomla_lms/includes/media/$player' quality='high' pluginspage='http://www.macromedia.com/go/getflashplayer' type='application/x-shockwave-flash' width='$media_width' height='$media_height' wmode='transparent' allowfullscreen='$usefullscreen'></embed>
</object></div>";
				$file_contents = $prepare_active_contents ? JLMS_PrepareActiveContent($file_contents_pre) : $file_contents_pre;
				$do_tracking = false;
			break;

			case 'swf':
			case 'swfl':
				$link_to_downloadable_file = str_replace('force=force','force=player', $link_to_downloadable_file);
				$size = getimagesize(_JOOMLMS_DOC_FOLDER . $file_data->file_srv_name);
				$swf_size = (isset($size[3]) && $size[3]) ? $size[3] : '';
				$file_contents_pre = "<div id='jlms_doc_container' align='center' style='text-align:center'>
<object classid='clsid:D27CDB6E-AE6D-11cf-96B8-444553540000' codebase='http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,19,0' ".$swf_size.">
  <param name='movie' value='$link_to_downloadable_file' />
  <param name='quality' value='high' />
  <param name='wmode' value='transparent' />
  <embed src='$link_to_downloadable_file' quality='high' pluginspage='http://www.macromedia.com/go/getflashplayer' type='application/x-shockwave-flash' wmode='transparent' ".$swf_size."></embed>
</object></div>";
				$file_contents = $prepare_active_contents ? JLMS_PrepareActiveContent($file_contents_pre) : $file_contents_pre;
				$do_tracking = false;
			break;			

			case 'avi':
			case 'mp4':
			case 'mpeg':
			case 'mpe':
			case 'mpg':
			case 'wmv':
			case 'wma':
				//$link_to_downloadable_file = str_replace('force=force','force=player', $link_to_downloadable_file);
				$media_width = '400';
				$media_height = '323';
				$show_controls = '1'; // IE only ?
				$auto_start = $enable_auto_start ? '1' : '0';
				
				$session = & JFactory::getSession();
				$link_to_downloadable_file .= '&sessionid='.$session->getId();
				
				$file_contents_pre = "<div id='jlms_doc_container' align='center' style='text-align:center'>
				<object type='application/x-oleobject' classid='CLSID:6BF52A52-394A-11d3-B153-00C04F79FAA6' style='width:".$media_width."px; height:".$media_height."px'>
					<param name='URL' value='".$link_to_downloadable_file."' />
					<param name='ShowControls' value='$show_controls'>
					<param name='autoStart' value='$auto_start'>
					<embed src='".$link_to_downloadable_file."' style='width:".$media_width."px; height:".$media_height."px' autoStart='$auto_start' type='application/x-mplayer2'></embed>
				</object></div>";
				
				$file_contents = $prepare_active_contents ? JLMS_PrepareActiveContent($file_contents_pre) : $file_contents_pre;
				$do_tracking = false;
			break;

			case 'mov':
			case 'qt':
			case '3gp':
				$media_width = '400';
				$media_height = '323';
				$show_controls = 'True';
				$auto_start = $enable_auto_start ? 'True' : 'False';
				$file_contents_pre = "<div id='jlms_doc_container' align='center' style='text-align:center'>
<object codebase='http://www.apple.com/qtactivex/qtplugin.cab' classid='clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B' style='width:".$media_width."px; height:".$media_height."px'>
	<param name='src' value='".$link_to_downloadable_file."' />
	<param name='controller' value='$show_controls' />
	<param name='cache' value='False' />
	<param name='autoplay' value='$auto_start' />
	<param name='kioskmode' value='False' />
	<param name='scale' value='tofit' />
<embed src='".$link_to_downloadable_file."' pluginspage='http://www.apple.com/quicktime/download/' scale='tofit' kioskmode='False' qtsrc='".$link_to_downloadable_file."' cache='False' style='width:".$media_width."px; height:".$media_height."px' controller='$show_controls' type='video/quicktime' autoplay='$auto_start' /></embed>
</object></div>";
				$file_contents = $prepare_active_contents ? JLMS_PrepareActiveContent($file_contents_pre) : $file_contents_pre;
				$do_tracking = false;
			break;

			case 'ram':
			case 'ra':
			case 'rm':
				$media_width = '400';
				$media_height = '323';
				$show_controls = 'ControlPanel';
				$auto_start = $enable_auto_start ? '1' : '0';
				$file_contents_pre = "<div id='jlms_doc_container' align='center' style='text-align:center'>
<object classid='clsid:CFCDAA03-8BE4-11cf-B84B-0020AFBBCCFA' style='width:".$media_width."px; height:".$media_height."px'>
	<param name='controls' value='$show_controls' />
	<param name='autostart' value='$auto_start' />
	<param name='src' value='".$link_to_downloadable_file."' />
<embed src='".$link_to_downloadable_file."' type='audio/x-pn-realaudio-plugin' style='width:".$media_width."px; height:".$media_height."px' controls='$show_controls' autostart='$auto_start' />
</object></div>";
				$file_contents = $prepare_active_contents ? JLMS_PrepareActiveContent($file_contents_pre) : $file_contents_pre;
				$do_tracking = false;
			break;

		/* text files */

			case 'txt':
				$srv_name = _JOOMLMS_DOC_FOLDER . $file_data->file_srv_name;
				$file_contents = htmlspecialchars(file_get_contents($srv_name));
				$file_contents = str_replace(array("\r\n", "\r", "\n"), "<br />", $file_contents);
				$do_tracking = true;
			break;

			case 'xml':
				$srv_name = _JOOMLMS_DOC_FOLDER . $file_data->file_srv_name;
				$file_contents = file_get_contents($srv_name);
				$file_contents = htmlspecialchars($file_contents);
				$file_contents = str_replace(array("\r\n", "\r", "\n"), "<br />", $file_contents);
				$do_tracking = true;
			break;

			default:
				$file_contents = false;
				$do_tracking = true;
			break;
		}
	}
	return $file_contents;
}

function JLMS_GetMimeType($type) {
	$mimetype_list = array (
		'xxx'  => array ('type'=>'document/unknown', 'icon'=>'unknown.gif'),
		'3gp'  => array ('type'=>'video/quicktime', 'icon'=>'video.gif'),
		'ai'   => array ('type'=>'application/postscript', 'icon'=>'image.gif'),
		'aif'  => array ('type'=>'audio/x-aiff', 'icon'=>'audio.gif'),
		'aiff' => array ('type'=>'audio/x-aiff', 'icon'=>'audio.gif'),
		'aifc' => array ('type'=>'audio/x-aiff', 'icon'=>'audio.gif'),
		'applescript'  => array ('type'=>'text/plain', 'icon'=>'text.gif'),
		'asc'  => array ('type'=>'text/plain', 'icon'=>'text.gif'),
		'asm'  => array ('type'=>'text/plain', 'icon'=>'text.gif'),
		'au'   => array ('type'=>'audio/au', 'icon'=>'audio.gif'),
		'avi'  => array ('type'=>'video/x-ms-wm', 'icon'=>'avi.gif'),
		'bmp'  => array ('type'=>'image/bmp', 'icon'=>'image.gif'),
		'c'    => array ('type'=>'text/plain', 'icon'=>'text.gif'),
		'cct'  => array ('type'=>'shockwave/director', 'icon'=>'flash.gif'),
		'cpp'  => array ('type'=>'text/plain', 'icon'=>'text.gif'),
		'cs'   => array ('type'=>'application/x-csh', 'icon'=>'text.gif'),
		'css'  => array ('type'=>'text/css', 'icon'=>'text.gif'),
		'dv'   => array ('type'=>'video/x-dv', 'icon'=>'video.gif'),
		'dmg'  => array ('type'=>'application/octet-stream', 'icon'=>'dmg.gif'),
		'doc'  => array ('type'=>'application/msword', 'icon'=>'word.gif'),
		'dcr'  => array ('type'=>'application/x-director', 'icon'=>'flash.gif'),
		'dif'  => array ('type'=>'video/x-dv', 'icon'=>'video.gif'),
		'dir'  => array ('type'=>'application/x-director', 'icon'=>'flash.gif'),
		'dxr'  => array ('type'=>'application/x-director', 'icon'=>'flash.gif'),
		'eps'  => array ('type'=>'application/postscript', 'icon'=>'pdf.gif'),
		'fdf'  => array ('type'=>'application/pdf', 'icon'=>'pdf.gif'),
		'gif'  => array ('type'=>'image/gif', 'icon'=>'image.gif'),
		'gtar' => array ('type'=>'application/x-gtar', 'icon'=>'zip.gif'),
		'tgz'   => array ('type'=>'application/g-zip', 'icon'=>'zip.gif'),
		'gz'   => array ('type'=>'application/g-zip', 'icon'=>'zip.gif'),
		'gzip' => array ('type'=>'application/g-zip', 'icon'=>'zip.gif'),
		'h'    => array ('type'=>'text/plain', 'icon'=>'text.gif'),
		'hpp'  => array ('type'=>'text/plain', 'icon'=>'text.gif'),
		'hqx'  => array ('type'=>'application/mac-binhex40', 'icon'=>'zip.gif'),
		'htc'  => array ('type'=>'text/x-component', 'icon'=>'text.gif'),
		'html' => array ('type'=>'text/html', 'icon'=>'html.gif'),
		'htm'  => array ('type'=>'text/html', 'icon'=>'html.gif'),
		'java' => array ('type'=>'text/plain', 'icon'=>'text.gif'),
		'jcb'  => array ('type'=>'text/xml', 'icon'=>'jcb.gif'),
		'jcl'  => array ('type'=>'text/xml', 'icon'=>'jcl.gif'),
		'jcw'  => array ('type'=>'text/xml', 'icon'=>'jcw.gif'),
		'jmt'  => array ('type'=>'text/xml', 'icon'=>'jmt.gif'),
		'jmx'  => array ('type'=>'text/xml', 'icon'=>'jmx.gif'),
		'jpe'  => array ('type'=>'image/jpeg', 'icon'=>'image.gif'),
		'jpeg' => array ('type'=>'image/jpeg', 'icon'=>'image.gif'),
		'jpg'  => array ('type'=>'image/jpeg', 'icon'=>'image.gif'),
		'jqz'  => array ('type'=>'text/xml', 'icon'=>'jqz.gif'),
		'js'   => array ('type'=>'application/x-javascript', 'icon'=>'text.gif'),
		'latex'=> array ('type'=>'application/x-latex', 'icon'=>'text.gif'),
		'm'    => array ('type'=>'text/plain', 'icon'=>'text.gif'),
		'mov'  => array ('type'=>'video/quicktime', 'icon'=>'video.gif'),
		'movie'=> array ('type'=>'video/x-sgi-movie', 'icon'=>'video.gif'),
		'm3u'  => array ('type'=>'audio/x-mpegurl', 'icon'=>'audio.gif'),
		'mp3'  => array ('type'=>'audio/mp3', 'icon'=>'audio.gif'),
		'mp4'  => array ('type'=>'video/mp4', 'icon'=>'video.gif'),
		'mpeg' => array ('type'=>'video/mpeg', 'icon'=>'video.gif'),
		'mpe'  => array ('type'=>'video/mpeg', 'icon'=>'video.gif'),
		'mpg'  => array ('type'=>'video/mpeg', 'icon'=>'video.gif'),

		'odt'  => array ('type'=>'application/vnd.oasis.opendocument.text', 'icon'=>'odt.gif'),
		'ott'  => array ('type'=>'application/vnd.oasis.opendocument.text-template', 'icon'=>'odt.gif'),
		'oth'  => array ('type'=>'application/vnd.oasis.opendocument.text-web', 'icon'=>'odt.gif'),
		'odm'  => array ('type'=>'application/vnd.oasis.opendocument.text-master', 'icon'=>'odm.gif'),
		'odg'  => array ('type'=>'application/vnd.oasis.opendocument.graphics', 'icon'=>'odg.gif'),
		'otg'  => array ('type'=>'application/vnd.oasis.opendocument.graphics-template', 'icon'=>'odg.gif'),
		'odp'  => array ('type'=>'application/vnd.oasis.opendocument.presentation', 'icon'=>'odp.gif'),
		'otp'  => array ('type'=>'application/vnd.oasis.opendocument.presentation-template', 'icon'=>'odp.gif'),
		'ods'  => array ('type'=>'application/vnd.oasis.opendocument.spreadsheet', 'icon'=>'ods.gif'),
		'ots'  => array ('type'=>'application/vnd.oasis.opendocument.spreadsheet-template', 'icon'=>'ods.gif'),
		'odc'  => array ('type'=>'application/vnd.oasis.opendocument.chart', 'icon'=>'odc.gif'),
		'odf'  => array ('type'=>'application/vnd.oasis.opendocument.formula', 'icon'=>'odf.gif'),
		'odb'  => array ('type'=>'application/vnd.oasis.opendocument.database', 'icon'=>'odb.gif'),
		'odi'  => array ('type'=>'application/vnd.oasis.opendocument.image', 'icon'=>'odi.gif'),

		'pct'  => array ('type'=>'image/pict', 'icon'=>'image.gif'),
		'pdf'  => array ('type'=>'application/pdf', 'icon'=>'pdf.gif'),
		'php'  => array ('type'=>'text/plain', 'icon'=>'text.gif'),
		'pic'  => array ('type'=>'image/pict', 'icon'=>'image.gif'),
		'pict' => array ('type'=>'image/pict', 'icon'=>'image.gif'),
		'png'  => array ('type'=>'image/png', 'icon'=>'image.gif'),
		'pps'  => array ('type'=>'application/vnd.ms-powerpoint', 'icon'=>'powerpoint.gif'),
		'ppt'  => array ('type'=>'application/vnd.ms-powerpoint', 'icon'=>'powerpoint.gif'),
		'ps'   => array ('type'=>'application/postscript', 'icon'=>'pdf.gif'),
		'qt'   => array ('type'=>'video/quicktime', 'icon'=>'video.gif'),
		'ra'   => array ('type'=>'audio/x-realaudio', 'icon'=>'audio.gif'),
		'ram'  => array ('type'=>'audio/x-pn-realaudio', 'icon'=>'audio.gif'),
		'rhb'  => array ('type'=>'text/xml', 'icon'=>'xml.gif'),
		'rm'   => array ('type'=>'audio/x-pn-realaudio', 'icon'=>'audio.gif'),
		'rtf'  => array ('type'=>'text/rtf', 'icon'=>'text.gif'),
		'rtx'  => array ('type'=>'text/richtext', 'icon'=>'text.gif'),
		'sh'   => array ('type'=>'application/x-sh', 'icon'=>'text.gif'),
		'sit'  => array ('type'=>'application/x-stuffit', 'icon'=>'zip.gif'),
		'smi'  => array ('type'=>'application/smil', 'icon'=>'text.gif'),
		'smil' => array ('type'=>'application/smil', 'icon'=>'text.gif'),
		'sqt'  => array ('type'=>'text/xml', 'icon'=>'xml.gif'),
		'svg'  => array ('type'=>'image/svg+xml', 'icon'=>'image.gif'),
		'svgz' => array ('type'=>'image/svg+xml', 'icon'=>'image.gif'),
		'swa'  => array ('type'=>'application/x-director', 'icon'=>'flash.gif'),
		'swf'  => array ('type'=>'application/x-shockwave-flash', 'icon'=>'flash.gif'),
		'swfl' => array ('type'=>'application/x-shockwave-flash', 'icon'=>'flash.gif'),

		'sxw'  => array ('type'=>'application/vnd.sun.xml.writer', 'icon'=>'odt.gif'),
		'stw'  => array ('type'=>'application/vnd.sun.xml.writer.template', 'icon'=>'odt.gif'),
		'sxc'  => array ('type'=>'application/vnd.sun.xml.calc', 'icon'=>'odt.gif'),
		'stc'  => array ('type'=>'application/vnd.sun.xml.calc.template', 'icon'=>'odt.gif'),
		'sxd'  => array ('type'=>'application/vnd.sun.xml.draw', 'icon'=>'odt.gif'),
		'std'  => array ('type'=>'application/vnd.sun.xml.draw.template', 'icon'=>'odt.gif'),
		'sxi'  => array ('type'=>'application/vnd.sun.xml.impress', 'icon'=>'odt.gif'),
		'sti'  => array ('type'=>'application/vnd.sun.xml.impress.template', 'icon'=>'odt.gif'),
		'sxg'  => array ('type'=>'application/vnd.sun.xml.writer.global', 'icon'=>'odt.gif'),
		'sxm'  => array ('type'=>'application/vnd.sun.xml.math', 'icon'=>'odt.gif'),

		'tar'  => array ('type'=>'application/x-tar', 'icon'=>'zip.gif'),
		'tif'  => array ('type'=>'image/tiff', 'icon'=>'image.gif'),
		'tiff' => array ('type'=>'image/tiff', 'icon'=>'image.gif'),
		'tex'  => array ('type'=>'application/x-tex', 'icon'=>'text.gif'),
		'texi' => array ('type'=>'application/x-texinfo', 'icon'=>'text.gif'),
		'texinfo'  => array ('type'=>'application/x-texinfo', 'icon'=>'text.gif'),
		'tsv'  => array ('type'=>'text/tab-separated-values', 'icon'=>'text.gif'),
		'txt'  => array ('type'=>'text/plain', 'icon'=>'text.gif'),
		'wav'  => array ('type'=>'audio/wav', 'icon'=>'audio.gif'),
		'wmv'  => array ('type'=>'video/x-ms-wmv', 'icon'=>'avi.gif'),
		'asf'  => array ('type'=>'video/x-ms-asf', 'icon'=>'avi.gif'),
		'xdp'  => array ('type'=>'application/pdf', 'icon'=>'pdf.gif'),
		'xfd'  => array ('type'=>'application/pdf', 'icon'=>'pdf.gif'),
		'xfdf' => array ('type'=>'application/pdf', 'icon'=>'pdf.gif'),
		'xls'  => array ('type'=>'application/vnd.ms-excel', 'icon'=>'excel.gif'),
		'xml'  => array ('type'=>'application/xml', 'icon'=>'xml.gif'),
		'xsl'  => array ('type'=>'text/xml', 'icon'=>'xml.gif'),
		'zip'  => array ('type'=>'application/zip', 'icon'=>'zip.gif')
	);
	if (isset($mimetype_list[$type]) && is_array($mimetype_list[$type]) && isset($mimetype_list[$type]['type'])) {
		return $mimetype_list[$type];
	} else {
		return array ('type'=>'application/octet-stream', 'icon'=>'unknown.gif');
	}
}
?>