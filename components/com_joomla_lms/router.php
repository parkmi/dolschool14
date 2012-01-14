<?php
/**
* router.php
* (c) JoomaLMS eLearning Software http://www.joomlalms.com/
**/

if (!defined('_JOOMLMS_FRONT_HOME')) { define('_JOOMLMS_FRONT_HOME', dirname(__FILE__)); }

require_once(_JOOMLMS_FRONT_HOME . DS . "includes" . DS . "libraries" . DS . "lms.lib.sef.php");
require_once(_JOOMLMS_FRONT_HOME . DS . "includes" . DS . "classes" . DS . "lms.titles.php");
require_once(_JOOMLMS_FRONT_HOME . DS . "includes" . DS . "classes" . DS . "lms.factory.php");
function Joomla_lmsBuildRoute( &$query )
{		
	$segments = array();	
			
	$vars = $query;
			
	$JLMSSefObject = getJLMSSefObject();
	$JLMSSefObject->setData( $query );
	$JLMSSefObject->calledFromRouterPHP();
	
	$segments = $JLMSSefObject->buildSefSegments();			
	$nonSefVars = $JLMSSefObject->getNonSefVars();
		
	$nonSefVars[] = 'option';
	$nonSefVars[] = 'Itemid';	
	$nonSefVars[] = 'start';
	$nonSefVars[] = 'limit';
	$nonSefVars[] = 'limitstart';
	$nonSefVars[] = 'pn_limit';
	$nonSefVars[] = 'pn_limitstart';
	$nonSefVars[] = 'dir';
	$nonSefVars[] = 'order';
				
	if( isset($vars) ) {	
		foreach( $vars AS $key => $value ) 
		{
			if( in_array( $key, $nonSefVars ) )
				continue;		
			
			unset( $query[$key] );	
		}
	}
					
	return $segments;
	
}

function Joomla_lmsParseRoute( $segments )
{	
	//Get the active menu item
	$component	= JComponentHelper::getComponent('com_joomla_lms');
	$app	= JFactory::getApplication(); 
	$menu		= $app->getMenu();	
	
	$item = $menu->getActive();				
	$itemid = isset($item->id) ? $item->id : 0;
	if (!$itemid) {
		$JLMS_CONFIG = & JLMSFactory::getConfig();
		$itemid = $JLMS_CONFIG->get('Itemid');
	}
			 		
	$JLMSSefObject = getJLMSSefObject();
	$segments = str_replace( ':', '-', $segments );	 
	$JLMSSefObject->setData( $segments );
	$JLMSSefObject->calledFromRouterPHP();
		
	$vars = $JLMSSefObject->parseSefSegments();
	
	$vars['Itemid'] = $itemid;
								
	return $vars;	
}
