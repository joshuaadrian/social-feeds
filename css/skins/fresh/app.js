jQuery(document).ready(function($) {

	$('body').append('<div class="sf-modal"><div class="sf-modal-inner"></div></div>');

	$('.instagram-link').live('click', function() {
		var id = $(this).parent().attr('id');
		$('.sf-modal-inner').html('<img src="' + $(this).attr('href') + '" alt="" class="instagram-large" />' + $('#' + id + '-details').html()).attr('id', id);
		$('.sf-modal').fadeIn('fast');
		return false;
	});

	$('.sf-modal').live('click', function() {
		$('.sf-modal').fadeOut('fast');
		return false;
	});

	$('.sf-modal-inner').live('click', function() {
		var instagram_length = $('.instagram').length - 1;
		var new_id = $(this).attr('id');
		new_id = new_id.substr(new_id.indexOf("-") + 1);
		if (new_id >= instagram_length) {
			new_id = 0;
		} else {
			new_id++;
		}
		$(this).html('<img src="' + $('#instagram-' + new_id + ' .instagram-link').attr('href') + '" alt="" class="instagram-large" />' + $('#instagram-' + new_id + '-details').html()).attr('id', 'instagram-' + new_id);
		return false;
	});

});