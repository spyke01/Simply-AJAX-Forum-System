<?php 
/***************************************************************************
 *                               posts.php
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
	// Returns the last post in a forum or topic from an ID
	//=================================================
	function getLastPost($type, $ID) {
		global $DB, $menuvar, $safs_config;
		
		$lastPost = "";
		
		// Topic or Forum?
		$sql = ($type == "topic") ? "SELECT p.id, p.topic_id, p.user_id, u.username, p.datetimestamp FROM `" . DBTABLEPREFIX . "posts` p LEFT JOIN `" . DBTABLEPREFIX . "users` u ON u.id = p.user_id WHERE p.topic_id='" . $ID . "' ORDER BY p.datetimestamp DESC LIMIT 1" : "SELECT p.id, p.topic_id, p.user_id, u.username, p.datetimestamp FROM `" . DBTABLEPREFIX . "posts` p LEFT JOIN `" . DBTABLEPREFIX . "topics` t ON t.id = p.topic_id LEFT JOIN `" . DBTABLEPREFIX . "users` u ON u.id = p.user_id WHERE t.forum_id='" . $ID . "' ORDER BY p.datetimestamp DESC LIMIT 1";
		$result = $DB->query($sql);
				
		if ($result && $DB->num_rows() > 0) {
			while ($row = $DB->fetch_array($result)) {
				$lastPost = "<a href=\"" . $menuvar['VIEWTOPIC'] . "&id=" . $row['topic_id'] . "#" . $row['id'] . "\"><img src=\"themes/" . $safs_config['ftssafs_theme'] . "/buttons/lastpost.gif\" alt=\"\" /></a> <b>By:</b> <a href=\"" . $menuvar['VIEWPROFILE'] . "&id=" . $row['user_id'] . "\">" . $row['username'] . "</a><br /> <b>On:</b> " . makeOrderDateTime($row['datetimestamp']) . "";
			}
			$DB->free_result($result);
		}
		
		return $lastPost;
	}
	
 	//=================================================
	// Returns the datetimestamp of the last post in a 
	// forum or topic from an ID
	//=================================================
	function getLastPostDatetimestamp($type, $ID) {
		global $DB;
		
		$lastPostDatetimestamp = "";
		
		// Topic or Forum?
		$sql = ($type == "topic") ? "SELECT p.datetimestamp FROM `" . DBTABLEPREFIX . "posts` p LEFT JOIN `" . DBTABLEPREFIX . "users` u ON u.id = p.user_id WHERE p.topic_id='" . $ID . "' ORDER BY p.datetimestamp DESC LIMIT 1" : "SELECT p.datetimestamp FROM `" . DBTABLEPREFIX . "posts` p LEFT JOIN `" . DBTABLEPREFIX . "topics` t ON t.id = p.topic_id LEFT JOIN `" . DBTABLEPREFIX . "users` u ON u.id = p.user_id WHERE t.forum_id='" . $ID . "' ORDER BY p.datetimestamp DESC LIMIT 1";
		$result = $DB->query($sql);
				
		if ($result && $DB->num_rows() > 0) {
			while ($row = $DB->fetch_array($result)) {
				$lastPostDatetimestamp = $row['datetimestamp'];
			}
			$DB->free_result($result);
		}
		
		return $lastPostDatetimestamp;
	}

 	//=================================================
	// Print the New Post Form
	//=================================================
	function printNewPostForm($topicID) {
		global $DB, $menuvar, $safs_config;

		$returnVar = "
											<h2 class=\"newPostFormTitle\">New Post</h2>
											<div class=\"newPostBlock\">
												<form id=\"newPostForm\" action=\"" . $menuvar['VIEWTOPIC'] . "&id=" . $topicID . "\" method=\"post\">
													<fieldset>
														<h3>Post</h3>
														<div>
															" . bbcode_box() . "
														</div>
													</fieldset>
													<fieldset class=\"buttonRow\">
														<input type=\"submit\" name=\"submit\" value=\"Post Reply\" />
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
	function returnNewPostFormJQuery() {
		global $DB, $menuvar, $safs_config;
					
		$JQueryReadyScripts = "";
		
		return $JQueryReadyScripts;
	}

 	//=================================================
	// Process the New Post Form
	//=================================================
	function processNewPostForm($topicID, $data) {
		global $DB, $menuvar, $safs_config;
		
		// Dont escape since this method does that, double escape isn't fun
		$result = $DB->query_insert("posts", array('topic_id' => $topicID, 'user_id' => $_SESSION['userid'], 'text' => $data['message'], 'datetimestamp' => time()));
		
		if ($result) {
			// Update topic reply count
			$sql2 = "UPDATE `" . DBTABLEPREFIX . "topics` SET replies = replies + 1 WHERE id = '" . $topicID . "'";
			$result2 = $DB->query($sql2);
	
			// Update forum post count
			$sql2 = "UPDATE `" . DBTABLEPREFIX . "forums` SET posts = posts + 1 WHERE id = '" . getForumIDByTopicID($topicID) . "'";
			$result2 = $DB->query($sql2);
	
			// Update user post count
			$sql2 = "UPDATE `" . USERSDBTABLEPREFIX . "users` SET posts = posts + 1 WHERE id = '" . $_SESSION['userid'] . "'";
			$result2 = $DB->query($sql2);
		}
		
		$returnVar = "
											<h2 class=\"processFormTitle\">New Post</h2>
											<div class=\"processFormBlock " . (($result) ? "processFormSuccess" : "processFormFailed") . "\">
												" . (($result) ? "Your reply has been posted and you're being redirected to the thread." : "There was an error posting your reply, you're being redirected to the thread.") . "
												<meta http-equiv=\"refresh\" content=\"3;url=" . $menuvar['VIEWTOPIC'] . "&id=" . $topicID . "\">
											</div>";
		
		// Return the HTML
		return $returnVar;
	}

?>