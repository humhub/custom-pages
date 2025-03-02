humhub.module('custom_pages.template.editor', function (module, require, $) {
    var Widget = require('ui.widget').Widget;
    var object = require('util').object;
    var client = require('client');
    var modal = require('ui.modal');
    var additions = require('ui.additions');

    TemplateInlineEditor = function (node, options) {
        Widget.call(this, node, options);
        additions.observe(this.$);
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

        // Set the currentElement when menu buttons are used.
        $(document).off('click.custom_pages').on('click.custom_pages', '.template-menu-button', function () {
            var $this = $(this);
            if ($this.data('action-target')) {
                that.currentElement = Widget.instance($this.data('action-target'));
            }
        });
    };

    TemplateInlineEditor.prototype.editElementSubmit = function (evt) {
        this._updateInputValue();

        var that = this;
        client.submit(evt, {dataType: 'json'}).then(function (response) {
            that._destroyInput();
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
        this._updateInputValue();
        this._removeDisabledFields(evt.$form);

        var that = this;
        client.submit(evt, {dataType: 'json'}).then(function (response) {
            that._destroyInput();
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
                additions.applyTo(that.$);
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
        this._updateInputValue();
        this._removeDisabledFields(evt.$form);

        var that = this;
        client.submit(evt, {dataType: 'json'}).then(function (response) {
            that._destroyInput();
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

    TemplateInlineEditor.prototype._updateInputValue = function () {
        if (typeof tinyMCE === 'object' && typeof tinyMCE.triggerSave === 'function') {
            tinyMCE.triggerSave();
        }
    };

    TemplateInlineEditor.prototype._destroyInput = function () {
        if (typeof tinyMCE === 'object' && typeof tinyMCE.remove === 'function') {
            tinyMCE.remove();
        }
    };

    TemplateInlineEditor.prototype.replaceElement = function (element, content) {
        element ? element.$.replaceWith($(content)) : location.reload();
    };

    TemplateInlineEditor.prototype.setActivateElement = function ($element) {
        var element = this.getElement($element);
        this.setSelection(element);

        if (element.isActive()) {
            return true;
        }

        element.activate();
        this.activeElements.push(element);

        var parent = element.getParent();
        while (parent)
        {
            if (!parent.isActive()) {
                parent.activate();
                this.activeElements.push(parent);
            }
            parent = parent.getParent();
        }

        return true;
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
            if (active.isParentOf(element) || element.isEqual(active)) {
                that.activeElements.push(active);
            } else {
                active.deactivate();
            }
        });
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
