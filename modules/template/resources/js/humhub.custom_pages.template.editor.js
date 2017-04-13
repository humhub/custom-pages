humhub.module('custom_pages.template.editor', function (module, require, $) {
    var Widget = require('ui.widget').Widget;
    var object = require('util').object;
    var client = require('client');
    var modal = require('ui.modal');

    TemplateInlineEditor = function (node, options) {
        Widget.call(this, node, options);
    };

    object.inherits(TemplateInlineEditor, Widget);

    TemplateInlineEditor.prototype.init = function () {
        this.activeElements = [];
        this.initEvents();
    };

    TemplateInlineEditor.prototype.initEvents = function () {
        var that = this;
        this.$.on('mouseover', '[data-template-element], [data-template-item]', function (evt) {
            if (that.setActivateElement($(this))) {
                evt.stopPropagation();
            }
        });

        this.$.on('click', '[data-template-element], [data-template-item]', function (evt) {
            evt.preventDefault();
            evt.stopPropagation();
        });

        this.$.on('custom_pages.afterActivateContainer', function (event, item) {
            that.activeItem = item;
            that.setActivateElement(item.$);
            that.clearExcept(item);
        });

        this.$.on('custom_pages.afterDeactivateContainer', function (event, item) {
            that.activeItem = undefined;
            if (item.getParent()) {
                that.setActivateElement(item.getParent().$);
            }
        });

        // Set the currentElement when menu buttons are used.
        $(document).off('click.custom_pages').on('click.custom_pages', '.template-menu-button', function () {
            var $this = $(this);
            if ($this.data('action-target')) {
                that.currentElement = Widget.instance($this.data('action-target'));
            }
        });
    };

    TemplateInlineEditor.prototype.editElementSubmit = function (evt) {
        this._updateCkEditorInputValue();

        var that = this;
        client.submit(evt, {dataType: 'json'}).then(function (response) {
            that._destroyCkEditorInput();
            if (response.success) {
                modal.global.close();
                that.replaceElement(that.currentElement, response.output);
            } else {
                modal.global.setDialog(response.output);
            }
        }).catch(function(e) {
            module.log.error(e, true);
        });
    };

    TemplateInlineEditor.prototype.editItemSubmit = function (evt) {
        this._updateCkEditorInputValue();
        this._removeDisabledFields(evt.$form);

        var that = this;
        client.submit(evt, {dataType: 'json'}).then(function (response) {
            that._destroyCkEditorInput();
            if (response.success) {
                var $result = $(response.output);
                if ($result.is('[data-template-item]')) {
                    // called for normal edit actions
                    var itemId = $result.data('template-item');
                    that.replaceElement(that.getItemById(itemId), $result);
                } else {
                    // called for addItem actions where currentElement is the container
                    that.replaceElement(that.currentElement, $result);
                }
                modal.global.close();
            } else {
                modal.global.setDialog(response);
            }
        });
    };

    TemplateInlineEditor.prototype._removeDisabledFields = function ($form) {
        // Remove disabled items, before submit, otherwise they are submitted empty.
        var $disabled = $form.find(':disabled');
        $disabled.each(function () {
            var name = $(this).attr('name');
            $form.find('[name="' + name + '"]').remove();
        });
    };

    TemplateInlineEditor.prototype.editMultipleElementsSubmit = function (evt) {
        this._updateCkEditorInputValue();
        this._removeDisabledFields(evt.$form);

        var that = this;
        client.submit(evt, {dataType: 'json'}).then(function (response) {
            that._destroyCkEditorInput();
            if (response.success) {
                client.reload();
            } else {
                modal.global.setDialog(response);
            }
        });
    };

    TemplateInlineEditor.prototype.getItemById = function (id) {
        return this.getElement($('[data-template-item="' + id + '"]'));
    };

    TemplateInlineEditor.prototype._updateCkEditorInputValue = function () {
        $('textarea.ckeditorInput').each(function () {
            var $textarea = $(this);
            $textarea.val(CKEDITOR.instances[$textarea.attr('id')].getData());
        });
    };

    TemplateInlineEditor.prototype._destroyCkEditorInput = function () {
        for (name in CKEDITOR.instances) {
            CKEDITOR.instances[name].destroy(true);
        }
    };

    TemplateInlineEditor.prototype.replaceElement = function (element, content) {
        var $content = $(content);
        element.$.replaceWith($content);
        var newElement = this.getElement($content);

        if (this.isActiveItem(element)) {
            this.activeItem = undefined;
            this.clearExcept();
            newElement.data('isActiveItem', true);
            this.setActivateElement($content);
            newElement.startInlineEdit(true);
        }

    };

    TemplateInlineEditor.prototype.clearExcept = function (element) {
        $.each(this.activeElements, function (index, active) {
            if (!element || !active.isEqual(element)) {
                active.deactivate();
            }
        });

        this.activeEllements = [element];
    };

    TemplateInlineEditor.prototype.setActivateElement = function ($element) {
        var element = this.getElement($element);

        if (element.isActive()) {
            this.setSelection(element);
            return true;
        }

        // Only activate elements within the current activeItem if there is an activeItem
        if (this.activeItem && !this.activeItem.isParentOf(element) && (!element.itemId || !this.activeItem.$.find(element.$).length)) {
            return false;
        }

        var hasRootOwner = element.owner === "humhub\\modules\\custom_pages\\modules\\template\\models\\TemplateInstance";
        var isActiveCotnainerItemContent = this.activeItem && this.activeItem.isParentOf(element);
        var isEmptyContainer = element.$.is('.emptyContainerBlock');

        // Only activate direct root elements or elements within the current activeItem or containerItems itself.
        if (isEmptyContainer || hasRootOwner || element.isContainerItem || isActiveCotnainerItemContent) {
            this.setSelection(element);
            element.activate();
            this.activeElements.push(element);
            return true;
        }

        return false;
    };

    /**
     * Activates the given element and checks the current selection.
     * This function will deactivate all selected elements, which are not parent of the new selection element.
     * This function will furthermore not deselect an active container item.
     * 
     * @param {type} element
     * @returns {undefined}
     */
    TemplateInlineEditor.prototype.setSelection = function (element) {
        var that = this;
        var oldActiveElements = this.activeElements;
        this.activeElements = [];
        $.each(oldActiveElements, function (index, active) {
            if (active.isParentOf(element) || element.isEqual(active) || that.isActiveItem(active)) {
                that.activeElements.push(active);
            } else {
                active.deactivate();
            }
        });
    };

    TemplateInlineEditor.prototype.isActiveItem = function (element) {
        return this.activeItem && this.activeItem.isEqual(element);
    };

    TemplateInlineEditor.prototype.getElement = function ($elem) {
        return Widget.instance($elem);
    };

    TemplateSourceEditor = function (node, options) {
        Widget.call(this, node, options);
    };

    object.inherits(TemplateSourceEditor, Widget);


    module.initOnPjaxLoad = true;
    var init = function () {
        $('.editMenu, .elementMenu').remove();
        if ($('#templatePageRoot').length && require('ui.view').getState().action !== 'edit-source') {
            module.editor = Widget.instance('#templatePageRoot');
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
        $('.editMenu, .elementMenu').remove();
        $(document).off('.custom_pages');
    };

    module.export({
        init: init,
        unload: unload,
        TemplateInlineEditor: TemplateInlineEditor
    });
});