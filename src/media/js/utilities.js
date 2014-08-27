(function ($) {
    $.extend($.fn, {
        closePanel: function (state) {
            if (state) {
                $(this).hide().find(':input').attr('disabled', true);
            } else {
                $(this).show().find(':input').attr('disabled', false);
            }
            return this;
        },
        closePanelSlide: function (state, options) {
            options = $.extend({'duration': 400}, options);
            if (state) {
                $(this).slideUp().find(':input').attr('disabled', true);
            } else {
                $(this).slideDown().find(':input').attr('disabled', false);
            }
            return this;
        }
    });

    $.Simplerenew = $.extend({}, $.Simplerenew);

    /**
     * Simple tabs. Define tab headings with any selector
     * and include the attribute data-content with a selector
     * for the content area it controls. All tabs selected by
     * the passed selector will hide all content panels
     * except the one(s) controlled by the active tab.
     *
     * @param options
     */
    $.Simplerenew.tabs = function (options) {
        options = $.extend(this.tabs.options, options);

        var headers = $(options.selector);
        headers
            .css('cursor', 'pointer')
            .each(function (idx, active) {
                $(this)
                    .data('contentPanel', $($(this).attr('data-content')))
                    .on('click', function (evt) {
                        headers.each(function (idx) {
                            $(this)
                                .toggleClass('tab-enabled', active === this)
                                .data('contentPanel').closePanel(active !== this)
                        });
                    });
            });

        // Start with first panel active
        $(headers[options.active]).trigger('click');
    };
    $.Simplerenew.tabs.options = {
        selector : null,
        active   : 0
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
    $.Simplerenew.sliders = function (options) {
        options = $.extend(this.sliders.options, options);

        $(options.selector).each(function () {
            $(this)
                .css('cursor', 'pointer')
                .data('contentPanel', $($(this).attr('data-content')))
                .on('click', function (evt) {
                    var contentPanel = $(this).data('contentPanel');
                    contentPanel.closePanelSlide(contentPanel.is(':visible'));
                })
                .data('contentPanel').closePanel(!options.visible);
        });
    };
    $.Simplerenew.sliders.options = {
        selector : null,
        visible  : false
    };
})(jQuery);

