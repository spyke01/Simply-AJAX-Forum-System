// Startup variables
var imageTag = false;
var theSelection = false;
var textareaID = 'message';

// Check for Browser & Platform for PC & IE specific bits
// More details from: http://www.mozilla.org/docs/web-developer/sniffer/browser_type.html
var clientPC = navigator.userAgent.toLowerCase(); // Get client info
var clientVer = parseInt(navigator.appVersion); // Get browser version

var is_ie = ((clientPC.indexOf('msie') != -1) && (clientPC.indexOf('opera') == -1));
var is_win = ((clientPC.indexOf('win') != -1) || (clientPC.indexOf('16bit') != -1));
var baseHeight;

// Helpline messages
var help_line = {
	b: 'Bold text: [b]text[/b]',
	i: 'Italic text: [i]text[/i]',
	u: 'Underline text: [u]text[/u]',
	q: 'Quote text: [quote]text[/quote]',
	c: 'Code display: [code]code[/code]',
	l: 'List: [list]text[/list]',
	o: 'Ordered list: [list=1]text[/list]',
	p: 'Insert image: [img]http://image_url[/img]',
	w: 'Insert URL: [url]http://url[/url] or [url=http://url]URL text[/url]',
	s: 'Font colour: [color=red]text[/color]  Tip: you can also use color=#FF0000',
	f: 'Font size: [size=85]small text[/size]',
	e: 'List: Add list element',
	d: 'Flash: [flash=width,height]http://url[/flash]',
	email: 'Insert Email: [email]email address[/email] or [email=address]email address[/email]',
	left: 'Align text to the left: [align=left]text[/align]',
	center: 'Align text to the center: [align=center]text[/align]',
	right: 'Align text to the right: [align=right]text[/align]',
	php: 'PHP Code: [php]text[/php]',
	youtube: 'YouTube: [youtube]Video URL[/youtube]',
	gvideo: 'Google Video: [youtube]Video URL[/youtube]',
	sub: 'Subscript: [youtube]Video URL[/youtube]',
	sup: 'Superscript: [youtube]Video URL[/youtube]',
	justify: 'Justify Text: [align=justify]text[/align]',
	strike: 'Strikethrough text: [s]text[/s]'
}

// Define the bbCode tags
bbcode = new Array();
bbtags = new Array('[b]','[/b]','[i]','[/i]','[u]','[/u]','[quote]','[/quote]','[code]','[/code]','[list]','[/list]','[list=]','[/list]','[img]','[/img]','[url]','[/url]','[email]','[/email]','[php]','[/php]','[align=center]','[/align]','[align=left]','[/align]','[align=right]','[/align]','[youtube]','[/youtube]','[gvideo]','[/gvideo]','[sub]','[/sub]','[sup]','[/sup]','[align=justify]','[/align]','[s]','[/s]');

// Shows the help messages in the helpline window
function helpline(help) {
	$('#helpLine').text(help_line[help]);
}

/**
* bbstyle
*/
function bbstyle(bbnumber) {	
	if (bbnumber != -1) {
		bbfontstyle(bbtags[bbnumber], bbtags[bbnumber+1]);
	} 
	else {
		insert_text('[*]');
		$('#' + textareaID).focus();
	}
}

/**
* Apply bbcodes
*/
function bbfontstyle(bbopen, bbclose) {
	theSelection = false;

	var textarea = $('#' + textareaID);

	textarea.focus();

	if ((clientVer >= 4) && is_ie && is_win) {
		// Get text selection
		theSelection = document.selection.createRange().text;

		if (theSelection) {
			// Add tags around selection
			document.selection.createRange().text = bbopen + theSelection + bbclose;
			document.getElementById(textareaID).focus();
			theSelection = '';
			return;
		}
	}
	else if (document.getElementById(textareaID).selectionEnd && (document.getElementById(textareaID).selectionEnd - document.getElementById(textareaID).selectionStart > 0)) {
		mozWrap(document.getElementById(textareaID), bbopen, bbclose);
		document.getElementById(textareaID).focus();
		theSelection = '';
		return;
	}
	
	//The new position for the cursor after adding the bbcode
	var caret_pos = getCaretPosition(textarea).start;
	var new_pos = caret_pos + bbopen.length;		

	// Open tag
	insert_text(bbopen + bbclose);

	// Center the cursor when we don't have a selection
	// Gecko and proper browsers
	if (!isNaN(textarea.selectionStart)) {
		textarea.selectionStart = new_pos;
		textarea.selectionEnd = new_pos;
	}	
	// IE
	else if (document.selection) {
		var range = textarea.createTextRange(); 
		range.move("character", new_pos); 
		range.select();
		storeCaret(textarea);
	}

	textarea.focus();
	return;
}

/**
* Insert text at position
*/
function insert_text(text, spaces, popup) {
	var textarea;
	
	if (!popup) {
		textarea = document.getElementById(textareaID);
	} 
	else {
		textarea = opener.document.getElementById(textareaID);
	}
	if (spaces) {
		text = ' ' + text + ' ';
	}
	
	if (!isNaN(textarea.selectionStart)) {
		var sel_start = textarea.selectionStart;
		var sel_end = textarea.selectionEnd;

		mozWrap(textarea, text, '');
		textarea.selectionStart = sel_start + text.length;
		textarea.selectionEnd = sel_end + text.length;
	}
	else if (textarea.createTextRange && textarea.caretPos) {
		if (baseHeight != textarea.caretPos.boundingHeight) 
		{
			textarea.focus();
			storeCaret(textarea);
		}

		var caret_pos = textarea.caretPos;
		caret_pos.text = caret_pos.text.charAt(caret_pos.text.length - 1) == ' ' ? caret_pos.text + text + ' ' : caret_pos.text + text;
	}
	else {
		textarea.value = textarea.value + text;
	}
	if (!popup) {
		textarea.focus();
	}
}

function mozWrap(txtarea, open, close) {
	var selLength = (typeof(txtarea.textLength) == 'undefined') ? txtarea.value.length : txtarea.textLength;
	var selStart = txtarea.selectionStart;
	var selEnd = txtarea.selectionEnd;
	var scrollTop = txtarea.scrollTop;

	if (selEnd == 1 || selEnd == 2) {
		selEnd = selLength;
	}

	var s1 = (txtarea.value).substring(0,selStart);
	var s2 = (txtarea.value).substring(selStart, selEnd);
	var s3 = (txtarea.value).substring(selEnd, selLength);

	txtarea.value = s1 + open + s2 + close + s3;
	txtarea.selectionStart = selStart + open.length;
	txtarea.selectionEnd = selEnd + open.length;
	txtarea.focus();
	txtarea.scrollTop = scrollTop;

	return;
}

// From http://www.faqts.com/knowledge_base/view.phtml/aid/1052/fid/130
function storeCaret(textEl) {
	if (textEl.createTextRange) {
		textEl.caretPos = document.selection.createRange().duplicate();
	}
}

function caretPosition() {
	var start = null;
	var end = null;
}

/**
* Get the caret position in an textarea
*/
function getCaretPosition(txtarea) {
	var caretPos = new caretPosition();
	
	// simple Gecko/Opera way
	if(txtarea.selectionStart || txtarea.selectionStart == 0) {
		caretPos.start = txtarea.selectionStart;
		caretPos.end = txtarea.selectionEnd;
	}
	// dirty and slow IE way
	else if(document.selection) {
	
		// get current selection
		var range = document.selection.createRange();

		// a new selection of the whole textarea
		var range_all = document.body.createTextRange();
		range_all.moveToElementText(txtarea);
		
		// calculate selection start point by moving beginning of range_all to beginning of range
		var sel_start;
		for (sel_start = 0; range_all.compareEndPoints('StartToStart', range) < 0; sel_start++) {		
			range_all.moveStart('character', 1);
		}
	
		txtarea.sel_start = sel_start;
	
		// we ignore the end value for IE, this is already dirty enough and we don't need it
		caretPos.start = txtarea.sel_start;
		caretPos.end = txtarea.sel_start;			
	}

	return caretPos;
}

