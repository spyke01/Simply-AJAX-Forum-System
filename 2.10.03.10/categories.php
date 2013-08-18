<? 
/***************************************************************************
 *                               categories.php
 *                            -------------------
 *   begin                : Tuseday, March 14, 2006
 *   copyright            : (C) 2006 Fast Track Sites
 *   email                : sales@fasttracksites.com
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
if ($_SESSION['user_level'] == ADMIN) {
	
	//==================================================
	// Print out our categories table
	//==================================================
	$sql = "SELECT * FROM `" . $DBTABLEPREFIX . "categories` ORDER BY cat_name ASC";
	$result = mysql_query($sql);
		
	$x = '1'; //reset the variable we use for our row colors	
		
	$content = "
				<div id=\"updateMe\">
					<table class=\"contentBox\" cellpadding=\"1\" cellspacing=\"1\" width=\"100%\">
						<tr>
							<td  class=\"title1\" colspan=\"2\">
								<div style='float: right;'>
									<form name=\"newCatForm\" action=\"$PHP_SELF\" method=\"post\" onSubmit=\"ValidateForm(this); return false;\">
										<input type=\"text\" name=\"newcatname\" />
										<input type=\"image\" src='images/plus.png' style='width: 15px; height: 15px; border:0px;' />
									</form>
								</div>
								Categories
							</td>
						</tr>							
						<tr  class=\"title2\">
							<td><b>Name</b></td><td></td>
						</tr>";
	$catids = array();
	if (mysql_num_rows($result) == "0") { // No cats yet!
		$content .= "\n					<tr class=\"greenRow\">";
		$content .= "\n						<td colspan=\"2\">There are no categories in the database.";
		$content .= "\n					</tr>";	
	}
	else {	 // Print all our cats								
		while ($row = mysql_fetch_array($result)) {
			
			$content .=	"					
								<tr id=\"$row[cat_id]\" class=\"row$x\">
									<td><div id=\"$row[cat_id]_text\">$row[cat_name]</div></td>
									<td>
										<center><a style=\"cursor: pointer; cursor: hand;\" onclick=\"new Ajax.Request('ajax.php?action=deleteitem&table=categories&id=$row[cat_id]', {asynchronous:true, onSuccess:function(){ new Effect.SlideUp('$row[cat_id]');}});\"><img src=\"images/x.png\" alt='Delete Category' style='width: 15px; height: 15px; border: 0px;' /></a></center>
									</td>
								</tr>";
			$catids[$row[cat_id]] = $row[cat_name];					
			$x = ($x=='2') ? '1' : '2';
		}
	}
	mysql_free_result($result);
		
	
	$content .=		"					</table>";
	$content .= "\n						<script type=\"text/javascript\">";
	
	$x = '1'; //reset the variable we use for our highlight colors
	foreach($catids as $key => $value) {
		$content .= ($x == 1) ? "\n							new Ajax.InPlaceEditor('" . $key . "_text', 'ajax.php?action=updateitem&table=categories&item=name&id=" . $key . "', {rows:1,cols:50,highlightcolor:'#CBD5DC',highlightendcolor:'#5194B6',loadTextURL:'ajax.php?action=getitem&table=categories&item=name&id=" . $key . "'});" : "\n							new Ajax.InPlaceEditor('" . $key . "_text', 'ajax.php?action=updatecat&id=" . $key . "', {rows:1,cols:50,highlightcolor:'#5194B6',highlightendcolor:'#CBD5DC',loadTextURL:'ajax.php?action=getitem&table=categories&item=name&id=" . $key . "'});";
		$x = ($x=='2') ? '1' : '2';
	}
	
	$content .= "\n						</script>";	
	$content .=		"				</div>";
	
	$content .= "\n	<script language = \"Javascript\">
		
		function ValidateForm(theForm){
			var name=document.newCatForm.newcatname
			
			if ((name.value==null)||(name.value==\"\")){
				alert(\"Please enter the new categories name.\")
				name.focus()
				return false
			}
			new Ajax.Updater('updateMe', 'ajax.php?action=postcat', {onComplete:function(){ new Effect.Highlight('newCat');},asynchronous:true, parameters:Form.serialize(theForm), evalScripts:true}); 
			name.value = '';
			return false;
		 }
		</script>";	

	$page->setTemplateVar("PageContent", $content);
}
else {
	$page->setTemplateVar("PageContent", "\nYou Are Not Authorized To Access This Area. Please Refrain From Trying To Do So Again.");
}
?>