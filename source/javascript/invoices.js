"use strict";

/**
 * @class Utilities common/javascript/utilities.js
 * @class Payment frontend/javascript/payment.js
 * @class Cart frontend/javascript/cart.js
 */

$(function () {
	utilities.location();
	payment.checkQuotas();

	let itemsSelected = [];

	if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
		$('#sidebarToggleTop').trigger('click');
	}

	$('.select-all').on('click', function () {
		payment.selectAll();
	});

	$('.unselect-all').on('click', function () {
		payment.unselectAll();
	});

	$('#item-list .list-group-item-action').on('click', function (e) {
		const $this = $(this);
		
		e.preventDefault();

		let $checkbox = $(this).find('.item');

		$checkbox.prop('checked', !$checkbox.prop('checked'));

		$this.toggleClass('active');

		$checkbox.trigger('change');
	})

	$('#item-list .item').on('change', function (e) {
		let $this = $(this);

		payment.selectItem($this);
	});

	$('button.submit-form').on('click', function (e) {
		let data = $('form#payment-form').serializeArray();
		let invoicesFound = 0;
		let $this = $(this);

		$this.prop('disabled', true);

		setInterval(function(){
			$this.prop('disabled', false);
		}, 5000);

		$.each(data, function (i, v) {
			if (v.name == 'invoices[]') {
				invoicesFound++;
			}
		});

		if (invoicesFound) {
			$('form#payment-form').trigger('submit');
			return false;
		} else {
			bootbox.alert("Debe seleccionar una factura");
			$this.prop('disabled', false);
			return false;
		}
  	});

  	$('.paymentMethodToggle').on('change', function () {
    	payment.toggleLast4Digits();
  	});

	$('#pay_btn').on('click', function(e) {
		let $this = $(this);

		$this.prop('disabled', true);

		setInterval(function(){
			$this.prop('disabled', false);
		}, 30000);

		$('#pay-form').trigger('submit');
	})

	if(itemsSelected.length > 0) {
		$.each(itemsSelected, function (i, v) {
			$(`#item-list .item[id="${v}"]`).trigger('click');
		});
	}
});

let getLocation = (position) => {
	$('#geolocation').val(position.coords.latitude+','+position.coords.longitude);
}