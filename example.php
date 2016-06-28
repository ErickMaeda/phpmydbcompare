<?php

error_reporting(E_ERROR | E_PARSE); //I put this to do not show warnings ;)

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

include("phpMyDBCompare.inc.php");

$comp = new DBCompare();
$comp -> SelectLocalDatabase("db_local_do_dev_zuado"); //database name local
$comp -> SelectRemoteDatabase("db_remota_linda");				// Call this only If the remote database has a different name
$comp -> ConectaLocal("127.0.0.1","root","senharoot"); //localcredentials
$comp -> ConectaRemote("mysql.basedoremoto.com.br","userremoto","senharemota"); //remote credentials
$comp -> displayMatches = true;     					// set true to display  matches as well as differences
$comp -> Compare();
?>