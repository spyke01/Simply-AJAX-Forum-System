<?
/***************************************************************************
 *                               optimize.php
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

$x = 0;

$page_content .= "<h3>Table Optimization Utility</h3><hr>";

	if ($_SESSION['user_level'] == ADMIN) { 
			$failed = array();
			$errors = 0;
		
			$showtables = mysql_query("SHOW tables");
			while($row = mysql_fetch_row($showtables)) { 			
				$table = $row[0];				
			
			    $sql2 = "OPTIMIZE TABLE `$table`";
			    $result2 = mysql_query($sql2);
			    	
				if ($result2 && mysql_num_rows($result2) > 0) {}
				else { 
					$errors = 1;
					$failed[$x] = $table;
				}
				
				$x++;			
			}
			// Print out success or failure
			if ($errors == 0) {
				$page_content .= "<strong><span color='green'>Succesfully optimized all tables.</span></strong><br />";
			}
			else {
				foreach ($failed as $key => $value) {
					$page_content .= "<strong><span color='green'>Failed to optimize $value.</span></strong><br />";
				}
			}
	}
	else { $page_content .= "<strong>You are not authorized to access this page.</strong>"; }

$page->setTemplateVar("PageContent", $page_content);
?>