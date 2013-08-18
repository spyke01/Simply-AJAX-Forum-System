<? 
/***************************************************************************
 *                               viewtopic.php
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

$lockedcheck = '0';

$sql = "SELECT topic_title FROM `" . $DBTABLEPREFIX . "topics` WHERE topic_id = '$actual_id'"; //GETS FORUM NAME
$result = mysql_query($sql);
if ($row=mysql_fetch_array($result)) $page_title = $row[topic_title];

if (isset($actual_id)) { //BEGIN ID CHECK SECTION

	//========================================
	// Mark our topic as read in the database
	//========================================
	if($_SESSION['username']) {
		$sql = "SELECT * FROM `" . $DBTABLEPREFIX . "posts_read` WHERE pr_topic_id = '$actual_id' AND pr_userid = '" . $_SESSION['userid'] . "'"; //GETS FORUM NAME
		$result = mysql_query($sql);
		
		if(mysql_num_rows($result) == 0) //if NO results, stop the script & return the error message
		{
			$sql = "INSERT INTO `" . $DBTABLEPREFIX . "posts_read` (pr_topic_id, pr_userid)".
		  	"VALUES('$actual_id', '" . $_SESSION['userid'] . "')";
			mysql_query($sql) or die('Error, insert query failed' . $sql);
		} 
		else //if result found, run the rest of the script
		{
		    //its already in the database so dont do anything		
		}
		mysql_free_result($result); //free our query
	}
	
	//=========================================
	// Breadcrumbs
	//=========================================	
	$sql = "SELECT * FROM `" . $DBTABLEPREFIX . "topics` WHERE topic_id = '$actual_id'"; //GETS FORUM NAME
	$result = mysql_query($sql);

	while ( $row = mysql_fetch_array($result) )
	{
		extract($row); //so we dont have to do long array variables
		$subforum = $topic_forum_id;
		
		$page_content .= "	<center>
							<div class='VForumForumWidth' id=\"breadcrumbs\">	
								<img src='$themedir/buttons/nav.gif' alt='' /> <a href='$menuvar[HOME]'>Home</a> &gt; ";
		if ($subforum) { 
			$tmpsfid = $subforum;
			
			while ($tmpsfid != 0) {
				$sfsql = "SELECT * FROM `" . $DBTABLEPREFIX . "forums` WHERE forum_id = '$tmpsfid'"; //GETS FORUM NAME
				$sfresult = mysql_query($sfsql);
				while ( $sfrow = mysql_fetch_array($sfresult) ) {
					$page_content .= "<a href='$menuvar[VIEWFORUM]&id=$tmpsfid'>" . $sfrow['forum_name'] . "</a> &gt; ";
					$tmpsfid = $sfrow['subforum'];
				}
				mysql_free_result($sfresult); //free our query
			}
		}
		$page_content .= "		Viewing Topic
							</div>";
	}
	mysql_free_result($result); //free our query
		
	$sql = "UPDATE `" . $DBTABLEPREFIX . "topics` SET topic_views = topic_views +1 WHERE topic_id = '$actual_id'"; //Increase views of this page
	$result = mysql_query($sql);

	//=========================================
	// Print the posts
	//=========================================		
	
	$page_content .= "	<a name='top'></a>
						<table class='VTopicForumBorder' border='0' cellpadding='0' cellspacing='1'>
					  		<tr><td id=\"updatePosts\">";
		
		buildPosts("page", $actual_id, "");

	$page_content .= "
						</td></tr>
				  <tr class='title1'>
				<td colspan='3'>&nbsp;</td>
			</tr>
		</table>";
	
	//==========================================
	// Admin Topic Functions 
	//==========================================
	if ($_SESSION['user_level'] != USER && $_SESSION['user_level'] != BANNED) {
		$page_content .= "<br />
								<div id=\"updateMe\"></div>
							  <table border='0' cellpadding='0' cellspacing='0'>
							  <tr>
							    <td width='100%' colspan='3'>
							<form name='topicfunctions' action='$menuvar[POST]&action=movetopic' method='post'>
							<input type='hidden' name='topicid' value='$actual_id'>";
		//=============================================================
		// Create our moveto box with our list of forums/subforums			
		//=============================================================
		$page_content .= "<select name='movetoforum'>";
		$sql = "SELECT cat_id, cat_title FROM `" . $DBTABLEPREFIX . "categories` ORDER BY cat_order";
		$result = mysql_query($sql) or die(mysql_error());
		
		while (list($cid, $cname) = mysql_fetch_row($result)) {
			$page_content .= "<optgroup label='$cname'>\n";
			$page_content .= forumOptions($cid);
			$page_content .= "</optgroup>\n";
		}
		mysql_free_result($sql);		
		$page_content .= "</select>";
			
		$page_content .= "	<input type='image' src='$themedir/buttons/movetopic.jpg' name='submit' value='movetopic'><span id=\"locker\">";
		if ($lockedcheck == TOPIC_LOCKED) {
			$page_content .= "<a href=\"javascript:doLock('unlock');\"><img src='$themedir/buttons/unlocktopic.jpg' alt='unlocktopic' /></a>";
		}
		else { $page_content .= "<a href=\"javascript:doLock('lock');\"><img src='$themedir/buttons/locktopic.jpg' alt='locktopic' /></a>"; }
		$page_content .= "</span>
							<a href=\"$menuvar[POST]&action=deletetopic&topicid=$actual_id\"><img src='$themedir/buttons/deletetopic.jpg' alt='deletetopic' /></a>		
							</form></td>
							  </tr>
							</table>";
	}
	$page_content .= "<br /><br />";

	//=========================================
	// Users viewing this page
	//=========================================		
	$file = "index.php?p=" . $requested_page_id . "&id=" . $actual_id;
	users_viewing_page($file);
		
	//=========================================
	// Fast Reply
	//=========================================	
	if($_SESSION['username']) {
		if ($lockedcheck == TOPIC_LOCKED && $_SESSION['user_level'] == USER) {
			$page_content .= "<center><b>Unfortunately You Cannot Reply To This Topic Because It Is Locked.</b></center>";
		}
		else {
			if ($_SESSION['user_level'] == BANNED) {
				$page_content .= "<table class='VForumForumBorder' border='0' cellpadding='0' cellspacing='1'>
										<tr class='title1'>
											<td class='VForumT2' colspan='2'>$T_Fast_Reply</td>
										</tr>
										<tr class='row1'>
											<td>$T_Banned</td>
										</tr>
									</table>
									<br /><br />";
			}
			else {
				$page_content .= "<form name=\"post\" id=\"post\" action='' method='post' onSubmit=\"ValidateForm(this); return false;\">
									<table class='VForumForumBorder' border='0' cellpadding='0' cellspacing='1'>
										<tr class='title1'>
											<td class='VForumT2' colspan='2'>
												<div style=\"float: right;\"><a href=\"javascript:sqr_show_hide('fastReplyDrop');\"><img src=\"images/plus.png\" style=\"width: 15px; height: 15px; border:0px;\" alt=\"Show/hide fast reply form\" /></a></div>
												$T_Fast_Reply
											</td>		
										</tr>
										<tbody id=\"fastReplyDrop\">
										";
				bbcode_box(); //print out the bbcode buttons
				$page_content .= "		<center><textarea cols='70' rows='8' name='message' class='textinput' wrap='virtual' onselect='storeCaret(this);' onclick='storeCaret(this);' onkeyup='storeCaret(this);'></textarea><br /></center></td>
										</tr>
										<tr class='title1'>
											<td colspan='2'><center><input type='submit' name='submit' value='Submit' class='button'></center></td>
										</tr>
										</tbody>	
									</table>
									</form>
									<br /><br />";
			}
		}
	}
		
	$page_content .= "</center>
		<script language = \"Javascript\">			
		function doLock(option){
			var lockerSpan=document.getElementById('locker')
			var loc = 'lock';
			var unloc = 'unlock';
			
			if (option == \"unlock\"){
				new Ajax.Updater('updateMe', 'ajax.php?action=unlocktopic&topicid=$actual_id', {onComplete:function(){ new Effect.Highlight('updateMe');},asynchronous:true, evalScripts:true});
				lockerSpan.innerHTML = \"<a style='cursor: pointer; cursor: hand; color: red;' onclick='javascript:doLock(\\\"\" + loc + \"\\\");'><img src='$themedir/buttons/locktopic.jpg' alt='locktopic /></a>\";
			}
			else {
				new Ajax.Updater('updateMe', 'ajax.php?action=locktopic&topicid=$actual_id', {onComplete:function(){ new Effect.Highlight('updateMe');},asynchronous:true, evalScripts:true});
				lockerSpan.innerHTML = \"<a style='cursor: pointer; cursor: hand; color: red;' onclick='javascript:doLock(\\\"\" + unloc + \"\\\");'><img src='$themedir/buttons/unlocktopic.jpg' alt='unlocktopic' /></a>\";
			}		
		 }
		function ValidateForm(theForm){
			if (theForm.message.value!='') {
					new Ajax.Updater('updatePosts', 'ajax.php?action=reply&topicid=$actual_id', {onComplete:function(){ new Effect.Highlight('newPost');},asynchronous:true, parameters:Form.serialize(theForm), evalScripts:true}); 
					theForm.message.value=' ';
					sqr_show_hide('fastReplyDrop');
				return false;
			}
			else {
				return false;
			}
		}
		</script>";

} //END ID CHECK

$page->setTemplateVar("PageContent", $page_content);
?>