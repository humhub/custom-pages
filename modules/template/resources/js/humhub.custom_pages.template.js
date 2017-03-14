humhub.module('custom_pages.template', function (module, require, $) {
    var object = require('util').object;
    var string = require('util').string;
    var Preview = require('file').Preview;
    
    var ImagePreview = function(node, options) {
        Preview.call(this, node, options);
    };
    
    object.inherits(ImagePreview, Preview);
    
    ImagePreview.prototype.add = function (file) {     
        if(this.source && this.source.options.uploadSingle) {
            this.$list.find('li').remove();
        }
        
        var template = this.getTemplate(file);
        var $file = $(string.template(template, file));
        this.$list.append($file);
        $file.fadeIn();
    };
    
    ImagePreview.prototype.setSource = function(source) {
        var that = this;
        this.source = source;
        this.source.on('humhub:file:uploadStart', function() {
            that.hide();
        });
    };
    
    ImagePreview.template = {
        file_image: '<li data-preview-guid="{guid}"><img src="{thumbnailUrl}" class="preview" /></li>'
    };
    
    ImagePreview.prototype.getTemplate = function (file) {
        return ImagePreview.template.file_image;
    };
    
    module.initOnPjaxLoad = true;
    var init = function () {
        if ($('#templatePageRoot').length) {
            _initEvents();
        }
    };

    var _initEvents = function () {
        // Tab logic in edit item modal
        $(document).on('keyup.custom_pages', '.template-edit-multiple-tab', function (e) {
            switch (e.which) {
                case 13:
                    e.preventDefault();
                    $(this).trigger('click');
                    break;
                case 39:
                case 40:
                    e.preventDefault();
                    if (!$(this).next('.panel-body').is(':visible')) {
                        $(this).trigger('click');
                    }
                    break;
                case 37:
                case 38:
                    e.preventDefault();
                    if ($(this).next('.panel-body').is(':visible')) {
                        $(this).trigger('click');
                    }
                    break;
            }
        }).on('click.custom_pages', '.template-edit-multiple-tab', function () {
            $(this).next('.panel-body').slideToggle('fast');
            var $switchIcon = $(this).find('.switchIcon');
            if ($switchIcon.hasClass('fa-caret-down')) {
                $switchIcon.removeClass('fa-caret-down');
                $switchIcon.addClass('fa-caret-up');
            } else {
                $switchIcon.removeClass('fa-caret-up');
                $switchIcon.addClass('fa-caret-down');
            }
        });
    };

    var unload = function () {
        $(document).off('.custom_pages');
    };

    module.export({
        init: init,
        unload: unload,
        ImagePreview: ImagePreview
    });
});