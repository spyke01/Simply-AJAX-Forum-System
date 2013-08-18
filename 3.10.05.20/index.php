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
// If the db connection file is missing we should redirect the user to install page
if (!file_exists('_db.php')) {
	header("Location: http://" . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['PHP_SELF']), '/\\') . "/install.php");	
	exit();
}

include 'includes/header.php';

$actual_page_id = (empty($_GET['p'])) ? 1 : parseurl($_GET['p']);
$actual_section = parseurl($_GET['s']);
$actual_id = parseurl($_GET['id']);
$actual_action = parseurl($_GET['action']);
$actual_action2 = parseurl($_GET['action2']);
$actual_style = parseurl($_GET['style']);
$actual_report = parseurl($_GET['report']);
$actual_page = parseurl($_GET['page']);
$actual_highlight = keeptasafe($_GET['highlight']);
$page_content = "";

// Warn the user if a install or update script is present
if (file_exists('install.php')) {
	$page_content .= "<div class=\"errorMessage\">Warning: install.php is present, please remove this file for security reasons.</div>";
}

if (file_exists('update.php')) {
	$page_content .= "<div class=\"errorMessage\">Warning: update.php is present, please remove this file for security reasons.</div>";
}

// We want to show all of our menus by default
$page->setTemplateVar("sidebar_active", INACTIVE);

//========================================
// Logout Function
//========================================
// Prevent spanning between apps to avoid a user getting more acces that they are allowed
if ($_SESSION['script_locale'] != rtrim(dirname($_SERVER['PHP_SELF']), '/\\') && session_is_registered('userid')) {
	session_destroy();
}

if ($actual_page_id == "logout") {	
	//Destroy Session Cookie
	$cookiename = $safs_config['ftssafs_cookie_name'];
	setcookie($cookiename, false, time()-2592000); //set cookie to delete back for 1 month
	
	//Destroy Session
	session_destroy();
	if(!session_is_registered('userid')){
		header("Location: http://" . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['PHP_SELF']), '/\\') . "/index.php");	
		exit();
	}
}

// Handle usersonline table
runUsersOnlineUpdate($actual_page_id, $actual_section, $actual_id);

// Top Menus
$page->makeMenuItem("top", "<img src=\"images/ftsLogo.png\" alt=\"Fast Track Sites Logo\" />", "", "logo");
		
//Check to see if advanced options are allowed or not
if (version_functions("advancedOptions") == true) {
	// If the system is locked, then only a moderator or admin should be able to view it
	if ($_SESSION['user_level'] != SYSTEM_ADMIN && $_SESSION['user_level'] != BOARD_ADMIN && $safs_config['ftssafs_active'] != ACTIVE) {
		$page->makeMenuItem("top", "Home", "index.php", "");

		if ($actual_page_id == "login") {
			$page->setTemplateVar("PageTitle", "Login");	
			$page->addBreadCrumb("Login", $menuvar['LOGIN']);
			include 'login.php';
		}
		else {	
			$page->setTemplateVar("PageTitle", 'Currently Disabled');
			$page->setTemplateVar("PageContent", bbcode($safs_config['ftssafs_inactive_msg']));
		
			// Let us login so we can access the system during a shutdown
			$page->setTemplateVar("PageTitle", "Login");	
			$page->addBreadCrumb("Login", $menuvar['LOGIN']);
			include 'login.php';
		}
	}
	else {
		//========================================
		// Admin panel options
		//========================================
		if ($actual_page_id == "admin") {
			// Add breadcrumb pointing home
			$page->addBreadCrumb("Admin", $menuvar['ADMIN']);
			
			if (!$_SESSION['username']) { include 'login.php'; }
			else {
				if ($_SESSION['user_level'] == BOARD_ADMIN || $_SESSION['user_level'] == SYSTEM_ADMIN) {
					if (empty($actual_section) || !isset($actual_section)) {
						$page->setTemplateVar("PageTitle", "Admin Panel");
						include 'admin/admin.php'; 
					}
					elseif ($actual_section == "categories") {
						$page->setTemplateVar("PageTitle", "Client Categories");	
						$page->addBreadCrumb("Categories", $menuvar['CATEGORIES']);
						include 'admin/categories.php';
					}
					elseif ($actual_section == "graphs") {
						$page->setTemplateVar("PageTitle", "Graphs");		
						$page->addBreadCrumb("Reporting", $menuvar['REPORTING']);
						$page->addBreadCrumb("Graphs", $menuvar['GRAPHS']);
						makeReportingSubMenu($page);
						include 'admin/graphs.php';
					}
					elseif ($actual_section == "optimize") {
						$page->setTemplateVar("PageTitle", "Optimize");
						$page->addBreadCrumb("System Settings", $menuvar['SYSTEMSETTINGS']);
						$page->addBreadCrumb("Optimize", $menuvar['OPTIMIZE']);
						makeSystemSettingsSubMenu($page);
						include 'admin/optimize.php';
					}
					elseif ($actual_section == "ranks") {
						$page->setTemplateVar("PageTitle", "Ranks");
						$page->addBreadCrumb("System Settings", $menuvar['SYSTEMSETTINGS']);
						$page->addBreadCrumb("Ranks", $menuvar['RANKS']);
						makeSystemSettingsSubMenu($page);
						include 'admin/ranks.php';
					}
					elseif ($actual_section == "reports") {
						$page->setTemplateVar("PageTitle", "Reports");		
						$page->addBreadCrumb("Reporting", $menuvar['REPORTING']);
						$page->addBreadCrumb("Reports", $menuvar['REPORTS']);
						makeReportingSubMenu($page);
						include 'admin/reports.php';
					}
					elseif ($actual_section == "reporting") {
						$page->setTemplateVar("PageTitle", "Reports");		
						$page->addBreadCrumb("Reporting", $menuvar['REPORTING']);
						$page->addBreadCrumb("Reports", $menuvar['REPORTS']);
						makeReportingSubMenu($page);
						include 'admin/reports.php';
					}
					elseif ($actual_section == "smilies") {
						$page->setTemplateVar("PageTitle", "Smilies");
						$page->addBreadCrumb("System Settings", $menuvar['SYSTEMSETTINGS']);
						$page->addBreadCrumb("Smilies", $menuvar['SMILIES']);
						makeSystemSettingsSubMenu($page);
						include 'admin/smilies.php';
					}
					elseif ($actual_section == "systemsettings") {
						$page->setTemplateVar("PageTitle", "System Settings");
						$page->addBreadCrumb("System Settings", $menuvar['SYSTEMSETTINGS']);
						$page->addBreadCrumb("Settings", $menuvar['SETTINGS']);
						makeSystemSettingsSubMenu($page);
						include 'admin/settings.php';
					}
					elseif ($actual_section == "settings") {
						$page->setTemplateVar("PageTitle", "Settings");
						$page->addBreadCrumb("System Settings", $menuvar['SYSTEMSETTINGS']);
						$page->addBreadCrumb("Settings", $menuvar['SETTINGS']);
						makeSystemSettingsSubMenu($page);
						include 'admin/settings.php';
					}
					elseif ($actual_section == "themes") {
						$page->setTemplateVar("PageTitle", "Themes");
						$page->addBreadCrumb("System Settings", $menuvar['SYSTEMSETTINGS']);
						$page->addBreadCrumb("Themes", $menuvar['THEMES']);
						makeSystemSettingsSubMenu($page);
						include 'admin/themes.php';
					}
					elseif ($actual_section == "topicicons") {
						$page->setTemplateVar("PageTitle", "Topic Icons");
						$page->addBreadCrumb("System Settings", $menuvar['SYSTEMSETTINGS']);
						$page->addBreadCrumb("Topic Icons", $menuvar['TOPICICONS']);
						makeSystemSettingsSubMenu($page);
						include 'admin/topicicons.php';
					}
					elseif ($actual_section == "users") {
						$page->setTemplateVar("PageTitle", "Users");
						$page->addBreadCrumb("System Settings", $menuvar['SYSTEMSETTINGS']);
						$page->addBreadCrumb("Users", $menuvar['USERS']);
						makeSystemSettingsSubMenu($page);
						include 'admin/users.php';
					}
					elseif ($actual_section == "wordfilter") {
						$page->setTemplateVar("PageTitle", "Word Filter");
						$page->addBreadCrumb("System Settings", $menuvar['SYSTEMSETTINGS']);
						$page->addBreadCrumb("Word Filter", $menuvar['WORDFILTER']);
						makeSystemSettingsSubMenu($page);
						include 'admin/wordfilter.php';
					}
				}
				else { $page->setTemplateVar("PageContent", "You are not authorized to access the admin panel."); }
			}
		}
		elseif ($actual_page_id == "inbox") {
			$page->setTemplateVar("PageTitle", "Inbox");	
			$page->addBreadCrumb("Inbox", $menuvar['INBOX']);
			makePrivateMessagesSubMenu($page);
			include 'inbox.php';			
		}
		elseif ($actual_page_id == "login") {
			$page->setTemplateVar("PageTitle", "Login");	
			$page->addBreadCrumb("Home", $menuvar['HOME']);
			$page->addBreadCrumb("Login", $menuvar['LOGIN']);
			include 'login.php';
		}
		elseif ($actual_page_id == "members") {
			$page->setTemplateVar("PageTitle", "Members");	
			$page->addBreadCrumb("Home", $menuvar['HOME']);
			$page->addBreadCrumb("Members", $menuvar['MEMBERS']);
			include 'members.php';
		}
		elseif ($actual_page_id == "profile") {
			$page->setTemplateVar("PageTitle", "Profile");	
			$page->addBreadCrumb("Home", $menuvar['HOME']);
			$page->addBreadCrumb("Profile", $menuvar['PROFILE']);
			include 'profile.php';
		}
		elseif ($actual_page_id == "version") {
			$page->setTemplateVar("PageTitle", "Version Information");	
			$page->addBreadCrumb("Home", $menuvar['HOME']);
			$page->addBreadCrumb("Version Information", "");
			
			include('_license.php');
		
			$page_content .= "
				<div class=\"roundedBox\">
					<h1>Version Information</h1>
					<strong>Application:</strong> " . A_NAME . "<br />
					<strong>Version:</strong> " . A_VERSION . "<br />
					<strong>Registered to:</strong> " . $A_Licensed_To . "<br />
					<strong>Serial:</strong> " . $A_License . "
				</div>";
			
			$page->setTemplateVar("PageContent", $page_content);	
		}
		elseif ($actual_page_id == "viewforum") {
			$page->setTemplateVar("PageTitle", "View Forum");	
			$page->addBreadCrumb("Home", $menuvar['HOME']);
			$page->addBreadCrumb("View Forum", $menuvar['VIEWFORUM']);
			include 'viewforum.php';
		}
		elseif ($actual_page_id == "viewtopic") {
			$page->setTemplateVar("PageTitle", "View Topic");	
			$page->addBreadCrumb("Home", $menuvar['HOME']);
			$page->addBreadCrumb(getForumNameByTopicID($actual_id), $menuvar['VIEWFORUM'] . "&id=" . getForumIDByTopicID($actual_id));
			$page->addBreadCrumb("View Topic", $menuvar['VIEWTOPIC']);
			include 'viewtopic.php';
		}
		else {			
			// Load our forum list
			$page->setTemplateVar("PageTitle", "Home");	
			$page->addBreadCrumb("Home", $menuvar['HOME']);
			
			// Return the table's HTML
			$page_content .= "
							<div class=\"roundedBox\">
								" . returnAnnouncementBlock() . "
								" . printCategoryForumsTable() . "
								" . returnBoardInformationBlock() . "
							</div>";
						
			// Handle our JQuery needs
			$JQueryReadyScripts = "";
		
			$page->setTemplateVar("PageContent", $page_content);
			$page->setTemplateVar("JQueryReadyScript", $JQueryReadyScripts);
		}
	
		//================================================
		// Get Menus
		//================================================
		// Create Our Top Menu
		if ($actual_page_id == "admin") {
			if ($_SESSION['user_level'] == BOARD_ADMIN || $_SESSION['user_level'] == SYSTEM_ADMIN) {		
				$page->makeMenuItem("top", "Manage Forums", $menuvar['MANAGEFORUMS'], "");
			}
			
			if ($_SESSION['user_level'] == SYSTEM_ADMIN) {
				$page->makeMenuItem("top", "Reporting", $menuvar['REPORTING'], "");
				$page->makeMenuItem("top", "System Settings", $menuvar['SYSTEMSETTINGS'], "");
			}
			
			if ($_SESSION['user_level'] == BOARD_ADMIN || $_SESSION['user_level'] == SYSTEM_ADMIN) {		
				$page->makeMenuItem("top", "Return to the Forum", $menuvar['HOME'], "");
			}
		}
		else {
			$page->makeMenuItem("top", "Home", "index.php", "");

			if ($_SESSION['user_level'] == BOARD_ADMIN || $_SESSION['user_level'] == SYSTEM_ADMIN) {		
				$page->makeMenuItem("top", "Admin Panel", $menuvar['ADMIN'], "");
			}
			
			if (!isset($_SESSION['username'])) {
				$page->makeMenuItem("top", "Register", $menuvar['REGISTER'], "");
				$page->makeMenuItem("top", "Login", $menuvar['LOGIN'], "");
			}
			else {
				$page->makeMenuItem("top", "Inbox", $menuvar['INBOX'], "");
				$page->makeMenuItem("top", "Profile", $menuvar['PROFILE'], "");
				$page->makeMenuItem("top", "Search", $menuvar['SEARCH'], "");
				$page->makeMenuItem("top", "Memberlist", $menuvar['MEMBERS'], "");
				$page->makeMenuItem("top", "Logout", $menuvar['LOGOUT'], "");
			}
		}
	}
}
else { $page->setTemplateVar("PageContent", version_functions("advancedOptionsText")); }

version_functions("no");
if (isset($actual_style) && $actual_style == "printerFriendly") { include "themes/" . $themeDir . "/printerFriendlyTemplate.php"; }
else { include "themes/" . $themeDir . "/template.php"; }
?>