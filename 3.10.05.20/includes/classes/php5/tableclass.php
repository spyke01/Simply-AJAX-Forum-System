<?php
/***************************************************************************
 *                               tableclass.php
 *                            -------------------
 *   begin                : Tuesday, August 15, 2006
 *   copyright            : (C) 2006 Paden Clayton
 *   website              : http://www.fasttracksites.com
 *   email                : sales@fasttracksites.com
 *
 *
 ***************************************************************************/

/***************************************************************************
 *
 * This program is licensed under the FTS program license that has been 
 * included with this program. It is located inside the license.txt file 
 * that is found in the base directory. This license is legally binding and
 * in the event of a breach of contract, legal action may be taken. 
 *
 ***************************************************************************/
 
class tableClass { 
	var $border = 0;  
	var $padding = 0;  
	var $spacing = 0;  
	var $class = "";  
	var $id = "";  
	var $tHeadRowData = array();
	var $tBodyRowData = array();
	var $tFootRowData = array();

	//===============================================================
	// This function will me used for setting our template variables 
	//===============================================================
	public function tableClass($border = 0, $padding = 0, $spacing = 0, $class = "", $id = "") {
		$this->border = $border;
		$this->padding = $padding;
		$this->spacing = $spacing;
		$this->class = $class;
		$this->id = $id;
	}
	
	//===============================================================
	// This function will add a new row to the table
	//===============================================================
	public function addNewRow($data, $id = "", $class = "", $section = "tbody") {
		switch ($section) {
			case 'thead':
				$this->tHeadRowData[] = array("class" => $class, "data" => $data, "id" => $id);
				break;
			case 'tfoot':
				$this->tFootRowData[] = array("class" => $class, "data" => $data, "id" => $id);
				break;
			default:
				$this->tBodyRowData[] = array("class" => $class, "data" => $data, "id" => $id);
				break;
		}
	}
	
	//===============================================================
	// This function will allow us to generate each sections HTML
	//===============================================================
	public function returnSectionHTML($sectionArray) {	
		foreach ($sectionArray as $key => $rowChunk) {
			$classBit = (!empty($rowChunk['class'])) ? " class=\"" . $rowChunk['class'] . "\"" : "";
			$idBit = (!empty($rowChunk['id'])) ? " id=\"" . $rowChunk['id'] . "\"" : "";
			
			$html .= "
							<tr" . $idBit . $classBit . ">";
			
			foreach ($rowChunk['data'] as $key => $rowChunkData) {
				$typeBit = (!empty($rowChunkData['type'])) ? $rowChunkData['type'] : "td";
				$classBit = (!empty($rowChunkData['class'])) ? " class=\"" . $rowChunkData['class'] . "\"" : "";
				$idBit = (!empty($rowChunkData['id'])) ? " id=\"" . $rowChunkData['id'] . "\"" : "";
				$colspanBit = (!empty($rowChunkData['colspan'])) ? " colspan=\"" . $rowChunkData['colspan'] . "\"" : "";
				$rowspanBit = (!empty($rowChunkData['rowspan'])) ? " rowspan=\"" . $rowChunkData['rowspan'] . "\"" : "";
				
				$html .= "
								<" . $typeBit . $idBit . $classBit . $colspanBit . $rowspanBit . ">" . $rowChunkData['data'] . "</" . $typeBit . ">";
			}
			
			$html .= "
							</tr>";
		}
			
		return $html;
	}
	
	//===============================================================
	// This function will allow us to generate the tables HTML
	//===============================================================
	public function returnTableHTML($theadID = "", $tbodyID = "", $tfootID = "") {
		$classBit = (!empty($this->class)) ? " class=\"" . $this->class . "\"" : "";
		$idBit = (!empty($this->id)) ? " id=\"" . $this->id . "\"" : "";
		
		$theadIDBit = (!empty($theadID)) ? " id=\"" . $theadID . "\"" : "";
		$tbodyIDBit = (!empty($tbodyID)) ? " id=\"" . $tbodyID . "\"" : "";
		$tfootIDBit = (!empty($tfootID)) ? " id=\"" . $tfootID . "\"" : "";
	
		// Start our table
		$html = "
						<table" . $idBit . " border=\"" . $this->border . "\" cellpadding=\"" . $this->padding . "\" cellspacing=\"" . $this->spacing . "\"" . $classBit . ">";
		
		// Add our sections
		$html .= (count($this->tHeadRowData) > 0) ? "
							<thead" . $theadIDBit . ">
								" . $this->returnSectionHTML($this->tHeadRowData) . "
							</thead>" : "";
		$html .= "
							<tbody" . $tbodyIDBit . ">
								" . $this->returnSectionHTML($this->tBodyRowData) . "
							</tbody>";
		$html .= (count($this->tFootRowData) > 0) ? "
							<tfoot" . $tfootIDBit . ">
								" . $this->returnSectionHTML($this->tFootRowData) . "
							</tfoot>" : "";
		// Close the table
		$html .= "
						</table>";
		
		// Return the HTML
		return $html;
	}
} 

?>