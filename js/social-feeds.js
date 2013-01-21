jQuery(document).ready(function($) {

	$('.sf_pagination li a').live('click', function() {
		if (!$(this).parent().hasClass('sf-active')) {
			$('.sf_pagination li').removeClass('sf-active');
			$('.sf_content li').removeClass('sf-active');
			$(this).parent().addClass('sf-active');
			$($(this).attr('href')).addClass('sf-active');
		}

		return false;
	});

});