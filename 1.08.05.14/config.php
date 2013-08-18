<? 
/***************************************************************************
 *                               config.php
 *                            -------------------
 *   begin                : Tuseday, March 14, 2006
 *   copyright            : (C) 2006 Fast Track Sites
 *   email                : sales@fasttracksites.com
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
include '_db.php';

$safs_config = array();

$sql = "SELECT * FROM `" . $DBTABLEPREFIX . "config`";
$result = mysql_query($sql);


while ( $row = mysql_fetch_array($result) )
{
	if ($row['config_name'] == "ftssafs_announcement_text") { $safs_config[$row['config_name']] = $row['config_extra_value']; }
	else { $safs_config[$row['config_name']] = $row['config_value']; }
}

?>