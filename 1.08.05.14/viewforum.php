<? 
/***************************************************************************
 *                               viewforum.php
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


$sql = "SELECT forum_name FROM `" . $DBTABLEPREFIX . "forums` WHERE forum_id = '$actual_id'"; //GETS FORUM NAME
$result = mysql_query($sql);
if ($row=mysql_fetch_array($result)) $page_title = $row[forum_name];

if (isset($actual_id)) { //BEGIN ID CHECK SECTION

	$sql = "SELECT * FROM `" . $DBTABLEPREFIX . "forums` WHERE forum_id = '$actual_id'"; //GETS FORUM NAME
	$result = mysql_query($sql);

	while ( $row = mysql_fetch_array($result) )
	{
		extract($row); //so we dont have to do long array variables
		
		
		//=========================================
		// This function prints out our directory 
		// tree list so we can navigate backwards
		//=========================================
		$page_content.= "<center>
						<div class='VForumForumWidth' id=\"breadcrumbs\">
						<img src='$themedir/buttons/nav.gif' alt='' /> <a href='$menuvar[HOME]'>Home</a> &gt; ";
		if ($forum_subforum) { 
			
			$tmpsfid = $forum_subforum;
			while ($tmpsfid != 0) {
				$sfsql = "SELECT * FROM `" . $DBTABLEPREFIX . "forums` WHERE forum_id = '$tmpsfid'"; //GETS FORUM NAME
				$sfresult = mysql_query($sfsql);
				while ( $sfrow = mysql_fetch_array($sfresult) ) {
					$page_content.= "<a href='$menuvar[VIEWFORUM]&id=$tmpsfid'>" . $sfrow['forum_name'] . "</a> &gt; ";
					$tmpsfid = $sfrow['forum_subforum'];
				}
				mysql_free_result($sfresult); //free our query
			}
		}
		$page_content.= "$forum_name
						</div>
				
						<table class='VForumForumBorder' border='0' cellpadding='0' cellspacing='1'>
						  <tr class='title1'>
						    <td class='VForumT1' colspan='7'>
								<div style=\"float: right;\"><a href=\"javascript:sqr_show_hide('forumDrop');\"><img src=\"images/plus.png\" style=\"width: 15px; height: 15px; border:0px;\" alt=\"Show/hide forum\" /></a></div>							 
							    $forum_name
						    </td>	
						  </tr>
						  <tbody id=\"forumDrop\">
						  <tr class='title2'>
						    <td class='VForumT2Column1'></td>
						    <td class='VForumT2Column2-4' colspan='3'>$T_Sub_Forums</td>
						    <td class='VForumT2Column5'>$T_Views</td>
						    <td class='VForumT2Column6'>$T_Replies</td>
						    <td class='VForumT2Column7'>$T_Last_Post</td>
						  </tr>";
	}
	mysql_free_result($result); //free our query
	
	$sql = "SELECT * FROM `" . $DBTABLEPREFIX . "forums` WHERE forum_subforum = '$actual_id' ORDER BY forum_order"; //GET SUBFORUMS
	$result = mysql_query($sql);
	
	while ( $row = mysql_fetch_array($result) )
	{
		extract($row); //so we dont have to do long array variables
		
		$page_content.= "  <tr class='row1'>
						    <td class='VForumR1Column1'>";
			//============================================
			// find out if the forum has any new topics
			//============================================
			$sql3 = "SELECT t.topic_forum_id FROM `" . $DBTABLEPREFIX . "topics` t, `" . $DBTABLEPREFIX . "posts_read` pr WHERE t.topic_forum_id = '$actual__id' AND t.topic_id = pr.pr_topic_id AND pr.pr_userid = '$_SESSION[userid]'"; //gets the forum info
			$result3 = mysql_query($sql3);
				
			if($result3 && mysql_num_rows($result3) > 0)
			{
				$totaltopicsinforum = mysql_num_rows($result3);
			}
			mysql_free_result($result3); //free our query
			if ($forum_topics == '0' || $forum_topics == $totaltopicsinforum) { $page_content.= "<center><img src='images/nonewf.jpg' alt='' /></center>"; }
			else { $page_content.= "<center><img src='images/newf.jpg' alt='' /></center>"; }				
		$page_content.= "</td>
						    <td class='VForumR1Column2-4' colspan='3'><a href='$menuvar[VIEWFORUM]&id=$forum_id'>$forum_name</a><br />$forum_desc</td>
						    <td class='VForumR1Column5'>$forum_topics</td>
						    <td class='VForumR1Column6'>$forum_posts</td>
						    <td class='VForumR1Column7'>";
		get_last_post('forum', $forum_id); // Print out the last post
		$page_content.= "		</td>
						  </tr>";		

	}
	mysql_free_result($result); //free our query

	$page_content.= "  <tr class='title2'>
					    <td class='VForumT1' colspan='7'></td>
					  </tr>
					  <tr class='title2'>
					    <td class='VForumT2Column1'></td>
					    <td class='VForumT2Column2'></td>
					    <td class='VForumT2Column3'>$T_Topics</td>
					    <td class='VForumT2Column4'>$T_Poster</td>
					    <td class='VForumT2Column5'>$T_Views</td>
					    <td class='VForumT2Column6'>$T_Replies</td>
					    <td class='VForumT2Column7'>$T_Last_Post</td>
					  </tr>
					  <tr><td colspan=\"7\" id=\"updateMe\">";
		
	buildTopics("page", $actual_id, "");

	
	$page_content.= "
						</td>
						</tr>
						</tbody>
					</table><br />
					<div id=\"updateMe\"></div>";
	
	$file = "index.php?p=" . $requested_page_id . "&id=" . $actual_id;
	users_viewing_page($file); //SHOW USERS VIEWING THIS PAGE
	
	//START FAST REPLY SECTION
	if($_SESSION['username']) {
		if ($lockedcheck == TOPIC_LOCKED && $_SESSION['user_level'] == USER) { }
		else {
			$page_content.= "<a href=\"javascript:sqr_show_hide('sqr');\"><img src=\"$themedir/buttons/newtopic.jpg\" border=\"0\" alt='Show/hide new topic form' /></a>
							<br /><br />
							<div id='sqr' style='display: none; position: relative; '>";	
				
			if ($_SESSION['user_level'] == BANNED) {
				$page_content.= "<table class='VForumForumBorder' border='0' cellpadding='0' cellspacing='1'>
									<tr class='title1'>
										<td class='VForumT2' colspan='2'>$T_New_Topic</td>	
									</tr>
									<tr class='row1'>
										<td>$T_Banned</td>
									</tr>
								</table>";
			}
			else {		
				$page_content.= "	<form id=\"post\" name=\"post\" action=\"\" method=\"post\" onSubmit=\"ValidateForm(this); return false;\">
										<div class=\"VForumForumBorder\">
											<div class=\"title1\">$T_New_Topic</div>
											<div class=\"row1\">
												<table border='0' cellpadding='0' cellspacing='1'>
													<tr>
														<td><b>Subject:  </b></td><td><input type='text' name='subject' size='73' class=\"required\" /></td>
													</tr>
												</table>	
											</div>
										</div>
										<br />";
				if ($_SESSION['user_level'] != USER) {
					$page_content.= "		<div class=\"VForumForumBorder\">
												<div class=\"title1\">Post Options</div>
												<div class=\"row1\"><input type=\"radio\" name=\"post_options\" value=\"" . POST_NORMAL . "\" checked /> <b>Normal</b> &nbsp;&nbsp; <input type=\"radio\" name=\"post_options\" value=\"" . POST_STICKY . "\" /> <b>Sticky</b> &nbsp;&nbsp; <input type=\"radio\" name=\"post_options\" value=\"" . POST_ANNOUNCE . "\" /> <b>Announce</b> &nbsp;&nbsp; <input type=\"radio\" name=\"post_options\" value=\"" . POST_GLOBAL_ANNOUNCE . "\" /> <b>Global Announce</b> &nbsp;&nbsp;</div>
											</div>
											<br />";
				}						
				$page_content.= "	<table class='VForumForumBorder' border='0' cellpadding='0' cellspacing='1'>
										<tr class='title1'>
											<td class='VForumT2' colspan='2'>$T_Topic_Message</td>	
										</tr>";
				bbcode_box(); //print out the bbcode buttons
				$page_content.= "			<center><textarea cols='70' rows='8' name='message' class='textinput' wrap='virtual' onselect='storeCaret(this);' onclick='storeCaret(this);' onkeyup='storeCaret(this);'></textarea><br /></center></td>
										</tr>
									</table>
										<br />
										<div class=\"VForumForumBorder\">
											<div class=\"title1\">Topic Icons</div>
											<div class=\"row1\">
												<input type=\"radio\" name=\"topic_icons\" value=\"none\" checked /> <b>None</b> &nbsp;&nbsp; ";
				
				$sql2 = "SELECT * FROM `" . $DBTABLEPREFIX . "topicicons` ORDER BY topicicons_id";
				$result2 = mysql_query($sql2);
				
				while ($row = mysql_fetch_array($result2)) {
					$page_content.= "				<input type=\"radio\" name=\"topic_icon\" value=\"$row[topicicons_id]\" /> <img src='$row[topicicons_image]' style='width: 20px; height: 20px;' alt='$row[topicicons_name]' /> &nbsp;&nbsp; ";
				}
				
				$page_content.= "			</div>			
										</div>
										<center><input type='submit' name='submit' value='Submit' class='button'></center>
									</form>
									<script type = \"text/javascript\">										
										function ValidateForm(theForm){
											if (theForm.subject.value!='' && theForm.message.value!='') {
												new Ajax.Updater('updateMe', 'ajax.php?action=newtopic&forumid=$actual_id', {onComplete:function(){ new Effect.Highlight('newTopic');},asynchronous:true, parameters:Form.serialize(theForm), evalScripts:true}); 
												theForm.subject.value=' ';
												theForm.message.value=' ';
												sqr_show_hide('sqr');
											}
											return false;
		 								}
									</script>";
			}
			$page_content.= "	<br /><br />
							</div>";			
		}
	}
	$page_content.= "</center>";
	
	//Print out our nice table
	$page_content.= "<center>
						<table class='VForumForumBorder' border='0' cellpadding='0' cellspacing='0' width='600'>
							<tr class='title2'>
								<td width='50%' align='left'>
										<img src='images/newp.jpg' alt='' /> New Posts<br />
								</td>
								<td width='50%' align='left'>
										<img src='images/nonewp.jpg' alt='' /> No New Posts<br />
								</td>
							</tr>
							<tr class='title2'>
								<td width='50%' align='left'>
										<img src='images/newp.jpg' alt='' /> New Poll<br />
								</td>
								<td width='50%' align='left'>
										<img src='images/nonewp.jpg' alt='' /> No New Polls<br />
								</td>
							</tr>
							<tr class='title2'>
								<td width='50%' align='left'>
										<img src='images/newa.jpg' alt='' /> New Announcement<br />
								</td>
								<td width='50%' align='left'>
										<img src='images/nonewa.jpg' alt='' /> No New Announcements<br />
								</td>
							</tr>
							<tr class='title2'>
								<td width='50%' align='left'>
										<img src='images/news.jpg' alt='' /> New Sticky<br />
								</td>
								<td width='50%' align='left'>
										<img src='images/nonews.jpg' alt='' /> No New Stickies<br />
								</td>
							</tr>
							<tr class='title2'>
								<td width='50%' align='left'>
										<img src='images/newl.jpg' alt='' /> New Locked<br />
								</td>
								<td width='50%' align='left'>
										<img src='images/nonewl.jpg' alt='' /> No New Locked<br />
								</td>
							</tr>
						</table>
					</center><br /><br />";

} //END ID CHECK

$page->setTemplateVar("PageContent", $page_content);
?>
