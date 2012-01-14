<?php
//defined( '_VALID_MOS' ) or defined( '_JEXEC' ) or die( 'Restricted access' );
/*if (!defined( '_VALID_MOS' )){
	define('_VALID_MOS', 1);
}*/
?>
<style type="text/css" >
div.it_test_error {
	border: 1px solid #DDBBBB;
	background-color: #FFDDDD;
	margin-bottom:3px;
	padding:3px;
	background: #FFDDDD;
	text-align: left;
}
span.it_txt_error {
	color:red;
	font-weight:bold;
}
</style>
<?php
$callback_filepath = dirname(__FILE__);
$is_under_backend = strpos($callback_filepath, 'administrator');
if ($is_under_backend) {
	if (!class_exists('JToolBarHelper')) { ?>
		<table class="adminheading">
		<tr>
			<th>
				JoomlaLMS: Error Page
			</th>
		</tr>
		</table> 
	<?php } elseif (class_exists('JToolBarHelper')) { 
		JToolBarHelper::title( 'JoomlaLMS: Error Page', 'systeminfo.png' );
	}
} else {
	echo '<div class="componentheading">JoomlaLMS: Error Page</div>';
}
if (!defined('_JOOMLMS_ADMIN_HOME')) { define('_JOOMLMS_ADMIN_HOME', dirname(__FILE__));}
if (!class_exists('mosMenuBar') && defined( '_JEXEC' ) && class_exists('JLoader') && $is_under_backend) {
	if (!class_exists('JToolbarHelper')) {
		JLoader::load('JToolbarHelper');
	}
	$version = new JVersion();
	if ($version->RELEASE >= '1.6') {
		class mosMenuBar extends JToolbarHelper
		{
			static function startTable() { return; }
			static function endTable() { return; }
			static function addNew() { return; }
			static function addNewX() { return; }
			static function saveedit() { return; }
			static function save() { return; }
			static function publishList() { return; }
			static function unpublishList() { return; }
			static function deleteList() { return; }
			static function editList() { return; }
			static function editListX() { return; }
			static function apply() { return; }
			static function cancel() { return; }
			static function custom() { return; }
			static function customX() { return; }
			static function divider() { return; }
			static function makeDefault() { return; }
		}
	} else {
		class mosMenuBar extends JToolbarHelper
		{
			function startTable() { return; }
			function endTable() { return; }
			function addNew() { return; }
			function addNewX() { return; }
			function saveedit() { return; }
			function save() { return; }
			function publishList() { return; }
			function unpublishList() { return; }
			function deleteList() { return; }
			function editList() { return; }
			function editListX() { return; }
			function apply() { return; }
			function cancel() { return; }
			function custom() { return; }
			function customX() { return; }
			function divider() { return; }
			function makeDefault() { return; }
		}
	}
	if (!defined('_JLMS_USERS_LIST')) { define('_JLMS_USERS_LIST', 'Error page');}
	if (!defined('_JLMS_CRSS_LIST')) { define('_JLMS_CRSS_LIST', 'Error page');}
	if (!defined('_JLMS_CFG')) { define('_JLMS_CFG', 'Error page');}
	if (!defined('_JLMS_LF_APPEARANCE')) { define('_JLMS_LF_APPEARANCE', 'Error page');}
	if (!defined('_JLMS_MENUM_HOMEPAGE_M')) { define('_JLMS_MENUM_HOMEPAGE_M', 'Error page');}
	if (!defined('_JLMS_SUBS_TBR_LIST')) { define('_JLMS_SUBS_TBR_LIST', 'Error page');}
	if (!defined('_JLMS_PAYS_TBR_LIST')) { define('_JLMS_PAYS_TBR_LIST', 'Error page');}
	if (!defined('_JLMS_PROCS_TBR_PROCS_LIST')) { define('_JLMS_PROCS_TBR_PROCS_LIST', 'Error page');}
	if (!defined('_JLMS_PLGS_TBR_LIST')) { define('_JLMS_PLGS_TBR_LIST', 'Error page');}
	if (!defined('_JLMS_MENUM')) { define('_JLMS_MENUM', '');}
	if (!defined('_JLMS_DELETE')) { define('_JLMS_DELETE', 'Error');}
	if (!defined('_JLMS_SAVE')) { define('_JLMS_SAVE', 'Error');}
	if (!defined('_JLMS_CANCEL')) { define('_JLMS_CANCEL', 'Error');}
	if (!defined('_JLMS_CLOSE')) { define('_JLMS_CLOSE', 'Error');}
	if (!defined('_JLMS_PAYS_EXPORT_TO_PDF')) { define('_JLMS_PAYS_EXPORT_TO_PDF', 'Error');}
	if (!defined('_JLMS_SUBS_TBR_ASSIGN')) { define('_JLMS_SUBS_TBR_ASSIGN', 'Error');}
	if (!defined('_JLMS_SUBS_TBR_RENEW')) { define('_JLMS_SUBS_TBR_RENEW', 'Error');}
}

if (!defined('_JOOMLMS_COMP_NAME')) { define('_JOOMLMS_COMP_NAME', 'JoomlaLMS'); }

function elm_get_serv_name() {
	$serv_name='';
	if (isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST']) {
		$serv_name = explode(':', $_SERVER['HTTP_HOST']);
		$serv_name = $serv_name[0];
	} elseif (isset($_SERVER['SERVER_NAME']) && $_SERVER['SERVER_NAME']) {
		$serv_name = $_SERVER['SERVER_NAME'];
	} else {					
		$pattern = "~^https?://([^/:\s]+)~";
		preg_match_all($pattern, substr_replace(JURI::root(), '', -1, 1), $rez);
		$serv_name = $rez[1][0];
	}
	
	return $serv_name;
}

function ioncube_file_corrupted($filename='') {
	echo '<div class="it_test_error">';
	echo '<span class="it_txt_error">Error occured:</span><br /><br />';
	echo 'The file '.$filename.' is corrupted. Get the latest version of JoomlaLMS and reinstall it.';
	echo '</div>';
	echo_jlms_branding();
}
function echo_jlms_branding() {
	$callback_filepath = dirname(__FILE__);
	$is_under_backend = strpos($callback_filepath, 'administrator');
	if ($is_under_backend) {
		
	} else {
		$branding_option = 0;
		$yes_db = false;
		if (class_exists('JFactory')) {
			$database = & JFactory::getDBO();
			$yes_db = true;
		} else {
			global $database;
			if (is_object($database)) {
				if (method_exists($database, 'query')) {
					$yes_db = true;
				}
			}
		}
		if ($yes_db) {
			$query = "SELECT lms_config_value FROM #__lms_config WHERE lms_config_var = 'branding_option'";
			$database->SetQuery($query);
			$branding_option = $database->LoadResult();
			if ($branding_option) {
				$branding_option = $branding_option - 1;
				if ($branding_option < 0 || $branding_option > 7) {
					$branding_option = 0;
				}
			} else {
				$branding_option = 0;
			}
		}
		
		$new_option = new stdClass();
		$new_option->poweredby = '<a target="_blank" href="http://www.joomlalms.com/">LMS e-learning software by JoomlaLMS</a>';
		$new_option->copyright = 'Copyright &copy; 2006 - '.date('Y');
		$brand_options[] = $new_option;
		$brand_options[] = $new_option;
		$brand_options[] = $new_option;
		$brand_options[] = $new_option;
	
		$new_option = new stdClass();
		$new_option->poweredby = '<a target="_blank" href="http://www.joomlalms.com/">Learning Management System by JoomlaLMS</a>';
		$new_option->copyright = 'Copyright &copy; 2006 - '.date('Y');
		$brand_options[] = $new_option;
		$brand_options[] = $new_option;
		$brand_options[] = $new_option;
		$brand_options[] = $new_option;
		if(!isset($brand_options[$branding_option])) {
			$branding_option = 0;
		}
		echo '<div style="width:100%; text-align:center" align="center">';
			echo $brand_options[$branding_option]->poweredby;
			if ($brand_options[$branding_option]->copyright) {
				echo '<br />';
				echo $brand_options[$branding_option]->copyright;
			}
		echo '</div>';

	}
}
function ioncube_not_installed($filename='', $ext_name='') {
	echo '<div class="it_test_error">';
	echo '<span class="it_txt_error">Error occured:</span><br /><br />';
	echo 'The file <b>'.$filename.'</b> requires the ionCube PHP Loader '.$ext_name.' to be installed by the site administrator.';
	echo '<br /><br />Please run JoomlaLMS installation helper to view detailed information on the problem or request assistance from your ISP or JoomlaLMS support team.';
	echo '</div>';
	echo_jlms_branding();
}

function ioncube_event_handler($err_code, $params) {
	$current_file = $params['current_file'];
	echo '<div class="it_test_error">';
	echo '<span class="it_txt_error">Error occured:</span><br /><br />';

	$msg_contact_support = 'Please contact <a href="mailto:support@joomlalms.com" title="Mail to support">JoomlaLMS Support</a> if you have any questions.';

	$msg_go_to_marea = 'Please go to the <a target="_blank" href="http://www.joomlalms.com/membersarea/" title="JoomlaLMS Members Area">Members Area</a>, download the license file and install it (copy to your site folder).<br />';
	$msg_go_to_marea .= 'You can get more detailed instructions in the Members Area or in our <a target="_blank" href="http://www.joomlalms.com/lms-help/" title="JoomlaLMS Manual">Help/FAQ section</a>.';
	switch ($err_code) {
		case 1:
			echo 'The encoded file <em>`'.$current_file.'`</em> has been corrupted.<br/><br/>Get the latest version of JoomlaLMS and reinstall it.<br />'.$msg_contact_support;
		break;
		case 2:
			echo "The encoded file <em>`$current_file`</em> has reached its expiry time.<br/><br/>You should renew the license.<br />".$msg_contact_support;
		break;
		
		case 3:
			echo "The encoded file <em>`$current_file`</em> has a server restriction and is used on a non-authorised system.<br/><br/>".$msg_contact_support;
		break;
		
		case 4:
			echo "The encoded file <em>`$current_file`</em> is used on a system where the clock is set more than 24 hours before the file was encoded.<br/><br/>".$msg_contact_support;
		break;
		
		case 5:
			echo "The encoded file <em>`$current_file`</em> was encoded with the option that doesn't allow this file to work if untrusted engine extensions are installed, and is used on a system with an unrecognised extension installed.<br/><br/>".$msg_contact_support;
		break;
		
 		case 6:
			echo "The license file required by an encoded script <em>`$current_file`</em> could not be found.<br/><br/>".$msg_go_to_marea.'<br />'.$msg_contact_support;
		break;

		case 7:
			echo "The license file has been altered or incorrect.<br/><br/>".$msg_go_to_marea.'<br />'.$msg_contact_support;
		break;

		case 8:
			echo "The license file has reached its expiry time.<br/><br/>".$msg_go_to_marea.'<br />'.$msg_contact_support;
		break;

		case 9:
			echo "The license file was not matched to the encoded file <em>`$current_file`</em>.<br/><br/>".$msg_go_to_marea.'<br />'.$msg_contact_support;
		break;
		
		case 10:
			echo "The header block of the license file has been altered.<br/><br/>".$msg_go_to_marea.'<br />'.$msg_contact_support;
		break;

		case 11:
			echo 'The license has a server restriction and is used on a non-authorised server.<br/><br/>'.$msg_go_to_marea.'<br />'.$msg_contact_support;
		break;

		case 12:
			echo "The encoded file has been included by a file which is either unencoded or has incorrect properties.<br/><br/>".$msg_contact_support;
		break;

		case 13:
			echo "The encoded file has included a file which is either unencoded or has incorrect properties.<br/><br/>".$msg_contact_support;
		break;

 		case 14:
			echo "The PHP configuration file `php.ini` has either the `--auto-append-file` or `--auto-prepend-file` setting enabled.<br/><br/>".$msg_contact_support;
		break;
	}

	echo '</div>';
	echo_jlms_branding();
}
?>