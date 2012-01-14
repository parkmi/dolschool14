<?php 
/**
* includes/classes/lms.user_agent.php
* (c) JoomaLMS eLearning Software http://www.joomlalms.com/
**/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.access.access');

class JLMSAccess extends JAccess 
{
	function get_group_children_tree( $root_id=null, $root_name=null, $inclusive=true, $html=true )
	{	
		$db = & JFactory::getDBO();
			
		if( $root_name == 'Public Backend' ) 
		{
			$where1 = ' WHERE a.title IN (\'Manager\', \'Super Users\')';	
		} else if( $root_name == 'USERS' ) 
		{
			$where1 = '';	
		} else {
			$where1 = ' WHERE a.title = '.$db->quote( $root_name );
		}
		
		$db = JFactory::getDbo();
		
		$db->setQuery(
			'SELECT a.id ' .
			' FROM #__usergroups AS a'	
			.$where1.					
			' GROUP BY a.id' 			
		);
		
		$root = $db->loadResultArray();		
						
		$rootStr = implode(',', $root);
					
		$db->setQuery(
			'SELECT a.*, COUNT(DISTINCT b.id) AS level, a.title AS name' .
			' FROM #__usergroups AS a' .
			' LEFT JOIN `#__usergroups` AS b ON a.lft > b.lft AND a.rgt < b.rgt' .
			' WHERE a.parent_id IN ('.$rootStr.') OR a.id IN ('.$rootStr.')' .
			' GROUP BY a.id' .
			' ORDER BY a.lft ASC'
		);
		
		$tree = $db->loadObjectList();		
						
		// first pass get level limits
		$n = count( $tree );
		$min = $tree[0]->level;
		$max = $tree[0]->level;
		for ($i=0; $i < $n; $i++) {
			$min = min( $min, $tree[$i]->level );
			$max = max( $max, $tree[$i]->level );
		}

		$indents = array();
		foreach (range( $min, $max ) as $i) {
			$indents[$i] = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
		}
		// correction for first indent
		$indents[$min] = '';

		$list = array();
		for ($i=$n-1; $i >= 0; $i--) {
			$shim = '';
			foreach (range( $min, $tree[$i]->level ) as $j) {
				$shim .= $indents[$j];
			}

			if (@$indents[$tree[$i]->level+1] == '.&nbsp;') {
				$twist = '&nbsp;';
			} else {
				$twist = "-&nbsp;";
			}
			$groupName = JText::_( $tree[$i]->name );
			//$list[$i] = $tree[$i]->level.$shim.$twist.$tree[$i]->name;
			if ($html) {
				$list[$i] = JHTML::_('select.option',  $tree[$i]->id, $shim.$twist.$groupName );
			} else {
				$list[$i] = array( 'value'=>$tree[$i]->id, 'text'=>$shim.$twist.$groupName );
			}
			if ($tree[$i]->level < @$tree[$i-1]->level) {
				$indents[$tree[$i]->level+1] = '.&nbsp;';
			}
		}

		ksort($list);
		return $list;
	}
	
	function is_group_child_of( $grp_src, $grp_tgt )
	{
		$db =& JFactory::getDBO();		
		
		$db->setQuery(
			'SELECT id, parent_id ' .
			' FROM #__usergroups AS a'.							
			' GROUP BY a.id' 			
		);
		
		$groups = $db->loadObjectList('id');		
				
		if ( !isset($groups[$grp_src]) || !$groups[$grp_src]->parent_id )
			return false;
		
		$parentId = $grp_src;
				
		while( $groups[$parentId]->parent_id != $grp_tgt ) 
		{	
			$parentId = $groups[$parentId]->parent_id;
					
			if( !$parentId )
				return false;			 
		}				
		
		return true;
	}
	
	function get_group_id( $name ) 
	{
		$db =& JFactory::getDBO();
		
		$db->setQuery(
			'SELECT id' .
			' FROM #__usergroups AS a'.
			' WHERE title = '.$db->quote( $name ).							
			' LIMIT 1' 			
		);
		
		$res = $db->loadResultArray();
		
		return $res;		
	}
	
}
?>