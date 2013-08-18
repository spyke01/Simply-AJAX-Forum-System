/*-------------------------------------------------------------------------*/
// Ajax Functions
/*-------------------------------------------------------------------------*/	
invoicesProductRowNumber = 0;

function ajaxDeleteNotifier(spinDivID, action, text, row) {
    if (confirm("Are you sure you want to delete this " + text + "?")) {
		$('#' + spinDivID).toggle();	
		jQuery.get(action, function(data) { $('#' + row).hide('drop',{},500); });
	}
}

function ajaxQuickDivUpdate(action, divID, spinnerHTML) {
	jQuery.get(action, function(data) {
		// Clear the current graph and show the new one
		$('#' + divID).html(spinnerHTML);
		$('#' + divID).html(data);
	});
}

$.fn.clearForm = function() {
	return this.each(function() {
		var type = this.type, tag = this.tagName.toLowerCase();
		if (tag == 'form')
			return $(':input',this).clearForm();
		if (type == 'text' || type == 'password' || tag == 'textarea')
			this.value = '';
		else if (type == 'checkbox' || type == 'radio')
			this.checked = false;
		else if (tag == 'select')
			this.selectedIndex = -1;
	});
};

function returnSuccessMessage(itemName) { 
    return "<span class=\"greenText bold\">Successfully created " + itemName + "!</span>";
}

function ajaxToggleDiv(divID, imageID) {
	if ($('#' + divID).hasClass('hideMe')) {
		// Show Me
		$('#' + divID).removeClass('hideMe');
		if (imageID != '') { var src = $('#' + imageID).attr('src', imgCollapse); }
	} 
	else {
		// Hide Me
		$('#' + divID).addClass('hideMe');
		if (imageID != '') { var src = $('#' + imageID).attr('src', imgExpand); }
	}
	
	return false;
}

function checkAll(checkAllObj) {
	$('#manageMessagesForm :checkbox').each(function() {
		if (this.name != "checkAll") this.checked = checkAllObj.checked;
	});
}

function checkCheckAll() {
	var totalBoxes = 0;
	var totalOn = 0;
	
	$('#manageMessagesForm :checkbox').each(function() {
		if (this.name != "checkAll") {
			totalBoxes++;
			if (this.checked) {
				totalOn++;
			}
		}
	});
	
	if (totalBoxes == totalOn) { $('#checkAll').checked = true; }
	else { $('#checkAll').checked = false; }
}

function lockTopic(option, topicID) {		
	// Fill the box with a progress spinner
	$('#lockTopicMessage').html(progressSpinnerHTML);
		
	jQuery.get('ajax.php?action=lockTopic&id=' + topicID + '&value=' + option, function(data) {
		// Update the proper div with the returned data
		$('#lockTopicMessage').html(data);
		$('#lockTopicMessage').effect('highlight', {}, 3000);
		
		// Update our lock/unlock button
		$('.lockButton a span').html(((option == 'lock') ? 'Unlock Topic' : 'Lock Topic'));
		$('.lockButton a').attr('onclick', '').click(function() { lockTopic(((option == 'lock') ? 'unlock' : 'lock'), topicID); return false; });		
	});

	return false;	
}

function updateGalleryImages() {	
	// Fill the box with a progress spinner
	$('#avatarGalleryHolder').html(progressSpinnerHTML);
		
	jQuery.get('ajax.php?action=showGalleryImages&folder=' + $('#avatarGalleryFolder').val(), function(data) {
		// Update the proper div with the returned data
		$('#avatarGalleryHolder').html(data);
		$('#avatarGalleryHolder').effect('highlight', {}, 3000);		
	});

	return false;	
}