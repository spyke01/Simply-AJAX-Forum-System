<?php
/***************************************************************************
 *							   DB.php
 *							-------------------
 *   begin				: Saturday, Sept 24, 2005
 *   copyright			: (C) 2005 Paden Clayton - Fast Track Sites
 *   email				: sales@fasttacksites.com
 *
 *
 ***************************************************************************/

/***************************************************************************
Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions are met:
    * Redistributions of source code must retain the above copyright
      notice, this list of conditions and the following disclaimer.
    * Redistributions in binary form must reproduce the above copyright
      notice, this list of conditions and the following disclaimer in the
      documentation and/or other materials provided with the distribution.
    * Neither the name of the <organization> nor the
      names of its contributors may be used to endorse or promote products
      derived from this software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
DISCLAIMED. IN NO EVENT SHALL <COPYRIGHT HOLDER> BE LIABLE FOR ANY
DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
(INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 ***************************************************************************/
 
class DB {
	// DB connection variables
	var $server				= "";
	var $dbuser				= "";
	var $dbpass				= "";
	var $dbname				= "";
	var $DBTABLEPREFIX		= "";
	var $USERSDBTABLEPREFIX	= "";
	
	// Error Variables
	var $error = "";
	var $errno = 0;
	
	// Query variables
	var $numnRows = 0;	
	var $DBLink = 0;
	var $queryID = 0;
	
	//===============================================================
	// Our class constructor
	//===============================================================
	function DB($server, $dbuser, $dbpass, $dbname) {
		$this->server = $server;
		$this->dbuser = $dbuser;
		$this->dbpass = $dbpass;
		$this->dbname = $dbname;
		$this->DBTABLEPREFIX = DBTABLEPREFIX;
		$this->USERSDBTABLEPREFIX = USERSDBTABLEPREFIX;
		
		// Connect to the database
		$this->connect();
	}
	
	//===============================================================
	// Connect to the database
	//===============================================================
	function connect() {
		$this->DBLink = @mysql_connect($this->server, $this->dbuser, $this->dbpass);
	
		if (!$this->DBLink) {
			$this->returnError("Failed to open DB connection: <strong>" . $this->server . "</strong>.");
		}
	
		if(!@mysql_select_db($this->dbname)) {
			$this->returnError("Failed to select database: <strong>" . $this->dbname . "</strong>.");
		}
	}
	
	//===============================================================
	// Close our connection to the database
	//===============================================================
	function close() {
		if(!@mysql_close($this->DBLink)){
			$this->returnError("Failed to close DB connection.");
		}
	}
	
	//===============================================================
	// Function used to sanitize our input
	//===============================================================
	function escape($string) {
		// Stripslashes
	    if (get_magic_quotes_gpc()) {
	        $string = stripslashes($string);
	    }
		
	    // Quote if not integer
	    if (!is_numeric($makesafe)) {
	        $string = mysql_real_escape_string($string, $this->DBLink);
	    }
		
		return $string;
	}
	
	//===============================================================
	// Implementation of mysql_query()
	//===============================================================
	function query($sql) {
		// do query
		$this->queryID = mysql_query($sql, $this->DBLink);
	
		if (!$this->queryID) {
			$this->returnError("<strong>MySQL Query fail:</strong>" . $sql);
			return 0;
		}
		
		$this->numnRows = @mysql_num_rows($this->queryID);
	
		return $this->queryID;
	}
	
	//===============================================================
	// Implementation of mysql_num_rows()
	//===============================================================
	function num_rows() {
		return $this->numnRows;
	}
		
	//===============================================================
	// Implementation of mysql_fetch_assoc()
	//===============================================================
	function fetch_array($queryID = -1) {
		if ($queryID != -1)  $this->queryID = $queryID;
	
		// Make sure we have an query to pull data from
		if (isset($this->queryID)) {
			$record = @mysql_fetch_assoc($this->queryID);
		}
		else {
			$this->returnError("Invalid queryID: <strong>" . $this->queryID . "</strong>. Records could not be fetched.");
		}
	
		return $record;
	}	
	
	//===============================================================
	// Implementation of mysql_fetch_assoc()
	//===============================================================
	function fetch_all_array($sql) {
		$queryID = $this->query($sql);
		$out = array();
	
		// Loop through the results, and then store them for output
		while ($row = $this->fetch_array($queryID, $sql)){
			$out[] = $row;
		}
	
		// Free our result
		$this->free_result($queryID);
		
		// Aeturn our array of arrays
		return $out;
	}
		
	//===============================================================
	// Implementation of mysql_free_result()
	//===============================================================
	function free_result($queryID = -1) {
		if ($queryID != -1)  $this->queryID = $queryID;
		
		// Try and free our DB result
		if($this->queryID != 0 && !@mysql_free_result($this->queryID)) {
			$this->returnError("Result ID: <strong>" . $this->queryID . "</strong> could not be freed.");
		}
	}
	
	//===============================================================
	// Return just the first returned row of a query
	//===============================================================
	function query_first($query_string) {
		$queryID = $this->query($query_string);
		$out = $this->fetch_array($queryID);
		$this->free_result($queryID);
		
		return $out;
	}	
	
	//===============================================================
	// Craft an UPDATE statement and sanitize it
	//===============================================================
	function query_update($table, $data, $where = "1") {
		$table = ($table == "users") ? $this->USERSDBTABLEPREFIX . $table : $this->DBTABLEPREFIX . $table;
		$sql = "UPDATE `" . $table . "` SET ";
	
		foreach($data as $key => $value) {
			if(strtolower($value) == 'null') $sql .= "`" . $key . "` = NULL, ";
			elseif(strtolower($value) == 'now()') $sql .= "`" . $key . "` = NOW(), ";
			else $sql .= "`" . $key . "`='" . $this->escape($value) . "', ";
		}
	
		$sql = rtrim($sql, ', ') . ' WHERE ' . $where . ';';
	
		return $this->query($sql);
	}	
	
	//===============================================================
	// Craft an INSERT statement and sanitize it
	//===============================================================
	function query_insert($table, $data) {
		$table = ($table == "users") ? $this->USERSDBTABLEPREFIX . $table : $this->DBTABLEPREFIX . $table;
		$sql = "INSERT INTO `" . $table . "` ";
		$values = $names = "";
	
		foreach($data as $key => $value) {
			$names .= "`" . $key . "`, ";
			if(strtolower($value) == 'null') $values .="NULL, ";
			elseif(strtolower($value) == 'now()') $values .="NOW(), ";
			else $values .= "'" . $this->escape($value) . "', ";
		}
	
		$sql .= "(". rtrim($names, ', ') .") VALUES (". rtrim($values, ', ') .");";
	
		if($this->query($sql)) {
			//$this->free_result();
			return mysql_insert_id($this->DBLink);
		}
		else return false;
	}	
	
	//===============================================================
	// Allows us to handle errors
	//===============================================================
	function returnError($msg = "") {
		if($this->DBLink > 0) {
			$this->error = mysql_error($this->DBLink);
			$this->errno = mysql_errno($this->DBLink);
		}
		else {
			$this->error = mysql_error();
			$this->errno = mysql_errno();
		}
		
		echo $this->errno . "<br /><br />" . $this->error . "<br /><br />" . $msg;
	}
}

?>