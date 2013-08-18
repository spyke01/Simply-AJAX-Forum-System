<?php 
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
	// Print the Search Results Table
	//=================================================
	function printSearchResultsTable($searchFor, $userNames = "", $searchLocation = SEARCH_TITLE_AND_POST, $startDate = "", $endDate = "", $forumIDs = array(), $resultsType = SEARCH_TOPICS) {
		global $DB, $menuvar, $safs_config;
		
		// Create our variables and initialize them to black
		$sqlSelects = $extraTables = $extraWhere = $topicPostTableData = "";
		
		// Sometimes we will display multiple result blocks on a single page so generate a timestamp and use it for this instance
		$currentTime = time();
		
		// What fields do we want?
		$sqlSelects = ($resultsType == SEARCH_TOPICS) ? "DISTINCT t.*" : "t.*, p.id AS postID, p.user_id AS postUserID, p.datetimestamp AS postDatetimestamp, p.text AS postText";
		
		// Do we need any extra tables?
		//$extraTables .= () ? "" : "";
		
		// Do we need any extra where clauses?
		// Handle searching for items by certain users
		if (!empty($userNames)) {
			$userIDs = "";
			$doneOnce = 0;
			
			// Replace spaces with semicolons just in case domeone sperated names that way
			$usernames = str_replace(' ', ';', $userNames);
			// Replace commas with semicolons as thats what we will use
			$usernames = str_replace(',', ';', $userNames);
			// Replace spaces with semicolons as thats what we will use
			$usernames = str_replace(',', ';', $userNames);
			// Just in case we have multiple semicolons
			$usernames = preg_replace('/[;]+/', ';', $userNames);
			
			// Determine our actual users from their username
			foreach (explode(';', $usernames) as $key => $username) {
				// Trim, sanitize, and find the user id
				$userID = getUserIDFromUsername(keepsafe(trim($username)));
				
				// If this person is actually a user add them to our array
				$userIDs .= ($doneOnce) ? "," : "";
				$userIDs .= "'" . $userID . "'";
				$doneOnce = 1;
			}			
				
			// Add our list if we actually have some userIDs
			$extraWhere .= (!empty($userIDs)) ? " AND (t.user_id IN (" . $userIDs . ") OR p.user_id IN (" . $userIDs . "))" : "";
		}
		
		// Handle searching for items between certain date ranges
		$extraWhere .= (!empty($startDate)) ? "AND (t.datetimestamp >= '" . strtotime($startDate) . "' OR p.datetimestamp >= '" . strtotime($startDate) . "')" : "";
		$extraWhere .= (!empty($endDate)) ? " AND (t.datetimestamp <= '" . strtotime($endDate) . "' OR p.datetimestamp <= '" . strtotime($endDate) . "')" : "";
		
		// Handle searching for items in certain forums
		if (count($forumIDs) > 0) {
			$extraWhere .= " AND t.forum_id IN (";
			
			$doneOnce = 0;
				
			foreach ($forumIDs as $key => $id) {
				$extraWhere .= ($doneOnce) ? "," : "";
				$extraWhere .= "'" . keepsafe($id) . "'";
				$doneOnce = 1;
			}
		
			$extraWhere .= ")";
		}
	
		// Figure out if we are using pagination due to the number of items in this table
		// Get our result count
		$resultsCount = 0;
		$sql = "SELECT COUNT(t.id) AS count FROM `" . DBTABLEPREFIX . "posts` p LEFT JOIN `" . DBTABLEPREFIX . "topics` t ON t.id = p.topic_id" . $extraTables . " WHERE 1" . $extraWhere . " ORDER BY t.type DESC, t.id DESC" . $extraSQL;
		$result = $DB->query($sql);
		
		if ($result && $DB->num_rows() > 0) {
			while ($row = $DB->fetch_array($result)) {
				$resultsCount = $row['count'];
			}
			$DB->free_result($result);
		}
		
		// Get our pagination on
		$paginationArray = determinePagination($resultsCount, $actual_page);
		$extraSQL = $paginationArray['extraSQL'];
		$totalPages = $paginationArray['totalPages'];
		
		// Pull our topics and add them to the table
		$sql = "SELECT " . $sqlSelects . " FROM `" . DBTABLEPREFIX . "posts` p LEFT JOIN `" . DBTABLEPREFIX . "topics` t ON t.id = p.topic_id" . $extraTables . " WHERE 1" . $extraWhere . " ORDER BY t.type DESC, t.id DESC" . $extraSQL;
		$result = $DB->query($sql);

		// Create our new table
		$table = new tableClass(1, 1, 1, "contentBox tablesorter", "forumsTable");
		
		// Create table title
		$table->addNewRow(array(array("data" => "<div class=\"floatRight\"><a style=\"cursor: pointer; cursor: hand;\" onclick=\"ajaxToggleDiv('searchResultsBody" . $currentTime . "', 'searchResultsToggle" . $currentTime . "');\"><img src=\"themes/" . $safs_config['ftssafs_theme'] . "/icons/collapse.png\" alt=\"Collapse / Expand\" id=\"searchResultsToggle" . $currentTime . "\" /></a></div>Search Results", "colspan" => "7")), "", "title1", "thead");
		
		// Create column headers
		if ($resultsType == SEARCH_TOPICS) {
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
		}
		else {
			// Posts style
		}
							
		// Add our data
		if (!$result || $DB->num_rows() == 0) {
			$table->addNewRow(array(array("data" => "There are no search results.", "colspan" => "7")), "forumsTableDefaultRow", "greenRow");
		}
		else {
			$x = 1; // Reset our row counter
					
			// Pull our read topics information so we know what status icon to use
			$topicsRead = getTopicsReadArray($forumID);
			
			while ($row = $DB->fetch_array($result)) {
				if ($resultsType == SEARCH_TOPICS) {					
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
				}
				else {
					// Posts style
				}
						
				$x = ($x == 1) ? 2 : 1;
			}
			$DB->free_result($result);
		}
		
		// Return the HTML
		return $table->returnTableHTML("", "searchResultsBody" . $currentTime) . "
		
						<div id=\"paginationHolder\">
							" . ((!empty($extraSQL)) ? generatePagination($menuvar['SEARCH'] . "&", $actual_page, $totalPages) : "") . "
						</div>";
	}
	
	//=================================================
	// Returns the JQuery functions used to allow 
	// in-place editing and table sorting
	//=================================================
	function returnSearchResultsTableJQuery() {
		global $DB, $menuvar, $safs_config;
					
		$JQueryReadyScripts = "";
		
		return $JQueryReadyScripts;
	}

?>