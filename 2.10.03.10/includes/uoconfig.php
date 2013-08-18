<?
/***************************************************************************
 *                               uoconfig.php
 *                            -------------------
 *   begin                : Saturday, Sept 24, 2005
 *   copyright            : (C) 2005 Paden Clayton - Fast Track Sites
 *   email                : sales@fasttacksites.com
 *
 *
 ***************************************************************************/

/***************************************************************************
 *
 * This program is licensed under the Fast Track Sites Program license 
 * located inside the license.txt file included with this program. This is a 
 * legally binding license, and is protected by all applicable laws, by 
 * editing this page you fall subject to these licensing terms.
 *
 ***************************************************************************/
//======================================================
// Users Online Configuration
//======================================================

	$timeoutseconds = ($_SESSION[username]) ? '600' : '300';	
	$timestamp=time();
	$timeout=$timestamp+$timeoutseconds;
	$ip = $REMOTE_ADDR;	
	$currentuser = ($_SESSION[username]) ? $_SESSION[username] : 'Guest';
	
	//==========================================================	
	// Delete users that have been online for more then "$timeoutseconds" seconds	
	//==========================================================
	@mysql_query("DELETE FROM `" . $DBTABLEPREFIX . "usersonline` WHERE uo_timestamp<$timestamp");
	
	//==========================================================		
	// Add this user to database	
	//==========================================================
	$page = keeptasafe($_GET['p']);
	$id = parseurl($_GET[id]);
	$file = ($page == 'viewforum' || $page == 'viewtopic') ? "index.php?p=" . $page . "&id=" . $id : "index.php?p=" . $page;

	$sql = "SELECT * FROM `" . $DBTABLEPREFIX . "usersonline` WHERE uo_ip = '$ip'";
	$result = mysql_query($sql);
	
	// If our user's ip address isn't in the db
	if (mysql_num_rows($result) == 0) {
		// Kill out all versions of our user
		if ($_SESSION[username]) {
			$sql2 = "DELETE FROM `" . $DBTABLEPREFIX . "usersonline` WHERE uo_username = '$_SESSION[username]'";				          
			@mysql_query($sql2);
		}
		$sql2 = "INSERT INTO `" . $DBTABLEPREFIX . "usersonline` (uo_username, uo_timestamp, uo_ip, uo_file) VALUES('$currentuser', '$timeout', '$ip', '$file')";				          
		mysql_query($sql2) or die('<br />Error, insert query failed' . $sql);
	}
	// If it is
	else {	
		// Kill out all versions of our user
		if ($_SESSION[username]) {
			$sql2 = "DELETE FROM `" . $DBTABLEPREFIX . "usersonline` WHERE uo_username = '$_SESSION[username]' OR uo_ip = '$ip'";				          
			@mysql_query($sql2);
		}
		else {
			$sql2 = "DELETE FROM `" . $DBTABLEPREFIX . "usersonline` WHERE uo_ip = '$ip'";				          
			@mysql_query($sql2);			
		}		
		$sql2 = "INSERT INTO `" . $DBTABLEPREFIX . "usersonline` (uo_username, uo_timestamp, uo_ip, uo_file) VALUES('$currentuser', '$timeout', '$ip', '$file')";				          
		mysql_query($sql2) or die('<br />Error, insert query failed' . $sql);
	}
	
	
	
?>