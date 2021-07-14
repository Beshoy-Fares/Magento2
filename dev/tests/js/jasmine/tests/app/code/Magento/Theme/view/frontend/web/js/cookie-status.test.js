/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'cookieStatus',
    'mage/calendar'
], function ($, Cookie) {
    'use strict';

    describe('Magento_Theme/js/cookie-status', function () {
        var widget,
            htmlContainer = '<div id="cookie-status" style="display: none"></div>',
            navigator;

        beforeEach(function () {
            jasmine.clock().install();
            widget = new Cookie();
            navigator = window.navigator;
            $('.modal-popup').remove();
            $('#cookie-status').remove();
            $(document.body).append(htmlContainer);
        });

        afterEach(function () {
            jasmine.clock().uninstall();
            window.navigator = navigator;
        });

        it('defines cookieStatus widget', function () {
            expect($.fn.cookieStatus).toBeDefined();
        });

        it('does not show a modal when cookies are supported', function () {
            Object.defineProperty(navigator,'cookieEnabled',{value: true, configurable: true});
            widget._init();
            jasmine.clock().tick(100);
            expect($(document.body).html()).not.toContain('<aside role="dialog" class="modal-popup');
        });

        it('shows the modal when cookies are not supported', function () {
            Object.defineProperty(navigator,'cookieEnabled',{value: false, configurable: true});
            widget._init();
            jasmine.clock().tick(100);
            expect($(document.body).html()).toContain('<aside role="dialog" class="modal-popup');
        });

    });
});
