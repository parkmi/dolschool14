<?php
jimport('joomla.mail.mail');

class MailManager extends JMail
{	
	function prepareEmail(&$params)
	{		
		$this->ClearAllRecipients();
		$this->ClearAttachments();		
		
		$sender		= isset( $params['sender'] ) ?  $params['sender'] : '';
		$subject	= isset( $params['subject'] ) ?  $params['subject'] : '';
		$recipient	= isset( $params['recipient'] ) ? $params['recipient'] : '';
		$emailHtmlText = isset( $params['body'] ) ?  $params['body'] : '';
		$cc			= isset( $params['cc'] ) ?  $params['cc'] : '';
		$bcc		= isset( $params['bcc'] ) ?  $params['bcc'] : '';
		$attachment = isset( $params['attachment'] ) ?  $params['attachment'] : '';
		$replyto	= isset( $params['replyto'] ) ?  $params['replyto'] : '';
		$alttext	= isset( $params['alttext'] ) ? $params['alttext'] : '';				
		
		if( !$recipient ) {			 					
			return false;			
		}	

		if( is_array( $recipient ) ) {
			foreach ( $recipient as $to ) {				
				$this->AddAddress( $to );
			}
		} else {
			$this->AddAddress($recipient);
		}

		if ( isset( $cc ) && $cc != '' ) {
		    if ( is_array( $cc ) ) {
		        foreach ( $cc as $to ) {							        	
		        	$this->AddCC( $to );
		        	
		        }
			} else {								
		       	$this->AddCC( $cc );
		    }
		}

		if ( isset( $bcc ) && $bcc != '' ) {									
		    if ( is_array( $bcc ) ) {
		        foreach ($bcc as $to) $this->AddBCC($to);
		    } else {		    	
		        $this->AddBCC($bcc);
		    }
		}

	    if (is_array($attachment)) {
			foreach ($attachment as $file) {
				if(isset($file->file_source) && isset($file->file_name)){
					PHPMailer::AddAttachment($file->file_source, $file->file_name);
				} else {
					PHPMailer::AddAttachment($file);
				}
			}
		} else if(isset($attachment->file_source) && isset($attachment->file_name)){
			PHPMailer::AddAttachment($attachment->file_source, $attachment->file_name);
		} 
		/*else {
			PHPMailer::AddAttachment($attachment);
		}
		*/

	    if ( $replyto ) {
	        if ( is_array( $replyto ) ) {
	        	reset( $replytoname );
	            foreach ( $replyto as $to ) {
	            	$toname		=	( ( false !== (list( $key, $value ) = each( $replytoname ) ) ) ? $value : '' );
	            	$this->AddReplyTo( $to, $toname );
	            }
	        } else
	            $this->AddReplyTo( $replyto, $replytoname );
	    }

		$this->setSender( $sender );					
		$this->setSubject( $subject );		
		$this->setBody( $emailHtmlText );			
		$this->AltBody = $alttext;			

		return true; 
	}
	
	function & getChildInstance($id = 'Joomla')
	{
		static $instances;

		if (!isset ($instances)) {
			$instances = array ();
		}

		if (empty ($instances[$id])) {
			$conf	=& JFactory::getConfig();
	
			$sendmail 	= $conf->getValue('config.sendmail');
			if( JLMS_J16version() ) 
			{
				$smtpauth	=  ($conf->get('smtpauth') == 0) ? null : 1;
			} else {
				$smtpauth 	= $conf->getValue('config.smtpauth');	
			}						
			$smtpuser 	= $conf->getValue('config.smtpuser');
			$smtppass  	= $conf->getValue('config.smtppass');
			$smtphost 	= $conf->getValue('config.smtphost');
			$smtpsecure	= $conf->getValue('config.smtpsecure');
			$smtpport	= $conf->getValue('config.smtpport');
			$mailfrom 	= $conf->getValue('config.mailfrom');
			$fromname 	= $conf->getValue('config.fromname');
			$mailer 	= $conf->getValue('config.mailer');
			
			$instance = new MailManager();	
				
			// Set default sender
			$instance->setSender(array ($mailfrom, $fromname));
	
			// Default mailer is to use PHP's mail function
			switch ($mailer)
			{
				case 'smtp' :
					$instance->useSMTP($smtpauth, $smtphost, $smtpuser, $smtppass, $smtpsecure, $smtpport);
					break;
				case 'sendmail' :
					$instance->useSendmail($sendmail);
					break;
				default :
					$instance->IsMail();
					break;
			}	
			
			$instances[$id] = $instance;	
		}

		return $instances[$id];
	}	

	function sendEmail() 
	{
		return $this->send();
	}	
}
?>