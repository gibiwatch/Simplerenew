(function ($) {
    $.extend($.fn, {
        recurly: {
            calculate: function (coupon, plan) {
                var code = $(coupon).is(':disabled') ? '' : $(coupon).val();
                recurly.Pricing()
                    .plan($(plan).val())
                    .coupon(code)
                    .catch(function (err) {
                        alert(err.message);
                    })
                    .done(function (price) {
                        $(plan).data('price', {
                            net: price.now.total,
                            symbol: price.currency.symbol
                        })
                    });
            }
        }
    });

    $.Simplerenew.validate.gateway = $.extend({}, $.Simplerenew.validate.gateway, {
        options: {
            key: null
        },

        init: function (form) {
            if (!this.options.key) {
                alert('System error: No public key defined');
                return;
            }
            recurly.configure(this.options.key);

            var coupon = $('#coupon_code')
                .attr('data-recurly', 'coupon')
                .on('change sr.disable', function(evt) {
                    plans.each(function(idx, plan) {
                        $(plan).recurly.calculate(coupon, plan);
                    });
                });

            var plans = $('[name^=planCodes]')
                .attr('data-recurly', 'plan')
                .on('click', function (evt) {
                    $(this).recurly.calculate(coupon, this);
                });

            plans.filter(':checked').each(function(idx, plan) {
                $(plan).recurly.calculate(coupon, plan);
            });

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

        submit: function (form) {
            var method = $(form).find('input[name=payment_method]:enabled').val();
            var billing_token = $(form).find('#billing_token');

            billing_token.val('');
            switch (method) {
                case 'pp':
                    var description, symbol,
                        total = 0,
                        items = [],
                        plans = $('[data-recurly=plan]:checked');

                    plans.each(function (idx, plan) {
                        var price = $(plan).data('price');
                        total += parseFloat(price.net);
                        symbol = price.symbol;
                        items.push($(plan).attr('data-description'));
                    });

                    description = 'Subscription to ' + items.join(', ')
                        + ' for a total of ' + symbol + total.toFixed(2);

                    recurly.paypal({description: description}, function (err, token) {
                        if (err) {
                            if (err.code != 'paypal-canceled') {
                                alert(err.message);
                            }
                        } else {
                            billing_token.val(token.id);
                            form.submit();
                        }
                    });
                    break;

                case 'cc':
                default:
                    var number = $(form).find('#billing_cc_number').val();

                    if (number) {
                        recurly.token(form, function (err, token) {
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
