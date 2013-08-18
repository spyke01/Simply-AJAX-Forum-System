<? 
/***************************************************************************
 *                               ranks.php
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
		if ($action =='addrank') { 
			if(isset($_POST['name']))
			{
				$rname = $_POST['name'];
				$rposts = $_POST['posts'];
				$rimage = $_POST['image'];
				
				$sql = "INSERT INTO `" . $DBTABLEPREFIX . "ranks` (rank_name, rank_posts, rank_image)".
			    "VALUES('$rname', '$rposts', '$rimage')";
			    mysql_query($sql) or die('<br />Error, insert query failed' . $sql);
			
			    //confirm
 				$page_content .= "Your rank has been added, and you are being redirected to the main index.
 									<meta http-equiv='refresh' content='1;url=" . $menuvar[EDITRANK] . "'>";
			}
			else{
				$page_content .= "\n<center>
									<form action=\"index.php?p=admin&s=ranks&action=addrank\" method=\"post\">
									<table class=\"forumborder\" border=\"0\" cellpadding=\"0\" cellspacing=\"1\" width=\"600\">
									  <tr class=\"title1\">
									    <td colspan=\"3\"><strong>Add a Rank</strong></td>
									  </tr>
									  <tr class=\"title2\">
									    <td width=\"280\">Rank</td>
									    <td width=\"80\" align=\"center\">Posts</td>
									    <td width=\"240\" align=\"center\">Image</td>
									  </tr>
									  <tr class=\"row1\">
									    <td width=\"280\"><input type=\"text\" name=\"name\" size=\"40\"></td>
									    <td width=\"80\" align=\"center\"><input type=\"text\" name=\"posts\" size=\"40\"></td>
									    <td width=\"240\" align=\"center\"><input type=\"text\" name=\"image\" size=\"40\" value=\"$board_config[ftssafs_ranks_path]/\"></td>
									  </tr>
									  <tr class=\"title2\">
									    <td colspan=\"3\"></td>
									  </tr>
									  <tr class=\"title1\">
									    <td  colspan=\"3\"><center><input type=\"submit\" name=\"submit\" value=\"Submit\"></center></td>
									  </tr>	
									</table>
									</form>
									<br><br>
									</center>";

			}			
			unset($_POST['name']);	
		}
		
		
		elseif ($action =='editrank' && isset($id)) { 
			if(isset($_POST['name']))
			{
				$rname = $_POST['name'];
				$rposts = $_POST['posts'];
				$rimage = $_POST['image'];
				
				$sql = "UPDATE `" . $DBTABLEPREFIX . "ranks` SET rank_name='$rname', rank_posts='$rposts', rank_image='$rimage' WHERE rank_id='$id'";
			    mysql_query($sql) or die('<br />Error, insert query failed' . $sql);
			
			    //confirm
 				$page_content .= "Your rank has been edited, and you are being redirected to the main index.
 									<meta http-equiv='refresh' content='1;url=" . $menuvar[EDITRANK] . "'>";
			}
			else{
				$sql = "SELECT * FROM `" . $DBTABLEPREFIX . "ranks` WHERE rank_id=$id";
				$result = mysql_query($sql);
					
				if($result && mysql_num_rows($result) > 0) {
					$row = mysql_fetch_array($result);
					
					$page_content .= "\n<center>
									<form action=\"index.php?p=admin&s=ranks&action=editrank&id=$id\" method=\"post\">
										<table class=\"forumborder\" border=\"0\" cellpadding=\"0\" cellspacing=\"1\" width=\"600\">
										  <tr class=\"title1\">
										    <td colspan=\"3\"><strong>Edit Rank</strong></td>
										  </tr>
										  <tr class=\"title2\">
										    <td width=\"280\">Rank</td>
										    <td width=\"80\" align=\"center\">Posts</td>
										    <td width=\"240\" align=\"center\">Image</td>
										  </tr>
										  <tr class=\"row1\">
										    <td width=\"280\"><input type=\"text\" name=\"name\" size=\"40\" value=\"" . $row['rank_name'] . "\"></td>
										    <td width=\"80\" align=\"center\"><input type=\"text\" name=\"posts\" size=\"40\" value=\"" . $row['rank_posts'] . "\"></td>
										    <td width=\"240\" align=\"center\"><input type=\"text\" name=\"image\" size=\"40\" value=\"" . $row['rank_image'] . "\"></td>
										  </tr>
										  <tr class=\"title2\">
										    <td colspan=\"3\"></td>
										  </tr>
										  <tr class=\"title1\">
										    <td  colspan=\"3\"><center><input type=\"submit\" name=\"submit\" value=\"Submit\"></center></td>
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
							<table class=\"forumborder\" border=\"0\" cellpadding=\"0\" cellspacing=\"1\" width=\"380\">
								<tr class=\"title1\">
								  <td colspan=\"3\"><strong>Ranks</strong></td><td align=\"center\"><a href=\"index.php?p=admin&s=ranks&action=addrank\"><img src=\"images/plus.png\" alt=\"Add a rank\" /></a></td>
								</tr>
								<tr class=\"title2\">
								  <td width=\"160\">Rank</td>
								  <td width=\"80\" align=\"center\">Posts</td>
								  <td width=\"160\" align=\"center\">Image</td>
								  <td width=\"80\" align=\"center\">&nbsp;</td>
								</tr>";
												
			$sql = "SELECT rank_id, rank_name, rank_posts, rank_image FROM `" . $DBTABLEPREFIX . "ranks` ORDER BY rank_id"; //gets ranks
			$result = mysql_query($sql);
	
			while ( $row = mysql_fetch_array($result) )
			{
				extract($row); //so we dont have to do long array variables
				$rankid1 = $rank_id; //so we can check it against the forum id's
		
				$page_content .= "\n  <tr class=\"row1\" id=\"" . $rank_id . "Row\">
									  <td width=\"160\">$rank_name</td>
									  <td width=\"80\" align=\"center\">$rank_posts</td>
									  <td width=\"160\" align=\"center\"><img src=\"$rank_image\" alt=\"\" /></td>
									  <td width=\"80\" align=\"center\"><a href=\"index.php?p=admin&s=ranks&action=editrank&id=$rank_id\"><img src=\"images/check.png\" alt=\"Edit\" /></a> &nbsp; <a style=\"cursor: pointer; cursor: hand;\" onclick=\"ajaxDeleteNotifier('" . $rank_id . "RankSpinner', 'ajax.php?action=deleterank&id=" . $rank_id . "', 'rank', '" . $rank_id . "Row');\"><img src=\"images/x.png\" alt=\"Delete\" /></a><div id=\"" . $rank_id . "RankSpinner\" style=\"display: none;\"><img src=\"images/indicator.gif\" alt=\"spinner\" /></div></td>
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