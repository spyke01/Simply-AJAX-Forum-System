<?
/***************************************************************************
 *                               fixtheme.php
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

	// This will update the system theme and all user themes to the default theme
	$sql="UPDATE `" . $DBTABLEPREFIX . "config` SET 'ftssafs_theme' = 'default';";
	$result = mysql_query($sql);
	
	$sql="UPDATE `" . $DBTABLEPREFIX . "users` SET 'users_style' = 'default';";
	$result = mysql_query($sql);
?>