(function ($) {
    $.fn.applyRules = function (methods, token) {
        var form = $(this[0]);

        $.each(methods, function (cls, rule) {
            if (rule.remote && token) {
                rule.remote.data[token.attr('name')] = token.val();
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
                validClass: 'ost_valid'
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
                }
            },

            gateway: {
                init: null,
                submit: null
            },

            init: function (selector, options) {
                var form = $(selector);
                if (form) {
                    var gateway = this.gateway;

                    if (typeof gateway.init === 'function') {
                        gateway.init(form);
                    }

                    options = $.extend(this.options, options, {
                        submitHandler: function(form) {
                            if (typeof gateway.submit === 'function') {
                                gateway.submit(form);
                            } else {
                                form.submit();
                            }
                        }
                    });

                    form.validate(options);
                    form.applyRules(
                        this.rules,
                        form.find('span#token input[type=hidden]')
                    );
                }
            }
        }
    });
})(jQuery);
