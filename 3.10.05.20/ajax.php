<? 
/***************************************************************************
 *                               ajax.php
 *                            -------------------
 *   begin                : Tuseday, March 14, 2006
 *   copyright            : (C) 2006 Fast Track Sites
 *   email                : sales@fasttracksites.com
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
	include 'includes/header.php';
	
	$actual_id = keepsafe($_GET['id']);
	$actual_action = parseurl($_GET['action']);
	$actual_value = parseurl($_GET['value']);
	$actual_type = parseurl($_GET['type']);
	$actual_showButtons = parseurl($_GET['showButtons']);
	$actual_showClient = parseurl($_GET['showClient']);
	$actual_folder = parseurl($_GET['folder']);
	
	// These functions can be used by anyone
	if ($actual_action == "javascriptVariables") {
		echo "
		progressSpinnerHTML = '" . progressSpinnerHTML() . "';
		imgCollapse = 'themes/" . $safs_config['ftssafs_theme'] . "/icons/collapse.png';
		imgExpand = 'themes/" . $safs_config['ftssafs_theme'] . "/icons/expand.png';";
	}
	
	// Only admins should be able to utilize any of these functions
	if ($_SESSION['user_level'] == SYSTEM_ADMIN || $_SESSION['user_level'] == BOARD_ADMIN) {
		//================================================
		// Main updater and get functions
		//================================================
		// Update an item in a DB table
		if ($actual_action == "updateitem") {
			$item = parseurl($_GET['item']);
			$table = parseurl($_GET['table']);
			$updateto = ($table == "notes" && $item == "text") ? preg_replace('/\<br(\s*)?\/?\>/i', "\n", $updateto) : $updateto;
			$updateto = ($item == "datetimestamp" || $item == "date_ordered" || $item == "date_shipped") ? strtotime(keeptasafe($_REQUEST['value'])) : keeptasafe($_REQUEST['value']);
			
			// Client admins can only modify certain tables
			if ($_SESSION['user_level'] == SYSTEM_ADMIN || ($_SESSION['user_level'] == BOARD_ADMIN && ($table != "config" || $table != "products" || $table != "users"))) {
				$table = ($table == "users") ? USERSDBTABLEPREFIX . $table : DBTABLEPREFIX . $table;
				
				$sql = "UPDATE `" . $table . "` SET " . $item ." = '" . $updateto . "' WHERE id = '" . $actual_id . "'";
				$result = $DB->query($sql);		
				
				if ($item == "datetimestamp" || $item == "date_ordered" || $item == "date_shipped") { 
					$result = (!empty($updateto)) ? makeDateTime($updateto) : "";
					echo $result;
				}
				elseif ($item == "discount") { 
					echo formatCurrency($updateto);
				}
				elseif ($item == "note") { 
					echo ajaxnl2br($updateto);
				}
				else { echo stripslashes($updateto); }
			}
		}
		
		// Get an item from a DB table
		elseif ($actual_action == "getitem") {
			$item = parseurl($_GET['item']);
			$table = parseurl($_GET['table']);
			
			// Client admins can only modify certain tables
			if ($_SESSION['user_level'] == SYSTEM_ADMIN || ($_SESSION['user_level'] == BOARD_ADMIN && ($table != "config" || $table != "products" || $table != "users"))) {
				$table = ($table == "users") ? USERSDBTABLEPREFIX . $table : DBTABLEPREFIX . $table;
			
				$sql = "SELECT " . $item ." FROM `" . $table . "` WHERE id = '" . $actual_id . "'";
				$result = $DB->query($sql);
				
				if ($result && $DB->num_rows() > 0) {
					while ($row = $DB->fetch_array($result)) {	
						if ($item == "datetimestamp" || $item == "date_ordered" || $item == "date_shipped") { 
							$returnVar = (!empty($row[$item])) ? makeShortDateTime($row[$item]) : ""; 
							echo $returnVar;
						}
						elseif ($item == "note") { 
							echo $row[$item];
						}
						else { echo bbcode($row[$item]); }
					}
					$DB->free_result($result);
				}
			}
		}	
		
		// Delete a row from a DB table
		elseif ($actual_action == "deleteitem") {
			$table = parseurl($_GET['table']);
			$errorCount = 0;
			
			// Client admins can only modify certain tables
			if ($_SESSION['user_level'] == SYSTEM_ADMIN || ($_SESSION['user_level'] == BOARD_ADMIN && ($table != "config" || $table != "products" || $table != "users"))) {
				// Delete and associated foreign items
				if ($table == "clients") {
					// Delete Appointments
					$sql = "DELETE FROM `" . DBTABLEPREFIX . "appointments` WHERE client_id = '" . $actual_id . "'";
					$result = $DB->query($sql);
					$errorCount += ($result) ? 0 : 1;
					
					// Select all associated Invoices so we can kill their foreign items
					$sql = "SELECT id FROM `" . DBTABLEPREFIX . "invoices` WHERE client_id = '" . $actual_id . "'";
					$result = $DB->query($sql);
					$errorCount += ($result) ? 0 : 1;
					
					if ($result && $DB->num_rows() > 0) {
						while ($row = $DB->fetch_array($result)) {
							// Delete Payments
							$sql = "DELETE FROM `" . DBTABLEPREFIX . "invoices_payments` WHERE invoice_id = '" . $row['id'] . "'";
							$result = $DB->query($sql);
							$errorCount += ($result) ? 0 : 1;
							
							// Delete Invoice Products
							$sql = "DELETE FROM `" . DBTABLEPREFIX . "invoices_products` WHERE invoice_id = '" . $row['id'] . "'";
							$result = $DB->query($sql);
							$errorCount += ($result) ? 0 : 1;
						}		
						$DB->free_result($result);
					}
					
					// Delete Invoices
					$sql = "DELETE FROM `" . DBTABLEPREFIX . "invoices` WHERE client_id = '" . $actual_id . "'";
					$result = $DB->query($sql);
					$errorCount += ($result) ? 0 : 1;
					
					// Delete Notes
					$sql = "DELETE FROM `" . DBTABLEPREFIX . "notes` WHERE client_id = '" . $actual_id . "'";
					$result = $DB->query($sql);
					$errorCount += ($result) ? 0 : 1;
					
					// Delete Orders
					$sql = "DELETE FROM `" . DBTABLEPREFIX . "orders` WHERE client_id = '" . $actual_id . "'";
					$result = $DB->query($sql);
					$errorCount += ($result) ? 0 : 1;
				}
				if ($table == "posts") {					
					// Get the topic and forum ids for this post so we can update their replies and posts values
					$sql = "SELECT t.id, t.forum_id, p.user_id FROM `" . DBTABLEPREFIX . "posts` p LEFT JOIN `" . DBTABLEPREFIX . "topics` t ON t.id = p.topic_id WHERE p.id = '" . $actual_id . "'";
					$result = $DB->query($sql);
					$errorCount += ($result) ? 0 : 1;
					
					if ($result && $DB->num_rows() > 0) {
						while ($row = $DB->fetch_array($result)) {
							// Update topic reply count
							$sql2 = "UPDATE `" . DBTABLEPREFIX . "topics` SET replies = replies - 1 WHERE id = '" . $row['id'] . "'";
							$result2 = $DB->query($sql2);
							$errorCount += ($result2) ? 0 : 1;
							
							// Update forum post count
							$sql2 = "UPDATE `" . DBTABLEPREFIX . "forums` SET posts = posts - 1 WHERE id = '" . $row['forum_id'] . "'";
							$result2 = $DB->query($sql2);
							$errorCount += ($result2) ? 0 : 1;
							
							// Update user post count
							$sql2 = "UPDATE `" . USERSDBTABLEPREFIX . "users` SET posts = posts - 1 WHERE id = '" . $row['user_id'] . "'";
							$result2 = $DB->query($sql2);
							$errorCount += ($result2) ? 0 : 1;
						}		
						$DB->free_result($result);
					}
				}
				if ($table == "invoices_payments") {
					// Check to see if our invoice is no longer paid in full and if so then change its status
					$sql = "UPDATE `" . DBTABLEPREFIX . "invoices` i SET status = '" . STATUS_INVOICE_AWAITING_PAYMENT . "' WHERE coalesce((SELECT SUM((ip.price + ip.profit + ip.shipping ) * ip.qty) - i.discount FROM `" . DBTABLEPREFIX . "invoices_products` ip WHERE ip.invoice_id = i.id), 0) - coalesce((SELECT SUM(ipa.paid) FROM `" . DBTABLEPREFIX . "invoices_payments` ipa WHERE ipa.invoice_id = i.id), 0) > 0";
					$result = $DB->query($sql);
					$errorCount += ($result) ? 0 : 1;
				}
				
				// Delete actual table row
				$table = ($table == "users") ? USERSDBTABLEPREFIX . $table : DBTABLEPREFIX . $table;
				$sql = "DELETE FROM `" . $table . "` WHERE id = '" . $actual_id . "'";	
			
				$result = $DB->query($sql);
				$errorCount += ($result) ? 0 : 1;
				
				$success = ($errorCount == 0) ? 1 : 0;
				
				echo $success;
			}
		}
		
		//================================================
		// Update our cats in the database
		//================================================
		elseif ($actual_action == "createCategory") {
			$name = keeptasafe($_GET['catname']);	
			
			$sql = "INSERT INTO `" . DBTABLEPREFIX . "categories` (`name`) VALUES ('" . $name . "')";
			$result = $DB->query($sql);
			$categoryID = mysql_insert_id();
			
			$content = ($result) ? "	<span class=\"greenText bold\">Successfully created category!</span>" : "	<span class=\"redText bold\">Failed to create category!!!</span>";
			
			switch(keepsafe($_GET['reprinttable'])) {
				case 1:
					$finalColumnData = ($actual_showButtons == 1) ? createDeleteLinkWithImage($categoryID, $categoryID . "_row", "categories", "category") : "";
					
					$tableHTML = "
						<tr class=\"even\" id=\"" . $categoryID . "_row\">
							<td>" . $name . "</td>
							<td class=\"center\">" . $finalColumnData . "</td>
						</tr>";
						
					echo $tableHTML;
					break;
				default:
					echo $content;
					break;
			}
		}
		
		//================================================
		// Update our ranks in the database
		//================================================
		elseif ($actual_action == "createRank") {
			$name = keeptasafe($_GET['rankName']);
			$posts = keeptasafe($_GET['rankPosts']);
			$image = keeptasafe($_GET['rankImage']);
			
			$rankID = $DB->query_insert("ranks", array('name' => $name, 'posts' => $posts, 'image' => $image));
			
			$content = ($rankID != FALSE) ? "	<span class=\"greenText bold\">Successfully created rank!</span>" : "	<span class=\"redText bold\">Failed to create rank!!!</span>";
			
			switch(keepsafe($_GET['reprinttable'])) {
				case 1:
					$finalColumnData = ($actual_showButtons == 1) ? createDeleteLinkWithImage($rankID, $rankID . "_row", "ranks", "rank") : "";
					
					$tableHTML = "
						<tr class=\"even\" id=\"" . $rankID . "_row\">
							<td>" . $name . "</td>
							<td>" . $posts . "</td>
							<td><img src=\"" . $image . "\" alt=\"\" /></td>
							<td class=\"center\">" . $finalColumnData . "</td>
						</tr>";
						
					echo $tableHTML;
					break;
				default:
					echo $content;
					break;
			}
		}
		
		//================================================
		// Update our smilies in the database
		//================================================
		elseif ($actual_action == "createSmiley") {
			$code = keeptasafe($_GET['smileyCode']);
			$image = keeptasafe($_GET['smileyImage']);
			
			$smileyID = $DB->query_insert("smilies", array('code' => $code, 'image' => $image));
			
			$content = ($smileyID != FALSE) ? "	<span class=\"greenText bold\">Successfully created smiley!</span>" : "	<span class=\"redText bold\">Failed to create smiley!!!</span>";
			
			switch(keepsafe($_GET['reprinttable'])) {
				case 1:
					$finalColumnData = ($actual_showButtons == 1) ? createDeleteLinkWithImage($smileyID, $smileyID . "_row", "smilies", "smiley") : "";
					
					$tableHTML = "
						<tr class=\"even\" id=\"" . $smileyID . "_row\">
							<td>" . $code . "</td>
							<td><img src=\"" . $image . "\" alt=\"\" /></td>
							<td class=\"center\">" . $finalColumnData . "</td>
						</tr>";
						
					echo $tableHTML;
					break;
				default:
					echo $content;
					break;
			}
		}
		
		//================================================
		// Update our topic icons in the database
		//================================================
		elseif ($actual_action == "createTopicIcon") {
			$name = keeptasafe($_GET['topicIconName']);
			$image = keeptasafe($_GET['topicIconImage']);
			
			$topicIconID = $DB->query_insert("topicicons", array('name' => $name, 'image' => $image));
			
			$content = ($topicIconID != FALSE) ? "	<span class=\"greenText bold\">Successfully created topic icon!</span>" : "	<span class=\"redText bold\">Failed to create topic icon!!!</span>";
			
			switch(keepsafe($_GET['reprinttable'])) {
				case 1:
					$finalColumnData = ($actual_showButtons == 1) ? createDeleteLinkWithImage($topicIconID, $topicIconID . "_row", "topicicons", "topic icon") : "";
					
					$tableHTML = "
						<tr class=\"even\" id=\"" . $topicIconID . "_row\">
							<td>" . $name . "</td>
							<td><img src=\"" . $image . "\" alt=\"\" /></td>
							<td class=\"center\">" . $finalColumnData . "</td>
						</tr>";
						
					echo $tableHTML;
					break;
				default:
					echo $content;
					break;
			}
		}
		
		//================================================
		// Update our word filter in the database
		//================================================
		elseif ($actual_action == "createWordFilter") {
			$code = keeptasafe($_GET['wordFilterCode']);
			$image = keeptasafe($_GET['wordFilterImage']);
			
			$wordFilterID = $DB->query_insert("wordfilters", array('code' => $code, 'image' => $image));
			
			$content = ($wordFilterID != FALSE) ? "	<span class=\"greenText bold\">Successfully created word filter!</span>" : "	<span class=\"redText bold\">Failed to create word filter!!!</span>";
			
			switch(keepsafe($_GET['reprinttable'])) {
				case 1:
					$finalColumnData = ($actual_showButtons == 1) ? createDeleteLinkWithImage($wordFilterID, $wordFilterID . "_row", "wordfilters", "word filter") : "";
					
					$tableHTML = "
						<tr class=\"even\" id=\"" . $wordFilterID . "_row\">
							<td>" . $code . "</td>
							<td><img src=\"" . $image . "\" alt=\"\" /></td>
							<td class=\"center\">" . $finalColumnData . "</td>
						</tr>";
						
					echo $tableHTML;
					break;
				default:
					echo $content;
					break;
			}
		}
		
		//================================================
		// Lets us lock a topic
		//================================================
		elseif ($actual_action == "lockTopic") {
			$status = ($actual_value == "lock") ? FORUM_LOCKED : FORUM_UNLOCKED;
			
			// Dont escape since this method does that, double escape isn't fun
			$result = $DB->query_update("topics", array('status' => $status), "id = '" . $actual_id . "'");
			
			echo "Your topic has been " . $actual_value . "ed.";
		}
		
		// Only System Admins can utilize these functions
		if ($_SESSION['user_level'] == SYSTEM_ADMIN) {
				
			//================================================
			// Update our users in the database
			//================================================
			if ($actual_action == "createUser") {
				$datetimestamp = time();
				$first_name = keeptasafe($_GET['first_name']);
				$last_name = keeptasafe($_GET['last_name']);
				$email_address = keeptasafe($_GET['email_address']);
				$username = keeptasafe($_GET['username']);
				$password = keeptasafe($_GET['password']);
				$password2 = keeptasafe($_GET['password2']);
				$company = keeptasafe($_GET['company']);
				$website = keeptasafe($_GET['website']);
				$userlevel = keeptasafe($_GET['userlevel']);
				
				if ($password == $password2) {
					$password = md5($password);
									
					$sql = "INSERT INTO `" . USERSDBTABLEPREFIX . "users` (`username`, `password`, `email_address`, `user_level`, `first_name`, `last_name`, `website`, `signup_date`) VALUES ('" . $username . "', '" . $password . "', '" . $email_address . "', '" . $userlevel . "', '" . $first_name . "', '" . $last_name . "', '" . $website . "', '" . $datetimestamp . "')";
					$result = $DB->query($sql);
					$userID = mysql_insert_id();
					
					$content = ($result) ? "	<span class=\"greenText bold\">Successfully created user!</span>" : "	<span class=\"redText bold\">Failed to create user!!!</span>";
				}
				else {
					$content = "<span class=\"redText bold\">The passwords you supplied do not match. Please fix this.</span>";			
				}
					
				switch(keepsafe($_GET['reprinttable'])) {
					case 1:				
						$finalColumnData = ($actual_showButtons == 1) ? "<a href=\"" . $menuvar['USERS'] . "&amp;action=edituser&amp;id=" . $userID . "\"><img src=\"themes/" . $safs_config['ftssafs_theme'] . "/icons/check.png\" alt=\"Edit User Details\" /></a> " . createDeleteLinkWithImage($userID, $userID . "_row", "users", "user") : "";
						
						$tableHTML = "
							<tr class=\"even\" id=\"" . $userID . "_row\">
								<td>" . $username . "</td>
								<td>" . $email_address . "</td>
								<td>" . $first_name . " " . $last_name . "</td>
								<td>" . makeDate($datetimestamp) . "</td>
								<td>" . getUserlevelFromID($userID) . "</td>
								<td class=\"center\">" . $finalColumnData . "</td>
							</tr>";
							
						echo $tableHTML;
						break;
					default:
						echo $content;
						break;
				}
			}
				
			//================================================
			// Update our users in the database
			//================================================
			elseif ($actual_action == "editUser") {
				$first_name = keeptasafe($_GET['first_name']);
				$last_name = keeptasafe($_GET['last_name']);
				$email_address = keeptasafe($_GET['email_address']);
				$username = keeptasafe($_GET['username']);
				$password = keeptasafe($_GET['password']);
				$password2 = keeptasafe($_GET['password2']);
				$company = keeptasafe($_GET['company']);
				$website = keeptasafe($_GET['website']);
				$userlevel = keeptasafe($_GET['userlevel']);
				
				if ($password == $password2) {
					$passwordSQL = (!empty($password)) ? " `password` = '" . md5($password) . "', " : "";
					
					$sql = "UPDATE `" . USERSDBTABLEPREFIX . "users` SET `username` = '" . $username . "'," . $passwordSQL . " `email_address` = '" . $email_address . "', `user_level` = '" . $userlevel . "', `first_name` = '" . $first_name . "', `last_name` = '" . $last_name . "', `website` = '" . $website . "' WHERE `id` = '" . $actual_id . "'";
					$result = $DB->query($sql);
					
					$content = ($result) ? "	<span class=\"greenText bold\">Successfully updated user!</span>" : "	<span class=\"redText bold\">Failed to update user!!!</span>";
				}
				else {
					$content = "<span class=\"redText bold\">The passwords you supplied do not match. Please fix this.</span>";			
				}
					
				echo $content;
			}
				
			//================================================
			// Search our user table
			//================================================
			elseif ($actual_action == "searchUsers") {
				echo printUsersTable($_GET, "");
			}
		}
	}
	
	// All users except banned users should be able to utilize any of these functions
	if (isset($_SESSION['username']) && $_SESSION['user_level'] != BANNED) {	
		//================================================
		// Show the images in a gallery folder
		//================================================
		if ($actual_action == "showGalleryImages") {
			$returnVar = "";
			$folder = $safs_config['ftssafs_avatar_gallery_path'] . '/' . $actual_folder;
			$avatarGallerImages = array();
			$x = 1;
			
			if($dir = opendir($folder)){
				while (false !== ($file = readdir($dir))) {				
					if ($file != "." && $file != ".." && !is_dir($folder . '/' . $file)) {
						$avatarGallerImages[$file] .= '';	
					}
				}
				
				ksort($avatarGallerImages); //sort by name
				
				foreach($avatarGallerImages as $avatarFilename => $nothing) {
					$path_info = pathinfo($avatarFilename);
					$ext = $path_info['extension']; 

					if($ext == "jpg" || $ext == "jpeg" || $ext == "gif" || $ext == "png") {
						$returnVar .= "
							" . (($x == 1) ? "<div class=\"clear\">" : "") . "
								<div class=\"avatar\">
									<img src=\"" . $folder . "/" . $avatarFilename . "\" alt=\"\" /><br />
									<input type=\"radio\" name=\"galleryAvatar\" id=\"" . str_replace(" ", "", $path_info['filename']) . "\" value=\"" . $folder . "/" . $avatarFilename . "\"><label for=\"" . str_replace(" ", "", $path_info['filename']) . "\">" . $path_info['filename'] . "</label>
								</div>
							" . (($x == 5) ? "</div>" : "");
						
						$x = ($x == 5) ? 1 : $x + 1;
					}
				}
			}
			else {
				$returnVar = "<strong>This folder is empty.</strong>";
			}
				
			echo $returnVar;
		}
	}
?>
