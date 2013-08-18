<?
/***************************************************************************
 *                               join.php
 *                            -------------------
 *   begin                : Saturday, Sept 24, 2005
 *   copyright            : (C) 2005 Paden Clayton - Fast Track Sites
 *   email                : sales@fasttacksites.com
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


	$content .= " 
	<center>
	<div id=\"formHolder\">
	<form name=\"form1\" method=\"post\" action=\"" . $menuvar['JOIN'] . "&action=newUser\" onSubmit=\"return ValidateForm(this)\">
			<table class=\"JForumBorder\" border=\"0\" cellpadding=\"0\" cellspacing=\"1\">
				<tr class=\"title1\">
					<td class=\"JT1\" colspan=\"2\">Test</td>
				</tr>
				<tr class=\"row1\"> 
					<td class=\"JT2Column1\">URL to Test</td>
					<td class=\"JR1Column1\"><div id=\"urlCheckerHolder\" style=\"float: right;\"><a style=\"cursor: pointer; cursor: hand; color: red;\" onclick=\"new Ajax.Updater('urlCheckerHolder', 'ajax.php?action=checkurl&value=' + document.form1.url.value, {asynchronous:true});\">[Check]</a></div><input name=\"url\" type=\"text\" size=\"60\" id=\"url\" /></td>
				</tr>
			</table>
	</form>
	</div>
	</center>";	

$page->setTemplateVar("PageContent", $content);
?>