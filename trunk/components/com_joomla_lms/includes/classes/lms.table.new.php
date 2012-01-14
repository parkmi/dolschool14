<?php 

defined( '_JEXEC' ) or die( 'Restricted access' );


class JLMSDBTable extends JTable
{
	var $_conds	=	null;
	
	/**
	 * Constructor
	 */
	function __construct($table, $key, &$db)
	{
		parent::__construct( $table, $key, $db );
	}

	function JLMSDBTable($table, $key, &$db)
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

		jimport('joomla.filter.input');
		$filter = & JFilterInput::getInstance();
		foreach ($this->getPublicProperties() as $k)
		{
			if ($ignore && in_array( $k, $ignoreList ) ) {
				continue;
			}
			$this->$k = $filter->clean( $this->$k );
		}
	}
	
	function load( $oid=null )
	{
		$k = $this->_tbl_key;

		if ($oid !== null) {
			$this->$k = $oid;
		}

		$oid = $this->$k;

		if ($oid === null) {
			return false;
		}
		$this->reset();

		$db =& $this->getDBO();
		
		$conds = ''; 
		if( isset($this->_conds[0]) ) 
		{						
			$conds = ' ';
			$conds .= implode( 'AND ', $this->_conds );
			$conds .= ' ';
		}		

		$query = 'SELECT *'
		. ' FROM '.$this->_tbl
		. ' WHERE '.$this->_tbl_key.' = '.$db->Quote($oid)
		. $conds;
		$db->setQuery( $query );		

		if ($result = $db->loadAssoc( )) {
			return $this->bind($result);
		}
		else
		{
			$this->setError( $db->getErrorMsg() );
			return false;
		}
	} 
	
	function addCond( $cond ) 
	{		
		$this->_conds[] = $cond;
	}
}
?>