<?php 
/***************************************************************************
 *                               profile.php
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
	// Print the View Profile Table
	//=================================================
	function printViewProfileTable($userID) {
		global $DB, $menuvar, $safs_config;
		
		$messagePostTableData = "";
		$sql = "SELECT * FROM `" . DBTABLEPREFIX . "users` WHERE id='" . $userID . "' LIMIT 1";
		$result = $DB->query($sql);

		// Add our data
		if (!$result || $DB->num_rows() == 0) {
			$messagePostTableData .= "The requested user does not exist.";
		}
		else {							
			while ($row = $DB->fetch_array($result)) {
				// Gather our user's signature settings
				$signatureArray = getUserSignatureArrayFromID($row['id']);
				
				// Build the actual message section
				$messagePostTableData .= "
								<h2 class=\"profileTitle\">" . getUsernameFromID($row['id']) . "'s Profile</h2>
								<div class=\"profileWrapper\">
									<div class=\"profile\">
										<div class=\"author\">
											" . getUserInfoBlockFromID($row['id']) . "
											
											<h3>Contact information</h3>
											" . getUserContactButtonBlockFromID($row['id']) . "
										</div>
										<div class=\"profile_body\">
											<div class=\"content\">
												<div id=\"tabs\">
													<ul>
														<li><a href=\"#userDetailsTab\"><span>User Details</span></a></li>
														<li><a href=\"#topicsTab\"><span>Topics</span></a></li>
														<li><a href=\"#postsTab\"><span>Posts</span></a></li>
													</ul>
													<div id=\"userDetailsTab\">
														<dl>
															" . ((empty($row['gender'])) ? "" : "
															<dt>Gender: </dt>
															<dd>" . $row['gender'] . "</dd>"
															)
															. ((empty($row['birthday'])) ? "" : "
															<dt>Birthday: </dt>
															<dd>" . makeDate($row['birthday']) . "</dd>"
															)
															. ((empty($row['country'])) ? "" : "
															<dt>Country: </dt>
															<dd>" . $row['country'] . "</dd>"
															)
															. ((empty($row['website'])) ? "" : "
															<dt>Website: </dt>
															<dd><a href=\"" . $row['website'] . "\">" . $row['website'] . "</a></dd>"
															) 
															. (($signatureArray[0] == 0 || empty($signatureArray[1])) ? "" : "
															<dt>Signature: </dt>
															<dd>" . $signatureArray[1] . "</dd>"
															) . "
														</dl>
													</div>
													<div id=\"topicsTab\">
														" . printSearchResultsTable("", $_SESSION['username']) . "
													</div>
													<div id=\"postsTab\">
														" . printSearchResultsTable("", $_SESSION['username'], SEARCH_TITLE_AND_POST, "", "", array(), SEARCH_POSTS) . "
													</div>
												</div>
											</div>
										</div>
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
	function returnViewProfileTableJQuery() {
		global $DB, $menuvar, $safs_config;
					
		$JQueryReadyScripts = "$(\"#tabs\").tabs();";
		
		return $JQueryReadyScripts;
	}
	
	//=================================================
	// Print the Edit Profile Form
	//=================================================
	function printEditProfileForm($userID = "") {
		global $DB, $menuvar, $safs_config;
		
		// Make sure only admins can edit other user accounts
		$userID = (empty($userID)) ? $_SESSION['userid'] : $userID;
		$userID = ($_SESSION['user_level'] == SYSTEM_ADMIN || $_SESSION['user_level'] == BOARD_ADMIN) ? $userID : $_SESSION['userid'];
		
		$returnVar = "";
		$sql = "SELECT * FROM `" . DBTABLEPREFIX . "users` WHERE id='" . $userID . "' LIMIT 1";
		$result = $DB->query($sql);

		// Add our data
		if (!$result || $DB->num_rows() == 0) {
			$returnVar .= "Failed to load user information.";
		}
		else {							
			while ($row = $DB->fetch_array($result)) {
				$returnVar = "
											<h2 class=\"editProfileFormTitle\">Edit Profile</h2>
											<div class=\"editProfileBlock\">
												<form id=\"editProfileForm\" action=\"" . $menuvar['PROFILE'] . "\" method=\"post\" enctype=\"multipart/form-data\">
													<fieldset>
														<h3>Profile Information</h3>
														<ul>
															<li>
																<label for=\"username\">Username:</label>
																<span id=\"username\">" . getUsernameFromID($userID) . "</span>
															</li>
															<li>
																<label for=\"password1\">Password:</label>
																<input type=\"password\" name=\"password1\" id=\"password1\" size=\"60\" />
															</li>
															<li>
																<label for=\"password2\">Confirm Password:</label>
																<input type=\"password\" name=\"password2\" id=\"password2\" size=\"60\" />
															</li>
															<li>
																<label for=\"firstName\">First Name:</label>
																<input type=\"text\" name=\"firstName\" id=\"firstName\" size=\"60\" value=\"" . $row['first_name'] . "\" />
															</li>
															<li>
																<label for=\"lastName\">Last Name:</label>
																<input type=\"text\" name=\"lastName\" id=\"lastName\" size=\"60\" value=\"" . $row['last_name'] . "\" />
															</li>
															" . (($_SESSION['user_level'] == SYSTEM_ADMIN || $_SESSION['user_level'] == BOARD_ADMIN) ? "
															<li>
																<label for=\"title\">Title:</label>
																<input type=\"text\" name=\"title\" id=\"title\" size=\"60\" value=\"" . $row['title'] . "\" />
															</li>" : ""
															) . "
															<li>
																<label for=\"emailAddress\">Email Address:</label>
																<input type=\"text\" name=\"emailAddress\" id=\"emailAddress\" class=\"required\" size=\"60\" value=\"" . $row['email_address'] . "\" />
															</li>
														</ul>
													</fieldset>
													<fieldset>
														<h3>Optional Information</h3>
														<ul>
															<li>
																<label for=\"gender\">Gender:</label>
																" . createDropdown("genders", "gender", $row['gender']) . "
															</li>
															<li>
																<label for=\"birthday\">Birthday:</label>
																<input type=\"text\" name=\"birthday\" id=\"birthday\" size=\"20\" value=\"" . makeShortDate($row['birthday']) . "\" />
															</li>
															<li>
																<label for=\"website\">Website:</label>
																<input type=\"text\" name=\"website\" id=\"website\" size=\"60\" value=\"" . $row['website'] . "\" />
															</li>
															<li>
																<label for=\"country\">Country:</label>
																<input type=\"text\" name=\"country\" id=\"country\" size=\"60\" value=\"" . $row['country'] . "\" />
															</li>
														</ul>
													</fieldset>
													<fieldset>
														<h3>Instant Messaging</h3>
														<ul>
															<li>
																<label for=\"aim\">AIM:</label>
																<input type=\"text\" name=\"aim\" id=\"aim\" size=\"60\" value=\"" . $row['aim'] . "\" />
															</li>
															<li>
																<label for=\"yim\">Yahoo:</label>
																<input type=\"text\" name=\"yim\" id=\"yim\" size=\"60\" value=\"" . $row['yim'] . "\" />
															</li>
															<li>
																<label for=\"msn\">MSN:</label>
																<input type=\"text\" name=\"msn\" id=\"msn\" size=\"60\" value=\"" . $row['msn'] . "\" />
															</li>
															<li>
																<label for=\"icq\">ICQ:</label>
																<input type=\"text\" name=\"icq\" id=\"icq\" size=\"60\" value=\"" . $row['icq'] . "\" />
															</li>
														</ul>
													</fieldset>
													<fieldset>
														<h3>Current Avatar</h3>
														<div id=\"currentAvatar\">
															<img src=\"" . ((empty($row['avatar'])) ? $safs_config['ftssafs_default_avatar'] : $row['avatar']) . "\" alt=\"\" />
														</div>
													</fieldset>
													<fieldset>
														<h3>Modify Avatar</h3>
														<ul>
															" . ((!$safs_config['ftssafs_allow_avatar_remote']) ? "" : "
															<li>
																<label for=\"avatar\">URL:</label>
																<input type=\"text\" name=\"avatar\" id=\"avatar\" size=\"60\" value=\"" . $row['avatar'] . "\" />
															</li>"
															)
															. ((!$safs_config['ftssafs_allow_avatar_local']) ? "" : "
															<li>
																<label for=\"gallery\">Gallery:</label>
																<div id=\"avatar\">
																	" . createDropdown("avatarGalleries", "avatarGalleryFolder", "", "updateGalleryImages()") . "
																	<div id=\"avatarGalleryHolder\">Choose a Gallery Folder to show available Avatars.</div>
																</div>
															</li>"
															)
															. ((!$safs_config['ftssafs_allow_avatar_upload']) ? "" : "
															<li>
																<label for=\"upload\">Upload an Avatar:</label>
																<div id=\"upload\" class=\"clear\">
																	<div>Maximum Size is: " . $safs_config['ftssafs_avatar_max_width'] . "x" . $safs_config['ftssafs_avatar_max_height'] . " and can be no bigger than " . ($safs_config['ftssafs_avatar_filesize'] / 1000) . "KB.</div>
																	<input type=\"file\" name=\"uploadedAvatar\" id=\"uploadedAvatar\" size=\"60\" value=\"\" />
																</div>
															</li>"
															)
															. (($safs_config['ftssafs_allow_avatar_remote'] && $safs_config['ftssafs_allow_avatar_local'] && $safs_config['ftssafs_allow_avatar_upload']) ? "" : "
															The Board Administrator has disabled the use of avatars."
															) . "
														</ul>
													</fieldset>
													<fieldset>
														<h3>Current Signature</h3>
														" . bbcode($row['signature']) . "
													</fieldset>
													<fieldset>
														<h3>Modify Signature</h3>
														" . ((!$safs_config['ftssafs_allow_sig']) ? "The Board Administrator has disabled the use of signatures." : bbcode_box($row['signature'])) . "
													</fieldset>
													<fieldset class=\"buttonRow\">
														<input type=\"submit\" name=\"submit\" value=\"Save Profile\" />
													</fieldset>
												</form>
											</div>";
			}
			$DB->free_result($result2);
		}
		
		// Return the HTML
		return $returnVar;
	}
	
	//=================================================
	// Returns the JQuery functions used to allow 
	// in-place editing and table sorting
	//=================================================
	function returnEditProfileFormJQuery() {
		global $DB, $menuvar, $safs_config;
					
		$JQueryReadyScripts = "
			$('#birthday').datepicker({
				showButtonPanel: true
			});
			var v = jQuery(\"#editProfileForm\").validate({
				errorElement: \"div\",
				errorClass: \"validation-advice\",
				rules: {
					password2: {
						equalTo: \"#password1\"
					}
				},
				messages: {
					password2: {
						equalTo: \"Your passwords do not match!\"
					}
				}
			});";
		
		return $JQueryReadyScripts;
	}

 	//=================================================
	// Process the Edit Profile Form
	//=================================================
	function processEditProfileForm($data) {
		global $DB, $menuvar, $safs_config;
		$errors = "";
		$allowedTypes = array("jpeg", "jpg", "png", "gif");
		
		// Remote avatars are the default choice
		$avatar = (!empty($data['avatar'])) ? $data['avatar'] : "";
		
		// If we spent time choosing a gallery avatar use it instead
		$avatar = (isset($data['galleryAvatar']) && !empty($data['galleryAvatar'])) ? $data['galleryAvatar'] : $avatar;
				
		// If we uploaded an image check to make sure it meets requirements
		if (!empty($_FILES['uploadedAvatar']['name'])) {
			$tempImageLocation = $_FILES['uploadedAvatar']['tmp_name'];
			$path_info = pathinfo($_FILES['uploadedAvatar']['name']);
			$fileName = $path_info['filename']; 
			$fileExt = $path_info['extension']; 
			
			// File size check
			$errors .= ($_FILES['uploadavatar']['size'] > $safs_config['ftssafs_avatar_filesize']) ? "<span class=\"error\">The file size is over " . ($safs_config['ftssafs_avatar_filesize'] / 1000) . "KB.</span>" : "";
			
			// File extension check
			$errors .= (!in_array($fileExt, $allowedTypes)) ? "<span class=\"error\">." . $fileExt . " files are not allowed to be uploaded.</span>" : "";
			
			// File dimensions check
			list($width, $height) = getimagesize($tempImageLocation);
			$errors .= ($width > $safs_config['ftssafs_avatar_max_width']) ? "<span class=\"error\">Your image is wider than the maximum width allowed. Your image: " . $width . " Max: " . $safs_config['ftssafs_avatar_max_width'] . ".</span>" : "";
			$errors .= ($height > $safs_config['ftssafs_avatar_max_height']) ? "<span class=\"error\">Your image is taller than the maximum height allowed. Your image: " . $height . " Max: " . $safs_config['ftssafs_avatar_max_height'] . ".</span>" : "";
			
			// All checks passed
			if (empty($errors)) {
				$randName = md5(rand() * time()); // make a random filename
				$filePath = $safs_config['ftssafs_avatar_path'] . "upload/" . $randName . $fileExt;
				
				// Upload the file
				$uploadResult = move_uploaded_file($tempImageLocation, $filePath);
				
				if (!$uploadResult) {
					$errors .= "<span class=\"error\">Unable to upload your avatar.</span>";
				}
				else {
					$avatar = $filePath;
				}
			}
		}

		// Update our user account
		// Dont escape since this method does that, double escape isn't fun
		$result = $DB->query_update("users", array(
												'first_name' => $data['firstName'], 
												'last_name' => $data['lastName'], 
												'title' => $data['title'], 
												'email_address' => $data['emailAddress'], 
												'gender' => $data['gender'], 
												'birthday' => strtotime($data['birthday']), 
												'website' => $data['website'], 
												'country' => $data['country'], 
												'aim' => $data['aim'], 
												'yim' => $data['yim'], 
												'msn' => $data['msn'], 
												'icq' => $data['icq'], 
												'avatar' => $avatar, 
												'signature' => $data['message']
											), "id = '" . $_SESSION['userid'] . "'");
		
		
		// Update our password if need be
		// Dont escape since this method does that, double escape isn't fun
		if (!empty($data['password1'])) {
			if ($data['password1'] == $data['password2']) {
				$result2 = $DB->query_update("users", array('password' => md5($data['password'])), "id = '" . $_SESSION['userid'] . "'");
			}
			else {
				$errors .= "<span class=\"error\">Your Passwords do not match, your password has not been updated.</span>";
			}
		}
		
		$returnVar = "
											<h2 class=\"processFormTitle\">Edit Profile</h2>
											<div class=\"processFormBlock " . (($result) ? "processFormSuccess" : "processFormFailed") . "\">
												" . ((!empty($errors)) ? $errors : "") . "
												" . (($result) ? "Your profile has been updated and you're being redirected to the Edit Profile page." : "There was an error updating your profile, you're being redirected to the Edit Profile page.") . "
												<meta http-equiv=\"refresh\" content=\"3;url=" . $menuvar['PROFILE'] . "\">
											</div>";
		
		// Return the HTML
		return $returnVar;
	}

?>