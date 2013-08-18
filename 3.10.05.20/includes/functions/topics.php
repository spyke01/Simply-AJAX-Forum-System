<?php 
/***************************************************************************
 *                               topics.php
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
	// Returns number of posts in a forum from the ID
	//=================================================
	function getPostCountByTopicID($topicID) {
		global $DB, $menuvar, $safs_config;
		$numRows = 0;
		
		$sql = "SELECT COUNT(id) AS numRows FROM `" . DBTABLEPREFIX . "posts` WHERE topic_id='" . $topicID . "'";
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
	// Returns an array of read topics and the times
	//=================================================
	function getTopicsReadArray($forumID) {
		global $DB;
		
		$topicsRead = array();
		
		// Topic or Forum?
		$sql = "SELECT topic_ids FROM `" . DBTABLEPREFIX . "topics_read` WHERE user_id='" . $_SESSION['userid'] . "' AND forum_id='" . $forumID . "' LIMIT 1";
		$result = $DB->query($sql);
				
		if ($result && $DB->num_rows() > 0) {
			while ($row = $DB->fetch_array($result)) {
				$topicsRead = unserialize($row['topic_ids']);
			}
			$DB->free_result($result);
		}
		
		return $topicsRead;
	}
	
 	//=================================================
	// Mark the current forum as read
	//=================================================
	function markTopicRead($forumID, $topicID) {
		global $DB;
		
		// Pull our read topics information so we can work with it
		$topicsRead = getTopicsReadArray($forumID);
			
		// Kill our read row in the DB
		$sql = "DELETE FROM `" . DBTABLEPREFIX . "topics_read` WHERE user_id='" . $_SESSION['userid'] . "' AND forum_id='" . $forumID . "' LIMIT 1";
		$result = $DB->query($sql);
		
		// Set this topic read time to right now
		$topicsRead[$topicID] = time();
		
		// Clean out any bad topics (they were probably deleted
		$topicsRead = array_filter($topicsRead, "topicExists");
			
		// We disable this since we don't want to escape our topic_ids field
		// Dont escape since this method does that, double escape isn't fun
		//$result = $DB->query_insert("topics_read", array('user_id' => $_SESSION['userid'], 'forum_id' => $forumID, 'topic_ids' => serialize($topicsRead)));
		
		// Warp everything up and save it
		$sql = "INSERT INTO `" . DBTABLEPREFIX . "topics_read` (user_id, forum_id, topic_ids) VALUES ('" . $_SESSION['userid'] . "', '" . $forumID . "', '" . serialize($topicsRead) . "')";
		$result = $DB->query($sql);
	}
	
 	//=================================================
	// Mark the current forum as read
	//=================================================
	function topicExists($topicID) {
		global $DB;
		
		$exists = 0;
		
		// Topic or Forum?
		$sql = "SELECT id FROM `" . DBTABLEPREFIX . "topics` WHERE id='" . $_SESSION['userid'] . "' LIMIT 1";
		$result = $DB->query($sql);
				
		if ($result && $DB->num_rows() > 0) {
			// If theres a row then it exists
			$exists = 1;
			
			$DB->free_result($result);
		}
		
		return $exists;
	}
	
 	//=================================================
	// Returns Topic Name from the ID
	//=================================================
	function getTopicNameByID($topicID) {
		global $DB, $menuvar, $safs_config;
		$topicName = "";
		
		$sql = "SELECT name FROM `" . DBTABLEPREFIX . "topics` WHERE id='" . $topicID . "' LIMIT 1";
		$result = $DB->query($sql);
				
		if ($result && $DB->num_rows() > 0) {
			while ($row = $DB->fetch_array($result)) {	
				$topicName = $row['name'];
			}
			$DB->free_result($result);
		}
		
		return $topicName;
	}
	
 	//=================================================
	// Returns Topic Prefix based on it's type
	//=================================================
	function getTopicTypePrefix($topicType) {		
		$topicTypePrefix = ($topicType == POST_STICKY) ? "Stcky: " : "";
		$topicTypePrefix = ($topicType == POST_ANNOUNCE) ? "Announcement: " : $topicTypePrefix;
		$topicTypePrefix = ($topicType == POST_GLOBAL_ANNOUNCE) ? "Global Announcement: " : $topicTypePrefix;
		
		return $topicTypePrefix;
	}
	
 	//=================================================
	// Returns Topic Icon by checking a few variables
	//=================================================
	function getTopicStatusIcon($topicRead, $topicType, $topicStatus) {
		$topicStatusIcon = "";
		
		// What status icon should we use?
		if($_SESSION['username']) {
			// If we are logged in and the topic is new
			if(!$topicRead) {
				// New Posts
				$topicStatusIcon = ($topicStatus == TOPIC_LOCKED) ? "images/board_icons/locked_new.jpg" : "images/board_icons/post_new.jpg";
				$topicStatusIcon = ($topicType == POST_STICKY && $topicStatus != TOPIC_LOCKED) ? "images/board_icons/sticky_new.jpg" : $topicStatusIcon;
				$topicStatusIcon = (($topicType == POST_ANNOUNCE || $topicType == POST_GLOBAL_ANNOUNCE) && $topicStatus != TOPIC_LOCKED) ? "images/board_icons/announcement_new.jpg" : $topicStatusIcon;
			} 
			else  {
				// No new Posts
				$topicStatusIcon = ($topicStatus == TOPIC_LOCKED) ? "images/board_icons/locked.jpg" : "images/board_icons/post.jpg";
				$topicStatusIcon = ($topicType == POST_STICKY && $topic_status != TOPIC_LOCKED) ? "images/board_icons/sticky.jpg" : $topicStatusIcon;
				$topicStatusIcon = (($topicType == POST_ANNOUNCE || $topicType == POST_GLOBAL_ANNOUNCE) && $topicStatus != TOPIC_LOCKED) ? "images/board_icons/announcement.jpg" : $topicStatusIcon;
			}
		}
		else  {
			// Guest is viewing the forum
			$topicStatusIcon = ($topicStatus == TOPIC_LOCKED) ? "images/board_icons/locked.jpg" : "images/board_icons/post.jpg";
			$topicStatusIcon = ($topicType == POST_STICKY && $topicStatus != TOPIC_LOCKED) ? "images/board_icons/sticky.jpg" : $topicStatusIcon;
			$topicStatusIcon = (($topicType == POST_ANNOUNCE || $topicType == POST_GLOBAL_ANNOUNCE) && $topicStatus != TOPIC_LOCKED) ? "images/board_icons/announcement.jpg" : $topicStatusIcon;
		}
		
		return $topicStatusIcon;
	}

 	//=================================================
	// Print the Topic Post Table
	//=================================================
	function printTopicPostTable($topicID) {
		global $DB, $menuvar, $safs_config;
		
		$topicPostTableData = "";
		$sql = "SELECT * FROM `" . DBTABLEPREFIX . "topics` WHERE id='" . $topicID . "' LIMIT 1";
		$result = $DB->query($sql);
		
		// Cycle through our categories and print out their individual forums
		if ($result && $DB->num_rows() > 0) {
			while ($row = $DB->fetch_array($result)) {
				// Mark this topic read
				markTopicRead($row['forum_id'], $topicID);
			
				// Build our topic post skeleton
				$topicPostTableData = "
								<h2 class=\"topicTitle\">" . runWordFilters($row['title']) . "</h2>
								<div class=\"postsWrapper\">";
								
				$sql2 = "SELECT * FROM `" . DBTABLEPREFIX . "posts` WHERE topic_id='" . $topicID . "' ORDER BY `datetimestamp` ASC";
				$result2 = $DB->query($sql2);
				
				// Add our data
				if (!$result2 || $DB->num_rows() == 0) {
					$topicPostTableData .= "There are no posts for this topic.";
				}
				else {
					$x = 1; // Reset our row counter
					
					while ($row2 = $DB->fetch_array($result2)) {
						// Gather our user's signature settings
						$signatureArray = getUserSignatureArrayFromID($row2['user_id']);
						
						// Build the actual post section
						$topicPostTableData .= "
											<div id=\"" . $row2['id'] . "_row\" class=\"post\">
												<h3>
													" . getUserContactButtonBlockFromID($row2['user_id']) . "
													<a href=\"" . $menuvar['VIEWPROFILE'] . "&id=" . $row2['user_id'] . "\">" . getUsernameFromID($row2['user_id']) . "</a>
												</h3>
												<div class=\"author\">
													" . getUserInfoBlockFromID($row2['user_id']) . "
												</div>
												<div class=\"post_body\">
													<div class=\"info\">
														Posted: " . makeDateTime($row2['datetimestamp']) . "
													</div>
													<div class=\"content\">
														" . bbcode($row2['text']) . "
														" . (($signatureArray[0] == 1 && !empty($signatureArray[1])) ? "
														<div class=\"signature\">
															" . $signatureArray[1] . "
														</div>" : "") . "
													</div>
												</div>
												<ul class=\"managePost\">
													<li class=\"replyButton\"><a href=\"#reply\"><span>Reply</span></a></li>
													" . (($_SESSION['user_level'] == SYSTEM_ADMIN || $_SESSION['user_level'] == BOARD_ADMIN) ? "
													<li class=\"deleteButton\">" . createDeleteLink($row2['id'], $row2['id'] . "_row", "posts", "post", "Delete") . "</li>
													" : "") . "
												</ul>
											</div>";
					}
					$DB->free_result($result2);
				}
				$topicPostTableData .= "
								</div>";
			}								
			$DB->free_result($result);
		}
		
		// Return the HTML
		return $topicPostTableData;
	}
	
	//=================================================
	// Returns the JQuery functions used to allow 
	// in-place editing and table sorting
	//=================================================
	function returnTopicPostTableJQuery() {
		global $DB, $menuvar, $safs_config;
					
		$JQueryReadyScripts = "";
		
		return $JQueryReadyScripts;
	}

 	//=================================================
	// Print the New Topic Form
	//=================================================
	function printManageTopicBlock($topicID) {
		global $DB, $menuvar, $safs_config;

		$returnVar = "
							<div id=\"manageTopicBlock\">
								<span id=\"lockTopicMessage\"></span>
								<ul>
									<li>
										<form action=\"" . $menuvar['MOVETOPIC'] . "&id=" . $topicID . "\" method=\"post\">
											" . createDropdown("forums", "forum_id", getForumIDByTopicID($topicID)) . "
											<input type=\"submit\" name=\"submit\" value=\"Move Topic\" />
										</form>
									</li>
									<li class=\"lockButton\"><a href=\"\" onclick=\"lockTopic('lock', " . $topicID . "); return false;\"><span>Lock Topic</span></a></li>
									<li class=\"deleteButton\"><a href=\"" . $menuvar['DELETETOPIC'] . "&id=" . $topicID . "\"><span>Delete Topic</span></a></li>
								</ul>
							</div>";
		
		// Return the HTML
		return $returnVar;
	}
	
	//=================================================
	// Returns the JQuery functions used to allow 
	// in-place editing and table sorting
	//=================================================
	function returnManageTopicBlockJQuery() {
		global $DB, $menuvar, $safs_config;
					
		$JQueryReadyScripts = "";
		
		return $JQueryReadyScripts;
	}

 	//=================================================
	// Print the New Topic Form
	//=================================================
	function printNewTopicForm($forumID) {
		global $DB, $menuvar, $safs_config;

		$returnVar = "
											<h2 class=\"newTopicFormTitle\">New Topic</h2>
											<div class=\"newPostBlock\">
												<form id=\"newTopicForm\" action=\"" . $menuvar['VIEWFORUM'] . "&id=" . $forumID . "\" method=\"post\">
													<fieldset>
														<h3>Topic Information</h3>
														<ul>
															<li>
																<label for=\"title\">Topic Title:</label>
																<input type=\"text\" name=\"title\" size=\"60\" />
															</li>
														</ul>
													</fieldset>
													<fieldset>
														<h3>Topic</h3>
														<div>
															" . bbcode_box() . "
														</div>
													</fieldset>
													<fieldset class=\"optionsRow\">
														<h3>Topic Options</h3>
														<ul>
															<li>
																<label for=\"icon\">Topic Icon:</label>
																<ul>
																	<li><input type=\"radio\" name=\"icon\" value=\"\" checked />No Icon</li>";
		
		$sql = "SELECT * FROM `" . DBTABLEPREFIX . "topicicons`";
		$result = $DB->query($sql);
		
		// Add our data
		if ($result && $DB->num_rows() > 0) {
			while ($row = $DB->fetch_array($result)) {
				$returnVar .= "
																	<li><input type=\"radio\" name=\"icon\" value=\"" . $row['id'] . "\" /><img src=\"" . $row['image'] . "\" alt=\"\" /></li>";
			}
			$DB->free_result($result);
		}
		
		$returnVar .= "			
																</ul>
															</li>
															" . (($_SESSION['user_level'] == SYSTEM_ADMIN || $_SESSION['user_level'] == BOARD_ADMIN) ? "
															<li>
																<label for=\"type\">Topic Type</label>
																<ul>
																	<li><input type=\"radio\" name=\"type\" value=\"" . POST_NORMAL . "\" checked />Normal</li>
																	<li><input type=\"radio\" name=\"type\" value=\"" . POST_STICKY . "\" />Sticky</li>
																	<li><input type=\"radio\" name=\"type\" value=\"" . POST_ANNOUNCE . "\" />Announcement</li>
																	<li><input type=\"radio\" name=\"type\" value=\"" . POST_GLOBAL_ANNOUNCE . "\" />Global Announcement</li>
																</ul>
															</li>" : "") . "
														</ul>
													</fieldset>
													<fieldset class=\"buttonRow\">
														<input type=\"submit\" name=\"submit\" value=\"Create Topic\" />
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
	function returnNewTopicFormJQuery() {
		global $DB, $menuvar, $safs_config;
					
		$JQueryReadyScripts = "";
		
		return $JQueryReadyScripts;
	}

 	//=================================================
	// Process the New Topic Form
	//=================================================
	function processNewTopicForm($forumID, $data) {
		global $DB, $menuvar, $safs_config;
		
		// Dont escape since this method does that, double escape isn't fun
		// Insert Topic
		$insertArray = array('forum_id' => $forumID, 'user_id' => $_SESSION['userid'], 'topicicon_id' => $data['icon'], 'title' => $data['title'], 'datetimestamp' => time());
		if ($_SESSION['user_level'] == SYSTEM_ADMIN || $_SESSION['user_level'] == BOARD_ADMIN) $insertArray['type'] = $data['type'];
		
		$topicID = $DB->query_insert("topics", $insertArray);

		if ($topicID) {
			// Insert Post
			$result = $DB->query_insert("posts", array('topic_id' => $topicID, 'user_id' => $_SESSION['userid'], 'text' => $data['message'], 'datetimestamp' => time()));
	
			// Update forum post count
			$sql2 = "UPDATE `" . DBTABLEPREFIX . "forums` SET posts = posts + 1 WHERE id = '" . $forumID . "'";
			$result2 = $DB->query($sql2);
	
			// Update user post count
			$sql2 = "UPDATE `" . DBTABLEPREFIX . "users` SET posts = posts + 1 WHERE id = '" . $_SESSION['userid'] . "'";
			$result2 = $DB->query($sql2);
		}
		
		$returnVar = "
											<h2 class=\"processFormTitle\">New Topic</h2>
											<div class=\"processFormBlock " . (($topicID) ? "processFormSuccess" : "processFormFailed") . "\">
												" . (($topicID) ? "Your topic has been posted and you're being redirected to the thread." : "There was an error posting your topic, you're being redirected to the thread.") . "
												<meta http-equiv=\"refresh\" content=\"3;url=" . $menuvar['VIEWTOPIC'] . "&id=" . $topicID . "\">
											</div>";
		
		// Return the HTML
		return $returnVar;
	}

 	//=================================================
	// Process the Delete Topic Request
	//=================================================
	function processDeleteTopic($topicID) {
		global $DB, $menuvar, $safs_config;
		$numOfPosts = 0;
		$forumID = getForumIDByTopicID($topicID);
		
		// Pull the posts in this topic, delete them and update our counts
		$sql = "SELECT id, user_id FROM `" . DBTABLEPREFIX . "posts` WHERE topic_id = '" . $topicID . "'";
		$result = $DB->query($sql);
		
		if ($result && $DB->num_rows() > 0) {
			while ($row = $DB->fetch_array($result)) {
				// Delete the post
				$sql2 = "DELETE FROM `" . DBTABLEPREFIX . "posts` WHERE id = '" . $row['id'] . "'";
				$result2 = $DB->query($sql2);
				
				// Update user post count
				$sql2 = "UPDATE `" . USERSDBTABLEPREFIX . "users` SET posts = posts - 1 WHERE id = '" . $row['user_id'] . "'";
				$result2 = $DB->query($sql2);
				
				$numOfPosts++;
			}		
			$DB->free_result($result);
		}
		
		// Update forum post count
		$sql = "UPDATE `" . DBTABLEPREFIX . "forums` SET posts = posts - " . $numOfPosts . " WHERE id = '" . $forumID . "'";
		$result = $DB->query($sql);
		
		// Delete the topic
		$sql = "DELETE FROM `" . DBTABLEPREFIX . "topics` WHERE id = '" . $topicID . "'";
		$result = $DB->query($sql);
		
		$returnVar = "
											<h2 class=\"processFormTitle\">Delete Topic</h2>
											<div class=\"processFormBlock " . (($result) ? "processFormSuccess" : "processFormFailed") . "\">
												" . (($result) ? "Your topic has been deleted and you're being redirected to the forum." : "There was an error deleting your topic, you're being redirected to the forum.") . "
												<meta http-equiv=\"refresh\" content=\"3;url=" . $menuvar['VIEWFORUM'] . "&id=" . $forumID . "\">
											</div>";
		
		// Return the HTML
		return $returnVar;
	}

 	//=================================================
	// Process the Move Topic Request
	//=================================================
	function processMoveTopic($topicID, $data) {
		global $DB, $menuvar, $safs_config;
		$numOfPosts = 0;
		$forumID = getForumIDByTopicID($topicID);
		$newForumID = $data['forum_id'];
		
		// Pull the posts in this topic, delete them and update our counts
		$sql = "SELECT COUNT(id) AS numRows FROM `" . DBTABLEPREFIX . "posts` WHERE topic_id = '" . $topicID . "'";
		$result = $DB->query($sql);
		
		if ($result && $DB->num_rows() > 0) {
			$row = $DB->fetch_array($result);
			$numOfPosts = $row['numRows'];
			
			$DB->free_result($result);
		}
		
		// Update the current forum post count
		$sql = "UPDATE `" . DBTABLEPREFIX . "forums` SET posts = posts - " . $numOfPosts . " WHERE id = '" . $forumID . "'";
		$result = $DB->query($sql);
		
		// Update the new forum post count
		$sql = "UPDATE `" . DBTABLEPREFIX . "forums` SET posts = posts + " . $numOfPosts . " WHERE id = '" . keepsafe($newForumID) . "'";
		$result = $DB->query($sql);
		
		// Move the topic
		// Dont escape since this method does that, double escape isn't fun
		$result = $DB->query_update("topics", array('forum_id' => $newForumID), "id = '" . $topicID . "'");
		
		$returnVar = "
											<h2 class=\"processFormTitle\">Move Topic</h2>
											<div class=\"processFormBlock " . (($result) ? "processFormSuccess" : "processFormFailed") . "\">
												" . (($result) ? "Your topic has been moved and you're being redirected to the thread." : "There was an error moving your topic, you're being redirected to the thread.") . "
												<meta http-equiv=\"refresh\" content=\"3;url=" . $menuvar['VIEWTOPIC'] . "&id=" . $topicID . "\">
											</div>";
		
		// Return the HTML
		return $returnVar;
	}

?>