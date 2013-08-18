<?php
/***************************************************************************
 *                               menu.php
 *                            -------------------
 *   begin                : Tuseday, March 14, 2006
 *   copyright            : (C) 2006 Fast Track Sites
 *   email                : sales@fasttracksites.com
 *
 *
 ***************************************************************************/

/***************************************************************************
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 * 
 * This program is also licensed under a program license located inside 
 * the license.txt file included with this program. This license overrides
 * all license statements made in the previous license reveference. 
 ***************************************************************************/
function appendVars($url) {
	$siteUrl = "http://forum.fasttracksites.com";
	return $siteUrl . $url;
}

$menuvar = array(

/*###############################
#       Menu Variables          #
###############################*/
'ADMIN' => appendVars('index.php?p=admin'),
'CHECKUSER' => appendVars('index.php?p=checkuser'),
'COMPOSEMSG' => appendVars('index.php?p=inbox&action=compose'),
'CONTACT' => appendVars('index.php?p=contact'),
'HOME' => appendVars('index.php'),
'JOIN' => appendVars('index.php?p=join'),
'LOGIN' => appendVars('index.php?p=login'),
'LOGOUT' => appendVars('index.php?p=logout'),
'MEMBERLIST' => appendVars('index.php?p=memberlist'),
'POST' => appendVars('index.php?p=post'),
'PRIVMSGS' => appendVars('index.php?p=inbox'),
'PROFILE' => appendVars('index.php?p=profile'),
'REGISTER' => appendVars('index.php?p=register'),
'SEARCH' => appendVars('index.php?p=search'),
'SWITCHER' => appendVars('index.php?p=switcher'),
'VIEWFORUM' => appendVars('index.php?p=viewforum'),
'VIEWTOPIC' => appendVars('index.php?p=viewtopic'),

/*###############################
#    Admin Menu Variables       #
###############################*/
'GENERALADMIN' => appendVars('index.php?p=admin&s=general'),
'FORUMADMIN' => appendVars('index.php?p=admin'),
'USERADMIN' => appendVars('index.php?p=admin&s=users'),
'PROFANITYADMIN' => appendVars('index.php?p=admin&s=profanity'),
'RANKADMIN' => appendVars('index.php?p=admin&s=ranks'),
'SMILEYADMIN' => appendVars('index.php?p=admin&s=smilies'),
'TOPICICONSADMIN' => appendVars('index.php?p=admin&s=topicicons'),
'TABLEADMIN' => appendVars('index.php?p=admin&s=optimize'),
'EDITFORUM' => appendVars('index.php?p=admin'),
'EDITRANK' => appendVars('index.php?p=admin&s=ranks'),


/*###############################
#       Dead Variable          #
###############################*/
'dead' => appendVars('dead'
);
?>