<?PHP 
/***************************************************************************
 *                               header.php
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
ini_set('arg_separator.output','&amp;');
//error_reporting(E_ALL); 
ini_set('display_errors', '0');
//ini_set('display_errors', '1');

include '_db.php';
session_start();
include_once ('includes/menu.php');
include_once ('includes/functions.php');
include_once ('includes/constants.php');

if (substr(phpversion(), 0, 1) == 5) { include_once ('includes/php5/pageclass.php'); }
else { include_once ('includes/php4/pageclass.php'); }

include_once ('includes/uoconfig.php');
include_once ('config.php');

global $safs_config;
$page = &new pageClass; //initialize our page

include_once ('useronline.php');

//=====================================================
// Cookie login script by: Demio from:  
// Changed some items and set it to expire in 1 month
//=====================================================
$cookiename = $safs_config['ftssafs_cookie_name'];

if (isset($_COOKIE[$cookiename]) && $_SESSION['STATUS'] != "true" && !defined("IN_LOGIN")) {
	$data = explode("-", $_COOKIE[$cookiename]);

	$sql = "SELECT * FROM `" . $DBTABLEPREFIX . "users` WHERE users_id='" . keeptasafe($data[0]) . "' AND users_password='" . keeptasafe($data[1]) . "' LIMIT 1";
	$cookie_query = mysql_query($sql);
	$cookieRow = mysql_fetch_array($cookie_query);
	if (mysql_num_rows($cookie_query) == 1) {                
		$_SESSION['STATUS'] = "true";
		$_SESSION['userid'] = $cookieRow['users_id'];
		$_SESSION['username'] = $cookieRow['users_username'];
		$_SESSION['epassword'] = $cookieRow['users_password'];
		$_SESSION['last_login'] = $cookieRow['users_last_login'];
		$_SESSION['session_avatar'] = $cookieRow['users_avatar'];
		$_SESSION['first_name'] = $cookieRow['users_first_name'];
		$_SESSION['last_name'] = $cookieRow['users_last_name'];
		$_SESSION['email_address'] = $cookieRow['users_email_address'];
		$_SESSION['user_level'] = $cookieRow['users_user_level'];
		$_SESSION['country'] = $cookieRow['users_country'];
		$_SESSION['info'] = $cookieRow['users_info'];
		$_SESSION['gender'] = $cookieRow['users_gender'];
		$_SESSION['script_locale'] = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
	}
}



//=====================================================
// Reregister all session variables so that they are
// are what is in the database, and havent been changed
//=====================================================
if (isset($_SESSION[userid])) { get_user_session_info($_SESSION[userid]); }
$themedir = (isset($_SESSION[userid])) ? get_theme_dir($_SESSION[username]) : "themes/$safs_config[ftssafs_theme]"; //gets the directory of the current theme

//=====================================================
// Load our usersonline config after the session 
// starts, that way we can use $_SESSION
//=====================================================
include_once ('includes/uoconfig.php');

if ($_SESSION['username']) {
	//==============================================
	// Pull our number of private messages from the 
	// inbox, as well as the number of those
	// that are new.
	//==============================================
	$sql = "SELECT pm.msg_read, pm.msg_id, pm.msg_notify, u.users_avatar FROM `" . $DBTABLEPREFIX . "priv_msgs` pm, `" . $DBTABLEPREFIX . "users` u WHERE pm.msg_to_id='$_SESSION[userid]' AND pm.msg_folder!='" . SAVEDFOLDER . "' AND u.users_username='$_SESSION[username]'"; //Gets ony msgs in the inbox 
	$result = mysql_query($sql);
			
	$nummsgs = '0';
	$numnewmsgs = '0';
	$newmsgcheck = '0';
	if($result && mysql_num_rows($result) > 0) {
		$nummsgs = mysql_num_rows($result); //get total number of messages in inbox
		
		while ( $row = mysql_fetch_array($result) ) {
			if($row[msg_read] != MSG_READ) { 
				$numnewmsgs++; 
						
				//we have to do this so that we dont bombard a person with popups ^^ could be fun, but we cant be too evil
				if($row[msg_notify] != MSG_NOTIFIED) {
					$newmsgcheck = '1';
					$sql = "UPDATE `" . $DBTABLEPREFIX . "priv_msgs` SET msg_notify = '" . MSG_NOTIFIED . "' WHERE msg_id='" . $row[msg_id] . "'";
					mysql_query($sql) or die('Error, update query failed' . $sql);
					
				}
			}
			$_SESSION['session_avatar'] = $row[users_avatar]; // Get the users avatar
		}
	}
	mysql_free_result($result); //free our query
	
	//==============================================
	// If theres new messages print out our popup
	//==============================================	
	if ($newmsgcheck) {
		$page_header .=	"			<div id=\"pmbox\" class=\"PMessagePopUpForumBorder\" style=\"position:absolute; text-align: left; z-index: 99; left: 376px; top: 151px;\">
										<div class=\"title1\">
											<div style=\"float: right;\"><a href=\"#\" onclick='document.getElementById(\"pmbox\").style.display=\"none\"'>[X]</a></div>
											<div id=\"pmboxHandle\" title=\"Click and hold to drag this window\" style=\"cursor: move;\">New Private Messages Has Arrived</div>
										</div>
										<div id=\"pmcontent\">";
		if($nummsgs > '1') {
			$page_header .=	"					<p>You have <strong>$numnewmsgs</strong> new messages.</p>";
		}
		else {
			$page_header .=	"					<p>You have <strong>$numnewmsgs</strong> new message.</p>";
		}	
		
		$page_header .=	"					<br /><br /><a href='$menuvar[PRIVMSGS]'>Click here to go to your inbox.</a>	
										</div>
									</div>
				
							<script language=\"javascript\">
								var thepmboxHandle = document.getElementById(\"pmboxHandle\");
								var thepmbox   = document.getElementById(\"pmbox\");
								Drag.init(thepmboxHandle, thepmbox);
							</script>";			
	}
		
	//==============================================
	// Print out user info box
	//==============================================
	$last_visit = makeDate($_SESSION['last_login']);
	$curent_time = makeDate(time());
	$page_header .=	"			<center>
								<div class='lastvisit'>
									<table border='0' cellspacing='0' cellpadding='0' class='lastvisitwidth' style='margin: 0 auto;'><tr style='margin: 0 auto;'><td width='80%'>
										<p>
											Welcome Back " . $_SESSION['username'] . ".<br />
											You Have $nummsgs In Your Inbox, $numnewmsgs Are New.<br />
											Your Last Visit Was: $last_visit.<br /><br />
											Today is: $curent_time.<br />
											<a href='$menuvar[SEARCH]&action=unreadposts' class='stats'>View All Unread Posts.</a>
										</p>
									</td>
									<td align='right'>";
									
	if ($_SESSION['session_avatar']) {
		$page_header .=	"					<img src='" . $_SESSION['session_avatar'] . "' alt='' /><br />";
	}		
	
	$page_header .=	"				</td></tr></table>
								</div>
								<br /><br />";
}
else {
	$page_header .=	"			Welcome Guest. Please click <a href='$menuvar[LOGIN]' class='newstylelink'>here</a> to login.
								<br /><br />";
}
$page_content .=	"		</center>";

$page->setTemplateVar("PageHeader", $page_header);	
?>
