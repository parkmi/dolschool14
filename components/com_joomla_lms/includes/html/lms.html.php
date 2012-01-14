<?php
/**
* lms.lib.html.php
* JoomlaLMS Component
**/

// no direct access
defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

class JLMS_HTML {
	
	function _( $type )
	{
			//Initialise variables
		$prefix = 'JLMS_HTML';
		$file   = '';
		$func   = $type;

		// Check to see if we need to load a helper file
		$parts = explode('.', $type);

		switch(count($parts))
		{
			case 3 :
			{
				$prefix		= preg_replace( '#[^A-Z0-9_]#i', '', $parts[0] );
				$file		= preg_replace( '#[^A-Z0-9_]#i', '', $parts[1] );
				$func		= preg_replace( '#[^A-Z0-9_]#i', '', $parts[2] );
			} break;

			case 2 :
			{
				$file		= preg_replace( '#[^A-Z0-9_]#i', '', $parts[0] );
				$func		= preg_replace( '#[^A-Z0-9_]#i', '', $parts[1] );
			} break;
		}

		$className	= $prefix.ucfirst($file);

		if (!class_exists( $className ))
		{
			jimport('joomla.filesystem.path');
			if ($path = JPath::find(_JOOMLMS_FRONT_HOME.DS.'includes'.DS.'html'.DS.'html', strtolower($file).'.php'))
			{
				require_once $path;

				if (!class_exists( $className ))
				{
					JError::raiseWarning( 0, $className.'::' .$func. ' not found in file.' );
					return false;
				}
			}
			else
			{
				JError::raiseWarning( 0, $prefix.$file . ' not supported. File not found.' );
				return false;
			}
		}

		if (is_callable( array( $className, $func ) ))
		{
			$args = func_get_args();
			array_shift( $args );
			return call_user_func_array( array( $className, $func ), $args );
		}
		else
		{
			JError::raiseWarning( 0, $className.'::'.$func.' not supported.' );
			return false;
		}									
	}
	/**
	 * Static progress bar
	 *
	 * @param int_type $percent
	 * @param string_type $suffix_class
	 * @return html
	 */
	function showProgressBar($percent=0, $uid=0, $suffix_class='', $hide_percent=0){
		$style_width = '';
		if(isset($percent) && $percent){
			$style_width .= ' ';
			$style_width .= 'style="width: '.$percent.'%;"';
		}
		if(!$uid){
			$uid = rand(1, 500);
		}
		$property_id = $uid ? 'id="jlmsPrgBar_'.$uid.'"' : '';
		$bar_text_add_class = '';
		if(isset($hide_percent) && $hide_percent){
			$bar_text_add_class .= ' '.'no_show';
		}
		$percent_text = $percent.'%';
		ob_start();
		if($percent >= 0){
			?>
			<div <?php echo $property_id;?> class="progress<?php echo $suffix_class;?>">
				<div class="bar"<?php echo $style_width;?>>
					<!--x-->
				</div>
				<div class="bar_text<?php echo $bar_text_add_class;?>">
					<?php echo $percent_text;?>
				</div>
				<div class="clr"><!--x--></div>
			</div>
			<?php
		}
		$return = ob_get_clean();
		return $return;
	}
}

?>