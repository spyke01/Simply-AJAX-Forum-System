<?
/***************************************************************************
 *                               users.php
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
	
if($_SESSION['username'] && $_SESSION['user_level'] == ADMIN) {	
	//========================================
	// Check Database For This User
	//========================================
	if (isset($_POST['user'])) {
		$user = $_POST['user'];
		$user = parseurl($user);
	
		$sql = "SELECT * from `" . $DBTABLEPREFIX . "users` WHERE users_username = '$user' LIMIT 1";
	
		$result = mysql_query($sql);	
			if(mysql_num_rows($result) == 0) //if NO results, stop the script & return the error message
			{
				$page_content .= "\nNo users were found by the name of $user.<br /><br />";
			} 
			else //if result found, run the rest of the script
			{	
				while ( $row = mysql_fetch_array($result) )
				{
					$page_content .= "You are being redirected to the edit profile page for: $user.
	 								<meta http-equiv='refresh' content='1;url=$menuvar[PROFILE]&action=editprofile&id=$row[users_id]'>";
 				}
			}
	}
	
	//========================================
	// If we got here then they havent
	// searched for a username yet
	//========================================
	else { 
		$page_content .= "\n<center>
	
		<form action=\"index.php?p=admin&s=users\" method=\"post\">
		<table class=\"forumborder\" border=\"0\" cellpadding=\"0\" cellspacing=\"1\" width=\"300\">
			<tr>
				<td class=\"title1\" colspan=\"2\">
					<div style=\"float: right;\"><a href=\"javascript:sqr_show_hide('divDrop');\"><img src=\"images/plus.png\" alt=\"Show/hide\" /></a></div>
					Edit Users
				</td>
			</tr>
			<tbody id=\"divDrop\">
				<tr>
					<td width=\"32%\" class=\"row1\">Username: </td>
					<td width=\"68%\" class=\"row1\"><input type=\"text\" name=\"user\" size=\"20\" maxlength=\"40\" value=\"\" /></td>
				</tr>
				<tr>
					<td width=\"100%\" colspan=\"2\" class=\"row1\">&nbsp;</td>
				</tr>
				<tr>
					<td width=\"100%\" colspan=\"2\" class=\"row1\">
						<center><input type=\"submit\" class=\"button\" value=\"Search\" /></center>
					</td>
				</tr>
			</tbody>
		</table>
		</form>
		</center>
		<br /><br />";
	
	}
	unset($_POST['username']); //weve finished registering the session variables le them pass so they dont get reregistered
	
}

$page->setTemplateVar("PageContent", $page_content);
?>