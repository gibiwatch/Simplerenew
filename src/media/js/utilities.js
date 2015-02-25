(function($) {
    $.extend($.fn, {
        closePanel     : function(state, options) {
            options = $.extend({
                focus: true
            }, options);

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

                if (options.focus) {
                    $(this).find(':input:visible').first().focus();
                }
            }
            return this;
        },
        closePanelSlide: function(state, options) {
            options = $.extend({
                duration: 400,
                focus   : true
            }, options);

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

                if (options.focus) {
                    $(this).find(':input:visible').first().focus();
                }
            }
            return this;
        },

        /**
         * Return an array of partner elements as requested from
         * a space delimited string of element ids. If an id string
         * does not begin with '#', an attempt will be made to find
         * a partner id with the same prefix. If the element name and
         * id follow the same pattern:
         *
         * member[1][username] == member_1_username
         *
         * member_1_ will be recognized as the prefix and a request for
         * 'email' will attempt to find #member_1_email.
         *
         * 'username' and 'email' will be considered the basenames and
         * in this case the current element will be returned as a partner
         * to itself
         *
         *
         * @param {string} list
         * @param {bool}   [useAttribute]
         *
         * @returns {Object}
         */
        findPartners: function(list, useAttribute) {
            var name = $(this).attr('name'),
                id = $(this).attr('id'),
                prefix = '#',
                partners = {};

            if (arguments.length == 1 || useAttribute) {
                list = $(this).attr(list);
            }

            if (list) {
                if (name.indexOf('[') > -1) {
                    var parts = name.replace(/]/g, '').split(/\[/);
                    if (parts.join('_') == id) {
                        name = parts.pop();
                        prefix = '#' + parts.join('_') + '_';
                    }
                }

                var partner;
                $.each(list.split(' '), function(idx, id) {
                    if (id.indexOf('#') < 0) {
                        partner = $(prefix + id);
                    } else {
                        partner = $(id);
                        id = id.substr(1);
                    }
                    if (partner) {
                        partners[id] = partner;
                    }
                });
            }
            return partners;
        }
    });

    $.Simplerenew = $.extend({}, $.Simplerenew);

    /**
     * Traverse a plain object using dot-notation key syntax
     * Default source is $.Simplerenew
     *
     * @param keys
     * @param source
     *
     * @returns {*}
     */
    $.Simplerenew.find = function(keys, source) {
        source = source || this;

        var item = keys.split('.');
        if (item.length > 1) {
            var key = item.shift();
            var value = item.join('.');
            if (source[key]) {
                return this.find(value, source[key]);
            }

        } else if (source[item]) {
            return source[item];
        }

        return null;
    };

    /**
     * Turn any element into an ajax submitter.
     * Element must contain a data-task attribute
     * that references a defined object in $.Simplerenew
     * containing all jQuery.ajax() options needed to complete
     * the request.
     *
     * Expects dot-notation [see this.find()]
     *
     * @param options
     */
    $.Simplerenew.ajax = function(options) {
        options = $.extend(this.ajax.options, options);

        $(options.selector).on('click', function(evt) {
            evt.preventDefault();
            var keys = $(this).attr('data-task');
            if (keys) {
                var options = $.Simplerenew.find(keys);
                if (options) {
                    $.ajax($.extend(options, {context: this}));
                }
            }
        });
    };
    $.Simplerenew.ajax.options = {
        selector: null
    };

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
    $.Simplerenew.tabs = function(options) {
        options = $.extend(this.tabs.options, options);

        var headers = $(options.selector);
        headers
            .css('cursor', 'pointer')
            .each(function(idx, active) {
                $(this)
                    .data('contentPanel', $($(this).attr('data-content')))
                    .on('click', function(evt, options) {
                        headers.each(function(idx) {
                            $(this)
                                .toggleClass('tab-enabled', active === this)
                                .data('contentPanel').closePanel(active !== this, options)
                        });
                    });
            });

        // Set active panel
        if (!options.active) {
            options.active = '#' + $(headers[0]).attr('id');
        }
        $(headers.filter(options.active)).trigger('click', {focus: false});
    };
    $.Simplerenew.tabs.options = {
        selector: null,
        active  : null
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
    $.Simplerenew.sliders = function(options) {
        options = $.extend(this.sliders.options, options);

        $(options.selector).each(function() {
            $(this)
                .css('cursor', 'pointer')
                .data('contentPanel', $($(this).attr('data-content')))
                .on('click', function(evt, options) {
                    var contentPanel = $(this).data('contentPanel');
                    contentPanel.closePanelSlide(contentPanel.is(':visible'), options);
                })
                .data('contentPanel').closePanel(!options.visible, {focus: false});
        });
    };
    $.Simplerenew.sliders.options = {
        selector: null,
        visible : false
    };

    /**
     * Create a clickable area for a radio button or checkbox somewhere inside it
     *
     * @param options
     *        selector : A jQuery selector for the area
     *        target   : alternative selector for the radio/checkbox(es)
     */
    $.Simplerenew.clickArea = function(options) {
        options = $.extend(this.clickArea.options, options);

        var areas = $(options.selector);
        areas.on('click', function(evt) {
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
        selector   : null,
        target     : 'input:radio,input:checkbox',
        selectClass: 'simplerenew-selected'
    };

    /**
     * use combination of input/select fields for controlling region/country values
     *
     * @param options
     */
    $.Simplerenew.region = function(options) {
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
        region : null,
        country: null
    };

})(jQuery);

