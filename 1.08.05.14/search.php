<?
/***************************************************************************
 *                               search.php
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



//========================================
// Get all unread posts
//========================================
if (isset($_GET['action']) && $_GET['action'] == 'unreadposts') {

	$sql = "SELECT DISTINCT t.topic_id, t.topic_status, t.topic_type, t.topic_title, t.topic_status, t.topic_poster, t.topic_views, t.topic_replies, u.users_username FROM `" . $DBTABLEPREFIX . "topics` t INNER JOIN `" . $DBTABLEPREFIX . "users` u ON u.users_id = t.topic_poster LEFT JOIN `" . $DBTABLEPREFIX . "posts_read` pr ON pr.pr_topic_id = t.topic_id AND pr.pr_userid = '$_SESSION[userid]' WHERE ISNULL(pr.pr_topic_id)";
	$result = mysql_query($sql);	
	
		if(mysql_num_rows($result) == 0) //if NO results, stop the script & return the error message
		{
			$page_content .= "\n<center><table class='SResultsForumBorder' border='0' cellpadding='0' cellspacing='1'>
								  <tr class='title1'>
								    <td class='VForumT1'>$T_Recent_Unread_Topics</td>	
								  </tr>
								  <tr class='row1'>
								    <td align='center'>No unread topics or posts were found.</td>
								  </tr>		
								  <tr class='title1'>
								    <td colspan='7'>&nbsp;</td>
								  </tr>
								  </table></center><br /><br />";
		} 
		else //if result found, run the rest of the script
		{		
			$page_content .= "\n<center><table class='SResultsForumBorder' border='0' cellpadding='0' cellspacing='1'>
								  <tr class='title1'>
								    <td class='VForumT1' colspan='7'><div style=\"float: right;\"><a href=\"$menuvar[search]?action=markallread\">Mark all topics read</a></div>$T_Recent_Unread_Topics</td>	
								  </tr>
								  <tr class='title2'>
								    <td class='SResultsT2Column1'></td>
								    <td class='SResultsT2Column2'></td>
								    <td class='SResultsT2Column3'>$T_Title</td>
								    <td class='SResultsT2Column4'>$T_Poster</td>
								    <td class='SResultsT2Column5'>$T_Views</td>
								    <td class='SResultsT2Column6'>$T_Replies</td>
								    <td class='SResultsT2Column7'>$T_Last Post</td>
								  </tr>";
								
			while ( $row = mysql_fetch_array($result) ) {
				$page_content .= "\n  <tr class='row1'>
									    <td class='SResultsR1Column1'><center>";
				//========================================
				// Mark wether our topic is read or not
				//========================================
				if($_SESSION['username']) {
					$sql2 = "SELECT * FROM `" . $DBTABLEPREFIX . "posts_read` WHERE pr_topic_id = '$row[topic_id]' AND pr_userid = '" . $_SESSION['userid'] . "'"; //GETS FORUM NAME
					$result2 = mysql_query($sql2);
					
					if(mysql_num_rows($result2) == 0) //if NO results, then the topic is new
					{
						//================================
						// New Posts
						// Display topic icon
						//================================
						if ($topic_status == TOPIC_LOCKED) {
							$page_content .= "<img src='images/newl.jpg' alt='' />";
						}
						else{
							if ($topic_type == POST_STICKY) {
								$page_content .= "<img src='images/news.jpg' alt='' />";
							}
							elseif ($topic_type == POST_ANNOUNCE || $topic_type == POST_GLOBAL_ANNOUNCE) {
								$page_content .= "<img src='images/newa.jpg' alt='' />";
							}
							else {
								$page_content .= "<img src='images/newp.jpg' alt='' />";
							}
						}
					} 
					else //if result found, run the rest of the script
					{
						//================================
						// No new Posts
						// Display topic icon
						//================================
						if ($topic_status == TOPIC_LOCKED) {
							$page_content .= "<img src='images/nonewl.jpg' alt='' />";
						}
						else{
							if ($topic_type == POST_STICKY) {
								$page_content .= "<img src='images/nonews.jpg' alt='' />";
							}
							elseif ($topic_type == POST_ANNOUNCE || $topic_type == POST_GLOBAL_ANNOUNCE) {
								$page_content .= "<img src='images/nonewa.jpg' alt='' />";
							}
							else {
								$page_content .= "<img src='images/nonewp.jpg' alt='' />";
							}	
						}
					}
					mysql_free_result($result2); //free our query
				}
				else //if result found, run the rest of the script
				{
					//================================
					// Guest is viewing the forum
					// Display topic icon
					//================================
					if ($topic_type == POST_STICKY) {
						$page_content .= "<img src='images/nonews.jpg' alt='' />";
					}
					elseif ($topic_type == POST_ANNOUNCE || $topic_type == POST_GLOBAL_ANNOUNCE) {
						$page_content .= "<img src='images/nonewa.jpg' alt='' />";
					}
					else {
						$page_content .= "<img src='images/nonewp.jpg' alt='' />";
					}	
				}
				
				$page_content .= "</center></td>
									    <td class='SResultsR1Column2'></td>
									    <td class='SResultsR1Column3'><a href='$menuvar[VIEWTOPIC]&id=" . $row['topic_id'] . "'>" . $row['topic_title'] . "</a></td>
									    <td class='SResultsR1Column4'><a href='$menuvar[PROFILE]&action=viewprofile&id=" . $row['topic_poster'] . "'>" . $row['users_username'] . "</a></td>
									    <td class='SResultsR1Column5'>" . $row['topic_views'] . "</td>
									    <td class='SResultsR1Column6'>" . $row['topic_replies'] . "</td>
									    <td class='SResultsR1Column7'>";
				get_last_post("pageContent", "topic", $row[topic_id]); // Print out the last topic
				$page_content .= "		</td>	
									  </td></tr>
									  <tr class='row1'>
									    <td colspan='7'></td>
									  </tr>";
			}
			
			$page_content .= "\n  <tr class='title1'>
								    <td colspan='7'>&nbsp;</td>
								  </tr>
								</table>
							</center>
							<br /><br />";
		}
}

//========================================
// Marks all posts as read
//========================================
elseif (isset($_GET[action]) && $_GET[action] == "markallread") {
	$sql = "SELECT t.topic_id FROM `" . $DBTABLEPREFIX . "topics` t INNER JOIN `" . $DBTABLEPREFIX . "users` u ON u.users_id = t.topic_poster LEFT JOIN `" . $DBTABLEPREFIX . "posts_read` pr ON pr.pr_topic_id = t.topic_id AND pr.pr_userid = '$_SESSION[userid]' WHERE ISNULL(pr.pr_topic_id)";

	$result = mysql_query($sql);	
		if(mysql_num_rows($result) == 0) //if NO results, stop the script & return the error message
		{
			$page_content .= "\nNo unread topics or posts were found.<br /><br />";
		} 
		else //if result found, run the rest of the script
		{	
			while($row = mysql_fetch_array($result)) {
				$sql2 = "INSERT INTO `" . $DBTABLEPREFIX . "posts_read` (`pr_topic_id` , `pr_userid` ) VALUES ('$row[topic_id]', '$_SESSION[userid]');";
				$result2 = mysql_query($sql2);
			}
			$page_content .= "\nAll posts have been marked as read, and you are being taken back to the list of unread topics.<br />
 								<meta http-equiv='refresh' content='1;url=$menuvar[search]?action=unreadposts'>";
		}
}

//========================================
// Perform search and display results
//========================================
elseif (isset($_POST['keywords'])) {
	$keywords = $_POST['keywords'];
	$keywords = keeptasafe($keywords);
	
	$sql = "SELECT DISTINCT t.topic_id, t.topic_status, t.topic_type, t.topic_title, t.topic_status, t.topic_poster, t.topic_views, t.topic_replies, u.users_username from `" . $DBTABLEPREFIX . "topics` t, `" . $DBTABLEPREFIX . "posts` p, `" . $DBTABLEPREFIX . "users` u WHERE (p.post_subject LIKE '%$keywords%' OR p.post_text LIKE '%$keywords%' OR t.topic_title LIKE '%$keywords%') AND t.topic_id = p.post_topic_id AND u.users_id = t.topic_poster";

	$result = mysql_query($sql);	
		if(mysql_num_rows($result) == 0) //if NO results, stop the script & return the error message
		{
			$page_content .= "\nNo topics or posts were found.<br /><br />";
		} 
		else //if result found, run the rest of the script
		{		
			$page_content .= "\n<center><table class='SResultsForumBorder' border='0' cellpadding='0' cellspacing='1'>
								  <tr class='title1'>
								    <td class='VForumT1' colspan='7'>$T_Search_Results</td>	
								  </tr>
								  <tr class='title2'>
								    <td class='SResultsT2Column1'></td>
								    <td class='SResultsT2Column2'></td>
								    <td class='SResultsT2Column3'>$T_Title</td>
								    <td class='SResultsT2Column4'>$T_Poster</td>
								    <td class='SResultsT2Column5'>$T_Views</td>
								    <td class='SResultsT2Column6'>$T_Replies</td>
								    <td class='SResultsT2Column7'>$T_Last Post</td>
								  </tr>";
								
			while ( $row = mysql_fetch_array($result) ) {
				$page_content .= "\n  <tr class='row1'>
									    <td class='SResultsR1Column1'><center>";
				//========================================
				// Mark wether our topic is read or not
				//========================================
				if($_SESSION['username']) {
					$sql2 = "SELECT * FROM `" . $DBTABLEPREFIX . "posts_read` WHERE pr_topic_id = '$row[topic_id]' AND pr_userid = '" . $_SESSION['userid'] . "'"; //GETS FORUM NAME
					$result2 = mysql_query($sql2);
					
					if(mysql_num_rows($result2) == 0) //if NO results, then the topic is new
					{
						//================================
						// New Posts
						// Display topic icon
						//================================
						if ($topic_status == TOPIC_LOCKED) {
							$page_content .= "<img src='images/newl.jpg' alt='' />";
						}
						else{
							if ($topic_type == POST_STICKY) {
								$page_content .= "<img src='images/news.jpg' alt='' />";
							}
							elseif ($topic_type == POST_ANNOUNCE || $topic_type == POST_GLOBAL_ANNOUNCE) {
								$page_content .= "<img src='images/newa.jpg' alt='' />";
							}
							else {
								$page_content .= "<img src='images/newp.jpg' alt='' />";
							}
						}
					} 
					else //if result found, run the rest of the script
					{
						//================================
						// No new Posts
						// Display topic icon
						//================================
						if ($topic_status == TOPIC_LOCKED) {
							$page_content .= "<img src='images/nonewl.jpg' alt='' />";
						}
						else{
							if ($topic_type == POST_STICKY) {
								$page_content .= "<img src='images/nonews.jpg' alt='' />";
							}
							elseif ($topic_type == POST_ANNOUNCE || $topic_type == POST_GLOBAL_ANNOUNCE) {
								$page_content .= "<img src='images/nonewa.jpg' alt='' />";
							}
							else {
								$page_content .= "<img src='images/nonewp.jpg' alt='' />";
							}	
						}
					}
					mysql_free_result($result2); //free our query
				}
				else //if result found, run the rest of the script
				{
					//================================
					// Guest is viewing the forum
					// Display topic icon
					//================================
					if ($topic_type == POST_STICKY) {
						$page_content .= "<img src='images/nonews.jpg' alt='' />";
					}
					elseif ($topic_type == POST_ANNOUNCE || $topic_type == POST_GLOBAL_ANNOUNCE) {
						$page_content .= "<img src='images/nonewa.jpg' alt='' />";
					}
					else {
						$page_content .= "<img src='images/nonewp.jpg' alt='' />";
					}	
				}
				
				$page_content .= "</center></td>
									    <td class='SResultsR1Column2'></td>
									    <td class='SResultsR1Column3'><a href='$menuvar[VIEWTOPIC]&id=" . $row['topic_id'] . "&highlight=$keywords'>" . $row['topic_title'] . "</a></td>
									    <td class='SResultsR1Column4'><a href='$menuvar[PROFILE]&action=viewprofile&id=" . $row['topic_poster'] . "'>" . $row['users_username'] . "</a></td>
									    <td class='SResultsR1Column5'>" . $row['topic_views'] . "</td>
									    <td class='SResultsR1Column6'>" . $row['topic_replies'] . "</td>
									    <td class='SResultsR1Column7'>";
				get_last_post("pageContent", "topic", $row[topic_id]); // Print out the last topic
				$page_content .= "		</td>
									  </td></tr>
									  <tr class='row1'>
									    <td colspan='7'></td>
									  </tr>";
			}
			
			$page_content .= "\n  <tr class='title1'>
								    <td colspan='7'>&nbsp;</td>
								  </tr>
								  </table></center><br /><br />";
		}
}

//========================================
// If all else fails, print out the 
// search page
//========================================
else { 

$page_content .= "\n	<center>
	<form action=\"$menuvar[SEARCH]\" method=\"post\">
	<table class='SForumBorder' border='0' cellpadding='0' cellspacing='1'>
		<tr class=\"title1\">
			<td class='VForumT1' colspan=\"2\">$T_Search</td>
		</tr>
		<tr class=\"row1\">
			<td class=\"SR1Column1\">$T_Keywords</td>
			<td class=\"SR1Column2\"><input type=\"text\" name=\"keywords\" size=\"20\" maxlength=\"40\" value=\"\" /></td>
		</tr>
		<tr class=\"row1\">
			<td colspan=\"2\">&nbsp;</td>
		</tr>
		<tr class=\"row1\">
			<td colspan=\"2\"><center><input type=\"submit\"  class=\"button\" value=\"Search\" /></center></td>
		</tr>
	</table>
	</form>
	</center>
	<br /><br />";


}
unset($_POST['keywords']); //weve finished registering the session variables le them pass so they dont get reregistered

$page->setTemplateVar("PageContent", $page_content); 
?>