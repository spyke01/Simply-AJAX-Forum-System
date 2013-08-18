<? 
/***************************************************************************
 *                               wordfilter.php
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
if ($_SESSION['user_level'] == SYSTEM_ADMIN || $_SESSION['user_level'] == BOARD_ADMIN) {
	//==================================================
	// Handle editing, adding, and deleting of Word Filters
	//==================================================	
	if ($actual_action == "editwordfilter" && isset($actual_id)) {
		// Add breadcrumb
		$page->addBreadCrumb("Edit Word Filter", "");
		
		$page_content .= "
			<div class=\"roundedBox\">
				" . printEditWordFilterForm($actual_id) . "
			</div>";
		
		// Handle our JQuery needs
		$JQueryReadyScripts = returnEditWordFilterFormJQuery($actual_id);
	}
	else {		
		//==================================================
		// Print out our Word Filters table
		//==================================================
		
		$page_content .= "
						<div id=\"tabs\">
							<ul>
								<li><a href=\"#currentWordFilters\"><span>Current Word Filters</span></a></li>
								<li><a href=\"#addAWordFilter\"><span>Add a Word Filter</span></a></li>
							</ul>
							<div id=\"currentWordFilters\">
								<div id=\"updateMeWordFilters\">
									" . printWordFiltersTable($_POST) . "
								</div>
							</div>
							<div id=\"addAWordFilter\">
								" . printNewWordFilterForm() . "
							</div>
						</div>";
				
		// Handle our JQuery needs
		$JQueryReadyScripts = returnWordFiltersTableJQuery() . returnNewWordFilterFormJQuery(1) . "$(\"#tabs\").tabs();";
	}
	
	$page->setTemplateVar("PageContent", $page_content);
	$page->setTemplateVar("JQueryReadyScript", $JQueryReadyScripts);
}
else {
	$page->setTemplateVar("PageContent", "\nYou Are Not Authorized To Access This Area. Please Refrain From Trying To Do So Again.");
}
?>