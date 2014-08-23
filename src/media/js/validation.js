(function ($) {
    $.fn.serializeObject = function() {
        var result = {};
        $(this.serializeArray()).each(function(idx, obj) {
            if (obj.name) {
                result[obj.name] = obj.value;
            }
        });
        return result;
    };

    $.fn.ajaxSubmit = function(data) {
        data = $.extend(this.serializeObject(), data);
        $.ajax({
            url: 'index.php',
            type: 'post',
            async: false,
            data: data,
            error: function(request, status, error) {
                alert(request.status + ': ' + error);
            },
            success: function(data, status, request) {
                console.log(data, 'done');
            }
        });
        console.log('next step?');
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

            subscribe: function (selector, options) {
                var form = $(selector);

                if (form) {
                    var rules = applyRules(
                        form,
                        this.rules,
                        form.find('span#token input[type=hidden]')
                    );

                    options = $.extend(
                        this.options,
                        {
                            rules: rules,
                            submitHandler: function(form) {
                                var method = $(form).find('input[name=payment_method]:enabled');

                                if (method.val() != 'cc') {
                                    // non Credit Card billing
                                    form.submit();
                                } else {
                                    // Credit Card billing requires special handling
                                    $(form).ajaxSubmit({
                                        task: 'subscribe.account',
                                        format: 'json'
                                    });
                                }
                            }
                        },
                        options
                    );
                    form.validate(options);
                }
            }
        }
    });

    var applyRules =  function (form, methods, token) {
        var rules = {};

        $.each(methods, function (cls, rule) {
            if (rule.remote && token) {
                rule.remote.data[token.attr('name')] = token.val();
            }

            $('.' + cls).each(function (idx, el) {
                rules[el.name] = rule;
            });
        });

        return rules;
    };

})(jQuery);
