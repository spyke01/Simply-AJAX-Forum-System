//------------------------------------------
// SSForum v2.0
// Global JS File
// (c) 2005 WNStudios.
//
// http://spyke01.mfhosting.com
//------------------------------------------

//==========================================
// Set up
//==========================================

// Sniffer based on http://www.mozilla.org/docs/web-developer/sniffer/browser_type.html

var uagent    = navigator.userAgent.toLowerCase();
var is_safari = ( (uagent.indexOf('safari') != -1) || (navigator.vendor == "Apple Computer, Inc.") );
var is_opera  = (uagent.indexOf('opera') != -1);
var is_webtv  = (uagent.indexOf('webtv') != -1);
var is_ie     = ( (uagent.indexOf('msie') != -1) && (!is_opera) && (!is_safari) && (!is_webtv) );
var is_ie4    = ( (is_ie) && (uagent.indexOf("msie 4.") != -1) );
var is_moz    = ( (navigator.product == 'Gecko')  && (!is_opera) && (!is_webtv) && (!is_safari) );
var is_ns     = ( (uagent.indexOf('compatible') == -1) && (uagent.indexOf('mozilla') != -1) && (!is_opera) && (!is_webtv) && (!is_safari) );
var is_ns4    = ( (is_ns) && (parseInt(navigator.appVersion) == 4) );
var is_kon    = (uagent.indexOf('konqueror') != -1);

var is_win    =  ( (uagent.indexOf("win") != -1) || (uagent.indexOf("16bit") !=- 1) );
var is_mac    = ( (uagent.indexOf("mac") != -1) || (navigator.vendor == "Apple Computer, Inc.") );
var ua_vers   = parseInt(navigator.appVersion);

var ssf_pages_shown = 0;
var ssf_pages_array = new Array();


/*-------------------------------------------------------------------------*/
// Get cookie
/*-------------------------------------------------------------------------*/

function my_getcookie( name )
{
	cname = ssf_var_cookieid + name + '=';
	cpos  = document.cookie.indexOf( cname );
	
	if ( cpos != -1 )
	{
		cstart = cpos + cname.length;
		cend   = document.cookie.indexOf(";", cstart);
		
		if (cend == -1)
		{
			cend = document.cookie.length;
		}
		
		return unescape( document.cookie.substring(cstart, cend) );
	}
	
	return null;
}

/*-------------------------------------------------------------------------*/
// Set cookie
/*-------------------------------------------------------------------------*/

function my_setcookie( name, value, sticky )
{
	expire = "";
	domain = "";
	path   = "/";
	
	if ( sticky )
	{
		expire = "; expires=Wed, 1 Jan 2020 00:00:00 GMT";
	}
	
	if ( ssf_var_cookie_domain != "" )
	{
		domain = '; domain=' + ssf_var_cookie_domain;
	}
	
	if ( ssf_var_cookie_path != "" )
	{
		path = ssf_var_cookie_path;
	}
	
	document.cookie = ssf_var_cookieid + name + "=" + value + "; path=" + path + expire + domain + ';';
}


/*-------------------------------------------------------------------------*/
// locationjump
/*-------------------------------------------------------------------------*/

function locationjump(url)
{
	window.location = ssforum_var_base_url + url;
}

/*-------------------------------------------------------------------------*/
// PRIVATE MESSAGE ACTION
/*-------------------------------------------------------------------------*/

function pmaction(obj)
{
	pmbox = obj.options[obj.selectedIndex].value;

		locationjump( 'privmsgs.php?action=' + pmbox );

}


/*-------------------------------------------------------------------------*/
// CHECKBOX FUNCTION FOR CHECKING ALL CHECKBOXES
/*-------------------------------------------------------------------------*/
var ie  = document.all  ? 1 : 0;
//var ns4 = document.layers ? 1 : 0;
function hl(cb)
{
   if (ie)
   {
	   while (cb.tagName != "TR")
	   {
		   cb = cb.parentElement;
	   }
   }
   else
   {
	   while (cb.tagName != "TR")
	   {
		   cb = cb.parentNode;
	   }
   }
   cb.className = 'hlight';
}
function dl(cb) {
   if (ie)
   {
	   while (cb.tagName != "TR")
	   {
		   cb = cb.parentElement;
	   }
   }
   else
   {
	   while (cb.tagName != "TR")
	   {
		   cb = cb.parentNode;
	   }
   }
   cb.className = 'colour1';
}
function cca(cb) {
   if (cb.checked)
   {
	   hl(cb);
   }
   else
   {
	   dl(cb);
   }
}
	   
function CheckAll(cb) {
	var fmobj = document.multiact;
	for (var i=0;i<fmobj.elements.length;i++) {
		var e = fmobj.elements[i];
		if ((e.name != 'allbox') && (e.type=='checkbox') && (!e.disabled)) {
			e.checked = fmobj.allbox.checked;
			if (fmobj.allbox.checked)
			{
			   hl(e);
			}
			else
			{
			   dl(e);
			}
		}
	}
}
function CheckCheckAll(cb) {	
	var fmobj = document.multiact;
	var TotalBoxes = 0;
	var TotalOn = 0;
	for (var i=0;i<fmobj.elements.length;i++) {
		var e = fmobj.elements[i];
		if ((e.name != 'allbox') && (e.type=='checkbox')) {
			TotalBoxes++;
			if (e.checked) {
				TotalOn++;
			}
		}
	}
	if (TotalBoxes==TotalOn) {fmobj.allbox.checked=true;}
	else {fmobj.allbox.checked=false;}
}
function select_read() {	
	var fmobj = document.multiact;
	for (var i=0;i<fmobj.elements.length;i++) {
		var e = fmobj.elements[i];
		if ((e.type=='hidden') && (e.value == 1) && (! isNaN(e.name) ))
		{
			eval("fmobj.msgid_" + e.name + ".checked=true;");
			hl(e);
		}
	}
}
function unselect_all() {	
	var fmobj = document.multiact;
	for (var i=0;i<fmobj.elements.length;i++) {
		var e = fmobj.elements[i];
		if (e.type=='checkbox') {
			e.checked=false;
			dl(e);
		}
	}
}

function sqr_show_hide(id) {
	var item = null;

	if (document.getElementById) {
		item = document.getElementById(id);
	}
	else if (document.all) {
		item = document.all[id];
	}
	else if (document.layers) {
		item = document.layers[id];
	}

	if (item && item.style) {
		if (item.style.display == "none") {
			item.style.display = "";
		}
		else {
			item.style.display = "none";
		}
	}
	else if (item) {
		item.visibility = "show";
	}
}