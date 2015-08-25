/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery',
    'fotorama/fotorama',
    'underscore',
    'matchMedia',
    'text!mage/gallery/gallery.html'
], function ($, fotorama, _, mediaCheck, template) {
    'use strict';

    return function (config, element) {

        var settings = {
            $element: $(element),
            currentConfig: config.options,
            defaultConfig: config.options,
            breakpoints: config.breakpoints,
            fotoramaApi: null,
            api: null
        };

        var triggeredBreakpoints = 0;

        /**
         * Checks if device has touch interface.
         * @return {boolean} The result of searching touch events on device.
         */
        var isTouchEnabled = (function () {
            return 'ontouchstart' in document.documentElement;
        })();

        /**
         * Initializes gallery with configuration options.
         */
        var initGallery = function () {
            var breakpoints = {};

            if (settings.breakpoints) {
                _.each(_.values(settings.breakpoints), function (breakpoint) {
                    var conditions;
                    _.each(_.pairs(breakpoint.conditions), function (pair) {
                        conditions = conditions ? conditions + ' and (' + pair[0] + ': ' + pair[1] + ')' : '(' + pair[0] + ': ' + pair[1] + ')';
                    });
                    breakpoints[conditions] = breakpoint.options;
                });
                settings.breakpoints = breakpoints;
            }

            _.extend(config, config.options);
            config.options = undefined;

            if (isTouchEnabled) {
                config.arrows = false;
            }

            config.click = false;
            config.breakpoints = null;
            settings.currentConfig = config;
            settings.defaultConfig = config;
            settings.$element.html(template);
            settings.$element = $(settings.$element.children()[0]);
            settings.$element.fotorama(config);
            settings.fotoramaApi = settings.$element.data('fotorama');
        };

        /**
         * Creates breakpoints for gallery.
         * @param {Object} breakpoints - Object with keys as media queries and values as configurations.
         */
        var setupBreakpoints = function (breakpoints) {
            if (_.isObject(breakpoints)) {
                var pairs = _.pairs(breakpoints);
                _.each(pairs, function (pair) {
                    var initialized = 0;
                    mediaCheck({
                        media: pair[0],
                        entry: function () {
                            triggeredBreakpoints ++;
                            initialized < pairs.length ? initialized ++ : initialized;
                            settings.api.updateOptions(settings.defaultConfig);
                            settings.api.updateOptions(pair[1]);
                            $.extend(true, config, pair[1]);
                            settings.$element.trigger('gallery:updated', $('.fotorama-item').data('fotorama'));
                            //_.extend(settings.currentConfig, pair[1]);
                        },
                        exit: function () {
                            triggeredBreakpoints > 0 ? triggeredBreakpoints -- : 0;
                            initialized < pairs.length ? initialized ++ : initialized;
                            if (!triggeredBreakpoints && (initialized === pairs.length)) {
                                settings.api.updateOptions(settings.defaultConfig);
                                $.extend(true, config, settings.defaultConfig);
                                settings.$element.trigger('gallery:updated', settings.fotoramaApi);
                                //_.extend(settings.currentConfig, settings.defaultConfig);
                            }
                        }
                    });
                });
            } 
        };

        /**
         * Creates gallery's API.
         */
        var initApi = function () {
            var api = {

                /**
                 * Contains fotorama's API methods.
                 */
                fotorama: settings.fotoramaApi,

                /**
                 * Displays the last image on preview.
                 */
                last: function () {
                    this.fotorama.show('>>');
                },

                /**
                 * Displays the first image on preview.
                 */
                first: function () {
                    this.fotorama.show('<<');
                },

                /**
                 * Displays previous element on preview.
                 */
                prev: function () {
                    this.fotorama.show('<');
                },

                /**
                 * Displays next element on preview.
                 */
                next: function () {
                    this.fotorama.show('>');
                },

                /**
                 * Displays image with appropriate count number on preview. 
                 * @param {Number} index - Number of image that should be displayed.
                 */
                seek: function (index) {
                    if (_.isNumber(index)) {
                        this.fotorama.show(index - 1);
                    }
                },

                /**
                 * Updates gallery with new set of options.
                 * @param {Object} config - Standart gallery configuration object.
                 */
                updateOptions: function (config) {
                    if (_.isObject(config)) {
                        if (isTouchEnabled) {
                            config.arrows = false;
                        }
                        config.click = false;
                        setupBreakpoints(config.breakpoints);
                        config.breakpoints = null;
                        //_.extend(settings.currentConfig, config);
                        this.fotorama.setOptions(config);
                    }
                },

                /**
                 * Updates gallery with specific set of items.
                 * @param {Array.<Object>} data - Set of gallery items to update.
                 */
                updateData: function (data) {
                    if (_.isArray(data)) {
                        this.fotorama.load(data);
                        //_.extend(settings.currentConfig, {data: data});
                        _.extend(settings.defaultConfig, {data: data});
                    }
                },
            }
            settings.$element.data("gallery", api);
            settings.api = settings.$element.data("gallery");
            settings.$element.trigger("gallery:loaded");
        };

        initGallery();
        initApi();
        setupBreakpoints(settings.breakpoints);
    };
});
