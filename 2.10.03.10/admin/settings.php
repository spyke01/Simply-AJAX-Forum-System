<? 
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
 *
 * This program is licensed under the Fast Track Sites Program license 
 * located inside the license.txt file included with this program. This is a 
 * legally binding license, and is protected by all applicable laws, by 
 * editing this page you fall subject to these licensing terms.
 *
 ***************************************************************************/

if($_SESSION['username'] && $_SESSION['user_level'] == ADMIN) {
	if(isset($_POST[submit])) {
		foreach($_POST as $name => $value) {
			if ($name != 'submit'){				
				if ($name == "ftssafs_announcement_text") { $sql = "UPDATE `" . $DBTABLEPREFIX . "config` SET config_extra_value = '$value' WHERE config_name = '$name'"; }
				else { $sql = "UPDATE `" . $DBTABLEPREFIX . "config` SET config_value = '$value' WHERE config_name = '$name'"; }
				
				$result = mysql_query($sql);
			}
		}		
		unset($_POST[submit]);
	}
	$sql = "SELECT * FROM `" . $DBTABLEPREFIX . "config`";
	$result = mysql_query($sql);
	
	if (mysql_num_rows($result) == '0') {
		$page_content .= "\n Error reading database $sql";
	}
	else {
		while ($row = mysql_fetch_array($result)) {			
			if ($row['config_name'] == "ftssafs_announcement_text") { $current_config[$row['config_name']] = $row['config_extra_value']; }
			else { $current_config[$row['config_name']] = $row['config_value']; }
		}	
			extract($current_config);
			
			$page_content .= "\n<form action=\"index.php?p=admin&s=settings\" method=\"post\">
									<table class=\"forumborder\" cellspacing=\"1\" cellpadding=\"0\" width=\"600\">
										<tr class=\"title1\">
											<td colspan=\"2\">Board Settings</td>
										</tr>
										<tr class=\"title2\">
											<td colspan=\"2\">General Configuration</td>
										</tr>
										<tr class=\"row1\">
											<td><strong>Board Name:</strong></td>
											<td>
												<input type=\"text\" size=\"60\" name=\"ftssafs_board_name\" value=\"$ftssafs_board_name\" />
											</td>
										</tr>
										<tr class=\"row1\">
											<td><strong>Board Description:</strong></td>
											<td>
												<textarea name=\"ftssafs_site_desc\" cols=\"45\" rows=\"10\">$ftssafs_site_desc</textarea>
											</td>
										</tr>
										<tr class=\"row1\">
											<td><strong>Board Style:</strong></td>
											<td>
												<input type=\"text\" size=\"60\" name=\"ftssafs_theme\" value=\"$ftssafs_theme\" />
											</td>
										</tr>
										<tr class=\"row1\">
											<td><strong>Board Email:</strong></td>
											<td>
												<input type=\"text\" size=\"60\" name=\"ftssafs_board_email\" value=\"$ftssafs_board_email\" />
											</td>
										</tr>
										<tr class=\"row1\">
											<td><strong>Board Email Signature:</strong></td>
											<td>
												<input type=\"text\" size=\"60\" name=\"ftssafs_board_email_sig\" value=\"$ftssafs_board_email_sig\" />
											</td>
										</tr>
										<tr class=\"row1\">
											<td><strong>Board Location:</strong></td>
											<td>
												<input type=\"text\" size=\"60\" name=\"ftssafs_board_url\" value=\"$ftssafs_board_url\" />
											</td>
										</tr>
										<tr class=\"row1\">
											<td><strong>Is the board active?</strong></td>
											<td>
												<select name=\"ftssafs_active\">
													<option value=\"0\"" . testSelected($ftssafs_active, 0) . ">No</option>
													<option value=\"1\"" . testSelected($ftssafs_active, 1) . ">Yes</option>
												</select>
											</td>
										</tr>
										<tr class=\"row1\">
											<td><strong>Require account activation via email?</strong></td>
											<td>
												<select name=\"ftssafs_activation_active\">
													<option value=\"0\"" . testSelected($ftssafs_activation_active, 0) . ">No</option>
													<option value=\"1\"" . testSelected($ftssafs_activation_active, 1) . ">Yes</option>
												</select>
											</td>
										</tr>
										<tr class=\"title2\">
											<td colspan=\"2\">Message Configuration</td>
										</tr>
										<tr class=\"row1\">
											<td><strong>Inactive Message:</strong></td>
											<td>
												<textarea name=\"ftssafs_inactive_msg\" cols=\"45\" rows=\"10\">$ftssafs_inactive_msg</textarea>
											</td>
										</tr>
										<tr class=\"row1\">
											<td><strong>Announcement Title:</strong></td>
											<td>
												<input type=\"text\" size=\"60\" name=\"ftssafs_announcement_title\" value=\"$ftssafs_announcement_title\" />
											</td>
										</tr>
										<tr class=\"row1\">
											<td><strong>Announcement Text:</strong></td>
											<td>
												<textarea name=\"ftssafs_announcement_text\" cols=\"45\" rows=\"10\">$ftssafs_announcement_text</textarea>
											</td>
										</tr>
										<tr class=\"title2\">
											<td colspan=\"2\">Size Configurations</td>
										</tr>
										<tr class=\"row1\">
											<td><strong>Max Sig Length:</strong></td>
											<td>
												<input type=\"text\" size=\"60\" name=\"ftssafs_max_sig_chars\" value=\"$ftssafs_max_sig_chars\" />
											</td>
										</tr>
										<tr class=\"row1\">
											<td><strong>Max Avatar Width:</strong></td>
											<td>
												<input type=\"text\" size=\"60\" name=\"ftssafs_avatar_max_width\" value=\"$ftssafs_avatar_max_width\" />
											</td>
										</tr>
										<tr class=\"row1\">
											<td><strong>Max Avatar Height:</strong></td>
											<td>
												<input type=\"text\" size=\"60\" name=\"ftssafs_avatar_max_height\" value=\"$ftssafs_avatar_max_height\" />
											</td>
										</tr>
										<tr class=\"row1\">
											<td><strong>Max Avatar Filesize:</strong></td>
											<td>
												<input type=\"text\" size=\"60\" name=\"ftssafs_avatar_filesize\" value=\"$ftssafs_avatar_filesize\" />
											</td>
										</tr>
										<tr class=\"title2\">
											<td colspan=\"2\">Path Configurations</td>
										</tr>
										<tr class=\"row1\">
											<td><strong>Avatar Path:</strong></td>
											<td>
												<input type=\"text\" size=\"60\" name=\"ftssafs_avatar_path\" value=\"$ftssafs_avatar_path\" />
											</td>
										</tr>
										<tr class=\"row1\">
											<td><strong>Avatar Gallery Path:</strong></td>
											<td>
												<input type=\"text\" size=\"60\" name=\"ftssafs_avatar_gallery_path\" value=\"$ftssafs_avatar_gallery_path\" />
											</td>
										</tr>
										<tr class=\"row1\">
											<td><strong>Smiley Path:</strong></td>
											<td>
												<input type=\"text\" size=\"60\" name=\"ftssafs_smilies_path\" value=\"$ftssafs_smilies_path\" />
											</td>
										</tr>
										<tr class=\"row1\">
											<td><strong>Topic Icon Path:</strong></td>
											<td>
												<input type=\"text\" size=\"60\" name=\"ftssafs_topic_icons_path\" value=\"$ftssafs_topic_icons_path\" />
											</td>
										</tr>
										<tr class=\"row1\">
											<td><strong>Ranks Path:</strong></td>
											<td>
												<input type=\"text\" size=\"60\" name=\"ftssafs_ranks_path\" value=\"$ftssafs_ranks_path\" />
											</td>
										</tr>
										<tr class=\"title1\">
											<td colspan=\"2\">
												<center><input type=\"submit\" name=\"submit\" class=\"button\" value=\"Submit\" /></center>			
											</td>
									</table>
								</form>";	
	}

}

$page->setTemplateVar("PageContent", $page_content);
?>