/**
 * Checkout Script
 * 
 * @package checkout
 * 
 */
$(function() {
    console.log('Checkout Script Loaded');

    const accordionPaymentMethods = document.getElementById('accordionPaymentMethods')
    const paymentMethodIdInput = document.getElementById('checkoutform-paymentmethodid');
    const paymentMethodTypeInput = document.getElementById('checkoutform-paymentmethodtype');

    if(!accordionPaymentMethods) {
        return;
    }

    accordionPaymentMethods.addEventListener('shown.bs.collapse', event => {
        console.log('Payment Method Shown');

        const paymentMethod = event.target;
        
        let payment_method_id = paymentMethod.getAttribute('data-payment_method_id');
        let payment_method_type = paymentMethod.getAttribute('data-payment_method_type');

        paymentMethodIdInput.value = payment_method_id;
        paymentMethodTypeInput.value = payment_method_type;
    })

    accordionPaymentMethods.addEventListener('hidden.bs.collapse', event => {
        paymentMethodIdInput.value = '';
        paymentMethodTypeInput.value = '';
    })

    $('#checkout-submit').on('click', function(e) {
        console.log('Checkout Submit Clicked');

        e.preventDefault();
        const $this = $(this);
        const $form = document.getElementById('checkout-form');

        if(paymentMethodIdInput.value === '') {
            bootbox.alert(paymentMethodAlertMessage);
            return false;
        }

        $this.prop('disabled', true);

        $form.submit();
    })
})