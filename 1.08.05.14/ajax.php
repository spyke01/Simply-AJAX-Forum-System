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
 *
 * This program is licensed under the Fast Track Sites Program license 
 * located inside the license.txt file included with this program. This is a 
 * legally binding license, and is protected by all applicable laws, by 
 * editing this page you fall subject to these licensing terms.
 *
 ***************************************************************************/
if ($action != "editpost" && $action != "updatepost" && $action != "deletetopic") { include 'includes/header.php'; }
	
//get variables from posted form
$userid = $_SESSION['userid'];
$user = $_SESSION['username'];
$action = $_REQUEST['action'];
$forumid = $_REQUEST['forumid'];
$topicid = $_REQUEST['topicid'];
$postid = $_REQUEST['postid'];
$subject = $_REQUEST['subject'];
$post_options = $_REQUEST['post_options'];
$text = trim($_REQUEST['message']);
$topicicon = trim($_REQUEST['topic_icon']);
$submit = $_REQUEST['submit'];
$movetoforum = $_REQUEST['movetoforum'];
$value = $_GET['value'];

//Make safe in case user tried hacking board
$action = parseurl($action);
$forumid = parseurl($forumid);
$topicid = parseurl($topicid);
$postid = parseurl($postid);
$subject = keeptasafe($subject);
$post_options = keeptasafe($post_options);
$text = keeptasafe($text);
$topicicon = keeptasafe($topicicon);
$submit = parseurl($submit);
$movetoforum = parseurl($movetoforum);
$value = parseurl($value);

$current_time = time();
$posterid = "";
$post_options = ($_SESSION['user_level'] != USER && $_SESSION['user_level'] != BANNED) ? $post_options : POST_NORMAL; //only let admins and mods make stickies and announcements
$sign = ($action == "deletepost" || $action == "deletetopic") ? "- 1" : "+ 1";

if (isset($postid) && $postid != '') {
	$table = "posts";
	$sql = "SELECT * FROM `$table` WHERE post_id = $postid"; //gets categories
	$result = mysql_query($sql);

	if($result && mysql_num_rows($result) > 0)
	{
		$row = mysql_fetch_array($result);
		$posterid = $row['post_poster_id'];

	}
}

// Checks to see if a username is already in use
if ($action == 'checkusername') {	
	$sql_username_check = mysql_query("SELECT users_username FROM `" . $DBTABLEPREFIX . "users` WHERE users_username='$value'");
	
	if (mysql_num_rows($sql_username_check) > 0) {
		echo "<a style=\"cursor: pointer; cursor: hand; color: red;\" onclick=\"new Ajax.Updater('usernameCheckerHolder', 'ajax.php?action=checkusername&value=' + document.newUserForm.username.value, {asynchronous:true});\">[Already In Use]</a>";
	}
	else {
		echo "<a style=\"cursor: pointer; cursor: hand; color: green;\" onclick=\"new Ajax.Updater('usernameCheckerHolder', 'ajax.php?action=checkusername&value=' + document.newUserForm.username.value, {asynchronous:true});\">[Good]</a>";
	}
}
// Checks to see if an email address is already in use
elseif ($action == 'checkemailaddress') {	
	$sql_username_check = mysql_query("SELECT users_email_address FROM `" . $DBTABLEPREFIX . "users` WHERE users_email_address='$value'");
	
	if (mysql_num_rows($sql_username_check) > 0) {
		echo "<a style=\"cursor: pointer; cursor: hand; color: red;\" onclick=\"new Ajax.Updater('emailaddressCheckerHolder', 'ajax.php?action=checkemailaddress&value=' + document.newUserForm.email_address.value, {asynchronous:true});\">[Already In Use]</a>";
	}
	else {
		echo "<a style=\"cursor: pointer; cursor: hand; color: green;\" onclick=\"new Ajax.Updater('emailaddressCheckerHolder', 'ajax.php?action=checkemailaddress&value=' + document.newUserForm.email_address.value, {asynchronous:true});\">[Good]</a>";
	}
}
//reply to topic function
elseif ($action == 'reply' && $_SESSION[userid]) {	
	
	$sql = "INSERT INTO `" . $DBTABLEPREFIX . "posts` (post_topic_id, post_poster_id, post_time, post_username, post_text)".
  	    "VALUES('$topicid', '$userid', '$current_time', '$user', '$text')";
	mysql_query($sql) or die('Error, insert query failed' . $sql);
	
	$post_id = mysql_insert_id();

	$sql = "SELECT * FROM `" . $DBTABLEPREFIX . "topics` WHERE topic_id = '$topicid'"; //gets categories
	$result = mysql_query($sql);
	if($result && mysql_num_rows($result) > 0)
	{
		$row = mysql_fetch_array($result);
		$forumid = $row['topic_forum_id'];
	}
	
	//UPDATE NUMBER OF REPLIES TO TOPIC
	$sql = "UPDATE `" . $DBTABLEPREFIX . "topics` SET topic_replies = topic_replies $sign WHERE topic_id='$topicid'";
	mysql_query($sql) or die('Error, update query failed' . $sql);

	//UPDATE NUMBER OF USERS POSTS
	$sql = "UPDATE `" . $DBTABLEPREFIX . "users` SET users_posts = users_posts $sign WHERE users_id='$userid'";
	mysql_query($sql) or die('Error, update query failed' . $sql);
    
	//UPDATE NUMBER OF POSTS IN FORUM
	$sql = "UPDATE `" . $DBTABLEPREFIX . "forums` SET forum_posts=forum_posts $sign WHERE forum_id='$forumid'";
	mysql_query($sql) or die('Error, update query failed' . $sql);

	//DELETE ALL ITEMS IN READ_POSTS FOR THIS TOPIC
	$sql = "DELETE FROM `" . $DBTABLEPREFIX . "posts_read` WHERE pr_topic_id='$topicid'";
	@mysql_query($sql) or die('Error, update query failed' . $sql);
    
	// Rebuild our list of topics on the page
    buildPosts("echo", $topicid, $post_id);
}

//newtopic function
elseif ($action == 'newtopic' && $_SESSION[userid]) {

    $sql = "INSERT INTO `" . $DBTABLEPREFIX . "topics` (topic_forum_id, topic_title, topic_time, topic_poster, topic_type, topic_icon)".
          "VALUES('$forumid', '$subject', '$current_time', '$userid', '$post_options', '$topicicon')";
    mysql_query($sql) or die('Error, insert query failed' . $sql);
    
    $topic_id = mysql_insert_id();
    
    $sql = "INSERT INTO `" . $DBTABLEPREFIX . "posts` (post_topic_id, post_poster_id, post_time, post_username, post_text)".
          "VALUES('$topic_id', '$userid', '$current_time', '$user', '$text')";
    mysql_query($sql) or die('Error, insert query failed' . $sql);    
    
    $post_id = mysql_insert_id();
    
    //UPDATE NUMBER OF TOPICS IN FORUM
    $sql = "UPDATE `" . $DBTABLEPREFIX . "forums` SET forum_topics=forum_topics $sign WHERE forum_id='$forumid'";
    mysql_query($sql) or die('Error, update query failed' . $sql);
    
    //UPDATE NUMBER OF POSTS IN FORUM
    $sql = "UPDATE `" . $DBTABLEPREFIX . "forums` SET forum_posts=forum_posts $sign WHERE forum_id='$forumid'";
    mysql_query($sql) or die('Error, update query failed' . $sql);
    
    //SETS FIRSTPOSTID VALUE THATS USED IN THE DELETEPOST FUNCTION    
    $sql = "UPDATE `" . $DBTABLEPREFIX . "topics` SET topic_first_post_id='$post_id' WHERE topic_id='$topic_id'";
    mysql_query($sql) or die('Error, update query failed' . $sql);
	
	//UPDATE NUMBER OF USERS POSTS
    $sql = "UPDATE `" . $DBTABLEPREFIX . "users` SET users_posts = users_posts $sign WHERE users_id='$userid'";
    mysql_query($sql) or die('Error, update query failed' . $sql);
           
    // Rebuild our list of topics on the page
    buildTopics("echo", $forumid, $topic_id);
}

//updatepost function
elseif ($action == 'updatepost' && ($_SESSION[user_level] != USER || $_SESSION[userid] == $posterid)) {
	
	$sql = "UPDATE `" . $DBTABLEPREFIX . "posts` SET post_text='$text' WHERE post_id='$postid'";
	mysql_query($sql) or die('Error, updatepost query failed');
	
	if (isset($_POST[subject])) {
		$sql = "UPDATE `" . $DBTABLEPREFIX . "topics` SET topic_title='$subject' WHERE topic_id='$topicid'";
		mysql_query($sql) or die('Error, updatepost query failed');
	}
	if ($_SESSION[user_level] != USER && $_SESSION['user_level'] != BANNED && isset($_POST[post_options])) {
		$sql = "UPDATE `" . $DBTABLEPREFIX . "topics` SET topic_type='$post_options' WHERE topic_id='$topicid'";
		mysql_query($sql) or die('Error, updatepost query failed');
	}
	if ($_SESSION['user_level'] != BANNED && isset($_POST[post_options])) {
		$sql = "UPDATE `" . $DBTABLEPREFIX . "topics` SET topic_icon='$topicicon' WHERE topic_id='$topicid'";
		mysql_query($sql) or die('Error, updatepost query failed');
	}
	//confirm
 	$page_content .= "Your post has been edited, and you are being redirected to the main topic. 
 			<meta http-equiv='refresh' content='1;url=index.php?p=viewtopic&id=$topicid#$postid'>";
	
	$page->setTemplateVar("PageContent", $page_content);
}

//deletepost function
elseif ($action == 'deletepost' && (($_SESSION[user_level] != USER && $_SESSION['user_level'] != BANNED) || $_SESSION[userid] == $posterid)) {
	
	$sql = "SELECT post_topic_id FROM `" . $DBTABLEPREFIX . "posts` WHERE post_topic_id = '$topicid'";
	$result = mysql_query($sql);

	if($result && mysql_num_rows($result) > 0) {
		$numberofposts = mysql_num_rows($result); //so we know how much to decrease total posts by
	}
	mysql_free_result($result);
	
	$sql = "SELECT * FROM `" . $DBTABLEPREFIX . "topics` WHERE topic_id = '$topicid'";
	$result = mysql_query($sql);

	if($result && mysql_num_rows($result) > 0) {
		$row = mysql_fetch_array($result);
		$firstpost = $row['topic_first_post_id'];
		$forumid = $row['topic_forum_id'];		
	}
	mysql_free_result($result);
	
	// for debugging purposes echo "<br />" . $firstpost . "<br />" . $postid . "<br />" . $topicid;
	
	/*Delete Post with $postid */
	$sql = "DELETE FROM `" . $DBTABLEPREFIX . "posts` WHERE post_id = '$postid' ";
	mysql_query($sql) or die('Error, delete query 1 failed');
		
	if ($firstpost == $postid) {
		
		/*Delete all Posts inside topic were deleting */
		$sql = "DELETE FROM `" . $DBTABLEPREFIX . "posts` WHERE post_topic_id = '$topicid' ";
		mysql_query($sql) or die('Error, delete query 2 failed');
		
		/*Delete topic itself */
		$sql = "DELETE FROM `" . $DBTABLEPREFIX . "topics` WHERE topic_id = '$topicid' ";
		mysql_query($sql) or die('Error, delete query 3 failed');
		
		echo "Your topic has been deleted, and you are being redirected to the main topic.";
				 
		//UPDATE NUMBER OF TOPICS IN FORUM
	    $sql = "UPDATE `" . $DBTABLEPREFIX . "forums` SET forum_topics=forum_topics $sign WHERE forum_id='$forumid'";
	    mysql_query($sql) or die('Error, update query failed' . $sql);

		//UPDATE NUMBER OF POSTS IN FORUM
		$sql = "UPDATE `" . $DBTABLEPREFIX . "forums` SET forum_posts=forum_posts - $numberofposts WHERE forum_id='$forumid'";
		mysql_query($sql) or die('Error, update query failed' . $sql);
	}
	else {
		//UPDATE NUMBER OF POSTS IN FORUM
		$sql = "UPDATE `" . $DBTABLEPREFIX . "forums` SET forum_posts=forum_posts $sign WHERE forum_id='$forumid'";
		mysql_query($sql) or die('Error, update query failed' . $sql);
	}
	    	    
   	//UPDATE NUMBER OF USERS POSTS
    $sql = "UPDATE `" . $DBTABLEPREFIX . "users` SET users_posts = users_posts $sign WHERE users_id='$userid'";
    mysql_query($sql) or die('Error, update query failed' . $sql);
    
	//UPDATE NUMBER OF REPLIES TO TOPIC
	$sql = "UPDATE `" . $DBTABLEPREFIX . "topics` SET topic_replies = topic_replies $sign WHERE topic_id='$topicid'";
	mysql_query($sql) or die('Error, update query failed' . $sql);
 	
}

//editpost function
elseif ($action == 'editpost' && (($_SESSION[user_level] != USER && $_SESSION['user_level'] != BANNED) || $_SESSION[userid] == $posterid)) {

	// for debugging purposes echo "<br />" . $postid . "<br />" . $topicid;  
	
	$sql = "SELECT * FROM `" . $DBTABLEPREFIX . "posts` p, `" . $DBTABLEPREFIX . "topics` t WHERE p.post_id = '$postid' AND t.topic_id = p.post_topic_id LIMIT 1"; //gets categories
	$result = mysql_query($sql);
	
	if($result && mysql_num_rows($result) > 0) {
		$row = mysql_fetch_array($result);
		
		$page_content .= "<center>
							<form name=\"post\" action=\"index.php?p=post&action=updatepost&topicid=$topicid&postid=$postid\" method=\"post\">";
		if ($row[topic_first_post_id] == $row[post_id]) {
			$page_content .= "\n		<div class=\"VForumForumBorder\">
											<div class=\"title1\">$T_New_Topic</div>
											<div class=\"row1\">
												<table border=\"0\" cellpadding=\"0\" cellspacing=\"1\">
													<tr>
														<td><b>Subject:  </b></td><td><input type=\"text\" name=\"subject\" value=\"$row[topic_title]\" size=\"73\"></td>
													</tr>
												</table>	
											</div>
										</div>
										<br />";	
				
			if ($_SESSION['user_level'] != USER && $_SESSION['user_level'] != BANNED) {
				$page_content .= "\n		<div class=\"VForumForumBorder\">
												<div class=\"title1\">Post Options</div>
												<div class=\"row1\">
													<input type=\"radio\" name=\"post_options\" value=\"" . POST_NORMAL . "\"" . testChecked($row[topic_type], POST_NORMAL) . " /> <b>Normal</b> &nbsp;&nbsp;
													<input type=\"radio\" name=\"post_options\" value=\"" . POST_STICKY . "\"" . testChecked($row[topic_type] == POST_STICKY) . " /><b>Sticky</b> &nbsp;&nbsp;
													<input type=\"radio\" name=\"post_options\" value=\"" . POST_ANNOUNCE . "\"" . testChecked($row[topic_type] == POST_ANNOUNCE) . " /><b>Announce</b> &nbsp;&nbsp;
													<input type=\"radio\" name=\"post_options\" value=\"" . POST_GLOBAL_ANNOUNCE . "\"" . testChecked($row[topic_type] == POST_GLOBAL_ANNOUNCE) . " /><b>Global Announce</b> &nbsp;&nbsp;
											</div>
											</div>
											<br />";
			}	
		}					
		$page_content .= "\n	<table class=\"VForumForumBorder\" border=\"0\" cellpadding=\"0\" cellspacing=\"1\">
									<tr class=\"title1\">
										<td class=\"VForumT2\" colspan=\"2\">Edit Post</td>	
									</tr>";
		bbcode_box(); //print out the bbcode buttons
		$page_content .= "\n			<center><textarea cols=\"70\" rows=\"8\" name=\"message\" class=\"textinput\" wrap=\"virtual\" onselect=\"storeCaret(this);\" onclick=\"storeCaret(this);\" onkeyup=\"storeCaret(this);\">$row[post_text]</textarea><br /></center></td>
									</tr>
								</table>";		
		if ($row[topic_first_post_id] == $row[post_id]) {
			$page_content .= "\n		<br />				
										<div class=\"VForumForumBorder\">
											<div class=\"title1\">Topic Icons</div>
											<div class=\"row1\">
												<input type=\"radio\" name=\"topic_icons\" value=\"none\" "; 
			
			if ($row[topic_icon] == "none") { $page_content .= "checked "; } 
			$page_content .= "/> <b>None</b> &nbsp;&nbsp;";
			
			$sql2 = "SELECT * FROM `" . $DBTABLEPREFIX . "topicicons` ORDER BY topicicons_id";
			$result2 = mysql_query($sql2);
			
			while ($row2 = mysql_fetch_array($result2)) {
				$page_content .= "\n				<input type=\"radio\" name=\"topic_icon\" value=\"$row2[topicicons_id]\"" . testChecked($row[topic_icon], $row2[topicicons_id]) . " /> <img src='$row2[topicicons_image]' style='width: 20px; height: 20px;' alt='$row2[topicicons_name]' /> &nbsp;&nbsp; ";
			}
			
			$page_content .= "\n			</div>			
										</div>";		
		}
		$page_content .= "\n		<center><input type=\"submit\" name=\"submit\" value=\"Submit\" class=\"button\"></center>
								</form>
								<br /><br />
							</div>";
	}
	mysql_free_result($result);
	
	$page->setTemplateVar("PageContent", $page_content);
}

//deletetopic function
elseif ($action == 'deletetopic' && $_SESSION[user_level] != USER && $_SESSION['user_level'] != BANNED) {

	$sql = "SELECT * FROM `" . $DBTABLEPREFIX . "posts` WHERE post_topic_id = '$topicid'";
	$result = mysql_query($sql);

	if($result && mysql_num_rows($result) > 0) {
		$numberofposts = mysql_num_rows($result); //so we know how much to decrease total posts by		
	}
	else { $numberofposts = 0; }
	
	$sql = "SELECT * FROM `" . $DBTABLEPREFIX . "topics` WHERE topic_id = '$topicid'";
	$result = mysql_query($sql);

	if($result && mysql_num_rows($result) > 0) {
		$row = mysql_fetch_array($result);
		$forumid = $row['topic_forum_id']; //so we know how much to decrease total posts by
	}
	
	// for debugging purposes echo "<br />" . $firstpost . "<br />" . $postid . "<br />" . $topicid;
	
		
	/*Delete all Posts inside topic were deleting */
	$sql = "DELETE FROM `" . $DBTABLEPREFIX . "posts` WHERE post_topic_id = '$topicid' ";
	mysql_query($sql) or die('Error, delete query 2 failed');
		
	/*Delete topic itself */
	$sql = "DELETE FROM `" . $DBTABLEPREFIX . "topics` WHERE topic_id = '$topicid' ";
	mysql_query($sql) or die('Error, delete query 3 failed');
		
	//UPDATE NUMBER OF TOPICS IN FORUM
    $sql = "UPDATE `" . $DBTABLEPREFIX . "forums` SET forum_topics=forum_topics $sign WHERE forum_id='$forumid'";
    mysql_query($sql) or die('Error, update query failed' . $sql);
	
	//UPDATE NUMBER OF POSTS IN FORUM
	$sql = "UPDATE `" . $DBTABLEPREFIX . "forums` SET forum_posts=forum_posts - $numberofposts WHERE forum_id='$forumid'";
	mysql_query($sql) or die('Error, update query failed' . $sql);
	    	    
	$page_content .= "Your topic has been deleted, and you are being redirected to the parent forum.
						<meta http-equiv='refresh' content='1;url=index.php?p=viewforum&id=" . $forumid . "'>";
	$page->setTemplateVar("PageContent", $page_content); 	
}

//movetopic function
elseif ($action == 'movetopic' && $_SESSION[user_level] != USER && $_SESSION['user_level'] != BANNED) {
	/*Make Shadow topic*/
	$sql = "INSERT INTO `" . $DBTABLEPREFIX . "topics`(topic_forum_id, topic_title, topic_icon, topic_time, topic_poster, topic_views, topic_replies, topic_type, topic_first_post_id)".
    	   "SELECT topic_forum_id, topic_title, topic_icon, topic_time, topic_poster, topic_views, topic_replies, topic_type, topic_first_post_id FROM `" . $DBTABLEPREFIX . "topics` WHERE topic_id=$topicid";
	mysql_query($sql) or die('Error, shadowed topic insert query failed');
	
	$topic_id = mysql_insert_id();	
	
	/*Add *MOVED* to front of topic_title for OLD Topic*/
	$sql = "SELECT * FROM `" . $DBTABLEPREFIX . "topics` WHERE topic_id = '$topicid'";
	$result = mysql_query($sql);

	if($result && mysql_num_rows($result) > 0) {
		$row = mysql_fetch_array($result);
		$topictitle = $row['topic_title']; //so we know how much to decrease total posts by
	}
	
	$sql = "UPDATE `" . $DBTABLEPREFIX . "topics` SET topic_title='*MOVED* $topictitle' WHERE topic_id = '$topicid' ";
	mysql_query($sql) or die('Error, adding MOVED query failed');	
	
	/*Lock Old Topic*/
	$sql = "UPDATE `" . $DBTABLEPREFIX . "topics` SET topic_type='" . TOPIC_LOCKED . "' WHERE topic_id = '$topicid' ";
	mysql_query($sql) or die('Error, lock topic failed');	
	
	//Change All Posts for OLD topic so that they now belong to the NEW topic
	$sql = "UPDATE `" . $DBTABLEPREFIX . "posts` SET post_topic_id='$topic_id' WHERE post_topic_id = '$topicid' ";
	mysql_query($sql) or die('Error, changing posts.topic_id query failed');
	
	//Create a topic with link to the new topic
	$sql = "INSERT INTO `" . $DBTABLEPREFIX . "posts`(post_topic_id, post_poster_id, post_time, post_username, post_subject, post_text)".
    	   "VALUES('$topicid', '" . $_SESSION['userid'] . "', '$current_time', '" . $_SESSION['username'] . "', '*TOPIC MOVED*', 'Your topic has been moved to [URL=viewtopic.php?id=$topic_id]here[/URL]')";
	mysql_query($sql) or die('Error, shadowed topic insert query failed');	
	
	/*Move NEW topic*/
	$sql = "UPDATE `" . $DBTABLEPREFIX . "topics` SET topic_forum_id='$movetoforum' WHERE topic_id = '$topic_id' ";
	mysql_query($sql) or die('Error, move topic query failed');	

	//===========================================
	// This large chunk of stuff lets us
	// update the number of posts and topics
	// so that the forums are always up to date
	//===========================================
	$sql = "SELECT * FROM `" . $DBTABLEPREFIX . "posts` p, `" . $DBTABLEPREFIX . "topics` t WHERE (t.topic_id = '$topic_id' AND p.post_topic_id = '$topic_id')";
	$result = mysql_query($sql);

	if($result && mysql_num_rows($result) > 0) {
		$numberofposts = mysql_num_rows($result); //so we know how much to decrease total posts by
		
		$row = mysql_fetch_array($result);
		$forumid = $row['topic_forum_id'];
	}

	
	// UPDATE NUMBER OF TOPICS IN THE FORUM WE MOVED THE TOPIC TO
    $sql = "UPDATE `" . $DBTABLEPREFIX . "forums` SET forum_topics=forum_topics $sign WHERE forum_id='$forumid'";
    mysql_query($sql) or die('Error, update query failed' . $sql);

	// UPDATE NUMBER OF POSTS IN THE FORUM WE MOVED THE TOPIC TO
	$sql = "UPDATE `" . $DBTABLEPREFIX . "forums` SET forum_posts=forum_posts + $numberofposts WHERE forum_id='$forumid'";
	mysql_query($sql) or die('Error, update query failed' . $sql);
	    	    
	echo "Your topic has been moved to forum $movetoforum, and you are being redirected to the main topic.";
	echo "<meta http-equiv='refresh' content='1;url=viewtopic.php?id=" . $topicid . "'>";
 	
}

// locktopic function
elseif ($action == 'locktopic' && $_SESSION[user_level] != USER && $_SESSION['user_level'] != BANNED) {
	/*Lock topic*/
	$sql = "UPDATE `" . $DBTABLEPREFIX . "topics` SET topic_status='" . TOPIC_LOCKED . "' WHERE topic_id = '$topicid' ";
	$result = mysql_query($sql);
	
	if (!$result) { echo "Error, locktopic function failed"; }
	else { echo "Your topic has been locked."; } 	
}

// unlocktopic function
elseif ($action == 'unlocktopic' && $_SESSION[user_level] != USER && $_SESSION['user_level'] != BANNED) {
	/*Unlock topic*/
	$sql = "UPDATE `" . $DBTABLEPREFIX . "topics` SET topic_status='" . TOPIC_UNLOCKED . "' WHERE topic_id = '$topicid' ";
	$result = mysql_query($sql);
	
	if (!$result) { echo "Error, locktopic function failed"; }
	else { echo "Your topic has been unlocked."; }
}

// deletefilter function
elseif ($action == 'deletefilter' && $_SESSION[user_level] != USER && $_SESSION['user_level'] != BANNED) {
	$sql = "DELETE FROM `" . $DBTABLEPREFIX . "profanityfilter` WHERE profanityfilter_id = '" . parseurl($id) . "'";
	$result = mysql_query($sql);
}

// deleteranks function
elseif ($action == 'deleterank' && $_SESSION[user_level] != USER && $_SESSION['user_level'] != BANNED) {
	$sql = "DELETE FROM `" . $DBTABLEPREFIX . "ranks` WHERE rank_id = '" . parseurl($id) . "'";
	$result = mysql_query($sql);
}

// deletesmilies function
elseif ($action == 'deletesmiley' && $_SESSION[user_level] != USER && $_SESSION['user_level'] != BANNED) {
	$sql = "DELETE FROM `" . $DBTABLEPREFIX . "smilies` WHERE smilies_id = '" . parseurl($id) . "'";
	$result = mysql_query($sql);
}

// deletetopicicons function
elseif ($action == 'deletetopicicon' && $_SESSION[user_level] != USER && $_SESSION['user_level'] != BANNED) {
	$sql = "DELETE FROM `" . $DBTABLEPREFIX . "topicicons` WHERE topicicons_id = '" . parseurl($id) . "'";
	$result = mysql_query($sql);
}

// deletecat function
elseif ($action == 'deletecat' && $_SESSION[user_level] != USER && $_SESSION['user_level'] != BANNED) {
	$sql = "DELETE FROM `" . $DBTABLEPREFIX . "categories` WHERE cat_id = '" . parseurl($id) . "'";
	$result = mysql_query($sql);
		
	$sql = "SELECT forum_id FROM `" . $DBTABLEPREFIX . "forums` WHERE forum_cat_id = '" . parseurl($id) . "'";
	$result = mysql_query($sql);
	
	while ($row = mysql_fetch_array($result)) {	
		$sql2 = "DELETE FROM `" . $DBTABLEPREFIX . "forums` WHERE forum_id = '" . $row['forum_id'] . "'";
		$result2 = mysql_query($sql2);
	
		$sql2 = "SELECT topic_id FROM `" . $DBTABLEPREFIX . "topics` WHERE topic_forum_id = '" . $row['forum_id'] . "'";
		$result2 = mysql_query($sql2);
	
		while ($row2 = mysql_fetch_array($result2)) {
			$sql3 = "DELETE FROM `" . $DBTABLEPREFIX . "topics` WHERE topic_id = '" . $row2['topic_id'] . "'";
			$result3 = mysql_query($sql3);
			
			$sql3 = "DELETE FROM `" . $DBTABLEPREFIX . "posts` WHERE post_topic_id = '" . $row2['topic_id'] . "'";
			$result3 = mysql_query($sql3);
		}
		mysql_free_result($result2);	
	}
	mysql_free_result($result);
}

// deleteforum function
elseif ($action == 'deleteforum' && $_SESSION[user_level] != USER && $_SESSION['user_level'] != BANNED) {
	$sql = "DELETE FROM `" . $DBTABLEPREFIX . "forums` WHERE forum_id = '" . parseurl($id) . "'";
	$result = mysql_query($sql);
	
	$sql = "SELECT topic_id FROM `" . $DBTABLEPREFIX . "topics` WHERE topic_forum_id = '" . parseurl($id) . "'";
	$result = mysql_query($sql);
	
	while ($row = mysql_fetch_array($result)) {
		$sql2 = "DELETE FROM `" . $DBTABLEPREFIX . "topics` WHERE topic_id = '" . $row['topic_id'] . "'";
		$result2 = mysql_query($sql2);
		
		$sql2 = "DELETE FROM `" . $DBTABLEPREFIX . "posts` WHERE post_topic_id = '" . $row['topic_id'] . "'";
		$result2 = mysql_query($sql2);
	}
	
	mysql_free_result($result);
}

?>
