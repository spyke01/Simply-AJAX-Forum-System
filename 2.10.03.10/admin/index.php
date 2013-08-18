<? 
/***************************************************************************
 *                               index.php
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

if($_SESSION['user_level'] == ADMIN || $_SESSION['user_level'] == MOD) {
		if ($_GET[action] == "moveforum") {
			$sql = "SELECT forum_order FROM `" . $DBTABLEPREFIX . "forums` WHERE forum_id='$_GET[id]' LIMIT 1";
			$result = mysql_query($sql);
			
			if ($row = mysql_fetch_array($result)) { $current_order_num = $row[forum_order]; }
			mysql_free_result($result);
			
			if ($_GET[dir] == "up") { $target_order_num = ($current_order_num > 0) ? $current_order_num - 1 : 1; }
			else { $target_order_num = $current_order_num + 1; }
			
			$sql = "UPDATE `" . $DBTABLEPREFIX . "forums` SET forum_order='999' WHERE forum_id='$_GET[id]'";
			$result = mysql_query($sql);
				
			$sql = "UPDATE `" . $DBTABLEPREFIX . "forums` SET forum_order='$current_order_num' WHERE (forum_order='$target_order_num' AND forum_cat_id='$_GET[catid]')";
			$result = mysql_query($sql);
				
			$sql = "UPDATE `" . $DBTABLEPREFIX . "forums` SET forum_order='$target_order_num' WHERE forum_id='$_GET[id]'";
			$result = mysql_query($sql);
		}
		
		elseif ($_GET[action] == "movecat") {
			$sql = "SELECT cat_order FROM `" . $DBTABLEPREFIX . "categories` WHERE cat_id='$_GET[id]' LIMIT 1";
			$result = mysql_query($sql);
			
			if ($row = mysql_fetch_array($result)) { $current_order_num = $row[cat_order]; }
			mysql_free_result($result);
						
			if ($_GET[dir] == "up") { $target_order_num = ($current_order_num > 0) ? $current_order_num - 1 : 1; }
			else { $target_order_num = $current_order_num + 1; }
			
			//$page_content .= "Cat ID: $_GET[id]<br />Direction: $_GET[dir]<br />current: $current_order_num<br /> Target: $target_order_num<br />";
			
			$sql = "UPDATE `" . $DBTABLEPREFIX . "categories` SET cat_order='999' WHERE cat_id='$_GET[id]'";
			$result = mysql_query($sql);
				
			$sql = "UPDATE `" . $DBTABLEPREFIX . "categories` SET cat_order='$current_order_num' WHERE cat_order='$target_order_num'";
			$result = mysql_query($sql);
				
			$sql = "UPDATE `" . $DBTABLEPREFIX . "categories` SET cat_order='$target_order_num' WHERE cat_id='$_GET[id]'";
			$result = mysql_query($sql);
		}
		
		//===========================================
		// Add Delete And Edit Categories Functions
		// All Functions for categories are below!
		//===========================================
		if ($action =='addcat') { 
			if(isset($_POST['title']))
			{
				$ctitle = $_POST['title'];
				$corder = $_POST['order'];

				
				$sql = "INSERT INTO `" . $DBTABLEPREFIX . "categories` (cat_title) VALUES('$ctitle')";
			    mysql_query($sql) or die('<br />Error, insert query failed' . $sql);
			
			    //confirm
 				$page_content .= "Your category has been added, and you are being redirected to the main index. 
 									<meta http-equiv='refresh' content='1;url=index.php?p=admin&s=forums'>";
			}
			else{
					$page_content .= "\n<center>
										<form action=\"index.php?p=admin&s=forums&action=addcat&id=$id\" method=\"post\">
										<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"forumborder\">
										<tr><td>
										<table border=\"0\" cellpadding=\"1\" cellspacing=\"1\" width=\"100%\">
										  <tr class=\"title1\">
										    <td width=\"100%\" colspan=\"2\"><strong>New Category</strong></td>	
										  </tr>
										  <tr class=\"title2\">
										    <td colspan=\"2\"></td>
										  </tr>
										  <tr class=\"row1\">
										    <td><strong>Cat Title: </strong></td><td><input type=\"text\" name=\"title\" size=\"73\"></td>
										  </tr>
										  <tr class=\"title1\">
										    <td  colspan=\"2\"><center><input type=\"submit\" name=\"submit\" value=\"Submit\"></center></td>
										  </tr>	
										</table>
										</table>
										</form>
										</center>
										<br /><br />";

			}			
			unset($_POST['title']);	
		}
		
		
		elseif ($action =='editcat' && isset($id)) { 
			if(isset($_POST['title']))
			{
				$ctitle = $_POST['title'];
				
				$sql = "UPDATE `" . $DBTABLEPREFIX . "categories` SET cat_title='$ctitle' WHERE cat_id='$id'";
			    mysql_query($sql) or die('<br />Error, insert query failed' . $sql);
			
			    //confirm
 				$page_content .= "Your category has been edited, and you are being redirected to the main index.
 									<meta http-equiv='refresh' content='1;url=index.php?p=admin&s=forums'>";
			}
			else{
				$sql = "SELECT * FROM `" . $DBTABLEPREFIX . "categories` WHERE cat_id=$id";
				$result = mysql_query($sql);
					
				if($result && mysql_num_rows($result) > 0) {
					$row = mysql_fetch_array($result);
					
					$page_content .= "\n<center>
										<form action=\"index.php?p=admin&s=forums&action=editcat&id=$id\" method=\"post\">
										<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"forumborder\">
										<tr><td>
										<table border=\"0\" cellpadding=\"1\" cellspacing=\"1\" width=\"100%\">
										  <tr class=\"title1\">
										    <td width=\"100%\" colspan=\"2\"><strong>New Category</strong></td>	
										  </tr>
										  <tr class=\"title2\">
										    <td colspan=\"2\"></td>
										  </tr>
										  <tr class=\"row1\">
										    <td><strong>Cat Title: </strong></td><td><input type=\"text\" name=\"title\" size=\"73\" value=\"" . $row['cat_title'] . "\"></td>
										  </tr>
										  <tr class=\"title1\">
										    <td  colspan=\"2\"><center><input type=\"submit\" name=\"submit\" value=\"Submit\"></center></td>
										  </tr>	
										</table>
										</table>
										</form>
										</center>
										<br /><br />";
				}
				else { $page_content .= "No such ID was found in the database!"; }
			}			
			unset($_POST['title']);	
		}
		
				
		
		//=======================================
		// Add Delete And Edit Forum Functions
		// All Functions for forums are below!
		//=======================================
		elseif ($action =='addforum') { 
			if(isset($_POST['name']))
			{
				$fname = $_POST['name'];
				$fcatid = $_POST['cat'];
				$fsubforum = $_POST['subforum'];
				$fdesc = $_POST['desc'];
				$ftopics = $_POST['topics'];
				$fposts = $_POST['posts'];
				
				$sql = "INSERT INTO `" . $DBTABLEPREFIX . "forums` (forum_cat_id, forum_name, forum_desc, forum_posts, forum_topics, forum_subforum)".
			    "VALUES('$fcatid', '$fname', '$fdesc', '$fposts', '$ftopics', '$fsubforum')";
			    mysql_query($sql) or die('<br />Error, insert query failed' . $sql);
			
			    //confirm
 				$page_content .= "Your forum has been added, and you are being redirected to the main index.
 									<meta http-equiv='refresh' content='1;url=index.php?p=admin&s=forums'>";
			}
			else{
					$page_content .= "\n<center>
										<form action=\"index.php?p=admin&s=forums&action=addforum\" method=\"post\">
										<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"forumborder\">
										<tr><td>
										<table border=\"0\" cellpadding=\"1\" cellspacing=\"1\" width=\"100%\">
										  <tr class=\"title1\">
										    <td width=\"100%\" colspan=\"2\"><strong>New Forum</strong></td>	
										  </tr>
										  <tr class=\"title2\">
										    <td colspan=\"2\"></td>
										  </tr>
										  <tr class=\"row1\">
										    <td><strong>Forum Name: </strong></td><td><input type=\"text\" name=\"name\" size=\"73\"></td>
										  </tr>
										  <tr class=\"row1\">
										    <td><strong>Category: </strong></td><td>";
							//=============================================================
							// Create a box with our list of categories			
							//=============================================================
							$page_content .= "\n<select name='cat'>";
							$sql2 = "SELECT cat_id, cat_title, cat_order FROM `" . $DBTABLEPREFIX . "categories` ORDER BY cat_order"; //gets categories
							$result2 = mysql_query($sql2);
					
							while ( $row2 = mysql_fetch_array($result2) )
							{
								extract($row2); //so we dont have to do long array variables
								
								$page_content .= "\n<option value='$cat_id'>$cat_title</option>";

							}
							mysql_free_result($result2);			
							$page_content .= "\n</select>
												    </td>
												  </tr>
												  <tr class=\"row1\">
												    <td><strong>Subforum of: </strong></td><td>"; 
							//=============================================================
							// Create a box with our list of forums/subforums			
							//=============================================================
							$page_content .= "\n<select name='subforum'>
												<option>--CHOOSE--</option>";
												
							$sql = "SELECT cat_id, cat_title FROM `" . $DBTABLEPREFIX . "categories` ORDER BY cat_order";
							$result = mysql_query($sql) or die(mysql_error());
							
							while (list($cid, $cname) = mysql_fetch_row($result)) {
								$page_content .= "<optgroup label='$cname'>" . forumOptions($cid) . "</optgroup>\n";
							}
							mysql_free_result($sql);		
							$page_content .= "\n</select>
										    </td>
										  </tr>
										  <tr class=\"row1\">
										    <td><strong>Forum Description: </strong></td><td><textarea cols=\"70\" rows=\"8\" name=\"desc\" class=\"textinput\" ></textarea><br />
											</center></td></tr>
										  <tr class=\"title2\">
										    <td colspan=\"2\"></td>
										  </tr>
										  <tr class=\"row1\">
										    <td><strong>Topics: </strong></td><td><input type=\"text\" name=\"topics\" size=\"73\"></td>
										  </tr>
										  <tr class=\"row1\">
										    <td><strong>Posts: </strong></td><td><input type=\"text\" name=\"posts\" size=\"73\"></td>
										  </tr>
										  <tr class=\"title1\">
										    <td  colspan=\"2\"><center><input type=\"submit\" name=\"submit\" value=\"Submit\"></center></td>
										  </tr>	
										</table>
										</table>
										</form>
										</center>
										<br /><br />";

			}			
			unset($_POST['name']);	
		}
		
		elseif ($action =='editforum' && isset($id)) { 
			if(isset($_POST['name']))
			{
				$fname = $_POST['name'];
				$fcatid = $_POST['cat'];
				$fsubforum = $_POST['subforum'];
				$fdesc = $_POST['desc'];
				$ftopics = $_POST['topics'];
				$fposts = $_POST['posts'];
				
				$sql = "UPDATE `" . $DBTABLEPREFIX . "forums` SET forum_cat_id='$fcatid', forum_name='$fname', forum_desc='$fdesc', forum_posts='$fposts', forum_topics='$ftopics', forum_subforum='$fsubforum' WHERE forum_id='$id'";
				mysql_query($sql) or die('Error, updatepost query failed');
				//confirm
 				$page_content .= "Your forum has been edited, and you are being redirected to the main index.
 									<meta http-equiv='refresh' content='1;url=index.php?p=admin&s=forums'>";
			}
			else{
				$sql = "SELECT * FROM `" . $DBTABLEPREFIX . "forums` WHERE forum_id=$id";
				$result = mysql_query($sql);
					
				if($result && mysql_num_rows($result) > 0) {
					$row = mysql_fetch_array($result);
					$currentcat = $row['forum_cat_id'];
					$currentsforum = $row['forum_subforum'];
				   
					$page_content .= "\n<center>
										<form action=\"index.php?p=admin&s=forums&action=editforum&id=$id\" method=\"post\">
										<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"forumborder\">
										<tr><td>
										<table border=\"0\" cellpadding=\"1\" cellspacing=\"1\" width=\"100%\">
										  <tr class=\"title1\">
										    <td width=\"100%\" colspan=\"2\"><strong>New Topic</strong></td>	
										  </tr>
										  <tr class=\"title2\">
										    <td colspan=\"2\"></td>
										  </tr>
										  <tr class=\"row1\">
										    <td><strong>Forum Name: </strong></td><td><input type=\"text\" name=\"name\" size=\"73\" value=\"" . $row['forum_name'] ."\"></td>
										  </tr>
										  <tr class=\"row1\">
										    <td><strong>Category: </strong></td><td>";
							//=============================================================
							// Create a box with our list of categories			
							//=============================================================
							$page_content .= "\n<select name='cat'>";
							$sql2 = "SELECT cat_id, cat_title, cat_order FROM `" . $DBTABLEPREFIX . "categories` ORDER BY cat_order"; //gets categories
							$result2 = mysql_query($sql2);
					
							while ( $row2 = mysql_fetch_array($result2) )
							{
								extract($row2); //so we dont have to do long array variables
								
								if ($cat_id == $currentcat) {
									$page_content .= "\n<option value='$cat_id' selected>$cat_title</option>";
								}
								else {
									$page_content .= "\n<option value='$cat_id'>$cat_title</option>";
								}
								

							}
							mysql_free_result($result2);			
							$page_content .= "\n</select>
												    </td>					
												  </tr>
												  <tr class=\"row1\">
												    <td><strong>Subforum of: </strong></td><td>"; 
							//=============================================================
							// Create a box with our list of forums/subforums			
							//=============================================================
							$page_content .= "\n<select name='subforum'>
												<option>--CHOOSE--</option>";
												
							$sql = "SELECT cat_id, cat_title FROM `" . $DBTABLEPREFIX . "categories` ORDER BY cat_order";
							$result = mysql_query($sql) or die(mysql_error());
							
							while (list($cid, $cname) = mysql_fetch_row($result)) {
								$page_content .= "\n<optgroup label='$cname'>" . forumOptions($cid) . "</optgroup>";
							}
							mysql_free_result($sql);		
					$page_content .= "\n</select>
										    </td>					
										  </tr>
										  <tr class=\"row1\">
										    <td><strong>Forum Description: </strong></td><td><textarea cols=\"70\" rows=\"8\" name=\"desc\" class=\"textinput\" >" . $row['forum_desc'] ."</textarea><br />
											</center></td></tr>
										  <tr class=\"title2\">
										    <td colspan=\"2\"></td>
										  </tr>
										  <tr class=\"row1\">
										    <td><strong>Topics: </strong></td><td><input type=\"text\" name=\"topics\" size=\"73\" value=\"" . $row['forum_topics'] ."\"></td>
										  </tr>
										  <tr class=\"row1\">
										    <td><strong>Posts: </strong></td><td><input type=\"text\" name=\"posts\" size=\"73\" value=\"" . $row['forum_posts'] ."\"></td>
										  </tr>
										  <tr class=\"title1\">
										    <td  colspan=\"2\"><center><input type=\"submit\" name=\"submit\" value=\"Submit\"></center></td>
										  </tr>	
										</table>
										</table>
										</form>
										</center>
										<br /><br />";
				}
				else { $page_content .= "No such ID was found in the database!"; }
			}			
			unset($_POST['name']);	
		}

		
		//=======================================
		// No functions were found so time to 
		// list all items below
		//=======================================		
		else {
			$page_content .= "\n<center>" . version_functions('yes') . "<a href='index.php?p=admin&s=forums&action=addforum'>Add a Forum</a> &nbsp;|&nbsp; <a href='index.php?p=admin&s=forums&action=addcat'>Add a Category</a><br />";
								
			$sql = "SELECT cat_id, cat_title, cat_order FROM `" . $DBTABLEPREFIX . "categories` ORDER BY cat_order"; //gets categories
			$result = mysql_query($sql);
	
			while ( $row = mysql_fetch_array($result) )
			{
				extract($row); //so we dont have to do long array variables
				$catid1 = $cat_id; //so we can check it against the forum cat_id's
		
				$page_content .= "\n<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"forumborder\" id=\"" . $cat_id . "CatRow\">
									<tr><td>
									<table border=\"0\" cellpadding=\"1\" cellspacing=\"1\" width=\"100%\">
									  <tr class=\"title1\">
									    <td width=\"520\"><strong>$cat_title</strong></td>
									    <td width=\"80\" align=\"center\"><a href=\"index.php?p=admin&s=forums&action=movecat&id=$cat_id&dir=down\"><img src=\"images/downarrow.png\"alt=\"\" /></a><a href=\"index.php?p=admin&s=forums&action=movecat&id=$cat_id&dir=up\"><img src=\"images/uparrow.png\" alt=\"\" /></a><a href=\"index.php?p=admin&s=forums&action=editcat&id=$cat_id\"><img src=\"images/check.png\" alt=\"Edit\" /></a><a style=\"cursor: pointer; cursor: hand;\" onclick=\"ajaxDeleteNotifier('" . $cat_id . "CatSpinner', 'ajax.php?action=deletecat&id=" . $cat_id . "', 'category', '" . $cat_id . "CatRow');\"><img src=\"images/x.png\" alt=\"Delete\" /></a><div id=\"" . $cat_id . "CatSpinner\" style=\"display: none;\"><img src=\"images/indicator.gif\" alt=\"spinner\" /></div></td>
									  </tr>
									  <tr class=\"title2\">
									    <td width=\"520\"><strong>Forum</strong></td>
									    <td width=\"80\" align=\"center\"></td>
									  </tr>";
	
				$sql2 = "SELECT * FROM `" . $DBTABLEPREFIX . "forums` WHERE forum_subforum = '0' AND forum_cat_id = '$cat_id' ORDER BY forum_order"; //gets the forum info
				$result2 = mysql_query($sql2);
		
				while ( $row2 = mysql_fetch_array($result2) )
				{
					extract($row2); //so we dont have to do long array variables

					$page_content .= "\n  <tr class=\"row1\" id=\"" . $forum_id . "ForumRow\">
										  <td width=\"520\"><strong>$forum_name</strong><br />$forum_desc</td>
										  <td width=\"80\" align=\"center\"><a href=\"index.php?p=admin&s=forums&action=moveforum&id=$forum_id&catid=$forum_cat_id&dir=down\"><img src=\"images/downarrow.png\" alt=\"\" /></a><a href=\"index.php?p=admin&s=forums&action=moveforum&id=$forum_id&catid=$forum_cat_id&dir=up\"><img src=\"images/uparrow.png\" alt=\"\" /></a><a href=\"index.php?p=admin&s=forums&action=editforum&id=$forum_id\"><img src=\"images/check.png\" alt=\"Edit\" /></a><a style=\"cursor: pointer; cursor: hand;\" onclick=\"ajaxDeleteNotifier('" . $forum_id . "ForumSpinner', 'ajax.php?action=deleteforum&id=" . $forum_id . "', 'forum', '" . $forum_id . "ForumRow');\"><img src=\"images/x.png\" alt=\"Delete\" /></a><div id=\"" . $row3['forum_id'] . "ForumSpinner\" style=\"display: none;\"><img src=\"images/indicator.gif\" alt=\"spinner\" /></div></td>
										</tr>";		
							
					$page_content .= subforumList($cat_id, $forum_id, 0);

			
				}
				mysql_free_result($result2); //free our query
		
				$page_content .= "\n  
									</table>
									</table>
									<br /><br />";
			}
			mysql_free_result($result);
			$page_content .= "\n</center>";
		}
}

$page->setTemplateVar("PageContent", $page_content);
?>