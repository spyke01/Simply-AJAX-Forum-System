<? 
/***************************************************************************
 *                               install.php
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
	ini_set('arg_separator.output','&amp;');
	//error_reporting(E_ALL);
	//ini_set('display_errors', '1');
	
	// Set up our installer
	define('INSTALLER_SCRIPT_NAME', 'Simply AJAX Forum System');
	define('INSTALLER_SCRIPT_DESC', 'The Fast Track Sites Simply AJAX Forum System was designed to allow businesses to keep track of current, past, and prospective clients in an easy to use and intuitive manner. We also wanted to allow them to keep track of their client\'s orders, take notes on the client(Their preferences, phone converstaions, etc), and easily schedule and track appointments. We\'ve made it easy to access multitudes of information on each client just by viewing their information. By doing that you\'ll be able to see all information related to this client from the various parts of the system at once.');
	define('INSTALLER_SCRIPT_IS_PROFESSIONAL_VERSION', 1);
	define('INSTALLER_SCRIPT_DB_PREFIX', 'SAFS_');
	
	// Inlcude the needed files
	include_once ('includes/constants.php');
	if (substr(phpversion(), 0, 1) == 5) { 
		include_once ('includes/classes/php5/DB.php');
		include_once ('includes/classes/php5/pageclass.php');
	}
	else { 
		include_once ('includes/classes/php4/DB.php');
		include_once ('includes/classes/php4/pageclass.php');
	}

	// Instantiate our page class
	$page = &new pageClass;

	// Handle our variables
	$requested_step = $_GET['step'];

	$actual_step = (empty($requested_step) || !isset($requested_step)) ? 1 : keepsafe($requested_step);
	$page_content = "";
	$failed = 0;
	$totalfailure = 0;
	$failed = array();
	$failedsql = array();
	$currentdate = time();

	
	//========================================
	// Custom Functions for this Page
	//========================================
	function keepsafe($makesafe) {
		$makesafe=strip_tags($makesafe); // strip away any dangerous tags
		$makesafe=str_replace(" ","",$makesafe); // remove spaces from variables
		$makesafe=str_replace("%20","",$makesafe); // remove escaped spaces
		$makesafe = trim(preg_replace('/[^\x09\x0A\x0D\x20-\x7F]/e', '"&#".ord($0).";"', $makesafe)); //encodes all ascii items above #127
		$makesafe = stripslashes($makesafe);
		
		return $makesafe;
	}
	
	function keeptasafe($makesafe) {
		$makesafe=strip_tags($makesafe); // strip away any dangerous tags
		$makesafe = trim(preg_replace('/[^\x09\x0A\x0D\x20-\x7F]/e', '"&#".ord($0).";"', $makesafe)); //encodes all ascii items above #127
		$makesafe = stripslashes($makesafe);
		
		return $makesafe;
	}

	function checkresult($result, $sql, $table) {
		global $failed;
		global $failedsql;
		global $totalfailure;
		
		if (!$result || empty($result)) {
			$failed[$table] = "failed";
			$failedsql[$table] = $sql;
			$totalfailure = 1;
		}  
		else {
			$failed[$table] = "succeeded";
			$failedsql[$table] = $sql;
		}	
	}
	
	//========================================
	// Build our Page
	//========================================
	switch ($actual_step) {
		case 1:
			$page->setTemplateVar("PageTitle", INSTALLER_SCRIPT_NAME . " Step 1 - Introduction");	
			
			// Print this page
			$page_content = "
					<h2>Welcome to the Fast Track Sites Script Installer</h2>
					Thank you for downloading the " . INSTALLER_SCRIPT_NAME . " this page will walk you through the setup procedure.
					<br /><br />
					" . INSTALLER_SCRIPT_DESC . "
					<br /><br />
					<h2><span class=\"iconText38px\"><img src=\"themes/installer/icons/paperAndPencil_38px.png\" alt=\"License Agreement\" /></span> <span class=\"iconText38px\">License Agreement</span></h2>
					By installing this application you are agreeing to all the terms and conditions stated in the <a href=\"http://www.fasttracksites.com/ftspl\">Fast Track Sites Program License</a>.
					<br /><br />";
					
			if (INSTALLER_SCRIPT_IS_PROFESSIONAL_VERSION) {
				$page_content .= "
					Please enter your registration information below, failure to do so can result in your application being disabled.
					<br /><br />
					<form id=\"licenseInformationForm\" action=\"install.php?step=2\" method=\"post\">
						<label for=\"serialNumber\">Serial Number</label> <input type=\"text\" name=\"serialNumber\" id=\"serialNumber\" class=\"required\" />
						<label for=\"registeredTo\">Registered To</label> <input type=\"text\" name=\"registeredTo\" id=\"registeredTo\" class=\"required\" />
						<input type=\"submit\" name=\"submit\" class=\"button\" value=\"Next\" />
					</form>
					<script type=\"text/javascript\">
						var valid = new Validation('licenseInformationForm', {immediate : true, useTitles:true});
					</script>";
			}
			else {
				$page_content .= "
					<a href=\"install.php?step=2\" class=\"button\">I Agree</a>";			
			}
			break;
		case 2:
			$page->setTemplateVar("PageTitle", INSTALLER_SCRIPT_NAME . " Step 2 - Database Connection");	
			
			// Create our license file
			if (INSTALLER_SCRIPT_IS_PROFESSIONAL_VERSION) {
				$serialNumber = keepsafe($_POST['serialNumber']);
				$registeredTo = keeptasafe($_POST['registeredTo']);
			}
			else {
				$serialNumber = "Free Edition";
				$registeredTo = "Fast Track Sites";
			}
			
			$str = "<?PHP\n\n\$A_License = \"" . $serialNumber . "\";\n\$A_Licensed_To = \"" . $registeredTo . "\";\n\n?>";
		
			$fp=fopen("_license.php","w");
			$result = fwrite($fp,$str);
			fclose($fp);	
			
			// Print this page
			$page_content = "
					<h2>License File Results</h2>";
			
			if (!$result || empty($result)) {
				$page_content .= "
					<span class=\"actionFailed\"><span class=\"iconText20px\"><img src=\"themes/installer/icons/delete_20px.png\" alt=\"Action Failed\" /></span> <span class=\"iconText20px\">Unable to create license file.</span></span>";
			}
			else {
				$page_content .= "
					<span class=\"actionSucceeded\"><span class=\"iconText20px\"><img src=\"themes/installer/icons/check_20px.png\" alt=\"Action Succeeded\" /></span> <span class=\"iconText20px\">Successfully created license file.</span></span>";
			}
			
			$page_content .= "
					<br /><br />
					<h2><span class=\"iconText38px\"><img src=\"themes/installer/icons/addDatabase_38px.png\" alt=\"Add Database\" /></span> <span class=\"iconText38px\">Configure Your Database Connection</span></h2>
					Please enter your database information below:
					<br /><br />
					<form id=\"databaseConnectionForm\" action=\"install.php?step=3\" method=\"post\">
						<label for=\"dbServer\">Server</label> <input type=\"text\" name=\"dbServer\" id=\"dbServer\" class=\"required\" />
						<label for=\"dbName\">Database Name</label> <input type=\"text\" name=\"dbName\" id=\"dbName\" class=\"required\" />
						<label for=\"dbUsername\">Username</label> <input type=\"text\" name=\"dbUsername\" id=\"dbUsername\" class=\"required\" />
						<label for=\"dbPassword\">Password</label> <input type=\"password\" name=\"dbPassword\" id=\"dbPassword\" class=\"required\" />
						<label for=\"dbTablePrefix\">Table Prefix</label> <input type=\"text\" name=\"dbTablePrefix\" id=\"dbTablePrefix\" class=\"required\" value=\"" . INSTALLER_SCRIPT_DB_PREFIX . "\" />
						<input type=\"submit\" name=\"submit\" class=\"button\" value=\"Next\" />
					</form>
					<script type=\"text/javascript\">
						var valid = new Validation('databaseConnectionForm', {immediate : true, useTitles:true});
					</script>";
			break;
		case 3:
			$page->setTemplateVar("PageTitle", INSTALLER_SCRIPT_NAME . " Step 3 - Create database Tables");	
			
			// Create our database connection file
			$dbServer = keepsafe($_POST['dbServer']); 
			$dbName = keepsafe($_POST['dbName']); 
			$dbUsername = keepsafe($_POST['dbUsername']); 
			$dbPassword = keepsafe($_POST['dbPassword']); 
			$DBTABLEPREFIX = keepsafe($_POST['dbTablePrefix']); 
			
			$str = "<?php\n\n// Connect to the database\n\n\$server = \"" . $dbServer . "\";\n\$dbuser = \"" . $dbUsername . "\";\n\$dbpass = \"" . $dbPassword . "\";\n\$dbname = \"" . $dbName . "\";\ndefine('DBTABLEPREFIX', '" . $DBTABLEPREFIX . "');\ndefine('USERSDBTABLEPREFIX', '" . $DBTABLEPREFIX . "');\n?>";
		
			$fp=fopen("_db.php","w");
			$result = fwrite($fp,$str);
			fclose($fp);	
	
			// Print this page
			$page_content = "
					<h2>Database Connection Results</h2>";
			
			if (!$result || empty($result)) {
				$page_content .= "
					<span class=\"actionFailed\"><span class=\"iconText20px\"><img src=\"themes/installer/icons/delete_20px.png\" alt=\"Action Failed\" /></span> <span class=\"iconText20px\">Unable to create database connection file.</span></span>";
			}
			else {
				$page_content .= "
					<span class=\"actionSucceeded\"><span class=\"iconText20px\"><img src=\"themes/installer/icons/check_20px.png\" alt=\"Action Succeeded\" /></span> <span class=\"iconText20px\">Successfully created database connection file.</span></span>";
			}
			
			$page_content .= "
					<br /><br />
					<h2><span class=\"iconText38px\"><img src=\"themes/installer/icons/table_38px.png\" alt=\"Create Tables\" /></span> <span class=\"iconText38px\">Create database Tables</span></h2>
					Press Next to create the database tables.
					<br /><br />
					<a href=\"install.php?step=4\" class=\"button\">Next</a>";
			break;
		case 4:
			$page->setTemplateVar("PageTitle", INSTALLER_SCRIPT_NAME . " Step 4 - Create Admin Account");	
			
			include('_db.php');
			$DB = new DB($server, $dbuser, $dbpass, $dbname); //initialize our DB connection
		
			// Create our Database Tables	  		
			$sql = "CREATE TABLE `" . DBTABLEPREFIX . "categories` (
				`id` mediumint(8) NOT NULL auto_increment,
				`name` varchar(100) NOT NULL default '',
				`order` mediumint(8) NOT NULL default '0',
				PRIMARY KEY			(`id`)
				) TYPE=MyISAM AUTO_INCREMENT=0;";
			$result = $DB->query($sql);
			checkresult($result, $sql, "categories");	
	
			$sql = "CREATE TABLE `" . DBTABLEPREFIX . "config` (
				`name` varchar(255) NOT NULL default '',
				`value` text NOT NULL
				) TYPE=MyISAM;";
			$result = $DB->query($sql);
			checkresult($result, $sql, "config");
	
			$sql = "CREATE TABLE `" . DBTABLEPREFIX . "forums` (
				`id` mediumint(8) NOT NULL auto_increment,
				`cat_id` mediumint(8) NOT NULL default '0',
				`parent_id` mediumint(8) NOT NULL default '0',
				`name` varchar(50) NOT NULL default '',
				`description` text NOT NULL,
				`posts` mediumint(8) NOT NULL default '0',
				`topics` mediumint(8) NOT NULL default '0',
				`order` mediumint(8) NOT NULL default '0',
				PRIMARY KEY			(`id`)
				) TYPE=MyISAM AUTO_INCREMENT=0;";
			$result = $DB->query($sql);
			checkresult($result, $sql, "forums");
	
			$sql = "CREATE TABLE `" . DBTABLEPREFIX . "posts` (
				`id` mediumint(8) NOT NULL auto_increment,
				`topic_id` mediumint(8) NOT NULL default '0',
				`user_id` mediumint(8) NOT NULL default '0',
				`datetimestamp` varchar(50) NOT NULL default '',
				`text` text,
				PRIMARY KEY			(`id`)
				) TYPE=MyISAM AUTO_INCREMENT=0;";
			$result = $DB->query($sql);
			checkresult($result, $sql, "posts");
	
			$sql = "CREATE TABLE `" . DBTABLEPREFIX . "messages` (
				`id` int(10) NOT NULL auto_increment,
				`status` tinyint(1) NOT NULL default '0',
				`folder` tinyint(1) NOT NULL default '0',
				`from_id` mediumint(8) NOT NULL default '0',
				`to_id` mediumint(8) NOT NULL default '0',
				`title` varchar(255) NOT NULL default '',
				`message` text,
				`datetimestamp` varchar(50) NOT NULL default '',
				PRIMARY KEY			(`id`)
				) TYPE=MyISAM AUTO_INCREMENT=0;";
			$result = $DB->query($sql);
			checkresult($result, $sql, "messages");
			
			$sql = "CREATE TABLE `" . DBTABLEPREFIX . "ranks` (
				`id` mediumint(8) NOT NULL auto_increment,
				`name` varchar(25) NOT NULL default '',
				`posts` mediumint(8) NOT NULL default '0',
				`image` varchar(100) NOT NULL default '',
				PRIMARY KEY			(`id`)
				) TYPE=MyISAM AUTO_INCREMENT=0;";
			$result = $DB->query($sql);
			checkresult($result, $sql, "ranks");
	
			$sql = "CREATE TABLE `" . DBTABLEPREFIX . "smilies` (
				`id` mediumint(8) NOT NULL auto_increment,
				`code` varchar(25) NOT NULL default '',
				`image` varchar(250) NOT NULL default '',
				PRIMARY KEY			(`id`)
				) TYPE=MyISAM AUTO_INCREMENT=0 ;";
			$result = $DB->query($sql);
			checkresult($result, $sql, "smilies");
			
			$sql = "CREATE TABLE `" . DBTABLEPREFIX . "topicicons` (
				`id` mediumint(8) NOT NULL auto_increment,
				`name` varchar(25) NOT NULL default '',
				`image` varchar(250) NOT NULL default '',
				PRIMARY KEY			(`id`)
				) TYPE=MyISAM AUTO_INCREMENT=0 ;";
			$result = $DB->query($sql);
			checkresult($result, $sql, "topicicons");
					
			$sql = "CREATE TABLE `" . DBTABLEPREFIX . "topics` (
				`id` mediumint(8) NOT NULL auto_increment,
				`forum_id` smallint(8) NOT NULL default '0',
				`user_id` mediumint(8) NOT NULL default '0',
				`topicicon_id` mediumint(8) unsigned NOT NULL default '0',
				`title` varchar(60) NOT NULL default '',
				`datetimestamp` varchar(50) NOT NULL default '',
				`views` mediumint(8) unsigned NOT NULL default '0',
				`replies` mediumint(8) unsigned NOT NULL default '0',
				`type` tinyint(3) unsigned NOT NULL default '0',
				`status` tinyint(3) NOT NULL default '0',
				PRIMARY KEY			(`id`)
				) TYPE=MyISAM AUTO_INCREMENT=0;";
			$result = $DB->query($sql);
			checkresult($result, $sql, "topics");
	
			$sql = "CREATE TABLE `" . DBTABLEPREFIX . "topics_read` (
				`id` mediumint(8) NOT NULL auto_increment,
				`user_id` mediumint(8) NOT NULL default '0',
				`forum_id` mediumint(8) NOT NULL default '0',
				`unread` mediumint(8) NOT NULL default '0',
				`topic_ids` text,
				PRIMARY KEY			(`id`)
				) TYPE=MyISAM AUTO_INCREMENT=0;";
			$result = $DB->query($sql);
			checkresult($result, $sql, "topics_read");
	
			$sql = "CREATE TABLE `" . USERSDBTABLEPREFIX . "users` (
				`id` mediumint(11) NOT NULL auto_increment,
				`active` tinyint(1) NOT NULL default '1',
				`user_level` tinyint(1) NOT NULL default '0',
				`username` varchar(255) NOT NULL default '',
				`password` varchar(255) NOT NULL default '',
				`first_name` varchar(50) NOT NULL default '',
				`last_name` varchar(50) NOT NULL default '',
				`email_address` varchar(100) NOT NULL default '',
				`title` varchar(50) NOT NULL default '',
				`gender` varchar(15) NOT NULL default '',
				`country` varchar(20) NOT NULL default '',
				`website` varchar(100) NOT NULL default '',
				`signup_date` varchar(50) NOT NULL default '',
				`notes` text NOT NULL,
				`info` text NOT NULL,
				`activation` varchar(255) NOT NULL default '',
				`signature` text NOT NULL,
				`aim` varchar(50) NOT NULL default '',
				`yim` varchar(50) NOT NULL default '',
				`msn` varchar(50) NOT NULL default '',
				`icq` varchar(50) NOT NULL default '',
				`birthday` int(10) NOT NULL default '',
				`avatar` varchar(100) NOT NULL default '',
				`style` varchar(50) NOT NULL default 'default',
				`language` varchar(50) NOT NULL default 'en',
				`avatar_type` tinyint(1) NOT NULL default '0',
				`attachsig` tinyint(1) NOT NULL default '1',
				`last_login` int(11) NOT NULL default '0',
				`posts` mediumint(8) NOT NULL default '0',
				PRIMARY KEY			(`id`)
				) TYPE=MyISAM AUTO_INCREMENT=0 ;";
			$result = $DB->query($sql);
			checkresult($result, $sql, "users");
	
			$sql = "CREATE TABLE `" . DBTABLEPREFIX . "usersonline` (
				`user_id` mediumint(8) NOT NULL default '0',
				`username` varchar(25) NOT NULL default '',
				`datetimestamp` int(15) NOT NULL default '0',
				`ip` varchar(40) NOT NULL default '',
				`file` varchar(100) NOT NULL default ''
				) TYPE=MyISAM;";
			$result = $DB->query($sql);
			checkresult($result, $sql, "usersonline");	
	
			$sql = "CREATE TABLE `" . DBTABLEPREFIX . "wordfilters` (
				`id` mediumint(8) NOT NULL auto_increment,
				`code` varchar(25) NOT NULL default '',
				`image` varchar(250) NOT NULL default '',
				PRIMARY KEY			(`id`)
				) TYPE=MyISAM AUTO_INCREMENT=0 ;";
			$result = $DB->query($sql);
			checkresult($result, $sql, "wordfilters");		
			
			// If we have our config table filled in then we've probably already run the install so lets not redo it
			$sql = "SELECT * FROM `" . DBTABLEPREFIX . "config`";
			$result = $DB->query($sql);
					
			if (!$result || $DB->num_rows() == 0) {
				$DB->free_result($result);
				
				$sql = "INSERT INTO `" . USERSDBTABLEPREFIX . "users` (`id`, `username`, `password`, `signup_date`, `user_level`) VALUES ('-1', 'Guest', '0', '" . time() . "', '3');";
				$result = $DB->query($sql);
				checkresult($result, $sql, "guestuser");
		
				$sql = "INSERT INTO `" . DBTABLEPREFIX . "ranks` VALUES (1, 'Noobie', 0, 'images/ranks/rank0.gif');";
				$result = $DB->query($sql);
				checkresult($result, $sql, "rankinsert1");
		
				$sql = "INSERT INTO `" . DBTABLEPREFIX . "ranks` VALUES (2, 'Member', 10, 'images/ranks/rank1.gif');";
				$result = $DB->query($sql);
				checkresult($result, $sql, "rankinsert2");
		
				$sql = "INSERT INTO `" . DBTABLEPREFIX . "ranks` VALUES (3, 'Guru', 50, 'images/ranks/rank2.gif');";
				$result = $DB->query($sql);
				checkresult($result, $sql, "rankinsert3");
		
				$sql = "INSERT INTO `" . DBTABLEPREFIX . "ranks` VALUES (4, 'Master Member', 100, 'images/ranks/rank3.gif');";
				$result = $DB->query($sql);
				checkresult($result, $sql, "rankinsert4");
				
				$sql = "INSERT INTO `" . DBTABLEPREFIX . "config` VALUES ('ftssafs_board_url', 'http://" . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['PHP_SELF']), '/\\') . "');";
				$result = $DB->query($sql);
				checkresult($result, $sql, "configinsert1");
				
				$sql="INSERT INTO `" . DBTABLEPREFIX . "config` VALUES ('ftssafs_theme', 'default');";
				$result = $DB->query($sql);
				checkresult($result, $sql, "configinsert2");
				
				$sql="INSERT INTO `" . DBTABLEPREFIX . "config` VALUES ('ftssafs_board_email_sig', 'Thanks, The Management');";
				$result = $DB->query($sql);
				checkresult($result, $sql, "configinsert3");
				
				$sql="INSERT INTO `" . DBTABLEPREFIX . "config` VALUES ('ftssafs_active', '1');";
				$result = $DB->query($sql);
				checkresult($result, $sql, "configinsert4");
				
				$sql="INSERT INTO `" . DBTABLEPREFIX . "config` VALUES ('ftssafs_inactive_msg', '');";
				$result = $DB->query($sql);
				checkresult($result, $sql, "configinsert5");
		
				$sql = "INSERT INTO `" . DBTABLEPREFIX . "config` VALUES ('ftssafs_time_zone', '-6.00');";
				$result = mysql_query($sql);
				checkresult($result, $sql, "configinsert6");
				
				$sql="INSERT INTO `" . DBTABLEPREFIX . "config` VALUES ('ftssafs_cookie_name', 'ftssafs');";
				$result = $DB->query($sql);
				checkresult($result, $sql, "configinsert7");
				
				$sql="INSERT INTO `" . DBTABLEPREFIX . "config` VALUES ('ftssafs_allow_bbcode', '1');";
				$result = $DB->query($sql);
				checkresult($result, $sql, "configinsert8");
				
				$sql="INSERT INTO `" . DBTABLEPREFIX . "config` VALUES ('ftssafs_allow_smilies', '1');";
				$result = $DB->query($sql);
				checkresult($result, $sql, "configinsert9");
				
				$sql="INSERT INTO `" . DBTABLEPREFIX . "config` VALUES ('ftssafs_allow_sig', '1');";
				$result = $DB->query($sql);
				checkresult($result, $sql, "configinsert10");
				
				$sql="INSERT INTO `" . DBTABLEPREFIX . "config` VALUES ('ftssafs_max_sig_chars', '400');";
				$result = $DB->query($sql);
				checkresult($result, $sql, "configinsert11");
				
				$sql="INSERT INTO `" . DBTABLEPREFIX . "config` VALUES ('ftssafs_allow_avatar_local', '1');";
				$result = $DB->query($sql);
				checkresult($result, $sql, "configinsert12");
				
				$sql="INSERT INTO `" . DBTABLEPREFIX . "config` VALUES ('ftssafs_allow_avatar_remote', '1');";
				$result = $DB->query($sql);
				checkresult($result, $sql, "configinsert13");
				
				$sql="INSERT INTO `" . DBTABLEPREFIX . "config` VALUES ('ftssafs_allow_avatar_upload', '1');";
				$result = $DB->query($sql);
				checkresult($result, $sql, "configinsert14");
				
				$sql="INSERT INTO `" . DBTABLEPREFIX . "config` VALUES ('ftssafs_avatar_filesize', '10000');";
				$result = $DB->query($sql);
				checkresult($result, $sql, "configinsert15");
				
				$sql="INSERT INTO `" . DBTABLEPREFIX . "config` VALUES ('ftssafs_avatar_max_width', '100');";
				$result = $DB->query($sql);
				checkresult($result, $sql, "configinsert16");
				
				$sql="INSERT INTO `" . DBTABLEPREFIX . "config` VALUES ('ftssafs_avatar_max_height', '100');";
				$result = $DB->query($sql);
				checkresult($result, $sql, "configinsert17");
				
				$sql="INSERT INTO `" . DBTABLEPREFIX . "config` VALUES ('ftssafs_posts_per_page', '15');";
				$result = $DB->query($sql);
				checkresult($result, $sql, "configinsert18");
				
				$sql="INSERT INTO `" . DBTABLEPREFIX . "config` VALUES ('ftssafs_topics_per_page', '50');";
				$result = $DB->query($sql);
				checkresult($result, $sql, "configinsert19");
				
				$sql="INSERT INTO `" . DBTABLEPREFIX . "config` VALUES ('ftssafs_max_inbox_privmsgs', '50');";
				$result = $DB->query($sql);
				checkresult($result, $sql, "configinsert20");
				
				$sql="INSERT INTO `" . DBTABLEPREFIX . "config` VALUES ('ftssafs_max_sent_privmsgs', '25');";
				$result = $DB->query($sql);
				checkresult($result, $sql, "configinsert21");
				
				$sql="INSERT INTO `" . DBTABLEPREFIX . "config` VALUES ('ftssafs_max_archived_privmsgs', '50');";
				$result = $DB->query($sql);
				checkresult($result, $sql, "configinsert22");
				
				$sql="INSERT INTO `" . DBTABLEPREFIX . "config` VALUES ('ftssafs_privmsg_active', '1');";
				$result = $DB->query($sql);
				checkresult($result, $sql, "configinsert23");
				
				$sql="INSERT INTO `" . DBTABLEPREFIX . "config` VALUES ('ftssafs_avatar_path', 'images/avatars/');";
				$result = $DB->query($sql);
				checkresult($result, $sql, "configinsert24");
				
				$sql="INSERT INTO `" . DBTABLEPREFIX . "config` VALUES ('ftssafs_avatar_gallery_path', 'images/avatars/gallery');";
				$result = $DB->query($sql);
				checkresult($result, $sql, "configinsert25");
				
				$sql="INSERT INTO `" . DBTABLEPREFIX . "config` VALUES ('ftssafs_smilies_path', 'images/smilies');";
				$result = $DB->query($sql);
				checkresult($result, $sql, "configinsert26");
				
				$sql="INSERT INTO `" . DBTABLEPREFIX . "config` VALUES ('ftssafs_topic_icons_path', 'images/topic_icons');";
				$result = $DB->query($sql);
				checkresult($result, $sql, "configinsert27");
				
				$sql="INSERT INTO `" . DBTABLEPREFIX . "config` VALUES ('ftssafs_ranks_path', 'images/ranks');";
				$result = $DB->query($sql);
				checkresult($result, $sql, "configinsert28");
				
				$sql="INSERT INTO `" . DBTABLEPREFIX . "config` VALUES ('ftssafs_announcement_text', 'This is an example announcement message. Go to the administration panel to change this.');";
				$result = $DB->query($sql);
				checkresult($result, $sql, "configinsert29");
				
				$sql="INSERT INTO `" . DBTABLEPREFIX . "config` VALUES ('ftssafs_announcement_title', 'Example Message');";
				$result = $DB->query($sql);
				checkresult($result, $sql, "configinsert30");
				
				$sql="INSERT INTO `" . DBTABLEPREFIX . "config` VALUES ('ftssafs_activation_active', '1');";
				$result = $DB->query($sql);
				checkresult($result, $sql, "configinsert31");
				
				$sql="INSERT INTO `" . DBTABLEPREFIX . "config` VALUES ('ftssafs_items_per_page', '50');";
				$result = $DB->query($sql);
				checkresult($result, $sql, "configinsert32");
				
				$sql="INSERT INTO `" . DBTABLEPREFIX . "config` VALUES ('ftssafs_default_avatar', 'images/avatars/no_avatar.jpg');";
				$result = $DB->query($sql);
				checkresult($result, $sql, "configinsert33");
													
				$sql="INSERT INTO `" . DBTABLEPREFIX . "categories` VALUES (0, 'New Category', 1);";
				$result = $DB->query($sql);
				checkresult($result, $sql, "samplecategory");
			
				$sql="INSERT INTO `" . DBTABLEPREFIX . "forums` VALUES (0, 1, 0, 'New Forum', 'This is a new forum, and can be changed using the admin panel.', 1, 1, 1);";
				$result = $DB->query($sql);
				checkresult($result, $sql, "sampleforum");
				
				$sql="INSERT INTO `" . DBTABLEPREFIX . "wordfilter` VALUES (1, 'CensorMe', 'images/smilies/censored.gif');";
				$result = $DB->query($sql);
				checkresult($result, $sql, "profanityinsert1");
				
				$sql="INSERT INTO `" . DBTABLEPREFIX . "smilies` VALUES (1, ':)', 'images/smilies/smile.gif');";
				$result = $DB->query($sql);
				checkresult($result, $sql, "smiliesinsert1");
				
				$sql="INSERT INTO `" . DBTABLEPREFIX . "smilies` VALUES (3, ':arrows:', 'images/smilies/arrows.gif');";
				$result = $DB->query($sql);
				checkresult($result, $sql, "smiliesinsert2");
				
				$sql="INSERT INTO `" . DBTABLEPREFIX . "smilies` VALUES (4, ':censored:', 'images/smilies/censored.gif');";
				$result = $DB->query($sql);
				checkresult($result, $sql, "smiliesinsert3");
				
				$sql="INSERT INTO `" . DBTABLEPREFIX . "smilies` VALUES (5, ':bigsmile:', 'images/smilies/bigSmile.gif');";
				$result = $DB->query($sql);
				checkresult($result, $sql, "smiliesinsert4");
				
				$sql="INSERT INTO `" . DBTABLEPREFIX . "smilies` VALUES (6, ':blush:', 'images/smilies/blush.gif');";
				$result = $DB->query($sql);
				checkresult($result, $sql, "smiliesinsert5");
				
				$sql="INSERT INTO `" . DBTABLEPREFIX . "smilies` VALUES (7, ':confused:', 'images/smilies/confused.gif');";
				$result = $DB->query($sql);
				checkresult($result, $sql, "smiliesinsert6");
				
				$sql="INSERT INTO `" . DBTABLEPREFIX . "smilies` VALUES (8, ':cool:', 'images/smilies/cool.gif');";
				$result = $DB->query($sql);
				checkresult($result, $sql, "smiliesinsert7");
				
				$sql="INSERT INTO `" . DBTABLEPREFIX . "smilies` VALUES (9, ':cry:', 'images/smilies/cry.gif');";
				$result = $DB->query($sql);
				checkresult($result, $sql, "smiliesinsert8");
				
				$sql="INSERT INTO `" . DBTABLEPREFIX . "smilies` VALUES (10, ':erm:', 'images/smilies/erm.gif');";
				$result = $DB->query($sql);
				checkresult($result, $sql, "smiliesinsert9");
				
				$sql="INSERT INTO `" . DBTABLEPREFIX . "smilies` VALUES (11, ':exclamation:', 'images/smilies/exclamation.gif');";
				$result = $DB->query($sql);
				checkresult($result, $sql, "smiliesinsert10");
				
				$sql="INSERT INTO `" . DBTABLEPREFIX . "smilies` VALUES (12, ':jawdrop:', 'images/smilies/jawDrop.gif');";
				$result = $DB->query($sql);
				checkresult($result, $sql, "smiliesinsert11");
				
				$sql="INSERT INTO `" . DBTABLEPREFIX . "smilies` VALUES (13, ':mad:', 'images/smilies/mad.gif');";
				$result = $DB->query($sql);
				checkresult($result, $sql, "smiliesinsert12");
				
				$sql="INSERT INTO `" . DBTABLEPREFIX . "smilies` VALUES (14, ':ninjabattle:', 'images/smilies/ninjabattle.gif');";
				$result = $DB->query($sql);
				checkresult($result, $sql, "smiliesinsert13");
				
				$sql="INSERT INTO `" . DBTABLEPREFIX . "smilies` VALUES (15, ':ninjameditate:', 'images/smilies/ninjameditate.gif');";
				$result = $DB->query($sql);
				checkresult($result, $sql, "smiliesinsert14");
				
				$sql="INSERT INTO `" . DBTABLEPREFIX . "smilies` VALUES (16, ':peeved:', 'images/smilies/peeved.gif');";
				$result = $DB->query($sql);
				checkresult($result, $sql, "smiliesinsert15");
				
				$sql="INSERT INTO `" . DBTABLEPREFIX . "smilies` VALUES (17, ':?:', 'images/smilies/question.gif');";
				$result = $DB->query($sql);
				checkresult($result, $sql, "smiliesinsert16");
				
				$sql="INSERT INTO `" . DBTABLEPREFIX . "smilies` VALUES (18, ':P', 'images/smilies/razz.gif');";
				$result = $DB->query($sql);
				checkresult($result, $sql, "smiliesinsert17");
				
				$sql="INSERT INTO `" . DBTABLEPREFIX . "smilies` VALUES (19, ':rolleyes:', 'images/smilies/rolleyes.gif');";
				$result = $DB->query($sql);
				checkresult($result, $sql, "smiliesinsert18");
				
				$sql="INSERT INTO `" . DBTABLEPREFIX . "smilies` VALUES (20, ':suspicious:', 'images/smilies/suspicious.gif');";
				$result = $DB->query($sql);
				checkresult($result, $sql, "smiliesinsert19");
				
				$sql="INSERT INTO `" . DBTABLEPREFIX . "smilies` VALUES (21, ':tongue:', 'images/smilies/tongue.gif');";
				$result = $DB->query($sql);
				checkresult($result, $sql, "smiliesinsert20");
				
				$sql="INSERT INTO `" . DBTABLEPREFIX . "topicicons` VALUES (1, 'Exclamation', 'images/topic_icons/excl.png');";
				$result = $DB->query($sql);
				checkresult($result, $sql, "topiciconsinsert1");
				
				$sql="INSERT INTO `" . DBTABLEPREFIX . "topicicons` VALUES (2, 'Frown', 'images/topic_icons/frown.png');";
				$result = $DB->query($sql);
				checkresult($result, $sql, "topiciconsinsert2");
				
				$sql="INSERT INTO `" . DBTABLEPREFIX . "topicicons` VALUES (3, 'Heart', 'images/topic_icons/heart.png');";
				$result = $DB->query($sql);
				checkresult($result, $sql, "topiciconsinsert3");
				
				$sql="INSERT INTO `" . DBTABLEPREFIX . "topicicons` VALUES (4, 'Question Mark', 'images/topic_icons/question.png');";
				$result = $DB->query($sql);
				checkresult($result, $sql, "topiciconsinsert4");
				
				$sql="INSERT INTO `" . DBTABLEPREFIX . "topicicons` VALUES (5, 'Right Arrows', 'images/topic_icons/rightarrows.png');";
				$result = $DB->query($sql);
				checkresult($result, $sql, "topiciconsinsert5");
				
				$sql="INSERT INTO `" . DBTABLEPREFIX . "topicicons` VALUES (6, 'Smile', 'images/topic_icons/smile.png');";
				$result = $DB->query($sql);
				checkresult($result, $sql, "topiciconsinsert6");
			}
		
			// Print this page
			$page_content = "
					<h2>Insert Table Results</h2>";
			
			if ($totalfailure == 1) {
				$page_content .= "
					<span class=\"actionFailed\"><span class=\"iconText20px\"><img src=\"themes/installer/icons/delete_20px.png\" alt=\"Action Failed\" /></span> <span class=\"iconText20px\">Unable to create database tables.</span></span>";
			}
			else {
				$page_content .= "
					<span class=\"actionSucceeded\"><span class=\"iconText20px\"><img src=\"themes/installer/icons/check_20px.png\" alt=\"Action Succeeded\" /></span> <span class=\"iconText20px\">Successfully created database tables.</span></span>";
			}
			
			$page_content .= "
					<br /><br />
					<h2><span class=\"iconText38px\"><img src=\"themes/installer/icons/addUser_38px.png\" alt=\"Add User\" /></span> <span class=\"iconText38px\">Create Your Admin Account</span></h2>
					Please enter your admin user information below:
					<form id=\"adminAccountForm\" action=\"install.php?step=5\" method=\"post\">
						<label for=\"usrUsername\">Username</label> <input type=\"text\" name=\"usrUsername\" id=\"usrUsername\" class=\"required validate-alphanum\" />
						<label for=\"usrEmailAddress\">Email Address</label> <input type=\"text\" name=\"usrEmailAddress\" id=\"usrEmailAddress\" class=\"required validate-email\" />
						<label for=\"usrPassword\">Password</label> <input type=\"password\" name=\"usrPassword\" id=\"usrPassword\" class=\"required validate-password\" />
						<label for=\"usrConfirmPassword\">Confirm Password</label> <input type=\"password\" name=\"usrConfirmPassword\" id=\"usrConfirmPassword\" class=\"required validate-password-confirm\" />
						<input type=\"submit\" name=\"submit\" class=\"button\" value=\"Next\" />
					</form>
					<script type=\"text/javascript\">
						var valid = new Validation('adminAccountForm', {immediate : true, useTitles:true});
						Validation.addAllThese([
								['validate-password', 'Your password must be at least 7 characters and cannot be your username, the word password, 1234567, or 0123456.', {
								minLength : 7,
								notOneOf : ['password','PASSWORD','1234567','0123456'],
								notEqualToField : 'usrUsername'
							}],
							['validate-password-confirm', 'Your passwords do not match.', {
								equalToField : 'usrPassword'
							}]
						]);
					</script>";
			break;
		case 5:
			$page->setTemplateVar("PageTitle", INSTALLER_SCRIPT_NAME . " Step 5 - Configure Your System");	
			
			include('_db.php');
			$DB = new DB($server, $dbuser, $dbpass, $dbname); //initialize our DB connection
				
	    	// Create our admin account
			$usrUsername = keepsafe($_POST['usrUsername']); 
			$usrPassword = md5(keepsafe($_POST['usrPassword']));
			$usrEmailAddress = keepsafe($_POST['usrEmailAddress']);
		
			// If we have our user in the table then we've probably already run this step so lets not redo it
			$sql = "SELECT * FROM `" . USERSDBTABLEPREFIX . "users` WHERE username = '" . $usrUsername . "'";
			$result = $DB->query($sql);
					
			if (!$result || $DB->num_rows() == 0) {
				$DB->free_result($result);
				
				$sql = "INSERT INTO `" . USERSDBTABLEPREFIX . "users` (`username`, `password`, `email_address`, `signup_date`, `notes`, `user_level`) VALUES ('" . $usrUsername . "', '" . $usrPassword . "', '" . $usrEmailAddress . "', '" . time() . "', '', '1');";
		    	$result = $DB->query($sql);
			    checkresult($result, $sql, "AdminUser");
		
				$sql = "INSERT INTO `" . DBTABLEPREFIX . "config` VALUES ('ftssafs_board_email', '" . $postemail . "');";
			    $result = $DB->query($sql);
			    checkresult($result, $sql, "configInsert");
			
				$sql="INSERT INTO `" . DBTABLEPREFIX . "topics` VALUES (1, 1, 1, 1, 'Welcome to SAFS!', '', '" . time() . "', 0, 0, 1, 0);";
				$result = $DB->query($sql);
				checkresult($result, $sql, "sampletopic");		
		
				$sql="INSERT INTO `" . DBTABLEPREFIX . "posts` VALUES (1, 1, 1, '" . time() . "', '[u][b]Welcome to SAFS![/b][/u]\r\nThank you for downloading and installing the Fast Track Sites Simply AJAX Forum SYSTEM or SAFS for short. We are always working to improve SAFS as well as our other programs, so make sure to check [url=http://forum.fasttracksites.com]our forums[/url] for updates.\r\n\r\n[b]The Fast Track Sites Development Team[/b]');";
				$result = $DB->query($sql);
				checkresult($result, $sql, "samplepost");
			}
		
			// Print this page
			$page_content = "
					<h2>Create Your Admin Account Results</h2>";
			
			if ($totalfailure == 1) {
				$page_content .= "
					<span class=\"actionFailed\"><span class=\"iconText20px\"><img src=\"themes/installer/icons/delete_20px.png\" alt=\"Action Failed\" /></span> <span class=\"iconText20px\">Unable to create admin account.</span></span>";
			}
			else {
				$page_content .= "
					<span class=\"actionSucceeded\"><span class=\"iconText20px\"><img src=\"themes/installer/icons/check_20px.png\" alt=\"Action Succeeded\" /></span> <span class=\"iconText20px\">Successfully created admin account.</span></span>";
			}
			
			$page_content .= "
					<br /><br />
					<h2><span class=\"iconText38px\"><img src=\"themes/installer/icons/configure_38px.png\" alt=\"Configure Your System\" /></span> <span class=\"iconText38px\">Configure Your System</span></h2>
					Please enter your board information below:
					<form id=\"configureSystemForm\" action=\"install.php?step=6\" method=\"post\">
						<label for=\"sysName\">Board Name</label> <input type=\"text\" name=\"sysName\" id=\"sysName\" class=\"required\" />
						<label for=\"sysDescription\">Description</label> <textarea name=\"sysDescription\" id=\"sysDescription\"></textarea>
						<input type=\"submit\" name=\"submit\" class=\"button\" value=\"Next\" />
					</form>
					<script type=\"text/javascript\">
						var valid = new Validation('configureSystemForm', {immediate : true, useTitles:true});
					</script>";
			break;	
		case 6:
			$page->setTemplateVar("PageTitle", INSTALLER_SCRIPT_NAME . " Step 6 - Finish");	
			
			include('_db.php');
			$DB = new DB($server, $dbuser, $dbpass, $dbname); //initialize our DB connection
				
	    	// Create our admin account
			$sysName = keepsafe($_POST['sysName']); 
			$sysDescription = keepsafe($_POST['sysDescription']);
		
			// If we have our board name in the table then we've probably already run this step so lets not redo it
			$sql = "SELECT * FROM `" . DBTABLEPREFIX . "config` WHERE name = '" . $ftssafs_board_name . "'";
			$result = $DB->query($sql);
					
			if (!$result || $DB->num_rows() == 0) {
				$DB->free_result($result);
					
				$sql = "INSERT INTO `" . DBTABLEPREFIX . "config` VALUES ('ftssafs_board_name', '" . $sysName . "');";
				$result = $DB->query($sql);
				checkresult($result, $sql, "configinsert1");
				
				$sql = "INSERT INTO `" . DBTABLEPREFIX . "config` VALUES ('ftssafs_site_desc', '" . $sysDescription . "');";
				$result = $DB->query($sql);
				checkresult($result, $sql, "configinsert2");
			}
			
			// Print this page
			$page_content = "
					<h2>Configure Your System Results</h2>";
			
			if ($totalfailure == 1) {
				$page_content .= "
					<span class=\"actionFailed\"><span class=\"iconText20px\"><img src=\"themes/installer/icons/delete_20px.png\" alt=\"Action Failed\" /></span> <span class=\"iconText20px\">Unable to create admin account.</span></span>";
			}
			else {
				$page_content .= "
					<span class=\"actionSucceeded\"><span class=\"iconText20px\"><img src=\"themes/installer/icons/check_20px.png\" alt=\"Action Succeeded\" /></span> <span class=\"iconText20px\">Successfully created admin account.</span></span>";
			}
			
			$page_content .= "
					<h2>Finishing Up</h2>
					Installation is now complete, before using the system please make sure and delete this file (install.php) so that it cannot be reused by someone else.
					<br /><br />
					<a href=\"index.php\" class=\"button\">Finish</a>";
			break;	
	}
	
	// Send out the content
	$page->setTemplateVar("PageContent", $page_content);	
	
	include "themes/installer/template.php";
?>