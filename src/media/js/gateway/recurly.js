(function($) {
    $.Simplerenew = $.extend({}, $.Simplerenew);

    $.Simplerenew.gateway = $.extend({}, $.Simplerenew.gateway, {
        options: {
            key      : null,
            popupWarn: [
                'Your browser appears to have blocked the paypal window.',
                'Please \'allow popups\' in your browser for this site.'
            ]
        },

        /**
         * Initialize form for Recurly
         *
         * @param {jQuery} form
         */
        init: function(form) {
            if (!this.options.key) {
                alert('System error: No public key defined');
                return;
            }

            try {
                if (!recurly.configured) {
                    recurly.configure(this.options.key);
                }
            } catch (err) {
                alert('Unable to configure Recurly: ' + err.message);
                return;
            }

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

            $.Simplerenew.calculator.registerHandler(this.calculator);

            // We're taking over all form submission to minimize paypal popup problem
            $.validator.defaults.onsubmit = false;
            var buttons = form.find(':submit'),
                billing_token = $(form).find('#billing_token');

            var resetForm = function() {
                form.tempNames().enableSubmit();
            };

            // Keep track of window focus
            var windowFocus = true;
            $(window)
                .on('focus', function(event) {
                    windowFocus = true;
                })
                .on('blur', function(event) {
                    windowFocus = false;
                });

            buttons.on('click', function(event) {
                event.preventDefault();

                var validator = form.validate();
                if (!validator.form()) {
                    validator.focusInvalid();

                } else {
                    form.tempNames(true).disableSubmit();

                    billing_token.val('');

                    var method = $(form).find('input[name=payment_method]:enabled').val();

                    if (method == 'pp') {
                        // Open paypal window
                        // @TODO: Ideally use the recurly pricing object for this
                        var description, symbol,
                            total = 0,
                            items = [],
                            plans = $('[data-recurly=plan]:checked');

                        plans.each(function(idx, plan) {
                            var price = $(plan).data('price');
                            total += parseFloat(price.net);
                            symbol = price.symbol;
                            items.push($(plan).attr('data-description'));
                        });

                        description = 'Subscription to ' + items.join(', ')
                        + ' for a total of ' + symbol + total.toFixed(2);

                        // Prepare for a blocked popup
                        var popupWait = setTimeout(
                            function() {
                                if (windowFocus) {
                                    alert(
                                        form.attr('data-popup-warning')
                                        || $.Simplerenew.gateway.options.popupWarn.join('\n')
                                    );
                                    resetForm();
                                }
                            },
                            2000
                        );

                        recurly.paypal({description: description}, function(err, token) {
                            clearTimeout(popupWait);
                            if (err) {
                                if (err.code != 'paypal-canceled') {
                                    alert(err.message);
                                }
                                resetForm();

                            } else {
                                billing_token.val(token.id);
                                form.submit();
                            }
                        });

                        // One last attempt to cover other situations
                        $(window).one('focus', function(event) {
                            resetForm();
                        });

                    } else {
                        var number = form.find('[data-recurly=number]').val();

                        if (!number) {
                            // We trust there is already a cc on file
                            form.submit()
                        } else {
                            // Get billing token for credit card when entered
                            recurly.token(form[0], function(err, token) {
                                if (err) {
                                    alert(err.message);
                                    resetForm()

                                } else {
                                    billing_token.val(token.id);
                                    form.submit();
                                }
                            });
                        }
                    }
                }
            });
        },

        /**
         * The Recurly calculator handler
         */
        calculator: {
            init: function() {
                this.coupon.attr('data-recurly', 'coupon');
                this.plans.attr('data-recurly', 'plan');
            },

            calculate: function(plan, next) {
                var planCode = $(plan).val();
                var couponCode = this.coupon.is(':disabled') ? '' : this.coupon.val();
                var calculator = this;

                var pricing = recurly.Pricing();
                pricing
                    .plan(planCode)
                    .catch(function(err) {
                        alert('Simplerenew Configuration Problem: ' + err.message);
                    })
                    .done(function(price) {
                        pricing.coupon(couponCode)
                            .catch(function(err) {
                                // Next .done() gets called even on failure
                            })
                            .done(function(price) {
                                calculator.setValue(plan, {
                                    amount  : price.now.plan,
                                    discount: price.now.discount,
                                    setup   : price.now.setup_fee
                                });
                                console.log(calculator.plans);
                                next();
                            });
                    });
            }
        }
    });
})(jQuery);
