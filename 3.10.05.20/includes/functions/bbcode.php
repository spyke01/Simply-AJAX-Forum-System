<?php 
/***************************************************************************
 *                               bbcode.php
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
	// BBCode Functions
	// Use the function like so: echo bbcode($string);
	//=================================================
	Function bbcode($str){
		// Makes < and > page friendly
		//$str=str_replace("&","&amp;",$str);
		$str=str_replace("<","&lt;",$str);
		$str=str_replace(">","&gt;",$str);
		
		// Alig text
		$str = preg_replace("#\[align=(left|center|justify|right)\](.*?)\[/align\]#si", "<div align=\"\\1\">\\2</div>", $str); 
		
		// Bold
		$str = preg_replace("#\[b\](.*?)\[/b\]#si", "<strong>\\1</strong>", $str);
		
		// Code
		$str = preg_replace("#\[code\](.*?)\[/code]#si", "<div class=\"codetop\"><u><strong>Code:</strong></u></div><div class=\"codemain\">\\1</div>", $str);
		
		// Code, Provide Author
		$str = preg_replace("#\[code=(.*?)\](.*?)\[/code]#si", "<div class=\"codetop\"><u><strong>Code \\1:</strong></u></div><div class=\"codemain\">\\2</div>", $str);
				
		// Email
		$str = preg_replace("#\[email\]([a-z0-9\-_.]+?)@([\w\-]+\.([\w\-\.]+\.)?[\w]+)\[/email\]#i", "<a href=\"mailto:\\1@\\2\">\\1@\\2</a>", $str);
		
		// Email, Provide Author
		$str = preg_replace("#\[email=([a-z0-9\-_.]+?)@([\w\-]+\.([\w\-\.]+\.)?[\w]+)?(.*?)\](.*?)\[/email\]#i", "<a href=\"mailto:\\1@\\2\">\\5</a>", $str);
		
		// Font Color
		$str = preg_replace("#\[color=(.*?)\](.*?)\[/color\]#si", "<span style=\"color: \\1\">\\2</span>", $str);
		
		// Font Size
		$str = preg_replace("#\[size=(.*?)\](.*?)\[/size\]#si", "<span style=\"font-size:\\1\">\\2</span>", $str);
		
		// Google Video
		$str = preg_replace("#\[gvideo\]http://video.google.[A-Za-z0-9.]{2,5}/videoplay\?docid=([0-9A-Za-z-_]*)[^[]*\[/gvideo\]#si", "<object width=\"425\" height=\"350\"><param name=\"movie\" value=\"http://video.google.com/googleplayer.swf\?docId=\\1\"></param><param name=\"wmode\" value=\"transparent\"></param><embed src=\"http://video.google.com/googleplayer.swf\?docId=\\1\" type=\"application/x-shockwave-flash\" allowScriptAccess=\"sameDomain\" quality=\"best\" bgcolor=\"#ffffff\" scale=\"noScale\" salign=\"TL\"  FlashVars=\"playerMode=embedded\" wmode=\"transparent\" width=\"425\" height=\"350\"></embed></object>", $str);
		
		// HR Line
		$str = preg_replace("#\[hr=(\d{1,2}|100)\]#si", "<hr class=\"linia\" width=\"\\1%\" />", $str);
		
		// Image
		$str = preg_replace("#\[img\](.*?)\[/img\]#si", "<img src=\"\\1\" border=\"0\" alt=\"\" />", $str);
		
		// Italics
		$str = preg_replace("#\[i\](.*?)\[/i\]#si", "<em>\\1</em>", $str);
				
		// Link inside tags new window
		$str = preg_replace("#\[url\](.*?)?(.*?)\[/url\]#si", "<a href=\"\\1\\2\" target=\"_blank\">\\1\\2</a>", $str);
		
		// Link inside first tag new window
		$str = preg_replace("#\[url=(.*?)?(.*?)\](.*?)\[/url\]#si", "<a href=\"\\2\" target=\"_blank\">\\3</a>", $str);
		
		// Link inside tags
		$str = preg_replace("#\[url2\](.*?)?(.*?)\[/url2\]#si", "<a href=\"\\1\\2\">\\1\\2</a>", $str);
		
		// Link inside first tag
		$str = preg_replace("#\[url2=(.*?)?(.*?)\](.*?)\[/url2\]#si", "<a href=\"\\2\">\\3</a>", $str);
		
		// Automatic links if no url tags used
		$str = preg_replace_callback("#([\n ])([a-z]+?)://([a-z0-9\-\.,\?!%\*_\#:;~\\&$@\/=\+]+)#si", "bbcode_autolink", $str);
		$str = preg_replace("#([\n ])www\.([a-z0-9\-]+)\.([a-z0-9\-.\~]+)((?:/[a-z0-9\-\.,\?!%\*_\#:;~\\&$@\/=\+]*)?)#i", " <a href=\"http://www.\\2.\\3\\4\" target=\"_blank\">www.\\2.\\3\\4</a>", $str);
		$str = preg_replace("#([\n ])([a-z0-9\-_.]+?)@([\w\-]+\.([\w\-\.]+\.)?[\w]+)#i", "\\1<a href=\"mailto: \\2@\\3\">\\2_(at)_\\3</a>", $str);
		
		// Ordered List
		$str = preg_replace( "#\[list\](.+?)\[/list\]#si", "<ul>" . bbcode_list_item('\\1') . "</ul>", $str );
		$str = preg_replace( "#\[list=(a|A|i|I|1)\](.+?)\[/list\]#si", "<ol type='\\1'>" . bbcode_list_item('\\2') . "</ol>", $str );

		// PHP Code
		$str = preg_replace("#\[php\](.*?)\[/php]#si", "<div class=\"codetop\"><u><strong>&lt?PHP:</strong></u></div><div class=\"codemain\">\\1</div>", $str);
		
		// Quote
		$str = preg_replace("#\[quote\](.*?)\[/quote]#si", "<div class=\"quotetop\"><u><strong>Quote:</strong></u></div><div class=\"quotemain\">\\1</div>", $str);
		
		// Quote, Provide Author
		$str = preg_replace("#\[quote=(.*?)\](.*?)\[/quote]#si", "<div class=\"quotetop\"><u><strong>Quote \\1:</strong></u></div><div class=\"quotemain\">\\2</div>", $str);
		
		// Subscript
		$str = preg_replace("#\[sub\](.*?)\[/sub\]#si", "<sub>\\1</sub>", $str);
		
		// Superscript
		$str = preg_replace("#\[sup\](.*?)\[/sup\]#si", "<sup>\\1</sup>", $str);
		
		// Strikethrough
		$str = preg_replace("#\[s\](.*?)\[/s\]#si", "<strike>\\1</strike>", $str);
		
		// Unordered List
		// Make this
		
		// Underline
		$str = preg_replace("#\[u\](.*?)\[/u\]#si", "<u>\\1</u>", $str);
		
		// YouTube
		$str = preg_replace("#\[youtube\]http://(?:www\.)?youtube.com/v/([0-9A-Za-z-_]{11})[^[]*\[/youtube\]#si", "<object width=\"425\" height=\"350\"><param name=\"movie\" value=\"http://www.youtube.com/v/\\1\"></param><param name=\"wmode\" value=\"transparent\"></param><embed src=\"http://www.youtube.com/v/\\1\" type=\"application/x-shockwave-flash\" wmode=\"transparent\" width=\"425\" height=\"350\"></embed></object>", $str);
		$str = preg_replace("#\[youtube\]http://(?:www\.)?youtube.com/watch\?v=([0-9A-Za-z-_]{11})[^[]*\[/youtube\]#si", "<object width=\"425\" height=\"350\"><param name=\"movie\" value=\"http://www.youtube.com/v/\\1\"></param><param name=\"wmode\" value=\"transparent\"></param><embed src=\"http://www.youtube.com/v/\\1\" type=\"application/x-shockwave-flash\" wmode=\"transparent\" width=\"425\" height=\"350\"></embed></object>", $str);
		
		// change \n to <br />
		$str = nl2br($str);
		
		// Run the word filters
		$str = runWordFilters($str);
		
		// return bbdecoded string
		return $str;
	}
	
	//=================================================
	// Used by our bbcode() function for generating links
	//=================================================
	function bbcode_autolink($str) {
		$lnk = $str[3];
		
		if (strlen($lnk) > 30) {
			if (substr($lnk, 0, 3) == "www") { $l = 9; }
			else{ $l = 5; }
			$lnk = substr($lnk, 0, $l) . "(...)" . substr($lnk, strlen($lnk) - 8);
		}
		
		return "<a href=\"" . $str[2] . "://" . $str[3] . "\" target=\"_blank\">" . $str[2] . "://" . $lnk . "</a>";
	}
	
	//=================================================
	// Used by our bbcode() function for generating list items
	//=================================================
	function bbcode_list_item($str) {
		$str = preg_replace("#\[\*\]#", "</li><li>", trim($str));
		$str = preg_replace("#^</?li>#", "", $str);
				
		return str_replace("\n</li>", "</li>", $str . "</li>");
	}

	//==================================================
	// Prints out a lovely little bbcode button box
	// Keeps me from having to redo several pages
	//==================================================
	function bbcode_box($currentContent = "") {
		global $DB, $safs_config, $themeImages;
		
		$returnVar = "	
									<div id=\"smiliesBlock\">
										<ul>";
		
		$sql = "SELECT * FROM `" . DBTABLEPREFIX . "smilies`";
		$result = $DB->query($sql);
		
		// Add our data
		if ($result && $DB->num_rows() > 0) {
			while ($row = $DB->fetch_array($result)) {
				$returnVar .= "
											<li><img src=\"" . $row['image'] . "\" alt=\"\" onclick=\"insert_text('" . $row['code'] . "', true); return false;\" /></a></li>";
			}
			$DB->free_result($result);
		}
		
		$returnVar .= "			
										</ul>
									</div>
									<div id=\"messageWrapper\">
										<div id=\"bbcodeBlock\">
											<ul>
												<li><img src=\"themes/" . $safs_config['ftssafs_theme'] . "/" . $themeImages['bbcode']['bold'] . "\" alt=\"Bold\" title=\"Bold\" onclick=\"bbstyle(0)\" onmouseover=\"helpline('b')\" /></li>
												<li><img src=\"themes/" . $safs_config['ftssafs_theme'] . "/" . $themeImages['bbcode']['italic'] . "\" alt=\"Italic\" title=\"Italic\" onclick=\"bbstyle(2)\" onmouseover=\"helpline('i')\" /></li>
												<li><img src=\"themes/" . $safs_config['ftssafs_theme'] . "/" . $themeImages['bbcode']['underline'] . "\" alt=\"Underline\" title=\"Underline\" onclick=\"bbstyle(4)\" onmouseover=\"helpline('u')\" /></li>
												<li class=\"sep\"><img src=\"themes/" . $safs_config['ftssafs_theme'] . "/" . $themeImages['bbcode']['strikethrough'] . "\" alt=\"Strikethrough\" title=\"Strikethrough\" onclick=\"bbstyle(38)\" onmouseover=\"helpline('strike')\" /></li>
												<li><img src=\"themes/" . $safs_config['ftssafs_theme'] . "/" . $themeImages['bbcode']['subscript'] . "\" alt=\"Subscript\" title=\"Subscript\" onclick=\"bbstyle(32)\" onmouseover=\"helpline('sub')\" /></li>
												<li class=\"sep\"><img src=\"themes/" . $safs_config['ftssafs_theme'] . "/" . $themeImages['bbcode']['superscript'] . "\" alt=\"Superscript\" title=\"Superscript\" onclick=\"bbstyle(34)\" onmouseover=\"helpline('sup')\" /></li>
												<li><img src=\"themes/" . $safs_config['ftssafs_theme'] . "/" . $themeImages['bbcode']['left'] . "\" alt=\"Align Text To The Left\" title=\"Align Text To The Left\" onclick=\"bbstyle(24)\" onmouseover=\"helpline('left')\" /></li>
												<li><img src=\"themes/" . $safs_config['ftssafs_theme'] . "/" . $themeImages['bbcode']['center'] . "\" alt=\"Align Text To The Center\" title=\"Align Text To The Center\" onclick=\"bbstyle(22)\" onmouseover=\"helpline('center')\" /></li>
												<li><img src=\"themes/" . $safs_config['ftssafs_theme'] . "/" . $themeImages['bbcode']['right'] . "\" alt=\"Align Text To The Right\" title=\"Align Text To The Right\" onclick=\"bbstyle(26)\" onmouseover=\"helpline('right')\" /></li>
												<li class=\"sep\"><img src=\"themes/" . $safs_config['ftssafs_theme'] . "/" . $themeImages['bbcode']['justify'] . "\" alt=\"Justify Text\" title=\"Justify Text\" onclick=\"bbstyle(36)\" onmouseover=\"helpline('justify')\" /></li>
												<li><img src=\"themes/" . $safs_config['ftssafs_theme'] . "/" . $themeImages['bbcode']['olist'] . "\" alt=\"Insert List\" title=\"Insert List\" onclick=\"bbstyle(10)\" onmouseover=\"helpline('l')\" /></li>
												<li class=\"sep\"><img src=\"themes/" . $safs_config['ftssafs_theme'] . "/" . $themeImages['bbcode']['ulist'] . "\" alt=\"Insert List\" title=\"Insert List\" onclick=\"bbstyle(12)\" onmouseover=\"helpline('o')\" /></li>
												<li><img src=\"themes/" . $safs_config['ftssafs_theme'] . "/" . $themeImages['bbcode']['link'] . "\" alt=\"Insert Link\" title=\"Insert Link\" onclick=\"bbstyle(16)\" onmouseover=\"helpline('w')\" /></li>
												<li><img src=\"themes/" . $safs_config['ftssafs_theme'] . "/" . $themeImages['bbcode']['email'] . "\" alt=\"Insert Email\" title=\"Insert Email\" onclick=\"bbstyle(18)\" onmouseover=\"helpline('email')\" /></li>
												<li><img src=\"themes/" . $safs_config['ftssafs_theme'] . "/" . $themeImages['bbcode']['image'] . "\" alt=\"Insert Image\" title=\"Insert Image\" onclick=\"bbstyle(14)\" onmouseover=\"helpline('p')\" /></li>
												<li class=\"sep\"><img src=\"themes/" . $safs_config['ftssafs_theme'] . "/" . $themeImages['bbcode']['quote'] . "\" alt=\"Wrap in a Quote\" title=\"Wrap in a Quote\" onclick=\"bbstyle(6)\" onmouseover=\"helpline('q')\" /></li>
												<li><img src=\"themes/" . $safs_config['ftssafs_theme'] . "/" . $themeImages['bbcode']['code'] . "\" code=\"\" title=\"Code\" onclick=\"bbstyle(8)\" onmouseover=\"helpline('c')\" /></li>
												<li class=\"sep\"><img src=\"themes/" . $safs_config['ftssafs_theme'] . "/" . $themeImages['bbcode']['php'] . "\" code=\"\" title=\"PHP\" onclick=\"bbstyle(20)\" onmouseover=\"helpline('php')\" /></li>
												<li><img src=\"themes/" . $safs_config['ftssafs_theme'] . "/" . $themeImages['bbcode']['youtube'] . "\" code=\"\" title=\"YouTube\" onclick=\"bbstyle(28)\" onmouseover=\"helpline('youtube')\" height=\"20\" width=\"20\" /></li>
												<li><img src=\"themes/" . $safs_config['ftssafs_theme'] . "/" . $themeImages['bbcode']['googlevideo'] . "\" code=\"\" title=\"Google Video\" onclick=\"bbstyle(30)\" onmouseover=\"helpline('gvideo')\" height=\"20\" width=\"20\" /></li>
											</ul>
											<ul>
												<li>
						 							Font colour: 
													<select name=\"fontcolor\" onchange=\"bbfontstyle('[color=' + $(this).selectedIndex.value + ']', '[/color]');$(this).selectedIndex=0;\" onmouseover=\"helpline('s')\">
														<option style=\"color: black; background-color: #FAFAFA\" value=\"#444444\">Default</option>
														<option style=\"color: darkred; background-color: #FAFAFA\" value=\"darkred\">Dark Red</option>
														<option style=\"color: red; background-color: #FAFAFA\" value=\"red\">Red</option>
														<option style=\"color: orange; background-color: #FAFAFA\" value=\"orange\">Orange</option>
														<option style=\"color: brown; background-color: #FAFAFA\" value=\"brown\">Brown</option>
														<option style=\"color: yellow; background-color: #FAFAFA\" value=\"yellow\">Yellow</option>
														<option style=\"color: green; background-color: #FAFAFA\" value=\"green\">Green</option>
														<option style=\"color: olive; background-color: #FAFAFA\" value=\"olive\">Olive</option>
														<option style=\"color: cyan; background-color: #FAFAFA\" value=\"cyan\">Cyan</option>
														<option style=\"color: blue; background-color: #FAFAFA\" value=\"blue\">Blue</option>
														<option style=\"color: darkblue; background-color: #FAFAFA\" value=\"darkblue\">Dark Blue</option>
														<option style=\"color: indigo; background-color: #FAFAFA\" value=\"indigo\">Indigo</option>
														<option style=\"color: violet; background-color: #FAFAFA\" value=\"violet\">Violet</option>
														<option style=\"color: white; background-color: #FAFAFA\" value=\"white\">White</option>
														<option style=\"color: black; background-color: #FAFAFA\" value=\"black\">Black</option>
													</select>
												</li>
												<li>
						 							Font size: 
													<select name=\"fontsize\" onchange=\"bbfontstyle('[size=' + $(this).selectedIndex.value + ']', '[/size]')\" onmouseover=\"helpline('f')\">
														<option value=\"7\">Tiny</option>
														<option value=\"9\">Small</option>
														<option value=\"12\" selected>Normal</option>
														<option value=\"18\">Large</option>
														<option  value=\"24\">Huge</option>
													</select>
												</li>
											</ul>
											<span id=\"helpLine\">&nbsp;</span>
										</div>
										<div id=\"messageBlock\"><textarea name=\"message\" id=\"message\" cols=\"100\" rows=\"10\">" . $currentContent . "</textarea></div>
									</div>";
		
		// Return the HTML
		return $returnVar;
	}

?>