<?php
/**
* joomla_lms.scorm.php
* (c) JoomaLMS eLearning Software http://www.joomlalms.com/
**/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

require_once(_JOOMLMS_FRONT_HOME . "/includes/n_scorm/lms_scorm.class.php");
require_once(_JOOMLMS_FRONT_HOME . "/includes/n_scorm/lms_scorm.lib.php");

$id 	= intval( mosGetParam( $_REQUEST, 'id', 0 ) );
$task 	= mosGetParam( $_REQUEST, 'task', '' );
if ($task == 'player_scorm') {
	global $JLMS_CONFIG, $Itemid, $option;
	$course_id = $JLMS_CONFIG->get('course_id',0);
	$pathway = array();
	$pathway[] = array('name' => _JLMS_PATHWAY_HOME, 'link' => sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid"), 'is_home' => true);
	$pathway[] = array('name' => $JLMS_CONFIG->get('course_name'), 'link' => sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid&amp;task=details_course&amp;id=$course_id"), 'is_course' => true);
	$pathway[] = array('name' => _JLMS_TOOLBAR_LPATH, 'link' => sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid&amp;task=learnpaths&amp;id=$course_id"));
	JLMSAppendPathWay($pathway);
}
switch ($task) {

	case 'player_scorm':
		/*$do_wait_redirect = intval(mosGetParam($_REQUEST, 'srw',0));
		if (!empty($_POST)) {
			$do_wait_redirect = 3;
			//do not redirect, because POST will be erased
		}
		if ($do_wait_redirect == 3) {*/
			JLMS_playerSCORM( $id, $option );
		/*} else {
			global $Itemid;
			//redirect to the same page after 1 second...in order for:
				// 1. have a time for datamodel request to post tracking information of the prestep
				// 2. redirect instead of just 'sleep' - to place this script in php/apache queue after the tracking one
			sleep(1);
			$url_params = array();
			$url_params['option'] = $option;
			$url_params['Itemid'] = $Itemid;
			$url_params['task'] = 'player_scorm';
			$url_params['id'] = intval(mosGetParam($_REQUEST, 'id',0));
			$url_params['course_id'] = intval(mosGetParam($_REQUEST, 'course_id',0));
			$url_params['lpath_id'] = intval(mosGetParam($_REQUEST, 'lpath_id',0));
			$url_params['currentorg'] = strval(mosGetParam($_REQUEST, 'currentorg',''));
			$url_params['newattempt'] = strval(mosGetParam($_REQUEST, 'newattempt',''));
			$url_params['mode'] = strval(mosGetParam($_REQUEST, 'mode',''));
			$url_params['scoid'] = intval(mosGetParam($_REQUEST, 'scoid',0));
			$url_params['height'] = intval(mosGetParam($_REQUEST, 'height',0));
			$url_params['int_skip_resume'] = intval(mosGetParam($_REQUEST, 'int_skip_resume',0));
			$url_params['srw'] = intval(mosGetParam($_REQUEST, 'srw',0)) + 1;
			$redirect_url = 'index.php?';
			$glue_str = '';
			foreach ($url_params as $url_param_name => $url_param_val) {
				if ($url_param_val) {
					$redirect_url .= $glue_str.$url_param_name.'='.$url_param_val;
					$glue_str = '&';
				}
			}
			JLMSRedirect( sefRelToAbs($redirect_url) );
		}*/
	break;

	case 'loadsco_scorm':	JLMS_LoadSCOSCORM( $option );		break;
	case 'api_scorm':		JLMS_APISCORM( $option );			break;
	case 'datamodel_scorm':	JLMS_DatamodelSCORM( $option );		break;
}

function JLMS_DatamodelSCORM( $option ) {
	global $JLMS_DB, $Itemid, $JLMS_CONFIG;
	$user = JLMSFactory::getUser();
	$user_id = $user->get('id');
	if (!$user_id) {
		$ssid = strval(mosGetParam($_REQUEST, 'ssid',''));
		if ($ssid) {
			$ssid_parts = explode('_', $ssid);
			if (count($ssid_parts) == 2) {
				$pre_userid = isset($ssid_parts[0]) ? $ssid_parts[0] : 0;
				$pre_hash = isset($ssid_parts[1]) ? $ssid_parts[1] : 'xxxxx';
				$user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
				$hash_check = md5($pre_userid.'_'.$user_agent);
				if ($hash_check == $pre_hash) {
					$user_id = $pre_userid;
				}
			}
		}
	}
	if (!$user_id) {
		$log_scorm_id = intval(mosGetParam($_REQUEST, 'id', 0)) ? intval(mosGetParam($_REQUEST, 'id', 0)) : intval(mosGetParam($_REQUEST, 'scorm_id', 0));
		$log_course_id = $JLMS_CONFIG->get('course_id') ? $JLMS_CONFIG->get('course_id') : intval(mosGetParam($_REQUEST, 'course_id', 0));
		JLMSErrorLog::writeSCORMLog('SCORM tracking error - user not logged in (session hash check failed)', $log_course_id, $log_scorm_id);
		exit();
	}
	$id = intval(mosGetParam($_REQUEST, 'id', 0));
	$skip_resume = intval(mosGetParam($_REQUEST, 'skip_resume', 0));
	if ($id) {
		$query = "SELECT * FROM #__lms_n_scorm WHERE id = $id";
		$JLMS_DB->SetQuery($query);
		$scorm = $JLMS_DB->LoadObject();
		if (is_object($scorm)) {
			$scoid = intval(mosGetParam($_REQUEST, 'scoid',0));
			$attempt = intval(mosGetParam($_REQUEST, 'attempt',0));

			if ($scoid) {
				$result = true;
				$request = null;
				// (DEN)
				//if (has_capability('mod/scorm:savetrack', get_context_instance(CONTEXT_MODULE,$cm->id))) {
				if (true) {


					foreach ($_POST as $element => $val_post) {
						$element = str_replace('__','.',$element);
						// (DEN) (i'm insert 'get_magic_quotes_gpc'). 15.03.2007
						$value = (get_magic_quotes_gpc()) ? stripslashes( $val_post ) : $val_post ;
						if (substr($element,0,3) == 'cmi') {
							$element = preg_replace('/N(\d+)/',".\$1",$element);
							$result = scorm_insert_track($user_id, $scorm->id, $scoid, $attempt, $element, $value) && $result;
						}
						if (substr($element,0,15) == 'adl.nav.request') {
							// SCORM 2004 Sequencing Request
							require_once( _JOOMLMS_FRONT_HOME .'/includes/n_scorm/datamodels/scorm_13lib.php');

							$search = array('@continue@', '@previous@', '@\{target=(\S+)\}choice@', '@exit@', '@exitAll@', '@abandon@', '@abandonAll@');
							$replace = array('continue_', 'previous_', '\1', 'exit_', 'exitall_', 'abandon_', 'abandonall');
							$action = preg_replace($search, $replace, $value);

							if ($action != $value) {
								require_once( _JOOMLMS_FRONT_HOME .'/includes/n_scorm/datamodels/sequencinglib.php');
								// Evaluating navigation request
								$valid = scorm_seq_overall ($scoid,$user_id,$action);

								// Set valid request
								$search = array('@continue@', '@previous@', '@\{target=(\S+)\}choice@');
								$replace = array('true', 'true', 'true');
								$matched = preg_replace($search, $replace, $value);
								if ($matched == 'true') {
									$request = 'adl.nav.request_valid["'.$action.'"] = "'.$valid.'";';
								}
							}
						}
					}

					/* 23 November 2007 (DEN) LMS resuming fix (for 'by the best attemp' tracking method */
					global $JLMS_CONFIG;
					$course_params = $JLMS_CONFIG->get('course_params');
					$params = new JLMSParameters($course_params);
					if ($params->get('track_type',0) == 1 && !$skip_resume) { //by the best attempt + skip_resume (24march2010)
						if ($attempt > 1) {
							$query = "SELECT * FROM #__lms_n_scorm_scoes_track WHERE userid = '$user_id' AND scormid = '$scorm->id' AND scoid = '$scoid' AND attempt = '".($attempt-1)."'";
							$JLMS_DB->SetQuery($query);
							$prev_tracks = $JLMS_DB->LoadObjectList();
							foreach($prev_tracks as $prev_track) {
								$query = "SELECT count(*) FROM #__lms_n_scorm_scoes_track WHERE userid = '$user_id' AND scormid = '$scorm->id' AND scoid = '$scoid' AND attempt = '$attempt' AND element = '$prev_track->element'";
								$JLMS_DB->SetQuery($query);
								$ssss = $JLMS_DB->LoadResult();
								if (!$ssss) {
									$track = new stdClass();
							        $track->userid = $user_id;
							        $track->scormid = $scorm->id;
							        $track->scoid = $scoid;
							        $track->attempt = $attempt;
							        $track->element = $prev_track->element;
							        $track->value = $prev_track->value;
							        $track->timemodified = $prev_track->timemodified;
							        $JLMS_DB->InsertObject('#__lms_n_scorm_scoes_track', $track, 'id');
								}
							}
						}
						//TODO: do not resume SCORM if it is played as LPath step and LPath was restarted !!!
					}
					/* End of LMS resuming fix */
				}
				if ($result) {
					echo "true\n0";
				} else {
					echo "false\n101";
				}
				if ($request != null) {
					echo "\n".$request;
				}
			}
		}
	}
	exit();
}

function JLMS_APISCORM( $option ) {
	global $JLMS_DB, $my, $Itemid;
	$id = intval(mosGetParam($_REQUEST, 'id', 0));
	$course_id = intval(mosGetParam($_REQUEST, 'course_id', 0));
	$skip_resume = intval(mosGetParam($_REQUEST, 'skip_resume', 0));
	if ($id) {
		$query = "SELECT * FROM #__lms_n_scorm WHERE id = $id";
		$JLMS_DB->SetQuery($query);
		$scorm = $JLMS_DB->LoadObject();
		if (is_object($scorm)) {
			$scoid = intval(mosGetParam($_REQUEST, 'scoid',0));
			$attempt = intval(mosGetParam($_REQUEST, 'attempt',0));
			$mode = strval(mosGetParam($_REQUEST, 'mode',''));

			$track_attempt = $attempt;
			if ($track_attempt > 1) {
				global $JLMS_CONFIG;
				$course_params = $JLMS_CONFIG->get('course_params');
				$params = new JLMSParameters($course_params);
				if ($params->get('track_type',0) == 1) { //by the best attaempt
					$track_attempt--;
				}
			}
			if ($skip_resume) {
				$userdata->status = '';
				$userdata->score_raw = '';
			} else {
				if ($usertrack=scorm_get_tracks($scoid, $my->id, $track_attempt)) {
					if ((isset($usertrack->{'cmi.exit'}) && ($usertrack->{'cmi.exit'} != 'time-out')) || ($scorm->version != "SCORM_1.3")) {
						$userdata = $usertrack;
					} else {
						$userdata->status = '';
						$userdata->score_raw = '';
					}
				} else {
					$userdata->status = '';
					$userdata->score_raw = '';
				}
			}

			$userdata->student_id = addslashes($my->username);
			$userdata->student_name = addslashes($my->name);
			$userdata->mode = 'normal';
			if ($mode) {
				$userdata->mode = $mode;
			}
			if ($userdata->mode == 'normal') {
				$userdata->credit = 'credit';
			} else {
				$userdata->credit = 'no-credit';
			}
			if ($scodatas = scorm_get_sco($scoid, SCO_DATA)) {
				foreach ($scodatas as $key => $value) {
					$userdata->$key = $value;
				}
			} else {
				// (DEN)
				//error('Sco not found');
				//echo 'SCO not found';
			}
			//$scorm->version = strtolower(clean_param($scorm->version, PARAM_SAFEDIR));   // Just to be safe
			$scorm->version = strtolower(preg_replace('/[^a-zA-Z0-9_-]/i', '', $scorm->version));
			if (file_exists( _JOOMLMS_FRONT_HOME .'/includes/n_scorm/datamodels/'.$scorm->version.'.js.php')) {
				include_once( _JOOMLMS_FRONT_HOME .'/includes/n_scorm/datamodels/'.$scorm->version.'.js.php');
			} else {
				include_once( _JOOMLMS_FRONT_HOME .'/includes/n_scorm/datamodels/scorm_12.js.php');
			}
			/*if (file_exists($CFG->dirroot.'/mod/scorm/datamodels/'.$scorm->version.'.js.php')) {
				include_once($CFG->dirroot.'/mod/scorm/datamodels/'.$scorm->version.'.js.php');
			} else {
				include_once($CFG->dirroot.'/mod/scorm/datamodels/scorm_12.js.php');
			}*/
			?>
			
var errorCode = "0";
function underscore(str) {
    str = str.replace(/.N/g,".");
    return str.replace(/\./g,"__");
}
<?php
		}
	}
	exit();
}


function JLMS_LoadSCOSCORM( $option ) {
	global $JLMS_DB, $my, $Itemid;
	$JLMS_CONFIG = & JLMSFactory::getConfig();
	$id = intval(mosGetParam($_REQUEST, 'id', 0));

	$delayseconds = 20;  // Delay time before sco launch, used to give time to browser to define API
	$delayseconds_nojs = 2;
	// if API were defined earlier than timer is passed - SCO will be launched

	if ($id) {
		$query = "SELECT * FROM #__lms_n_scorm WHERE id = $id";
		$JLMS_DB->SetQuery($query);
		$scorm = $JLMS_DB->LoadObject();
		if (is_object($scorm)) {
			$scoid = intval(mosGetParam($_REQUEST, 'scoid',0));
			if (!empty($scoid)) {
				//
				// Direct SCO request
				//
				if ($sco = scorm_get_sco($scoid)) { // (DEN) check if this $scoid from our SCORM !!!!
					if ($sco->launch == '') {
						// Search for the next launchable sco
						$query = "SELECT * FROM #__lms_n_scorm_scoes WHERE scorm = $scorm->id AND launch <> '' AND id > $sco->id ORDER BY id ASC";
						$JLMS_DB->SetQuery($query);
						$scoes = $JLMS_DB->LoadObjectList();
						//if ($scoes = get_records_select('scorm_scoes','scorm='.$scorm->id." AND launch<>'' AND id>".$sco->id,'id ASC')) {
						if (!empty($scoes)) {
							$sco = current($scoes);
						}
					}
				}
			}
			//
			// If no sco was found get the first of SCORM package
			//
			if (!isset($sco)) {
				$query = "SELECT * FROM #__lms_n_scorm_scoes WHERE scorm = $scorm->id AND launch <> '' ORDER BY id ASC";
				$JLMS_DB->SetQuery($query);
				$scoes = $JLMS_DB->LoadObjectList();
				//$scoes = get_records_select('scorm_scoes','scorm='.$scorm->id." AND launch<>''",'id ASC');
				$sco = current($scoes);
			}
			if (!empty($sco)) {
				if ($sco->scormtype == 'asset') {
					$attempt = scorm_get_last_attempt($scorm->id, $my->id);
					$element = $scorm->version == 'scorm_13'?'cmi.completion_status':'cmi.core.lesson_status';
					$value = 'completed';
					$result = scorm_insert_track($my->id, $scorm->id, $sco->id, $attempt, $element, $value);
				}
			}

			//
			// Forge SCO URL
			//
			$connector = '';
			$version = substr($scorm->version,0,4);
			if ((isset($sco->parameters) && (!empty($sco->parameters))) || ($version == 'AICC')) {
				/**
				 * 06.10.2007 (DEN) "''." - is added for compatibility with Joomla compatibility :)) library compat.php50x.php (on line 105 in PHP 4.4.7 there was a notice)
				 */
				if (stripos(''.$sco->launch,'?') !== false) {
					$connector = '&';
				} else {
					$connector = '?';
				}
				if ((isset($sco->parameters) && (!empty($sco->parameters))) && ($sco->parameters[0] == '?')) {
					$sco->parameters = substr($sco->parameters,1);
				}
			}
			
			if ($version == 'AICC') {
				if (isset($sco->parameters) && (!empty($sco->parameters))) {
					$sco->parameters = '&'. $sco->parameters;
				}
				//$launcher = $sco->launch.$connector.'aicc_sid='.$my->id.'&aicc_url='.$CFG->wwwroot.'/mod/scorm/aicc.php'.$sco->parameters;
				$launcher = $sco->launch.$connector.'aicc_sid='.$my->id.'&aicc_url='.$JLMS_CONFIG->get('live_site')."/index.php?option=$option&Itemid=$Itemid&task=aicc_task&course_id=$course_id".$sco->parameters;
				// (DEN) check this URL /\ !!!!!!!!!
			} else {
				if (isset($sco->parameters) && (!empty($sco->parameters))) {
					$launcher = $sco->launch.$connector.$sco->parameters;
				} else {
					$launcher = $sco->launch;
				}
			}

			$query = "SELECT * FROM #__lms_scorm_packages WHERE id = $scorm->scorm_package";
			$JLMS_DB->SetQuery($query);
			$scorm_ref = $JLMS_DB->LoadObject();
			//$reference = $CFG->dataroot.'/'.$courseid.'/'.$reference;
			//$row->reference = _JOOMLMS_SCORM_FOLDER_PATH . "/" . $scorm_ref->package_srv_name;
			$reference_folder = $JLMS_CONFIG->get('live_site') . "/" . _JOOMLMS_SCORM_PLAYER . "/" . $scorm_ref->folder_srv_name;
			//$reference_folder = _JOOMLMS_SCORM_FOLDER_PATH . "/" . $scorm_ref->folder_srv_name;

			// (DEN) we don't use external links nor repositry (but maybe...maybe...)
			/*if (scorm_external_link($sco->launch)) {
				// Remote learning activity
				$result = $launcher;
			} else if ($scorm->reference[0] == '#') {
				// Repository
				require_once($repositoryconfigfile);
				$result = $CFG->repositorywebroot.substr($scorm->reference,1).'/'.$sco->launch;
			} else {*/
			if (true) {
				// (DEN) we don't use external packages
				/*if ((basename($scorm->reference) == 'imsmanifest.xml') && scorm_external_link($scorm->reference)) {
					// Remote manifest
					$result = dirname($scorm->reference).'/'.$launcher;
				} else {*/
				if (true) {
					// Moodle internal package/manifest or remote (auto-imported) package
					//if (basename($scorm->reference) == 'imsmanifest.xml') {
					if (basename($reference_folder) == 'imsmanifest.xml') {
						//$basedir = dirname($scorm->reference);
						$basedir = dirname($reference_folder);
					} else {
						$basedir = $reference_folder;//$CFG->moddata.'/scorm/'.$scorm->id;
					}
					/*if ($CFG->slasharguments) {
						$result = $CFG->wwwroot.'/file.php/'.$scorm->course.'/'.$basedir.'/'.$launcher;
					} else {
						$result = $CFG->wwwroot.'/file.php?file=/'.$scorm->course.'/'.$basedir.'/'.$launcher;
					}*/
					$result = $reference_folder . '/' . $launcher;

			// determine the name of the API variable, which are we looking for
		    $LMS_api = ($scorm->version == 'scorm_12' || $scorm->version == 'SCORM_1.2' || empty($scorm->version)) ? 'API' : 'API_1484_11'; 

			if (isset($sco->scormtype) && strtolower($sco->scormtype) == 'asset') {
				$delayseconds = 2;// if resource is 'asset' - we don't need SCORM API
			}
?>
<html>
    <head>
        <title>LoadSCO</title>
        <script type="text/javascript">
        //<![CDATA[
		var delaySeconds = <?php echo $delayseconds ?>

		function findscormAPI(win) {
			var findAPITries = 0;
			while ((win.<?php echo $LMS_api; ?> == null) && (win.parent != null) && (win.parent != win)) {
				findAPITries++;
				if (findAPITries > 7) { // we don't have more than 7 nested window objects....
					return null;
				}
				win = win.parent;
			}
			return win.<?php echo $LMS_api; ?>;
		}

		function getscormAPI() {
			var theAPI = findscormAPI(window);
			if ((theAPI == null) && (window.opener != null) && (typeof(window.opener) != "undefined")) {
				theAPI = findscormAPI(window.opener);
			}
			if (theAPI == null) {
				return null;
			}
			return theAPI;
		}

		function try_redirect() {
			if (getscormAPI() == null) {
				delaySeconds = delaySeconds - 1;
				if (delaySeconds < 0) {
					setTimeout('do_window_redirect();',1000);
				} else {
					setTimeout('try_redirect();',1000);
				}
			} else {
				setTimeout('do_window_redirect();',1000);
			}
		}
		function do_window_redirect() {
			document.location = "<?php echo $result ?>";
		}
        //]]>
        </script>
        <noscript>
            <meta http-equiv="refresh" content="<?php echo $delayseconds_nojs ?>;url=<?php echo $result ?>" />
        </noscript> 
    </head>
    <body onload="try_redirect();">
        <br /><br /><center><img src="<?php echo $JLMS_CONFIG->get('live_site'); ?>/components/com_joomla_lms/lms_images/loading.gif" height="32" width="32" border="0" alt="loading" /></center>
    </body>
</html>
<?php
				}
			}
		}
	}
	die;
}



function JLMS_playerSCORM( $id, $option ) {
	global $JLMS_DB, $my, $JLMS_CONFIG, $Itemid, $JLMS_SESSION;
	$course_id = $JLMS_CONFIG->get('course_id');
	$usertype = $JLMS_CONFIG->get('current_usertype', 0);
	$lpath_id = intval(mosGetParam($_REQUEST, 'lpath_id',0));
	$int_skip_resume = intval(mosGetParam($_REQUEST, 'int_skip_resume',0));
	$scorm = null;//new stdClass();
	if ( $course_id && ($usertype == 1 || $usertype == 2) && (JLMS_CheckSCORM($id, $lpath_id, $course_id, $scorm, $usertype)) ) {
		if (!empty($scorm)) {
			// 11.04.2007 (parameter 'redirect_to_learnpath' used to avoid redirect cycles from course home to LP and back
			$JLMS_SESSION->clear('redirect_to_learnpath');

			// sleep one second. SCORM could send some requests on onCloasPage event. We need wait for these requests to be processed.
			#sleep(2);

			$scoid = intval(mosGetParam($_REQUEST, 'scoid',0));
    		$mode = 'normal';//strval(mosGetParam($_REQUEST, 'mode','normal'));//optional_param('mode', 'normal', PARAM_ALPHA);
    		$currentorg = strval(mosGetParam($_REQUEST, 'currentorg', ''));//, PARAM_RAW);
    		$newattempt = strval(mosGetParam($_REQUEST, 'newattempt', 'on'));

			require_once( _JOOMLMS_FRONT_HOME . "/includes/n_scorm/lms_scorm.play.php");

			$query = "SELECT * FROM #__lms_learn_paths WHERE id = '".$lpath_id."' AND course_id = '".$course_id."'";
			$JLMS_DB->SetQuery( $query );
			$lpath_data = $JLMS_DB->LoadObjectList();
			$box_view = false;
			if (count($lpath_data) == 1) {
				$pathway = array();
				$pathway[] = array('name' => $lpath_data[0]->lpath_name, 'link' => sefRelToAbs("index.php?option=$option&amp;Itemid=$Itemid&amp;task=player_scorm&amp;id=$id&amp;course_id=$course_id&amp;lpath_id=".$lpath_data[0]->id));
				JLMSAppendPathWay($pathway);
			}
			if (isset($scorm->params)) {
				$params = new JLMSParameters($scorm->params);
				if ($params->get('scorm_layout', 0) && $params->get('scorm_layout', 0) == 1) {
					$box_view = true;
				}
			}

			if ($box_view) {
				// SqueezeBox view - don't show Heading.
				echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
				echo '<html xmlns="http://www.w3.org/1999/xhtml" style="margin:0;padding:0"><head>';
				$iso_site = 'charset=utf-8';
				if (defined('_ISO')) {
					$iso_site = _ISO;
					if (!$iso_site) {
						$iso_site = 'charset=utf-8';
					}
				}
				echo '<meta http-equiv="Content-Type" content="text/html; '.$iso_site.'" />';
				echo '</head><body style="margin:0;padding:0">';
			} else {
				JLMS_ShowHeading();
			}

			/*JLMS_TMPL::OpenMT();

			$hparams = array();
			JLMS_TMPL::ShowHeader('scorm', $scorm->scorm_name, $hparams);
*/
			$skip_resume = $int_skip_resume ? true : false;
			JLMS_SCORM_PLAY_MAIN( $scorm, $option, $course_id, $lpath_id, $scoid, $mode, $currentorg, $newattempt, false, false, $box_view, $skip_resume );
			if ($box_view) {
				echo '</body></html>';
				JLMS_die();
			}
			//JLMS_TMPL::CloseMT();
		} else {
			JLMSRedirect( sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=learnpaths&id=$course_id") );
		}
	} else {
		JLMSRedirect( sefRelToAbs("index.php?option=$option&Itemid=$Itemid&task=learnpaths&id=$course_id") );
	}
}
?>