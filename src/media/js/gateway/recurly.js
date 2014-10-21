(function($) {
    $.Simplerenew.validate.gateway = $.extend({}, $.Simplerenew.validate.gateway, {
        options: {
            key: null
        },

        pricing: function() {
            alert('gotcha');
        },

        init: function(form) {
            if (!this.options.key) {
                alert('System error: No public key defined');
                return;
            }
            recurly.configure(this.options.key);


            $('[name^=planCodes]').attr('data-recurly', 'plan');
            $('#coupon_code').attr('data-recurly', 'coupon');
            $('#billing_cc_number').attr('data-recurly', 'number');
            $('#billing_cc_month').attr('data-recurly', 'month');
            $('#billing_cc_year').attr('data-recurly', 'year');
            $('#billing_cc_cvv').attr('data-recurly', 'cvv');
            $('#billing_firstname').attr('data-recurly', 'first_name');
            $('#billing_lastname').attr('data-recurly', 'last_name');
            $('#billing_address1').attr('data-recurly', 'address1');
            $('#billing_address2').attr('data-recurly', 'address2');
            $('#billing_city').attr('data-recurly', 'city');
            $('#billing_region').attr('data-recurly', 'state');
            $('#billing_postal').attr('data-recurly', 'postal_code');
            $('#billing_country').attr('data-recurly', 'country');
        },

        submit: function(form) {
            var method = $(form).find('input[name=payment_method]:enabled').val();
            var billing_token = $(form).find('#billing_token');

            billing_token.val('');
            switch (method) {
                case 'pp':
                    var pricing     = recurly.Pricing(),
                        plan        = $('[data-recurly=plan]:checked'),
                        coupon      = $('[data-recurly=coupon]:enabled'),
                        description = plan.attr('data-description') || 'Subscription';

                    pricing
                        .plan(plan.val())
                        .coupon(coupon.val())
                        .catch(function (err) {
                            alert(err.message);
                        })
                        .done(function (price) {
                            description += ' ' + price.currency.symbol + price.now.total;
                            recurly.paypal({'description': description}, function(err, token) {
                                if (err) {
                                    alert(err.message);
                                } else {
                                    billing_token.val(token.id);
                                    form.submit();
                                }
                            });
                        });
                    break;

                case 'cc':
                default:
                    var number = $(form).find('#billing_cc_number').val();

                    if (number) {
                        recurly.token(form, function(err, token) {
                            if (err) {
                                alert(err.message);
                            } else {
                                billing_token.val(token.id);
                                form.submit();
                            }
                        });
                    } else {
                        form.submit();
                    }
                    break;
            }
        }
    });
})(jQuery);
