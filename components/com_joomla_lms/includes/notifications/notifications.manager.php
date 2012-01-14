<?php
class NotificationsManager {	
	function getEmailTemplates() 
	{
		static $email_templates;
		
		if (!is_array($email_templates)) {
			require_once( JPATH_SITE.DS.'components'.DS.'com_joomla_lms'.DS.'includes'.DS.'notifications'.DS.'email.templates.php' );	
		}
		
		return $email_templates;	
	} 
	
	function getNativeEmailTemplate( $id ) 
	{
		static $templates_html;
		$email_templates = NotificationsManager::getEmailTemplates();
																					
		if( isset($email_templates[0]) ) {			
			foreach( $email_templates AS $email_template ) 
			{				
				if( $email_template->id == $id ) 
				{	
					if (!isset($templates_html[$id])) {		
						if( file_exists( JPATH_SITE.DS.'components'.DS.'com_joomla_lms'.DS.'includes'.DS.'notifications'.DS.'templates'.DS.$email_template->template_html_file ) ) {
							ob_start();		
							require_once( JPATH_SITE.DS.'components'.DS.'com_joomla_lms'.DS.'includes'.DS.'notifications'.DS.'templates'.DS.$email_template->template_html_file );
							$templates_html[$id] = ob_get_contents();		
							$email_template->template_html = $templates_html[$id];					 		
							ob_end_clean();					
						} else {
								$email_template->template_html = '';
						}
					} else {
						$email_template->template_html = $templates_html[$id]; 
					}
										
					return $email_template; 
				}
			}		
		} else 
		{
			return false;
		}				
	}
	
	function getNotificationEvents() 
	{
		static $notification_events;
		
		if (!is_array($notification_events)) {
			require_once( JPATH_SITE.DS.'components'.DS.'com_joomla_lms'.DS.'includes'.DS.'notifications'.DS.'notification.events.php' );
		}
		
		return $notification_events;			
	}
	
	function getNotificationEvent( $id ) 
	{
		$notification_events = NotificationsManager::getNotificationEvents();
																					
		if( isset($notification_events[0]) ) {			
			foreach( $notification_events AS $notification_event ) 
			{				
				if( $notification_event->id == $id ) 
				{										
					return $notification_event; 
				}
			}		
		} else 
		{
			return false;
		}				
	}
	
	function getNotificationTypes() 
	{
		static $notification_types;
		
		if (!is_array($notification_types)) {
			require_once( JPATH_SITE.DS.'components'.DS.'com_joomla_lms'.DS.'includes'.DS.'notifications'.DS.'notification.types.php' );
		}		
		
		return $notification_types;		
	}	
	
	function getNotificationType( $id ) 
	{
		static $notific_types;
		
		$notification_types = NotificationsManager::getNotificationTypes();
		
		if( isset( $notific_types[$id] ) ) 
		{
			return $notific_types[$id];
		}
																					
		if( isset($notification_types[0]) ) {						
			foreach( $notification_types AS $notification_type ) 
			{				
				if( $notification_type->id == $id ) 
				{	
					$notific_types[$id] = $notification_type;					 									
					return $notification_type; 
				}
			}		
		} else 
		{
			return false;
		}				
	}
	
	function getEmailTemplatesByNotificType( $type ) 
	{
		$email_templates = NotificationsManager::getEmailTemplates();
																							
		if( isset($email_templates[0]) ) {
			$e_templates = array();						
			foreach( $email_templates AS $email_template ) 
			{				
				if( $email_template->notification_type == $type ) 
				{									
					 $e_templates[] = $email_template; 
				}
			}
						
			return $e_templates;		
		} else 
		{
			return false;
		}		
	}
	
	function getNotificationEventByActionName( $action_name ) 
	{
		$notification_events = NotificationsManager::getNotificationEvents();
		 
		if( $notification_events[0] ) {
			foreach( $notification_events AS $notification_event ) 
			{
				if( $notification_event->event_action == $action_name ) 
				{
					return $notification_event; 			
				}
			}
		} else {
			return false;
		}	  
	}
	
	function replaceWrappers( $wrappers, $text ) 
	{		
		foreach( $wrappers AS $tag => $isShow ) 
		{	
			$tag = trim($tag,'{}');		 	
			$preg_expr = "/\{".$tag."\}(.*?)\{\/".$tag."\}/is";
			 												 											
			if ( preg_match_all($preg_expr, $text, $matches) )		
			{																					
				foreach( $matches[1] AS $match ) 
				{					
					$str_repl = "{".$tag."}".$match."{/".$tag."}";																																	
					if( $isShow == 'show' )							
						$text = str_replace($str_repl,$match,$text); 					
					else
						$text = str_replace($str_repl,"",$text);													 			
				}		
			}
		}
		
		return $text;
	}
	
	function getEmailTemplate( $id ) 
	{
		static $templates;
		
		if (!isset($templates[$id])) 
		{
			$db = JFactory::getDBO();
			
			$nativeEmailTemplate = NotificationsManager::getNativeEmailTemplate( $id );
									
			if( $nativeEmailTemplate ) {			 
				$row = new stdClass();
				$row->id = $nativeEmailTemplate->id;
				$row->name = isset($nativeEmailTemplate->name)?$nativeEmailTemplate->name:'';
				$row->subject = isset($nativeEmailTemplate->subject)?$nativeEmailTemplate->subject:'';
				$row->template_html = $nativeEmailTemplate->template_html;
				$row->template_alt_text = isset($nativeEmailTemplate->template_alt_text)?$nativeEmailTemplate->template_alt_text:'';
				$row->disabled = false;
				$row->native = true;	
				$row->notification_type = $nativeEmailTemplate->notification_type;
			} else {
				$sql = "SELECT id, name, body_html AS template_html, body_text AS template_alt_text, notification_type, subject, disabled, '0' AS native FROM #__lms_email_templates WHERE id = '".$id."'";
				$db->setQuery( $sql );
				$row = $db->loadObject();				
			}
			
			$templates[$id] = $row;		
		}
				
		return php4_clone( $templates[$id] );			
	}
	
	function addDateTimeMarkers( $markers ) 
	{
		$app = & JFactory::getApplication(); 
		$markers['{date}'] = date('D, d M Y');
		$markers['{time}'] = date('H:i:s');
		$markers['{datetime}'] = date('D, d M Y H:i:s');
		$markers['{sitename}'] = $app->getCfg('sitename' );

		return $markers;  
	}

}