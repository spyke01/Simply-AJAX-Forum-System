<?php 
/***************************************************************************
 *                               topicicons.php
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
	// Returns number of posts in a forum from the ID
	//=================================================
	function getTopicIconImageByID($topicIconID) {
		global $DB, $menuvar, $safs_config;
		$numRows = 0;
		
		$sql = "SELECT image FROM `" . DBTABLEPREFIX . "topicicons` WHERE id='" . $topicIconID . "'";
		$result = $DB->query($sql);
				
		if ($result && $DB->num_rows() > 0) {
			while ($row = $DB->fetch_array($result)) {	
				$numRows = $row['image'];
			}
			$DB->free_result($result);
		}
		
		return $numRows;
	}

 	//=================================================
	// Print the Topic Icons Table
	//=================================================
	function printTopicIconsTable() {
		global $DB, $menuvar, $safs_config;
		
		$sql = "SELECT * FROM `" . DBTABLEPREFIX . "topicicons` ORDER BY name ASC";
		$result = $DB->query($sql);
		
		// Create our new table
		$table = new tableClass(1, 1, 1, "contentBox tablesorter", "topicIconsTable");
		
		// Create table title
		$table->addNewRow(array(array("data" => "Topic Icons", "colspan" => "4")), "", "title1", "thead");
		
		// Create column headers
		$table->addNewRow(
			array(
				array("type" => "th", "data" => "Name"),
				array("type" => "th", "data" => "Image"),
				array("type" => "th", "data" => "")
			), "", "title2", "thead"
		);
							
		// Add our data
		if (!$result || $DB->num_rows() == 0) {
			$table->addNewRow(array(array("data" => "There are no topic icons in the system.", "colspan" => "4")), "topicIconsTableDefaultRow", "greenRow");
		}
		else {
			while ($row = $DB->fetch_array($result)) {				
				$table->addNewRow(
					array(
						array("data" => "<div id=\"" . $row['id'] . "_name\">" . $row['name'] . "</div>"),
						array("data" => "<div id=\"" . $row['id'] . "_image\"><img src=\"" . $row['image'] . "\" alt=\"\" /></div>"),
						array("data" => createDeleteLinkWithImage($row['id'], $row['id'] . "_row", "topicicons", "topic icon"), "class" => "center")
					), $row['id'] . "_row", ""
				);
			}
			$DB->free_result($result);
		}
		
		// Return the table's HTML
		return $table->returnTableHTML() . "
				<div id=\"topicIconsTableUpdateNotice\"></div>";
	}
	
	//=================================================
	// Returns the JQuery functions used to allow 
	// in-place editing and table sorting
	//=================================================
	function returnTopicIconsTableJQuery() {
		global $DB, $menuvar, $safs_config;		
					
		$JQueryReadyScripts = "
				$('#topicIconsTable').tablesorter({ widgets: ['zebra'], headers: { 3: { sorter: false } } });";
		
		$sql = "SELECT id FROM `" . DBTABLEPREFIX . "topicicons`";
		$result = $DB->query($sql);

		if ($result && $DB->num_rows() > 0) {
			while ($row = $DB->fetch_array($result)) {	
				$JQueryReadyScripts .= returnEditInPlaceJQuery($row['id'], "name", "topicicons");
				$JQueryReadyScripts .= returnEditInPlaceJQuery($row['id'], "image", "topicicons", "", "loadurl  : 'ajax.php?action=getitem&table=topicicons&item=image&id=" . $row['id'] . "'");
			}
			$DB->free_result($result);
		}
		
		return $JQueryReadyScripts;
	}
	
	//=================================================
	// Create a form to add new topicicon
	//
	// Used so that we can display it in many places
	//=================================================
	function printNewTopicIconForm() {
		global $menuvar, $safs_config;

		$content .= "
				<div id=\"newTopicIconResponse\">
				</div>
				<form name=\"newTopicIconForm\" id=\"newTopicIconForm\" action=\"" . $menuvar['TOPICICONS'] . "\" method=\"post\" class=\"inputForm\" onsubmit=\"return false;\">
					<fieldset>
						<legend>New Topic Icon</legend>
						<div><label for=\"topicIconName\">Name <span>- Required</span></label> <input name=\"topicIconName\" id=\"topicIconName\" type=\"text\" size=\"60\" class=\"required\" /></div>
						<div><label for=\"topicIconImage\">Image <span>- Required</span></label> <input name=\"topicIconImage\" id=\"topicIconImage\" type=\"text\" size=\"60\" class=\"required\" /></div>
						<div class=\"center\"><input type=\"submit\" class=\"button\" value=\"Create Topic Icon\" /></div>
					</fieldset>
				</form>";
			
		return $content;
	}
	
	//=================================================
	// Returns the JQuery functions used to run the 
	// new order form
	//=================================================
	function returnNewTopicIconFormJQuery($reprintTable = 0, $allowModification = 1) {
		$extraJQuery = ($reprintTable == 0) ? "
  						// Update the proper div with the returned data
						$('#newTopicIconResponse').html('" . progressSpinnerHTML() . "');
						$('#newTopicIconResponse').html(data);
						$('#newTopicIconResponse').effect('highlight',{},500);" 
						: "
						// Clear the default row
						$('#topicIconsTableDefaultRow').remove();
  						// Update the table with the new row
						$('#topicIconsTable > tbody:last').append(data);
						$('#topicIconsTableUpdateNotice').html('" . tableUpdateNoticeHTML() . "');
						// Show a success message
						$('#newTopicIconResponse').html('" . progressSpinnerHTML() . "');
						$('#newTopicIconResponse').html(returnSuccessMessage('topic icon'));";
							
		$JQueryReadyScripts = "
			var v = jQuery(\"#newTopicIconForm\").validate({
				errorElement: \"div\",
				errorClass: \"validation-advice\",
				submitHandler: function(form) {			
					jQuery.get('ajax.php?action=createTopicIcon&reprinttable=" . $reprintTable . "&showButtons=" . $allowModification . "', $('#newTopicIconForm').serialize(), function(data) {
						" . $extraJQuery . "
						// Clear the form
						$('#newTopicIconForm').clearForm();
					});
				}
			});";
		
		return $JQueryReadyScripts;
	}

?>