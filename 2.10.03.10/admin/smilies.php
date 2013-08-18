<? 
/***************************************************************************
 *                               smilies.php
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
		
		if ($action =='addsmiley') { 
			if(isset($_POST['code']))
			{
				$pcode = $_POST['code'];
				$pimageurl = $_POST['imageurl'];
				
				$sql = "INSERT INTO `" . $DBTABLEPREFIX . "smilies` (smilies_code, smilies_image)".
			    "VALUES('$pcode', '$pimageurl')";
			    mysql_query($sql) or die('<br />Error, insert query failed' . $sql);
			
			    //confirm
 				$page_content .= "Your smiley has been added, and you are being redirected to the main index. 
 									<meta http-equiv='refresh' content='1;url=index.php?p=admin&s=smilies'>";
			}
			else{
				$page_content .= "\n<center>
									<form action=\"index.php?p=admin&s=smilies&action=addsmiley\" method=\"post\">
									<table class=\"forumborder\" border=\"0\" cellpadding=\"0\" cellspacing=\"1\" width=\"160\">
									  <tr class=\"title1\">
									    <td colspan=\"2\"><strong>Add a smiley</strong></td>
									  </tr>
									  <tr class=\"title2\">
									    <td width=\"80\">Code</td>
									    <td width=\"80\" align=\"center\">Image</td>
									  </tr>
									  <tr class=\"row1\">
									    <td width=\"80\"><input type=\"text\" name=\"code\" size=\"50\"></td>
									    <td width=\"80\" align=\"center\"><input type=\"text\" name=\"imageurl\" size=\"50\" value=\"$board_config[ftssafs_smilies_path]/\"></td>
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
		
		
		elseif ($action =='editsmiley' && isset($id)) { 
			if(isset($_POST['code']))
			{
				$pcode = $_POST['code'];
				$pimageurl = $_POST['imageurl'];
				
				$sql = "UPDATE `" . $DBTABLEPREFIX . "smilies` SET smilies_code='$pcode', smilies_image='$pimageurl' WHERE smilies_id='$id'";
			    mysql_query($sql) or die('<br />Error, insert query failed' . $sql);
			
			    //confirm
 				$page_content .= "Your smiley has been edited, and you are being redirected to the main index.
 									<meta http-equiv='refresh' content='1;url=index.php?p=admin&s=smilies'>";
			}
			else{
				$sql = "SELECT * FROM `" . $DBTABLEPREFIX . "smilies` WHERE smilies_id=$id";
				$result = mysql_query($sql);
					
				if($result && mysql_num_rows($result) > 0) {
					$row = mysql_fetch_array($result);
					
					$page_content .= "\n<center>
										<form action=\"index.php?p=admin&s=smilies&action=editsmiley&id=$id\" method=\"post\">
										<table class=\"forumborder\" border=\"0\" cellpadding=\"0\" cellspacing=\"1\" width=\"160\">
										  <tr class=\"title1\">
										    <td colspan=\"2\"><strong>Edit smiley</strong></td>
										  </tr>
										  <tr class=\"title2\">
										    <td width=\"80\">smiley</td>
										    <td width=\"80\" align=\"center\">Image</td>
										  </tr>
										  <tr class=\"row1\">
										    <td width=\"80\"><input type=\"text\" name=\"code\" size=\"40\" value=\"$row[smilies_code]\"></td>
										    <td width=\"80\" align=\"center\"><input type=\"text\" name=\"imageurl\" size=\"40\" value=\"$row[smilies_image]\"></td>
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
								  <td colspan=\"2\" align=\"left\"><strong>Current Smileys</strong></td><td align=\"center\"><a href=\"index.php?p=admin&s=smilies&action=addsmiley\"><img src=\"images/plus.png\" alt=\"Add a smiley\" /></a></td>
								</tr>
								<tr class=\"title2\">
								  <td width=\"80\" align=\"center\">Code</td>
								  <td width=\"80\" align=\"center\">Images</td>
								  <td width=\"80\" align=\"center\">&nbsp;</td>
								</tr>";
												
			$sql = "SELECT * FROM `" . $DBTABLEPREFIX . "smilies` ORDER BY smilies_id"; //gets smileys
			$result = mysql_query($sql);
	
			while ( $row = mysql_fetch_array($result) )
			{
				extract($row); //so we dont have to do long array variables
				$smileyid1 = $smilies_id; 
		
				$page_content .= "\n  <tr class=\"row1\" id=\"" . $smilies_id . "Row\">
									  <td width=\"80\" align=\"center\">$smilies_code</td>
									  <td width=\"80\" align=\"center\"><img src=\"$smilies_image\" alt=\"\" /></td>
									  <td width=\"80\" align=\"center\"><a href=\"index.php?p=admin&s=smilies&action=editsmiley&id=$smilies_id\"><img src=\"images/check.png\" alt=\"Edit\" /></a> &nbsp; <a style=\"cursor: pointer; cursor: hand;\" onclick=\"ajaxDeleteNotifier('" . $cat_id . "SmiliesSpinner', 'ajax.php?action=deletesmiley&id=" . $smilies_id . "', 'smiley', '" . $smilies_id . "Row');\"><img src=\"images/x.png\" alt=\"Delete\" /></a><div id=\"" . $smilies_id . "SmiliesSpinner\" style=\"display: none;\"><img src=\"images/indicator.gif\" alt=\"spinner\" /></div></td>
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