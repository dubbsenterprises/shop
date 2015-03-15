$(document).ready(function() {
	$('ul#filter a').click(function() {
		var filter_a = $(this);

		filter_a.css('outline','none');
		$('ul#filter .current').removeClass('current');
		filter_a.parent().addClass('current');

		var filterVal = filter_a.text().toLowerCase().replace(' ','-');

		if(filterVal === 'all') {
			$('ul#portfolio li.hidden').fadeIn('slow').removeClass('hidden');
		} else {
			$('ul#portfolio li').each(function() {
				var portfolio_li = $(this);

				if(!portfolio_li.hasClass(filterVal)) {
					portfolio_li.fadeOut('normal').addClass('hidden');
				} else {
					portfolio_li.fadeIn('slow').removeClass('hidden');
				}
			});
		}

		return false;
	});
});