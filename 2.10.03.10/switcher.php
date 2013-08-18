<?php
/***************************************************************************
 *                               switcher.php
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
	
	//check and see if there is a user logged in
	if (isset($currentuser)) {
		$style = $_REQUEST['style']; //Get the style user is changing to
		$style = keepsafe($style);
		
		$sql = "UPDATE `" . $DBTABLEPREFIX . "users` SET users_style='$style' WHERE users_username='" . $_SESSION['username'] . "'";
		mysql_query($sql) or die('Error, update query failed');
					
		//confirm
	 	$page_content .= "Your style has been changed, and you are being redirected to the homepage. 
	 						<meta http-equiv='refresh' content='1;url=" . $menuvar[HOME] . "'>";
 	
	}
	
	//otherwise tell them to register and account
	else {
	 	$page_content .= "You must have a user account to change your style, you are being redirected to the homepage. 
	 						<meta http-equiv='refresh' content='3;url=" . $menuvar[HOME] . "'>";
	}

$page->setTemplateVar("PageContent", $page_content);
?>