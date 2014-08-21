(function ($) {
    $.Simplerenew = $.extend({}, $.Simplerenew, {
        validate: {
            classRules: {
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

            subscribe: function (selector) {
                var form = $(selector);

                if (form) {
                    var token = form.find('span#token input[type=hidden]');

                    var rules = {};
                    if (token) {
                        $.each(this.classRules, function (cls, rule) {
                            if (rule.remote) {
                                rule.remote.data[token.attr('name')] = token.val();
                            }

                            $('.' + cls).each(function(idx, el) {
                                rules[el.name] = rule;
                            });
                        });
                    }

                    console.log(rules);

                    form.validate({
                        debug: true,
                        errorClass: 'ost_error',
                        rules: rules
                    });
                }
            }
        }
    });
})(jQuery);
