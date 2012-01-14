<?php 
/**
* includes/classes/lms.css.php
* (c) JoomaLMS eLearning Software http://www.joomlalms.com/
**/

defined( 'JPATH_BASE' ) or die( 'Restricted access' );

class JLMSCSS
{	
	function _( $classname, $addclass1 = '', $addclass2 = '', $addclass3 = '' ) 
	{
		$classes = array();
		if ($addclass1) $classes[] = $addclass1;
		if ($addclass2) $classes[] = $addclass2;
		if ($addclass3) $classes[] = $addclass3;
		
		if( JLMS_J16version() ) 
		{
			switch( $classname ) 
			{
				case 'sectiontableentry1':	{ $classes[] = 'sectiontableentry1'; $classes[] = 'odd'; }			break;
				case 'sectiontableentry2':	{ $classes[] = 'sectiontableentry2'; $classes[] = 'even'; }			break;
				case 'sectiontableheader':	{ $classes[] = 'sectiontableheader'; }								break;
				case 'jlmslist':			{ $classes[] = 'jlmslist'; $classes[] = 'category'; }				break;
				case 'jlmslist-footer_td':	{ $classes[] = 'jlmslist-footer_td'; $classes[] = 'table_footer';}	break;
			} 
		} else {
			switch( $classname ) 
			{
				case 'sectiontableentry1':	{ $classes[] = 'sectiontableentry1'; }								break;
				case 'sectiontableentry2':	{ $classes[] = 'sectiontableentry2'; }								break;
				case 'sectiontableheader':	{ $classes[] = 'sectiontableheader'; }								break;
				case 'jlmslist':			{ $classes[] = 'jlmslist'; }										break;
				case 'jlmslist-footer_td':	{ $classes[] = 'jlmslist-footer_td'; }								break;
			} 
		}
		$classes = array_unique($classes);
		if (count($classes)) {
			$res = implode(' ', $classes);
		} else {
			$res = '';
		}
		return $res;
    }

    function tableheadertag() 
	{
		if( JLMS_J16version() ) {
			return 'th';
		} else {
			return 'td';
		}		
	}

	function h2($text = '') {
		if (JLMS_J16version()) {
			return '<h2>'.$text.'</h2>';
		} else {
			return '<div class="contentheading">'.$text.'</div>';
		}
	}
	
	function h2_js($text = 'var_js'){
		ob_start();
		?>
		<script type="text/javascript">
		function JLMSCSS_h2_js(<?php echo $text;?>){
			var js = '<?php echo JLMSCSS::h2('{text}');?>';
			return js.replace('{text}', <?php echo $text;?>);
		}
		</script>
		<?php
		$js_fn = ob_get_contents();
		ob_get_clean();
		
		$js_fn = str_replace('<script type="text/javascript">', '', $js_fn);
		$js_fn = str_replace('</script>', '', $js_fn);
		
		return $js_fn;
	}

	function file() {
		$JLMS_CONFIG = & JLMSFactory::getConfig();
		return 'jlms_107.css?rev='.$JLMS_CONFIG->getVersionToken();
	}
	
	function link() 
	{
		return 	JURI::base().'components/com_joomla_lms/lms_css/'.JLMSCSS::file();	
	}
}
?>