<? 
/***************************************************************************
 *                               privmsgs.php
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
$action = $_GET['action'];
$msgid = $_GET['msgid'];
$to = $_GET['to'];
$re = $_GET['re'];
$move = $_POST['move'];
$ChgPM = $_POST['ChgPM'];
$pmactions = $_POST['pmactions'];
$delete = $_POST['delete'];

$action = keepsafe($action);
$msgid = keepsafe($msgid);
$to = keeptasafe($to);
$re = keeptasafe($re);
$move = keeptasafe($move);
$pmactions = keeptasafe($pmactions);
$delete = keeptasafe($delete);

$subject = ""; //will hold subject for replies
$msg = ""; //will hold msg for replies
$sql = ""; //hold generated sql statements
$savedmsgs = $_GET['savedmsgs'];
$current_time = time();

if (isset($_SESSION['username'])) {

	//===========================================
	// Jumpbox Actions
	//===========================================				
	if (trim($move) != "" && trim($ChgPM) != "") {
		if($pmactions == 'saved') { $folder = SAVEDFOLDER; }
		else { $folder = INBOX; }
		
		if(isset($_POST['ChgPM'])) {
			foreach ($ChgPM as $num => $tomove) {	
				$tomove = keeptasafe($tomove);	
					
				$sql = "UPDATE `" . $DBTABLEPREFIX . "priv_msgs` SET msg_folder='$folder' WHERE msg_id = '$tomove'";
				mysql_query($sql) or die('Error, update query 1 failed');
			}
			$page_content .= "Your message(s) has been moved, and you are being redirected to your inbox.
							<meta http-equiv='refresh' content='1;url=" . $menuvar[PRIVMSGS] . "'>";	
		}
		else {
			$page_content .= "You did not select any messages to be moved, and you are being redirected to your inbox. 
							<meta http-equiv='refresh' content='1;url=" . $menuvar[PRIVMSGS] . "'>";		
		}	
		unset($move);
	}		
	elseif (trim($delete) != "" && trim($ChgPM) != "") {
		if(isset($_POST['ChgPM'])) {
			foreach ($ChgPM as $num => $todelete) {	
				$todelete = keeptasafe($todelete);	
				
				$sql = "DELETE FROM `" . $DBTABLEPREFIX . "priv_msgs` WHERE msg_id = '$todelete' AND msg_to_id='" . $_SESSION[userid] . "' ";
				mysql_query($sql) or die('Error, delete query 1 failed');
			}
			$page_content .= "Your message(s) has been deleted, and you are being redirected to your inbox. 
							<meta http-equiv='refresh' content='1;url=" . $menuvar[PRIVMSGS] . "'>";
		}
		else {
			$page_content .= "You did not select any messages to be deleteded, and you are being redirected to your inbox. 
							<meta http-equiv='refresh' content='1;url=" . $menuvar[PRIVMSGS] . "'>";		
		}		
		unset($delete);	
	}			
	
	//===========================================
	// Compose Message
	//===========================================		
	elseif ($action == 'compose') {
		$page_content .= "\n<center>
							<table border='0' cellpadding='0' cellspacing=0' width='100%'>
	  							<tr>
									<td>";
		write_pmbox();
		$page_content .= "\n		</td>
	   								<td>";
		
		if (isset($_POST['message'])) {
			$pmto = $_POST['to'];
			$pmtoid = $_POST['to'];
			$pmfromid = $_SESSION['userid'];
			$pmsubject = $_POST['subject'];
			$pmmsg = $_POST['message'];
	
			keeptasafe($pmsubject); //strip the subject of dangerous tags
			keeptasafe($pmmsg); //strip the message of dangerous tags
			keeptasafe($pmtoid);
			keeptasafe($pmto);
			
			$sql = "SELECT * FROM `" . $DBTABLEPREFIX . "users` WHERE users_username='$pmto' LIMIT 1";
			$result = mysql_query($sql) or die (mysql_error()."<br />Couldn't execute query: $sql");
			
			if(mysql_num_rows($result) == 0) //if NO results, stop the script & return the error message
			{
				$page_content .= "\nWrong username, please check the memberlist for the proper name.<br /><br />";
			} 
			else //if result found, run the rest of the script
			{
			        $row = mysql_fetch_array($result);
			        $pmtoid = $row[users_id];
			
			        $sql = "INSERT INTO `" . $DBTABLEPREFIX . "priv_msgs` (msg_read, msg_folder, msg_date, msg_from_id, msg_to_id, msg_title, msg_post)".
			          "VALUES('0', '0', '$current_time', '$pmfromid', '$pmtoid', '$pmsubject', '$pmmsg')";
			          
			        mysql_query($sql) or die('<br />Error, insert query failed' . $sql);
			
			        $page_content .= "\nYour message has been sent to: $pmto, and you are being redirected to your inbox.<br /><br />
										<meta http-equiv='refresh' content='1;url=" . $menuvar[PRIVMSGS] . "'>";			        
			}
			mysql_free_result($result); //free our selection query
		}
		else {
			if (isset($re)) { 
				$sql = "SELECT * FROM `" . $DBTABLEPREFIX . "priv_msgs` WHERE msg_id = '$re'";
				$result = mysql_query($sql);
				
				if(mysql_num_rows($result) == 0) //if NO results, stop the script & return the error message
				{
					$msg = "No such ID to reply to.";
				} 
				else //if result found, run the rest of the script
				{
					$row = mysql_fetch_array($result);
					
					//see if its an admin, a hacker, or the user trying to view the msg
					if ($row['msg_to_id'] == $_SESSION['userid'] || $_SESSION['user_level'] != USER) {
						$subject = "Re: " . $row['msg_title'];
						$msg = "[quote]" . $row['msg_post'] . "[/quote]";
					}
					else { $msg = "You are not authorized to reply to this msg."; }					
				} 
			}
			$page_content .= "\n<form name='post' action='$menuvar[COMPOSEMSG]' method='post' onsubmit='return checkForm(this)'>
									<div class=\"VForumForumBorder\">
										<div class=\"title1\">$T_New_PM</div>
										<div class=\"row1\">
											<table border='0' cellpadding='0' cellspacing='1'>
												<tr>
													<td><b>$T_PM_To</b></td><td><input type='text' name='to' size='73'";
										if (isset($to)) { $page_content .= " value='$to'></td>"; }
										else { $page_content .= "></td>"; }
			$page_content .= "\n				</tr>
												<tr>
													<td><b>$T_Subject</b></td><td><input type='text' name='subject' size='73' value='$subject'></td>
												</tr>
											</table>	
										</div>
									</div>
									<br />
									<table class='VForumForumBorder' border='0' cellpadding='0' cellspacing='1'>
										<tr class='title1'>
											<td class='VForumT2' colspan='2'>$T_New_Topic</td>	
										</tr>";
			bbcode_box(); //print out the bbcode buttons
			$page_content .= "\n			<center><textarea cols='70' rows='8' name='message' class='textinput' wrap='virtual' onselect='storeCaret(this);' onclick='storeCaret(this);' onkeyup='storeCaret(this);'></textarea><br /></center></td>
										</tr>
										<tr class='title1'>
											<td colspan='2'><center><input type='submit' name='submit' value='Submit' class='button'></center></td>
										</tr>	
									</table>
								</form>";
		}		
		unset($_POST['message']);
		
		$page_content .= "\n</td></tr>
							</table>
							<br /><br />";
	
	}

	//===========================================
	// Delete Message
	//===========================================	
	elseif ($action == 'deletemsg' && trim($msgid) != "") {
				$sql = "SELECT * FROM `" . $DBTABLEPREFIX . "priv_msgs` WHERE msg_id = '$msgid'";
				$result = mysql_query($sql);
				
				if(mysql_num_rows($result) == 0) //if NO results, stop the script & return the error message
				{
					$msg = "No such ID found.";
				} 
				else //if result found, run the rest of the script
				{
					$row = mysql_fetch_array($result);
					
					if ($_SESSION['userid'] == $row['msg_to_id'] || $_SESSION[user_level] != USER) {
						$sql = "DELETE FROM `" . $DBTABLEPREFIX . "priv_msgs` WHERE msg_id = '$msgid' ";
						mysql_query($sql) or die('Error, delete query 1 failed');
						$page_content .= "Your message has been deleted, and you are being redirected to your inbox. 
		 									<meta http-equiv='refresh' content='3;url=" . $menuvar[PRIVMSGS] . "'>";
					}
					else { $page_content .= "You are not authorized to delete this message"; }				
				} 			
	}

	//===========================================
	// Read Message
	//===========================================	
	elseif ($action == 'readmsg' && trim($msgid) != "") {

		$sql = "SELECT * FROM `" . $DBTABLEPREFIX . "priv_msgs` WHERE msg_id='$msgid'"; //Gets the msg to read 
		$result = mysql_query($sql);	
		if(mysql_num_rows($result) == 0) //if NO results, stop the script & return the error message
		{
			$page_content .= "\nNo such message was found.<br /><br />";
		} 
		else //if result found, run the rest of the script
		{		
			while ( $row = mysql_fetch_array($result) ) {
				extract($row); //so we dont have to do long array variables
				
				if ($_SESSION['userid'] == $msg_to_id || $_SESSION[user_level] != USER) {
					$sql = "UPDATE `" . $DBTABLEPREFIX . "priv_msgs` SET msg_read='1' WHERE msg_id='$msgid'";
					mysql_query($sql) or die('Error, updatepost query failed');
				
				
					$userid = $_SESSION['userid'];
	
					$msg_date = makeDate($msg_date);
					
			
					$page_content .= "\n<center>
										<table border='0' cellpadding='0' cellspacing=0' width='100%'>
			  								<tr>
			    								<td>";
					write_pmbox();
					$page_content .= "\n		</td>
			    								<td>
										<table class='PMMsgForumBorder' border='0' cellpadding='0' cellspacing='1'>
										  <tr class='title1'>
										    <td class='VForumT1' colspan='3'>$msg_title</td>	
										  </tr>
										  <tr class='title2'>";
					
			
					$sql2 = "SELECT * FROM `" . $DBTABLEPREFIX . "users` WHERE users_id='$msg_from_id'";
					$result2 = mysql_query($sql2);
				
					for($i = 0; $i < mysql_num_rows($result2); $i++) {
						
						$row = mysql_fetch_array($result2);
								
						$page_content .= "\n    <td class='PMT2MsgColumn1'><a href='$menuvar[PROFILE]&action=viewprofile&amp;id=$msg_from_id'>$row[users_username]</a></td>
											    <td class='PMT2MsgColumn2'><b><u>$T_Sent</u></b> $msg_date</td>
											    <td class='PMT2MsgColumn3'>
											<a href='$menuvar[PRIVMSGS]&action=deletemsg&amp;msgid=$msgid'><img src='$themedir/buttons/delete.jpg' alt='delete message' /></a>
											<a href='$menuvar[PRIVMSGS]&action=compose&amp;to=$row[users_username]&amp;re=$msgid'><img src='$themedir/buttons/reply.jpg' alt='reply to message' /></a>
											</td>
											  </tr>
											  <tr class='row1'><td class='PMR1MsgColumn1'>";
						if ($row[users_avatar] != NULL || $row[users_avatar] != "") {
							$page_content .= "\n    <img src='$row[users_avatar]' alt='' /><br />";
						}
						rank_title($row[users_username]);
						$page_content .= "\n</td>";
			
					}
					mysql_free_result($result2);
						
					$page_content .= "\n    <td class='PMR1MsgColumn2-3' colspan='2'>"; 
					
					$msg_post = bbcode($msg_post); //CHANGE BBCODE TO HTML
					$sig = bbcode($sig); //CHANGE BBCODE IN SIGNATURE TO HTML
					
					$page_content .= "\n    $msg_post"; //OUTPUT THE MESSAGE
					if ($attachsig) {
						$page_content .= "\n    <hr width=100 />" . $sig;
					}
					$page_content .= "\n  </td></tr>";
				}
				else { $page_content .= $T_PM_Hacking_Attempt; }
			}
		}
		
		mysql_free_result($result); //free our query
	
		
		$page_content .= "\n  <tr class='title1'>
							    <td colspan='3'>&nbsp;</td>
							  </tr>
							</table>
							</td></tr>
							</table>
							<br /><br />";
	
	
	
	}

	//===========================================
	// List Message
	//===========================================	
	else {
	
		$page_content .= "\n<center>
							 <form action='$menuvar[PRIVMSGS]' method ='post' style='display: inline; margin: 0; padding: 0;' name='multiact'>
							<table border='0' cellpadding='0' cellspacing=0' width='100%'>
	  							<tr>
	    							<td>";
		write_pmbox();
		$page_content .= "\n		</td>
	    							<td>
							<table class='PMInboxForumBorder' border='0' cellpadding='0' cellspacing='1'>
							  <tr class='title1'>
							    <td class='VForumT1' colspan='6'>$T_Messages</td>
							  </tr>
							  <tr class='title2'>
							    <td class='PMT2Column1'><input name=\"allbox\" type=\"checkbox\" value=\"Check All\" onclick=\"CheckAll();\" /></td>
							    <td class='PMT2Column2'></td>
							    <td class='PMT2Column3' colspan='2'>Title</td>
							    <td class='PMT2Column4'>$T_Sender</td>
							    <td class='PMT2Column5'>$T_Date</td>
							  </tr>";
	
		
		if (isset($savedmsgs) && $savedmsgs == '1') { 
			$sql = "SELECT * FROM `" . $DBTABLEPREFIX . "priv_msgs` WHERE msg_to_id='$userid' AND msg_folder='" . SAVEDFOLDER . "'"; //Gets ony msgs that are saved 
		}
		else {  
			$sql = "SELECT * FROM `" . $DBTABLEPREFIX . "priv_msgs` WHERE msg_to_id='$userid' AND msg_folder!='" . SAVEDFOLDER . "'"; //Gets ony msgs in the inbox 
		}
		
		$result = mysql_query($sql);
		
		if($result && mysql_num_rows($result) > 0) {
			while ( $row = mysql_fetch_array($result) ) {
				extract($row); //so we dont have to do long array variables
				
				$msg_date = makeDate($msg_date);
					
					$page_content .= "\n  <tr class='row1'>
										    <td class='PMR1Column1'><input type='checkbox' name='ChgPM[]' id='ChgPM[]' value='$msg_id' onclick=\"cca(this);\" /></td>";
					
					if ($msg_read == MSG_READ) { $page_content .= "\n    <td class='PMR1Column2'><img src='images/msgread.gif' alt='' /></td>"; }
					else { $page_content .= "\n    <td class='PMR1Column2'><center><img src='images/newmsg.gif' alt='' /></center></td>"; }	
					
					$page_content .= "\n    <td class='PMR1Column3' colspan='2'><a href='$menuvar[PRIVMSGS]&action=readmsg&msgid=$msg_id'>$msg_title</a></td>";
					
					$sql2 = "SELECT users_username, users_id FROM `" . $DBTABLEPREFIX . "users` WHERE users_id=$msg_from_id";
					$result2 = mysql_query($sql2);
			
					for($i = 0; $i < mysql_num_rows($result2); $i++) {
					
							$row = mysql_fetch_array($result2);
							
							$page_content .= "\n    <td class='PMR1Column4'><a href='$menuvar[PROFILE]&action=viewprofile&id=$msg_from_id'>$row[users_username]</a></td>";
					}
					mysql_free_result($result2);
					
					$page_content .= "\n    <td class='PMR1Column5'>$msg_date</td>
										  </tr>";
			}
		}
		else { $page_content .= "\n<tr class='row1'><td width='100%' colspan='6'><center>$T_No_Msgs</center></td></tr>"; }
		mysql_free_result($result); //free our query
	
		
		$page_content .= "\n  <tr class='title1'>
							    <td class='VForumT1' colspan='6'><center><img src='images/msgread.gif' alt='' /> Read messages <img src='images/newmsg.gif' alt='' /> Unread messages </center></td>
							  </tr>
							  <tr class='title2'>
							    <td colspan='6'>
									<input type='submit' name='move' value='move' class='button'>
									<select name='pmactions' >
										<option value='inbox'>Inbox</option>
										<option value='saved'>Saved</option>
									</select>
									<input type='submit' name='delete' value='delete' class='button'>
							    </td>
							  </tr>
							</table>
							</td></tr>
							</table>
							 </form>
							 </center>
							<br /><br />";
	
	}

} //end username check to see if theyre logged in
else { $page_content .= "Please login to check your messages."; }

	function write_pmbox() {
		global $menuvar, $T_Menu, $T_Compose, $T_Inbox, $T_Saved_Msgs, $page_content;
		
		$page_content .= "\n<table class='PMNavForumBorder' border='0' cellpadding='0' cellspacing='1'>
							  <tr class='title1'>
							    <td class='VForumT1'>$T_Menu</td>	
							  </tr>
							  <tr class='title2'>
							    <td class='PMNavList'> 
							    	<ul>
							    		<li><a href='$menuvar[COMPOSEMSG]'>$T_Compose</a></li><br />
							    		<li><a href='$menuvar[PRIVMSGS]'>$T_Inbox</a></li>
							    		<li><a href='$menuvar[PRIVMSGS]&savedmsgs=1'>$T_Saved_Msgs</a></li>
							    	</ul>
							  </td></tr>
							  <tr class='title1'>
							    <td>&nbsp;</td>
							  </tr>
							</table>";	
	}
	

$page->setTemplateVar("PageContent", $page_content);
?>