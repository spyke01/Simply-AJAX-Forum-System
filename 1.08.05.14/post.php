<? 
/***************************************************************************
 *                               post.php
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
include 'includes/header.php';

//get variables from posted form
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

$current_time = time();
$posterid;
$post_options = ($_SESSION['user_level'] != USER && $_SESSION['user_level'] != BANNED) ? $post_options : POST_NORMAL; //only let admins and mods make stickies and announcements
$sign = ($action == 'deletepost' || $action == 'deletetopic') ? '- 1' : '+ 1';

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


//reply to topic function
if ($action == 'reply' && $_SESSION[userid]) {

	keeptasafe($text);
	
	$userid = $_SESSION['userid'];
	$user = $_SESSION['username'];
	
	$sql = "INSERT INTO `posts` (post_topic_id, post_poster_id, post_time, post_username, post_text)".
  	    "VALUES('$topicid', '$userid', '$current_time', '$user', '$text')";
	mysql_query($sql) or die('Error, insert query failed' . $sql);
	
	$post_id = mysql_insert_id();

	$sql = "SELECT * FROM `topics` WHERE topic_id = '$topicid'"; //gets categories
	$result = mysql_query($sql);
	if($result && mysql_num_rows($result) > 0)
	{
		$row = mysql_fetch_array($result);
		$forumid = $row['topic_forum_id'];
	}
	
	//UPDATE NUMBER OF REPLIES TO TOPIC
	$sql = "UPDATE `topics` SET topic_replies = topic_replies $sign WHERE topic_id='$topicid'";
	mysql_query($sql) or die('Error, update query failed' . $sql);

	//UPDATE NUMBER OF USERS POSTS
	$sql = "UPDATE `users` SET users_posts = users_posts $sign WHERE users_userid='$userid'";
	mysql_query($sql) or die('Error, update query failed' . $sql);
    
	//UPDATE NUMBER OF POSTS IN FORUM
	$sql = "UPDATE `forums` SET forum_posts=forum_posts $sign WHERE forum_id='$forumid'";
	mysql_query($sql) or die('Error, update query failed' . $sql);

	//DELETE ALL ITEMS IN READ_POSTS FOR THIS TOPIC
	$sql = "DELETE FROM `posts_read` WHERE pr_topic_id='$topicid'";
	@mysql_query($sql) or die('Error, update query failed' . $sql);
    
	//confirm
	echo "Your reply has been posted, and you are being redirected to the main topic."; 
	echo "<meta http-equiv='refresh' content='1;url=viewtopic.php?id=$topicid#$post_id'>";
}

//newtopic function
if ($action == 'newtopic' && $_SESSION[userid]) {

    keeptasafe($subject);
    keeptasafe($text);
    $userid = $_SESSION['userid'];
    $username = $_SESSION['username'];
    $DONT_KNOW_HOW = '0';

    $sql = "INSERT INTO `topics` (topic_forum_id, topic_title, topic_time, topic_poster, topic_type, topic_icon)".
          "VALUES('$forumid', '$subject', '$current_time', '$userid', '$post_options', '$topicicon')";
    mysql_query($sql) or die('Error, insert query failed' . $sql);
    
    $topic_id = mysql_insert_id();
    
    $sql = "INSERT INTO `posts` (post_topic_id, post_poster_id, post_time, post_username, post_text)".
          "VALUES('$topic_id', '$userid', '$current_time', '$username', '$text')";
    mysql_query($sql) or die('Error, insert query failed' . $sql);
    
    
    $post_id = mysql_insert_id();
    
    //UPDATE NUMBER OF TOPICS IN FORUM
    $sql = "UPDATE `forums` SET forum_topics=forum_topics $sign WHERE forum_id='$forumid'";
    mysql_query($sql) or die('Error, update query failed' . $sql);
    
    //UPDATE NUMBER OF POSTS IN FORUM
    $sql = "UPDATE `forums` SET forum_posts=forum_posts $sign WHERE forum_id='$forumid'";
    mysql_query($sql) or die('Error, update query failed' . $sql);
    
    //SETS FIRSTPOSTID VALUE THATS USED IN THE DELETEPOST FUNCTION    
    $sql = "UPDATE `topics` SET topic_first_post_id='$post_id' WHERE topic_id='$topic_id'";
    mysql_query($sql) or die('Error, update query failed' . $sql);
	
	//UPDATE NUMBER OF USERS POSTS
    $sql = "UPDATE `users` SET users_posts = users_posts $sign WHERE users_userid='$userid'";
    mysql_query($sql) or die('Error, update query failed' . $sql);
       
    
    //confirm
    echo "Your topic has been posted, and you are being redirected to it.";
    echo "<meta http-equiv='refresh' content='1;url=viewtopic.php?id=" . $topic_id . "'>";
}

//updatepost function
if ($action == 'updatepost' && ($_SESSION[user_level] != USER || $_SESSION[userid] == $posterid)) {

	keeptasafe($text);
	
	$sql = "UPDATE `posts` SET post_text='$text' WHERE post_id='$postid'";
	mysql_query($sql) or die('Error, updatepost query failed');
	
	if (isset($_POST[subject])) {
		$sql = "UPDATE `topics` SET topic_title='$subject' WHERE topic_id='$topicid'";
		mysql_query($sql) or die('Error, updatepost query failed');
	}
	if ($_SESSION[user_level] != USER && $_SESSION['user_level'] != BANNED && isset($_POST[post_options])) {
		$sql = "UPDATE `topics` SET topic_type='$post_options' WHERE topic_id='$topicid'";
		mysql_query($sql) or die('Error, updatepost query failed');
	}
	if ($_SESSION['user_level'] != BANNED && isset($_POST[post_options])) {
		$sql = "UPDATE `topics` SET topic_icon='$topicicon' WHERE topic_id='$topicid'";
		mysql_query($sql) or die('Error, updatepost query failed');
	}
	//confirm
 	echo "Your post has been edited, and you are being redirected to the main topic."; 
 	echo "<meta http-equiv='refresh' content='1;url=viewtopic.php?id=$topicid#$postid'>";

}

//deletepost function
if ($action == 'deletepost' && (($_SESSION[user_level] != USER && $_SESSION['user_level'] != BANNED) || $_SESSION[userid] == $posterid)) {

	$userid = $_SESSION['userid'];
	$firstpost;
	
	$sql = "SELECT * FROM `posts` WHERE post_topic_id = '$topicid'";
	$result = mysql_query($sql);

	if($result && mysql_num_rows($result) > 0) {
		$numberofposts = mysql_num_rows($result); //so we know how much to decrease total posts by
	}

	$sql = "SELECT * FROM `topics` WHERE topic_id = '$topicid'";
	$result = mysql_query($sql);

	if($result && mysql_num_rows($result) > 0) {
		$row = mysql_fetch_array($result);
		extract($row); //so we dont have to do long array variables
		$firstpost = $topic_first_post_id;
		$forumid = $topic_forum_id;		
	}
	
	// for debugging purposes echo "<br />" . $firstpost . "<br />" . $postid . "<br />" . $topicid;
	
	/*Delete Post with $postid */
	$sql = "DELETE FROM `posts` WHERE post_id = '$postid' ";
	mysql_query($sql) or die('Error, delete query 1 failed');
		
	if ($firstpost == $postid) {
		
		/*Delete all Posts inside topic were deleting */
		$sql = "DELETE FROM `posts` WHERE post_topic_id = '$topicid' ";
		mysql_query($sql) or die('Error, delete query 2 failed');
		
		/*Delete topic itself */
		$sql = "DELETE FROM `topics` WHERE topic_id = '$topicid' ";
		mysql_query($sql) or die('Error, delete query 3 failed');
		
		echo "Your topic has been deleted, and you are being redirected to the main topic.";
				 
		//UPDATE NUMBER OF TOPICS IN FORUM
	    $sql = "UPDATE `forums` SET forum_topics=forum_topics $sign WHERE forum_id='$forumid'";
	    mysql_query($sql) or die('Error, update query failed' . $sql);

		//UPDATE NUMBER OF POSTS IN FORUM
		$sql = "UPDATE `forums` SET forum_posts=forum_posts - $numberofposts WHERE forum_id='$forumid'";
		mysql_query($sql) or die('Error, update query failed' . $sql);
	}
	else {
		//UPDATE NUMBER OF POSTS IN FORUM
		$sql = "UPDATE `forums` SET forum_posts=forum_posts $sign WHERE forum_id='$forumid'";
		mysql_query($sql) or die('Error, update query failed' . $sql);
	}
	    	    
   	//UPDATE NUMBER OF USERS POSTS
    $sql = "UPDATE `users` SET users_posts = users_posts $sign WHERE users_userid='$userid'";
    mysql_query($sql) or die('Error, update query failed' . $sql);

    
	//UPDATE NUMBER OF REPLIES TO TOPIC
	$sql = "UPDATE `topics` SET topic_replies = topic_replies $sign WHERE topic_id='$topicid'";
	mysql_query($sql) or die('Error, update query failed' . $sql);
	
	
	if ($firstpost == $postid) {
	 	echo "Your post has been deleted, and you are being redirected to the main topic."; 
	 	echo "<meta http-equiv='refresh' content='1;url=viewforum.php?id=" . $forumid . "'>";
 	}
 	else {
	 	echo "Your post has been deleted, and you are being redirected to the main topic."; 
	 	echo "<meta http-equiv='refresh' content='1;url=viewtopic.php?id=" . $topicid . "'>";
 	}
 	
}

//editpost function
if ($action == 'editpost' && (($_SESSION[user_level] != USER && $_SESSION['user_level'] != BANNED) || $_SESSION[userid] == $posterid)) {

	// for debugging purposes echo "<br />" . $postid . "<br />" . $topicid;  
	
	$sql = "SELECT * FROM `posts` p, `topics` t WHERE p.post_id = '$postid' AND t.topic_id = p.post_topic_id LIMIT 1"; //gets categories
	$result = mysql_query($sql);
	
	if($result && mysql_num_rows($result) > 0) {
		$row = mysql_fetch_array($result);
		
		echo "<center>";
		echo "<form name='post' action='$menuvar[POST]' method='post'>";
		echo "<input type='hidden' name='action' value='updatepost'>";
		echo "<input type='hidden' name='topicid' value='$topicid'>";
		echo "<input type='hidden' name='postid' value='$postid'>";
		if ($row[topic_first_post_id] == $row[post_id]) {
			echo "\n		<div class=\"VForumForumBorder\">";
			echo "\n			<div class=\"title\">$T_New_Topic</div>";
			echo "\n			<div class=\"colour1\">";
			echo "\n				<table border='0' cellpadding='0' cellspacing='1'>";
			echo "\n					<tr>";
			echo "\n						<td><b>Subject:  </b></td><td><input type='text' name='subject' value='$row[topic_title]' size='73'></td>";
			echo "\n					</tr>";
			echo "\n				</table>";	
			echo "\n			</div>";
			echo "\n		</div>";
			echo "\n		<br />";	
				
			if ($_SESSION['user_level'] != USER && $_SESSION['user_level'] != BANNED) {
				echo "\n		<div class=\"VForumForumBorder\">";
				echo "\n			<div class=\"title\">Post Options</div>";
				echo "\n			<div class=\"colour1\">";
				echo "\n				<input type=\"radio\" name=\"post_options\" value=\"" . POST_NORMAL . "\" "; if ($row[topic_type] == POST_NORMAL) { echo "checked"; } echo " /> <b>Normal</b> &nbsp;&nbsp;";
				echo "\n				<input type=\"radio\" name=\"post_options\" value=\"" . POST_STICKY . "\" "; if ($row[topic_type] == POST_STICKY) { echo "checked"; } echo " /><b>Sticky</b> &nbsp;&nbsp;";
				echo "\n				<input type=\"radio\" name=\"post_options\" value=\"" . POST_ANNOUNCE . "\" "; if ($row[topic_type] == POST_ANNOUNCE) { echo "checked"; } echo " /><b>Announce</b> &nbsp;&nbsp;";
				echo "\n				<input type=\"radio\" name=\"post_options\" value=\"" . POST_GLOBAL_ANNOUNCE . "\" "; if ($row[topic_type] == POST_GLOBAL_ANNOUNCE) { echo "checked"; } echo " /><b>Global Announce</b> &nbsp;&nbsp;";
				echo "\n		</div>";
				echo "\n		</div>";
				echo "\n		<br />";
			}	
		}					
		echo "\n	<table class='VForumForumBorder' border='0' cellpadding='0' cellspacing='1'>";
		echo "\n		<tr class='title1'>";
		echo "\n			<td class='VForumT2' colspan='2'>Edit Post</td>";	
		echo "\n		</tr>";
		bbcode_box(); //print out the bbcode buttons
		echo "\n			<center><textarea cols='70' rows='8' name='message' class='textinput' wrap='virtual' onselect='storeCaret(this);' onclick='storeCaret(this);' onkeyup='storeCaret(this);'>$row[post_text]</textarea><br /></center></td>";
		echo "\n		</tr>";
		echo "\n	</table>";		
		if ($row[topic_first_post_id] == $row[post_id]) {
			echo "\n		<br />";				
			echo "\n		<div class=\"VForumForumBorder\">";
			echo "\n			<div class=\"title\">Topic Icons</div>";
			echo "\n			<div class=\"colour1\">";
			echo "\n				<input type=\"radio\" name=\"topic_icons\" value=\"none\" "; if ($row[topic_icon] == "none") { echo "checked "; } echo "/> <b>None</b> &nbsp;&nbsp; ";
			$sql2 = "SELECT * FROM `topicicons` ORDER BY topicicons_id";
			$result2 = mysql_query($sql2);
			while ($row2 = mysql_fetch_array($result2)) {
				echo "\n				<input type=\"radio\" name=\"topic_icon\" value=\"$row2[topicicons_id]\" "; if ($row[topic_icon] == $row2[topicicons_id]) { echo "checked "; } echo "/> <img src='$row2[topicicons_image]' style='width: 20px; height: 20px;' alt='$row2[topicicons_name]' /> &nbsp;&nbsp; ";
			}
			echo "\n			</div>";				
			echo "\n		</div>";		
		}
		echo "\n		<center><input type='submit' name='submit' value='Submit' class='button'></center>";
		echo "\n	</form>";
		echo "\n	<br /><br />";
		echo "\n</div>";
	}
	mysql_free_result($result);
}

//deletetopic function
if ($action == 'deletetopic' && $_SESSION[user_level] != USER && $_SESSION['user_level'] != BANNED) {

	$sql = "SELECT * FROM `posts` WHERE post_topic_id = '$topicid'";
	$result = mysql_query($sql);

	if($result && mysql_num_rows($result) > 0) {
		$numberofposts = mysql_num_rows($result); //so we know how much to decrease total posts by
	}
	
	$sql = "SELECT * FROM `topics` WHERE topic_id = '$topicid'";
	$result = mysql_query($sql);

	if($result && mysql_num_rows($result) > 0) {
		$row = mysql_fetch_array($result);
		$forumid = $row['topic_forum_id']; //so we know how much to decrease total posts by
	}
	
	// for debugging purposes echo "<br />" . $firstpost . "<br />" . $postid . "<br />" . $topicid;
	
		
	/*Delete all Posts inside topic were deleting */
	$sql = "DELETE FROM `posts` WHERE post_topic_id = '$topicid' ";
	mysql_query($sql) or die('Error, delete query 2 failed');
		
	/*Delete topic itself */
	$sql = "DELETE FROM `topics` WHERE topic_id = '$topicid' ";
	mysql_query($sql) or die('Error, delete query 3 failed');
		
	//UPDATE NUMBER OF TOPICS IN FORUM
    $sql = "UPDATE `forums` SET forum_topics=forum_topics $sign WHERE forum_id='$forumid'";
    mysql_query($sql) or die('Error, update query failed' . $sql);
	
	//UPDATE NUMBER OF POSTS IN FORUM
	$sql = "UPDATE `forums` SET forum_posts=forum_posts - $numberofposts WHERE forum_id='$forumid'";
	mysql_query($sql) or die('Error, update query failed' . $sql);
	    	    
	echo "Your topic has been deleted, and you are being redirected to the parent forum.";
	echo "<meta http-equiv='refresh' content='1;url=viewforum.php?id=" . $forumid . "'>";
 	
}

//movetopic function
if ($action == 'movetopic' && $_SESSION[user_level] != USER && $_SESSION['user_level'] != BANNED) {
	/*Make Shadow topic*/
	$sql = "INSERT INTO `topics`(topic_forum_id, topic_title, topic_icon, topic_time, topic_poster, topic_views, topic_replies, topic_type, topic_first_post_id)".
    	   "SELECT topic_forum_id, topic_title, topic_icon, topic_time, topic_poster, topic_views, topic_replies, topic_type, topic_first_post_id FROM `topics` WHERE topic_id=$topicid";
	mysql_query($sql) or die('Error, shadowed topic insert query failed');
	
	$topic_id = mysql_insert_id();	
	
	/*Add *MOVED* to front of topic_title for OLD Topic*/
	$sql = "SELECT * FROM `topics` WHERE topic_id = '$topicid'";
	$result = mysql_query($sql);

	if($result && mysql_num_rows($result) > 0) {
		$row = mysql_fetch_array($result);
		$topictitle = $row['topic_title']; //so we know how much to decrease total posts by
	}
	
	$sql = "UPDATE `topics` SET topic_title='*MOVED* $topictitle' WHERE topic_id = '$topicid' ";
	mysql_query($sql) or die('Error, adding MOVED query failed');	
	
	/*Lock Old Topic*/
	$sql = "UPDATE `topics` SET topic_type='" . TOPIC_LOCKED . "' WHERE topic_id = '$topicid' ";
	mysql_query($sql) or die('Error, lock topic failed');	
	
	//Change All Posts for OLD topic so that they now belong to the NEW topic
	$sql = "UPDATE `posts` SET post_topic_id='$topic_id' WHERE post_topic_id = '$topicid' ";
	mysql_query($sql) or die('Error, changing posts.topic_id query failed');
	
	//Create a topic with link to the new topic
	$sql = "INSERT INTO `posts`(post_topic_id, post_poster_id, post_time, post_username, post_subject, post_text)".
    	   "VALUES('$topicid', '" . $_SESSION['userid'] . "', '$current_time', '" . $_SESSION['username'] . "', '*TOPIC MOVED*', 'Your topic has been moved to [URL=viewtopic.php?id=$topic_id]here[/URL]')";
	mysql_query($sql) or die('Error, shadowed topic insert query failed');	
	
	/*Move NEW topic*/
	$sql = "UPDATE `topics` SET topic_forum_id='$movetoforum' WHERE topic_id = '$topic_id' ";
	mysql_query($sql) or die('Error, move topic query failed');	

	//===========================================
	// This large chunk of stuff lets us
	// update the number of posts and topics
	// so that the forums are always up to date
	//===========================================
	$sql = "SELECT * FROM `posts` p, `topics` t WHERE (t.topic_id = '$topic_id' AND p.post_topic_id = '$topic_id')";
	$result = mysql_query($sql);

	if($result && mysql_num_rows($result) > 0) {
		$numberofposts = mysql_num_rows($result); //so we know how much to decrease total posts by
		
		$row = mysql_fetch_array($result);
		$forumid = $row['topic_forum_id'];
	}

	
	//UPDATE NUMBER OF TOPICS IN THE FORUM WE MOVED THE TOPIC TO
    $sql = "UPDATE `forums` SET forum_topics=forum_topics $sign WHERE forum_id='$forumid'";
    mysql_query($sql) or die('Error, update query failed' . $sql);

	//UPDATE NUMBER OF POSTS IN THE FORUM WE MOVED THE TOPIC TO
	$sql = "UPDATE `forums` SET forum_posts=forum_posts + $numberofposts WHERE forum_id='$forumid'";
	mysql_query($sql) or die('Error, update query failed' . $sql);
	    	    
	echo "Your topic has been moved to forum $movetoforum, and you are being redirected to the main topic.";
	echo "<meta http-equiv='refresh' content='1;url=viewtopic.php?id=" . $topicid . "'>";
 	
}

//locktopic function
if ($action == 'locktopic' && $_SESSION[user_level] != USER && $_SESSION['user_level'] != BANNED) {
	/*Lock topic*/
	$sql = "UPDATE `topics` SET topic_status='" . TOPIC_LOCKED . "' WHERE topic_id = '$topicid' ";
	mysql_query($sql) or die('Error, lock topic query failed');
	    	    
	echo "Your topic has been locked, and you are being redirected to the main topic.";
	echo "<meta http-equiv='refresh' content='1;url=viewtopic.php?id=" . $topicid . "'>";
 	
}

//unlocktopic function
if ($action == 'unlocktopic' && $_SESSION[user_level] != USER && $_SESSION['user_level'] != BANNED) {
	/*Unlock topic*/
	$sql = "UPDATE `topics` SET topic_status='" . TOPIC_UNLOCKED . "' WHERE topic_id = '$topicid' ";
	mysql_query($sql) or die('Error, delete query 3 failed');
	    	    
	echo "Your topic has been unlocked, and you are being redirected to the main topic.";
	echo "<meta http-equiv='refresh' content='1;url=viewtopic.php?id=" . $topicid . "'>";
 	
}

include 'includes/footer.php';
?>
