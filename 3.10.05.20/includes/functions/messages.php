<?php 
/***************************************************************************
 *                               messages.php
 *                            -------------------
 *   begin                : Saturday, Sept 24, 2005
 *   copyright            : (C) 2005 Paden Clayton - Fast Track Sites
 *   email                : sales@fasttacksites.com
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
	
 	//=================================================
	// Returns number of messages in a forum from the ID
	//=================================================
	function getMessageCountByFolder($folder = "") {
		global $DB, $safs_config;
		$numRows = 0;
		
		// Build our SQL query
		$extraSQL = ($folder == "sent") ? "WHERE from_id = '" . $_SESSION['userid'] . "'" : "WHERE to_id = '" . $_SESSION['userid'] . "'";
		$extraSQL .= (empty($folder)) ? " AND folder = '" . MSG_IN_INBOX . "'" : "";
		$extraSQL .= ($folder == "archive") ? " AND folder = '" . MSG_IN_ARCHIVE . "'" : "";
		
		// Make sure we aren't going over our message limit
		$extraSQL = ($folder == "sent") ? " LIMIT " . $safs_config['ftssafs_max_sent_privmsgs'] : "";
		$extraSQL .= (empty($folder)) ? " LIMIT " . $safs_config['ftssafs_max_inbox_privmsgs'] : "";
		$extraSQL .= ($folder == "archive") ? " LIMIT " . $safs_config['ftssafs_max_archived_privmsgs'] : "";
		
		
		$sql = "SELECT COUNT(id) AS numRows FROM `" . DBTABLEPREFIX . "messages` " . $extraSQL;
		$result = $DB->query($sql);
				
		if ($result && $DB->num_rows() > 0) {
			while ($row = $DB->fetch_array($result)) {	
				$numRows = $row['numRows'];
			}
			$DB->free_result($result);
		}
		
		return $numRows;
	}
	
 	//=================================================
	// Print the Message Table
	//=================================================
	function printMessageTable($folder = "") {
		global $DB, $menuvar, $safs_config, $actual_page;

		// Determine what folder we are in
		$folderName = (empty($folder)) ? "Messages" : "Archived Messages";
		$folderName = ($folder == "sent") ? "Sent Messages" : $folderName;
		
		// Determine what page link we are using
		$pageLink = (empty($folder)) ? $menuvar['INBOX'] : $menuvar['ARCHIVEDMESSAGES'];
		$pageLink = ($folder == "sent") ? $menuvar['SENTMESSAGES'] : $pageLink;
		
		// Determine whether to use the archive button, unarchive button, or neither
		$archiveButton = (empty($folder)) ? "<input type=\"submit\" name=\"archive\" value=\"Archive\" class=\"archiveButton\">" : "<input type=\"submit\" name=\"unarchive\" value=\"Unarchive\" class=\"unarchiveButton\"> ";
		$archiveButton = ($folder == "sent") ? "" : $archiveButton;
		
		// Build our SQL query
		$extraSQL = ($folder == "sent") ? "WHERE from_id = '" . $_SESSION['userid'] . "'" : "WHERE to_id = '" . $_SESSION['userid'] . "'";
		$extraSQL .= (empty($folder)) ? " AND folder = '" . MSG_IN_INBOX . "'" : "";
		$extraSQL .= ($folder == "archive") ? " AND folder = '" . MSG_IN_ARCHIVE . "'" : "";
		
		// Create our new table
		$table = new tableClass(1, 1, 1, "contentBox tablesorter", "messagesTable");
		
		// Create table title
		$table->addNewRow(array(array("data" => "<div class=\"floatRight\"><a style=\"cursor: pointer; cursor: hand;\" onclick=\"ajaxToggleDiv('messageBody', 'messageToggle');\"><img src=\"themes/" . $safs_config['ftssafs_theme'] . "/icons/collapse.png\" alt=\"Collapse / Expand\" id=\"messageToggle\" /></a></div>" . $folderName . "", "colspan" => "7")), "", "title1", "thead");
		
		// Figure out if we are using pagination due to the number of items in this table
		$numOfMessages = getMessageCountByFolder($folder);
		$paginationArray = determinePagination($numOfMessages, $actual_page);
		$limitSQL = $paginationArray['extraSQL'];
		$totalPages = $paginationArray['totalPages'];
		
		// Pull our submessages and add them to the table
		$sql = "SELECT * FROM `" . DBTABLEPREFIX . "messages` " . $extraSQL . " ORDER BY `datetimestamp` DESC" . $limitSQL;
		$result = $DB->query($sql);
		
		// Create column headers
		$table->addNewRow(
			array(
				array("type" => "th", "data" => "<input name=\"checkAll\" id=\"checkAll\" type=\"checkbox\" value=\"Check All\" onclick=\"checkAll(this);\" />"),
				array("type" => "th", "data" => ""),
				array("type" => "th", "data" => "Sender"),
				array("type" => "th", "data" => "Message"),
				array("type" => "th", "data" => "Date")
			), "", "title2"
		); // This should be part of tbody not thead so that a collapse looks better
							
		// Add our data
		if (!$result || $DB->num_rows() == 0) {
			$table->addNewRow(array(array("data" => "You have no messages.", "colspan" => "7")), "messagesTableDefaultRow", "greenRow");
		}
		else {
			$x = 1; // Reset our row counter
					
			while ($row = $DB->fetch_array($result)) {
				// Unread Message
				$messageStatusIcon = ($row['status'] == MSG_READ) ? "images/board_icons/message.gif" : "images/board_icons/message_new.gif";
						
				$table->addNewRow(
					array(
						array("data" => "<input type=\"checkbox\" name=\"ChgPM[]\" value=\"" . $row['id'] . "\" onclick=\"checkCheckAll();\" />", "class" => "center"),
						array("data" => "<img src=\"" . $messageStatusIcon . "\" alt=\"\" />", "class" => "center"),
						array("data" => "<a href=\"" . $menuvar['VIEWPROFILE'] . "&id=" . $row['from_id'] . "\">" . getUsernameFromID($row['from_id']) . "</a>"),
						array("data" => "<a href=\"" . $menuvar['VIEWMESSAGE'] . "&id=" . $row['id'] . "\">" . runWordFilters($row['title']) . "</a>"),
						array("data" => makeShortDateTime($row['datetimestamp']), "class" => "center")
					), $row['id'] . "_row", "row" . $x
				);
						
				$x = ($x == 1) ? 2 : 1;
			}
			$DB->free_result($result);
		}
		
		// Create our controls
		$table->addNewRow(
			array(
				array("type" => "th", "data" => $archiveButton . "<input type=\"submit\" name=\"delete\" value=\"Delete\" class=\"deleteButton\">", "colspan" => "7")
			), "", "title2"
		);
		
		// Return the HTML
		return "
						<form name=\"manageMessagesForm\" id=\"manageMessagesForm\" action=\"" . $pageLink . "\" method=\"post\">
							" . $table->returnTableHTML("", "messageBody") . "
						</form>
						<div id=\"paginationHolder\">
							" . ((!empty($limitSQL)) ? generatePagination($pageLink, $actual_page, $totalPages) : "") . "
						</div>";
	}
	
	//=================================================
	// Returns the JQuery functions used to allow 
	// in-place editing and table sorting
	//=================================================
	function returnMessageTableJQuery() {
		global $DB, $menuvar, $safs_config;
					
		$JQueryReadyScripts = "";
		
		return $JQueryReadyScripts;
	}

 	//=================================================
	// Process the Manage Messages
	//=================================================
	function processManageMessages($data) {
		global $DB, $menuvar, $safs_config, $actual_action;
		$result;
		$redirectTo = "";
		
		// What folder are we coming from?
		$folderName = ($actual_action == "viewArchive") ? "message archive" : "inbox";
		$folderName = ($actual_action == "viewSent") ? "sent messages" : $folderName;
		
		// Where should we redirect to?
		$redirectTo = ($actual_action == "viewArchive") ? $menuvar['ARCHIVEDMESSAGES'] : $menuvar['INBOX'];
		$redirectTo = ($actual_action == "viewSent") ? $menuvar['SENTMESSAGES'] : $redirectTo;
		
		// Loop through out messages
		foreach ($data['ChgPM'] as $key => $id) {
			if (isset($data['delete'])) {
				// Handle Deletes
				$sql = "DELETE FROM `" . DBTABLEPREFIX . "messages` WHERE id = '" . keepsafe($id) . "'";
				$result = $DB->query($sql);
				
				// Set our message to display
				$message = ($result) ? "Your message(s) have been deleted and you're being redirected to your " . $folderName . "." : "There was an error deleting your message(s), you're being redirected to your " . $folderName . ".";
			}
			else {
				// We are dealing with moving messages
				$folder = (isset($data['archive'])) ? MSG_IN_ARCHIVE : MSG_IN_INBOX;
				$newFolderName = (isset($data['archive'])) ? "message archive" : "inbox";
				$result = $DB->query_update("messages", array('folder' => $folder), "id = '" . keeptasafe($id) . "'");
				
				// Set our message to display
				$message = ($result) ? "Your message has been moved to the " . $newFolderName . " and you're being redirected to your " . $folderName . "." : "There was an error moving your message to the " . newFolderName . ", you're being redirected to your " . $folderName . ".";
			}
		}
		
		$returnVar = "
											<h2 class=\"processFormTitle\">New Message</h2>
											<div class=\"processFormBlock " . (($result) ? "processFormSuccess" : "processFormFailed") . "\">
												" . $message . "
												<meta http-equiv=\"refresh\" content=\"3;url=" . $redirectTo . "\">
											</div>";
		
		// Return the HTML
		return $returnVar;
	}

 	//=================================================
	// Print the View Message Table
	//=================================================
	function printViewMessageTable($messageID) {
		global $DB, $menuvar, $safs_config;
		
		$messagePostTableData = "";
		$sql = "SELECT * FROM `" . DBTABLEPREFIX . "messages` WHERE id='" . $messageID . "' LIMIT 1";
		$result = $DB->query($sql);

		// Add our data
		if (!$result || $DB->num_rows() == 0) {
			$messagePostTableData .= "There are no messages for this message.";
		}
		else {
			$x = 1; // Reset our row counter
							
			while ($row = $DB->fetch_array($result)) {
				// Mark this message as read
				// Dont escape since this method does that, double escape isn't fun
				$result = $DB->query_update("messages", array('status' => MSG_READ), "id='" . $messageID . "'");
			
				// Gather our user's signature settings
				$signatureArray = getUserSignatureArrayFromID($row['from_id']);
				
				// Build the actual message section
				$messagePostTableData .= "
								<h2 class=\"messageTitle\">" . runWordFilters($row['title']) . "</h2>
								<div class=\"messagesWrapper\">
									<div class=\"message\">
										<h3>
											<ul class=\"contactBlock\">
												<li>PM</li>
												<li>IM</li>
											</ul>
											<a href=\"" . $menuvar['VIEWPROFILE'] . "&id=" . $row['from_id'] . "\">" . getUsernameFromID($row['from_id']) . "</a>
										</h3>
										<div class=\"author\">
											" . getUserInfoBlockFromID($row['from_id']) . "
										</div>
										<div class=\"message_body\">
											<div class=\"info\">
												Posted: " . makeDateTime($row['datetimestamp']) . "
											</div>
											<div class=\"content\">
												" . bbcode($row['message']) . "
												" . (($signatureArray[0] == 1 && !empty($signatureArray[1])) ? "
												<div class=\"signature\">
													" . getUserSignatureArrayFromID($row['from_id']) . "
												</div>" : "") . "
											</div>
										</div>
										<ul class=\"manageMessage\">
											<li class=\"replyButton\"><a href=\"#reply\"><span>Reply</span></a></li>
											<li class=\"deleteButton\">" . createDeleteLink($row['id'], $row['id'] . "_row", "messages", "message", "Delete") . "</li>
										</ul>
									</div>
								</div>";
			}
			$DB->free_result($result2);
		}
		
		// Return the HTML
		return $messagePostTableData;
	}
	
	//=================================================
	// Returns the JQuery functions used to allow 
	// in-place editing and table sorting
	//=================================================
	function returnViewMessageTableJQuery() {
		global $DB, $menuvar, $safs_config;
					
		$JQueryReadyScripts = "";
		
		return $JQueryReadyScripts;
	}
	
	//=================================================
	// Print the New Message Form
	//=================================================
	function printNewMessageForm($userID) {
		global $DB, $menuvar, $safs_config;

		$returnVar = "
											<h2 class=\"newMessageFormTitle\">New Message</h2>
											<div class=\"newMessageBlock\">
												<form id=\"newMessageForm\" action=\"" . $menuvar['COMPOSE'] . "\" method=\"post\">
													<fieldset>
														<h3>Message Information</h3>
														<ul>
															<li>
																<label for=\"to\">To:</label>
																<input type=\"text\" name=\"to\" id=\"to\" size=\"60\" value=\"" . ((!empty($userID)) ? getUsernameFromID($userID) : "") . "\" />
															</li>
															<li>
																<label for=\"subject\">Subject:</label>
																<input type=\"text\" name=\"subject\" id=\"subject\" size=\"60\" />
															</li>
														</ul>
													</fieldset>
													<fieldset>
														<h3>Message</h3>
														<div>
															" . bbcode_box() . "
														</div>
													</fieldset>
													<fieldset class=\"buttonRow\">
														<input type=\"submit\" name=\"submit\" value=\"Send Message\" />
														<input type=\"button\" name=\"clear\" value=\"Clear Form\" />
													</fieldset>
												</form>
											</div>";
		
		// Return the HTML
		return $returnVar;
	}
	
	//=================================================
	// Returns the JQuery functions used to allow 
	// in-place editing and table sorting
	//=================================================
	function returnNewMessageFormJQuery() {
		global $DB, $menuvar, $safs_config;
					
		$JQueryReadyScripts = "";
		
		return $JQueryReadyScripts;
	}

 	//=================================================
	// Process the New Message Form
	//=================================================
	function processNewMessageForm($data) {
		global $DB, $menuvar, $safs_config;
		$userIDs = array();
		
		// Replace commas with semicolons as thats what we will use
		$usernames = str_replace(',', ';', $data['to']);
		
		// Determine our actual users from their username
		foreach (explode(';', $usernames) as $key => $username) {
			// Trim, sanitize, and find the user id
			$userID = getUserIDFromUsername(keepsafe(trim($username)));
			
			// If this person is actually a user add them to our array
			if ($userID) array_push($userIDs, $userID);
		}
		
		// Loop through our intended recepients
		foreach ($userIDs as $key => $userID) {
			// Insert Message(s)
			// Dont escape since this method does that, double escape isn't fun
			$result = $DB->query_insert("messages", array('to_id' => $userID, 'from_id' => $_SESSION['userid'], 'title' => $data['subject'], 'message' => $data['message'], 'datetimestamp' => time()));
		}
		
		$returnVar = "
											<h2 class=\"processFormTitle\">New Message</h2>
											<div class=\"processFormBlock " . (($result) ? "processFormSuccess" : "processFormFailed") . "\">
												" . (($result) ? "Your message has been sent and you're being redirected to your inbox." : "There was an error sending your message, you're being redirected to your inbox.") . "
												<meta http-equiv=\"refresh\" content=\"3;url=" . $menuvar['INBOX'] . "\">
											</div>";
		
		// Return the HTML
		return $returnVar;
	}
	
	//=================================================
	// Print the Reply Message Form
	//=================================================
	function printReplyMessageForm($messageID) {
		global $DB, $menuvar, $safs_config;

		$returnVar = "
											<h2 class=\"newMessageFormTitle\">Reply</h2>
											<div class=\"newMessageBlock\">
												<form id=\"newMessageForm\" action=\"" . $menuvar['VIEWMESSAGE'] . "&id=" . $messageID . "\" method=\"post\">
													<input type=\"hidden\" name=\"id\" size=\"60\" value=\"" . $messageID . "\" />
													<fieldset>
														<h3>Message</h3>
														<div>
															" . bbcode_box() . "
														</div>
													</fieldset>
													<fieldset class=\"buttonRow\">
														<input type=\"submit\" name=\"submit\" value=\"Send Reply\" />
														<input type=\"button\" name=\"clear\" value=\"Clear Form\" />
													</fieldset>
												</form>
											</div>";
		
		// Return the HTML
		return $returnVar;
	}
	
	//=================================================
	// Returns the JQuery functions used to allow 
	// in-place editing and table sorting
	//=================================================
	function returnReplyMessageFormJQuery() {
		global $DB, $menuvar, $safs_config;
					
		$JQueryReadyScripts = "";
		
		return $JQueryReadyScripts;
	}

 	//=================================================
	// Process the New Topic Form
	//=================================================
	function processNewReplyMessageForm($messageID, $data) {
		global $DB, $menuvar, $safs_config;
		$userID = 0;
		$title = "";
		$result = false;
				
		$sql = "SELECT from_id, title FROM `" . DBTABLEPREFIX . "messages` WHERE id = '" . $messageID . "'";
		$result = $DB->query($sql);
				
		if ($result && $DB->num_rows() > 0) {
			while ($row = $DB->fetch_array($result)) {	
				$userID = $row['from_id'];
				$title = $row['title'];
			}
			$DB->free_result($result);
		}
		
		// If we haven't already added Re: then add it
		if (substr($title, 0, 3) != "Re:") $title = "Re: " . $title;
		
		// Insert Message
		// Dont escape since this method does that, double escape isn't fun
		if ($userID != 0) $result = $DB->query_insert("messages", array('to_id' => $userID, 'from_id' => $_SESSION['userid'], 'title' => $title, 'message' => $data['message'], 'datetimestamp' => time()));
		
		$returnVar = "
											<h2 class=\"processFormTitle\">New Reply Message</h2>
											<div class=\"processFormBlock " . (($result) ? "processFormSuccess" : "processFormFailed") . "\">
												" . (($result) ? "Your reply has been sent and you're being redirected to the message." : "There was an error sending your reply, you're being redirected to the message.") . "
												<meta http-equiv=\"refresh\" content=\"3;url=" . $menuvar['VIEWMESSAGE'] . "&id=" . $messageID . "\">
											</div>";
		
		// Return the HTML
		return $returnVar;
	}

?>