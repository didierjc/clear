<?php
	class SQLQuery {
    	protected $_dbHandle;
    	protected $_result;
		
		private $queryID = 0;		// Result of most recent mysql_query().
		private $qryRow;			// current row number.
		private $qryRecord = array();	// current mysql_fetch_array()-result.

 		/** Connects to database **/
		function connect($host, $username, $pwd, $name) {
			$this->_dbHandle = mysql_connect($host, $username, $pwd); // connect to DB
			if($this->_dbHandle){
				if (mysql_select_db($name, $this->_dbHandle)){ // select database
					return 1;
				}else{
					return 0;
				}
			}else{
				return 0;
			}
		}

		/** Disconnects from database **/
		function disconnect() {
			if(mysql_close($this->_dbHandle)) {
				return 1;
			}else{
				return 0;
			}
		}

		function selectAll() {
			$query = 'SELECT * FROM '.$this->_table.;
			return $this->query($query);
		}

		function select($id) {
			$query = 'SELECT * FROM '.$this->_table.' WHERE id = \''.mysql_real_escape_string($id).'\'';
			return $this->query($query, 1);
		}

		/** Custom SQL Query **/
		function query($query, $singleResult = 0) {
			$this->_result = mysql_query($query, $this->_dbHandle);
			$this->qryRow = 0;

			if (preg_match("/select/i",$query)) {
				$result = array();
				$table = array();
				$field = array();
				$tempResults = array();
				$numOfFields = mysql_num_fields($this->_result);

				for ($i = 0; $i < $numOfFields; ++$i) {
					array_push($table,mysql_field_table($this->_result, $i));
					array_push($field,mysql_field_name($this->_result, $i));
				}

				while ($row = mysql_fetch_row($this->_result)) {
					for ($i = 0;$i < $numOfFields; ++$i) {
						$table[$i] = trim(ucfirst($table[$i]),"s");
						$tempResults[$table[$i]][$field[$i]] = $row[$i];
					}
					if ($singleResult == 1) {
		 				mysql_free_result($this->_result);
						return $tempResults;
					}
					array_push($result,$tempResults);
				}
				mysql_free_result($this->_result);
				return($result);
			}
		}

		/** Get number of rows **/
		function getNumRows() {
			return mysql_num_rows($this->_result);
		}

		/** Free resources allocated by a query **/
		function freeResult() {
			mysql_free_result($this->_result);
		}

		/** Get error string **/
		function getError() {
			return mysql_error($this->_dbHandle);
		}

		//------------------------------------------- 
		//    Retrieves the next record in a recordset 
		//------------------------------------------- 
		function nextRecord(){
			$this->qryRecord = mysql_fetch_array($this->_result); 
			$this->Row += 1;

			$stat = is_array($this->qryRecord);
			if(!$stat){
				mysql_free_result($this->_result);
				$this->_result = 0; 
			} 

			return $stat; 
		} 
	}