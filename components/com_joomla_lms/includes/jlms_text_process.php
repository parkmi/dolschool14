<?php
/**
* includes/jlms_text_process.php
* (c) JoomaLMS eLearning Software http://www.joomlalms.com/
**/

defined( '_JLMS_EXEC' ) or die( 'Restricted access' );

function JLMS_ProcessText_LowFilter($text) {
	return $text;
	$iFilter_flex = new JLMS_InputFilter( null, null, 1, 1);
/* Default black lists:
	var $tagBlacklist = array ('applet', 'body', 'bgsound', 'base', 'basefont', 'embed', 'frame', 'frameset', 'head', 'html', 'id', 'iframe', 'ilayer', 'layer', 'link', 'meta', 'name', 'object', 'script', 'style', 'title', 'xml');
	var $attrBlacklist = array ('action', 'background', 'codebase', 'dynsrc', 'lowsrc'); // also will strip ALL event handlers
*/
	$tagWhiteList = array('embed', 'id', 'object');
	$attrWhiteList = array('background', 'codebase');
	$tagBlackList = $iFilter_flex->tagBlacklist;
	$attrBlackList = $iFilter_flex->attrBlacklist;
	$tagBlackList_new = array();
	foreach ($tagBlackList as $tagBlack) {
		if (!in_array($tagBlack, $tagWhiteList)) {
			$tagBlackList_new[] = $tagBlack;
		}
	}
	$attrBlackList_new = array();
	foreach ($attrBlackList as $attrBlack) {
		if (!in_array($attrBlack, $attrWhiteList)) {
			$attrBlackList_new[] = $attrBlack;
		}
	}
	$iFilter_flex->tagBlacklist = $tagBlackList_new;//array ('applet', 'body', 'bgsound', 'base', 'basefont', 'frame', 'frameset', 'head', 'html', 'id', 'iframe', 'ilayer', 'layer', 'link', 'meta', 'name', 'script', 'style', 'title', 'xml');
	$iFilter_flex->attrBlacklist = $attrBlackList_new;//array ('action', 'dynsrc', 'lowsrc');
	$new_text = $iFilter_flex->process( $text );
	return $new_text;
}

function JLMS_ProcessText_HardFilter($text) {
	$iFilter = new JLMS_InputFilter( null, null, 1, 1);
	$new_text = $iFilter->process( $text );
	return $new_text;
}

/**
 * 13.04.2007 - DEN
 * function to process Names of all LMS items (ex. course names, lpath names ....)
 * 
 * @param string $text - input text
 * @return string - output text
 */
function JLMS_Process_ContentNames($text) {
	$new_text = preg_replace("[\<|\>]", '', $text);
	$new_text = htmlspecialchars($new_text, ENT_QUOTES);
	$new_text = str_replace('&amp;','&', $new_text);
	return $new_text;
}

function JLMS_getParam_LowFilter(&$arr, $name, $def = '') {
	if (isset($arr[$name])) {
		$value = get_magic_quotes_gpc() ? stripslashes( $arr[$name] ) : $arr[$name]; 
		return $value;
	} else {
		return $def;
	}
}

function JLMS_ShowText_WithFeatures($text, $force_compatibility = false, $process_js_separately = false){
	// Black list of mambots:
	$banned_bots = array();
	if ($process_js_separately) { $force_compatibility = true; }
	if ($force_compatibility) {
		/* Fix of the excellent :) "EOLAS - no click to activate" plugin */
		// 26.02.2007 - function "writethis(jsval);" generates opening of new window during processing DATA within LP Ajax procedures (document.write() fails)
		$banned_bots[] = strtolower('botgznoclicktoactivate');

		/* Ban the Joomla email cloack mambot */
		// 06.12.2007 - this mambot generates document.write() js code
		$banned_bots[] =  strtolower('botMosEmailCloak');
		$banned_bots[] =  strtolower('plgContentEmailCloak');
		$banned_bots[] =  strtolower('plgEmailCloak');
		if (class_exists('JURI')) {
			// Joomla 1.5.x
			$base   = JURI::base(true).'/';
			$protocols = '[a-zA-Z0-9]+:'; //To check for all unknown protocols (a protocol must contain at least one alpahnumeric fillowed by :
	      	$regex     = '#(src|href)="(?!/|'.$protocols.'|\#|\')([^"]*)"#m';
	        $text    = preg_replace($regex, "$1=\"$base\$2\"", $text);

	        // Background image
			$regex 		= '#style\s*=\s*[\'\"](.*):\s*url\s*\([\'\"]?(?!/|'.$protocols.'|\#)([^\)\'\"]+)[\'\"]?\)#m';
			$text 	= preg_replace($regex, 'style="$1: url(\''. $base .'$2$3\')', $text);

			// OBJECT <param name="xx", value="yy"> -- fix it only inside the <param> tag
			$regex 		= '#(<param\s+)name\s*=\s*"(movie|src|url)"[^>]\s*value\s*=\s*"(?!/|'.$protocols.'|\#|\')([^"]*)"#m';
			$text 	= preg_replace($regex, '$1name="$2" value="' . $base . '$3"', $text);

			// OBJECT <param value="xx", name="yy"> -- fix it only inside the <param> tag
			$regex 		= '#(<param\s+[^>]*)value\s*=\s*"(?!/|'.$protocols.'|\#|\')([^"]*)"\s*name\s*=\s*"(movie|src|url)"#m';
			$text 	= preg_replace($regex, '<param value="'. $base .'$2" name="$3"', $text);

			// OBJECT data="xx" attribute -- fix it only in the object tag
			$regex = 	'#(<object\s+[^>]*)data\s*=\s*"(?!/|'.$protocols.'|\#|\')([^"]*)"#m';
			$text 	= preg_replace($regex, '$1data="' . $base . '$2"$3', $text);
	    }
	}
	$known_bots = array();
	/* strtolower fix for PHP4 */
	if ($process_js_separately) {
		$known_bots[] =  strtolower('botmgmediabot210');
		$known_bots[] =  strtolower('botJCEUtilities');
		$known_bots[] =  strtolower('botJceUtilities');
		$known_bots[] =  strtolower('plgSystemJCEUtilities');
		$known_bots[] =  strtolower('plgContentAvreloaded'); // J1.5 native plugin
		$known_bots[] =  strtolower('plgContentjlmsSqueezeBox'); // J1.5 native plugin
		$known_bots[] =  strtolower('plgContentjlmsCoursesContentLoader'); // J1.5 native plugin
		$known_bots[] =  strtolower('botLLschedule1');
		$known_bots[] =  strtolower('botLLschedule');
		$known_bots[] =  strtolower('plgContentRokbox');
		$known_bots[] =  strtolower('plgSystemRokBox');
		$known_bots[] =  strtolower('plgContentJw_allvideos');
		#$known_bots[] =  strtolower('plgContentjlmsSqueezeBox');
	}
	$is_avreloaded = false;

	$js_separately_processed_mambots = false;
	
	$_JLMS_PLUGINS = & JLMSFactory::getPlugins();
	$_JLMS_PLUGINS->loadBotGroup('system');
	$arg_params = array();
	$arg_params[] = & $text;
	$_JLMS_PLUGINS->trigger('onPreparePageText', $arg_params);

	$row = new stdclass();
	$row->text = $text;
	$row->introtext = '';
	$params = new JLMSParameters('');
	$new_text = $text;	
	
	JPluginHelper::importPlugin('content');
	$dispatcher	= JDispatcher::getInstance();
	
	if ($force_compatibility) 
	{
		$proceed_force_compat = true;
		if (method_exists($dispatcher, 'getObservers')) {
			$onPrepareContent_bots = $dispatcher->getObservers();//$_MAMBOTS->_events['onPrepareContent'];
		} elseif (is_array($dispatcher->get('_observers')) && $dispatcher->get('_observers') ) {
			$onPrepareContent_bots = $dispatcher->get('_observers');
		} else {
			$proceed_force_compat = false;
		}
		if ($proceed_force_compat) {
			$onPrepareContent_bots_allowed = array();
			foreach ($onPrepareContent_bots as $oPCb) {
				if (is_array($oPCb) && isset($oPCb['event']) && $oPCb['event'] == 'onPrepareContent' && isset($oPCb['handler']) && in_array(strtolower($oPCb['handler']), $banned_bots) ) {
					if (method_exists($dispatcher, 'detach')) {
						$dispatcher->detach( $oPCb );
					}
				} else {
					if (is_array($oPCb) && isset($oPCb['event']) && $oPCb['event'] == 'onPrepareContent' && isset($oPCb['handler']) && in_array(strtolower($oPCb['handler']), $known_bots)) {
						$js_separately_processed_mambots = true;
					} elseif (is_object($oPCb) && in_array(strtolower(get_class($oPCb)), $known_bots)) {
						$js_separately_processed_mambots = true;
						if (strtolower(get_class($oPCb)) == strtolower('plgContentAvreloaded')) {
							$is_avreloaded = true;
						}
					}
					$onPrepareContent_bots_allowed[] = $oPCb;
				}
			}
			if (method_exists($dispatcher, 'attach')) {
				//$dispatcher->attach( $onPrepareContent_bots_allowed );
			}
		}
	}
	
	
	$results = $dispatcher->trigger('onPrepareContent', array (& $row, & $params, 0));
	$new_text = $row->text;
	
	//echo '<pre>';
	//print_r($new_text);
	//echo '</pre>';

	if ($process_js_separately) {
		//ajax pages...
		//trigger on AfterDispatch in order for system plugins to add required js/css into document head
		// ... we will parse head later (below in this function) to extract required things from there
		$app = & JFactory::getApplication();
		$app->triggerEvent('onAfterDispatch');
	}

	/**
	 * JLMS topic readmore replacing
	 */
		global $JLMS_CONFIG, $JLMS_topic_readmore_closeTag, $JLMS_replace_step, $JLMS_count_begin_tags, $JLMS_count_end_tags, $JLMS_topic_readmore_js;
		$JLMS_topic_readmore_closeTag = 0;
		$JLMS_replace_step = 0;
		$JLMS_count_begin_tags = 0;
		$JLMS_count_end_tags = 0;
		
		$doc = & JFactory::getDocument();
		
		$js_readmore = "";
		$js_readmore .= "
			function toogleReadmore(){
				var toogle_readmore = $$('.toogle_readmore');
				toogle_readmore.removeEvents('click');
				toogle_readmore.addEvents({
					'click': function(){
						var next = this.getNext();
						if((next.tagName == 'DIV' || next.tagName == 'SPAN') && next.hasClass('topic_readmore')){
						";
						
						if( JLMS_mootools12() ){		
						$js_readmore .= "
							var next_Fx_show = new Fx.Morph(next, {
								duration: 500, 
								transition: Fx.Transitions.Sine.easeInOut,
								onStart: function(){
									this.element.setStyles({'display': 'block', 'opacity': 0});
								},
								onComplete: function(){
									this.element.setStyles({'display': 'block'});
								}
							});	
							var next_Fx_hide = new Fx.Morph(next, {
								duration: 500, 
								transition: Fx.Transitions.Sine.easeInOut,
								onComplete: function(){
									this.element.setStyles({'display': 'none'});
								}
							});	
							if(next.getStyle('display') == 'block'){
								hideReadmore(next, next_Fx_hide);
							} else {
								showReadmore(next, next_Fx_show);
							}						
						";
							
						} else {
						$js_readmore .=	"
							var next_Fx_show = next.effects({
								duration: 500, 
								transition: Fx.Transitions.Sine.easeInOut,
								onStart: function(){
									this.element.setStyles({'display': 'block', 'opacity': 0});
								},
								onComplete: function(){
									this.element.setStyles({'display': 'block'});
								}
							});	
							var next_Fx_hide = next.effects({
								duration: 500, 
								transition: Fx.Transitions.Sine.easeInOut,
								onComplete: function(){
									this.element.setStyles({'display': 'none'});
								}
							});	
							if(next.getStyle('display') == 'block'){
								hideReadmore(next, next_Fx_hide);
							} else {
								showReadmore(next, next_Fx_show);
							}
						";
						}
						$js_readmore .= "
						}
					}
				});
			}
			function showReadmore(el, Fx){
				Fx.start({
					'opacity': [0, 1]
				});	
			}
			function hideReadmore(el, Fx){
				Fx.start({
					'opacity': [1, 0]
				});
			}
			window.addEvent('domready', function(){
				toogleReadmore();
			});
		";
		$domready = "";
		//$domready .= $js_readmore;
		$domready .= "
			toogleReadmore();
		";
		if(!isset($JLMS_topic_readmore_js) || !$JLMS_topic_readmore_js){
			$doc->addScriptDeclaration($js_readmore);
			//$JLMS_CONFIG->set('web20_domready_code', $JLMS_CONFIG->get('web20_domready_code','').$domready);
			$JLMS_topic_readmore_js = true;
		}
		
		if(preg_match_all('#\{readmore([^}]*)\}#', $new_text, $out)){
			$js_separately_processed_mambots = true;
			
			$count_begin = count($out[0]);
			$JLMS_count_begin_tags = $count_begin;
			$JLMS_topic_readmore_closeTag = 1;
			$new_text = preg_replace_callback('#\{readmore([^}]*)\}#', 'callbackFnBegin', $new_text);	
		}
		if(preg_match_all('#\{\/readmore\}#', $new_text, $out)){
			$count_end = count($out[0]);
			$JLMS_count_end_tags = $count_end;
			if($count_begin >= $count_end){
				$JLMS_topic_readmore_closeTag = 1;
				$replace = '<div class="clr"><!-- --></div></div>' . "\n";
				$new_text = str_replace('{/readmore}', $replace, $new_text);
			} else 
			if($count_begin < $count_end){
				$JLMS_topic_readmore_closeTag = 0;
				$new_text = preg_replace_callback('#\{\/readmore\}#', 'callbackFnEnd', $new_text);
			}
		}
	/**
	 * JLMS topic readmore replacing
	 */
	
	/*
	 *	17.12.2007 (DEN)
	 *	Processing of MGMediabot2 JS's in IE.
	 *	We should extract all javascripts from content and execute them separately.
	 */
	
	//b Events JLMS Plugins
	$_JLMS_PLUGINS = & JLMSFactory::getPlugins();
	$_JLMS_PLUGINS->loadBotGroup('system');
	$_JLMS_PLUGINS->trigger('onTextProcess', array(&$new_text));
	//e Events JLMS Plugins
	
	
	/* !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
	 * 03.01.2007:
	 * BAD-IDEA (we don't know the list of used mambots and their syntax) - idea: Before starting lpath, we should analize list of available mambots and preload their JS libs at the lpath starting page!
	 *
	 */
	if ($process_js_separately) {
		
		$document = & JFactory::getDocument();
		$document->addScriptDeclaration($JLMS_CONFIG->get('web20_domready_code'));
		
		if ($js_separately_processed_mambots) {			
			$nn = '';
			
			//if (!$is_avreloaded) { // ignore included libraries if avreloaded is used (to avoid reloading of swfobject)
				$document=& JFactory::getDocument();
				$scripts = $document->_scripts;
				foreach ($document->_scripts as $strSrc => $strType) {
					/*if (strpos($strSrc, 'swfobject.js') === false) {
						//this is not swfobject.js library
					} elseif (strpos($strSrc, 'mootools.js') === false) {
						//this is not mootools library
					} else {
						$nn .= '<script type="'.$strType.'" src="'.$strSrc.'"></script>';
					}*/
					
					//fix for !J1.7
					if(is_array($strType) && isset($strType['mime'])){
						$strType = $strType['mime'];
					}
					
					$known_lib = false;
					if (strpos($strSrc, 'swfobject.js') !== false) {
						//do not include this lib ! (in order to avoid issues with avreloaded)
					} elseif (strpos($strSrc, 'mootools.js') !== false) {
						//do not include this lib !
					} elseif (strpos($strSrc, 'rokbox') !== false) {
						//roxbox plugins (system + content ) - provide a possibility to open links in 'lightbox' similar way
						$known_lib = true;
					} elseif(strpos($strSrc, 'jwplayer.js') || strpos($strSrc, 'silverlight.js') || strpos($strSrc, 'wmvplayer.js') || strpos($strSrc, 'AC_QuickTime.js')){
						$known_lib = true;
					}
					if ($known_lib) {
						$nn .= '<script type="'.$strType.'" src="'.$strSrc.'"></script>';
					}
	
				}
				foreach ($document->_script as $strType => $strSrc) {
					$nn .= '<script type="'.$strType.'">'.$strSrc.'</script>';
				}
			//}
						 
			$new_text = $nn. $new_text;			
	
			$js = '';
			$tags=array('`<script[^>]*>(.*)</script>`isU'); # <script ...>...</script> areas
			$replacements=array(); # Storage for the elements found to be processed
			foreach(array_keys($tags) as $idx) { # Handle all kings of tag areas and tags, one by one
				$tmptags=array(); # Storage for the found occurrences
				preg_match_all($tags[$idx],$new_text,$tmptags); # And here they are
				$js1 = '';
				$js1_count = 0;
				$js2 = '';
				$js2_count = 0;
				if ($tmptags) { # Found some?
					if (isset($tmptags[0])) {
						$tmptags = $tmptags[0];
						foreach ($tmptags as $tmptag) {
							preg_match('`<script type="text/javascript" src="(.*)"></script>`isU', $tmptag, $matches2);# <script...src="(#our_content#)"...></script> areas
							if (isset($matches2[1])) {
								$js1_count ++;
								$js1 .= "\r\n"."dhtmlLoadScript(\"".$matches2[1]."\");"."\r\n";
							} else {
								preg_match('`<script[^>]*>(.*)</script>`isU', $tmptag, $matches2);# <script...>(#our_content#)</script> areas
								if (isset($matches2[1])) {
									$js2_count ++;
									if(false) {//} && preg_match_all('#\/\/jlms_exec_js_start\/\/([^<]*)\/\/jlms_exec_js_end\/\/#m', $matches2[1], $out, PREG_PATTERN_ORDER)>0){
										$js2 .= $out[1][0];	
									} else {
										$new_js = $matches2[1];
										$new_js = str_replace('jQuery(document).ready(','jQuery.isReady = true; jQuery(document).ready(', $new_js);
										if (strpos($new_js, "addEvent('domready'")) {
											$rnd = "".rand(1,1000).time();
											$new_js = str_replace("addEvent('domready'","addEvent('domready_jlms_ajax".$rnd."'", $new_js);
											$new_js .= "\r\n"."window.fireEvent('domready_jlms_ajax".$rnd."');window.removeEvents('domready_jlms_ajax".$rnd."');";
										}
										$js2 .= "\r\n".$new_js."\r\n";	
									}
								}
							}
							$new_text=str_replace($tmptag,'',$new_text);
						}
					}
				}
				if ($js1_count) {
					$js .= "\r\n" . "function OtherScriptsShouldBeExecuted(){"
						. "\r\n\t" . "scripts_already_loaded ++;"
						. "\r\n\t" . "if (scripts_already_loaded > ($js1_count - 1) ){"
						. "\r\n\t\t" . "if(!other_scripts_executed){"
						. "\r\n\t\t\t" . "other_scripts_executed = 1;"
						. "\r\n\t\t\t" . $js2
						. "\r\n\t\t" . "}"
						. "\r\n\t" . "}"
						. "\r\n" . "}"
						. "\r\n";
					$js .= "\r\n" . 'function dhtmlLoadScript(url) {'
						. "\r\n\t" . 'var e = document.createElement("script");'
						. "\r\n\t" . 'e.src = url;e.type="text/javascript";'
						. "\r\n\t" . 'e.onreadystatechange= function () {'
						. "\r\n\t\t" . 'if (this.readyState == "loaded") {'
						. "\r\n\t\t\t" . 'OtherScriptsShouldBeExecuted();'
						. "\r\n\t\t" . '}'
						. "\r\n\t" . '}'
						. "\r\n\t" . 'e.onload = OtherScriptsShouldBeExecuted;document.getElementsByTagName("head")[0].appendChild(e);'
						. "\r\n" . '}' . "\r\n";
					$js .= "var scripts_already_loaded = 0;"
						. "\r\n" . "other_scripts_executed = 0;"
	//						. "\r\n" . "scripts_already_loaded = $js1_count;"
	//						. "\r\n" . "OtherScriptsShouldBeExecuted();" . "\r\n"
	//						. "\r\n" . "setTimeout('scripts_already_loaded = $js1_count;OtherScriptsShouldBeExecuted();', 1000);" . "\r\n"
						. "\r\n" . "setTimeout(function(){scripts_already_loaded = $js1_count;OtherScriptsShouldBeExecuted();}, 1000);" . "\r\n"
						;
	
					$js .= $js1;
				} else {
					$js .= $js2;
				}
				unset($tmptags); # A bit of dirty work
			}
			
			$js = str_replace('<![CDATA[', '<!--', $js);
			$js = str_replace(']]>', '-->', $js);
			$new_text = str_replace('<![CDATA[', '<!--', $new_text);
			$new_text = str_replace(']]>', '-->', $new_text);
			$ret_ar = array();
			$ret_ar['new_text'] = &$new_text;
			$ret_ar['js'] = &$js;
			return $ret_ar;
		} else {
			$new_text = str_replace('<![CDATA[', '<!--', $new_text);
			$new_text = str_replace(']]>', '-->', $new_text);
		}
		$ret_ar = array();
		$ret_ar['new_text'] = &$new_text;
		$ret_ar['js'] = '';//$js;
		return $ret_ar;
	} else {
		return $new_text;
	}
	return $new_text;
}

/**
 * JLMS topic readmore replacing
 * callback function
 */
function callbackFnBegin($maths){
	$return = '';
	if(isset($maths[0])){
		$search = $maths[0];
		$readmore_text = JText::_('Readmore...');
		if(isset($maths[1])){
//			$subject = strip_tags($maths[1]);
			$subject = $maths[1];
			
			preg_match('#title=\"(.*)\"#', $subject, $out);
			if(isset($out[0]) && $out[1]){
				$readmore_text = JText::_($out[1]);
			}
		}
		$return = '<div class="clr"><!-- --></div><div class="toogle_readmore"><a class="readon" href="javascript:void(0);">'.$readmore_text.'</a><div class="clr"><!-- --></div></div><div class="topic_readmore">' . "\n";
	}
	return $return;
}
function callbackFnEnd($maths){
	global $JLMS_replace_step, $JLMS_cout_begin_tags;
	$return = '';
	if($JLMS_cout_begin_tags > $JLMS_replace_step){
		$return = '<div class="clr"><!-- --></div></div>' . "\n";
	} 
	$JLMS_replace_step++;
	return $return;
}

/**
 * 28.02.2007 - DEN
 * Function replaces all linebreaks to <br />
 *
 * @param string $string - input string with linebreaks
 * @return string - output string with <br /> instead of any linebreak
 */
function JLMS_nl2br($string) {
	$string = str_replace(array("\r\n", "\r", "\n"), "<br />", $string);
	return $string;
}

/**
 * 28.02.2007 - DEN
 * Function return string, which is safe for overlib windows.
 * exampe of use this string in html code:
 * 		 echo "<a onmouseover='overlib(\"".JLMS_txt2overlib($description)."\", ......
 * !!! ordering of quotes is important
 * 
 * @param string $string -- bad string with double quotes and linebreakes (\n,\r)
 * @return string -- well-formed string for overlib using
 */
function JLMS_txt2overlib($string){
	$string = JLMS_nl2br($string);
	$string = str_replace('\"','\\\\"',$string);
	$string = str_replace('"','\"',$string);
	$string = str_replace("'","&#039;",$string);
	$string = str_replace('\&quot;','\\\\\"',$string);
	$string = str_replace('&quot;','\"',$string);
	return $string;
}

function JLMS_processCSVField($field_text) {
	$field_text = trim(strip_tags($field_text));
	$field_text = str_replace( '&#039;', "'", $field_text );
	$field_text = str_replace( '&#39;', "'", $field_text );
	$field_text = str_replace('&quot;',  '"', $field_text );
	$field_text = str_replace( '"', '""', $field_text );
	if (function_exists('html_entity_decode')) {
		$field_text = @html_entity_decode($field_text, ENT_QUOTES, 'UTF-8'); 
	} elseif (function_exists('get_html_translation_table')) {
		$trans_tbl = get_html_translation_table(HTML_ENTITIES, ENT_QUOTES);
		$trans_tbl = array_flip($trans_tbl);
		$field_text = strtr($field_text, $trans_tbl);
	}
	$field_text = preg_replace('~&#x([0-9a-f]+);~ei', 'chr(hexdec("\\1"))', $field_text); 
   	$field_text = preg_replace('~&#([0-9]+);~e', 'chr("\\1")', $field_text);
	$field_text = '"'.$field_text.'"';
	return $field_text;
}

function jlms_UTF8string_check($string) {
	return preg_match('%(?:
		[\xC2-\xDF][\x80-\xBF]
		|\xE0[\xA0-\xBF][\x80-\xBF]
		|[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}
		|\xED[\x80-\x9F][\x80-\xBF]
		|\xF0[\x90-\xBF][\x80-\xBF]{2}
		|[\xF1-\xF3][\x80-\xBF]{3}
		|\xF4[\x80-\x8F][\x80-\xBF]{2}
		)+%xs', $string);
}

function jlms_UTF8string_substr($str, $offset, $length = NULL) {

    if ( $offset >= 0 && $length >= 0 ) {

        if ( $length === NULL ) {
            $length = '*';
        } else {
            if ( !preg_match('/^[0-9]+$/', $length) ) {
                trigger_error('utf8_substr expects parameter 3 to be long', E_USER_WARNING);
                return '';//FALSE;
            }

            $strlen = strlen(utf8_decode($str));
            if ( $offset > $strlen ) {
                return '';
            }

            if ( ( $offset + $length ) > $strlen ) {
               $length = '*';
            } else {
                $length = '{'.$length.'}';
            }
        }

        if ( !preg_match('/^[0-9]+$/', $offset) ) {
            trigger_error('utf8_substr expects parameter 2 to be long', E_USER_WARNING);
            return '';//FALSE;
        }

        $pattern = '/^.{'.$offset.'}(.'.$length.')/us';

        preg_match($pattern, $str, $matches);

        if ( isset($matches[1]) ) {
            return $matches[1];
        }

        return '';//FALSE;

    } else {

        // Handle negatives using different, slower technique
        // From: http://www.php.net/manual/en/function.substr.php#44838
        preg_match_all('/./u', $str, $ar);
        if( $length !== NULL ) {
            return join('',array_slice($ar[0],$offset,$length));
        } else {
            return join('',array_slice($ar[0],$offset));
        }
    }
}

function jlms_string_substr($str, $offset, $length = NULL) {
	if (jlms_UTF8string_check($str)) {
		return jlms_UTF8string_substr($str, $offset, $length);
	} else {
		return substr($str, $offset, $length);
	}
}
function php4_utf8_urldecode($str) {
	preg_match_all('/%u([[:alnum:]]{4})/', $str, $a);

	foreach ($a[1] as $uniord) {
		$dec = hexdec($uniord);
		$utf = '';

		if ($dec < 128) {
			$utf = chr($dec);
		} else if ($dec < 2048) {
			$utf = chr(192 + (($dec - ($dec % 64)) / 64));
			$utf .= chr(128 + ($dec % 64));
		} else {
			$utf = chr(224 + (($dec - ($dec % 4096)) / 4096));
			$utf .= chr(128 + ((($dec % 4096) - ($dec % 64)) / 64));
			$utf .= chr(128 + ($dec % 64));
		}

		$str = str_replace('%u'.$uniord, $utf, $str);
	}

	return urldecode($str);
}
/**
/* documentation for html_entity_decode() states that
/* "Support for multi-byte character sets was added at PHP 5.0.0" so this might not work for PHP 4
**/
function php5_utf8_urldecode($str) {
	$str = preg_replace("/%u([0-9a-f]{3,4})/i","&#x\\1;",urldecode($str));
	return html_entity_decode($str,null,'UTF-8');
}

function JLMS_initialize_SqueezeBox($scorm_box = true) {
	JLMS_SqueezeBox('jlms', $scorm_box);
}
function JLMS_SqueezeBox_getDomready($scorm_box = true, $fire = false) 
{
	JLMS_SqueezeBox('jlms', $scorm_box);
}
function JLMS_SqueezeBox_getDomreadyFire($scorm_box = true) {
	return JLMS_SqueezeBox('fire', $scorm_box);
}

function JLMS_initialize_SqueezeBox_AutoLoad() {	
	JLMS_SqueezeBox('autoload', true);
}

function JLMS_SqueezeBox( $type = 'jlms', $scormBox = false ) 
{
	global $JLMS_CONFIG;
	static $called;
	
	$app = & JFactory::getApplication();
	$doc = & JFactory::getDocument();
	$base = JURI::base();
	$js = '';
	
	if($type == 'jlms' && $scormBox){
		$type_tmp = 'jlms_scorm';
	} else {
		$type_tmp = $type;
	}
	if( isset($called[$type_tmp]) ) 
	{
		return '';
	} else {
		$called[$type_tmp] = true;
	}
		
	if( $type == 'autoload' ) 
	{					
		$js = "		
		    jlmsSqueezeBox_scorm.initialize({'closeWithOverlay':false});		    
		    var class_z = 'zoom_text';
		    if($('scorm_sqbox_autoload').getChildren()[0] && $('scorm_sqbox_autoload').getChildren()[0].tagName == 'IMG'){
					class_z = 'zoom_img';
				}
				
			new Element('img', {'class': class_z, 'src': '".$base."/components/com_joomla_lms/lms_images/sbox_modal/sbox_zoomLink.png'}).injectInside($('scorm_sqbox_autoload'));
			jlmsSqueezeBox_scorm.fromElement($('scorm_sqbox_autoload'), {parse: 'rel'});		
		"; 
	} 
	else if( $type == 'joomla') 
	{ 
		JHTML::_('behavior.modal');
		return true; 
	} else {
				
		$class = ($scormBox ? 'scorm_modal' : 'jlms_modal');
			
		$fireJs = '';		
		if( $type == 'fire' ) 
		{
			if( JLMS_mootools12() ) {				
				$fireJs = "$$('a.".$class."')[0].fireEvent('click');";
			} else {
				$fireJs = ( $scormBox ? 'jlmsSqueezeBox_scorm' : 'jlmsSqueezeBox').".fromElement($$('a.".$class."')[0]);";
			}
		}
		
		if( JLMS_mootools12() ) {		
			$js = "
				$$('a.".$class."').removeEvents('click');	
				".( $scormBox ? 'jlmsSqueezeBox_scorm' : 'jlmsSqueezeBox').".assign($$('a.".$class."'), {
					parse: 'rel'
				});		    
			    $$('a.".$class."').each(function(el){	    	
					var class_z = 'zoom_text';
					if(el.getChildren()[0]){
						if(el.getChildren()[0].tagName == 'IMG'){
							class_z = 'zoom_img';
						}
					}
					new Element('img', {'class': class_z, 'src': '".$base."/components/com_joomla_lms/lms_images/sbox_modal/sbox_zoomLink.png'}).injectInside(el);
				});		
				$fireJs
			";
		} else {
			$js = "	
			    $$('a.".$class."').each(function(el){
					el.removeEvents('click').addEvent('click', function(e){
						new Event(e).stop();
						".( $scormBox ? 'jlmsSqueezeBox_scorm' : 'jlmsSqueezeBox').".fromElement(el);
					});	    	
					var class_z = 'zoom_text';
					if(el.getChildren()[0]){
						if(el.getChildren()[0].tagName == 'IMG'){
							class_z = 'zoom_img';
						}
					}
					new Element('img', {'class': class_z, 'src': '".$base."/components/com_joomla_lms/lms_images/sbox_modal/sbox_zoomLink.png'}).injectInside(el);
				});	
				$fireJs
			";
		}
		
	}		
	
	$cssFile = "components/com_joomla_lms/lms_css/sbox_modal.css";
	
	if( JLMS_mootools12() ) {			
		if( $scormBox ){			
			$jsFile = "components/com_joomla_lms/includes/js/sbox_scorm16.js";
		} else {
			$jsFile = "components/com_joomla_lms/includes/js/sbox_modal16.js";
		}
	} else {		 
		if( $scormBox ){
			$jsFile = "components/com_joomla_lms/includes/js/sbox_scorm.js";
		} else {
			$jsFile = "components/com_joomla_lms/includes/js/sbox_modal.js";
		}		
	}
	
	$cssFile .= '?token='.$JLMS_CONFIG->getVersionToken();
	$jsFile .= '?token='.$JLMS_CONFIG->getVersionToken();
	
	$doc->addStyleSheet( $base.$cssFile );	
	$doc->addScript( $base.$jsFile );
	
	if(JRequest::getVar('option', '') == 'com_joomla_lms'){		
		$JLMS_CONFIG->set('web20_domready_code', $JLMS_CONFIG->get('web20_domready_code','').$js);
	} else {
		$dom_ready_js = "
			window.addEvent('domready', function(){
				".$js."
			});
		";	
		$doc->addScriptDeclaration( $dom_ready_js );
	}
	if($type == 'fire'){
		return $js;
	}
}
?>