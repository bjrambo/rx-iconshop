jQuery(function($) {
	$('.selected_days').click(function() {
		var $opt = $(this).children('input');
		var icon_srl = $opt.attr('data-icon-srl');
		var day_price = $opt.attr('data-day-price');

		$.exec_json('iconshop.getPricePoint', {'icon_srl': icon_srl, 'day_price': day_price}, function(ret_obj) {
			jQuery('#icon_price_' + icon_srl).html(ret_obj.icon_price + 'P');
			jQuery('#day_price_' + icon_srl).html(day_price + 'P');
			jQuery('#total_icon_price_' + icon_srl).html(ret_obj.total_point_price + 'P');
		});
	});

	$('.g_selected_days').click(function () {
		var $opt = $(this).children('input');
		var icon_srl = $opt.attr('data-icon-srl');
		var day_price = $opt.attr('data-day-price');
		$.exec_json('iconshop.getPricePoint', {'icon_srl': icon_srl, 'day_price': day_price}, function (ret_obj) {
			jQuery('#g_icon_price_' + icon_srl).html(ret_obj.icon_price + 'P');
			jQuery('#g_day_price_' + icon_srl).html(day_price + 'P');
			jQuery('#g_total_icon_price_' + icon_srl).html(ret_obj.total_point_price + 'P');
		});
	});
});
var st = $(":input:radio[name=search_type]:checked").val();
