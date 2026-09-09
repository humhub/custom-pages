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
            const container = $('[data-editor-container-id="' + containerId + '"]');
            const items = container.find('[data-editor-container-item-id]');
            const itemActions = items.map(function () {
                return '[data-actions-container-item-id="' + $(this).data('editor-container-item-id') + '"]';
            }).get();
            // The actions of the items inside belong to the same group as the container and its add button
            if (isOutside(e, ['[data-editor-container-id="' + containerId + '"]', '[data-actions-container-id="' + containerId + '"]', ...itemActions])) {
                container.removeClass('cp-editor-container-hover');
                $('[data-actions-container-id=' + containerId + ']').hide();
                items.removeClass('cp-editor-container-hover');
                if (itemActions.length) {
                    $(itemActions.join(',')).hide();
                }
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
            const containerId = $('[data-editor-container-item-id="' + containerItemId + '"]').closest('[data-editor-container-id]').data('editor-container-id');
            // The add button of the parent container belongs to the same group, moving onto it keeps the item actions
            if (isOutside(e, ['[data-editor-container-item-id="' + containerItemId + '"]', '[data-actions-container-item-id="' + containerItemId + '"]', '[data-actions-container-id="' + containerId + '"]'])) {
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
            if (!actions.length) {
                return;
            }
            actions.show();
            const posBlock = block.getBoundingClientRect();
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            const scrollLeft = window.pageXOffset || document.documentElement.scrollLeft;
            // Keep the actions of long blocks reachable: pin them below the fixed top bars while the top edge
            // of the block is scrolled out of view. Only the innermost hovered item and the add button of its
            // container are pinned, the actions of the outer blocks (parent containers, page) keep their
            // natural position to avoid a row of pinned buttons.
            const fixedTopOffset = getFixedTopOffset();
            const naturalTop = posBlock.top - actions.outerHeight();
            const nestedHoverSelector = actions.is('[data-actions-container-id]')
                ? '[data-editor-container-id].cp-editor-container-hover'
                : '.cp-editor-container-hover';
            const isInnermost = !block.querySelector(nestedHoverSelector);
            const top = isInnermost
                ? Math.min(Math.max(naturalTop, fixedTopOffset), posBlock.bottom - actions.outerHeight())
                : naturalTop;
            actions.toggleClass('cp-editor-actions-pinned', top > naturalTop + 1);
            // Hide the actions instead of drawing them over the fixed top bars
            actions.toggleClass('cp-editor-actions-offscreen', top < fixedTopOffset);
            actions.css({
                top: top + scrollTop,
                left: posBlock.left + scrollLeft + posBlock.width - actions.outerWidth(),
            });

            const allActions = $('[data-actions-container-item-id]:visible, [data-actions-container-id]:visible, [data-actions-page-id]:visible');
            if (allActions.length < 2) {
                return;
            }

            let posActions = actions[0].getBoundingClientRect();
            allActions.each(function () {
                if ($(this).is(actions)) {
                    return;
                }
                const posThis = this.getBoundingClientRect();
                // Overlap test with a tolerance of one pixel, because exact edge comparisons fail
                // on fractional positions, e.g. with browser zoom or different font rendering
                if (posActions.left < posThis.right - 1 &&
                    posActions.right > posThis.left + 1 &&
                    posActions.top < posThis.bottom - 1 &&
                    posActions.bottom > posThis.top + 1) {
                    // The add button of a container keeps the corner, other actions move to the left of it
                    if (actions.is('[data-actions-container-id]')) {
                        $(this).css('left', posActions.left + scrollLeft - posThis.width);
                    } else {
                        actions.css('left', posThis.left + scrollLeft - posActions.width);
                        posActions = actions[0].getBoundingClientRect();
                    }
                }
            });
        }

        const isOutside = function(e, selectors) {
            const target = e.relatedTarget;
            return !target || selectors.every(selector => {
                const el = document.querySelector(selector);
                return !el || !el.contains(target);
            });
        }

        const getFixedTopOffset = function () {
            let offset = 0;
            $('.fixed-top:visible').each(function () {
                offset = Math.max(offset, this.getBoundingClientRect().bottom);
            });
            return offset;
        }

        const getActionsBlock = function (actions) {
            const $actions = $(actions);
            if ($actions.is('[data-actions-container-item-id]')) {
                return $('[data-editor-container-item-id="' + $actions.data('actions-container-item-id') + '"]')[0];
            }
            if ($actions.is('[data-actions-container-id]')) {
                return $('[data-editor-container-id="' + $actions.data('actions-container-id') + '"]')[0];
            }
            return $('[data-editor-page-id="' + $actions.data('actions-page-id') + '"]')[0];
        }

        const realignActions = function () {
            $('[data-actions-container-item-id]:visible, [data-actions-container-id]:visible, [data-actions-page-id]:visible').each(function () {
                const block = getActionsBlock(this);
                if (block) {
                    alignActions(block, this);
                }
            });
        }

        // Re-align the visible actions on scrolling, so they follow their block through the viewport
        let realignRequested = false;
        $(window).off('scroll.cpEditorActions resize.cpEditorActions').on('scroll.cpEditorActions resize.cpEditorActions', function () {
            if (realignRequested) {
                return;
            }
            realignRequested = true;
            window.requestAnimationFrame(function () {
                realignRequested = false;
                realignActions();
            });
        });

        // The pinning depends on which hovered block is the innermost one, so re-align on every hover change
        $(document).on('mouseenter mouseleave', '[data-editor-page-id], [data-editor-container-id], [data-editor-container-item-id], [data-actions-page-id], [data-actions-container-id], [data-actions-container-item-id]', function () {
            realignActions();
        });
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
