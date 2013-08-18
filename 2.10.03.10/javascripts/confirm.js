function confirmDelete(text) {
    return confirm("Are you sure you want to delete this "+ text +"?");
}
function ajaxDeleteNotifier(spinDivID, action, text, row) {
    if (confirm("Are you sure you want to delete this "+ text +"?")) {
		sqr_show_hide(spinDivID);
		new Ajax.Request(action, {asynchronous:true, onSuccess:function(){ new Effect.SlideUp(row);}});
	}
}