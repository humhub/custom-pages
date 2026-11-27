humhub.module('custom_pages.template', function (module, require, $) {
    var object = require('util').object;
    var Widget = require('ui.widget').Widget;
    var client = require('client');

    /**
     * The image of an image element. `ImageElement` renders the container with the current file as the init data (as
     * the upload response has it, `FileHelper::getFileInfos()`). Not a `file.Preview`, which lists files as file rows:
     * the upload stays one request and adds the uploaded image here.
     */
    var ImagePreview = function (node, options) {
        Widget.call(this, node, options);
    };

    object.inherits(ImagePreview, Widget);

    ImagePreview.prototype.init = function (files) {
        var that = this;
        this.$list = $('<ul class="files"></ul>').appendTo(this.$);
        (Array.isArray(files) ? files : []).forEach(function (file) {
            that.add(file);
        });

        if (this.hasFiles()) {
            this.$.show();
        } else {
            this.$.hide();
        }
    };

    // A single image: a new one replaces the one before
    ImagePreview.prototype.add = function (file) {
        this.$list.html(
            $('<li class="file-preview-item"></li>')
                .attr('data-preview-guid', file.guid)
                .append($('<img class="preview">').attr({src: file.thumbnailUrl, alt: file.name}))
        );
    };

    ImagePreview.prototype.getFileCount = function () {
        return this.$list.find('.file-preview-item').length;
    };

    ImagePreview.prototype.hasFiles = function () {
        return this.getFileCount() > 0;
    };

    ImagePreview.prototype.reset = function () {
        this.$list.empty();
        this.$.hide();
    };

    ImagePreview.prototype.setSource = function (source) {
        var that = this;
        this.source = source;
        this.source.on('humhub:file:uploadStart', function () {
            that.hide();
        });
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
                    if (!$(this).next('.card-body').is(':visible')) {
                        $(this).trigger('click');
                    }
                    break;
                case 37:
                case 38:
                    e.preventDefault();
                    if ($(this).next('.card-body').is(':visible')) {
                        $(this).trigger('click');
                    }
                    break;
            }
        }).on('click.custom_pages', '.template-edit-multiple-tab', function () {
            $(this).next('.card-body').slideToggle('fast');
            var $switchIcon = $(this).find('.switchIcon');
            if ($switchIcon.hasClass('ti-caret-down-filled')) {
                $switchIcon.removeClass('ti-caret-down-filled');
                $switchIcon.addClass('ti-caret-up-filled');
            } else {
                $switchIcon.removeClass('ti-caret-up-filled');
                $switchIcon.addClass('ti-caret-down-filled');
            }
        });
    };

    var unload = function () {
        $(document).off('.custom_pages');
    };

    const deleteElementContent = function (evt) {
        const btn = evt.$trigger;
        const data = {
            elementContentId: btn.data('element-content-id'),
        }

        client.post(evt, {data}).then(function (response) {
            if (response.success) {
                const preview = $('#' + btn.data('preview-id'));
                if (preview.length) {
                    // The image element's own preview or the file rows of `file.Preview`
                    preview.fadeOut(400, function () {
                        Widget.instance(preview).reset();
                    });
                }
                btn.remove();
            }
        }).catch(function (e) {
            module.log.error(e, true);
        });
    }

    module.export({
        init,
        unload,
        ImagePreview,
        deleteElementContent
    });
});
