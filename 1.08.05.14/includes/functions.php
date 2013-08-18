<?php 
/***************************************************************************
 *                               functions.php
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
//==================================================
// Strips Dangerous tags out of input boxes 
//==================================================
function keepsafe($makesafe) {
	$makesafe=strip_tags($makesafe); // strip away any dangerous tags
	$makesafe=str_replace(" ","",$makesafe); // remove spaces from variables
	$makesafe=str_replace("%20","",$makesafe); // remove escaped spaces
	$makesafe = trim(preg_replace('/[^\x09\x0A\x0D\x20-\x7F]/e', '"&#".ord($0).";"', $makesafe)); //encodes all ascii items above #127

    // Stripslashes
    if (get_magic_quotes_gpc()) {
        $makesafe = stripslashes($makesafe);
    }
    // Quote if not integer
    if (!is_numeric($makesafe)) {
        $makesafe = mysql_real_escape_string($makesafe);
    }
    return $makesafe;
}

//==================================================
// Strips Dangerous tags out of textareas 
//==================================================
function keeptasafe($makesafe) {
	$makesafe = trim(preg_replace('/[^\x09\x0A\x0D\x20-\x7F]/e', '"&#".ord($0).";"', $makesafe)); //encodes all ascii items above #127
	
    // Stripslashes
    if (get_magic_quotes_gpc()) {
        $makesafe = stripslashes($makesafe);
    }
    // Quote if not integer
    if (!is_numeric($makesafe)) {
        $makesafe = mysql_real_escape_string($makesafe);
    }
    return $makesafe;
}

//==================================================
// Strips Dangerous tags out of get and post values
//==================================================
function parseurl($makesafe) {
	$makesafe=strip_tags($makesafe); // strip away any dangerous tags
	$makesafe=str_replace(" ","",$makesafe); // remove spaces from variables
	$makesafe=str_replace("%20","",$makesafe); // remove escaped spaces
	$makesafe = trim(preg_replace('/[^\x09\x0A\x0D\x20-\x7F]/e', '"&#".ord($0).";"', $makesafe)); //encodes all ascii items above #127

    // Stripslashes
    if (get_magic_quotes_gpc()) {
        $makesafe = stripslashes($makesafe);
    }
    // Quote if not integer
    if (!is_numeric($makesafe)) {
        $makesafe = mysql_real_escape_string($makesafe);
    }
    return $makesafe;
}

//==================================================
// Creates a date from a timestamp
//==================================================
function makeDate($time) {
	$date = @gmdate('l M d, Y h:i a', $time + (3600 * '-7.00')); //uses a function from PHPbb to display date/time from timestamp
	return $date;
}
function make_lastpost_date($time) {
	$date = @gmdate('M d h:i a', $time + (3600 * '-7.00')); //uses a function from PHPbb to display date/time from timestamp
	return $date;
}
function make_joined_date($time) {
	$date = @gmdate('M d, Y', $time + (3600 * '-7.00')); //uses a function from PHPbb to display date/time from timestamp
	return $date;
}


//=================================================
// BBCode Functions Generated from: 
// http://bbcode.strefaphp.net/bbcode.php
// A gigantic thanks goes out to the 
// programmers there!!
// 
// Use the function like so: echo bbcode($string);
//=================================================
Function bbcode($str){
	global $DBTABLEPREFIX;
	// Smilies - YAY!! Finally
	$sql = "SELECT smilies_code, smilies_image FROM `" . $DBTABLEPREFIX . "smilies`";
	$result = mysql_query($sql);
	
	while ($row = mysql_fetch_array($result)) {
		$str=str_replace($row[smilies_code],"[img]$row[smilies_image][/img]",$str);
	}
	mysql_free_result($result);
	
	// Profanity filter - No cursing!
	$sql = "SELECT profanityfilter_code, profanityfilter_image FROM `" . $DBTABLEPREFIX . "profanityfilter`";
	$result = mysql_query($sql);
	
	while ($row = mysql_fetch_array($result)) {
		$str = preg_replace("#\b" . $row[profanityfilter_code] . "\b#i", "[img]$row[profanityfilter_image][/img]", $str);
	}
	mysql_free_result($result);
	
	// Makes < and > page friendly
	//$str=str_replace("&","&amp;",$str);
	$str=str_replace("<","&lt;",$str);
	$str=str_replace(">","&gt;",$str);
	
	
	// Link inside tags new window
	$str = preg_replace("#\[url\](.*?)?(.*?)\[/url\]#si", "<a href=\"\\1\\2\">\\1\\2</A>", $str);
	
	// Link inside first tag new window
	$str = preg_replace("#\[url=(.*?)?(.*?)\](.*?)\[/url\]#si", "<a href=\"\\2\">\\3</A>", $str);
	
	// Link inside tags
	$str = preg_replace("#\[url2\](.*?)?(.*?)\[/url2\]#si", "<a href=\"\\1\\2\">\\1\\2</A>", $str);
	
	// Link inside first tag
	$str = preg_replace("#\[url2=(.*?)?(.*?)\](.*?)\[/url2\]#si", "<a href=\"\\2\">\\3</A>", $str);
	
	// Lightbox Image Link
	$str = preg_replace("#\[lightbox=(.*?)?(.*?)\](.*?)\[/lightbox\]#si", "<a href=\"\\2\" rel=\"lightbox\"><img src=\"\\3\" alt=\"lightbox image\" /></a>", $str);
	
	// Automatic links if no url tags used
	$str = preg_replace_callback("#([\n ])([a-z]+?)://([a-z0-9\-\.,\?!%\*_\#:;~\\&$@\/=\+]+)#si", "bbcode_autolink", $str);
	$str = preg_replace("#([\n ])www\.([a-z0-9\-]+)\.([a-z0-9\-.\~]+)((?:/[a-z0-9\-\.,\?!%\*_\#:;~\\&$@\/=\+]*)?)#i", " <a href=\"http://www.\\2.\\3\\4\">www.\\2.\\3\\4</a>", $str);
	$str = preg_replace("#([\n ])([a-z0-9\-_.]+?)@([\w\-]+\.([\w\-\.]+\.)?[\w]+)#i", "\\1<a href=\"mailto: \\2@\\3\">\\2_(at)_\\3</a>", $str);
	
	// PHP Code
	$str = preg_replace("#\[php\](.*?)\[/php]#si", "<div class=\"codetop\"><u><b>&lt?PHP:</b></u></div><div class=\"codemain\">\\1</div>", $str);
	
	// Bold
	$str = preg_replace("#\[b\](.*?)\[/b\]#si", "<b>\\1</b>", $str);
	
	// Italics
	$str = preg_replace("#\[i\](.*?)\[/i\]#si", "<i>\\1</i>", $str);
	
	// Underline
	$str = preg_replace("#\[u\](.*?)\[/u\]#si", "<u>\\1</u>", $str);
	
	// Align text
	$str = preg_replace("#\[align=(left|center|right)\](.*?)\[/align\]#si", "<div align=\"\\1\">\\2</div>", $str); 
	
	// Font Color
	$str = preg_replace("#\[color=(.*?)\](.*?)\[/color\]#si", "<span style=\"color: \\1;\">\\2</span>", $str);
	
	// Font Size
	$str = preg_replace("#\[size=(.*?)\](.*?)\[/size\]#si", "<span style=\"font-size: \\1px;\">\\2</span>", $str);
	
	// Image
	$str = preg_replace("#\[img\](.*?)\[/img\]#si", "<img src=\"\\1\" border=\"0\" alt=\"\" />", $str);
	
	// Uploaded image
	$str = preg_replace("#\[ftp_img\](.*?)\[/ftp_img\]#si", "<img src=\"img/\\1\" border=\"0\" alt=\"\" />", $str);
	
	// HR Line
	$str = preg_replace("#\[hr=(\d{1,2}|100)\]#si", "<hr class=\"linia\" width=\"\\1%\" />", $str);
	
	// Code
	$str = preg_replace("#\[code\](.*?)\[/code]#si", "<div class=\"codetop\"><u><b>Code:</b></u></div><div class=\"codemain\">\\1</div>", $str);
	
	// Code, Provide Author
	$str = preg_replace("#\[code=(.*?)\](.*?)\[/code]#si", "<div class=\"codetop\"><u><b>Code \\1:</b></u></div><div class=\"codemain\">\\2</div>", $str);
	
	// Quote
	$str = preg_replace("#\[quote\](.*?)\[/quote]#si", "<div class=\"quotetop\"><u><b>Quote:</b></u></div><div class=\"quotemain\">\\1</div>", $str);
	
	// Quote, Provide Author
	$str = preg_replace("#\[quote=(.*?)\](.*?)\[/quote]#si", "<div class=\"quotetop\"><u><b>Quote \\1:</b></u></div><div class=\"quotemain\">\\2</div>", $str);
	
	// Email
	$str = preg_replace("#\[email\]([a-z0-9\-_.]+?)@([\w\-]+\.([\w\-\.]+\.)?[\w]+)\[/email\]#i", "<a href=\"mailto:\\1@\\2\">\\1_(at)_\\2</a>", $str);

	// Email, Provide Author
	$str = preg_replace("#\[email=([a-z0-9\-_.]+?)@([\w\-]+\.([\w\-\.]+\.)?[\w]+)?(.*?)\](.*?)\[/email\]#i", "<a href=\"mailto:\\1_(at)_\\2\">\\5</a>", $str);
	
	// YouTube
	$str = preg_replace("#\[youtube\]http://(?:www\.)?youtube.com/v/([0-9A-Za-z-_]{11})[^[]*\[/youtube\]#si", "<object width=\"425\" height=\"350\"><param name=\"movie\" value=\"http://www.youtube.com/v/\\1\"></param><param name=\"wmode\" value=\"transparent\"></param><embed src=\"http://www.youtube.com/v/\\1\" type=\"application/x-shockwave-flash\" wmode=\"transparent\" width=\"425\" height=\"350\"></embed></object>", $str);
	$str = preg_replace("#\[youtube\]http://(?:www\.)?youtube.com/watch\?v=([0-9A-Za-z-_]{11})[^[]*\[/youtube\]#si", "<object width=\"425\" height=\"350\"><param name=\"movie\" value=\"http://www.youtube.com/v/\\1\"></param><param name=\"wmode\" value=\"transparent\"></param><embed src=\"http://www.youtube.com/v/\\1\" type=\"application/x-shockwave-flash\" wmode=\"transparent\" width=\"425\" height=\"350\"></embed></object>", $str);
	
	// Google Video
	$str = preg_replace("#\[gvideo\]http://video.google.[A-Za-z0-9.]{2,5}/videoplay\?docid=([0-9A-Za-z-_]*)[^[]*\[/gvideo\]#si", "<object width=\"425\" height=\"350\"><param name=\"movie\" value=\"http://video.google.com/googleplayer.swf\?docId=\\1\"></param><param name=\"wmode\" value=\"transparent\"></param><embed src=\"http://video.google.com/googleplayer.swf\?docId=\\1\" type=\"application/x-shockwave-flash\" allowScriptAccess=\"sameDomain\" quality=\"best\" bgcolor=\"#ffffff\" scale=\"noScale\" salign=\"TL\"  FlashVars=\"playerMode=embedded\" wmode=\"transparent\" width=\"425\" height=\"350\"></embed></object>", $str);
	
	// Ordered Lists
	$str = preg_replace("#\[olist\](.*?)\[/olist\]#si", "<ol>\\1</ol>", $str);
	
	// Unordered Lists
	$str = preg_replace("#\[list\](.*?)\[/list\]#si", "<ul>\\1</ul>", $str);
	
	// List Items
	$str = preg_replace("#\[item\](.*?)\[/item\]#si", "<li>\\1</li>", $str);
	
	// change \n to <br />
	$str=nl2br($str);
	
	// return bbdecoded string
	return $str;
}


function bbcode_autolink($str) {
$lnk=$str[3];
if(strlen($lnk)>30){
if(substr($lnk,0,3)=='www'){$l=9;}else{$l=5;}
$lnk=substr($lnk,0,$l).'(...)'.substr($lnk,strlen($lnk)-8);}
return ' <a href="'.$str[2].'://'.$str[3].'">'.$str[2].'://'.$lnk.'</a>';
}

//==================================================
// Replacement for die()
// Used to display msgs without displaying the board
//==================================================
function message_die($msg_text = '', $msg_title = '') {
	echo "<html>\n<body>\n" . $msg_title . "\n<br /><br />\n" . $msg_text . "</body>\n</html>";
	include('includes/footer.php');
	exit;
}


//==================================================
// Prints out a lovely little bbcode button box
// Keeps me from having to redo several pages
//==================================================
function bbcode_box() {
	global $DBTABLEPREFIX, $page_content;
	$page_content .= "	<tr class='row1'>
						<td width=\"25%\">
							<center>
								<table border=\"0\" cellspacing=\"0\" cellpadding=\"2\">";
	
	$x = 1;
	$doit = 1;
	$smiliesql = "SELECT smilies_code, smilies_image FROM `" . $DBTABLEPREFIX . "smilies`";
	$smilieresult = mysql_query($smiliesql);	
	$numresults = mysql_num_rows($smilieresult);
	$extratds = "";
	
	$numresultsdiv = $numresults / 3;
	$pieces = explode(".", $numresultsdiv);	
	$numresults = 3 - ($numresults - ($pieces[0] * 3)); //this length of code will get how many extra ttds we need so that our code isnt screwy
		
	for ($y = 0; $y < $numresults; $y++) {
		$extratds .= "\n							<td>&nbsp;</td>";
	}
	
	while ($row = mysql_fetch_array($smilieresult)) {
		if ($x <= 2) { 
			if ($x == 1) { $page_content .= "						<tr>"; }
			$page_content .= "							<td><img src=\"$row[smilies_image]\" alt=\"\" onmouseover=\"this.style.cursor='hand';\" onclick=\"javascript:emoticon('$row[smilies_code]')\" /></a></td>";		
			$x++; 
		}
		else { 			
			$page_content .= "							<td><img src=\"$row[smilies_image]\" alt=\"\" onmouseover=\"this.style.cursor='hand';\" onclick=\"javascript:emoticon('$row[smilies_code]')\" /></a></td>
											</tr>";
			$x = 1; 
		}		 
	}
	mysql_free_result($smilieresult);	
	
	$page_content .= $extratds;
	$page_content .= "						</tr>
								</table>
							</center>
						</td>
						<td width=\"75%\">
							<center>
								<table border='0' cellspacing='0' cellpadding='0'>
									<tr class='row1' style='padding: 0px; margin: 0px;'>
										<td style=\"padding: 1px; margin: 0px;\"><img src=\"images/bbcode/bold.gif\" alt=\"Bold\" title=\"Bold\" onclick=\"bbstyle(0)\" onmouseover=\"helpline('b')\" height=\"24\" width=\"25\" /></td>
										<td style=\"padding: 1px; margin: 0px;\"><img src=\"images/bbcode/italic.gif\" alt=\"Italic\" title=\"Italic\" onclick=\"bbstyle(2)\" onmouseover=\"helpline('i')\" height=\"24\" width=\"25\" /></td>
										<td style=\"padding: 1px; margin: 0px;\"><img src=\"images/bbcode/underline.gif\" alt=\"Underline\" title=\"Underline\" onclick=\"bbstyle(4)\" onmouseover=\"helpline('u')\" height=\"24\" width=\"25\" /></td>
										<td style=\"padding: 1px; margin: 0px;\"><img src=\"images/bbcode/image.gif\" alt=\"Insert Image\" title=\"Insert Image\" onclick=\"bbstyle(14)\" onmouseover=\"helpline('p')\" height=\"24\" width=\"25\" /></td>
										<td style=\"padding: 1px; margin: 0px;\"><img src=\"images/bbcode/email.gif\" alt=\"Insert Email\" title=\"Insert Email\" onclick=\"bbstyle(18)\" onmouseover=\"helpline('email')\" height=\"24\" width=\"25\" /></td>
										<td style=\"padding: 1px; margin: 0px;\"><img src=\"images/bbcode/hyperlink.gif\" alt=\"Insert Link\" title=\"Insert Link\" onclick=\"bbstyle(16)\" onmouseover=\"helpline('w')\" height=\"24\" width=\"25\" /></td>
										<td style=\"padding: 1px; margin: 0px;\"><img src=\"images/bbcode/left_just.gif\" alt=\"Align Text To The Left\" title=\"Align Text To The Left\" onclick=\"bbstyle(24)\" onmouseover=\"helpline('left')\" height=\"24\" width=\"25\" /></td>
										<td style=\"padding: 1px; margin: 0px;\"><img src=\"images/bbcode/center.gif\" alt=\"Align Text To The Center\" title=\"Align Text To The Center\" onclick=\"bbstyle(22)\" onmouseover=\"helpline('center')\" height=\"24\" width=\"25\" /></td>
										<td style=\"padding: 1px; margin: 0px;\"><img src=\"images/bbcode/right_just.gif\" alt=\"Align Text To The Right\" title=\"Align Text To The Right\" onclick=\"bbstyle(26)\" onmouseover=\"helpline('right')\" height=\"24\" width=\"25\" /></td>
										<td style=\"padding: 1px; margin: 0px;\"><img src=\"images/bbcode/numbered_list.gif\" alt=\"Insert List\" title=\"Insert List\" onclick=\"bbstyle(10)\" onmouseover=\"helpline('l')\" height=\"24\" width=\"25\" /></td>
										<td style=\"padding: 1px; margin: 0px;\"><img src=\"images/bbcode/list.gif\" alt=\"Insert List\" title=\"Insert List\" onclick=\"bbstyle(12)\" onmouseover=\"helpline('o')\" height=\"24\" width=\"25\" /></td>
										<td style=\"padding: 1px; margin: 0px;\"><img src=\"images/bbcode/quote.gif\" alt=\"Wrap in a Quote\" title=\"Wrap in a Quote\" onclick=\"bbstyle(6)\" onmouseover=\"helpline('q')\" height=\"24\" width=\"25\" /></td>
										<td style=\"padding: 1px; margin: 0px;\"><img src=\"images/bbcode/code.gif\" code=\"\" title=\"Code\" onclick=\"bbstyle(8)\" onmouseover=\"helpline('c')\" height=\"24\" width=\"25\" /></td>
										<td style=\"padding: 1px; margin: 0px;\"><img src=\"images/bbcode/php.gif\" code=\"\" title=\"PHP\" onclick=\"bbstyle(20)\" onmouseover=\"helpline('php')\" height=\"24\" width=\"25\" /></td>
										<td style=\"padding: 1px; margin: 0px;\"><img src=\"images/bbcode/youtube.gif\" code=\"\" title=\"YouTube\" onclick=\"bbstyle(28)\" onmouseover=\"helpline('youtube')\" height=\"20\" width=\"20\" /></td>
										<td style=\"padding: 1px; margin: 0px;\"><img src=\"images/bbcode/googlevid.gif\" code=\"\" title=\"Google Video\" onclick=\"bbstyle(30)\" onmouseover=\"helpline('gvideo')\" height=\"20\" width=\"20\" /></td>
								</tr>
								<tr class='row1'>
									<td colspan='12'>
			 							&nbsp;Font colour: 
										<select name=\"fontcolor\" onchange=\"bbfontstyle('[color=' + this.form.fontcolor.options[this.form.fontcolor.selectedIndex].value + ']', '[/color]');this.selectedIndex=0;\" onmouseover=\"helpline('s')\">
											<option style=\"color:black; background-color: #FAFAFA\" value=\"#444444\">Default</option>
											<option style=\"color:darkred; background-color: #FAFAFA\" value=\"darkred\">Dark Red</option>
											<option style=\"color:red; background-color: #FAFAFA\" value=\"red\">Red</option>
											<option style=\"color:orange; background-color: #FAFAFA\" value=\"orange\">Orange</option>
											<option style=\"color:brown; background-color: #FAFAFA\" value=\"brown\">Brown</option>
											<option style=\"color:yellow; background-color: #FAFAFA\" value=\"yellow\">Yellow</option>
											<option style=\"color:green; background-color: #FAFAFA\" value=\"green\">Green</option>
											<option style=\"color:olive; background-color: #FAFAFA\" value=\"olive\">Olive</option>
											<option style=\"color:cyan; background-color: #FAFAFA\" value=\"cyan\">Cyan</option>
											<option style=\"color:blue; background-color: #FAFAFA\" value=\"blue\">Blue</option>
											<option style=\"color:darkblue; background-color: #FAFAFA\" value=\"darkblue\">Dark Blue</option>
											<option style=\"color:indigo; background-color: #FAFAFA\" value=\"indigo\">Indigo</option>
											<option style=\"color:violet; background-color: #FAFAFA\" value=\"violet\">Violet</option>
											<option style=\"color:white; background-color: #FAFAFA\" value=\"white\">White</option>
											<option style=\"color:black; background-color: #FAFAFA\" value=\"black\">Black</option>
										</select>  
			 							&nbsp;Font size: 
										<select name=\"fontsize\" onchange=\"bbfontstyle('[size=' + this.form.fontsize.options[this.form.fontsize.selectedIndex].value + ']', '[/size]')\" onmouseover=\"helpline('f')\">
											<option value=\"7\">Tiny</option>
											<option value=\"9\">Small</option>
											<option value=\"12\" selected>Normal</option>
											<option value=\"18\">Large</option>
											<option  value=\"24\">Huge</option>
										</select>
									</td>
								</tr>
								<tr class='row1'>
									<td colspan='12'>
										<input name=\"helpbox\" size='45' maxlength='100' style='width: 380px; font-size: 10px;' class='helpline' value=\"Tip: Styles can be applied quickly to selected text.\" type=\"text\">
									</td>
							</tr>
							</table>
						</center>";
}

//==================================================
// This function will build a topic list
//==================================================
function buildTopics($printTo, $forumID, $newTopicID) {
	global $DBTABLEPREFIX, $menuvar, $themedir, $page_content, $content;
	
	$content= "
					<table border='0' cellpadding='0' cellspacing='1' width=\"100%\">";
	
	$rowcheck = '0';
	$sql = "SELECT * FROM `" . $DBTABLEPREFIX . "topics` WHERE (topic_forum_id = '$forumID' OR topic_type=" . POST_GLOBAL_ANNOUNCE . ") ORDER BY topic_type DESC, topic_id DESC"; //GETS TOPICS
	$result = mysql_query($sql);

	while ( $row = mysql_fetch_array($result) )
	{
		extract($row); //so we dont have to do long array variables
		if ($topic_type == POST_STICKY) { $typetext = "Stcky: "; }
		elseif ($topic_type == POST_ANNOUNCE) {$typetext = "Announcement: ";} 
		elseif ($topic_type == POST_GLOBAL_ANNOUNCE) { $typetext = "Global Announcement: ";	}
		else { $typetext = ""; }
		
		$id = ($newTopicID == $topic_id) ? " id = \"newTopic\"" : "";
		
		$content.= "  <tr class='row1'" . $id . ">
						    <td class='VForumR1Column1'>";
		//========================================
		// Mark wether our topic is read or not
		//========================================
		if($_SESSION['username']) {
			$sql2 = "SELECT * FROM `" . $DBTABLEPREFIX . "posts_read` WHERE pr_topic_id = '$topic_id' AND pr_userid = '" . $_SESSION['userid'] . "'"; //GETS FORUM NAME
			$result2 = mysql_query($sql2);
			
			if(mysql_num_rows($result2) == 0) //if NO results, then the topic is new
			{
				//================================
				// New Posts
				// Display topic icon
				//================================
				if ($topic_status == TOPIC_LOCKED) {
					$content.= "<img src='images/newl.jpg' alt='' />";
				}
				else{
					if ($topic_type == POST_STICKY) {
						$content.= "<img src='images/news.jpg' alt='' />";
					}
					elseif ($topic_type == POST_ANNOUNCE || $topic_type == POST_GLOBAL_ANNOUNCE) {
						$content.= "<img src='images/newa.jpg' alt='' />";
					}
					else {
						$content.= "<img src='images/newp.jpg' alt='' />";
					}
				}
			} 
			else //if result found, run the rest of the script
			{
				//================================
				// No new Posts
				// Display topic icon
				//================================
				if ($topic_status == TOPIC_LOCKED) {
					$content.= "<img src='images/nonewl.jpg' alt='' />";
				}
				else{
					if ($topic_type == POST_STICKY) {
						$content.= "<img src='images/nonews.jpg'>";
					}
					elseif ($topic_type == POST_ANNOUNCE || $topic_type == POST_GLOBAL_ANNOUNCE) {
						$content.= "<img src='images/nonewa.jpg' alt='' />";
					}
					else {
						$content.= "<img src='images/nonewp.jpg' alt='' />";
					}	
				}
			}
			mysql_free_result($result2); //free our query
		}
		else //if result found, run the rest of the script
		{
			//================================
			// Guest is viewing the forum
			// Display topic icon
			//================================
			if ($topic_type == POST_STICKY) {
				$content.= "<img src='images/nonews.jpg' alt='' />";
			}
			elseif ($topic_type == POST_ANNOUNCE || $topic_type == POST_GLOBAL_ANNOUNCE) {
				$content.= "<img src='images/nonewa.jpg' alt='' />";
			}
			else {
				$content.= "<img src='images/nonewp.jpg' alt='' />";
			}	
		}
		
		$content.= "	</td>
						    <td class='VForumR1Column2'>";
		
		$sql2 = "SELECT topicicons_image FROM `" . $DBTABLEPREFIX . "topicicons` WHERE topicicons_id = '$topic_icon' LIMIT 1";
		$result2 = mysql_query($sql2);
		while ($row2 = mysql_fetch_array($result2)) {
			$content.= "<img src=\"$row2[topicicons_image]\" alt=\"\" style='width: 20px; height: 20px;' />";
		}
		$content.= "	</td>
						    <td class='VForumR1Column3'>$typetext<a href='$menuvar[VIEWTOPIC]&id=$topic_id'>$topic_title</a></td>";
		
		//OUTPUT USERNAME BY LOOKING UP TOPIC_POSTER
		$sql2 = "SELECT users_username FROM `" . $DBTABLEPREFIX . "users` WHERE users_id = '$topic_poster'";
		$result2 = mysql_query($sql2);

		while ( $r = mysql_fetch_array($result2) )
		{
			$content .= "
							<td class='VForumR1Column4'>
								$r[users_username]
							</td>";
		}
		mysql_free_result($result2);
	
		$content.= "    <td class='VForumR1Column5'>$topic_views</td>
						    <td class='VForumR1Column6'>$topic_replies</td>
						    <td class='VForumR1Column7'>";
		get_last_post("content", "topic", $topic_id); // Print out the last topic
		$content.= "		</td>
						  </tr>";		
	}
	mysql_free_result($result);
	
	$content.= "
					</table>";
					
	if ($printTo == "page") { $page_content .= $content; }
	else { echo $content; }
}

//==================================================
// This function will build a post list
//==================================================
function buildPosts($printTo, $topicID, $newPostID) {
	global $DBTABLEPREFIX, $menuvar, $themedir, $page_content, $content, $actual_highlight;
	
	$content = "
					<table border='0' cellpadding='0' cellspacing='1' width=\"100%\">";
	$nextrow = '0';
	$sql = "SELECT * FROM `" . $DBTABLEPREFIX . "topics` t, `" . $DBTABLEPREFIX . "posts` p, `" . $DBTABLEPREFIX . "users` u  WHERE p.post_topic_id = '$topicID' AND t.topic_id = '$topicID' AND u.users_id = p.post_poster_id ORDER BY p.post_time"; //gets categories
	$result = mysql_query($sql);
	
	while ( $row = mysql_fetch_array($result) )	{
		//extract($row); //so we dont have to do long array variables //cant use it because it overwrites session variables
		
		$post_time = $row['post_time'];
		$post_time = makeDate($post_time);
		$actual_id = $row[topic_id];
				
		$lockedcheck = $row['topic_status'];
		
		if ($nextrow == '0') {
			$content .= "  <tr class='title1'>
						<td class='VTopicT1' colspan='3'>
							<div style=\"float: right;\"><a href=\"javascript:sqr_show_hide('postsDrop');\"><img src=\"images/plus.png\" style=\"width: 15px; height: 15px; border:0px;\" alt=\"Show/hide posts\" /></a></div>
							$row[topic_title]
						</td>
					</tr>
					<tbody id=\"postsDrop\">";
			$nextrow = '1';
		}
		else {
			$content .= "	<tr class='title2'>
							<td class='VTopicT2Divider' colspan='3'></td>
						</tr>";
		}
		
		$id = ($newPostID == $row[post_id]) ? "newTopic" : $row[post_id];
		
		$content .= "  <tr>
					<td id=\"$id\">
						<table border=\"0\" cellspacing=\"1\" cellpadding=\"0\" width=\"100%\">
						<tr class='title2'>
				    <td class='VTopicT2Column1'><a href='$menuvar[PROFILE]&action=viewprofile&id=" . $row['users_id'] . "'>" . $row['post_username'] . "</a></td>
				    <td class='VTopicT2Column2'><b>$T_Posted</b> $post_time</td>
				    <td class='VTopicT2Column3'>
						<a name='$row[post_id]'></a>"; //Allows us to jump straight to a post using the #topicid method
		
		//BEGIN EDIT AND DELETE FUNCTIONS
		//$content .= $row['post_username'] . " " . $_SESSION['username'] . " " . $_SESSION['user_level'];
		if ($row['post_username'] == $_SESSION['username'] || ($_SESSION['user_level'] != USER && $_SESSION['user_level'] != BANNED)) { 
			$content .= "	<a style=\"cursor: pointer; cursor: hand;\" onclick=\"new Ajax.Request('ajax.php?action=deletepost&topicid=$row[topic_id]&postid=$row[post_id]', {asynchronous:true, onSuccess:function(){ new Effect.SlideUp('$id');}});\"><img src='$themedir/buttons/delete.jpg' alt='deletepost' /></a>
								<a href=\"$menuvar[POST]&action=editpost&topicid=$row[topic_id]&postid=$row[post_id]\"><img src='$themedir/buttons/edit.jpg' alt='editpost' /></a>";	
		} 
		//END EDIT AND DELETE FUNCTIONS
		
		$content .= "	</td>
				</tr>
				<tr class='row1'><td class='VTopicR1Column1'>";
		if ($row['users_avatar'] != NULL || $row['users_avatar'] != "") {
			$content .= "    <a href='$menuvar[PROFILE]&action=viewprofile&id=" . $row['users_id'] . "'><img src='" . $row['users_avatar'] . "' alt='' /></a><br />";
		}
		rank_title("content", $row['post_username']);
		$content .= "</td>
				<td class='VTopicR1Column2-3' colspan='2'>"; 

		$post_text = bbcode($row['post_text']); //CHANGE BBCODE IN POST TO HTML
		$post_text = (trim($actual_highlight) != "") ? str_ireplace($actual_highlight, "<span class=\"highlight\">$actual_highlight</span>", $post_text) : $post_text;
		$sig = bbcode($row['users_sig']); //CHANGE BBCODE IN SIGNATURE TO HTML
		
		$content .= "    $post_text"; //OUTPUT THE MESSAGE
		if ($row['users_attachsig']) {
			$content .= "    <hr width='100' />" . $sig; //OUPUT THE USER'S SIGNATURE
		}
		$content .= "  </td>
				</tr>
				<tr class='title2'>
					<td class='VTopicT2Column1'>"; 
		contact_methods("content", $row['post_username']); //this has to be on its own line otherwise it will show as being after <tr> and not after <td> in the code
		$content .= "	</td>
				<td class='VTopicT2Column1'><a href='#top'><img src='$themedir/buttons/uparrow.png' alt='Back To The Top' class='uparrow' /></a></td><td class='VTopicT2Column1'>&nbsp;</td>
			</tr>
			</tbody>
			</table>
			</td>
			</tr>";

	}
	mysql_free_result($result); //FREE THE SQL RESULT	
					
	$content.= "
					</table>";
					
	if ($printTo == "page") { $page_content .= $content; }
	else { echo $content; }
}

//==================================================
// This function will echo our Title, Rank, and
// any other item we need to add for posts and 
// private messages
//==================================================
function rank_title($where, $user) {
	global $DBTABLEPREFIX, $menuvar, $themedir, $page_content, $content;
	$sql = "SELECT * FROM `" . $DBTABLEPREFIX . "users` u, `" . $DBTABLEPREFIX . "ranks` r WHERE u.users_username='$user' AND u.users_posts>r.rank_posts ORDER BY r.rank_posts DESC LIMIT 1";
	$result = mysql_query($sql);
	
	if(mysql_num_rows($result) == 0) //if NO results, stop the script & return the error message
	{
		
	} 
	else //if result found, run the rest of the script
	{
		//Find out if our user is online
		$sql2 = "SELECT * FROM `" . $DBTABLEPREFIX . "usersonline` WHERE uo_username = '$user'";
		$result2 = mysql_query($sql2) or die (mysql_error()."<br />Couldn't execute query: $sql");	
					
		if(mysql_num_rows($result2) == 0) {
			$onlinefile = "offline";
		} 
		else {
			$onlinefile = "online";		
		}
		mysql_free_result($result2); //free our query
		
		while ( $row = mysql_fetch_array($result) ) {
			$signup_date = make_joined_date($row[users_signup_date]);
			$newContent .= "\n<b>$row[users_title]</b> \n<br />$row[rank_name]\n<br /><img src='$row[rank_image]' alt='' />\n<br /><b>Posts:</b> $row[users_posts]\n<br /><b>Joined:</b> $signup_date\n<br /><img src='$themedir/buttons/$onlinefile.jpg' alt='' />\n<br />";
		}
	}
	
	if ($where == "content") { $content .= $newContent; }
	elseif ($where == "pageContent") { $page_content .= $newContent; }
	else { echo $newContent; }
}

//==================================================
// This function will all the ways to contact a 
// specified user
//
// USAGE:
// contact_methods(username);
//
// This will show all users viewing the same 
// page as you are.
//==================================================
function contact_methods($where, $user) {
	global $DBTABLEPREFIX, $menuvar, $themedir, $page_content, $content;
	$sql = "SELECT * FROM `" . $DBTABLEPREFIX . "users` u WHERE u.users_username='$user' LIMIT 1";
	$result = mysql_query($sql);
	
	if(mysql_num_rows($result) == 0) //if NO results, stop the script & return the error message
	{
		
	} 
	else //if result found, run the rest of the script
	{
		while ( $row = mysql_fetch_array($result) ) {
			$contact = '';			
			
			if($row[users_email_address]){
				$contact .= "<a href='mailto:$row[users_email_address]'><img src='$themedir/buttons/email.jpg' alt='$row[users_email_address]' /></a>";
			}
			if($row[users_aim]){
				$contact .= "<a href='aim:goim?screenname=$row[users_aim]&amp;message=Hi.+Are+you+there?' target='_blank'><img src='$themedir/buttons/aim.jpg' alt='$row[users_aim]' /></a>";
			}
			if($row[users_msn]){
				$contact .= "<a href='http://members.msn.com/$row[users_msn]' target='_blank'><img src='$themedir/buttons/msn.jpg' alt='$row[users_msn]' /></a>";
			}
			if($row[users_yim]){
				$contact .= "<a href='http://edit.yahoo.com/config/send_webmesg?.target=$row[users_yim]' target='_blank'><img src='$themedir/buttons/yim.jpg' alt='$row[users_yim]' /></a>";
			}
			if($row[users_icq]){
				$contact .= "<a href='http://web.icq.com/whitepages/about_me/1,,,00.html?Uin=$row[users_icq]' target='_blank'><img src='$themedir/buttons/icq.jpg' alt='$row[users_icq]' /></a>";
			}		
			$newcontent .= "\n<a href='$menuvar[PRIVMSGS]&action=compose&to=$row[users_username]'><img src='$themedir/buttons/pm.jpg'></a>$contact";
		}
	}
	
	if ($where == "content") { $content .= $newContent; }
	elseif ($where == "pageContent") { $page_content .= $newContent; }
	else { echo $newContent; }
}

//============================================
// This function is designed to let us show 
// whos viewing the same page we call this 
// function from, it was going to be in the 
// usersonline.php file, but the n it will be 
// below the actual topics because of the 
// fast reply form
//
// USAGE:
// users_viewing_page($PHP_SELF);
//
// This will show all users viewing the same 
// page as you are.
//============================================
function users_viewing_page($thispage) {
	global $DBTABLEPREFIX, $menuvar, $page_content;

	//==========================================================
	// Find total amount of users viewing this page	
	//==========================================================
	$sql = "SELECT * FROM `" . $DBTABLEPREFIX . "usersonline` WHERE uo_file = '$thispage'";
	$result = mysql_query($sql) or die (mysql_error()."<br />Couldn't execute query: $sql");
	
	if(mysql_num_rows($result) == 0) //if NO results, stop the script & return the error message
	{
		$totalonline = 0;
	} 
	else //if result found, run the rest of the script
	{
		$totalonline = mysql_num_rows($result);
	}
	mysql_free_result($result); //free our query
			
	//==========================================================
	// Find out whos online and viewing this page and if 
	// they're a user make a link to their profile page	
	//==========================================================
	$sql = "SELECT * FROM `" . $DBTABLEPREFIX . "usersonline` WHERE uo_username != 'Guest' AND uo_file = '$thispage'";
	$result = mysql_query($sql) or die (mysql_error()."<br />Couldn't execute query: $sql");
	
	$totalguests = $totalonline - mysql_num_rows($result);
	$totalusers = $totalonline - $totalguests;
				
	if(mysql_num_rows($result) == 0) //if NO results, stop the script & return the error message
	{
		$users = "No registered users logged in."; //add username to our list ^^
	} 
	else //if result found, run the rest of the script
	{
		while ( $row = mysql_fetch_array($result) ) {
			$sql2 = "SELECT * FROM `" . $DBTABLEPREFIX . "users` WHERE users_username = '$row[uo_username]'";
			$result2 = mysql_query($sql2) or die (mysql_error()."<br />Couldn't execute query: $sql");
			
			if(mysql_num_rows($result2) == 0) //if NO results, stop the script & return the error message
			{
				$users = "No registered users logged in."; //add username to our list ^^
			} 
			else //if result found, run the rest of the script
			{
				while ( $row2 = mysql_fetch_array($result2) ) {
					$users .= "<a href='$menuvar[PROFILE]&action=viewprofile&id=" . $row2['users_id'] . "'>$row2[users_username]</a> ";
				}
					
			}
			mysql_free_result($result2); //free our query
		}
	
	}
	mysql_free_result($result); //free our query
		
	//==========================================================
	// Print out our nice little div	
	//==========================================================
	$page_content .= "
		<center>
		<div class='UOnlineForumBorder'>
			<h3>
				<div style=\"float: right;\"><a href=\"javascript:sqr_show_hide('viewingDrop');\"><img src=\"images/plus.png\" style=\"width: 15px; height: 15px; border:0px;\" alt=\"Show/hide users viewing stats\" /></a></div>
				$totalusers Users, $totalguests Guests Are Viewing This Page:
			</h3>
			<p id=\"viewingDrop\">
			$users
			</p>
		</div>
		</center>
		<br /><br />";
}

//============================================
// This function is designed to keep a user's
// $_SESSION items up to date
//
// USAGE:
// users_viewing_page($PHP_SELF);
//
// This will create all the needed session 
// variables.
//============================================
function get_user_session_info($currentuserid) {
	global $DBTABLEPREFIX;

	$sql = "SELECT * FROM `" . $DBTABLEPREFIX . "users` WHERE users_id = '$currentuserid' LIMIT 1";
	$result = mysql_query($sql);
	
	if(mysql_num_rows($result) == 0) //if NO results, stop the script & return the error message
	{
		echo "\nCould not find user in database. get_user_session_info() section 1.";
	} 
	else //if result found, run the rest of the script
	{
		$row = mysql_fetch_array($result);
		extract($row);
		
		// Register session variables
		$_SESSION[STATUS] = "true";
		$_SESSION['username'] = $users_username;
		$_SESSION['epassword'] = $users_password;
		$_SESSION['last_login'] = $users_last_login;
		$_SESSION['session_avatar'] = $users_avatar;
		$_SESSION['first_name'] = $users_first_name;
		$_SESSION['last_name'] = $users_last_name;
		$_SESSION['email_address'] = $users_email_address;
		$_SESSION['user_level'] = $users_user_level;
		$_SESSION['country'] = $users_country;
		$_SESSION['info'] = $users_info;
		$_SESSION['gender'] = $users_gender;		
	}
	mysql_free_result($result); //free our query		
}

//===================================================
// This function is designed to get the directory 
// for the current style.
//
// USAGE:
// $currenttheme = get_theme_dir($_SESSION[username]);
//
// This will return the theme dir.
//===================================================
function get_theme_dir($currentuser) {
	global $DBTABLEPREFIX;
	if (isset($currentuser) && $currentuser != " ") {
						
		$sql = "SELECT users_style FROM `" . $DBTABLEPREFIX . "users` WHERE users_username='$currentuser' ";
		$result = mysql_query($sql);
		
		if($result && mysql_num_rows($result) > 0) {
   			$row = mysql_fetch_array($result);
   			return "themes/$row[users_style]";
   			
   		}
   		else { return "themes/$safs_config[ftssafs_theme]"; }
	}
	else {
			return "themes/$safs_config[ftssafs_theme]";
	}
}

//===================================================
// This function is designed to print out the last 
// post made to a topic or forum.
//
// USAGE:
// get_last_post('forum', $forum_id);
// get_last_post('topic', $topic_id);
//
// This will echo the last post info.
//===================================================
function get_last_post($where, $type, $id) {
	global $themedir, $menuvar, $DBTABLEPREFIX, $page_content, $content;
	
	if ($type == "forum") {
		$sql3 = "SELECT p.post_time, p.post_username, p.post_poster_id, p.post_id, p.post_topic_id FROM `" . $DBTABLEPREFIX . "topics` t, `" . $DBTABLEPREFIX . "posts` p WHERE t.topic_id = p.post_topic_id AND topic_forum_id = '$id' ORDER BY post_time DESC LIMIT 1"; //gets the forum info
		$result3 = mysql_query($sql3);
			
		while ( $row3 = mysql_fetch_array($result3) ) {
			$time = make_lastpost_date($row3[post_time]);
			$newContent .= "<a href='$menuvar[VIEWTOPIC]&id=$row3[post_topic_id]#$row3[post_id]'><img src='$themedir/buttons/lastpost.gif' /></a> <b>By:</b> <a href='$menuvar[PROFILE]?action=viewprofile&id=$row3[post_poster_id]'>$row3[post_username]</a><br /> <b>On:</b> $time";
		}
		mysql_free_result($result3); //free our query	
	}
	else {
		$sql3 = "SELECT post_time, post_username, post_poster_id, post_id, post_topic_id FROM `" . $DBTABLEPREFIX . "posts` WHERE post_topic_id = '$id' ORDER BY post_time DESC LIMIT 1"; //gets the forum info
		$result3 = mysql_query($sql3);
		
		while ( $row3 = mysql_fetch_array($result3) ) {
			$time = make_lastpost_date($row3[post_time]);
			$newContent .= "<a href='$menuvar[VIEWTOPIC]&id=$row3[post_topic_id]#$row3[post_id]'><img src='$themedir/buttons/lastpost.gif' /></a> <b>By:</b> <a href='$menuvar[PROFILE]?action=viewprofile&id=$row3[post_poster_id]'>$row3[post_username]</a><br /> <b>On:</b> $time";
		}
		mysql_free_result($result3); //free our query	
	}
	if ($where == "content") { $content .= $newContent; }
	elseif ($where == "pageContent") { $page_content .= $newContent; }
	else { echo $newContent; }
}


//===================================================
// This function is used to print the move topic 
// to ___ drop down box
//===================================================		
function forumOptions($catid, $parent=0, $level=0) {
	global $DBTABLEPREFIX;
	$sql2 = "SELECT forum_id, forum_name FROM `" . $DBTABLEPREFIX . "forums` WHERE forum_subforum = '$parent' AND forum_cat_id = '$catid' ORDER BY forum_order";
	$result2 = mysql_query($sql2) or die(mysql_error());
	$str = '';
	while (list ($fid, $fname) = mysql_fetch_row($result2)) {
		 $indent = str_repeat('&nbsp;&nbsp;', $level+1); 
		 $str .= "<option value='$fid'>$indent&#0124;-- $fname</option>\n";
		 $str .= forumOptions($catid, $fid, $level+1);
	}
	mysql_free_result($sql2);
	return $str;
}

//===================================================
// This function is used to print out subforums
//===================================================	
function subforumList($catid, $parent=0, $level=0, $recall=0) {
	global $DBTABLEPREFIX;
	$firstRowWidth = 0;
	$secondRowWidth = 720;
	$sql2 = "SELECT * FROM `" . $DBTABLEPREFIX . "forums` WHERE forum_subforum = '$parent' AND forum_cat_id = '$catid' ORDER BY forum_order";
	$result2 = mysql_query($sql2) or die(mysql_error());
	
	if (mysql_num_rows($result2) != 0) {
	
		while ($row3 = mysql_fetch_array($result2)) {
			if ($recall == 1) {
			for ($x=0; $x<($level+1); $x++) {
				$firstRowWidth += 10;
				$secondRowWidth -= 10; 
			}
			}
			else {
				$firstRowWidth = 10;
				$secondRowWidth = 710; 
			}
			$str .= "\n  <tr class=\"row1\" id=\"" . $row3['forum_id'] . "SubForumRow\">
								<td width=\"$secondRowWidth\" style=\"padding-left: " . $firstRowWidth . "px;\"><b>$row3[forum_name]</b><br />$row3[forum_desc]</td>
								<td width=\"80\" align=\"center\"><a href=\"index.php?p=admin&s=forums&action=moveforum&id=$row3[forum_id]&catid=$row3[forum_cat_id]&dir=down\"><img src=\"images/downarrow.png\" alt=\"\" style=\"width: 12px; height: 12px;\" /></a><a href=\"index.php?p=admin&s=forums&action=moveforum&id=$row3[forum_id]&catid=$row3[forum_cat_id]&dir=up\"><img src=\"images/uparrow.png\" alt=\"\" style=\"width: 12px; height: 12px;\" /></a><a href=\"index.php?p=admin&s=forums&action=editforum&id=$row3[forum_id]\"><img src=\"images/check.png\" alt=\"Edit\" style=\"width: 15px; height: 15px; border: 0px;\" /></a><a style=\"cursor: pointer; cursor: hand;\" onclick=\"ajaxDeleteNotifier('" . $row3['forum_id'] . "SubForumSpinner', 'ajax.php?action=deleteforum&id=" . $row3['forum_id'] . "', 'forum', '" . $row3['forum_id'] . "SubForumRow');\"><img src=\"images/x.png\" alt=\"Delete\" style=\"width: 15px; height: 15px; border: 0px;\" /><div id=\"" . $row3['forum_id'] . "SubForumSpinner\" style=\"display: none;\"><img src=\"images/indicator.gif\" alt=\"spinner\" /></div></a></td>
							</tr>";
			$str .= subforumList($row3[forum_cat_id], $row3[forum_id], $level+1, 1);
		}
	}
	mysql_free_result($sql2);
	return $str;
}	

//=========================================================
// Check if this item should be selected
//=========================================================
function testSelected($testFor, $testAgainst) {
	if ($testFor == $testAgainst) { return " selected=\"selected\""; }
}

//=========================================================
// Check if this item should be checked
//=========================================================
function testChecked($testFor, $testAgainst) {
	if ($testFor == $testAgainst) { return " checked=\"schecked\""; }
}

//=========================================================
// Case insensitive str_replace
//=========================================================
if(!function_exists('str_ireplace')){
   function str_ireplace($search, $replace, $subject){
       if(is_array($search)){
           array_walk($search, 'make_pattern');
       }
       else{
           $search = '/'.preg_quote($search, '/').'/i';
       }
       return preg_replace($search, $replace, $subject);
   }
} 

//==================================================
// This function will notify user of updates and
// other important information
//
// USAGE:
// version_functions();
// 
// Removal or hinderance is a direct violation of 
// the program license and is constituted as a 
// breach of contract as is punishable by law.
//
	// MODIFIED TO REMOVE CALLHOME AND VERSION CHECK
	//==================================================
	function version_functions($print_update_info) {
		include('_license.php');
		
		//=========================================================
		// Get all of the variables we need to pass to the 
		// call home script ready
		//=========================================================
		
			
		//=========================================================
		// Should we display advanced option?
		// Connection to the FTS server has to be made or the 
		// options will not be shown
		//=========================================================
		if ($print_update_info == "advancedOptions" || $print_update_info == "advancedOptionsText") {
			return true;
		}
			
		//=========================================================
		// Should we print out wether or not to update?
		//=========================================================
		if ($print_update_info == "yes") {
			//return "<div class=\"errorMessage\">Version check connection failed.</div>";
		}
	}

?>