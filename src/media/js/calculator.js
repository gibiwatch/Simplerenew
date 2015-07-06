(function($) {
    $.Simplerenew = $.extend({}, $.Simplerenew);

    $.Simplerenew.calculator = {
        settings  : {
            selector: {
                calculator: '.simplerenew-calculator',
                lineitems : '.simplerenew-items'
            }
        },
        calculator: null,

        init: function(settings) {
            settings = $.extend(true, this.settings, settings);

            console.log(settings.selector.calculator);
            this.calculator = $(settings.selector.calculator);
        }
    };

    $(document).ready(function() {
        $.Simplerenew.calculator.init();
    });
})(jQuery);

