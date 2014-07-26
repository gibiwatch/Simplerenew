(function ($) {
    if (typeof($.Simplerenew) == 'undefined') {
        $.Simplerenew = {};
    }

    $.fn.closePanel = function (state) {
        if (state) {
            $(this).hide().find(':input').attr('disabled', true);
        } else {
            $(this).show().find(':input').attr('disabled', false);
        }
        return this;
    };

    $.fn.closePanelSlide = function(state, options) {
        options = $.extend({'duration': 400}, options);
        if (state) {
            $(this).slideUp().find(':input').attr('disabled', true);
        } else {
            $(this).slideDown().find(':input').attr('disabled', false);
        }
        return this;
    };

    /**
     * Simple tabs. Define tab headings with any selector
     * and include the attribute data-content with a selector
     * for the content area it controls. All tabs selected by
     * the passed selector will hide all content panels
     * except the one(s) controlled by the active tab.
     *
     * @param selector
     */
    $.Simplerenew.tabs = function (selector) {
        var headers = $(selector);
        headers
            .css('cursor', 'pointer')
            .each(function (idx, active) {
                $(this)
                    .prop('contentPanel', $($(this).attr('data-content')))
                    .on('click', function (evt) {
                        headers.each(function (idx) {
                            $(this)
                                .toggleClass('tab-enabled', active === this)
                                .prop('contentPanel').closePanel(active !== this)
                        });
                    });
            });

        // Start with first panel active
        $(headers[0]).trigger('click');
    };

    /**
     * Independent sliding panels. Use any selector
     * to select one or more slide controls. Use the
     * data-content attribute to select the content
     * panels to slide Up/Down on clicking the control.
     *
     * @param selector
     * @param options
     *        visible : bool - initial visible state (default: false)
     */
    $.Simplerenew.sliders = function (selector, options) {
        options = $.extend({'visible': false}, options);

        $(selector).each(function() {
            $(this)
                .css('cursor', 'pointer')
                .prop('contentPanel',$($(this).attr('data-content')))
                .on('click', function (evt) {
                    var contentPanel = $(this).prop('contentPanel');
                    contentPanel.closePanelSlide(contentPanel.is(':visible'));
                })
                .prop('contentPanel').closePanel(!options.visible);
        });
    }
})(jQuery);

