<?php 
/***************************************************************************
 *                               wordfilters.php
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
	// Runs our word filters on text and returns the clean version
	//=================================================
	function runWordFilters($text) {
		global $DB;
		
		// Run word filters
		$sql = "SELECT * FROM `" . DBTABLEPREFIX . "wordfilters`";
		$result = $DB->query($sql);

		if ($result && $DB->num_rows() > 0) {
			while ($row = $DB->fetch_array($result)) {	
				$text = str_replace($row['code'], ((!empty($row['image'])) ? "<img src=\"" . $row['image'] . "\" alt=\"\" />" : "*CENSORED*"), $text);
			}
			$DB->free_result($result);
		}
		
		// Handle highlighting		
		if (!empty($actual_highlight)) {
			// Highlight as a phrase
			str_replace($actual_highlight, "<span class=\"highlight\">" . $actual_highlight . "</span>", $text);
			
			// Highlight as individual words
			$words = explode(" ", $actual_highlight);
			foreach ($words as $key => $word) {
				str_replace($word, "<span class=\"highlight\">" . $word . "</span>", $text);
			}
		}
		
		return $text;
	}
	
 	//=================================================
	// Print the Word Filters Table
	//=================================================
	function printWordFiltersTable() {
		global $DB, $menuvar, $safs_config;
		
		$sql = "SELECT * FROM `" . DBTABLEPREFIX . "wordfilters` ORDER BY code ASC";
		$result = $DB->query($sql);
		
		// Create our new table
		$table = new tableClass(1, 1, 1, "contentBox tablesorter", "wordFiltersTable");
		
		// Create table title
		$table->addNewRow(array(array("data" => "Word Filters", "colspan" => "4")), "", "title1", "thead");
		
		// Create column headers
		$table->addNewRow(
			array(
				array("type" => "th", "data" => "Code"),
				array("type" => "th", "data" => "Image"),
				array("type" => "th", "data" => "")
			), "", "title2", "thead"
		);
							
		// Add our data
		if (!$result || $DB->num_rows() == 0) {
			$table->addNewRow(array(array("data" => "There are no word filters in the system.", "colspan" => "4")), "wordFiltersTableDefaultRow", "greenRow");
		}
		else {
			while ($row = $DB->fetch_array($result)) {				
				$table->addNewRow(
					array(
						array("data" => "<div id=\"" . $row['id'] . "_code\">" . $row['code'] . "</div>"),
						array("data" => "<div id=\"" . $row['id'] . "_image\"><img src=\"" . $row['image'] . "\" alt=\"\" /></div>"),
						array("data" => createDeleteLinkWithImage($row['id'], $row['id'] . "_row", "wordfilters", "word filter"), "class" => "center")
					), $row['id'] . "_row", ""
				);
			}
			$DB->free_result($result);
		}
		
		// Return the table's HTML
		return $table->returnTableHTML() . "
				<div id=\"wordFiltersTableUpdateNotice\"></div>";
	}
	
	//=================================================
	// Returns the JQuery functions used to allow 
	// in-place editing and table sorting
	//=================================================
	function returnWordFiltersTableJQuery() {
		global $DB, $menuvar, $safs_config;		
					
		$JQueryReadyScripts = "
				$('#wordFiltersTable').tablesorter({ widgets: ['zebra'], headers: { 3: { sorter: false } } });";
		
		$sql = "SELECT id FROM `" . DBTABLEPREFIX . "wordfilters`";
		$result = $DB->query($sql);

		if ($result && $DB->num_rows() > 0) {
			while ($row = $DB->fetch_array($result)) {	
				$JQueryReadyScripts .= returnEditInPlaceJQuery($row['id'], "code", "wordfilters");
				$JQueryReadyScripts .= returnEditInPlaceJQuery($row['id'], "image", "wordfilters", "", "loadurl  : 'ajax.php?action=getitem&table=wordfilters&item=image&id=" . $row['id'] . "'");
			}
			$DB->free_result($result);
		}
		
		return $JQueryReadyScripts;
	}
	
	//=================================================
	// Create a form to add new wordfilter
	//
	// Used so that we can display it in many places
	//=================================================
	function printNewWordFilterForm() {
		global $menuvar, $safs_config;

		$content .= "
				<div id=\"newWordFilterResponse\">
				</div>
				<form name=\"newWordFilterForm\" id=\"newWordFilterForm\" action=\"" . $menuvar['WORDFILTER'] . "\" method=\"post\" class=\"inputForm\" onsubmit=\"return false;\">
					<fieldset>
						<legend>New Word Filter</legend>
						<div><label for=\"wordFilterCode\">Code <span>- Required</span></label> <input name=\"wordFilterCode\" id=\"wordFilterCode\" type=\"text\" size=\"60\" class=\"required\" /></div>
						<div><label for=\"wordFilterImage\">Image <span>- Required</span></label> <input name=\"wordFilterImage\" id=\"wordFilterImage\" type=\"text\" size=\"60\" class=\"required\" /></div>
						<div class=\"center\"><input type=\"submit\" class=\"button\" value=\"Create Word Filter\" /></div>
					</fieldset>
				</form>";
			
		return $content;
	}
	
	//=================================================
	// Returns the JQuery functions used to run the 
	// new order form
	//=================================================
	function returnNewWordFilterFormJQuery($reprintTable = 0, $allowModification = 1) {
		$extraJQuery = ($reprintTable == 0) ? "
  						// Update the proper div with the returned data
						$('#newWordFilterResponse').html('" . progressSpinnerHTML() . "');
						$('#newWordFilterResponse').html(data);
						$('#newWordFilterResponse').effect('highlight',{},500);" 
						: "
						// Clear the default row
						$('#wordFiltersTableDefaultRow').remove();
  						// Update the table with the new row
						$('#wordFiltersTable > tbody:last').append(data);
						$('#wordFiltersTableUpdateNotice').html('" . tableUpdateNoticeHTML() . "');
						// Show a success message
						$('#newWordFilterResponse').html('" . progressSpinnerHTML() . "');
						$('#newWordFilterResponse').html(returnSuccessMessage('word filter'));";
							
		$JQueryReadyScripts = "
			var v = jQuery(\"#newWordFilterForm\").validate({
				errorElement: \"div\",
				errorClass: \"validation-advice\",
				submitHandler: function(form) {			
					jQuery.get('ajax.php?action=createWordFilter&reprinttable=" . $reprintTable . "&showButtons=" . $allowModification . "', $('#newWordFilterForm').serialize(), function(data) {
						" . $extraJQuery . "
						// Clear the form
						$('#newWordFilterForm').clearForm();
					});
				}
			});";
		
		return $JQueryReadyScripts;
	}

?>