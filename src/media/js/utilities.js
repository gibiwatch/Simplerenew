(function ($) {
    $.extend($.fn, {
        closePanel: function (state) {
            if (state) {
                $(this)
                    .hide()
                    .find(':input')
                    .attr('disabled', true)
                    .trigger('sr.disable');
            } else {
                $(this)
                    .show()
                    .find(':input')
                    .attr('disabled', false)
                    .trigger('sr.enable');
                $(this).find(':input:visible').first().focus();
            }
            return this;
        },
        closePanelSlide: function (state, options) {
            options = $.extend({'duration': 400}, options);
            if (state) {
                $(this)
                    .slideUp()
                    .find(':input')
                    .attr('disabled', true)
                    .trigger('sr.disable');
            } else {
                $(this)
                    .slideDown()
                    .find(':input')
                    .attr('disabled', false)
                    .trigger('sr.enable');
                $(this).find(':input:visible').first().focus();
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
     *        selector : A jQuery selector for the tab headers
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
     * @param options
     *        selector : a jQuery selector for the slider headers
     *        visible  : bool - initial visible state (default: false)
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

    /**
     * Create a clickable area for a radio button or checkbox somewhere inside it
     *
     * @param options
     *        selector : A jQuery selector for the area
     *        target   : alternative selector for the radio/checkbox(es)
     */
    $.Simplerenew.clickArea = function (options) {
        options = $.extend(this.clickArea.options, options);

        var areas = $(options.selector);
        areas.on('click', function (evt) {
            var target = $($(this).find(options.target));
            if (target.attr('type') == 'radio') {
                target.prop('checked', true);
                areas.removeClass(options.selectClass);
                $(this).addClass(options.selectClass);
            } else {
                if (target.prop('checked')) {
                    $(this).addClass(options.selectClass);
                } else {
                    $(this).removeClass(options.selectClass);
                }
            }

            // Prevent bubbling to trigger click event
            if (!$(evt.target).is('input')) {
                target.trigger('click');
            }
        });

        // Set initial state
        areas.each(function(idx, element) {
            var area = $(element);
            if (area.find(options.target).is(':checked')) {
                area.addClass(options.selectClass);
            } else {
                area.removeClass(options.selectClass);
            }
        });
    };
    $.Simplerenew.clickArea.options = {
        selector    : null,
        target      : 'input:radio,input:checkbox',
        selectClass : 'simplerenew-selected'
    };

    /**
     * use combination of input/select fields for controlling region/country values
     *
     * @param options
     */
    $.Simplerenew.region = function (options) {
        options = $.extend(this.region.options, options);

        var region = $(options.region),
            regionLists = $('select[id^=' + options.region.substr(1) + '_]'),
            country = $(options.country);

        var updateValues = function(newValue) {
            var selected = findSelected();

            newValue = newValue ? newValue : '';
            regionLists.hide().val(newValue.toUpperCase());
            region.val(newValue);

            if (selected[0]) {
                region.hide();
                selected.show();
            } else {
                region.show();
            }
        };

        var findSelected = function() {
            return $(options.region + '_' + country.val());
        };

        if (!country) {
            regionLists.hide();
            return;
        }

        region.add(regionLists).on('change', function(evt) {
            updateValues($(this).val());
        });

        country.on('change', function(evt) {
            var selected = findSelected(),
                newValue;

            if (selected[0]) {
                newValue = selected.val();
            } else {
                newValue = region.val();
            }
            updateValues(newValue);
        }).trigger('change');

    };
    $.Simplerenew.region.options = {
        region: null,
        country: null
    };

})(jQuery);

