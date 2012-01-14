<?php 

defined( '_JLMS_EXEC' ) or die( 'Restricted access' );


/* MAMBO 4.5.2.3 FIXES + EXTENSIONS: */

/**
*	Corrects bugs in mambo core class mosDBTable ! :
*	1) NULL values from SQL tables are not loaded !
*	2) updateOrder method is buggy and does not allow to specify modified row ids to force them into the right position !
*/
class JLMSDBTable extends mosDBTable {
	/**
	*	Object constructor to set table and key field
	*
	*	Can be overloaded/supplemented by the child class
	*	@param string $table name of the table in the db schema relating to child class
	*	@param string $key name of the primary key field in the table
	*/
	function JLMSDBTable( $table, $key, &$db ) {
		$this->mosDBTable($table, $key, $db);
	}

	/**
	*	binds an array/hash to this object
	*	@param int $oid optional argument, if not specifed then the value of current key is used
	*	@return any result from the database operation
	*/
	function load( $oid=null ) {
		$k = $this->_tbl_key;
		if ($oid !== null) {
			$this->$k = $oid;
		}
		$oid = $this->$k;
		if ($oid === null) {
			return false;
		}
		
		//BB fix : resets default values to all object variables, because NULL SQL fields do not overide existing variables !
		//Note: Prior to PHP 4.2.0, Uninitialized class variables will not be reported by get_class_vars().
		$class_vars = get_class_vars(get_class($this));
		foreach ($class_vars as $name => $value) {
			if (($name != $k) and ($name != "_db") and ($name != "_tbl") and ($name != "_tbl_key")) {
				$this->$name = $value;
			}
		}
		//end of BB fix.

		return parent::load( $oid );
	}
	/**
	* @param string $where This is expected to be a valid (and safe!) SQL expression
	*/
	function move( $dirn, $where = '', $ordering = 'ordering' ) {
		$k = $this->_tbl_key;

		$sql = "SELECT $this->_tbl_key, $ordering FROM $this->_tbl";

		if ($dirn < 0) {
			$sql .= "\n WHERE $ordering < " . (int) $this->$ordering;
			$sql .= ($where ? "\n	AND $where" : '');
			$sql .= "\n ORDER BY $ordering DESC";
			$sql .= "\n LIMIT 1";
		} else if ($dirn > 0) {
			$sql .= "\n WHERE $ordering > " . (int) $this->$ordering;
			$sql .= ($where ? "\n	AND $where" : '');
			$sql .= "\n ORDER BY $ordering";
			$sql .= "\n LIMIT 1";
		} else {
			$sql .= "\nWHERE $ordering = " . (int) $this->$ordering;
			$sql .= ($where ? "\n AND $where" : '');
			$sql .= "\n ORDER BY $ordering";
			$sql .= "\n LIMIT 1";
		}

		$this->_db->setQuery( $sql );

		$row = null;
		if ($this->_db->loadObject( $row )) {
			$query = "UPDATE $this->_tbl"
			. "\n SET $ordering = " . (int) $row->$ordering
			. "\n WHERE $this->_tbl_key = " . $this->_db->Quote( $this->$k )
			;
			$this->_db->setQuery( $query );

			if (!$this->_db->query()) {
				$err = $this->_db->getErrorMsg();
				die( $err );
			}

			$query = "UPDATE $this->_tbl"
			. "\n SET $ordering = " . (int) $this->$ordering
			. "\n WHERE $this->_tbl_key = " . $this->_db->Quote( $row->$k )
			;
			$this->_db->setQuery( $query );

			if (!$this->_db->query()) {
				$err = $this->_db->getErrorMsg();
				die( $err );
			}

			$this->$ordering = $row->$ordering;
		} else {
			$query = "UPDATE $this->_tbl"
			. "\n SET $ordering = " . (int) $this->$ordering
			. "\n WHERE $this->_tbl_key = " . $this->_db->Quote( $this->$k )
			;
			$this->_db->setQuery( $query );

			if (!$this->_db->query()) {
				$err = $this->_db->getErrorMsg();
				die( $err );
			}
		}
	}

	/** private utility method for updateOrder() back-called by usort() for comparing orderings
	*/
	function _cmp_obj($a, $b) {
		$k = $this->_cbc_cbc_ordering_tmp;
		if ($a->$k == $b->$k) {
           return 0;
       }
       return ($a->$k > $b->$k) ? +1 : -1;
   }
	/**
	* Compacts the ordering sequence of the selected records
	* @param string Additional where query to limit ordering to a particular subset of records
	* @param array of table key ids which should preserve their position (in addition of the negative positions) 
	*/
	function updateOrder( $where = '' , $cids = null, $ordering = 'ordering' ) {
		$k = $this->_tbl_key;

		if (!array_key_exists( $ordering, get_class_vars( strtolower(get_class( $this )) ) )) {
			$this->_error = "WARNING: ".strtolower(get_class( $this ))." does not support ordering field" . $ordering . ".";
			return false;
		}

		if ($this->_tbl == "#__content_frontpage") {
			$order2 = ", content_id DESC";
		} else {
			$order2 = "";
		}

		$this->_db->setQuery( "SELECT $this->_tbl_key, $ordering FROM $this->_tbl"
		. ($where ? "\nWHERE $where" : '')
		. "\nORDER BY " . $ordering . $order2
		);
		if (!($orders = $this->_db->loadObjectList())) {
			$this->_error = $this->_db->getErrorMsg();
			return false;
		}

		$n=count( $orders );
		$iOfThis = null;
		
		if($cids !== null) {
			$cidsOrderings = array();			// determine list of reserved/changed ordering numbers
			for ($i=0; $i < $n; $i++) {
				if (in_array($orders[$i]->$k, $cids)) {
					$cidsOrderings[$orders[$i]->$k] = $orders[$i]->$ordering;
				}
			}

			$j = 1;								// change ordering numbers outside of reserved and negative ordering numbers list
			for ($i=0; $i < $n; $i++) {
				if ($orders[$i]->$k == $this->$k) {
					// place 'this' record in the desired location at the end !
					$iOfThis = $i;
					if ($orders[$i]->$ordering == $j) $j++;
				} else if (in_array($orders[$i]->$k, $cids)) {
					if ($orders[$i]->$ordering == $j) $j++;
				} else {
					if ($orders[$i]->$ordering >= 0) $orders[$i]->$ordering = $j++;
					while (in_array($orders[$i]->$ordering, $cidsOrderings)) $orders[$i]->$ordering = $j++;
				}
			}
		} else {
			$j = 1;
			for ($i=0; $i < $n; $i++) {
				if ($orders[$i]->$k == $this->$k) {
					// place 'this' record in the desired location at the end !
					$iOfThis = $i;
					if ($orders[$i]->$ordering == $j) $j++;
				} else if ($orders[$i]->$ordering != $this->$ordering && $this->$ordering > 0 && $orders[$i]->$ordering >= 0) {
					$orders[$i]->$ordering = $j++;
				} else if ($orders[$i]->$ordering == $this->$ordering && $this->$ordering > 0 && $orders[$i]->$ordering >= 0) {
					if ($orders[$i]->$ordering == $j) $j++;
					$orders[$i]->$ordering = $j++;
				}
			}
		}
		if ($iOfThis !== null) {
			$orders[$iOfThis]->$ordering = min( $this->$ordering, $j );
		}
		// sort entries by ->$ordering:
		$this->_cbc_cbc_ordering_tmp	=	$ordering;
		usort($orders, array("comprofilerDBTable", "_cmp_obj"));
		unset( $this->_cbc_cbc_ordering_tmp );

		// compact ordering:
		$j = 1;
		for ($i=0; $i < $n; $i++) {
			if ($orders[$i]->$ordering >= 0) {
				$orders[$i]->$ordering = $j++;
			}
		}

		for ($i=0; $i < $n; $i++) {
			if (($orders[$i]->$ordering >= 0) or ($orders[$i]->$k == $this->$k)) {
				$this->_db->setQuery( "UPDATE $this->_tbl"
				. "\nSET $ordering='".$orders[$i]->$ordering."' WHERE $k='".$orders[$i]->$k."'"
				);
				$this->_db->query();
			}
		}

		// if we didn't find to reorder the current record, make it last
		if (($iOfThis === null) && ($this->$ordering > 0)) {
			$order = $n+1;
			$this->_db->setQuery( "UPDATE $this->_tbl"
			. "\nSET $ordering='$order' WHERE $k='".$this->$k."'"
			);
			$this->_db->query();
		}
		return true;
	}

	// MISSING FROM EARLY VERSIONS:
	/**
	* Resets public properties
	* @param mixed The value to set all properties to, default is null
	*/
	function reset( $value=null ) {
		$keys = $this->getPublicProperties();
		foreach ($keys as $k) {
			$this->$k = $value;
		}
	}
	/**
	* Tests if item is checked out
	* @param  int      A user id
	* @return boolean
	 */
	function isCheckedOut( $user_id = 0 ) {
		if ( $user_id ) {
			return ( $this->checked_out && ( $this->checked_out != $user_id ) );
		} else {
			return $this->checked_out;
		}
	}
	// EXTENSIONS: EXPERIMENTAL IN CB 1.1, NOT PART OF API:
	/**
	* Loads an array of typed objects of a given class (same class as current object by default)
	*
	* @param  string $class [optional] class name
	* @param  string $key [optional] key name in db to use as key of array
	* @param  array  $additionalVars [optional] array of string additional key names to add as vars to object
	* @return array  of objects of the same class (empty array if no objects)
	*/
	function & loadTrueObjects( $class=null, $key="", $additionalVars=array() ) {
		$objectsArray = array();
		$resultsArray = $this->_db->loadAssocList( $key );
		if ( is_array($resultsArray) ) {
			if ( $class == null ) {
				$class = get_class($this);
			}
			foreach ( $resultsArray as $k => $value ) {
				$objectsArray[$k] =& new $class( $this->_db );
				mosBindArrayToObject( $value, $objectsArray[$k], null, null, false );
				foreach ( $additionalVars as $index ) {
					if ( array_key_exists( $index, $value ) ) {
						$objectsArray[$k]->$index = $value[$index];
					}
				}
			}
		}
		return $objectsArray;
	}
}	// end class JLMSDBTable
?>