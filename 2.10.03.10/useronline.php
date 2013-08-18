<?php
/***************************************************************************
 *                               useronline.php
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
	
	//==========================================================
	// Find total number of registered users
	//==========================================================
	$sql = "SELECT * FROM `" . $DBTABLEPREFIX . "users` WHERE users_username != 'Guest' ORDER BY users_signup_date DESC";
	$result = mysql_query($sql) or die (mysql_error()."<br />Couldn't execute query: $sql");
	
	if(mysql_num_rows($result) == 0) //if NO results, stop the script & return the error message
	{
		$page_footer .= "\nError reading users table section 1.<br /><br />";
	} 
	else //if result found, run the rest of the script
	{
		$totalregistered = mysql_num_rows($result);
		$row = mysql_fetch_array($result);
		$newestuser = $row['users_username'];
	}
	mysql_free_result($result); //free our query
	
	//==========================================================
	// Find total number of posts
	//==========================================================
	$sql = "SELECT * FROM `" . $DBTABLEPREFIX . "posts`";
	$result = mysql_query($sql) or die (mysql_error()."<br />Couldn't execute query: $sql");
	
	if(mysql_num_rows($result) == 0) //if NO results, stop the script & return the error message
	{
		$totalposts = 0;
	} 
	else //if result found, run the rest of the script
	{
		$totalposts = mysql_num_rows($result);
	}
	mysql_free_result($result); //free our query
	
	//==========================================================
	// Shows total users online as well as username	
	//==========================================================
	$sql = "SELECT * FROM `" . $DBTABLEPREFIX . "usersonline`";
	$result = mysql_query($sql) or die (mysql_error()."<br />Couldn't execute query: $sql");
	
	if(mysql_num_rows($result) == 0) //if NO results, stop the script & return the error message
	{
		$page_footer .= "\nError reading usersonline table section 2.<br /><br />";
	} 
	else //if result found, run the rest of the script
	{
		$totalonline = mysql_num_rows($result);
	}
	mysql_free_result($result); //free our query
		
	//==========================================================
	// Find out whos online and if they're a user make a link 
	// to their profile page	
	//==========================================================
	$sql = "SELECT * FROM `" . $DBTABLEPREFIX . "usersonline` WHERE uo_username != 'Guest'";
	$result = mysql_query($sql) or die (mysql_error()."<br />Couldn't execute query: $sql");
	
	$totalguests = $totalonline - mysql_num_rows($result);
	$totalusers = $totalonline - $totalguests;
			
	if(mysql_num_rows($result) == 0) //if NO results, stop the script & return the error message
	{
		$users = "No registered users logged in."; //add username to our list ^^
	} 
	else //if result found, run the rest of the script
	{
		while ( $row = mysql_fetch_array($result) ) {
			$sql2 = "SELECT * FROM `" . $DBTABLEPREFIX . "users` WHERE users_username = '$row[uo_username]'";
			$result2 = mysql_query($sql2) or die (mysql_error()."<br />Couldn't execute query: $sql");
			
			if(mysql_num_rows($result2) == 0) //if NO results, stop the script & return the error message
			{
				$users = "No registered users logged in."; //add username to our list ^^
			} 
			else //if result found, run the rest of the script
			{
				while ( $row2 = mysql_fetch_array($result2) ) {
					$users .= ($doneonce == "1") ? ", " : "";
					$users .= "<a href='$menuvar[PROFILE]?action=viewprofile&amp;id=" . $row2['users_id'] . "' class='stats'>$row2[users_username]</a>";
					$doneonce = "1";
				}
				
			}
			mysql_free_result($result2); //free our query
		}
	
	}
	mysql_free_result($result); //free our query
		
	//==========================================================
	//Print out our nice table	
	//==========================================================
	$page_footer .= "\n<center>
						<div class='UOnlineForumBorder'>
						<h3>
							<div style=\"float: right;\"><a href=\"javascript:sqr_show_hide('boardStatsDrop');\"><img src=\"images/plus.png\" style=\"width: 15px; height: 15px; border:0px;\" alt=\"Show/hide users viewing stats\" /></a></div>
							Board Stats
						</h3>
						<div id=\"boardStatsDrop\">
							<div class='title'>Online Stats</div>
							<p>
								" . $totalonline . " Users Online.<br /><br />
								" . $totalguests . " Guests.<br />
								" . $totalusers . " Users.<br /><br />
								" . $users . "
							</p>
							<div class='title'>Board Stats</div>
							<p>	
								We have a total of " . $totalregistered . " registered users.<br />
								Our newest user is: " . $newestuser . ".<br /><br />
								Our users have made " . $totalposts . " posts.<br />
							</p>
						</div>
						</div>
						</center>
						<br /><br />";

$page->setTemplateVar("PageFooter", $page_footer);
?>