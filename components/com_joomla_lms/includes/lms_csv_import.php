<?php
/**
* /includes/lms_csv_import.php
* Joomla LMS Component
* * * ElearningForce DK
**/

// no direct access
defined( '_JLMS_EXEC' ) or die( 'Restricted access' );

//Classes for CSV import
class DeImportFieldDescriptor {
	var $name			= '';
	var $required		= FALSE;
	var $defaultValue	= NULL;
	
	function DeImportFieldDescriptor($name, $required = FALSE, $defaultValue = NULL) {
		$this->name				= $name;
		$this->required			= $required;
		$this->defaultValue		= $defaultValue;
	}
	
	function getName() {
		return $this->name;
	}
	
	function isRequired() {
		return $this->required;
	}
	
	function getDefaultValue() {
		return $this->defaultValue;
	}
}

class DeImportFieldDescriptors {
	var $fieldDescriptorsByName		= array();
	
	function addRequired($name) {
		$this->fieldDescriptorsByName[$name]	= new DeImportFieldDescriptor($name, TRUE);
	}
	
	function addOptional($name, $defaultValue = NULL) {
		$this->fieldDescriptorsByName[$name]	= new DeImportFieldDescriptor($name, FALSE, $defaultValue);
	}
	
	function get($name) {
		$result	= NULL;
		if (isset($this->fieldDescriptorsByName[$name])) {
			$result	= $this->fieldDescriptorsByName[$name];
		}
		return $result;
	}
	
	function getFieldNames() {
		$a		= array();
		foreach(array_keys($this->fieldDescriptorsByName) as $fieldName) {
			$a[]	= $fieldName;
		}
		return $a;
	}
	
	function getRequiredFieldNames() {
		$a		= array();
		foreach(array_keys($this->fieldDescriptorsByName) as $fieldName) {
			$fieldDescriptor	= $this->fieldDescriptorsByName[$fieldName];
			if ($fieldDescriptor->isRequired()) {
				$a[]	= $fieldName;
			}
		}
		return $a;
	}
	
	function contains($name) {
		return isset($this->fieldDescriptorsByName[$name]);
	}
	
	function isRequired($name) {
		$fieldDescriptor	= $this->get($name);
		return ($fieldDescriptor != NULL ? $fieldDescriptor->isRequired() : FALSE);
	}
	
	function getDefaultValue($name) {
		$fieldDescriptor	= $this->get($name);
		return ($fieldDescriptor != NULL ? $fieldDescriptor->getDefaultValue() : FALSE);
	}
	
}

class DeCsvLoader {
	
	var $fileName;
	var $delimiter		= ',';
	var $loaded			= FALSE;
	var $fieldNames		= array();
	var $rows			= array();
	var $rowIndex		= 0;
	var $errorMessage	= '';
	var $flag 			= 0;
	
	function setFileName($fileName) {
		$this->fileName	= $fileName;
	}
	
	function resetError() {
		$this->setErrorMessage('');
	}
	
	function setDelimiter($delimiter) {
		$this->delimiter	= $delimiter;
	}
	
	function getDelimiter() {
		return $this->delimiter;
	}
	
	function setErrorMessage($errorMessage) {
		$this->errorMessage		= $errorMessage;
	}
	
	function getErrorMessage() {
		return $this->errorMessage;
	}
	
	function load() {
		$this->resetError();
		$this->rowIndex		= 0;
		$this->rows			= array();
		$this->fieldNames	= array();
		$this->loaded		= FALSE;
		if ($this->fileName == '') {
			$this->setErrorMessage("FileName missing or file doesn\'t exists");
			return FALSE;
		}
		$this->rows		= JLMS_file($this->fileName);//file($this->fileName);
		if ($this->rows === FALSE) {
			$this->rows	= array();
			$this->setErrorMessage("Unable to read CSV file or file doesn\'t exists");
			return FALSE;
		}
		if (count($this->rows) < 1) {
			$this->setErrorMessage('CSV Header information missing');
			return FALSE;
		}
		$this->fieldNames	= $this->getNextValues(FALSE);
		if ($this->fieldNames === FALSE) {
			$this->fieldNames	= array();
			return FALSE;
		}
		$this->loaded	= TRUE;
		return TRUE;
	}
	
	function isEof() {
		return ($this->rowIndex >= count($this->rows));
	}
	
	function getNextRow() {
		if ($this->isEof()) {
			$this->setErrorMessage('End of file reached');
			return FALSE;
		}
		return rtrim($this->rows[$this->rowIndex++]);
	}
	
	function getNextValues($fieldNameKeys = TRUE) {
		$row	= $this->getNextRow();
		if ($row === FALSE) {
			return FALSE;
		}
		$a	= explode($this->delimiter, $row);
		if (($fieldNameKeys) && (count($this->fieldNames) > 0)) {
			$a2		= array();
			foreach($this->fieldNames as $k => $fieldName) {
				if (isset($a[$k])) {
					$a2[$fieldName]		= trim($a[$k]);
				}
			}
			return $a2;
		} else {
			return $a;
		}
	}
	
	function getLastLineNumber() {
		return $this->rowIndex;
	}
	
	function getFieldNames() {
		return $this->fieldNames;
	}
	
	function setFieldNames($fieldNames) {
		$this->fieldNames	= $fieldNames;
	}
} 

//for CSV import
function JLMS_file($filename){
	if (!file_exists($filename)) {
		return false;
	}
	if (!filesize($filename)) {
		return false;
	}
	$fp = fopen($filename, "rb");
	$buffer = fread($fp, filesize($filename));
	fclose($fp);
	$lines = preg_split("/\r?\n|\r/", $buffer);
	return $lines;
}

//for CSV import
function JLMS_prepareImport(&$loader, &$fieldDescriptors, $allow_unknown = false, $allow_missing = false) {
	
	$unknownFieldNames	= array();
	$missingFieldNames	= array();
	$requiredFieldNames	= $fieldDescriptors->getRequiredFieldNames();
	$fieldNames	= $loader->getFieldNames();
	foreach($fieldNames as $k => $fieldName) {
		$fieldName			= strtolower(trim($fieldName));
		$fieldNames[$k]		= $fieldName;
		if (!$fieldDescriptors->contains($fieldName)) {
			$unknownFieldNames[]	= $fieldName;
		}
	}
	$loader->setFieldNames($fieldNames);	// set the "normalized" field names
	foreach($requiredFieldNames as $fieldName) {
		if (!in_array($fieldName, $fieldNames)) {
			$missingFieldNames[]	= $fieldName;
		}
	}
	if (!$allow_unknown) {
		if (count($unknownFieldNames) > 0) {
			return false;
		}
	}
	if (!$allow_missing) {
		if (count($missingFieldNames) > 0) {
			
			$fields = implode(',', $missingFieldNames);
			
			if(count($missingFieldNames) > 1) {
				$loader->setErrorMessage('Header columns "'.$fields.'" are missing');
			}	
			else {
				$loader->setErrorMessage('Header column "'.$fields.'" is missing');
			}
					
			return false;
		}
	}
	#if ((count($unknownFieldNames) > 0) || (count($missingFieldNames) > 0)) {
		/*$this->setErrorMsg('import failed:'.
			(count($unknownFieldNames) > 0 ? ' the field(s) "'.implode(', ', $unknownFieldNames).'" is/are unknown' : '').
			(count($missingFieldNames) > 0 ? ' the required field(s) "'.implode(', ', $missingFieldNames).'" is/are missing' : ''));
		*/
		#return FALSE;
	#}
	return TRUE;
}

function JLMS_prepareImportRow(&$loader, &$fieldDescriptors, &$values, $requiredFieldNames, $allFieldNames, $allow_unknown = false, $allow_missing = false) {
	$unknownFieldNames	= array();
	$missingFieldNames	= array();
	foreach($requiredFieldNames as $fieldName) {
		if ((!isset($values[$fieldName])) || (trim($values[$fieldName]) == '')) {
			$missingFieldNames[]	= $fieldName;
		}
	}
	if (!$allow_unknown) {
		if (count($unknownFieldNames) > 0) {
			return false;
		}
	}
	if (!$allow_missing) {
		if (count($missingFieldNames) > 0) {
			return false;
		}
	}
	#if ((count($unknownFieldNames) > 0) || (count($missingFieldNames) > 0)) {
		/*$this->setErrorMsg('import failed('.$loader->getLastLineNumber().'):'.
			(count($unknownFieldNames) > 0 ? ' the field(s) "'.implode(', ', $unknownFieldNames).'" is/are unknown' : '').
			(count($missingFieldNames) > 0 ? ' the required field(s) "'.implode(', ', $missingFieldNames).'" is/are missing' : ''));
		*/
		#return FALSE;
	#}
	foreach($allFieldNames as $fieldName) {
	//23.10.2006 (DEN) "if (!isset($values[$fieldName])) {"   ==>   "if (empty($values[$fieldName])) {"
		if (empty($values[$fieldName])) {
			$defaultValue		= $fieldDescriptors->getDefaultValue($fieldName);
			if ($defaultValue != '') {
				$values[$fieldName]	= $defaultValue;
			}
		}
	}
	return TRUE;
}
?>