<? 
/***************************************************************************
 *                               index.php
 *                            -------------------
 *   begin                : Tuseday, March 14, 2006
 *   copyright            : (C) 2006 Fast Track Sites
 *   email                : sales@fasttracksites.com
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
//error_reporting(E_ALL);
//ini_set('display_errors', '1');


include 'includes/header.php';

$requested_page_id = $_GET['p'];
$requested_section = $_GET['s'];
$requested_id = $_GET['id'];
$requested_highlight = $_GET['highlight'];

$actual_page_id = ($requested_page_id == "" || !isset($requested_page_id)) ? 1 : $requested_page_id;
$actual_page_id = parseurl($actual_page_id);
$actual_section = parseurl($requested_section);
$actual_id = parseurl($requested_id);
$actual_highlight = keeptasafe($requested_highlight);
$page_content = "";
$action = parseurl($_GET['action']);

// We want to show all of our menus by default
$page->setTemplateVar("cm_active", ACTIVE);

// Build the announcement item
if (trim($safs_config[ftssafs_announcement_title]) != "" && trim($safs_config[ftssafs_announcement_text]) != "") {
	$page_content .= "\n		<center>
					<div class='UOnlineForumBorder'>
						<h3>
							<div style=\"float: right;\"><a href=\"javascript:sqr_show_hide('annoucementDrop');\"><img src=\"images/plus.png\" style=\"width: 15px; height: 15px; border:0px;\" alt=\"Show/hide users viewing stats\" /></a></div>
							" . bbcode($safs_config[ftssafs_announcement_title]) . "
						</h3>
						<div id=\"annoucementDrop\">
							<p>" . bbcode($safs_config[ftssafs_announcement_text]) . "</p>
						</div>
					</div>
						<br /><br />
			</center>";
}

//========================================
// Logout Function
//========================================
// Prevent spanning between apps to avoid a user getting more acces that they are allowed
if ($_SESSION['script_locale'] != rtrim(dirname($_SERVER['PHP_SELF']), '/\\') && session_is_registered('userid')) {
	session_destroy();
}

if ($actual_page_id == "logout") {
	define('IN_FTSSAFS', true);
	include '_db.php';
	include_once ('includes/menu.php');
	include_once ('config.php');
	global $safs_config;
	
	//Destroy Session Cookie
	$cookiename = $safs_config['ftssafs_cookie_name'];
	setcookie($cookiename, false, time()-2592000); //set cookie to delete back for 1 month
	
	//Destroy Session
	session_destroy();
	if(!session_is_registered('username')){
		header("Location: http://" . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['PHP_SELF']), '/\\') . "/index.php");	
		exit();
	}
}


// If the system is locked, then only a moderator or admin should be able to view it
if ($_SESSION['user_level'] != ADMIN && $_SESSION['user_level'] != MOD && $safs_config['ftssafs_active'] != ACTIVE) {
	if ($actual_page_id == "login") {
		include 'login.php';
	}
	else {	
		$page->setTemplateVar("PageTitle", 'Currently Disabled');
		$page_content .= bbcode($safs_config[ftssafs_inactive_msg]);
		$page->setTemplateVar("PageContent", $page_content);
		
		if (isset($_SESSION[username])) {
			$page->makeMenuItem("top", "Logout", "index.php?p=logout", "");
		}
		else {
			$page->makeMenuItem("top", "Login", "index.php?p=login", "");
		}
	}
}
else {
	//========================================
	// User is banned
	//========================================
	if ($_SESSION['user_level'] == BANNED) {
		$page->setTemplateVar("PageTitle", 'You Have Been Banned');
		$page->setTemplateVar("PageContent", "Unfortunately you have banned from this board by the Admin, if you wish to dispute this, or feel that you have been unjustly banned, please feel free to contact the admin at: " . $safs_config['ftssafs_board_email']); 
		$page->makeMenuItem("top", "Logout", "index.php?p=logout", "");
	}
	else {
		//========================================
		// Admin panel options
		//========================================
		if ($actual_page_id == "admin") {
			if (!$_SESSION[username]) { include 'login.php'; }
			else {
				if ($_SESSION['user_level'] == MOD || $_SESSION['user_level'] == ADMIN) {
					if ($actual_section == "" || !isset($actual_section)) {
						include 'admin/index.php'; 
						$page->setTemplateVar("PageTitle", "Forums");
					}
					elseif ($actual_section == "settings") {
						include 'admin/settings.php';				
						$page->setTemplateVar("PageTitle", "Settings");
					}
					elseif ($actual_section == "forums") {
						include 'admin/index.php';			
						$page->setTemplateVar("PageTitle", "Forums");	
					}
					elseif ($actual_section == "topicicons") {
						include 'admin/topicicons.php';			
						$page->setTemplateVar("PageTitle", "Topic Icons");	
					}
					elseif ($actual_section == "ranks") {
						include 'admin/ranks.php';			
						$page->setTemplateVar("PageTitle", "Ranks");	
					}
					elseif ($actual_section == "smilies") {
						include 'admin/smilies.php';			
						$page->setTemplateVar("PageTitle", "Smilies");	
					}
					elseif ($actual_section == "profanity") {
						include 'admin/profanity.php';			
						$page->setTemplateVar("PageTitle", "Profanity Filter");	
					}
					elseif ($actual_section == "optimize") {
						include 'admin/optimize.php';			
						$page->setTemplateVar("PageTitle", "Optimize");	
					}
					elseif ($actual_section == "users") {
						include 'admin/users.php';			
						$page->setTemplateVar("PageTitle", "Users");	
					}			
				}				
				else { setTemplateVar("PageContent", "You are not authorized to access the admin panel."); }				
			}
		}
		elseif ($actual_page_id == "join") {
			include 'join.php';
			$page->setTemplateVar("PageTitle", "Register a New Account");
		}
		elseif ($actual_page_id == "login") {
			include 'login.php';
			$page->setTemplateVar("PageTitle", "Login");
		}
		elseif ($actual_page_id == "inbox") {
			include 'privmsgs.php';	
			$page->setTemplateVar("PageTitle", "Inbox");	
		}
		elseif ($actual_page_id == "profile") {
			include 'profile.php';	
			$page->setTemplateVar("PageTitle", "Profile");	
		}
		elseif ($actual_page_id == "search") {
			include 'search.php';		
			$page->setTemplateVar("PageTitle", "Search");
		}
		elseif ($actual_page_id == "memberlist") {
			include 'memberlist.php';		
			$page->setTemplateVar("PageTitle", "Memberlist");
		}
		elseif ($actual_page_id == "post") {
			include 'ajax.php';		
			$page->setTemplateVar("PageTitle", "Post Options");
		}
		elseif ($actual_page_id == "viewforum" && $actual_id != "") {
			include 'viewforum.php';	
			$page->setTemplateVar("PageTitle", "View Forum");	
		}
		elseif ($actual_page_id == "viewtopic" && $actual_id != "") {
			include 'viewtopic.php';		
			$page->setTemplateVar("PageTitle", "View Topic");
		}
		elseif ($actual_page_id == "switcher") {
			include 'switcher.php';		
			$page->setTemplateVar("PageTitle", "Style Switcher");
		}
		else {
			// Print out the main forum index
			$page_content .= "<center>";
			
				$sql = "SELECT cat_id, cat_title, cat_order FROM `" . $DBTABLEPREFIX . "categories` ORDER BY cat_order"; //gets categories
				$result = mysql_query($sql);
				
				while ( $row = mysql_fetch_array($result) )
				{
					extract($row); //so we dont have to do long array variables
					$catid1 = $cat_id; //so we can check it against the forum cat_id's
					
					
					$page_content.= "<table class='MForumBorder' border='0' cellpadding='0' cellspacing='1'>
									  <tr class='title1'>
									    <td class='VForumT1' colspan='5'>
											<div style=\"float: right;\"><a href=\"javascript:sqr_show_hide('" . $cat_title . "ForumDrop');\"><img src=\"images/plus.png\" style=\"width: 15px; height: 15px; border:0px;\" alt=\"Show/hide forum\" /></a></div>									    
									    	$cat_title
									    </td>
									  </tr>
									  <tbody id=\"" . $cat_title . "ForumDrop\">
									  <tr class='title2'>
									    <th class='MPT2Column1'></th>
									    <th class='MPT2Column2'>$T_Forum</th>
									    <th class='MPT2Column3' nowrap>$T_Topics</th>
									    <th class='MPT2Column4' nowrap>$T_Posts</th>
									    <th class='MPT2Column5' nowrap>$T_Last_Post</th>
									  </tr>";
				
					$sql2 = "SELECT * FROM `" . $DBTABLEPREFIX . "forums` WHERE forum_subforum = '0' ORDER BY forum_cat_id, forum_order"; //gets the forum info
					$result2 = mysql_query($sql2);
					
					while ( $row2 = mysql_fetch_array($result2) )
					{
						extract($row2); //so we dont have to do long array variables
						
						if ($catid1 == $forum_cat_id) { 
							$page_content.= "  <tr class='row1'>
													<td class='MPR1Column1 wo vm'>";
							
							//============================================
							// find out if the forum has any new topics
							//============================================
							$sql3 = "SELECT t.topic_forum_id FROM `" . $DBTABLEPREFIX . "topics` t, `" . $DBTABLEPREFIX . "posts_read` pr WHERE t.topic_forum_id = '$forum_id' AND t.topic_id = pr.pr_topic_id AND pr.pr_userid = '$_SESSION[userid]'"; //gets the forum info
							$result3 = mysql_query($sql3);
								
							if($result3 && mysql_num_rows($result3) > 0) {
								$totaltopicsinforum = mysql_num_rows($result3);
							}
							mysql_free_result($result3); //free our query
							if ($forum_topics == '0' || $forum_topics == $totaltopicsinforum) { $page_content.= "<center><img src='images/nonewf.jpg' alt='' /></center>"; }
							else { $page_content.= "<img src='images/newf.jpg' alt='' />"; }				
				
							$page_content.= "</td>
											    <td class='MPR1Column2 wa'><a href='$menuvar[VIEWFORUM]&id=$forum_id'>$forum_name</a><br />$forum_desc</td>
											    <td class='MPR1Column3'>$forum_topics</td>
											    <td class='MPR1Column4'>$forum_posts</td>
											    <td class='MPR1Column5 nw'>"; 
							get_last_post("pageContent", "forum", $forum_id);
							$page_content.= "				
													</td>
											  </tr>";		
							
						}
					}
					mysql_free_result($result2); //free our query
					
					$page_content.= "  
										</tbody>	
									</table>
									<br /><br />";
				}
				mysql_free_result($result);
			
			$page_content.= "</center>";
					
			$page->setTemplateVar("PageTitle", "Home");
			$page->setTemplateVar("PageContent", $page_content);	
	
		}
	
		//================================================
		// Build Main Menus
		//================================================
		
		// Top Menus
		if ($actual_page_id != "admin") {  $page->makeMenuItem("top", "Home", $menuvar['HOME'], ""); }
		
		// The menus
		if (!isset($_SESSION[username])) {
			$page->makeMenuItem("top", "Memberlist", $menuvar['MEMBERLIST'], "");
			$page->makeMenuItem("top", "Register", $menuvar['JOIN'], "");
			$page->makeMenuItem("top", "Login", $menuvar['LOGIN'], "");
		}
		else {
			// Make admin panel menu item
			if ($_SESSION['user_level'] == MOD || $_SESSION['user_level'] == ADMIN && $actual_page_id != "admin") {
				$page->makeMenuItem("top", "Admin Panel", $menuvar['ADMIN'], "");
			}		
			if ($_SESSION['user_level'] == MOD || $_SESSION['user_level'] == ADMIN && $actual_page_id == "admin") {
				// Only for admin section			
				$page->makeMenuItem("top", "Settings", $menuvar['SETTINGS'], "");
				$page->makeMenuItem("top", "Forums", $menuvar['FORUMS'], "");
				$page->makeMenuItem("top", "Ranks", $menuvar['RANKS'], "");
				$page->makeMenuItem("top", "Smilies", $menuvar['SMILIES'], "");
				$page->makeMenuItem("top", "Icons", $menuvar['ICONS'], "");
				$page->makeMenuItem("top", "Word Filter", $menuvar['WORDFILTER'], "");
				$page->makeMenuItem("top", "Optimize", $menuvar['OPTIMIZE'], "");
				$page->makeMenuItem("top", "Users", $menuvar['USERS'], "");
				$page->makeMenuItem("top", "Main Forum", $menuvar['HOME'], "");			
			}
			else {
				// Only for main section
				$page->makeMenuItem("top", "Inbox", $menuvar['INBOX'], "");
				$page->makeMenuItem("top", "Profile", $menuvar['PROFILE'], "");
				$page->makeMenuItem("top", "Search", $menuvar['SEARCH'], "");
				$page->makeMenuItem("top", "Memberlist", $menuvar['MEMBERLIST'], "");
			}
			$page->makeMenuItem("top", "Logout", $menuvar['LOGOUT'], "");
		}
		
		// Only for the FTS Site
		if ($actual_page_id != "admin") { $page->makeMenuItem("top", "Back to the Main Site", "http://www.fasttracksites.com", ""); }
	}
}

version_functions("no");
include $themedir . "/template.php";
?>