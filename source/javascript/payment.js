/**
 * Payment Class
 * 
 * This class is used to handle the payment process
 * 
 * TODO: There us a bug in the selectItem method, it is not working as expected
 * 
 */

let holdItems = {};
let activeItems = {};

const payment = {
	name: 'Payment functions',

	selectItem: ($this) => {
		let itemId = $this.prop('id');
        let serviceId = $this.data('service-id');

		if($this.is(':not(:checked)')) {
			// When unchecked

			if(activeItems[serviceId].some(id => parseInt(id) < parseInt(itemId))) {
				bootbox.alert({
					message: '<div class="text-center"><i class="bi bi-exclamation-triangle-fill text-warning fs-4"></i><br>Favor pagar los recibos mas antiguos primero</div>',
					callback: () => {
						$this.closest('#item-list .list-group-item-action').toggleClass('active');
						$this.prop('checked',true).trigger('change');
					}
				});
			}

            let pos = holdItems[serviceId].indexOf(itemId);

			activeItems[serviceId].splice(pos,1);

			if(pos==-1)
				holdItems[serviceId].push(itemId);

			cart.removeItem($this);
		} else {
			// When checked	

			if(holdItems[serviceId].some(id => parseInt(id) > parseInt(itemId))) {
				bootbox.alert({
					message: '<div class="text-center"><i class="bi bi-exclamation-triangle-fill text-warning fs-4"></i><br>Favor pagar los recibos mas antiguos primero</div>',
					callback: () => {
						$this.closest('#item-list .list-group-item-action').toggleClass('active');
						$this.prop('checked',false).trigger('change');
					}
				});
			} else {
				let pos = holdItems[serviceId].indexOf(itemId);

                holdItems[serviceId].splice(pos,1);

				if(pos>=0)
					activeItems[serviceId].push(itemId);

				cart.addItem($this);
			}
		}

		console.log('holdItems:');
		console.log(JSON.stringify(holdItems));
		console.log('-----------------');
		console.log('activeItems:');
		console.log(JSON.stringify(activeItems));
		console.log('-----------------');
	},
	
	selectAll: () => {
		let $checkbox = $('#item-list .item');

		$checkbox.prop('checked',true);

		$checkbox.each(function() {
			let $this = $(this);

			activeItems[$this.prop('service-id')].push($this.prop('id'));
		})

		holdItems = [];

		payment.addToPayment($this);
	},
	
	unselectAll: () => {
		let $checkbox = $('#item-list .item');

		$checkbox.prop('checked',false);

		$checkbox.each(function() {
			let $this = $(this);

			holdItems[$this.prop('service-id')].push($this.prop('id'));
		})

		activeItems = [];

		payment.addToPayment($this);
	},

	addToPayment: (item) => {
		// cart();
	},

	toggleLast4Digits: () => {
		$('.last4digits-group').toggleClass('d-none')
	},

	checkQuotas: () => {
		$('#item-list .item').each(function() {
			let $this = $(this);
			
			if(!$this.is(':checked')) {
                if(holdItems[$this.data('service-id')]===undefined)
				    holdItems[$this.data('service-id')] = [];

                holdItems[$this.data('service-id')].push($this.prop('id'));

                if(activeItems[$this.data('service-id')]===undefined)
                    activeItems[$this.data('service-id')] = [];
            }
		})
	}
}

console.log('holdItems: ', holdItems);