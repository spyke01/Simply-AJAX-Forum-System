 <? 
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
 *
 * This program is licensed under the Fast Track Sites Program license 
 * located inside the license.txt file included with this program. This is a 
 * legally binding license, and is protected by all applicable laws, by 
 * editing this page you fall subject to these licensing terms.
 *
 ***************************************************************************/


$avatarpath = "./images/avatars"; //our avatar directory
$uploadDir = $avatarpath . "/upload"; //our upload directory
$maxSize = 2000000; // Mmaximum file size (In MB).                            
$allowedTypes  = array('.jpeg','.jpg','.png','.gif'); 
$message = "";

$usernameToUpdate = $_SESSION['username'];
$action = $_GET['action'];
$id = $_REQUEST['id'];
$avatar = $_GET['avatar'];
$category = $_POST['category'];
$changeavatar = $_POST['changeavatar'];


//Make safe in case user tried hacking board
$action = parseurl($action);
$id = parseurl($id);
$category = parseurl($category);
$changeavatar = parseurl($changeavatar);

// Allow admins to change a users info
$sql = "SELECT users_username FROM `" . $DBTABLEPREFIX . "users` WHERE users_id = '$id' LIMIT 1";
$result = mysql_query($sql);
if (($row = mysql_fetch_array($result)) && $_SESSION['user_level'] == ADMIN) {
	$usernameToUpdate = $row['users_username'];
}
mysql_free_result($result);

//************************************************************
// Create Upload Directory
//************************************************************
if (!is_dir($uploadDir)) {
	if (!mkdir($uploadDir)) {
	echo $uploadDir;
		die ("Unable to create upload directory.");
	}
	if (!chmod($uploadDir,0755)) {
		die ("Changing upload directory permissions to 755 failed.");
	}
}

//============================================
// Displays all of our avatars to choose from
//============================================
if ($action == 'changeavatar' || $changeavatar == 'Choose') {
	if ($_SESSION['username']) {
		
		$page_content .= "<b><u>Please Choose an Avatar From Below:</u></b><br /><br />";
		
		// Gets all the images in $avatarpath
		if($dir = opendir($avatarpath)){
			
			$x = '0';
			$avatar_images = array();
			$sub_dir_names = array();
			
		
			while (false !== ($file = readdir($dir))) {
			
				//select only dirrectories
				if ($file != "." && $file != ".." && !is_file($avatarpath . '/' . $file) && !is_link($avatarpath . '/' . $file)) {
					
					//holds our directories so that we can get our files
					$sub_dir = @opendir($avatarpath . '/' . $file);
					$sub_dir_names[$file] .= '';
					
					//gets all images in all directories
					while( $sub_file = @readdir($sub_dir) )
					{
						if( preg_match('/(\.gif$|\.png$|\.jpg|\.jpeg)$/is', $sub_file) )
						{	
							if ($file != $last_file) { $x =0; }
							if ($x == '5') { //actually displays 5 images because we count 0
								$avatar_images[$file] .= "\n</tr>\n<tr class='row1'>\n<td><a href=\"index.php?p=profile&action=updateavatar&id=$id&avatar=$file/$sub_file\"><img src=\"$avatarpath/$file/$sub_file\" border=\"0\" alt='' /></a></td>";
								$x = '1';
							}
							else {
								$avatar_images[$file] .= "\n<td><a href=\"index.php?p=profile&action=updateavatar&id=$id&avatar=$file/$sub_file\"><img src=\"$avatarpath/$file/$sub_file\" border=\"0\" alt='' /></a></td>";
								$x++;
							}
							$last_file = $file;
						}
					}
				}
			}
			
			
			//ksort($data); //sort by name
			$page_content .= "\n<form action=\"index.php?p=profile&action=changeavatar&id=$id\" method=\"post\">
								<b>Category:</b><select name='category'>";
								
			foreach($sub_dir_names as $key => $value) { 			
				$selected = ( $key == $category ) ? ' selected="selected"' : '';
				$page_content .= "\n<option value='$key'$selected>" . ucfirst($key) . "</option>";
				
			} //print out the link url for the img
			
			$page_content .= "\n</select>
								<input class='button' type='submit' value='Go!'><br />				
								</form><br />";
		
		}
		
		if (isset($category)) {
			foreach($avatar_images as $k => $v) { 
				if ($category == $k) {
					$page_content .= "\n<center><table class='PChangeAvatarForumBorder' border='0' cellpadding='0' cellspacing='1'>
										<tr class='title1'>
										  <td colspan='5'>$k Avatars</td>	
										</tr>
										<tr class='title2'>
										  <td colspan='5'>&nbsp;</td>
										</tr>					
										<tr class='row1'>
										$v
										</tr>
										<tr class='title1'>
										  <td colspan='5'>&nbsp;</td>
										</tr>
										</table></center>";
				}				
			} //print out the link url for the img
			$page_content .= "<br />";
		}
		else {
			$page_content .= "\nPlease Select a category<br />";
		}
	}
	else { $page_content .= "You are not authorized to change avatars!"; }
}


//============================================
// Changes our avatar for us ^^
//============================================
elseif ($action == 'updateavatar' && isset($avatar)) {
	if ($_SESSION['username']) {
		if(isset($avatar)) {
			$avatar = $avatarpath . '/' . $avatar;
			$avatar = str_replace(array('../', '..\\', './', '.\\'), '', $avatar);
			$sql = "UPDATE `" . $DBTABLEPREFIX . "users` SET users_avatar='$avatar', users_avatar_type='1' WHERE users_username='$usernameToUpdate'";
			$result = mysql_query($sql);
			
			if(!$result){
				$page_content .= 'There has been an error editing your avatar. Please contact the webmaster.';
			} 
			else {
				$page_content .= "\n<img src='$avatar' alt='' /><br />
									Your avatar has been changed, and you are being redirected back to the editprofile page.
		 							<meta http-equiv='refresh' content='3;url=index.php?p=profile&action=editprofile&id=$id'>";
			}
		}
		else {
			$page_content .= "Your avatar has not been changed due to an error, and you are being redirected back to the editprofile page.
		 						<meta http-equiv='refresh' content='3;url=index.php?p=profile&action=editprofile&id=$id'>";
		}
	}
	else { $page_content .= "You must login to change your avatar!"; }
}

//============================================
// View users profile information
//============================================
elseif ($action == 'viewprofile' && isset($id)) { //BEGIN VIEWPROFILE SECTION
	$result = mysql_query("SELECT * FROM `" . $DBTABLEPREFIX . "users` WHERE users_id = '$id' LIMIT 1");
	
	while($r=mysql_fetch_array($result)){
		$editLink = ($_SESSION['user_level'] == ADMIN) ? "<div style=\"float: right;\"><a href=\"index.php?p=profile&action=editprofile&id=" . $r['users_id'] ."\">Edit profile</a></div>" : "";
	$page_content .= "\n	<center>
		<table class=\"PForumBorder\" border=\"0\" cellpadding=\"0\" cellspacing=\"1\">
			<tr class=\"title1\">
				<td class=\"PVPT1\">
					$editLink
					$T_Viewing_Profile $r[users_username]
				</td>
			</tr>
		
			<tr class=\"row1\">  
				<td align=\"left\" valign=\"top\" width=\"100%\">
			       <img src=\"$r[users_avatar]\" alt=\"\" /><br />";
	rank_title("pageContent", $r['users_username']);
	$page_content .= "\n		    </td>
			</tr>
		</table>
		<br /><br />
		
		<table class=\"PForumWidth\" border=\"0\" cellpadding=\"0\" cellspacing=\"1\">
			<tr>
				<!-- STATS -->
				<td class=\"PForumWidthColumn1\">
					<table class=\"PForumColumn1Border\" border=\"0\" cellpadding=\"0\" cellspacing=\"1\">
						<tr class=\"title1\">		
							<td class=\"PVPT1\" colspan=\"2\">$T_Active_Stats</td>
						</tr>
						<tr class=\"row1\">		
							<td class=\"PVPT2Column1\">$T_Total_Posts</td>
							<td class=\"PVPR1Column1\">$r[users_posts]</td>
						</tr>
						<tr class=\"row1\">
							<td class=\"PVPT2Column1\">$T_Last_Active</td>
							<td class=\"PVPR1Column1\">" . makeDate($r[users_last_login]) . "</td>
						</tr>
						<tr class=\"row1\">
							<td class=\"PVPT2Column1\">&nbsp;</td>
							<td class=\"PVPR1Column1\">&nbsp;</td>
						</tr>
						<tr class=\"row1\">
							<td class=\"PVPT2Column1\">&nbsp;</td>
							<td class=\"PVPR1Column1\">&nbsp;</td>
						</tr>
						<tr class=\"row1\">
							<td class=\"PVPT2Column1\">&nbsp;</td>
							<td class=\"PVPR1Column1\">&nbsp;</td>
						</tr>
						<tr class=\"row1\">
							<td class=\"PVPT2Column1\">&nbsp;</td>
							<td class=\"PVPR1Column1\">&nbsp;</td>
						</tr>
					</table>
				</td>
				<!-- Communication -->
				<td class=\"PForumWidthColumn2\">
					<table class=\"PForumColumn2Border\" border=\"0\" cellpadding=\"0\" cellspacing=\"1\">
						<tr class=\"title1\">
							<td class=\"PVPT1\" colspan=\"2\">$T_Communication</td>
						</tr>
						<tr class=\"row1\">
							<td class=\"PVPT2Column1\"><img src=\"$themedir/buttons/profile_aim.gif\" border=\"0\"  alt=\"AIM\" /></td>
							<td class=\"PVPR1Column1\"><a href=\"aim:goim?screenname=$r[users_aim]&message=Hi.+Are+you+there?\" target=\"_blank\">$r[users_aim]</a></td>
						</tr>
						<tr class=\"row1\">
							<td class=\"PVPT2Column1\"><img src=\"$themedir/buttons/profile_yahoo.gif\" border=\"0\"  alt=\"Yahoo\" /></td>		
							<td class=\"PVPR1Column1\"><a href=\"http://edit.yahoo.com/config/send_webmesg?.target=$r[users_yim]\" target=\"_blank\">$r[users_yim]</a></td>
						</tr>
						<tr class=\"row1\">
							<td class=\"PVPT2Column1\"><img src=\"$themedir/buttons/profile_msn.gif\" border=\"0\"  alt=\"MSN\" /></td>
							<td class=\"PVPR1Column1\"><a href=\"http://members.msn.com/$row[users_msn]\" target=\"_blank\">$r[users_msn]</a></td>
						</tr>
						<tr class=\"row1\">
							<td class=\"PVPT2Column1\"><img src=\"$themedir/buttons/profile_icq.gif\" border=\"0\"  alt=\"ICQ\" /></td>
							<td class=\"PVPR1Column1\"><a href=\"http://web.icq.com/whitepages/about_me/1,,,00.html?Uin=$r[users_icq]\" target=\"_blank\">$r[users_icq]</a></td>
						</tr>
						<tr class=\"row1\">
							<td class=\"PVPT2Column1\"><img src=\"images/newmsg.gif\" border=\"0\"  alt=\"Contact\" /></td>
							<td class=\"PVPR1Column1\"><a href=\"$menuvar[PRIVMSGS]&action=compose&to=$r[users_username]\">Send a Personal Message</a></td>
						</tr>
						<tr class=\"row1\">
							<td class=\"PVPT2Column1\"><img src=\"images/newmsg.gif\" border=\"0\"  alt=\"Contact\" /></td>
							<td class=\"PVPR1Column1\"><a href=\"mailto:$r[users_email_address]\">$r[users_email_address]</a></td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		<br /><br />
		
		<table class=\"PForumWidth\" border=\"0\" cellpadding=\"0\" cellspacing=\"1\">
			<tr>
				<td class=\"PForumWidthColumn1\">
					<table class=\"PForumColumn1Border\" border=\"0\" cellpadding=\"0\" cellspacing=\"1\">
						<tr class=\"title1\">
							<td class=\"PVPT1\" colspan=\"2\">$T_Profile_Information</td>
						</tr>
						<tr class=\"row1\">
							<td class=\"PVPT2Column1\">$T_Website_NC</td>
							<td class=\"PVPR1Column1\"><a href=\"$r[users_website]\" target=\"blank\">$r[users_website]</a></td>		
						</tr>
						<tr class=\"row1\">
							<td class=\"PVPT2Column1\">$T_Birthday_NC</td>
							<td class=\"PVPR1Column1\">$r[users_birthday]</td>
						</tr>
						<tr class=\"row1\">
							<td class=\"PVPT2Column1\">$T_Country_NC</td>
							<td class=\"PVPR1Column1\">$r[users_country]</td>
						</tr>
						<tr class=\"row1\">
							<td class=\"PVPT2Column1\">$T_Intrests_NC</td>
							<td class=\"PVPR1Column1\">$r[users_info]</td>
						</tr>
					</table>
		
				</td>
				<!-- Profile -->
				<td class=\"PForumWidthColumn2\">
					<table class=\"PForumColumn2Border\" border=\"0\" cellpadding=\"0\" cellspacing=\"1\">
						<tr class=\"title1\">
							<td class=\"PVPT1\" colspan=\"2\">$T_Additional_Information</td>
						</tr>
						<tr class=\"row1\">
							<td class=\"PVPT2Column1\">$T_Gender_NC</td>
							<td class=\"PVPR1Column1\">";
							
								if ($r[users_gender] == "male" || $r[users_gender] == "female") {
									$page_content .= "<img src=\"images/gender_$r[users_gender].gif\" alt=\"\" />";
								} 
								else { $page_content .= "None Specified"; }
								
		$page_content .= "\n					</td>
						</tr>
						<tr class=\"row1\">
							<td class=\"PVPT2Column1\">&nbsp;</td>
							<td class=\"PVPR1Column1\">&nbsp;</td>
						</tr>
						<tr class=\"row1\">
							<td class=\"PVPT2Column1\">&nbsp;</td>
							<td class=\"PVPR1Column1\">&nbsp;</td>
						</tr>
						<tr class=\"row1\">
							<td class=\"PVPT2Column1\">&nbsp;</td>
							<td class=\"PVPR1Column1\">&nbsp;</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		<br /><br />
		
		<table class=\"PForumBorder\" border=\"0\" cellpadding=\"0\" cellspacing=\"1\">
			<tr class=\"title1\">
				<td class=\"PVPT1\">$T_Signature_NC</td>
			</tr>		
			<tr class=\"row1\">  
				<td align=\"left\" valign=\"top\" width=\"100%\">" . bbcode($r[users_sig]) . "</td>
			</tr>
		</table>
		<br /><br />
		</center>";
	}

} //END VIEWPROFILE SECTION


//===========================================
// Lets Admins change users profiles
// and allows each user to change their own
//===========================================
else {
	if ($_SESSION['username']) {
		if(!isset($_POST['submit'])) {
			if ($action == 'editprofile' && isset($id) && $_SESSION['user_level'] != USER && $_SESSION['user_level'] != BANNED) { }
			else { $id = $_SESSION['userid']; }
				
			$result = mysql_query("SELECT * FROM `" . $DBTABLEPREFIX . "users` WHERE users_id = '$id' LIMIT 1");
			
			while($r=mysql_fetch_array($result)){
			
			
				$page_content .= "\n			<center>
				<form name=\"form1\" method=\"post\" action=\"index.php?p=profile&action=editprofile&id=$id\" enctype=\"multipart/form-data\">";
				 
					if ($_SESSION['user_level'] != USER) {
						$page_content .= "<input name=\"username\" type=\"hidden\" value=\"$r[users_username]\">
											<input name=\"id\" type=\"hidden\" value=\"$r[users_id]\">";
					} 
				$page_content .= "\n
				<table class=\"PChangeInfoForumBorder\" border=\"0\" cellpadding=\"0\" cellspacing=\"1\">
					<tr class=\"title1\">
						<td class=\"PChangeInfoT1\" colspan=\"2\">$T_User_Info</td>
					</tr>
					<tr class=\"row1\"> 
						<td class=\"PChangeInfoT2Column1\">$T_First_Name</td>
						<td class=\"PChangeInfoR1Column1\">$r[users_first_name]</td>
					</tr>
					<tr class=\"row1\"> 
						<td class=\"PChangeInfoT2Column1\">$T_Last_Name</td>
						<td class=\"PChangeInfoR1Column1\">$r[users_last_name]</td>
					</tr>
					<tr class=\"row1\"> 
						<td class=\"PChangeInfoT2Column1\">$T_Gender</td>
						<td class=\"PChangeInfoR1Column1\">";
						
							if ($r[users_gender] == "male" || $r[users_gender] == "female") { 
								$page_content .= "<img src=\"images/gender_" . $r[users_gender] . ".gif\" alt=\"\" />";
							} 
							else { $page_content .= "None Specified"; }
					$page_content .= "\n				</td>
					</tr>
					<tr class=\"row1\">  
						<td class=\"PChangeInfoT2Column1\"><$T_Username</td>
						<td class=\"PChangeInfoR1Column1\">$r[users_username]</td>
					</tr>";
					
					
					//=======================================
					// Allow admins to change users posts
					// and their title
					//=======================================
					if ($_SESSION['user_level'] == ADMIN) { 
						$page_content .= "\n					<tr class=\"row1\"> 
							<td class=\"PChangeInfoT2Column1\">$T_Title</td>
							<td class=\"PChangeInfoR1Column1\"><input name=\"title\" type=\"text\" id=\"title\" value=\"$r[users_title]\" size=\"50\" /></td>
						</tr>
						<tr class=\"row1\"> 
							<td class=\"PChangeInfoT2Column1\">$T_Posts</td>
							<td class=\"PChangeInfoR1Column1\"><input name=\"posts\" type=\"text\" id=\"posts\" value=\"$r[users_posts]\" size=\"50\" /></td>
						</tr>
						<tr class=\"row1\">
							<td class=\"PChangeInfoT2Column1\">$T_User_Level</td>
							<td class=\"PChangeInfoR1Column1\">
								<select name=\"userlevel\">
									<option value=\"" . USER . "\"" . testSelected($r[users_user_level], USER) . ">User</option>
									<option value=\"" . MOD . "\"" . testSelected($r[users_user_level], MOD) . ">Moderator</option>
									<option value=\"" . ADMIN . "\"" . testSelected($r[users_user_level], ADMIN) . ">Administrator</option>
									<option value=\"" . BANNED . "\"" . testSelected($r[users_user_level], BANNED) . ">Banned</option>
								</select>
							</td>
						</tr>";				
					}	
					$page_content .= "\n
					<tr class=\"row1\"> 
						<td class=\"PChangeInfoT2Column1\">$T_New_Password</td>
						<td class=\"PChangeInfoR1Column1\"><input name=\"pass\" type=\"password\" id=\"pass\" value=\"\" size=\"50\" /></td>
					</tr>
					<tr class=\"row1\"> 
						<td class=\"PChangeInfoT2Column1\">$T_Confirm_New_Password</td>
						<td class=\"PChangeInfoR1Column1\"><input name=\"pass1\" type=\"password\" id=\"pass1\" value=\"\" size=\"50\" /></td>
					</tr>
					<tr class=\"row1\"> 
						<td class=\"PChangeInfoT2Column1\">$T_Email_Address</td>
						<td class=\"PChangeInfoR1Column1\"><input name=\"email_address\" type=\"text\" id=\"email_address\" value=\"$r[users_email_address]\" size=\"50\" /></td>
					</tr>
					<tr class=\"title2\">
						<td class=\"PChangeInfoT2Notification\" colspan=\"2\"></td>
					</tr>
					<tr class=\"title1\">
						<td class=\"PChangeInfoT1\" colspan=\"2\">$T_Optional_Info</td>
					</tr>
					<tr class=\"row1\"> 
						<td class=\"PChangeInfoT2Column1\">$T_Country_NC</td>
						<td class=\"PChangeInfoR1Column1\"><input type=\"text\" name=\"country\" id=\"country\" value=\"$r[users_country]\" size=\"50\" /></td>
					</tr>
					<tr class=\"row1\"> 
						<td class=\"PChangeInfoT2Column1\">$T_Intrests_NC</td>
						<td class=\"PChangeInfoR1Column1\"><textarea name=\"info\" id=\"info\" rows=\"5\" cols=\"47\">$r[users_info]</textarea></td>
					</tr>
					<tr class=\"row1\"> 
						<td class=\"PChangeInfoT2Column1\">$T_Website_NC</td>
						<td class=\"PChangeInfoR1Column1\"><input type=\"text\" name=\"website\" value=\"$r[users_website]\" size=\"50\" /></td>
					</tr>
					<tr class=\"row1\"> 
						<td class=\"PChangeInfoT2Column1\">$T_AIM_NC</td>
						<td class=\"PChangeInfoR1Column1\"><input type=\"text\" name=\"aim\" value=\"$r[users_aim]\" size=\"50\" /></td>
					</tr>
					<tr class=\"row1\"> 
						<td class=\"PChangeInfoT2Column1\">$T_YIM_NC</td>
						<td class=\"PChangeInfoR1Column1\"><input type=\"text\" name=\"yim\" value=\"$r[users_yim]\" size=\"50\" /></td>
					</tr>
					<tr class=\"row1\"> 
						<td class=\"PChangeInfoT2Column1\">$T_MSN_NC</td>
						<td class=\"PChangeInfoR1Column1\"><input type=\"text\" name=\"msn\" value=\"$r[users_msn]\" size=\"50\" /></td>
					</tr>
					<tr class=\"title2\">
						<td class=\"PChangeInfoT2Notification\" colspan=\"2\"></td>
					</tr>
					<tr class=\"title1\">
						<td class=\"PChangeInfoT1\" colspan=\"2\">$T_Current_Avatar</td>
					</tr>
					<tr class=\"row1\">  
						<td colspan=\"2\"><center><img src=\"$r[users_avatar]\" alt=\"\" /><br /></td>
					</tr>
					<tr class=\"title2\">
						<td class=\"PChangeInfoT2Notification\" colspan=\"2\"></td>
					</tr>
					<tr class=\"title1\">
						<td class=\"PChangeInfoT1\" colspan=\"2\">$T_Edit_Avatar</td>
					</tr>
					<tr class=\"title2\">
						<td class=\"PChangeInfoT2Notification\" colspan=\"2\">Current Max Size is: " . $safs_config['ftssafs_avatar_max_width'] . "px X " . $safs_config['ftssafs_avatar_max_height'] . "px</td>
					</tr>
					<tr class=\"row1\">  
						<td class=\"PChangeInfoT2Column1\">$T_Remote_Avatar</td>
						<td class=\"PChangeInfoR1Column1\"><input name=\"avatar\" type=\"text\" id=\"avatar\" value=\"$r[users_avatar]\" size=\"50\" /></td>
					</tr>
					<tr class=\"row1\">  
						<td class=\"PChangeInfoT2Column1\">$T_Upload_Avatar</td>
						<td class=\"PChangeInfoR1Column1\"><input name=\"uploadavatar\" type=\"file\" id=\"uploadavatar\" size=\"50\" class=\"button\" /></td>
					</tr>
					<tr class=\"row1\">  
						<td class=\"PChangeInfoT2Column1\">$T_Browse_Gallery</td>
						<td class=\"PChangeInfoR1Column1\">";
							
								$avatarpath = './images/avatars'; //our avatar directory
									
								// Gets all the images in $avatarpath
								if($dir = opendir($avatarpath)){
										
									$x = '0';
									$avatar_images = array();
									$sub_dir_names = array();
									while (false !== ($file = readdir($dir))) {
										
										//select only dirrectories
										if ($file != "." && $file != ".." && !is_file($avatarpath . '/' . $file) && !is_link($avatarpath . '/' . $file)) {
											
											//holds our directories so that we can get our files
											$sub_dir = @opendir($avatarpath . '/' . $file);
											$sub_dir_names[$file] .= '';
												
										}
									}
										
										
									ksort($sub_dir_names); //sort by name
									$page_content .= "\n<select name='category'>";
									foreach($sub_dir_names as $key => $value) { 
										
										$selected = ( $key == $category ) ? ' selected="selected"' : '';
										$page_content .= "\n<option value='$key'$selected>" . ucfirst($key) . "</option>";
											
									} //print out the link url for the img
									$page_content .= "\n</select>";
								}
					$page_content .= "\n		
					       	<input class=\"button\" type=\"submit\" name=\"changeavatar\" value=\"Choose\">
				        </td>
					</tr>
					<tr class=\"title2\">
						<td class=\"PChangeInfoT2Notification\" colspan=\"2\"></td>
					</tr>
					<tr class=\"title1\">
						<td class=\"PChangeInfoT1\" colspan=\"2\">$T_Current_Signature</td>
					</tr>
					<tr class=\"row1\"> 
						<td align=\"left\" valign=\"top\" height=\"100%\" colspan=\"2\">	" . bbcode($r[users_sig]) . "</td>
					</tr>
					<tr class=\"title2\">
						<td class=\"PChangeInfoT2Notification\" colspan=\"2\"></td>
					</tr>
					<tr class=\"title1\">
						<td class=\"PChangeInfoT1\" colspan=\"2\">$T_Edit_Signature</td>
					</tr>
					<tr class=\"title2\">
						<td class=\"PChangeInfoT2Notification\" colspan=\"2\">Current Max Length is <u>" . $safs_config['ftssafs_max_sig_chars'] . "</u></td>
					</tr>
					<tr class=\"row1\"> 
						<td class=\"PChangeInfoR1Column1\" colspan=\"2\" align=\"center\"><textarea name=\"sig\" rows=\"7\" cols=\"70\">$r[users_sig]</textarea></td>
					</tr>
					<tr class=\"row1\"> 
						<td colspan=\"2\"><center><input type=\"submit\" name=\"submit\" value=\"Submit\" class=\"button\" /></center></td>
					</tr>
				</table>
				</td></tr></table>
				</center>";
		
		
			} //END PAGE PRINTING
		}
		else {					
			// Define post fields into simple variables
			if ($_SESSION['user_level'] != USER && $_SESSION['user_level'] != BANNED) {
				$user = $_POST['username'];
				$title = $_POST['title'];
				$posts = $_POST['posts'];
				$userlevel = $_POST['userlevel'];
			} 
			else {
				$user = $_SESSION['username'];
			}		
			
			$allowUpdate = 1;
			$updateAvatar = 1;
			$postEmailAddress = $_POST['email_address'];
			$info = $_POST['info'];
			$country = $_POST['country'];
			$website = $_POST['website'];
			$sig = $_POST['sig'];
			$aim = $_POST['aim'];
			$yim = $_POST['yim'];
			$msn = $_POST['msn'];
			$avatar = $_POST['avatar'];
			// Handle avatar uploads
			//************************************************************
			// Process UploadRequest
			//************************************************************
			if (trim($_FILES['uploadavatar']['name']) != "") {
				$typeOfFile = $_FILES['uploadavatar']['type']; 
				$fileName = $_FILES['uploadavatar']['name'];
				$fileExt = strtolower(substr($fileName,strrpos($fileName,".")));
			
				if ( $_FILES['uploadavatar']['size'] > $maxSize) { 
					$page_content .= "The file size is over 2MB.";
				} 
				else if (!in_array($fileExt, $allowedTypes)) {
					$page_content .= "Sorry, $fileName($typeOfFile) is not allowed to be uploaded.";
				}
				else {
					$temp_name = $_FILES['uploadavatar']['tmp_name'];
					$fileName = $_FILES['uploadavatar']['name']; 
					$fileName = str_replace("\\","",$fileName);
					$fileName = str_replace("'","",$fileName);
					$randName = md5(rand() * time()); // make a random filename
					$filePath = $uploadDir . "/" . $randName . $fileExt;
					
					// Update the log file
					$resource = fopen($uploadDir . "/" . "uploadlog.txt","a");
					fwrite($resource, "$randName " . date("Ymd h:i:s") . "UPLOAD - $_SERVER[REMOTE_ADDR] $fileName" . $_FILES['uploadavatar']['type'] . "\n");
					fclose($resource);
					
					// File Name Check
					if ( $fileName == "") { 
						$page_content .= "Invalid File Name Specified";
					}
					else {
						$result  =  move_uploaded_file($temp_name, $filePath);
						
						if (!chmod($filePath,0666)) {
					   		$page_content .= "Failed to change file permissions to 666.";
						}
						elseif (!$result) {
							$page_content .= "Unable to upload your avatar.";
						}
						else {
							$avatar = $filePath;
						}
					}
				}
			}			
			
			/* Let's strip some slashes in case the user entered
			any escaped characters. */
			
			$user = keepsafe($user);
			$title = keeptasafe($title);
			$posts = keepsafe($posts);
			$userlevel = keepsafe($userlevel);
			$postEmailAddress = keepsafe($postEmailAddress);
			$info = keeptasafe($info);
			$country = keeptasafe($country);
			$website = keepsafe($website);
			$sig = keeptasafe($sig);
			$aim = keepsafe($aim);
			$yim = keepsafe($yim);
			$msn = keepsafe($msn);
			$avatar = keeptasafe($avatar);
			
			//check email
			if (!$postEmailAddress) {
				$allowUpdate = 0;
				$page_content .= "Email address must be provided, please try again.<br />";
			}
			
			if ( strlen($sig) > $safs_config['ftssafs_max_sig_chars'] ) {
				$allowUpdate = 0;
				$page_content .= "Signature is too long, please try again.<br />";
			}
			
			if ($avatar != "") {			
				list($width, $height) = getimagesize($avatar);
			
				if ($width > 0 && $height > 0 && $width <= $safs_config['ftssafs_avatar_max_width'] && $height <= $safs_config['ftssafs_avatar_max_height']) {
					//good sized avatar
				}
				else {
					$updateAvatar = 0;
					$page_content .= "Your avatar (" . $width . "x" . $height . ") is large than the allowed size of " . $safs_config['ftssafs_avatar_max_width'] . "x" . $safs_config['ftssafs_avatar_max_height'] . ", your avatar has not been changed.<br />";
				}
			}
			
			if ($allowUpdate) {
				/* see if they changed their password */
				$passwordChunk = (!$_POST['pass'] || !$_POST['pass1']) ? "" : " users_password='" . md5($_POST['pass']) . "', ";	
				$adminChunk = ($_SESSION['user_level'] == USER && $_SESSION['user_level'] == BANNED) ? "" : "users_posts='$posts', users_title='$title', users_user_level='$userlevel',";
				$avatarChunk = ($updateAvatar) ? "" : " users_avatar='$avatar',";
				$usernameToUpdateChunk = ($_SESSION['user_level'] == USER && $_SESSION['user_level'] == BANNED) ? $user : $usernameToUpdate;
				
				$sql = "UPDATE `" . $DBTABLEPREFIX . "users` SET " . $adminChunk . $passwordChunk . "users_email_address='$postEmailAddress', users_country='$country', users_info='$info', users_sig='$sig'," . $avatarChunk . " users_website='$website', users_yim='$yim', users_aim='$aim', users_msn='$msn' WHERE users_username='$usernameToUpdateChunk'";
				$result = mysql_query($sql);
				
				if(!$result){
					$page_content .= 'There has been an error editing your profile. Please contact the webmaster.';
				} 
				else {
					$page_content .= "Your profile has been updated, and you are being redirected back to the editprofile page.";				
					$page_content .= ($_SESSION['user_level'] == USER && $_SESSION['user_level'] == BANNED) ? "<meta http-equiv='refresh' content='3;url=index.php?p=profile'>" : "<meta http-equiv='refresh' content='3;url=index.php?p=profile&action=editprofile&id=$id'>";	
				}		
			}
			unset($_POST['submit']);
			
		}
	} //end session check
	else { $page_content .= "Please login to change your account information."; }

} //end else staement



$page->setTemplateVar("PageContent", $page_content);
?>
