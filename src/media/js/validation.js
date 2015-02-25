(function($) {
    /**
     * Custom method for assigning validation by class
     *
     * @param rules
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
                        $(el).tempName().rules('add', $.extend(true, {}, custom, rule));
                    } else {
                        $(el).tempName().rules('add', rule);
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

    $.fn.disableSubmit = function(state) {
        var buttons = $(this).find(':button[type=submit]'),
            enabled = buttons.find('.ost-text-enabled'),
            disabled = buttons.find('.ost-text-disabled');

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

    $.fn.enableSubmit = function(state) {
        state = $.type(state) === 'undefined' || state;
        return $(this).disableSubmit(!state);
    };

    /**
     * This will give us a chance to validate no-name fields like
     * CC Number and CVV
     *
     * @param {bool} [clear]
     *
     * @returns {$.fn}
     */
    $.fn.tempName = function(clear) {
        var self = $(this);
        if (self.is('input')) {
            if (clear && self.data('clearName')) {
                self.attr('name', null);
            } else if (!self.attr('name')) {
                self
                    .attr('name', self.attr('id'))
                    .data('clearName', true);
            }
        }
        return this;
    };

    $.Simplerenew = $.extend({}, $.Simplerenew, {
        validate: {
            options: {
                errorClass    : 'ost_error',
                validClass    : 'ost_valid',
                onkeyup       : false,
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
            },

            gateway: {
                init  : null,
                submit: null
            },

            /**
             * Initialize a form for validation.
             * Passes the Joomla session token for ajax calls
             *
             * @param selector
             * @param options
             */
            init: function(selector, options) {
                var form = $(selector);
                if (form) {
                    var gateway = this.gateway;

                    // Store the CSRF Token and setup submit buttons
                    var csrfToken = form.find('span#token input:hidden');
                    form
                        .data('csrfToken', {
                            name : csrfToken.attr('name'),
                            value: csrfToken.val()
                        })
                        .enableSubmit();

                    // Load custom methods
                    $.each(this.methods, function(name, method) {
                        $.validator.addMethod(name, method.method, method.message);
                    });

                    if (typeof gateway.init === 'function') {
                        // Allow gateway to do custom form setup
                        gateway.init(form);
                    }

                    options = $.extend(this.options, options, {
                        submitHandler: function(form) {

                            // Disable submit, Clear temporary names to prevent being sent to server
                            $(form)
                                .disableSubmit()
                                .find(':input').each(function(idx, element) {
                                    $(element).tempName(true);
                                });

                            if (typeof gateway.submit === 'function') {
                                // Gateway is handling form submit
                                gateway.submit(form);
                            } else {
                                form.submit();
                            }
                        }
                    });

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
            },

            /**
             * Custom methods
             */
            methods: {
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
                            plans = [],
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
                        previous.originalMessage = this.settings.messages[element.name].remote;
                        this.settings.messages[element.name].remote = previous.message;

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
                            result = false;

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
            },

            /**
             * Class rules to be applied via $.applyRules()
             */
            rules: {
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
            }
        }
    });
})(jQuery);
