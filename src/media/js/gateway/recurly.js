(function($) {
    $.Simplerenew.validate.gateway = $.extend({}, $.Simplerenew.validate.gateway, {
        options: {
            key: null
        },

        init: function(form) {
            if (!this.options.key) {
                alert('System error: No public key defined');
                return;
            }
            recurly.configure(this.options.key);

            $(document.getElementById('billing_cc_number')).attr('data-recurly', 'number');
            $(document.getElementById('billing_cc_month')).attr('data-recurly', 'month');
            $(document.getElementById('billing_cc_year')).attr('data-recurly', 'year');
            $(document.getElementById('billing_cc_cvv')).attr('data-recurly', 'cvv');
            $(document.getElementById('billing_firstname')).attr('data-recurly', 'first_name');
            $(document.getElementById('billing_lastname')).attr('data-recurly', 'last_name');
            $(document.getElementById('billing_address1')).attr('data-recurly', 'address1');
            $(document.getElementById('billing_address2')).attr('data-recurly', 'address2');
            $(document.getElementById('billing_city')).attr('data-recurly', 'city');
            $(document.getElementById('billing_region')).attr('data-recurly', 'state');
            $(document.getElementById('billing_postal')).attr('data-recurly', 'postal_code');
            $(document.getElementById('billing_country')).attr('data-recurly', 'country');
        },

        submit: function(form) {
            var method = $(form).find('input[name=payment_method]:enabled').val();
            var billing_token = document.getElementById('billing_token');

            billing_token.value = '';
            switch (method) {
                case 'cc':
                    var number = $(form).find('#billing_cc_number').val();

                    if (number) {
                        recurly.token(form, function(err, token) {
                            if (err) {
                                alert(err.message);
                            } else {
                                billing_token.value = token.id;
                                form.submit();
                            }
                        });
                    } else {
                        billing_token.value = '';
                        form.submit();
                    }
                    break;

                case 'pp':
                    recurly.paypal({description: 'This is my test'}, function(err, token) {
                        if (err) {
                            alert(err.message);
                        } else {
                            billing_token.value = token.id;
                            form.submit();
                        }
                    });
                    break;

                default:
                    alert('unsupported payment method - ' + method);
                    break;
            }
        }
    });
})(jQuery);
