<? 
/***************************************************************************
 *                               settings.php
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

if ($_SESSION['user_level'] == SYSTEM_ADMIN) {
	// Handle updating system variables in the database
	if (isset($_POST['submit'])) {		
		foreach($_POST as $name => $value) {
			if ($name != "submit"){			
				if ($name == "ftssafs_active") {
					if (empty($value)) { $value = 0; }
					else { $value = 1; }	
				}
				// Dont escape since this method does that, double escape isn't fun
				$result = $DB->query_update("config", array('value' => $value), "name = '" . keeptasafe($name) . "'");
			}
		}		
		
		// Handle checkboxes, unchecked boxes are not posted so we check for this and mark them in the DB as such
		if (!isset($_POST['ftssafs_active'])) {
			// Dont escape since this method does that, double escape isn't fun
			$result = $DB->query_update("config", array('value' => '0'), "name = 'ftssafs_active'");
		}
		if (!isset($_POST['ftssafs_allow_bbcode'])) {
			// Dont escape since this method does that, double escape isn't fun
			$result = $DB->query_update("config", array('value' => '0'), "name = 'ftssafs_allow_bbcode'");
		}
		if (!isset($_POST['ftssafs_allow_smilies'])) {
			// Dont escape since this method does that, double escape isn't fun
			$result = $DB->query_update("config", array('value' => '0'), "name = 'ftssafs_allow_smilies'");
		}
		if (!isset($_POST['ftssafs_allow_avatar_local'])) {
			// Dont escape since this method does that, double escape isn't fun
			$result = $DB->query_update("config", array('value' => '0'), "name = 'ftssafs_allow_avatar_local'");
		}
		if (!isset($_POST['ftssafs_allow_avatar_remote'])) {
			// Dont escape since this method does that, double escape isn't fun
			$result = $DB->query_update("config", array('value' => '0'), "name = 'ftssafs_allow_avatar_remote'");
		}
		if (!isset($_POST['ftssafs_allow_avatar_upload'])) {
			// Dont escape since this method does that, double escape isn't fun
			$result = $DB->query_update("config", array('value' => '0'), "name = 'ftssafs_allow_avatar_upload'");
		}
		if (!isset($_POST['ftssafs_allow_sig'])) {
			// Dont escape since this method does that, double escape isn't fun
			$result = $DB->query_update("config", array('value' => '0'), "name = 'ftssafs_allow_sig'");
		}
		if (!isset($_POST['ftssafs_privmsg_active'])) {
			// Dont escape since this method does that, double escape isn't fun
			$result = $DB->query_update("config", array('value' => '0'), "name = 'ftssafs_privmsg_active'");
		}
		
		unset($_POST['submit']);
	}
	
	// Pull the curent variables since we can't trust oir safs_config to carry the latest
	$current_config = array();
	
	$sql = "SELECT * FROM `" . DBTABLEPREFIX . "config`";
	$result = mysql_query($sql);
	
	// This is used to let us get the actual items and not just name and value
	if ($result && mysql_num_rows($result) > 0) {
		while ($row = mysql_fetch_array($result)) {
			$name = $row['name'];
			$value = $row['value'];
			$current_config[$name] = $value;
		}
		mysql_free_result($result);
	}
		
	// Give our template the values
	$page_content .= "
				<form action=\"" . $menuvar['SETTINGS'] . "\" method=\"post\" class=\"inputForm\">
					<div id=\"tabs\">
						<ul>
							<li><a href=\"#systemSettings\"><span>System Settings</span></a></li>
							<li><a href=\"#emailSettings\"><span>Email Settings</span></a></li>
							<li><a href=\"#avatarSettings\"><span>Avatar Settings</span></a></li>
							<li><a href=\"#signatureSettings\"><span>Signature Settings</span></a></li>
							<li><a href=\"#inboxSettings\"><span>Inbox Settings</span></a></li>
							<li><a href=\"#paths\"><span>Paths</span></a></li>
						</ul>
						<div id=\"systemSettings\">
							<fieldset>
								<legend>System Settings</legend>
								<div><label for=\"ftssafs_active\">Active </label> <input name=\"ftssafs_active\" type=\"checkbox\" value=\"1\"". testChecked($current_config['ftssafs_active'], ACTIVE) . " /></div>
								<div><label for=\"ftssafs_inactive_msg\">Inactive Message </label> <textarea name=\"ftssafs_inactive_msg\" cols=\"45\" rows=\"10\">" . $current_config['ftssafs_inactive_msg'] . "</textarea></div>
								<div><label for=\"ftssafs_time_zone\">System Time Zone </label> " . createDropdown("timezone", "ftssafs_time_zone", $current_config['ftssafs_time_zone'], "") . "</div>
								<div><label for=\"ftssafs_cookie_name\">Cookie Name </label> <input type=\"text\" name=\"ftssafs_cookie_name\" id=\"ftssafs_cookie_name\" size=\"60\" value=\"" . $current_config['ftssafs_cookie_name'] . "\" /></div>
								<div><label for=\"ftssafs_allow_bbcode\">Allow BBCode </label> <input name=\"ftssafs_allow_bbcode\" type=\"checkbox\" value=\"1\"". testChecked($current_config['ftssafs_allow_bbcode'], ACTIVE) . " /></div>
								<div><label for=\"ftssafs_allow_smilies\">Allow Smilies </label> <input name=\"ftssafs_allow_smilies\" type=\"checkbox\" value=\"1\"". testChecked($current_config['ftssafs_allow_smilies'], ACTIVE) . " /></div>
								<div><label for=\"ftssafs_items_per_page\">Items (Topics, Users, etc) Per Page </label> " . createDropdown("itemsPerPage", "ftssafs_items_per_page", $current_config['ftssafs_items_per_page'], "") . "</div>
								<div><label for=\"ftssafs_announcement_title\">Announcement Title </label> <input type=\"text\" name=\"ftssafs_cookie_name\" id=\"ftssafs_cookie_name\" size=\"60\" value=\"" . $current_config['ftssafs_cookie_name'] . "\" /></div>
								<div><label for=\"ftssafs_announcement_text\">Announcement Text </label> <textarea name=\"ftssafs_announcement_text\" cols=\"45\" rows=\"10\">" . $current_config['ftssafs_announcement_text'] . "</textarea></div>
							</fieldset>
						</div>
						<div id=\"emailSettings\">
							<fieldset>
								<legend>Email Settings</legend>
								<div><label for=\"ftssafs_board_email_sig\">Email Signature </label> <textarea name=\"ftssafs_board_email_sig\" cols=\"45\" rows=\"10\">" . $current_config['ftssafs_board_email_sig'] . "</textarea></div>
							</fieldset>
						</div>
						<div id=\"avatarSettings\">
							<fieldset>
								<legend>Avatar Settings</legend>
								<div><label for=\"ftssafs_allow_avatar_local\">Allow Local Avatars </label> <input name=\"ftssafs_allow_avatar_local\" type=\"checkbox\" value=\"1\"". testChecked($current_config['ftssafs_allow_avatar_local'], ACTIVE) . " /></div>
								<div><label for=\"ftssafs_allow_avatar_remote\">Allow Remote Avatars </label> <input name=\"ftssafs_allow_avatar_remote\" type=\"checkbox\" value=\"1\"". testChecked($current_config['ftssafs_allow_avatar_remote'], ACTIVE) . " /></div>
								<div><label for=\"ftssafs_allow_avatar_upload\">Allow Avatar Upload </label> <input name=\"ftssafs_allow_avatar_upload\" type=\"checkbox\" value=\"1\"". testChecked($current_config['ftssafs_allow_avatar_upload'], ACTIVE) . " /></div>
								<div><label for=\"ftssafs_default_avatar\">Default Avatar </label> <input type=\"text\" name=\"ftssafs_default_avatar\" id=\"ftssafs_default_avatar\" size=\"60\" value=\"" . $current_config['ftssafs_default_avatar'] . "\" /></div>
								<div><label for=\"ftssafs_avatar_filesize\">Max Filesize </label> <input type=\"text\" name=\"ftssafs_avatar_filesize\" id=\"ftssafs_avatar_filesize\" size=\"60\" value=\"" . $current_config['ftssafs_avatar_filesize'] . "\" /></div>
								<div><label for=\"ftssafs_avatar_max_width\">Max Width </label> <input type=\"text\" name=\"ftssafs_avatar_max_width\" id=\"ftssafs_avatar_max_width\" size=\"60\" value=\"" . $current_config['ftssafs_avatar_max_width'] . "\" /></div>
								<div><label for=\"ftssafs_avatar_max_height\">Max Height </label> <input type=\"text\" name=\"ftssafs_avatar_max_height\" id=\"ftssafs_avatar_max_height\" size=\"60\" value=\"" . $current_config['ftssafs_avatar_max_height'] . "\" /></div>
							</fieldset>
						</div>
						<div id=\"signatureSettings\">
							<fieldset>
								<legend>Signature Settings</legend>
								<div><label for=\"ftssafs_allow_sig\">Allow Signatures </label> <input name=\"ftssafs_allow_sig\" type=\"checkbox\" value=\"1\"". testChecked($current_config['ftssafs_allow_sig'], ACTIVE) . " /></div>
								<div><label for=\"ftssafs_max_sig_chars\">Max Length </label> <input type=\"text\" name=\"ftssafs_max_sig_chars\" id=\"ftssafs_max_sig_chars\" size=\"60\" value=\"" . $current_config['ftssafs_max_sig_chars'] . "\" /></div>
							</fieldset>
						</div>
						<div id=\"inboxSettings\">
							<fieldset>
								<legend>Inbox Settings</legend>
								<div><label for=\"ftssafs_privmsg_active\">Private Messaging Active </label> <input name=\"ftssafs_privmsg_active\" type=\"checkbox\" value=\"1\"". testChecked($current_config['ftssafs_privmsg_active'], ACTIVE) . " /></div>
								<div><label for=\"ftssafs_max_inbox_privmsgs\">Max Inbox Messages </label> <input type=\"text\" name=\"ftssafs_max_inbox_privmsgs\" id=\"ftssafs_max_inbox_privmsgs\" size=\"60\" value=\"" . $current_config['ftssafs_max_inbox_privmsgs'] . "\" /></div>
								<div><label for=\"ftssafs_max_sent_privmsgs\">Max Sent Messages </label> <input type=\"text\" name=\"ftssafs_max_sent_privmsgs\" id=\"ftssafs_max_sent_privmsgs\" size=\"60\" value=\"" . $current_config['ftssafs_max_sent_privmsgs'] . "\" /></div>
								<div><label for=\"ftssafs_max_archived_privmsgs\">Max Archived Messages </label> <input type=\"text\" name=\"ftssafs_max_archived_privmsgs\" id=\"ftssafs_max_archived_privmsgs\" size=\"60\" value=\"" . $current_config['ftssafs_max_archived_privmsgs'] . "\" /></div>
							</fieldset>
						</div>
						<div id=\"paths\">
							<fieldset>
								<legend>Paths</legend>
								<div><label for=\"ftssafs_avatar_path\">Avatars </label> <input type=\"text\" name=\"ftssafs_avatar_path\" id=\"ftssafs_avatar_path\" size=\"60\" value=\"" . $current_config['ftssafs_avatar_path'] . "\" /></div>
								<div><label for=\"ftssafs_avatar_gallery_path\">Avatar Gallery </label> <input type=\"text\" name=\"ftssafs_avatar_gallery_path\" id=\"ftssafs_avatar_gallery_path\" size=\"60\" value=\"" . $current_config['ftssafs_avatar_gallery_path'] . "\" /></div>
								<div><label for=\"ftssafs_smilies_path\">Smilies </label> <input type=\"text\" name=\"ftssafs_smilies_path\" id=\"ftssafs_smilies_path\" size=\"60\" value=\"" . $current_config['ftssafs_smilies_path'] . "\" /></div>
								<div><label for=\"ftssafs_topic_icons_path\">Topic Icons </label> <input type=\"text\" name=\"ftssafs_topic_icons_path\" id=\"ftssafs_topic_icons_path\" size=\"60\" value=\"" . $current_config['ftssafs_topic_icons_path'] . "\" /></div>
								<div><label for=\"ftssafs_ranks_path\">Ranks </label> <input type=\"text\" name=\"ftssafs_ranks_path\" id=\"ftssafs_ranks_path\" size=\"60\" value=\"" . $current_config['ftssafs_ranks_path'] . "\" /></div>
							</fieldset>
						</div>
					</div>
					<div class=\"clear center\"><input type=\"submit\" name=\"submit\" class=\"button\" value=\"Update Settings\" /></div>
				</form>";
				
	$JQueryReadyScripts .= "$(\"#tabs\").tabs();";

	$page->setTemplateVar("PageContent", $page_content);
	$page->setTemplateVar("JQueryReadyScript", $JQueryReadyScripts);
}
else {
	$page->setTemplateVar('PageContent', "\nYou Are Not Authorized To Access This Area. Please Refrain From Trying To Do So Again.");
}
?>