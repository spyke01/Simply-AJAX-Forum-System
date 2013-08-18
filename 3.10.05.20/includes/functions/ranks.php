<?php 
/***************************************************************************
 *                               ranks.php
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
	// Returns Rank Name from the ID
	//=================================================
	function getRankNameByID($rankID) {
		global $DB, $menuvar, $safs_config;
		$rankName = "";
		
		$sql = "SELECT name FROM `" . DBTABLEPREFIX . "ranks` WHERE id='" . $rankID . "' LIMIT 1";
		$result = $DB->query($sql);
				
		if ($result && $DB->num_rows() > 0) {
			while ($row = $DB->fetch_array($result)) {	
				$rankName = $row['name'];
			}
			$DB->free_result($result);
		}
		
		return $rankName;
	}

 	//=================================================
	// Print the Ranks Table
	//=================================================
	function printRanksTable() {
		global $DB, $menuvar, $safs_config;
		
		$sql = "SELECT * FROM `" . DBTABLEPREFIX . "ranks` ORDER BY posts ASC";
		$result = $DB->query($sql);
		
		// Create our new table
		$table = new tableClass(1, 1, 1, "contentBox tablesorter", "ranksTable");
		
		// Create table title
		$table->addNewRow(array(array("data" => "Ranks", "colspan" => "4")), "", "title1", "thead");
		
		// Create column headers
		$table->addNewRow(
			array(
				array("type" => "th", "data" => "Name"),
				array("type" => "th", "data" => "Posts"),
				array("type" => "th", "data" => "Image"),
				array("type" => "th", "data" => "")
			), "", "title2", "thead"
		);
							
		// Add our data
		if (!$result || $DB->num_rows() == 0) {
			$table->addNewRow(array(array("data" => "There are no ranks in the system.", "colspan" => "4")), "ranksTableDefaultRow", "greenRow");
		}
		else {
			while ($row = $DB->fetch_array($result)) {				
				$table->addNewRow(
					array(
						array("data" => "<div id=\"" . $row['id'] . "_name\">" . $row['name'] . "</div>"),
						array("data" => "<div id=\"" . $row['id'] . "_posts\">" . $row['posts'] . "</div>"),
						array("data" => "<div id=\"" . $row['id'] . "_image\"><img src=\"" . $row['image'] . "\" alt=\"\" /></div>"),
						array("data" => createDeleteLinkWithImage($row['id'], $row['id'] . "_row", "ranks", "rank"), "class" => "center")
					), $row['id'] . "_row", ""
				);
			}
			$DB->free_result($result);
		}
		
		// Return the table's HTML
		return $table->returnTableHTML() . "
				<div id=\"ranksTableUpdateNotice\"></div>";
	}
	
	//=================================================
	// Returns the JQuery functions used to allow 
	// in-place editing and table sorting
	//=================================================
	function returnRanksTableJQuery() {
		global $DB, $menuvar, $safs_config;		
					
		$JQueryReadyScripts = "
				$('#ranksTable').tablesorter({ widgets: ['zebra'], headers: { 3: { sorter: false } } });";
		
		$sql = "SELECT id FROM `" . DBTABLEPREFIX . "ranks`";
		$result = $DB->query($sql);

		if ($result && $DB->num_rows() > 0) {
			while ($row = $DB->fetch_array($result)) {	
				$JQueryReadyScripts .= returnEditInPlaceJQuery($row['id'], "name", "ranks");
				$JQueryReadyScripts .= returnEditInPlaceJQuery($row['id'], "posts", "ranks");
				$JQueryReadyScripts .= returnEditInPlaceJQuery($row['id'], "image", "ranks", "", "loadurl  : 'ajax.php?action=getitem&table=ranks&item=image&id=" . $row['id'] . "'");
			}
			$DB->free_result($result);
		}
		
		return $JQueryReadyScripts;
	}
	
	//=================================================
	// Create a form to add new rank
	//
	// Used so that we can display it in many places
	//=================================================
	function printNewRankForm() {
		global $menuvar, $safs_config;

		$content .= "
				<div id=\"newRankResponse\">
				</div>
				<form name=\"newRankForm\" id=\"newRankForm\" action=\"" . $menuvar['RANKS'] . "\" method=\"post\" class=\"inputForm\" onsubmit=\"return false;\">
					<fieldset>
						<legend>New Rank</legend>
						<div><label for=\"rankName\">Name <span>- Required</span></label> <input name=\"rankName\" id=\"rankName\" type=\"text\" size=\"60\" class=\"required\" /></div>
						<div><label for=\"rankPosts\">Posts <span>- Required</span></label> <input name=\"rankPosts\" id=\"rankPosts\" type=\"text\" size=\"60\" class=\"required\" /></div>
						<div><label for=\"rankImage\">Image <span>- Required</span></label> <input name=\"rankImage\" id=\"rankImage\" type=\"text\" size=\"60\" class=\"required\" /></div>
						<div class=\"center\"><input type=\"submit\" class=\"button\" value=\"Create Rank\" /></div>
					</fieldset>
				</form>";
			
		return $content;
	}
	
	//=================================================
	// Returns the JQuery functions used to run the 
	// new order form
	//=================================================
	function returnNewRankFormJQuery($reprintTable = 0, $allowModification = 1) {
		$extraJQuery = ($reprintTable == 0) ? "
  						// Update the proper div with the returned data
						$('#newRankResponse').html('" . progressSpinnerHTML() . "');
						$('#newRankResponse').html(data);
						$('#newRankResponse').effect('highlight',{},500);" 
						: "
						// Clear the default row
						$('#ranksTableDefaultRow').remove();
  						// Update the table with the new row
						$('#ranksTable > tbody:last').append(data);
						$('#ranksTableUpdateNotice').html('" . tableUpdateNoticeHTML() . "');
						// Show a success message
						$('#newRankResponse').html('" . progressSpinnerHTML() . "');
						$('#newRankResponse').html(returnSuccessMessage('rank'));";
							
		$JQueryReadyScripts = "
			var v = jQuery(\"#newRankForm\").validate({
				errorElement: \"div\",
				errorClass: \"validation-advice\",
				submitHandler: function(form) {			
					jQuery.get('ajax.php?action=createRank&reprinttable=" . $reprintTable . "&showButtons=" . $allowModification . "', $('#newRankForm').serialize(), function(data) {
						" . $extraJQuery . "
						// Clear the form
						$('#newRankForm').clearForm();
					});
				}
			});";
		
		return $JQueryReadyScripts;
	}

?>