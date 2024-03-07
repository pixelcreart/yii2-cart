/**
 * Cart Class
 * 
 * This class is used to handle the cart process
 */

let $checkboxChecked = 0;
let $tableItems = $('#cart-items');

const cart = {
	name: 'Cart',

	addItem: (item) => {
        let $checkboxChecked = $('#item-list .item:checked');
		let invoice = getInvoiceData(item.data('invoice_id'));
		let serviceId = item.data('service_id');
		let invoiceDate = item.data('invoice-date');
		let itemId = `cart-item-${invoice.data.id}`;

		$checkboxChecked.data('item_id', itemId);

        if($checkboxChecked.length > 0) {
            $('#cart #cart-empty').hide();
            $('#cart #cart-items').show();
        }

        $tableItems.find('tbody').append(`
            <tr id="${itemId}" data-invoice_date="${invoiceDate}" data-service_id="${serviceId}" data-total_amount="${parseFloat(invoice.data.total_amount)}" data-overdue_fee="${parseFloat(invoice.data.overdue_fee)}">
                <td colspan="2" class="align-middle">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>${item.data('invoice-identity')}</div>
                        <div>
                            <span class="small">${$.number(parseFloat(invoice.data.total_amount)+invoice.data.overdue_fee,2)}</span>
                        </div>
                    </div>
                </td>
            </tr>
        `)

		cart.updateCart();
	},

	removeItem: (item) => {
        let $checkboxChecked = $('#item-list .item:checked').length;

        $(document).find(`#${item.data('item_id')}`).remove();

        if($checkboxChecked == 0) {
            $('#cart #cart-empty').show();
            $('#cart #cart-items').hide();
        }

		cart.updateCart();
	},

	updateCart: () => {
		let subtotalAmount = 0;
		let totalAmount = 0;
		let discountAmount = 0;
		let taxAmount = 0;
		let service_fee = {};
		let service_fee_type = {};
		let ids = [];
	
		$('#cart #cart-items tbody tr').each(function() {
			let $item = $(this);
			let itemTotalAmount = parseFloat($item.data('total_amount'));
			let itemOverdueFee = parseFloat($item.data('overdue_fee'));
			let serviceId = $item.data('service_id');
		
			service_fee_type[serviceId] = parseFloat($(`#service-${serviceId}`).data('service_fee_type'));
			service_fee[serviceId] = parseFloat($(`#service-${serviceId}`).data('service_fee_amount'));

			subtotalAmount += itemTotalAmount+itemOverdueFee;

			totalAmount = subtotalAmount - discountAmount + taxAmount;

			if(service_fee_type[serviceId]==2) {
				service_fee[serviceId] = itemTotalAmount * (service_fee[serviceId]/100);
			}

			ids.push($item.prop('id').split('-')[2]);
		})

		// sum service fee
		if(collectorServiceFee) {
			service_fee = parseFloat(collectorServiceFee);
		} else {
			service_fee = Object.values(service_fee).reduce((a,b) => a+b,0);
		}
	
		if(service_fee<=0) {
			$('#service-fee-container').hide();
		} else {
			$('#service-fee-container').show();
		}

		if(discountAmount<=0) {
			$('#discount-container').hide();
		} else {
			$('#discount-container').show();
		}

		// Displays
		$('.subtotal_value').text($.number(subtotalAmount,2));
		$('.discount_value').text($.number(discountAmount,2));
		$('.tax_value').text($.number(taxAmount,2));
		$('.service_fee').text($.number(service_fee,2));
		$('.total_value').text($.number(totalAmount+service_fee,2));

		// Update hidden inputs
		$('#service_fee').val(service_fee);
	}
}

const getInvoiceData = (invoiceId) => {
	let data = null;

	$.ajax({
		url: '/cart/invoice/'+invoiceId,
		type: 'GET',
		async: false,
		success: (response) => {
			data = response;
		}
	})

	return data;
}