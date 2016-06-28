<?php
// ======================================================================================================================================================
	//  Author:     				Gustavo Arcila (tavoarcila at gmail dot com)
	//	Web: 	    				http://www.gurusistemas.com
	//	Name: 	    				phpMyDBCompare.inc.php
	// 	Description:   				Class
	//	License:    	  			GNU General Public License (GPL)
	//  Release Date:               May 29th / 2006
	//  Last Update date: 			Jan 11th / 2011 (rewrited)
	// 
	//  Comments: A very simple tool which will help php developers to compare if their Remote databases are the same as their local databases
	//
	//            Sometimes you made changes in your local database but forget to do in your Remote, even more if you use several databases
	//
	//	Features: 
	//					* Easy to use
	//					* Local and remote databases may have different names.
	//					* If the class find differences between databases, the row containing the differences are painted red
	//
	//  Tested on:
	//					* php 4.x, php 5x
	//
	//				
	//  If you make any modifications making it better, please let me know to info at gurusistemas dot com
	//
	//  Do you know the powerfull phpMyDataGrid?  go to www.gurusistemas.com to get more info
	//
// =====================================================================================================================================================

class DBCompare{
	var $linkMySQL = "";
	var $linkODBC  = "";
	var $LocalDatabase  = "";
	var $RemoteDatabase = "";
	var $displayMatches = false;
	
	function SelectLocalDatabase($db){
		$this->LocalDatabase=$db;
		$this->RemoteDatabase=$db;
	}
	
	function SelectRemoteDatabase($db){
		$this->RemoteDatabase=$db;
	}
	
	function ConectaLocal($serverdb="localhost",$userdb="root",$passworddb=""){
    //$this->linkMySQL = mysqli_connect($serverdb, $userdb, $passworddb, $this->LocalDatabase);
		$this->linkMySQL = mysql_connect($serverdb, $userdb, $passworddb) or die(mysql_error());
    
		mysql_select_db($this->LocalDatabase, $this->linkMySQL) or die(mysql_error());
	}

	function ConectaRemote($serverdb="localhost",$userdb="root",$passworddb=""){
    //$this->linkRemote = mysqli_connect($serverdb, $userdb, $passworddb, $this->linkRemote);
    
		$this->linkRemote = mysql_connect($serverdb, $userdb, $passworddb) or die(mysql_error());
		mysql_select_db($this->RemoteDatabase, $this->linkRemote) or die(mysql_error());
    
    
	}
	
	function Compare(){
		$tablesLocal = $tablesRemote = $tableList = array();
		$strSQL = "SHOW TABLES FROM {$this->LocalDatabase}";
		$resultTablesMySQL = mysql_query($strSQL, $this->linkMySQL);
		if (!$resultTablesMySQL) {
		   echo mysql_error($this->linkMySQL);
		   exit;
		}
		$nMySql=0;
		while ($rowTablesMySQL = mysql_fetch_row($resultTablesMySQL)) {
			$tablesLocal[$rowTablesMySQL[0]] = $rowTablesMySQL[0];
			$nMySql++;
		}
		$strSQL = "SHOW TABLES FROM {$this->RemoteDatabase}";
		$resultTablesMySQL = mysql_query($strSQL, $this->linkRemote);
		if (!$resultTablesMySQL) {
		   echo mysql_error($this->linkRemote);
		   exit;
		}
		$nMySqlRemote=0;
		while ($rowTablesMySQL = mysql_fetch_row($resultTablesMySQL)) {
			$tablesRemote[$rowTablesMySQL[0]] = $rowTablesMySQL[0];
			$nMySqlRemote++;
		}
		mysql_free_result($resultTablesMySQL);
		
		echo "<table border='1' style='border-collapse:collapse;width:100%'>";
		echo "<tr><td colspan='2' style='background:#000;color:#FFF; text-align:center'><strong>List of Tables</strong></td></tr>";
		echo "<tr style='background:#000; color:#FFF; text-align:center'><td style='width:50%'><strong>Local [".$this->LocalDatabase."]</strong></td>";
		echo "<td style='width:50%'><strong>Remote [".$this->RemoteDatabase."]</strong></td></tr>";
		foreach( $tablesLocal as $tablename){
			if (isset($tablesRemote[$tablename])){
			   	$tableList[$tablename] = $tablename;
				if ($this->displayMatches){
					echo "<tr align='center' style='background:#DFFFDF'><td>" . $tablename . "</td>";
					echo "<td>" . $tablesRemote[$tablename] . "</td></tr>";
				}
			}else{
				echo "<tr align='center' style='background:#FFDFDF'><td>" . $tablename . "</td>";
				echo "<td>&nbsp;</td></tr>";
			}
			unset($tablesLocal[$tablename]);
			unset($tablesRemote[$tablename]);
		}
		foreach( $tablesRemote as $tablename){
			echo "<tr align='center' style='background:#FFDFDF'><td>&nbsp;</td>";
			echo "<td>" . $tablesRemote[$tablename] . "</td></tr>";
			unset($tablesLocal[$tablename]);
			unset($tablesRemote[$tablename]);
		}
		if ($nMySql!=$nMySqlRemote){
			echo "<tr><td colspan='2' bgcolor='#FFDFDF' align='center'><strong>Table number mismatch (Local: $nMySql tables <=> Remote: $nMySqlRemote tables)</strong></td></tr>";
		}
		echo "</table><br>&nbsp;";
		echo "<table border='1' align='center' width='100%'>";

		foreach( $tableList as $tablename ){
			$fieldsLocal = $fieldsRemote = array();
			$foundErrors = 0;
			echo "<tr><td colspan='4' style='background:#000; color:#fff'><strong>Checking table [".$tablename."]</strong></td></tr>";
			echo "<tr style='background:#000; color:#fff'><td align='center'><strong>Status</strong></td>";
			echo "<td align='center'><strong>FIELD DATA</strong></td>";
			echo "<td align='center'><strong>LOCAL</strong></td>";
			echo "<td align='center'><strong>REMOTE</strong></td></tr>";
			$resultMySQL = mysql_query("SHOW COLUMNS FROM " . $tablename, $this->linkMySQL);
			if (!$resultMySQL) { echo mysql_error($this->linkMySQL); exit; }
			
			if (mysql_num_rows($resultMySQL) > 0) {
				while ($rowTablesMySQL = mysql_fetch_assoc($resultMySQL)) $fieldsLocal[] = $rowTablesMySQL;
			}
			
			$resultRemote = mysql_query("SHOW COLUMNS FROM " . $tablename, $this->linkRemote);
			if (!$resultRemote) { echo mysql_error($this->linkRemote); exit; }
			if (mysql_num_rows($resultRemote) > 0) {
				while ($rowTablesMySQL = mysql_fetch_assoc($resultRemote)) $fieldsRemote[] = $rowTablesMySQL;
			}
			$n=1;
			$first = "";
			$fieldOutput = "";
			foreach ($fieldsLocal as $key => $value){
				$fieldHasErrors = false;
				foreach($value as $subkey=>$subValue){
					if ($first == $subkey and $this->displayMatches) $fieldOutput.= "<tr><td colspan='4'>&nbsp;</td></tr>";
					if (empty($first)) $first = $subkey;
					if ($fieldsLocal[$key][$subkey] != $fieldsRemote[$key][$subkey]){
						$fieldOutput.= "<tr style='background:#FFDFDF'><td align='center' width='5%'>ERROR</td>";
						$fieldOutput.= "<td align='center' width='33%'>".$subkey."&nbsp;</td>";
						$fieldOutput.= "<td align='center' width='31%'>".$fieldsLocal[$key][$subkey]."&nbsp;</td>";
						$fieldOutput.= "<td align='center' width='31%'>".$fieldsRemote[$key][$subkey]."&nbsp;</td></tr>";
						$foundErrors++;
						$fieldHasErrors = true;
					}else{
						$fieldOutput.= "<tr style='background:#DFFFDF'><td align='center' width='5%'>OK</td>";
						$fieldOutput.= "<td align='center' width='33%'>".$subkey."&nbsp;</td>";
						$fieldOutput.= "<td align='center' width='31%'>".$fieldsLocal[$key][$subkey]."&nbsp;</td>";
						$fieldOutput.= "<td align='center' width='31%'>".$fieldsRemote[$key][$subkey]."&nbsp;</td></tr>";
					}
				}
			}
			if ($foundErrors>0){
				echo $fieldOutput . "<tr style='background:#FFDFDF'><td align='right' colspan='4'>{$foundErrors} errors found</td></tr>";
			}else{
				if ($this->displayMatches) echo $fieldOutput;
				echo "<tr style='background:#DFFFDF'><td align='right' colspan='4'>OK</td></tr>";
			}
		}
		echo "</table><br>&nbsp;";
	}
}
?>