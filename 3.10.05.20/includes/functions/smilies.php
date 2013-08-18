<?php 
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
	// Returns Smiley Code from the ID
	//=================================================
	function getSmileyCodeByID($smileyID) {
		global $DB, $menuvar, $safs_config;
		$smileyCode = "";
		
		$sql = "SELECT code FROM `" . DBTABLEPREFIX . "smilies` WHERE id='" . $smileyID . "' LIMIT 1";
		$result = $DB->query($sql);
				
		if ($result && $DB->num_rows() > 0) {
			while ($row = $DB->fetch_array($result)) {	
				$smileyCode = $row['code'];
			}
			$DB->free_result($result);
		}
		
		return $smileyCode;
	}

 	//=================================================
	// Print the Smilies Table
	//=================================================
	function printSmiliesTable() {
		global $DB, $menuvar, $safs_config;
		
		$sql = "SELECT * FROM `" . DBTABLEPREFIX . "smilies` ORDER BY code ASC";
		$result = $DB->query($sql);
		
		// Create our new table
		$table = new tableClass(1, 1, 1, "contentBox tablesorter", "smiliesTable");
		
		// Create table title
		$table->addNewRow(array(array("data" => "Smilies", "colspan" => "4")), "", "title1", "thead");
		
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
			$table->addNewRow(array(array("data" => "There are no smilies in the system.", "colspan" => "4")), "smiliesTableDefaultRow", "greenRow");
		}
		else {
			while ($row = $DB->fetch_array($result)) {				
				$table->addNewRow(
					array(
						array("data" => "<div id=\"" . $row['id'] . "_code\">" . $row['code'] . "</div>"),
						array("data" => "<div id=\"" . $row['id'] . "_image\"><img src=\"" . $row['image'] . "\" alt=\"\" /></div>"),
						array("data" => createDeleteLinkWithImage($row['id'], $row['id'] . "_row", "smilies", "smiley"), "class" => "center")
					), $row['id'] . "_row", ""
				);
			}
			$DB->free_result($result);
		}
		
		// Return the table's HTML
		return $table->returnTableHTML() . "
				<div id=\"smiliesTableUpdateNotice\"></div>";
	}
	
	//=================================================
	// Returns the JQuery functions used to allow 
	// in-place editing and table sorting
	//=================================================
	function returnSmiliesTableJQuery() {
		global $DB, $menuvar, $safs_config;		
					
		$JQueryReadyScripts = "
				$('#smiliesTable').tablesorter({ widgets: ['zebra'], headers: { 3: { sorter: false } } });";
		
		$sql = "SELECT id FROM `" . DBTABLEPREFIX . "smilies`";
		$result = $DB->query($sql);

		if ($result && $DB->num_rows() > 0) {
			while ($row = $DB->fetch_array($result)) {	
				$JQueryReadyScripts .= returnEditInPlaceJQuery($row['id'], "code", "smilies");
				$JQueryReadyScripts .= returnEditInPlaceJQuery($row['id'], "image", "smilies", "", "loadurl  : 'ajax.php?action=getitem&table=smilies&item=image&id=" . $row['id'] . "'");
			}
			$DB->free_result($result);
		}
		
		return $JQueryReadyScripts;
	}
	
	//=================================================
	// Create a form to add new smiley
	//
	// Used so that we can display it in many places
	//=================================================
	function printNewSmileyForm() {
		global $menuvar, $safs_config;

		$content .= "
				<div id=\"newSmileyResponse\">
				</div>
				<form name=\"newSmileyForm\" id=\"newSmileyForm\" action=\"" . $menuvar['SMILIES'] . "\" method=\"post\" class=\"inputForm\" onsubmit=\"return false;\">
					<fieldset>
						<legend>New Smiley</legend>
						<div><label for=\"smileyCode\">Code <span>- Required</span></label> <input name=\"smileyCode\" id=\"smileyCode\" type=\"text\" size=\"60\" class=\"required\" /></div>
						<div><label for=\"smileyImage\">Image <span>- Required</span></label> <input name=\"smileyImage\" id=\"smileyImage\" type=\"text\" size=\"60\" class=\"required\" /></div>
						<div class=\"center\"><input type=\"submit\" class=\"button\" value=\"Create Smiley\" /></div>
					</fieldset>
				</form>";
			
		return $content;
	}
	
	//=================================================
	// Returns the JQuery functions used to run the 
	// new order form
	//=================================================
	function returnNewSmileyFormJQuery($reprintTable = 0, $allowModification = 1) {
		$extraJQuery = ($reprintTable == 0) ? "
  						// Update the proper div with the returned data
						$('#newSmileyResponse').html('" . progressSpinnerHTML() . "');
						$('#newSmileyResponse').html(data);
						$('#newSmileyResponse').effect('highlight',{},500);" 
						: "
						// Clear the default row
						$('#smiliesTableDefaultRow').remove();
  						// Update the table with the new row
						$('#smiliesTable > tbody:last').append(data);
						$('#smiliesTableUpdateNotice').html('" . tableUpdateNoticeHTML() . "');
						// Show a success message
						$('#newSmileyResponse').html('" . progressSpinnerHTML() . "');
						$('#newSmileyResponse').html(returnSuccessMessage('smiley'));";
							
		$JQueryReadyScripts = "
			var v = jQuery(\"#newSmileyForm\").validate({
				errorElement: \"div\",
				errorClass: \"validation-advice\",
				submitHandler: function(form) {			
					jQuery.get('ajax.php?action=createSmiley&reprinttable=" . $reprintTable . "&showButtons=" . $allowModification . "', $('#newSmileyForm').serialize(), function(data) {
						" . $extraJQuery . "
						// Clear the form
						$('#newSmileyForm').clearForm();
					});
				}
			});";
		
		return $JQueryReadyScripts;
	}

?>