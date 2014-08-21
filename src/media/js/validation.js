(function ($) {
    $.Simplerenew = $.extend({}, $.Simplerenew, {
        validate: {
            options: {
                errorClass: 'ost_error'
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

                    options = $.extend(this.options, {rules: rules}, options);
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
