jQuery(function($) {
	$('.selected_days').click(function() {
		var $opt = $(this).children('input:checked');
		var icon_price = parseInt($opt.attr('data-icon-price'));
		var icon_srl = $opt.attr('data-icon-srl');
		var day_price = parseInt($opt.attr('data-day-price'));
		var total_price = icon_price + day_price;

		jQuery('#icon_price_' + icon_srl).html(icon_price + 'P');
		jQuery('#day_price_' + icon_srl).html(day_price + 'P');
		jQuery('#total_icon_price_' + icon_srl).html(total_price + 'P');
	});

	$('.g_selected_days').click(function () {
		var $opt = $(this).children('input:checked');
		var icon_price = parseInt($opt.attr('data-icon-price'));
		var icon_srl = $opt.attr('data-icon-srl');
		var day_price = parseInt($opt.attr('data-day-price'));
		var total_price = icon_price + day_price;

		jQuery('#g_icon_price_' + icon_srl).html(icon_price + 'P');
		jQuery('#g_day_price_' + icon_srl).html(day_price + 'P');
		jQuery('#g_total_icon_price_' + icon_srl).html(total_price + 'P');
	});
});
