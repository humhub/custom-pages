humhub.module('custom_pages.template.editor', function (module, require, $) {
    const Widget = require('ui.widget').Widget;
    const object = require('util').object;
    const client = require('client');
    const modal = require('ui.modal');
    const additions = require('ui.additions');

    TemplateInlineEditor = function (node, options) {
        Widget.call(this, node, options);
        additions.observe(this.$);
    };

    object.inherits(TemplateInlineEditor, Widget);

    TemplateInlineEditor.prototype.init = function () {
        this.initHighlight();
    };

    TemplateInlineEditor.prototype.initHighlight = function () {
        $(document).on('mouseenter', '[data-editor-container-id]', function () {
            if ($(this).hasClass('cp-editor-container-hover')) {
                return;
            }
            $(this).addClass('cp-editor-container-hover');

            const containerId = $(this).data('editor-container-id');
            const actions = $('[data-actions-container-id=' + containerId+ ']');
            if (actions.length) {
                actions.show();
                return;
            }

            const addButton = $('.cp-structure [data-container-id=' + containerId + '] > div.cp-structure-container > [data-action-click="addContainerItem"]');
            if (addButton.length) {
                const pos = this.getBoundingClientRect();
                const actions = $('<div>').attr('data-actions-container-id', containerId);
                $('body').append(actions.append(addButton.clone()
                    .removeAttr('data-action-click')
                    .on('click', () => addButton.click())));
                actions.css({
                    top: pos.top - actions.outerHeight(),
                    left: pos.left + pos.width - actions.outerWidth(),
                });
                $(this).find('[data-editor-container-item-id]').each(function () {
                    const itemActions = $('[data-actions-container-item-id=' + $(this).data('editor-container-item-id')+ ']');
                    alignActions(itemActions, actions);
                });
            }
        }).on('mouseleave', '[data-editor-container-id], [data-actions-container-id]', function (e) {
            const containerId = $(this).data('editor-container-id') ?? $(this).data('actions-container-id');
            if (isOutside(e, ['[data-editor-container-id="' + containerId+ '"]', '[data-actions-container-id="' + containerId+ '"]'])) {
                $('[data-editor-container-id=' + containerId+ ']').removeClass('cp-editor-container-hover');
                $('[data-actions-container-id=' + containerId+ ']').hide();
            }
        }).on('mouseenter', '[data-editor-container-item-id]', function () {
            if ($(this).hasClass('cp-editor-container-hover')) {
                return;
            }
            $(this).addClass('cp-editor-container-hover');

            const containerItemId = $(this).data('editor-container-item-id');
            const containerItem = $('.cp-structure [data-container-item-id=' + containerItemId + '] > li > .cp-structure-row');
            containerItem.addClass('cp-structure-active');

            $('[data-actions-container-item-id]').hide();
            const actions = $('[data-actions-container-item-id=' + containerItemId + ']');
            if (actions.length) {
                actions.show();
                return;
            }

            const editButton = containerItem.find('[data-action-click="editElements"] > .fa');
            if (editButton.length) {
                const pos = this.getBoundingClientRect();
                const actions = $('<div>').attr('data-actions-container-item-id', containerItemId);
                $('body').append(actions.append(editButton.clone()
                    .removeAttr('data-action-click')
                    .on('click', () => editButton.parent().click())));
                actions.css({
                    top: pos.top - actions.outerHeight(),
                    left: pos.left + pos.width - actions.outerWidth(),
                });
                const containerActions = $('[data-actions-container-id=' + $(this).closest('[data-editor-container-id]').data('editor-container-id') + ']');
                alignActions(actions, containerActions);
            }
        }).on('mouseleave', '[data-editor-container-item-id], [data-actions-container-item-id]', function (e) {
            const containerItemId = $(this).data('editor-container-item-id') ?? $(this).data('actions-container-item-id');
            if (isOutside(e, ['[data-editor-container-item-id="' + containerItemId + '"]', '[data-actions-container-item-id="' + containerItemId + '"]'])) {
                $('[data-editor-container-item-id=' + containerItemId+ ']').removeClass('cp-editor-container-hover');
                $('[data-actions-container-item-id=' + containerItemId+ ']').hide();
                $('.cp-structure-active').removeClass('cp-structure-active');
            }
        }).on('mouseenter mouseleave', '[data-actions-container-item-id]', function (e) {
            const containerItemId = $(this).data('actions-container-item-id');
            const container = $('[data-editor-container-item-id=' + containerItemId + ']').closest('[data-editor-container-id]');
            if (container.length) {
                const containerId = container.data('editor-container-id');
                if (e.type === 'mouseenter') {
                    container.addClass('cp-editor-container-hover');
                    $('[data-actions-container-id=' + container.data('editor-container-id') + ']').show();
                } else if (isOutside(e, ['[data-editor-container-id="' + containerId+ '"]', '[data-actions-container-id="' + containerId+ '"]'])) {
                    container.removeClass('cp-editor-container-hover');
                    $('[data-actions-container-id=' + container.data('editor-container-id') + ']').hide();
                }
            }
        });

        const alignActions = function (itemActions, contActions) {
            if (itemActions.length && contActions.length) {
                const posItem = itemActions[0].getBoundingClientRect();
                const posCont = contActions[0].getBoundingClientRect();
                if (posItem.right >= posCont.left &&
                    posItem.right <= posCont.right &&
                    posItem.top >= posCont.top &&
                    posItem.top <= posCont.bottom) {
                    itemActions.css('left', posCont.left - posItem.width);
                }
            }
        }

        const isOutside = function(e, selectors) {
            const target = e.relatedTarget;
            return !target || selectors.every(selector => {
                const el = document.querySelector(selector);
                return el && !el.contains(target);
            });
        }
    }

    TemplateInlineEditor.prototype.editItemSubmit = function (evt) {
        const that = this;
        that._updateInputValue();
        that._removeDisabledFields(evt.$form);

        client.submit(evt, {dataType: 'json'}).then(function (response) {
            that._destroyInput();
            if (response.success) {
                const $result = $(response.output);
                if ($result.is('[data-template-item-id]')) {
                    // called for normal edit actions
                    const itemId = $result.data('template-item-id');
                    that.replaceElement(that.getItemById(itemId), $result);
                } else {
                    // called for addItem actions where currentElement is the container
                    that.replaceElement(that.current, $result);
                    that.structure().appendContainerItem($result.data('editor-container-id'), $(response.structure));
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
        $form.find(':disabled').each(function () {
            $form.find('[name="' + $(this).attr('name') + '"]').remove();
        });
    };

    TemplateInlineEditor.prototype.editMultipleElementsSubmit = function (evt) {
        const that = this;
        that._updateInputValue();
        that._removeDisabledFields(evt.$form);

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
        return this.getElement($('[data-template-item-id="' + id + '"]'));
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
        element.$.replaceWith($(content));
    };

    TemplateInlineEditor.prototype.getElement = function ($elem) {
        return Widget.instance($elem);
    };

    TemplateInlineEditor.prototype.structure = function () {
        if (typeof(this._structure) === 'undefined') {
            this._structure = Widget.instance('[data-ui-widget="custom_pages.template.TemplateStructure"]');
        }
        return this._structure;
    };

    const init = function () {
        if ($('#templatePageRoot').length && require('ui.view').getState().action !== 'edit-source') {
            module.editor = Widget.instance('#templatePageRoot');
        }
    };

    module.export({
        initOnPjaxLoad: true,
        init,
        TemplateInlineEditor,
    });
});
