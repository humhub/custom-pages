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
        $(document).on('mouseenter', '[data-editor-page-id]', function () {
            if ($(this).find('[data-editor-page-id].cp-editor-page-hover').length) {
                return;
            }
            const pageId = $(this).data('editor-page-id');
            const actionsSelector = '[data-actions-page-id=' + pageId + ']';
            const pageRow = $('.cp-structure > ul > li > .cp-structure-row');
            $(this).addClass('cp-editor-page-hover');
            $('[data-actions-page-id]').hide();

            if (!$(actionsSelector).length) {
                const editButton = pageRow.find('[data-action-click="editElements"] > .fa');
                if (editButton.length) {
                    $('body').append($('<div>')
                        .attr('data-actions-page-id', pageId)
                        .append(editButton.clone()
                            .removeAttr('data-action-click')
                            .on('click', () => pageRow.click())));
                }
            }

            alignActions(this, actionsSelector);
        }).on('mouseleave', '[data-editor-page-id]', function (e) {
            if (isOutside(e, ['[data-editor-page-id]', '[data-actions-page-id]'])) {
                $('[data-editor-page-id]').removeClass('cp-editor-page-hover');
                $('[data-actions-page-id]').hide();
            }
        }).on('mouseenter', '[data-editor-container-id]', function () {
            const containerId = $(this).data('editor-container-id');
            const actionsSelector = '[data-actions-container-id=' + containerId + ']';
            $(this).addClass('cp-editor-container-hover');

            if (!$(actionsSelector).length) {
                const addButtonSelector = '.cp-structure [data-container-id=' + containerId + '] > div.cp-structure-container > [data-action-click="addContainerItem"]';
                const addButton = $(addButtonSelector);
                if (addButton.length) {
                    $('body').append($('<div>')
                        .attr('data-actions-container-id', containerId)
                        .append(addButton.clone()
                        .removeAttr('data-action-click')
                        .on('click', () => $(addButtonSelector).click())));
                }
            }

            alignActions(this, actionsSelector);
        }).on('mouseleave', '[data-editor-container-id], [data-actions-container-id]', function (e) {
            const containerId = $(this).data('editor-container-id') ?? $(this).data('actions-container-id');
            if (isOutside(e, ['[data-editor-container-id="' + containerId+ '"]', '[data-actions-container-id="' + containerId+ '"]'])) {
                $('[data-editor-container-id=' + containerId+ ']').removeClass('cp-editor-container-hover');
                $('[data-actions-container-id=' + containerId+ ']').hide();
            }
        }).on('mouseenter', '[data-editor-container-item-id]', function () {
            if ($(this).find('[data-editor-container-item-id].cp-editor-container-hover').length) {
                return;
            }
            const containerItemId = $(this).data('editor-container-item-id');
            const actionsSelector = '[data-actions-container-item-id=' + containerItemId + ']';
            const containerItemSelector = '.cp-structure [data-container-item-id=' + containerItemId + '] > li > .cp-structure-row';
            const containerItem = $(containerItemSelector);
            containerItem.addClass('cp-structure-active');
            $(this).addClass('cp-editor-container-hover');
            $('[data-actions-container-item-id]').hide();

            if (!$(actionsSelector).length) {
                const editButton = containerItem.find('[data-action-click="editElements"] > .fa');
                if (editButton.length) {
                    $('body').append($('<div>')
                        .attr('data-actions-container-item-id', containerItemId)
                        .append(editButton.clone()
                        .removeAttr('data-action-click')
                        .on('click', () => $(containerItemSelector).click())));
                }
            }

            alignActions(this, actionsSelector);
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

        const alignActions = function (block, actionsSelector) {
            const actions = $(actionsSelector);
            actions.show();
            const posBlock = block.getBoundingClientRect();
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            const scrollLeft = window.pageXOffset || document.documentElement.scrollLeft;
            actions.css({
                top: posBlock.top + scrollTop - actions.outerHeight(),
                left: posBlock.left + scrollLeft + posBlock.width - actions.outerWidth(),
            });

            const allActions = $('[data-actions-container-item-id]:visible, [data-actions-container-id]:visible, [data-actions-page-id]:visible');
            if (allActions.length < 2) {
                return;
            }

            const posActions = actions[0].getBoundingClientRect();
            allActions.each(function () {
                if ($(this).is(actions)) {
                    return;
                }
                const posThis = this.getBoundingClientRect();
                if (posActions.right >= posThis.left &&
                    posActions.right <= posThis.right &&
                    posActions.top >= posThis.top &&
                    posActions.top <= posThis.bottom) {
                    if (actions.is('[data-actions-container-id]')) {
                        $(this).css('left', posActions.left + scrollLeft - posThis.width);
                    } else {
                        actions.css('left', posThis.left + scrollLeft - posActions.width);
                    }
                }
            });
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
