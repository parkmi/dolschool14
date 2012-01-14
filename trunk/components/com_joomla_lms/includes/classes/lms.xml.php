<?php 
/**
* includes/classes/lms.titles.php
* Joomla LMS Component
* * * ElearningForce DK
**/

defined( 'JPATH_BASE' ) or die( 'Restricted access' );

jimport('joomla.utilities.simplexml');

class JLMSXML extends JSimpleXML
{
	function __construct($options = null)
	{
		parent::__construct( $options );
	}
}

class JLMSXMLElement extends JSimpleXMLElement
{
	function __construct($name, $attrs = array(), $level = 0)
	{
		parent::__construct($name, $attrs, $level );
	}

	function &getElementByPath($path)
	{	
		$ref = & parent::getElementByPath($path);
			
		if ( !$ref ) {					
			$classname = get_class( $this );
			$ref = new $classname('empty');
		}
				
		return $ref;
	}
}
?>