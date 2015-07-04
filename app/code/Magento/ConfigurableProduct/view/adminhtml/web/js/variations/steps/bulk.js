/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*jshint browser:true jquery:true*/
/*global FORM_KEY*/
define([
    'uiComponent',
    'jquery',
    'ko',
    'underscore',
    'Magento_Ui/js/lib/collapsible',
    'mage/template',
    "jquery/file-uploader"
], function (Component, $, ko, _, Collapsible, mageTemplate) {
    'use strict';

    //TODO: where unique id for options
    var viewModel;
    viewModel = Component.extend({
        attributes: ko.observableArray([]),
        newProductsCount: ko.observable(),
        types: ['each', 'single', 'none'],
        sections: ko.observable({
            images: {
                label: 'images',
                type: ko.observable('none'),
                value: ko.observable({images:[], preview:null}),
                attribute: ko.observable()
            },
            pricing: {
                label: 'pricing',
                type: ko.observable('none'),
                value: ko.observable(0),
                attribute: ko.observable()
            },
            inventory: {
                label: 'inventory',
                type: ko.observable('none'),
                value: ko.observable(0),
                attribute: ko.observable()
            }
        }),
        render: function (wizard) {
            this.attributes(wizard.data.attributes());
            var count = 1;
            this.attributes.each(function (attribute) {
                count *= attribute.chosen.length;
                attribute.chosen.each(function (option) {
                    option.sections = ko.observable({images:'',pricing:'',inventory:''});
                });
            });
            _.each(this.sections(), function (section) {
                section.attribute(null);
            });
            this.newProductsCount(count);
            this.bindGalleries();
        },
        getSectionValue: function (section, options) {
            switch (this.sections()[section].type()) {
                case 'each':
                    return _.find(this.sections()[section].attribute().chosen, function (chosen) {
                        return _.find(options, function (option) {
                            return chosen.id == option.id;
                        });
                    }).sections()[section];
                case 'single':
                    return this.sections()[section].value();
                case 'none':
                    return null;
            }
        },
        getImageProperty: function (node) {
            var preview;
            var types = node.find('[data-role=gallery]').productGallery('option').types;
            var images = _.map(node.find('[data-role=image]'), function (image) {
                var imageData = $(image).data('imageData');
                imageData.galleryTypes = _.pluck(_.filter(types, function (type) {
                    return type.value == imageData.file;
                }), 'code');
                return imageData;
            });
            preview = _.find(images, function (image) {
                return _.contains(image.galleryTypes, 'image');
            });
            return images.length && preview
                ? {'images': images, preview: preview.url, file: preview.file}
                : null;
        },
        fillImagesSection: function () {
            switch (this.sections().images.type()) {
                case 'each':
                    this.sections().images.attribute().chosen.each(function (option) {
                        option.sections().images = this.getImageProperty(
                            $('[data-role=step-gallery-option-'+option.id+']')
                        );
                    }, this);
                    break;
                case 'single':
                    this.sections().images.value(this.getImageProperty($('[data-role=step-gallery-single]')));
                    break;
                default:
                    this.sections().images.value(null);
                    break;
            }
        },
        force: function (wizard) {
            this.fillImagesSection();
            this.validate();
            wizard.data.sections = this.sections;
            wizard.data.sectionHelper = this.getSectionValue.bind(this);
        },
        validate: function () {
            _.each(this.sections(), function (section) {
                switch (section.type()) {
                    case 'each':
                        if (!section.attribute()) {
                            throw new Error($.mage.__('Please, select attribute for the section ' + section.label));
                        }
                        break;
                    case 'single':
                        if (!section.value()) {
                            throw new Error($.mage.__('Please fill in the values for the section ' + section.label));
                        }
                        break;
                }
            }, this);
        },
        back: function (wizard) {
        },
        bindGalleries: function () {
            $('[data-role=bulk-step] [data-role=gallery]').each(function (index, element) {
                var gallery = $(element),
                    uploadInput = $(gallery.find('[name=image]'));

                if (!gallery.data('gallery-initialized')) {
                    gallery.mage('productGallery', {
                        template: '[data-template=gallery-content]',
                        types: {
                            "image":{
                                "code":"image",
                                "value":null,
                                "label":"Base Image",
                                "scope":"<br\/>[STORE VIEW]",
                                "name":"product[image]"
                            },
                            "small_image":{
                                "code":"small_image",
                                "value":null,
                                "label":"Small Image",
                                "scope":"<br\/>[STORE VIEW]",
                                "name":"product[small_image]"
                            },
                            "thumbnail": {
                                "code":"thumbnail",
                                "value":null,
                                "label":"Thumbnail",
                                "scope":"<br\/>[STORE VIEW]",
                                "name":"product[thumbnail]"
                            }
                        }
                    });

                    uploadInput.fileupload({
                        dataType: 'json',
                        process: [{
                            action: 'load',
                            fileTypes: /^image\/(gif|jpeg|png)$/
                        }, {
                            action: 'resize',
                            maxWidth: 1920 ,
                            maxHeight: 1200
                        }, {
                            action: 'save'
                        }],
                        formData: {form_key: FORM_KEY},
                        sequentialUploads: true,
                        acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,
                        add: function (e, data) {
                            var progressTmpl = mageTemplate('[data-template=uploader]'),
                                fileSize,
                                tmpl;

                            $.each(data.files, function (index, file) {
                                fileSize = typeof file.size == "undefined" ?
                                    $.mage.__('We could not detect a size.') :
                                    byteConvert(file.size);

                                data.fileId = Math.random().toString(33).substr(2, 18);

                                tmpl = progressTmpl({
                                    data: {
                                        name: file.name,
                                        size: fileSize,
                                        id: data.fileId
                                    }
                                });

                                $(tmpl).appendTo(gallery.find('[data-role=uploader]'));
                            });

                            $(this).fileupload('process', data).done(function () {
                                data.submit();
                            });
                        },
                        done: function (e, data) {
                            if (data.result && !data.result.error) {
                                gallery.trigger('addItem', data.result);
                            } else {
                                $('#' + data.fileId)
                                    .delay(2000)
                                    .hide('highlight');
                                alert($.mage.__('We don\'t recognize or support this file extension type.'));
                            }
                            $('#' + data.fileId).remove();
                        },
                        progress: function (e, data) {
                            var progress = parseInt(data.loaded / data.total * 100, 10);
                            var progressSelector = '#' + data.fileId + ' .progressbar-container .progressbar';
                            $(progressSelector).css('width', progress + '%');
                        },
                        fail: function (e, data) {
                            var progressSelector = '#' + data.fileId;
                            $(progressSelector).removeClass('upload-progress').addClass('upload-failure')
                                .delay(2000)
                                .hide('highlight')
                                .remove();
                        }
                    });
                    gallery.data('gallery-initialized', 1);
                }
            });
        }
    });
    return viewModel;
});
