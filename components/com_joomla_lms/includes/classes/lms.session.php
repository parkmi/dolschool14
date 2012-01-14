<?php 

defined( '_JLMS_EXEC' ) or die( 'Restricted access' );

/**
 * This class handles the session initialization, restart
 * and the re-init of a session after redirection to a Shared SSL domain
 *
 */
class JLMS_Session extends JLMSObject {

	var $component_name = "option=com_joomla_lms";
	var $_session_name = 'joomla_lms';
	/**
	 * Initialize the Session environment
	 *
	 */
	function __construct( ) {
		$this->initSession();
	}

	/**
     * Initiate the Session
     *
     */
	function initSession() {
		global $my;
		$user_id = 0;
		if (class_exists('JFactory')) {
			$user =& JFactory::getUser();
			$user_id = $user->get('id');
		} elseif (isset($my->id)) {
			$user_id = $my->id;
		}
		// Some servers start the session before we can, so close those and start again		
		if(!empty($_SESSION)) {
			// 24.12.2007 - don't clear the session in Joomla1.5 !
			//session_write_close();
			//unset( $_SESSION );
		}
		if( empty( $_SESSION )) {
			// Session not yet started!			
			session_name( $this->_session_name );

			if (session_id() == ""){ @session_start(); }
			
			if( !empty($_SESSION) && !empty($_COOKIE[$this->_session_name])) {
				// cookies are disabled ?
			}
		}
		if (empty($_SESSION['jlms_auth_user'])) {
			$_SESSION['jlms_auth_user'] = $user_id;
		} else {
			if( ( @$_SESSION['jlms_auth_user'] != $user_id )) {
				// If the user ID has changed (after logging out)
				// empty the session!
				// 24.12.2007 - Do we need sessions for 'guest' users ???
				// Do we need to clear the session???
			}
		}

	}
		
	//deprecated
	function getSessionId() {
		//deleted		
	}

	function restartSession( $sid = '') {
		
		// Save the session data and close the session
		session_write_close();
		
		// Prepare the new session
		if( $sid != '' ) {
			session_id( $sid );
		}
		session_name( $this->_session_name );
		// Start the new Session.
		session_start();
		
	}

	//deprecated
	function emptySession() {
		//deleted
	}
	 /**
     * Get DATA from session
     */
	function &get($name, $default = null) {
		if (isset($_SESSION[$name])) {
			return $_SESSION[$name];
		}
		return $default;
	}
    /**
     * Save DATA into session
     */
	function set($name, $value) {
		$old = isset($_SESSION[$name]) ?  $_SESSION[$name] : null;
		if (null === $value) {
			unset($_SESSION[$name]);
		} else {
			$_SESSION[$name] = $value;
		}
		return $old;
	}
	/**
	* Check wheter a session value exists
	*/
	function has( $name ) {
		return isset( $_SESSION[$name] );
	}
	/**
	* Unset data from session
	*/
	function clear( $name ) {
		$value	=	null;
		if( isset( $_SESSION[$name] ) ) {
			$value	=	$_SESSION[$name];
			unset( $_SESSION[$name] );
		}
		return $value;
	}
} // end of class session

class JLMSCookie {
	/**
	 * PHP setcookie but smarter and more secure:
	 * //TBD: add domain info in cookie-name
	 *
	 * @param  string  $name
	 * @param  string  $value
	 * @param  int     $expire
	 * @param  string  $path
	 * @param  string  $domain
	 * @param  boolean $secure
	 * @param  boolean $httponly
	 * @return boolean
	 */
	function setcookie( $name, $value = '', $expire = false, $path = null, $domain = null, $secure = false,  $httponly = false ) {
		global $JLMS_CONFIG, $_SERVER;
	
		static $PrivacyHeaderSent		=	false;
	
		if ( ! $PrivacyHeaderSent ) {
			header('P3P: CP="NOI ADM DEV PSAi COM NAV OUR OTRo STP IND DEM"');		// needed for IE6 to accept this cookie in higher security setting.
			$PrivacyHeaderSent			=	true;
		}
	
		$sp								=	session_get_cookie_params();
	
		if ( ( $domain === null ) || ( $path === null ) ) {
			$matches					=	null;
			if ( $JLMS_CONFIG ) {
				$live_ok				=	( preg_match( '#^https?://([^/]+)(.*)#i', $JLMS_CONFIG->get( 'live_site' ), $matches ) );
			} else {
				$live_ok				=	false;
			}
			
		}
		if ( $domain === null ) {
			// handles www and non-www domains: e.g. live_site = 'site.com' but on 'www.site.com' (or the other way around):
			// in that case, cookie-domain needs to be '.site.com':
			if ( $live_ok ) {
				$pageDomain				=	$_SERVER['HTTP_HOST'];
				$liveDomain				=	$matches[1];
				if ( $liveDomain === $pageDomain ) {
					$domain				=	$liveDomain;
				} else {
					$live_len			=	strlen( $liveDomain );
					$page_len			=	strlen( $pageDomain );
					if ( ( $live_len < $page_len )
						&& ( $liveDomain === substr( $pageDomain, $page_len - $live_len ) )
						&& ( substr( $pageDomain, $page_len - $live_len - 1, 1 ) === '.' ) )
					{
						// ends of domains match, but live_site domain is shorter (e.g. no 'www.'):
						$domain			=	'.' . $liveDomain;		// '.' in front needed for 2-3 dots security-rule of browsers ( '.site.com' )
					} elseif ( ( $live_len > $page_len )
						&& ( $pageDomain === substr( $liveDomain, $live_len - $page_len ) )
						&& ( substr( $liveDomain, $live_len - $page_len - 1, 1 ) === '.' ) )
					{
						$domain			=	'.' . $pageDomain;
					}
				}
			}
			if ( $domain === null ) {
				$domain					=	$sp['domain'];
			}
		}
		if ( substr_count( $domain, '.' ) < 2 ) {
			$domain						=	null;
		}
		if ( $path === null ) {
			$directory_len				=	strlen( $matches[2] );
			if ( $live_ok && ( $directory_len > 1 ) ) {
				// get the query string:
				if ( ! empty( $_SERVER['PHP_SELF'] ) && ! empty( $_SERVER['REQUEST_URI'] ) ) {
					$queryString		=	urldecode( $_SERVER['REQUEST_URI'] );	// Apache
				} else {
					$queryString		=	urldecode( $_SERVER['SCRIPT_NAME'] );	// IIS
					// that part is not needed in this case:
					//	if (isset($_SERVER['QUERY_STRING']) && ! empty($_SERVER['QUERY_STRING'])) {
					//		$return	.=	'?' . $_SERVER['QUERY_STRING'];
					//	}
				}
				if ( substr( $queryString, 0, $directory_len ) === $matches[2] ) {
					$path				=	$matches[2];
				}
			}
			if ( $path === null ) {
				$path					=	'/';		// $sp['path']
			}
		}
		if ( isset( $sp['secure'] ) ) {
			if ( $secure === null ) {
				$secure					=	$sp['secure'];
			}
			if ( isset( $sp['httponly'] ) ) {
				// php >= 5.2.0:
				return setcookie( $name, $value, $expire, $path, $domain, $secure, $httponly );
			} else {
				// php < 5.2.0, but > 4.0.4:
				return setcookie( $name, $value, $expire, $path, $domain, $secure );
			}
		} else {
			return setcookie( $name, $value, $expire, $path, $domain );
		}
	}
	/**
	 * gets cookie set by cbSetcookie ! WARNING: always unescaped
	 * //TBD: add domain info in cookie-name
	 *
	 * @param  string            $name
	 * @param  string|array      $defaultValue
	 * @return string|array|null
	 */
	/*function getcookie( $name, $defaultValue = null ) {
		global $_COOKIE;
	
		return cbStripslashes( cbGetParam( $_COOKIE, $name, $defaultValue ) );
	}*/
}	// class CBCookie
 
?>