<?php

defined('JPATH_BASE') or die;

if (class_exists('JEventDispatcher')) { // 1.5 RC3 and below
	class JLMSDispatcher extends JEventDispatcher
	{	
		public static function & getInstance()
		{
			static $instance;
	
			if (!is_object($instance)) {
				$instance = new JLMSDispatcher();
			}
	
			return $instance;
		}
	
		function getObservers() 
		{
			return $this->_observers;
		}
		
		function setObservers( $values ) 
		{
			$this->_observers = $values;
		}
	}
} elseif(class_exists('JDispatcher')) 
{ // 1.5 RC4 and above

	class JLMSDispatcher extends JDispatcher
	{	
		function & getChildInstance()
		{
			static $instance;
	
			if (!is_object($instance)) {
				$instance = new JLMSDispatcher();
			}
	
			return $instance;
		}
	
		function getObservers() 
		{
			return $this->_observers;
		}
		
		function setObservers( $values ) 
		{
			$this->_observers = $values;
		}
	}
	
}
