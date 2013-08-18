<? 
/***************************************************************************
 *                                install.php
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
define('IN_FTSSAFS', true);
include_once ('includes/menu.php');
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" >
<head>
	<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
	<title>Fast Track Sites Simply AJAX Forum Install Page</title>

	<!--Stylesheets Begin-->
	<link rel="stylesheet" type="text/css" href="themes/style.css" />
	<link rel="stylesheet" type="text/css" href="themes/default/dim.css" />
	<link rel="stylesheet" type="text/css" href="themes/default/main.css" />
	<!--Stylesheets End-->
	<link rel="shortcut icon" href="favicon.ico" />
</head>
<body>
<div id="container">
			<div id="page">
				<div id="header">
					<img src="images/header.png" alt="header" />
					<center><h3>Fast Track Sites Simply AJAX Forum Install Page</h3></center>
				</div>
				<div id="content">
				<center>
<?
	function checkresult($result, $table, $sql) {
		global $failed;
		global $failedsql;
		global $totalfailure;
		
		if (!$result || $result == "") {
			$failed[$table] = "failed";
			$failedsql[$table] = $sql;
			$totalfailure = 1;
		}  
		else {
			$failed[$table] = "succeeded";
			$failedsql[$table] = $sql;
		}	
	}
	
	if (isset($_POST[submit])) {
		$failed = 0;
		$totalfailure = 0;
		$failed = array();
		$failedsql = array();
		
		$adminname = $_POST[adminname]; 
		$pass = $_POST[password]; 
		$password = md5($pass);
		$currenttime = time();
		$postname = $_POST[board_name];
		$postemail = $_POST[board_email];
		$postdesc = $_POST[site_desc];
		$postusername = $_POST[username];
		$posturl = $_POST[url];
	
		$str = "<?PHP\n\n// Connect to the database\n\n\$server = \"" . $_POST[dbserver] . "\";\n\$dbuser = \"" . $_POST[dbusername] . "\";\n\$dbpass = \"" . $_POST[dbpassword] . "\";\n\$dbname = \"" . $_POST[dbname] . "\";\n\$DBTABLEPREFIX = \"" . $_POST[dbtableprefix] . "\";\n\n\$connect = mysql_connect(\$server,\$dbuser,\$dbpass);\n\n//display error if connection fails\nif (\$connect==FALSE) {\n   print 'Unable to connect to database: '.mysql_error();\n   exit;\n}\n\nmysql_select_db(\$dbname); // select database\n\n?>";
		
		$fp=fopen("_db.php","w");
		$result = fwrite($fp,$str);
		fclose($fp);		
		checkresult($result, "dbconnection", "The installation program failed to create a connection file to your database, you will manually need to do this. Please see the readme file for more information.");
	
	  	include '_db.php';
	  		
		$sql = "CREATE TABLE `" . $DBTABLEPREFIX . "categories` (
			`cat_id` mediumint(8) NOT NULL auto_increment,
			`cat_title` varchar(100) NOT NULL default '',
			`cat_order` mediumint(8) NOT NULL default '0',
			PRIMARY KEY			(`cat_id`)
			) TYPE=MyISAM AUTO_INCREMENT=0;";
		$result = mysql_query($sql);
		checkresult($result, "categories", $sql);


		$sql = "CREATE TABLE `" . $DBTABLEPREFIX . "config` (
			`config_name` varchar(255) NOT NULL default '',
			`config_value` varchar(255) NOT NULL default '',
			`config_extra_value` text NOT NULL
			) TYPE=MyISAM;";
		$result = mysql_query($sql);
		checkresult($result, "config", $sql);

		$sql = "CREATE TABLE `" . $DBTABLEPREFIX . "forums` (
			`forum_id` smallint(5) NOT NULL auto_increment,
			`forum_cat_id` mediumint(8) NOT NULL default '0',
			`forum_name` varchar(50) NOT NULL default '',
			`forum_desc` text NOT NULL,
			`forum_posts` mediumint(8) NOT NULL default '0',
			`forum_topics` mediumint(8) NOT NULL default '0',
			`forum_subforum` mediumint(8) NOT NULL default '0',
			`forum_order` mediumint(8) NOT NULL default '0',
						PRIMARY KEY			(`forum_id`)
			) TYPE=MyISAM AUTO_INCREMENT=0;";
		$result = mysql_query($sql);
		checkresult($result, "forums", $sql);

		$sql = "CREATE TABLE `" . $DBTABLEPREFIX . "posts` (
			`post_id` mediumint(8) NOT NULL auto_increment,
			`post_topic_id` mediumint(8) NOT NULL default '0',
			`post_poster_id` mediumint(8) NOT NULL default '0',
			`post_time` int(11) NOT NULL default '0',
			`post_username` varchar(25) NOT NULL default '',
			`post_subject` varchar(60) default NULL,
			`post_text` text,
						PRIMARY KEY			(`post_id`)
			) TYPE=MyISAM AUTO_INCREMENT=0;";
		$result = mysql_query($sql);
		checkresult($result, "posts", $sql);

		$sql = "CREATE TABLE `" . $DBTABLEPREFIX . "posts_read` (
			`pr_id` mediumint(8) NOT NULL auto_increment,
			`pr_topic_id` mediumint(8) default '0',
			`pr_userid` mediumint(8) NOT NULL default '0',
						PRIMARY KEY			(`pr_id`)
			) TYPE=MyISAM AUTO_INCREMENT=0;";
		$result = mysql_query($sql);
		checkresult($result, "posts_read", $sql);

		$sql = "CREATE TABLE `" . $DBTABLEPREFIX . "priv_msgs` (
			`msg_id` int(10) NOT NULL auto_increment,
			`msg_read` tinyint(1) NOT NULL default '0',
			`msg_notify` tinyint(1) NOT NULL default '0',
			`msg_folder` varchar(32) NOT NULL default '0',
			`msg_date` int(10) default NULL,
			`msg_from_id` mediumint(8) NOT NULL default '0',
			`msg_to_id` mediumint(8) NOT NULL default '0',
			`msg_title` varchar(255) NOT NULL default '',
			`msg_post` text,
						PRIMARY KEY			(`msg_id`)
			) TYPE=MyISAM AUTO_INCREMENT=0;";
		$result = mysql_query($sql);
		checkresult($result, "priv_msgs", $sql);

		$sql = "CREATE TABLE `" . $DBTABLEPREFIX . "profanityfilter` (
			`profanityfilter_id` mediumint(8) NOT NULL auto_increment,
			`profanityfilter_code` varchar(25) NOT NULL default '',
			`profanityfilter_image` varchar(250) NOT NULL default '',
			PRIMARY KEY			(`profanityfilter_id`)
			) TYPE=MyISAM AUTO_INCREMENT=0 ;";
		$result = mysql_query($sql);
		checkresult($result, "profanityfilter", $sql);
		
		$sql = "CREATE TABLE `" . $DBTABLEPREFIX . "ranks` (
			`rank_id` mediumint(8) NOT NULL auto_increment,
			`rank_name` varchar(25) NOT NULL default '',
			`rank_posts` mediumint(8) NOT NULL default '0',
			`rank_image` varchar(100) NOT NULL default '',
						PRIMARY KEY			(`rank_id`)
			) TYPE=MyISAM AUTO_INCREMENT=0;";
		$result = mysql_query($sql);
		checkresult($result, "ranks", $sql);

		$sql = "CREATE TABLE `" . $DBTABLEPREFIX . "smilies` (
			`smilies_id` mediumint(8) NOT NULL auto_increment,
			`smilies_code` varchar(25) NOT NULL default '',
			`smilies_image` varchar(250) NOT NULL default '',
			PRIMARY KEY			(`smilies_id`)
			) TYPE=MyISAM AUTO_INCREMENT=0 ;";
		$result = mysql_query($sql);
		checkresult($result, "smilies", $sql);
		
		$sql = "CREATE TABLE `" . $DBTABLEPREFIX . "topicicons` (
			`topicicons_id` mediumint(8) NOT NULL auto_increment,
			`topicicons_name` varchar(25) NOT NULL default '',
			`topicicons_image` varchar(250) NOT NULL default '',
			PRIMARY KEY			(`topicicons_id`)
			) TYPE=MyISAM AUTO_INCREMENT=0 ;";
		$result = mysql_query($sql);
		checkresult($result, "topicicons", $sql);
				
		$sql = "CREATE TABLE `" . $DBTABLEPREFIX . "topics` (
			`topic_id` mediumint(8) NOT NULL auto_increment,
			`topic_forum_id` smallint(8) NOT NULL default '0',
			`topic_title` varchar(60) NOT NULL default '',
			`topic_icon` varchar(200) NOT NULL default '',			
			`topic_time` int(11) NOT NULL default '0',
			`topic_poster` mediumint(8) NOT NULL default '0',
			`topic_views` mediumint(8) unsigned NOT NULL default '0',
			`topic_replies` mediumint(8) unsigned NOT NULL default '0',
			`topic_type` tinyint(3) unsigned NOT NULL default '0',
			`topic_status` tinyint(3) NOT NULL default '0',
			`topic_first_post_id` mediumint(8) unsigned NOT NULL default '0',
						PRIMARY KEY			(`topic_id`)
			) TYPE=MyISAM AUTO_INCREMENT=0;";
		$result = mysql_query($sql);
		checkresult($result, "topics", $sql);

		$sql = "CREATE TABLE `" . $DBTABLEPREFIX . "users` (
			`users_id` mediumint(11) NOT NULL auto_increment,
			`users_username` varchar(255) NOT NULL default '',
			`users_password` varchar(255) NOT NULL default '',
			`users_first_name` varchar(50) NOT NULL default '',
			`users_last_name` varchar(50) NOT NULL default '',
			`users_email_address` varchar(100) NOT NULL default '',
			`users_title` varchar(50) NOT NULL default '',
			`users_gender` varchar(15) NOT NULL default '',
			`users_style` varchar(50) NOT NULL default 'default',
			`users_language` varchar(50) NOT NULL default 'en',
			`users_country` varchar(20) NOT NULL default '',
			`users_info` text NOT NULL,
			`users_website` varchar(100) NOT NULL default '',
			`users_sig` text NOT NULL,
			`users_aim` varchar(255) NOT NULL default '',
			`users_yim` varchar(255) NOT NULL default '',
			`users_msn` varchar(255) NOT NULL default '',
			`users_birthday` int(10) NOT NULL default '0',
			`users_avatar` varchar(100) NOT NULL default 'images/avatars/no_avatar.jpg',
			`users_avatar_type` tinyint(1) NOT NULL default '0',
			`users_attachsig` tinyint(1) NOT NULL default '1',
			`users_last_login` int(11) NOT NULL default '0',
			`users_posts` mediumint(8) NOT NULL default '0',
			`users_signup_date` int(11) default NULL,
			`users_notes` text NOT NULL,
			`users_user_level` tinyint(1) NOT NULL default '0',
			`users_active` tinyint(1) NOT NULL default '1',
			`users_activation` varchar(255) NOT NULL default '',
			PRIMARY KEY			(`users_id`)
			) TYPE=MyISAM AUTO_INCREMENT=0 ;";
		$result = mysql_query($sql);
		checkresult($result, "users", $sql);

		$sql = "CREATE TABLE `" . $DBTABLEPREFIX . "usersonline` (
			`uo_username` varchar(25) NOT NULL default '',
			`uo_timestamp` int(15) NOT NULL default '0',
			`uo_ip` varchar(40) NOT NULL default '',
			`uo_file` varchar(100) NOT NULL default ''
			) TYPE=MyISAM;";
		$result = mysql_query($sql);
		checkresult($result, "usersonline", $sql);

		$sql = "INSERT INTO `" . $DBTABLEPREFIX . "users` (`users_username`, `users_password`, `users_email_address`, `users_signup_date`, `users_user_level`) VALUES ('$postusername', '$password', '$postemail', '$currenttime', '1');";
		$result = mysql_query($sql);
		checkresult($result, "adminuser", $sql);

		$sql = "INSERT INTO `" . $DBTABLEPREFIX . "users` (`users_id`, `users_username`, `users_password`, `users_signup_date`, `users_user_level`) VALUES ('-1', 'Guest', '0', '$currenttime', '3');";
		$result = mysql_query($sql);
		checkresult($result, "guestuser", $sql);

		$sql = "INSERT INTO `" . $DBTABLEPREFIX . "ranks` VALUES (1, 'Noobie', 0, 'images/ranks/rank0.gif');";
		$result = mysql_query($sql);
		checkresult($result, "rankinsert1", $sql);

		$sql = "INSERT INTO `" . $DBTABLEPREFIX . "ranks` VALUES (2, 'Member', 10, 'images/ranks/rank1.gif');";
		$result = mysql_query($sql);
		checkresult($result, "rankinsert2", $sql);

		$sql = "INSERT INTO `" . $DBTABLEPREFIX . "ranks` VALUES (3, 'Guru', 50, 'images/ranks/rank2.gif');";
		$result = mysql_query($sql);
		checkresult($result, "rankinsert3", $sql);

		$sql = "INSERT INTO `" . $DBTABLEPREFIX . "ranks` VALUES (4, 'Master Member', 100, 'images/ranks/rank3.gif');";
		$result = mysql_query($sql);
		checkresult($result, "rankinsert4", $sql);

		$sql = "INSERT INTO `" . $DBTABLEPREFIX . "config` VALUES ('ftssafs_board_name', '$postname', '');";
		$result = mysql_query($sql);
		checkresult($result, "configinsert1", $sql);
		
		$sql = "INSERT INTO `" . $DBTABLEPREFIX . "config` VALUES ('ftssafs_site_desc', '$postdesc', '');";
		$result = mysql_query($sql);
		checkresult($result, "configinsert2", $sql);
		
		$sql = "INSERT INTO `" . $DBTABLEPREFIX . "config` VALUES ('ftssafs_board_email', '$postemail', '');";
		$result = mysql_query($sql);
		checkresult($result, "configinsert3", $sql);
		
		$sql = "INSERT INTO `" . $DBTABLEPREFIX . "config` VALUES ('ftssafs_board_url', '$posturl', '');";
		$result = mysql_query($sql);
		checkresult($result, "configinsert4", $sql);
		
		$sql="INSERT INTO `" . $DBTABLEPREFIX . "config` VALUES ('ftssafs_theme', 'default', '');";
		$result = mysql_query($sql);
		checkresult($result, "configinsert5", $sql);
		
		$sql="INSERT INTO `" . $DBTABLEPREFIX . "config` VALUES ('ftssafs_board_email_sig', 'Thanks, The Management', '');";
		$result = mysql_query($sql);
		checkresult($result, "configinsert6", $sql);
		
		$sql="INSERT INTO `" . $DBTABLEPREFIX . "config` VALUES ('ftssafs_active', '1', '');";
		$result = mysql_query($sql);
		checkresult($result, "configinsert7", $sql);
		
		$sql="INSERT INTO `" . $DBTABLEPREFIX . "config` VALUES ('ftssafs_inactive_msg', '', '');";
		$result = mysql_query($sql);
		checkresult($result, "configinsert8", $sql);
		
		$sql="INSERT INTO `" . $DBTABLEPREFIX . "config` VALUES ('ftssafs_cookie_name', 'ftssafs', '');";
		$result = mysql_query($sql);
		checkresult($result, "configinsert9", $sql);
		
		$sql="INSERT INTO `" . $DBTABLEPREFIX . "config` VALUES ('ftssafs_session_length', '3600', '');";
		$result = mysql_query($sql);
		checkresult($result, "configinsert10", $sql);
		
		$sql="INSERT INTO `" . $DBTABLEPREFIX . "config` VALUES ('ftssafs_allow_bbcode', '1', '');";
		$result = mysql_query($sql);
		checkresult($result, "configinsert11", $sql);
		
		$sql="INSERT INTO `" . $DBTABLEPREFIX . "config` VALUES ('ftssafs_allow_smilies', '1', '');";
		$result = mysql_query($sql);
		checkresult($result, "configinsert12", $sql);
		
		$sql="INSERT INTO `" . $DBTABLEPREFIX . "config` VALUES ('ftssafs_allow_sig', '1', '');";
		$result = mysql_query($sql);
		checkresult($result, "configinsert13", $sql);
		
		$sql="INSERT INTO `" . $DBTABLEPREFIX . "config` VALUES ('ftssafs_allow_avatar_local', '1', '');";
		$result = mysql_query($sql);
		checkresult($result, "configinsert14", $sql);
		
		$sql="INSERT INTO `" . $DBTABLEPREFIX . "config` VALUES ('ftssafs_allow_avatar_remote', '1', '');";
		$result = mysql_query($sql);
		checkresult($result, "configinsert15", $sql);
		
		$sql="INSERT INTO `" . $DBTABLEPREFIX . "config` VALUES ('ftssafs_allow_avatar_upload', '0', '');";
		$result = mysql_query($sql);
		checkresult($result, "configinsert16", $sql);
		
		$sql="INSERT INTO `" . $DBTABLEPREFIX . "config` VALUES ('ftssafs_posts_per_page', '15', '');";
		$result = mysql_query($sql);
		checkresult($result, "configinsert17", $sql);
		
		$sql="INSERT INTO `" . $DBTABLEPREFIX . "config` VALUES ('ftssafs_topics_per_page', '50', '');";
		$result = mysql_query($sql);
		checkresult($result, "configinsert18", $sql);
		
		$sql="INSERT INTO `" . $DBTABLEPREFIX . "config` VALUES ('ftssafs_max_inbox_privmsgs', '50', '');";
		$result = mysql_query($sql);
		checkresult($result, "configinsert19", $sql);
		
		$sql="INSERT INTO `" . $DBTABLEPREFIX . "config` VALUES ('ftssafs_max_sentbox_privmsgs', '25', '');";
		$result = mysql_query($sql);
		checkresult($result, "configinsert5", $sql);
		
		$sql="INSERT INTO `" . $DBTABLEPREFIX . "config` VALUES ('ftssafs_max_savebox_privmsgs', '50', '');";
		$result = mysql_query($sql);
		checkresult($result, "configinsert20", $sql);
		
		$sql="INSERT INTO `" . $DBTABLEPREFIX . "config` VALUES ('ftssafs_avatar_filesize', '10000', '');";
		$result = mysql_query($sql);
		checkresult($result, "configinsert21", $sql);
		
		$sql="INSERT INTO `" . $DBTABLEPREFIX . "config` VALUES ('ftssafs_privmsg_disable', '0', '');";
		$result = mysql_query($sql);
		checkresult($result, "configinsert22", $sql);
		
		$sql="INSERT INTO `" . $DBTABLEPREFIX . "config` VALUES ('ftssafs_max_sig_chars', '400', '');";
		$result = mysql_query($sql);
		checkresult($result, "configinsert23", $sql);
		
		$sql="INSERT INTO `" . $DBTABLEPREFIX . "config` VALUES ('ftssafs_avatar_max_width', '100', '');";
		$result = mysql_query($sql);
		checkresult($result, "configinsert24", $sql);
		
		$sql="INSERT INTO `" . $DBTABLEPREFIX . "config` VALUES ('ftssafs_avatar_max_height', '100', '');";
		$result = mysql_query($sql);
		checkresult($result, "configinsert25", $sql);
		
		$sql="INSERT INTO `" . $DBTABLEPREFIX . "config` VALUES ('ftssafs_avatar_path', 'images/avatars/', '');";
		$result = mysql_query($sql);
		checkresult($result, "configinsert26", $sql);
		
		$sql="INSERT INTO `" . $DBTABLEPREFIX . "config` VALUES ('ftssafs_avatar_gallery_path', 'images/avatars/gallery', '');";
		$result = mysql_query($sql);
		checkresult($result, "configinsert27", $sql);
		
		$sql="INSERT INTO `" . $DBTABLEPREFIX . "config` VALUES ('ftssafs_smilies_path', 'images/smilies', '');";
		$result = mysql_query($sql);
		checkresult($result, "configinsert28", $sql);
		
		$sql="INSERT INTO `" . $DBTABLEPREFIX . "config` VALUES ('ftssafs_smilies_path', 'images/smilies', '');";
		$result = mysql_query($sql);
		checkresult($result, "configinsert29", $sql);
		
		$sql="INSERT INTO `" . $DBTABLEPREFIX . "config` VALUES ('ftssafs_topic_icons_path', 'images/topic_icons', '');";
		$result = mysql_query($sql);
		checkresult($result, "configinsert30", $sql);
		
		$sql="INSERT INTO `" . $DBTABLEPREFIX . "config` VALUES ('ftssafs_ranks_path', 'images/ranks', '');";
		$result = mysql_query($sql);
		checkresult($result, "configinsert31", $sql);
		
		$sql="INSERT INTO `" . $DBTABLEPREFIX . "config` VALUES ('ftssafs_announcement_text', '', 'This is an example announcement message. Go to the administration panel to change this.');";
		$result = mysql_query($sql);
		checkresult($result, "configinsert32", $sql);
		
		$sql="INSERT INTO `" . $DBTABLEPREFIX . "config` VALUES ('ftssafs_announcement_title', 'Example Message', '');";
		$result = mysql_query($sql);
		checkresult($result, "configinsert33", $sql);
		
		$sql="INSERT INTO `" . $DBTABLEPREFIX . "config` VALUES ('ftssafs_activation_active', '1', '');";
		$result = mysql_query($sql);
		checkresult($result, "configinsert34", $sql);
											
		$sql="INSERT INTO `" . $DBTABLEPREFIX . "categories` VALUES (0, 'New Category', 1);";
		$result = mysql_query($sql);
		checkresult($result, "samplecategory", $sql);
	
		$sql="INSERT INTO `" . $DBTABLEPREFIX . "forums` VALUES (0, 1, 'New Forum', 'This is a new forum, and can be changed using the admin panel.', 1, 1, 0, 1);";
		$result = mysql_query($sql);
		checkresult($result, "sampleforum", $sql);
		
		$sql="INSERT INTO `" . $DBTABLEPREFIX . "profanityfilter` VALUES (1, 'fuck', 'images/smilies/censored.gif');";
		$result = mysql_query($sql);
		checkresult($result, "profanityinsert1", $sql);
		
		$sql="INSERT INTO `" . $DBTABLEPREFIX . "profanityfilter` VALUES (2, 'shit', 'images/smilies/censored.gif');";
		$result = mysql_query($sql);
		checkresult($result, "profanityinsert2", $sql);
		
		$sql="INSERT INTO `" . $DBTABLEPREFIX . "profanityfilter` VALUES (3, 'hell', 'images/smilies/censored.gif');";
		$result = mysql_query($sql);
		checkresult($result, "profanityinsert3", $sql);
		
		$sql="INSERT INTO `" . $DBTABLEPREFIX . "profanityfilter` VALUES (4, 'ass', 'images/smilies/censored.gif');";
		$result = mysql_query($sql);
		checkresult($result, "profanityinsert4", $sql);
		
		$sql="INSERT INTO `" . $DBTABLEPREFIX . "profanityfilter` VALUES (5, 'damn', 'images/smilies/censored.gif');";
		$result = mysql_query($sql);
		checkresult($result, "profanityinsert5", $sql);
		
		$sql="INSERT INTO `" . $DBTABLEPREFIX . "profanityfilter` VALUES (6, 'pussy', 'images/smilies/censored.gif');";
		$result = mysql_query($sql);
		checkresult($result, "profanityinsert6", $sql);
		
		$sql="INSERT INTO `" . $DBTABLEPREFIX . "profanityfilter` VALUES (7, 'cock', 'images/smilies/censored.gif');";
		$result = mysql_query($sql);
		checkresult($result, "profanityinsert7", $sql);
		
		$sql="INSERT INTO `" . $DBTABLEPREFIX . "smilies` VALUES (1, ':)', 'images/smilies/smile.gif');";
		$result = mysql_query($sql);
		checkresult($result, "smiliesinsert1", $sql);
		
		$sql="INSERT INTO `" . $DBTABLEPREFIX . "smilies` VALUES (3, ':arrows:', 'images/smilies/arrows.gif');";
		$result = mysql_query($sql);
		checkresult($result, "smiliesinsert2", $sql);
		
		$sql="INSERT INTO `" . $DBTABLEPREFIX . "smilies` VALUES (4, ':censored:', 'images/smilies/censored.gif');";
		$result = mysql_query($sql);
		checkresult($result, "smiliesinsert3", $sql);
		
		$sql="INSERT INTO `" . $DBTABLEPREFIX . "smilies` VALUES (5, ':bigsmile:', 'images/smilies/bigSmile.gif');";
		$result = mysql_query($sql);
		checkresult($result, "smiliesinsert4", $sql);
		
		$sql="INSERT INTO `" . $DBTABLEPREFIX . "smilies` VALUES (6, ':blush:', 'images/smilies/blush.gif');";
		$result = mysql_query($sql);
		checkresult($result, "smiliesinsert5", $sql);
		
		$sql="INSERT INTO `" . $DBTABLEPREFIX . "smilies` VALUES (7, ':confused:', 'images/smilies/confused.gif');";
		$result = mysql_query($sql);
		checkresult($result, "smiliesinsert6", $sql);
		
		$sql="INSERT INTO `" . $DBTABLEPREFIX . "smilies` VALUES (8, ':cool:', 'images/smilies/cool.gif');";
		$result = mysql_query($sql);
		checkresult($result, "smiliesinsert7", $sql);
		
		$sql="INSERT INTO `" . $DBTABLEPREFIX . "smilies` VALUES (9, ':cry:', 'images/smilies/cry.gif');";
		$result = mysql_query($sql);
		checkresult($result, "smiliesinsert8", $sql);
		
		$sql="INSERT INTO `" . $DBTABLEPREFIX . "smilies` VALUES (10, ':erm:', 'images/smilies/erm.gif');";
		$result = mysql_query($sql);
		checkresult($result, "smiliesinsert9", $sql);
		
		$sql="INSERT INTO `" . $DBTABLEPREFIX . "smilies` VALUES (11, ':exclamation:', 'images/smilies/exclamation.gif');";
		$result = mysql_query($sql);
		checkresult($result, "smiliesinsert10", $sql);
		
		$sql="INSERT INTO `" . $DBTABLEPREFIX . "smilies` VALUES (12, ':jawdrop:', 'images/smilies/jawDrop.gif');";
		$result = mysql_query($sql);
		checkresult($result, "smiliesinsert11", $sql);
		
		$sql="INSERT INTO `" . $DBTABLEPREFIX . "smilies` VALUES (13, ':mad:', 'images/smilies/mad.gif');";
		$result = mysql_query($sql);
		checkresult($result, "smiliesinsert12", $sql);
		
		$sql="INSERT INTO `" . $DBTABLEPREFIX . "smilies` VALUES (14, ':ninjabattle:', 'images/smilies/ninjabattle.gif');";
		$result = mysql_query($sql);
		checkresult($result, "smiliesinsert13", $sql);
		
		$sql="INSERT INTO `" . $DBTABLEPREFIX . "smilies` VALUES (15, ':ninjameditate:', 'images/smilies/ninjameditate.gif');";
		$result = mysql_query($sql);
		checkresult($result, "smiliesinsert14", $sql);
		
		$sql="INSERT INTO `" . $DBTABLEPREFIX . "smilies` VALUES (16, ':peeved:', 'images/smilies/peeved.gif');";
		$result = mysql_query($sql);
		checkresult($result, "smiliesinsert15", $sql);
		
		$sql="INSERT INTO `" . $DBTABLEPREFIX . "smilies` VALUES (17, ':?:', 'images/smilies/question.gif');";
		$result = mysql_query($sql);
		checkresult($result, "smiliesinsert16", $sql);
		
		$sql="INSERT INTO `" . $DBTABLEPREFIX . "smilies` VALUES (18, ':P', 'images/smilies/razz.gif');";
		$result = mysql_query($sql);
		checkresult($result, "smiliesinsert17", $sql);
		
		$sql="INSERT INTO `" . $DBTABLEPREFIX . "smilies` VALUES (19, ':rolleyes:', 'images/smilies/rolleyes.gif');";
		$result = mysql_query($sql);
		checkresult($result, "smiliesinsert18", $sql);
		
		$sql="INSERT INTO `" . $DBTABLEPREFIX . "smilies` VALUES (20, ':suspicious:', 'images/smilies/suspicious.gif');";
		$result = mysql_query($sql);
		checkresult($result, "smiliesinsert19", $sql);
		
		$sql="INSERT INTO `" . $DBTABLEPREFIX . "smilies` VALUES (21, ':tongue:', 'images/smilies/tongue.gif');";
		$result = mysql_query($sql);
		checkresult($result, "smiliesinsert20", $sql);
		
		$sql="INSERT INTO `" . $DBTABLEPREFIX . "topicicons` VALUES (1, 'Exclamation', 'images/topic_icons/excl.png');";
		$result = mysql_query($sql);
		checkresult($result, "topiciconsinsert1", $sql);
		
		$sql="INSERT INTO `" . $DBTABLEPREFIX . "topicicons` VALUES (2, 'Frown', 'images/topic_icons/frown.png');";
		$result = mysql_query($sql);
		checkresult($result, "topiciconsinsert2", $sql);
		
		$sql="INSERT INTO `" . $DBTABLEPREFIX . "topicicons` VALUES (3, 'Heart', 'images/topic_icons/heart.png');";
		$result = mysql_query($sql);
		checkresult($result, "topiciconsinsert3", $sql);
		
		$sql="INSERT INTO `" . $DBTABLEPREFIX . "topicicons` VALUES (4, 'Question Mark', 'images/topic_icons/question.png');";
		$result = mysql_query($sql);
		checkresult($result, "topiciconsinsert4", $sql);
		
		$sql="INSERT INTO `" . $DBTABLEPREFIX . "topicicons` VALUES (5, 'Right Arrows', 'images/topic_icons/rightarrows.png');";
		$result = mysql_query($sql);
		checkresult($result, "topiciconsinsert5", $sql);
		
		$sql="INSERT INTO `" . $DBTABLEPREFIX . "topicicons` VALUES (6, 'Smile', 'images/topic_icons/smile.png');";
		$result = mysql_query($sql);
		checkresult($result, "topiciconsinsert6", $sql);
		
		$sql="INSERT INTO `" . $DBTABLEPREFIX . "topics` VALUES (1, 1, 'Welcome to SAFS!', '', 1151258734, 1, 0, 0, 1, 0, 2);";
		$result = mysql_query($sql);
		checkresult($result, "sampletopic", $sql);		
	
		$sql="INSERT INTO `" . $DBTABLEPREFIX . "posts` VALUES (1, 1, 1, 1151258734, '$postusername', NULL, '[u][b]Welcome to SAFS![/b][/u]\r\nThank you for downloading and installing the Fast Track Sites Simply AJAX Forum SYSTEM or SAFS for short. We are always working to improve SAFS as well as our other programs, so make sure to check [url=http://forum.fasttracksites.com]our forums[/url] for updates.\r\n\r\n[b]The Fast Track Sites Development Team[/b]');";
		$result = mysql_query($sql);
		checkresult($result, "samplepost", $sql);
		
		if ($totalfailure == 0) { 
			echo "\n<br /><h3><font color='green'>Installation Completed successfully!</font></h3><br /><b><u>Please Delete This File Before Continuing</u></b>.<br /><br />You can now view your new forum <a href=\"$menuvar[HOME]\">here</a>."; 
		}
		else { 
			echo "\nInstallation failed, please see the explanations below"; 
			
			foreach ($failed as $table => $status) {
				echo "\nQuery for $table has ";
				if ($status == "failed") {
					echo "<font color='red'>$status</font> $failedsql[$table].<br />";
					$totalfailure = 1;
				}
				else {
					echo "<font color='green'>$status</font>.<br />";				
				}
			}					
		}
	}
	else {
		$boardurl = "http://" . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
		echo "\n<form action='$PHP_SELF' method='POST'>";
		echo "\n		<input type='hidden' name='url' value='$boardurl' />";
		echo "\n	<table cellspacing='1' cellpadding='0' style='border: 1px solid #000; width: 600px;'>";
		echo "\n		<tr>";
		echo "\n			<td class='title1' colspan='2'>";
		echo "\n				Database Configuration";
		echo "\n			</td>";
		echo "\n		</tr>";
		echo "\n		<tr>";
		echo "\n			<td class='row1'>";
		echo "\n				<b>Database Server:</b>";
		echo "\n			</td>";
		echo "\n			<td class='row1'>";
		echo "\n				<input type='text' name='dbserver' />";
		echo "\n			</td>";
		echo "\n		</tr>";
		echo "\n		<tr>";
		echo "\n			<td class='row2'>";
		echo "\n				<b>Database Name:</b>";
		echo "\n			</td>";
		echo "\n			<td class='row2'>";
		echo "\n				<input type='text' name='dbname' />";
		echo "\n			</td>";
		echo "\n		</tr>";
		echo "\n		<tr>";
		echo "\n			<td class='row1'>";
		echo "\n				<b>Database Username:</b>";
		echo "\n			</td>";
		echo "\n			<td class='row1'>";
		echo "\n				<input type='text' name='dbusername' />";
		echo "\n			</td>";
		echo "\n		</tr>";
		echo "\n		<tr>";
		echo "\n			<td class='row2'>";
		echo "\n				<b>Database Password:</b>";
		echo "\n			</td>";
		echo "\n			<td class='row2'>";
		echo "\n				<input type='text' name='dbpassword' />";
		echo "\n			</td>";
		echo "\n		</tr>";
		echo "\n		<tr>";
		echo "\n			<td class='row1'>";
		echo "\n				<b>Table Prefix:</b>";
		echo "\n			</td>";
		echo "\n			<td class='row1'>";
		echo "\n				<input type='text' name='dbtableprefix' value=\"SAFS_\" />";
		echo "\n			</td>";
		echo "\n		</tr>";
		echo "\n		<tr class='title2'>";
		echo "\n			<td colspan='2'>";
		echo "\n				General Configuration";
		echo "\n			</td>";
		echo "\n		</tr>";
		echo "\n		<tr class='row1'>";
		echo "\n			<td>";
		echo "\n				<b>Board Name:</b>";
		echo "\n			</td>";
		echo "\n			<td>";
		echo "\n				<input type='text' name='board_name' size='40' />";
		echo "\n			</td>";
		echo "\n		</tr>";
		echo "\n		<tr class='row2'>";
		echo "\n			<td>";
		echo "\n				<b>Board Description:</b>";
		echo "\n			</td>";
		echo "\n			<td>";
		echo "\n				<textarea name='site_desc' cols='40' rows='10'></textarea>";
		echo "\n			</td>";
		echo "\n		</tr>";
		echo "\n		<tr class='row1'>";
		echo "\n			<td>";
		echo "\n				<b>Your Username:</b>";
		echo "\n			</td>";
		echo "\n			<td>";
		echo "\n				<input type='text' name='username' size='40' />";
		echo "\n			</td>";
		echo "\n		</tr>";
		echo "\n		<tr class='row2'>";
		echo "\n			<td>";
		echo "\n				<b>Your Password:</b>";
		echo "\n			</td>";
		echo "\n			<td>";
		echo "\n				<input type='text' name='password' size='40' />";
		echo "\n			</td>";
		echo "\n		</tr>";
		echo "\n		<tr class='row1'>";
		echo "\n			<td>";
		echo "\n				<b>Your Email:</b>";
		echo "\n			</td>";
		echo "\n			<td>";
		echo "\n				<input type='text' name='board_email' size='40' />";
		echo "\n			</td>";
		echo "\n		</tr>";
		echo "\n		<tr class='title2'>";
		echo "\n			<td colspan='2'>";
		echo "\n				<center><input type='submit' name='submit' class='button' value='Submit' /></center>";			
		echo "\n			</td>";
		echo "\n		</tr>";
		echo "\n	</table>";
		echo "\n</form>";
		echo "\n</center>";
	}
?>
			</div>			
				<div id="footer">				
					<div id="footer-leftcol" class="FForumBorder">
					Copyright &copy; 2007 Fast Track Sites
					</div>
					<div id="footer-rightcol">
						Powered By: <a href="http://www.fasttracksites.com">Fast Track Sites Simply AJAX Forum System</a>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>

