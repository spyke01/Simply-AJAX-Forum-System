<? 
/***************************************************************************
 *                               profanity.php
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
		
		//===========================================
		// Add Delete And Edit profanity Functions
		// All Functions for profanities are below!
		//===========================================
		if ($action =='deletefilter' && isset($id)) { 
				if ($_SESSION[user_level] != USER) {
					$sql = "DELETE FROM `" . $DBTABLEPREFIX . "profanityfilter` WHERE profanityfilter_id = '$id'";
					mysql_query($sql) or die('Error, delete query 1 failed');
					$page_content .= "Your filter has been deleted, and you are being redirected to the main index. 
	 									<meta http-equiv='refresh' content='1;url=index.php?p=admin&s=profanityfilter'>";
				}
				else { $page_content .= "You are not authorized to delete this item."; }
		
		}
		elseif ($action =='addfilter') { 
			if(isset($_POST['code']))
			{
				$pcode = $_POST['code'];
				$pimageurl = $_POST['imageurl'];
				
				$sql = "INSERT INTO `" . $DBTABLEPREFIX . "profanityfilter` (profanityfilter_code, profanityfilter_image)".
			    "VALUES('$pcode', '$pimageurl')";
			    mysql_query($sql) or die('<br />Error, insert query failed' . $sql);
			
			    //confirm
 				$page_content .= "Your filter has been added, and you are being redirected to the main index.
 									<meta http-equiv='refresh' content='1;url=index.php?p=admin&s=profanityfilter'>";
			}
			else{
				$page_content .= "\n<center>
									<form action=\"index.php?p=admin&s=profanityfilter&action=addfilter\" method=\"post\">
									<table class=\"forumborder\" border=\"0\" cellpadding=\"0\" cellspacing=\"1\" width=\"160\">
										<tr class=\"title1\">
											<td colspan=\"2\">
												Add a Filter
											</td>
										</tr>
										<tbody id=\"divDrop\">
											<tr class=\"title2\">
												<td width=\"80\">Word to Filter</td>
												<td width=\"80\" align=\"center\">Image</td>
											</tr>
											<tr class=\"row1\">
												<td width=\"80\"><input type=\"text\" name=\"code\" size=\"50\"></td>
												<td width=\"80\" align=\"center\"><input type=\"text\" name=\"imageurl\" size=\"50\" value=\"$board_config[ftssafs_smilies_path]/censored.gif\"></td>
											</tr>
											<tr class=\"title2\">
										  		<td colspan=\"2\"></td>
											</tr>
											<tr class=\"title1\">
												<td  colspan=\"2\"><center><input type=\"submit\" name=\"submit\" value=\"Submit\"></center></td>
											</tr>
										</tbody>
									</table>
									</form>
									<br><br>
									</center>";

			}			
			unset($_POST['name']);	
		}
		
		
		elseif ($action =='editfilter' && isset($id)) { 
			if(isset($_POST['code']))
			{
				$pcode = $_POST['code'];
				$pimageurl = $_POST['imageurl'];
				
				$sql = "UPDATE `" . $DBTABLEPREFIX . "profanityfilter` SET profanityfilter_code='$pcode', profanityfilter_image='$pimageurl' WHERE profanityfilter_id='$id'";
			    mysql_query($sql) or die('<br />Error, insert query failed' . $sql);
			
			    //confirm
 				$page_content .= "Your filter has been edited, and you are being redirected to the main index.
 									<meta http-equiv='refresh' content='1;url=index.php?p=admin&s=profanityfilter'>";
			}
			else{
				$sql = "SELECT * FROM `" . $DBTABLEPREFIX . "profanityfilter` WHERE profanityfilter_id=$id";
				$result = mysql_query($sql);
					
				if($result && mysql_num_rows($result) > 0) {
					$row = mysql_fetch_array($result);
					
					$page_content .= "\n<center>
								<form action=\"index.php?p=admin&s=profanityfilter&action=editfilter&id=$id\" method=\"post\">
									<table class=\"forumborder\" border=\"0\" cellpadding=\"0\" cellspacing=\"1\" width=\"160\">
									  <tr class=\"title1\">
									    <td colspan=\"2\">
											Edit Filter
										</td>
									  </tr>
									  <tbody id=\"divDrop\">
									  	<tr class=\"title2\">
									  	  <td width=\"80\">Word to Filter</td>
									  	  <td width=\"80\" align=\"center\">Image</td>
									  	</tr>
									  	<tr class=\"row1\">
									  	  <td width=\"80\"><input type=\"text\" name=\"code\" size=\"40\" value=\"$row[profanityfilter_code]\"></td>
									  	  <td width=\"80\" align=\"center\"><input type=\"text\" name=\"imageurl\" size=\"40\" value=\"$row[profanityfilter_image]\"></td>
									  	</tr>
									  	<tr class=\"title2\">
									  	  <td colspan=\"2\"></td>
									  	</tr>
									  	<tr class=\"title1\">
									  	  <td  colspan=\"2\"><center><input type=\"submit\" name=\"submit\" value=\"Submit\"></center></td>
									  	</tr>	
									  </tbody>
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
								  <td colspan=\"2\" align=\"left\">
								  Current Word Filters</td>
								  <td align=\"center\">
								  	<a href=\"index.php?p=admin&s=profanityfilter&action=addfilter\"><img src=\"images/plus.png\" alt=\"Add a filter\" /></a>
								  </td>
								</tr>
								<tbody id=\"divDrop\">
								<tr class=\"title2\">
								  <td width=\"80\" align=\"center\">Word to Filter</td>
								  <td width=\"80\" align=\"center\">Images</td>
								  <td width=\"80\" align=\"center\">&nbsp;</td>
								</tr>";
												
			$sql = "SELECT * FROM `" . $DBTABLEPREFIX . "profanityfilter` ORDER BY profanityfilter_id"; //gets profanities
			$result = mysql_query($sql);
	
			while ( $row = mysql_fetch_array($result) )
			{
				extract($row); //so we dont have to do long array variables
				$filterid1 = $profanityfilter_id; 
		
				$page_content .= "\n  <tr class=\"row1\" id=\"" . $profanityfilter_id . "Row\">
									  <td width=\"80\" align=\"center\">$profanityfilter_code</td>
									  <td width=\"80\" align=\"center\"><img src=\"$profanityfilter_image\" alt=\"\" /></td>
									  <td width=\"80\" align=\"center\"><a href=\"index.php?p=admin&s=profanityfilter&action=editfilter&id=$profanityfilter_id\"><img src=\"images/check.png\" alt=\"Edit\" /></a> &nbsp; <a style=\"cursor: pointer; cursor: hand;\" onclick=\"ajaxDeleteNotifier('" . $profanityfilter_id . "ProfanitySpinner', 'ajax.php?action=deletefilter&id=" . $profanityfilter_id . "', 'word filter', '" . $profanityfilter_id . "Row');\"><img src=\"images/x.png\" alt=\"Delete\" /></a><div id=\"" . $profanityfilter_id . "ProfanitySpinner\" style=\"display: none;\"><img src=\"images/indicator.gif\" alt=\"spinner\" /></div></td>
									</tr>";
			}
			mysql_free_result($result);
			
			$page_content .= "\n  </tbody>
								</table>
								<br><br>
								</center>";
		}
}

$page->setTemplateVar("PageContent", $page_content);
?>