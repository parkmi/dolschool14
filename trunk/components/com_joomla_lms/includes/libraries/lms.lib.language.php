<?php
/**
* libraries/lms.lib.language.php
* JoomaLMS eLearning Software http://www.joomlalms.com/
* * * (c) ElearningForce Inc - http://www.elearningforce.biz/
**/

// no direct access
defined( '_JLMS_EXEC' ) or die( 'Restricted access' );

function JLMS_cp1251_to_utf8($s) {
	$t = '';
	$c209 = chr(209); $c208 = chr(208); $c129 = chr(129);
	for($i=0; $i<strlen($s); $i++) {
		$c=ord($s[$i]);
		if ($c>=192 and $c<=239) $t.=$c208.chr($c-48);
		elseif ($c>239) $t.=$c209.chr($c-112);
		elseif ($c==184) $t.=$c209.$c209;
		elseif ($c==168) $t.=$c208.$c129;
		else $t.=$s[$i];
	}
	return $t;
}
function JLMS_utf8_to_cp1251($s) {
	$out = '';
	for ($c=0;$c<strlen($s);$c++) {
		$i=ord($s[$c]);
		if ($i<=127) $out.=$s[$c];
		if ($byte2) {
			$new_c2=($c1&3)*64+($i&63);
			$new_c1=($c1>>2)&5;
			$new_i=$new_c1*256+$new_c2;
			if ($new_i==1025) {
				$out_i=168;
			} else {
				if ($new_i==1105) {
					$out_i=184;
				} else {
					$out_i=$new_i-848;
				}
			}
			$out.=chr($out_i);
			$byte2=false;
		}
		if (($i>>5)==6) {
			$c1=$i;
			$byte2=true;
		}
	}
	return $out;
}

function JLMS_require_lang( &$JLMS_LANGUAGE, $section, $language, $client='frontend' ) {	 
	static $ex_sections = array();
	static $sec_languages = array();
	
	if($client == 'frontend'){
		$path_lang_dir = JPATH_SITE . DS . "components" . DS . "com_joomla_lms" . DS . 'languages';
	} else 
	if($client == 'backend'){
		$path_lang_dir = JPATH_SITE . DS . "administrator" . DS . "components" . DS . "com_joomla_lms". DS . 'language';
	} 
	
	if (is_array($section)) {
		foreach ($section as $so) {
			if ($so && !in_array($so,$ex_sections)) {
				if (file_exists($path_lang_dir.DS."english".DS.$so.".php")) {
					require( $path_lang_dir.DS."english".DS.$so.".php");
					$ex_sections[] = $so;
					$sec_languages[$so] = 'english';
					if ($language && $language != 'english') {
						if (file_exists( $path_lang_dir.DS.$language.DS.$so.".php")) {
							require($path_lang_dir.DS.$language.DS.$so.".php");
							$sec_languages[$so] = $language;
						}
					}
				}
			//} elseif (in_array($so,$ex_sections) && $language && $language != 'english' && $sec_languages[$so] != $language) {
			// fix 17.08.2007 (DEN)
			} elseif (in_array($so,$ex_sections) && $language && $sec_languages[$so] != $language) {
				if (file_exists($path_lang_dir.DS.$language.DS.$so.".php")) {
					require($path_lang_dir.DS.$language.DS.$so.".php");
					$sec_languages[$so] = $language;
				}
			}
		}
	} elseif ($section) {		
		if (!in_array($section,$ex_sections)) {	
			if (file_exists($path_lang_dir.DS."english".DS.$section.".php")) {				
				require($path_lang_dir.DS."english".DS.$section.".php");
				$ex_sections[] = $section;
				$sec_languages[$section] = 'english';				
				if ($language && $language != 'english') {
					if (file_exists($path_lang_dir.DS.$language.DS.$section.".php")) {					
						require($path_lang_dir.DS.$language.DS.$section.".php");
						$sec_languages[$section] = $language;
					}
				}
			}
		//} elseif (in_array($section,$ex_sections) && $language && $language != 'english' && $sec_languages[$section] != $language) {
		// fix 17.08.2007 (DEN)
		} elseif (in_array($section,$ex_sections) && $language && $sec_languages[$section] != $language) {			
			if (file_exists($path_lang_dir.DS.$language.DS.$section.".php")) {
				require($path_lang_dir.DS.$language.DS.$section.".php");
				$sec_languages[$section] = $language;
			}
		}
	}
}

function JLMS_processLanguage( &$JLMS_LANGUAGE, $force_utf = false, $client = 'frontend' ) {
	$JLMS_CONFIG = & JLMSFactory::GetConfig();	

	$do_utf = false;
	$utf_method = '';
	$iso_enc = 'utf-8';
	if (defined('_ISO')) {
		$iso_enc = '';
		$iso = explode( '=', _ISO );
		if (isset($iso[1]) && $iso[1]) {
			$iso_enc = $iso[1];
		}
	}
	if ($force_utf) {
		$iso_enc = 'utf-8';
	}
	if ($iso_enc && (strtolower($iso_enc) === 'utf-8' || strtolower($iso_enc) === 'utf8')) {
		$cur_lang = strtolower($JLMS_CONFIG->get('default_language', 'english'));
		if (substr($cur_lang, -4) === '_utf') {
		} else {
			$sup_iso_languages_pre = array('danish', 'french', 'german', 'italian', 'norwegian', 'spanish', 'dutch', 'brazilian');
			$sup_iso_languages = $JLMS_CONFIG->get('iso88591_compat_languages', $sup_iso_languages_pre);
			if (in_array($cur_lang, $sup_iso_languages)) {
				if (function_exists('utf8_encode')) {
					$do_utf = true;
					$utf_method = 'utf8_encode';
				}
				if ($cur_lang == 'german' && $client == 'backend') {
					$utf_method = 'skip_encoding';
				}
			} elseif ($cur_lang == 'russian') {
				$do_utf = true;
				$utf_method = 'cp1251_manual';
			} elseif ($cur_lang == 'bulgarian') {
				$do_utf = true;
				$utf_method = 'cp1251_manual';
			}
		}
	}
	foreach ($JLMS_LANGUAGE as $jl_key => $jl_value) {
		if (!defined($jl_key)) {
			if ($do_utf && $utf_method) {
				if ($utf_method == 'utf8_encode') {
					$jl_value = utf8_encode($jl_value);
				} elseif ($utf_method == 'cp1251_manual') {
					$jl_value = JLMS_cp1251_to_utf8($jl_value);
				}
			}
			define($jl_key, $jl_value);
		}
	}
	$isWindows = (substr(PHP_OS, 0, 3) == 'WIN');
	if ($isWindows) {
		if (isset($JLMS_LANGUAGE['_JLMS_LOCALE_WIN']) && $JLMS_LANGUAGE['_JLMS_LOCALE_WIN']) {
			setlocale(LC_TIME, $JLMS_LANGUAGE['_JLMS_LOCALE_WIN']);
		}
	} else {
		if (isset($JLMS_LANGUAGE['_JLMS_LOCALE']) && $JLMS_LANGUAGE['_JLMS_LOCALE']) {
			setlocale(LC_TIME, $JLMS_LANGUAGE['_JLMS_LOCALE']);
		}
	}
}
?>