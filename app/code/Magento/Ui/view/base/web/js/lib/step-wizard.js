/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    "jquery",
    "underscore",
    "jquery/ui"
], function ($, _) {
    "use strict";

    var getWizardBySteps = function (steps, element) {
        var deferred = new $.Deferred();
        require(steps, function () {
            deferred.resolve(new Wizard(arguments, element));
        });
        return deferred.promise();
    };

    var Wizard = function(steps, element) {
        this.steps = _.map(steps, function (step) {
            return _.isFunction(step) ? step.prototype : step;
        });
        this.index = 0;
        this.step = this.steps[this.index];
        this.element = element;
        this.data = {};
        this.tab = {};
        this.move = function(newIndex, tab) {
            this.tab = tab;
            if (newIndex > this.index) {
                this.next(newIndex);
            } else if (newIndex < this.index) {
                this.prev(newIndex);
            }
            this.render();
        };
        this.next = function() {
            this.data = this.step.force(this);
            this.step = this.steps[++this.index];
        };
        this.prev = function(newIndex) {
            this.step.back(this);
            this.index = newIndex;
            this.step = this.steps[this.index];
        };
        this.render = function() {
            this.step.render(this);
        };
    };

    $.widget('mage.step-wizard', $.ui.tabs, {
        wizard: {},
        options: {
            collapsible: false,
            disabled: [],
            event: "click",
            buttonNextElement: '[data-role="step-wizard-next"]',
            buttonPrevElement: '[data-role="step-wizard-prev"]',
            buttonFinalElement: '[data-role="step-wizard-final"]'
        },
        _create: function() {
            this._control();
            this.wizard = getWizardBySteps(this.options.steps, this.element);
            this._super();
        },
        _control: function() {
            var self = this;
            this.prev = this.element.find(this.options.buttonPrevElement);
            this.next = this.element.find(this.options.buttonNextElement);
            this.final = this.element.find(this.options.buttonFinalElement);

            this.next.on('click.' + this.eventNamespace, function(event){
                self._activate(self.options.active + 1);
            });
            this.prev.on('click.' + this.eventNamespace, function(event){
                self._activate(self.options.active - 1);
            });
            this.final.hide();
        },
        load: function(index, event) {
            this._disabledTabs(index);
            this._super(index, event);
            this._handlerStep(index);
            this._actionControl(index);
        },
        _handlerStep: function (index) {
            var tab = this.panels.eq(index);
            this.wizard.done(function (wizard) {
                wizard.move(index, tab);
            });
        },
        _way: function(index) {
            return this.options.selected > index ? 'back' : 'force';
        },
        _actionControl: function (index) {
            var self = this;
            if (index < 1) {
                this.prev.find('button').addClass("disabled");
            }
            if (index === 1 && this._way(index) === 'force') {
                this.prev.find('button').removeClass("disabled");
            }
            if (index > this.tabs.length - 2) {
                this.next.hide();
                this.final.show();
            }
            if (this._way(index) === 'back') {
                this.final.hide();
                this.next.show();
            }
        },
        _disabledTabs: function(index) {
            this._setupDisabled(_.range(index + 2, this.tabs.length));
        }
    });

    $(document).ready(function () {
       var dialog = $('[data-role="step-wizard-dialog"]').dialog({
            title: $.mage.__('Create Product Configurations'),
            autoOpen: false,
            minWidth: 980,
            modal: true,
            resizable: false,
            draggable: false,
            position: {
                my: 'left top',
                at: 'center top',
                of: 'body'
            },
            open: function () {
                $(this).closest('.ui-dialog').addClass('ui-dialog-active');

                var topMargin = $(this).closest('.ui-dialog').children('.ui-dialog-titlebar').outerHeight() + 135;
                $(this).closest('.ui-dialog').css('margin-top', topMargin);

                $(this).addClass('admin__scope-old'); // ToDo UI: remove with old styles removal
            },
            close: function () {
                $(this).closest('.ui-dialog').removeClass('ui-dialog-active');
            }
        });
        $('[data-action="open-steps-wizard"]').on('click', function () {
            dialog.dialog('open');
            dialog.removeClass('hidden');
        });
        $('[data-action="close-steps-wizard"]').on('click', function () {
            dialog.dialog('close');
        });
    });

    return $.mage["step-wizard"];
});
