<?php 

defined( '_JLMS_EXEC' ) or die( 'Restricted access' );

// this class is never used!!!!!

class JLMSdatabase extends JDatabaseMySQL
{
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
?>