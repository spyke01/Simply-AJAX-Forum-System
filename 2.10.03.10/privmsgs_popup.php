<?PHP 

/***************************************************************************
 *                               privmsgs_popup.php
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
define('IN_SAFORUM', true);
include '_db.php';
session_start();
include_once ('includes/menu.php');
include_once ('includes/functions.php');
include_once ('includes/constants.php');
include_once ('config.php');
global $board_config;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" >
<head>
	<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
	<title>.:<? echo $board_config['board_name']; ?>:.</title>

	<!--Stylesheets Begin-->
	<link rel="stylesheet" type="text/css" href="stylesheets/main.css" />
	<link rel="stylesheet" type="text/css" href="stylesheets/newblue.css" />
	
	<link rel="stylesheet" type="text/css" href="stylesheets/
		<?php 
		
			$currentuser = $_SESSION['username'];
			
			if (isset($currentuser)) {
						
				$sql = "SELECT * FROM `users` WHERE users_username='$currentuser' ";
				$result = mysql_query($sql);
				if($result && mysql_num_rows($result) > 0) {
   					$row = mysql_fetch_array($result);
   					echo $row['users_style'];
   					
   				}
   				else { echo $board_config['style']; }

			}
			else {
					echo $board_config['style'];
			}
		
		?>
	.css" />
	<!--Stylesheets End-->
	
	<!--Javascripts Begin-->
	<script type="text/javascript">
		var saforum_var_base_url      = "<? echo $board_config[board_url]; ?>";
	</script>
	<script type="text/javascript" src='javascripts/ssf_global.js'></script>
	<script type="text/javascript" src='javascripts/bbcode.js'></script>
	<!--Javascripts End-->
	<link rel="shortcut icon" href="favicon-1.ico" />
</head>

<body>
<? 
	if (isset($_SESSION['username'])) {
		//==============================================
		// Pull our number of private messages from the 
		// inbox, as well as the number of those
		// that are new.
		//==============================================
		$sql = "SELECT msg_read FROM `priv_msgs` WHERE msg_to_id = '$_SESSION[userid]' AND msg_folder != '" . SAVEDFOLDER . "' AND msg_read != '" . MSG_READ . "'"; //Gets ony msgs in the inbox 
		
		$result = mysql_query($sql);
		
		$nummsgs = '0';

		if($result && mysql_num_rows($result) > 0) {
			$nummsgs = mysql_num_rows($result); //get total number of messages in inbox
			if($nummsgs > '1') {
				$msg = "You have <b>$nummsgs</b> new messages.";
			}
			else {
				$msg = "You have <b>$nummsgs</b> new message.";
			}			
		}
		else {
			$msg = "You have <b>no</b> new messages.";
		}
		mysql_free_result($result); //free our query
		//==============================================
		// END DATABASE QUERY
		//==============================================	
	
		echo "\n<center>";
		echo "\n<div class='pmbox'>";
		echo "\n		<h3>New Messages</h3>";	
		echo "\n		<p>";
		echo "\n			$msg<br /><br />";
		echo "\n			<a href='javascript:window.close()'>Close Window</a><br />";
		echo "\n		</p>";		
		echo "\n</div>";
		echo "\n</center>";
	}
?>
</body>
</html>