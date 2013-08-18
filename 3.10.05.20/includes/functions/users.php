<?php 
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
	// Returns number of users
	//=================================================
	function getUserCount() {
		global $DB;
		$numRows = 0;
		
		$sql = "SELECT COUNT(id) AS numRows FROM `" . USERSDBTABLEPREFIX . "users`";
		$result = $DB->query($sql);
				
		if ($result && $DB->num_rows() > 0) {
			while ($row = $DB->fetch_array($result)) {	
				$numRows = $row['numRows'];
			}
			$DB->free_result($result);
		}
		
		return $numRows;
	}
 
	//=========================================================
	// Gets a username from a userid
	//=========================================================
	function getUsernameFromID($userID) {
		global $DB;
	
		$sql = "SELECT username FROM `" . USERSDBTABLEPREFIX . "users` WHERE id='" . $userID . "'";
		$result = $DB->query($sql);
		
		if ($result && $DB->num_rows() > 0) {
			while ($row = $DB->fetch_array($result)) {
				return $row['username'];
			}	
			$DB->free_result($result);
		}
	}
 
	//=========================================================
	// Gets a userid from a username
	//=========================================================
	function getUserIDFromUsername($username) {
		global $DB;
	
		$sql = "SELECT id FROM `" . USERSDBTABLEPREFIX . "users` WHERE username='" . $username . "'";
		$result = $DB->query($sql);
		
		if ($result && $DB->num_rows() > 0) {
			while ($row = $DB->fetch_array($result)) {
				return $row['id'];
			}	
			$DB->free_result($result);
		}
		else { return false; }
	}
	
	//=========================================================
	// Gets a user's userlevel from a userid
	//=========================================================
	function getUserlevelFromID($userID) {
		global $DB;
		$level = "";
		
		$sql = "SELECT user_level FROM `" . USERSDBTABLEPREFIX . "users` WHERE id='" . $userID . "' LIMIT 1";
		$result = $DB->query($sql);
		
		if ($result && $DB->num_rows() > 0) {
			while ($row = $DB->fetch_array($result)) {
				$level = ($row['user_level'] == SYSTEM_ADMIN) ? "System Administrator" : "Client Administrator";
				$level = ($row['user_level'] == USER) ? "User" : $level;
				$level = ($row['user_level'] == BANNED) ? "Banned" : $level;
			}	
			$DB->free_result($result);
		}
		
		return $level;
	}
	
	//=========================================================
	// Gets an email address from a userid
	//=========================================================
	function getEmailAddressFromID($userID) {
		global $DB;
	
		$sql = "SELECT email_address FROM `" . USERSDBTABLEPREFIX . "users` WHERE id='" . $userID . "'";
		$result = $DB->query($sql);
		
		if ($result && $DB->num_rows() > 0) {
			while ($row = $DB->fetch_array($result)) {
				return $row['email_address'];
			}	
			$DB->free_result($result);
		}
	}
	
	//=========================================================
	// Gets an user's signature array from a userid
	//=========================================================
	function getUserSignatureArrayFromID($userID) {
		global $DB;
	
		$sql = "SELECT attachsig, signature FROM `" . USERSDBTABLEPREFIX . "users` WHERE id='" . $userID . "'";
		$result = $DB->query($sql);
		
		if ($result && $DB->num_rows() > 0) {
			while ($row = $DB->fetch_array($result)) {
				// Make sure we filter our signature
				return array($row['attach_sig'], runWordFilters($row['signature']));
			}	
			$DB->free_result($result);
		}
	}
	
	//=========================================================
	// Gets an user's information block from a userid
	//=========================================================
	function getUserInfoBlockFromID($userID) {
		global $DB;
		$onlineStatus = "Offline";
		$userInfoBlock = "";
	
		$sql = "SELECT u.avatar, u.title, u.posts, u.signup_date, r.name, r.image FROM `" . USERSDBTABLEPREFIX . "users` u LEFT JOIN `" . DBTABLEPREFIX . "ranks` r ON u.posts >=r.posts WHERE u.id='" . $userID . "' ORDER BY r.posts ASC";
		$result = $DB->query($sql);
		
		if ($result && $DB->num_rows() > 0) {
			while ($row = $DB->fetch_array($result)) {
				// See if the user is online
				$sql2 = "SELECT * FROM `" . DBTABLEPREFIX . "usersonline` WHERE user_id = '" . $userID . "'";
				$result2 = $DB->query($sql2);
				
				if ($result2 && $DB->num_rows() > 0) {
					while ($row2 = $DB->fetch_array($result2)) {
						$online = "Online";
					}	
					$DB->free_result($result2);
				}
				
				// Build our block
				$userInfoBlock = "
								<ul class=\"userInfoBlock\">
									<li class=\"avatar\"><a href=\"" . $menuvar['VIEWPROFILE'] . "&id=" . $userID . "\"><img src=\"" . ((empty($row['avatar'])) ? $safs_config['ftssafs_default_avatar'] : $row['avatar']) . "\" alt=\"\" /></a></li>
									<li class=\"title\">" . $row['title'] . "</li>
									<li class=\"rank\">
										" . $row['name'] . "<br />
										" . ((!empty($row['image'])) ? "<img src=\"" . $row['image'] . "\" alt=\"\" />" : "") . "
									</li>
									<li>
										<span class=\"title\">Posts:</span>
										<span>" . $row['posts'] . "</span>
									</li>
									<li>
										<span class=\"title\">Joined:</span>
										<span>" . makeShortDate($row['signup_date']) . "</span>
									</li>
									<li class=\"onlineStatus\"><a class=\"button\"><span>" . $onlineStatus . "</span></a></li>
								</ul>";
			}	
			$DB->free_result($result);
		}
		
		return $userInfoBlock;
	}
	
	//=========================================================
	// Gets an user's contact button block from a userid
	//=========================================================
	function getUserContactButtonBlockFromID($userID) {
		global $DB, $menuvar;
		$userContactButtonBlock = "";
	
		$sql = "SELECT aim, yim, msn FROM `" . USERSDBTABLEPREFIX . "users` WHERE id='" . $userID . "' LIMIT 1";
		$result = $DB->query($sql);
		
		if ($result && $DB->num_rows() > 0) {
			while ($row = $DB->fetch_array($result)) {
				// Build our block
				$userContactButtonBlock = "
								<ul class=\"contactBlock\">
									<li class=\"pmButton\"><a href=\"" . $menuvar['COMPOSE'] . "&id=" . $userID . "\"><span>PM</span></a></li>
									" . ((!empty($row['aim'])) ? "<li class=\"aimButton\"><a href=\"aim:GoIm?screenname=" . $row['aim'] . "\"><span>AIM</span></a></li>" : "") . "
									" . ((!empty($row['yim'])) ? "<li class=\"yimButton\"><a href=\"http://edit.yahoo.com/config/send_webmesg?.target=" . $row['yim'] . "&.src=pg\"><span>YIM</span></a></li>" : "") . "
									" . ((!empty($row['msn'])) ? "<li class=\"msnButton\"><a href=\"http://members.msn.com/" . $row['msn'] . "\"><span>MSN</span></a></li>" : "") . "
									" . ((!empty($row['icq'])) ? "<li class=\"msnButton\"><a href=\"http://www.icq.com/people/webmsg.php?to=" . $row['icq'] . "\"><span>ICQ</span></a></li>" : "") . "
								</ul>";
			}	
			$DB->free_result($result);
		}
		
		return $userContactButtonBlock;
	}
	
	//=========================================================
	// Handles processes for our useronline table
	//=========================================================
	function runUsersOnlineUpdate($p, $s, $id) {
		global $DB, $_SERVER;
		
		$timeoutseconds = (session_is_registered('username')) ? 600 : 300;	
		$timestamp = time();
		$timeout = $timestamp + $timeoutseconds;
		$ip = $_SERVER['REMOTE_ADDR'];	
		$currentUserID = (session_is_registered('userid')) ? $_SESSION['userid'] : 0;
		$currentUsername = (session_is_registered('username')) ? $_SESSION['username'] : "Guest";
		
		//==========================================================	
		// Delete users that have been online for more then "$timeoutseconds" seconds	
		// Delete our records
		//==========================================================
		$extraSQL = (session_is_registered('username')) ? " OR username = '" . $_SESSION['username'] . "'" : "";
		$sql = "DELETE FROM `" . DBTABLEPREFIX . "usersonline` WHERE datetimestamp < '" . $timestamp . "' OR ip = '" . $ip . "'" . $extraSQL;
		$result = $DB->query($sql);
		
		//==========================================================		
		// Add this user to database
		//==========================================================
		$file = (!empty($p)) ? "index.php?p=" . $p : "index.php";
		$file .= (!empty($s)) ? "&s=" . $s : "";
		$file .= ($p == 'viewforum' || $p == 'viewtopic') ? "&id=" . $id : "";
		
		$result = $DB->query_insert('usersonline', array('user_id' => $currentUserID, 'username' => $currentUsername, 'datetimestamp' => $timeout, 'ip' => $ip, 'file' => $file));
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
	function returnUsersViewingThisPageBlock($thisPage) {
		global $DB, $menuvar, $safs_config;
		
		// Initialize our variables
		$totalonline = $totalusers = $totalguests = 0;
	
		//==========================================================
		// Find total amount of users viewing this page	
		//==========================================================
		$sql = "SELECT COUNT(username) AS numRows FROM `" . DBTABLEPREFIX . "usersonline` WHERE file = '" . $thisPage . "'";
		$result = $DB->query($sql);
		
		// Add our data
		if ($result && $DB->num_rows() > 0) {
			while ($row = $DB->fetch_array($result)) {
				$totalonline = $row['numRows'];
			}
			$DB->free_result($result);
		}
				
		//==========================================================
		// Find out whos online and viewing this page and if 
		// they're a user make a link to their profile page	
		//==========================================================
		$sql = "SELECT * FROM `" . DBTABLEPREFIX . "usersonline` WHERE username != 'Guest' AND file = '" . $thisPage . "'";
		$result = $DB->query($sql);
		
		// Add our data
		if (!$result || $DB->num_rows() == 0) {
			$users = "<li>No registered users logged in.</li>";
		}
		else {
			// Calculate our number of users and guests
			$totalusers = $DB->num_rows();
			$totalguests = $totalonline - $totalusers;
		
			while ($row = $DB->fetch_array($result)) {
				$users .= "<li><a href=\"" . $menuvar['VIEWPROFILE'] . "&id=" . $row['user_id'] . "\">" . $row['username'] . "</a></li>";
			}
			$DB->free_result($result);
		}
		
			
		//==========================================================
		// Print out our nice little div	
		//==========================================================
		$block = "
			<div id=\"viewingThisPageBlock\">
				<h3>
					<div class=\"floatRight\"><a style=\"cursor: pointer; cursor: hand;\" onclick=\"ajaxToggleDiv('viewingBody', 'viewingToggle');\"><img src=\"themes/" . $safs_config['ftssafs_theme'] . "/icons/collapse.png\" alt=\"Collapse / Expand\" id=\"viewingToggle\" /></a></div>
					" . $totalusers . " Users, " . $totalguests . "  Guests Are Viewing This Page:
				</h3>
				<ul id=\"viewingBody\">
					" . $users . "
				</ul>
			</div>";
			
		return $block;
	}
	
 	//=================================================
	// Print the Users Table
	//=================================================
	function printMembersTable() {
		global $DB, $menuvar, $safs_config, $actual_page;
				
		// Figure out if we are using pagination due to the number of items in this table
		$numOfTopics = getUserCount();
		$paginationArray = determinePagination($numOfTopics, $actual_page);
		$extraSQL = $paginationArray['extraSQL'];
		$totalPages = $paginationArray['totalPages'];
		
		$sql = "SELECT u.id, u.username, u.user_level, u.posts, r.name, r.image, u.signup_date, u.aim, u.yim, u.msn FROM `" . USERSDBTABLEPREFIX . "users` u LEFT JOIN `" . DBTABLEPREFIX . "ranks` r ON u.posts >=r.posts WHERE u.id > 0 ORDER BY u.username, r.posts ASC" . $extraSQL;
		$result = $DB->query($sql);
		
		$x = 1; //reset the variable we use for our row colors	
		
		// Create our new table
		$table = new tableClass(1, 1, 1, "contentBox tablesorter", "usersTable");
		
		// Create table title
		$table->addNewRow(array(array("data" => "Current Members", "colspan" => "6")), "", "title1", "thead");
		
		// Create column headers
		$table->addNewRow(
			array(
				array("type" => "th", "data" => "Username"),
				array("type" => "th", "data" => "User Level"),
				array("type" => "th", "data" => "Rank"),
				array("type" => "th", "data" => "Posts"),
				array("type" => "th", "data" => "Joined"),
				array("type" => "th", "data" => "Contact")
			), "", "title2", "thead"
		);
							
		// Add our data
		if (!$result || $DB->num_rows() == 0) {
			$table->addNewRow(array(array("data" => "There are no users in the system.", "colspan" => "6")), "usersTableDefaultRow", "greenRow");
		}
		else {
			while ($row = $DB->fetch_array($result)) {
				// User Level
				$level = ($row['user_level'] == SYSTEM_ADMIN) ? "System Administrator" : "Client Administrator";
				$level = ($row['user_level'] == USER) ? "User" : $level;
				$level = ($row['user_level'] == BANNED) ? "Banned" : $level;
				
				$table->addNewRow(
					array(
						array("data" => "<a href=\"" . $menuvar['VIEWPROFILE'] . "&id=" . $row['id'] . "\">" . $row['username'] . "</a>"),
						array("data" => $level),
						array("data" => $row['name'] . ((!empty($row['image'])) ? " <img src=\"" . $row['image'] . "\" alt=\"\" />" : "")),
						array("data" => $row['posts'], "class" => "center"),
						array("data" => makeDate($row['signup_date'])),
						array("data" => getUserContactButtonBlockFromID($row['id']), "class" => "center")
					), "", "row" . $x
				);
				
				$x = ($x == 1) ? 2 : 1;
			}
			$DB->free_result($result);
		}
		
		// Return the table's HTML
		return $table->returnTableHTML() . "
		
						<div id=\"paginationHolder\">
							" . ((!empty($extraSQL)) ? generatePagination($menuvar['MEMBERS'], $actual_page, $totalPages) : "") . "
						</div>";
	}
	
	//=================================================
	// Returns the JQuery functions used to run the 
	// new order form
	//=================================================
	function returnMembersTableJQuery() {
		$JQueryReadyScripts = "";
		
		return $JQueryReadyScripts;
	}
	
 	//=================================================
	// Print the Users Table
	//=================================================
	function printSearchUsersTable($postVars) {
		global $menuvar, $safs_config;
			
		$x = 1; //reset the variable we use for our row colors	
		
		// Create our new table
		$table = new tableClass(1, 1, 1, "contentBox");
		
		// Create table title
		$table->addNewRow(array(array("data" => "Search Users", "colspan" => "2")), "", "title1");
		
		// Create column headers
		$table->addNewRow(
			array(
				array("type" => "th", "data" => "Choose any or all of the following to search by.", "colspan" => "2")
			), "", "title2"
		);
							
		// Add our data
		$fieldArray = array(
			'search_username' => "Username:",
			'search_email_address' => "Email Address:",
			'search_first_name' => "First name:",
			'search_last_name' => "Last name:"
		);
		
		foreach ($fieldArray as $fieldName => $title) {
			$table->addNewRow(
				array(
					array("data" => $title),
					array("data" => "<input type=\"text\" name=\"" . $fieldName . "\" size=\"40\" value=\"" . keeptasafe($postVars[$fieldName]) . "\" />")
				), "", "row" . $x
			);
			
			$x = ($x==2) ? 1 : 2;
		}
		
		// Return the table's HTML
		$content = "
				<form name=\"searchUsersForm\" id=\"searchUsersForm\" action=\"" . $menuvar['USERS'] . "\" method=\"post\" onsubmit=\"return false;\">
					" . $table->returnTableHTML() . "
					<br />
					<input type=\"submit\" name=\"submit\" class=\"button\" value=\"Search!\" />
				</form>";
		
		return $content;
	}
	
	//=================================================
	// Returns the JQuery functions used to run the 
	// new order form
	//=================================================
	function returnSearchUsersTableJQuery() {			
		$JQueryReadyScripts = "
			var v = jQuery(\"#searchUsersForm\").validate({
				errorElement: \"div\",
				errorClass: \"validation-advice\",
				submitHandler: function(form) {	
					jQuery.get('ajax.php?action=searchUsers', $('#searchUsersForm').serialize(), function(data) {
						// Update the proper div with the returned data
						$('#updateMeUsers').html(data);
					});
				}
			});";
		
		return $JQueryReadyScripts;
	}
	
 	//=================================================
	// Print the Users Table
	//=================================================
	function printUsersTable($postVars = array()) {
		global $DB, $menuvar, $safs_config;
		
		$currentTimestamp = time();
		$todayTimestamp = strtotime(gmdate('Y-m-d', $currentTimestamp + (3600 * '-7.00')));
		$tomorrowTimestamp = strtotime(gmdate('Y-m-d', strtotime("+1 day") + (3600 * '-7.00')));
		
		$extraSQL = " WHERE 1";
		$extraSQL .= (isset($postVars['search_username']) && !empty($postVars['search_username'])) ? " AND username LIKE '%" . keepsafe($postVars['search_username']) . "%'" : "";
		$extraSQL .= (isset($postVars['search_email_address']) && !empty($postVars['search_email_address'])) ? " AND email_address LIKE '%" . keepsafe($postVars['search_email_address']) . "%'" : "";
		$extraSQL .= (isset($postVars['search_first_name']) && !empty($postVars['search_first_name'])) ? " AND first_name LIKE '%" . keeptasafe($postVars['search_first_name']) . "%'" : "";
		$extraSQL .= (isset($postVars['search_last_name']) && !empty($postVars['search_last_name'])) ? " AND last_name LIKE '%" . keeptasafe($postVars['search_last_name']) . "%'" : "";
		
		$sql = "SELECT * FROM `" . USERSDBTABLEPREFIX . "users`" . $extraSQL . " ORDER BY signup_date DESC";
		$result = $DB->query($sql);
		
		// Create our new table
		$table = new tableClass(1, 1, 1, "contentBox tablesorter", "usersTable");
		
		// Create table title
		$table->addNewRow(array(array("data" => "Current Users (" . $DB->num_rows() . ")", "colspan" => "6")), "", "title1", "thead");
		
		// Create column headers
		$table->addNewRow(
			array(
				array("type" => "th", "data" => "Username"),
				array("type" => "th", "data" => "Email Address"),
				array("type" => "th", "data" => "Full Name"),
				array("type" => "th", "data" => "Signup Date"),
				array("type" => "th", "data" => "User Level"),
				array("type" => "th", "data" => "")
			), "", "title2", "thead"
		);
							
		// Add our data
		if (!$result || $DB->num_rows() == 0) {
			$table->addNewRow(array(array("data" => "There are no users in the system.", "colspan" => "6")), "usersTableDefaultRow", "greenRow");
		}
		else {
			while ($row = $DB->fetch_array($result)) {				
				$table->addNewRow(
					array(
						array("data" => $row['username']),
						array("data" => $row['email_address']),
						array("data" => $row['first_name'] . " " . $row['last_name']),
						array("data" => makeDate($row['signup_date'])),
						array("data" => getUserlevelFromID($row['id'])),
						array("data" => "<a href=\"" . $menuvar['USERS'] . "&amp;action=edituser&amp;id=" . $row['id'] . "\"><img src=\"themes/" . $safs_config['ftssafs_theme'] . "/icons/check.png\" alt=\"Edit User Details\" /></a> " . createDeleteLinkWithImage($row['id'], $row['id'] . "_row", "users", "user"), "class" => "center")
					), $row['id'] . "_row", ""
				);
			}
			$DB->free_result($result);
		}
		
		// Return the table's HTML
		return $table->returnTableHTML() . "
				<div id=\"usersTableUpdateNotice\"></div>";
	}
	
	//=================================================
	// Returns the JQuery functions used to run the 
	// users table
	//=================================================
	function returnUsersTableJQuery() {							
		$JQueryReadyScripts = "
				$('#usersTable').tablesorter({ widgets: ['zebra'], headers: { 5: { sorter: false } 
					}
				});";
		
		return $JQueryReadyScripts;
	}
	
	//=================================================
	// Create a form to add new orders
	//
	// Used so that we can display it in many places
	//=================================================
	function printNewUserForm() {
		global $menuvar, $safs_config;

		$content .= "
				<div id=\"newUserResponse\">
				</div>
				<form name=\"newUserForm\" id=\"newUserForm\" action=\"" . $menuvar['USERS'] . "\" method=\"post\" class=\"inputForm\" onsubmit=\"return false;\">
					<fieldset>
						<legend>New User</legend>
						<div><label for=\"first_name\">First Name <span>- Required</span></label> <input name=\"first_name\" id=\"first_name\" type=\"text\" size=\"60\" class=\"required\" /></div>
						<div><label for=\"last_name\">Last Name <span>- Required</span></label> <input name=\"last_name\" id=\"last_name\" type=\"text\" size=\"60\" class=\"required\" /></div>
						<div><label for=\"email_address\">Email Address <span>- Required</span></label> <input name=\"email_address\" id=\"email_address\" type=\"text\" size=\"60\" class=\"required\" /></div>
						<div><label for=\"username\">Username <span>- Required</span></label> <input name=\"username\" id=\"username\" type=\"text\" size=\"60\" class=\"required\" /></div>
						<div><label for=\"password\">Password <span>- Required</span></label> <input name=\"password\" id=\"password\" type=\"password\" size=\"60\" class=\"required\" /></div>
						<div><label for=\"password2\">Confirm Password <span>- Required</span></label> <input name=\"password2\" id=\"password2\" type=\"password\" size=\"60\" class=\"required\" /></div>
						<div><label for=\"company\">Company </label> <input name=\"company\" id=\"company\" type=\"text\" size=\"60\" /></div>
						<div><label for=\"website\">Website </label> <input name=\"website\" id=\"website\" type=\"text\" size=\"60\" /></div>
						<div><label for=\"userlevel\">User Level <span>- Required</span></label> " . createDropdown("userlevel", "userlevel", "", "", "required") . "</div>
						<div class=\"center\"><input type=\"submit\" class=\"button\" value=\"Create User\" /></div>
					</fieldset>
				</form>";
			
		return $content;
	}
	
	//=================================================
	// Returns the JQuery functions used to run the 
	// new order form
	//=================================================
	function returnNewUserFormJQuery($reprintTable = 0, $allowModification = 1) {		
		$extraJQuery = ($reprintTable == 0) ? "
  						// Update the proper div with the returned data
						$('#newUserResponse').html('" . progressSpinnerHTML() . "');
						$('#newUserResponse').html(data);
						$('#newUserResponse').effect('highlight',{},500);" 
						: "
						// Clear the default row
						$('#usersTableDefaultRow').remove();
  						// Update the table with the new row
						$('#usersTable > tbody:last').append(data);
						$('#usersTableUpdateNotice').html('" . tableUpdateNoticeHTML() . "');
						// Show a success message
						$('#newUserResponse').html('" . progressSpinnerHTML() . "');
						$('#newUserResponse').html(returnSuccessMessage('user'));";
							
		$JQueryReadyScripts = "
			var v = jQuery(\"#newUserForm\").validate({
				errorElement: \"div\",
				errorClass: \"validation-advice\",
				rules: {
					password2: {
						equalTo: '#password'
					}
				},
				submitHandler: function(form) {			
					jQuery.get('ajax.php?action=createUser&reprinttable=" . $reprintTable . "&showButtons=" . $allowModification . "', $('#newUserForm').serialize(), function(data) {
  						" . $extraJQuery . "
						// Clear the form
						/*
						$('#first_name').val = '';
						$('#last_name').val = '';
						$('#email_address').val = '';
						$('#username').val = '';
						$('#password').val = '';
						$('#password2').val = '';
						$('#company').val = '';
						$('#website').val = '';
						*/
					});
				}
			});";
		
		return $JQueryReadyScripts;
	}
	
	//=================================================
	// Create a form to edit orders
	//
	// Used so that we can display it in many places
	//=================================================
	function printEditUserForm($userID) {
		global $DB, $menuvar, $safs_config;
		
		$sql = "SELECT * FROM `" . USERSDBTABLEPREFIX . "users` WHERE id = '" . $userID . "' LIMIT 1";
		$result = $DB->query($sql);
		
		if ($result && $DB->num_rows() == 0) {
			$page_content = "<span class=\"center\">There was an error while accessing the user's details you are trying to update. You are being redirected to the main page.</span>
							<meta http-equiv=\"refresh\" content=\"5;url=" . $menuvar['USERS'] . "\">";	
		}
		else {
			$row = $DB->fetch_array($result);
			
			$content .= "
				<div id=\"editUserResponse\">
				</div>
				<form name=\"editUserForm\" id=\"editUserForm\" action=\"" . $menuvar['USERS'] . "\" method=\"post\" class=\"inputForm\" onsubmit=\"return false;\">
					<fieldset>
						<legend>Edit User</legend>
						<div><label for=\"first_name\">First Name <span>- Required</span></label> <input name=\"first_name\" id=\"first_name\" type=\"text\" size=\"60\" value=\"" . $row['first_name'] . "\" /></div>
						<div><label for=\"last_name\">Last Name <span>- Required</span></label> <input name=\"last_name\" id=\"last_name\" type=\"text\" size=\"60\" value=\"" . $row['last_name'] . "\" /></div>
						<div><label for=\"email_address\">Email Address <span>- Required</span></label> <input name=\"email_address\" id=\"email_address\" type=\"text\" size=\"60\" value=\"" . $row['email_address'] . "\" /></div>
						<div><label for=\"username\">Username <span>- Required</span></label> <input name=\"username\" id=\"username\" type=\"text\" size=\"60\" value=\"" . $row['username'] . "\" /></div>
						<div><label for=\"password\">Password <span>- Required</span></label> <input name=\"password\" id=\"password\" type=\"password\" size=\"60\" /></div>
						<div><label for=\"password2\">Confirm Password <span>- Required</span></label> <input name=\"password2\" id=\"password2\" type=\"password\" size=\"60\" /></div>
						<div><label for=\"company\">Company </label> <input name=\"company\" id=\"company\" type=\"text\" size=\"60\" value=\"" . $row['company'] . "\" /></div>
						<div><label for=\"website\">Website </label> <input name=\"website\" id=\"website\" type=\"text\" size=\"60\" value=\"" . $row['website'] . "\" /></div>
						<div><label for=\"userlevel\">User Level <span>- Required</span></label> " . createDropdown("userlevel", "userlevel", $row['user_level'], "") . "</div>
						<div><input type=\"submit\" class=\"button\" value=\"Update User\" /></div>
					</fieldset>
				</form>";
				
			$DB->free_result($result);
		}
			
		return $content;
	}
	
	//=================================================
	// Returns the JQuery functions used to run the 
	// edit order form
	//=================================================
	function returnEditUserFormJQuery($userID) {		
		$JQueryReadyScripts = "
			var v = jQuery(\"#editUserForm\").validate({
				errorElement: \"div\",
				errorClass: \"validation-advice\",
				submitHandler: function(form) {			
					jQuery.get('ajax.php?action=editUser&id=' + " . $userID . ", $('#editUserForm').serialize(), function(data) {
  						// Update the proper div with the returned data
						$('#editUserResponse').html(data);
					});
				}
			});";
		
		return $JQueryReadyScripts;
	}

?>