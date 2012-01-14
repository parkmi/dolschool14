<?php
/**
* includes/classes/lms.params.php
* Class to parse parameters (without mosParameters class)
* * * ElearningForce Inc
**/

// no direct access
defined( '_JLMS_EXEC' ) or die( 'Restricted access' );

jimport( 'joomla.html.editor' );

class JLMS07062010_JFactory extends JFactory {
	function __construct(){
		parent::__construct();
	}
	static function &getEditor($editor = null)
	{
		//jimport( 'joomla.html.editor' );
		
		//get the editor configuration setting
		if(is_null($editor))
		{
			$conf =& JFactory::getConfig();
			$editor = $conf->getValue('config.editor');
		}

		if( $editor == 'jckeditor') {		
			$instance = & JFactory::getEditor($editor);
		} else {
			$instance =& JLMS07062010_JEditor::getInstance($editor);	
		}	
		
		return $instance;
	}
}
class JLMS07062010_JEditor extends JEditor{
	function __construct($editor = 'none'){
		parent::__construct($editor);
	}
	static function &getInstance($editor = 'none')
	{
		static $instances;

		if (!isset ($instances)) {
			$instances = array ();
		}

		$signature = serialize($editor);
		
		if (empty ($instances[$signature])) {
			$instances[$signature] = new JLMS07062010_JEditor($editor);
		}
		
		return $instances[$signature];
	}
	function getButtons($editor, $buttons = true){
		
		$result = array();

		if(is_bool($buttons) && !$buttons) {
			return $result;
		}

		// Get plugins
		$plugins = JPluginHelper::getPlugin('editors-xtd');
		
		//The buttons are not displayed
		if(isset($plugins) && count($plugins)){
			$JLMS_ACL = & JLMSFactory::getACL();
			$notavailable_plugins = array();
			$notavailable_plugins[] = 'pagebreak';
			$notavailable_plugins[] = 'readmore';
			if(!in_array($JLMS_ACL->_role_type, array(1))){
				$tmp_plugins = array();
				$i=0;
				foreach($plugins as $plugin){
					if(!in_array($plugin->name, $notavailable_plugins)){
						$tmp_plugins[$i] = $plugin;
						$i++;
					}
				}
				if(isset($tmp_plugins) && count($tmp_plugins)){
					$plugins = array();
					$plugins = $tmp_plugins;
				}
			} else {
				if(isset($plugins) && count($plugins)){
					$plugins = array();
				}
			}
		}
		//The buttons are not displayed
		
		foreach($plugins as $plugin)
		{
			if(is_array($buttons) &&  in_array($plugin->name, $buttons)) {
				continue;
			}

			$isLoaded = JPluginHelper::importPlugin('editors-xtd', $plugin->name, false);

			$className = 'plgButton'.$plugin->name;
			if(class_exists($className)) {
				$plugin = new $className($this, (array)$plugin);
			}
			
			$asset = JLMS_getAsset();
			$author = & JFactory::getUser()->id;

			// Try to authenticate -- only add to array if authentication is successful
			$resultTest = $plugin->onDisplay($editor, $asset, $author);
			if ($resultTest) $result[] =  $resultTest;
		}
		
		/**
		 * JLMS hack. 
		 * add button read more for special function JLMS
		 */
			$doc =& JFactory::getDocument();
	
			// button is not active in specific content components
			$getContent = $this->getContent($editor);
			$present = JText::_('ALREADY EXISTS', true) ;
			
			$css = "";
			$css .= "
				p.system-jlmsreadmore{
					border: 1px dashed #0000ff;
					padding: 5px;
				}
			";
			$js = "";
			$js .= "
				function insertReadmoreBlock(editor) {
					var content = $getContent
					jInsertEditorText('{readmore}   {/readmore}', editor);
				}
			";
			$js .= "
				function insertJLMSReadmore_begin(editor) {
					var content = $getContent
					jInsertEditorText('{readmore}', editor);
				}
			";
			$js .= "
				function insertJLMSReadmore_end(editor) {
					var content = $getContent
					jInsertEditorText('{/readmore}', editor);
				}
			";
			
//			$doc->addStyleDeclaration($css);
			$doc->addScriptDeclaration($js);
			
			$button_x = new JObject();
			$button_x->set('modal', false);
			$button_x->set('onclick', 'insertReadmoreBlock(\''.$editor.'\');return false;');
			$button_x->set('text', JText::_('Readmore (block)'));
			$button_x->set('name', 'blank');
			$button_x->set('link', '#');
			
			$button_begin = new JObject();
			$button_begin->set('modal', false);
			$button_begin->set('onclick', 'insertJLMSReadmore_begin(\''.$editor.'\');return false;');
			$button_begin->set('text', JText::_('Readmore (start)'));
			$button_begin->set('name', 'blank');
			$button_begin->set('link', '#');
			
			$button_end = new JObject();
			$button_end->set('modal', false);
			$button_end->set('onclick', 'insertJLMSReadmore_end(\''.$editor.'\');return false;');
			$button_end->set('text', JText::_('Readmore (end)'));
			$button_end->set('name', 'blank');
			$button_end->set('link', '#');
			
//			$result[] = $button_x;
			$result[] = $button_begin;
			$result[] = $button_end;
		/**
		 * JLMS hack.
		 */

		return $result;
	}
}
?>