(function ($) {
    $.fn.applyRules = function (rules) {
        var form = $(this[0]);
        var token = form.data('csrfToken');

        $.each(rules, function (cls, rule) {
            if (rule.remote && token.name) {
                rule.remote.data[token.name] = token.value;
            }

            form.find('.' + cls + ':not([readonly=true])')
                .each(function (idx, el) {
                    $(el).rules('add', rule);
                });
        });
    };

    $.Simplerenew = $.extend({}, $.Simplerenew, {
        validate: {
            options: {
                errorClass: 'ost_error',
                validClass: 'ost_valid',
                onkeyup: false
            },

            gateway: {
                init: null,
                submit: null
            },

            init: function (selector, options) {
                var form = $(selector);
                if (form) {
                    var gateway = this.gateway;

                    // Store the CSRF Token if there is one
                    var csrfToken = form.find('span#token input:hidden');
                    form.data('csrfToken', {
                        name: csrfToken.attr('name'),
                        value: csrfToken.val()
                    });

                    // Load custom methods
                    $.each(this.methods, function (name, method) {
                        $.validator.addMethod(name, method.method, method.message);
                    });

                    if (typeof gateway.init === 'function') {
                        // Allow gateway to do custom form setup
                        gateway.init(form);
                    }

                    options = $.extend(this.options, options, {
                        submitHandler: function (form) {
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
                    // @TODO: Find a better way to init coupon method
                    $('.check_coupon[data-plan]').each(function (idx, coupon) {
                        $($(this).attr('data-plan')).on('click', function(evt) {
                            $(coupon).valid();
                        });
                    });
                }
            },

            methods: {
                coupon: {
                    method: function (value, element) {
                        if (this.optional(element)) {
                            return "dependency-mismatch";
                        }

                        var previous = this.previousValue(element),
                            plan = $($(element).attr('data-plan')).filter('input:checked,input:selected'),
                            validator, data, keyValue;

                        if (!this.settings.messages[ element.name ]) {
                            this.settings.messages[ element.name ] = {};
                        }
                        previous.originalMessage = this.settings.messages[ element.name ].remote;
                        this.settings.messages[ element.name ].remote = previous.message;

                        keyValue = value + ':' + plan.val();
                        if (previous.old === keyValue) {
                            return previous.valid;
                        }
                        previous.old = keyValue;

                        validator = this;
                        this.startRequest(element);

                        $.ajax({
                            url: 'index.php',
                            type: 'post',
                            mode: 'abort',
                            port: 'validate' + element.name,
                            dataType: 'json',
                            data: {
                                option: 'com_simplerenew',
                                task: 'validate.coupon',
                                format: 'json',
                                plan: plan.val(),
                                coupon: value
                            },
                            context: validator.currentForm,
                            success: function (response) {

                                var valid = response.valid && response.valid === true,
                                    errors, message, submitted;

                                validator.settings.messages[ element.name ].coupon = previous.originalMessage;
                                if (valid) {
                                    submitted = validator.formSubmitted;
                                    validator.prepareElement(element);
                                    validator.formSubmitted = submitted;
                                    validator.successList.push(element);
                                    delete validator.invalid[ element.name ];
                                    validator.showErrors();
                                } else {
                                    errors = {};
                                    message = response.error || validator.defaultMessage(element, 'coupon');
                                    errors[ element.name ] = previous.message = $.isFunction(message) ? message(value) : message;
                                    validator.invalid[ element.name ] = true;
                                    validator.showErrors(errors);
                                }
                                previous.valid = valid;
                                validator.stopRequest(element, valid);
                            }
                        });

                        return 'pending';
                    },
                    message: 'Invalid Coupon'
                }
            },

            rules: {
                unique_user: {
                    remote: {
                        url: 'index.php',
                        type: 'post',
                        data: {
                            option: 'com_simplerenew',
                            format: 'json',
                            task: 'validate.username'
                        }
                    }
                },

                unique_email: {
                    remote: {
                        url: 'index.php',
                        type: 'post',
                        data: {
                            option: 'com_simplerenew',
                            format: 'json',
                            task: 'validate.email'
                        }
                    }
                },

                check_coupon: 'coupon'
            }
        }
    });
})(jQuery);
