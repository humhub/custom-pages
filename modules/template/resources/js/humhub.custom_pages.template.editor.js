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
        const that = this;

        this.$.on('mouseenter', '[data-editor-container-id]', function () {
            if ($(this).hasClass('cp-editor-container-selected')) {
                return;
            }

            $(this).addClass('cp-editor-container-selected');

            const addButton = $('.cp-structure [data-container-id=' + $(this).data('editor-container-id') + '] > div.cp-structure-container > [data-action-click="addContainerItem"]');
            if (addButton.length) {
                const actions = $('<div>').addClass('cp-editor-container-actions');
                $('body').append(actions.append(addButton.clone()
                    .removeAttr('data-action-click')
                    .on('click', () => addButton.click())));
                actions.css({
                    top: $(this).offset().top - actions.outerHeight(),
                    left: $(this).offset().left + $(this).outerWidth() - actions.outerWidth(),
                });
            }
        })
        .on('mouseleave', '[data-editor-container-id]', (e) => leaveContainer(e));
        $(document).on('mouseleave', '.cp-editor-container-actions', (e) => leaveContainer(e));
        const leaveContainer = function (e) {
            if (isOutside(e, ['[data-editor-container-id]', '.cp-editor-container-actions'])) {
                $('.cp-editor-container-selected').removeClass('cp-editor-container-selected');
                $('.cp-editor-container-actions').remove();
            }
        }

        this.$.on('mouseenter', '[data-editor-container-item-id]', function () {
            const containerItem = $('.cp-structure [data-container-item-id=' + $(this).data('editor-container-item-id') + '] > li > .cp-structure-row');
            containerItem.addClass('cp-structure-active');

            const editButton = containerItem.find('[data-action-click="editElements"] > .fa');
            if (editButton.length) {
                const actions = $('<div>').addClass('cp-editor-container-item-actions');
                $('body').append(actions.append(editButton.clone()
                    .removeAttr('data-action-click')
                    .on('click', () => editButton.parent().click())));
                actions.css({
                    top: $(this).offset().top - actions.outerHeight(),
                    left: $(this).offset().left + $(this).outerWidth() - actions.outerWidth(),
                });
                const containerActions = $('.cp-editor-container-actions');
                if (containerActions.length &&
                    actions.position().top > containerActions.position().top - actions.outerHeight() &&
                    actions.position().top < containerActions.position().top + actions.outerHeight()) {
                    actions.css('left', containerActions.position().left - actions.outerWidth());
                }
            }
        }).on('mouseleave', '[data-editor-container-item-id]', (e) => leaveContainerItem(e));
        $(document).on('mouseleave', '.cp-editor-container-item-actions', (e) => leaveContainerItem(e));
        const leaveContainerItem = function (e) {
            if (isOutside(e, ['[data-editor-container-item-id]', '.cp-editor-container-item-actions'])) {
                $('.cp-structure-active').removeClass('cp-structure-active');
                $('.cp-editor-container-item-actions').remove();
            }
        }

        const isOutside = function(e, selectors) {
            const target = e.relatedTarget;
            if (!target) {
                return true;
            }

            return selectors.every(selector => {
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
