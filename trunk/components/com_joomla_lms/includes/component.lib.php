<?php
// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();
if (class_exists('jeditor')) { null; } elseif (class_exists('JLoader')) { JLoader::register('JEditor' , JPATH_LIBRARIES.DS.'joomla'.DS.'html'.DS.'editor.php');JLoader::load('JEditor'); }
if (class_exists('jobject')) { null; }
if (class_exists('jrequest')) { null; }
if (class_exists('jresponse')) { null; }
if (class_exists('jfactory')) { null; }
if (class_exists('jversion')) { null; }
if (class_exists('jerror')) { null; }
if (class_exists('jexception')) { null; }
if (class_exists('jarrayhelper')) { null; }
if (class_exists('jfilterinput')) { null; }
if (class_exists('jfilteroutput')) { null; }
if (class_exists('jtext')) { null; }
if (class_exists('jroute')) { null; }
if (class_exists('jmenu')) { null; }
if (class_exists('juser')) { null; }
if (class_exists('juri')) { null; }
if (class_exists('jhtml')) { null; }
if (class_exists('jparameter')) { null; }
if (class_exists('jutility')) { null; }
if (class_exists('jevent')) { null; }
if (class_exists('jdispatcher')) { null; }
if (class_exists('jlanguage')) { null; }
if (class_exists('jstring')) { null; }
if (class_exists('jtoolbar')) { null; }
if (class_exists('japplication')) { null; }
if (class_exists('japplicationhelper')) { null; }
if (class_exists('jcomponenthelper')) { null; }
if (class_exists('jregistry')) { null; }
if (class_exists('jregistryformat')) { null; }
if (class_exists('jsession')) { null; }
if (class_exists('jsessionstorage')) { null; }
if (class_exists('jdatabase')) { null; }
if (class_exists('jtable')) { null; }
if (class_exists('jpath')) { null; }
if (class_exists('jelement')) { null; }
if (class_exists('jfolder')) { null; }
if (class_exists('jpluginhelper')) { null; }
if (class_exists('jplugin')) { null; }
if (class_exists('jobserver')) { null; }
if (class_exists('jobservable')) { null; }
if (class_exists('jsimplexml')) { null; }
if (class_exists('jpanetabs')) { null; }
if (class_exists('mosadminmenus')) { null; }
if (class_exists('moscache')) { null; }
if (class_exists('moscategory')) { null; }
if (class_exists('moscommonhtml')) { null; }
if (class_exists('moscontent')) { null; }
if (class_exists('mosdbtable')) { null; }
if (class_exists('moshtml')) { null; }
if (class_exists('mosinstaller')) { null; }
if (class_exists('mosmainframe')) { null; }
if (class_exists('mosmambothandler')) { null; }
if (class_exists('mosmenu')) { null; }
if (class_exists('mosmodule')) { null; }
if (class_exists('mosparameters')) { null; }
if (class_exists('patfactory')) { null; }
if (class_exists('mosprofiler')) { null; }
if (class_exists('mossection')) { null; }
if (class_exists('mossession')) { null; }
if (class_exists('mostoolbar')) { null; }
if (class_exists('mosuser')) { null; }
if (class_exists('database')) { null; }
if (class_exists('jdatabasemysql')) { null; }
if (class_exists('jauthorization')) { null; }
if (class_exists('jdocument')) { null; }
if (class_exists('jdocumentrenderer')) { null; }
if (class_exists('jmodulehelper')) { null; }
if (class_exists('jtablecategory')) { null; }
if (class_exists('jtablecontent')) { null; }
if (class_exists('jinstaller')) { null; }
if (class_exists('jfile')) { null; }
if (class_exists('jarchive')) { null; }
if (class_exists('jtableplugin')) { null; }
if (class_exists('jtablemenu')) { null; }
if (class_exists('jtoolbarhelper')) { null; }
if (class_exists('jtablemodule')) { null; }
if (class_exists('jprofiler')) { null; }
if (class_exists('jtablesection')) { null; }
if (class_exists('jtablesession')) { null; }
if (class_exists('jtableuser')) { null; }
if (class_exists('jdatabasemysqli')) { null; }

if (!class_exists('mosAdminMenus')) {
class mosAdminMenus
{
	/**
 	 * Legacy function, use {@link JHTML::_('menu.ordering')} instead
 	 *
 	 * @deprecated	As of version 1.5
 	*/
	function Ordering( &$row, $id )
	{
		return JHTML::_('menu.ordering', $row, $id);
	}

	/**
 	 * Legacy function, use {@link JHTML::_('list.accesslevel', )} instead
 	 *
 	 * @deprecated	As of version 1.5
 	*/
	function Access( &$row )
	{
		return JHTML::_('list.accesslevel', $row);
	}

	/**
 	 * Legacy function, deprecated
 	 *
 	 * @deprecated	As of version 1.5
 	*/
	function Published( &$row )
	{
		$published = JHTML::_('select.booleanlist',  'published', 'class="inputbox"', $row->published );
		return $published;
	}

	/**
 	 * Legacy function, use {@link JAdminMenus::MenuLinks()} instead
 	 *
 	 * @deprecated	As of version 1.5
 	*/
	function MenuLinks( &$lookup, $all=NULL, $none=NULL, $unassigned=1 )
	{
		$options = JHTML::_('menu.linkoptions', $lookup, $all, $none|$unassigned);
		if (empty( $lookup )) {
			$lookup = array( JHTML::_('select.option',  -1 ) );
		}
		$pages = JHTML::_('select.genericlist',   $options, 'selections[]', 'class="inputbox" size="15" multiple="multiple"', 'value', 'text', $lookup, 'selections' );
		return $pages;
	}

	/**
 	 * Legacy function, deprecated
 	 *
 	 * @deprecated	As of version 1.5
 	*/
	function Category( &$menu, $id, $javascript='' )
	{
		$db =& JFactory::getDBO();

		$query = 'SELECT c.id AS `value`, c.section AS `id`, CONCAT_WS( " / ", s.title, c.title) AS `text`'
		. ' FROM #__sections AS s'
		. ' INNER JOIN #__categories AS c ON c.section = s.id'
		. ' WHERE s.scope = "content"'
		. ' ORDER BY s.name, c.name'
		;
		$db->setQuery( $query );
		$rows = $db->loadObjectList();
		$category = '';

		$category .= JHTML::_('select.genericlist',   $rows, 'componentid', 'class="inputbox" size="10"'. $javascript, 'value', 'text', $menu->componentid );
		$category .= '<input type="hidden" name="link" value="" />';

		return $category;
	}

	/**
 	 * Legacy function, deprecated
 	 *
 	 * @deprecated	As of version 1.5
 	*/
	function Section( &$menu, $id, $all=0 )
	{
		$db =& JFactory::getDBO();

		$query = 'SELECT s.id AS `value`, s.id AS `id`, s.title AS `text`'
		. ' FROM #__sections AS s'
		. ' WHERE s.scope = "content"'
		. ' ORDER BY s.name'
		;
		$db->setQuery( $query );
		if ( $all ) {
			$rows[] = JHTML::_('select.option',  0, '- '. JText::_( 'All Sections' ) .' -' );
			$rows = array_merge( $rows, $db->loadObjectList() );
		} else {
			$rows = $db->loadObjectList();
		}

		$section = JHTML::_('select.genericlist',   $rows, 'componentid', 'class="inputbox" size="10"', 'value', 'text', $menu->componentid );
		$section .= '<input type="hidden" name="link" value="" />';

		return $section;
	}

	/**
 	 * Legacy function, deprecated
 	 *
 	 * @deprecated	As of version 1.5
 	*/
	function Component( &$menu, $id )
	{
		$db =& JFactory::getDBO();

		$query = 'SELECT c.id AS value, c.name AS text, c.link'
		. ' FROM #__components AS c'
		. ' WHERE c.link <> ""'
		. ' ORDER BY c.name'
		;
		$db->setQuery( $query );
		$rows = $db->loadObjectList( );

		$component = JHTML::_('select.genericlist',   $rows, 'componentid', 'class="inputbox" size="10"', 'value', 'text', $menu->componentid, '', 1 );

		return $component;
	}


	/**
 	 * Legacy function, deprecated
 	 *
 	 * @deprecated	As of version 1.5
 	*/
	function ComponentName( &$menu, $id )
	{
		$db =& JFactory::getDBO();

		$query = 'SELECT c.id AS value, c.name AS text, c.link'
		. ' FROM #__components AS c'
		. ' WHERE c.link <> ""'
		. ' ORDER BY c.name'
		;
		$db->setQuery( $query );
		$rows = $db->loadObjectList( );

		$component = 'Component';
		foreach ( $rows as $row ) {
			if ( $row->value == $menu->componentid ) {
				$component = JText::_( $row->text );
			}
		}

		return $component;
	}


	/**
 	 * Legacy function, use {@link JHTML::_('list.images', )} instead
 	 *
 	 * @deprecated	As of version 1.5
 	*/
	function Images( $name, &$active, $javascript=NULL, $directory=NULL )
	{
		return JHTML::_('list.images', $name, $active, $javascript, $directory);
	}

	/**
 	 * Legacy function, use {@link JHTML::_('list.specificordering', )} instead
 	 *
 	 * @deprecated	As of version 1.5
 	*/
	function SpecificOrdering( &$row, $id, $query, $neworder=0 )
	{
		return JHTML::_('list.specificordering', $row, $id, $query, $neworder);
	}

	/**
 	 * Legacy function, use {@link JHTML::_('list.users', )} instead
 	 *
 	 * @deprecated	As of version 1.5
 	*/
	function UserSelect( $name, $active, $nouser=0, $javascript=NULL, $order='name', $reg=1 )
	{
		return JHTML::_('list.users', $name, $active, $nouser, $javascript, $order, $reg);
	}

	/**
 	 * Legacy function, use {@link JHTML::_('list.positions', )} instead
 	 *
 	 * @deprecated	As of version 1.5
 	*/
	function Positions( $name, $active=NULL, $javascript=NULL, $none=1, $center=1, $left=1, $right=1, $id=false )
	{
		return JHTML::_('list.positions', $name, $active, $javascript, $none, $center, $left, $right, $id);
	}

	/**
 	 * Legacy function, use {@link JHTML::_('list.category', )} instead
 	 *
 	 * @deprecated	As of version 1.5
 	*/
	function ComponentCategory( $name, $section, $active=NULL, $javascript=NULL, $order='ordering', $size=1, $sel_cat=1 )
	{
		return JHTML::_('list.category', $name, $section, $active, $javascript, $order, $size, $sel_cat);
	}

	/**
 	 * Legacy function, use {@link JHTML::_('list.section', )} instead
 	 *
 	 * @deprecated	As of version 1.5
 	*/
	function SelectSection( $name, $active=NULL, $javascript=NULL, $order='ordering' )
	{
		return JHTML::_('list.section', $name, $active, $javascript, $order);
	}

	/**
 	 * Legacy function, deprecated
 	 *
 	 * @deprecated	As of version 1.5
 	*/
	function Links2Menu( $type, $and )
	{
		$db =& JFactory::getDBO();

		$query = 'SELECT * '
		. ' FROM #__menu '
		. ' WHERE type = '.$db->Quote($type)
		. ' AND published = 1'
		. $and
		;
		$db->setQuery( $query );
		$menus = $db->loadObjectList();

		return $menus;
	}

	/**
 	 * Legacy function, deprecated
 	 *
 	 * @deprecated	As of version 1.5
 	*/
	function MenuSelect( $name='menuselect', $javascript=NULL )
	{
		$db =& JFactory::getDBO();

		$query = 'SELECT params'
		. ' FROM #__modules'
		. ' WHERE module = "mod_mainmenu"'
		;
		$db->setQuery( $query );
		$menus = $db->loadObjectList();
		$total = count( $menus );
		$menuselect = array();
		for( $i = 0; $i < $total; $i++ )
		{
			$registry = new JRegistry();
			$registry->loadINI($menus[$i]->params);
			$params = $registry->toObject( );

			$menuselect[$i]->value 	= $params->menutype;
			$menuselect[$i]->text 	= $params->menutype;
		}
		// sort array of objects
		JArrayHelper::sortObjects( $menuselect, 'text', 1 );

		$menus = JHTML::_('select.genericlist',   $menuselect, $name, 'class="inputbox" size="10" '. $javascript, 'value', 'text' );

		return $menus;
	}

	/**
 	 * Legacy function, deprecated
 	 *
 	 * @deprecated	As of version 1.5
 	*/
	function ReadImages( $imagePath, $folderPath, &$folders, &$images )
	{
		jimport( 'joomla.filesystem.folder' );
		$imgFiles = JFolder::files( $imagePath );

		foreach ($imgFiles as $file)
		{
			$ff_ 	= $folderPath.DS.$file;
			$ff 	= $folderPath.DS.$file;
			$i_f 	= $imagePath .'/'. $file;

			if ( is_dir( $i_f ) && $file <> 'CVS' && $file <> '.svn') {
				$folders[] = JHTML::_('select.option',  $ff_ );
				mosAdminMenus::ReadImages( $i_f, $ff_, $folders, $images );
			} else if ( preg_match( "/bmp|gif|jpg|png/i", $file ) && is_file( $i_f ) ) {
				// leading / we don't need
				$imageFile = substr( $ff, 1 );
				$images[$folderPath][] = JHTML::_('select.option',  $imageFile, $file );
			}
		}
	}

	/**
 	 * Legacy function, deprecated
 	 *
 	 * @deprecated	As of version 1.5
 	*/
	function GetImageFolders( &$folders, $path )
	{
		$javascript 	= "onchange=\"changeDynaList( 'imagefiles', folderimages, document.adminForm.folders.options[document.adminForm.folders.selectedIndex].value, 0, 0);  previewImage( 'imagefiles', 'view_imagefiles', '$path/' );\"";
		$getfolders 	= JHTML::_('select.genericlist',   $folders, 'folders', 'class="inputbox" size="1" '. $javascript, 'value', 'text', '/' );
		return $getfolders;
	}

	/**
	 * Legacy function, deprecated
	 *
	 * @deprecated	As of version 1.5
	 */
	function GetImages( &$images, $path )
	{
		if ( !isset($images['/'] ) ) {
			$images['/'][] = JHTML::_('select.option',  '' );
		}

		//$javascript	= "onchange=\"previewImage( 'imagefiles', 'view_imagefiles', '$path/' )\" onfocus=\"previewImage( 'imagefiles', 'view_imagefiles', '$path/' )\"";
		$javascript	= "onchange=\"previewImage( 'imagefiles', 'view_imagefiles', '$path/' )\"";
		$getimages	= JHTML::_('select.genericlist',   $images['/'], 'imagefiles', 'class="inputbox" size="10" multiple="multiple" '. $javascript , 'value', 'text', null );

		return $getimages;
	}

	/**
 	 * Legacy function, deprecated
 	 *
 	 * @deprecated	As of version 1.5
 	*/
	function GetSavedImages( &$row, $path )
	{
		$images2 = array();
		foreach( $row->images as $file ) {
			$temp = explode( '|', $file );
			if( strrchr($temp[0], '/') ) {
				$filename = substr( strrchr($temp[0], '/' ), 1 );
			} else {
				$filename = $temp[0];
			}
			$images2[] = JHTML::_('select.option',  $file, $filename );
		}
		//$javascript	= "onchange=\"previewImage( 'imagelist', 'view_imagelist', '$path/' ); showImageProps( '$path/' ); \" onfocus=\"previewImage( 'imagelist', 'view_imagelist', '$path/' )\"";
		$javascript	= "onchange=\"previewImage( 'imagelist', 'view_imagelist', '$path/' ); showImageProps( '$path/' ); \"";
		$imagelist 	= JHTML::_('select.genericlist',   $images2, 'imagelist', 'class="inputbox" size="10" '. $javascript, 'value', 'text' );

		return $imagelist;
	}

	/**
 	 * Legacy function, use {@link JHTML::_('image.site')} instead
 	 *
 	 * @deprecated	As of version 1.5
 	*/
	function ImageCheck( $file, $directory='/images/M_images/', $param=NULL, $param_directory='/images/M_images/', $alt=NULL, $name='image', $type=1, $align='top' )
	{
		$attribs = array('align' => $align);
		return JHTML::_('image.site', $file, $directory, $param, $param_directory, $alt, $attribs, $type);
	}

	/**
 	 * Legacy function, use {@link JHTML::_('image.administrator')} instead
 	 *
 	 * @deprecated	As of version 1.5
 	*/
	function ImageCheckAdmin( $file, $directory='/images/', $param=NULL, $param_directory='/images/', $alt=NULL, $name=NULL, $type=1, $align='middle' )
	{
		$attribs = array('align' => $align);
		return JHTML::_('image.administrator', $file, $directory, $param, $param_directory, $alt, $attribs, $type);
	}

	/**
 	 * Legacy function, use {@link MenusHelper::getMenuTypes()} instead
 	 *
 	 * @deprecated	As of version 1.5
 	*/
	function menutypes()
	{
		JError::raiseNotice( 0, 'mosAdminMenus::menutypes method deprecated' );
	}

	/**
 	 * Legacy function, use {@link MenusHelper::menuItem()} instead
 	 *
 	 * @deprecated	As of version 1.5
 	*/
	function menuItem( $item )
	{
		JError::raiseNotice( 0, 'mosAdminMenus::menuItem method deprecated' );
	}
}
}

if (!class_exists('mosCache')) {
class mosCache
{
	/**
	* @return object A function cache object
	*/
	function &getCache(  $group=''  )
	{
		return JFactory::getCache($group);
	}
	/**
	* Cleans the cache
	*/
	function cleanCache( $group=false )
	{
		$cache =& JFactory::getCache($group);
		$cache->clean($group);
	}
}
}



// Register legacy classes for autoloading
JLoader::register('JTableCategory' , JPATH_LIBRARIES.DS.'joomla'.DS.'database'.DS.'table'.DS.'category.php');

/**
 * Legacy class, use {@link JTableCategory} instead
 *
 * @deprecated	As of version 1.5
 * @package	Joomla.Legacy
 * @subpackage	1.5
 */
 
if (!class_exists('mosCategory')) {

if (!class_exists('JTableCategory')) {
	JLoader::load('JTableCategory');
}

class mosCategory extends JTableCategory
{
	/**
	 * Constructor
	 */
	function __construct( &$db)
	{
		parent::__construct( $db );
	}

	function mosCategory(&$db)
	{
		parent::__construct( $db );
	}

	/**
	 * Legacy Method, use {@link JTable::reorder()} instead
	 * @deprecated As of 1.5
	 */
	function updateOrder( $where='' )
	{
		return $this->reorder( $where );
	}

	/**
	 * Legacy Method, use {@link JTable::publish()} instead
	 * @deprecated As of 1.0.3
	 */
	function publish_array( $cid=null, $publish=1, $user_id=0 )
	{
		$this->publish( $cid, $publish, $user_id );
	}
}
}


JLoader::register('JPaneTabs',  JPATH_LIBRARIES.DS.'joomla'.DS.'html'.DS.'pane.php');

if (!class_exists('mosAbstractTasker')) {
class mosAbstractTasker
{
	function mosAbstractTasker()
	{
		jexit( 'mosAbstractTasker deprecated, use JController instead' );
	}
}
}

if (!class_exists('mosEmpty')) {
class mosEmpty
{
	function def( $key, $value='' )
	{
		return 1;
	}
	function get( $key, $default='' )
	{
		return 1;
	}
}
}

if (!class_exists('MENU_Default')) {
class MENU_Default
{
	function MENU_Default()
	{
		JToolBarHelper::publishList();
		JToolBarHelper::unpublishList();
		JToolBarHelper::addNew();
		JToolBarHelper::editList();
		JToolBarHelper::deleteList();
		JToolBarHelper::spacer();
	}
}
}


if (!class_exists('mosTabs')) {

if (!class_exists('JPaneTabs')) {
	JLoader::load('JPaneTabs');
}

class mosTabs extends JPaneTabs
{
	var $useCookies = false;
	
	function mosTabs( $useCookies, $xhtml = null) {
		parent::__construct( array('useCookies' => $useCookies) );
	}
	
	function __construct( $useCookies, $xhtml = null) {
		parent::__construct( array('useCookies' => $useCookies) );
	}

	function startTab( $tabText, $paneid ) {
		echo $this->startPanel( $tabText, $paneid);
	}

	function endTab() {
		echo $this->endPanel();
	}

	function startPane( $tabText ){
		echo parent::startPane( $tabText );
	}

	function endPane(){
		echo parent::endPane();
	}
}
}


if (!class_exists('mosCommonHTML')) {
class mosCommonHTML
{
	/**
 	 * Legacy function, use {@link JHTML::_('legend');} instead
 	 *
 	 * @deprecated	As of version 1.5
 	 */
	function ContentLegend( )
	{
		JHTML::addIncludePath( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'html' );
		JHTML::_('grid.legend');
	}

	/**
 	 * Legacy function, deprecated
 	 *
 	 * @deprecated	As of version 1.5
 	 */
	function menuLinksContent( &$menus )
	{
		foreach( $menus as $menu ) {
			?>
			<tr>
				<td colspan="2">
					<hr />
				</td>
			</tr>
			<tr>
				<td width="90" valign="top">
					<?php echo JText::_( 'Menu' ); ?>
				</td>
				<td>
					<a href="javascript:go2('go2menu','<?php echo $menu->menutype; ?>');" title="<?php echo JText::_( 'Go to Menu' ); ?>">
						<?php echo $menu->menutype; ?></a>
				</td>
			</tr>
			<tr>
				<td width="90" valign="top">
				<?php echo JText::_( 'Link Name' ); ?>
				</td>
				<td>
					<strong>
					<a href="javascript:go2('go2menuitem','<?php echo $menu->menutype; ?>','<?php echo $menu->id; ?>');" title="<?php echo JText::_( 'Go to Menu Item' ); ?>">
						<?php echo $menu->name; ?></a>
					</strong>
				</td>
			</tr>
			<tr>
				<td width="90" valign="top">
					<?php echo JText::_( 'State' ); ?>
				</td>
				<td>
					<?php
					switch ( $menu->published ) {
						case -2:
							echo '<font color="red">'. JText::_( 'Trashed' ) .'</font>';
							break;
						case 0:
							echo JText::_( 'UnPublished' );
							break;
						case 1:
						default:
							echo '<font color="green">'. JText::_( 'Published' ) .'</font>';
							break;
					}
					?>
				</td>
			</tr>
			<?php
		}
		?>
		<tr>
			<td colspan="2">
				<input type="hidden" name="menu" value="" />
				<input type="hidden" name="menuid" value="" />
			</td>
		</tr>
		<?php
	}

	/**
 	 * Legacy function, deprecated
 	 *
 	 * @deprecated	As of version 1.5
 	 */
	function menuLinksSecCat( &$menus )
	{
		$i = 1;
		foreach( $menus as $menu ) {
			?>
			<fieldset>
				<legend align="right"> <?php echo $i; ?>. </legend>

				<table class="admintable">
				<tr>
					<td valign="top" class="key">
						<?php echo JText::_( 'Menu' ); ?>
					</td>
					<td>
						<a href="javascript:go2('go2menu','<?php echo $menu->menutype; ?>');" title="<?php echo JText::_( 'Go to Menu' ); ?>">
							<?php echo $menu->menutype; ?></a>
					</td>
				</tr>
				<tr>
					<td valign="top" class="key">
						<?php echo JText::_( 'Type' ); ?>
					</td>
					<td>
						<?php echo $menu->type; ?>
					</td>
				</tr>
				<tr>
					<td valign="top" class="key">
						<?php echo JText::_( 'Item Name' ); ?>
					</td>
					<td>
						<strong>
						<a href="javascript:go2('go2menuitem','<?php echo $menu->menutype; ?>','<?php echo $menu->id; ?>');" title="<?php echo JText::_( 'Go to Menu Item' ); ?>">
							<?php echo $menu->name; ?></a>
						</strong>
					</td>
				</tr>
				<tr>
					<td valign="top" class="key">
						<?php echo JText::_( 'State' ); ?>
					</td>
					<td>
						<?php
						switch ( $menu->published ) {
							case -2:
								echo '<font color="red">'. JText::_( 'Trashed' ) .'</font>';
								break;
							case 0:
								echo JText::_( 'UnPublished' );
								break;
							case 1:
							default:
								echo '<font color="green">'. JText::_( 'Published' ) .'</font>';
								break;
						}
						?>
					</td>
				</tr>
				</table>
			</fieldset>
			<?php
			$i++;
		}
		?>
		<input type="hidden" name="menu" value="" />
		<input type="hidden" name="menuid" value="" />
		<?php
	}

	/**
 	 * Legacy function, use {@link JHTMLGrid::checkedOut()} instead
 	 *
 	 * @deprecated	As of version 1.5
 	 */
	function checkedOut( &$row, $overlib=1 )
	{
		jimport('joomla.html.html.grid');
		return JHTML::_('grid.checkedOut',$row, $overlib);
	}

	/**
 	 * Legacy function, use {@link JHTML::_('behavior.tooltip')} instead
 	 *
 	 * @deprecated	As of version 1.5
 	 */
	function loadOverlib()
	{
		JHTML::_('behavior.tooltip');
	}

	/**
 	 * Legacy function, use {@link JHTML::_('behavior.calendar')} instead
 	 *
 	 * @deprecated	As of version 1.5
 	 */
	function loadCalendar()
	{
		JHTML::_('behavior.calendar');
	}

	/**
 	 * Legacy function, use {@link JHTML::_('grid.access')} instead
 	 *
 	 * @deprecated	As of version 1.5
 	 */
	function AccessProcessing( &$row, $i, $archived=NULL )
	{
		return JHTML::_('grid.access',  $row, $i, $archived);
	}

	/**
 	 * Legacy function, use {@link JHTML::_('grid.checkedout')} instead
 	 *
 	 * @deprecated	As of version 1.5
 	 */
	function CheckedOutProcessing( &$row, $i )
	{
		return JHTML::_('grid.checkedout',  $row, $i);
	}

	/**
 	 * Legacy function, use {@link JHTML::_('grid.published')} instead
 	 *
 	 * @deprecated	As of version 1.5
 	 */
	function PublishedProcessing( &$row, $i, $imgY='tick.png', $imgX='publish_x.png' )
	{
		return JHTML::_('grid.published',$row, $i, $imgY, $imgX);
	}

	/**
 	 * Legacy function, use {@link JHTML::_('grid.state')} instead
 	 *
 	 * @deprecated	As of version 1.5
 	 */
	function selectState( $filter_state=NULL, $published='Published', $unpublished='Unpublished', $archived=NULL )
	{
		return JHTML::_('grid.state', $filter_state, $published, $unpublished, $archived);
	}

	/**
 	 * Legacy function, use {@link JHTML::_('grid.order')} instead
 	 *
 	 * @deprecated	As of version 1.5
 	 */
	function saveorderButton( $rows, $image='filesave.png' )
	{
		echo JHTML::_('grid.order', $rows, $image);
	}

	/**
 	 * Legacy function, use {@link echo JHTML::_('grid.sort')} instead
 	 *
 	 * @deprecated	As of version 1.5
 	 */
	function tableOrdering( $text, $ordering, &$lists, $task=NULL )
	{
		// TODO: We may have to invert order_Dir here because this control now does the flip for you
		echo JHTML::_('grid.sort',  $text, $ordering, @$lists['order_Dir'], @$lists['order'], $task);
	}
}
}


// Register legacy classes for autoloading
JLoader::register('JTableContent'  , JPATH_LIBRARIES.DS.'joomla'.DS.'database'.DS.'table'.DS.'content.php');

/**
 * Legacy class, use {@link JTableContent} instead
 *
 * @deprecated	As of version 1.5
 * @package	Joomla.Legacy
 * @subpackage	1.5
 */
if (!class_exists('mosContent')) {

if (!class_exists('JTableContent')) {
	JLoader::load('JTableContent');
}

class mosContent extends JTableContent
{
	/**
	 * Constructor
	 */
	function __construct( &$db )
	{
		parent::__construct( $db );
	}

	function mosComponent( &$db )
	{
		parent::__construct($db );
	}

	/**
	 * Legacy Method, use {@link JTable::reorder()} instead
	 * @deprecated As of 1.5
	 */
	function updateOrder( $where='' )
	{
		return $this->reorder( $where );
	}

	/**
	 * Legacy Method, use {@link JTable::publish()} instead
	 * @deprecated As of 1.0.3
	 */
	function publish_array( $cid=null, $publish=1, $user_id=0 )
	{
		$this->publish( $cid, $publish, $user_id );
	}
}
}


// Register legacy classes for autoloading
JLoader::register('JTable', JPATH_LIBRARIES.DS.'joomla'.DS.'database'.DS.'table.php');

/**
 * Legacy class, derive from {@link JTable} instead
 *
 * @deprecated	As of version 1.5
 * @package	Joomla.Legacy
 * @subpackage	1.5
 */
if (!class_exists('mosDBTable')) {

if (!class_exists('JTable')) {
	JLoader::load('JTable');
}

class mosDBTable extends JTable
{
	/**
	 * Error number
	 *
	 * @var		string
	 * @access	protected
	 */
	var $_error = '';

	/**
	 * Error number
	 *
	 * @var		int
	 * @access	protected
	 */
	var $_errorNum = 0;

	/**
	 * Constructor
	 */
	function __construct($table, $key, &$db)
	{
		parent::__construct( $table, $key, $db );
	}

	function mosDBTable($table, $key, &$db)
	{
		parent::__construct( $table, $key, $db );
	}

	/**
	 * Legacy Method, use {@link JTable::reorder()} instead
	 * @deprecated As of 1.5
	 */
	function updateOrder( $where='' )
	{
		return $this->reorder( $where );
	}

	/**
	 * Legacy Method, use {@link JTable::publish()} instead
	 * @deprecated As of 1.0.3
	 */
	function publish_array( $cid=null, $publish=1, $user_id=0 )
	{
		$this->publish( $cid, $publish, $user_id );
	}

	/**
	 * Legacy Method, make sure you use {@link JRequest::get()} or {@link JRequest::getVar()} instead
	 * @deprecated As of 1.5
	 */
	function filter( $ignoreList=null )
	{
		$ignore = is_array( $ignoreList );

		$filter = & JFilterInput::getInstance();
		foreach ($this->getProperties() as $k => $v)
		{
			if ($ignore && in_array( $k, $ignoreList ) ) {
				continue;
			}
			$this->$k = $filter->clean( $this->$k );
		}
	}

	/**
	 * Legacy Method, use {@link JObject::getProperties()}  instead
	 * @deprecated As of 1.5
	 */
	function getPublicProperties()
	{
		$properties = $this->getProperties();
		return array_keys($properties);
	}

	/**
	 * Legacy Method, use {@link JObject::getError()}  instead
	 * @deprecated As of 1.5
	 */
	function getError($i = null, $toString = true )
	{
		return $this->_error;
	}

	/**
	 * Legacy Method, use {@link JObject::setError()}  instead
	 * @deprecated As of 1.5
	 */
	function setErrorNum( $value )
	{
		$this->_errorNum = $value;
	}

	/**
	 * Legacy Method, use {@link JObject::getError()}  instead
	 * @deprecated As of 1.5
	 */
	function getErrorNum()
	{
		return $this->_errorNum;
	}
}
}


/**
 * Legacy function, use <jdoc:exists type="modules" condition="{POSITION}" /> instead
 *
 * @deprecated	As of version 1.5
 */
if (!function_exists('mosCountModules')) {
function mosCountModules( $position='left' ) {
	jimport('joomla.application.module.helper');
	return count(JModuleHelper::getModules($position));
}
}
/**
 * Legacy function, use <jdoc:include type="component" /> instead
 *
 * @deprecated	As of version 1.5
 */
if (!function_exists('mosMainBody')) {
function mosMainBody() {
	?><jdoc:include type="component" /><?php
}
}
/**
 * Legacy function, use <jdoc:include type="modules" /> instead
 *
 * @deprecated		As of version 1.5
 */
if (!function_exists('mosLoadModules')) {
function mosLoadModules( $position='left', $style=0 )
{
	// Select the module chrome function
	if (is_numeric($style))
	{
		switch ( $style )
		{
			case -3:
				$style = 'rounded';
				break;

			case -2:
				$style = 'xhtml';
				break;

			case -1:
				$style = 'raw';
				break;

			case 0  :
			default :
				$style = 'table';
				break;
		}
	}
	?><jdoc:include type="modules" name="<?php echo $position ?>" style="<?php echo $style ?>"/><?php
}
}
/**
 * Legacy function, use <jdoc:include type="module" /> instead
 *
 * @deprecated		As of version 1.5
 */
if (!function_exists('mosLoadModule')) {
function mosLoadModule( $name, $style=-1 ) {
	?><jdoc:include type="module" name="<?php echo $name ?>" style="<?php echo $style ?>" /><?php
}
}
/**
 * Legacy function, use <jdoc:include type="head" /> instead
 *
 * @deprecated	As of version 1.5
 */
if (!function_exists('mosShowHead')) {
function mosShowHead() {
	?><jdoc:include type="head" /><?php
}
}
/**
 * Legacy function, using <jdoc:exists> instead
 *
 * @deprecated	As of version 1.5
 */
if (!function_exists('mosCountAdminModules')) {
function mosCountAdminModules(  $position='left' ) {
	$document =& JFactory::getDocument();
	return count($document->getModules($position));
}
}
/**
 * Legacy function, using <jdoc:include type="component" /> instead
 *
 * @deprecated	As of version 1.5
 */
if (!function_exists('mosMainBody_Admin')) {
function mosMainBody_Admin() {
	?><jdoc:include type="component" /><?php
}
}
/**
 * Legacy function, using <jdoc:include type="modules" /> instead
 *
 * @deprecated	As of version 1.5
 */
if (!function_exists('mosLoadAdminModules')) {
function mosLoadAdminModules( $position='left', $style=0 ) {

	// Select the module chrome function
	if (is_numeric($style))
	{
		switch ( $style )
		{
			case 2:
				$style = 'xhtml';
				break;

			case 0  :
			default :
				$style = 'raw';
				break;
		}
	}
	?><jdoc:include type="modules" name="<?php echo $position ?>" style="<?php echo $style ?>" /><?php
}
}
/**
 * Legacy function, using <jdoc:include type="module" /> instead
 *
 * @deprecated	As of version 1.5
 */
if (!function_exists('mosLoadAdminModule')) {
function mosLoadAdminModule( $name, $style=0 ) {
	?><jdoc:include type="module" name="<?php echo $name ?>" style="<?php echo $style ?>" /><?php
}
}
/**
 * Legacy function, using <jdoc:include type="head" /> instead
 *
 * @deprecated	As of version 1.5
 */
if (!function_exists('mosShowHead_Admin')) {
function mosShowHead_Admin() {
	?><jdoc:include type="head" /><?php
}
}

/**
 * Legacy function, use {@link JArrayHelper JArrayHelper->toObject()} instead
 *
 * @deprecated	As of version 1.5
 */
if (!function_exists('mosBindArrayToObject')) {
function mosBindArrayToObject( $array, &$obj, $ignore='', $prefix=NULL, $checkSlashes=true )
{
	if (!is_array( $array ) || !is_object( $obj )) {
		return (false);
	}

	foreach (get_object_vars($obj) as $k => $v)
	{
		if( substr( $k, 0, 1 ) != '_' )
		{
			// internal attributes of an object are ignored
			if (strpos( $ignore, $k) === false)
			{
				if ($prefix) {
					$ak = $prefix . $k;
				} else {
					$ak = $k;
				}
				if (isset($array[$ak])) {
					if (is_array($array[$ak])) {
						$els = array();											
						if ( $checkSlashes && get_magic_quotes_gpc() ) 
						{
							foreach( $array[$ak] AS $el ) 
							{
								$els[] =  stripslashes( $el );
							}
						} else {
							$els = $array[$ak];
						}
						
						$obj->$k = $els; 
					} else {
						$obj->$k = ($checkSlashes && get_magic_quotes_gpc()) ? stripslashes( $array[$ak] ) : $array[$ak];
					}
				}
			}
		}
	}

	return true;
}
}

/**
 * Legacy function, use {@link JUtility::getHash()} instead
 *
 * @deprecated	As of version 1.5
 */
if (!function_exists('mosHash')) {
function mosHash( $seed ) {
	return JUtility::getHash( $seed );
}
}
/**
* Legacy function
 *
 * @deprecated	As of version 1.5
*/
if (!function_exists('mosNotAuth')) {
function mosNotAuth()
{
	$user =& JFactory::getUser();
	echo JText::_('ALERTNOTAUTH');
	if ($user->get('id') < 1) {
		echo "<br />" . JText::_( 'You need to login.' );
	}
}
}
/**
 * Legacy function, use (@link JError} or {@link JApplication::redirect()} instead.
 *
 * @deprecated	As of version 1.5
 */
if (!function_exists('mosErrorAlert')) {
	function mosErrorAlert( $text, $action='window.history.go(-1);', $mode=1 )
	{
		$app = & JFactory::getApplication();
		
		$text = nl2br( $text );
		$text = addslashes( $text );
		$text = strip_tags( $text );
	
		switch ( $mode ) {
			case 2:
				echo "<script>$action</script> \n";
				break;
	
			case 1:
			default:
				echo "<script>alert('$text'); $action</script> \n";
				echo '<noscript>';
				echo "$text\n";
				echo '</noscript>';
				break;
		}	
		
		$app->close();
	}
}
/**
 * Legacy function, use {@link JPath::clean()} instead
 *
 * @deprecated	As of version 1.5
 */
if (!function_exists('mosPathName')) {
function mosPathName($p_path, $p_addtrailingslash = true)
{
	jimport('joomla.filesystem.path');
	$path = JPath::clean($p_path);
	if ($p_addtrailingslash) {
		$path = rtrim($path, DS) . DS;
	}
	return $path;
}
}
/**
 * Legacy function, use {@link JFolder::files()} or {@link JFolder::folders()} instead
 *
 * @deprecated	As of version 1.5
 */
if (!function_exists('mosReadDirectory')) {
function mosReadDirectory( $path, $filter='.', $recurse=false, $fullpath=false  )
{
	$arr = array(null);

	// Get the files and folders
	jimport('joomla.filesystem.folder');
	$files		= JFolder::files($path, $filter, $recurse, $fullpath);
	$folders	= JFolder::folders($path, $filter, $recurse, $fullpath);
	// Merge files and folders into one array
	$arr = array_merge($files, $folders);
	// Sort them all
	asort($arr);
	return $arr;
}
}
/**
 * Legacy function, use {@link JFactory::getMailer()} instead
 *
 * @deprecated	As of version 1.5
 */
if (!function_exists('mosCreateMail')) {
function mosCreateMail( $from='', $fromname='', $subject, $body ) {

	$mail =& JFactory::getMailer();

	$mail->From 	= $from ? $from : $mail->From;
	$mail->FromName = $fromname ? $fromname : $mail->FromName;
	$mail->Subject 	= $subject;
	$mail->Body 	= $body;

	return $mail;
}
}
/**
 * Legacy function, use {@link JUtility::sendMail()} instead
 *
 * @deprecated	As of version 1.5
 */
if (!function_exists('mosMail')) {
function mosMail($from, $fromname, $recipient, $subject, $body, $mode=0, $cc=NULL, $bcc=NULL, $attachment=NULL, $replyto=NULL, $replytoname=NULL ) {
	return JUTility::sendMail($from, $fromname, $recipient, $subject, $body, $mode, $cc, $bcc, $attachment, $replyto, $replytoname );
}
}
/**
 * Legacy function, use {@link JUtility::sendAdminMail()} instead
 *
 * @deprecated	As of version 1.5
 */
if (!function_exists('mosSendAdminMail')) {
function mosSendAdminMail( $adminName, $adminEmail, $email, $type, $title, $author ) {
	JUtility::sendAdminMail( $adminName, $adminEmail, $email, $type, $title, $author );
}
}
/**
 * Legacy function, use {@link JUserHelper::genRandomPassword()} instead
 *
 * @deprecated	As of version 1.5
 */
if (!function_exists('mosMakePassword')) {
function mosMakePassword() {
	jimport('joomla.user.helper');
	
	if (!class_exists('JUserHelper')) {
		JLoader::load('JUserHelper');
	}	

	return JUserHelper::genRandomPassword();
}
}
/**
 * Legacy function, use {@link JApplication::redirect() JApplication->redirect()} instead
 *
 * @deprecated	As of version 1.5
 */
if (!function_exists('mosRedirect')) {
function mosRedirect( $url, $msg='' ) {
	$app = & JFactory::getApplication();
	$app->redirect( $url, $msg );
}
}
/**
 * Legacy function, use {@link JFolder::create()}
 *
 * @deprecated	As of version 1.5
 */
if (!function_exists('mosMakePath')) {
function mosMakePath($base, $path='', $mode = NULL) {

	if ($mode===null) {
		$mode = 0755;
	}

	jimport('joomla.filesystem.folder');
	return JFolder::create($base.$path, $mode);
}
}
/**
 * Legacy function, use {@link JArrayHelper::toInteger()} instead
 *
 * @deprecated	As of version 1.5
 */
if (!function_exists('mosArrayToInts')) {
function mosArrayToInts( &$array, $default=null ) {
	return JArrayHelper::toInteger( $array, $default );
}
}
/**
 * Legacy function, use {@link JException::getTrace() JException->getTrace()} instead
 *
 * @deprecated	As of version 1.5
 */
if (!function_exists('mosBackTrace')) {
function mosBackTrace( $message='' ) {
	if (function_exists( 'debug_backtrace' )) {
		echo '<div align="left">';
		if ($message) {
			echo '<p><strong>' . $message . '</strong></p>';
		}
		foreach( debug_backtrace() as $back) {
			if (@$back['file']) {
				echo '<br />' . str_replace( JPATH_ROOT, '', $back['file'] ) . ':' . $back['line'];
			}
		}
		echo '</div>';
	}
}
}
/**
 * Legacy function, use {@link JPath::setPermissions()} instead
 *
 * @deprecated	As of version 1.5
 */
if (!function_exists('mosChmod')) {
function mosChmod( $path ) {
	jimport('joomla.filesystem.path');
	return JPath::setPermissions( $path );
}
}
/**
 * Legacy function, use {@link JPath::setPermissions()} instead
 *
 * @deprecated	As of version 1.5
 */
 if (!function_exists('mosChmodRecursive')) {
function mosChmodRecursive( $path, $filemode=NULL, $dirmode=NULL ) {
	jimport('joomla.filesystem.path');
	return JPath::setPermissions( $path, $filemode, $dirmode );
}
}

/**
 * Legacy function, use {@link JPath::canChmod()} instead
 *
 * @deprecated	As of version 1.5
 */
 if (!function_exists('mosIsChmodable')) {
function mosIsChmodable( $file ) {
	jimport('joomla.filesystem.path');
	return JPath::canChmod( $file );
}
}
/**
 * Legacy function, replaced by geshi bot
 *
 * @deprecated	As of version 1.5
 */
 if (!function_exists('mosShowSource')) {
function mosShowSource( $filename, $withLineNums=false ) {

	ini_set('highlight.html', '000000');
	ini_set('highlight.default', '#800000');
	ini_set('highlight.keyword','#0000ff');
	ini_set('highlight.string', '#ff00ff');
	ini_set('highlight.comment','#008000');

	if (!($source = @highlight_file( $filename, true ))) {
		return JText::_( 'Operation Failed' );
	}
	$source = explode("<br />", $source);

	$ln = 1;

	$txt = '';
	foreach( $source as $line ) {
		$txt .= "<code>";
		if ($withLineNums) {
			$txt .= "<font color=\"#aaaaaa\">";
			$txt .= str_replace( ' ', '&nbsp;', sprintf( "%4d:", $ln ) );
			$txt .= "</font>";
		}
		$txt .= "$line<br /><code>";
		$ln++;
	}
	return $txt;
}
}
/**
 * Legacy function, use mosLoadModule( 'breadcrumb', -1 ); instead
 *
 * @deprecated	As of version 1.5
 */
 if (!function_exists('mosPathWay')) {
function mosPathWay() {
	mosLoadModule('breadcrumb', -1);
}
}
/**
 * Legacy function, use {@link JBrowser::getInstance()} instead
 *
 * @deprecated	As of version 1.5
 */
 if (!function_exists('mosGetBrowser')) {
function mosGetBrowser( $agent ) {
	jimport('joomla.environment.browser');
	$instance =& JBrowser::getInstance();
	return $instance;
}
}

/**
 * Legacy function, use {@link JApplication::getBrowser()} instead
 *
 * @deprecated	As of version 1.5
 */
 if (!function_exists('mosGetOS')) {
function mosGetOS( $agent ) {
	jimport('joomla.environment.browser');
	$instance =& JBrowser::getInstance();
	return $instance->getPlatform();
}
}
/**
 * Legacy function, use {@link JArrayHelper::getValue()} instead
 *
 * @deprecated	As of version 1.5
 */
 if (!function_exists('mosGetParam')) {
function mosGetParam( &$arr, $name, $def=null, $mask=0 )
{
	// Static input filters for specific settings
	static $noHtmlFilter	= null;
	static $safeHtmlFilter	= null;

	$var = JArrayHelper::getValue( $arr, $name, $def, '' );

	// If the no trim flag is not set, trim the variable
	if (!($mask & 1) && is_string($var)) {
		$var = trim($var);
	}

	// Now we handle input filtering
	if ($mask & 2) {
		// If the allow html flag is set, apply a safe html filter to the variable
		if (is_null($safeHtmlFilter)) {
			$safeHtmlFilter = & JFilterInput::getInstance(null, null, 1, 1);
		}
		$var = $safeHtmlFilter->clean($var, 'none');
	} elseif ($mask & 4) {
		// If the allow raw flag is set, do not modify the variable
		$var = $var;
	} else {
		// Since no allow flags were set, we will apply the most strict filter to the variable
		if (is_null($noHtmlFilter)) {
			$noHtmlFilter = & JFilterInput::getInstance(/* $tags, $attr, $tag_method, $attr_method, $xss_auto */);
		}
		$var = $noHtmlFilter->clean($var, 'none');
	}
	return $var;
}
}

/**
 * Legacy function, use {@link JHTML::_('list.genericordering', )} instead
 *
 * @deprecated	As of version 1.5
 */
 if (!function_exists('mosGetOrderingList')) {
function mosGetOrderingList( $sql, $chop='30' )
{
	return JHTML::_('list.genericordering', $sql, $chop);
}
}
/**
 * Legacy function, use {@link JRegistry} instead
 *
 * @deprecated	As of version 1.5
 */
 if (!function_exists('mosParseParams')) {
function mosParseParams( $txt ) {

	$registry = new JRegistry();
	$registry->loadINI($txt);
	return $registry->toObject( );
}
}

/**
 * Legacy function, removed
 *
 * @deprecated	As of version 1.5
 */
 if (!function_exists('mosLoadComponent')) {
function mosLoadComponent( $name )
{
	// set up some global variables for use by the frontend component
	global $database;
	$name = JFilterInput::clean($name, 'cmd');
	$path = JPATH_SITE.DS.'components'.DS.'com_'.$name.DS.$name.'.php';
	if (file_exists($path)) {
		include $path;
	}
}
}
/**
 * Legacy function, use {@link JEditor::init()} instead
 *
 * @deprecated	As of version 1.5
 */
if (!function_exists('initEditor')) {
function initEditor()
{
	$editor =& JFactory::getEditor();
	echo $editor->initialise();
}
}
/**
 * Legacy function, use {@link JEditor::save()} or {@link JEditor::getContent()} instead
 *
 * @deprecated	As of version 1.5
 */
if (!function_exists('getEditorContents')) {
function getEditorContents($editorArea, $hiddenField)
{
	jimport( 'joomla.html.editor' );
	$editor =& JFactory::getEditor();
	echo $editor->save( $hiddenField );
}
}
/**
 * Legacy function, use {@link JEditor::display()} instead
 *
 * @deprecated	As of version 1.5
 */
if (!function_exists('editorArea')) {
function editorArea($name, $content, $hiddenField, $width, $height, $col, $row)
{
	jimport( 'joomla.html.editor' );
	$editor =& JFactory::getEditor();
	echo $editor->display($hiddenField, $content, $width, $height, $col, $row);
}
}
/**
 * Legacy function, use {@link JMenu::authorize()} instead
 *
 * @deprecated	As of version 1.5
 */
if (!function_exists('mosMenuCheck')) {
function mosMenuCheck( $Itemid, $menu_option, $task, $gid )
{
	$user =& JFactory::getUser();
	$menus =& JSite::getMenu();
	return $menus->authorize($Itemid, $user->get('aid'));
}
}
/**
 * Legacy function, use {@link JArrayHelper::fromObject()} instead
 *
 * @deprecated	As of version 1.5
 */
if (!function_exists('mosObjectToArray')) {
function mosObjectToArray( $p_obj, $recurse = true, $regex = null )
{
	$result = JArrayHelper::fromObject( $p_obj, $recurse, $regex );
	return $result;
}
}
/**
 * Legacy function, use {@link JHTML::_('date', )} instead
 *
 * @deprecated	As of version 1.5
 */
if (!function_exists('mosFormatDate')) {
function mosFormatDate( $date = 'now', $format = null, $offset = null )  {

	if ( ! $format )
	{
		$format = JText::_('DATE_FORMAT_LC1');
	}

	return JHTML::_('date', $date, $format, $offset);
}
}
/**
 * Legacy function, use {@link JHTML::_('date', )} instead
 *
 * @deprecated	As of version 1.5
 */
if (!function_exists('mosCurrentDate')) {
function mosCurrentDate( $format="" )
{
	if ($format=="") {
		$format = JText::_( 'DATE_FORMAT_LC1' );
	}

	return JHTML::_('date', 'now', $format);
}
}
/**
 * Legacy function, use {@link JFilterOutput::objectHTMLSafe()} instead
 *
 * @deprecated	As of version 1.5
 */
if (!function_exists('mosMakeHtmlSafe')) {
function mosMakeHtmlSafe( &$mixed, $quote_style=ENT_QUOTES, $exclude_keys='' ) {
	JFilterOutput::objectHTMLSafe( $mixed, $quote_style, $exclude_keys );
}
}
/**
 * Legacy function, handled by {@link JDocument} Zlib outputfilter
 *
 * @deprecated	As of version 1.5
 */
if (!function_exists('initGzip')) {
function initGzip()
{
	global $do_gzip_compress;


	// attempt to disable session.use_trans_sid
	ini_set('session.use_trans_sid', false);

	$do_gzip_compress = FALSE;
	
	$JLMS_CONFIG = & JLMSFactory::getConfig();
	
	if ( $JLMS_CONFIG->get('gzip') == 1) {
		$phpver = phpversion();
		$useragent = mosGetParam( $_SERVER, 'HTTP_USER_AGENT', '' );
		$canZip = mosGetParam( $_SERVER, 'HTTP_ACCEPT_ENCODING', '' );

		if ( $phpver >= '4.0.4pl1' &&
				( strpos($useragent,'compatible') !== false ||
					strpos($useragent,'Gecko') !== false
				)
			) {
			// Check for gzip header or northon internet securities
			if ( isset($_SERVER['HTTP_ACCEPT_ENCODING']) ) {
				$encodings = explode(',', strtolower($_SERVER['HTTP_ACCEPT_ENCODING']));
			}
			if ( (in_array('gzip', $encodings) || isset( $_SERVER['---------------']) ) && extension_loaded('zlib') && function_exists('ob_gzhandler') && !ini_get('zlib.output_compression') && !ini_get('session.use_trans_sid') ) {
				// You cannot specify additional output handlers if
				// zlib.output_compression is activated here
				ob_start( 'ob_gzhandler' );
				return;
			}
		} else if ( $phpver > '4.0' ) {
			if ( strpos($canZip,'gzip') !== false ) {
				if (extension_loaded( 'zlib' )) {
					$do_gzip_compress = TRUE;
					ob_start();
					ob_implicit_flush(0);

					header( 'Content-Encoding: gzip' );
					return;
				}
			}
		}
	}
	ob_start();
}
}
/**
 * Legacy function, use JFolder::delete($path)
 *
 * @deprecated	As of version 1.5
 */
if (!function_exists('deldir')) {
function deldir( $dir )
{
	$current_dir = opendir( $dir );
	$old_umask = umask(0);
	while ($entryname = readdir( $current_dir )) {
		if ($entryname != '.' and $entryname != '..') {
			if (is_dir( $dir . $entryname )) {
				deldir( mosPathName( $dir . $entryname ) );
			} else {
				@chmod($dir . $entryname, 0777);
				unlink( $dir . $entryname );
			}
		}
	}
	umask($old_umask);
	closedir( $current_dir );
	return rmdir( $dir );
}
}
/**
 * Legacy function, handled by {@link JDocument} Zlib outputfilter
 *
 * @deprecated	As of version 1.5
 */
if (!function_exists('doGzip')) {
function doGzip()
{
	global $do_gzip_compress;
	if ( $do_gzip_compress )
	{
		$gzip_contents = ob_get_contents();
		ob_end_clean();

		$gzip_size = strlen($gzip_contents);
		$gzip_crc = crc32($gzip_contents);

		$gzip_contents = gzcompress($gzip_contents, 9);
		$gzip_contents = substr($gzip_contents, 0, strlen($gzip_contents) - 4);

		echo "\x1f\x8b\x08\x00\x00\x00\x00\x00";
		echo $gzip_contents;
		echo pack('V', $gzip_crc);
		echo pack('V', $gzip_size);
	} else {
		ob_end_flush();
	}
}
}
/**
 * Legacy function, use {@link JArrayHelper::sortObjects()} instead
 *
 * @deprecated	As of version 1.5
 */
if (!function_exists('SortArrayObjects')) {
function SortArrayObjects( &$a, $k, $sort_direction=1 )
{
	JArrayHelper::sortObjects($a, $k, $sort_direction);
}
}
/**
 * Legacy function, {@link JRequest::getVar()}
 *
 * @deprecated	As of version 1.5
 */
if (!function_exists('josGetArrayInts')) {
function josGetArrayInts( $name, $type=NULL ) {

	$array	=  JRequest::getVar($name, array(), 'default', 'array' );

	return $array;
}
}
/**
 * Legacy function, {@link JSession} transparently checks for spoofing attacks
 *
 * @deprecated	As of version 1.5
 */
if (!function_exists('josSpoofCheck')) {
function josSpoofCheck( $header=false, $alternate=null )
{
	// Lets make sure they saw the html form
	$check = true;
	$hash	= josSpoofValue($alternate);
	$valid	= JRequest::getBool( $hash, 0, 'post' );
	if (!$valid) {
		$check = false;
	}

	// Make sure request came from a client with a user agent string.
	if (!isset( $_SERVER['HTTP_USER_AGENT'] )) {
		$check = false;
	}

	// Check to make sure that the request was posted as well.
	$requestMethod = JArrayHelper::getValue( $_SERVER, 'REQUEST_METHOD' );
	if ($requestMethod != 'POST') {
		$check = false;
	}

	if (!$check)
	{
		header( 'HTTP/1.0 403 Forbidden' );
		jexit( JText::_('E_SESSION_TIMEOUT') );
	}
}
}
/**
 * Legacy function, use {@link JUtility::getToken()} instead
 *
 * @deprecated	As of version 1.5
 */
if (!function_exists('josSpoofValue')) {
function josSpoofValue($alt = NULL)
{
	if ($alt) {
		if ( $alt == 1 ) {
			$random		= date( 'Ymd' );
		} else {
			$random		= $alt . date( 'Ymd' );
		}
	} else {
		$random		= date( 'dmY' );
	}
	
	$JLMS_CONFIG = & JLMSFactory::getConfig();
		
	$validate 	= 'j' . mosHash( $JLMS_CONFIG->get( 'db' ) . $random );

	return $validate;
}
}
/**
 * Legacy function to load the tooltip library.
 *
 * @deprecated	As of version 1.5
 */
if (!function_exists('loadOverlib')) {
function loadOverlib() {
	JHTML::_('behavior.tooltip');
}
}
/**
* Legacy utility function to provide ToolTips
*
* @deprecated	As of version 1.5
*/
if (!function_exists('mosToolTip')) {
function mosToolTip( $tooltip, $title='', $width='', $image='tooltip.png', $text='', $href='', $link=1 )
{
	// Initialize the toolips if required
	static $init;
	if ( ! $init )
	{
		JHTML::_('behavior.tooltip');
		$init = true;
	}

	return JHTML::_('tooltip', $tooltip, $title, $image, $text, $href, $link);
}
}
/**
 * Legacy function to convert an internal Joomla URL to a humanly readible URL.
 *
 * @deprecated	As of version 1.5
 */
if (!function_exists('sefRelToAbs')) {
function sefRelToAbs($value)
{
	// Replace all &amp; with & as the router doesn't understand &amp;
	$url = str_replace('&amp;', '&', $value);
	if(substr(strtolower($url),0,9) != "index.php") return $url;
	$uri    = JURI::getInstance();
	$prefix = $uri->toString(array('scheme', 'host', 'port'));
	return $prefix.JRoute::_($url);
}
}

/**
 * Legacy function to replaces &amp; with & for xhtml compliance
 *
 * @deprecated	As of version 1.5
 */
if (!function_exists('ampReplace')) {
function ampReplace( $text ) {
	return JFilterOutput::ampReplace($text);
}
}
/**
 * Legacy function to replaces &amp; with & for xhtml compliance
 *
 * @deprecated	As of version 1.5
 */
if (!function_exists('mosTreeRecurse')) {
function mosTreeRecurse( $id, $indent, $list, &$children, $maxlevel=9999, $level=0, $type=1 )
{
	jimport('joomla.html.html');
	return JHTML::_('menu.treerecurse', $id, $indent, $list, $children, $maxlevel, $level, $type);
}
}
/**
 * Legacy function, use {@link JHTML::tooltip()} instead
 *
 * @deprecated	As of version 1.5
 */
if (!function_exists('mosWarning')) {
function mosWarning($warning, $title='Joomla! Warning') {
	return JHTML::tooltip($warning, $title, 'warning.png', null, null, null);
}
}


/**
 * Legacy class, use {@link JHTML} instead
 *
 * @deprecated	As of version 1.5
 * @package	Joomla.Legacy
 * @subpackage	1.5
 */
if (!class_exists('mosHTML')) {
class mosHTML
{
	/**
 	 * Legacy function, use {@link JHTML::_('select.option')} instead
 	 *
 	 * @deprecated	As of version 1.5
 	*/
	function makeOption( $value, $text='', $value_name='value', $text_name='text' )
	{
		return JHTML::_('select.option', $value, $text, $value_name, $text_name);
	}

	/**
 	 * Legacy function, use {@link JHTML::_('select.genericlist')} instead
 	 *
 	 * @deprecated	As of version 1.5
 	*/
	function selectList( &$arr, $tag_name, $tag_attribs, $key = 'value', $text = 'text', $selected=NULL, $idtag=false, $flag=false )
	{
		for( $i = 0; $i < count($arr); $i++ ) 
		{
			$arr[$i]->{$text} = str_replace('&nbsp;','{nbsp}',$arr[$i]->{$text});
		}		
		
		$html = JHTML::_('select.genericlist', $arr, $tag_name, $tag_attribs, $key, $text, $selected, $idtag, $flag );
		$html = str_replace( '{nbsp}', '&nbsp;', $html );
		$html = str_replace( '</option>', '</option>'."\n", $html );
		return $html;
	}

	/**
 	 * Legacy function, use {@link JHTML::_('select.integerlist')} instead
 	 *
 	 * @deprecated	As of version 1.5
 	*/
	function integerSelectList( $start, $end, $inc, $tag_name, $tag_attribs, $selected, $format="" )
	{
		return JHTML::_('select.integerlist', $start, $end, $inc, $tag_name, $tag_attribs, $selected, $format) ;
	}

	/**
 	 * Legacy function, use {@link JHTML::_('select.radiolist')} instead
 	 *
 	 * @deprecated	As of version 1.5
 	*/
	function radioList( &$arr, $tag_name, $tag_attribs, $selected=null, $key='value', $text='text', $idtag=false )
	{
		return JHTML::_('select.radiolist', $arr, $tag_name, $tag_attribs, $key, $text,  $selected, $idtag) ;
	}

	/**
 	 * Legacy function, use {@link JHTML::_('select.booleanlist')} instead
 	 *
 	 * @deprecated	As of version 1.5
 	*/
	function yesnoRadioList( $tag_name, $tag_attribs, $selected, $yes='yes', $no='no', $id=false )
	{
		$app = & JFactory::getApplication();
		if ($app->isAdmin() && defined('_JLMS_YES') && defined('_JLMS_NO') && $yes == 'yes' && $no == 'no') {
			$yes = _JLMS_YES;
			$no = _JLMS_NO;
		} elseif (defined('_JLMS_YES_ALT_TITLE') && defined('_JLMS_NO_ALT_TITLE') && $yes == 'yes' && $no == 'no') {
			$yes = _JLMS_YES_ALT_TITLE;
			$no = _JLMS_NO_ALT_TITLE;
		}
		
		return JHTML::_('select.booleanlist',  $tag_name, $tag_attribs, $selected, $yes, $no, $id ) ;
	}

	/**
 	 * Legacy function, deprecated
 	 *
 	 * @deprecated	As of version 1.5
 	*/
	function writableCell( $folder, $relative=1, $text='', $visible=1 )
	{
		$writeable 		= '<b><font color="green">'. JText::_( 'Writable' ) .'</font></b>';
		$unwriteable 	= '<b><font color="red">'. JText::_( 'Unwritable' ) .'</font></b>';

		echo '<tr>';
		echo '<td class="item">';
		echo $text;
		if ( $visible ) {
			echo $folder . '/';
		}
		echo '</td>';
		echo '<td >';
		if ( $relative ) {
			echo is_writable( "../$folder" ) 	? $writeable : $unwriteable;
		} else {
			echo is_writable( "$folder" ) 		? $writeable : $unwriteable;
		}
		echo '</td>';
		echo '</tr>';
	}

	/**
 	 * Legacy function, deprecated
 	 *
 	 * @deprecated	As of version 1.5
 	*/
	function monthSelectList( $tag_name, $tag_attribs, $selected )
	{
		$arr = array(
			mosHTML::makeOption( '01', JText::_( 'JANUARY_SHORT' ) ),
			mosHTML::makeOption( '02', JText::_( 'FEBRUARY_SHORT' ) ),
			mosHTML::makeOption( '03', JText::_( 'MARCH_SHORT' ) ),
			mosHTML::makeOption( '04', JText::_( 'APRIL_SHORT' ) ),
			mosHTML::makeOption( '05', JText::_( 'MAY_SHORT' ) ),
			mosHTML::makeOption( '06', JText::_( 'JUNE_SHORT' ) ),
			mosHTML::makeOption( '07', JText::_( 'JULY_SHORT' ) ),
			mosHTML::makeOption( '08', JText::_( 'AUGUST_SHORT' ) ),
			mosHTML::makeOption( '09', JText::_( 'SEPTEMBER_SHORT' ) ),
			mosHTML::makeOption( '10', JText::_( 'OCTOBER_SHORT' ) ),
			mosHTML::makeOption( '11', JText::_( 'NOVEMBER_SHORT' ) ),
			mosHTML::makeOption( '12', JText::_( 'DECEMBER_SHORT' ) )
		);

		return mosHTML::selectList( $arr, $tag_name, $tag_attribs, 'value', 'text', $selected );
	}

	/**
 	 * Legacy function, deprecated
 	 *
 	 * @deprecated	As of version 1.5
 	*/
	function treeSelectList( &$src_list, $src_id, $tgt_list, $tag_name, $tag_attribs, $key, $text, $selected )
	{

		// establish the hierarchy of the menu
		$children = array();
		// first pass - collect children
		foreach ($src_list as $v ) {
			$pt = $v->parent;
			$list = @$children[$pt] ? $children[$pt] : array();
			array_push( $list, $v );
			$children[$pt] = $list;
		}
		// second pass - get an indent list of the items
		$ilist = JHTML::_('menu.treerecurse', 0, '', array(), $children );

		// assemble menu items to the array
		$this_treename = '';
		foreach ($ilist as $item) {
			if ($this_treename) {
				if ($item->id != $src_id && strpos( $item->treename, $this_treename ) === false) {
					$tgt_list[] = mosHTML::makeOption( $item->id, $item->treename );
				}
			} else {
				if ($item->id != $src_id) {
					$tgt_list[] = mosHTML::makeOption( $item->id, $item->treename );
				} else {
					$this_treename = "$item->treename/";
				}
			}
		}
		// build the html select list
		return mosHTML::selectList( $tgt_list, $tag_name, $tag_attribs, $key, $text, $selected );
	}

	/**
 	 * Legacy function, deprecated
 	 *
 	 * @deprecated	As of version 1.5
 	*/
	function yesnoSelectList( $tag_name, $tag_attribs, $selected, $yes='yes', $no='no' )
	{
		$arr = array(
			mosHTML::makeOption( 0, JText::_( $no ) ),
			mosHTML::makeOption( 1, JText::_( $yes ) ),
		);

		return mosHTML::selectList( $arr, $tag_name, $tag_attribs, 'value', 'text', (int) $selected );
	}

	/**
 	 * Legacy function, use {@link JHTML::_('grid.id')} instead
 	 *
 	 * @deprecated	As of version 1.5
 	*/
	function idBox( $rowNum, $recId, $checkedOut=false, $name='cid' )
	{
		return JHTML::_('grid.id', $rowNum, $recId, $checkedOut, $name);
	}

	/**
 	 * Legacy function, deprecated
 	 *
 	 * @deprecated	As of version 1.5
 	*/
	function sortIcon( $text, $base_href, $field, $state='none' )
	{
		$alts = array(
			'none' 	=> JText::_( 'No Sorting' ),
			'asc' 	=> JText::_( 'Sort Ascending' ),
			'desc' 	=> JText::_( 'Sort Descending' ),
		);

		$next_state = 'asc';
		if ($state == 'asc') {
			$next_state = 'desc';
		} else if ($state == 'desc') {
			$next_state = 'none';
		}

		if ($state == 'none') {
			$img = '';
		} else {
			$img = "<img src=\"images/sort_$state.png\" width=\"12\" height=\"12\" border=\"0\" alt=\"{$alts[$next_state]}\" />";
		}

		$html = "<a href=\"$base_href&field=$field&order=$next_state\">"
		. JText::_( $text )
		. '&nbsp;&nbsp;'
		. $img
		. "</a>";

		return $html;
	}

	/**
 	 * Legacy function, deprecated
 	 *
 	 * @deprecated	As of version 1.5
 	*/
	function CloseButton ( &$params, $hide_js=NULL )
	{

		// displays close button in Pop-up window
		if ( $params->get( 'popup' ) && !$hide_js ) {
			?>
			<div align="center" style="margin-top: 30px; margin-bottom: 30px;">
				<script type="text/javascript">
					document.write('<a href="#" onclick="javascript:window.close();"><span class="small"><?php echo JText::_( 'Close Window' );?></span></a>');
				</script>
				<?php
				if ( $_SERVER['HTTP_REFERER'] != "") {
					echo '<noscript>';
					echo '<a href="'. $_SERVER['HTTP_REFERER'] .'"><span class="small">'. JText::_( 'BACK' ) .'</span></a>';
					echo '</noscript>';
				}
				?>
			</div>
			<?php
		}
	}

	/**
 	 * Legacy function, deprecated
 	 *
 	 * @deprecated	As of version 1.5
 	*/
	function BackButton ( &$params, $hide_js=NULL )
	{

		// Back Button
		if ( $params->get( 'back_button' ) && !$params->get( 'popup' ) && !$hide_js) {
			?>
			<div class="back_button">
				<a href='javascript:history.go(-1)'>
					<?php echo JText::_( 'BACK' ); ?></a>
			</div>
			<?php
		}
	}

	/**
 	 * Legacy function, use {@link JFilterOutput::cleanText()} instead
 	 *
 	 * @deprecated	As of version 1.5
 	*/
	function cleanText ( &$text ) {
		return JFilterOutput::cleanText($text);
	}

	/**
 	 * Legacy function, deprecated
 	 *
 	 * @deprecated	As of version 1.5
 	*/
	function PrintIcon( &$row, &$params, $hide_js, $link, $status=NULL )
	{

		if ( $params->get( 'print' )  && !$hide_js ) {
			// use default settings if none declared
			if ( !$status ) {
				$status = 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no';
			}

			// checks template image directory for image, if non found default are loaded
			if ( $params->get( 'icons' ) ) {
				$image = mosAdminMenus::ImageCheck( 'printButton.png', '/images/M_images/', NULL, NULL, JText::_( 'Print' ), JText::_( 'Print' ) );
			} else {
				$image = JText::_( 'ICON_SEP' ) .'&nbsp;'. JText::_( 'Print' ) .'&nbsp;'. JText::_( 'ICON_SEP' );
			}

			if ( $params->get( 'popup' ) && !$hide_js ) {
				// Print Preview button - used when viewing page
				?>
				<script type="text/javascript">
					document.write('<td align="right" width="100%" class="buttonheading">');
					document.write('<a href="#" onclick="javascript:window.print(); return false" title="<?php echo JText::_( 'Print' );?>">');
					document.write('<?php echo $image;?>');
					document.write('</a>');
					document.write('</td>');
				</script>
				<?php
			} else {
				// Print Button - used in pop-up window
				?>
				<td align="right" width="100%" class="buttonheading">
				<a href="<?php echo $link; ?>" onclick="window.open('<?php echo $link; ?>','win2','<?php echo $status; ?>'); return false;" title="<?php echo JText::_( 'Print' );?>">
					<?php echo $image;?></a>
				</td>
				<?php
			}
		}
	}

	/**
 	 * Legacy function, use {@link JHTML::_('email.cloak')} instead
 	 *
 	 * @deprecated	As of version 1.5
 	*/
	function emailCloaking( $mail, $mailto=1, $text='', $email=1 )
	{
		return JHTML::_('email.cloak', $mail, $mailto, $text, $email);
	}

	/**
 	 * Legacy function, use {@link JHTML::_('behavior.keepalive')} instead
 	 *
 	 * @deprecated	As of version 1.5
 	*/
	function keepAlive()
	{
		echo JHTML::_('behavior.keepalive');
	}
}
}


// Register legacy classes for autoloading
JLoader::register('JInstaller'     , JPATH_LIBRARIES.DS.'joomla'.DS.'installer'.DS.'installer.php');

/**
 * Legacy class, use JInstaller instead
 * @deprecated	As of version 1.5
 * @package		Joomla.Legacy
 * @subpackage	1.5
 *
 */
if (!class_exists('mosInstaller')) {

if (!class_exists('JInstaller')) {
	JLoader::load('JInstaller');
}

class mosInstaller extends JInstaller
{
	function mosInstaller() {
		parent::__construct();
	}
	
	function __construct() {
		parent::__construct();
	}
}
}
// Register legacy classes for autoloading
JLoader::register('JApplication' , JPATH_LIBRARIES.DS.'joomla'.DS.'application'.DS.'application.php');

/**
 * Legacy class, derive from {@link JApplication} instead
 *
 * @deprecated	As of version 1.5
 * @package	Joomla.Legacy
 * @subpackage	1.5
 */
if (!class_exists('mosMainFrame')) {

if (!class_exists('JApplication')) {
	JLoader::load('JApplication');
}

class mosMainFrame extends JApplication
{
	/**
	 * Class constructor
	 * @param database A database connection object
	 * @param string The url option [DEPRECATED]
	 * @param string The path of the mos directory [DEPRECATED]
	 */	
	function __construct( &$db, $option, $basePath=null, $client=0 )
	{
		$config = array();
		$config['clientId'] = $client;
		parent::__construct( $config );
	}

	/**
	 * Class constructor
	 * @param database A database connection object
	 * @param string The url option [DEPRECATED]
	 * @param string The path of the mos directory [DEPRECATED]
	 */
	function mosMainFrame( &$db, $option, $basePath=null, $client=0 )
	{
		$config = array();
		$config['clientId'] = $client;
		parent::__construct( $config );
	}

	/**
	 * Initialises the user session
	 *
	 * Old sessions are flushed based on the configuration value for the cookie
	 * lifetime. If an existing session, then the last access time is updated.
	 * If a new session, a session id is generated and a record is created in
	 * the mos_sessions table.
	 */
	function initSession( )
	{

	}

	/**
	 * Gets the base path for the client
	 * @param mixed A client identifier
	 * @param boolean True (default) to add traling slash
	 */
	function getBasePath( $client=0, $addTrailingSlash=true )
	{
		switch ($client)
		{
			case '0':
			case 'site':
			case 'front':
			default:
				return mosPathName( JPATH_SITE, $addTrailingSlash );
				break;

			case '2':
			case 'installation':
				return mosPathName( JPATH_INSTALLATION, $addTrailingSlash );
				break;

			case '1':
			case 'admin':
			case 'administrator':
				return mosPathName( JPATH_ADMINISTRATOR, $addTrailingSlash );
				break;

		}
	}

	/**
	* Deprecated, use {@link JDocument::setTitle() JDocument->setTitle()} instead or override in your application class
	*
	* @since 1.5
	* @deprecated As of version 1.5
	*/
	function setPageTitle( $title=null )
	{
		$document=& JFactory::getDocument();
		$document->setTitle($title);
	}

	/**
	* Deprecated, use {@link JDocument::getTitle() JDocument->getTitle()} instead or override in your application class
	* @since 1.5
	* @deprecated As of version 1.5
	*/
	function getPageTitle()
	{
		$document=& JFactory::getDocument();
		return $document->getTitle();
	}
}
}

// Register legacy classes for autoloading
JLoader::register('JDispatcher' , JPATH_LIBRARIES.DS.'joomla'.DS.'event'.DS.'dispatcher.php');

/**
 * Legacy class, use {@link JDispatcher} instead
 *
 * @deprecated	As of version 1.5
 * @package	Joomla.Legacy
 * @subpackage	1.5
 */
if (!class_exists('mosMambotHandler')) {

if (!class_exists('JDispatcher')) {
	JLoader::load('JDispatcher');
}

class mosMambotHandler extends JDispatcher
{
	function mosMambotHandler()
	{
		parent::__construct();
	}
	
	function __construct()
	{
		parent::__construct();
	}

	/**
	* Loads all the bot files for a particular group
	* @param string The group name, relates to the sub-directory in the plugins directory
	*/
	function loadBotGroup( $group )
	{
		return JPluginHelper::importPlugin($group, null, false);
	}
	/**
	 * Loads the bot file
	 * @param string The folder (group)
	 * @param string The elements (name of file without extension)
	 * @param int Published state
	 * @param string The params for the bot
	 */
	function loadBot( $folder, $element, $published, $params='' )
	{
		return JPluginHelper::_import($folder, $element, $published, $params='' );
	}

	/**
	* Registers a function to a particular event group
	*
	* @param string The event name
	* @param string The function name
	*/
	function registerFunction( $event, $function )
	{
		 JApplication::registerEvent( $event, $function );
	}

	/**
	* Deprecated, use {@link JDispatcher::trigger() JDispatcher->trigger()} instead and handle return values
	* in your code
	*
	* @param string The event name
	* @since 1.5
	* @deprecated As of 1.5
	*/
	function call($event)
	{
		$args = & func_get_args();
		array_shift($args);

		$retArray = $this->trigger( $event, $args );
		return $retArray[0];
	}
}
}

// Register legacy classes for autoloading
JLoader::register('JTableMenu', JPATH_LIBRARIES.DS.'joomla'.DS.'database'.DS.'table'.DS.'menu.php');

/**
 * Legacy class, use {@link JTableMenu} instead
 *
 * @deprecated	As of version 1.5
 * @package	Joomla.Legacy
 * @subpackage	1.5
 */
if (!class_exists('mosMenu')) {

if (!class_exists('JTableMenu')) {
	JLoader::load('JTableMenu');
}

class mosMenu extends JTableMenu
{
	/**
	 * Constructor
	 */
	function __construct(&$db)
	{
		parent::__construct( $db );
	}

	function mosMenu(&$db)
	{
		parent::__construct( $db );
	}

	/**
	 * Legacy Method, use {@link JTable::reorder()} instead
	 * @deprecated As of 1.5
	 */
	function updateOrder( $where='' )
	{
		return $this->reorder( $where );
	}

	/**
	 * Legacy Method, use {@link JTable::publish()} instead
	 * @deprecated As of 1.0.3
	 */
	function publish_array( $cid=null, $publish=1, $user_id=0 )
	{
		$this->publish( $cid, $publish, $user_id );
	}
}
}

// Register legacy classes for autoloading
JLoader::register('JTableModule'   , JPATH_LIBRARIES.DS.'joomla'.DS.'database'.DS.'table'.DS.'module.php');

/**
 * Legacy class, use {@link JTableModule} instead
 *
 * @deprecated	As of version 1.5
 * @package	Joomla.Legacy
 * @subpackage	1.5
 */
if (!class_exists('mosModule')) {

if (!class_exists('JTableModule')) {
	JLoader::load('JTableModule');
}

class mosModule extends JTableModule
{
	/**
	 * Constructor
	 */
	function __construct(&$db)
	{
		parent::__construct( $db );
	}

	function mosModule(&$db)
	{
		parent::__construct( $db );
	}

	/**
	 * Legacy Method, use {@link JTable::reorder()} instead
	 * @deprecated As of 1.5
	 */
	function updateOrder( $where='' )
	{
		return $this->reorder( $where );
	}

	/**
	 * Legacy Method, use {@link JTable::publish()} instead
	 * @deprecated As of 1.0.3
	 */
	function publish_array( $cid=null, $publish=1, $user_id=0 )
	{
		$this->publish( $cid, $publish, $user_id );
	}
}
}



// Register legacy classes for autoloading
JLoader::register('JParameter' , JPATH_LIBRARIES.DS.'joomla'.DS.'html'.DS.'parameter.php');

/**
 * Legacy class, use {@link JParameter} instead
 *
 * @deprecated	As of version 1.5
 * @package	Joomla.Legacy
 * @subpackage	1.5
 */
if (!class_exists('mosParameters')) {

if (!class_exists('JParameter')) {
	JLoader::load('JParameter');
}

class mosParameters extends JParameter
{
	/**
	* @param string The raw parms text
	* @param string Path to the xml setup file
	* @param string The type of setup file
	*/
	function mosParameters($text, $path = '', $type = 'component')
	{
		parent::__construct($text, $path);
	}
	
	function __construct($text, $path = '', $type = 'component')
	{
		parent::__construct($text, $path);
	}

	/**
	 * Legacy function, use {@link JParameter::toObject() JParameter->toObject()} instead
	 *
	 * @deprecated As of version 1.5
	 */
	function toObject()
	{
		parent::toObject();
	}

	/**
	 * Legacy function, use {@link JParameter::toArray() JParameter->toArray()} instead
	 *
	 * @deprecated As of version 1.5
	 */
	function toArray()
	{
		parent::toArray();
	}

	/**
	 * Parse an .ini string, based on phpDocumentor phpDocumentor_parse_ini_file function
	 *
	 * @access public
	 * @param mixed The ini string or array of lines
	 * @param boolean add an associative index for each section [in brackets]
	 * @return object
	 */
	function parse($txt, $process_sections = false, $asArray = false)
	{
		$this->loadINI($txt);

		if($asArray) {
			return $this->toArray();
		}

		return $this->toObject( );
	}

	/**
	* Special handling for textarea param
	*/
	function textareaHandling( &$txt )
	{
		$total = count( $txt );
		for( $i=0; $i < $total; $i++ ) {
			if ( strstr( $txt[$i], "\n" ) ) {
				$txt[$i] = str_replace( "\n", '<br />', $txt[$i] );
			}
		}
		$txt = implode( "\n", $txt );

		return $txt;
	}
}
}

/**
 * Legacy class, use {@link JTemplate::getInstance()} instead
 *
 * @deprecated	As of version 1.5
 * @package	Joomla.Legacy
 * @subpackage	1.5
 */
if (!class_exists('patFactory')) {
class patFactory
{
	function &createTemplate( $option, $isAdmin=false, $useCache=false )
	{
		$bodyHtml='';
		$files=null;

		jimport('joomla.template.template');
		$tmpl = new JTemplate();

		// load the wrapper and common templates
		$tmpl->readTemplatesFromFile( 'page.html' );
		$tmpl->applyInputFilter('ShortModifiers');

		// load the stock templates
		if (is_array( $files )) {
			foreach ($files as $file)
			{
				$tmpl->readTemplatesFromInput( $file );
			}
		}

		// TODO: Do the protocol better
		$tmpl->addVar( 'form', 'formAction', basename($_SERVER['PHP_SELF']) );
		$tmpl->addVar( 'form', 'formName', 'adminForm' );

		$tmpl->setRoot( dirname( __FILE__ ) . '/tmpl');
		$tmpl->setNamespace( 'mos' );

		if ($bodyHtml) {
			$tmpl->setAttribute( 'body', 'src', $bodyHtml );
		}
		return $tmpl;
	}
}
}
// Register legacy classes for autoloading
JLoader::register('JProfiler', JPATH_LIBRARIES.DS.'joomla'.DS.'error'.DS.'profiler.php');


 /**
 * Legacy class, use {@link JProfiler} instead
 *
 * @deprecated	As of version 1.5
 * @package	Joomla.Legacy
 * @subpackage	1.5
 */
if (!class_exists('mosProfiler')) {

if (!class_exists('JProfiler')) {
	JLoader::load('JProfiler');
}

class mosProfiler extends JProfiler
{
	/**
	* @return object A function cache object
	*/
	function mosProfiler (  $prefix=''  )
	{
		parent::__construct($prefix);
	}
	
	function __construct (  $prefix=''  )
	{
		parent::__construct($prefix);
	}
	
	function JProfiler (  $prefix=''  )
	{
		parent::__construct($prefix);
	}
}
}


// Register legacy classes for autoloading
JLoader::register('JTableSession', JPATH_LIBRARIES.DS.'joomla'.DS.'database'.DS.'table'.DS.'session.php');

/**
 * Legacy class, use {@link JTableSession} instead
 *
 * @deprecated	As of version 1.5
 * @package	Joomla.Legacy
 * @subpackage	1.5
 */
if (!class_exists('mosSession')) {

if (!class_exists('JTableSession')) {
	JLoader::load('JTableSession');
}

class mosSession extends JTableSession
{
	/**
	 * Constructor
	 */
	function __construct(&$db)
	{
		parent::__construct(  $db );
	}

	function mosSession(&$db)
	{
		parent::__construct( $db );
	}

	/**
	 * Encodes a session id
	 */
	function hash( $value )
	{
		$JLMS_CONFIG = & JLMSFactory::getConfig();
		if (phpversion() <= '4.2.1') {
			$agent = getenv( 'HTTP_USER_AGENT' );
		} else {
			$agent = $_SERVER['HTTP_USER_AGENT'];
		}

		return md5( $agent . $JLMS_CONFIG->get('secret') . $value . $_SERVER['REMOTE_ADDR'] );
	}

	/**
	 * Set the information to allow a session to persist
	 */
	function persist()
	{
		$app = & JFactory::getApplication();		

		$usercookie = mosGetParam( $_COOKIE, 'usercookie', null );
		if ($usercookie) {
			// Remember me cookie exists. Login with usercookie info.
			$app->login( $usercookie['username'], $usercookie['password'] );
		}
	}

	/**
	 * Legacy Method, use {@link JTable::reorder()} instead
	 * @deprecated As of 1.5
	 */
	function updateOrder( $where='' )
	{
		return $this->reorder( $where );
	}

	/**
	 * Legacy Method, use {@link JTable::publish()} instead
	 * @deprecated As of 1.0.3
	 */
	function publish_array( $cid=null, $publish=1, $user_id=0 )
	{
		$this->publish( $cid, $publish, $user_id );
	}

	/**
	 * Legacy Method, use {@link JTable::publish()} instead
	 * @deprecated As of 1.5
	 */
	function setFromRequest( $key, $varName, $default=null )
	{
		if (isset( $_REQUEST[$varName] )) {
			return $_SESSION[$key] = $_REQUEST[$varName];
		} else if (isset( $_SESSION[$key] )) {
			return $_SESSION[$key];
		} else {
			return $_SESSION[$key] = $default;
		}
	}
}
}


/**
 * Legacy class
 *
 * @deprecated	As of version 1.5
 * @package		Joomla.Legacy
 * @subpackage	1.5
 */
if (!class_exists('mosToolBar')) {
class mosToolBar {

	/**
	* Writes the start of the button bar table
	*/
	function startTable()
	{
		// Initialize some variables
		$document = & JFactory::getDocument();

		// load toolbar css
		$document->addStyleSheet( 'templates/system/css/toolbar.css' );
		?>
		<table cellpadding="0" cellspacing="3" border="0" id="toolbar">
		<tr valign="middle" align="center">
		<?php
	}

	/**
	* Writes a custom option and task button for the button bar
	* @param string The task to perform (picked up by the switch($task) blocks
	* @param string The image to display
	* @param string The image to display when moused over
	* @param string The alt text for the icon image
	* @param boolean True if required to check that a standard list item is checked
	*/
	function custom( $task='', $icon=NULL, $iconOver='', $alt='', $listSelect=true ) {

		$icon 	= ( $iconOver ? $iconOver : $icon );
		$image 	= JHTML::_('image.site',  $icon, '/images/', NULL, NULL, $alt );

		if ($listSelect) {
			$message = JText::sprintf( 'Please make a selection from the list to', JText::_( $alt ) );
			$message = addslashes($message);
			$onclick = "javascript:if (document.adminForm.boxchecked.value == 0){ alert('".  $message . "');}else{submitbutton('$task')}";
		} else {
			$onclick = "javascript:submitbutton('$task')";
		}

		?>
		<td>
			<a class="toolbar" onclick="<?php echo $onclick ;?>">
				<?php echo $image; ?></a>
		</td>
		<?php
	}

	/**
	* Writes the common 'new' icon for the button bar
	* @param string An override for the task
	* @param string An override for the alt text
	*/
	function addNew( $task='new', $alt='New' ) {
		$alt= JText::_( $alt );

		mosToolBar::custom( $task, 'new_f2.png', '', $alt, false );
	}

	/**
	* Writes a common 'publish' button
	* @param string An override for the task
	* @param string An override for the alt text
	*/
	function publish( $task='publish', $alt='Published' ) {
 		$alt= JText::_( $alt );

		mosToolBar::custom( $task, 'publish_f2.png', '', $alt, false );
	}

	/**
	* Writes a common 'publish' button for a list of records
	* @param string An override for the task
	* @param string An override for the alt text
	*/
	function publishList( $task='publish', $alt='Published' ) {
		$alt= JText::_( $alt );

		mosToolBar::custom( $task, 'publish_f2.png', '', $alt, true );
	}

	/**
	* Writes a common 'unpublish' button
	* @param string An override for the task
	* @param string An override for the alt text
	*/
	function unpublish( $task='unpublish', $alt='Unpublished' ) {
		$alt= JText::_( $alt );

		mosToolBar::custom( $task, 'unpublish_f2.png', '', $alt, false );
	}

	/**
	* Writes a common 'unpublish' button for a list of records
	* @param string An override for the task
	* @param string An override for the alt text
	*/
	function unpublishList( $task='unpublish', $alt='Unpublished' ) {
		$alt= JText::_( $alt );

		mosToolBar::custom( $task, 'unpublish_f2.png', '', $alt, true );
	}

	/**
	* Writes a common 'archive' button for a list of records
	* @param string An override for the task
	* @param string An override for the alt text
	*/
	function archiveList( $task='archive', $alt='Archived' ) {
		$alt= JText::_( $alt );

		mosToolBar::custom( $task, 'archive_f2.png', '', $alt, true );
	}

	/**
	* Writes an unarchive button for a list of records
	* @param string An override for the task
	* @param string An override for the alt text
	*/
	function unarchiveList( $task='unarchive', $alt='Unarchive' ) {
		$alt= JText::_( $alt );

		mosToolBar::custom( $task, 'unarchive_f2.png', '', $alt, true );
	}

	/**
	* Writes a common 'edit' button for a list of records
	* @param string An override for the task
	* @param string An override for the alt text
	*/
	function editList( $task='edit', $alt='Edit' ) {
		$alt= JText::_( $alt );

		mosToolBar::custom( $task, 'edit_f2.png', '', $alt, true );
	}

	/**
	* Writes a common 'edit' button for a template html
	* @param string An override for the task
	* @param string An override for the alt text
	*/
	function editHtml( $task='edit_source', $alt='Edit HTML' ) {
		$alt= JText::_( $alt );

		mosToolBar::custom( $task, 'edit_f2.png', '', $alt, true );
	}

	/**
	* Writes a common 'edit' button for a template css
	* @param string An override for the task
	* @param string An override for the alt text
	*/
	function editCss( $task='edit_css', $alt='Edit CSS' ) {
		$alt= JText::_( $alt );

		mosToolBar::custom( $task, 'css_f2.png', '', $alt, true );
	}

	/**
	* Writes a common 'delete' button for a list of records
	* @param string  Postscript for the 'are you sure' message
	* @param string An override for the task
	* @param string An override for the alt text
	*/
	function deleteList( $msg='', $task='remove', $alt='Delete' ) {
		$alt= JText::_( $alt );

		mosToolBar::custom( $task, 'delete_f2.png', '', $alt, true );
	}

	/**
	* Writes a save button for a given option
	* @param string An override for the task
	* @param string An override for the alt text
	*/
	function save( $task='save', $alt='Save' ) {
		$alt= JText::_( $alt );

		mosToolBar::custom( $task, 'save_f2.png', '', $alt, false );
	}

	/**
	* Writes a save button for a given option
	* @param string An override for the task
	* @param string An override for the alt text
	*/
	function apply( $task='apply', $alt='Apply' ) {
		$alt= JText::_( $alt );

		mosToolBar::custom( $task, 'apply_f2.png', '', $alt, false );
	}

	/**
	* Writes a cancel button and invokes a cancel operation (eg a checkin)
	* @param string An override for the task
	* @param string An override for the alt text
	*/
	function cancel( $task='cancel', $alt='Cancel' ) {
		$alt= JText::_( $alt );

		mosToolBar::custom( $task, 'cancel_f2.png', '', $alt, false );
	}

	/**
	* Writes a preview button for a given option (opens a popup window)
	* @param string The name of the popup file (excluding the file extension)
	*/
	function preview( $popup='' ) {
		$db =& JFactory::getDBO();

		$sql = 'SELECT template'
		. ' FROM #__templates_menu'
		. ' WHERE client_id = 0'
		. ' AND menuid = 0';
		$db->setQuery( $sql );
		$cur_template = $db->loadResult();

		$alt	= JText::_( 'Preview' );
		$image 	= JHTML::_('image.site',  'preview_f2.png', 'images/', NULL, NULL, $alt );
		?>
		<td>
			<a class="toolbar" onclick="window.open('popups/<?php echo $popup;?>.php?t=<?php echo $cur_template; ?>', 'win1', 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no');" >
				<?php echo $image; ?></a>
		</td>
		<?php
	}

	/**
	* Writes a cancel button that will go back to the previous page without doing
	* any other operation
	*/
	function back() {
		$alt= JText::_( 'back' );
		$image = JHTML::_('image.site',  'back_f2.png', '/images/', NULL, NULL, $alt );
		?>
		<td>
			<a class="toolbar" href="javascript:window.history.back();" >
				<?php echo $image;?></a>
		</td>
		<?php
	}

	/**
	* Write a divider between menu buttons
	*/
	function divider() {
		$image = JHTML::_('image.site',  'menu_divider.png', '/images/' );
		?>
		<td>
			<?php echo $image; ?>
		</td>
		<?php
	}

	/**
	* Writes a media_manager button
	* @param string The sub-drectory to upload the media to
	*/
	function media_manager( $directory = '' ) {
		$alt= JText::_( 'Upload Image' );
		$image = JHTML::_('image.site',  'upload_f2.png', '/images/', NULL, NULL, $alt );
		?>
		<td>
			<a class="toolbar" onclick="popupWindow('popups/uploadimage.php?directory=<?php echo $directory; ?>','win1',250,100,'no');">
				<?php echo $image; ?></a>
		</td>
		<?php
	}

	/**
	* Writes a spacer cell
	* @param string The width for the cell
	*/
	function spacer( $width='' ) {
		if ($width != '') {
			?>
			<td width="<?php echo $width;?>">&nbsp;</td>
			<?php
		} else {
			?>
			<td>&nbsp;</td>
			<?php
		}
	}

	/**
	* Writes the end of the menu bar table
	*/
	function endTable() {
		?>
		</tr>
		</table>
		<?php
	}
} 
}


// Register legacy classes for autoloading
JLoader::register('JTableUser', JPATH_LIBRARIES.DS.'joomla'.DS.'database'.DS.'table'.DS.'user.php');

/**
 * Legacy class, use {@link JTableUser} instead
 *
 * @deprecated	As of version 1.5
 * @package	Joomla.Legacy
 * @subpackage	1.5
 */
if (!class_exists('mosUser')) {

if (!class_exists('JTableUser')) {
	JLoader::load('JTableUser');
}

class mosUser extends JTableUser
{
	/**
	 * Constructor
	 */
	function __construct(&$db)
	{
		parent::__construct( $db );
	}

	function mosUser(&$db)
	{
		parent::__construct( $db);
	}

	/**
	 * Legacy Method, use {@link JTable::reorder()} instead
	 * @deprecated As of 1.5
	 */
	function updateOrder( $where='' )
	{
		return $this->reorder( $where );
	}

	/**
	 * Legacy Method, use {@link JTable::publish()} instead
	 * @deprecated As of 1.0.3
	 */
	function publish_array( $cid=null, $publish=1, $user_id=0 )
	{
		$this->publish( $cid, $publish, $user_id );
	}

	/**
	 * Returns a complete user list
	 *
	 * @return array
	 * @deprecated As of 1.5
	 */
	function getUserList()
	{
		$this->_db->setQuery("SELECT username FROM #__users");
		return $this->_db->loadAssocList();
	}

	/**
	 * Gets the users from a group
	 *
	 * @param	string	The value for the group
	 * @param	string	The name for the group
	 * @param	string	If RECURSE, will drill into child groups
	 * @param	string	Ordering for the list
	 * @return	array
	 * @deprecated As of 1.5
	 */
	function getUserListFromGroup( $value, $name, $recurse='NO_RECURSE', $order='name' )
	{
		$acl =& JFactory::getACL();

		// Change back in
		$group_id = $acl->get_group_id( $value, $name, 'ARO');
		$objects = $acl->get_group_objects( $group_id, 'ARO', 'RECURSE');

		if (isset( $objects['users'] ))
		{
			$gWhere = '(id =' . implode( ' OR id =', $objects['users'] ) . ')';

			$query = 'SELECT id AS value, name AS text'
			. ' FROM #__users'
			. ' WHERE block = "0"'
			. ' AND ' . $gWhere
			. ' ORDER BY '. $order
			;
			$this->_db->setQuery( $query );
			$options = $this->_db->loadObjectList();
			return $options;
		} else {
			return array();
		}
	}
}
}

// surveyforce - define global $database object
if (file_exists(dirname(__FILE__).DS.'..'.DS.'..'.DS.'com_surveyforce'.DS.'surveyforce.php')) {
	
	if (!class_exists('database') && $config->getValue('config.dbtype', 'mysql') == 'mysql') {
	// Register legacy classes for autoloading
	JLoader::register('JDatabaseMySQL', JPATH_LIBRARIES.DS.'joomla'.DS.'database'.DS.'database'.DS.'mysql.php');
	
	if (!class_exists('JDatabaseMySQL')) {
		JLoader::load('JDatabaseMySQL');
	}
	
	class database extends JDatabaseMySQL
	{
		function database ($host='localhost', $user, $password, $database='', $prefix='', $offline = true)
		{
			$options        = array ( 'host' => $host, 'user' => $user, 'password' => $password, 'database' => $database, 'prefix' => $prefix );
			parent::__construct( $options );
		}
		
		function __construct ($host='localhost', $user, $password, $database='', $prefix='', $offline = true)
		{
			$options        = array ( 'host' => $host, 'user' => $user, 'password' => $password, 'database' => $database, 'prefix' => $prefix );
			parent::__construct( $options );
		}
	
		/**
		* This global function loads the first row of a query into an object
		*
		* If an object is passed to this function, the returned row is bound to the existing elements of <var>object</var>.
		* If <var>object</var> has a value of null, then all of the returned query fields returned in the object.
		*
		* @param object The address of variable
		*/
		function loadObject( &$object )
		{
			if ($object != null)
			{
				if (!($cur = $this->query())) {
					return false;
				}
	
				if ($array = mysql_fetch_assoc( $cur ))
				{
					mysql_free_result( $cur );
					mosBindArrayToObject( $array, $object, null, null, false );
					return true;
				} else {
					return false;
				}
	
			}
			else
			{
				$object = parent::loadObject();
				return $object;
			}
		}
	
		/**
		* Execute a batch query
		*
		* @abstract
		* @access public
		* @return mixed A database resource if successful, FALSE if not.
		*/
		function query_batch( $abort_on_error=true, $p_transaction_safe = false)
		{
			return parent::queryBatch( $abort_on_error, $p_transaction_safe);
		}
	}
	}
	
	
	
	/**
	 * Legacy class, use {@link JDatabase} instead
	 *
	 * @deprecated	As of version 1.5
	 * @package	Joomla.Legacy
	 * @subpackage	1.5
	 */
	if (!class_exists('database') && $config->getValue('config.dbtype', 'mysql') == 'mysqli') {
		// Register legacy classes for autoloading
		JLoader::register('JDatabaseMySQLi', JPATH_LIBRARIES.DS.'joomla'.DS.'database'.DS.'database'.DS.'mysqli.php');
		
		if (!class_exists('JDatabaseMySQLi')) {
			JLoader::load('JDatabaseMySQLi');
		}
		
		class database extends JDatabaseMySQLi
		{
			function database ($host='localhost', $user, $password, $database='', $prefix='', $offline = true)
			{
				$options        = array ( 'host' => $host, 'user' => $user, 'password' => $password, 'database' => $database, 'prefix' => $prefix );
				parent::__construct( $options );
			}
			function __construct ($host='localhost', $user, $password, $database='', $prefix='', $offline = true)
			{
				$options        = array ( 'host' => $host, 'user' => $user, 'password' => $password, 'database' => $database, 'prefix' => $prefix );
				parent::__construct( $options );
			}
		
			/**
			* This global function loads the first row of a query into an object
			*
			* If an object is passed to this function, the returned row is bound to the existing elements of <var>object</var>.
			* If <var>object</var> has a value of null, then all of the returned query fields returned in the object.
			*
			* @param object The address of variable
			*/
			function loadObject( &$object )
			{
				if ($object != null)
				{
					if (!($cur = $this->query())) {
						return false;
					}
		
					if ($array = mysqli_fetch_assoc( $cur ))
					{
						mysqli_free_result( $cur );
						mosBindArrayToObject( $array, $object, null, null, false );
						return true;
					} else {
						return false;
					}
		
				}
				else
				{
					$object = parent::loadObject();
					return $object;
				}
			}
		
			/**
			* Execute a batch query
			*
			* @abstract
			* @access public
			* @return mixed A database resource if successful, FALSE if not.
			*/
			function query_batch( $abort_on_error=true, $p_transaction_safe = false)
			{
				return parent::queryBatch( $abort_on_error, $p_transaction_safe);
			}
		}
	}
}
?>