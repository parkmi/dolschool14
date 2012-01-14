<?php 
/**
* includes/classes/lms.user_agent.php
* Joomla LMS Component
* * * ElearningForce Biz
**/

// no direct access
defined( '_JLMS_EXEC' ) or die( 'Restricted access' );

class JLMS_UserAgent extends JLMSObject {

	var $_agent = '';

	var $_lowerAgent = '';

	var $_accept = '';

	var $_robots = array(
		/* The most common ones. */
		'Googlebot',
		'msnbot',
		'Slurp',
		'Yahoo',
		/* The rest alphabetically. */
		'Arachnoidea',
		'ArchitextSpider',
		'Ask Jeeves',
		'B-l-i-t-z-Bot',
		'Baiduspider',
		'BecomeBot',
		'cfetch',
		'ConveraCrawler',
		'ExtractorPro',
		'FAST-WebCrawler',
		'FDSE robot',
		'fido',
		'geckobot',
		'Gigabot',
		'Girafabot',
		'grub-client',
		'Gulliver',
		'HTTrack',
		'ia_archiver',
		'InfoSeek',
		'kinjabot',
		'KIT-Fireball',
		'larbin',
		'LEIA',
		'lmspider',
		'Lycos_Spider',
		'Mediapartners-Google',
		'MuscatFerret',
		'NaverBot',
		'OmniExplorer_Bot',
		'polybot',
		'Pompos',
		'Scooter',
		'Teoma',
		'TheSuBot',
		'TurnitinBot',
		'Ultraseek',
		'ViolaBot',
		'webbandit',
		'www.almaden.ibm.com/cs/crawler',
		'ZyBorg',
	);

	function __construct($userAgent = null, $accept = null)	{
		$this->match($userAgent, $accept);
	}

	function match($userAgent = null, $accept = null) {
		// Set our agent string.
		if (is_null($userAgent)) {
			if (isset($_SERVER['HTTP_USER_AGENT'])) {
				$this->_agent = trim($_SERVER['HTTP_USER_AGENT']);
			}
		} else {
			$this->_agent = $userAgent;
		}
		$this->_lowerAgent = strtolower($this->_agent);

		// Set our accept string.
		if (is_null($accept)) {
			if (isset($_SERVER['HTTP_ACCEPT'])) {
				$this->_accept = strtolower(trim($_SERVER['HTTP_ACCEPT']));
			}
		} else {
			$this->_accept = strtolower($accept);
		}
	}

	function getAgentString() {
		return $this->_agent;
	}

	function isRobot() {
		 foreach ($this->_robots as $robot) {
			 if (strpos($this->_agent, $robot) !== false) {
				 return true;
			}
		}
		return false;
	}
}
?>