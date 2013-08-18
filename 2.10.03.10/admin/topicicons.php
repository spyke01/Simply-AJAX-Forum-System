<? 
/***************************************************************************
 *                               topicicons.php
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

		if ($action =='addtopicicon') { 
			if(isset($_POST['name']))
			{
				$pname = $_POST['name'];
				$pimageurl = $_POST['imageurl'];
				
				$sql = "INSERT INTO `" . $DBTABLEPREFIX . "topicicons` (topicicons_name, topicicons_image)".
			    "VALUES('$pname', '$pimageurl')";
			    mysql_query($sql) or die('<br />Error, insert query failed' . $sql);
			
			    //confirm
 				$page_content .= "Your topic icon has been added, and you are being redirected to the main index. 
 									<meta http-equiv='refresh' content='1;url=index.php?p=admin&s=topicicons'>";
			}
			else{
				$page_content .= "\n<center>
									<form action=\"index.php?p=admin&s=topicicons&action=addtopicicon\" method=\"post\">
									<table class=\"forumborder\" border=\"0\" cellpadding=\"0\" cellspacing=\"1\" width=\"160\">
									  <tr class=\"title1\">
									    <td colspan=\"2\"><strong>Add a Topic Icon</strong></td>
									  </tr>
									  <tr class=\"title2\">
									    <td width=\"80\">Name</td>
									    <td width=\"80\" align=\"center\">Image</td>
									  </tr>
									  <tr class=\"row1\">
									    <td width=\"80\"><input type=\"text\" name=\"name\" size=\"50\"></td>
									    <td width=\"80\" align=\"center\"><input type=\"text\" name=\"imageurl\" size=\"50\" value=\"$board_config[ftssafs_topic_icons_path]/\"></td>
									  </tr>
									  <tr class=\"title2\">
									    <td colspan=\"2\"></td>
									  </tr>
									  <tr class=\"title1\">
									    <td  colspan=\"2\"><center><input type=\"submit\" name=\"submit\" value=\"Submit\"></center></td>
									  </tr>	
									</table>
									</form>
									<br><br>
									</center>";

			}			
			unset($_POST['name']);	
		}
		
		
		elseif ($action =='edittopicicon' && isset($id)) { 
			if(isset($_POST['name']))
			{
				$pname = $_POST['name'];
				$pimageurl = $_POST['imageurl'];
				
				$sql = "UPDATE `" . $DBTABLEPREFIX . "topicicons` SET topicicons_name='$pname', topicicons_image='$pimageurl' WHERE topicicons_id='$id'";
			    mysql_query($sql) or die('<br />Error, insert query failed' . $sql);
			
			    //confirm
 				$page_content .= "Your topic icon has been edited, and you are being redirected to the main index.
 									<meta http-equiv='refresh' content='1;url=index.php?p=admin&s=topicicons'>";
			}
			else{
				$sql = "SELECT * FROM `" . $DBTABLEPREFIX . "topicicons` WHERE topicicons_id=$id";
				$result = mysql_query($sql);
					
				if($result && mysql_num_rows($result) > 0) {
					$row = mysql_fetch_array($result);
					
					$page_content .= "\n<center>
										<form action=\"index.php?p=admin&s=topicicons&action=edittopicicon&id=$id\" method=\"post\">
										<table class=\"forumborder\" border=\"0\" cellpadding=\"0\" cellspacing=\"1\" width=\"160\">
										  <tr class=\"title1\">
										    <td colspan=\"2\"><strong>Edit Topic Icon</strong></td>
										  </tr>
										  <tr class=\"title2\">
										    <td width=\"80\">Name</td>
										    <td width=\"80\" align=\"center\">Image</td>
										  </tr>
										  <tr class=\"row1\">
										    <td width=\"80\"><input type=\"text\" name=\"name\" size=\"40\" value=\"$row[topicicons_name]\"></td>
										    <td width=\"80\" align=\"center\"><input type=\"text\" name=\"imageurl\" size=\"40\" value=\"$row[topicicons_image]\"></td>
										  </tr>
										  <tr class=\"title2\">
										    <td colspan=\"2\"></td>
										  </tr>
										  <tr class=\"title1\">
										    <td  colspan=\"2\"><center><input type=\"submit\" name=\"submit\" value=\"Submit\"></center></td>
										  </tr>	
										</table>
										</form>
										<br><br>
										</center>";
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
			$page_content .= "\n<center>
								<table class=\"forumborder\" border=\"0\" cellpadding=\"0\" cellspacing=\"1\" style=\"width: 240px;\">
								<tr class=\"title1\">
								  <td colspan=\"2\" align=\"left\"><strong>Current Topic Icons</strong></td><td align=\"center\"><a href=\"index.php?p=admin&s=topicicons&action=addtopicicon\"><img src=\"images/plus.png\" alt=\"Add a topic icon\" /></a></td>
								</tr>
								<tr class=\"title2\">
								  <td width=\"80\" align=\"center\">Name</td>
								  <td width=\"80\" align=\"center\">Images</td>
								  <td width=\"80\" align=\"center\">&nbsp;</td>
								</tr>";
												
			$sql = "SELECT * FROM `" . $DBTABLEPREFIX . "topicicons` ORDER BY topicicons_id"; //gets Topic Icons
			$result = mysql_query($sql);
	
			while ( $row = mysql_fetch_array($result) )
			{
				extract($row); //so we dont have to do long array variables
				$topiciconid1 = $topicicons_id; 
		
				$page_content .= "\n  <tr class=\"row1\" id=\"" . $topicicons_id . "Row\">
									  <td width=\"80\" align=\"center\">$topicicons_name</td>
									  <td width=\"80\" align=\"center\"><img src=\"$topicicons_image\" alt=\"\" /></td>
									  <td width=\"80\" align=\"center\"><a href=\"index.php?p=admin&s=topicicons&action=edittopicicon&id=$topicicons_id\"><img src=\"images/check.png\" alt=\"Edit\" /></a> &nbsp; <a style=\"cursor: pointer; cursor: hand;\" onclick=\"ajaxDeleteNotifier('" . $topicicons_id . "TopicIconsSpinner', 'ajax.php?action=deletetopicicon&id=" . $topicicons_id . "', 'topic icon', '" . $topicicons_id . "Row');\"><img src=\"images/x.png\" alt=\"Delete\" /></a><div id=\"" . $topicicons_id . "TopicIconsSpinner\" style=\"display: none;\"><img src=\"images/indicator.gif\" alt=\"spinner\" /></div></td>
									</tr>";
			}
			mysql_free_result($result);
			
			$page_content .= "\n  
								</table>
								<br><br>
								</center>";
		}
}

$page->setTemplateVar("PageContent", $page_content);
?>