<?php 
/***************************************************************************
 *                               general.php
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
 
	//==================================================
	// Strips Dangerous tags out of input boxes 
	//==================================================
	function keepsafe($makesafe) {
		global $DB;
	
		$makesafe = strip_tags($makesafe); // strip away any dangerous tags
		$makesafe = str_replace(" ","",$makesafe); // remove spaces from variables
		$makesafe = str_replace("%20","",$makesafe); // remove escaped spaces
		$makesafe = trim(preg_replace('/[^\x09\x0A\x0D\x20-\x7F]/e', '"&#".ord($0).";"', $makesafe)); //encodes all ascii items above #127
	
		// Prepare for the DB
		$makesafe = $DB->escape($makesafe);
		
	    return $makesafe;
	}
	
	//==================================================
	// Strips Dangerous tags out of textareas 
	//==================================================
	function keeptasafe($makesafe) {
		global $DB;
	
		$makesafe = trim(preg_replace('/[^\x09\x0A\x0D\x20-\x7F]/e', '"&#".ord($0).";"', $makesafe)); //encodes all ascii items above #127
		
		// Prepare for the DB
		$makesafe = $DB->escape($makesafe);
		
	    return $makesafe;
	}
	
	//==================================================
	// Strips Dangerous tags out of get and post values
	//==================================================
	function parseurl($makesafe) {
		global $DB;
	
		$makesafe = strip_tags($makesafe); // strip away any dangerous tags
		$makesafe = str_replace(" ","",$makesafe); // remove spaces from variables
		$makesafe = str_replace("%20","",$makesafe); // remove escaped spaces
		$makesafe = trim(preg_replace('/[^\x09\x0A\x0D\x20-\x7F]/e', '"&#".ord($0).";"', $makesafe)); //encodes all ascii items above #127
	
		// Prepare for the DB
		$makesafe = $DB->escape($makesafe);
		
	    return $makesafe;
	}
	
	//==================================================
	// Creates a date from a timestamp
	//==================================================
	function makeDate($time) {
		global $safs_config;
		
		$date = @gmdate('l F d, Y', $time + (3600 * $safs_config['ftssafs_time_zone'])); // Makes date in the format of: Thursday July 05, 2006
		return $date;
	}
	
	function makeTime($time) {
		global $safs_config;
		
		$date = @gmdate('g:i A', $time + (3600 * $safs_config['ftssafs_time_zone'])); // Makes date in the format of: 3:30 PM
		return $date;
	}
	
	function makeDateTime($time) {
		global $safs_config;
		
		$date = @gmdate('l F d, Y - g:i A', $time + (3600 * $safs_config['ftssafs_time_zone'])); // Makes date in the format of: Thursday July 5, 2006 - 3:30 pm
		return $date;
	}
	
	function makeOrderDateTime($time) {
		global $safs_config;
		
		$date = @gmdate('M d, Y - g:i A', $time + (3600 * $safs_config['ftssafs_time_zone'])); // Makes date in the format of: Jul 5, 2006 - 3:30 pm
		return $date;
	}
	
	function makeShortDate($time) {
		global $safs_config;
		
		$date = (empty($time)) ? "" : @gmdate('m/d/Y', $time + (3600 * $safs_config['ftssafs_time_zone'])); // Makes date in the format of: 07/05/2006
		return $date;
	}
	
	function makeShortDateTime($time) {
		global $safs_config;
		
		$date = (empty($time)) ? "" : @gmdate('m/d/Y g:i A', $time + (3600 * $safs_config['ftssafs_time_zone'])); // Makes date in the format of: 07/05/2006 - 3:30 pm
		return $date;
	}
	
	function makeCurrentYear($time) {
		global $safs_config;
		
		$date = (empty($time)) ? "" : @gmdate('Y', $time + (3600 * $safs_config['ftssafs_time_zone'])); // Makes date in the format of: 2006
		return $date;
	}
	
	function makeXYearsFromCurrentYear($time, $numOfYears) {
		global $safs_config;
		
		$date = (empty($time)) ? "" : @gmdate('Y', $time + (3600 * $safs_config['ftssafs_time_zone'])) + $numOfYears; // Makes date in the format of: 2026
		return $date;
	}
	
	function makeYear($time) {
		global $safs_config;
		
		$date = @gmdate('Y', $time + (3600 * $safs_config['ftssafs_time_zone'])); // Makes date in the format of: 2006
		return $date;
	}
	
	function makeMonth($time) {
		global $safs_config;
		
		$date = @gmdate('M', $time + (3600 * $safs_config['ftssafs_time_zone'])); // Makes date in the format of: Jul
		return $date;
	}
	
	function makeShortMonth($time) {
		global $safs_config;
		
		$date = @gmdate('m', $time + (3600 * $safs_config['ftssafs_time_zone'])); // Makes date in the format of: 05
		return $date;
	}
	
	function makeXMonthsFromCurrentMonthAsTimestamp($numOfMonths) {
		$currentTime = time();
		$currentMonth = makeShortMonth($currentTime);
		$currentYear = makeYear($currentTime);
		
		// Increase month count
		for ($i = 0; $i < $numOfMonths; $i++) {
			// Handle Dec
			$currentMonth = ($currentMonth == "12") ? 1 : ($currentMonth + 1);
			$currentYear = ($currentMonth == "12") ? ($currentYear + 1) : $currentYear;
		}
		
		$timestamp = strtotime($currentMonth . "/01/" . $currentYear);
		return $timestamp;
	}
	
	function makeDay($time) {
		global $safs_config;
		
		$date = @gmdate('d', $time + (3600 * $safs_config['ftssafs_time_zone'])); // Makes date in the format of: Jul
		return $date;
	}
	
	//==================================================
	// Replacement for die()
	// Used to display msgs without displaying the board
	//==================================================
	function message_die($msg_text = '', $msg_title = '') {
		echo "<html>\n<body>\n" . $msg_title . "\n<br /><br />\n" . $msg_text . "</body>\n</html>";
		include('includes/footer.php');
		exit;
	}
	
	//=========================================================
	// nl2br replacement for ajax calls since newlines are escaped
	//=========================================================
	function ajaxnl2br($string) {
		return str_replace(array("\\r\\n", "\\r", "\\n"), "<br />", $string);
	}
	
	//=========================================================
	// Check if this item should be selected
	//=========================================================
	function testSelected($testFor, $testAgainst) {
		if ($testFor == $testAgainst) { return " selected=\"selected\""; }
	}
	
	//=========================================================
	// Check if this item should be checked
	//=========================================================
	function testChecked($testFor, $testAgainst) {
		if ($testFor == $testAgainst) { return " checked=\"checked\""; }
	}
	
	//=========================================================
	// Outputs Yes or No
	//=========================================================
	function returnYesNo($value) {
		if ($value == 1 || $value == true) { return "Yes"; }
		else { return "No"; }
	}
	
	//=========================================================
	// Returns the system's selected currency symbol
	//=========================================================
	function returnCurrencySymbol() {
		global $safs_config;
		
		return $safs_config['ftssafs_currency_type'];
	}
	
	//=========================================================
	// Returns the proper http or https depending on the system setting
	//=========================================================
	function returnHttpLinks($input) {
		global $safs_config;
		
		$output = ($safs_config['ftsss_use_https'] == 1) ? str_replace("http://", "https://", $input) : str_replace("https://", "http://", $input);
		
		return $output;
	}
	
	//=========================================================
	// Padds a string to a certain length
	//=========================================================
	function paddString($var, $desiredLength, $paddingValue, $sideToPadd) {
		$padding = "";
			
		if (strlen($var) == $desiredLength) {
			return $var;
		}
		elseif (strlen($var) > $desiredLength) {
			// If we are padding the left then we will grab the right most pieces
			if ($sideToPadd == "L") { return substr($var, 0, -$desiredLength); }
			// If we are padding the right then we will grab the left most pieces
			else { return substr($var, 0, $desiredLength); }		
		}
		else {
			$spacesToPadd = $desiredLength - strlen($var);
			
			for ($i = 0; $i < $spacesToPadd; $i++) {
				$padding .= $paddingValue;
			}
			
			if ($sideToPadd == "L") { return $padding . $var; }
			else { return $var . $padding; }
		}
	}
	
	//=========================================================
	// Puts items into money format
	//=========================================================
	function formatCurrency($value) {
		// All non numeric values should be turned into 0
		if (!is_numeric($value)) $value = 0;
		
		return returnCurrencySymbol() . number_format($value, 2, '.', ',');
	}
	
	//=========================================================
	// Takes the change off of a number without rounding it up
	//=========================================================
	function stripChange($value) {
		$returnVar = "";
				
		if (is_numeric($value)) { 
			// If we have multiple periods then we will output all but the last one
			$explodedValue = explode(".", $value);
			
			if (count($explodedValue) > 1) {
				for ($x = 0; $x < count($explodedValue) - 1; $x++) {
					$returnVar = (empty($returnVar)) ? $explodedValue[$x] :  "." . $explodedValue[$x];
				}
			}
			else { $returnVar = $explodedValue[0]; }
		}
		else { $returnVar = $value; }
		
		return $returnVar;
	}
	
	//=========================================================
	// Returns the HTML code for our delete links
	//=========================================================
	function createDeleteLink($DBTableRowID, $rowName, $DBTableName, $typeName, $linkText) {
		global $safs_config;
	
		return "<a style=\"cursor: pointer; cursor: hand;\" onclick=\"ajaxDeleteNotifier('" . $DBTableRowID . $DBTableName . "Spinner', 'ajax.php?action=deleteitem&table=" . $DBTableName . "&id=" . $DBTableRowID . "', '" . $typeName . "', '" . $rowName . "');\"><span>" . $linkText . "</span></a><span id=\"" . $DBTableRowID . $DBTableName . "Spinner\" style=\"display: none;\">" . progressSpinnerHTML() . "</span>";
	}
	
	//=========================================================
	// Returns the HTML code for our delete links
	//=========================================================
	function createDeleteLinkWithImage($DBTableRowID, $rowName, $DBTableName, $typeName) {
		global $safs_config;
	
		return "<a style=\"cursor: pointer; cursor: hand;\" onclick=\"ajaxDeleteNotifier('" . $DBTableRowID . $DBTableName . "Spinner', 'ajax.php?action=deleteitem&table=" . $DBTableName . "&id=" . $DBTableRowID . "', '" . $typeName . "', '" . $rowName . "');\"><img src=\"themes/" . $safs_config['ftssafs_theme'] . "/icons/delete.png\" alt=\"Delete " . ucfirst($typeName) . "\" /></a><span id=\"" . $DBTableRowID . $DBTableName . "Spinner\" style=\"display: none;\">" . progressSpinnerHTML() . "</span>";
	}
	
	//=========================================================
	// Returns the HTML code for our spinner
	//=========================================================
	function progressSpinnerHTML() {
		global $safs_config;
	
		return "<img src=\"themes/" . $safs_config['ftssafs_theme'] . "/icons/indicator.gif\" alt=\"spinner\" />";
	}
	
	//=========================================================
	// Returns the HTML code for a table update notice
	//=========================================================
	function tableUpdateNoticeHTML() {	
		return "<div class=\"updateNotice\">A new row has been added to this table, inline editing for this new row will be disabled until the next page refresh.</div>";
	}
	
	//=========================================================
	// Returns the JQUERY code for our edit in-place
	//=========================================================
	function returnEditInPlaceJQuery($DBTableRowID, $DBTableFieldName, $DBTableName, $inputtype = "", $extraOptions = "") {
		$inputtypeJQuery = (!empty($inputtype)) ? "type      : '" . $inputtype . "'," : "";
		$extraOptions = (!empty($extraOptions)) ? $extraOptions . "," : "";
		
		return "
					     $('#" . $DBTableRowID . "_" . $DBTableFieldName . "').addClass('editableItemHolder').editable('ajax.php?action=updateitem&table=" . $DBTableName . "&item=" . $DBTableFieldName . "&id=" . $DBTableRowID . "', { 
					         " . $inputtypeJQuery . "
							 " . $extraOptions . "
							 cancel    : 'Cancel',
					         submit    : 'OK',
					         indicator : '" . progressSpinnerHTML() . "',
					         tooltip   : 'Click to edit...'
					     });";
	}
	
	//=========================================================
	// Create a dropdown without the need for repeating code
	//=========================================================
	function createDropdown($type, $inputName, $currentSelection = "", $onChange = "", $class = "") {
		global $DB, $safs_config;
		
		$onChangeVar = (empty($onChange)) ? "" : " onChange=\"" . $onChange . "\"";
		$classVar = (empty($class)) ? "" : " class=\"" . $class . "\"";
		
		$dropdown = "<select name=\"" . $inputName . "\" id=\"" . $inputName . "\"" . $classVar . "" . $onChangeVar . ">
						<option value=\"\">--Select One--</option>";
		if ($type == "avatarGalleries") {
			$avatarGallerFolders = array();
			if($dir = opendir($safs_config['ftssafs_avatar_gallery_path'])){
				while (false !== ($file = readdir($dir))) {				
					if ($file != "." && $file != ".." && is_dir($safs_config['ftssafs_avatar_gallery_path'] . '/' . $file)) {
						$avatarGallerFolders[$file] .= '';	
					}
				}
			}
			ksort($avatarGallerFolders); //sort by name
			
			foreach($avatarGallerFolders as $name => $nothing) {
				$dropdown .= "<option value=\"" . $name . "\">" . $name . "</option>";
			}
		}
		if ($type == "categories") {
			$sql = "SELECT id, name FROM `" . DBTABLEPREFIX . "categories` ORDER BY name";
			$result = $DB->query($sql);
			
			if ($result && $DB->num_rows() > 0) {
				while ($row = $DB->fetch_array($result)) {
					$dropdown .= "<option value=\"" . $row['id'] . "\"" . testSelected($row['id'], $currentSelection) . ">" . $row['name'] . "</option>";
				}
				$DB->free_result($result);
			}
		}
		if ($type == "clients") {
			$sql = "SELECT id, first_name, last_name FROM `" . DBTABLEPREFIX . "clients` ORDER BY last_name";
			$result = $DB->query($sql);
			
			if ($result && $DB->num_rows() > 0) {
				while ($row = $DB->fetch_array($result)) {
					$dropdown .= "<option value=\"" . $row['id'] . "\"" . testSelected($row['id'], $currentSelection) . ">" . $row['last_name'] . ", " . $row['first_name'] . "</option>";
				}
				$DB->free_result($result);
			}
		}
		if ($type == "currencies") {
			global $FTS_CURRENCIES;
			
			foreach($FTS_CURRENCIES as $key => $value) {
				$dropdown .= "<option value=\"" . $key . "\"" . testSelected($key, $currentSelection) . ">" . $value . "</option>";
			}
		}
		if ($type == "countries") {
			global $FTS_COUNTRIES;
		
			foreach($FTS_COUNTRIES as $key => $value) {
				$dropdown .= "<option value=\"" . $key . "\"" . testSelected($key, $currentSelection) . ">" . $value . "</option>";
			}
		}
		if ($type == "daterange") {
			$itemArray = array('today' => "Today", 'thisWeek' => "This Week", 'thisMonth' => "This Month", 'thisYear' => "This Year", 'allTime' => "Alltime", 'custom' => "Custom Date Range");
			
			foreach ($itemArray as $key => $value) {
				$dropdown .= "
						<option value=\"" . $key . "\"" . testSelected($currentSelection, $key) . ">" . $value . "</option>";
			}
		}
		if ($type == "forums") {		
			$sql = "SELECT id, name FROM `" . DBTABLEPREFIX . "categories` ORDER BY `order` ASC";
			$result = $DB->query($sql);
		
			if ($result && $DB->num_rows() > 0) {
				while ($row = $DB->fetch_array($result)) {
					$dropdown .= "<optgroup label=\"" . $row['name'] . "\">";
				 	$dropdown .= forumListRecursor($row['id'], $currentSelection);
					$dropdown .= "</optgroup>\n";
				}
				$DB->free_result($result);
			}
		}
		if ($type == "genders") {
			$dropdown .= "
						<option value=\"" . GENDER_FEMALE . "\"" . testSelected($currentSelection, GENDER_FEMALE) . ">Female</option>
						<option value=\"" . GENDER_MALE . "\"" . testSelected($currentSelection, GENDER_MALE) . ">Male</option>";
		}
		if ($type == "graphs") {
			$itemArray = array('invoicedVsPaid' => "Invoiced vs Paid", 'totalPayments' => "Total Payments", 'totalProfit' => "Total Profit");
			
			foreach ($itemArray as $key => $value) {
				$dropdown .= "
						<option value=\"" . $key . "\"" . testSelected($currentSelection, $key) . ">" . $value . "</option>";
			}
		}
		if ($type == "graphtypes") {
			$itemArray = array('area2d' => "Area (2D)", 'bar2d' => "Bar (2D)", 'column' => "Column", 'column2d' => "Column (2D)", 'doughnut2d' => "Doughnut (2D)", 'funnel' => "Funnel", 'line' => "Line", 'pie' => "Pie", 'pie2d' => "Pie (2D)");
			
			foreach ($itemArray as $key => $value) {
				$dropdown .= "
						<option value=\"" . $key . "\"" . testSelected($currentSelection, $key) . ">" . $value . "</option>";
			}
		}
		if ($type == "itemsPerPage") {
			global $FTS_ITEMS_PER_PAGE;
		
			foreach($FTS_ITEMS_PER_PAGE as $key => $value) {
				$dropdown .= "<option value=\"" . $value . "\"" . testSelected($value, $currentSelection) . ">" . $value . "</option>";
			}
		}
		if ($type == "messageFolders") {
			$dropdown .= "
						<option value=\"" . MSG_IN_INBOX . "\"" . testSelected($currentSelection, MSG_IN_INBOX) . ">Inbox</option>
						<option value=\"" . MSG_IN_ARCHIVE . "\"" . testSelected($currentSelection, MSG_IN_ARCHIVE) . ">Archive</option>";
		}
		if ($type == "paymenttypes") {
			global $FTS_PAYMENTTYPES;
		
			foreach($FTS_PAYMENTTYPES as $key => $value) {
				$dropdown .= "<option value=\"" . $key . "\"" . testSelected($key, $currentSelection) . ">" . $value . "</option>";
			}
		}
		if ($type == "products") {
			$sql = "SELECT id, name FROM `" . DBTABLEPREFIX . "products` ORDER BY name ASC";
			$result = $DB->query($sql);
			
			if ($result && $DB->num_rows() > 0) {
				while ($row = $DB->fetch_array($result)) {
					$dropdown .= "<option value=\"" . $row['id'] . "\"" . testSelected($row['id'], $currentSelection) . ">" . $row['name'] . "</option>";
				}
				$DB->free_result($result);
			}
		}
		if ($type == "productswithprice") {
			$sql = "SELECT id, name, price, profit, shipping FROM `" . DBTABLEPREFIX . "products` ORDER BY name ASC";
			$result = $DB->query($sql);
			
			if ($result && $DB->num_rows() > 0) {
				while ($row = $DB->fetch_array($result)) {
					$dropdown .= "<option value=\"" . $row['id'] . "\"" . testSelected($row['id'], $currentSelection) . ">" . $row['name'] . " - " . formatCurrency($row['price'] + $row['profit'] + $row['shipping']) . "</option>";
				}
				$DB->free_result($result);
			}
		}
		if ($type == "timezone") {
			global $FTS_TIMEZONES;
		
			foreach($FTS_TIMEZONES as $key => $value) {
				$dropdown .= "<option value=\"" . $key . "\"" . testSelected($key, $currentSelection) . ">" . $value . "</option>";
			}
		}
		if ($type == "urgency") {
			$dropdown .= "
						<option value=\"" . LOW . "\"" . testSelected($currentSelection, LOW) . ">Low</option>
						<option value=\"" . MEDIUM . "\"" . testSelected($currentSelection, MEDIUM) . ">Medium</option>
						<option value=\"" . HIGH . "\"" . testSelected($currentSelection, HIGH) . ">High</option>";
		}
		if ($type == "users") {
			$sql = "SELECT id, email_address, first_name, last_name FROM `" . USERSDBTABLEPREFIX . "users` ORDER BY last_name";
			$result = $DB->query($sql);
			
			if ($result && $DB->num_rows() > 0) {
				while ($row = $DB->fetch_array($result)) {
					$dropdown .= "<option value=\"" . $row['id'] . "\"" . testSelected($row['id'], $currentSelection) . ">" . $row['last_name'] . ", " . $row['first_name'] . " (" . $row['email_address'] . ")</option>";
				}
				$DB->free_result($result);
			}
		}
		if ($type == "userlevel") {
			$dropdown .= "
						<option value=\"" . BANNED . "\"" . testSelected($currentSelection, BANNED) . ">Banned</option>
						<option value=\"" . USER . "\"" . testSelected($currentSelection, USER) . ">User</option>
						<option value=\"" . BOARD_ADMIN . "\"" . testSelected($currentSelection, BOARD_ADMIN) . ">Client Administrator</option>
						<option value=\"" . SYSTEM_ADMIN . "\"" . testSelected($currentSelection, SYSTEM_ADMIN) . ">System Administrator</option>";
		}
		$dropdown .= "</select>";	
		
		return $dropdown;	
	}
	
	//=========================================================
	// Used to build our forum list by recursivley going 
	// through forums and their child forums
	//=========================================================
	function forumListRecursor($catID, $currentSelection, $parentID = 0, $level = 0) {
		global $DB;
		$returnVar = "";
		
		$sql = "SELECT id, name FROM `" . DBTABLEPREFIX . "forums` WHERE parent_id = '" . $parentID . "' AND cat_id = '" . $catID . "' ORDER BY `order`";
		$result = $DB->query($sql);
	
		if ($result && $DB->num_rows() > 0) {
			while ($row = $DB->fetch_array($result)) {
			 	$indent = str_repeat("&nbsp;&nbsp;", $level+1); 
				$returnVar .= "<option value=\"" . $row['id'] . "\"" . testSelected($row['id'], $currentSelection) . ">" .$indent . "&#0124;--- " . $row['name'] . "</option>";
				$returnVar .= forumListRecursor($catID, $currentSelection, $row['id'], $level+1);
			}
			$DB->free_result($result);
		}
		
		return $returnVar;
	}
	
	//=========================================================
	// Case insensitive str_replace
	//=========================================================
	if(!function_exists('str_ireplace')){
	   function str_ireplace($search, $replace, $subject){
	       if(is_array($search)){
	           array_walk($search, 'make_pattern');
	       }
	       else{
	           $search = '/'.preg_quote($search, '/').'/i';
	       }
	       return preg_replace($search, $replace, $subject);
	   }
	} 
	
	//=========================================================
	// Returns icon legend block
	//=========================================================
	function returnIconLegendBlock() {
		global $safs_config;
		
		$iconLegendBlock = "
							<div id=\"iconLegendBlock\">
								<dl>
									<dt><img src=\"images/board_icons/post_new.jpg\" alt=\"New Replies\" /></dt>
									<dd>New Replies</dd>
									<dt><img src=\"images/board_icons/post.jpg\" alt=\"No New Replies\" /></dt>
									<dd>No New Replies</dd>
									<dt class=\"clear\"><img src=\"images/board_icons/poll_new.jpg\" alt=\"Poll (New)\" /></dt>
									<dd>Poll (New)</dd>
									<dt><img src=\"images/board_icons/poll.jpg\" alt=\"Poll\" /></dt>
									<dd>Poll</dd>
									<dt class=\"clear\"><img src=\"images/board_icons/announcement_new.jpg\" alt=\"Announcement (New)\" /></dt>
									<dd>Announcement (New)</dd>
									<dt><img src=\"images/board_icons/announcement.jpg\" alt=\"Announcement\" /></dt>
									<dd>Announcement</dd>
									<dt class=\"clear\"><img src=\"images/board_icons/sticky_new.jpg\" alt=\"Sticky (New)\" /></dt>
									<dd>Sticky (New)</dd>
									<dt><img src=\"images/board_icons/sticky.jpg\" alt=\"Sticky\" /></dt>
									<dd>Sticky</dd>
									<dt class=\"clear\"><img src=\"images/board_icons/locked_new.jpg\" alt=\"Locked Topic (New)\" /></dt>
									<dd>Locked Topic (New)</dd>
									<dt><img src=\"images/board_icons/locked.jpg\" alt=\"Locked Topic\" /></dt>
									<dd>Locked Topic</dd>
								</dl>
							</div>";
		
		return $iconLegendBlock;
	}
	
	//=========================================================
	// Returns announcement block
	//=========================================================
	function returnAnnouncementBlock() {
		global $safs_config;
		
		$announcementBlock = (!empty($safs_config['ftssafs_announcement_title']) || !empty($safs_config['ftssafs_announcement_text'])) ? "
							<div id=\"announcementBlock\">
								<h2>
									<div class=\"floatRight\"><a style=\"cursor: pointer; cursor: hand;\" onclick=\"ajaxToggleDiv('announcementBody', 'announcementToggle');\"><img src=\"themes/" . $safs_config['ftssafs_theme'] . "/icons/collapse.png\" alt=\"Collapse / Expand\" id=\"announcementToggle\" /></a></div>
									" . $safs_config['ftssafs_announcement_title'] . "
								</h2>
								<div id=\"announcementBody\">
									" .  $safs_config['ftssafs_announcement_text'] . "
								</div>
							</div>" : "";
		
		return $announcementBlock;
	}

	//============================================
	// This function is designed to let us show 
	// whos viewing the same page we call this 
	// function from, it was going to be in the 
	// usersonline.php file, but the n it will be 
	// below the actual topics because of the 
	// fast reply form
	//
	// USAGE:
	// returnUsersViewingThisPageBlock($PHP_SELF);
	//
	// This will show all users viewing the same 
	// page as you are.
	//============================================
	function returnBoardInformationBlock() {
		global $DB, $menuvar, $safs_config;
		
		// Initialize our variables
		$totalUsersOnline = $usersOnline = $guestsOnline = $numOfUsers = $numOfPosts = 0;
		$users = $newestUser = "";
	
		//==========================================================
		// Find total amount of users
		//==========================================================
		$sql = "SELECT COUNT(id) AS numOfUsers FROM `" . USERSDBTABLEPREFIX . "users`";
		$result = $DB->query($sql);
		
		// Add our data
		if ($result && $DB->num_rows() > 0) {
			while ($row = $DB->fetch_array($result)) {
				$numOfUsers = $row['numOfUsers'];
			}
			$DB->free_result($result);
		}
		
		//==========================================================
		// Find total amount of posts
		//==========================================================
		$sql = "SELECT COUNT(id) AS numOfPosts FROM `" . DBTABLEPREFIX . "posts`";
		$result = $DB->query($sql);
		
		// Add our data
		if ($result && $DB->num_rows() > 0) {
			while ($row = $DB->fetch_array($result)) {
				$numOfPosts = $row['numOfPosts'];
			}
			$DB->free_result($result);
		}
				
		//==========================================================
		// Find out whos online and viewing this page and if 
		// they're a user make a link to their profile page	
		//==========================================================
		$sql = "SELECT user_id, username FROM `" . DBTABLEPREFIX . "usersonline`";
		$result = $DB->query($sql);
		
		// Add our data
		if (!$result || $DB->num_rows() == 0) {
			$users = "<li>No one is on the board!</li>";
		}
		else {
			// Calculate our number of users and guests
			$totalUsersOnline = $DB->num_rows();
		
			while ($row = $DB->fetch_array($result)) {
				$totalusers++;
				$users .= "<li><a href=\"" . $menuvar['VIEWPROFILE'] . "&id=" . $row['user_id'] . "\">" . $row['username'] . "</a></li>";
			}
			$DB->free_result($result);
			
			$totalguests = $totalonline - $totalusers;
		}
		
		$sql = "SELECT id, username FROM `" . USERSDBTABLEPREFIX . "users` ORDER BY id DESC LIMIT 1";
		$result = $DB->query($sql);
		
		// Add our data
		if ($result && $DB->num_rows() > 0) {		
			while ($row = $DB->fetch_array($result)) {
				$newestUser = "<a href=\"" . $menuvar['VIEWPROFILE'] . "&id=" . $row['id'] . "\">" . $row['username'] . "</a>";
			}
			$DB->free_result($result);
		}
		
			
		//==========================================================
		// Print out our nice little div	
		//==========================================================
		$block = "
			<h2 class=\"boardInformationBlockTitle\">
				<div class=\"floatRight\"><a style=\"cursor: pointer; cursor: hand;\" onclick=\"ajaxToggleDiv('boardInformationBlock', 'boardInformationToggle');\"><img src=\"themes/" . $safs_config['ftssafs_theme'] . "/icons/collapse.png\" alt=\"Collapse / Expand\" id=\"boardInformationToggle\" /></a></div>
				Board Information
			</h2>
			<div id=\"boardInformationBlock\">
				<h3>User Statistics</h3>
				<p>
					<span class=\"totalUsersOnline\">" . $totalUsersOnline . " Users Online.</span>
					<span class=\"usersOnline\">" . $usersOnline . " Users</span>
					<span class=\"guestsOnline\">" . $guestsOnline . "  Guests</span>
					<ul class=\"users\">" . $users . "</ul>
				</p>
				<h3>Board Statistics</h3>
				<p>
					<span class=\"totalUsers\">We have a total of " . $numOfUsers . " users.</span>
					<span class=\"newestUser\">Our newest user is: " . $newestUser . ".</span>						
					<span class=\"numOfPosts\">Our users have made a total of " . $numOfPosts . " posts.</span>
				</p>
			</div>";
			
		return $block;
	}
	
	//=========================================================
	// Do we need pagination?
	//=========================================================
	function determinePagination($numOfItems, $currentPage) {
		global $safs_config;
		$returnVar = array();
		$returnVar['extraSQL'] = "";
		$returnVar['totalPages'] = "";
		
		$usePagination = ($numOfItems <= $safs_config['ftssafs_items_per_page']) ? 0 : 1;
		
		// Handle our pagination SQL building function
		if ($usePagination) {
			// Make sure to take care of 0 values
			$currentPage = ($currentPage == 0) ? 1 : $currentPage;
			
			// Determine which record to start and stop at based on page number
			$returnVar['totalPages'] = $numOfItems / $safs_config['ftssafs_items_per_page'];
					
			// Decimal places signify that another $page is needed
			$returnVar['totalPages'] = ($returnVar['totalPages'] > stripChange($returnVar['totalPages'])) ? stripChange($returnVar['totalPages']) + 1 : $returnVar['totalPages'];
			
			// Calculate our starting row
			$startAt = ($currentPage == 1) ? 0 : $safs_config['ftssafs_items_per_page'] * ($currentPage - 1);
					
			// Calculate our ending row
			$stopAt = (empty($currentPage) || $currentPage == 1) ? $safs_config['ftssafs_items_per_page'] : $startAt + $safs_config['ftssafs_items_per_page'];
			
			// Create our SQL piece
			$returnVar['extraSQL'] = " LIMIT " . $startAt . ", " . $stopAt;
		}
		
		return $returnVar;
	}
	
	//=========================================================
	// Generates a pagination set
	//=========================================================
	function generatePagination($linkURL, $currentPage, $totalPages) {
		global $safs_config;
		$returnVar = "";
		$numOfFlankingLinks = 5;
		
		// Make sure to take care of 0 values
		$currentPage = (empty($currentPage) || $currentPage == 0) ? 1 : $currentPage;
		$totalPages = (empty($totalPages) || $totalPages == 0) ? 1 : $totalPages;
		$prevPage = ($currentPage == 1) ? 1 : $currentPage - 1;
		$nextPage = ($currentPage == $totalPages) ? $totalPages : $currentPage + 1;
		
		// We shouldn't print any pagination items if there is only one page
		if ($totalPages > 1) {
			// Start our block
			$returnVar = "
							<ul class=\"pagination\">
								" . (($currentPage != 1) ? "<li class=\"prev\"><a href=\"" . $linkURL . "&page=" . $prevPage . "\">Prev</a></li>" : "") . "
								" . (($currentPage > 1) ? "			<li><a href=\"" . $linkURL . "&page=1\">1</a></li>" : "") . "
								" . (($currentPage - $numOfFlankingLinks > 2) ? "			<li>...</li>" : "");

			// Create Links Prior to current page
			for ($x = $numOfFlankingLinks; $x >= 1; $x--) {
				$prevPageNum = $currentPage - $x;
				$returnVar .= ($prevPageNum > 1) ? "\n								<li><a href=\"" . $linkURL . "&page=" . $prevPageNum . "\">" . $prevPageNum . "</a></li>" : "";
			}
			
			// Create Links to the current page
			$returnVar .= "\n								<li class=\"current\">" . $currentPage . "</li>";
				
			// Create Links After the current page
			for ($x = 1; $x <= $numOfFlankingLinks; $x++) {
				$nextPageNum = $currentPage + $x;
				$returnVar .= ($nextPageNum < $totalPages) ? "\n								<li><a href=\"" . $linkURL . "&page=" . $nextPageNum . "\">" . $nextPageNum . "</a></li>" : "";
			}
							
			// End our block
			$returnVar .= "
								" . (($currentPage + $numOfFlankingLinks < $totalPages - 1) ? "<li>...</li>" : "") . "
								" . (($totalPages > 1 && $currentPage != $totalPages) ? "<li><a href=\"" . $linkURL . "&page=" . $totalPages . "\">" . $totalPages . "</a></li>" : "") . "
								" . (($currentPage < $totalPages) ? "<li class=\"next\"><a href=\"" . $linkURL . "&page=" . $nextPage . "\">Next</a></li>" : "") . "
							</ul>";
		}
		
		return $returnVar;
	}
	
	//=========================================================
	// Returns company info block
	//=========================================================
	function returnCompanyInfoBlock() {
		global $safs_config;
		
		$companyInfoBlock = "";
		$companyInfoBlock .= (!empty($safs_config['ftssafs_invoice_company_name'])) ? $safs_config['ftssafs_invoice_company_name'] . "<br />" : "";
		$companyInfoBlock .= (!empty($safs_config['ftssafs_invoice_address'])) ? nl2br($safs_config['ftssafs_invoice_address']) . "<br />" : "";
		$companyInfoBlock .= (!empty($safs_config['ftssafs_invoice_city'])) ? $safs_config['ftssafs_invoice_city'] . ", " . $safs_config['ftssafs_invoice_state'] . " " . $safs_config['ftssafs_invoice_zip'] . "<br />" : "";
		$companyInfoBlock .= (!empty($safs_config['ftssafs_invoice_phone_number'])) ? "Phone: " . $safs_config['ftssafs_invoice_phone_number'] . "<br />" : "";
		$companyInfoBlock .= (!empty($safs_config['ftssafs_invoice_fax'])) ? "Fax: " . $safs_config['ftssafs_invoice_fax'] . "<br />" : "";
		$companyInfoBlock .= (!empty($safs_config['ftssafs_invoice_email_address'])) ? "Email: " . $safs_config['ftssafs_invoice_email_address'] . "<br />" : "";
		$companyInfoBlock .= (!empty($safs_config['ftssafs_invoice_website'])) ? "Website: " . $safs_config['ftssafs_invoice_website'] . "<br />" : "";
			
		return $companyInfoBlock;
	}
	
	//=========================================================
	// Returns a header for emails
	//=========================================================
	function returnEmailHeader() {
		global $safs_config;
		
		return "<img src=\"" . returnHttpLinks($safs_config['ftsss_store_url']) . "/images/logo.png\" alt=\"" . $safs_config['ftsss_store_name'] . "\" /><br />
			Phone: " . $safs_config['ftsss_phone_number'] . "<br />
			Fax: " . $safs_config['ftsss_fax'] . "<br />
			Website: " . returnHttpLinks($safs_config['ftsss_store_url']) . "<br /><br />";
	}	
	
	//=========================================================
	// Sends an email message using the supplied values
	//=========================================================
	function emailMessage($emailAddress, $subject, $message) {
		global $safs_config;
		
		$headers = "";
		
		// Additional headers
		//$headers .= "To: " . $emailAddress . "\n";
		$headers .= "From: " . $safs_config['ftssafs_invoice_email_address'] . "\n";
		
		// To send HTML mail, the content-type header must be set
		$headers .= "MIME-Version: 1.0" . "\n";
		$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
		
		// Mail it
		$emailResult = mail($emailAddress, $subject, $message, $headers);
		
		if ($emailResult) {
			return 1;
		}
		else {
			return 0;
		}
	}

	//=========================================================
	// Allows us to get any remote file we need with post vars
	//=========================================================	
	function returnRemoteFilePost($host, $directory, $filename, $urlVariablesArray = array()) {
		$result = "";
	
		$urlVariables = array();    
		foreach($urlVariablesArray as $key=>$value) {
	        $urlVariables[] = $key . "=" . urlencode($value);
	    }  
		$urlVariables = implode('&', $urlVariables);

		//open connection
		$ch = curl_init();
		
		//set the url, number of POST vars, POST data
		curl_setopt($ch, CURLOPT_URL, "http://" . $host . "/" . $directory . "/" . $filename);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $urlVariables);
		
		//execute post
		$result = curl_exec($ch);
		$info = curl_getinfo($ch);
		
		//close connection
		curl_close($ch);		
		
		return $result;
	}
	
	//==================================================
	// This function will notify user of updates and
	// other important information
	//
	// USAGE:
	// version_functions();
	// 
	// Removal or hinderance is a direct violation of 
	// the program license and is constituted as a 
	// breach of contract as is punishable by law.
	//
	// MODIFIED TO REMOVE CALLHOME AND VERSION CHECK
	//==================================================
	function version_functions($print_update_info) {
		include('_license.php');
		
		//=========================================================
		// Get all of the variables we need to pass to the 
		// call home script ready
		//=========================================================
		
			
		//=========================================================
		// Should we display advanced option?
		// Connection to the FTS server has to be made or the 
		// options will not be shown
		//=========================================================
		if ($print_update_info == "advancedOptions" || $print_update_info == "advancedOptionsText") {
			return true;
		}
			
		//=========================================================
		// Should we print out wether or not to update?
		//=========================================================
		if ($print_update_info == "yes") {
			//return "<div class=\"errorMessage\">Version check connection failed.</div>";
		}
	}

?>