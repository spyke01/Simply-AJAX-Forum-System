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
Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions are met:
    * Redistributions of source code must retain the above copyright
      notice, this list of conditions and the following disclaimer.
    * Redistributions in binary form must reproduce the above copyright
      notice, this list of conditions and the following disclaimer in the
      documentation and/or other materials provided with the distribution.
    * Neither the name of the <organization> nor the
      names of its contributors may be used to endorse or promote products
      derived from this software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
DISCLAIMED. IN NO EVENT SHALL <COPYRIGHT HOLDER> BE LIABLE FOR ANY
DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
(INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 ***************************************************************************/

$menuvar = array(

	/*###############################
	#       Menu Variables          #
	###############################*/
	'ADMIN' => 'index.php?p=admin',
	'ARCHIVEDMESSAGES' => 'index.php?p=inbox&action=viewArchive',
	'CATEGORIES' => 'index.php?p=admin&s=categories',
	'COMPOSE' => 'index.php?p=inbox&action=compose',
	'DELETETOPIC' => 'index.php?p=viewtopic&action=deleteTopic',
	'FORUMS' => 'index.php?p=admin&s=forums',
	'GRAPHS' => 'index.php?p=admin&s=graphs',
	'HOME' => 'index.php',
	'INBOX' => 'index.php?p=inbox',
	'LOGIN' => 'index.php?p=login',
	'LOGOUT' => 'index.php?p=logout',
	'MANAGEFORUMS' => 'index.php?p=admin&s=manageForums',
	'MEMBERS' => 'index.php?p=members',
	'MOVETOPIC' => 'index.php?p=viewtopic&action=moveTopic',
	'OPTIMIZE' => 'index.php?p=admin&s=optimize',
	'PROFILE' => 'index.php?p=profile',
	'RANKS' => 'index.php?p=admin&s=ranks',
	'REGISTER' => 'index.php?p=register',
	'REPORTING' => 'index.php?p=admin&s=reporting',
	'REPORTS' => 'index.php?p=admin&s=reports',
	'SEARCH' => 'index.php?p=search',
	'SENTMESSAGES' => 'index.php?p=inbox&action=viewSent',
	'SETTINGS' => 'index.php?p=admin&s=settings',
	'SMILIES' => 'index.php?p=admin&s=smilies',
	'SYSTEMSETTINGS' => 'index.php?p=admin&s=systemsettings',
	'THEMES' => 'index.php?p=admin&s=themes',
	'TOPICICONS' => 'index.php?p=admin&s=topicicons',
	'USERS' => 'index.php?p=admin&s=users',
	'VIEWFORUM' => 'index.php?p=viewforum',
	'VIEWMESSAGE' => 'index.php?p=inbox&action=viewMessage',
	'VIEWPROFILE' => 'index.php?p=profile&action=viewProfile',
	'VIEWTOPIC' => 'index.php?p=viewtopic',
	'WORDFILTER' => 'index.php?p=admin&s=wordfilter',
	
	/*###############################
	#       Dead Variable          #
	###############################*/
	'dead' => 'dead.php'
);
?>