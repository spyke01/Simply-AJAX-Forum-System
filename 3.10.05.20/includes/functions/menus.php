<?php 
/***************************************************************************
 *                               menus.php
 *                            -------------------
 *   begin                : Saturday, Sept 24, 2005
 *   copyright            : (C) 2005 Paden Clayton - Fast Track Sites
 *   email                : sales@fasttacksites.com
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
 
	//=================================================
	// Create our Manage Forums sidebar
	//=================================================
	function makeForumsSubMenu($page) {
		global $menuvar;
		
		$page->setTemplateVar("sidebar_active", ACTIVE);
		$page->makeMenuItem("sidebar", "Manage Forums", "", "title");
		$page->makeMenuItem("sidebar", "Forums", $menuvar['FORUMS'], "");
		$page->makeMenuItem("sidebar", "Categories", $menuvar['CATEGORIES'], "");
	}
	
	//=================================================
	// Create our Manage Forums sidebar
	//=================================================
	function makeReportingSubMenu($page) {
		global $menuvar;
		
		$page->setTemplateVar("sidebar_active", ACTIVE);
		$page->makeMenuItem("sidebar", "Reporting", "", "title");
		$page->makeMenuItem("sidebar", "Reports", $menuvar['REPORTS'], "");
		$page->makeMenuItem("sidebar", "Graphs", $menuvar['GRAPHS'], "");
	}
	
	//=================================================
	// Create our ystem Settings sidebar
	//=================================================
	function makeSystemSettingsSubMenu($page) {
		global $menuvar;
		
		$page->setTemplateVar("sidebar_active", ACTIVE);
		$page->makeMenuItem("sidebar", "System Settings", "", "title");
		$page->makeMenuItem("sidebar", "Configure", $menuvar['SETTINGS'], "");
		$page->makeMenuItem("sidebar", "Ranks", $menuvar['RANKS'], "");
		$page->makeMenuItem("sidebar", "Smilies", $menuvar['SMILIES'], "");
		$page->makeMenuItem("sidebar", "Topic Icons", $menuvar['TOPICICONS'], "");
		$page->makeMenuItem("sidebar", "Word Filter", $menuvar['WORDFILTER'], "");
		$page->makeMenuItem("sidebar", "Optimize", $menuvar['OPTIMIZE'], "");
		$page->makeMenuItem("sidebar", "Themes", $menuvar['THEMES'], "");
		$page->makeMenuItem("sidebar", "User Administration", $menuvar['USERS'], "");
	}
	
	//=================================================
	// Create our Private Messages sidebar
	//=================================================
	function makePrivateMessagesSubMenu($page) {
		global $menuvar;
		
		$page->setTemplateVar("sidebar_active", ACTIVE);
		$page->makeMenuItem("sidebar", "Message Center", "", "title");
		$page->makeMenuItem("sidebar", "Compose", $menuvar['COMPOSE'], "");
		$page->makeMenuItem("sidebar", "Inbox", $menuvar['INBOX'], "");
		$page->makeMenuItem("sidebar", "Archive", $menuvar['ARCHIVEDMESSAGES'], "");
		$page->makeMenuItem("sidebar", "Sent", $menuvar['SENTMESSAGES'], "");
	}

?>