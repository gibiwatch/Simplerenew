(function($) {
    /**
     * Custom method for assigning validation by class
     *
     * @param {Object} [rules]
     */
    $.fn.applyRules = function(rules) {
        var form = $(this[0]);
        var token = form.data('csrfToken');

        $.each(rules, function(cls, rule) {
            if (rule.remote && token.name) {
                rule.remote.data[token.name] = token.value;
            }

            form.find('.' + cls + ':not([readonly=true])')
                .each(function(idx, el) {
                    if (rule.remote) {
                        // Add option to include other fields in remote validations
                        var custom = {
                            remote: {
                                data: {}
                            }
                        };

                        $.each($(el).findPartners('data-include'), function(urlVar, el) {
                            custom.remote.data[urlVar] =
                                function() {
                                    return $(el).val();
                                };
                        });
                        $(el).rules('add', $.extend(true, {}, custom, rule));
                    } else {
                        $(el).rules('add', rule);
                    }
                });
        });

        // A special case we want to run only on user changes
        $('[data-recheck]:input').each(function(idx, element) {
            var targets = $(element).findPartners('data-recheck');
            if (targets) {
                $(element).on('blur', function(evt) {
                    $.each(targets, function(idx, target) {
                        $(target).removeData('previousValue').valid();
                    });
                });
            }
        });
    };

    /**
     * For use in forms. Looks for all submit buttons and disables/enables
     * them. If provided, will show/hide elements contained in the button
     * as requested by enable/disable classes.
     *
     * state == true  : (default) Disable buttons, show text marked for disabled state
     * state == false : Enable buttons, show text marked for enabled state
     *
     * @param {Boolean} [state]
     *
     * @returns {$.fn}
     */
    $.fn.disableSubmit = function(state) {
        var buttons  = $(this).find(':button[type=submit]'),
            enabled  = buttons.find($.Simplerenew.settings.enableText),
            disabled = buttons.find($.Simplerenew.settings.disableText);

        if ($.type(state) === 'undefined' || state) {
            buttons.prop('disabled', true).css('cursor', 'default');
            enabled.hide();
            disabled.show();

        } else {
            buttons.prop('disabled', false).css('cursor', 'pointer');
            enabled.show();
            disabled.hide();
        }

        return this;
    };

    /**
     * Convenience wrapper for .disableSubmit() with opposite logic
     *
     * state == true  : (default) Enable buttons, show text marked for enabled state
     * state == false : Disable buttons, show text marked for disabled state
     *
     * @param {Boolean} [state]
     *
     * @returns {$.fn}
     */
    $.fn.enableSubmit = function(state) {
        state = $.type(state) === 'undefined' || state;
        return $(this).disableSubmit(!state);
    };

    /**
     * For use in forms. This will give us a chance to validate no-name fields like
     * CC Number and CVV but not submit them to the server. Any form field that does
     * not have a name attribute will be given a temporary name that can be cleared
     * on submit.
     *
     * @param {Boolean} [clear]
     *
     * @returns {$.fn}
     */
    $.fn.tempNames = function(clear) {
        $(this)
            .find(':input')
            .each(function(idx, element) {
                var field = $(element);
                if (clear && field.data('clearName')) {
                    field.attr('name', null);
                } else if (!field.attr('name')) {
                    field
                        .attr('name', field.attr('id'))
                        .data('clearName', true);
                }
            });
        return this;
    };

    /**
     * Settings and default options for validator
     */
    $.extend(true, $.Simplerenew, {
        settings: {
            enableText : '.ost-text-enabled',
            disableText: '.ost-text-disabled'
        },

        gateway: {
            options   : {},
            init      : null,
            submit    : null,
            calculator: {}
        },

        validate: {
            options: {
                errorClass    : 'ost-error',
                validClass    : 'ost-valid',
                onkeyup       : false,
                ignore        : ':hidden:not(.validate)',
                rules         : {
                    password2: {
                        equalTo: '#password'
                    }
                },
                errorPlacement: function(place, element) {
                    var placeId = $(element).attr('data-error-placement');
                    if (placeId) {
                        $(placeId).append(place);
                    } else {
                        place.insertAfter(element);
                    }
                }
            }
        }
    });

    /**
     * Initialize a form for validation.
     * Passes the Joomla session token for ajax calls
     *
     * @param {String} [selector]
     * @param {Object=} [options]
     */
    $.Simplerenew.validate.init = function(selector, options) {
        var form = $(selector);

        if (form.is('form')) {
            var gateway = $.Simplerenew.gateway;

            // Store the CSRF Token, set no-name fields and setup submit buttons
            var csrfToken = form.find('span#token input:hidden');
            form
                .tempNames()
                .data('csrfToken', {
                    name : csrfToken.attr('name'),
                    value: csrfToken.val()
                })
                .enableSubmit();

            // Load custom methods
            $.each(this.methods, function(name, method) {
                $.validator.addMethod(name, method.method, method.message);
            });

            // Register and initialise extensions and custom processors
            options = $.extend(true, this.options, options);

            if (typeof gateway.init === 'function') {
                // Allow gateway to do custom form setup
                gateway.init(form);
            }
            $.Simplerenew.calculator.init(options.calculator);

            options.submitHandler = function(form) {
                // Disable submit, Clear temporary names to prevent being sent to server
                $(form)
                    .tempNames(true)
                    .disableSubmit();

                var success = true;
                if (typeof gateway.submit === 'function') {
                    // Gateway has something to do on submit
                    success = gateway.submit(form);
                }
                return success;
            };

            form.validate(options);
            form.applyRules(this.rules);

            // Back link plan selection to coupons
            $('.check_coupon[data-plan]').each(function(idx, coupon) {
                $(coupon).on('focusout', function(evt) {
                    if ($(this).val().length == 0) {
                        $('label[id^=' + this.id + ']').remove();
                    }
                });
                $($(this).attr('data-plan')).on('click', function(evt) {
                    $(coupon).valid();
                });
            });

            // Add special validation events for date dropdowns
            $('select[class*=check_date]').each(function(idx, element) {
                $(element).on('change', function(evt) {
                    $(this).valid();
                });
                $($(element).attr('data-partner')).on('change', function(evt) {
                    $(element).valid();
                });
            });
        }
    };

    /**
     * Custom methods
     */
    $.Simplerenew.validate.methods = {
        email: {
            method: function(value, element) {
                var regex = /^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)+$/;
                return this.optional(element) || regex.test(value);
            }
        },

        coupon: {
            method : function(value, element) {
                if (this.optional(element)) {
                    return 'dependency-mismatch';
                }

                var previous = this.previousValue(element),
                    plans    = [],
                    validator, data, keyValue;

                $($(element)
                    .attr('data-plan'))
                    .filter(':checked,:selected')
                    .each(function(idx, plan) {
                        plans.push($(plan).val());
                    });

                if (!this.settings.messages[element.name]) {
                    this.settings.messages[element.name] = {};
                }
                previous.originalMessage = this.settings.messages[element.name].coupon;
                this.settings.messages[element.name].coupon = previous.message;

                keyValue = value + ':' + plans.join('|');
                if (previous.old === keyValue) {
                    return previous.valid;
                }
                previous.old = keyValue;

                validator = this;
                this.startRequest(element);

                $.ajax({
                    url     : 'index.php',
                    type    : 'post',
                    mode    : 'abort',
                    port    : 'validate' + element.name,
                    dataType: 'json',
                    data    : {
                        option: 'com_simplerenew',
                        task  : 'validate.coupon',
                        format: 'json',
                        plans : plans,
                        coupon: value
                    },
                    context : validator.currentForm,
                    success : function(response) {
                        var valid = response.valid && response.valid === true,
                            errors, message, submitted;

                        validator.settings.messages[element.name].coupon = previous.originalMessage;
                        $('label[id=' + element.id + '-message]').remove();
                        if (valid) {
                            submitted = validator.formSubmitted;
                            validator.prepareElement(element);
                            validator.formSubmitted = submitted;
                            validator.successList.push(element);
                            delete validator.invalid[element.name];
                            validator.showErrors();

                            var label = $('<label>', {
                                id   : element.id + '-message',
                                for  : element.id,
                                class: validator.settings.validClass
                            }).html(response.message);
                            if (response.coupon.description) {
                                label.append($('<div>').html(response.coupon.description));
                            }
                            $(element).after(label);
                        } else {
                            errors = {};
                            message = response.error || validator.defaultMessage(element, 'coupon');
                            errors[element.name] = previous.message = $.isFunction(message) ? message(value) : message;
                            validator.invalid[element.name] = true;
                            validator.showErrors(errors);
                        }
                        previous.valid = valid;
                        validator.stopRequest(element, valid);
                    }
                });

                return 'pending';
            },
            message: 'Invalid Coupon'
        },

        ccnumber: {
            method : function(value, element) {
                return !value || $.Simplerenew.creditCard.verifyNumber(value);
            },
            message: 'Invalid card number'
        },

        cvv: {
            method : function(value, element, params) {
                var ccnumber = $(element).attr(params.partner);
                if (ccnumber) {
                    return $.Simplerenew.creditCard.verifyCVV($(ccnumber).val(), value);
                }
                return true;
            },
            message: 'Invalid CVV'
        },

        ccdate: {
            method : function(value, element, params) {
                var partner = $(element).attr(params.partner),
                    result  = false;

                if (partner) {
                    if (element.id.match(/month/i)) {
                        result = $.Simplerenew.creditCard.verifyDate(value, $(partner).val());
                    } else {
                        result = $.Simplerenew.creditCard.verifyDate($(partner).val(), value);
                    }

                    if (result) {
                        this.settings.unhighlight.call(
                            this,
                            $(partner),
                            this.settings.errorClass,
                            this.settings.validClass
                        );
                    } else {
                        this.settings.highlight.call(
                            this,
                            $(partner),
                            this.settings.errorClass,
                            this.settings.validClass
                        );
                    }
                }

                return result;
            },
            message: 'Invalid Date'
        },

        password_compare: {
            method : function(value, element, params) {
                var text = element.id.match(/(\S+?)(\d+)$/)
                if (text && text[2] == 2) {
                    var partner = $('#' + text[1]);
                    if (partner.length) {
                        return value == partner.val();
                    }
                }
                return true;
            },
            message: 'Passwords don\'t match'
        }
    };

    /**
     * Class rules to be applied via $.applyRules()
     */
    $.Simplerenew.validate.rules = {
        unique_user: {
            remote: {
                url : 'index.php',
                type: 'post',
                data: {
                    option: 'com_simplerenew',
                    format: 'json',
                    task  : 'validate.username'
                }
            }
        },

        unique_email: {
            remote: {
                url : 'index.php',
                type: 'post',
                data: {
                    option: 'com_simplerenew',
                    format: 'json',
                    task  : 'validate.email'
                }
            }
        },

        verify_password: {
            remote: {
                url : 'index.php',
                type: 'post',
                data: {
                    option: 'com_simplerenew',
                    format: 'json',
                    task  : 'validate.password'
                }
            }
        },

        password_compare: 'password_compare',

        check_coupon: 'coupon',

        check_ccnumber: 'ccnumber',

        check_cvv: {
            cvv: {
                partner: 'data-ccnumber'
            }
        },

        check_date: {
            ccdate: {
                partner: 'data-partner'
            }
        }
    };

    /**
     * Calculator for use in customizing and displaying prices
     */
    $.extend(true, $.Simplerenew, {
        validate  : {
            options: {
                calculator: {
                    output: '.simplerenew-calculator'
                }
            }
        },
        calculator: {
            settings      : {
                empty  : '.simplerenew-calculator-empty',
                display: '.simplerenew-calculator-display',
                items  : '.simplerenew-calculator-items',
                overlay: '.simplerenew-calculator-overlay'
            },
            plans         : null,
            coupon        : null,
            output        : null,
            overlay       : null,
            cover         : null,
            selectedValues: {},
            handlers      : []
        }
    });

    /**
     * Initialise the calculator. Gateway methods and custom methods can use their own
     * init code with registerHandler() (see below)
     *
     * @param {Object=} [options]
     *
     */
    $.Simplerenew.calculator.init = function(options) {
        options = $.extend(true, this.options, options);

        this.output = $(options.output);
        this.overlay = this.output.find(this.settings.overlay);

        if (this.overlay[0]) {
            this.output.css('position', 'relative');
            this.overlay.css({
                position : 'absolute',
                width    : 0,
                height   : 0,
                'z-index': 9999
            }).hide();
        }

        this.plans = $('[name^=planCodes]');
        this.coupon = $('#coupon_code');

        var calculator = this;

        // Init any handlers requesting it
        $(this.handlers).each(function(idx, handler) {
            if (typeof handler.init === 'function') {
                handler.init(calculator);
            }
        });

        // Add event handlers
        this.coupon
            .on('change sr.disable sr.enable', function(evt) {
                calculator.calculate(this.plans);
            }, this);

        this.plans
            .on('click', function(evt) {
                calculator.calculate([this]);
            });

        // Set initial states
        var checkedPlans = this.plans.filter(':checked');
        calculator.calculate(checkedPlans);
    };

    /**
     * Update all prices based on current plan and coupon selections
     *
     * @param {Array} [plans]
     */
    $.Simplerenew.calculator.calculate = function(plans) {
        var calculator  = this,
            jCalculator = $(this);

        this.addCover();
        if ($(plans).length > 0 && this.overlay) {
            this.overlay.css({
                height: this.output.height(),
                width : this.output.width()
            }).show();
        }
        $(plans).each(function(idx, plan) {
            $(calculator.handlers).each(function(idx, handler) {
                if (typeof handler.calculate === 'function') {
                    jCalculator.queue('sr', function(next) {
                        handler.calculate(calculator, plan, next);
                    });
                }
            });
        });
        jCalculator.queue('sr', function(next) {
            calculator.display.call(calculator, next);
        });
        jCalculator.dequeue('sr');
    };

    /**
     * Stores/Removes a price object for a plan on the form
     *
     * @param {Object} plan  The <input> element of the plan
     * @param {Object} price When the plan is selected, the price information supplied by the gateway
     *
     */
    $.Simplerenew.calculator.setValue = function(plan, price) {
        var planCode = $(plan).val();
        if ($(plan).prop('checked')) {
            this.selectedValues[planCode] = $.extend({
                plan          : plan,
                amount        : null,
                discount      : null,
                setup         : null,
                currencySymbol: null
            }, price);
        } else if (this.selectedValues[planCode]) {
            delete this.selectedValues[planCode];
        }
    };

    /**
     * Display the results of all calculations if an output area has been provided
     *
     * @param {Function} next The jQuery provided function to process the asynchronous queue
     */
    $.Simplerenew.calculator.display = function(next) {
        if (this.output) {
            var result = {
                prices        : [],
                empty         : $(this.output).find(this.settings.empty),
                display       : $(this.output).find(this.settings.display),
                currencySymbol: '',
                subtotal      : 0,
                discount      : 0,
                total         : 0
            };

            var priceDisplay = result.display.find(this.settings.items);
            priceDisplay.empty();

            if ($.isEmptyObject(this.selectedValues)) {
                result.empty.show();
                result.display.hide();
            } else {
                result.empty.hide();
                result.display.show();
                $.each(this.selectedValues, function(idx, price) {
                    result.currencySymbol = price.currencySymbol || result.currencySymbol;

                    result.subtotal += parseFloat(price.amount);
                    result.discount += parseFloat(price.discount);
                    result.prices.push(price);
                });
                result.total = result.subtotal - result.discount;
            }
            $(this.handlers).each(function(idx, handler) {
                if (typeof handler.display === 'function') {
                    handler.display($.Simplerenew.calculator, result);
                }
            });

            $(result.prices).each(function(idx, price) {
                priceDisplay
                    .append($('<div/>')
                        .addClass('simplerenew-calculator-plan')
                        .html($(price.plan).attr('data-description')))
                    .append($('<div/>')
                        .addClass('simplerenew-calculator-amount')
                        .html($.formatCurrency(price.amount, result.currencySymbol)));
            });
            result.display
                .find('.simplerenew-calculator-subtotal-amount')
                .html($.formatCurrency(result.subtotal, result.currencySymbol));

            result.display
                .find('.simplerenew-calculator-discount-amount')
                .html($.formatCurrency(result.discount, result.currencySymbol))
                .show();

            result.display
                .find('.simplerenew-calculator-total-amount')
                .html($.formatCurrency(result.total, result.currencySymbol));

            if (this.overlay) {
                this.overlay.hide();
            }
            if (this.cover) {
                this.cover.remove();
                this.cover = null;
            }
        }
        next();
    };

    $.Simplerenew.calculator.addCover = function() {
        var plansList = $('.ost-plans-list').css('position', 'relative');
        this.cover = $('<div/>')
            .css({
                position : 'absolute',
                width    : plansList.width(),
                height   : plansList.height(),
                'z-index': 9999
            });

        plansList.prepend(this.cover);
    };

    /**
     * Registers additional/custom calculator handlers to hook into the calculation/display process.
     * All functions are optional.
     *
     * Functions recognized:
     *
     * init(calculator)                 : After the core initialization is completed
     * calculate(calculator, plan, next): After core calculations are completed (the 'next' function MUST
     *                                    be called on completion to continue the async calculation chain!
     * display(calculator)              : After core display is completed
     *
     * @param {Object}  handler
     * @param {Boolean=} prepend Add handler to beginning of handler list
     */
    $.Simplerenew.calculator.registerHandler = function(handler, prepend) {
        if (prepend && prepend === true) {
            $.Simplerenew.calculator.handlers.unshift(handler);
        } else {
            $.Simplerenew.calculator.handlers.push(handler);
        }
    };
})(jQuery);
