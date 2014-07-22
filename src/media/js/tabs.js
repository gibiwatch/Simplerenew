/**
 * Simple tabs. Structure:
 *
 * <div class="payment-tabs">
 *     <div data-content="selector"></div>
 *     <div data-content="selector"></div>
 * </div>
 *
 */
(function ($) {
    if (typeof($.Simplerenew) == 'undefined') {
        $.Simplerenew = {};
    }

    $.Simplerenew.tabs = function (selector) {
        var headers = $(selector);
        headers.each(function (idx, active) {
            $(this).prop('contentPanel', $($(this).attr('data-content')));

            $(this)
                .on('click', function (evt) {
                    headers.each(function (idx) {
                        var contentPanel = $(this).prop('contentPanel');
                        if (active === this && contentPanel) {
                            $(this).toggleClass('tab-enabled', true);
                            contentPanel.show().find(':input').attr('disabled', false);
                        } else {
                            $(this).toggleClass('tab-enabled', false)
                            contentPanel.hide().find(':input').attr('disabled', true);
                        }
                    });
                });
        });

        $(headers[0]).trigger('click');
    };
})(jQuery);

