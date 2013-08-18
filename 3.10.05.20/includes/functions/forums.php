<?php 
/***************************************************************************
 *                               forums.php
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
	// Returns number of topics in a forum from the ID
	//=================================================
	function getTopicCountByForumID($forumID) {
		global $DB;
		$numRows = 0;
		
		$sql = "SELECT COUNT(id) AS numRows FROM `" . DBTABLEPREFIX . "topics` WHERE forum_id='" . $forumID . "'";
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
	// Returns number of posts in a forum from the ID
	//=================================================
	function getPostCountByForumID($forumID) {
		global $DB;
		$numRows = 0;
		
		$sql = "SELECT COUNT(p.id) AS numRows FROM `" . DBTABLEPREFIX . "posts` p LEFT JOIN `" . DBTABLEPREFIX . "topics` t ON p.topic_id = t.id WHERE t.forum_id='" . $forumID . "'";
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
	// Returns Forum ID from the topic ID
	//=================================================
	function getForumIDByTopicID($topicID) {
		global $DB;
		$forumID = "";
		
		$sql = "SELECT forum_id FROM `" . DBTABLEPREFIX . "topics` WHERE id='" . $topicID . "' LIMIT 1";
		$result = $DB->query($sql);
				
		if ($result && $DB->num_rows() > 0) {
			while ($row = $DB->fetch_array($result)) {	
				$forumID = $row['forum_id'];
			}
			$DB->free_result($result);
		}
		
		return $forumID;
	}
	
 	//=================================================
	// Returns Forum Name from the ID
	//=================================================
	function getForumNameByID($forumID) {
		global $DB;
		$forumName = "";
		
		$sql = "SELECT name FROM `" . DBTABLEPREFIX . "forums` WHERE id='" . $forumID . "' LIMIT 1";
		$result = $DB->query($sql);
				
		if ($result && $DB->num_rows() > 0) {
			while ($row = $DB->fetch_array($result)) {	
				$forumName = $row['name'];
			}
			$DB->free_result($result);
		}
		
		return $forumName;
	}
	
 	//=================================================
	// Returns Forum Name from the topic ID
	//=================================================
	function getForumNameByTopicID($topicID) {
		global $DB;
		$forumName = "";
		
		$sql = "SELECT f.name FROM `" . DBTABLEPREFIX . "topics` t LEFT JOIN `" . DBTABLEPREFIX . "forums` f ON f.id = t.forum_id WHERE t.id='" . $topicID . "' LIMIT 1";
		$result = $DB->query($sql);
				
		if ($result && $DB->num_rows() > 0) {
			while ($row = $DB->fetch_array($result)) {	
				$forumName = $row['name'];
			}
			$DB->free_result($result);
		}
		
		return $forumName;
	}

 	//=================================================
	// Print the Category Forums Table
	//=================================================
	function printCategoryForumsTable() {
		global $DB, $menuvar, $safs_config;
		
		$forums = "";
		$sql = "SELECT * FROM `" . DBTABLEPREFIX . "categories` ORDER BY `order` ASC";
		$result = $DB->query($sql);
		
		// Cycle through our categories and print out their individual forums
		if ($result && $DB->num_rows() > 0) {
			while ($row = $DB->fetch_array($result)) {	
				$sql2 = "SELECT f.id, f.name, f.description, tr.unread FROM `" . DBTABLEPREFIX . "forums` f LEFT JOIN `" . DBTABLEPREFIX . "topics_read` tr ON tr.forum_id = f.id WHERE f.cat_id = '" . $row['id'] . "' ORDER BY f.order ASC";
				$result2 = $DB->query($sql2);
				
				// Create our new table
				$table = new tableClass(1, 1, 1, "contentBox tablesorter", "categoryForumsTable");
				
				// Create table title
				$table->addNewRow(array(array("data" => "<div class=\"floatRight\"><a style=\"cursor: pointer; cursor: hand;\" onclick=\"ajaxToggleDiv('categoryBody" . $row['id'] . "', 'categoryToggle" . $row['id'] . "');\"><img src=\"themes/" . $safs_config['ftssafs_theme'] . "/icons/collapse.png\" alt=\"Collapse / Expand\" id=\"categoryToggle" . $row['id'] . "\" /></a></div>" . $row['name'] . "", "colspan" => "5")), "", "title1", "thead");
				
				// Create column headers
				$table->addNewRow(
					array(
						array("type" => "th", "data" => ""),
						array("type" => "th", "data" => "Forum"),
						array("type" => "th", "data" => "Topics"),
						array("type" => "th", "data" => "Posts"),
						array("type" => "th", "data" => "Last Post")
					), "", "title2"
				); // This should be part of tbody not thead so that a collapse looks better
									
				// Add our data
				if (!$result2 || $DB->num_rows() == 0) {
					$table->addNewRow(array(array("data" => "There are no forums for this category.", "colspan" => "5")), "categoryForumsTableDefaultRow", "greenRow");
				}
				else {
					$x = 1; // Reset our row counter
					
					while ($row2 = $DB->fetch_array($result2)) {
						// Are there new posts in this forum?
						$forumIcon = ($row['unread'] == 0) ? "images/board_icons/forum.jpg" : "images/board_icons/forum_new.jpg";
					
						$table->addNewRow(
							array(
								array("data" => "<img src=\"" . $forumIcon . "\" alt=\"\" />", "class" => "center"),
								array("data" => "<a href=\"" . $menuvar['VIEWFORUM'] . "&id=" . $row['id'] . "\">" . $row2['name'] . "</a><br />" . $row2['description']),
								array("data" => getTopicCountByForumID($row2['id']), "class" => "center"),
								array("data" => getPostCountByForumID($row2['id']), "class" => "center"),
								array("data" => getLastPost("forum", $row['id']))
							), $row['id'] . "_row", "row" . $x
						);
						
						$x = ($x == 1) ? 2 : 1;
					}
					$DB->free_result($result2);
				}
				
				$forums .= (empty($forums)) ? "" : "<br />";
				$forums .= $table->returnTableHTML("", "categoryBody" . $row['id']);
				unset($table);
			}
			$DB->free_result($result);
		}
		
		// Return the HTML
		return $forums;
	}
	
	//=================================================
	// Returns the JQuery functions used to allow 
	// in-place editing and table sorting
	//=================================================
	function returnCategoryForumsTableJQuery() {
		global $DB, $menuvar, $safs_config;
					
		$JQueryReadyScripts = "";
		
		return $JQueryReadyScripts;
	}

 	//=================================================
	// Print the Forum Table
	//=================================================
	function printForumTable($forumID) {
		global $DB, $menuvar, $safs_config, $actual_page;

		// Create our new table
		$table = new tableClass(1, 1, 1, "contentBox tablesorter", "forumsTable");
		
		// Create table title
		$table->addNewRow(array(array("data" => "<div class=\"floatRight\"><a style=\"cursor: pointer; cursor: hand;\" onclick=\"ajaxToggleDiv('forumBody" . $forumID . "', 'forumToggle" . $forumID . "');\"><img src=\"themes/" . $safs_config['ftssafs_theme'] . "/icons/collapse.png\" alt=\"Collapse / Expand\" id=\"forumToggle" . $forumID . "\" /></a></div>" . getForumNameByID($forumID) . "", "colspan" => "7")), "", "title1", "thead");
		
		// Pull our subforums and add them to the table
		$sql = "SELECT f.id, f.name, f.description, tr.unread FROM `" . DBTABLEPREFIX . "forums` f LEFT JOIN `" . DBTABLEPREFIX . "topics_read` tr ON tr.forum_id = f.id WHERE f.parent_id = '" . $forumID . "' ORDER BY f.order ASC";
		$result = $DB->query($sql);
		
		// Create column headers
		$table->addNewRow(
			array(
				array("type" => "th", "data" => ""),
				array("type" => "th", "data" => "Subforums", "colspan" => "3"),
				array("type" => "th", "data" => "Topics"),
				array("type" => "th", "data" => "Posts"),
				array("type" => "th", "data" => "Last Post")
			), "", "title2"
		); // This should be part of tbody not thead so that a collapse looks better
							
		// Add our data
		if (!$result || $DB->num_rows() == 0) {
			$table->addNewRow(array(array("data" => "There are no subforums for this forum.", "colspan" => "7")), "forumsTableDefaultRow", "greenRow");
		}
		else {
			$x = 1; // Reset our row counter
					
			while ($row = $DB->fetch_array($result)) {
				// Are there new posts in this forum?
				$forumIcon = ($row['unread'] == 0) ? "images/board_icons/forum.jpg" : "images/board_icons/forum_new.jpg";
			
				$table->addNewRow(
					array(
						array("data" => "<img src=\"" . $forumIcon . "\" alt=\"\" />", "class" => "center"),
						array("data" => "<a href=\"" . $menuvar['VIEWFORUM'] . "&id=" . $row['id'] . "\">" . $row['name'] . "</a><br />" . $row['description'], "colspan" => "3"),
						array("data" => getTopicCountByForumID($row['id']), "class" => "center"),
						array("data" => getPostCountByForumID($row['id']), "class" => "center"),
						array("data" => getLastPost("forum", $row['id']))
					), $row['id'] . "_row", "row" . $x
				);
						
				$x = ($x == 1) ? 2 : 1;
			}
			$DB->free_result($result);
		}
	
		// Figure out if we are using pagination due to the number of items in this table
		$numOfTopics = getTopicCountByForumID($forumID);
		$paginationArray = determinePagination($numOfTopics, $actual_page);
		$extraSQL = $paginationArray['extraSQL'];
		$totalPages = $paginationArray['totalPages'];
	
		// Pull our topics and add them to the table
		$sql = "SELECT * FROM `" . DBTABLEPREFIX . "topics` WHERE (forum_id = '" . $forumID . "' OR type = " . POST_GLOBAL_ANNOUNCE . ") ORDER BY type DESC, id DESC" . $extraSQL;
		$result = $DB->query($sql);
		
		// Create column headers
		$table->addNewRow(
			array(
				array("type" => "th", "data" => ""),
				array("type" => "th", "data" => ""),
				array("type" => "th", "data" => "Topics"),
				array("type" => "th", "data" => "Poster"),
				array("type" => "th", "data" => "Views"),
				array("type" => "th", "data" => "Replies"),
				array("type" => "th", "data" => "Last Post")
			), "", "title2"
		); // This should be part of tbody not thead so that a collapse looks better
							
		// Add our data
		if (!$result || $DB->num_rows() == 0) {
			$table->addNewRow(array(array("data" => "There are no topics for this forum.", "colspan" => "7")), "forumsTableDefaultRow", "greenRow");
		}
		else {
			$x = 1; // Reset our row counter
					
			// Pull our read topics information so we know what status icon to use
			$topicsRead = getTopicsReadArray($forumID);
			
			while ($row = $DB->fetch_array($result)) {				
				// Pull our read topics information so we know what icons to use
				$topicRead = (isset($topicsRead[$row['id']]) && $topicsRead[$row['id']] >= getLastPostDatetimestamp("topic", $row['id'])) ? 1 : 0;
			
				$table->addNewRow(
					array(
						array("data" => "<img src=\"" . getTopicStatusIcon($topicRead, $row['type'], $row['status']) . "\" alt=\"\" />", "class" => "center"),
						array("data" => ((!empty($topicIcon)) ? "<img src=\"" . getTopicIconImageByID($row['topicicon_id']) . "\" alt=\"\" />" : ""), "class" => "center"),
						array("data" => "<a href=\"" . $menuvar['VIEWTOPIC'] . "&id=" . $row['id'] . "\">" . getTopicTypePrefix($row['type']) . runWordFilters($row['title']) . "</a>"),
						array("data" => getUsernameFromID($row['user_id']), "class" => "center"),
						array("data" => $row['views'], "class" => "center"),
						array("data" => (getPostCountByTopicID($row['id']) - 1), "class" => "center"),
						array("data" => getLastPost("topic", $row['id']))
					), $row['id'] . "_row", "row" . $x
				);
						
				$x = ($x == 1) ? 2 : 1;
			}
			$DB->free_result($result);
		}
		
		// Return the HTML
		return $table->returnTableHTML("", "forumBody" . $forumID) . "
		
						<div id=\"paginationHolder\">
							" . ((!empty($extraSQL)) ? generatePagination($menuvar['VIEWFORUM'] . "&id=" . $forumID, $actual_page, $totalPages) : "") . "
						</div>";
	}
	
	//=================================================
	// Returns the JQuery functions used to allow 
	// in-place editing and table sorting
	//=================================================
	function returnForumTableJQuery() {
		global $DB, $menuvar, $safs_config;
					
		$JQueryReadyScripts = "";
		
		return $JQueryReadyScripts;
	}

?>