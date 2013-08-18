<? 
/***************************************************************************
 *                               memberlist.php
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


$page_content .= "
					<br />
					<center>
						<table class='MLForumBorder' border='0' cellpadding='0' cellspacing='1'>
							<tr class=\"title1\">
								<td class='VForumT1' colspan=\"9\">$T_Members</td>
								</tr>
								<tr class='title2'> 
									<td class='MLT2Column1'>$T_Username_NC</td>
									<td class='MLT2Column2'>$T_Gender_NC</td>
									<td class='MLT2Column3'>$T_Country_NC</td>
									<td class='MLT2Column4'>$T_Contact_Info_NC</td>
								</tr>";


			$sql = "SELECT * FROM `" . $DBTABLEPREFIX . "users` WHERE users_username != 'Guest' ORDER BY users_username";
			$result = mysql_query($sql);
			
			if(mysql_num_rows($result) == 0) //if NO results, stop the script & return the error message
			{
				$page_content .= "\nError reading users table section 1.<br /><br />";
			} 
			else //if result found, run the rest of the script
			{
				while ( $row = mysql_fetch_array($result) ) {
					extract($row); 		
	
					$page_content .= "
								<tr class='row1'>			
									<td class='MLR1Column1'><a href='$menuvar[PROFILE]&action=viewprofile&id=$users_id'>$users_username</a></td>
									<td class='MLR1Column2'>";
					
					if ($users_gender == 'male' || $users_gender == 'female') {
						$page_content .= "<img src='images/gender_$users_gender.gif' alt='' />";
					} 
					else { 
						$page_content .= "&nbsp;"; 
					}
					$page_content .= "</td>
									<td class='MLR1Column3'>$users_country</td>
									<td class='MLR1Column4'>";
					if (trim($users_email_address) != "") { $page_content .= "<a href=\"mailto:$users_email_address\"><img src='images/newmsg.gif' border='0'  alt='Email User' /></a>"; }
					if (trim($users_website) != "") { $page_content .= "<a href='$users_website' target='blank'>www</a>"; }
					if (trim($users_aim) != "") { $page_content .= "<a href='aim:goim?screenname=$users_aim&amp;message=Hi.+Are+you+there?' target='_blank'><img src='$themedir/buttons/profile_aim.gif' border='0'  alt='AIM' /></a>"; }
					if (trim($users_yim) != "") { $page_content .= "<a href='http://edit.yahoo.com/config/send_webmesg?.target=$users_yim' target='_blank'><img src='$themedir/buttons/profile_yahoo.gif' border='0'  alt='Yahoo' /></a>"; }
					if (trim($users_msn) != "") { $page_content .= "<a href='http://members.msn.com/$users_msn' target='_blank'><img src='$themedir/buttons/profile_msn.gif' border='0'  alt='MSN' /></a>"; }
					if (trim($users_icq) != "") { $page_content .= "<a href='http://web.icq.com/whitepages/about_me/1,,,00.html?Uin=$users_icq' target='_blank'><img src='$themedir/buttons/profile_icq.gif' border='0'  alt='ICQ' /></a>"; }
					$page_content .= "</td>";
					$page_content .= "\n								</tr>";

				}
			}
			mysql_free_result($result); //free our query

		

$page_content .= "		
						</table>	
					</center>";


$page->setTemplateVar("PageContent", $page_content);
?>
